<?php

namespace Suitcorecms\Sites\Settings;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;

class Model extends BaseModel implements Resourceable
{
    use ResourceableTrait;

    protected $baseName;
    protected $fillable = ['key', 'value'];
    protected $captionField = 'key';

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcoresite.settings.table', 'settings');
        $this->baseName = config('suitcoresite.settings.basename', 'Settings');
        parent::__construct($attributes);
    }
}
