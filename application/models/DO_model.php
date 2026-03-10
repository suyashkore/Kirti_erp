<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DO_model extends App_Model
{
  protected $table = 'SalesOrderMaster';
  protected $primaryKey = 'id';
  public function __construct()
  {
    parent::__construct();
  }

  public function getCityList()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'xx_citylist');
    return $this->db->get()->result_array();
  }

  // ===== GET OTHER TABLE DROPDOWN DATA =====
  public function getDropdown($table, $fields, $where = null, $order = null, $orderBy = 'ASC')
  {
    $this->db->select($fields);
    $this->db->from(db_prefix() . $table);
    if ($where != null) {
      $this->db->where($where);
    }
    if ($order != null) {
      $this->db->order_by($order, $orderBy);
    }
    return $this->db->get()->result_array();
  }

  // ===== CHECK DUPLICATE =====
  public function checkDuplicate($table, $where = null)
  {
    $this->db->where($where);
    return $this->db->count_all_results(db_prefix() . $table) > 0;
  }

  // ===== SAVE DATA =====
  public function saveData($table, $data)
  {
    $data['UserID'] = $this->session->userdata('username');
    $this->db->insert(db_prefix() . $table, $data);
    return $this->db->insert_id();
  }

  // ===== UPDATE DATA =====
  public function updateData($table, $data, $where = null)
  {
    $data['UserID2'] = $this->session->userdata('username');
    $this->db->where($where);
    return $this->db->update(db_prefix() . $table, $data);
  }

  // ===== GET ROW DATA =====
  public function getRowData($table, $select = '*', $where = null)
  {
    $this->db->select($select);
    $this->db->from(db_prefix() . $table);
    if ($where != null) $this->db->where($where);
    return $this->db->get()->row();
  }

  public function getCustomerDropdown()
  {
    $this->db->select('c.userid, c.AccountID, c.company');
    $this->db->from(db_prefix() . 'clients c');
    $this->db->join(
      db_prefix() . 'AccountSubGroup2 a',
      'a.SubActGroupID = c.ActSubGroupID2',
      'inner'
    );
    $this->db->where('a.IsCustomer', 'Y');

    $query = $this->db->get();
    return $query->result_array();
  }

  public function getSODropdown()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'SalesOrderMaster');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function getTransporterDropdown()
  {
    $this->db->select('c.userid, c.AccountID, c.company');
    $this->db->from(db_prefix() . 'clients c');
    $this->db->join(
      db_prefix() . 'AccountSubGroup2 a',
      'a.SubActGroupID = c.ActSubGroupID2',
      'inner'
    );
    $this->db->where('a.IsTransporter', 'Y');

    $query = $this->db->get();
    return $query->result_array();
  }

  public function getVehicleNoDropdown()
  {
    $this->db->select('distinct(v.VehicleNo) as VehicleNo');
    $this->db->from(db_prefix() . 'vehicle v');
    $this->db->where('v.IsActive', 'Y');
    $query = $this->db->get();
    return $query->result_array();
  }

  public function getCategoryDropdown()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'ItemCategoryMaster');

    $query = $this->db->get();
    return $query->result_array();
  }

  public function getNextDONo()
  {

    // Get Financial Year & Root Company from session
    $FY      = $this->session->userdata('finacial_year');   // e.g. 25
    $PlantID = $this->session->userdata('root_company');    // e.g. 1

    // Count existing Delivery Order
    $this->db->from(db_prefix() . 'DeliveryOrderMaster');
    $count = $this->db->count_all_results();

    $next_number = $count + 1;

    // Prefix format: SQ + FY + PlantID + ShortCode
    $prefix = 'DO' . $FY . $PlantID;

    // Final Quotation Number
    $order_no = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);

    return $order_no;
  }


  /**
   * Check if a history record is locked.
   * A record is locked if another history row exists with:
   * - same OrderID + ItemID + AccountID
   * - different TransID
   * - excluding the current record (by id)
   */
  public function isHistoryLocked($history_id, $order_id, $item_id, $account_id, $trans_id)
  {
    $this->db->from(db_prefix() . 'history');
    $this->db->where('OrderID', $order_id);
    $this->db->where('ItemID', $item_id);
    $this->db->where('AccountID', $account_id);
    $this->db->where('TransID !=', $trans_id);
    $this->db->where('TransID IS NOT NULL', null, false);
    // $this->db->where('id !=', $history_id);
    $this->db->where('id >', $history_id);
    $count = $this->db->count_all_results();
    return $count > 0;
  }



  public function saveMultiData($data)
  {
    $customer_state = isset($data['customer_state']) ? strtoupper(trim($data['customer_state'])) : '';
    if (count($data['item_id']) <= 0) {
      return false;
    }
    for ($i = 0; $i < count($data['item_id']); $i++) {
      $gst_percent = floatval($data['gst'][$i]) ?? 0;
      $taxable_amt = ((floatval($data['unit_rate'][$i]) ?? 0) - (floatval($data['disc_amt'][$i]) ?? 0)) * (floatval($data['quantity'][$i]) ?? 0);

      $cgst = $sgst = $cgstamt = $sgstamt = $igst = $igstamt = 0;
      if ($customer_state === 'MAHARASHTRA') {
        $cgst = $sgst = $gst_percent / 2;
        $cgstamt = $sgstamt = ($taxable_amt * $cgst) / 100;
      } else {
        $igst = $gst_percent;
        $igstamt = ($taxable_amt * $igst) / 100;
      }
      $item_uid = $data['item_uid'][$i];
      $order_id  = $data['so_no'][$i];
      $item_id   = $data['item_id'][$i];
      $account_id = $data['customer_id'];
      $trans_id  = $data['TransID'];

      $item_data = [
        'OrderID' => $data['so_no'][$i],
        'TransID' => $data['TransID'],
        'PlantID' => $data['plant_id'],
        'FY' => $data['fy'],
        'TransDate' => date('Y-m-d'),
        'AccountID' => $data['customer_id'] ?? '',
        'ItemID' => $data['item_id'][$i],
        'BasicRate' => $data['unit_rate'][$i],
        'SaleRate' => ($data['unit_rate'][$i] * $gst_percent) / 100,
        'SuppliedIn' => $data['uom'][$i],
        'UnitWeight' => $data['unit_weight'][$i],
        'WeightUnit' => $data['uom'][$i],
        'OrderQty' => $data['disquantity'][$i],
        'DiscAmt' => $data['disc_amt'][$i],
        'cgst' => $cgst,
        'cgstamt' => $cgstamt,
        'sgst' => $sgst,
        'sgstamt' => $sgstamt,
        'igst' => $igst,
        'igstamt' => $igstamt,
        'OrderAmt' => $data['quantity'][$i] * $data['unit_rate'][$i],
        'NetOrderAmt' => $data['amount'][$i],
        'Ordinalno' =>  $i + 1,
        'TType' => 'S',
        'TType2' => 'Delivery',
      ];

      if ($item_uid == 0) {
        // ── INSERT new record ──
        $item_data['UserID']     = $this->session->userdata('username');
        $item_data['TransDate2'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'history', $item_data);
      } else {
        // ── UPDATE existing record — check lock first ──
        $locked = $this->isHistoryLocked($item_uid, $order_id, $item_id, $account_id, $trans_id);

        if ($locked) {
          // Skip silently — locked row should not be updated
          // The frontend already prevents editing, this is a backend safety net
          continue;
        }

        $item_data['UserID2'] = $this->session->userdata('username');
        $item_data['Lupdate'] = date('Y-m-d H:i:s');
        $this->db->where('id', $item_uid);
        $this->db->update(db_prefix() . 'history', $item_data);
      }
    }
  }

  public function getOrderList($customer_id = null, $type = 'valid')
  {
    $today = date('Y-m-d');

    $this->db->select("
        pm.*,
        c.company,

        h.ItemID as history_item_id,
        h.BasicRate,
        h.id as history_id,
        h.OrderQty as Item_Order_Qty,
        h.OrderQty as OrderQty,
        h.OrderAmt,

        IFNULL(used.TotalUsedQty, 0) as UsedQty,
        (h.OrderQty - IFNULL(used.TotalUsedQty, 0)) as BalanceQty,

        IFNULL(used.TotalUsedAmt, 0) as UsedAmt,
        
        i.ItemName as item_name,
        i.unit as item_shortcode,
        i.tax as item_tax,
        u.ShortCode as item_unit,

        h.TransID
    ", false);

    $this->db->from(db_prefix() . 'SalesOrderMaster pm');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = pm.AccountID', 'left');

    // Join all history rows
    $this->db->join(
      db_prefix() . 'history h',
      'h.OrderID = pm.OrderID',
      'left'
    );

    // Subquery for used qty and amount
    $used_subquery = "
        (
            SELECT 
                OrderID,
                ItemID,
                SUM(OrderQty) as TotalUsedQty,
                SUM(OrderQty * BasicRate) as TotalUsedAmt
            FROM " . db_prefix() . "history
            WHERE TransID IS NOT NULL
            GROUP BY OrderID, ItemID
        ) used
    ";
    $this->db->join($used_subquery, 'used.OrderID = h.OrderID AND used.ItemID = h.ItemID', 'left', false);

    $this->db->join(db_prefix() . 'items i', 'i.ItemID = h.ItemID', 'left');
    $this->db->join(db_prefix() . 'UnitMaster u', 'u.id = i.unit', 'left');

    if ($customer_id) {
      $this->db->where('pm.AccountID', $customer_id);
    }

    // Only original rows
    $this->db->where('h.TransID IS NULL', null, false);

    $balanceExpr = '(h.OrderQty - IFNULL(used.TotalUsedQty, 0))';

    // Optional: valid / invalid
    if ($type == 'valid') {
      $this->db->where('pm.DeliveryTo >=', $today);
      $this->db->where("$balanceExpr >= 1", null, false);
    } else {
      $this->db->where('pm.DeliveryTo <', $today);
      $this->db->where("$balanceExpr >= 1", null, false);
    }

    $this->db->order_by('pm.TransDate', 'DESC');

    $result = $this->db->get()->result_array();

    return $result;
  }


  public function getDeliveryOrderList()
  {
    $this->db->select('dom.*, icm.CategoryName as CategoryName, c.company');
    $this->db->from(db_prefix() . 'DeliveryOrderMaster dom');
    $this->db->join(
      db_prefix() . 'ItemCategoryMaster icm',
      'icm.Id = dom.CategoryID',
      'left'
    );
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = dom.AccountID', 'left');

    return $this->db->get()->result_array();
  }

  public function gethistoryList()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'history');
    return $this->db->get()->result_array();
  }

  public function getgateinList()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'GateMaster');
    return $this->db->get()->result_array();
  }

  public function getOrderDetails($id)
  {
    $this->db->select('pm.*, c.company');
    $this->db->from(db_prefix() . 'SalesOrderMaster pm');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = pm.AccountID', 'left');
    $this->db->where('pm.id', $id);
    $master = $this->db->get()->row_array();

    if (!$master) {
      return [];
    }

    $this->db->from(db_prefix() . 'history');
    $this->db->where('OrderID', $master['OrderID']);
    $history = $this->db->get()->result_array();
    $master['history'] = $history;

    return $master;
  }

  public function getDeliveryOrderDetails($id)
{
    // ── Get Master Record ──
    $this->db->select('dom.*, c.company, icm.CategoryName');
    $this->db->from(db_prefix() . 'DeliveryOrderMaster dom');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = dom.AccountID', 'left');
    $this->db->join(db_prefix() . 'ItemCategoryMaster icm', 'icm.Id = dom.CategoryID', 'left');
    $this->db->where('dom.id', $id);

    $master = $this->db->get()->row_array();

    if (!$master || empty($master['OrderID'])) {
        return [];
    }

    // ── Subquery for Total Ordered Qty (TransID IS NULL) ──
    $order_subquery = "
        (
            SELECT 
                OrderID,
                ItemID,
                SUM(OrderQty) as TotalOrderQty
            FROM " . db_prefix() . "history
            WHERE TransID IS NULL
            GROUP BY OrderID, ItemID
        ) u
    ";

    // ── Subquery for Used Qty ──
    $used_subquery = "
        (
            SELECT 
                OrderID,
                ItemID,
                SUM(OrderQty) as TotalUsedQty
            FROM " . db_prefix() . "history
            WHERE TransID IS NOT NULL
            GROUP BY OrderID, ItemID
        ) used
    ";

    // ── Get History Records ──
    $this->db->select('
        h.*,
        IFNULL(u.TotalOrderQty, 0) as TotalOrderQty,
        IFNULL(used.TotalUsedQty, 0) as UsedQty,
        (IFNULL(u.TotalOrderQty, 0) - IFNULL(used.TotalUsedQty, 0)) as BalanceQty
    ');
    $this->db->from(db_prefix() . 'history h');

    $this->db->join($order_subquery, 'u.OrderID = h.OrderID AND u.ItemID = h.ItemID', 'left', false);
    $this->db->join($used_subquery, 'used.OrderID = h.OrderID AND used.ItemID = h.ItemID', 'left', false);

    $this->db->where('h.TransID', $master['OrderID']);

    $history = $this->db->get()->result_array();

    foreach ($history as &$row) {
        $row['is_locked'] = $this->isHistoryLocked(
            $row['id'],
            $row['OrderID'],
            $row['ItemID'],
            $row['AccountID'],
            $row['TransID']
        ) ? 1 : 0;
    }
    unset($row);

    $master['history'] = $history;

    return $master;
}

  public function getCustomerBrokerList($customer_id)
  {
    $this->db->select('c.AccountID, c.company');
    $this->db->from(db_prefix() . 'PartyBrokerMaster pbm');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = pbm.BrokerID', 'left');
    $this->db->where('pbm.AccountID', $customer_id);
    return $this->db->get()->result_array();
  }

  public function getItemDetailsById($item_id)
  {
    // Select item fields and join taxes to get tax rate
    $this->db->select(
      db_prefix() . "items.ItemID,
        " . db_prefix() . "items.ItemName,
			" . db_prefix() . "items.hsn_code,
			tblUnitMaster.ShortCode as unit,
			" . db_prefix() . "items.UnitWeight,
			tbltaxes.taxrate as tax"
    );

    $this->db->from(db_prefix() . 'items');
    // join taxes table to fetch tax rate (tbltaxes.id = items.tax)
    $this->db->join('tbltaxes', 'tbltaxes.id = ' . db_prefix() . "items.tax", 'left');
    // join unit master to get ShortCode for unit (tblUnitMaster.id = items.unit)
    $this->db->join('tblUnitMaster', 'tblUnitMaster.id = ' . db_prefix() . "items.unit", 'left');
    $this->db->where(db_prefix() . 'items.ItemID', $item_id);

    $result = $this->db->get()->result_array();

    if (!empty($result)) {
      return array(
        'ItemID' => $result[0]['ItemID'] ?? '',
        'ItemName' => $result[0]['ItemName'] ?? '',
        'hsn_code' => $result[0]['hsn_code'] ?? '',
        'unit' => $result[0]['unit'] ?? '',
        'UnitWeight' => $result[0]['UnitWeight'] ?? '0',
        'tax' => $result[0]['tax'] ?? '0',
      );
    }

    return array();
  }

  public function getVehicleDetailsByVehicleNo($vehicle_no)
  {
    $this->db->select('tblvehicle.*, tblGateMaster.GateINID');
    $this->db->from(db_prefix() . 'vehicle as tblvehicle');
    $this->db->join('tblGateMaster', 'tblvehicle.VehicleNo = tblGateMaster.VehicleNo', 'left');
    $this->db->where('tblvehicle.VehicleNo', $vehicle_no);  // FIXED

    $result = $this->db->get()->result_array();

    return $result;
  }

  public function get_company_detail()
  {
    $selected_company = $this->session->userdata('root_company');
    $sql = 'SELECT ' . db_prefix() . 'rootcompany.*, tblxx_statelist.state_name as state FROM ' . db_prefix() . 'rootcompany LEFT JOIN tblxx_statelist ON tblxx_statelist.short_name = ' . db_prefix() . 'rootcompany.state WHERE tblrootcompany.id = ' . $selected_company;

    $result = $this->db->query($sql)->row();
    return $result;
  }

  public function getListByFilter($data, $limit, $offset)
  {
    $from_date  = $data['from_date'] ?? date('Y-m-01');
    $to_date    = $data['to_date'] ?? date('Y-m-d');
    $customer_id  = $data['customer_id'] ?? '';
    $broker_id  = $data['broker_id'] ?? '';
    $status     = $data['status'] ?? 1;

    $this->db->from(db_prefix() . $this->table);

    $this->db->join(db_prefix() . 'clients customer', 'customer.AccountID = ' . db_prefix() . $this->table . '.AccountID', 'left');
    $this->db->join(db_prefix() . 'clients broker', 'broker.AccountID = ' . db_prefix() . $this->table . '.BrokerID', 'left');

    if ($customer_id != '')       $this->db->where(db_prefix() . $this->table . '.AccountID', $customer_id);
    if ($broker_id != '')       $this->db->where(db_prefix() . $this->table . '.BrokerID', $broker_id);
    // if($status != '')          $this->db->where(db_prefix().$this->table.'.Status', $status);

    $from_date  = $data['from_date'] ?? '';
    $to_date    = $data['to_date'] ?? '';

    if (!empty($from_date)) {
      $from_date = DateTime::createFromFormat('d/m/Y', $from_date)->format('Y-m-d 00:00:00');
    }

    if (!empty($to_date)) {
      $to_date = DateTime::createFromFormat('d/m/Y', $to_date)->format('Y-m-d 23:59:59');
    }


    if ($from_date != '')       $this->db->where(db_prefix() . $this->table . '.TransDate >=', $from_date);
    if ($to_date != '')         $this->db->where(db_prefix() . $this->table . '.TransDate <=', $to_date);

    $total = $this->db->count_all_results('', FALSE);

    $this->db->select([
      db_prefix() . $this->table . '.id',
      db_prefix() . $this->table . '.OrderID',
      db_prefix() . $this->table . '.TransDate',
      db_prefix() . $this->table . '.AccountID',
      db_prefix() . $this->table . '.BrokerID',
      db_prefix() . $this->table . '.TotalWeight',

      db_prefix() . $this->table . '.TotalQuantity',
      db_prefix() . $this->table . '.ItemAmt',
      db_prefix() . $this->table . '.DiscAmt',
      db_prefix() . $this->table . '.TaxableAmt',
      db_prefix() . $this->table . '.CGSTAmt',
      db_prefix() . $this->table . '.SGSTAmt',
      db_prefix() . $this->table . '.IGSTAmt',
      db_prefix() . $this->table . '.RoundOffAmt',
      db_prefix() . $this->table . '.NetAmt',

      db_prefix() . $this->table . '.NetAmt',
      'customer.company as customer_name',
      'broker.company as broker_name'
    ]);

    $this->db->order_by($this->primaryKey, 'desc');
    $this->db->limit($limit, $offset);

    $rows = $this->db->get()->result_array();

    return [
      'total' => $total,
      'rows'  => $rows
    ];
  }


  public function getData($table, $select = '*', $where = null)
  {
    $this->db->select($select);
    $this->db->from(db_prefix() . $table);
    if ($where != null) $this->db->where($where);
    return $this->db->get()->row_array();
  }


  public function get_freight_terms()
  {
    $this->db->select([db_prefix() . 'FreightTerms.*']);
    $this->db->where(db_prefix() . 'FreightTerms.IsActive', 'Y');
    $this->db->order_by(db_prefix() . 'FreightTerms.Id', 'ASC');
    return $this->db->get('tblFreightTerms')->result_array();
  }

  public function get_dispatch_location()
  {
    $selected_company = $this->session->userdata('root_company');

    $this->db->select([db_prefix() . 'PlantLocationDetails.*']);
    $this->db->where(db_prefix() . 'PlantLocationDetails.PlantID', $selected_company);
    return $this->db->get('tblPlantLocationDetails')->result_array();
  }

  // public function getCustomerDetailByAccountID($AccountID)
  // {
  //   $this->db->select(
  //     'AccountID,
	// 		company,
	// 		GSTIN as gst_number,
	// 		PAN as pan,
	// 		billing_city as city,
	// 		billing_zip as postal_code,
	// 		billing_address as address,
	// 		tblxx_statelist.state_name as state,
	// 		tblcountries.long_name as country'
  //   );

  //   $this->db->from(db_prefix() . 'clients');

  //   $this->db->join('tblxx_statelist', 'clients.billing_state = tblxx_statelist.short_name', 'left');
  //   $this->db->join('tblcountries', 'clients.billing_country = tblcountries.country_id', 'left');

  //   $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

  //   $result = $this->db->get()->result_array();

  //   if (!empty($result)) {
  //     return array(
  //       'gst_no' => $result[0]['gst_number'] ?? '',
  //       'pan' => $result[0]['pan'] ?? '',
  //       'country' => $result[0]['country'] ?? '',
  //       'state' => $result[0]['state'] ?? '',
  //       'city' => $result[0]['city'] ?? '',
  //       'postal_code' => $result[0]['postal_code'] ?? '',
  //       'address' => $result[0]['address'] ?? '',
  //       'company' => $result[0]['company'] ?? '',

  //     );
  //   }

  //   return array();
  // }

  // public function getShippingDatacity($AccountID)
  // {
  //   $this->db->select(
  //     'tblclientwiseshippingdata.id,
	// 		tblxx_citylist.city_name'
  //   );

  //   $this->db->from('tblclientwiseshippingdata');
  //   $this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclientwiseshippingdata.ShippingCity', 'LEFT');
  //   $this->db->where('tblclientwiseshippingdata.AccountID', $AccountID);

  //   return $this->db->get()->result_array();
  // }

  
}
