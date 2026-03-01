<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_terms extends AdminController { // Changed from CI_Controller to AdminController

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
    }

    public function index()
    {
        $data['title'] = 'Payment Terms';
        
        // Mock data based on your existing records requirement
        $data['payment_terms'] = [
            (object)['code' => 'P000', 'desc' => 'Immediate', 'days' => 0, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P001', 'desc' => '1 Days', 'days' => 1, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P002', 'desc' => '2 Days', 'days' => 2, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P003', 'desc' => '3 Days', 'days' => 3, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P004', 'desc' => '4 Day', 'days' => 4, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P005', 'desc' => '5 Days', 'days' => 5, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P007', 'desc' => '7 Days', 'days' => 7, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P009', 'desc' => '9 Days', 'days' => 9, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P010', 'desc' => '10 Days', 'days' => 10, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P015', 'desc' => '15 Days', 'days' => 15, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P020', 'desc' => '20 Days', 'days' => 20, 'base' => 'Posting Date', 'blocked' => 'No'],
            (object)['code' => 'P025', 'desc' => '25 Days', 'days' => 25, 'base' => 'Posting Date', 'blocked' => 'No'],
        ];

        $this->load->view('admin/payments/payment_terms', $data);
    }

    public function create()
    {
        // Form Validation Rules
        $this->form_validation->set_rules('code', 'Payment Terms Code', 'required|trim');
        $this->form_validation->set_rules('description', 'Payment Terms Description', 'required|trim');
        $this->form_validation->set_rules('days', 'No. of Days', 'required|numeric');
        $this->form_validation->set_rules('due_date_based_on', 'Due Date Based On', 'required');

        if ($this->form_validation->run() == FALSE) {
            // If validation fails, reload the view with errors
            $this->index();
        } else {
            // Logic to save data to database would go here
            // $this->Payment_terms_model->insert($this->input->post());

            set_alert('success', 'Payment Term Created Successfully');
            redirect(admin_url('payment_terms'));
        }
    }
}
