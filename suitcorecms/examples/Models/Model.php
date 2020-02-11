<?php

namespace App\Models;

use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;
use Suitcorecms\Calendars\CalendarResourceable;
use Suitcorecms\Calendars\CalendarResourceableTrait;
use Suitcorecms\Medialibrary\HasMediaTrait;
use Suitcorecms\Medialibrary\OnTheFlyConversion\HasOnTheFlyMediaContract;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;
use Suitcorecms\Seo\Contract\HasSeo;
use Suitcorecms\Seo\HasSeoTrait;
use Suitcorecms\Sluggable\ModelSluggableTrait;

abstract class Model extends BaseModel implements CalendarResourceable, Resourceable, HasOnTheFlyMediaContract
{
    use CalendarResourceableTrait;
    use FormAccessible;
    use HasMediaTrait;
    use ModelSluggableTrait;
    use LogsActivity;
    use ResourceableTrait;
    use HasSeoTrait;

    protected $baseName;

    protected static $logUnguarded = true;

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = true;

    protected static $translateSeo = true;

    public function newQuery()
    {
        $builder = parent::newQuery();

        return $this->withMedia($builder);
    }

    public function fillAttributes(array $attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    protected function getDeletingMedia($field)
    {
        $deleteImageInput = config('suitcorecms.fields.image.delete_name');
        $signal = (array) request($deleteImageInput);
        if ($deleted = in_array($field, $signal) ? $field : ($signal[$field] ?? false)) {
            if (is_array($deleted)) {
                return
                    $this
                        ->getMedia($field)
                        ->whereIn(
                            config('suitcorecms.fields.image.delete_identifier'),
                            $deleted
                        );
            }

            return true;
        }

        return false;
    }

    public function getSeoFieldAttribute()
    {
        return $this instanceof HasSeo ? ($this->seoData->meta ?? null) : null;
    }
}
