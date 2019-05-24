<div class="panel panel-default">
    <div class="table-responsive">
        <table id="client-groups-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#client-groups-table").appTable({
            source: '<?php echo_uri("client_groups/list_data") ?>',
            columns: [
                {title: '<?php echo lang("title") ?>'},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>