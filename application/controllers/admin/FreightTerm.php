<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FreightTerm extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('FreightTermMaster_Model');
    }
    public function index()
    {
        if (!has_permission_new('FreightTerm', '', 'view')) {
            access_denied('Freight Term');
        }
        $data['lastId'] = $this->FreightTermMaster_Model->get_last_recordFreightTerm();
        $data['table_data'] = $this->FreightTermMaster_Model->get_FreightTerm_data();
        $this->load->view('admin/FreightTerm/AddEditFreight.php', $data);
    }

    /* Save New  Item Division / ajax */
    public function SaveFreightTerm()
    {
        $FreightTermName = $this->input->post('FreightTermName');
        if ($FreightTermName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Freight Term Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('FreightTerms', $FreightTermName);
        $exists = $this->db->get('FreightTerms')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Freight Term Name already exists'
            ]);
            return;
        }
        $data = array(
            'Id' => $this->input->post('FreightTermID'),
            'FreightTerms' => strtoupper($this->input->post('FreightTermName')),
            'IsActive' => $this->input->post('IsActive'),
            'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
        );
        $FreightTerms  = $this->FreightTermMaster_Model->SaveFreightTerm($data);
        echo json_encode($FreightTerms);
    }

    /* Get Freight Terms Details by FreightTermsID / ajax */
    public function GetFreightTermDetailByID()
    {
        $FreightTermID = $this->input->post('FreightTermID');
        $row = $this->FreightTermMaster_Model->getFreightTermDetails($FreightTermID);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'FreightTermID' => $row->Id,
                'FreightTermName' => $row->FreightTerms,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting Item Division / ajax */
    public function UpdateFreightTerm()
    {
        $FreightTermID = $this->input->post('FreightTermID');
        $FreightTermName = strtoupper($this->input->post('FreightTermName'));
        if ($FreightTermName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Freight Term Name cannot be empty'
            ]);
            exit;
        }

        // Duplicate name check (EXCEPT same ID)
        $this->db->where('FreightTerms', $FreightTermName);
        $this->db->where('Id !=', $FreightTermID);
        $exists = $this->db->get('FreightTerms')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Freight Term Name already exists'
            ]);
            exit;
        }

        $data = array(
            'FreightTerms'     => strtoupper($this->input->post('FreightTermName')),
            'IsActive' => $this->input->post('IsActive'),
            'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
        );
        $FreightTerm  = $this->FreightTermMaster_Model->UpdateFreightTerm($data, $FreightTermID);
        echo json_encode($FreightTerm);
    }
}
