<?php

namespace Suitcorecms\ControllerTagging;

use Illuminate\Support\Str;
use Suitcorecms\Breadcrumbs\Breadcrumb;

trait ControllerTaggingTrait
{
    protected static $tag;

    /**
     * put this method inside prepResource.
     *
     * @return [type] [description]
     */
    protected function taggingSetup($hasTaggedModel)
    {
        $this->defaultQueryParameters = count($this->defaultQueryParameters) ? $this->defaultQueryParameters : request()->query();
        $id = $this->defaultQueryParameters[$this->tagging_id] ?? request($this->tagging_id);
        $hasTaggedModel::setTag($this->findTag($id));
    }

    public function taggingPrepResource($method, $id = null)
    {
        $class = get_class($this->baseResourceable());
        $this->taggingSetup($class);

        return parent::prepResource($method, $id);
    }

    public function prepResource($method, $id = null)
    {
        return $this->taggingPrepResource($method, $id);
    }

    protected function showBreadcrumb($resource, array $items = [], array $lead = [])
    {
        $tag = static::$tag;
        $taggingModel = $tag;
        $taggingModelField = $taggingModel ? (method_exists($taggingModel, 'getTaggingField') ? $taggingModel->getTaggingField() : null) : null;
        $taggingModelValue = $taggingModelField ? $taggingModel->{$taggingModelField} : null;

        $index = $lead + [route(str_replace('.show', '.index', $this->baseRedirectRoute), array_filter([$taggingModelField => $taggingModelValue])) => $tag->getName(), route($this->baseRedirectRoute, array_filter(['id' => $tag->id, $taggingModelField => $taggingModelValue])) => $tag->getCaption()];
        $items = array_merge($index, ($resource->routeExist('index')
                    ? [$resource->routeIndex() => $resource->getName()]
                    : []
                ) + $items);
        $breadcrumb = new Breadcrumb(url()->current(), $items);
        view()->share(compact('breadcrumb'));
    }

    protected function findTag($id)
    {
        if (static::$tag) {
            return static::$tag;
        }

        $tapMethod = method_exists($this, 'tapFindTag') ? [$this, 'tapFindTag'] : null;

        $tag = $this->baseTag()->findOrFail($id);

        return
            static::$tag = $tapMethod
                ? tap($tag, $tapMethod)
                : $tag;
    }

    public function redirectToUrl()
    {
        $hash = Str::snake($this->name, '_');

        $taggingModel = $this->baseResourceable();
        $taggingModelField = $taggingModel ? (method_exists($taggingModel, 'getTaggingField') ? $taggingModel->getTaggingField() : null) : null;
        $taggingModelValue = $taggingModelField ? $taggingModel->{$taggingModelField} : null;

        return route($this->baseRedirectRoute, array_filter(['id' => request($this->tagging_id, static::$tag->id ?? null), $taggingModelField => $taggingModelValue]))."#_{$hash}_";
    }

    public function taggingFields(array $fields = [])
    {
        return array_merge([
            'Tagging' => [
                'type'    => 'hidden',
                'name'    => $this->tagging_id,
                'value'   => request($this->tagging_id),
                'on_edit' => false,
            ],
            'Redirect To' => [
                'type'   => 'hidden',
                'name'   => 'redirectTo',
                'output' => function ($model, $name, $resource) {
                    return $this->redirectToUrl();
                },
            ],
        ],
            $fields
        );
    }

    public function importFields()
    {
        $relation = str_replace('_id', '', $this->tagging_id);
        $title = Str::title(str_replace('_', ' ', $relation));

        $fields = [
            $title => [
                'name'     => $this->tagging_id,
                'relation' => $relation,
            ],
            'Tagging' => [
                'on_form' => false,
            ],
        ];

        if (method_exists($this, 'overrideImportFields')) {
            $override = $this->overrideImportFields();
            $fields = array_replace($fields, $override);
        }

        return $fields;
    }

    public function index()
    {
        if (!request()->ajax()) {
            return redirect($this->redirectToUrl());
        }

        $index = parent::index();

        return $index;
    }
}
