<?php

defined('BASEPATH') or exit('No direct script access allowed');

class TdsMaster extends AdminController
{
    private $not_importable_fields = ['id'];
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tds_model');
    }
    public function index()
    {
        if (!has_permission_new('tdsmaster', '', 'view')) {
            access_denied('tdsmaster');
        }
        $data=[];
        $data['next_tds_code'] = $this->Tds_model->GetNextTDSCode();
        $this->load->view('admin/TDSMaster/AddEditTdsMaster', $data);
    }
	 public function AccountListPopUp()
    {
        $TDSList  = $this->Tds_model->GetTDSList();
        $html = "";
        foreach ($TDSList  as $key => $value) {
           $html .= '<tr class="get_AccountID" data-id="' . $value["TDSCode"] . '">';
           $html .= '<td>' . $value["TDSCode"] . '</td>';
           $html .= '<td>' . $value["TDSName"] . '</td>';
           $html .= '<td>' . $value["ThresholdLimit"] . '</td>';
           $html .= '</tr>';
        }
        echo $html;
    }
	public function GetNextTDSCode()
    {
        $nextCode = $this->Tds_model->GetNextTDSCode();
        echo json_encode(['code' => $nextCode]);
    }
	public function GetAccountDetailByID()
    {
        $TDSCode = $this->input->post('TDSCode');
        $itemDetails = $this->Tds_model->GetTDSDetails($TDSCode);
        echo json_encode($itemDetails);
    }
	public function SaveItemID()
    {
        $data = array(
            'TDSCode'=>$this->input->post('TDSCode'),
            'TDSName'=>strtoupper($this->input->post('TDSName')),
            'Blocked'=>$this->input->post('isactive'),
            'paradataArraylength'=>$this->input->post('paradataArraylength'),
            'paradataSerializedArr'=>$this->input->post('paradataSerializedArr'),
        );
        try {
            $item = $this->Tds_model->SaveItemID($data);
            if ($item === true) {
                echo json_encode(['success' => true]);
            } else {
                log_message('error', 'TdsMaster: SaveItemID failed to save. Input: ' . json_encode($data));
                echo json_encode(['success' => false, 'message' => 'Could not save record']);
            }
        } catch (Exception $e) {
            log_message('error', 'TdsMaster: SaveItemID exception: ' . $e->getMessage() . ' | Input: ' . json_encode($data));
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }
	 /* Update Exiting ItemID / ajax */
    public function UpdateItemID()
    {
        $data = array(
            'TDSCode'=>$this->input->post('TDSCode'),
            'TDSName'=>strtoupper($this->input->post('TDSName')),
            'Blocked'=>$this->input->post('isactive'),
            'paradataArraylength'=>$this->input->post('paradataArraylength'),
            'paradataSerializedArr'=>$this->input->post('paradataSerializedArr'),
        );
        try {
            $item = $this->Tds_model->UpdateItemID($data);
            if ($item === true) {
                echo json_encode(['success' => true]);
            } else {
                log_message('error', 'TdsMaster: UpdateItemID failed to update. Input: ' . json_encode($data));
                echo json_encode(['success' => false, 'message' => 'Could not update record']);
            }
        } catch (Exception $e) {
            log_message('error', 'TdsMaster: UpdateItemID exception: ' . $e->getMessage() . ' | Input: ' . json_encode($data));
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }
}