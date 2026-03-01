<?php defined('BASEPATH') or exit('No direct script access allowed');

class Grn extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->view('admin/GRN/grn_form');
    }
}