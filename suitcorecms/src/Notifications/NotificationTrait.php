<?php

namespace Suitcorecms\Notifications;

trait NotificationTrait
{
    public function message()
    {
        return new Message();
    }
}
