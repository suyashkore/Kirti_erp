<?php

defined('BASEPATH') or exit('No direct script access allowed');

class UnitMaster extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('UnitMaster_Model');
    }

    public function index()
    {
        if (!has_permission_new('UnitMaster', '', 'view')) {
            access_denied('Unit Master');
        }

        $data['table_data'] = $this->UnitMaster_Model->get_UnitMaster_data();

        $this->load->view('admin/UnitMaster/AddEditUnit.php', $data);
    }

    // Get the Stack Master Details By ID
    public function GetUnitMasterDetailByID()
    {
        $UnitCode = $this->input->post('UnitCode');
        $row = $this->UnitMaster_Model->getunitMasterDetails($UnitCode);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'UnitCode'       => $row->ShortCode,
                'UnitName'     => $row->UnitName,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }


    /* Save New  Stack / ajax */
    public function SaveUnitMaster()
    {
        $UnitCode = $this->input->post('UnitCode');
        $UnitName = $this->input->post('UnitName');
        if ($UnitCode === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Unit Code cannot be empty'
            ]);
            exit;
        }
        if ($UnitName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Unit Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('ShortCode', $UnitCode);
        $exists = $this->db->get('UnitMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Unit Code already exists'
            ]);
            return;
        }
        // Check duplicate
        $this->db->where('UnitName', $UnitName);
        $exists = $this->db->get('UnitMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Unit Name already exists'
            ]);
            return;
        }
        
        $data = array(
            'ShortCode' => strtoupper($this->input->post('UnitCode')),
            'UnitName' => strtoupper($this->input->post('UnitName')),
            'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $UnitMaster  = $this->UnitMaster_Model->SaveUnitMaster($data);
        echo json_encode($UnitMaster);
    }



    /* Update Exiting Chamber / ajax */
    public function UpdateUnitMaster()
    {
        $UnitName = $this->input->post('UnitName');

        if ($UnitName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Unit Name cannot be empty'
            ]);
            exit;
        }
        $UnitCode = $this->input->post('UnitCode');
        $data = array(
            'UnitName' => strtoupper($this->input->post('UnitName')),
            'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $UnitMaster  = $this->UnitMaster_Model->UpdateUnitMaster($data, $UnitCode);
        echo json_encode($UnitMaster);
    }
}
