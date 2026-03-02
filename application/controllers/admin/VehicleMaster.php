<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a purchase.
 */
class VehicleMaster extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clients_model');
        $this->load->model('VehicleMaster_Model');
    }

    public function index()
    {
        if (!has_permission_new('VehicleMaster', '', 'view')) {
            access_denied('Vehicle Master');
        }

        $data['getvehicleownergroups'] = $this->VehicleMaster_Model->get_vehicleowner();

        $data['transporter'] = $this->VehicleMaster_Model->get_transporter();

        $data['Tdssection'] = $this->clients_model->get_tds_sections();

        $this->load->view('admin/VehicleMaster/AddEditVehicleOwner', $data);
    }

    public function GetNextVehicleOwnerCode()
    {
        $ActSubGroupID2 = $this->input->post('ActSubGroupID2');
        if (!$ActSubGroupID2) {
            $ActSubGroupID2 = $this->input->get('ActSubGroupID2');
        }

        if (!$ActSubGroupID2) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Vehicle Owner ID not provided'
            ]);
            exit;
        }
        $VehicleOwner_data = $this->VehicleMaster_Model->GetNextVehicleOwnerCode($ActSubGroupID2);
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'next_code' => isset($VehicleOwner_data['next_code']) ? $VehicleOwner_data['next_code'] : '',
            'count' => isset($VehicleOwner_data['count']) ? $VehicleOwner_data['count'] : 0,
            'VehicleOwner_code' => isset($VehicleOwner_data['VehicleOwner_code']) ? $VehicleOwner_data['VehicleOwner_code'] : '',
            'VehicleOwner_name' => isset($VehicleOwner_data['VehicleOwner_name']) ? $VehicleOwner_data['VehicleOwner_name'] : '',
            'ActSubGroupID2' => $ActSubGroupID2
        ]);
        exit;
    }

    public function verifyBankAccount()
    {
        $bank_ac_no = $this->input->post('bank_ac_no');
        $ifsc_code = $this->input->post('ifsc_code');
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://kyc-api.surepass.io/api/v1/bank-verification/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
			"id_number": "' . $bank_ac_no . '",
			"ifsc": "' . $ifsc_code . '",
			"ifsc_details": true
			}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token . ''
                ),
            )
        );
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }

    public function gettdspercent()
    {
        $Tdsselection = $this->input->post('Tdsselection');
        $this->db->select(db_prefix() . 'TDSDetails.*');
        $this->db->where(db_prefix() . 'TDSDetails.TDSCode', $Tdsselection);
        $this->db->from(db_prefix() . 'TDSDetails');
        $data = $this->db->get()->result_array();
        echo json_encode($data);
    }

    public function gettdspercent_new($Tdsselection)
    {
        $this->db->select(db_prefix() . 'TDSDetails.*');
        $this->db->where(db_prefix() . 'TDSDetails.TDSCode', $Tdsselection);
        $this->db->from(db_prefix() . 'TDSDetails');
        $data = $this->db->get()->result_array();
        echo json_encode($data);
    }

    public function GetAllVehicleOwnerList()
    {
        $VehicleOwnerList = $this->VehicleMaster_Model->GetAllVehicleOwnerList();
        $html = "";
        foreach ($VehicleOwnerList as $key => $value) {
            $IsActive = ($value["IsActive"] == "Y") ? "Yes" : "No";
            $TDS = ($value["IsTDS"] == "Y") ? "Yes" : "No";
            $html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';
            $html .= '<td align="center">' . $value['AccountID'] . '</td>';
            $html .= '<td align="left">' . (isset($value['company']) ? $value['company'] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["PAN"]) ? $value["PAN"] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["MobileNo"]) ? $value["MobileNo"] : '') . '</td>';
            $html .= '<td align="left">' . $TDS . '</td>';
            $html .= '<td align="left">' . $IsActive . '</td>';
            $html .= '</tr>';
        }
        echo json_encode($html);
    }

    // Get Data By the AccountID
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
        $data = $this->VehicleMaster_Model->getComprehensiveAccountDataByID($AccountID);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // Save a new Client
    public function SaveAccountID()
    {
        // Collect POST data
        $AccountDetails = $this->input->post();

        $result = $this->VehicleMaster_Model->add_to_tblclients($AccountDetails);
        $response = [
            'success' => $result ? true : false,
            'account_id' => $result ? $result : null,
        ];
        echo json_encode($response);
    }

    public function UpdateAccountID($id = '')
    {

        $AccountDetails = $this->input->post();

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
        $updateResult = $this->VehicleMaster_Model->update_tblclients($AccountDetails);
        $response = [
            'success' => $updateResult ? true : false,
            'account_id' => !empty($AccountID) ? $AccountID : null,
        ];

        echo json_encode($response);
        // echo json_encode(print_r($updateResult, true));

    }

    public function CheckPanExit()
    {
        $Pan = $this->input->post('Pan');
        $PanDetails  = $this->VehicleMaster_Model->CheckPanExit($Pan);
        echo json_encode($PanDetails);
    }

}
