<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Accounts_master extends AdminController
{
	private $not_importable_fields = ['id'];
	public function __construct()
	{
		parent::__construct();
		$this->load->model('clients_model');
		$this->load->model('accounts_master_model');
		$this->load->model('departments_model');
	}

	//====================== VERYFIED CODE =========================================

	/* Add / edit account Main group  */
	public function ActMainGroup()
	{
		if (!has_permission_new('account_main_groups', '', 'view')) {
			access_denied('Invoice Items');
		}

		$data['title'] = "Add/Edit Account Main Group";
		$data['account_group_mov'] = $this->accounts_master_model->GetActMovement();
		$data['table_data'] = $this->accounts_master_model->GetActMainGroupData();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$data['lastId'] = $this->accounts_master_model->GetNextAccountMainGroupID();
		$this->load->view('admin/accounts_master/AddEditMainGroup', $data);
	}

	public function get_account_Maingroup_details()
	{
		$accountID = $this->input->post('MainGroupID');
		$row = $this->accounts_master_model->get_account_Maingroup_details($accountID);
		if ($row) {
			echo json_encode([
				'MainGroupID'       => $row->ActGroupID,
				'MainGroupName'     => $row->ActGroupName,
				'GroupType' => $row->ActGroupTypeID,
				'Movement' => $row->ActGroupMovementID,
				'IsActive' => $row->IsActive,
				'IsEditYN'      => $row->IsEditYN

			]);
		} else {
			echo json_encode(null);
		}
	}

	public function get_accounts_main_group()
	{
		$postData = $this->input->post();
		$data = $this->accounts_master_model->GetMainAccountList($postData);
		echo json_encode($data);
	}

	/* Save New Main Group / ajax */
	public function SaveMainGroup()
	{
		if (!has_permission_new('MainGroup', '', 'create')) {
			access_denied('Invoice Items');
		}

		$MainGroupName = $this->input->post('MainGroupName');
		if ($MainGroupName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Main Group Name cannot be empty'
			]);
			exit;
		}
		$requiredDropdowns = [
			'GroupType'  => 'Please select Group Type',
			'Movement'  => 'Please select Movement'
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
			'ActGroupID' => $this->input->post('MainGroupID'),
			'ActGroupName' => strtoupper($this->input->post('MainGroupName')),
			'ActGroupTypeID' => $this->input->post('GroupType'),
			'ActGroupMovementID' => $this->input->post('Movement'),
			'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
			'IsActive' => $this->input->post('IsActive'),
			'IsEditYN' => 'Y'

		);
		$AccountGroup  = $this->accounts_master_model->SaveMainGroup($data);
		echo json_encode($AccountGroup);
	}

	/* Update Exiting Main Group / ajax */
	public function UpdateMainGroup()
	{
		if (!has_permission_new('MainGroup', '', 'edit')) {
			access_denied('Invoice Items');
		}

		$MainGroupID = $this->input->post('MainGroupID');
		$MainGroupName = $this->input->post('MainGroupName');
		if ($MainGroupName === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Main Group Name cannot be empty'
			]);
			exit;
		}

		// Duplicate name check (EXCEPT same ID)
		$this->db->where('ActGroupName', $MainGroupName);
		$this->db->where('ActGroupID !=', $MainGroupID);
		$exists = $this->db->get('AccountMainGroup')->row();

		if ($exists) {
			echo json_encode([
				'status' => false,
				'message' => 'Main Group Name already exists'
			]);
			exit;
		}

		$requiredDropdowns = [
			'GroupType'  => 'Please select Group Type',
			'Movement'  => 'Please select Movement'
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
			'ActGroupName' => strtoupper($this->input->post('MainGroupName')),
			'ActGroupTypeID' => $this->input->post('GroupType'),
			'ActGroupMovementID' => $this->input->post('Movement'),
			'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
			'IsActive' => $this->input->post('IsActive'),

		);
		$ActGroupID = $this->input->post('MainGroupID');
		$IsEditYN = $this->db
			->select('IsEditYN')
			->from('AccountMainGroup')
			->where('ActGroupID', $ActGroupID)
			->get()
			->row('IsEditYN');
		if ($IsEditYN === 'N') {
			echo json_encode([
				'status'  => false,
				'message' => 'This record is locked and cannot be updated'
			]);
			exit;
		} else {
		$AccountGroupID  = $this->accounts_master_model->UpdateMainGroup($data, $ActGroupID);
		echo json_encode($AccountGroupID);
		}
	}


	/* Sub Group 1 List report page*/
	public function AccountSubGroupList1()
	{
		if (!has_permission_new('AcccountSubGroup1List', '', 'view')) {
			access_denied('Acccount SubGroup 1 List');
		}
		$data['title'] = "Account SubGroup 1 List ";
		$data['AccountMainGroup'] = $this->accounts_master_model->GetMainGroup();
		$data['AccountSubGroupID1'] = $this->accounts_master_model->GetAccountSubGroupID1_List();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$this->load->view('admin/accounts_master/AccountSubGroupList1', $data);
	}

	// Load Accountsub group 1 list by ajax
	public function load_subgroup1_data()
	{
		$main_group = $this->input->post('main_group');
		$AccountSubGroupID1 = $this->accounts_master_model->GetAccountSubGroupID1_List($main_group);
		$html = '';
		foreach ($AccountSubGroupID1 as $key => $value) {
			$html .= '<tr>';
			$html .= '<td>' . $value["SubActGroupID1"] . '</td>';
			$html .= '<td>' . $value["SubActGroupName"] . '</td>';
			$html .= '<td>' . $value["ActGroupName"] . '</td>';
			$html .= '</tr>';
		}
		echo json_encode($html);
	}
	/* Export Account Sub group list 1 */
	public function ExportAccount_sub_groupList1()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		if ($this->input->post()) {
			$mainGroup = $this->input->post('main_group');
			$data = $this->accounts_master_model->GetAccountSubGroupID1_List($mainGroup);
			$selected_company_details    = $this->accounts_master_model->get_company_detail();

			$writer = new XLSXWriter();
			$company_name = array($selected_company_details->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 5);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);

			$address = $selected_company_details->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 5);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);

			// empty row
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);

			$set_col_tk = [];
			$set_col_tk["SubAccountGroupID"] =  'AccountSubGroupID';
			$set_col_tk["SubAccountGroupName"] = 'AccountSubGroupName';
			$set_col_tk["MainGroup"] = 'MainGroup';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$i = 1;
			foreach ($data as $k => $value) {
				$list_add = [];
				$list_add[] = $value["ActGroupID"];
				$list_add[] = $value["SubActGroupName"];
				$list_add[] = $value["ActGroupName"];;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'AccountSubGroupList1.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}


	/* Sub Group 2 List report page*/
	public function AccountSubGroupList2()
	{
		if (!has_permission_new('AcccountSubGroup2List', '', 'view')) {
			access_denied('AcccountSubGroup 2 List');
		}
		$data['title'] = "Account SubGroup 2 List ";
		$data['AccountMainGroup'] = $this->accounts_master_model->GetMainGroup();
		// $data['AccountSubGroupID2'] = $this->accounts_master_model->GetAccountSubGroupID2_List();

		$data['AccountMainGroup1'] = $this->accounts_master_model->GetSubGroupList();
		$data['AccountSubGroupID2'] = $this->accounts_master_model->GetAllAccountSubGroupID2_List();
		// print_r($data['AccountSubGroupID2']);die;
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$this->load->view('admin/accounts_master/AccountSubGroupList2', $data);
	}

	// Load Accountsub group 2 list by ajax
	public function load_subgroup2_data()
	{
		$main_group = $this->input->post('main_group');
		$sub_group1 = $this->input->post('sub_group1');
		$AccountSubGroupID1 = $this->accounts_master_model->GetAllAccountSubGroupID2_List($main_group, $sub_group1);
		$html = '';
		foreach ($AccountSubGroupID1 as $key => $value) {
			$html .= '<tr>';
			$html .= '<td>' . $value["SubActGroupID"] . '</td>';
			$html .= '<td>' . $value["SubActGroupName"] . '</td>';
			$html .= '<td>' . $value["SubGroup1"] . '</td>';
			$html .= '<td>' . $value["MainGroup"] . '</td>';
			$html .= '</tr>';
		}
		echo json_encode($html);
	}

	// Load Accountsub group 1 list by ajax
	public function GetSubGroupList1ByMainGroup()
	{
		$main_group = $this->input->post('main_group');
		$AccountSubGroupID1 = $this->accounts_master_model->GetAccountSubGroupID1($main_group);
		echo json_encode($AccountSubGroupID1);
	}

	// Load Accountsub group 2 list by ajax
	public function GetSubGroupList2BySubGroup1()
	{
		$SubGroup1 = $this->input->post('SubGroup1');
		$AccountSubGroupID2 = $this->accounts_master_model->GetAccountSubGroupID2_List($main_group = "", $SubGroup1);
		echo json_encode($AccountSubGroupID2);
	}

	/* Export Account Sub group list 2 */
	public function ExportAccount_sub_groupList2()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		if ($this->input->post()) {
			$mainGroup = $this->input->post('main_group');
			$sub_group1 = $this->input->post('sub_group1');
			$data = $this->accounts_master_model->GetAllAccountSubGroupID2_List($mainGroup, $sub_group1);
			$selected_company_details    = $this->accounts_master_model->get_company_detail();

			$writer = new XLSXWriter();
			$company_name = array($selected_company_details->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 5);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);

			$address = $selected_company_details->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 5);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);

			// empty row
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);

			$set_col_tk = [];
			$set_col_tk["SubAccountGroupID"] =  'Account SubGroupID';
			$set_col_tk["SubAccountGroupName"] = 'Account SubGroupName';
			$set_col_tk["SubAccountGroupName1"] = 'Account SubGroup1 Name';
			$set_col_tk["MainGroup"] = 'MainGroup';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$i = 1;
			foreach ($data as $k => $value) {
				$list_add = [];
				$list_add[] = $value["SubActGroupID"];
				$list_add[] = $value["SubActGroupName"];
				$list_add[] = $value["SubGroup1"];
				$list_add[] = $value["MainGroup"];;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'AccountSubGroupList2.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}

	/* List account group  */
	public function AccountMainGroupList()
	{
		if (!has_permission('AcccountGroupList', '', 'view')) {
			access_denied('AcccountGroupList');
		}
		$data['title'] = "Account Group List";
		$data['account_group_mov'] = $this->accounts_master_model->GetActMovement();
		$data['account_group_table'] = $this->accounts_master_model->GetActMainGroupData();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$this->load->view('admin/accounts_master/AccountGroupList', $data);
	}

	public function Account_main_group()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		if ($this->input->post()) {
			$data = $this->accounts_master_model->GetActMainGroupData();
			$selected_company_details    = $this->accounts_master_model->get_company_detail();

			$writer = new XLSXWriter();
			$company_name = array($selected_company_details->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 5);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);

			$address = $selected_company_details->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 5);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);

			// empty row
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);

			$set_col_tk = [];
			$set_col_tk["AccountGroup"] =  'AccountGroupID';
			$set_col_tk["AccountDescription"] = 'AccountDescription';
			$set_col_tk["GroupType"] = 'GroupType';
			$set_col_tk["Movement"] = 'Movement';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			$i = 1;
			foreach ($data as $k => $value) {
				$list_add = [];
				$list_add[] = $value["ActGroupID"];
				$list_add[] = $value["ActGroupName"];
				if ($value["ActGroupTypeID"] == "A") {
					$groupType = "Assets";
				} else {
					$groupType = "Liability";
				}
				$list_add[] = $groupType;
				if ($value["ActGroupMovementID"] == "B") {
					$movement = "BALANCE SHEET";
				} elseif ($value["ActGroupMovementID"] == "P") {
					$movement = "PROFIT & LOSS A/C";
				} elseif ($value["ActGroupMovementID"] == "T") {
					$movement = "TRADING A/C";
				}
				$list_add[] = $movement;
				$writer->writeSheetRow('Sheet1', $list_add);
			}
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'AccountMainGroup.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}

	/*  Accounts Head List */
	public function AccountHeadList($accountId = '')
	{
		if (!has_permission_new('account_head', '', 'view')) {
			access_denied('Invoice Items');
		}

		$data['title'] = "Account Head List";
		//table code start here
		$data['MainGroup'] = $this->accounts_master_model->GetActMainGroupData();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$this->load->view('admin/accounts_master/AccountHeadList', $data);
	}

	//load table in here
	public function loadAccountHead_data()
	{
		$data = $this->accounts_master_model->get_accounts_list($this->input->post());

		$selected_company = $this->session->userdata('root_company');
		$MainGroup = $this->input->post('MainGroup');
		$SubGroup1 = $this->input->post('SubGroup1');
		$SubGroup2 = $this->input->post('SubGroup2');
		$status = $this->input->post('status');
		if ($status == 1) {
			$status = 'Yes';
		}
		if ($SubGroup2 != '') {
			$SubGroup2Name = GetSubGroup2($SubGroup2, $selected_company);
			$SubGroup2Name_header = $SubGroup2Name->SubActGroupName;
		} else {
			$SubGroup2Name_header = '';
		}

		if ($SubGroup1 != '') {
			$SubGroup1Name = GetSubGroup1($SubGroup1, $selected_company);
			$SubGroup1Name_header = $SubGroup1Name->SubActGroupName;
		} else {
			$SubGroup1Name_header = '';
		}
		if ($MainGroup != '') {
			$MainGroupName = GetMainGroupName($MainGroup, $selected_company);
			$MainGroupName_header = $MainGroupName->ActGroupName;
		} else {
			$MainGroupName_header = '';
		}

		$html = '';
		foreach ($data as $value) {
			$html .= '<tr>';

			$html .= '<td>' . $value['AccountID'] . '</td>';
			if (has_permission_new('account_head', '', 'edit')) {
				$account_name = '<a href="' . admin_url('accounts_master/edit_account_head/' . $value['AccountID']) . '">' . $value['company'] . '</a>';
			} else {
				$account_name = $value['company'];
			}
			$html .= '<td>' . $value['company'] . '</td>';
			$html .= '<td>' . $value['SubActGroupName2'] . '</td>';
			$html .= '<td>' . $value['SubActGroupName1'] . '</td>';
			$html .= '<td>' . $value['MainGroupName'] . '</td>';
			if ($value['active'] == "1") {
				$status = "Yes";
			} else {
				$status = "No";
			}
			$html .= '<td>' . $status . '</td>';
			$html .= '</tr>';
		}
		$data_array = array('html' => $html, 'SubGroup2' => $SubGroup2Name_header, 'SubGroup1' => $SubGroup1Name_header, 'MainGroup' => $MainGroupName_header);
		echo json_encode($data_array);
	}

	public function export_Account_Head()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		if ($this->input->post()) {

			$data = $this->accounts_master_model->get_accounts_list($this->input->post());
			$selected_company_details    = $this->accounts_master_model->get_company_detail();
			$selected_company = $this->session->userdata('root_company');
			$MainGroup = $this->input->post('MainGroup');
			$SubGroup1 = $this->input->post('SubGroup1');
			$SubGroup2 = $this->input->post('SubGroup2');
			$status = $this->input->post('status');
			if ($status == 1) {
				$status = 'Yes';
			} else {
				$status = 'No';
			}
			if ($SubGroup2 != '') {
				$SubGroup2Name = GetSubGroup2($SubGroup2, $selected_company);
				$SubGroup2Name_header = $SubGroup2Name->SubActGroupName;
			} else {
				$SubGroup2Name_header = '';
			}

			if ($SubGroup1 != '') {
				$SubGroup1Name = GetSubGroup1($SubGroup1, $selected_company);
				$SubGroup1Name_header = $SubGroup1Name->SubActGroupName;
			} else {
				$SubGroup1Name_header = '';
			}
			if ($MainGroup != '') {
				$MainGroupName = GetMainGroupName($MainGroup, $selected_company);
				$MainGroupName_header = $MainGroupName->ActGroupName;
			} else {
				$MainGroupName_header = '';
			}
			$writer = new XLSXWriter();

			$company_name = array($selected_company_details->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 6);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);

			$address = $selected_company_details->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 6);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);

			$msg = "Accounts List Filter MainGroupID: " . $MainGroupName_header . ", SubGroup 1 Name: " . $SubGroup1Name_header . ", SubGroup 2 Name: " . $SubGroup2Name_header;
			$filter = array($msg);
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 6);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter);

			// empty row
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);


			$set_col_tk = [];
			$set_col_tk["AccountID"] =  'AccountID';
			$set_col_tk["Account Name"] = 'Account Name';
			$set_col_tk["Subgroup"] = 'SubGroup 2';
			$set_col_tk["Subgroup"] = 'SubGroup 1';
			$set_col_tk["Main Group"] = 'Main Group';
			$set_col_tk["Blocked"] = 'Blocked';
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);


			foreach ($data as $k => $value) {

				$list_add = [];
				$list_add[] = $value["AccountID"];
				$list_add[] = $value["company"];
				$list_add[] = $value["SubActGroupName2"];
				$list_add[] = $value["SubActGroupName1"];
				$list_add[] = $value["MainGroupName"];
				$list_add[] = $value["Blockyn"];
				$writer->writeSheetRow('Sheet1', $list_add);
			}


			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'AccountHeadList.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}

	/* Update Exiting ItemID / ajax */
	public function UpdateAccountID()
	{
		$UserID = $this->session->userdata('username');
		if ($this->input->post('tax') == "") {
			$tax = null;
		} else {
			$tax = $this->input->post('tax');
		}

		if ($this->input->post('hsn_code') == "") {
			$hsn = null;
		} else {
			$hsn = $this->input->post('hsn_code');
		}
		$dataClient = array(
			'company' => $this->input->post('company'),
			'ActGroupID' => $this->input->post('MainAccount_Group'),
			'SubActGroupID1' => $this->input->post('SubAccount_Group1'),
			'SubActGroupID' => $this->input->post('SubAccount_Group2'),
			'Blockyn' => $this->input->post('Blockyn'),
			'ifsc_code' => $this->input->post('ifsc'),
			'bank_add' => $this->input->post('bankaddress'),
			'bank_name' => $this->input->post('bankname'),
			'acc_name' => $this->input->post('accname'),
			'acc_no' => $this->input->post('accountno'),
			'acc_type' => $this->input->post('accounttype'),
			'closing_bal' => $this->input->post('closing_bal'),
			'ad_code' => $this->input->post('ad_code'),
			'payment_term' => $this->input->post('payment_term'),
			'hsn_code' => $hsn,
			'tax' => $tax,
			'StartDate' => to_sql_date($this->input->post('StartDate')),
			"UserID2" => $UserID,
			"Lupdate" => date('Y-m-d H:i:s')
		);
		$AccountID = $this->input->post('AccountID');
		$BAL1 = $this->input->post('BAL1');
		$AccountDetails         = $this->accounts_master_model->UpdateAccountID($dataClient, $AccountID, $BAL1);
		echo json_encode($AccountDetails);
	}

	/* Save New ItemID / ajax */
	public function SaveHeadAccountID()
	{
		if ($this->input->post('tax') == "") {
			$tax = null;
		} else {
			$tax = $this->input->post('tax');
		}

		if ($this->input->post('hsn_code') == "") {
			$hsn = null;
		} else {
			$hsn = $this->input->post('hsn_code');
		}
		$FY = $this->session->userdata('finacial_year');
		$prefix = "L";
		$next_cust_numberval = (int) get_option('next_account_ledger_number');
		$next_cust_number = $prefix . str_pad($next_cust_numberval, 6, '0', STR_PAD_LEFT);
		$NewAccountID = $next_cust_number;

		$data = array(
			'AccountID' => strtoupper($NewAccountID),
			'company' => $this->input->post('company'),
			'TType' => 2,
			'ActGroupID' => $this->input->post('MainAccount_Group'),
			'SubActGroupID1' => $this->input->post('SubAccount_Group1'),
			'SubActGroupID' => $this->input->post('SubAccount_Group2'),
			'Blockyn' => $this->input->post('Blockyn'),
			'ifsc_code' => $this->input->post('ifsc'),
			'bank_add' => $this->input->post('bankaddress'),
			'bank_name' => $this->input->post('bankname'),
			'acc_name' => $this->input->post('accname'),
			'acc_no' => $this->input->post('accountno'),
			'acc_type' => $this->input->post('accounttype'),
			'closing_bal' => $this->input->post('closing_bal'),
			'ad_code' => $this->input->post('ad_code'),
			'payment_term' => $this->input->post('payment_term'),
			'hsn_code' => $hsn,
			'tax' => $tax,
			'StartDate' => to_sql_date($this->input->post('StartDate')),
		);
		$BAL1 = $this->input->post('BAL1');
		$AccountID = strtoupper($NewAccountID);
		$AccountDetails  = $this->accounts_master_model->SaveAccountID($data, $BAL1, $AccountID);
		echo json_encode($AccountDetails);
	}

	/*================== Below this Mixed Code =======================*/

	/* Add / edit account Sub group  */
	public function SubGroup1()
	{
		if (!has_permission_new('account_subgroups1', '', 'view')) {
			access_denied('Invoice Items');
		}
		$data['title'] = "Add/Edit Account SubGroup1";
		$data['AccountMainGroup'] = $this->accounts_master_model->GetMainGroup();
		$InitialMainGroup = "10000";
		$data['AccountSubGroupID1'] = $this->accounts_master_model->GetAccountSubGroupID1_List();
		$data['lastId'] = $this->accounts_master_model->GetNextAccountSunGroupID1();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$this->load->view('admin/accounts_master/AddEditSubGroup1', $data);
	}

	/* Add / edit account Sub group  */
	public function SubGroup2()
	{
		if (!has_permission_new('account_subgroups2', '', 'view')) {
			access_denied('Invoice Items');
		}
		$data['title'] = "Add/Edit Account SubGroup2";
		$data['AccountMainGroup'] = $this->accounts_master_model->GetMainGroup();
		$data['AccountMainGroup1'] = $this->accounts_master_model->GetSubGroupList();

		$InitialMainGroup = "10000";
		$data['AccountSubGroupID1'] = $this->accounts_master_model->GetAccountSubGroupID1($InitialMainGroup);
		$InitialAccountSubGroupID1 = "100000";
		$data['lastId'] = $this->accounts_master_model->GetNextAccountSunGroupID2();

		$data['AccountSubGroupID2'] = $this->accounts_master_model->GetAllAccountSubGroupID2();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$this->load->view('admin/accounts_master/AddEditSubGroup2', $data);
	}

	/* Add / edit account Sub group  */
	public function SubGroup()
	{
		if (!has_permission_new('account_subgroups', '', 'view')) {
			access_denied('Invoice Items');
		}
		$data['title'] = "Add/Edit Account SubGroup3";
		$data['AccountMainGroup'] = $this->accounts_master_model->GetMainGroup();
		$data['AccountMainGroup2'] = $this->accounts_master_model->GetAllAccountSubGroupID2();
		$InitialMainGroup = "10000";
		$data['AccountSubGroupID1'] = $this->accounts_master_model->GetAccountSubGroupID1($InitialMainGroup);
		$InitialAccountSubGroupID1 = "100000";
		$data['AccountSubGroupID2'] = $this->accounts_master_model->GetAccountSubGroupID2($InitialAccountSubGroupID1);
		$data['NextAccountGroupID3'] = $this->accounts_master_model->GetNextAccountSunGroupID3();
		$data['AccountSubGroupID3'] = $this->accounts_master_model->GetAllAccountSubGroupID();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$this->load->view('admin/accounts_master/AddEditSubGroup', $data);
	}

	// Get Next AccountSubGroupID3 
	public function NextAccountGroupID3()
	{
		$account_data = $this->accounts_master_model->GetNextAccountSunGroupID33();
		echo json_encode($account_data);
	}
	// Get Next AccountSubGroupID3 
	public function NextAccountGroupID2()
	{
		$account_data = $this->accounts_master_model->GetNextAccountSunGroupID2();
		echo json_encode($account_data);
	}

	// Get Next AccountSubGroupID3 
	public function NextAccountGroupID1()
	{
		$account_data = $this->accounts_master_model->GetNextAccountSunGroupID1();
		echo json_encode($account_data);
	}

	// Get Next AccountSubGroupID
	public function NextAccountGroupID()
	{
		$account_data = $this->accounts_master_model->GetNextAccountSunGroupID();
		echo json_encode($account_data);
	}

	// Get AccountSubGroupID by AccountMain GroupID Using Ajax 
	public function GetAccountSubGroupID1()
	{
		$AccountMainGeoupID = $this->input->post('AccountMainGroupID');
		$account_data = $this->accounts_master_model->GetAccountSubGroupID1($AccountMainGeoupID);
		echo json_encode($account_data);
	}

	// Get AccountSubGroupID2 by AccountGroupID1 Using Ajax 
	public function GetAccountSubGroupID2()
	{
		$AccountSubGroupID1 = $this->input->post('AccountSubGroupID1');
		$account_data = $this->accounts_master_model->GetAccountSubGroupID2($AccountSubGroupID1);
		echo json_encode($account_data);
	}


	// Get AccountSubGroupID2 by AccountGroupID3 Using Ajax 
	public function GetAccountSubGroupID3()
	{
		$AccountSubGroupID2 = $this->input->post('AccountSubGroupID2');
		$account_data = $this->accounts_master_model->GetAccountSubGroupID3($AccountSubGroupID2);
		echo json_encode($account_data);
		// var_dump($account_data);
	}


	/* Save New Sub Group / ajax */
	public function SaveSubGroup1()
	{
		if (!has_permission_new('account_subgroups1', '', 'create')) {
			access_denied('Invoice Items');
		}

		$SubGroup1ID = $this->input->post('SubGroup1ID');
		$SubGroupName1 = $this->input->post('SubGroupName1');
		if ($SubGroup1ID === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group Code cannot be empty'
			]);
			exit;
		}
		if ($SubGroupName1 === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group Name cannot be empty'
			]);
			exit;
		}
		$requiredDropdowns = [
			'MainGroup'  => 'Please select Main Group'
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
			'SubActGroupID1' => $this->input->post('SubGroup1ID'),
			'SubActGroupName' => strtoupper($this->input->post('SubGroupName1')),
			'ActGroupID' => $this->input->post('MainGroup'),
			'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
			'IsActive' => $this->input->post('IsActive'),
			'IsEditYN' => 'Y'

		);
		$AccountSubGroup  = $this->accounts_master_model->SaveSubGroup1($data);
		echo json_encode($AccountSubGroup);
	}


	/* Update Exiting SubGroup / ajax */
	public function UpdateSubGroup1()
	{
		if (!has_permission_new('account_subgroups1', '', 'edit')) {
			access_denied('Invoice Items');
		}
		$SubGroup1ID = $this->input->post('SubGroup1ID');
		$SubGroupName1 = $this->input->post('SubGroupName1');

		if ($SubGroup1ID === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group Code cannot be empty'
			]);
			exit;
		}

		if ($SubGroupName1 === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group Name cannot be empty'
			]);
			exit;
		}

		// Duplicate name check (EXCEPT same ID)
		$this->db->where('SubActGroupName', $SubGroupName1);
		$this->db->where('SubActGroupID1 !=', $SubGroup1ID);
		$exists = $this->db->get('AccountSubGroup1')->row();

		if ($exists) {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group Name already exists'
			]);
			exit;
		}

		$requiredDropdowns = [
			'MainGroup'  => 'Please select Main Group'
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

			'SubActGroupName' => strtoupper($this->input->post('SubGroupName1')),
			'ActGroupID' => $this->input->post('MainGroup'),
			'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
			'IsActive' => $this->input->post('IsActive')
		);
		$SubGroup1ID = $this->input->post('SubGroup1ID');
		$IsEditYN = $this->db
			->select('IsEditYN')
			->from('AccountSubGroup1')
			->where('SubActGroupID1', $SubGroup1ID)
			->get()
			->row('IsEditYN');

		if ($IsEditYN === 'N') {
			echo json_encode([
				'status'  => false,
				'message' => 'This record is locked and cannot be updated'
			]);
			exit;
		} else {
		$itemGroupID                     = $this->accounts_master_model->UpdateSubGroup1($data, $SubGroup1ID);
		echo json_encode($itemGroupID);
		}

		
	}

	/* Save New Sub Group / ajax */
	public function SaveSubGroup()
	{
		$data = array(
			'SubActGroupID' => $this->input->post('SubGroupID'),
			'SubActGroupName' => $this->input->post('SubGroupName'),
			'SubActGroupID2' => $this->input->post('MainGroupID'),
		);
		$AccountSubGroup  = $this->accounts_master_model->SaveSubGroup($data);
		echo json_encode($AccountSubGroup);
	}

	/* Update Exiting SubGroup / ajax */
	public function UpdateSubGroup()
	{
		$data = array(

			'SubActGroupName' => $this->input->post('SubGroupName'),
			'SubActGroupID2' => $this->input->post('MainGroupID'),
			'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
		);
		$SubGroupID = $this->input->post('SubGroupID');
		$itemGroupID                     = $this->accounts_master_model->UpdateSubGroup($data, $SubGroupID);
		echo json_encode($itemGroupID);
	}

	/* Save New Sub Group / ajax */
	public function SaveSubGroup2()
	{

		$SubGroup1ID = $this->input->post('SubGroupID');
		$SubGroupName1 = $this->input->post('SubGroupName');
		$ShortCode = $this->input->post('ShortCode');
		if ($SubGroup1ID === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group 2 Code cannot be empty'
			]);
			exit;
		}
		if ($SubGroupName1 === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group 2 Name cannot be empty'
			]);
			exit;
		}
		if ($ShortCode === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Short Code cannot be empty'
			]);
			exit;
		}
		$requiredDropdowns = [
			'AccountSubGroupID1'  => 'Please select Account Sub Group 1'
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

		if (!has_permission_new('account_subgroups2', '', 'create')) {
			access_denied('Invoice Items');
		}
		$data = array(
			'SubActGroupID' => $this->input->post('SubGroupID'),
			'SubActGroupName' => strtoupper($this->input->post('SubGroupName')),
			'ShortCode' => strtoupper($this->input->post('ShortCode')),
			'SubActGroupID1' => $this->input->post('AccountSubGroupID1'),
			'UserID' => $this->session->userdata('username'),
			'Transdate' => date('Y-m-d H:i:s'),
			'IsEditYN' => 'Y',
			'IsActive' => $this->input->post('IsActive'),
		);
		$GroupFor = $this->input->post('GroupFor');

		if ($GroupFor == 'Account') {
			$data['IsAccountHead'] = 'Y';
		}
		if ($GroupFor == 'Customer') {
			$data['IsCustomer'] = 'Y';
		}
		if ($GroupFor == 'Vendor') {
			$data['IsVendor'] = 'Y';
		}
		if ($GroupFor == 'Staff') {
			$data['IsStaff'] = 'Y';
		}
		if ($GroupFor == 'Broker') {
			$data['IsBroker'] = 'Y';
		}
		if ($GroupFor == 'Transporter') {
			$data['IsTransporter'] = 'Y';
		}
		if ($GroupFor == 'VehicleOwner') {
			$data['IsVehicleOwner'] = 'Y';
		}
		$AccountSubGroup  = $this->accounts_master_model->SaveSubGroup2($data);
		echo json_encode($AccountSubGroup);
	}


	/* Update Exiting SubGroup / ajax */
	public function UpdateSubGroup2()
	{
		if (!has_permission_new('account_subgroups2', '', 'edit')) {
			access_denied('Invoice Items');
		}

		$SubGroup1ID = $this->input->post('SubGroupID');
		$SubGroupName1 = $this->input->post('SubGroupName');
		$ShortCode = $this->input->post('ShortCode');

		if ($SubGroup1ID === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group 2 Code cannot be empty'
			]);
			exit;
		}

		if ($SubGroupName1 === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group 2 Name cannot be empty'
			]);
			exit;
		}
		if ($ShortCode === '') {
			echo json_encode([
				'status' => false,
				'message' => 'Short Code cannot be empty'
			]);
			exit;
		}

		// Duplicate name check (EXCEPT same ID)
		$this->db->where('SubActGroupName', $SubGroupName1);
		$this->db->where('SubActGroupID !=', $SubGroup1ID);
		$exists = $this->db->get('AccountSubGroup2')->row();

		if ($exists) {
			echo json_encode([
				'status' => false,
				'message' => 'Sub Group 2 Name already exists'
			]);
			exit;
		}

		$requiredDropdowns = [
			'AccountSubGroupID1'  => 'Please select Account Sub Group 1'
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

			'SubActGroupName' => strtoupper($this->input->post('SubGroupName')),
			'ShortCode' => strtoupper($this->input->post('ShortCode')),
			'SubActGroupID1' => $this->input->post('AccountSubGroupID1'),
			'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
			'IsActive' => $this->input->post('IsActive'),
		);

		$GroupFor = $this->input->post('GroupFor');

		if ($GroupFor == 'Account') {
			$data['IsAccountHead'] = 'Y';
			$data['IsCustomer'] = 'N';
			$data['IsVendor'] = 'N';
			$data['IsStaff'] = 'N';
			$data['IsBroker'] = 'N';
			$data['IsTransporter'] = 'N';
			$data['IsVehicleOwner'] = 'N';
		}
		if ($GroupFor == 'Customer') {
			$data['IsCustomer'] = 'Y';
			$data['IsAccountHead'] = 'N';
			$data['IsVendor'] = 'N';
			$data['IsStaff'] = 'N';
			$data['IsBroker'] = 'N';
			$data['IsTransporter'] = 'N';
			$data['IsVehicleOwner'] = 'N';
		}
		if ($GroupFor == 'Vendor') {
			$data['IsVendor'] = 'Y';
			$data['IsAccountHead'] = 'N';
			$data['IsCustomer'] = 'N';
			$data['IsStaff'] = 'N';
			$data['IsBroker'] = 'N';
			$data['IsTransporter'] = 'N';
			$data['IsVehicleOwner'] = 'N';
		}
		if ($GroupFor == 'Staff') {
			$data['IsStaff'] = 'Y';
			$data['IsAccountHead'] = 'N';
			$data['IsCustomer'] = 'N';
			$data['IsVendor'] = 'N';
			$data['IsBroker'] = 'N';
			$data['IsTransporter'] = 'N';
			$data['IsVehicleOwner'] = 'N';
		}
		if ($GroupFor == 'Broker') {
			$data['IsBroker'] = 'Y';
			$data['IsAccountHead'] = 'N';
			$data['IsCustomer'] = 'N';
			$data['IsVendor'] = 'N';
			$data['IsStaff'] = 'N';
			$data['IsTransporter'] = 'N';
			$data['IsVehicleOwner'] = 'N';
		}
		if ($GroupFor == 'Transporter') {
			$data['IsTransporter'] = 'Y';
			$data['IsAccountHead'] = 'N';
			$data['IsCustomer'] = 'N';
			$data['IsVendor'] = 'N';
			$data['IsStaff'] = 'N';
			$data['IsBroker'] = 'N';
			$data['IsVehicleOwner'] = 'N';
		}
		if ($GroupFor == 'VehicleOwner') {
			$data['IsVehicleOwner'] = 'Y';
			$data['IsAccountHead'] = 'N';
			$data['IsCustomer'] = 'N';
			$data['IsVendor'] = 'N';
			$data['IsStaff'] = 'N';
			$data['IsBroker'] = 'N';
			$data['IsTransporter'] = 'N';
		}

		$SubGroupID = $this->input->post('SubGroupID');
		$IsEditYN = $this->db
			->select('IsEditYN')
			->from('AccountSubGroup2')
			->where('SubActGroupID', $SubGroupID)
			->get()
			->row('IsEditYN');

		if ($IsEditYN === 'N') {
			echo json_encode([
				'status'  => false,
				'message' => 'This record is locked and cannot be updated'
			]);
			exit;
		} else {
			$itemGroupID = $this->accounts_master_model->UpdateSubGroup2($data, $SubGroupID);
			echo json_encode($itemGroupID);
		}
	}

	/* Add/Edit New Accounts Head */
	public function AddEditAccountHead($accountId = '')
	{
		if (!has_permission_new('account_head', '', 'view')) {
			access_denied('Invoice Items');
		}
		$this->load->model('taxes_model');
		$data['title'] = "Add/Edit Account Head";
		//table code start here
		$filter['TType'] = '2';
		$data['AccountHead'] = $this->accounts_master_model->GetAccountLedger();
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();

		$data['hsn_data']        = $this->accounts_master_model->get_hsn();
		$data['taxes']        = $this->taxes_model->get();
		$data['account_subgroup'] = $this->accounts_master_model->get_subgroup_for_accounting_head();
		$data['account_group'] = $this->accounts_master_model->get_group_for_accounting_head();
		$this->load->view('admin/accounts_master/AddEditAccountHead', $data);
	}

	//=========================== VERYFIED CODE END ================================




	/* Get Account Details by AccountID / ajax */
	public function GetSubGroupOneByMainGroupId()
	{
		$Account_Group = $this->input->post('Account_Group');
		$Account                    = $this->accounts_master_model->GetSubGroupOneByMainGroupId($Account_Group);
		echo json_encode($Account);
	}

	/* Get Account Details by AccountID / ajax */
	public function GetSubGroupTwoBySubAccount_Group1()
	{
		$SubAccount_Group1 = $this->input->post('SubAccount_Group1');
		$Account                    = $this->accounts_master_model->GetSubGroupTwoBySubAccount_Group1($SubAccount_Group1);
		echo json_encode($Account);
	}
	/* Get Account Details by AccountID / ajax */
	public function GetSubGroupBySubAccount_Group2()
	{
		$SubAccount_Group2 = $this->input->post('SubAccount_Group2');
		$Account                    = $this->accounts_master_model->GetSubGroupBySubAccount_Group2($SubAccount_Group2);
		echo json_encode($Account);
	}
	/* Get Account Details by AccountID / ajax */
	public function GetAccountDetailByID()
	{
		$AccountID = $this->input->post('AccountID');
		$Account = $this->accounts_master_model->GetAccountDetails($AccountID);
		echo json_encode($Account);
	}

	public function get_user_list()
	{
		// POST data
		$postData = $this->input->post();
		// Get data
		$data = $this->accounts_master_model->get_user_list($postData);
		echo json_encode($data);
	}

	public function get_no_act_list()
	{
		// POST data
		$postData = $this->input->post();
		// Get data
		$data = $this->accounts_master_model->get_no_act_list($postData);
		$data2 = $this->accounts_master_model->get_no_act_list_for_staff($postData);
		$body_data = $this->accounts_master_model->get_selected_record($postData);
		$html = '';
		$html .= '<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." title="Type in a name" style="float: right;">';
		$html .= '<div class="tableFixHead">';
		$html .= '<table class="table table-striped table-bordered tableFixHead" width="100%" id="no_show_act_table">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th>AccountID</th>';
		$html .= '<th>AccountName</th>';
		$html .= '<th>AllowedTo View</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		foreach ($data as $key => $value) {
			$html .= '<tr>';
			$html .= '<td>' . $value["AccountID"] . '</td>';
			$html .= '<td>' . $value["company"] . '</td>';
			$checked = '';
			foreach ($body_data as $key2 => $value2) {
				if ($value2['AccountID'] == $value["AccountID"]) {
					$checked = 'checked';
				}
			}
			$html .= '<td><input type="checkbox" class="selected_acct" name="selected_acct[]" value="' . $value["AccountID"] . '" ' . $checked . '></td>';
			$html .= '</tr>';
		}
		foreach ($data2 as $key1 => $value1) {
			$html .= '<tr>';
			$html .= '<td>' . $value1["AccountID"] . '</td>';
			$html .= '<td>' . $value1["firstname"] . " " . $value1["lastname"] . '</td>';
			$checked = '';
			foreach ($body_data as $key2 => $value2) {
				if ($value2['AccountID'] == $value1["AccountID"]) {
					$checked1 = 'checked';
				}
			}
			$html .= '<td><input type="checkbox" class="selected_acct" name="selected_acct[]" value="' . $value1["AccountID"] . '" ' . $checked1 . '></td>';
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= "</div>";
		echo json_encode($html);
	}
	public function get_accounts_subgroup2()
	{
		// POST data
		$postData = $this->input->post();
		// Get data
		$data = $this->accounts_master_model->get_accounts_subgroup2($postData);
		echo json_encode($data);
	}
	public function get_accounts_subgroup1()
	{

		// POST data
		$postData = $this->input->post();

		// Get data
		$data = $this->accounts_master_model->get_accounts_subgroup1($postData);

		echo json_encode($data);
	}
	public function get_accounts_subgroup()
	{

		// POST data
		$postData = $this->input->post();

		// Get data
		$data = $this->accounts_master_model->get_accounts_subgroup($postData);

		echo json_encode($data);
	}

	public function get_staff_details()
	{

		$userID = $this->input->post('userID');
		$staff_data = $this->accounts_master_model->get_staff_details($userID);
		echo json_encode($staff_data);
	}


	public function get_account_subgroup_details1()
	{
		$account_subgroupID = $this->input->post('SubGroup1ID');
		$row = $this->accounts_master_model->get_account_subgroup_details1($account_subgroupID);
		$this->output->set_content_type('application/json');
		if ($row) {
			echo json_encode([
				'SubGroup1ID'     => $row->SubActGroupID1,
				'SubGroupName1'     => $row->SubActGroupName,
				'MainGroup'       => $row->ActGroupID,
				'IsActive' => $row->IsActive,
				'IsEditYN'      => $row->IsEditYN

			]);
		} else {
			echo json_encode(null);
		}
	}
	public function get_account_subgroup_details2()
	{
		$account_subgroupID = $this->input->post('account_subgroupID');
		$account_data = $this->accounts_master_model->get_account_subgroup_details2($account_subgroupID);
		echo json_encode($account_data);
	}
	public function get_account_subgroup_details()
	{
		$account_subgroupID = $this->input->post('account_subgroupID');
		$account_data = $this->accounts_master_model->get_account_subgroup_details($account_subgroupID);
		echo json_encode($account_data);
	}


	public function User_master()
	{
		if (!has_permission_new('user_master', '', 'view')) {
			access_denied('Invoice Items');
		}
		if ($this->input->post()) {

			if (!has_permission_new('user_master', '', 'edit')) {
				access_denied('Invoice Items');
			}
			$data = $this->input->post();
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			/*echo "<pre>";
					print_r($data);
				die;*/
			$affected_row = 0;
			$this->db->where('UserID', $data['userid']);
			$this->db->delete(db_prefix() . 'nsaccountmaster');
			if ($this->db->affected_rows()) {
				$affected_row++;
			}
			foreach ($data['selected_acct'] as $id) {
				$permisstion_array = array(
					"PlantID" => $selected_company,
					"AccountID" => $id,
					"UserID" => $data['userid']
				);

				$this->db->insert(db_prefix() . 'nsaccountmaster', $permisstion_array);
				if ($this->db->affected_rows()) {
					$affected_row++;
				}
			}
			if ($data['new_password'] !== "" && $data['re_password'] !== "" && $data['new_password'] == $data['re_password']) {
				$staff_update = array(
					"login_access" => $data['login_access'],
					"password_erp" => app_hash_password($data['new_password']),
					"last_password_change" => date('Y-m-d H:i:s')
				);
			} else {
				$staff_update = array(
					"login_access" => $data['login_access'],
				);
			}

			$this->db->where('AccountID', $data['userid']);
			$this->db->update(db_prefix() . 'staff', $staff_update);
			if ($this->db->affected_rows()) {
				$affected_row++;
			}
			if ($affected_row > 0) {
				set_alert('success', "Updated Successfully...");
				redirect(admin_url('accounts_master/User_master'));
			} else {
				set_alert('warning', "somethng went wrong...");
				redirect(admin_url('accounts_master/User_master'));
			}
		}
		$data['user_list'] = $this->accounts_master_model->get_login_user_list($postData ?? '');
		$data['title'] = "User Master";
		$this->load->view('admin/accounts_master/user_master', $data);
	}

	public function DayReport()
	{
		if (!has_permission_new('DayReport', '', 'view')) {
			access_denied('Invoice Items');
		}

		close_setup_menu();
		$data['title']                = "Day Report";
		$data['company_detail'] = $this->accounts_master_model->get_company_detail();
		$data['bodyclass']            = 'invoices-total-manual';
		$this->load->view('admin/accounts_master/DayReport', $data);
	}

	public function GetDayReport()
	{
		$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
		);
		$body_data = $this->accounts_master_model->GetDayReportBodyData($filterdata);
		// echo json_encode($body_data);
		// die;
		$html = '';
		$html .= '<table class="table-striped table-bordered SaleVsSaleRtn_report" id="SaleVsSaleRtn_report" width="100%">';
		$html .= '<thead style="font-size:11px;">';
		$html .= '<tr>';
		$html .= '<th align="center">Sr.No</th>';
		$html .= '<th align="center">Date</th>';
		$html .= '<th align="center">Particular</th>';
		$html .= '<th align="center">Voucher Type</th>';
		$html .= '<th align="center">Voucher No.</th>';
		$html .= '<th align="center">Narration</th>';
		$html .= '<th align="center">Debit Amount</th>';
		$html .= '<th align="center">Credit Amount</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		$i = 1;

		$totalcredit = 0;
		$totaldebit = 0;
		foreach ($body_data as $key => $value) {

			$text = $value["Narration"];
			$limit = 40;

			// If the length of the text is less than or equal to the limit, display the original text
			if (strlen($text) <= $limit) {
				$limitedText = $text;
			} else {
				// Otherwise, take only the first $limit characters
				$limitedText = substr($text, 0, $limit);

				// Add an ellipsis to indicate that the text has been truncated
				$limitedText .= '...';
			}




			$crditAmt = 0;
			$debitAmt = 0;
			if ($value["TType"] == 'D') {
				$debitAmt = $value["Amount"];
				$totaldebit += $value["Amount"];
			} else {
				$crditAmt = $value["Amount"];
				$totalcredit += $value["Amount"];
			}

			$html .= '<tr>';
			$html .= '<td align="center">' . $i . '</td>';
			$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';
			$html .= '<td align="left">' . $value["company"] . '</td>';
			$html .= '<td align="left">' . $value["PassedFrom"] . '</td>';
			$html .= '<td align="left">' . $value["VoucherID"] . '</td>';
			$html .= '<td align="left"  title="' . $text . '">' . $limitedText . '</td>';
			$html .= '<td align="right">' . $debitAmt . '</td>';
			$html .= '<td align="right">' . $crditAmt . '</td>';
			$html .= '</tr>';

			$i++;
		}
		$html .= '<tr>';
		$html .= '<td align="center" colspan="6">Total</td>';
		$html .= '<td align="right">' . $totaldebit . '</td>';
		$html .= '<td align="right">' . $totalcredit . '</td>';
		$html .= '</tr>';

		// Footer Data

		$html .= '</tbody>';
		$html .= '</table>';
		echo json_encode($html);
		die;
	}

	public function ExportDayReport()
	{
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		if ($this->input->post()) {

			$filterdata = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'  => $this->input->post('to_date')
			);

			$body_data = $this->accounts_master_model->GetDayReportBodyData($filterdata);
			$company_detail = $this->accounts_master_model->get_company_detail();
			/*echo json_encode($body_data);
				die;*/
			$AccountDetails = 'Day Report';
			$colspan = '9';


			$writer = new XLSXWriter();
			$company_name = array($company_detail->company_name);
			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = $colspan);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);

			$address = $company_detail->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = $colspan);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);

			$msg = "Report Date : " . $this->input->post('from_date') . " To " . $this->input->post('to_date');
			$filter = array($msg);
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = $colspan);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter);

			$msg2 = $AccountDetails;
			$filter2 = array($msg2);
			$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = $colspan);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter2);

			$set_col_tk = [];

			$set_col_tk["Date"] = 'Date';
			$set_col_tk["Particular"] = 'Particular';
			$set_col_tk["Voucher Type"] = 'Voucher Type';
			$set_col_tk["Voucher No."] = 'Voucher No.';
			$set_col_tk["Narration"] = 'Narration';
			$set_col_tk["Debit Amount"] = 'Debit Amount';
			$set_col_tk["Credit Amount"] = 'Credit Amount';

			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);

			$i = 1;
			$totalcredit = 0;
			$totaldebit = 0;
			foreach ($body_data as $key => $value) {

				$crditAmt = 0;
				$debitAmt = 0;
				if ($value["TType"] == 'D') {
					$debitAmt = $value["Amount"];
					$totaldebit += $value["Amount"];
				} else {
					$crditAmt = $value["Amount"];
					$totalcredit += $value["Amount"];
				}


				$list_add = [];
				$list_add[] = _d(substr($value["Transdate"], 0, 10));
				$list_add[] = $value["company"];
				$list_add[] = $value["PassedFrom"];
				$list_add[] = $value["VoucherID"];
				$list_add[] = $value["Narration"];
				$list_add[] = $debitAmt;
				$list_add[] = $crditAmt;

				$i++;
				$writer->writeSheetRow('Sheet1', $list_add);
			}

			$list_add = [];
			$list_add[] = '';
			$list_add[] = '';
			$list_add[] = '';
			$list_add[] = '';
			$list_add[] = 'Total';
			$list_add[] = $totaldebit;
			$list_add[] = $totalcredit;
			$writer->writeSheetRow('Sheet1', $list_add);


			// Footer Data


			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
			foreach ($files as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
			$filename = 'DayReport.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));
			echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE . $filename,
			]);
			die;
		}
	}

	public function CheckShortCodeExit()
    {
        $ShortCode = $this->input->post('ShortCode');
        $ShortCodeDetails  = $this->accounts_master_model->CheckShortCodeExit($ShortCode);
        echo json_encode($ShortCodeDetails);
    }



}
