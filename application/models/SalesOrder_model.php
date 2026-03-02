<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SalesOrder_model extends App_Model
{
  protected $table = 'SalesOrderMaster';
  protected $primaryKey = 'id';
  public function __construct()
  {
    parent::__construct();
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
    $data['Lupdate'] = date('Y-m-d H:i:s');
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

  public function getCategoryDropdown()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'ItemCategoryMaster');

    $query = $this->db->get();
    return $query->result_array();
  }

  public function getNextSalesOrderNoByCategory($category_id)
  {
    if (!$category_id) return '';

    // Get Financial Year & Root Company from session
    $FY      = $this->session->userdata('finacial_year');   // e.g. 25
    $PlantID = $this->session->userdata('root_company');    // e.g. 1

    // Get Category ShortCode (like LO)
    $this->db->select('prefix as ShortCode');
    $this->db->from('tblItemCategoryMaster');
    $this->db->where('id', $category_id);
    $category = $this->db->get()->row();

    $short_code = $category ? $category->ShortCode : '';

    // Count existing quotations for this category
    $this->db->from(db_prefix() . 'SalesOrderMaster');
    $this->db->where('ItemCategory', $category_id); // adjust column name if different
    $count = $this->db->count_all_results();

    $next_number = $count + 1;

    // Prefix format: SQ + FY + PlantID + ShortCode
    $prefix = 'SO' . $FY . $PlantID . $short_code;

    // Final Quotation Number
    $order_no = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);

    return $order_no;
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

      $item_data = [
        'OrderID' => $data['OrderID'],
        'PlantID' => $data['plant_id'],
        'FY' => $data['fy'],
        'TransDate' => $data['quotation_date'] ?? date('Y-m-d'),
        'AccountID' => $data['customer_id'] ?? '',
        'ItemID' => $data['item_id'][$i],
        'BasicRate' => $data['unit_rate'][$i],
        'SaleRate' => ($data['unit_rate'][$i] * $gst_percent) / 100,
        'SuppliedIn' => $data['uom'][$i],
        'UnitWeight' => $data['unit_weight'][$i],
        'WeightUnit' => $data['uom'][$i],
        'OrderQty' => $data['quantity'][$i],
        'DiscAmt' => $data['disc_amt'][$i],
        'cgst' => $cgst,
        'cgstamt' => $cgstamt,
        'sgst' => $sgst,
        'sgstamt' => $sgstamt,
        'igst' => $igst,
        'igstamt' => $igstamt,
        'OrderAmt' => $data['quantity'][$i] * $data['unit_rate'][$i],
        'NetOrderAmt' => $data['amount'][$i],
        'Ordinalno' =>  $i+1,
      ];
      //   echo json_encode($item_data);
      // exit;
      if ($data['item_uid'][$i] == 0) {
        $item_data['UserID'] = $this->session->userdata('username');
        $item_data['TransDate2'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix() . 'history', $item_data);
      } else {
        $item_data['UserID2'] = $this->session->userdata('username');
        $item_data['Lupdate'] = date('Y-m-d H:i:s');
        $this->db->where('id', $data['item_uid'][$i]);
        $this->db->update(db_prefix() . 'history', $item_data);
      }
    }
  }

  public function getOrderList()
  {
    $this->db->select('pm.*, c.company, icm.CategoryName');
    $this->db->from(db_prefix() . 'SalesOrderMaster pm');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = pm.AccountID', 'left');
    $this->db->join(db_prefix() . 'ItemCategoryMaster icm', 'icm.id = pm.ItemCategory', 'left');
    $this->db->where('pm.TransDate >=', date('Y-m-01'));
    $this->db->order_by('pm.TransDate', 'DESC');
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
    $this->db->where('TransID IS NULL', null, false);

    $history = $this->db->get()->result_array();
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


  public function getCustomerQuotationList($customer_id)
  {
    $this->db->select('QuotationID');
    $this->db->from(db_prefix() . 'SalesQuotationMaster');
    $this->db->where('AccountID', $customer_id);
    $this->db->where('Status !=', 6);
    return $this->db->get()->result_array();
  }

  //   public function getQuotationFullDetails($quotation_id)
  // {

  //     $master = $this->db
  //         ->where('QuotationID', $quotation_id)
  //         ->get(db_prefix().'SalesQuotationMaster')
  //         ->result_array();

  //     return [
  //         'master' => $master
  //     ];
  // }


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
        'hsn_code' => $result[0]['hsn_code'] ?? '',
        'unit' => $result[0]['unit'] ?? '',
        'UnitWeight' => $result[0]['UnitWeight'] ?? '0',
        'tax' => $result[0]['tax'] ?? '0',
      );
    }

    return array();
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

  public function get_sales_location()
  {
    $selected_company = $this->session->userdata('root_company');

    $this->db->select([db_prefix() . 'PlantLocationDetails.*']);
    $this->db->where(db_prefix() . 'PlantLocationDetails.PlantID', $selected_company);
    return $this->db->get('tblPlantLocationDetails')->result_array();
  }

  public function getCustomerDetailByAccountID($AccountID)
  {
    $this->db->select(
      'AccountID,
			company,
			GSTIN as gst_number,
			PAN as pan,
			billing_city as city,
			billing_zip as postal_code,
			billing_address as address,
			tblxx_statelist.state_name as state,
			tblcountries.long_name as country'
    );

    $this->db->from(db_prefix() . 'clients');

    $this->db->join('tblxx_statelist', 'clients.billing_state = tblxx_statelist.short_name', 'left');
    $this->db->join('tblcountries', 'clients.billing_country = tblcountries.country_id', 'left');

    $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

    $result = $this->db->get()->result_array();

    if (!empty($result)) {
      return array(
        'gst_no' => $result[0]['gst_number'] ?? '',
        'pan' => $result[0]['pan'] ?? '',
        'country' => $result[0]['country'] ?? '',
        'state' => $result[0]['state'] ?? '',
        'city' => $result[0]['city'] ?? '',
        'postal_code' => $result[0]['postal_code'] ?? '',
        'address' => $result[0]['address'] ?? '',
        'company' => $result[0]['company'] ?? '',

      );
    }

    return array();
  }

  public function getShippingDatacity($AccountID)
  {
    $this->db->select(
      'tblclientwiseshippingdata.id,
			tblxx_citylist.city_name'
    );

    $this->db->from('tblclientwiseshippingdata');
    $this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclientwiseshippingdata.ShippingCity', 'LEFT');
    $this->db->where('tblclientwiseshippingdata.AccountID', $AccountID);

    return $this->db->get()->result_array();
  }

  /* =========================
	* Sales Order Print Model
	* ========================= */

  public function GetSalesOrderDetailsForPdf($OrderID)
  {
    $selected_company = $this->session->userdata('root_company');

    $this->db->select(
      db_prefix() . 'SalesOrderMaster.*, ' .
        db_prefix() . 'clients.company, ' .
        db_prefix() . 'clients.billing_address, ' .
        db_prefix() . 'clients.billing_city, ' .
        db_prefix() . 'clients.billing_state, ' .
        db_prefix() . 'clients.GSTIN, ' .
        db_prefix() . 'xx_statelist.state_name, ' .
        db_prefix() . 'ItemTypeMaster.ItemTypeName, ' .
        db_prefix() . 'ItemCategoryMaster.CategoryName, ' .
        db_prefix() . 'clientwiseshippingdata.ShippingCity, ' .
        'delivery_city.city_name, ' .
        db_prefix() . 'FreightTerms.FreightTerms, ' .
        db_prefix() . 'PlantLocationDetails.LocationName , ' .
        db_prefix() . 'SalesQuotationMaster.TransDate as QuotationDate, ' .
        'shipping_citys.city_name as ShippingCityName'
    );

    $this->db->join(
      db_prefix() . 'clients',
      db_prefix() . 'clients.AccountID = ' . db_prefix() . 'SalesOrderMaster.AccountID',
      'left'
    );

    $this->db->join(
      db_prefix() . 'xx_statelist',
      db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.billing_state',
      'left'
    );

    $this->db->join(
      db_prefix() . 'ItemTypeMaster',
      db_prefix() . 'ItemTypeMaster.Id = ' . db_prefix() . 'SalesOrderMaster.ItemType',
      'left'
    );

    $this->db->join(
      db_prefix() . 'ItemCategoryMaster',
      db_prefix() . 'ItemCategoryMaster.Id = ' . db_prefix() . 'SalesOrderMaster.ItemCategory',
      'left'
    );

    $this->db->join(
      db_prefix() . 'clientwiseshippingdata',
      db_prefix() . 'clientwiseshippingdata.id = ' . db_prefix() . 'SalesOrderMaster.DeliveryLocation',
      'left'
    );

    // Aliased to avoid conflict with the second xx_citylist join
    $this->db->join(
      db_prefix() . 'PlantLocationDetails',
      db_prefix() . 'PlantLocationDetails.id = ' . db_prefix() . 'SalesOrderMaster.SalesLocation',
      'left'
    )->join(db_prefix() . 'xx_citylist as delivery_city', 'delivery_city.id = delivery_city.Id', 'left');

    $this->db->join(
      db_prefix() . 'FreightTerms',
      db_prefix() . 'SalesOrderMaster.FreightTerms = ' . db_prefix() . 'FreightTerms.Id',
      'left'
    );

    // $this->db->join(db_prefix() . 'SalesQuotationMaster',
    //     db_prefix() . 'SalesQuotationMaster.QuotationID = ' . db_prefix() . 'SalesOrderMaster.QuotationID', 'left');

    $this->db->join(
      db_prefix() . 'SalesQuotationMaster',
      db_prefix() . 'SalesQuotationMaster.QuotationID COLLATE utf8mb4_general_ci = '
        . db_prefix() . 'SalesOrderMaster.QuotationID COLLATE utf8mb4_general_ci',
      'left',
      false
    );


    // Aliased for ShippingCity
    $this->db->join(
      db_prefix() . 'xx_citylist as shipping_citys',
      'shipping_citys.id = ' . db_prefix() . 'clientwiseshippingdata.ShippingCity',
      'left'
    );

    $this->db->where(db_prefix() . 'SalesOrderMaster.OrderID', $OrderID);

    return $this->db->get(db_prefix() . 'SalesOrderMaster')->row();
  }

  public function get_order_data($OrderID)
  {
    $selected_company = $this->session->userdata('root_company');

    $this->db->select([db_prefix() . 'history.*', 'tblitems.ItemName']);
    $this->db->join('tblitems', 'tblitems.ItemID = ' . db_prefix() . 'history.ItemID', 'left');
    $this->db->where(db_prefix() . 'history.OrderID', $OrderID);
    return $this->db->get('tblhistory')->result_array();
  }
}
