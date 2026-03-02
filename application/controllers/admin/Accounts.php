<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounts extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Accounts_model');
	}

  /* =========================
		* ACCOUNTS LEDGER
		* ========================= */

  public function Ledger(){
    $data['title'] = 'Accounts Ledger';
    
    $data['accountSubGroup2'] = $this->Accounts_model->getDropdown('AccountSubGroup2', 'SubActGroupID, SubActGroupName', ['IsAccountHead' => 'Y'], 'SubActGroupName', 'ASC');
    $data['hsn'] 				= $this->Accounts_model->getDropdown('hsn', 'id, name', ['status' => '1'], 'name', 'ASC');
		$data['gst'] 				= $this->Accounts_model->getDropdown('taxes', 'id, name, taxrate', '', 'taxrate', 'ASC');
		$data['all_ledger'] 		= $this->Accounts_model->getAllLeadger();

    $this->load->view('admin/Accounts/CreateLedger', $data);
  }

  public function SaveLedger(){
    $PlantID = $this->session->userdata('root_company');
    $UserID = $this->session->userdata('username');

    if (!$this->input->post()) {
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }

    $data = $this->input->post(null, true);

    $field_names = array_keys($data);
    foreach ($field_names as $key => $value) {
      $data[$value] = trim($data[$value]);
    }
    $required_fields = [ 'account_group2', 'account_code', 'account_name'];
    if ($data['is_bank'] == 'Y') {
      $required_fields[] = 'ifsc_code';
      $required_fields[] = 'bank_name';
      $required_fields[] = 'branch_name';
      $required_fields[] = 'bank_address';
      $required_fields[] = 'account_number';
      $required_fields[] = 'account_holder_name';
    }
    if($data['is_active'] == 'N') {
      $required_fields[] = 'blocked_reason';
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
    // Save Client Details
    $actSubGroupID1 = $this->Accounts_model->getRowData('AccountSubGroup2', 'SubActGroupID1', ['SubActGroupID' => $data['account_group2']])->SubActGroupID1;
    $actMainGroupID = $this->Accounts_model->getRowData('AccountSubGroup1', 'ActGroupID', ['SubActGroupID1' => $actSubGroupID1])->ActGroupID;
    $insertData = [
      'PlantID' => $PlantID,
      'AccountID' => $data['account_code'],
      'company' => $data['account_name'],
      'ActMainGroupID' => $actMainGroupID,
      'ActSubGroupID1' => $actSubGroupID1,
      'ActSubGroupID2' => $data['account_group2'],
      'IsActive' => $data['is_active'],
      'DeActiveReason' => $data['blocked_reason']
    ];
    $check = $this->Accounts_model->checkDuplicate('clients', ['AccountID' => $data['account_code']]);
    if(!$check) {
      $success = $this->Accounts_model->saveData('clients', $insertData);
    }else{
      $success = $this->Accounts_model->updateData('clients', $insertData, ['AccountID' => $data['account_code']]);
    }

    // Save Contact Details
    $insertData = [
      'PlantID' => $PlantID,
      'AccountID' => $data['account_code'],
      'firstname' => $data['account_name'],
      'IsActive' => $data['is_active']
    ];
    $check1 = $this->Accounts_model->checkDuplicate('contacts', ['AccountID' => $data['account_code']]);
    if(!$check1) {
      $this->Accounts_model->saveData('contacts', $insertData);
    }else{
      $this->Accounts_model->updateData('contacts', $insertData, ['AccountID' => $data['account_code']]);
    }

    // Save Bank Details
    if ($data['is_bank'] == 'Y') {
      $insertData = [
        'AccountID' => $data['account_code'],
        'IFSC' => $data['ifsc_code'],
        'BankName' => $data['bank_name'],
        'BranchName' => $data['branch_name'],
        'BankAddress' => $data['bank_address'],
        'AccountNo' => $data['account_number'],
        'HolderName' => $data['account_holder_name']
      ];
      $check2 = $this->Accounts_model->checkDuplicate('BankMaster', ['AccountID' => $data['account_code']]);
      if(!$check2) {
        $this->Accounts_model->saveData('BankMaster', $insertData);
      }else{
        $this->Accounts_model->updateData('BankMaster', $insertData, ['AccountID' => $data['account_code']]);
      }
    }
    if ($success) {
      $ledgerDetails = $this->Accounts_model->getLedgerDetails($data['account_code']);
      echo json_encode([
        'success' => true,
        'message' => 'Ledger '.((!$check) ? 'created' : 'updated').' successfully...',
        'data' => $ledgerDetails
      ]);
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'Failed to '.((!$check) ? 'create' : 'update').' ledger.'
      ]);
    }

  }

  public function NextLedgerCode() {
    if (!$this->input->post()) {
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }
    $data = $this->input->post(null, true);
    $actSubGroupID2 = $data['accountSubGroupId2'];
    if(empty($actSubGroupID2)){
      echo json_encode(['success'=>false,'message'=>'Account group is required']);
      return;
    }
    
    $nextNumber = $this->Accounts_model->getNextLedgerCode(null);
    $nextLedgerCode = 'L'.(str_repeat('0', max(0, 5 - strlen($nextNumber)))).$nextNumber;

    echo json_encode(['success'=>true, 'data'=>$nextLedgerCode]);
  }

  public function GetLedgerDetails(){
    if (!$this->input->post()) {
      echo json_encode(['success'=>false,'message'=>'Invalid request']);
      return;
    }
    $data = $this->input->post(null, true);
    $ledgerId = $data['ledgerId'];
    if(empty($ledgerId)){
      echo json_encode(['success'=>false,'message'=>'Account ID is required']);
      return;
    }
    
    $ledgerDetails = $this->Accounts_model->getLedgerDetails($ledgerId);

    echo json_encode(['success'=>true, 'data'=>$ledgerDetails]);
  }
  /* =========================
		* BANK RELATED API CURL
		* ========================= */
  public function fetchBankDetailsFromIFSC() {
    $ifsc_code = $this->input->post('ifsc_code');
    
    if (empty($ifsc_code)) {
      echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
      ]);
      return;
    }
    
    // Validate IFSC code format (11 characters)
    if (strlen($ifsc_code) != 11) {
      echo json_encode([
        'success' => false,
        'message' => 'Invalid IFSC code format'
      ]);
      return;
    }

    $bank_details = $this->getBankDetailsFromIFSC($ifsc_code);
    
    if ($bank_details) {
      echo json_encode([
        'success' => true,
        'message' => 'Bank details fetched successfully',
        'data' => $bank_details
      ]);
    } else {
      echo json_encode([
        'success' => false,
        'message' => 'Bank details not found'
      ]);
    }
  }

  private function getBankDetailsFromIFSC($ifsc_code) {
    // First try: RBI IFSC API (Official)
    $response = $this->callIFSCAPI("https://ifsc.razorpay.com/{$ifsc_code}", $ifsc_code);
    if ($response) {
      return $response;
    }
    
    // Second try: Alternative API
    $response = $this->callAlternativeAPI($ifsc_code);
    return $response;
  }
  
  private function callIFSCAPI($url, $ifsc_code) {
    try {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      
      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      
      if ($http_code == 200 && $response) {
        $data = json_decode($response, true);
        
        if (isset($data['BANK']) && isset($data['BRANCH'])) {
          return array(
            'BANK' => isset($data['BANK']) ? $data['BANK'] : '',
            'BRANCH' => isset($data['BRANCH']) ? $data['BRANCH'] : '',
            'ADDRESS' => isset($data['ADDRESS']) ? $data['ADDRESS'] : '',
            'IFSC' => $ifsc_code
          );
        }
      }
    } catch (Exception $e) {
      log_message('error', 'IFSC API Error: ' . $e->getMessage());
    }
    
    return false;
  }
  
  private function callAlternativeAPI($ifsc_code) {
    try {
      // Using Indian Bank IFSC API
      $url = "https://bank-api.example.com/ifsc/{$ifsc_code}";
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      
      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      
      if ($http_code == 200 && $response) {
        $data = json_decode($response, true);
        
        if ($data) {
          return array(
            'BANK' => isset($data['bank_name']) ? $data['bank_name'] : (isset($data['BANK']) ? $data['BANK'] : ''),
            'BRANCH' => isset($data['branch_name']) ? $data['branch_name'] : (isset($data['BRANCH']) ? $data['BRANCH'] : ''),
            'ADDRESS' => isset($data['address']) ? $data['address'] : (isset($data['ADDRESS']) ? $data['ADDRESS'] : ''),
            'IFSC' => $ifsc_code
          );
        }
      }
    } catch (Exception $e) {
      log_message('error', 'Alternative API Error: ' . $e->getMessage());
    }
    
    return false;
  }

  public function verifyBankAccount(){
    $bank_ac_no = $this->input->post('bank_ac_no');
    $ifsc_code = $this->input->post('ifsc_code');
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';

    $curl = curl_init();
    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => 'https://kyc-api.surepass.io/api/v1/bank-verification/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
          "id_number": "' . $bank_ac_no . '",
          "ifsc": "' . $ifsc_code . '",
          "ifsc_details": true
        }',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Authorization: Bearer ' . $token . ''
        ),
      )
    );
    $response = curl_exec($curl);
    curl_close($curl);
    echo $response;
  }

}