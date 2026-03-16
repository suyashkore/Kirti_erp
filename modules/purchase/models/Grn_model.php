<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grn_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetch purchase orders by AccountID and purchaselocation
     */
    public function getpurchaseorder($AccountID, $purchaselocation)
    {
        $this->db->select('tblPurchaseOrderMaster.PurchID');
        $this->db->from('tblPurchaseOrderMaster');
        $this->db->where('tblPurchaseOrderMaster.AccountID', $AccountID);
        // if (!empty($purchaselocation)) {
            $this->db->where('tblPurchaseOrderMaster.PurchaseLocation', $purchaselocation);
        // }
        return $this->db->get()->result_array();
    }

    public function getpurchaseorderheader($PurchID)
    {
        $this->db->select('tblPurchaseOrderMaster.*,tblclients.Company,tblFreightTerms.FreightTerms as freight_terms');
        $this->db->from('tblPurchaseOrderMaster');
        $this->db->join('tblclients', 'tblPurchaseOrderMaster.BrokerID = tblclients.AccountID', 'LEFT');
        $this->db->join('tblFreightTerms', 'tblPurchaseOrderMaster.FreightTerms = tblFreightTerms.Id', 'LEFT');
        $this->db->where('tblPurchaseOrderMaster.PurchID', $PurchID);
        return $this->db->get()->result_array();
    }

    public function getpurchaseorderitems($OrderID)
    {
        $this->db->select('tblhistory.*,tblitems.ItemName');
        $this->db->from('tblhistory');
        $this->db->join('tblitems', 'tblhistory.ItemID = tblitems.ItemID', 'LEFT');
        $this->db->where('tblhistory.OrderID', $OrderID);
        return $this->db->get()->result_array();
    }

    public function get_transporter_name()
    {
        $this->db->select('tblclients.Company,tblclients.AccountID');
        $this->db->from('tblclients');
        $this->db->join('tblAccountSubGroup2', 'tblclients.ActSubGroupID2 = tblAccountSubGroup2.SubActGroupID', 'LEFT');
        $this->db->where('tblAccountSubGroup2.IsTransporter', 'Y');
        $this->db->where('tblclients.IsActive', 'Y');
        return $this->db->get()->result_array();
    }

    public function get_vehicle_owner()
    {
        $this->db->select('tblclients.Company,tblclients.AccountID');
        $this->db->from('tblclients');
        $this->db->join('tblAccountSubGroup2', 'tblclients.ActSubGroupID2 = tblAccountSubGroup2.SubActGroupID', 'LEFT');
        $this->db->where('tblAccountSubGroup2.IsVehicleOwner', 'Y');
        $this->db->where('tblclients.IsActive', 'Y');
        return $this->db->get()->result_array();
    }

    /**
     * Generate next GRN number
     * Format: GRN + FY + PlantID + 0001 (4-digit sequence)
     */
    public function getNextGRNNo()
    {
        $PlantID = $this->session->userdata('root_company');
        $FY      = $this->session->userdata('finacial_year');
        $prefix  = 'GRN' . $FY . $PlantID;

        $this->db->select('GRNNo');
        $this->db->from('tblGRNMaster');
        $this->db->like('GRNNo', $prefix, 'after');
        $this->db->order_by('GRNNo', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $row   = $query->row_array();

        if (!empty($row) && !empty($row['GRNNo'])) {
            $lastNo  = $row['GRNNo'];
            $seqPart = (int) substr($lastNo, strlen($prefix));
            $nextSeq = $seqPart + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Insert GRN Master record into tblGRNMaster
     */
    public function saveGRNMaster($data)
    {
        $result = $this->db->insert('tblGRNMaster', $data);
        if ($result) {
            $insert_id = $this->db->insert_id();
            return $insert_id ? $insert_id : $this->db->affected_rows();
        }
        return false;
    }

    /**
     * Insert GRN Item record into tblhistory
     */
    public function saveGRNItem($data)
    {
        return $this->db->insert('tblhistory', $data);
    }

    /**
     * Update GRN Master record by GRNNo
     */
    public function updateGRNMaster($grn_no, $data)
    {
        $this->db->where('GRNNo', $grn_no);
        return $this->db->update('tblGRNMaster', $data);
    }

    /**
     * ✅ FIXED: Delete old GRN items from tblhistory
     * Items are stored with AccountID = GRNNo (as set in SaveGRN/UpdateGRN)
     * TType = 'G' for GRN items
     */
    public function deleteGRNItems($grn_no)
    {
        $this->db->where('AccountID', $grn_no);
        $this->db->where('TType', 'G');
        return $this->db->delete('tblhistory');
    }

    /**
     * Get GRN Master by GRNNo
     */
    public function getGRNById($grn_id)
    {
        $this->db->where('GRNNo', $grn_id);
        return $this->db->get('tblGRNMaster')->row_array();
    }

    /**
     * Get all GRN list for modal
     */
    public function getAllGRNList()
    {
        $this->db->select('tblGRNMaster.GRNNo, tblGRNMaster.GRNNo as id, tblGRNMaster.GRNDate,
                           tblclients.company as VendorName,
                           tblGRNMaster.OrderNo, tblGRNMaster.PlantLocation,
                           tblGRNMaster.Status');
        $this->db->from('tblGRNMaster');
        $this->db->join('tblclients', 'tblGRNMaster.AccountID = tblclients.AccountID', 'LEFT');
        $this->db->order_by('tblGRNMaster.id', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * ✅ FIXED: Get GRN items by GRNNo
     * Items stored with AccountID = GRNNo and TType = 'G'
     */
    public function getGRNItemsByBillID($grn_no)
    {
        $this->db->select('tblhistory.*, tblitems.ItemName');
        $this->db->from('tblhistory');
        $this->db->join('tblitems', 'tblhistory.ItemID = tblitems.ItemID', 'LEFT');
        $this->db->where('tblhistory.AccountID', $grn_no);
        $this->db->where('tblhistory.TType', 'G');
        return $this->db->get()->result_array();
    }
}