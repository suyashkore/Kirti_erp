<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hsn_master_model extends App_Model
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
        
        $this->db->select('*');
        $this->db->from(db_prefix() . 'hsn');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'hsn.id', $id);

            return $this->db->get()->row();
        }
        return $this->db->get()->result_array();
    }
    
    

    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data)
    {
        unset($data["itemid"]);
        
        $data["created_date"] = date('Y-m-d h:i:s');
        
        $this->db->insert(db_prefix() . 'hsn', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            

           

            log_activity('New HSN Added [ID:' . $insert_id . ', ' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update invoiec item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data)
    {
        $itemid = $data['itemid'];
        unset($data['itemid']);

        $data["created_date"] = date('Y-m-d h:i:s');
        $data["Lupdate"] = date('Y-m-d h:i:s');
        if (is_staff_logged_in()) {
            $data['UserID2'] = get_staff_user_id();
        }
        $this->db->where('id', $itemid);
        $this->db->update(db_prefix() . 'hsn', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity(' HSN Code Updated [ID: ' . $itemid . ', ' . $data['name'] . ']');
            $affectedRows++;
        }

        

        return $affectedRows > 0 ? true : false;
    }

    public function hsn_dependancy($id = '')
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $sql = 'SELECT tblhsn.* FROM tblhsn
        INNER JOIN tblitems ON tblitems.hsn_code=tblhsn.name 
        WHERE tblhsn.name = '.$id;
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('name', $id);
        $this->db->delete(db_prefix() . 'hsn');
        if ($this->db->affected_rows() > 0) {
            

            log_activity('HSN Code Deleted [ID: ' . $id . ']');

            

            return true;
        }

        return false;
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
        
        //$this->db->where(db_prefix() . 'hsn.PlantID', $selected_company);
        $this->db->order_by(db_prefix() . 'hsn.name', 'ASC');
        $data = $this->db->get(db_prefix() . 'hsn')->result_array();
         return $data;
    }
    
}
