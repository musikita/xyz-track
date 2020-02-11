<?php

namespace Suitcorecms\Sites\Contacts;

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
        return new Model();
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

        foreach (config('suitcoresite.contacts.fields') as $key => $value) {
            $fields[$value] = [
                'name' => $key,
                'type' => 'mute',
            ];
        }

        return $fields;
    }

    public function submit(Request $request)
    {
        if ($observer = config('suitcoresite.contacts.observer', Observer::class)) {
            Model::observe(new $observer());
        }
        $resource = $this->prepResource('create');
        $this->message()->error()->title(__('suitcoresite.contacts.failed_submit'));
        if ($resource->create($request)) {
            $this->message()
                ->flash(__('suitcoresite.contacts.success_message'))
                ->title(__('suitcoresite.contacts.success_title'))
                ->success()
                ->canCancel('error');

            return back();
        }

        return back();
    }

    public static function frontendRoutes($uri = null, $basename = null)
    {
        $uri = $uri ?? 'contact';
        $name = $basename ?? 'suitcorecms.frontend.contacts';
        Route::post($uri, [static::class, 'submit'])->name($name.'.submit');
    }

    public static function cmsRoutes($uri = 'contacts')
    {
        CmsRoute::resource($uri, static::class)->only('index', 'show');
    }
}
