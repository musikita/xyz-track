<?php

namespace Suitcorecms\Sites\Newsletters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;
use Suitcorecms\Sites\Newsletters\Contracts\Newsletterable;

class NewsletterModel extends BaseModel implements Newsletterable, Resourceable
{
    use ResourceableTrait;

    protected $additionalDatas = [];
    protected $guarded = [];
    protected $baseName = 'Newsletters';
    protected $captionField = 'title';
    protected $dates = ['published_at', 'send_at'];

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcoresite.newsletters.table', 'newsletters');
        parent::__construct($attributes);
    }

    public function rules($method)
    {
        return [
            'title'        => 'required|max:255',
            'content'      => 'required',
            'published_at' => 'required',
        ];
    }

    public function scopeNotSent($query)
    {
        return $query->whereNull('sent_at');
    }

    public function scopeNotDraft($query)
    {
        return $query->where('is_draft', false);
    }

    public function scopeCanSend($query)
    {
        return $query->where('is_draft', false)->where('published_at', '<=', Carbon::now());
    }

    public function template()
    {
        return $this->belongsTo(config('suitcoresite.newsletters.template_model'), 'template_id');
    }

    protected function curate()
    {
        foreach (config('suitcoresite.newsletters.datas', []) as $key => $value) {
            if (is_callable($value)) {
                $value = $value();
            }
            $this->additionalDatas[$key] = $value;
        }
    }

    public function toHtml()
    {
        $this->curate();
        $template = $this->template->html;

        $html = preg_replace_callback(
            "/\[\[(.*)\]\]/",
            function ($words) {
                $key = strtolower($words[1]);

                return $this->{$key} ?? $this->additionalDatas[$key] ?? null;
            },
            $template
        );

        return $html;
    }
}
