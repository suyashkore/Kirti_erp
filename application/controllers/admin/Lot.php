<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Lot extends AdminController
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('LotMaster_Model');
	}
	public function index()
	{
		if (!has_permission_new('LotMaster', '', 'view')) {
			access_denied('Lot Master');
		}
		$data['table_data'] = $this->LotMaster_Model->get_LotMaster_data();

		$data['godown'] = $this->LotMaster_Model->get_GodownMaster_data();

		// $data['chamber'] = $this->LotMaster_Model->get_ChamberMaster_data();

		// $data['stack'] = $this->LotMaster_Model->get_StackMaster_data();


		$this->load->view('admin/Lot/AddEditLot.php', $data);
	}

	// Get the Lot Master Details By ID
	public function GetLotMasterDetailByID()
	{
		$LotCode = $this->input->post('LotCode');
		$row = $this->LotMaster_Model->getLotMasterDetails($LotCode);

		$this->output->set_content_type('application/json');

		if ($row) {
			echo json_encode([
				'LotCode'       => $row->LotCode,
				'LotName'     => $row->LotName,
				'GodownName' => $row->GodownID,
				'ChamberName' => $row->ChamberID,
				'StackName' => $row->StackID,
				'Length' => $row->length,
				'Width' => $row->width,
				'Height' => $row->height,
				'Margin' => $row->margin,
				'TotalArea' => $row->total_area,
				'UtilizeArea' => $row->utilize_area,
				'volume' => $row->volume,
				'Capacity' => $row->capacity,
				'IsActive' => $row->IsActive
			]);
		} else {
			echo json_encode(null);
		}
	}

	// Get Chambers filtered by GodownID
public function GetChambersByGodown()
{
    $GodownID = $this->input->post('GodownID');
    $chambers = $this->LotMaster_Model->get_ChambersByGodown($GodownID);
    $this->output->set_content_type('application/json');
    echo json_encode($chambers);
}

// Get Stacks filtered by GodownID + ChamberID
public function GetStacksByGodownAndChamber()
{
    $GodownID  = $this->input->post('GodownID');
    $ChamberID = $this->input->post('ChamberID');
    $stacks    = $this->LotMaster_Model->get_StacksByGodownAndChamber($GodownID, $ChamberID);
    $this->output->set_content_type('application/json');
    echo json_encode($stacks);
}


	/* Save New  Lot / ajax */
	public function SaveLotMaster()
	{
		
		$LotCode = $this->input->post('LotCode');
		$LotName = $this->input->post('LotName');
		if ($LotCode === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Lot Code cannot be empty'
			]);
			exit;
		}
		if ($LotName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Lot Name cannot be empty'
			]);
			exit;
		}

		// Check duplicate
		$this->db->where('LotName', $LotName);
		$exists = $this->db->get('LotMaster')->row();

		if ($exists) {
			echo json_encode([
				'status' => false,
				'message' => 'Lot Name already exists'
			]);
			return;
		}

		$requiredDropdowns = [
			'GodownName'  => 'Please select Godown Name',
			'ChamberName' => 'Please select Chamber Name',
			'StackName' => 'Please select Stack Name'
		];

		foreach ($requiredDropdowns as $field => $errorMsg) {
			$value = $this->input->post($field);
			if (empty($value) || $value == '0') {
				echo json_encode([
					'status' => false,
					'message' => $errorMsg
				]);
				exit;
			}
		}

		$data = array(
			'LotCode' => strtoupper($this->input->post('LotCode')),
			'LotName' => strtoupper($this->input->post('LotName')),
			'PlantID' => $this->session->userdata('root_company'),
			'GodownID' => $this->input->post('GodownName'),
			'ChamberID' => $this->input->post('ChamberName'),
			'StackID' => $this->input->post('StackName'),
			'length' => $this->input->post('length'),
			'width' => $this->input->post('Width'),
			'height' => $this->input->post('Height'),
			'margin' => $this->input->post('Margin'),
			'total_area' => $this->input->post('TotalArea'),
			'utilize_area' => $this->input->post('UtilizeArea'),
			'volume' => $this->input->post('Volume'),
			'capacity' => $this->input->post('Capacity'),
			'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
			'IsActive' => $this->input->post('IsActive'),
		);
		// echo '<pre>';
		// echo print_r($data);
		// echo '</pre>';
		// exit;
		$LotMaster  = $this->LotMaster_Model->SaveLotMaster($data);
		echo json_encode($LotMaster);
	}



	/* Update Exiting Lot / ajax */
	public function UpdateLotMaster()
	{
		$LotCode = $this->input->post('LotCode');
		$LotName = $this->input->post('LotName');

		if ($LotName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Lot Name cannot be empty'
			]);
			exit;
		}
		$data = array(
			'LotName' => strtoupper($this->input->post('LotName')),
			'GodownID' => $this->input->post('GodownName'),
			'ChamberID' => $this->input->post('ChamberName'),
			'StackID' => $this->input->post('StackName'),
			'length' => $this->input->post('length'),
			'width' => $this->input->post('Width'),
			'height' => $this->input->post('Height'),
			'margin' => $this->input->post('Margin'),
			'total_area' => $this->input->post('TotalArea'),
			'utilize_area' => $this->input->post('UtilizeArea'),
			'volume' => $this->input->post('Volume'),
			'capacity' => $this->input->post('Capacity'),
			'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
			'IsActive' => $this->input->post('IsActive'),
		);
		$LotMaster  = $this->LotMaster_Model->UpdateLotMaster($data, $LotCode);
		echo json_encode($LotMaster);
	}
}
