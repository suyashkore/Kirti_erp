<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SalesQuotation extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('SalesQuotation_model');
	}

	/* =========================
	* View Sales Quotation Print
	* ========================= */

	public function SalesQuotationPrint($OrderID)
	{
		if (!has_permission_new('salesQuotation', '', 'print')) {
			access_denied('Access Denied');
		}

		if (!$OrderID) {
			redirect($this->load->view('admin/SalesQuotation/SalesQuotationAddEdit'));
		}

		if (!has_permission_new('CashOrderList', '', 'view')) {
			access_denied('Invoices');
		}
		$invoice = [];
		$invoice1  = $this->SalesQuotation_model->GetSalesQuotationDetailsForPdf($OrderID);
		$history  = $this->SalesQuotation_model->get_order_data($OrderID);

		$invoice = [
			'invoice' => $invoice1,
			'history' => $history
		];
		try {
			$pdf = SalesQuotation_pdf($invoice);
		} catch (Exception $e) {
			$message = $e->getMessage();
			echo $message;
			if (strpos($message, 'Unable to get the size of the image') !== false) {
				show_pdf_unable_to_get_image_size_error();
			}
			die;
		}

		$type = 'I';

		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}

		if ($this->input->get('print')) {
			$type = 'I';
		}

		$pdf->Output(mb_strtoupper(slug_it($OrderID)) . '-InwardSlip.pdf', $type);
	}

	/* =========================
		* ADD / EDIT PAGE
		* ========================= */
	public function index()
	{
		if (!has_permission_new('salesQuotation', '', 'view')) {
			access_denied('Access Denied');
		}
		$data['title'] = 'Sales Quotation';
		$data['item_type'] = $this->SalesQuotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['customer_list'] = $this->SalesQuotation_model->getCustomerDropdown();
		$data['FreightTerms'] = $this->SalesQuotation_model->get_freight_terms();
		$data['saleslocation'] = $this->SalesQuotation_model->get_sales_location();
		$data['quotation_list'] = $this->SalesQuotation_model->getQuotationList();

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->SalesQuotation_model->get_company_detail($selected_company);

		$this->load->view('admin/SalesQuotation/SalesQuotationAddEdit', $data);
	}

	public function getNextQuotationNo()
	{
		$category_id = $this->input->post('category_id');
		if (!$category_id) {
			echo json_encode(['success' => false, 'message' => 'No category id']);
			return;
		}
		$quote_no = $this->SalesQuotation_model->getNextQuotationNoByCategory($category_id);
		$item_list = $this->SalesQuotation_model->getDropdown('items', 'ItemId, ItemName', ['ItemCategoryCode' => $category_id, 'IsActive' => 'Y'], 'ItemName', 'ASC') ?? [];

		echo json_encode(['success' => true, 'quote_no' => $quote_no, 'item_list' => $item_list]);
	}

	public function getCustomerDetailsLocation()
	{
		$customer_id = $this->input->post('customer_id');
		if (!$customer_id) {
			echo json_encode(['success' => false, 'message' => 'No customer id']);
			return;
		}
		$customer_details = $this->SalesQuotation_model->getCustomerDetailByAccountID($customer_id);
		$location_details = $this->SalesQuotation_model->getShippingDatacity($customer_id);
		$broker_list = $this->SalesQuotation_model->getCustomerBrokerList($customer_id);
		$locations = array();
		if (!empty($location_details)) {
			foreach ($location_details as $location) {
				$locations[] = array(
					'id' => $location['id'],
					'city' => $location['city_name'] ?? '',
				);
			}
		}
		echo json_encode(['success' => true, 'data' => $customer_details, 'location' => $locations, 'broker_list' => $broker_list]);
	}

	public function SaveQuotation()
	{
		if (empty($this->input->post())) {
			echo json_encode(['success' => false, 'message' => 'No data received']);
			return;
		}

		// =============================================
		// LOCK VALIDATION — block update if Status = 5
		// =============================================
		$form_mode_raw    = $this->input->post('form_mode') ?? 'add';
		$quotation_no_raw = trim($this->input->post('quotation_no') ?? '');
		$update_id_raw    = trim($this->input->post('update_id') ?? '');

		// form_mode may also be empty if somehow disabled — fallback: if update_id is
		// present it must be an edit regardless of form_mode value
		$is_edit = ($form_mode_raw === 'edit') || (!empty($update_id_raw));

		if ($is_edit && !empty($quotation_no_raw)) {
			$locked_status = $this->SalesQuotation_model->isQuotationLocked($quotation_no_raw);

			if ($locked_status !== false) {
				$status_messages = [
					2 => 'This Quotation is locked because it has been Cancelled.',
					5 => 'This Quotation is locked because it is Complete (all quantities are sold out).',
					7 => 'This Quotation is locked because it has been marked as Partially Complete.',
				];
				echo json_encode([
					'success' => false,
					'message' => $status_messages[$locked_status] ?? 'This Quotation is locked and cannot be updated.',
				]);
				return;
			}
		}
		// =============================================
		// End Lock Validation
		// =============================================

		$PlantID = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		$FY = $this->session->userdata('finacial_year');
		$data = $this->input->post(null, true);
		$field_names = array_keys($data);
		foreach ($field_names as $key => $value) {
			if (!is_array($data[$value])) $data[$value] = trim($data[$value]);
		}
		$required_fields = ['item_type', 'item_category', 'quotation_no', 'quotation_date', 'sales_location', 'customer_id', 'customer_location', 'broker_id', 'payment_terms', 'freight_terms'];

		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill ' . $value . ' fields'
				]);
				die;
			}
		}

		$item_type = $data['item_type'] ?? '';
		$item_category = $data['item_category'] ?? '';
		$quotation_no = $data['quotation_no'] ?? '';
		if ($data['quotation_date']) {
			$quotation_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['quotation_date'])));
		} else {
			$quotation_date = date('Y-m-d');
		}
		$sales_location = $data['sales_location'] ?? '';
		$customer_id = $data['customer_id'] ?? '';
		$GSTIN = $data['GSTIN'] ?? '';
		$customer_country = $data['customer_country'] ?? '';
		$customer_state = $data['customer_state'] ?? '';
		$customer_address = $data['customer_address'] ?? '';
		$customer_location = $data['customer_location'] ?? '';
		$broker_id = $data['broker_id'] ?? '';
		if ($data['delivery_from']) {
			$delivery_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_from'])));
		} else {
			$delivery_from = date('Y-m-d');
		}
		if ($data['delivery_to']) {
			$delivery_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_to'])));
		} else {
			$delivery_to = date('Y-m-d', strtotime('+10 days'));
		}
		$payment_terms = $data['payment_terms'] ?? '';
		$freight_terms = $data['freight_terms'] ?? '';
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
			$check = $this->SalesQuotation_model->checkDuplicate('SalesQuotationMaster', ['QuotationID' => $quotation_no]);
			if ($check) {
				echo json_encode([
					'success' => false,
					'message' => 'Quotation number already exists.'
				]);
				die;
			}
		}

		// =============================================
		// Financial Year Date Validation
		// =============================================
		$FY_int      = (int) $FY;
		$fy_start    = '20' . str_pad($FY_int, 2, '0', STR_PAD_LEFT) . '-04-01';
		$fy_end      = '20' . str_pad($FY_int + 1, 2, '0', STR_PAD_LEFT) . '-03-31';

		$today        = date('Y-m-d');
		$max_txn_date = ($fy_end < $today) ? $fy_end : $today;

		$quotation_date_only = date('Y-m-d', strtotime($quotation_date));
		if ($quotation_date_only < $fy_start || $quotation_date_only > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Quotation Date (' . date('d/m/Y', strtotime($quotation_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		if ($delivery_from < $fy_start || $delivery_from > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery From Date (' . date('d/m/Y', strtotime($delivery_from)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		if ($delivery_to < $fy_start || $delivery_to > $fy_end) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery To Date (' . date('d/m/Y', strtotime($delivery_to)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($fy_end)) . ').'
			]);
			return;
		}

		if ($delivery_to < $delivery_from) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery To Date (' . date('d/m/Y', strtotime($delivery_to)) . ') cannot be earlier than Delivery From Date (' . date('d/m/Y', strtotime($delivery_from)) . ').'
			]);
			return;
		}
		// =============================================
		// End of Date Validation
		// =============================================

		

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'SalesLocation' => $sales_location,
			'QuotationID' => $quotation_no,
			'TransDate' => $quotation_date,
			'ItemType' => $item_type,
			'ItemCategory' => $item_category,
			'AccountID' => $customer_id,
			'BrokerID' => $broker_id,
			'CustomerLocation' => $customer_location,
			'DeliveryFrom' => $delivery_from,
			'DeliveryTo' => $delivery_to,
			'PaymentTerms' => $payment_terms,
			'FreightTerms' => $freight_terms,
			'GSTIN' => $GSTIN,
			'TotalWeight' => $total_weight,
			'TotalQuantity' => $total_qty,
			'ItemAmt' => $item_total_amt,
			'DiscAmt' => $total_disc_amt,
			'TaxableAmt' => $taxable_amt,
			'CGSTAmt' => $cgst_amt,
			'SGSTAmt' => $sgst_amt,
			'IGSTAmt' => $igst_amt,
			'RoundOffAmt' => $round_off_amt,
			'NetAmt' => $net_amt
		];

		if ($form_mode == 'add') {
			if (!has_permission_new('salesQuotation', '', 'create')) {
				access_denied('Access Denied');
			}
			$insertData['TransDate2'] = date('Y-m-d H:i:s');
			$insertData['Status'] = 4;
			$result = $this->SalesQuotation_model->saveData('SalesQuotationMaster', $insertData);
			$details = $this->SalesQuotation_model->getQuoteDetails($result);
		} else {
			if (!has_permission_new('salesQuotation', '', 'edit')) {
				access_denied('Access Denied');
			}
			$result = $this->SalesQuotation_model->updateData('SalesQuotationMaster', $insertData, ['id' => $update_id]);
			$details = $this->SalesQuotation_model->getQuoteDetails($update_id);
		}
		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'quotation_no' => $quotation_no,
				'quotation_date' => $quotation_date,
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
				'amount' => $data['amount'] ?? [],
				'gst' => $data['gst'] ?? []
			];
			$this->SalesQuotation_model->saveMultiData($multi_insert_data);

			echo json_encode([
				'success' => true,
				'message' => 'Quotation ' . ($form_mode == 'add' ? 'created' : 'updated') . ' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving quotation'
			]);
		}
	}

	public function SalesQuotationList()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model('SalesQuotation_model');

		$order_list = $this->SalesQuotation_model->getSalesQuotationList();

		echo json_encode([
			'success' => true,
			'data'    => $order_list
		]);
	}

	public function GetSalesQuotationDetails()
	{
		$id = $this->input->post('id');
		$data = $this->SalesQuotation_model->getQuoteDetails($id);
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
		$data['title'] = 'Sales Quotation List';
		$data['customer_list'] = $this->SalesQuotation_model->getCustomerDropdown();
		$data['saleslocation'] = $this->SalesQuotation_model->get_sales_location();
		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->SalesQuotation_model->get_company_detail($selected_company);

		$this->load->view('admin/SalesQuotation/SalesQuotationList', $data);
	}

	public function ListFilter()
	{
		$from_date   = $this->input->post('from_date');
		$to_date     = $this->input->post('to_date');
		$customer_id = $this->input->post('customer_id');
		$broker_id   = $this->input->post('broker_id');
		$sales_location = $this->input->post('SalesLocation');
		$status      = $this->input->post('status');
		$offset      = (int)$this->input->post('offset');
		$limit       = (int)$this->input->post('limit') ?: 100;

		

		$this->db->select('q.*,pld.LocationName as SalesLocationName, c.billing_state, c.company as customer_name, 
		    IFNULL(b.billing_state, \'\') as broker_state, 
		    IFNULL(b.company, \'\') as broker_name,  
		    IFNULL(som.so_total_weight, 0) as so_total_weight,
			IFNULL(som.so_total_qty, 0) as so_total_qty');
		$this->db->from(db_prefix() . 'SalesQuotationMaster q');
		$this->db->join('tblclients c', 'c.AccountID = q.AccountID', 'left');
		$this->db->join('tblclients b',
		    'TRIM(b.AccountID) = TRIM(q.BrokerID) AND q.BrokerID IS NOT NULL AND q.BrokerID != \'\'',
		    'left');
		$this->db->join(
		    '(SELECT QuotationID, SUM(TotalWeight) as so_total_weight, SUM(TotalQuantity) as so_total_qty
		      FROM ' . db_prefix() . 'SalesOrderMaster 
		      GROUP BY QuotationID
		    ) som',
		    'CONVERT(som.QuotationID USING utf8mb4) = CONVERT(q.QuotationID USING utf8mb4)',
		    'left'
		);

		$this->db->join('tblPlantLocationDetails pld', 'pld.id = q.SalesLocation', 'left');

		if ($from_date) {
			$this->db->where('DATE(q.TransDate) >=', date('Y-m-d', strtotime(str_replace('/', '-', $from_date))));
		}
		if ($to_date) {
			$this->db->where('DATE(q.TransDate) <=', date('Y-m-d', strtotime(str_replace('/', '-', $to_date))));
		}
		if ($customer_id) {
			$this->db->where('q.AccountID', $customer_id);
		}
		if ($broker_id) {
			$this->db->where('q.BrokerID', $broker_id);
		}
		if ($sales_location) {
			$this->db->where('q.SalesLocation', $sales_location);
		}
		if ($status !== false && $status !== null && strlen($status) > 0) {
			$this->db->where('q.Status', (int)$status);
		}

		$total = $this->db->count_all_results('', false);

		$this->db->limit($limit, $offset);
		$this->db->order_by('q.TransDate', 'DESC');
		$rows = $this->db->get()->result_array();

		if (!empty($rows)) {
		    echo json_encode(['success' => true, 'total' => $total, 'rows' => $rows]);
		} else {
		    echo json_encode(['success' => false, 'total' => 0, 'rows' => []]);
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
			'Customer Name'   => 'string',
			'Broker Name'     => 'string',
			'Quotation Weight' => 'string',
			'Quotation Amount' => 'string',
			'Inward Weight'   => 'string',
			'Status'          => 'string'
		];

		$writer->writeSheetHeader($sheetName, $header, ['suppress_row' => true]);

		$selected_company = $this->session->userdata('root_company');
		$company_detail   = $this->SalesQuotation_model->get_company_detail($selected_company);

		$writer->markMergedCell($sheetName, 0, 0, 0, 12);
		$writer->writeSheetRow($sheetName, [$company_detail->company_name]);

		$writer->markMergedCell($sheetName, 1, 0, 1, 12);
		$writer->writeSheetRow($sheetName, [$company_detail->address]);

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
			$status_list = [1 => 'Pending', 2 => 'Cancel', 3 => 'Expired', 4 => 'Approved', 5 => 'Complete', 6 => 'In Progress', 7 => 'Partially Complete'];
			$reportedBy .= 'Status : ' . ($status_list[$status] ?? '') . ', ';
		}

		$writer->markMergedCell($sheetName, 2, 0, 2, 12);
		$writer->writeSheetRow($sheetName, [$reportedBy]);
		$writer->writeSheetRow($sheetName, []);

		$writer->writeSheetRow($sheetName, array_keys($header));

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
					$row['Status'] ?? '',
				]);
			}

			$offset += $limit;
			unset($result);
		}

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
			$parent_id    = $this->input->post('parent_id');
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

	/* =========================
	 * CANCEL QUOTATION
	 * Status 1 (Pending) or 4 (Approved) → set Status to 2 (Cancel)
	 * ========================= */
	public function cancelQuotation()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$quotation_id = $this->input->post('quotation_id');

		if (empty($quotation_id)) {
			echo json_encode(['success' => false, 'message' => 'Quotation ID is required.']);
			return;
		}

		$result = $this->SalesQuotation_model->cancelQuotation($quotation_id);

		if ($result) {
			echo json_encode(['success' => true, 'message' => 'Quotation cancelled successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to cancel quotation. Please try again.']);
		}
	}

	/* =========================
	 * PARTIALLY COMPLETE QUOTATION
	 * Status 6 (In Progress) → set Status to 7 (Partially Complete)
	 * ========================= */
	public function partiallyCompleteQuotation()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$quotation_id = $this->input->post('quotation_id');

		if (empty($quotation_id)) {
			echo json_encode(['success' => false, 'message' => 'Quotation ID is required.']);
			return;
		}

		$result = $this->SalesQuotation_model->partiallyCompleteQuotation($quotation_id);

		if ($result) {
			echo json_encode(['success' => true, 'message' => 'Quotation marked as Partially Complete successfully.']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to update quotation. Please try again.']);
		}
	}
}