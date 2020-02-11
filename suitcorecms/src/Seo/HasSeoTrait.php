<?php

namespace Suitcorecms\Seo;

use Illuminate\Support\Facades\App;
use Suitcorecms\Seo\Contract\HasSeo;

trait HasSeoTrait
{
    public static function bootHasSeoTrait()
    {
        if (in_array(HasSeo::class, get_declared_interfaces())) {
            static::observe(app(HasSeoObserver::class));
            $model = static::getSeoModelClass();
            $model::observe(app(static::getSeoModelObserverClass()));
        }
    }

    public function isTranslateSeo()
    {
        return static::$translateSeo ?? false;
    }

    public function seoTranslationLocale()
    {
        return $this->isTranslateSeo() ? \App::getLocale() : '';
    }

    public static function getSeoModelClass()
    {
        return config('suitcorecms.seo.seo_model', Model::class);
    }

    public static function getSeoModelObserverClass()
    {
        return config('suitcorecms.seo.seo_model_observer', ModelObserver::class);
    }

    public function getSeoField()
    {
        return config('suitcorecms.seo.form.field_name');
    }

    public function getSeoMediaFields()
    {
        return app(static::getSeoModelClass())->getMediaFields();
    }

    public function seoMediaConversion()
    {
        return app(static::getSeoModelClass())->createSeoMediaConversion($this);
    }

    public function seo()
    {
        return app(SeoTool::class)->setRawMeta($this->seoData->meta ?? []);
    }

    public function seoData()
    {
        return $this->seoModel()->where('locale', $this->seoTranslationLocale());
    }

    public function seoModel()
    {
        return $this->morphOne(static::getSeoModelClass(), 'model');
    }
}
