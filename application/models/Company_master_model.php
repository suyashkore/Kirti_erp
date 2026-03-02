<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Company_master_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->get('tblrootcompany')->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('id', (int)$id);
        return $this->db->get('tblrootcompany')->row();
    }

    // NEW
    public function get_by_shortcode($short)
    {
        $short = strtoupper(trim((string)$short));
        $this->db->where('comp_short', $short);
        return $this->db->get('tblrootcompany')->row();
    }

    public function get_locations($plant_id)
    {
        $this->db->where('PlantID', (int)$plant_id);
        return $this->db->get('tblPlantLocationDetails')->result_array();
    }

    private function map_root_status($status_input)
    {
        // View sends: active/deactive OR 1/2
        if ($status_input === 'active' || (string)$status_input === '1') return 1;
        if ($status_input === 'deactive' || (string)$status_input === '2') return 2;
        return 1;
    }

    private function map_loc_active($status_input)
    {
        // View sends: active/deactive OR Y/N
        if ($status_input === 'active' || $status_input === 'Y' || $status_input === 'y') return 'Y';
        if ($status_input === 'deactive' || $status_input === 'N' || $status_input === 'n') return 'N';
        return 'Y';
    }

    public function add($data)
    {
        if (isset($data['id'])) unset($data['id']);

        $root_status = $this->map_root_status($data['status'] ?? '1');

        $db_data = [
            'comp_short'     => $data['comp_short'] ?? '',
            'company_name'   => $data['company_name'] ?? '',
            'gst'            => $data['gst_no'] ?? ($data['gst'] ?? ''),
            'address'        => $data['address'] ?? '',
            'pincode'        => !empty($data['pincode']) ? (int)$data['pincode'] : null,
            'CityID'         => !empty($data['city']) ? (int)$data['city'] : 0,
            'city'           => null,
            'state'          => $data['state'] ?? '',
            'BusinessEmail'  => $data['email'] ?? ($data['BusinessEmail'] ?? null),
            'mobile1'        => $data['mobile'] ?? ($data['mobile1'] ?? ''),
            'status'         => $root_status,
            'datecreated'    => date('Y-m-d H:i:s'),
            'dateupdated'    => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('tblrootcompany', $db_data);
        $plant_id = $this->db->insert_id();

        if (!$plant_id) return false;

        $this->save_locations($plant_id, $data);

        return $plant_id;
    }

    public function update($data, $id)
    {
        if (isset($data['id'])) unset($data['id']);

        $root_status = $this->map_root_status($data['status'] ?? '1');

        $db_data = [
            'comp_short'     => $data['comp_short'] ?? '',
            'company_name'   => $data['company_name'] ?? '',
            'gst'            => $data['gst_no'] ?? ($data['gst'] ?? ''),
            'address'        => $data['address'] ?? '',
            'pincode'        => !empty($data['pincode']) ? (int)$data['pincode'] : null,
            'CityID'         => !empty($data['city']) ? (int)$data['city'] : 0,
            'state'          => $data['state'] ?? '',
            'BusinessEmail'  => $data['email'] ?? ($data['BusinessEmail'] ?? null),
            'mobile1'        => $data['mobile'] ?? ($data['mobile1'] ?? ''),
            'status'         => $root_status,
            'dateupdated'    => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', (int)$id);
        $success = $this->db->update('tblrootcompany', $db_data);

        if ($success) {
            $this->db->where('PlantID', (int)$id)->delete('tblPlantLocationDetails');
            $this->save_locations((int)$id, $data);
        }

        return $success;
    }

    private function save_locations($plant_id, $data)
    {
        $loc_states  = $data['loc_state'] ?? [];
        $loc_cities  = $data['loc_city'] ?? [];
        $loc_names   = $data['loc_location'] ?? [];
        $loc_addr    = $data['loc_address'] ?? [];
        $loc_pin     = $data['loc_pincode'] ?? [];
        $loc_mob     = $data['loc_mobile'] ?? [];
        $loc_status  = $data['loc_status'] ?? [];
        $loc_fssai   = $data['loc_fssai'] ?? [];
        $loc_expiry  = $data['loc_expiry_date'] ?? [];

        $has_rows = is_array($loc_states) && count($loc_states) > 0;

        if (!$has_rows) {
            $loc_states = [ $data['state'] ?? '' ];
            $loc_cities = [ $data['city'] ?? 0 ];
            $loc_names  = [ '' ];
            $loc_addr   = [ $data['address'] ?? '' ];
            $loc_pin    = [ $data['pincode'] ?? '' ];
            $loc_mob    = [ $data['mobile'] ?? '' ];
            $loc_status = [ ($data['status'] ?? 'active') ];
            $loc_fssai  = [ '' ];
            $loc_expiry = [ '' ];
        }

        $staff_id = function_exists('get_staff_user_id') ? (string)get_staff_user_id() : '0';

        $rows = count($loc_states);
        for ($i = 0; $i < $rows; $i++) {
            $stateCode = $loc_states[$i] ?? '';
            $cityId    = $loc_cities[$i] ?? 0;
            $addr      = $loc_addr[$i] ?? '';

            if ($stateCode === '' && (int)$cityId === 0 && trim($addr) === '') {
                continue;
            }

            $location_data = [
                'PlantID'         => (int)$plant_id,
                'comp_short'      => $data['comp_short'] ?? '',
                'StateCode'       => $stateCode,
                'CityID'          => !empty($cityId) ? (int)$cityId : 0,
                'LocationName'    => $loc_names[$i] ?? '',
                'Address'         => $addr,
                'PinCode'         => !empty($loc_pin[$i]) ? (int)$loc_pin[$i] : 0,
                'MobileNo'        => $loc_mob[$i] ?? '',
                'fssai_no'        => $loc_fssai[$i] ?? null,
                'fssai_no_expiry' => !empty($loc_expiry[$i]) ? $loc_expiry[$i] : null,
                'IsActive'        => $this->map_loc_active($loc_status[$i] ?? 'active'),
                'UserID'          => $staff_id,
                'TransDate'       => date('Y-m-d H:i:s'),
                'UserID2'         => null,
                'LupDate'         => null,
            ];

            $this->db->insert('tblPlantLocationDetails', $location_data);
        }
    }

    public function delete($id)
    {
        $id = (int)$id;
        $this->db->where('PlantID', $id)->delete('tblPlantLocationDetails');
        $this->db->where('id', $id);
        return $this->db->delete('tblrootcompany');
    }
}