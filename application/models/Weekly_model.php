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

}
