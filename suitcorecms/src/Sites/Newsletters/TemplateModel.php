<?php

namespace Suitcorecms\Sites\Newsletters;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Validation\Rule;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;

class TemplateModel extends BaseModel implements Resourceable
{
    use ResourceableTrait;

    protected $guarded = [];
    protected $baseName = 'Newsletter Templates';
    protected $captionField = 'name';

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcoresite.newsletters.template_table', 'newsletter_templates');
        parent::__construct($attributes);
    }

    public function rules($method)
    {
        return [
            'name' => [
                'required',
                'max:50',
                $method == 'create' ? Rule::unique($this->getTable()) : Rule::unique($this->getTable())->ignore($this->id),
            ],
            'html' => 'required',
        ];
    }
}
