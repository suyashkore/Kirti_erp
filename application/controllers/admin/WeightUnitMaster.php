<?php

defined('BASEPATH') or exit('No direct script access allowed');

class WeightUnitMaster extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('WeightUnitMaster_Model');
    }

    public function index()
    {
        if (!has_permission_new('WeightUnitMaster', '', 'view')) {
            access_denied('Weight Unit Master');
        }

        $data['table_data'] = $this->WeightUnitMaster_Model->get_WeightUnitMaster_data();

        $this->load->view('admin/WeightUnitMaster/AddEditWeightUnit.php', $data);
    }

    // Get the Stack Master Details By ID
    public function GetWeightUnitMasterDetailByID()
    {
        $WeightUnitCode = $this->input->post('WeightUnitCode');
        $row = $this->WeightUnitMaster_Model->getWeightUnitMasterDetails($WeightUnitCode);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'WeightUnitCode'       => $row->ShortCode,
                'WeightUnitName'     => $row->UnitName,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }


    /* Save New  Stack / ajax */
    public function SaveWeightUnitMaster()
    {
        $WeightUnitCode = $this->input->post('WeightUnitCode');
        $WeightUnitName = $this->input->post('WeightUnitName');
        if ($WeightUnitCode === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Weight Unit Code cannot be empty'
            ]);
            exit;
        }
        if ($WeightUnitName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Weight Unit Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('ShortCode', $WeightUnitCode);
        $exists = $this->db->get('WeightUnitMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Weight Unit Code already exists'
            ]);
            return;
        }
        // Check duplicate
        $this->db->where('UnitName', $WeightUnitName);
        $exists = $this->db->get('WeightUnitMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Weight Unit Name already exists'
            ]);
            return;
        }
        
        $data = array(
            'ShortCode' => strtoupper($this->input->post('WeightUnitCode')),
            'UnitName' => strtoupper($this->input->post('WeightUnitName')),
            'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $WeightUnitMaster  = $this->WeightUnitMaster_Model->SaveWeightUnitMaster($data);
        echo json_encode($WeightUnitMaster);
    }



    /* Update Exiting Weight Unit Master / ajax */
    public function UpdateWeightUnitMaster()
    {
        $WeightUnitName = $this->input->post('WeightUnitName');

        if ($WeightUnitName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Weight Unit Name cannot be empty'
            ]);
            exit;
        }
        $WeightUnitCode = $this->input->post('WeightUnitCode');
        $data = array(
            'UnitName' => strtoupper($this->input->post('WeightUnitName')),
            'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $WeightUnitMaster  = $this->WeightUnitMaster_Model->UpdateWeightUnitMaster($data, $WeightUnitCode);
        echo json_encode($WeightUnitMaster);
    }
}
