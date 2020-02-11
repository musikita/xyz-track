<?php

namespace Suitcorecms\Calendars;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Calendar
{
    protected $dbScopes;
    protected $dataSource;
    protected $eventProcessor;

    public function __construct()
    {
        $this->dbScopes = $this->parseRequests();
    }

    public static function of(CalendarResourceable $dataSource)
    {
        $instance = app(static::class);

        return $instance->setDataSource($dataSource);
    }

    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function setEventProcessor(callable $processor)
    {
        $this->eventProcessor = $processor;

        return $this;
    }

    public function parseRequests()
    {
        return function ($query) {
            $startField = $this->dataSource->startCalendarField();
            $query->where($startField, '>=', $this->queryDateTime(request('start')))
                ->where($startField, '<=', $this->queryDateTime(request('end')));
        };
    }

    protected function query()
    {
        return $this->dataSource->calendarQuery();
    }

    protected function queryDateTime($dateTime)
    {
        return new Carbon($dateTime);
    }

    protected function processEvents(Collection $events)
    {
        if ($this->eventProcessor) {
            return $events->map(function ($item) {
                return call_user_func_array($this->eventProcessor, [$item]);
            })->values();
        }

        return $events;
    }

    public function toJson()
    {
        $events = $this->query()
                    ->where($this->dbScopes)
                    ->get();

        return $this->processEvents($events)->toArray();
    }
}
