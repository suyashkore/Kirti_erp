<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customer_master extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_master_model');
    }

    /* List all customer masters */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('customer_master');
        }
        $data['title'] = 'Customer Master';
        $data['customers'] = $this->customer_master_model->get();
        $this->load->view('admin/customer_master/manage', $data);
    }

    /* Add or Edit Customer Master */
    public function customer($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            // Handle File Upload
            if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {
                $path = FCPATH . 'uploads/customer_master/';
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $config['upload_path']   = $path;
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx';
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('attachment')) {
                    $file_data = $this->upload->data();
                    $data['attachment'] = $file_data['file_name'];
                }
            }

            if ($id == '') {
                $id = $this->customer_master_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', 'Customer Master'));
                    redirect(admin_url('customer_master'));
                }
            } else {
                $success = $this->customer_master_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', 'Customer Master'));
                }
                redirect(admin_url('customer_master'));
            }
        }

        if ($id == '') {
            $data['title'] = 'Add New Customer';
            $data['customer_code'] = $this->customer_master_model->get_next_code();
        } else {
            $data['customer'] = $this->customer_master_model->get($id);
            $data['contacts'] = $this->customer_master_model->get_contacts($id);
            $data['locations'] = $this->customer_master_model->get_locations($id);
            $data['title'] = 'Edit Customer';
        }

        // Load Masters Data
        $this->load->model('clients_model');
        $data['customer_groups'] = $this->clients_model->get_groups();
        $data['countries']       = get_all_countries();
        $data['currencies']      = $this->currencies_model->get();
        $data['states']          = get_all_states();
        $data['cities']          = get_all_cities();
        
        // Placeholder for other masters (Assuming standard tables or you need to create models for them)
        // You would typically load these from their respective models
        $data['territories']     = []; // $this->territory_model->get();
        $data['brokers']         = []; // $this->broker_model->get();
        $data['broker_persons']  = []; // $this->broker_person_model->get();
        $data['freight_terms']   = []; // $this->freight_term_model->get();
        $data['locations_master']= []; // $this->location_master_model->get();
        $data['tds_master']      = []; // $this->tds_model->get();
        $data['payment_terms']   = []; // $this->payment_terms_model->get();

        $this->load->view('admin/customer_master/customer', $data);
    }

    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('customer_master'));
        }
        $response = $this->customer_master_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', 'Customer Master'));
        } else {
            set_alert('warning', _l('problem_deleting', 'Customer Master'));
        }
        redirect(admin_url('customer_master'));
    }
}