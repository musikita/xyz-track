<?php

namespace Suitcorecms\Sites\Subscribers;

class Observer
{
    public function saving($model)
    {
        if ($exist = $model::where('email', $model->email)->first()) {
            return false;
        }
    }
}
