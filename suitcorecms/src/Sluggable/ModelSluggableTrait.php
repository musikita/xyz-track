<?php

namespace Suitcorecms\Sluggable;

use Cviebrock\EloquentSluggable\Sluggable;

trait ModelSluggableTrait
{
    use Sluggable {
        bootSluggable as baseBootSluggable;
    }

    public static function bootSluggable()
    {
        if (property_exists(static::class, 'slugBy')) {
            static::baseBootSluggable();
            static::observe(app(ModelObserverForAutoSlugFormat::class));
        }
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => $this->slugBy,
            ],
        ];
    }
}
