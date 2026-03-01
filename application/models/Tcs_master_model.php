<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tcs_master_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '')
    {
        $c_date = date('Y-m-d');
        $this->db->select('*');
       $this->db->where('EffDate <=',date('Y-m-d'));
        $this->db->from(db_prefix() . 'tcsmaster');
        $this->db->order_by('id',"desc");
        //$this->db->limit(1);
        return $this->db->get()->result_array();
    }
    
    
    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data)
    {
        /*echo "<pre>";
        print_r($data);*/
        $loged_id = !DEFINED('CRON') ? get_staff_user_id() : 0;
        $tcsdate = array(
            'tcs' =>$data["tcspercent"],
            'EffDate' =>$data["tcsdate"],
            'UserId' =>$loged_id
            );
       /* print_r($tcsdate);
        die;*/
        
        $this->db->insert(db_prefix() . 'tcsmaster', $tcsdate);
        /*$insert_id = $this->db->insert_id();
        if ($insert_id) {*/
            
            log_activity('New TCS Added [ID:' . $data['name'] . ', ' . $data['name'] . ']');

            return true;
        /*}

        return false;*/
    }

    public function get_company_detail()
     {   
        $selected_company = $this->session->userdata('root_company');
        $sql ='SELECT '.db_prefix().'rootcompany.*
        FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
        $result = $this->db->query($sql)->row();
        return $result;
        
     }
    public function get_data_table()
    {
        $selected_company = $this->session->userdata('root_company');
       
        $data = $this->db->get(db_prefix() . 'tcsmaster')->result_array();
         return $data;
    }
    
}
