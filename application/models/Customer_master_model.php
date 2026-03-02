<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customer_master_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblcustomer_master')->row();
        }
        return $this->db->get('tblcustomer_master')->result_array();
    }

    public function get_contacts($master_id)
    {
        $this->db->where('customer_master_id', $master_id);
        return $this->db->get('tblcustomer_master_contacts')->result_array();
    }

    public function get_locations($master_id)
    {
        $this->db->where('customer_master_id', $master_id);
        return $this->db->get('tblcustomer_master_locations')->result_array();
    }

    public function get_next_code()
    {
        $this->db->select_max('id');
        $res = $this->db->get('tblcustomer_master')->row();
        $next_id = ($res->id) ? $res->id + 1 : 1;
        return 'C' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    public function add($data)
    {
        $contacts = [];
        if (isset($data['contact'])) {
            $contacts = $data['contact'];
            unset($data['contact']);
        }

        $locations = [];
        if (isset($data['location'])) {
            $locations = $data['location'];
            unset($data['location']);
        }

        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert('tblcustomer_master', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if (!empty($contacts)) {
                $this->add_related_data($insert_id, $contacts, $locations);
            }
            log_activity('New Customer Master Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    public function update($data, $id)
    {
        $contacts = [];
        if (isset($data['contact'])) {
            $contacts = $data['contact'];
            unset($data['contact']);
        }

        $locations = [];
        if (isset($data['location'])) {
            $locations = $data['location'];
            unset($data['location']);
        }

        $data['updated_by'] = get_staff_user_id();
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tblcustomer_master', $data);

        // Clear existing related data and re-insert (simplest approach for dynamic rows)
        $this->db->where('customer_master_id', $id);
        $this->db->delete('tblcustomer_master_contacts');

        $this->db->where('customer_master_id', $id);
        $this->db->delete('tblcustomer_master_locations');

        $this->add_related_data($id, $contacts, $locations);

        if ($this->db->affected_rows() > 0) {
            log_activity('Customer Master Updated [ID: ' . $id . ']');
            return true;
        }
        return true; // Return true even if no main fields changed but related data might have
    }

    private function add_related_data($id, $contacts = [], $locations = [])
    {
        if (!empty($contacts) && is_array($contacts)) {
            foreach ($contacts as $contact) {
                if (!empty($contact['name'])) {
                    $contact['customer_master_id'] = $id;
                    $this->db->insert('tblcustomer_master_contacts', $contact);
                }
            }
        }
        if (!empty($locations) && is_array($locations)) {
            foreach ($locations as $location) {
                if (!empty($location['address'])) {
                    $location['customer_master_id'] = $id;
                    $this->db->insert('tblcustomer_master_locations', $location);
                }
            }
        }
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcustomer_master');
        
        $this->db->where('customer_master_id', $id);
        $this->db->delete('tblcustomer_master_contacts');

        $this->db->where('customer_master_id', $id);
        $this->db->delete('tblcustomer_master_locations');

        if ($this->db->affected_rows() > 0) {
            log_activity('Customer Master Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
}