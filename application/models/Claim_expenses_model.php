<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Claim_expenses_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get expense(s)
     * @param  mixed $id Optional expense id
     * @return mixed     object or array
     */ 
   public function get_data_table(){
            $cur_date = to_sql_date(date('d/m/Y')); 
            $first_date = to_sql_date("01/".date('m')."/".date('Y'));
            
        $selected_company = $this->session->userdata('root_company');
           $this->db->select(db_prefix() . 'claimexpense.*,'.db_prefix() .'staff.*,'.db_prefix() . 'claimexpense.id as cliam_id,'.db_prefix() . 'claimexpense.date as date_claim,'.db_prefix() .'timesheets_timesheet.*,'.db_prefix() .'check_in_out_app2.*,'.db_prefix() .'staff.staffid,'.db_prefix() .'headquarter.name,'.db_prefix() .'staff.firstname,'.db_prefix() .'staff.lastname');
      $this->db->from(db_prefix() . 'claimexpense');
        $this->db->join(db_prefix() .'staff', db_prefix() .'claimexpense.UserID = '.db_prefix() .'staff.AccountID');
      $this->db->join(db_prefix() .'check_in_out_app2', db_prefix() .'staff.staffid = '.db_prefix() .'check_in_out_app2.staff_id AND '.db_prefix() .'claimexpense.date = '.db_prefix() .'check_in_out_app2.date','left');
      $this->db->join(db_prefix() .'timesheets_timesheet', db_prefix() .'staff.staffid = '.db_prefix() .'timesheets_timesheet.staff_id AND '.db_prefix() .'claimexpense.date = '.db_prefix() .'timesheets_timesheet.date_work','left'); 
      $this->db->join(db_prefix() .'headquarter', db_prefix() .'staff.headqurter = '.db_prefix() .'headquarter.id ','left');
      
			$this->db->where(db_prefix() . 'claimexpense.PlantID', $selected_company);
				$this->db->where( db_prefix() . 'claimexpense.date BETWEEN "'.$first_date.' 00:00:00" AND "'.$cur_date.' 23:59:59"');
			$this->db->order_by(db_prefix() . 'claimexpense.date','ASC');
		return	$data = $this->db->get()->result_array();
// 		    echo '<pre>';
// 			print_r($data);
			
   }
   public function get_filter_table($data){
         $FY = $_SESSION['finacial_year'];
         $month = substr($data['month_data'], -2);
     if($month <= 03){
         $year = $FY+1;
          $from_date = '20'.$year.'-'.$month.'-01';
          $to_date = '20'.$year.'-'.$month.'-31';
     }else{
          $from_date = '20'.$FY.'-'.$month.'-01';
          $to_date = '20'.$FY.'-'.$month.'-31';
     }
        //  $from_date = to_sql_date( $data['from_date']);
        //     $to_date = to_sql_date($data['to_date']);
            $staff_id = $data['staff_id'];
               $deparment = $data['deparment'];
               //$company = $data['company'];
        $selected_company = $this->session->userdata('root_company');
           $this->db->select(db_prefix() . 'claimexpense.*,'.db_prefix() . 'staff_departments.*,'.db_prefix() .'staff.*,'.db_prefix() . 'claimexpense.id as cliam_id,'.db_prefix() . 'claimexpense.date as date_claim,'.db_prefix() .'timesheets_timesheet.*,'.db_prefix() .'check_in_out_app2.*,'.db_prefix() .'staff.staffid,'.db_prefix() .'headquarter.name,'.db_prefix() .'staff.firstname,'.db_prefix() .'staff.lastname');
			$this->db->from(db_prefix() . 'claimexpense');
		    $this->db->join(db_prefix() .'staff', db_prefix() .'claimexpense.UserID = '.db_prefix() .'staff.AccountID');
		    $this->db->join(db_prefix() .'staff_departments', db_prefix() .'staff.staffid = '.db_prefix() .'staff_departments.staffid','left');
			$this->db->join(db_prefix() .'check_in_out_app2', db_prefix() .'staff.staffid = '.db_prefix() .'check_in_out_app2.staff_id AND '.db_prefix() .'claimexpense.date = '.db_prefix() .'check_in_out_app2.date','left');
			$this->db->join(db_prefix() .'timesheets_timesheet', db_prefix() .'staff.staffid = '.db_prefix() .'timesheets_timesheet.staff_id AND '.db_prefix() .'claimexpense.date = '.db_prefix() .'timesheets_timesheet.date_work','left'); 
			$this->db->join(db_prefix() .'headquarter', db_prefix() .'staff.headqurter = '.db_prefix() .'headquarter.id ','left');
		
			
				$this->db->where( db_prefix() . 'claimexpense.date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				if($staff_id !=''){
				    	$this->db->where(db_prefix() . 'claimexpense.UserID', $staff_id);
				}
					if($deparment !=''){
				    	$this->db->where(db_prefix() . 'staff_departments.departmentid', $deparment);
				}
				/*if($company !=''){
				    $this->db->where(db_prefix() . 'claimexpense.PlantID', $company);
				}else{*/
				    $this->db->where(db_prefix() . 'claimexpense.PlantID', $selected_company);
				//}
				if($staff_id !=''){
				    // $this->db->order_by(db_prefix() . 'staff.firstname','ASC');
				    	$this->db->order_by(db_prefix() . 'claimexpense.date','ASC');
				}else{
				    	$this->db->order_by(db_prefix() . 'claimexpense.date','ASC');
				}
				
		    $this->db->where(db_prefix() . 'claimexpense.FY', $FY);
		    $this->db->group_by(db_prefix() . 'claimexpense.date,'.db_prefix() . 'claimexpense.UserID');
			$data = $this->db->get()->result_array();
			return $data;
   }
   public function get_staff_details(){
             
        $selected_company = $this->session->userdata('root_company');
        $regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
        $this->db->select(db_prefix() .'staff.staffid,'.db_prefix() .'staff.AccountID,'.db_prefix() .'staff.firstname,'.db_prefix() .'staff.lastname');
	    $this->db->from(db_prefix() . 'staff');
        //$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
		$this->db->where('PlantID',$selected_company);
	    return $this->db->get()->result_array();
   }
   public function approve_km_by_hr($data){
        $selected_company = $this->session->userdata('root_company');
       $value = array(
           'approve_km_by_hr' => $data['value']
           );
           $this->db->where('id',$data['id']);
        //   $this->db->where('PlantID',$selected_company);
       $data_value = $this->db->update(db_prefix() . 'claimexpense',$value);
        return $data_value;
   }
    public function passed_amount_per_day($data){
        $selected_company = $this->session->userdata('root_company');
       $value = array(
           'passed_amount_per_day' => $data['value']
           );
           $this->db->where('id',$data['id']);
        //   $this->db->where('PlantID',$selected_company);
       $data_value = $this->db->update(db_prefix() . 'claimexpense',$value);
        return $data_value;
   }
     public function update_remark($data){
        $selected_company = $this->session->userdata('root_company');
 
       $value = array(
           'remark' => $data['value']
           );
           $this->db->where('id',$data['id']);
        //   $this->db->where('PlantID',$selected_company);
       $data_value = $this->db->update(db_prefix() . 'claimexpense',$value);
        return $data_value;
   }
    
    
    
}