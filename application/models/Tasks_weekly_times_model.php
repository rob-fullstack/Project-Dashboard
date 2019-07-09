<?php

class Tasks_weekly_times_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'task_weekly_times';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
      $weekly_table = $this->db->dbprefix('task_weekly_times');
      $where = "";

      $id = get_array_value($options, "id");
      if ($id) {
          $where = " AND $weekly_table.id=$id";
      }

      $user_id = get_array_value($options, "user_id");
      if ($user_id) {
        $where .= " AND $weekly_table.user_id=$user_id";
      }

      $grid_id = get_array_value($options, "grid_id");
      if ($grid_id) {
        $where .= " AND $weekly_table.grid_id=$grid_id";
      }

      $project_id = get_array_value($options, "project_id");
      if ($project_id) {
        $where .= " AND $weekly_table.project_id=$project_id";
      }

      $task_id = get_array_value($options, "task_id");
      if ($task_id) {
        $where .= " AND $weekly_table.task_id=$task_id";
      }

      $has_started = get_array_value($options, "has_started");
      if ($has_started) {
        $where .= " AND $weekly_table.has_started=$has_started";
      }

      $sql = "SELECT $weekly_table.*
      FROM $weekly_table
      WHERE $weekly_table.deleted=0 $where";
      return $this->db->query($sql);
    }

}
