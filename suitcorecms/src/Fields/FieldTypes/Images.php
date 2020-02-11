<?php

namespace Suitcorecms\Fields\FieldTypes;

use Illuminate\Support\HtmlString;
use Spatie\MediaLibrary\Models\Media;

class Images extends BasicField
{
    protected $attributes = [
        'searchable' => false,
        'orderable'  => false,
        'multiple'   => true,
    ];

    protected $specificAttributes = ['multiple'];

    public function datatablesOutput($model)
    {
        $medias = $model instanceof Media ? $model : $model->getMedia($this->getName());

        $big = null;
        foreach ($medias as $media) {
            $big .= ($media ? ($media->getExtensionAttribute() == 'svg' ? new HtmlString("<img src='{$media->getUrl()}' style='max-width: 300px; max-height: 300px;'>") : $media('thumb', ['style' => 'max-width: 300px; max-height: 300px;', 'data-image-url' => $media->getUrl()])) : null).'<br>';
        }

        $big = str_replace('"', '', $big);
        $html = null;
        foreach ($medias->take(2) as $media) {
            $html .= $media ? ($media->getExtensionAttribute() == 'svg' ? new HtmlString("<img src='{$media->getUrl()}' style='max-width: 30px; max-height: 30px;'>") : $media('thumb', ['style' => 'max-width: 30px; max-height: 30px;', 'data-image-url' => $media->getUrl()])) : null;
        }
        if (0 < $remain = $medias->count() - 2) {
            $html .= ' <div class="kt-badge kt-badge--sm kt-badge--dark"><small>'.$remain.'+</small></div>';
        }

        if ($medias->count()) {
            $html .= $this->popoverHtml('images', $this->getTitle(), $big);
        }

        return $html;
    }

    public function datatablesJavascript()
    {
        return $this->popoverJavascript('images', '250px', '300px');
    }

    public function formBuild($builder, $value = null, $newName = null)
    {
        $this->attributes['attributes'] = array_merge($this->attributes['attributes'], ['multiple' => true]);
        $name = $this->getName();
        $file = $this->oneTypeField($builder, 'file', $newName.'[]');
        $deleteInput = config('suitcorecms.fields.image.delete_name').'['.change_bracket_to_dot($name).'][]';
        $html = '';
        $model = $builder->getModel();
        if ($model->exists && ($medias = $model instanceof Media ? $model : $model->getMedia(change_bracket_to_dot($name)))) {
            foreach ($medias as $img) {
                $picture = $img('thumb', ['class' => 'img-thumbnail', 'id' => 'image-field-'.$img->id]);
                if ($img->getExtensionAttribute() == 'svg') {
                    $picture = new HtmlString("<img src='{$img->getUrl()}' id='image-field-{$img->id}' class='img-thumbnail'>");
                }
                $html .= <<<HTML
                <div class="row mb-2 existed-image-container">
                    <div class="col-6 col-md-3">
                        {$picture}
                    </div>
                    <div class="col-6 pl-2 my-auto existed-image-button">
                        <a href="{$img->getUrl()}" class="btn-image-download btn btn-xs btn-secondary mr-2 mb-2" role="button" ><i class="la la-download"></i> Download</a>
                        <label class="kt-checkbox kt-checkbox--solid kt-checkbox--danger">
                            <input type="checkbox" name="{$deleteInput}" value="{$img->name}" data-image="#image-field-{$img->id}" onchange="imageDeleteToggles(this)"> Delete Image
                            <span></span>
                        </label>
                    </div>
                </div> 
HTML;
            }
        }

        return $html.$file;
    }

    public function formJavascript()
    {
        return <<<'JavaScript'
            // Image Field JavaScript
            var removeImages = function(obj) {
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

            var revertImages = function(obj) {
                image = $(obj.data('image'));
                image.attr('src', image.data('original-url')).attr('alt', image.data('original-alt'));
            }

            var imageDeleteToggles = function (obj) {
                obj = $(obj);
                if (obj.is(':checked')) {
                    removeImages(obj);
                    obj.parents('.existed-image-button').find('.btn-image-download').addClass('disabled').attr('aria-disabled', 'true');
                } else {
                    revertImages(obj);
                    obj.parents('.existed-image-button').find('.btn-image-download').removeClass('disabled').removeAttr('aria-disabled');
                }
            }
            // End of Image Field JavaScript
JavaScript;
    }

    public function showOutput($model, $value)
    {
        $name = $this->getName();
        $html = null;

        if ($model->exists && ($medias = $model instanceof Media ? $model : $model->getMedia($name))) {
            if (count($medias)) {
                foreach ($medias as $img) {
                    $picture = $img('thumb', ['class' => 'img-thumbnail', 'id' => 'image-field-'.$name]);
                    if ($img->getExtensionAttribute() == 'svg') {
                        $picture = new HtmlString("<img src='{$img->getUrl()}' id='image-field-{$name}' class='img-thumbnail'>");
                    }

                    $html .= new HtmlString("<a href='{$img->getUrl()}' target='_blank'>{$picture}</a>");
                }
            }
        }

        return $html ?? '<em>(No Image)</em>';
    }
}
