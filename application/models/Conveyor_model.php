<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Conveyor_model extends App_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  // ===== GET PLANT LOCATION DROPDOWN =====
  public function get_plant_location()
  {
    $selected_company = $this->session->userdata('root_company');

    $this->db->select([db_prefix() . 'PlantLocationDetails.*']);
    $this->db->where(db_prefix() . 'PlantLocationDetails.PlantID', $selected_company);
    return $this->db->get('tblPlantLocationDetails')->result_array();
  }

  // ===== GET GODOWN ON PLANT LOCATION =====
  public function getGodownDropdownByPlantLocation($PlantLocation)
  {
    $this->db->select('gm.*');
    $this->db->from(db_prefix() . 'godownmaster gm');
    $this->db->where('gm.LocationID', $PlantLocation);
    return $this->db->get()->result_array();
  }

  // ===== CHECK's IF CONVERY ID ALREADY PRESENT =====
  function CheckConveyorIDExit($ConveyorID)
  {
    $this->db->select('*');
    $this->db->where("ShortCode", $ConveyorID);
    return $this->db->get(db_prefix() . 'ConveyorMaster')->row();
  }

  // ===== GET CONVEYOR DETAILS =====
  public function getConveyorDetails($Godown, $PlantLocation)
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'ConveyorMaster');
    $this->db->where('GodownID', $Godown);
    $this->db->where('PlantID', $PlantLocation);
    $query = $this->db->get();
    return $query->result_array();
  }

  // ===== SAVES CONVEYOR DATA =====
  public function SaveConveyor($data)
  {
    $insertData = [];
    $updateData = [];

    $count  = count($data['conveyor_name']);
    $now    = date('Y-m-d H:i:s');

    for ($i = 0; $i < $count; $i++) {
      $row = [
        'PlantID' => $data['PlantLocation'],
        'GodownID'        => $data['Godown'],
        'ConveyorName'  => $data['conveyor_name'][$i],
        'ShortCode'    => $data['conveyor_id'][$i],
        'IsActive'      => $data['IsActive'][$i],
      ];

      if (!empty($data['update_id'][$i])) {
        $row['id'] = $data['update_id'][$i];
        $row['UserID2'] = $data['UserID'];
        $row['Lupdate'] = $now;
        $updateData[] = $row;
      } else {
        $row['UserID'] = $data['UserID'];
        $row['TransDate'] = $now;
        $insertData[] = $row;
      }
    }

    $this->db->trans_start();

    if (!empty($insertData)) {
      $this->db->insert_batch(db_prefix() . 'ConveyorMaster', $insertData);
    }

    if (!empty($updateData)) {
      $this->db->update_batch(db_prefix() . 'ConveyorMaster', $updateData, 'id');
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }
}
