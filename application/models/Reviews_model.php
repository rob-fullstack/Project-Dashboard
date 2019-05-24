<?php

class Reviews_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'project_reviews';
        parent::__construct($this->table);
    }

    function get_project_review($project_id) {
        $project_comments_table = $this->db->dbprefix('project_comments');
        $review_table = $this->db->dbprefix('project_reviews');

        $sql = 
        "SELECT $review_table.project_id AS project_commment_id, $review_table.comment_id AS review_id, $project_comments_table.review as review_content
        FROM $review_table
        LEFT JOIN $project_comments_table ON $project_comments_table.id=  $review_table.comment_id
        WHERE $review_table.project_id = $project_id AND $project_comments_table.deleted=0
        LIMIT 1";

        return $this->db->query($sql);
    }
}