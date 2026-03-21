<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Conveyor extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Conveyor_model');
	}

	/* =========================
	* ADD / EDIT PAGE
	* ========================= */
	public function index()
	{
		if (!has_permission_new('Conveyor', '', 'view')) {
			access_denied('Access Denied');
		}
		$data['title'] = 'Conveyor';
		$data['plantlocation'] = $this->Conveyor_model->get_plant_location();

		$this->load->view('admin/Conveyor/ConveyorAddEdit', $data);
	}

	// ===== GET GODOWN DROPDOWN =====
	public function getGodownDropdown()
	{
		$PlantLocation = $this->input->post('PlantLocation');

		if (!$PlantLocation) {
			echo json_encode(['success' => false, 'message' => 'No Plant Location']);
			return;
		}
		$Godown = $this->Conveyor_model->getGodownDropdownByPlantLocation($PlantLocation);

		echo json_encode(['success' => true, 'Godown' => $Godown]);
	}

	// ===== CHECK's CONVEYOR ID EXIT's =====
	public function CheckConveyorIDExit()
	{
		$ConveyorID = $this->input->post('ConveyorID');
		$ConveyorIdDetails  = $this->Conveyor_model->CheckConveyorIDExit($ConveyorID);
		echo json_encode($ConveyorIdDetails);
	}

	// ===== GET CONVEYOR DETAILS =====
	public function getConveyorDetails()
	{
		if (!$this->input->post()) {
			echo json_encode(['success' => false, 'message' => 'Invalid request']);
			return;
		}

		$Godown = $this->input->post('Godown');
		$PlantLocation = $this->input->post('PlantLocation');

		if (empty($Godown) || empty($PlantLocation)) {
			echo json_encode(['success' => false, 'message' => 'Invalid request']);
			return;
		}

		$data = $this->Conveyor_model->getConveyorDetails($Godown, $PlantLocation);

		echo json_encode(['success' => true, 'data' => $data]);
	}

	// ===== SAVE CONVEYOR =====
	public function SaveConveyor()
	{
		if (!$this->input->post()) {
			echo json_encode(['success' => false, 'message' => 'Invalid request']);
			return;
		}

		$data = $this->input->post(null, true);
		$PlantLocation = $data['PlantLocation'];
		$Godown = $data['Godown'];
		$ConveyorName = $data['conveyor_name'] ?? [];
		$ConveyorID = $data['conveyor_id'] ?? [];
		$IsActive = $data['IsActive'] ?? [];

		$form_mode = $data['form_mode'] ?? 'add';

		if (empty($PlantLocation) || empty($Godown) || empty($ConveyorName) || empty($ConveyorID)) {
			echo json_encode(['success' => false, 'message' => 'Invalid request']);
			return;
		}
		$insertData = [];

		$data['UserID'] = $this->session->userdata('username');

		$success = $this->Conveyor_model->SaveConveyor($data);
		if ($success) {
			echo json_encode([
				'success' => true,
				'message' => 'Record ' . ($form_mode == 'add' ? 'created' : 'updated') . ' successfully...'
			]);
			die;
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Problem occured while ' . ($form_mode == 'add' ? 'creating' : 'updating') . ' record'
			]);
			die;
		}
	}
}
