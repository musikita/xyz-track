<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\HtmlString;
use Spatie\MediaLibrary\Models\Media;

class Image extends BasicField
{
    protected $attributes = [
        'searchable' => false,
        'orderable'  => false,
    ];

    public function datatablesOutput($model)
    {
        $media = $model instanceof Media ? $model : $model->getFirstMedia($this->getName());

        return $media ? ($media->getExtensionAttribute() == 'svg' ? new HtmlString("<img src='{$media->getUrl()}' style='max-width: 50px; max-height: 50px;'>") : $media('thumb', ['style' => 'max-width: 50px; max-height: 50px;', 'data-image-url' => $media->getUrl()])) : null;
    }

    public function formBuild($builder, $value = null, $newName = null)
    {
        $name = $this->getName();
        $file = $this->oneTypeField($builder, 'file', $newName);
        $deleteInput = config('suitcorecms.fields.image.delete_name');
        $html = '';
        $model = $builder->getModel();
        if ($model->exists && ($img = $model instanceof Media ? $model : $model->getFirstMedia(change_bracket_to_dot($name)))) {
            $picture = $img('thumb', ['class' => 'img-thumbnail', 'id' => 'image-field-'.$name]);
            if ($img->getExtensionAttribute() == 'svg') {
                $picture = new HtmlString("<img src='{$img->getUrl()}' id='image-field-{$name}' class='img-thumbnail'>");
            }
            $html .= <<<HTML
                <div class="row mb-2 existed-image-container">
                    <div class="col-6 col-md-3">
                        {$picture}
                    </div>
                    <div class="col-6 pl-2 my-auto existed-image-button">
                        <a href="{$img->getUrl()}" class="btn-image-download btn btn-xs btn-secondary mr-2 mb-2" role="button" ><i class="la la-download"></i> Download</a>
                        <label class="kt-checkbox kt-checkbox--solid kt-checkbox--danger">
                            <input type="checkbox" name="{$deleteInput}" value="{$name}" data-image="#image-field-{$name}" onchange="imageDeleteToggle(this)"> Delete Image
                            <span></span>
                        </label>
                    </div>
                </div> 
HTML;
        }

        return $html.$file;
    }

    public function formJavascript()
    {
        return <<<'JavaScript'
            // Image Field JavaScript
            var removeImage = function(obj) {
                image = $(obj.data('image'));
                url = image.attr('src');
                alt = image.attr('alt');
                height = image.outerHeight();
                width = image.outerWidth();
                newUrl = 'https://via.placeholder.com/'+Math.round(width)+'x'+Math.round(height)+'.png?text=Deleted+Image';
                image.data('original-url', url);
                image.data('original-alt', alt);
                image.attr('src', newUrl).attr('alt', 'Deleted Image').css('width', width).css('height', height);
            }

            var revertImage = function(obj) {
                image = $(obj.data('image'));
                image.attr('src', image.data('original-url')).attr('alt', image.data('original-alt'));
            }

            var imageDeleteToggle = function (obj) {
                obj = $(obj);
                if (obj.is(':checked')) {
                    removeImage(obj);
                    obj.parents('.existed-image-button').find('.btn-image-download').addClass('disabled').attr('aria-disabled', 'true');
                    obj.parents('.existed-image-container').siblings('[type=file]').attr('disabled', 'disabled');
                } else {
                    revertImage(obj);
                    obj.parents('.existed-image-button').find('.btn-image-download').removeClass('disabled').removeAttr('aria-disabled');
                    obj.parents('.existed-image-container').siblings('[type=file]').removeAttr('disabled');
                }
            }
            // End of Image Field JavaScript
JavaScript;
    }

    public function showOutput($model, $value)
    {
        $name = $this->getName();
        if ($model->exists && ($img = $model instanceof Media ? $model : $model->getFirstMedia(change_bracket_to_dot($name)))) {
            $picture = $img('thumb', ['class' => 'img-thumbnail', 'id' => 'image-field-'.$name]);
            if ($img->getExtensionAttribute() == 'svg') {
                $picture = new HtmlString("<img src='{$img->getUrl()}' id='image-field-{$name}' class='img-thumbnail'>");
            }

            return new HtmlString("<a href='{$img->getUrl()}' target='_blank'>{$picture}</a>");
        }

        return '<em>(No Image)</em>';
    }
}
