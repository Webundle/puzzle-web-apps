$(function() {
    altair_fullcalendar.calendar_selectable();
});

altair_fullcalendar = {
    calendar_selectable: function() {
        var $calendar_selectable = $('#calendar_selectable');

        if($calendar_selectable.length) {
            $calendar_selectable.fullCalendar({
                lang:'fr',
                header: {
                    left: 'title today',
                    center: '',
                    right: 'month,agendaWeek,agendaDay prev,next'
                },
                buttonIcons: {
                    prev: 'md-left-single-arrow',
                    next: 'md-right-single-arrow',
                    prevYear: 'md-left-double-arrow',
                    nextYear: 'md-right-double-arrow'
                },
                buttonText: {
                    today: ' ',
                    month: ' ',
                    week: ' ',
                    day: ' '
                },
                aspectRatio: 2.1,
                defaultDate: moment(),
                selectable: false,
                selectHelper: false,
                // select: function(start, end) {

                //     $("#started_at").val(start.format('YYYY-MM-DD'));
                //     $("#ended_at").val(end.format('YYYY-MM-DD'));

                //     UIkit.modal("#new_moment").show();

                // },
                editable: false,
                eventLimit: true,
                timeFormat: '(HH)(:mm)',
                events: {
                    url: Routing.generate('admin_calendar_moment_list'),
                    type: 'POST',
                    data: {}
                }
            });
        }
    },
};