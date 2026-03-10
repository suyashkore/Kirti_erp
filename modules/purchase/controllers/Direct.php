<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Direct extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Quotation_model');
		$this->load->model('Orders_model');
		$this->load->model('purchase_model');
    $this->load->model('DirectPurchase_model');
	}

	/* =========================
		* ADD / EDIT PAGE
		* ========================= */
	public function index(){
		$data['title'] = 'Direct Purchase';
		$data['FY'] = $this->session->userdata('finacial_year');
    $data['po_no'] = $this->DirectPurchase_model->getNextDPONo();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
    $data['item_type'] = $this->Quotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
    $data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
    $data['company_detail'] = $this->Quotation_model->get_company_detail();
    $data['tds_list'] = $this->Quotation_model->getDropdown('TDSMaster', 'id, TDSCode, TDSName', ['Blocked' => 'N'], 'TDSName', 'ASC');
    
		$this->load->view('admin/Direct/DirectPurchaseAddEdit', $data);
	}

  public function GetTDSDetailsByCode(){
    $tds_code = $this->input->post('tds_code');
		if (!$tds_code) {
			echo json_encode(['success' => false, 'message' => 'TDS not found']);
			return;
		}
    $tds_per_list = $this->Quotation_model->getDropdown('TDSDetails', 'id, description, rate', ['TDSCode' => $tds_code], 'description', 'ASC');
		echo json_encode(['success' => true, 'message' => 'TDS found', 'per_list' => $tds_per_list]);
  }

  public function GetGodownListByLocation(){
    $location_id = $this->input->post('location_id');
    if (!$location_id) {
      echo json_encode(['success' => false, 'message' => 'Location not found']);
      return;
    }
    $godown_list = $this->Quotation_model->getDropdown('godownmaster', 'id, GodownCode, GodownName', ['LocationID' => $location_id], 'GodownName', 'ASC');
    echo json_encode(['success' => true, 'message' => 'Location found', 'godown_list' => $godown_list]);
  }

  public function GetItemListByType(){
    $item_type = $this->input->post('item_type');
    if (!$item_type) {
      echo json_encode(['success' => false, 'message' => 'Item type not found']);
      return;
    }
    $item_list = $this->Quotation_model->getDropdown('items', 'id, ItemID, ItemName', ['ItemTypeID' => $item_type], 'ItemName', 'ASC');
    echo json_encode(['success' => true, 'message' => 'Item type found', 'item_list' => $item_list]);
  }

  public function GetItemDetailsById(){
		$item_id = $this->input->post('item_id');
		if (!$item_id) {
			echo json_encode(['success' => false, 'message' => 'Item not found']);
			return;
		}
		$itemData = $this->Quotation_model->getItemDetailsById($item_id);
    echo json_encode(['success' => true, 'message' => 'Item found', 'data' => $itemData]);
	}

  public function SaveDirectPurchase(){
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
		$required_fields = ['center_location', 'warehouse_id', 'purchase_type', 'vendor_id', 'vendor_state', 'payment_terms'];

    if($data['form_mode'] == 'edit'){
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

    $form_mode = $data['form_mode'] ?? 'add';
    $update_id = $data['update_id'] ?? '';
    $po_no = $data['po_no'] ?? '';
    if($data['order_date']){
			$order_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['order_date'])));
		}else{
			$order_date = date('Y-m-d');
		}
    $center_location = $data['center_location'] ?? '';
    $warehouse_id = $data['warehouse_id'] ?? '';
    $purchase_type = $data['purchase_type'] ?? '';
    $payment_terms = $data['payment_terms'] ?? '';
    $vendor_id = $data['vendor_id'] ?? '';
    $vendor_gst = $data['vendor_gst'] ?? '';
    $vendor_state = $data['vendor_state'] ?? '';
    $closing_balance = $data['closing_balance'] ?? '';
    $broker_id = $data['broker_id'] ?? '';
    $vendor_doc_no = $data['vendor_doc_no'] ?? '';
    if($data['vendor_doc_date']){
			$vendor_doc_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['vendor_doc_date'])));
		}else{
			$vendor_doc_date = date('Y-m-d');
		}
    $vendor_doc_amt = $data['vendor_doc_amt'] ?? '';
    $tds_id = $data['tds_id'] ?? '';
    $tds_percentage = $data['tds_percentage'] ?? '';
    $ledger_group_id = $data['ledger_group_id'] ?? '';
    $ledger_id = $data['ledger_id'] ?? '';
    $purchase_amount = $data['purchase_amount'] ?? 0;
    $discount_amount = $data['discount_amount'] ?? 0;
    $total_cgst_amount = $data['total_cgst_amount'] ?? 0;
    $total_sgst_amount = $data['total_sgst_amount'] ?? 0;
    $total_igst_amount = $data['total_igst_amount'] ?? 0;
    $total_freight_amount = $data['total_freight_amount'] ?? 0;
    $other_amount = $data['other_amount'] ?? 0;
    $roundoff_amount = $data['roundoff_amount'] ?? 0;
    $tds_amount = $data['tds_amount'] ?? 0;
    $final_amount = $data['final_amount'] ?? 0;

		if($form_mode == 'add'){
			$po_no = $this->DirectPurchase_model->getNextDPONo();
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

		// --- vendor_doc_date check ---
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


		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'OrderID' => $po_no,
      'OrderDate' => $order_date,
      'CenterLocation' => $center_location,
      'WarehouseID' => $warehouse_id,
      'ItemType' => $purchase_type,
      'PaymentTerms' => $payment_terms,
      'AccountID' => $vendor_id,
      'BrokerID' => $broker_id,
      'GSTIN' => $vendor_gst,
      'state' => $vendor_state,
      'ClosingBalance' => $closing_balance,
      'VendorDocNo' => $vendor_doc_no,
      'VendorDocDate' => $vendor_doc_date,
      'VendorDocAmt' => $vendor_doc_amt,
      'TDSCode' => $tds_id,
      'TDSRate' => $tds_percentage,
      'LeaderGroupID' => $ledger_group_id,
      'LeaderID' => $ledger_id,
      'PurchaseAmt' => $purchase_amount,
      'DiscAmt' => $discount_amount,
      'CGSTAmt' => $total_cgst_amount,
      'SGSTAmt' => $total_sgst_amount,
      'IGSTAmt' => $total_igst_amount,
      'FreightAmt' => $total_freight_amount,
      'OtherAmt' => $other_amount,
      'RoundOff' => $roundoff_amount,
      'TDSAmt' => $tds_amount,
      'FinalAmt' => $final_amount
		];

		if ($form_mode == 'add') {
			$result = $this->DirectPurchase_model->saveData('DirectPurchaseMaster', $insertData);
			$details = $this->DirectPurchase_model->getDPODetails($result);
		} else {
			$result = $this->DirectPurchase_model->updateData('DirectPurchaseMaster', $insertData, ['id' => $update_id]);
			$details = $this->DirectPurchase_model->getDPODetails($update_id);
		}

		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'po_no' => $po_no,
				'order_date' => $order_date,
				'vendor_id' => $vendor_id,
				'vendor_state' => $vendor_state,
				'item_uid' => $data['item_uid'] ?? [],
				'item_id' => $data['item_id'] ?? [],
				'item_group' => $data['item_group'] ?? [],
				'hsn_code' => $data['hsn_code'] ?? [],
				'unit_rate' => $data['unit_rate'] ?? [],
				'unit_weight' => $data['unit_weight'] ?? [],
				'disc_amt' => $data['disc_amt'] ?? [],
				'uom' => $data['uom'] ?? [],
				'quantity' => $data['min_qty'] ?? [],
				'amount' => $data['amount'] ?? [],
				'gst' => $data['gst'] ?? [],
        'cgst' => $data['cgst'] ?? [],
        'sgst' => $data['sgst'] ?? [],
        'igst' => $data['igst'] ?? [],
				'TType' => 'P',
				'TType2' => 'Direct'
			];
			$this->DirectPurchase_model->saveMultiData($multi_insert_data);

			echo json_encode([
				'success' => true,
				'message' => 'Direct purchase '.($form_mode == 'add' ? 'created' : 'updated').' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving order'
			]);
		}
  }

  public function GetDirectOrderDetails(){
		$id = $this->input->post('id');
		$data = $this->DirectPurchase_model->getDPODetails($id);
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
  
	public function ListFilter(){
    if ($this->input->post()) {
			$data = $this->input->post(null, true);
      
      $limit  = $data['limit'] ?? 100;
      $offset = $data['offset'] ?? 0;
      if($data['from_date']){
        $data['from_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['from_date']))).' 00:00:00';
      }else{
        $data['from_date'] = date('Y-m-01 00:00:00');
      }
      
      if($data['to_date']){
        $data['to_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data['to_date']))).' 23:59:59';
      }else{
        $data['to_date'] = date('Y-m-d 23:59:59');
      }
      
      $result  = $this->DirectPurchase_model->getListByFilter($data, $limit, $offset);
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
  
  public function PrintPDF($OrderID){
		$data = $this->DirectPurchase_model->getDPODetailsPrint($OrderID);
		if(!$data){
			redirect(admin_url('purchase/Direct'));
		}
		$invoice = [
			'invoice' => $data
		];

		try {
			$pdf = DirectPurchaseOrder_pdf($invoice);
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
			
		$pdf->Output(mb_strtoupper(slug_it($OrderID)) . '-DirectOrderSlip.pdf', $type);
	}
	
	public function List(){
		$data['title'] = 'Direct Purchase Order List';
		$data['FY'] = $this->session->userdata('finacial_year');
    $data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
    $data['company_detail'] = $this->Quotation_model->get_company_detail();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
    $data['item_type'] = $this->Quotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');

		$this->load->view('admin/Direct/DirectPurchaseList', $data);
	}

	public function ListExportExcel(){
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

    $sheetName = 'Direct Purchase Order List';
    $writer = new XLSXWriter();

    $header = [
			'Order No' => 'string',
			'Order Date' => 'string',
			'Center Location' => 'string',
			'Warehouse' => 'string',
			'Vendor' => 'string',
			'Broker' => 'string',
			'Type' => 'string',
			'TDS Section' => 'string',
			'TDS Rate' => 'string',
			'Total Amt' => 'string',
			'Disc Amt' => 'string',
			'CGST Amt' => 'string',
			'SGST Amt' => 'string',
			'IGST Amt' => 'string',
			'Freight Amt' => 'string',
			'Other Amt' => 'string',
			'Round Off' => 'string',
			'TDS Amt' => 'string',
			'Final Amt' => 'string',
		];

    $writer->writeSheetHeader($sheetName, $header, ['suppress_row'=>true]);

    $selected_company = $this->session->userdata('root_company');
    $company_detail   = $this->Quotation_model->get_company_detail($selected_company);

    // ===== COMPANY NAME ROW =====
    $writer->markMergedCell($sheetName, 0, 0, 0, 18);
    $writer->writeSheetRow($sheetName, [$company_detail->company_name]);

    // ===== COMPANY ADDRESS ROW =====
    $writer->markMergedCell($sheetName, 1, 0, 1, 18);
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
    
    $center_location = $post['center_location'] ?? '';
    $warehouse_id = $post['warehouse_id'] ?? '';
    $purchase_type = $post['purchase_type'] ?? '';
    $vendor_id = $post['vendor_id'] ?? '';
    $broker_id = $post['broker_id'] ?? '';

    if($from_date != ''){
      $reportedBy .= 'From Date : ' . date('d/m/Y', strtotime($from_date)) . ', ';
    }

    if($to_date != ''){
      $reportedBy .= 'To Date : ' . date('d/m/Y', strtotime($to_date)) . ', ';
    }

		if($center_location != ''){
			$reportedBy .= 'Center Location : ' .( $this->DirectPurchase_model->getData('PlantLocationDetails', 'LocationName', ['id' => $center_location])['LocationName'] ?? '') . ', ';
		}

		if($warehouse_id != ''){
			$reportedBy .= 'Warehouse : ' .( $this->DirectPurchase_model->getData('godownmaster', 'GodownName', ['id' => $warehouse_id])['GodownName'] ?? '') . ', ';
		}

		if($purchase_type != ''){
			$reportedBy .= 'Purchase Type : ' .( $this->DirectPurchase_model->getData('ItemTypeMaster', 'ItemTypeName', ['id' => $purchase_type])['ItemTypeName'] ?? '') . ', ';
		}

    if($vendor_id != ''){
      $reportedBy .= 'Vendor : ' .( $this->DirectPurchase_model->getData('clients', 'company', ['AccountID' => $vendor_id])['company'] ?? '') . ', ';
    }

    if($broker_id != ''){
      $reportedBy .= 'Broker : ' .( $this->DirectPurchase_model->getData('clients', 'company', ['AccountID' => $broker_id])['company'] ?? '') . ', ';
    }

    $writer->markMergedCell($sheetName, 2, 0, 2, 18);
    $writer->writeSheetRow($sheetName, [$reportedBy]);
    $writer->writeSheetRow($sheetName, []);

    // ===== HEADER ROW =====
    $writer->writeSheetRow($sheetName, array_keys($header));

    // ===== CHUNK FETCH START =====
    $limit = 100;
    $offset = 0;

    while(true){
      $result = $this->DirectPurchase_model->getListByFilter($post, $limit, $offset);
      if(empty($result['rows'])){
        break;
      }

      foreach($result['rows'] as $row){
        $writer->writeSheetRow($sheetName, [
					$row['OrderID'] ?? '',
					date('d/m/Y', strtotime($row['OrderDate'])) ?? '',
					$row['LocationName'] ?? '',
					$row['GodownName'] ?? '',
					$row['VendorName'] ?? '',
					$row['BrokerName'] ?? '',
					$row['ItemTypeName'] ?? '',
					$row['TDSName'] ?? '',
					$row['TDSRate'] ?? '',
					$row['PurchaseAmt'] ?? '',
					$row['DiscAmt'] ?? '',
					$row['CGSTAmt'] ?? '',
					$row['SGSTAmt'] ?? '',
					$row['IGSTAmt'] ?? '',
					$row['FreightAmt'] ?? '',
					$row['OtherAmt'] ?? '',
					$row['RoundOff'] ?? '',
					$row['TDSAmt'] ?? '',
					$row['FinalAmt'] ?? ''
        ]);
      }

      $offset += $limit;
      unset($result);
    }

    // ===== SAVE FILE =====
    $filename = 'DirectPurchaseOrderList_'.date('YmdHis').'.xlsx';
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

}