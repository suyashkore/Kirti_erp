<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class ItemQCParameter_model extends App_Model
	{
		protected $table = 'ItemQCParameter';
    	protected $primaryKey = 'id';

		public function __construct()
		{
			parent::__construct();
		}

		public function saveBatch($data)
		{
			$insertBatch = [];
			$updateBatch = [];

			$itemId = $data['item_id'];
			$count  = count($data['parameter_id']);
			$now    = date('Y-m-d H:i:s');

			for ($i = 0; $i < $count; $i++) {
				$row = [
					'ItemID'        	=> $itemId,
					'ItemParameterID'   => $data['parameter_id'][$i],
					'MinValue'      	=> $data['min_value'][$i],
					'MaxValue'      	=> $data['max_value'][$i],
					'BaseValue'     	=> $data['base_value'][$i],
					'CalculationBy' 	=> $data['calculation_by'][$i],
					'Status'         	=> $data['status'][$i],
					'UserID'			=> $data['UserID2'],
					'UserID2'			=> $data['UserID2']
				];

				if (!empty($data['update_id'][$i])) {
					$row['id'] = $data['update_id'][$i];
					$row['Lupdate'] = $now;
					$updateBatch[] = $row;
				}else {
					$row['TransDate'] = $now;
					$insertBatch[] = $row;
				}
			}

			$this->db->trans_start();

			if (!empty($insertBatch)) {
				$this->db->insert_batch(db_prefix() . $this->table, $insertBatch);
			}

			if (!empty($updateBatch)) {
				$this->db->update_batch(db_prefix() . $this->table, $updateBatch, 'id');
			}

			$this->db->trans_complete();

			return $this->db->trans_status();
		}

		public function getByItemID($itemID)
		{
			$this->db->select('id, ItemID, ItemParameterID, MinValue, MaxValue, BaseValue, CalculationBy, Status');
			$this->db->where('ItemID', $itemID);
			return $this->db->get(db_prefix() . $this->table)->result_array();
		}
	}
