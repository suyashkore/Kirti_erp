<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Roles extends AdminController

{

    /* List all staff roles */

    public function index()

    {

        if (staff_cant('view', 'roles')) {

            access_denied('roles');

        }

        if ($this->input->is_ajax_request()) {

            $this->app->get_table_data('roles');

        }

        $data['title'] = _l('all_roles');

        $this->load->view('admin/roles/manage', $data);

    }

    /* Add new role or edit existing one New */
    public function user_rights($id = '')
    {
        if (!has_permission_new('user_rights', '', 'view')) {
            access_denied('access denied');
        }
        if ($this->input->post()) {
            
            $id = $this->input->post('staff_id');
            if ($id == '') {
                if (!has_permission_new('user_rights', '', 'edit')) {
                        access_denied('access denied');
                }
                $id = $this->roles_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('role')));
                    redirect(admin_url('roles/role/' . $id));
                }
            } else {
                if (!has_permission_new('user_rights', '', 'edit')) {
                        access_denied('access denied');
                }
                $success = $this->roles_model->update_staff_permission($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', "Staff Permission"));
                }
                redirect(admin_url('roles/user_rights/'));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('role_lowercase'));
        } else {
            $data['firm_data'] = $this->roles_model->get_firmDetails();
            $member = $this->staff_model->get($id);
            $data['member']            = $member;
            /*echo "<pre>";
            print_r($data['member']);
            die;*/
            $data['role_staff'] = $this->roles_model->get_role_staff($id);
            $role               = $this->roles_model->get($id);
            $data['role']       = $role;
            $title              = _l('edit', "Staff Permission") . ' ' . $member->firstname . ' ' . $member->lastname;
        }
        
        $data['all_staff'] = $this->roles_model->get_all_staff();
        $data['title'] = $title;
        $this->load->view('admin/roles/role_new', $data);
    }
    
    public function get_userlist_details_by_userid(){
        
    // POST data
    $postData = $this->input->post();

    // Get data
    $data = $this->roles_model->get_userlist_details_by_userid($postData);

    echo json_encode($data);
  }
    
    //------------------- List of menu permission-------------------------------
    public function get_permission_by_staff()
    {
        $staff_id = $this->input->post('staff_id'); 
        $plant_fy = $this->input->post('plant_fy'); 
        $aa = explode("-",$plant_fy);
        
        $plant_id= $aa[0];
        $year = $aa[1];
        $member = $this->staff_model->get_new($staff_id,$plant_id,$year);
        $role               = $this->roles_model->get($staff_id);
        $data['role']       = $role;
            if(isset($member)) {
                $permissionsData['member'] = $member;
            }
       return $this->load->view('admin/staff/permissions_new', $permissionsData);
    }
    
    //------------------- List of Company selection-------------------------------
    public function get_company_list()
    {
        $staff_id = $this->input->post('staff_id'); 
        $member = $this->staff_model->get_staff_details_for_user_rights($staff_id);
        //echo json_encode($userID);
        if(empty($member)){
            return false;
        }else{
            $firm_data['details'] = $this->roles_model->get_firmDetails();
            
            return $this->load->view('admin/roles/company_list', $firm_data);
            
        }
        
    }
    
    //------------------- Get User details -------------------------------
    public function get_user_details()
    {
        $staff_id = $this->input->post('staff_id'); 
        $member = $this->staff_model->get_staff_details_for_user_rights($staff_id);
        //echo json_encode($userID);
        if(empty($member)){
            return false;
        }else{
           // return $member;
            echo json_encode($member);
        }
        
    }
    
    //------------------- Get User details Using username -------------------------------
    public function get_user_details_by_userid()
    {
        $user_id = $this->input->post('user_id'); 
        $member = $this->staff_model->get_staff_details_for_using_username($user_id);
        //echo json_encode($userID);
        if($member == false){
            echo json_encode("false");
        }else{
           // return $member;
            echo json_encode($member);
        }
        
    }



    /* Add new role or edit existing one */

    public function role($id = '')

    {

        if (staff_cant('view', 'roles')) {

            access_denied('roles');

        }

        if ($this->input->post()) {

            if ($id == '') {

                if (staff_cant('create', 'roles')) {

                    access_denied('roles');

                }

                $id = $this->roles_model->add($this->input->post());

                if ($id) {

                    set_alert('success', _l('added_successfully', _l('role')));

                    redirect(admin_url('roles/role/' . $id));

                }

            } else {

                if (staff_cant('edit', 'roles')) {

                    access_denied('roles');

                }

                $success = $this->roles_model->update($this->input->post(), $id);

                if ($success) {

                    set_alert('success', _l('updated_successfully', _l('role')));

                }

                redirect(admin_url('roles/role/' . $id));

            }

        }

        if ($id == '') {

            $title = _l('add_new', _l('role'));

        } else {

            $data['role_staff'] = $this->roles_model->get_role_staff($id);

            $role               = $this->roles_model->get($id);

            $data['role']       = $role;

            $title              = _l('edit', _l('role')) . ' ' . $role->name;

        }

        $data['title'] = $title;

        $this->load->view('admin/roles/role', $data);

    }



    /* Delete role from database */

    public function delete($id)

    {

        if (staff_cant('delete', 'roles')) {

            access_denied('roles');

        }

        if (!$id) {

            redirect(admin_url('roles'));

        }

        $response = $this->roles_model->delete($id);

        if (is_array($response) && isset($response['referenced'])) {

            set_alert('warning', _l('is_referenced', _l('role_lowercase')));

        } elseif ($response == true) {

            set_alert('success', _l('deleted', _l('role')));

        } else {

            set_alert('warning', _l('problem_deleting', _l('role_lowercase')));

        }

        redirect(admin_url('roles'));

    }

}

