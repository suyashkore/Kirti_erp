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

  private function CurlRequest($url, $method='POST', $data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
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
      // check root plant company
      $FY = (date('m') >= 4 ? date('y') : (date('y') - 1));
      $PlantId = $this->companyShortCodeDetails($data['cocd']);

      if (empty($data['trinvs'][0]) || empty($data['sporddtl'])) {
        return $this->response(false,'Invalid payload structure',400);
      }

      $trade = $data['trinvs'][0];

      $this->validateRequired($trade, [
        'doc_type',
        'party_st',
        'party_no',
        'doc_ref',
        'im_loc'
      ]);

      // check location of plant
      $LocationId = $this->locationCenterIdDetails($trade['im_loc'], $PlantId, $data['cocd']);

      // Trade link or not check
      $TradeLink = $this->Api_model->getRowData('OldErpMapping', '*', ['old_type' => 'Trade', 'old_id' => $trade['doc_ref'], 'new_type' => 'Purchase Quotation']);

      if ($TradeLink) {
        return $this->response(false,'Trade already linked with quotation '.$TradeLink->new_id, 400);
      }

      // Check Vendor exists
      $clientData = $this->Api_model->getRowData('clients', '*', ['ShortCode' => $trade['party_no']]);
      if ($clientData) {
        // return $this->response(true,'Vendor details', 200, $clientData);
      }else{
        $payload = [
          'AccountCode' => $trade['party_no'],
          'access_tokan' => 'fe3fd1f94239c467727c5cae504d4fdd'
        ];
        $response = $this->CurlRequest('https://kirti.globalinfocloud.in/GetAccountDetailsNew', 'POST', $payload);
        // return $this->response(true,'Vendor details', 200, json_decode($response));
        $res = json_decode($response);
        if(empty($res)){
          return $this->response(false,'Vendor details not found',400);
        }

        $SubActGroupID1 = $this->Api_model->getRowData('AccountSubGroup2', 'SubActGroupID1', ['SubActGroupID' => 1000186])->SubActGroupID1 ?? 100048;
        $ActGroupID = $this->Api_model->getRowData('AccountSubGroup1', 'ActGroupID', ['SubActGroupID1' => $SubActGroupID1])->ActGroupID ?? 10000;
        $count = (int)($this->Api_model->getRowData('clients', 'COUNT(userid) as count', ['ActSubGroupID2' => '1000186'])->count ?? 0) + 1 ;

        $address = '';
        $res = $res->AccountDetails;
        // customer type = 1 => Farmer, 2 => broker, 3 => treader, 4 => cooperate
        if($res->CustomerType == 1){
          $addArray = [];
          if($res->house){
            $addArray[] = $res->house;
          }
          if($res->street){
            $addArray[] = $res->street;
          }
          if($res->loc){
            $addArray[] = $res->loc;
          }
          if($res->po){
            $addArray[] = $res->po;
          }
          $address = implode(', ', $addArray);
        }else{
          $address = $res->loc ?? '';
        }
        $AccountID = 'RMV'.str_pad($count, 4, '0', STR_PAD_LEFT);

        $createVendor = [
          'PlantID' => $PlantId,
          'AccountID' => $AccountID,
          'ShortCode' => $trade['party_no'],
          'company' => $res->company,
          'FavouringName' => $res->company,
          'ActMainGroupID' => $ActGroupID,
          'ActSubGroupID1' => $SubActGroupID1,
          'ActSubGroupID2' => '1000186',
          'PAN' => $res->Pan,
          'GSTIN' => null,
          'AadhaarNo' => $res->aadhaar_number,
          'billing_country' => 102,
          'billing_state' => $res->state,
          'billing_city' => $res->dist,
          'billing_zip' => $res->zip,
          'billing_address' => $address,
          'MobileNo' => $res->AccountID,
          'Email' => null,
          'IsActive' => 'Y',
          'TransDate' => date('Y-m-d H:i:s')
        ];
        $this->Api_model->saveData('clients', $createVendor);
        $clientData = $this->Api_model->getRowData('clients', '*', ['AccountID' => $AccountID]);

        $dataTrue = false;
        if(isset($res->AadharDetails) && !empty($res->AadharDetails)){
          foreach ($res->AadharDetails as $key => $value) {
            if(empty($res->AadharDetails[$key]->state)){
              continue;
            }
            if(is_numeric($res->AadharDetails[$key]->state)){
              $stateCode = $this->Api_model->getRowData('xx_statelist', 'short_name', ['id' => $res->AadharDetails[$key]->state])->short_name ?? 'MH';
            }else{
              $stateCode = $this->Api_model->getRowData('xx_statelist', 'short_name', ['state_name' => $res->AadharDetails[$key]->state])->short_name ?? 'MH';
            }

            if(is_numeric($res->AadharDetails[$key]->dist)){
              $cityId = $res->AadharDetails[$key]->dist;
            }else{
              $cityId = $this->Api_model->getRowData('xx_citylist', 'id', ['city_name' => $res->AadharDetails[$key]->dist])->id ?? 552;
            }
            $batchInsert = [
              'AccountID' => $AccountID,
              'ShippingPin' => $res->AadharDetails[$key]->pincode ?? '',
              'ShippingAdrees' => $res->AadharDetails[$key]->loc ?? '-',
              'ShippingState' => $stateCode,
              'ShippingCity' => $cityId,
              'MobileNo' => $res->AadharDetails[$key]->AccountID,
              'TransDate' => date('Y-m-d H:i:s')
            ];
            $this->Api_model->saveData('clientwiseshippingdata', $batchInsert);
            $dataTrue = true;
            break;
          }
        }

        if(isset($res->GstRecord) && !empty($res->GstRecord)){
          foreach ($res->GstRecord as $key => $value) {
            if($res->GstRecord[$key]->active_status == 'Inactive' || empty($res->GstRecord[$key]->address) || empty($res->GstRecord[$key]->gstin)){
              continue;
            }
            $address = $res->GstRecord[$key]->address;
            $parts = array_map('trim', explode(',', $address));
            $count = count($parts);
            $pincode = $count >= 1 ? $parts[$count - 1] : '';
            $city    = $count >= 3 ? $parts[$count - 3] : '';
            
            if(is_numeric($res->GstRecord[$key]->state)){
              $stateCode = $this->Api_model->getRowData('xx_statelist', 'short_name', ['id' => $res->GstRecord[$key]->state])->short_name ?? 'MH';
            }else{
              $stateCode = $this->Api_model->getRowData('xx_statelist', 'short_name', ['state_name' => $res->GstRecord[$key]->state])->short_name ?? 'MH';
            }

            $cityId = $this->Api_model->getRowData('xx_citylist', 'id', ['city_name' => $city])->id ?? 552;

            $batchInsert = [
              'AccountID' => $AccountID,
              'ShippingPin' => $pincode,
              'ShippingAdrees' => $address,
              'ShippingState' => $stateCode,
              'ShippingCity' => $cityId,
              'MobileNo' => $res->GstRecord[$key]->AccountID,
              'TransDate' => date('Y-m-d H:i:s')
            ];
            $this->Api_model->saveData('clientwiseshippingdata', $batchInsert);
            $dataTrue = true;
            // gst data update in account
            $orgType = [
              'P' => 'Proprietorship',
              'F' => 'Partnership',
              'C' => 'Private Limited',
              'T' => 'Society / Trust / Club',
              'G' => 'Government Department/Body',
              'L' => 'Local Authority',
              'H' => 'Hindu Undivided Family (HUF)'
            ];
            $updateData = [
              'GSTIN' => $res->GstRecord[$key]->gstin,
              'OrganisationType' => $orgType[$res->GstRecord[$key]->gstin[5]] ?? 'Other',
              'GSTType' => '1'
            ];
            $this->Api_model->updateData('clients', $updateData, ['AccountID' => $AccountID]);
            break;
          }
        }

        if(!$dataTrue){
          $this->Api_model->saveData('clientwiseshippingdata', [
            'AccountID' => $clientData->AccountID,
            'ShippingPin' => $clientData->billing_zip ?? '',
            'ShippingAdrees' => $clientData->billing_address ?? '-',
            'ShippingState' => $clientData->billing_state ?? 'MH',
            'ShippingCity' => $clientData->billing_city,
            'MobileNo' => $clientData->MobileNo,
            'TransDate' => date('Y-m-d H:i:s')
          ]);
        }

        if(isset($res->BankList) && !empty($res->BankList)){
          $batchInsert = [];
          foreach ($res->BankList as $key => $value) {
            if(empty($res->BankList[$key]->ifsc)){
              continue;
            }
            $batchInsert[] = [
              'PlantID' => $PlantId,
              'AccountID' => $AccountID,
              'IFSC' => $res->BankList[$key]->ifsc ?? null,
              'BankName' => $res->BankList[$key]->bankName ?? null,
              'BranchName' => $res->BankList[$key]->branchName ?? null,
              'BankAddress' => null,
              'AccountNo' => $res->BankList[$key]->accountNumber ?? null,
              'HolderName' => $res->BankList[$key]->AaccountName ?? null,
              'IsPrimary' => $res->BankList[$key]->IsPrimary ?? 2,
              'cheque_image' => $res->BankList[$key]->cheque_image ?? null,
              'TransDate' => date('Y-m-d H:i:s')
            ];
          }
          $this->Api_model->saveBatchData('BankMaster', $batchInsert);
        }

        // return $this->response(true,'Vendor create', 201, $clientData);
      }
      
      $quote_count = (int)($this->Api_model->getRowData('PurchQuotationMaster', 'COUNT(id) as count', ['FY' => $FY, 'PlantID' => $PlantId])->count ?? 0) + 1;
      $quotation_no = 'PQ'.$FY.$PlantId.str_pad($quote_count, 6, '0', STR_PAD_LEFT);

      // Item calculation
      $historyData = [];
      $totalWt = 0;
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
        if (empty($itemid)) {
          return $this->response(false,'Item not found, sync first',400);
        }
        $itemData = $this->Items_model->getById($itemid);

        $historyData[] = [
          'OrderID'     => $quotation_no,
          'PlantID'     => $PlantId,
          'FY'          => $FY,
          'TransDate'   => date('Y-m-d'),
          'TransDate2'  => date('Y-m-d H:i:s'),
          'TType'       => 'P',
          'TType2'      => 'Quotation',
          'AccountID'   => $clientData->AccountID,
          'ItemID'      => $itemData->ItemID,
          'GodownID'    => '',
          'BasicRate'   => $item['im_ordrate'],
          'SaleRate'    => $item['im_ordrate'],
          'SuppliedIn'  => $itemData->ShortCode,
          'UnitWeight'  => $itemData->UnitWeight,
          'WeightUnit'  => $itemData->UnitWeightIn,
          'CaseQty'     => $itemData->PackingQty,
          'OrderQty'    => $item['im_qty'],
          'Cases'       => ($item['im_qty'] / $itemData->PackingQty),
          'DiscAmt'     => 0,
          'DiscPerc'    => 0,
          'cgst'        => 0,
          'cgstamt'     => 0,
          'sgst'        => 0,
          'sgstamt'     => 0,
          'igst'        => 0,
          'igstamt'     => 0,
          'OrderAmt'    => ($item['im_qty'] * $item['im_ordrate']),
          'NetOrderAmt' => ($item['im_qty'] * $item['im_ordrate']),
          'Ordinalno'   => $i+1,
        ];

        $totalWt += $itemData->UnitWeight * $item['im_qty'];
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
        'FY' => $FY,
        'PurchaseLocation' => $LocationId,
        'QuotatioonID' => $quotation_no,
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
        'TotalWeight' => round($totalWt, 2),
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

      $this->Api_model->saveData('OldErpMapping', ['old_type' => 'Trade', 'old_id' => $trade['doc_ref'], 'new_type' => 'Purchase Quotation', 'new_id' => $quotation_no, 'TransDate' => date('Y-m-d H:i:s')]);

      $this->Api_model->saveBatchData('history', $historyData);

      return $this->response(true,'Purchase Quotation created successfully', 201, ['id' => $poId]);

    } catch (Exception $e) {
      return $this->response(false,'Internal server error',500,['error' => $e->getMessage()]);
    }

    return $this->response(false,'Something went wrong',500);
  }

  private function companyShortCodeDetails($short_code){
    $PlantId = $this->Api_model->getRowData('rootcompany', 'id', ['comp_short' => $short_code])->id ?? null;
    if(empty($PlantId)){
      $payload = [
        'PlantID' => $short_code,
        'access_tokan' => 'fe3fd1f94239c467727c5cae504d4fdd'
      ];
      $response = $this->CurlRequest('https://kirti.globalinfocloud.in/CompanyDetails', 'POST', $payload);
      
      $plantData = json_decode($response);
      $res = $plantData->PlantDetails;

      // create new root company
      $rootData = [
        'company_name'=> $res->PlantName,
        'comp_short'=> $res->PlantID,
        'gst'=> $res->GstNo,
        'address'=> $res->address,
        'pincode'=> $res->pincode,
        'CityID'=> $res->city,
        'city'=> $res->city,
        'state'=> $res->state,
        'state_code'=> $res->state,
        'status'=> 1,
        'datecreated'=> date('Y-m-d H:i:s')
      ];
      $PlantId = $this->Api_model->saveData('rootcompany', $rootData);
    }

    return $PlantId;
  }

  private function locationCenterIdDetails($center_id, $PlantId, $short_code){
    $LocationId = $this->Api_model->getRowData('PlantLocationDetails', 'id', ['CenterID' => $center_id])->id ?? null;
    if (empty($LocationId)) {
      $payload = [
        'CenterID' => $center_id,
        'access_tokan' => 'fe3fd1f94239c467727c5cae504d4fdd'
      ];
      $response = $this->CurlRequest('https://kirti.globalinfocloud.in/CenterDetails', 'POST', $payload);
      $locationData = json_decode($response);
      $res = $locationData->CenterDetails;

      // create new location
      $locationData = [
        'PlantID' => $PlantId,
        'comp_short' => $short_code,
        'CenterID' => $center_id,
        'StateCode' => $res->state,
        'CityID' => $res->city,
        'LocationName' => $res->CenterName,
        'Address' => $res->address,
        'PinCode' => $res->pincode,
        'MobileNo' => $res->MobileNo,
        'IsActive' => 'Y',
        'TransDate' => date('Y-m-d H:i:s')
      ];
      $LocationId = $this->Api_model->saveData('PlantLocationDetails', $locationData);
    }

    return $LocationId;
  }

  public function GateIn(){
    $this->validateMethod('post');

    $data = $this->getInputData();

    $this->validateRequired($data, ['COCD', 'TradeID', 'GateInID', 'VehicleNo']);

    try {
      // check root plant company
      $FY = (date('m') >= 4 ? date('y') : (date('y') - 1));
      $PlantId = $this->companyShortCodeDetails($data['COCD']);

      // check Trade are present or not
      $trade = $this->Api_model->getRowData('OldErpMapping', 'new_id', ['old_type' => 'Trade', 'old_id' => $data['TradeID'], 'new_type' => 'Purchase Quotation']);
      if (empty($trade)) {
        return $this->response(false,'Trade not found, Please sync Trade data', 404);
      }

      // check gate in link or not
      $gateInLinked = $this->Api_model->getRowData('OldErpMapping', 'new_id', ['old_type' => 'GateIn', 'old_id' => $data['GateInID'], 'new_type' => 'GateIn']);
      if ($gateInLinked) {
        return $this->response(false,'Gate In linked with '.$gateInLinked->new_id, 400);
      }

      // find purchase location from quotation
      $quotataionDetails = $this->Api_model->getRowData('PurchQuotationMaster', 'PurchaseLocation', ['QuotatioonID' => $trade->new_id]);
      if (empty($quotataionDetails)) {
        return $this->response(false,'Quotation not found', 404);
      }

      // check vehicle present or not in any gate in on location
      $checkGateIn = $this->Api_model->getRowData('GateMaster', 'GateINID', ['PlantID' => $PlantId, 'LocationID' => $quotataionDetails->PurchaseLocation, 'VehicleNo' => strtoupper($data['VehicleNo']), 'status <' => '7']);
      if ($checkGateIn) {
        $this->Api_model->saveData('OldErpMapping', ['old_type' => 'GateIn', 'old_id' => $data['GateInID'], 'new_type' => 'GateIn', 'new_id' => $checkGateIn->GateINID, 'TransDate' => date('Y-m-d H:i:s')]);

        return $this->response(true, 'Gate In linked with '.$checkGateIn->GateINID, 200, ['GateInID' => $checkGateIn->GateINID]);
      }else{
        // create new gate in
        $GateInNo = $this->generateGateInNo($quotataionDetails->PurchaseLocation);
        $GateInData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'Type' => 'P',
          'LocationID' => $quotataionDetails->PurchaseLocation,
          'GateINID' => $GateInNo,
          'TransDate' => date('Y-m-d H:i:s'),
          'VehicleNo' => strtoupper($data['VehicleNo']),
          'DriverName' => ucwords($data['DriverName'] ?? ''),
          'DriverMobileNo' => $data['DriverMobileNo'] ?? ''
        ];
        $save = $this->Api_model->saveData('GateMaster', $GateInData);
        if($save){
          $this->Api_model->saveData('OldErpMapping', ['old_type' => 'GateIn', 'old_id' => $data['GateInID'], 'new_type' => 'GateIn', 'new_id' => $GateInNo, 'TransDate' => date('Y-m-d H:i:s')]);

          return $this->response(true, 'Gate In created successfully', 200, ['GateInID' => $GateInNo]);
        }else{
          return $this->response(false,'Internal server error',500);
        }
      }
      
      return $this->response(false,'Payload data', 500, $data);
    } catch (Exception $e) {
      return $this->response(false,'Internal server error',500,['error' => $e->getMessage()]);
    }
  }

  private function generateGateInNo($location_id){
    $this->db->where('DATE(TransDate)', date('Y-m-d'));
    $this->db->where('LocationID', $location_id);
    $count = $this->db->count_all_results(db_prefix().'GateMaster');
    $next_no = $count + 1;
    $last_no = str_pad($next_no, 3, '0', STR_PAD_LEFT);
    $location_no = str_pad($location_id, 3, '0', STR_PAD_LEFT);

    return 'G'.$location_no.date('ydm').$last_no;
  }

  public function Inward(){
    $this->validateMethod('post');

    $data = $this->getInputData();

    $this->validateRequired($data, ['COCD', 'TradeID', 'GateInID', 'gross_wt', 'tare_wt', 'StackDetails']);

    try {
      // check root plant company
      $FY = (date('m') >= 4 ? date('y') : (date('y') - 1));
      $PlantId = $this->companyShortCodeDetails($data['COCD']);

      // check Trade are present or not
      $qCheck = $this->Api_model->getRowData('OldErpMapping', 'new_id', ['old_type' => 'Trade', 'old_id' => $data['TradeID'], 'new_type' => 'Purchase Quotation']);
      if (empty($qCheck)) {
        return $this->response(false,'Trade not found, Please sync Trade data', 404);
      }
      $QuotationNo = $qCheck->new_id;

      // find purchase location from quotation
      $quotataionDetails = $this->Api_model->getRowData('PurchQuotationMaster', '*', ['QuotatioonID' => $QuotationNo]);
      if (empty($quotataionDetails)) {
        return $this->response(false,'Quotation not found', 404);
      }

      // check order or create order
      $oCheck = $this->Api_model->getRowData('OldErpMapping', 'new_id', ['old_type' => 'Trade', 'old_id' => $data['TradeID'], 'new_type' => 'Purchase Order']);
      if ($oCheck) {
        $orderId = $oCheck->new_id;
      }else{
        $catCode = $this->Api_model->getRowData('ItemCategoryMaster', 'CategoryCode', ['id' => $quotataionDetails->ItemCategory])->CategoryCode ?? '-';
        $order_count = (int)($this->Api_model->getRowData('PurchQuotationMaster', 'COUNT(id) as count', ['FY' => $FY, 'PlantID' => $PlantId])->count ?? 0) + 1;
        $orderId = 'PO'.$FY.$PlantId.$catCode.str_pad($order_count, 5, '0', STR_PAD_LEFT);

        $insertData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'PurchaseLocation' => $quotataionDetails->PurchaseLocation,
          'PurchID' => $orderId,
          'TransDate' => date('Y-m-d H:i:s'),
          'TransDate2' => date('Y-m-d H:i:s'),
          'ItemType' => $quotataionDetails->ItemType,
          'ItemCategory' => $quotataionDetails->ItemCategory,
          'QuatationID' => $quotataionDetails->QuotatioonID,
          'AccountID' => $quotataionDetails->AccountID,
          'BrokerID' => $quotataionDetails->BrokerID,
          'DeliveryLocation' => $quotataionDetails->DeliveryLocation,
          'DeliveryFrom' => $quotataionDetails->DeliveryFrom,
          'DeliveryTo' => $quotataionDetails->DeliveryTo,
          'VendorDocNo' => '',
          'VendorDocDate' => '',
          'PaymentTerms' => $quotataionDetails->PaymentTerms,
          'FreightTerms' => $quotataionDetails->FreightTerms,
          'GSTIN' => $quotataionDetails->GSTIN,
          'Internal_Remarks' => '',
          'Document_Remark' => '',
          'Attachment' => '',
          'TotalWeight' => $quotataionDetails->TotalWeight,
          'TotalQuantity' => $quotataionDetails->TotalQuantity,
          'ItemAmt' => $quotataionDetails->ItemAmt,
          'DiscAmt' => $quotataionDetails->DiscAmt,
          'TaxableAmt' => $quotataionDetails->TaxableAmt,
          'CGSTAmt' => $quotataionDetails->CGSTAmt,
          'SGSTAmt' => $quotataionDetails->SGSTAmt,
          'IGSTAmt' => $quotataionDetails->IGSTAmt,
          'TDSSection' => $quotataionDetails->TDSSection,
          'TDSPercentage' => $quotataionDetails->TDSPercentage,
          'TDSAmt' => $quotataionDetails->TDSAmt,
          'RoundOffAmt' => $quotataionDetails->RoundOffAmt,
          'NetAmt' => $quotataionDetails->NetAmt
        ];
        $this->Api_model->saveData('PurchaseOrderMaster', $insertData);
        $this->Api_model->saveData('OldErpMapping', ['old_type' => 'Trade', 'old_id' => $data['TradeID'], 'new_type' => 'Purchase Order', 'new_id' => $orderId, 'TransDate' => date('Y-m-d H:i:s')]);
        $this->Api_model->updateData('PurchQuotationMaster', ['status' => 6], ['QuotatioonID' => $QuotationNo]);

        // history data save for order from quotation
        $history = $this->Api_model->getResultData('history', '*', ['OrderID' => $QuotationNo]);
        $batchInsert = [];
        foreach ($history as $key => $value) {
          $batchInsert[] = [
            'PlantID' => $value->PlantID,
            'FY' => $value->FY,
            'OrderID' => $orderId,
            'TransDate' => date('Y-m-d H:i:s'),
            'TransDate2' => date('Y-m-d H:i:s'),
            'TType' => 'P',
            'TType2' => 'Order',
            'AccountID' => $value->AccountID,
            'ItemID' => $value->ItemID,
            'GodownID' => $value->GodownID,
            'Mrp' => $value->Mrp,
            'BasicRate' => $value->BasicRate,
            'SaleRate' => $value->SaleRate,
            'SuppliedIn' => $value->SuppliedIn,
            'UnitWeight' => $value->UnitWeight,
            'WeightUnit' => $value->WeightUnit,
            'CaseQty' => $value->CaseQty,
            'OrderQty' => $value->OrderQty,
            'BilledQty' => $value->BilledQty,
            'Cases' => $value->Cases,
            'DiscPerc' => $value->DiscPerc,
            'DiscAmt' => $value->DiscAmt,
            'cgst' => $value->cgst,
            'cgstamt' => $value->cgstamt,
            'sgst' => $value->sgst,
            'sgstamt' => $value->sgstamt,
            'igst' => $value->igst,
            'igstamt' => $value->igstamt,
            'OrderAmt' => $value->OrderAmt,
            'ChallanAmt' => $value->ChallanAmt,
            'NetOrderAmt' => $value->NetOrderAmt,
            'NetChallanAmt' => $value->NetChallanAmt,
            'Ordinalno' => $value->Ordinalno
          ];
        }
        $this->Api_model->saveBatchData('history', $batchInsert);
      }

      // check Inward or create Inward
      $godownId = $this->Api_model->getRowData('godownmaster', 'id', ['LocationID' => $quotataionDetails->PurchaseLocation])->id ?? 0;
      $iCheck = $this->Api_model->getRowData('OldErpMapping', 'new_id', ['old_type' => 'Trade', 'old_id' => $data['TradeID'], 'new_type' => 'Purchase Inward']);
      if ($iCheck) {
        $InwardId = $iCheck->new_id;
      }else{
        $orderDetails = $this->Api_model->getRowData('PurchaseOrderMaster', '*', ['PurchID' => $orderId]);

        $catCode = $this->Api_model->getRowData('ItemCategoryMaster', 'CategoryCode', ['id' => $orderDetails->ItemCategory])->CategoryCode ?? '-';
        $order_count = (int)($this->Api_model->getRowData('PurchInwardsMaster', 'COUNT(id) as count', ['FY' => $FY, 'PlantID' => $PlantId])->count ?? 0) + 1;
        $InwardId = 'INV'.$FY.$PlantId.$catCode.str_pad($order_count, 5, '0', STR_PAD_LEFT);

        $insertData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'PurchaseLocation' => $orderDetails->PurchaseLocation,
          'InwardsID' => $InwardId,
          'OrderID' => $orderId,
          'GodownID' => $godownId,
          'TransDate' => date('Y-m-d H:i:s'),
          'TransDate2' => date('Y-m-d H:i:s'),
          'ItemType' => $orderDetails->ItemType,
          'ItemCategory' => $orderDetails->ItemCategory,
          'AccountID' => $orderDetails->AccountID,
          'BrokerID' => $orderDetails->BrokerID,
          'VendorLocation' => $orderDetails->DeliveryLocation,
          'DeliveryFrom' => $orderDetails->DeliveryFrom,
          'DeliveryTo' => $orderDetails->DeliveryTo,
          'PaymentTerms' => $orderDetails->PaymentTerms,
          'FreightTerms' => $orderDetails->FreightTerms,
          'GSTIN' => $orderDetails->GSTIN,
          'TotalWeight' => $orderDetails->TotalWeight,
          'TotalQuantity' => $orderDetails->TotalQuantity,
          'ItemAmt' => $orderDetails->ItemAmt,
          'DiscAmt' => $orderDetails->DiscAmt,
          'TaxableAmt' => $orderDetails->TaxableAmt,
          'CGSTAmt' => $orderDetails->CGSTAmt,
          'SGSTAmt' => $orderDetails->SGSTAmt,
          'IGSTAmt' => $orderDetails->IGSTAmt,
          'TDSSection' => $orderDetails->TDSSection,
          'TDSPercentage' => $orderDetails->TDSPercentage,
          'TDSAmt' => $orderDetails->TDSAmt,
          'RoundOffAmt' => $orderDetails->RoundOffAmt,
          'NetAmt' => $orderDetails->NetAmt
        ];
        $this->Api_model->saveData('PurchInwardsMaster', $insertData);
        $this->Api_model->saveData('OldErpMapping', ['old_type' => 'Trade', 'old_id' => $data['TradeID'], 'new_type' => 'Purchase Inward', 'new_id' => $InwardId, 'TransDate' => date('Y-m-d H:i:s')]);
        $this->Api_model->updateData('PurchaseOrderMaster', ['status' => 6], ['PurchID' => $orderId]);

        // history data save for order from quotation
        $history = $this->Api_model->getResultData('history', '*', ['OrderID' => $orderId]);
        $batchInsert = [];
        foreach ($history as $key => $value) {
          $batchInsert[] = [
            'PlantID' => $value->PlantID,
            'FY' => $value->FY,
            'OrderID' => $InwardId,
            'BillID' => $orderId,
            'TransDate' => date('Y-m-d H:i:s'),
            'TransDate2' => date('Y-m-d H:i:s'),
            'TType' => 'P',
            'TType2' => 'Inward',
            'AccountID' => $value->AccountID,
            'ItemID' => $value->ItemID,
            'GodownID' => $value->GodownID,
            'Mrp' => $value->Mrp,
            'BasicRate' => $value->BasicRate,
            'SaleRate' => $value->SaleRate,
            'SuppliedIn' => $value->SuppliedIn,
            'UnitWeight' => $value->UnitWeight,
            'WeightUnit' => $value->WeightUnit,
            'CaseQty' => $value->CaseQty,
            'OrderQty' => $value->OrderQty,
            'BilledQty' => $value->BilledQty,
            'Cases' => $value->Cases,
            'DiscPerc' => $value->DiscPerc,
            'DiscAmt' => $value->DiscAmt,
            'cgst' => $value->cgst,
            'cgstamt' => $value->cgstamt,
            'sgst' => $value->sgst,
            'sgstamt' => $value->sgstamt,
            'igst' => $value->igst,
            'igstamt' => $value->igstamt,
            'OrderAmt' => $value->OrderAmt,
            'ChallanAmt' => $value->ChallanAmt,
            'NetOrderAmt' => $value->NetOrderAmt,
            'NetChallanAmt' => $value->NetChallanAmt,
            'Ordinalno' => $value->Ordinalno
          ];
        }
        $this->Api_model->saveBatchData('history', $batchInsert);
      }
      
      // check gate in present or not
      $gCheck = $this->Api_model->getRowData('OldErpMapping', 'new_id', ['old_type' => 'GateIn', 'old_id' => $data['GateInID'], 'new_type' => 'GateIn']);
      if (empty($gCheck)) {
        return $this->response(false,'Gate In not found, Please sync Gate In data', 404);
      }
      $GateINID = $gCheck->new_id;
      $this->Api_model->updateData('GateMaster', ['InwardID' => $InwardId], ['GateINID' => $GateINID]);
      $gateInStatus = $this->Api_model->getRowData('GateMaster', 'status', ['GateINID' => $GateINID]);

      // save gross weight data
      $value = ['gross_weight' => $data['gross_wt'], 'TopImage' => '', 'FrontImage' => '', 'SideImage' => ''];
      $where = ['GateINID' => $GateINID, 'type' => 'GrossWeight'];
      $exist = $this->Api_model->getRowData('GateMasterDetails', '*', $where);
      if ($exist) {
        $fetchValue = json_decode($exist->value);
        $value['TopImage'] = $fetchValue->TopImage ?? '';
        $value['FrontImage'] = $fetchValue->FrontImage ?? '';
        $value['SideImage'] = $fetchValue->SideImage ?? '';
        
        $value = json_encode($value);

        $this->Api_model->updateData('GateMasterDetails', ['value' => $value, 'Lupdate' => date('Y-m-d H:i:s')], $where);
      }else{
        $saveData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'GateINID' => $GateINID,
          'type' => 'GrossWeight',
          'value' => json_encode($value),
          'TransDate' => date('Y-m-d H:i:s')
        ];
        $this->Api_model->saveData('GateMasterDetails', $saveData);
      }

      if($gateInStatus < 2){
        $this->Api_model->updateData('GateMaster', ['status' => 2], ['GateINID' => $GateINID]);
        $gateInStatus = 2;
      }

      // stack qc details save as per items
      $StackDetails = $data['StackDetails'];
      $i = 0;
      foreach ($StackDetails as $sd) {
        $ItemId = $this->Api_model->getRowData('items', 'ItemID', ['old_item_id' => $sd['ItemID']])->ItemID ?? null;
        if (empty($ItemId)) {
          return $this->response(false,'Item not found, sync first',400);
        }
        // $itemData = $this->Items_model->getById($ItemId);

        $insertData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'PurchID' => $orderId,
          'GateINID' => $GateINID,
          'InwardID' => $InwardId,
          'QCID' => $i+1,
          'CenterQCApprove' => 'Y',
          'ROQCApprove' => 'Y',
          'HOQCApprove' => 'Y',
          'TType' => 'P',
          'ItemID' => $ItemId,
          'AccountID' => $quotataionDetails->AccountID,
          'LocationID' => $quotataionDetails->PurchaseLocation,
          'WHID' => $godownId,
          'CHID' => $sd['ChamberID'],
          'StackID' => $sd['Stack'],
          'LOTID' => $sd['LotID'],
          'Weight' => $sd['Weight'],
          'BagQty' => $sd['BagQty']
        ];

        $where = [
          'GateINID' => $GateINID,
          'ItemID' => $ItemId,
          'TType' => 'P',
        ];

        $check = $this->Api_model->checkDuplicate('stockInventory',$where);

        if($check){
          $insertData['Lupdate'] = date('Y-m-d H:i:s');
          $this->Api_model->updateData('stockInventory',$insertData,$where);
          $last_id = $this->Api_model->getRowData('stockInventory','id',$where)->id;
        }else{
          $insertData['TransDate'] = date('Y-m-d H:i:s');
          $last_id = $this->Api_model->saveData('stockInventory',$insertData);
        }

        if(!empty($sd['QCDetails'])){
          foreach($sd['QCDetails'] as $qc){
            $multiData = [
              'PurchID' => $orderId,
              'GateINID' => $GateINID,
              'InwardID' => $InwardId,
              'TType' => 'P',
              'ItemID' =>  $ItemId,
              'layer_number' => $last_id,
              'ItemParameterID' => $qc['QCParameterID'],
              'ParameterValue' => $qc['QCParameterValue'],
              'EParameterValue' => $qc['QCParameterValue'],
              'HParameterValue' => $qc['QCParameterValue'],
              'deductionAmt' => $qc['DeductionAmt'],
            ];

            $whereQC = [
              'GateINID' => $GateINID,
              'ItemID' => $ItemId,
              'ItemParameterID' => $qc['QCParameterID'],
              'layer_number' => $last_id
            ];

            $checkQC = $this->Api_model->checkDuplicate('QCParameterValues',$whereQC);

            if($checkQC){
              $multiData['Lupdate']=date('Y-m-d H:i:s');
              $this->Api_model->updateData('QCParameterValues',$multiData,$whereQC);
            }else{
              $multiData['TransDate']=date('Y-m-d H:i:s');
              $this->Api_model->saveData('QCParameterValues',$multiData);
            }

          }

        }
      }

      if($gateInStatus < 4){
        $this->Api_model->updateData('GateMaster', ['status' => 4], ['GateINID' => $GateINID]);
        $gateInStatus = 4;
      }

      // save tare weight data
      $value = ['tare_weight' => $data['tare_wt'], 'TopImage' => '', 'FrontImage' => '', 'SideImage' => ''];
      $where = ['GateINID' => $GateINID, 'type' => 'TareWeight'];
      $exist = $this->Api_model->getRowData('GateMasterDetails', '*', $where);
      if ($exist) {
        $fetchValue = json_decode($exist->value);
        $value['TopImage'] = $fetchValue->TopImage ?? '';
        $value['FrontImage'] = $fetchValue->FrontImage ?? '';
        $value['SideImage'] = $fetchValue->SideImage ?? '';
        
        $value = json_encode($value);

        $this->Api_model->updateData('GateMasterDetails', ['value' => $value, 'Lupdate' => date('Y-m-d H:i:s')], $where);
      }else{
        $saveData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'GateINID' => $GateINID,
          'type' => 'TareWeight',
          'value' => json_encode($value),
          'TransDate' => date('Y-m-d H:i:s')
        ];
        $this->Api_model->saveData('GateMasterDetails', $saveData);
      }

      if($gateInStatus < 5){
        $this->Api_model->updateData('GateMaster', ['status' => 5], ['GateINID' => $GateINID]);
        $gateInStatus = 5;
      }

      // save gate out time
      if(!empty($data['gate_out_time'])){
        $insertData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'GateINID' => $GateINID,
          'type' => 'GateOut',
          'value' => json_encode(['Time' => $data['gate_out_time']])
        ];
        $where = ['GateINID' => $GateINID, 'type' => 'GateOut'];
        $exist = $this->Api_model->checkDuplicate('GateMasterDetails', $where);
        if($exist){
          $insertData['Lupdate'] = date('Y-m-d H:i:s');
          $this->Api_model->updateData('GateMasterDetails', $insertData, $where);
        }else{
          $insertData['TransDate'] = date('Y-m-d H:i:s');
          $this->Api_model->saveData('GateMasterDetails', $insertData);
        }

        if($gateInStatus < 6){
          $this->Api_model->updateData('GateMaster', ['status' => 6], ['GateINID' => $GateINID]);
          $gateInStatus = 6;
        }
      }

      // save gate exit time
      if(!empty($data['gate_exit_time'])){
        $insertData = [
          'PlantID' => $PlantId,
          'FY' => $FY,
          'GateINID' => $GateINID,
          'type' => 'GateExit',
          'value' => json_encode(['Time' => $data['gate_exit_time']])
        ];
        $where = ['GateINID' => $GateINID, 'type' => 'GateExit'];
        $exist = $this->Api_model->checkDuplicate('GateMasterDetails', $where);
        if($exist){
          $insertData['Lupdate'] = date('Y-m-d H:i:s');
          $this->Api_model->updateData('GateMasterDetails', $insertData, $where);
        }else{
          $insertData['TransDate'] = date('Y-m-d H:i:s');
          $this->Api_model->saveData('GateMasterDetails', $insertData);
        }

        if($gateInStatus < 7){
          $this->Api_model->updateData('GateMaster', ['status' => 7], ['GateINID' => $GateINID]);
          $gateInStatus = 7;
        }
      }
      
      return $this->response(true,'All details saved', 200);
    } catch (Exception $e) {
      return $this->response(false,'Internal server error',500,['error' => $e->getMessage()]);
    }
  }
  
}