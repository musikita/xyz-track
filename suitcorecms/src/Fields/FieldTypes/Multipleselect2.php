<?php

namespace Suitcorecms\Fields\FieldTypes;

class Multipleselect2 extends Multipleselect
{
    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes'] = array_merge($this->attributes['attributes'] ?? [], ['select2' => true, 'data-tags' => true]);

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
        if (!defined('MULTIPLE_SELECT2_CSS')) {
            define('MULTIPLE_SELECT2_CSS', true);

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

                </style>
HTML;
        }

        return '';
    }
}
