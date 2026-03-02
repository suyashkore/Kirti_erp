<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VehicleInPremises extends AdminController
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('VehicleInPremises_model');
	}

	/* ==========================
	* VEHICLE IN PREMISES REPORT
	* =========================== */
	public function index(){
		$data['title'] = 'Vehicle In Premises';
		$selected_company = $this->session->userdata('root_company');
    	$data['company_detail'] = $this->VehicleInPremises_model->get_company_detail($selected_company);
		$data['GateMaster'] = $this->VehicleInPremises_model->get_in_premises_report();
		$this->load->view('admin/VehicleInPremises/VehicleInPremisesReport', $data);
	}
}