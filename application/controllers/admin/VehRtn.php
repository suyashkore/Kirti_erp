<?php

	

	defined('BASEPATH') or exit('No direct script access allowed');

	

	class VehRtn extends AdminController

	{

		public function __construct()

		{

			parent::__construct();

			$this->load->model('vehicle_return_model');

			$this->load->model('VehRtn_model');

			$this->load->model('Clients_model');

		}

		

		public function index(){

			

			if (!has_permission_new('vehicle_return', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Rtn";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			//$data['clients_details'] = $this->vehicle_return_model->get_vendor_data();

			//$data['staff_details'] = $this->vehicle_return_model->get_staff_data();

			$fy = $this->session->userdata('finacial_year');

			$fy_new  = $fy + 1;

			$lastdate_date = '20'.$fy_new.'-03-31';

			$firstdate_date = '20'.$fy_new.'-04-01';

			$curr_date = date('Y-m-d');

			$curr_date_new    = new DateTime($curr_date);

			$last_date_yr = new DateTime($lastdate_date);

			if($last_date_yr < $curr_date_new){

				$to_date = '31/03/20'.$fy_new;

				$from_date = '01/03/20'.$fy_new;

				}else{

				$from_date = "01/04/".date('Y');

				$to_date = date('d/m/Y');

			}

			$date = array(

            "from_date"=>$from_date,

            "to_date"=>$to_date,

            );

            

			/*echo "<pre>";

				print_r($date);

			die;*/

			$data['vRtnlist'] =  $this->VehRtn_model->vehicle_return_table($date);

			$data['chllist'] =  $this->VehRtn_model->challan_model_table($date);

			$this->load->view('admin/VehRtn/Manage', $data);

		}

		

		public function TransportDashboard()

		{

			if (!has_permission_new('TransportDashboard', '', 'view')) {

				access_denied('orders');

			}

			close_setup_menu();

			$data['title']                = "Transport Dashboard";

			$data['Vehicles'] =  $this->VehRtn_model->getvehicle();

			$data['routes']    = $this->clients_model->getroute();

			

			$data['bodyclass']            = 'invoices-total-manual';

			$this->load->view('admin/VehRtn/TransportDashboard', $data);

		}

		public function GetTransportCounters()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$TotalTrips = $this->VehRtn_model->TotalReturnEntryBydate($data);

			$TotalVehicles = $this->VehRtn_model->TotalVehiclesByFilter($data);

			$TotalTravel = $this->VehRtn_model->DistanceTravelBydate($data);

			$Expense = $this->VehRtn_model->ExpensesCalculationCounter($data);

			$FuelCons = $this->VehRtn_model->FuelConsumptionBydate($data);

			$MileageGap = $this->VehRtn_model->GetMileageGap($data);

			$FuelEfficiency = $this->VehRtn_model->FuelEfficiencyBydate($data);

			// print_r($MileageGap);die;

			$return = [

			'TotalTrips' => $TotalTrips->TotalEntry,

			'TotalVehicles' => $TotalVehicles->TotalEntry,

			'TotalTravel' => $TotalTravel->TotalTravel,

			'TotalExp' => $Expense->TotalExp,

			'MileageGap' => $MileageGap['MileageGap'],

			'FuelConsumption' => $FuelCons->FuelConsumption,

			'FuelEfficiency' => $FuelEfficiency['FuelEfficiency'],

			];

			

			echo json_encode($return);

		}

		public function GetVehicleTypeChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			);

			$VchTypes = $this->VehRtn_model->GetVehicleTypeChart();

			$return = [

			'VchTypes' => $VchTypes,

			];

			

			echo json_encode($return);

		}

		public function GetVehicleFuelTypeChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			);

			$VchTypes = $this->VehRtn_model->GetVehicleFuelTypeChart();

			$return = [

			'VchTypes' => $VchTypes,

			];

			

			echo json_encode($return);

		}

		public function GetTotalChallanByRouteChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$VchTypes = $this->VehRtn_model->GetTotalChallanByRouteChart($data);

			$return = [

			'VchTypes' => $VchTypes,

			];

			

			echo json_encode($return);

		}

		public function GetTotalChallanByDriverChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$TotalChallan = $this->VehRtn_model->GetTotalChallanByDriverChart($data);

			$return = [

			'TotalChallan' => $TotalChallan,

			];

			

			echo json_encode($return);

		}

		public function GetMonthWiseExpenses()

		{

			$data = array(

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			'FilterType'  => $this->input->post('FilterType'),

			);

			$TotalExpense = $this->VehRtn_model->GetTotalExpenseMonthWise($data);

			// echo "<pre>";print_r($TotalExpense);die;

			$return = [

			'TotalExpense' => $TotalExpense['chartData'],

			'Months' => $TotalExpense['Months'],

			];

			

			echo json_encode($return);

		}

		public function GetFleetUtilization()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$TotalExpense = $this->VehRtn_model->GetFleetUtilization($data);

			// echo "<pre>";print_r($TotalExpense);die;

			$return = [

			'TotalExpense' => $TotalExpense['chartData'],

			'Dates' => $TotalExpense['Dates'],

			];

			

			echo json_encode($return);

		}

		public function GetHighestDeliveryStationChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$VchTypes = $this->VehRtn_model->GetHighestDeliveryStationChart($data);

			$return = [

			'VchTypes' => $VchTypes,

			];

			

			echo json_encode($return);

		}

		public function Get_top_five_mileage_vehicle()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$VchTypes = $this->VehRtn_model->Get_top_five_mileage_vehicle($data);

			$return = [

			'VchTypes' => $VchTypes,

			];

			

			echo json_encode($return);

		}

		public function Get_Highest_maintance_charge_vehicles()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$VchTypes = $this->VehRtn_model->Get_Highest_maintance_charge_vehicles($data);

			$return = [

			'VchTypes' => $VchTypes,

			];

			

			echo json_encode($return);

		}

		public function ExpensesCalculationBydate()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$Expenses = $this->VehRtn_model->ExpensesCalculationBydate($data);

			$return = [

			'Expenses' => $Expenses,

			];

			

			echo json_encode($return);

		}

		public function GetStandard_VS_ActualMileage()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'vehicle'  => $this->input->post('vehicle'),

			);

			$result = $this->VehRtn_model->GetStandard_VS_ActualMileage($data);

			

			

			$data = [

			'Standard' => $result['Standard'],

			'Actual' => $result['Actual'],

			/*'TableData' => $html,*/

			];

			

			echo json_encode($data);

		}

		//==================== Add Edit Crate Received Vie Vehicle return =============

		public function AddEditCrate()

		{

			if (!has_permission_new('Vehicle_Crate', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Ruturn - Crate Details";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			

			$data['routes']    = $this->clients_model->getroute();

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$this->load->view('admin/VehRtn/AddEditCrate', $data);

		}

		

		//==================== Add Edit Only Vehicle return =============

		public function AddEditOnlyVehicleRtn()

		{

			if (!has_permission_new('Only_Vehicle_Rtn', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Ruturn - Crate Details";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$data['routes']    = $this->clients_model->getroute();

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$this->load->view('admin/VehRtn/AddEditOnlyVehicleRtn', $data);

		}

		//==================== Add Edit Crate Received Vie Vehicle return =============

		public function AddEditPayment()

		{

			if (!has_permission_new('Vehicle_Payment', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Ruturn - Payment Details";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$data['routes']    = $this->clients_model->getroute();

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$this->load->view('admin/VehRtn/AddEditPayment', $data);

		}

		//==================== Add Edit Crate Received Vie Vehicle return =============

		public function AddEditExpense()

		{

			if (!has_permission_new('Vehicle_Expense', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Ruturn - Payment Details";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$data['routes']    = $this->clients_model->getroute();

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$this->load->view('admin/VehRtn/AddEditExpense', $data);

		}

		

		public function VehicleLoadedCapacityReport()

		{

			if (!has_permission_new('VehicleLoadedCapacityReport', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Loaded Capacity Report";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$this->load->model('clients_model');

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$data['routes']    = $this->clients_model->getroute();

			$data['vehicle']    = $this->VehRtn_model->getvehicle();

			$this->load->view('admin/VehRtn/VehicleLoadedCapacityReport', $data);

		}

		public function GetVehicleLoadedReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'vehicle'  => $this->input->post('vehicle'),

			'Route'  => $this->input->post('Route'),

			);

			$result = $this->VehRtn_model->GetVehicleLoadedReport($data);

			// echo "<pre>";print_r($result);die;

			$html = '';

			$i=1;

			

			if(count($result)){

				$TotalCapacity = 0;

				$TotalLoaded = 0;

				foreach($result as $each){

					

					$TotalCapacity += $each["VehicleCapacity"];

					$TotalLoaded += $each["Crates"];

					

					$html .='<tr>';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["ChallanID"].'</td>';

					$html .='<td>'._d($each["Transdate"]).'</td>';

					$html .='<td>'._d($each["gatepasstime"]).'</td>';

					$html .='<td style="text-align:right;">'.$each["VehicleCapacity"].'</td>';

					$html .='<td style="text-align:right;">'.$each["Crates"].'</td>';

					$html .='</tr>';

					$i++;

				}

				

				$html .='<tr>';

				$html .='<td colspan ="4" align="right" style="font-size: 13px;font-weight: 700;">Total</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.$TotalCapacity.'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.$TotalLoaded.'</td>';

				$html .='</tr>';

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		

		public function export_VehicleLoadedReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				'vehicle'  => $this->input->post('vehicle'),

				'Route'  => $this->input->post('Route')

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->GetVehicleLoadedReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Vehicle Loaded Capacity Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["ChallanID"] =  'ChallanID';

				$set_col_tk["Challan Date"] =  'Challan Date';

				$set_col_tk["Gatepass Time"] =  'Gatepass Time';

				$set_col_tk["Vehicle Capacity"] =  'Vehicle Capacity';

				$set_col_tk["Vehicle Capacity Of Loadedo"] =  'Vehicle Capacity Of Loaded';

				

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$i=1;

                

				$TotalCapacity = 0;

				$TotalLoaded = 0;

                foreach ($body_data as $each) {

					

					$TotalCapacity += $each["VehicleCapacity"];

					$TotalLoaded += $each["Crates"];

					

                    $list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each['ChallanID'];

                    $list_add[] = _d($each['Transdate']);

                    $list_add[] = _d($each['gatepasstime']);

                    $list_add[] = $each['VehicleCapacity'];

                    $list_add[] = $each['Crates'];

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

                

                $list_add = [];

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = 'Total';

                $list_add[] = $TotalCapacity;

                $list_add[] = $TotalLoaded;

                $writer->writeSheetRow('Sheet1', $list_add);

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'LoadedCapacity_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		public function MileageReport()

		{

			if (!has_permission_new('MileageReport', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Ruturn - Mileage Report";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$data['DriverList']    = $this->VehRtn_model->GetDriverList();

			$data['vehicle']    = $this->VehRtn_model->getvehicle();

			$data['StationList']    = $this->VehRtn_model->StationList();

			

			$data['routes']    = $this->clients_model->getroute();

			$this->load->model('dashboard_model');

			$data['leads_status_stats']    = json_encode($this->dashboard_model->leads_status_stats());

			$this->load->view('admin/VehRtn/MileageReport', $data);

		}

		public function DamageCurrencyReport()

		{

			if (!has_permission_new('DamageCurrencyReport', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Ruturn - Damage Currency Report";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$data['CustomerList']    = $this->VehRtn_model->getCustomerList();

			$this->load->view('admin/VehRtn/DamageCurrencyReport', $data);

		}

		

		

		public function GetDetail(){

			$VRtnID = $this->input->post('VRtnID');

			// Get data

			$data = $this->VehRtn_model->GetDetails($VRtnID);

			echo json_encode($data);

		}

		// Get Account List For Crates and Payments

		public function GetAccountlistForCrates(){

			$postData = $this->input->post();

			$data = $this->VehRtn_model->GetAccountlistForCrates($postData);

			echo json_encode($data);

		}

		

		// Get Account Details For Crates and Payments

		public function getAccountDetails(){

			$postData = $this->input->post();

			$Account_data = $this->VehRtn_model->getAccountDetails($postData);

			echo json_encode($Account_data);

		}

		

		// Get Account list For Expense

		public function staffaccountlist(){

			$postData = $this->input->post();

			//$data = $this->VehRtn_model->staffgetaccounts($postData);

			$data = $this->VehRtn_model->GetAccountlistForExpenses($postData);

			echo json_encode($data);

		}

		

		// Get Account Details For Expense

		public function get_staffAccount_Details(){

			$postData = $this->input->post();

			//$Account_data = $this->VehRtn_model->get_staffAccount_Details($postData);

			$Account_data = $this->VehRtn_model->getAccountDetailsForExpenses($postData);

			echo json_encode($Account_data);

		}

		

		public function unique_challan_details(){

			

			$data =  $this->VehRtn_model->challan_unique_data($this->input->post());

			echo json_encode($data);

		}

		

		public function vehicle_return_model(){

			$data =  $this->VehRtn_model->vehicle_return_table($this->input->post());

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$url = "'".admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"]."'";

					//$html.= '<tr onclick="location.href='.$url.'">';

					$html.= '<tr class= "get_VehicleRtnID" data-id = "'.$value["ReturnID"].'">';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td></td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';

					

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["loader_fn"].' '.$value["loader_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["Salesman_fn"].' '.$value["Salesman_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["ChallanAmt"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["OtherVehicleDetails"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			echo $html;

		}

		public function vehicle_return_model_new(){

			$data =  $this->VehRtn_model->vehicle_return_table($this->input->post());

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$url = "'".admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"]."'";

					//$html.= '<tr onclick="location.href='.$url.'">';

					$html.= '<tr class= "get_VehicleRtnID" data-id = "'.$value["ReturnID"].'">';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["ChallanAmt"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsCrateRcvd"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsRtnRcvd"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsPaymentRcvd"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsExpRcvd"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			echo $html;

		}

		public function vehicle_return_model_crates(){

			$data =  $this->VehRtn_model->vehicle_return_table($this->input->post(),'Crates');

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$url = "'".admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"]."'";

					//$html.= '<tr onclick="location.href='.$url.'">';

					if($value["IsCrateRcvd"] == "Y"){

						$color="green";

						}else{

						$color="red";

					}

					

					$html.= '<tr style="background-color:'.$color.'; color:white; cursor: pointer;" class= "get_VehicleRtnID" data-id = "'.$value["ReturnID"].'">';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["Station"].'</td>';

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["return_crates"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			echo $html;

		}

		public function vehicle_return_model_payment(){

			$data =  $this->VehRtn_model->vehicle_return_table($this->input->post(),'Payments');

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$url = "'".admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"]."'";

					//$html.= '<tr onclick="location.href='.$url.'">';

					if($value["IsPaymentRcvd"] == "Y"){

						$color="green";

						}else{

						$color="red";

					}

					

					$html.= '<tr style="background-color:'.$color.'; color:white; cursor: pointer;" class= "get_VehicleRtnID" data-id = "'.$value["ReturnID"].'">';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["return_crates"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			echo $html;

		}

		public function vehicle_return_model_expenses(){

			$data =  $this->VehRtn_model->vehicle_return_table($this->input->post(),'Expenses');

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$url = "'".admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"]."'";

					//$html.= '<tr onclick="location.href='.$url.'">';

					if($value["IsExpRcvd"] == "Y"){

						$color="green";

						}else{

						$color="red";

					}

					

					$html.= '<tr style="background-color:'.$color.'; color:white; cursor: pointer;" class= "get_VehicleRtnID" data-id = "'.$value["ReturnID"].'">';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["return_crates"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			echo $html;

		}

		public function vehicle_return_model_new2(){

			$data =  $this->VehRtn_model->vehicle_return_table($this->input->post());

			// print_r($data);die;

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$url = "'".admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"]."'";

					//$html.= '<tr onclick="location.href='.$url.'">';

					$VehicleType = "";

					if($value["VehicleType"] == "SELF"){

						$VehicleType = "SELF";

						}elseif($value["VehicleType"] == "TV"){

						$VehicleType = "Transport Vehicle";

						}else{

						$VehicleType = $value["VehicleType"];

					}

					

					$html.= '<tr class= "get_VehicleRtnID" data-id = "'.$value["ReturnID"].'">';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["Station"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$VehicleType.'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			echo $html;

		}

		

		public function challan_details_model(){

			$data =  $this->VehRtn_model->challan_model_table($this->input->post());

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$html.= '<tr class="get_challan_id" data-id="'.$value["ChallanID"].'">'; 

					$html.= '<td style="padding:0px 3px !important;" >'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td></td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["loader_fn"].' '.$value["loader_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["Salesman_fn"].' '.$value["Salesman_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["ChallanAmt"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["OtherVehicleDetails"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="12"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			

			echo $html;

		}

		public function challan_details_model_new(){

			$data =  $this->VehRtn_model->challan_model_table($this->input->post());

			$html ='';

			if(count($data) >0 ){

				foreach($data as $value){

					$html.= '<tr class="get_challan_id" data-id="'.$value["ChallanID"].'">'; 

					$html.= '<td style="padding:0px 3px !important;" >'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["name"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$value["Station"].'</td>';

					$VehicleType = "";

					if($value["VehicleType"] == "SELF"){

						$VehicleType = "SELF";

						}elseif($value["VehicleType"] == "TV"){

						$VehicleType = "Transport Vehicle";

						}else{

						$VehicleType = $value["VehicleType"];

					}

					$html.= '<td style="padding:0px 3px !important;">'.$VehicleType.'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_fn"].' '.$value["driver_ln"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["ChallanAmt"].'</td>'; 

					$html.= '</tr>'; 

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="12"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			

			echo $html;

		}

		

		public function all_challan_details(){

			

			$data_a =  $this->VehRtn_model->challan_all_data($this->input->post());

			echo json_encode($data_a);

		}

		

		public function SaveVehRtn()

		{

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			if ($selected_company == 1) {

				$new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');

				} elseif ($selected_company == 2) {

				$new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');

				} elseif ($selected_company == 3) {

				$new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');

				} elseif ($selected_company == 4) {

				$new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbupl');

			}

			

			if ($selected_company == "1") {

				$GodownID = 'CSPL';

				} else if ($selected_company == "2") {

				$GodownID = 'CFF';

				} else if ($selected_company == "3") {

				$GodownID = 'CBUPL';

			}

			

			$new_vehicle_return_Numbar = 'VRT' . $FY . $new_vehicle_returnNumber;

			$ChallanID = $this->input->post('challan_n');

			$CheckVehRtnForChallan = $this->VehRtn_model->CheckVehRtnForChallan($ChallanID);

			if (empty($CheckVehRtnForChallan)) {

				$RtnCrates = $this->input->post('refund_crates');

				$vehicle_number = $this->input->post('vehicle_number');

				$Transdate = to_sql_date($this->input->post('from_date')) . " " . date('H:i:s');

				$affectedRow = 0;

				$vehicleRtn_data = array(

                'PlantID' => $selected_company,

                'ReturnID' => $new_vehicle_return_Numbar,

                'Transdate' => $Transdate,

                'Crates' => $RtnCrates,

                'ChallanID' => $ChallanID,

                'UserID' => $_SESSION['username'],

                'FY' => $FY

				);

				

				$this->db->insert(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				if ($this->db->affected_rows() > 0) {

					$this->VehRtn_model->increment_next_number();

					$affectedRow++;

					/*$this->db->where('PlantID', $selected_company);

						$this->db->where('EngageID', $vehicle_number);

						$this->db->update(db_prefix() . 'accountsld', [

						'EngageID' => NULL,

					]);*/

					

					// Fresh Rtn Values

					$frRtnSerializedArr = $this->input->post('frRtnSerializedArr');

					$FreshRtnValArray = json_decode($frRtnSerializedArr, true);

					$frRtnValCount = $this->input->post('frRtnVal');

					$ord_no5 = 1;

					

					

					foreach($FreshRtnValArray as $Key=>$val){

						$AccountID_SRtn = $val[0];

						$RtnAmt_val = $val[1];

						$cgst_val = $val[2];

						$sgst_val = $val[3];

						$igst_val = $val[4];

						

						$igst_total = 0;

						$cgst_total = 0;

						$sgst_total = 0;

						

						$sub_total = $RtnAmt_val - $sgst_val - $cgst_val - $igst_val;

						if ($RtnAmt_val !== "0.00" && $RtnAmt_val !== "0") {

							$igst_total = $igst_total + $igst_val;

							$cgst_total = $cgst_total + $cgst_val;

							$sgst_total = $sgst_total + $sgst_val;

							

							// Respective Account ledger Entry for Credit

							$credit_ledger = array(

                            "FY" => $FY,

							"PlantID" => $selected_company,

							"VoucherID" => $new_vehicle_return_Numbar,

							"Transdate" => $Transdate,

							"TransDate2" => date('Y-m-d H:i:s'),

							"TType" => "C",

							"AccountID" => $AccountID_SRtn,

							"Amount" => $RtnAmt_val,

							"Narration" => 'By Fresh stock return/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

							"PassedFrom" => "VEHRTNFRESH",

							"OrdinalNo" => $ord_no5,

							"UserID" => $this->session->userdata('username'),

							);

							$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);

							

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							

							$ord_no5++;

							// Cash Account ledger Entry for Credit

							$debit_ledger = array(

                            "FY" => $FY,

                            "PlantID" => $selected_company,

                            "VoucherID" => $new_vehicle_return_Numbar,

                            "Transdate" => $Transdate,

                            "TransDate2" => date('Y-m-d H:i:s'),

                            "TType" => "D",

                            "AccountID" => "SALE",

                            "Amount" => $sub_total,

                            "Narration" => 'By Fresh stock return/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            "PassedFrom" => "VEHRTNFRESH",

                            "OrdinalNo" => $ord_no5,

                            "UserID" => $this->session->userdata('username'),

							);

							$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

							

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							$ord_no5++;

							

							if ($cgst_total !== 0) {

								// SGST Account Ledger & Balance     

								$debit_ledger = array(

                                "FY" => $FY,

                                "PlantID" => $selected_company,

                                "VoucherID" => $new_vehicle_return_Numbar,

                                "Transdate" => $Transdate,

                                "TransDate2" => date('Y-m-d H:i:s'),

                                "TType" => "D",

                                "AccountID" => "SGST",

                                "Amount" => $sgst_total,

                                "Narration" => 'By Fresh stock return/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                                "PassedFrom" => "VEHRTNFRESH",

                                "OrdinalNo" => $ord_no5,

                                "UserID" => $this->session->userdata('username'),

								);

								$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

								

								if ($this->db->affected_rows() > 0) {

									$affectedRow++;

								}

								

								$ord_no5++;

								// CGST Account Ledger & Balance

								$debit_ledger = array(

                                "FY" => $FY,

                                "PlantID" => $selected_company,

                                "VoucherID" => $new_vehicle_return_Numbar,

                                "Transdate" => $Transdate,

                                "TransDate2" => date('Y-m-d H:i:s'),

                                "TType" => "D",

                                "AccountID" => "CGST",

                                "Amount" => $cgst_total,

                                "Narration" => 'By Fresh stock return/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                                "PassedFrom" => "VEHRTNFRESH",

                                "OrdinalNo" => $ord_no5,

                                "UserID" => $this->session->userdata('username'),

								);

								$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

								if ($this->db->affected_rows() > 0) {

									$affectedRow++;

								}

								$ord_no5++;

								} elseif ($igst_total !== 0) {

								

								$debit_ledger = array(

                                "FY" => $FY,

                                "PlantID" => $selected_company,

                                "VoucherID" => $new_vehicle_return_Numbar,

                                "Transdate" => $Transdate,

                                "TransDate2" => date('Y-m-d H:i:s'),

                                "TType" => "D",

                                "AccountID" => "IGST",

                                "Amount" => $igst_total,

                                "Narration" => 'By Fresh stock return/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                                "PassedFrom" => "VEHRTNFRESH",

                                "OrdinalNo" => $ord_no5,

                                "UserID" => $this->session->userdata('username'),

								);

								$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

								if ($this->db->affected_rows() > 0) {

									$affectedRow++;

								}

							}

						}

					}

					

					// For Fresh Rtn

					$FreshRtnSerializedArr = $this->input->post('FreshRtnSerializedArr');

					$FreshRtnArray = json_decode($FreshRtnSerializedArr, true);

					$FrtRtnCount = $this->input->post('ItemCount');

					$ord_no4 = 1;

					foreach($FreshRtnArray as $key=>$val){

						$rtnqty = $val[0];

						$TransID_val = $val[1];

						$ItemID_val = $val[2];

						$AccountID_val = $val[3];

						$rate_val = $val[4];

						$gst_val = $val[5];

						$state_val = $val[6];

						$PackQty_val = $val[7];

						if ($rtnqty == '') {

							

							} else {

							$ChallanAmt = $rate_val * $rtnqty;

							$gst_amt = ($ChallanAmt / 100) * $gst_val;

							$NetChallanAmt = $ChallanAmt + $gst_amt;

							$gstRate = ($rate_val / 100) * $gst_val;

							$saleRate = $gstRate + $rate_val;

							$CaseQty = $PackQty_val;

							if ($state_val == "UP") {

								$cgstAmt = $gst_amt / 2;

								$sgstAmt = $gst_amt / 2;

								$igstAmt = 0.00;

								

								$cgstPer = $gst_val / 2;

								$sgstPer = $gst_val / 2;

								$igstPer = 0.00;

								} else {

								$cgstAmt = 0.00;

								$sgstAmt = 0.00;

								$igstAmt = $gst_amt;

								

								$cgstPer = 0.00;

								$sgstPer = 0.00;

								$igstPer = $gst_val;

							}

							

							$new_record_details = array(

                            "PlantID" => $selected_company,

                            "FY" => $FY,

                            "cnfid" => "1",

                            "OrderID" => $new_vehicle_return_Numbar,

                            "TransDate" => $Transdate,

                            "TransDate2" => $Transdate,

                            "BillID" => $ChallanID,

                            "TransID" => $TransID_val,

                            "GodownID" => $GodownID,

                            "TType" => "R",

                            "TType2" => "Fresh",

                            "AccountID" => $AccountID_val,

                            "ItemID" => $ItemID_val,

                            "CaseQty" => $CaseQty,

                            "SaleRate" => $saleRate,

                            "BasicRate" => $rate_val,

                            "SuppliedIn" => "CS",

                            "BilledQty" => $rtnqty,

                            "DiscPerc" => "0.00",

                            "DiscAmt" => "0.00",

                            "cgst" => $cgstPer,

                            "cgstamt" => $cgstAmt,

                            "sgst" => $sgstPer,

                            "sgstamt" => $sgstAmt,

                            "igst" => $igstPer,

                            "igstamt" => $igstAmt,

                            "ChallanAmt" => $ChallanAmt,

                            "NetChallanAmt" => $NetChallanAmt,

                            "Ordinalno" => $ord_no4,

                            "UserID" => $this->session->userdata('username'),

							);

							//print_r($new_record_details);

							$this->db->insert(db_prefix() . 'history', $new_record_details);

						}

					}

					

					

					// For Crate Ledger

					$CratesSerializedArr = $this->input->post('CratesSerializedArr');

					$CrateArray = json_decode($CratesSerializedArr, true);

					$CrateCount = $this->input->post('CrateCount');

					$ord_no3 = 1;

					foreach($CrateArray as $key=>$val){

						$AccountID = $val[0];

						$RtnCrates = $val[1];

						if ($RtnCrates != "" && $RtnCrates != '0') {

							$vehicleCrates_data = array(

                            'PlantID' => $selected_company,

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'ChallanID' => $ChallanID,

                            'AccountID' => $AccountID,

                            'TType' => 'C',

                            'Qty' => $RtnCrates,

                            'PassedFrom' => 'VEHRTNCRATES',

                            'Narration' => 'Against VehicleID ' . $new_vehicle_return_Numbar . '/ChallanID /' . $ChallanID,

                            'Ordinalno' => $ord_no3,

                            'UserID' => $_SESSION['username'],

                            'FY' => $FY,

							);

							//print_r($vehicleCrates_data);

							$data_i = $this->db->insert(db_prefix() . 'accountcrates', $vehicleCrates_data);

							$ord_no3++;

						}

					}

					

					// For Expenses

					$ExpSerializedArr = $this->input->post('ExpSerializedArr');

					$EXPArray = json_decode($ExpSerializedArr, true);

					$row_count_exp = $this->input->post('ExpCount');

					$ord_no = 1;

					foreach($EXPArray as $Key=>$val){

						$AccountID = $val[0];

						$ExpAmt = $val[1];

						if ($ExpAmt !== "0.00" && $ExpAmt !== "0") {

							$expense_detail_result = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => $AccountID,

                            'TType' => 'D',

                            'Amount' => $ExpAmt,

                            'PassedFrom' => 'VEHRTNEXP',

                            'Narration' => 'By Vehicle Expense ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($expense_detail_result);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $expense_detail_result);

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							

							$expense_detail_result_debit = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => 'CASH',

                            'TType' => 'C',

                            'Amount' => $ExpAmt,

                            'PassedFrom' => 'VEHRTNEXP',

                            'Narration' => 'By Vehicle Expense ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($expense_detail_result_debit);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $expense_detail_result_debit);

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							$ord_no++;

						}

					}

					

					

					// For Payment Ledger 

					$PaymentSerializedArr = $this->input->post('PaymentSerializedArr');

					$PayArray = json_decode($PaymentSerializedArr, true);

					$PayCount = $this->input->post('PayCount');

					$ord_no2 = 1;

					foreach($PayArray as $key=>$val){

						$AccountID = $val[0];

						$PayAmt = $val[1];

						if ($PayAmt !== "0.00" && $PayAmt !== "0") {

							$payment_reciept_result = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => $AccountID,

                            'TType' => 'C',

                            'Amount' => $PayAmt,

                            'PassedFrom' => 'VEHRTNPYMTS',

                            'Narration' => 'Cash Received/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no2,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($expense_detail_result);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $payment_reciept_result);

							

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							

							$payment_reciept_result_debit = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => 'CASH',

                            'TType' => 'D',

                            'Amount' => $PayAmt,

                            'PassedFrom' => 'VEHRTNPYMTS',

                            'Narration' => 'Cash Received/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no2,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($payment_reciept_result_debit);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $payment_reciept_result_debit);

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							$ord_no2++;

						}

					}

				}

				if ($affectedRow > 0) {

					if ($selected_company == 1) {

						$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');

						} elseif ($selected_company == 2) {

						$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');

						} elseif ($selected_company == 3) {

						$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');

						} elseif ($selected_company == 4) {

						$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbupl');

					}

					$new_vehicle_return_Numbar = 'VRT' . $FY . $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					} else {

					echo json_encode(false);

					die;

				}

				

				} else {

				echo json_encode('Created');

				die;

			}

			

		}

		

		public function UpdateVehRtn()

		{

			$selected_company = $this->session->userdata('root_company');

			if($selected_company == "1"){

				$GodownID = 'CSPL';

				}else if($selected_company == "2"){

				$GodownID = 'CFF';

				}else if($selected_company == "3"){

				$GodownID = 'CBUPL';

			}

			$FY = $this->session->userdata('finacial_year');

			$ChallanID = $this->input->post('challan_n');

			$RtnCrates = $this->input->post('refund_crates');

			$vehicle_number = $this->input->post('vehicle_number');

			$VRtnID = $this->input->post('VRtnID');

			$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

			$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

			$oldDate = $GetVRtnDetails->Transdate;

			$affectedRow = 0;

			$vehicleRtn_data = array(

            'Transdate'=>$Transdate,

            'Crates'=>$RtnCrates,

            'UserID2'=>$_SESSION['username'],

            'Lupdate'=>date('Y-m-d H:i:s')

			);

			

			

			//Insert Records into vehicle Return audit table before updating vehiclereturn table

			$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

			if(!empty($previousVehicleReturnDetails)){

				$insertArray = array(

                'PlantID' =>  $previousVehicleReturnDetails->PlantID,

                'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

                'Transdate' =>  $previousVehicleReturnDetails->Transdate,

                'Crates' =>  $previousVehicleReturnDetails->Crates,

                'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

                'UserID' =>  $previousVehicleReturnDetails->UserID,

                'FY' =>  $previousVehicleReturnDetails->FY,

                'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

                'UserID2' =>  $previousVehicleReturnDetails->UserID2,

                'created_by' =>  $this->session->userdata('username'),

                'created_at' =>  date('Y-m-d H:i:s'),

				);  

				$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

			}

			

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $FY); 

			$this->db->where('ReturnID', $VRtnID);

			$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

			

			if($this->db->affected_rows() > 0){

				$affectedRow++;

			}

			

			// Fresh Rtn Values

			// Delete and Revert balances from privous ledger to ladger audit

			$GetPreLedger = $this->VehRtn_model->GetPreLedger($VRtnID);   

			foreach($GetPreLedger as $key => $value)

			{

				$ledger_audit = array(

                "PlantID"=>$value["PlantID"],

                "FY"=>$value["FY"],

                "Transdate"=>$value["Transdate"],

                "TransDate2"=>$value["TransDate2"],

                "VoucherID"=>$value["VoucherID"],

                "AccountID"=>$value["AccountID"],

                "TType"=>$value["TType"],

                "Amount"=>$value["Amount"],

                "Narration"=>$value["Narration"],

                "PassedFrom"=>$value["PassedFrom"],

                "OrdinalNo"=>$value["OrdinalNo"],

                "UserID"=>$value["UserID"],

                "UserID2"=>$this->session->userdata('username'),

                "Lupdate"=>date('Y-m-d H:i:s')

				);

				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);

				$UserID1 = $value["UserID"];

			}

			// Delete all record related to vehicle return

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $FY);

			$this->db->where('VoucherID', $VRtnID);

			$this->db->delete(db_prefix() . 'accountledger');

			

			// New Ledger Entery created

			$frRtnSerializedArr = $this->input->post('frRtnSerializedArr');

			$FreshRtnValArray = json_decode($frRtnSerializedArr, true);

			$frRtnValCount = $this->input->post('frRtnVal');

			$ord_no5 = 1;

			

			foreach($FreshRtnValArray as $key=>$val){

				

				$igst_total = 0;

				$cgst_total = 0;

				$sgst_total = 0;

				$AccountID_SRtn = $val[0];

				$RtnAmt_val = $val[1];

				$cgst_val = $val[2];

				$sgst_val = $val[3];

				$igst_val = $val[4];

				$Sub_total =  $RtnAmt_val - $sgst_val - $cgst_val - $igst_val;

				if($RtnAmt_val !== "0.00"  && $RtnAmt_val !== "0"){

					$igst_total = $igst_total + $igst_val;

					$cgst_total = $cgst_total + $cgst_val;

					$sgst_total = $sgst_total + $sgst_val;

					

					// Respective Account ledger Entry for Credit

					$credit_ledger = array(

                    "FY"=>$FY,

                    "PlantID"=>$selected_company,

                    "VoucherID"=>$VRtnID,

                    "Transdate"=>$Transdate,

                    "TransDate2"=>date('Y-m-d H:i:s'),

                    "TType"=>"C",

                    "AccountID"=>$AccountID_SRtn,

                    "Amount"=>$RtnAmt_val,

                    "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,

                    "PassedFrom"=>"VEHRTNFRESH",

                    "OrdinalNo"=>$ord_no5,

                    "UserID"=>$this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledger', $credit_ledger);

					if($this->db->affected_rows()>0){

						$affectedRow++;

					}

					$ord_no5++;

					// Cash Account ledger Entry for Credit

					$debit_ledger = array(

                    "FY"=>$FY,

                    "PlantID"=>$selected_company,

                    "VoucherID"=>$VRtnID,

                    "Transdate"=>$Transdate,

                    "TransDate2"=>date('Y-m-d H:i:s'),

                    "TType"=>"D",

                    "AccountID"=>"SALE",

                    "Amount"=>$Sub_total,

                    "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,

                    "PassedFrom"=>"VEHRTNFRESH",

                    "OrdinalNo"=>$ord_no5,

                    "UserID"=>$this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

					if($this->db->affected_rows()>0){

						$affectedRow++;

					}

					$ord_no5++;

					

					if($cgst_total !== 0){

						// SGST Account Ledger & Balance     

						$debit_ledger = array(

                        "FY"=>$FY,

                        "PlantID"=>$selected_company,

                        "VoucherID"=>$VRtnID,

                        "Transdate"=>$Transdate,

                        "TransDate2"=>date('Y-m-d H:i:s'),

                        "TType"=>"D",

                        "AccountID"=>"SGST",

                        "Amount"=>$sgst_total,

                        "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,

                        "PassedFrom"=>"VEHRTNFRESH",

                        "OrdinalNo"=>$ord_no5,

                        "UserID2"=>$this->session->userdata('username'),

                        "Lupdate"=>date('Y-m-d H:i:s'),

                        "UserID"=>$UserID1

						);

						$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

						if($this->db->affected_rows()>0){

							$affectedRow++;

						}

						$ord_no5++;

						// CGST Account Ledger & Balance     

						$debit_ledger = array(

                        "FY"=>$FY,

                        "PlantID"=>$selected_company,

                        "VoucherID"=>$VRtnID,

                        "Transdate"=>$Transdate,

                        "TransDate2"=>date('Y-m-d H:i:s'),

                        "TType"=>"D",

                        "AccountID"=>"CGST",

                        "Amount"=>$cgst_total,

                        "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,

                        "PassedFrom"=>"VEHRTNFRESH",

                        "OrdinalNo"=>$ord_no5,

                        "UserID"=>$this->session->userdata('username')

						);

						$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

						if($this->db->affected_rows()>0){

							$affectedRow++;

						}

						$ord_no5++;

						}elseif($igst_total !== 0){

						$debit_ledger = array(

                        "FY"=>$FY,

                        "PlantID"=>$selected_company,

                        "VoucherID"=>$VRtnID,

                        "Transdate"=>$Transdate,

                        "TransDate2"=>date('Y-m-d H:i:s'),

                        "TType"=>"D",

                        "AccountID"=>"IGST",

                        "Amount"=>$igst_total,

                        "Narration"=>'By Fresh stock return/VehicleReturn '.$VRtnID.'/'.$ChallanID,

                        "PassedFrom"=>"VEHRTNFRESH",

                        "OrdinalNo"=>$ord_no5,

                        "UserID"=>$this->session->userdata('username')

						);

						$this->db->insert(db_prefix() . 'accountledger', $debit_ledger);

						if($this->db->affected_rows()>0){

							$affectedRow++;

						}

					}

				}

			}

			

			// For Fresh Rtn

			$SaleRtnItemList = array();

			$GetSaleRtn = $this->VehRtn_model->GetSaleRtn($VRtnID);

			

			//Insert record into historyAudit table before updating history table

			

			foreach($GetSaleRtn as $value){

				$insertArray = array(

                "PlantID"=> $value['PlantID'],

                "FY"=> $value['FY'],

                "OrderID"=> $value['OrderID'],

                "BillID"=> $value['BillID'],

                "TransID"=> $value['TransID'],

                "IsSchemeYN"=> $value['IsSchemeYN'],

                "TransDate"=> $value['TransDate'],

                "TransDate2"=> $value['TransDate2'],

                "TType"=> $value['TType'],

                "TType2"=> $value['TType2'],

                "AccountID"=> $value['AccountID'],

                "ItemID"=> $value['ItemID'],

                "GodownID"=> $value['GodownID'],

                "PurchRate"=> $value['PurchRate'],

                "Mrp"=> $value['Mrp'],

                "SaleRate"=> $value['SaleRate'],

                "BasicRate"=> $value['BasicRate'],

                "SuppliedIn"=> $value['SuppliedIn'],

                "OrderQty"=> $value['OrderQty'],

                "eOrderQty"=> $value['eOrderQty'],

                "ereason"=> $value['ereason'],

                "BilledQty"=> $value['BilledQty'],

                "DiscPerc"=> $value['DiscPerc'],

                "DiscAmt"=> $value['DiscAmt'],

                "gst"=> $value['gst'],

                "gstamt"=> $value['gstamt'],

                "cgst"=> $value['cgst'],

                "cgstamt"=> $value['cgstamt'],

                "sgst"=> $value['sgst'],

                "sgstamt"=> $value['sgstamt'],

                "igst"=> $value['igst'],

                "igstamt"=> $value['igstamt'],

                "CaseQty"=> $value['CaseQty'],

                "Cases"=> $value['Cases'],

                "OrderAmt"=> $value['OrderAmt'],

                "ChallanAmt"=> $value['ChallanAmt'],

                "NetOrderAmt"=> $value['NetOrderAmt'],

                "NetChallanAmt"=> $value['NetChallanAmt'],

                "rowid"=> $value['rowid'],

                "UserID"=> $value['UserID'],

                "cnfid"=> $value['cnfid'],

                "UserID2"=> $value['UserID2'],

                "Lupdate"=> $value['Lupdate'],

                "created_by"=> $this->session->userdata('username'),

                "created_at"=> date('Y-m-d H:i:s'),

				);

				$this->db->insert(db_prefix() . 'history_Audit', $insertArray);

			}

			

			$ord_no4 = 1;

			foreach($GetSaleRtn as $key => $value){

				$ord_no4++;

				array_push($SaleRtnItemList, $value["ItemID"]);

			}

			

			$Updatedetails = array(

            "BilledQty"=>0.00,

            "cgstamt"=>0.00,

            "sgstamt"=>0.00,

            "igstamt"=>0.00,

            "ChallanAmt"=>0.00,

            "NetChallanAmt"=>0.00,

            "Lupdate"=>date('Y-m-d H:i:s'),

            "UserID2"=>$this->session->userdata('username')

			);

			

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $FY); 

			$this->db->where('OrderID', $VRtnID);

			$this->db->update(db_prefix() . 'history', $Updatedetails);

			

			

			

			$FreshRtnSerializedArr = $this->input->post('FreshRtnSerializedArr');

			$FreshRtnArray = json_decode($FreshRtnSerializedArr, true);

			$FrtRtnCount = $this->input->post('ItemCount');

			foreach($FreshRtnArray as $key=>$val){

				$rtnqty = $val[0];

				$TransID_val = $val[1];

				$ItemID_val = $val[2];

				$AccountID_val = $val[3];

				$rate_val = $val[4];

				$gst_val = $val[5];

				$state_val = $val[6];

				$PackQty_val = $val[7];

				if($rtnqty == ''){

					

					}else{

					$ChallanAmt = $rate_val * $rtnqty;

					$gst_amt = ($ChallanAmt/100) * $gst_val;

					$NetChallanAmt = $ChallanAmt + $gst_amt;

					$gstRate = ($rate_val/100) * $gst_val;

					$saleRate = $gstRate + $rate_val;

					$CaseQty = $PackQty_val;

					if($state_val == "UP"){

						$cgstAmt = $gst_amt / 2;

						$sgstAmt = $gst_amt / 2;

						$igstAmt = 0.00;

						

						$cgstPer = $gst_val / 2;

						$sgstPer = $gst_val / 2;

						$igstPer = 0.00;

						}else{

						$cgstAmt = 0.00;

						$sgstAmt = 0.00;

						$igstAmt = $gst_amt;

						

						$cgstPer = 0.00;

						$sgstPer = 0.00;

						$igstPer = $gst_val;

					}

					// stock update

					

					if (in_array($ItemID_val, $SaleRtnItemList)){

						$Updatedetails = array(

                        "BilledQty"=>$rtnqty,

                        "cgstamt"=>$cgstAmt,

                        "sgstamt"=>$sgstAmt,

                        "igstamt"=>$igstAmt,

                        "ChallanAmt"=>$ChallanAmt,

                        "NetChallanAmt"=>$NetChallanAmt,

						);

						$this->db->where('PlantID', $selected_company);

						$this->db->where('FY', $FY);

						$this->db->where('ItemID', $ItemID_val);

						$this->db->where('OrderID', $VRtnID);

						$this->db->where('TransID', $TransID_val);

						$this->db->update(db_prefix() . 'history', $Updatedetails);

						}else{

						$new_record_details = array(

                        "PlantID"=>$selected_company,

                        "FY"=>$FY,

                        "cnfid"=>"1",

                        "OrderID"=>$VRtnID,

                        "TransDate"=>$Transdate,

                        "TransDate2"=>$Transdate,

                        "BillID"=>$ChallanID,

                        "TransID"=>$TransID_val,

                        "GodownID"=>$GodownID,

                        "TType"=>"R",

                        "TType2"=>"Fresh",

                        "AccountID"=>$AccountID_val,

                        "ItemID"=>$ItemID_val,

                        "CaseQty"=>$CaseQty,

                        "SaleRate"=>$saleRate,

                        "BasicRate"=>$rate_val,

                        "SuppliedIn"=>"CS",

                        "BilledQty"=>$rtnqty,

                        "DiscPerc"=>"0.00",

                        "DiscAmt"=>"0.00",

                        "cgst"=>$cgstPer,

                        "cgstamt"=>$cgstAmt,

                        "sgst"=>$sgstPer,

                        "sgstamt"=>$sgstAmt,

                        "igst"=>$igstPer,

                        "igstamt"=>$igstAmt,

                        "ChallanAmt"=>$ChallanAmt,

                        "NetChallanAmt"=>$NetChallanAmt,

                        "Ordinalno"=>$ord_no4,

                        "UserID"=>$this->session->userdata('username'),

						);

						$this->db->insert(db_prefix() . 'history', $new_record_details);

					}

				}

			}

			

			

			//Insert previous records into new table tblaccountcrates_audit

			$previousAccountCratesDetails = $this->VehRtn_model->GetPreviousAccountCratesDetails("VEHRTNCRATES",$VRtnID);

			foreach($previousAccountCratesDetails as $value){

				$insertArray = array(

				'PlantID'=>$value['PlantID'],

				'FY'=>$value['FY'],

				'VoucherID' =>$value['VoucherID'],

				'Transdate' =>$value['Transdate'],

				'TransDate2' =>$value['TransDate2'],

				'ChallanID' =>$value['ChallanID'],

				'AccountID' =>$value['AccountID'],

				'TType' =>$value['TType'],

				'Qty'=>$value['Qty'],

				'PassedFrom'=>$value['PassedFrom'],

				'Narration'=> $value['Narration'],

				'Ordinalno'=>$value['Ordinalno'],

				'UserID'=>$value['UserID'],

				'UserID2'=>$value['UserID2'],

				'Lupdate'=>$value['Lupdate'],

				'created_by'=>$this->session->userdata('username'),

				'created_at'=>date('Y-m-d H:i:s'),

				);  

				$data_i = $this->db->insert(db_prefix() . 'accountcrates_audit',$insertArray);

			}

			

			// For Crate Ledger

			// Delete Previoud ledger

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $FY);

			$this->db->where('PassedFrom', "VEHRTNCRATES");

			$this->db->where('VoucherID', $VRtnID);

			$this->db->delete(db_prefix() . 'accountcrates');

			

			// Create New

			$CratesSerializedArr = $this->input->post('CratesSerializedArr');

			$CrateArray = json_decode($CratesSerializedArr, true); 

			$CrateCount = $this->input->post('CrateCount');

			$ord_no3 = 1;

			foreach($CrateArray as $key=>$kay){

				$AccountID = $kay[0];

				$RtnCrates = $kay[1];

				if($RtnCrates !="" && $RtnCrates != '0'){

					$vehicleCrates_data = array(

                    'PlantID'=>$selected_company,

                    'VoucherID' =>$VRtnID,

                    'Transdate' =>$Transdate,

                    'TransDate2' =>date('Y-m-d H:i:s'),

                    'ChallanID' =>$ChallanID,

                    'AccountID' =>$AccountID,

                    'TType' =>'C',

                    'Qty'=>$RtnCrates,

                    'PassedFrom'=>'VEHRTNCRATES',

                    'Narration'=> 'Against VehicleID '.$VRtnID.'/ChallanID /'.$ChallanID,

                    'Ordinalno'=>$ord_no3,

                    'UserID'=>$_SESSION['username'],

                    'FY'=>$FY,

					);

					$data_i = $this->db->insert(db_prefix() . 'accountcrates',$vehicleCrates_data);

					$ord_no3++;

				}

			}

			

			// For Expenses

			

			// Create New ledger

			$ExpSerializedArr = $this->input->post('ExpSerializedArr');

			$EXPArray = json_decode($ExpSerializedArr, true);

			$row_count_exp = $this->input->post('ExpCount');

			$ord_no = 1;

			foreach($EXPArray as $key=>$val){

				$AccountID = $val[0];

				$ExpAmt = $val[1];

				if($ExpAmt !== "0.00" && $ExpAmt !== "0"){

					$expense_detail_result = array(

                    'PlantID'=>$selected_company,

                    'FY' =>$FY,

                    'Transdate' =>$Transdate,

                    'TransDate2' =>date('Y-m-d H:i:s'),

                    'VoucherID' =>$VRtnID,

                    'AccountID' =>$AccountID,

                    'TType' =>'D',

                    'Amount'=>$ExpAmt,

                    'PassedFrom'=>'VEHRTNEXP',

                    'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,

                    'OrdinalNo'=>$ord_no,

                    'UserID'=>$_SESSION['username']

					);

					//print_r($expense_detail_result);

					$data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result);

					if($this->db->affected_rows()>0){

						$affectedRow++;

					}

					

					$expense_detail_result_debit = array(

                    'PlantID'=>$selected_company,

                    'FY' =>$FY,

                    'Transdate' =>$Transdate,

                    'TransDate2' =>date('Y-m-d H:i:s'),

                    'VoucherID' =>$VRtnID,

                    'AccountID' =>'CASH',

                    'TType' =>'C',

                    'Amount'=>$ExpAmt,

                    'PassedFrom'=>'VEHRTNEXP',

                    'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,

                    'OrdinalNo'=>$ord_no,

                    'UserID'=>$_SESSION['username']

					);

					//print_r($expense_detail_result_debit);

					$data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result_debit);

					if($this->db->affected_rows()>0){

						$affectedRow++;

					}

					$ord_no++;

				} 

			}

			

			// For Payment Ledger 

			$PaymentSerializedArr = $this->input->post('PaymentSerializedArr');

			$PayArray = json_decode($PaymentSerializedArr, true);

			$PayCount = $this->input->post('PayCount');

			$ord_no2 = 1;

            foreach($PayArray as $key=>$val){

                $AccountID = $val[0];

                $PayAmt = $val[1];

                if($PayAmt !== "0.00" && $PayAmt !== "0"){

                    $payment_reciept_result = array(

					'PlantID'=>$selected_company,

					'FY' =>$FY,

					'Transdate' =>$Transdate,

					'TransDate2' =>date('Y-m-d H:i:s'),

					'VoucherID' =>$VRtnID,

					'AccountID' =>$AccountID,

					'TType' =>'C',

					'Amount'=>$PayAmt,

					'PassedFrom'=>'VEHRTNPYMTS',

					'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,

					'OrdinalNo'=>$ord_no2,

					"UserID"=>$this->session->userdata('username')

                    );

					//print_r($expense_detail_result);

                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result);

                    

                    if($this->db->affected_rows()>0){

                        $affectedRow++;

					}

                    

                    $payment_reciept_result_debit = array(

					'PlantID'=>$selected_company,

					'FY' =>$FY,

					'Transdate' =>$Transdate,

					'TransDate2' =>date('Y-m-d H:i:s'),

					'VoucherID' =>$VRtnID,

					'AccountID' =>'CASH',

					'TType' =>'D',

					'Amount'=>$PayAmt,

					'PassedFrom'=>'VEHRTNPYMTS',

					'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,

					'OrdinalNo'=>$ord_no2,

					"UserID"=>$this->session->userdata('username')

                    );

                    //print_r($payment_reciept_result_debit);

                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result_debit);

                    if($this->db->affected_rows()>0){

                        $affectedRow++;

					}

                    $ord_no2++;

				}

			}

            

			if($affectedRow > 0){

				if($selected_company == 1){

					$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');

					}elseif($selected_company == 2){

					$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');

					}elseif($selected_company == 3){

					$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');

					}elseif($selected_company == 4){

					$next_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbupl');

				}

				$new_vehicle_return_Numbar = 'VRT'.$FY.$next_vehicle_returnNumber;

				echo json_encode($new_vehicle_return_Numbar);

				die;

				}else{

				echo json_encode(false);

				die;

			}

		}

		

		public function TransportEntryList()

		{

			if (!has_permission_new('TransportEntryList', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Transport Entry List";

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			

			$this->load->view('admin/VehRtn/TransportEntryList', $data);

		}

		

		public function load_data()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'status'  => $this->input->post('status')

			);

			$result = $this->VehRtn_model->load_data($data);

			echo json_encode($result);

		}

		public function GetMileageReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'vehicle'  => $this->input->post('vehicle'),

			'driver'  => $this->input->post('driver'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'Route'  => $this->input->post('Route'),

			'Station'  => $this->input->post('Station'),

			'VehicleType'  => $this->input->post('VehicleType')

			);

			$result = $this->VehRtn_model->GetMileageReport($data);

			$html = '';

			$i=1;

			

			if(count($result)){

				$TotalExpAll = 0;

				$OtherExpAll = 0;

				$FoodExpAll = 0;

				$TotalLoadingRate = 0;

				$TollExpAll = 0;

				$PhoneExpAll = 0;

				$PoliceExpAll = 0;

				$MisExpAll = 0;

				$RepairingExpAll = 0;

				$TotalDieselAll = 0;

				$TotalDistanceTravelAll = 0;

				$TotalDieseValuelAll = 0;

				$TotalAPIDistanceTravelAll = 0;

				foreach($result as $each){

					$OtherExp = $each["Detail"]->Fooding+$each["Detail"]->Toll+$each["Detail"]->Phone+$each["Detail"]->Police+$each["Detail"]->Misc_Expense+$each["Detail"]->Expense_repairing;

					$TotalExp = $OtherExp+$each["Detail"]->Diesel_value;

					

					$TotalExpAll += $TotalExp;

					$OtherExpAll += $OtherExp;

					$TotalLoadingRate += $each["Detail"]->Loading_Rate;

					

					$FoodExpAll += $each["Detail"]->Fooding;

					$TollExpAll += $each["Detail"]->Toll;

					$PhoneExpAll += $each["Detail"]->Phone;

					$PoliceExpAll += $each["Detail"]->Police;

					$MisExpAll  += $each["Detail"]->Misc_Expense;

					$RepairingExpAll += $each["Detail"]->Expense_repairing;

					

					$TotalDieselAll += $each["Detail"]->Diesel;

					$TotalDieseValuelAll += $each["Detail"]->Diesel_value;

					$TotalDistanceTravelAll += $each["Detail"]->DistanceTravel;

					$TotalAPIDistanceTravelAll += $each["Detail"]->APIDistanceTravel;

					

					$date1 = new DateTime($each["gatepasstime"]);

					$date2 = new DateTime($each["Transdate"]);

					$diff = $date1->diff($date2);

					

					// Format the output

					$days    = $diff->d;

					$hours   = $diff->h;

					$minutes = $diff->i;

					$totaltime = "{$days} Day {$hours} Hours {$minutes} Minutes"; 

					$url = admin_url().'VehRtn/AddEditVehicleReturnEntry/'.$each["ReturnID"];

					$html .='<tr>';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["ChallanID"].'</td>';

					$html .='<td>'._d($each["gatepasstime"]).'</td>';

					$html .='<td><a href="'.$url.'" target="_blank">'.$each["ReturnID"].'</a></td>';

					$html .='<td>'._d($each["Transdate"]).'</td>';

					$html .='<td>'.$totaltime.'</td>';

					$html .='<td>'.$each["VehicleID"].'</td>';

					$html .='<td>'.$each["Driver"].'</td>';

					$html .='<td>'.$each["routename"].'</td>';

					$html .='<td>'.$each["Station"].'</td>';

					$html .='<td>'.$each["Detail"]->out_meter_reading.'</td>';

					$html .='<td>'.$each["Detail"]->in_meter_reading.'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->DistanceTravel, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->APIDistanceTravel, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Diesel, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["mileage"], 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Actual_Mileage, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Diesel_value, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Loading_Rate, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Fooding, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Toll, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Phone, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Police, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Misc_Expense, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($each["Detail"]->Expense_repairing, 2, '.', '').'</td>';

					$html .='<td style="text-align:right;">'.number_format($TotalExp, 2, '.', '').'</td>';

					$html .='</tr>';

					$i++;

				}

				

				$html .='<tr>';

				$html .='<td colspan ="12" align="right" style="font-size: 13px;font-weight: 700;">Total</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.$TotalDistanceTravelAll.'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.$TotalAPIDistanceTravelAll.'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.$TotalDieselAll.'</td>';

				$html .='<td></td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($TotalDistanceTravelAll/$TotalDieselAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($TotalDieseValuelAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($TotalLoadingRate, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($FoodExpAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($TollExpAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($PhoneExpAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($PoliceExpAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($MisExpAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($RepairingExpAll, 2, ".", "").'</td>';

				$html .='<td style="text-align:right;font-size: 13px;font-weight: 700;">'.number_format($TotalExpAll, 2, ".", "").'</td>';

				$html .='</tr>';

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		public function GetMileageReportChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'vehicle'  => $this->input->post('vehicle'),

			'driver'  => $this->input->post('driver'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType')

			);

			// $result = $this->VehRtn_model->GetMileageReport($data);

			$result = $this->VehRtn_model->GetMileageReportChart($data);

			

			$chart = [];

			$i=0;

			foreach ($result as $index => $status) {

				$Actual_Mileage = number_format($status['AvgMileage'], 2, '.', '');

				if(empty($Actual_Mileage)){

					$Actual_Mileage = 0;

				}

				array_push($chart, [

				'name' 		=> $status['Driver'],

				'y' 		=>	(float) $Actual_Mileage,

				'z' 		=> 100,

				'label' 		=> "Mileage"

				]);

				$i++;

			}

			

			// return $chart;

			

			echo json_encode($chart);

		}

		public function GetMileageReportChartVehicleWise()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'vehicle'  => $this->input->post('vehicle'),

			'driver'  => $this->input->post('driver'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType')

			);

			// $result = $this->VehRtn_model->GetMileageReport($data);

			$result = $this->VehRtn_model->GetMileageReportChartVehicleWise($data);

			

			$chart = [];

			$i=0;

			foreach ($result as $index => $status) {

				$Actual_Mileage = number_format($status['AvgMileage'], 2, '.', '');

				if(empty($Actual_Mileage)){

					$Actual_Mileage = 0;

				}

				array_push($chart, [

				'name' 		=> $status['VehicleID'],

				'y' 		=>	(float) $Actual_Mileage,

				'z' 		=> 100,

				'label' 		=> "Mileage"

				]);

				$i++;

			}

			

			// return $chart;

			

			echo json_encode($chart);

		}

		

		public function GetDriverOutTimeReportChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'vehicle'  => $this->input->post('vehicle'),

			'driver'  => $this->input->post('driver'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType')

			);

			$result = $this->VehRtn_model->GetDriverOutTimeReportChart($data);

			

			$chart = [];

			$i=0;

			foreach ($result as $index => $status) {

				

				array_push($chart, [

				'name' 		=> $status['Driver'],

				'y' 		=>	(float) $status['TotalHour'],

				'z' 		=> 100,

				'label' 		=> "Hours"

				]);

				$i++;

			}

			

			// return $chart;

			

			echo json_encode($chart);

		}

		public function GetVehicleOutTimeReportChart()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'vehicle'  => $this->input->post('vehicle'),

			'driver'  => $this->input->post('driver'),

			'fuel_type'  => $this->input->post('fuel_type'),

			'Route'  => $this->input->post('Route'),

			'VehicleType'  => $this->input->post('VehicleType')

			);

			$result = $this->VehRtn_model->GetVehicleOutTimeReportChart($data);

			

			$chart = [];

			$i=0;

			foreach ($result as $index => $status) {

				

				array_push($chart, [

				'name' 		=> $status['VehicleID'],

				'y' 		=>	(float) $status['TotalHour'],

				'z' 		=> 100,

				'label' 		=> "Hours"

				]);

				$i++;

			}

			

			// return $chart;

			

			echo json_encode($chart);

		}

		

		public function export_MileageReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				'vehicle'  => $this->input->post('vehicle'),

				'driver'  => $this->input->post('driver'),

				'fuel_type'  => $this->input->post('fuel_type'),

				'Route'  => $this->input->post('Route'),

				'Station'  => $this->input->post('Station'),

				'VehicleType'  => $this->input->post('VehicleType')

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->GetMileageReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Mileage Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["ChallanID"] =  'ChallanID';

				$set_col_tk["Challan Date"] =  'Challan Date';

				$set_col_tk["ReturnID"] =  'ReturnID';

				$set_col_tk["Return Date"] =  'Return Date';

				$set_col_tk["Vehicle No"] =  'Vehicle No';

				$set_col_tk["Driver Name"] =  'Driver Name';

				$set_col_tk["Route"] =  'Route';

				$set_col_tk["Station"] =  'Station';

				$set_col_tk["Out Meter Reading"] =  'Out Meter Reading';

				$set_col_tk["In Meter Reading"] =  'In Meter Reading';

				$set_col_tk["Distance Travel"] =  'Distance Travel';

				$set_col_tk["API Distance Travel"] =  'API Distance Travel';

				$set_col_tk["Diesel In(Ltr)"] =  'Diesel In(Ltr)';

				$set_col_tk["Standard Mileage"] =  'Standard Mileage';

				$set_col_tk["Actual Mileage"] =  'Actual Mileage';

				$set_col_tk["Diesel Value"] =  'Diesel Value';

				$set_col_tk["Fooding Exp."] =  'Fooding Exp.';

				$set_col_tk["Toll Exp."] =  'Toll Exp.';

				$set_col_tk["Phone Exp."] =  'Phone Exp.';

				$set_col_tk["Police Exp."] =  'Police Exp.';

				$set_col_tk["Misc Exp."] =  'Misc Exp.';

				$set_col_tk["Repairing Exp."] =  'Repairing Exp.';

				$set_col_tk["Total Expense"] =  'Total Expense';

				

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$i=1;

                $TotalExpAll = 0;

				$OtherExpAll = 0;

				$FoodExpAll = 0;

				$TollExpAll = 0;

				$PhoneExpAll = 0;

				$PoliceExpAll = 0;

				$MisExpAll = 0;

				$RepairingExpAll = 0;

				$TotalDieselAll = 0;

				$TotalDistanceTravelAll = 0;

				$TotalAPIDistanceTravelAll = 0;

				$TotalDieseValuelAll = 0;

				$OtherExp = 0;

				$TotalExp = 0;

                foreach ($body_data as $each) {

					

					$OtherExp = $each["Detail"]->Fooding+$each["Detail"]->Toll+$each["Detail"]->Phone+$each["Detail"]->Police+$each["Detail"]->Misc_Expense+$each["Detail"]->Expense_repairing;

					$TotalExp = $OtherExp+$each["Detail"]->Diesel_value;

					

					$TotalExpAll += $TotalExp;

					

					

					$FoodExpAll += $each["Detail"]->Fooding;

					$TollExpAll += $each["Detail"]->Toll;

					$PhoneExpAll += $each["Detail"]->Phone;

					$PoliceExpAll += $each["Detail"]->Police;

					$MisExpAll  += $each["Detail"]->Misc_Expense;

					$RepairingExpAll += $each["Detail"]->Expense_repairing;

					

					$OtherExpAll += $OtherExp;

					$TotalDieselAll += $each["Detail"]->Diesel;

					$TotalDieseValuelAll += $each["Detail"]->Diesel_value;

					$TotalDistanceTravelAll += $each["Detail"]->DistanceTravel;

					$TotalAPIDistanceTravelAll += $each["Detail"]->APIDistanceTravel;

					

                    $list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each['ChallanID'];

                    $list_add[] = _d($each['Challandate']);

                    $list_add[] = $each['ReturnID'];

                    $list_add[] = _d($each['Transdate']);

                    $list_add[] = $each['VehicleID'];

                    $list_add[] = $each['Driver'];

                    $list_add[] = $each['routename'];

                    $list_add[] = $each['Station'];

                    $list_add[] = $each["Detail"]->out_meter_reading;

                    $list_add[] = $each["Detail"]->in_meter_reading;

                    $list_add[] = $each["Detail"]->DistanceTravel;

                    $list_add[] = $each["Detail"]->APIDistanceTravel;

                    $list_add[] = $each["Detail"]->Diesel;

                    $list_add[] = number_format($each['mileage'], 2, ".", "");;

                    $list_add[] = $each["Detail"]->Actual_Mileage;

                    $list_add[] = $each["Detail"]->Diesel_value;

                    $list_add[] = $each["Detail"]->Fooding;

                    $list_add[] = $each["Detail"]->Toll;

                    $list_add[] = $each["Detail"]->Phone;

                    $list_add[] = $each["Detail"]->Police;

                    $list_add[] = $each["Detail"]->Misc_Expense;

                    $list_add[] = $each["Detail"]->Expense_repairing;

                    $list_add[] = $TotalExp;

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

                

                $list_add = [];

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = 'Total';

                $list_add[] = $TotalDistanceTravelAll;

                $list_add[] = $TotalAPIDistanceTravelAll;

                $list_add[] = $TotalDieselAll;

                $list_add[] = '';

                $list_add[] = number_format($TotalDistanceTravelAll/$TotalDieselAll, 2, ".", "");

                $list_add[] = number_format($TotalDieseValuelAll, 2, ".", "");

                $list_add[] = $FoodExpAll;

                $list_add[] = $TollExpAll;

                $list_add[] = $PhoneExpAll;

                $list_add[] = $PoliceExpAll;

                $list_add[] = $MisExpAll;

                $list_add[] = $RepairingExpAll;

                $list_add[] = $TotalExpAll;

                $writer->writeSheetRow('Sheet1', $list_add);

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'Mileage_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		public function GetDmgCurrencyReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Customer'  => $this->input->post('Customer'),

			);

			$result = $this->VehRtn_model->GetDmgCurrencyReport($data);

			$html = '';

			$i=1;

			// print_r($result);die;

			if(count($result)){

				$TotalAmount = 0;

				foreach($result as $each){

					

					$TotalAmount += $each["Amount"];

					

					$html .='<tr>';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["company"].'</td>';

					$html .='<td>'.$each["ChallanID"].'</td>';

					$html .='<td>'._d($each["Challandate"]).'</td>';

					$html .='<td>'.$each["ReturnID"].'</td>';

					$html .='<td>'._d($each["ReturnDate"]).'</td>';

					$html .='<td align="right">'.$each["Amount"].'</td>';

					$html .='</tr>';

					$i++;

				}

				

				$html .='<tr>';

				$html .='<td colspan ="6" align="right">Total</td>';

				$html .='<td align="right">'.number_format($TotalAmount, 2, '.', '').'</td>';

				$html .='</tr>';

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		

		public function export_DmgCurrencyReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				'Customer'  => $this->input->post('Customer'),

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->GetDmgCurrencyReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Damage Currency Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["Customer"] =  'Customer';

				$set_col_tk["ChallanID"] =  'ChallanID';

				$set_col_tk["Challan Date"] =  'Challan Date';

				$set_col_tk["ReturnID"] =  'ReturnID';

				$set_col_tk["Return Date"] =  'Return Date';

				$set_col_tk["Amount"] =  'Amount';

				

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$i=1;

                $TotalAmount = 0;

                foreach ($body_data as $each) {

					

					$TotalAmount += $each["Amount"];

					

					

                    $list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each['company'];

                    $list_add[] = $each['ChallanID'];

                    $list_add[] = _d($each['Challandate']);

                    $list_add[] = $each['ReturnID'];

                    $list_add[] = _d($each['ReturnDate']);

                    $list_add[] = $each['Amount'];

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

                

                $list_add = [];

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = '';

                $list_add[] = 'Total';

                $list_add[] = $TotalAmount;

                $writer->writeSheetRow('Sheet1', $list_add);

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'DmgCurrency_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		

		public function TranportEntry($VehRtn)

		{

			if (!has_permission_new('TransportEntry', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Transport Entry";

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			$data['EntryData'] = $this->VehRtn_model->GetDataForTransportEntry($VehRtn);

			$data['EntryDetail'] = $data['EntryData']->Detail;

			// echo "<pre>";

			// print_r($data['EntryData']);

			// die;

			$this->load->view('admin/VehRtn/TranportEntry', $data);

		}

		

		public function SaveTransportEntry()

		{

			$data = array(

            'VehRtn_no'=>$this->input->post('VehRtnNo'),

            'out_meter_reading'=>$this->input->post('out_meter_reading'),

            'in_meter_reading'=>$this->input->post('in_meter_reading'),

            'DistanceTravel'=>$this->input->post('DistanceTravel'),

            'Diesel'=>$this->input->post('Diesel'),

            'Diesel_value'=>$this->input->post('Diesel_value'),

            'Fooding'=>$this->input->post('Fooding'),

            'Toll'=>$this->input->post('Toll'),

            'Phone'=>$this->input->post('Phone'),

            'Police'=>$this->input->post('Police'),

            'Misc_Expense'=>$this->input->post('Misc_Expense'),

            'Expense_repairing'=>$this->input->post('Expense_repairing'),

            'Actual_Mileage'=>$this->input->post('Actual_Mileage'),

			'Transdate'=>date('Y-m-d H:i:s'),

			'UserID'=>$this->session->userdata('username')

			);

			$itemDivision  = $this->VehRtn_model->SaveTransportEntry($data,$this->input->post('VehRtnNo'));

			echo json_encode($itemDivision);

		}

		public function UpdateTransportEntry()

		{

			$data = array(

            'out_meter_reading'=>$this->input->post('out_meter_reading'),

            'in_meter_reading'=>$this->input->post('in_meter_reading'),

            'DistanceTravel'=>$this->input->post('DistanceTravel'),

            'Diesel'=>$this->input->post('Diesel'),

            'Diesel_value'=>$this->input->post('Diesel_value'),

            'Fooding'=>$this->input->post('Fooding'),

            'Toll'=>$this->input->post('Toll'),

            'Phone'=>$this->input->post('Phone'),

            'Police'=>$this->input->post('Police'),

            'Misc_Expense'=>$this->input->post('Misc_Expense'),

            'Expense_repairing'=>$this->input->post('Expense_repairing'),

            'Actual_Mileage'=>$this->input->post('Actual_Mileage'),

			'Lupdate'=>date('Y-m-d H:i:s'),

			'UserID2'=>$this->session->userdata('username')

			);

			$itemDivision  = $this->VehRtn_model->UpdateTransportEntry($data,$this->input->post('VehRtnNo'));

			echo json_encode($itemDivision);

		}

		

		public function SaveOnlyVehRtn()

		{

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			$new_vehicle_returnNumber = get_option('next_vrt_number');

			

			$new_vehicle_return_Numbar = 'VRT' . $FY . $new_vehicle_returnNumber;

			$ChallanID = $this->input->post('challan_n');

			$CheckVehRtnForChallan = $this->VehRtn_model->CheckVehRtnForChallan($ChallanID);

			if (empty($CheckVehRtnForChallan)) {

				$RtnCrates = $this->input->post('refund_crates');

				$vehicle_number = $this->input->post('vehicle_number');

				$Transdate = to_sql_date($this->input->post('from_date')) . " " . date('H:i:s');

				$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

				$affectedRow = 0;

				$vehicleRtn_data = array(

                'PlantID' => $selected_company,

                'ReturnID' => $new_vehicle_return_Numbar,

                'Transdate' => $Transdate,

				'Act_entry_datetime'=>$Act_datetime,

                'ChallanID' => $ChallanID,

                'IsRtnRcvd' => 'Y',

                'UserID' => $_SESSION['username'],

                'FY' => $FY

				);

				

				$this->db->insert(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				if ($this->db->affected_rows() > 0) {

					$this->VehRtn_model->increment_next_number_vehicle();

					$affectedRow++;

				}

				if ($affectedRow > 0) {

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					} else {

					echo json_encode(false);

					die;

				}

				

				} else {

				

				$VRtnID = $CheckVehRtnForChallan->ReturnID;

				$ChallanID = $CheckVehRtnForChallan->ChallanID;

				$RtnCrates = $this->input->post('refund_crates');

				$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

				$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

				$oldDate = $GetVRtnDetails->Transdate;

				

				$vehicleRtn_data = array(

				'UserID2'=>$_SESSION['username'],

                'IsRtnRcvd' => 'Y',

				'Lupdate'=>date('Y-m-d H:i:s')

				);

				

				

				//Insert Records into vehicle Return audit table before updating vehiclereturn table

				$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

				if(!empty($previousVehicleReturnDetails)){

					$insertArray = array(

					'PlantID' =>  $previousVehicleReturnDetails->PlantID,

					'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

					'Transdate' =>  $previousVehicleReturnDetails->Transdate,

					'Crates' =>  $previousVehicleReturnDetails->Crates,

					'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

					'UserID' =>  $previousVehicleReturnDetails->UserID,

					'FY' =>  $previousVehicleReturnDetails->FY,

					'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

					'UserID2' =>  $previousVehicleReturnDetails->UserID2,

					'created_by' =>  $this->session->userdata('username'),

					'created_at' =>  date('Y-m-d H:i:s'),

					);  

					$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

				}

				

				$this->db->where('PlantID', $selected_company);

				$this->db->where('FY', $FY); 

				$this->db->where('ReturnID', $VRtnID);

				$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				

				if($this->db->affected_rows() > 0){

					$affectedRow++;

				}

				if($affectedRow > 0){

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					}else{

					echo json_encode(false);

					die;

				}

			}

			

		}

		public function SaveVehRtnCrates()

		{

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			$new_vehicle_returnNumber = get_option('next_vrt_number');

			

			$new_vehicle_return_Numbar = 'VRT' . $FY . $new_vehicle_returnNumber;

			$ChallanID = $this->input->post('challan_n');

			$CheckVehRtnForChallan = $this->VehRtn_model->CheckVehRtnForChallan($ChallanID);

			

			if (empty($CheckVehRtnForChallan)) {

				$RtnCrates = $this->input->post('refund_crates');

				$vehicle_number = $this->input->post('vehicle_number');

				$Transdate = to_sql_date($this->input->post('from_date')) . " " . date('H:i:s');

				$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

				

				$affectedRow = 0;

				$vehicleRtn_data = array(

                'PlantID' => $selected_company,

                'ReturnID' => $new_vehicle_return_Numbar,

                'Transdate' => $Transdate,

                // 'Act_entry_datetime' => $Act_datetime,

                'Crates' => $RtnCrates,

                'IsCrateRcvd' => 'Y',

                'ChallanID' => $ChallanID,

                'UserID' => $_SESSION['username'],

                'FY' => $FY

				);

				

				$this->db->insert(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				if ($this->db->affected_rows() > 0) {

					$this->VehRtn_model->increment_next_number_vehicle();

					$affectedRow++;

					

					

					// For Crate Ledger

					$CratesSerializedArr = $this->input->post('CratesSerializedArr');

					$CrateArray = json_decode($CratesSerializedArr, true);

					$CrateCount = $this->input->post('CrateCount');

					$ord_no3 = 1;

					foreach($CrateArray as $key=>$val){

						$AccountID = $val[0];

						$RtnCrates = $val[1];

						if ($RtnCrates == "") {

							$RtnCrates = 0;

						}

						if ($RtnCrates != "") {

							$vehicleCrates_data = array(

                            'PlantID' => $selected_company,

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'ChallanID' => $ChallanID,

                            'AccountID' => $AccountID,

                            'TType' => 'C',

                            'Qty' => $RtnCrates,

                            'PassedFrom' => 'VEHRTNCRATES',

                            'Narration' => 'Against VehicleID ' . $new_vehicle_return_Numbar . '/ChallanID /' . $ChallanID,

                            'Ordinalno' => $ord_no3,

                            'UserID' => $_SESSION['username'],

                            'FY' => $FY,

							);

							//print_r($vehicleCrates_data);

							$data_i = $this->db->insert(db_prefix() . 'accountcrates', $vehicleCrates_data);

							$ord_no3++;

						}

					}

				}

				if ($affectedRow > 0) {

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					} else {

					echo json_encode(false);

					die;

				}

				

				} else {

				

				$VRtnID = $CheckVehRtnForChallan->ReturnID;

				$ChallanID = $CheckVehRtnForChallan->ChallanID;

				$RtnCrates = $this->input->post('refund_crates');

				$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

				$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

				$oldDate = $GetVRtnDetails->Transdate;

				if($CheckVehRtnForChallan->IsCrateRcvd == 'Y'){

					$affectedRow = 0;

					$vehicleRtn_data = array(

					'Crates'=>$RtnCrates,

					'UserID2'=>$_SESSION['username'],

					'Lupdate'=>date('Y-m-d H:i:s')

					);

					}else{  

					$affectedRow = 0;

					$vehicleRtn_data = array(

					'Crates'=>$RtnCrates,

					'IsCrateRcvd' => 'Y',

					'UserID2'=>$_SESSION['username'],

					'Lupdate'=>date('Y-m-d H:i:s')

					);

					

				}

				

				//Insert Records into vehicle Return audit table before updating vehiclereturn table

				$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

				if(!empty($previousVehicleReturnDetails)){

					$insertArray = array(

					'PlantID' =>  $previousVehicleReturnDetails->PlantID,

					'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

					'Transdate' =>  $previousVehicleReturnDetails->Transdate,

					'Crates' =>  $previousVehicleReturnDetails->Crates,

					'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

					'UserID' =>  $previousVehicleReturnDetails->UserID,

					'FY' =>  $previousVehicleReturnDetails->FY,

					'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

					'UserID2' =>  $previousVehicleReturnDetails->UserID2,

					'created_by' =>  $this->session->userdata('username'),

					'created_at' =>  date('Y-m-d H:i:s'),

					);  

					$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

				}

				

				$this->db->where('PlantID', $selected_company);

				$this->db->where('FY', $FY); 

				$this->db->where('ReturnID', $VRtnID);

				$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				

				if($this->db->affected_rows() > 0){

					$affectedRow++;

				}

				

				//Insert previous records into new table tblaccountcrates_audit

				$previousAccountCratesDetails = $this->VehRtn_model->GetPreviousAccountCratesDetails("VEHRTNCRATES",$VRtnID);

				foreach($previousAccountCratesDetails as $value){

					$insertArray = array(

					'PlantID'=>$value['PlantID'],

					'FY'=>$value['FY'],

					'VoucherID' =>$value['VoucherID'],

					'Transdate' =>$value['Transdate'],

					'TransDate2' =>$value['TransDate2'],

					'ChallanID' =>$value['ChallanID'],

					'AccountID' =>$value['AccountID'],

					'TType' =>$value['TType'],

					'Qty'=>$value['Qty'],

					'PassedFrom'=>$value['PassedFrom'],

					'Narration'=> $value['Narration'],

					'Ordinalno'=>$value['Ordinalno'],

					'UserID'=>$value['UserID'],

					'UserID2'=>$value['UserID2'],

					'Lupdate'=>$value['Lupdate'],

					'created_by'=>$this->session->userdata('username'),

					'created_at'=>date('Y-m-d H:i:s'),

					);  

					$data_i = $this->db->insert(db_prefix() . 'accountcrates_audit',$insertArray);

				}

				

				// For Crate Ledger

				// Delete Previoud ledger

				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $FY);

				$this->db->where('VoucherID', $VRtnID);

				$this->db->delete(db_prefix() . 'accountcrates');

				

				// Create New

				$CratesSerializedArr = $this->input->post('CratesSerializedArr');

				$CrateArray = json_decode($CratesSerializedArr, true); 

				$CrateCount = $this->input->post('CrateCount');

				$ord_no3 = 1;

				foreach($CrateArray as $key=>$kay){

					$AccountID = $kay[0];

					$RtnCrates = $kay[1];

					if ($RtnCrates == "") {

						$RtnCrates = 0;

					}

					if($RtnCrates !=""){

						$vehicleCrates_data = array(

						'PlantID'=>$selected_company,

						'VoucherID' =>$VRtnID,

						'Transdate' =>date('Y-m-d H:i:s'),

						'TransDate2' =>date('Y-m-d H:i:s'),

						'ChallanID' =>$ChallanID,

						'AccountID' =>$AccountID,

						'TType' =>'C',

						'Qty'=>$RtnCrates,

						'PassedFrom'=>'VEHRTNCRATES',

						'Narration'=> 'Against VehicleID '.$VRtnID.'/ChallanID /'.$ChallanID,

						'Ordinalno'=>$ord_no3,

						'UserID'=>$_SESSION['username'],

						'FY'=>$FY,

						);

						$data_i = $this->db->insert(db_prefix() . 'accountcrates',$vehicleCrates_data);

						$ord_no3++;

					}

				}

				

				if($affectedRow > 0){

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					}else{

					echo json_encode(false);

					die;

				}

			}

			

		}

		public function SaveVehRtnPaymentReceipt()

		{

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			$new_vehicle_returnNumber = get_option('next_vrt_number');

			

			$new_vehicle_return_Numbar = 'VRT' . $FY . $new_vehicle_returnNumber;

			$ChallanID = $this->input->post('challan_n');

			$CheckVehRtnForChallan = $this->VehRtn_model->CheckVehRtnForChallan($ChallanID);

			if (empty($CheckVehRtnForChallan)) {

				$vehicle_number = $this->input->post('vehicle_number');

				$Transdate = to_sql_date($this->input->post('from_date')) . " " . date('H:i:s');

				$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

				$affectedRow = 0;

				$vehicleRtn_data = array(

                'PlantID' => $selected_company,

                'ReturnID' => $new_vehicle_return_Numbar,

                'Transdate' => $Transdate,

				// 'Act_entry_datetime' => $Act_datetime,

                'IsPaymentRcvd' => 'Y',

                'ChallanID' => $ChallanID,

                'UserID' => $_SESSION['username'],

                'FY' => $FY

				);

				

				$this->db->insert(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				if ($this->db->affected_rows() > 0) {

					$this->VehRtn_model->increment_next_number_vehicle();

					$affectedRow++;

					

					// For Payment Ledger 

					$PaymentSerializedArr = $this->input->post('PaymentSerializedArr');

					$PayArray = json_decode($PaymentSerializedArr, true);

					$PayCount = $this->input->post('PayCount');

					$ord_no2 = 1;

					foreach($PayArray as $key=>$val){

						$AccountID = $val[0];

						$PayAmt = $val[1];

						$DmgAmt = $val[2];

						if ($PayAmt !== "0.00" && $PayAmt !== "0") {

							$payment_reciept_result = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => $AccountID,

                            'TType' => 'C',

                            'Amount' => $PayAmt,

                            'PassedFrom' => 'VEHRTNPYMTS',

                            'Narration' => 'Cash Received/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no2,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($expense_detail_result);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $payment_reciept_result);

							

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							

							$payment_reciept_result_debit = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => 'CASH',

                            'TType' => 'D',

                            'Amount' => $PayAmt,

                            'PassedFrom' => 'VEHRTNPYMTS',

                            'Narration' => 'Cash Received/VehicleReturn ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no2,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($payment_reciept_result_debit);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $payment_reciept_result_debit);

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							$ord_no2++;

							

							if ($DmgAmt !== "0.00" && $DmgAmt !== "0" && $DmgAmt !== "" ) {

								$DamageCurrency = array(

								'ChallanID' => $ChallanID,

								'ReturnID' => $new_vehicle_return_Numbar,

								'TransDate' => $Transdate,

								'AccountID' => $AccountID,

								'Amount' => $DmgAmt,

								'UserID' => $_SESSION['username'],

								);

								$data_i = $this->db->insert(db_prefix() . 'DamageCurrency', $DamageCurrency);

							}

						}

					}

				}

				if ($affectedRow > 0) {

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					} else {

					echo json_encode(false);

					die;

				}

				

				} else {

				$PaymentSerializedArr = $this->input->post('PaymentSerializedArr');

				$VRtnID = $CheckVehRtnForChallan->ReturnID;

				$ChallanID = $CheckVehRtnForChallan->ChallanID;

				$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

				$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

				$oldDate = $GetVRtnDetails->Transdate;

				if($CheckVehRtnForChallan->IsPaymentRcvd == 'Y'){

					$affectedRow = 0;

					$vehicleRtn_data = array(

					'UserID2'=>$_SESSION['username'],

					'Lupdate'=>date('Y-m-d H:i:s')

					);

					}else{  

					$affectedRow = 0;

					$vehicleRtn_data = array(

					'IsPaymentRcvd' => 'Y',

					'UserID2'=>$_SESSION['username'],

					'Lupdate'=>date('Y-m-d H:i:s')

					);

					

				}

				

				

				//Insert Records into vehicle Return audit table before updating vehiclereturn table

				$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

				if(!empty($previousVehicleReturnDetails)){

					$insertArray = array(

					'PlantID' =>  $previousVehicleReturnDetails->PlantID,

					'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

					'Transdate' =>  $previousVehicleReturnDetails->Transdate,

					'Crates' =>  $previousVehicleReturnDetails->Crates,

					'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

					'UserID' =>  $previousVehicleReturnDetails->UserID,

					'FY' =>  $previousVehicleReturnDetails->FY,

					'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

					'UserID2' =>  $previousVehicleReturnDetails->UserID2,

					'created_by' =>  $this->session->userdata('username'),

					'created_at' =>  date('Y-m-d H:i:s'),

					);  

					$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

				}

				

				$this->db->where('PlantID', $selected_company);

				$this->db->where('FY', $FY); 

				$this->db->where('ReturnID', $VRtnID);

				$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				

				if($this->db->affected_rows() > 0){

					$affectedRow++;

				}

				// Delete and Revert balances from privous ledger to ladger audit

				$GetPreLedger = $this->VehRtn_model->GetPreLedgerPaymentReceipt($VRtnID);   

				foreach($GetPreLedger as $key => $value)

				{

					$ledger_audit = array(

					"PlantID"=>$value["PlantID"],

					"FY"=>$value["FY"],

					"Transdate"=>$value["Transdate"],

					"TransDate2"=>$value["TransDate2"],

					"VoucherID"=>$value["VoucherID"],

					"AccountID"=>$value["AccountID"],

					"TType"=>$value["TType"],

					"Amount"=>$value["Amount"],

					"Narration"=>$value["Narration"],

					"PassedFrom"=>$value["PassedFrom"],

					"OrdinalNo"=>$value["OrdinalNo"],

					"UserID"=>$value["UserID"],

					"UserID2"=>$this->session->userdata('username'),

					"Lupdate"=>date('Y-m-d H:i:s')

					);

					$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);

					$UserID1 = $value["UserID"];

				}

				// Delete all record related to vehicle return

				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $FY);

				$this->db->where('VoucherID', $VRtnID);

				$this->db->where('PassedFrom', 'VEHRTNPYMTS');

				$this->db->delete(db_prefix() . 'accountledger');

				

				// Delete Damage Currency

				$this->db->where('ReturnID', $VRtnID);

				$this->db->delete(db_prefix() . 'DamageCurrency');

				

				// For Payment Ledger 

				$PaymentSerializedArr = $this->input->post('PaymentSerializedArr');

				$PayArray = json_decode($PaymentSerializedArr, true);

				$PayCount = $this->input->post('PayCount');

				$ord_no2 = 1;

				foreach($PayArray as $key=>$val){

					$AccountID = $val[0];

					$PayAmt = $val[1];

					$DmgAmt = $val[2];

					if($PayAmt !== "0.00" && $PayAmt !== "0"){

						$payment_reciept_result = array(

						'PlantID'=>$selected_company,

						'FY' =>$FY,

						'Transdate' =>date('Y-m-d H:i:s'),

						'TransDate2' =>date('Y-m-d H:i:s'),

						'VoucherID' =>$VRtnID,

						'AccountID' =>$AccountID,

						'TType' =>'C',

						'Amount'=>$PayAmt,

						'PassedFrom'=>'VEHRTNPYMTS',

						'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,

						'OrdinalNo'=>$ord_no2,

						"UserID"=>$this->session->userdata('username')

						);

						//print_r($expense_detail_result);

						$data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result);

						

						if($this->db->affected_rows()>0){

							$affectedRow++;

						}

						

						$payment_reciept_result_debit = array(

						'PlantID'=>$selected_company,

						'FY' =>$FY,

						'Transdate' =>date('Y-m-d H:i:s'),

						'TransDate2' =>date('Y-m-d H:i:s'),

						'VoucherID' =>$VRtnID,

						'AccountID' =>'CASH',

						'TType' =>'D',

						'Amount'=>$PayAmt,

						'PassedFrom'=>'VEHRTNPYMTS',

						'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,

						'OrdinalNo'=>$ord_no2,

						"UserID"=>$this->session->userdata('username')

						);

						//print_r($payment_reciept_result_debit);

						$data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result_debit);

						if($this->db->affected_rows()>0){

							$affectedRow++;

						}

						$ord_no2++;

						

						if ($DmgAmt !== "0.00" && $DmgAmt !== "0" && $DmgAmt !== "" ) {

							$DamageCurrency = array(

							'ChallanID' => $ChallanID,

							'ReturnID' => $VRtnID,

							'TransDate' => date('Y-m-d H:i:s'),

							'AccountID' => $AccountID,

							'Amount' => $DmgAmt,

							'UserID' => $_SESSION['username'],

							);

							$data_i = $this->db->insert(db_prefix() . 'DamageCurrency', $DamageCurrency);

						}

					}

				}

				

				if($affectedRow > 0){

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					}else{

					echo json_encode(false);

					die;

				}

			}

			

		}

		public function SaveVehRtnExpense()

		{

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			$new_vehicle_returnNumber = get_option('next_vrt_number');

			

			$new_vehicle_return_Numbar = 'VRT' . $FY . $new_vehicle_returnNumber;

			$ChallanID = $this->input->post('challan_n');

			$CheckVehRtnForChallan = $this->VehRtn_model->CheckVehRtnForChallan($ChallanID);

			if (empty($CheckVehRtnForChallan)) {

				$RtnCrates = $this->input->post('refund_crates');

				$vehicle_number = $this->input->post('vehicle_number');

				$Transdate = to_sql_date($this->input->post('from_date')) . " " . date('H:i:s');

				$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

				$affectedRow = 0;

				$vehicleRtn_data = array(

                'PlantID' => $selected_company,

                'ReturnID' => $new_vehicle_return_Numbar,

                'Transdate' => $Transdate,

				// 'Act_entry_datetime' => $Act_datetime,

                'IsExpRcvd' => 'Y',

                'ChallanID' => $ChallanID,

                'UserID' => $_SESSION['username'],

                'FY' => $FY

				);

				

				$this->db->insert(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				if ($this->db->affected_rows() > 0) {

					$this->VehRtn_model->increment_next_number_vehicle();

					$affectedRow++;

					

					// For Expenses

					$ExpSerializedArr = $this->input->post('ExpSerializedArr');

					$EXPArray = json_decode($ExpSerializedArr, true);

					$row_count_exp = $this->input->post('ExpCount');

					$ord_no = 1;

					foreach($EXPArray as $Key=>$val){

						$AccountID = $val[0];

						$ExpAmt = $val[1];

						if ($ExpAmt !== "0.00" && $ExpAmt !== "0") {

							$expense_detail_result = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => $AccountID,

                            'TType' => 'D',

                            'Amount' => $ExpAmt,

                            'PassedFrom' => 'VEHRTNEXP',

                            'Narration' => 'By Vehicle Expense ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($expense_detail_result);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $expense_detail_result);

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							

							$expense_detail_result_debit = array(

                            'PlantID' => $selected_company,

                            'FY' => $FY,

                            'Transdate' => $Transdate,

                            'TransDate2' => date('Y-m-d H:i:s'),

                            'VoucherID' => $new_vehicle_return_Numbar,

                            'AccountID' => 'CASH',

                            'TType' => 'C',

                            'Amount' => $ExpAmt,

                            'PassedFrom' => 'VEHRTNEXP',

                            'Narration' => 'By Vehicle Expense ' . $new_vehicle_return_Numbar . '/' . $ChallanID,

                            'OrdinalNo' => $ord_no,

                            'UserID' => $_SESSION['username'],

							);

							//print_r($expense_detail_result_debit);

							$data_i = $this->db->insert(db_prefix() . 'accountledger', $expense_detail_result_debit);

							if ($this->db->affected_rows() > 0) {

								$affectedRow++;

							}

							$ord_no++;

						}

					}

				}

				if ($affectedRow > 0) {

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					} else {

					echo json_encode(false);

					die;

				}

				

				} else {

				

				$VRtnID = $CheckVehRtnForChallan->ReturnID;

				$ChallanID = $CheckVehRtnForChallan->ChallanID;

				$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

				$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

				$oldDate = $GetVRtnDetails->Transdate;

				if($CheckVehRtnForChallan->IsExpRcvd == 'Y'){

					$affectedRow = 0;

					$vehicleRtn_data = array(

					'UserID2'=>$_SESSION['username'],

					'Lupdate'=>date('Y-m-d H:i:s')

					);

					}else{  

					$affectedRow = 0;

					$vehicleRtn_data = array(

					'IsExpRcvd' => 'Y',

					'UserID2'=>$_SESSION['username'],

					'Lupdate'=>date('Y-m-d H:i:s')

					);

					

				}

				

				

				//Insert Records into vehicle Return audit table before updating vehiclereturn table

				$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

				if(!empty($previousVehicleReturnDetails)){

					$insertArray = array(

					'PlantID' =>  $previousVehicleReturnDetails->PlantID,

					'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

					'Transdate' =>  $previousVehicleReturnDetails->Transdate,

					'Crates' =>  $previousVehicleReturnDetails->Crates,

					'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

					'UserID' =>  $previousVehicleReturnDetails->UserID,

					'FY' =>  $previousVehicleReturnDetails->FY,

					'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

					'UserID2' =>  $previousVehicleReturnDetails->UserID2,

					'created_by' =>  $this->session->userdata('username'),

					'created_at' =>  date('Y-m-d H:i:s'),

					);  

					$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

				}

				

				$this->db->where('PlantID', $selected_company);

				$this->db->where('FY', $FY); 

				$this->db->where('ReturnID', $VRtnID);

				$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				

				if($this->db->affected_rows() > 0){

					$affectedRow++;

				}

				// Delete and Revert balances from privous ledger to ladger audit

				$GetPreLedger = $this->VehRtn_model->GetPreLedgerExpense($VRtnID);   

				foreach($GetPreLedger as $key => $value)

				{

					$ledger_audit = array(

					"PlantID"=>$value["PlantID"],

					"FY"=>$value["FY"],

					"Transdate"=>$value["Transdate"],

					"TransDate2"=>$value["TransDate2"],

					"VoucherID"=>$value["VoucherID"],

					"AccountID"=>$value["AccountID"],

					"TType"=>$value["TType"],

					"Amount"=>$value["Amount"],

					"Narration"=>$value["Narration"],

					"PassedFrom"=>$value["PassedFrom"],

					"OrdinalNo"=>$value["OrdinalNo"],

					"UserID"=>$value["UserID"],

					"UserID2"=>$this->session->userdata('username'),

					"Lupdate"=>date('Y-m-d H:i:s')

					);

					$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);

					$UserID1 = $value["UserID"];

				}

				// Delete all record related to vehicle return

				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $FY);

				$this->db->where('VoucherID', $VRtnID);

				$this->db->where('PassedFrom', 'VEHRTNEXP');

				$this->db->delete(db_prefix() . 'accountledger');

				

				// Create New ledger

				$ExpSerializedArr = $this->input->post('ExpSerializedArr');

				$EXPArray = json_decode($ExpSerializedArr, true);

				$row_count_exp = $this->input->post('ExpCount');

				$ord_no = 1;

				foreach($EXPArray as $key=>$val){

					$AccountID = $val[0];

					$ExpAmt = $val[1];

					if($ExpAmt !== "0.00" && $ExpAmt !== "0"){

						$expense_detail_result = array(

						'PlantID'=>$selected_company,

						'FY' =>$FY,

						'Transdate' =>date('Y-m-d H:i:s'),

						'TransDate2' =>date('Y-m-d H:i:s'),

						'VoucherID' =>$VRtnID,

						'AccountID' =>$AccountID,

						'TType' =>'D',

						'Amount'=>$ExpAmt,

						'PassedFrom'=>'VEHRTNEXP',

						'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,

						'OrdinalNo'=>$ord_no,

						'UserID'=>$_SESSION['username']

						);

						//print_r($expense_detail_result);

						$data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result);

						if($this->db->affected_rows()>0){

							$affectedRow++;

						}

						

						$expense_detail_result_debit = array(

						'PlantID'=>$selected_company,

						'FY' =>$FY,

						'Transdate' =>date('Y-m-d H:i:s'),

						'TransDate2' =>date('Y-m-d H:i:s'),

						'VoucherID' =>$VRtnID,

						'AccountID' =>'CASH',

						'TType' =>'C',

						'Amount'=>$ExpAmt,

						'PassedFrom'=>'VEHRTNEXP',

						'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,

						'OrdinalNo'=>$ord_no,

						'UserID'=>$_SESSION['username']

						);

						//print_r($expense_detail_result_debit);

						$data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result_debit);

						if($this->db->affected_rows()>0){

							$affectedRow++;

						}

						$ord_no++;

					} 

				}

				

				

				if($affectedRow > 0){

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					}else{

					echo json_encode(false);

					die;

				}

				// echo json_encode('Created');

				// die;

			}

			

		}

		

		

		public function UpdateVehRtnExpense()

		{

			$selected_company = $this->session->userdata('root_company');

			

			$FY = $this->session->userdata('finacial_year');

			$ChallanID = $this->input->post('challan_n');

			$RtnCrates = $this->input->post('refund_crates');

			$vehicle_number = $this->input->post('vehicle_number');

			$VRtnID = $this->input->post('VRtnID');

			$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

			$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

			$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

			$oldDate = $GetVRtnDetails->Transdate;

			$affectedRow = 0;

			$vehicleRtn_data = array(

            'Transdate'=>$Transdate,

			// 'Act_entry_datetime' => $Act_datetime,

			'IsExpRcvd' => 'Y',

            'UserID2'=>$_SESSION['username'],

            'Lupdate'=>date('Y-m-d H:i:s')

			);

			

			

			//Insert Records into vehicle Return audit table before updating vehiclereturn table

			$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

			if(!empty($previousVehicleReturnDetails)){

				$insertArray = array(

                'PlantID' =>  $previousVehicleReturnDetails->PlantID,

                'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

                'Transdate' =>  $previousVehicleReturnDetails->Transdate,

                'Crates' =>  $previousVehicleReturnDetails->Crates,

                'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

                'UserID' =>  $previousVehicleReturnDetails->UserID,

                'FY' =>  $previousVehicleReturnDetails->FY,

                'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

                'UserID2' =>  $previousVehicleReturnDetails->UserID2,

                'created_by' =>  $this->session->userdata('username'),

                'created_at' =>  date('Y-m-d H:i:s'),

				);  

				$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

			}

			

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $FY); 

			$this->db->where('ReturnID', $VRtnID);

			$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

			

			if($this->db->affected_rows() > 0){

				$affectedRow++;

			}

			// Delete and Revert balances from privous ledger to ladger audit

			$GetPreLedger = $this->VehRtn_model->GetPreLedgerExpense($VRtnID);   

			foreach($GetPreLedger as $key => $value)

			{

				$ledger_audit = array(

                "PlantID"=>$value["PlantID"],

                "FY"=>$value["FY"],

                "Transdate"=>$value["Transdate"],

                "TransDate2"=>$value["TransDate2"],

                "VoucherID"=>$value["VoucherID"],

                "AccountID"=>$value["AccountID"],

                "TType"=>$value["TType"],

                "Amount"=>$value["Amount"],

                "Narration"=>$value["Narration"],

                "PassedFrom"=>$value["PassedFrom"],

                "OrdinalNo"=>$value["OrdinalNo"],

                "UserID"=>$value["UserID"],

                "UserID2"=>$this->session->userdata('username'),

                "Lupdate"=>date('Y-m-d H:i:s')

				);

				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);

				$UserID1 = $value["UserID"];

			}

			// Delete all record related to vehicle return

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $FY);

			$this->db->where('VoucherID', $VRtnID);

			$this->db->where('PassedFrom', 'VEHRTNEXP');

			$this->db->delete(db_prefix() . 'accountledger');

			

			// Create New ledger

			$ExpSerializedArr = $this->input->post('ExpSerializedArr');

			$EXPArray = json_decode($ExpSerializedArr, true);

			$row_count_exp = $this->input->post('ExpCount');

			$ord_no = 1;

			foreach($EXPArray as $key=>$val){

				$AccountID = $val[0];

				$ExpAmt = $val[1];

				if($ExpAmt !== "0.00" && $ExpAmt !== "0"){

					$expense_detail_result = array(

                    'PlantID'=>$selected_company,

                    'FY' =>$FY,

                    'Transdate' =>$Transdate,

                    'TransDate2' =>date('Y-m-d H:i:s'),

                    'VoucherID' =>$VRtnID,

                    'AccountID' =>$AccountID,

                    'TType' =>'D',

                    'Amount'=>$ExpAmt,

                    'PassedFrom'=>'VEHRTNEXP',

                    'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,

                    'OrdinalNo'=>$ord_no,

                    'UserID'=>$_SESSION['username']

					);

					//print_r($expense_detail_result);

					$data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result);

					if($this->db->affected_rows()>0){

						$affectedRow++;

					}

					

					$expense_detail_result_debit = array(

                    'PlantID'=>$selected_company,

                    'FY' =>$FY,

                    'Transdate' =>$Transdate,

                    'TransDate2' =>date('Y-m-d H:i:s'),

                    'VoucherID' =>$VRtnID,

                    'AccountID' =>'CASH',

                    'TType' =>'C',

                    'Amount'=>$ExpAmt,

                    'PassedFrom'=>'VEHRTNEXP',

                    'Narration'=> 'By Vehicle Expense '.$VRtnID.'/'.$ChallanID,

                    'OrdinalNo'=>$ord_no,

                    'UserID'=>$_SESSION['username']

					);

					//print_r($expense_detail_result_debit);

					$data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result_debit);

					if($this->db->affected_rows()>0){

						$affectedRow++;

					}

					$ord_no++;

				} 

			}

			

			

			if($affectedRow > 0){

				$next_vehicle_returnNumber = get_option('next_vrt_number');

				

				$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

				echo json_encode($new_vehicle_return_Numbar);

				die;

				}else{

				echo json_encode(false);

				die;

			}

		}

		public function UpdateVehRtnPaymentReceipt()

		{

			$selected_company = $this->session->userdata('root_company');

			

			$FY = $this->session->userdata('finacial_year');

			$ChallanID = $this->input->post('challan_n');

			$RtnCrates = $this->input->post('refund_crates');

			$vehicle_number = $this->input->post('vehicle_number');

			$VRtnID = $this->input->post('VRtnID');

			$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

			$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

			$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

			$oldDate = $GetVRtnDetails->Transdate;

			$affectedRow = 0;

			$vehicleRtn_data = array(

            'Transdate'=>$Transdate,

			// 'Act_entry_datetime' => $Act_datetime,

			'IsPaymentRcvd' => 'Y',

            'UserID2'=>$_SESSION['username'],

            'Lupdate'=>date('Y-m-d H:i:s')

			);

			

			

			//Insert Records into vehicle Return audit table before updating vehiclereturn table

			$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

			if(!empty($previousVehicleReturnDetails)){

				$insertArray = array(

                'PlantID' =>  $previousVehicleReturnDetails->PlantID,

                'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

                'Transdate' =>  $previousVehicleReturnDetails->Transdate,

                'Crates' =>  $previousVehicleReturnDetails->Crates,

                'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

                'UserID' =>  $previousVehicleReturnDetails->UserID,

                'FY' =>  $previousVehicleReturnDetails->FY,

                'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

                'UserID2' =>  $previousVehicleReturnDetails->UserID2,

                'created_by' =>  $this->session->userdata('username'),

                'created_at' =>  date('Y-m-d H:i:s'),

				);  

				$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

			}

			

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $FY); 

			$this->db->where('ReturnID', $VRtnID);

			$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

			

			if($this->db->affected_rows() > 0){

				$affectedRow++;

			}

			// Delete and Revert balances from privous ledger to ladger audit

			$GetPreLedger = $this->VehRtn_model->GetPreLedgerPaymentReceipt($VRtnID);   

			foreach($GetPreLedger as $key => $value)

			{

				$ledger_audit = array(

                "PlantID"=>$value["PlantID"],

                "FY"=>$value["FY"],

                "Transdate"=>$value["Transdate"],

                "TransDate2"=>$value["TransDate2"],

                "VoucherID"=>$value["VoucherID"],

                "AccountID"=>$value["AccountID"],

                "TType"=>$value["TType"],

                "Amount"=>$value["Amount"],

                "Narration"=>$value["Narration"],

                "PassedFrom"=>$value["PassedFrom"],

                "OrdinalNo"=>$value["OrdinalNo"],

                "UserID"=>$value["UserID"],

                "UserID2"=>$this->session->userdata('username'),

                "Lupdate"=>date('Y-m-d H:i:s')

				);

				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);

				$UserID1 = $value["UserID"];

			}

			// Delete all record related to vehicle return

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $FY);

			$this->db->where('VoucherID', $VRtnID);

			$this->db->where('PassedFrom', 'VEHRTNPYMTS');

			$this->db->delete(db_prefix() . 'accountledger');

			

			

			// Delete Damage Currency

			$this->db->where('ReturnID', $VRtnID);

			$this->db->delete(db_prefix() . 'DamageCurrency');

			

			// For Payment Ledger 

			$PaymentSerializedArr = $this->input->post('PaymentSerializedArr');

			$PayArray = json_decode($PaymentSerializedArr, true);

			$PayCount = $this->input->post('PayCount');

			$ord_no2 = 1;

            foreach($PayArray as $key=>$val){

                $AccountID = $val[0];

                $PayAmt = $val[1];

                $DmgAmt = $val[2];

                if($PayAmt !== "0.00" && $PayAmt !== "0"){

                    $payment_reciept_result = array(

					'PlantID'=>$selected_company,

					'FY' =>$FY,

					'Transdate' =>$Transdate,

					'TransDate2' =>date('Y-m-d H:i:s'),

					'VoucherID' =>$VRtnID,

					'AccountID' =>$AccountID,

					'TType' =>'C',

					'Amount'=>$PayAmt,

					'PassedFrom'=>'VEHRTNPYMTS',

					'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,

					'OrdinalNo'=>$ord_no2,

					"UserID"=>$this->session->userdata('username')

                    );

					//print_r($expense_detail_result);

                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result);

                    

                    if($this->db->affected_rows()>0){

                        $affectedRow++;

					}

                    

                    $payment_reciept_result_debit = array(

					'PlantID'=>$selected_company,

					'FY' =>$FY,

					'Transdate' =>$Transdate,

					'TransDate2' =>date('Y-m-d H:i:s'),

					'VoucherID' =>$VRtnID,

					'AccountID' =>'CASH',

					'TType' =>'D',

					'Amount'=>$PayAmt,

					'PassedFrom'=>'VEHRTNPYMTS',

					'Narration'=> 'Cash Received/VehicleReturn '.$VRtnID.'/'.$ChallanID,

					'OrdinalNo'=>$ord_no2,

					"UserID"=>$this->session->userdata('username')

                    );

                    //print_r($payment_reciept_result_debit);

                    $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result_debit);

                    if($this->db->affected_rows()>0){

                        $affectedRow++;

					}

                    $ord_no2++;

					

					if ($DmgAmt !== "0.00" && $DmgAmt !== "0" && $DmgAmt !== "" ) {

						$DamageCurrency = array(

						'ChallanID' => $ChallanID,

						'ReturnID' => $VRtnID,

						'TransDate' => $Transdate,

						'AccountID' => $AccountID,

						'Amount' => $DmgAmt,

						'UserID' => $_SESSION['username'],

						);

						$data_i = $this->db->insert(db_prefix() . 'DamageCurrency', $DamageCurrency);

					}

				}

			}

			

			if($affectedRow > 0){

				$next_vehicle_returnNumber = get_option('next_vrt_number');

				

				$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

				echo json_encode($new_vehicle_return_Numbar);

				die;

				}else{

				echo json_encode(false);

				die;

			}

		}

		public function UpdateVehRtnCrates()

		{

			// print_r($this->input->post());die;

			$selected_company = $this->session->userdata('root_company');

			

			$CratesSerializedArr = $this->input->post('CratesSerializedArr');

			$CrateArray = json_decode($CratesSerializedArr, true); 

			// print_r($CrateArray);die;

			$FY = $this->session->userdata('finacial_year');

			$ChallanID = $this->input->post('challan_n');

			$RtnCrates = $this->input->post('refund_crates');

			$vehicle_number = $this->input->post('vehicle_number');

			$VRtnID = $this->input->post('VRtnID');

			$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

			$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

			$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

			$oldDate = $GetVRtnDetails->Transdate;

			$affectedRow = 0;

			$vehicleRtn_data = array(

            'Transdate'=>$Transdate,

			// 'Act_entry_datetime' => $Act_datetime,

            'Crates'=>$RtnCrates,

			'IsCrateRcvd' => 'Y',

            'UserID2'=>$_SESSION['username'],

            'Lupdate'=>date('Y-m-d H:i:s')

			);

			

			

			//Insert Records into vehicle Return audit table before updating vehiclereturn table

			$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

			if(!empty($previousVehicleReturnDetails)){

				$insertArray = array(

                'PlantID' =>  $previousVehicleReturnDetails->PlantID,

                'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

                'Transdate' =>  $previousVehicleReturnDetails->Transdate,

                'Crates' =>  $previousVehicleReturnDetails->Crates,

                'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

                'UserID' =>  $previousVehicleReturnDetails->UserID,

                'FY' =>  $previousVehicleReturnDetails->FY,

                'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

                'UserID2' =>  $previousVehicleReturnDetails->UserID2,

                'created_by' =>  $this->session->userdata('username'),

                'created_at' =>  date('Y-m-d H:i:s'),

				);  

				$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

			}

			

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $FY); 

			$this->db->where('ReturnID', $VRtnID);

			$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

			

			if($this->db->affected_rows() > 0){

				$affectedRow++;

			}

			

			//Insert previous records into new table tblaccountcrates_audit

			$previousAccountCratesDetails = $this->VehRtn_model->GetPreviousAccountCratesDetails("VEHRTNCRATES",$VRtnID);

			foreach($previousAccountCratesDetails as $value){

				$insertArray = array(

				'PlantID'=>$value['PlantID'],

				'FY'=>$value['FY'],

				'VoucherID' =>$value['VoucherID'],

				'Transdate' =>$value['Transdate'],

				'TransDate2' =>$value['TransDate2'],

				'ChallanID' =>$value['ChallanID'],

				'AccountID' =>$value['AccountID'],

				'TType' =>$value['TType'],

				'Qty'=>$value['Qty'],

				'PassedFrom'=>$value['PassedFrom'],

				'Narration'=> $value['Narration'],

				'Ordinalno'=>$value['Ordinalno'],

				'UserID'=>$value['UserID'],

				'UserID2'=>$value['UserID2'],

				'Lupdate'=>$value['Lupdate'],

				'created_by'=>$this->session->userdata('username'),

				'created_at'=>date('Y-m-d H:i:s'),

				);  

				$data_i = $this->db->insert(db_prefix() . 'accountcrates_audit',$insertArray);

			}

			

			// For Crate Ledger

			// Delete Previoud ledger

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $FY);

			$this->db->where('VoucherID', $VRtnID);

			$this->db->delete(db_prefix() . 'accountcrates');

			

			// Create New

			$CratesSerializedArr = $this->input->post('CratesSerializedArr');

			$CrateArray = json_decode($CratesSerializedArr, true); 

			$CrateCount = $this->input->post('CrateCount');

			$ord_no3 = 1;

			foreach($CrateArray as $key=>$kay){

				$AccountID = $kay[0];

				$RtnCrates = $kay[1];

				if ($RtnCrates == "") {

					$RtnCrates = 0;

				}

				if($RtnCrates !=""){

					$vehicleCrates_data = array(

                    'PlantID'=>$selected_company,

                    'VoucherID' =>$VRtnID,

                    'Transdate' =>$Transdate,

                    'TransDate2' =>date('Y-m-d H:i:s'),

                    'ChallanID' =>$ChallanID,

                    'AccountID' =>$AccountID,

                    'TType' =>'C',

                    'Qty'=>$RtnCrates,

                    'PassedFrom'=>'VEHRTNCRATES',

                    'Narration'=> 'Against VehicleID '.$VRtnID.'/ChallanID /'.$ChallanID,

                    'Ordinalno'=>$ord_no3,

                    'UserID'=>$_SESSION['username'],

                    'FY'=>$FY,

					);

					$data_i = $this->db->insert(db_prefix() . 'accountcrates',$vehicleCrates_data);

					$ord_no3++;

				}

			}

			

			if($affectedRow > 0){

				$next_vehicle_returnNumber = get_option('next_vrt_number');

				

				$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

				echo json_encode($new_vehicle_return_Numbar);

				die;

				}else{

				echo json_encode(false);

				die;

			}

		}

		public function UpdateOnlyVehRtn()

		{

			$selected_company = $this->session->userdata('root_company');

			

			$FY = $this->session->userdata('finacial_year');

			$ChallanID = $this->input->post('challan_n');

			$RtnCrates = $this->input->post('refund_crates');

			$vehicle_number = $this->input->post('vehicle_number');

			$VRtnID = $this->input->post('VRtnID');

			$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

			$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

			$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

			$oldDate = $GetVRtnDetails->Transdate;

			$affectedRow = 0;

			$vehicleRtn_data = array(

            'Transdate'=>$Transdate,

            'IsRtnRcvd' => 'Y',

            'Act_entry_datetime'=>$Act_datetime,

            'UserID2'=>$_SESSION['username'],

            'Lupdate'=>date('Y-m-d H:i:s')

			);

			

			

			//Insert Records into vehicle Return audit table before updating vehiclereturn table

			$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

			if(!empty($previousVehicleReturnDetails)){

				$insertArray = array(

                'PlantID' =>  $previousVehicleReturnDetails->PlantID,

                'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

                'Transdate' =>  $previousVehicleReturnDetails->Transdate,

                'Crates' =>  $previousVehicleReturnDetails->Crates,

                'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

                'UserID' =>  $previousVehicleReturnDetails->UserID,

                'FY' =>  $previousVehicleReturnDetails->FY,

                'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

                'UserID2' =>  $previousVehicleReturnDetails->UserID2,

                'created_by' =>  $this->session->userdata('username'),

                'created_at' =>  date('Y-m-d H:i:s'),

				);  

				$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

			}

			

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $FY); 

			$this->db->where('ReturnID', $VRtnID);

			$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

			

			if($this->db->affected_rows() > 0){

				$affectedRow++;

			}

			

			if($affectedRow > 0){

				$next_vehicle_returnNumber = get_option('next_vrt_number');

				

				$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

				echo json_encode($new_vehicle_return_Numbar);

				die;

				}else{

				echo json_encode(false);

				die;

			}

		}

		

		

		public function PendingVehicleReturnList()

		{

			if (!has_permission_new('PendingVehicleReturnList', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Pending Vehicle Return List";

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			

			$this->load->view('admin/VehRtn/PendingVehicleReturnList', $data);

		}

		

		public function LoadPendingVehicleRetuernList()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'IsReturn'  => $this->input->post('IsReturn'),

			'IsMileage'  => $this->input->post('IsMileage'),

			'IsCrate'  => $this->input->post('IsCrate'),

			'IsPayment'  => $this->input->post('IsPayment'),

			'IsExpenses'  => $this->input->post('IsExpenses'),

			);

			$result = $this->VehRtn_model->LoadPendingVehicleRetuernList($data);

			$html ='';

			if(count($result) >0 ){

				$sr = 1;

				foreach($result as $value){

					

					$stage = 0;

					if($value["IsRtnRcvd"] == "Y"){

						$stage++;

					}

					if($value["IsCrateRcvd"] == "Y"){

						$stage++;

					}

					if($value["IsPaymentRcvd"] == "Y"){

						$stage++;

					}

					if($value["IsExpRcvd"] == "Y"){

						$stage++;

					}

					$style = "";

					if($stage == 1){

						$style ="background-color:gray !important; color:white !important;";

					}

					if($stage == 2){

						$style ="background-color:orange !important; color:black !important;";

					}

					if($stage == 3){

						$style ="background-color:yellow !important; color:black !important;";

					}

					if($stage == 4){

						$style ="background-color:green !important; color:white !important;";

					}

					

					$VehicleType = "";

					if($value["VehicleType"] == "SELF"){

						$VehicleType = "SELF";

						}elseif($value["VehicleType"] == "TV"){

						$VehicleType = "Transport Vehicle";

						}else{

						$VehicleType = "Company Vehicle";

					}

					

					$html.= '<tr style="'.$style.'">';

					$html.= '<td style="padding:0px 3px !important;">'.$sr.'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["ChallanID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["Transdate"],0,10)).'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["ReturnID"].'</td>';

					$html.= '<td style="padding:0px 3px !important;">'. _d(substr($value["returnTransdate"],0,10)).'</td>';

					$html.= '<td style="padding:0px 3px !important;">'.$VehicleType.'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["VehicleID"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["driver_ln"].'</td>';

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'. $value["Crates"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;text-align:right;">'.$value["Cases"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsRtnRcvd"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsCrateRcvd"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsPaymentRcvd"].'</td>'; 

					$html.= '<td style="padding:0px 3px !important;">'.$value["IsExpRcvd"].'</td>'; 

					$html.= '</tr>'; 

					$sr++;

				} 

				}else{

				$html.= '<tr>'; 

				$html.= '<td colspan="13"><span style="color:red;">No data found..</span></td>';

				$html.= '</tr>'; 

			}

			echo json_encode($html);

		}

		

		public function export_PendingVehicleReturnReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				'IsReturn'  => $this->input->post('IsReturn'),

				'IsMileage'  => $this->input->post('IsMileage'),

				'IsCrate'  => $this->input->post('IsCrate'),

				'IsPayment'  => $this->input->post('IsPayment'),

				'IsExpenses'  => $this->input->post('IsExpenses'),

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->LoadPendingVehicleRetuernList($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Pending Vehicle Return Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				// Define header style

				$header_style = [

				'font' => 'Arial',           // Font type

				'font-size' => 12,           // Font size

				'font-style' => 'bold',      // Font style (bold)

				'color' => '#FFFFFF',        // Font color (white)

				'fill' => '#50607b',         // Background color (green)

				'halign' => 'center',        // Horizontal alignment

				'valign' => 'center',        // Vertical alignment

				'border' => 'left,right,top,bottom' // Add border to header cells

				];

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["ChallanID"] =  'ChallanID';

				$set_col_tk["Challan Date"] =  'Challan Date';

				$set_col_tk["ReturnID"] =  'ReturnID';

				$set_col_tk["Return Date"] =  'Return Date';

				$set_col_tk["Vehicle Type"] =  'Vehicle Type';

				$set_col_tk["Vehicle No"] =  'Vehicle No';

				$set_col_tk["Driver Name"] =  'Driver Name';

				$set_col_tk["Crates"] =  'Crates';

				$set_col_tk["Cases"] =  'Cases';

				$set_col_tk["Mileage Detail"] =  'Mileage Detail';

				$set_col_tk["Crate Detail"] =  'Crate Detail';

				$set_col_tk["Payment Detail"] =  'Payment Detail';

				$set_col_tk["Expense Detail"] =  'Expense Detail';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header, $header_style);

				$i=1;

                

                foreach ($body_data as $each) {

					

					$stage = 0;

					if($each["IsRtnRcvd"] == "Y"){

						$stage++;

					}

					if($each["IsCrateRcvd"] == "Y"){

						$stage++;

					}

					if($each["IsPaymentRcvd"] == "Y"){

						$stage++;

					}

					if($each["IsExpRcvd"] == "Y"){

						$stage++;

					}

					

					$row_style = [];

					if ($stage == 1) {

						$row_style = ['fill' => '#808080', 'color' => '#FFFFFF']; // Gray background, white text

						} elseif ($stage == 2) {

						$row_style = ['fill' => '#FFA500', 'color' => '#000000']; // Orange background, black text

						} elseif ($stage == 3) {

						$row_style = ['fill' => '#FFFF00', 'color' => '#000000']; // Yellow background, black text

						} elseif ($stage == 4) {

						$row_style = ['fill' => '#008000', 'color' => '#FFFFFF']; // Green background, white text

					}

					

					$VehicleType = "";

					if($each["VehicleType"] == "SELF"){

						$VehicleType = "SELF";

						}elseif($each["VehicleType"] == "TV"){

						$VehicleType = "Transport Vehicle";

						}else{

						$VehicleType = "Company Vehicle";

					}

					

                    $list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each['ChallanID'];

                    $list_add[] = _d(substr($each["Transdate"],0,10));

                    $list_add[] = $each['ReturnID'];

                    $list_add[] =  _d(substr($each["returnTransdate"],0,10));

                    $list_add[] = $VehicleType;

                    $list_add[] = $each['VehicleID'];

                    $list_add[] = $each['driver_ln'];

                    $list_add[] = $each['Crates'];

                    $list_add[] = $each['Cases'];

                    $list_add[] = $each['IsRtnRcvd'];

                    $list_add[] = $each['IsCrateRcvd'];

                    $list_add[] = $each['IsPaymentRcvd'];

                    $list_add[] = $each['IsExpRcvd'];

                    $writer->writeSheetRow('Sheet1', $list_add, $row_style);

					

					$i++;

				}

                

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'PendingVehicleReturn_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		

		public function AddEditVehicleReturnEntry()

		{

			if (!has_permission_new('VehicleReturnEntry', '', 'view')) {

				access_denied('invoices');

			}

			$title = "Vehicle Ruturn Entry";

			$data['title'] = $title;

			$this->load->model('sale_reports_model');

			$data['routes']    = $this->clients_model->getroute();

			$data['company_detail'] = $this->sale_reports_model->get_company_detail();

			$this->load->view('admin/VehRtn/AddEditVehicleReturnEntry', $data);

		}

		

		public function SaveVehicleReturnEntry()

		{

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			$new_vehicle_returnNumber = get_option('next_vrt_number');

			$GatePassDateTime = $this->input->post('GatePassDateTime');

			$Token = 'TG_'.date('Y').date('m').date('d');

			$vehicle_number = $this->input->post('vehicle_number');

			$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

			$new_vehicle_return_Numbar = 'VRT' . $FY . $new_vehicle_returnNumber;

			$ChallanID = $this->input->post('challan_n');

			$CheckVehRtnForChallan = $this->VehRtn_model->CheckVehRtnForChallan($ChallanID);

			if (empty($CheckVehRtnForChallan)) {

				$RtnCrates = $this->input->post('refund_crates');

				$Transdate = to_sql_date($this->input->post('from_date')) . " " . date('H:i:s');

				$affectedRow = 0;

				$vehicleRtn_data = array(

                'PlantID' => $selected_company,

                'ReturnID' => $new_vehicle_return_Numbar,

                'Transdate' => $Transdate,

				'Act_entry_datetime'=>$Act_datetime,

                'ChallanID' => $ChallanID,

                'IsRtnRcvd' => 'Y',

                'UserID' => $_SESSION['username'],

                'FY' => $FY

				);

				

				$this->db->insert(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				if ($this->db->affected_rows() > 0) {

					$this->VehRtn_model->increment_next_number_vehicle();

					// Calculate Travel Distance for Vehicle

    			    $vhlarray =  array(

					"token" => $Token,

					"VehicleNumber"=>$vehicle_number,

					"FromDateTime"=>$GatePassDateTime,

					"ToDateTime"=>$Act_datetime

                    );

                    $vhl_data = json_encode($vhlarray);

                    

                    $curl = curl_init();

                    curl_setopt_array($curl, array(

					CURLOPT_URL => "https://capi.trackinggenie.com/gaurifoods_get_total_km.php", //  -> LIVE URL

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_MAXREDIRS => 10,

					CURLOPT_TIMEOUT => 30,

					CURLOPT_CUSTOMREQUEST => "POST",

					CURLOPT_POSTFIELDS => $vhl_data,

					CURLOPT_HTTPHEADER => array(

					"content-type: application/json"

					),

					)

                    );

                    $response = curl_exec($curl);

                    $response_array = json_decode($response);

                    if($response_array->data->TotalKM){

                        $km = $response_array->data->TotalKM;

						}else{

                        $km = 0;

					}

					$data = array(

					'VehRtn_no'=>$new_vehicle_return_Numbar,

					'out_meter_reading'=>$this->input->post('out_meter_reading'),

					'in_meter_reading'=>$this->input->post('in_meter_reading'),

					'DistanceTravel'=>$this->input->post('DistanceTravel'),

					'APIDistanceTravel'=>$km,

					'Diesel'=>$this->input->post('Diesel'),

					'Diesel_value'=>$this->input->post('Diesel_value'),

					'Fuel_Price'=>$this->input->post('Fuel_Price'),

					'Fuel_Station'=>$this->input->post('Fuel_Station'),

					'Loading_Rate'=>$this->input->post('Loading_Rate'),

					'Fooding'=>$this->input->post('Fooding'),

					'Toll'=>$this->input->post('Toll'),

					'Phone'=>$this->input->post('Phone'),

					'Police'=>$this->input->post('Police'),

					'Misc_Expense'=>$this->input->post('Misc_Expense'),

					'Misc_Expense_Remark'=>$this->input->post('Misc_Expense_Remark'),

					'Expense_repairing'=>$this->input->post('Expense_repairing'),

					'Repairing_Expense_Remark'=>$this->input->post('Repairing_Expense_Remark'),

					'Actual_Mileage'=>$this->input->post('Actual_Mileage'),

					'Transdate'=>date('Y-m-d H:i:s'),

					'UserID'=>$this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'transport_entry', $data);

					$affectedRow++;

				}

				if ($affectedRow > 0) {

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					} else {

					echo json_encode(false);

					die;

				}

				

				} else {

				

				$VRtnID = $CheckVehRtnForChallan->ReturnID;

				$ChallanID = $CheckVehRtnForChallan->ChallanID;

				$RtnCrates = $this->input->post('refund_crates');

				$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

				$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

				$oldDate = $GetVRtnDetails->Transdate;

				

				$vehicleRtn_data = array(

				'UserID2'=>$_SESSION['username'],

                'IsRtnRcvd' => 'Y',

				'Lupdate'=>date('Y-m-d H:i:s')

				);

				

				

				//Insert Records into vehicle Return audit table before updating vehiclereturn table

				$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

				if(!empty($previousVehicleReturnDetails)){

					$insertArray = array(

					'PlantID' =>  $previousVehicleReturnDetails->PlantID,

					'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

					'Transdate' =>  $previousVehicleReturnDetails->Transdate,

					'Crates' =>  $previousVehicleReturnDetails->Crates,

					'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

					'UserID' =>  $previousVehicleReturnDetails->UserID,

					'FY' =>  $previousVehicleReturnDetails->FY,

					'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

					'UserID2' =>  $previousVehicleReturnDetails->UserID2,

					'created_by' =>  $this->session->userdata('username'),

					'created_at' =>  date('Y-m-d H:i:s'),

					);  

					$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

				}

				

				$this->db->where('PlantID', $selected_company);

				$this->db->where('FY', $FY); 

				$this->db->where('ReturnID', $VRtnID);

				$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

				

				if($this->db->affected_rows() > 0){

				    // Calculate Travel Distance for Vehicle

    			    $vhlarray =  array(

					"token" => $Token,

					"VehicleNumber"=>$vehicle_number,

					"FromDateTime"=>$GatePassDateTime,

					"ToDateTime"=>$Act_datetime

                    );

                    $vhl_data = json_encode($vhlarray);

                    

                    $curl = curl_init();

                    curl_setopt_array($curl, array(

					CURLOPT_URL => "https://capi.trackinggenie.com/gaurifoods_get_total_km.php", //  -> LIVE URL

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_MAXREDIRS => 10,

					CURLOPT_TIMEOUT => 30,

					CURLOPT_CUSTOMREQUEST => "POST",

					CURLOPT_POSTFIELDS => $vhl_data,

					CURLOPT_HTTPHEADER => array(

					"content-type: application/json"

					),

					)

                    );

                    $response = curl_exec($curl);

                    $response_array = json_decode($response);

                    if($response_array->data->TotalKM){

                        $km = $response_array->data->TotalKM;

						}else{

                        $km = 0;

					}

					$affectedRow++;

					

					$this->db->where('VehRtn_no', $VRtnID);

					$this->db->delete(db_prefix() . 'transport_entry');

					

					$data = array(

					'VehRtn_no'=>$VRtnID,

					'out_meter_reading'=>$this->input->post('out_meter_reading'),

					'in_meter_reading'=>$this->input->post('in_meter_reading'),

					'DistanceTravel'=>$this->input->post('DistanceTravel'),

					'APIDistanceTravel'=>$km,

					'Diesel'=>$this->input->post('Diesel'),

					'Diesel_value'=>$this->input->post('Diesel_value'),

					'Fooding'=>$this->input->post('Fooding'),

					'Toll'=>$this->input->post('Toll'),

					'Phone'=>$this->input->post('Phone'),

					'Police'=>$this->input->post('Police'),

					'Misc_Expense'=>$this->input->post('Misc_Expense'),

					'Expense_repairing'=>$this->input->post('Expense_repairing'),

					'Actual_Mileage'=>$this->input->post('Actual_Mileage'),

					'Transdate'=>date('Y-m-d H:i:s'),

					'UserID'=>$this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'transport_entry', $data);

				}

				if($affectedRow > 0){

					$next_vehicle_returnNumber = get_option('next_vrt_number');

					$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

					echo json_encode($new_vehicle_return_Numbar);

					die;

					}else{

					echo json_encode(false);

					die;

				}

			}

		}

		

		public function UpdateVehicleReturnEntry()

		{

			$selected_company = $this->session->userdata('root_company');

			

			$FY = $this->session->userdata('finacial_year');

			$ChallanID = $this->input->post('challan_n');

			$RtnCrates = $this->input->post('refund_crates');

			$vehicle_number = $this->input->post('vehicle_number');

			$VRtnID = $this->input->post('VRtnID');

			$GetVRtnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);   

			$Transdate = to_sql_date($this->input->post('from_date'))." ".date('H:i:s');

			$Act_datetime = to_sql_date(substr($this->input->post('Act_datetime'),0,10))." ".substr($this->input->post('Act_datetime'),11,5).":00";

			$GatePassDateTime = $this->input->post('GatePassDateTime');

			$oldDate = $GetVRtnDetails->Transdate;

			$affectedRow = 0;

			$vehicleRtn_data = array(

            'Transdate'=>$Transdate,

            'IsRtnRcvd' => 'Y',

            'Act_entry_datetime'=>$Act_datetime,

            'UserID2'=>$_SESSION['username'],

            'Lupdate'=>date('Y-m-d H:i:s')

			);

			

			

			//Insert Records into vehicle Return audit table before updating vehiclereturn table

			$previousVehicleReturnDetails = $this->VehRtn_model->GetVRtnDetails($VRtnID);

			if(!empty($previousVehicleReturnDetails)){

				$insertArray = array(

                'PlantID' =>  $previousVehicleReturnDetails->PlantID,

                'ReturnID' =>  $previousVehicleReturnDetails->ReturnID,

                'Transdate' =>  $previousVehicleReturnDetails->Transdate,

                'Crates' =>  $previousVehicleReturnDetails->Crates,

                'ChallanID' =>  $previousVehicleReturnDetails->ChallanID,

                'UserID' =>  $previousVehicleReturnDetails->UserID,

                'FY' =>  $previousVehicleReturnDetails->FY,

                'Lupdate' =>  $previousVehicleReturnDetails->Lupdate,

                'UserID2' =>  $previousVehicleReturnDetails->UserID2,

                'created_by' =>  $this->session->userdata('username'),

                'created_at' =>  date('Y-m-d H:i:s'),

				);  

				$this->db->insert(db_prefix() . 'vehiclereturn_audit',$insertArray);

			}

			

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $FY); 

			$this->db->where('ReturnID', $VRtnID);

			$this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);

			$Token = 'TG_'.date('Y').date('m').date('d');

			if($this->db->affected_rows() > 0){

			    // Calculate Travel Distance for Vehicle

			    $vhlarray =  array(

				"token" => $Token,

				"VehicleNumber"=>$vehicle_number,

				"FromDateTime"=>$GatePassDateTime,

				"ToDateTime"=>$Act_datetime

                );

                $vhl_data = json_encode($vhlarray);

                

                $curl = curl_init();

                curl_setopt_array($curl, array(

				CURLOPT_URL => "https://capi.trackinggenie.com/gaurifoods_get_total_km.php", //  -> LIVE URL

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_MAXREDIRS => 10,

				CURLOPT_TIMEOUT => 30,

				CURLOPT_CUSTOMREQUEST => "POST",

				CURLOPT_POSTFIELDS => $vhl_data,

				CURLOPT_HTTPHEADER => array(

				"content-type: application/json"

				),

				)

                );

                $response = curl_exec($curl);

                $response_array = json_decode($response);

                if($response_array->data->TotalKM){

                    $km = $response_array->data->TotalKM;

					}else{

                    $km = 0;

				}

				$affectedRow++;

				$this->db->where('VehRtn_no', $VRtnID);

				$this->db->delete(db_prefix() . 'transport_entry');

				

				$data = array(

				'VehRtn_no'=>$VRtnID,

				'out_meter_reading'=>$this->input->post('out_meter_reading'),

				'in_meter_reading'=>$this->input->post('in_meter_reading'),

				'DistanceTravel'=>$this->input->post('DistanceTravel'),

				'APIDistanceTravel'=>$km,

				'Diesel'=>$this->input->post('Diesel'),

				'Diesel_value'=>$this->input->post('Diesel_value'),

				'Fuel_Price'=>$this->input->post('Fuel_Price'),

				'Fuel_Station'=>$this->input->post('Fuel_Station'),

				'Loading_Rate'=>$this->input->post('Loading_Rate'),

				'Fooding'=>$this->input->post('Fooding'),

				'Toll'=>$this->input->post('Toll'),

				'Phone'=>$this->input->post('Phone'),

				'Police'=>$this->input->post('Police'),

				'Misc_Expense'=>$this->input->post('Misc_Expense'),

				'Misc_Expense_Remark'=>$this->input->post('Misc_Expense_Remark'),

				'Expense_repairing'=>$this->input->post('Expense_repairing'),

				'Repairing_Expense_Remark'=>$this->input->post('Repairing_Expense_Remark'),

				'Actual_Mileage'=>$this->input->post('Actual_Mileage'),

				'Transdate'=>date('Y-m-d H:i:s'),

				'UserID'=>$this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'transport_entry', $data);

			}

			

			if($affectedRow > 0){

				$next_vehicle_returnNumber = get_option('next_vrt_number');

				

				$new_vehicle_return_Numbar = $next_vehicle_returnNumber;

				echo json_encode($new_vehicle_return_Numbar);

				die;

				}else{

				echo json_encode(false);

				die;

			}

		}

		

		

		public function FinalVehicleReport()

		{

			if (!has_permission_new('FinalVehicleReport', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Final Vehicle Report";

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			

			$this->load->view('admin/VehRtn/FinalVehicleReport', $data);

		}

		

		public function LoadFinalVehicleReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			);

			$result = $this->VehRtn_model->LoadFinalVehicleReport($data);

			// echo "<pre>";print_r($result);die;

			$html = '';

			$i=1;

			

			if(count($result)){

				

				foreach($result as $each){

					

					$VehicleType = "";

					if($each["VehicleType"] == "SELF"){

						$VehicleType = "SELF";

						}elseif($each["VehicleType"] == "TV"){

						$VehicleType = "Transport Vehicle";

						}else{

						$VehicleType = "Company Vehicle";

					}

					$html .='<tr>';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["company"].'</td>';

					$html .='<td>'._d($each["Dispatchdate"]).'</td>';

					$html .='<td>'.$each["routename"].'</td>';

					$html .='<td>'.$VehicleType.'</td>';

					$html .='<td>'.$each["VehicleID"].'</td>';

					$html .='<td>'.$each["Driver"].'</td>';

					$html .='<td>'.$each["OrderID"].'</td>';

					$html .='<td>'.$each["ChallanID"].'</td>';

					$html .='<td>'.$each["OrderAmt"].'</td>';

					$html .='<td>'._d($each["gatepasstime"]).'</td>';

					$html .='<td>'._d($each["VehicleIndate"]).'</td>';

					$html .='<td>'.$each["OutCrates"].'</td>';

					$html .='<td>'.$each["InCrates"].'</td>';

					$html .='<td>'.$each["out_meter_reading"].'</td>';

					$html .='<td>'.$each["in_meter_reading"].'</td>';

					$html .='</tr>';

					$i++;

				}

				

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		

		public function export_FinalVehicleReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->LoadFinalVehicleReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Final Vehicle Return Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				// Define header style

				$header_style = [

				'font' => 'Arial',           // Font type

				'font-size' => 12,           // Font size

				'font-style' => 'bold',      // Font style (bold)

				'color' => '#FFFFFF',        // Font color (white)

				'fill' => '#50607b',         // Background color (green)

				'halign' => 'center',        // Horizontal alignment

				'valign' => 'center',        // Vertical alignment

				'border' => 'left,right,top,bottom' // Add border to header cells

				];

				

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["Party Name"] =  'Party Name';

				$set_col_tk["Expected Delivery Time"] =  'Expected Delivery Time';

				$set_col_tk["Route"] =  'Route';

				$set_col_tk["Vehicle Type"] =  'Vehicle Type';

				$set_col_tk["Vehicle No."] =  'Vehicle No.';

				$set_col_tk["Driver Name"] =  'Driver Name';

				$set_col_tk["OrderID"] =  'OrderID';

				$set_col_tk["Challan No."] =  'Challan No.';

				$set_col_tk["Bill Amount"] =  'Bill Amount';

				$set_col_tk["Out DateTime"] =  'Out DateTime';

				$set_col_tk["In DateTime"] =  'In DateTime';

				$set_col_tk["Out Crate Qty"] =  'Out Crate Qty';

				$set_col_tk["In Crate Qty"] =  'In Crate Qty';

				$set_col_tk["Out Meater Reading"] =  'Out Meater Reading';

				$set_col_tk["In Meater Reading"] =  'In Meater Reading';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header, $header_style);

				$i=1;

                

                foreach ($body_data as $each) {

					$VehicleType = "";

					if($each["VehicleType"] == "SELF"){

						$VehicleType = "SELF";

						}elseif($each["VehicleType"] == "TV"){

						$VehicleType = "Transport Vehicle";

						}else{

						$VehicleType = "Company Vehicle";

					}

					$list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each["company"];

                    $list_add[] = _d($each["Dispatchdate"]);

                    $list_add[] = $each["routename"];

                    $list_add[] = $VehicleType;

                    $list_add[] = $each["VehicleID"];

                    $list_add[] = $each["Driver"];

                    $list_add[] = $each["OrderID"];

                    $list_add[] = $each["ChallanID"];

                    $list_add[] = $each["OrderAmt"];

                    $list_add[] = _d($each["gatepasstime"]);

                    $list_add[] = _d($each["VehicleIndate"]);

                    $list_add[] = $each["OutCrates"];

                    $list_add[] = $each["InCrates"];

                    $list_add[] = $each["out_meter_reading"];

                    $list_add[] = $each["in_meter_reading"];

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

                

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'FinalVehicleReturn_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		

		public function GetVehiclesByType()

		{

			$data = array(	

				"VehicleType" =>$this->input->post('VehicleType'),

			    "FromDate"=>$this->input->post('from_date'),

			    "ToDate"=>$this->input->post('to_date')

			);

			//$VehicleType = $this->input->post('VehicleType');

			$VehicleList = $this->VehRtn_model->GetVehiclesByType($data);

			echo json_encode($VehicleList);

		}

		

		public function PremisesReport()

		{
			$VRtnID = 0;


			if (!has_permission_new('PremisesReport', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']         = "Final Vehicle Report";

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			$data['PremiseData'] = $this->VehRtn_model->GetPremisesReport($VRtnID);

			// echo "<pre>";print_r($data['PremiseData']);die;

			$this->load->view('admin/VehRtn/PremisesReport', $data);

		}

		

		public function DriverRestRecord()

		{

			if (!has_permission_new('DriverRestRecord', '', 'view')) {

				access_denied('VehRtn');

			}

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			$data['DriverList']    = $this->VehRtn_model->GetDriverList();

			$data['table_data'] = $this->VehRtn_model->get_All_Rest_Record();

			$data['title'] = 'Driver Rest Record';

			$this->load->view('admin/VehRtn/DriverRestRecord', $data);

		}

		public function SaveRestEntry()

		{

			$data = array(

			'DriverID'=>$this->input->post('Driver'),

			'TransDate'=>to_sql_date($this->input->post('Date'))." ".date('H:i:s'),

			'Amount'=>$this->input->post('Amount'),

            'UserID' => $_SESSION['username'],

			'TransDate2'=>date('Y-m-d H:i:s'),

			);

			$Return  = $this->VehRtn_model->SaveRestEntry($data);

			echo json_encode($Return);

		}

		/* Get Rest Record By ID / ajax */

		public function GetRestRecordDetailByID()

		{

			$EntryID = $this->input->post('EntryID');

			$Return  = $this->VehRtn_model->getRest_Record_ByID($EntryID);

			echo json_encode($Return);

		}

		

		public function UpdateRestRecord()

		{

			$data = array(

			'DriverID'=>$this->input->post('Driver'),

			'TransDate'=>to_sql_date($this->input->post('Date'))." ".date('H:i:s'),

			'Amount'=>$this->input->post('Amount'),

            'UserID2' => $_SESSION['username'],

			'lupdate'=>date('Y-m-d H:i:s'),

			);

			$EntryID = $this->input->post('EntryID');

			$EntryID                     = $this->VehRtn_model->UpdateRestRecord($data,$EntryID);

			echo json_encode($EntryID);

		}

		

		public function RestRecordReport()

		{

			if (!has_permission_new('RestRecordReport', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Rest Record Report";

			$data['DriverList']    = $this->VehRtn_model->GetDriverList();

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			

			$this->load->view('admin/VehRtn/RestRecordReport', $data);

		}

		

		public function LoadRestRecordReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Driver'  => $this->input->post('Driver'),

			);

			$result = $this->VehRtn_model->LoadRestRecordReport($data);

			// echo "<pre>";print_r($result);die;

			$html = '';

			$i=1;

			

			if(count($result)){

				

				$TotalAmount = '';

				foreach($result as $each){

					$TotalAmount += $each["Amount"];

					$html .= '<tr onclick="RedirectEntry(\'' . $each["id"] . '\')" style="cursor:pointer;">';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["id"].'</td>';

					$html .='<td>'._d($each["TransDate"]).'</td>';

					$html .='<td>'.$each["firstname"]." ".$each["lastname"].'</td>';

					$html .='<td>'.$each["Amount"].'</td>';

					$html .='</tr>';

					$i++;

				}

				$html .='<tr>';

				$html .='<td align="right" colspan="4">Total</td>';

				$html .='<td>'.$TotalAmount.'</td>';

				$html .='</tr>';

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		

		public function export_RestRecordReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				'Driver'  => $this->input->post('Driver'),

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->LoadRestRecordReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Rest Record Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				// Define header style

				$header_style = [

				'font' => 'Arial',           // Font type

				'font-size' => 12,           // Font size

				'font-style' => 'bold',      // Font style (bold)

				'color' => '#FFFFFF',        // Font color (white)

				'fill' => '#50607b',         // Background color (green)

				'halign' => 'center',        // Horizontal alignment

				'valign' => 'center',        // Vertical alignment

				'border' => 'left,right,top,bottom' // Add border to header cells

				];

				

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["EntryID"] =  'EntryID';

				$set_col_tk["Date"] =  'Date';

				$set_col_tk["Driver Name"] =  'Driver Name';

				$set_col_tk["Amount"] =  'Amount';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$i=1;

                

				$TotalAmount = '';

				foreach ($body_data as $each) {

					$TotalAmount += $each["Amount"];

					

					$list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each["id"];

                    $list_add[] = _d($each["TransDate"]);

                    $list_add[] = $each["firstname"]." ".$each["lastname"];

                    $list_add[] = $each["Amount"];

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

                

				$list_add = [];

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = 'Total';

				$list_add[] = $TotalAmount;

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'RestRecord_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		

		public function SetEntryID()

		{

			$EntryID = $this->input->post('EntryID');

			$EntryID_for_edit = array(

			'EntryID'  => $EntryID,

			);

			$this->session->set_userdata($EntryID_for_edit);

			echo json_encode($EntryID);

		}

		

		public function CustomerFeedback()

		{

			if (!has_permission_new('CustomerFeedback', '', 'view')) {

				access_denied('VehRtn');

			}

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			$data['DriverList']    = $this->VehRtn_model->GetDriverList();

			$data['CustomerList']    = $this->VehRtn_model->getCustomerList();

			$data['table_data'] = $this->VehRtn_model->get_All_Feedback_Record();

			$data['title'] = 'Customer Feedback';

			$this->load->view('admin/VehRtn/CustomerFeedback', $data);

		}

		public function SaveCustomerFeedback()

		{

			$data = array(

			'TransDate'=>to_sql_date($this->input->post('Date'))." ".date('H:i:s'),

			'DriverID'=>$this->input->post('Driver'),

			'AccountID'=>$this->input->post('Customer'),

			'Quality'=>$this->input->post('Quality'),

			'Quantity'=>$this->input->post('Quantity'),

			'QualityRemark'=>$this->input->post('QualityRemark'),

			'DeliveryRemark'=>$this->input->post('DeliveryRemark'),

			'QuantityRemark'=>$this->input->post('QuantityRemark'),

			'Dispatcher'=>$this->input->post('Dispatcher'),

			'Driver_Behaviour'=>$this->input->post('Driver_Behaviour'),

			'Delivery_Status'=>$this->input->post('Delivery_Status'),

			'Other'=>$this->input->post('Other'),

            'UserID' => $_SESSION['username'],

			'TransDate2'=>date('Y-m-d H:i:s'),

			);

			$Return  = $this->VehRtn_model->SaveCustomerFeedback($data);

			echo json_encode($Return);

		}

		public function GetFeedbackDetailByID()

		{

			$EntryID = $this->input->post('EntryID');

			$Return  = $this->VehRtn_model->get_Feedback_Record_ByID($EntryID);

			echo json_encode($Return);

		}

		

		public function UpdateCustomerFeedback()

		{

			$data = array(

			'TransDate'=>to_sql_date($this->input->post('Date'))." ".date('H:i:s'),

			'DriverID'=>$this->input->post('Driver'),

			'AccountID'=>$this->input->post('Customer'),

			'Quality'=>$this->input->post('Quality'),

			'Quantity'=>$this->input->post('Quantity'),

			'Dispatcher'=>$this->input->post('Dispatcher'),

			'Driver_Behaviour'=>$this->input->post('Driver_Behaviour'),

			'Delivery_Status'=>$this->input->post('Delivery_Status'),

			'QualityRemark'=>$this->input->post('QualityRemark'),

			'DeliveryRemark'=>$this->input->post('DeliveryRemark'),

			'QuantityRemark'=>$this->input->post('QuantityRemark'),

			'Other'=>$this->input->post('Other'),

            'UserID2' => $_SESSION['username'],

			'lupdate'=>date('Y-m-d H:i:s'),

			);

			$EntryID = $this->input->post('EntryID');

			$EntryID                     = $this->VehRtn_model->UpdateCustomerFeedback($data,$EntryID);

			echo json_encode($EntryID);

		}

		

		public function CustomerFeedbackReport()

		{

			if (!has_permission_new('CustomerFeedbackReport', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Customer Feedback Report";

			$data['DriverList']    = $this->VehRtn_model->GetDriverList();

			$data['CustomerList']    = $this->VehRtn_model->getCustomerList();

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			

			$this->load->view('admin/VehRtn/CustomerFeedbackReport', $data);

		}

		

		public function LoadCustomerFeedbackReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Driver'  => $this->input->post('Driver'),

			'Customer'  => $this->input->post('Customer'),

			);

			$result = $this->VehRtn_model->LoadCustomerFeedbackReport($data);

			// echo "<pre>";print_r($result);die;

			$html = '';

			$i=1;

			

			if(count($result)){

				

				foreach($result as $each){

					$html .= '<tr onclick="RedirectEntry(\'' . $each["id"] . '\')" style="cursor:pointer;">';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["id"].'</td>';

					$html .='<td>'._d($each["TransDate"]).'</td>';

					$html .='<td>'.$each["firstname"]." ".$each["lastname"].'</td>';

					$html .='<td>'.$each["company"].'</td>';

					$html .='<td>'.$each["Quality"].'</td>';

					$html .='<td>'.$each["Quantity"].'</td>';

					$html .='<td>'.$each["Dispatcher"].'</td>';

					$html .='<td>'.$each["Delivery_Status"].'</td>';

					$html .='<td>'.$each["Driver_Behaviour"].'</td>';

					$html .='<td>'.$each["Other"].'</td>';

					$html .='</tr>';

					$i++;

				}

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		public function GetAllFeedbackList()

		{

			

			$result = $this->VehRtn_model->GetAllFeedbackList();

			// echo "<pre>";print_r($result);die;

			$html = '';

			$i=1;

			

			if(count($result)){

				

				foreach($result as $each){

					$html .= '<tr class="get_EntryID" data-id="'.$each["id"].'" style="cursor:pointer;">';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["id"].'</td>';

					$html .='<td>'._d($each["TransDate"]).'</td>';

					$html .='<td>'.$each["firstname"]." ".$each["lastname"].'</td>';

					$html .='<td>'.$each["company"].'</td>';

					$html .='<td>'.$each["Quality"].'</td>';

					$html .='<td>'.$each["Quantity"].'</td>';

					$html .='<td>'.$each["Dispatcher"].'</td>';

					$html .='<td>'.$each["Delivery_Status"].'</td>';

					$html .='<td>'.$each["Driver_Behaviour"].'</td>';

					$html .='<td>'.$each["Other"].'</td>';

					$html .='</tr>';

					$i++;

				}

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		

		public function export_CustomerFeedbackReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				'Driver'  => $this->input->post('Driver'),

				'Customer'  => $this->input->post('Customer'),

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->LoadCustomerFeedbackReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Customer Feedback Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["EntryID"] =  'EntryID';

				$set_col_tk["Date"] =  'Date';

				$set_col_tk["Driver Name"] =  'Driver Name';

				$set_col_tk["Customer"] =  'Customer';

				$set_col_tk["Quality"] =  'Quality';

				$set_col_tk["Quantity"] =  'Quantity';

				$set_col_tk["Dispatcher Name"] =  'Dispatcher Name';

				$set_col_tk["Delivery Status"] =  'Delivery Status';

				$set_col_tk["Driver Behaviour"] =  'Driver Behaviour';

				$set_col_tk["Other"] =  'Other';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$i=1;

                

				foreach ($body_data as $each) {

					

					

					$list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each["id"];

                    $list_add[] = _d($each["TransDate"]);

                    $list_add[] = $each["firstname"]." ".$each["lastname"];

                    $list_add[] = $each["company"];

                    $list_add[] = $each["Quality"];

                    $list_add[] = $each["Quantity"];

                    $list_add[] = $each["Dispatcher"];

                    $list_add[] = $each["Delivery_Status"];

                    $list_add[] = $each["Driver_Behaviour"];

                    $list_add[] = $each["Other"];

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

                

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'CustomerFeedback_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		

		public function VehicleMaintenanceReport()

		{

			if (!has_permission_new('VehicleMaintenanceReport', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Final Vehicle Report";

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			

			$this->load->view('admin/VehRtn/VehicleMaintenanceReport', $data);

		}

		

		public function LoadVehicleMaintenanceReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			);

			$result = $this->VehRtn_model->LoadVehicleMaintenanceReport($data);

			// echo "<pre>";print_r($result);die;

			$html = '';

			$i=1;

			

			if(count($result)){

				

				$Total = '';

				foreach($result as $each){

					$Total += $each["Expense_repairing"];

					$html .='<tr>';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'._d(substr($each["Transdate"],0,10)).'</td>';

					$html .='<td>'.$each["ChallanID"].'</td>';

					$html .='<td>'.$each["VehicleID"].'</td>';

					$html .='<td>'.$each["DriverName"].'</td>';

					$html .='<td>'.$each["Repairing_Expense_Remark"].'</td>';

					$html .='<td>'.$each["Expense_repairing"].'</td>';

					$html .='</tr>';

					$i++;

				}

				$html .='<tr>';

				$html .='<td align="right" colspan="6">Total</td>';

				$html .='<td>'.$Total.'</td>';

				$html .='</tr>';

				

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		

		

		public function export_VehicleMaintenanceReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->LoadVehicleMaintenanceReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Vehicle Maintenance Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["Date"] =  'Date';

				$set_col_tk["ChallanID"] =  'ChallanID';

				$set_col_tk["Vehicle Number"] =  'Vehicle Number';

				$set_col_tk["Driver Name"] =  'Driver Name';

				$set_col_tk["Remark"] =  'Remark';

				$set_col_tk["Cost"] =  'Cost';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$i=1;

                

				$Total = '';

                foreach ($body_data as $each) {

					

					$Total += $each["Expense_repairing"];

					$list_add = [];

                    $list_add[] = $i;

                    $list_add[] = _d(substr($each["Transdate"],0,10));

                    $list_add[] = $each["ChallanID"];

                    $list_add[] = $each["VehicleID"];

                    $list_add[] = $each["DriverName"];

                    $list_add[] = $each["Repairing_Expense_Remark"];

                    $list_add[] = $each["Expense_repairing"];

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

				

                $list_add = [];

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = 'Total';

				$list_add[] = $Total;

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'VehicleMaintenance_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		

		

		public function VehicleMaintenanceEntry()

		{

			if (!has_permission_new('VehicleMaintenanceEntry', '', 'view')) {

				access_denied('VehRtn');

			}

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			$data['DriverList']    = $this->VehRtn_model->GetDriverList();

			$data['Vehicles'] =  $this->VehRtn_model->getvehicle();

			$data['table_data'] = $this->VehRtn_model->get_All_VehicleMaintenance_Record();

			$data['title'] = 'Vehicle Maintenance Entry';

			$this->load->view('admin/VehRtn/VehicleMaintenanceEntry', $data);

		}

		

		

		public function SaveMaintenanceEntry()

		{

			$data = array(

			'DriverID'=>$this->input->post('Driver'),

			'TransDate'=>to_sql_date($this->input->post('Date'))." ".date('H:i:s'),

			'Amount'=>$this->input->post('Amount'),

			'PartRemark'=>$this->input->post('PartRemark'),

			'VehicleID'=>$this->input->post('VehicleID'),

            'UserID' => $_SESSION['username'],

			'TransDate2'=>date('Y-m-d H:i:s'),

			);

			$Return  = $this->VehRtn_model->SaveMaintenanceEntry($data);

			echo json_encode($Return);

		}

		

		

		public function UpdateMaintenanceEntry()

		{

			$data = array(

			'DriverID'=>$this->input->post('Driver'),

			'TransDate'=>to_sql_date($this->input->post('Date'))." ".date('H:i:s'),

			'Amount'=>$this->input->post('Amount'),

			'PartRemark'=>$this->input->post('PartRemark'),

			'VehicleID'=>$this->input->post('VehicleID'),

            'UserID2' => $_SESSION['username'],

			'lupdate'=>date('Y-m-d H:i:s'),

			);

			$EntryID = $this->input->post('EntryID');

			$EntryID                     = $this->VehRtn_model->UpdateMaintenanceEntry($data,$EntryID);

			echo json_encode($EntryID);

		}

		

		public function GetMaintenanceEntryDetailByID()

		{

			$EntryID = $this->input->post('EntryID');

			$Return  = $this->VehRtn_model->getMaintenanceEntry_ByID($EntryID);

			echo json_encode($Return);

		}

		

		public function VehicleMaintenanceEntryReport()

		{

			if (!has_permission_new('VehicleMaintenanceEntryReport', '', 'view')) {

				access_denied('VehRtn');

			}

			

			close_setup_menu();

			

			$data['title']                = "Vehicle Maintenance Entry Report";

			$data['DriverList']    = $this->VehRtn_model->GetDriverList();

			$data['Vehicles'] =  $this->VehRtn_model->getvehicle();

			$data['company_detail'] = $this->VehRtn_model->get_company_detail();

			

			$this->load->view('admin/VehRtn/VehicleMaintenanceEntryReport', $data);

		}

		

		public function LoadVehicleMaintenanceEntryReport()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Driver'  => $this->input->post('Driver'),

			'VehicleID'  => $this->input->post('VehicleID'),

			);

			$result = $this->VehRtn_model->LoadVehicleMaintenanceEntryReport($data);

			// echo "<pre>";print_r($result);die;

			$html = '';

			$i=1;

			

			if(count($result)){

				

				$TotalAmount = '';

				foreach($result as $each){

					$TotalAmount += $each["Amount"];

					$html .= '<tr onclick="RedirectEntry(\'' . $each["id"] . '\')" style="cursor:pointer;">';

					$html .='<td>'.$i.'</td>';

					$html .='<td>'.$each["id"].'</td>';

					$html .='<td>'._d($each["TransDate"]).'</td>';

					$html .='<td>'.$each["VehicleID"].'</td>';

					$html .='<td>'.$each["firstname"]." ".$each["lastname"].'</td>';

					$html .='<td>'.$each["PartRemark"].'</td>';

					$html .='<td>'.$each["Amount"].'</td>';

					$html .='</tr>';

					$i++;

				}

				$html .='<tr>';

				$html .='<td align="right" colspan="6">Total</td>';

				$html .='<td>'.$TotalAmount.'</td>';

				$html .='</tr>';

				}else{

				$html .='<span style="color:red;">No data found<span>';

			}

			echo json_encode($html);

		}

		

		

		public function export_VhlMaintenanceReport()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$filterdata = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date'),

				'Driver'  => $this->input->post('Driver'),

				'VehicleID'  => $this->input->post('VehicleID'),

				);

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				

				$body_data = $this->VehRtn_model->LoadVehicleMaintenanceEntryReport($filterdata);

				

				$selected_company_details = $this->VehRtn_model->get_company_detail();

				

				$writer = new XLSXWriter();

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				

				$msg2 = "Vehicle Maintenance Entry Report From : " .$from_date." To ".$to_date;

				

				$filter2 = array($msg2);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 12);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter2);

				

				// empty row

				$list_add = [];

        		$list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        		$list_add[] = "";

        		$list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

        	    $list_add[] = "";

				

				$writer->writeSheetRow('Sheet1', $list_add);

				

				// Define header style

				$header_style = [

				'font' => 'Arial',           // Font type

				'font-size' => 12,           // Font size

				'font-style' => 'bold',      // Font style (bold)

				'color' => '#FFFFFF',        // Font color (white)

				'fill' => '#50607b',         // Background color (green)

				'halign' => 'center',        // Horizontal alignment

				'valign' => 'center',        // Vertical alignment

				'border' => 'left,right,top,bottom' // Add border to header cells

				];

				

				

				$set_col_tk = [];

				$set_col_tk["Sr No."] =  'Sr No.';

				$set_col_tk["EntryID"] =  'EntryID';

				$set_col_tk["Date"] =  'Date';

				$set_col_tk["Vehicle Number"] =  'Vehicle Number';

				$set_col_tk["Driver Name"] =  'Driver Name';

				$set_col_tk["Part Repair/Replaced"] =  'Part Repair/Replaced';

				$set_col_tk["Amount"] =  'Amount';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$i=1;

                

				$TotalAmount = '';

				foreach ($body_data as $each) {

					$TotalAmount += $each["Amount"];

					

					$list_add = [];

                    $list_add[] = $i;

                    $list_add[] = $each["id"];

                    $list_add[] = _d($each["TransDate"]);

                    $list_add[] = $each["VehicleID"];

                    $list_add[] = $each["firstname"]." ".$each["lastname"];

                    $list_add[] = $each["PartRemark"];

                    $list_add[] = $each["Amount"];

                    $writer->writeSheetRow('Sheet1', $list_add);

					

					$i++;

				}

                

				$list_add = [];

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = '';

				$list_add[] = 'Total';

				$list_add[] = $TotalAmount;

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'VhlMaintenanceEntry_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

	}						