<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Purchase extends CI_Controller {

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
      if (!array_key_exists($field, $data)) {
        $missing[] = $field;
        continue;
      }

      $value = $data[$field];
      if (is_string($value) && trim($value) === '') {
        $missing[] = $field;
      }
      if (is_array($value) && empty($value)) {
        $missing[] = $field;
      }
    }

    if (!empty($missing)) {
      $this->response(false, 'Required fields missing: '.implode(', ', $missing), 400);
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
    return $this->response(true,'Prchase Controller Working', 200);
  }

  public function Order(){
    $this->validateMethod('post');

    $data = $this->getInputData();

    $this->validateRequired($data, ['cocd','trinvs','sporddtl']);

    try {
      $PlantId = $this->Api_model->getRowData('rootcompany', 'id', ['comp_short' => $data['cocd']])->id ?? 1;
      if (empty($data['trinvs'][0]) || empty($data['sporddtl'])) {
        return $this->response(false,'Invalid payload structure',400);
      }

      $invoice = $data['trinvs'][0];

      $this->validateRequired($invoice, [
        'doc_type',
        'party_st',
        'party_no',
        'doc_ref',
        'im_loc'
      ]);

      // Check Vendor exists
      $clientData = $this->Api_model->getRowData('clients', '*', ['AccountID' => 'RMV'.$invoice['party_no']]);
      if ($clientData) {
        // return $this->response(true,'Vendor details', 200, $clientData);
      }else{
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://kirti.globalinfocloud.in/GetAccountDetails',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "AccountCode": "'.$invoice['party_no'].'",
            "access_tokan" : "fe3fd1f94239c467727c5cae504d4fdd"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // return $this->response(true,'Vendor details', 200, json_decode($response));
        $clientData = json_decode($response);

        $SubActGroupID1 = $this->Api_model->getRowData('AccountSubGroup2', 'SubActGroupID1', ['SubActGroupID' => 1000186])->SubActGroupID1 ?? 100048;
        $ActGroupID = $this->Api_model->getRowData('AccountSubGroup1', 'ActGroupID', ['SubActGroupID1' => $SubActGroupID1])->ActGroupID ?? 10000;

        $createVendor = [
          'PlantID' => $PlantId,
          'AccountID' => 'RMV'.$invoice['party_no'],
          'company' => $clientData->AccountDetails->FirmName,
          'FavouringName' => $clientData->AccountDetails->FirmName,
          'ActMainGroupID' => $ActGroupID,
          'ActSubGroupID1' => $SubActGroupID1,
          'ActSubGroupID2' => '1000186',
          'PAN' => $clientData->AccountDetails->aadhaar_number,
          'GSTIN' => $clientData->AccountDetails->gstin,
          'billing_country' => $clientData->AccountDetails->AccountID,
          'billing_state' => $clientData->AccountDetails->state,
          'billing_city' => $clientData->AccountDetails->subdist,
          'billing_zip' => $clientData->AccountDetails->pincode,
          'billing_address' => $clientData->AccountDetails->street,
          'MobileNo' => $clientData->AccountDetails->AccountID,
          'Email' => null,
          'IsActive' => 'Y',
          'TransDate' => date('Y-m-d H:i:s')
        ];
        $this->Api_model->saveData('clients', $createVendor);
        $clientData = $this->Api_model->getRowData('clients', '*', ['AccountID' => 'RMV'.$invoice['party_no']]);

        $this->Api_model->saveData('clientwiseshippingdata', [
          'AccountID' => $clientData->AccountID,
          'ShippingPin' => $clientData->billing_zip ?? '',
          'ShippingAdrees' => $clientData->billing_address ?? '-',
          'ShippingState' => $clientData->billing_state ?? 'MH',
          'ShippingCity' => $clientData->billing_city,
          'MobileNo' => $clientData->MobileNo,
          'TransDate' => date('Y-m-d H:i:s')
        ]);
        // return $this->response(true,'Vendor create', 201, $clientData);
      }

      // Item calculation
      $historyData = [];
      $totalQty = 0;
      $totalAmt = 0;
      $i = 0;
      foreach ($data['sporddtl'] as $item) {

        $this->validateRequired($item, [
          'IM_CODE',
          'im_qty',
          'im_ordrate'
        ]);

        $itemid = $this->Api_model->getRowData('items', 'id', ['old_item_id' => $item['IM_CODE']])->id ?? null;
        if (!$itemid) {
          return $this->response(false,'Item not found, sync first',400);
        }
        $itemData = $this->Items_model->getById($itemid);

        $historyData[] = [
          'OrderID'     => $invoice['doc_ref'],
          'PlantID'     => $PlantId,
          'FY'          => (date('m') >= 4 ? date('y') : (date('y') - 1)),
          'TransDate'   => date('Y-m-d'),
          'TType'       => 'P',
          'TType2'      => 'Quotation',
          'AccountID'   => $clientData->AccountID,
          'ItemID'      => $itemData->ItemID,
          'GodownID'    => '',
          'BasicRate'   => $item['im_ordrate'],
          'SaleRate'    => $item['im_ordrate'],
          'SuppliedIn'  => $itemData->ShortCode,
          'UnitWeight'  => $item['im_qty'],
          'WeightUnit'  => $itemData->ShortCode,
          'CaseQty'     => 1,
          'OrderQty'    => 1,
          'Cases'       => 0,
          'DiscAmt'     => 0,
          'DiscPerc'    => 0,
          'cgst'        => 0,
          'cgstamt'     => 0,
          'sgst'        => 0,
          'sgstamt'     => 0,
          'igst'        => 0,
          'igstamt'     => 0,
          'OrderAmt'    => $item['im_qty'] * $item['im_ordrate'],
          'NetOrderAmt' => $item['im_qty'] * $item['im_ordrate'],
          'Ordinalno'   => $i+1,
        ];

        $totalQty += $item['im_qty'];
        $totalAmt += $item['im_qty'] * $item['im_ordrate'];
      }

      $deliveryLocation = $this->Api_model->getRowData('clientwiseshippingdata', 'id', ['AccountID' => $clientData->AccountID])->id ?? null;
      if (!$deliveryLocation) {
        $deliveryLocation = $this->Api_model->saveData('clientwiseshippingdata', [
          'AccountID' => $clientData->AccountID,
          'ShippingPin' => $clientData->billing_zip ?? '',
          'ShippingAdrees' => $clientData->billing_address ?? '-',
          'ShippingState' => $clientData->billing_state ?? 'MH',
          'ShippingCity' => $clientData->billing_city,
          'MobileNo' => $clientData->MobileNo,
          'TransDate' => date('Y-m-d H:i:s')
        ]);
      }

      // PURCHASE ORDER MASTER
      $insertData = [
        'PlantID' => $PlantId,
        'FY' => (date('m') >= 4 ? date('y') : (date('y') - 1)),
        'PurchaseLocation' => $invoice['im_loc'],
        'QuotatioonID' => $invoice['doc_ref'],
        'TransDate' => date('Y-m-d H:i:s'),
        'ItemType' => $itemData->ItemTypeID,
        'ItemCategory' => $itemData->ItemCategoryCode,
        'AccountID' => $clientData->AccountID,
        'BrokerID' => null,
        'DeliveryLocation' => $deliveryLocation,
        'DeliveryFrom' => date('Y-m-d'),
        'DeliveryTo' => date('Y-m-d', strtotime('+7 days')),
        'PaymentTerms' => 'Credit', // ['Credit', 'Advance', 'OnDelivery']
        'FreightTerms' => 1, // [1 => 'spot', 2 => 'delivery']
        'GSTIN' => $clientData->GSTIN,
        'TotalWeight' => $totalQty,
        'TotalQuantity' => $totalQty,
        'ItemAmt' => $totalAmt,
        'DiscAmt' => 0,
        'TaxableAmt' => 0,
        'CGSTAmt' => 0,
        'SGSTAmt' => 0,
        'IGSTAmt' => 0,
        'RoundOffAmt' => 0,
        'NetAmt' => $totalAmt
      ];

      $poId = $this->Api_model->saveData('PurchQuotationMaster', $insertData);

      if (!$poId) {
        return $this->response(false,'Failed to create purchase quotation',500);
      }

      $this->Api_model->saveBatchData('history', $historyData);

      return $this->response(true,'Purchase Quotation created successfully',201);

    } catch (Exception $e) {
      return $this->response(false,'Internal server error',500,['error' => $e->getMessage()]);
    }

    return $this->response(false,'Something went wrong',500);
  }
}