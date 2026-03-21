<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders_model extends App_Model
{
  protected $table = 'PurchaseOrderMaster';
  protected $primaryKey = 'id';
	public function __construct()
	{
		parent::__construct();
	}

  public function getListByFilter($data, $limit, $offset)
  {
    $from_date    = $data['from_date'] ?? date('Y-m-01');
    $to_date      = $data['to_date'] ?? date('Y-m-d');
    $category_id  = $data['category_id'] ?? '';
    $vendor_id    = $data['vendor_id'] ?? '';
    $broker_id    = $data['broker_id'] ?? '';
    $status       = $data['status'] ?? 1;
    $location       = $data['filter_purchase_location'] ?? '' ;

    $this->db->from(db_prefix().$this->table);

    $this->db->join(db_prefix().'ItemCategoryMaster cat', 'cat.id = '.db_prefix().$this->table.'.ItemCategory', 'left');
    $this->db->join(db_prefix().'clients vendor', 'vendor.AccountID = '.db_prefix().$this->table.'.AccountID', 'left');
    $this->db->join(db_prefix().'clients broker', 'broker.AccountID = '.db_prefix().$this->table.'.BrokerID', 'left');
    $this->db->join(
			db_prefix() . 'PlantLocationDetails',
			db_prefix() . 'PlantLocationDetails.id = ' . db_prefix() . 'PurchaseOrderMaster.PurchaseLocation',
			'left'
		);
    
    if($category_id != '')     $this->db->where(db_prefix().$this->table.'.ItemCategory', $category_id);
    if($vendor_id != '')       $this->db->where(db_prefix().$this->table.'.AccountID', $vendor_id);
    if($broker_id != '')       $this->db->where(db_prefix().$this->table.'.BrokerID', $broker_id);
    if($status != '')          $this->db->where(db_prefix().$this->table.'.Status', $status);
    if($from_date != '')       $this->db->where(db_prefix().$this->table.'.TransDate >=', $from_date);
    if($to_date != '')         $this->db->where(db_prefix().$this->table.'.TransDate <=', $to_date);
    if (!empty($location)) {
			$this->db->where(db_prefix() . 'PurchaseOrderMaster.PurchaseLocation', $location);
	}

		$this->db->order_by(db_prefix() . 'PurchaseOrderMaster.id', 'DESC');

    $total = $this->db->count_all_results('', FALSE);

    $this->db->select([
      db_prefix().$this->table.'.*',
      'cat.CategoryName as category_name',
      'vendor.company as vendor_name',
      'broker.company as broker_name',
      'tblPlantLocationDetails.LocationName'
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