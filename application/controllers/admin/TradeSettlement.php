<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TradeSettlement extends AdminController
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('TradeSettlement_model');
		// $this->load->model('SalesQuotation_model');
		// $this->load->model('SalesOrder_model');
		// $this->load->model('DO_model');
		// $this->load->model('SalesInvoice_model');
	}

	/* =========================
	* ADD / EDIT PAGE
	* ========================= */
	public function Purchase()
	{
		$data['title'] = 'Purchase Trade Settlement';
		$data['vendor_list'] = $this->TradeSettlement_model->getVendorDropdown();
		$data['customer_list'] = $this->TradeSettlement_model->getCustomerDropdown();

		$this->load->view('admin/TradeSettlement/PurchTradeSettlement', $data);
	}

	public function Sales()
	{
		$data['title'] = 'Sales Trade Settlement';
		$data['vendor_list'] = $this->TradeSettlement_model->getVendorDropdown();
		$data['customer_list'] = $this->TradeSettlement_model->getCustomerDropdown();

		$this->load->view('admin/TradeSettlement/SalesTradeSettlement', $data);
	}

	public function getBookingDetails()
	{
		$PartyID = $this->input->post('PartyID');

		if (!$PartyID) {
			echo json_encode(['success' => false, 'message' => 'No Party ID']);
			return;
		}
		$vendor_details = $this->TradeSettlement_model->getVendorDetailByPartyID($PartyID);
		$customer_details = $this->TradeSettlement_model->getCustomerDetailByPartyID($PartyID);

		echo json_encode([
			'success' => true,
			'vendor_data' => $vendor_details,
			'customer_data' => $customer_details
		]);
	}

	public function getBookingListDetails()
	{
		$InvoiceID = $this->input->post('InvoiceID');
		$data = $this->TradeSettlement_model->getBookingListDetails($InvoiceID);
		$inward_data = $this->TradeSettlement_model->getInwardDetails($InvoiceID);
		$sales_data = $this->TradeSettlement_model->getSalesOrderDetails($InvoiceID);
		$delivery_data = $this->TradeSettlement_model->getDeliveryOrderDetails($InvoiceID);
			echo json_encode([
				'success' => true,
				'data' => $data,
				'inward_data' => $inward_data,
				'sales_data' => $sales_data,
				'delivery_data' => $delivery_data
			]);
	}

	public function SaveSalesInvoice()
	{
		$PlantID = $this->session->userdata('root_company');
		$UserID = $this->session->userdata('username');
		$FY = $this->session->userdata('finacial_year');
		$data = $this->input->post(null, true);
		$field_names = array_keys($data);
		foreach ($field_names as $key => $value) {
			if (!is_array($data[$value])) $data[$value] = trim($data[$value]);
		}
		$required_fields = ['item_category', 'OrderID', 'invoice_date', 'SalesLocation', 'customer_id', 'customer_location'];

		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill ' . $value . ' fields'
				]);
				die;
			}
		}
		$customer_id = $data['customer_id'] ?? '';
		$deliveryorder_id = $data['deliveryorder_id'] ?? '';
		$item_category = $data['item_category'] ?? '';
		$InvoiceID = $data['OrderID'] ?? '';
		if ($data['invoice_date']) {
			$invoice_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['invoice_date'])));
		} else {
			$invoice_date = date('Y-m-d');
		}
		$SalesLocation = $data['SalesLocation'] ?? '';
		$customer_state = $data['customer_state'] ?? '';
		$customer_location = $data['customer_location'] ?? '';
		// $billing_state = $data['billing_state'] ?? '';
		// $GSTIN = $data['GSTIN'] ?? '';
		$gate_no = $data['gate_no'] ?? '';

		// Data is coming from the $do_data(tblDeliveryOrderMaster) against Delivery ID(OrderID)
		$do_data = $this->SalesInvoice_model->getDeliveryOrderDetails($deliveryorder_id);

		$advreg = $do_data['AdvRegType'] ?? 'Y';
		$consignee_id = $do_data['ConsigneeName'] ?? '';
		$consignee_location = $do_data['ConsigneeLocation'] ?? '';
		$customer_address = $do_data['CustAddress'] ?? '';
		$consignee_address = $do_data['ConsigneeAddress'] ?? '';
		$trans_arranged = $do_data['TransArranged'] ?? 'N';
		$transporter_name = $do_data['TransporterID'] ?? '';
		$VehicleNo = $do_data['VehicleNo'] ?? '';
		$lr_no = $do_data['LRNo'] ?? '';
		if ($do_data['LRDate']) {
			$LRDate = date('Y-m-d', strtotime(str_replace('/', '-', $do_data['LRDate'])));
		} else {
			$LRDate = date('Y-m-d');
		}
		$driver_name = $do_data['DriverName'] ?? '';
		$mobile = $do_data['MobileNo'] ?? '';
		$license_no = $do_data['LicenseNo'] ?? '';
		$freight_rate = $do_data['FreightRate'] ?? '';
		$total_freight = $do_data['TotalFreight'] ?? '';
		$by_customer = $do_data['ToPayByCust'] ?? '';
		$pay_cash = $do_data['PayInCash'] ?? '';
		$pay_bank = $do_data['PayByBank'] ?? '';
		$after_delivery = $do_data['PayAfterDelivery'] ?? '';


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
			$check = $this->SalesInvoice_model->checkDuplicate('SalesInvoiceMaster', ['InvoiceID' => $InvoiceID]);
			if ($check) {
				echo json_encode([
					'success' => false,
					'message' => 'Invoice ID already exists.'
				]);
				die;
			}
		}

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'InvoiceID' => $InvoiceID,
			'DeliveryOrderID' => $deliveryorder_id,
			'InvoiceDate' => $invoice_date,
			'SalesLocation' => $SalesLocation,
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
			'LRDate' => $LRDate,
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

			// 'GSTIN' => $GSTIN,
			// 'billing_state' => $billing_state,


		];

		if ($form_mode == 'add') {
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->SalesInvoice_model->saveData('SalesInvoiceMaster', $insertData);
			// SAVE LEDGER
			$this->SalesInvoice_model->createSalesLedger(
				$InvoiceID,
				$deliveryorder_id,
				$data['customer_id'],
				date('Y-m-d'),
				$item_total_amt,
				$total_disc_amt,
				$cgst_amt,
				$sgst_amt,
				$igst_amt,
				$net_amt,
				$round_off_amt
			);
			$details = $this->SalesInvoice_model->getSalesInvoiceDetails($result);
		} else {
			$insertData['Lupdate'] = date('Y-m-d H:i:s');
			$result = $this->SalesInvoice_model->updateData('SalesInvoiceMaster', $insertData, ['id' => $update_id]);
			// UPDATE LEDGER
			$this->SalesInvoice_model->updateSalesLedger(
				$InvoiceID,
				$deliveryorder_id,
				$customer_id,
				$invoice_date,
				$item_total_amt,
				$total_disc_amt,
				$cgst_amt,
				$sgst_amt,
				$igst_amt,
				$net_amt,
				$round_off_amt
			);

			$details = $this->SalesInvoice_model->getSalesInvoiceDetails($update_id);
		}

		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'so_no' => $data['so_no'] ?? [],
				'BillID' => $InvoiceID,
				'TransID' => $deliveryorder_id,
				'invoice_date' => $invoice_date,
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

			$this->SalesInvoice_model->saveMultiData($multi_insert_data);

			echo json_encode([
				'success' => true,
				'message' => 'Sales Invoice ' . ($form_mode == 'add' ? 'created' : 'updated') . ' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving Invoice'
			]);
		}
	}

	public function GetSalesInvoiceDetails()
	{
		$id = $this->input->post('id');
		$data = $this->SalesInvoice_model->GetSalesInvoiceDetails($id);
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
		$data['title'] = 'Sales Invoice List';
		$data['customer_list'] = $this->SalesInvoice_model->getCustomerDropdown();

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->SalesInvoice_model->get_company_detail($selected_company);

		$this->load->view('admin/SalesInvoice/SalesInvoiceList', $data);
	}

	public function ListFilter()
	{
		if ($this->input->post()) {
			$data = $this->input->post(null, true);

			$limit  = $data['limit'] ?? 100;
			$offset = $data['offset'] ?? 0;

			$result  = $this->SalesInvoice_model->getListByFilter($data, $limit, $offset);
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
}
