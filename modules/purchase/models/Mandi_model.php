<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mandi_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all purchase order numbers
     * @return array
     */
    public function get_po_no()
    {
        $PlantID = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');

        $this->db->select('OrderID');
        $this->db->order_by('Id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('tblMandiPurchaseMaster');

        if ($query->num_rows() > 0) {

            $row = $query->row();
            $last_po = $row->OrderID; // MPO25100001

            // last 5 digit number
            $number = (int) substr($last_po, -5);

            $number = $number + 1;

            $new_po = 'MPO' . $FY . $PlantID . str_pad($number, 5, '0', STR_PAD_LEFT);

            return $new_po;

        } else {

            return 'MPO' . $FY . $PlantID . str_pad(1, 5, '0', STR_PAD_LEFT);

        }
    }

    /**
     * Get all active items list
     * @return array
     */
    public function get_Items_list()
    {
        $this->db->select(db_prefix() . 'items.*');
        $this->db->order_by(db_prefix() . 'items.Id', 'ASC');
        $this->db->where(db_prefix() . 'items.IsActive', 'Y');
        return $this->db->get('tblitems')->result_array();
    }
     public function get_COMPANY_list()
    {

        $PlantID = $this->session->userdata('root_company');
        $this->db->select(db_prefix() . 'rootcompany.*');
        $this->db->order_by(db_prefix() . 'rootcompany.Id', 'ASC');
        $this->db->where(db_prefix() . 'rootcompany.status', '1');
        $this->db->where(db_prefix() . 'rootcompany.id', $PlantID);
        return $this->db->get('tblrootcompany')->result_array();
    }

    public function get_godown_list()
    {
        $this->db->select(db_prefix() . 'godownmaster.*');
        $this->db->order_by(db_prefix() . 'godownmaster.Id', 'ASC');
        $this->db->where(db_prefix() . 'godownmaster.IsActive', 'Y');
        return $this->db->get('tblgodownmaster')->result_array();
    }

    /**
     * Get TDS code list (only blocked ones)
     * @return array
     */
        public function get_tds_list()
        {
            $this->db->select('m.*, d.rate');
            $this->db->from('tblTDSMaster as m');
            $this->db->join('tblTDSDetails as d', 'm.TDSCode = d.TDSCode', 'left');
            $this->db->where('m.Blocked', 'N');
            $this->db->order_by('m.Id', 'ASC');

            return $this->db->get()->result_array();
        }

    /**
     * Get godowns by location ID
     * @param int $location_id
     * @return array
     */
    public function get_godown_by_location($location_id)
    {
        $this->db->select('GodownName, id');
        $this->db->where('LocationID', $location_id);
        $this->db->order_by('GodownName', 'ASC');
        return $this->db->get('tblgodownmaster')->result_array();
    }

    /**
     * Get vendor payment terms and TDS percentage
     * @param string $vendor_id
     * @return array
     */
    public function get_vendor_terms($vendor_id)
    {
        $query = $this->db->query(
            "SELECT PaymentTerms, TDSPer FROM tblclients WHERE AccountID = ?",
            [$vendor_id]
        );

        if ($query->num_rows() > 0) {
            return [
                'success' => true,
                'data' => $query->row_array()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Vendor not found',
                'data' => ['PaymentTerms' => '', 'TDSPer' => 0]
            ];
        }
    }

    /**
     * Save Mandi Purchase (Add/Update)
     * @param array $purchase_data
     * @return array
     */
    public function save_mandi_purchase($purchase_data)
    {
        try {
            // Validate required fields
            $validation_result = $this->_validate_purchase_data($purchase_data);
            if (!$validation_result['success']) {
                return $validation_result;
            }

            // Parse date format
            $inwards_date = $this->_format_date($purchase_data['inwards_date']);

            // Decode JSON items
            $items_data = json_decode($purchase_data['form_json'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'message' => 'Invalid JSON format'];
            }

            if (empty($items_data['items'])) {
                return ['success' => false, 'message' => 'No line items found'];
            }

            // Start transaction
            $this->db->trans_start();

            if ($purchase_data['form_mode'] == 'add') {
                $result = $this->_insert_mandi_purchase($purchase_data, $inwards_date, $items_data['items']);
            } else if ($purchase_data['form_mode'] == 'edit') {
                $result = $this->_update_mandi_purchase($purchase_data, $inwards_date, $items_data['items']);
            } else {
                return ['success' => false, 'message' => 'Invalid form mode'];
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return ['success' => false, 'message' => 'Transaction failed'];
            }

            return $result;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate purchase data
     * @param array $data
     * @return array
     */
    private function _validate_purchase_data($data)
    {
        $required_fields = ['purchase_order', 'inwards_date', 'location_id', 'godown_id', 'item_id_header', 'tds_code', 'form_json'];

        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'];
            }
        }

        return ['success' => true];
    }

    /**
     * Format date from DD/MM/YYYY to YYYY-MM-DD
     * @param string $date
     * @return string
     */
    private function _format_date($date)
    {
        $date_parts = explode('/', $date);
        if (count($date_parts) == 3) {
            return $date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0];
        }
        return $date;
    }

    /**
     * Insert new Mandi Purchase record
     * @param array $purchase_data
     * @param string $inwards_date
     * @param array $items
     * @return array
     */
    private function _insert_mandi_purchase($purchase_data, $inwards_date, $items)
    {
        $insert_data = [
            'PlantID' => $purchase_data['selected_company'],
            'FY' => $purchase_data['FY'],
            'OrderID' => $purchase_data['purchase_order'],
            'OrderDate' => $inwards_date,
            'CenterLocation' => $purchase_data['location_id'],
            'WarehouseID' => $purchase_data['godown_id'],
            'ItemType' => $purchase_data['item_id_header'],
            'ItemID' => $purchase_data['item_id_header'],
            'TDSCode' => $purchase_data['tds_code'],
            'VehicleNo' => $purchase_data['vehicle_no'] ?? null,
            'LeaderGroupID' => null,
            'LeaderID' => null,
            'TotalQty' => $purchase_data['total_qty_quintal'] ?? 0,
            'TotalValue' => $purchase_data['total_value'] ?? 0,
            'TotalBrokerage' => $purchase_data['total_brokerage'] ?? 0,
            'TotalMarketPrice' => $purchase_data['total_market_levy'] ?? 0,
            'TotalGrossValue' => $purchase_data['total_gross_value'] ?? 0,
            'TDSAmt' => $purchase_data['tds'] ?? 0,
            'FinalAmt' => $purchase_data['total_net_value'] ?? 0,
            'TransDate' => date('Y-m-d H:i:s'),
            'UserID' => $purchase_data['user'],
            'Lupdate' => date('Y-m-d H:i:s'),
            'UserID2' => null
        ];

        $insert_result = $this->db->insert('tblMandiPurchaseMaster', $insert_data);

        if (!$insert_result) {
            throw new Exception('Failed to insert Mandi Purchase record');
        }

        $mandi_purchase_id = $this->db->insert_id();

        // Insert line items
        $this->_insert_mandi_purchase_items($mandi_purchase_id, $items, $purchase_data['purchase_order'], $purchase_data['user'],$purchase_data['item_id_header']);

        return [
            'success' => true,
            'message' => 'Mandi Purchase record added successfully',
            'id' => $mandi_purchase_id
        ];
    }

    /**
     * Update existing Mandi Purchase record
     * @param array $purchase_data
     * @param string $inwards_date
     * @param array $items
     * @return array
     */
    private function _update_mandi_purchase($purchase_data, $inwards_date, $items)
    {
        $update_id = $purchase_data['update_id'];

        if (empty($update_id)) {
            throw new Exception('Update ID is required for update mode');
        }

        // Check if record exists
        $existing = $this->db->where('id', $update_id)->get('tblMandiPurchaseMaster')->row_array();
        if (empty($existing)) {
            throw new Exception('Mandi Purchase record not found');
        }

        $update_data = [
            'OrderID' => $purchase_data['purchase_order'],
            'OrderDate' => $inwards_date,
            'CenterLocation' => $purchase_data['location_id'],
            'WarehouseID' => $purchase_data['godown_id'],
            'ItemID' => $purchase_data['item_id_header'],
            'TDSCode' => $purchase_data['tds_code'],
            'VehicleNo' => $purchase_data['vehicle_no'] ?? null,
            'TotalQty' => $purchase_data['total_qty_quintal'] ?? 0,
            'TotalValue' => $purchase_data['total_value'] ?? 0,
            'TotalBrokerage' => $purchase_data['total_brokerage'] ?? 0,
            'TotalMarketPrice' => $purchase_data['total_market_levy'] ?? 0,
            'TotalGrossValue' => $purchase_data['total_gross_value'] ?? 0,
            'TDSAmt' => $purchase_data['tds'] ?? 0,
            'FinalAmt' => $purchase_data['total_net_value'] ?? 0,
            'Lupdate' => date('Y-m-d H:i:s'),
            'UserID2' => $purchase_data['user']
        ];

        $this->db->where('id', $update_id);
        $update_result = $this->db->update('tblMandiPurchaseMaster', $update_data);

        if (!$update_result) {
            throw new Exception('Failed to update Mandi Purchase record');
        }

        // Delete old line items
        $this->db->where('PurchID', $purchase_data['purchase_order'])->delete('tblMandiPurchaseDetails');

        // Insert updated line items
                // ItemID
        $this->_insert_mandi_purchase_items($update_id, $items, $purchase_data['purchase_order'], $purchase_data['user'],$purchase_data['item_id_header']);

        return [
            'success' => true,
            'message' => 'Mandi Purchase record updated successfully',
            'id' => $update_id
        ];
    }

    /**
     * Insert Mandi Purchase line items
     * @param int $mandi_purchase_id
     * @param array $items
     * @param string $purchase_order
     * @return void
     */
    private function _insert_mandi_purchase_items($mandi_purchase_id, $items, $purchase_order, $user_id, $item_id)
    {
        $PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
        foreach ($items as $item) {
            $item_insert = [
                'FY' => $FY,
                'PlantID' => $PlantID,
                'PurchID' => $purchase_order,
                'ItemID' => $item_id,
                'DocNo' => isset($item['doc_no']) ? $item['doc_no'] : null,
                'AccountID' => isset($item['vendor_id']) ? $item['vendor_id'] : null,
                'Payment_term' => isset($item['payment_term']) ? $item['payment_term'] : null,
                'bag' => isset($item['bag']) ? $item['bag'] : 0,
                'wt_per_bag' => isset($item['weight_per_bag']) ? $item['weight_per_bag'] : 0,
                'loose_in_kg' => isset($item['loose_kg']) ? $item['loose_kg'] : 0,
                'OrderQty' => isset($item['qty_quintal']) ? $item['qty_quintal'] : 0,
                'PurchRate' => isset($item['rate_quintal']) ? $item['rate_quintal'] : 0,
                'OrderAmt' => isset($item['value']) ? $item['value'] : 0,
                'BrokerAmt' => isset($item['brokerage']) ? $item['brokerage'] : 0,
                'MrtLevyAmt' => isset($item['market_levy']) ? $item['market_levy'] : 0,
                'Round_off' => isset($item['round_off']) ? $item['round_off'] : 0,
                'GrossAmt' => isset($item['gross']) ? $item['gross'] : 0,
                'TDSAmt' => isset($item['tds_amt']) ? $item['tds_amt'] : 0,
                'NetOrderAmt' => isset($item['net_amt']) ? $item['net_amt'] : 0,
                'LupDate' => date('Y-m-d H:i:s'),
                'TransDate'=> date('Y-m-d H:i:s'),
                'UserID' => $user_id
            ];

            if (!$this->db->insert('tblMandiPurchaseDetails', $item_insert)) {
                throw new Exception('Failed to insert line item');
            }
        }
    }

    /**
     * Get Mandi Purchase record by ID
     * @param int $id
     * @return array
     */
    public function get_mandi_purchase_by_id($id)
    {
        $this->db->select('*');
        $this->db->where('id', $id);
        return $this->db->get('tblMandiPurchaseMaster')->row_array();
    }



    /**
     * Get all Mandi Purchase records with pagination
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function get_all_mandi_purchases($limit = 10, $offset = 0)
    {
        $this->db->select('*');
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get('tblMandiPurchaseMaster')->result_array();
    }

    /**
     * Get total count of Mandi Purchase records
     * @return int
     */
    public function count_mandi_purchases()
    {
        return $this->db->count_all('tblMandiPurchaseMaster');
    }

    /**
     * Search Mandi Purchase by Order ID or Date
     * @param string $search_term
     * @return array
     */
    public function search_mandi_purchases($search_term)
    {
        $this->db->select('*');
        $this->db->where('OrderID LIKE', '%' . $search_term . '%');
        $this->db->or_where('VehicleNo LIKE', '%' . $search_term . '%');
        $this->db->order_by('id', 'DESC');
        return $this->db->get('tblMandiPurchaseMaster')->result_array();
    }

    /**
     * Delete Mandi Purchase record
     * @param int $id
     * @return bool
     */
    public function delete_mandi_purchase($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tblMandiPurchaseMaster');
    }

    /**
     * Get vendor details
     * @param string $vendor_id
     * @return array
     */
    public function get_vendor_details($vendor_id)
    {
        $query = $this->db->query(
            "SELECT * FROM tblclients WHERE AccountID = ?",
            [$vendor_id]
        );
        return $query->row_array();
    }

    /**
     * Get item details
     * @param string $item_id
     * @return array
     */
    public function get_item_details($item_id)
    {
        $this->db->where('ItemID', $item_id);
        return $this->db->get('tblitems')->row_array();
    }

    /**
     * Get TDS code details
     * @param int $tds_code
     * @return array
     */
    public function get_tds_details($tds_code)
    {
        $this->db->where('id', $tds_code);
        return $this->db->get('tblTDSMaster')->row_array();
    }

    /**
     * Get location details
     * @param int $location_id
     * @return array
     */
    public function get_location_details($location_id)
    {
        $this->db->where('id', $location_id);
        return $this->db->get('tblpurchaselocation')->row_array();
    }

    /**
     * Get godown details
     * @param int $godown_id
     * @return array
     */
    public function get_godown_details($godown_id)
    {
        $this->db->where('id', $godown_id);
        return $this->db->get('tblgodownmaster')->row_array();
    }


   public function getMandiList($from_date = null, $to_date = null,$filter_location= null, $filter_godown = null, $filter_item = null)
{
    $this->db->select('
        m.id,
        m.OrderID,
        m.OrderDate,
        m.TransDate,
        m.VehicleNo,
        m.FinalAmt,
        l.LocationName  AS CenterLocation,
        g.GodownName    AS WarehouseID,
        i.ItemName      AS ItemID
    ');
    $this->db->from('tblMandiPurchaseMaster m');
    $this->db->join('tblPlantLocationDetails l', 'l.id = m.CenterLocation', 'left');
    $this->db->join('tblgodownmaster g',         'g.id = m.WarehouseID',    'left');
    $this->db->join('tblitems i',                'i.ItemID = m.ItemID',     'left');

    if (!empty($from_date)) {
        $this->db->where('DATE(m.TransDate) >=', $from_date);
    }
    if (!empty($to_date)) {
        $this->db->where('DATE(m.TransDate) <=', $to_date);
    }
    if (!empty($filter_location)) {
        $this->db->where('m.CenterLocation', $filter_location);
    }
    if (!empty($filter_godown)) {
        $this->db->where('m.WarehouseID', $filter_godown);
    }
    if (!empty($filter_item)) {
        $this->db->where('m.ItemID', $filter_item);
    }


    $this->db->order_by('m.id', 'ASC');
    return $this->db->get()->result_array();
}


public function GetMandiDetailsall($id, $order_id) {
    $this->db->select('
        m.FY,m.PlantID,m.OrderID, m.OrderDate, m.CenterLocation, m.WarehouseID,
        m.ItemType, m.ItemID, m.TDSCode, m.VehicleNo,
        m.LeaderGroupID, m.LeaderID, m.TotalQty, m.TotalValue,
        m.TotalBrokerage, m.TotalMarketPrice, m.TotalGrossValue,
        m.TDSAmt AS MasterTDSAmt, m.FinalAmt, m.TransDate', FALSE);
    $this->db->from('tblMandiPurchaseMaster m');
    $this->db->where('m.OrderID', $order_id);
    // Uncomment below if $id filters something specific:
    // $this->db->where('h.id', $id);

    $query = $this->db->get();

    if ($query && $query->num_rows() > 0) {
        return $query->result_array();
    }
    return [];
}


public function GetMandiDetailsalldata($id, $order_id) {
    $this->db->select('
        h.id as id, h.PurchID as MandiPurchaseID, h.DocNo as DocumentNo, h.AccountID as VendorID,
        h.Payment_term as PaymentTerm, h.bag as BagQty, h.wt_per_bag as WeightPerBag, h.loose_in_kg as LooseKG,
        h.OrderQty as QtyQuintal, h.PurchRate as RatePerQuintal, h.OrderAmt as Value, h.BrokerAmt as Brokerage,
        h.MrtLevyAmt as MarketLevy, h.Round_off as RoundOff, h.GrossAmt as Gross,
        h.TDSAmt AS HistoryTDSAmt, h.NetOrderAmt as NetAmt, h.LupDate as CreatedDate, h.UserID as CreatedBy
    ', FALSE);
    $this->db->from('tblMandiPurchaseDetails h');
    $this->db->where('h.PurchID', $order_id);
    // Uncomment below if $id filters something specific:
    // $this->db->where('h.id', $id);

    $query = $this->db->get();

    if ($query && $query->num_rows() > 0) {
        return $query->result_array();
    }
    return [];
}


public function GetMandiDetailsallPDF($id, $order_id) {
    $this->db->select('
        m.FY,m.PlantID,m.OrderID, m.OrderDate, 
        m.ItemType,  m.VehicleNo,
        m.LeaderGroupID, m.LeaderID, m.TotalQty, m.TotalValue,
        m.TotalBrokerage, m.TotalMarketPrice, m.TotalGrossValue,
        m.TDSAmt AS MasterTDSAmt, m.FinalAmt, m.TransDate ,  l.LocationName  AS CenterLocation,
        g.GodownName    AS WarehouseID,
        i.ItemName      AS ItemID,
        t.TDSName       AS TDSCode', FALSE);
    $this->db->from('tblMandiPurchaseMaster m');
       $this->db->join('tblPlantLocationDetails l', 'l.id = m.CenterLocation', 'left');
    $this->db->join('tblgodownmaster g',         'g.id = m.WarehouseID',    'left');
    $this->db->join('tblitems i',                'i.ItemID = m.ItemID',     'left');
    $this->db->join('tblTDSMaster t',                't.TDSCode = m.TDSCode',     'left');

    $this->db->where('m.OrderID', $order_id);
    // Uncomment below if $id filters something specific:
    // $this->db->where('h.id', $id);

    $query = $this->db->get();

    if ($query && $query->num_rows() > 0) {
        return $query->result_array();
    }
    return [];
}

public function GetMandiDetailsalldataPDF($id, $order_id) {
    $this->db->select('
         h.id as id, h.PurchID as MandiPurchaseID, h.DocNo as DocumentNo, h.AccountID as VendorID,
        h.Payment_term as PaymentTerm, h.bag as BagQty, h.wt_per_bag as WeightPerBag, h.loose_in_kg as LooseKG,
        h.OrderQty as QtyQuintal, h.PurchRate as RatePerQuintal, h.OrderAmt as Value, h.BrokerAmt as Brokerage,
        h.MrtLevyAmt as MarketLevy, h.Round_off as RoundOff, h.GrossAmt as Gross,
        h.TDSAmt AS HistoryTDSAmt, h.NetOrderAmt as NetAmt, h.LupDate as CreatedDate, h.UserID as CreatedBy,
     v.company AS VendorID
    ', FALSE);
    $this->db->from('tblMandiPurchaseDetails h');
    $this->db->join('tblclients v', 'v.AccountID = h.AccountID', 'left');
    $this->db->where('h.PurchID', $order_id);
    // Uncomment below if $id filters something specific:
    // $this->db->where('h.id', $id);

    $query = $this->db->get();

    if ($query && $query->num_rows() > 0) {
        return $query->result_array();
    }
    return [];
}


}