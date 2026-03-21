<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mandi extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Quotation_model');
		$this->load->model('Orders_model');
		$this->load->model('Mandi_model');
		$this->load->model('purchase_model');
	}

	/**
	 * Display Mandi Purchase form
	 */
	public function index()
	{
		if (!has_permission_new('MandiPurchase', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Mandi Purchase';
		$data['PurchID'] = $this->Mandi_model->get_po_no();
		$data['Items'] = $this->Mandi_model->get_Items_list();
		$data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
		$data['tds_code_list'] = $this->Mandi_model->get_tds_list();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
		$data['godown_list'] = $this->Mandi_model->get_godown_list();
		// echo "<pre>"; print_r($data['godown_list']); die;
		$this->load->view('admin/Mandi/MandiPurchaseAddEdit', $data);
	}

	public function getNewPurchaseID()
	{
		$purchase_id = $this->Mandi_model->get_po_no();

		echo json_encode([
			'success' => true,
			'PurchID' => $purchase_id
		]);
	}

	/**
	 * Get godown list by location ID
	 */
	public function getGodownByLocation()
	{
		$location_id = $this->input->post('location_id');

		if (empty($location_id)) {
			echo json_encode(['success' => false, 'message' => 'Location ID required.']);
			return;
		}

		$data = $this->Mandi_model->get_godown_by_location($location_id);

		if (!empty($data)) {
			echo json_encode(['success' => true, 'data' => $data]);
		} else {
			echo json_encode(['success' => false, 'message' => 'No godown found for this location.']);
		}
	}

	/**
	 * Get vendor payment terms and TDS percentage
	 */
	public function getVendorTerms()
	{
		$vendor_id = $this->input->post('vendor_id');

		if (empty($vendor_id)) {
			echo json_encode(['success' => false, 'message' => 'Vendor ID required']);
			return;
		}

		$result = $this->Mandi_model->get_vendor_terms($vendor_id);
		echo json_encode($result);
	}

	/**
	 * Save Mandi Purchase (Add/Update)
	 */
	public function SaveMandiPurchase()
	{
		if (!has_permission_new('MandiPurchase', '', 'create')) { access_denied('Access Denied'); }
		// Check request method
		if (!$this->input->is_ajax_request() && $_SERVER['REQUEST_METHOD'] != 'POST') {
			echo json_encode(['success' => false, 'message' => 'Invalid request method']);
			return;
		}

		// Prepare data array
		$purchase_data = [
			'form_mode'             => $this->input->post('form_mode'),
			'update_id'             => $this->input->post('update_id'),
			'purchase_order'        => $this->input->post('purchase_order'),
			'inwards_date'          => $this->input->post('inwards_date'),
			'location_id'           => $this->input->post('location_id'),
			'godown_id'             => $this->input->post('godown_id'),
			'item_id_header'        => $this->input->post('item_id_header'),
			// 'tds_code'              => $this->input->post('tds_code'),
			'vehicle_no'            => $this->input->post('vehicle_no'),
			'total_qty_quintal'     => $this->input->post('total_qty_quintal'),
			'total_value'           => $this->input->post('total_value'),
			'total_brokerage'       => $this->input->post('total_brokerage'),
			'total_market_levy'     => $this->input->post('total_market_levy'),
			'total_gross_value'     => $this->input->post('total_gross_value'),
			'tds'                   => $this->input->post('tds'),
			'total_net_value'       => $this->input->post('total_net_value'),
			'form_json'             => $this->input->post('form_json'),
			'selected_company'      => $this->session->userdata('root_company'),
			'user'                  => $this->session->userdata('staff_user_id'),
			'FY'                    => $this->session->userdata('finacial_year')
		];

		// =============================================
		// Financial Year Date Validation
		// =============================================
		$FY           = $this->session->userdata('finacial_year');
		$FY_int       = (int) $FY;
		$fy_start     = '20' . str_pad($FY_int, 2, '0', STR_PAD_LEFT) . '-04-01';
		$fy_end       = '20' . str_pad($FY_int + 1, 2, '0', STR_PAD_LEFT) . '-03-31';
		$today        = date('Y-m-d');
		$max_txn_date = ($fy_end < $today) ? $fy_end : $today;

		$inwards_date = !empty($purchase_data['inwards_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $purchase_data['inwards_date'])))
			: date('Y-m-d');

		if ($inwards_date < $fy_start || $inwards_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Document Date (' . date('d/m/Y', strtotime($inwards_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}
		// =============================================
		// End of Date Validation
		// =============================================

		// Update purchase_data with converted date for DB insert
		$purchase_data['inwards_date'] = $inwards_date;

		// Call model method
		$result = $this->Mandi_model->save_mandi_purchase($purchase_data);
		echo json_encode($result);
	}


	public function GetMandiDetails()
	{
		$id        = $this->input->post('id');
		$from_date = $this->input->post('from_date');
		$to_date   = $this->input->post('to_date');
		$filter_location = $this->input->post('filter_location');
		$filter_godown = $this->input->post('filter_godown');
		$filter_item = $this->input->post('filter_item');

		if (!empty($id)) {
			$record = $this->Mandi_model->getMandiById($id);
			if ($record) {
				echo json_encode(['success' => true, 'record' => $record]);
			} else {
				echo json_encode(['success' => false, 'message' => 'Record not found.']);
			}
			return;
		}

		// List fetch - date format fix Y/m/d → Y-m-d
		$from_formatted = null;
		$to_formatted   = null;

		if (!empty($from_date)) {
			$from = DateTime::createFromFormat('d/m/Y', $from_date);
			if ($from) $from_formatted = $from->format('Y-m-d'); // ✅ Y-m-d
		}

		if (!empty($to_date)) {
			$to = DateTime::createFromFormat('d/m/Y', $to_date);
			if ($to) $to_formatted = $to->format('Y-m-d'); // ✅ Y-m-d
		}

		$result = $this->Mandi_model->getMandiList($from_formatted, $to_formatted, $filter_location, $filter_godown, $filter_item);

		echo json_encode([
			'success' => true,
			'data'    => $result
		]);
	}


	public function GetMandiDetailsall()
	{

		$id       = $this->input->post('id');
		$order_id = $this->input->post('order_id');

		header('Content-Type: application/json');

		if (empty($id) || empty($order_id)) {
			http_response_code(400);
			echo json_encode([
				'status'  => false,
				'message' => 'id and order_id are required',
				'data'    => []
			]);
			return;
		}

		$data  = $this->Mandi_model->GetMandiDetailsall($id, $order_id);
		$data1 = $this->Mandi_model->GetMandiDetailsalldata($id, $order_id);
		// ECHO"<pre>";print_r($data1);die;


		if (!empty($data)) {
			echo json_encode([
				'status'  => true,
				'message' => 'Data fetched successfully',
				'data'    => $data,
				'data1'   => $data1
			]);
		} else {
			echo json_encode([
				'status'  => false,
				'message' => 'No records found',
				'data'    => [],
				'data1'   => []
			]);
		}
	}


	//======================= View Mandi Purchase Order Print ============================
	public function printMandiPurchaseOrderPdf($order_id)
	{
		// echo"";print_r($PurchID);die;
		$id = '';
		if (!$order_id) {
			redirect(admin_url('admin/Mandi/MandiPurchaseAddEdit'));
		}

		if (!has_permission_new('MandiPurchaseAddEdit', '', 'view')) {
			access_denied('Invoices');
		}
		$invoice = [];
		$data  = $this->Mandi_model->GetMandiDetailsallPDF($id, $order_id);
		$data1 = $this->Mandi_model->GetMandiDetailsalldataPDF($id, $order_id);

		// invoice data + history array madhe ghala
		$invoice = [
			'data'  => $data,   // was 'invoice'
			'data1' => $data1   // was 'history'
		];
		try {
			$pdf = MandiPurchOrder_pdf($invoice);
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

		$pdf->Output(mb_strtoupper(slug_it($order_id)) . '-MandiPurchaseOrder.pdf', $type);
	}

	public function MandiPurchaselist()
	{
		if (!has_permission_new('MandiPurchaseList', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Mandi Purchase';
		$data['PurchID'] = $this->Mandi_model->get_po_no();
		$data['Items'] = $this->Mandi_model->get_Items_list();
		$data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
		$data['tds_code_list'] = $this->Mandi_model->get_tds_list();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
		$data['godown_list'] = $this->Mandi_model->get_godown_list();
		$data['COMPANY'] = $this->Mandi_model->get_COMPANY_list();
		// echo "<pre>"; print_r($data['COMPANY']); die;

		$this->load->view('admin/Mandi/MandiPurchaselist', $data);
	}
}
