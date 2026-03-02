<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ItemType extends AdminController
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ItemType_Model');
	}
	public function index()
	{
		if (!has_permission_new('ItemType', '', 'view')) {
			access_denied('Item Type Master');
		}
		$data['table_data'] = $this->ItemType_Model->get_ItemTypeMaster_data();

		// $data['item'] = $this->ItemCategory_Model->get_ItemTypeMaster_data();

		$this->load->view('admin/ItemType/AddEditItemType.php', $data);
	}

	// Get the Item Type Master Details By ID
	public function GetItemTypeMasterDetailByID()
	{
		$ItemTypeName = $this->input->post('ItemTypeName');
		$row = $this->ItemType_Model->getItemTypeMasterDetails($ItemTypeName);

		$this->output->set_content_type('application/json');

		if ($row) {
			echo json_encode([
				'ItemTypeName' => $row->ItemTypeName,
				'IsActive' => $row->IsActive
			]);
		} else {
			echo json_encode(null);
		}
	}


	/* Save New  Item Type / ajax */
	public function SaveItemTypeMaster()
	{
		
		$ItemTypeName = $this->input->post('ItemTypeName');
		if ($ItemTypeName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Item Type Name cannot be empty'
			]);
			exit;
		}

		$data = array(
			'ItemTypeName' => strtoupper($this->input->post('ItemTypeName')),
			'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
			'IsActive' => $this->input->post('IsActive'),
		);
		$ItemTypeMaster  = $this->ItemType_Model->SaveItemTypeMaster($data);
		echo json_encode($ItemTypeMaster);
	}



	/* Update Exiting Lot / ajax */
	public function UpdateItemTypeMaster()
	{
		$ItemTypeName = $this->input->post('ItemTypeName');
		if ($ItemTypeName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Item Type Name cannot be empty'
			]);
			exit;
		}
		$data = array(
			'ItemTypeName' => strtoupper($this->input->post('ItemTypeName')),
			'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
			'IsActive' => $this->input->post('IsActive'),
		);
		$ItemTypeMaster  = $this->ItemType_Model->UpdateItemTypeMaster($data, $ItemTypeName);
		echo json_encode($ItemTypeMaster);
	}
}
