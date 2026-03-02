<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class UnitMaster_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// GodownMaster Table Data
	public function get_UnitMaster_data()
	{

		$this->db->select(db_prefix() . 'UnitMaster.*');
		$this->db->from(db_prefix() . 'UnitMaster');
		$this->db->order_by('id', 'ASC');
		return $this->db->get()->result_array();
	}

	// Unit Master Table Data By UnitCode
	public function getUnitMasterDetails($UnitCode)
	{
		$this->db->select(db_prefix() . 'UnitMaster.*');
		$this->db->from(db_prefix() . 'UnitMaster');
		$this->db->where(db_prefix() . 'UnitMaster.ShortCode', $UnitCode);
		return $this->db->get()->row();
	}

	// Add New Unit
	public function SaveUnitMaster($data)
	{
		$this->db->insert(db_prefix() . 'UnitMaster', $data);
		$INSERT = $this->db->affected_rows();
		if ($INSERT > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Update the exisiting Unit 
	public function UpdateUnitMaster($data, $UnitCode)
	{
		$this->db->where('ShortCode', $UnitCode);
		$this->db->update(db_prefix() . 'UnitMaster', $data);
		$UPDATE = $this->db->affected_rows();
		if ($UPDATE > 0) {
			return true;
		} else {
			return false;
		}
	}

		

	}
