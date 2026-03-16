<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inwards extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Inwards_model');
		$this->load->model('Quotation_model');
		$this->load->model('purchase_model');
	}

	/* =========================
		* ADD / EDIT PAGE
		* ========================= */
	public function index(){
		if (!has_permission_new('PurchaseInward', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Inwards Master';
		$data['vendor_list'] = $this->Inwards_model->getVendorDropdownByPO();
		$data['inwards_list'] = $this->Inwards_model->getInwardsList();
		// $data['gatein_list'] = $this->Inwards_model->getDropdown('GateMaster', 'id, GateINID, VehicleNo', ['InwardID' => null, 'Type' => 'P'], 'id', 'DESC');

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->Quotation_model->get_company_detail($selected_company);

		$this->load->view('admin/Inwards/InwardsAddEdit', $data);
	}

	public function getVendorDetails(){
		$vendor_id = $this->input->post('vendor_id');
		if (!$vendor_id) {
			echo json_encode(['success' => false, 'message' => 'No vendor id']);
			return;
		}
		$vendor_details = $this->Quotation_model->getVendorDetailByAccountID($vendor_id);
		$order_list = $this->Inwards_model->getVendorPurchaseOrder($vendor_id);
		
		echo json_encode(['success' => true, 'data' => $vendor_details, 'order_list' => $order_list]);
	}

	public function GetOrderDetails(){
		$order_id = $this->input->post('order_id');
		if (!$order_id) {
			echo json_encode(['success' => false, 'message' => 'No order id']);
			return;
		}
		$order_details = $this->Inwards_model->getPurchaseOrderDetails($order_id);
		$item_list = $this->Quotation_model->getDropdown('items', 'ItemId, ItemName', ['ItemCategoryCode' => $order_details['ItemCategory'], 'IsActive' => 'Y'], 'ItemName', 'ASC') ?? [];
		$gatein_list = $this->Quotation_model->getDropdown('GateMaster', 'id, GateINID, VehicleNo', ['InwardID' => null, 'Type' => 'P', 'LocationID' => $order_details['PurchaseLocation']], 'id', 'DESC') ?? [];
		$godown_list = $this->Quotation_model->getDropdown('godownmaster', 'id, GodownCode, GodownName', ['LocationID' => $order_details['PurchaseLocation'], 'IsActive' => 'Y'], 'GodownName', 'ASC') ?? [];
		$inwards_no = $this->Inwards_model->getNextInwardsNoByCategory($order_details['ItemCategory']);

		echo json_encode(['success' => true, 'data' => $order_details, 'item_list' => $item_list, 'gatein_list' => $gatein_list, 'godown_list' => $godown_list, 'inwards_no' => $inwards_no]);
	}

	public function SaveInwards(){
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
		$required_fields = ['vendor_id', 'purchase_order', 'inwards_no', 'inwards_date', 'godown_id'];
		
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}

		$vendor_id = $data['vendor_id'] ?? '';
		$vendor_gst_no = $data['vendor_gst_no'] ?? '';
		$vendor_country = $data['vendor_country'] ?? '';
		$vendor_state = $data['vendor_state'] ?? '';
		$vendor_address = $data['vendor_address'] ?? '';
		$purchase_order = $data['purchase_order'] ?? '';
		$inwards_no = $data['inwards_no'] ?? '';
		$gatein_no = $data['gatein_no'] ?? '';
		$godown_id = $data['godown_id'] ?? '';
		if($data['inwards_date']){
			$inwards_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['inwards_date'])));
		}else{
			$inwards_date = date('Y-m-d');
		}
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

		$order_details = $this->Inwards_model->getPurchaseOrderDetails($purchase_order);
		if($form_mode == 'add'){
		// 	$check = $this->Quotation_model->checkDuplicate('PurchInwardsMaster', ['InwardsID' => $inwards_no]);
		// 	if ($check) {
		// 		echo json_encode([
		// 			'success' => false,
		// 			'message' => 'Inwards number already exists.'
		// 		]);
		// 		die;
		// 	}
			$inwards_no = $this->Inwards_model->getNextInwardsNoByCategory($order_details['ItemCategory']);
		}

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'PurchaseLocation' => $order_details['PurchaseLocation'] ?? '',
			'InwardsID' => $inwards_no,
			'OrderID' => $purchase_order,
			'GodownID' => $godown_id,
			'TransDate' => $inwards_date,
			'ItemType' => $order_details['ItemType'] ?? '',
			'ItemCategory' => $order_details['ItemCategory'] ?? '',
			'AccountID' => $vendor_id,
			'BrokerID' => $order_details['BrokerID'] ?? '',
			'VendorLocation' => $order_details['DeliveryLocation'] ?? '',
			'DeliveryFrom' => $order_details['DeliveryFrom'] ?? date('Y-m-d'),
			'DeliveryTo' => $order_details['DeliveryTo'] ?? date('Y-m-d', strtotime('+10 days')),
			'PaymentTerms' => $order_details['PaymentTerms'] ?? '',
			'FreightTerms' => $order_details['FreightTerms'] ?? '',
			'GSTIN' => $vendor_gst_no,
			'TotalWeight' => $total_weight,
			'TotalQuantity' => $total_qty,
			'ItemAmt' => $item_total_amt,
			'DiscAmt' => $total_disc_amt,
			'TaxableAmt' => $taxable_amt,
			'CGSTAmt' => $cgst_amt,
			'SGSTAmt' => $sgst_amt,
			'IGSTAmt' => $igst_amt,
			'TDSSection' => $order_details['TDSSection'] ?? '',
			'TDSPercentage' => $order_details['TDSPercentage'] ?? '',
			'TDSAmt' => $order_details['TDSAmt'] ?? '',
			'RoundOffAmt' => $round_off_amt,
			'NetAmt' => $net_amt
		];

		if ($form_mode == 'add') {
			if (!has_permission_new('PurchaseInward', '', 'create')) { access_denied('Access Denied'); }
			$insertData['TransDate2'] = date('Y-m-d H:i:s');
			$result = $this->Inwards_model->saveData('PurchInwardsMaster', $insertData);
			$details = $this->Inwards_model->getInwardsDetails($result);
			$this->Inwards_model->updateData('PurchaseOrderMaster', ['Status' => 6], ['PurchID' => $purchase_order]);
		} else {
			if (!has_permission_new('PurchaseInward', '', 'edit')) { access_denied('Access Denied'); }
			$result = $this->Inwards_model->updateData('PurchInwardsMaster', $insertData, ['id' => $update_id]);
			$details = $this->Inwards_model->getInwardsDetails($update_id);
		}
		if(!empty($gatein_no)){
			$this->Inwards_model->updateData('GateMaster', ['InwardID' => $inwards_no], ['GateINID' => $gatein_no]);
		}
		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'inwards_no' => $inwards_no,
				'inwards_date' => $inwards_date,
				'vendor_id' => $vendor_id,
				'vendor_state' => $vendor_state,
				'item_uid' => $data['item_uid'] ?? [],
				'item_id' => $data['item_id'] ?? [],
				'hsn_code' => $data['hsn_code'] ?? [],
				'unit_rate' => $data['unit_rate'] ?? [],
				'unit_weight' => $data['unit_weight'] ?? [],
				'disc_amt' => $data['disc_amt'] ?? [],
				'uom' => $data['uom'] ?? [],
				'quantity' => $data['min_qty'] ?? [],
				'amount' => $data['amount'] ?? [],
				'gst' => $data['gst'] ?? [],
				'TType' => 'P',
				'TType2' => 'Inward',
				'GodownID' => $godown_id
			];
			$this->Inwards_model->saveMultiData($multi_insert_data);

			echo json_encode([
				'success' => true,
				'message' => 'Inwards '.($form_mode == 'add' ? 'created' : 'updated').' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving Inwards'
			]);
		}
	}

	public function GetInwardsDetails(){
		$id = $this->input->post('id');
		$data = $this->Inwards_model->getInwardsDetails($id);
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

	public function PrintPDF($InwardID){
		if (!has_permission_new('PurchaseInward', '', 'view')) { access_denied('Access Denied'); }
		$data = $this->Inwards_model->getInwardsDetailsPrint($InwardID);
		if(!$data){
			redirect(admin_url('purchase/Inwards'));
		}
		$invoice = [
			'invoice' => $data
		];

		try {
			$pdf = PurchInward_pdf($invoice);
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
		* DETAILS PAGE
		* ========================= */
	public function Details($gatein_no){
		$data['title'] = 'Inwards Master';
		$gatein = $this->Inwards_model->getGateinDetails($gatein_no);
		$statusArray = [1 =>  'Gate IN Generated', 2 => 'Gross Weight Captured', 3 => 'Conveyor Assigned', 4 => 'QC Stack Captured', 5 => 'Tare Weight Captured', 6 => 'Gate Out Pass Generated', 7 => 'Vehicle Exit', 8 => 'QC', 9 => 'Purchase Invoice Generated', 10 => 'Complete'];
		$gatein->StatusName = $statusArray[$gatein->status];
		$data['gatein'] = $gatein;
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

		$this->load->view('admin/Inwards/InwardsDetails', $data);
	}

	private function validateStage($GateINID, $currentStage){
		$gateStatus = $this->Inwards_model->getData('GateMaster','status',['GateINID'=>$GateINID])['status'] ?? 0;

		// Cannot skip stage
		if($currentStage > ($gateStatus + 1)){
			return [
				'success'=>false,
				'message'=>'Previous stage not completed.'
			];
		}

		// Cannot go back
		if($currentStage < $gateStatus){
			return [
				'success'=>false,
				'message'=>'Cannot modify previous stage.'
			];
		}

		return ['success'=>true];
	}

	public function SaveGrossWeight(){
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
		$required_fields = ['GateINID', 'gross_weight'];
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}

		$GateINID = $data['GateINID'] ?? '';
		$gross_weight = $data['gross_weight'] ?? '';
		$form_mode = $data['form_mode'] ?? 'add';
		$update_id = $data['update_id'] ?? '';

		$check = $this->validateStage($GateINID,2);
		if(!$check['success']){
			echo json_encode($check);
			die;
		}

		if($form_mode == 'add'){
			$check = $this->Quotation_model->checkDuplicate('GateMasterDetails', ['GateINID' => $GateINID, 'type' => 'GrossWeight']);
			if ($check) {
				echo json_encode([
					'success' => false,
					'message' => 'Record already exists.'
				]);
				die;
			}
			$TopImgUrl = $FrontImgUrl = $SideImgUrl = '';
		}else{
			$details = $this->Inwards_model->getINDetails($update_id);
			$TopImgUrl = $details->value->TopImage ?? '';
			$FrontImgUrl = $details->value->FrontImage ?? '';
			$SideImgUrl = $details->value->SideImage ?? '';
		}

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'GateINID' => $GateINID,
			'type' => 'GrossWeight'
		];
		$valueArray = ['gross_weight' => $gross_weight, 'TopImage' => $TopImgUrl, 'FrontImage' => $FrontImgUrl, 'SideImage' => $SideImgUrl];

		$img_array = ['top_image' => 'TopImage', 'front_image' => 'FrontImage', 'side_image' => 'SideImage'];
		foreach($img_array as $key => $value) {
			$attachmentUrl = null;
			if (isset($_FILES[$key]['name']) && $_FILES[$key]['name'] !== '') {
				$path = FCPATH . 'uploads/GateInDetails/';
				if (!file_exists($path)) {
					mkdir($path, 0755, true);
				}
				$config['upload_path'] = $path;
				$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
				$config['file_name'] = $GateINID.'GrossWeight'.$value.'.'.pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
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
				$attachmentUrl = 'uploads/GateInDetails/' . $file_data['file_name'];
				$valueArray[$value] = $attachmentUrl;
			}else{
				// if($form_mode == 'add'){
				// 	echo json_encode([
				// 		'success' => false,
				// 		'message' => 'Please upload '.$key
				// 	]);
				// 	die;
				// }
			}
		}
		$insertData['value'] = json_encode($valueArray);

		if ($form_mode == 'add') {
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->Inwards_model->saveData('GateMasterDetails', $insertData);
			$details = $this->Inwards_model->getINDetails($result);
		} else {
			$result = $this->Inwards_model->updateData('GateMasterDetails', $insertData, ['id' => $update_id]);
			$details = $this->Inwards_model->getINDetails($update_id);
		}
		$this->Inwards_model->updateData('GateMaster', ['status' => 2], ['GateINID' => $GateINID]);
		if ($result) {
			echo json_encode([
				'success' => true,
				'message' => 'Gross weight '.($form_mode == 'add' ? 'added' : 'updated').' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving data'
			]);
		}
	}

	public function SaveConveyorAssignment(){
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
		$required_fields = ['GateINID', 'conveyor_id'];
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}

		$GateINID = $data['GateINID'] ?? '';
		$conveyor_id = $data['conveyor_id'] ?? '';
		$form_mode = $data['form_mode'] ?? 'add';
		$update_id = $data['update_id'] ?? '';

		$check = $this->validateStage($GateINID,3);
		if(!$check['success']){
			echo json_encode($check);
			die;
		}

		if($form_mode == 'add'){
			$check = $this->Quotation_model->checkDuplicate('GateMasterDetails', ['GateINID' => $GateINID, 'type' => 'Conveyor']);
			if ($check) {
				echo json_encode([
					'success' => false,
					'message' => 'Record already exists.'
				]);
				die;
			}
		}

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'GateINID' => $GateINID,
			'type' => 'Conveyor',
			'value' => json_encode(['ConveyorID' => $conveyor_id])
		];

		if ($form_mode == 'add') {
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->Inwards_model->saveData('GateMasterDetails', $insertData);
			$details = $this->Inwards_model->getINDetails($result);
		} else {
			$result = $this->Inwards_model->updateData('GateMasterDetails', $insertData, ['id' => $update_id]);
			$details = $this->Inwards_model->getINDetails($update_id);
		}
		$this->Inwards_model->updateData('GateMaster', ['status' => 3], ['GateINID' => $GateINID]);
		if ($result) {
			echo json_encode([
				'success' => true,
				'message' => 'Conveyor '.($form_mode == 'add' ? 'added' : 'updated').' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving data'
			]);
		}
	}

	public function ItemQCList(){
		$itemID = $this->input->post('ItemID') ?? '';
		if(empty($itemID)){
			echo json_encode([]);
			return;
		}
		$data = $this->Inwards_model->getByItemID($itemID);
		echo json_encode($data);
	}

	public function StackListByChamber(){
		$chamber_id = $this->input->post('chamber_id') ?? '';
		if(empty($chamber_id)){
			echo json_encode([]);
			return;
		}
		$data = $this->Inwards_model->getDropdown('StackMaster', 'id, StackCode, StackName', ['ChamberID' => $chamber_id], 'id', 'DESC');
		echo json_encode($data);
	}

	public function LotListByStack(){
		$stack_id = $this->input->post('stack_id') ?? '';
		if(empty($stack_id)){
			echo json_encode([]);
			return;
		}
		$data = $this->Inwards_model->getDropdown('LotMaster', 'id, LotCode, LotName', ['StackID' => $stack_id], 'id', 'DESC');
		echo json_encode($data);
	}

	public function SaveStackQCDetails(){
		if (empty($this->input->post())) {
			echo json_encode(['success'=>false,'message'=>'No data received']);
			return;
		}

		$PlantID = $this->session->userdata('root_company');
		$UserID  = $this->session->userdata('username');
		$FY      = $this->session->userdata('finacial_year');

		$data = $this->input->post(null,true);

		foreach ($data as $k=>$v){
			if(!is_array($v)) $data[$k] = trim($v);
		}

		if(empty($data['GateINID'])){
			echo json_encode(['success'=>false,'message'=>'GateINID required']);
			return;
		}

		$GateINID = $data['GateINID'];
		$form_mode = $data['form_mode'] ?? 'add';
		$godown = $data['godown'] ?? '';
		$rows = [];

		if (!empty($data['rows_json'])) {
				$rows = json_decode($data['rows_json'], true);
		}

		if (!is_array($rows)) {
				echo json_encode([
						'success' => false,
						'message' => 'Invalid QC details data'
				]);
				return;
		}

		if(empty($rows)){
			echo json_encode(['success'=>false,'message'=>'QC details missing']);
			return;
		}

		$check = $this->validateStage($GateINID,4);
		if(!$check['success']){
			echo json_encode($check);
			die;
		}

		$gateinData = $this->Inwards_model->getGateinDetails($GateINID);
		if(!$gateinData){
			echo json_encode(['success'=>false,'message'=>'Gate in not found']);
			return;
		}

		$inwardData = $this->Inwards_model->getInwardsDetails($gateinData->InwardID);
		if(!$inwardData){
			echo json_encode(['success'=>false,'message'=>'Inward not found']);
			return;
		}

		foreach ($rows as $i=>$row){
			$insertData = [
				'PlantID'=>$PlantID,
				'FY'=>$FY,
				'PurchID'=>$inwardData['OrderID'],
				'GateINID'=>$GateINID,
				'InwardID'=>$inwardData['InwardsID'],
				'QCID'=>$i+1,
				'CenterQCApprove'=>'Y',
				'ROQCApprove'=>'Y',
				'HOQCApprove'=>'Y',
				'TType'=>'P',
				'ItemID'=>$row['item_id'],
				'AccountID'=>$inwardData['AccountID'],
				'LocationID'=>$inwardData['PurchaseLocation'],
				'WHID'=>$godown,
				'CHID'=>$row['chamber'],
				'StackID'=>$row['stack'],
				'LOTID'=>$row['lot'],
				'Weight'=>$row['weight'],
				'BagQty'=>$row['bag_qty']
			];

			$where = [
				'GateINID'=>$GateINID,
				'ItemID'=>$row['item_id'],
				'TType'=>'P',
			];

			$check = $this->Inwards_model->checkDuplicate('stockInventory',$where);

			if($check){
				$this->Inwards_model->updateData('stockInventory',$insertData,$where);
				$last_id = $this->Inwards_model->getRowData('stockInventory','id',$where)->id;
			}else{
				$insertData['TransDate'] = date('Y-m-d H:i:s');
				$last_id = $this->Inwards_model->saveData('stockInventory',$insertData);
			}
			$this->Inwards_model->updateData('GateMaster',['status'=>4],['GateINID'=>$GateINID]);

			if(!empty($row['qc'])){
				foreach($row['qc'] as $qc){
					$multiData = [
						'PurchID'=>$inwardData['OrderID'],
						'GateINID'=>$GateINID,
						'InwardID'=>$inwardData['InwardsID'],
						'TType'=>'P',
						'ItemID'=>$row['item_id'],
						'layer_number'=>$last_id,
						'ItemParameterID'=>$qc['parameter_id'],
						'ParameterValue'=>$qc['value'],
						'EParameterValue'=>$qc['value'],
						'HParameterValue'=>$qc['value'],
						'deductionAmt'=>$qc['deductionamt'],
					];

					$whereQC = [
						'GateINID'=>$GateINID,
						'ItemID'=>$row['item_id'],
						'ItemParameterID'=>$qc['parameter_id'],
						'layer_number'=>$last_id
					];

					$checkQC = $this->Inwards_model->checkDuplicate('QCParameterValues',$whereQC);

					if($checkQC){
						$this->Inwards_model->updateData('QCParameterValues',$multiData,$whereQC);
					}
					else{
						$multiData['TransDate']=date('Y-m-d H:i:s');
						$this->Inwards_model->saveData('QCParameterValues',$multiData);
					}

				}

			}

		}

		echo json_encode([
			'success'=>true,
			'message'=>'Stack details '.($form_mode=='add'?'added':'updated').' successfully',
			'data' => $this->Inwards_model->getStackQcDetails($GateINID)

		]);
	}

	public function SaveTareWeight(){
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
		$required_fields = ['GateINID', 'tare_weight'];
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}

		$GateINID = $data['GateINID'] ?? '';
		$tare_weight = $data['tare_weight'] ?? '';
		$form_mode = $data['form_mode'] ?? 'add';
		$update_id = $data['update_id'] ?? '';

		$check = $this->validateStage($GateINID,5);
		if(!$check['success']){
			echo json_encode($check);
			die;
		}

		if($form_mode == 'add'){
			$check = $this->Quotation_model->checkDuplicate('GateMasterDetails', ['GateINID' => $GateINID, 'type' => 'TareWeight']);
			if ($check) {
				echo json_encode([
					'success' => false,
					'message' => 'Record already exists.'
				]);
				die;
			}
			$TopImgUrl = $FrontImgUrl = $SideImgUrl = '';
		}else{
			$details = $this->Inwards_model->getINDetails($update_id);
			$TopImgUrl = $details->value->TopImage ?? '';
			$FrontImgUrl = $details->value->FrontImage ?? '';
			$SideImgUrl = $details->value->SideImage ?? '';
		}

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'GateINID' => $GateINID,
			'type' => 'TareWeight'
		];
		$valueArray = ['tare_weight' => $tare_weight, 'TopImage' => $TopImgUrl, 'FrontImage' => $FrontImgUrl, 'SideImage' => $SideImgUrl];

		$img_array = ['top_image' => 'TopImage', 'front_image' => 'FrontImage', 'side_image' => 'SideImage'];
		foreach($img_array as $key => $value) {
			$attachmentUrl = null;
			if (isset($_FILES[$key]['name']) && $_FILES[$key]['name'] !== '') {
				$path = FCPATH . 'uploads/GateInDetails/';
				if (!file_exists($path)) {
					mkdir($path, 0755, true);
				}
				$config['upload_path'] = $path;
				$config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
				$config['file_name'] = $GateINID.'TareWeight'.$value.'.'.pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
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
				$attachmentUrl = 'uploads/GateInDetails/' . $file_data['file_name'];
				$valueArray[$value] = $attachmentUrl;
			}else{
				// if($form_mode == 'add'){
				// 	echo json_encode([
				// 		'success' => false,
				// 		'message' => 'Please upload '.$key
				// 	]);
				// 	die;
				// }
			}
		}
		$insertData['value'] = json_encode($valueArray);

		if ($form_mode == 'add') {
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->Inwards_model->saveData('GateMasterDetails', $insertData);
			$details = $this->Inwards_model->getINDetails($result);
		} else {
			$result = $this->Inwards_model->updateData('GateMasterDetails', $insertData, ['id' => $update_id]);
			$details = $this->Inwards_model->getINDetails($update_id);
		}
		$this->Inwards_model->updateData('GateMaster', ['status' => 5], ['GateINID' => $GateINID]);
		if ($result) {
			echo json_encode([
				'success' => true,
				'message' => 'Tare weight '.($form_mode == 'add' ? 'added' : 'updated').' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving data'
			]);
		}
	}

	public function SaveDeductionMatrix(){
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
        $required_fields = ['GateINID', 'ActualWeight', 'BagWeight', 'TotalDeduction', 'FinalRate', 'NetAmt', 'Deduction'];
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}
		$GateINID = $data['GateINID'] ?? '';
		$ActualWeight = $data['ActualWeight'] ?? '';
		$BagWeight = $data['BagWeight'] ?? '';
		$TotalDeduction = $data['TotalDeduction'] ?? '';
		$FinalRate = $data['FinalRate'] ?? '';
		$NetAmt = $data['NetAmt'] ?? '';
		$QCMatrix = $data['QCMatrix'] ?? [];
		$Deduction = $data['Deduction'] ?? [];

		$insertData = [
			'ActualWeight' 	=> $ActualWeight,
			'QCMatrix' 		=> json_encode($QCMatrix),
			'BagWeight' 	=> $BagWeight,
			'TotalDeduction' => $TotalDeduction,
			'FinalRate' 	=> $FinalRate,
			'NetAmt' 		=> $NetAmt,
			'Deduction' 	=> json_encode($Deduction)
		];

		$result = $this->Inwards_model->updateData('GateMaster', $insertData, ['GateINID' => $GateINID]);
		
		if ($result) {
			echo json_encode([
				'success' => true,
				'message' => 'Data saved successfully',
				'data' => $result
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving data'
			]);
		}
	}

	public function SaveGateOutPass(){
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
		$required_fields = ['GateINID'];
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}
		$GateINID = $data['GateINID'] ?? '';

		$check = $this->validateStage($GateINID,6);
		if(!$check['success']){
			echo json_encode($check);
			die;
		}

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'GateINID' => $GateINID,
			'type' => 'GateOut',
			'value' => json_encode(['Time' => date('Y-m-d H:i:s')])
		];

		$check = $this->Quotation_model->checkDuplicate('GateMasterDetails', ['GateINID' => $GateINID, 'type' => 'GateOut']);
		if ($check) {
			$result = $this->Inwards_model->updateData('GateMasterDetails', $insertData, ['GateINID' => $GateINID, 'type' => 'GateOut']);
			$details = $this->Inwards_model->getINDetails(['GateINID' => $GateINID, 'type' => 'GateOut']);
		}else{
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->Inwards_model->saveData('GateMasterDetails', $insertData);
			$details = $this->Inwards_model->getINDetails($result);
			$this->Inwards_model->updateData('GateMaster', ['status' => 6], ['GateINID' => $GateINID]);
		}
		
		if ($result) {
			echo json_encode([
				'success' => true,
				'message' => 'Gate out generated successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving data'
			]);
		}
	}

	public function SaveGateExit(){
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
		$required_fields = ['GateINID'];
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}
		$GateINID = $data['GateINID'] ?? '';

		$check = $this->validateStage($GateINID,7);
		if(!$check['success']){
			echo json_encode($check);
			die;
		}

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'GateINID' => $GateINID,
			'type' => 'GateExit',
			'value' => json_encode(['Time' => date('Y-m-d H:i:s')])
		];

		$check = $this->Quotation_model->checkDuplicate('GateMasterDetails', ['GateINID' => $GateINID, 'type' => 'GateExit']);
		if ($check) {
			$result = $this->Inwards_model->updateData('GateMasterDetails', $insertData, ['GateINID' => $GateINID, 'type' => 'GateExit']);
			$details = $this->Inwards_model->getINDetails(['GateINID' => $GateINID, 'type' => 'GateExit']);
		}else{
			$insertData['TransDate'] = date('Y-m-d H:i:s');
			$result = $this->Inwards_model->saveData('GateMasterDetails', $insertData);
			$details = $this->Inwards_model->getINDetails($result);
			$this->Inwards_model->updateData('GateMaster', ['status' => 7], ['GateINID' => $GateINID]);
		}
		
		if ($result) {
			echo json_encode([
				'success' => true,
				'message' => 'Vehicle exit gate saved successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving data'
			]);
		}
	}

	/* =========================
		* HEAD QC Details
		* ========================= */
	public function HeadQC($gatein_no){
		$data['title'] = 'Inwards Master';
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

		$this->load->view('admin/HeadQC/Head_qc', $data);
	}

	public function SaveHeadQCDetails(){
		if (empty($this->input->post())) {
			echo json_encode(['success'=>false,'message'=>'No data received']);
			return;
		}

		$PlantID = $this->session->userdata('root_company');
		$UserID  = $this->session->userdata('username');
		$FY      = $this->session->userdata('finacial_year');

		$data = $this->input->post(null,true);

		foreach ($data as $k=>$v){
			if(!is_array($v)) $data[$k] = trim($v);
		}

		if(empty($data['GateINID'])){
			echo json_encode(['success'=>false,'message'=>'GateINID required']);
			return;
		}

		$GateINID = $data['GateINID'];
		$form_mode = $data['form_mode'] ?? 'add';
		$id = $data['id'] ?? [];
		$hvalue = $data['value'] ?? [];
		$deductionAmt = $data['deductionAmt'] ?? [];
		
		if(empty($id) || empty($hvalue) || empty($deductionAmt)){
			echo json_encode(['success'=>false,'message'=>'QC details missing']);
			return;
		}

		// $check = $this->validateStage($GateINID,4);
		// if(!$check['success']){
		// 	echo json_encode($check);
		// 	die;
		// }

		for($i=0; $i<count($id); $i++){
			$multiData = [
				'HParameterValue'=>$hvalue[$i],
				'deductionAmt'=>$deductionAmt[$i],
			];
			$checkQC = $this->Inwards_model->checkDuplicate('QCParameterValues', ['GateINID'=>$GateINID,'id'=>$id[$i]]);
			if($checkQC){
				$this->Inwards_model->updateData('QCParameterValues',$multiData, ['id' => $id[$i]]);
			}
		}
		
		echo json_encode([
			'success'=>true,
			'message'=>'Head QC '.($form_mode=='add'?'added':'updated').' successfully',
			'data' => $this->Inwards_model->getStackQcDetails($GateINID)

		]);
	}
	/* =========================
		* REPORTS PAGE
		* ========================= */
	public function Reports(){
		if (!has_permission_new('PurchaseInwardList', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Inwards Reports';
		$data['vendor_list'] = $this->Inwards_model->getVendorDropdownByInwards();

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->Quotation_model->get_company_detail($selected_company);

		$this->load->view('admin/Inwards/InwardsReports', $data);
	}

  public function ListFilter(){
    if ($this->input->post()) {
			$data = $this->input->post(null, true);
      
      $limit  = $data['limit'] ?? 100;
      $offset = $data['offset'] ?? 0;
      if($data['from_date']){
        $data['from_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['from_date'])));
      }else{
        $data['from_date'] = date('Y-m-01');
      }
      
      if($data['to_date']){
        $data['to_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['to_date'])));
      }else{
        $data['to_date'] = date('Y-m-d');
      }
      
      $result  = $this->Inwards_model->getListByFilter($data, $limit, $offset);
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
		}else{
			echo json_encode([
				'success' => false,
				'message' => 'Invalid request',
        'total'   => 0,
        'rows'    => []
			]);
		}
  }

  public function ListExportExcel(){
		if (!has_permission_new('PurchaseInwardList', '', 'export')) { access_denied('Access Denied'); }
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
      'Inward No'  				=> 'string',
      'Inward Date'  			=> 'string',
      'Vehicle No'  			=> 'string',
      'Vendor Name'     	=> 'string',
      'Broker Name'     	=> 'string',
      'Vehicle Loaded Wt'	=> 'string',
      'Item Wt'    				=> 'string',
      'Vehicle Empty Wt'  => 'string',
      'Status'          	=> 'string'
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
      $result = $this->Inwards_model->getListByFilter($post, $limit, $offset);
      if(empty($result['rows'])){
        break;
      }

      foreach($result['rows'] as $row){
        $writer->writeSheetRow($sheetName, [
          $row['InwardsID'] ?? '',
          date('d/m/Y', strtotime($row['TransDate'])) ?? '',
					'-',
          $row['vendor_name'] ?? '' . ' (' . $row['AccountID'] ?? '' . ')',
          $row['broker_name'] ?? '' . ' (' . $row['BrokerID'] ?? '' . ')',
          '-',
          $row['TotalWeight']/100 ?? '',
          '-',
          $status_list[$row['Status'] ?? 1] ?? 'Pending'
        ]);
      }

      $offset += $limit;
      unset($result);
    }

    // ===== SAVE FILE =====
    $filename = 'InwardList_'.date('YmdHis').'.xlsx';
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

	/* =========================
		* ADD / EDIT ASN
		* ========================= */
	public function ASN(){
		$data['title'] = 'Inwards Master';
		$data['vendor_list'] = $this->Inwards_model->getVendorDropdownByASN();
		
		$selected_company = $this->session->userdata('root_company');
    $data['company_detail'] = $this->Quotation_model->get_company_detail($selected_company);

		$this->load->view('admin/Inwards/GenerateASN', $data);
	}

	public function getVendorDetailsASN(){
		$vendor_id = $this->input->post('vendor_id');
		if (!$vendor_id) {
			echo json_encode(['success' => false, 'message' => 'No vendor id']);
			return;
		}
		$vendor_details = $this->Quotation_model->getVendorDetailByAccountID($vendor_id);
		$order_list = $this->Inwards_model->getVendorPurchaseOrderASN($vendor_id);
		
		echo json_encode(['success' => true, 'data' => $vendor_details, 'order_list' => $order_list]);
	}


}
