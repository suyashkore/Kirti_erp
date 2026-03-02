<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Quotation_model extends App_Model
{
  protected $table = 'PurchQuotationMaster';
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

  public function getVendorDropdown(){
    $this->db->select('c.userid, c.AccountID, c.company');
    $this->db->from(db_prefix().'clients c');
    $this->db->join(
        db_prefix().'AccountSubGroup2 a',
        'a.SubActGroupID = c.ActSubGroupID2',
        'inner'
    );
    $this->db->where('a.IsVendor', 'Y');

    $query = $this->db->get();
    return $query->result_array();
  }

  public function getNextQuotationNoByCategory($category_id) {
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
    if (!$category_id) return '';
    $this->db->where('PlantID', $PlantID);
    $this->db->where('FY', $FY);
    // $this->db->where('ItemCategory', $category_id);
    $count = $this->db->count_all_results(db_prefix().'PurchQuotationMaster');
    $next_no = $count + 1;
    return str_pad($next_no, 6, '0', STR_PAD_LEFT);
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
        'OrderID' => $data['quotation_no'],
        'PlantID' => $data['plant_id'],
        'FY' => $data['fy'],
        'TransDate' => $data['quotation_date'] ?? date('Y-m-d'),
        'AccountID' => $data['vendor_id'] ?? '',
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
        'Ordinalno' =>  $i,
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

  public function getQuotationList(){
    $this->db->select('pm.id, pm.QuotatioonID, pm.TransDate, pm.AccountID, pm.TotalWeight, pm.NetAmt, c.company');
    $this->db->from(db_prefix().'PurchQuotationMaster pm');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = pm.AccountID', 'left');
    $this->db->where('pm.TransDate >=', date('Y-m-01'));
    $this->db->order_by('pm.TransDate', 'DESC');
    return $this->db->get()->result_array();
  }

  public function getQuoteDetails($id)
  {
    $this->db->select('pm.*, c.company, icm.CategoryName as category_name');
    $this->db->from(db_prefix().'PurchQuotationMaster pm');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = pm.AccountID', 'left');
    $this->db->join( db_prefix().'ItemCategoryMaster icm', 'icm.id = pm.ItemCategory', 'left' );
    if(is_numeric($id)){
      $this->db->where('pm.id', $id);
    }else{
      $this->db->where('pm.QuotatioonID', $id);
    }
    
    $master = $this->db->get()->row_array();

    if (!$master) {
      return [];
    }

    $this->db->from(db_prefix().'history');
    $this->db->where('OrderID', $master['QuotatioonID']);
    $history = $this->db->get()->result_array();
    $master['history'] = $history;

    return $master;
  }

  public function getVendorBrokerList($vendor_id){
    $this->db->select('c.AccountID, c.company');
    $this->db->from(db_prefix().'PartyBrokerMaster pbm');
    $this->db->join(db_prefix().'clients c', 'c.AccountID = pbm.BrokerID', 'left');
    $this->db->where('pbm.AccountID', $vendor_id);
    return $this->db->get()->result_array();
  }

  public function getVendorDetailByAccountID($AccountID){
		$this->db->select([
      'clients.AccountID',
      'clients.company',
      'clients.GSTIN',
      'clients.PAN',
      'clients.billing_state',
      'clients.billing_city',
      'clients.billing_zip',
      'clients.billing_address',
      'state.state_name',
      'country.long_name as country_name',
    ]);

		$this->db->from(db_prefix() . 'clients clients');

		$this->db->join(db_prefix() . 'xx_statelist state', 'state.short_name = clients.billing_state', 'left');
		$this->db->join(db_prefix() . 'countries country', 'country.country_id = clients.billing_country', 'left');

		$this->db->where('clients.AccountID', $AccountID);

		$result = $this->db->get()->result_array();
		return $result ? $result[0] : null;
	}

  public function get_company_detail(){
    $selected_company = $this->session->userdata('root_company');
    $sql ='SELECT '.db_prefix().'rootcompany.* FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
    $result = $this->db->query($sql)->row();
    return $result;
  }

  public function get_quotation_category(){
    $this->db->distinct();
    $this->db->select('icm.id, icm.CategoryName, icm.CategoryCode');
    $this->db->from(db_prefix().'PurchQuotationMaster pqm');
    $this->db->join( db_prefix().'ItemCategoryMaster icm', 'icm.id = pqm.ItemCategory', 'inner' );

    return $this->db->get()->result_array();
  }

  public function getListByFilter($data, $limit, $offset)
  {
    $from_date    = $data['from_date'] ?? date('Y-m-01');
    $to_date      = $data['to_date'] ?? date('Y-m-d');
    $category_id  = $data['category_id'] ?? '';
    $vendor_id    = $data['vendor_id'] ?? '';
    $broker_id    = $data['broker_id'] ?? '';
    $status       = $data['status'] ?? 1;

    $this->db->from(db_prefix().$this->table);

    $this->db->join(db_prefix().'ItemCategoryMaster cat', 'cat.id = '.db_prefix().$this->table.'.ItemCategory', 'left');
    $this->db->join(db_prefix().'clients vendor', 'vendor.AccountID = '.db_prefix().$this->table.'.AccountID', 'left');
    $this->db->join(db_prefix().'clients broker', 'broker.AccountID = '.db_prefix().$this->table.'.BrokerID', 'left');
    $this->db->join(
      '(SELECT QuatationID, SUM(TotalWeight) as po_total_weight 
        FROM '.db_prefix().'PurchaseOrderMaster 
        GROUP BY QuatationID
      ) pom',
      'pom.QuatationID = '.db_prefix().$this->table.'.QuotatioonID',
      'left'
    );
    
    if($category_id != '')     $this->db->where(db_prefix().$this->table.'.ItemCategory', $category_id);
    if($vendor_id != '')       $this->db->where(db_prefix().$this->table.'.AccountID', $vendor_id);
    if($broker_id != '')       $this->db->where(db_prefix().$this->table.'.BrokerID', $broker_id);
    if($status != '')          $this->db->where(db_prefix().$this->table.'.Status', $status);
    if($from_date != '')       $this->db->where(db_prefix().$this->table.'.TransDate >=', $from_date);
    if($to_date != '')         $this->db->where(db_prefix().$this->table.'.TransDate <=', $to_date);

    $total = $this->db->count_all_results('', FALSE);

    $this->db->select([
      db_prefix().$this->table.'.*',
      'cat.CategoryName as category_name',
      'vendor.company as vendor_name',
      'broker.company as broker_name',
      'IFNULL(pom.po_total_weight,0) as po_total_weight'
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
}