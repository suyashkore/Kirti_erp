<?php
defined('BASEPATH') or exit('No direct script access allowed');

class QC_Parameter extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Qcparametermaster_model');
        $this->load->model('Items_model');
		$this->load->model('ItemQCParameter_model');
    }

    /* =========================
     * ADD / EDIT PAGE
     * ========================= */
    public function index()
    {
        $data['title'] = 'QC Parameter Master';
		$data['next_id'] = $this->Qcparametermaster_model->getNextParameterId();
		$data['parameters_list'] = $this->Qcparametermaster_model->getParameters();

        $this->load->view('admin/qc_master/AddEditQcParameter', $data);
    }

	public function GetQcParameterByID()
	{
		$parameterID = $this->input->post('parameterID');
		$data = $this->Qcparametermaster_model->getParameterById($parameterID);
		echo json_encode($data);
	}

	public function SaveQcParameter()
	{
		if ($this->input->post()) {
			$data = $this->input->post(null, true);
			$data['parameter_name'] = trim($data['parameter_name']);

			if ($data['parameter_name'] == '') {
				echo json_encode([
					'success' => false,
					'message' => 'Please enter parameter name'
				]);
				die;
			}

			// Check duplicate
			if ($this->Qcparametermaster_model->checkDuplicate($data['parameter_name'], isset($data['parameter_id']) ? $data['parameter_id'] : null)) {
				echo json_encode([
					'success' => false,
					'message' => 'Parameter name already exists'
				]);
				die;
			}

			if (empty($data['parameter_id'])) {

				$insertData = [
					'ItemParameterName' => $data['parameter_name'],
					'IsActive' => isset($data['isActive']) ? 'Y' : 'N'
				];
				$insertId = $this->Qcparametermaster_model->addParameter($insertData);

				if ($insertId) {
					echo json_encode([
						'success'   => true,
						'message'   => 'Record created successfully...',
						'insert_id' => $insertId
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => 'Problem creating record'
					]);
				}

			}else {
				$id = $data['parameter_id'];
				$insertData = [
					'ItemParameterName' => $data['parameter_name'],
					'IsActive' => $data['isActive']
				];
				$success = $this->Qcparametermaster_model->updateParameter($id, $insertData);

				if ($success) {
					echo json_encode([
						'success' => true,
						'message' => 'Record updated successfully...'
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => 'Problem updating record'
					]);
				}
			}
		}else{
			echo json_encode([
				'success' => false,
				'message' => 'Invalid request'
			]);
		}
	}

	/* =========================
     * ITEM AGAINST QC PARAMETER PAGE
     * ========================= */
	public function item()
	{
		if (!has_permission_new('qc_parameter', '', 'view')) {
			access_denied('Invoice Items');
		}

		$data['items_list'] 	= $this->Items_model->getDropdown('items', 'ItemID, ItemName', ['IsActive' => 'Y'], 'ItemName', 'ASC');

		$data['parameters_list'] = $this->Qcparametermaster_model->getParameterDropdown();

		$data['title'] = "Item QC Parameter";
		$this->load->view('admin/qc_master/ItemWithQcParameter', $data);

	}

	public function SaveQcParameterItem(){
		$UserID = $this->session->userdata('username');
		if ($this->input->post()) {
			$data = $this->input->post(null, true);
			$validate = ['form_mode', 'item_id'];
			foreach ($validate as $field) {
				if (empty($data[$field])) {
					echo json_encode([
						'success' => false,
						'message' => 'Invalid request'
					]);
					die;
				}
				$data[$field] = trim($data[$field]);
			}
			if(!empty($data['parameter_id']) && count($data['parameter_id']) > 0){
				$data['UserID2'] = $UserID;
				$success = $this->ItemQCParameter_model->saveBatch($data);
				if ($success) {
					echo json_encode([
						'success' => true,
						'message' => 'Record '.($data['form_mode'] == 'add' ? 'created' : 'updated').' successfully...'
					]);
					die;
				} else {
					echo json_encode([
						'success' => false,
						'message' => 'Problem '.($data['form_mode'] == 'add' ? 'creating' : 'updating').' record'
					]);
					die;
				}
			}
		}else{
			echo json_encode([
				'success' => false,
				'message' => 'Invalid request'
			]);
			die;
		}
	}

	public function GetQcParameterByItemID(){
		$itemID = $this->input->post('itemID');
		$data = $this->ItemQCParameter_model->getByItemID($itemID);
		echo json_encode($data);
	}
}
