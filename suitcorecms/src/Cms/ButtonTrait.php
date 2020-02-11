<?php

namespace Suitcorecms\Cms;

trait ButtonTrait
{
    protected function buttons()
    {
        return method_exists($this, 'registerButtons') ? $this->registerButtons() : [];
    }
}
