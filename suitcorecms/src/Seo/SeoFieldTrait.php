<?php

namespace Suitcorecms\Seo;

use Illuminate\Support\Str;

trait SeoFieldTrait
{
    protected function seoFieldName()
    {
        return config('suitcorecms.seo.form.field_name');
    }

    protected function setSeoMetaName($name, $group = null)
    {
        $group = $group ? '['.$group.']' : null;

        return $this->seoFieldName().$group.'['.$name.']';
    }

    protected function seoGrouped()
    {
        $general = config('suitcorecms.seo.form.general') ?? [$this, 'seoGeneralFields'];
        $openGraph = config('suitcorecms.seo.form.open_graph') ?? [$this, 'seoOpenGraphFields'];
        $twitterCard = config('suitcorecms.seo.form.twitter_card') ?? [$this, 'seoTwitterCardFields'];

        return array_filter([
            'General'      => is_callable($general) ? $general() : $general,
            'Open Graph'   => is_callable($openGraph) ? $openGraph() : $openGraph,
            'Twitter Card' => is_callable($twitterCard) ? $twitterCard() : $twitterCard,
        ]);
    }

    public function seoFields()
    {
        return [
            'Seo Tools' => [
                'type'      => 'dropdownmultigroup',
                'name'      => $this->seoFieldName(),
                'groups'    => $this->seoGrouped(),
            ],
        ];
    }

    public function seoValidationRules()
    {
        $rules = [];
        foreach ($this->seoGrouped() as $group) {
            foreach ($group as $title => $field) {
                if ($rule = $field['rules'] ?? false) {
                    $rules[$field['name'] ?? Str::snake($this->getTitle())] = $rule;
                }
            }
        }

        return $rules;
    }

    public function seoValidationRuleMessages()
    {
        return [];
    }

    public function seoValidationRuleAttributes()
    {
        $attributes = [];
        foreach ($this->seoGrouped() as $group) {
            foreach ($group as $title => $field) {
                if ($rule = $field['rules'] ?? false) {
                    $attributes[$field['name'] ?? Str::snake($title)] = $title;
                }
            }
        }

        return $attributes;
    }

    protected function seoGeneralFields()
    {
        return [
            'Title' => [
                'type'       => 'text',
                'name'       => $this->setSeoMetaName('title'),
                'rules'      => 'max:70',
                'attributes' => [
                    'maxlength' => '70',
                ],
            ],
            'Description' => [
                'type'       => 'text',
                'name'       => $this->setSeoMetaName('description'),
                'rules'      => 'max:320',
                'attributes' => [
                    'maxlength' => '320',
                ],
            ],
            'Image' => [
                'type' => 'image',
                'name' => $this->setSeoMetaName('image'),
            ],
        ];
    }

    protected function seoOpenGraphFields()
    {
        return [
            'Title' => [
                'type'       => 'text',
                'name'       => $this->setSeoMetaName('title', 'og'),
                'rules'      => 'max:70',
                'attributes' => [
                    'maxlength' => '70',
                ],
            ],
            'Description' => [
                'type'       => 'text',
                'name'       => $this->setSeoMetaName('description', 'og'),
                'rules'      => 'max:320',
                'attributes' => [
                    'maxlength' => '320',
                ],
            ],
        ];
    }

    protected function seoTwitterCardFields()
    {
        return [
            'Title' => [
                'type'       => 'text',
                'name'       => $this->setSeoMetaName('title', 'twitter'),
                'rules'      => 'max:70',
                'attributes' => [
                    'maxlength' => '70',
                ],
            ],
            'Description' => [
                'type'       => 'text',
                'name'       => $this->setSeoMetaName('description', 'twitter'),
                'rules'      => 'max:320',
                'attributes' => [
                    'maxlength' => '320',
                ],
            ],
        ];
    }
}
