<?php

namespace Suitcorecms\Sites\Seo;

use Suitcorecms\Cms\Controller as BaseController;
use Suitcorecms\Cms\Route;

class Controller extends BaseController
{
    protected function baseResourceable()
    {
        return new Model();
    }

    protected function fields()
    {
        return [
            'URL' => [
                'name' => 'url',
                'type' => 'text',
            ],
        ];
    }

    public static function cmsRoutes($uri = 'seo-tools')
    {
        Route::resource($uri, static::class);
    }
}
