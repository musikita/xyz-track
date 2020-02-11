<?php

namespace Suitcorecms\Resources;

use Illuminate\Support\Str;

trait ResourceButtonTrait
{
    protected $buttons = [];

    public function setButtons(array $buttons = null)
    {
        $this->buttons = $buttons ?? [];

        return $this;
    }

    public function addButton($button)
    {
        $this->buttons[] = $button;

        return $this;
    }

    public function buttons()
    {
        return collect($this->buttons ?? [])
                ->map(function ($button) {
                    $button->setResourceable($this->getResourceable());

                    return $button;
                })
                ->filter(function ($button) {
                    return $button->{Str::camel('show in '.$this->method)} === true;
                })
                ->toArray();
    }
}
