<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grn extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Quotation_model');
        $this->load->model('Grn_model');
        $this->load->model('purchase_model');
    }

    public function index()
    {
        $this->load->model('Quotation_model');
        // $data['vendor_list']      = $this->Quotation_model->getVendorDropdown();
        $data['purchaselocation'] = $this->purchase_model->get_purchase_location();
        $data['transporters']     = $this->Grn_model->get_transporter_name();
        $data['vehicle_owner']    = $this->Grn_model->get_vehicle_owner();

        $this->load->view('admin/GRN/grn_form', $data);
    }

    // ========================
    // AUTO GENERATE GRN NO
    // ========================
    public function GetNextGRNNo()
    {
        $grn_no = $this->Grn_model->getNextGRNNo();
        echo json_encode(['grn_no' => $grn_no]);
    }

    // ========================
    // SAVE GRN
    // ========================
    public function SaveGRN()
    {
        $fy     = $this->session->userdata('finacial_year');
        $userId = $this->session->userdata('staff_user_id') ?? 1;
        $plant  = $this->session->userdata('root_company');

        $grn_no = $this->input->post('grn_no');

        $masterData = [
            'PlantID'          => $plant,
            'FY'               => $fy,
            'GRNNo'            => $grn_no,
            'GRNDate'          => $this->_toDbDate($this->input->post('grn_date')),
            'PlantLocation'    => $this->input->post('location')           ?: null,
            'PostingDate'      => $this->_toDbDate($this->input->post('posting_date')),
            'ArrivalDate'      => $this->_toDbDate($this->input->post('arrival_date')),
            'GRNType'          => $this->input->post('grn_category')       ?: '',
            'OrderNo'          => $this->input->post('po_no'),
            'PaymentTerms'     => $this->input->post('payment_terms')      ?: '',
            'AccountID'        => $this->input->post('vendor_id'),
            'BrokerID'         => $this->input->post('broker_id')          ?: '',
            'GSTIN'            => $this->input->post('vendor_gst_no')      ?: '',
            'state'            => $this->input->post('vendor_state')       ?: '',
            'VendorLocation'   => $this->input->post('vendor_location')    ?: null,
            'VendorDocNo'      => $this->input->post('vendor_doc_no')      ?: '',
            'VendorDocDate'    => $this->_toDbDate($this->input->post('vendor_doc_date')),
            'VendorDocAmt'     => $this->input->post('vendor_doc_amount')      ?: 0,
            'VendorDispatchWt' => $this->input->post('vendor_dispatch_weight') ?: 0,
            'VehicleNo'        => strtoupper($this->input->post('vehicle_no')  ?: ''),
            'Status'           => $this->input->post('grn_status')         ?: 'Open',
            'BusinessUnit'     => $this->input->post('business_unit')      ?: null,
            'FreightTerms'     => $this->input->post('freight_terms')      ?: '',
            'Currency'         => $this->input->post('currency')           ?: '',
            'GateINID'         => $this->input->post('gate_entry_no')      ?: '',
            'GateINDate'       => $this->_toDbDate($this->input->post('gate_entry_date')),
            'FreightPayer'     => $this->input->post('freight_payable_to') ?: '',
            'TransporterID'    => $this->input->post('transporter_id')     ?: null,
            'VehicleOwn'       => $this->input->post('vehicle_owner')      ?: '',
            'TDSFreight'       => $this->input->post('tds_freight')        ?: 'No',
            // 'TDSCode'          => $this->input->post('tds_code')           ?: '',
            // 'TDSPercent'       => $this->input->post('tds_percent')        ?: 0,
            'TotalFreight'     => $this->input->post('total_freight')      ?: 0,
            'FreightInCash'    => $this->input->post('freight_in_cash')    ?: 0,
            'FreightPayable'   => $this->input->post('freight_payable')    ?: 0,
            'FreightTDSAmt'    => $this->input->post('freight_tds_amount') ?: 0,
            'InternalRemart'   => $this->input->post('internal_remarks')   ?: '',
            'DocumentRemark'   => $this->input->post('document_remark')    ?: '',
            'FinalAmt'         => $this->input->post('total_amount')       ?: 0,
            'TransDate'        => date('Y-m-d'),
            'UserID'           => $userId,
            'Lupdate'          => date('Y-m-d H:i:s'),
        ];

        // Attachment upload
        $masterData['Attachment'] = $this->_uploadAttachment();

        // Insert Master
        $grn_id = $this->Grn_model->saveGRNMaster($masterData);

        if ($grn_id === false) {
            $dbError = $this->db->error();
            echo json_encode([
                'success'    => false,
                'message'    => 'Failed to save GRN master.',
                'db_error'   => $dbError,
                'last_query' => $this->db->last_query()
            ]);
            return;
        }

        // Insert Items into tblhistory
        $itemDataRaw = $this->input->post('ItemData');
        $items       = json_decode($itemDataRaw, true);

        if (!empty($items)) {
            $po_no     = $this->input->post('po_no');
            $transDate = date('Y-m-d');

            foreach ($items as $item) {
                $itemData = [
                    'PlantID'     => $plant,
                    'FY'          => $fy,
                    'OrderID'     => $po_no,
                    'BillID'      => '',
                    'TransID'     => $grn_id,
                    'TransDate'   => $transDate,
                    'TransDate2'  => $transDate,
                    'TType'       => 'G',
                    'TType2'      => 'Purchase',
                    'AccountID'   => $grn_no,   // GRNNo stored here for retrieval
                    'ItemID'      => $item['item_id']      ?? '',
                    'GodownID'    => $this->input->post('location'),
                    'BasicRate'   => $item['unit_price']   ?? 0,
                    'SaleRate'    => $item['item_rate']    ?? 0,
                    'SuppliedIn'  => $item['item_uom']     ?? '',
                    'WeightUnit'  => $item['receipt_uom']  ?? '',
                    'CaseQty'     => $item['total_bag']    ?? 0,
                    'OrderQty'    => $item['po_orig_qty']  ?? 0,
                    'eOrderQty'   => $item['po_bal_qty']   ?? 0,
                    'BilledQty'   => $item['receipt_qty']  ?? 0,
                    'cgst'        => $item['gst_percent']  ?? 0,
                    'sgst'        => $item['gst_percent']  ?? 0,
                    'igst'        => 0,
                    'OrderAmt'    => $item['item_amount']  ?? 0,
                    'ChallanAmt'  => $item['item_amount']  ?? 0,
                    'NetOrderAmt' => $item['item_amount']  ?? 0,
                    'UserID'      => $userId,
                    'Lupdate'     => date('Y-m-d H:i:s'),
                ];

                $this->Grn_model->saveGRNItem($itemData);
            }
        }

        echo json_encode(['success' => true, 'grn_id' => $grn_id, 'message' => 'GRN saved successfully.']);
    }

    // ========================
    // ✅ UPDATE GRN - FIXED
    // ========================
    public function UpdateGRN($grn_id = '')
    {
        if (empty($grn_id)) {
            echo json_encode(['success' => false, 'message' => 'GRN ID missing.']);
            return;
        }

        $fy     = $this->session->userdata('finacial_year');
        $userId = $this->session->userdata('staff_user_id') ?? 1;
        $plant  = $this->session->userdata('root_company');

        // grn_no = the GRN number string (e.g. GRN25100001)
        $grn_no = $this->input->post('grn_no');

        $masterData = [
            'PlantID'          => $plant,
            'FY'               => $fy,
            'GRNNo'            => $grn_no,
            'GRNDate'          => $this->_toDbDate($this->input->post('grn_date')),
            'PlantLocation'    => $this->input->post('location'),
            'PostingDate'      => $this->_toDbDate($this->input->post('posting_date')),
            'ArrivalDate'      => $this->_toDbDate($this->input->post('arrival_date')),
            'GRNType'          => $this->input->post('grn_category'),
            'OrderNo'          => $this->input->post('po_no'),
            'PaymentTerms'     => $this->input->post('payment_terms'),
            'AccountID'        => $this->input->post('vendor_id'),
            'BrokerID'         => $this->input->post('broker_id'),
            'GSTIN'            => $this->input->post('vendor_gst_no'),
            'state'            => $this->input->post('vendor_state'),
            'VendorLocation'   => $this->input->post('vendor_location'),
            'VendorDocNo'      => $this->input->post('vendor_doc_no'),
            'VendorDocDate'    => $this->_toDbDate($this->input->post('vendor_doc_date')),
            'VendorDocAmt'     => $this->input->post('vendor_doc_amount')      ?: 0,
            'VendorDispatchWt' => $this->input->post('vendor_dispatch_weight') ?: 0,
            'VehicleNo'        => strtoupper($this->input->post('vehicle_no')),
            'Status'           => $this->input->post('grn_status'),
            'BusinessUnit'     => $this->input->post('business_unit'),
            'FreightTerms'     => $this->input->post('freight_terms'),
            'Currency'         => $this->input->post('currency'),
            'GateINID'         => $this->input->post('gate_entry_no'),
            'GateINDate'       => $this->_toDbDate($this->input->post('gate_entry_date')),
            'FreightPayer'     => $this->input->post('freight_payable_to'),
            'TransporterID'    => $this->input->post('transporter_id'),
            'VehicleOwn'       => $this->input->post('vehicle_owner'),
            'TDSFreight'       => $this->input->post('tds_freight'),
            // 'TDSCode'          => $this->input->post('tds_code')           ?: '',
            // 'TDSPercent'       => $this->input->post('tds_percent')        ?: 0,
            'TotalFreight'     => $this->input->post('total_freight')      ?: 0,
            'FreightInCash'    => $this->input->post('freight_in_cash')    ?: 0,
            'FreightPayable'   => $this->input->post('freight_payable')    ?: 0,
            'FreightTDSAmt'    => $this->input->post('freight_tds_amount') ?: 0,
            'InternalRemart'   => $this->input->post('internal_remarks'),
            'DocumentRemark'   => $this->input->post('document_remark'),
            'FinalAmt'         => $this->input->post('total_amount')       ?: 0,
            'Lupdate'          => date('Y-m-d H:i:s'),
            'UserID2'          => $userId,
        ];

        // Attachment - only update if new file uploaded
        $newAttachment = $this->_uploadAttachment();
        if ($newAttachment) {
            $masterData['Attachment'] = $newAttachment;
        }

        // ✅ Update master record (where GRNNo = $grn_id)
        $updated = $this->Grn_model->updateGRNMaster($grn_id, $masterData);

        if ($updated === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to update GRN master.']);
            return;
        }

        // ✅ FIXED: Delete old items using GRNNo (AccountID = grn_no, TType = 'G')
        $this->Grn_model->deleteGRNItems($grn_no);

        // ✅ Re-insert updated items
        $itemDataRaw = $this->input->post('ItemData');
        $items       = json_decode($itemDataRaw, true);
        $po_no       = $this->input->post('po_no');
        $transDate   = date('Y-m-d');

        if (!empty($items)) {
            foreach ($items as $item) {
                $itemData = [
                    'PlantID'     => $plant,
                    'FY'          => $fy,
                    'OrderID'     => $po_no,
                    'BillID'      => '',
                    'TransID'     => $grn_id,
                    'TransDate'   => $transDate,
                    'TransDate2'  => $transDate,
                    'TType'       => 'G',
                    'TType2'      => 'Purchase',
                    'AccountID'   => $grn_no,   // GRNNo stored here for retrieval
                    'ItemID'      => $item['item_id']      ?? '',
                    'GodownID'    => $this->input->post('location'),
                    'BasicRate'   => $item['unit_price']   ?? 0,
                    'SaleRate'    => $item['item_rate']    ?? 0,
                    'SuppliedIn'  => $item['item_uom']     ?? '',
                    'WeightUnit'  => $item['receipt_uom']  ?? '',
                    'CaseQty'     => $item['total_bag']    ?? 0,
                    'OrderQty'    => $item['po_orig_qty']  ?? 0,
                    'eOrderQty'   => $item['po_bal_qty']   ?? 0,
                    'BilledQty'   => $item['receipt_qty']  ?? 0,
                    'cgst'        => $item['gst_percent']  ?? 0,
                    'sgst'        => $item['gst_percent']  ?? 0,
                    'igst'        => 0,
                    'OrderAmt'    => $item['item_amount']  ?? 0,
                    'ChallanAmt'  => $item['item_amount']  ?? 0,
                    'NetOrderAmt' => $item['item_amount']  ?? 0,
                    'UserID'      => $userId,
                    'Lupdate'     => date('Y-m-d H:i:s'),
                ];

                $this->Grn_model->saveGRNItem($itemData);
            }
        }

        echo json_encode(['success' => true, 'message' => 'GRN updated successfully.']);
    }

    // ========================
    // GET ALL GRN LIST (Modal)
    // ========================
    public function GetAllGRNList()
    {
        $list = $this->Grn_model->getAllGRNList();

        $html = '';
        if (!empty($list)) {
            foreach ($list as $row) {
                $html .= '<tr class="get_GRN_ID" data-id="' . $row['id'] . '" style="cursor:pointer;">';
                $html .= '<td>' . htmlspecialchars($row['GRNNo'])     . '</td>';
                $html .= '<td>' . htmlspecialchars($row['GRNDate'])   . '</td>';
                $html .= '<td>' . htmlspecialchars($row['VendorName'] ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($row['OrderNo'])   . '</td>';
                $html .= '<td>' . htmlspecialchars($row['PlantLocation'] ?? '') . '</td>';
                $html .= '<td>' . htmlspecialchars($row['Status'])    . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html = '<tr><td colspan="6" class="text-center">No GRN records found.</td></tr>';
        }

        echo json_encode($html);
    }

    // ========================
    // GET GRN DETAIL BY ID
    // ========================
    public function GetGRNDetailByID()
    {
        $grn_id = $this->input->post('grn_id');

        if (empty($grn_id)) {
            echo json_encode(['success' => false, 'message' => 'GRN ID missing.']);
            return;
        }

        $grn = $this->Grn_model->getGRNById($grn_id);

        if (empty($grn)) {
            echo json_encode(['success' => false, 'message' => 'GRN not found.']);
            return;
        }

        // ✅ Get items by GRNNo (AccountID = GRNNo, TType = 'G')
        $items = $this->Grn_model->getGRNItemsByBillID($grn['GRNNo']);

        // Map tblhistory columns → view field names
        $mappedItems = [];
        foreach ($items as $item) {
            $mappedItems[] = [
                'item_id'           => $item['ItemID']           ?? '',
                'item_name'         => $item['ItemName']         ?? '',
                'po_orig_qty'       => $item['OrderQty']         ?? '',
                'po_bal_qty'        => $item['eOrderQty']        ?? '',
                'item_uom'          => $item['SuppliedIn']       ?? '',
                'total_bag'         => $item['CaseQty']          ?? '',
                'receipt_qty'       => $item['BilledQty']        ?? '',
                'unit_price'        => $item['BasicRate']        ?? '',
                'receipt_uom'       => $item['WeightUnit']       ?? '',
                'rebate_settlement' => $item['rebate_settlement'] ?? 'No',
                'item_rate'         => $item['SaleRate']         ?? '',
                'rate_uom'          => $item['WeightUnit']       ?? '',
                'calc_rate'         => $item['calc_rate']        ?? '',
                'gst_percent'       => $item['cgst']             ?? '',
                'item_amount'       => $item['OrderAmt']         ?? '',
            ];
        }

        // Map tblGRNMaster columns → view field names
        $grnData = [
            'grn_no'                 => $grn['GRNNo'],
            'grn_date'               => $grn['GRNDate'],
            'location'               => $grn['PlantLocation'],
            'posting_date'           => $grn['PostingDate'],
            'arrival_date'           => $grn['ArrivalDate'],
            'vendor_id'              => $grn['AccountID'],
            'vendor_location'        => $grn['VendorLocation'],
            'grn_category'           => $grn['GRNType'],
            'po_no'                  => $grn['OrderNo'],
            'vendor_doc_date'        => $grn['VendorDocDate'],
            'vendor_doc_amount'      => $grn['VendorDocAmt'],
            'vendor_doc_no'          => $grn['VendorDocNo'],
            'broker_id'              => $grn['BrokerID'],
            'payment_terms'          => $grn['PaymentTerms'],
            'vehicle_no'             => $grn['VehicleNo'],
            'vendor_dispatch_weight' => $grn['VendorDispatchWt'],
            'grn_status'             => $grn['Status'],
            'business_unit'          => $grn['BusinessUnit'],
            'freight_terms'          => $grn['FreightTerms'],
            'currency'               => $grn['Currency'],
            'gate_entry_no'          => $grn['GateINID'],
            'gate_entry_date'        => $grn['GateINDate'],
            'freight_payable_to'     => $grn['FreightPayer'],
            'transporter_id'         => $grn['TransporterID'],
            'vehicle_owner'          => $grn['VehicleOwn'],
            'tds_freight'            => $grn['TDSFreight'],
            'tds_code'               => $grn['TDSCode']          ?? '',
            'tds_percent'            => $grn['TDSPercent']       ?? '',
            'total_freight'          => $grn['TotalFreight'],
            'freight_in_cash'        => $grn['FreightInCash'],
            'freight_payable'        => $grn['FreightPayable'],
            'freight_tds_amount'     => $grn['FreightTDSAmt'],
            'internal_remarks'       => $grn['InternalRemart'],
            'document_remark'        => $grn['DocumentRemark'],
            'total_amount'           => $grn['FinalAmt'],
            'vendor_gst_no'          => $grn['GSTIN'],
            'vendor_state'           => $grn['state'],
            'attachment'             => $grn['Attachment'],
        ];

        echo json_encode([
            'success' => true,
            'data'    => [
                'grnDetails' => $grnData,
                'itemData'   => $mappedItems,
            ]
        ]);
    }

    // ========================
    // PURCHASE ORDERS BY VENDOR
    // ========================
    public function getpurchaseorder()
    {
        $AccountID        = $this->input->post('AccountID');
        $purchaselocation = $this->input->post('purchaselocation');

        $shippingData = $this->Grn_model->getpurchaseorder($AccountID, $purchaselocation);

        $locations = [];
        if (!empty($shippingData)) {
            foreach ($shippingData as $location) {
                $locations[] = ['po_no' => $location['PurchID'] ?? ''];
            }
        }

        echo json_encode(['status' => 'success', 'locations' => $locations]);
    }

    // ========================
    // PO DETAILS BY ID
    // ========================
    public function GetPODetailsByID()
    {
        $po_id  = $this->input->post('po_id');
        $header = $this->Grn_model->getpurchaseorderheader($po_id);
        $items  = $this->Grn_model->getpurchaseorderitems($po_id);

        echo json_encode([
            'status' => 'success',
            'data'   => ['header' => $header, 'items' => $items]
        ]);
    }

    // ========================
    // TRANSPORTER / VEHICLE OWNER PAN
    // ========================
    public function transport($transporter_id = '')
    {
        if (empty($transporter_id)) {
            echo json_encode(['pan' => '']);
            return;
        }

        $this->db->select('PAN');
        $this->db->from('tblclients');
        $this->db->where('AccountID', $transporter_id);
        $result = $this->db->get()->row_array();

        echo json_encode(['pan' => $result ? $result['PAN'] : '']);
    }

    // ========================
    // PRIVATE HELPERS
    // ========================

    /**
     * Convert DD-MM-YYYY or DD/MM/YYYY → YYYY-MM-DD for DB
     */
    private function _toDbDate($date)
    {
        if (empty($date)) return null;

        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $date, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1];
        }
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1];
        }
        return $date;
    }

    /**
     * Handle attachment file upload
     */
    private function _uploadAttachment()
    {
        if (empty($_FILES['attachment']['name'])) return '';

        $uploadPath = './uploads/grn/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

        $config = [
            'upload_path'   => $uploadPath,
            'allowed_types' => 'pdf|jpg|jpeg|png|doc|docx|xlsx|xls',
            'max_size'      => 5120,
            'encrypt_name'  => true,
        ];

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('attachment')) {
            $uploadData = $this->upload->data();
            return $uploadData['file_name'];
        }

        return '';
    }
    
    
    /**
 * GET VENDOR LIST BY PURCHASE LOCATION
 * URL: purchase/Grn/GetVendorByLocation
 */
public function GetVendorByLocation()
{
    // Only POST allow
    if ($this->input->server('REQUEST_METHOD') !== 'POST') {
        show_404();
        return;
    }

    $location_id = $this->input->post('location_id', TRUE);

    if (empty($location_id)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Location ID is required',
            'data'    => []
        ]);
        return;
    }

    // Model call
    $data = $this->Grn_model->getVendorsByLocation($location_id);

    if (!empty($data)) {
        echo json_encode([
            'status' => 'success',
            'data'   => $data
        ]);
    } else {
        echo json_encode([
            'status'  => 'success',
            'message' => 'No vendors found for this location',
            'data'    => []
        ]);
    }
}
}