<?php

namespace Suitcorecms\Sites\Newsletters;

use Illuminate\Http\Request;
use Suitcorecms\Cms\Controller as BaseController;
use Suitcorecms\Cms\Route as CmsRoute;

class NewsletterController extends BaseController
{
    protected function getNewsletterModel()
    {
        return config('suitcoresite.newsletters.model', NewsletterModel::class);
    }

    protected function getTemplateModel()
    {
        return config('suitcoresite.newsletters.template_model', TemplateModel::class);
    }

    protected function baseResourceable()
    {
        $model = $this->getNewsletterModel();

        return new $model();
    }

    public function getTemplateOptions()
    {
        $template = $this->getTemplateModel();

        return $template::get()->pluck('name', 'id')->toArray();
    }

    public function show($id)
    {
        if (request('preview') == 'true') {
            return $this->browserPreview($id);
        }

        return parent::show($id);
    }

    public function browserPreview($id)
    {
        $resource = $this->prepResource('show', $id);
        $newsletter = $resource->getResourceable();

        return $newsletter->toHtml();
    }

    public function showPreview($model)
    {
        $url = cms_route('show', [$model->id]).'?preview=true';

        return <<<HTML
            <iframe onload="resizeIframe(this)" width="100%" src="{$url}"></iframe>
HTML;
    }

    public function showJavascript()
    {
        return <<<'JS'
            <script>
                function resizeIframe(iframe) {
                    iframe.height = iframe.contentWindow.document.body.scrollHeight + "px";
                }
            </script>
JS;
    }

    protected function fields()
    {
        return [
            'Template' => [
                'name'      => 'template_id',
                'relation'  => 'template',
                'type'      => 'select2',
                'options'   => [$this, 'getTemplateOptions'],
            ],
            'Title' => [
                'type' => 'text',
            ],
            'Content' => [
                'type'     => 'richtext',
                'on_index' => false,
            ],
            'Sent At' => [
                'type'    => 'date',
                'on_form' => false,
            ],
            'Published At' => [
                'type' => 'date',
            ],
            'Is Draft' => [
                'type'      => 'boolean',
            ],
            'Preview' => [
                'on_index'  => false,
                'on_form'   => false,
                'on_show'   => [
                    'output'        => [$this, 'showPreview'],
                    'javascript'    => [$this, 'showJavascript'],
                ],
            ],
        ];
    }

    public static function cmsRoutes($uri = 'newsletters')
    {
        CmsRoute::resource($uri, static::class);
    }
}
