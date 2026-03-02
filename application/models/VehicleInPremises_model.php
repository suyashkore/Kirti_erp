<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VehicleInPremises_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_company_detail($selected_company)
    {
        $selected_company = $this->session->userdata('root_company');
        $sql = 'SELECT ' . db_prefix() . 'rootcompany.*, tblxx_statelist.state_name as state 
                FROM ' . db_prefix() . 'rootcompany 
                LEFT JOIN tblxx_statelist ON tblxx_statelist.short_name = ' . db_prefix() . 'rootcompany.state 
                WHERE tblrootcompany.id = ' . $selected_company;

        $result = $this->db->query($sql)->row();
        return $result;
    }

    public function get_in_premises_report()
    {
        $statusTextMap = [
            1 => 'GateIn',
            2 => 'GrossWeight',
            3 => 'Conveyor',
            4 => 'StackQC',
            5 => 'TareWeight',
            6 => 'GateOut',
            7 => 'GateExit',
        ];

        // Fetch all GateMaster records (including status 1)
        $this->db->select('gm.GateINID, gm.VehicleNo, gm.TransDate as EntryTime, gm.status, pl.LocationName as Location');
        $this->db->from('tblGateMaster gm');
        $this->db->join('tblPlantLocationDetails pl','pl.id = gm.LocationID', 'left');
        $this->db->where_in('gm.status', array_keys($statusTextMap));
        $result = $this->db->get()->result_array();

        foreach ($result as &$row) {
            $statusType = $statusTextMap[$row['status']] ?? null;

            if ($statusType && $row['status'] > 1) {
                // For status > 1, fetch the matching GateMasterDetails TransDate as ExitTime
                $exitRow = $this->db
                    ->select('TransDate')
                    ->from(db_prefix() . 'GateMasterDetails')
                    ->where('GateINID', $row['GateINID'])
                    ->where('type', $statusType)
                    ->get()->row_array();
                    
                $row['ExitTime'] = $exitRow['TransDate'] ?? null;
            } else {
                //  Status 1 = only gate in, not yet progressed - no exit time
                $row['ExitTime'] = null;
            }

            //  If no ExitTime found, fall back to current timestamp
            if (empty($row['ExitTime'])) {
                $row['ExitTime'] = null; // Let JS handle live ticking - don't hardcode now()
            }

            // Map status number to display label
            $row['status_text'] = $statusType ?? 'Unknown';
        }
        return $result;
    }
}