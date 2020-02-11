<?php

namespace Suitcorecms\Excel\Imports\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Images
{
    protected $googleDrive;

    public function __construct()
    {
        $this->googleDrive = \Storage::disk('google');
    }

    public function getImagesFieldValue($field, $value)
    {
        $images = collect([]);
        collect(explode(',', $value))
            ->each('trim')
            ->each(function ($val) use ($images) {
                $this->pushToImages($images, $val);
            });

        return $images->toArray();
    }

    protected function pushToImages(Collection $images, $val)
    {
        if (Str::contains($val, 'drive.google.com')) {
            $this->pushFromGoogleDrive($images, $val);
        } else {
            $images->push($val);
        }
    }

    protected function getGoogleDriveId($url)
    {
        $queries = collect(explode('&', parse_url($url, PHP_URL_QUERY)));
        $id = $queries->filter(function ($item) {
            return Str::contains($item, 'id=');
        })->first();

        return $id ? str_replace('id=', '', $id) : null;
    }

    public function isGoogleDriveDirectory($id)
    {
        try {
            $metadata = $this->googleDrive->getMetadata($id);
            $result = $metadata['type'] == 'dir';
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    protected function getGoogleDriveFile($id)
    {
        return $this->googleDrive->getAdapter()->getUrl($id);
    }

    protected function pushFromGoogleDriveDirectory($images, $id)
    {
        $contents = collect($this->googleDrive->listContents($id, $recursive = true));
        $contents->where('type', '=', 'file')->each(function ($item) use ($images) {
            $id = $item['basename'];
            $file = $this->getGoogleDriveFile($id);
            $images->push($file);
        });
    }

    protected function pushFromGoogleDrive(Collection $images, $url)
    {
        if ($id = $this->getGoogleDriveId($url)) {
            if ($this->isGoogleDriveDirectory($id)) {
                $this->pushFromGoogleDriveDirectory($images, $id);
            } else {
                try {
                    $file = $this->getGoogleDriveFile($id);
                    $images->push($file);
                } catch (\Exception $e) {
                    info('No Images in Google Drive '.$url);
                }
            }
        }
    }
}
