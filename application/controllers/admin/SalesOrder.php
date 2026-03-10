<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SalesOrder extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('SalesQuotation_model');
		$this->load->model('SalesOrder_model');

	}

	/* =========================
	* View Sales Order Print
	* ========================= */

    public function SalesOrderPrint($OrderID)
    {
        if (!$OrderID) {
            redirect($this->load->view('admin/SalesOrder/SalesOrderAddEdit'));
        }
        
        if (!has_permission_new('CashOrderList', '', 'view')) {
            access_denied('Invoices');
        }
        $invoice = [];
        $invoice1  = $this->SalesOrder_model->GetSalesOrderDetailsForPdf($OrderID);
      	$history  = $this->SalesOrder_model->get_order_data($OrderID);
    
    	$invoice = [
        	'invoice' => $invoice1,
        	'history' => $history
    	];
        try {
            $pdf = SalesOrder_pdf($invoice);
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
	public function index(){
		$data['title'] = 'Sales Order';
		$data['item_type'] = $this->SalesQuotation_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['customer_list'] = $this->SalesQuotation_model->getCustomerDropdown();
		$data['category_list'] = $this->SalesOrder_model->getCategoryDropdown();
		$data['FreightTerms'] = $this->SalesQuotation_model->get_freight_terms();
		$data['saleslocation'] = $this->SalesQuotation_model->get_sales_location();
		$data['order_list'] = $this->SalesOrder_model->getOrderList();
    	
		$selected_company = $this->session->userdata('root_company');
    	$data['company_detail'] = $this->SalesQuotation_model->get_company_detail($selected_company);

    
		$this->load->view('admin/SalesOrder/SalesOrderAddEdit', $data);
	}

	public function getNextSalesOrderNo()
	{
		$category_id = $this->input->post('category_id');
		if (!$category_id) {
			echo json_encode(['success' => false, 'message' => 'No category id']);
			return;
		}
		$order_no = $this->SalesOrder_model->getNextSalesOrderNoByCategory($category_id);
		$item_list = $this->SalesOrder_model->getDropdown('items', 'ItemId, ItemName', ['ItemCategoryCode' => $category_id, 'IsActive' => 'Y'], 'ItemName', 'ASC') ?? [];

		echo json_encode(['success' => true, 'Order_no' => $order_no, 'item_list' => $item_list]);
	}

	public function getCustomerDetailsLocation(){
		$customer_id = $this->input->post('customer_id');

		if (!$customer_id) {
			echo json_encode(['success' => false, 'message' => 'No customer id']);
			return;
		}
		$customer_details = $this->SalesOrder_model->getCustomerDetailByAccountID($customer_id);
		$location_details = $this->SalesOrder_model->getShippingDatacity($customer_id);
		$broker_list = $this->SalesOrder_model->getCustomerBrokerList($customer_id);
		$quotation_list = $this->SalesOrder_model->getCustomerQuotationList($customer_id);

		$locations = array();
		if (!empty($location_details)) {
			foreach ($location_details as $location) {
				$locations[] = array(
					'id' => $location['id'],
					'city' => $location['city_name'] ?? '',
				);
			}
		}
		echo json_encode(['success' => true, 'data' => $customer_details, 'location' => $locations, 'broker_list' => $broker_list, 'quotation_list' => $quotation_list]);
	}

	public function GetItemDetails()
	{
		$item_id = $this->input->post('item_id');

		if (!$item_id) {
			echo json_encode(['status' => 'error', 'data' => null]);
			return;
		}

		$itemData = $this->SalesOrder_model->getItemDetailsById($item_id);

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


	public function SaveSalesOrder(){
		if (empty($this->input->post())) {
			echo json_encode(['success' => false, 'message' => 'No data received']);
			return;
		}


		

		// echo json_encode(print_r($this->input->post()));
		// exit;

		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$UserID = $this->session->userdata('username');
		$data = $this->input->post(null, true);
		$field_names = array_keys($data);
		foreach ($field_names as $key => $value) {
			if(!is_array($data[$value])) $data[$value] = trim($data[$value]);
		}
		$required_fields = ['item_type', 'item_category', 'OrderID', 'order_date', 'sales_location', 'customer_id', 'customer_location', 'broker_id', 'payment_terms', 'freight_terms'];
		
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
		$OrderID = $data['OrderID'] ?? '';
		if($data['order_date']){
			$order_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['order_date'])));
		}else{
			$order_date = date('Y-m-d');
		}
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
		$sales_location = $data['sales_location'] ?? '';
		$customer_id = $data['customer_id'] ?? '';
		$GSTIN = $data['GSTIN'] ?? '';
		$customer_country = $data['customer_country'] ?? '';
		$customer_state = $data['customer_state'] ?? '';
		$customer_address = $data['customer_address'] ?? '';
		$customer_location = $data['customer_location'] ?? '';
		$broker_id = $data['broker_id'] ?? '';
		$quotation_id = $data['quotation_id'] ?? '';
		
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
			$check = $this->SalesOrder_model->checkDuplicate('SalesOrderMaster', ['OrderID' => $OrderID]);
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

		// --- order_date check ---
		if ($order_date < $fy_start || $order_date > $max_txn_date) {
			echo json_encode([
				'success' => false,
				'message' => 'Order Date (' . date('d/m/Y', strtotime($order_date)) . ') is outside the allowed financial year range ('
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
			'SalesLocation' => $sales_location,
			'OrderID' => $OrderID,
			'TransDate' => $order_date,
			'DeliveryFrom' => $delivery_from,
			'DeliveryTo' => $delivery_to,
			'ItemType' => $item_type,
			'ItemCategory' => $item_category,
			'AccountID' => $customer_id,
			'BrokerID' => $broker_id,
			'QuotationID' => $quotation_id,
			'DeliveryLocation' => $customer_location,
			
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
			$insertData['TransDate2'] = date('Y-m-d H:i:s');
			$result = $this->SalesOrder_model->saveData('SalesOrderMaster', $insertData);
			$details = $this->SalesOrder_model->getOrderDetails($result);
		} else {
			$result = $this->SalesOrder_model->updateData('SalesOrderMaster', $insertData, ['id' => $update_id]);
			$details = $this->SalesOrder_model->getOrderDetails($update_id);
		}

		if (!empty((isset($quotation_id)))) {
			$quote_no = $quotation_id;
			$this->db->where('QuotationID', $quote_no);
			$this->db->update('tblSalesQuotationMaster', ['Status' => 6]);
		}

		if ($result) {
			$multi_insert_data = [
				'plant_id' => $PlantID,
				'fy' => $FY,
				'user_id' => $UserID,
				'OrderID' => $OrderID,
				'order_date' => $order_date,
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
			if (empty($data['item_id']) || !is_array($data['item_id'])) {
				echo json_encode([
					'success' => false,
					'message' => 'No item data found'
				]);
				return;
			}


			$this->SalesOrder_model->saveMultiData($multi_insert_data);

			echo json_encode([
				'success' => true,
				'message' => 'Sales Order ' . ($form_mode == 'add' ? 'created' : 'updated') . ' successfully',
				'data' => $details
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Error while saving Order'
			]);
		}
	}

// 	public function getQuotationDetails()
// {
//     $quotation_id = $this->input->post('quotation_id');

//     $this->load->model('SalesOrder_model');

//     $data = $this->SalesOrder_model->getQuotationFullDetails($quotation_id);

//     echo json_encode([
//         'success'        => true,
//         // 'item_type'      => $data['master']->ItemType,
//         // 'item_category'  => $data['master']->CategoryID,
//         'items'          => $data['master']
//     ]);
// }



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

public function GetHistoryDetails()
{
    $OrderID = $this->input->post('OrderID');

    $items = $this->db
        ->where('OrderID', $OrderID)
        ->get('tblhistory')
        ->result_array();

    if ($items) {
        echo json_encode([
            'success' => true,
            'items' => $items
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Items not found'
        ]);
    }
}



	public function GetSalesOrderDetails(){
		$id = $this->input->post('id');
		$data = $this->SalesOrder_model->getOrderDetails($id);
		if($data){
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
		$data['title'] = 'Sales Order List';
		$data['customer_list'] = $this->SalesQuotation_model->getCustomerDropdown();

		$selected_company = $this->session->userdata('root_company');
		$data['company_detail'] = $this->SalesQuotation_model->get_company_detail($selected_company);

		$this->load->view('admin/SalesOrder/SalesOrderList', $data);
	}
	
	public function OrderList()
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model('SalesOrder_model');

		$order_list = $this->SalesOrder_model->getOrderList();

		echo json_encode([
			'success' => true,
			'data'    => $order_list
		]);
	}


	public function ListFilter(){
    if ($this->input->post()) {
			$data = $this->input->post(null, true);
      
      $limit  = $data['limit'] ?? 100;
      $offset = $data['offset'] ?? 0;
      
      $result  = $this->SalesOrder_model->getListByFilter($data, $limit, $offset);
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
      'Customer Name'     => 'string',
      'Broker Name'     => 'string',
      'Quotation Weight'=> 'string',
      'Quotation Amount'=> 'string',
      'Inward Weight'   => 'string',
      'Status'          => 'string'
    ];

    $writer->writeSheetHeader($sheetName, $header, ['suppress_row'=>true]);

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

    if($from_date != ''){
      $reportedBy .= 'From Date : ' . $from_date . ', ';
    }

    if($to_date != ''){
      $reportedBy .= 'To Date : ' . $to_date . ', ';
    }

    if($customer_id != ''){
      $reportedBy .= 'Customer : ' .( $this->SalesQuotation_model->getData('clients', 'company', ['AccountID' => $customer_id])['company'] ?? '') . ', ';
    }

    if($broker_id != ''){
      $reportedBy .= 'Broker : ' .( $this->SalesQuotation_model->getData('clients', 'company', ['AccountID' => $broker_id])['company'] ?? '') . ', ';
    }

    if($status != ''){
      $status_list = [1 => 'Pending', 2 =>'Cancel', 3 =>'Expired', 4 =>'Approved', 5 =>'Inprogress', 6 =>'Complete', 7 =>'Partially Complete'];
      $reportedBy .= 'Status : ' .( $status_list[$status] ?? '') . ', ';
    }

    $writer->markMergedCell($sheetName, 2, 0, 2, 12);
    $writer->writeSheetRow($sheetName, [$reportedBy]);
    $writer->writeSheetRow($sheetName, []);

    // ===== HEADER ROW =====
    $writer->writeSheetRow($sheetName, array_keys($header));

    // ===== CHUNK FETCH START =====
    $limit = 100;
    $offset = 0;

    while(true){
      $result = $this->SalesQuotation_model->getListByFilter($post, $limit, $offset);
      if(empty($result['rows'])){
        break;
      }

      foreach($result['rows'] as $row){
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
function GetCustomDropdownList(){
		if ($this->input->post()) {
			$parent_id 		= $this->input->post('parent_id');
			$parent_value = $this->input->post('parent_value');
			$child_id     = $this->input->post('child_id');

			switch($parent_id){
				case 'item_type':
          switch($child_id){
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
      if(empty($data)){
        echo json_encode([
          'success' => false,
          'message' => 'No data found',
          'data' => []
        ]);
      }else{
        echo json_encode([
          'success' => true,
          'message' => 'Data found',
          'data' => $data
        ]);
      }
			die;
		}else{
			echo json_encode([
				'success' => false,
				'message' => 'Invalid request'
			]);
			die;
		}
	}
  
}
