<?php

class Weekly_times_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'project_weekly_times';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
      $weekly_time_table = $this->db->dbprefix('project_weekly_times');
      $where = "";

      $id = get_array_value($options, "id");
      if ($id) {
          $where = " AND $weekly_time_table.id=$id";
      }

      $user_id = get_array_value($options, "user_id");
      if ($user_id) {
        $where .= " AND $weekly_time_table.user_id=$user_id";
      }

      $project_id = get_array_value($options, "project_id");
      if ($project_id) {
        $where .= " AND $weekly_time_table.project_id=$project_id";
      }

      $grid_id = get_array_value($options, "grid_id");
      if ($grid_id) {
        $where .= " AND $weekly_time_table.grid_id=$grid_id";
      }

      $has_started = get_array_value($options, "has_started");
      if ($has_started) {
        $where .= " AND $weekly_time_table.has_started=$has_started";
      }

      $sql = "SELECT $weekly_time_table.*
      FROM $weekly_time_table
      WHERE $weekly_time_table.deleted=0 $where";
      return $this->db->query($sql);
    }

}
