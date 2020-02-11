<?php

namespace Suitcorecms\Medialibrary\OnTheFlyConversion;

use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

trait HasOnTheFlyMediaTrait
{
    use HasMediaTrait;

    protected static $onTheFlyConversions = [];

    public function addOnTheFlyConversion($name, callable $callback)
    {
        static::$onTheFlyConversions[$name] = $callback;
    }

    public function callOnTheFlyConversions(Media $media = null)
    {
        foreach (static::$onTheFlyConversions as $name => $callback) {
            $conversion = $this->addMediaConversion($name);
            $callback($conversion, $media);
        }
    }
}
