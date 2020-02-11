<?php

namespace Suitcorecms\Sites\Newsletters\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Suitcorecms\Sites\Newsletters\Contracts\Newsletterable;

class Newsletter extends Mailable
{
    use Queueable;
    use SerializesModels;
    public $newsletter;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Newsletterable $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->html($this->newsletter->toHtml());
    }
}
