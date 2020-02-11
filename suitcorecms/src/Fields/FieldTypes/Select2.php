<?php

namespace Suitcorecms\Fields\FieldTypes;

class Select2 extends Select
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes'] = array_merge($this->attributes['attributes'] ?? [], ['select2' => true]);

        $select2 = parent::formBuild($builder, $value, $newName);
        $style = $this->formCss();

        return $select2.$style;
    }

    public function formJavascript()
    {
        if (!defined('SELECT2_JS')) {
            define('SELECT2_JS', true);

            return <<<'JavaScript'
                $('[select2]').on('select2:select', function (e) {
                    $(this).valid();
                }).select2();
JavaScript;
        }

        return '';
    }

    public function formCss()
    {
        if (!defined('SELECT2_CSS')) {
            define('SELECT2_CSS', true);

            return <<<'HTML'
                <style>
                    .select2 .select2-selection__choice {
                        color: #fff !important;
                        background-color: #5767dd !important;
                        border-color: #5767dd !important;
                    }
                    .select2 .select2-selection__choice__remove {
                        color: #fff !important;
                    }
                    .on-validation .select2-selection--single .select2-selection__arrow,
                    .on-validation .select2-selection--multiple .select2-selection__arrow {
                        right: 25px;
                    }

                </style>
HTML;
        }

        return '';
    }
}
