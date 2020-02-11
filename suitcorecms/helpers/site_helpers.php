<?php

if (!function_exists('site_setting')) {
    function site_setting($key, $default = null)
    {
        $setting = new Suitcorecms\Sites\Settings\Setting();

        return $setting->get($key, $default);
    }
}

if (!function_exists('notification')) {
    function notification($name)
    {
        return (new \Suitcorecms\Notifications\Notification())->{$name}();
    }
}

if (!function_exists('show_flash_message')) {
    function show_flash_message($message)
    {
        $content = $message->message;
        $content = is_callable($content) ? $content() : $content;

        return <<<HTML
            Swal.fire('{$message->title}', '{$content}', '{$message->type}');
HTML;
    }
}

if (!function_exists('show_notification_messages')) {
    function show_notification_messages()
    {
        if ($message = notification('message')->pull('flash')) {
            return show_flash_message($message);
        }
    }
}
