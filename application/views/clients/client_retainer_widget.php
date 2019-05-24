<div class="panel">
	<div class="page-title panel-orange clearfix">
		<h1>Retainer Tracker</h1>
	</div>
	<div class="table-responsive">
		<table id="retainer-table" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Retainer Month</th>
					<th>Monthly Hours</th>
					<th>Time Spent</th>
					<th>Time Remaining</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($retainers as $key => $ret) { ?>
					<tr>
						<td class="text-capitalize"><?php echo  $key ?></td>
						<td><?php echo $retainer_hours ?> Hours</td>
						<td><?php echo $ret['time_spent'] ?></td>
						<td class="<?php echo $ret['status'] === 'under' ? 'text-success' : 'text-danger' ?>"><strong><?php echo $ret['time_remaining'] ?></strong></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
