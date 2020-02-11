<?php

namespace Suitcorecms\ControllerTagging;

use Illuminate\Validation\Rule;

trait HasTaggingModelTrait
{
    protected static $tag;

    public static function bootHasTaggingModelTrait()
    {
        static::addGlobalScope(new HasTaggingModelScope());
    }

    public static function setTag($tag)
    {
        static::$tag = $tag;
    }

    public static function getTag()
    {
        return static::$tag;
    }

    public function getTaggingField()
    {
        return $this->tagging_field;
    }

    public function getSiblingIds($request)
    {
        return static::withoutGlobalScopes()->where($request->only($this->tagging_field))->pluck('id')->toArray();
    }

    public function taggingUniqueRule($method, $request)
    {
        $allIds = $this->getSiblingIds($request);
        $unique = Rule::unique($this->getTable())->whereIn($this->getKeyName(), $allIds);

        return $method == 'create'
            ? $unique
            : $unique->ignore($this->getKey());
    }

    public function taggingTranslateableUniqueRule($method, $request)
    {
        $allIds = $this->getSiblingIds($request);
        $unique = Rule::unique($this->getTranslationTable())->whereIn($this->getForeignKey(), $allIds);

        return $method == 'create'
            ? $unique
            : $unique->ignore($this->getKey(), $this->getForeignKey());
    }
}
