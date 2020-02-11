<?php

namespace Suitcorecms\Medialibrary\OnTheFlyConversion;

use Spatie\MediaLibrary\Conversion\ConversionCollection;
use Spatie\MediaLibrary\FileManipulator;

trait OnTheFlyMediaTrait
{
    public function getUrl(string $conversionName = ''): string
    {
        try {
            ConversionCollection::createForMedia($this)->getByName($conversionName);
        } catch (\Exception $e) {
            if (func_num_args() >= 2 && $callback = func_get_arg(1)) {
                if (is_callable($callback) && $this->model instanceof HasOnTheFlyMediaContract) {
                    $this->model->addOnTheFlyConversion($conversionName, $callback);
                    app(FileManipulator::class)->createDerivedFiles($this, [$conversionName], func_num_args() == 2 || func_get_arg(2) == false);
                }
            }
        }

        return parent::getUrl($conversionName);
    }
}
