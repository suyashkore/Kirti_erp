<?php

defined('BASEPATH') or exit('No direct script access allowed');

class GSTMaster_Model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // GST Table Data
    public function get_GST_data()
    {
        $this->db->select('
        t.*
    ');
        $this->db->from('tbltaxes t');

        return $this->db->get()->result_array();
    }

    // GST Table Data By ID
    public function getGSTDetails($GSTID){

    	$this->db->select(db_prefix() . 'taxes.*');
    	$this->db->from(db_prefix() . 'taxes');
    	$this->db->where(db_prefix() . 'taxes.id', $GSTID);
    	return $this->db->get()->row();
    }

    // Last Id For GST
    public function get_last_recordGST()
    {
        $this->db->select('*');
        $this->db->from('taxes');
        $this->db->order_by('id', 'DESC');
        $GSTRecord =  $this->db->get()->row();
        return $GSTRecord ? $GSTRecord->id : 0;
    }

    // Add New GST
    public function SaveGST($data)
    {
        $this->db->insert(db_prefix() . 'taxes', $data);
        $INSERT = $this->db->affected_rows();
        if ($INSERT > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Update Exiting GST
    public function UpdateGST($data, $GSTID)
    {
        $this->db->where('id', $GSTID);
        $this->db->update(db_prefix() . 'taxes', $data);
        $UPDATE = $this->db->affected_rows();
        if ($UPDATE >= 0) {
            return true;
        } else {
            return false;
        }
    }
}
