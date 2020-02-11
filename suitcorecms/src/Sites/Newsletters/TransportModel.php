<?php

namespace Suitcorecms\Sites\Newsletters;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Suitcorecms\Resources\Contracts\Resourceable;
use Suitcorecms\Resources\ResourceableTrait;

class TransportModel extends BaseModel implements Resourceable
{
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';

    use ResourceableTrait;

    protected $guarded = [];
    protected $baseName = 'Newsletter Deliveries';
    protected $captionField = 'name';

    public function __construct(array $attributes = [])
    {
        $this->table = config('suitcoresite.newsletters.transport_table', 'transports');
        parent::__construct($attributes);
    }

    public function newsletter()
    {
        return $this->belongsTo(config('suitcoresite.newsletters.model', NewsletterModel::class), 'newsletter_id');
    }

    public static function transport($newsletter, $email)
    {
        $transport = static::create([
            'newsletter_id' => $newsletter->id,
            'email'         => $email,
            'uuid'          => Uuid::uuid4(),
            'status'        => static::STATUS_SENDING,
        ]);

        Mail::to($email)->send(new \Suitcorecms\Sites\Newsletters\Mail\Newsletter($newsletter));

        $transport->status = static::STATUS_SENT;
        $transport->save();

        return $transport;
    }

    public function getNameAttribute()
    {
        return $this->newsletter->title.' to '.$this->email;
    }
}
