<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Item_Division_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		// ItemDivision Table Data
		public function get_ItemDivision_data(){
			
			$this->db->select(db_prefix() . 'ItemsDivisionMaster.*');
			$this->db->from(db_prefix() . 'ItemsDivisionMaster');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}

		// Item Division Table Data By ID
		public function getitemDivisionDetails($ItemDivisionID){
			
			$this->db->select(db_prefix() . 'ItemsDivisionMaster.*');
			$this->db->from(db_prefix() . 'ItemsDivisionMaster');
			$this->db->where(db_prefix() . 'ItemsDivisionMaster.id', $ItemDivisionID);
			return $this->db->get()->row();
		}

		// Last Id For Item Division
		public function get_last_recordItemDevision(){
			$this->db->select('*');
			$this->db->from('ItemsDivisionMaster');
			$this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
			$ItemDivisionRecord =  $this->db->get()->row();
			return $ItemDivisionRecord ? $ItemDivisionRecord->id : 0;
		}

		// Add New Item Division
		public function SaveItemDivision($data)
		{
			$this->db->insert(db_prefix() . 'ItemsDivisionMaster', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}

		

		// Update Exiting Item Division
		public function UpdateItemDivision($data,$ItemDivisionID)
		{
			$this->db->where('id', $ItemDivisionID);
			$this->db->update(db_prefix() . 'ItemsDivisionMaster', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE >= 0){
				return true;
				}else{
					return false;
			}
		}

	}
