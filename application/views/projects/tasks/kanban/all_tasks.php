<div id="page-content" class="p20 pb0 clearfix">

    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("tasks"); ?></h4></li>

        <?php $this->load->view("projects/tasks/tabs", array("active_tab"=>"tasks_kanban")); ?>       
      
        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php
                if ($can_create_tasks) {
                    echo modal_anchor(get_uri("projects/task_modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_task'), array("class" => "btn btn-default", "title" => lang('add_task')));
                }
                ?>
            </div>
        </div>
    </ul>
    <div class="p15 bg-white pb0">
        <div class="row">
            <div class="col-md-1">
                <button class="btn btn-default" id="reload-kanban-button"><i class="fa fa-refresh"></i></button>
            </div>
            <div id="kanban-filters" class="col-md-11"></div>
        </div>
    </div>

    <div id="load-kanban"></div>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("projects/all_tasks_kanban_data") ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            search: {name: "search"},
            filterDropdown: [
                {name: "specific_user_id", class: "w200", options: <?php echo $team_members_dropdown; ?>},
                {name: "milestone_id", class: "w200", options: [{id: "", text: "- <?php echo lang('milestone'); ?> -"}], dependency: ["project_id"], dataSource: '<?php echo_uri("projects/get_milestones_for_filter") ?>'}, //milestone is dependent on project
                {name: "project_id", class: "w200", options: <?php echo $projects_dropdown; ?>, dependent: ["milestone_id"]}, //reset milestone on changing of project               
            ],
            singleDatepicker: [{name: "deadline", defaultText: "<?php echo lang('deadline') ?>",
                    options: [
                        {value: "expired", text: "<?php echo lang('expired') ?>"},
                        {value: moment().format("YYYY-MM-DD"), text: "<?php echo lang('today') ?>"},
                        {value: moment().add(1, 'days').format("YYYY-MM-DD"), text: "<?php echo lang('tomorrow') ?>"},
                        {value: moment().add(7, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(lang('in_number_of_days'), 7); ?>"},
                        {value: moment().add(15, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(lang('in_number_of_days'), 15); ?>"}
                    ]}],
            beforeRelaodCallback: function () {
                scrollLeft = $("#kanban-wrapper").scrollLeft();
            },
            afterRelaodCallback: function () {
                setTimeout(function () {
                    $("#kanban-wrapper").animate({scrollLeft: scrollLeft}, 'slow');
                }, 500);

            }
        });

    });

</script>