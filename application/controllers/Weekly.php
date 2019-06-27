<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Weekly extends MY_Controller {

    private $is_user_a_project_member = false;
    private $is_clients_project = false; //check if loged in user's client's project

    public function __construct() {
        parent::__construct();
        $this->load->helper("url");
        $this->load->model("Project_settings_model");
        $this->load->model("Projects_model");
        $this->load->model("Tasks_model");
        $this->load->model("Milestones_model");
        $this->load->model("Custom_fields_model");
        $this->load->model("Custom_field_values_model");
        $this->load->model("Weekly_model");
    }

    private function can_manage_all_projects() {
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_all_projects") == "1") {
            return true;
        }
    }

    //When checking project permissions, to reduce db query we'll use this init function, where team members has to be access on the project
    private function init_project_permission_checker($project_id = 0) {
        if ($this->login_user->user_type == "client") {
            $project_info = $this->Projects_model->get_one($project_id);
            if ($project_info->client_id == $this->login_user->client_id) {
                $this->is_clients_project = true;
            }
        } else {
            $this->is_user_a_project_member = $this->Project_members_model->is_user_a_project_member($project_id, $this->login_user->id);
        }
    }

    private function can_edit_projects() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_projects") == "1") {
                return true;
            }
        }
    }

    private function can_delete_projects() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_projects") == "1") {
                return true;
            }
        }
    }

    private function can_add_remove_project_members() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if ($this->login_user->is_admin) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_add_remove_project_members") == "1") {
                return true;
            }
        }
    }

    private function can_view_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if ($this->is_user_a_project_member) {
                //all team members who has access to project can view tasks
                return true;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_tasks")) {
                //even the settings allow to create/edit task, the client can only create their own project's tasks
                return $this->is_clients_project;
            }
        }
    }

    private function can_create_tasks($in_project = true) {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_create_tasks") == "1") {
                //check is user a project member
                if($in_project){
                     return $this->is_user_a_project_member; //check the specific project permission
                }else{
                   return true;
                }

            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_create_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_edit_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_edit_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_delete_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_delete_tasks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_comment_on_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_comment_on_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_comment_on_tasks")) {
                //even the settings allow to create/edit task, the client can only create their own project's tasks
                return $this->is_clients_project;
            }
        }
    }

    private function can_view_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_milestones")) {
                //even the settings allow to view milestones, the client can only create their own project's milestones
                return $this->is_clients_project;
            }
        }
    }

    private function can_create_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_create_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_edit_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_delete_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_milestones") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_delete_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_files") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    private function can_view_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_project_files")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_add_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_add_project_files")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_comment_on_files() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_comment_on_files")) {
                //even the settings allow to create/edit task, the client can only comment on their own project's files
                return $this->is_clients_project;
            }
        }
    }

    private function can_view_gantt() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_gantt")) {
                //even the settings allow to view gantt, the client can only view on their own project's gantt
                return $this->is_clients_project;
            }
        }
    }

    /* load the project settings into ci settings */

    private function init_project_settings($project_id) {
        $settings = $this->Project_settings_model->get_all_where(array("project_id" => $project_id))->result();
        foreach ($settings as $setting) {
            $this->config->set_item($setting->setting_name, $setting->setting_value);
        }
    }

    private function can_view_timesheet($project_id = 0) {
        if (!get_setting("module_project_timesheet")) {
            return false;
        }

        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {


                if ($project_id) {
                    //check is user a project member
                    return $this->is_user_a_project_member;
                } else {
                    $access_info = $this->get_access_info("timesheet_manage_permission");

                    if ($access_info->access_type == "all") {
                        return true;
                    } else if (count($access_info->allowed_members)) {
                        return true;
                    }
                }
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_timesheet")) {
                //even the settings allow to view gantt, the client can only view on their own project's gantt
                return $this->is_clients_project;
            }
        }
    }

    /* load weekly project view */

    function index() {
      $teams = [];
      $groups = $this->Team_model->get_all()->result();

      $index = 0;
      if (count($groups) > 0) {
          foreach($groups as $group) {
              $teams[$index]['title'] = $group->title;
              $member_ids = explode(',', $group->members);
              foreach ($member_ids as $member_id) {
                  $teams[$index]['members'][] = $this->Users_model->get_one($member_id);
              }
              $index++;
          }
      }

      $users = $this->Users_model->get_all_where(array('user_type' => 'staff', 'deleted' => 0))->result();

      $team_members = array();

      foreach ($users as $key => $user) {
        $team_members[] = array(
          'id' => $user->id,
          'full_name' => $user->first_name.' '.$user->last_name,
          'image' => $user->image,
          'hours' => $this->_calculate_hours($user->id)
        );

      }

      $view_data['can_edit'] = $this->is_superadmin();
      $view_data['users'] = $team_members;
      $view_data['teams'] = $teams;
      $view_data['can_filter'] = $this->is_superadmin();
      $view_data['grid_data'] = unserialize($this->Weekly_model->get_one_where(array('user_id'=>$this->login_user->id))->grid_projects);
      $this->template->rander('weekly/index',$view_data);
    }

    function import_weekly_project() {
      $g_id = $this->input->post('grid_id');
      $g_act = $this->input->post('act');

      if (!$this->can_edit_projects()) {
        redirect("forbidden");
      }

      $range = date('Y-m-d', strtotime('+2 weeks'));

      $options = array(
        'range' => $range,
        'deleted' => 0,
        'status' => 'open'
      );

      $projects = $this->Projects_model->get_details($options)->result();

      $import_projects = array();

      foreach ($projects as $key => $project) {
        $import_projects[] = array(
            'project_id' => $project->id,
            'unique_id' => $project->unique_project_id,
            'title' => $project->title,
            'description' => $project->description,
            'company_name' => $project->company_name,
            'deadline' => $project->deadline,
            'data-col' => '',
            'data-row' => '',
            'sizex' => "1",
            'tasks' => $this->Tasks_model->get_details(array('project_id'=>$project->id))->result_array(),
            'milestones' => $this->Milestones_model->get_details(array('project_id'=>$project->id))->result()
         );
      }

      $grid_data = $this->Weekly_model->get_one_where(array('user_id'=>$this->login_user->id));

      $data['user_id'] = $this->login_user->id;
      $data['grid_projects'] = serialize($import_projects);

      if($g_act === 'edit') {
        $new_grid_id = $this->Weekly_model->update_where($data, array('id'=>$g_id));
      }else{
        $new_grid_id = $this->Weekly_model->save($data);
      }

      if ($new_grid_id) {
          echo json_encode(array("success" => true, 'id' => $new_grid_id, 'message' => lang('import_success')));
      } else {
          echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
      }

    }

    function import() {

      $grid_data = $this->Weekly_model->get_details(array('user_id'=>$this->login_user->id))->row();

      if($grid_data->id) {
        $view_data['act'] = 'edit';
        $view_data['grid_id'] = $grid_data->id;
      }else{
        $view_data['act'] = 'new';
        $view_data['grid_id'] = '';
      }

      $this->load->view('weekly/import_weekly_modal', $view_data);
    }

    function import_manual() {
      $view_data['projects'] =$this-> _get_all_projects_dropdown_list();
      $this->load->view('weekly/import_manual_modal', $view_data);
    }

    function save_grid_status() {
      $grid_id    = $this->input->post('id');
      $grid_row   = $this->input->post('row');
      $grid_col   = $this->input->post('col');
      $grid_size  = $this->input->post('sizex');
      $grid_data  = $this->Weekly_model->get_details(array('user_id'=>$this->login_user->id))->row();

      $data = unserialize($grid_data->grid_projects);

      $modified_grid = array();

      foreach ($data as $key => $grid) {
        if ($grid_id === $grid['project_id']) {
          $modified_grid[$key] = array(
            'project_id'    => $grid['project_id'],
            'unique_id'     => $grid['unique_id'],
            'title'         => $grid['title'],
            'description'   => $grid['description'],
            'company_name'  => $grid['company_name'],
            'deadline'      => $grid['deadline'],
            'data-col'      => $grid_col,
            'data-row'      => $grid_row,
            'sizex'         => $grid_size,
            'tasks'         => $grid['tasks'],
            'milestones'    => $grid['milestones']
          );
        }else{
          $modified_grid[$key] = $grid;
        }
      }

      $update_data = array('grid_projects'=>serialize($modified_grid));

      $grid_update_id = $this->Weekly_model->update_where($update_data, array('id'=>$grid_data->id));

      echo json_encode($modified_grid);

    }

    /* get all projects list */

    private function _get_all_projects_dropdown_list() {
        $projects = $this->Projects_model->get_dropdown_list(array("title"));

        $projects_dropdown = array(array("id" => "", "text" => "- " . lang("project") . " -"));
        foreach ($projects as $id => $title) {
            $projects_dropdown[] = array("id" => $id, "text" => $title);
        }
        return $projects_dropdown;
    }

    private function _calculate_hours($user) {
      $range = date('Y-m-d', strtotime('+1 weeks'));

      $options = array(
        'range' => $range,
        'assigned_to' => $user
      );

      $assigned_tasks = $this->Tasks_model->get_details($options)->result();

      $task_hours = array();
      foreach ($assigned_tasks as $key => $task) {
        $options2 = array(
          'related_to_type' => 'tasks',
          'related_to_id' => $task->id,
          'custom_field_id' =>  4
        );

        $task_hours = $this->Custom_field_values_model->get_details($options2)->result();
      }

      $allocated_hours = array();
      foreach ($task_hours as $key => $hour) {
        $allocated_hours[] = $task_hours[$key]->value;
      }

      $total_hours = array_sum($allocated_hours);

      $user_time = $total_hours / 40;

      if($user_time >= 1){
        return 1;
      }else{
        return $user_time;
      }
    }

}
