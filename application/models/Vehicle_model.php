<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Vehicle_model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		/**
			* Get invoice item by ID
			* @param  mixed $id
			* @return mixed - array if not passed id, object if id passed
		*/
		public function get($id = '')
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('*');
			$this->db->from(db_prefix() . 'vehicle');
			//if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'vehicle.VehicleID', $id);
            $this->db->where(db_prefix() . 'vehicle.PlantID', $selected_company);
			
            return $this->db->get()->row();
			//}
			// return $this->db->get()->result_array();
		}
		
		
		/**
			* Add new invoice item
			* @param array $data Invoice item data
			* @return boolean
		*/
		public function add($data)
		{
			unset($data["itemid"]);
			$selected_company = $this->session->userdata('root_company');
			$data["PlantID"] = $selected_company;
			$this->db->insert(db_prefix() . 'vehicle', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				
				log_activity('New Vehicle Added [ID:' . $insert_id . ', ' . $data['VehicleID'] . ']');
				
				return $insert_id;
			}
			
			return true;
		}
		
		/**
			* Update invoiec item
			* @param  array $data Invoice data to update
			* @return boolean
		*/
		public function edit($data)
		{
			$itemid = $data['itemid'];
			unset($data['itemid']);
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('VehicleID', $itemid);
			$this->db->where('PlantID', $selected_company);
			$this->db->update(db_prefix() . 'vehicle', $data);
			if ($this->db->affected_rows() > 0) {
				log_activity(' Vehicle Updated [ID: ' . $itemid . ', ' . $data['VehicleID'] . ']');
				$affectedRows++;
			}
			
			
			
			return $affectedRows > 0 ? true : false;
		}
		
		/**
			* Delete invoice item
			* @param  mixed $id
			* @return boolean
		*/
		public function delete($id)
		{
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->where('PlantID', $selected_company);
			$this->db->where('VehicleID', $id);
			$this->db->delete(db_prefix() . 'vehicle');
			if ($this->db->affected_rows() > 0) {
				
				
				log_activity('Vehicle Item Deleted [ID: ' . $id . ']');
				
				
				
				return true;
			}
			
			return false;
		}
//========================= Get Vehicle List ===================================
		public function GetVehicleList()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'vehicle.*,tblstaff.firstname,tblstaff.lastname');
			$this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.AccountID = ' . db_prefix() . 'vehicle.DriverID',"LEFT");
			$this->db->where(db_prefix() . 'vehicle.PlantID',$selected_company);
			return $this->db->get(db_prefix() . 'vehicle')->result_array();
		}
		
		public function get_company_detail()
		{   
			$selected_company = $this->session->userdata('root_company');
			$sql ='SELECT '.db_prefix().'rootcompany.*
			FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
			$result = $this->db->query($sql)->row();
			return $result;
			
		}
		
		function accountlist_driver(){
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select(db_prefix() . 'staff.*');
			$this->db->join(db_prefix() . 'accountsld', '' . db_prefix() . 'accountsld.AccountID = ' . db_prefix() . 'staff.AccountID');
			$this->db->where(db_prefix() . 'accountsld.SLDTypeID','3');
			$this->db->where(db_prefix() . 'accountsld.PlantID',$selected_company);
			return $this->db->get(db_prefix() . 'staff')->result_array();
			
		}

		public function get_tds_sections()
{
    $this->db->select(db_prefix().'TDSMaster.*');
    $this->db->from(db_prefix().'TDSMaster');
    return $this->db->get()->result_array();
}

public function gettdspercent_new($Tdsselection)
{
    $this->db->select(db_prefix().'TDSDetails.*');
    $this->db->where(db_prefix().'TDSDetails.TDSCode', $Tdsselection);
    $this->db->from(db_prefix().'TDSDetails');
    return $this->db->get()->result_array();
}
public function get_vehicle()
{
    return $this->db->get(db_prefix() . 'vehicle')->result_array();
}


	}
