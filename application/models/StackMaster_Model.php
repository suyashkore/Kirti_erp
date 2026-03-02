<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class StackMaster_Model extends App_Model
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

		$this->db->select(db_prefix() . 'ChamberMaster.*');
		$this->db->from(db_prefix() . 'ChamberMaster');
		$this->db->order_by('id', 'ASC');
		return $this->db->get()->result_array();
	}



	// StackMaster Table Data
	public function get_StackMaster_data()
	{
    $this->db->select('
        sm.*,
		gm.GodownName,
		ch.ChamberName
    ');

    $this->db->from('tblStackMaster sm');

    // GodownMaster
    $this->db->join('tblgodownmaster gm', 'gm.id = sm.GodownID', 'left');

	// ChamberMaster
    $this->db->join('tblChamberMaster ch', 'ch.id = sm.ChamberID', 'left');

    return $this->db->get()->result_array();
	}


	// Stack Master Table Data By StackCode
	public function getStackMasterDetails($StackCode)
	{
		$this->db->select(db_prefix() . 'StackMaster.*');
		$this->db->from(db_prefix() . 'StackMaster');
		$this->db->where(db_prefix() . 'StackMaster.StackCode', $StackCode);
		return $this->db->get()->row();
	}

	// Add New Stack
	public function SaveStackMaster($data)
	{
		$this->db->insert(db_prefix() . 'StackMaster', $data);
		$INSERT = $this->db->affected_rows();
		if ($INSERT > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Update the exisiting Stack 
	public function UpdateStackMaster($data, $StackCode)
	{
		$this->db->where('StackCode', $StackCode);
		$this->db->update(db_prefix() . 'StackMaster', $data);
		$UPDATE = $this->db->affected_rows();
		if ($UPDATE > 0) {
			return true;
		} else {
			return false;
		}
	}

		

	}
