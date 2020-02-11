<!--begin: Calendar -->
<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__body">
        <div class="mb-4">
            @if ($childResource->routeExist('create'))
            <a href="{{$childResource->routeCreate()}}" class="btn btn-sm btn-primary btn-icon-h">
                Add New &nbsp;
                <i class="flaticon2-plus"></i>
            </a>
            @endif

            <div class="pull-right">
                @if ($childResource->routeExist('export-csv'))
                <a href="{{$childResource->route('export-csv')}}" class="btn btn-warning  btn-sm btn-icon-h">
                    Export CSV &nbsp;
                    <i class="fa fa-file-csv"></i>
                </a>
                @endif
                @if ($childResource->routeExist('import'))
                <a href="{{$childResource->route('import')}}" class="btn btn-primary  btn-sm btn-icon-h">
                    Import &nbsp;
                    <i class="fa fa-file-excel"></i>
                </a>
                @endif
            </div>
        </div>
        <div id="kt_calendar"></div>
    </div>
</div>
<!--end: Calendar -->
@push('scripts-footer')
<script type="text/javascript">
    jQuery(function () {
        var todayDate = moment().startOf('day');
        var TODAY = todayDate.format('YYYY-MM-DD');
        var calendarEl = document.getElementById('kt_calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
            isRTL: KTUtil.isRTL(),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            height: 800,
            contentHeight: 780,
            aspectRatio: 3,  // see: https://fullcalendar.io/docs/aspectRatio

            nowIndicator: true,
            now: TODAY, // just for demo

            views: {
                dayGridMonth: { buttonText: 'month' },
                timeGridWeek: { buttonText: 'week' },
                timeGridDay: { buttonText: 'day' }
            },

            defaultView: 'dayGridMonth',
            defaultDate: TODAY,

            eventLimit: true, // allow "more" link when too many events
            navLinks: true,
            selectable: true,
            events: function (info, successCallback, failureCallback) {
                $.ajax({
                    url: '{{$childResource->route('index')}}',
                    type: 'GET',
                    dataType: "json",
                    contentType: "application/json",
                    data: {
                        start: info.startStr,
                        end: info.endStr,
                    }
                }).done(function (data) {
                    successCallback(data)
                }).fail(function () {
                    failureCallback()
                })
            },

            eventRender: function(info) {
                var element = $(info.el);

                if (info.event.extendedProps && info.event.extendedProps.description) {
                    if (element.hasClass('fc-day-grid-event')) {
                        element.data('content', info.event.extendedProps.description);
                        element.data('placement', 'top');
                        element.data('trigger', 'click');
                        element.attr('data-html', 'true');
                        element.data('hide-on-outside-click', 'true');
                        KTApp.initPopover(element);
                    } else if (element.hasClass('fc-time-grid-event')) {
                        element.find('.fc-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>'); 
                    } else if (element.find('.fc-list-item-title').lenght !== 0) {
                        element.find('.fc-list-item-title').append('<div class="fc-description">' + info.event.extendedProps.description + '</div>'); 
                    }
                } 
            }
        });

        calendar.render();
    })
</script>
@endpush
