<?php

namespace Suitcorecms\Cms;

trait RedirectorTrait
{
    protected function redirect($resource)
    {
        return redirect($this->redirectTo ?? $this->indexUrl());
    }
}
