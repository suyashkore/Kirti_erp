<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		// $this->load->model('Items_model');
	}

	/* =========================
		* ADD / EDIT PAGE
		* ========================= */
	public function index(){
		$data['title'] = 'Invoice Master';
    
		$this->load->view('admin/Invoice/InvoiceAddEdit', $data);
	}
  
  /* =========================
		* LIST PAGE
		* ========================= */
	public function List(){
		$data['title'] = 'Invoice List';
    
		$this->load->view('admin/Invoice/InvoiceList', $data);
	}
}
