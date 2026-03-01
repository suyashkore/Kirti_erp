<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Currency_master_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Returns array of rows or empty array if table missing
    public function get_all()
    {
        if (!$this->db->table_exists('currency_master')) {
            return [];
        }

        return $this->db->order_by('id', 'asc')->get('currency_master')->result_array();
    }

    public function add($data)
    {
        $row = [
            'code' => $data['code'],
            'description' => $data['description'],
            'country' => $data['country'],
            'blocked' => isset($data['blocked']) ? $data['blocked'] : 'No',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('currency_master', $row);

        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }

        return false;
    }

    /**
     * Get a currency row by country short_name
     * Returns row array or null if not found or table missing
     */
    public function get_by_country($country)
    {
        if (!$this->db->table_exists('currency_master')) {
            return null;
        }

        return $this->db->where('country', $country)->get('currency_master')->row_array();
    }

    /**
     * Update currency by id
     * Returns true on success or false on failure
     */
    public function update($id, $data)
    {
        if (!$this->db->table_exists('currency_master')) {
            return false;
        }

        $row = [
            'code' => isset($data['code']) ? $data['code'] : '',
            'description' => isset($data['description']) ? $data['description'] : '',
            'country' => isset($data['country']) ? $data['country'] : '',
            'blocked' => isset($data['blocked']) ? $data['blocked'] : 'No',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('id', $id)->update('currency_master', $row);

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Backwards compatible wrapper used by older controllers
     */
    public function get_currencies()
    {
        return $this->get_all();
    }
}

