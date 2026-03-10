<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('purchase_model');
		$this->load->model('Quotation_model');
		$this->load->model('currencies_model');
		$this->load->model('invoice_model');
	}

	public function AddEditPurchInvoice($id = '')
	{
		/*if($this->input->post()){
	        $form_data = $this->input->post();
	        $result = $this->invoice_model->AddNewPurchInvoice($form_data);
	        
	    }*/
		$data['title'] = 'Add Edit Purchase Invoice';
		$data['vendor_list'] = $this->invoice_model->GetPendingInvoiceVendorList();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
		$data['RootCompanyDetails'] = $this->invoice_model->GetRootCompanyDetails();
		/*echo "<pre>";
	    print_r($data['vendor_list']);
	    die;*/
		$data['item_type'] = $this->Quotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['FreightTerms'] = $this->purchase_model->get_freight_terms();
		$data['currencies'] = $this->currencies_model->get();
		$data['Broker'] = $this->purchase_model->get_broker_name();
		$this->load->view('purchase_invoice/AddPurchaseInvoice', $data);
	}
	public function AddNewPurchInvoice()
	{
		$data = array(
			"VendorID" => $this->input->post('VendorID'),
			"InwardID" => $this->input->post('InwardID'),
			"InvoiceDate" => $this->input->post('InvoiceDate'),
			"vendor_doc_no" => $this->input->post('vendor_doc_no'),
			"VendorDocDate" => $this->input->post('VendorDocDate'),
			"vendor_doc_amount" => $this->input->post('vendor_doc_amount'),
			"vendor_dispatch_weight" => $this->input->post('vendor_dispatch_weight'),
			"internal_remarks" => $this->input->post('internal_remarks'),
			"document_remark" => $this->input->post('document_remark'),
			"vendor_state" => $this->input->post('vendor_state'),
			"Company_state" => $this->input->post('Company_state'),
			"hold_payment" => $this->input->post('hold_payment'),
		);

		// Convert dates to Y-m-d before validation
		$invoice_date = !empty($data['InvoiceDate'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['InvoiceDate'])))
			: date('Y-m-d');

		$vendor_doc_date = !empty($data['VendorDocDate'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['VendorDocDate'])))
			: date('Y-m-d');

		// =============================================
		// Financial Year Date Validation
		// =============================================
		$FY           = $this->session->userdata('finacial_year');
		$FY_int       = (int) $FY;
		$fy_start     = '20' . str_pad($FY_int, 2, '0', STR_PAD_LEFT) . '-04-01';
		$fy_end       = '20' . str_pad($FY_int + 1, 2, '0', STR_PAD_LEFT) . '-03-31';
		$today        = date('Y-m-d');
		$max_txn_date = ($fy_end < $today) ? $fy_end : $today;

		// --- InvoiceDate check ---
		if ($invoice_date < $fy_start || $invoice_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Invoice Date (' . date('d/m/Y', strtotime($invoice_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- VendorDocDate check ---
		if ($vendor_doc_date < $fy_start || $vendor_doc_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Vendor Doc Date (' . date('d/m/Y', strtotime($vendor_doc_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}

		// --- VendorDocDate must not be after InvoiceDate ---
		if ($vendor_doc_date > $invoice_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Vendor Doc Date (' . date('d/m/Y', strtotime($vendor_doc_date)) . ') cannot be later than Invoice Date (' . date('d/m/Y', strtotime($invoice_date)) . ').'
			]);
			return;
		}
		// =============================================
		// End of Date Validation
		// =============================================

		// Update $data array with converted dates for DB insert
		$data['InvoiceDate']   = $invoice_date;
		$data['VendorDocDate'] = $vendor_doc_date;

		$attachmentUrl = null;
		if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] !== '') {
			$path = FCPATH . 'uploads/item_master/';
			if (!file_exists($path)) {
				mkdir($path, 0755, true);
			}
			$config['upload_path'] = $path;
			$config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png';
			$config['max_size']      = 5000;
			$config['file_name'] = $data['InvoiceID_Hidden'] . '_Attachment.' . pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
			$config['overwrite'] = TRUE;

			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('attachment')) {
				echo json_encode([
					'success' => false,
					'message' => strip_tags($this->upload->display_errors())
				]);
				die;
			}
			$file_data = $this->upload->data();
			$attachmentUrl = 'uploads/item_master/' . $file_data['file_name'];

			$data['Attachment'] = $attachmentUrl;
		} else {
			$data['Attachment'] = null;
		}

		$result = $this->invoice_model->AddNewPurchInvoice($data);

		echo json_encode($result);
	}
	public function GetPendingInwardList()
	{
		$VendorID = $this->input->post('VendorID');
		$PendingInwardList = $this->invoice_model->GetPendingInwardList($VendorID);
		$VendorDetails = $this->invoice_model->GetVendorDetails($VendorID);
		echo json_encode(['status' => true, 'InwardList' => $PendingInwardList, "VendorDetails" => $VendorDetails, 'message' => 'Pending Inward List']);
	}

	public function GetInvoiceList()
	{
		$data = array(
			"from_date" => $this->input->post('from_date'),
			"to_date" => $this->input->post('to_date'),
			"category" => $this->input->post('category'),
		);
		$InvoiceList = $this->invoice_model->GetInvoiceList($data);
		echo json_encode($InvoiceList);
	}
	public function GetPurchaseInvoiceDetails()
	{
		$data = array(
			"InvoiceID" => $this->input->post('InvoiceID'),
		);
		$InvoiceDetails = $this->invoice_model->GetPurchaseInvoiceDetails($data);
		echo json_encode($InvoiceDetails);
	}
	public function GetInwardDetails()
	{
		$InwardID = $this->input->post('InwardID');
		$InwardDetails = $this->invoice_model->GetInwardDetailsByInwardID($InwardID);
		echo json_encode(['status' => true, 'InwardDetails' => $InwardDetails, 'message' => 'Inward Details']);
	}
	public function index()
	{
		$data['title'] = 'Invoice Master';

		$data['vendor_list'] = $this->invoice_model->GetPendingInvoiceVendorList();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
		$data['RootCompanyDetails'] = $this->invoice_model->GetRootCompanyDetails();
		$data['item_type'] = $this->Quotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['FreightTerms'] = $this->purchase_model->get_freight_terms();
		$data['currencies'] = $this->currencies_model->get();
		$data['Broker'] = $this->purchase_model->get_broker_name();

		$this->load->view('admin/Invoice/InvoiceAddEdit', $data);
	}

	public function UpdateInvoice()
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
		$required_fields = ['update_id', 'hold_payment', 'frt_terms', 'BrokerID'];

		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill ' . $value . ' fields'
				]);
				die;
			}
		}

		$check = $this->Quotation_model->checkDuplicate('PurchaseInvoiceMaster', ['id' => $data['update_id']]);
		if (!$check) {
			echo json_encode([
				'success' => false,
				'message' => 'Invoice not exists.'
			]);
			die;
		}

		// =============================================
		// Financial Year Date Validation
		// =============================================
		$vendor_doc_date = !empty($data['VendorDocDate'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['VendorDocDate'])))
			: date('Y-m-d');

		$FY_int       = (int) $FY;
		$fy_start     = '20' . str_pad($FY_int, 2, '0', STR_PAD_LEFT) . '-04-01';
		$fy_end       = '20' . str_pad($FY_int + 1, 2, '0', STR_PAD_LEFT) . '-03-31';
		$today        = date('Y-m-d');
		$max_txn_date = ($fy_end < $today) ? $fy_end : $today;

		if ($vendor_doc_date < $fy_start || $vendor_doc_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Vendor Doc Date (' . date('d/m/Y', strtotime($vendor_doc_date)) . ') is outside the allowed financial year range ('
					. date('d/m/Y', strtotime($fy_start)) . ' to ' . date('d/m/Y', strtotime($max_txn_date)) . ').'
			]);
			return;
		}
		// =============================================
		// End of Date Validation
		// =============================================

		$updateArray = [
			"HoldPayment" => $data["hold_payment"],
			"VendorDocNo" => $data["vendor_doc_no"],
			"VendorDocDate" => to_sql_date($data["VendorDocDate"]),
			"VendorDocAmt" => $data["vendor_doc_amount"],
			"VendorDocWeight" => $data["vendor_dispatch_weight"],
			"BrokerID" => $data["BrokerID"],
			"FreightTerms" => $data["frt_terms"],
			"Internal_Remarks" => $data["internal_remarks"],
			"Document_Remark" => $data["document_remark"],
		];

		$attachmentUrl = null;
		if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] !== '') {
			$path = FCPATH . 'uploads/item_master/';
			if (!file_exists($path)) {
				mkdir($path, 0755, true);
			}
			$config['upload_path'] = $path;
			$config['allowed_types'] = 'pdf|doc|docx|xls|xlsx|jpg|jpeg|png';
			$config['max_size']      = 5000;
			$config['file_name'] = $data['InvoiceID_Hidden'] . '_Attachment.' . pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
			$config['overwrite'] = TRUE;

			$this->load->library('upload', $config);
			if (!$this->upload->do_upload('attachment')) {
				echo json_encode([
					'success' => false,
					'message' => strip_tags($this->upload->display_errors())
				]);
				die;
			}
			$file_data = $this->upload->data();
			$attachmentUrl = 'uploads/item_master/' . $file_data['file_name'];

			$updateArray['Attachment'] = $attachmentUrl;
		}

		$update = $this->invoice_model->updateData('PurchaseInvoiceMaster', $updateArray, ['id' => $data['update_id']]);
		if ($update) {
			echo json_encode([
				'success' => true,
				'message' => 'Invoice Updated Successfully'
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Something went wrong'
			]);
		}
		// echo '<pre>'; print_r($data); die;
	}

	public function PrintPDF($InwardID)
	{
		$data = $this->invoice_model->getInvoiceDetailsPrint($InwardID);
		if (!$data) {
			redirect(admin_url('purchase/Invoice'));
		}
		$invoice = [
			'invoice' => $data ?? []
		];

		try {
			$pdf = PurchInvoice_pdf($invoice);
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

		$pdf->Output(mb_strtoupper(slug_it($InwardID)) . '-InwardSlip.pdf', $type);
	}

	/* =========================
		* LIST PAGE
		* ========================= */
	public function List()
	{
		$data['title'] = 'Invoice List';

		$this->load->view('admin/Invoice/InvoiceList', $data);
	}
}
