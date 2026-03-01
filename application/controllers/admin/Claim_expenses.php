<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Claim_expenses extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('claim_expenses_model');
         $this->load->model('departments_model');
    }
    public function index(){
        if (!has_permission_new('cliam_expenses', '', 'view')) {
            access_denied('transaction');
        }
          $this->load->model('hsn_master_model');
         $data['company_detail'] = $this->hsn_master_model->get_company_detail();
         $data['departments']   = $this->departments_model->get();
         $data['table_data'] = $this->claim_expenses_model->get_data_table();
        // echo "<pre>";
        // print($data['route']);
        // die;

        $data['staff_details'] = $this->claim_expenses_model->get_staff_details(); 
       
        $data['title'] = "Claim Expenese";
        $this->load->view('admin/claim_expenses/manage', $data);
    }
    public function approve_km_by_hr(){
            $data = $this->input->post();
            $response = $this->claim_expenses_model->approve_km_by_hr($data);
            echo json_encode($response);
    }
    
      public function passed_amount_per_day(){
            $data = $this->input->post();
            $response = $this->claim_expenses_model->passed_amount_per_day($data);
            echo json_encode($response);
    }
      public function update_remark(){
            $data = $this->input->post();
            $response = $this->claim_expenses_model->update_remark($data);
            echo json_encode($response);
    }
    
   public function export_claim_expenses(){
        if(!class_exists('XLSXReader_fin')){
    		require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
    	}
    	require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
    	
    	if($this->input->post()){
    	
    	 $this->load->model('hsn_master_model');
         $selected_company_details = $this->hsn_master_model->get_company_detail();
         $FY = $_SESSION['finacial_year'];
         
        $data = $this->input->post();
        $table_data = $this->claim_expenses_model->get_filter_table($data);
    		$dateObj   = DateTime::createFromFormat('!m', $data['month_data']);
        $monthName = $dateObj->format('F');
    		$writer = new XLSXWriter();
    	$j=0;
    		$company_name = array($selected_company_details->company_name);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 8);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_name);
    	$j++;	
    		$address = $selected_company_details->address;
    		$company_addr = array($address);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 8);  //merge cells
    		$writer->writeSheetRow('Sheet1', $company_addr);
    	$j++;	
    		$msg = "Claim Expenses Report";
    		$filter = array($msg);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 8);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter);
    	$j++;	
    	    $msg1 = "Report Month : ".$monthName;
    		$filter1 = array($msg1);
    		$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 8);  //merge cells
    		$writer->writeSheetRow('Sheet1', $filter1);
    	$j++;	
    		if($this->input->post('deparment_name')){
    		    $msg2 = "Department : ".$this->input->post('deparment_name');
    		    $filter2 = array($msg2);
    		    $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 8);  //merge cells
    		    $writer->writeSheetRow('Sheet1', $filter2);
    		    $j++;
    		}
    		if($this->input->post('staff_id')){
    		    $msg3 = " Staff name : ".$table_data[0]["firstname"].' '.$table_data[0]["lastname"];
    		    $filter3 = array($msg3);
    		    $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 8);  //merge cells
    		    $writer->writeSheetRow('Sheet1', $filter3);
    		    $j++;
    		}
    		if($this->input->post('company')){
    		    
    		    $msg = "Company Name : ".$this->input->post('company_name');
    		    $filter = array($msg);
    		    $writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 8);  //merge cells
    		    $writer->writeSheetRow('Sheet1', $filter);
    		    $j++;
    		}
    		
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
            $list_add[] = "";
            $list_add[] = "";
            $list_add[] = "";
            //$list_add[] = "";
            $writer->writeSheetRow('Sheet1', $list_add);
            
            
            $set_col_tk = [];
    		$set_col_tk["Date"] =  'Date';
    		 if($data['staff_id'] == ''){
    		$set_col_tk["Staff"] = 'Staff';
    		 }
    		$set_col_tk["Start Time"] = 'Start Time';
    		$set_col_tk["End Time"] = 'End Time';
    		$set_col_tk["Net Time"] = 'Net Time';
    		$set_col_tk["Location Counter"] = 'Location Counter';
    		$set_col_tk["Attendence_on_ERP"] = 'Attendence on ERP';
    		$set_col_tk["From"] = 'From';
    		$set_col_tk["To"] = 'To';
    		$set_col_tk["DA_Type"] = 'DA Type';
    		$set_col_tk["DA_Amount"] = 'DA Amount';
    		$set_col_tk["Travel_KM_entered"] = 'Travel KM entered';
    		$set_col_tk["Travel_Km_on_Software"] = 'Travel Km on Software';
    		$set_col_tk["Approved_KM_by_HR"] = 'Approved KM by HR';
    		$set_col_tk["Travel_Mode"] = 'Travel Mode';
    		$set_col_tk["TA_Amount"] = 'TA Amount';
    		$set_col_tk["Misc_Exp"] = 'Misc Exp';
    		$set_col_tk["Misc_Exp_Reason"] = 'Misc Exp Reason';
    		$set_col_tk["Total_Claim_for_a_Day"] = 'Total Claim for a Day';
    		$set_col_tk["Passed_Amount_for_a_Day"] = 'Passed Amount for a Day';
    		$set_col_tk["Remark"] = 'Remark';
    		//$set_col_tk["Document"] = 'Document';
    		$writer_header = $set_col_tk;
    		$writer->writeSheetRow('Sheet1', $writer_header);
            
    	    $i =1;
            $taxable_amt = 0;
            $tcs = 0;
            
            if($data['month_data'] <= 03){
         $year = '20'.$FY+1;
     }else{
          $year = '20'.$FY;
     }
       $d=cal_days_in_month(CAL_GREGORIAN,$data['month_data'],$year);
       $ii = 1;
       for($ii = 1;$ii <=$d;$ii++){
           if($ii < 10){
               $ii = '0'.$ii;
           }
           $date = $year.'-'.$data['month_data'].'-'.$ii;
           $match = 0;
           foreach($table_data as $k => $value){
               
               if($date == substr($value["date_claim"],0,10)){
                    $match = 1;
                    $travel_detail = get_travel_detail_by_staff_id($value['staffid'],$value['date_claim']);
                $j = 1;
                $dist_cal_new = 0.00;
                $pi80 = M_PI / 180; 
                $r = 6372.797; // mean radius of Earth in km
                foreach ($travel_detail as $key1 => $value1) {
                  if($j == 1){
                    $value_array = explode(",", $value1["location_list"]);
                       $lat1 = $value_array["0"];
                       $lon1 = $value_array["1"];
                       $lat1 *= $pi80; 
                        $lon1 *= $pi80;
                    }
                    if($j>1){
                        $value_array = explode(",", $value1["location_list"]);
                        $lat2 = $value_array["0"];
                        $lon2 = $value_array["1"];
                        $lat2 *= $pi80; 
                                    $lon2 *= $pi80;
                        if($lat1 == $lat2 && $lon1 == $lon2){       
                        }else{
                            $dlat = $lat2 - $lat1; 
                            $dlon = $lon2 - $lon1; 
                            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                            $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                            $km = $r * $c; 
                            $lat1 = $lat2;
                            $lon1 = $lon2;
                            $dist_cal_new =  $dist_cal_new  + $km;           
                           }             
                        }
                     $j++;
                 }
                $distance = round($dist_cal_new, 2);
    			$list_add = [];
    			$list_add[] =  _d(substr($value["date_claim"],0,10));
    			if($data['staff_id'] == ''){
                    $list_add[] = $value["firstname"].' '.$value["lastname"]; 
                }
                
                $list_add[] = unserialize($value["check_in"]);
                $list_add[] = unserialize($value["check_out"]);
                $difference = 0;
                $time2 = strtotime(unserialize($value["check_out"]));
                $time1 = strtotime(unserialize($value["check_in"]));
                $difference = round(abs($time2 - $time1) / 3600,2);
                $list_add[] = $difference;
                $list_add[] = $j -1;
                if($value["type_check"] !=''){
                   $list_add[] = "P";  
                }else{
                     $list_add[] = 'A';  
                }
               
    			$list_add[] = $value["name"];
    			$list_add[] = $value["market"];
    			$list_add[] = $value["da_type"];
    			$list_add[] = '';
    			$list_add[] = $value["kilometer"];
    			$list_add[] = $distance;
    			$list_add[] = $value["approve_km_by_hr"];
    			$list_add[] = $value["travel_mode"];
    			$list_add[] = $value["travel_expenses"];
    			$list_add[] = $value["misc_expenses"];
    			
    			$list_add[] = $value["reason"];
    			$list_add[] = $value["travel_expenses"]+$value["misc_expenses"];
    			$list_add[] = $value["passed_amount_per_day"];
    			$list_add[] = $value["remark"];
    			
    			if($value["image_path"]){
                   $img_array =  explode(",",$value["image_path"]);
                   $i = 1;
                   $link = '';
                   foreach($img_array as $data_img){
                        if($i > 1){
                           $html.= ',';
                       }
                      $html.= '<a href="'.base_url($data_img).'" target="_blank">View'.$i.'</a>';
                      $newLink = 'https://erp.crazygroup.in/'.$data_img;
                      $link = $link.','.$newLink;
                      
                  $i++; }
                  //$list_add[] = $link;
               }else{
                
               }
    		    
    			$writer->writeSheetRow('Sheet1', $list_add);
               }
           }
           if($match < 1){
               $list_add = [];
               $list_add[] = _d($date);
               $list_add[] = 'A'; 
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
               $list_add[] = ''; 
               $list_add[] = ''; 
               $list_add[] = ''; 
               $list_add[] = ''; 
               $list_add[] = ''; 
               $writer->writeSheetRow('Sheet1', $list_add);
           }
           
       }
    	/*	foreach ($table_data as $k => $value) {
    		    	$travel_detail = get_travel_detail_by_staff_id($value['staffid'],$value['date_claim']);
                $j = 1;
                $dist_cal_new = 0.00;
                $pi80 = M_PI / 180; 
                $r = 6372.797; // mean radius of Earth in km
                foreach ($travel_detail as $key1 => $value1) {
                  if($j == 1){
                    $value_array = explode(",", $value1["location_list"]);
                       $lat1 = $value_array["0"];
                       $lon1 = $value_array["1"];
                       $lat1 *= $pi80; 
                        $lon1 *= $pi80;
                    }
                    if($j>1){
                        $value_array = explode(",", $value1["location_list"]);
                        $lat2 = $value_array["0"];
                        $lon2 = $value_array["1"];
                        $lat2 *= $pi80; 
                                    $lon2 *= $pi80;
                        if($lat1 == $lat2 && $lon1 == $lon2){       
                        }else{
                            $dlat = $lat2 - $lat1; 
                            $dlon = $lon2 - $lon1; 
                            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                            $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                            $km = $r * $c; 
                            $lat1 = $lat2;
                            $lon1 = $lon2;
                            $dist_cal_new =  $dist_cal_new  + $km;           
                           }             
                        }
                     $j++;
                 }
                $distance = round($dist_cal_new, 2);
    			$list_add = [];
    			$list_add[] =  _d(substr($value["date_claim"],0,10));
    			 if($data['staff_id'] == ''){
                $list_add[] = $value["firstname"].' '.$value["lastname"]; 
                 }
                if($value["type_check"] !=''){
                   $list_add[] = "P";  
                }else{
                     $list_add[] = '-';  
                }
               
    			$list_add[] = $value["name"];
    			$list_add[] = $value["market"];
    			$list_add[] = $value["da_type"];
    			$list_add[] = '';
    			$list_add[] = $value["kilometer"];
    			$list_add[] = $distance;
    			$list_add[] = $value["approve_km_by_hr"];
    			$list_add[] = $value["travel_mode"];
    			$list_add[] = $value["travel_expenses"];
    			$list_add[] = $value["misc_expenses"];
    			
    			$list_add[] = $value["reason"];
    			$list_add[] = $value["travel_expenses"]+$value["misc_expenses"];
    			$list_add[] = $value["passed_amount_per_day"];
    			$list_add[] = $value["remark"];
    			$writer->writeSheetRow('Sheet1', $list_add);
    	}*/
    	
    		$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
    		foreach($files as $file){
    			if(is_file($file)) {
    				unlink($file); 
    			}
    		}
    		$filename = 'Claim_expensesReport.xlsx';
    		$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
    		echo json_encode([
    			'site_url'          => site_url(),
    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
    		]);
    		die;
    	}
    }
    
    public function load_data(){
         $this->load->model('hsn_master_model');
         $company_detail = $this->hsn_master_model->get_company_detail();
         
        $data = $this->input->post();
        $FY = $_SESSION['finacial_year'];
        if($data['deparment'] != ''){
            $data_department = $this->db->select('name')->get_where('tbldepartments',array('departmentid'=>$data['deparment']))->row_array();
        }
         $dateObj   = DateTime::createFromFormat('!m', $data['month_data']);
        $monthName = $dateObj->format('F');
        // print_r($data);
        $table_data = $this->claim_expenses_model->get_filter_table($data);
         //echo '<pre>';
         //print_r($table_data);die;
        if(count($table_data) > 0){ 
           
       
       $html ='';
       
       $html.='<table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">';
       $html.='<thead>';
       $html.='<tr style="display:none;">';
      
          $html.='<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">'.$company_detail->company_name.'</span><br><span style="font-size:10px;font-weight:600;">'.$company_detail->address.'</span><br><span style="font-size:10px;font-weight:600;">Claim Expenses</span><br><span class="report_for" style="font-size:10px;">Filter Month: '.$monthName;
         if($data['staff_id'] != ''){
          $html.=', Staff name: '.$table_data[0]["firstname"].' '.$table_data[0]["lastname"];
         }
          if($data['deparment'] != ''){
            $html.=', Deparment : '.$data_department['name'];   
          }
          if($data['company'] != ''){
              if($data['company'] == 1){
                  $company = 'Crazy Snacks Pvt. Ltd.';
              }else if($data['company'] == 2){
                   $company = 'CrazyFun Foods (P) Ltd.';
              }else if($data['company'] == 3){
                   $company = 'CRAZY BAKERY UDYOG';
              }
               $html.=', Company name: '.$company;
          }
          $html.='</span></h5></td>';  
    //   }else{
    //       $html.='<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;">'.$company_detail->company_name.'</span><br><span style="font-size:10px;font-weight:600;">'.$company_detail->address.'</span><br><span style="font-size:10px;font-weight:600;">Claim Expenses</span><br><span class="report_for" style="font-size:10px;">Filter From date: '.$data[from_date].' To date: '.$data[to_date].'</span></h5></td>';
             
    //   }
        $html.= '</tr>';
       $html.= '<tr>'; 
       $html.= '<th id="sl">Date <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>'; 
        if($data['staff_id'] == ''){
            $html.= '<th>Staff</th>'; 
       }
       $html.= '<th style="white-space: pre-line; width: 5%;">Attendence on ERP</th>'; 
       $html.= '<th>From</th>'; 
       $html.= '<th>To</th>'; 
       $html.= '<th>DA Type</th>'; 
       $html.= '<th>DA Amount</th>'; 
       $html.= '<th style="white-space: pre-line; width: 5%;">Travel KM entered</th>'; 
       $html.= '<th style="white-space: pre-line; width: 5%;">Travel Km on Software</th>'; 
       $html.= '<th style="white-space: pre-line; width: 5%;">Approved KM by HR</th>'; 
       $html.= ' <th>Travel Mode</th>'; 
       $html.= '<th>TA Amount</th>'; 
       $html.= '<th>Misc Exp</th>'; 
       $html.= '<th>Misc Exp Reason</th>'; 
       $html.= '<th style="white-space: pre-line; width: 5%;">Total Claim for a Day</th>'; 
       $html.= '<th style="white-space: pre-line; width: 7%;">Passed Amount for a Day</th>'; 
       $html.= '<th style="width: 15%;">Remark</th>'; 
       $html.= '<th style="width: 4%;">Documents</th>'; 
       $html.= '<th style="width: 4%; display:none;" >Id</th>'; 
      
       $html.= '</tr>'; 
       $html.='</thead>';
       $html.='<tbody>';
       if($data['month_data'] <= 03){
         $year = '20'.$FY+1;
     }else{
          $year = '20'.$FY;
     }
       $d=cal_days_in_month(CAL_GREGORIAN,$data['month_data'],$year);
       $ii = 1;
       for($ii = 1;$ii <=$d;$ii++){
           if($ii < 10){
               $ii = '0'.$ii;
           }
           $date = $year.'-'.$data['month_data'].'-'.$ii;
           $match = 0;
           foreach($table_data as $value){
               
               if($date == substr($value["date_claim"],0,10)){
                    $match = 1;
                    $travel_detail = get_travel_detail_by_staff_id($value['staffid'],$value['date_claim']);
                    $j = 1;
                    $dist_cal_new = 0.00;
                    $pi80 = M_PI / 180; 
                    $r = 6372.797; // mean radius of Earth in km
                    foreach ($travel_detail as $key1 => $value1) {
                      if($j == 1){
                        $value_array = explode(",", $value1["location_list"]);
                           $lat1 = $value_array["0"];
                           $lon1 = $value_array["1"];
                           $lat1 *= $pi80; 
                            $lon1 *= $pi80;
                        }
                        if($j>1){
                            $value_array = explode(",", $value1["location_list"]);
                            $lat2 = $value_array["0"];
                            $lon2 = $value_array["1"];
                            $lat2 *= $pi80; 
                                        $lon2 *= $pi80;
                            if($lat1 == $lat2 && $lon1 == $lon2){       
                            }else{
                                $dlat = $lat2 - $lat1; 
                                $dlon = $lon2 - $lon1; 
                                $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                                $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                                $km = $r * $c; 
                                $lat1 = $lat2;
                                $lon1 = $lon2;
                                $dist_cal_new =  $dist_cal_new  + $km;           
                               }             
                            }
                         $j++;
                     }
                    $distance = round($dist_cal_new, 2);
               $html.= '<tr>'; 
               $html.= '<td>'. _d(substr($value["date_claim"],0,10)).'</td>'; 
                 if($data['staff_id'] == ''){
                $html.= '<td>'.$value["firstname"].' '.$value["lastname"].'</td>'; 
                 }
                if($value["type_check"] !=''){
                    $html.= '<td align="center">P</td>';  
                }else{
                    $html.= '<td align="center">A</td>';  
                }
                 $html.= '<td>'.$value["name"].'</td>'; 
               $html.= '<td>'.$value["market"].'</td>'; 
                $html.= '<td>'.$value["da_type"].'</td>';
                $html.= '<td></td>'; 
              
               $html.= '<td>'.$value["kilometer"].'</td>'; 
              
                $html.= '<td>'.$distance.'</td>'; 
               $html.= '<td><input type="text"  class="approve_km_by_hr" data-id="'. $value['cliam_id'].'" name="approve_km_by_hr[]" value="'.$value["approve_km_by_hr"].'" style="width:95%;height:25px;font-size:12px;"></td>'; 
               $html.= '<td>'. $value["travel_mode"].'</td>'; 
               $html.= '<td>'.$value["travel_expenses"].'</td>'; 
               $html.= '<td>'.$value["misc_expenses"].'</td>'; 
               $html.= '<td>'.$value["reason"].'</td>'; 
               $html.= '<td>'.($value["travel_expenses"]+$value["misc_expenses"]).'</td>'; 
               $html.= '<td><input type="text"  class="passed_amount_per_day" id="" data-id="'.$value['cliam_id'].'" value="'. $value["passed_amount_per_day"].'" name="passed_amount_per_day[]" style="width:95%;height:25px;font-size:12px;"></td>'; 
               $html.= '<td><input type="text" class="remark" data-id="'.$value['cliam_id'].'"name="remark[]" value="'.$value["remark"].'" style="width:95%;height:25px;font-size:12px;"></td>'; 
              if($value["image_path"]){
                   $img_array =  explode(",",$value["image_path"]);
                   $html.= '<td>';
                   $i = 1;
                   
                   foreach($img_array as $data_img){
                        if($i > 1){
                           $html.= ',';
                       }
                      $html.= '<a href="'.base_url($data_img).'" target="_blank">View'.$i.'</a>';   
                  $i++; }
                  $html.= '</td>';  
                 // $html.= '<td><a href="'.base_url($value["image_path"]).'" target="_blank">View</a></td>';  
               }else{
                   $html.= '<td></td>';
               }
               $html.= '<td style="width: 4%; display:none;" ><input type="hidden" name="approve_km_by_hr_id[]" value="'.$value['cliam_id'].'"></td>'; 
               $html.= '</tr>';
                }
           }
           if($match < 1){
               $html.= '</tr>';
               $html.= '<td>'._d($date).'</td>';
               $html.= '<td align="center">A</td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '<td></td>';
               $html.= '</tr>';
           }
           
       }
       /*foreach($table_data as $value){
           	$travel_detail = get_travel_detail_by_staff_id($value['staffid'],$value['date_claim']);
                $j = 1;
                $dist_cal_new = 0.00;
                $pi80 = M_PI / 180; 
                $r = 6372.797; // mean radius of Earth in km
                foreach ($travel_detail as $key1 => $value1) {
                  if($j == 1){
                    $value_array = explode(",", $value1["location_list"]);
                       $lat1 = $value_array["0"];
                       $lon1 = $value_array["1"];
                       $lat1 *= $pi80; 
                        $lon1 *= $pi80;
                    }
                    if($j>1){
                        $value_array = explode(",", $value1["location_list"]);
                        $lat2 = $value_array["0"];
                        $lon2 = $value_array["1"];
                        $lat2 *= $pi80; 
                                    $lon2 *= $pi80;
                        if($lat1 == $lat2 && $lon1 == $lon2){       
                        }else{
                            $dlat = $lat2 - $lat1; 
                            $dlon = $lon2 - $lon1; 
                            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                            $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                            $km = $r * $c; 
                            $lat1 = $lat2;
                            $lon1 = $lon2;
                            $dist_cal_new =  $dist_cal_new  + $km;           
                           }             
                        }
                     $j++;
                 }
                $distance = round($dist_cal_new, 2);
           $html.= '<tr>'; 
           $html.= '<td>'. _d(substr($value["date_claim"],0,10)).'</td>'; 
             if($data['staff_id'] == ''){
            $html.= '<td>'.$value["firstname"].' '.$value["lastname"].'</td>'; 
             }
            if($value["type_check"] !=''){
                $html.= '<td>P</td>';  
            }else{
                $html.= '<td>-</td>';  
            }
             $html.= '<td>'.$value["name"].'</td>'; 
           $html.= '<td>'.$value["market"].'</td>'; 
            $html.= '<td>'.$value["da_type"].'</td>';
            $html.= '<td></td>'; 
          
           $html.= '<td>'.$value["kilometer"].'</td>'; 
          
            $html.= '<td>'.$distance.'</td>'; 
           $html.= '<td><input type="text"  class="approve_km_by_hr" data-id="'. $value['cliam_id'].'" name="approve_km_by_hr[]" value="'.$value["approve_km_by_hr"].'" style="width:95%;height:25px;font-size:12px;"></td>'; 
           $html.= '<td>'. $value["travel_mode"].'</td>'; 
           $html.= '<td>'.$value["travel_expenses"].'</td>'; 
           $html.= '<td>'.$value["misc_expenses"].'</td>'; 
           $html.= '<td>'.$value["reason"].'</td>'; 
           $html.= '<td>'.($value["travel_expenses"]+$value["misc_expenses"]).'</td>'; 
           $html.= '<td><input type="text"  class="passed_amount_per_day" id="" data-id="'.$value['cliam_id'].'" value="'. $value["passed_amount_per_day"].'" name="passed_amount_per_day[]" style="width:95%;height:25px;font-size:12px;"></td>'; 
           $html.= '<td><input type="text" class="remark" data-id="'.$value['cliam_id'].'"name="remark[]" value="'.$value["remark"].'" style="width:95%;height:25px;font-size:12px;"></td>'; 
          if($value["image_path"]){
               $img_array =  explode(",",$value["image_path"]);
               $html.= '<td>';
               $i = 1;
               
               foreach($img_array as $data_img){
                    if($i > 1){
                       $html.= ',';
                   }
                  $html.= '<a href="'.base_url($data_img).'" target="_blank">View'.$i.'</a>';   
              $i++; }
              $html.= '</td>';  
             // $html.= '<td><a href="'.base_url($value["image_path"]).'" target="_blank">View</a></td>';  
           }else{
               $html.= '<td></td>';
           }
           $html.= '<td style="width: 4%; display:none;" ><input type="hidden" name="approve_km_by_hr_id[]" value="'.$value['cliam_id'].'"></td>'; 
           $html.= '</tr>'; 
       }*/
       $html.='</tbody>';
        $html.='</table>';
        }else{
             $html ='';
            $html.='<span  id="searchh22" style="color:red;" >No data Found</span>';
        } 
    //   echo $html;die;
       $response = array('html'=>$html);
        echo json_encode($response);
    }
    public function update_claim_fields(){
        // echo '<pre>';
        $data = $this->input->post();
        // print_r($data);die;
        foreach($data[approve_km_by_hr_id] as $key=>$value){
            $this->db->set('approve_km_by_hr', $data['approve_km_by_hr'][$key]);
            $this->db->set('passed_amount_per_day', $data['passed_amount_per_day'][$key]);
            $this->db->set('remark', $data['remark'][$key]);
            $this->db->where('id',$value);
            $this->db->update(db_prefix() . 'claimexpense');
        }
        
            set_alert('success', _l('update Successfully'));
            redirect(admin_url('Claim_expenses'));
        // die;
    }
    
    
    
    
    
    
    
}