<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vehiclein extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Inwards_model');
		$this->load->model('Quotation_model');
		$this->load->model('purchase_model');
	}

	/* =========================
		* ADD / EDIT PAGE GATE IN
		* ========================= */
	public function index(){
		if (!has_permission_new('generateGateIn', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Gate In';
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();

		$this->load->view('admin/VehicleIn/GateIn', $data);
	}

	public function GetGateInNo(){
		$location_id = $this->input->post('location_id');
		if (!$location_id) {
			echo json_encode(['success' => false, 'message' => 'No location id']);
			return;
		}
		$GateInNo = $this->Inwards_model->GetGateInNo($location_id);
		echo json_encode(['success' => true, 'data' => $GateInNo]);
	}

	public function SaveGateIn(){
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
			if(!is_array($data[$value])) $data[$value] = trim($data[$value]);
		}
		$required_fields = ['location_id', 'gatein_no', 'gatein_date', 'vehicle_no', 'driver_name', 'phone_no'];
		if ($data['form_mode'] == 'edit') {
    	$required_fields = array_diff($required_fields, ['location_id']);
			$required_fields[] = 'update_id';
		}
		
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}

		$location_id = $data['location_id'] ?? '';
		$gatein_no = $data['gatein_no'] ?? '';
		$vehicle_no = $data['vehicle_no'] ?? '';
		$driver_name = $data['driver_name'] ?? '';
		$phone_no = $data['phone_no'] ?? '';
		if($data['gatein_date']){
			$gatein_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['gatein_date']))).' '.date('H:i:s');
		}else{
			$gatein_date = date('Y-m-d H:i:s');
		}
		$type = $data['type'] ?? 'P';
		$form_mode = $data['form_mode'] ?? 'add';
		$update_id = $data['update_id'] ?? '';

		if($form_mode == 'add'){
		// 	$check = $this->Quotation_model->checkDuplicate('GateMaster', ['GateINID' => $gatein_no]);
		// 	if ($check) {
		// 		echo json_encode([
		// 			'success' => false,
		// 			'message' => 'Gate in number already exists.'
		// 		]);
		// 		die;
		// 	}
			$gatein_no = $this->Inwards_model->GetGateInNo($location_id);
		}

		$gatein_date = !empty($data['gatein_date'])
			? date('Y-m-d', strtotime(str_replace('/', '-', $data['gatein_date'])))
			: date('Y-m-d');
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
		if ($gatein_date < $fy_start || $gatein_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Gate In Date (' . date('d/m/Y', strtotime($gatein_date)) . ') is outside the allowed financial year range ('
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
			'Type' => $type,
			'LocationID' => $location_id,
			'GateINID' => $gatein_no,
			'TransDate' => $gatein_date,
			'VehicleNo' => strtoupper($vehicle_no),
			'DriverName' => ucwords($driver_name),
			'DriverMobileNo' => $phone_no
		];

		$img_array = ['vehicle_image' => 'VehicleImage', 'driver_image' => 'DriverImage'];
		foreach($img_array as $key => $value) {
			$attachmentUrl = null;
			if (isset($_FILES[$key]['name']) && $_FILES[$key]['name'] !== '') {
				$path = FCPATH . 'uploads/GateIn/';
				if (!file_exists($path)) {
					mkdir($path, 0755, true);
				}
				$config['upload_path'] = $path;
				$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
				$config['file_name'] = $gatein_no.$value.'.'.pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
				$config['overwrite'] = TRUE;

				$this->load->library('upload');
				$this->upload->initialize($config);
				if (!$this->upload->do_upload($key)) {
					echo json_encode([
						'success' => false,
						'message' => strip_tags($this->upload->display_errors())
					]);
					die;
				}
				$file_data = $this->upload->data();
				$attachmentUrl = 'uploads/GateIn/' . $file_data['file_name'];
				$insertData[$value] = $attachmentUrl;
			}else{
				if($data['form_mode'] == 'add'){
					echo json_encode([
						'success' => false,
						'message' => 'Please upload '.$key
					]);
					die;
				}
			}
		}

		if ($form_mode == 'add') {
			if (!has_permission_new('generateGateIn', '', 'create')) { access_denied('Access Denied'); }
			$result = $this->Inwards_model->saveData('GateMaster', $insertData);
			$details = $this->Inwards_model->getGateinDetails($result);
		} else {
			if (!has_permission_new('generateGateIn', '', 'edit')) { access_denied('Access Denied'); }
			unset($insertData['GateINID']);
			unset($insertData['LocationID']);
			$result = $this->Inwards_model->updateData('GateMaster', $insertData, ['id' => $update_id]);
			$details = $this->Inwards_model->getGateinDetails($update_id);
		}
		if ($result) {
			echo json_encode([
				'success' => true,
				'message' => 'Gate In '.($form_mode == 'add' ? 'created' : 'updated').' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving data'
			]);
		}
	}

	public function GetGateInDetails(){
		$id = $this->input->post('id');
		$data = $this->Inwards_model->getGateinDetails($id);
		if($data){
			echo json_encode([
				'success' => true,
				'data' => $data
			]);
		}else{
			echo json_encode([
				'success' => false,
				'message' => 'No data found'
			]);
		}
	}

	public function GateInListFilter(){
		if ($this->input->post()) {
			$data = $this->input->post(null, true);
      
      $list  = $data['list'] ?? null;
      $limit  = $data['limit'] ?? 100;
      $offset = $data['offset'] ?? 0;
      if($data['from_date']){
        $data['from_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['from_date'])));
      }else{
        $data['from_date'] = date('Y-m-01');
      }
      
      if($data['to_date']){
        $data['to_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['to_date']))).' 23:59:59';
      }else{
        $data['to_date'] = date('Y-m-d').' 23:59:59';
      }

			$result  = $this->Inwards_model->getGateInListByFilter($data, $limit, $offset, $list);
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
					'total'   => $result['total'],
					'rows'    => $result['rows']
				]);
			}
		}
	}

	public function GateControl(){
		if (!has_permission_new('gateControl', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Gate Control';
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();

		$this->load->view('admin/Inwards/GateInList', $data);
	}
  
  /* =========================
		* LIST PAGE / PREMISES PAGE
		* ========================= */
	public function Premises(){
		$data['title'] = 'Vehicle In Premises';
    
		$this->load->view('admin/VehicleIn/VehicleInPremises', $data);
	}


		/**
	 * API: Get Gate In List By Filter
	 * Calls Inwards_model->getGateInListByFilter and returns JSON
	 */
	public function GateInListExportExcel() {
		if (!has_permission_new('gateControl', '', 'export')) { access_denied('Access Denied'); }
		$this->output->enable_profiler(FALSE);
		ob_end_clean();

		if(!class_exists('XLSXReader_fin')){
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		if (!$this->input->post()) {
			echo json_encode(['success'=>false,'message'=>'Invalid request']);
			return;
		}

		$post = $this->input->post(NULL, TRUE);

		$sheetName = 'Inward List';
		$writer = new XLSXWriter();

		$header = [
			'Trans Date'        => 'string',
			'Location'          => 'string',
			'GateIN ID'         => 'string',
			'ASN ID'            => 'string',
			'ASN Date'          => 'string',
			'Inward ID'         => 'string',
			'Vehicle No'        => 'string',
			'Driver Name'       => 'string',
			'Driver Mobile No'  => 'string'
		];

		$writer->writeSheetHeader($sheetName, $header, ['suppress_row'=>true]);

		$selected_company = $this->session->userdata('root_company');
		$company_detail   = $this->Quotation_model->get_company_detail($selected_company);

		// ===== COMPANY NAME ROW =====
		$writer->markMergedCell($sheetName, 0, 0, 0, 8);
		$writer->writeSheetRow($sheetName, [$company_detail->company_name]);

		// ===== COMPANY ADDRESS ROW =====
		$writer->markMergedCell($sheetName, 1, 0, 1, 8);
		$writer->writeSheetRow($sheetName, [$company_detail->address]);

		// ===== FILTER ROW =====
		$reportedBy = "Filtered By : ";
		if($post['from_date']){
			$from_date = $post['from_date']= date('Y-m-d', strtotime(str_replace('/', '-', $post['from_date'])));
		}else{
			$from_date = $post['from_date'] = date('Y-m-01');
		}
		if($post['to_date']){
			$to_date = $post['to_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $post['to_date'])));
		}else{
			$to_date = $post['to_date'] = date('Y-m-d');
		}
		$vendor_id  = $post['vendor_id'] ?? '';
		$broker_id  = $post['broker_id'] ?? '';
		$status     = $post['status'] ?? 1;

		if($from_date != ''){
			$reportedBy .= 'From Date : ' . date('d/m/Y', strtotime($from_date)) . ', ';
		}
		if($to_date != ''){
			$reportedBy .= 'To Date : ' . date('d/m/Y', strtotime($to_date)) . ', ';
		}
		if($vendor_id != ''){
			$reportedBy .= 'Vendor : ' .( $this->Quotation_model->getData('clients', 'company', ['AccountID' => $vendor_id])['company'] ?? '') . ', ';
		}
		if($broker_id != ''){
			$reportedBy .= 'Broker : ' .( $this->Quotation_model->getData('clients', 'company', ['AccountID' => $broker_id])['company'] ?? '') . ', ';
		}
		$status_list = [1 => 'Pending', 2 =>'Cancel', 3 =>'Expired', 4 =>'Approved', 5 =>'Inprogress', 6 =>'Complete', 7 =>'Partially Complete'];
		if($status != ''){
			$reportedBy .= 'Status : ' .( $status_list[$status] ?? '') . ', ';
		}

		$writer->markMergedCell($sheetName, 2, 0, 2, 8);
		$writer->writeSheetRow($sheetName, [$reportedBy]);
		$writer->writeSheetRow($sheetName, []);

		// ===== HEADER ROW =====
		$writer->writeSheetRow($sheetName, array_keys($header));

		// ===== CHUNK FETCH START =====
		$limit = 100;
		$offset = 0;

		while(true){
			$result = $this->Inwards_model->getGateInListByFilter($post, $limit, $offset);
			if(empty($result['rows'])){
				break;
			}
			foreach($result['rows'] as $row){
				$writer->writeSheetRow($sheetName, [
					$row['TransDate'] ? date('d/m/Y', strtotime($row['TransDate'])) : '',
					$row['LocationName'] ?? '',
					$row['GateINID'] ?? '',
					$row['ASNID'] ?? '',
					$row['ASNDate'] ? date('d/m/Y', strtotime($row['ASNDate'])) : '',
					$row['InwardID'] ?? '',
					$row['VehicleNo'] ?? '',
					$row['DriverName'] ?? '',
					$row['DriverMobileNo'] ?? ''
				]);
			}
			$offset += $limit;
			unset($result);
		}

		// ===== SAVE FILE =====
		$filename = 'GateInList_'.date('YmdHis').'.xlsx';
		$filepath = FCPATH.'uploads/exports/'.$filename;

		if(!is_dir(FCPATH.'uploads/exports')){
			mkdir(FCPATH.'uploads/exports', 0777, true);
		}

		$writer->writeToFile($filepath);

		echo json_encode([
			'success' => true,
			'file_url' => base_url('uploads/exports/'.$filename)
		]);
	}
	
	//======================= View GateinPass_pdf Print ============================
	public function GateinPassPrint($gatein_no){
		if (!$gatein_no) {
			redirect(admin_url('purchase/AddPurchaseOrder'));
		}
		
		if (!has_permission_new('gateControl', '', 'view')) { access_denied('Access Denied'); }
		$invoice = [];
		$data['gatein'] = $this->Inwards_model->getGateinDetails($gatein_no);
		if(!$data['gatein']){
			redirect(admin_url('purchase/Inwards'));
		}
		$data['inward'] = $this->Inwards_model->getInwardsDetails($data['gatein']->InwardID) ?? [];
		if($data['inward']){
			$data['order'] = $this->Inwards_model->getPurchaseOrderDetails($data['inward']['OrderID']) ?? [];
			$data['chamber'] = $this->Inwards_model->getDropdown('ChamberMaster', 'id, ChamberCode, ChamberName', ['GodownID' => $data['inward']['GodownID']], 'id', 'DESC') ?? [];
		}else{
			$data['order'] = $data['chamber'] = [];
		}
		$gateDetails = $this->Inwards_model->getAllINDetails($gatein_no);
		foreach ($gateDetails as $key => $val) {
			$data[$key] = $val;
		}
		$data['gateDetails'] = $gateDetails;
		$data['stack_qc_details'] = $this->Inwards_model->getStackQcDetails($gatein_no);

		$invoice = [
			'invoice' => $data,
		];
	
		try {
			$pdf = GateinPass_pdf($invoice);
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
		
		$pdf->Output(mb_strtoupper(slug_it($gatein_no)) . '-GateinPassSlip.pdf', $type);
	}

}
