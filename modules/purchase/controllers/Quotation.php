<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotation extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Quotation_model');
		$this->load->model('purchase_model');
	}

	/* =========================
		* ADD / EDIT PAGE
		* ========================= */
	public function index(){
		$data['title'] = 'Quotation Master';
		$data['item_type'] = $this->Quotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
		$data['FreightTerms'] = $this->purchase_model->get_freight_terms();
		$data['purchaselocation'] = $this->purchase_model->get_purchase_location();
		$data['quotation_list'] = $this->Quotation_model->getQuotationList();
    $selected_company = $this->session->userdata('root_company');
    $data['company_detail'] = $this->Quotation_model->get_company_detail($selected_company);
    $data['quotation_category'] = $this->Quotation_model->get_quotation_category();
    
		$this->load->view('admin/Quotation/QuotationAddEdit', $data);
	}

	public function getNextQuotationNo(){
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');

		$category_id = $this->input->post('category_id');
		if (!$category_id) {
			echo json_encode(['success' => false, 'message' => 'No category id']);
			return;
		}
		$quote_count = $this->Quotation_model->getNextQuotationNoByCategory($category_id);
		$item_list = $this->Quotation_model->getDropdown('items', 'ItemId, ItemName', ['ItemCategoryCode' => $category_id, 'IsActive' => 'Y'], 'ItemName', 'ASC') ?? [];
    	$quote_no = 'PQ'.$FY.$PlantID.$quote_count;

		echo json_encode(['success' => true, 'quote_no' => $quote_no, 'item_list' => $item_list]);
	}

	public function getVendorDetailsLocation(){
		$vendor_id = $this->input->post('vendor_id');
		if (!$vendor_id) {
			echo json_encode(['success' => false, 'message' => 'No vendor id']);
			return;
		}
		$vendor_details = $this->Quotation_model->getVendorDetailByAccountID($vendor_id);
		$location_details = $this->purchase_model->getShippingDatacity($vendor_id);
		$broker_list = $this->Quotation_model->getVendorBrokerList($vendor_id);
		$locations = array();
		if (!empty($location_details)) {
			foreach ($location_details as $location) {
				$locations[] = array(
					'id' => $location['id'],
					'city' => $location['city_name'] ?? '',
				);
			}
		}
		echo json_encode(['success' => true, 'data' => $vendor_details, 'location' => $locations, 'broker_list' => $broker_list]);
	}

	public function SaveQuotation(){
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
		$required_fields = ['item_type', 'item_category', 'quotation_no', 'quotation_date', 'purchase_location', 'vendor_id', 'vendor_location', 'broker_id', 'payment_terms', 'freight_terms'];
		
		foreach ($required_fields as $key => $value) {
			if (empty($data[$value])) {
				echo json_encode([
					'success' => false,
					'message' => 'Please fill '.$value.' fields'
				]);
				die;
			}
		}

		$item_type = $data['item_type'] ?? '';
		$item_category = $data['item_category'] ?? '';
		$quotation_no = $data['quotation_no'] ?? '';
		if($data['quotation_date']){
			$quotation_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['quotation_date'])));
		}else{
			$quotation_date = date('Y-m-d');
		}
		$purchase_location = $data['purchase_location'] ?? '';
		$vendor_id = $data['vendor_id'] ?? '';
		$vendor_gst_no = $data['vendor_gst_no'] ?? '';
		$vendor_country = $data['vendor_country'] ?? '';
		$vendor_state = $data['vendor_state'] ?? '';
		$vendor_address = $data['vendor_address'] ?? '';
		$vendor_location = $data['vendor_location'] ?? '';
		$broker_id = $data['broker_id'] ?? '';
		if($data['delivery_from']){
			$delivery_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_from'])));
		}else{
			$delivery_from = date('Y-m-d');
		}
		if($data['delivery_to']){
			$delivery_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['delivery_to'])));
		}else{
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

		if($form_mode == 'add'){
		// 	$check = $this->Quotation_model->checkDuplicate('PurchQuotationMaster', ['QuotatioonID' => $quotation_no]);
		// 	if ($check) {
		// 		echo json_encode([
		// 			'success' => false,
		// 			'message' => 'Quotation number already exists.'
		// 		]);
		// 		die;
		// 	}
			$quote_count = $this->Quotation_model->getNextQuotationNoByCategory($item_category);
			$quotation_no = 'PQ'.$FY.$PlantID.$quote_count;
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

		// --- quotation_date check ---
		if ($quotation_date < $fy_start || $quotation_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Quotation Date (' . date('d/m/Y', strtotime($quotation_date)) . ') is outside the allowed financial year range ('
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

		// --- delivery_to check (allows up to FY end for future scheduling) ---
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

		$insertData = [
			'PlantID' => $PlantID,
			'FY' => $FY,
			'PurchaseLocation' => $purchase_location,
			'QuotatioonID' => $quotation_no,
			'TransDate' => $quotation_date,
			'ItemType' => $item_type,
			'ItemCategory' => $item_category,
			'AccountID' => $vendor_id,
			'BrokerID' => $broker_id,
			'DeliveryLocation' => $vendor_location,
			'DeliveryFrom' => $delivery_from,
			'DeliveryTo' => $delivery_to,
			'PaymentTerms' => $payment_terms,
			'FreightTerms' => $freight_terms,
			'GSTIN' => $vendor_gst_no,
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
			$insertData['TransDate2'] = date('Y-m-d H:i:s');
			$result = $this->Quotation_model->saveData('PurchQuotationMaster', $insertData);
			$details = $this->Quotation_model->getQuoteDetails($result);
		} else {
			$result = $this->Quotation_model->updateData('PurchQuotationMaster', $insertData, ['id' => $update_id]);
			$details = $this->Quotation_model->getQuoteDetails($update_id);
		}
		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'quotation_no' => $quotation_no,
				'quotation_date' => $quotation_date,
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
				'TType2' => 'Quotation'
			];
			$this->Quotation_model->saveMultiData($multi_insert_data);

			echo json_encode([
				'success' => true,
				'message' => 'Quotation '.($form_mode == 'add' ? 'created' : 'updated').' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving quotation'
			]);
		}
	}

	public function GetQuotationDetails(){
		$id = $this->input->post('id');
		$data = $this->Quotation_model->getQuoteDetails($id);
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
  
  /* =========================
		* LIST PAGE
		* ========================= */
	public function List(){
		$data['title'] = 'Quotation List';
    $data['vendor_list'] = $this->Quotation_model->getVendorDropdown();

    $selected_company = $this->session->userdata('root_company');
    $data['company_detail'] = $this->Quotation_model->get_company_detail($selected_company);

		$this->load->view('admin/Quotation/QuotationList', $data);
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
      
      $result  = $this->Quotation_model->getListByFilter($data, $limit, $offset);
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

    $sheetName = 'Quotation List';
    $writer = new XLSXWriter();

    $header = [
      'Quotation Code'  => 'string',
      'Quotation Date'  => 'string',
      'Category'     		=> 'string',
      'Vendor Name'     => 'string',
      'Broker Name'     => 'string',
      'Total Wt' => 'string',
      'Total Qty' => 'string',
      'Item Total' => 'string',
      'Total Disc' => 'string',
      'Taxable Amt' => 'string',
      'CGST Amt' => 'string',
      'SGST Amt' => 'string',
      'IGST Amt' => 'string',
      'Round Off' => 'string',
      'Net Amt' => 'string',
      'Order Wt' => 'string',
      'Status' => 'string'
    ];

    $writer->writeSheetHeader($sheetName, $header, ['suppress_row'=>true]);

    $selected_company = $this->session->userdata('root_company');
    $company_detail   = $this->Quotation_model->get_company_detail($selected_company);

    // ===== COMPANY NAME ROW =====
    $writer->markMergedCell($sheetName, 0, 0, 0, 16);
    $writer->writeSheetRow($sheetName, [$company_detail->company_name]);

    // ===== COMPANY ADDRESS ROW =====
    $writer->markMergedCell($sheetName, 1, 0, 1, 16);
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

    $writer->markMergedCell($sheetName, 2, 0, 2, 16);
    $writer->writeSheetRow($sheetName, [$reportedBy]);
    $writer->writeSheetRow($sheetName, []);

    // ===== HEADER ROW =====
    $writer->writeSheetRow($sheetName, array_keys($header));

    // ===== CHUNK FETCH START =====
    $limit = 100;
    $offset = 0;

    while(true){
      $result = $this->Quotation_model->getListByFilter($post, $limit, $offset);
      if(empty($result['rows'])){
        break;
      }

      foreach($result['rows'] as $row){
        $writer->writeSheetRow($sheetName, [
          $row['QuotatioonID'] ?? '',
          date('d/m/Y', strtotime($row['TransDate'])) ?? '',
          $row['category_name'] ?? '',
          $row['vendor_name'] ?? '' . ' (' . $row['AccountID'] ?? '' . ')',
          $row['broker_name'] ?? '' . ' (' . $row['BrokerID'] ?? '' . ')',
          $row['TotalWeight']/100 ?? '',
        	$row['TotalQuantity'] ?? '',
        	$row['ItemAmt'] ?? '',
        	$row['DiscAmt'] ?? '',
        	$row['TaxableAmt'] ?? '',
        	$row['CGSTAmt'] ?? '',
        	$row['SGSTAmt'] ?? '',
        	$row['IGSTAmt'] ?? '',
        	$row['RoundOffAmt'] ?? '',
          $row['NetAmt'] ?? '',
          $row['po_total_weight']/100 ?? '',
          $status_list[$row['Status'] ?? 1] ?? 'Pending'
        ]);
      }

      $offset += $limit;
      unset($result);
    }

    // ===== SAVE FILE =====
    $filename = 'QuotationList_'.date('YmdHis').'.xlsx';
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
