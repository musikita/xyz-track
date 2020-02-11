<?php

namespace Suitcorecms\Notifications;

class MessageTransport
{
    protected $via;

    public function via($via)
    {
        $this->via = $via;

        return $this;
    }

    public function __call($func, $params = [])
    {
        return call_user_func_array([call_user_func($this->via), $func], $params);
    }
}
