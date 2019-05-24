<form id="project-form" class="general-form" role="form"> 
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
            <label for="task_id" class="col-md-3"><?php echo lang('task'); ?></label>
            <div class="col-md-9">
                <?php
                echo form_input(array(
                    "id" => "task_id",
                    "name" => "task_id",
                    "class" => "form-control",
                    "placeholder" => lang('task')
                ));
                ?>
            </div>
        </div>
    </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
    <button type="submit" class="btn btn-danger"><span class="fa fa-check-circle"></span> <?php echo lang('start_timer'); ?></button>
</div>
</form>

<script type="text/javascript">
    $(document).ready( function(){

        //init dropdown
        $('#project_id').select2({data: <?php echo json_encode($projects); ?>})
        .on('change',function(){
            var project_id = $(this).val();
            console.log(project_id);

            $.ajax({
                url: "<?php echo get_uri("projects/get_project_tasks") ?>" + "/" + project_id,
                dataType: "json",
                success: function (result) {
                    $('#task_id').select2({data: result});
                }
            });
        });

        $('#project-form').submit( function(e){
            e.preventDefault();

            var projectId = $('#project_id').val();
            var taskId    = $('#task_id').val();
            var taskNote  = $('#task_note').val();

            $.post("<?php echo get_uri('projects/timer/'); ?>"+projectId,{
                task_id: taskId,
                },
                function(response){
                    location.reload();
                }
            );
        });
    });
</script>
