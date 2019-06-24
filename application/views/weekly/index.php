<div id="page-content" class="p20 clearfix weekly">
    <div class="panel panel-default">
        <div class="page-title clearfix">
          <h4><?php echo lang('project_weekly'); ?></h4>
            <div class="title-button-group">
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
            </div>
        </div>
        <div class="p15 bg-white pb0">
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-default" id="reload-kanban-button"><i class="fa fa-refresh"></i></button>
                    <a href="#" class="btn btn-default" title="Import Projects" data-act="ajax-modal" data-title="Import Projects" data-action-url="404"><i class="fa fa-plus-circle"></i> Import Projects</a>
                    <a href="#" class="btn btn-default" title="Import Projects Manually" data-act="ajax-modal" data-title="Import Projects Manually" data-action-url="404"><i class="fa fa-plus-circle"></i> Import Projects Manually</a>

                <?php foreach ($users as $key => $user) { ?>
                  <span id="user_<?php echo $user->id; ?>" class="avatar-sm avatar pull-right mt-5 mr10 weekly-avatar">
                    <img alt="<?php echo $user->first_name.' '.$user->last_name; ?>" src="<?php echo ($user->image ? base_url().'files/profile_images/'.$user->image : base_url().'assets/images/avatar.jpg'); ?>">
                  </span>
                <?php }?>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div id="weekly-kanban"></div>
        </div>
    </div>
</div>

  <script type="text/javascript">
    $(document).ready(function () {

        var $userSelect  = $('#user_filter');
        var $teamSelect  = $('#team_filter');

        $userSelect.select2();
        $teamSelect.select2();

        <?php foreach ($users as $key => $user): ?>

        var bar_<?php echo $user->id; ?> = new ProgressBar.Circle(user_<?php echo $user->id; ?>, {
          color: '#28B513',
          trailColor: '#eee',
          trailWidth: 8,
          duration: 2800,
          easing: 'easeInOut',
          strokeWidth: 8,
          from: {color: '#28B513', a:0},
          to: {color: '#28B513', a:1},
          // Set default step function for all animate calls
          step: function(state, circle) {
            circle.path.setAttribute('stroke', state.color);
          }
        });

        bar_<?php echo $user->id; ?>.animate(0.75);

        <?php endforeach; ?>
    });

  </script>
