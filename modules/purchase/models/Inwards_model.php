<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inwards_model extends App_Model
{
  protected $table = 'PurchInwardsMaster';
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
  public function checkDuplicate($table, $where=null)
  {
    $this->db->where($where);
    return $this->db->count_all_results(db_prefix().$table) > 0;
  }

  // ===== SAVE DATA =====
  public function saveData($table, $data)
  {
    $data['UserID'] = $this->session->userdata('username');

    $this->db->insert(db_prefix().$table, $data);
    return $this->db->insert_id();
  }

  // ===== UPDATE DATA =====
  public function updateData($table, $data, $where = null)
  {
    $data['Lupdate'] = date('Y-m-d H:i:s');
    $data['UserID2'] = $this->session->userdata('username');

    $this->db->where($where);
    return $this->db->update(db_prefix().$table, $data);
  }

  // ===== GET ROW DATA =====
  public function getRowData($table, $select='*', $where = null)
  {
    $this->db->select($select);
    $this->db->from(db_prefix().$table);
    if($where != null) $this->db->where($where);
    return $this->db->get()->row();
  }

  public function getVendorDropdownByPO() {
    $this->db->distinct();
    $this->db->select('c.userid, c.AccountID, c.company, c.billing_state');
    $this->db->from(db_prefix().'clients c');
    $this->db->join( db_prefix().'AccountSubGroup2 a', 'a.SubActGroupID = c.ActSubGroupID2', 'inner' );
    $this->db->join( db_prefix().'PurchaseOrderMaster pom', 'pom.AccountID = c.AccountID AND pom.Status != 6', 'inner' );
    $this->db->where('a.IsVendor', 'Y');

    return $this->db->get()->result_array();
  }

  public function getVendorDropdownByASN() {
    $this->db->distinct();
    $this->db->select('c.userid, c.AccountID, c.company');
    $this->db->from(db_prefix().'clients c');
    $this->db->join( db_prefix().'AccountSubGroup2 a', 'a.SubActGroupID = c.ActSubGroupID2', 'inner' );
    $this->db->join( db_prefix().'PurchaseOrderMaster pom', 'pom.AccountID = c.AccountID', 'inner' );
    $this->db->where('a.IsVendor', 'Y');

    return $this->db->get()->result_array();
  }

  public function getVendorDropdownByInwards() {
    $this->db->distinct();
    $this->db->select('c.userid, c.AccountID, c.company');
    $this->db->from(db_prefix().'clients c');
    $this->db->join(db_prefix().'AccountSubGroup2 a', 'a.SubActGroupID = c.ActSubGroupID2', 'inner' );
    $this->db->join(db_prefix().'PurchInwardsMaster pim', 'pim.AccountID = c.AccountID', 'inner' );
    $this->db->where('a.IsVendor', 'Y');

    return $this->db->get()->result_array();
  }

  public function getNextInwardsNoByCategory($category_id) {
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
    if (!$category_id) return '';
    $this->db->where('PlantID', $PlantID);
    $this->db->where('FY', $FY);
    $this->db->where('ItemCategory', $category_id);
    $count = $this->db->count_all_results(db_prefix().'PurchInwardsMaster');
    $next_no = $count + 1;
    $last_no = str_pad($next_no, 5, '0', STR_PAD_LEFT);

    $this->db->select('Prefix');
    $this->db->from(db_prefix().'ItemCategoryMaster');
    $this->db->where('id', $category_id);
    $query = $this->db->get()->row_array();
    $prefix = $query['Prefix'] ?? '-';

    return 'INV'.$FY.$PlantID.$prefix.$last_no;
  }

  public function saveMultiData($data){
    $selected_company = $this->session->userdata('root_company');
    $sql ='SELECT '.db_prefix().'rootcompany.* FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
    $company = $this->db->query($sql)->row();

    $vendor_state = isset($data['vendor_state']) ? strtoupper(trim($data['vendor_state'])) : '';
    if(count($data['item_id']) <= 0){
      return false;
    }

    for($i=0; $i<count($data['item_id']); $i++){
      $itemData = $this->db->select('*')->from(db_prefix().'items')->where(['ItemID' => $data['item_id'][$i]])->get()->row();

      $gst_percent = floatval($data['gst'][$i]) ?? 0;
      $taxable_amt = ((floatval($data['unit_rate'][$i]) ?? 0) - (floatval($data['disc_amt'][$i]) ?? 0) ) * (floatval($data['quantity'][$i]) ?? 0);
      
      $cgst = $sgst = $cgstamt = $sgstamt = $igst = $igstamt = 0;
      if ($vendor_state === $company->state) {
        $cgst = $sgst = $gst_percent / 2;
        $cgstamt = $sgstamt = ($taxable_amt * $cgst) / 100;
      } else {
        $igst = $gst_percent;
        $igstamt = ($taxable_amt * $igst) / 100;
      }
      
      $item_data = [
        'OrderID'     => $data['inwards_no'],
        'PlantID'     => $data['plant_id'],
        'FY'          => $data['fy'],
        'TransDate'   => $data['inwards_date'] ?? date('Y-m-d'),
				'TType'       => $data['TType'],
				'TType2'      => $data['TType2'],
				'GodownID'    => $data['GodownID'],
        'AccountID'   => $data['vendor_id'] ?? '',
        'ItemID'      => $data['item_id'][$i],
        'BasicRate'   => $data['unit_rate'][$i],
        'SaleRate'    => ($data['unit_rate'][$i] * ($gst_percent / 100)),
        'SuppliedIn'  => $data['uom'][$i],
        'UnitWeight'  => $data['unit_weight'][$i],
        'WeightUnit'  => $data['uom'][$i],
        'CaseQty'     => (int)($itemData->MinOrderQty ?? 0),
        'OrderQty'    => $data['quantity'][$i],
        'Cases'       => ((int)$data['quantity'][$i] - (int)($itemData->MinOrderQty ?? 0)),
        'DiscAmt'     => $data['disc_amt'][$i],
        'DiscPerc'    => (($data['disc_amt'][$i] / $data['unit_rate'][$i]) * 100),
        'cgst'        => $cgst,
        'cgstamt'     => $cgstamt,
        'sgst'        => $sgst,
        'sgstamt'     => $sgstamt,
        'igst'        => $igst,
        'igstamt'     => $igstamt,
        'OrderAmt'    => $data['quantity'][$i] * $data['unit_rate'][$i],
        'NetOrderAmt' => $data['amount'][$i],
        'Ordinalno'   =>  $i+1,
      ];

      if($data['item_uid'][$i] == 0){
        $item_data['UserID'] = $this->session->userdata('username');
        $item_data['TransDate2'] = date('Y-m-d H:i:s');
        $this->db->insert(db_prefix().'history', $item_data);
      } else {
        $item_data['UserID2'] = $this->session->userdata('username');
        $item_data['Lupdate'] = date('Y-m-d H:i:s');
        $this->db->where('id', $data['item_uid'][$i]);
        $this->db->update(db_prefix().'history', $item_data);
      }
    }
  }

  public function getInwardsList(){
    $this->db->select('pm.id, pm.InwardsID, pm.OrderID, pm.TransDate, pm.AccountID, pm.TotalWeight, pm.NetAmt, c.company, gm.id as gatein_id, gm.GateINID as gatein_no, gm.VehicleNo');
    $this->db->from(db_prefix().'PurchInwardsMaster pm');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = pm.AccountID', 'left');
    $this->db->join(db_prefix().'GateMaster gm', 'gm.InwardID = pm.InwardsID', 'left');
    $this->db->where('pm.TransDate >=', date('Y-m-01'));
    $this->db->order_by('pm.TransDate', 'DESC');
    return $this->db->get()->result_array();
  }

  public function getInwardsDetails($id)
  {
    $this->db->select('pm.*, c.company, gm.id as gatein_id, gm.GateINID as gatein_no, gm.VehicleNo');
    $this->db->from(db_prefix().'PurchInwardsMaster pm');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = pm.AccountID', 'left');
    $this->db->join(db_prefix().'GateMaster gm', 'gm.InwardID = pm.InwardsID', 'left');
    if(is_numeric($id)){
      $this->db->where('pm.id', $id);
    }else{
      $this->db->where('pm.InwardsID', $id);
    }
    $master = $this->db->get()->row_array();

    if (!$master) {
      return [];
    }

    $this->db->select('h.*, i.ItemName as item_name');
    $this->db->from(db_prefix().'history h');
    $this->db->join(db_prefix().'items i', 'i.ItemID = h.ItemID', 'left');
    $this->db->where('h.OrderID', $master['InwardsID']);
    $history = $this->db->get()->result_array();
    $master['history'] = $history;

    return $master;
  }

  public function getInwardsDetailsPrint($id){
    $this->db->select(' pm.*,  pom.TransDate as PODate,  c.company, c.billing_address, c.billing_city, c.billing_state, c.GSTIN, gm.id as gatein_id, gm.GateINID as gatein_no, gm.VehicleNo, pld.LocationName,  sl.state_name,  itm.ItemTypeName,  icm.CategoryName,  cwsd.ShippingCity,  cl.city_name,  ft.FreightTerms, gdm.GodownName ', FALSE);

    $this->db->from(db_prefix().'PurchInwardsMaster pm');
    // String joins (disable escaping)
    $this->db->join( db_prefix().'PurchaseOrderMaster pom', 'pom.PurchID = pm.OrderID COLLATE utf8mb4_general_ci', 'left', FALSE );
    $this->db->join( db_prefix().'clients c', 'c.AccountID = pm.AccountID COLLATE utf8mb4_general_ci', 'left', FALSE );
    $this->db->join(db_prefix().'GateMaster gm', 'gm.InwardID = pm.InwardsID', 'left');
    $this->db->join( db_prefix().'xx_statelist sl', 'CONVERT(sl.short_name USING utf8mb4) COLLATE utf8mb4_general_ci = c.billing_state', 'left', FALSE );
    // Normal joins (no need for collate)
    $this->db->join(db_prefix().'PlantLocationDetails pld', 'pld.id = pm.PurchaseLocation', 'left');
    $this->db->join(db_prefix().'ItemTypeMaster itm', 'itm.Id = pm.ItemType', 'left');
    $this->db->join(db_prefix().'ItemCategoryMaster icm', 'icm.Id = pm.ItemCategory', 'left');
    $this->db->join(db_prefix().'clientwiseshippingdata cwsd', 'cwsd.id = pm.VendorLocation', 'left');
    $this->db->join(db_prefix().'xx_citylist cl', 'cl.id = cwsd.ShippingCity', 'left');
    $this->db->join(db_prefix().'FreightTerms ft', 'ft.Id = pm.FreightTerms', 'left');
    $this->db->join(db_prefix().'godownmaster gdm', 'gdm.Id = pm.GodownID', 'left');

    if (is_numeric($id)) {
      $this->db->where('pm.id', $id);
    } else {
      $this->db->where('pm.InwardsID', $id);
    }

    $master = $this->db->get()->row();

    if (!$master) {
      return null;
    }

    $this->db->select('h.*, i.ItemName as item_name');
    $this->db->from(db_prefix().'history h');
    $this->db->join(db_prefix().'items i', 'i.ItemID = h.ItemID', 'left');
    $this->db->where('h.OrderID', $master->InwardsID);
    $history = $this->db->get()->result();

    $master->history = $history;

    return $master;
  }

  public function getVendorBrokerList($vendor_id){
    $this->db->select('c.AccountID, c.company');
    $this->db->from(db_prefix().'PartyBrokerMaster pbm');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = pbm.BrokerID', 'left');
    $this->db->where('pbm.AccountID', $vendor_id);
    return $this->db->get()->result_array();
  }

  public function getVendorPurchaseOrder($AccountID){
    $this->db->select('id, PurchID, QuatationID');
    $this->db->from(db_prefix().'PurchaseOrderMaster');
    $this->db->where('AccountID', $AccountID);
    $this->db->where('Status !=', 6);
    return $this->db->get()->result_array();
	}

  public function getVendorPurchaseOrderASN($AccountID){
    $this->db->select('id, PurchID, QuatationID');
    $this->db->from(db_prefix().'PurchaseOrderMaster');
    $this->db->where('AccountID', $AccountID);
    return $this->db->get()->result_array();
	}

  public function getPurchaseOrderDetails($order_id){
    $this->db->select('pom.*, c.company');
    $this->db->from(db_prefix().'PurchaseOrderMaster pom');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = pom.AccountID', 'left');
    if(is_numeric($order_id)){
      $this->db->where('pom.id', $order_id);
    } else {
      $this->db->where('pom.PurchID', $order_id);
    }
    $master = $this->db->get()->row_array();

    if (!$master) {
      return [];
    }

    $this->db->from(db_prefix().'history');
    $this->db->where('OrderID', $master['PurchID']);
    $history = $this->db->get()->result_array();
    $master['history'] = $history;

    return $master;
  }

  public function get_company_detail(){
    $selected_company = $this->session->userdata('root_company');
    $sql ='SELECT '.db_prefix().'rootcompany.* FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
    $result = $this->db->query($sql)->row();
    return $result;
  }

  public function getListByFilter($data, $limit, $offset)
  {
    $from_date  = $data['from_date'] ?? date('Y-m-01');
    $to_date    = $data['to_date'] ?? date('Y-m-d');
    $vendor_id  = $data['vendor_id'] ?? '';
    $broker_id  = $data['broker_id'] ?? '';
    $status     = $data['status'] ?? 1;

    $this->db->from(db_prefix().$this->table);

    $this->db->join(db_prefix().'clients vendor', 'vendor.AccountID = '.db_prefix().$this->table.'.AccountID', 'left');
    $this->db->join(db_prefix().'clients broker', 'broker.AccountID = '.db_prefix().$this->table.'.BrokerID', 'left');
    $this->db->join(db_prefix().'GateMaster gm', 'gm.InwardID = '.db_prefix().$this->table.'.InwardsID', 'left');
    
    if($vendor_id != '')       $this->db->where(db_prefix().$this->table.'.AccountID', $vendor_id);
    if($broker_id != '')       $this->db->where(db_prefix().$this->table.'.BrokerID', $broker_id);
    if($status != '')          $this->db->where(db_prefix().$this->table.'.Status', $status);
    if($from_date != '')       $this->db->where(db_prefix().$this->table.'.TransDate >=', $from_date);
    if($to_date != '')         $this->db->where(db_prefix().$this->table.'.TransDate <=', $to_date);

    $total = $this->db->count_all_results('', FALSE);

    $this->db->select([
      db_prefix().$this->table.'.*',
      'vendor.company as vendor_name',
      'broker.company as broker_name',
      'gm.id as gatein_id',
      'gm.GateINID as gatein_no',
      'gm.VehicleNo'
    ]);

    // $this->db->order_by($this->primaryKey, 'desc');
    $this->db->limit($limit, $offset);

    $rows = $this->db->get()->result_array();

    return [
      'total' => $total,
      'rows'  => $rows
    ];
  }

  public function getData($table, $select='*', $where=null){
    $this->db->select($select);
    $this->db->from(db_prefix().$table);
    if($where != null) $this->db->where($where);
    return $this->db->get()->row_array();
  }

  public function GetGateInNo($location_id){
    $this->db->where('DATE(TransDate)', date('Y-m-d'));
    $this->db->where('LocationID', $location_id);
    $count = $this->db->count_all_results(db_prefix().'GateMaster');
    $next_no = $count + 1;
    $last_no = str_pad($next_no, 3, '0', STR_PAD_LEFT);
    $location_no = str_pad($location_id, 3, '0', STR_PAD_LEFT);

    return 'G'.$location_no.date('ydm').$last_no;
  }

  public function getGateinDetails($id){
    $this->db->select('g.*, pld.LocationName');
    $this->db->from(db_prefix().'GateMaster g');
    $this->db->join( db_prefix().'PlantLocationDetails pld', 'pld.id = g.LocationID', 'left' );
    if(is_numeric($id)){
      $this->db->where('g.id', $id);
    }else{
      $this->db->where('g.GateINID', $id);
    }

    return $this->db->get()->row();
  }

  public function getGateInListByFilter($data, $limit, $offset, $list=null){
    $from_date  = $data['from_date'] ?? date('Y-m-01');
    $to_date    = $data['to_date'] ?? date('Y-m-d');

    $this->db->from(db_prefix().'GateMaster g');
    $this->db->join(db_prefix().'PlantLocationDetails pld', 'pld.id = g.LocationID', 'left');
    if($list != null){
      $this->db->where('g.InwardID', null);
    }else{
      $this->db->where('g.InwardID !=', null);
    }
    
    if($from_date != '')       $this->db->where('g.TransDate >=', $from_date);
    if($to_date != '')         $this->db->where('g.TransDate <=', $to_date);

    $total = $this->db->count_all_results('', FALSE);

    $this->db->select([
      'g.*',
      'pld.LocationName'
    ]);

    // $this->db->order_by($this->primaryKey, 'desc');
    $this->db->limit($limit, $offset);

    $rows = $this->db->get()->result_array();

    return [
      'total' => $total,
      'rows'  => $rows
    ];
  }

  public function getINDetails($id){
    $this->db->select('*');
    $this->db->from(db_prefix().'GateMasterDetails');
    if(is_numeric($id)){
      $this->db->where('id', $id);
    }else{
      $this->db->where($id);
    }
    $data =$this->db->get()->row();
    if(empty($data)) return [];
    $data->value = !empty($data->value) ? json_decode($data->value) : [];

    return $data;
  }

  public function getAllINDetails($gatein_no){
    $this->db->where('GateINID', $gatein_no);
    $result = $this->db->get(db_prefix().'GateMasterDetails')->result();
    $data = [];
    foreach ($result as $row) {
      $row->value = !empty($row->value) ? json_decode($row->value) : [];
      $key = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $row->type));
      $data[$key] = $row;
    }

    return $data;
  }

  public function getByItemID($itemID)
  {
    $this->db->select('iqp.id, iqp.ItemID, iqp.ItemParameterID, iqp.MinValue, iqp.MaxValue, iqp.BaseValue, iqp.CalculationBy, qpm.ItemParameterName as ParameterName');
    $this->db->from(db_prefix().'ItemQCParameter iqp');
    $this->db->join(db_prefix().'QCParameterMaster qpm', 'qpm.ItemParameterID = iqp.ItemParameterID', 'left');
    $this->db->where('iqp.ItemID', $itemID);
    $iqpRows = $this->db->get()->result();
    
    // Attach deduction_matrix rows
    foreach ($iqpRows as $row){
      $row->CalculationBy = ($row->CalculationBy == 1) ? 'Percentage' : 'Amount';
      $this->db->select('id, Value, Deduction');
      $this->db->where('ItemID', $row->ItemID);
      $this->db->where('ItemParameterID', $row->ItemParameterID);
      $row->deduction_matrix = $this->db->get(db_prefix().'deduction_matrix')->result();
    }

    return $iqpRows;
  }

  public function getStackQcDetails($GateINID){
    $this->db->select('st.*, i.UnitWeightIn');
    $this->db->from(db_prefix().'stockInventory st');
    $this->db->join(db_prefix().'items i', 'i.ItemID = st.ItemID', 'left');
    $this->db->where('st.GateINID', $GateINID);
    $stacks = $this->db->get()->result_array();
    $result = [];
    foreach ($stacks as $row) {
      $stack = [
        'id'      => $row['id'],
        'item_id' => $row['ItemID'],
        'godown'  => $row['WHID'],
        'chamber' => $row['CHID'],
        'stack'   => $row['StackID'],
        'lot'     => $row['LOTID'],
        'weight'  => $row['Weight'],
        'bag_qty' => $row['BagQty'],
        'uom'     => $row['UnitWeightIn'],
        'qc'      => []
      ];

      $this->db->where('GateINID', $GateINID);
      $this->db->where('layer_number', $row['id']);

      $qcRows = $this->db->get(db_prefix().'QCParameterValues')->result_array();

      foreach ($qcRows as $qc) {

        $stack['qc'][] = [
          'id'           => $qc['id'],
          'parameter_id' => $qc['ItemParameterID'],
          'value'        => $qc['ParameterValue'],
          'evalue'       => $qc['EParameterValue'],
          'hvalue'       => $qc['HParameterValue'],
          'deductionamt' => $qc['deductionAmt']
        ];
      }

      $result[] = $stack;
    }

    return $result;
  }
}