<?php

namespace Suitcorecms\Resources;

use Illuminate\Support\Str;
use ReflectionClass;

trait ResourceableTrait
{
    protected function getClassName()
    {
        $reflection = new ReflectionClass($this);

        return Str::title(Str::snake($reflection->getShortName(), ' '));
    }

    public function getName()
    {
        return $this->baseName ?? $this->getClassName();
    }

    public function getCaptionField()
    {
        return $this->captionField ?? 'name';
    }

    public function getCaption()
    {
        $captionField = $this->getCaptionField();

        return $this->{$captionField} ?? $this->getName();
    }

    public function datatablesQuery()
    {
        if (method_exists($this, 'translation')) {
            return $this->newQuery()->with('translation')->joinTranslation();
        }

        return $this->query();
    }
}
