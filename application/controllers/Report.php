<?php

class Report extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('pdf');
	}

	// public function display_data($id)
	// {
	// 	echo json_encode($this->_fetch_data($id), JSON_PRETTY_PRINT);
	// }

	private function _fetch_data($id)
	{
		$comprehensive_array = [];
		$project    = $this->Projects_model->get_one_where(['id' => $id, 'deleted' => 0]);
		$members    = $this->Project_members_model->get_all_where(['project_id' => $id, 'deleted' => 0])->result();
		$expenses   = $this->Expenses_model->get_all_where(['project_id' => $id, 'deleted' => 0])->result();
		$client     = $this->Clients_model->get_one_where(['id' => $project->client_id, 'deleted' => 0]);
		$timestamps = $this->Timesheets_model->get_all_where(['project_id'=> $id, 'deleted' => 0])->result();

		foreach ($members as $mem) {
			$user = $this->Users_model->get_one_where(['id' => $mem->user_id, 'deleted' => 0]);

			unset($mem->id);
			unset($mem->user_id);
			unset($mem->project_id);
			unset($mem->is_leader);
			unset($mem->deleted);

			$mem->name = $user->first_name . ' ' . $user->last_name;
			$mem->email = $user->email;
			$mem->job_title = $user->job_title;
			$mem->avatar = $user->image;
		}

		foreach($expenses as $exp) {
			$exp_cat = $this->Expense_categories_model->get_one_where(['id' => $exp->category_id, 'deleted' => 0]);
			$exp->category_name = $exp_cat->title;
		}

		foreach($timestamps as $t) {
			$user = $this->Users_model->get_one_where(['id' => $t->user_id, 'deleted' => 0]);
			$t->user_name = $user->first_name . ' ' . $user->last_name;
		}

		$cfv = $this->Custom_field_values_model->get_all_where(['related_to_type' => 'projects', 'related_to_id' => $project->id])->result();

		if (count($cfv) > 0) {
			foreach ($cfv as $custom_field) {
				$cfv_model = $this->Custom_fields_model->get_one_where(['id' => $custom_field->custom_field_id]);

				unset($custom_field->id);
				unset($custom_field->related_to_type);
				unset($custom_field->related_to_id);
				unset($custom_field->custom_field_id);
				unset($custom_field->deleted);

				$custom_field->field_title = $cfv_model->title;
			}
		}

		$comprehensive_array['project']       = $project;
		$comprehensive_array['members']       = $members;
		$comprehensive_array['client']        = $client;
		$comprehensive_array['timestamps']    = $timestamps;
		$comprehensive_array['custom_fields'] = $cfv;
		$comprehensive_array['expenses']      = $expenses;

		return $comprehensive_array;
	}

	private function _report_template()
	{
		$html = <<<EOD
		<style>
			* {
				color: #353535;
				font-family: "Helvetica", "Arial", sans-serif;
			}

			ul, p {
			  	font-size: 10px;
			}

			.text-left {
				text-align: left;
			}

			.text-right {
				text-align: right;
			}

			.font-bold {
				font-weight: bold;
			}

			.heading {
			  margin: 0;
			  color: #f2af3d;
			}

			hr {
				border-color: #eee;
			}

			ul li {
				list-style-type: none;
			}

			table {
				width: 100%;
				border-collapse: collapse;
				border-spacing: 0;
				font-size: 10px;
			}
	    </style>
      	<h3 class="heading">@heading@</h3>
      	<div class="content">@content@</div>
EOD;

		return $html;
	}

	public function generate_pdf($id)
	{
		$data = $this->_fetch_data($id);

		$project    = $data["project"];
		$members    = $data["members"];
		$client     = $data["client"];
		$timestamps = $data["timestamps"];
		$cfs        = $data["custom_fields"];
		$expenses   = $data["expenses"];

		$data = null;

		define("PDF_TITLE", $project->unique_project_id . ' - ' . $project->title);

		$logo = 't2ds-logo.png';
		$margin = 50;

		$pdf = new PDF();
		$pdf->setPageUnit("px");
		$pdf->setTitle("T2DS Project Report");
		$pdf->setHeaderData($logo, 60, "{$project->title}", "", array(0,0,0), array(255,255,255));
		$pdf->setFooterData(array(0,0,0), array(255,255,255));
		$pdf->setHeaderFont(Array('helvetica', 'B', 18));
		$pdf->setFooterFont(Array('helvetica', '', 10));
		$pdf->setMargins($margin, 100, $margin, true);
		$pdf->setHeaderMargin(30);
		$pdf->setFooterMargin(50);
		$pdf->setAutoPageBreak(true, 67);
		$pdf->addPage();
		$pdf->setListIndentWidth(10);

		// $pdf->WriteHTML(
		// 	"<div>
		// 		<h2 style=\"font-family: Helventica, Arial, sans-serif; color: #353535;\">{$project->title}</h2>
		// 	</div>",
		// 	true,
		// 	false,
		// 	true,
		// 	false,
		// 	''
		// );

		$overview_template = $this->_scaffold_overview($project, $cfs, $client->company_name, "Overview", $this->_report_template());
		$pdf->WriteHTML($overview_template, true, false, true, false, '');

		$project_members_template = $this->_scaffold_project_members($members, "Project Members", $this->_report_template());
		$pdf->WriteHTML($project_members_template, true, false, true, false, '');

		// $custom_field_template = $this->_scaffold_custom_fields($cfs, "Others", $this->_report_template());
		// $pdf->WriteHTML($custom_field_template, true, false, true, false, '');

		$expenses_template = $this->_scaffold_expenses($expenses, "Project Costs", $this->_report_template());
		$pdf->WriteHTML($expenses_template, true, false, true, false, '');

		$timesheet_template = $this->_scaffold_timesheet($timestamps, "Timesheet", $this->_report_template());
		$pdf->WriteHTML($timesheet_template, true, false, true, false, '');

		$pdf->Output("{$project->unique_project_id} {$project->title} report.pdf", "D");
	}

	private function _scaffold_overview($data, $cfs, $company, $heading, $template)
	{
		$project_start = date("F d, Y", strtotime($data->start_date));
		$project_end   = date("F d, Y", strtotime($data->deadline));
		$content = "<p>Description: {$data->description}</p>";
		$content .= "<p>Start Date: {$project_start}</p>";
		$content .= "<p>Deadline: {$project_end}</p>";
		$content .= "<p>Client: {$company}</p>";

		$content .= "<ul>";
		foreach ($cfs as $cf) {
			$content .= "<li>{$cf->field_title}: {$cf->value}</li>";
		}
		$content .= "</ul>";

		$template = str_replace("@content@", $content, $template);
		$template = str_replace("@heading@", $heading, $template);

		return $template;
	}

	private function _scaffold_project_members($data, $heading, $template)
	{
		$content = "<table cellpadding=\"6\" cellspacing=\"0\">";
		$content .= "<thead>
						<tr>
							<th class=\"font-bold\" style=\"background-color: #353535; color: #f5f5f5;\">Name</th>
							<th class=\"font-bold\" style=\"background-color: #353535; color: #f5f5f5;\">Email</th>
							<th class=\"font-bold\" style=\"background-color: #353535; color: #f5f5f5;\">Title</th>
						</tr>
					</thead>";
		$content .= "<tbody>";
		foreach ($data as $member) {
			$content .= "<tr>
							<td style=\"border-bottom: 1px solid #eee;\">{$member->name}</td>
							<td style=\"border-bottom: 1px solid #eee;\">{$member->email}</td>
							<td style=\"border-bottom: 1px solid #eee;\">{$member->job_title}</td>
						</tr>";
		}
		$content .= "</tbody>";
		$content .= "</table>";

		$template = str_replace("@content@", $content, $template);
		$template = str_replace("@heading@", $heading, $template);

		return $template;
	}

	private function _scaffold_custom_fields($data, $heading, $template)
	{
		$content = "<ul>";
		foreach ($data as $cf) {
			$content .= "<li>{$cf->field_title}: {$cf->value}</li>";
		}
		$content .= "</ul>";

		$template = str_replace("@content@", $content, $template);
		$template = str_replace("@heading@", $heading, $template);

		return $template;
	}

	private function _scaffold_expenses($data, $heading, $template)
	{
		$total_expenses = 0;

		$content = "<br><br><table cellpadding=\"6\" cellspacing=\"0\">";
		$content .= "<thead>
						<tr>
							<td class=\"font-bold\" width=\"75\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Date</td>
							<td class=\"font-bold\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Category</td>
							<td class=\"font-bold\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Title</td>
							<td class=\"font-bold\" width=\"150\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Description</td>
							<td class=\"font-bold text-right\" width=\"70\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Amount</td>
						</tr>
					</thead>";
		$content .= "<tbody>";
		foreach ($data as $exp) {
			$total_expenses += (int) $exp->amount;

			$content .= "<tr>
					   		<td width=\"75\" style=\"border-bottom: 1px solid #eee;\">{$exp->expense_date}</td>
					   		<td style=\"border-bottom: 1px solid #eee;\">{$exp->category_name}</td>
					   		<td style=\"border-bottom: 1px solid #eee;\">{$exp->title}</td>
					   		<td width=\"150\" style=\"border-bottom: 1px solid #eee;\">{$exp->description}</td>
					   		<td class=\"text-right\" width=\"70\" style=\"border-bottom: 1px solid #eee;\">&pound; {$exp->amount}</td>
					    </tr>";
		}
		$content .= "<tfoot>
						<tr>
							<td width=\"75\"></td>
							<td></td>
							<td></td>
							<td class=\"font-bold text-right\" width=\"150\">Total:</td>
							<td class=\"font-bold text-right\" width=\"70\">&pound; {$total_expenses}</td>
						</tr>
					</tfoot>";
		$content .= "</tbody>";
		$content .= "</table>";

		$template = str_replace("@content@", $content, $template);
		$template = str_replace("@heading@", $heading, $template);

		return $template;
	}

	private function _scaffold_timesheet($data, $heading, $template)
	{
		$total_hours   = 0;
        $total_minutes = 0;
        $total_seconds = 0;

		$content = "<br><br><table cellpadding=\"6\" cellspacing=\"0\">";
		$content .= "<thead>
						<tr>
							<th class=\"font-bold\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Member</th>
							<th class=\"font-bold\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">End time</th>
							<th class=\"font-bold\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Note</th>
							<th class=\"font-bold text-right\" style=\"border-bottom: 1px solid #eee; background-color: #353535; color: #f5f5f5;\">Total</th>
						</tr>
					</thead>";
		$content .= "<tbody>";
		foreach ($data as $stamp) {
			$end_date = new DateTime($stamp->end_time);
			$formatted_end_date = $end_date->format("d/m/Y h:i a");

			$start_time = strtotime($stamp->start_time);
	        $end_time   = strtotime($stamp->end_time);
	        $seconds    = $end_time - $start_time;
	        $days       = floor($seconds / 86400);
	        $hours      = floor(($seconds - ($days * 86400)) / 3600);
	        $minutes    = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
	        $seconds    = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

	        $days_to_hours = $days * 24;
	        $days_to_hours += $hours;

	        $total_time_single = sprintf('%02d:%02d:%02d', $days_to_hours, $minutes, $seconds);

            $total_hours   += (int) $days_to_hours;
            $total_minutes += (int) $minutes;
            $total_seconds += (int) $seconds;

            // Convert each 60 minutes to an hour
            if ($total_minutes >= 60) {
                $total_hours++;
                $total_minutes -= 60;
            }

            // Convert each 60 seconds to a minute
            if ($total_seconds >= 60) {
                $total_minutes++;
                $total_seconds -= 60;
            }

			$content .= "<tr>
							<td style=\"border-bottom: 1px solid #eee\">{$stamp->user_name}</td>
							<td style=\"border-bottom: 1px solid #eee\">{$formatted_end_date}</td>
							<td style=\"border-bottom: 1px solid #eee\">{$stamp->note}</td>
							<td class=\"text-right\" style=\"border-bottom: 1px solid #eee\">{$total_time_single}</td>
						</tr>";
		}

		$total_time = sprintf('%02d:%02d:%02d', $total_hours, $total_minutes, $total_seconds);

		$content .= "<tr>
						<td></td>
						<td></td>
						<td class=\"text-right font-bold\">Total</td>
						<td class=\"text-right font-bold\">{$total_time}</td>
					</tr>";
		$content .= "</tbody>";
		$content .= "</table>";

		$template = str_replace("@content@", $content, $template);
		$template = str_replace("@heading@", $heading, $template);

		return $template;
	}
}
