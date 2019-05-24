<div class="panel">
    <div class="tab-title clearfix">
        <h4><?php echo lang('expenses'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("expenses/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_expense'), array("class" => "btn btn-default mb0", "title" => lang('add_expense'), "data-post-user_id" => $user_id)); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="expense-table" class="display" cellspacing="0" width="100%">
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $EXPENSE_TABLE = $("#expense-table");

        $EXPENSE_TABLE.appTable({
            source: '<?php echo_uri("expenses/list_data/") ?>',
            filterParams: {user_id: "<?php echo $user_id; ?>"},
            order: [[0, "asc"]],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo lang("date") ?>', "iDataSort": 0},
                {title: '<?php echo lang("category") ?>'},
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("description") ?>'},
                {title: '<?php echo lang("file") ?>'},
                {title: '<?php echo lang("amount") ?>', "class": "text-right"},
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            printColumns: [1, 2, 3, 4, 6],
            xlsColumns: [1, 2, 3, 4, 6],
            summation: [{column: 6, dataType: 'currency'}]
        });
    });
</script>