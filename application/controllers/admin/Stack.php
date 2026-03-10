<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stack extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('StackMaster_Model');
    }

    public function index()
    {
        if (!has_permission_new('StackMaster', '', 'view')) {
            access_denied('Stack Master');
        }
        $data['title'] = 'Stack Master';

        $data['table_data'] = $this->StackMaster_Model->get_StackMaster_data();

        $data['godown'] = $this->StackMaster_Model->get_GodownMaster_data();

        // $data['chamber'] = $this->StackMaster_Model->get_ChamberMaster_data();


        $this->load->view('admin/Stack/AddEditStack.php', $data);
    }

    // Get the Stack Master Details By ID
    public function GetStackMasterDetailByID()
    {
        $StackCode = $this->input->post('StackCode');
        $row = $this->StackMaster_Model->getStackMasterDetails($StackCode);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'StackCode'       => $row->StackCode,
                'StackName'     => $row->StackName,
                'GodownName' => $row->GodownID,
                'ChamberName' => $row->ChamberID,
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

    // Get Chambers by GodownID
public function GetChambersByGodown()
{
    $GodownID = $this->input->post('GodownID');
    $chambers = $this->StackMaster_Model->get_ChambersByGodown($GodownID);
    $this->output->set_content_type('application/json');
    echo json_encode($chambers);
}


    /* Save New  Stack / ajax */
    public function SaveStackMaster()
    {
        $StackCode = $this->input->post('StackCode');
        $StackName = $this->input->post('StackName');
        if ($StackCode === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Stack Code cannot be empty'
            ]);
            exit;
        }
        if ($StackName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Stack Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('StackName', $StackName);
        $exists = $this->db->get('StackMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Stack Name already exists'
            ]);
            return;
        }
        $requiredDropdowns = [
			'GodownName'  => 'Please select Godown',
			'ChamberName' => 'Please select Chamber'
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
            'StackCode' => strtoupper($this->input->post('StackCode')),
            'StackName' => strtoupper($this->input->post('StackName')),
            'PlantID' => $this->session->userdata('root_company'),
            'GodownID' => $this->input->post('GodownName'),
            'ChamberID' => $this->input->post('ChamberName'),
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
        $StackMaster  = $this->StackMaster_Model->SaveStackMaster($data);
        echo json_encode($StackMaster);
    }



    /* Update Exiting Chamber / ajax */
    public function UpdateStackMaster()
    {
        $StackName = $this->input->post('StackName');

        if ($StackName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Stack Name cannot be empty'
            ]);
            exit;
        }
        $StackCode = $this->input->post('StackCode');
        $data = array(
            'StackName' => strtoupper($this->input->post('StackName')),
            'GodownID' => $this->input->post('GodownName'),
            'ChamberID' => $this->input->post('ChamberName'),
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
        $StackMaster  = $this->StackMaster_Model->UpdateStackMaster($data, $StackCode);
        echo json_encode($StackMaster);
    }
}
