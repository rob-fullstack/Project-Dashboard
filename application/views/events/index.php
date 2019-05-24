<?php
load_css(array(
    "assets/js/fullcalendar/fullcalendar.min.css"
));

load_js(array(
    "assets/js/fullcalendar/fullcalendar.min.js",
    "assets/js/fullcalendar/lang-all.js",
    "assets/js/bootstrap-confirmation/bootstrap-confirmation.js"
));

$client = "";
if (isset($client_id)) {
    $client = $client_id;
}
?>

<div id="page-content<?php echo $client; ?>" class="p20<?php echo $client; ?> clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <?php if ($client) { ?>
                <h4><?php echo lang('events'); ?></h4>
            <?php } else { ?>
                <h1><?php echo lang('event_calendar'); ?></h1>
            <?php } ?>
            <div class="title-button-group">
                <?php if ($can_filter) { ?>
                    <select class="colab_filter" name="colab_filter" id="colab_filter">
                        <option value="all" selected>Filter collaborators</option>
                        <?php foreach ($users as $user) { ?>
                            <option value="<?php echo $user->id ?>"><?php echo $user->first_name . ' ' . $user->last_name ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
                <?php if ($can_filter) { ?>
                    <select class="user_filter" name="user_filter" id="user_filter">
                        <option value="all" selected>Filter all users</option>
                        <?php foreach ($users as $user) { ?>
                            <option value="<?php echo $user->id ?>"><?php echo $user->first_name . ' ' . $user->last_name ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
                <?php if (!empty($teams)) { ?>
                    <select class="team_filter" name="team_filter" id="team_filter">
                        <option value="all" selected>Filter all teams</option>
                        <?php foreach ($teams as $team) { ?>
                            <?php $member_ids = implode(',', array_map(function($entry) {
                                return $entry->id;
                            }, $team['members'])); ?>
                            <option value="<?php echo $member_ids ?>"><?php echo $team['title'] ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
                <?php if ($can_filter && !empty($milestones)) { ?>
                    <select class="miles_filter" name="miles_filter" id="miles_filter">
                        <option value="all" selected>Filter Milestones</option>
                        <?php foreach ($milestones as $milestone) { ?>
                            <option value="<?php echo $milestone->id ?>"><?php echo $milestone->title; ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
                <?php echo modal_anchor(get_uri("events/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_event'), array("class" => "btn btn-default", "title" => lang('add_event'), "data-post-client_id" => $client)); ?>
                <?php echo modal_anchor(get_uri("events/modal_form"), "", array("class" => "hide", "id" => "add_event_hidden", "title" => lang('add_event'), "data-post-client_id" => $client)); ?>
                <?php echo modal_anchor(get_uri("events/view"), "", array("class" => "hide", "id" => "show_event_hidden", "data-post-client_id" => $client, "data-post-cycle" => "0", "data-post-editable" => "1", "title" => lang('event_details'))); ?>
                <?php echo modal_anchor(get_uri("leaves/application_details"), "", array("class" => "hide", "data-post-id" => "", "id" => "show_leave_hidden")); ?>
                <?php echo modal_anchor(get_uri("projects/task_view"), "", array("class" => "hide", "id" => "preview_task_link", "title" => lang('task_info'))); ?>
            </div>
        </div>
        <div class="panel-body">
            <div id="event-calendar"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        var $userSelect  = $('#user_filter');
        var $teamSelect  = $('#team_filter');
        var $colabSelect  = $('#colab_filter'); 
        var $milesSelect  = $('#miles_filter'); 
        var $eventCalendar = $('#event-calendar');

        $userSelect.select2();
        $teamSelect.select2();
        $colabSelect.select2();
        $milesSelect.select2();

        $eventCalendar.fullCalendar({
            allDay: true,
            editable: <?php echo $can_edit ?  'true' : 'false'; ?>,
            lang: AppLanugage.locale,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: "<?php echo_uri("events/calendar_events/" . $client); ?>", 
            dayClick: function (date, jsEvent, view) {
                $("#add_event_hidden").attr("data-post-start_date", date.format("YYYY-MM-DD"));
                var startTime = date.format("HH:mm:ss");
                if (startTime === "00:00:00") {
                    startTime = "";
                }
                $("#add_event_hidden").attr("data-post-start_time", startTime);
                var endDate = date.add(1, 'hours');

                $("#add_event_hidden").attr("data-post-end_date", endDate.format("YYYY-MM-DD"));
                var endTime = "";
                if (startTime != "") {
                    endTime = endDate.format("HH:mm:ss");
                }

                $("#add_event_hidden").attr("data-post-end_time", endTime);
                $("#add_event_hidden").trigger("click");
            },
            eventClick: function (calEvent, jsEvent, view) {
                var isAdmin = <?php echo $can_edit ?  'true' : 'false'; ?>

                $("#show_event_hidden").attr("data-post-id", calEvent.encrypted_event_id);
                $("#show_event_hidden").attr("data-post-cycle", calEvent.cycle);
                $("#show_leave_hidden").attr("data-post-id", calEvent.encrypted_event_id);

                if (calEvent.event_type === "event") {
                    $("#show_event_hidden").trigger("click");
                } else if (calEvent.event_type === 'task') {
                    $('#preview_task_link').attr({
                        'data-id': calEvent.task.id,
                        'data-project_id': calEvent.task.project_id,
                        'data-post-id': calEvent.task.id,
                        'title': 'Task info #' + calEvent.task.id,
                        'data-title': 'Task info #' + calEvent.task.id,
                        'data-editable': isAdmin
                    }).trigger("click")
                } else {
                    $("#show_leave_hidden").trigger("click");
                }
            },
            eventRender: function (event, element) {
                //console.log(event.task.collaborators);
                 $(element).find('.fc-title').html(function() {
                    var text = $(this).text().trim();
                    var splits = text.split(' | ');
                    return splits[0] + ' | <strong>' + splits[1] + '</strong>';
                });
                 
                if (event.event_type === 'task') {                    
                    element.addClass('event-task');
                }
                if (event.collaborator) {
                    element.addClass('event-collaborator');
                }
                if (event.icon) {
                    element.find(".fc-title").prepend("<i class='fa " + event.icon + "'></i> ");
                }
                if (event.avatar) {
                    element.find(".fc-content").append('<img class="img-circle event-task-assignee" src="/files/profile_images//' + event.avatar + '" alt="User Avatar" title="' + event.user_name + '">')
                } else {
                    element.find(".fc-content").append('<img class="img-circle event-task-assignee" src="/assets/images/avatar.jpg" alt="User Avatar" title="' + event.user_name + '">')
                }

                if ($milesSelect.length) {
                    if($milesSelect.val() !== 'all'){
                        $colabSelect.prop('disabled', true);
                        $userSelect.prop('disabled', true);
                        $teamSelect.prop('disabled', true);
                        return ['all', event.task.milestone_id].indexOf($milesSelect.val()) >= 0;
                    }else{
                        $colabSelect.prop('disabled', false);
                        $userSelect.prop('disabled', false);
                        $teamSelect.prop('disabled', false);
                    }      
                }

                if ($teamSelect.length) {
                    if ($teamSelect.val() !== 'all') {
                        $userSelect.prop('disabled', true);
                        $milesSelect.prop('disabled', true);
                        $colabSelect.prop('disabled', true);
                        var members = $teamSelect.val().split(',');
                        return members.some(function (v) {
                            return ['all', event.assigned_to].indexOf(v) >= 0;
                        });
                    } else {
                        $userSelect.prop('disabled', false);
                        $milesSelect.prop('disabled', false);
                        $colabSelect.prop('disabled', false);
                    }
                }

                if ($userSelect.length) {
                    if ($userSelect.val() !== 'all') {
                        $teamSelect.prop('disabled', true);
                        $milesSelect.prop('disabled', true);
                        $colabSelect.prop('disabled', true);
                        return ['all', event.assigned_to].indexOf($userSelect.val()) >= 0;
                    } else {
                        $teamSelect.prop('disabled', false);
                        $milesSelect.prop('disabled', false);
                        $colabSelect.prop('disabled', false);
                    }
                } 

                if ($colabSelect.length) {
                    if($colabSelect.val() !== 'all'){
                        $userSelect.prop('disabled', true);
                        $milesSelect.prop('disabled', true);
                        $teamSelect.prop('disabled', true);
                        var taskCollaborators = event.task.collaborators.split(',')                    
                        return taskCollaborators.indexOf($colabSelect.val()) >= 0;
                    }else{
                        $userSelect.prop('disabled', false);
                        $milesSelect.prop('disabled', false);
                        $teamSelect.prop('disabled', false);
                    }      
                }


            },
            eventDrop: function (event) {
                if (event.event_type === 'task') {
                    var start    = moment(event.start._d).format(event.start._f);
                    var deadline = event.end === null ? start : moment(event.end._d).subtract(1, 'days').format(event.end._f);

                    // If start date is available use that, otherwise use the deadline
                    var comparison = event.task.start_date || event.task.deadline

                    if (start === comparison) {
                        return false
                    }

                    $.ajax({
                        url: '/index.php/projects/save_task',
                        type: 'POST',
                        data: {
                            assigned_to  : event.task.assigned_to,
                            collaborators: event.task.collaborators,
                            deadline     : deadline,
                            deleted      : event.task.deleted,
                            description  : event.task.description,
                            id           : event.task.id,
                            labels       : event.task.labels,
                            milestone_id : event.task.milestone_id,
                            points       : event.task.points,
                            project_id   : event.task.project_id,
                            sort         : event.task.sort,
                            start_date   : start,
                            status       : event.task.status,
                            status_id    : event.task.status_id,
                            title        : event.task.title
                        }
                    });
                }
            },
            eventResize: function (event, jsEvent, ui, view) {
                if (event.event_type === "task") {

                    var start    = moment(event.start._d).format(event.start._f);
                    var deadline = moment(event.end._d).subtract(1, 'days').format(event.end._f);

                    var comparison = event.task.deadline || event.task.start_date

                    if (deadline === comparison) {
                        return false;
                    }

                    $.ajax({
                        url: '/index.php/projects/save_task',
                        type: 'POST',
                        data: {
                            assigned_to  : event.task.assigned_to,
                            collaborators: event.task.collaborators,
                            deadline     : deadline,
                            deleted      : event.task.deleted,
                            description  : event.task.description,
                            id           : event.task.id,
                            labels       : event.task.labels,
                            milestone_id : event.task.milestone_id,
                            points       : event.task.points,
                            project_id   : event.task.project_id,
                            sort         : event.task.sort,
                            start_date   : start,
                            status       : event.task.status,
                            status_id    : event.task.status_id,
                            title        : event.task.title
                        }
                    });
                }
            },
            firstDay: AppHelper.settings.firstDayOfWeek
        });

        var client = "<?php echo $client; ?>";
        if (client) {
            setTimeout(function () {
                $eventCalendar.fullCalendar('today');
            });
        }

        // Filter
        $userSelect.on('change',function(){
            $eventCalendar.fullCalendar('rerenderEvents');
        })

        $teamSelect.on('change',function(){
            $eventCalendar.fullCalendar('rerenderEvents');
        })

        $colabSelect.on('change',function(){
            $eventCalendar.fullCalendar('rerenderEvents');
        })

        $milesSelect.on('change',function(){
            $eventCalendar.fullCalendar('rerenderEvents');
        })

        // Refresh table on task save
        $(document).ajaxComplete(function( event, xhr, settings ) {
            if ( settings.url.indexOf('save_task') !== -1 ) {
                $eventCalendar.fullCalendar('refetchEvents');
            }
        });

        //autoload the event popover
        var encrypted_event_id = "<?php echo isset($encrypted_event_id) ? $encrypted_event_id : ''; ?>";

        if (encrypted_event_id) {
            $("#show_event_hidden").attr("data-post-id", encrypted_event_id);
            $("#show_event_hidden").trigger("click");
        }

    });
</script>
