<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CurrencyMaster extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('currency_master_model');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('currencies_master');
        }

        // As in TransportMaster, load transport_model to get countries
        $this->load->model('transport_model');
        $data['countries'] = $this->transport_model->getallcountry();

        $data['currencies'] = $this->currency_master_model->get_all();
        $data['title'] = 'Currency Master';
        $this->load->view('admin/CurrencyMaster/manage', $data);
    }

    public function create()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->currency_master_model->add($data);
            if ($success) {
                echo json_encode(['success' => true, 'message' => _l('added_successfully', 'Currency')]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add currency.']);
            }
        }
    }
}
