<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CustomerCategory extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        // $this->load->model('CustomerCategory_Model');
    }
    public function index()
    {
        if (!has_permission_new('CustomerCategory', '', 'view')) {
            access_denied('Customer Category Master');
        }

        // $data['lastId'] = $this->PriorityMaster_Model->get_last_recordPriority();
        
        // $data['table_data'] = $this->PriorityMaster_Model->get_Priority_data();
        
        // $data['form'] = $this->PriorityMaster_Model->get_Form_data();

        $this->load->view('admin/CustomerCategory/AddEditCustomerCategory.php');
    }

    // /* Save New  Priority / ajax */
    // public function SavePriority()
    // {
    //     $PriorityName = $this->input->post('PriorityName');
    //     if ($PriorityName === '') {
    //         echo json_encode([
    //             'status' => false,
    //             'message' => 'Priority Name cannot be empty'
    //         ]);
    //         exit;
    //     }
    //     // Check duplicate
    //     $this->db->where('PriorityName', $PriorityName);
    //     $exists = $this->db->get('PriorityMaster')->row();

    //     if ($exists) {
    //         echo json_encode([
    //             'status' => false,
    //             'message' => 'Priority Name already exists'
    //         ]);
    //         return;
    //     }

    //     $requiredDropdowns = [
	// 		'Form'  => 'Please select Form'
	// 	];

	// 	foreach ($requiredDropdowns as $field => $errorMsg) {
	// 		$value = $this->input->post($field);
	// 		if (empty($value) || $value == '0') {
	// 			echo json_encode([
	// 				'status' => false,
	// 				'message' => $errorMsg
	// 			]);
	// 			exit;
	// 		}
	// 	}

    //     $data = array(
    //         'id' => $this->input->post('FormID'),
    //         'PriorityName' => strtoupper($this->input->post('PriorityName')),
    //         'FormID' => $this->input->post('Form'),
    //         'IsActive' => $this->input->post('IsActive'),
    //         'Transdate' => date('Y-m-d H:i:s'),
	// 		'UserID' => $this->session->userdata('username'),
    //     );
    //     $Priority  = $this->PriorityMaster_Model->SavePriority($data);
    //     echo json_encode($Priority);
    // }

    // /* Get Priority Details by PriorityID / ajax */
    // public function GetPriorityDetailByID()
    // {
    //     $PriorityID = $this->input->post('PriorityID');
    //     $row = $this->PriorityMaster_Model->getPriorityDetails($PriorityID);

    //     $this->output->set_content_type('application/json');

    //     if ($row) {
    //         echo json_encode([
    //             'PriorityID' => $row->id,
    //             'PriorityName' => $row->PriorityName,
    //             'Form' => $row->FormID,
    //             'IsActive' => $row->IsActive
    //         ]);
    //     } else {
    //         echo json_encode(null);
    //     }
    // }

    // /* Update Exiting Priority / ajax */
    // public function UpdatePriority()
    // {
    //     $PriorityID = $this->input->post('PriorityID');
    //     $PriorityName = $this->input->post('PriorityName');
    //     if ($PriorityName === '') {
    //         echo json_encode([
    //             'status' => false,
    //             'message' => 'Priority Name cannot be empty'
    //         ]);
    //         exit;
    //     }

    //     // Duplicate name check (EXCEPT same ID)
    //     $this->db->where('PriorityName', $PriorityName);
    //     $this->db->where('id !=', $PriorityID);
    //     $exists = $this->db->get('PriorityMaster')->row();

    //     if ($exists) {
    //         echo json_encode([
    //             'status' => false,
    //             'message' => 'Priority Name already exists'
    //         ]);
    //         exit;
    //     }

    //     $data = array(
    //         'PriorityName'     => strtoupper($this->input->post('PriorityName')),
    //         'FormID' => $this->input->post('Form'),
    //         'IsActive' => $this->input->post('IsActive'),
    //         'UserID2' => $this->session->userdata('username'),
	// 		'Lupdate' => date('Y-m-d H:i:s'),
    //     );
    //     $Priority  = $this->PriorityMaster_Model->UpdatePriority($data, $PriorityID);
    //     echo json_encode($Priority);
    // }
}
