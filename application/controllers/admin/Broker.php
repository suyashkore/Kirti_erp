<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Broker extends AdminController {

    public function __construct() {
        parent::__construct();
        // Load necessary models, libraries, etc.
        $this->load->model('Broker_model');
        $this->load->model('clients_model');

    }

    public function broker_form() {
        // Load the view for the Broker form with vendors list (Prefer Vendor)
       		$data['getbrokergroups'] = $this->Broker_model->get_broker();

       		$data['Vendor'] = $this->Broker_model->get_vendor();

       		$data['Customer'] = $this->Broker_model->get_customer();

        $data['position'] = $this->Broker_model->get_position();

        $data['Tdssection'] = $this->clients_model->get_tds_sections();
$data['state'] = $this->clients_model->getallstate();

		$data['countries'] = $this->Broker_model->getallcountry();

		


        $this->load->view('admin/broker/broker_form', $data);
    }

    public function GetNextBrokerCode()
	{
        // Accept either POST or GET (AJAX may vary). Prefer POST.
        $ActSubGroupID2 = $this->input->post('ActSubGroupID2');
        if (!$ActSubGroupID2) {
            $ActSubGroupID2 = $this->input->get('ActSubGroupID2');
        }

        if (!$ActSubGroupID2) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Broker ID not provided'
            ]);
            exit;
        }
		$Broker_data = $this->Broker_model->GetNextBrokerCode($ActSubGroupID2);

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'next_code' => isset($Broker_data['next_code']) ? $Broker_data['next_code'] : '',
            'count' => isset($Broker_data['count']) ? $Broker_data['count'] : 0,
            'broker_code' => isset($Broker_data['broker_code']) ? $Broker_data['broker_code'] : '',
            'broker_name' => isset($Broker_data['broker_name']) ? $Broker_data['broker_name'] : '',
            'ActSubGroupID2' => $ActSubGroupID2
        ]);
        exit;
	}



    // =============== Start of new code for fetching Broker names ===============//
    public function GetAllBrokerList()
    {


        $BrokerList = $this->Broker_model->GetAllBrokerList();

        //         echo"<pre>";
		// print_r($BrokerList);
		// die;


        $html = "";

        foreach ($BrokerList as $key => $value) {
            $html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';
            $html .= '<td align="center">' . $value['AccountID'] . '</td>';
            $html .= '<td align="left">' . (isset($value['company']) ? $value['company'] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["FavouringName"]) ? $value["FavouringName"] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["PAN"]) ? $value["PAN"] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["GSTIN"]) ? $value["GSTIN"] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["OrganisationType"]) ? $value["OrganisationType"] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["GSTType"]) ? $value["GSTType"] : '') . '</td>';
            $status = ($value["IsActive"] == "Y") ? "Yes" : "No";
            $html .= '<td align="left">' . $status . '</td>';
            $html .= '</tr>';
        }
        echo json_encode($html);

    }


    public function SaveAccountID()
    {
        // Collect POST data
        $AccountDetails = $this->input->post();

        // Handle file upload for 'attachment' field — save to FCPATH/uploads/clients and set DB field 'Attachment'
        if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDirRel = 'uploads/Broker/';
            $uploadDir = FCPATH . $uploadDirRel;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $origName = $_FILES['attachment']['name'];
            $ext = pathinfo($origName, PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $newName = $safeName . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                // Store relative path for DB (e.g., 'uploads/clients/filename.ext')
                $relPath = $uploadDirRel . $newName;
                // Set both possible keys (model mapping checks 'attachment')
                $AccountDetails['attachment'] = $relPath;
                $AccountDetails['Attachment'] = $relPath;
            } else {
                // upload failed, log and continue without attachment
                log_message('error', 'Attachment upload failed for SaveAccountID: ' . json_encode($_FILES['attachment']));
            }
        }


        $result = $this->Broker_model->add_to_tblclients($AccountDetails);
        $response = [
            'success' => $result ? true : false,
            'account_id' => $result ? $result : null,
            'attachment' => isset($AccountDetails['Attachment']) ? $AccountDetails['Attachment'] : (isset($AccountDetails['attachment']) ? $AccountDetails['attachment'] : null)
        ];
        echo json_encode($response);
    }


	public function UpdateAccountID($id = '')
    {

        $AccountDetails = $this->input->post();
        // Handle file upload for update as well
        if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDirRel = 'uploads/Broker/';
            $uploadDir = FCPATH . $uploadDirRel;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $origName = $_FILES['attachment']['name'];
            $ext = pathinfo($origName, PATHINFO_EXTENSION);
            $safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $newName = $safeName . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                $relPath = $uploadDirRel . $newName;
                $AccountDetails['attachment'] = $relPath;
                $AccountDetails['Attachment'] = $relPath;
            } else {
                log_message('error', 'Attachment upload failed for UpdateAccountID: ' . json_encode($_FILES['attachment']));
            }
        }

        // Get userid from AccountID
        $AccountID = isset($AccountDetails['AccountID']) ? $AccountDetails['AccountID'] : '';
        if (!empty($AccountID)) {
            // Fetch userid from tblclients using AccountID
            $this->db->select('userid');
            $this->db->from('tblclients');
            $this->db->where('AccountID', $AccountID);
            $result = $this->db->get()->row();

            $userid = $result ? $result->userid : 0;
        } else {
            $userid = 0;
        }

        // Pass userid as second parameter to update_tblclients
        // $updateResult = $this->Broker_model->update_tblclients($AccountDetails);
        $updateResult = $this->Broker_model->update_tblclients($AccountDetails, $userid);

        // $response = [
        //     'success' => $updateResult ? true : false,
        //     'account_id' => !empty($AccountID) ? $AccountID : null,
        //     'attachment' => isset($AccountDetails['Attachment']) ? $AccountDetails['Attachment'] : (isset($AccountDetails['attachment']) ? $AccountDetails['attachment'] : null)
        // ];

        if ($updateResult) {

            $updatedData = $this->Broker_model->getComprehensiveAccountDataByID($AccountID);

            echo json_encode([
			'success' =>$updateResult ? true : false,
         'account_id' => $AccountID,
            'data'       => $updatedData
		]);
        }

		// echo json_encode(print_r($updateResult, true));

    }

    public function GetComprehensiveAccountData()
	{
		if (!$this->input->is_ajax_request()) {
			return;
		}
		$AccountID = $this->input->post('AccountID');
		if (!$AccountID) {
			echo json_encode([
				'status' => 'error',
				'message' => 'AccountID is required'
			]);
			return;
		}
		$data = $this->Broker_model->getComprehensiveAccountDataByID($AccountID);
		echo json_encode([
			'status' => 'success',
			'data' => $data
		]);
	}


}
