<?php

namespace Suitcorecms\Sites\Seo;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Suitcorecms\Medialibrary\HasMediaTrait;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;
use Suitcorecms\Seo\Contract\HasSeo;
use Suitcorecms\Seo\HasSeoTrait;

class Model extends BaseModel implements HasMedia, HasSeo, Resourceable
{
    use HasMediaTrait;
    use HasSeoTrait;
    use ResourceableTrait;
    protected $baseName;

    protected $captionField = 'url';

    protected $fillable = ['url'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcoresite.seo.table', 'seo_urls');
        $this->baseName = config('suitcoresite.seo.basename', 'Url Based SEO');
        parent::__construct($attributes);
    }

    public function getSeoFieldAttribute()
    {
        return $this->seoData->meta ?? null;
    }

    public function rules($method)
    {
        return [
            'url' => [
                'required',
                'url',
                $method == 'create' ? Rule::unique($this->table) : Rule::unique($this->table)->ignore($this->id),
            ],
        ];
    }
}
