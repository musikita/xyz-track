<?php

namespace Suitcorecms\Seo;

class ModelObserver
{
    public function saving(Model $model)
    {
        if (json_decode($model->getOriginal('meta'), true) == $model->meta) {
            return false;
        }
        $locale = $model->model->seoTranslationLocale();
        $model->locale = $model->locale ?? $locale;
    }
}
