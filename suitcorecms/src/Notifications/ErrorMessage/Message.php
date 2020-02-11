<?php

namespace Suitcorecms\Notifications\ErrorMessage;

use Suitcorecms\Notifications\FlashMessage\Message as BaseMessage;

class Message extends BaseMessage
{
    protected $transportName = 'notification.errormessage';

    public $type = 'warning';

    public $title = 'Error';

    protected function catchErrors()
    {
        return session()->get('errors');
    }

    public function check()
    {
        return session()->has('errors');
    }

    public function pull()
    {
        $message = parent::pull();
        if (!$message) {
            return null;
        }
        $message->message = $this->catchErrors();

        return $message;
    }

    public function receive()
    {
        $message = parent::receive();
        if (!$message) {
            return null;
        }
        $message->message = $this->catchErrors();

        return $message;
    }
}
