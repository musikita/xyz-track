<?php

namespace Suitcorecms\Translations;

use Illuminate\Database\Eloquent\Relations\HasOne;
use RichanFongdasen\I18n\Eloquent\Extensions\TranslateableTrait;
use RichanFongdasen\I18n\Eloquent\TranslationModel;

trait TranslationTrait
{
    use TranslateableTrait;

    public function translation()
    {
        $model = new TranslationModel();
        $model->setTable($this->getTranslationTable());

        return (new HasOne(
            $model->newQuery(),
            $this,
            $this->getForeignKey(),
            $this->getKeyName()
        ))
         ->where($this->getTranslationTable().'.locale', \App::getLocale());
    }
}
