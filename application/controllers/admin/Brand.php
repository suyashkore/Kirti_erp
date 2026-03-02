<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Brand extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('BrandMaster_Model');
    }
    public function index()
    {
        if (!has_permission_new('Brand', '', 'view')) {
            access_denied('Brand Master');
        }
        $data['lastId'] = $this->BrandMaster_Model->get_last_recordBrand();
        $data['table_data'] = $this->BrandMaster_Model->get_Brand_data();
        $this->load->view('admin/Brand/AddEditBrand.php', $data);
    }

    /* Get Brand Details by BrandID / ajax */
    public function GetBrandDetailByID()
    {
        $BrandID = $this->input->post('BrandID');
        $row = $this->BrandMaster_Model->getBrandDetails($BrandID);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'BrandID' => $row->id,
                'BrandCode' => $row->BrandID,
                'BrandName' => $row->BrandName,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    
    public function GetBrandDetailByCode()
{
    $BrandCode = strtoupper($this->input->post('BrandCode'));

    $row = $this->db
        ->where('BrandID', $BrandCode)
        ->get('BrandMaster')
        ->row();

    $this->output->set_content_type('application/json');

    if ($row) {
        echo json_encode([
            'BrandID'   => $row->id,
            'BrandCode' => $row->BrandID,
            'BrandName' => $row->BrandName,
            'IsActive'  => $row->IsActive
        ]);
    } else {
        echo json_encode(null);
    }
}


    /* Save New  Brand / ajax */
    public function SaveBrand()
    {
        $BrandCode = $this->input->post('BrandCode');
        $BrandName = $this->input->post('BrandName');
        if ($BrandCode === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Brand Code cannot be empty'
            ]);
            exit;
        }
        if ($BrandName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Brand Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('BrandName', $BrandName);
        $exists = $this->db->get('BrandMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Brand Name already exists'
            ]);
            return;
        }
        $data = array(
            'id' => $this->input->post('BrandID'),
            'BrandID' => strtoupper($this->input->post('BrandCode')),
            'BrandName' => strtoupper($this->input->post('BrandName')),
            'IsActive' => $this->input->post('IsActive'),
            'Transdate' => date('Y-m-d H:i:s'),
			'UserID' => $this->session->userdata('username'),
        );
        $Brand  = $this->BrandMaster_Model->SaveBrand($data);
        echo json_encode($Brand);
    }
    

    /* Update Exiting Brand / ajax */
    public function UpdateBrand()
    {
        $BrandID = $this->input->post('BrandID');
        $BrandName = $this->input->post('BrandName');
        if ($BrandName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Brand Name cannot be empty'
            ]);
            exit;
        }

        // Duplicate name check (EXCEPT same ID)
        $this->db->where('BrandName', $BrandName);
        $this->db->where('id !=', $BrandID);
        $exists = $this->db->get('BrandMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Brand Name already exists'
            ]);
            exit;
        }

        $data = array(
            'BrandName' => strtoupper($this->input->post('BrandName')),
            'IsActive' => $this->input->post('IsActive'),
            'UserID2' => $this->session->userdata('username'),
			'Lupdate' => date('Y-m-d H:i:s'),
        );
        $Brand  = $this->BrandMaster_Model->UpdateBrand($data, $BrandID);
        echo json_encode($Brand);
    }
}
