<?php

namespace Suitcorecms\Resources;

use Suitcorecms\Forms\Form;

trait ResourceFormTrait
{
    public function createFormFieldName($name)
    {
        return !$this->prefix ? $name : "{$this->prefix}[{$name}]";
    }

    public function form($method = null)
    {
        if ($method = $method ?? $this->method) {
            if ($form = $this->forms[$method] ?? false) {
                return $form;
            }
            $fields = $this->fields($method);

            return $this->forms[$method] = app(Form::class)
                ->setResource($this)
                ->nameCreator([$this, 'createFormFieldName'])
                ->of($this->resourceable)
                ->setMethod($method)
                ->fields($fields);
        }
    }
}
