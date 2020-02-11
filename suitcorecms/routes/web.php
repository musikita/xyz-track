<?php

if (!defined('CMS_ROUTE_PATH')) {
    if ($cmsRoutePath = config('suitcorecms.routes')) {
        define('CMS_ROUTE_PATH', $cmsRoutePath);

        Route::domain(config('suitcorecms.base_domain'))
            ->name('cms.')
            ->prefix(config('suitcorecms.prefix_url'))
            ->middleware(config('suitcorecms.middlewares'))
            ->group(function () {
                Route::namespace(config('suitcorecms.namespace'))
                    ->group(function () {
                        require CMS_ROUTE_PATH;
                    });
            });
    }
}

Route::any('robot.txt', '\\Suitcorecms\\Controllers\\RobotController@robot');
