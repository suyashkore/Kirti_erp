<?php
use app\services\utilities\Arr;
defined('BASEPATH') or exit('No direct script access allowed');

class Accounts_model extends CI_Model
{
  protected $table = 'clients';
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
    $data['TransDate'] = date('Y-m-d H:i:s');
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
  
  public function getAllLeadger($accountSubGroupId2=null){
    $this->db->select('c.userid, c.AccountID, c.company, c.ActSubGroupID2, mg.ActGroupName as ActMainGroupName, sg1.SubActGroupName as ActSubGroup1Name, sg2.SubActGroupName as ActSubGroup2Name, c.IsActive, c.DeActiveReason, b.IFSC, b.BankName, b.BranchName, b.BankAddress, b.AccountNo, b.HolderName');
    $this->db->from(db_prefix().'clients c');
    $this->db->join( db_prefix().'AccountSubGroup2 sg2', 'sg2.SubActGroupID = c.ActSubGroupID2', 'inner' );
    $this->db->join( db_prefix().'AccountSubGroup1 sg1', 'sg1.SubActGroupID1 = c.ActSubGroupID1', 'left' );
    $this->db->join( db_prefix().'AccountMainGroup mg', 'mg.ActGroupID = c.ActMainGroupID', 'left' );
    $this->db->join( db_prefix().'BankMaster b', 'b.AccountID = c.AccountID', 'left' );
    $this->db->where('sg2.IsAccountHead', 'Y');
    if($accountSubGroupId2 != null) $this->db->where('c.ActSubGroupID2', $accountSubGroupId2);
    $this->db->order_by('c.AccountID', 'DESC');

    $query = $this->db->get();
    return $query->result_array();
  }

  public function getLedgerDetails($ledgerId){
    $this->db->select('c.userid, c.AccountID, c.company, c.ActSubGroupID2, mg.ActGroupName as ActMainGroupName, sg1.SubActGroupName as ActSubGroup1Name, sg2.SubActGroupName as ActSubGroup2Name, c.IsActive, c.DeActiveReason, b.IFSC, b.BankName, b.BranchName, b.BankAddress, b.AccountNo, b.HolderName');
    $this->db->from(db_prefix().'clients c');
    $this->db->join( db_prefix().'AccountSubGroup2 sg2', 'sg2.SubActGroupID = c.ActSubGroupID2', 'inner' );
    $this->db->join( db_prefix().'AccountSubGroup1 sg1', 'sg1.SubActGroupID1 = c.ActSubGroupID1', 'left' );
    $this->db->join( db_prefix().'AccountMainGroup mg', 'mg.ActGroupID = c.ActMainGroupID', 'left' );
    $this->db->join( db_prefix().'BankMaster b', 'b.AccountID = c.AccountID', 'left' );
    $this->db->where('c.AccountID', $ledgerId);

    $query = $this->db->get();
    return $query->row();
  }

  public function getNextLedgerCode($accountSubGroupId2=null){
    $this->db->select('COUNT(c.userid) as total');
    $this->db->from(db_prefix().'clients c');
    $this->db->join( db_prefix().'AccountSubGroup2 a', 'a.SubActGroupID = c.ActSubGroupID2', 'inner' );

    $this->db->where('a.IsAccountHead', 'Y');
    if($accountSubGroupId2 != null) $this->db->where('c.ActSubGroupID2', $accountSubGroupId2);

    $query = $this->db->get()->row();
    return $query->total + 1;
  }
}