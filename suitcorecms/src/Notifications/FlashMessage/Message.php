<?php

namespace Suitcorecms\Notifications\FlashMessage;

use Suitcorecms\Notifications\MessageTransport;

class Message
{
    protected $transportName = 'notification.flashmessage';

    public $type = 'info';

    public $title;

    public $message;

    public $buttonText = 'Ok';

    protected $transport;

    public function __construct()
    {
        $this->transport = (new MessageTransport())
                            ->via([$this, 'transport']);
    }

    public static function create($message = null)
    {
        $messenger = app(static::class);
        $messenger->message = $message;

        return $messenger;
    }

    public function recall()
    {
        $this->transport->send($this);

        return $this;
    }

    public function getTransport()
    {
        return $this->transport;
    }

    public function transport()
    {
        return $this;
    }

    public function title($title)
    {
        $this->title = $title ?? $this->title;

        return $this->recall();
    }

    public function message($message)
    {
        $this->message = $message;

        return $this->recall();
    }

    public function type($type)
    {
        if ($type) {
            $this->type = $type;
        }

        return $this->recall();
    }

    public function success()
    {
        return $this->type('success');
    }

    public function info()
    {
        return $this->type('info');
    }

    public function error()
    {
        return $this->type('error');
    }

    public function danger()
    {
        return $this->type('danger');
    }

    public function warning()
    {
        return $this->type('warning');
    }

    public function canCancel($bag)
    {
        app(\Suitcorecms\Notifications\Message::class)->destroy($bag);

        return $this;
    }

    public function check()
    {
        return session()->has($this->transportName);
    }

    public function pull()
    {
        return $this->check() ? session()->pull($this->transportName, null) : null;
    }

    public function receive()
    {
        return $this->check() ? session()->get($this->transportName, null) : null;
    }

    public function destroy()
    {
        return session()->forget($this->transportName);
    }

    public function send($package)
    {
        $this->destroy();
        session()->put($this->transportName, $package);
    }

    public function html()
    {
    }

    public function scripts()
    {
        $this->destroy();

        return <<<Javascript
                Swal.fire('{$this->title}', '{$this->message}', '{$this->type}');
Javascript;
    }
}
