<?php

namespace Suitcorecms\Resources;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Arr;
use Suitcorecms\Seo\Contract\HasSeo;

trait ResourceRequestTrait
{
    use ValidatesRequests;

    protected $ruleMessages = [];

    protected $ruleAttributes = [];

    protected function createRequestFieldName($name)
    {
        return !$this->prefix ? $name : "{$this->prefix}.{$name}";
    }

    protected function getRulesFromFields()
    {
        return array_filter(Arr::pluck($this->fields(), 'rules', 'name'));
    }

    protected function arrayableRules($rules)
    {
        $rules = is_array($rules) ? $rules : (is_string($rules) ? explode('|', $rules) : [$rules]);

        return array_filter(array_map(function ($item) {
            return is_string($item) ? trim($item) : $item;
        }, $rules));
    }

    protected function ruleMerge($rules, $addrules)
    {
        $merged = $rules;
        foreach ($addrules as $key => $value) {
            $value = $this->arrayableRules($value);
            if (isset($rules[$key])) {
                $oldValue = $this->arrayableRules($rules[$key]);
                $value = array_unique(array_merge($value, $oldValue));
            }
            $merged[$key] = $value;
        }

        return $merged;
    }

    public function rules($method = null, $request = null)
    {
        $method = $method ?? $this->method;
        $request = $request ?? request();
        $rules = $this->getRulesFromFields();

        if (method_exists($this->resourceable, 'rules')) {
            if (method_exists($this->resourceable, 'rules')) {
                $rules = $this->ruleMerge($rules, $this->resourceable->rules($method, $request) ?? []);
            }
        }

        if ($this->resourceable instanceof HasSeo) {
            $this->rules = array_merge($this->rules, $this->seoValidationRules());
        }

        return $this->ruleMerge($rules, $this->rules);
    }

    public function validRules($method = null, $request = null)
    {
        $rules = $this->rules($method, $request);

        $validRules = [];
        foreach ($rules as $key => $value) {
            $key = strpos($key, '[') === false ? $key : str_replace('[', '.', str_replace(']', '', $key));
            $validRules[$key] = $value;
        }

        return $validRules;
    }

    public function setRuleMessages(array $ruleMessages = null)
    {
        $this->ruleMessages = $ruleMessages ?? [];

        return $this;
    }

    public function setRuleAttributes(array $ruleAttributes = null)
    {
        $this->ruleAttributes = $ruleAttributes ?? [];

        return $this;
    }

    public function ruleMessages($method)
    {
        $ruleMessages = [];
        if (method_exists($this->resourceable, 'ruleMessages')) {
            $ruleMessages = $this->resourceable->ruleMessages($method) ?? [];
        }

        if ($this->resourceable instanceof HasSeo) {
            $this->ruleMessages = array_merge($this->ruleMessages, $this->seoValidationRuleMessages());
        }

        return array_replace_recursive($ruleMessages, $this->ruleMessages);
    }

    public function ruleAttributes($method)
    {
        $ruleAttributes = [];
        if (method_exists($this->resourceable, 'ruleAttributes')) {
            $ruleAttributes = $this->resourceable->ruleAttributes($method) ?? [];
        }

        if ($this->resourceable instanceof HasSeo) {
            $this->ruleAttributes = array_merge($this->ruleAttributes, $this->seoValidationRuleAttributes());
        }

        $ruleAttributes = array_replace_recursive($ruleAttributes, $this->ruleAttributes);

        $validAttributes = [];
        foreach ($ruleAttributes as $key => $value) {
            $key = strpos($key, '[') === false ? $key : str_replace('[', '.', str_replace(']', '', $key));
            $validAttributes[$key] = $value;
        }

        return $validAttributes;
    }

    public function processRequest($method, $request)
    {
        $request->merge($this->loadRequestProcessorFromFields($request, $method));
        $rules = [];
        foreach ($this->validRules($method, $request) as $name => $rule) {
            $name = $this->createRequestFieldName($name);
            $rules[$name] = $rule;
        }

        $this->validate($request, $rules, $this->ruleMessages($method), $this->ruleAttributes($method));

        return $request;
    }

    protected function loadRequestProcessorFromFields($request, $method)
    {
        $newRequests = [];
        foreach ($fields = $this->fields($method) as $field) {
            if ($name = $this->createRequestFieldName($field['name'])) {
                // make sure it's not null for name of field
                $value = $request->get($name, null);
                if ($input = $field['input'] ?? false) {
                    $newRequests[$name] = call_user_func_array($input, [$value, $request]);
                }
            }
        }

        return $newRequests;
    }
}
