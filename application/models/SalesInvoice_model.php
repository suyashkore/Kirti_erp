<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SalesInvoice_model extends App_Model
{
  protected $table = 'SalesInvoiceMaster';
  protected $primaryKey = 'id';
  public function __construct()
  {
    parent::__construct();
  }

  // ===== CHECK DUPLICATE =====
  public function checkDuplicate($table, $where = null)
  {
    $this->db->where($where);
    return $this->db->count_all_results(db_prefix() . $table) > 0;
  }

  // ===== DELIVERY ORDER DATA =====
  public function getHistoryDetails()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'history');
    $this->db->where('BillID is NOT NULL');
    return $this->db->get()->result_array();
  }

  // ===== DELIVERY ORDER DATA =====
  public function getDeliveryOrderDetails($id)
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'DeliveryOrderMaster');
    $this->db->where('OrderID', $id);
    return $this->db->get()->row_array();
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

  // ===== CUSTOMER DATA =====
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

  public function getCategoryDropdown()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'ItemCategoryMaster');

    $query = $this->db->get();
    return $query->result_array();
  }

  public function getNextSINo()
  {
    // Get Financial Year & Root Company from session
    $FY      = $this->session->userdata('finacial_year');   // e.g. 25
    $PlantID = $this->session->userdata('root_company');    // e.g. 1

    // Count existing Delivery Order
    $this->db->from(db_prefix() . 'SalesInvoiceMaster');
    $count = $this->db->count_all_results();

    $next_number = $count + 1;

    // Prefix format: SQ + FY + PlantID + ShortCode
    $prefix = 'SI' . $FY . $PlantID;

    // Final Quotation Number
    $order_no = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);

    return $order_no;
  }

  // Comapny Details
  public function get_company_detail()
  {
    $selected_company = $this->session->userdata('root_company');
    $sql = 'SELECT ' . db_prefix() . 'rootcompany.*, tblxx_statelist.state_name as state FROM ' . db_prefix() . 'rootcompany LEFT JOIN tblxx_statelist ON tblxx_statelist.short_name = ' . db_prefix() . 'rootcompany.state WHERE tblrootcompany.id = ' . $selected_company;

    $result = $this->db->query($sql)->row();
    return $result;
  }

  public function getSalesInvoiceList()
  {
    $this->db->select('sim.*, icm.CategoryName as CategoryName, c.company');
    $this->db->from(db_prefix() . 'SalesInvoiceMaster sim');
    $this->db->join(
      db_prefix() . 'ItemCategoryMaster icm',
      'icm.Id = sim.CategoryID',
      'left'
    );
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = sim.AccountID', 'left');

    return $this->db->get()->result_array();
  }

  // Delivery Order Drop Down List
  public function getDeliveryOrderListDetails($id)
  {
    $this->db->select('dom.*, c.company, icm.CategoryName');
    $this->db->from(db_prefix() . 'DeliveryOrderMaster dom');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = dom.AccountID', 'left');
    $this->db->join(db_prefix() . 'ItemCategoryMaster icm', 'icm.Id = dom.CategoryID', 'left');
    $this->db->where('dom.OrderID', $id);

    $master = $this->db->get()->row_array();

    if (!$master || empty($master['OrderID'])) {
      return [];
    }

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

    $master['history'] = $history;

    return $master;
  }

  // Sales Invoice Data
  public function getSalesInvoiceDetails($id)
  {
    // Get Master Record
    $this->db->select('sim.*, c.company, icm.CategoryName');
    $this->db->from(db_prefix() . 'SalesInvoiceMaster sim');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = sim.AccountID', 'left');
    $this->db->join(db_prefix() . 'ItemCategoryMaster icm', 'icm.Id = sim.CategoryID', 'left');
    $this->db->where('sim.id', $id);

    $master = $this->db->get()->row_array();

    if (!$master || empty($master['DeliveryOrderID'])) {
      return [];
    }

    // Subquery for Total Ordered Qty (TransID IS NULL)
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

    // Subquery for Used Qty
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

    // Get History Records
    $this->db->select('
        h.*,
        IFNULL(u.TotalOrderQty, 0) as TotalOrderQty,
        IFNULL(used.TotalUsedQty, 0) as UsedQty,
        (IFNULL(u.TotalOrderQty, 0) - IFNULL(used.TotalUsedQty, 0)) as BalanceQty
    ');
    $this->db->from(db_prefix() . 'history h');

    $this->db->join($order_subquery, 'u.OrderID = h.OrderID AND u.ItemID = h.ItemID', 'left', false);
    $this->db->join($used_subquery, 'used.OrderID = h.OrderID AND used.ItemID = h.ItemID', 'left', false);

    $this->db->where('h.TransID', $master['DeliveryOrderID']);

    $history = $this->db->get()->result_array();

    $master['history'] = $history;

    return $master;
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

      $item_data = [
        'OrderID' => $data['so_no'][$i],
        'BillID' => $data['BillID'],
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
        'TType2' => 'Invoice',
      ];

      if ($item_uid == 0) {
        // INSERT new record
        $item_data['UserID']     = $this->session->userdata('username');
        $item_data['TransDate2'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'history', $item_data);
      } else {
        $item_data['UserID2'] = $this->session->userdata('username');
        $item_data['Lupdate'] = date('Y-m-d H:i:s');
        $this->db->where('id', $item_uid);
        $this->db->update(db_prefix() . 'history', $item_data);
      }
    }
  }



  public function getListByFilter($data, $limit, $offset)
  {
    $from_date  = $data['from_date'] ?? date('Y-m-01');
    $to_date    = $data['to_date'] ?? date('Y-m-d');

    $this->db->from(db_prefix() . $this->table);

    $this->db->join(db_prefix() . 'clients customer', 'customer.AccountID = ' . db_prefix() . $this->table . '.AccountID', 'left');

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
      db_prefix() . $this->table . '.InvoiceID',
      db_prefix() . $this->table . '.InvoiceDate',
      db_prefix() . $this->table . '.AccountID',
      db_prefix() . $this->table . '.TotalWt',

      db_prefix() . $this->table . '.TotalQty',
      db_prefix() . $this->table . '.ItemTotal',
      db_prefix() . $this->table . '.TotalDisc',
      db_prefix() . $this->table . '.TaxAmt',
      db_prefix() . $this->table . '.CGSTAmt',
      db_prefix() . $this->table . '.SGSTAmt',
      db_prefix() . $this->table . '.IGSTAmt',
      db_prefix() . $this->table . '.RoundOff',
      db_prefix() . $this->table . '.NetAmt',

      db_prefix() . $this->table . '.NetAmt',
      'customer.company as customer_name',
    ]);

    $this->db->order_by($this->primaryKey, 'asc');
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

  public function get_dispatch_location()
  {
    $selected_company = $this->session->userdata('root_company');

    $this->db->select([db_prefix() . 'PlantLocationDetails.*']);
    $this->db->where(db_prefix() . 'PlantLocationDetails.PlantID', $selected_company);
    return $this->db->get('tblPlantLocationDetails')->result_array();
  }

  // Sales Ledger is Created
  public function createSalesLedger($InvoiceID, $DeliveryOrderID, $CustomerID, $InvoiceDate, $TotalItemAmt, $TotalDiscAmt, $TotalCGSTAmt, $TotalSGSTAmt, $TotalIGSTAmt, $NetAmt, $RoundOffAmt)
  {
    $PlantID = $this->session->userdata('root_company');
    $FY = $this->session->userdata('finacial_year');
    $Narration = "Sales Invoice against " . $InvoiceID . " Delivery Order " . $DeliveryOrderID;
    $ordNo = 1;

    // Customer Debit
    $CustomerLedger = array(
      "PlantID" => $PlantID,
      "FY" => $FY,
      "Transdate" => $InvoiceDate,
      "VoucherID" => $InvoiceID,
      "TransDate2" => date('Y-m-d H:i:s'),
      "AccountID" => $CustomerID,
      "EffectOn" => "SALES",
      "TType" => "D",
      "Amount" => $NetAmt,
      "Narration" => $Narration,
      "PassedFrom" => "SALES",
      "OrdinalNo" => $ordNo,
      "UserID" => $this->session->userdata('username'),
    );
    $this->db->insert('tblaccountledger', $CustomerLedger);
    $ordNo++;

    // Sales Credit
    $SalesLedger = array(
      "PlantID" => $PlantID,
      "FY" => $FY,
      "Transdate" => $InvoiceDate,
      "VoucherID" => $InvoiceID,
      "TransDate2" => date('Y-m-d H:i:s'),
      "AccountID" => "SALES",
      "EffectOn" => $CustomerID,
      "TType" => "C",
      "Amount" => $TotalItemAmt,
      "Narration" => $Narration,
      "PassedFrom" => "SALES",
      "OrdinalNo" => $ordNo,
      "UserID" => $this->session->userdata('username'),
    );
    $this->db->insert('tblaccountledger', $SalesLedger);
    $ordNo++;

    // GST Ledger
    if ($TotalIGSTAmt > 0) {
      // IGST
      $IGSTLedger = array(
        "PlantID" => $PlantID,
        "FY" => $FY,
        "Transdate" => $InvoiceDate,
        "VoucherID" => $InvoiceID,
        "TransDate2" => date('Y-m-d H:i:s'),
        "AccountID" => "IGSTOUT",
        "EffectOn" => $CustomerID,
        "TType" => "C",
        "Amount" => $TotalIGSTAmt,
        "Narration" => $Narration,
        "PassedFrom" => "SALES",
        "OrdinalNo" => $ordNo,
        "UserID" => $this->session->userdata('username'),
      );
      $this->db->insert('tblaccountledger', $IGSTLedger);
      $ordNo++;
    } else {
      // CGST
      $CGSTLedger = array(
        "PlantID" => $PlantID,
        "FY" => $FY,
        "Transdate" => $InvoiceDate,
        "VoucherID" => $InvoiceID,
        "TransDate2" => date('Y-m-d H:i:s'),
        "AccountID" => "CGSTOUT",
        "EffectOn" => $CustomerID,
        "TType" => "C",
        "Amount" => $TotalCGSTAmt,
        "Narration" => $Narration,
        "PassedFrom" => "SALES",
        "OrdinalNo" => $ordNo,
        "UserID" => $this->session->userdata('username'),
      );
      $this->db->insert('tblaccountledger', $CGSTLedger);
      $ordNo++;

      // SGST
      $SGSTLedger = array(
        "PlantID" => $PlantID,
        "FY" => $FY,
        "Transdate" => $InvoiceDate,
        "VoucherID" => $InvoiceID,
        "TransDate2" => date('Y-m-d H:i:s'),
        "AccountID" => "SGSTOUT",
        "EffectOn" => $CustomerID,
        "TType" => "C",
        "Amount" => $TotalSGSTAmt,
        "Narration" => $Narration,
        "PassedFrom" => "SALES",
        "OrdinalNo" => $ordNo,
        "UserID" => $this->session->userdata('username'),
      );

      $this->db->insert('tblaccountledger', $SGSTLedger);
      $ordNo++;
    }

    // Discount
    if ($TotalDiscAmt > 0) {
      $DiscLedger = array(
        "PlantID" => $PlantID,
        "FY" => $FY,
        "Transdate" => $InvoiceDate,
        "VoucherID" => $InvoiceID,
        "TransDate2" => date('Y-m-d H:i:s'),
        "AccountID" => "SDISC",
        "EffectOn" => "SALES",
        "TType" => "D",
        "Amount" => $TotalDiscAmt,
        "Narration" => $Narration,
        "PassedFrom" => "SALES",
        "OrdinalNo" => $ordNo,
        "UserID" => $this->session->userdata('username'),
      );

      $this->db->insert('tblaccountledger', $DiscLedger);
      $ordNo++;
    }

    // Round Off
    if ($RoundOffAmt != 0) {
      $type = $RoundOffAmt > 0 ? 'C' : 'D';
      $RoundLedger = array(
        "PlantID" => $PlantID,
        "FY" => $FY,
        "Transdate" => $InvoiceDate,
        "VoucherID" => $InvoiceID,
        "TransDate2" => date('Y-m-d H:i:s'),
        "AccountID" => "ROUNDOFF",
        "EffectOn" => $CustomerID,
        "TType" => $type,
        "Amount" => abs($RoundOffAmt),
        "Narration" => $Narration,
        "PassedFrom" => "SALES",
        "OrdinalNo" => $ordNo,
        "UserID" => $this->session->userdata('username'),
      );

      $this->db->insert('tblaccountledger', $RoundLedger);
    }
  }

  // Updating the Sales Ledger
  public function updateSalesLedger($InvoiceID, $DeliveryOrderID, $CustomerID, $InvoiceDate, $TotalItemAmt, $TotalDiscAmt, $TotalCGSTAmt, $TotalSGSTAmt, $TotalIGSTAmt, $NetAmt, $RoundOffAmt)
  {
    $PlantID = $this->session->userdata('root_company');
    $FY = $this->session->userdata('finacial_year');
    $Narration = "Sales Invoice against " . $InvoiceID . " Delivery Order " . $DeliveryOrderID;
    $ordNo = 1;

    // Common data for update
    $updateData = [
      "PlantID" => $PlantID,
      "FY" => $FY,
      "Transdate" => $InvoiceDate,
      "lupdate" => date('Y-m-d H:i:s'),
      "Narration" => $Narration,
      "PassedFrom" => "SALES",
      "UserID2" => $this->session->userdata('username'),
    ];

    // Update Customer Debit
    $this->db->where(['VoucherID' => $InvoiceID, 'TType' => 'D', 'AccountID' => $CustomerID]);
    $this->db->update('tblaccountledger', array_merge($updateData, [
      "EffectOn" => "SALES",
      "Amount" => $NetAmt,
      "OrdinalNo" => $ordNo++
    ]));

    // Update Sales Credit
    $this->db->where(['VoucherID' => $InvoiceID, 'TType' => 'C', 'AccountID' => 'SALES']);
    $this->db->update('tblaccountledger', array_merge($updateData, [
      "EffectOn" => $CustomerID,
      "Amount" => $TotalItemAmt,
      "OrdinalNo" => $ordNo++
    ]));

    // Update GST
    if ($TotalIGSTAmt > 0) {
      $this->db->where(['VoucherID' => $InvoiceID, 'AccountID' => 'IGSTOUT']);
      $this->db->update('tblaccountledger', array_merge($updateData, [
        "EffectOn" => $CustomerID,
        "TType" => 'C',
        "Amount" => $TotalIGSTAmt,
        "OrdinalNo" => $ordNo++
      ]));
    } else {
      // CGST
      $this->db->where(['VoucherID' => $InvoiceID, 'AccountID' => 'CGSTOUT']);
      $this->db->update('tblaccountledger', array_merge($updateData, [
        "EffectOn" => $CustomerID,
        "TType" => 'C',
        "Amount" => $TotalCGSTAmt,
        "OrdinalNo" => $ordNo++
      ]));

      // SGST
      $this->db->where(['VoucherID' => $InvoiceID, 'AccountID' => 'SGSTOUT']);
      $this->db->update('tblaccountledger', array_merge($updateData, [
        "EffectOn" => $CustomerID,
        "TType" => 'C',
        "Amount" => $TotalSGSTAmt,
        "OrdinalNo" => $ordNo++
      ]));
    }

    // Discount
    if ($TotalDiscAmt > 0) {
      $this->db->where(['VoucherID' => $InvoiceID, 'AccountID' => 'SDISC']);
      $this->db->update('tblaccountledger', array_merge($updateData, [
        "EffectOn" => "SALES",
        "TType" => 'D',
        "Amount" => $TotalDiscAmt,
        "OrdinalNo" => $ordNo++
      ]));
    }

    // Round Off
    if ($RoundOffAmt != 0) {
      $type = $RoundOffAmt > 0 ? 'C' : 'D';
      $this->db->where(['VoucherID' => $InvoiceID, 'AccountID' => 'ROUNDOFF']);
      $this->db->update('tblaccountledger', array_merge($updateData, [
        "EffectOn" => $CustomerID,
        "TType" => $type,
        "Amount" => abs($RoundOffAmt),
        "OrdinalNo" => $ordNo++
      ]));
    }
  }

  // Sales Ledger Ends Here

  public function GetSalesInvoiceDetailsForPdf($OrderID)
  {
    $this->db->select(
      db_prefix() . 'SalesInvoiceMaster.*, ' .

        // Client fields
        db_prefix() . 'clients.company, ' .
        db_prefix() . 'clients.billing_address, ' .
        db_prefix() . 'clients.billing_city, ' .
        db_prefix() . 'clients.billing_state, ' .
        db_prefix() . 'clients.GSTIN, ' .

        // Delivery Order fields
        db_prefix() . 'DeliveryOrderMaster.DODate, ' .
        db_prefix() . 'DeliveryOrderMaster.VehicleNo, ' .
        db_prefix() . 'DeliveryOrderMaster.DriverName, ' .

        // State
        db_prefix() . 'xx_statelist.state_name, ' .

        // Category
        db_prefix() . 'ItemCategoryMaster.CategoryName, ' .

        // Location
        db_prefix() . 'PlantLocationDetails.LocationName, ' .

        // Shipping city
        'shipping_citys.city_name as ShippingCityName, ' .

        // DeliveryFrom / DeliveryTo → map from DO columns
        // db_prefix() . 'DeliveryOrderMaster.DODate, '.

        // Summary totals - DO table uses different column names than view expects
        db_prefix() . 'SalesInvoiceMaster.ItemTotal as ItemAmt, ' .     // view line 124
        db_prefix() . 'SalesInvoiceMaster.TaxAmt as TaxableAmt, ' .    // view line 125
        db_prefix() . 'SalesInvoiceMaster.TotalWt as TotalWeight, ' .  // view line 159
        db_prefix() . 'SalesInvoiceMaster.TotalQty as TotalQuantity'   // view line 161
    );

    // Customer
    $this->db->join(
      db_prefix() . 'clients',
      db_prefix() . 'clients.AccountID = ' . db_prefix() . 'SalesInvoiceMaster.AccountID',
      'left'
    );

    // Billing state name
    $this->db->join(
      db_prefix() . 'xx_statelist',
      db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.billing_state',
      'left'
    );

    // Item Category
    $this->db->join(
      db_prefix() . 'ItemCategoryMaster',
      db_prefix() . 'ItemCategoryMaster.Id = ' . db_prefix() . 'SalesInvoiceMaster.CategoryID',
      'left'
    );

    // Dispatch / Plant location
    $this->db->join(
      db_prefix() . 'PlantLocationDetails',
      db_prefix() . 'PlantLocationDetails.id = ' . db_prefix() . 'SalesInvoiceMaster.SalesLocation',
      'left'
    );

    // Freight Terms
    $this->db->join(
      db_prefix() . 'FreightTerms',
      db_prefix() . 'FreightTerms.Id = ' . db_prefix() . 'SalesInvoiceMaster.FreightRate',
      'left'
    );

    // Customer shipping location → city name
    $this->db->join(
      db_prefix() . 'clientwiseshippingdata',
      db_prefix() . 'clientwiseshippingdata.id = ' . db_prefix() . 'SalesInvoiceMaster.CustLocationID',
      'left'
    );

    $this->db->join(
      db_prefix() . 'xx_citylist as shipping_citys',
      'shipping_citys.id = ' . db_prefix() . 'clientwiseshippingdata.ShippingCity',
      'left'
    );


    $this->db->join(
      db_prefix() . 'DeliveryOrderMaster',
      db_prefix() . 'DeliveryOrderMaster.OrderID = ' . db_prefix() . 'SalesInvoiceMaster.DeliveryOrderID',
      'left'
    );

    $this->db->where(db_prefix() . 'SalesInvoiceMaster.InvoiceID', $OrderID);

    return $this->db->get(db_prefix() . 'SalesInvoiceMaster')->row();
  }

  public function get_order_data($OrderID)
  {
    $this->db->select([
      db_prefix() . 'history.*',
      'tblitems.ItemName',
      'tblitems.hsn_code'
    ]);
    $this->db->join('tblitems', 'tblitems.ItemID = ' . db_prefix() . 'history.ItemID', 'left');
    // DO-linked history rows store the DO's OrderID in TransID
    $this->db->where(db_prefix() . 'history.BillID', $OrderID);
    return $this->db->get('tblhistory')->result_array();
  }
}
