<div id="page-content" class="p20 clearfix">
    <?php
    announcements_alert_widget();
    ?>

    <?php client_retainer_widget(); ?>

    <div class="row">
        <?php $this->load->view("clients/info_widgets"); ?>
    </div>

    <?php if(!in_array("projects", $hidden_menu)) { ?>
    <div class="">
        <?php $this->load->view("clients/projects/index"); ?>
    </div>
    <?php } ?>
    
</div>

