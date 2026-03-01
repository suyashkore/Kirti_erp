<?php
use app\services\utilities\Arr;
defined('BASEPATH') or exit('No direct script access allowed');

class Qcparametermaster_model extends CI_Model
{
    protected $table = 'QCParameterMaster';
    protected $primaryKey = 'ItemParameterID';

    public function __construct()
    {
        parent::__construct();
    }

    /* =========================
    * GET NEXT ITEM PARAMETER ID
    * ========================= */
    public function getNextParameterId()
    {
        $this->db->select('ItemParameterID');
        $this->db->from(db_prefix() . $this->table);
        $this->db->order_by('ItemParameterID', 'DESC');
        $this->db->limit(1);

        $row = $this->db->get()->row_array();

        return isset($row['ItemParameterID']) ? ((int)$row['ItemParameterID'] + 1) : 1;
    }


    /* =========================
     * GET ALL PARAMETERS
     * ========================= */
    public function getParameters($activeOnly = false)
    {
        $this->db->select(db_prefix() . $this->table . '.*');
        $this->db->from(db_prefix() . $this->table);
        if ($activeOnly) {
            $this->db->where(db_prefix() . $this->table . '.IsActive', 'Y');
        }
        $this->db->order_by($this->primaryKey, 'ASC');

        return $this->db->get()->result_array();
    }

    /* =========================
     * GET SINGLE PARAMETER
     * ========================= */
    public function getParameterById($id)
    {
        $this->db->select(db_prefix() . $this->table . '.*');
        $this->db->where($this->primaryKey, $id);

        return $this->db->get(db_prefix() . $this->table)->row_array();
    }

    /* =========================
     * INSERT
     * ========================= */
    public function addParameter($data)
    {
        $data['TransDate'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . $this->table, $data);
        return $this->db->insert_id();
    }

    /* =========================
     * UPDATE
     * ========================= */
    public function updateParameter($id, $data)
    {
        $data['Lupdate'] = date('Y-m-d H:i:s');

        $this->db->where($this->primaryKey, $id);
        return $this->db->update(db_prefix() . $this->table, $data);
    }

    /* =========================
     * DELETE
     * ========================= */
    public function deleteParameter($id)
    {
        $this->db->where($this->primaryKey, $id);
        return $this->db->delete(db_prefix() . $this->table);
    }

    /* =========================
     * CHECK DUPLICATE
     * ========================= */
    public function checkDuplicate($name, $id = null)
    {
        $this->db->where('ItemParameterName', $name);

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
                'IsActive' => $status,
                'Lupdate'  => date('Y-m-d H:i:s')
            ]);
    }

    /* =========================
     * DROPDOWN DATA
     * ========================= */
    public function getParameterDropdown()
    {
        $this->db->select('ItemParameterID, ItemParameterName');
        $this->db->where('IsActive', 'Y');
        $this->db->order_by('ItemParameterName', 'ASC');

        $result = $this->db->get(db_prefix() . $this->table)->result_array();

        $data = [];
        foreach ($result as $row) {
            $data[$row['ItemParameterID']] = $row['ItemParameterName'];
        }

        return $data;
    }
}
