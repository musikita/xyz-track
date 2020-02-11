<?php

namespace Suitcorecms\Sites\Banners;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;

class Model extends BaseModel implements Resourceable
{
    use ResourceableTrait;

    protected $baseName;

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcoresite.banners.table', 'banners');
        $this->baseName = config('suitcoresite.banners.basename', 'Banners');
        $this->fillable = array_keys(config('suitcoresite.banners.fields', []));
        parent::__construct($attributes);
    }

    public function rules($method)
    {
        return config('suitcoresite.banners.rules', []);
    }
}
