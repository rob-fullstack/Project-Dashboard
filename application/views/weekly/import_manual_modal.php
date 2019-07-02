<?php echo form_open(get_uri("weekly/import_project_milestone"), array("id" => "import-manual", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="clearfix">
        <div class="form-group">
            <label for="project_id" class=" col-md-3"><?php echo lang('project'); ?></label>
            <div class="col-md-9" id="dropdown-apploader-section">
                <?php
                echo form_input(array(
                    "id" => "project_id",
                    "name" => "project_id",
                    "class" => "form-control",
                    "placeholder" => lang('project')
                ));
                ?>
            </div>
        </div>
        <div class="form-group">
            <label for="project_id" class=" col-md-3"><?php echo lang('milestone'); ?></label>
            <div class="col-md-9" id="dropdown-apploader-section">
                <?php
                echo form_input(array(
                    "id" => "milestone_id",
                    "name" => "milestone_id",
                    "class" => "form-control",
                    "placeholder" => lang('milestone')
                ));
                ?>
            </div>
        </div>

    </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('import'); ?></button>
</div>
<?php echo form_close();?>
<script>
$(document).ready( function() {
  $('#project_id').select2({data: <?php echo json_encode($projects); ?>})
  .on('change',function(){
      var project_id = $(this).val();

      $.ajax({
          url: "<?php echo get_uri("weekly/get_project_milestones"); ?>" + "/" + project_id,
          dataType: "json",
          success: function (result) {
            $('#milestone_id').select2({data: result});
          }
      });
  });

  $("#import-manual").appForm({
    onSuccess: function (result) {
        if (result.success) {
          setTimeout( function(){
            location.reload()
          },1000);
        } else {
          console.log(result);
        }
    }
  });
});
</script>
