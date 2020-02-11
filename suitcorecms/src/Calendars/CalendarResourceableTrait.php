<?php

namespace Suitcorecms\Calendars;

trait CalendarResourceableTrait
{
    public function startCalendarField()
    {
        return $this->startCalendarField;
    }

    public function endCalendarField()
    {
        return property_exists($this, 'endCalendarField') ? $this->endCalendarField : false;
    }

    public function toCalendarEvent()
    {
        return [
            'start' => $this->{$this->startCalendarField()},
            'end'   => ($endField = $this->endCalendarField()) ? $this->{$endField} : null,
            'title' => $this->getCaption(),
        ];
    }

    public function calendarQuery()
    {
        return $this->query();
    }
}
