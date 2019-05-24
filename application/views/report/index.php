<div id="page-content" class="p20 clearfix">

	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="text-bold mb0 pb10"><?php echo $project->title ?></h3>
				</div>
				<div class="panel-body">
					<p><?php echo $project->description ?></p>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-6">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">Overview</div>
						<div class="panel-body">
							<p>Start Time: <?php echo $project->start_date; ?></p>
							<p>End Time: <?php echo $project->deadline; ?></p>
							<p>Client: <?php echo $client->company_name; ?></p>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="panel">
						<div class="pnel-body no-padding">
							<?php foreach ($custom_fields as $field) { ?>
								<div class="p10"><i class="fa fa-cube"></i> <?php echo $field->field_title ?></div>
								<div class="p10 pt0 b-b ml15"><?php echo $field->value ?></div>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">Project Members</div>
						<div class="pnel-body">
							<table class="table dataTable b-b-only">
								<?php foreach ($members as $member) { ?>
									<tr>
										<td><span class="avatar avatar-xs mr10"><img src="<?php echo $member->avatar ? '/files/profile_images//' . $member->avatar : '/assets/images/avatar.jpg' ?>" alt="User Image"></span> <?php echo $member->name ?></td>
									</tr>
								<?php } ?>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
