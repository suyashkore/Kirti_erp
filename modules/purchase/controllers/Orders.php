<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Quotation_model');
		$this->load->model('Orders_model');
	}

	/* =========================
		* ADD / EDIT PAGE
		* ========================= */
	public function index(){
    if (!has_permission_new('PurchaseOrder', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Orders Master';
    
		$this->load->view('admin/Orders/OrdersAddEdit', $data);
	}
  
  /* =========================
		* LIST PAGE
		* ========================= */
	public function List(){
    if (!has_permission_new('PurchaseOrderList', '', 'view')) { access_denied('Access Denied'); }
		$data['title'] = 'Orders List';
    
    $data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
    $selected_company = $this->session->userdata('root_company');
    $data['company_detail'] = $this->Quotation_model->get_company_detail($selected_company);

		$this->load->view('admin/Orders/OrdersList', $data);
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
      
      $result  = $this->Orders_model->getListByFilter($data, $limit, $offset);
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
    if (!has_permission_new('PurchaseOrderList', '', 'export')) { access_denied('Access Denied'); }
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

    $sheetName = 'Purchase Order List';
    $writer = new XLSXWriter();

    $header = [
      'Order No'  => 'string',
      'Order Date'  => 'string',
      'Category'     		=> 'string',
      'Quotation No'     		=> 'string',
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
      $result = $this->Orders_model->getListByFilter($post, $limit, $offset);
      if(empty($result['rows'])){
        break;
      }

      foreach($result['rows'] as $row){
        $writer->writeSheetRow($sheetName, [
          $row['PurchID'] ?? '',
          date('d/m/Y', strtotime($row['TransDate'])) ?? '',
          $row['category_name'] ?? '',
          $row['QuatationID'] ?? '',
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
          $status_list[$row['Status'] ?? 1] ?? 'Pending'
        ]);
      }

      $offset += $limit;
      unset($result);
    }

    // ===== SAVE FILE =====
    $filename = 'PurchaseOrderList_'.date('YmdHis').'.xlsx';
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
