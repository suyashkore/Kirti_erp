<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class LotMaster_Model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}

		// GodownMaster Table Data
		public function get_GodownMaster_data(){
			
			$this->db->select(db_prefix() . 'godownmaster.*');
			$this->db->from(db_prefix() . 'godownmaster');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}

		// ChamberMaster Table Data
		// public function get_ChamberMaster_data(){
			
		// 	$this->db->select(db_prefix() . 'ChamberMaster.*');
		// 	$this->db->from(db_prefix() . 'ChamberMaster');
		// 	$this->db->order_by('id', 'ASC');
		// 	return $this->db->get()->result_array();
		// }

		// // StackMaster Table Data
		// public function get_StackMaster_data(){
			
		// 	$this->db->select(db_prefix() . 'StackMaster.*');
		// 	$this->db->from(db_prefix() . 'StackMaster');
		// 	$this->db->order_by('id', 'ASC');
		// 	return $this->db->get()->result_array();
		// }


		// Get Chambers filtered by GodownID
public function get_ChambersByGodown($GodownID)
{
    $this->db->select('id, ChamberName');
    $this->db->from(db_prefix() . 'ChamberMaster');
    $this->db->where('GodownID', $GodownID);
    $this->db->order_by('id', 'ASC');
    return $this->db->get()->result_array();
}

// Get Stacks filtered by GodownID AND ChamberID
public function get_StacksByGodownAndChamber($GodownID, $ChamberID)
{
    $this->db->select('id, StackName');
    $this->db->from(db_prefix() . 'StackMaster');
    $this->db->where('GodownID', $GodownID);
    $this->db->where('ChamberID', $ChamberID);
    $this->db->order_by('id', 'ASC');
    return $this->db->get()->result_array();
}

		// LotMaster Table Data
		public function get_LotMaster_data()
		{
    	$this->db->select('
        	lm.*,
			gm.GodownName,
			ch.ChamberName,
			sm.StackName
    	');

    	$this->db->from('tblLotMaster lm');

    	// GodownMaster
    	$this->db->join('tblgodownmaster gm', 'gm.id = lm.GodownID', 'left');

		// ChamberMaster
    	$this->db->join('tblChamberMaster ch', 'ch.id = lm.ChamberID', 'left');

		// StacKMaster
    	$this->db->join('tblStackMaster sm', 'sm.id = lm.StackID', 'left');

    	return $this->db->get()->result_array();
		}

		// Lot Master Table Data By ID
		public function getLotMasterDetails($LotCode){
			
			$this->db->select(db_prefix() . 'LotMaster.*');
			$this->db->from(db_prefix() . 'LotMaster');
			$this->db->where(db_prefix() . 'LotMaster.LotCode', $LotCode);
			return $this->db->get()->row();
		}

		// Add New Lot
		public function SaveLotMaster($data)
		{
			$this->db->insert(db_prefix() . 'LotMaster', $data);
			$INSERT = $this->db->affected_rows();
			if ($INSERT > 0) {
				return true;
			} else {
				return false;
			}
		}

		// Update the exisiting Lot 
		public function UpdateLotMaster($data, $LotCode)
		{
			$this->db->where('LotCode', $LotCode);
			$this->db->update(db_prefix() . 'LotMaster', $data);
			$UPDATE = $this->db->affected_rows();
			if ($UPDATE > 0) {
				return true;
			} else {
				return false;
			}
		}
	}
