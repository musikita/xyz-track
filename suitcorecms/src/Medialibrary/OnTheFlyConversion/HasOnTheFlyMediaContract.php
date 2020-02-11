<?php

namespace Suitcorecms\Medialibrary\OnTheFlyConversion;

use Spatie\MediaLibrary\HasMedia\HasMedia;

interface HasOnTheFlyMediaContract extends HasMedia
{
    public function addOnTheFlyConversion($name, callable $callback);
}
