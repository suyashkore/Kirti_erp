<?php
defined('BASEPATH') or exit('No direct script access allowed');

class StockTransfer_model extends App_Model
{
  protected $table = 'SalesOrderMaster';
  protected $primaryKey = 'id';
  public function __construct()
  {
    parent::__construct();
  }

  public function getNextSTNo()
  {

    // Get Financial Year & Root Company from session
    $FY      = $this->session->userdata('finacial_year');   // e.g. 25
    $PlantID = $this->session->userdata('root_company');    // e.g. 1

    // Count existing Delivery Order
    $this->db->from(db_prefix() . 'StockTransferMaster');
    $count = $this->db->count_all_results();

    $next_number = $count + 1;

    // Prefix format: SQ + FY + PlantID + ShortCode
    $prefix = 'ST' . $FY . $PlantID;

    // Final Quotation Number
    $order_no = $prefix . str_pad($next_number, 5, '0', STR_PAD_LEFT);

    return $order_no;
  } 

  public function GetDataForEWayBill($id) {
    $this->db->select('stm.TransferID,stm.Distance,pld1.LocationName as FromLocation,sl1.id as FromStateCode,pld1.PinCode as FromPincode, pld2.LocationName as ToLocation,sl2.id as ToStateCode, pld2.PinCode as ToPincode, rc1.gst as FromGSTIN,rc1.company_name as FromCompany, rc2.gst as ToGSTIN, rc2.company_name as ToCompany,sl3.id as ActFromStateCode,sl4.id as ActToStateCode, gm1.GodownName as FromGodown,gm2.GodownName as ToGodown,gm1.Address as FromAddress,gm2.Address as ToAddress,cl1.city_name as FromCity, cl2.city_name as ToCity');

    $this->db->from(db_prefix() . 'StockTransferMaster stm');


    


    // From Location
    $this->db->join(db_prefix(). 'PlantLocationDetails pld1', 'pld1.id = stm.FromLocationID', 'left');

    // To Location
    $this->db->join(db_prefix(). 'PlantLocationDetails pld2', 'pld2.id = stm.ToLocationID', 'left');

    // From Plant ID GSTIN
    $this->db->join(db_prefix(). 'rootcompany rc1', 'rc1.id = pld1.PlantID', 'left');

    // To Plant ID GSTIN
    $this->db->join(db_prefix(). 'rootcompany rc2', 'rc2.id = pld2.PlantID', 'left');

    // From Godown
    $this->db->join(db_prefix(). 'godownmaster gm1', 'gm1.id = stm.FromWHID', 'left');

    // To Godown
    $this->db->join(db_prefix(). 'godownmaster gm2', 'gm2.id = stm.ToWHID', 'left');

    // From City
    $this->db->join(db_prefix(). 'xx_citylist cl1', 'gm1.CityID = cl1.id', 'left');

    // To City
    $this->db->join(db_prefix(). 'xx_citylist cl2', 'gm2.CityID = cl2.id', 'left');

    // From State Code
    $this->db->join(db_prefix(). 'xx_statelist sl1', 'sl1.short_name = pld1.StateCode', 'left');

    // To State Code
    $this->db->join(db_prefix(). 'xx_statelist sl2', 'sl2.short_name = pld2.StateCode', 'left');

    // From Act State Code
    $this->db->join(db_prefix(). 'xx_statelist sl3', 'sl3.short_name = rc1.state_code', 'left');

    // To Act State Code
    $this->db->join(db_prefix(). 'xx_statelist sl4', 'sl4.short_name = rc2.state_code', 'left');

    $this->db->where('stm.id', $id);

    return $this->db->get()->row_array();
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

  // ===== GET PLANT LOCATION DROPDOWN DATA =====
  public function getPlantLocationDropdown()
  {
    $selected_company = $this->session->userdata('root_company');
    $this->db->select('*');
    $this->db->from(db_prefix() . 'PlantLocationDetails');
    $this->db->where('PlantID', $selected_company);
    $query = $this->db->get();
    return $query->result_array();
  }

  // ===== GET GODOWN DETAILS ON PLANT ID =====
  public function getGodownDetailsByPlantID($LocationID, $excludeLocationID = null)
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'godownmaster');
    $this->db->where('LocationID', $LocationID);  // <-- changed from PlantID to LocationID
    if (!empty($excludeLocationID)) {
      $this->db->where('LocationID !=', $excludeLocationID);
    }
    return $this->db->get()->result_array();
  }

  public function getStockTransferList()
{
    $this->db->select('stm.*, pld1.LocationName as FromLocation, pld2.LocationName as ToLocation');

    $this->db->from(db_prefix() . 'StockTransferMaster stm');

    $this->db->join(db_prefix() . 'PlantLocationDetails pld1', 'pld1.id = stm.FromLocationID', 'left');

    $this->db->join(db_prefix() . 'PlantLocationDetails pld2', 'pld2.id = stm.ToLocationID', 'left');

    return $this->db->get()->result_array();
}

  // ===== GET PINCODE ON GODOWN ID =====
  public function getPincode($GodownID)
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'godownmaster');
    $this->db->where('id', $GodownID);
    $query = $this->db->get();
    return $query->result_array();
  }

  // ===== GET CHAMBERS BY GODOWN ID =====
  public function getChambersByGodownID($GodownID)
  {
    $this->db->select('id, ChamberName, ChamberCode');
    $this->db->from(db_prefix() . 'ChamberMaster');
    $this->db->where('GodownID', $GodownID);
    $this->db->where('IsActive', 'Y');
    return $this->db->get()->result_array();
  }

  // ===== GET STACKS BY GODOWN ID AND CHAMBER ID =====
  public function getStacksByChamberID($GodownID, $ChamberID)
  {
    $this->db->select('id, StackName, StackCode');
    $this->db->from(db_prefix() . 'StackMaster');
    $this->db->where('GodownID', $GodownID);
    $this->db->where('ChamberID', $ChamberID);
    $this->db->where('IsActive', 'Y');
    return $this->db->get()->result_array();
  }

  // ===== GET LOTS BY GODOWN, CHAMBER AND STACK ID =====
  public function getLotsByStackID($GodownID, $ChamberID, $StackID)
  {
    $this->db->select('id, LotName, LotCode');
    $this->db->from(db_prefix() . 'LotMaster');
    $this->db->where('GodownID', $GodownID);
    $this->db->where('ChamberID', $ChamberID);
    $this->db->where('StackID', $StackID);
    $this->db->where('IsActive', 'Y');
    return $this->db->get()->result_array();
  }

  // ===== GET VEHICLE NO DROPDOWN =====
  public function getVehicleNoDropdown()
  {
    $this->db->select('distinct(v.VehicleNo) as VehicleNo');
    $this->db->from(db_prefix() . 'vehicle v');
    $this->db->where('v.IsActive', 'Y');
    $query = $this->db->get();
    return $query->result_array();
  }

  // ===== GET DRIVER NAME BY VEHICLE NO =====
  public function getVehicleDetailsByVehicleNo($vehicle_no)
  {
    $this->db->select('tblvehicle.*, tblGateMaster.GateINID');
    $this->db->from(db_prefix() . 'vehicle as tblvehicle');
    $this->db->join('tblGateMaster', 'tblvehicle.VehicleNo = tblGateMaster.VehicleNo', 'left');
    $this->db->where('tblvehicle.VehicleNo', $vehicle_no);  // FIXED

    $result = $this->db->get()->result_array();

    return $result;
  }
  // ===== GET ITEMS DROPDOWN =====
  public function getItemsDropdown()
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'items');
    $query = $this->db->get();
    return $query->result_array();
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
    if (empty($data['item_id']) || !is_array($data['item_id'])) {
        return false;
    }

    $count = count($data['item_id']);

    for ($i = 0; $i < $count; $i++) {

        // Skip row if item_id is empty
        if (empty($data['item_id'][$i])) {
            continue;
        }

        $base_data = [
            'OrderID'    => $data['OrderID'],
            'PlantID'    => $data['plant_id'],
            'FY'         => $data['fy'],
            'TransDate'  => $data['order_date'] ?? date('Y-m-d'),
            'ItemID'     => $data['item_id'][$i],
            'SuppliedIn' => $data['uom'][$i]         ?? '',
            'UnitWeight' => $data['unit_weight'][$i] ?? 0,
            'WeightUnit' => $data['uom'][$i]         ?? '',
            'OrderQty'   => $data['quantity'][$i]    ?? 0,
            'Ordinalno'  => $i + 1,
            'TType'      => 'T',
            'UserID'     => $this->session->userdata('username'),
            'TransDate2' => date('Y-m-d H:i:s'),
        ];

        $out_data = array_merge($base_data, [
            'TType2'   => 'Out',
            'GodownID' => $data['FromGodown'] ?? null,
        ]);

        $in_data = array_merge($base_data, [
            'TType2'   => 'In',
            'GodownID' => $data['ToGodown'] ?? null,
        ]);

        // if (!empty($data['item_uid'][$i]) && $data['item_uid'][$i] != 0) {
        //     $out_data['UserID2'] = $this->session->userdata('username');
        //     $out_data['Lupdate'] = date('Y-m-d H:i:s');
        //     $this->db->where('id', $data['item_uid'][$i]);
        //     $this->db->where('TType2', 'Out');
        //     $this->db->update(db_prefix() . 'history', $out_data);

        //     $in_data['UserID2'] = $this->session->userdata('username');
        //     $in_data['Lupdate'] = date('Y-m-d H:i:s');
        //     $this->db->where('id', $data['item_uid'][$i]);
        //     $this->db->where('TType2', 'In');
        //     $this->db->update(db_prefix() . 'history', $in_data);
        // } else {
        //     $this->db->insert(db_prefix() . 'history', $out_data);
        //     $this->db->insert(db_prefix() . 'history', $in_data);
        // }

        $item_uid = !empty($data['item_uid'][$i]) ? (int)$data['item_uid'][$i] : 0;

        if ($item_uid > 0) {
            // UPDATE — find Out and In records by OrderID + ItemID + Ordinalno + TType2
            // Do NOT rely on item_uid alone since Out and In are separate rows with different IDs
            $out_data['UserID2'] = $this->session->userdata('username');
            $out_data['Lupdate'] = date('Y-m-d H:i:s');
            $this->db->where('OrderID', $data['OrderID']);
            $this->db->where('ItemID',  $data['item_id'][$i]);
            $this->db->where('Ordinalno', $i + 1);
            $this->db->where('TType',  'T');
            $this->db->where('TType2', 'Out');
            $this->db->update(db_prefix() . 'history', $out_data);

            $in_data['UserID2'] = $this->session->userdata('username');
            $in_data['Lupdate'] = date('Y-m-d H:i:s');
            $this->db->where('OrderID', $data['OrderID']);
            $this->db->where('ItemID',  $data['item_id'][$i]);
            $this->db->where('Ordinalno', $i + 1);
            $this->db->where('TType',  'T');
            $this->db->where('TType2', 'In');
            $this->db->update(db_prefix() . 'history', $in_data);

        } else {
            // INSERT — fresh rows
            $this->db->insert(db_prefix() . 'history', $out_data);
            $this->db->insert(db_prefix() . 'history', $in_data);
        }
    }
}

  public function getOrderDetails($id)
  {
    $this->db->select('stm.*, pld1.LocationName as FromLocation, pld2.LocationName as ToLocation');
    $this->db->from(db_prefix() . 'StockTransferMaster stm');
    $this->db->join(db_prefix() . 'PlantLocationDetails pld1', 'pld1.id = stm.FromLocationID', 'left');
    $this->db->join(db_prefix() . 'PlantLocationDetails pld2', 'pld2.id = stm.ToLocationID', 'left');
    $this->db->where('stm.id', $id);
    $this->db->order_by('stm.id', 'ASC');
    $master = $this->db->get()->row_array();

    if (!$master) {
      return [];
    }

    $this->db->from(db_prefix() . 'history');
    $this->db->where('OrderID', $master['TransferID']);
    $history = $this->db->get()->result_array();
    $master['history'] = $history;

    return $master;
  }


  public function getOrderList()
  {
    $this->db->select('pm.*, c.company, icm.CategoryName');
    $this->db->from(db_prefix() . 'SalesOrderMaster pm');
    $this->db->join(db_prefix() . 'clients c', 'c.AccountID = pm.AccountID', 'left');
    $this->db->join(db_prefix() . 'ItemCategoryMaster icm', 'icm.id = pm.ItemCategory', 'left');
    $this->db->order_by('pm.TransDate', 'ASC');
    return $this->db->get()->result_array();
  }

  public function getStockTransferDetails($id)
  {
    $this->db->select('*');
    $this->db->from(db_prefix() . 'StockTransferMaster stm');    
    $this->db->where('stm.id', $id);
    $master = $this->db->get()->row_array();

    if (!$master) {
      return [];
    }

    $this->db->from(db_prefix() . 'history');
    $this->db->where('OrderID', $master['TransferID']);
    $this->db->group_by('ItemID');

    $history = $this->db->get()->result_array();
    $master['history'] = $history;

    return $master;
  }
  public function getCustomerBrokerList($customer_id)
  {
    $this->db->select('c.AccountID, c.company, c.billing_state');
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

  public function getItemDetailsById($item_id)
{
    $this->db->select(
        db_prefix() . "items.ItemID,
        " . db_prefix() . "items.ItemName,
        " . db_prefix() . "items.hsn_code,
        tblUnitMaster.ShortCode as unit,
        " . db_prefix() . "items.UnitWeight,
        tbltaxes.taxrate as tax"
    );
    $this->db->from(db_prefix() . 'items');
    $this->db->join('tbltaxes', 'tbltaxes.id = ' . db_prefix() . "items.tax", 'left');
    $this->db->join('tblUnitMaster', 'tblUnitMaster.id = ' . db_prefix() . "items.unit", 'left');
    $this->db->where(db_prefix() . 'items.ItemID', $item_id);
    $result = $this->db->get()->result_array();

    if (empty($result)) {
      return array();
    }

    // InwardSum: TType='P' AND TType2='Inward'
    $this->db->select_sum('OrderQty', 'InwardSum');
    $this->db->from('tblhistory');
    $this->db->where('ItemID', $item_id);
    $this->db->where('TType', 'P');
    $this->db->where('TType2', 'Inward');
    $inwardResult = $this->db->get()->row_array();
    $InwardSum = (float)($inwardResult['InwardSum'] ?? 0);  // null → 0

    // InSum: TType='T' AND TType2='In'
    $this->db->select_sum('OrderQty', 'InSum');
    $this->db->from('tblhistory');
    $this->db->where('ItemID', $item_id);
    $this->db->where('TType', 'T');
    $this->db->where('TType2', 'In');
    $inResult = $this->db->get()->row_array();
    $InSum = (float)($inResult['InSum'] ?? 0);  // null → 0

    // DeliverySum: TType='S' AND TType2='Delivery'
    $this->db->select_sum('OrderQty', 'DeliverySum');
    $this->db->from('tblhistory');
    $this->db->where('ItemID', $item_id);
    $this->db->where('TType', 'S');
    $this->db->where('TType2', 'Delivery');
    $deliveryResult = $this->db->get()->row_array();
    $DeliverySum = (float)($deliveryResult['DeliverySum'] ?? 0);  // null → 0

    // OutSum: TType='T' AND TType2='Out'
    $this->db->select_sum('OrderQty', 'OutSum');
    $this->db->from('tblhistory');
    $this->db->where('ItemID', $item_id);
    $this->db->where('TType', 'T');
    $this->db->where('TType2', 'Out');
    $outResult = $this->db->get()->row_array();
    $OutSum = (float)($outResult['OutSum'] ?? 0);  // null → 0

    // CurrentStockQty = (InwardSum) - (DeliverySum + OutSum)
    $CurrentStockQty = ($InwardSum) - ($DeliverySum + $OutSum);

    return array(
        'hsn_code'        => $result[0]['hsn_code'] ?? '',
        'unit'            => $result[0]['unit'] ?? '',
        'UnitWeight'      => $result[0]['UnitWeight'] ?? '0',
        'tax'             => $result[0]['tax'] ?? '0',
        '1.InwardSum'       => $InwardSum,
        '1.InSum'           => $InSum,
        '2.DeliverySum'     => $DeliverySum,
        '2.OutSum'          => $OutSum,
        'CurrentStockQty' => $CurrentStockQty,
    );
}

public function getGodownById($godownId)
{
    if (!$godownId) return [];
    $this->db->select('*');
    $this->db->from(db_prefix() . 'godownmaster');
    $this->db->where('id', $godownId);
    return $this->db->get()->row_array();
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
