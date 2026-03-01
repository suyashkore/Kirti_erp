<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Roles_model extends App_Model

{

    /**

     * Add new employee role

     * @param mixed $data

     */

    public function add($data)

    {

        $permissions = [];

        if (isset($data['permissions'])) {

            $permissions = $data['permissions'];

        }



        $data['permissions'] = serialize($permissions);



        $this->db->insert(db_prefix() . 'roles', $data);

        $insert_id = $this->db->insert_id();



        if ($insert_id) {

            log_activity('New Role Added [ID: ' . $insert_id . '.' . $data['name'] . ']');



            return $insert_id;

        }



        return false;

    }



    /**

     * Update employee role

     * @param  array $data role data

     * @param  mixed $id   role id

     * @return boolean

     */

    public function update($data, $id)

    {

        $affectedRows = 0;

        $permissions  = [];

        if (isset($data['permissions'])) {

            $permissions = $data['permissions'];

        }



        $data['permissions'] = serialize($permissions);



        $update_staff_permissions = false;

        if (isset($data['update_staff_permissions'])) {

            $update_staff_permissions = true;

            unset($data['update_staff_permissions']);

        }



        $this->db->where('roleid', $id);

        $this->db->update(db_prefix() . 'roles', $data);



        if ($this->db->affected_rows() > 0) {

            $affectedRows++;

        }



        if ($update_staff_permissions == true) {

            $this->load->model('staff_model');



            $staff = $this->staff_model->get('', [

                'role' => $id,

            ]);



            foreach ($staff as $member) {

                if ($this->staff_model->update_permissions($permissions, $member['staffid'])) {

                    $affectedRows++;

                }

            }

        }



        if ($affectedRows > 0) {

            log_activity('Role Updated [ID: ' . $id . ', Name: ' . $data['name'] . ']');



            return true;

        }



        return false;

    }
    /**

     * Update employee role

     * @param  array $data role data

     * @param  mixed $id   role id

     * @return boolean

     */

    public function update_staff_permission($data, $id)

    {

        $affectedRows = 0;

        $permissions  = [];

        if (isset($data['permissions'])) {

            $permissions = $data['permissions'];

        }



        $data['permissions'] = serialize($permissions);

        

       /* echo "<pre>";

        print_r($data);*/

        $aa = explode("-",$data['company_select']);

        

        $plant_id= $aa[0];

        $year = $aa[1];

        $day_detail = $data['number_day'];

       

        

        if ($this->staff_model->update_permissions_new($permissions, $id,$plant_id,$year,$day_detail,$data)) {

                    $affectedRows++;

                }



        if ($affectedRows > 0) {

            log_activity('Staff permission Updated [ID: ' . $id . ', Name: ' . $data['username'] . ']');



            return true;

        }



        return false;

    }

    

    function get_userlist_details_by_userid($postData){



    $response = array();

    $selected_company = $this->session->userdata('root_company');

    $regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';

    $where_ = '';

     if(isset($postData['search']) ){

       

       $q = $postData['search'];

       

       $this->db->select(db_prefix() . 'staff.*');

       $where_ .= '(AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR username LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q. '%" ESCAPE \'!\' OR lastname LIKE "%' . $q. '%" ESCAPE \'!\')';

       $this->db->where($where_);

       $this->db->where('login_access','Yes');

       //$this->db->where('tblstaff.staff_comp REGEXP',$regExp);

       $records = $this->db->get(db_prefix() . 'staff')->result();

    //   echo $this->db->last_query();die;



       foreach($records as $row ){

           $full_name = $row->firstname." ".$row->lastname;

          $response[] = array("label"=>$full_name,"value"=>$row->AccountID,"staff_id"=>$row->staffid);

       }



     }



     return $response;

  }



    /**

     * Get employee role by id

     * @param  mixed $id Optional role id

     * @return mixed     array if not id passed else object

     */

    public function get($id = '')

    {

        if (is_numeric($id)) {



            $role = $this->app_object_cache->get('role-' . $id);



            if ($role) {

                return $role;

            }



            $this->db->where('roleid', $id);



            $role              = $this->db->get(db_prefix() . 'roles')->row();

            $role->permissions = !empty($role->permissions) ? unserialize($role->permissions) : [];



            $this->app_object_cache->add('role-' . $id, $role);



            return $role;

        }



        return $this->db->get(db_prefix() . 'roles')->result_array();

    }

    /**

     * Get employee role by id

     * @param  mixed $id Optional role id

     * @return mixed     array if not id passed else object

     */

    public function get_firmDetails()

    {

    

        return $this->db->get(db_prefix() . 'setup')->result_array();

    }



    /**

     * Delete employee role

     * @param  mixed $id role id

     * @return mixed

     */

    public function delete($id)

    {

        $current = $this->get($id);



        // Check first if role is used in table

        if (is_reference_in_table('role', db_prefix() . 'staff', $id)) {

            return [

                'referenced' => true,

            ];

        }



        $affectedRows = 0;

        $this->db->where('roleid', $id);

        $this->db->delete(db_prefix() . 'roles');



        if ($this->db->affected_rows() > 0) {

            $affectedRows++;

        }



        if ($affectedRows > 0) {

            log_activity('Role Deleted [ID: ' . $id);



            return true;

        }



        return false;

    }



    public function get_contact_permissions($id)

    {

        $this->db->where('userid', $id);



        return $this->db->get(db_prefix() . 'contact_permissions')->result_array();

    }



    public function get_role_staff($role_id)

    {

        $this->db->where('role', $role_id);



        return $this->db->get(db_prefix() . 'staff')->result_array();

    }

    

    public function get_all_staff()

    {

        $this->db->where('admin', 0);

        $this->db->where('active', 1);

        return $this->db->get(db_prefix() . 'staff')->result_array();

    }


}

