<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grn_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}


    	/**
	 * Fetch shipping/location data from tblclientwiseshippingdata by AccountID
	 * @param  string $AccountID Client Account ID
	 * @param  string $purchaselocation purchaselocation  ID
	 * @return array Shipping data rows with city and state names
	 */
	public function getpurchaseorder($AccountID,$purchaselocation)
	{
		$this->db->select(	'tblPurchaseOrderMaster.PurchID');
		$this->db->from('tblPurchaseOrderMaster');
		$this->db->where('tblPurchaseOrderMaster.AccountID', $AccountID);
		if(!empty($purchaselocation)){
			$this->db->where('tblPurchaseOrderMaster.PurchaseLocation', $purchaselocation);
		}

		return $this->db->get()->result_array();
	}


    public function getpurchaseorderheader($PurchID)
	{
		$this->db->select(	'tblPurchaseOrderMaster.*,tblclients.Company,tblFreightTerms.FreightTerms as freight_terms');
		$this->db->from('tblPurchaseOrderMaster');
	    $this->db->join('tblclients', 'tblPurchaseOrderMaster.BrokerID = tblclients.AccountID  ', 'LEFT');
	    $this->db->join('tblFreightTerms', 'tblPurchaseOrderMaster.FreightTerms = tblFreightTerms.Id  ', 'LEFT');
		$this->db->where('tblPurchaseOrderMaster.PurchID', $PurchID);

		return $this->db->get()->result_array();
	}
     public function getpurchaseorderitems($OrderID)
	{
		$this->db->select(	'tblhistory.*,tblitems.ItemName');
		$this->db->from('tblhistory');
	$this->db->join('tblitems', 'tblhistory.ItemID = tblitems.ItemID ', 'LEFT');
		$this->db->where('tblhistory.OrderID', $OrderID);
		return $this->db->get()->result_array();
	}

 public function get_transporter_name()
	{
		$this->db->select(	'tblclients.Company,tblclients.AccountID');
		$this->db->from('tblclients');
     	$this->db->join('tblAccountSubGroup2', 'tblclients.ActSubGroupID2 = tblAccountSubGroup2.SubActGroupID', 'LEFT');
		$this->db->where('tblAccountSubGroup2.IsTransporter', 'Y');
		$this->db->where('tblclients.IsActive', 'Y');
		return $this->db->get()->result_array();
	}

	 public function get_vehicle_owner()
	{
		$this->db->select(	'tblclients.Company,tblclients.AccountID');
		$this->db->from('tblclients');
     	$this->db->join('tblAccountSubGroup2', 'tblclients.ActSubGroupID2 = tblAccountSubGroup2.SubActGroupID', 'LEFT');
		$this->db->where('tblAccountSubGroup2.IsVehicleOwner', 'Y');
		$this->db->where('tblclients.IsActive', 'Y');
		return $this->db->get()->result_array();
	}

}