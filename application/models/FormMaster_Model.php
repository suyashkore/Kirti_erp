<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class FormMaster_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// Form Table Data
		public function get_Form_data(){
			
			$this->db->select(db_prefix() . 'Form.*');
			$this->db->from(db_prefix() . 'Form');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}

		// Form Table Data By ID
		public function getFormDetails($FormID){
			
			$this->db->select(db_prefix() . 'Form.*');
			$this->db->from(db_prefix() . 'Form');
			$this->db->where(db_prefix() . 'Form.id', $FormID);
			return $this->db->get()->row();
		}
		
		// Last Id For Form
		public function get_last_recordForm(){
			$this->db->select('*');
			$this->db->from('Form');
			$this->db->order_by('id', 'DESC');
			$FormRecord =  $this->db->get()->row();
			return $FormRecord ? $FormRecord->id : 0;
		}

		// Add New Territory
		public function SaveForm($data)
		{
			$this->db->insert(db_prefix() . 'Form', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}

		// Update Exiting Form
		public function UpdateForm($data,$FormID)
		{
			$this->db->where('id', $FormID);
			$this->db->update(db_prefix() . 'Form', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE >= 0){
				return true;
				}else{
					return false;
			}
		}

	}
