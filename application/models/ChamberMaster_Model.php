<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ChamberMaster_Model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	// GodownMaster Table Data
	public function get_GodownMaster_data()
	{

		$this->db->select(db_prefix() . 'godownmaster.*');
		$this->db->from(db_prefix() . 'godownmaster');
		$this->db->order_by('id', 'ASC');
		return $this->db->get()->result_array();
	}

	// ChamberMaster Table Data
	public function get_ChamberMaster_data()
	{
    $this->db->select('
        cm.*,
		gm.GodownName
    ');

    $this->db->from('tblChamberMaster cm');

    // GodownMaster
    $this->db->join('tblgodownmaster gm', 'gm.id = cm.GodownID', 'left');

    return $this->db->get()->result_array();
	}


	// Chamber Master Table Data By ChamberCode
	public function getChamberMasterDetails($ChamberCode)
	{
		$this->db->select(db_prefix() . 'ChamberMaster.*');
		$this->db->from(db_prefix() . 'ChamberMaster');
		$this->db->where(db_prefix() . 'ChamberMaster.ChamberCode', $ChamberCode);
		return $this->db->get()->row();
	}

	// Add New Chamber
	public function SaveChamberMaster($data)
	{
		$this->db->insert(db_prefix() . 'ChamberMaster', $data);
		$INSERT = $this->db->affected_rows();
		if ($INSERT > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Update the exisiting Chamber 
	public function UpdateChamberMaster($data, $ChamberCode)
	{
		$this->db->where('ChamberCode', $ChamberCode);
		$this->db->update(db_prefix() . 'ChamberMaster', $data);
		$UPDATE = $this->db->affected_rows();
		if ($UPDATE > 0) {
			return true;
		} else {
			return false;
		}
	}

}
