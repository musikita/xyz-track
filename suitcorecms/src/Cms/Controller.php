<?php

namespace Suitcorecms\Cms;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Suitcorecms\Breadcrumbs\Breadcrumb;
use Suitcorecms\Medialibrary\MediaObserver;
use Suitcorecms\Resources\Buttons\Button;
use Suitcorecms\Resources\Resource;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use ButtonTrait;
    use ControllerTrait;
    use DispatchesJobs;
    use RedirectorTrait;
    use ValidatesRequests;
    use ViewTrait;

    protected static $registeredObserver = false;
    protected $prepedResources = [];
    protected $redirectTo;

    public function __construct()
    {
        MediaObserver::setScope(config('suitcorecms.medialibrary.cms_scope', 'cms'));
        if (!static::$registeredObserver) {
            $this->registerObserver();
            static::$registeredObserver = true;
        }
        $this->redirectTo = request('redirectTo');
    }

    abstract protected function baseResourceable();

    protected function rules($method)
    {
        return [];
    }

    public function prepResource($method, $id = null)
    {
        $childName = Str::camel('view of '.$method);
        $resource =
            Resource::make(
                $resourceable = $this->{$method.'Resourceable'}($id),
                $this->fields($resourceable, $method),
                $this->rules($method) ?? [],
                $this->defaultTimestamp()
            )
            ->setName($this->name)
            ->setMethod($method)
            ->setBaseRoute($this->baseRoute)
            ->setDefaultQueryParameters($this->defaultQueryParameters)
            ->setButtons($this->buttons());

        if ($method != 'delete') {
            $resource->setChildView($this->{$childName});
        }

        if ($method == 'show') {
            $this->addDeleteButton($resource);
        }

        return $this->prepedResources[$method] = $resource;
    }

    public function getPrepedResource($method)
    {
        return $this->prepedResources[$method] ?? null;
    }

    protected function showBreadcrumb($resource, array $items = [], array $lead = [])
    {
        $items = $lead + ($resource->routeExist('index') ? [
            $resource->routeIndex() => $resource->getName(),
        ] : []) + $items;
        $breadcrumb = new Breadcrumb(url()->current(), $items);
        view()->share(compact('breadcrumb'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resource = $this->prepResource('index');
        if (request()->ajax()) {
            return $resource->jsonDatatables();
        }
        $this->showBreadcrumb($resource);

        return $this->view(compact('resource'));
    }

    /**
     * Show the form for creating the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resource = $this->prepResource('create');
        $this->showBreadcrumb($resource, [null => 'Create New']);

        return $this->view(compact('resource'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param any $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $resource = $this->prepResource('update', $id);
        $this->showBreadcrumb($resource, [$resource->routeExist('show') ? $resource->routeShow($id) : $resource->routeEdit($id) => $resource->getCaption(), null => 'Edit']);

        return $this->view(compact('resource'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $resource = $this->prepResource('create')->create($request);
        if (request()->ajax()) {
            return ['url' => $resource->routeEdit()];
        }

        return $this->redirect($resource);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $resource = $this->prepResource('show', $id);
        $this->showBreadcrumb($resource, [null => $resource->getCaption()]);

        return $this->view(compact('resource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User         $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $resource = $this->prepResource('update', $id)->update($request);
        if (request()->ajax()) {
            return ['url' => $resource->routeEdit()];
        }

        return $this->redirect($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->prepResource('delete', $id)->delete();
        $resource = $this->prepResource('index');

        return $this->redirect($resource);
    }

    protected function addDeleteButton($resource)
    {
        if ($resource->routeExist('destroy')) {
            $csrf_field = csrf_field();
            $resource->setButtons([(new Button('Delete'))
                ->url('#')
                ->icon('la la-trash')
                ->beforeHtml(<<<HTML
                    <form action="{$resource->routeDestroy()}" method="POST">
                        <input type="hidden" name="_method" value="DELETE">
                        {$csrf_field}
                    </form>
HTML
)
                ->onclick('
                    return (confirm(&quot;Are you sure?&quot;) ? $(this).siblings(&quot;form&quot;).submit() : false);
                '),
            ]);
        }
    }
}
