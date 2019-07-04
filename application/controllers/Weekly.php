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
        $this->load->model("Project_members_model");
        $this->load->model("Tasks_model");
        $this->load->model("Milestones_model");
        $this->load->model("Custom_fields_model");
        $this->load->model("Custom_field_values_model");
        $this->load->model("Weekly_model");
        $this->load->model("Weekly_times_model");
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

    /* load the project settings into ci settings */

    private function init_project_settings($project_id) {
        $settings = $this->Project_settings_model->get_all_where(array("project_id" => $project_id))->result();
        foreach ($settings as $setting) {
            $this->config->set_item($setting->setting_name, $setting->setting_value);
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

      $view_data['grid_id'] = $this->Weekly_model->get_one_where(array('user_id'=>$this->login_user->id,'deleted'=>0))->id;

      $team_members = array();
      foreach ($users as $key => $user) {
        $team_members[] = array(
          'id' => $user->id,
          'full_name' => $user->first_name.' '.$user->last_name,
          'image' => $user->image,
          'time_allocated' => $this->_calculate_hours(array('user_id'=>$user->id, 'grid_id'=>$view_data['grid_id']))
        );
      }

      $view_data['can_edit'] = $this->is_superadmin();
      $view_data['users'] = $team_members;
      $view_data['teams'] = $teams;
      $view_data['can_filter'] = $this->is_superadmin();
      $view_data['grid_data'] = unserialize($this->Weekly_model->get_one_where(array('user_id'=>$this->login_user->id,'deleted'=>0))->grid_projects);
      $this->template->rander('weekly/index',$view_data);
    }

    function import_weekly_project() {
      $g_id = $this->input->post('grid_id');
      $g_act = $this->input->post('act');

      if (!$this->can_edit_projects()) {
        redirect("forbidden");
      }

      $options = array(
        'range' => date('Y-m-d', strtotime('+2 weeks')),
        'deleted' => 0
      );

      $get_all = array_merge(
        $this->_create_grid_items('projects', $options),
        //$this->_create_grid_items('tasks', $options),
        $this->_create_grid_items('milestones', $options)
      );

      $grid_data = $this->Weekly_model->get_one_where(array('user_id'=>$this->login_user->id));

      $data['user_id'] = $this->login_user->id;
      $data['grid_projects'] = serialize($get_all);
      $data['created'] = date('Y-m-d');

      if($g_act === 'edit') {
        $new_grid_id = $this->Weekly_model->update_where($data, array('id'=>$g_id));
      }else{
        $new_grid_id = $this->Weekly_model->save($data);
      }

      if ($new_grid_id) {
          echo json_encode(array("success" => true, 'id' => $new_grid_id, 'message' => lang('import_success'), 'import_data'=> $get_all));
      } else {
          echo json_encode(array("success" => false, 'message' => lang('error_occurred'), 'import_data'=> $get_all));
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

    function import_project_milestone() {
      if (!$this->can_edit_projects()) {
        redirect("forbidden");
      }

      $p_id = $this->input->post('project_id');
      $m_id = $this->input->post('milestone_id');

      $grid_entry = array();

      $project   = $this->Projects_model->get_details(array('id'=>$p_id))->row();
      $milestone  = $this->Milestones_model->get_one($m_id);
      $project_tasks = $this->Tasks_model->get_details(array('project_id'=>$project->id,'deleted'=> 0))->result();

      $data['user_id'] = $this->login_user->id;

      $assignees = array();
      $project_members = $this->Project_members_model->get_details(array('project_id'=>$p_id))->result();

      $total_hours = array();

      foreach ($project_tasks as $key => $task) {
        $custom_values = $this->Custom_field_values_model->get_details(array('related_to_type'=>'tasks', 'related_to_id'=> $task->id , 'custom_field_id'=> 4))->result();
        $tasks[] = array(
          'id'    => $task->id,
          'title' => $task->title,
          'esti_hours' => $custom_values[0]->value
        );
        $total_time[] = $custom_values[0]->value;
      }

      foreach ($project_members as $key => $member) {
        $assignees[] = $member->user_id;
      }

      $grid_entry[] = array(
          'project_id' => $project->id,
          'unique_id' => $project->unique_project_id,
          'title' => $project->title,
          'description' => $project->description,
          'company_name' => $project->company_name,
          'deadline' => $project->deadline,
          'assigned_to' => implode(',',$assignees),
          'total_hours' => ''.array_sum($total_hours).'',
          'data-col' => '',
          'data-row' => '',
          'sizex' => '1',
          'is_milestone'  => ($m_id ? true : false),
          'milesone_id'   => ($m_id ? $milestone->id : ''),
          'milestone_name'=> ($m_id ? $milestone->title : '')
       );

       $cur_grid = $this->Weekly_model->get_details(array('user_id'=>$this->login_user->id,'deleted'=>0))->row();

       if (!empty($cur_grid)) {
        $de_grid   = unserialize($cur_grid->grid_projects);
        $new_grid  = array_merge($de_grid,$grid_entry);

        $data['grid_projects'] = serialize($new_grid);

        $grid_id = $this->Weekly_model->update_where($data, array('id'=>$cur_grid->id));

       } else {
        $data['grid_projects'] = serialize($grid_entry);

        $grid_id = $this->Weekly_model->save($data);
       }

       if ($grid_id) {
         echo json_encode(array( 'success' => true, 'message' => 'Saved successfully.', 'id' => $grid_id ));
       } else {
         echo json_encode(array( 'success' => false, 'message' => 'Error saving changes.' ));
       }
    }

    function save_grid_status() {
      $project_id     = $this->input->post('id');
      $grid_row       = $this->input->post('row');
      $grid_col       = $this->input->post('col');
      $grid_size      = $this->input->post('sizex');
      $grid_assignees = explode(',',$this->input->post('assignee'));
      $grid_time      = $this->input->post('time');
      $grid_data      = $this->Weekly_model->get_details(array('user_id'=>$this->login_user->id))->row();

      $data = unserialize($grid_data->grid_projects);

      foreach ($data as $key => $grid) {
        if ($project_id === $grid['project_id']) {
          $data[$key]['data-col'] = $grid_col;
          $data[$key]['data-row'] = $grid_row;
          $data[$key]['sizex']    = $grid_size;
        }
      }

      $user_time = array();
      $new_user_time = array();
      foreach ($grid_assignees as $key => $assignee) {
        $assignee_rows = $this->Weekly_times_model->get_details(array('user_id'=> $assignee, 'project_id' => $project_id));

        if ($assignee_rows->num_rows() <= 0) {
          $user_time[] = array(
            'grid_id'         => $grid_data->id,
            'project_id'      => $project_id,
            'user_id'         => $assignee,
            'time_allocated'  => $grid_time,
            'has_started'     => ($grid_col >= 3 ? '1': '0')
          );

          $id = $this->Weekly_times_model->save($user_time[$key]);
        } else {

          if ($grid_col < 3) {
            $update_user = array(
              'has_started' => '0'
            );
          } else {
            $update_user = array(
              'has_started' => '1'
            );
          }

          $update_id = $this->Weekly_times_model->update_where($update_user, array('user_id'=>$assignee, 'project_id'=> $project_id));
        }

        //get new updated time allocation
        $new_user_times[] = array(
          'user_id' => 'user_'.$assignee,
          'time_allocated' => $new_user_time = $this->_calculate_hours(array('user_id'=>$assignee, 'grid_id'=>$grid_data->id))
        );
      }

      $update_data = array('grid_projects'=>serialize($data));

      $grid_update_id = $this->Weekly_model->update_where($update_data, array('id'=>$grid_data->id));

      echo json_encode(array('data'=>$new_user_times));
    }

    function refresh_grid($grid_id) {
      $grid_data = unserialize($this->Weekly_model->get_details(array('id'=>$grid_id, 'deleted'=> 0))->result()[0]->grid_projects);

      foreach ($grid_data as $key => $data) {
        if ($data['item_type'] === 'milestones') {
          $grid_data[$key]['milestone_due'] = $this->Milestones_model->get_details(array('project_id'=>$data['project_id']))->result()[0]->due_date;
        } else {
          $grid_data[$key]['deadline'] = $this->Projects_model->get_details(array('id'=>$data['project_id']))->result()[0]->deadline;
        }
      }

      $update_data = array('grid_projects'=>serialize($grid_data));

      $grid_update_id = $this->Weekly_model->update_where($update_data, array('id'=>$grid_id));

      if ($grid_update_id) {
        echo json_encode(array("success" => true, 'message' => lang('record_updated'), 'id' => $grid_update_id));
      } else {
        echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_updated')));
      }
    }

    function get_project_milestones($project_id) {
      if (!$this->can_edit_projects()) {
        redirect('forbidden');
      }

      echo $this->_get_milestones_dropdown_list($project_id);
    }

    function delete_grid() {
      if (!$this->can_delete_projects()) {
        redirect('forbidden');
      }

      $id = $this->input->post('id');

      $success = $this->Weekly_model->delete_entry(array('grid_id'=>$id));

      if ($success) {
        echo json_encode(array("success" => true, 'message' => lang('record_deleted'), 'id' => $id));
      } else {
        echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
      }
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

    private function _get_milestones_dropdown_list($project_id = 0) {
        $milestones = $this->Milestones_model->get_all_where(array("project_id" => $project_id, "deleted" => 0))->result();
        $milestone_dropdown = array(array("id" => "", "text" => "- " . lang("milestone") . " -"));

        foreach ($milestones as $milestone) {
            $milestone_dropdown[] = array("id" => $milestone->id, "text" => $milestone->title);
        }
        return json_encode($milestone_dropdown);
    }

    private function _create_grid_items($item_type, $item_options) {

      $return_data = array();
      if ($item_type === 'projects') {
        $item_options['status'] = 'open';
        $data_items = $this->Projects_model->get_details($item_options)->result();
      }

      if ($item_type === 'tasks') {
        $data_items = $this->Tasks_model->get_details($item_options)->result();
      }

      if ($item_type === 'milestones') {
        $data_items = $this->Milestones_model->get_details($item_options)->result();
      }

      $total_time = array();
      $assignees = array();
      foreach ($data_items as $key => $item) {

        if ($item_type === 'milestones') {
          $project_details = $this->Projects_model->get_details(array('id'=>$item->project_id, 'deleted'=> 0))->row();

          if ($item_type === 'tasks') {
            $total_time[] = $this->_get_estimated_hours($item->id);
          }
        }

        if ($item_type === 'projects' || $item_type === 'milestones') {

          if ($item_type === 'milestones') {
            $members = $this->Project_members_model->get_details(array('project_id'=>$item->project_id,'deleted'=>0))->result();
            $tasks = $this->Tasks_model->get_details(array('project_id'=> $item->project_id, 'status'=> 'open', 'deleted'=>0))->result();
          } else {
            $members = $this->Project_members_model->get_details(array('project_id'=>$item->id,'deleted'=>0))->result();
            $tasks = $this->Tasks_model->get_details(array('project_id'=> $item->id, 'status'=> 'open', 'deleted'=>0))->result();
          }

          foreach ($tasks as $key => $task) {
            $total_time[] = $this->_get_estimated_hours($task->id);
          }

          foreach ($members as $key => $member) {
            $assignees[] = $member->user_id;
          }

        } else if ($item_type === 'tasks') {
          $assignee = explode(',',$item->assigned_to) ;
          $collaborators = $item->collaborators;

          $e_collaborators = explode(',',$collaborators);

          $assignees = array_merge($assignee, $e_collaborators);
        }

        $return_data[] = array(
            'project_id' => ($item_type === 'projects' ? $item->id : $project_details->id),
            'unique_id' => ($item_type === 'projects' ? $item->unique_project_id : $project_details->unique_project_id),
            'title' => ($item_type === 'projects' ? $item->title : $project_details->title),
            'description' => ($item_type === 'projects' ? $item->description : $project_details->description),
            'company_name' => ($item_type === 'projects' ? $item->company_name : $project_details->company_name),
            'deadline' => ($item_type === 'projects' ? $item->deadline : $project_details->deadline),
            'assigned_to' => implode(',',$assignees),
            'total_hours' => ''.array_sum($total_time).'',
            'data-col' => '',
            'data-row' => '',
            'sizex' => '1',
            'item_type' => $item_type,
            'is_milestone'  => ($item_type === 'milestones' ? true : false),
            'milestone_id'   => ($item_type === 'milestones' ? $item->id : ''),
            'milestone_name'=> ($item_type === 'milestones' ? $item->title : ''),
            'milestone_due'=> ($item_type === 'milestones' ? $item->due_date : '')
         );
      }

      return $return_data;
    }

    private function _get_estimated_hours($id) {
      return $this->Custom_field_values_model->get_details(array('related_to_type'=>'tasks', 'related_to_id'=> $id , 'custom_field_id'=> 4))->row()->value;
    }

    private function _calculate_hours($options) {
      $user       = get_array_value($options, 'user_id');
      $grid_id    = get_array_value($options, 'grid_id');

      $weekly_times = $this->Weekly_times_model->get_details(array('user_id'=>$user,'grid_id'=>$grid_id, 'has_started'=> 1))->result();

      $allocated_hours = array();
      foreach ($weekly_times as $key => $hour) {
        $allocated_hours[] = $hour->time_allocated;
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
