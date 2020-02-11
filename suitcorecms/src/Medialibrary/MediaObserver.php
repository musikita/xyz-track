<?php

namespace Suitcorecms\Medialibrary;

use Spatie\MediaLibrary\MediaObserver as Observer;

class MediaObserver extends Observer
{
    protected static $scope;

    public static function setScope($scope)
    {
        static::$scope = $scope;
    }

    public function saving($media)
    {
        $media->fill($media->model->getMediaProperties());
        if ($scope = static::$scope) {
            $media->scope = $scope;
        }
    }
}
