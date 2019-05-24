<div id="page-content" class="clearfix p20">
    <div class="panel clearfix">
        <ul data-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo lang("expenses"); ?></h4></li>
            <li><a id="monthly-expenses-button"  role="presentation" class="active" href="javascript:;" data-target="#monthly-expenses"><?php echo lang("monthly"); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("expenses/yearly/"); ?>" data-target="#yearly-expenses"><?php echo lang('yearly'); ?></a></li>
            <li><a role="presentation" href="<?php echo_uri("expenses/yearly_chart/"); ?>" data-target="#yearly-chart"><?php echo lang('chart'); ?></a></li>
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php echo modal_anchor(get_uri("expenses/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_expense'), array("class" => "btn btn-default mb0", "title" => lang('add_expense'))); ?>
                </div>
            </div>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="monthly-expenses">
                <div class="table-responsive">
                    <table id="monthly-expense-table" class="display" cellspacing="0" width="100%">
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-expenses"></div>
            <div role="tabpanel" class="tab-pane fade" id="yearly-chart"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    loadExpensesTable = function (selector, dateRange) {
        $(selector).appTable({
            source: '<?php echo_uri("expenses/list_data") ?>',
            dateRangeType: dateRange,
            filterDropdown: [
                {name: "category_id", class: "w200", options: <?php echo $categories_dropdown; ?>},
                {name: "user_id", class: "w200", options: <?php echo $members_dropdown; ?>},
                {name: "project_id", class: "w200", options: <?php echo $projects_dropdown; ?>}
            ],
            order: [[0, "asc"]],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo lang("date") ?>', "iDataSort": 0},
                {title: '<?php echo lang("category") ?>'},
                {title: '<?php echo lang("title") ?>'},
                {title: '<?php echo lang("description") ?>'},
                {title: '<?php echo lang("files") ?>'},
                {title: '<?php echo lang("amount") ?>', "class": "text-right"}
                <?php echo $custom_field_headers; ?>,
                {title: '<i class="fa fa-bars"></i>', "class": "text-center option w100"}
            ],
            printColumns: [1, 2, 3, 4, 6],
            xlsColumns: [1, 2, 3, 4, 6],
            summation: [{column: 6, dataType: 'currency'}]
        });
    };

    $(document).ready(function () {
        $("#monthly-expenses-button").trigger("click");
        loadExpensesTable("#monthly-expense-table", "monthly");
    });
</script>
