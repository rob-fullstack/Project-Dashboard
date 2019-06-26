<div id="page-content" class="p20 clearfix weekly">
    <div class="panel panel-default">
        <div class="page-title clearfix">
          <h4><?php echo lang('project_weekly'); ?></h4>
            <div class="title-button-group">
              <?php if ($can_filter) { ?>
                  <select class="user_filter" name="user_filter" id="user_filter">
                      <option value="all" selected>Filter all users</option>
                      <?php foreach ($users as $user) { ?>
                          <option value="<?php echo $user['id'] ?>"><?php echo $user['full_name']; ?></option>
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
            </div>
        </div>
        <div class="p15 bg-white">
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-default" id="reload-kanban-button"><i class="fa fa-refresh"></i></button>
                    <a href="#" class="btn btn-default" title="Import Projects" data-act="ajax-modal" data-title="Import Projects" data-action-url="<?php echo get_uri('weekly/import')?>"><i class="fa fa-plus-circle"></i> Import Projects</a>
                    <a href="#" class="btn btn-default" title="Import Projects Manually" data-act="ajax-modal" data-title="Import Projects Manually" data-action-url="404"><i class="fa fa-plus-circle"></i> Import Projects Manually</a>

                <?php foreach ($users as $key => $user) { ?>
                  <span id="user_<?php echo $user['id']; ?>" class="avatar-sm avatar pull-right mt-5 mr10 weekly-avatar">
                    <img alt="<?php echo $user['full_name']; ?>" src="<?php echo ($user['image'] ? base_url().'files/profile_images/'.$user['image'] : base_url().'assets/images/avatar.jpg'); ?>">
                  </span>
                <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div id="weekly-kanban" class="gridster">
      <ul id="weekly-grid">
        <li data-row="1" data-col="1" data-sizex="1" data-sizey="1">
          <div class="kanban-col-title" style="background: #b9b9b9;"> 2 Weeks </div>
        </li>
        <li data-row="1" data-col="2" data-sizex="1" data-sizey="1">
          <div class="kanban-col-title" style="background: #5AA574;"> Ready to start </div>
        </li>
        <li data-row="1" data-col="3" data-sizex="1" data-sizey="1">
          <div class="kanban-col-title" style="background: #F2B03F;"> Monday </div>
        </li>
        <li data-row="1" data-col="4" data-sizex="1" data-sizey="1">
          <div class="kanban-col-title" style="background: #F2B03F;"> Tuesday </div>
        </li>
        <li data-row="1" data-col="5" data-sizex="1" data-sizey="1">
          <div class="kanban-col-title" style="background: #F2B03F;"> Wednesday </div>
        </li>
        <li data-row="1" data-col="6" data-sizex="1" data-sizey="1">
          <div class="kanban-col-title" style="background: #F2B03F;"> Thursday </div>
        </li>
        <li data-row="1" data-col="7" data-sizex="1" data-sizey="1">
          <div class="kanban-col-title" style="background: #F2B03F;"> Friday </div>
        </li>
      </ul>
    </div>
    <div id="weekly-board" class="gridster">
      <ul id="project-grid">
        <li id="proj-123" data-row="1" data-col="1" data-sizex="1" data-sizey="1">
          <div class="kanban-item"> Test Project </div>
        </li>
        <li id="proj-234" data-row="2" data-col="1" data-sizex="1" data-sizey="1">
          <div class="kanban-item"> Test Project 2</div>
        </li>
      </ul>
    </div>
</div>

  <script type="text/javascript">
    $(document).ready(function () {

        var $userSelect  = $('#user_filter');
        var $teamSelect  = $('#team_filter');

        $userSelect.select2();
        $teamSelect.select2();

        var weeklygrid = $("#weekly-grid").gridster({
          widget_margins: [10, 10],
          widget_base_dimensions: [220, 50],
          max_row: 1,
          min_cols: 1,
          max_cols: 7
        }).data('gridster');

        weeklygrid.disable();

        var projectgrid = $("#project-grid").gridster({
          widget_margins: [10, 5],
          widget_base_dimensions: [220, 50],
          resize: {
            enabled: true,
            axes: ['x'],
            max_size: [5,1],
            stop: function(e, ui, $widget){
              var newDimensions = this.serialize($widget)[0];

              //prevent from resizing on col 1 and 2
              if(newDimensions.col == 1 || newDimensions.col == 2){
                var widgetId = $($widget).attr('id');
                //$('#'+widgetId).attr('data-sizex', 1);
              }

            }
          },
          draggable: {
            stop: function(e, ui) {
              var newPosition = ui.$player[0].dataset;

              //revert size when dragging to col 1 and 2
              if(newPosition.col == 1 || newPosition.col == 2){
                var widgetId = $(ui.$helper[0]).attr('id');
                //$('#'+widgetId).attr('data-sizex', 1);
              }
            }
          },
          min_cols: 1,
          max_cols: 7,
        }).data('gridster');

        function update_grid(grid) {
          var gridData = grid.serialize();
        }

        <?php foreach ($users as $key => $user): ?>

        var bar_<?php echo $user['id']; ?> = new ProgressBar.Circle(user_<?php echo $user['id']; ?>, {
          color: '#28B513',
          trailColor: '#eee',
          trailWidth: 8,
          duration: 2800,
          easing: 'easeInOut',
          strokeWidth: 8,
          from: {color: '#28B513', a:0},
          to: {color: '#E96464', a:1},
          // Set default step function for all animate calls
          step: function(state, circle) {
            circle.path.setAttribute('stroke', state.color);
          }
        });

        bar_<?php echo $user['id']; ?>.animate(<?php echo $user['hours']?>);

        <?php endforeach; ?>
    });

  </script>
