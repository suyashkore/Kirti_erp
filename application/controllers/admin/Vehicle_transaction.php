<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vehicle_transaction extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
    }

    public function index()
    {
        // Dummy data for dropdowns (since no model is used)
        $data['plants'] = ['' => 'Select Plant', '1' => 'Pune Plant', '2' => 'Mumbai Plant'];
        $data['categories'] = ['' => 'Select Category', '1' => 'Raw Material', '2' => 'Finished Goods'];
        $data['advance_regular'] = ['' => 'Select Type', 'Advance' => 'Advance', 'Regular' => 'Regular'];
        $data['dispatch_from'] = ['' => 'Select Dispatch', 'Warehouse 1' => 'Warehouse 1', 'Warehouse 2' => 'Warehouse 2'];
        $data['customers'] = ['' => 'Select Customer', '1' => 'Customer A', '2' => 'Customer B'];
        $data['consignees'] = ['' => 'Select Consignee', '1' => 'Consignee X', '2' => 'Consignee Y'];
        $data['locations'] = ['' => 'Select Location', 'Loc1' => 'Location 1', 'Loc2' => 'Location 2'];
        $data['trucks'] = ['' => 'Select Vehicle No.', 'MH12AB1234' => 'MH12AB1234', 'MH14XY9876' => 'MH14XY9876'];
        $data['transporters'] = ['' => 'Select Transporter', '1' => 'Transporter Alpha', '2' => 'Transporter Beta'];

        // Load the view
        $this->load->view('admin/Vehicle_transaction/add', $data);
    }

    public function add()
    {
        $this->index();
    }
}
