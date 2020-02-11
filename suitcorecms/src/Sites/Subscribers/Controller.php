<?php

namespace Suitcorecms\Sites\Subscribers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Suitcorecms\Cms\Controller as BaseController;
use Suitcorecms\Cms\Route as CmsRoute;
use Suitcorecms\Notifications\NotificationTrait;

class Controller extends BaseController
{
    use NotificationTrait;

    protected function baseResourceable()
    {
        $model = config('suitcoresite.subscribers.model', Model::class);

        return new $model();
    }

    protected function registerObserver()
    {
        $model = config('suitcoresite.subscribers.model', Model::class);
        $observer = config('suitcoresite.subscribers.observer', Observer::class);
        $model::observe(new $observer());
        $model::observe($this);
    }

    protected function fields()
    {
        $fields = [
            'ID' => [
                'name'    => 'id',
                'type'    => 'text',
                'on_form' => false,
            ],
        ];

        foreach (config('suitcoresite.subscribers.fields') as $key => $value) {
            $fields[$value] = [
                'name' => $key,
                'type' => 'mute',
            ];
        }

        return $fields;
    }

    public function submit(Request $request)
    {
        if ($observer = config('suitcoresite.subscribers.observer', Observer::class)) {
            Model::observe(new $observer());
        }
        $resource = $this->prepResource('create');
        $this->message()->error()->title(__('suitcoresite.subscribers.failed_submit'));
        if ($resource->create($request)) {
            $this->message()
                ->flash(__('suitcoresite.subscribers.success_message'))
                ->title(__('suitcoresite.subscribers.success_title'))
                ->success()
                ->canCancel('error');

            return back();
        }

        return back();
    }

    public static function frontendRoutes($uri = null, $basename = null)
    {
        $uri = $uri ?? 'subscriber';
        $name = $basename ?? 'suitcorecms.frontend.subscribers';
        Route::post($uri, [static::class, 'submit'])->name($name.'.submit');
    }

    public static function cmsRoutes($uri = 'subscribers')
    {
        CmsRoute::resource($uri, static::class)->only('index', 'show');
    }
}
