<?php

namespace Suitcorecms\ControllerTagging;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HasTaggingModelScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if ($tag = $model::getTag() ?? false) {
            $builder = $builder->where($model->getTaggingField(), $tag->id);
        }

        return $builder;
    }
}
