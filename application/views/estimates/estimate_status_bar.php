<div class="panel panel-default  p15 no-border m0">
    <span><?php echo lang("status") . ": " . $estimate_status_label; ?></span>
    <span class="ml15"><?php
        echo lang("client") . ": ";
        echo (anchor(get_uri("clients/view/" . $estimate_info->client_id), $estimate_info->company_name));
        ?>
    </span>
</div>