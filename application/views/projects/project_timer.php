<?php

if ($this->session->timer_status === "open") {
    echo modal_anchor(get_uri("projects/stop_timer_modal_form/". $this->session->project_id),  "<i class='fa fa fa-clock-o'></i> " . lang('stop_timer'), array("class" => "btn btn-danger", "title" => lang('stop_timer')));
} else {
    echo modal_anchor(get_uri("projects/project_timer_modal/"), "<i class='fa fa fa-clock-o'></i> " . lang('start_timer'), array("class" => "btn btn-info", "title" => lang('start_timer')));
}
?>
