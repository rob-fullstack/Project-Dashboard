<div class="panel">
    <?php
      if (in_array($this->login_user->role_id, array(0, 6, 9))) {
        $this->load->view("projects/comments/comment_form");
      }
        $this->load->view("projects/comments/comment_list"); 
        ?>
</div>
