<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DirectPurchase_model extends App_Model
{
  protected $table = 'DirectPurchaseMaster';
  protected $primaryKey = 'id';
	public function __construct()
	{
		parent::__construct();
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
    $data['TransDate'] = date('Y-m-d H:i:s');

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

  public function getNextDPONo() {
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
    $this->db->where('PlantID', $PlantID);
    $this->db->where('FY', $FY);
    $count = $this->db->count_all_results(db_prefix().$this->table);
    $next_no = $count + 1;
    
    return 'DPO'.$FY.$PlantID.str_pad($next_no, 5, '0', STR_PAD_LEFT);
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
      $taxable_amt = ((floatval($data['unit_rate'][$i]) ?? 0)  * (floatval($data['quantity'][$i]) ?? 0)) - (floatval($data['disc_amt'][$i]) ?? 0);
      
      $cgst = $sgst = $cgstamt = $sgstamt = $igst = $igstamt = 0;
      if ($vendor_state === $company->state) {
        $cgst = $sgst = $gst_percent / 2;
        $cgstamt = $sgstamt = ($taxable_amt * $cgst) / 100;
      } else {
        $igst = $gst_percent;
        $igstamt = ($taxable_amt * $igst) / 100;
      }
      
      $item_data = [
        'OrderID'     => $data['po_no'],
        'PlantID'     => $data['plant_id'],
        'FY'          => $data['fy'],
        'TransDate'   => $data['order_date'] ?? date('Y-m-d'),
				'TType'       => $data['TType'],
				'TType2'      => $data['TType2'],
        'AccountID'   => $data['vendor_id'] ?? '',
        'ItemID'      => $data['item_id'][$i],
        'GodownID'    => $data['godown_id'],
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
        'Ordinalno'   => $i+1,
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

  public function getDPODetails($id)
  {
    $this->db->select('dpm.*, pld.LocationName, c.company as VendorName, b.company as BrokerName, gm.GodownName');

    $this->db->from(db_prefix().$this->table.' dpm');
    $this->db->join(db_prefix().'PlantLocationDetails pld', 'pld.id = dpm.CenterLocation', 'left');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = dpm.AccountID', 'left');
    $this->db->join(db_prefix().'clients b', 'b.AccountID = dpm.BrokerID', 'left');
    $this->db->join(db_prefix().'godownmaster gm', 'gm.id = dpm.WarehouseID', 'left');

    if(is_numeric($id)){
      $this->db->where('dpm.id', $id);
    }else{
      $this->db->where('dpm.OrderID', $id);
    }
    
    $master = $this->db->get()->row_array();

    if (!$master) {
      return [];
    }

    $this->db->from(db_prefix().'history');
    $this->db->where('OrderID', $master['OrderID']);
    $history = $this->db->get()->result_array();
    $master['history'] = $history;

    return $master;
  }

  public function getDPODetailsPrint($id){
    $this->db->select('dpm.*, pld.LocationName, c.company as VendorName, c.billing_address, b.company as BrokerName, gm.GodownName, itm.ItemTypeName, sl.state_name, tdsm.TDSName, tdsd.rate as TDSRate', FALSE);

    $this->db->from(db_prefix().$this->table.' dpm');
    $this->db->join(db_prefix().'PlantLocationDetails pld', 'pld.id = dpm.CenterLocation', 'left');
    $this->db->join(db_prefix().'godownmaster gm', 'gm.id = dpm.WarehouseID', 'left');
    $this->db->join(db_prefix().'ItemTypeMaster itm', 'itm.Id = dpm.ItemType', 'left');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = dpm.AccountID', 'left');
    $this->db->join( db_prefix().'xx_statelist sl', 'CONVERT(sl.short_name USING utf8mb4) COLLATE utf8mb4_general_ci = c.billing_state', 'left', FALSE );
    $this->db->join(db_prefix().'clients b', 'b.AccountID = dpm.BrokerID', 'left');
    $this->db->join(db_prefix().'TDSMaster tdsm', 'tdsm.TDSCode = dpm.TDSCode', 'left');
    $this->db->join(db_prefix().'TDSDetails tdsd', 'tdsd.rate = dpm.TDSRate', 'left');

    if (is_numeric($id)) {
      $this->db->where('dpm.id', $id);
    } else {
      $this->db->where('dpm.OrderID', $id);
    }

    $master = $this->db->get()->row();

    if (!$master) {
      return null;
    }

    $this->db->select('h.*, i.ItemName as item_name, i.hsn_code, idm.name as DivisionName');
    $this->db->from(db_prefix().'history h');
    $this->db->join(db_prefix().'items i', 'i.ItemID = h.ItemID', 'left');
		$this->db->join(db_prefix().'ItemsDivisionMaster idm', 'idm.id = i.DivisionID', 'left');
    $this->db->where('h.OrderID', $master->OrderID);
    $history = $this->db->get()->result();

    $master->history = $history;

    return $master;
  }

  public function getListByFilter($data, $limit, $offset)
  {
    $from_date    = $data['from_date'] ?? date('Y-m-01');
    $to_date      = $data['to_date'] ?? date('Y-m-d');
    $center_location = $data['center_location'] ?? '';
    $warehouse_id = $data['warehouse_id'] ?? '';
    $purchase_type = $data['purchase_type'] ?? '';
    $vendor_id = $data['vendor_id'] ?? '';
    $broker_id = $data['broker_id'] ?? '';

    $this->db->from(db_prefix().$this->table.' dpm');
    $this->db->join(db_prefix().'PlantLocationDetails pld', 'pld.id = dpm.CenterLocation', 'left');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = dpm.AccountID', 'left');
    $this->db->join(db_prefix().'clients b', 'b.AccountID = dpm.BrokerID', 'left');
    $this->db->join(db_prefix().'godownmaster gm', 'gm.id = dpm.WarehouseID', 'left');
    $this->db->join(db_prefix().'ItemTypeMaster itm', 'itm.Id = dpm.ItemType', 'left');
    $this->db->join(db_prefix().'TDSMaster tdsm', 'tdsm.TDSCode = dpm.TDSCode', 'left');
    // $this->db->join(db_prefix().'TDSDetails tdsd', 'tdsd.rate = dpm.TDSRate', 'left');
    
    if($from_date != '')       $this->db->where('dpm.TransDate >=', $from_date);
    if($to_date != '')         $this->db->where('dpm.TransDate <=', $to_date);
    if($center_location != '') $this->db->where('dpm.CenterLocation', $center_location);
    if($warehouse_id != '')    $this->db->where('dpm.WarehouseID', $warehouse_id);
    if($purchase_type != '')   $this->db->where('dpm.ItemType', $purchase_type);
    if($vendor_id != '')       $this->db->where('dpm.AccountID', $vendor_id);
    if($broker_id != '')       $this->db->where('dpm.BrokerID', $broker_id);

    $total = $this->db->count_all_results('', FALSE);

    $this->db->select(['dpm.id', 'dpm.OrderID', 'dpm.OrderDate', 'dpm.PurchaseAmt', 'dpm.DiscAmt', 'dpm.CGSTAmt', 'dpm.SGSTAmt', 'dpm.IGSTAmt', 'dpm.FreightAmt', 'dpm.OtherAmt', 'dpm.RoundOff', 'dpm.TDSAmt', 'dpm.FinalAmt', 'pld.LocationName', 'c.company as VendorName', 'b.company as BrokerName', 'gm.GodownName', 'itm.ItemTypeName', 'tdsm.TDSName', 'dpm.TDSRate']);

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
}