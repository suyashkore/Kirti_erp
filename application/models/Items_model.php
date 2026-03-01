<?php
use app\services\utilities\Arr;
defined('BASEPATH') or exit('No direct script access allowed');

class Items_model extends CI_Model
{
  protected $table = 'items';
  protected $primaryKey = 'id';

  public function __construct()
  {
    parent::__construct();
  }

  /* =========================
  * GET OTHER TABLE DROPDOWN DATA FOR ITEM MASTER
  * ========================= */
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

  public function getDropdownJoinBy($joinTable){
    $this->db->from(db_prefix().$this->table.' as items');
    switch($joinTable){
      case 'hsn':
        $this->db->distinct()->select('items.hsn_code as name');
        return $this->db->get()->result_array();
        break;
      case 'taxes':
        $this->db->select([
          'taxes.id as id',
          'taxes.taxrate as name',
        ]);
        $this->db->join(db_prefix().'taxes as taxes', 'taxes.id = items.tax', 'left');
        $this->db->group_by('items.tax');
        return $this->db->get()->result_array();
        break;
      case 'UnitMaster':
        $this->db->select([
          'unit.id as id',
          'unit.ShortCode as name',
        ]);
        $this->db->join(db_prefix().'UnitMaster as unit', 'unit.id = items.unit', 'left');
        $this->db->group_by('items.unit');
        return $this->db->get()->result_array();
        break;
      default:
        return [];
        break;
    }
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

  /* =========================
  * GET NEXT ITEM  CODE
  * ========================= */
  public function getNextItemCode($category_id)
  {
    $this->db->select('COUNT(' . $this->primaryKey . ') as id');
    $this->db->from(db_prefix() . $this->table);
    $this->db->where(db_prefix() . $this->table . '.ItemCategoryCode', $category_id);
    $row = $this->db->get()->row_array();

    $num = isset($row['id']) ? ((int)$row['id'] + 1) : 1;
    $strlen = strlen($num);
    $str = str_repeat('0', max(0, 5 - $strlen)) . $num;
    return $str;
  }


  /* =========================
    * GET ALL S
    * ========================= */

  public function getItemsList()
  {
      $this->db->select([
          db_prefix().$this->table.'.id',
          db_prefix().$this->table.'.ItemID',
          db_prefix().$this->table.'.ItemName',
          db_prefix().$this->table.'.hsn_code',
          db_prefix().$this->table.'.IsActive',
          db_prefix().'ItemTypeMaster.ItemTypeName',
          db_prefix().'items_main_groups.name as main_group_name',
          db_prefix().'ItemsSubGroup1.name as sub_group1_name',
          db_prefix().'ItemsSubGroup2.name as sub_group2_name',
          db_prefix().'ItemCategoryMaster.CategoryName',
          db_prefix().'ItemsDivisionMaster.name as division_name',
          db_prefix().'taxes.taxrate',
          db_prefix().'UnitMaster.ShortCode'
      ]);
      $this->db->from(db_prefix().$this->table);
      $this->db->join(db_prefix().'ItemTypeMaster', db_prefix().'ItemTypeMaster.id = '.db_prefix().$this->table.'.ItemTypeID', 'left');
      $this->db->join(db_prefix().'items_main_groups', db_prefix().'items_main_groups.id = '.db_prefix().$this->table.'.MainGrpID', 'left');
      $this->db->join(db_prefix().'ItemsSubGroup1', db_prefix().'ItemsSubGroup1.id = '.db_prefix().$this->table.'.SubGrpID1', 'left');
      $this->db->join(db_prefix().'ItemsSubGroup2', db_prefix().'ItemsSubGroup2.id = '.db_prefix().$this->table.'.SubGrpID2', 'left');
      $this->db->join(db_prefix().'ItemCategoryMaster', db_prefix().'ItemCategoryMaster.id = '.db_prefix().$this->table.'.ItemCategoryCode', 'left');
      $this->db->join(db_prefix().'ItemsDivisionMaster', db_prefix().'ItemsDivisionMaster.id = '.db_prefix().$this->table.'.DivisionID', 'left');
      $this->db->join(db_prefix().'taxes', db_prefix().'taxes.id = '.db_prefix().$this->table.'.tax', 'left');
      $this->db->join(db_prefix().'UnitMaster', db_prefix().'UnitMaster.id = '.db_prefix().$this->table.'.unit', 'left');
      $this->db->order_by($this->primaryKey, 'desc');

      return $this->db->get()->result_array();
  }

  public function getItemsListByFilter($data, $limit, $offset)
  {
      $item_type        = $data['item_type'] ?? '';
      $item_main_group  = $data['item_main_group'] ?? '';
      $item_sub_group1  = $data['item_sub_group1'] ?? '';
      $item_sub_group2  = $data['item_sub_group2'] ?? '';
      $item_division    = $data['item_division'] ?? '';
      $item_category    = $data['item_category'] ?? '';
      $hsn              = $data['hsn'] ?? '';
      $gst              = $data['gst'] ?? '';
      $unit             = $data['unit'] ?? '';

      $this->db->from(db_prefix().$this->table);

      $this->db->join(db_prefix().'ItemTypeMaster', db_prefix().'ItemTypeMaster.id = '.db_prefix().$this->table.'.ItemTypeID', 'left');
      $this->db->join(db_prefix().'items_main_groups', db_prefix().'items_main_groups.id = '.db_prefix().$this->table.'.MainGrpID', 'left');
      $this->db->join(db_prefix().'ItemsSubGroup1', db_prefix().'ItemsSubGroup1.id = '.db_prefix().$this->table.'.SubGrpID1', 'left');
      $this->db->join(db_prefix().'ItemsSubGroup2', db_prefix().'ItemsSubGroup2.id = '.db_prefix().$this->table.'.SubGrpID2', 'left');
      $this->db->join(db_prefix().'ItemCategoryMaster', db_prefix().'ItemCategoryMaster.id = '.db_prefix().$this->table.'.ItemCategoryCode', 'left');
      $this->db->join(db_prefix().'ItemsDivisionMaster', db_prefix().'ItemsDivisionMaster.id = '.db_prefix().$this->table.'.DivisionID', 'left');
      $this->db->join(db_prefix().'taxes', db_prefix().'taxes.id = '.db_prefix().$this->table.'.tax', 'left');
      $this->db->join(db_prefix().'UnitMaster', db_prefix().'UnitMaster.id = '.db_prefix().$this->table.'.unit', 'left');

      if($item_type != '')       $this->db->where(db_prefix().$this->table.'.ItemTypeID', $item_type);
      if($item_main_group != '') $this->db->where(db_prefix().$this->table.'.MainGrpID', $item_main_group);
      if($item_sub_group1 != '') $this->db->where(db_prefix().$this->table.'.SubGrpID1', $item_sub_group1);
      if($item_sub_group2 != '') $this->db->where(db_prefix().$this->table.'.SubGrpID2', $item_sub_group2);
      if($item_division != '')   $this->db->where(db_prefix().$this->table.'.DivisionID', $item_division);
      if($item_category != '')   $this->db->where(db_prefix().$this->table.'.ItemCategoryCode', $item_category);
      if($hsn != '')             $this->db->where(db_prefix().$this->table.'.hsn_code', $hsn);
      if($gst != '')             $this->db->where(db_prefix().$this->table.'.tax', $gst);
      if($unit != '')            $this->db->where(db_prefix().$this->table.'.unit', $unit);

      $total = $this->db->count_all_results('', FALSE);

      $this->db->select([
          db_prefix().$this->table.'.id',
          db_prefix().$this->table.'.ItemID',
          db_prefix().$this->table.'.ItemName',
          db_prefix().$this->table.'.hsn_code',
          db_prefix().$this->table.'.PackingWeight',
          db_prefix().$this->table.'.UnitWeightIn',
          db_prefix().$this->table.'.IsActive',
          db_prefix().'ItemTypeMaster.ItemTypeName',
          db_prefix().'items_main_groups.name as main_group_name',
          db_prefix().'ItemsSubGroup1.name as sub_group1_name',
          db_prefix().'ItemsSubGroup2.name as sub_group2_name',
          db_prefix().'ItemCategoryMaster.CategoryName',
          db_prefix().'ItemsDivisionMaster.name as division_name',
          db_prefix().'taxes.taxrate',
          db_prefix().'UnitMaster.ShortCode'
      ]);

      $this->db->order_by($this->primaryKey, 'desc');
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


  /* =========================
    * GET SINGLE 
    * ========================= */
  public function getById($id)
  {
      $this->db->select([
          db_prefix().$this->table.'.*',
          db_prefix().'ItemTypeMaster.ItemTypeName',
          db_prefix().'items_main_groups.name as main_group_name',
          db_prefix().'ItemsSubGroup1.name as sub_group1_name',
          db_prefix().'ItemsSubGroup2.name as sub_group2_name',
          db_prefix().'ItemCategoryMaster.CategoryName',
          db_prefix().'ItemsDivisionMaster.name as division_name',
          db_prefix().'taxes.taxrate',
          db_prefix().'UnitMaster.ShortCode'
      ]);
      $this->db->from(db_prefix().$this->table);
      $this->db->join(db_prefix().'ItemTypeMaster', db_prefix().'ItemTypeMaster.id = '.db_prefix().$this->table.'.ItemTypeID', 'left');
      $this->db->join(db_prefix().'items_main_groups', db_prefix().'items_main_groups.id = '.db_prefix().$this->table.'.MainGrpID', 'left');
      $this->db->join(db_prefix().'ItemsSubGroup1', db_prefix().'ItemsSubGroup1.id = '.db_prefix().$this->table.'.SubGrpID1', 'left');
      $this->db->join(db_prefix().'ItemsSubGroup2', db_prefix().'ItemsSubGroup2.id = '.db_prefix().$this->table.'.SubGrpID2', 'left');
      $this->db->join(db_prefix().'ItemCategoryMaster', db_prefix().'ItemCategoryMaster.id = '.db_prefix().$this->table.'.ItemCategoryCode', 'left');
      $this->db->join(db_prefix().'ItemsDivisionMaster', db_prefix().'ItemsDivisionMaster.id = '.db_prefix().$this->table.'.DivisionID', 'left');
      $this->db->join(db_prefix().'taxes', db_prefix().'taxes.id = '.db_prefix().$this->table.'.tax', 'left');
      $this->db->join(db_prefix().'UnitMaster', db_prefix().'UnitMaster.id = '.db_prefix().$this->table.'.unit', 'left');
      $this->db->where(db_prefix().$this->table.'.'.$this->primaryKey, $id);
      return $this->db->get()->row();
  }

  /* =========================
    * INSERT
    * ========================= */
  public function addItem($data)
  {
      $data['TransDate'] = date('Y-m-d H:i:s');

      $this->db->insert(db_prefix() . $this->table, $data);
      return $this->db->insert_id();
  }

  /* =========================
    * UPDATE
    * ========================= */
  public function updateItem($id, $data)
  {
      $data['Lupdate'] = date('Y-m-d H:i:s');

      $this->db->where($this->primaryKey, $id);
      return $this->db->update(db_prefix() . $this->table, $data);
  }

  /* =========================
    * DELETE
    * ========================= */
  public function delete($id)
  {
      $this->db->where($this->primaryKey, $id);
      return $this->db->delete(db_prefix() . $this->table);
  }

  /* =========================
    * CHECK DUPLICATE
    * ========================= */
  public function checkDuplicate($name, $id = null)
  {
      $this->db->where('ItemID', $name);

      if (!empty($id)) {
          $this->db->where($this->primaryKey . ' !=', $id);
      }

      return $this->db->count_all_results(db_prefix() . $this->table) > 0;
  }

  /* =========================
    * CHANGE STATUS
    * ========================= */
  public function changeStatus($id, $status)
  {
      return $this->db->where($this->primaryKey, $id)
          ->update(db_prefix() . $this->table, [
              'isactive' => $status,
              'Lupdate'  => date('Y-m-d H:i:s')
          ]);
  }

  public function get_company_detail()
  {
    $selected_company = $this->session->userdata('root_company');
    $sql ='SELECT '.db_prefix().'rootcompany.* FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
    $result = $this->db->query($sql)->row();
    return $result;
  }

  public function getQcParameterByItemID($itemID)
  {
    $this->db->select('qcpm.ItemParameterID, qcpm.ItemParameterName');
    $this->db->from(db_prefix().'QCParameterMaster qcpm');
    $this->db->join(
        db_prefix().'ItemQCParameter iqcp',
        'iqcp.ItemParameterID = qcpm.ItemParameterID',
        'inner'
    );
    $this->db->where('iqcp.ItemId', $itemID);
    $this->db->where('iqcp.Status', 'Y');

    $query = $this->db->get();
    return $query->result();
  }

  public function getQcParameterDetails($itemID, $qcParameterID){
    $this->db->select('id, MaxValue, MinValue, BaseValue, CalculationBy');
    $this->db->from(db_prefix().'ItemQCParameter');
    $this->db->where('ItemId', $itemID);
    $this->db->where('ItemParameterID', $qcParameterID);
    $query = $this->db->get();
    return $query->row();
  }
  
  public function getDeductionMatrixList($itemID, $qcParameterID){
    $this->db->select('*');
    $this->db->from(db_prefix().'deduction_matrix');
    $this->db->where('ItemId', $itemID);
    $this->db->where('ItemParameterID', $qcParameterID);
    $query = $this->db->get();
    return $query->result();
  }

  public function saveBatchDeductionMatrix($data)
  {
    $insertBatch = [];
    $updateBatch = [];

    $count  = count($data['value']);
    $now    = date('Y-m-d H:i:s');

    for ($i = 0; $i < $count; $i++) {
      $row = [
        'ItemID'        	=> $data['item_id'],
        'ItemParameterID' => $data['qc_parameter'],
        'Value'      	    => $data['value'][$i],
        'Deduction'      	=> $data['deduction'][$i]
      ];

      if (!empty($data['update_id'][$i])) {
        $row['id'] = $data['update_id'][$i];
        $row['UserID2'] = $data['UserID'];
        $row['Lupdate'] = $now;
        $updateBatch[] = $row;
      }else {
        $row['UserID'] = $data['UserID'];
        $row['TransDate'] = $now;
        $insertBatch[] = $row;
      }
    }

    $this->db->trans_start();

    if (!empty($insertBatch)) {
      $this->db->insert_batch(db_prefix().'deduction_matrix', $insertBatch);
    }

    if (!empty($updateBatch)) {
      $this->db->update_batch(db_prefix().'deduction_matrix', $updateBatch, 'id');
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }
  
}