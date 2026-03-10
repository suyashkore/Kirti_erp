<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Clients extends AdminController
{
    // KYC API Configuration
    const KYC_API_BEARER_TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo";

    /* List all clients */

    public function index()
    {



        if (staff_cant('view', 'customers')) {

            if (!have_assigned_customers() && staff_cant('create', 'customers')) {

                access_denied('customers');

            }

        }






        $this->load->model('contracts_model');

        $data['contract_types'] = $this->contracts_model->get_contract_types();

        $data['groups'] = $this->clients_model->get_groups();

        $data['title'] = _l('clients');



        $this->load->model('proposals_model');

        $data['proposal_statuses'] = $this->proposals_model->get_statuses();



        $this->load->model('invoices_model');

        $data['invoice_statuses'] = $this->invoices_model->get_statuses();



        $this->load->model('estimates_model');

        $data['estimate_statuses'] = $this->estimates_model->get_statuses();



        $this->load->model('projects_model');

        $data['project_statuses'] = $this->projects_model->get_project_statuses();



        $data['customer_admins'] = $this->clients_model->get_customers_admin_unique_ids();



        $whereContactsLoggedIn = '';

        if (staff_cant('view', 'customers')) {

            $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id() . ')';

        }



        $data['contacts_logged_in_today'] = $this->clients_model->get_contacts('', 'last_login LIKE "' . date('Y-m-d') . '%"' . $whereContactsLoggedIn);



        $data['countries'] = $this->clients_model->get_clients_distinct_countries();



        $data['table'] = App_table::find('clients');

        $this->load->view('admin/clients/manage', $data);

    }



    public function table()
    {

        if (staff_cant('view', 'customers')) {

            if (!have_assigned_customers() && staff_cant('create', 'customers')) {

                ajax_access_denied();

            }

        }



        App_table::find('clients')->output();

    }



    public function all_contacts()
    {

        if ($this->input->is_ajax_request()) {

            $this->app->get_table_data('all_contacts');

        }



        if (is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1') {

            $this->load->model('gdpr_model');

            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();

        }



        $data['title'] = _l('customer_contacts');

        $this->load->view('admin/clients/all_contacts', $data);

    }



    /* Edit client or add new client*/

    public function client($id = '')
    {

        if (staff_cant('view', 'customers')) {

            if ($id != '' && !is_customer_admin($id)) {

                access_denied('customers');

            }

        }



        if ($this->input->post() && !$this->input->is_ajax_request()) {

            if ($id == '') {

                if (staff_cant('create', 'customers')) {

                    access_denied('customers');

                }



                $data = $this->input->post();



                $save_and_add_contact = false;

                if (isset($data['save_and_add_contact'])) {

                    unset($data['save_and_add_contact']);

                    $save_and_add_contact = true;

                }

                $id = $this->clients_model->add($data);

                if (staff_cant('view', 'customers')) {

                    $assign['customer_admins'] = [];

                    $assign['customer_admins'][] = get_staff_user_id();

                    $this->clients_model->assign_admins($assign, $id);

                }

                if ($id) {

                    set_alert('success', _l('added_successfully', _l('client')));

                    if ($save_and_add_contact == false) {

                        redirect(admin_url('clients/client/' . $id));

                    } else {

                        redirect(admin_url('clients/client/' . $id . '?group=contacts&new_contact=true'));

                    }

                }

            } else {

                if (staff_cant('edit', 'customers')) {

                    if (!is_customer_admin($id)) {

                        access_denied('customers');

                    }

                }

                $success = $this->clients_model->update($this->input->post(), $id);

                if ($success == true) {

                    set_alert('success', _l('updated_successfully', _l('client')));

                }

                redirect(admin_url('clients/client/' . $id));

            }

        }



        $group = !$this->input->get('group') ? 'profile' : $this->input->get('group');

        $data['group'] = $group;



        if ($group != 'contacts' && $contact_id = $this->input->get('contactid')) {

            redirect(admin_url('clients/client/' . $id . '?group=contacts&contactid=' . $contact_id));

        }



        // Customer groups

        $data['groups'] = $this->clients_model->get_groups();



        if ($id == '') {

            $title = _l('add_new', _l('client'));

        } else {

            $client = $this->clients_model->get($id);

            $data['customer_tabs'] = get_customer_profile_tabs($id);



            if (!$client) {

                show_404();

            }



            $data['contacts'] = $this->clients_model->get_contacts($id);

            $data['tab'] = isset($data['customer_tabs'][$group]) ? $data['customer_tabs'][$group] : null;



            if (!$data['tab']) {

                show_404();

            }



            // Fetch data based on groups

            if ($group == 'profile') {

                $data['customer_groups'] = $this->clients_model->get_customer_groups($id);

                $data['customer_admins'] = $this->clients_model->get_admins($id);

            } elseif ($group == 'attachments') {

                $data['attachments'] = get_all_customer_attachments($id);

            } elseif ($group == 'vault') {

                $data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));



                if ($data['vault_entries'] === -1) {

                    $data['vault_entries'] = [];

                }

            } elseif ($group == 'estimates') {

                $this->load->model('estimates_model');

                $data['estimate_statuses'] = $this->estimates_model->get_statuses();

            } elseif ($group == 'invoices') {

                $this->load->model('invoices_model');

                $data['invoice_statuses'] = $this->invoices_model->get_statuses();

            } elseif ($group == 'credit_notes') {

                $this->load->model('credit_notes_model');

                $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();

                $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($id);

            } elseif ($group == 'payments') {

                $this->load->model('payment_modes_model');

                $data['payment_modes'] = $this->payment_modes_model->get();

            } elseif ($group == 'notes') {

                $data['user_notes'] = $this->misc_model->get_notes($id, 'customer');

            } elseif ($group == 'projects') {

                $this->load->model('projects_model');

                $data['project_statuses'] = $this->projects_model->get_project_statuses();

            } elseif ($group == 'statement') {

                if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {

                    set_alert('danger', _l('access_denied'));

                    redirect(admin_url('clients/client/' . $id));

                }



                $data = array_merge($data, prepare_mail_preview_data('customer_statement', $id));

            } elseif ($group == 'map') {

                if (get_option('google_api_key') != '' && !empty($client->latitude) && !empty($client->longitude)) {

                    $this->app_scripts->add('map-js', base_url($this->app_scripts->core_file('assets/js', 'map.js')) . '?v=' . $this->app_css->core_version());



                    $this->app_scripts->add('google-maps-api-js', [

                        'path' => 'https://maps.googleapis.com/maps/api/js?key=' . get_option('google_api_key') . '&callback=initMap',

                        'attributes' => [

                            'async',

                            'defer',

                            'latitude' => "$client->latitude",

                            'longitude' => "$client->longitude",

                            'mapMarkerTitle' => "$client->company",

                        ],

                    ]);

                }

            }



            $data['staff'] = $this->staff_model->get('', ['active' => 1]);



            $data['client'] = $client;

            $title = $client->company;



            // Get all active staff members (used to add reminder)

            $data['members'] = $data['staff'];



            if (!empty($data['client']->company)) {

                // Check if is realy empty client company so we can set this field to empty

                // The query where fetch the client auto populate firstname and lastname if company is empty

                if (is_empty_customer_company($data['client']->userid)) {

                    $data['client']->company = '';

                }

            }

        }



        $this->load->model('currencies_model');

        $data['currencies'] = $this->currencies_model->get();



        if ($id != '') {

            $customer_currency = $data['client']->default_currency;



            foreach ($data['currencies'] as $currency) {

                if ($customer_currency != 0) {

                    if ($currency['id'] == $customer_currency) {

                        $customer_currency = $currency;



                        break;

                    }

                } else {

                    if ($currency['isdefault'] == 1) {

                        $customer_currency = $currency;



                        break;

                    }

                }

            }



            if (is_array($customer_currency)) {

                $customer_currency = (object) $customer_currency;

            }



            $data['customer_currency'] = $customer_currency;



            $slug_zip_folder = (

                $client->company != ''

                ? $client->company

                : get_contact_full_name(get_primary_contact_user_id($client->userid))

            );



            $data['zip_in_folder'] = slug_it($slug_zip_folder);

        }



        $data['bodyclass'] = 'customer-profile dynamic-create-groups';

        $data['title'] = $title;



        $this->load->view('admin/clients/client', $data);

    }



    public function export($contact_id)
    {

        if (is_admin()) {

            $this->load->library('gdpr/gdpr_contact');

            $this->gdpr_contact->export($contact_id);

        }

    }



    // Used to give a tip to the user if the company exists when new company is created

    public function check_duplicate_customer_name()
    {

        if (staff_can('create', 'customers')) {

            $companyName = trim($this->input->post('company'));

            $response = [

                'exists' => (bool) total_rows(db_prefix() . 'clients', ['company' => $companyName]) > 0,

                'message' => _l('company_exists_info', '<b>' . $companyName . '</b>'),

            ];

            echo json_encode($response);

        }

    }



    public function save_longitude_and_latitude($client_id)
    {

        if (staff_cant('edit', 'customers')) {

            if (!is_customer_admin($client_id)) {

                ajax_access_denied();

            }

        }



        $this->db->where('userid', $client_id);

        $this->db->update(db_prefix() . 'clients', [

            'longitude' => $this->input->post('longitude'),

            'latitude' => $this->input->post('latitude'),

        ]);

        if ($this->db->affected_rows() > 0) {

            echo 'success';

        } else {

            echo 'false';

        }

    }



    public function form_contact($customer_id, $contact_id = '')
    {

        if (staff_cant('view', 'customers')) {

            if (!is_customer_admin($customer_id)) {

                echo _l('access_denied');

                die;

            }

        }

        $data['customer_id'] = $customer_id;

        $data['contactid'] = $contact_id;



        if (is_automatic_calling_codes_enabled()) {

            $clientCountryId = $this->db->select('country')

                ->where('userid', $customer_id)

                ->get('clients')->row()->country ?? null;



            $clientCountry = get_country($clientCountryId);



            $callingCode = $clientCountry && $clientCountry->calling_code ?

                ($clientCountry ? '+' . ltrim($clientCountry->calling_code, '+') : null) :

                null;

        } else {

            $callingCode = null;

        }



        if ($this->input->post()) {

            $data = $this->input->post();

            $data['password'] = $this->input->post('password', false);



            if ($callingCode && !empty($data['phonenumber']) && $data['phonenumber'] == $callingCode) {

                $data['phonenumber'] = '';

            }



            unset($data['contactid']);



            if ($contact_id == '') {

                if (staff_cant('create', 'customers')) {

                    if (!is_customer_admin($customer_id)) {

                        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

                        echo json_encode([

                            'success' => false,

                            'message' => _l('access_denied'),

                        ]);

                        die;

                    }

                }

                $id = $this->clients_model->add_contact($data, $customer_id);

                $message = '';

                $success = false;

                if ($id) {

                    handle_contact_profile_image_upload($id);

                    $success = true;

                    $message = _l('added_successfully', _l('contact'));

                }

                echo json_encode([

                    'success' => $success,

                    'message' => $message,

                    'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

                    'is_individual' => is_empty_customer_company($customer_id) && total_rows(db_prefix() . 'contacts', ['userid' => $customer_id]) == 1,

                ]);

                die;

            }

            if (staff_cant('edit', 'customers')) {

                if (!is_customer_admin($customer_id)) {

                    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

                    echo json_encode([

                        'success' => false,

                        'message' => _l('access_denied'),

                    ]);

                    die;

                }

            }

            $original_contact = $this->clients_model->get_contact($contact_id);

            $success = $this->clients_model->update_contact($data, $contact_id);

            $message = '';

            $proposal_warning = false;

            $original_email = '';

            $updated = false;

            if (is_array($success)) {

                if (isset($success['set_password_email_sent'])) {

                    $message = _l('set_password_email_sent_to_client');

                } elseif (isset($success['set_password_email_sent_and_profile_updated'])) {

                    $updated = true;

                    $message = _l('set_password_email_sent_to_client_and_profile_updated');

                }

            } else {

                if ($success == true) {

                    $updated = true;

                    $message = _l('updated_successfully', _l('contact'));

                }

            }

            if (handle_contact_profile_image_upload($contact_id) && !$updated) {

                $message = _l('updated_successfully', _l('contact'));

                $success = true;

            }

            if ($updated == true) {

                $contact = $this->clients_model->get_contact($contact_id);

                if (
                    total_rows(db_prefix() . 'proposals', [

                        'rel_type' => 'customer',

                        'rel_id' => $contact->userid,

                        'email' => $original_contact->email,

                    ]) > 0 && ($original_contact->email != $contact->email)
                ) {

                    $proposal_warning = true;

                    $original_email = $original_contact->email;

                }

            }

            echo json_encode([

                'success' => $success,

                'proposal_warning' => $proposal_warning,

                'message' => $message,

                'original_email' => $original_email,

                'has_primary_contact' => (total_rows(db_prefix() . 'contacts', ['userid' => $customer_id, 'is_primary' => 1]) > 0 ? true : false),

            ]);

            die;

        }





        $data['calling_code'] = $callingCode;



        if ($contact_id == '') {

            $title = _l('add_new', _l('contact'));

        } else {

            $data['contact'] = $this->clients_model->get_contact($contact_id);



            if (!$data['contact']) {

                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

                echo json_encode([

                    'success' => false,

                    'message' => 'Contact Not Found',

                ]);

                die;

            }

            $title = $data['contact']->firstname . ' ' . $data['contact']->lastname;

        }



        $data['customer_permissions'] = get_contact_permissions();

        $data['title'] = $title;

        $this->load->view('admin/clients/modals/contact', $data);

    }



    public function confirm_registration($client_id)
    {

        if (!is_admin()) {

            access_denied('Customer Confirm Registration, ID: ' . $client_id);

        }

        $this->clients_model->confirm_registration($client_id);

        set_alert('success', _l('customer_registration_successfully_confirmed'));

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);

    }



    public function update_file_share_visibility()
    {

        if ($this->input->post()) {

            $file_id = $this->input->post('file_id');

            $share_contacts_id = [];



            if ($this->input->post('share_contacts_id')) {

                $share_contacts_id = $this->input->post('share_contacts_id');

            }



            $this->db->where('file_id', $file_id);

            $this->db->delete(db_prefix() . 'shared_customer_files');



            foreach ($share_contacts_id as $share_contact_id) {

                $this->db->insert(db_prefix() . 'shared_customer_files', [

                    'file_id' => $file_id,

                    'contact_id' => $share_contact_id,

                ]);

            }

        }

    }



    public function delete_contact_profile_image($contact_id)
    {

        $this->clients_model->delete_contact_profile_image($contact_id);

    }



    public function mark_as_active($id)
    {

        $this->db->where('userid', $id);

        $this->db->update(db_prefix() . 'clients', [

            'active' => 1,

        ]);

        redirect(admin_url('clients/client/' . $id));

    }



    public function consents($id)
    {

        if (staff_cant('view', 'customers')) {

            if (!is_customer_admin(get_user_id_by_contact_id($id))) {

                echo _l('access_denied');

                die;

            }

        }



        $this->load->model('gdpr_model');

        $data['purposes'] = $this->gdpr_model->get_consent_purposes($id, 'contact');

        $data['consents'] = $this->gdpr_model->get_consents(['contact_id' => $id]);

        $data['contact_id'] = $id;

        $this->load->view('admin/gdpr/contact_consent', $data);

    }



    public function update_all_proposal_emails_linked_to_customer($contact_id)
    {

        $success = false;

        $email = '';

        if ($this->input->post('update')) {

            $this->load->model('proposals_model');



            $this->db->select('email,userid');

            $this->db->where('id', $contact_id);

            $contact = $this->db->get(db_prefix() . 'contacts')->row();



            $proposals = $this->proposals_model->get('', [

                'rel_type' => 'customer',

                'rel_id' => $contact->userid,

                'email' => $this->input->post('original_email'),

            ]);

            $affected_rows = 0;



            foreach ($proposals as $proposal) {

                $this->db->where('id', $proposal['id']);

                $this->db->update(db_prefix() . 'proposals', [

                    'email' => $contact->email,

                ]);

                if ($this->db->affected_rows() > 0) {

                    $affected_rows++;

                }

            }



            if ($affected_rows > 0) {

                $success = true;

            }

        }

        echo json_encode([

            'success' => $success,

            'message' => _l('proposals_emails_updated', [

                _l('contact_lowercase'),

                $contact->email,

            ]),

        ]);

    }



    public function assign_admins($id)
    {

        if (staff_cant('create', 'customers') && staff_cant('edit', 'customers')) {

            access_denied('customers');

        }

        $success = $this->clients_model->assign_admins($this->input->post(), $id);

        if ($success == true) {

            set_alert('success', _l('updated_successfully', _l('client')));

        }



        redirect(admin_url('clients/client/' . $id . '?tab=customer_admins'));

    }



    public function delete_customer_admin($customer_id, $staff_id)
    {

        if (staff_cant('create', 'customers') && staff_cant('edit', 'customers')) {

            access_denied('customers');

        }



        $this->db->where('customer_id', $customer_id);

        $this->db->where('staff_id', $staff_id);

        $this->db->delete(db_prefix() . 'customer_admins');

        redirect(admin_url('clients/client/' . $customer_id) . '?tab=customer_admins');

    }



    public function delete_contact($customer_id, $id)
    {

        if (staff_cant('delete', 'customers')) {

            if (!is_customer_admin($customer_id)) {

                access_denied('customers');

            }

        }

        $contact = $this->clients_model->get_contact($id);

        $hasProposals = false;

        if ($contact && is_gdpr()) {

            if (total_rows(db_prefix() . 'proposals', ['email' => $contact->email]) > 0) {

                $hasProposals = true;

            }

        }



        $this->clients_model->delete_contact($id);

        if ($hasProposals) {

            $this->session->set_flashdata('gdpr_delete_warning', true);

        }

        redirect(admin_url('clients/client/' . $customer_id . '?group=contacts'));

    }



    public function contacts($client_id)
    {

        $this->app->get_table_data('contacts', [

            'client_id' => $client_id,

        ]);

    }



    public function upload_attachment($id)
    {

        handle_client_attachments_upload($id);

    }



    public function add_external_attachment()
    {

        if ($this->input->post()) {

            $this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'customer', $this->input->post('files'), $this->input->post('external'));

        }

    }



    public function delete_attachment($customer_id, $id)
    {

        if (staff_can('delete', 'customers') || is_customer_admin($customer_id)) {

            $this->clients_model->delete_attachment($id);

        }

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);

    }



    /* Delete client */

    public function delete($id)
    {

        if (staff_cant('delete', 'customers')) {

            access_denied('customers');

        }

        if (!$id) {

            redirect(admin_url('clients'));

        }

        $response = $this->clients_model->delete($id);

        if (is_array($response) && isset($response['referenced'])) {

            set_alert('warning', _l('customer_delete_transactions_warning', _l('invoices') . ', ' . _l('estimates') . ', ' . _l('credit_notes')));

        } elseif ($response == true) {

            set_alert('success', _l('deleted', _l('client')));

        } else {

            set_alert('warning', _l('problem_deleting', _l('client_lowercase')));

        }

        redirect(admin_url('clients'));

    }



    /* Staff can login as client */

    public function login_as_client($id)
    {

        if (is_admin()) {

            login_as_client($id);

        }

        hooks()->do_action('after_contact_login');

        redirect(site_url());

    }



    public function get_customer_billing_and_shipping_details($id)
    {

        echo json_encode($this->clients_model->get_customer_billing_and_shipping_details($id));

    }



    /* Change client status / active / inactive */

    public function change_contact_status($id, $status)
    {

        if (staff_can('edit', 'customers') || is_customer_admin(get_user_id_by_contact_id($id))) {

            if ($this->input->is_ajax_request()) {

                $this->clients_model->change_contact_status($id, $status);

            }

        }

    }



    /* Change client status / active / inactive */

    public function change_client_status($id, $status)
    {

        if ($this->input->is_ajax_request()) {

            $this->clients_model->change_client_status($id, $status);

        }

    }



    /* Zip function for credit notes */

    public function zip_credit_notes($id)
    {

        $has_permission_view = staff_can('view', 'credit_notes');



        if (!$has_permission_view && staff_cant('view_own', 'credit_notes')) {

            access_denied('Zip Customer Credit Notes');

        }



        if ($this->input->post()) {

            $this->load->library('app_bulk_pdf_export', [

                'export_type' => 'credit_notes',

                'status' => $this->input->post('credit_note_zip_status'),

                'date_from' => $this->input->post('zip-from'),

                'date_to' => $this->input->post('zip-to'),

                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=credit_notes'),

            ]);



            $this->app_bulk_pdf_export->set_client_id($id);

            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

            $this->app_bulk_pdf_export->export();

        }

    }



    public function zip_invoices($id)
    {

        $has_permission_view = staff_can('view', 'invoices');

        if (
            !$has_permission_view && staff_cant('view_own', 'invoices')

            && get_option('allow_staff_view_invoices_assigned') == '0'
        ) {

            access_denied('Zip Customer Invoices');

        }



        if ($this->input->post()) {

            $this->load->library('app_bulk_pdf_export', [

                'export_type' => 'invoices',

                'status' => $this->input->post('invoice_zip_status'),

                'date_from' => $this->input->post('zip-from'),

                'date_to' => $this->input->post('zip-to'),

                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=invoices'),

            ]);



            $this->app_bulk_pdf_export->set_client_id($id);

            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

            $this->app_bulk_pdf_export->export();

        }

    }



    /* Since version 1.0.2 zip client estimates */

    public function zip_estimates($id)
    {

        $has_permission_view = staff_can('view', 'estimates');

        if (
            !$has_permission_view && staff_cant('view_own', 'estimates')

            && get_option('allow_staff_view_estimates_assigned') == '0'
        ) {

            access_denied('Zip Customer Estimates');

        }



        if ($this->input->post()) {

            $this->load->library('app_bulk_pdf_export', [

                'export_type' => 'estimates',

                'status' => $this->input->post('estimate_zip_status'),

                'date_from' => $this->input->post('zip-from'),

                'date_to' => $this->input->post('zip-to'),

                'redirect_on_error' => admin_url('clients/client/' . $id . '?group=estimates'),

            ]);



            $this->app_bulk_pdf_export->set_client_id($id);

            $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

            $this->app_bulk_pdf_export->export();

        }

    }



    public function zip_payments($id)
    {

        $has_permission_view = staff_can('view', 'payments');



        if (
            !$has_permission_view && staff_cant('view_own', 'invoices')

            && get_option('allow_staff_view_invoices_assigned') == '0'
        ) {

            access_denied('Zip Customer Payments');

        }



        $this->load->library('app_bulk_pdf_export', [

            'export_type' => 'payments',

            'payment_mode' => $this->input->post('paymentmode'),

            'date_from' => $this->input->post('zip-from'),

            'date_to' => $this->input->post('zip-to'),

            'redirect_on_error' => admin_url('clients/client/' . $id . '?group=payments'),

        ]);



        $this->app_bulk_pdf_export->set_client_id($id);

        $this->app_bulk_pdf_export->set_client_id_column(db_prefix() . 'clients.userid');

        $this->app_bulk_pdf_export->in_folder($this->input->post('file_name'));

        $this->app_bulk_pdf_export->export();

    }



    public function import()
    {

        if (staff_cant('create', 'customers')) {

            access_denied('customers');

        }



        $dbFields = $this->db->list_fields(db_prefix() . 'contacts');

        foreach ($dbFields as $key => $contactField) {

            if ($contactField == 'phonenumber') {

                $dbFields[$key] = 'contact_phonenumber';

            }

        }



        $dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix() . 'clients'));



        $this->load->library('import/import_customers', [], 'import');



        $this->import->setDatabaseFields($dbFields)

            ->setCustomFields(get_custom_fields('customers'));



        if ($this->input->post('download_sample') === 'true') {

            $this->import->downloadSample();

        }



        if (
            $this->input->post()

            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''
        ) {

            $this->import->setSimulation($this->input->post('simulate'))

                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])

                ->setFilename($_FILES['file_csv']['name'])

                ->perform();





            $data['total_rows_post'] = $this->import->totalRows();



            if (!$this->import->isSimulation()) {

                set_alert('success', _l('import_total_imported', $this->import->totalImported()));

            }

        }



        $data['groups'] = $this->clients_model->get_groups();

        $data['title'] = _l('import');

        $data['bodyclass'] = 'dynamic-create-groups';

        $this->load->view('admin/clients/import', $data);

    }



    public function groups()
    {

        if (!is_admin()) {

            access_denied('Customer Groups');

        }

        if ($this->input->is_ajax_request()) {

            $this->app->get_table_data('customers_groups');

        }

        $data['title'] = _l('customer_groups');

        $this->load->view('admin/clients/groups_manage', $data);

    }



    public function group()
    {

        if (!is_admin() && get_option('staff_members_create_inline_customer_groups') == '0') {

            access_denied('Customer Groups');

        }



        if ($this->input->is_ajax_request()) {

            $data = $this->input->post();

            if ($data['id'] == '') {

                $id = $this->clients_model->add_group($data);

                $message = $id ? _l('added_successfully', _l('customer_group')) : '';

                echo json_encode([

                    'success' => $id ? true : false,

                    'message' => $message,

                    'id' => $id,

                    'name' => $data['name'],

                ]);

            } else {

                $success = $this->clients_model->edit_group($data);

                $message = '';

                if ($success == true) {

                    $message = _l('updated_successfully', _l('customer_group'));

                }

                echo json_encode([

                    'success' => $success,

                    'message' => $message,

                ]);

            }

        }

    }



    public function delete_group($id)
    {

        if (!is_admin()) {

            access_denied('Delete Customer Group');

        }

        if (!$id) {

            redirect(admin_url('clients/groups'));

        }

        $response = $this->clients_model->delete_group($id);

        if ($response == true) {

            set_alert('success', _l('deleted', _l('customer_group')));

        } else {

            set_alert('warning', _l('problem_deleting', _l('customer_group_lowercase')));

        }

        redirect(admin_url('clients/groups'));

    }



    public function bulk_action()
    {

        hooks()->do_action('before_do_bulk_action_for_customers');

        $total_deleted = 0;

        if ($this->input->post()) {

            $ids = $this->input->post('ids');

            $groups = $this->input->post('groups');



            if (is_array($ids)) {

                foreach ($ids as $id) {

                    if ($this->input->post('mass_delete')) {

                        if ($this->clients_model->delete($id)) {

                            $total_deleted++;

                        }

                    } else {

                        if (!is_array($groups)) {

                            $groups = false;

                        }

                        $this->client_groups_model->sync_customer_groups($id, $groups);

                    }

                }

            }

        }



        if ($this->input->post('mass_delete')) {

            set_alert('success', _l('total_clients_deleted', $total_deleted));

        }

    }



    public function vault_entry_create($customer_id)
    {

        $data = $this->input->post();



        if (isset($data['fakeusernameremembered'])) {

            unset($data['fakeusernameremembered']);

        }



        if (isset($data['fakepasswordremembered'])) {

            unset($data['fakepasswordremembered']);

        }



        unset($data['id']);

        $data['creator'] = get_staff_user_id();

        $data['creator_name'] = get_staff_full_name($data['creator']);

        $data['description'] = nl2br($data['description']);

        $data['password'] = $this->encryption->encrypt($this->input->post('password', false));



        if (empty($data['port'])) {

            unset($data['port']);

        }



        $this->clients_model->vault_entry_create($data, $customer_id);

        set_alert('success', _l('added_successfully', _l('vault_entry')));

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);

    }



    public function vault_entry_update($entry_id)
    {

        $entry = $this->clients_model->get_vault_entry($entry_id);



        if ($entry->creator == get_staff_user_id() || is_admin()) {

            $data = $this->input->post();



            if (isset($data['fakeusernameremembered'])) {

                unset($data['fakeusernameremembered']);

            }

            if (isset($data['fakepasswordremembered'])) {

                unset($data['fakepasswordremembered']);

            }



            $data['last_updated_from'] = get_staff_full_name(get_staff_user_id());

            $data['description'] = nl2br($data['description']);



            if (!empty($data['password'])) {

                $data['password'] = $this->encryption->encrypt($this->input->post('password', false));

            } else {

                unset($data['password']);

            }



            if (empty($data['port'])) {

                unset($data['port']);

            }



            $this->clients_model->vault_entry_update($entry_id, $data);

            set_alert('success', _l('updated_successfully', _l('vault_entry')));

        }

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);

    }



    public function vault_entry_delete($id)
    {

        $entry = $this->clients_model->get_vault_entry($id);

        if ($entry->creator == get_staff_user_id() || is_admin()) {

            $this->clients_model->vault_entry_delete($id);

        }

        redirect(previous_url() ?: $_SERVER['HTTP_REFERER']);

    }



    public function vault_encrypt_password()
    {

        $id = $this->input->post('id');

        $user_password = $this->input->post('user_password', false);

        $user = $this->staff_model->get(get_staff_user_id());



        if (!app_hasher()->CheckPassword($user_password, $user->password)) {

            header('HTTP/1.1 401 Unauthorized');

            echo json_encode(['error_msg' => _l('vault_password_user_not_correct')]);

            die;

        }



        $vault = $this->clients_model->get_vault_entry($id);

        $password = $this->encryption->decrypt($vault->password);



        $password = html_escape($password);



        // Failed to decrypt

        if (!$password) {

            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

            echo json_encode(['error_msg' => _l('failed_to_decrypt_password')]);

            die;

        }



        echo json_encode(['password' => $password]);

    }



    public function get_vault_entry($id)
    {

        $entry = $this->clients_model->get_vault_entry($id);

        unset($entry->password);

        $entry->description = clear_textarea_breaks($entry->description);

        echo json_encode($entry);

    }



    public function statement_pdf()
    {

        $customer_id = $this->input->get('customer_id');



        if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {

            set_alert('danger', _l('access_denied'));

            redirect(admin_url('clients/client/' . $customer_id));

        }



        $from = $this->input->get('from');

        $to = $this->input->get('to');



        $data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));



        try {

            $pdf = statement_pdf($data['statement']);

        } catch (Exception $e) {

            $message = $e->getMessage();

            echo $message;

            if (strpos($message, 'Unable to get the size of the image') !== false) {

                show_pdf_unable_to_get_image_size_error();

            }

            die;

        }



        $type = 'D';

        if ($this->input->get('print')) {

            $type = 'I';

        }



        $pdf->Output(slug_it(_l('customer_statement') . '-' . $data['statement']['client']->company) . '.pdf', $type);

    }



    public function send_statement()
    {

        $customer_id = $this->input->get('customer_id');



        if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {

            set_alert('danger', _l('access_denied'));

            redirect(admin_url('clients/client/' . $customer_id));

        }



        $from = $this->input->get('from');

        $to = $this->input->get('to');



        $send_to = $this->input->post('send_to');

        $cc = $this->input->post('cc');



        $success = $this->clients_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);

        // In case client use another language

        load_admin_language();

        if ($success) {

            set_alert('success', _l('statement_sent_to_client_success'));

        } else {

            set_alert('danger', _l('statement_sent_to_client_fail'));

        }



        redirect(admin_url('clients/client/' . $customer_id . '?group=statement'));

    }



    public function statement()
    {

        if (staff_cant('view', 'invoices') && staff_cant('view', 'payments')) {

            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad error');

            echo _l('access_denied');

            die;

        }



        $customer_id = $this->input->get('customer_id');

        $from = $this->input->get('from');

        $to = $this->input->get('to');



        $data['statement'] = $this->clients_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));



        $data['from'] = $from;

        $data['to'] = $to;



        $viewData['html'] = $this->load->view('admin/clients/groups/_statement', $data, true);



        echo json_encode($viewData);

    }

    /* Get City List by State ID / ajax */

    public function GetCity()
    {

        $StateID = $this->input->post('StateID');

        $CityList = $this->clients_model->GetCityList($StateID);

        echo json_encode($CityList);

    }

    public function GetRoutePoints()
    {
        $routes = $this->input->post('routes');
        if (!empty($routes)) {
            $RoutePoints = $this->clients_model->GetRoutePoints($routes);
            echo json_encode($RoutePoints);
        } else {
            echo json_encode([]);
        }
    }




    public function client_add()
    {

        if (!has_permission_new('customers', '', 'view')) {

            if ($id != '' && !is_customer_admin($id)) {

                access_denied('customers');

            }

        }



        if ($this->input->post() && !$this->input->is_ajax_request()) {



            if (!has_permission_new('customers', '', 'create')) {

                access_denied('customers');

            }

            $data = $this->input->post();

            $AccountID = trim($data["AccountID"]);



            $Account_details_code = $this->clients_model->check_AccountID($AccountID);

            if ($Account_details_code == true) {

                set_alert('warning', "AccountID already exists");

                redirect(admin_url('clients/client'));

            }



            $rootcompany = $this->clients_model->get_rootcompany();

            $itemdivision = $this->clients_model->get_itemDivision();





            $itemDivision = array();

            foreach ($itemdivision as $item_Division) {



                $item_division_id = "itemdiv" . $item_Division["id"];

                $Selected_item_division_ids = $data[$item_division_id];

                if ($Selected_item_division_ids == $item_Division["id"]) {



                    array_push($itemDivision, $item_Division["id"]);

                    unset($data[$item_division_id]);

                }

            }







            $itemDivision_comp = array();

            foreach ($itemdivision as $item_Division) {



                $item_division_comp_id = "itemdivisioncomp" . $item_Division["id"];

                $Selected_item_division_comp_ids = $data[$item_division_comp_id];



                if ($Selected_item_division_comp_ids !== "") {



                    $itemDivision_comp[$item_Division["id"]] = $data[$item_division_comp_id];

                    unset($data[$item_division_comp_id]);

                } else {

                    unset($data[$item_division_comp_id]);

                }

            }



            /*echo "<pre>";



            print_r($data['customer_admins']);

            die;*/

            $company_assigned = array();

            foreach ($rootcompany as $r_company) {



                $company_assigned_id = "company_assigned" . $r_company["id"];

                $Selected_company_assigned_ids = $data[$company_assigned_id];

                if ($Selected_company_assigned_ids == $r_company["id"]) {



                    array_push($company_assigned, $r_company["id"]);

                    unset($data[$company_assigned_id]);

                }

            }



            $company_assigned_sales_p = array();

            $company_sales_person_assign_data = array();

            foreach ($rootcompany as $r_company) {



                $company_assigned_sales_p_id = "company_assigned_staff" . $r_company["id"];

                $Selected_company_assigned_sales_p_ids = $data[$company_assigned_sales_p_id];

                //print_r($Selected_company_assigned_sales_p_ids);



                if ($Selected_company_assigned_sales_p_ids !== "") {



                    foreach ($Selected_company_assigned_sales_p_ids as $assigned_ids) {

                        $company_assigned_sales_p[$r_company["id"]] = $assigned_ids;

                        $company_assigned_sales_p_id = "company_assigned_staff" . $r_company["id"];



                        $company_sales_person_assign_data[$r_company["id"]] = $data[$company_assigned_sales_p_id];

                    }



                    unset($data[$company_assigned_sales_p_id]);

                } else {

                    unset($data[$company_assigned_sales_p_id]);

                }

            }









            $company_assigned_opn_bal = array();

            foreach ($rootcompany as $r_company) {



                $company_assigned_opn_bal_id = "opening_bal" . $r_company["id"];

                $Selected_company_assigned_opn_bal_ids = $data[$company_assigned_opn_bal_id];



                if ($Selected_company_assigned_opn_bal_ids !== "") {



                    $company_assigned_opn_bal[$r_company["id"]] = $data[$company_assigned_opn_bal_id];

                    unset($data[$company_assigned_opn_bal_id]);

                } else {

                    unset($data[$company_assigned_opn_bal_id]);

                }

            }



            $company_assigned_drcr = array();

            foreach ($rootcompany as $r_company) {



                $company_assigned_drcr_id = "drcr" . $r_company["id"];

                //$Selected_company_assigned_drcr_ids = $data[$company_assigned_drcr_id];



                if ($data[$company_assigned_drcr_id] !== "") {



                    $company_assigned_drcr[$r_company["id"]] = $data[$company_assigned_drcr_id];

                    unset($data[$company_assigned_drcr_id]);

                } else {

                    unset($data[$company_assigned_drcr_id]);

                }

            }



            $newcompany_assigned_sales_p = array();

            $newcompany_assigned_sales_p = array_filter($company_assigned_sales_p, 'strlen');



            /*print_r($company_assigned_sales_p);

            //print_r($company_assigned);

            die;*/



            $data["itemdivision"] = serialize($itemDivision);

            $data["itemdivision_comp"] = serialize($itemDivision_comp);



            $data["company_assigned"] = serialize($company_assigned);

            $data["company_assigned_staff"] = serialize($company_assigned_sales_p);

            $data["opening_bal"] = serialize($company_assigned_opn_bal);

            $data["drcr"] = serialize($company_assigned_drcr);

            /*echo "<pre>";

            print_r($data["company_assigned_staff"]);

            print_r($data);

            die;*/

            $contacts_fields["kms"] = $data['kms'];

            unset($data['kms']);



            $contacts_fields["FLNO1"] = $data['FLNO1'];

            unset($data['FLNO1']);



            $contacts_fields["Pan"] = $data['Pan'];

            unset($data['Pan']);



            $contacts_fields["Aadhaarno"] = $data['Aadhaarno'];

            unset($data['Aadhaarno']);



            $contacts_fields["istcs"] = $data['istcs'];

            unset($data['istcs']);



            $contacts_fields["TcsStartDate"] = $data['TcsStartDate'];

            unset($data['TcsStartDate']);



            /*$contacts_fields["TcsStartDate"] = $data['TcsStartDate'];

            unset($data['TcsStartDate']);*/



            $contacts_fields["BalancesYN"] = $data['BalancesYN'];

            unset($data['BalancesYN']);



            $contacts_fields["BalancelYN"] = $data['BalancelYN'];

            unset($data['BalancelYN']);



            if (isset($data['profile_image'])) {

                $contacts_fields["profile_image"] = $data['profile_image'];

                unset($data['profile_image']);

            }



            if (isset($data['title'])) {

                $contacts_fields["title"] = $data['title'];

                unset($data['title']);

            }

            if (isset($data['firstname'])) {

                $contacts_fields["firstname"] = $data['firstname'];

                unset($data['firstname']);

            }

            if (isset($data['lastname'])) {

                $contacts_fields["lastname"] = $data['lastname'];

                unset($data['lastname']);

            }



            if (isset($data['email'])) {

                $contacts_fields["email"] = $data['email'];

                unset($data['email']);

            }



            if (isset($data['save_and_add_contact'])) {

                unset($data['save_and_add_contact']);

                $save_and_add_contact = true;

            }

            if (isset($data['profile_image'])) {

                $contacts_fields["profile_image"] = $data['profile_image'];

                unset($data['profile_image']);

            }



            if (isset($data['title'])) {

                $contacts_fields["title"] = $data['title'];

                unset($data['title']);

            }

            if (isset($data['firstname'])) {

                $contacts_fields["firstname"] = $data['firstname'];

                unset($data['firstname']);

            }

            if (isset($data['lastname'])) {

                $contacts_fields["lastname"] = $data['lastname'];

                unset($data['lastname']);

            }



            if (isset($data['email'])) {

                $contacts_fields["email"] = $data['email'];

                unset($data['email']);

            }

            if (isset($data['phonenumber'])) {

                $contacts_fields["phonenumber"] = $data['phonenumber'];

                //unset($data['phonenumber']);

            }







            //client table array



            if (isset($data['AccountID'])) {

                $client_fields["AccountID"] = $data['AccountID'];

                $account_id = $data['AccountID'];

                unset($data['AccountID']);

            }

            if (isset($data['CtrlAccountID'])) {

                $client_fields["CtrlAccountID"] = $data['CtrlAccountID'];

                unset($data['CtrlAccountID']);

            }



            if (isset($data['company'])) {

                $client_fields["company"] = $data['company'];

                unset($data['company']);

            }



            if (isset($data['city'])) {

                $client_fields["city"] = $data['city'];

                unset($data['city']);

            }



            if (isset($data['city'])) {

                $client_fields["city"] = $data['city'];

                unset($data['city']);

            }



            if (isset($data['address'])) {

                $client_fields["address"] = $data['address'];

                unset($data['address']);

            }



            if (isset($data['Address3'])) {

                $client_fields["Address3"] = $data['Address3'];

                unset($data['Address3']);

            }



            if (isset($data['state'])) {

                $client_fields["state"] = $data['state'];

                unset($data['state']);

            }



            if (isset($data['zip'])) {

                $client_fields["zip"] = $data['zip'];

                unset($data['zip']);

            }



            if (isset($data['groups_in'])) {

                $client_fields["DistributorType"] = $data['groups_in'];

                unset($data['groups_in']);

            }



            if (isset($data['MaxCrdAmt'])) {

                $client_fields["MaxCrdAmt"] = $data['MaxCrdAmt'];

                unset($data['MaxCrdAmt']);

            }



            if (isset($data['MaxDays'])) {

                $client_fields["MaxDays"] = $data['MaxDays'];

                unset($data['MaxDays']);

            }



            if (isset($data['ActSalestype'])) {

                $client_fields["ActSalestype"] = $data['ActSalestype'];

                unset($data['ActSalestype']);

            }



            if (isset($data['SalesFrequency'])) {

                $client_fields["SalesFrequency"] = $data['SalesFrequency'];

                unset($data['SalesFrequency']);

            }



            if (isset($data['Blockyn'])) {

                $client_fields["Blockyn"] = $data['Blockyn'];

                unset($data['Blockyn']);

            }



            if (isset($data['phonenumber'])) {

                $client_fields["phonenumber"] = $data['phonenumber'];

                //unset($data['Blockyn']);

            }



            if (isset($data['altphonenumber'])) {

                $client_fields["altphonenumber"] = $data['altphonenumber'];

                //unset($data['Blockyn']);

            }

            if (isset($data['website'])) {

                $client_fields["website"] = $data['website'];

                unset($data['website']);

            }

            if (isset($data['bill_till_bal'])) {

                $client_fields["bill_till_bal"] = $data['bill_till_bal'];

                unset($data['bill_till_bal']);

            }



            if (isset($data['billing_street'])) {

                $client_fields["billing_street"] = $data['billing_street'];

                unset($data['billing_street']);

            }



            if (isset($data['billing_city'])) {

                $client_fields["billing_city"] = $data['billing_city'];

                unset($data['billing_city']);

            }

            if (isset($data['billing_state'])) {

                $client_fields["billing_state"] = $data['billing_state'];

                unset($data['billing_state']);

            }



            if (isset($data['billing_zip'])) {

                $client_fields["billing_zip"] = $data['billing_zip'];

                unset($data['billing_zip']);

            }

            if (isset($data['billing_country'])) {

                $client_fields["billing_country"] = $data['billing_country'];

                unset($data['billing_country']);

            }

            if (isset($data['shipping_street'])) {

                $client_fields["shipping_street"] = $data['shipping_street'];

                unset($data['shipping_street']);

            }

            if (isset($data['shipping_city'])) {

                $client_fields["shipping_city"] = $data['shipping_city'];

                unset($data['shipping_city']);

            }

            if (isset($data['shipping_state'])) {

                $client_fields["shipping_state"] = $data['shipping_state'];

                unset($data['shipping_state']);

            }

            if (isset($data['shipping_zip'])) {

                $client_fields["shipping_zip"] = $data['shipping_zip'];

                unset($data['shipping_zip']);

            }



            if (isset($data['shipping_country'])) {

                $client_fields["shipping_country"] = $data['shipping_country'];

                unset($data['shipping_country']);

            }



            if (isset($data['vat'])) {

                $client_fields["vat"] = $data['vat'];

                unset($data['vat']);

            }



            if (isset($data['company_assigned'])) {

                $client_fields["company_assigned"] = $data['company_assigned'];

                unset($data['company_assigned']);

            }

            if (isset($data['company_assigned_staff'])) {

                $client_fields["company_assigned_staff"] = $data['company_assigned_staff'];

                unset($data['company_assigned_staff']);

            }

            if (isset($data['country'])) {

                $client_fields["country"] = $data['country'];

                unset($data['country']);

            }



            if (isset($data['country'])) {

                $client_fields["country"] = $data['country'];

                unset($data['country']);

            }

            if (isset($data['default_currency'])) {

                $client_fields["default_currency"] = $data['default_currency'];

                unset($data['default_currency']);

            }



            if (isset($data['StationName'])) {

                $client_fields["StationName"] = $data['StationName'];

                unset($data['StationName']);

            }

            if (isset($data['location_type'])) {

                $LocationTypeID = $data['location_type'];

                unset($data['location_type']);

            }





            // echo "<pre>";



            //print_r($data);

            //print_r($client_fields);

            //print_r($contacts_fields);

            //print_r($itemDivision_comp);

            /* print_r($company_assigned_opn_bal);

             print_r($company_assigned_drcr);

             print_r($company_assigned_sales_p);

             echo $company_assigned_drcr[1];

             foreach($company_assigned_drcr as $key1 => $value1){



                 echo $company_assigned_drcr[$key1];

                 echo "<br>";

             }*/

            $routes = $data["route"];

            unset($data["route"]);





            // die;

            $selected_company = $this->session->userdata('root_company');

            $fy = $this->session->userdata('finacial_year');

            foreach ($rootcompany as $r_company) {

                $client_fields['addedfrom'] = get_staff_user_id();

                $client_fields['UserID2'] = get_staff_user_id();

                $client_fields["PlantID"] = $r_company["id"];

                $client_fields['StartDate'] = date('Y-m-d H:i:s');

                $client_fields['ActSubGroupID2'] = "60001004";

                $customer_id = $this->clients_model->add($client_fields);



                $this->db->insert(db_prefix() . 'accountlocations', [

                    'PlantID' => $r_company["id"],

                    'LocationTypeID' => $LocationTypeID,

                    'AccountID' => $account_id,

                ]);

            }

            if ($customer_id) {







                foreach ($itemDivision_comp as $key => $value) {

                    $this->db->insert(db_prefix() . 'accountitemdiv', [

                        'ItemDivID' => $key,

                        'PlantID' => $selected_company,

                        'plant_assign' => $value,

                        'AccountID' => $account_id,

                    ]);



                }



                foreach ($routes as $value) {

                    # code...

                    $route_data = array(

                        "PlantID" => $selected_company,

                        "AccountID" => $account_id,

                        "RouteID" => $value

                    );

                    $this->db->insert(db_prefix() . 'accountroutes', $route_data);

                }

                foreach ($rootcompany as $r_company) {

                    $contacts_fields["PlantID"] = $r_company["id"];

                    $contacts_fields["AccountID"] = $account_id;

                    $this->db->insert(db_prefix() . 'contacts', $contacts_fields);

                }



                foreach ($newcompany_assigned_sales_p as $key => $value) {





                    /*if($value){

                        foreach($value as $val){*/

                    //echo $key . " = >" .$val;

                    $this->db->insert(db_prefix() . 'customer_admins', [

                        'customer_id' => $account_id,

                        'staff_id' => $value,

                        'company_id' => $key,

                        'date_assigned' => date('d-m-Y H:i:s'),

                    ]);

                    /* }

                 }*/



                }

                $bal_array = array();

                $non_bal_array = array();

                foreach ($company_assigned_opn_bal as $key1 => $value1) {



                    $value_type = $company_assigned_drcr[$key1];

                    array_push($bal_array, $key1);



                    if ($value_type == "DR") {

                        $value1 = "-" . $value1;

                    }



                    $this->db->insert(db_prefix() . 'accountbalances', [

                        'PlantID' => $key1,

                        'FY' => $fy,

                        'AccountID' => $account_id,

                        'BAL1' => $value1,

                        'UserID2' => get_staff_user_id(),

                    ]);

                }



                foreach ($company_assigned_drcr as $key2 => $value2) {



                    if (in_array($key2, $bal_array)) {



                    } else {

                        array_push($non_bal_array, $key2);

                    }



                }



                foreach ($non_bal_array as $value3) {

                    # code...

                    $this->db->insert(db_prefix() . 'accountbalances', [

                        'PlantID' => $value3,

                        'FY' => $fy,

                        'AccountID' => $account_id,

                        'BAL1' => "0.00",

                        'UserID2' => get_staff_user_id(),

                    ]);

                }







                set_alert('success', _l('added_successfully', _l('client')));



                redirect(admin_url('clients/client/' . $account_id));



            }





        }

    }

	public function GetNextCustomerCode()
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
                'message' => 'Category ID not provided'
            ]);
            exit;
        }
		$Customer_data = $this->clients_model->GetNextCustomerCode($ActSubGroupID2);

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'next_code' => isset($Customer_data['next_code']) ? $Customer_data['next_code'] : '',
            'count' => isset($Customer_data['count']) ? $Customer_data['count'] : 0,
            'category_code' => isset($Customer_data['category_code']) ? $Customer_data['category_code'] : '',
            'category_name' => isset($Customer_data['category_name']) ? $Customer_data['category_name'] : '',
            'ActSubGroupID2' => $ActSubGroupID2
        ]);
        exit;
	}

    /* Edit client or add new client*/

    public function AddEditAccount($id = '')
    {

        if (!has_permission_new('customers', '', 'view')) {

            access_denied('customers');

        }

        if ($id !== "") {

            $AccountIDSetData = array(

                'AccountIDSet' => $id

            );

            $this->session->set_userdata($AccountIDSetData);

        } else {

            $this->session->unset_userdata('AccountIDSet');

        }

        $post_data = array();

        //$data['table_data'] = $this->clients_model->get_table_on_load_filter($post_data);

        $this->load->model('currencies_model');

        $data['currencies'] = $this->currencies_model->get();




        //$data['staff'] = $this->staff_model->get('', ['active' => 1]);

        // Customer groups

        // $data['accountgroupssub'] = $this->clients_model->Getaccountgroupssub();

        $data['groups'] = $this->clients_model->get_groups();

        //$data['routes'] = $this->clients_model->getroute();
        $data['getcustomergroups'] = $this->clients_model->get_customer();

        $data['FreightTerms'] = $this->clients_model->get_freight_terms();

        $data['Priority'] = $this->clients_model->get_priority();

        $data['Territory'] = $this->clients_model->get_territory();

        $data['Broker'] = $this->clients_model->get_broker();

        $data['position'] = $this->clients_model->get_position();


        // echo "<pre>";
        // print_r($data['position']);  
        // die;


        // echo "<pre>";

        // print_r($data['getcustomergroups']);die;

        $data['state'] = $this->clients_model->getallstate();

        $data['rootcompany'] = $this->clients_model->get_rootcompany();

        //$data['StationList'] = $this->clients_model->get_StationList();

        $data['Tdssection'] = $this->clients_model->get_tds_sections();

        $data['title'] = 'Add/Edit Accounts';

        $this->load->view('admin/clients/ManageNew', $data);

    }



    /* add new client*/

    public function SaveAccountID($id = '')
    {
        // Collect POST data
        $AccountDetails = $this->input->post();

        // Handle file upload for 'attachment' field — save to FCPATH/uploads/clients and set DB field 'Attachment'
        if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDirRel = 'uploads/clients/';
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


        $result = $this->clients_model->add_to_tblclients($AccountDetails);

        $response = [
            'success' => $result ? true : false,
            'account_id' => $result ? $result : null,
            'attachment' => isset($AccountDetails['Attachment']) ? $AccountDetails['Attachment'] : (isset($AccountDetails['attachment']) ? $AccountDetails['attachment'] : null)
        ];

        echo json_encode($response);
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



    /* Edit client*/

    public function UpdateAccountID($id = '')
    {

        $AccountDetails = $this->input->post();
        // Handle file upload for update as well
        if (isset($_FILES['attachment']) && isset($_FILES['attachment']['tmp_name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDirRel = 'uploads/clients/';
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
        $updateResult = $this->clients_model->update_tblclients($AccountDetails, $userid);

        $response = [
            'success' => $updateResult ? true : false,
            'account_id' => !empty($AccountID) ? $AccountID : null,
            'attachment' => isset($AccountDetails['Attachment']) ? $AccountDetails['Attachment'] : (isset($AccountDetails['attachment']) ? $AccountDetails['attachment'] : null)
        ];

        echo json_encode($response);

    }

    /* Get All Customer List / ajax */
    public function GetAllCustomerList()
    {


        $CustomerList = $this->clients_model->GetAllCustomerList();

        //         echo"<pre>";
		// print_r($CustomerList);
		// die;


        $html = "";

        foreach ($CustomerList as $key => $value) {

            $status = ($value["IsActive"] == "Y") ? "Active" : "DeActive";

            $html .= '<tr class="get_AccountID" data-id="' . $value["AccountID"] . '">';

            $html .= '<td align="center">' . $value['AccountID'] . '</td>';

            $html .= '<td align="left">' . (isset($value['company']) ? $value['company'] : '') . '</td>';

            $html .= '<td align="left">' . (isset($value["FavouringName"]) ? $value["FavouringName"] : '') . '</td>';

            $html .= '<td align="left">' . (isset($value["PAN"]) ? $value["PAN"] : '') . '</td>';

            $html .= '<td align="left">' . (isset($value["GSTIN"]) ? $value["GSTIN"] : '') . '</td>';

            $html .= '<td align="left">' . (isset($value["OrganisationType"]) ? $value["OrganisationType"] : '') . '</td>';

            $html .= '<td align="left">' . (isset($value["GSTType"]) ? $value["GSTType"] : '') . '</td>';

            $html .= '<td align="left">' . $status . '</td>';

            $html .= '</tr>';



        }

        echo json_encode($html);

    }

    /* Get Account Details by AccountID / ajax */

    public function GetAccountDetailByID()
    {

        $AccountID = $this->input->post('AccountID');

        $AccountDetails = $this->clients_model->get_AccountDetails($AccountID);

        echo json_encode($AccountDetails);

    }

    /**
     * Get comprehensive account data by AccountID
     * Fetches data from tblclients, tblclientwiseshippingdata, tblBankMaster, and tblcontacts
     */
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

        $data = $this->clients_model->getComprehensiveAccountDataByID($AccountID);

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get tblclients data by AccountID
     */
    public function GetTblClientsData()
    {
        $AccountID = $this->input->post('AccountID');

        if (!$AccountID) {
            echo json_encode([]);
            return;
        }

        // Query tblclients table using AccountID
        $this->db->select('*');
        $this->db->from('tblclients');
        $this->db->where('AccountID', $AccountID);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode([]);
        }
    }

    public function verify_pan()
    {
        if (!$this->input->is_ajax_request()) {
            return;
        }

        $pan = strtoupper($this->input->post('pan'));

        if (empty($pan) || strlen($pan) != 10) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid PAN format']);
            return;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kyc-api.aadhaarkyc.io/api/v1/pan/pan",
            //CURLOPT_URL => "https://sandbox.surepass.io/api/v1/pan/pan",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "id_number" => $pan
            ]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . self::KYC_API_BEARER_TOKEN
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            echo json_encode(['status' => 'error', 'message' => 'API Error: ' . $err]);
            return;
        }

        $result = json_decode($response, true);

        // Log response for debugging
        log_message('info', 'PAN Verification Response: ' . $response);

        if ($http_code == 200 && isset($result['success']) && $result['success'] === true) {
            // Extract data from the response
            $pan_data = $result['data'] ?? $result;
            echo json_encode([
                'status' => 'success',
                'message' => 'PAN verified successfully',
                'data' => $pan_data
            ]);
        } else {
            $error_msg = isset($result['message']) ? $result['message'] : 'Invalid PAN';
            echo json_encode([
                'status' => 'error',
                'message' => $error_msg,
                'response' => $result
            ]);
        }
    }

    public function get_gstin_by_pan()
    {
        if (!$this->input->is_ajax_request()) {
            return;
        }

        $pan = strtoupper($this->input->post('pan'));

        if (empty($pan) || strlen($pan) != 10) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid PAN format']);
            return;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kyc-api.aadhaarkyc.io/api/v1/corporate/gstin-by-pan",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "id_number" => $pan
            ]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . self::KYC_API_BEARER_TOKEN
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            echo json_encode(['status' => 'error', 'message' => 'API Error: ' . $err]);
            return;
        }

        $result = json_decode($response, true);

        // Log response for debugging
        log_message('info', 'GSTIN by PAN Response: ' . $response);

        // Check if API returned success
        if ($http_code == 200 && isset($result['success']) && $result['success'] === true) {
            $gstin_data = $result['data'] ?? $result;
            echo json_encode([
                'status' => 'success',
                'message' => 'GSTIN fetched successfully',
                'data' => $gstin_data
            ]);
        } else {
            $error_msg = isset($result['message']) ? $result['message'] : 'No GSTIN found for this PAN';
            echo json_encode([
                'status' => 'error',
                'message' => $error_msg,
                'response' => $result
            ]);
        }
    }

    /**
     * Verify GSTIN using KYC API
     * Calls the corporate-otp/gstin/init endpoint
     */
    public function verify_gstin_kyc()
    {
        if (!$this->input->is_ajax_request()) {
            return;
        }

        $gstin = strtoupper($this->input->post('gstin'));
        $exclude_userid = $this->input->post('userid') ? intval($this->input->post('userid')) : 0;

        // Validate GSTIN format (15 characters)
        if (empty($gstin) || strlen($gstin) != 15) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid GSTIN format. GSTIN must be 15 characters.']);
            return;
        }

        // Check if GSTIN already exists in database
        $existing_client = $this->clients_model->check_gstin_exists($gstin, $exclude_userid);

        if ($existing_client) {
            echo json_encode([
                'status' => 'duplicate',
                'message' => 'GSTIN already exists in the system for: ' . $existing_client['company'],
                'existing_record' => $existing_client
            ]);
            return;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://kyc-api.aadhaarkyc.io/api/v1/corporate-otp/gstin/init",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "id_number" => $gstin,
                "hsn_info_get" => true
            ]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . self::KYC_API_BEARER_TOKEN
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            echo json_encode(['status' => 'error', 'message' => 'API Error: ' . $err]);
            return;
        }

        $result = json_decode($response, true);

        // Log response for debugging
        log_message('info', 'GSTIN Verification Response: ' . $response);

        // Check if API returned success
        if ($http_code == 200 && isset($result['success']) && $result['success'] === true) {
            echo json_encode([
                'status' => 'success',
                'message' => 'GSTIN verified successfully',
                'data' => $result['data'] ?? $result
            ]);
        } else {
            $error_msg = isset($result['message']) ? $result['message'] : 'GSTIN verification failed';
            echo json_encode([
                'status' => 'error',
                'message' => $error_msg,
                'response' => $result
            ]);
        }
    }
}
