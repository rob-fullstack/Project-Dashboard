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

$comment_id = $comments[0]->id;
?>

<div class="panel panel-default">
    <ul data-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("project_review"); ?></h4> </li>
        <li><a id="review-details-button" role="presentation" href="#" data-target="#review-details"><?php echo lang("details"); ?></a></li>
        <?php if(in_array($this->login_user->role_id, array(0, 6, 9))) {?>
            <li><a role="presentation" href="<?php echo_uri("projects/project_review/" . $project_id); ?>/?act=edit" data-target="#review-edit"><?php echo lang('edit'); ?></a></li>
        <?php } ?>
        <?php if(!empty($review)) {?>
        <div class="tab-title clearfix no-border">
            <div class="title-button-group"><?php echo ajax_anchor(get_uri("projects/delete_comment/$comment_id"), "<i class='fa fa-trash'></i> " . lang('delete'), array("class" => "btn btn-default", "title" => lang('delete'), "data-fade-out-on-success" => "#prject-comment-container-$type-$comment->id")); ?></div>
        </div>
        <?php } ?>
    </ul>
     
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="review-details"> 
            <div class="p15 box b-b">
            <?php if(!empty($review)) {?>
            <div class="box-content avatar  <?php echo isset($project_id) || isset($customer_feedback_id) ? " avatar-md" : " avatar-sm"; ?>  pr15">
                <img src="<?php echo get_avatar($this->login_user->image); ?>" alt="..." />
            </div>
            <?php $review_content = unserialize($review->review_content); ?>

                <div class="form-group">
                    <h4><?php echo lang('review_1')?></h4>
                    <blockquote><p><?php echo $review_content['review_1'];?></p></blockquote>
                </div>

                <div class="form-group">
                    <h4><?php echo lang('review_2')?></h4>
                    <blockquote><p><?php echo $review_content['review_2'];?></p></blockquote>
                </div>

                <div class="form-group">
                    <h4><?php echo lang('review_3')?></h4>
                    <blockquote><p><?php echo $review_content['review_3'];?></p></blockquote>
                </div>

                <div class="form-group">
                    <h4><?php echo lang('review_4')?></h4>
                    <blockquote><p><?php echo $review_content['review_4'];?></p></blockquote>
                </div>

                <div class="form-group">
                    <h4><?php echo lang('review_5')?></h4>
                    <blockquote><p><?php echo $review_content['review_5'];?></p></blockquote>
                </div>

                <div class="form-group">
                    <h4><?php echo lang('review_6')?></h4>
                    <blockquote><p><?php echo $review_content['review_6'];?></p></blockquote>
                </div>

                <?php
                $files = unserialize($comments[0]->files);
                $total_files = count($files);
                ?>
            <?php if($total_files >= 1){?>  
            <h4><?php echo lang('review_attachment');?>
            <blockquote>
                <div class="comment-image-box clearfix">
                    <?php
                        $files = unserialize($comments[0]->files);
                        $total_files = count($files);
                        $this->load->view("includes/timeline_preview", array("files" => $files));
                    ?>

                    <div class="mb15 clearfix">
                        <?php
                            if ($total_files) {
                                $download_caption = lang('download');
                            if ($total_files > 1) {
                                $download_caption = sprintf(lang('download_files'), $total_files);
                            }
                            if (!$can_reply) {
                                echo "<i class='fa fa-paperclip pull-left font-16'></i>";
                            }

                            echo anchor(get_uri("projects/download_comment_files/" . $comments[0]->id), $download_caption, array("class" => "pull-right", "title" => $download_caption));
                            }   ?>
                    </div>
                </div>
            </blockquote>
            <?php }?>         
            <?php } else { ?>
                <h4><?php echo lang('no_review');?></h4>
            <?php } ?>    
        </div>
    </div>
        <div role="tabpanel" class="tab-pane fade" id="review-edit"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready( function(){
        $('#review-details-button').trigger('click');
    });
</script>