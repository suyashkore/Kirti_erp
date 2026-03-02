<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Country extends AdminController
	{	
		public function __construct()
		{
			parent::__construct();
		}
		
		/* List all available items */
		public function index()
		{
			$this->load->model('transport_model');

			$data['countries'] = $this->transport_model->getallcountry();
			$this->load->view('admin/CountryMaster/manage',$data);
		}
}
