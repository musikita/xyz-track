<?php

namespace Suitcorecms\Medialibrary;

use Spatie\MediaLibrary\HasMedia\HasMedia;

class HasMediaObserver
{
    public function saved(HasMedia $model)
    {
        $model->addToMedia($model->getSavingMedia());
        $model->deleteFromMedia();
    }
}
