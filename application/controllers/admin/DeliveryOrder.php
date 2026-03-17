<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DeliveryOrder extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('SalesQuotation_model');
		$this->load->model('SalesOrder_model');
		$this->load->model('DO_model');
	}

	/* =========================
	* ADD / EDIT PAGE
	* ========================= */
	public function index()
	{
		if (!has_permission_new('deliveryOrder', '', 'view')) {
			access_denied('Access Denied');
		}
		$data['title'] = 'Delivery Order';
		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->SalesQuotation_model->get_company_detail($selected_company);
		$data['item_type'] = $this->SalesQuotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['customer_list'] = $this->SalesQuotation_model->getCustomerDropdown();
		$data['category_list'] = $this->DO_model->getCategoryDropdown();
		$data['FreightTerms'] = $this->SalesQuotation_model->get_freight_terms();
		$data['dispatchlocation'] = $this->DO_model->get_dispatch_location();
		$data['order_list'] = $this->DO_model->getOrderList();
		$data['city_list'] = $this->DO_model->getCityList();
		$data['so_list'] = $this->DO_model->getSODropdown();
		$data['transporter_list'] = $this->DO_model->getTransporterDropdown();
		$data['vehicle_no_list'] = $this->DO_model->getVehicleNoDropdown();
		$data['deliveryorder_list'] = $this->DO_model->getDeliveryOrderList();
		$data['history_list'] = $this->DO_model->gethistoryList();
		$data['gate_in'] = $this->DO_model->getgateinList();

		$this->load->view('admin/DO/DOAddEdit', $data);
	}

	public function getNextDONo() {
		$NextDONo = $this->DO_model->getNextDONo();
		echo json_encode(['success' => true, 'NextDONo' => $NextDONo]);
	}

	public function getItem()
	{
		$item_list = $this->DO_model->getDropdown(
			'items',
			'ItemId, ItemName',
			['IsActive' => 'Y'],
			'ItemName',
			'ASC'
		);

		echo json_encode(['success' => true, 'item_list' => $item_list]);
	}

	public function getCustomerDetailsLocation()
	{
		$customer_id = $this->input->post('customer_id');

		if (!$customer_id) {
			echo json_encode(['success' => false, 'message' => 'No customer id']);
			return;
		}
		$customer_details = $this->SalesOrder_model->getCustomerDetailByAccountID($customer_id);
		$location_details = $this->SalesOrder_model->getShippingDatacity($customer_id);
		$valid_orders = $this->DO_model->getOrderList($customer_id, 'valid');
		$invalid_orders = $this->DO_model->getOrderList($customer_id, 'invalid');

		$locations = array();
		if (!empty($location_details)) {
			foreach ($location_details as $location) {
				$locations[] = array(
					'id' => $location['id'],
					'city' => $location['city_name'] ?? '',
				);
			}
		}
		echo json_encode([
			'success' => true,
			'data' => $customer_details,
			'location' => $locations,
			'valid_orders' => $valid_orders,
			'invalid_orders' => $invalid_orders
		]);
	}

	public function GetItemDetails()
	{
		$item_id = $this->input->post('item_id');

		if (!$item_id) {
			echo json_encode(['status' => 'error', 'data' => null]);
			return;
		}

		$itemData = $this->DO_model->getItemDetailsById($item_id);

		if (!empty($itemData)) {
			echo json_encode([
				'status' => 'success',
				'data' => $itemData
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'data' => null
			]);
		}
	}


	public function getVehicleDetails()
	{
		$VehicleNo = $this->input->post('VehicleNo');

		if (!$VehicleNo) {
			echo json_encode(['status' => 'error', 'data' => null]);
			return;
		}

		$VehicleData = $this->DO_model->getVehicleDetailsByVehicleNo($VehicleNo);

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


	public function SaveDeliveryOrder()
	{

		if (empty($this->input->post())) {
			echo json_encode(['success' => false, 'message' => 'No data received']);
			return;
		}

		$PlantID = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		$FY = $this->session->userdata('finacial_year');
		$data = $this->input->post(null, true);
		$field_names = array_keys($data);
		foreach ($field_names as $key => $value) {
			if (!is_array($data[$value])) $data[$value] = trim($data[$value]);
		}
		$required_fields = ['item_category', 'OrderID', 'delivery_order_date', 'DispatchFrom', 'customer_id', 'customer_location'];

		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill ' . $value . ' fields'
				]);
				die;
			}
		}

		$OrderID = $data['OrderID'] ?? '';
		if ($data['delivery_order_date']) {
			$delivery_order_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_order_date'])));
		} else {
			$delivery_order_date = date('Y-m-d');
		}
		$DispatchFrom = $data['DispatchFrom'] ?? '';
		$item_category = $data['item_category'] ?? '';
		$advreg = $data['advreg'] ?? 'Y';
		$gate_no = $data['gate_no'] ?? '';
		$customer_id = $data['customer_id'] ?? '';
		$customer_location = $data['customer_location'] ?? '';
		$customer_state = $data['customer_state'] ?? '';
		$consignee_id = $data['consignee_name'] ?? '';
		$consignee_location = $data['consignee_location'] ?? '';
		$customer_address = $data['customer_address'] ?? '';
		$consignee_address = $data['consignee_address'] ?? '';
		$trans_arranged = $data['trans_arranged'] ?? 'N';
		$transporter_name = $data['transporter_name'] ?? '';
		$VehicleNo = $data['VehicleNo'] ?? '';
		$lr_no = $data['lr_no'] ?? '';
		if ($data['lr_date']) {
			$lr_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['lr_date'])));
		} else {
			$lr_date = date('Y-m-d');
		}
		$driver_name = $data['driver_name'] ?? '';
		$mobile = $data['mobile'] ?? '';
		$license_no = $data['license_no'] ?? '';
		$freight_rate = $data['freight_rate'] ?? '';
		$total_freight = $data['total_freight'] ?? '';
		$by_customer = $data['by_customer'] ?? '';
		$pay_cash = $data['pay_cash'] ?? '';
		$pay_bank = $data['pay_bank'] ?? '';
		$after_delivery = $data['after_delivery'] ?? '';
		$total_weight = $data['total_weight'] ?? 0;
		$total_qty = $data['total_qty'] ?? 0;
		$item_total_amt = $data['item_total_amt'] ?? 0;
		$total_disc_amt = $data['total_disc_amt'] ?? 0;
		$taxable_amt = $data['taxable_amt'] ?? 0;
		$cgst_amt = $data['cgst_amt'] ?? 0;
		$sgst_amt = $data['sgst_amt'] ?? 0;
		$igst_amt = $data['igst_amt'] ?? 0;
		$round_off_amt = $data['round_off_amt'] ?? 0;
		$net_amt = $data['net_amt'] ?? 0;

		$form_mode = $data['form_mode'] ?? 'add';
		$update_id = $data['update_id'] ?? '';

		if ($form_mode == 'add') {
			$check = $this->DO_model->checkDuplicate('DeliveryOrderMaster', ['OrderID' => $OrderID]);
			if ($check) {
				echo json_encode([
					'success' => false,
					'message' => 'Order number already exists.'
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

		// --- delivery_order_date check ---
		if ($delivery_order_date < $fy_start || $delivery_order_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery Order Date (' . date('d/m/Y', strtotime($delivery_order_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}
		// --- lr_date check ---
		if ($lr_date < $fy_start || $lr_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'LR Date (' . date('d/m/Y', strtotime($lr_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// =============================================
		// End of Date Validation
		// =============================================

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'OrderID' => $OrderID,
			'DODate' => $delivery_order_date,
			'DispatchFrom' => $DispatchFrom,
			'CategoryID' => $item_category,
			'AdvRegType' => $advreg,
			'GateINID' => $gate_no,
			'AccountID' => $customer_id,
			'CustLocationID' => $customer_location,
			'ConsigneeName' => $consignee_id,
			'ConsigneeLocation' => $consignee_location,
			'CustAddress' => $customer_address,
			'ConsigneeAddress' => $consignee_address,
			'TransArranged' => $trans_arranged,
			'TransporterID' => $transporter_name,
			'VehicleNo' => $VehicleNo,
			'LRNo' => $lr_no,
			'LRDate' => $lr_date,
			'DriverName' => $driver_name,
			'MobileNo' => $mobile,
			'LicenseNo' => $license_no,
			'FreightRate' => $freight_rate,
			'TotalFreight' => $total_freight,
			'ToPayByCust' => $by_customer,
			'PayInCash' => $pay_cash,
			'PayByBank' => $pay_bank,
			'PayAfterDelivery' => $after_delivery,
			'TotalWt' => $total_weight,
			'TotalQty' => $total_qty,
			'ItemTotal' => $item_total_amt,
			'TotalDisc' => $total_disc_amt,
			'TaxAmt' => $taxable_amt,
			'CGSTAmt' => $cgst_amt,
			'SGSTAmt' => $sgst_amt,
			'IGSTAmt' => $igst_amt,
			'RoundOff' => $round_off_amt,
			'NetAmt' => $net_amt
		];

		if ($form_mode == 'add') {
			if (!has_permission_new('deliveryOrder', '', 'create')) {
				access_denied('Access Denied');
			}
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->DO_model->saveData('DeliveryOrderMaster', $insertData);
			$details = $this->DO_model->getDeliveryOrderDetails($result);
		} else {
			if (!has_permission_new('deliveryOrder', '', 'edit')) {
				access_denied('Access Denied');
			}
			$insertData['Lupdate'] = date('Y-m-d H:i:s');
			$result = $this->DO_model->updateData('DeliveryOrderMaster', $insertData, ['id' => $update_id]);
			$details = $this->DO_model->getDeliveryOrderDetails($update_id);
		}

		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'so_no' => $data['so_no'] ?? [],
				'TransID' => $OrderID,
				'order_date' => $delivery_order_date,
				'customer_id' => $customer_id,
				'customer_state' => $customer_state,
				'item_uid' => $data['item_uid'] ?? [],
				'item_id' => $data['item_id'] ?? [],
				'hsn_code' => $data['hsn_code'] ?? [],
				'unit_rate' => $data['unit_rate'] ?? [],
				'unit_weight' => $data['unit_weight'] ?? [],
				'disc_amt' => $data['disc_amt'] ?? [],
				'uom' => $data['uom'] ?? [],
				'quantity' => $data['min_qty'] ?? [],
				'disquantity' => $data['dispatch_qty'] ?? [],

				'amount' => $data['total_amt'] ?? [],
				'gst' => $data['gst'] ?? []
			];
			if (empty($data['item_id']) || !is_array($data['item_id'])) {
				echo json_encode([
					'success' => false,
					'message' => 'No item data found'
				]);
				return;
			}

			// // ── LOCK VALIDATION (before saveMultiData) ──
			// $item_ids  = $data['item_id']   ?? [];
			// $item_uids = $data['item_uid']  ?? [];
			// $so_nos    = $data['so_no']     ?? [];

			// for ($i = 0; $i < count($item_ids); $i++) {
			// 	$uid = $item_uids[$i] ?? 0;

			// 	// Only check existing records (uid != 0), new inserts are always allowed
			// 	if ($uid != 0) {
			// 		$locked = $this->DO_model->isHistoryLocked(
			// 			$uid,
			// 			$so_nos[$i],
			// 			$item_ids[$i],
			// 			$customer_id,
			// 			$OrderID
			// 		);

			// 		if ($locked) {
			// 			echo json_encode([
			// 				'success' => false,
			// 				'message' => 'Item "' . $item_ids[$i] . '" in SO "' . $so_nos[$i] . '" is locked because a newer Delivery Order already exists for it. You cannot update this record.'
			// 			]);
			// 			return;
			// 		}
			// 	}
			// }


			$this->DO_model->saveMultiData($multi_insert_data);

			echo json_encode([
				'success' => true,
				'message' => 'Delivery Order ' . ($form_mode == 'add' ? 'created' : 'updated') . ' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving Order'
			]);
		}
	}

	public function GetQuotationDetails()
	{
		$quotation_id = $this->input->post('quotation_id');

		$quotation = $this->db
			->where('QuotationID', $quotation_id)
			->get('tblSalesQuotationMaster')
			->row_array();

		if ($quotation) {
			echo json_encode([
				'success' => true,
				'data' => $quotation
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Quotation not found'
			]);
		}
	}

	public function getGateEntryDetails()
	{
		$gate_id = $this->input->post('gate_id');

		if (empty($gate_id)) {
			echo json_encode(['status' => 'error', 'message' => 'Gate ID required']);
			return;
		}

		// Fetch from tblGateMaster
		$gate = $this->db->get_where('tblGateMaster', ['GateINID' => $gate_id])->row_array();

		if (empty($gate)) {
			echo json_encode(['status' => 'error', 'message' => 'Gate entry not found']);
			return;
		}

		$vehicleNo = $gate['VehicleNo'];

		// Check if VehicleNo exists in tblvehicle
		$vehicle = $this->db->get_where('tblvehicle', ['VehicleNo' => $vehicleNo, 'IsActive' => 'Y'])->row_array();

		$response = [
			'status'          => 'success',
			'vehicle_no'      => $vehicleNo,
			'vehicle_in_list' => !empty($vehicle),   // true = exists in dropdown
			'driver_name'     => $gate['DriverName'],
			'mobile'          => $gate['DriverMobileNo'],
			// LicenceNo only available if vehicle record exists
			'license_no'      => !empty($vehicle) ? $vehicle['LicenceNo'] : '',
			'transporter_id'  => !empty($vehicle) ? $vehicle['TransporterID'] : '',
		];

		echo json_encode($response);
	}


	public function GetHistoryDetails()
	{
		$order_id = $this->input->post('OrderID');

		$this->db->select("
        h.*,
        h.OrderQty AS TotalOrderQty,
        IFNULL(u.UsedOrderQty, 0) AS UsedOrderQty,
        (IFNULL(h.OrderQty, 0) - IFNULL(u.UsedOrderQty, 0)) AS BalanceQty,
        h.TransID
    ", false);

		$this->db->from(db_prefix() . 'history h');

		// Subquery for used quantities
		$used_subquery = "
        (
            SELECT 
                OrderID,
                ItemID,
                SUM(IFNULL(OrderQty,0)) AS UsedOrderQty
            FROM " . db_prefix() . "history
            WHERE TransID IS NOT NULL
            GROUP BY OrderID, ItemID
        ) u
    ";

		$this->db->join(
			$used_subquery,
			'u.OrderID = h.OrderID AND u.ItemID = h.ItemID',
			'left',
			false
		);

		// Filter by OrderID if provided
		if (!empty($order_id)) {
			if (is_array($order_id)) {
				$this->db->where_in('h.id', $order_id);
			} else {
				$this->db->where('h.id', $order_id);
			}
		}

		$items = $this->db->get()->result_array();

		if (!empty($items)) {
			echo json_encode([
				'success' => true,
				'items'   => $items
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Items not found'
			]);
		}
	}

	public function GetDeliveryOrderDetails()
	{
		$id = $this->input->post('id');
		$data = $this->DO_model->getDeliveryOrderDetails($id);
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
		$data['title'] = 'Delivery Order List';

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->DO_model->get_company_detail($selected_company);

		$this->load->view('admin/DO/DOList', $data);
	}

	public function ListFilter()
	{
		if ($this->input->post()) {
			$data = $this->input->post(null, true);

			$limit  = $data['limit'] ?? 100;
			$offset = $data['offset'] ?? 0;

			$result  = $this->DO_model->getListByFilter($data, $limit, $offset);
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

		$sheetName = 'Delivery Order List';
		$writer    = new XLSXWriter();

		$header = [
			'Delivery Order No' => 'string',
			'Order Date'        => 'string',
			'Customer Name'     => 'string',
			'Total Weight'      => 'string',
			'Dispatched Qty'    => 'string',
			'Item Total'        => 'string',
			'Total Disc'        => 'string',
			'Taxable Amt'       => 'string',
			'CGST Amt'          => 'string',
			'SGST Amt'          => 'string',
			'IGST Amt'          => 'string',
			'Round Off'         => 'string',
			'Amount'            => 'string',
			'Status'            => 'string',
		];

		$col_count = count($header) - 1; // 13 (0-based last index)

		$writer->writeSheetHeader($sheetName, $header, ['suppress_row' => true]);

		$selected_company = $this->session->userdata('root_company');
		$company_detail   = $this->DO_model->get_company_detail($selected_company);

		// ===== COMPANY NAME ROW =====
		$writer->markMergedCell($sheetName, 0, 0, 0, $col_count);
		$writer->writeSheetRow($sheetName, [$company_detail->company_name ?? '']);

		// ===== COMPANY ADDRESS ROW =====
		$writer->markMergedCell($sheetName, 1, 0, 1, $col_count);
		$writer->writeSheetRow($sheetName, [$company_detail->address ?? '']);

		// ===== FILTER ROW =====
		$from_date = $post['from_date'] ?? '';
		$to_date   = $post['to_date']   ?? '';
		$status    = $post['status']    ?? '';

		$reportedBy = 'Filtered By : ';

		if ($from_date != '') {
			$reportedBy .= 'From Date : ' . $from_date . ', ';
		}
		if ($to_date != '') {
			$reportedBy .= 'To Date : ' . $to_date . ', ';
		}
		if ($status != '') {
			$status_list = [1 => 'Pending', 2 => 'Complete'];
			$reportedBy .= 'Status : ' . ($status_list[$status] ?? '') . ', ';
		}

		$reportedBy = rtrim($reportedBy, ', ');

		$writer->markMergedCell($sheetName, 2, 0, 2, $col_count);
		$writer->writeSheetRow($sheetName, [$reportedBy]);

		// ===== BLANK ROW =====
		$writer->writeSheetRow($sheetName, []);

		// ===== HEADER ROW =====
		$writer->writeSheetRow($sheetName, array_keys($header));

		// ===== CHUNK FETCH =====
		$limit  = 100;
		$offset = 0;

		while (true) {
			$result = $this->DO_model->getListByFilter($post, $limit, $offset);

			if (empty($result['rows'])) {
				break;
			}

			foreach ($result['rows'] as $row) {
				$order_date = !empty($row['DODate'])
					? date('d/m/Y', strtotime($row['DODate']))
					: '';

				$writer->writeSheetRow($sheetName, [
					$row['OrderID']       ?? '',
					$order_date,
					($row['customer_name'] ?? '') . ' (' . ($row['AccountID'] ?? '') . ')',
					$row['TotalWt']       ?? '',
					$row['TotalQty']      ?? '',
					$row['ItemTotal']     ?? '',
					$row['TotalDisc']     ?? '',
					$row['TaxAmt']        ?? '',
					$row['CGSTAmt']       ?? '',
					$row['SGSTAmt']       ?? '',
					$row['IGSTAmt']       ?? '',
					$row['RoundOff']      ?? '',
					$row['NetAmt']        ?? '',
					$row['Status']        ?? 'Pending',
				]);
			}

			$offset += $limit;
			unset($result);
		}

		// ===== SAVE FILE =====
		$filename = 'DeliveryOrderList_' . date('YmdHis') . '.xlsx';
		$filepath = FCPATH . 'uploads/exports/' . $filename;

		if (!is_dir(FCPATH . 'uploads/exports')) {
			mkdir(FCPATH . 'uploads/exports', 0777, true);
		}

		$writer->writeToFile($filepath);

		echo json_encode([
			'success'  => true,
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
