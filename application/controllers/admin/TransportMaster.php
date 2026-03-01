<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * This class describes a purchase.
 */
class TransportMaster extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('clients_model');
        $this->load->model('transport_model');
    }

    public function index()
    {
        if (!has_permission_new('transporter', '', 'view')) {
            access_denied('Transporter Master');
        }
        $data['state'] = $this->clients_model->getallstate();

        $data['countries'] = $this->transport_model->getallcountry();

        $data['gettransportergroups'] = $this->transport_model->get_transporter();

        $data['position'] = $this->clients_model->get_position();

        $data['Tdssection'] = $this->clients_model->get_tds_sections();

        $this->load->view('admin/transport/AddEditTransport', $data);
    }

    public function GetNextTransporterCode()
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
                'message' => 'Transporter ID not provided'
            ]);
            exit;
        }
        $Transport_data = $this->transport_model->GetNextTransporterCode($ActSubGroupID2);

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'next_code' => isset($Transport_data['next_code']) ? $Transport_data['next_code'] : '',
            'count' => isset($Transport_data['count']) ? $Transport_data['count'] : 0,
            'transport_code' => isset($Transport_data['transport_code']) ? $Transport_data['transport_code'] : '',
            'transport_name' => isset($Transport_data['transport_name']) ? $Transport_data['transport_name'] : '',
            'ActSubGroupID2' => $ActSubGroupID2
        ]);
        exit;
    }

    public function GetCityListByStateID()
    {
        $id = $this->input->post('id');
        $quarter_data = $this->transport_model->GetCityList($id);
        echo json_encode($quarter_data);
    }

    public function GetAllTransporterList()
    {
        $TransporterList = $this->transport_model->GetAllTransporterList();
        $html = "";
        foreach ($TransporterList as $key => $value) {
            $IsActive = ($value["IsActive"] == "Y") ? "Yes" : "No";
            $html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';
            $html .= '<td align="center">' . $value['AccountID'] . '</td>';
            $html .= '<td align="left">' . (isset($value['company']) ? $value['company'] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["FavouringName"]) ? $value["FavouringName"] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["PAN"]) ? $value["PAN"] : '') . '</td>';
            $html .= '<td align="left">' . (isset($value["GSTIN"]) ? $value["GSTIN"] : '') . '</td>';
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
        $data = $this->transport_model->getComprehensiveAccountDataByID($AccountID);
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

        $result = $this->transport_model->add_to_tblclients($AccountDetails);
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
        $updateResult = $this->transport_model->update_tblclients($AccountDetails);
        $response = [
            'success' => $updateResult ? true : false,
            'account_id' => !empty($AccountID) ? $AccountID : null,
            // 'attachment' => isset($AccountDetails['Attachment']) ? $AccountDetails['Attachment'] : (isset($AccountDetails['attachment']) ? $AccountDetails['attachment'] : null)
        ];

        echo json_encode($response);
        // echo json_encode(print_r($updateResult, true));

    }
}
