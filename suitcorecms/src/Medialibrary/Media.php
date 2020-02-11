<?php

namespace Suitcorecms\Medialibrary;

use Spatie\MediaLibrary\Models\Media as BaseMedia;
use Suitcorecms\Medialibrary\OnTheFlyConversion\OnTheFlyMediaTrait;

class Media extends BaseMedia
{
    use OnTheFlyMediaTrait;

    protected $sortable = [];

    public function sortWhenCreating($bool)
    {
        $this->sortable['sort_when_creating'] = $bool;

        return $this;
    }
}
