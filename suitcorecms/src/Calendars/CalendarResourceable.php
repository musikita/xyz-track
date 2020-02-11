<?php

namespace Suitcorecms\Calendars;

interface CalendarResourceable
{
    public function startCalendarField();

    public function endCalendarField();

    public function toCalendarEvent();
}
