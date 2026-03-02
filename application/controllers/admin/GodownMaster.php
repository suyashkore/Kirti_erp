<?php

defined('BASEPATH') or exit('No direct script access allowed');

class GodownMaster extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('GodownMaster_Model');
    }
    public function index()
    {
        if (!has_permission_new('GodownMaster', '', 'view')) {
            access_denied('Godown Master');
        }
        $data['state'] = $this->GodownMaster_Model->getallstate();

        $data['table_data'] = $this->GodownMaster_Model->get_GodownMaster_data();

        $data['locations'] = $this->GodownMaster_Model->get_location_detail();

        $this->load->view('admin/GodownMaster/GodownMaster.php', $data);
    }
    public function GetCityListByStateID()
    {
        $id = $this->input->post('id');
        $quarter_data = $this->GodownMaster_Model->GetCityList($id);
        echo json_encode($quarter_data);
    }
    /* Get Godown Master Details by GodownCode / ajax */
    public function GetGodownMasterDetailByID()
    {
        $GodownCode = $this->input->post('GodownCode');
        $row = $this->GodownMaster_Model->getGodownMasterDetails($GodownCode);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'GodownCode'       => $row->GodownCode,
                'GodownName'     => $row->GodownName,
                'Location' => $row->LocationID,
                'Pincode' => $row->Pincode,
                'State' => $row->StateCode,
                'City' => $row->CityID,
                'Address' => $row->Address,
                'IsActive' => $row->IsActive

            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Get Location Details by PlantID / ajax */
    public function GetLocationDetailByPlantID()
    {
        $PlantID = $this->session->PlantID('root_company');
        $row = $this->GodownMaster_Model->getLocationDetails($GodownCode);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'id'       => $row->id,
                'LocationName'     => $row->LocationName
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Save New  Godown Master / ajax */
    public function SaveGodownMaster()
    {
        $GodownCode = $this->input->post('GodownCode');
        $GodownName = $this->input->post('GodownName');
        $Pincode = $this->input->post('Pincode');
        if ($GodownCode === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Godown Code cannot be empty'
            ]);
            exit;
        }
        if ($GodownName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Godown Name cannot be empty'
            ]);
            exit;
        }
        if ($Pincode === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Pin Code cannot be empty'
            ]);
            exit;
        }
        $requiredDropdowns = [
			'Location'  => 'Please select Plant Location',
			'State'  => 'Please select State',
			'City'  => 'Please select Plant Location'
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
            'PlantID' => $this->session->userdata('root_company'),
            'GodownCode' => strtoupper($this->input->post('GodownCode')),
            'GodownName' => $this->input->post('GodownName'),
            'LocationID' => $this->input->post('Location'),
            'Pincode' => $this->input->post('Pincode'),
            'Statecode' => $this->input->post('State'),
            'CityID' => $this->input->post('City'),
            'Address' => $this->input->post('Address'),
            'IsActive' => $this->input->post('IsActive'),
            'UserID' => $this->session->userdata('username'),
            'Transdate' => date('Y-m-d H:i:s'),
        );
        $GodownMaster  = $this->GodownMaster_Model->SaveGodownMaster($data);
        echo json_encode($GodownMaster);
    }


    /* Update Exiting Godown Master / ajax */
    public function UpdateGodownMaster()
    {
        $GodownCode = $this->input->post('GodownCode');
        $data = array(
            'GodownName' => $this->input->post('GodownName'),
            'LocationID' => $this->input->post('Location'),

            'Pincode' => $this->input->post('Pincode'),
            'Statecode' => $this->input->post('State'),
            'CityID' => $this->input->post('City'),
            'Address' => $this->input->post('Address'),
            'IsActive' => $this->input->post('IsActive'),
            'UserID2' => $this->session->userdata('username'),
            'Lupdate' => date('Y-m-d H:i:s'),
        );
        $GodownMaster  = $this->GodownMaster_Model->UpdateGodownMaster($data, $GodownCode);
        echo json_encode($GodownMaster);
    }
}
