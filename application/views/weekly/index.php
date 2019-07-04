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
                  <a href="<?php echo get_uri('weekly/refresh_grid/'.$grid_id);?>" class="btn btn-default" id="reload-kanban-button"><i class="fa fa-refresh"></i></a>
                  <button class="btn btn-default" id="clear-kanban-button"><i class="fa fa-trash"></i></button>
                  <a href="#" class="btn btn-default" title="Import Projects" data-act="ajax-modal" data-title="Import Projects" data-action-url="<?php echo get_uri('weekly/import');?>"><i class="fa fa-plus-circle"></i> Import Projects</a>
                    <a href="#" class="btn btn-default" title="Import Projects Manually" data-act="ajax-modal" data-title="Import Projects Manually" data-action-url="<?php echo get_uri('weekly/import_manual');?>"><i class="fa fa-plus-circle"></i> Import Projects Manually</a>

                <?php foreach ($users as $key => $user) { ?>
                  <span id="user_<?php echo $user['id']; ?>" class="avatar-sm avatar pull-right mt-5 mr10 weekly-avatar">
                    <img alt="<?php echo $user['full_name']; ?>" src="<?php echo ($user['image'] ? base_url().'files/profile_images/'.$user['image'] : base_url().'assets/images/avatar.jpg'); ?>">
                  </span>
                <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div id="project-gridly">
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
          <?php $i = 1; if(!empty($grid_data)): foreach ($grid_data as $key => $widget) {

            $labels = "";

            if (isset($widget['deadline']) || !empty($widget['deadline'])) {
              if ($widget['is_milestone']) {
                $labels .= '<span class="label label-'.(strtotime($widget['milestone_due']) < strtotime(date('Y-m-d'), strtotime('+3 days')) ? 'danger' : 'warning' ).' deadline">'.date('d/m/y', strtotime($widget['milestone_due'])).'</span>';
              } else {
                $labels .= '<span class="label label-'.(strtotime($widget['deadline']) < strtotime(date('Y-m-d'), strtotime('+3 days')) ? 'danger' : 'default' ).' deadline">'.date('d/m/y', strtotime($widget['deadline'])).'</span>';
              }
            }

            if ($widget['is_milestone']) {
              if (isset($widget['milesone_id']) || !empty($widget['milesone_id'])) {
                $labels .= '<span class="milestone-title">'.$widget['milestone_name'].'</span>';
              }
            }

            ?>

            <li id="proj-<?php echo $widget['project_id'];?>" data-project-id="<?php echo $widget['project_id'];?>" data-assigne="<?php echo $widget['assigned_to'];?>" data-row="<?php echo ($widget['data-row'] ? $widget['data-row'] : $i )?>" data-col="<?php echo ($widget['data-col'] ? $widget['data-col'] : 1 )?>" data-sizex="<?php echo ($widget['sizex'] ? $widget['sizex'] : 1 )?>" data-sizey="1" data-time="<?php echo $widget['total_hours'];?>">
              <a href="#" class="kanban-item" title="<?php echo $widget['title']?>">
                <?php echo $widget['unique_id'].' | '.$widget['title'];?>
              <div class="meta">
                <?php echo $labels; ?>
              </div>
              </a>
            </li>
          <?php $i++; } endif;  ?>
        </ul>
      </div>
    </div>
</div>

  <script type="text/javascript">
    $(document).ready(function () {

        var $userSelect  = $('#user_filter');
        var $teamSelect  = $('#team_filter');

        $userSelect.select2();
        $teamSelect.select2();

        var weeklygrid = $("#weekly-grid").gridster({
          namespace: '#weekly-grid',
          widget_margins: [10, 20],
          autogenerate_stylesheet: true,
          widget_base_dimensions: [220, 50],
          max_row: 1,
          min_cols: 1,
          max_cols: 7
        }).data('gridster');

        weeklygrid.disable();

        var projectgrid = $("#project-grid").gridster({
          namespace: '#project-grid',
          widget_margins: [10, 10],
          widget_base_dimensions: [220, 80],
          autogenerate_stylesheet: true,
          avoid_overlapped_widgets: true,
          extra_rows: 3,
          min_cols: 1,
          max_cols: 7,
          resize: {
            enabled: true,
            axes: ['x'],
            max_size: [5,1],
            stop: function(e, ui, $widget){
              var newState = $widget[0].dataset;

              //prevent from resizing on col 1 and 2
              if (newState.col == 1 || newState.col == 2) {
                var widgetId = $($widget[0]).attr('id');
                this.resize_widget($('#'+widgetId),1,1);

                newState.sizex = '1';
              }

              update_grid(newState);
            }
          },
          draggable: {
            stop: function(e, ui) {
              var newPosition = ui.$player[0].dataset;

              //revert size when dragging to col 1 and 2
              if(newPosition.col == 1 || newPosition.col == 2){
                var widgetId = $(ui.$helper[0]).attr('id');
                this.resize_widget($('#'+widgetId),1,1);

                newPosition.sizex = '1';
              }

              update_grid(newPosition);
            }
          }
        }).data('gridster');

        var userBar = {};

        <?php foreach ($users as $key => $user): ?>

        userBar['user_<?php echo $user['id']?>'] = new ProgressBar.Circle(user_<?php echo $user['id']; ?>, {
            color: '#28B513',
            trailColor: '#eee',
            trailWidth: 10,
            duration: 1000,
            easing: 'easeInOut',
            strokeWidth: 10,
            from: {color: '#28B513', a:0},
            to: {color: <?php echo ($user['time_allocated'] <= 1 ? '"#28B513"' : '"#28B513"')?>, a:1},
            // Set default step function for all animate calls
            step: function(state, circle) {
              circle.path.setAttribute('stroke', state.color);
            }
          });

          userBar['user_<?php echo $user['id']?>'].animate(<?php echo $user['time_allocated']?>);

        <?php endforeach; ?>

        function update_grid(gridState) {
          $.ajax({
              url: '<?php echo_uri("weekly/save_grid_status") ?>',
              type: "POST",
              data: {
                id: gridState.projectId,
                row: gridState.row,
                col: gridState.col,
                sizex: gridState.sizex,
                time: gridState.time,
                assignee: gridState.assigne
              },
              success: function (response) {
                appLoader.hide();
                var userTime = JSON.parse(response);
                $(userTime.data).each( function(){
                  console.log(this);
                  userBar[this.user_id].animate(this.time_allocated);
                });
              }
          });
        }

        $('#clear-kanban-button').on('click',  function() {
          var grid_id = <?php echo ($grid_id ? $grid_id : 0); ?>;
          $.ajax({
              url: '<?php echo_uri("weekly/delete_grid") ?>',
              type: "POST",
              data: {id: grid_id},
              success: function (response) {
                location.reload();
                appLoader.hide();
              }
          });
        });
    });

  </script>
