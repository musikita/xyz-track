<?php

namespace Suitcorecms\Sites\Settings;

class Observer
{
    public function saved(Model $model)
    {
        (new Setting())->clearCache();
    }

    public function deleted(Model $model)
    {
        (new Setting())->clearCache();
    }
}
