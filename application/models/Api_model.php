<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends App_Model
{
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
    $this->db->insert(db_prefix().$table, $data);
    return $this->db->insert_id();
  }

  // ===== UPDATE DATA =====
  public function updateData($table, $data, $where = null)
  {
    $data['Lupdate'] = date('Y-m-d H:i:s');
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

  // ===== GET RESULT DATA =====
  public function getResultData($table, $select='*', $where = null)
  {
    $this->db->select($select);
    $this->db->from(db_prefix().$table);
    if($where != null) $this->db->where($where);
    return $this->db->get()->result();
  }

  // ITEM CONTROLLER RELATED FUNCTIONS
  function getItemGroupDetails($id){
    $this->db->select('isg2.*, itm.id as item_type_id');
    $this->db->from(db_prefix().'ItemsSubGroup2 isg2');
    $this->db->join(db_prefix().'items_main_groups img', 'img.id = isg2.main_group_id', 'left');
    $this->db->join(db_prefix().'ItemTypeMaster itm', 'itm.id = img.ItemTypeID', 'left');
    $this->db->join(db_prefix().'ItemCategoryMaster icm', 'icm.ItemType = itm.id', 'left');
    $this->db->where('isg2.id', $id);
    return $this->db->get()->row();
  }

}