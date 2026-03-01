<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Chamber extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ChamberMaster_Model');
    }
    public function index()
    {
        if (!has_permission_new('ChamberMaster', '', 'view')) {
            access_denied('Chamber Master');
        }

        $data['table_data'] = $this->ChamberMaster_Model->get_ChamberMaster_data();

        $data['godown'] = $this->ChamberMaster_Model->get_GodownMaster_data();

        $this->load->view('admin/Chamber/AddEditChamber.php', $data);
    }

    // Get the Chamber Master Details By ID
    public function GetChamberMasterDetailByID()
    {
        $ChamberCode = $this->input->post('ChamberCode');
        $row = $this->ChamberMaster_Model->getChamberMasterDetails($ChamberCode);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'ChamberCode'       => $row->ChamberCode,
                'ChamberName'     => $row->ChamberName,
                'GodownName' => $row->GodownID,
                'Length' => $row->length,
                'Width' => $row->width,
                'Height' => $row->height,
                'Margin' => $row->margin,
                'TotalArea' => $row->total_area,
                'UtilizeArea' => $row->utilize_area,
                'volume' => $row->volume,
                'Capacity' => $row->capacity,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }


    /* Save New  Chamber / ajax */
    public function SaveChamberMaster()
    {
        $ChamberCode = $this->input->post('ChamberCode');
        $ChamberName = $this->input->post('ChamberName');

        if ($ChamberCode === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Chamber Code cannot be empty'
            ]);
            exit;
        }
        if ($ChamberName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Chamber Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('ChamberName', $ChamberName);
        $exists = $this->db->get('ChamberMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Chamber Name already exists'
            ]);
            return;
        }
        $requiredDropdowns = [
			'GodownName'  => 'Please select Godown'
		];

		foreach ($requiredDropdowns as $field => $errorMsg) {
			$value = $this->input->post($field);
			if (empty($value) || $value == '0') {
				echo json_encode([
					'status' => false,
					'message' => $errorMsg
				]);
				exit;
			}
		}
        $data = array(
            'ChamberCode' => strtoupper($this->input->post('ChamberCode')),
            'ChamberName' => strtoupper($this->input->post('ChamberName')),
            'PlantID' => $this->session->userdata('root_company'),
            'GodownID' => $this->input->post('GodownName'),
            'length' => $this->input->post('length'),
            'width' => $this->input->post('Width'),
            'height' => $this->input->post('Height'),
            'margin' => $this->input->post('Margin'),
            'total_area' => $this->input->post('TotalArea'),
            'utilize_area' => $this->input->post('UtilizeArea'),
            'volume' => $this->input->post('Volume'),
            'capacity' => $this->input->post('Capacity'),
            'Transdate' => date('Y-m-d H:i:s'),
            'UserID' => $this->session->userdata('username'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $ChamberMaster  = $this->ChamberMaster_Model->SaveChamberMaster($data);
        echo json_encode($ChamberMaster);
    }



    /* Update Exiting Chamber / ajax */
    public function UpdateChamberMaster()
    {
        $ChamberName = $this->input->post('ChamberName');
        if ($ChamberName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Chamber Name cannot be empty'
            ]);
            exit;
        }
        $ChamberCode = $this->input->post('ChamberCode');
        $data = array(
            'ChamberName' => strtoupper($this->input->post('ChamberName')),
            'GodownID' => $this->input->post('GodownName'),
            'length' => $this->input->post('length'),
            'width' => $this->input->post('Width'),
            'height' => $this->input->post('Height'),
            'margin' => $this->input->post('Margin'),
            'total_area' => $this->input->post('TotalArea'),
            'utilize_area' => $this->input->post('UtilizeArea'),
            'volume' => $this->input->post('Volume'),
            'capacity' => $this->input->post('Capacity'),
            'UserID2' => $this->session->userdata('username'),
            'Lupdate' => date('Y-m-d H:i:s'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $ChamberMaster  = $this->ChamberMaster_Model->UpdateChamberMaster($data, $ChamberCode);
        echo json_encode($ChamberMaster);
    }
}
