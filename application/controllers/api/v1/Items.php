<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Items extends CI_Controller {

  public function __construct(){
    parent::__construct();
    header('Content-Type: application/json');
    $this->load->model('Api_model');
    $this->load->model('Items_model');
  }

  private function validateMethod($method){
    if ($this->input->method() !== $method) {
      $this->output
        ->set_status_header(405)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode([
          'status' => false,
          'message' => 'Invalid request method'
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        ->_display();
      exit;
    }
  }

  private function getInputData(){
    $contentType = $this->input->get_request_header('Content-Type');
    if (strpos($contentType, 'application/json') !== false) {
      $rawData = json_decode($this->input->raw_input_stream, true);
      $data = is_array($rawData) ? $rawData : [];
    } else {
      $data = $this->input->post(NULL, TRUE);
    }
    foreach ($data as $key => $value) {
      if (!is_array($value)) {
        $data[$key] = trim($value);
      }
    }

    return $data;
  }

  private function validateRequired($data, $fields){
    $missing = [];

    foreach ($fields as $field) {
      if (!isset($data[$field]) || trim($data[$field]) === '') {
        $missing[] = $field;
      }
    }

    if (!empty($missing)) {
      $this->response( false, 'Required fields missing: '.implode(', ', $missing), 400 );
      exit;
    }
  }

  private function response($status,$message,$code=200,$data=[]){
    $response = [
      'status' => $status,
      'message' => $message
    ];

    if (!empty($data)) {
      $response['data'] = $data;
    }

    $this->output
        ->set_status_header($code)
        ->set_content_type('application/json','utf-8')
        ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
        ->_display();

    exit;
  }

  // ==================== Do Not Edit Above This Line =================
  // ==================== All Code After This Line ====================

  public function index(){
    $this->validateMethod('get');
    return $this->response(true,'Items Controller Working', 200);
  }

  public function create(){
    $this->validateMethod('post');

    $data = $this->getInputData();

    $this->validateRequired($data, ['item_id','item_name','tax','unit_weight', 'hsn_code']);

    try {
      if ($this->Api_model->checkDuplicate('items', ['old_item_id' => $data['item_id']])) {
        return $this->response(false,'Item already exists', 400);
      }

      if ($this->Api_model->checkDuplicate('items', ['ItemName' => $data['item_name']])) {
        $result = $this->Api_model->updateData( 'items', ['old_item_id' => $data['item_id']], ['ItemName' => $data['item_name']] );
        if ($result) {
          return $this->response(true,'Item sync successfully', 200);
        }
      }
      
      $itemID = $data['item_id'];
      $itemName = $data['item_name'];
      $tax = $data['tax'];
      $unitWeight = $data['unit_weight'] ?? 'Kgs';
      $hsnCode = $data['hsn_code'];
      $itemGroupID = $data['item_group'];
      $division_id = $data['division_id'];

      $groupDetails = $this->Api_model->getItemGroupDetails($itemGroupID);
      $nextNumber = $this->Items_model->getNextItemCode(2);
      $prefix = $this->Api_model->getRowData('ItemCategoryMaster', 'Prefix', ['id' => '2'],)->Prefix ?? 'RET';
      $ItemCode = $prefix.$nextNumber;

      $insertData = [
        'old_item_id' => $itemID,
        'ItemID' => $ItemCode,
        'ItemName' => $itemName,
        'tax' => $tax,
        'UnitWeightIn' => $unitWeight,
        'hsn_code' => $hsnCode,
        'ItemTypeID' => $groupDetails->item_type_id,
        'ItemCategoryCode' => '2',
        'DivisionID' => $division_id,
        'MainGrpID' => $groupDetails->main_group_id,
        'SubGrpID1' => $groupDetails->sub_group_id1,
        'SubGrpID2' => $itemGroupID
      ];

      $result = $this->Api_model->saveData('items', $insertData);
      if ($result) {
        return $this->response(true,'Item created successfully', 201);
      }
    } catch (Exception $e) {
      return $this->response(false,'Internal server error', 500, ['error' => $e->getMessage()]);
    }
    
    return $this->response(false,'Something went wrong', 500);
  }
}