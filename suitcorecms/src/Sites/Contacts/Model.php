<?php

namespace Suitcorecms\Sites\Contacts;

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
        $this->table = config('suitcoresite.contacts.table', 'contacts');
        $this->baseName = config('suitcoresite.contacts.basename', 'Contacts');
        $this->fillable = array_keys(config('suitcoresite.contacts.fields', []));
        parent::__construct($attributes);
    }

    public function rules($method)
    {
        return config('suitcoresite.contacts.rules', []);
    }
}
