<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Form extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('FormMaster_Model');
    }
    public function index()
    {
        if (!has_permission_new('Form', '', 'view')) {
            access_denied('Form Master');
        }
        $data['lastId'] = $this->FormMaster_Model->get_last_recordForm();
        $data['table_data'] = $this->FormMaster_Model->get_Form_data();
        $this->load->view('admin/Form/AddEditForm.php', $data);
    }

    /* Save New  Form / ajax */
    public function SaveForm()
    {
        $FormName = $this->input->post('FormName');
        if ($FormName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Form Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('FormName', $FormName);
        $exists = $this->db->get('Form')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Form Name already exists'
            ]);
            return;
        }
        $data = array(
            'id' => $this->input->post('FormID'),
            'FormName' => strtoupper($this->input->post('FormName')),
            'IsActive' => $this->input->post('IsActive'),
            'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
        );
        $Form  = $this->FormMaster_Model->SaveForm($data);
        echo json_encode($Form);
    }

    /* Get Territory Details by FormID / ajax */
    public function GetFormDetailByID()
    {
        $FormID = $this->input->post('FormID');
        $row = $this->FormMaster_Model->getFormDetails($FormID);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'FormID' => $row->id,
                'FormName' => $row->FormName,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting Form / ajax */
    public function UpdateForm()
    {
        $FormID = $this->input->post('FormID');
        $FormName = $this->input->post('FormName');
        if ($FormName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Form Name cannot be empty'
            ]);
            exit;
        }

        // Duplicate name check (EXCEPT same ID)
        $this->db->where('FormName', $FormName);
        $this->db->where('id !=', $FormID);
        $exists = $this->db->get('Form')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Form Name already exists'
            ]);
            exit;
        }

        $data = array(
            'FormName'     => strtoupper($this->input->post('FormName')),
            'IsActive' => $this->input->post('IsActive'),
            'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
        );
        $Form  = $this->FormMaster_Model->UpdateForm($data, $FormID);
        echo json_encode($Form);
    }
}
