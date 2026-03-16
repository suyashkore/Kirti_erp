<?php



defined('BASEPATH') or exit('No direct script access allowed');

/**

 * This class describes a purchase.

 */

class purchase extends AdminController

/**
 * AJAX: Get purchase order and items by PurchID for autofill
 */
{

	const KYC_API_BEARER_TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo";


	public function __construct()
	{

		parent::__construct();

		$this->load->model('purchase_model');

		require_once module_dir_path(PURCHASE_MODULE_NAME) . '/third_party/excel/PHPExcel.php';
	}

	//======================= View Purchase Order Print ============================
	public function PurchOrderPrint($PurchID)
	{
		if (!$PurchID) {
			redirect(admin_url('purchase/AddPurchaseOrder'));
		}

		if (!has_permission_new('PurchaseOrderList', '', 'view')) { access_denied('Access Denied'); }
		$invoice = [];
		$invoice1  = $this->purchase_model->GetPurchaseOrderDetailsForPdf($PurchID);
		$history  = $this->purchase_model->get_order_data($PurchID);

		// invoice data + history array madhe ghala
		$invoice = [
			'invoice' => $invoice1,
			'history' => $history
		];
		try {
			$pdf = PurchOrder_pdf($invoice);
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

		$pdf->Output(mb_strtoupper(slug_it($PurchID)) . '-InwardSlip.pdf', $type);
	}


	public function getPurchaseOrderById()
	{
		$PurchID = $this->input->post('PurchID');
		if (!$PurchID) {
			echo json_encode(['status' => 'error', 'message' => 'No PurchID provided']);
			return;
		}
		$this->load->model('purchase_model');
		$order = $this->purchase_model->get_purchase_orderdata($PurchID);
		$items = $this->purchase_model->get_order_data($PurchID);
		if ($order) {
			// Map order fields to match JS expectations (adjust as needed)
			$order_data = [
				'item_type' => $order['ItemType'] ?? '',
				'order_category' => $order['OrderCategory'] ?? '',
				'order_code' => $order['PurchID'] ?? '',
				'po_date' => isset($order['TransDate']) ? date('d/m/Y', strtotime($order['TransDate'])) : '',
				'purchase_location' => $order['PurchaseLocation'] ?? '',
				'currency' => $order['Currency'] ?? '',
				'vendor_name' => $order['AccountID'] ?? '',
				'vendor_location' => $order['VendorLocation'] ?? '',
				'delivery_from' => isset($order['DeliveryFrom']) ? date('d/m/Y', strtotime($order['DeliveryFrom'])) : '',
				'delivery_to' => isset($order['DeliveryTo']) ? date('d/m/Y', strtotime($order['DeliveryTo'])) : '',
				'vendor_quote_no' => $order['VendorQuoteNo'] ?? '',
				'vendor_quote_date' => isset($order['VendorQuoteDate']) ? date('d/m/Y', strtotime($order['VendorQuoteDate'])) : '',
				'vendor_doc_no' => $order['VendorDocNo'] ?? '',
				'vendor_doc_date' => isset($order['VendorDocDate']) ? date('d/m/Y', strtotime($order['VendorDocDate'])) : '',
				'payment_terms' => $order['PaymentTerms'] ?? '',
				'freight_terms' => $order['FreightTerms'] ?? '',
				'vendor_gst_no' => $order['GSTNo'] ?? '',
				'vendor_country' => $order['Country'] ?? '',
				'vendor_state' => $order['State'] ?? '',
				'vendor_address' => $order['Address'] ?? '',
				'internal_remarks' => $order['InternalRemarks'] ?? '',
				'document_remark' => $order['DocumentRemark'] ?? '',
				'total_weight' => $order['TotalWeight'] ?? '0.00',
				'total_qty' => $order['TotalQuantity'] ?? '0.00',
				'item_total_amt' => $order['ItemTotalAmt'] ?? '0.00',
				'disc_amt' => $order['DiscAmt'] ?? '0.00',
				'taxable_amt' => $order['TaxableAmt'] ?? '0.00',
				'cgst_amt' => $order['CGSTAmt'] ?? '0.00',
				'sgst_amt' => $order['SGSTAmt'] ?? '0.00',
				'igst_amt' => $order['IGSTAmt'] ?? '0.00',
				'round_off_amt' => $order['RoundOffAmt'] ?? '0.00',
				'net_amt' => $order['NetAmt'] ?? '0.00',
			];
			// Map items for JS
			$order_data['items'] = array();
			if ($items && is_array($items)) {
				foreach ($items as $item) {
					$order_data['items'][] = [
						'item_name' => $item['ItemID'] ?? '',
						'hsnno' => $item['HSNCode'] ?? '',
						'uom' => $item['UOM'] ?? '',
						'unit_weight' => $item['UnitWeight'] ?? '',
						'min_qty' => $item['MinQty'] ?? '',
						'max_qty' => $item['MaxQty'] ?? '',
						'disc_amt' => $item['DiscAmt'] ?? '',
						'unit_rate' => $item['UnitRate'] ?? '',
						'gst_percent' => $item['GSTPercent'] ?? '',
						'amount' => $item['Amount'] ?? '',
					];
				}
			}
			echo json_encode(['status' => 'success', 'data' => $order_data]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Order not found']);
		}
	}


	/**
	 * AJAX: Get item categories for a given ItemType
	 */
	public function GetItemCategoriesByType()
	{
		$ItemType = $this->input->post('item_type');

		if (!$ItemType) {
			echo json_encode(['status' => 'error', 'categories' => []]);


			return;
		}

		$categories = $this->purchase_model->GetItemCategoriesByType($ItemType);

		if (!empty($categories)) {
			echo json_encode(['status' => 'success', 'categories' => $categories]);
		} else {
			echo json_encode(['status' => 'success', 'categories' => []]);
		}
	}


	public function getOrderNoByCategory()
	{
		$category_id = $this->input->post('category_id');
		if (!$category_id) {
			echo json_encode(['status' => 'error', 'msg' => 'No category id']);
			return;
		}
		$this->load->model('purchase_model');
		$order_no = $this->purchase_model->get_next_order_no_by_category($category_id);
		echo json_encode(['status' => 'success', 'order_no' => $order_no]);
	}


	// Add this function for updating purchase order
	public function UpdatePurchaseOrder()
	{
		$id = $this->input->post('PurchID'); // PurchID from input

		// Handle file upload for 'attachment' field — save to FCPATH/uploads/Purchase and set DB field 'Attachment'
		if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
			$uploadDirRel = 'uploads/Purchase/';
			$uploadDir = FCPATH . $uploadDirRel;
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$origName = $_FILES['attachment']['name'];
			$ext = pathinfo($origName, PATHINFO_EXTENSION);
			$safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
			$newName = $safeName . '_' . time() . '.' . $ext;
			$targetPath = $uploadDir . $newName;

			if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
				// Store relative path for DB (e.g., 'uploads/Purchase/filename.ext')
				$relPath = $uploadDirRel . $newName;
				// Set both possible keys (model mapping checks 'attachment')

				$data['attachment'] = $relPath;
			} else {
				// upload failed, log and continue without attachment
				log_message('error', 'Attachment upload failed for SaveAccountID: ' . json_encode($_FILES['attachment']));
			}
		} // Handle file upload for 'attachment' field — save to FCPATH/uploads/Purchase and set DB field 'Attachment'
		if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
			$uploadDirRel = 'uploads/Purchase/';
			$uploadDir = FCPATH . $uploadDirRel;
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$origName = $_FILES['attachment']['name'];
			$ext = pathinfo($origName, PATHINFO_EXTENSION);
			$safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
			$newName = $safeName . '_' . time() . '.' . $ext;
			$targetPath = $uploadDir . $newName;

			if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
				// Store relative path for DB (e.g., 'uploads/Purchase/filename.ext')
				$relPath = $uploadDirRel . $newName;
				// Set both possible keys (model mapping checks 'attachment')

				$data['attachment'] = $relPath;
			} else {
				// upload failed, log and continue without attachment
				log_message('error', 'Attachment upload failed for SaveAccountID: ' . json_encode($_FILES['attachment']));
			}
		}

		$FY = $this->session->userdata('finacial_year');

		$data = $this->input->post(); // All form data


		// Convert all dates to Y-m-d before validation
		$quotation_date = !empty($data['quotation_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['quotation_date'])))
			: date('Y-m-d');

		$vendor_doc_date = !empty($data['vendor_doc_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['vendor_doc_date'])))
			: date('Y-m-d');

		$vendor_quote_date = !empty($data['vendor_quote_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['vendor_quote_date'])))
			: date('Y-m-d');

		$delivery_from = !empty($data['delivery_from'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_from'])))
			: date('Y-m-d');

		$delivery_to = !empty($data['delivery_to'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_to'])))
			: date('Y-m-d', strtotime('+10 days'));

		// =============================================
		// Financial Year Date Validation
		// =============================================
		$FY_int       = (int) $FY;
		$fy_start     = '20' . str_pad($FY_int, 2, '0', STR_PAD_LEFT) . '-04-01';
		$fy_end       = '20' . str_pad($FY_int + 1, 2, '0', STR_PAD_LEFT) . '-03-31';
		$today        = date('Y-m-d');
		$max_txn_date = ($fy_end < $today) ? $fy_end : $today;

		// --- quotation_date check ---
		if ($quotation_date < $fy_start || $quotation_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Order Date (' . date('d/m/Y', strtotime($quotation_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- vendor_doc_date check ---
		if ($vendor_doc_date < $fy_start || $vendor_doc_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Vendor Doc Date (' . date('d/m/Y', strtotime($vendor_doc_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- vendor_quote_date check ---
		if ($vendor_quote_date < $fy_start || $vendor_quote_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Quotation Date (' . date('d/m/Y', strtotime($vendor_quote_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- delivery_from check ---
		if ($delivery_from < $fy_start || $delivery_from > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery From Date (' . date('d/m/Y', strtotime($delivery_from)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- delivery_to check ---
		if ($delivery_to < $fy_start || $delivery_to > $fy_end) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery To Date (' . date('d/m/Y', strtotime($delivery_to)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($fy_end)) . ').'
			]);
			return;
		}

		// --- delivery_to must not be before delivery_from ---
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


		if (isset($relPath)) {
			$data['attachment'] = $relPath;
		}


		$result = $this->purchase_model->update_purchase_order_PO($data, $id);
		if ($result) {
			echo json_encode(['success' => true, 'message' => 'Updated successfully']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Update failed']);
		}
	}



	//========================= Save Purchase Order (Custom) ============================
	public function savepurchase()
	{
		if (!has_permission_new('PurchaseOrder', '', 'create')) { access_denied('Access Denied'); }



		// Handle file upload for 'attachment' field — save to FCPATH/uploads/Purchase and set DB field 'Attachment'
		if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
			$uploadDirRel = 'uploads/Purchase/';
			$uploadDir = FCPATH . $uploadDirRel;
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$origName = $_FILES['attachment']['name'];
			$ext = pathinfo($origName, PATHINFO_EXTENSION);
			$safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
			$newName = $safeName . '_' . time() . '.' . $ext;
			$targetPath = $uploadDir . $newName;

			if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
				// Store relative path for DB (e.g., 'uploads/Purchase/filename.ext')
				$relPath = $uploadDirRel . $newName;
				// Set both possible keys (model mapping checks 'attachment')

				$pur_order_data['attachment'] = $relPath;
			} else {
				// upload failed, log and continue without attachment
				log_message('error', 'Attachment upload failed for SaveAccountID: ' . json_encode($_FILES['attachment']));
			}
		}

		$FY = $this->session->userdata('finacial_year');



		$pur_order_data = $this->input->post();


		// Convert all dates to Y-m-d before validation
		$quotation_date = !empty($data['quotation_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['quotation_date'])))
			: date('Y-m-d');

		$vendor_doc_date = !empty($data['vendor_doc_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['vendor_doc_date'])))
			: date('Y-m-d');

		$vendor_quote_date = !empty($data['vendor_quote_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['vendor_quote_date'])))
			: date('Y-m-d');

		$delivery_from = !empty($data['delivery_from'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_from'])))
			: date('Y-m-d');

		$delivery_to = !empty($data['delivery_to'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_to'])))
			: date('Y-m-d', strtotime('+10 days'));

		// =============================================
		// Financial Year Date Validation
		// =============================================
		$FY_int       = (int) $FY;
		$fy_start     = '20' . str_pad($FY_int, 2, '0', STR_PAD_LEFT) . '-04-01';
		$fy_end       = '20' . str_pad($FY_int + 1, 2, '0', STR_PAD_LEFT) . '-03-31';
		$today        = date('Y-m-d');
		$max_txn_date = ($fy_end < $today) ? $fy_end : $today;

		// --- quotation_date check ---
		if ($quotation_date < $fy_start || $quotation_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Order Date (' . date('d/m/Y', strtotime($quotation_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- vendor_doc_date check ---
		if ($vendor_doc_date < $fy_start || $vendor_doc_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Vendor Doc Date (' . date('d/m/Y', strtotime($vendor_doc_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- vendor_quote_date check ---
		if ($vendor_quote_date < $fy_start || $vendor_quote_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Quotation Date (' . date('d/m/Y', strtotime($vendor_quote_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- delivery_from check ---
		if ($delivery_from < $fy_start || $delivery_from > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery From Date (' . date('d/m/Y', strtotime($delivery_from)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- delivery_to check ---
		if ($delivery_to < $fy_start || $delivery_to > $fy_end) {
			echo json_encode([
				'success' => false,
				'message' => 'Delivery To Date (' . date('d/m/Y', strtotime($delivery_to)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($fy_end)) . ').'
			]);
			return;
		}

		// --- delivery_to must not be before delivery_from ---
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



		// Ensure attachment path is set in $pur_order_data for DB
		if (isset($relPath)) {
			$pur_order_data['attachment'] = $relPath;
		}

		// $PlantID = $this->session->userdata('root_company');
		// $FY = $this->session->userdata('finacial_year');
		// $order_count = $this->purchase_model->getNextOrderNoByCategory($pur_order_data['item_category']);
		// $order_count_prefix = $this->purchase_model->getNextOrderNoByCategoryprefix($pur_order_data['item_category']);

		// $pur_order_data['order_no'] = 'PO' . $FY . $PlantID . $order_count_prefix . $order_count;

		// Convert item arrays to JSON array for items_json
		$item_fields = [
			'item_name',
			'hsnno',
			'uom',
			'unit_weight',
			'min_qty',
			'max_qty',
			'disc_Amt',
			'unit_rate',
			'gst_percent',
			'amount'
		];
		$items = [];
		$count = 0;
		if (isset($pur_order_data['item_name']) && is_array($pur_order_data['item_name'])) {
			$count = count($pur_order_data['item_name']);
		}
		for ($i = 0; $i < $count; $i++) {
			$item = [];
			foreach ($item_fields as $field) {
				$item[$field] = isset($pur_order_data[$field][$i]) ? $pur_order_data[$field][$i] : '';
			}
			// Only add if at least item_name is present
			if (!empty($item['item_name'])) {
				$items[] = $item;
			}
		}
		$pur_order_data['items_json'] = json_encode($items);


		// Example: You can add custom processing here if needed
		$pur_order_data['terms'] = isset($pur_order_data['terms']) ? nl2br($pur_order_data['terms']) : '';

		$this->load->model('purchase_model');


		$id = $this->purchase_model->add_pur_order_po($pur_order_data);

		if ($id) {
			echo json_encode([
				'success' => true,
				'message' => 'Purchase order saved successfully.',
				'data' => [
					'id' => $id,
					'QuotatioonID' => isset($pur_order_data['quotation_no']) ? $pur_order_data['quotation_no'] : '',
					'TransDate' => isset($pur_order_data['quotation_date']) ? $pur_order_data['quotation_date'] : '',
					'company' => isset($pur_order_data['vendor_id']) ? $pur_order_data['vendor_id'] : '',
					'TotalWeight' => isset($pur_order_data['total_weight']) ? $pur_order_data['total_weight'] : '',
					'NetAmt' => isset($pur_order_data['net_amt']) ? $pur_order_data['net_amt'] : '',
				]
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Failed to save purchase order.'
			]);
		}
		return;
	}


	public function index()
	{

		if (is_staff_logged_in()) {

			redirect(admin_url('purchase/reports'));
		}



		if (is_vendor_logged_in()) {



			redirect(site_url('purchase/authentication_vendor'));
		}
	}





	//------------  new dashboard ------------------------------------		



	public function NewPurchaseDashboard()
	{

		if (!has_permission_new('PurchaseDashboard', '', 'view')) {

			access_denied('orders');
		}

		close_setup_menu();

		$data['title'] = "Purchase Dashboard";

		$data['PlantDetail'] = $this->purchase_model->GetPlantDetails();

		$data['AllPartyList'] = $this->purchase_model->GetVendorList();

		$data['MainItemGroup'] = $this->purchase_model->get_MainItemGroup_data();

		$data['CityList'] = $this->purchase_model->GetAllCityList();

		$data['StationList'] = $this->purchase_model->GetAllStationList();

		$data['bodyclass'] = 'invoices-total-manual';

		$this->load->view('purchase_order/NewPurchaseDashboard', $data);
	}



	public function GetPartyListDateWise()
	{

		$data = array(

			"FromDate" => $this->input->post('FromDate'),

			"ToDate" => $this->input->post('ToDate')

		);

		$Party = $this->purchase_model->GetPartyListDateWise($data);

		echo json_encode($Party);
	}



	public function GetPartyCityListByFilter()
	{

		$data = array(

			"FromDate" => $this->input->post('FromDate'),

			"ToDate" => $this->input->post('ToDate')

		);

		$CityList = $this->purchase_model->GetPartyCityListByFilter($data);

		echo json_encode($CityList);
	}



	public function GetSubgroup1Data()
	{

		$data = array(

			"MainItemGroup" => $this->input->post('MainItemGroup'),

			"FromDate" => $this->input->post('from_date'),

			"ToDate" => $this->input->post('to_date')

		);

		// print_r($data); die;

		$Subgroup = $this->purchase_model->GetSubgroup1DateWise($data);

		echo json_encode($Subgroup);
	}



	public function GetSubgroup2Data()
	{

		$data = array(

			"SubGroup1" => $this->input->post('SubGroup1'),

			"FromDate" => $this->input->post('from_date'),

			"ToDate" => $this->input->post('to_date')

		);

		// $SubGroup1 = $this->input->post('SubGroup1');

		$Subgroup2 = $this->purchase_model->GetSubgroup2DateWise($data);

		echo json_encode($Subgroup2);
	}



	public function GetItemBySubgroup2Data()
	{

		$SubGroup2 = $this->input->post('SubGroup2');

		$Subgroup2 = $this->purchase_model->GetItemBySubgroup2Data($SubGroup2);

		echo json_encode($Subgroup2);
	}



	//------------end new dashboard ------------------------------------		





	public function GetDashboardCounters()
	{



		$TotalVendor = $this->purchase_model->GetVendorList();

		$TotalOrders = $this->purchase_model->TotalOrders($this->input->post());

		$TotalEntryInvoice = $this->purchase_model->TotalEntryInvoice($this->input->post());

		$TotalPurchaseAmount = $this->purchase_model->TotalPurchaseAmt($this->input->post());

		$TotalPurchaseQuantity = $this->purchase_model->TotalPurchaseQuantity($this->input->post());

		$AvgOrderValue = $this->purchase_model->AvgOrderValue($this->input->post());

		$AvgOrderQty = $this->purchase_model->AvgOrderQty($this->input->post());

		$PurchaseReturnAmount = $this->purchase_model->PurchaseReturnAmount($this->input->post());

		$PurchaseReturnQty = $this->purchase_model->PurchaseReturnQty($this->input->post());

		$PurchaseGstAmount = $this->purchase_model->PurchaseGstAmount($this->input->post());

		$AvgReturnOrder = $this->purchase_model->AvgReturnOrder($this->input->post());

		// print_r($MileageGap);die;

		$return = [

			'TotalVendor' => count($TotalVendor),

			'TotalOrders' => $TotalOrders,

			'TotalPurchaseEntry' => $TotalEntryInvoice['TotalPurchaseEntry'],

			'TotalPurchaseInvoice' => $TotalEntryInvoice['TotalPurchaseInvoice'],

			'TotalPurchaseAmount' => $TotalPurchaseAmount,

			'TotalPurchaseQuantity' => $TotalPurchaseQuantity,

			'AvgOrderValue' => $AvgOrderValue,

			'AvgOrderQty' => $AvgOrderQty,

			'PurchaseReturnAmount' => $PurchaseReturnAmount,

			'PurchaseReturnQty' => $PurchaseReturnQty,

			'PurchaseGstAmount' => $PurchaseGstAmount,

			'AvgReturnOrder' => $AvgReturnOrder,

		];



		echo json_encode($return);
	}

	public function GetTopCustomer()
	{



		$TransData = $this->purchase_model->GetTopCustomer($this->input->post());

		$return = [

			'TransData' => $TransData,

		];



		echo json_encode($return);
	}

	public function GetTopGroupItem()
	{



		$TransData = $this->purchase_model->GetTopGroupItem($this->input->post());

		$return = [

			'TransData' => $TransData,

		];



		echo json_encode($return);
	}





	public function GetMonthlyPurchase()
	{

		$TransData = $this->purchase_model->GetMonthlyPurchase($this->input->post());

		$return = [

			'Purchase' => $TransData['Purchase'],

			'Months' => $TransData['Months'],

		];



		echo json_encode($return);
	}

	public function GetDailyPurchase()
	{

		$TransData = $this->purchase_model->GetDailyPurchase($this->input->post());

		$return = [

			'Purchase' => $TransData['Purchase'],

			'Days' => $TransData['Days'],

		];



		echo json_encode($return);
	}



	public function GetTopPurchaseRateByItemGroup()
	{



		$TransData = $this->purchase_model->GetTopPurchaseRateByItemGroup($this->input->post());

		$return = [

			'TransData' => $TransData,

		];



		echo json_encode($return);
	}

	public function GetTopPurchaseRateByVendor()
	{



		$TransData = $this->purchase_model->GetTopPurchaseRateByVendor($this->input->post());

		$return = [

			'TransData' => $TransData,

		];



		echo json_encode($return);
	}

	//=========================== Purchase Dashboard ==================================

	public function PurchaseDashboard()
	{

		if (!has_permission_new('PurchaseDashboard', '', 'view')) {

			access_denied('orders');
		}

		close_setup_menu();

		$data['title'] = "Purchase Dashboard";

		$data['PlantDetail'] = $this->purchase_model->GetPlantDetails();

		$data['state'] = $this->purchase_model->GetStateList();

		$data['SubGroup'] = $this->purchase_model->GetItemGroupList();

		$data['PurchaseStatus'] = $this->purchase_model->TodaysPurchaseStatus();

		$data['PurchaseEntryStatus'] = $this->purchase_model->TodaysPurchaseEntryStatus();

		$data['PurchaseSKU'] = $this->purchase_model->TotalPurchaseSKU();

		$data['PurchaseVendors'] = $this->purchase_model->TotalPurchaseVendors();

		$data['CompletedInvoices'] = $this->purchase_model->TotalCompletedInvoices();

		$data['RMLowestPurchaseSKU'] = $this->purchase_model->TopRMLowestPurchaseSKU();

		$data['PMLowestPurchaseSKU'] = $this->purchase_model->TopPMLowestPurchaseSKU();

		$data['TopPartyByPurchAmt'] = $this->purchase_model->TopPartyByPurchAmt();

		$data['QCStatusList'] = $this->purchase_model->LoadQCStatusList();

		// echo "<pre>";print_r($data['QCStatusList']);die;

		$data['bodyclass'] = 'invoices-total-manual';

		$this->load->view('purchase_order/PurchaseDashboard', $data);
	}



	public function GetGetPurchaseCounters()
	{

		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

		);

		$PurchaseStatus = $this->purchase_model->TodaysPurchaseStatus($data);

		$PurchaseEntryStatus = $this->purchase_model->TodaysPurchaseEntryStatus($data);

		$PurchaseSKU = $this->purchase_model->TotalPurchaseSKU($data);

		$PurchaseVendors = $this->purchase_model->TotalPurchaseVendors($data);

		$CompletedInvoices = $this->purchase_model->TotalCompletedInvoices($data);

		$RMLowestPurchaseSKU = $this->purchase_model->TopRMLowestPurchaseSKU($data);

		$PMLowestPurchaseSKU = $this->purchase_model->TopPMLowestPurchaseSKU($data);

		$TopPartyByPurchAmt = $this->purchase_model->TopPartyByPurchAmt($data);

		$QCStatusList = $this->purchase_model->LoadQCStatusList($data);

		$return = [

			'PurchaseStatus' => $PurchaseStatus,

			'PurchaseEntryStatus' => $PurchaseEntryStatus,

			'PurchaseSKU' => $PurchaseSKU,

			'PurchaseVendors' => $PurchaseVendors,

			'CompletedInvoices' => $CompletedInvoices,

			'RMLowestPurchaseSKU' => $RMLowestPurchaseSKU,

			'PMLowestPurchaseSKU' => $PMLowestPurchaseSKU,

			'TopPartyByPurchAmt' => $TopPartyByPurchAmt,

			'QCStatusList' => $QCStatusList,

		];



		echo json_encode($return);
	}



	public function GetDailyPurchaseReports()
	{

		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'Items' => $this->input->post('Items'),

			'SubGroup' => $this->input->post('SubGroup'),

			'ReportIn' => $this->input->post('ReportIn'),

			'state' => $this->input->post('state'),

		);

		$result = $this->purchase_model->GetDaywisePurchaseForthisMonth($data);

		echo json_encode($result);
	}

	//======================== Load Top SKU'S ======================================

	public function GetTopPurchaseItem()
	{

		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'MaxCount' => $this->input->post('MaxCount'),

			'state' => $this->input->post('state'),

			'maingroupid' => $this->input->post('maingroupid'),

			'SubGroup' => $this->input->post('SubGroup'),

			'SubGroup2' => $this->input->post('SubGroup2'),

			'ReportIn' => $this->input->post('ReportIn'),

			'Items' => $this->input->post('Items'),

		);

		$result = $this->purchase_model->GetTopPurchaseItem($data);





		$data = [

			'ChartData' => $result['ChartData'],

			/*'TableData' => $html,*/

		];



		echo json_encode($data);
	}



	// Get Result for PartyItemWise report

	public function GetBillPayableDashboardReport()
	{



		// $month_input = $this->input->post('month'); // Example: '2024-11'

		// $date = $month_input.'-01';//your given date

		// $first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");

		// $from_date = date("Y-m-d",$first_date_find);



		// $last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");

		// $last_date = date("Y-m-d",$last_date_find);



		// $Currentdate = date('Y-m-d');

		// if($last_date > $Currentdate){

		// $to_date = $Currentdate;

		// }else{

		// $to_date = $last_date;

		// }



		// echo json_encode();

		// die;

		// $filterdata = array(

		// 'from_date' => date("d/m/Y",strtotime($from_date)),

		// 'to_date'  => date("d/m/Y",strtotime($to_date)),

		// );

		$filterdata = array(

			'from_date' => $this->input->post("from_date"),

			'to_date' => $this->input->post("to_date"),

		);

		$ReportType = 'Overdue';

		$body_data = $this->purchase_model->GetBillsPayableBodyData($filterdata);

		// echo json_encode($body_data);

		// die;

		$html = '';

		$html .= '<table class="table-striped table-bordered SaleVsSaleRtn_report" id="SaleVsSaleRtn_report" width="100%">';

		$html .= '<thead style="font-size:11px;background-color:#50607b;color:#fff;">';

		$html .= '<tr>';

		$html .= '<th align="center">Sr.No</th>';

		$html .= '<th align="center">Date</th>';

		$html .= '<th align="center">Vendor</th>';

		$html .= '<th align="center">Inv No.</th>';

		// $html .= '<th align="center">Inv Amt</th>';

		// $html .= '<th align="center">Paid Amt</th>';

		$html .= '<th align="center">Due Amt</th>';

		$html .= '<th align="center">Due On</th>';

		$html .= '<th align="center">Over Due By Days</th>';

		$html .= '</tr>';

		$html .= '</thead>';

		$html .= '<tbody>';

		$i = 1;



		$chkid = '';

		$total = 0;

		$totaldue = 0;

		$totalpaid = 0;

		$chk = 0;

		$TotalRecord = count($body_data);

		foreach ($body_data as $key => $value) {



			$dueAmt = $value["Invamt"] - $value["PaidAmt"];

			$transdate = $value["Transdate"];



			// Assuming $value["payment_term"] is the number of days

			$paymentTerm = $value["MaxDays"];



			// Calculate the next date based on current date and payment term

			$nextDateTimestamp = strtotime($transdate . " + $paymentTerm days");



			// Format the timestamp to the desired date format

			$nextDate = date('d-M-y', $nextDateTimestamp);



			// Get the current timestamp

			$currentTimestamp = time();



			// Calculate the difference in seconds between current date and next date

			$differenceInSeconds = $currentTimestamp - $nextDateTimestamp;



			// Convert the difference to days

			$overdueDays = ceil($differenceInSeconds / (60 * 60 * 24)); // ceil to round up to the nearest whole day

			if ($chkid != $value["AccountID"]) {

				if ($chk > 0) {

					$chk = 0;

					$html .= '<tr>';

					$html .= '<td colspan="4" align="right" style="font-weight: 700;font-size: 14px;text-align:right;">Total</td>';

					// $html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">'.number_format($total, 2, ".", "").'</td>';

					// $html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">'.number_format($totalpaid, 2, ".", "").'</td>';

					$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaldue, 2, ".", "") . '</td>';

					$html .= '<td align="right" ></td>';



					$html .= '<td align="right"></td>';

					$html .= '</tr>';
				}

				$total = 0;

				$totaldue = 0;

				$totalpaid = 0;
			}

			if ($ReportType == "Overdue" && $overdueDays > 0 && $dueAmt > 0) {

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$total += $value["Invamt"];

				$html .= '<tr>';

				$html .= '<td align="center">' . $i . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="center">' . $value["company"] . '</td>';

				$html .= '<td align="center">' . $value["Invoiceno"] . '</td>';

				// $html .= '<td align="right">'.$value["Invamt"].'</td>';

				// $html .= '<td align="right">'.$value["PaidAmt"].'</td>';

				$html .= '<td align="right">' . number_format($dueAmt, 2, ".", "") . '</td>';

				$html .= '<td align="center">' . $nextDate . '</td>';

				if ($dueAmt > 0 && $overdueDays > 0) {

					$html .= '<td align="center">' . $overdueDays . ' Days</td>';
				} else {

					$html .= '<td align="center"></td>';
				}

				$html .= '</tr>';
			}

			if ($ReportType == "NonOverdue" && $overdueDays <= 0) {

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$total += $value["Invamt"];

				$html .= '<tr>';

				$html .= '<td align="center">' . $i . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="center">' . $value["company"] . '</td>';

				$html .= '<td align="center">' . $value["Invoiceno"] . '</td>';

				// $html .= '<td align="right">'.$value["Invamt"].'</td>';

				// $html .= '<td align="right">'.$value["PaidAmt"].'</td>';

				$html .= '<td align="right">' . number_format($dueAmt, 2, ".", "") . '</td>';

				$html .= '<td align="center">' . $nextDate . '</td>';

				if ($dueAmt > 0 && $overdueDays > 0) {

					$html .= '<td align="center">' . $overdueDays . ' Days</td>';
				} else {

					$html .= '<td align="center"></td>';
				}

				$html .= '</tr>';
			}

			if (empty($ReportType)) {

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$total += $value["Invamt"];

				$html .= '<tr>';

				$html .= '<td align="center">' . $i . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="center">' . $value["company"] . '</td>';

				$html .= '<td align="center">' . $value["Invoiceno"] . '</td>';

				// $html .= '<td align="right">'.$value["Invamt"].'</td>';

				// $html .= '<td align="right">'.$value["PaidAmt"].'</td>';

				$html .= '<td align="right">' . number_format($dueAmt, 2, ".", "") . '</td>';

				$html .= '<td align="center">' . $nextDate . '</td>';

				if ($dueAmt > 0 && $overdueDays > 0) {

					$html .= '<td align="center">' . $overdueDays . ' Days</td>';
				} else {

					$html .= '<td align="center"></td>';
				}

				$html .= '</tr>';
			}

			// for last party total row

			if ($TotalRecord == $i && $chk > 0) {

				$html .= '<tr>';

				$html .= '<td colspan="4" align="right" style="font-weight: 700;font-size: 14px;text-align:right;">Total</td>';

				// $html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">'.number_format($total, 2, ".", "").'</td>';

				// $html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">'.number_format($totalpaid, 2, ".", "").'</td>';

				$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaldue, 2, ".", "") . '</td>';

				$html .= '<td align="right" ></td>';

				$html .= '<td align="right"></td>';

				$html .= '</tr>';
			}



			$i++;
		}

		// Footer Data

		$html .= '</tbody>';

		$html .= '</table>';

		echo json_encode($html);

		die;
	}

	// Get Pending Order Dashboard report

	public function GetPendingOrderDashboardReport()
	{



		// $month_input = $this->input->post('month'); // Example: '2024-11'

		// $date = $month_input.'-01';//your given date

		// $first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");

		// $from_date = date("Y-m-d",$first_date_find);



		// $last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");

		// $last_date = date("Y-m-d",$last_date_find);



		// $Currentdate = date('Y-m-d');

		// if($last_date > $Currentdate){

		// $to_date = $Currentdate;

		// }else{

		// $to_date = $last_date;

		// }

		// echo json_encode();

		// die;

		// $filterdata = array(

		// 'from_date' => date("d/m/Y",strtotime($from_date)),

		// 'to_date'  => date("d/m/Y",strtotime($to_date)),

		// 'status'  => 'Pending',

		// );

		$filterdata = array(

			'from_date' => $this->input->post("from_date"),

			'to_date' => $this->input->post("to_date"),

			'status' => 'Pending',

		);

		$ReportType = 'Overdue';

		$body_data = $this->purchase_model->load_data_for_purchaseOrder($filterdata);

		// echo json_encode($body_data);

		// die;

		$html = '';

		$html .= '<table class="table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">';

		$html .= '<thead style="font-size:11px;background-color:#50607b;color:#fff;">';

		$html .= '<tr>';

		$html .= '<th align="center">Sr.No</th>';

		$html .= '<th align="center">PO.No</th>';

		$html .= '<th align="center">Date</th>';

		$html .= '<th align="center">Purchased From</th>';

		$html .= '<th align="center">Purchase Amt</th>';

		$html .= '<th align="center">Disc Amt</th>';

		$html .= '<th align="center">GST Amt</th>';

		$html .= '<th align="center">Inv. Amt</th>';

		$html .= '</tr>';

		$html .= '</thead>';

		$html .= '<tbody>';

		$i = 1;



		foreach ($body_data as $key => $value) {



			$GstAmt = $value["cgstamt"] + $value["sgstamt"] + $value["igstamt"];

			$html .= '<tr>';

			$html .= '<td align="center">' . $i . '</td>';

			$html .= '<td align="center">' . $value["PurchID"] . '</td>';

			$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

			$html .= '<td align="center">' . $value["AccountName"] . '</td>';

			$html .= '<td align="center">' . $value["Purchamt"] . '</td>';

			$html .= '<td align="center">' . $value["Discamt"] . '</td>';

			$html .= '<td align="center">' . $GstAmt . '</td>';

			$html .= '<td align="center">' . $value["Invamt"] . '</td>';

			$html .= '</tr>';

			$i++;
		}





		// Footer Data

		$html .= '</tbody>';

		$html .= '</table>';

		echo json_encode($html);

		die;
	}

	//=========================== Vendor Add Edit ==================================

	public function AddEditVendor($id = '')
	{
		if (!has_permission_new('vendors', '', 'view')) { access_denied('Access Denied'); }

		// Handle POST request for adding/updating vendor
		if ($this->input->post() && !$this->input->is_ajax_request()) {
			log_message('debug', 'AddEditVendor POST - ID: ' . $id . ', POST data: ' . json_encode($_POST));

			if ($id == '') {
				// Adding new vendor
				if (staff_cant('create', 'vendors')) {
					access_denied('vendors');
				}

				$post_data = $this->input->post();
				log_message('info', 'Adding new vendor - Data: ' . json_encode($post_data));
				$vendor_id = $this->purchase_model->add_vendor($post_data);

				if ($vendor_id) {
					log_message('info', 'Vendor created successfully with ID: ' . $vendor_id);
					set_alert('success', 'Vendor created successfully');
					redirect(admin_url('purchase/AddEditVendor/' . $vendor_id));
				} else {
					log_message('error', 'Failed to create vendor');
					set_alert('danger', 'Failed to create vendor');
				}
			} else {
				// Updating existing vendor
				if (staff_cant('edit', 'vendors')) {
					access_denied('vendors');
				}

				$post_data = $this->input->post();
				log_message('info', 'Updating vendor ID ' . $id . ' - Data: ' . json_encode($post_data));
				$success = $this->purchase_model->update_vendor($post_data, $id);

				if ($success) {
					log_message('info', 'Vendor updated successfully - ID: ' . $id);
					set_alert('success', 'Vendor updated successfully');
				} else {
					log_message('error', 'Failed to update vendor ID: ' . $id);
					set_alert('danger', 'Failed to update vendor');
				}

				redirect(admin_url('purchase/AddEditVendor/' . $id));
			}
		}

		if ($id !== "") {
			$VendorIDSetData = array(
				'VendorIDSet' => $id
			);
			$this->session->set_userdata($VendorIDSetData);
		} else {
			$this->session->unset_userdata('VendorIDSet');
		}

		$this->load->model('currencies_model');
		$data['currencies'] = $this->currencies_model->get();

		// Vendor groups
		$data['groups'] = []; // Add vendor groups if needed

		// Get vendor types
		$data['VendorType'] = $this->purchase_model->get_vendor_categories();

		// Get master data
		$data['FreightTerms'] = $this->purchase_model->get_freight_terms();
		$data['Priority'] = $this->purchase_model->get_priority();
		$data['Territory'] = $this->purchase_model->get_territory();
		$data['Broker'] = $this->purchase_model->get_broker();
		$data['position'] = $this->purchase_model->get_position();
		$data['country'] = $this->purchase_model->get_country();
		$this->load->model('currencies_model');

        $data['currencies'] = $this->currencies_model->get();


		// Get location data
		$data['state'] = $this->purchase_model->getallstate();
		$data['rootcompany'] = $this->purchase_model->get_rootcompany();

		// Get compliance data
		$data['Tdssection'] = $this->purchase_model->get_tds_sections();


		$data['title'] = 'Add/Edit Vendor';

		if ($id != '') {
			$vendor = $this->purchase_model->get_vendor_bkp($id);
			$data['vendor'] = $vendor;
		}

		$this->load->view('vendors/AddEditVendor', $data);
	}



	/* Get item Group Details by ItemID / ajax */

	public function GetAccountID()
	{

		$AccountID = $this->input->post('AccountID');

		$AccountDetails = $this->purchase_model->GetVendorListNEW($AccountID);

		echo json_encode($AccountDetails);
	}

	/* Get All Vendors for List Modal - AJAX */
	public function GetAllVendorList()
	{
		$VendorList = $this->purchase_model->GetAllVendorList();

		// echo"<pre>";
		// print_r($VendorList);
		// die;


		$html = "";

		foreach ($VendorList as $key => $value) {

			$status = ($value["IsActive"] == "Y") ? "Active" : "DeActive";

			$html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';

			$html .= '<td align="center">' . $value['AccountID'] . '</td>';

			$html .= '<td align="left">' . (isset($value['company']) ? $value['company'] : '') . '</td>';

			$html .= '<td align="left">' . (isset($value["FavouringName"]) ? $value["FavouringName"] : '') . '</td>';

			$html .= '<td align="left">' . (isset($value["PAN"]) ? $value["PAN"] : '') . '</td>';

			$html .= '<td align="left">' . (isset($value["GSTIN"]) ? $value["GSTIN"] : '') . '</td>';

			$html .= '<td align="left">' . (isset($value["OrganisationType"]) ? $value["OrganisationType"] : '') . '</td>';

			$html .= '<td align="left">' . (isset($value["GSTType"]) ? $value["GSTType"] : '') . '</td>';

			$html .= '<td align="left">' . $status . '</td>';

			$html .= '</tr>';
		}

		echo json_encode($html);
	}


	/* Get Vendor Details by AccountID - AJAX */
	public function GetVendorDetailByID()
	{
		$AccountID = $this->input->post('AccountID');

		$VendorData = $this->purchase_model->getComprehensiveAccountDataByID($AccountID);


		echo json_encode([
			'status' => 'success',
			'data' => $VendorData
		]);
	}

	/* Get Vendor Shipping Locations by AccountID - AJAX */
	public function GetVendorShippingLocations()
	{
		$AccountID = $this->input->post('AccountID');

		$shippingData = $this->purchase_model->getShippingDatacity($AccountID);

		// echo "<pre>";
		// print_r($shippingData);	
		// die;
		$locations = array();
		if (!empty($shippingData)) {
			foreach ($shippingData as $location) {
				$locations[] = array(
					'id' => $location['id'],
					'city' => $location['city_name'] ?? '',
				);
			}
		}

		echo json_encode([
			'status' => 'success',
			'locations' => $locations
		]);
	}

	public function GetVendorDetails()
	{
		$AccountID = $this->input->post('AccountID');

		$vendorData = $this->purchase_model->getVendorDetailByAccountID($AccountID);

		// echo"<pre>";
		// print_r($vendorData);
		// die;
		if (!empty($vendorData)) {
			echo json_encode([
				'status' => 'success',
				'data' => $vendorData
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'data' => array()
			]);
		}
	}

	public function GetQuotationMaster()
	{
		$AccountID = $this->input->post('AccountID');

		$QuotatioonID = $this->purchase_model->GetQuotationMaster($AccountID);

		// echo"<pre>";
		// print_r($vendorData);
		// die;
		if (!empty($QuotatioonID)) {
			echo json_encode([
				'status' => 'success',
				'data' => $QuotatioonID
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'data' => array()
			]);
		}
	}
	/**
	 * AJAX: Get item details from tblitems table
	 */
	public function GetItemDetails()
	{
		$item_id = $this->input->post('item_id');

		if (!$item_id) {
			echo json_encode(['status' => 'error', 'data' => null]);
			return;
		}

		$itemData = $this->purchase_model->getItemDetailsById($item_id);

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




	public function GetVendorCodeByGroup()
	{

		$vendor_type = $this->input->post('vendor_type');

		$client = $this->purchase_model->GetVendorCodeByGroup($vendor_type);

		$Group = $this->purchase_model->GetVendorGroupDetails($vendor_type);

		$Prefix = $Group->ShortCode;

		// print_r($client);

		// die;

		if (!empty($client)) {



			$AccountID = $client->AccountID;

			// print_r($AccountID);

			// die;

			$AccountID = preg_replace('/\D/', '', $AccountID);

			$code = sprintf('%05d', $AccountID);

			$newcode = sprintf('%05d', $code + 1);

			// print_r($newcode);

			// die;

			$data = $Prefix . $newcode;
		} else {

			$data = $Prefix . sprintf('%05d', 1);
		}

		echo json_encode($data);
	}

	/**
	 * AJAX: Get next vendor code for selected category
	 * Stores ActSubGroupID2 and returns auto-generated vendor code
	 */
	public function GetNextVendorCode()
	{
		if (!$this->input->post('ActSubGroupID2')) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Category ID not provided'
			]);
			return;
		}

		$ActSubGroupID2 = $this->input->post('ActSubGroupID2');
		$vendor_data = $this->purchase_model->GetNextVendorCode($ActSubGroupID2);

		echo json_encode([
			'status' => 'success',
			'next_code' => $vendor_data['next_code'],
			'count' => $vendor_data['count'],
			'category_code' => $vendor_data['category_code'],
			'category_name' => $vendor_data['category_name'],
			'ActSubGroupID2' => $ActSubGroupID2
		]);
	}

	//--------------------------------------------------

	public function GetCityListByStateID()
	{

		$id = $this->input->post('id');

		$quarter_data = $this->purchase_model->GetCityList($id);

		echo json_encode($quarter_data);
	}

	public function GetCity()
	{

		$StateID = $this->input->post('StateID');

		$CityList = $this->clients_model->GetCityList($StateID);

		echo json_encode($CityList);
	}

	/* Save New  Vendor / ajax */

	public function SaveVendor()
	{

		// Collect POST data
		$AccountDetails = $this->input->post();

		// Handle file upload for 'attachment' field — save to FCPATH/uploads/clients and set DB field 'Attachment'
		if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
			$uploadDirRel = 'uploads/Vendor/';
			$uploadDir = FCPATH . $uploadDirRel;
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$origName = $_FILES['attachment']['name'];
			$ext = pathinfo($origName, PATHINFO_EXTENSION);
			$safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
			$newName = $safeName . '_' . time() . '.' . $ext;
			$targetPath = $uploadDir . $newName;

			if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
				// Store relative path for DB (e.g., 'uploads/clients/filename.ext')
				$relPath = $uploadDirRel . $newName;
				// Set both possible keys (model mapping checks 'attachment')
				$AccountDetails['attachment'] = $relPath;
				$AccountDetails['Attachment'] = $relPath;
			} else {
				// upload failed, log and continue without attachment
				log_message('error', 'Attachment upload failed for SaveAccountID: ' . json_encode($_FILES['attachment']));
			}
		}


		$result = $this->purchase_model->SaveVendor($AccountDetails);

		// Normalize model result: it can return AccountID (string/int) on success or an array with success/message
		$response = [
			'success' => false,
			'account_id' => null,
			'attachment' => isset($AccountDetails['Attachment']) ? $AccountDetails['Attachment'] : (isset($AccountDetails['attachment']) ? $AccountDetails['attachment'] : null),
			'message' => 'Failed to create vendor'
		];

		if (is_array($result)) {
			$response['success'] = isset($result['success']) ? (bool) $result['success'] : false;
			if (isset($result['account_id'])) {
				$response['account_id'] = $result['account_id'];
			}
			if (isset($result['message'])) {
				$response['message'] = $result['message'];
			}
		} elseif (!empty($result)) {
			// result assumed to be the AccountID
			$response['success'] = true;
			$response['account_id'] = $result;
			$response['message'] = 'Vendor created successfully';
		}

		echo json_encode($response);
	}



	/* Save Update  Vendor / ajax */

	public function UpdateVendor($id = '')
	{


		$AccountDetails = $this->input->post();
		// Handle file upload for update as well
		if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
			$uploadDirRel = 'uploads/Vendor/';
			$uploadDir = FCPATH . $uploadDirRel;
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$origName = $_FILES['attachment']['name'];
			$ext = pathinfo($origName, PATHINFO_EXTENSION);
			$safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
			$newName = $safeName . '_' . time() . '.' . $ext;
			$targetPath = $uploadDir . $newName;

			if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
				$relPath = $uploadDirRel . $newName;
				$AccountDetails['attachment'] = $relPath;
				$AccountDetails['Attachment'] = $relPath;
			} else {
				log_message('error', 'Attachment upload failed for UpdateAccountID: ' . json_encode($_FILES['attachment']));
			}
		}

		// Get userid from AccountID
		$AccountID = isset($AccountDetails['AccountID']) ? $AccountDetails['AccountID'] : '';
		if (!empty($AccountID)) {
			// Fetch userid from tblclients using AccountID
			$this->db->select('userid');
			$this->db->from('tblclients');
			$this->db->where('AccountID', $AccountID);
			$result = $this->db->get()->row();

			$userid = $result ? $result->userid : 0;
		} else {
			$userid = 0;
		}

		// Pass userid as second parameter to update_tblclients
		$updateResult = $this->purchase_model->update_tblclients($AccountDetails, $userid);

		$response = [
			'success' => $updateResult ? true : false,
			'account_id' => !empty($AccountID) ? $AccountID : null,
			'attachment' => isset($AccountDetails['Attachment']) ? $AccountDetails['Attachment'] : (isset($AccountDetails['attachment']) ? $AccountDetails['attachment'] : null)
		];

		echo json_encode($response);
	}



	public function GetShippingDetails()
	{

		// $Ship_to = $this->input->post('ShipToAddress');

		// $shippingDetails = $this->Purchase_model->get_shipping_addresses($Ship_to);

		// echo json_encode($shippingDetails);

		$ShippingID = $this->input->post('ShipToAddress');

		$AccountID = "";

		$ShippingDetails = $this->purchase_model->GetShippingAddress($AccountID, $ShippingID);

		echo json_encode($ShippingDetails);
	}

	//=========================================== Get Shipping Address List =============

	// public function GetShippingAddressList()

	// {

	// $ShipToParty = $this->input->post('ShipToParty');

	// $shippingAddresses = $this->purchase_model->GetShippingAddress($ShipToParty);

	// echo json_encode($shippingAddresses);

	// }

	public function GetShippingAddressList()
	{

		$ShipToParty = $this->input->post('ShipToParty');



		if ($ShipToParty) {

			$shippingAddresses = $this->purchase_model->GetShippingAddress($ShipToParty);

			echo json_encode($shippingAddresses);
		} else {

			echo json_encode(array());
		}
	}



	//========================= Add Purchase Order ============================

	public function AddPurchaseOrder($id = '')
	{


		$selected_company = $this->session->userdata('root_company');
		$this->load->model('Quotation_model');


		$data['title'] = 'Quotation Master';
		$data['item_type'] = $this->Quotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
		$data['FreightTerms'] = $this->purchase_model->get_freight_terms();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
		$data['quotation_list'] = $this->Quotation_model->getQuotationList();
		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->Quotation_model->get_company_detail($selected_company);


		if (!has_permission_new('purchase-order-po', '', 'view')) {

			access_denied('purchase');
		}

		if ($this->input->post()) {

			// echo "Under Maintenance";die;

			if (!has_permission_new('purchase-order-po', '', 'create')) {

				access_denied('purchase');
			}

			$pur_order_data = $this->input->post();



			$pur_order_data['terms'] = nl2br($pur_order_data['terms']);

			if ($id == '') {

				if (!has_permission_new('purchase-order', '', 'create')) {

					access_denied('purchase_order');
				}
			}
		}



		if ($id == '') {

			$title = _l('create_new_pur_order');
		}

		$this->load->model('currencies_model');

		$data['base_currency'] = $this->currencies_model->get_base_currency();



		$this->load->model('clients_model');



		$this->load->model('departments_model');

		$data['departments'] = $this->departments_model->get();



		$data['taxes'] = $this->purchase_model->get_taxes();

		$data['staff'] = $this->staff_model->get('', ['active' => 1]);

		$data['vendors'] = $this->purchase_model->GetRMVendor();



		$data['Order_list'] = $this->purchase_model->get_Order_list();


		$data['Broker'] = $this->purchase_model->get_broker_name();

		$data['units'] = $this->purchase_model->get_units();

		$data['items'] = $this->purchase_model->get_items();
		// echo"<pre>";
		// print_r($data['items']);
		// die;

		$data['item_code'] = array();

		$data['accounts'] = $this->purchase_model->get_accounts_list();

		$data['freight_id'] = $this->purchase_model->get_accounts_freightid();

		$data['other_id'] = $this->purchase_model->get_accounts_othertid();

		$data['PartyList'] = $this->clients_model->GetPartyList();



		$data['title'] = $title;

		$data['FreightTerms'] = $this->purchase_model->get_freight_terms();
		$data['Vendorname'] = $this->purchase_model->get_vendor_name();
		$data['purchasedata'] = $this->purchase_model->get_purchase_order_data();
		// echo "<pre>";
		// print_r($data['purchasedata']);
		// die;
		$data['item_category_list'] = $this->purchase_model->get_item_category_list();

		$this->load->model('currencies_model');

		$data['currencies'] = $this->currencies_model->get();
		$data['types'] = $this->purchase_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');

		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();

		// echo"<pre>";
		// print_r($data['purchaselocation']);
		// die;

		$this->load->view('purchase_order/AddPurchaseOrder', $data);
	}


	public function get_order_history()
	{
		$order_id = $this->input->post('order_id');



		$data = $this->purchase_model->get_order_history($order_id);
		echo json_encode($data);
	}


	//========================= Add Purchase Invoice ============================

	public function AddPurchaseInvoice($id = '')
	{
		$this->load->model('Quotation_model');
		$this->load->model('currencies_model');



		$data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
		// echo"";
		// print_r($data['vendor_list']);die;
		$data['item_type'] = $this->Quotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['FreightTerms'] = $this->purchase_model->get_freight_terms();
		$data['currencies'] = $this->currencies_model->get();
		$data['Broker'] = $this->purchase_model->get_broker_name();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();






		$this->load->view('purchase_invoice/AddPurchaseInvoice', $data);
	}



	/**

	 * { vendors }

	 */

	public function vendors()
	{



		if (!has_permission_new('VendorList', '', 'view')) {

			access_denied('purchase');
		}

		$data['title'] = _l('vendor');

		$data['states'] = $this->purchase_model->get_state();

		$data['company_detail'] = $this->purchase_model->get_company_detail();

		$this->load->view('vendors/manage', $data);
	}



	public function company_detail()
	{

		$selected_company = $this->session->userdata('root_company');

		$company_detail = $this->purchase_model->get_company_detail($selected_company);

		$company_detail->company_name;

		$company_detail->address;

		echo json_encode($company_detail);



		die();
	}



	public function pur_register()
	{



		$data['title'] = "Purchase Register";

		$data['ItemSubGroups'] = $this->purchase_model->ItemSubGroups();

		$data['items_main_groups'] = $this->purchase_model->get_main_groups();



		$this->load->view('purchase_register/manage', $data);
	}



	public function AccountListPopUp()
	{

		$AccountList = $this->purchase_model->GetAccountList();

		$html = "";

		foreach ($AccountList as $key => $value) {

			$html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';

			$html .= '<td>' . $value["AccountID"] . '</td>';

			$html .= '<td>' . $value["company"] . '</td>';

			$html .= '<td>' . $value["state_name"] . '</td>';

			$html .= '<td>' . $value["city_name"] . '</td>';

			$html .= '</tr>';
		}

		echo $html;
	}



	/* Get Account Details by AccountID / ajax */

	public function GetAccountDetailByID()
	{

		$AccountID = $this->input->post('AccountID');

		$AccountDetails = $this->purchase_model->GetAccountDetails($AccountID);

		echo json_encode($AccountDetails);
	}



	public function ItemListPopUp()
	{

		$MainGroupID = $this->input->post('MainGroupID');

		$Subgroup = $this->input->post('Subgroup');

		$Subgroup2 = $this->input->post('Subgroup2');

		$AccountList = $this->purchase_model->GetItemList($MainGroupID, $Subgroup, $Subgroup2);

		$html = "";

		foreach ($AccountList as $key => $value) {

			$html .= '<tr class="get_ItemID" data-id="' . $value["item_code"] . '">';

			$html .= '<td>' . $value["item_code"] . '</td>';

			$html .= '<td>' . $value["description"] . '</td>';

			$html .= '<td>' . $value["hsn_code"] . '</td>';

			$html .= '<td>' . $value["DivisionName"] . '</td>';

			$html .= '<td>' . $value["SubGroupName"] . '</td>';

			$html .= '<td>' . $value["MainGroupName"] . '</td>';

			$html .= '</tr>';
		}

		echo $html;
	}



	/* Get Item Details by AccountID / ajax */

	public function GetItemDetailByID()
	{

		$ItemID = $this->input->post('ItemID');

		$ItemDetails = $this->purchase_model->GetItemDetails($ItemID);

		echo json_encode($ItemDetails);
	}



	public function accountlist()
	{



		// POST data

		$postData = $this->input->post();



		// Get data

		$data = $this->purchase_model->getaccounts($postData);



		echo json_encode($data);
	}



	public function itemlist()
	{



		// POST data

		$postData = $this->input->post();



		// Get data

		$data = $this->purchase_model->itemlist($postData);



		echo json_encode($data);
	}



	public function get_item_details()
	{



		$ItemID = $this->input->post('ItemID');

		$account_data = $this->purchase_model->get_item_details($ItemID);

		echo json_encode($account_data);
	}



	public function get_account_details()
	{



		$accountID = $this->input->post('act_id');

		$account_data = $this->purchase_model->get_account_details($accountID);

		echo json_encode($account_data);
	}

	public function export_purchase_register()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {



			$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date' => $this->input->post('to_date'),

				'report_type' => $this->input->post('report_type'),

				'accountID' => $this->input->post('accountID'),

				'ItemID' => $this->input->post('ItemID'),

				'MainGroupID' => $this->input->post('MainGroupID'),

				'Subgroup' => $this->input->post('Subgroup'),

				'Subgroup2' => $this->input->post('Subgroup2')

			);

			$accountID = $this->input->post('accountID');

			$ItemID = $this->input->post('ItemID');

			$Subgroup = $this->input->post('Subgroup');

			$MainGroupID = $this->input->post('MainGroupID');

			$accountname = $this->input->post('accountName');

			$Itemname = $this->input->post('Itemname');

			$report_type = $this->input->post('report_type');

			$body_data = $this->purchase_model->get_purchase_for_body_data($filterdata);

			$selected_company_details = $this->purchase_model->get_company_detail();



			$writer = new XLSXWriter();

			//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

			//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');



			$company_name = array($selected_company_details->company_name);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);



			$address = $selected_company_details->address;

			$company_addr = array($address,);

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);



			if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

				$msg = "Purchase Register :  - " . $this->input->post('from_date') . " To " . $this->input->post('to_date');

				$filter = array($msg);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter);
			} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {

				$msg = "Purchase Register - Summary  - " . $this->input->post('from_date') . " To " . $this->input->post('to_date');
			} else if (empty($ItemID) && !empty($accountID)) {

				$msg = "Item Purchase From - " . $accountname . "  - " . $this->input->post('from_date') . " To " . $this->input->post('to_date');

				$filter = array($msg);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter);
			} else if (!empty($ItemID)) {

				$msg = "Item Purchase  - " . $Itemname . "   - " . $this->input->post('from_date') . " To " . $this->input->post('to_date');

				$filter = array($msg);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter);

				$msg2 = "Item Purchase From - " . $accountname;

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 3, $start_col = 0, $end_row = 3, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);
			}







			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);



			if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

				$set_col_tk = [];

				$set_col_tk["Sr.No"] = 'Sr.No';

				$set_col_tk["PurchID"] = 'PurchID';

				$set_col_tk["RcptDate"] = 'RcptDate';

				$set_col_tk["AccountName"] = 'AccountName';

				$set_col_tk["Inv No."] = 'Inv No.';

				$set_col_tk["Inv. Date"] = 'Inv. Date';

				$set_col_tk["PurchAmt"] = 'PurchAmt';

				$set_col_tk["DiscAmt"] = 'DiscAmt';

				$set_col_tk["GSTAmt"] = 'GSTAmt';

				$set_col_tk["FrtAmt"] = 'FrtAmt';

				$set_col_tk["RndOff"] = 'RndOff';

				$set_col_tk["InvAmt"] = 'InvAmt';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);
			} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {

				$set_col_tk = [];

				$set_col_tk["AccountID"] = 'AccountID';

				$set_col_tk["AccountName"] = 'AccountName';

				$set_col_tk["PurchAmt"] = 'PurchAmt';

				$set_col_tk["DiscAmt"] = 'DiscAmt';

				$set_col_tk["GSTAmt"] = 'GSTAmt';

				$set_col_tk["FrtAmt"] = 'FrtAmt';

				$set_col_tk["RndOff"] = 'RndOff';

				$set_col_tk["InvAmt"] = 'InvAmt';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);
			} else if (!empty($ItemID) || !empty($MainGroupID)) {

				$set_col_tk = [];

				$set_col_tk["PurchID"] = 'PurchID';

				$set_col_tk["RcptDate"] = 'RcptDate';

				$set_col_tk["Invoice No."] = 'Invoice No.';

				$set_col_tk["InvDate"] = 'InvDate';

				$set_col_tk["Suppliers Details"] = 'Suppliers Details';

				$set_col_tk["Item Name"] = 'Item Name';

				$set_col_tk["Rate"] = 'Rate';

				$set_col_tk["RcptQty"] = 'RcptQty';

				$set_col_tk["Amount"] = 'Amount';

				$set_col_tk["DiscAmt"] = 'DiscAmt';

				$set_col_tk["GSTAmt"] = 'GSTAmt';

				$set_col_tk["NetAmt"] = 'NetAmt';

				$set_col_tk["Mfg Date"] = 'Mfg Date';

				$set_col_tk["Exp Date"] = 'Exp Date';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);
			} else if (empty($ItemID) && empty($MainGroupID) && !empty($accountID)) {

				$set_col_tk = [];

				$set_col_tk["PurchID"] = 'PurchID';

				$set_col_tk["RcptDate"] = 'RcptDate';

				$set_col_tk["Inv No."] = 'Inv No.';

				$set_col_tk["Inv. Date"] = 'Inv. Date';

				$set_col_tk["ItemName"] = 'ItemName';

				$set_col_tk["Pack"] = 'Pack';

				$set_col_tk["PurchRate"] = 'PurchRate';

				$set_col_tk["BilledQty"] = 'BilledQty';

				$set_col_tk["Amount"] = 'Amount';

				$set_col_tk["DiscAmt"] = 'DiscAmt';

				$set_col_tk["GST"] = 'GST';

				$set_col_tk["GstAmt"] = 'GstAmt';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);
			}



			$i = 1;

			$purchAmt1 = 0;

			$Discamt1 = 0;

			$gstamt_sum1 = 0;

			$Frtamt1 = 0;

			$RoundOffAmt1 = 0;

			$Invamt1 = 0;



			$Purchamt2 = 0;

			$Discamt2 = 0;

			$gstamt2 = 0;

			$Frtamt2 = 0;

			$RoundOffAmt2 = 0;

			$Invamt2 = 0;





			$rcptqty3 = 0;

			$amount3 = 0;

			$discamt3 = 0;

			$gst_sum3 = 0;

			$netamt3 = 0;



			$rcptqty4 = 0;

			$amount4 = 0;

			$discamt4 = 0;

			$gst_sum4 = 0;

			foreach ($body_data as $key => $value) {

				if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

					$list_add = [];

					$list_add[] = $i;

					$list_add[] = $value["PurchID"];

					$list_add[] = _d(substr($value["Transdate"], 0, 10));

					$list_add[] = $value["company"];

					$list_add[] = $value["Invoiceno"];

					$list_add[] = _d(substr($value["Invoicedate"], 0, 10));

					$list_add[] = $value["Purchamt"];

					$purchAmt1 = $purchAmt1 + $value["Purchamt"];

					$list_add[] = $value["Discamt"];

					$Discamt1 = $Discamt1 + $value["Discamt"];

					if ($value["igstamt"] == "0.00") {

						$gstamt = $value["cgstamt"] + $value["sgstamt"];
					} else {

						$gstamt = $value["igstamt"];
					}

					$gstamt_sum1 = $gstamt_sum1 + $gstamt;

					$list_add[] = $gstamt;

					$list_add[] = $value["Frtamt"];

					$Frtamt1 = $Frtamt1 + $value["Frtamt"];

					$list_add[] = $value["RoundOffAmt"];

					$RoundOffAmt1 = $RoundOffAmt1 + $value["RoundOffAmt"];

					$list_add[] = $value["Invamt"];

					$Invamt1 = $Invamt1 + $value["Invamt"];

					$writer->writeSheetRow('Sheet1', $list_add);
				} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {

					$list_add = [];

					$list_add[] = $value["AccountID"];

					$list_add[] = $value["company"];

					$list_add[] = $value["Purchamt"];

					$Purchamt2 = $Purchamt2 + $value["Purchamt"];

					$list_add[] = $value["Discamt"];

					$Discamt2 = $Discamt2 + $value["Discamt"];

					if ($value["igstamt"] == "0.00") {

						$gstamt = $value["cgstamt"] + $value["sgstamt"];
					} else {

						$gstamt = $value["igstamt"];
					}

					$gstamt2 = $gstamt2 + $gstamt;

					$list_add[] = $gstamt;

					$list_add[] = $value["Frtamt"];

					$Frtamt2 = $Frtamt2 + $value["Frtamt"];

					$list_add[] = $value["RoundOffAmt"];

					$RoundOffAmt2 = $RoundOffAmt2 + $value["RoundOffAmt"];

					$list_add[] = $value["Invamt"];

					$Invamt2 = $Invamt2 + $value["Invamt"];

					$writer->writeSheetRow('Sheet1', $list_add);
				} else if (!empty($ItemID) || !empty($MainGroupID)) {

					$list_add = [];

					$list_add[] = $value["OrderID"];

					$list_add[] = _d(substr($value["Transdate"], 0, 10));

					$list_add[] = $value["Invoiceno"];

					$list_add[] = _d(substr($value["Invoicedate"], 0, 10));

					$list_add[] = $value["company"];

					$list_add[] = $value["description"];

					$list_add[] = $value["PurchRate"];

					$list_add[] = $value["rcptqty"];

					$rcptqty3 = $rcptqty3 + $value["rcptqty"];

					$list_add[] = $value["amount"];

					$amount3 = $amount3 + $value["amount"];

					$list_add[] = $value["discamt"];

					$discamt3 = $discamt3 + $value["discamt"];

					$gst_sum = $value["sgstamt"] + $value["cgstamt"] + $value["igstamt"];

					$list_add[] = $gst_sum;

					$gst_sum3 = $gst_sum3 + $gst_sum;

					$netamt = $gst_sum + $value["amount"];

					$list_add[] = $netamt;

					$list_add[] = $value["mfg_date"];

					$list_add[] = $value["expiry_date"];

					$netamt3 = $netamt3 + $netamt;

					$writer->writeSheetRow('Sheet1', $list_add);
				} else if (empty($ItemID) && empty($MainGroupID) && !empty($accountID)) {

					$list_add = [];

					$list_add[] = $value["OrderID"];

					$list_add[] = _d(substr($value["Transdate"], 0, 10));

					$list_add[] = $value["Invoiceno"];

					$list_add[] = _d(substr($value["Invoicedate"], 0, 10));

					$list_add[] = $value["description"];

					$list_add[] = $value["case_qty"];

					$list_add[] = $value["PurchRate"];

					$list_add[] = $value["rcptqty"];

					$rcptqty4 = $rcptqty4 + $value["rcptqty"];

					$list_add[] = $value["amount"];

					$amount4 = $amount4 + $value["amount"];

					$list_add[] = $value["discamt"];

					$discamt4 = $discamt4 + $value["discamt"];

					$list_add[] = $value["taxname"];

					$gst_sum = $value["sgstamt"] + $value["cgstamt"] + $value["igstamt"];

					$list_add[] = $gst_sum;

					$gst_sum4 = $gst_sum4 + $gst_sum;



					$writer->writeSheetRow('Sheet1', $list_add);
				}

				$i++;
			}

			if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

				$list_add = [];

				$list_add[] = "Total";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = $purchAmt1;

				$list_add[] = $Discamt1;

				$list_add[] = $gstamt_sum1;

				$list_add[] = $Frtamt1;

				$list_add[] = $RoundOffAmt1;

				$list_add[] = $Invamt1;

				$writer->writeSheetRow('Sheet1', $list_add);
			} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {

				$list_add = [];

				$list_add[] = "Total";

				$list_add[] = "";

				$list_add[] = $Purchamt2;

				$list_add[] = $Discamt2;

				$list_add[] = $gstamt2;

				$list_add[] = $Frtamt2;

				$list_add[] = $RoundOffAmt2;

				$list_add[] = $Invamt2;

				$writer->writeSheetRow('Sheet1', $list_add);
			} else if (!empty($ItemID) || !empty($MainGroupID)) {

				$list_add = [];

				$list_add[] = "Total";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = $rcptqty3;

				$list_add[] = $amount3;

				$list_add[] = $discamt3;

				$list_add[] = $gst_sum3;

				$list_add[] = $netamt3;

				$list_add[] = '';

				$list_add[] = '';

				$writer->writeSheetRow('Sheet1', $list_add);
			} else if (empty($ItemID) && empty($MainGroupID) && !empty($accountID)) {

				$list_add = [];

				$list_add[] = "Total";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = $rcptqty4;

				$list_add[] = $amount4;

				$list_add[] = $discamt4;

				$list_add[] = "";

				$list_add[] = $gst_sum4;

				$writer->writeSheetRow('Sheet1', $list_add);
			}





			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'PurchaseRegister.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}

	public function get_purchase_data()
	{

		$filterdata = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'report_type' => $this->input->post('report_type'),

			'accountID' => $this->input->post('accountID'),

			'ItemID' => $this->input->post('ItemID'),

			'MainGroupID' => $this->input->post('MainGroupID'),

			'Subgroup' => $this->input->post('Subgroup'),

			'Subgroup2' => $this->input->post('Subgroup2')

		);

		// Parse ContactData JSON array for update
		$contactDataJson = $this->input->post('ContactData');
		$contactDataArr = !empty($contactDataJson) ? json_decode($contactDataJson, true) : [];
		if (!is_array($contactDataArr)) {
			$contactDataArr = [];
		}

		$Contactdata = [];
		foreach ($contactDataArr as $c) {
			$Contactdata[] = [
				'AccountID' => strtoupper($AccountID),
				'PlantID' => $selected_company,
				'firstname' => isset($c['Name']) ? $c['Name'] : (isset($c['name']) ? $c['name'] : ''),
				'title' => isset($c['Designation']) ? $c['Designation'] : (isset($c['PositionID']) ? $c['PositionID'] : ''),
				'phonenumber' => isset($c['Mobile']) ? $c['Mobile'] : (isset($c['mobile']) ? $c['mobile'] : ''),
				'email' => isset($c['Email']) ? $c['Email'] : (isset($c['email']) ? $c['email'] : ''),
				'send_sms' => (isset($c['SendSMS']) && $c['SendSMS']) ? 1 : 0,
				'send_email' => (isset($c['SendEmail']) && $c['SendEmail']) ? 1 : 0,
				'datecreated' => date('Y-m-d H:i:s'),
				'active' => 1,
			];
		}

		$accountID = $this->input->post('accountID');

		$ItemID = $this->input->post('ItemID');

		$accountname = $this->input->post('accountName');

		$Itemname = $this->input->post('Itemname');

		$report_type = $this->input->post('report_type');

		$Subgroup = $this->input->post('Subgroup');

		$MainGroupID = $this->input->post('MainGroupID');

		$body_data = $this->purchase_model->get_purchase_for_body_data($filterdata);

		$company_details = $this->purchase_model->get_company_detail();

		if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

			$colspan = 12;
		} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {

			$colspan = 8;
		} else if (empty($ItemID) && !empty($accountID)) {

			$colspan = 12;
		} else if (!empty($ItemID)) {

			$colspan = 11;
		}

		$html = '';

		$html .= '<table class="table-striped table-bordered daily_report fixTableHead " id="daily_report" width="100%">';

		$html .= '<thead style="font-size:11px;">';



		$html .= '<tr style="display:none;">';

		$html .= '<td colspan="' . $colspan . '" style="font-size:18px;font-weight:700;text-align:center;"><b>' . $company_details->company_name . '</b></td>';

		$html .= '</tr>';



		$html .= '<tr style="display:none;">';

		$html .= '<td colspan="' . $colspan . '" style="font-size:16px;font-weight:600;" align="center"><b>' . $company_details->address . '</b></td>';

		$html .= '</tr>';

		if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

			$html .= '<tr style="display:none;">';

			$html .= '<td colspan="' . $colspan . '" style="text-align:center;"><b>Purchase Register : </b> - ' . $this->input->post('from_date') . ' To ' . $this->input->post('to_date') . '</td>';

			$html .= '</tr>';
		} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {

			$html .= '<tr style="display:none;">';

			$html .= '<td colspan="' . $colspan . '" style="text-align:center;"><b>Purchase Register - Summary </b> - ' . $this->input->post('from_date') . ' To ' . $this->input->post('to_date') . '</td>';

			$html .= '</tr>';
		} else if (empty($ItemID) && !empty($accountID)) {

			$html .= '<tr style="display:none;">';

			$html .= '<td colspan="' . $colspan . '" style="text-align:center;"><b>Item Purchase From - ' . $accountname . ' </b> - ' . $this->input->post('from_date') . ' To ' . $this->input->post('to_date') . '</td>';

			$html .= '</tr>';
		} else if (!empty($ItemID)) {

			$html .= '<tr style="display:none;">';

			$html .= '<td colspan="' . $colspan . '" style="text-align:center;"><b>Item Purchase  - ' . $Itemname . ' </b>  - ' . $this->input->post('from_date') . ' To ' . $this->input->post('to_date') . '</td>';

			$html .= '</tr>';
		}



		$html .= '<tr>';

		if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

			$html .= '<th class="sortable" align="center">Sr.No</th>';

			$html .= '<th class="sortable" align="center">PurchID</th>';

			$html .= '<th class="sortable" align="center">RcptDate</th>';

			$html .= '<th class="sortable" align="center">AccountName</th>';

			$html .= '<th class="sortable" align="center">Inv No.</th>';

			$html .= '<th class="sortable" align="center">Inv. Date</th>';

			$html .= '<th class="sortable" align="center">PurchAmt</th>';

			$html .= '<th class="sortable" align="center">DiscAmt</th>';

			$html .= '<th class="sortable" align="center">GSTAmt</th>';

			$html .= '<th class="sortable" align="center">FrtAmt</th>';

			$html .= '<th class="sortable" align="center">RndOff</th>';

			$html .= '<th class="sortable" align="center">InvAmt</th>';
		} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {

			$html .= '<th class="sortable" align="center">AccountID</th>';

			$html .= '<th class="sortable" align="center">AccountName</th>';

			$html .= '<th class="sortable" align="center">PurchAmt</th>';

			$html .= '<th class="sortable" align="center">DiscAmt</th>';

			$html .= '<th class="sortable" align="center">GSTAmt</th>';

			$html .= '<th class="sortable" align="center">FrtAmt</th>';

			$html .= '<th class="sortable" align="center">RndOff</th>';

			$html .= '<th class="sortable" align="center">InvAmt</th>';
		} else if (!empty($ItemID) || !empty($MainGroupID)) {

			$html .= '<th class="sortable" align="left">PurchID</th>';

			$html .= '<th class="sortable" align="left">RcptDate</th>';

			$html .= '<th class="sortable" align="left">Invoice No.</th>';

			$html .= '<th class="sortable" align="left">InvDate</th>';

			$html .= '<th class="sortable" align="left">Suppliers Details</th>';

			$html .= '<th class="sortable" align="left">Item Name</th>';

			$html .= '<th class="sortable" align="right">Rate</th>';

			$html .= '<th class="sortable" align="right">RcptQty</th>';

			$html .= '<th class="sortable" align="right">Amount</th>';

			$html .= '<th class="sortable" align="right">DiscAmt</th>';

			$html .= '<th class="sortable" align="right">GSTAmt</th>';

			$html .= '<th class="sortable" align="right">NetAmt</th>';

			$html .= '<th class="sortable" align="right">Mfg Date</th>';

			$html .= '<th class="sortable" align="right">Exp Date</th>';
		} else if (empty($ItemID) && empty($MainGroupID) && !empty($accountID)) {

			$html .= '<th class="sortable" align="left">PurchID</th>';

			$html .= '<th class="sortable" align="center">RcptDate</th>';

			$html .= '<th class="sortable" align="left">Inv No.</th>';

			$html .= '<th class="sortable" align="left">Inv. Date</th>';

			$html .= '<th class="sortable" align="left">ItemName</th>';

			$html .= '<th class="sortable" align="center">Pack</th>';

			$html .= '<th class="sortable" align="right">PurchRate</th>';

			$html .= '<th class="sortable" align="right">BilledQty</th>';

			$html .= '<th class="sortable" align="right">Amount</th>';

			$html .= '<th class="sortable" align="right">DiscAmt</th>';

			$html .= '<th class="sortable" align="right">GST %</th>';

			$html .= '<th class="sortable" align="right">GstAmt</th>';
		}





		$html .= '</tr>';



		$html .= '</thead>';

		$html .= '<tbody>';

		$i = 1;

		$purchAmt1 = 0;

		$Discamt1 = 0;

		$gstamt_sum1 = 0;

		$Frtamt1 = 0;

		$RoundOffAmt1 = 0;

		$Invamt1 = 0;



		$Purchamt2 = 0;

		$Discamt2 = 0;

		$gstamt2 = 0;

		$Frtamt2 = 0;

		$RoundOffAmt2 = 0;

		$Invamt2 = 0;





		$rcptqty3 = 0;

		$amount3 = 0;

		$discamt3 = 0;

		$gst_sum3 = 0;

		$netamt3 = 0;



		$rcptqty4 = 0;

		$amount4 = 0;

		$discamt4 = 0;

		$gst_sum4 = 0;

		foreach ($body_data as $key => $value) {



			$html .= '<tr>';

			if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

				$html .= '<td align="center">' . $i . '</td>';

				$html .= '<td align="center">' . $value["PurchID"] . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="left">' . $value["company"] . '</td>';

				$html .= '<td align="left">' . $value["Invoiceno"] . '</td>';

				$html .= '<td align="left">' . _d(substr($value["Invoicedate"], 0, 10)) . '</td>';

				$html .= '<td align="right">' . number_format($value["Purchamt"], 2, '.', '') . '</td>';

				$purchAmt1 = $purchAmt1 + $value["Purchamt"];

				$html .= '<td align="right">' . number_format($value["Discamt"], 2, '.', '') . '</td>';

				$Discamt1 = $Discamt1 + $value["Discamt"];

				if ($value["igstamt"] == "0.00") {

					$gstamt = $value["cgstamt"] + $value["sgstamt"];
				} else {

					$gstamt = $value["igstamt"];
				}

				$gstamt_sum1 = $gstamt_sum1 + $gstamt;

				$html .= '<td align="right">' . number_format($gstamt, 2, '.', '') . '</td>';

				$html .= '<td align="right">' . number_format($value["Frtamt"], 2, '.', '') . '</td>';

				$Frtamt1 = $Frtamt1 + $value["Frtamt"];

				$html .= '<td align="right">' . number_format($value["RoundOffAmt"], 2, '.', '') . '</td>';

				$RoundOffAmt1 = $RoundOffAmt1 + $value["RoundOffAmt"];

				$html .= '<td align="right">' . number_format($value["Invamt"], 2, '.', '') . '</td>';

				$Invamt1 = $Invamt1 + $value["Invamt"];
			} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {



				$html .= '<td align="left">' . $value["AccountID"] . '</td>';

				$html .= '<td align="left">' . $value["company"] . '</td>';

				$html .= '<td align="right">' . number_format($value["Purchamt"], 2, '.', '') . '</td>';

				$Purchamt2 = $Purchamt2 + $value["Purchamt"];

				$html .= '<td align="right">' . number_format($value["Discamt"], 2, '.', '') . '</td>';

				$Discamt2 = $Discamt2 + $value["Discamt"];

				if ($value["igstamt"] == "0.00") {

					$gstamt = $value["cgstamt"] + $value["sgstamt"];
				} else {

					$gstamt = $value["igstamt"];
				}

				$gstamt2 = $gstamt2 + $gstamt;

				$html .= '<td align="right">' . number_format($gstamt, 2, '.', '') . '</td>';

				$html .= '<td align="right">' . number_format($value["Frtamt"], 2, '.', '') . '</td>';

				$Frtamt2 = $Frtamt2 + $value["Frtamt"];

				$html .= '<td align="right">' . number_format($value["RoundOffAmt"], 2, '.', '') . '</td>';

				$RoundOffAmt2 = $RoundOffAmt2 + $value["RoundOffAmt"];

				$html .= '<td align="right">' . number_format($value["Invamt"], 2, '.', '') . '</td>';

				$Invamt2 = $Invamt2 + $value["Invamt"];
			} else if (!empty($ItemID) || !empty($MainGroupID)) {

				$html .= '<td align="left">' . $value["OrderID"] . '</td>';

				$html .= '<td align="left">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="left">' . $value["Invoiceno"] . '</td>';

				$html .= '<td align="left">' . _d(substr($value["Invoicedate"], 0, 10)) . '</td>';

				$html .= '<td align="left">' . $value["company"] . '</td>';

				$html .= '<td align="left">' . $value["description"] . '</td>';

				$html .= '<td align="right">' . $value["PurchRate"] . '</td>';

				$html .= '<td align="right">' . number_format($value["rcptqty"], 2, '.', '') . '</td>';

				$rcptqty3 = $rcptqty3 + $value["rcptqty"];

				$html .= '<td align="right">' . number_format($value["amount"], 2, '.', '') . '</td>';

				$amount3 = $amount3 + $value["amount"];

				$html .= '<td align="right">' . number_format($value["discamt"], 2, '.', '') . '</td>';

				$discamt3 = $discamt3 + $value["discamt"];

				$gst_sum = $value["sgstamt"] + $value["cgstamt"] + $value["igstamt"];

				$html .= '<td align="right">' . number_format($gst_sum, 2, '.', '') . '</td>';

				$gst_sum3 = $gst_sum3 + $gst_sum;

				$netamt = $gst_sum + $value["amount"];

				$html .= '<td align="right">' . number_format($netamt, 2, '.', '') . '</td>';

				$html .= '<td align="right">' . $value["mfg_date"] . '</td>';

				$html .= '<td align="right">' . $value["expiry_date"] . '</td>';

				$netamt3 = $netamt3 + $netamt;
			} else if (empty($ItemID) && empty($MainGroupID) && !empty($accountID)) {

				$html .= '<td align="left">' . $value["OrderID"] . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="left">' . $value["Invoiceno"] . '</td>';

				$html .= '<td align="left">' . _d(substr($value["Invoicedate"], 0, 10)) . '</td>';

				$html .= '<td align="left">' . $value["description"] . '</td>';

				$html .= '<td align="center">' . $value["case_qty"] . '</td>';

				$html .= '<td align="right">' . $value["PurchRate"] . '</td>';

				$html .= '<td align="right">' . number_format($value["rcptqty"], 2, '.', '') . '</td>';

				$rcptqty4 = $rcptqty4 + $value["rcptqty"];

				$html .= '<td align="right">' . $value["amount"] . '</td>';

				$amount4 = $amount4 + $value["amount"];

				$html .= '<td align="right">' . number_format($value["discamt"], 2, '.', '') . '</td>';

				$discamt4 = $discamt4 + $value["discamt"];

				$html .= '<td align="right">' . $value["taxname"] . '%</td>';

				$gst_sum = $value["sgstamt"] + $value["cgstamt"] + $value["igstamt"];

				$html .= '<td align="right">' . $gst_sum . '</td>';

				$gst_sum4 = $gst_sum4 + $gst_sum;
			}



			$html .= '</tr>';

			$i++;
		}

		$html .= '</tbody>';

		$html .= '<tfoot>';

		$html .= '<tr>';

		if ($report_type == 1 && empty($ItemID) && empty($accountID)) {

			$html .= '<td align="center"><b>Total</b></td>';

			$html .= '<td align="center"></td>';

			$html .= '<td align="center"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="right"><b>' . number_format($purchAmt1, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($Discamt1, 2, '.', '') . '</b></td>';



			$html .= '<td align="right"><b>' . number_format($gstamt_sum1, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($Frtamt1, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($RoundOffAmt1, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($Invamt1, 2, '.', '') . '</b></td>';
		} else if ($report_type == 2 && empty($ItemID) && empty($accountID)) {



			$html .= '<td align="left"><b>Total</b></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="right"><b>' . number_format($Purchamt2, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($Discamt2, 2, '.', '') . '</b></td>';



			$html .= '<td align="right"><b>' . number_format($gstamt2, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($Frtamt2, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($RoundOffAmt2, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($Invamt2, 2, '.', '') . '</b></td>';
		} else if (!empty($ItemID) || !empty($MainGroupID)) {

			$html .= '<td align="left"><b>Total</b></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="right"></td>';

			$html .= '<td align="right"><b>' . number_format($rcptqty3, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($amount3, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($discamt3, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($gst_sum3, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($netamt3, 2, '.', '') . '</b></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="right"></td>';
		} else if (empty($ItemID) && empty($MainGroupID) && !empty($accountID)) {

			$html .= '<td align="left"><b>Total</b></td>';

			$html .= '<td align="center"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="left"></td>';

			$html .= '<td align="center"></td>';

			$html .= '<td align="right"></td>';

			$html .= '<td align="right"><b>' . number_format($rcptqty4, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($amount4, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"><b>' . number_format($discamt4, 2, '.', '') . '</b></td>';

			$html .= '<td align="right"></td>';

			$html .= '<td align="right"><b>' . number_format($gst_sum4, 2, '.', '') . '</b></td>';
		}

		$html .= '</tr>';

		$html .= '</tfoot>';

		$html .= '</table>';

		echo json_encode($html);

		die;
	}



	/**

	 * { table vendor }

	 */

	public function table_vendor()
	{



		$this->app->get_table_data(module_views_path('purchase', 'vendors/table_vendor'));
	}



	/**

	 * { vendor }

	 *

	 * @param      string  $id     The vendor

	 * @return      view

	 */

	public function vendor($id = '')
	{

		if ($this->input->post() && !$this->input->is_ajax_request()) {

			$data = $this->input->post();

			// Normalize data
			if (isset($data["vat"]) && !empty($data["vat"])) {
				$data["vat"] = strtoupper($data["vat"]);
			}
			if (isset($data["pan"]) && !empty($data["pan"])) {
				$data["pan"] = strtoupper($data["pan"]);
			}

			// Validate PAN if provided
			if (isset($data["pan"]) && !empty($data["pan"])) {
				if (!preg_match("/^[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}?$/", $data["pan"])) {
					set_alert('warning', 'Enter valid PAN number');
					redirect(admin_url('purchase/vendor/' . ($id != '' ? $id : '')));
					return;
				}
			}

			// Validate GST if provided
			if (isset($data["vat"]) && !empty($data["vat"])) {
				if (!preg_match("/^([0-9]){2}([A-Za-z]){5}([0-9]){4}([A-Za-z]){1}([0-9]{1})([0-9A-Za-z]){2}?$/", $data["vat"])) {
					set_alert('warning', 'Enter valid GST number');
					redirect(admin_url('purchase/vendor/' . ($id != '' ? $id : '')));
					return;
				}
				$data["gsttype"] = 1;
			} else {
				$data["vat"] = NULL;
				$data["gsttype"] = 2;
			}

			if ($id == '') {
				// New vendor
				if (staff_cant('create', 'vendors')) {
					access_denied('purchase');
				}

				$id = $this->purchase_model->add_vendor($data);

				if ($id) {
					set_alert('success', _l('added_successfully', _l('vendor')));
					redirect(admin_url('purchase/vendors'));
				} else {
					set_alert('error', 'Error creating vendor');
					redirect(admin_url('purchase/vendor'));
				}
			} else {
				// Update existing vendor
				if (staff_cant('edit', 'vendors')) {
					access_denied('purchase');
				}

				$success = $this->purchase_model->update_vendor($data, $id);

				if ($success == true) {
					set_alert('success', _l('updated_successfully', _l('vendor')));
				} else {
					set_alert('error', 'Error updating vendor');
				}

				redirect(admin_url('purchase/vendor/' . $id));
			}
		}

		// echo $id;die;

		$group = !$this->input->get('group') ? 'profile' : $this->input->get('group');

		$data['group'] = $group;



		if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {

			redirect(admin_url('clients/client/' . $id . '?group=contacts&contactid=' . $contact_id));
		}









		if ($id == '') {

			$title = _l('add_new', _l('vendor_lowercase'));
		} else {

			//  echo $id;die;

			$client = $this->purchase_model->get_vendor($id);

			$data['client'] = $client;

			// print_r($client);die;

			$data['customer_tabs'] = get_customer_profile_tabs();



			if (!$client) {

				show_404();
			}



			$data['contacts'] = $this->purchase_model->get_contacts($id);



			// $data['payments'] = $this->purchase_model->get_payment_by_vendor($id);

			$data['payments'] = '';



			// print_r($data);die;

			$data['group'] = $this->input->get('group');



			$data['title'] = _l('setting');

			// $data['tab'][] = ['name' => 'view', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];

			// $data['tab'][] = ['name' => 'profile', 'icon' => '<i class="fa fa-user-circle menu-icon"></i>'];

			// $data['tab'][] = ['name' => 'contacts','icon' => '<i class="fa fa-users menu-icon"></i>'];

			//     $data['tab'][] = ['name' => 'contracts', 'icon' => '<i class="fa fa-file-text-o menu-icon"></i>'];

			//     $data['tab'][] = ['name' => 'purchase_order', 'icon' => '<i class="fa fa-cart-plus menu-icon"></i>'];

			//     $data['tab'][] = ['name' => 'payments', 'icon' => '<i class="fa fa-usd menu-icon"></i>']; 

			//     $data['tab'][] = ['name' => 'expenses', 'icon' => '<i class="fa fa-money menu-icon"></i>']; 

			//     $data['tab'][] = ['name' => 'notes', 'icon' => '<i class="fa fa-sticky-note-o menu-icon"></i>'];

			//     $data['tab'][] = ['name' => 'attachments', 'icon' => '<i class="fa fa-paperclip menu-icon"></i>'];



			if ($data['group'] == '') {

				$data['group'] = 'profile';
			}



			// $data['tabs']['view'] = 'vendors/groups/'.$data['group'];

			// die;

			// Fetch data based on groups

			if ($data['group'] == 'profile') {

				$data['customer_admins'] = $this->purchase_model->get_vendor_admins($id);
			} elseif ($group == 'estimates') {

				$this->load->model('estimates_model');

				$data['estimate_statuses'] = $this->estimates_model->get_statuses();
			} elseif ($group == 'notes') {



				$data['user_notes'] = $this->misc_model->get_notes($id, 'pur_vendor');
			} elseif ($group == 'payments') {

				$this->load->model('payment_modes_model');

				$data['payment_modes'] = $this->payment_modes_model->get();
			} elseif ($group == 'attachments') {

				$data['attachments'] = get_all_pur_vendor_attachments($id);
			} elseif ($group == 'expenses') {

				$this->load->model('expenses_model');

				$data['expenses'] = $this->expenses_model->get('', ['vendor' => $id]);
			}



			$data['staff'] = $this->staff_model->get('', ['active' => 1]);



			$data['client'] = $client;

			$title = $client->company;



			// Get all active staff members (used to add reminder)

			$data['members'] = $data['staff'];



			if (!empty($data['client']->company)) {

				// Check if is realy empty client company so we can set this field to empty

				// The query where fetch the client auto populate firstname and lastname if company is empty

				if (is_empty_vendor_company($data['client']->userid)) {

					// $data['client']->company = '';

				}
			}
		}



		$this->load->model('currencies_model');

		$data['currencies'] = $this->currencies_model->get();



		if ($id != '') {

			$customer_currency = $data['client']->default_currency;



			foreach ($data['currencies'] as $currency) {

				if ($customer_currency != 0) {

					if ($currency['id'] == $customer_currency) {

						$customer_currency = $currency;



						break;
					}
				} else {

					if ($currency['isdefault'] == 1) {

						$customer_currency = $currency;



						break;
					}
				}
			}



			if (is_array($customer_currency)) {

				$customer_currency = (object) $customer_currency;
			}



			$data['customer_currency'] = $customer_currency;
		}



		$data['bodyclass'] = 'customer-profile dynamic-create-groups';

		$data['vendor_categories'] = $this->purchase_model->get_vendor_category();

		$data['countries'] = $this->clients_model->get_clients_distinct_countries();

		// $data['staff'] = $this->staff_model->get('', ['active' => 1]);

		$data['state'] = $this->clients_model->getallstate();

		$data['SubGroup'] = $this->clients_model->VendorActSubgroup();

		$data['title'] = $title;

		/*echo "<pre>";

			print_r($data);

		die;*/

		$this->load->view('vendors/vendor', $data);
	}



	/**

	 * { setting }

	 */

	public function setting()
	{

		if (!has_permission_new('purchase', '', 'edit') && !is_admin()) {

			access_denied('purchase');
		}

		$data['group'] = $this->input->get('group');



		$data['title'] = _l('setting');



		$this->db->where('module_name', 'warehouse');

		$module = $this->db->get(db_prefix() . 'modules')->row();

		$data['tab'][] = 'purchase_order_setting';

		$data['tab'][] = 'units';

		$data['tab'][] = 'approval';

		$data['tab'][] = 'commodity_group';

		$data['tab'][] = 'sub_group';

		$data['tab'][] = 'vendor_category';

		if ($data['group'] == '') {

			$data['group'] = 'purchase_order_setting';
		} else if ($data['group'] == 'units') {

			$data['unit_types'] = $this->purchase_model->get_unit_type();
		}

		$data['tabs']['view'] = 'includes/' . $data['group'];

		$data['commodity_group_types'] = $this->purchase_model->get_commodity_group_type();

		$data['sub_groups'] = $this->purchase_model->get_sub_group();

		$data['item_group'] = $this->purchase_model->get_item_group();

		$data['approval_setting'] = $this->purchase_model->get_approval_setting();

		$data['vendor_categories'] = $this->purchase_model->get_vendor_category();

		$data['staffs'] = $this->staff_model->get();



		$this->load->view('manage_setting', $data);
	}



	/**

	 * { assign vendor admins }

	 *

	 * @param      string  $id     The identifier

	 * @return      redirect

	 */

	public function assign_vendor_admins($id)
	{

		if (!has_permission_new('purchase', '', 'create') && !has_permission_new('purchase', '', 'edit')) {

			access_denied('vendors');
		}

		$success = $this->purchase_model->assign_vendor_admins($this->input->post(), $id);

		if ($success == true) {

			set_alert('success', _l('updated_successfully', _l('vendor')));
		}



		redirect(admin_url('purchase/vendor/' . $id . '?tab=vendor_admins'));
	}



	/**

	 * { delete vendor }

	 *

	 * @param      <type>  $id     The identifier

	 * @return      redirect

	 */

	public function delete_vendor($id)
	{

		if (!has_permission_new('purchase', '', 'delete')) {

			access_denied('vendors');
		}

		if (!$id) {

			redirect(admin_url('purchase/vendors'));
		}

		$response = $this->purchase_model->delete_vendor($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('customer_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('client')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('client_lowercase')));
		}

		redirect(admin_url('purchase/vendors'));
	}



	/**

	 * { form contact }

	 *

	 * @param      <type>  $customer_id  The customer identifier

	 * @param      string  $contact_id   The contact identifier

	 */

	public function form_contact($customer_id, $contact_id = '')
	{

		if (!has_permission_new('purchase', '', 'view')) {

			if (!is_customer_admin($customer_id)) {

				echo _l('access_denied');

				die;
			}
		}

		$data['customer_id'] = $customer_id;

		$data['contactid'] = $contact_id;

		if ($this->input->post()) {

			$data = $this->input->post();

			$data['password'] = $this->input->post('password', false);



			unset($data['contactid']);

			if ($contact_id == '') {

				if (!has_permission_new('customers', '', 'create')) {

					if (!is_customer_admin($customer_id)) {

						header('HTTP/1.0 400 Bad error');

						echo json_encode([

							'success' => false,

							'message' => _l('access_denied'),

						]);

						die;
					}
				}

				$id = $this->purchase_model->add_contact($data, $customer_id);

				$message = '';

				$success = false;

				if ($id) {



					$success = true;

					$message = _l('added_successfully', _l('contact'));
				}

				echo json_encode([

					'success' => $success,

					'message' => $message,

					'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

					'is_individual' => is_empty_customer_company($customer_id) && total_rows(db_prefix() . 'pur_contacts', ['userid' => $customer_id]) == 1,

				]);

				die;
			}

			if (!has_permission_new('customers', '', 'edit')) {

				if (!is_customer_admin($customer_id)) {

					header('HTTP/1.0 400 Bad error');

					echo json_encode([

						'success' => false,

						'message' => _l('access_denied'),

					]);

					die;
				}
			}

			$original_contact = $this->purchase_model->get_contact($contact_id);

			$success = $this->purchase_model->update_contact($data, $contact_id);

			$message = '';

			$proposal_warning = false;

			$original_email = '';

			$updated = false;

			if (is_array($success)) {

				if (isset($success['set_password_email_sent'])) {

					$message = _l('set_password_email_sent_to_client');
				} elseif (isset($success['set_password_email_sent_and_profile_updated'])) {

					$updated = true;

					$message = _l('set_password_email_sent_to_client_and_profile_updated');
				}
			} else {

				if ($success == true) {

					$updated = true;

					$message = _l('updated_successfully', _l('contact'));
				}
			}

			if (handle_contact_profile_image_upload($contact_id) && !$updated) {

				$message = _l('updated_successfully', _l('contact'));

				$success = true;
			}

			if ($updated == true) {

				$contact = $this->purchase_model->get_contact($contact_id);

				if (
					total_rows(db_prefix() . 'proposals', [

						'rel_type' => 'customer',

						'rel_id' => $contact->userid,

						'email' => $original_contact->email,

					]) > 0 && ($original_contact->email != $contact->email)
				) {

					$proposal_warning = true;

					$original_email = $original_contact->email;
				}
			}

			echo json_encode([

				'success' => $success,

				'proposal_warning' => $proposal_warning,

				'message' => $message,

				'original_email' => $original_email,

				'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

			]);

			die;
		}

		if ($contact_id == '') {

			$title = _l('add_new', _l('contact_lowercase'));
		} else {

			$data['contact'] = $this->purchase_model->get_contact($contact_id);



			if (!$data['contact']) {

				header('HTTP/1.0 400 Bad error');

				echo json_encode([

					'success' => false,

					'message' => 'Contact Not Found',

				]);

				die;
			}

			$title = $data['contact']->firstname . ' ' . $data['contact']->lastname;
		}





		$data['title'] = $title;

		$this->load->view('vendors/modals/contact', $data);
	}



	/**

	 * { vendor contacts }

	 *

	 * @param      <type>  $client_id  The client identifier

	 */

	public function vendor_contacts($client_id)
	{

		$this->app->get_table_data(module_views_path('purchase', 'vendors/table_contacts'), [

			'client_id' => $client_id,

		]);
	}



	/**

	 * Determines if contact email exists.

	 */

	public function contact_email_exists()
	{

		if ($this->input->is_ajax_request()) {

			if ($this->input->post()) {

				// First we need to check if the email is the same

				$userid = $this->input->post('userid');

				if ($userid != '') {

					$this->db->where('id', $userid);

					$_current_email = $this->db->get(db_prefix() . 'pur_contacts')->row();

					if ($_current_email->email == $this->input->post('email')) {

						echo json_encode(true);

						die();
					}
				}

				$this->db->where('email', $this->input->post('email'));

				$total_rows = $this->db->count_all_results(db_prefix() . 'pur_contacts');

				if ($total_rows > 0) {

					echo json_encode(false);
				} else {

					echo json_encode(true);
				}

				die();
			}
		}
	}



	/**

	 * { delete vendor contact }

	 *

	 * @param      string  $customer_id  The customer identifier

	 * @param      <type>  $id           The identifier

	 * @return     redirect

	 */

	public function delete_vendor_contact($customer_id, $id)
	{

		if (!has_permission_new('purchase', '', 'delete')) {

			if (!is_customer_admin($customer_id)) {

				access_denied('vendors');
			}
		}



		$this->purchase_model->delete_contact($id);



		redirect(admin_url('purchase/vendor/' . $customer_id . '?group=contacts'));
	}





	/**

	 * { all contacts }

	 * @return     view

	 */

	public function all_contacts()
	{

		if ($this->input->is_ajax_request()) {

			$this->app->get_table_data(module_views_path('purchase', 'vendors/table_all_contacts'));
		}



		if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {

			$this->load->model('gdpr_model');

			$data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
		}



		$data['title'] = _l('customer_contacts');

		$this->load->view('vendors/all_contacts', $data);
	}



	/**

	 * { purchase request }

	 * @return     view

	 */

	public function purchase_request()
	{

		$data['title'] = _l('purchase_request');

		$data['vendors'] = $this->purchase_model->get_vendor();

		$this->load->view('purchase_request/manage', $data);
	}



	/**

	 * { add update purchase request }

	 *

	 * @param      string  $id     The identifier

	 * @return    redirect, view

	 */

	public function pur_request($id = '')
	{

		$this->load->model('departments_model');

		$this->load->model('staff_model');

		$this->load->model('projects_model');

		if ($id == '') {



			if ($this->input->post()) {

				$add_data = $this->input->post();

				$id = $this->purchase_model->add_pur_request($add_data);

				if ($id) {

					set_alert('success', _l('added_pur_request'));
				}

				redirect(admin_url('purchase/purchase_request'));
			}



			$data['title'] = _l('add_new');
		} else {

			if ($this->input->post()) {

				$edit_data = $this->input->post();

				$success = $this->purchase_model->update_pur_request($edit_data, $id);

				if ($success == true) {

					set_alert('success', _l('updated_pur_request'));
				}

				redirect(admin_url('purchase/purchase_request'));
			}



			$data['pur_request_detail'] = json_encode($this->purchase_model->get_pur_request_detail($id));

			$data['pur_request'] = $this->purchase_model->get_purchase_request($id);

			$data['title'] = _l('edit');
		}



		$data['projects'] = $this->projects_model->get();

		$data['staffs'] = $this->staff_model->get();

		$data['departments'] = $this->departments_model->get();

		$data['units'] = $this->purchase_model->get_units();

		$data['items'] = $this->purchase_model->get_items();



		$this->load->view('purchase_request/pur_request', $data);
	}



	/**

	 * { view pur request }

	 *

	 * @param      <type>  $id     The identifier

	 * @return view

	 */

	public function view_pur_request($id)
	{

		$this->load->model('departments_model');



		$send_mail_approve = $this->session->userdata("send_mail_approve");

		if ((isset($send_mail_approve)) && $send_mail_approve != '') {

			$data['send_mail_approve'] = $send_mail_approve;

			$this->session->unset_userdata("send_mail_approve");
		}

		$data['pur_request_detail'] = json_encode($this->purchase_model->get_pur_request_detail($id));

		$data['pur_request'] = $this->purchase_model->get_purchase_request($id);

		$data['title'] = $data['pur_request']->pur_rq_name;

		$data['departments'] = $this->departments_model->get();

		$data['units'] = $this->purchase_model->get_units();

		$data['items'] = $this->purchase_model->get_items();



		$data['check_appr'] = $this->purchase_model->get_approve_setting('pur_request');

		$data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'pur_request');

		$data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'pur_request');

		$data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'pur_request');



		$this->load->view('purchase_request/view_pur_request', $data);
	}



	/**

	 * { approval setting }

	 * @return redirect

	 */

	public function approval_setting()
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			if ($data['approval_setting_id'] == '') {

				$message = '';

				$success = $this->purchase_model->add_approval_setting($data);

				if ($success) {

					$message = _l('added_successfully', _l('approval_setting'));
				}

				set_alert('success', $message);

				redirect(admin_url('purchase/setting?group=approval'));
			} else {

				$message = '';

				$id = $data['approval_setting_id'];

				$success = $this->purchase_model->edit_approval_setting($id, $data);

				if ($success) {

					$message = _l('updated_successfully', _l('approval_setting'));
				}

				set_alert('success', $message);

				redirect(admin_url('purchase/setting?group=approval'));
			}
		}
	}



	/**

	 * { delete approval setting }

	 *

	 * @param      <type>  $id     The identifier

	 * @return redirect

	 */

	public function delete_approval_setting($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/setting?group=approval'));
		}

		$response = $this->purchase_model->delete_approval_setting($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('approval_setting')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('approval_setting')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('approval_setting')));
		}

		redirect(admin_url('purchase/setting?group=approval'));
	}



	/**

	 * { items change event}

	 *

	 * @param      <type>  $val    The value

	 * @return      json

	 */

	public function items_change($val)
	{



		$value = $this->purchase_model->items_change($val);

		$selected_company = $this->session->userdata('root_company');



		echo json_encode([

			'value' => $value

		]);
	}



	/**

	 * { table pur request }

	 */

	public function table_pur_request()
	{

		$this->app->get_table_data(module_views_path('purchase', 'purchase_request/table_pur_request'));
	}



	/**

	 * { delete pur request }

	 *

	 * @param      <type>  $id     The identifier

	 * @return     redirect

	 */

	public function delete_pur_request($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/purchase_request'));
		}

		$response = $this->purchase_model->delete_pur_request($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('purchase_request')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('purchase_request')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('purchase_request')));
		}

		redirect(admin_url('purchase/purchase_request'));
	}



	/**

	 * { change status pur request }

	 *

	 * @param      <type>  $status  The status

	 * @param      <type>  $id      The identifier

	 * @return     json

	 */

	public function change_status_pur_request($status, $id)
	{

		$change = $this->purchase_model->change_status_pur_request($status, $id);

		if ($change == true) {



			$message = _l('change_status_pur_request') . ' ' . _l('successfully');

			echo json_encode([

				'result' => $message,

			]);
		} else {

			$message = _l('change_status_pur_request') . ' ' . _l('fail');

			echo json_encode([

				'result' => $message,

			]);
		}
	}



	/**

	 * { quotations }

	 *

	 * @param      string  $id     The identifier

	 * @return     view

	 */

	public function quotations($id = '')
	{

		if (!has_permission_new('purchase', '', 'view') && !has_permission_new('purchase', '', 'view_own')) {

			access_denied('quotations');
		}



		// Pipeline was initiated but user click from home page and need to show table only to filter

		if ($this->input->get('status') || $this->input->get('filter') && $isPipeline) {

			$this->pipeline(0, true);
		}



		$data['estimateid'] = $id;

		$data['pur_request'] = $this->purchase_model->get_purchase_request();

		$data['vendors'] = $this->purchase_model->get_vendor();

		$data['title'] = _l('estimates');

		$data['bodyclass'] = 'estimates-total-manual';



		$this->load->view('quotations/manage', $data);
	}



	/**

	 * { function_description }

	 *

	 * @param      string  $id     The identifier

	 * @return     redirect

	 */

	public function estimate($id = '')
	{

		if ($this->input->post()) {

			$estimate_data = $this->input->post();

			$estimate_data['terms'] = nl2br($estimate_data['terms']);

			if ($id == '') {

				if (!has_permission_new('purchase', '', 'create')) {

					access_denied('quotations');
				}

				$id = $this->purchase_model->add_estimate($estimate_data);

				if ($id) {

					set_alert('success', _l('added_successfully', _l('estimate')));



					redirect(admin_url('purchase/quotations/' . $id));
				}
			} else {

				if (!has_permission_new('vendors', '', 'edit')) {

					access_denied('quotations');
				}

				$success = $this->purchase_model->update_estimate($estimate_data, $id);

				if ($success) {

					set_alert('success', _l('updated_successfully', _l('estimate')));
				}

				redirect(admin_url('purchase/quotations/' . $id));
			}
		}

		if ($id == '') {

			$title = _l('create_new_estimate');
		} else {

			$estimate = $this->purchase_model->get_estimate($id);





			$data['estimate_detail'] = json_encode($this->purchase_model->get_pur_estimate_detail($id));

			$data['estimate'] = $estimate;

			$data['edit'] = true;

			$title = _l('edit', _l('estimate_lowercase'));
		}

		if ($this->input->get('customer_id')) {

			$data['customer_id'] = $this->input->get('customer_id');
		}

		$this->load->model('taxes_model');

		$data['taxes'] = $this->purchase_model->get_taxes();

		$this->load->model('currencies_model');

		$data['currencies'] = $this->currencies_model->get();



		$data['base_currency'] = $this->currencies_model->get_base_currency();



		$this->load->model('invoice_items_model');



		$data['ajaxItems'] = false;

		if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {

			$data['items'] = $this->invoice_items_model->get_grouped();
		} else {

			$data['items'] = [];

			$data['ajaxItems'] = true;
		}

		$data['items_groups'] = $this->invoice_items_model->get_groups();



		$data['staff'] = $this->staff_model->get('', ['active' => 1]);

		$data['vendors'] = $this->purchase_model->get_vendor();

		$data['pur_request'] = $this->purchase_model->get_pur_request_by_status(2);

		$data['units'] = $this->purchase_model->get_units();

		$data['items'] = $this->purchase_model->get_items();

		$data['title'] = $title;

		$this->load->view('quotations/estimate', $data);
	}



	/**

	 * { validate estimate number }

	 */

	public function validate_estimate_number()
	{

		$isedit = $this->input->post('isedit');

		$number = $this->input->post('number');

		$date = $this->input->post('date');

		$original_number = $this->input->post('original_number');

		$number = trim($number);

		$number = ltrim($number, '0');



		if ($isedit == 'true') {

			if ($number == $original_number) {

				echo json_encode(true);

				die;
			}
		}



		if (
			total_rows(db_prefix() . 'pur_estimates', [

				'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),

				'number' => $number,

			]) > 0
		) {

			echo 'false';
		} else {

			echo 'true';
		}
	}



	/**

	 * { table estimates }

	 */

	public function table_estimates()
	{

		$this->app->get_table_data(module_views_path('purchase', 'quotations/table_estimates'));
	}



	/**

	 * Gets the estimate data ajax.

	 *

	 * @param      <type>   $id         The identifier

	 * @param      boolean  $to_return  To return

	 *

	 * @return     <type>   view.

	 */

	public function get_estimate_data_ajax($id, $to_return = false)
	{

		if (!has_permission_new('purchase', '', 'view') && !has_permission_new('purchase', '', 'view_own')) {

			echo _l('access_denied');

			die;
		}



		if (!$id) {

			die('No estimate found');
		}



		$estimate = $this->purchase_model->get_estimate($id);



		$estimate->date = _d($estimate->date);

		$estimate->expirydate = _d($estimate->expirydate);





		if ($estimate->sent == 0) {

			$template_name = 'estimate_send_to_customer';
		} else {

			$template_name = 'estimate_send_to_customer_already_sent';
		}



		$data['pur_estimate_attachments'] = $this->purchase_model->get_purchase_estimate_attachments($id);

		$data['estimate_detail'] = $this->purchase_model->get_pur_estimate_detail($id);

		$data['estimate'] = $estimate;

		$data['members'] = $this->staff_model->get('', ['active' => 1]);



		$send_mail_approve = $this->session->userdata("send_mail_approve");

		if ((isset($send_mail_approve)) && $send_mail_approve != '') {

			$data['send_mail_approve'] = $send_mail_approve;

			$this->session->unset_userdata("send_mail_approve");
		}

		$data['check_appr'] = $this->purchase_model->get_approve_setting('pur_quotation');

		$data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'pur_quotation');

		$data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'pur_quotation');

		$data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'pur_quotation');



		if ($to_return == false) {

			$this->load->view('quotations/estimate_preview_template', $data);
		} else {

			return $this->load->view('quotations/estimate_preview_template', $data, true);
		}
	}



	/**

	 * { delete estimate }

	 *

	 * @param      <type>  $id     The identifier

	 * @return     redirect

	 */

	public function delete_estimate($id)
	{

		if (!has_permission_new('purchase', '', 'delete')) {

			access_denied('estimates');
		}

		if (!$id) {

			redirect(admin_url('purchase/quotations'));
		}

		$success = $this->purchase_model->delete_estimate($id);

		if (is_array($success)) {

			set_alert('warning', _l('is_invoiced_estimate_delete_error'));
		} elseif ($success == true) {

			set_alert('success', _l('deleted', _l('estimate')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('estimate_lowercase')));
		}

		redirect(admin_url('purchase/quotations'));
	}



	/**

	 * { tax change event }

	 *

	 * @param      <type>  $tax    The tax

	 * @return   json

	 */

	public function tax_change($tax)
	{

		$taxes = explode('%7C', $tax);

		$total_tax = $this->purchase_model->get_total_tax($taxes);



		echo json_encode([

			'total_tax' => $total_tax,

		]);
	}





	/**

	 * { coppy pur request }

	 *

	 * @param      <type>  $pur_request  The purchase request id

	 * @return json

	 */

	public function coppy_pur_request($pur_request)
	{

		$pur_request_detail = $this->purchase_model->get_pur_request_detail_in_estimate($pur_request);

		echo json_encode([

			'result' => $pur_request_detail,

		]);
	}



	/**

	 * { coppy pur estimate }

	 *

	 * @param      <type>  $pur_estimate  The purchase estimate id

	 * @return  json

	 */

	public function coppy_pur_estimate($pur_estimate)
	{

		$pur_estimate_detail = $this->purchase_model->get_pur_estimate_detail_in_order($pur_estimate);

		$pur_estimate = $this->purchase_model->get_estimate($pur_estimate);

		echo json_encode([

			'result' => $pur_estimate_detail,

			'dc_percent' => $pur_estimate->discount_percent,

			'dc_total' => $pur_estimate->discount_total,

		]);
	}



	/**

	 * { view purchase order }

	 *

	 * @param      <type>  $pur_order  The purchase order id

	 * @return json

	 */

	public function view_pur_order($pur_order)
	{

		$pur_order_detail = $this->purchase_model->get_pur_order_detail($pur_order);

		$pur_order = $this->purchase_model->get_pur_order($pur_order);



		echo json_encode([

			'total' => app_format_money($pur_order->total, ''),

			'vendor' => $pur_order->vendor,

			'buyer' => $pur_order->buyer,

		]);
	}



	/**

	 * { change status pur estimate }

	 *

	 * @param      <type>  $status  The status

	 * @param      <type>  $id      The identifier

	 * @return json

	 */

	public function change_status_pur_estimate($status, $id)
	{

		$change = $this->purchase_model->change_status_pur_estimate($status, $id);

		if ($change == true) {



			$message = _l('change_status_pur_estimate') . ' ' . _l('successfully');

			echo json_encode([

				'result' => $message,

			]);
		} else {

			$message = _l('change_status_pur_estimate') . ' ' . _l('fail');

			echo json_encode([

				'result' => $message,

			]);
		}
	}



	/**

	 * { change status pur order }

	 *

	 * @param      <type>  $status  The status

	 * @param      <type>  $id      The identifier

	 * @return json

	 */

	public function change_status_pur_order($status, $id)
	{

		$change = $this->purchase_model->change_status_pur_order($status, $id);

		if ($change == true) {



			$message = _l('change_status_pur_order') . ' ' . _l('successfully');

			echo json_encode([

				'result' => $message,

			]);
		} else {

			$message = _l('change_status_pur_order') . ' ' . _l('fail');

			echo json_encode([

				'result' => $message,

			]);
		}
	}



	/**

	 * { purchase order }

	 *

	 * @param      string  $id     The identifier

	 * @return view

	 */

	public function purchase_order($id = '')
	{

		$this->load->model('expenses_model');

		$this->load->model('payment_modes_model');

		$this->load->model('taxes_model');

		$this->load->model('currencies_model');

		$this->load->model('departments_model');

		$this->load->model('projects_model');



		$data['pur_orderid'] = $id;

		$data['title'] = _l('purchase_order');



		$data['departments'] = $this->departments_model->get();

		$data['projects'] = $this->projects_model->get();

		$data['currency'] = $this->currencies_model->get_base_currency();

		$data['payment_modes'] = $this->payment_modes_model->get('', [], true);

		$data['currencies'] = $this->currencies_model->get();

		$data['taxes'] = $this->taxes_model->get();

		$data['vendors'] = $this->purchase_model->get_vendor();

		$data['expense_categories'] = $this->expenses_model->get_category();

		$data['item_tags'] = $this->purchase_model->get_item_tag_filter();



		$this->load->view('purchase_order/manage', $data);
	}



	/**

	 * Gets the pur order data ajax.

	 *

	 * @param      <type>   $id         The identifier

	 * @param      boolean  $to_return  To return

	 *

	 * @return     view.

	 */

	public function get_pur_order_data_ajax($id, $to_return = false)
	{

		if (!has_permission_new('purchase', '', 'view') && !has_permission_new('purchase', '', 'view_own')) {

			echo _l('access_denied');

			die;
		}



		if (!$id) {

			die('No purchase order found');
		}



		$estimate = $this->purchase_model->get_pur_order($id);



		$this->load->model('payment_modes_model');

		$data['payment_modes'] = $this->payment_modes_model->get('', [

			'expenses_only !=' => 1,

		]);



		$data['payment'] = $this->purchase_model->get_payment_purchase_order($id);

		$data['pur_order_attachments'] = $this->purchase_model->get_purchase_order_attachments($id);

		$data['estimate_detail'] = $this->purchase_model->get_pur_order_detail($id);

		$data['estimate'] = $estimate;

		$data['members'] = $this->staff_model->get('', ['active' => 1]);



		$send_mail_approve = $this->session->userdata("send_mail_approve");

		if ((isset($send_mail_approve)) && $send_mail_approve != '') {

			$data['send_mail_approve'] = $send_mail_approve;

			$this->session->unset_userdata("send_mail_approve");
		}

		$data['check_appr'] = $this->purchase_model->get_approve_setting('pur_order');

		$data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'pur_order');

		$data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'pur_order');

		$data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'pur_order');



		if ($to_return == false) {

			$this->load->view('purchase_order/pur_order_preview', $data);
		} else {

			return $this->load->view('purchase_order/pur_order_preview', $data, true);
		}
	}



	/**

	 * { purchase order form }

	 *

	 * @param      string  $id     The identifier

	 * @return redirect, view

	 */

	public function PurchaseEntry($id = '')
	{

		if ($this->input->post()) {

			$pur_order_data = $this->input->post();

			$pur_order_data['terms'] = nl2br($pur_order_data['terms']);

			if ($id == '') {

				if (!has_permission_new('purchase-order', '', 'create')) {

					access_denied('purchase_order');
				}

				$id = $this->purchase_model->add_pur_order_new($pur_order_data);

				if ($id) {

					set_alert('success', _l('added_successfully', _l('pur_order')));

					redirect(admin_url('purchase/PurchaseEntry'));
				}
			}
		}



		if ($id == '') {

			$title = _l('create_new_pur_order');
		}

		$this->load->model('currencies_model');

		$data['base_currency'] = $this->currencies_model->get_base_currency();



		$this->load->model('clients_model');



		$this->load->model('departments_model');

		$data['departments'] = $this->departments_model->get();



		$data['taxes'] = $this->purchase_model->get_taxes();

		$data['staff'] = $this->staff_model->get('', ['active' => 1]);

		$data['vendors'] = $this->purchase_model->GetRMVendor();



		$data['GodownData'] = $this->purchase_model->GetGodownData();

		$data['pendingOrder_list'] = $this->purchase_model->pendingOrder_list();

		$data['Order_list'] = $this->purchase_model->get_Order_list();



		$data['units'] = $this->purchase_model->get_units();

		$data['items'] = $this->purchase_model->get_items();

		$data['item_code'] = $this->purchase_model->get_items_code();

		$data['accounts'] = $this->purchase_model->get_accounts_list();

		$data['freight_id'] = $this->purchase_model->get_accounts_freightid();

		$data['other_id'] = $this->purchase_model->get_accounts_othertid();

		$data['acc_head'] = $this->purchase_model->get_acc_head();

		$data['title'] = $title;



		$this->load->view('purchase_order/pur_order', $data);
	}

	public function account_change_by_AccountID($val)
	{



		$value = $this->purchase_model->account_change_by_AccountID($val);

		$selected_company = $this->session->userdata('root_company');



		echo json_encode([

			'value' => $value

		]);
	}

	public function get_accounts_freightid($id)
	{

		$items = $this->purchase_model->get_accounts_freightid($id);

		echo json_encode([

			'items' => $items,

		]);
	}

	public function get_accounts_othertid($id)
	{

		$items = $this->purchase_model->get_accounts_othertid($id);

		echo json_encode([

			'items' => $items,

		]);
	}

	/**

	 * { delete pur order }

	 *

	 * @param      <type>  $id     The identifier

	 * @return redirect

	 */

	public function delete_pur_order($id)
	{

		if (!has_permission_new('purchase', '', 'delete')) {

			access_denied('purchase_order');
		}

		if (!$id) {

			redirect(admin_url('purchase/purchase_order'));
		}

		$success = $this->purchase_model->delete_pur_order($id);

		if (is_array($success)) {

			set_alert('warning', _l('purchase_order'));
		} elseif ($success == true) {

			set_alert('success', _l('deleted', _l('purchase_order')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('purchase_order')));
		}

		redirect(admin_url('purchase/purchase_order'));
	}



	/**

	 * { estimate by vendor }

	 *

	 * @param      <type>  $vendor  The vendor

	 * @return json

	 */



	public function items_vendor_check($item, $vendor)
	{

		$items = $this->purchase_model->get_items_by_vendor_data($item, $vendor);

		echo json_encode([

			'items' => $items,

		]);
	}

	public function items_purchaseid_check($item, $vendor)
	{

		$data = $this->purchase_model->items_purchaseid_check($item, $vendor);

		echo json_encode($data);
	}

	public function items_vendor_check_tcs($id)
	{

		$vendor = $this->purchase_model->items_vendor_check_tcs($id);



		if ($vendor->istcs > 0) {

			$rResult = $this->db->get(db_prefix() . 'tcsmaster')->result_array();

			foreach ($rResult as $aRow) {



				$date = substr($aRow['EffDate'], 0, 10);

				$cur_date = date('Y-m-d');

				if ($date <= $cur_date) {

					$active = "Active";

					$tcs_id = $aRow['id'];

					$tcs_prec = $aRow['tcs'];
				}
			}
		} else {

			$tcs_prec = '';
		}



		echo json_encode([



			'vendor' => $vendor,

			'tcs_prec' => $tcs_prec,

		]);
	}

	public function estimate_by_vendor($vendor)
	{

		$estimate = $this->purchase_model->estimate_by_vendor($vendor);

		$ven = $this->purchase_model->get_vendor($vendor);

		$items = $this->purchase_model->get_items_by_vendor($vendor);

		$vendor_data = '';

		$html = '<option value=""></option>';

		$company = '';

		foreach ($estimate as $es) {

			$html .= '<option value="' . $es['id'] . '">' . format_pur_estimate_number($es['id']) . '</option>';
		}

		if ($ven) {

			$vendor_data .= '<div class="col-md-6">';

			$vendor_data .= '<p class="bold p_style">' . _l('vendor_detail') . '</p>

				<hr class="hr_style"/>';

			$vendor_data .= '<table class="table table-striped table-bordered"><tbody>';

			$vendor_data .= '<tr><td>' . _l('company') . '</td><td>' . $ven->company . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('client_vat_number') . '</td><td>' . $ven->vat . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('client_phonenumber') . '</td><td>' . $ven->phonenumber . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('website') . '</td><td>' . $ven->website . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('vendor_category') . '</td><td>' . get_vendor_category_html($ven->category) . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('client_address') . '</td><td>' . $ven->address . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('client_city') . '</td><td>' . $ven->city . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('client_state') . '</td><td>' . $ven->state . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('client_postal_code') . '</td><td>' . $ven->zip . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('clients_country') . '</td><td>' . get_country_short_name($ven->country) . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('bank_detail') . '</td><td>' . $ven->bank_detail . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('payment_terms') . '</td><td>' . $ven->payment_terms . '</td></tr>';

			$vendor_data .= '</tbody></table>';

			$vendor_data .= '</div>';



			$vendor_data .= '<div class="col-md-6">';

			$vendor_data .= '<p class="bold p_style">' . _l('billing_address') . '</p>

				<hr class="hr_style"/>';

			$vendor_data .= '<table class="table table-striped table-bordered"><tbody>';

			$vendor_data .= '<tr><td>' . _l('billing_street') . '</td><td>' . $ven->billing_street . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('billing_city') . '</td><td>' . $ven->billing_city . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('billing_state') . '</td><td>' . $ven->billing_state . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('billing_zip') . '</td><td>' . $ven->billing_zip . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('billing_country') . '</td><td>' . get_country_short_name($ven->billing_country) . '</td></tr>';

			$vendor_data .= '</tbody></table>';

			$vendor_data .= '<p class="bold p_style">' . _l('shipping_address') . '</p>

				<hr class="hr_style"/>';

			$vendor_data .= '<table class="table table-striped table-bordered"><tbody>';

			$vendor_data .= '<tr><td>' . _l('shipping_street') . '</td><td>' . $ven->shipping_street . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('shipping_city') . '</td><td>' . $ven->shipping_city . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('shipping_state') . '</td><td>' . $ven->shipping_state . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('shipping_zip') . '</td><td>' . $ven->shipping_zip . '</td></tr>';

			$vendor_data .= '<tr><td>' . _l('shipping_country') . '</td><td>' . get_country_short_name($ven->shipping_country) . '</td></tr>';

			$vendor_data .= '</tbody></table>';

			$vendor_data .= '</div>';



			if ($ven->vendor_code != '') {

				$company = $ven->vendor_code;
			}
		}





		echo json_encode([

			'result' => $html,

			'ven_html' => $vendor_data,

			'company' => $company,

			'items' => $items,

		]);
	}



	/**

	 * { table pur order }

	 */

	public function table_pur_order()
	{

		$this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'));
	}



	/**

	 * { contracts }

	 * @return  view

	 */

	public function contracts()
	{

		$this->load->model('departments_model');

		$data['departments'] = $this->departments_model->get();

		$this->load->model('projects_model');

		$data['projects'] = $this->projects_model->get();

		$data['vendors'] = $this->purchase_model->get_vendor();

		$data['title'] = _l('contracts');

		$this->load->view('contracts/manage', $data);
	}



	/**

	 * { contract }

	 *

	 * @param      string  $id     The identifier

	 * @return redirect , view

	 */

	public function contract($id = '')
	{

		if ($this->input->post()) {

			$contract_data = $this->input->post();

			if ($id == '') {



				$id = $this->purchase_model->add_contract($contract_data);

				if ($id) {

					handle_pur_contract_file($id);

					set_alert('success', _l('added_successfully', _l('contract')));



					redirect(admin_url('purchase/contracts'));
				}
			} else {

				handle_pur_contract_file($id);

				$success = $this->purchase_model->update_contract($contract_data, $id);

				if ($success) {

					set_alert('success', _l('updated_successfully', _l('pur_order')));
				}

				redirect(admin_url('purchase/contract/' . $id));
			}
		}



		if ($id == '') {

			$title = _l('create_new_contract');
		} else {

			$data['contract'] = $this->purchase_model->get_contract($id);

			$data['attachments'] = $this->purchase_model->get_pur_contract_attachment($id);

			$data['payment'] = $this->purchase_model->get_payment_by_contract($id);

			$title = _l('contract_detail');
		}

		$this->load->model('departments_model');

		$data['departments'] = $this->departments_model->get();

		$this->load->model('projects_model');

		$data['projects'] = $this->projects_model->get();

		$data['ven'] = $this->input->get('vendor');

		$data['pur_orders'] = $this->purchase_model->get_pur_order_approved();

		$data['taxes'] = $this->purchase_model->get_taxes();

		$data['staff'] = $this->staff_model->get('', ['active' => 1]);

		$data['members'] = $this->staff_model->get('', ['active' => 1]);

		$data['vendors'] = $this->purchase_model->get_vendor();

		$data['units'] = $this->purchase_model->get_units();

		$data['items'] = $this->purchase_model->get_items();

		$data['title'] = $title;



		$this->load->view('contracts/contract', $data);
	}



	/**

	 * { delete contract }

	 *

	 * @param      <type>  $id     The identifier

	 * @return redirect

	 */

	public function delete_contract($id)
	{

		if (!has_permission_new('purchase', '', 'delete')) {

			access_denied('contracts');
		}

		if (!$id) {

			redirect(admin_url('purchase/contracts'));
		}

		$success = $this->purchase_model->delete_contract($id);

		if (is_array($success)) {

			set_alert('warning', _l('contracts'));
		} elseif ($success == true) {

			set_alert('success', _l('deleted', _l('contracts')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('contracts')));
		}

		redirect(admin_url('purchase/contracts'));
	}



	/**

	 * Determines if contract number exists.

	 */

	public function contract_number_exists()
	{

		if ($this->input->is_ajax_request()) {

			if ($this->input->post()) {

				// First we need to check if the email is the same

				$contract = $this->input->post('contract');

				if ($contract != '') {

					$this->db->where('id', $contract);

					$cd = $this->db->get('tblpur_contracts')->row();

					if ($cd->contract_number == $this->input->post('contract_number')) {

						echo json_encode(true);

						die();
					}
				}

				$this->db->where('contract_number', $this->input->post('contract_number'));

				$total_rows = $this->db->count_all_results('tblpur_contracts');

				if ($total_rows > 0) {

					echo json_encode(false);
				} else {

					echo json_encode(true);
				}

				die();
			}
		}
	}



	/**

	 * { table contracts }

	 */

	public function table_contracts()
	{

		$this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'));
	}



	/**

	 * Saves a contract data.

	 * @return  json

	 */

	public function save_contract_data()
	{

		if (!has_permission_new('purchase', '', 'edit') && !has_permission_new('purchase', '', 'create')) {

			header('HTTP/1.0 400 Bad error');

			echo json_encode([

				'success' => false,

				'message' => _l('access_denied'),

			]);

			die;
		}



		$success = false;

		$message = '';



		$this->db->where('id', $this->input->post('contract_id'));

		$this->db->update(db_prefix() . 'pur_contracts', [

			'content' => $this->input->post('content', false),

		]);



		$success = $this->db->affected_rows() > 0;

		$message = _l('updated_successfully', _l('contract'));



		echo json_encode([

			'success' => $success,

			'message' => $message,

		]);
	}



	/**

	 * { pdf contract }

	 *

	 * @param      <type>  $id     The identifier

	 * @return pdf output

	 */

	public function pdf_contract($id)
	{

		if (!has_permission_new('purchase', '', 'view') && !has_permission_new('purchase', '', 'view_own')) {

			access_denied('contracts');
		}



		if (!$id) {

			redirect(admin_url('purchase/contracts'));
		}



		$contract = $this->purchase_model->get_contract($id);

		$pdf = pur_contract_pdf($contract);



		$type = 'D';



		if ($this->input->get('output_type')) {

			$type = $this->input->get('output_type');
		}



		if ($this->input->get('print')) {

			$type = 'I';
		}



		$pdf->Output(slug_it($contract->contract_number) . '.pdf', $type);
	}



	/**

	 * { sign contract }

	 *

	 * @param      <type>  $contract  The contract

	 * @return json

	 */

	public function sign_contract($contract)
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			$success = $this->purchase_model->sign_contract($contract, $data['status']);

			$message = '';

			if ($success == true) {

				process_digital_signature_image($data['signature'], PURCHASE_MODULE_UPLOAD_FOLDER . '/contract_sign/' . $contract);

				$message = _l('sign_successfully');
			}



			echo json_encode([

				'success' => $success,

				'message' => $message,

			]);
		}
	}



	/**

	 * Sends a request approve.

	 * @return  json

	 */

	public function send_request_approve()
	{

		$data = $this->input->post();

		$message = 'Send request approval fail';

		$success = $this->purchase_model->send_request_approve($data);

		if ($success === true) {

			$message = 'Send request approval success';

			$data_new = [];

			$data_new['send_mail_approve'] = $data;

			$this->session->set_userdata($data_new);
		} elseif ($success === false) {

			$message = _l('no_matching_process_found');

			$success = false;
		} else {

			$message = _l('could_not_find_approver_with', _l($success));

			$success = false;
		}

		echo json_encode([

			'success' => $success,

			'message' => $message,

		]);

		die;
	}



	/**

	 * Sends a mail.

	 * @return json

	 */

	public function send_mail()
	{

		if ($this->input->is_ajax_request()) {

			$data = $this->input->post();

			if ((isset($data)) && $data != '') {

				$this->purchase_model->send_mail($data);



				$success = 'success';

				echo json_encode([

					'success' => $success,

				]);
			}
		}
	}



	/**

	 * { approve request }

	 * @return json

	 */

	public function approve_request()
	{

		$data = $this->input->post();

		$data['staff_approve'] = get_staff_user_id();

		$success = false;

		$code = '';

		$signature = '';



		if (isset($data['signature'])) {

			$signature = $data['signature'];

			unset($data['signature']);
		}

		$status_string = 'status_' . $data['approve'];

		$check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);



		if (isset($data['approve']) && in_array(get_staff_user_id(), $check_approve_status['staffid'])) {



			$success = $this->purchase_model->update_approval_details($check_approve_status['id'], $data);



			$message = _l('approved_successfully');



			if ($success) {

				if ($data['approve'] == 2) {

					$message = _l('approved_successfully');

					$data_log = [];



					if ($signature != '') {

						$data_log['note'] = "signed_request";
					} else {

						$data_log['note'] = "approve_request";
					}

					if ($signature != '') {

						switch ($data['rel_type']) {

							case 'payment_request':

								$path = PURCHASE_MODULE_UPLOAD_FOLDER . '/payment_invoice/signature/' . $data['rel_id'];

								break;

							case 'pur_order':

								$path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/signature/' . $data['rel_id'];

								break;

							case 'pur_request':

								$path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_request/signature/' . $data['rel_id'];

								break;

							case 'pur_quotation':

								$path = PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/signature/' . $data['rel_id'];

								break;

							default:

								$path = PURCHASE_MODULE_UPLOAD_FOLDER;

								break;
						}

						purchase_process_digital_signature_image($signature, $path, 'signature_' . $check_approve_status['id']);

						$message = _l('sign_successfully');
					}







					$check_approve_status = $this->purchase_model->check_approval_details($data['rel_id'], $data['rel_type']);

					if ($check_approve_status === true) {

						$this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], 2);
					}
				} else {

					$message = _l('rejected_successfully');



					$this->purchase_model->update_approve_request($data['rel_id'], $data['rel_type'], '3');
				}
			}
		}



		$data_new = [];

		$data_new['send_mail_approve'] = $data;

		$this->session->set_userdata($data_new);

		echo json_encode([

			'success' => $success,

			'message' => $message,

		]);

		die();
	}



	/**

	 * Sends a request quotation.

	 * @return redirect

	 */

	public function send_request_quotation()
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			foreach ($data['vendor'] as $id) {

				$vendor = $this->purchase_model->get_primary_contacts($id);

				$data['email'][] = $vendor->email;
			}



			if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {



				if (file_exists(PURCHASE_MODULE_UPLOAD_FOLDER . '/request_quotation/' . $data['pur_request_id'])) {

					$delete_old = delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/request_quotation/' . $data['pur_request_id']);
				} else {

					$delete_old = true;
				}



				if ($delete_old == true) {

					handle_request_quotation($data['pur_request_id']);
				}
			}



			$send = $this->purchase_model->send_request_quotation($data);

			if ($send == true) {

				set_alert('success', _l('send_request_quotation_successfully'));
			} else {

				set_alert('warning', _l('send_request_quotation_fail'));
			}

			redirect(admin_url('purchase/purchase_request'));
		}
	}



	/**

	 * { purchase request pdf }

	 *

	 * @param      <type>  $id     The identifier

	 * @return pdf output

	 */

	public function pur_request_pdf($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/purchase_request'));
		}



		$pur_request = $this->purchase_model->get_pur_request_pdf_html($id);



		try {

			$pdf = $this->purchase_model->pur_request_pdf($pur_request);
		} catch (Exception $e) {

			echo html_entity_decode($e->getMessage());

			die;
		}



		$type = 'D';



		if ($this->input->get('output_type')) {

			$type = $this->input->get('output_type');
		}



		if ($this->input->get('print')) {

			$type = 'I';
		}



		$pdf->Output('purchase_request.pdf', $type);
	}



	/**

	 * { request quotation pdf }

	 *

	 * @param      <type>  $id     The identifier

	 * @return pdf output

	 */

	public function request_quotation_pdf($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/purchase_request'));
		}



		$pur_request = $this->purchase_model->get_request_quotation_pdf_html($id);



		try {

			$pdf = $this->purchase_model->request_quotation_pdf($pur_request);
		} catch (Exception $e) {

			echo html_entity_decode($e->getMessage());

			die;
		}



		$type = 'D';



		if ($this->input->get('output_type')) {

			$type = $this->input->get('output_type');
		}



		if ($this->input->get('print')) {

			$type = 'I';
		}



		$pdf->Output('request_quotation.pdf', $type);
	}



	/**

	 * { purchase order setting }

	 * @return  json

	 */

	public function purchase_order_setting()
	{

		$data = $this->input->post();

		if ($data != 'null') {

			$value = $this->purchase_model->update_purchase_setting($data);

			if ($value) {

				$success = true;

				$message = _l('updated_successfully');
			} else {

				$success = false;

				$message = _l('updated_false');
			}

			echo json_encode([

				'message' => $message,

				'success' => $success,

			]);

			die;
		}
	}



	/**

	 * { purchase order setting }

	 * @return  json

	 */

	public function item_by_vendor()
	{

		$data = $this->input->post();

		if ($data != 'null') {

			$value = $this->purchase_model->update_purchase_setting($data);

			if ($value) {

				$success = true;

				$message = _l('updated_successfully');
			} else {

				$success = false;

				$message = _l('updated_false');
			}

			echo json_encode([

				'message' => $message,

				'success' => $success,

			]);

			die;
		}
	}



	/**

	 * Gets the notes.

	 *

	 * @param      <type>  $id     The id of purchase order

	 */

	public function get_notes($id)
	{

		$data['notes'] = $this->misc_model->get_notes($id, 'purchase_order');

		$this->load->view('admin/includes/sales_notes_template', $data);
	}



	/**

	 * Gets the purchase contract notes.

	 *

	 * @param      <type>  $id     The id of purchase order

	 */

	public function get_notes_pur_contract($id)
	{

		$data['notes'] = $this->misc_model->get_notes($id, 'pur_contract');

		$this->load->view('admin/includes/sales_notes_template', $data);
	}



	/**

	 * Gets the purchase invoice notes.

	 *

	 * @param      <type>  $id     The id of purchase order

	 */

	public function get_notes_pur_invoice($id)
	{

		$data['notes'] = $this->misc_model->get_notes($id, 'pur_invoice');

		$this->load->view('admin/includes/sales_notes_template', $data);
	}



	/**

	 * Adds a note.

	 *

	 * @param        $rel_id  The purchase contract id

	 */

	public function add_pur_contract_note($rel_id)
	{

		if ($this->input->post()) {

			$this->misc_model->add_note($this->input->post(), 'pur_contract', $rel_id);

			echo html_entity_decode($rel_id);
		}
	}



	/**

	 * Adds a note.

	 *

	 * @param        $rel_id  The purchase contract id

	 */

	public function add_pur_invoice_note($rel_id)
	{

		if ($this->input->post()) {

			$this->misc_model->add_note($this->input->post(), 'pur_invoice', $rel_id);

			echo html_entity_decode($rel_id);
		}
	}



	/**

	 * Adds a note.

	 *

	 * @param      <type>  $rel_id  The purchase order id

	 */

	public function add_note($rel_id)
	{

		if ($this->input->post()) {

			$this->misc_model->add_note($this->input->post(), 'purchase_order', $rel_id);

			echo html_entity_decode($rel_id);
		}
	}



	/**

	 * Uploads a purchase order attachment.

	 *

	 * @param      string  $id  The purchase order

	 * @return redirect

	 */

	public function purchase_order_attachment($id)
	{



		handle_purchase_order_file($id);



		redirect(admin_url('purchase/purchase_order/' . $id));
	}





	/**

	 * { preview purchase order file }

	 *

	 * @param      <type>  $id      The identifier

	 * @param      <type>  $rel_id  The relative identifier

	 * @return  view

	 */

	public function file_purorder($id, $rel_id)
	{

		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());

		$data['current_user_is_admin'] = is_admin();

		$data['file'] = $this->purchase_model->get_file($id, $rel_id);

		if (!$data['file']) {

			header('HTTP/1.0 404 Not Found');

			die;
		}

		$this->load->view('purchase_order/_file', $data);
	}



	/**

	 * { delete purchase order attachment }

	 *

	 * @param      <type>  $id     The identifier

	 */

	public function delete_purorder_attachment($id)
	{

		$this->load->model('misc_model');

		$file = $this->misc_model->get_file($id);

		if ($file->staffid == get_staff_user_id() || is_admin()) {

			echo html_entity_decode($this->purchase_model->delete_purorder_attachment($id));
		} else {

			header('HTTP/1.0 400 Bad error');

			echo _l('access_denied');

			die;
		}
	}



	/**

	 * Adds a payment.

	 *

	 * @param      <type>  $pur_order  The purchase order id

	 * @return  redirect

	 */

	public function add_payment($pur_order)
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			$message = '';

			$success = $this->purchase_model->add_payment($data, $pur_order);

			if ($success) {

				$message = _l('added_successfully', _l('payment'));
			}

			set_alert('success', $message);

			redirect(admin_url('purchase/purchase_order/' . $pur_order));
		}
	}



	/**

	 * { delete payment }

	 *

	 * @param      <type>  $id         The identifier

	 * @param      <type>  $pur_order  The pur order

	 * @return  redirect

	 */

	public function delete_payment($id, $pur_order)
	{

		if (!$id) {

			redirect(admin_url('purchase/purchase_order/' . $pur_order));
		}

		$response = $this->purchase_model->delete_payment($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('payment')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('payment')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('payment')));
		}

		redirect(admin_url('purchase/purchase_order/' . $pur_order));
	}



	/**

	 * { purchase order pdf }

	 *

	 * @param      <type>  $id     The identifier

	 * @return pdf output

	 */

	public function purorder_pdf($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/purchase_request'));
		}



		$pur_request = $this->purchase_model->get_purorder_pdf_html($id);



		try {

			$pdf = $this->purchase_model->purorder_pdf($pur_request);
		} catch (Exception $e) {

			echo html_entity_decode($e->getMessage());

			die;
		}



		$type = 'D';



		if ($this->input->get('output_type')) {

			$type = $this->input->get('output_type');
		}



		if ($this->input->get('print')) {

			$type = 'I';
		}



		$pdf->Output('purchase_order.pdf', $type);
	}



	/**

	 * { clear signature }

	 *

	 * @param      <type>  $id     The identifier

	 */

	public function clear_signature($id)
	{

		if (has_permission_new('purchase', '', 'delete')) {

			$this->purchase_model->clear_signature($id);
		}



		redirect(admin_url('contracts/contract/' . $id));
	}



	/**

	 * { Purchase reports }

	 * 

	 * @return view

	 */

	public function reports()
	{

		if (!is_admin() && !has_permission_new('purchase', '', 'view')) {

			access_denied('purchase');
		}

		$data['title'] = _l('purchase_reports');

		$data['items'] = $this->purchase_model->get_items();

		$this->load->view('reports/manage_report', $data);
	}



	/**

	 *  import goods report

	 *  

	 *  @return json

	 */

	public function import_goods_report()
	{

		if ($this->input->is_ajax_request()) {

			$this->load->model('currencies_model');



			$select = [

				'tblitems.commodity_code as item_code',

				'tblitems.description as item_name',

				'(select pur_order_name from ' . db_prefix() . 'pur_orders where ' . db_prefix() . 'pur_orders.id = pur_order) as po_name',

				'total_money',

			];

			$where = [];

			$custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');

			if ($custom_date_select != '') {

				array_push($where, $custom_date_select);
			}



			if ($this->input->post('products_services')) {

				$products_services = $this->input->post('products_services');

				$_products_services = [];

				if (is_array($products_services)) {

					foreach ($products_services as $product) {

						if ($product != '') {

							array_push($_products_services, $product);
						}
					}
				}

				if (count($_products_services) > 0) {

					array_push($where, 'AND tblitems.id IN (' . implode(', ', $_products_services) . ')');
				}
			}

			$currency = $this->currencies_model->get_base_currency();

			$aColumns = $select;

			$sIndexColumn = 'id';

			$sTable = db_prefix() . 'pur_order_detail';

			$join = [

				'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'pur_order_detail.item_code',

				'LEFT JOIN ' . db_prefix() . 'pur_orders ON ' . db_prefix() . 'pur_orders.id = ' . db_prefix() . 'pur_order_detail.pur_order',

			];



			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [

				db_prefix() . 'items.id as item_id',

				db_prefix() . 'pur_order_detail.pur_order as po_id'

			]);



			$output = $result['output'];

			$rResult = $result['rResult'];



			$footer_data = [

				'total' => 0,

			];



			foreach ($rResult as $aRow) {

				$row = [];



				$row[] = '<a href="' . admin_url('werehouse/commodity_list/' . $aRow['item_id']) . '" target="_blank">' . $aRow['item_code'] . '</a>';



				$row[] = $aRow['item_name'];



				$row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['po_id']) . '" target="_blank">' . $aRow['po_name'] . '</a>';









				$row[] = app_format_money($aRow['total_money'], $currency->name);

				$footer_data['total'] += $aRow['total_money'];



				$output['aaData'][] = $row;
			}



			foreach ($footer_data as $key => $total) {

				$footer_data[$key] = app_format_money($total, $currency->name);
			}



			$output['sums'] = $footer_data;

			echo json_encode($output);

			die();
		}
	}



	/**

	 * Gets the where report period.

	 *

	 * @param      string  $field  The field

	 *

	 * @return     string  The where report period.

	 */

	private function get_where_report_period($field = 'date')
	{

		$months_report = $this->input->post('report_months');

		$custom_date_select = '';

		if ($months_report != '') {

			if (is_numeric($months_report)) {

				// Last month

				if ($months_report == '1') {

					$beginMonth = date('Y-m-01', strtotime('first day of last month'));

					$endMonth = date('Y-m-t', strtotime('last day of last month'));
				} else {

					$months_report = (int) $months_report;

					$months_report--;

					$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));

					$endMonth = date('Y-m-t');
				}



				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
			} elseif ($months_report == 'this_month') {

				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
			} elseif ($months_report == 'this_year') {

				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .

					date('Y-m-d', strtotime(date('Y-01-01'))) .

					'" AND "' .

					date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
			} elseif ($months_report == 'last_year') {

				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .

					date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .

					'" AND "' .

					date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
			} elseif ($months_report == 'custom') {

				$from_date = to_sql_date($this->input->post('report_from'));

				$to_date = to_sql_date($this->input->post('report_to'));

				if ($from_date == $to_date) {

					$custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
				} else {

					$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
				}
			}
		}



		return $custom_date_select;
	}



	/**

	 * get data Purchase statistics by number of purchase orders

	 * 

	 * @return     json

	 */

	public function number_of_purchase_orders_analysis()
	{

		$year_report = $this->input->post('year');

		echo json_encode($this->purchase_model->number_of_purchase_orders_analysis($year_report));

		die();
	}



	/**

	 * get data Purchase statistics by cost

	 * 

	 * @return     json

	 */

	public function cost_of_purchase_orders_analysis()
	{

		$this->load->model('currencies_model');

		$year_report = $this->input->post('year');

		$currency = $this->currencies_model->get_base_currency();

		$currency_name = '';

		$currency_unit = '';

		if ($currency) {

			$currency_name = $currency->name;

			$currency_unit = $currency->symbol;
		}

		echo json_encode([

			'data' => $this->purchase_model->cost_of_purchase_orders_analysis($year_report),

			'unit' => $currency_unit,

			'name' => $currency_name,

		]);

		die();
	}



	/**

	 * { table vendor contracts }

	 *

	 * @param      <type>  $vendor  The vendor

	 */

	public function table_vendor_contracts($vendor)
	{

		$this->app->get_table_data(module_views_path('purchase', 'contracts/table_contracts'), ['vendor' => $vendor]);
	}



	/**

	 * { table vendor pur order }

	 *

	 * @param      <type>  $vendor  The vendor

	 */

	public function table_vendor_pur_order($vendor)
	{

		$this->app->get_table_data(module_views_path('purchase', 'purchase_order/table_pur_order'), ['vendor' => $vendor]);
	}



	/**

	 * { delete vendor admin }

	 *

	 * @param      <type>  $customer_id  The customer identifier

	 * @param      <type>  $staff_id     The staff identifier

	 */

	public function delete_vendor_admin($customer_id, $staff_id)
	{

		if (!has_permission_new('customers', '', 'create') && !has_permission_new('customers', '', 'edit')) {

			access_denied('customers');
		}



		$this->db->where('vendor_id', $customer_id);

		$this->db->where('staff_id', $staff_id);

		$this->db->delete(db_prefix() . 'pur_vendor_admin');

		redirect(admin_url('purchase/vendor/' . $customer_id) . '?tab=vendor_admins');
	}



	/**

	 * table commodity list

	 * 

	 * @return array

	 */

	public function table_item_list()
	{

		$this->app->get_table_data(module_views_path('purchase', 'items/table_item_list'));
	}



	/**

	 * item list

	 * @param  integer $id 

	 * @return load view

	 */

	public function items($id = '')
	{

		$this->load->model('departments_model');

		$this->load->model('staff_model');





		$data['units'] = $this->purchase_model->get_unit_add_item();

		$data['taxes'] = $this->purchase_model->get_taxes();

		$data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();

		$data['sub_groups'] = $this->purchase_model->get_sub_group();

		$data['title'] = _l('item_list');



		$data['item_id'] = $id;



		$this->load->view('items/item_list', $data);
	}



	/**

	 * get item data ajax

	 * @param  integer $id 

	 * @return view

	 */

	public function get_item_data_ajax($id)
	{



		$data['id'] = $id;

		$data['item'] = $this->purchase_model->get_item($id);

		$data['item_file'] = $this->purchase_model->get_item_attachments($id);

		$this->load->view('items/item_detail', $data);
	}



	/**

	 * add item list

	 * @param  integer $id 

	 * @return redirect

	 */

	public function add_item_list($id = '')
	{

		if ($this->input->post()) {

			$message = '';

			$data = $this->input->post();



			if (!$this->input->post('id')) {



				$mess = $this->purchase_model->add_item($data);

				if ($mess) {

					set_alert('success', _l('added_successfully') . _l('item_list'));
				} else {

					set_alert('warning', _l('Add_item_list_false'));
				}

				redirect(admin_url('purchase/item_list'));
			} else {

				$id = $data['id'];

				unset($data['id']);

				$success = $this->purchase_model->add_purchase($data, $id);

				if ($success) {

					set_alert('success', _l('updated_successfully') . _l('item_list'));
				} else {

					set_alert('warning', _l('updated_item_list_false'));
				}



				redirect(admin_url('purchase/item_list'));
			}
		}
	}



	/**

	 * delete item

	 * @param  integer $id 

	 * @return redirect

	 */

	public function delete_item($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/item_list'));
		}

		$response = $this->purchase_model->delete_item($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('item_list')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('item_list')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('item_list')));
		}

		redirect(admin_url('purchase/item_list'));
	}



	/**

	 * Gets the commodity barcode.

	 */

	public function get_commodity_barcode()
	{

		$commodity_barcode = $this->purchase_model->generate_commodity_barcode();



		echo json_encode([

			$commodity_barcode

		]);

		die();
	}



	/**

	 * commodity list add edit

	 * @param  integer $id

	 * @return json

	 */

	public function commodity_list_add_edit($id = '')
	{

		$data = $this->input->post();

		if ($data) {

			if (!isset($data['id'])) {

				$ids = $this->purchase_model->add_commodity_one_item($data);

				if ($ids) {



					// handle commodity list add edit file

					$success = true;

					$message = _l('added_successfully');

					set_alert('success', $message);

					/*upload multifile*/

					echo json_encode([

						'url' => admin_url('purchase/items/' . $ids),

						'commodityid' => $ids,

					]);

					die;
				}

				echo json_encode([

					'url' => admin_url('purchase/items'),

				]);

				die;
			} else {

				$id = $data['id'];

				unset($data['id']);

				$success = $this->purchase_model->update_commodity_one_item($data, $id);



				/*update file*/



				if ($success == true) {



					$message = _l('updated_successfully');

					set_alert('success', $message);
				}



				echo json_encode([

					'url' => admin_url('purchase/items/' . $id),

					'commodityid' => $id,

				]);

				die;
			}
		}
	}



	/**

	 * add commodity attachment

	 * @param  integer $id

	 * @return json

	 */

	public function add_commodity_attachment($id)
	{



		handle_item_attachments($id);

		echo json_encode([

			'url' => admin_url('purchase/items'),

		]);
	}



	/**

	 * get commodity file url 

	 * @param  integer $commodity_id

	 * @return json

	 */

	public function get_commodity_file_url($commodity_id)
	{

		$arr_commodity_file = $this->purchase_model->get_item_attachments($commodity_id);

		/*get images old*/

		$images_old_value = '';





		if (count($arr_commodity_file) > 0) {

			foreach ($arr_commodity_file as $key => $value) {

				$images_old_value .= '<div class="dz-preview dz-image-preview image_old' . $value["id"] . '">';



				$images_old_value .= '<div class="dz-image">';

				if (file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $value["rel_id"] . '/' . $value["file_name"])) {

					$images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/purchase/uploads/item_img/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
				} else {

					$images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/warehouse/uploads/item_img/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
				}

				$images_old_value .= '</div>';



				$images_old_value .= '<div class="dz-error-mark">';

				$images_old_value .= '<a class="dz-remove" data-dz-remove>Remove file';

				$images_old_value .= '</a>';

				$images_old_value .= '</div>';



				$images_old_value .= '<div class="remove_file">';

				$images_old_value .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,' . $value["id"] . '); return false;"><i class="fa fa fa-times"></i></a>';

				$images_old_value .= '</div>';



				$images_old_value .= '</div>';
			}
		}





		echo json_encode([

			'arr_images' => $images_old_value,

		]);

		die();
	}



	/**

	 * delete commodity file

	 * @param  integer $attachment_id

	 * @return json

	 */

	public function delete_commodity_file($attachment_id)
	{

		if (!has_permission_new('purchase', '', 'delete') && !is_admin()) {

			access_denied('purchase');
		}



		$file = $this->misc_model->get_file($attachment_id);

		echo json_encode([

			'success' => $this->purchase_model->delete_commodity_file($attachment_id),

		]);
	}



	/**

	 * unit type 

	 * @param  integer $id 

	 * @return redirect    

	 */

	public function unit_type($id = '')
	{

		if ($this->input->post()) {

			$message = '';

			$data = $this->input->post();



			if (!$this->input->post('id')) {

				$mess = $this->purchase_model->add_unit_type($data);

				if ($mess) {

					set_alert('success', _l('added_successfully') . ' ' . _l('unit_type'));
				} else {

					set_alert('warning', _l('Add_unit_type_false'));
				}

				redirect(admin_url('purchase/setting?group=units'));
			} else {

				$id = $data['id'];

				unset($data['id']);

				$success = $this->purchase_model->add_unit_type($data, $id);

				if ($success) {

					set_alert('success', _l('updated_successfully') . ' ' . _l('unit_type'));
				} else {

					set_alert('warning', _l('updated_unit_type_false'));
				}



				redirect(admin_url('purchase/setting?group=units'));
			}
		}
	}





	/**

	 * delete unit type 

	 * @param  integer $id

	 * @return redirect

	 */

	public function delete_unit_type($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/setting?group=units'));
		}

		$response = $this->purchase_model->delete_unit_type($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('unit_type')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('unit_type')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('unit_type')));
		}

		redirect(admin_url('purchase/setting?group=units'));
	}



	/**

	 * delete commodity

	 * @param  integer $id 

	 * @return redirect

	 */

	public function delete_commodity($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/items'));
		}

		$response = $this->purchase_model->delete_commodity($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('commodity_list')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('commodity_list')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('commodity_list')));
		}

		redirect(admin_url('purchase/items'));
	}



	/**

	 * Adds an expense.

	 */

	public function add_expense()
	{

		if ($this->input->post()) {

			$this->load->model('expenses_model');

			$data = $this->input->post();



			if (isset($data['pur_order'])) {

				$pur_order = $data['pur_order'];

				unset($data['pur_order']);
			}



			$id = $this->expenses_model->add($data);



			if ($id) {



				$this->purchase_model->mark_converted_pur_order($pur_order, $id);



				set_alert('success', _l('converted', _l('expense')));

				echo json_encode([

					'url' => admin_url('expenses/list_expenses/' . $id),

					'expenseid' => $id,

				]);

				die;
			}
		}
	}



	/**

	 * Uploads an attachment.

	 *

	 * @param      <type>  $id     The identifier

	 */

	public function upload_attachment($id)
	{

		handle_pur_vendor_attachments_upload($id);
	}



	/**

	 * { function_description }

	 *

	 * @param      <type>  $id      The identifier

	 * @param      <type>  $rel_id  The relative identifier

	 */

	public function file_pur_vendor($id, $rel_id)
	{

		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());

		$data['current_user_is_admin'] = is_admin();

		$data['file'] = $this->purchase_model->get_file($id, $rel_id);

		if (!$data['file']) {

			header('HTTP/1.0 404 Not Found');

			die;
		}

		$this->load->view('vendors/_file', $data);
	}



	/**

	 * { delete ic attachment }

	 *

	 * @param      <type>  $id     The identifier

	 */

	public function delete_ic_attachment($id)
	{

		$this->load->model('misc_model');

		$file = $this->misc_model->get_file($id);

		if ($file->staffid == get_staff_user_id() || is_admin()) {

			echo html_entity_decode($this->purchase_model->delete_ic_attachment($id));
		} else {

			header('HTTP/1.0 400 Bad error');

			echo _l('access_denied');

			die;
		}
	}



	/* Change client status / active / inactive */

	public function change_contact_status($id, $status)
	{

		if (has_permission_new('purchase', '', 'edit') || is_vendor_admin(get_user_id_by_contact_id_pur($id)) || is_admin()) {

			if ($this->input->is_ajax_request()) {

				$this->purchase_model->change_contact_status($id, $status);
			}
		}
	}



	/**

	 * { vendor items }

	 */

	public function vendor_items()
	{

		if (!has_permission_new('purchase', '', 'view') && !is_admin()) {

			access_denied('vendor_items');
		}



		$data['title'] = _l('vendor_items');

		$data['vendors'] = $this->purchase_model->get_vendor();

		$data['items'] = $this->purchase_model->get_item();

		$data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity();

		$this->load->view('vendor_items/manage', $data);
	}



	/**

	 *  vendor item table

	 *  

	 *  @return json

	 */

	public function vendor_items_table()
	{

		if ($this->input->is_ajax_request()) {



			$select = [

				db_prefix() . 'pur_vendor_items.id as vendor_items_id',

				db_prefix() . 'pur_vendor_items.items as items',

				db_prefix() . 'pur_vendor.company as company',

				db_prefix() . 'pur_vendor_items.add_from as pur_vendor_items_addedfrom',



			];

			$where = [];





			if ($this->input->post('vendor_filter')) {

				$vendor_filter = $this->input->post('vendor_filter');

				array_push($where, 'AND vendor IN (' . implode(',', $vendor_filter) . ')');
			}



			if ($this->input->post('group_items_filter')) {

				$group_items_filter = $this->input->post('group_items_filter');

				array_push($where, 'AND group_items IN (' . implode(',', $group_items_filter) . ')');
			}



			if ($this->input->post('items_filter')) {

				$items_filter = $this->input->post('items_filter');

				$staff_where = '';

				foreach ($items_filter as $key => $value) {

					if ($staff_where != '') {

						$staff_where .= ' or find_in_set(' . $value . ', items)';
					} else {

						$staff_where .= 'find_in_set(' . $value . ', items)';
					}
				}



				if ($staff_where != '') {

					array_push($where, 'AND (' . $staff_where . ')');
				}
			}



			$aColumns = $select;

			$sIndexColumn = 'id';

			$sTable = db_prefix() . 'pur_vendor_items';

			$join = [
				'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_vendor_items.vendor',

				'LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'pur_vendor_items.items'

			];



			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'pur_vendor.userid as userid', 'datecreate', 'description', 'commodity_code']);



			$output = $result['output'];

			$rResult = $result['rResult'];



			$footer_data = [

				'total' => 0,

			];



			foreach ($rResult as $aRow) {

				$row = [];



				$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['vendor_items_id'] . '"><label></label></div>';



				$row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['userid']) . '">' . $aRow['company'] . '</a>';



				$row[] = '<a href="' . admin_url('purchase/items/' . $aRow['items']) . '" >' . $aRow['commodity_code'] . ' - ' . $aRow['description'] . '</a>';



				$row[] = _d($aRow['datecreate']);



				$options = icon_btn('purchase/delete_vendor_items/' . $aRow['vendor_items_id'], 'remove', 'btn-danger', ['title' => _l('delete')]);



				$row[] = $options;



				$output['aaData'][] = $row;
			}



			echo json_encode($output);

			die();
		}
	}



	/**

	 * new vendor items

	 */

	public function get_vendor_data($id = "")
	{

		$vendor = $this->purchase_model->get_data_vendor($id);

		$PO = $this->purchase_model->pendingOrder_list_ByVendor($id);

		echo json_encode([



			'vendor' => $vendor,

			'PendingOrder' => $PO,

		]);
	}

	public function new_vendor_items()
	{

		if (!has_permission_new('purchase', '', 'create') && !is_admin()) {

			access_denied('vendor_items');
		}

		$this->load->model('staff_model');



		if ($this->input->post()) {

			$data = $this->input->post();

			if (!has_permission_new('purchase', '', 'create')) {

				access_denied('vendor_items');
			}

			$success = $this->purchase_model->add_vendor_items($data);

			if ($success) {

				set_alert('success', _l('added_successfully', _l('vendor_items')));
			}

			redirect(admin_url('purchase/vendor_items'));
		}

		$data['title'] = _l('vendor_items');



		$data['vendors'] = $this->purchase_model->get_vendor_data();



		$data['items'] = $this->purchase_model->get_item_data();

		// print_r($data['items']);die;

		$data['commodity_groups'] = $this->purchase_model->get_commodity_group_add_commodity_data();



		$this->load->view('vendor_items/vendor_items', $data);
	}



	public function item_list_by_item_group()
	{

		$data = $this->purchase_model->get_item_list_by_item_group($this->input->post('id'));

		echo json_encode($data);
	}



	/**

	 * { group item change }

	 */

	public function group_it_change($group = '')
	{

		if ($group != '') {



			$list_items = $this->purchase_model->get_item_by_group($group);



			$html = '';

			if (count($list_items) > 0) {

				foreach ($list_items as $item) {

					$html .= '<option value="' . $item['id'] . '" selected>' . $item['commodity_code'] . ' - ' . $item['description'] . '</option>';
				}
			}



			echo json_encode([

				'html' => $html,

			]);
		} else {

			$items = $this->purchase_model->get_item();

			$html = '';

			if (count($items) > 0) {

				foreach ($items as $it) {

					$html .= '<option value="' . $it['id'] . '">' . $it['commodity_code'] . ' - ' . $it['description'] . '</option>';
				}
			}



			echo json_encode([

				'html' => $html,

			]);
		}
	}



	/**

	 * { delete vendor item  }

	 *

	 * @param      <type>  $id     The identifier

	 */

	public function delete_vendor_items($id)
	{

		if (!has_permission_new('purchase', '', 'delete') && !is_admin()) {

			access_denied('vendor_items');
		}

		if (!$id) {

			redirect(admin_url('purchase/vendor_items'));
		}



		$success = $this->purchase_model->delete_vendor_items($id);

		if ($success == true) {

			set_alert('success', _l('deleted', _l('vendor_items')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('vendor_items')));
		}

		redirect(admin_url('purchase/vendor_items'));
	}



	/**

	 * purchase delete bulk action

	 * @return

	 */

	public function purchase_delete_bulk_action()
	{

		if (!is_staff_member()) {

			ajax_access_denied();
		}

		$total_deleted = 0;

		if ($this->input->post()) {



			$ids = $this->input->post('ids');

			$rel_type = $this->input->post('rel_type');

			/*check permission*/

			switch ($rel_type) {

				case 'commodity_list':

					if (!has_permission_new('purchase', '', 'delete') && !is_admin()) {

						access_denied('commodity_list');
					}

					break;



				case 'vendors':

					if (!has_permission_new('purchase', '', 'delete') && !is_admin()) {

						access_denied('vendors');
					}

					break;



				case 'vendor_items':

					if (!has_permission_new('purchase', '', 'delete') && !is_admin()) {

						access_denied('vendor_items');
					}

					break;



				default:

					break;
			}



			/*delete data*/

			if ($this->input->post('mass_delete')) {

				if (is_array($ids)) {

					foreach ($ids as $id) {



						switch ($rel_type) {

							case 'commodity_list':

								if ($this->purchase_model->delete_commodity($id)) {

									$total_deleted++;

									break;
								} else {

									break;
								}



							case 'vendors':

								if ($this->purchase_model->delete_vendor($id)) {

									$total_deleted++;

									break;
								} else {

									break;
								}



							case 'vendor_items':

								if ($this->purchase_model->delete_vendor_items($id)) {

									$total_deleted++;

									break;
								} else {

									break;
								}



							default:



								break;
						}
					}
				}

				/*return result*/

				switch ($rel_type) {

					case 'commodity_list':

						set_alert('success', _l('total_commodity_list') . ": " . $total_deleted);

						break;



					case 'vendors':

						set_alert('success', _l('total_vendors_list') . ": " . $total_deleted);

						break;



					case 'vendor_items':

						set_alert('success', _l('total_vendor_items_list') . ": " . $total_deleted);

						break;



					default:

						break;
				}
			}
		}
	}



	/**

	 * { pur order setting }

	 * @return redirect

	 */

	public function pur_order_setting()
	{

		if (!has_permission_new('purchase', '', 'edit') && !is_admin()) {

			access_denied('purchase');
		}



		if ($this->input->post()) {

			$data = $this->input->post();

			$update = $this->purchase_model->update_po_number_setting($data);



			if ($update == true) {

				set_alert('success', _l('updated_successfully'));
			} else {

				set_alert('warning', _l('updated_fail'));
			}



			redirect(admin_url('purchase/setting'));
		}
	}



	public function get_html_approval_setting($id = '')
	{

		$html = '';

		$staffs = $this->staff_model->get();

		$approver = [

			0 => ['id' => 'direct_manager', 'name' => _l('direct_manager')],

			1 => ['id' => 'department_manager', 'name' => _l('department_manager')],

			2 => ['id' => 'staff', 'name' => _l('staff')]
		];

		$action = [

			1 => ['id' => 'approve', 'name' => _l('approve')],

			0 => ['id' => 'sign', 'name' => _l('sign')],

		];

		if (is_numeric($id)) {

			$approval_setting = $this->purchase_model->get_approval_setting($id);



			$setting = json_decode($approval_setting->setting);



			foreach ($setting as $key => $value) {

				if ($key == 0) {

					$html .= '<div id="item_approve">

						<div class="col-md-11">

						<div class="col-md-1 hide"> ' .

						render_select('approver[' . $key . ']', $approver, array('id', 'name'), 'task_single_related', 'staff') . '

						</div>

						<div class="col-md-8">

						' . render_select('staff[' . $key . ']', $staffs, array('staffid', 'full_name'), 'staff', $value->staff) . '

						</div>

						<div class="col-md-4">

						' . render_select('action[' . $key . ']', $action, array('id', 'name'), 'action', $value->action) . ' 

						</div>

						</div>

						<div class="col-md-1 btn_apr">

						<span class="pull-bot">

						<button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>

						</span>

						</div>

						</div>';
				} else {

					$html .= '<div id="item_approve">

						<div class="col-md-11">

						<div class="col-md-1 hide">

						' .

						render_select('approver[' . $key . ']', $approver, array('id', 'name'), 'task_single_related', 'staff') . ' 

						</div>

						<div class="col-md-8">

						' . render_select('staff[' . $key . ']', $staffs, array('staffid', 'full_name'), 'staff', $value->staff) . ' 

						</div>

						<div class="col-md-4">

						' . render_select('action[' . $key . ']', $action, array('id', 'name'), 'action', $value->action) . ' 

						</div>

						</div>

						<div class="col-md-1 btn_apr">

						<span class="pull-bot">

						<button name="add" class="btn remove_vendor_requests btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>

						</span>

						</div>

						</div>';
				}
			}
		} else {

			$html .= '<div id="item_approve">

				<div class="col-md-11">

				<div class="col-md-1 hide"> ' .

				render_select('approver[0]', $approver, array('id', 'name'), 'task_single_related', 'staff') . '

				</div>

				<div class="col-md-8">

				' . render_select('staff[0]', $staffs, array('staffid', 'full_name'), 'staff') . '

				</div>

				<div class="col-md-4">

				' . render_select('action[0]', $action, array('id', 'name'), 'action', 'approve') . ' 

				</div>

				</div>

				<div class="col-md-1 btn_apr">

				<span class="pull-bot">

				<button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>

				</span>

				</div>

				</div>';
		}



		echo json_encode([

			$html

		]);
	}



	/**

	 * commodty group type

	 * @param  integer $id

	 * @return redirect

	 */

	public function commodity_group_type($id = '')
	{

		if ($this->input->post()) {

			$message = '';

			$data = $this->input->post();



			if (!$this->input->post('id')) {



				$mess = $this->purchase_model->add_commodity_group_type($data);

				if ($mess) {

					set_alert('success', _l('added_successfully') . _l('commodity_group_type'));
				} else {

					set_alert('warning', _l('Add_commodity_group_type_false'));
				}

				redirect(admin_url('purchase/setting?group=commodity_group'));
			} else {

				$id = $data['id'];

				unset($data['id']);

				$success = $this->purchase_model->add_commodity_group_type($data, $id);

				if ($success) {

					set_alert('success', _l('updated_successfully') . _l('commodity_group_type'));
				} else {

					set_alert('warning', _l('updated_commodity_group_type_false'));
				}



				redirect(admin_url('purchase/setting?group=commodity_group'));
			}
		}
	}



	/**

	 * delete commodity group type

	 * @param  integer $id

	 * @return redirect

	 */

	public function delete_commodity_group_type($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/setting?group=commodity_group'));
		}

		$response = $this->purchase_model->delete_commodity_group_type($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('commodity_group_type')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('commodity_group_type')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('commodity_group_type')));
		}

		redirect(admin_url('purchase/setting?group=commodity_group'));
	}



	/**

	 * sub group

	 * @param  integer $id

	 * @return redirect

	 */

	public function sub_group($id = '')
	{

		if ($this->input->post()) {

			$message = '';

			$data = $this->input->post();



			if (!$this->input->post('id')) {



				$mess = $this->purchase_model->add_sub_group($data);

				if ($mess) {

					set_alert('success', _l('added_successfully') . ' ' . _l('sub_group'));
				} else {

					set_alert('warning', _l('Add_sub_group_false'));
				}

				redirect(admin_url('purchase/setting?group=sub_group'));
			} else {

				$id = $data['id'];

				unset($data['id']);

				$success = $this->purchase_model->add_sub_group($data, $id);

				if ($success) {

					set_alert('success', _l('updated_successfully') . ' ' . _l('sub_group'));
				} else {

					set_alert('warning', _l('updated_sub_group_false'));
				}



				redirect(admin_url('purchase/setting?group=sub_group'));
			}
		}
	}



	/**

	 * delete sub group

	 * @param  integer $id

	 * @return redirect

	 */

	public function delete_sub_group($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/setting?group=sub_group'));
		}

		$response = $this->purchase_model->delete_sub_group($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('sub_group')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('sub_group')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('sub_group')));
		}

		redirect(admin_url('purchase/setting?group=sub_group'));
	}



	/**

	 * get subgroup fill data

	 * @return html 

	 */

	public function get_subgroup_fill_data()
	{

		$data = $this->input->post();



		$subgroup = $this->purchase_model->list_subgroup_by_group($data['group_id']);



		echo json_encode([

			'subgroup' => $subgroup

		]);
	}



	/**

	 * { copy public link }

	 *

	 * @param      string  $id     The identifier

	 */

	public function copy_public_link($id)
	{

		$pur_order = $this->purchase_model->get_pur_order($id);

		$copylink = '';

		if ($pur_order) {

			if ($pur_order->hash != '' && $pur_order->hash != null) {

				$copylink = site_url('purchase/vendors_portal/pur_order/' . $id . '/' . $pur_order->hash);
			} else {

				$hash = app_generate_hash();

				$copylink = site_url('purchase/vendors_portal/pur_order/' . $id . '/' . $hash);

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'pur_orders', ['hash' => $hash,]);
			}
		}



		echo json_encode([

			'copylink' => $copylink,

		]);
	}



	/**

	 * { copy public link pur request }

	 *

	 * @param      string  $id     The identifier

	 */

	public function copy_public_link_pur_request($id)
	{

		$pur_request = $this->purchase_model->get_purchase_request($id);

		$copylink = '';

		if ($pur_request) {

			if ($pur_request->hash != '' && $pur_request->hash != null) {

				$copylink = site_url('purchase/vendors_portal/pur_request/' . $id . '/' . $pur_request->hash);
			} else {

				$hash = app_generate_hash();

				$copylink = site_url('purchase/vendors_portal/pur_request/' . $id . '/' . $hash);

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'pur_request', ['hash' => $hash,]);
			}
		}



		echo json_encode([

			'copylink' => $copylink,

		]);
	}



	/**

	 * { file pur vendor }

	 *

	 * @param       $id      The identifier

	 * @param       $rel_id  The relative identifier

	 */

	public function file_pur_contract($id, $rel_id)
	{

		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());

		$data['current_user_is_admin'] = is_admin();

		$data['file'] = $this->purchase_model->get_file($id, $rel_id);

		if (!$data['file']) {

			header('HTTP/1.0 404 Not Found');

			die;
		}

		$this->load->view('contracts/_file', $data);
	}



	/**

	 * { delete purchase contract attachment }

	 *

	 * @param        $id     The identifier

	 */

	public function delete_pur_contract_attachment($id)
	{

		$this->load->model('misc_model');

		$file = $this->misc_model->get_file($id);

		if ($file->staffid == get_staff_user_id() || is_admin()) {

			echo html_entity_decode($this->purchase_model->delete_pur_contract_attachment($id));
		} else {

			header('HTTP/1.0 400 Bad error');

			echo _l('access_denied');

			die;
		}
	}



	/**

	 * { vendor category form }

	 * @return redirect

	 */

	public function vendor_cate()
	{

		if ($this->input->post()) {

			$message = '';

			$data = $this->input->post();

			if (!$this->input->post('id')) {

				$id = $this->purchase_model->add_vendor_category($data);

				if ($id) {

					$success = true;

					$message = _l('added_successfully', _l('vendor_category'));

					set_alert('success', $message);
				}

				redirect(admin_url('purchase/setting?group=vendor_category'));
			} else {

				$id = $data['id'];

				unset($data['id']);

				$success = $this->purchase_model->update_vendor_category($data, $id);

				if ($success) {

					$message = _l('updated_successfully', _l('vendor_category'));

					set_alert('success', $message);
				}

				redirect(admin_url('purchase/setting?group=vendor_category'));
			}

			die;
		}
	}



	/**

	 * delete job_position

	 * @param  integer $id

	 * @return redirect

	 */

	public function delete_vendor_category($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/setting?group=vendor_category'));
		}

		$response = $this->purchase_model->delete_vendor_category($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('vendor_category')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('vendor_category')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('vendor_category')));
		}

		redirect(admin_url('purchase/setting?group=vendor_category'));
	}



	/**

	 * Uploads a purchase estimate attachment.

	 *

	 * @param      string  $id  The purchase order

	 * @return redirect

	 */

	public function purchase_estimate_attachment($id)
	{



		handle_purchase_estimate_file($id);



		redirect(admin_url('purchase/quotations/' . $id));
	}



	/**

	 * { preview purchase estimate file }

	 *

	 * @param        $id      The identifier

	 * @param        $rel_id  The relative identifier

	 * @return  view

	 */

	public function file_pur_estimate($id, $rel_id)
	{

		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());

		$data['current_user_is_admin'] = is_admin();

		$data['file'] = $this->purchase_model->get_file($id, $rel_id);

		if (!$data['file']) {

			header('HTTP/1.0 404 Not Found');

			die;
		}

		$this->load->view('quotations/_file', $data);
	}



	/**

	 * { delete purchase order attachment }

	 *

	 * @param      <type>  $id     The identifier

	 */

	public function delete_estimate_attachment($id)
	{

		$this->load->model('misc_model');

		$file = $this->misc_model->get_file($id);

		if ($file->staffid == get_staff_user_id() || is_admin()) {

			echo html_entity_decode($this->purchase_model->delete_estimate_attachment($id));
		} else {

			header('HTTP/1.0 400 Bad error');

			echo _l('access_denied');

			die;
		}
	}



	/**

	 * Determines if vendor code exists.

	 */

	public function vendor_code_exists()
	{

		if ($this->input->is_ajax_request()) {

			$selected_company = $this->session->userdata('root_company');

			if ($this->input->post()) {

				// First we need to check if the email is the same

				$id = $this->input->post('userid');

				if ($id != '') {

					$this->db->where('AccountID', $this->input->post('vendor_code'));

					$this->db->where('PlantID', $selected_company);

					$pur_vendor = $this->db->get(db_prefix() . 'clients')->row();

					if ($pur_vendor->AccountID == $this->input->post('vendor_code')) {

						echo json_encode(true);

						die();
					}
				}



				$this->db->where('AccountID', $this->input->post('vendor_code'));

				$this->db->where('PlantID', $selected_company);

				$total_rows = $this->db->count_all_results(db_prefix() . 'clients    ');

				if ($total_rows > 0) {

					echo json_encode(false);
				} else {

					echo json_encode(true);
				}

				die();
			}
		}
	}



	/**

	 * { dpm name in pur request number }

	 *

	 * @param        $dpm    The dpm

	 */

	public function dpm_name_in_pur_request_number($dpm)
	{

		$this->load->model('departments_model');

		$department = $this->departments_model->get($dpm);

		$name_rs = '';

		if ($department) {

			$name_repl = str_replace(' ', '', $department->name);

			$name_rs = strtoupper($name_repl);
		}



		echo json_encode([

			'rs' => $name_rs,

		]);
	}



	/**

	 * { update customfield po }

	 *

	 * @param        $id     The identifier

	 */

	public function update_customfield_po($id)
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			$success = $this->purchase_model->update_customfield_po($id, $data);

			if ($success) {

				$message = _l('updated_successfully', _l('vendor_category'));

				set_alert('success', $message);
			}

			redirect(admin_url('purchase/purchase_order/' . $id));
		}
	}



	/**

	 * { po voucher }

	 */

	public function po_voucher()
	{



		$po_voucher = $this->purchase_model->get_po_voucher_html();



		try {

			$pdf = $this->purchase_model->povoucher_pdf($po_voucher);
		} catch (Exception $e) {

			echo html_entity_decode($e->getMessage());

			die;
		}



		$type = 'D';



		if ($this->input->get('output_type')) {

			$type = $this->input->get('output_type');
		}



		if ($this->input->get('print')) {

			$type = 'I';
		}



		$pdf->Output('PO_voucher.pdf', $type);
	}





	/**

	 *  po voucher report

	 *  

	 *  @return json

	 */

	public function po_voucher_report()
	{

		if ($this->input->is_ajax_request()) {

			$this->load->model('currencies_model');



			$select = [

				'pur_order_number',

				'order_date',

				'type',

				'project',

				'department',

				'vendor',

				'approve_status',

				'delivery_status',

			];

			$where = [];

			$custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');

			if ($custom_date_select != '') {

				array_push($where, $custom_date_select);
			}







			$currency = $this->currencies_model->get_base_currency();

			$aColumns = $select;

			$sIndexColumn = 'id';

			$sTable = db_prefix() . 'pur_orders';

			$join = [

				'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_orders.department',

				'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'pur_orders.project',

				'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor',

			];



			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [

				db_prefix() . 'pur_orders.id as id',

				db_prefix() . 'departments.name as department_name',

				db_prefix() . 'projects.name as project_name',

				db_prefix() . 'pur_vendor.company as vendor_name',

				'total',

			]);



			$output = $result['output'];

			$rResult = $result['rResult'];



			foreach ($rResult as $aRow) {

				$row = [];



				$row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">' . $aRow['pur_order_number'] . '</a>';



				$row[] = _d($aRow['order_date']);



				$row[] = _l($aRow['type']);



				$row[] = '<a href="' . admin_url('projects/view/' . $aRow['project']) . '" target="_blank">' . $aRow['project_name'] . '</a>';



				$row[] = $aRow['department_name'];



				$row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';



				$row[] = get_status_approve($aRow['approve_status']);



				$delivery_status = '';

				if ($aRow['delivery_status'] == 0) {

					$delivery_status = '<span class="label label-danger">' . _l('undelivered') . '</span>';
				} elseif ($aRow['delivery_status'] == 1) {

					$delivery_status = '<span class="label label-success">' . _l('delivered') . '</span>';
				}

				$row[] = $delivery_status;



				$paid = $aRow['total'] - purorder_left_to_pay($aRow['id']);

				$percent = 0;

				if ($aRow['total'] > 0) {

					$percent = ($paid / $aRow['total']) * 100;
				}



				$row[] = '<div class="progress">

					<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"

					aria-valuemin="0" aria-valuemax="100" style="width:' . round($percent) . '%">

					' . round($percent) . ' % 

					</div>

					</div>';



				$output['aaData'][] = $row;
			}



			echo json_encode($output);

			die();
		}
	}



	/**

	 *  po voucher report

	 *  

	 *  @return json

	 */

	public function po_report()
	{

		if ($this->input->is_ajax_request()) {

			$this->load->model('currencies_model');



			$select = [

				'pur_order_number',

				'order_date',

				'department',

				'vendor',

				'approve_status',

				'subtotal',

				'total_tax',

				'total',

			];

			$where = [];

			$custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_orders.order_date');

			if ($custom_date_select != '') {

				array_push($where, $custom_date_select);
			}







			$currency = $this->currencies_model->get_base_currency();

			$aColumns = $select;

			$sIndexColumn = 'id';

			$sTable = db_prefix() . 'pur_orders';

			$join = [

				'LEFT JOIN ' . db_prefix() . 'departments ON ' . db_prefix() . 'departments.departmentid = ' . db_prefix() . 'pur_orders.department',

				'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'pur_orders.project',

				'LEFT JOIN ' . db_prefix() . 'pur_vendor ON ' . db_prefix() . 'pur_vendor.userid = ' . db_prefix() . 'pur_orders.vendor',

			];



			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [

				db_prefix() . 'pur_orders.id as id',

				db_prefix() . 'departments.name as department_name',

				db_prefix() . 'projects.name as project_name',

				db_prefix() . 'pur_vendor.company as vendor_name',

				'total',

			]);



			$output = $result['output'];

			$rResult = $result['rResult'];



			$footer_data = [

				'total' => 0,

				'total_tax' => 0,

				'total_value' => 0,

			];



			foreach ($rResult as $aRow) {

				$row = [];



				$row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '" target="_blank">' . $aRow['pur_order_number'] . '</a>';



				$row[] = _d($aRow['order_date']);



				$row[] = $aRow['department_name'];



				$row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" target="_blank">' . $aRow['vendor_name'] . '</a>';



				$row[] = get_status_approve($aRow['approve_status']);



				$row[] = app_format_money($aRow['subtotal'], $currency->name);



				$row[] = app_format_money($aRow['total_tax'], $currency->name);



				$row[] = app_format_money($aRow['total'], $currency->name);



				$footer_data['total'] += $aRow['total'];

				$footer_data['total_tax'] += $aRow['total_tax'];

				$footer_data['total_value'] += $aRow['subtotal'];



				$output['aaData'][] = $row;
			}



			foreach ($footer_data as $key => $total) {

				$footer_data[$key] = app_format_money($total, $currency->name);
			}



			$output['sums'] = $footer_data;

			echo json_encode($output);

			die();
		}
	}



	/**

	 *  purchase inv report

	 *  

	 *  @return json

	 */

	public function purchase_inv_report()
	{

		if ($this->input->is_ajax_request()) {

			$this->load->model('currencies_model');



			$select = [

				'invoice_number',

				'contract',

				db_prefix() . 'pur_invoices.pur_order',

				'invoice_date',

				'payment_status',

				'subtotal',

				'tax',

				'total',

			];

			$where = [];

			$custom_date_select = $this->get_where_report_period(db_prefix() . 'pur_invoices.invoice_date');

			if ($custom_date_select != '') {

				array_push($where, $custom_date_select);
			}







			$currency = $this->currencies_model->get_base_currency();

			$aColumns = $select;

			$sIndexColumn = 'id';

			$sTable = db_prefix() . 'pur_invoices';

			$join = [

				'LEFT JOIN ' . db_prefix() . 'pur_contracts ON ' . db_prefix() . 'pur_contracts.id = ' . db_prefix() . 'pur_invoices.contract'

			];



			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [

				db_prefix() . 'pur_invoices.id as id',

				db_prefix() . 'pur_contracts.contract_number as contract_number',



			]);



			$output = $result['output'];

			$rResult = $result['rResult'];



			$footer_data = [

				'total' => 0,

				'total_tax' => 0,

				'total_value' => 0,

			];



			foreach ($rResult as $aRow) {

				$row = [];



				$row[] = '<a href="' . admin_url('purchase/purchase_invoice/' . $aRow['id']) . '" target="_blank">' . $aRow['invoice_number'] . '</a>';



				$row[] = '<a href="' . admin_url('purchase/contract/' . $aRow['contract']) . '" target="_blank">' . $aRow['contract_number'] . '</a>';



				$row[] = '<a href="' . admin_url('purchase/purchase_order/' . $aRow[db_prefix() . 'pur_invoices.pur_order']) . '" target="_blank">' . get_pur_order_subject($aRow[db_prefix() . 'pur_invoices.pur_order']) . '</a>';



				$row[] = _d($aRow['invoice_date']);



				$class = '';

				if ($aRow['payment_status'] == 'unpaid') {

					$class = 'danger';
				} elseif ($aRow['payment_status'] == 'paid') {

					$class = 'success';
				} elseif ($aRow['payment_status'] == 'partially_paid') {

					$class = 'warning';
				}



				$row[] = '<span class="label label-' . $class . ' s-status invoice-status-3">' . _l($aRow['payment_status']) . '</span>';



				$row[] = app_format_money($aRow['subtotal'], $currency->name);



				$row[] = app_format_money($aRow['tax'], $currency->name);



				$row[] = app_format_money($aRow['total'], $currency->name);



				$footer_data['total'] += $aRow['total'];

				$footer_data['total_tax'] += $aRow['tax'];

				$footer_data['total_value'] += $aRow['subtotal'];



				$output['aaData'][] = $row;
			}



			foreach ($footer_data as $key => $total) {

				$footer_data[$key] = app_format_money($total, $currency->name);
			}



			$output['sums'] = $footer_data;

			echo json_encode($output);

			die();
		}
	}



	/**

	 * { invoices }

	 * @return view

	 */

	public function invoices()
	{

		$data['title'] = _l('invoices');

		$data['contracts'] = $this->purchase_model->get_contract();

		$this->load->view('invoices/manage', $data);
	}



	/**

	 * { table purchase invoices }

	 */

	public function table_pur_invoices()
	{

		$this->app->get_table_data(module_views_path('purchase', 'invoices/table_pur_invoices'));
	}



	/**

	 * { purchase invoice }

	 *

	 * @param      string  $id     The identifier

	 */

	public function pur_invoice($id = '')
	{

		if ($id == '') {

			$data['title'] = _l('add_invoice');
		} else {

			$data['title'] = _l('edit_invoice');

			$data['pur_invoice'] = $this->purchase_model->get_pur_invoice($id);
		}

		$data['contracts'] = $this->purchase_model->get_contract();

		$data['taxes'] = $this->purchase_model->get_taxes();

		$data['pur_orders'] = $this->purchase_model->get_pur_order_approved();

		$this->load->view('invoices/pur_invoice', $data);
	}



	/**

	 * { pur invoice form }

	 * @return redirect

	 */

	public function pur_invoice_form()
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			if ($data['id'] == '') {

				unset($data['id']);

				$mess = $this->purchase_model->add_pur_invoice($data);

				if ($mess) {

					handle_pur_invoice_file($mess);

					set_alert('success', _l('added_successfully') . ' ' . _l('purchase_invoice'));
				} else {

					set_alert('warning', _l('add_purchase_invoice_fail'));
				}

				redirect(admin_url('purchase/invoices'));
			} else {

				$id = $data['id'];

				unset($data['id']);

				handle_pur_invoice_file($id);

				$success = $this->purchase_model->update_pur_invoice($id, $data);

				if ($success) {

					set_alert('success', _l('updated_successfully') . ' ' . _l('purchase_invoice'));
				} else {

					set_alert('warning', _l('update_purchase_invoice_fail'));
				}

				redirect(admin_url('purchase/invoices'));
			}
		}
	}



	public function delete_pur_invoice($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/invoices'));
		}

		$response = $this->purchase_model->delete_pur_invoice($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('purchase_invoice')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('purchase_invoice')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('purchase_invoice')));
		}

		redirect(admin_url('purchase/invoices'));
	}



	/**

	 * { contract change }

	 *

	 * @param      <type>  $ct    

	 */

	public function contract_change($ct)
	{

		$contract = $this->purchase_model->get_contract($ct);

		$value = 0;

		if ($contract) {

			$value = $contract->contract_value;
		}



		echo json_encode([

			'value' => $value,

		]);
	}



	/**

	 * { purchase order change }

	 *

	 * @param      <type>  $ct    

	 */

	public function pur_order_change($ct)
	{

		$pur_order = $this->purchase_model->get_pur_order($ct);

		$value = 0;

		if ($pur_order) {

			$value = $pur_order->total;
		}



		echo json_encode([

			'value' => $value,

		]);
	}



	/**

	 * { tax rate change }

	 *

	 * @param        $tax    The tax

	 */

	public function tax_rate_change($tax)
	{

		$this->load->model('taxes_model');

		$tax = $this->taxes_model->get($tax);

		$rate = 0;

		if ($tax) {

			$rate = $tax->taxrate;
		}



		echo json_encode([

			'rate' => $rate,

		]);
	}



	/**

	 * { purchase invoice }

	 *

	 * @param       $id     The identifier

	 */

	public function purchase_invoice($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/invoices'));
		}



		$this->load->model('staff_model');



		$this->load->model('payment_modes_model');

		$data['payment_modes'] = $this->payment_modes_model->get('', [

			'expenses_only !=' => 1,

		]);

		$data['pur_invoice'] = $this->purchase_model->get_pur_invoice($id);

		$data['title'] = $data['pur_invoice']->invoice_number;

		$data['members'] = $this->staff_model->get('', ['active' => 1]);

		$data['payment'] = $this->purchase_model->get_payment_invoice($id);

		$data['pur_invoice_attachments'] = $this->purchase_model->get_purchase_invoice_attachments($id);

		$this->load->view('invoices/pur_invoice_preview', $data);
	}



	/**

	 * Adds a payment for invoice.

	 *

	 * @param      <type>  $pur_order  The purchase order id

	 * @return  redirect

	 */

	public function add_invoice_payment($invoice)
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			$message = '';

			$success = $this->purchase_model->add_invoice_payment($data, $invoice);

			if ($success) {

				$message = _l('added_successfully', _l('payment'));
			}

			set_alert('success', $message);

			redirect(admin_url('purchase/purchase_invoice/' . $invoice));
		}
	}



	/**

	 * { delete payment }

	 *

	 * @param      <type>  $id         The identifier

	 * @param      <type>  $pur_order  The pur order

	 * @return  redirect

	 */

	public function delete_payment_pur_invoice($id, $inv)
	{

		if (!$id) {

			redirect(admin_url('purchase/purchase_invoice/' . $inv));
		}

		$response = $this->purchase_model->delete_payment_pur_invoice($id);

		if (is_array($response) && isset($response['referenced'])) {

			set_alert('warning', _l('is_referenced', _l('payment')));
		} elseif ($response == true) {

			set_alert('success', _l('deleted', _l('payment')));
		} else {

			set_alert('warning', _l('problem_deleting', _l('payment')));
		}

		redirect(admin_url('purchase/purchase_invoice/' . $inv));
	}



	/**

	 * { payment invoice }

	 *

	 * @param       $id     The identifier

	 * @return view

	 */

	public function payment_invoice($id)
	{

		$this->load->model('currencies_model');



		$send_mail_approve = $this->session->userdata("send_mail_approve");

		if ((isset($send_mail_approve)) && $send_mail_approve != '') {

			$data['send_mail_approve'] = $send_mail_approve;

			$this->session->unset_userdata("send_mail_approve");
		}



		$data['check_appr'] = $this->purchase_model->get_approve_setting('payment_request');

		$data['get_staff_sign'] = $this->purchase_model->get_staff_sign($id, 'payment_request');

		$data['check_approve_status'] = $this->purchase_model->check_approval_details($id, 'payment_request');

		$data['list_approve_status'] = $this->purchase_model->get_list_approval_details($id, 'payment_request');





		$data['payment_invoice'] = $this->purchase_model->get_payment_pur_invoice($id);

		$data['title'] = _l('payment_for') . ' ' . get_pur_invoice_number($data['payment_invoice']->pur_invoice);



		$data['invoice'] = $this->purchase_model->get_pur_invoice($data['payment_invoice']->pur_invoice);



		$data['base_currency'] = $this->currencies_model->get_base_currency();

		$this->load->view('invoices/payment_invoice', $data);
	}



	/**

	 * { purchase invoice attachment }

	 */

	public function purchase_invoice_attachment($id)
	{

		handle_pur_invoice_file($id);

		redirect(admin_url('purchase/purchase_invoice/' . $id));
	}



	/**

	 * { preview purchase invoice file }

	 *

	 * @param        $id      The identifier

	 * @param        $rel_id  The relative identifier

	 * @return  view

	 */

	public function file_purinv($id, $rel_id)
	{

		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());

		$data['current_user_is_admin'] = is_admin();

		$data['file'] = $this->purchase_model->get_file($id, $rel_id);

		if (!$data['file']) {

			header('HTTP/1.0 404 Not Found');

			die;
		}

		$this->load->view('invoices/_file', $data);
	}



	/**

	 * { delete purchase order attachment }

	 *

	 * @param      <type>  $id     The identifier

	 */

	public function delete_purinv_attachment($id)
	{

		$this->load->model('misc_model');

		$file = $this->misc_model->get_file($id);

		if ($file->staffid == get_staff_user_id() || is_admin()) {

			echo html_entity_decode($this->purchase_model->delete_purinv_attachment($id));
		} else {

			header('HTTP/1.0 400 Bad error');

			echo _l('access_denied');

			die;
		}
	}



	/**

	 * { purchase estimate pdf }

	 *

	 * @param      <type>  $id     The identifier

	 * @return pdf output

	 */

	public function purestimate_pdf($id)
	{

		if (!$id) {

			redirect(admin_url('purchase/quotations'));
		}



		$pur_estimate = $this->purchase_model->get_purestimate_pdf_html($id);



		try {

			$pdf = $this->purchase_model->purestimate_pdf($pur_estimate, $id);
		} catch (Exception $e) {

			echo html_entity_decode($e->getMessage());

			die;
		}



		$type = 'D';



		if ($this->input->get('output_type')) {

			$type = $this->input->get('output_type');
		}



		if ($this->input->get('print')) {

			$type = 'I';
		}



		$pdf->Output(format_pur_estimate_number($id) . '.pdf', $type);
	}



	/**

	 * Sends a request quotation.

	 * @return redirect

	 */

	public function send_quotation()
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			foreach ($data['vendors'] as $id) {

				$vendor = $this->purchase_model->get_primary_contacts($id);

				$data['email'][] = $vendor->email;
			}



			if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {



				if (file_exists(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_quotation/' . $data['pur_estimate_id'])) {

					$delete_old = delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_quotation/' . $data['pur_estimate_id']);
				} else {

					$delete_old = true;
				}



				if ($delete_old == true) {

					handle_send_quotation($data['pur_estimate_id']);
				}
			}



			$send = $this->purchase_model->send_quotation($data);

			if ($send) {

				set_alert('success', _l('send_quotation_successfully'));
			} else {

				set_alert('warning', _l('send_quotation_fail'));
			}

			redirect(admin_url('purchase/quotations/' . $data['pur_estimate_id']));
		}
	}



	/**

	 * Sends a purchase order.

	 * @return redirect

	 */

	public function send_po()
	{

		if ($this->input->post()) {

			$data = $this->input->post();

			foreach ($data['vendor'] as $id) {

				$vendor = $this->purchase_model->get_primary_contacts($id);

				$data['email'][] = $vendor->email;
			}



			if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {



				if (file_exists(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_po/' . $data['po_id'])) {

					$delete_old = delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_po/' . $data['po_id']);
				} else {

					$delete_old = true;
				}



				if ($delete_old == true) {

					handle_send_po($data['po_id']);
				}
			}



			$send = $this->purchase_model->send_po($data);

			if ($send) {

				set_alert('success', _l('send_po_successfully'));
			} else {

				set_alert('warning', _l('send_po_fail'));
			}

			redirect(admin_url('purchase/purchase_order/' . $data['po_id']));
		}
	}



	/**

	 * import xlsx commodity

	 * @param  integer $id

	 * @return view

	 */

	public function import_xlsx_commodity()
	{

		if (!is_admin() && !has_permission_new('purchase', '', 'create')) {

			access_denied('purchase');
		}

		$this->load->model('staff_model');

		$data_staff = $this->staff_model->get(get_staff_user_id());



		/*get language active*/

		if ($data_staff) {

			if ($data_staff->default_language != '') {

				$data['active_language'] = $data_staff->default_language;
			} else {



				$data['active_language'] = get_option('active_language');
			}
		} else {

			$data['active_language'] = get_option('active_language');
		}

		$data['title'] = _l('import_excel');



		$this->load->view('items/import_excel', $data);
	}



	/**

	 * import file xlsx commodity

	 * @return json

	 */

	public function import_file_xlsx_commodity()
	{

		if (!is_admin() && !has_permission_new('purchase', '', 'create')) {

			access_denied(_l('purchase'));
		}



		$total_row_false = 0;

		$total_rows_data = 0;

		$dataerror = 0;

		$total_row_success = 0;

		$total_rows_data_error = 0;

		$filename = '';



		if ($this->input->post()) {



			/*delete file old before export file*/

			$path_before = COMMODITY_ERROR_PUR . 'FILE_ERROR_COMMODITY' . get_staff_user_id() . '.xlsx';

			if (file_exists($path_before)) {

				unlink(COMMODITY_ERROR_PUR . 'FILE_ERROR_COMMODITY' . get_staff_user_id() . '.xlsx');
			}



			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				//do_action('before_import_leads');



				// Get the temp file path

				$tmpFilePath = $_FILES['file_csv']['tmp_name'];

				// Make sure we have a filepath

				if (!empty($tmpFilePath) && $tmpFilePath != '') {

					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';



					if (!file_exists(TEMP_FOLDER)) {

						mkdir(TEMP_FOLDER, 0755);
					}



					if (!file_exists($tmpDir)) {

						mkdir($tmpDir, 0755);
					}



					// Setup our new file path

					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];



					if (move_uploaded_file($tmpFilePath, $newFilePath)) {

						$import_result = true;

						$rows = [];



						$objReader = new PHPExcel_Reader_Excel2007();

						$objReader->setReadDataOnly(true);

						$objPHPExcel = $objReader->load($newFilePath);

						$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

						$sheet = $objPHPExcel->getActiveSheet();



						//innit  file exel error start



						$dataError = new PHPExcel();

						$dataError->setActiveSheetIndex(0);

						//create header file



						// add style to the header

						$styleArray = array(

							'font' => array(

								'bold' => true,



							),



							'borders' => array(

								'top' => array(

									'style' => PHPExcel_Style_Border::BORDER_THIN,

								),

							),

							'fill' => array(



								'rotation' => 90,

								'startcolor' => array(

									'argb' => 'FFA0A0A0',

								),

								'endcolor' => array(

									'argb' => 'FFFFFFFF',

								),

							),

						);



						// set the names of header cells

						$dataError->setActiveSheetIndex(0)



							->setCellValue("A1", "(*)" . _l('commodity_code'))

							->setCellValue("B1", "(*)" . _l('commodity_name'))

							->setCellValue("C1", _l('commodity_barcode'))

							->setCellValue("D1", _l('sku_code'))

							->setCellValue("E1", _l('sku_name'))

							->setCellValue("F1", _l('description'))

							->setCellValue("G1", _l('unit_id'))

							->setCellValue("H1", _l('commodity_group'))

							->setCellValue("I1", _l('sub_group'))

							->setCellValue("J1", "(*)" . _l('purchase_price'))

							->setCellValue("K1", _l('rate'))

							->setCellValue("L1", _l('tax'));





						/*set style for header*/

						$dataError->getActiveSheet()->getStyle('A1:W1')->applyFromArray($styleArray);



						// auto fit column to content



						foreach (range('A', 'W') as $columnID) {

							$dataError->getActiveSheet()->getColumnDimension($columnID)

								->setAutoSize(true);
						}



						$dataError->getActiveSheet()->getStyle('A1:W1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);

						$dataError->getActiveSheet()->getStyle('A1:W1')->getFill()->getStartColor()->setARGB('29bb04');

						// Add some data

						$dataError->getActiveSheet()->getStyle('A1:W1')->getFont()->setBold(true);

						$dataError->getActiveSheet()->getStyle('A1:W1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



						/*set header middle alignment*/

						$dataError->getActiveSheet()->getStyle('A1:W1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



						$dataError->getActiveSheet()->getStyle('A1:W1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



						/*set row1 height*/

						$dataError->getActiveSheet()->getRowDimension('1')->setRowHeight(40);



						//init file error end



						// start row write 2

						$numRow = 2;

						$total_rows = 0;



						$total_rows_actualy = 0;



						//get data for compare



						foreach ($rowIterator as $row) {

							$rowIndex = $row->getRowIndex();

							if ($rowIndex > 1) {

								$rd = array();

								$flag = 0;

								$flag2 = 0;

								$flag_mail = 0;

								$string_error = '';

								$flag_contract_form = 0;



								$flag_id_unit_id;

								$flag_id_commodity_group;

								$flag_id_sub_group;

								$flag_id_tax;





								$value_cell_commodity_code = $sheet->getCell('A' . $rowIndex)->getValue();

								$value_cell_description = $sheet->getCell('B' . $rowIndex)->getValue();



								$value_cell_commodity_group = $sheet->getCell('H' . $rowIndex)->getValue();



								$value_cell_unit_id = $sheet->getCell('G' . $rowIndex)->getValue();



								$value_cell_sub_group = $sheet->getCell('I' . $rowIndex)->getValue();



								$value_cell_tax = $sheet->getCell('L' . $rowIndex)->getValue();



								$value_cell_rate = $sheet->getCell('K' . $rowIndex)->getValue();

								$value_cell_purchase_price = $sheet->getCell('J' . $rowIndex)->getValue();



								$pattern = '#^[a-z][a-z0-9\._]{2,31}@[a-z0-9\-]{3,}(\.[a-z]{2,4}){1,2}$#';



								$reg_day = '#^(((1)[0-2]))(\/)\d{4}-(3)[0-1])(\/)(((0)[0-9])-[0-2][0-9]$#'; /*yyyy-mm-dd*/



								/*check null*/

								if (is_null($value_cell_commodity_code) == true) {

									$string_error .= _l('commodity_code') . _l('not_yet_entered');

									$flag = 1;
								}



								if (is_null($value_cell_commodity_group) == true) {

									$string_error .= _l('commodity_group') . _l('not_yet_entered');

									$flag = 1;
								}





								if (is_null($value_cell_description) == true) {

									$string_error .= _l('commodity_name') . _l('not_yet_entered');

									$flag = 1;
								}



								//check unit_code exist  (input: id or name contract)

								if (is_null($value_cell_unit_id) != true && ($value_cell_unit_id != '0')) {

									/*case input id*/

									if (is_numeric($value_cell_unit_id)) {



										$this->db->where('unit_type_id', $value_cell_unit_id);

										$unit_id_value = $this->db->count_all_results(db_prefix() . 'ware_unit_type');



										if ($unit_id_value == 0) {

											$string_error .= _l('unit_id') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get id unit_id*/

											$flag_id_unit_id = $value_cell_unit_id;
										}
									} else {

										/*case input name*/

										$this->db->like(db_prefix() . 'ware_unit_type.unit_code', $value_cell_unit_id);



										$unit_id_value = $this->db->get(db_prefix() . 'ware_unit_type')->result_array();

										if (count($unit_id_value) == 0) {

											$string_error .= _l('unit_id') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get unit_id*/

											$flag_id_unit_id = $unit_id_value[0]['unit_type_id'];
										}
									}
								}



								//check commodity_group exist  (input: id or name contract)

								if (is_null($value_cell_commodity_group) != true && ($value_cell_commodity_group != '0')) {

									/*case input id*/

									if (is_numeric($value_cell_commodity_group)) {



										$this->db->where('id', $value_cell_commodity_group);

										$commodity_group_value = $this->db->count_all_results(db_prefix() . 'items_groups');



										if ($commodity_group_value == 0) {

											$string_error .= _l('commodity_group') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get id commodity_group*/

											$flag_id_commodity_group = $value_cell_commodity_group;
										}
									} else {

										/*case input name*/

										$this->db->like(db_prefix() . 'items_groups.commodity_group_code', $value_cell_commodity_group);



										$commodity_group_value = $this->db->get(db_prefix() . 'items_groups')->result_array();

										if (count($commodity_group_value) == 0) {

											$string_error .= _l('commodity_group') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get id commodity_group*/



											$flag_id_commodity_group = $commodity_group_value[0]['id'];
										}
									}
								}





								//check taxes exist  (input: id or name contract)

								if (is_null($value_cell_tax) != true && ($value_cell_tax != '0')) {

									/*case input id*/

									if (is_numeric($value_cell_tax)) {



										$this->db->where('id', $value_cell_tax);

										$cell_tax_value = $this->db->count_all_results(db_prefix() . 'taxes');



										if ($cell_tax_value == 0) {

											$string_error .= _l('tax') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get id cell_tax*/

											$flag_id_tax = $value_cell_tax;
										}
									} else {

										/*case input name*/

										$this->db->like(db_prefix() . 'taxes.name', $value_cell_tax);



										$cell_tax_value = $this->db->get(db_prefix() . 'taxes')->result_array();

										if (count($cell_tax_value) == 0) {

											$string_error .= _l('tax') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get id warehouse_id*/



											$flag_id_tax = $cell_tax_value[0]['id'];
										}
									}
								}



								//check commodity_group exist  (input: id or name contract)

								if (is_null($value_cell_sub_group) != true) {

									/*case input id*/

									if (is_numeric($value_cell_sub_group)) {



										$this->db->where('id', $value_cell_sub_group);

										$sub_group_value = $this->db->count_all_results(db_prefix() . 'wh_sub_group');



										if ($sub_group_value == 0) {

											$string_error .= _l('sub_group') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get id sub_group*/

											$flag_id_sub_group = $value_cell_sub_group;
										}
									} else {

										/*case input  name*/

										$this->db->like(db_prefix() . 'wh_sub_group.sub_group_code', $value_cell_sub_group);



										$sub_group_value = $this->db->get(db_prefix() . 'wh_sub_group')->result_array();

										if (count($sub_group_value) == 0) {

											$string_error .= _l('sub_group') . _l('does_not_exist');

											$flag2 = 1;
										} else {

											/*get id sub_group*/



											$flag_id_sub_group = $sub_group_value[0]['id'];
										}
									}
								}



								//check value_cell_rate input

								if (is_null($value_cell_rate) != true) {

									if (!is_numeric($value_cell_rate)) {

										$string_error .= _l('cell_rate') . _l('_check_invalid');

										$flag = 1;
									}
								}



								//check value_cell_rate input

								if (is_null($value_cell_purchase_price) != true) {

									if (!is_numeric($value_cell_purchase_price)) {

										$string_error .= _l('purchase_price') . _l('_check_invalid');

										$flag = 1;
									}
								}



								if (($flag == 1) || ($flag2 == 1)) {

									$dataError->getActiveSheet()->setCellValue('A' . $numRow, $sheet->getCell('A' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('B' . $numRow, $sheet->getCell('B' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('C' . $numRow, $sheet->getCell('C' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('D' . $numRow, $sheet->getCell('D' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('E' . $numRow, $sheet->getCell('E' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('F' . $numRow, $sheet->getCell('F' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('G' . $numRow, $sheet->getCell('G' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('H' . $numRow, $sheet->getCell('H' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('I' . $numRow, $sheet->getCell('I' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('J' . $numRow, $sheet->getCell('J' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('K' . $numRow, $sheet->getCell('K' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('M' . $numRow, $sheet->getCell('M' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('N' . $numRow, $sheet->getCell('N' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('O' . $numRow, $sheet->getCell('O' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('P' . $numRow, $sheet->getCell('P' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('Q' . $numRow, $sheet->getCell('Q' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('R' . $numRow, $sheet->getCell('R' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('R' . $numRow, $sheet->getCell('R' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('S' . $numRow, $sheet->getCell('S' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('T' . $numRow, $sheet->getCell('T' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('U' . $numRow, $sheet->getCell('U' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('V' . $numRow, $sheet->getCell('V' . $rowIndex)->getValue());





									$dataError->getActiveSheet()->setCellValue('W' . $numRow, $string_error)->getStyle('W' . $numRow)->applyFromArray($styleArray);



									$numRow++;

									$total_rows_data_error++;
								}



								if (($flag == 0) && ($flag2 == 0)) {





									/*staff id is HR_code, input is HR_CODE, insert => staffid*/

									$rd['commodity_code'] = $sheet->getCell('A' . $rowIndex)->getValue();

									$rd['commodity_barcode'] = $sheet->getCell('C' . $rowIndex)->getValue();

									$rd['sku_code'] = $sheet->getCell('D' . $rowIndex)->getValue();

									$rd['sku_name'] = $sheet->getCell('E' . $rowIndex)->getValue();

									$rd['description'] = $sheet->getCell('B' . $rowIndex)->getValue();

									$rd['long_description'] = $sheet->getCell('F' . $rowIndex)->getValue();

									$rd['unit_id'] = isset($flag_id_unit_id) ? $flag_id_unit_id : '';

									$rd['group_id'] = isset($flag_id_commodity_group) ? $flag_id_commodity_group : '';

									$rd['sub_group'] = isset($flag_id_sub_group) ? $flag_id_sub_group : '';

									$rd['tax'] = isset($flag_id_tax) ? $flag_id_tax : '';

									$rd['rate'] = $sheet->getCell('K' . $rowIndex)->getValue();

									$rd['purchase_price'] = $sheet->getCell('J' . $rowIndex)->getValue();
								}



								if (get_staff_user_id() != '' && $flag == 0 && $flag2 == 0) {

									$rows[] = $rd;

									$result_value = $this->purchase_model->import_xlsx_commodity($rd);

									if ($result_value) {

										$total_rows_actualy++;
									}
								}



								$total_rows++;

								$total_rows_data++;
							}
						}



						if ($total_rows_actualy != $total_rows) {

							$total_rows = $total_rows_actualy;
						}





						$total_rows = $total_rows;

						$data['total_rows_post'] = count($rows);

						$total_row_success = count($rows);

						$total_row_false = $total_rows - (int) count($rows);

						$dataerror = $dataError;

						$message = 'Not enought rows for importing';



						if (($total_rows_data_error > 0) || ($total_row_false != 0)) {

							$objWriter = new PHPExcel_Writer_Excel2007($dataError);



							$filename = 'FILE_ERROR_COMMODITY' . get_staff_user_id() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';

							$objWriter->save(str_replace($filename, PURCHASE_IMPORT_ITEM_ERROR . $filename, $filename));



							$filename = PURCHASE_IMPORT_ITEM_ERROR . $filename;
						}



						$import_result = true;

						@delete_dir($tmpDir);
					}
				} else {

					set_alert('warning', _l('import_upload_failed'));
				}
			}
		}

		echo json_encode([

			'message' => 'Not enought rows for importing',

			'total_row_success' => $total_row_success,

			'total_row_false' => $total_rows_data_error,

			'total_rows' => $total_rows_data,

			'site_url' => site_url(),

			'staff_id' => get_staff_user_id(),

			'total_rows_data_error' => $total_rows_data_error,

			'filename' => $filename,

		]);
	}



	/**

	 * { import vendor }

	 */

	public function vendor_import()
	{

		if (!has_permission_new('purchase', '', 'create')) {

			access_denied('purchase');
		}



		$this->load->model('staff_model');

		$data_staff = $this->staff_model->get(get_staff_user_id());



		/*get language active*/

		if ($data_staff) {

			if ($data_staff->default_language != '') {

				$data['active_language'] = $data_staff->default_language;
			} else {



				$data['active_language'] = get_option('active_language');
			}
		} else {

			$data['active_language'] = get_option('active_language');
		}

		$data['title'] = _l('import_excel');



		$this->load->view('vendors/import_excel', $data);
	}



	/**

	 * { reset data }

	 */

	public function reset_data()
	{



		if (!is_admin()) {

			access_denied('purchase');
		}



		//delete purchase request

		$this->db->truncate(db_prefix() . 'pur_request');

		//delete purchase request detail

		$this->db->truncate(db_prefix() . 'pur_request_detail');

		//delete purchase order

		$this->db->truncate(db_prefix() . 'pur_orders');

		//delete purchase order detail

		$this->db->truncate(db_prefix() . 'pur_order_detail');

		//delete purchase order payment

		$this->db->truncate(db_prefix() . 'pur_order_payment');

		//delete purchase invoice

		$this->db->truncate(db_prefix() . 'pur_invoices');

		//delete purchase invoice payment

		$this->db->truncate(db_prefix() . 'pur_invoice_payment');

		//delete purchase estimate

		$this->db->truncate(db_prefix() . 'pur_estimates');

		//delete pur_estimate_detail

		$this->db->truncate(db_prefix() . 'pur_estimate_detail');

		//delete pur_contracts

		$this->db->truncate(db_prefix() . 'pur_contracts');

		//delete tblpur_approval_details

		$this->db->truncate(db_prefix() . 'pur_approval_details');



		//delete create task rel_type: "pur_contract", "pur_contract".

		$this->db->where('rel_type', 'pur_contract');

		$this->db->or_where('rel_type', 'pur_order');

		$this->db->or_where('rel_type', 'pur_quotation');

		$this->db->or_where('rel_type', 'pur_invoice');

		$this->db->delete(db_prefix() . 'tasks');





		$this->db->where('rel_type', 'pur_contract');

		$this->db->or_where('rel_type', 'pur_order');

		$this->db->or_where('rel_type', 'pur_estimate');

		$this->db->or_where('rel_type', 'pur_invoice');

		$this->db->delete(db_prefix() . 'files');



		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/signature/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_invoice/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/signature/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/payment_invoice/signature/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/payment_request/signature/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/request_quotation/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_po/');

		delete_files_pur(PURCHASE_MODULE_UPLOAD_FOLDER . '/send_quotation/');



		$this->db->where('rel_type', 'pur_contract');

		$this->db->or_where('rel_type', 'purchase_order');

		$this->db->or_where('rel_type', 'pur_invoice');

		$this->db->delete(db_prefix() . 'notes');



		$this->db->where('rel_type', 'pur_contract');

		$this->db->or_where('rel_type', 'purchase_order');

		$this->db->or_where('rel_type', 'pur_invoice');

		$this->db->delete(db_prefix() . 'reminders');



		$this->db->where('fieldto', 'pur_order');

		$this->db->delete(db_prefix() . 'customfieldsvalues');



		$this->db->where('rel_type', 'pur_invoice');

		$this->db->or_where('rel_type', 'pur_order');

		$this->db->delete(db_prefix() . 'taggables');



		set_alert('success', _l('reset_data_successful'));



		redirect(admin_url('purchase/setting'));
	}



	/**

	 * Removes a po logo.

	 */

	public function remove_po_logo()
	{

		if (!has_permission_new('purchase', '', 'delete') || !is_admin()) {

			access_denied('purchase');
		}



		$success = $this->purchase_model->remove_po_logo();

		if ($success) {

			set_alert('success', _l('deleted', _l('po_logo')));
		}

		redirect(admin_url('purchase/setting'));
	}

	public function EditPurchaseEntry($id = "")
	{

		if (!has_permission_new('purchase-order', '', 'view')) {

			access_denied('purchase');
		}

		// echo $id;die;

		if ($this->input->post()) {

			$pur_order_data = $this->input->post();

			if (!has_permission_new('purchase-order', '', 'edit')) {

				access_denied('purchase');
			}

			$idd = $this->purchase_model->update_purchase_order($pur_order_data, $id);



			if ($idd === true) {

				set_alert('success', _l('updated_successfully', _l('pur_order')));
			} elseif ($idd == 'QCNOTOK') {

				set_alert('warning', _l('Please Complete QC', _l('pur_order')));
			} else {

				set_alert('warning', _l('Something went wrong', _l('pur_order')));
			}

			redirect(admin_url('purchase/EditPurchaseEntry/' . $id));
		}

		$this->load->model('QcMaster_model');

		$title = "Edit Purchase Order";

		$data['purchase_details'] = $this->purchase_model->get_unique_purchasemaster($id);

		// print_r($data['purchase_details']);die;

		$data['purchase_history'] = $this->purchase_model->get_unique_history($id);

		$data['vendors'] = $this->purchase_model->GetRMVendor();

		$data['units'] = $this->purchase_model->get_units();

		$data['items'] = $this->purchase_model->get_items();

		$data['item_code'] = $this->purchase_model->get_items_code();

		$data['accounts'] = $this->purchase_model->get_accounts_list();

		$data['freight_id'] = $this->purchase_model->get_accounts_freightid();

		// print_r($data['freight_id']);die;

		$data['GodownData'] = $this->purchase_model->GetGodownData();

		$data['other_id'] = $this->purchase_model->get_accounts_othertid();

		$data['pur_order_detail'] = json_encode($this->purchase_model->get_p_order_detail($id));

		$data['charges_detail'] = json_encode($this->purchase_model->get_charges_entry_detail_full($id));

		$data['Order_list'] = $this->purchase_model->get_Order_list();

		$data['acc_head'] = $this->purchase_model->get_acc_head();

		$data['QCItemsList'] = $this->QcMaster_model->GetQCApplicableItem($id);

		$data['title'] = $title;



		// print_r($data['purchase_details']);die;

		$this->load->view('purchase_order/pur_order_list', $data);
	}



	public function load_data_for_purchase()
	{

		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'status' => $this->input->post('status'),

		);

		$data = $this->purchase_model->load_data_for_purchase($data);

		echo json_encode($data);
	}

	public function report_data()
	{

		$title = _l('Market Outstanding');

		$data['title'] = $title;

		$data['vendors'] = $this->purchase_model->get_vendor_data();

		$this->load->view('purchase_order/market_outstanding', $data);
	}

	public function export_vendor_report()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {





			$data = $this->purchase_model->table_data($this->input->post());

			$this->load->model('sale_reports_model');

			$selected_company_details = $this->sale_reports_model->get_company_detail();



			$writer = new XLSXWriter();

			//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

			//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');



			$company_name = array($selected_company_details->company_name);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 7);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);



			$address = $selected_company_details->address;

			$company_addr = array($address,);

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 7);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);

			if ($this->input->post('status') == "1") {

				$status = "Active";
			} else {

				$status = "Deactive";
			}

			$msg = "Vendor Report State : " . $this->input->post('states') . " Status :  " . $status;

			$filter = array($msg);

			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 7);  //merge cells

			$writer->writeSheetRow('Sheet1', $filter);



			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);





			$set_col_tk = [];

			$set_col_tk["AccountID"] = 'AccountID';

			$set_col_tk["FirmName"] = 'FirmName';

			$set_col_tk["Station"] = 'Station';

			$set_col_tk["City"] = 'City';

			$set_col_tk["State"] = 'State';

			$set_col_tk["Address"] = 'Address';

			$set_col_tk["Status"] = 'Status';



			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);





			foreach ($data as $k => $value) {



				$list_add = [];

				$list_add[] = $value["AccountID"];

				$list_add[] = $value["company"];

				$list_add[] = $value["StationName"];

				$city_name = get_city_name($value['city']);

				if ($city_name->city_name) {

					$city = $city_name->city_name;
				} else {

					$city = $value['city'];
				}

				$row = $city;

				$list_add[] = $row;

				$list_add[] = $value["state"];

				$list_add[] = nl2br($value["address"]);

				if ($value['actstatus'] == 1) {

					$status = "Active";
				} else {

					$status = "DeActive";
				}

				$list_add[] = $status;



				$writer->writeSheetRow('Sheet1', $list_add);
			}





			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'Vendor_Report.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}

	// public function load_data()
	// {
	// 	$approve_status = '';

	// 	$data = $this->purchase_model->table_data($this->input->post());

	// 	$states = $this->input->post('states');

	// 	$status = $this->input->post('status');

	// 	$data_state_name = $this->db->get_where('tblxx_statelist', array('short_name' => $states))->row_array();

	// 	// echo $this->db->last_query();

	// 	if ($data_state_name == '') {

	// 		$data_state_name['state_name'] = '';

	// 	}

	// 	if ($status == '') {

	// 		$status = '';

	// 	}

	// 	$html = '';

	// 	foreach ($data as $value) {


	// 		$html .= '<tr>';

	// 		$html .= '<td>' . $value['SubActGroupName'] . '</td>';


	// 		$html .= '<td>' . $value['AccountID'] . '</td>';

	// 		$companyy = $value['company'];

	// 		$isPerson = false;



	// 		if ($companyy == '') {

	// 			$companyy = _l('no_company_view_profile');

	// 			$isPerson = true;

	// 		}



	// 		$url = admin_url('purchase/vendor/' . $value['AccountID']);



	// 		if ($isPerson && $value['contact_id']) {

	// 			$url .= '?contactid=' . $value['contact_id'];

	// 		}

	// 		$companyy = '<a href="' . $url . '">' . $companyy . '</a>';

	// 		$company = '';

	// 		$company .= '<div class="row-options">';

	// 		$company .= '<a href="' . $url . '">' . _l('view') . '</a>';



	// 		if (($aRow['registration_confirmed'] ?? 0) == 0 && is_admin()) {

	// 			$company .= ' | <a href="' . admin_url('purchase/confirm_registration/' . ($aRow['AccountID'] ?? 0)) . '" class="text-success bold">' . _l('confirm_registration') . '</a>';

	// 		}

	// 		if (!$isPerson) {

	// 			$company .= ' | <a href="' . admin_url('purchase/vendor/' . ($aRow['AccountID'] ?? 0) . '?group=contacts') . '">' . _l('customer_contacts') . '</a>';

	// 		}


	// 		if ($hasPermissionDelete ?? '') {

	// 			$company .= ' | <a href="' . admin_url('purchase/delete_vendor/' . ($aRow['AccountID'] ?? 0)) . '" class="text-danger _delete">' . _l('delete') . '</a>';

	// 		}



	// 		$company .= '</div>';



	// 		$row_c = $companyy;

	// 		$vendor_name = $value['company'];

	// 		$html .= '<td>' . substr($vendor_name, 0, 40) . '</td>';

	// 		$html .= '<td>' . $value['acc_name'] . '</td>';


	// 		if ($value['actstatus'] == 1) {

	// 			$status = "Active";

	// 		} else {

	// 			$status = "DeActive";

	// 		}






	// 		// if($value['approved'] == 1){

	// 		// 	$vndr_status = "Approved";

	// 		// 	}else{

	// 		// 	$vndr_status = "Not Approved";

	// 		// }



	// 		// $html.= '<td>'.$vndr_status.'</td>';

	// 		$html .= '<td>' . substr(nl2br($value['address']), 0, 50) . '</td>';

	// 		$html .= '<td>' . $value['state_name'] . '</td>';


	// 		$city_name = get_city_name($value['city']);

	// 		if ($city_name->city_name) {

	// 			$city = $city_name->city_name;

	// 		} else {

	// 			$city = $value['city'];

	// 		}


	// 		$row = $city;

	// 		$html .= '<td>' . $row . '</td>';

	// 		// $html.= '<td>'.($value['city']).'</td>';

	// 		$html .= '<td>' . ($value['zip']) . '</td>';



	// 		$html .= '<td>' . $value['StationName'] . '</td>';


	// 		$html .= '<td>' . ($value['phonenumber']) . '</td>';

	// 		$html .= '<td>' . ($value['altphonenumber'] ?? 0) . '</td>';
	// 		$html .= '<td>' . ($value['email'] ?? '') . '</td>';

	// 		$html .= '<td>' . $value['vat'] . '</td>';
	// 		$html .= '<td>' . $status . '</td>';
	// 		$html .= '<td>' . $approve_status . '</td>';

	// 		$html .= '</tr>';

	// 	}

	// 	// echo $html;

	// 	$data_array = array('html' => $html, 'state' => $data_state_name, 'status' => $status);

	// 	echo json_encode($data_array);

	// }



	// purchase return



	public function pur_return()
	{



		if ($this->input->post()) {

			$pur_order_data = $this->input->post();

			$pur_order_data['terms'] = nl2br($pur_order_data['terms']);

			if ($id == '') {



				if (!has_permission_new('purchase-return', '', 'create')) {

					access_denied('purchase');
				}

				$id = $this->purchase_model->add_pur_return_order($pur_order_data);



				if ($id) {

					set_alert('success', _l('added_successfully', _l('pur_return')));



					redirect(admin_url('purchase/pur_return'));
				}
			} else {
			}
		}



		if ($id == '') {

			$title = _l('Create purchase return');
		} else {



			$title = _l('pur_return_order_detail');
		}



		$this->load->model('currencies_model');

		$data['base_currency'] = $this->currencies_model->get_base_currency();



		$this->load->model('clients_model');



		$this->load->model('departments_model');

		$data['departments'] = $this->departments_model->get();



		$data['taxes'] = $this->purchase_model->get_taxes();

		$data['staff'] = $this->staff_model->get('', ['active' => 1]);

		$data['vendors'] = $this->purchase_model->get_vendor_data();

		// print_r( $data['vendors'] );die;



		$data['units'] = $this->purchase_model->get_units();

		// $data['items'] = $this->purchase_model->get_items_for_purchRtn();

		$data['items'] = array();

		$data['item_code'] = $this->purchase_model->get_items_code_purReturn();

		$data['accounts'] = $this->purchase_model->get_accounts_list();

		$data['freight_id'] = $this->purchase_model->get_accounts_freightid();

		$data['other_id'] = $this->purchase_model->get_accounts_othertid();



		$data['title'] = $title;



		$this->load->view('purchase_order_return/pur_return', $data);
	}

	public function items_change_purchaseId($item_id, $purchase_id)
	{



		$value = $this->purchase_model->items_change_purchaseId($item_id, $purchase_id);



		echo json_encode([

			'value' => $value

		]);
	}

	public function load_data_for_purchaseRtn()
	{

		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date')

		);

		$data = $this->purchase_model->load_data_for_purchaseRtn($data);

		echo json_encode($data);
	}

	public function purchaseRtn_list($id = "")
	{

		if ($this->input->post()) {

			$purRtn_order_data = $this->input->post();

			if (!has_permission_new('purchase-return', '', 'edit')) {

				access_denied('purchase');
			}

			$idd = $this->purchase_model->update_purchaseRtn_order($purRtn_order_data, $id);

			if ($idd) {

				set_alert('success', _l('updated_successfully', _l('pur_order')));
			} else {

				set_alert('error', _l('Some thing went wrong', _l('pur_order')));
			}

			redirect(admin_url('purchase/purchaseRtn_list/' . $id));
		}

		$title = "Edit PurchaseRtn";

		$data['purchase_details'] = $this->purchase_model->get_unique_purchasereturn($id);

		//$data['purchase_history'] = $this->purchase_model->get_unique_historyreturn($id);

		$data['vendors'] = $this->purchase_model->get_vendor_data();

		$data['units'] = $this->purchase_model->get_units();

		// $data['items'] = $this->purchase_model->get_items_for_purchRtn();

		$data['items'] = $this->purchase_model->GetVendorWiseItemsEditReturn($data['purchase_details']->AccountID);

		$data['item_code'] = $this->purchase_model->get_items_code_purReturn();

		$data['accounts'] = $this->purchase_model->get_accounts_list();

		$data['freight_id'] = $this->purchase_model->get_accounts_freightid();

		$data['other_id'] = $this->purchase_model->get_accounts_othertid();

		$ForView = $this->purchase_model->get_pReturn_order_detail($id);

		$forJS = json_encode($ForView);

		$data['pur_order_detail'] = $forJS;

		$data['purchase_history'] = $ForView;

		$this->load->model('sale_reports_model');

		$data['company_detail'] = $this->sale_reports_model->get_company_detail();

		//$ss = $this->purchase_model->get_pReturn_order_detail($id);

		/*echo "<pre>";

			print_r($data['purchase_history']);



		die;*/

		$data['Order_list'] = $this->purchase_model->get_Order_list();

		$data['title'] = $title;

		$this->load->view('purchase_order_return/purRtn_order_list', $data);
	}



	// end purchase return



	/* List all QC Unit */

	public function QC_Unit()
	{

		if (!has_permission_new('qc_unit', '', 'view')) {

			access_denied('Invoice Items');
		}

		$data['route_table'] = $this->purchase_model->get_data_table_unit();

		$data['company_detail'] = $this->purchase_model->get_company_detail();

		/*echo "<pre>";

			print($data['route']);

			die;

		*/

		$data['title'] = "QC Unit";

		$this->load->view('QC/QC_Unit', $data);
	}





	/* Edit or update Units / ajax request /*/

	public function manage_unit()
	{

		if (has_permission_new('qc_unit', '', 'view')) {

			if ($this->input->post()) {

				$data = $this->input->post();



				if ($data['itemid'] == '') {

					if (!has_permission_new('qc_unit', '', 'create')) {

						header('HTTP/1.0 400 Bad error');

						echo _l('access_denied');

						die;
					}

					$id = $this->purchase_model->add_unit($data);

					$success = false;

					$message = '';

					if ($id) {

						$success = true;

						$message = _l('added_successfully', _l('sales_item'));
					}

					echo json_encode([

						'success' => $success,

						'message' => $message,

					]);
				} else {

					if (!has_permission_new('qc_unit', '', 'edit')) {

						header('HTTP/1.0 400 Bad error');

						echo _l('access_denied');

						die;
					}

					$success = $this->purchase_model->edit_unit($data);

					$message = '';

					if ($success) {

						$message = _l('updated_successfully', _l('sales_item'));
					}

					echo json_encode([

						'success' => $success,

						'message' => $message,

					]);
				}
			}

			redirect(admin_url('purchase/QC_Unit'));
		}
	}



	public function get_unit_by_id($id)
	{

		if ($this->input->is_ajax_request()) {

			$units = $this->purchase_model->get_unit_by_id($id);





			echo json_encode($units);
		}
	}





	public function export_QC_Unit()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {



			$data = $this->purchase_model->get_data_table_unit();

			$selected_company_details = $this->purchase_model->get_company_detail();



			$writer = new XLSXWriter();

			//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

			//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');



			$company_name = array($selected_company_details->company_name);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);



			$address = $selected_company_details->address;

			$company_addr = array($address,);

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 4);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);







			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);



			$set_col_tk = [];

			$set_col_tk["Route Name"] = 'Unit Name';

			$set_col_tk["Route KM"] = 'Measured In';

			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);





			foreach ($data as $k => $value) {



				$list_add = [];

				$list_add[] = $value["unit_name"];

				$list_add[] = $value["measured_in"];



				$writer->writeSheetRow('Sheet1', $list_add);
			}

			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'QCUnit_Master.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}



	public function QC_Parameter()
	{

		if (!has_permission_new('qc_parameter', '', 'view')) {

			access_denied('Invoice Items');
		}

		$data['unit_data'] = $this->purchase_model->get_data_table_unit();

		$data['table_data'] = $this->purchase_model->get_data_table_parameter();

		$data['company_detail'] = $this->purchase_model->get_company_detail();

		/*echo "<pre>";

			print($data['route']);

			die;

		*/

		$data['title'] = "QC Parameter";

		$this->load->view('QC/QC_Parameter', $data);
	}



	/* Edit or update Units / ajax request /*/

	public function manage_parameter()
	{

		if (has_permission_new('qc_parameter', '', 'view')) {

			if ($this->input->post()) {

				$data = $this->input->post();



				if ($data['itemid'] == '') {

					if (!has_permission_new('qc_parameter', '', 'create')) {

						header('HTTP/1.0 400 Bad error');

						echo _l('access_denied');

						die;
					}

					$id = $this->purchase_model->add_parameter($data);

					$success = false;

					$message = '';

					if ($id) {

						$success = true;

						$message = _l('added_successfully', _l('sales_item'));
					}

					echo json_encode([

						'success' => $success,

						'message' => $message,

					]);
				} else {

					if (!has_permission_new('qc_parameter', '', 'edit')) {

						header('HTTP/1.0 400 Bad error');

						echo _l('access_denied');

						die;
					}

					$success = $this->purchase_model->edit_parameter($data);

					$message = '';

					if ($success) {

						$message = _l('updated_successfully', _l('sales_item'));
					}

					echo json_encode([

						'success' => $success,

						'message' => $message,

					]);
				}
			}

			redirect(admin_url('purchase/QC_Parameter'));
		}
	}



	public function get_parameter_by_id($id)
	{

		if ($this->input->is_ajax_request()) {

			$units = $this->purchase_model->get_parameter_by_id($id);





			echo json_encode($units);
		}
	}



	public function export_QC_Parameter()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {



			$data = $this->purchase_model->get_data_table_parameter();

			$selected_company_details = $this->purchase_model->get_company_detail();



			$writer = new XLSXWriter();

			//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

			//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');



			$company_name = array($selected_company_details->company_name);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);



			$address = $selected_company_details->address;

			$company_addr = array($address,);

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 4);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);







			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);



			$set_col_tk = [];

			$set_col_tk["Parameter Name"] = 'Parameter Name';

			$set_col_tk["Unit Name"] = 'Unit Name';

			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);





			foreach ($data as $k => $value) {



				$list_add = [];

				$list_add[] = $value["parameter_name"];

				$list_add[] = $value["unit_name"];



				$writer->writeSheetRow('Sheet1', $list_add);
			}

			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'QCParameter_Master.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}



	public function QC_Master($id = '')
	{

		if (!has_permission_new('qc_master', '', 'view')) {

			access_denied('Invoice Items');
		}



		$data['item_list'] = $this->purchase_model->get_data_items();



		$data['parameter_data'] = $this->purchase_model->get_data_table_parameter();

		$data['ItemMainGroups'] = $this->purchase_model->ItemMainGroups();

		$data['table_data'] = $this->purchase_model->get_data_table_qc_master();





		$data['company_detail'] = $this->purchase_model->get_company_detail();

		// echo "<pre>";

		// print_r($data['ItemMainGroups']);

		// die;



		$data['title'] = "QC Master";

		$this->load->view('QC/QC_Master', $data);
	}

	public function GetItemListbyMainGroup()
	{

		$main_group = $this->input->post('main_group');

		$data = $this->purchase_model->get_data_items($main_group);

		echo json_encode($data);
	}

	public function GetItemListbyGroups()
	{

		$main_group = $this->input->post('main_group');

		$SubGroup1 = $this->input->post('SubGroup1');

		$SubGroup2 = $this->input->post('SubGroup2');

		$data = $this->purchase_model->GetItemListbyGroups($main_group, $SubGroup1, $SubGroup2);

		echo json_encode($data);
	}

	public function GetQCMasterDetailByItemID()
	{



		$item_id = $this->input->post('item_id');

		$data = $this->purchase_model->get_master_data_byId($item_id);

		echo json_encode($data);
	}

	public function GetQCMasterDetailByItemID_edit()
	{



		$item_id = $this->input->post('item_id');

		$data = $this->purchase_model->get_master_data_byItemId($item_id);

		echo json_encode($data);
	}

	public function DeleteQCMaster()
	{



		$item_id = $this->input->post('item_id');

		$data = $this->purchase_model->DeleteQCMaster($item_id);

		echo json_encode($data);
	}

	public function DeleteQCParameter()
	{



		$id = $this->input->post('id');

		$data = $this->purchase_model->DeleteQCParameter($id);

		echo json_encode($data);
	}

	public function DeleteQCUnit()
	{



		$id = $this->input->post('id');

		$data = $this->purchase_model->DeleteQCUnit($id);

		echo json_encode($data);
	}

	public function DeleteQCMasterParameter()
	{



		$id = $this->input->post('id');

		$data = $this->purchase_model->DeleteQCMasterParameter($id);

		echo json_encode($data);
	}



	public function UpdateQCMaster()
	{

		$data = $this->input->post();



		$data = $this->purchase_model->UpdateQCMaster($data);

		echo json_encode($data);
	}



	public function export_QC_Master()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {



			$data = $this->purchase_model->get_data_table_qc_master();

			$selected_company_details = $this->purchase_model->get_company_detail();



			$writer = new XLSXWriter();

			//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

			//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

			//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');



			$company_name = array($selected_company_details->company_name);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);



			$address = $selected_company_details->address;

			$company_addr = array($address,);

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 4);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);







			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);



			$set_col_tk = [];

			$set_col_tk["ItemId"] = 'ItemId';

			$set_col_tk["Item Name"] = 'Item Name';

			$set_col_tk["Unit"] = 'Unit';

			$set_col_tk["Sub-Group Name"] = 'Sub-Group Name';

			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);





			foreach ($data as $k => $value) {



				$list_add = [];

				$list_add[] = $value["ItemID"];

				$list_add[] = $value["description"];

				$list_add[] = $value["unit"];

				$list_add[] = $value["subgroup_name"];



				$writer->writeSheetRow('Sheet1', $list_add);
			}

			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'QC_Master.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}









	public function load_data_for_purchaseOrder()
	{

		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'status' => $this->input->post('status')

		);

		$data = $this->purchase_model->load_data_for_purchaseOrder($data);

		echo json_encode($data);
	}



	public function load_data_for_PendingpurchaseOrder()
	{

		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'status' => $this->input->post('status'),

		);



		$ReportType = $this->input->post('ReportType');

		if ($ReportType == 'BillWise') {

			$result = $this->purchase_model->load_data_for_PendingpurchaseOrder($data);
		} else {

			$result = $this->purchase_model->load_data_for_PendingpurchaseOrderItemWise($data);
		}



		// echo "<pre>";print_r($result);die;

		$html = '<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">';

		$html .= '<thead>';

		$html .= '<tr>';

		$html .= '<th class="sortable">PO. No.</th>';

		$html .= '<th class="sortable">PO. Date</th>';

		$html .= '<th class="sortable">Purchased From</th>';

		if ($ReportType == 'ItemWise') {

			$html .= '<th>Item Name</th>';

			$html .= '<th>Expected Delivery Date</th>';

			$html .= '<th>Extended Delivery Date</th>';
		}

		$html .= '<th class="sortable">Purchase Qty</th>';

		$html .= '<th class="sortable">Received Qty</th>';

		if ($ReportType == 'BillWise') {

			$html .= '<th>Mark As Complete</th>';
		}

		if ($ReportType == 'ItemWise') {

			$html .= '<th>Extend Date</th>';
		}

		$html .= '</tr>';

		$html .= '</thead>';

		$html .= '<tbody>';



		if (!empty($result)) {

			foreach ($result as $row) {

				$url = admin_url() . 'purchase/EditPurchaseOrder/' . $row['PurchID'];

				$date_new = date('d/m/Y', strtotime($row['Transdate']));



				$html .= '<tr>';

				$html .= '<td style="text-align:center;"><a href="' . $url . '" target="_blank">' . $row['PurchID'] . '</a></td>';

				$html .= '<td style="text-align:center;">' . $date_new . '</td>';

				$html .= '<td>' . $row['AccountName'] . '</td>';

				if ($ReportType == 'ItemWise') {

					$html .= '<td>' . $row['description'] . '</td>';

					if (empty($row['NewDelivery_Date'])) {

						$ExpectedDate = $row['Delivery_Date'];

						$NewDate = '';
					} else {

						$ExpectedDate = $row['NewDelivery_Date'];

						$NewDate = $row['Delivery_Date'];
					}

					$html .= '<td>' . _d(substr($row['Delivery_Date'], 0, 10)) . '</td>';

					$html .= '<td>' . _d(substr($NewDate, 0, 10)) . '</td>';



					$deliveryDate = date('Y-m-d', strtotime($ExpectedDate));

					$today = date('Y-m-d');
				}

				$html .= '<td style="text-align:right;">' . $row['OrderQty'] . '</td>';

				$html .= '<td style="text-align:right;">' . $row['ReceivedQty'] . '</td>';

				if ($ReportType == 'BillWise') {

					$html .= '<td align="center"><button type="button" onclick="CompletePendingOrder(\'' . $row['PurchID'] . '\')"><i class="fa fa-check"></i></button></td>';
				}

				if ($ReportType == 'ItemWise') {

					if ($deliveryDate < $today) {

						$html .= '<td align="center"><button type="button" onclick="ExtendeDate(\'' . $row['PurchID'] . '\',\'' . $date_new . '\',\'' . _d(substr($ExpectedDate, 0, 10)) . '\',\'' . $row['ItemID'] . '\',\'' . $row['description'] . '\',\'' . $row['AccountName'] . '\')"><i class="fa fa-check"></i></button></td>';
					} else {

						$html .= '<td align="center"></td>';
					}
				}

				$html .= '</tr>';
			}
		} else {

			$html .= '<tr><td colspan="6" style="text-align:center;">No Records Found</td></tr>';
		}



		$html .= '</tbody>';

		$html .= '</table>';

		echo json_encode($html);
	}



	public function ItemDateExtension()
	{

		if (!has_permission('purchase-order-po-list', '', 'edit')) {

			access_denied('purchase-order-po-list');
		}



		$selected_company = $this->session->userdata('root_company');



		if ($this->input->post()) {

			$Postdata = array(

				"PurchID" => $this->input->post('eventId'),

				"ItemID" => $this->input->post('ItemID'),

				"PurchDate" => $this->input->post('order_date'),

				"extension_date" => $this->input->post('extension_date'),

				"extension_remark" => $this->input->post('extension_remark')

			);
		}

		$success = $this->purchase_model->ItemDateExtension($Postdata);

		if ($success) {

			$this->db->select(db_prefix() . 'staff.*');

			$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'staff.admin', '1');

			$AdminStaff = $this->db->get(db_prefix() . 'staff')->result_array();



			$this->db->select(db_prefix() . 'items.*');

			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'items.item_code', $this->input->post('ItemID'));

			$ItemData = $this->db->get(db_prefix() . 'items')->row();



			foreach ($AdminStaff as $admin) {

				$Notification_msg = "Item Date Extend (" . $ItemData->description . ")(" . $this->input->post('eventId') . ")";

				$notification_data = [

					'description' => $Notification_msg,

					'touserid' => $admin['staffid'],

					'link' => 'purchase/EditPurchaseOrder/' . $this->input->post('eventId'),

				];

				$notification_data['additional_data'] = serialize([

					'Purch Order No. ' . $this->input->post('eventId'),

				]);



				if (add_notification($notification_data)) {

					pusher_trigger_notification($admin['staffid']);
				}
			}



			set_alert('success', _l('Date Extended Successfully', 'Purchase Order'));
		} else {

			set_alert('warning', _l('Something went wrong, Please try again', 'Purchase Order'));
		}

		redirect(admin_url('purchase/PendingPurchaseOrderList'));
	}



	public function ApprovePO($id)
	{

		$success = $this->purchase_model->ApprovePO($id);

		if ($success) {

			set_alert('success', _l('successfully approved'));
		}

		redirect(admin_url('purchase/EditPurchaseOrder/') . $id);
	}



	public function GetPOData()
	{

		// POST data

		$PoNumber = $this->input->post('PoNumber');

		// Get data

		$InwardData['ordertbl'] = $this->purchase_model->get_purchase_order_master_data($PoNumber);

		$InwardData['historytbl'] = $this->purchase_model->get_p_order_detail($PoNumber);

		echo json_encode($InwardData);
	}



	public function PurchaseOrderList()
	{

		if (!has_permission_new('purchase-order-po-list', '', 'view')) {

			access_denied('purchase');
		}



		$title = _l('Purchase Order Report');



		$data['PlantDetail'] = $this->purchase_model->GetPlantDetails();

		$data['title'] = $title;



		$this->load->view('purchase_order/PurchaseOrderList', $data);
	}

	public function PendingPurchaseOrderList()
	{

		if (!has_permission_new('purchase-order-po-list', '', 'view')) {

			access_denied('purchase');
		}



		$title = _l('Purchase Order Report');



		$data['PlantDetail'] = $this->purchase_model->GetPlantDetails();

		$data['title'] = $title;



		$this->load->view('purchase_order/PendingPurchaseOrderList', $data);
	}



	public function export_purchase_order_PO()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {



			$data = array(

				'from_date' => $this->input->post('from_date'),

				'to_date' => $this->input->post('to_date'),

				'status' => $this->input->post('status')

			);

			$data = $this->purchase_model->load_data_for_purchaseOrder($data);

			$status = $this->input->post('status');

			if (empty($status)) {

				$status = "All";
			}

			$PlantDetail = $this->purchase_model->GetPlantDetails();

			$writer = new XLSXWriter();



			$company_name = array($PlantDetail->FIRMNAME);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 9);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);



			$address = $PlantDetail->ADDRESS1 . ' ' . $PlantDetail->ADDRESS2;

			$company_addr = array($address,);

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 9);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);



			$msg = "Purchase Order Report " . $this->input->post('from_date') . " To " . $this->input->post('to_date') . " - Status : " . $status;

			$filter = array($msg);

			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 9);  //merge cells

			$writer->writeSheetRow('Sheet1', $filter);



			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);





			$set_col_tk = [];

			$set_col_tk["PO. No."] = 'PO. No.';

			$set_col_tk["Date"] = 'Date';

			$set_col_tk["Purchased From"] = 'Purchased From';

			$set_col_tk["Purchase Amt"] = 'Purchase Amt';

			$set_col_tk["Disc"] = 'Disc';

			$set_col_tk["CGST Amt"] = 'CGST Amt';

			$set_col_tk["SGST Amt"] = 'SGST Amt';

			$set_col_tk["IGST Amt"] = 'IGST Amt';

			$set_col_tk["Inv. Amt"] = 'Inv. Amt';

			$set_col_tk["Status"] = 'Status';

			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);



			$i = 0;

			$total = 0;

			$rowspan = 0;

			$grand_total = 0;

			foreach ($data as $k => $value) {



				$list_add = [];

				$list_add[] = $value["PurchID"];

				$date = _d(substr($value["Transdate"], 0, 10));

				$list_add[] = $date;

				$list_add[] = $value["AccountName"];

				$list_add[] = $value["Purchamt"];

				$list_add[] = $value["Discamt"];

				$list_add[] = $value["cgstamt"];

				$list_add[] = $value["sgstamt"];

				$list_add[] = $value["igstamt"];

				$list_add[] = $value["Invamt"];

				$list_add[] = $value["cur_status"];



				$writer->writeSheetRow('Sheet1', $list_add);
			}





			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'PurchaseOrderReport.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}



	public function Purchase_entry_Report()
	{

		if (!has_permission_new('purchase-order-list', '', 'view')) {

			access_denied('purchase');
		}

		$title = _l('Purchase Entry Report');

		$data['PlantDetail'] = $this->purchase_model->GetPlantDetails();

		// var_dump($data['PlantDetail']);die;

		$data['title'] = $title;

		$this->load->view('purchase_order/Purchase_entry_Report', $data);
	}



	public function export_purchase_Entries()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {



			$data = array(

				'from_date' => $this->input->post('from_date'),

				'to_date' => $this->input->post('to_date'),

				'status' => $this->input->post('status')

			);

			$status = $this->input->post('status');

			if (empty($status)) {

				$status = "All";
			}

			$data = $this->purchase_model->load_data_for_purchase($data);



			$PlantDetail = $this->purchase_model->GetPlantDetails();

			$writer = new XLSXWriter();



			$company_name = array($PlantDetail->FIRMNAME);

			$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 9);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_name);



			$address = $PlantDetail->ADDRESS1 . ' ' . $PlantDetail->ADDRESS2;

			$company_addr = array($address,);

			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 9);  //merge cells

			$writer->writeSheetRow('Sheet1', $company_addr);



			$msg = "Purchase Entries Report " . $this->input->post('from_date') . " To " . $this->input->post('to_date') . " - Status : " . $status;

			$filter = array($msg);

			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 9);  //merge cells

			$writer->writeSheetRow('Sheet1', $filter);



			// empty row

			$list_add = [];

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$list_add[] = "";

			$writer->writeSheetRow('Sheet1', $list_add);





			$set_col_tk = [];

			$set_col_tk["Purch Entry No."] = 'Purch Entry No.';

			$set_col_tk["Date"] = 'Date';

			$set_col_tk["Purchased From"] = 'Purchased From';

			$set_col_tk["InvoceNo"] = 'InvoceNo';

			$set_col_tk["Inv. Date"] = 'Inv. Date';

			$set_col_tk["Purchase Amt"] = 'Purchase Amt';

			$set_col_tk["Disc"] = 'Disc';

			$set_col_tk["Other Charges"] = 'Other Charges';

			$set_col_tk["CGST Amt"] = 'CGST Amt';

			$set_col_tk["SGST Amt"] = 'SGST Amt';

			$set_col_tk["IGST Amt"] = 'IGST Amt';

			$set_col_tk["Tds Amt	"] = 'Tds Amt';

			$set_col_tk["Inv. Amt"] = 'Inv. Amt';

			$set_col_tk["Status"] = 'Status';

			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);



			$i = 0;

			$total = 0;

			$rowspan = 0;

			$grand_total = 0;

			foreach ($data as $k => $value) {



				$list_add = [];

				$list_add[] = $value["PurchID"];

				$date = _d(substr($value["Transdate"], 0, 10));

				$invdate = _d(substr($value["Invoicedate"], 0, 10));

				$list_add[] = $date;

				$list_add[] = $value["AccountName"];

				$list_add[] = $value["Invoiceno"];

				$list_add[] = $invdate;

				$list_add[] = $value["Purchamt"];

				$list_add[] = $value["Discamt"];

				$list_add[] = $value["OtherCharges"];

				$list_add[] = $value["cgstamt"];

				$list_add[] = $value["sgstamt"];

				$list_add[] = $value["igstamt"];

				$list_add[] = $value["TdsAmt"];

				$list_add[] = $value["Invamt"];

				$list_add[] = $value["cur_status"];



				$writer->writeSheetRow('Sheet1', $list_add);
			}





			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'PurchaseEntriesReport.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}



	public function Item_QC($id = '')
	{

		if (!has_permission_new('Item_QC', '', 'view')) {

			access_denied('Permission Denied');
		}

		if ($this->input->post()) {

			if (!has_permission_new('Item_QC', '', 'add') && !has_permission_new('Item_QC', '', 'edit')) {

				access_denied('Permission Denied');
			}

			$data = $this->input->post();



			// print_r($data);die;



			$success = $this->purchase_model->update_QC_Data($data, $id);

			// var_dump($success);

			// die;

			if ($success == true) {

				set_alert('success', _l('added_successfully'));
			} else {

				set_alert('warning', _l('Something Went Wrong'));
			}

			redirect(admin_url('purchase/Item_QC'));
		}

		if ($id == '') {

			$title = _l('Item QC');

			$data['POData'] = $this->purchase_model->get_Total_Inspection_Done_PO();
		} else {

			$title = _l('Update Item QC');



			$data['order_details'] = $this->purchase_model->GetQCData_byQCno($id);



			$data['Itemdata'] = $this->purchase_model->get_Total_Inspection_Done_By_PO_Item($data['order_details']->PONumber, $data['order_details']->ItemID);



			$data['order_entry_detail'] = json_encode($this->purchase_model->GetQCParameterByItem_QCNo($id, $data['order_details']->ItemID));
		}



		$data['vendors'] = $this->purchase_model->GetRMVendor();

		$data['title'] = $title;

		// echo "<pre>";

		// print_r($data['Itemdata']);

		// die;

		$this->load->view('QC/Item_QC', $data);
	}





	public function GetPODataIsnpectionDone()
	{

		// POST data

		$PO_number = $this->input->post('PO_number');

		$Data = $this->purchase_model->get_unique_history_QC($PO_number);



		$option = "<option>Non selected</option>";

		foreach ($Data as $each) {

			$option .= "<option value='" . $each['item_code'] . "'>" . $each['description'] . "(" . $each['item_code'] . ")</option>";
		}



		// Get data

		$POData['Itemlist'] = $option;

		$POData['purchmastertbl'] = $this->purchase_model->get_Total_Inspection_Done_By_PO($PO_number);

		echo json_encode($POData);
	}





	public function GetQCParameterByItem()
	{

		// POST data

		$itemid = $this->input->post('itemid');

		$PO_number = $this->input->post('PO_number');



		// Get data

		$POData['mastertbl'] = $this->purchase_model->GetQCParameterByItem($itemid);

		$POData['qcItemtbl'] = $this->purchase_model->GetQC_CompleteByItem_PO($PO_number, $itemid);

		echo json_encode($POData);
	}



	public function load_data_for_qc_entry()
	{



		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

		);

		$data = $this->purchase_model->load_data_for_qc_entry($data);

		if (count($data) > 0) {

			$minutes = 0;

			$i = 1;

			foreach ($data as $value) {



				$url = '"' . admin_url('purchase/Item_QC/' . $value['QC_no']) . '"';

				$html1 .= '<tr onclick=location.href=' . $url . '>';

				$html1 .= '<td onclick=location.href=' . $url . ' align="center"><a href=' . $url . '>' . $value['QC_no'] . '</a></td>';

				$html1 .= '<td align="center">' . _d(substr($value["TransDate"], 0, 10)) . '</td>';

				$html1 .= '<td align="center">' . $value['PONumber'] . '</td>';

				$html1 .= '<td align="center">' . $value['description'] . ' (' . $value['ItemID'] . ')</td>';

				$html1 .= '<td align="center">' . $value['UserID'] . '</td>';

				$html1 .= '</tr>';

				$i++;
			}
		} else {

			$html1 .= '<span style="color:red;">No Data found...</span>';
		}

		echo json_encode($html1);
	}



	public function FG_Test_Report($id = '')
	{

		if (!has_permission_new('Fg_Test', '', 'view')) {

			access_denied('Permission Denied');
		}



		if ($this->input->post()) {

			if (!has_permission_new('Fg_Test', '', 'add') && !has_permission_new('Fg_Test', '', 'edit')) {

				access_denied('Permission Denied');
			}

			$data = $this->input->post();



			// print_r($data);die;



			$success = $this->purchase_model->update_FG_Test_Data($data, $id);

			// var_dump($success);

			// die;

			if ($success == true) {

				set_alert('success', _l('added_successfully'));
			} else {

				set_alert('warning', _l('Something Went Wrong'));
			}

			redirect(admin_url('purchase/FG_Test_Report'));
		}



		if ($id == '') {
		} else {



			$data['order_details'] = $this->purchase_model->GetFgTestData_byentry($id);



			$data['order_entry_detail'] = json_encode($this->purchase_model->GetFgTestDetail_byentry($id));
		}

		$data['item_code'] = $this->purchase_model->get_items_code_qc();

		$title = _l('Finish Good Test Report');

		$data['title'] = $title;

		// echo "<pre>";

		// print_r($data['Itemdata']);

		// die;

		$this->load->view('QC/FG_Test_Report', $data);
	}



	public function In_Process_QC($id = '')
	{

		if (!has_permission_new('In_Process_QC', '', 'view')) {

			access_denied('Permission Denied');
		}



		if ($this->input->post()) {

			if (!has_permission_new('In_Process_QC', '', 'add') && !has_permission_new('In_Process_QC', '', 'edit')) {

				access_denied('Permission Denied');
			}

			$data = $this->input->post();



			// print_r($data);die;



			$success = $this->purchase_model->update_In_process_plant_Data($data, $id);

			// var_dump($success);

			// die;

			if ($success == true) {

				set_alert('success', _l('added_successfully'));
			} else {

				set_alert('warning', _l('Something Went Wrong'));
			}

			redirect(admin_url('purchase/In_Process_QC'));
		}



		if ($id == '') {
		} else {



			$data['order_details'] = $this->purchase_model->GetProcessPlantData_byentry($id);



			$data['order_entry_detail'] = json_encode($this->purchase_model->GetProcessPlantDetail_byentry($id));
		}

		$data['item_code'] = $this->purchase_model->get_items_code_qc();

		$title = _l('In-Process QC');

		$data['title'] = $title;

		// echo "<pre>";

		// print_r($data['order_details']);

		// die;

		$this->load->view('QC/In_Process_QC', $data);
	}



	public function Metal_Detector_Report($id = '')
	{

		if (!has_permission_new('Metal_Detector', '', 'view')) {

			access_denied('Permission Denied');
		}



		if ($this->input->post()) {

			if (!has_permission_new('Metal_Detector', '', 'add') && !has_permission_new('Metal_Detector', '', 'edit')) {

				access_denied('Permission Denied');
			}

			$data = $this->input->post();



			// print_r($data);die;



			$success = $this->purchase_model->update_Metal_Detector_Test_Data($data, $id);

			// var_dump($success);

			// die;

			if ($success == true) {

				set_alert('success', _l('added_successfully'));
			} else {

				set_alert('warning', _l('Something Went Wrong'));
			}

			redirect(admin_url('purchase/Metal_Detector_Report'));
		}



		if ($id == '') {
		} else {



			$data['order_details'] = $this->purchase_model->GetMetalDetectorData_byentry($id);



			$data['order_entry_detail'] = json_encode($this->purchase_model->GetMetalDetectorDetail_byentry($id));
		}

		$data['item_code'] = $this->purchase_model->get_items_code_qc();

		$title = _l('Metal Detector Report');

		$data['title'] = $title;

		// echo "<pre>";

		// print_r($data['Itemdata']);

		// die;

		$this->load->view('QC/Metal_Detector_Report', $data);
	}



	public function load_data_for_fg_test_entry()
	{



		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

		);

		$data = $this->purchase_model->load_data_for_fg_test_entry($data);

		if (count($data) > 0) {

			$minutes = 0;

			$i = 1;

			foreach ($data as $value) {



				$url = '"' . admin_url('purchase/FG_Test_Report/' . $value['entry_no']) . '"';

				$html1 .= '<tr onclick=location.href=' . $url . '>';

				$html1 .= '<td onclick=location.href=' . $url . ' align="center"><a href=' . $url . '>' . $value['entry_no'] . '</a></td>';

				$html1 .= '<td align="center">' . _d(substr($value["TransDate"], 0, 10)) . '</td>';

				$html1 .= '<td align="center">' . $value['UserID'] . '</td>';

				$html1 .= '</tr>';

				$i++;
			}
		} else {

			$html1 .= '<span style="color:red;">No Data found...</span>';
		}

		echo json_encode($html1);
	}

	public function load_data_for_metal_detector_entry()
	{



		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

		);

		$data = $this->purchase_model->load_data_for_metal_detector_entry($data);

		if (count($data) > 0) {

			$minutes = 0;

			$i = 1;

			foreach ($data as $value) {



				$url = '"' . admin_url('purchase/Metal_Detector_Report/' . $value['entry_no']) . '"';

				$html1 .= '<tr onclick=location.href=' . $url . '>';

				$html1 .= '<td onclick=location.href=' . $url . ' align="center"><a href=' . $url . '>' . $value['entry_no'] . '</a></td>';

				$html1 .= '<td align="center">' . _d(substr($value["TransDate"], 0, 10)) . '</td>';

				$html1 .= '<td align="center">' . $value['UserID'] . '</td>';

				$html1 .= '</tr>';

				$i++;
			}
		} else {

			$html1 .= '<span style="color:red;">No Data found...</span>';
		}

		echo json_encode($html1);
	}



	public function load_data_for_process_plant_entry()
	{



		$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

		);

		$data = $this->purchase_model->load_data_for_process_plant_entry($data);

		if (count($data) > 0) {

			$minutes = 0;

			$i = 1;

			foreach ($data as $value) {



				$url = '"' . admin_url('purchase/In_Process_QC/' . $value['entry_no']) . '"';

				$html1 .= '<tr onclick=location.href=' . $url . '>';

				$html1 .= '<td onclick=location.href=' . $url . ' align="center"><a href=' . $url . '>' . $value['entry_no'] . '</a></td>';

				$html1 .= '<td align="center">' . _d(substr($value["TransDate"], 0, 10)) . '</td>';

				$html1 .= '<td align="center">' . $value['UserID'] . '</td>';

				$html1 .= '</tr>';

				$i++;
			}
		} else {

			$html1 .= '<span style="color:red;">No Data found...</span>';
		}

		echo json_encode($html1);
	}



	/* PartyItem Wise report page */

	public function BillsPayableReport()
	{

		if (!has_permission_new('TradePayableReport', '', 'view')) {

			access_denied('purchase');
		}



		close_setup_menu();

		$data['title'] = "Trade Payable Report";

		$data['company_detail'] = $this->purchase_model->get_company_detail();

		$data['bodyclass'] = 'invoices-total-manual';

		$this->load->view('purchase_register/BillsPayableReport', $data);
	}



	// Get Result for PartyItemWise report

	public function GetBillsPayableReport()
	{



		$selected_company = $this->session->userdata('root_company');

		$filterdata = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'vendor_type' => $this->input->post('vendor_type'),

			'DueOn' => $this->input->post('DueOn'),

		);

		$ReportType = $this->input->post('ReportType');

		$body_data = $this->purchase_model->GetBillsPayableBodyData($filterdata);

		// echo json_encode($body_data);

		// die;

		$html = '';

		$html .= '<table class="table-striped table-bordered SaleVsSaleRtn_report" id="SaleVsSaleRtn_report" width="100%">';

		$html .= '<thead style="font-size:11px;">';

		$html .= '<tr>';

		$html .= '<th align="center">Sr.No</th>';

		$html .= '<th align="center">Date</th>';

		$html .= '<th align="center">Vendor</th>';

		$html .= '<th align="center">Inv No.</th>';

		$html .= '<th align="center">Inv Amt</th>';

		$html .= '<th align="center">Paid Amt</th>';

		$html .= '<th align="center">PurchRtn Amt</th>';

		$html .= '<th align="center">Debit Note Amt</th>';

		$html .= '<th align="center">Journal Amt</th>';

		$html .= '<th align="center">Due Amt</th>';

		$html .= '<th align="center">Due On</th>';

		$html .= '<th align="center">Over Due By Days</th>';

		$html .= '<th align="center">Disc</th>';

		$html .= '<th align="center">Actual Amt</th>';

		$html .= '</tr>';

		$html .= '</thead>';

		$html .= '<tbody>';

		$i = 0;

		$z = 1;



		$chkid = '';

		$total = 0;

		$totaldue = 0;

		$totalpaid = 0;

		$totalpurchrtn = 0;

		$totaldebitnote = 0;

		$totaljournal = 0;

		$chk = 0;

		$TotalRecord = count($body_data);

		foreach ($body_data as $key => $value) {



			$dueAmt = $value["Invamt"] - $value["PaidAmt"] - $value["PurchRtnAmt"] - $value["DebitNoteAmt"] - $value["JournalAmt"];

			$transdate = $value["Transdate"];



			// Assuming $value["payment_term"] is the number of days

			$paymentTerm = $value["MaxDays"];



			// Calculate the next date based on current date and payment term

			$nextDateTimestamp = strtotime($transdate . " + $paymentTerm days");



			// Format the timestamp to the desired date format

			$nextDate = date('d-M-y', $nextDateTimestamp);



			// Get the current timestamp

			$currentTimestamp = time();



			// Calculate the difference in seconds between current date and next date

			$differenceInSeconds = $currentTimestamp - $nextDateTimestamp;



			// Convert the difference to days

			$overdueDays = ceil($differenceInSeconds / (60 * 60 * 24)); // ceil to round up to the nearest whole day





			$BillTimestamp = strtotime($transdate);

			$differenceInSecondsbybill = $currentTimestamp - $BillTimestamp;

			$TotalDays = ceil($differenceInSecondsbybill / (60 * 60 * 24));

			// echo $TotalDays;die;



			$DisPercentage = 0;

			foreach ($value["DisDays"] as $Days) {

				if ($TotalDays <= $Days['Days']) {

					$DisPercentage = $Days['Percentage'];
				}
			}



			$DiscAmt = '';

			if ($DisPercentage > 0) {

				$DiscAmt = ($value["Invamt"] * $DisPercentage) / 100;
			}

			if ($chkid != $value["AccountID"]) {

				if ($chk > 0) {

					$chk = 0;

					$html .= '<tr>';

					$html .= '<td colspan="4" align="right" style="font-weight: 700;font-size: 14px;text-align:right;">Total</td>';

					$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($total, 2, ".", "") . '</td>';

					$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totalpaid, 2, ".", "") . '</td>';

					$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totalpurchrtn, 2, ".", "") . '</td>';

					$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaldebitnote, 2, ".", "") . '</td>';

					$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaljournal, 2, ".", "") . '</td>';

					$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaldue, 2, ".", "") . '</td>';

					$html .= '<td align="right" ></td>';



					$html .= '<td align="right"></td>';

					$html .= '<td align="center"></td>';

					$html .= '<td align="center"></td>';

					$html .= '</tr>';
				}

				$PartyName = '';

				$total = 0;

				$totaldue = 0;

				$totalpaid = 0;

				$totalpurchrtn = 0;

				$totaldebitnote = 0;

				$totaljournal = 0;
			}

			if ($ReportType == "Overdue" && $overdueDays > 0 && $dueAmt > 0) {

				$i++;

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$totalpurchrtn += $value["PurchRtnAmt"];

				$totaldebitnote += $value["DebitNoteAmt"];

				$totaljournal += $value["JournalAmt"];

				$total += $value["Invamt"];

				$PartyName = $value["company"];

				$html .= '<tr>';

				$html .= '<td align="center">' . $i . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="center">' . $value["company"] . '</td>';

				$html .= '<td align="center">' . $value["Invoiceno"] . '</td>';

				$html .= '<td align="right">' . $value["Invamt"] . '</td>';

				$html .= '<td align="right">' . $value["PaidAmt"] . '</td>';

				$html .= '<td align="right">' . $value["PurchRtnAmt"] . '</td>';

				$html .= '<td align="right">' . $value["DebitNoteAmt"] . '</td>';

				$html .= '<td align="right">' . $value["JournalAmt"] . '</td>';

				$html .= '<td align="right">' . number_format($dueAmt, 2, ".", "") . '</td>';

				$html .= '<td align="center">' . $nextDate . '</td>';

				if ($dueAmt > 0 && $overdueDays > 0) {

					$html .= '<td align="center">' . $overdueDays . ' Days</td>';
				} else {

					$html .= '<td align="center"></td>';
				}

				$html .= '<td align="right">' . $DiscAmt . '</td>';





				$html .= '<td align="right">' . ($value["Invamt"] - $DiscAmt) . '</td>';

				$html .= '</tr>';
			}

			if ($ReportType == "NonOverdue" && $overdueDays <= 0) {

				$i++;

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$totalpurchrtn += $value["PurchRtnAmt"];

				$totaldebitnote += $value["DebitNoteAmt"];

				$totaljournal += $value["JournalAmt"];

				$total += $value["Invamt"];

				$PartyName = $value["company"];

				$html .= '<tr>';

				$html .= '<td align="center">' . $i . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="center">' . $value["company"] . '</td>';

				$html .= '<td align="center">' . $value["Invoiceno"] . '</td>';

				$html .= '<td align="right">' . $value["Invamt"] . '</td>';

				$html .= '<td align="right">' . $value["PaidAmt"] . '</td>';

				$html .= '<td align="right">' . $value["PurchRtnAmt"] . '</td>';

				$html .= '<td align="right">' . $value["DebitNoteAmt"] . '</td>';

				$html .= '<td align="right">' . $value["JournalAmt"] . '</td>';

				$html .= '<td align="right">' . number_format($dueAmt, 2, ".", "") . '</td>';

				$html .= '<td align="center">' . $nextDate . '</td>';

				if ($dueAmt > 0 && $overdueDays > 0) {

					$html .= '<td align="center">' . $overdueDays . ' Days</td>';
				} else {

					$html .= '<td align="center"></td>';
				}

				$html .= '<td align="right">' . $DiscAmt . '</td>';

				$html .= '<td align="right">' . ($value["Invamt"] - $DiscAmt) . '</td>';

				$html .= '</tr>';
			}

			if (empty($ReportType)) {

				$i++;

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$totalpurchrtn += $value["PurchRtnAmt"];

				$totaldebitnote += $value["DebitNoteAmt"];

				$totaljournal += $value["JournalAmt"];

				$total += $value["Invamt"];

				$PartyName = $value["company"];

				$html .= '<tr>';

				$html .= '<td align="center">' . $i . '</td>';

				$html .= '<td align="center">' . _d(substr($value["Transdate"], 0, 10)) . '</td>';

				$html .= '<td align="center">' . $value["company"] . '</td>';

				$html .= '<td align="center">' . $value["Invoiceno"] . '</td>';

				$html .= '<td align="right">' . $value["Invamt"] . '</td>';

				$html .= '<td align="right">' . $value["PaidAmt"] . '</td>';

				$html .= '<td align="right">' . $value["PurchRtnAmt"] . '</td>';

				$html .= '<td align="right">' . $value["DebitNoteAmt"] . '</td>';

				$html .= '<td align="right">' . $value["JournalAmt"] . '</td>';

				$html .= '<td align="right">' . number_format($dueAmt, 2, ".", "") . '</td>';

				$html .= '<td align="center">' . $nextDate . '</td>';

				if ($dueAmt > 0 && $overdueDays > 0) {

					$html .= '<td align="center">' . $overdueDays . ' Days</td>';
				} else {

					$html .= '<td align="center"></td>';
				}

				$html .= '<td align="right">' . $DiscAmt . '</td>';

				$html .= '<td align="right">' . ($value["Invamt"] - $DiscAmt) . '</td>';

				$html .= '</tr>';



				// $this->db->select(db_prefix() . 'staff.*');

				// $this->db->where(db_prefix() . 'staff.PlantID', $selected_company);

				// $this->db->where(db_prefix() . 'staff.admin', '1');

				// $AdminStaff = $this->db->get(db_prefix() . 'staff')->result_array();

				// if($overdueDays == '1'){

				// foreach($AdminStaff as $admin){



				// $Notification_msg = "Payment Over Due ";

				// $notification_data = [

				// 'description' => $Notification_msg,

				// 'touserid'    => $admin['staffid'],

				// 'link'        => '',

				// ]; 

				// $notification_data['additional_data'] = serialize([

				// 'VoucherID. '.$new_voucher_number,

				// ]);



				// if (add_notification($notification_data)) {

				// pusher_trigger_notification($admin['staffid']);

				// }

				// }

				// }

			}

			// for last party total row

			if ($TotalRecord == $z && $chk > 0) {

				$html .= '<tr>';

				$html .= '<td colspan="4" align="right" style="font-weight: 700;font-size: 14px;text-align:right;">Total</td>';

				$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($total, 2, ".", "") . '</td>';

				$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totalpaid, 2, ".", "") . '</td>';

				$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totalpurchrtn, 2, ".", "") . '</td>';

				$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaldebitnote, 2, ".", "") . '</td>';

				$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaljournal, 2, ".", "") . '</td>';

				$html .= '<td align="right" style="font-weight: 700;font-size: 14px;text-align:right;">' . number_format($totaldue, 2, ".", "") . '</td>';

				$html .= '<td align="right" ></td>';

				$html .= '<td align="right"></td>';

				$html .= '<td align="center"></td>';

				$html .= '<td align="center"></td>';

				$html .= '</tr>';
			}

			$z++;
		}

		// Footer Data

		$html .= '</tbody>';

		$html .= '</table>';

		echo json_encode($html);

		die;
	}

	public function GetBillsPayableReportChart()
	{



		$selected_company = $this->session->userdata('root_company');

		$filterdata = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'vendor_type' => $this->input->post('vendor_type'),

			'DueOn' => $this->input->post('DueOn'),

		);

		$ReportType = $this->input->post('ReportType');

		$body_data = $this->purchase_model->GetBillsPayableBodyData($filterdata);

		// echo json_encode($body_data);

		// die;

		$ReturnArr = [];

		$i = 1;



		$chkid = '';

		$total = 0;

		$totaldue = 0;

		$totalpaid = 0;

		$chk = 0;

		$TotalRecord = count($body_data);

		foreach ($body_data as $key => $value) {



			$dueAmt = $value["Invamt"] - $value["PaidAmt"] - $value["PurchRtnAmt"] - $value["DebitNoteAmt"] - $value["JournalAmt"];

			$transdate = $value["Transdate"];



			// Assuming $value["payment_term"] is the number of days

			$paymentTerm = $value["MaxDays"];



			// Calculate the next date based on current date and payment term

			$nextDateTimestamp = strtotime($transdate . " + $paymentTerm days");



			// Format the timestamp to the desired date format

			$nextDate = date('d-M-y', $nextDateTimestamp);



			// Get the current timestamp

			$currentTimestamp = time();



			// Calculate the difference in seconds between current date and next date

			$differenceInSeconds = $currentTimestamp - $nextDateTimestamp;



			// Convert the difference to days

			$overdueDays = ceil($differenceInSeconds / (60 * 60 * 24)); // ceil to round up to the nearest whole day





			$BillTimestamp = strtotime($transdate);

			$differenceInSecondsbybill = $currentTimestamp - $BillTimestamp;

			$TotalDays = ceil($differenceInSecondsbybill / (60 * 60 * 24));

			// echo $TotalDays;die;



			$DisPercentage = 0;

			foreach ($value["DisDays"] as $Days) {

				if ($TotalDays <= $Days['Days']) {

					$DisPercentage = $Days['Percentage'];
				}
			}



			$DiscAmt = '';

			if ($DisPercentage > 0) {

				$DiscAmt = ($value["Invamt"] * $DisPercentage) / 100;
			}

			if ($chkid != $value["AccountID"]) {

				if ($chk > 0) {

					$chk = 0;

					$ReturnArr[] = [

						'name' => $PartyName,

						'y' => round($total, 2),  // Optional: round to 2 decimal places

					];
				}

				$PartyName = '';

				$total = 0;

				$totaldue = 0;

				$totalpaid = 0;
			}

			if ($ReportType == "Overdue" && $overdueDays > 0 && $dueAmt > 0) {

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$total += $value["Invamt"];

				$PartyName = $value["company"];
			}

			if ($ReportType == "NonOverdue" && $overdueDays <= 0) {

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$total += $value["Invamt"];

				$PartyName = $value["company"];
			}

			if (empty($ReportType)) {

				$chkid = $value["AccountID"];

				$chk = 1;

				$totaldue += $dueAmt;

				$totalpaid += $value["PaidAmt"];

				$total += $value["Invamt"];

				$PartyName = $value["company"];
			}

			// for last party total row

			if ($TotalRecord == $i && $chk > 0) {

				$ReturnArr[] = [

					'name' => $PartyName,

					'y' => round($total, 2),  // Optional: round to 2 decimal places

				];
			}

			$i++;
		}

		echo json_encode($ReturnArr);

		die;
	}

	public function GetBillsPayableReportDaywiseChart()
	{



		$selected_company = $this->session->userdata('root_company');

		$filterdata = array(

			'from_date' => $this->input->post('from_date'),

			'to_date' => $this->input->post('to_date'),

			'vendor_type' => $this->input->post('vendor_type'),

			'DueOn' => $this->input->post('DueOn'),

		);

		$ReportType = $this->input->post('ReportType');

		$body_data = $this->purchase_model->GetBillsPayableBodyData($filterdata);

		// echo json_encode($body_data);

		// die;

		$ReturnArr = [

			'0-15' => 0,

			'15-30' => 0,

			'30-60' => 0,

			'60+' => 0,

		];

		$currentTimestamp = time();



		foreach ($body_data as $value) {



			$dueAmt = $value["Invamt"] - $value["PaidAmt"] - $value["PurchRtnAmt"] - $value["DebitNoteAmt"] - $value["JournalAmt"];

			if ($dueAmt <= 0) {

				continue; // Skip fully paid bills

			}

			$transdate = $value["Transdate"];



			// Assuming $value["payment_term"] is the number of days

			$paymentTerm = $value["MaxDays"];



			// Calculate the next date based on current date and payment term

			$nextDateTimestamp = strtotime($transdate . " + $paymentTerm days");



			// Format the timestamp to the desired date format

			$nextDate = date('d-M-y', $nextDateTimestamp);



			// Calculate the difference in seconds between current date and next date

			$differenceInSeconds = $currentTimestamp - $nextDateTimestamp;



			// Convert the difference to days

			$overdueDays = ceil($differenceInSeconds / (60 * 60 * 24)); // ceil to round up to the nearest whole day

			// echo $dueAmt;die;

			if ($overdueDays > 0 && $overdueDays <= 15) {

				$ReturnArr['0-15'] += $dueAmt;
			} elseif ($overdueDays > 0 && $overdueDays <= 30) {

				$ReturnArr['15-30'] += $dueAmt;
			} elseif ($overdueDays > 0 && $overdueDays <= 60) {

				$ReturnArr['30-60'] += $dueAmt;
			} elseif ($overdueDays > 60) {

				$ReturnArr['60+'] += $dueAmt;
			}
		}



		// Format for Highcharts

		$FinalArr = [];

		foreach ($ReturnArr as $range => $amt) {

			$FinalArr[] = [

				'name' => $range,

				'y' => round($amt, 2)

			];
		}

		echo json_encode($FinalArr);

		die;
	}



	public function ExportBillsPayableReport()
	{

		if (!class_exists('XLSXReader_fin')) {

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php');
		}

		require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');



		if ($this->input->post()) {



			$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date' => $this->input->post('to_date'),

				'DueOn' => $this->input->post('DueOn'),

			);

			$ReportType = $this->input->post('ReportType');

			$body_data = $this->purchase_model->GetBillsPayableBodyData($filterdata);

			$company_detail = $this->purchase_model->get_company_detail();

			/*echo json_encode($body_data);

			die;*/

			$AccountDetails = 'Trade Payable Report';

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

			$set_col_tk["Customer"] = 'Vendor';

			$set_col_tk["Invoice No."] = 'Invoice No.';

			$set_col_tk["Invoice Amount"] = 'Invoice Amount';

			$set_col_tk["Paid Amount"] = 'Paid Amount';

			$set_col_tk["PurchRtn Amt"] = 'PurchRtn Amt';

			$set_col_tk["Debit Note Amt"] = 'Debit Note Amt';

			$set_col_tk["Journal Amt"] = 'Journal Amt';

			$set_col_tk["Due Amt"] = 'Due Amt';

			$set_col_tk["Due On"] = 'Due On';

			$set_col_tk["Over Due By Days"] = 'Over Due By Days';

			$set_col_tk["Disc"] = 'Disc';

			$set_col_tk["Actual Amt"] = 'Actual Amt';



			$writer_header = $set_col_tk;

			$writer->writeSheetRow('Sheet1', $writer_header);



			$chkid = '';

			$total = 0;

			$totaldue = 0;

			$totalpaid = 0;

			$totalpurchrtn = 0;

			$totaldebitnote = 0;

			$chk = 0;

			$TotalRecord = count($body_data);

			foreach ($body_data as $key => $value) {



				$dueAmt = $value["Invamt"] - $value["PaidAmt"] - $value["PurchRtnAmt"] - $value["DebitNoteAmt"] - $value["JournalAmt"];

				$transdate = $value["Transdate"];



				// Assuming $value["payment_term"] is the number of days

				$paymentTerm = $value["MaxDays"];



				// Calculate the next date based on current date and payment term

				$nextDateTimestamp = strtotime($transdate . " + $paymentTerm days");



				// Format the timestamp to the desired date format

				$nextDate = date('d-M-y', $nextDateTimestamp);



				// Get the current timestamp

				$currentTimestamp = time();



				// Calculate the difference in seconds between current date and next date

				$differenceInSeconds = $currentTimestamp - $nextDateTimestamp;



				// Convert the difference to days

				$overdueDays = ceil($differenceInSeconds / (60 * 60 * 24)); // ceil to round up to the nearest whole day







				$BillTimestamp = strtotime($transdate);

				$differenceInSecondsbybill = $currentTimestamp - $BillTimestamp;

				$TotalDays = ceil($differenceInSecondsbybill / (60 * 60 * 24));

				// echo $TotalDays;die;



				$DisPercentage = 0;

				foreach ($value["DisDays"] as $Days) {

					if ($TotalDays <= $Days['Days']) {

						$DisPercentage = $Days['Percentage'];
					}
				}



				$DiscAmt = '';

				if ($DisPercentage > 0) {

					$DiscAmt = ($value["Invamt"] * $DisPercentage) / 100;
				}



				if ($chkid != $value["AccountID"]) {

					if ($chk > 0) {



						$chk = 0;



						$list_add = [];

						$list_add[] = '';

						$list_add[] = '';

						$list_add[] = 'Total';

						$list_add[] = number_format($total, 2, ".", "");

						$list_add[] = number_format($totalpaid, 2, ".", "");

						$list_add[] = number_format($totalpurchrtn, 2, ".", "");

						$list_add[] = number_format($totaldebitnote, 2, ".", "");

						$list_add[] = number_format($totaldue, 2, ".", "");

						$list_add[] = '';

						$list_add[] = '';

						$list_add[] = '';

						$list_add[] = '';

						$writer->writeSheetRow('Sheet1', $list_add);
					}

					$total = 0;

					$totaldue = 0;

					$totalpaid = 0;

					$totalpurchrtn = 0;

					$totaldebitnote = 0;

					$totaljournal = 0;
				}

				if ($ReportType == "Overdue" && $overdueDays > 0 && $dueAmt > 0) {

					$chkid = $value["AccountID"];

					$chk = 1;

					$totaldue += $dueAmt;

					$totalpaid += $value["PaidAmt"];

					$totalpurchrtn += $value["PurchRtnAmt"];

					$totaldebitnote += $value["PaidAmt"];

					$totaljournal += $value["JournalAmt"];

					$total += $value["Invamt"];





					$list_add = [];

					$list_add[] = _d(substr($value["Transdate"], 0, 10));

					$list_add[] = $value["company"];

					$list_add[] = $value["Invoiceno"];

					$list_add[] = number_format($value["Invamt"], 2, ".", "");

					$list_add[] = number_format($value["PaidAmt"], 2, ".", "");

					$list_add[] = number_format($value["PurchRtnAmt"], 2, ".", "");

					$list_add[] = number_format($value["DebitNoteAmt"], 2, ".", "");

					$list_add[] = number_format($value["JournalAmt"], 2, ".", "");



					$list_add[] = number_format($dueAmt, 2, ".", "");

					$list_add[] = $nextDate;

					if ($dueAmt > 0 && $overdueDays > 0) {

						$list_add[] = $overdueDays;
					} else {

						$list_add[] = '';
					}

					$list_add[] = $DiscAmt;

					$list_add[] = ($value["Invamt"] - $DiscAmt);

					$writer->writeSheetRow('Sheet1', $list_add);
				}

				if ($ReportType == "NonOverdue" && $overdueDays <= 0) {

					$chkid = $value["AccountID"];

					$chk = 1;

					$totaldue += $dueAmt;

					$totalpaid += $value["PaidAmt"];

					$totalpurchrtn += $value["PurchRtnAmt"];

					$totaldebitnote += $value["PaidAmt"];

					$totaljournal += $value["JournalAmt"];

					$total += $value["Invamt"];



					$list_add = [];

					$list_add[] = _d(substr($value["Transdate"], 0, 10));

					$list_add[] = $value["company"];

					$list_add[] = $value["Invoiceno"];

					$list_add[] = number_format($value["Invamt"], 2, ".", "");

					$list_add[] = number_format($value["PaidAmt"], 2, ".", "");

					$list_add[] = number_format($value["PurchRtnAmt"], 2, ".", "");

					$list_add[] = number_format($value["DebitNoteAmt"], 2, ".", "");

					$list_add[] = number_format($value["JournalAmt"], 2, ".", "");



					$list_add[] = number_format($dueAmt, 2, ".", "");

					$list_add[] = $nextDate;

					if ($dueAmt > 0 && $overdueDays > 0) {

						$list_add[] = $overdueDays;
					} else {

						$list_add[] = '';
					}

					$list_add[] = $DiscAmt;

					$list_add[] = ($value["Invamt"] - $DiscAmt);



					$writer->writeSheetRow('Sheet1', $list_add);
				}

				if (empty($ReportType)) {

					$chkid = $value["AccountID"];

					$chk = 1;

					$totaldue += $dueAmt;

					$totalpaid += $value["PaidAmt"];

					$totalpurchrtn += $value["PurchRtnAmt"];

					$totaldebitnote += $value["PaidAmt"];

					$totaljournal += $value["JournalAmt"];

					$total += $value["Invamt"];



					$list_add = [];

					$list_add[] = _d(substr($value["Transdate"], 0, 10));

					$list_add[] = $value["company"];

					$list_add[] = $value["Invoiceno"];

					$list_add[] = number_format($value["Invamt"], 2, ".", "");

					$list_add[] = number_format($value["PaidAmt"], 2, ".", "");

					$list_add[] = number_format($value["PurchRtnAmt"], 2, ".", "");

					$list_add[] = number_format($value["DebitNoteAmt"], 2, ".", "");

					$list_add[] = number_format($value["JournalAmt"], 2, ".", "");



					$list_add[] = number_format($dueAmt, 2, ".", "");

					$list_add[] = $nextDate;

					if ($dueAmt > 0 && $overdueDays > 0) {

						$list_add[] = $overdueDays;
					} else {

						$list_add[] = '';
					}

					$list_add[] = $DiscAmt;

					$list_add[] = ($value["Invamt"] - $DiscAmt);

					$writer->writeSheetRow('Sheet1', $list_add);
				}

				// for last party total row

				if ($TotalRecord == $i && $chk > 0) {

					$list_add = [];

					$list_add[] = '';

					$list_add[] = '';

					$list_add[] = 'Total';

					$list_add[] = number_format($total, 2, ".", "");

					$list_add[] = number_format($totalpaid, 2, ".", "");

					$list_add[] = number_format($totalpurchrtn, 2, ".", "");

					$list_add[] = number_format($totaldebitnote, 2, ".", "");

					$list_add[] = number_format($totaljournal, 2, ".", "");

					$list_add[] = number_format($totaldue, 2, ".", "");

					$list_add[] = '';

					$list_add[] = '';

					$list_add[] = '';

					$list_add[] = '';

					$writer->writeSheetRow('Sheet1', $list_add);
				}



				$i++;
			}





			// Footer Data





			$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');

			foreach ($files as $file) {

				if (is_file($file)) {

					unlink($file);
				}
			}

			$filename = 'TradePayableReport.xlsx';

			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE . $filename, $filename));

			echo json_encode([

				'site_url' => site_url(),

				'filename' => TIMESHEETS_PATH_EXPORT_FILE . $filename,

			]);

			die;
		}
	}



	// public function fetchBankDetailsFromIFSC()

	// {

	// 	$ifsc_code = $this->input->post('ifsc_code');

	// 	$curl = curl_init();

	// 	curl_setopt_array(

	// 	$curl,

	// 	array(

	// 	CURLOPT_URL => 'https://ifsc.razorpay.com/' . $ifsc_code . '',

	// 	CURLOPT_RETURNTRANSFER => true,

	// 	CURLOPT_ENCODING => '',

	// 	CURLOPT_MAXREDIRS => 10,

	// 	CURLOPT_TIMEOUT => 0,

	// 	CURLOPT_FOLLOWLOCATION => true,

	// 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

	// 	CURLOPT_CUSTOMREQUEST => 'GET',

	// 	)

	// 	);



	// 	$response = curl_exec($curl);



	// 	curl_close($curl);

	// 	echo $response;

	// }



	public function verifyBankAccount()
	{



		$bank_ac_no = $this->input->post('bank_ac_no');

		$ifsc_code = $this->input->post('ifsc_code');

		$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';



		$curl = curl_init();

		curl_setopt_array(

			$curl,

			array(

				CURLOPT_URL => 'https://kyc-api.surepass.io/api/v1/bank-verification/',

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_ENCODING => '',

				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 0,

				CURLOPT_FOLLOWLOCATION => true,

				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

				CURLOPT_CUSTOMREQUEST => 'POST',

				CURLOPT_POSTFIELDS => '{

			"id_number": "' . $bank_ac_no . '",

			"ifsc": "' . $ifsc_code . '",

			"ifsc_details": true

			}',

				CURLOPT_HTTPHEADER => array(

					'Content-Type: application/json',

					'Authorization: Bearer ' . $token . ''

				),

			)

		);



		$response = curl_exec($curl);



		curl_close($curl);

		echo $response;
	}





	public function gettdspercent()
	{

		$Tdsselection = $this->input->post('Tdsselection');



		$this->db->select(db_prefix() . 'TDSDetails.*');

		$this->db->where(db_prefix() . 'TDSDetails.TDSCode', $Tdsselection);

		$this->db->from(db_prefix() . 'TDSDetails');

		$data = $this->db->get()->result_array();

		echo json_encode($data);
	}

	public function gettdspercent_new($Tdsselection)
	{

		/**
		 * AJAX: Get next order number for a given category
		 */

		$this->db->select(db_prefix() . 'TDSDetails.*');

		$this->db->where(db_prefix() . 'TDSDetails.TDSCode', $Tdsselection);

		$this->db->from(db_prefix() . 'TDSDetails');

		$data = $this->db->get()->result_array();

		echo json_encode($data);
	}



	public function CompletePendingOrder()
	{



		$purchID = $this->input->post('PurchID');

		$data = $this->purchase_model->CompletePendingOrder($purchID);

		echo json_encode($data);
	}

	/**
	 * Fetch bank details from IFSC code using RBI IFSC API
	 */
	public function fetchBankDetailsFromIFSC()
	{
		$ifsc_code = $this->input->post('ifsc_code');

		if (empty($ifsc_code)) {
			echo json_encode("Not Found");
			return;
		}

		// Validate IFSC code format (11 characters)
		if (strlen($ifsc_code) != 11) {
			echo json_encode("Not Found");
			return;
		}

		// Try multiple API sources
		$bank_details = $this->getBankDetailsFromIFSC($ifsc_code);

		if ($bank_details) {
			echo json_encode($bank_details);
		} else {
			echo json_encode("Not Found");
		}
	}

	/**
	 * Get bank details from IFSC code
	 */
	private function getBankDetailsFromIFSC($ifsc_code)
	{
		// First try: RBI IFSC API (Official)
		$response = $this->callIFSCAPI("https://ifsc.razorpay.com/{$ifsc_code}", $ifsc_code);
		if ($response) {
			return $response;
		}

		// Second try: Alternative API
		$response = $this->callAlternativeAPI($ifsc_code);
		return $response;
	}

	/**
	 * Call IFSC API (Razorpay)
	 */
	private function callIFSCAPI($url, $ifsc_code)
	{
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($http_code == 200 && $response) {
				$data = json_decode($response, true);

				if (isset($data['BANK']) && isset($data['BRANCH'])) {
					return array(
						'BANK' => isset($data['BANK']) ? $data['BANK'] : '',
						'BRANCH' => isset($data['BRANCH']) ? $data['BRANCH'] : '',
						'ADDRESS' => isset($data['ADDRESS']) ? $data['ADDRESS'] : '',
						'IFSC' => $ifsc_code
					);
				}
			}
		} catch (Exception $e) {
			log_message('error', 'IFSC API Error: ' . $e->getMessage());
		}

		return false;
	}

	/**
	 * Call Alternative API for Bank Details
	 */
	private function callAlternativeAPI($ifsc_code)
	{
		try {
			// Using Indian Bank IFSC API
			$url = "https://bank-api.example.com/ifsc/{$ifsc_code}";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);

			if ($http_code == 200 && $response) {
				$data = json_decode($response, true);

				if ($data) {
					return array(
						'BANK' => isset($data['bank_name']) ? $data['bank_name'] : (isset($data['BANK']) ? $data['BANK'] : ''),
						'BRANCH' => isset($data['branch_name']) ? $data['branch_name'] : (isset($data['BRANCH']) ? $data['BRANCH'] : ''),
						'ADDRESS' => isset($data['address']) ? $data['address'] : (isset($data['ADDRESS']) ? $data['ADDRESS'] : ''),
						'IFSC' => $ifsc_code
					);
				}
			}
		} catch (Exception $e) {
			log_message('error', 'Alternative API Error: ' . $e->getMessage());
		}

		return false;
	}

	public function verify_pan()
	{
		if (!$this->input->is_ajax_request()) {
			return;
		}

		$pan = strtoupper($this->input->post('pan'));

		if (empty($pan) || strlen($pan) != 10) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid PAN format']);
			return;
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://kyc-api.aadhaarkyc.io/api/v1/pan/pan",
			//CURLOPT_URL => "https://sandbox.surepass.io/api/v1/pan/pan",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode([
				"id_number" => $pan
			]),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: Bearer " . self::KYC_API_BEARER_TOKEN
			),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($err) {
			echo json_encode(['status' => 'error', 'message' => 'API Error: ' . $err]);
			return;
		}

		$result = json_decode($response, true);

		// Log response for debugging
		log_message('info', 'PAN Verification Response: ' . $response);

		if ($http_code == 200 && isset($result['success']) && $result['success'] === true) {
			// Extract data from the response
			$pan_data = $result['data'] ?? $result;
			echo json_encode([
				'status' => 'success',
				'message' => 'PAN verified successfully',
				'data' => $pan_data
			]);
		} else {
			$error_msg = isset($result['message']) ? $result['message'] : 'Invalid PAN';
			echo json_encode([
				'status' => 'error',
				'message' => $error_msg,
				'response' => $result
			]);
		}
	}

	public function get_gstin_by_pan()
	{
		if (!$this->input->is_ajax_request()) {
			return;
		}

		$pan = strtoupper($this->input->post('pan'));

		if (empty($pan) || strlen($pan) != 10) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid PAN format']);
			return;
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://kyc-api.aadhaarkyc.io/api/v1/corporate/gstin-by-pan",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode([
				"id_number" => $pan
			]),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: Bearer " . self::KYC_API_BEARER_TOKEN
			),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($err) {
			echo json_encode(['status' => 'error', 'message' => 'API Error: ' . $err]);
			return;
		}

		$result = json_decode($response, true);

		// Log response for debugging
		log_message('info', 'GSTIN by PAN Response: ' . $response);

		// Check if API returned success
		if ($http_code == 200 && isset($result['success']) && $result['success'] === true) {
			$gstin_data = $result['data'] ?? $result;
			echo json_encode([
				'status' => 'success',
				'message' => 'GSTIN fetched successfully',
				'data' => $gstin_data
			]);
		} else {
			$error_msg = isset($result['message']) ? $result['message'] : 'No GSTIN found for this PAN';
			echo json_encode([
				'status' => 'error',
				'message' => $error_msg,
				'response' => $result
			]);
		}
	}

	/**
	 * Verify GSTIN using KYC API
	 * Calls the corporate-otp/gstin/init endpoint
	 */
	public function verify_gstin_kyc()
	{
		if (!$this->input->is_ajax_request()) {
			return;
		}

		$gstin = strtoupper($this->input->post('gstin'));
		$exclude_userid = $this->input->post('userid') ? intval($this->input->post('userid')) : 0;


		// Validate GSTIN format (15 characters)
		if (empty($gstin) || strlen($gstin) != 15) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid GSTIN format. GSTIN must be 15 characters.']);
			return;
		}

		// Check if GSTIN already exists in database
		$existing_client = $this->clients_model->check_gstin_exists($gstin, $exclude_userid);

		if ($existing_client) {
			echo json_encode([
				'status' => 'duplicate',
				'message' => 'GSTIN already exists in the system for: ' . $existing_client['company'],
				'existing_record' => $existing_client
			]);
			return;
		}
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://kyc-api.aadhaarkyc.io/api/v1/corporate-otp/gstin/init",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode([
				"id_number" => $gstin,
				"hsn_info_get" => true
			]),
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: Bearer " . self::KYC_API_BEARER_TOKEN
			),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($err) {
			echo json_encode(['status' => 'error', 'message' => 'API Error: ' . $err]);
			return;
		}

		$result = json_decode($response, true);

		// Log response for debugging
		log_message('info', 'GSTIN Verification Response: ' . $response);

		// Check if API returned success
		if ($http_code == 200 && isset($result['success']) && $result['success'] === true) {
			echo json_encode([
				'status' => 'success',
				'message' => 'GSTIN verified successfully',
				'data' => $result['data'] ?? $result
			]);
		} else {
			$error_msg = isset($result['message']) ? $result['message'] : 'GSTIN verification failed';
			echo json_encode([
				'status' => 'error',
				'message' => $error_msg,
				'response' => $result
			]);
		}
	}


	public function get_purchase_order_data()
	{
		if (!isset($this->db)) {
			$this->load->database();
		}

		$from_date = $this->input->get_post('from_date');
		$to_date   = $this->input->get_post('to_date');
		$category  = $this->input->get_post('category'); // ✅ Category filter

		$this->db->select(
			db_prefix() . 'PurchaseOrderMaster.*,' .
				db_prefix() . 'clients.company,' .
				db_prefix() . 'PlantLocationDetails.LocationName,' .
				'shipping_citys.city_name as ShippingCityName,' .
				db_prefix() . 'ItemCategoryMaster.CategoryName'
		);

		$this->db->from(db_prefix() . 'PurchaseOrderMaster');

		$this->db->join(
			db_prefix() . 'clients',
			db_prefix() . 'clients.AccountID = ' . db_prefix() . 'PurchaseOrderMaster.AccountID',
			'left'
		);
		$this->db->join(
			db_prefix() . 'PlantLocationDetails',
			db_prefix() . 'PlantLocationDetails.id = ' . db_prefix() . 'PurchaseOrderMaster.PurchaseLocation',
			'left'
		);
		$this->db->join(
			db_prefix() . 'clientwiseshippingdata',
			db_prefix() . 'clientwiseshippingdata.id = ' . db_prefix() . 'PurchaseOrderMaster.DeliveryLocation',
			'left'
		);
		$this->db->join(
			db_prefix() . 'xx_citylist as shipping_citys',
			'shipping_citys.id = ' . db_prefix() . 'clientwiseshippingdata.ShippingCity',
			'left'
		);
		$this->db->join(
			db_prefix() . 'ItemCategoryMaster',
			db_prefix() . 'ItemCategoryMaster.Id = ' . db_prefix() . 'PurchaseOrderMaster.ItemCategory',
			'left'
		);

		// ✅ DATE FILTER
		if (!empty($from_date) && !empty($to_date)) {
			$from_converted = date('Y-m-d', strtotime(str_replace('/', '-', $from_date)));
			$to_converted   = date('Y-m-d', strtotime(str_replace('/', '-', $to_date)));
			$this->db->where(db_prefix() . 'PurchaseOrderMaster.TransDate >=', $from_converted . ' 00:00:00');
			$this->db->where(db_prefix() . 'PurchaseOrderMaster.TransDate <=', $to_converted . ' 23:59:59');
		} else {
			$this->db->where(db_prefix() . 'PurchaseOrderMaster.TransDate >=', date('Y-m-01') . ' 00:00:00');
			$this->db->where(db_prefix() . 'PurchaseOrderMaster.TransDate <=', date('Y-m-t')  . ' 23:59:59');
		}

		if (!empty($category)) {
			$this->db->where(db_prefix() . 'PurchaseOrderMaster.ItemCategory', $category);
		}

		$this->db->order_by(db_prefix() . 'PurchaseOrderMaster.id', 'ASC');

		$result = $this->db->get()->result_array();

		header('Content-Type: application/json');
		echo json_encode($result);
		exit;
	}


	public function getNextOrderNo()
	{
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$this->load->model('purchase_model');

		$category_id = $this->input->post('category_id');
		if (!$category_id) {
			echo json_encode(['success' => false, 'message' => 'No category id']);
			return;
		}

		// Pass category_id to model and get count from tblPurchaseOrderMaster
		$order_count = $this->purchase_model->getNextOrderNoByCategory($category_id);
		$order_count_prefix = $this->purchase_model->getNextOrderNoByCategoryprefix($category_id);

		$order_no = 'PO' . $FY . $PlantID . $order_count_prefix . $order_count;

		echo json_encode(['success' => true, 'order_no' => $order_no]);
	}



	public function GetPurchaseOrderDetails()
	{
		$id = $this->input->post('id');
		$data = $this->purchase_model->GetPurchaseOrderDetails($id);
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

	// public function GetQuotationMasterdate()
	// {
	// 	$QuotationID = $this->input->post('QuotationID');
	// 	$date = $this->purchase_model->GetQuotationMasterdate($QuotationID);
	// 	if ($date) {
	// 		echo json_encode([
	// 			'success' => true,
	// 			'data' => $date
	// 		]);
	// 	} else {
	// 		echo json_encode([
	// 			'success' => false,
	// 			'message' => 'No data found'
	// 		]);
	// 	}
	// }


public function GetQuotationMasterdate()
	{
		$QuotationID = $this->input->post('QuotationID');
		$date = $this->purchase_model->GetQuotationMasterdate($QuotationID);
		if ($date) {
			echo json_encode([
				'success' => true,
				'data' => $date
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No data found'
			]);
		}
	}

public function getCurrency()
{
    $AccountID = $this->input->post('AccountID');

    $currencyData = $this->purchase_model->getCurrency($AccountID);

    if ($currencyData) {
        echo json_encode([
            'status' => 'success',
            'data' => $currencyData
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No data found'
        ]);
    }
}


	public function GetvandocDetails()
	{
		$purchase_order_no = $this->input->post('purchase_order_no');

		$date = $this->purchase_model->GetvandocDetails($purchase_order_no);
		// echo"";
		// print_r($date);
		// die;

		if ($date) {
			echo json_encode([
				'success' => true,
				'data' => $date
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'No data found'
			]);
		}
	}


	public function ListFilter()
	{
		if ($this->input->post()) {

			$data = $this->input->post(null, true);

			$limit  = $data['limit'] ?? 100;
			$offset = $data['offset'] ?? 0;

			$result  = $this->purchase_model->getListByFilter($data, $limit, $offset);
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

		$sheetName = 'Vendor List';
		$writer    = new XLSXWriter();

		$header = [
			'Vendor No'      => 'string',
			'Vendor Name'    => 'string',
			'Favouring Name' => 'string',
			'PAN No'         => 'string',
			'GSTIN'          => 'string',
			'State'          => 'string',
			'Pincode'        => 'string',
			'Mobile'         => 'string',
			'Email'          => 'string',
			'Is Active'      => 'string',
		];

		// suppress_row = true means don't auto-write header yet (we write it manually below)
		$writer->writeSheetHeader($sheetName, $header, ['suppress_row' => true]);

		$selected_company = $this->session->userdata('root_company');
		$company_detail   = $this->purchase_model->get_company_detail($selected_company);

		// ===== ROW 1 : COMPANY NAME =====
		$col_count = count($header) - 1; // 9 (0-indexed last col)
		$writer->markMergedCell($sheetName, 0, 0, 0, $col_count);
		$writer->writeSheetRow($sheetName, [$company_detail->company_name ?? '']);

		// ===== ROW 2 : COMPANY ADDRESS =====
		$writer->markMergedCell($sheetName, 1, 0, 1, $col_count);
		$writer->writeSheetRow($sheetName, [$company_detail->address ?? '']);

		// ===== ROW 3 : FILTER INFO =====
		$state    = $post['state']    ?? '';
		$IsActive = $post['IsActive'] ?? '';

		$reportedBy = 'Filtered By : ';

		if ($state != '') {
			$reportedBy .= 'State : ' . $state . ', ';
		}

		if ($IsActive != '') {
			$reportedBy .= 'Is Active : ' . ($IsActive === 'Y' ? 'Yes' : 'No') . ', ';
		}

		// Trim trailing comma+space
		$reportedBy = rtrim($reportedBy, ', ');

		$writer->markMergedCell($sheetName, 2, 0, 2, $col_count);
		$writer->writeSheetRow($sheetName, [$reportedBy]);

		// ===== ROW 4 : EMPTY SPACER =====
		$writer->writeSheetRow($sheetName, []);

		// ===== ROW 5 : COLUMN HEADERS =====
		$writer->writeSheetRow($sheetName, array_keys($header));

		// ===== CHUNK FETCH =====
		$limit  = 100;
		$offset = 0;

		while (true) {
			$result = $this->purchase_model->getListByFilter($post, $limit, $offset);

			if (empty($result['rows'])) {
				break;
			}

			foreach ($result['rows'] as $row) {
				$writer->writeSheetRow($sheetName, [
					$row['AccountID']     ?? '',
					$row['customer_name'] ?? '',
					$row['FavouringName'] ?? '',
					$row['PAN']           ?? '',
					$row['GSTIN']         ?? '',
					$row['state']         ?? '',
					$row['billing_zip']   ?? '',
					$row['MobileNo']      ?? '',
					$row['Email']         ?? '',
					($row['IsActive'] === 'Y') ? 'Yes' : (($row['IsActive'] === 'N') ? 'No' : ''),
				]);
			}

			$offset += $limit;
			unset($result);
		}

		// ===== SAVE FILE =====
		$filename = 'VendorList_' . date('YmdHis') . '.xlsx';
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
	public function get_company_detail($selected_company)
	{
		return $this->db
			->where('id', $selected_company)
			->get(db_prefix() . 'rootcompany')
			->row();
	}
}
