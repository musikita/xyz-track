<?php

namespace Suitcorecms\Sites\Newsletters;

use Carbon\Carbon;

class Newsletter
{
    protected $model;

    protected $newsletters;

    public static function send($id = null, $email = null)
    {
        $instance = app(static::class);
        $newsletter = $id ? $instance->find($id) : $instance->all();
        $email = $email ?? $instance->getNewsletterRecipients();

        return $newsletter->sentTo($email);
    }

    public function __construct()
    {
        $this->model = config('suitcoresite.newsletters.model', NewsletterModel::class);
    }

    public function find($id)
    {
        $model = $this->model;
        $this->newsletters = collect([$model::canSend()->findOrFail($id)]);

        return $this;
    }

    public function all()
    {
        $model = $this->model;
        $this->newsletters = $model::notSent()->canSend()->get();

        return $this;
    }

    protected function sending($newsletter, $email)
    {
        $transportModel = config('suitcoresite.newsletters.transport_model', TransportModel::class);
        $transport = $transportModel::transport($newsletter, $email);

        return $transport->newsletter;
    }

    public function sentTo($email = null)
    {
        if (!$this->newsletters || !count($this->newsletters)) {
            throw new \Exception('Instance not exist', 1);
        }

        $emails = (array) $email;

        $sent = [];
        $unsent = [];
        foreach ($this->newsletters as $newsletter) {
            $success = false;
            $sent[$newsletter->id] = [];
            $unsent[$newsletter->id] = [];
            foreach ($emails as $email) {
                try {
                    $this->sending($newsletter, $email);
                    $sent[$newsletter->id][] = $email;
                    $success = true;
                } catch (\Exception $e) {
                    $unsent[$newsletter->id][] = $email;
                }
            }

            if ($success) {
                $newsletter->sent_at = Carbon::now();
                $newsletter->save();
            }
        }

        return [$this->newsletters, $sent, $unsent];
    }

    public static function newsletterRecipients()
    {
        return \Suitcorecms\Sites\Subscribers\Model::pluck('email')->toArray();
    }

    protected function getNewsletterRecipients()
    {
        return call_user_func(config('suitcoresite.newsletters.recipients', [static::class, 'newsletterRecipients']));
    }
}
