<?php

namespace Suitcorecms\Sluggable;

use Illuminate\Support\Str;

class ModelObserverForAutoSlugFormat
{
    public function saving($model)
    {
        if (method_exists($model, 'autoSlugFormat')) {
            return $model->autoSlugFormat();
        }
        if ($model->notAutoSlugFormat ?? false) {
            return true;
        }
        if ($model->slug) {
            $model->slug = Str::slug($model->slug);
        }
    }

    public function deleted($model)
    {
        if ($model->exists) {
            $model->slug = Str::limit($model->slug.'-'.uniqid(), 200, '');
            $model->save();
        }
    }
}
