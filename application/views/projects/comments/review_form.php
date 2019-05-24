<?php
$comment_type = "";
if (isset($task_id)) {
    $comment_type = "task";
} else if (isset($file_id)) {
    $comment_type = "file";
} else if (isset($customer_feedback_id)) {
    $comment_type = "customer_feedback";
} else {
    $comment_type = "project";
}
?>

<div id="<?php echo $comment_type . "-comment-form-container"; ?>">
    <?php echo form_open(get_uri("projects/save_comment"), array("id" => $comment_type . "-comment-form", "class" => "general-form", "role" => "form")); ?>
    <div class="p15 box b-b">
        <div id="<?php echo $comment_type . "-dropzone"; ?>" class="post-dropzone box-content form-group">
            <input type="hidden" name="project_id" value="<?php echo isset($project_id) ? $project_id : 0; ?>">
            <input type="hidden" name="file_id" value="<?php echo isset($file_id) ? $file_id : 0; ?>">
            <input type="hidden" name="task_id" value="<?php echo isset($task_id) ? $task_id : 0; ?>">
            <input type="hidden" name="customer_feedback_id" value="<?php echo isset($customer_feedback_id) ? $customer_feedback_id : 0; ?>">
            <input type="hidden" name="reload_list" value="1">
            <input type="hidden" name="id" value="<?php echo $comments[0]->id;?>">
            <input type="hidden" name="description" value="<i>Wrote a project review</i>">
            <input type="hidden" name="is_review" value="1">

            <div class="form-group">
            <label for="review_1"><h4><?php echo lang('review_1')?></h4></label>
            <?php 
            $review_content = unserialize($review->review_content);

                echo form_textarea(array(
                    "id" => "review_1",
                    "name" => "review_1",
                    "class" => "form-control comment_description",
                    "placeholder" => lang('write_a_comment'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "value" => $review_content['review_1']
                ));

            ?>
            </div>

            <div class="form-group">
            <label for="review_2"><h4><?php echo lang('review_2')?></h4></label>
            <?php 
                echo form_textarea(array(
                    "id" => "review_2",
                    "name" => "review_2",
                    "class" => "form-control comment_description",
                    "placeholder" => lang('write_a_comment'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "value" => $review_content['review_2']
                ));
            ?>
            </div>

            <div class="form-group">
            <label for="review_3"><h4><?php echo lang('review_3')?></h4></label>
            <?php 
                echo form_textarea(array(
                    "id" => "review_3",
                    "name" => "review_3",
                    "class" => "form-control comment_description",
                    "placeholder" => lang('write_a_comment'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "value" => $review_content['review_3']
                ));
            ?>
            </div>

            <div class="form-group">
            <label for="review_4"><h4><?php echo lang('review_4')?></h4></label>
            <?php 
                echo form_textarea(array(
                    "id" => "review_4",
                    "name" => "review_4",
                    "class" => "form-control comment_description",
                    "placeholder" => lang('write_a_comment'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "value" => $review_content['review_4']
                ));
            ?>
            </div>

            <div class="form-group">
            <label for="review_5"><h4><?php echo lang('review_5')?></h4></label>
            <?php 
                echo form_textarea(array(
                    "id" => "review_5",
                    "name" => "review_5",
                    "class" => "form-control comment_description",
                    "placeholder" => lang('write_a_comment'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "value" => $review_content['review_5']
                ));
            ?>
            </div>

            <div class="form-group">
            <label for="review_6"><h4><?php echo lang('review_6')?></h4></label>
            <?php 
                echo form_textarea(array(
                    "id" => "review_6",
                    "name" => "review_6",
                    "class" => "form-control comment_description",
                    "placeholder" => lang('write_a_comment'),
                    "data-rule-required" => true,
                    "data-msg-required" => lang("field_required"),
                    "value" => $review_content['review_6']
                ));
            ?>
            </div>


            <?php $this->load->view("includes/dropzone_preview"); ?>
            <footer class="panel-footer b-a clearfix">
                <?php if ($comment_type != "file") { ?>
                    <button class="btn btn-default upload-file-button pull-left btn-sm round" type="button" style="color:#7988a2"><i class='fa fa-camera'></i> <?php echo lang("upload_file"); ?></button>
                <?php } ?>
                <button class="btn btn-primary pull-right btn-sm" type="submit"><i class='fa fa-paper-plane'></i> <?php echo lang("post_review"); ?></button>
            </footer>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#comment_description').appMention({
            source: "<?php echo_uri("projects/get_member_suggestion_to_mention"); ?>",
            data: {project_id: <?php echo $project_id; ?>}
        });

        var dropzone;
<?php if ($comment_type != "file") { ?>
            var uploadUrl = "<?php echo get_uri("projects/upload_file"); ?>";
            var validationUrl = "<?php echo get_uri("projects/validate_project_file"); ?>";
            dropzone = attachDropzoneWithForm("#<?php echo $comment_type . "-dropzone"; ?>", uploadUrl, validationUrl);
<?php } ?>

        $("#<?php echo $comment_type; ?>-comment-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                $(result.data).insertAfter("#<?php echo $comment_type; ?>-comment-form-container");
                appAlert.success(result.message, {duration: 5000});

                var reviews_url = '<?php echo_uri("projects/project_review/"  . $project_id); ?>';

                $.get(reviews_url, function(data) {
                    $('#project-customer-feedback-section').html(data);
                });
            }   
        });

        $(document).on('click', '#comment-edit', 'data-id',  function(e){
            e.preventDefault();

            var commentId       = $(this).data('id');
            var commentContent  = $('#prject-comment-container-task-'+commentId).find('.comment-content').text();
            console.log(commentContent);
            
            $('#comment_description').val(commentContent);
            $('.general-form [name="id"]').val(commentId);
            
            $('#prject-comment-container-task-'+commentId).hide();
        });
    });
</script>