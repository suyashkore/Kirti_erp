<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ItemCategory extends AdminController
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ItemCategory_Model');
	}
	public function index()
	{
		if (!has_permission_new('ItemCategory', '', 'view')) {
			access_denied('Item Category Master');
		}
		$data['table_data'] = $this->ItemCategory_Model->get_ItemCategoryMaster_data();

		$data['item'] = $this->ItemCategory_Model->get_ItemTypeMaster_data();

		$this->load->view('admin/ItemCategory/AddEditItemCategory.php', $data);
	}

	// Get the Item Category Master Details By ID
	public function GetItemCategoryMasterDetailByID()
	{
		$CategoryCode = $this->input->post('CategoryCode');
		$row = $this->ItemCategory_Model->getItemCategoryMasterDetails($CategoryCode);

		$this->output->set_content_type('application/json');

		if ($row) {
			echo json_encode([
				'Prefix'=>$row->Prefix,
				'CategoryCode' => $row->CategoryCode,
				'CategoryName' => $row->CategoryName,
				'ItemType' => $row->ItemType,
				'IsActive' => $row->IsActive
			]);
		} else {
			echo json_encode(null);
		}
	}


	public function GetItemCategoryMasterDetailByPrefix()
{
    $prefix = trim($this->input->post('Prefix'));

    if ($prefix === '') {
        echo json_encode(['exists' => false]);
        return;
    }

    $this->db->select('Prefix');
    $this->db->from('tblItemCategoryMaster');
    $this->db->where('Prefix', $prefix);
    $this->db->limit(1);

    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        // PREFIX EXISTS
        echo json_encode([
            'exists' => true,
            'message' => 'Prefix already present'
        ]);
    } else {
        // PREFIX NOT EXISTS
        echo json_encode(['exists' => false]);
    }
}



	/* Save New  Lot / ajax */
	public function SaveItemCategoryMaster()
	{
		$Prefix = $this->input->post('Prefix');
		$CategoryCode = $this->input->post('CategoryCode');
		$CategoryName = $this->input->post('CategoryName');
		if ($Prefix === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Prefix cannot be empty'
			]);
			exit;
		}
		if ($CategoryCode === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Item Category Code cannot be empty'
			]);
			exit;
		}
		if ($CategoryName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Item Category Name cannot be empty'
			]);
			exit;
		}

		$requiredDropdowns = [
			'ItemType' => 'Please select Item Type'
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
			'Prefix' => strtoupper($this->input->post('Prefix')),
			'CategoryCode' => strtoupper($this->input->post('CategoryCode')),
			'CategoryName' => strtoupper($this->input->post('CategoryName')),
			'ItemType' => $this->input->post('ItemType'),
			'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
			'IsActive' => $this->input->post('IsActive'),
		);
		$ItemCategoryMaster  = $this->ItemCategory_Model->SaveItemCategoryMaster($data);
		echo json_encode($ItemCategoryMaster);
	}



	/* Update Exiting Lot / ajax */
	public function UpdateItemCategoryMaster()
	{
		$CategoryCode = $this->input->post('CategoryCode');
		$CategoryName = $this->input->post('CategoryName');

		if ($CategoryName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Category Name cannot be empty'
			]);
			exit;
		}
		$data = array(
			'CategoryName' => strtoupper($this->input->post('CategoryName')),
			'ItemType' => $this->input->post('ItemType'),
			'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
			'IsActive' => $this->input->post('IsActive'),
		);
		$ItemCategoryMaster  = $this->ItemCategory_Model->UpdateItemCategoryMaster($data, $CategoryCode);
		echo json_encode($ItemCategoryMaster);
	}
}
