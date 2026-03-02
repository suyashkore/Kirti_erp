<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CustomerCategory_Model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // // Priority Table Data
    // public function get_Priority_data()
    // {
    //     $this->db->select('
    //     pm.*,
    //     fm.FormName
    // ');
    //     $this->db->from('tblPriorityMaster pm');

    //     // Form
    //     $this->db->join('tblForm fm', 'fm.id = pm.FormID', 'left');

    //     return $this->db->get()->result_array();
    // }

    // // Form Table Data
    // public function get_Form_data()
    // {

    //     $this->db->select(db_prefix() . 'Form.*');
    //     $this->db->from(db_prefix() . 'Form');
    //     $this->db->order_by('id', 'ASC');
    //     return $this->db->get()->result_array();
    // }

    // // Priority Table Data By ID
    // public function getPriorityDetails($PriorityID){

    // 	$this->db->select(db_prefix() . 'PriorityMaster.*');
    // 	$this->db->from(db_prefix() . 'PriorityMaster');
    // 	$this->db->where(db_prefix() . 'PriorityMaster.id', $PriorityID);
    // 	return $this->db->get()->row();
    // }

    // // Last Id For Priority
    // public function get_last_recordPriority()
    // {
    //     $this->db->select('*');
    //     $this->db->from('PriorityMaster');
    //     $this->db->order_by('id', 'DESC');
    //     $PriorityRecord =  $this->db->get()->row();
    //     return $PriorityRecord ? $PriorityRecord->id : 0;
    // }

    // // Add New Priority
    // public function SavePriority($data)
    // {
    //     $this->db->insert(db_prefix() . 'PriorityMaster', $data);
    //     $INSERT = $this->db->affected_rows();
    //     if ($INSERT > 0) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // // Update Exiting Priority
    // public function UpdatePriority($data, $PriorityID)
    // {
    //     $this->db->where('id', $PriorityID);
    //     $this->db->update(db_prefix() . 'PriorityMaster', $data);
    //     $UPDATE = $this->db->affected_rows();
    //     if ($UPDATE >= 0) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
}
