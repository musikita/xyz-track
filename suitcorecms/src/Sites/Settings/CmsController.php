<?php

namespace Suitcorecms\Sites\Settings;

use Suitcorecms\Cms\Controller;

class CmsController extends Controller
{
    protected function registerObserver()
    {
        Model::observe(new Observer());
    }

    protected function baseResourceable()
    {
        return new Model();
    }

    protected function fields()
    {
        return [
            'ID' => [
                'name'    => 'id',
                'type'    => 'text',
                'on_form' => false,
            ],
            'Key' => [
                'type' => 'mute',
            ],
            'Value' => [
                'type' => 'text',
            ],
        ];
    }
}
