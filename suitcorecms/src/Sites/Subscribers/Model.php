<?php

namespace Suitcorecms\Sites\Subscribers;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;

class Model extends BaseModel implements Resourceable
{
    use ResourceableTrait;

    protected $baseName;
    protected $captionField = 'created_at';

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcoresite.subscribers.table', 'subscribers');
        $this->baseName = config('suitcoresite.subscribers.basename', 'Subscribers');
        $this->fillable = array_keys(config('suitcoresite.subscribers.fields', []));
        parent::__construct($attributes);
    }

    public function rules($method)
    {
        return config('suitcoresite.subscribers.rules', []);
    }
}
