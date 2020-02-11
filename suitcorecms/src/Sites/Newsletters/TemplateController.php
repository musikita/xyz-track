<?php

namespace Suitcorecms\Sites\Newsletters;

use Suitcorecms\Cms\Controller as BaseController;
use Suitcorecms\Cms\Route as CmsRoute;

class TemplateController extends BaseController
{
    protected function getTemplateModel()
    {
        return config('suitcoresite.newsletters.template_model', TemplateModel::class);
    }

    protected function baseResourceable()
    {
        $model = $this->getTemplateModel();

        return new $model();
    }

    protected function fields()
    {
        return [
            'ID' => [
                'name'    => 'id',
                'type'    => 'text',
                'on_form' => false,
            ],
            'Name' => [
                'type' => 'text',
            ],
            'Html' => [
                'type'      => 'textarea',
                'on_index'  => false,
            ],
        ];
    }

    public static function cmsRoutes($uri = 'newsletter_templates')
    {
        CmsRoute::resource($uri, static::class);
    }
}
