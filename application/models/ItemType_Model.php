<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class ItemType_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// Item Type Table Data
		public function get_ItemTypeMaster_data(){
			
			$this->db->select(db_prefix() . 'ItemTypeMaster.*');
			$this->db->from(db_prefix() . 'ItemTypeMaster');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}

		// Item Type Master Table Data By Name
		public function getItemTypeMasterDetails($ItemTypeName){
			
			$this->db->select(db_prefix() . 'ItemTypeMaster.*');
			$this->db->from(db_prefix() . 'ItemTypeMaster');
			$this->db->where(db_prefix() . 'ItemTypeMaster.ItemTypeName', $ItemTypeName);
			return $this->db->get()->row();
		}

		// Add New Item Category
		public function SaveItemTypeMaster($data)
		{
			$this->db->insert(db_prefix() . 'ItemTypeMaster', $data);
			$INSERT = $this->db->affected_rows();
			if ($INSERT > 0) {
				return true;
			} else {
				return false;
			}
		}

		// Update the exisiting Lot 
		public function UpdateItemTypeMaster($data, $ItemTypeName)
		{
			$this->db->where('ItemTypeName', $ItemTypeName);
			$this->db->update(db_prefix() . 'ItemTypeMaster', $data);
			$UPDATE = $this->db->affected_rows();
			if ($UPDATE > 0) {
				return true;
			} else {
				return false;
			}
		}
	}
