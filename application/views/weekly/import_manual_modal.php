<form id="import_manual-form" class="general-form" role="form">
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
</form>
<script>
$(document).ready( function() {
  $('#project_id').select2({data: <?php echo json_encode($projects); ?>});
});
</script>
