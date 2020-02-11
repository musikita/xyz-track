<?php

namespace Suitcorecms\Medialibrary\Generators;

use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\ImageGenerators\BaseGenerator;

class Svg extends BaseGenerator
{
    public function convert(string $file, Conversion $conversion = null): string
    {
        return $file;
    }

    public function requirementsAreInstalled(): bool
    {
        return class_exists('Imagick');
    }

    public function supportedExtensions(): Collection
    {
        return collect('svg');
    }

    public function supportedMimeTypes(): Collection
    {
        return collect('image/svg+xml');
    }
}
