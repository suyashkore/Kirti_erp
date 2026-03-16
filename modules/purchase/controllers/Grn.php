<?php defined('BASEPATH') or exit('No direct script access allowed');

class Grn extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Quotation_model');
        $this->load->model('Grn_model');
        $this->load->model('purchase_model');
    }

    public function index()
    {

        $this->load->model('Quotation_model');
        $data['vendor_list'] = $this->Quotation_model->getVendorDropdown();
        $data['purchaselocation'] = $this->purchase_model->get_purchase_location();
        $data['transporters'] = $this->Grn_model->get_transporter_name();
        $data['vehicle_owner'] = $this->Grn_model->get_vehicle_owner();


        $this->load->view('admin/GRN/grn_form', $data);
    }
    public function getpurchaseorder()
    {

        $AccountID = $this->input->post('AccountID');
         $purchaselocation = $this->input->post('purchaselocation');

        $shippingData = $this->Grn_model->getpurchaseorder($AccountID,$purchaselocation);
        $locations = array();
        if (!empty($shippingData)) {
            foreach ($shippingData as $location) {
                $locations[] = array(
                    'po_no' => $location['PurchID'] ?? '',
                );
            }
        }

        echo json_encode([
            'status' => 'success',
            'locations' => $locations
        ]);
    }

    // PO Details by ID - Ajax
public function GetPODetailsByID()
{
    $po_id = $this->input->post('po_id');
        $header = $this->Grn_model->getpurchaseorderheader($po_id);
        $items = $this->Grn_model->getpurchaseorderitems($po_id);
        // echo"<pre>";    
        // echo"hader";
        // print_r($header);
        // echo"item";
        // print_r($items);
        // die;

    echo json_encode([
        'status' => 'success',
        'data'   => [
            'header' => $header,
            'items'  => $items
        ]
    ]);
}


public function transport($transporter_id = '')
{
    if (empty($transporter_id)) {
        echo json_encode(['pan' => '']);
        return;
    }

    $this->db->select('PAN'); 
    $this->db->from('tblclients'); 
    $this->db->where('AccountID', $transporter_id);
    $query = $this->db->get();
    $result = $query->row_array();

    if ($result) {
        echo json_encode(['pan' => $result['PAN']]);
    } else {
        echo json_encode(['pan' => '']);
    }
}

}