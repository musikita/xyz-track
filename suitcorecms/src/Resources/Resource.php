<?php

namespace Suitcorecms\Resources;

use Illuminate\Http\Request;
use Suitcorecms\Fields\Field;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Seo\Contract\HasSeo;
use Suitcorecms\Seo\SeoFieldTrait;

class Resource
{
    use ResourceButtonTrait;
    use ResourceCalendarTrait;
    use ResourceChildrenTrait;
    use ResourceDatatablesTrait;
    use ResourceFormTrait;
    use ResourceRequestTrait;
    use ResourceShowTrait;
    use ResourceUrlTrait;
    use ResourceValidatorTrait;
    use SeoFieldTrait;

    protected $title;
    protected $name;
    protected $prefix;
    protected $field;
    protected $method;
    protected $resourceable;
    protected $fields = [];
    protected $rules;
    protected $timestamps;
    protected $withoutSeo = false;

    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    public static function make(Resourceable $resourceable, array $fields = null, array $rules = null, array $timestamps = null)
    {
        return app(static::class)
            ->setResourceable($resourceable)
            ->setFields($fields)
            ->setRules($rules)
            ->setTimestamps($timestamps);
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function setResourceable(Resourceable $resourceable)
    {
        $this->resourceable = $resourceable;

        return $this;
    }

    public function setFields(array $fields = null)
    {
        $this->fields = $fields ?? [];

        return $this;
    }

    public function useSeo()
    {
        $this->withoutSeo = false;

        return $this;
    }

    public function dontUseSeo()
    {
        $this->withoutSeo = true;

        return $this;
    }

    public function addFields(array $fields)
    {
        foreach ($fields as $key => $field) {
            $this->fields[$key] = $field;
        }

        return $this;
    }

    public function setRules(array $rules = null)
    {
        $this->rules = $rules ?? [];

        return $this;
    }

    public function setTimestamps(array $timestamps = null)
    {
        $this->timestamps = $timestamps ?? [];

        return $this;
    }

    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    public function getName()
    {
        return $this->name ?? $this->setName($this->resourceable->getName())->name;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getResourceable()
    {
        return $this->resourceable;
    }

    public function fields($method = null, $fresh = false)
    {
        $method = $method ?? $this->method;
        if ($this->fieldset[$method] ?? false) {
            if ($fresh) {
                $this->fieldset[$method] = null;
            } else {
                return $this->fieldset[$method];
            }
        }

        if ($this->resourceable instanceof HasSeo) {
            if (!$this->withoutSeo) {
                $this->fields = array_merge($this->fields, $this->seoFields());
            }
        }

        return $this->fieldset[$method] = $this->field->setFields($this->fields + $this->timestamps)->{$method}();
    }

    public function create(Request $request)
    {
        $this->processRequest('create', $request);
        $this->resourceable = $this->resourceable->create($request->all());

        return $this;
    }

    public function update(Request $request)
    {
        $this->processRequest('update', $request);
        $this->resourceable->update($request->all());

        return $this;
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->resourceable, $method], $params);
    }
}
