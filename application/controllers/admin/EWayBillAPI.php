<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EWayBillAPI extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('StockTransfer_model');
    }

    public function index()
    {
        $payload = json_decode($this->input->post('payload'), true);

        if (empty($payload)) {
            echo json_encode(['success' => false, 'message' => 'No payload provided']);
            return;
        }

        $id = $this->input->post('TransferID');   // numeric DB primary key

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'TransferID not provided']);
            return;
        }

        $this->setEWBCredential();
        
        $payload['fromGstin'] = $this->ewb_gstin;

        $authResponse = $this->authenticateEWBPortel();

        if (empty($authResponse) || !$authResponse['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'Authentication failed',
                'details' => $authResponse,
            ]);
            return;
        }

        $authToken = $authResponse['api_response']['authToken']
            ?? $authResponse['api_response']['header']['authToken']
            ?? $authResponse['api_response']['header']['txn']
            ?? '';

        if (empty($payload['fromGstin'])) {
            $payload['fromGstin'] = $this->ewb_gstin;
        }

        $result = $this->GenerateEWayBill($payload, (int) $id, (string) $authToken);

        echo json_encode($result);
    }

    private $ewb_ip_address;
    private $ewb_client_id;
    private $ewb_client_secret;
    private $ewb_gstin;
    private $ewb_email;
    private $ewb_username;
    private $ewb_password;
    private $ewb_accept;

    protected $ewb_error_codes = [
        '100' => 'Invalid Json',
        '101' => 'Invalid Username',
        '102' => 'Invalid Password',
        '103' => 'Invalid Client-Id',
        '104' => 'Invalid Client Secret',
        '105' => 'Invalid Token',
        '106' => 'Token Expired',
        '107' => 'Authentication failed. Pls. inform the helpdesk',
        '108' => 'Invalid login credentials.',
        '109' => 'Decryption of data failed',
        '110' => 'Invalid Client-ID/Client-Secret',
        '111' => 'GSTIN is not registered to this GSP',
        '112' => 'Inactive Client',
        '113' => 'Inactive User',
        '114' => 'Technical Error, Pl. contact the helpdesk.',
        '115' => 'Request payload data cannot be empty',
        '116' => 'Auth token is not valid for this client',
        '117' => 'This option is not enabled in Eway Bill 2',
        '118' => 'Try after 5 minutes',
        '201' => 'Invalid Supply Type',
        '202' => 'Invalid Sub-supply Type',
        '203' => 'Sub-transaction type does not belong to transaction type',
        '204' => 'Invalid Document type',
        '205' => 'Document type does not match with transaction & Sub trans type',
        '206' => 'Invalid Invoice Number',
        '207' => 'Invalid Invoice Date',
        '208' => 'Invalid Supplier GSTIN / Enrolled URP',
        '209' => 'Blank Supplier Address',
        '210' => 'Invalid or Blank Supplier PIN Code',
        '211' => 'Invalid or Blank Supplier State Code',
        '212' => 'Invalid Consignee GSTIN / Enrolled URP',
        '213' => 'Invalid Consignee Address',
        '214' => 'Invalid Consignee PIN Code',
        '215' => 'Invalid Consignee State Code',
        '216' => 'Invalid HSN Code',
        '217' => 'Invalid UQC Code',
        '218' => 'Invalid Tax Rate for Intra State Transaction',
        '219' => 'Invalid Tax Rate for Inter State Transaction',
        '220' => 'Invalid Trans mode',
        '221' => 'Invalid Approximate Distance',
        '222' => 'Invalid Transporter Id',
        '223' => 'Invalid Transaction Document Number',
        '224' => 'Invalid Transaction Date',
        '225' => 'Invalid Vehicle Number Format',
        '226' => 'Both Transaction and Vehicle Number Blank',
        '227' => 'User Gstin cannot be blank',
        '228' => 'User id cannot be blank',
        '229' => 'Supplier name is required',
        '230' => 'Supplier place is required',
        '231' => 'Consignee name is required',
        '232' => 'Consignee place is required',
        '233' => 'Eway bill does not contain any items',
        '234' => 'Total amount/Taxable amount is mandatory',
        '235' => 'Tax rates for Intra state transaction is blank',
        '236' => 'Tax rates for Inter state transaction is blank',
        '237' => 'Invalid client-Id/client-secret',
        '238' => 'Invalid auth token',
        '239' => 'Invalid action',
        '240' => 'Could not generate eway bill, pls contact helpdesk',
        '242' => 'Invalid State Code',
        '250' => 'Invalid Vehicle Release Date Format',
        '251' => 'CGST and SGST TaxRate should be same',
        '252' => 'Invalid CGST Tax Rate',
        '253' => 'Invalid SGST Tax Rate',
        '254' => 'Invalid IGST Tax Rate',
        '255' => 'Invalid CESS Rate',
        '278' => 'User Gstin does not match with Transporter Id',
        '280' => 'Status is not ACTIVE',
        '281' => 'Eway Bill is already expired hence update transporter is not allowed.',
        '282' => 'At least 4 digit HSN code is mandatory for taxpayers with turnover less than 5Cr.',
        '283' => 'At least 6 digit HSN code is mandatory for taxpayers with turnover 5Cr. and above',
        '301' => 'Invalid eway bill number',
        '302' => 'Invalid transporter mode',
        '303' => 'Vehicle number is required',
        '304' => 'Invalid vehicle format',
        '305' => 'Place from is required',
        '306' => 'Invalid from state',
        '307' => 'Invalid reason',
        '308' => 'Invalid remarks',
        '309' => 'Could not update vehicle details, pl contact helpdesk',
        '311' => 'Validity period lapsed, you cannot update vehicle details',
        '312' => 'This eway bill is either not generated by you or cancelled',
        '315' => 'Validity period lapsed, you cannot cancel this eway bill',
        '316' => 'Eway bill is already verified, you cannot cancel it',
        '317' => 'Could not cancel eway bill, please contact helpdesk',
        '320' => 'Invalid state to',
        '321' => 'Invalid place to',
        '322' => 'Could not generate consolidated eway bill',
        '325' => 'Could not retrieve data',
        '326' => 'Could not retrieve GSTIN details for the given GSTIN number',
        '327' => 'Could not retrieve data from hsn',
        '328' => 'Could not retrieve transporter details from gstin',
        '329' => 'Could not retrieve States List',
        '330' => 'Could not retrieve UQC list',
        '331' => 'Could not retrieve Error code',
        '334' => 'Could not retrieve user details by userid',
        '336' => 'Could not retrieve transporter data by gstin',
        '337' => 'Could not retrieve HSN details for the given HSN number',
        '338' => 'You cannot update transporter details, as the current transporter is already entered Part B details of the eway bill',
        '339' => 'You are not assigned to update the transporter details of this eway bill',
        '341' => 'This e-way bill is generated by you and hence you cannot reject it',
        '342' => 'You cannot reject this e-way bill as you are not the other party to do so',
        '343' => 'This e-way bill is cancelled',
        '344' => 'Invalid eway bill number',
        '345' => 'Validity period lapsed, you cannot reject the e-way bill',
        '346' => 'You can reject the e-way bill only within 72 hours from generated time',
        '347' => 'Validation of eway bill number failed, while rejecting ewaybill',
        '350' => 'Could not generate consolidated eway bill',
        '351' => 'Invalid state code',
        '352' => 'Invalid rfid date',
        '353' => 'Invalid location code',
        '354' => 'Invalid rfid number',
        '355' => 'Invalid Vehicle Number Format',
        '356' => 'Invalid wt on bridge',
        '357' => 'Could not retrieve eway bill details, pl. contact helpdesk',
        '358' => 'GSTIN passed in request header is not matching with the user gstin mentioned in payload JSON',
        '359' => 'User GSTIN should match to GSTIN(from) for outward transactions',
        '360' => 'User GSTIN should match to GSTIN(to) for inward transactions',
        '361' => 'Invalid Vehicle Type',
        '362' => 'Transporter document date cannot be earlier than the invoice date',
        '363' => 'E-way bill is not enabled for intra state movement for your state',
        '364' => 'Error in verifying eway bill',
        '365' => 'Error in verifying consolidated eway bill',
        '366' => 'You will not get the ewaybills generated today, however you can access the ewaybills of yesterday',
        '367' => 'Could not retrieve data for officer login',
        '368' => 'Could not update transporter',
        '369' => 'GSTIN/Transin passed in request header should match with the transported Id mentioned in payload JSON',
        '370' => 'GSTIN/Transin passed in request header should not be the same as supplier(fromGSTIN) or recipient(toGSTIN)',
        '371' => 'Invalid or Blank Supplier Ship-to State Code',
        '372' => 'Invalid or Blank Consignee Ship-to State Code',
        '373' => 'The Supplier ship-from state code should be Other Country for Sub Supply Type - Export',
        '374' => 'The Consignee pin code should be 999999 for Sub Supply Type - Export',
        '375' => 'The Supplier ship-to state code should be Other Country for Sub Supply Type - Import',
        '376' => 'The Supplier pin code should be 999999 for Sub Supply Type - Import',
        '377' => 'Sub Supply Type is mentioned as Others, the description for that is mandatory',
        '378' => 'The supplier or consignee belong to SEZ, Inter state tax rates are applicable here',
        '379' => 'Eway Bill cannot be extended. Already Cancelled',
        '380' => 'Eway Bill cannot be Extended. Not in Active State',
        '381' => 'There is No PART-B/Vehicle Entry. So Please Update Vehicle Information.',
        '382' => 'You Cannot Extend as EWB can be Extended only 8 hours before or after w.r.t Validity of EWB.',
        '383' => 'Error While Extending. Please Contact Helpdesk.',
        '384' => 'You are not current transporter or Generator of the ewayBill, with no transporter details.',
        '385' => 'For Rail/Ship/Air transDocNo and transDocDate is mandatory',
        '386' => 'Reason Code, Remarks is mandatory.',
        '387' => 'No Record Found for Entered consolidated eWay bill.',
        '388' => 'Exception in regeneration of consolidated eWayBill. Please Contact helpdesk',
        '389' => 'Remaining Distance Required',
        '390' => 'Remaining Distance cannot be greater than Actual Distance.',
        '391' => 'No eway bill of specified tripsheet, neither ACTIVE nor Valid.',
        '392' => 'Tripsheet is already cancelled, Hence Regeneration is not possible',
        '393' => 'Invalid GSTIN',
        '394' => 'For other than Road Transport, TransDoc number is required',
        '395' => 'Eway Bill Number should be numeric only',
        '396' => 'Either Eway Bill Number Or Consolidated Eway Bill Number is required for Verification',
        '397' => 'Error in Multi Vehicle Movement Initiation',
        '398' => 'Eway Bill Item List is Empty',
        '399' => 'Unit Code is not matching with any of the Unit Code from ItemList',
        '400' => 'Total quantity is exceeding from multi vehicle movement initiation quantity',
        '401' => 'Error in inserting multi vehicle details',
        '402' => 'Total quantity cannot be less than or equal to zero',
        '403' => 'Error in multi vehicle details',
        '405' => 'No record found for multi vehicle update with specified ewbNo groupNo and old vehicleNo/transDocNo with status as ACT',
        '406' => 'Group number cannot be empty or zero',
        '407' => 'Invalid old vehicle number format',
        '408' => 'Invalid new vehicle number format',
        '409' => 'Invalid old transDoc number',
        '410' => 'Invalid new transDoc number',
        '411' => 'Multi Vehicle Initiation data is not there for specified ewayBill and group No',
        '412' => 'Multi Vehicle movement is already Initiated, hence PART B updation not allowed',
        '413' => 'Unit Code is not matching with unit code of first initiation',
        '415' => 'Error in fetching verification data for officer',
        '416' => 'Date range is exceeding allowed date range',
        '417' => 'No verification data found for officer',
        '418' => 'No record found',
        '419' => 'Error in fetching search result for taxpayer/transporter',
        '420' => 'Minimum six characters required for Tradename/legalname search',
        '421' => 'Invalid pincode',
        '422' => 'Invalid mobile number',
        '423' => 'Error in fetching ewaybill list by vehicle number',
        '424' => 'Invalid PAN number',
        '432' => 'Invalid vehicle released value',
        '433' => 'Invalid goods detained parameter value',
        '434' => 'Invalid ewbNoAvailable parameter value',
        '435' => 'Part B is already updated, hence updation is not allowed',
        '436' => 'Invalid email id',
        '442' => 'Error in inserting verification details',
        '443' => 'Invalid invoice available value',
        '444' => 'This eway bill cannot be cancelled as it is generated from Eway Bill 1',
        '445' => 'This eway bill cannot be cancelled as it is generated from Eway Bill 2',
        '446' => 'Transport details cannot be updated here as it is generated from Eway Bill 1',
        '447' => 'Transport details cannot be updated here as it is generated from Eway Bill 2',
        '448' => 'Part B cannot be updated as this Ewaybill Part A is generated in Eway Bill 1',
        '449' => 'Part B cannot be updated as this Ewaybill Part A is generated in Eway Bill 2',
        '450' => 'For outward-export ewaybill, To GSTIN has to be either URP or SEZ',
        '451' => 'For inward-import ewaybill, From GSTIN has to be either URP or SEZ',
        '452' => 'Consolidated Ewaybill cannot be generated as this Ewaybill Part A is generated in Eway Bill 2',
        '600' => 'Invalid category',
        '601' => 'Invalid date format',
        '602' => 'Invalid File Number',
        '603' => 'For file details file number is required',
        '604' => 'E-way bill(s) are already generated for the same document number, you cannot generate again on same document number',
        '605' => 'If the goods are moving towards transporter location, the value of toTransporterLoc should be Y',
        '606' => 'Vehicle type is mandatory, if the goods are moving to transporter place',
        '607' => 'Dispatch from GSTIN is mandatory',
        '608' => 'Ship to from GSTIN is mandatory',
        '609' => 'Invalid ship to from GSTIN',
        '610' => 'Invalid dispatch from GSTIN',
        '611' => 'Invalid document type for the given supply type',
        '612' => 'Invalid transaction type',
        '614' => 'Transaction type is mandatory',
        '617' => 'Bill-from and dispatch-from gstin should not be same for this transaction type',
        '618' => 'Bill-to and ship-to gstin should not be same for this transaction type',
        '619' => 'Transporter Id is mandatory for generation of Part A slip',
        '620' => 'Total invoice value cannot be less than the sum of total assessible value and tax values',
        '621' => 'Trans mode is mandatory since vehicle number is present',
        '622' => 'Trans mode is mandatory since trans doc number is present',
        '627' => 'Total value should not be negative',
        '628' => 'Total invoice value should not be negative',
        '629' => 'IGST value should not be negative',
        '630' => 'CGST value should not be negative',
        '631' => 'SGST value should not be negative',
        '632' => 'Cess value should not be negative',
        '633' => 'Cess non advol should not be negative',
        '634' => 'Vehicle type should not be ODC when transmode is other than road',
        '635' => 'You cannot update part B, as the current transporter is already entered Part B details of the eway bill',
        '636' => 'You are not assigned to update part B',
        '637' => 'You cannot extend ewaybill, as the current transporter is already entered Part B details of the ewaybill',
        '638' => 'Transport mode is mandatory as Vehicle Number/Transport Document Number is given',
        '640' => 'Total Invoice value is mandatory',
        '641' => 'For outward CKD/SKD/Lots supply type, Bill To state should be as Other Country, since the Bill To GSTIN given is of SEZ unit',
        '642' => 'For inward CKD/SKD/Lots supply type, Bill From state should be as Other Country, since the Bill From GSTIN given is of SEZ unit',
        '643' => 'For regular transaction, Bill from state code and Dispatch from state code should be same',
        '644' => 'For regular transaction, Bill to state code and Ship to state code should be same',
        '645' => 'You cannot do Multi Vehicle movement, as current transporter already entered part B',
        '646' => 'You are not assigned to do multi vehicle movement',
        '647' => 'Could not insert RFID data, please contact to helpdesk',
        '648' => 'Multi Vehicle movement is already Initiated, hence generation of consolidated eway bill is not allowed',
        '649' => 'You cannot generate consolidated eway bill, as the current transporter is already entered Part B details of the eway bill',
        '650' => 'You are not assigned to generate consolidated ewaybill',
        '651' => 'For Category PartA or PartB ewbDt is mandatory',
        '652' => 'For Category EWB03 procDt is mandatory',
        '653' => 'The Ewaybill is cancelled',
        '654' => 'This GSTIN has generated a common Enrolment Number. Hence you are not allowed to generate Eway bill',
        '655' => 'This GSTIN has generated a common Enrolment Number. Hence you cannot mention it as a transporter',
        '656' => 'This Eway Bill does not belong to your state',
        '657' => 'Eway Bill Category wise details will be available after 4 days only',
        '658' => 'You are blocked for accessing this API as the allowed number of requests has been exceeded',
        '659' => 'Remarks is mandatory',
        '670' => 'Invalid Month Parameter',
        '671' => 'Invalid Year Parameter',
        '672' => 'User Id is mandatory',
        '673' => 'Error in getting officer dashboard',
        '675' => 'Error in getting EWB03 details by acknowledgement date range',
        '678' => 'Invalid Uniq No',
        '679' => 'Invalid EWB03 Ack No',
        '680' => 'Invalid Close Reason',
        '681' => 'Error in Closing EWB Verification Data',
        '682' => 'No Record available to Close',
        '683' => 'Error in fetching WatchList Data',
        '700' => 'You are not assigned to extend e-waybill',
        '711' => 'Invalid value for isInTransit field',
        '712' => 'Transit Type is not required as the goods are not in movement',
        '713' => 'Transit Address is not required as the goods are not in movement',
        '714' => 'Document type - Tax Invoice is not allowed for composite tax payer',
        '715' => 'The Consignor GSTIN is blocked from e-waybill generation as Return is not filed for past 2 months',
        '716' => 'The Consignee GSTIN is blocked from e-waybill generation as Return is not filed for past 2 months',
        '717' => 'The Transporter GSTIN is blocked from e-waybill generation as Return is not filed for past 2 months',
        '718' => 'The User GSTIN is blocked from Transporter Updation as Return is not filed for past 2 months',
        '719' => 'The Transporter GSTIN is blocked from Transporter Updation as Return is not filed for past 2 months',
        '800' => 'Redis server is not working, try after some time',
        '801' => 'Transporter id is not required for ewaybill for gold',
        '802' => 'Transporter name is not required for ewaybill for gold',
        '803' => 'TransDocNo is not required for ewaybill for gold',
        '804' => 'TransDocDate is not required for ewaybill for gold',
        '805' => 'Vehicle No is not required for ewaybill for gold',
        '806' => 'Vehicle Type is not required for ewaybill for gold',
        '807' => 'Transmode is mandatory for ewaybill for gold',
        '808' => 'Inter-State ewaybill is not allowed for gold',
        '809' => 'Other items are not allowed with eway bill for gold',
        '810' => 'Transport cannot be updated for EwayBill For Gold',
        '811' => 'Vehicle cannot be updated for EwayBill For Gold',
        '812' => 'ConsolidatedEWB cannot be generated for EwayBill For Gold',
        '813' => 'Transporter id is not required for ewaybill for gold',
        '814' => 'Transporter name is not required for ewaybill for gold',
        '815' => 'TransDocNo is not required for ewaybill for gold',
        '816' => 'TransDocDate is not required for ewaybill for gold',
        '817' => 'Vehicle No is not required for ewaybill for gold',
        '818' => 'Validity period lapsed. Cannot generate consolidated Eway Bill',
        '819' => 'Ewaybill cannot be generated for the document date which is prior to 01/07/2017',
        '820' => 'You cannot generate e-Waybill with document date earlier than 180 days',
        '821' => 'e-Waybill cannot be extended as the allowed limit is 360 days',
        '822' => 'Both supplier and recipient cannot be URP',
    ];

    protected function setEWBCredential()
    {
        $this->ewb_ip_address    = "49.248.155.99";
        $this->ewb_client_id     = "EWBS9b6a21f2-c644-48aa-99c9-0233e73de7ae";
        $this->ewb_client_secret = "EWBS2d477cc9-a452-4044-9a45-5cfd93e5f88b";
        $this->ewb_gstin         = "29AAGCB1286Q000";
        $this->ewb_email         = "ajinkya.bhalerao@globalinfocloud.com";
        $this->ewb_username      = "BVMGSP";
        $this->ewb_password      = "Wbooks@0142";
        $this->ewb_accept        = "application/json";
    }

    protected function getEWBErrorMessage($errorCode)
    {
        $code = (string) $errorCode;
        return isset($this->ewb_error_codes[$code])
            ? '[' . $code . '] ' . $this->ewb_error_codes[$code]
            : '[' . $code . '] Unknown EWB error code.';
    }

    protected function authenticateEWBPortel()
    {
        $params = [
            'url'     => "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/authenticate"
                       . "?email="    . urlencode($this->ewb_email)
                       . "&username=" . urlencode($this->ewb_username)
                       . "&password=" . urlencode($this->ewb_password),
            'request' => 'GET',
            'id'      => null,
            'payload' => '',
            'header'  => [
                'ip_address'    => $this->ewb_ip_address,
                'client_id'     => $this->ewb_client_id,
                'client_secret' => $this->ewb_client_secret,
                'gstin'         => $this->ewb_gstin,
                'Accept'        => $this->ewb_accept,
                'authtoken'     => '',
            ],
        ];

        return $this->executeCURL($params);
    }

    protected function GenerateEWayBill(array $payload, int $id, string $authToken = '')
    {
        // Fix float precision — prevents 300.6700000001... in JSON
        array_walk_recursive($payload, function (&$val) {
            if (is_float($val)) {
                $val = (float) number_format($val, 2, '.', '');
            }
        });

        $params = [
            'url'     => "https://apisandbox.whitebooks.in/ewaybillapi/v1.03/ewayapi/genewaybill"
                       . "?email=" . urlencode($this->ewb_email),
            'request' => 'POST',
            'id'      => $id,
            'payload' => json_encode($payload, JSON_PRESERVE_ZERO_FRACTION),
            'header'  => [
                'ip_address'    => $this->ewb_ip_address,
                'client_id'     => $this->ewb_client_id,
                'client_secret' => $this->ewb_client_secret,
                'gstin'         => $this->ewb_gstin,
                'Accept'        => $this->ewb_accept,
                'authtoken'     => $authToken,
            ],
        ];

        return $this->executeCURL($params);
    }

    protected function executeCURL($params = [])
    {
        $isPost = strtoupper($params['request']) === 'POST';

        $httpHeaders = [
            'ip_address: '    . $params['header']['ip_address'],
            'client_id: '     . $params['header']['client_id'],
            'client_secret: ' . $params['header']['client_secret'],
            'gstin: '         . $params['header']['gstin'],
            'Accept: '        . ($params['header']['Accept'] ?? 'application/json'),
        ];

        if ($isPost && !empty($params['payload'])) {
            $httpHeaders[] = 'Content-Type: application/json';
        }

        // authtoken must always be present — even empty string
        $httpHeaders[] = 'authtoken: ' . ($params['header']['authtoken'] ?? '');

        $curlOptions = [
            CURLOPT_URL            => $params['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $params['request'],
            CURLOPT_HTTPHEADER     => $httpHeaders,
        ];

        if ($isPost && !empty($params['payload'])) {
            $curlOptions[CURLOPT_POSTFIELDS] = $params['payload'];
        }

        $curl = curl_init();
        curl_setopt_array($curl, $curlOptions);

        $response  = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // 1. Network error
        if ($curlError) {
            return ['success' => false, 'message' => 'Network error: ' . $curlError];
        }

        // 2. Empty response
        if (empty($response)) {
            return ['success' => false, 'message' => 'Empty response from EWB server. HTTP ' . $httpCode];
        }

        // 3. Non-JSON response
        $decoded = json_decode($response, false);
        if (json_last_error() !== JSON_ERROR_NONE || $decoded === null) {
            return ['success' => false, 'message' => 'Non-JSON response. HTTP ' . $httpCode, 'raw' => $response];
        }

        // 4. Missing status_cd
        if (!isset($decoded->status_cd)) {
            return ['success' => false, 'message' => 'Unexpected response (no status_cd).', 'raw' => $response];
        }

        // 5. EWB API error
        if ((int) $decoded->status_cd === 0) {
            $errorCode = 'UNKNOWN';
            $infoHint  = '';

            // Error code is nested inside a JSON string in error.message
            if (!empty($decoded->error->message)) {
                $innerMsg = json_decode($decoded->error->message, true);
                if (json_last_error() === JSON_ERROR_NONE && !empty($innerMsg['errorCodes'])) {
                    $codesStr  = rtrim(trim((string) $innerMsg['errorCodes']), ',');
                    $errorCode = trim(explode(',', $codesStr)[0]);
                }
            }

            // Fallback locations
            if ($errorCode === 'UNKNOWN') {
                $fallback = $decoded->error->errorCodes
                    ?? $decoded->error->errorCode
                    ?? $decoded->errorCodes
                    ?? $decoded->errorCode
                    ?? 'UNKNOWN';
                $errorCode = rtrim(trim(is_array($fallback) ? ($fallback[0] ?? 'UNKNOWN') : (string) $fallback), ',');
            }

            if (!empty($decoded->error->info)) {
                $infoHint = base64_decode($decoded->error->info);
            }

            return [
                'success'    => false,
                'error_code' => $errorCode,
                'message'    => $this->getEWBErrorMessage($errorCode),
                'info'       => $infoHint,
            ];
        }

        // 6. Success
        $apiResponse = json_decode($response, true);

		// ewayBillNo is inside api_response.data
		$billData    = $apiResponse['data'] ?? $apiResponse;
		$ewayBillNo  = $billData['ewayBillNo']  ?? null;
		$ewayBillDate = $billData['ewayBillDate'] ?? null;
		$validUpto   = $billData['validUpto']   ?? null;

		// Save to DB using TransferID (numeric PK)
		if (!empty($ewayBillNo) && !empty($params['id'])) {

			$ewayExpDate = null;
			if (!empty($validUpto)) {
				$parsedDate = DateTime::createFromFormat('d/m/Y h:i:s A', $validUpto);
				$ewayExpDate = $parsedDate ? $parsedDate->format('Y-m-d H:i:s') : null;
			}

			$this->StockTransfer_model->updateData(
				'StockTransferMaster',
				[
					'isEwayBill'   => 'Y',
					'EWayBillNo'   => $ewayBillNo,
					'EWayBillDate' => date('Y-m-d H:i:s'),
					'EwayBillExpDate' => $ewayExpDate,
				],
				['id' => $params['id']]
			);
		}

		// Clean response — only what the UI needs
		return [
			'success'      => true,
			'status_cd'    => $apiResponse['status_cd']   ?? '1',
			'status_desc'  => $apiResponse['status_desc'] ?? '',
			'ewayBillNo'   => $ewayBillNo,
			'ewayBillDate' => $ewayBillDate,
			'validUpto'    => $validUpto,
			'alert'        => $billData['alert'] ?? '',
		];
	}
}