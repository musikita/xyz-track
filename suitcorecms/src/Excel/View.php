<?php

namespace Suitcorecms\Excel;

use Illuminate\Support\Facades\Route;

class View
{
    public static function javascript($routeName = 'cms.excel.checker')
    {
        if (Route::has($routeName)) {
            $url = route($routeName);

            return <<<JavaScript
                (function ($) {
                    var download = function (url, filename) {
                        var a = document.createElement("a");
                        a.href = url;
                        a.setAttribute("download", filename);
                        a.click();
                    }
                    setInterval(function () {
                        $.getJSON('{$url}', function (data) {
                            if (data.export) {
                                data.export.forEach(function (el) {
                                    download(el.url, el.filename);
                                });
                            }
                        });
                    }, 20000);
                })(jQuery);
JavaScript;
        }
    }
}
