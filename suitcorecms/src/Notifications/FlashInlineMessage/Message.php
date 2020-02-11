<?php

namespace Suitcorecms\Notifications\FlashInlineMessage;

use Suitcorecms\Notifications\FlashMessage\Message as BaseMessage;

class Message extends BaseMessage
{
    protected $transportName = 'notification.flashinlinemessage';
}
