<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Company_master extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('company_master_model');
        $this->load->model('clients_model');
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('company_master');
        }

        $data['title'] = 'Company Master';
        $data['table_exists'] = false;
        $data['companies'] = [];
        $data['error_message'] = '';

        $table_exists = $this->db->query("SHOW TABLES LIKE 'tblrootcompany'")->num_rows() > 0;

        if ($table_exists) {
            $data['companies'] = $this->company_master_model->get_all();
            $data['table_exists'] = true;
        } else {
            $data['error_message'] = 'Database table "tblrootcompany" not found. Please run the SQL migration first.';
        }

        $data['state'] = $this->clients_model->getallstate();
        $data['states'] = $data['state'];
        $data['StationList'] = $this->clients_model->get_StationList();

        $this->load->view('admin/company/manage', $data);
    }

    public function company($id = '')
    {
        if ($this->input->post()) {
            $post = $this->input->post();
            
            // Add UserID from POST data
            $post['UserID'] = $this->input->post('UserID');

            if ($id == '') {
                // INSERT: Add new company
                $insert_id = $this->company_master_model->add($post);
                if ($insert_id) {
                    set_alert('success', _l('added_successfully', 'Company Master'));
                } else {
                    set_alert('danger', 'Error adding company');
                }
                redirect(admin_url('company_master'));
            } else {
                // UPDATE: Update existing company
                $post['id'] = $id;
                $success = $this->company_master_model->update($post, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', 'Company Master'));
                } else {
                    set_alert('danger', 'Error updating company');
                }
                redirect(admin_url('company_master'));
            }
        }

        // GET: form load
        $data = [];
        $data['title'] = 'Company Master';

        $data['state'] = $this->clients_model->getallstate();
        $data['states'] = $data['state'];
        $data['StationList'] = $this->clients_model->get_StationList();

        if (is_numeric($id) && $id != '') {
            $data['company'] = $this->company_master_model->get_by_id($id);
            $data['locations'] = $this->company_master_model->get_locations($id);
        } else {
            $data['company'] = null;
            $data['locations'] = [];
        }

        $this->load->view('admin/company/manage', $data);
    }

    public function delete($id)
    {
        if (!is_numeric($id)) {
            redirect(admin_url('company_master'));
        }

        $table_exists = $this->db->query("SHOW TABLES LIKE 'tblrootcompany'")->num_rows() > 0;

        if ($table_exists) {
            $success = $this->company_master_model->delete($id);
            if ($success) {
                set_alert('success', _l('deleted_successfully', 'Company Master'));
            } else {
                set_alert('danger', _l('deletion_failed', 'Company Master'));
            }
        } else {
            set_alert('danger', 'Database table not found');
        }

        redirect(admin_url('company_master'));
    }

    public function change_status($id, $status)
    {
        // tblrootcompany.status => 1 Active, 2 Deactive
        $new_status = ((int)$status === 1) ? 2 : 1;
        $this->db->update('tblrootcompany', ['status' => $new_status, 'dateupdated' => date('Y-m-d H:i:s')], ['id' => $id]);
        echo json_encode(['status' => 'success', 'new_status' => $new_status]);
    }

    public function GetCity()
    {
        $StateID = $this->input->post('StateID');
        $CityList = $this->clients_model->GetCityList($StateID);
        echo json_encode($CityList);
    }

    public function get_company_list()
    {
        $list = $this->company_master_model->get_all();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'data'   => $list
        ]);
        exit;
    }

    public function get_company($id = '')
    {
        $id = (int)$id;
        if ($id <= 0) {
            $id = (int)$this->input->post('id');
        }

        if ($id <= 0) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
            exit;
        }

        $company = $this->company_master_model->get_by_id($id);
        if (!$company) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Company not found']);
            exit;
        }

        $locations = $this->company_master_model->get_locations($id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'company' => $company,
            'locations' => $locations,
        ]);
        exit;
    }

    public function get_company_by_shortcode()
    {
        $short = trim((string)$this->input->post('comp_short'));
        if ($short === '') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Short code is required']);
            exit;
        }

        $short = strtoupper($short);

        $company = $this->company_master_model->get_by_shortcode($short);
        if (!$company) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'not_found', 'message' => 'No data found']);
            exit;
        }

        $locations = $this->company_master_model->get_locations((int)$company->id);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'success',
            'company' => $company,
            'locations' => $locations,
        ]);
        exit;
    }
}