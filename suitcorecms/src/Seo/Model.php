<?php

namespace Suitcorecms\Seo;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;

class Model extends BaseModel implements Resourceable
{
    use ResourceableTrait;

    protected $baseName;

    protected $fillable = ['locale', 'meta'];

    protected $mediaFields = ['image'];

    protected $casts = [
        'meta' => 'array',
    ];

    public static $dontTranslate = false;

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcorecms.seo.seo_table', 'seo');
        $this->baseName = config('suitcorecms.seo.basename', 'Seo Tools');

        $mediaFields = [];
        foreach ($this->mediaFields ?? [] as $field) {
            $mediaFields[] = config('suitcorecms.seo.form.field_name').'.'.$field;
        }

        $this->mediaFields = $mediaFields;

        parent::__construct($attributes);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function createSeoMediaConversion($model)
    {
        $model->addMediaConversion('seo')
            ->width(config('suitcorecms.seo.image_width', 300))
            ->height(config('suitcorecms.seo.image_height', 300));
    }

    public function getMediaFields()
    {
        return $this->mediaFields ?? [];
    }

    public function getMultipleMedias()
    {
        return $this->multipleMedias ?? [];
    }
}
