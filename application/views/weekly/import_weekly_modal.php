<?php echo form_open(get_uri("weekly/import_weekly_project"), array("id" => "project-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
  <p>This will automatically import projects that is not due with a span of two weeks and will refresh the board, click "Import" to proceed.<p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('import'); ?></button>
</div>
<input type="hidden" name="act" value="<?php echo $act;?>">
<input type="hidden" name="grid_id" value="<?php echo $grid_id;?>">
<?php echo form_close(); ?>
