<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hsn_master extends AdminController
{
    private $not_importable_fields = ['id'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('hsn_master_model');
        $this->load->model('route_master_model');
    }

    /* List all available items */
    public function index()
    {
        if (!has_permission_new('hsnmaster', '', 'view')) {
            access_denied('Invoice Items');
        }
        
        $data['route'] = $this->hsn_master_model->get();
        $data['hsn_table'] = $this->hsn_master_model->get_data_table();
        $data['company_detail'] = $this->hsn_master_model->get_company_detail();
        /*echo "<pre>";
        print_r($data['hsn_table']);
        die;*/

        $data['title'] = "HSN Master";
        $this->load->view('admin/hsn_master/manage', $data);
    }

    public function export_hsnMaster()
    {
    	if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
            $data = $this->hsn_master_model->get_data_table();
    		$selected_company_details    = $this->hsn_master_model->get_company_detail();
    		
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
            $writer->writeSheetRow('Sheet1', $list_add);
            
            $set_col_tk = [];
    		$set_col_tk["HSN Code"] =  'HSN Code';
    		$set_col_tk["Description"] = 'Description';
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
    		
    		foreach ($data as $k => $value) {
    		    
    			$list_add = [];
    			$list_add[] = $value["name"];
    			$list_add[] = $value["hsndesc"];
    			
    			$writer->writeSheetRow('Sheet1', $list_add);
    		
    	    }
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'hsnMaster.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }

    /* Edit or update items / ajax request /*/
    public function manage()
    {
        if (has_permission_new('hsnmaster', '', 'view')) {
            if ($this->input->post()) {
                $data = $this->input->post();
                if ($data['itemid'] == '') {
                    if (!has_permission_new('hsnmaster', '', 'create')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id      = $this->hsn_master_model->add($data);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', "HSN Code");
                        echo json_encode([
                        'success' => $success,
                        'message' => $message,
                        'item'    => $this->hsn_master_model->get($id),
                    ]);
                    }else{
                        $success = true;
                        $message = 'This HSN code already exists.';
                        echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                    }
                    
                } else {
                    if (!has_permission_new('hsnmaster', '', 'edit')) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $success = $this->hsn_master_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', "HSN Code");
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
    }

    public function import()
    {
        if (!has_permission_new('hsnmaster', '', 'create')) {
            access_denied('Items Import');
        }

        $this->load->library('import/import_items', [], 'import');

        $this->import->setDatabaseFields($this->db->list_fields(db_prefix().'items'))
                     ->setCustomFields(get_custom_fields('items'));

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
            $this->import->setSimulation($this->input->post('simulate'))
                          ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                          ->setFilename($_FILES['file_csv']['name'])
                          ->perform();

            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }

        $data['title'] = _l('import');
        $this->load->view('admin/invoice_items/import', $data);
    }

    

    public function delete($id)
    {
        if (!has_permission_new('hsnmaster', '', 'delete')) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('hsn_master'));
        }

        $check_hsn_dep = $this->hsn_master_model->hsn_dependancy($id);
        /*echo "<pre>";
        print_r($check_hsn_dep);
        die;*/
        if($check_hsn_dep){
            set_alert('warning', 'Unable to delete HSN code, it is used in item master.');
            redirect(admin_url('hsn_master'));
        }else{
            $response = $this->hsn_master_model->delete($id);
            if ($response == true) {
            set_alert('success', 'HSN Code Delected Successfully..');
            } else {
                set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
            }
            redirect(admin_url('hsn_master'));
        }
        
    }

   

    public function search()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->invoice_items_model->search($this->input->post('q')));
        }
    }

    /* Get item by id / ajax */
    public function get_hsn_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $hsn                    = $this->hsn_master_model->get($id);
            

            echo json_encode($hsn);
        }
    }
	
	public function validate_hsn_mastergst()
		{
			$hsn_code = $this->input->post('hsn_code');
			$url = "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/gethsndetailsbyhsncode";
			$company_details = $this->hsn_master_model->get_company_detail($selected_company);
			
			
			// Step 1: Authentication - Get AuthToken
			$authHeaders = [
			'email'         => $company_details->eway_email,
			'username'      => $company_details->eway_username,
			'password'      => $company_details->eway_password,
			'ip_address'    => $_SERVER['REMOTE_ADDR'],
			'client_id'     => $company_details->eway_client_id,
			'client_secret' => $company_details->eway_client_secret,
			'gstin'         => $company_details->eway_gstin,
			];
			
			$queryParams = http_build_query([
			'email'    => $authHeaders['email'],
			'username' => $authHeaders['username'],
			'password' => $authHeaders['password']
			]);
			
			$authURL = "https://api.mastergst.com/ewaybillapi/v1.03/authenticate?" . $queryParams;
			
			$ch = curl_init();
			curl_setopt_array($ch, [
			CURLOPT_URL            => $authURL,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER     => [
            "email: {$authHeaders['email']}",
            "username: {$authHeaders['username']}",
            "password: {$authHeaders['password']}",
            "ip_address: {$authHeaders['ip_address']}",
            "client_id: {$authHeaders['client_id']}",
            "client_secret: {$authHeaders['client_secret']}",
            "gstin: {$authHeaders['gstin']}"
			],
			]);
			
			$response = curl_exec($ch);
			curl_close($ch);
			$authRes = json_decode($response, true);
			if ($authRes['status_cd'] == 0) {
				echo json_encode(['Status' => 'error', 'ErrorMsg' => 'Auth failedd', 'response' => $authRes]);
				return;
			}
			
			
			$url = "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/gethsndetailsbyhsncode?email=" 
			. urlencode($authHeaders['email']) 
			. "&hsncode=" . urlencode($hsn_code);
			
			$ch = curl_init();
			curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPGET        => true, // ✅ GET method
			CURLOPT_HTTPHEADER     => [
			"Accept: application/json",
			"ip_address: {$authHeaders['ip_address']}",   // your public IP
			"client_id: {$authHeaders['client_id']}",
			"client_secret: {$authHeaders['client_secret']}",
			"gstin: {$authHeaders['gstin']}"
			]
			]);
			
			$response = curl_exec($ch);
			if (curl_errno($ch)) {
				echo 'Curl error: ' . curl_error($ch);
			}
			curl_close($ch);
			
			// Decode and show response
			$data = json_decode($response, true);
			// echo "<pre>";print_r($data);die;
			
			if (isset($data['status_cd']) && $data['status_cd'] == "1") {
				$Result = [
				'Status'   => true,
				'ErrorMsg' => '',
				'HsnData'  => $data['data']
				];
				} else {
				$Result = [
				'Status'   => false,
				'ErrorMsg' => isset($data['error']['message']) ? $data['error']['message'] : 'Invalid HSN',
				'HsnData'  => []
				];
			}
			
			echo json_encode($Result);
		}
}
