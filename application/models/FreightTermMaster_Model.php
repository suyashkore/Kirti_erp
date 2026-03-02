<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class FreightTermMaster_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// Freight Term Table Data
		public function get_FreightTerm_data(){
			
			$this->db->select(db_prefix() . 'FreightTerms.*');
			$this->db->from(db_prefix() . 'FreightTerms');
			$this->db->order_by('Id', 'ASC');
			return $this->db->get()->result_array();
		}

		// Item Division Table Data By ID
		public function getFreightTermDetails($FreightTermID){
			
			$this->db->select(db_prefix() . 'FreightTerms.*');
			$this->db->from(db_prefix() . 'FreightTerms');
			$this->db->where(db_prefix() . 'FreightTerms.Id', $FreightTermID);
			return $this->db->get()->row();
		}
		
		// Last Id For Freight Terms
		public function get_last_recordFreightTerm(){
			$this->db->select('*');
			$this->db->from('FreightTerms');
			$this->db->order_by('Id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
			$FreightTermsRecord =  $this->db->get()->row();
			return $FreightTermsRecord ? $FreightTermsRecord->Id : 0;
		}

		// Add New Freight Terms
		public function SaveFreightTerm($data)
		{
			$this->db->insert(db_prefix() . 'FreightTerms', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}

		// Update Exiting Freight Terms
		public function UpdateFreightTerm($data,$FreightTermID)
		{
			$this->db->where('Id', $FreightTermID);
			$this->db->update(db_prefix() . 'FreightTerms', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE >= 0){
				return true;
				}else{
					return false;
			}
		}

	}
