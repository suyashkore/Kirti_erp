<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	/**
		* Hr profile model
	*/
	class Hr_profile_model extends App_Model
	{
		/**
			* get hr profile dashboard data
			* @return array 
		*/
		public function get_hr_profile_dashboard_data(){
			$data_hrm = [];
			$staff = $this->staff_model->get();
			$total_staff = count($staff);
			$new_staff_in_month = $this->db->query('SELECT * FROM '.db_prefix().'staff WHERE MONTH(datecreated) = '.date('m').' AND YEAR(datecreated) = '.date('Y'))->result_array();
			$staff_working = $this->db->query('SELECT * FROM '.db_prefix().'staff WHERE status_work = "working"')->result_array();
			$staff_birthday = $this->db->query('SELECT * FROM '.db_prefix().'staff WHERE active = 1 AND MONTH(birthday) = '.date('m').' ORDER BY birthday ASC')->result_array();
			$staff_inactivity = $this->db->query('SELECT * FROM '.db_prefix().'staff WHERE status_work = "inactivity" AND staffid in (SELECT staffid FROM '.db_prefix().'hr_list_staff_quitting_work where dateoff >= \''.date('Y-m-01').' 00:00:00'.'\' and dateoff <= \''.date('Y-m-t').' 23:59:59'.'\')')->result_array();
			$overdue_contract = $this->db->query('SELECT * FROM '.db_prefix().'hr_staff_contract WHERE end_valid < "'.date('Y-m-d').'" AND contract_status = "valid"')->result_array();
			$expire_contract = $this->db->query('SELECT * FROM '.db_prefix().'hr_staff_contract WHERE end_valid <= "'.date('Y-m-d',strtotime('+7 day',strtotime(date('Y-m-d')))).'" AND end_valid >= "'.date('Y-m-d').'" AND contract_status = "valid"')->result_array();
			$data_hrm['staff_birthday'] = $staff_birthday;
			$data_hrm['total_staff'] = $total_staff;
			$data_hrm['new_staff_in_month'] = count($new_staff_in_month);
			$data_hrm['staff_working'] = count($staff_working);
			$data_hrm['staff_inactivity'] = count($staff_inactivity);
			$data_hrm['overdue_contract'] = count($overdue_contract);
			$data_hrm['expire_contract'] = count($expire_contract);
			$data_hrm['overdue_contract_data'] = $overdue_contract;
			$data_hrm['expire_contract_data'] = $expire_contract;
			return $data_hrm;
		}
		//=============== Add New Production Order =====================================
		function SaveUserAttendance($data)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$UserAttendanceString = $data['UserAttendanceString'];
			$UserAttendanceArray = json_decode($UserAttendanceString, true);
			// echo "<pre>";print_r($UserAttendanceArray);die;
			$date = to_sql_date($data["date"]);
			$getData = $this->GetUserAttendance($data["date"]);
			foreach ($getData as $key => $value) {
				$audit = array(
				"PlantID"=>$value["PlantID"],
				"TransDate"=>$value["TransDate"],
				"AccountID"=>$value["AccountID"],
				"InDateTime"=>$value["InDateTime"],
				"OutDateTime"=>$value["OutDateTime"],
				"UserID"=>$value["UserID"],
                );
				$this->db->insert(db_prefix().'UserAttendanceAudit', $audit);
			}
			// Delete Records
			// Step 1: Get relevant AccountIDs
			$this->db->select('tblstaff.AccountID');
			$this->db->from(db_prefix() . 'staff');
			$this->db->join(db_prefix() . 'staff_departments', 'tblstaff_departments.staffid = tblstaff.staffid', 'LEFT');
			$this->db->where('tblstaff.active', 1);
			if (!empty($data["Department"])) {
				$this->db->where('tblstaff_departments.departmentid', $data["Department"]);
			}
			$subquery = $this->db->get();
			$account_ids = array_column($subquery->result_array(), 'AccountID'); // Convert result to array
			// Step 2: Delete records from UserAttendance
			$this->db->where('PlantID', $selected_company);
			$this->db->where('TransDate >=', $date . ' 00:00:00');
			$this->db->where('TransDate <=', $date . ' 23:59:59');
			if (!empty($account_ids)) {
				$this->db->where_in('AccountID', $account_ids);
			}
			$this->db->delete(db_prefix() . 'UserAttendance');
			$Count = 0;
			foreach($UserAttendanceArray as $val){
				if($val[1] !="" || $val[2] !=""){
					// Staff Wise Attendance Update
					$StaffWiseAttendance = array(
			        "PlantID"=>$selected_company, 
			        "TransDate"=>$date." ".date('H:i:s'), 
			        "AccountID"=>$val[0], 
			        // "InDateTime"=>$date." ".$val[1].":00", 
			        // "OutDateTime"=>$date." ".$val[2].":00", 
			        "UserID"=>$this->session->userdata('username')
					);
					if($val[1] !=""){
						$StaffWiseAttendance["InDateTime"] = $date." ".$val[1].":00";
					}
					if($val[2] !=""){
						$StaffWiseAttendance["OutDateTime"] = $date." ".$val[2].":00";
					}
					if($this->db->insert(db_prefix() . 'UserAttendance', $StaffWiseAttendance)){
						$Count++;
					}
				}
			}
			return $Count;	
		}
		//======================= Get Current date User Attendance =====================
		public function GetUserAttendance($date)
		{
			$selected_company = $this->session->userdata('root_company');
			if(!empty($date)){
				$CurrentDate = to_sql_date($date);
				}else{
				$CurrentDate = date("Y-m-d");
			}
			$sql = "SELECT tblUserAttendance.* FROM tblUserAttendance WHERE PlantID = '".$selected_company."' AND 
			TransDate BETWEEN '".$CurrentDate." 00:00:00' AND '".$CurrentDate." 23:59:59'" ;
			return $this->db->query($sql)->result_array();
		}
		public function get_break_dates($type = ''){
			if($type != ''){
				$this->db->where('off_type', $type);
				return $this->db->get(db_prefix().'day_off')->result_array();
				}else{
				return $this->db->get(db_prefix().'day_off')->result_array();
			}
		}
		public function get_Shift_data($filter){
			// $this->db->where('("'.$date.'" >=  from_date and "'.$date.'" <= to_date)');
			// return $this->db->get(db_prefix().'work_shift')->result_array();
			// Extract year and month from input (e.g., 2024-12)
			$month = $filter['month'];
			$start_date = $month . "-01"; // Start of the month
			$end_date = date("Y-m-t", strtotime($start_date)); // End of the month
			// Query to join tblstaff and tblUserAttendance
			// $this->db->select('*');
			// $this->db->from('tblwork_shift_detail_number_day');
			// $this->db->join('tblwork_shift', 'tblwork_shift.id = tblwork_shift_detail_number_day.work_shift_id');
			// $this->db->where('DATE(tblwork_shift.from_date) >=', $start_date);
			// $this->db->where('DATE(tblwork_shift.to_date) <=', $end_date);
			// $query = $this->db->get();
			$this->db->select('*');
			$this->db->from('tblwork_shift_detail_number_day');
			$this->db->join('tblwork_shift', 'tblwork_shift.id = tblwork_shift_detail_number_day.work_shift_id');
			// Check for overlapping shifts
			$this->db->where('tblwork_shift.from_date <=', $end_date);
			$this->db->where('tblwork_shift.to_date >=', $start_date);
			$query = $this->db->get();
			// Return the result as an array
			return $query->result_array();
		}
		public function get_leaves_data($filter){
			$month = $filter['month'];
			$start_date = $month . "-01"; // Start of the month
			$end_date = date("Y-m-t", strtotime($start_date)); // End of the month
			$this->db->select('*');
			$this->db->from('tbltimesheets_requisition_leave');
			$this->db->where('DATE(tbltimesheets_requisition_leave.start_time) >=', $start_date);
			$this->db->where('DATE(tbltimesheets_requisition_leave.end_time) <=', $end_date);
			$this->db->where('tbltimesheets_requisition_leave.status ', '1');
			$query = $this->db->get();
			// Return the result as an array
			return $query->result_array();
		}
		public function GetMonthWiseAttendance($filter)
		{
			// Extract year and month from input (e.g., 2024-12)
			$month = $filter['month'];
			$start_date = $month . "-01"; // Start of the month
			$end_date = date("Y-m-t", strtotime($start_date)); // End of the month
			// Query to join tblstaff and tblUserAttendance
			$this->db->select('
			tblstaff.AccountID,
			tblstaff.firstname,
			tblstaff.lastname,
			tblUserAttendance.TransDate,
			tblUserAttendance.InDateTime,
			tblUserAttendance.OutDateTime
			');
			$this->db->from('tblstaff');
			$this->db->join('tblUserAttendance', 'tblstaff.AccountID = tblUserAttendance.AccountID');
			$this->db->where('DATE(tblUserAttendance.InDateTime) >=', $start_date);
			$this->db->where('DATE(tblUserAttendance.InDateTime) <=', $end_date);
			$this->db->order_by('tblUserAttendance.InDateTime', 'ASC');
			$query = $this->db->get();
			// Return the result as an array
			return $query->result_array();
		}
		//-----------------------------------------------------
		public function get_city_by_id($id){
			$query = $this->db->get_where('tblxx_citylist', array('state_id' => $id));
			return $result = $query->result_array();
		}
		//-----------------------------------------------------
		public function get_account_balance_details($plantID,$AccountID,$FY){
			$query = $this->db->get_where('tblaccountbalances', array('PlantID' => $plantID,'AccountID' => $AccountID,'FY' => $FY));
			return $result = $query->row();
		}
		//-----------------------------------------------------
		public function get_quarter_by_id($id){
			$query = $this->db->get_where('tblheadquarter', array('state_id' => $id));
			return $result = $query->result_array();
		}
		//-----------------------------------------------------
		public function add_head_quarter($data){
			$this->db->insert(db_prefix() . 'headquarter', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				log_activity('New Head Quarter Created [ID:' . $insert_id . ', Name:' . $data['name'] . ']');
				return $insert_id;
			}
			return false;
		}
		//-----------------------------------------------------
		public function get_all_rsm($id){
			/*$staff_data = $this->db->query('SELECT staffid,firstname,lastname FROM tblstaff')->result_array();
			return $staff_data;*/
			$query = $this->db->get_where('tblstaff', array('job_position' => $id));
			return $result = $query->result_array();
		}
		//-----------------------------------------------------
		public function get_all_asm($id){
			/*$staff_data = $this->db->query('SELECT staffid,firstname,lastname FROM tblstaff')->result_array();
			return $staff_data;*/
			$query = $this->db->get_where('tblstaff', array('job_position' => $id));
			return $result = $query->result_array();
		}
		//-----------------------------------------------------
		public function get_all_asm2($id){
			$sql = "SELECT * FROM tblstaff WHERE staffid NOT IN (SELECT report_by FROM tblstaff_mapping) And active = 1 AND job_position = ".$id;
			return $this->db->query($sql)->result_array();
		}
		public function get_not_assigned_an_selected_asm($asm_id, $staff_id){
			$sql = "SELECT * FROM tblstaff WHERE staffid NOT IN (SELECT report_by FROM tblstaff_mapping WHERE staff_id != '".$staff_id."') And active = 1 AND job_position = ".$asm_id;
			return $this->db->query($sql)->result_array();
		}
		public function get_not_assigned_an_selected_ase($ase_id, $staff_id){
			$sql = "SELECT * FROM tblstaff WHERE staffid NOT IN (SELECT report_by FROM tblstaff_mapping WHERE staff_id != '".$staff_id."') And active = 1 AND job_position = ".$ase_id;
			return $this->db->query($sql)->result_array();
		}
		public function get_not_assigned_an_selected_so($so_id, $staff_id){
			$sql = "SELECT * FROM tblstaff WHERE staffid NOT IN (SELECT report_by FROM tblstaff_mapping WHERE staff_id != '".$staff_id."') And active = 1 AND job_position = ".$so_id;
			return $this->db->query($sql)->result_array();
		}
		//-----------------------------------------------------
		public function get_all_ase($id){
			/*$staff_data = $this->db->query('SELECT staffid,firstname,lastname FROM tblstaff')->result_array();
			return $staff_data;*/
			$query = $this->db->get_where('tblstaff', array('job_position' => $id));
			return $result = $query->result_array();
		}
		//-----------------------------------------------------
		public function get_all_ase2($id){
			$sql = "SELECT * FROM tblstaff WHERE staffid NOT IN (SELECT report_by FROM tblstaff_mapping) AND job_position = ".$id;
			return $this->db->query($sql)->result_array();
		}
		//-----------------------------------------------------
		public function get_all_distributor(){
			/*$distributor_data = $this->db->query('SELECT id,name, FROM tblxx_customer_list')->result_array();
			return $distributor_data;*/
			/*$query = $this->db->get_where('tblxx_customer_list');
			return $result = $query->result_array();*/
			$id = 8;
			$selected_company = $this->session->userdata('root_company');
			//$sql = "SELECT * FROM tblxx_customer_list WHERE id NOT IN (SELECT report_by FROM tblstaff_mapping)";
			$sql = "SELECT * FROM tblclients WHERE AccountID NOT IN (SELECT report_by FROM tblstaff_mapping) AND PlantID=".$selected_company;
			return $this->db->query($sql)->result_array();
		}
		public function get_not_assigned_an_selected_distributor($staff_id){
			$selected_company = $this->session->userdata('root_company');
			$sql = "SELECT * FROM tblclients WHERE AccountID NOT IN (SELECT report_by FROM tblstaff_mapping WHERE staff_id != '".$staff_id."' AND PlantID !='".$selected_company."') AND PlantID=".$selected_company;
			return $this->db->query($sql)->result_array();
		}
		//-----------------------------------------------------
		public function get_all_so($id){
			$sql = "SELECT * FROM tblstaff WHERE staffid NOT IN (SELECT report_by FROM tblstaff_mapping) AND job_position = ".$id;
			return $this->db->query($sql)->result_array();
		}
		//-----------------------------------------------------
		public function get_all_so2($id){
			$sql = "SELECT * FROM tblstaff WHERE job_position = ".$id;
			return $this->db->query($sql)->result_array();
		}
		//-----------------------------------------------------
		public function get_not_assigned_rsm(){
			$id = 4;
			$sql = "SELECT * FROM tblstaff WHERE staffid NOT IN (SELECT report_by FROM tblstaff_mapping) AND job_position = ".$id;
			return $this->db->query($sql)->result_array();
		}
		/**
			* staff chart by age
			* @return array 
		*/
		public function staff_chart_by_age()
		{
			$staffs = $this->staff_model->get();
			$chart = [];
			$status_1 = ['name' => _l('18_24_age'), 'color' => '#777', 'y' => 0, 'z' => 100];
			$status_2 = ['name' => _l('25_29_age'), 'color' => '#fc2d42', 'y' => 0, 'z' => 100];
			$status_3 = ['name' => _l('30_39_age'), 'color' => '#03a9f4', 'y' => 0, 'z' => 100];
			$status_4 = ['name' => _l('40_60_age'), 'color' => '#ff6f00', 'y' => 0, 'z' => 100];
			foreach ($staffs as $staff) {
				$diff = date_diff(date_create(), date_create($staff['birthday']));
				$age = $diff->format('%Y');
				if($age >= 18 && $age <= 24)
				{
					$status_1['y'] += 1;
					}elseif ($age >= 25 && $age <= 29) {
					$status_2['y'] += 1;
					}elseif ($age >= 30 && $age <= 39) {
					$status_3['y'] += 1;
					}elseif ($age >= 40 && $age <= 60) {
					$status_4['y'] += 1;
				}
			}
			if($status_1['y'] > 0){
				array_push($chart, $status_1);
			}
			if($status_2['y'] > 0){
				array_push($chart, $status_2);
			}
			if($status_3['y'] > 0){
				array_push($chart, $status_3);
			}
			if($status_4['y'] > 0){
				array_push($chart, $status_4);
			}
			return $chart;
		}
		/**
			* contract type chart
			* @return  array
		*/
		public function contract_type_chart()
		{
			$contracts = $this->db->query('SELECT * FROM tblhr_staff_contract')->result_array();
			$statuses = $this->get_contracttype();
			$color_data = ['#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70', '#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1' ,
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			$_data                         = [];
			$total_value =0;
			$has_permission = has_permission('pw_mana_projects', '', 'view');
			$sql            = '';
			foreach ($statuses as $status) {
				$sql .= ' SELECT COUNT(*) as total';
				$sql .= ' FROM ' . db_prefix() . 'hr_staff_contract';
				$sql .= ' WHERE name_contract=' . $status['id_contracttype'];
				$sql .= ' UNION ALL ';
				$sql = trim($sql);
			}
			$result = [];
			if ($sql != '') {
				$sql    = substr($sql, 0, -10);
				$result = $this->db->query($sql)->result();
			}
			foreach ($statuses as $key => $status) {
				$total_value+=(int)$result[$key]->total;
			}
			foreach ($statuses as $key => $status) {
				if($total_value > 0){
					array_push($_data,
					[ 
					'name' => $status['name_contracttype'],
					'y'    => (int)$result[$key]->total,
					'z'    => (number_format(((int)$result[$key]->total/$total_value), 4, '.',""))*100,
					'color'=>$color_data[$key]
					]);
					}else{
					array_push($_data,
					[ 
					'name' => $status['name_contracttype'],
					'y'    => (int)$result[$key]->total,
					'z'    => (number_format(((int)$result[$key]->total/1), 4, '.',""))*100,
					'color'=>$color_data[$key]
					]);
				}
			}
			return $_data;
		}
		/**
			* staff chart by departments
			* @return [type] 
		*/
		public function staff_chart_by_departments()
		{
			$chart = [];
			$color_data = ['#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70' ,
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			$this->db->select(db_prefix().'staff_departments.departmentid, count(staffdepartmentid) as total_staff,'.db_prefix().'departments.name as department_name');
			$this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'staff_departments.departmentid', 'left');
			$this->db->group_by('departmentid');
			$staff_departments = $this->db->get(db_prefix().'staff_departments')->result_array();
			$color_index=0;
			foreach ($staff_departments as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
					'name' 		=> $value['department_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['total_staff'],
					'z' 		=> 100
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['department_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['total_staff'],
					'z' 		=> 100
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		/**
			* staff chart by job positions
			* @return [type] 
		*/
		public function staff_chart_by_job_positions()
		{
			$chart = [];
			$color_data = ['#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70', '#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1' ];
			$this->db->select(db_prefix().'hr_job_position.position_name, count(staffid) as total_staff, job_position');
			$this->db->join(db_prefix() . 'hr_job_position', db_prefix() . 'hr_job_position.position_id = ' . db_prefix() . 'staff.job_position', 'left');
			$this->db->group_by('job_position');
			$staff_departments = $this->db->get(db_prefix().'staff')->result_array();
			$color_index=0;
			foreach ($staff_departments as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
					'name' 		=> $value['position_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['total_staff'],
					'z' 		=> 100
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['position_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['total_staff'],
					'z' 		=> 100
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		/**
			* Get Top Five Customer
			* @return [type] 
		*/
		public function top_five_customer2()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = date('Y').'-'.date('m').'-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = [
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			$this->db->select(db_prefix().'salesmaster.AccountID, SUM(BillAmt) as total_sale,'.db_prefix().'clients.company as company_name');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'salesmaster.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'salesmaster.PlantID');
			$this->db->group_by('AccountID');
			$this->db->where('tblsalesmaster.PlantID',$selected_company);
			$this->db->where('tblsalesmaster.FY',$fy);
			$this->db->where('tblsalesmaster.Transdate >=', $minvalue);
			$this->db->where('tblsalesmaster.Transdate <=', $maxvalue);
			$this->db->group_by('tblsalesmaster.AccountID');
			$this->db->order_by("total_sale", "DESC");
			$this->db->limit(5);
			$staff_departments = $this->db->get(db_prefix().'salesmaster')->result_array();
			$color_index=0;
			foreach ($staff_departments as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
					'name' 		=> $value['company_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['total_sale'],
					'z' 		=> 100,
					'label' 		=> "Amt"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['company_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['total_sale'],
					'z' 		=> 100,
					'label' 		=> "Amt"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		public function top_five_customer()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = date('Y').'-'.date('m').'-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = [
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			$this->db->select(db_prefix().'history.AccountID, SUM(NetChallanAmt) as total_sale,'.db_prefix().'clients.company as company_name');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $minvalue);
			$this->db->where('tblhistory.TransDate <=', $maxvalue);
			$this->db->where('tblhistory.TType', 'O');
			$this->db->where('tblhistory.TType2', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.AccountID');
			$this->db->order_by("total_sale", "DESC");
			$this->db->limit(5);
			$staff_departments = $this->db->get(db_prefix().'history')->result_array();
			$color_index=0;
			foreach ($staff_departments as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
					'name' 		=> $value['company_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['total_sale'],
					'z' 		=> 100,
					'label' 		=> "Amt"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['company_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['total_sale'],
					'z' 		=> 100,
					'label' 		=> "Amt"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		/**
			* Get Top Five SKUS
			* @return [type] 
		*/
		public function top_five_skus()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = date('Y').'-'.date('m').'-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = ['#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70' ,
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			$this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items.description as description_name,tblhistory.ItemID');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->group_by('tblhistory.ItemID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $minvalue);
			$this->db->where('tblhistory.TransDate <=', $maxvalue);
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.ItemID');
			$this->db->order_by("total_qty", "DESC");
			$this->db->limit(5);
			$staff_departments = $this->db->get(db_prefix().'history')->result_array();
			$color_index=0;
			foreach ($staff_departments as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
					'name' 		=> $value['description_name'],
					'ItemID' 		=> $value['ItemID'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['total_qty'],
					'z' 		=> 100,
					'label' 		=> "Qty"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['description_name'],
					'ItemID' 	=> $value['ItemID'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['total_qty'],
					'z' 		=> 100,
					'label' 		=> "Qty"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		public function totalMonthlyPurchase()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = date('Y').'-'.date('m').'-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = ['#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70' ,
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			$sql = "SELECT 
			MonthList.Month,
			COALESCE(SUM(tblpurchasemaster.Invamt), 0) AS PurchAmt
			FROM 
			(
			SELECT 'April-20{$fy}' AS Month UNION ALL
            SELECT 'May-20{$fy}' UNION ALL
            SELECT 'June-20{$fy}' UNION ALL
            SELECT 'July-20{$fy}' UNION ALL
            SELECT 'August-20{$fy}' UNION ALL
            SELECT 'September-20{$fy}' UNION ALL
            SELECT 'October-20{$fy}' UNION ALL
            SELECT 'November-20{$fy}' UNION ALL
            SELECT 'December-20{$fy}' UNION ALL
            SELECT 'January-20" . ($fy + 1) . "' UNION ALL
            SELECT 'February-20" . ($fy + 1) . "' UNION ALL
            SELECT 'March-20" . ($fy + 1) . "'
			) AS MonthList
			LEFT JOIN 
			`tblpurchasemaster` 
			ON 
			DATE_FORMAT(tblpurchasemaster.Transdate, '%M-%Y') = MonthList.Month
			AND 
			tblpurchasemaster.cur_status = 'Completed' AND 
			tblpurchasemaster.FY = '".$fy."'
			GROUP BY 
			MonthList.Month
			ORDER BY 
			STR_TO_DATE(MonthList.Month, '%M-%Y') ASC;";
			$result = $this->db->query($sql)->result_array();
			$color_index=0;
			foreach ($result as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
					'name' 		=> $value['Month'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['PurchAmt'],
					'z' 		=> 100,
					'label' 		=> "Amount"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['Month'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['PurchAmt'],
					'z' 		=> 100,
					'label' 		=> "Amount"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		public function totalMonthlySale()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = date('Y').'-'.date('m').'-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = ['#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70' ,
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			$sql = "SELECT 
			MonthList.Month,
			COALESCE(SUM(tblsalesmaster.RndAmt), 0) AS SaleAmt
			FROM 
			(
			SELECT 'April-20{$fy}' AS Month UNION ALL
            SELECT 'May-20{$fy}' UNION ALL
            SELECT 'June-20{$fy}' UNION ALL
            SELECT 'July-20{$fy}' UNION ALL
            SELECT 'August-20{$fy}' UNION ALL
            SELECT 'September-20{$fy}' UNION ALL
            SELECT 'October-20{$fy}' UNION ALL
            SELECT 'November-20{$fy}' UNION ALL
            SELECT 'December-20{$fy}' UNION ALL
            SELECT 'January-20" . ($fy + 1) . "' UNION ALL
            SELECT 'February-20" . ($fy + 1) . "' UNION ALL
            SELECT 'March-20" . ($fy + 1) . "'
			) AS MonthList
			LEFT JOIN 
			`tblsalesmaster` 
			ON 
			DATE_FORMAT(tblsalesmaster.Transdate, '%M-%Y') = MonthList.Month
			AND 
			tblsalesmaster.FY = '".$fy."'
			GROUP BY 
			MonthList.Month
			ORDER BY 
			STR_TO_DATE(MonthList.Month, '%M-%Y') ASC;";
			$result = $this->db->query($sql)->result_array();
			$color_index=0;
			foreach ($result as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
					'name' 		=> $value['Month'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['SaleAmt'],
					'z' 		=> 100,
					'label' 		=> "Amount"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['Month'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['SaleAmt'],
					'z' 		=> 100,
					'label' 		=> "Amount"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		/**
			* report by staffs
			* @return [type] 
		*/
		public function report_by_staffs()
		{
			$months_report = $this->input->post('months_report');
			$custom_date_select = '';
			$current_year = date('Y');
			for($_month = 1 ; $_month <= 12; $_month++){
				$month_t = date('m',mktime(0, 0, 0, $_month, 04, 2016));
				if($_month == 5){
					$chart['categories'][] = _l('month_05');
					}else{
					$chart['categories'][] = _l('month_'.$_month);
				}
				$month = $current_year.'-'.$month_t;
				$chart['hr_new_staff'][] = $this->new_staff_by_month($month);
				$chart['hr_staff_are_working'][] = $this->staff_working_by_month($month);
				$chart['hr_staff_quit'][] = $this->staff_quit_work_by_month($month);
			}
			return $chart;
		}
		/**
			* new staff by month
			* @param  [type] $from 
			* @param  [type] $to   
			* @return [type]       
		*/
		public function new_staff_by_month($month)
		{
			$this->db->select('count(staffid) as total_staff');
			$sql_where = "date_format(datecreated, '%Y-%m') = '".$month."'";
			$this->db->where($sql_where);
			$result = $this->db->get(db_prefix().'staff')->row();
			if($result){
				return (int)$result->total_staff;
			}
			return 0;
		}
		/**
			* staff working by_month
			* @param  [type] $from 
			* @param  [type] $to   
			* @return [type]       
		*/
		public function staff_working_by_month($month)
		{
			$this->db->select('count(staffid) as total_staff');
			$sql_where = "status_work = 'working' AND date_format(datecreated, '%Y-%m') < '".$month."'";
			$this->db->where($sql_where);
			$result = $this->db->get(db_prefix().'staff')->row();
			if($result){
				return (int)$result->total_staff;
			}
			return 0;
		}
		/**
			* staff quit work by month
			* @param  [type] $month 
			* @return [type]        
		*/
		public function staff_quit_work_by_month($month)
		{	
			$this->db->select('count(staffid) as total_staff');
			$sql_where = 'staffid in (SELECT staffid FROM '.db_prefix().'hr_list_staff_quitting_work where date_format(dateoff, "%Y-%m") <= '.$month.') OR (status_work = "inactivity" AND date_format(date_update, "%Y-%m") = "'.$month.'")';
			$this->db->where($sql_where);
			$result = $this->db->get(db_prefix().'staff')->row();
			if($result){
				return (int)$result->total_staff;
			}
			return 0;
		}
		/**
			* get contracttype
			* @param  boolean $id 
			* @return [type]      
		*/
		public function get_contracttype($id = false){
			if (is_numeric($id)) {
				$this->db->where('id_contracttype', $id);
				return $this->db->get(db_prefix() . 'hr_staff_contract_type')->row();
			}
			if ($id == false) {
				return $this->db->query('select * from '.db_prefix().'hr_staff_contract_type order by id_contracttype desc')->result_array();
			}
		}
		/**
			* get data departmentchart
			* @return array 
		*/
		public function get_data_departmentchart(){        
			$department =  $this->db->query('select  departmentid as id, parent_id as pid, name, manager_id
			from '.db_prefix().'departments as d order by d.parent_id, d.departmentid')->result_array();
			$dep_tree = [];
			foreach ($department as $dep) {
				if($dep['pid']==0){
					$job_pst = hr_profile_job_position_by_staff($dep['manager_id']);
					if($dep['manager_id']){
						$job_pst = 'Acting';
					}
					array_push($dep_tree, 
					[
					'id' => $dep['id'],
					'name' =>$dep['name'],
					'title'    =>get_staff_full_name($dep['manager_id']),
					'image' => staff_profile_image($dep['manager_id'], [
					'staff-profile-image-small staff-chart-padding',
					]),
					'children'=>$this->get_child_node_chart($dep['id'], $department),
					'reality_now' => _l('hr_current_personnel').': '.$this->count_reality_now($dep['id']),
					'dp_user_icon' => '"fa fa-user-o"',
					'job_position' => $job_pst,
					]
					);
					} else {
					break;
				}            
			}  
			return $dep_tree;
		}
		/**
			* get child node chart
			* @param  integer $id      
			* @param  integer $arr_dep 
			* @return array          
		*/
		private function get_child_node_chart($id, $arr_dep){
			$dep_tree = array();
			foreach ($arr_dep as $dep) {
				if($dep['pid']==$id){   
					$node = array();  
					$node['id'] = $dep['id'];           
					$node['name'] = $dep['name'];
					$node['title'] = get_staff_full_name($dep['manager_id']);
					$node['image'] = staff_profile_image($dep['manager_id'], [
					'staff-profile-image-small staff-chart-padding',
					]);
					$node['dp_user_icon'] = '"fa fa-user-o"';
					$node['job_position'] = hr_profile_job_position_by_staff($dep['manager_id']);
					if($dep['manager_id'] == 49 || $dep['manager_id'] == '49'){
						$node['job_position'] = 'Acting';
					}
					$node['children'] = $this->get_child_node_chart($dep['id'], $arr_dep);
					$node['reality_now'] = _l('hr_current_personnel').': '.$this->count_reality_now($dep['id']);
					if(count($node['children'])==0){
						unset($node['children']);
					}
					$dep_tree[] = $node;
				} 
			} 
			return $dep_tree;
		}
		/**
			* count reality now
			* @param  integer $department 
			* @return integer             
		*/
		public function count_reality_now($department){
			$staff_dpm = $this->db->query('select '.db_prefix().'staff_departments.staffid from '.db_prefix().'staff_departments Left join '.db_prefix().'staff ON '.db_prefix().'staff.staffid = '.db_prefix().'staff_departments.staffid where '.db_prefix().'staff_departments.departmentid = '.$department.' and '.db_prefix().'staff.status_work != "inactivity"')->result_array();
			return count($staff_dpm);
		}
		/**
			* get data chart
			* @return array 
		*/
		public function get_data_chart()
		{
			$department =  $this->db->query('select s.staffid as id, s.team_manage as pid, s.firstname as name,  r.name as rname, j.position_name as job_position_name
			from tblstaff as s left join tblroles as r on s.role = r.roleid left join '.db_prefix().'hr_job_position as j on j.position_id = s.job_position where s.status_work != "inactivity"       
			order by s.team_manage, s.staffid')->result_array();
			$dep_tree = array();
			foreach ($department as $dep) {
				if($dep['pid'] == 0){
					$dpm = $this->getdepartment_name($dep['id']);
					$node = array();
					$node['name'] = $dep['name'];
					$node['job_position_name'] = '';
					if($dep['job_position_name'] != null && $dep['job_position_name'] != 'undefined'){
						$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
						$node['job_position_name'] = $dep['job_position_name'];
					}
					if($dep['rname'] != null){
						$node['title'] = $dep['rname'];
						$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
						}else{
						$node['title'] = '';
					}
					if($dpm->name != null){
						$node['departmentname'] = $dpm->name;
						$node['dp_icon'] = '"fa fa-sitemap"';
						}else{
						$node['departmentname'] = '';
					}
					$node['image'] = staff_profile_image($dep['id'], [
					'staff-profile-image-small staff-chart-padding',
					]);
					$node['children'] = $this->get_child_node_staff_chart($dep['id'], $department);
					$dep_tree[] = $node;
					} else {
					break;
				}            
			}   
			return $dep_tree;
		}
		/**
			* get department tree
			* @return array 
		*/
		public function get_department_tree(){
			$department =  $this->db->query('select  departmentid as id, parent_id as pid, name from '.db_prefix().'departments as d order by d.parent_id, d.departmentid')->result_array();
			$dep_tree = array();
			foreach ($department as $dep) {
				if($dep['pid']==0){
					$node = array();
					$node['id'] = $dep['id'];
					$node['title'] = $dep['name'];
					$node['subs'] = $this->get_child_node($dep['id'], $department);
					$dep_tree[] = $node;
					} else {
					break;
				}            
			}     
			return $dep_tree;
		}
		/**
			* Get child node of department tree
			* @param  $id      current department id
			* @param  $arr_dep department array
			* @return current department tree
		*/
		private function get_child_node($id, $arr_dep){
			$dep_tree = array();
			foreach ($arr_dep as $dep) {
				if($dep['pid']==$id){   
					$node = array();             
					$node['id'] = $dep['id'];
					$node['title'] = $dep['name'];
					$node['subs'] = $this->get_child_node($dep['id'], $arr_dep);
					if(count($node['subs'])==0){
						unset($node['subs']);
					}
					$dep_tree[] = $node;
				} 
			} 
			return $dep_tree;
		}
		/**
			* get department name
			* @param  integer $departmentid 
			* @return object               
		*/
		public function hr_profile_get_department_name($departmentid){
			return $this->db->query('select '.db_prefix().'departments.name from tbldepartments where departmentid = '.$departmentid)->row();
		}
		/**
			* get all staff not in record
			* @return array object
		*/
		public function get_all_staff_not_in_record(){
			return $this->db->query('select * from '.db_prefix().'staff where staffid not in (select staffid from '.db_prefix().'hr_rec_transfer_records)')->result_array();
		}
		/**
			* get setting transfer records
			* @return array 
		*/
		public function get_setting_transfer_records(){
			return $this->db->get(db_prefix().'setting_transfer_records')->result_array();
		}
		/**
			* get_staff_tree
			* @return array 
		*/
		public function get_staff_tree(){
			$department =  $this->db->query('select s.staffid as id, s.team_manage as pid, s.firstname as name
			from tblstaff as s         
			order by s.team_manage, s.staffid')->result_array();
			$dep_tree = array();
			foreach ($department as $dep) {
				if($dep['pid'] == 0){
					$node = array();
					$node['id'] = $dep['id'];
					$node['title'] = $dep['name'];
					$node['subs'] = $this->get_child_node_staff($dep['id'], $department);
					$dep_tree[] = $node;
					} else {
					break;
				}            
			}     
			return $dep_tree;
		}
		/**
			* Get child node of department tree
			* @param  $id      current department id
			* @param  $arr_dep department array
			* @return current department tree
		*/
		private function get_child_node_staff($id, $arr_dep){
			$dep_tree = array();
			foreach ($arr_dep as $dep) {
				if($dep['pid']==$id){   
					$node = array();             
					$node['id'] = $dep['id'];
					$node['title'] = $dep['name'];
					$node['subs'] = $this->get_child_node_staff($dep['id'], $arr_dep);
					if(count($node['subs']) == 0){
						unset($node['subs']);
					}
					$dep_tree[] = $node;
				} 
			} 
			return $dep_tree;
		}
		/**
			* get all jp interview training
			* @return object 
		*/
		public function get_all_jp_interview_training(){
			return $this->db->query('select * from '.db_prefix().'hr_jp_interview_training')->row();
		}
		/**
			* get setting asset allocation
			* @return array 
		*/
		public function get_setting_asset_allocation(){
			return $this->db->get(db_prefix().'setting_asset_allocation')->result_array();
		}
		/**
			* get list record meta
			* @return array 
		*/
		public function get_list_record_meta(){
			return $this->db->get(db_prefix().'records_meta')->result_array();
		}
		/**
			* add setting transfer records
		*/
		public function add_setting_transfer_records($data_transfer_meta){
			$this->db->empty_table(db_prefix() . 'setting_transfer_records');
			$list_meta = $this->get_list_record_meta();
			foreach ($data_transfer_meta['meta'] as $key => $value) {
				if($value != ''){
					$name='';
					foreach ($list_meta as $list_item) {
						if($list_item['meta']==$value){
							$name=$list_item['name'];
						}
					}
					$this->db->insert(db_prefix().'setting_transfer_records', [
					'name' => $name,
					'meta' => $value
					]);
				}
			}  
		}
		/**
			* add setting asset allocation
			* @param array $data_asset_name 
		*/
		public function add_setting_asset_allocation($data_asset_name){
			$this->db->empty_table(db_prefix() . 'setting_asset_allocation');       
			foreach ($data_asset_name['name'] as $key => $value) {  
				if($value != ''){
					$this->db->insert(db_prefix().'setting_asset_allocation', [
					'name' => $value,
					'meta' => ''
					]);
				}              
			}
		} 
		/**
			* add rec transfer records
			* @param array $data_asset_name 
		*/
		public function add_rec_transfer_records($data){     
			$this->db->insert(db_prefix().'hr_rec_transfer_records', [
			'staffid' => $data['staffid'],
			'creator' => get_staff_user_id(),
			'firstname' => $data['firstname'],
			'birthday' => $data['birthday'],
			'staff_identifi' => $data['staffidentifi']
			]);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				return $insert_id;
			}
			return false;
		}
		/**
			* group checklist
			* @return array 
		*/
		public function group_checklist(){
			return $this->db->get(db_prefix().'group_checklist')->result_array();
		}
		/**
			* get setting training 
			* @return object
		*/
		public function get_setting_training(){
			return $this->db->get(db_prefix().'setting_training')->row();
		}
		//========================== Get Job Designation ===============================
		public function get_job_position($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('position_id', $id);
				return $this->db->get(db_prefix() . 'hr_job_position')->row();
			}
			if ($id == false) {
				return $this->db->query('select * from '.db_prefix().'hr_job_position')->result_array();
			}
		}
		//========================== Get Staff Type ====================================
		public function GetStaffType($id = false)
		{
			$this->db->select(db_prefix().'AccountSubGroup2.*');
			$this->db->where(db_prefix().'AccountSubGroup2.IsStaff',"Y");
			$StaffType = $this->db->get(db_prefix().'AccountSubGroup2')->result_array();
			return $StaffType;
		}
		public function get_staff_type($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('roleid', $id);
				return $this->db->get(db_prefix() . 'roles')->row();
			}
			if ($id == false) {
				return $this->db->query('select * from '.db_prefix().'roles')->result_array();
			}
		}
		/**
			* get job position
			* @param  integer $id 
			* @return array or object     
		*/
		public function get_staff_hierarchy($id = false)
		{
		    $selected_company = $this->session->userdata('root_company');
            $this->db->where('PlantID', $selected_company);
			$this->db->where('staff_id', $id);
			return $this->db->get(db_prefix() . 'staff_mapping')->result_array();
		}
		/**
			* get allowance type
			* @param  integer $id 
			* @return array or object      
		*/
		public function get_allowance_type($id = false){
			if (is_numeric($id)) {
				$this->db->where('type_id', $id);
				return $this->db->get(db_prefix() . 'hr_allowance_type')->row();
			}
			if ($id == false) {
				return  $this->db->get(db_prefix() . 'hr_allowance_type')->result_array();
			}
		}
		/**
			* get salary form
			* @param  integer $id 
			* @return array or object
		*/
		public function get_salary_form($id = false){
			if (is_numeric($id)) {
				$this->db->where('form_id', $id);
				return $this->db->get(db_prefix() . 'hr_salary_form')->row();
			}
			if ($id == false) {
				return $this->db->query('select * from '.db_prefix().'hr_salary_form order by form_id desc')->result_array();
			}
		}
		/**
			* get procedure retire
			* @param  integer $id 
			* @return array     
		*/
		public function get_procedure_retire($id = ''){
			if($id == ''){
				return $this->db->get(db_prefix().'hr_procedure_retire')->result_array();
				}else{
				$this->db->where('procedure_retire_id', $id);
				return $this->db->get(db_prefix().'hr_procedure_retire')->result_array();
			}
		}
		/**
			* get allowance type tax
			* @param  integer $id 
		*/
		public function get_allowance_type_tax($id = false){
			$this->db->where('taxable', "1");
			return  $this->db->get(db_prefix() . 'hr_allowance_type')->result_array();
		}
		/**
			* add contract type
			* @param array $data 
		*/
		public function add_contract_type($data){
			$this->db->insert(db_prefix() . 'hr_staff_contract_type', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		/**
			* delete contract type
			* @param  integer $id 
		*/
		public function delete_contract_type($id){
			$this->db->where('id_contracttype', $id);
			$this->db->delete(db_prefix() . 'hr_staff_contract_type');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* add allowance type
			* @param array $data 
		*/
		public function add_allowance_type($data){
			$data['allowance_val'] = hr_profile_reformat_currency($data['allowance_val']);
			$this->db->insert(db_prefix() . 'hr_allowance_type', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		/**
			* update allowance type
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function update_allowance_type($data, $id)
		{   
			$data['allowance_val'] = hr_profile_reformat_currency($data['allowance_val']);
			$this->db->where('type_id', $id);
			$this->db->update(db_prefix() . 'hr_allowance_type', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* update contract type
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function update_contract_type($data, $id)
		{   
			$this->db->where('id_contracttype', $id);
			$this->db->update(db_prefix() . 'hr_staff_contract_type', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete allowance type
			* @param  integer $id 
			* @return boolean     
		*/
		public function delete_allowance_type($id){
			$this->db->where('type_id', $id);
			$this->db->delete(db_prefix() . 'hr_allowance_type');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* add salary form
			* @param array $data 
		*/
		public function add_salary_form($data){
			$data['salary_val'] = hr_profile_reformat_currency($data['salary_val']);
			$this->db->insert(db_prefix() . 'hr_salary_form', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		/**
			* update salary form
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function update_salary_form($data, $id)
		{   
			$data['salary_val'] = hr_profile_reformat_currency($data['salary_val']);
			$this->db->where('form_id', $id);
			$this->db->update(db_prefix() . 'hr_salary_form', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete salary form
			* @param  integer $id 
			* @return boolean     
		*/
		public function delete_salary_form($id){
			$this->db->where('form_id', $id);
			$this->db->delete(db_prefix() . 'hr_salary_form');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* add procedure form manage
			* @param array $data 
		*/
		public function add_procedure_form_manage($data)
		{
			if(isset($data['departmentid'])){
				$data['department'] = json_encode($data['departmentid']);
				unset($data['departmentid']);
			}
			$data['datecreator'] = date('Y-m-d H:i:s');
			$this->db->insert(db_prefix().'hr_procedure_retire_manage',$data);
			$insert_id = $this->db->insert_id();
			if($insert_id){
				return $insert_id;
			}
			return false;
		}
		/**
			* update procedure form manage
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function update_procedure_form_manage($data,$id)
		{
			if(isset($data['departmentid'])){
				$data['department'] = json_encode($data['departmentid']);
				unset($data['departmentid']);
			}
			if(isset($data['name_procedure_retire_edit'])){
				$data['name_procedure_retire'] = $data['name_procedure_retire_edit'];
				unset($data['name_procedure_retire_edit']);
			}
			$data['datecreator'] = date('Y-m-d H:i:s');
			$this->db->where('id',$id);
			$this->db->update(db_prefix().'hr_procedure_retire_manage',$data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* get procedure form manage
			* @param  integer $id 
			* @return array or object     
		*/
		public function get_procedure_form_manage($id = ''){
			if ($id != '') {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'hr_procedure_retire_manage')->row();
			}
			if ($id == '') {
				return  $this->db->get(db_prefix() . 'hr_procedure_retire_manage')->result_array();
			}
		}
		/**
			* delete procedure form manage
			* @param  integer $id 
			* @return boolean     
		*/
		public function delete_procedure_form_manage($id){
			$affected_rows = 0;
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'hr_procedure_retire_manage');
			if ($this->db->affected_rows() > 0) {
				$affected_rows++;
			}
			$this->db->where('procedure_retire_id', $id);
			$this->db->delete(db_prefix() . 'hr_procedure_retire');
			if ($this->db->affected_rows() > 0) {
				$affected_rows++;
			}
			if ($affected_rows > 0) {
				return true;
			}
			return false;
		}
		/**
			* check department on procedure
			* @param  integer $departmentid 
			* @return array               
		*/
		public function check_department_on_procedure($departmentid)
		{
			$data = $this->get_procedure_form_manage();
			$data_val = [];
			foreach ($data as $key => $value) {
				$departments = json_decode($value['department'], true);
				if(in_array((int)$departmentid,$departments)){
					return $data_val[] = $value['id'];
				}
			}
			return $data_val;
		}
		/**
			* add procedure retire
			* @param array $data 
		*/
		public function add_procedure_retire($data){
			$data['option_name'] = json_encode($data['option_name'][1]);
			$data['rel_name'] = implode($data['rel_name']);
			$this->db->insert(db_prefix().'hr_procedure_retire', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		/**
			* delete procedure retire
			* @param  integer $id 
			* @return boolean     
		*/
		public function delete_procedure_retire($id){
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'hr_procedure_retire');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* get edit procedure retire
			* @param  integer $id 
			* @return object     
		*/
		public function get_edit_procedure_retire($id){
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'hr_procedure_retire')->row();
		}
		/**
			* edit procedure retire
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function edit_procedure_retire($data, $id){
			$data['option_name'] = json_encode($data['option_name'][1]);
			$data['rel_name'] = implode($data['rel_name']);
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'hr_procedure_retire', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		public function get_job_position_training_process($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('job_position_id', $id);
				return  $this->db->get(db_prefix() . 'hr_jp_interview_training')->result_array();
			}
			if ($id == false) {
				return  $this->db->get(db_prefix() . 'hr_jp_interview_training')->result_array();
			} 
		}
		/**
			* get job position interview process
			* @param  integer $id 
			* @return array or object      
		*/
		public function get_job_position_interview_process($id = false){
			if(is_numeric($id)){
				$this->db->where('interview_process_id', $id);
				return  $this->db->get(db_prefix() . 'hr_jp_interview_training')->row();
			}
			if($id == false){
				return  $this->db->get(db_prefix() . 'hr_jp_interview_training')->result_array();
			}
		}
		/**
			* add position training
			* @param [type] $data 
		*/
		public function add_position_training($data)
		{
			if (isset($data['disabled'])) {
				$data['active'] = 0;
				unset($data['disabled']);
				} else {
				$data['active'] = 1;
			}
			if (isset($data['iprestrict'])) {
				$data['iprestrict'] = 1;
				} else {
				$data['iprestrict'] = 0;
			}
			if (isset($data['onlyforloggedin'])) {
				$data['onlyforloggedin'] = 1;
				} else {
				$data['onlyforloggedin'] = 0;
			}
			$datecreated = date('Y-m-d H:i:s');
			$this->db->insert(db_prefix().'hr_position_training', [
			'subject'         => $data['subject'],
			'training_type'   => $data['training_type'],
			'slug'            => slug_it($data['subject']),
			'description'     => $data['description'],
			'viewdescription' => $data['viewdescription'],
			'datecreated'     => $datecreated,
			'active'          => $data['active'],
			'onlyforloggedin' => $data['onlyforloggedin'],
			'iprestrict'      => $data['iprestrict'],
			'hash'            => md5($datecreated),
			'fromname'        => $data['fromname'],
			]);
			$trainingid = $this->db->insert_id();
			if (!$trainingid) {
				// return false;
			}
			log_activity('New Training Type Added [ID: ' . $trainingid . ', Subject: ' . $data['subject'] . ']');
			return $trainingid;
		}
		/**
			* update position training
			* @param  [type] $data        
			* @param  [type] $training_id 
			* @return [type]              
		*/
		public function update_position_training($data, $training_id)
		{
			if (isset($data['disabled'])) {
				$data['active'] = 0;
				unset($data['disabled']);
				} else {
				$data['active'] = 1;
			}
			if (isset($data['onlyforloggedin'])) {
				$data['onlyforloggedin'] = 1;
				} else {
				$data['onlyforloggedin'] = 0;
			}
			if (isset($data['iprestrict'])) {
				$data['iprestrict'] = 1;
				} else {
				$data['iprestrict'] = 0;
			}
			$this->db->where('training_id', $training_id);
			$this->db->update(db_prefix().'hr_position_training', [
			'subject'         => $data['subject'],
			'training_type'   => $data['training_type'],
			'slug'            => slug_it($data['subject']),
			'description'     => $data['description'],
			'viewdescription' => $data['viewdescription'],
			'iprestrict'      => $data['iprestrict'],
			'active'          => $data['active'],
			'onlyforloggedin' => $data['onlyforloggedin'],
			'fromname'        => $data['fromname'],
			]);
			if ($this->db->affected_rows() > 0) {
				log_activity('Training Updated [ID: ' . $training_id . ', Subject: ' . $data['subject'] . ']');
				return true;
			}
			return false;
		}
		/**
			* get position training
			* @param  integer $id 
			* @return array     
		*/
		public function get_position_training($id = '')
		{
			$this->db->where('training_id', $id);
			$position_training = $this->db->get(db_prefix().'hr_position_training')->row();
			if (!$position_training) {
				return false;
			}
			$this->db->where('rel_id', $position_training->training_id);
			$this->db->where('rel_type', 'position_training');
			$this->db->order_by('question_order', 'asc');
			$questions = $this->db->get(db_prefix().'hr_position_training_question_form')->result_array();
			$i         = 0;
			foreach ($questions as $question) {
				$this->db->where('questionid', $question['questionid']);
				$box                      = $this->db->get(db_prefix().'hr_p_t_form_question_box')->row();
				$questions[$i]['boxid']   = $box->boxid;
				$questions[$i]['boxtype'] = $box->boxtype;
				if ($box->boxtype == 'checkbox' || $box->boxtype == 'radio') {
					$this->db->order_by('questionboxdescriptionid', 'asc');
					$this->db->where('boxid', $box->boxid);
					$boxes_description = $this->db->get(db_prefix().'hr_p_t_form_question_box_description')->result_array();
					if (count($boxes_description) > 0) {
						$questions[$i]['box_descriptions'] = [];
						foreach ($boxes_description as $box_description) {
							$questions[$i]['box_descriptions'][] = $box_description;
						}
					}
				}
				$i++;
			}
			$position_training->questions = $questions;
			return $position_training;
		}
		/**
			* add training question
			* @param [type] $data 
		*/
		public function add_training_question($data)
		{
			$questionid = $this->insert_training_question($data['training_id']);
			if ($questionid) {
				$boxid    = $this->insert_question_type($data['type'], $questionid);
				$response = [
				'questionid' => $questionid,
				'boxid'      => $boxid,
				];
				if ($data['type'] == 'checkbox' or $data['type'] == 'radio') {
					$questionboxdescriptionid = $this->add_box_description($questionid, $boxid);
					array_push($response, [
					'questionboxdescriptionid' => $questionboxdescriptionid,
					]);
				}
				return $response;
			}
			return false;
		}
		/**
			* insert training question
			* @param  [type] $training_id 
			* @param  string $question    
			* @return [type]              
		*/
		private function insert_training_question($training_id, $question = '')
		{
			$this->db->insert(db_prefix().'hr_position_training_question_form', [
			'rel_id'   => $training_id,
			'rel_type' => 'position_training',
			'question' => $question,
			]);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				log_activity('New Training Question Added [TrainingID: ' . $training_id . ']');
			}
			return $insert_id;
		}
		/**
			* Add new question type
			* @param  string $type       checkbox/textarea/radio/input
			* @param  mixed $questionid question id
			* @return mixed
		*/
		private function insert_question_type($type, $questionid)
		{
			$this->db->insert(db_prefix().'hr_p_t_form_question_box', [
			'boxtype'    => $type,
			'questionid' => $questionid,
			]);
			return $this->db->insert_id();
		}
		/**
			* update question
			* @param  array $data 
			* @return boolean        
		*/
		public function update_question($data)
		{
			$_required = 1;
			if ($data['question']['required'] == 'false') {
				$_required = 0;
			}
			$affectedRows = 0;
			$this->db->where('questionid', $data['questionid']);
			$this->db->update(db_prefix().'hr_position_training_question_form', [
			'question' => $data['question']['value'],
			'required' => $_required,
			'point' => $data['question']['point'],
			]);
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			if (isset($data['boxes_description'])) {
				foreach ($data['boxes_description'] as $box_description) {
					$this->db->where('questionboxdescriptionid', $box_description[0]);
					$this->db->update(db_prefix().'hr_p_t_form_question_box_description', [
					'description' => $box_description[1],
					]);
					if ($this->db->affected_rows() > 0) {
						$affectedRows++;
					}
				}
			}
			if ($affectedRows > 0) {
				log_activity('Training Question Updated [QuestionID: ' . $data['questionid'] . ']');
				return true;
			}
			return false;
		}
		/**
			* update survey questions orders
			* @param  array $data 
		*/
		public function update_survey_questions_orders($data)
		{
			foreach ($data['data'] as $question) {
				$this->db->where('questionid', $question[0]);
				$this->db->update(db_prefix().'hr_position_training_question_form', [
				'question_order' => $question[1],
				]);
			}
		}
		/**
			* remove question
			* @param  integer $questionid 
			* @return boolean             
		*/
		public function remove_question($questionid)
		{
			$affectedRows = 0;
			$this->db->where('questionid', $questionid);
			$this->db->delete(db_prefix().'hr_p_t_form_question_box_description');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			$this->db->where('questionid', $questionid);
			$this->db->delete(db_prefix().'hr_p_t_form_question_box');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			$this->db->where('questionid', $questionid);
			$this->db->delete(db_prefix().'hr_position_training_question_form');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			if ($affectedRows > 0) {
				log_activity('Training Question Deleted [' . $questionid . ']');
				return true;
			}
			return false;
		}
		/**
			* remove box description
			* @param  integer $questionbod 
			* @return boolean                           
		*/
		public function remove_box_description($questionboxdescriptionid)
		{
			$this->db->where('questionboxdescriptionid', $questionboxdescriptionid);
			$this->db->delete(db_prefix().'hr_p_t_form_question_box_description');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* add box description
			* @param integer $questionid  
			* @param integer $boxid       
			* @param string $description
			* @return  integer
		*/
		public function add_box_description($questionid, $boxid, $description = '')
		{
			$this->db->insert(db_prefix().'hr_p_t_form_question_box_description', [
			'questionid'  => $questionid,
			'boxid'       => $boxid,
			'description' => $description,
			]);
			return $this->db->insert_id();
		}
		/**
			* add training result
			* @param integer $id     
			* @param array $result 
		*/
		public function add_training_result($id, $result)
		{
			$this->db->insert(db_prefix().'hr_p_t_surveyresultsets', [
			'date'      => date('Y-m-d H:i:s'),
			'trainingid'  => $id,
			'ip'        => $this->input->ip_address(),
			'useragent' => substr($this->input->user_agent(), 0, 149),
			'staff_id'  => get_staff_user_id(),
			]);
			$resultsetid = $this->db->insert_id();
			if ($resultsetid) {
				if (isset($result['selectable']) && sizeof($result['selectable']) > 0) {
					foreach ($result['selectable'] as $boxid => $question_answers) {
						foreach ($question_answers as $questionid => $answer) {
							$count = count($answer);
							for ($i = 0; $i < $count; $i++) {
								$this->db->insert(db_prefix().'hr_p_t_form_results', [
								'boxid'            => $boxid,
								'boxdescriptionid' => $answer[$i],
								'rel_id'           => $id,
								'rel_type'         => 'position_training',
								'questionid'       => $questionid,
								'answer'      	   => $answer[$i],
								'resultsetid'      => $resultsetid,
								]);
							}
						}
					}
				}
				unset($result['selectable']);
				if (isset($result['question'])) {
					foreach ($result['question'] as $questionid => $val) {
						$boxid = $this->get_training_question_box_id($questionid);
						$this->db->insert(db_prefix().'hr_p_t_form_results', [
						'boxid'       => $boxid,
						'rel_id'      => $id,
						'rel_type'    => 'position_training',
						'questionid'  => $questionid,
						'answer'      => $val[0],
						'resultsetid' => $resultsetid,
						]);
					}
				}
				return true;
			}
			return false;
		}
		/**
			* get training question box id
			* @param  integer $questionid 
			* @return integer             
		*/
		private function get_training_question_box_id($questionid)
		{
			$this->db->select('boxid');
			$this->db->from(db_prefix().'hr_p_t_form_question_box');
			$this->db->where('questionid', $questionid);
			$box = $this->db->get()->row();
			return $box->boxid;
		}
		/**
			* update answer question
			* @param  array $data 
			* @return array       
		*/
		public function update_answer_question($data)
		{
			$this->db->where('questionboxdescriptionid', $data['questionboxdescriptionid']);
			$this->db->update(db_prefix().'hr_p_t_form_question_box_description', [
			'correct' => $data['correct'],
			]);
			if ($this->db->affected_rows() > 0) {
				log_activity('Training Question Updated [QuestionID: questionboxdescriptionid ' . $data['questionboxdescriptionid'] . ']');
				return true;
			}
			return false;
		}
		/**
			* get child training type
			* @param  integer $id 
			* @return array     
		*/
		public function get_child_training_type($id){
			$this->db->where('training_type',$id);
			$this->db->order_by('datecreated', 'desc');
			$rs = $this->db->get(db_prefix().'hr_position_training')->result_array();
			return  $rs;
		}
		/**
			* add job position training process
			* @param array $data 
		*/
		public function add_job_position_training_process($data){
			if(isset($data['department_id'])){
				unset($data['department_id']);
			}
			$data['date_add'] = date('Y-m-d H:i:s');
			$data['position_training_id'] = implode(',',$data['position_training_id']);
			$data['job_position_id'] = implode(',',$data['job_position_id']);
			$this->db->insert(db_prefix().'hr_jp_interview_training',$data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				return $insert_id;
			}
			return false;
		}
		/**
			* update job position training process
			* @param  array $data 
			* @param  integer $id   
			* @return integer or boolean       
		*/
		public function update_job_position_training_process($data, $id){
			if(isset($data['department_id'])){
				unset($data['department_id']);
			}
			$data['date_add'] = date('Y-m-d H:i:s');
			$data['position_training_id'] = implode(',',$data['position_training_id']);
			$data['job_position_id'] = implode(',',$data['job_position_id']);
			$this->db->where('training_process_id', $id);
			$this->db->update(db_prefix().'hr_jp_interview_training',$data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* get jobposition by department
			* @param integer $department_id 
			* @param  integer $status        
			* @return string                
		*/
		public function get_jobposition_by_department($department_id = '', $status)
		{
			$arr_staff_id=[];
			$index_dep = 0;
			if(is_array($department_id)){
				/*get staff in deaprtment start*/
				foreach ($department_id as $key => $value) {
					/*get staff in department*/
					$this->db->select('staffid');
					$this->db->where('departmentid', $value);
					$arr_staff = $this->db->get(db_prefix().'staff_departments')->result_array();
					if(count($arr_staff) > 0){
						foreach ($arr_staff as $value) {
							if(!in_array($value['staffid'], $arr_staff_id)){
								$arr_staff_id[$index_dep] = $value['staffid'];
								$index_dep++;
							}
						}
					}
				}
				/*get staff in deaprtment end*/
				$options = '';
				if(count($arr_staff_id) == 0){
					return $options;
				}
				/*get position start*/
				$arr_staff_id = implode(",", $arr_staff_id);
				$sql_where = 'SELECT '.db_prefix().'hr_job_position.position_id, position_name FROM '.db_prefix().'staff left join '.db_prefix().'hr_job_position on '.db_prefix().'staff.job_position = '.db_prefix().'hr_job_position.position_id WHERE '.db_prefix().'staff.job_position != "0" AND '.db_prefix().'staff.staffid IN ('.$arr_staff_id.')';
				$arr_job_position = $this->db->query($sql_where)->result_array();
				$arr_check_exist=[];
				foreach ($arr_job_position as $k => $note) {
					if(!in_array($note['position_id'], $arr_check_exist)){
						$select = ' selected';
						$options .= '<option value="' . $note['position_id'] . '" '.$select.'>' . $note['position_name'] . '</option>';
						$arr_check_exist[$k] = $note['position_id'];
					}
				}
				/*get position end*/
				return $options;
				}else{
				$arr_job_position = $this->get_job_position();
				$options = '';
				foreach ($arr_job_position as $note) {
					$options .= '<option value="' . $note['position_id'] . '">' . $note['position_name'] . '</option>';
				}
				return $options;
			}
		}
		/**
			* get job position
			* @param  integer $id 
			* @return object or array      
		*/
		public function get_job_p($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('job_id', $id);
				return $this->db->get(db_prefix() . 'hr_job_p')->row();
			}
			if ($id == false) {
				return $this->db->query('select * from tblhr_job_p')->result_array();
			}
		}
		/**
			* add job position
			* @param array $data 
		*/
		public function add_job_p($data)
		{
			$option = 'off';
			if(isset($data['create_job_position'])){
				$option = $data['create_job_position'];
				unset($data['create_job_position']);
			}
			$this->db->insert(db_prefix() . 'hr_job_p', $data);
			$insert_id = $this->db->insert_id();
			if($insert_id){
				if($option == 'on'){
					$data_position['position_name'] = $data['job_name'];
					$data_position['job_position_description'] = $data['description'];
					$data_position['job_p_id'] = $insert_id;
					$this->add_job_position($data_position);
				}
			}
			return $insert_id;
		}
		/**
			* update job position
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function update_job_p($data, $id)
		{ 
			$this->db->where('job_id', $id);
			$this->db->update(db_prefix() . 'hr_job_p', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return true;
		}
		/**
			* delete job position
			* @param  integer $id 
			* @return boolean     
		*/
		public function delete_job_p($id)
		{
			$this->db->where('job_id', $id);
			$this->db->delete(db_prefix() . 'hr_job_p');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* add job position
			* @param aray $data 
		*/
		public function add_job_position($data)
		{
			if(isset($data['file'])){
				$files = $data['file'];
				unset($data['file']);
			}
			if(isset($data['tags'])){
				$tags = $data['tags'];
				unset($data['tags']);
			}
			if(isset($data['order'])){
				$orders = $data['order'];
				unset($data['order']);
			}
			if(isset($data['interview_name'])){
				$interview_names = $data['interview_name'];
				unset($data['interview_name']);
			}
			if(isset($data['rec_evaluation_form_id'])){
				$rec_evaluation_form_ids = $data['rec_evaluation_form_id'];
				unset($data['rec_evaluation_form_id']);
			}
			if(isset($data['rec_evaluation_form_id'])){
				$rec_evaluation_form_ids = $data['rec_evaluation_form_id'];
				unset($data['rec_evaluation_form_id']);
			}
			if(isset($data['head_unit'])){
				$head_units = $data['head_unit'];
				unset($data['head_unit']);
			}
			if(isset($data['specific_people'])){
				$specific_peoples = $data['specific_people'];
				unset($data['specific_people']);
			}
			if(isset($data['description'])){
				$descriptions = $data['description'];
				unset($data['description']);
			}
			if(isset($data['training_process_order'])){
				$descriptions = $data['training_process_order'];
				unset($data['training_process_order']);
			}
			if(isset($data['training_process_id'])){
				$descriptions = $data['training_process_id'];
				unset($data['training_process_id']);
			}
			if(isset($data['department_id'])){
				$data['department_id'] = implode(',', $data['department_id']);
			}
			$this->db->insert(db_prefix() . 'hr_job_position', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				if(isset($tags)){
					handle_tags_save($tags, $insert_id, 'hr_job_position');
				}
				/*update next number setting*/
				$this->update_prefix_number(['job_position_number' =>  get_hr_profile_option('job_position_number')+1]);
			}
			return $insert_id;
		}
		/**
			* update job position
			* @param aray $data 
		*/
		public function update_job_position($data, $id)
		{   
			$affected_rows = 0;
			if(isset($data['file'])){
				$files = $data['file'];
				unset($data['file']);
			}
			if(isset($data['tags'])){
				$tags = $data['tags'];
				unset($data['tags']);
			}
			//delete tag
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'job_position');
			$arr_tag = $this->db->get(db_prefix() . 'taggables')->result_array();
			foreach ($arr_tag as $tag_key => $tag_value) {
				//delete tag item
				$this->db->where('id', $tag_value['tag_id']);
				$this->db->delete(db_prefix() . 'tags');
			}
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'job_position');
			$this->db->delete(db_prefix() . 'taggables');
			//insert tags
			$result_handle_tag = handle_tags_save($tags, $id, 'job_position');
			if($result_handle_tag){
				$affected_rows++;
			}
			if(isset($data['department_id'])){
				$data['department_id'] = implode(',', $data['department_id']);
				}else{
				$data['department_id'] = null;
			}
			$this->db->where('position_id', $id);
			$this->db->update(db_prefix() . 'hr_job_position', $data);
			if ($this->db->affected_rows() > 0) {
				$affected_rows++;
			}
			if($affected_rows > 0){
				return true;
			}
			return false;
		}
		/**
			* delete job position
			* @param aray $data 
		*/
		public function delete_job_position($id){
			//delete atachement file
			$job_position_file = $this->get_hr_profile_file($id, 'job_position');
			foreach ($job_position_file as $file_key => $file_value) {
				$this->delete_hr_job_position_attachment_file($file_value['id']);
			}
			//delete tags
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'job_position');
			$arr_tag = $this->db->get(db_prefix() . 'taggables')->result_array();
			foreach ($arr_tag as $tag_key => $tag_value) {
				//delete tag item
				$this->db->where('id', $tag_value['tag_id']);
				$this->db->delete(db_prefix() . 'tags');
			}
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'job_position');
			$this->db->delete(db_prefix() . 'taggables');
			//delete salary scale
			$this->db->where('job_position_id', $id);
			$this->db->delete(db_prefix() . 'hr_jp_salary_scale');
			//delete table job position
			$this->db->where('position_id', $id);
			$this->db->delete(db_prefix() . 'hr_job_position');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}
		/**
			* get list job position tags file
			* @param  [type] $job_position_id 
			* @return [type]                  
		*/
		public function get_list_job_position_tags_file($job_position_id)
		{
			$data=[];
			$arr_file = $this->get_hrm_attachments_file($job_position_id, 'job_position');
			/* get list tinymce start*/
			$this->db->from(db_prefix() . 'taggables');
			$this->db->join(db_prefix() . 'tags', db_prefix() . 'tags.id = ' . db_prefix() . 'taggables.tag_id', 'left');
			$this->db->where(db_prefix() . 'taggables.rel_id', $job_position_id);
			$this->db->where(db_prefix() . 'taggables.rel_type', 'job_position');
			$this->db->order_by('tag_order', 'ASC');
			$job_position_tags = $this->db->get()->result_array();
			$html_tags='';
			foreach ($job_position_tags as $tag_value) {
				$html_tags .='<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable tag-id-'.$tag_value['id'].' true" value="'.$tag_value['id'].'">
				<span class="tagit-label">'.$tag_value['name'].'</span>
				<a class="tagit-close">
				<span class="text-icon">×</span>
				<span class="ui-icon ui-icon-close"></span>
				</a>
				</li>';
			}
			$htmlfile='';
			//get file attachment html
			if(isset($arr_file)){
				$htmlfile = '<div class="row col-md-12" id="attachment_file">';
				foreach($arr_file as $attachment) {
					$href_url = site_url('modules/hrm/uploads/job_position/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
					if(!empty($attachment['external'])){
						$href_url = $attachment['external_link'];
					}
					$htmlfile .= '<div class="display-block contract-attachment-wrapper">';
					$htmlfile .= '<div class="col-md-10">';
					$htmlfile .= '<div class="col-md-1 mr-5">';
					$htmlfile .= '<a name="preview-btn" onclick="preview_file_job_position(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
					$htmlfile .= '<i class="fa fa-eye"></i>'; 
					$htmlfile .= '</a>';
					$htmlfile .= '</div>';
					$htmlfile .= '<div class=col-md-9>';
					$htmlfile .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
					$htmlfile .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
					$htmlfile .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
					$htmlfile .= '</div>';
					$htmlfile .= '</div>';
					$htmlfile .= '<div class="col-md-2 text-right">';
					if(has_permission('staffmanage_job_position', '', 'delete')){
						$htmlfile .= '<a href="#" class="text-danger" onclick="delete_job_position_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
					}
					$htmlfile .= '</div>';
					$htmlfile .= '<div class="clearfix"></div><hr/>';
					$htmlfile .= '</div>';
				}
				$htmlfile .= '</div>';
			}
			$data['htmltag']    = $html_tags;  
			$data['htmlfile']   = $htmlfile;  
			return $data;
		}
		/**
			* get hrm attachments file
			* @param  [type] $rel_id   
			* @param  [type] $rel_type 
			* @return [type]           
		*/
		public function get_hrm_attachments_file($rel_id, $rel_type){
			//contract : //rel_id = $id_contract, rel_type = 'hrm_contract'
			$this->db->order_by('dateadded', 'desc');
			$this->db->where('rel_id', $rel_id);
			$this->db->where('rel_type', $rel_type);
			return $this->db->get(db_prefix() . 'files')->result_array();
		}
		/**
			* get department from job p
			* @param  integer $job_p_id 
			* @return array           
		*/
		public function get_department_from_job_p($job_p_id)
		{   
			$data=[];
			$index=0;
			$this->db->where('job_p_id', $job_p_id);
			$job_position =  $this->db->get(db_prefix().'hr_job_position')->result_array();
			if(count($job_position) > 0){
				foreach ($job_position as $job_value) {
					if($job_value['department_id'] != null && $job_value['department_id'] != ''){
						$arr = explode(',', $job_value['department_id']);
						foreach ($arr as $arr_value) {
							if(!in_array($arr_value, $data)){
								$data[$index] = $arr_value;
								$index ++;
							}
						}
					}
				}
			}
			return $data;
		}
		/**
			* check child in job position
			* @param  integer $id 
			* @return boolean     
		*/
		public function check_child_in_job_p($id)
		{
			$this->db->where('job_p_id', $id);
			$arr_job_chil = $this->db->get(db_prefix() . 'hr_job_position')->result_array();
			foreach ($arr_job_chil as $key => $value) {
				if (is_reference_in_table('job_position', db_prefix() . 'staff', $value['position_id'])) {
					return true;;
				}
			}
			return false;
		}
		/**
			* get array job position
			* @param  integer $id 
			* @return boolean      
		*/
		public function get_array_job_position($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('job_p_id', $id);
				return $this->db->get(db_prefix() . 'hr_job_position')->result_array();
			}
			return false;
		}
		/**
			* get job position tag
			* @param  integer $id 
		*/
		public function get_job_position_tag($id=''){
			/* get list tinymce start*/
			$this->db->from(db_prefix() . 'taggables');
			$this->db->join(db_prefix() . 'tags', db_prefix() . 'tags.id = ' . db_prefix() . 'taggables.tag_id', 'left');
			$this->db->where(db_prefix() . 'taggables.rel_id', $id);
			$this->db->where(db_prefix() . 'taggables.rel_type', 'job_position');
			$this->db->order_by('tag_order', 'ASC');
			$job_position_tags = $this->db->get()->result_array();
			return $job_position_tags;
		}
		/**
			* get array interview process by position id
			* @param  integer $id
			* @return  array
		*/
		public function get_interview_process_byposition($id = false){
			if (is_numeric($id)) {
				$sql_where ='find_in_set("'.$id.'", job_position_id)';
				$this->db->where($sql_where);
				$this->db->order_by('interview_process_id', 'desc');
				return  $this->db->get(db_prefix() . 'jp_interview_process')->result_array();
			}
		}
		/**
			* get array training process by position id
			* @param  integer $id
			* @return  array
		*/
		public function get_traing_process_byposition($id = false){
			if (is_numeric($id)) {
				$sql_where ='find_in_set("'.$id.'", job_position_id)';
				$this->db->where($sql_where);
				$this->db->order_by('training_process_id', 'desc');
				return  $this->db->get(db_prefix() . 'hr_jp_interview_training')->result_array();
			}
		}
		/**
			* get job position salary scale
			* @param  integer $job_position_id 
			* @return array                  
		*/
		public function get_job_position_salary_scale($job_position_id){
			$data=[];
			$salary_insurance = 0;
			$salary_form = [];        
			$salary_allowance = [];   
			$this->db->where('job_position_id', $job_position_id);
			$arr_salary_sacale = $this->db->get(db_prefix() . 'hr_jp_salary_scale')->result_array();
			foreach ($arr_salary_sacale as $key => $value) {
				switch ($value['rel_type']) {
					case 'insurance':
					# code...
					$salary_insurance = $value['value'];
					break;
					case 'salary':
					# code...
					array_push($salary_form, $arr_salary_sacale[$key]);
					break;
					case 'allowance':
					# code...
					array_push($salary_allowance, $arr_salary_sacale[$key]);
					break;
				}
			}
			$data['insurance'] = $salary_insurance;
			$data['salary'] = $salary_form;
			$data['allowance'] = $salary_allowance;
			return $data;
		}
		/**
			* get hr profile attachments file
			* @param  integer $rel_id   
			* @param  integer $rel_type 
			* @return array           
		*/
		public function get_hr_profile_attachments_file($rel_id, $rel_type){        
			$this->db->order_by('dateadded', 'desc');
			$this->db->where('rel_id', $rel_id);
			$this->db->where('rel_type', $rel_type);
			return $this->db->get(db_prefix() . 'files')->result_array();
		}
		/**
			* get hr Travel Histry
			* @return array           
		*/
		public function get_travel_distance($id){        
			$this->db->order_by('travDate', 'desc');
			$this->db->where('staff_id', $id);
			return $this->db->get(db_prefix() . 'staffVisitLoc')->result_array();
		}
		/**
			* get department from position department
			* @param  array $arr_value 
			* @param  integer $position  
			* @return string            
		*/
		public function get_department_from_position_department($arr_value, $position)
		{
			$job_p_id='';
			$job_p=[];
			$index_dep = 0;
			if($position == false){
				foreach ($arr_value as $key => $value) {
					$sql_where = 'find_in_set('.$value.', department_id)';
					$this->db->where($sql_where);
					$arr_job_position = $this->db->get(db_prefix().'hr_job_position')->result_array();
					if(count($arr_job_position) > 0){
						foreach ($arr_job_position as $value) {
							if(!in_array($value['job_p_id'], $job_p)){
								$job_p[$index_dep] = $value['job_p_id'];
								$index_dep++;
							}
						}
					}
				}
				if(count($job_p) > 0){
					$job_p_id .= implode(',', $job_p);
				}
				}else{
				foreach ($arr_value as $key => $value) {
					$this->db->where('position_id', $value);
					$arr_job_position = $this->db->get(db_prefix().'hr_job_position')->result_array();
					if(count($arr_job_position) > 0){
						foreach ($arr_job_position as $value) {
							if(!in_array($value['job_p_id'], $job_p)){
								$job_p[$index_dep] = $value['job_p_id'];
								$index_dep++;
							}
						}
					}
				}
				if(count($job_p) > 0){
					$job_p_id .= implode(',', $job_p);
				}
			}
			return $job_p_id;
		}
		/**
			* get position by department
			* @param integer $department_id 
			* @param  integer $status        
			* @return string                
		*/
		public function get_position_by_department($department_id = '', $status)
		{
			$job_position=[];
			$index_dep = 0;
			$options = '';
			if(is_array($department_id))
			{
				/*get staff in deaprtment start*/
				foreach ($department_id as $key => $value) {
					$sql_where = 'find_in_set('.$value.', department_id)';
					$this->db->where($sql_where);
					$arr_job_position = $this->db->get(db_prefix().'hr_job_position')->result_array();
					if(count($arr_job_position) > 0){
						foreach ($arr_job_position as $value) {
							if(!in_array($value['position_id'], $job_position)){
								$options .= '<option value="' . $value['position_id'] . '">' . $value['position_name'] . '</option>';
								$job_position[$index_dep] = $value['position_id'];
								$index_dep++;
							}
						}
					}
				}
				return $options;
				}else{
				$arr_job_position = $this->get_job_position();
				$options = '';
				foreach ($arr_job_position as $note) {
					$options .= '<option value="' . $note['position_id'] . '">' . $note['position_name'] . '</option>';
				}
				return $options;
			}
		}
		/**
			* job position add update salary scale
			* @param  array $data 
			* @return boolean       
		*/
		public function job_position_add_update_salary_scale($data){
			if(isset($data['job_position_id'])){
				$job_position_id = $data['job_position_id'];
				unset($data['job_position_id']);
			}
			$this->db->where('job_position_id', $job_position_id);
			$this->db->delete(db_prefix().'hr_jp_salary_scale');
			$this->db->insert(db_prefix().'hr_jp_salary_scale',[
			'job_position_id' => $job_position_id,
			'rel_type' => 'insurance',
			'value' => hr_profile_reformat_currency($data['premium_rates']),
			]);
			foreach($data['salary_form'] as $salary_key => $salary_value){
				$this->db->insert(db_prefix().'hr_jp_salary_scale', [
				'job_position_id' => $job_position_id,
				'rel_type' => 'salary',
				'rel_id' => $salary_value,
				'value' =>  hr_profile_reformat_currency($data['contract_expense'][$salary_key]),
				]);
			}
			foreach($data['allowance_type'] as $allowance_key => $allowance_value){
				$this->db->insert(db_prefix().'hr_jp_salary_scale', [
				'job_position_id' => $job_position_id,
				'rel_type' => 'allowance',
				'rel_id' => $allowance_value,
				'value' =>  hr_profile_reformat_currency($data['allowance_expense'][$allowance_key]),
				]);
			}
			return true;
		}
		/**
			* get staff
			* @param  integer $id    
			* @param  array  $where 
			* @return array        
		*/
		public function get_staff($id = '', $where = [])
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$select_str = db_prefix() . 'staff.*,CONCAT(firstname," ",lastname) as full_name,'.db_prefix() . 'accountbalances.BAL1,tblaccountsld.SLDTypeID';
			if (is_staff_logged_in() && $id != '' && $id == get_staff_user_id()) {
				$select_str .= ',(SELECT COUNT(*) FROM ' . db_prefix() . 'notifications WHERE touserid=' . get_staff_user_id() . ' and isread=0) as total_unread_notifications, (SELECT COUNT(*) FROM ' . db_prefix() . 'todos WHERE finished=0 AND staffid=' . get_staff_user_id() . ') as total_unfinished_todos';
			}
			$this->db->select($select_str);
			$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = '.db_prefix() . 'staff.AccountID AND tblaccountbalances.PlantID = '.$selected_company.' AND tblaccountbalances.FY='.$fy,'LEFT');
			$this->db->join(db_prefix() . 'accountsld', db_prefix() . 'accountsld.AccountID = '.db_prefix() . 'staff.AccountID AND tblaccountsld.PlantID = '.$selected_company,'LEFT');
			$this->db->where($where);
			if (is_numeric($id)) {
				$this->db->where('staffid', $id);
				$staff = $this->db->get(db_prefix() . 'staff')->row();
				if ($staff) {
					$staff->permissions = $this->get_staff_permissions($id);
				}
				return $staff;
			}
			$this->db->order_by('firstname', 'desc');
			return $this->db->get(db_prefix() . 'staff')->result_array();
		}
		/**
			* add manage info reception
			* @param array $data 
		*/
		public function add_manage_info_reception($data){
			$this->db->empty_table(db_prefix() . 'group_checklist');       
			$this->db->empty_table(db_prefix() . 'checklist');       
			foreach ($data['title_name'] as $key => $menu) {
				if($menu != ''){
					$data_s['group_name'] = $menu;
					$this->db->insert(db_prefix() . 'group_checklist', $data_s);
					$insert_id = $this->db->insert_id();
					foreach ($data['sub_title_name'][$key] as $sub_menu) {
						if($sub_menu != ''){
							$data_ss['name'] = $sub_menu;
							$data_ss['group_id'] = $insert_id;
							$this->db->insert(db_prefix() . 'checklist', $data_ss);
						}                      
					}
				}         
			}
		}
		/**
			* add setting training
		*/
		public function add_setting_training($data){
			if(isset($data['training_type'])){
				$this->db->empty_table(db_prefix() . 'setting_training');  
				$this->db->insert(db_prefix() . 'setting_training', $data);  
			}   
		}
		/**
			* checklist by group
			* @param  integer $group_id 
			* @return array           
		*/
		public function checklist_by_group($group_id = ''){
			$this->db->where('group_id', $group_id);
			return $this->db->get(db_prefix().'checklist')->result_array();
		}
		/**
			* count max checklist
			* @return [type] 
		*/
		public function count_max_checklist()
		{
			$sql_where = "SELECT count(id) as total_sub_item  FROM ".db_prefix()."checklist
			group by group_id
			order by total_sub_item desc limit 1";
			$max_sub_item = $this->db->query($sql_where)->row();
			if($max_sub_item){
				return (float)$max_sub_item->total_sub_item;
			}
			return 1;
		}
		/**
			* get staff info id
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function get_staff_info_id($staffid){
			$this->db->where('staffid', $staffid);
			return $this->db->get(db_prefix().'staff')->row();
		}
		/**
			* add_manage_info_reception_for_staff
			* @param integer $id_staff 
			* @param integer $data     
		*/
		public function add_manage_info_reception_for_staff($id_staff, $data){
			if(isset($data['sub_title_name'])&&isset($data['title_name'])){
				foreach ($data['title_name'] as $key => $menu) {
					if($menu != ''){
						$data_s['group_name'] = $menu;
						$data_s['staffid'] = $id_staff;
						$this->db->insert(db_prefix() . 'hr_group_checklist_allocation', $data_s);
						$insert_id = $this->db->insert_id();
						foreach ($data['sub_title_name'][$key] as $sub_menu) {
							if($sub_menu != ''){
								$data_ss['name'] = $sub_menu;
								$data_ss['group_id'] = $insert_id;
								$this->db->insert(db_prefix() . 'hr_checklist_allocation', $data_ss);
							}                      
						}
					}         
				}
			}            
		} 
		/**
			* add asset staff
			* @param integer $id   
			* @param array $data 
		*/
		public function add_asset_staff($id,$data){  
			foreach ($data as $key => $value) {
				$this->db->insert(db_prefix() . 'hr_allocation_asset', [
				'staff_id'      => $id,
				'asset_name' => $value['name'],
				'assets_amount' => '1']);
			}
		}
		/**
			* get jp interview training
			* @param  integer $position_id   
			* @param  integer $training_type 
			* @return object                
		*/
		public function get_jp_interview_training($position_id, $training_type = ''){
			if($training_type==''){
				$type_training = $this->getTraining_Setting();    
				if($type_training){
					return $this->db->query('select * from '.db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id) and training_type = \''.$type_training->training_type.'\' ORDER BY date_add desc limit 1')->row();
				}
				else{
					return $this->db->query('select * from '.db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id) ORDER BY date_add desc limit 1')->row();
				}
			}
			else{
				return $this->db->query('select * from '.db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id) and training_type = \''.$training_type.'\' ORDER BY date_add desc limit 1')->row();
			}
		}
		/**
			* add training staff
			* @param integer $data_training 
			* @param integer $id_staff      
		*/
		public function add_training_staff($data_training,$id_staff){
			$data['staffid'] = $id_staff;
			$explode = explode(',', $data_training->position_training_id);
			$data['training_process_id'] = implode(',',array_unique($explode));
			$data['training_type'] = $data_training->training_type;
			$data['training_name'] = $data_training->training_name;
			$data['jp_interview_training_id'] = $data_training->training_process_id;
			$this->db->insert(db_prefix() . 'hr_training_allocation', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		/**
			* add transfer records reception
			* @param array $data    
			* @param integer $staffid 
		*/
		public function add_transfer_records_reception($data,$staffid){
			$list_meta = $this->get_list_record_meta();
			foreach ($data as $key => $value) {
				$name='';
				foreach ($list_meta as $list_item) {
					if($list_item['meta']==$value){
						$name=$list_item['name'];
					}
				}
				$this->db->insert(db_prefix().'hr_transfer_records_reception', [
				'name' => $name,
				'meta' => $value,
				'staffid' => $staffid
				]);
			}  
		}
		/**
			* getPercent
			* @param  integer $total  
			* @param  integer $effect 
			* @return foat         
		*/
		public function getPercent($total,$effect){
			if($total == 0){
				return 0;
			}
			return number_format(($effect * 100 / $total), 0);
		}
		/**
			* get group checklist allocation by staff id
			* @param  integer $staffid 
			* @return integer          
		*/
		public function get_group_checklist_allocation_by_staff_id($staffid){
			$this->db->where('staffid', $staffid);
			return $this->db->get(db_prefix().'hr_group_checklist_allocation')->result_array();
		}
		/**
			* get checklist allocation by group id
			* @param  integer $id_group 
			* @return array           
		*/
		public function get_checklist_allocation_by_group_id($id_group){
			$this->db->where('group_id', $id_group);
			return $this->db->get(db_prefix().'hr_checklist_allocation')->result_array();
		}
		/**
			* get resultset training
			* @param  integer $id 
			* @return integer     
		*/
		public function get_resultset_training($id){
			return $this->db->query('select * from '.db_prefix().'hr_p_t_surveyresultsets where staff_id = \''.$id.'\' order by date desc')->result_array();
		}
		/**
			* get allocation asset
			* @param  integer $staff_id 
			* @return array           
		*/
		public function get_allocation_asset($staff_id){
			$this->db->where('staff_id',$staff_id);
			return $this->db->get(db_prefix().'hr_allocation_asset')->result_array();
		}
		/**
			* get result training staff
			* @param  integer $list_resultsetid 
			* @return array                   
		*/
		public function get_result_training_staff($list_resultsetid){
			return $this->db->query('select * from '.db_prefix().'hr_p_t_form_results where resultsetid in ('.$list_resultsetid.')')->result_array();
		}
		/**
			* get id result correct
			* @param  integer $id_question 
			* @return object              
		*/
		public function get_id_result_correct($question_id){
			$boxdescriptionids =[];
			$this->db->where('questionid', $question_id);
			$this->db->where('correct', 0);
			$result = $this->db->get(db_prefix().'hr_p_t_form_question_box_description')->result_array();
			foreach ($result as $value) {
				array_push($boxdescriptionids, $value['questionboxdescriptionid']);
			}
			return $boxdescriptionids;
		}
		/**
			* get point training question form
			* @param  [type] $id_question 
			* @return [type]              
		*/
		public function get_point_training_question_form($id_question){
			$this->db->where('questionid',$id_question);
			return $this->db->get(db_prefix().'hr_position_training_question_form')->row();
		}
		/**
			* delete manage info reception
			* @param  integer $id 
		*/
		public function delete_manage_info_reception($id){
			$this->db->where('staffid', $id);
			$list = $this->db->get(db_prefix().'hr_group_checklist_allocation')->result_array();
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix().'hr_group_checklist_allocation');
			foreach ($list as $sub_menu) {
				$this->db->where('group_id', $sub_menu['id']);
				$this->db->delete(db_prefix().'hr_checklist_allocation');
			}                         
		}
		/**
			* delete setting training
			* @param  integer $id 
		*/
		public function delete_setting_training($id){
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix().'hr_training_allocation');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete setting asset allocation
			* @param  integer $id 
			* @return integer     
		*/
		public function delete_setting_asset_allocation($id){
			$this->db->where('staff_id', $id);
			$this->db->delete(db_prefix().'hr_allocation_asset');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete reception
			* @param  integer $id 
			* @return boolean     
		*/
		public function delete_reception($id){
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix() . 'hr_rec_transfer_records');
			if ($this->db->affected_rows() > 0) {
				$this->db->where('staffid', $id);
				$this->db->delete(db_prefix() . 'hr_training_allocation');
				$this->db->where('staff_id', $id);
				$this->db->delete(db_prefix() . 'hr_allocation_asset');
				$this->db->where('staffid', $id);
				$data_checklist = $this->db->get(db_prefix().'hr_group_checklist_allocation')->result_array();
				if(isset($data_checklist)){
					if($data_checklist){
						$this->db->where('staffid', $id);
						$this->db->delete(db_prefix() . 'hr_group_checklist_allocation');
						foreach ($data_checklist as $key => $checklist) {
							$this->db->where('group_id', $checklist['id']);
							$this->db->delete(db_prefix() . 'hr_checklist_allocation');                                         
						}                    
					}
				}
				return true;
			}
			return false;
		}
		/**
			* get department by staffid
			* @param  integer $id_staff 
			* @return object           
		*/
		public function get_department_by_staffid($id_staff){
			$this->db->where('staffid',$id_staff);
			$departments = $this->db->get(db_prefix().'staff_departments')->result_array();
			$w = '0';
			if(isset($departments[0]['departmentid'])){
				$w = $departments[0]['departmentid'];
			}
			return $this->db->query('select * from '.db_prefix().'departments where departmentid = '.$w)->row();
		}
		/**
			* get transfer records reception staff
			* @param  integer $id 
			* @return integer     
		*/
		public function get_transfer_records_reception_staff($id){
			$this->db->where('staffid',$id);
			return $this->db->get(db_prefix().'hr_transfer_records_reception')->result_array();
		}
		/**
			* update checklist
			* @param  array $data 
			* @return boolean       
		*/
		public function update_checklist($data){ 
			$this->db->where('id', $data['checklist_id']);
			$this->db->update(db_prefix() . 'hr_checklist_allocation', ['status' => $data['status_checklist']]);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete tag item
			* @param  array $data 
			* @return boolean       
		*/
		public function delete_tag_item($tag_id){
			$count_af = 0;
			$this->db->where(db_prefix() . 'taggables.tag_id', $tag_id);
			$this->db->delete(db_prefix() . 'taggables');
			if ($this->db->affected_rows() > 0) {
				$count_af++;
			}
			$this->db->where(db_prefix() . 'tags.id', $tag_id);
			$this->db->delete(db_prefix() . 'tags');
			if ($this->db->affected_rows() > 0) {
				$count_af++;
			}
			return $count_af > 0 ?  true :  false;
		}
		/**
			* add new asset staff
			* @param integer $id   
			* @param array $data 
		*/
		public function add_new_asset_staff($id,$data)
		{  
			foreach ($data as $key => $value) {
				if($value != ''){
					$this->db->insert(db_prefix() . 'hr_allocation_asset', [
					'staff_id'      => $id,
					'asset_name' => $value,
					'assets_amount' => '1',
					]);
				}
			}
		}
		/**
			* update asset staff
			* @param  array $data 
			* @return boolean       
		*/
		public function update_asset_staff($data){ 
			$this->db->where('allocation_id', $data['allocation_id']);
			$this->db->update(db_prefix() . 'hr_allocation_asset', ['status_allocation' => $data['status_allocation']]);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete allocation asset
			* @param  integer $allocation_id 
			* @return boolean                
		*/
		public function delete_allocation_asset($allocation_id){
			$this->db->where('allocation_id',$allocation_id);
			$this->db->delete(db_prefix() . 'hr_allocation_asset');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* get training allocation staff
			* @param  integer $id 
			* @return object     
		*/
		public function get_training_allocation_staff($id){
			$this->db->where('staffid',$id);
			return $this->db->get(db_prefix().'hr_training_allocation')->row();
		}
		/**
			* @param  integer ID (option)
			* @param  boolean (optional)
			* @return mixed
			* Get departments where staff belongs
			* If $onlyids passed return only departmentsID (simple array) if not returns array of all departments
		*/
		public function get_staff_departments($userid = false, $onlyids = false)
		{
			if ($userid == false) {
				$userid = get_staff_user_id();
			}
			if ($onlyids == false) {
				$this->db->select();
				} else {
				$this->db->select(db_prefix() . 'staff_departments.departmentid');
			}
			$this->db->from(db_prefix() . 'staff_departments');
			$this->db->join(db_prefix() . 'departments', db_prefix() . 'staff_departments.departmentid = ' . db_prefix() . 'departments.departmentid', 'left');
			$this->db->where('staffid', $userid);
			$departments = $this->db->get()->result_array();
			if ($onlyids == true) {
				$departmentsid = [];
				foreach ($departments as $department) {
					array_push($departmentsid, $department['departmentid']);
				}
				return $departmentsid;
			}
			return $departments;
		}
		/**
			* Get staff permissions
			* @param  mixed $id staff id
			* @return array
		*/
		public function get_staff_permissions($id)
		{
			// Fix for version 2.3.1 tables upgrade
			if (defined('DOING_DATABASE_UPGRADE')) {
				return [];
			}
			$permissions = $this->app_object_cache->get('staff-' . $id . '-permissions');
			if (!$permissions && !is_array($permissions)) {
				$this->db->where('staff_id', $id);
				$permissions = $this->db->get('staff_permissions')->result_array();
				$this->app_object_cache->add('staff-' . $id . '-permissions', $permissions);
			}
			return $permissions;
		}
		public function get_job_position_arrayid()
		{
			$position = $this->db->query('select * from '.db_prefix().'hr_job_position')->result_array();
			$position_arrray = [];
			foreach ($position as $value) {
				array_push($position_arrray, $value['position_id']);
			}
			return $position_arrray;
		}
		/**
			* get workplace array id
			* @return [type] 
		*/
		public function get_workplace_array_id()
		{
			$workplace = $this->db->query('select * from tblhr_workplace')->result_array();
			$workpalce_array =[];
			foreach ($workplace as $value) {
				array_push($workpalce_array, $value['id']);
			}
			return $workpalce_array;
		}
		/**
			* get workplace
			* @param  boolean $id 
			* @return [type]      
		*/
		public function get_workplace($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'hr_workplace')->row();
			}
			if ($id == false) {
				$this->db->select('*');
				$this->db->from(db_prefix() . 'hr_workplace');
				$this->db->order_by('name');
				$query = $this->db->get();
				return $query->result_array();
				//return  $this->db->get(db_prefix() . 'hr_workplace')->result_array();
			}
		}
		/**
			* get workplace
			* @param  boolean $id 
			* @return [type]      
		*/
		public function get_headquarter($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'headquarter')->row();
			}
			if ($id == false) {
				$this->db->select('*');
				$this->db->from(db_prefix() . 'headquarter');
				$this->db->order_by('name');
				$query = $this->db->get();
				return $query->result_array();
				//return  $this->db->get(db_prefix() . 'hr_workplace')->result_array();
			}
		}
		public function get_state_list()
		{
			$this->db->select('*');
			$this->db->where('country_id', '1');
			$this->db->from(db_prefix() . 'xx_statelist');
			$this->db->order_by('state_name');
			$query = $this->db->get();
			return $query->result_array();
		}
		public function SLDTYPE()
		{
			$this->db->select('*');
			$this->db->where('FilterTypeID', 'SLDTYPE');
			$this->db->from(db_prefix() . 'sldtypes');
			$this->db->order_by('SLDTypeName');
			$query = $this->db->get();
			return $query->result_array();
		}
		public function GetOfficeLocation()
		{
			$this->db->select('*');
			$this->db->from(db_prefix() . 'OfficeMaster');
			$this->db->order_by('AccountID');
			$query = $this->db->get();
			return $query->result_array();
		}
		/**
			* add workplace
			* @param [type] $data 
		*/
		public function add_workplace($data){
			$this->db->insert(db_prefix() . 'hr_workplace', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				return $insert_id;
			}
			return false;
		}
		/**
			* add workplace
			* @param [type] $data 
		*/
		public function add_headquarter($data){
			$this->db->insert(db_prefix() . 'headquarter', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				return $insert_id;
			}
			return false;
		}
		/**
			* update Headquarter
			* @param  [type] $data 
			* @param  [type] $id   
			* @return [type]       
		*/
		public function update_headquarter($data, $id)
		{   
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'headquarter', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete workplace
			* @param  [type] $id 
			* @return [type]     
		*/
		public function delete_headquarter($id){
			$check_ = $this->check_quarter($id);
			if(empty($check_)){
				$this->db->where('id', $id);
				$this->db->delete(db_prefix() . 'headquarter');
				if ($this->db->affected_rows() > 0) {
					return true;
				}
			}
			return false;
		}
		public function check_quarter($id)
		{
		    $this->db->select('*');
		    $this->db->where('headqurter', $id);
            $this->db->from(db_prefix() . 'staff');
            $query = $this->db->get();
            return $query->result_array();
		}
		/**
			* update workplace
			* @param  [type] $data 
			* @param  [type] $id   
			* @return [type]       
		*/
		public function update_workplace($data, $id)
		{   
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'hr_workplace', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete workplace
			* @param  [type] $id 
			* @return [type]     
		*/
		public function delete_workplace($id){
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'hr_workplace');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* format date
			* @param  date $date     
			* @return date           
		*/
		public function format_date($date){
			if(!$this->check_format_date_ymd($date)){
				$date = to_sql_date($date);
			}
			return $date;
		}            
		/**
			* format date time
			* @param  date $date     
			* @return date           
		*/
		public function format_date_time($date){
			if(!$this->check_format_date($date)){
				$date = to_sql_date($date, true);
			}
			return $date;
		}
		/**
			* check format date ymd
			* @param  date $date 
			* @return boolean       
		*/
		public function check_format_date_ymd($date) {
			if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
				return true;
				} else {
				return false;
			}
		}
		/**
			* check format date
			* @param  date $date 
			* @return boolean 
		*/
		public function check_format_date($date){
			if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/",$date)) {
				return true;
				} else {
				return false;
			}
		}
		/**
			* @param  integer (optional)
			* @return object
			* Get single goal
		*/
		public function add_staff($data)
		{
			$selected_company = $this->session->userdata('root_company');
			if (isset($data['fakeusernameremembered'])) {
				unset($data['fakeusernameremembered']);
			}
			if (isset($data['fakepasswordremembered'])) {
				unset($data['fakepasswordremembered']);
			}
			// First check for all cases if the email exists.
			$this->db->where('email', $data['email']);
			$email = $this->db->get(db_prefix() . 'staff')->row();
			/*if ($email) {
				//	die('Email already exists');
			}*/
			$data['admin'] = 0;
			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
				}
			}
			$send_welcome_email = true;
			$original_password  = $data['password'];
			if (!isset($data['send_welcome_email'])) {
				$send_welcome_email = false;
				} else {
				unset($data['send_welcome_email']);
			}
			$data['password']        = app_hash_password($data['password']);
			$data['datecreated']     = date('Y-m-d H:i:s');
			$data['StartDate']     = to_sql_date($data['datecreated'])." ".date('H:i:s');
			if (isset($data['departments'])) {
				$departments = $data['departments'];
				unset($data['departments']);
			}
			if (isset($data['custom_fields'])) {
				$custom_fields = $data['custom_fields'];
				unset($data['custom_fields']);
			}
			if ($data['admin'] == 1) {
				$data['is_not_staff'] = 0;
			}
			if (isset($data['birthday'])) {
				$data['birthday'] = to_sql_date($data['birthday']);
				}else{
				$data['birthday'] = null;
			}
			if (isset($data['days_for_identity'])) {
				$data['days_for_identity'] = to_sql_date($data['days_for_identity']);
				}else{
				$data['days_for_identity'] = null;
			}
			$data['UserID'] = $this->session->userdata('username');
			$Sltd = $data['sldtype'];
			unset($data['sldtype']);
			$this->db->insert(db_prefix() . 'staff', $data);
			$staffid = $this->db->insert_id();
			if ($staffid) {
				$sldtData = array(
		        'PlantID'=>$selected_company,
		        'AccountID'=>$data['AccountID'],
		        'SLDTypeID'=>$Sltd,
		        'UserID'=>$this->session->userdata('username'),
				);
				$this->db->insert(db_prefix() . 'accountsld', $sldtData);
				/*update next number setting*/
				$this->update_prefix_number(['staff_code_number' =>  get_hr_profile_option('staff_code_number')+1]);
				$slug = $data['firstname'] . ' ' . $data['lastname'];
				if ($slug == ' ') {
					$slug = 'unknown-' . $staffid;
				}
				/*if ($send_welcome_email == true) {
					send_mail_template('staff_created', $data['email'], $staffid, $original_password);
				}*/
				$this->db->where('staffid', $staffid);
				$this->db->update(db_prefix() . 'staff', [
				'media_path_slug' => slug_it($slug),
				]);
				if (isset($custom_fields)) {
					handle_custom_fields_post($staffid, $custom_fields);
				}
				if (isset($departments)) {
					foreach ($departments as $department) {
						$this->db->insert(db_prefix() . 'staff_departments', [
						'staffid'      => $staffid,
						'departmentid' => $department,
						]);
					}
				}
				log_activity('New Staff Member Added [ID: ' . $staffid . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
				// Get all announcements and set it to read.
				$this->db->select('announcementid');
				$this->db->from(db_prefix() . 'announcements');
				$this->db->where('showtostaff', 1);
				$announcements = $this->db->get()->result_array();
				foreach ($announcements as $announcement) {
					$this->db->insert(db_prefix() . 'dismissed_announcements', [
					'announcementid' => $announcement['announcementid'],
					'staff'          => 1,
					'userid'         => $staffid,
					]);
				}
				hooks()->do_action('staff_member_created', $staffid);
				return $staffid;
			}
			return false;
		}
		/**
			* update staff
			* @param  [type] $data 
			* @param  [type] $id   
			* @return [type]       
		*/
		public function update_staff($data, $id)
		{
			if (isset($data['fakeusernameremembered'])) {
				unset($data['fakeusernameremembered']);
			}
			if (isset($data['fakepasswordremembered'])) {
				unset($data['fakepasswordremembered']);
			}
			$data = hooks()->apply_filters('before_update_staff_member', $data, $id);
			if($this->get_staff($id)->admin == '1') {
				$data['administrator'] = 1;
			}
			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
					} else {
					if ($id != get_staff_user_id()) {
						if ($id == 1) {
							return [
							'cant_remove_main_admin' => true,
							];
						}
						} else {
						return [
						'cant_remove_yourself_from_admin' => true,
						];
					}
					$data['admin'] = 0;
				}
			}
			$affectedRows = 0;
			if (isset($data['departments'])) {
				$departments = $data['departments'];
				unset($data['departments']);
			}
			if (isset($data['custom_fields'])) {
				$custom_fields = $data['custom_fields'];
				if (handle_custom_fields_post($id, $custom_fields)) {
					$affectedRows++;
				}
				unset($data['custom_fields']);
			}
			if (empty($data['password'])) {
				unset($data['password']);
				} else {
				$data['password']             = app_hash_password($data['password']);
				$data['last_password_change'] = date('Y-m-d H:i:s');
			}
			if (isset($data['two_factor_auth_enabled'])) {
				$data['two_factor_auth_enabled'] = 1;
				} else {
				$data['two_factor_auth_enabled'] = 0;
			}
			if (isset($data['is_not_staff'])) {
				$data['is_not_staff'] = 1;
				} else {
				$data['is_not_staff'] = 0;
			}
			if (isset($data['admin']) && $data['admin'] == 1) {
				$data['is_not_staff'] = 0;
			}
			if (isset($data['birthday'])) {
				$data['birthday'] = to_sql_date($data['birthday']);
				}else{
				$data['birthday'] = null;
			}
			if (isset($data['days_for_identity'])) {
				$data['days_for_identity'] = to_sql_date($data['days_for_identity']);
				}else{
				$data['days_for_identity'] = null;
			}
			$data['date_update'] = date('Y-m-d H:i:s');
			$data['UserID2'] = $this->session->userdata('username');
			$data['StartDate'] = to_sql_date($data['datecreated'])." ".date('H:i:s');
			$data['email_signature'] = nl2br_save_html($data['email_signature']);
			$this->load->model('departments_model');
			$staff_departments = $this->departments_model->get_staff_departments($id);
			if (sizeof($staff_departments) > 0) {
				if (!isset($data['departments'])) {
					$this->db->where('staffid', $id);
					$this->db->delete(db_prefix() . 'staff_departments');
					} else {
					foreach ($staff_departments as $staff_department) {
						if (isset($departments)) {
							if (!in_array($staff_department['departmentid'], $departments)) {
								$this->db->where('staffid', $id);
								$this->db->where('departmentid', $staff_department['departmentid']);
								$this->db->delete(db_prefix() . 'staff_departments');
								if ($this->db->affected_rows() > 0) {
									$affectedRows++;
								}
							}
						}
					}
				}
				if (isset($departments)) {
					foreach ($departments as $department) {
						$this->db->where('staffid', $id);
						$this->db->where('departmentid', $department);
						$_exists = $this->db->get(db_prefix() . 'staff_departments')->row();
						if (!$_exists) {
							$this->db->insert(db_prefix() . 'staff_departments', [
							'staffid'      => $id,
							'departmentid' => $department,
							]);
							if ($this->db->affected_rows() > 0) {
								$affectedRows++;
							}
						}
					}
				}
				} else {
				if (isset($departments)) {
					foreach ($departments as $department) {
						$this->db->insert(db_prefix() . 'staff_departments', [
						'staffid'      => $id,
						'departmentid' => $department,
						]);
						if ($this->db->affected_rows() > 0) {
							$affectedRows++;
						}
					}
				}
			}
			/*echo "<pre>";
				print_r($data);
				echo $id;
			die;*/
			$SltDetails = $this->GetSldDetails($data['AccountID']);
			$selected_company = $this->session->userdata('root_company');
			if(empty($SltDetails)){
				$Sltd = $data['sldtype'];
				unset($data['sldtype']);
				$sldtData = array(
		        'SLDTypeID'=>$Sltd,
		        'UserID'=>$this->session->userdata('username'),
		        'AccountID'=>$data['AccountID'],
		        'PlantID'=>$selected_company,
				);
				$this->db->insert(db_prefix() . 'accountsld',$sldtData);
				}else{
				$Sltd = $data['sldtype'];
				unset($data['sldtype']);
				$sldtData = array(
		        'SLDTypeID'=>$Sltd,
		        'UserID2'=>$this->session->userdata('username'),
		        'Lupdate'=>date('Y-m-d H:i:s'),
				);
				$this->db->where('AccountID', $data['AccountID']);
				$this->db->where('PlantID', $selected_company);
				$this->db->update(db_prefix() . 'accountsld', $sldtData);
			}
			if($data["DiveceID"] == ""){
				$data["DiveceID"] = NULL;
			}
			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'staff', $data);
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			if ($affectedRows > 0) {
				hooks()->do_action('staff_member_updated', $id);
				log_activity('Staff Member Updated [ID: ' . $id . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
				return true;
			}
			return false;
		}
		public function GetSldDetails($AccountID){
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('AccountID', $AccountID);
			$this->db->where('PlantID', $selected_company);
			return $this->db->get(db_prefix() . 'accountsld')->row();
		}
		/**
			* get department name
			* @param  integer $staffid 
		*/
		public function getdepartment_name($staffid){
			return $this->db->query('select s.staffid, d.departmentid ,d.name
			from tblstaff as s 
			left join tblstaff_departments as sd on sd.staffid = s.staffid
			left join tbldepartments d on d.departmentid = sd.departmentid 
			where s.staffid in ('.$staffid.')
			order by d.departmentid,s.staffid')->row();
		}
		/**
			* get child node staff chart
			* @param  integer $id      
			* @param  integer $arr_dep 
			* @return array          
		*/
		private function get_child_node_staff_chart($id, $arr_dep){
			$dep_tree = array();
			foreach ($arr_dep as $dep) {
				if($dep['pid']==$id){ 
					$dpm = $this->getdepartment_name($dep['id']);  
					$node = array();             
					$node['name'] = $dep['name'];
					$node['job_position_name'] = '';
					if($dep['job_position_name'] != null && $dep['job_position_name'] != 'undefined'){
						$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
						$node['job_position_name'] = $dep['job_position_name'];
					}
					if($dep['rname'] != null){
						$node['title'] = $dep['rname'];
						$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
						}else{
						$node['title'] = '';
					}
					if($dpm->name != null){
						$node['departmentname'] = $dpm->name;
						$node['dp_icon'] = '"fa fa-sitemap"';
						}else{
						$node['departmentname'] = ' ';
					}
					$node['image'] = staff_profile_image($dep['id'], [
					'staff-profile-image-small staff-chart-padding',
					]);
					$node['children'] = $this->get_child_node_staff_chart($dep['id'], $arr_dep);
					if(count($node['children']) == 0){
						unset($node['children']);
					}
					$dep_tree[] = $node;
				} 
			} 
			return $dep_tree;
		}
		/**
			* delete staff
			* @param  [type] $id               
			* @param  [type] $transfer_data_to 
			* @return [type]                   
		*/
		public function delete_staff($id, $transfer_data_to)
		{
			if (!is_numeric($transfer_data_to)) {
				return false;
			}
			if ($id == $transfer_data_to) {
				return false;
			}
			hooks()->do_action('before_delete_staff_member', [
			'id'               => $id,
			'transfer_data_to' => $transfer_data_to,
			]);
			$name           = get_staff_full_name($id);
			$transferred_to = get_staff_full_name($transfer_data_to);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'estimates', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('sale_agent', $id);
			$this->db->update(db_prefix() . 'estimates', [
			'sale_agent' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'invoices', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('sale_agent', $id);
			$this->db->update(db_prefix() . 'invoices', [
			'sale_agent' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'expenses', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'notes', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('userid', $id);
			$this->db->update(db_prefix() . 'newsfeed_post_comments', [
			'userid' => $transfer_data_to,
			]);
			$this->db->where('creator', $id);
			$this->db->update(db_prefix() . 'newsfeed_posts', [
			'creator' => $transfer_data_to,
			]);
			$this->db->where('staff_id', $id);
			$this->db->update(db_prefix() . 'projectdiscussions', [
			'staff_id' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'projects', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'creditnotes', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('staff_id', $id);
			$this->db->update(db_prefix() . 'credits', [
			'staff_id' => $transfer_data_to,
			]);
			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'project_files', [
			'staffid' => $transfer_data_to,
			]);
			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'proposal_comments', [
			'staffid' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'proposals', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'task_comments', [
			'staffid' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->where('is_added_from_contact', 0);
			$this->db->update(db_prefix() . 'tasks', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'files', [
			'staffid' => $transfer_data_to,
			]);
			$this->db->where('renewed_by_staff_id', $id);
			$this->db->update(db_prefix() . 'contract_renewals', [
			'renewed_by_staff_id' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'task_checklist_items', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('finished_from', $id);
			$this->db->update(db_prefix() . 'task_checklist_items', [
			'finished_from' => $transfer_data_to,
			]);
			$this->db->where('admin', $id);
			$this->db->update(db_prefix() . 'ticket_replies', [
			'admin' => $transfer_data_to,
			]);
			$this->db->where('admin', $id);
			$this->db->update(db_prefix() . 'tickets', [
			'admin' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'leads', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('assigned', $id);
			$this->db->update(db_prefix() . 'leads', [
			'assigned' => $transfer_data_to,
			]);
			$this->db->where('staff_id', $id);
			$this->db->update(db_prefix() . 'taskstimers', [
			'staff_id' => $transfer_data_to,
			]);
			$this->db->where('addedfrom', $id);
			$this->db->update(db_prefix() . 'contracts', [
			'addedfrom' => $transfer_data_to,
			]);
			$this->db->where('assigned_from', $id);
			$this->db->where('is_assigned_from_contact', 0);
			$this->db->update(db_prefix() . 'task_assigned', [
			'assigned_from' => $transfer_data_to,
			]);
			$this->db->where('responsible', $id);
			$this->db->update(db_prefix() . 'leads_email_integration', [
			'responsible' => $transfer_data_to,
			]);
			$this->db->where('responsible', $id);
			$this->db->update(db_prefix() . 'web_to_lead', [
			'responsible' => $transfer_data_to,
			]);
			$this->db->where('created_from', $id);
			$this->db->update(db_prefix() . 'subscriptions', [
			'created_from' => $transfer_data_to,
			]);
			$this->db->where('notify_type', 'specific_staff');
			$web_to_lead = $this->db->get(db_prefix() . 'web_to_lead')->result_array();
			foreach ($web_to_lead as $form) {
				if (!empty($form['notify_ids'])) {
					$staff = unserialize($form['notify_ids']);
					if (is_array($staff)) {
						if (in_array($id, $staff)) {
							if (($key = array_search($id, $staff)) !== false) {
								unset($staff[$key]);
								$staff = serialize(array_values($staff));
								$this->db->where('id', $form['id']);
								$this->db->update(db_prefix() . 'web_to_lead', [
								'notify_ids' => $staff,
								]);
							}
						}
					}
				}
			}
			$this->db->where('id', 1);
			$leads_email_integration = $this->db->get(db_prefix() . 'leads_email_integration')->row();
			if ($leads_email_integration->notify_type == 'specific_staff') {
				if (!empty($leads_email_integration->notify_ids)) {
					$staff = unserialize($leads_email_integration->notify_ids);
					if (is_array($staff)) {
						if (in_array($id, $staff)) {
							if (($key = array_search($id, $staff)) !== false) {
								unset($staff[$key]);
								$staff = serialize(array_values($staff));
								$this->db->where('id', 1);
								$this->db->update(db_prefix() . 'leads_email_integration', [
								'notify_ids' => $staff,
								]);
							}
						}
					}
				}
			}
			$this->db->where('assigned', $id);
			$this->db->update(db_prefix() . 'tickets', [
			'assigned' => 0,
			]);
			$this->db->where('staff', 1);
			$this->db->where('userid', $id);
			$this->db->delete(db_prefix() . 'dismissed_announcements');
			$this->db->where('userid', $id);
			$this->db->delete(db_prefix() . 'newsfeed_comment_likes');
			$this->db->where('userid', $id);
			$this->db->delete(db_prefix() . 'newsfeed_post_likes');
			$this->db->where('staff_id', $id);
			$this->db->delete(db_prefix() . 'customer_admins');
			$this->db->where('fieldto', 'staff');
			$this->db->where('relid', $id);
			$this->db->delete(db_prefix() . 'customfieldsvalues');
			$this->db->where('userid', $id);
			$this->db->delete(db_prefix() . 'events');
			$this->db->where('touserid', $id);
			$this->db->delete(db_prefix() . 'notifications');
			$this->db->where('staff_id', $id);
			$this->db->delete(db_prefix() . 'user_meta');
			$this->db->where('staff_id', $id);
			$this->db->delete(db_prefix() . 'project_members');
			$this->db->where('staff_id', $id);
			$this->db->delete(db_prefix() . 'project_notes');
			$this->db->where('creator', $id);
			$this->db->or_where('staff', $id);
			$this->db->delete(db_prefix() . 'reminders');
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix() . 'staff_departments');
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix() . 'todos');
			$this->db->where('staff', 1);
			$this->db->where('user_id', $id);
			$this->db->delete(db_prefix() . 'user_auto_login');
			$this->db->where('staff_id', $id);
			$this->db->delete(db_prefix() . 'staff_permissions');
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix() . 'task_assigned');
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix() . 'task_followers');
			$this->db->where('staff_id', $id);
			$this->db->delete(db_prefix() . 'pinned_projects');
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix() . 'staff');
			log_activity('Staff Member Deleted [Name: ' . $name . ', Data Transferred To: ' . $transferred_to . ']');
			$this->db->where('staffid', $id);
			$this->db->delete(db_prefix() . 'hr_rec_transfer_records');
			hooks()->do_action('staff_member_deleted', [
			'id'               => $id,
			'transfer_data_to' => $transfer_data_to,
			]);      
			return true;
		}
		/**
			* get hr profile attachments
			* @param  integer $staffid 
			* @return array          
		*/
		public function get_hr_profile_attachments($staffid){
			$this->db->order_by('dateadded', 'desc');
			$this->db->where('rel_id', $staffid);
			$this->db->where('rel_type', 'hr_staff_file');
			return $this->db->get(db_prefix() . 'files')->result_array();
		}
		/**
			* get records received
			* @param  integer $id
			* @return object     
		*/
		public function get_records_received($id)
		{
			return $this->db->query('select tblstaff.records_received from tblstaff where staffid = '.$id)->row();
		}
		/**
			* get hr profile profile file
			* @param  integer $staffid 
			* @return array          
		*/
		public function get_hr_profile_profile_file($staffid){
			$this->db->order_by('dateadded', 'desc');
			$this->db->where('rel_id', $staffid);
			$this->db->where('rel_type', 'staff_profile_images');
			return $this->db->get(db_prefix() . 'files')->result_array();
		}
		/**
			* get duration
			* @return array 
		*/
		public function get_duration(){
			return $this->db->query('SELECT duration, unit FROM tblhr_staff_contract_type group by duration, unit')->result_array();
		}
		/**
			* add education
			* @param array $data 
		*/
		public function add_education($data){
			$data['date_create'] = date('y-m-d');
			$insert_id = $this->db->insert(db_prefix() . 'hr_education', $data);
			if ($insert_id) {
				return $insert_id;
			}
			return false;
		}
		/**
			* update education
			* @param array $data 
		*/
		public function update_education($data)
		{   
			$this->db->where('id', $data['id']);
			$this->db->update(db_prefix() . 'hr_education', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete education
			* @param integer $id 
		*/
		public function delete_education($id){
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'hr_education');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* member get evaluate form
			* @param  integer $staffid 
			* @return array          
		*/
		public function member_get_evaluate_form($staffid){
			$arr_evaluate_form = $this->get_evaluate_form_status();
			$sql = "SELECT staffid, staff_identifi, firstname FROM ".db_prefix().'staff WHERE staffid ='.$staffid;
			$arr_staff = $this->db->query($sql)->result_array();
			$data_object =[];
			foreach ($arr_evaluate_form as $evaluate_value) {
				$data =[];
				if(strlen(json_encode($arr_staff)) != 2){
					$evalute_staff = $this->get_dataobject_result_evaluate($evaluate_value['id'], $arr_staff);
					if(count($evalute_staff[0]) != 0){
						$data['id'] = $evaluate_value['id'];
						$data['hr_code'] = $arr_staff[0]['staff_identifi'];
						$data['eval_form_name'] = $this->get_evaluation_form($evaluate_value['evaluate_form'])->eval_form_name;
						$start_month = $this->get_evaluation_form($evaluate_value['evaluate_form'])->start_month;
						$end_month = $this->get_evaluation_form($evaluate_value['evaluate_form'])->end_month;
						$data['period_eval'] =  date("m/Y", strtotime($evaluate_value['start_month'])).' - '. date("m/Y", strtotime($evaluate_value['end_month']));
						$data['total_kpi'] =  array_reverse($evalute_staff[0])[0];
					}
				}
				if(count($data) != 0){
					array_push($data_object, $data);
				}
			}
			return $data_object;
		}
		/**
			* get evaluate form status
			* @return array 
		*/
		public function get_evaluate_form_status(){
			$this->db->where('status', '1');
			return  $this->db->get(db_prefix() . 'evaluate_form')->result_array();
		}
		/**
			* get dataobject result evaluate
			* @param  integer  $id       
			* @param  boolean $arrstaff 
			* @return integer            
		*/
		public function get_dataobject_result_evaluate($id, $arrstaff = false){
			$evaluation_form = $this->get_evaluate_form($id);
			$emp_marks = json_decode($evaluation_form->emp_marks);
			if(isset($evaluation_form->percent)){
				$percent = json_decode($evaluation_form->percent);
				}else{
				$percent = (float)0;
			}
			$evaluation_form_detail = $this->get_evaluation_form_detail($evaluation_form->evaluate_form);
			$evaluate_result = $this->get_assessor_from($id);
			if($arrstaff != false){
				$arr_staff = $arrstaff;
				}else{
				$sql = "SELECT staffid, staff_identifi, firstname FROM ".db_prefix().'staff WHERE 1 = 1';           
				if(isset($evaluation_form->department_id) && $evaluation_form->department_id != 'null' && $evaluation_form->department_id != '0'&&$evaluation_form->apply_for=='department'){
					$searchVal = array('[', ']', '"');
					$replaceVal = array('(', ')', '');
					$department_array = str_replace($searchVal, $replaceVal, $evaluation_form->department_id);
					$sql .= ' AND staffid in ( select staffid from tblstaff_departments where departmentid in '.$department_array.' )';
				}
				if(isset($evaluation_form->role_id) && $evaluation_form->role_id != 'null' && $evaluation_form->role_id != '0'&&$evaluation_form->apply_for=='role'){
					$searchVal = array('[', ']', '"');
					$replaceVal = array('(', ')', '');
					$role_array = str_replace($searchVal, $replaceVal, $evaluation_form->roles_id);
					$sql .= ' AND role in '.$role_array.'';
				} 
				if(isset($evaluation_form->staff_id) && $evaluation_form->staff_id != 'null' && $evaluation_form->staff_id != '0'&&$evaluation_form->apply_for=='staff'){
					$searchVal = array('[', ']', '"');
					$replaceVal = array('(', ')', '');
					$staff_array = str_replace($searchVal, $replaceVal, $evaluation_form->staff_id);
					$sql .= ' AND staffid in '.$staff_array.'';
				} 
				$arr_staff = $this->db->query($sql)->result_array();
			}
			$arr_object =[];
			$flag_member_evaluate = 0;
			foreach ($arr_staff as $staff) {
				$kpi_staff = 0;
				$staff_info =[];
				$staff_info[] = $staff['staff_identifi'];
				$staff_info[] = $staff['firstname'];
				foreach ($evaluation_form_detail as $eval_det_key => $eval_det_value) {
					$arr_income = json_decode($eval_det_value['income']);
					$arr_kpi_percent = json_decode($eval_det_value['kpi_percent']);
					$arr_kpi_formula = json_decode($eval_det_value['kpi_formula']);
					$kpi_temp = 0;
					foreach (json_decode($eval_det_value['kpi_key']) as $kpi_key => $kpi_value) {
						$staff_info[] = $arr_income[$kpi_key] ;
						foreach ($emp_marks as $emp_marks_key =>  $staff_id) {
							$kpi_formula1 = '';
							$kpi_formula2 = '';
							foreach ($evaluate_result as $evaluate_result_value) {
								if($evaluate_result_value['assessor_id'] == $staff_id){
									$arr_result = json_decode($evaluate_result_value['result']);
									foreach ($arr_result as $arr_result_value) {
										if($arr_result_value->staff_id == $staff['staff_identifi']){
											$staff_info[] = $arr_result_value->$kpi_value ;
											$formula = $arr_kpi_formula[$kpi_key];
											if($arr_result_value->$kpi_value != ''){
												$result_value = $arr_result_value->$kpi_value;
												}else{
												$result_value = 0;
											}
											$formula = str_replace($kpi_value,$result_value,$formula);
											$formula = eval('return '.$formula.';');
											$kpi_formula2 .= (($formula*$percent[$emp_marks_key]/100)/$arr_income[$kpi_key])*$arr_kpi_percent[$kpi_key]/100;
											$kpi_temp += (float)eval('return '.$kpi_formula2.';');
										}
									}
									if($arrstaff != false){
										if(count($staff_info) == 3){
											$flag_member_evaluate = 1;
										}
									}
								}
							}
						}
						$staff_info[] = number_format($kpi_temp, 3);
						$kpi_staff += $kpi_temp;
					}
				}
				if($arrstaff != false && $flag_member_evaluate == 1){
					$member_evaluate = [];
					array_push($arr_object, $member_evaluate);
					}else{
					$staff_info[] = number_format($kpi_staff, 3);
					array_push($arr_object, $staff_info);
				}
			}
			return $arr_object;
		}
		/**
			* add attachment to database
			* @param integer  $rel_id     
			* @param string  $rel_type   
			* @param string  $attachment 
			* @param integer $insert_id
		*/
		public function add_attachment_to_database($rel_id, $rel_type, $attachment, $external = false)
		{
			$data['dateadded'] = date('Y-m-d H:i:s');
			$data['rel_id']    = $rel_id;
			if (!isset($attachment[0]['staffid'])) {
				$data['staffid'] = get_staff_user_id();
				} else {
				$data['staffid'] = $attachment[0]['staffid'];
			}
			if (isset($attachment[0]['task_comment_id'])) {
				$data['task_comment_id'] = $attachment[0]['task_comment_id'];
			}
			$data['rel_type'] = $rel_type;
			if (isset($attachment[0]['contact_id'])) {
				$data['contact_id']          = $attachment[0]['contact_id'];
				$data['visible_to_customer'] = 1;
				if (isset($data['staffid'])) {
					unset($data['staffid']);
				}
			}
			$data['attachment_key'] = app_generate_hash();
			if ($external == false) {
				$data['file_name'] = $attachment[0]['file_name'];
				$data['filetype']  = $attachment[0]['filetype'];
				} else {
				$path_parts            = pathinfo($attachment[0]['name']);
				$data['file_name']     = $attachment[0]['name'];
				$data['external_link'] = $attachment[0]['link'];
				$data['filetype']      = !isset($attachment[0]['mime']) ? get_mime_by_extension('.' . $path_parts['extension']) : $attachment[0]['mime'];
				$data['external']      = $external;
				if (isset($attachment[0]['thumbnailLink'])) {
					$data['thumbnail_link'] = $attachment[0]['thumbnailLink'];
				}
			}
			$this->db->insert(db_prefix() . 'files', $data);
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		/**
			* function get file for hrm staff
			* @param  integer  $id     
			* @param  boolean $rel_id 
			* @return object          
		*/
		public function get_file($id, $rel_id = false)
		{
			if (is_client_logged_in()) {
				$this->db->where('visible_to_customer', 1);
			}
			$this->db->where('id', $id);
			$file = $this->db->get('tblfiles')->row();
			if ($file && $rel_id) {
				if ($file->rel_id != $rel_id) {
					return false;
				}
			}
			return $file;
		}
		/**
			* delete staff attchement
			* @param  integer $attachment_id 
			* @return integer                
		*/
		public function delete_hr_profile_staff_attachment($attachment_id)
		{
			$deleted    = false;
			$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
			if ($attachment) {
				if (empty($attachment->external)) {
					unlink(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id.'/'.$attachment->file_name);
				}
				$this->db->where('id', $attachment->id);
				$this->db->delete(db_prefix() . 'files');
				if ($this->db->affected_rows() > 0) {
					$deleted = true;
					log_activity('Contract Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
				}
				if (is_dir(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id)) {
					$other_attachments = list_files(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id);
					if (count($other_attachments) == 0) {
						delete_dir(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id);
					}
				}
			}
			return $deleted;
		}
		/**
			* get hr profile attachments delete
			* @param  integer $id 
			* @return object     
		*/
		public function get_hr_profile_attachments_delete($id){
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'files')->row();
			}
		}
		/**
			* update staff permissions
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function update_staff_permissions($data){
			if($this->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $data['id'])) {
				$affectedRows++;
			}
			if ($affectedRows > 0) {
				hooks()->do_action('staff_member_updated', $data['id']);
				log_activity('Staff Member Updated [ID: ' . $data['id'] . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
				return true;
			}
			return false;
		}
		/**
			* update permissions
			* @param  array $permissions 
			* @param  integer $id          
			* @return boolean              
		*/
		public function update_permissions($permissions, $id)
		{
			$this->db->where('staff_id', $id);
			$this->db->delete('staff_permissions');
			$is_staff_member = is_staff_member($id);
			foreach ($permissions as $feature => $capabilities) {
				foreach ($capabilities as $capability) {
					if ($feature == 'leads' && !$is_staff_member) {
						continue;
					}
					$this->db->insert('staff_permissions', ['staff_id' => $id, 'feature' => $feature, 'capability' => $capability]);
				}
			}
			return true;
		}
		/**
			* get file info
			* @param  integer $id       
			* @param  string $rel_type 
			* @return object           
		*/
		public function get_file_info($id,$rel_type){
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', $rel_type);
			return $this->db->get(db_prefix().'files')->row();
		}
		/**
			* update staff profile
			* @param  array $data 
			* @return boolean       
		*/
		public function update_staff_profile($data){
			$id = $data['id'];
			unset($data['id']);
			$data['date_update']          = date('Y-m-d');
			$data['birthday']             = to_sql_date($data['birthday']);
			$data['days_for_identity']    = to_sql_date($data['days_for_identity']);
			if (isset($data['fakeusernameremembered'])) {
				unset($data['fakeusernameremembered']);
			}
			if (isset($data['fakepasswordremembered'])) {
				unset($data['fakepasswordremembered']);
			}
			if (isset($data['nationality'])) {
				unset($data['nationality']);
			}
			$data = hooks()->apply_filters('before_update_staff_member', $data, $id);
			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
					} else {
					if ($id != get_staff_user_id()) {
						if ($id == 1) {
							return [
							'cant_remove_main_admin' => true,
							];
						}
						} else {
						return [
						'cant_remove_yourself_from_admin' => true,
						];
					}
					$data['admin'] = 0;
				}
			}
			$affectedRows = 0;
			if (isset($data['departments'])) {
				$departments = $data['departments'];
				unset($data['departments']);
			}
			$permissions = [];
			if (isset($data['permissions'])) {
				$permissions = $data['permissions'];
				unset($data['permissions']);
			}
			if (isset($data['custom_fields'])) {
				$custom_fields = $data['custom_fields'];
				if (handle_custom_fields_post($id, $custom_fields)) {
					$affectedRows++;
				}
				unset($data['custom_fields']);
			}
			if (!isset($data['password'])) {
				unset($data['password']);
				} else {
				$data['password']             = app_hash_password($data['password']);
				$data['last_password_change'] = date('Y-m-d H:i:s');
			}
			if (isset($data['two_factor_auth_enabled'])) {
				$data['two_factor_auth_enabled'] = 1;
				} else {
				$data['two_factor_auth_enabled'] = 0;
			}
			if (isset($data['is_not_staff'])) {
				$data['is_not_staff'] = 1;
				} else {
				$data['is_not_staff'] = 0;
			}
			if (isset($data['admin']) && $data['admin'] == 1) {
				$data['is_not_staff'] = 0;
			}
			if(isset($data['year_requisition'])){
				unset($data['year_requisition']);
			}
			// First check for all cases if the email exists.
			$this->db->where('email', $data['email']);
			$email = $this->db->get(db_prefix() . 'staff')->row();
			if ($email) {
				// sdie('Email already exists');
			}
			$data['admin'] = 0;
			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
				}
			}
			$send_welcome_email = true;
			$original_password  = $data['password'];
			if (!isset($data['send_welcome_email'])) {
				$send_welcome_email = false;
				} else {
				unset($data['send_welcome_email']);
			}
			if ($data['admin'] == 1) {
				$data['is_not_staff'] = 0;
			}
			$data['email_signature'] = nl2br_save_html($data['email_signature']);
			$this->load->model('departments_model');
			$staff_departments = $this->departments_model->get_staff_departments($id);
			if (sizeof($staff_departments) > 0) {
				if (!isset($data['departments'])) {
					$this->db->where('staffid', $id);
					$this->db->delete(db_prefix() . 'staff_departments');
					} else {
					foreach ($staff_departments as $staff_department) {
						if (isset($departments)) {
							if (!in_array($staff_department['departmentid'], $departments)) {
								$this->db->where('staffid', $id);
								$this->db->where('departmentid', $staff_department['departmentid']);
								$this->db->delete(db_prefix() . 'staff_departments');
								if ($this->db->affected_rows() > 0) {
									$affectedRows++;
								}
							}
						}
					}
				}
				if (isset($departments)) {
					foreach ($departments as $department) {
						$this->db->where('staffid', $id);
						$this->db->where('departmentid', $department);
						$_exists = $this->db->get(db_prefix() . 'staff_departments')->row();
						if (!$_exists) {
							$this->db->insert(db_prefix() . 'staff_departments', [
							'staffid'      => $id,
							'departmentid' => $department,
							]);
							if ($this->db->affected_rows() > 0) {
								$affectedRows++;
							}
						}
					}
				}
				} else {
				if (isset($departments)) {
					foreach ($departments as $department) {
						$this->db->insert(db_prefix() . 'staff_departments', [
						'staffid'      => $id,
						'departmentid' => $department,
						]);
						if ($this->db->affected_rows() > 0) {
							$affectedRows++;
						}
					}
				}
			}
			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'staff', $data);
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			/*update avatar end*/
			if ($this->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $permissions), $id)) {
				$affectedRows++;
			}
			if ($affectedRows > 0) {
				hooks()->do_action('staff_member_updated', $id);
				log_activity('Staff Member Updated [ID: ' . $id . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
				return true;
			}
			return false;
		}
		/**
			* get staff in deparment
			* @param  integer $department_id 
			* @return integer                
		*/
		public function get_staff_in_deparment($department_id)
		{
			$data = [];
			$sql = 'select 
			departmentid 
			from    (select * from '.db_prefix().'departments
			order by '.db_prefix().'departments.parent_id, '.db_prefix().'departments.departmentid) departments_sorted,
			(select @pv := '.$department_id.') initialisation
			where   find_in_set(parent_id, @pv)
			and     length(@pv := concat(@pv, ",", departmentid)) OR departmentid = '.$department_id.'';
			$result_arr = $this->db->query($sql)->result_array();
			foreach ($result_arr as $key => $value) {
				$data[$key] = $value['departmentid'];
			}
			return $data;
		}
		/**
			* get staff role
			* @param  [type] $staff_id 
			* @return [type]           
		*/
		public function get_staff_role($staff_id){
			return $this->db->query('select r.name
			from '.db_prefix().'staff as s 
			left join '.db_prefix().'roles as r on r.roleid = s.role
			where s.staffid ='.$staff_id)->row();
		}
		/**
			* delete hr profile permission
			* @param  [type] $id 
			* @return [type]     
		*/
		public function delete_hr_profile_permission($id)
		{
			$str_permissions ='';
			foreach (list_hr_profile_permisstion() as $per_key =>  $per_value) {
				if(strlen($str_permissions) > 0){
					$str_permissions .= ",'".$per_value."'";
					}else{
					$str_permissions .= "'".$per_value."'";
				}
			}
			$sql_where = " feature IN (".$str_permissions.") ";
			$this->db->where('staff_id', $id);
			$this->db->where($sql_where);
			$this->db->delete(db_prefix() . 'staff_permissions');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* get data dpm chart
			* @param  [type] $dpm 
			* @return [type]      
		*/
		public function get_data_dpm_chart($dpm)
		{
			$department =  $this->db->query('select s.staffid as id,s.job_position, s.phonenumber, s.staff_identifi, s.email as staff_email, s.team_manage as pid, s.firstname as name
			from tblstaff as s 
			left join tblstaff_departments as sd on sd.staffid = s.staffid
			left join tbldepartments d on d.departmentid = sd.departmentid where d.departmentid = "'.$dpm.'" and s.status_work != "inactivity"
			order by s.team_manage, s.staffid')->result_array();
			$dep_tree = array(); 
			$list_id = [];
			foreach ($department as $ds ) {
				$list_id[] = $ds['id'];
			}
			foreach ($department as $dep) {
				if($dep['pid'] == 0 ||  !in_array($dep['pid'], $list_id) ){
					$dpm = $this->getdepartment_name($dep['id']);
					$node = array();
					$node['name'] = $dep['name'];
					$node['staff_identifi'] = $dep['staff_identifi'];
					$node['identifi_icon'] = '"fa fa-qrcode"';
					$node['staff_email'] = $dep['staff_email'];
					$node['mail_icon'] = '"fa fa-envelope"';
					$node['dp_phonenumber'] = '"fa fa-phone"';
					$node['dp_user_icon'] = '"fa fa-user-o"';
					if($dep['job_position'] != null && $dep['job_position'] != 0){
						$node['job_position'] = $this->get_job_position($dep['job_position']);
						$node['job_position_url'] = admin_url('hrm/job_position_view_edit/'.$dep['job_position']);
						}else{
						$node['job_position'] = '';
						$node['job_position_url'] = '';
					}
					if($dep['phonenumber'] != null){
						$node['phonenumber'] = $dep['phonenumber'];
						}else{
						$node['phonenumber'] = '';
					}
					if($dpm->name != null){
						$node['departmentname'] = $dpm->name;
						$node['dp_icon'] = '"fa fa-sitemap"';
						}else{
						$node['departmentname'] = '';
					}
					$node['image'] = staff_profile_image($dep['id'], [
					'staff-profile-image-small staff-chart-padding',
					]);
					$node['children'] = $this->get_child_node_staff_dpm_chart($dep['id'], $department);
					$dep_tree[] = $node;
				}        
			}   
			return $dep_tree;
		}
		/**
			* list job department
			* @param  [type] $department 
			* @return [type]             
		*/
		public function list_job_department($department){
			$this->db->select('staffid');
			$this->db->where('departmentid', $department);
			$arr_staff_id = [];
			$arr_staff = $this->db->get(db_prefix().'staff_departments')->result_array();
			$index_dep = 0;
			if(count($arr_staff) > 0){
				foreach ($arr_staff as $value) {
					if(!in_array($value['staffid'], $arr_staff_id)){
						$arr_staff_id[$index_dep] = $value['staffid'];
						$index_dep++;
					}                
				}
			}
			$rs = [];
			if(count($arr_staff_id) > 0){
				$arr_staff_id = implode(",", $arr_staff_id);
				$sql_where = 'SELECT '.db_prefix().'hr_job_position.position_id, position_name FROM '.db_prefix().'staff left join '.db_prefix().'hr_job_position on '.db_prefix().'staff.job_position = '.db_prefix().'hr_job_position.position_id WHERE '.db_prefix().'staff.job_position != "0" AND '.db_prefix().'staff.staffid IN ('.$arr_staff_id.')';
				$arr_job_position = $this->db->query($sql_where)->result_array();
				$arr_check_exist=[];
				foreach ($arr_job_position as $k => $note) {
					if(!in_array($note['position_id'], $arr_check_exist)){
						$rs[] = $note['position_id'];
						$arr_check_exist[$k] = $note['position_id'];
					}
				}
			}
			return $rs;
		}
		/**
			* delete hr job position attachment file
			* @param  [type] $attachment_id 
			* @return [type]                
		*/
		public function delete_hr_job_position_attachment_file($attachment_id)
		{
			$deleted    = false;
			$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
			if ($attachment) {
				if (empty($attachment->external)) {
					unlink(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id.'/'.$attachment->file_name);
				}
				$this->db->where('id', $attachment->id);
				$this->db->delete(db_prefix() . 'files');
				if ($this->db->affected_rows() > 0) {
					$deleted = true;
					log_activity('job_position Attachment Deleted [job_positionID: ' . $attachment->rel_id . ']');
				}
				if (is_dir(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id)) {
					// if (is_dir(get_upload_path_by_type('job_position') . $attachment->rel_id)) {
					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id);
					}
				}
			}
			return $deleted;
		}
		/**
			* get hrm profile file
			* @param  [type] $rel_id   
			* @param  [type] $rel_type 
			* @return [type]           
		*/
		public function get_hr_profile_file($rel_id, $rel_type){
			$this->db->order_by('dateadded', 'desc');
			$this->db->where('rel_id', $rel_id);
			$this->db->where('rel_type', $rel_type);
			return $this->db->get(db_prefix() . 'files')->result_array();
		}
		/**
			* get job position training de
			* @param  boolean $id 
			* @return [type]      
		*/
		public function get_job_position_training_de($id = false){
			$this->db->where('training_process_id', $id);
			return  $this->db->get(db_prefix() . 'hr_jp_interview_training')->row();
		}
		/**
			* delete job position training process
			* @param  [type] $trainingid 
			* @return [type]             
		*/
		public function delete_job_position_training_process($trainingid){
			//delete general info
			$this->db->where('training_process_id', $trainingid);
			$this->db->delete(db_prefix().'hr_jp_interview_training');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete position training
			* @param  [type] $trainingid 
			* @return [type]             
		*/
		public function delete_position_training($trainingid)
		{
			$affectedRows = 0;
			$this->db->where('training_id', $trainingid);
			$this->db->delete(db_prefix().'hr_position_training');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
				// get all questions from the survey
				$this->db->where('rel_id', $trainingid);
				$this->db->where('rel_type', 'position_training');
				$questions = $this->db->get(db_prefix().'hr_position_training_question_form')->result_array();
				// Delete the question boxes
				foreach ($questions as $question) {
					$this->db->where('questionid', $question['questionid']);
					$this->db->delete(db_prefix().'hr_p_t_form_question_box');
					$this->db->where('questionid', $question['questionid']);
					$this->db->delete(db_prefix().'hr_p_t_form_question_box_description');
				}
				$this->db->where('rel_id', $trainingid);
				$this->db->where('rel_type', 'position_training');
				$this->db->delete(db_prefix().'hr_position_training_question_form');
				$this->db->where('rel_id', $trainingid);
				$this->db->where('rel_type', 'position_training');
				$this->db->delete(db_prefix().'hr_p_t_form_results');
				$this->db->where('trainingid', $trainingid);
				$this->db->delete(db_prefix().'hr_p_t_surveyresultsets');
			}
			if ($affectedRows > 0) {
				log_activity('Training Deleted [ID: ' . $trainingid . ']');
				return true;
			}
			return false;
		}
		/**
			* get list position training by id training
			* @param  array $training_id_aray 
			* @return array                   
		*/
		public function get_list_position_training_by_id_training($training_id_aray){
			return $this->db->query('select * from '.db_prefix().'hr_position_training where training_id in ('.$training_id_aray.')')->result_array();
		}
		/**
			* get contract
			* @param  integer $id 
			* @return array     
		*/
		public function get_contract($id){
			if (is_numeric($id)) {
				$this->db->where('id_contract', $id);
				return $this->db->get(db_prefix() . 'hr_staff_contract')->row();
			}
			if ($id == false) {
				return  $this->db->get(db_prefix() . 'hr_staff_contract')->result_array();
			}
		}
		/**
			* get contract detail
			* @param  integer $id 
			* @return array     
		*/
		public function get_contract_detail($id){
			$staff_contract_detail = $this->db->query('select * from '.db_prefix().'hr_staff_contract_detail where staff_contract_id = '.$id)->result_array();
			return $staff_contract_detail;
		}
		/**
			* add contract
			* @param array $data 
		*/
		public function add_contract($data){
			$data['start_valid']    = to_sql_date($data['start_valid']);
			$data['end_valid']      = to_sql_date($data['end_valid']);
			$data['sign_day']       = to_sql_date($data['sign_day']);
			if(isset($data['job_position'])){
				$job_position = $data['job_position'];
				unset($data['job_position']);
			}
			if (isset($data['staff_contract_hs'])) {
				$staff_contract_hs = $data['staff_contract_hs'];
				unset($data['staff_contract_hs']);
			}
			$this->db->insert(db_prefix() . 'hr_staff_contract', $data);
			$insert_id = $this->db->insert_id();
			if(isset($staff_contract_hs)){
				$staff_contract_detail = json_decode($staff_contract_hs);
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'type';
				$header[] = 'rel_type';
				$header[] = 'rel_value';
				$header[] = 'since_date';
				$header[] = 'contract_note';
				foreach ($staff_contract_detail as $key => $value) {
					if($value[0] != ''){
						$es_detail[] = array_combine($header, $value);
					}
				}
			}
			if (isset($insert_id)) {
				/*insert detail*/
				foreach($es_detail as $key => $rqd){
					$es_detail[$key]['staff_contract_id'] = $insert_id;
				}
				$this->db->insert_batch(db_prefix().'hr_staff_contract_detail',$es_detail);
				/*update next number setting*/
				$this->update_prefix_number(['contract_code_number' =>  get_hr_profile_option('contract_code_number')+1]);
			}
			return $insert_id;
		}
		/**
			* update contract
			* @param  array $data 
			* @param  integer $id   
			* @return boolean       
		*/
		public function update_contract($data, $id)
		{   
			$affectedRows = 0;
			$data['start_valid']    = to_sql_date($data['start_valid']);
			$data['end_valid']      = to_sql_date($data['end_valid']);
			$data['sign_day']       = to_sql_date($data['sign_day']);
			if(isset($data['job_position'])){
				$job_position = $data['job_position'];
				unset($data['job_position']);
			}
			if (isset($data['staff_contract_hs'])) {
				$staff_contract_hs = $data['staff_contract_hs'];
				unset($data['staff_contract_hs']);
			}
			$this->db->where('id_contract', $id);
			$this->db->update(db_prefix() . 'hr_staff_contract', $data);
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			if(isset($staff_contract_hs)){
				$staff_contract_detail = json_decode($staff_contract_hs);
				$es_detail = [];
				$row = [];
				$rq_val = [];
				$header = [];
				$header[] = 'type';
				$header[] = 'rel_type';
				$header[] = 'rel_value';
				$header[] = 'since_date';
				$header[] = 'contract_note';
				$header[] = 'contract_detail_id';
				$header[] = 'staff_contract_id';
				foreach ($staff_contract_detail as $key => $value) {
					if($value[0] != ''){
						$es_detail[] = array_combine($header, $value);
					}
				}
			}
			$row = [];
			$row['update'] = []; 
			$row['insert'] = []; 
			$row['delete'] = [];
			$total = [];
			$total['total_amount'] = 0;
			foreach ($es_detail as $key => $value) {
				if($value['contract_detail_id'] != ''){
					$row['delete'][] = $value['contract_detail_id'];
					$row['update'][] = $value;
					}else{
					unset($value['contract_detail_id']);
					$value['staff_contract_id'] = $id;
					$row['insert'][] = $value;
				}
			}
			if(empty($row['delete'])){
				$row['delete'] = ['0'];
			}
			$row['delete'] = implode(",",$row['delete']);
			$this->db->where('contract_detail_id NOT IN ('.$row['delete'] .') and staff_contract_id ='.$id);
			$this->db->delete(db_prefix().'hr_staff_contract_detail');
			if($this->db->affected_rows() > 0){
				$affectedRows++;
			}
			if(count($row['insert']) != 0){
				$this->db->insert_batch(db_prefix().'hr_staff_contract_detail', $row['insert']);
				if($this->db->affected_rows() > 0){
					$affectedRows++;
				}
			}
			if(count($row['update']) != 0){
				$this->db->update_batch(db_prefix().'hr_staff_contract_detail', $row['update'], 'contract_detail_id');
				if($this->db->affected_rows() > 0){
					$affectedRows++;
				}
			}
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete contract
			* @param  integer $id 
			* @return boolean     
		*/
		public function delete_contract($id){
			$affectedRows = 0;
			$staff_name='';
			$staff_id='';
			$staff_contract_id=$id;
			$staff_contract = $this->get_contract($id);
			if($staff_contract){
				$staff_name .=  get_staff_full_name($staff_contract->staff);
				$staff_id .= $staff_contract->staff;
			}
			$this->db->where('staff_contract_id', $id);
			$this->db->delete(db_prefix() . 'hr_staff_contract_detail');
			if($this->db->affected_rows() > 0){
				$affectedRows++;
			}
			$this->db->where('id_contract', $id);
			$this->db->delete(db_prefix() . 'hr_staff_contract');
			if($this->db->affected_rows() > 0){
				$affectedRows++;
			}
			//delete atachement file
			$hr_contract_file = $this->get_hr_profile_file($id, 'hr_contract');
			foreach ($hr_contract_file as $file_key => $file_value) {
				$this->delete_hr_contract_attachment_file($file_value['id']);
			}
			/*write log delete contract*/
			log_activity('Staff Contract Deleted [ID Contract: ' . $staff_contract_id . ', ' . $staff_name . ' - ' . $staff_id . ' Deleted by '. get_staff_full_name(get_staff_user_id()). ' - '.get_staff_user_id().' ]', date('Y-m-d H:i:s'), get_staff_full_name(get_staff_user_id()));
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		/**
			* get contracttype by id
			* @param  [type] $id 
			* @return [type]     
		*/
		public function get_contracttype_by_id($id){
			return $this->db->query('select * from '.db_prefix().'hr_staff_contract_type where id_contracttype = '.$id)->result_array();
		}
		/**
			* get staff active
			* @return array 
		*/
		public function get_staff_active()
		{
			$staff = $this->db->query('select * from '.db_prefix().'staff as s where s.active = "1"  order by s.staffid')->result_array();
			return $staff;
		}
		/**
			* get staff active has contract
			* @return array 
		*/
		public function get_staff_active_has_contract()
		{
			$where = '(select count(*) from '.db_prefix().'hr_staff_contract where staff = '.db_prefix().'staff.staffid and start_valid <="'.date('Y-m-d').'" and IF(end_valid != null, end_valid >="'.date('Y-m-d').'",1=1)) > 0 and (status_work="working" OR status_work="maternity_leave") and active=1';
			$this->db->where($where);
			return $this->db->get(db_prefix().'staff')->result_array();
		}
		/**
			*  update prefix number
			* @param  [type] $data 
			* @return [type]       
		*/
		public function update_prefix_number($data)
		{
			$affected_rows=0;
			foreach ($data as $key => $value) {
				$this->db->where('option_name',$key);
				$this->db->update(db_prefix() . 'hr_profile_option', [
				'option_val' => $value,
				]);
				if ($this->db->affected_rows() > 0) {
					$affected_rows++;
				}
			}
			if($affected_rows > 0){
				return true;
				}else{
				return false;
			}
		}
		/**
			* create code
			* @param  [type] $rel_type 
			* @return [type]           
		*/
		public function create_code($rel_type) {
			//rel_type: position_code, staff_contract, ...
			$str_result ='';
			$prefix_str ='';
			switch ($rel_type) {
				case 'position_code':
				$prefix_str .= get_hr_profile_option('job_position_prefix');
				$next_number = (int) get_hr_profile_option('job_position_number');
				$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
				break;
				case 'staff_contract_code':
				$prefix_str .= get_hr_profile_option('contract_code_prefix');
				$next_number = (int) get_hr_profile_option('contract_code_number');
				$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('M-Y');
				break;
				case 'staff_code':
				$prefix_str .= get_hr_profile_option('staff_code_prefix');
				$next_number = (int) get_hr_profile_option('staff_code_number');
				$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
				break;
				default:
				# code...
				break;
			}
			return $str_result;
		}
		/**
			* check department format
			* @param  [type] $department 
			* @return [type]             
		*/
		public function check_department_format($departments)
		{
			$str_error = '';
			$department = [];
			$arr_department = explode(',', $departments);
			for ($i = 0; $i < count($arr_department); $i++) {
				$this->db->like(db_prefix() . 'departments.departmentid', $arr_department[$i]);
				$department_value = $this->db->get(db_prefix() . 'departments')->row();
				if($department_value){
					$department[$i] = $department_value->departmentid;
					}else{
					$str_error .= $arr_department[$i].', ';
					return ['status' => false, 'result' => $str_error];
				}
			}
			return ['status' => true, 'result' => $department];
		}
		/**
			* get dependent person
			* @param  boolean $id 
			* @return [type]      
		*/
		public function get_dependent_person($id = false)
		{
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'hr_dependent_person')->row();
			}
			if ($id == false) {
				return  $this->db->get(db_prefix() . 'hr_dependent_person')->result_array();
			}
		}    
		/**
			* get dependent person bytstaff
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function get_dependent_person_bytstaff($staffid)
		{
			$this->db->where('staffid', $staffid);
			return $this->db->get(db_prefix() . 'hr_dependent_person')->result_array();
		}
		/**
			* add dependent person
			* @param [type] $data 
		*/
		public function add_dependent_person($data)
		{
			if(!isset($data['staffid'])){
				$data['staffid'] = get_staff_user_id();
			}
			$data['dependent_bir'] = to_sql_date($data['dependent_bir']);
			if(isset($data['start_month'])){
				$data['start_month'] = to_sql_date($data['start_month']);
			}
			if(isset($data['end_month'])){
				$data['end_month'] = to_sql_date($data['end_month']);
			}
			$this->db->insert(db_prefix().'hr_dependent_person', $data);
			$insert_id = $this->db->insert_id();
			if($insert_id){
				return $insert_id;
			}
			return false;
		}
		/**
			* update dependent person
			* @param  [type] $data 
			* @param  [type] $id   
			* @return [type]       
		*/
		public function update_dependent_person($data, $id)
		{   
			if(isset($data['start_month'])){
				$data['start_month'] = to_sql_date($data['start_month']);
			}
			if(isset($data['end_month'])){
				$data['end_month'] = to_sql_date($data['end_month']);
			}
			$this->db->where('id', $id);
			$data['dependent_bir'] = to_sql_date($data['dependent_bir']);
			$this->db->update(db_prefix() . 'hr_dependent_person', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete dependent person
			* @param  [type] $id 
			* @return [type]     
		*/
		public function delete_dependent_person($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'hr_dependent_person');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* update approval status
			* @param  [type] $data 
			* @return [type]       
		*/
		public function update_approval_dependent_person($data)
		{
			$data_obj['start_month'] = to_sql_date($data['start_month']);
			$data_obj['end_month'] = to_sql_date($data['end_month']);
			$data_obj['status_comment'] = $data['reason'];
			$data_obj['status'] = $data['status'];
			$this->db->where('id', $data['id']);
			$this->db->update(db_prefix() . 'hr_dependent_person',$data_obj);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* update approval status
			* @param  [type] $data 
			* @return [type]       
		*/
		public function update_approval_status($data){
			$data_obj['start_month'] = to_sql_date($data['start_month']);
			$data_obj['end_month'] = to_sql_date($data['end_month']);
			$data_obj['status_comment'] = $data['reason'];
			$data_obj['status'] = $data['status'];
			$this->db->where('id', $data['id']);
			$this->db->update(db_prefix() . 'hr_dependent_person',$data_obj);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* add resignation procedure
			* @param [type] $data 
		*/
		public function add_resignation_procedure($data)
		{
			$data['dateoff'] = to_sql_date($data['dateoff'], true);
			$data['staff_name'] = get_staff_full_name($data['staffid']);
			$staffid = $data['staffid'];
			$insert_id = $this->db->insert(db_prefix().'hr_list_staff_quitting_work', $data);
			if($insert_id){
				$asset = $this->get_data_asset($staffid);
				if(count($asset) > 0){
					$rel_id_asset = $this->add_data_of_staff_quit_work_by_id( _l('asset'));
					foreach ($asset as $key => $name) {
						if($rel_id_asset){
							$option_name_by_id = $this->add_data_of_staff_quit_work($rel_id_asset, $name['asset_name'], $staffid);
						}
					}
				}
				$department_staff = $this->departments_model->get_staff_departments($staffid);
				if(count($department_staff) > 0){
					$check = $this->check_department_on_procedure($department_staff[0]['departmentid']);
					}else{
					$check = '';
				}
				if($check != ''){
					$result = $this->get_procedure_retire($check);
					if(count($result) > 0){
						foreach ($result as $key => $name) {
							if($name['rel_name']){
								$rel_id = $this->add_data_of_staff_quit_work_by_id($name['rel_name'], $name['people_handle_id']);
								if($rel_id){
									$name['option_name'] = json_decode($name['option_name']);
									foreach ($name['option_name'] as $option) {
										$option_name_by_id = $this->add_data_of_staff_quit_work($rel_id, $option, $staffid);
									}
								}
								$people_handle_id = $name['people_handle_id'];
								$staffid_user = get_staff_user_id();
								$subject = get_staff_full_name($staffid);
								$link = 'hr_profile/resignation_procedures?detail='.$staffid;
								if($people_handle_id != ''){
									if ($staffid_user != $people_handle_id) {
										$notification_data = [
										'description' => _l('hr_resignation_procedures_are_waiting_for_your_confirmation') .$subject,
										'touserid'    => $people_handle_id,
										'link'        => $link,
										];
										$notification_data['additional_data'] = serialize([
										$subject,
										]);
										if (add_notification($notification_data)) {
											pusher_trigger_notification([$people_handle_id]);
										}
									}
								}
							}
						}
					}
				}
				return $insert_id;
			}
			return false;        
		}
		/**
			* get data asset
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function get_data_asset($staffid)
		{
			$this->db->where('staff_id', $staffid);
			return $this->db->get(db_prefix().'hr_allocation_asset')->result_array();
		}
		/**
			* add data of staff quit work by id
			* @param [type] $rel_name         
			* @param string $people_handle_id 
		*/
		public function add_data_of_staff_quit_work_by_id($rel_name, $people_handle_id = '')
		{
			if($people_handle_id == ''){
				$people_handle_id = get_staff_user_id();
			}
			$this->db->insert(db_prefix().'hr_procedure_retire_of_staff_by_id',[
			'rel_name' => $rel_name,
			'people_handle_id' => $people_handle_id
			]);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				return $insert_id;
			}
			return false;        
		}
		/**
			* add data of staff quit work
			* @param [type] $rel_id      
			* @param [type] $option_name 
			* @param [type] $staffid     
		*/
		public function add_data_of_staff_quit_work($rel_id, $option_name, $staffid)
		{
			$insert_id = $this->db->insert(db_prefix().'hr_procedure_retire_of_staff',[
			'rel_id' => $rel_id,
			'option_name' => $option_name,
			'status' => 0,
			'staffid' => $staffid
			]);
			if ($insert_id) {
				return $insert_id;
			}
			return false;        
		}
		/**
			* get resignation procedure by staff
			* @param  [type] $staff_id 
			* @return [type]           
		*/
		public function get_resignation_procedure_by_staff($staff_id)
		{
			$this->db->where('staffid', $staff_id);
			$resignation_procedure = $this->db->get(db_prefix() . 'hr_list_staff_quitting_work')->row();
			return $resignation_procedure;
		}
		/**
			* delete procedures for quitting work
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function delete_procedures_for_quitting_work($staffid)
		{
			$affectedRows = 0;
			$this->db->where('staffid', $staffid);
			$this->db->delete(db_prefix() . 'hr_list_staff_quitting_work');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			$this->db->where('staffid', $staffid);
			$this->db->delete(db_prefix() . 'hr_procedure_retire_of_staff');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		/**
			* get data procedure retire of staff
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function get_data_procedure_retire_of_staff($staffid)
		{
			$this->db->select('a.id, a.staffid, a.rel_id, a.option_name, a.status, b.rel_name, b.people_handle_id');
			$this->db->from(db_prefix().'hr_procedure_retire_of_staff as a');
			$this->db->join(db_prefix().'hr_procedure_retire_of_staff_by_id as b','b.id = a.rel_id');
			$this->db->where('staffid', $staffid);
			return $this->db->get()->result_array();
		}
		/**
			* update status quit work
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function update_status_quit_work($staffid, $id)
		{
			$affectedRows = 0;
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'hr_list_staff_quitting_work', [
			'approval' => 'approved'
			]);
			if ($affectedRows > 0) {
				return true;
			}
			if($staffid){
				$this->db->where('staffid',$staffid);
				$this->db->update(db_prefix().'staff', [
				'active' => 0,
				'status_work' => 'inactivity'
				]);
				if ($affectedRows > 0) {
					return true;
				}
			}
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		/**
			* update status procedure retire of staff
			* @param  array  $where 
			* @return [type]        
		*/
		public function update_status_procedure_retire_of_staff($where =[])
		{
			$this->db->where($where);
			$this->db->update(db_prefix().'hr_procedure_retire_of_staff', [
			'status' => 1
			]);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		/**
			* delete hr q a attachment file
			* @param  [type] $attachment_id 
			* @return [type]                
		*/
		public function delete_hr_q_a_attachment_file($attachment_id)
		{
			$deleted    = false;
			$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
			if ($attachment) {
				if (empty($attachment->external)) {
					unlink(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id.'/'.$attachment->file_name);
				}
				$this->db->where('id', $attachment->id);
				$this->db->delete(db_prefix() . 'files');
				if ($this->db->affected_rows() > 0) {
					$deleted = true;
					log_activity('kb article files Attachment Deleted [job_positionID: ' . $attachment->rel_id . ']');
				}
				if (is_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id)) {
					// if (is_dir(get_upload_path_by_type('kb_article_files') . $attachment->rel_id)) {
					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
					}
				}
			}
			return $deleted;
		}
		/**
			* get salary allowance handsontable
			* @return [type] 
		*/
		public function get_salary_allowance_handsontable()
		{
			$salary_type        = _l('hr_salary_type');
			$allowance_type     = _l('hr_allowance_type');
			$salary_symbol      = 'st';
			$allowance_symbol   = 'al';
			$salary_types = $this->db->query('select CONCAT("'.$salary_symbol.'","_",form_id) as id, CONCAT("'.$salary_type.'",": ",form_name) as label from ' . db_prefix() . 'hr_salary_form ')->result_array();
			$allowance_types = $this->db->query('select CONCAT("'.$allowance_symbol.'","_",type_id) as id, CONCAT("'.$allowance_type.'",": ",type_name) as label from ' . db_prefix() . 'hr_allowance_type ')->result_array();
			return array_merge($salary_types, $allowance_types);
		}
		/**
			* delete hr contract attachment file
			* @param  [type] $attachment_id 
			* @return [type]               
		*/
		public function delete_hr_contract_attachment_file($attachment_id)
		{
			$deleted    = false;
			$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
			if ($attachment) {
				if (empty($attachment->external)) {
					unlink(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id.'/'.$attachment->file_name);
				}
				$this->db->where('id', $attachment->id);
				$this->db->delete(db_prefix() . 'files');
				if ($this->db->affected_rows() > 0) {
					$deleted = true;
					log_activity('staff_contract Attachment Deleted [staff_contractID: ' . $attachment->rel_id . ']');
				}
				if (is_dir(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id)) {
					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id);
					}
				}
			}
			return $deleted;
		}
		/**
			* get salary allowance for table
			* @param  [type] $contract_id 
			* @return [type]              
		*/
		public function get_salary_allowance_for_table($contract_id)
		{   
			$salary_allowance = '';
			$contract_details = $this->get_contract_detail($contract_id);
			if(count($contract_details) > 0){
				foreach ($contract_details as $key => $value) {
					$type_name ='';
					if(preg_match('/^st_/', $value['rel_type'])){
						$rel_value = str_replace('st_', '', $value['rel_type']);
						$salary_type = $this->get_salary_form($rel_value);
						$type = 'salary';
						if($salary_type){
							$type_name = $salary_type->form_name;
						}
						}elseif(preg_match('/^al_/', $value['rel_type'])){
						$rel_value = str_replace('al_', '', $value['rel_type']);
						$allowance_type = $this->get_allowance_type($rel_value);
						$type = 'allowance';
						if($allowance_type){
							$type_name = $allowance_type->type_name;
						}
					}
					$salary_allowance .= $type_name.': '. app_format_money($value['rel_value'],'').'('._l('hr_start_month').':'._d($value['since_date']).')'.'<br>';
				}
			}
			return $salary_allowance;
		}
		/**
			* send mail training
			* @param  [type] $email       
			* @param  [type] $sender_name 
			* @param  [type] $subject     
			* @param  [type] $body        
			* @return [type]              
		*/
		public function send_mail_training($email,$sender_name,$subject,$body){
			$staff_id = get_staff_user_id();
			$inbox = array();
			$inbox['to'] = $email;
			$inbox['sender_name'] = get_option('companyname');
			$inbox['subject'] = _strip_tags($subject);
			$inbox['body'] = _strip_tags($body);        
			$inbox['body'] = nl2br_save_html($inbox['body']);
			$inbox['date_received']      = date('Y-m-d H:i:s');
			if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0){
				$ci = &get_instance();
				$ci->email->initialize();
				$ci->load->library('email');    
				$ci->email->clear(true);
				$ci->email->from(get_option('smtp_email'), $inbox['sender_name']);
				$ci->email->to($inbox['to']);
				$ci->email->subject($inbox['subject']);
				$ci->email->message($inbox['body']);
				$ci->email->send(true);
			}
			return true;
		}
		/**
			* get board mark form
			* @param  [type] $rel_id 
			* @return [type]         
		*/
		public function get_board_mark_form($rel_id){
			$this->db->where('training_id',$rel_id);
			return $this->db->get(db_prefix().'hr_position_training')->row();
		}
		public function report_by_leave_statistics()
		{
			$months_report = $this->input->post('months_report');
			$custom_date_select = '';
			if ($months_report != '') {
				if (is_numeric($months_report)) {
					// Last month
					if ($months_report == '1') {
						$beginMonth = date('Y-m-01', strtotime('first day of last month'));
						$endMonth   = date('Y-m-t', strtotime('last day of last month'));
						} else {
						$months_report = (int) $months_report;
						$months_report--;
						$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
						$endMonth   = date('Y-m-t');
					}
					$custom_date_select = '(hrl.start_time BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
					} elseif ($months_report == 'this_month') {
					$custom_date_select = '(hrl.start_time BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
					} elseif ($months_report == 'this_year') {
					$custom_date_select = '(hrl.start_time BETWEEN "' .
					date('Y-m-d', strtotime(date('Y-01-01'))) .
					'" AND "' .
					date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
					} elseif ($months_report == 'last_year') {
					$custom_date_select = '(hrl.start_time BETWEEN "' .
					date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
					'" AND "' .
					date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
					} elseif ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date   = to_sql_date($this->input->post('report_to'));
					if ($from_date == $to_date) {
						$custom_date_select =  'hrl.start_time ="' . $from_date . '"';
						} else {
						$custom_date_select = '(hrl.start_time BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
					}
				}
			}
			$chart = [];
			$dpm = $this->departments_model->get();
			foreach($dpm as $d){
				$chart['categories'][] = $d['name'];
				$chart['sick_leave'][] = $this->count_type_leave($d['departmentid'],1,$custom_date_select);
				$chart['maternity_leave'][] = $this->count_type_leave($d['departmentid'],2,$custom_date_select);
				$chart['private_work_with_pay'][] = $this->count_type_leave($d['departmentid'],3,$custom_date_select);
				$chart['private_work_without_pay'][] = $this->count_type_leave($d['departmentid'],4,$custom_date_select);
				$chart['child_sick'][] = $this->count_type_leave($d['departmentid'],5,$custom_date_select);
				$chart['power_outage'][] = $this->count_type_leave($d['departmentid'],6,$custom_date_select);
				$chart['meeting_or_studying'][] = $this->count_type_leave($d['departmentid'],7,$custom_date_select);
			}
			return $chart;
		}
		/**
			* get list quiting work
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function get_list_quiting_work($staffid){
			if($staffid != ''){
				$this->db->where('staffid', $staffid);
				return $this->db->get(db_prefix().'hr_list_staff_quitting_work')->row();
				}else{
				return $this->db->get(db_prefix().'hr_list_staff_quitting_work')->result_array();
			}
		}
		/**
			* get staff by _month
			* @param  [type] $from_month 
			* @param  [type] $to_month   
			* @return [type]             
		*/
		public function get_staff_by_month($from_month, $to_month)
		{
			return $this->db->query('select * from '.db_prefix().'staff where datecreated between \''.$from_month.'\' and \''.$to_month.'\'')->result_array();
		}
		/**
			* get dstafflist by year
			* @param  [type] $year  
			* @param  [type] $month 
			* @return [type]        
		*/
		public function get_dstafflist_by_year($year,$month)
		{
			return $this->db->query('select * from '.db_prefix().'staff where year(datecreated) = \''.$year.'\' and month(datecreated) >= \''.$month.'\' and staffid not in (select staffid from '.db_prefix().'hr_list_staff_quitting_work)')->result_array();
		}
		/**
			* get staff by department id and time
			* @param  [type] $id_department 
			* @param  [type] $from_time     
			* @param  [type] $to_time       
			* @return [type]                
		*/
		public function get_staff_by_department_id_and_time($id_department, $from_time, $to_time)
		{
			$format_from_date = preg_replace('/\//','-', $from_time); 
			$format_to_date = preg_replace('/\//','-', $to_time);
			$start_date = strtotime(date_format(date_create($format_from_date),"Y/m/d"));
			$end_date = strtotime(date_format(date_create($format_to_date),"Y/m/d"));
			$list_staff = $this->db->query('select * from '.db_prefix().'staff where staffid in (SELECT staffid FROM '.db_prefix().'staff_departments where departmentid = '.$id_department.')')->result_array();
			$list_id_staff = [];
			$list_id=[];
			foreach ($list_staff as $key => $value) {
				$list_staff_contract = $this->db->query('select * from '.db_prefix().'hr_staff_contract where staff = '.$value['staffid'].'')->result_array();
				$min = 9999999999;
				$max = 0;
				foreach ($list_staff_contract as $key => $item_contract) {
					$format_date1 = preg_replace('/\//','-', $item_contract['start_valid']); 
					$date = date_format(date_create($format_date1),"Y/m/d");                                                                 
					$start_date = strtotime($date);
					if($start_date < $min){
						$min = $start_date;
					}
					$format_date2 = preg_replace('/\//','-', $item_contract['end_valid']); 
					$date = date_format(date_create($format_date2),"Y/m/d");                     
					$to_date = strtotime($date);
					if($to_date > $max){
						$max = $to_date;
					}
				}
				if(($min >= $start_date)&&($min <= $end_date)){
					$list_id[] = $value['staffid'];
				}
				else{
					if(($max>=$end_date)&&($max<=$end_date)){
						$list_id[] = $value['staffid'];
					}
				}
			}
			$implode = '0';
			if(isset($list_id)){
				if(count($list_id)>0){
					$implode = implode(',', $list_id);
				}
			}
			return $this->db->query('SELECT * FROM '.db_prefix().'staff where staffid in ('.$implode.')')->result_array();
		}
		/**
			* get department by list id
			* @param  string $list_id 
			* @return [type]          
		*/
		public function get_department_by_list_id($list_id = '')
		{
			if($list_id==''){
				return $this->db->query('select * from '.db_prefix().'departments')->result_array();
			}
			else{
				return $this->db->query('select * from '.db_prefix().'departments where departmentid in ('.$list_id.')')->result_array();
			}
		}
		/**
			* get list contract detail staff
			* @param  [type] $staffid 
			* @return [type]          
		*/
		public function get_list_contract_detail_staff($staffid)
		{
			$this->db->where('staff', $staffid);
			$this->db->order_by('start_valid', 'desc');
			$this->db->limit(2);
			$staff_contracts = $this->db->get(db_prefix() . 'hr_staff_contract')->result_array();
			if(count($staff_contracts) == 2){
				$new_salary=0;
				$old_salary=0;
				$staff_contract_ids = [];
				foreach ($staff_contracts as $key => $staff_contract) {
					if($key == 0){
						$date_effect = $staff_contract['start_valid'];
					}
					array_push($staff_contract_ids, $staff_contract['id_contract']);
				}
				$this->db->select('sum(rel_value) as rel_value, staff_contract_id');
				$sql_where = 'staff_contract_id IN ("'.implode('", "', $staff_contract_ids).'")';
				$this->db->where($sql_where);
				$this->db->group_by('staff_contract_id');
				$staff_contract_details = $this->db->get(db_prefix().'hr_staff_contract_detail')->result_array();
				$contract_detail=[];
				foreach ($staff_contract_details as $d_key => $staff_contract_detail) {
					$contract_detail[$staff_contract_detail['staff_contract_id']] = $staff_contract_detail['rel_value'];
				}
				foreach ($staff_contract_ids as $key => $value) {
					if($key == 0){
						//new
						if(isset($contract_detail[$value])){
							$new_salary = $contract_detail[$value];
						}
						}else{
						//old
						if(isset($contract_detail[$value])){
							$old_salary = $contract_detail[$value];
						}
					}
				}
				$result_array=[];
				$result_array['new_salary']=$new_salary;
				$result_array['old_salary']=$old_salary;
				$result_array['date_effect']=$date_effect;
				$result_array;
				return $result_array;
				}else{
				return false;
			}
		}
		/**
			* get list staff by year
			* @param  [type] $year 
			* @return [type]       
		*/
		public function get_list_staff_by_year($year)
		{
			return $this->db->query('select * from '.db_prefix().'staff where year(datecreated) = \''.$year.'\' and staffid not in (select staffid from '.db_prefix().'hr_list_staff_quitting_work)')->result_array();
		}
		/**
			* count staff by department literacy
			* @param  string $department_ids 
			* @return [type]                 
		*/
		public function count_staff_by_department_literacy($department_ids='')
		{
			$result =[];
			$this->db->select('count(staffdepartmentid) as total_staff, departmentid, literacy');
			if($department_ids != ''){
				$sql_where = db_prefix().'staff_departments.departmentid in ('.$department_ids.')';
				$this->db->where($sql_where);
			}
			$this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid = ' . db_prefix() . 'staff_departments.staffid', 'left');
			$this->db->group_by('departmentid, literacy');
			$this->db->order_by('departmentid', 'asc');
			$staff_departments = $this->db->get(db_prefix().'staff_departments')->result_array();
			$department_id= 0;
			$temp=[];
			foreach ($staff_departments as $key => $value) {
				if($value['literacy'] != ''){
					$temp[$value['literacy']] = $value['total_staff'];
					if(count($staff_departments) != $key+1){
						if($value['departmentid'] != $staff_departments[$key+1]['departmentid']){
							$result[$value['departmentid']] = $temp;
							$temp=[];
						}
						}else{
						$result[$value['departmentid']] = $temp;
					}
				}
			}
			return $result;
		}
		/**
			* report by staffs month
			* @param  [type] $from_date 
			* @param  [type] $to_date   
			* @return [type]            
		*/
		public function report_by_staffs_month($from_date, $to_date)
		{
			$new_staff_by_month = $this->report_new_staff_by_month($from_date, $to_date);
			$staff_working_by_month = $this->report_staff_working_by_month($from_date, $to_date);
			$staff_quit_work_by_month = $this->report_staff_quit_work_by_month($from_date, $to_date);
			for($_month = 1 ; $_month <= 12; $_month++){
				$month_t = date('m',mktime(0, 0, 0, $_month, 04, 2016));
				if($_month == 5){
					$chart['categories'][] = _l('month_05');
					}else{
					$chart['categories'][] = _l('month_'.$_month);
				}
				$chart['hr_new_staff'][] = isset($new_staff_by_month[$month_t]) ? $new_staff_by_month[$month_t] : 0;
				$chart['hr_staff_are_working'][] = isset($staff_working_by_month[$month_t]) ? $staff_working_by_month[$month_t] : 0;
				$chart['hr_staff_quit'][] = isset($staff_quit_work_by_month[$month_t]) ? $staff_quit_work_by_month[$month_t] : 0;
			}
			return $chart;
		}
		/**
			* [report_new_staff_by_month
			* @param  [type] $month 
			* @return [type]        
		*/
		public function report_new_staff_by_month($from_date ,$to_date)
		{
			$result =[];
			$this->db->select('count(staffid) as total_staff, date_format(datecreated, "%m") as datecreated');
			$sql_where = "date_format(datecreated, '%Y-%m-%d') >= '".$from_date."' AND date_format(datecreated, '%Y-%m-%d') <= '".$to_date."'";
			$this->db->where($sql_where);
			$this->db->group_by("date_format(datecreated, '%m')");
			$staffs = $this->db->get(db_prefix().'staff')->result_array();
			foreach ($staffs as $key => $value) {
				$result[$value['datecreated']] = (int)$value['total_staff'];
			}
			return $result;
		}
		/**
			* report staff working by month
			* @param  [type] $month 
			* @return [type]        
		*/
		public function report_staff_working_by_month($from_date ,$to_date)
		{
			$result =[];
			$this->db->select('count(staffid) as total_staff, date_format(datecreated, "%m") as datecreated');
			$sql_where = "date_format(datecreated, '%Y-%m-%d') >= '".$from_date."' AND date_format(datecreated, '%Y-%m-%d') <= '".$to_date."' AND status_work = 'working'";
			$this->db->where($sql_where);
			$this->db->group_by("date_format(datecreated, '%m')");
			$staffs = $this->db->get(db_prefix().'staff')->result_array();
			foreach ($staffs as $key => $value) {
				$result[$value['datecreated']] = (int)$value['total_staff'];
			}
			return $result;
		}
		/**
			* report staff quit work by month
			* @param  [type] $month 
			* @return [type]        
		*/
		public function report_staff_quit_work_by_month($from_date ,$to_date)
		{	
			$result =[];
			$this->db->select('count(id) as total_staff, date_format(dateoff, "%m") as datecreated');
			$sql_where = " date_format(dateoff, '%Y-%m') <= '".$to_date."'";
			$this->db->where($sql_where);
			$this->db->group_by("date_format(dateoff, '%m')");
			$quitting_works = $this->db->get(db_prefix().'hr_list_staff_quitting_work')->result_array();
			//
			$this->db->select('count(staffid) as total_staff, date_format(date_update, "%m") as datecreated');
			$sql_where1 = " status_work = 'inactivity' AND date_format(date_update, '%Y-%m') <= '".$to_date."' ";
			$this->db->where($sql_where1);
			$this->db->group_by("date_format(date_update, '%m')");
			$staffs = $this->db->get(db_prefix().'staff')->result_array();
			$arr_result =[];
			foreach ($quitting_works as $value) {
				if(isset($arr_result[$value['datecreated']])){
					$arr_result[$value['datecreated']] += (int)$value['total_staff'];
					}else{
					$arr_result[$value['datecreated']] = $value['total_staff'];
				}
			}
			foreach ($staffs as $value) {
				if(isset($arr_result[$value['datecreated']])){
					$arr_result[$value['datecreated']] += (int)$value['total_staff'];
					}else{
					$arr_result[$value['datecreated']] = $value['total_staff'];
				}
			}
			return $arr_result;
		}
		/**
			* hr get training question form by relid
			* @param  [type] $relid 
			* @return [type]        
		*/
		public function hr_get_training_question_form_by_relid($rel_id)
		{
			$this->db->where('rel_id', $rel_id);
			$training_question_forms = $this->db->get(db_prefix().'hr_position_training_question_form')->result_array();
			return $training_question_forms;
		}	
		/**
			* hr get form results by resultsetid
			* @param  [type] $resultsetid 
			* @return [type]              
		*/
		public function hr_get_form_results_by_resultsetid($resultsetid, $questionid)
		{
			$boxdescriptionids =[];
			$this->db->where('resultsetid', $resultsetid);
			$this->db->where('questionid', $questionid);
			$form_results = $this->db->get(db_prefix().'hr_p_t_form_results')->result_array();
			foreach ($form_results as $value) {
				array_push($boxdescriptionids, $value['boxdescriptionid']);
			}
			return $boxdescriptionids;
		}
		/**
			* delete hr article attachment file
			* @param  [type] $attachment_id 
			* @return [type]                
		*/
		public function delete_hr_article_attachment_file($attachment_id)
		{
			$deleted    = false;
			$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
			if ($attachment) {
				if (empty($attachment->external)) {
					unlink(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id.'/'.$attachment->file_name);
				}
				$this->db->where('id', $attachment->id);
				$this->db->delete(db_prefix() . 'files');
				if ($this->db->affected_rows() > 0) {
					$deleted = true;
					log_activity('kb_article_files Attachment Deleted [kb_article_filesID: ' . $attachment->rel_id . ']');
				}
				if (is_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id)) {
					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
					}
				}
			}
			return $deleted;
		}
		//end file
		public function headquarter_table_data($data){
			$this->db->select('*');
            $this->db->from(db_prefix() . 'headquarter');
            if($data['state_id'] !=''){
                $this->db->where('state_id',$data['state_id']);
			}
            $this->db->where('status',$data['status_id']);
            $this->db->order_by('name');
            $query = $this->db->get();
            return $query->result_array();
		}
		public function get_AccountDetails($AccountID)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
            $regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
            $this->db->select(db_prefix() . 'staff.*,'.db_prefix() . 'accountbalances.BAL1,'.db_prefix() . 'staff_departments.departmentid');
            $this->db->join(db_prefix() . 'staff_departments', db_prefix() . 'staff_departments.staffid = '.db_prefix() . 'staff.staffid ','LEFT');
            $this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = '.db_prefix() . 'staff.AccountID AND tblaccountbalances.PlantID = '.$selected_company.' AND tblaccountbalances.FY = '.$FY,'LEFT');
            $this->db->where(db_prefix() . 'staff.AccountID',$AccountID);
            $this->db->where(db_prefix() . 'staff.PlantID',$selected_company);
            $this->db->order_by(db_prefix() . 'staff.firstname', 'ASC');
            $result = $this->db->get('staff')->row();
            if($result){
                $CityList = $this->getCityList($result->state);
                $DesignationList = $this->job_position_by_id($result->departmentid);
                $result->Type = 'staff';
                $result->CityList = $CityList;
                $result->DesignationList = $DesignationList;
			}
			return $result;
		}
		public function GetAllStaffList()
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
            $this->db->select(db_prefix() . 'staff.*,'.db_prefix() . 'hr_job_position.position_name,'.db_prefix() . 'departments.name,'.db_prefix() . 'AccountSubGroup2.SubActGroupName');
            $this->db->join(db_prefix() . 'hr_job_position', db_prefix() . 'hr_job_position.position_id = '.db_prefix() . 'staff.job_position',"LEFT");
            $this->db->join(db_prefix() . 'staff_departments', db_prefix() . 'staff_departments.staffid = '.db_prefix() . 'staff.staffid ','LEFT');
            $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = '.db_prefix() . 'staff_departments.departmentid ','LEFT');
            $this->db->join(db_prefix() . 'AccountSubGroup2', db_prefix() . 'AccountSubGroup2.SubActGroupID = '.db_prefix() . 'staff.SubActGroupID ','LEFT');
            $this->db->where(db_prefix() . 'staff.PlantID',$selected_company);
            $this->db->where(db_prefix() . 'staff.admin','0');
            $this->db->order_by('tblstaff.firstname,tblstaff.lastname', 'ASC');
            $result = $this->db->get('staff')->result_array();
			return $result;
		}
		public function GetCityList($StateID) 
		{
			$this->db->select(db_prefix() . 'xx_citylist.*');
			$this->db->where(db_prefix() . 'xx_citylist.state_id', $StateID);
			$this->db->order_by(db_prefix() . 'xx_citylist.city_name', 'ASC');
			return $this->db->get('tblxx_citylist')->result_array();
		}
		public function getHeadQurterList($StateID)
		{
			$this->db->select(db_prefix() . 'headquarter.*');
			$this->db->where(db_prefix() . 'headquarter.state_id', $StateID);
			$this->db->order_by(db_prefix() . 'headquarter.name', 'ASC');
			return $this->db->get('tblheadquarter')->result_array();
		}
		public function staff_table_data($data){
			if($this->input->post('staff_type')){
				$where =array();
				$staff_type      = $this->input->post('staff_type');
				foreach ($staff_type as $staff_id) {
					if($staff_id != '')
					{
						array_push($where, $staff_id);
					}
				}
			}
			$staff_type_data = implode(",",$where??[]);
			if($this->input->post('staff_role')){
				$staff_role_where =array();
				$staff_role      = $this->input->post('staff_role');
				foreach ($staff_role as $staff_id) {
					if($staff_id != '')
					{
						array_push($staff_role_where, $staff_id);
					}
				}
			}
			$staff_role_where_data = implode(",",$staff_role_where??[]);
			$this->db->select('tblstaff.*,xx_statelist.state_name,a.firstname as reported_f_n,a.lastname as reported_l_n,' . db_prefix() . 'hr_job_position.position_name');
            $this->db->from(db_prefix() . 'staff');
            $this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'staff.state = ' . db_prefix() . 'xx_statelist.short_name', 'left');
            $this->db->join(db_prefix() . 'departments', db_prefix() . 'staff.role = ' . db_prefix() . 'departments.name', 'left');
            $this->db->join(db_prefix() . 'hr_job_position', db_prefix() . 'staff.job_position = ' . db_prefix() . 'hr_job_position.position_id', 'left');
            $this->db->join(db_prefix() . 'staff a', db_prefix() . 'staff.team_manage = a.staffid', 'left');
			$this->db->join(db_prefix() . 'staff_departments', db_prefix() . 'staff.staffid = ' . db_prefix() . 'staff_departments.staffid', 'left');
	        if($data['hr_profile_deparment'] !=''){
				$this->db->where('tblstaff_departments.departmentid',$data['hr_profile_deparment']);  
			}
			$data['staff_role']='';
			if($data['staff_role'] !=''){
				$this->db->where_in('tblstaff.job_position',$staff_role_where_data,'false');  
			}
			if($this->input->post('staff_type')){
				$this->db->where_in('tblstaff.role',$staff_type_data,'false');
			}
			if($data['hr_profile_report_to'] !=''){
				$this->db->where('tblstaff.team_manage',$data['hr_profile_report_to']);  
			}
            if($data['hr_profile_state'] !=''){
                $this->db->where('tblstaff.state',$data['hr_profile_state']);
			}
            if($data['status'] == 'all'){
				}else{
                $this->db->where('tblstaff.active',$data['status']);
			}
            $selected_company = $this->session->userdata('root_company');
            $notShowStaff = array('3','2513');
            $regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
            //$regExp = 'REGEXP ".*;s:[0-9]+:'2'.*";
            //$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
            $this->db->where('tblstaff.PlantID',$selected_company);
            $this->db->where_not_in('tblstaff.staffid',$notShowStaff);
            $this->db->group_by('tblstaff.AccountID');
            $this->db->order_by('tblstaff.firstname','ASC');
            $query = $this->db->get();
			return  $query->result_array();
		}
		//===================== Get Job Position List By Department ====================
		public function job_position_by_id($id)
		{
			$sql='SELECT * FROM tblhr_job_position WHERE department_id REGEXP "'.$id.'." OR department_id = "'.$id.'"';
			//$this->db->where('department_id', $id);
			return $this->db->query($sql)->result_array();
		}
		//====================================================================
		// Add Edit Section 
		public function SaveAccountDetails($Data)
		{
			//print_r($Data); die;
			$AccountID = $Data["AccountID"];
			$opening_b = $Data["opening_b"];
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$LogID = $this->session->userdata('username');
			$Department = $Data['Department'];
			$next_staff_code = get_option('next_staff_number');
			$prefix = "STF";
			$new_next_staff_code = str_pad($next_staff_code, 5, '0', STR_PAD_LEFT);
			$StaffCode = $prefix.$new_next_staff_code;
			$password        = app_hash_password($StaffCode);
			$StaffArray = array(
				'PlantID' =>$selected_company,
				'AccountID' =>$StaffCode,
				'firstname' =>$Data["firstname"],
				'lastname' =>$Data["lastname"],
				'ActGroupID' =>"10000",
				'SubActGroupID1' =>"100023",
				'SubActGroupID' =>$Data["StaffType"],
				'job_position' =>$Data["designation"],
				'team_manage' =>$Data["reporting_to"],
				'phonenumber' =>$Data["phonenumber"],
				'mobile2' =>$Data["altphonenumber"],
				'email' =>$Data["email"],
				'peremail' =>$Data["peremail"],
				'state' =>$Data["state"],
				'city' =>$Data["city"],
				'current_address' =>$Data["address1"],
				'home_town' =>$Data["address2"],
				'pincode' =>$Data["zip"],
				'pan_number' =>$Data["Pan"],
				'aadhar_number' =>$Data["Aadhaarno"],
				'ifsc' =>$Data["ifsc"],
				'issue_bank' =>$Data["issue_bank"],
				'account_number' =>$Data["account_number"],
				'name_account' =>$Data["name_account"],
				'sex' =>$Data["sex"],
				'marital_status' =>$Data["marital_status"],
				'literacy' =>$Data["literacy"],
				'birthday' =>to_sql_date($Data["birthday"]),
				'app_access' =>$Data["app_access"],
				'DiveceID' =>$Data["DeviceID"] ?? '',
				'StartDate' =>to_sql_date($Data["datecreated"]),
				'UserID' =>$LogID,
				'datecreated' =>date("Y-m-d H:i:s"),
				'working_hour' =>$Data["working_hour"],
				'active' =>$Data["active"],
				'IsPF' =>$Data["IsPF"],
				'IsESIC' =>$Data["IsESIC"],
				'EmpCode' =>$Data["EmpCode"],
				'FatherName' =>$Data["FatherName"],
				'MotherName' =>$Data["MotherName"],
				'SpouseName' =>$Data["SpouseName"],
				'EmergencyContact' =>$Data["EmergencyContact"],
				'PermanentAddress' =>$Data["PermanentAddress"],
				'PermanentAddressPincode' =>$Data["PermanentAddressPincode"],
				'BloodGroup' =>$Data["BloodGroup"],
				'NomineeName' =>$Data["NomineeName"],
				'NomineeDob' =>to_sql_date($Data["NomineeDob"]),
				'DateOfLeaving' =>to_sql_date($Data["DateOfLeaving"]),
				'LastWorkingDay' =>to_sql_date($Data["LastWorkingDay"]),
				'ResignationReason' =>$Data["ResignationReason"],
				'TechnicalEduction' =>$Data["TechnicalEduction"],
				'UAN_No' =>$Data["UAN_No"],
				'ESI_No' =>$Data["ESI_No"],
				'EmpType' =>$Data["EmpType"],
				'Remark' =>$Data["Remark"],
				'BranchAddress' =>$Data["BranchAddress"],
				'BranchName' =>$Data["BranchName"],
				'Permanentstate' =>$Data["Permanentstate"],
				'Permanentcity' =>$Data["Permanentcity"],
				'PermanentAddressline2' =>$Data["PermanentAddressline2"],
			);
			$StaffArray["password"] = $password;
			
			$this->db->insert(db_prefix() . 'staff',$StaffArray);
			$LastId = $this->db->insert_id();
			if($LastId){
				$this->increment_next_staff_code();
				$balArray = array(
                'PlantID' =>$selected_company,
                'AccountID' =>strtoupper($AccountID),
                'BAL1' =>$opening_b,
                'FY' =>$FY,
				);
				$this->db->insert(db_prefix() . 'accountbalances',$balArray);
				// Department Added
				$DepData = array(
				'departmentid'=>$Department,
				'staffid'=>$LastId
				);
				$this->db->insert(db_prefix() . 'staff_departments', $DepData);
				$next_staff_code = get_option('next_staff_number');
				$new_next_staff_code = str_pad($next_staff_code, 5, '0', STR_PAD_LEFT);
				return $new_next_staff_code;
				}else{
				return false;
			}
		}
		public function increment_next_staff_code()
		{
			$this->db->where('name', 'next_staff_number');
			$this->db->set('value', 'value+1', false);
			$this->db->update(db_prefix() . 'options');
		}
		public function UpdateAccountDetails($Data)
		{
			$AccountID = 'STF'.$Data["AccountID"];
			$userID = $Data["userID"];
			// echo $userID;die;
			$opening_b = $Data["opening_b"];
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$LogID = $this->session->userdata('username');
			$Department = $Data['Department'];
			$updatted = 0;
			$StaffArray = array(
				'SubActGroupID' =>$Data["StaffType"],
				'job_position' =>$Data["designation"],
				'firstname' =>$Data["firstname"],
				'lastname' =>$Data["lastname"],
				'phonenumber' =>$Data["phonenumber"],
				'mobile2' =>$Data["altphonenumber"],
				'email' =>$Data["email"],
				'peremail' =>$Data["peremail"],
				'state' =>$Data["state"],
				'city' =>$Data["city"],
				'current_address' =>$Data["address1"],
				'home_town' =>$Data["address2"],
				'pincode' =>$Data["zip"],
				'sex' =>$Data["sex"],
				'pan_number' =>$Data["Pan"],
				'aadhar_number' =>$Data["Aadhaarno"],
				'team_manage' =>$Data["reporting_to"],
				'StationName' =>$Data["StationName"] ?? '',
				'account_number' =>$Data["account_number"],
				'name_account' =>$Data["name_account"],
				'ifsc' =>$Data["ifsc"],
				'issue_bank' =>$Data["issue_bank"],
				'headqurter' =>$Data["headqurter"] ?? '',
				'birthday' =>to_sql_date($Data["birthday"]),
				'literacy' =>$Data["literacy"],
				'marital_status' =>$Data["marital_status"],
				'active' =>$Data["active"],
				'IsPF' =>$Data["IsPF"],
				'IsESIC' =>$Data["IsESIC"],
				'working_hour' =>$Data["working_hour"],
				'app_access' =>$Data["app_access"],
				'DiveceID' =>$Data["DeviceID"] ?? '',
				'Movement' =>$Data["Movement"] ?? '',
				'OfficeID' =>$Data["OfficeID"] ?? '',
				'StartDate' =>date('Y-m-d H:i:s'),
				'UserID' =>$LogID,
				'PlantID' =>$selected_company,
				'EmpCode' =>$Data["EmpCode"],
				'FatherName' =>$Data["FatherName"],
				'MotherName' =>$Data["MotherName"],
				'SpouseName' =>$Data["SpouseName"],
				'EmergencyContact' =>$Data["EmergencyContact"],
				'PermanentAddress' =>$Data["PermanentAddress"],
				'PermanentAddressPincode' =>$Data["PermanentAddressPincode"],
				'BloodGroup' =>$Data["BloodGroup"],
				'NomineeName' =>$Data["NomineeName"],
				'NomineeDob' =>to_sql_date($Data["NomineeDob"]),
				'DateOfLeaving' =>to_sql_date($Data["DateOfLeaving"]),
				'LastWorkingDay' =>to_sql_date($Data["LastWorkingDay"]),
				'ResignationReason' =>$Data["ResignationReason"],
				'TechnicalEduction' =>$Data["TechnicalEduction"],
				'UAN_No' =>$Data["UAN_No"],
				'ESI_No' =>$Data["ESI_No"],
				'EmpType' =>$Data["EmpType"],
				'Remark' =>$Data["Remark"],
				'BranchAddress' =>$Data["BranchAddress"],
				'BranchName' =>$Data["BranchName"],
				'Permanentstate' =>$Data["Permanentstate"],
				'Permanentcity' =>$Data["Permanentcity"],
				'PermanentAddressline2' =>$Data["PermanentAddressline2"],
			);
			
			$this->db->where('AccountID', $AccountID);
			$this->db->update(db_prefix() . 'staff', $StaffArray);
			if($this->db->affected_rows() > 0){
				$updatted++; 
			}
			$CheckBalRecord = $this->ChkBalRecord($AccountID,$selected_company,$FY);
			$staff_user_id = $this->session->userdata('staff_user_id');
			if($CheckBalRecord){
				if($staff_user_id == "3"){
					$UpdateAccountBal = array(
                    'BAL1' =>$opening_b,
                    'UserID2' =>$LogID,
                    'Lupdate' =>date('Y-m-d H:i:s'),
					);
					$this->db->where('AccountID', $AccountID);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('FY', $FY);
					$this->db->update(db_prefix() . 'accountbalances', $UpdateAccountBal);
					if($this->db->affected_rows() > 0){
						$updatted++; 
					}
				}
				}else{
				$InsAccountBal = array(
                'PlantID' =>$selected_company,
                'AccountID' =>strtoupper($AccountID),
                'BAL1' =>$opening_b,
                'FY' =>$FY
				);
				$this->db->insert(db_prefix() . 'accountbalances',$InsAccountBal);
				$LastId = $this->db->insert_id();
				if($LastId){
					$updatted++;  
				}
			}
			
			$CheckDepartmentRecord = $this->ChkDepartmentRecord($userID);
			if($CheckDepartmentRecord){
				$DepData = array(
        		'departmentid'=>$Department
				);
				$this->db->where('staffid', $userID);
				$this->db->update(db_prefix() . 'staff_departments', $DepData);
				if($this->db->affected_rows() > 0){
					$updatted++; 
				}
				}else{
                $DepData = array(
				'staffid'=>$userID,
				'departmentid'=>$Department
    		    );
    		    $this->db->insert(db_prefix() . 'staff_departments', $DepData);
    		    $LastId = $this->db->insert_id();
				if($LastId){
					$updatted++;  
				}
			}
			if($this->db->affected_rows() > 0){
				$updatted++; 
			}
			if($updatted > 0){
				return true;
				}else{
				return false;
			}
		}
		// Check AccountBalanceMaster Record
		public function ChkBalRecord($AccountID,$PlantID,$FY)
		{
			$this->db->select(db_prefix() . 'accountbalances.*');
			$this->db->where('AccountID', $AccountID);
			$this->db->where('PlantID', $PlantID);
			$this->db->where('FY', $FY);
			$this->db->from(db_prefix() . 'accountbalances');
			$data =  $this->db->get()->row();
			return $data;
		}
		// Check Sltd Record
		public function ChkSldtRecord($AccountID,$PlantID,$FY)
		{
			$this->db->select(db_prefix() . 'accountsld.*');
			$this->db->where('AccountID', $AccountID);
			$this->db->where('PlantID', $PlantID);
			//$this->db->where('FY', $FY);
			$this->db->from(db_prefix() . 'accountsld');
			$data =  $this->db->get()->row();
			return $data;
		}
		// Check Sltd Record
		public function ChkDepartmentRecord($userID)
		{
			$this->db->select(db_prefix() . 'staff_departments.*');
			$this->db->where('staffid', $userID);
			//$this->db->where('FY', $FY);
			$this->db->from(db_prefix() . 'staff_departments');
			$data =  $this->db->get()->row();
			return $data;
		}
	}
