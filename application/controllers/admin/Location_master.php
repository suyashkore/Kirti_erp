<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Location_master extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title'] = 'Location Master';

        // Sample data for the table as requested
        $data['locations'] = [
            ['id' => 1, 'description' => 'BHANDGAON', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 2, 'description' => 'Etafhawade', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 3, 'description' => 'GWALIOR', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 4, 'description' => 'KRUSHNUR', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 5, 'description' => 'Mangdeulgaon', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 6, 'description' => 'PARATWADA', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 7, 'description' => 'TIKAMGARH', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 8, 'description' => 'Tirunelveli', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 9, 'description' => '21 ST CENTURY WH BAGALKOT BIJAPUR', 'type' => 'Our Location', 'blocked' => 'No'],
            ['id' => 10, 'description' => '79-C Market Yard, Latur', 'type' => 'Our Location', 'blocked' => 'No'],
            ['id' => 11, 'description' => 'AADGAON', 'type' => 'Party Location', 'blocked' => 'No'],
            ['id' => 12, 'description' => 'Aakupamula', 'type' => 'Party Location', 'blocked' => 'No'],
        ];

        $this->load->view('admin/location/location_form', $data);
    }

    public function manage()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            // Logic to save data to database would go here
            
            set_alert('success', 'Location created successfully');
            redirect(admin_url('location_master'));
        }
    }
}