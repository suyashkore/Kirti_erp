<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class WeightUnitMaster_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// GodownMaster Table Data
	public function get_WeightUnitMaster_data()
	{

		$this->db->select(db_prefix() . 'WeightUnitMaster.*');
		$this->db->from(db_prefix() . 'WeightUnitMaster');
		$this->db->order_by('id', 'ASC');
		return $this->db->get()->result_array();
	}

	// Unit Master Table Data By UnitCode
	public function getWeightUnitMasterDetails($WeightUnitCode)
	{
		$this->db->select(db_prefix() . 'WeightUnitMaster.*');
		$this->db->from(db_prefix() . 'WeightUnitMaster');
		$this->db->where(db_prefix() . 'WeightUnitMaster.ShortCode', $WeightUnitCode);
		return $this->db->get()->row();
	}

	// Add New Unit
	public function SaveWeightUnitMaster($data)
	{
		$this->db->insert(db_prefix() . 'WeightUnitMaster', $data);
		$INSERT = $this->db->affected_rows();
		if ($INSERT > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Update the exisiting Unit 
	public function UpdateWeightUnitMaster($data, $WeightUnitCode)
	{
		$this->db->where('ShortCode', $WeightUnitCode);
		$this->db->update(db_prefix() . 'WeightUnitMaster', $data);
		$UPDATE = $this->db->affected_rows();
		if ($UPDATE > 0) {
			return true;
		} else {
			return false;
		}
	}

		

	}
