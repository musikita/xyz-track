<?php

namespace Suitcorecms\Medialibrary;

use Closure;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Suitcorecms\Medialibrary\OnTheFlyConversion\HasOnTheFlyMediaTrait;
use Suitcorecms\Seo\Contract\HasSeo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HasMediaTrait
{
    // protected $mediaFields = [];

    // protected $multipleMedias = [];
    //
    protected $savingMedias = [];

    use HasOnTheFlyMediaTrait {
        bootHasMediaTrait as baseBootHasMediaTrait;
        registerAllMediaConversions as baseRegisterAllMediaConversions;
    }

    public $mediaProperties;
    public $customMediaProperties;
    protected $sortable = [];
    protected $finalMediaFields = [];
    protected $finalMultipleMedias = [];

    public function dontSortWhenCreating()
    {
        $this->sortable['sort_when_creating'] = false;

        return $this;
    }

    public static function bootHasMediaTrait()
    {
        static::baseBootHasMediaTrait();
        static::observe(app(HasMediaObserver::class));

        $mediaClass = config('medialibrary.media_model');
        $mediaClass::observe(app(MediaObserver::class));
    }

    protected function withMedia($builder)
    {
        if (!count($this->mediaFields ?? [])) {
            return $builder;
        }

        return $builder->with('media');
    }

    protected function createThumbnailConversion()
    {
        if (count($collections = $this->getThumbSpecificCollection())) {
            $this->addMediaConversion('thumb')
                ->width(config('suitcorecms.medialibrary.thumbnail.width', 100))
                ->height(config('suitcorecms.medialibrary.thumbnail.height', 100))
                ->performOnCollections(...$collections);
        }
    }

    protected function getThumbSpecificCollection()
    {
        $exluded = $this->excludedThumbCollections ?? [];
        if (count($exluded) == 0) {
            return $this->getMediaFields();
        }

        return array_diff($this->getMediaFields(), $exluded);
    }

    public function registerAllMediaConversions(Media $media = null)
    {
        $this->baseRegisterAllMediaConversions($media);
        $this->createThumbnailConversion();
        if ($this instanceof HasSeo) {
            $this->seoMediaConversion();
        }
        $this->callOnTheFlyConversions($media);
    }

    public function getMediaObject($name, Closure $next = null)
    {
        $media = $this->getFirstMedia($name);

        if ($next === null) {
            return $media ?? null;
        }

        return $media ? $next($media) : null;
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->getMediaFields())) {
            if (in_array($key, $this->getMultipleMedias() ?? [])) {
                return $this->getMedia($key);
            }

            return $this->getFirstMedia($key);
        }

        return parent::getAttribute($key);
    }

    public function hideMedia()
    {
        return $this->setHidden(array_unique(array_merge($this->hidden, ['media'])));
    }

    public function fill(array $attributes)
    {
        $medias = [];
        foreach ($this->getMediaFields() as $key) {
            if (isset($attributes[$key])) {
                $medias[$key] = $attributes[$key];
                unset($attributes[$key]);
            }
        }

        $this->fillMedia($medias);

        return parent::fill($attributes);
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getMediaFields())) {
            $this->fillMedia([$key => $value]);

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    public function fillMedia(array $medias = [])
    {
        $this->savingMedias = array_replace($this->savingMedias, $medias);

        return $this;
    }

    public function getSavingMedia()
    {
        return $this->savingMedias;
    }

    public function getMediaFields()
    {
        $fields = $this->mediaFields ?? [];
        if ($this instanceof HasSeo) {
            $fields = array_merge($this->mediaFields ?? [], $this->getSeoMediaFields());
        }

        return $fields;
    }

    public function getMultipleMedias()
    {
        return $this->multipleMedias ?? [];
    }

    public function excludedMimeForResponsiveImage()
    {
        return $this->responsiveImageExcludedMimes ?? ['image/svg+xml'];
    }

    public function addToMedia(array $medias = null)
    {
        if (!count($medias)) {
            $medias = $this->getMediaFields();
        }

        foreach ($medias as $name => $field) {
            if ($files = is_numeric($name) ? request($field) : $field) {
                $files = is_array($files) ? $files : [$files];
                if (!is_numeric($name)) {
                    $field = $name;
                }
                foreach ($files as $file) {
                    $mediaAdder = (is_string($file) && strpos($file, 'http') === 0) ? $this->addMediaFromUrl($file) : $this->addMedia($file)->preservingOriginal();
                    if (!in_array($field, $this->getMultipleMedias() ?? [])) {
                        $this->clearMediaCollection($field);
                    }
                    if (method_exists($this, $method = Str::camel('set '.$field.' Media'))) {
                        call_user_func_array([$this, $method], [$mediaAdder, $file]);
                    }

                    $this->addingMediaHandler($mediaAdder, $field, $file);
                    $media = $mediaAdder;
                    if ($this->isExcludedFromMimeType($file, $this->excludedMimeForResponsiveImage())) {
                        $media->withResponsiveImages();
                    }
                    $media->toMediaCollection($field);
                    if (isset($this->sortable['sort_when_creating'])) {
                        $media->sortWhenCreating($this->sortable['sort_when_creating']);
                    }
                }
            }
        }
    }

    protected function isExcludedFromMimeType($file, array $excludedMimeTypes)
    {
        if (empty($excludedMimeTypes)) {
            return true;
        }
        $file = is_string($file) ? new File($file) : $file;
        $validation = Validator::make(['file' => $file], ['file' => 'mimetypes:'.implode(',', $excludedMimeTypes)]);

        return $validation->fails();
    }

    public function deleteFromMedia()
    {
        foreach ($this->getMediaFields() ?? [] as $field) {
            if ($collection = $this->getDeletingMedia($field)) {
                if (!in_array($field, $this->multipleMedias ?? [])) {
                    $this->clearMediaCollection($field);
                } else {
                    $collection->each->delete();
                }
            }
        }
    }

    public function addingMediaHandler($fileAddr, $field, $file)
    {
        if ($prop = $this->mediaProperties ?? false) {
            $fileAddr->withProperties($prop);
        }
        if ($custom = $this->customMediaProperties ?? false) {
            $fileAddr->withCustomProperties($custom);
        }

        $newName = $this->setMediaName($file);
        $extension = $this->getMediaExtension($file);
        $fileAddr->setName($newName)->setFileName(rtrim($newName.'.'.$extension, '.'));
    }

    /*
     * Set the file that needs to be imported.
     *
     * @param string|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return $this
     */
    public function getMediaFilename($file)
    {
        if (is_string($file)) {
            return pathinfo($file, PATHINFO_FILENAME);
        }

        if ($file instanceof UploadedFile) {
            return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        }

        if ($file instanceof SymfonyFile) {
            return pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }
    }

    public function getMediaExtension($file)
    {
        if (is_string($file)) {
            return pathinfo($file, PATHINFO_EXTENSION);
        }

        if ($file instanceof UploadedFile) {
            return pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        }

        if ($file instanceof SymfonyFile) {
            return pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        }
    }

    public function setMediaName($file)
    {
        return Str::slug(Str::limit($this->getMediaFilename($file), 200, '').' '.md5(uniqid()));
    }

    public function getMediaProperties()
    {
        return array_merge($this->mediaProperties ?? [], $this->mediaCustomProperties() ?? []);
    }

    public function mediaCustomProperties()
    {
        //
    }

    protected function getDeletingMedia($field)
    {
        //
    }
}
