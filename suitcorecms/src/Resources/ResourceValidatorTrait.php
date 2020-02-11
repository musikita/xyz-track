<?php

namespace Suitcorecms\Resources;

use Proengsoft\JsValidation\Facades\JsValidatorFacade;

trait ResourceValidatorTrait
{
    public function validator()
    {
        $method = $this->method;

        return JsValidatorFacade::make($this->validRules(), $this->ruleMessages($method), $this->ruleAttributes($method));
    }
}
