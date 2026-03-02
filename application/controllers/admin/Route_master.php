<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Route_master extends AdminController
	{
		private $not_importable_fields = ['id'];
		
		public function __construct()
		{
			parent::__construct();
			$this->load->model('invoice_items_model');
			$this->load->model('route_master_model');
		}
		
		/* List all available items */
		public function index()
		{
			if (!has_permission_new('routemaster', '', 'view')) {
				access_denied('Invoice Items');
			}
			
			$data['route'] = $this->route_master_model->get();
			$data['route_table'] = $this->route_master_model->get_data_table();
			$data['company_detail'] = $this->route_master_model->get_company_detail();
			$data['RoutePoints'] = $this->route_master_model->get_data_table_PointMaster();
			/*echo "<pre>";
				print($data['route']);
				die;
			*/
			$data['title'] = "Route Master";
			$this->load->view('admin/route_master/manage', $data);
		}
		
		public function export_RouteMaster()
		{
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				
				$data = $this->route_master_model->get_data_table();
				$selected_company_details    = $this->route_master_model->get_company_detail();
				
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
				$writer->writeSheetRow('Sheet1', $list_add);
				
				$set_col_tk = [];
				$set_col_tk["Route Name"] =  'Route Name';
				$set_col_tk["Route KM"] = 'Route KM';
				$set_col_tk["Status"] = 'Status';
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				
				foreach ($data as $k => $value) {
					
					$list_add = [];
					$list_add[] = $value["name"];
					$list_add[] = $value["km"];
					if($value['status'] == "1"){
                        $status = "Y";
						}else{
                        $status = "N";
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
				$filename = 'RouteMaster.xlsx';
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
			if (!has_permission_new('routemaster', '', 'view')) {
				ajax_access_denied();
			}
			$this->app->get_table_data('route_table');
		}
		
		/* Edit or update items / ajax request /*/
		public function manage()
		{
			if (has_permission_new('routemaster', '', 'view')) {
				if ($this->input->post()) {
					$data = $this->input->post();
					
					if ($data['itemid'] == '') {
						if (!has_permission_new('routemaster', '', 'create')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}
						$id      = $this->route_master_model->add($data);
						$success = false;
						$message = '';
						if ($id) {
							$success = true;
							$message = _l('added_successfully', _l('sales_item'));
						}
						echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item'    => $this->route_master_model->get($id),
						]);
						exit;
						} else {
						if (!has_permission_new('routemaster', '', 'edit')) {
							header('HTTP/1.0 400 Bad error');
							echo _l('access_denied');
							die;
						}
						$success = $this->route_master_model->edit($data);
						$message = '';
						if ($success) {
							$message = _l('updated_successfully', _l('sales_item'));
						}
						echo json_encode([
                        'success' => $success,
                        'message' => $message,
						]);
						exit;
					}
				}
				redirect(admin_url('route_master'));
			}
		}
		
		
		
		
		/* Delete item*/
		public function delete($id)
		{
			if (!has_permission_new('routemaster', '', 'delete')) {
				access_denied('Invoice Items');
			}
			
			if (!$id) {
				redirect(admin_url('vehicles'));
			}
			
			$response = $this->route_master_model->delete($id);
			if (is_array($response) && isset($response['referenced'])) {
				set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
				} elseif ($response == true) {
				set_alert('success', 'Route Delected Successfully..');
				} else {
				set_alert('warning', _l('problem_deleting', _l('Route')));
			}
			redirect(admin_url('route_master'));
		}
		
		
		
		/* Get item by id / ajax */
		public function get_route_by_id($id)
		{
			if ($this->input->is_ajax_request()) {
				$vehicle                     = $this->route_master_model->get($id);
				
				
				echo json_encode($vehicle);
			}
		}
		
		public function PointMaster()
		{
			if (!has_permission_new('PointMaster', '', 'view')) {
				access_denied('Invoice Items');
			}
			
			$data['state'] = $this->route_master_model->getallstate();
			$data['table_data'] = $this->route_master_model->get_data_table_PointMaster();
			$data['lastId'] = $this->route_master_model->get_last_recordPointMaster();
			$data['company_detail'] = $this->route_master_model->get_company_detail();
		
			
			$data['title'] = "Point Master";
			$this->load->view('admin/route_master/PointMaster', $data);
		}
		
		public function SavePointMaster()
		{
			$data = array(
            'PointName'=>$this->input->post('PointName'),
            'state'=>$this->input->post('State'),
            'city'=>$this->input->post('City'),
            'status'=>$this->input->post('status'),
            'TransDate'=>date('Y-m-d H:i:s'),
            'UserID'=>$this->session->userdata('username')??'',
			);
			$itemGroup  = $this->route_master_model->SavePointMaster($data);
			echo json_encode($itemGroup);
		}
		
		public function UpdatePointMaster()
		{
			$data = array(
            'PointName'=>$this->input->post('PointName'),
            'state'=>$this->input->post('State'),
            'city'=>$this->input->post('City'),
            'status'=>$this->input->post('status'),
            'UserID2'=>$this->session->userdata('username')??'',
            'Lupdate'=>date('Y-m-d H:i:s'),
			);
			$PointID = $this->input->post('PointID');
			$PointID                     = $this->route_master_model->UpdatePointMaster($data,$PointID);
			echo json_encode($PointID);
		}
		
		public function GetPointMasterDetailByID()
		{
			$PointID = $this->input->post('PointID');
			$itemGroupDetails  = $this->route_master_model->GetPointMasterDetailByID($PointID);
			echo json_encode($itemGroupDetails);
		}
		
		public function StationMaster()
		{
			if (!has_permission_new('StationMaster', '', 'view')) {
				access_denied('Invoice Items');
			}
			
			$data['state'] = $this->route_master_model->getallstate();
			$data['table_data'] = $this->route_master_model->get_data_table_StationMaster();
			$data['lastId'] = $this->route_master_model->get_last_recordStationMaster();
			$data['company_detail'] = $this->route_master_model->get_company_detail();
			
			
			$data['title'] = "Station Master";
			$this->load->view('admin/route_master/StationMaster', $data);
		}
		public function GetStationMasterDetailByID()
		{
			$StationID = $this->input->post('StationID');
			$data  = $this->route_master_model->GetStationMasterDetailByID($StationID);
			echo json_encode($data);
		}
		
		
		public function SaveStationMaster()
		{
			$data = array(
            'StationName'=>$this->input->post('StationName'),
            'status'=>$this->input->post('status'),
            'TransDate'=>date('Y-m-d H:i:s'),
            'UserID'=>$this->session->userdata('username')??'',
			);
			
			$this->db->select(db_prefix() . 'StationMaster.*');
			$this->db->from(db_prefix() . 'StationMaster');
			$this->db->where(db_prefix() . 'StationMaster.StationName', $this->input->post('StationName'));
			$GetRecord = $this->db->get()->row();
			
			if(empty($GetRecord)){
				$data  = $this->route_master_model->SaveStationMaster($data);
				echo json_encode($data);
				}else{
				echo json_encode('exist');
			}
			
		}
		
		public function UpdateStationMaster()
		{
			$data = array(
            'StationName'=>$this->input->post('StationName'),
            'status'=>$this->input->post('status'),
            'UserID2'=>$this->session->userdata('username')??'',
            'Lupdate'=>date('Y-m-d H:i:s'),
			);
			$StationID = $this->input->post('StationID');
			
			$this->db->select(db_prefix() . 'StationMaster.*');
			$this->db->from(db_prefix() . 'StationMaster');
			$this->db->where(db_prefix() . 'StationMaster.StationName', $this->input->post('StationName'));
			$this->db->where(db_prefix() . 'StationMaster.id !=', $StationID);
			$GetRecord = $this->db->get()->row();
			
			if(empty($GetRecord)){
				$StationID                     = $this->route_master_model->UpdateStationMaster($data,$StationID);
				echo json_encode($StationID);
				}else{
				echo json_encode('exist');
			}
		}
	}
