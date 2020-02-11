<?php

namespace Suitcorecms\Resources;

use Suitcorecms\Calendars\Calendar;

trait ResourceCalendarTrait
{
    protected $eventProcessor;

    public function calendarJson()
    {
        return Calendar::of($this->resourceable)
            ->setEventProcessor($this->eventProcessor())
            ->toJson();
    }

    public function setEventProcessor(callable $processor)
    {
        $this->eventProcessor = $processor;

        return $this;
    }

    public function eventProcessor()
    {
        return $this->eventProcessor ?? [$this, 'eventCalendar'];
    }

    public function eventCalendar($event)
    {
        return $event->toCalendarEvent();
    }
}
