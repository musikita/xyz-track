<?php

namespace Suitcorecms\Breadcrumbs;

use CLosure;

class Breadcrumb
{
    protected $active;
    protected $items;

    public function __construct($active, array $items = null)
    {
        $this->active = $active;
        $this->items = $items ?? [];
    }

    public function getItems()
    {
        return $this->items;
    }

    public function process(Closure $format, $separator = null)
    {
        $items = [];
        foreach ($this->items as $link => $title) {
            $items[] = $format($link, $title, $this->active == $link || $link == null);
        }
        $separator = $separator ?? config('suitcorecms.breadcrumbs.separator');
        $result = implode($separator, $items);

        return $result;
    }

    public function output($format = null)
    {
        $format = $format ?? config('suitcorecms.breadcrumbs.item');

        return $this->process($format);
    }
}
