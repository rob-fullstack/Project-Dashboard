<div class="panel">
    <?php
      if (in_array($this->login_user->role_id, array(0, 6, 9)) && $_GET['act'] == 'edit') {
        $this->load->view("projects/comments/review_form");
        //$this->load->view("projects/comments/comment_list"); 
      }else{
        $this->load->view("projects/comments/review_view"); 
      }
     ?>
</div>
