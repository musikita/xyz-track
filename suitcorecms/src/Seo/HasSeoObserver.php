<?php

namespace Suitcorecms\Seo;

use Illuminate\Support\Arr;

class HasSeoObserver
{
    public function saved($model)
    {
        $meta = request($model->getSeoField());
        $this->fillImage($meta, $model);
        $seoData = $model->seoData ?? new Model();
        $seoData->fill(compact('meta'));
        $model->seoData()->save($seoData);
    }

    protected function fillImage(&$meta, $model)
    {
        foreach ($model->getMediaFields() as $field) {
            if (strpos($field, $model->getSeoField()) === 0) {
                $name = str_replace($model->getSeoField().'.', '', $field);
                $media = $model->getFirstMedia($field);
                $media = $media ? $media->getFullUrl('seo') : null;
                Arr::set($meta, $name, $media);
            }
        }

        return $meta;
    }
}
