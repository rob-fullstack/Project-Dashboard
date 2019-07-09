<?php

class Weekly_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'project_weekly';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
      $weekly_table = $this->db->dbprefix('project_weekly');
      $where = "";

      $id = get_array_value($options, "id");
      if ($id) {
          $where = " AND $weekly_table.id=$id";
      }

      $user_id = get_array_value($options, "user_id");
      if ($user_id) {
        $where .= " AND $weekly_table.user_id=$user_id";
      }

      $sql = "SELECT $weekly_table.*
      FROM $weekly_table
      WHERE $weekly_table.deleted=0 $where";
      return $this->db->query($sql);
    }

    function delete_entry($options = array()) {
      $weekly_table = $this->db->dbprefix('project_weekly');
      $task_weekly_table = $this->db->dbprefix('task_weekly');
      $weekly_time_table = $this->db->dbprefix('project_weekly_times');

      $grid_id = get_array_value($options, 'grid_id');

      $delete_weekly_sql = "UPDATE $weekly_table SET $weekly_table.deleted=1 WHERE $weekly_table.id=$grid_id; ";
      $this->db->query($delete_weekly_sql);

      $delete_weekly_time_sql = "UPDATE $weekly_time_table SET $weekly_time_table.deleted=1 WHERE $weekly_time_table.grid_id=$grid_id; ";
      $this->db->query($delete_weekly_time_sql);

      $delete_task_weekly_sql = "UPDATE $task_weekly_table SET $task_weekly_table.deleted=1 WHERE $task_weekly_table.project_grid_id=$grid_id; ";
      $this->db->query($delete_task_weekly_sql);

      return true;
    }

}
