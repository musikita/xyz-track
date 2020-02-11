<?php

namespace Suitcorecms\Notifications;

use Exception;

class NotificationException extends Exception
{
    use NotificationTrait;

    public function notificationHandler($request)
    {
        $this->message()
            ->flash($this->getMessage())
            ->error();

        return back();
    }
}
