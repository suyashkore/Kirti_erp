<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Territory extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('TerritoryMaster_Model');
    }
    public function index()
    {
        if (!has_permission_new('Territory', '', 'view')) {
            access_denied('Territory Master');
        }
        $data['lastId'] = $this->TerritoryMaster_Model->get_last_recordTerritory();
        $data['table_data'] = $this->TerritoryMaster_Model->get_Territory_data();
        $this->load->view('admin/Territory/AddEditTerritory.php', $data);
    }

    /* Save New  Territory / ajax */
    public function SaveTerritory()
    {
        $TerritoryName = $this->input->post('TerritoryName');
        if ($TerritoryName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Territory Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('TerritoryDescription', $TerritoryName);
        $exists = $this->db->get('Territory')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Territory Name already exists'
            ]);
            return;
        }
        $data = array(
            'Id' => $this->input->post('TerritoryID'),
            'TerritoryDescription' => strtoupper($this->input->post('TerritoryName')),
            'IsActive' => $this->input->post('IsActive'),
            'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
        );
        $Territory  = $this->TerritoryMaster_Model->SaveTerritory($data);
        echo json_encode($Territory);
    }

    /* Get Territory Details by TerritoryID / ajax */
    public function GetTerritoryDetailByID()
    {
        $TerritoryID = $this->input->post('TerritoryID');
        $row = $this->TerritoryMaster_Model->getTerritoryDetails($TerritoryID);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'TerritoryID' => $row->Id,
                'TerritoryName' => $row->TerritoryDescription,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting Territory / ajax */
    public function UpdateTerritory()
    {
        $TerritoryID = $this->input->post('TerritoryID');
        $TerritoryName = $this->input->post('TerritoryName');
        if ($TerritoryName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Territory Name cannot be empty'
            ]);
            exit;
        }

        // Duplicate name check (EXCEPT same ID)
        $this->db->where('TerritoryDescription', $TerritoryName);
        $this->db->where('Id !=', $TerritoryID);
        $exists = $this->db->get('Territory')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Territory Name already exists'
            ]);
            exit;
        }

        $data = array(
            'TerritoryDescription'     => strtoupper($this->input->post('TerritoryName')),
            'IsActive' => $this->input->post('IsActive'),
            'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
        );
        $Territory  = $this->TerritoryMaster_Model->UpdateTerritory($data, $TerritoryID);
        echo json_encode($Territory);
    }
}
