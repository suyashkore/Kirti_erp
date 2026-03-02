<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class BrandMaster_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// Brand Table Data
		public function get_Brand_data(){
			
			$this->db->select(db_prefix() . 'BrandMaster.*');
			$this->db->from(db_prefix() . 'BrandMaster');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}

		// Brand Table Data By ID
		public function getBrandDetails($BrandID){
			
			$this->db->select(db_prefix() . 'BrandMaster.*');
			$this->db->from(db_prefix() . 'BrandMaster');
			$this->db->where(db_prefix() . 'BrandMaster.id', $BrandID);
			return $this->db->get()->row();
		}
		
		// Last Id For Brand Master
		public function get_last_recordBrand(){
			$this->db->select('*');
			$this->db->from('BrandMaster');
			$this->db->order_by('id', 'DESC');
			$BrandRecord =  $this->db->get()->row();
			return $BrandRecord ? $BrandRecord->id : 0;
		}

		// Add New Brand
		public function SaveBrand($data)
		{
			$this->db->insert(db_prefix() . 'BrandMaster', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}

		// Update Exiting Brand
		public function UpdateBrand($data,$BrandID)
		{
			$this->db->where('id', $BrandID);
			$this->db->update(db_prefix() . 'BrandMaster', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE >= 0){
				return true;
				}else{
					return false;
			}
		}

	}
