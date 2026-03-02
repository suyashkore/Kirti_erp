<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Vehicles extends AdminController
	{
		private $not_importable_fields = ['id'];
		
		public function __construct()
		{
			parent::__construct();
			$this->load->model('invoice_items_model');
			$this->load->model('vehicle_model');

		}
		
		/* List all available items */
		public function index()
		{
			if (!has_permission_new('vehiclemaster', '', 'view')) {
				access_denied('Invoice Items');
			}
			
			$this->load->model('taxes_model');
			$data['taxes']        = $this->taxes_model->get();
			$data['items_groups'] = $this->invoice_items_model->get_groups();
			$data['items_main_groups'] = $this->invoice_items_model->get_main_groups();
			$data['items_sub_groups'] = $this->invoice_items_model->get_sub_groups();
			
			$this->load->model('currencies_model');
			$data['currencies'] = $this->currencies_model->get();
			
			$data['base_currency'] = $this->currencies_model->get_base_currency();
			
			$data['vehicle'] = $this->vehicle_model->get();
			$data['vehicle_data'] = $this->vehicle_model->GetVehicleList();
			$data['company_detail'] = $this->vehicle_model->get_company_detail();
			$DriverType = "1000159";
			$data['DriverList']    = $this->clients_model->GetStaffListTypeWise($DriverType);
			$data['title'] = "Vehicle Master";
			$data['Tdssection'] = $this->vehicle_model->get_tds_sections();

			$data['vehicle'] = $this->vehicle_model->get_vehicle();


			$this->load->view('admin/vehicle/manage', $data);
		}
		public function export_VehicleMaster()
		{
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				
				$data = $this->vehicle_model->GetVehicleList();
				$selected_company_details    = $this->vehicle_model->get_company_detail();
				
				$writer = new XLSXWriter();
				
				// Creating the Sheet
				$writer->writeSheetHeader(
    				'Sheet1',
    				[
        				'Route Name' => 'string',
        				'KM'         => 'string'
    				]
				);
				//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');
				//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');
				//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
				//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
				
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 4);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 4);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				
				
				
				// empty row
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				$set_col_tk = [];
				$set_col_tk["Vehicle RegNo"] =  'Vehicle RegNo';
				$set_col_tk["Vehicle Type"] = 'Vehicle Type';
				$set_col_tk["Vehicle Capacity"] = 'Vehicle Capacity';
				$set_col_tk["Driver Name"] = 'Driver Name';
				$set_col_tk["Brand"] = 'Brand';
				$set_col_tk["Model"] = 'Model';
				$set_col_tk["Fuel Type"] = 'Fuel Type';
				$set_col_tk["Fuel Capacity"] = 'Fuel Capacity';
				$set_col_tk["Mileage"] = 'Mileage';
				$set_col_tk["Excel Type"] = 'Excel Type';
				$set_col_tk["Fitness Expiry Date"] = 'Fitness Expiry Date';
				$set_col_tk["Pollution Expiry Date"] = 'Pollution Expiry Date';
				$set_col_tk["Insurance No."] = 'Insurance No.';
				$set_col_tk["Start Day"] = 'Start Day';
				$set_col_tk["Status"] = 'Status';
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				
				foreach ($data as $k => $value) {
					
					$list_add = [];
					$list_add[] = $value["VehicleID"];
					if($value["VehicleTypeID"] == "0"){
						$VehicleTypeID = "Own";
						}elseif($value["VehicleTypeID"] == "1"){
						$VehicleTypeID = "Transport";
						}elseif($value["VehicleTypeID"] == "2"){
						$VehicleTypeID = "Rental";
						}else{
                        $VehicleTypeID =  "";
					}
					$list_add[] = $VehicleTypeID;
					
					$list_add[] = $value["VehicleCapacity"];
					$list_add[] = $value["firstname"]." ".$value["lastname"];
					$list_add[] = $value["brand"];
					$list_add[] = $value["model"];
					$list_add[] = $value["fuel_type"];
					$list_add[] = $value["fuel_capacity"];
					$list_add[] = $value["mileage"];
					$list_add[] = $value["excel_type"];
					$list_add[] = substr(_d($value['fitness_exp_date']),0,10);
					$list_add[] = substr(_d($value['pollution_exp_date']),0,10);
					$list_add[] = $value["insuranceno"];
					$date = substr(_d($value['StartDate']),0,10);
					$list_add[] = $date;
					if($value['ActiveYN'] == "1"){
						$status = "Available";
						}elseif($value['ActiveYN'] == "0"){
						$status = "Deactive";
						} elseif($value['ActiveYN'] == "2"){
						$status = "In-Maintenance";
						} elseif($value['ActiveYN'] == "3"){
						$status = "Legal";
						}  else{
						$status = "";
					} 
					$list_add[] = $status;
					
					$writer->writeSheetRow('Sheet1', $list_add);
					
				}
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'VehicleMaster.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		
		public function table()
		{
			if (!has_permission_new('vehiclemaster', '', 'view')) {
				ajax_access_denied();
			}
			$this->app->get_table_data('vehicle_table');
		}
		
		/* Edit or update items / ajax request /*/
		public function manage()
		{
			if (has_permission_new('vehiclemaster', '', 'view')) {
				if ($this->input->post()) {
					$data = $this->input->post();
					// echo "<pre>";print_r($data);die;
					if ($data['itemid'] == '') {
						if (!has_permission_new('vehiclemaster', '', 'create')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}
						// var_dump($data);
						// die;
						$time = date('H:i:s');
						$data['StartDate'] = to_sql_date($data['StartDate'])." ".$time;
						$data['duedate'] = to_sql_date($data['duedate']);
						$data['taxduedate'] = to_sql_date($data['taxduedate']);
						$data['fitness_exp_date'] = to_sql_date($data['fitness_exp_date']);
						$data['pollution_exp_date'] = to_sql_date($data['pollution_exp_date']);
						$id      = $this->vehicle_model->add($data);
						$success = false;
						$message = '';
						if ($id) {
							$success = true;
							$message = _l('added_successfully', "Vehicle");
						}
						// echo json_encode([
                        // 'success' => $success,
                        // 'message' => $message,
                        // 'item'    => $this->vehicle_model->get($id),
						// ]);
						redirect(admin_url('Vehicles'));
						
						} else {
						if (!has_permission_new('vehiclemaster', '', 'edit')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}
						$time = date('H:i:s');
						$data['StartDate'] = to_sql_date($data['StartDate'])." ".$time;
						$data['duedate'] = to_sql_date($data['duedate']);
						$data['taxduedate'] = to_sql_date($data['taxduedate']);
						$data['fitness_exp_date'] = to_sql_date($data['fitness_exp_date']);
						$data['pollution_exp_date'] = to_sql_date($data['pollution_exp_date']);
						// print_r($data);
						
						// die;
						$success = $this->vehicle_model->edit($data);
						$message = '';
						if ($success) {
							$message = _l('updated_successfully', _l('sales_item'));
						}
						redirect(admin_url('vehicles'));
						// echo json_encode([
                        // 'success' => $success,
                        // 'message' => $message,
						// ]);
						
					}
				}
			}
		}		
		
		
		/* Delete item*/
		public function delete($id)
		{
			if (!has_permission_new('vehiclemaster', '', 'delete')) {
				access_denied('Invoice Items');
			}
			
			if (!$id) {
				redirect(admin_url('vehicles'));
			}
			
			$response = $this->vehicle_model->delete($id);
			if (is_array($response) && isset($response['referenced'])) {
				set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
				} elseif ($response == true) {
				set_alert('success', 'Vehicle Delected Successfully..');
				} else {
				set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
			}
			redirect(admin_url('vehicles'));
		}
		
		
		
		
		
		/* Get item by id / ajax */
		public function get_vehicle_by_id($id)
		{
			if ($this->input->is_ajax_request()) {
				$vehicle                     = $this->vehicle_model->get($id);
				
				
				echo json_encode($vehicle);
			}
		}

		public function gettdspercent()
{
    $Tdsselection = $this->input->post('Tdsselection');
    $data = $this->vehicle_model->gettdspercent_new($Tdsselection);
    echo json_encode($data);
}

/**
		 * Fetch bank details from IFSC code using RBI IFSC API
		 */
		public function fetchBankDetailsFromIFSC() {
			$ifsc_code = $this->input->post('ifsc_code');
			
			if (empty($ifsc_code)) {
				echo json_encode("Not Found");
				return;
			}
			
			// Validate IFSC code format (11 characters)
			if (strlen($ifsc_code) != 11) {
				echo json_encode("Not Found");
				return;
			}
			
			// Try multiple API sources
			$bank_details = $this->getBankDetailsFromIFSC($ifsc_code);
			
			if ($bank_details) {
				echo json_encode($bank_details);
			} else {
				echo json_encode("Not Found");
			}
		}

		/**
		 * Get bank details from IFSC code
		 */
		private function getBankDetailsFromIFSC($ifsc_code) {
			// First try: RBI IFSC API (Official)
			$response = $this->callIFSCAPI("https://ifsc.razorpay.com/{$ifsc_code}", $ifsc_code);
			if ($response) {
				return $response;
			}
			
			// Second try: Alternative API
			$response = $this->callAlternativeAPI($ifsc_code);
			return $response;
		}

		/**
		 * Call IFSC API (Razorpay)
		 */
		private function callIFSCAPI($url, $ifsc_code) {
			try {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				
				$response = curl_exec($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				
				if ($http_code == 200 && $response) {
					$data = json_decode($response, true);
					
					if (isset($data['BANK']) && isset($data['BRANCH'])) {
						return array(
							'BANK' => isset($data['BANK']) ? $data['BANK'] : '',
							'BRANCH' => isset($data['BRANCH']) ? $data['BRANCH'] : '',
							'ADDRESS' => isset($data['ADDRESS']) ? $data['ADDRESS'] : '',
							'IFSC' => $ifsc_code
						);
					}
				}
			} catch (Exception $e) {
				log_message('error', 'IFSC API Error: ' . $e->getMessage());
			}
			
			return false;
		}
		public function verifyBankAccount()

		{

			

			$bank_ac_no = $this->input->post('bank_ac_no');

			$ifsc_code = $this->input->post('ifsc_code');

			$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmcmVzaCI6ZmFsc2UsImlhdCI6MTY3ODM0NzIwNCwianRpIjoiYjFiMTllMGItZTI2MS00MGU2LWFkZGEtMmE0ZTZjMDFjNjllIiwidHlwZSI6ImFjY2VzcyIsImlkZW50aXR5IjoiZGV2Lmdsb2JhbGluZm9jbG91ZEBzdXJlcGFzcy5pbyIsIm5iZiI6MTY3ODM0NzIwNCwiZXhwIjoxOTkzNzA3MjA0LCJ1c2VyX2NsYWltcyI6eyJzY29wZXMiOlsidXNlciJdfX0.G6rjGKnYMdloV6HaFO5yUGvVmbMjJSHXATqsFXlJtbo';

			

			$curl = curl_init();

			curl_setopt_array(

			$curl,

			array(

			CURLOPT_URL => 'https://kyc-api.surepass.io/api/v1/bank-verification/',

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_ENCODING => '',

			CURLOPT_MAXREDIRS => 10,

			CURLOPT_TIMEOUT => 0,

			CURLOPT_FOLLOWLOCATION => true,

			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

			CURLOPT_CUSTOMREQUEST => 'POST',

			CURLOPT_POSTFIELDS => '{

			"id_number": "' . $bank_ac_no . '",

			"ifsc": "' . $ifsc_code . '",

			"ifsc_details": true

			}',

			CURLOPT_HTTPHEADER => array(

			'Content-Type: application/json',

			'Authorization: Bearer ' . $token . ''

			),

			)

			);

			

			$response = curl_exec($curl);

			

			curl_close($curl);

			echo $response;

		}


		/**
		 * Call Alternative API for Bank Details
		 */
		private function callAlternativeAPI($ifsc_code) {
			try {
				// Using Indian Bank IFSC API
				$url = "https://bank-api.example.com/ifsc/{$ifsc_code}";
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				
				$response = curl_exec($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				
				if ($http_code == 200 && $response) {
					$data = json_decode($response, true);
					
					if ($data) {
						return array(
							'BANK' => isset($data['BankName']) ? $data['BankName'] : (isset($data['BANK']) ? $data['BANK'] : ''),
							'BRANCH' => isset($data['BranchName']) ? $data['BranchName'] : (isset($data['BRANCH']) ? $data['BRANCH'] : ''),
							'ADDRESS' => isset($data['address']) ? $data['address'] : (isset($data['ADDRESS']) ? $data['ADDRESS'] : ''),
							'IFSC' => $ifsc_code
						);
					}
				}
			} catch (Exception $e) {
				log_message('error', 'Alternative API Error: ' . $e->getMessage());
			}
			
			return false;
		}



	// public function gettdspercent()
	// {
	// 	$Tdsselection = $this->input->post('Tdsselection');

	// 	$this->db->select(db_prefix() . 'TDSDetails.*');
	// 	$this->db->where(db_prefix() . 'TDSDetails.TDSCode', $Tdsselection);
	// 	$this->db->from(db_prefix() . 'TDSDetails');
	// 	$data =  $this->db->get()->result_array();
	// 	echo json_encode($data);
	// }
	}
