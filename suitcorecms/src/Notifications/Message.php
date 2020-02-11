<?php

namespace Suitcorecms\Notifications;

class Message
{
    protected static $bags = [];

    protected function callMessenger($messenger, $message = null, $title = null, $type = null)
    {
        $messenger = $messenger::create($message);

        return $messenger->title($title)->type($type);
    }

    protected function loadBags($messengers = null)
    {
        $messengers = $messengers ?: config('suitcorecms.notifications.messengers', []);
        foreach ($messengers as $name => $messenger) {
            static::registerBag($name, app($messenger));
        }
    }

    public function publishViews($messengers = null)
    {
        if (!count(self::$bags)) {
            $this->loadBags(func_get_args());
        }

        return $this;
    }

    public function destroy($bag)
    {
        $this->publishViews();
        $exist = self::$bags[$bag] ?? false;

        return $exist ? $exist->getTransport()->destroy() : false;
    }

    public function pull($bag)
    {
        $this->publishViews();
        $exist = self::$bags[$bag] ?? false;

        return $exist ? $exist->getTransport()->pull() : false;
    }

    public function check($bag)
    {
        $this->publishViews();
        $exist = self::$bags[$bag] ?? false;

        return $exist ? $exist->getTransport()->check() : false;
    }

    public function receive($bag)
    {
        $this->publishViews();
        $exist = self::$bags[$bag] ?? false;

        return $exist ? $exist->getTransport()->receive() : false;
    }

    public function html($bagName = null, callable $html = null)
    {
        $this->publishViews();
        $showHtml = '';
        foreach (self::$bags as $name => $bag) {
            if (!$bagName || $bagName == $name) {
                if ($this->check($name)) {
                    $showHtml .= $html ? $html($bag->messenger()->getTransport()->receive()) : $bag->html();
                }
            }
        }

        return $showHtml;
    }

    public function scripts($bagName = null, callable $scripts = null)
    {
        $this->publishViews();
        $showScripts = '';
        foreach (self::$bags as $name => $bag) {
            if (!$bagName || $bagName == $name) {
                if ($this->check($name)) {
                    $showScripts .= $scripts ? $scripts($bag->messenger()->getTransport()->receive()) : $bag->scripts();
                }
            }
        }

        return $showScripts;
    }

    public static function registerBag($name, $bag)
    {
        if (!isset(self::$bags[$name])) {
            self::$bags[$name] = $bag;
        }
    }

    public function __call($func, $args = [])
    {
        $messengers = config('suitcorecms.notifications.messengers', []);
        if ($messenger = $messengers[strtolower($func)] ?? false) {
            $args = array_merge([$messenger], $args);

            return call_user_func_array([$this, 'callMessenger'], $args);
        }

        throw new \BadMethodCallException("Method not found {$func}", 1);
    }
}
