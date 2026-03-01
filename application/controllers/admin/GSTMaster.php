<?php

defined('BASEPATH') or exit('No direct script access allowed');

class GSTMaster extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('GSTMaster_Model');
    }
    public function index()
    {
        if (!has_permission_new('GSTMaster', '', 'view')) {
            access_denied('GST Master');
        }

        $data['lastId'] = $this->GSTMaster_Model->get_last_recordGST();
        
        $data['table_data'] = $this->GSTMaster_Model->get_GST_data();

        $this->load->view('admin/GST/AddEditGST.php', $data);
    }

    /* Save New GST / ajax */
    public function SaveGST()
    {
        $GSTName = $this->input->post('GSTName');
        if ($GSTName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'GST Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('name', $GSTName);
        $exists = $this->db->get('tbltaxes')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'GST Name already exists'
            ]);
            return;
        }

        $data = array(
            'id' => $this->input->post('GSTID'),
            'name' => strtoupper($this->input->post('GSTName')),
            'taxrate' => $this->input->post('GSTRate'),
            'IsActive' => $this->input->post('IsActive'),
            'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
        );
        $GST  = $this->GSTMaster_Model->SaveGST($data);
        echo json_encode($GST);
    }

    /* Get GST Details by GSTID / ajax */
    public function GetGSTDetailByID()
    {
        $GSTID = $this->input->post('GSTID');
        $row = $this->GSTMaster_Model->getGSTDetails($GSTID);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'GSTID' => $row->id,
                'GSTName' => $row->name,
                'GSTRate' => $row->taxrate,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting GST / ajax */
    public function UpdateGST()
    {
        $GSTID = $this->input->post('GSTID');
        $GSTName = $this->input->post('GSTName');
        if ($GSTName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'GST Name cannot be empty'
            ]);
            exit;
        }

        // Duplicate name check (EXCEPT same ID)
        $this->db->where('name', $GSTName);
        $this->db->where('id !=', $GSTID);
        $exists = $this->db->get('tbltaxes')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'GST Name already exists'
            ]);
            exit;
        }

        $data = array(
            'name'     => strtoupper($this->input->post('GSTName')),
            'taxrate' => $this->input->post('GSTRate'),
            'IsActive' => $this->input->post('IsActive'),
            'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
        );
        $GST  = $this->GSTMaster_Model->UpdateGST($data, $GSTID);
        echo json_encode($GST);
    }
}
