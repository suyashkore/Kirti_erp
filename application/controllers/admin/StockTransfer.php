<?php
defined('BASEPATH') or exit('No direct script access allowed');

class StockTransfer extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('SalesQuotation_model');
		$this->load->model('StockTransfer_model');
		$this->load->model('SalesOrder_model');
	}

	/* =========================
	* ADD / EDIT PAGE
	* ========================= */
	public function index()
	{
		if (!has_permission_new('stockTransfer', '', 'view')) {
			access_denied('Access Denied');
		}
		$data['title'] = 'Stock Transfer';
		$data['item_type'] = $this->SalesQuotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['FromPlantLocation'] = $this->StockTransfer_model->getPlantLocationDropdown();
		$data['ToPlantLocation'] = $this->StockTransfer_model->getPlantLocationDropdown();
		$data['VehicleNo'] = $this->StockTransfer_model->getVehicleNoDropdown();
		$data['Items'] = $this->StockTransfer_model->getItemsDropdown();
		$data['NextSTNumber'] = $this->StockTransfer_model->getNextSTNo();
		$data['category_list'] = $this->SalesOrder_model->getCategoryDropdown();
		$data['FreightTerms'] = $this->SalesQuotation_model->get_freight_terms();
		$data['saleslocation'] = $this->SalesQuotation_model->get_sales_location();
		$data['StockTransfer_list'] = $this->StockTransfer_model->getStockTransferList();

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->SalesQuotation_model->get_company_detail($selected_company);


		$this->load->view('admin/StockTransfer/StockTransferAddEdit', $data);
	}

	public function GetDataForEWayBill(){
		$id = $this->input->post('id');
		$data = $this->StockTransfer_model->GetDataForEWayBill($id);

		if (!empty($data)) {
			echo json_encode(['success' => true, 'data' => $data]);
		} else {
			echo json_encode(['success' => false, 'data' => null]);
		}

	}


	// ===== GET PLANT LOCATION DETAILS =====
	public function getPlantLocationDetails()
	{
		$PlantID         = $this->input->post('FromLocation');
		$type            = $this->input->post('type');
		$excludeLocID    = $this->input->post('ExcludeLocationID'); // LocationID to exclude

		if (!$PlantID) {
			echo json_encode(['success' => false, 'message' => 'No location provided']);
			return;
		}

		$Godown = $this->StockTransfer_model->getGodownDetailsByPlantID(
			$PlantID,
			($type === 'to' ? $excludeLocID : null)
		);
		// echo json_encode(['success' => true, 'Godown' => $Godown]);
		$PlantLocation = $this->StockTransfer_model->getPlantLocationDropdown();
		echo json_encode(['success' => true, 'PlantLocation' => $PlantLocation, 'Godown' => $Godown]);
	}

	// ===== GET PINCODE FROM GODOWN =====
	public function getPincode()
	{
		$GodownID = $this->input->post('FromGodown');
		if (!$GodownID) {
			echo json_encode(['success' => false, 'message' => 'No Godown provided']);
			return;
		}
		$result = $this->StockTransfer_model->getPincode($GodownID);
		$pincode = !empty($result) ? ($result[0]['Pincode'] ?? '') : '';
		echo json_encode(['success' => true, 'Pincode' => $pincode]);
	}

	// ===== GET CHAMBERS =====
	public function getChambers()
	{
		$GodownID = $this->input->post('GodownID');
		if (!$GodownID) {
			echo json_encode(['success' => false, 'message' => 'No Godown provided']);
			return;
		}
		$chambers = $this->StockTransfer_model->getChambersByGodownID($GodownID);
		echo json_encode(['success' => true, 'data' => $chambers]);
	}

	// ===== GET STACKS =====
	public function getStacks()
	{
		$GodownID  = $this->input->post('GodownID');
		$ChamberID = $this->input->post('ChamberID');

		log_message('debug', 'getStacks - GodownID: ' . $GodownID . ' | ChamberID: ' . $ChamberID);

		if (!$GodownID || !$ChamberID) {
			echo json_encode(['success' => false, 'message' => 'Missing parameters']);
			return;
		}
		$stacks = $this->StockTransfer_model->getStacksByChamberID($GodownID, $ChamberID);

		log_message('debug', 'getStacks - result count: ' . count($stacks));

		echo json_encode(['success' => true, 'data' => $stacks, 'debug' => ['GodownID' => $GodownID, 'ChamberID' => $ChamberID]]);
	}

	// ===== GET LOTS =====
	public function getLots()
	{
		$GodownID  = $this->input->post('GodownID');
		$ChamberID = $this->input->post('ChamberID');
		$StackID   = $this->input->post('StackID');
		if (!$GodownID || !$ChamberID || !$StackID) {
			echo json_encode(['success' => false, 'message' => 'Missing parameters']);
			return;
		}
		$lots = $this->StockTransfer_model->getLotsByStackID($GodownID, $ChamberID, $StackID);
		echo json_encode(['success' => true, 'data' => $lots]);
	}

	public function getVehicleDetails()
	{
		$VehicleNo = $this->input->post('VehicleNo');

		if (!$VehicleNo) {
			echo json_encode(['status' => 'error', 'data' => null]);
			return;
		}

		$VehicleData = $this->StockTransfer_model->getVehicleDetailsByVehicleNo($VehicleNo);

		if (!empty($VehicleData)) {
			echo json_encode([
				'status' => 'success',
				'data' => $VehicleData
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'data' => null
			]);
		}
	}

	public function getNextSalesOrderNo()
	{
		$category_id = $this->input->post('category_id');
		if (!$category_id) {
			echo json_encode(['success' => false, 'message' => 'No category id']);
			return;
		}
		$order_no = $this->SalesOrder_model->getNextSalesOrderNoByCategory($category_id);
		$item_list = $this->SalesOrder_model->getDropdown('items', 'ItemId, ItemName', ['ItemCategoryCode' => $category_id, 'IsActive' => 'Y'], 'ItemName', 'ASC') ?? [];

		echo json_encode(['success' => true, 'Order_no' => $order_no, 'item_list' => $item_list]);
	}


	public function GetItemDetails()
	{
		$item_id = $this->input->post('item_id');

		if (!$item_id) {
			echo json_encode(['status' => 'error', 'data' => null]);
			return;
		}

		$itemData = $this->StockTransfer_model->getItemDetailsById($item_id);

		if (!empty($itemData)) {
			echo json_encode(['status' => 'success', 'data' => $itemData]);
		} else {
			echo json_encode(['status' => 'error', 'data' => null]);
		}
	}


	public function SaveStockTransfer()
	{
		if (empty($this->input->post())) {
			echo json_encode(['success' => false, 'message' => 'No data received']);
			return;
		}

		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$UserID = $this->session->userdata('username');
		$data = $this->input->post(null, true);
		$field_names = array_keys($data);
		foreach ($field_names as $key => $value) {
			if (!is_array($data[$value])) $data[$value] = trim($data[$value]);
		}
		$required_fields = ['VehicleNo', 'FromLocation', 'FromGodown', 'FromPincode', 'ToLocation', 'ToGodown', 'ToPincode'];

		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill ' . $value . ' fields'
				]);
				die;
			}
		}

		$TransferID = $data['TransferNo'] ?? '';
		if ($data['TransferDate']) {
			$TransferDate = date('Y-m-d', strtotime(str_replace('/', '-', $data['TransferDate'])));
		} else {
			$TransferDate = date('Y-m-d');
		}
		$FromLocationID = $data['FromLocation'] ?? '';
		$FromWHID = $data['FromGodown'] ?? '';
		$FromChamberID = $data['FromChamber'] ?? '';
		$FromStackID = $data['FromStack'] ?? '';
		$FromLotID = $data['FromLot'] ?? '';
		$ToLocationID = $data['ToLocation'] ?? '';
		$ToWHID = $data['ToGodown'] ?? '';
		$ToChamberID = $data['ToChamber'] ?? '';
		$ToStackID = $data['ToStack'] ?? '';
		$ToLotID = $data['ToLot'] ?? '';
		$VehicleNo = $data['VehicleNo'] ?? '';
		$DriverName = $data['DriverName'] ?? '';
		$Distance = $data['Distance'] ?? 0;
		$TotalWeight = $data['total_weight'] ?? 0;
		$TotalQuantity = $data['total_qty'] ?? 0;

		$form_mode = $data['form_mode'] ?? 'add';
		$update_id = $data['update_id'] ?? '';

		if ($form_mode == 'add') {
			$check = $this->StockTransfer_model->checkDuplicate('StockTransferMaster', ['TransferID' => $TransferID]);
			if ($check) {
				echo json_encode([
					'success' => false,
					'message' => 'Transfer number already exists.'
				]);
				die;
			}
		}

		// =============================================
		// Financial Year Date Validation
		// =============================================
		$FY_int      = (int) $FY;
		$fy_start    = '20' . str_pad($FY_int, 2, '0', STR_PAD_LEFT) . '-04-01';          // e.g. 2024-04-01
		$fy_end      = '20' . str_pad($FY_int + 1, 2, '0', STR_PAD_LEFT) . '-03-31';      // e.g. 2025-03-31

		$today       = date('Y-m-d');
		// Max allowed for order/delivery_from: lesser of today or FY end
		$max_txn_date = ($fy_end < $today) ? $fy_end : $today;

		// --- order_date check ---
		if ($TransferDate < $fy_start || $TransferDate > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Transfer Date (' . date('d/m/Y', strtotime($TransferDate)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// =============================================
		// End of Date Validation
		// =============================================


		$insertData = [
			'PlantID'        => $PlantID,
			'FY'             => $FY,
			'TransferID'     => $TransferID,
			'TransferDate'   => $TransferDate,
			'FromLocationID' => $FromLocationID,
			'FromWHID'       => $FromWHID,
			'FromChamberID'  => $FromChamberID,
			'FromStackID'    => $FromStackID,
			'FromLotID'      => $FromLotID,      // ← was missing from $insertData
			'ToLocationID'   => $ToLocationID,   // ← was missing from $insertData
			'ToWHID'         => $ToWHID,
			'ToChamberID'    => $ToChamberID,
			'ToStackID'      => $ToStackID,
			'ToLotID'        => $ToLotID,
			'VehicleNo'      => $VehicleNo,
			'DriverName'     => $DriverName,
			'Distance'       => $Distance,
			'TotalWeight'    => $TotalWeight,
			'TotalQuantity'  => $TotalQuantity,
		];

		if ($form_mode == 'add') {
			if (!has_permission_new('salesOrder', '', 'create')) {
				access_denied('Access Denied');
			}
			$insertData['UserID'] = $UserID;
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->StockTransfer_model->saveData('StockTransferMaster', $insertData);
			// $details = $this->StockTransfer_model->getOrderDetails($result);
			$master_id = $result;
		} else {
			if (!has_permission_new('salesOrder', '', 'edit')) {
				access_denied('Access Denied');
			}
			$insertData['UserID2'] = $UserID;
			$insertData['Lupdate'] = date('Y-m-d H:i:s');
			$result = $this->StockTransfer_model->updateData('StockTransferMaster', $insertData, ['id' => $update_id]);
			// $details = $this->StockTransfer_model->getOrderDetails($update_id);
			$master_id = $update_id;
		}

		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'OrderID' => $TransferID,
				'order_date' => $TransferDate,
				'FromGodown'=> $FromWHID,
				'ToGodown'=> $ToWHID,
				'item_uid' => $data['item_uid'] ?? [],
				'item_id' => $data['item_id'] ?? [],
				'hsn_code' => $data['hsn_code'] ?? [],
				'unit_rate' => $data['unit_rate'] ?? [],
				'unit_weight' => $data['unit_weight'] ?? [],
				'disc_amt' => $data['disc_amt'] ?? [],
				'uom' => $data['uom'] ?? [],
				'quantity' => $data['qty'] ?? []
			];
			if (empty($data['item_id']) || !is_array($data['item_id'])) {
				echo json_encode([
					'success' => false,
					'message' => 'No item data found'
				]);
				return;
			}

			$this->StockTransfer_model->saveMultiData($multi_insert_data);

			// Fetched the Complete Data Including the History
			$details = $this->StockTransfer_model->getOrderDetails($master_id);
			echo json_encode([
				'success' => true,
				'message' => 'Stock Transfer ' . ($form_mode == 'add' ? 'created' : 'updated') . ' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving Order'
			]);
		}
	}

	public function GetHistoryDetails()
	{
		$OrderID = $this->input->post('OrderID');

		$items = $this->db
			->where('OrderID', $OrderID)
			->get('tblhistory')
			->result_array();

		if ($items) {
			echo json_encode([
				'success' => true,
				'items' => $items
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Items not found'
			]);
		}
	}



	public function GetStockTransferDetails()
	{
		$id = $this->input->post('id');
		$data = $this->StockTransfer_model->getStockTransferDetails($id);
		if ($data) {
			echo json_encode([
				'success' => true,
				'data' => $data
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No data found'
			]);
		}
	}

	/* =========================
	* LIST PAGE
	* ========================= */
	public function List()
	{
		$data['title'] = 'Sales Order List';
		$data['customer_list'] = $this->SalesQuotation_model->getCustomerDropdown();

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->SalesQuotation_model->get_company_detail($selected_company);

		$this->load->view('admin/SalesOrder/SalesOrderList', $data);
	}

	public function OrderList()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model('SalesOrder_model');

		$order_list = $this->SalesOrder_model->getOrderList();

		echo json_encode([
			'success' => true,
			'data'    => $order_list
		]);
	}


	public function ListFilter()
	{
		if ($this->input->post()) {
			$data = $this->input->post(null, true);

			$limit  = $data['limit'] ?? 100;
			$offset = $data['offset'] ?? 0;

			$result  = $this->SalesOrder_model->getListByFilter($data, $limit, $offset);
			if (!empty($result['rows'])) {
				echo json_encode([
					'success' => true,
					'message' => 'Data found',
					'total'   => $result['total'],
					'rows'    => $result['rows']
				]);
			} else {
				echo json_encode([
					'success' => false,
					'message' => 'No data found',
					'total'   => 0,
					'rows'    => []
				]);
			}
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid request',
				'total'   => 0,
				'rows'    => []
			]);
		}
	}

	public function ListExportExcel()
	{
		$this->output->enable_profiler(FALSE);
		ob_end_clean();

		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		if (!$this->input->post()) {
			echo json_encode(['success' => false, 'message' => 'Invalid request']);
			return;
		}

		$post = $this->input->post(NULL, TRUE);

		$sheetName = 'Quotation List';
		$writer = new XLSXWriter();

		$header = [
			'Quotation Code'  => 'string',
			'Quotation Date'  => 'string',
			'Customer Name'     => 'string',
			'Broker Name'     => 'string',
			'Quotation Weight' => 'string',
			'Quotation Amount' => 'string',
			'Inward Weight'   => 'string',
			'Status'          => 'string'
		];

		$writer->writeSheetHeader($sheetName, $header, ['suppress_row' => true]);

		$selected_company = $this->session->userdata('root_company');
		$company_detail   = $this->SalesQuotation_model->get_company_detail($selected_company);

		// ===== COMPANY NAME ROW =====
		$writer->markMergedCell($sheetName, 0, 0, 0, 12);
		$writer->writeSheetRow($sheetName, [$company_detail->company_name]);

		// ===== COMPANY ADDRESS ROW =====
		$writer->markMergedCell($sheetName, 1, 0, 1, 12);
		$writer->writeSheetRow($sheetName, [$company_detail->address]);

		// ===== FILTER ROW =====
		$reportedBy = "Filtered By : ";
		$from_date  = $post['from_date'] ?? date('Y-m-01');
		$to_date    = $post['to_date'] ?? date('Y-m-d');
		$customer_id  = $post['customer_id'] ?? '';
		$broker_id  = $post['broker_id'] ?? '';
		$status     = $post['status'] ?? 1;

		if ($from_date != '') {
			$reportedBy .= 'From Date : ' . $from_date . ', ';
		}

		if ($to_date != '') {
			$reportedBy .= 'To Date : ' . $to_date . ', ';
		}

		if ($customer_id != '') {
			$reportedBy .= 'Customer : ' . ($this->SalesQuotation_model->getData('clients', 'company', ['AccountID' => $customer_id])['company'] ?? '') . ', ';
		}

		if ($broker_id != '') {
			$reportedBy .= 'Broker : ' . ($this->SalesQuotation_model->getData('clients', 'company', ['AccountID' => $broker_id])['company'] ?? '') . ', ';
		}

		if ($status != '') {
			$status_list = [1 => 'Pending', 2 => 'Cancel', 3 => 'Expired', 4 => 'Approved', 5 => 'Inprogress', 6 => 'Complete', 7 => 'Partially Complete'];
			$reportedBy .= 'Status : ' . ($status_list[$status] ?? '') . ', ';
		}

		$writer->markMergedCell($sheetName, 2, 0, 2, 12);
		$writer->writeSheetRow($sheetName, [$reportedBy]);
		$writer->writeSheetRow($sheetName, []);

		// ===== HEADER ROW =====
		$writer->writeSheetRow($sheetName, array_keys($header));

		// ===== CHUNK FETCH START =====
		$limit = 100;
		$offset = 0;

		while (true) {
			$result = $this->SalesQuotation_model->getListByFilter($post, $limit, $offset);
			if (empty($result['rows'])) {
				break;
			}

			foreach ($result['rows'] as $row) {
				$writer->writeSheetRow($sheetName, [
					$row['QuotationID'] ?? '',
					$row['TransDate'] ?? '',
					$row['customer_name'] ?? '' . ' (' . $row['AccountID'] ?? '' . ')',
					$row['broker_name'] ?? '' . ' (' . $row['BrokerID'] ?? '' . ')',
					$row['TotalWeight'] ?? '',
					$row['NetAmt'] ?? '',
					'',
					'Pending'
				]);
			}

			$offset += $limit;
			unset($result);
		}

		// ===== SAVE FILE =====
		$filename = 'QuotationList_' . date('YmdHis') . '.xlsx';
		$filepath = FCPATH . 'uploads/exports/' . $filename;

		if (!is_dir(FCPATH . 'uploads/exports')) {
			mkdir(FCPATH . 'uploads/exports', 0777, true);
		}

		$writer->writeToFile($filepath);

		echo json_encode([
			'success' => true,
			'file_url' => base_url('uploads/exports/' . $filename)
		]);
	}
	function GetCustomDropdownList()
	{
		if ($this->input->post()) {
			$parent_id 		= $this->input->post('parent_id');
			$parent_value = $this->input->post('parent_value');
			$child_id     = $this->input->post('child_id');

			switch ($parent_id) {
				case 'item_type':
					switch ($child_id) {
						case 'item_category':
							$data = $this->Items_model->getDropdown('ItemCategoryMaster', 'id, CategoryName as name', ['ItemType' => $parent_value], 'CategoryName', 'ASC');
							break;
						case 'item_main_group':
							$data = $this->Items_model->getDropdown('items_main_groups', 'id, name', ['ItemTypeID' => $parent_value], 'name', 'ASC');
							break;
						default:
							$data = [];
							break;
					}
					break;
				case 'item_main_group':
					$data = $this->Items_model->getDropdown('ItemsSubGroup1', 'id, name', ['main_group_id' => $parent_value], 'name', 'ASC');
					break;
				case 'item_sub_group1':
					$data = $this->Items_model->getDropdown('ItemsSubGroup2', 'id, name', ['sub_group_id1' => $parent_value], 'name', 'ASC');
					break;
				default:
					$data = [];
					break;
			}
			if (empty($data)) {
				echo json_encode([
					'success' => false,
					'message' => 'No data found',
					'data' => []
				]);
			} else {
				echo json_encode([
					'success' => true,
					'message' => 'Data found',
					'data' => $data
				]);
			}
			die;
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Invalid request'
			]);
			die;
		}
	}
}
