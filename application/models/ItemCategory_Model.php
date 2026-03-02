<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class ItemCategory_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// GodownMaster Table Data
		public function get_ItemTypeMaster_data(){
			
			$this->db->select(db_prefix() . 'ItemTypeMaster.*');
			$this->db->from(db_prefix() . 'ItemTypeMaster');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}

		// ItemCategoryMaster Table Data
		public function get_ItemCategoryMaster_data()
		{
    	$this->db->select('
        	icm.*,
			itm.ItemTypeName
    	');

    	$this->db->from('tblItemCategoryMaster icm');

    	// ItemTypeMaster
    	$this->db->join('tblItemTypeMaster itm', 'itm.id = icm.ItemType', 'left');

    	return $this->db->get()->result_array();
		}

		// Item Category Master Table Data By ID
		public function getItemCategoryMasterDetails($CategoryCode){
			
			$this->db->select(db_prefix() . 'ItemCategoryMaster.*');
			$this->db->from(db_prefix() . 'ItemCategoryMaster');
			$this->db->where(db_prefix() . 'ItemCategoryMaster.CategoryCode', $CategoryCode);
			return $this->db->get()->row();
		}

		// Add New Item Category
		public function SaveItemCategoryMaster($data)
		{
			$this->db->insert(db_prefix() . 'ItemCategoryMaster', $data);
			$INSERT = $this->db->affected_rows();
			if ($INSERT > 0) {
				return true;
			} else {
				return false;
			}
		}

		// Update the exisiting Lot 
		public function UpdateItemCategoryMaster($data, $CategoryCode)
		{
			$this->db->where('CategoryCode', $CategoryCode);
			$this->db->update(db_prefix() . 'ItemCategoryMaster', $data);
			$UPDATE = $this->db->affected_rows();
			if ($UPDATE > 0) {
				return true;
			} else {
				return false;
			}
		}
	}
