<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class TerritoryMaster_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// Territory Table Data
		public function get_Territory_data(){
			
			$this->db->select(db_prefix() . 'Territory.*');
			$this->db->from(db_prefix() . 'Territory');
			$this->db->order_by('Id', 'ASC');
			return $this->db->get()->result_array();
		}

		// Territory Table Data By ID
		public function getTerritoryDetails($TerritoryID){
			
			$this->db->select(db_prefix() . 'Territory.*');
			$this->db->from(db_prefix() . 'Territory');
			$this->db->where(db_prefix() . 'Territory.Id', $TerritoryID);
			return $this->db->get()->row();
		}
		
		// Last Id For Territory
		public function get_last_recordTerritory(){
			$this->db->select('*');
			$this->db->from('Territory');
			$this->db->order_by('Id', 'DESC');
			$TerritoryRecord =  $this->db->get()->row();
			return $TerritoryRecord ? $TerritoryRecord->Id : 0;
		}

		// Add New Territory
		public function SaveTerritory($data)
		{
			$this->db->insert(db_prefix() . 'Territory', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}

		// Update Exiting Territory
		public function UpdateTerritory($data,$TerritoryID)
		{
			$this->db->where('Id', $TerritoryID);
			$this->db->update(db_prefix() . 'Territory', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE >= 0){
				return true;
				}else{
					return false;
			}
		}

	}
