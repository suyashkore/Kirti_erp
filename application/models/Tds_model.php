<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tds_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
   
    // Get Next TDS Code (Auto Increment)
    public function GetNextTDSCode()
    {
		$this->db->select('COALESCE(MAX(CAST(TDSCode AS UNSIGNED)), 0) + 1 AS NextCode', FALSE);
		$result = $this->db->get(db_prefix() . 'TDSMaster')->row();
        return str_pad($result->NextCode, 4, '0', STR_PAD_LEFT);
    }

    public function GetTDSList()
        {
			$this->db->select('TDSCode, TDSName,ThresholdLimit');
			$TDSList = $this->db->get(db_prefix() . 'TDSMaster')->result_array();
			return $TDSList;
        }

	public function GetTDSDetails($TDSCode)
	{
		$this->db->select(db_prefix() . 'TDSMaster.*');
		$this->db->where('TDSCode', $TDSCode);
		$TDSDetails = $this->db->get(db_prefix() . 'TDSMaster')->row();
		if($TDSDetails){
			$this->db->select(db_prefix() . 'TDSDetails.*');
			$this->db->where('TDSCode', $TDSCode);
			$TDSDetailsList = $this->db->get(db_prefix() . 'TDSDetails')->result_array();
			$TDSDetails->Details = $TDSDetailsList;
		}
		return $TDSDetails;
	}
	 // Add New ItemID
    public function SaveItemID($data)
    {
		$ParameterArray = array();
		if (isset($data['paradataSerializedArr'])) {
			$ParameterArray = json_decode($data['paradataSerializedArr'], true);
			if (!is_array($ParameterArray)) {
				$ParameterArray = array();
			}
		}
		unset($data['paradataArraylength']);
		unset($data['paradataSerializedArr']);

		// Use transaction for atomic insert
		$this->db->trans_start();
		$this->db->insert(db_prefix() . 'TDSMaster', $data);

		if (!empty($ParameterArray)) {
			foreach ($ParameterArray as $value) {
				$insertArray = array(
					"TDSCode" => $data["TDSCode"],
					"effective_date" => date('Y-m-d H:i:s'),
					"description" => isset($value[0]) ? $value[0] : '',
					"rate" => isset($value[1]) ? $value[1] : 0,
					"Transdate" => date('Y-m-d H:i:s'),
				);
				$this->db->insert(db_prefix() . 'TDSDetails', $insertArray);
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		return true;
    }
	
	 // Update Exiting TDSCode
    public function UpdateItemID($data)
    {
		$ParameterArray = array();
		if (isset($data['paradataSerializedArr'])) {
			$ParameterArray = json_decode($data['paradataSerializedArr'], true);
			if (!is_array($ParameterArray)) {
				$ParameterArray = array();
			}
		}
		$TDSCode = isset($data['TDSCode']) ? $data['TDSCode'] : '';
		unset($data["paradataSerializedArr"]);
		unset($data["paradataArraylength"]);

		// Use transaction for update
		$this->db->trans_start();
		$this->db->where('TDSCode', $TDSCode);
		$this->db->update(db_prefix() . 'TDSMaster', $data);

		// Insert / Update Parameter
		for ($k = 0; $k < count($ParameterArray); $k++) {
			$description = isset($ParameterArray[$k][0]) ? $ParameterArray[$k][0] : '';
			$rate = isset($ParameterArray[$k][1]) ? $ParameterArray[$k][1] : 0;
			$ids = isset($ParameterArray[$k][2]) ? $ParameterArray[$k][2] : '';
			if (!empty($ids)) {
				$UpdateAddress = array(
					"description" => $description,
					"rate" => $rate,
				);
				$this->db->where('id', $ids);
				$this->db->update(db_prefix() . 'TDSDetails', $UpdateAddress);
			} else {
				$InsAddress = array(
					"TDSCode" => $TDSCode,
					"effective_date" => date('Y-m-d H:i:s'),
					"description" => $description,
					"rate" => $rate,
					"Transdate" => date('Y-m-d H:i:s')
				);
				$this->db->insert(db_prefix() . 'TDSDetails', $InsAddress);
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		return true;
        
    }
    
}

