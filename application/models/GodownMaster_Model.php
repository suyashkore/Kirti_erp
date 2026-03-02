<?php

defined('BASEPATH') or exit('No direct script access allowed');

class GodownMaster_Model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	// All State for India/Country_id=1
	public function getallstate()
	{
		$this->db->where('country_id', '1');

		$this->db->order_by('state_name', 'ASE');

		return $this->db->get(db_prefix() . 'xx_statelist')->result_array();
	}
	public function GetCityList($id)
	{
		$query = $this->db->get_where('tblxx_citylist', array('state_id' => $id));
		return $result = $query->result_array();
	}


	// // GodownMaster Table Data
	// public function get_GodownMaster_data()
	// {

	// 	$this->db->select(db_prefix() . 'godownmaster.*');
	// 	$this->db->from(db_prefix() . 'godownmaster');
	// 	$this->db->order_by('id', 'ASC');
	// 	return $this->db->get()->result_array();
	// }

	// GodownMaster Table Data
	public function get_GodownMaster_data()
	{
    $this->db->select('
        gm.GodownCode,
        gm.GodownName,
        pl.LocationName,
        gm.Pincode,
        st.state_name AS StateName,
        ct.city_name AS CityName,
        gm.Address,
        gm.IsActive
    ');

    $this->db->from('tblgodownmaster gm');

    // Location
    $this->db->join('tblPlantLocationDetails pl', 'pl.id = gm.LocationID', 'left');

    // State
    $this->db->join('tblxx_statelist st', 'st.short_name = gm.Statecode', 'left');

    // City
    $this->db->join('tblxx_citylist ct', 'ct.id = gm.CityID', 'left');

    return $this->db->get()->result_array();

	}


	// Godown Master Table Data By GodownCode
	public function getGodownMasterDetails($GodownCode)
	{

		$this->db->select(db_prefix() . 'godownmaster.*');
		$this->db->from(db_prefix() . 'godownmaster');
		$this->db->where(db_prefix() . 'godownmaster.GodownCode', $GodownCode);
		return $this->db->get()->row();
	}

	// Add New Godown
	public function SaveGodownMaster($data)
	{
		$this->db->insert(db_prefix() . 'godownmaster', $data);
		$INSERT = $this->db->affected_rows();
		if ($INSERT > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Update the exisiting Godown 
	public function UpdateGodownMaster($data, $GodownCode)
	{
		$this->db->where('GodownCode', $GodownCode);
		$this->db->update(db_prefix() . 'godownmaster', $data);
		$UPDATE = $this->db->affected_rows();
		if ($UPDATE > 0) {
			return true;
		} else {
			return false;
		}
	}

	// Location Details
	public function get_location_detail()
	{
		$selected_company = $this->session->userdata('root_company');

		$this->db->select('pl.id, pl.LocationName');
		$this->db->from(db_prefix() . 'PlantLocationDetails pl');
		$this->db->where('pl.PlantID', $selected_company);

		return $this->db->get()->result_array();
	}
}
