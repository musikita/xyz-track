<?php

namespace Suitcorecms\Sites\Settings;

use Illuminate\Support\Facades\Cache;
use Suitcorecms\Cms\Route;

class Setting
{
    protected $cacheName = 'suitcoresite.site_settings';

    protected $configName = 'suitcoresite.site_settings';

    protected static $settings = [];

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        if (!count(static::$settings)) {
            static::$settings = $this->getSettings();
        }

        return $this;
    }

    protected function crawlSettings()
    {
        $fromConfigFile = $this->fromConfigFile();
        $formDatabase = $this->fromDatabase();

        return array_replace($fromConfigFile, $formDatabase);
    }

    protected function getSettings()
    {
        return Cache::rememberForever($this->cacheName, function () {
            return $this->crawlSettings();
        });
    }

    protected function getValue($array, $key, $default = null)
    {
        return $array[$key] ?? (is_callable($default) ? $default() : $default);
    }

    public function fromConfigFile($key = false)
    {
        $settings = config($this->configName, []);

        return $key ? $this->getValue($settings, $key) : $settings;
    }

    public function fromDatabase($key = false)
    {
        return $key ? array_first(Model::where('key', $key)->pluck('value')->toArray()) : Model::pluck('value', 'key')->toArray();
    }

    public function clearCache()
    {
        Cache::forget($this->cacheName);
        static::$settings = [];

        return $this;
    }

    public function get($key, $default = null)
    {
        return $this->init()->getValue(static::$settings, $key, $default);
    }

    public function all()
    {
        $this->init();

        return static::$settings;
    }

    public static function seedDatabase()
    {
        foreach ($fromFiles = (new static())->fromConfigFile() as $key => $value) {
            $setting = Model::firstOrNew(['key' => $key]);
            if (!$setting->exists) {
                $setting->value = $value;
                $setting->save();
            }
        }
    }

    public static function cmsRoutes($uri = 'settings')
    {
        Route::resource($uri, CmsController::class)->only('index', 'edit', 'update');
    }
}
