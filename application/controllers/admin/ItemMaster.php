<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ItemMaster extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Items_model');
	}

	/* =========================
		* ADD / EDIT PAGE
		* ========================= */
	public function index(){
		$data['title'] = 'Item Master';

		$data['types'] 			= $this->Items_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['division'] 	= $this->Items_model->getDropdown('ItemsDivisionMaster', 'id, name', ['IsActive' => 'Y'], 'name', 'ASC');
		$data['hsn'] 				= $this->Items_model->getDropdown('hsn', 'id, name', ['status' => '1'], 'name', 'ASC');
		$data['gst'] 				= $this->Items_model->getDropdown('taxes', 'id, name, taxrate', '', 'taxrate', 'ASC');
		$data['uom'] 				= $this->Items_model->getDropdown('UnitMaster', 'id, ShortCode, UnitName', '', 'id', 'ASC');
		$data['weight_unit']= $this->Items_model->getDropdown('WeightUnitMaster', 'ShortCode, UnitName', ['IsActive' => 'Y'], 'ShortCode', 'ASC');
		$data['brand'] 			= $this->Items_model->getDropdown('BrandMaster', 'id, BrandName', '', 'id', 'ASC');
		$data['priority'] 	= $this->Items_model->getDropdown('PriorityMaster', 'id, PriorityName', ['IsActive' => 'Y'], 'id', 'ASC');
		$data['vendor'] 		= $this->Items_model->getVendorDropdown();

    $data['item_list'] = $this->Items_model->getItemsList();
    
		$this->load->view('admin/item_master/AddEditItem', $data);
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

	public function GetNextItemCode()
	{
		$category_id = $this->input->post('category_id');
		$nextNumber = $this->Items_model->getNextItemCode($category_id);
    $prefix = $this->Items_model->getDropdown('ItemCategoryMaster', 'Prefix', ['id' => $category_id], '', '')[0]['Prefix'];
    $data = $prefix.$nextNumber;

    echo json_encode([
      'success' => true,
      'message' => 'Data found',
      'data' => $data
    ]);
	}

	public function SaveItemMaster(){
		if ($this->input->post()) {
      $PlantID = $this->session->userdata('root_company');
      $UserID = $this->session->userdata('username');
			$data = $this->input->post(null, true);
      $field_names = array_keys($data);
      foreach ($field_names as $key => $value) {
        $data[$value] = trim($data[$value]);
      }
      $required_fields = [ 'item_main_group', 'item_sub_group1', 'item_sub_group2', 'item_division', 'item_code', 'item_name', 'hsn', 'gst', 'uom', 'unit_weight', 'unit_weight_in'];
      if (empty($data['item_id'])) {
        $required_fields[] = 'item_type';
        $required_fields[] = 'item_category';
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

      $insertData = [
        'ItemTypeID'        => $data['item_type'] ?? '',
        'MainGrpID' => $data['item_main_group'],
        'SubGrpID1' => $data['item_sub_group1'],
        'SubGrpID2' => $data['item_sub_group2'],
        'DivisionID'   => $data['item_division'],
        'ItemCategoryCode'   => $data['item_category'] ?? '',
        'PlantID'       => $PlantID,
        'ItemID'       => $data['item_code'],
        'ItemName'       => $data['item_name'],
        'description' => $data['item_description'] ?? '',
        'hsn_code'             => $data['hsn'],
        'tax'             => $data['gst'],
        'unit'             => $data['uom'],
        'PackingQty' => $data['packing_quantity'],
        'UnitWeight'     => $data['unit_weight'],
        'UnitWeightIn' => $data['unit_weight_in'],
        'PackingWeight' => $data['packing_weight'] ?? '',
        'BrandID' => $data['brand'] ?? '',
        'PriorityID'       => $data['priority'] ?? '',
        'UpperTolerence' => $data['upper_tolerence'] ?? 0,
        'DownTolerence' => $data['down_tolerence'] ?? 0,
        'UnloadingRate' => $data['unloading_rate'] ?? 0,
        'IsBagApplicable' => $data['bag_applicable'] ?? 'Y',
        'IsBOMApplicable' => $data['bom_applicable'] ?? 'N',
        'IsActive'      => $data['is_active'] ?? 'Y',
        'QCManage' => $data['quality_managed'] ?? 'N',
        'BatchManage' => $data['batch_managed'] ?? 'N',
        'BatchManageType' => $data['batch_managed_method'] ?? '',
        'ItemLife' => $data['self_life'] ?? '',
        'MaxStockLevel' => $data['max_level'] ?? '',
        'MinStockLevel' => $data['min_level'] ?? '',
        'ReOrderLevel' => $data['reorder_level'] ?? '',
        'MinOrderQty' => $data['reorder_quantity'] ?? '',
        'MRP' => $data['mrp'] ?? '',
        'PreferVendorID' => $data['prefer_vendor'] ?? '',
        'VendorPartNo' => $data['vendor_part_no'] ?? '',
        'HindiName' => $data['hindi_name'] ?? '',
        'AdditionalInformation' => $data['additional_info'] ?? ''
      ];

      $attachmentUrl = null;
      if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] !== '') {
        $path = FCPATH . 'uploads/item_master/';
        if (!file_exists($path)) {
          mkdir($path, 0755, true);
        }
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
        $config['file_name'] = $data['item_code'] . '.' . pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
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

        $insertData['Attachment'] = $attachmentUrl;
      }

			if (empty($data['item_id'])) {
        // Check duplicate
        if ($this->Items_model->checkDuplicate($data['item_code'], null)) {
          echo json_encode([
            'success' => false,
            'message' => 'Item code already exists'
          ]);
          die;
        }
        $insertData['UserID'] = $UserID;

				$insertId = $this->Items_model->addItem($insertData);

				if ($insertId) {
          $details = $this->Items_model->getById($insertId);
					echo json_encode([
						'success'   => true,
						'message'   => 'Record created successfully...',
						'insert_id' => $insertId,
            'data'      => $details
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => 'Problem creating record'
					]);
				}

			}else {
				$id = $data['item_id'];
        unset($insertData['ItemTypeID']);
        unset($insertData['ItemID']);
        unset($insertData['ItemCategoryCode']);
        $insertData['UserID2'] = $UserID;

				$success = $this->Items_model->updateItem($id, $insertData);

				if ($success) {
          $details = $this->Items_model->getById($data['item_id']);
					echo json_encode([
						'success'   => true,
						'message'   => 'Record updated successfully...',
            'data'      => $details
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => 'Problem updating record'
					]);
				}
			}
		}else{
			echo json_encode([
				'success' => false,
				'message' => 'Invalid request'
			]);
		}
	}

  public function GetItemDetails(){
    $item_id = $this->input->post('item_id');
    $item = $this->Items_model->getById($item_id);
    if(empty($item)){
      echo json_encode([
        'success' => false,
        'message' => 'No data found',
        'data' => []
      ]);
      die;
    }

    echo json_encode([
      'success' => true,
      'message' => 'Data found',
      'data' => $item
    ]);
  }

  /* =========================
		* LIST PAGE WITH FILTER
		* ========================= */
  public function list(){
    $data['title'] = 'Item Master';
    $data['item_list'] = $this->Items_model->getItemsList();
    $data['types']    = $this->Items_model->getDropdown('ItemTypeMaster', 'id, ItemTypeName', ['isActive' => 'Y'], 'id', 'ASC');
		$data['division'] = $this->Items_model->getDropdown('ItemsDivisionMaster', 'id, name', ['IsActive' => 'Y'], 'name', 'ASC');
		$data['hsn'] 			= $this->Items_model->getDropdownJoinBy('hsn');
		$data['gst'] 			= $this->Items_model->getDropdownJoinBy('taxes');
		$data['unit'] 		= $this->Items_model->getDropdownJoinBy('UnitMaster');

    $selected_company = $this->session->userdata('root_company');
    $data['company_detail'] = $this->Items_model->get_company_detail($selected_company);
    
    $this->load->view('admin/item_master/ListItem', $data);
  }

  public function ItemListFilter(){
    if ($this->input->post()) {
			$data = $this->input->post(null, true);
      
      $limit  = $data['limit'] ?? 100;
      $offset = $data['offset'] ?? 0;
      
      $result  = $this->Items_model->getItemsListByFilter($data, $limit, $offset);
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

  public function ItemListExportExcel()
  {
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

    $sheetName = 'Items List';
    $writer = new XLSXWriter();

    $header = [
      'Item Code'   => 'string',
      'Item Name'   => 'string',
      'Type'        => 'string',
      'Main Group'  => 'string',
      'Group1'      => 'string',
      'Group2'      => 'string',
      'Category'    => 'string',
      'Division'    => 'string',
      'HSN'         => 'string',
      'GST'         => 'string',
      'UOM'         => 'string',
      'Packing Wt'  => 'string',
      'IsActive'    => 'string'
    ];

    $writer->writeSheetHeader($sheetName, $header, ['suppress_row'=>true]);

    $selected_company = $this->session->userdata('root_company');
    $company_detail   = $this->Items_model->get_company_detail($selected_company);

    // ===== COMPANY NAME ROW =====
    $writer->markMergedCell($sheetName, 0, 0, 0, 12);
    $writer->writeSheetRow($sheetName, [$company_detail->company_name]);

    // ===== COMPANY ADDRESS ROW =====
    $writer->markMergedCell($sheetName, 1, 0, 1, 12);
    $writer->writeSheetRow($sheetName, [$company_detail->address]);

    // ===== FILTER ROW =====
    $reportedBy = "Filtered By : ";
    $item_type        = $post['item_type'] ?? '';
    $item_main_group  = $post['item_main_group'] ?? '';
    $item_sub_group1  = $post['item_sub_group1'] ?? '';
    $item_sub_group2  = $post['item_sub_group2'] ?? '';
    $item_division    = $post['item_division'] ?? '';
    $item_category    = $post['item_category'] ?? '';
    $hsn              = $post['hsn'] ?? '';
    $gst              = $post['gst'] ?? '';
    $unit             = $post['unit'] ?? '';

    if($item_type != ''){
      $reportedBy .= 'Item Type : ' .( $this->Items_model->getData('ItemTypeMaster', 'ItemTypeName', ['id' => $item_type])['ItemTypeName'] ?? '') . ', ';
    }

    if($item_main_group != ''){
      $reportedBy .= 'Main Group : ' .( $this->Items_model->getData('items_main_groups', 'name', ['id' => $item_main_group])['name'] ?? '') . ', ';
    }

    if($item_sub_group1 != ''){
      $reportedBy .= 'Sub Group 1 : ' .( $this->Items_model->getData('ItemsSubGroup1', 'name', ['id' => $item_sub_group1])['name'] ?? '') . ', ';
    }

    if($item_sub_group2 != ''){
      $reportedBy .= 'Sub Group 2 : ' .( $this->Items_model->getData('ItemsSubGroup2', 'name', ['id' => $item_sub_group2])['name'] ?? '') . ', ';
    }

    if($item_division != ''){
      $reportedBy .= 'Division : ' .( $this->Items_model->getData('ItemsDivisionMaster', 'name', ['id' => $item_division])['name'] ?? '') . ', ';
    }

    if($item_category != ''){
      $reportedBy .= 'Category : ' .( $this->Items_model->getData('ItemCategoryMaster', 'CategoryName', ['id' => $item_category])['CategoryName'] ?? '') . ', ';
    }

    if($hsn != ''){
      $reportedBy .= 'HSN : ' .( $hsn ) . ', ';
    }

    if($gst != ''){
      $reportedBy .= 'GST : ' .( $this->Items_model->getData('taxes', 'taxrate', ['id' => $gst])['taxrate'] ) . ' %, ';
    }

    if($unit != ''){
      $reportedBy .= 'UOM : ' .( $this->Items_model->getData('UnitMaster', 'ShortCode', ['id' => $unit])['ShortCode'] ) . ', ';
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
      $result = $this->Items_model->getItemsListByFilter($post, $limit, $offset);
      if(empty($result['rows'])){
        break;
      }

      foreach($result['rows'] as $row){
        $writer->writeSheetRow($sheetName, [
          $row['ItemID'] ?? '',
          $row['ItemName'] ?? '',
          $row['ItemTypeName'] ?? '',
          $row['main_group_name'] ?? '',
          $row['sub_group1_name'] ?? '',
          $row['sub_group2_name'] ?? '',
          $row['CategoryName'] ?? '',
          $row['division_name'] ?? '',
          $row['hsn_code'] ?? '',
          (int)($row['taxrate'] ?? '').'%',
          $row['ShortCode'] ?? '',
          ($row['PackingWeight'] ?? '').' '.($row['UnitWeightIn'] ?? ''),
          ($row['IsActive'] == 'Y' ? 'Yes' : 'No')
        ]);
      }

      $offset += $limit;
      unset($result);
    }

    // ===== SAVE FILE =====
    $filename = 'ItemsList_'.date('YmdHis').'.xlsx';
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
		* QC DEDUCTION MATRIX
		* ========================= */

  public function DeductionMatrix(){
		$data['title'] = 'Item Master';

		$data['items_list'] 	= $this->Items_model->getDropdown('items', 'ItemID, ItemName', ['IsActive' => 'Y'], 'id', 'ASC');
    
		$this->load->view('admin/item_master/AddEditDeductionMatrix', $data);
  }

  public function GetQcParameterByItemID(){
    if (!$this->input->post()) {
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }

    $itemID = $this->input->post('itemID');

    if(empty($itemID)){
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }

    $result = $this->Items_model->getQcParameterByItemID($itemID);
    echo json_encode(['success'=>true,'data'=>$result]);
  }

  public function GetQcParameterDetails(){
    if (!$this->input->post()) {
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }

    $itemID = $this->input->post('itemID');
    $qcParameterID = $this->input->post('qcParameterID');

    if(empty($itemID) || empty($qcParameterID)){
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }

    $parameterDetails = $this->Items_model->getQcParameterDetails($itemID, $qcParameterID);
    $deductionMatrixList = $this->Items_model->getDeductionMatrixList($itemID, $qcParameterID);

    $result = ['parameterDetails'=>$parameterDetails, 'deductionMatrixList'=>$deductionMatrixList];
    echo json_encode(['success'=>true,'data'=>$result]);
  }

  public function SaveDeductionMatrix(){
    if (!$this->input->post()) {
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }

    $data = $this->input->post(null, true);
    $item_id = $data['item_id'];
    $qc_parameter = $data['qc_parameter'];
    $value = $data['value'] ?? [];
    $deduction = $data['deduction'] ?? [];
    $form_mode = $data['form_mode'] ?? 'add';

    if(empty($item_id) || empty($qc_parameter) || empty($value) || empty($deduction)){
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }
    $insertData = [];
    
    $data['UserID'] = $this->session->userdata('username');

    $success = $this->Items_model->saveBatchDeductionMatrix($data);
    if ($success) {
      echo json_encode([
        'success' => true,
        'message' => 'Record '.($form_mode == 'add' ? 'created' : 'updated').' successfully...'
      ]);
      die;
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'Problem occured while '.($form_mode == 'add' ? 'creating' : 'updating').' record'
      ]);
      die;
    }
  }
}
