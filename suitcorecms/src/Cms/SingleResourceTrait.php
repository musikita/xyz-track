<?php

namespace Suitcorecms\Cms;

use Illuminate\Support\Facades\Route;

trait SingleResourceTrait
{
    abstract protected function getResourceId();

    public function index()
    {
        return $this->show(null);
    }

    public function show($id)
    {
        if ($id != '_') {
            return redirect($this->indexUrl());
        }

        return parent::show($this->getResourceId());
    }

    public function showOne()
    {
        return $this->show(null);
    }

    public function edit($id)
    {
        if ($id != '_') {
            return redirect()->route($this->baseRoute().'.edit', '_');
        }

        return parent::edit($this->getResourceId());
    }

    protected function indexUrl()
    {
        return route($this->baseRoute().'.show', '_');
    }

    public static function cmsRoutes($uri)
    {
        Route::get($uri, [static::class, 'showOne'])->name($uri.'.one');
    }
}
