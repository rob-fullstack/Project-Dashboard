<?php echo form_open(get_uri("projects/timer/" . $project_id . "/stop"), array("id" => "stop-timer-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="form-group">
        <label for="note" class=" col-md-12"><?php echo lang('note'); ?></label>
        <div class=" col-md-12">
            <?php
            echo form_textarea(array(
                "id" => "note",
                "name" => "note",
                "class" => "form-control",
                "placeholder" => lang('note'),
                "required" => "required"
            ));
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="task" class="col-md-12"><?php echo lang('task'); ?>        </label>
        <div class="col-md-12">
            <?php
            echo form_dropdown("task_id", $tasks_dropdown, $this->session->task_id, "class='select2' required");
            ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span class="fa fa-check-circle"></span> <?php echo lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#stop-timer-form").appForm({
            onSuccess: function (result) {
                setTimeout(function(){
                    location.reload();
                },1000);
            }
        });

        $("#stop-timer-form .select2").select2();
        $("#note").focus();
    });
</script>
