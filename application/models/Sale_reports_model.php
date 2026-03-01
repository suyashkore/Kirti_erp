<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Sale_reports_model extends App_Model
	{
		
		public function __construct()
		{
			parent::__construct(); 
		}
		
		public function getroute()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('*');
			$this->db->where(db_prefix() . 'route.PlantID', $selected_company);
			$this->db->order_by('name', 'ASC');
			
			return $this->db->get(db_prefix() . 'route')->result_array();
		}
		public function GetSubgroup2()
		{
			$this->db->select('*');
			$this->db->order_by('name', 'ASC');
			
			return $this->db->get(db_prefix() . 'items_sub_group2')->result_array();
		}
		//=================== Get Day Wise Sale For current month ======================
		public function GetDaywiseSaleForthisMonthByAccountID($filter = "")
		{
			$from_date = to_sql_date($filter["from_date"]);
			$to_date = to_sql_date($filter["to_date"]);
			$period = new DatePeriod(
    		new DateTime($from_date),
    		new DateInterval('P1D'),
    		new DateTime($to_date)
			);
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			
			$DayWiseSale  = $this->GetDayWiseSaleReportByAccountID($filter);
			
			$labels = [];
			$totals = [];
			// Get the current date
			$i = 1;
			foreach ($period as $key => $value) {
				$date = $value->format('d/m/Y'); 
				$date2 = $value->format('Y-m-d');
				$lable = substr($date,0,2) ."-".date("M", strtotime($date2));
				array_push($labels, $lable);
				$DaySale = 0;
				foreach ($DayWiseSale as $key1 => $value1) {
					if(substr($value1['Transdate'],0,10) == $date2){
						$DaySale = $value1["BillAmt"];
					}
				}
				array_push($totals, $DaySale);
				$i++;
			}
			$chart = [
            'labels'   => $labels,
            'datasets' => [
			[
			'label'           => "Amount",
			'backgroundColor' => 'rgba(37,155,35,0.2)',
			'borderColor'     => '#84c529',
			'tension'         => false,
			'borderWidth'     => 1,
			'data'            => $totals,
			],
            ],
			];
			
			return $chart;
		}
		//=================== Get Day Wise Sale Return =================================
		public function GetDayWiseSaleReturnReports($filter = "")
		{
			$from_date = to_sql_date($filter["from_date"]);
			$to_date = to_sql_date($filter["to_date"]);
			$period = new DatePeriod(
    		new DateTime($from_date),
    		new DateInterval('P1D'),
    		new DateTime($to_date)
			);
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			
			//$DayWiseSale  = $this->DayWiseSaleReturnData($filter);
			$DayWiseSaleRtn  = $this->GetDayWiseSaleReturnReport($filter);
			$labels = [];
			$totals = [];
			// Get the current date
			$i = 1;
			foreach ($period as $key => $value) {
				$date = $value->format('d/m/Y'); 
				$date2 = $value->format('Y-m-d');
				$lable = substr($date,0,2) ."-".date("M", strtotime($date2));
				array_push($labels, $lable);
				$DaySale = 0;
				foreach ($DayWiseSaleRtn as $key1 => $value1) {
					if(substr($value1['TransDate2'],0,10) == $date2){
						$DaySale = $value1["AmtSum"];
					}
				}
				array_push($totals, $DaySale);
				$i++;
			}
			$chart = [
            'labels'   => $labels,
            'datasets' => [
			[
			'label'           => "Amount",
			'backgroundColor' => 'rgba(37,155,35,0.2)',
			'borderColor'     => '#84c529',
			'tension'         => false,
			'borderWidth'     => 1,
			'data'            => $totals,
			],
            ],
			];
			
			return $chart;
		}
		//=================== Get Sale data from sale master ===========================
		public function GetDayWiseSaleReportByAccountID($filterdata = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"];
			$to_date = $filterdata["to_date"];
			
			
			$sql1 = '('.db_prefix().'salesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'salesmaster.PlantID="'.$selected_company.'"';
			
			// if(!empty($filterdata["AccountID"])){
			$sql1 .= ' AND '.db_prefix().'ordermaster.AccountID2="'.$filterdata["AccountID"].'"';
			// }
			
			$sql1 .= ' GROUP BY DATE(tblsalesmaster.Transdate)';
			
			$sql ='SELECT tblordermaster.AccountID2 AS AccountID,tblsalesmaster.Transdate,COALESCE(SUM(BillAmt),0) As BillAmt FROM '.db_prefix().'salesmaster
			INNER JOIN tblordermaster ON tblordermaster.OrderID = tblsalesmaster.OrderID 
			WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//=================== Get Sale return data from sale master ====================
		public function DayWiseSaleReturnData($filterdata = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"];
			$to_date = $filterdata["to_date"];
			
			$sql1 = '('.db_prefix().'salesreturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") 
			AND '.db_prefix().'salesreturn.PlantID="'.$selected_company.'" AND tblsalesreturn.FY = "'.$fy.'"';
			
			$sql1 .= ' GROUP BY DATE(tblsalesreturn.Transdate)';
			
			$sql ='SELECT tblsalesreturn.Transdate,COALESCE(SUM(tblsalesreturn.BillAmt),0) As BillAmt FROM '.db_prefix().'salesreturn
			WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//======================= Day Wise Sale data ===================================
		public function GetCalenderMonthlySaleData($filter = "")
		{
			$month_input = $filter['Month']; // Example: '2024-11'
			// $selected_year = date('Y', strtotime($month_input . "-01")); // Extract year
			// $selected_month = date('m', strtotime($month_input . "-01")); // Extract month
			$date = $month_input.'-01';//your given date
			$first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			$first_date = date("Y-m-d",$first_date_find);
			
			$last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			$last_date = date("Y-m-d",$last_date_find);
			
			$from_date = $first_date;
			$to_date = $last_date;
			
			$to_date_new = date( 'Y-m-d', strtotime( $to_date . ' +1 day' ) );
			$period = new DatePeriod(
			new DateTime($from_date),
			new DateInterval('P1D'),
			new DateTime($to_date_new)
			);
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			
			if($filter["ReportType"] =="NetSale" || $filter["ReportType"] =="FreshDamage"){
				$DayWiseSaleRtn  = $this->GetDayWiseSaleReturnReport($filter);
			}
			if($filter["ReportType"] !="FreshDamage"){
				$DayWiseSale  = $this->GetDayWiseSaleReport($filter);
			}
			$i = 1;
			$response = array();
			foreach ($period as $key => $value) {
				$date = $value->format('d/m/Y'); 
				$date2 = $value->format('Y-m-d');
				$DaySale = 0;
				foreach ($DayWiseSale as $key1 => $value1) {
					if(substr($value1['TransDate2'],0,10) == $date2){
						$DaySale = $value1["AmtSum"];
					}
				}
				foreach ($DayWiseSaleRtn as $key2 => $value2) {
					if(substr($value2['TransDate2'],0,10) == $date2){
						if($filter["ReportType"] =="NetSale"){
							$DaySale -= $value2["AmtSum"];
							}else{
							$DaySale += $value2["AmtSum"];
						}
					}
				}
				$new_array = array("date"=>$date2,"temperature"=>(int)$DaySale);
				array_push($response, $new_array);
			}
			return $response;
		}
		public function GetCalenderMonthlySaleDataNew($filter = "")
		{
			$month_input = $filter['Month']; // Example: '2024-11'
			$date = $month_input.'-01';//your given date
			$first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			$first_date = date("Y-m-d",$first_date_find);
			
			$last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			$last_date = date("Y-m-d",$last_date_find);
			
			$from_date = $first_date;
			$to_date = $last_date;
			
			$to_date_new = date( 'Y-m-d', strtotime( $to_date . ' +1 day' ) );
			$period = new DatePeriod(
			new DateTime($from_date),
			new DateInterval('P1D'),
			new DateTime($to_date_new)
			);
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			
			$DayWiseSale  = $this->GetDayWiseSaleReportNew($filter);
			
			$i = 1;
			$response = array();
			foreach ($period as $key => $value) {
				$date = $value->format('d/m/Y'); 
				$date2 = $value->format('Y-m-d');
				$DaySale = 0;
				foreach ($DayWiseSale as $key1 => $value1) {
					if(substr($value1['TransDate2'],0,10) == $date2){
						$DaySale = $value1["AmtSum"];
					}
				}
				
				$new_array = array("date"=>$date2,"temperature"=>(int)$DaySale);
				array_push($response, $new_array);
			}
			return $response;
		}
		//==================== Day Wise Sale Return data History =======================
		public function GetCalenderMonthlySaleReturnData($filter = "")
		{
			$month_input = $filter['Month']; // Example: '2024-11' 
			$date = $month_input.'-01';//your given date
			$first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			$first_date = date("Y-m-d",$first_date_find);
			
			$last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			$last_date = date("Y-m-d",$last_date_find);
			
			$from_date = $first_date;
			$to_date = $last_date;
			
			$to_date_new = date( 'Y-m-d', strtotime( $to_date . ' +1 day' ) );
			$period = new DatePeriod(
    		new DateTime($from_date),
    		new DateInterval('P1D'),
    		new DateTime($to_date_new)
			);
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			
			$DayWiseSaleRtn  = $this->GetDayWiseSaleReturnReport($filter);
			
			$i = 1;
			$response = array();
			foreach ($period as $key => $value) {
				$date = $value->format('d/m/Y'); 
				$date2 = $value->format('Y-m-d');
				$DaySale = 0;
				
				foreach ($DayWiseSaleRtn as $key2 => $value2) {
					if(substr($value2['TransDate2'],0,10) == $date2){
						$DaySale += $value2["AmtSum"];
					}
				}
				$new_array = array("date"=>$date2,"temperature"=>(int)$DaySale);
				array_push($response, $new_array);
			}
			return $response;
		}
		
		//======================== Get Customer Overview ===============================
		public function GetCustomerOverview($filter = "")
		{
			$month_input = $filter['Month']; // Example: '2024-11'
			// $selected_year = date('Y', strtotime($month_input . "-01")); // Extract year
			// $selected_month = date('m', strtotime($month_input . "-01")); // Extract month
			$date = $month_input.'-01';//your given date
			$first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			$first_date = date("Y-m-d",$first_date_find);
			
			$last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			$last_date = date("Y-m-d",$last_date_find);
			$from_date = $first_date;
			$to_date = $last_date;
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			// Get Client List
			$response  = $this->GetCustomerOverviewData($filter);
			
			return $response;
		}
		public function GetCustomerOverviewNew($filter = "")
		{
			$month_input = $filter['Month']; // Example: '2024-11'
			// $selected_year = date('Y', strtotime($month_input . "-01")); // Extract year
			// $selected_month = date('m', strtotime($month_input . "-01")); // Extract month
			$date = $month_input.'-01';//your given date
			$first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			$first_date = date("Y-m-d",$first_date_find);
			
			$last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			$last_date = date("Y-m-d",$last_date_find);
			$from_date = $first_date;
			$to_date = $last_date;
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			// Get Client List
			$response  = $this->GetCustomerOverviewDataNew($filter);
			
			return $response;
		}
		//======================= Day Wise Sale data ===================================
		public function GetDaywiseSaleForthisMonth($filter = "")
		{
			
			if(!empty($filter["from_date"])){
				$from_date = to_sql_date($filter["from_date"]);
				$to_date = to_sql_date($filter["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			
			$to_date_new = date( 'Y-m-d', strtotime( $to_date . ' +1 day' ) );
			$period = new DatePeriod(
			new DateTime($from_date),
			new DateInterval('P1D'),
			new DateTime($to_date_new)
			);
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			
			
			if($filter["ReportType"] =="NetSale" || $filter["ReportType"] =="FreshDamage"){
				$DayWiseSaleRtn  = $this->GetDayWiseSaleReturnReport($filter);
			}
			if($filter["ReportType"] !="FreshDamage"){
				$DayWiseSale  = $this->GetDayWiseSaleReport($filter);
			}
			$labels = [];
			$totals = [];
			//$types  = $this->get();
			// Get the current date
			$i = 1;
			foreach ($period as $key => $value) {
				$date = $value->format('d/m/Y'); 
				$date2 = $value->format('Y-m-d');
				$lable = substr($date,0,2) ."-".date("M", strtotime($date2));
				array_push($labels, $lable);
				$DaySale = 0;
				foreach ($DayWiseSale as $key1 => $value1) {
					if(substr($value1['TransDate2'],0,10) == $date2){
						$DaySale = $value1["AmtSum"];
					}
				}
				foreach ($DayWiseSaleRtn as $key2 => $value2) {
					if(substr($value2['TransDate2'],0,10) == $date2){
						if($filter["ReportType"] =="NetSale"){
							$DaySale -= $value2["AmtSum"];
							}else{
							$DaySale += $value2["AmtSum"];
						}
					}
				}
				array_push($totals, $DaySale);
				$i++;
			}
			$chart = [
			'labels'   => $labels,
			'datasets' => [
			[
			'label'           => "Amount",
			'backgroundColor' => 'rgba(37,155,35,0.2)',
			'borderColor'     => '#84c529',
			'tension'         => false,
			'borderWidth'     => 1,
			'data'            => $totals,
			],
			],
			];
			
			return $chart;
		}
		
		public function GetDaywiseSaleForthisMonthNew($filter = "")
		{
			
			if(!empty($filter["from_date"])){
				$from_date = to_sql_date($filter["from_date"]);
				$to_date = to_sql_date($filter["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			
			$to_date_new = date( 'Y-m-d', strtotime( $to_date . ' +1 day' ) );
			$period = new DatePeriod(
			new DateTime($from_date),
			new DateInterval('P1D'),
			new DateTime($to_date_new)
			);
			$filter["from_date"] = $from_date;
			$filter["to_date"] = $to_date;
			
			$DayWiseSale  = $this->GetDayWiseSaleReportNew($filter);
			
			$labels = [];
			$totals = [];
			//$types  = $this->get();
			// Get the current date
			$i = 1;
			foreach ($period as $key => $value) {
				$date = $value->format('d/m/Y'); 
				$date2 = $value->format('Y-m-d');
				$lable = substr($date,0,2) ."-".date("M", strtotime($date2));
				array_push($labels, $lable);
				$DaySale = 0;
				foreach ($DayWiseSale as $key1 => $value1) {
					if(substr($value1['TransDate2'],0,10) == $date2){
						$DaySale = $value1["AmtSum"];
					}
				}
				array_push($totals, $DaySale);
				$i++;
			}
			$chart = [
			'labels'   => $labels,
			'datasets' => [
			[
			'label'           => "Amount",
			'backgroundColor' => 'rgba(37,155,35,0.2)',
			'borderColor'     => '#84c529',
			'tension'         => false,
			'borderWidth'     => 1,
			'data'            => $totals,
			],
			],
			];
			
			return $chart;
		}
		//=========================== Get YOY Monthly Sale =============================
		public function GetYOYMonthlySaleReports($filter = "")
		{
			$colors = get_system_favourite_colors();
			$chart        = [
            'labels'   => [],
            'datasets' => [],
			];
			$data           = [];
			$data['months'] = [];
			$fy = $this->session->userdata('finacial_year');
			$PreFY = $fy - 1;
			$selected_company = $this->session->userdata('root_company');
			$filter["PlantID"] = $selected_company;
			$filter["FY"] = array($fy,$PreFY);
			$filter["TType"] = 'O';
			$a = "20".$fy."-04-01";
			$b = "20".($fy + 1)."-03-31";
			$monthList = array();
			$i = date("Ym", strtotime($a));
			while($i <= date("Ym", strtotime($b))){
				//echo $i."\n";
				$monthNum = substr($i,4,2);
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('F'); // March
				$monthName2 = $dateObj->format('M'); // Mar
				$new_array = array(
                "YEAR"=>substr($i,0,4),
                "Month"=>substr($i,4,2),
                "MonthName"=>$monthName,
                "MonthName2"=>$monthName2,
				);
				array_push($monthList,$new_array);
				if(substr($i, 4, 2) == "12")
                $i = (date("Y", strtotime($i."01")) + 1)."01";
				else
                $i++;
			}
			
			$MonthWiseSale  = $this->GetMonthWiseSaleReport($filter);
			
			foreach ($monthList as $month) {
				array_push($chart['labels'], $month["MonthName2"]);
			}
			$Years = array($PreFY,$fy);
			$i = 0;
			foreach($Years as $val){
				$FullYear = '20'.$val;
				if ($i > 0) {
					$color = "#03a9f4";
					}else{
					$color = '#4B5158';
				}
				$datasets_data          = [];
				$datasets_data['total'] = [];
				foreach ($monthList as $month) {
					$total_payments = [];
					if (!isset($datasets_data['temp'][$month])) {
						$datasets_data['temp'][$month] = [];
					}
					foreach($MonthWiseSale as $Skey=>$Sval){
						if($val == $Sval["FY"] && $month["MonthName"]==$Sval['month']){
							$total_payments[] = $Sval['AmtSum'];
						}
					}
					$datasets_data['total'][] = array_sum($total_payments);
				}
				
				$chart['datasets'][] = [
                'label'           => $val,
                /*'backgroundColor' => $color,*/
                'borderColor'     => adjust_color_brightness($color, -20),
                'tension'         => false,
                'borderWidth'     => 1,
                'data'            => $datasets_data['total'],
				];
				$i++;
			}
			return $chart;
		}
		//======================== Get Monthly Sale Return =============================
		public function GetMonthlySaleReturns($filter = "")
		{
			$colors = get_system_favourite_colors();
			$chart        = [
            'labels'   => [],
            'datasets' => [],
			];
			$data           = [];
			$data['months'] = [];
			$fy = $this->session->userdata('finacial_year');
			$PreFY = $fy - 1;
			$selected_company = $this->session->userdata('root_company');
			$filter["PlantID"] = $selected_company;
			$filter["FY"] = array($fy);
			$filter["TType"] = 'R';
			
			$a = "20".$fy."-04-01";
			$b = "20".($fy + 1)."-03-31";
			$monthList = array();
			$i = date("Ym", strtotime($a));
			while($i <= date("Ym", strtotime($b))){
				//echo $i."\n";
				$monthNum = substr($i,4,2);
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('F'); // March
				$monthName2 = $dateObj->format('M'); // Mar
				$new_array = array(
                "YEAR"=>substr($i,0,4),
                "Month"=>substr($i,4,2),
                "MonthName"=>$monthName,
                "MonthName2"=>$monthName2,
				);
				array_push($monthList,$new_array);
				if(substr($i, 4, 2) == "12")
                $i = (date("Y", strtotime($i."01")) + 1)."01";
				else
                $i++;
			}
			foreach ($monthList as $month) {
				array_push($chart['labels'], $month["MonthName2"]);
			}
			$MonthWiseSale  = $this->GetMonthWiseSaleReport($filter);
			
			$Type = array("Damage","Fresh");
			$i = 0;
			foreach($Type as $val){
				if ($i > 0) {
					$color = "#03a9f4";
					}else{
					$color = '#4B5158';
				}
				$datasets_data          = [];
				$datasets_data['total'] = [];
				foreach ($monthList as $month) {
					$total_payments = [];
					if (!isset($datasets_data['temp'][$month])) {
						$datasets_data['temp'][$month] = [];
					}
					foreach($MonthWiseSale as $Skey=>$Sval){
						if($val == $Sval["TType2"] && $month["MonthName"]==$Sval['month']){
							$total_payments[] = $Sval['AmtSum'];
						}
					}
					$datasets_data['total'][] = array_sum($total_payments);
				}
				$chart['datasets'][] = [
                'label'           => $val,
                /*'backgroundColor' => $color,*/
                'borderColor'     => adjust_color_brightness($color, -20),
                'tension'         => false,
                'borderWidth'     => 1,
                'data'            => $datasets_data['total'],
				];
				$i++;
			}
			return $chart;
		}
		public function GetMonthlySaleReturnsNew($filter = "")
		{
			$colors = get_system_favourite_colors();
			$chart        = [
            'labels'   => [],
            'datasets' => [],
			];
			$data           = [];
			$data['months'] = [];
			$fy = $this->session->userdata('finacial_year');
			$PreFY = $fy - 1;
			$selected_company = $this->session->userdata('root_company');
			$filter["PlantID"] = $selected_company;
			$filter["FY"] = array($fy);
			$filter["TType"] = 'R';
			
			$a = "20".$fy."-04-01";
			$b = "20".($fy + 1)."-03-31";
			$monthList = array();
			$i = date("Ym", strtotime($a));
			while($i <= date("Ym", strtotime($b))){
				//echo $i."\n";
				$monthNum = substr($i,4,2);
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('F'); // March
				$monthName2 = $dateObj->format('M'); // Mar
				$new_array = array(
                "YEAR"=>substr($i,0,4),
                "Month"=>substr($i,4,2),
                "MonthName"=>$monthName,
                "MonthName2"=>$monthName2,
				);
				array_push($monthList,$new_array);
				if(substr($i, 4, 2) == "12")
                $i = (date("Y", strtotime($i."01")) + 1)."01";
				else
                $i++;
			}
			foreach ($monthList as $month) {
				array_push($chart['labels'], $month["MonthName2"]);
			}
			$MonthWiseSale  = $this->GetMonthWiseSaleReportNew($filter);
			
			$Type = array("Damage","Fresh");
			$i = 0;
			foreach($Type as $val){
				if ($i > 0) {
					$color = "#03a9f4";
					}else{
					$color = '#4B5158';
				}
				$datasets_data          = [];
				$datasets_data['total'] = [];
				foreach ($monthList as $month) {
					$total_payments = [];
					if (!isset($datasets_data['temp'][$month])) {
						$datasets_data['temp'][$month] = [];
					}
					foreach($MonthWiseSale as $Skey=>$Sval){
						if($val == $Sval["TType2"] && $month["MonthName"]==$Sval['month']){
							$total_payments[] = $Sval['AmtSum'];
						}
					}
					$datasets_data['total'][] = array_sum($total_payments);
				}
				$chart['datasets'][] = [
                'label'           => $val,
                /*'backgroundColor' => $color,*/
                'borderColor'     => adjust_color_brightness($color, -20),
                'tension'         => false,
                'borderWidth'     => 1,
                'data'            => $datasets_data['total'],
				];
				$i++;
			}
			return $chart;
		}
		//=================== Get Monthly sale & Sale Forecasting ======================
		public function GetSalesForecasting($filter = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$filter["PlantID"] = $selected_company;
			$filter["FY"] = array($fy);
			$filter["TType"] = 'O';
			$MonthWiseSale  = $this->GetMonthWiseSaleReport($filter);
			
			$labels = [];
			$totals = [];
			$color = [];
			$i = 1;
			$TotalMonth = count($MonthWiseSale);
			$LastMonth = "";
			$lastYear = "";
			$NewMonth = "";
			$SaleAmt = 0;
			$SaleGrowth = 0;
			$PreSaleAmt = 0;
			$PreSaleGrowth = 0;
			foreach ($MonthWiseSale as $key => $value) {
				$lable = $value["month"] ."-".$value["year"];
				array_push($labels, $lable);
				array_push($totals, $value["AmtSum"]);
				array_push($color, 'rgba(37,155,35,0.2)');
				$LastMonth = $value["month"];
				$lastYear = $value["year"];
				if($i == 1){
					$PreSaleAmt = $value["AmtSum"];
					$PreSaleGrowth = 100;
					}elseif($i > 0 && $i<$TotalMonth){
					$PreSaleGrowth = (($value["AmtSum"] / $PreSaleAmt) - 1);
					$PreSaleAmt = $value["AmtSum"];
					}elseif($i > 0 && $i==$TotalMonth){
					$SaleAmt = ($PreSaleAmt * (1+$PreSaleGrowth));
					$PreSaleGrowth = (($SaleAmt / $PreSaleAmt) - 1);
					$PreSaleAmt = $value["AmtSum"];
					array_push($totals, $SaleAmt);
					array_push($color, '#e29ed4');
					array_push($labels, $lable);
				}
				
				$i++;
			}
			if($LastMonth == "December"){
				$NewMonth = "January-".($lastYear + 1);
				}else{
				$firstDateOfLastMonth = $lastYear.'-'.$LastMonth.'-01';
				$d = new DateTime($firstDateOfLastMonth);
				$d->modify('next month');
				$NewMonth = $d->format('M')."-".$lastYear;
			}
			$NextMonthSale = ($SaleAmt * (1+$PreSaleGrowth));
			array_push($labels, $NewMonth);
			array_push($totals, $NextMonthSale);
			array_push($color, '#e29ed4');
			$chart = [
            'labels'   => $labels,
            'datasets' => [
			[
			'label'           => "Amount",
			'backgroundColor' => $color,
			'borderColor'     =>'#84c529',
			'tension'         => true,
			'borderWidth'     => 1,
			'data'            => $totals,
			],
            ],
			];
			
			return $chart;
		}
		//========================= Get Monthly Best Seller Items ======================
		public function MonthlyBestSellerItems($filter = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$filter["PlantID"] = $selected_company;
			$filter["FY"] = array($fy);
			$filter["TType"] = 'O';
			$a = "20".$fy."-04-01";
			$b = "20".($fy + 1)."-03-31";
			$monthList = array();
			$i = date("Ym", strtotime($a));
			while($i <= date("Ym", strtotime($b))){
				//echo $i."\n";
				$monthNum = substr($i,4,2);
				$dateObj   = DateTime::createFromFormat('!m', $monthNum);
				$monthName = $dateObj->format('M'); // March
				$new_array = array(
                "YEAR"=>substr($i,0,4),
                "Month"=>substr($i,4,2),
                "MonthName"=>$monthName,
				);
				array_push($monthList,$new_array);
				if(substr($i, 4, 2) == "12")
                $i = (date("Y", strtotime($i."01")) + 1)."01";
				else
                $i++;
			}
			$labels = [];
			$totals = [];
			$html = "";
			$ItemList = array();
			foreach ($monthList as $key => $value) {
				$filter["Month"] = $value["Month"];
				$MonthWiseBestSKU  = $this->GetMonthlyBestSellerItems($filter);
				if($MonthWiseBestSKU){
					$new_array = array(
                    "ItemID"=>$MonthWiseBestSKU->ItemID,
                    "ItemName"=>$MonthWiseBestSKU->description
					);
					array_push($ItemList,$new_array);
					//
				}
				$lable = $MonthWiseBestSKU->ItemID."(".$value["MonthName"] ."-".$value["YEAR"].")";
				array_push($labels, $lable);
				array_push($totals, $MonthWiseBestSKU->AmtSum);
			}
			$ItemList = array_map("unserialize", array_unique(array_map("serialize", $ItemList)));
			foreach($ItemList as $val){
				$html .= "<b>".$val["ItemID"]."</b> : ".$val["ItemName"]."  ";
			}
			$chart = [
            'labels'   => $labels,
            'datasets' => [
			[
			'label'           => "Amount",
			'backgroundColor' => 'rgba(37,155,35,0.2)',
			'borderColor'     =>'#84c529',
			'tension'         => true,
			'borderWidth'     => 1,
			'data'            => $totals,
			],
            ],
            'ItemList'=>$html,
			];
			return $chart;
		}
		//====================== Get Items List By Item Group ==========================
		public function GetGroupWiseItemList($SubGroup = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblitems.*');
			$this->db->where_in('SubGrpID1', $SubGroup);
			$this->db->where('isactive', 'Y');
			$this->db->order_by('tblitems.description', 'ASC');
			return $this->db->get('tblitems')->result_array();
			
		}
		
		
		//========================== Get Client List ===================================
		public function GetCustomerOverviewData($filterdata = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"].' 00:00:00';
			$to_date = $filterdata["to_date"].' 23:59:59';
			
			// Client List
			$this->db->select('Count(tblclients.AccountID) TotalCount,tblclients.active');
			if($filterdata["state"]){
				$this->db->where_in('tblclients.state', $filterdata["state"]);
			}
			$this->db->where('tblclients.SubActGroupID1', "100056");
			$this->db->where('tblclients.PlantID', $selected_company);
			$this->db->group_by('tblclients.active');
			$ClientList = $this->db->get('tblclients')->result_array();
			$Active = 0;
			$Deactive = 0;
			foreach($ClientList as $val){
				if($val["active"] == "1"){
					$Active = $val["TotalCount"];
					}else{
					$Deactive = $val["TotalCount"];
				}
			}
			$All = $Active + $Deactive;
			// Order Account List
			$this->db->distinct();
			$this->db->select('tblordermaster.AccountID AS TotalCount');
			$this->db->join('tblclients', 'tblclients.AccountID = tblordermaster.AccountID');
			if($filterdata["state"]){
				$this->db->where_in('tblclients.state', $filterdata["state"]);
			}
			$this->db->where('tblordermaster.PlantID', $selected_company);
			$this->db->where('tblordermaster.FY', $fy);
			//$this->db->where("tblordermaster.Transdate BETWEEN '$from_date' AND '$to_date'");
			$OrderedPartyList = $this->db->get('tblordermaster')->result_array();
			
			// Invoiced Account List
			$this->db->distinct();
			$this->db->select('tblordermaster.AccountID AS TotalCount');
			$this->db->join('tblclients', 'tblclients.AccountID = tblordermaster.AccountID');
			if($filterdata["state"]){
				$this->db->where_in('tblclients.state', $filterdata["state"]);
			}
			$this->db->where('tblordermaster.PlantID', $selected_company);
			$this->db->where('tblordermaster.FY', $fy);
			$this->db->where('tblordermaster.SalesID IS NOT NULL');
			//$this->db->where("tblordermaster.Transdate BETWEEN '$from_date' AND '$to_date'");
			$InvoicedPartyList = $this->db->get('tblordermaster')->result_array();
			
			$OrderParty = count($OrderedPartyList);
			$InvoicedParty = count($InvoicedPartyList);
			$response = array();
			$AllCustomer = array("All Customers",(int)$All);
			$ActiveCustomer = array("Active Customers",(int)$Active);
			$OrderedCustomer = array("Ordered Customers",(int)$OrderParty);
			$InvoicedCustomer = array("Invoiced Customers",(int)$InvoicedParty);
			array_push($response,$AllCustomer);
			array_push($response,$ActiveCustomer);
			array_push($response,$OrderedCustomer);
			array_push($response,$InvoicedCustomer);
			
			return $response;
			
		}
		public function GetCustomerOverviewDataNew($filterdata = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"].' 00:00:00';
			$to_date = $filterdata["to_date"].' 23:59:59';
			
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			// Client List
			$this->db->select('Count(tblclients.AccountID) TotalCount,tblclients.active');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			
			$this->db->where('tblclients.SubActGroupID1', "100056");
			$this->db->where('tblclients.PlantID', $selected_company);
			$this->db->group_by('tblclients.active');
			$ClientList = $this->db->get('tblclients')->result_array();
			$Active = 0;
			$Deactive = 0;
			foreach($ClientList as $val){
				if($val["active"] == "1"){
					$Active = $val["TotalCount"];
					}else{
					$Deactive = $val["TotalCount"];
				}
			}
			$All = $Active + $Deactive;
			// Order Account List
			$this->db->distinct();
			$this->db->select('tblordermaster.AccountID AS TotalCount');
			$this->db->join('tblclients', 'tblclients.AccountID = tblordermaster.AccountID');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblordermaster.PlantID', $selected_company);
			$this->db->where('tblordermaster.FY', $fy);
			//$this->db->where("tblordermaster.Transdate BETWEEN '$from_date' AND '$to_date'");
			$OrderedPartyList = $this->db->get('tblordermaster')->result_array();
			
			// Invoiced Account List
			$this->db->distinct();
			$this->db->select('tblordermaster.AccountID AS TotalCount');
			$this->db->join('tblclients', 'tblclients.AccountID = tblordermaster.AccountID');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblordermaster.PlantID', $selected_company);
			$this->db->where('tblordermaster.FY', $fy);
			$this->db->where('tblordermaster.SalesID IS NOT NULL');
			//$this->db->where("tblordermaster.Transdate BETWEEN '$from_date' AND '$to_date'");
			$InvoicedPartyList = $this->db->get('tblordermaster')->result_array();
			
			$OrderParty = count($OrderedPartyList);
			$InvoicedParty = count($InvoicedPartyList);
			$response = array();
			$AllCustomer = array("All Customers",(int)$All);
			$ActiveCustomer = array("Active Customers",(int)$Active);
			$OrderedCustomer = array("Ordered Customers",(int)$OrderParty);
			$InvoicedCustomer = array("Invoiced Customers",(int)$InvoicedParty);
			array_push($response,$AllCustomer);
			array_push($response,$ActiveCustomer);
			array_push($response,$OrderedCustomer);
			array_push($response,$InvoicedCustomer);
			
			return $response;
			
		}
		//============== Get Day Wise Sale return Reports ==============================
		public function GetDayWiseSaleReturnReport($filterdata = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"].' 00:00:00';
			$to_date = $filterdata["to_date"].' 23:59:59';
			$this->db->select('tblhistory.TransDate2,SUM(tblhistory.NetChallanAmt) AS AmtSum,SUM(tblhistory.cgstamt) AS cgstamtSum,SUM(tblhistory.sgstamt) AS sgstamtSum,
			SUM(tblhistory.igstamt) AS igstamtSum');
			
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID');
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			if($filterdata["state"]){
				$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID');
				$this->db->where_in('tblclients.state', $filterdata["state"]);
			}
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.TType', "R");
			$this->db->where("TransDate2 BETWEEN '$from_date' AND '$to_date'");
			$this->db->group_by('DATE(tblhistory.TransDate2)');
			$this->db->order_by('tblhistory.TransDate2', 'ASC');
			return $this->db->get('tblhistory')->result_array();
		}
		
		//===================== Get Day Wise Sale Reports ==============================
		public function GetDayWiseSaleReport($filterdata = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"].' 00:00:00';
			$to_date = $filterdata["to_date"].' 23:59:59';
			
			
			$this->db->select('tblhistory.TransDate2,SUM(tblhistory.NetChallanAmt) AS AmtSum,SUM(tblhistory.cgstamt) AS cgstamtSum,SUM(tblhistory.sgstamt) AS sgstamtSum,
			SUM(tblhistory.igstamt) AS igstamtSum');
			
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID');
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			if($filterdata["state"]){
				$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID');
				$this->db->where_in('tblclients.state', $filterdata["state"]);
			}
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if($filterdata["ReportType"] == "Sale" || $filterdata["ReportType"] == "NetSale"){
				$this->db->where('tblhistory.TType', "O");
			}
			if($filterdata["ReportType"] == "Fresh"){
				$this->db->where('tblhistory.TType', "R");
				$this->db->where('tblhistory.TType2', "Fresh");
			}
			if($filterdata["ReportType"] == "Damage"){
				$this->db->where('tblhistory.TType', "R");
				$this->db->where('tblhistory.TType2', "Damage");
			}
			
			$this->db->where("TransDate2 BETWEEN '$from_date' AND '$to_date'");
			$this->db->group_by('DATE(tblhistory.TransDate2)');
			$this->db->order_by('tblhistory.TransDate2', 'ASC');
			return $this->db->get('tblhistory')->result_array();
		}
		public function GetDayWiseSaleReportNew($filterdata = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"].' 00:00:00';
			$to_date = $filterdata["to_date"].' 23:59:59';
			
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('tblhistory.TransDate2,SUM(tblhistory.NetChallanAmt) AS AmtSum,SUM(tblhistory.cgstamt) AS cgstamtSum,SUM(tblhistory.sgstamt) AS sgstamtSum,
			SUM(tblhistory.igstamt) AS igstamtSum');
			
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID');
			$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID');
			
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.TType', "O");
			
			$this->db->where('tblhistory.TType2', "Order");
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			
			$this->db->where("TransDate2 BETWEEN '$from_date' AND '$to_date'");
			$this->db->group_by('DATE(tblhistory.TransDate2)');
			$this->db->order_by('tblhistory.TransDate2', 'ASC');
			return $this->db->get('tblhistory')->result_array();
		}
		
		
		//================== Get Month Wise Sale Or Sale return Reports ================
		public function GetMonthWiseSaleReport($filterdata = "")
		{
			$this->db->select('tblhistory.FY,tblhistory.TType2,Year(tblhistory.TransDate2) as year,MONTHNAME(tblhistory.TransDate2) as month,MONTH(tblhistory.TransDate2) as MonthNumber,SUM(tblhistory.NetChallanAmt) AS AmtSum');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID');
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			if($filterdata["state"]){
				$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID');
				$this->db->where_in('tblclients.state', $filterdata["state"]);
			}
			$this->db->where('tblhistory.PlantID', $filterdata["PlantID"]);
			$this->db->where_in('tblhistory.FY', $filterdata["FY"]);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.TType', $filterdata["TType"]);
			$this->db->order_by("YEAR(tblhistory.TransDate2)", "ASC");
			$this->db->order_by("MONTH(tblhistory.TransDate2)", "ASC");
			$this->db->group_by(array("year","month"));
			return $this->db->get('tblhistory')->result_array();
		}
		public function GetMonthWiseSaleReportNew($filterdata = "")
		{
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			
			$this->db->select('tblhistory.FY,tblhistory.TType2,Year(tblhistory.TransDate2) as year,MONTHNAME(tblhistory.TransDate2) as month,MONTH(tblhistory.TransDate2) as MonthNumber,SUM(tblhistory.NetChallanAmt) AS AmtSum');
			
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID');
			
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblhistory.PlantID', $filterdata["PlantID"]);
			$this->db->where_in('tblhistory.FY', $filterdata["FY"]);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.TType', $filterdata["TType"]);
			$this->db->order_by("YEAR(tblhistory.TransDate2)", "ASC");
			$this->db->order_by("MONTH(tblhistory.TransDate2)", "ASC");
			$this->db->group_by(array("year","month"));
			return $this->db->get('tblhistory')->result_array();
		}
		//================== Get Month Wise Top Selling Items ==========================
		public function GetMonthlyBestSellerItems($filterdata = "")
		{
			$this->db->select('tblhistory.ItemID,tblitems.description,Year(tblhistory.TransDate2) as year,MONTHNAME(tblhistory.TransDate2) as month,MONTH(tblhistory.TransDate2) as MonthNumber,SUM(tblhistory.NetChallanAmt) AS AmtSum');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID');
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			if($filterdata["state"]){
				$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID');
				$this->db->where_in('tblclients.state', $filterdata["state"]);
			}
			$this->db->where('tblhistory.PlantID', $filterdata["PlantID"]);
			$this->db->where_in('tblhistory.FY', $filterdata["FY"]);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.TType', $filterdata["TType"]);
			$this->db->where('MONTH(tblhistory.TransDate2)', $filterdata["Month"]);
			$this->db->order_by("AmtSum", "DESC");
			$this->db->limit(1);
			$this->db->group_by(array("ItemID"));
			return $this->db->get('tblhistory')->row();
		}
		
		//===================== Get Item Group List By Main GroupID ====================
		public function GetItemGroupList($ItemMainGrpID = "")
		{
			$this->db->select('tblitems_sub_groups.*');
			$this->db->where('main_group_id', $ItemMainGrpID);
			$this->db->order_by('tblitems_sub_groups.name', 'ASC');
			return $this->db->get('tblitems_sub_groups')->result_array();
		}
		public function GetItemListByGroup($ItemMainGrpID = "")
		{
			$this->db->select('tblitems.*');
			$this->db->where('MainGrpID', $ItemMainGrpID);
			$this->db->where('isactive', 'Y');
			$this->db->order_by('tblitems.description', 'ASC');
			return $this->db->get('tblitems')->result_array();
		}
		//===================== Get Distributor Type List  =============================
		public function GetDistributorTypeList()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblcustomers_groups.*');
			$this->db->where('PlantID', $selected_company);
			$this->db->order_by('tblcustomers_groups.name', 'ASC');
			return $this->db->get('tblcustomers_groups')->result_array();
		}
		//===================== Get Customer List  =====================================
		public function GetCustomerList()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblclients.*');
			//$this->db->join('tblaccountgroupssub', 'tblclients.SubActGroupID = tblaccountgroupssub.SubActGroupID');
			//Trade Payable
			$this->db->where('tblclients.SubActGroupID1', "100056");
			$this->db->where('tblclients.PlantID', $selected_company);
			$this->db->order_by('tblclients.company', 'ASC');
			return $this->db->get('tblclients')->result_array();
		}
		
		//=============== Get Customer Wise Sale Person List ===========================
		public function GetPartySalePerson()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblcustomer_admins.customer_id,tblstaff.*');
			$this->db->join('tblstaff', ' tblstaff.AccountID = tblcustomer_admins.staff_id');
			$this->db->where('tblcustomer_admins.company_id', $selected_company);
			$this->db->group_by('tblstaff.staffid');
			$this->db->order_by('tblstaff.firstname,tblstaff.lastname', 'ASC');
			return $this->db->get('tblcustomer_admins')->result_array();
		}
		
		//=============== Get Customer Route List ======================================
		public function GetRouteList()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblaccountroutes.AccountID,tblroute.*');
			$this->db->join('tblroute', 'tblroute.RouteID = tblaccountroutes.RouteID AND tblroute.PlantID = tblaccountroutes.PlantID');
			$this->db->where('tblaccountroutes.PlantID', $selected_company);
			$this->db->group_by('tblaccountroutes.RouteID');
			$this->db->order_by('tblroute.name', 'ASC');
			return $this->db->get('tblaccountroutes')->result_array();
		}
		
		//===================== Get Distributor Type List  =============================
		public function GetStateList()
		{
			$this->db->select('tblxx_statelist.*');
			$this->db->where('country_id', 1);
			$this->db->order_by('tblxx_statelist.state_name', 'ASC');
			return $this->db->get('tblxx_statelist')->result_array();
		}
		//===================== Get Item Group Wise Chart Report  ======================
		public function GetItemGroupWiseChartReport($data)
		{  
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			
			
			$custom_date_select = 'tblhistory.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
			AND tblhistory.FY = "'.$fy.'" AND  tblhistory.PlantID = '.$selected_company;
			$custom_date_select .= ' AND tblhistory.AccountID IS NOT NULL AND tblhistory.TType2 = "Order" AND tblhistory.TType = "O" AND tblhistory.TransID IS NOT NULL ';
			
			
			
			if(!empty($data["state"]))
			{
				$custom_date_select .= ' AND '.db_prefix().'clients.state="'.$data["state"].'" ';
			}
			if(!empty($data["city"]))
			{
				$custom_date_select .= ' AND '.db_prefix().'clients.city="'.$data["city"].'" ';
			}
			
			if($data["ReportIn"] == "1" || $data["ReportIn"] == "2"){
			    $this->db->select('tblitems.SubGrpID1,SUM(tblhistory.NetChallanAmt) AS NetSaleAmt,tblitems_sub_groups.name AS ItemGroupName');
				}else{
			    $this->db->select('tblitems.SubGrpID1,SUM(tblhistory.BilledQty) AS TotalBilledQty,tblitems_sub_groups.name AS ItemGroupName');
			}
			$this->db->from(db_prefix() . 'history');
			$this->db->join(db_prefix() . 'items', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID','INNER');
			$this->db->join(db_prefix() . 'items_sub_groups', 'tblitems_sub_groups.id = tblitems.SubGrpID1','INNER');
			$this->db->join(db_prefix() . 'clients', 'clients.AccountID = tblhistory.AccountID','INNER');
			if(!empty($data["SalesPerson"]))
			{
				$this->db->join(db_prefix() . 'customer_admins', 'tblcustomer_admins.customer_id = tblclients.AccountID','INNER');
			}
			if(!empty($data["Route"]))
			{
				$this->db->join(db_prefix() . 'accountroutes', 'tblaccountroutes.AccountID = tblclients.AccountID','INNER');
			}
			$this->db->where($custom_date_select);
			if(!empty($data["ItemGroup"]))
			{
			    $this->db->where_in('tblitems.SubGrpID1',$data["ItemGroup"]);
			}
			if(!empty($data["DistributorType"]))
			{
			    $this->db->where_in('tblclients.DistributorType',$data["DistributorType"]);
			}
			
			if(!empty($data["PartyName"]))
			{
			    $this->db->where_in('tblclients.AccountID',$data["PartyName"]);
			}
			if(!empty($data["Route"]))
			{
			    $this->db->where_in('tblaccountroutes.RouteID',$data["Route"]);
			}
			if(!empty($data["SalesPerson"]))
			{
			    $this->db->where_in('tblcustomer_admins.staff_id',$data["SalesPerson"]);
			}
			
			$this->db->group_by(db_prefix() . 'items.SubGrpID1');
			if($data["ReportIn"] == "1" || $data["ReportIn"] == "2"){
			    $this->db->order_by('NetSaleAmt', 'DESC');
				}else{
			    $this->db->order_by('TotalBilledQty', 'DESC');
			}
			
			return $this->db->get()->result_array();
			
		}
		public function GetItemGroupWiseTableChartReport($data)
		{  
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			
			
			$custom_date_select = 'tblhistory.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
			AND tblhistory.FY = "'.$fy.'" AND  tblhistory.PlantID = '.$selected_company;
			$custom_date_select .= ' AND tblhistory.AccountID IS NOT NULL AND tblhistory.TType2 = "Order" AND tblhistory.TType = "O" AND tblhistory.TransID IS NOT NULL ';
			
			
			
			if(!empty($data["state"]))
			{
				$custom_date_select .= ' AND '.db_prefix().'clients.state="'.$data["state"].'" ';
			}
			if(!empty($data["city"]))
			{
				$custom_date_select .= ' AND '.db_prefix().'clients.city="'.$data["city"].'" ';
			}
			
			if($data["ReportIn"] == "1" || $data["ReportIn"] == "2"){
			    $this->db->select('tblhistory.Transdate,tblitems.SubGrpID1,SUM(tblhistory.NetChallanAmt) AS NetSaleAmt,tblitems_sub_groups.name AS ItemGroupName');
				}else{
			    $this->db->select('tblhistory.Transdate,tblitems.SubGrpID1,SUM(tblhistory.BilledQty) AS TotalBilledQty,tblitems_sub_groups.name AS ItemGroupName');
			}
			$this->db->from(db_prefix() . 'history');
			$this->db->join(db_prefix() . 'items', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID','INNER');
			$this->db->join(db_prefix() . 'items_sub_groups', 'tblitems_sub_groups.id = tblitems.SubGrpID1','INNER');
			$this->db->join(db_prefix() . 'clients', 'clients.AccountID = tblhistory.AccountID','INNER');
			if(!empty($data["SalesPerson"]))
			{
				$this->db->join(db_prefix() . 'customer_admins', 'tblcustomer_admins.customer_id = tblclients.AccountID','INNER');
			}
			if(!empty($data["Route"]))
			{
				$this->db->join(db_prefix() . 'accountroutes', 'tblaccountroutes.AccountID = tblclients.AccountID','INNER');
			}
			$this->db->where($custom_date_select);
			if(!empty($data["ItemGroup"]))
			{
			    $this->db->where_in('tblitems.SubGrpID1',$data["ItemGroup"]);
			}
			if(!empty($data["DistributorType"]))
			{
			    $this->db->where_in('tblclients.DistributorType',$data["DistributorType"]);
			}
			
			if(!empty($data["PartyName"]))
			{
			    $this->db->where_in('tblclients.AccountID',$data["PartyName"]);
			}
			if(!empty($data["Route"]))
			{
			    $this->db->where_in('tblaccountroutes.RouteID',$data["Route"]);
			}
			if(!empty($data["SalesPerson"]))
			{
			    $this->db->where_in('tblcustomer_admins.staff_id',$data["SalesPerson"]);
			}
			
			$this->db->group_by('tblitems.SubGrpID1,DATE(tblhistory.TransDate)');
			if($data["ReportIn"] == "1" || $data["ReportIn"] == "2"){
			    $this->db->order_by('ItemGroupName,tblhistory.TransDate', 'ASC');
				}else{
			    $this->db->order_by('ItemGroupName,tblhistory.TransDate', 'ASC');
			}
			
			return $this->db->get()->result_array();
			
		}
		
		
		public function get_company_detail()
		{  	
			$selected_company = $this->session->userdata('root_company');
			$sql ='SELECT '.db_prefix().'rootcompany.*
			FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
			$result = $this->db->query($sql)->row();
			return $result;
		}
		public function GetGodownDAccountList_tableata()
		{
			$PlantID = $this->session->userdata('root_company');
			$this->db->where('PlantID', $PlantID);
			$this->db->order_by(db_prefix() . 'godownmaster.Type,'.db_prefix() . 'godownmaster.AccountID', 'ASC');
			return $this->db->get(db_prefix().'godownmaster')->result_array();
		}
		public function GetPlantDetails()
		{   
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			
			$sql ='SELECT '.db_prefix().'setup.*
			FROM '.db_prefix().'setup WHERE PlantID = '.$selected_company.' AND FY = "'.$FY.'"';
			$result = $this->db->query($sql)->row();
			return $result;
		}
		// get Account List
		function AccountList($postData){
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$where_clients = '';
			if(isset($postData['search']) ){
				$q = $postData['search'];
				
				$this->db->select(db_prefix() . 'clients.*,' . db_prefix() . 'xx_citylist.city_name');
				$where_clients .= '(company LIKE "%' . $q . '%" ESCAPE \'!\' OR StationName LIKE "%' . $q . '%" ESCAPE \'!\' OR address LIKE "%' . $q. '%" ESCAPE \'!\' OR Address3 LIKE "%' . $q . '%" ESCAPE \'!\')  AND ' . db_prefix() . 'clients.SubActGroupID1 IN("100056")';
				$this->db->join(db_prefix() . 'xx_citylist', '' . db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'clients.city', 'left');
				$this->db->where($where_clients);
				$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
				$records = $this->db->get(db_prefix() . 'clients')->result();
				foreach($records as $row ){
					$response[] = array("label"=>$row->company,"value"=>$row->AccountID,"address"=>$row->address,"Address3"=>$row->Address3,"StationName"=>$row->StationName,"state"=>$row->state,"CityName"=>$row->city_name);
				}
			}
			return $response;
		}
		
		// AccountList List
		public function AccountList_table(){
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblclients.*');
			$this->db->where('tblclients.PlantID ', $selected_company);
			$this->db->where('tblclients.SubActGroupID1 ', '100056');
			$this->db->order_by('tblclients.company','ASC');
			return $this->db->get('tblclients')->result_array();
		}
		// Get Party wise Transaction Bill Report
		public function GetBillsReceivableBodyData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// $sql = 'SELECTT '.db_prefix() . 'salesmaster.*,tblclients.company,COALESCE(tblclients.credit_limit, 0) AS MaxDays,(SELECT COALESCE(SUM(Amount), 0) as PaidAmt 
			// FROM `tblaccountledger` 
			// WHERE bill_no = tblsalesmaster.SalesID AND TType="C") as PaidAmt FROM '.db_prefix() . 'salesmaster 
			// INNER JOIN tblclients ON tblclients.AccountID = tblsalesmaster.AccountID
			// WHERE '.db_prefix() . 'salesmaster.FY = '.$fy.'  AND '.db_prefix() . 'salesmaster.PlantID = '.$selected_company.'
			// AND tblsalesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
			// ORDER BY tblsalesmaster.AccountID,tblsalesmaster.Transdate DESC';
			
			$sql = 'SELECT 
			s.*,
			c.company,
			COALESCE(c.credit_days, 0) AS MaxDays,
			COALESCE(al.PaidAmt, 0) AS PaidAmt,
			COALESCE(al2.CDNoteAmt, 0) AS CDNoteAmt,
			COALESCE(al3.SaleRtnAmt, 0) AS SaleRtnAmt,
			COALESCE(al5.JournalDebitAmt, 0) AS JournalDebitAmt,
			COALESCE(al4.JournalCreditAmt, 0) AS JournalCreditAmt
			FROM 
			tblsalesmaster s
			INNER JOIN 
			tblclients c ON c.AccountID = s.AccountID
			LEFT JOIN 
			(SELECT AccountID,BillNo, SUM(Amount) AS PaidAmt 
			FROM tblaccountledger 
			WHERE TType = "C" AND PassedFrom = "RECEIPTS" 
			GROUP BY BillNo) al 
			ON al.BillNo = s.SalesID AND al.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS CDNoteAmt FROM tblaccountledger WHERE TType = "C" AND PassedFrom ="CDNOTE" GROUP BY BillNo) al2 ON al2.BillNo = s.SalesID AND al2.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS SaleRtnAmt FROM tblaccountledger WHERE TType = "C" AND PassedFrom ="SALESRTN" GROUP BY BillNo) al3 ON al3.BillNo = s.SalesID AND al3.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS JournalCreditAmt FROM tblaccountledger WHERE TType = "C" AND PassedFrom ="JOURNAL" GROUP BY BillNo) al4 ON al4.BillNo = s.SalesID AND al4.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS JournalDebitAmt FROM tblaccountledger WHERE TType = "D" AND PassedFrom ="JOURNAL" GROUP BY BillNo) al5 ON al5.BillNo = s.SalesID AND al5.AccountID = s.AccountID
			WHERE 
			s.FY = '.$fy.' 
			AND s.PlantID = '.$selected_company.'
			AND s.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
			ORDER BY 
			s.AccountID, s.Transdate DESC;
			';
			
			$result = $this->db->query($sql)->result_array();
			// foreach($result as &$each)
			// {
			
			// $sql = 'SELECT COALESCE(SUM(Amount), 0) as PaidAmt 
			// FROM `tblaccountledger` 
			// WHERE bill_no = "'.$each['SalesID'].'" AND TType="C"';
			
			// $data = $this->db->query($sql)->row_array();
			// $each['PaidAmt'] = $data['PaidAmt'];
			// }
			return $result;
		}
		
		public function GetAllCityList()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT * FROM tblxx_citylist WHERE status = "1" Order By city_name ASC';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function GetAllStationList()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT * FROM tblStationMaster WHERE status = "1" Order By StationName ASC';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//=================== Get All Party List =======================================
		public function GetAllPartyList()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT tblclients.AccountID,tblclients.company,tblaccountlocations.LocationTypeID FROM tblclients 
			LEFT JOIN tblaccountlocations ON tblaccountlocations.AccountID = tblclients.AccountID AND tblaccountlocations.PlantID = tblclients.PlantID
			WHERE tblclients.SubActGroupID1 = "100056" AND tblclients.PlantID = "'.$selected_company.'" 
			AND tblclients.Blockyn = "N" Order By tblaccountlocations.LocationTypeID ASC';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//===================== Get OParty List By Filter data =====================
		public function GetPartyListByTradeType($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$FromDate = to_sql_date($data["FromDate"])." 00:00:00";
			$ToDate = to_sql_date($data["ToDate"])." 23:59:59";
			$TradeType = $data["TradeType"];
			$sql = 'SELECT tblsalesmaster.AccountID,tblclients.company 
			FROM `tblsalesmaster` 
			INNER JOIN tblclients ON tblclients.AccountID = tblsalesmaster.AccountID 
			WHERE tblsalesmaster.Transdate BETWEEN "'.$FromDate.'" AND "'.$ToDate.'" AND
			tblclients.SubActGroupID1 = "100056" AND tblclients.PlantID = "'.$selected_company.'"';
			if($TradeType){
				$sql .= ' AND tblclients.Trade_Type = "'.$TradeType.'" ';
			}
			$sql .= ' GROUP BY tblsalesmaster.AccountID ORDER BY tblclients.company';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//======================= Get Party City List By Filter ====================
		public function GetPartyCityListByFilter($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$FromDate = to_sql_date($data["FromDate"])." 00:00:00";
			$ToDate = to_sql_date($data["ToDate"])." 23:59:59";
			$TradeType = $data["TradeType"];
			$sql = 'SELECT tblclients.city,tblxx_citylist.city_name 
			FROM `tblsalesmaster` 
			INNER JOIN tblclients ON tblclients.AccountID = tblsalesmaster.AccountID 
			INNER JOIN tblxx_citylist ON tblxx_citylist.id = tblclients.city 
			WHERE tblsalesmaster.Transdate BETWEEN "'.$FromDate.'" AND "'.$ToDate.'" AND
			tblclients.SubActGroupID1 = "100056" AND tblclients.PlantID = "'.$selected_company.'"';
			if($TradeType){
				$sql .= ' AND tblclients.Trade_Type = "'.$TradeType.'" ';
			}
			$sql .= ' GROUP BY tblclients.city ORDER BY tblxx_citylist.city_name';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		//==================== Get Party Station List By Filter ====================
		public function GetPartyStationListByFilter($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$FromDate = to_sql_date($data["FromDate"])." 00:00:00";
			$ToDate = to_sql_date($data["ToDate"])." 23:59:59";
			$TradeType = $data["TradeType"];
			$sql = 'SELECT tblStationMaster.id AS StationID,tblStationMaster.StationName 
			FROM `tblsalesmaster` 
			INNER JOIN tblclients ON tblclients.AccountID = tblsalesmaster.AccountID 
			INNER JOIN tblStationMaster ON tblStationMaster.id = tblclients.StationName 
			WHERE tblsalesmaster.Transdate BETWEEN "'.$FromDate.'" AND "'.$ToDate.'" AND
			tblclients.SubActGroupID1 = "100056" AND tblclients.PlantID = "'.$selected_company.'"';
			if($TradeType){
				$sql .= ' AND tblclients.Trade_Type = "'.$TradeType.'" ';
			}
			$sql .= ' GROUP BY tblclients.StationName ORDER BY tblStationMaster.StationName';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//===================== Get Ledger before From Date for All Party ==============
		public function GetPreLedgerEntry($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($data["from_date"]);
			if($from_date > date('20'.$FY.'-04-01') ){
				$NewFromDate = date('20'.$FY.'-04-01');
				$NewToDate = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
				
				$sql = 'SELECT tblaccountledger.AccountID,tblaccountledger.TType,SUM(tblaccountledger.Amount) AS TotalAmt FROM tblaccountledger 
				INNER JOIN tblclients ON tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
				WHERE tblclients.SubActGroupID1 = "100056" AND tblclients.PlantID = "'.$selected_company.'" AND tblclients.Blockyn = "N" AND 
				tblaccountledger.FY = "'.$fy.'" AND tblaccountledger.Transdate BETWEEN "'.$NewFromDate.' 00:00:00" AND "'.$NewToDate.' 23:59:59" 
				GROUP BY tblaccountledger.AccountID,tblaccountledger.TType';
				$result = $this->db->query($sql)->result_array();
				return $result;
			}
		}
		//================== Get All Ledger Entry Beetween Date ========================
		public function GetLedgerEntry($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($data["from_date"]);
			
			$sql = 'SELECT tblaccountledger.AccountID,tblaccountledger.TType,SUM(tblaccountledger.Amount) AS TotalAmt,tblaccountledger.PassedFrom
			FROM tblaccountledger 
			INNER JOIN tblclients ON tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
			WHERE tblclients.SubActGroupID1 = "100056" AND tblclients.PlantID = "'.$selected_company.'" AND tblclients.Blockyn = "N" AND 
			tblaccountledger.FY = "'.$fy.'" AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59" 
			GROUP BY tblaccountledger.AccountID,tblaccountledger.TType,tblaccountledger.PassedFrom';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//================== Get Opening Balance for All Party =========================
		public function GetPartyOpnBal()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT tblaccountbalances.AccountID,tblaccountbalances.BAL1 FROM tblaccountbalances 
			INNER JOIN tblclients ON tblclients.AccountID = tblaccountbalances.AccountID AND tblclients.PlantID = tblaccountbalances.PlantID
			WHERE tblclients.SubActGroupID1 = "100056" AND tblclients.PlantID = "'.$selected_company.'" AND tblclients.Blockyn = "N" AND 
			tblaccountbalances.FY = "'.$fy.'"';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function load_data_daily_sale($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = ''.db_prefix().'clients.SubActGroupID1 ="100056" AND '.db_prefix().'clients.Blockyn="N" AND '.db_prefix().'clients.PlantID="'.$selected_company.'" ORDER BY tblaccountlocations.LocationTypeID,'.db_prefix().'clients.company ASC';
			
			$sql ='SELECT '.db_prefix().'clients.company,tblclients.AccountID,COALESCE(tblaccountlocations.LocationTypeID,"") AS location_name,tblaccountbalances.BAL1,
			(SELECT COALESCE(SUM(Amount),0) FROM '.db_prefix().'accountledger WHERE '.db_prefix().'accountledger.PassedFrom = "SALE" AND '.db_prefix().'accountledger.AccountID = tblclients.AccountID AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59") as Total_Sale,
			(SELECT COALESCE(SUM(Amount),0) FROM '.db_prefix().'accountledger WHERE '.db_prefix().'accountledger.PassedFrom = "SALESRTN" AND '.db_prefix().'accountledger.AccountID = tblclients.AccountID AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59") as Total_Sale_rtn,
			(SELECT COALESCE(SUM(Amount),0) FROM '.db_prefix().'accountledger WHERE '.db_prefix().'accountledger.PassedFrom = "CDNOTE" AND '.db_prefix().'accountledger.AccountID = tblclients.AccountID AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59" AND TType = "C") as Total_creditnote,
			(SELECT COALESCE(SUM(Amount),0) FROM '.db_prefix().'accountledger WHERE '.db_prefix().'accountledger.PassedFrom = "CDNOTE" AND '.db_prefix().'accountledger.AccountID = tblclients.AccountID AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59" AND TType = "D") as Total_debitnote,
			(SELECT COALESCE(SUM(Amount),0) FROM '.db_prefix().'accountledger WHERE '.db_prefix().'accountledger.PassedFrom = "RECEIPTS" AND '.db_prefix().'accountledger.AccountID = tblclients.AccountID AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59") as Total_RECEIPTS
			FROM '.db_prefix().'clients
			LEFT JOIN tblaccountbalances ON tblaccountbalances.AccountID = tblclients.AccountID AND tblaccountbalances.PlantID = tblclients.PlantID AND tblaccountbalances.FY="'.$fy.'"
			LEFT JOIN tblaccountlocations ON tblaccountlocations.AccountID = tblclients.AccountID AND tblaccountlocations.PlantID = tblclients.PlantID
			WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			foreach($result as &$each)
			{
				$each['acctbal'] = $this->get_accbal($each['AccountID'],$selected_company,$fy,$from_date);
			}
			return $result;
		}
		
		public function get_accbal($AccountID,$PlantID,$FY,$from_date){
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			// $passedfrom = array('SALE', 'RECEIPTS', 'SALESRTN', 'CDNOTE');
			// $passedfrom_list = '"' . implode('","', $passedfrom) . '"';
			// AND PassedFrom IN ('.$passedfrom_list.') 
			$Obal = 0;
			if($from_date > date('20'.$FY.'-04-01') ){
				$NewFromDate = date('20'.$FY.'-04-01');
				$NewToDate = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
				$sql = '';
				$sql .= 'SELECT SUM(Amount) as dramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';
				$sql .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'" 
				AND tblaccountledger.Transdate BETWEEN "'.$NewFromDate.' 00:00:00" AND "'.$NewToDate.' 23:59:59" AND tblaccountledger.TType = "D"';
				$result1 = $this->db->query($sql)->row();
				
				$sql2 = '';
				$sql2 .= 'SELECT SUM(Amount) as cramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';
				$sql2 .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'"
				AND tblaccountledger.Transdate BETWEEN "'.$NewFromDate.' 00:00:00" AND "'.$NewToDate.' 23:59:59" AND tblaccountledger.TType = "C"';
				$result2 = $this->db->query($sql2)->row();
				
				$sql3 = '';
				$sql3 .= 'SELECT BAL1 FROM `tblaccountbalances`';
				$sql3 .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountbalances.PlantID = '.$selected_company.' AND tblaccountbalances.FY = "'.$FY.'"';
				$result3 = $this->db->query($sql3)->row();
				if(empty($result3)){
					
					}else{
					$Obal = $result3->BAL1;
				}
				$bal = $Obal + $result1->dramt_sum - $result2->cramt_sum;
				}else{
				$bal = 0;
			}
			return $bal;
		}
		
		public function load_data_daily_ItemWise_sale_report($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// $sql1 = ''.db_prefix().'items.PlantID ='.$selected_company.' ORDER BY '.db_prefix().'items.description ASC';
			
			// $sql ='SELECT '.db_prefix().'items.description,tblitems.item_code,
			// (SELECT COALESCE(SUM(BilledQty/CaseQty),0) FROM '.db_prefix().'history 
			// WHERE '.db_prefix().'history.TType = "O" AND '.db_prefix().'history.TType2 = "Order" AND '.db_prefix().'history.ItemID = tblitems.item_code AND '.db_prefix().'history.PlantID = tblitems.PlantID 
			// AND tblhistory.TransID IS NOT NULL AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59") as Total_Sale,
			// (SELECT COALESCE(SUM(BilledQty),0) FROM '.db_prefix().'history 
			// WHERE '.db_prefix().'history.TType = "O" AND '.db_prefix().'history.TType2 = "Order" AND '.db_prefix().'history.ItemID = tblitems.item_code AND '.db_prefix().'history.PlantID = tblitems.PlantID 
			// AND tblhistory.TransID IS NOT NULL AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59") as Total_unit,
			// (SELECT COALESCE(Sum(BilledQty)*tblitems.weight,0) FROM '.db_prefix().'history 
			// WHERE '.db_prefix().'history.TType = "O" AND '.db_prefix().'history.TType2 = "Order" AND '.db_prefix().'history.ItemID = tblitems.item_code AND '.db_prefix().'history.PlantID = tblitems.PlantID 
			// AND tblhistory.TransID IS NOT NULL AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59") as Total_weight
			// FROM '.db_prefix().'items
			// WHERE '.$sql1;
			$sql ='select b.description,c.name ItemGroup,e.name Division, Sum(BilledQty) "Total_unit",Sum(BilledQty)/Avg(CaseQty) "Total_Sale",Avg(SaleRate) Rate,Sum(NetChallanAmt) Amount,Sum(BilledQty)*Avg(b.weight) Total_weight FROM tblhistory as a 
			LEFT join tblitems as b on b.PlantID=a.PlantID and b.item_code=a.ItemID
			LEFT join tblitems_sub_groups as c on c.id=b.SubGrpID1 
			LEFT join tblitems_groups as e on e.id=b.group_id 
			where a.PlantID=1 and a.FY="'.$fy.'" AND a.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$from_date.' 23:59:59" and TType="o" and TType2="Order" and transId is not null GROUP BY b.description,c.name,e.name ORDER by e.name,c.name,b.description asc;';
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
		}
		
		// get Item List
		function ItemList($postData){
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$where_clients = '';
			if(isset($postData['search']) ){
				$q = $postData['search'];
				
				$this->db->select(db_prefix() . 'items.*');
				$where_clients .= '(item_code LIKE "%' . $q . '%" ESCAPE \'!\' OR description LIKE "%' . $q . '%" ESCAPE \'!\' )';
				$this->db->where($where_clients);
				$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
				$records = $this->db->get(db_prefix() . 'items')->result();
				foreach($records as $row ){
					$response[] = array("label"=>$row->description,"value"=>$row->item_code);
				}
			}
			return $response;
		}
		
		public function GetItemDetails($ItemID)
		{  
			$selected_company = $this->session->userdata('root_company');
			$sql ='SELECT '.db_prefix().'items.*
			FROM '.db_prefix().'items WHERE item_code = "'.$ItemID.'" AND PlantID = '.$selected_company;
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		public function GetAccountDetails($AccountID)
		{  
			$selected_company = $this->session->userdata('root_company');
			$sql ='SELECT '.db_prefix().'clients.*,tblxx_citylist.city_name 
			FROM '.db_prefix().'clients 
			LEFT JOIN tblxx_citylist ON tblxx_citylist.id = tblclients.city  WHERE AccountID = "'.$AccountID.'" AND PlantID = '.$selected_company;
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		public function GetItemRate($ItemID)
		{  
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == '1'){
				$distID = '1';
				}else if($selected_company == '2'){
				$distID = '13';
				}else if($selected_company == '3'){
				$distID = '21';
			}
			$sql ='SELECT '.db_prefix().'rate_master.SaleRate
			FROM '.db_prefix().'rate_master WHERE item_id = "'.$ItemID.'" AND state_id = "UP" AND distributor_id = "'.$distID.'" AND PlantID = '.$selected_company;
			$result = $this->db->query($sql)->row();
			return $result;
		}
		// Item Wise Stock report
		public function GetItemWiseStockReport($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
			$ItemID = $filterdata["ItemID"];
			$GodownID = $filterdata["GodownID"];
			
			if($GodownID !==''){
				$sql = 'SELECT tblhistory.TransDate2, tblhistory.ItemID,tblhistory.TType,tblhistory.TType2,tblhistory.CaseQty,tblhistory.SaleRate,
				SUM(tblhistory.BilledQty) AS Qty,SUM(tblhistory.NetChallanAmt) AS AmtSum,tblitems.item_code,tblitems.description,tblstockmaster.OQty,tblhistory.SuppliedIn 
				FROM `tblhistory` 
				INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				INNER JOIN tblstockmaster ON tblstockmaster.ItemID = tblhistory.ItemID AND tblstockmaster.PlantID = tblhistory.PlantID AND tblstockmaster.FY = tblhistory.FY AND tblstockmaster.GodownID = tblhistory.GodownID
				WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'"  
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND 
				tblhistory.ItemID = "'.$ItemID.'" AND tblhistory.BillID IS NOT NULL  AND tblstockmaster.GodownID = "'.$GodownID.'" AND tblhistory.GodownID = "'.$GodownID.'" 
				GROUP BY tblhistory.TType,tblhistory.TType2, DATE(tblhistory.TransDate2)   
				ORDER BY tblhistory.TransDate2 ASC'; 
				}else{
				$sql = 'SELECT tblhistory.TransDate2, tblhistory.ItemID,tblhistory.TType,tblhistory.TType2,tblhistory.CaseQty,tblhistory.SaleRate,
				SUM(tblhistory.BilledQty) AS Qty,SUM(tblhistory.NetChallanAmt) AS AmtSum,tblitems.item_code,tblitems.description,tblhistory.SuppliedIn,
				(SELECT SUM(tblstockmaster.OQty) FROM tblstockmaster WHERE tblstockmaster.ItemID=tblhistory.ItemID AND tblstockmaster.PlantID = '.$selected_company.' AND tblstockmaster.FY = "'.$fy.'" AND tblstockmaster.cnfid = "1" GROUP BY tblstockmaster.ItemID,tblstockmaster.PlantID,tblstockmaster.FY) AS OQty
				FROM `tblhistory` 
				INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'"  
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND 
				tblhistory.ItemID = "'.$ItemID.'" AND tblhistory.BillID IS NOT NULL 
				GROUP BY tblhistory.TType,tblhistory.TType2, DATE(tblhistory.TransDate2)   
				ORDER BY tblhistory.TransDate2 ASC';
			}
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function GetItemWiseStockReportOQty($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
			$ItemID = $filterdata["ItemID"];
			$GodownID = $filterdata["GodownID"];
			if($from_date == "2022-04-01"){
				$day_before = '2022-04-01 23:59:59';
				}else{
				$day_before = date( 'Y-m-d', strtotime( $from_date . ' -1 day' ) ).' 23:59:59';
			}
			$first_date = '2022-04-01 00:00:00';
			
			if($GodownID !==''){
				$sql = 'SELECT tblhistory.TransDate2, tblhistory.ItemID,tblhistory.TType,tblhistory.TType2,tblhistory.CaseQty,
				SUM(tblhistory.BilledQty) AS Qty,tblitems.item_code,tblitems.description,tblstockmaster.OQty,tblhistory.SuppliedIn 
				FROM `tblhistory` 
				INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				INNER JOIN tblstockmaster ON tblstockmaster.ItemID = tblhistory.ItemID AND tblstockmaster.PlantID = tblhistory.PlantID AND tblstockmaster.FY = tblhistory.FY AND tblstockmaster.GodownID = tblhistory.GodownID
				WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'"  
				AND tblhistory.TransDate2 BETWEEN "'.$first_date.'" AND "'.$day_before.'" AND 
				tblhistory.ItemID = "'.$ItemID.'" AND tblhistory.BillID IS NOT NULL AND tblstockmaster.GodownID = "'.$GodownID.'" AND tblhistory.GodownID = "'.$GodownID.'"  
				GROUP BY tblhistory.TType,tblhistory.TType2, DATE(tblhistory.TransDate2)  
				ORDER BY tblhistory.TransDate2 ASC';
				}else{
				$sql = 'SELECT tblhistory.TransDate2, tblhistory.ItemID,tblhistory.TType,tblhistory.TType2,tblhistory.CaseQty,
				SUM(tblhistory.BilledQty) AS Qty,tblitems.item_code,tblitems.description,tblhistory.SuppliedIn,
				(SELECT SUM(tblstockmaster.OQty) FROM tblstockmaster WHERE tblstockmaster.ItemID=tblhistory.ItemID AND tblstockmaster.PlantID = '.$selected_company.' AND tblstockmaster.FY = "'.$fy.'" AND tblstockmaster.cnfid = "1" GROUP BY tblstockmaster.ItemID,tblstockmaster.PlantID,tblstockmaster.FY) AS OQty
				FROM `tblhistory` 
				INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'"  
				AND tblhistory.TransDate2 BETWEEN "'.$first_date.'" AND "'.$day_before.'" AND 
				tblhistory.ItemID = "'.$ItemID.'" AND tblhistory.BillID IS NOT NULL 
				GROUP BY tblhistory.TType,tblhistory.TType2, DATE(tblhistory.TransDate2)  
				ORDER BY tblhistory.TransDate2 ASC';
			}
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		// Party Item Wise Report body data
		public function GetPartyItemWiseBodyData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$TransType = $filterdata["TransType"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($TransType =='1'){
				$sql = 'SELECT '.db_prefix() . 'history.*,'.db_prefix() . 'items.description,Sum('.db_prefix() . 'history.NetChallanAmt) AS ItemValue,Sum('.db_prefix() . 'history.BilledQty) AS BilledQty,tblitems.SubGrpID1 AS subgroup_id
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("O") AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				}else if($TransType =='2'){
				$sql = 'SELECT '.db_prefix() . 'history.*,'.db_prefix() . 'items.description,Sum('.db_prefix() . 'history.NetChallanAmt) AS ItemValue,Sum('.db_prefix() . 'history.BilledQty) AS BilledQty,tblitems.SubGrpID1 AS subgroup_id
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType = "R" AND tblhistory.TType2 = "Fresh" AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				}else if($TransType =='3'){
				$sql = 'SELECT '.db_prefix() . 'history.*,'.db_prefix() . 'items.description,Sum('.db_prefix() . 'history.NetChallanAmt) AS ItemValue,Sum('.db_prefix() . 'history.BilledQty) AS BilledQty,tblitems.SubGrpID1 AS subgroup_id
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("R","D") AND tblhistory.TType2 = "Damage" AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				}else if($TransType =='4'){
				
				$AllItemList = 'SELECT '.db_prefix() . 'history.ItemID,'.db_prefix() . 'items.description,tblitems.SubGrpID1 AS subgroup_id
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("O","R","D") AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				$AllItems = $this->db->query($AllItemList)->result_array();
				
				
				$sql1 = 'SELECT '.db_prefix() . 'history.*,Sum('.db_prefix() . 'history.NetChallanAmt) AS ItemValue,Sum('.db_prefix() . 'history.BilledQty) AS BilledQty FROM '.db_prefix() . 'history 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("O") AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				$result1 = $this->db->query($sql1)->result_array();
				
				$sql2 = 'SELECT '.db_prefix() . 'history.*,Sum('.db_prefix() . 'history.NetChallanAmt) AS ItemValue,Sum('.db_prefix() . 'history.BilledQty) AS BilledQty FROM '.db_prefix() . 'history 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType = "R" AND tblhistory.TType2 = "Fresh" AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				$result2 = $this->db->query($sql2)->result_array();
				
				$sql3 = 'SELECT '.db_prefix() . 'history.*,Sum('.db_prefix() . 'history.NetChallanAmt) AS ItemValue,Sum('.db_prefix() . 'history.BilledQty) AS BilledQty FROM '.db_prefix() . 'history 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("R","D") AND tblhistory.TType2 = "Damage" AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				$result3 = $this->db->query($sql3)->result_array();
				
				$ResultData = array();
				$i = 0;
				foreach($AllItems as $key => $value){
					$ResultData[$i]['ItemID'] = $value["ItemID"];
					$ResultData[$i]['description'] = $value["description"];
					$suplIn = "";
					$CaseQty = "";
					$SaleRate = "";
					$BilledQty = 0;
					$ItemValue = 0;
					
					// for Order
					foreach ($result1 as $key1 => $value1) {
						if($value["ItemID"] == $value1["ItemID"]){
							$suplIn = $value1["SuppliedIn"];
							$CaseQty = $value1["CaseQty"];
							$SaleRate = $value1["SaleRate"];
							$BilledQty = $value1["BilledQty"];
							$ItemValue = $value1["ItemValue"];
						}
					}
					// for Sale return as fresh
					foreach ($result2 as $key2 => $value2) {
						if($value["ItemID"] == $value2["ItemID"]){
							$suplIn = $value2["SuppliedIn"];
							$CaseQty = $value2["CaseQty"];
							$SaleRate = $value2["SaleRate"];
							$BilledQty -= $value2["BilledQty"];
							$ItemValue -= $value2["ItemValue"];
						}
					}
					
					// for Sale return as damage
					foreach ($result3 as $key3 => $value3) {
						if($value["ItemID"] == $value3["ItemID"]){
							$suplIn = $value3["SuppliedIn"];
							$CaseQty = $value3["CaseQty"];
							$SaleRate = $value3["SaleRate"];
							$BilledQty -= $value3["BilledQty"];
							$ItemValue -= $value3["ItemValue"];
						}
					}
					$ResultData[$i]['SuppliedIn'] = $suplIn;
					$ResultData[$i]['CaseQty'] = $CaseQty;
					$ResultData[$i]['SaleRate'] = $SaleRate;
					$ResultData[$i]['BilledQty'] = $BilledQty;
					$ResultData[$i]['ItemValue'] = $ItemValue;
					$i++;
					
				}
				return $ResultData;
			}
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function GetPartyWiseItemSubGroupData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$TransType = $filterdata["TransType"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($TransType =='1'){
				$sql = 'SELECT '.db_prefix() . 'history.ItemID,tblitems.SubGrpID1 AS subgroup_id,tblitems_sub_groups.name AS ItemSubGrpName
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				INNER JOIN tblitems_sub_groups ON tblitems_sub_groups.id = tblitems.SubGrpID1 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("O") AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblitems.SubGrpID1 ORDER BY tblitems.SubGrpID1';
				}else if($TransType =='2'){
				$sql = 'SELECT '.db_prefix() . 'history.ItemID,tblitems.SubGrpID1 AS subgroup_id,tblitems_sub_groups.name AS ItemSubGrpName 
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID
				INNER JOIN tblitems_sub_groups ON tblitems_sub_groups.id = tblitems.SubGrpID1 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType = "R" AND tblhistory.TType2 = "Fresh" AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblitems.SubGrpID1 ORDER BY tblitems.SubGrpID1';
				}else if($TransType =='3'){
				$sql = 'SELECT '.db_prefix() . 'history.ItemID,tblitems.SubGrpID1 AS subgroup_id,tblitems_sub_groups.name AS ItemSubGrpName 
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				INNER JOIN tblitems_sub_groups ON tblitems_sub_groups.id = tblitems.SubGrpID1 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("R","D") AND tblhistory.TType2 = "Damage" AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblitems.SubGrpID1 ORDER BY tblitems.SubGrpID1';
				}else if($TransType =='4'){
				
				$sql = 'SELECT '.db_prefix() . 'history.ItemID,tblitems.SubGrpID1 AS subgroup_id,tblitems_sub_groups.name AS ItemSubGrpName
				FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'
				AND tblhistory.TType IN("O","R","D") AND tblhistory.AccountID = "'.$AccountID.'" 
				AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblitems.SubGrpID1 ORDER BY tblitems.SubGrpID1';
				
			}
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function GetSaleVsSaleRtnBodyRowData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$locType = $filterdata["locType"];
			$repType = $filterdata["repType"];
			$Subgroup2 = $filterdata["Subgroup2"];
			
			$where_subgroup = ''; // Empty default
			
			if (!empty($Subgroup2) && is_array($Subgroup2)) {
				// Sanitize and convert to comma-separated string
				$Subgroup2_str = implode(',', array_map('intval', $Subgroup2));
				$where_subgroup = " AND tblitems.SubGrpID2 IN ($Subgroup2_str)";
			}
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($AccountID !==''){
				$sql = 'SELECT '.db_prefix() . 'history.ItemID,'.db_prefix() . 'history.TType,'.db_prefix() . 'history.TType2,'.db_prefix() . 'clients.AccountID FROM '.db_prefix() . 'history 
				INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'  AND tblhistory.TType IN("O","R") AND tblclients.AccountID = "'.$AccountID.'" '.$where_subgroup.' AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
				}else{
				if($repType == '2'){
					$sql = 'SELECT '.db_prefix() . 'history.ItemID,'.db_prefix() . 'history.TType,'.db_prefix() . 'history.TType2,'.db_prefix() . 'clients.AccountID FROM '.db_prefix() . 'history 
					INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID 
					INNER JOIN  tblaccountlocations ON  tblaccountlocations.AccountID = tblhistory.AccountID AND  tblaccountlocations.PlantID = tblhistory.PlantID 
					INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
					WHERE '.db_prefix() . 'history.FY = '.$fy.' AND '.db_prefix() . 'history.PlantID = '.$selected_company.'  AND tblhistory.TType IN("O","R")  '.$where_subgroup.'  AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" ';
					if($locType){
						$sql .= ' AND '.db_prefix() . 'accountlocations.LocationTypeID = "'.$locType.'"';
					}
					$sql .= ' Group By tblhistory.ItemID ORDER BY tblhistory.ItemID';
					}else {
					$sql = 'SELECT '.db_prefix() . 'history.ItemID,'.db_prefix() . 'history.TType,'.db_prefix() . 'history.TType2,'.db_prefix() . 'clients.AccountID FROM '.db_prefix() . 'history 
					INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID 
					INNER JOIN  tblaccountlocations ON  tblaccountlocations.AccountID = tblhistory.AccountID AND  tblaccountlocations.PlantID = tblhistory.PlantID 
					WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.' AND tblhistory.TType IN("O","R") AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" ';
					if($locType){
						$sql .= ' AND '.db_prefix() . 'accountlocations.LocationTypeID = "'.$locType.'"';
					}
					$sql .= ' Group By tblhistory.AccountID ORDER BY tblhistory.AccountID';
				}
			}
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function GetSaleVsSaleRtnBodyData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$locType = $filterdata["locType"];
			$repType = $filterdata["repType"];
			$Subgroup2 = $filterdata["Subgroup2"];
			
			$where_subgroup = ''; // Empty default
			
			if (!empty($Subgroup2) && is_array($Subgroup2)) {
				// Sanitize and convert to comma-separated string
				$Subgroup2_str = implode(',', array_map('intval', $Subgroup2));
				$where_subgroup = " AND tblitems.SubGrpID2 IN ($Subgroup2_str)";
			}
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($AccountID !==''){
				$sql = 'SELECT '.db_prefix() . 'history.ItemID,'.db_prefix() . 'history.CaseQty,SUM('.db_prefix() . 'history.NetChallanAmt) AS NetChallanAmt,SUM('.db_prefix() . 'history.BilledQty) AS BilledQty,'.db_prefix() . 'history.TType,'.db_prefix() . 'history.TType2,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.StationName,'.db_prefix() . 'items.description FROM '.db_prefix() . 'history 
				INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'  AND tblhistory.TType IN("O","R") AND tblhistory.TransID IS NOT NULL AND tblclients.AccountID = "'.$AccountID.'" '.$where_subgroup.'  AND tblhistory.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID,tblhistory.TType,tblhistory.TType2 ORDER BY tblhistory.ItemID';
				}else{
				if($repType == '2'){
					$sql = 'SELECT '.db_prefix() . 'history.ItemID,'.db_prefix() . 'history.CaseQty,SUM('.db_prefix() . 'history.NetChallanAmt) AS NetChallanAmt,SUM('.db_prefix() . 'history.BilledQty) AS BilledQty,'.db_prefix() . 'history.TType,'.db_prefix() . 'history.TType2,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.StationName,'.db_prefix() . 'items.description FROM '.db_prefix() . 'history 
					INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID 
					LEFT JOIN  tblaccountlocations ON  tblaccountlocations.AccountID = tblhistory.AccountID AND  tblaccountlocations.PlantID = tblhistory.PlantID 
					INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
					WHERE '.db_prefix() . 'history.FY = '.$fy.' AND '.db_prefix() . 'history.PlantID = '.$selected_company.'  AND tblhistory.TType IN("O","R") '.$where_subgroup.'  AND tblhistory.TransID IS NOT NULL AND tblhistory.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" ';
					if($locType){
						$sql .= ' AND '.db_prefix() . 'accountlocations.LocationTypeID = "'.$locType.'"';
					}
					$sql .= ' Group By tblhistory.ItemID,tblhistory.TType,tblhistory.TType2 ORDER BY tblhistory.ItemID';
					}else {
					$sql = 'SELECT '.db_prefix() . 'history.ItemID,SUM('.db_prefix() . 'history.NetChallanAmt) AS NetChallanAmt,SUM('.db_prefix() . 'history.BilledQty) AS BilledQty,'.db_prefix() . 'history.TType,'.db_prefix() . 'history.TType2,'.db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.StationName FROM '.db_prefix() . 'history 
					INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID 
					LEFT JOIN  tblaccountlocations ON  tblaccountlocations.AccountID = tblhistory.AccountID AND  tblaccountlocations.PlantID = tblhistory.PlantID 
					WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.' AND tblhistory.TType IN("O","R") AND tblhistory.TransID IS NOT NULL AND tblhistory.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" ';
					if($locType){
						$sql .= ' AND '.db_prefix() . 'accountlocations.LocationTypeID = "'.$locType.'"';
					}
					$sql .= ' Group By tblhistory.AccountID,tblhistory.TType,tblhistory.TType2 ORDER BY tblhistory.AccountID';
				}
			}
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function GetSaleRtnBodyData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$AccountAddress2 = $filterdata["AccountAddress2"];
			$AccountCity = $filterdata["AccountCity"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			if($AccountID !==''){
				$sql = 'SELECT '.db_prefix() . 'history.AccountID,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.ItemID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.TransDate2,SUM('.db_prefix() . 'history.NetChallanAmt) AS NetChallanAmt,SUM('.db_prefix() . 'history.ChallanAmt) AS ChallanAmt,SUM('.db_prefix() . 'history.cgstamt) AS cgstamtSum,SUM('.db_prefix() . 'history.sgstamt) AS sgstamtSum,SUM('.db_prefix() . 'history.igstamt) AS igstamtSum,SUM('.db_prefix() . 'history.BilledQty) AS BilledQty,'.db_prefix() . 'items.description FROM '.db_prefix() . 'history 
				INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.'  AND tblhistory.TType IN("R") AND tblhistory.AccountID = "'.$AccountID.'" AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
				Group By tblhistory.ItemID,tblhistory.OrderID ORDER BY tblhistory.OrderID ASC';
				}else{
				
				$sql = 'SELECT '.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.TransDate2,SUM('.db_prefix() . 'history.NetChallanAmt) AS NetChallanAmt,SUM('.db_prefix() . 'history.ChallanAmt) AS ChallanAmt,SUM('.db_prefix() . 'history.cgstamt) AS cgstamtSum,SUM('.db_prefix() . 'history.sgstamt) AS sgstamtSum,SUM('.db_prefix() . 'history.igstamt) AS igstamtSum,'.db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.address,'.db_prefix() . 'clients.vat FROM '.db_prefix() . 'history 
				INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID 
				WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.' AND tblhistory.TType IN("R") AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" ';
				
				$sql .= ' Group By tblhistory.OrderID,tblhistory.AccountID ORDER BY tblclients.company ASC';
			}
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		//======================== Get Daily Sale Report ===============================	
		public function load_data($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '(tblsalesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND 
			tblsalesmaster.PlantID="'.$selected_company.'"  ORDER BY ChallanID ASC';
			
			$sql ='SELECT '.db_prefix().'salesmaster.*,tblclients.company AS AccountName,tblclients.StationName,
			(SELECT COUNT(OrderID) FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.ChallanID = '.db_prefix().'salesmaster.ChallanID AND '.db_prefix().'ordermaster.PlantID = '.$selected_company.') as Count_number, 
			(SELECT SUM(OrderAmt) FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.ChallanID = '.db_prefix().'salesmaster.ChallanID AND '.db_prefix().'ordermaster.PlantID = '.$selected_company.') as Total_number
			FROM '.db_prefix().'salesmaster 
			INNER JOIN tblclients ON tblclients.AccountID = tblsalesmaster.AccountID AND tblclients.PlantID = tblsalesmaster.PlantID
			WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function get_sale_item_group2($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '(Transdate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59")';
			
			$sql1 .= ' AND TType ="O" AND PlantID = "'.$selected_company.'" AND FY = "'.$fy.'"';
			
			$sql ='SELECT '.db_prefix().'history.* FROM '.db_prefix().'history WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			// print_r($result);die;
			if(empty($result)){
				return $result;
			}
			
			$order_ids = array();
			$item_ids = array();
			foreach ($result as $key => $value) {
				# code...
				array_push($item_ids, $value["ItemID"]);
			}
			
			if(empty($item_ids)){
				
				}else{
				
				
				$item_ids_uniqu = array_unique($item_ids);
				
				$this->db->select('*');
				$this->db->from(db_prefix() . 'items');
				$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
				//$this->db->where(db_prefix() . 'items.isactive', "Y");
				$this->db->where_in('item_code',$item_ids_uniqu);
				$result3 = $this->db->get()->result_array();
				
				$item_group_ids = array();
				foreach ($result3 as $key3 => $value3) {
					# code...
					array_push($item_group_ids, $value3["SubGrpID1"]);
				}
				$item_group_ids_uniqu = array_unique($item_group_ids);
				
				$this->db->select('*');
				$this->db->from(db_prefix() . 'items_sub_groups');
				//$this->db->where(db_prefix() . 'items_groups.PlantID', $selected_company);
				
				$this->db->where_in('id',$item_group_ids_uniqu);
				$this->db->order_by('name','ASC');
				$result4 = $this->db->get()->result_array();
				
				return $result4;
				
			}
		}
		
		public function get_itemdetails_for_sale_return($item_id,$AccountId,$filterdata)
		{ 
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$report_type = $filterdata["report_type"];
			
			$sql = 'SELECT SUM(NetChallanAmt) as sr_amt_sum,AccountID,ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sr_sumcases,SUM(tblhistory.BilledQty) AS sr_sumunit  FROM `tblhistory` WHERE TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND AccountID IN('.$AccountId.') AND PlantID = '.$selected_company.' AND FY = "'.$fy.'"';
			
			$sql .= ' AND tblhistory.TType ="R" AND ItemID="'.$item_id.'"';
			
			//$sql .= ' GROUP BY ItemID,AccountID';
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		public function GetPartyPackAccountList($filterdata)
		{
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]); 
			$loc_type = $filterdata["loc_type"];
			$states = $filterdata["states"];
			$client_type = $filterdata["client_type"];
			$report_type = $filterdata["report_type"];
			$staff_designation = $filterdata["staff_designation"];
			$staff_id = $filterdata["staff_id"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($report_type == "freshrtn" || $report_type == "damage"){
				$tbl = "tblsalesreturn";
				}else{
				$tbl = "tblsalesmaster";
			}
			
			$sql = 'SELECT '.$tbl.'.AccountID,'.$tbl.'.AccountID2,tblStationMaster.StationName,tblclients.company';
			$sql .= ' FROM `'.$tbl.'`';
			
			
			if($filterdata["AcountType"] == "BillTo"){
				$sql .= ' INNER JOIN tblclients ON tblclients.AccountID = '.$tbl.'.AccountID AND tblclients.PlantID = '.$tbl.'.PlantID';
				$sql .= ' LEFT JOIN tblaccountlocations ON '.$tbl.'.AccountID = tblaccountlocations.AccountID AND '.$tbl.'.PlantID = tblaccountlocations.PlantID';
				}else{
				$sql .= ' INNER JOIN tblclients ON tblclients.AccountID = '.$tbl.'.AccountID2 AND tblclients.PlantID = '.$tbl.'.PlantID';
				$sql .= ' LEFT JOIN tblaccountlocations ON '.$tbl.'.AccountID2 = tblaccountlocations.AccountID AND '.$tbl.'.PlantID = tblaccountlocations.PlantID';
			}
			
			$sql .= ' LEFT JOIN tblStationMaster ON tblStationMaster.id = tblclients.StationName';
			
			/*if($staff_id && $filterdata["AcountType"] == "BillTo"){
				$sql .= ' LEFT JOIN tblcustomer_admins ON tblcustomer_admins.customer_id = tblsalesmaster.AccountID AND tblcustomer_admins.company_id = tblsalesmaster.PlantID';
				}else if($staff_id && $filterdata["AcountType"] == "ShipTo"){
				$sql .= ' LEFT JOIN tblcustomer_admins ON tblcustomer_admins.customer_id = tblsalesmaster.AccountID2 AND tblcustomer_admins.company_id = tblsalesmaster.PlantID';
			}*/
			$sql .= ' WHERE '.$tbl.'.PlantID = '.$selected_company.' AND '.$tbl.'.FY = "'.$fy.'" AND 
			'.$tbl.'.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" ';
			
			if($report_type == "freshrtn"){
				$sql .= ' AND '.$tbl.'.SalesRtnTypeID = "Fresh"';
				}else if($report_type == "damage"){
				$sql .= ' AND '.$tbl.'.SalesRtnTypeID = "Damage"';
			}
			if($loc_type == "3"){
				}else{
				$sql .= ' AND tblaccountlocations.LocationTypeID = '.$loc_type;
			}
			if($states){
				$sql .= ' AND tblclients.state ="'.$states.'"';
			}
			/*if($staff_id){
				$sql .= ' AND tblcustomer_admins.staff_id IN('.$staff_ids_uniqu_s.')';
			}*/
			if($client_type){
				$sql .= ' AND tblclients.DistributorType ="'.$client_type.'"';
			}
			if($filterdata["AcountType"] == "BillTo"){
				$sql .= ' GROUP BY '.$tbl.'.AccountID ORDER BY tblclients.company ASC';
				}else{
				$sql .= ' GROUP BY '.$tbl.'.AccountID2 ORDER BY tblclients.company ASC';
			}
			$result = $this->db->query($sql)->result_array();
			return $result;
			
		}
		
		/*public function GetPartyPackItemList($filterdata,$item_group)
			{
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]); 
			$loc_type = $filterdata["loc_type"];
			$states = $filterdata["states"];
			$client_type = $filterdata["client_type"];
			$report_type = $filterdata["report_type"];
			$staff_designation = $filterdata["staff_designation"];
			$staff_id = $filterdata["staff_id"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$staff_ids = array();
			array_push($staff_ids, $staff_id);
			if($staff_id){
			$get_sql1 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$staff_id.'"';
			$get_result1 = $this->db->query($get_sql1)->result_array();
			foreach ($get_result1 as $key1 => $value1) {
			array_push($staff_ids, $value1["staffid"]);
			$get_sql2 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value1["staffid"].'"';
			$get_result2 = $this->db->query($get_sql2)->result_array();
			foreach ($get_result2 as $key2 => $value2) {
			array_push($staff_ids, $value2["staffid"]);
			$get_sql3 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value2["staffid"].'"';
			$get_result3 = $this->db->query($get_sql3)->result_array();
			foreach ($get_result3 as $key3 => $value3) {
			array_push($staff_ids, $value3["staffid"]);
			$get_sql4 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value3["staffid"].'"';
			$get_result4 = $this->db->query($get_sql4)->result_array();
			foreach ($get_result4 as $key4 => $value4) {
			array_push($staff_ids, $value4["staffid"]);
			$get_sql5 = 'SELECT * FROM tblstaff WHERE team_manage = "'.$value4["staffid"].'"';
			$get_result5 = $this->db->query($get_sql5)->result_array();
			foreach ($get_result5 as $key5 => $value5) {
			array_push($staff_ids, $value5["staffid"]);
			}
			}
			}
			}
			}
			}
			$staff_ids_uniqu = array_unique($staff_ids);  
			$staff_ids_uniqu_s = implode(", ", $staff_ids_uniqu);
			
			
			$sql = 'SELECT tblhistory.OrderID, tblhistory.ItemID,tblitems.description,tblsalesmaster.AccountID,tblsalesmaster.AccountID2,';
			
			$sql .= 'tblclients.StationName FROM `tblhistory`
			INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID ';
			
			$sql .= 'INNER JOIN tblsalesmaster ON tblsalesmaster.AccountID = tblhistory.AccountID AND tblsalesmaster.SalesID = tblhistory.TransID AND tblsalesmaster.PlantID = tblhistory.PlantID ';
			
			if($filterdata["AcountType"] == "BillTo"){
			$sql .= ' INNER JOIN tblclients ON tblclients.AccountID = tblsalesmaster.AccountID AND tblclients.PlantID = tblsalesmaster.PlantID';
			$sql .= ' LEFT JOIN tblaccountlocations ON tblsalesmaster.AccountID = tblaccountlocations.AccountID AND tblsalesmaster.PlantID = tblaccountlocations.PlantID';
			}else{
			$sql .= ' INNER JOIN tblclients ON tblclients.AccountID = tblsalesmaster.AccountID2 AND tblclients.PlantID = tblsalesmaster.PlantID';
			$sql .= ' LEFT JOIN tblaccountlocations ON tblsalesmaster.AccountID2 = tblaccountlocations.AccountID AND tblsalesmaster.PlantID = tblaccountlocations.PlantID';
			}
			
			if($staff_id && $filterdata["AcountType"] == "BillTo"){
			$sql .= ' LEFT JOIN tblcustomer_admins ON tblcustomer_admins.customer_id = tblsalesmaster.AccountID AND tblcustomer_admins.company_id = tblsalesmaster.PlantID';
			}else if($staff_id && $filterdata["AcountType"] == "ShipTo"){
			$sql .= ' LEFT JOIN tblcustomer_admins ON tblcustomer_admins.customer_id = tblsalesmaster.AccountID2 AND tblcustomer_admins.company_id = tblsalesmaster.PlantID';
			}
			$sql .= ' WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND 
			tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND 
			tblitems.SubGrpID1 IN('.$item_group.') AND tblhistory.BillID IS NOT NULL AND tblhistory.NetChallanAmt !=0.00';
			
			if($loc_type == "3"){
			//$sql .= ' AND tblaccountlocations.LocationTypeID IN(1,2,3)';
			}else{
			$sql .= ' AND tblaccountlocations.LocationTypeID = '.$loc_type;
			}
			if($states){
			$sql .= ' AND tblclients.state ="'.$states.'"';
			}
			if($staff_id){
			$sql .= ' AND tblcustomer_admins.staff_id IN('.$staff_ids_uniqu_s.')';
			}
			if($client_type){
			$sql .= ' AND tblclients.DistributorType ="'.$client_type.'"';
			}
			
			if($report_type == "freshrtn"){
			$sql .= ' AND tblhistory.TType ="R" AND TType2="Fresh"';
			}elseif($report_type == "damage"){
			$sql .= ' AND tblhistory.TType IN("R","D") AND TType2="Damage"';
			}elseif($report_type == "netsales"){
			$sql .= ' AND tblhistory.TType IN("O","R","D") AND TType2 IN("Order","Damage","Fresh")';
			}elseif($report_type == "sales"){
			$sql .= ' AND tblhistory.TType ="O" AND TType2="Order"';
			}
			if($filterdata["AcountType"] == "BillTo"){
			$sql .= ' GROUP BY tblhistory.ItemID,tblsalesmaster.AccountID ORDER BY tblitems.SubGrpID1 ASC';
			}else{
			$sql .= ' GROUP BY tblhistory.ItemID,tblsalesmaster.AccountID2 ORDER BY tblitems.SubGrpID1 ASC';
			}
			$result = $this->db->query($sql)->result_array();
			return $result;
			
		}*/
		
		public function GetPartyPackItemList($filterdata,$item_groupArray,$AccountListArray)
		{
			$from_date = to_sql_date($filterdata["from_date"])." 00:00:00";
			$to_date = to_sql_date($filterdata["to_date"])." 23:59:59"; 
			$loc_type = $filterdata["loc_type"];
			$states = $filterdata["states"];
			$client_type = $filterdata["client_type"];
			$report_type = $filterdata["report_type"];
			$staff_designation = $filterdata["staff_designation"];
			$staff_id = $filterdata["staff_id"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblhistory.OrderID, tblhistory.ItemID,tblitems.description,tblitems.unit,tblitems.weight,tblhistory.AccountID');
			$this->db->from(db_prefix() . 'history');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			// $this->db->where_in('tblhistory.AccountID',$AccountListArray);
			if (!empty($AccountListArray)) {
				$this->db->where_in('tblhistory.AccountID', $AccountListArray);
				} else {
				// Force no results
				$this->db->where('1', '0');
			}
			$this->db->where_in('tblitems.SubGrpID1',$item_groupArray);
			if($report_type == "freshrtn"){
				$this->db->where('tblhistory.TType', "R");
				$this->db->where('tblhistory.TType2', "Fresh");
				}elseif($report_type == "damage"){
				$r_d = array("R","D");
				$this->db->where_in('tblhistory.TType', $r_d);
				$this->db->where('tblhistory.TType2', "Damage");
				}elseif($report_type == "netsales"){
				$r_d_o = array("R","D","O");
				$r_d_o_2 = array("Fresh","Damage","Order");
				$this->db->where_in('tblhistory.TType', $r_d_o);
				$this->db->where_in('tblhistory.TType2', $r_d_o_2);
				}elseif($report_type == "sales"){
				$this->db->where('tblhistory.TType', "O");
				$this->db->where('tblhistory.TType2', "Order");
			}
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.NetChallanAmt >',"0.00");
			$this->db->where('tblhistory.TransDate2 >=', $from_date);
			$this->db->where('tblhistory.TransDate2 <=', $to_date);
			$this->db->group_by("tblhistory.ItemID");
			$this->db->order_by("tblitems.SubGrpID1 ASC");
			$result = $this->db->get()->result_array();
			return $result;
			
		}
		
		
		public function GetPartyPackBodySaleData($AccountListArray,$filterdata,$item_groupArray)
		{ 
			$from_date = to_sql_date($filterdata["from_date"])." 00:00:00";
			$to_date = to_sql_date($filterdata["to_date"])." 23:59:59";
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$report_type = $filterdata["report_type"];
			$values_in = $filterdata["values_in"];
			$AcountType = $filterdata["AcountType"];
			
			if($AcountType == "ShipTo"){
				if($report_type == "sales" || $report_type == "netsales"){
					$tbl = "tblsalesmaster.AccountID2 AS AccountID";
					}else{
					$tbl = "tblsalesreturn.AccountID2 AS AccountID";
				}
				
				}else{
				$tbl = "tblhistory.AccountID";
			}
			
			if($values_in == '1'){
				$this->db->select('SUM(tblhistory.NetChallanAmt) as amt_sum,'.$tbl.',
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sumcases,SUM(tblhistory.BilledQty) AS sumunit,tblhistory.SuppliedIn');
				}else{
				$this->db->select('SUM(tblhistory.ChallanAmt) as amt_sum,'.$tbl.',
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sumcases,SUM(tblhistory.BilledQty) AS sumunit,tblhistory.SuppliedIn');
			}
			$this->db->from(db_prefix() . 'history');
			if($AcountType == "ShipTo" ){
				if($report_type == "sales" || $report_type == "netsales"){
					$this->db->join('tblsalesmaster', 'tblsalesmaster.SalesID = tblhistory.TransID AND tblsalesmaster.AccountID = tblhistory.AccountID');
					}else{
					$this->db->join('tblsalesreturn', 'tblsalesreturn.SalesRtnID = tblhistory.OrderID AND tblsalesreturn.AccountID = tblhistory.AccountID');
				}
			}
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			// $this->db->where_in('tblhistory.AccountID',$AccountListArray);
			if (!empty($AccountListArray)) {
				$this->db->where_in('tblhistory.AccountID', $AccountListArray);
				} else {
				// Force no results
				$this->db->where('1', '0');
			}
			$this->db->where_in('tblitems.SubGrpID1',$item_groupArray);
			if($report_type == "freshrtn"){
				$this->db->where('tblhistory.TType', "R");
				$this->db->where('tblhistory.TType2', "Fresh");
				}elseif($report_type == "damage"){
				$r_d = array("R","D");
				$this->db->where_in('tblhistory.TType', $r_d);
				$this->db->where('tblhistory.TType2', "Damage");
				}elseif($report_type == "sales" || $report_type == "netsales"){
				$this->db->where('tblhistory.TType', "O");
				$this->db->where('tblhistory.TType2', "Order");
			}
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.NetChallanAmt >',"0.00");
			$this->db->where('tblhistory.TransDate2 >=', $from_date);
			$this->db->where('tblhistory.TransDate2 <=', $to_date);
			
			if($AcountType == "ShipTo"){
				if($report_type == "sales" || $report_type == "netsales"){
					$this->db->group_by("tblhistory.ItemID,tblsalesmaster.AccountID2");
					}else{
					$this->db->group_by("tblhistory.ItemID,tblsalesreturn.AccountID2");
				}
				}else{
				$this->db->group_by("tblhistory.ItemID,tblhistory.AccountID");
			}
			$this->db->order_by("tblhistory.ItemID ASC");
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function GetPartyPackSaleRtnData($AccountListArray,$filterdata,$item_groupArray)
		{ 
			$from_date = to_sql_date($filterdata["from_date"])." 00:00:00";
			$to_date = to_sql_date($filterdata["to_date"])." 23:59:59";
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$report_type = $filterdata["report_type"];
			$values_in = $filterdata["values_in"];
			
			if($values_in == '1'){
				$this->db->select('SUM(tblhistory.NetChallanAmt) as sr_amt_sum,tblhistory.AccountID,
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sr_sumcases,SUM(tblhistory.BilledQty) AS sr_sumunit');
				}else{
				$this->db->select('SUM(tblhistory.ChallanAmt) as sr_amt_sum,tblhistory.AccountID,
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sr_sumcases,SUM(tblhistory.BilledQty) AS sr_sumunit');
			}
			$this->db->from(db_prefix() . 'history');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			// $this->db->where_in('tblhistory.AccountID',$AccountListArray);
			if (!empty($AccountListArray)) {
				$this->db->where_in('tblhistory.AccountID', $AccountListArray);
				} else {
				// Force no results
				$this->db->where('1', '0');
			}
			$this->db->where_in('tblitems.SubGrpID1',$item_groupArray);
			$r_d = array("R","D");
			$r_d2 = array("Fresh","Damage");
			$this->db->where_in('tblhistory.TType', $r_d);
			$this->db->where_in('tblhistory.TType2', $r_d2);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.NetChallanAmt >',"0.00");
			$this->db->where('tblhistory.TransDate2 >=', $from_date);
			$this->db->where('tblhistory.TransDate2 <=', $to_date);
			$this->db->group_by("tblhistory.ItemID,tblhistory.AccountID");
			$this->db->order_by("tblhistory.ItemID ASC");
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function GetPartyPackSaleItemWiseData($AccountListArray,$filterdata,$item_groupArray)
		{ 
			$from_date = to_sql_date($filterdata["from_date"])." 00:00:00";
			$to_date = to_sql_date($filterdata["to_date"])." 23:59:59";
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$report_type = $filterdata["report_type"];
			$values_in = $filterdata["values_in"];
			
			if($values_in == '1'){
				$sql .= ' ';
				$this->db->select('SUM(tblhistory.NetChallanAmt) as amt_sum,
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sumcases,SUM(tblhistory.BilledQty) AS sumunit,tblhistory.SuppliedIn');
				}else{
				$sql .= ' ';
				$this->db->select('SUM(tblhistory.ChallanAmt) as amt_sum,
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sumcases,SUM(tblhistory.BilledQty) AS sumunit,tblhistory.SuppliedIn');
			}
			$this->db->from(db_prefix() . 'history');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			// $this->db->where_in('tblhistory.AccountID',$AccountListArray);
			if (!empty($AccountListArray)) {
				$this->db->where_in('tblhistory.AccountID', $AccountListArray);
				} else {
				// Force no results
				$this->db->where('1', '0');
			}
			$this->db->where_in('tblitems.SubGrpID1',$item_groupArray);
			if($report_type == "freshrtn"){
				$this->db->where('tblhistory.TType', "R");
				$this->db->where('tblhistory.TType2', "Fresh");
				}elseif($report_type == "damage"){
				$r_d = array("R","D");
				$this->db->where_in('tblhistory.TType', $r_d);
				$this->db->where('tblhistory.TType2', "Damage");
				}elseif($report_type == "sales" || $report_type == "netsales"){
				$this->db->where('tblhistory.TType', "O");
				$this->db->where('tblhistory.TType2', "Order");
			}
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.NetChallanAmt >',"0.00");
			$this->db->where('tblhistory.TransDate2 >=', $from_date);
			$this->db->where('tblhistory.TransDate2 <=', $to_date);
			$this->db->group_by("tblhistory.ItemID");
			$this->db->order_by("tblhistory.ItemID ASC");
			$result = $this->db->get()->result_array();
			return $result;
		}
		public function GetPartyPackSaleRtnItemWise($AccountListArray,$filterdata,$item_groupArray)
		{ 
			$from_date = to_sql_date($filterdata["from_date"])." 00:00:00";
			$to_date = to_sql_date($filterdata["to_date"])." 23:59:59";
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$report_type = $filterdata["report_type"];
			$values_in = $filterdata["values_in"];
			
			if($values_in == '1'){
				$this->db->select('SUM(tblhistory.NetChallanAmt) as sr_amt_sum,
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sr_sumcases,SUM(tblhistory.BilledQty) AS sr_sumunit');
				}else{
				$this->db->select('SUM(tblhistory.ChallanAmt) as sr_amt_sum,
				tblhistory.ItemID,SUM(tblhistory.BilledQty / tblhistory.CaseQty) AS sr_sumcases,SUM(tblhistory.BilledQty) AS sr_sumunit');
			}
			$this->db->from(db_prefix() . 'history');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $fy);
			// $this->db->where_in('tblhistory.AccountID',$AccountListArray);
			if (!empty($AccountListArray)) {
				$this->db->where_in('tblhistory.AccountID', $AccountListArray);
				} else {
				// Force no results
				$this->db->where('1', '0');
			}
			$this->db->where_in('tblitems.SubGrpID1',$item_groupArray);
			$r_d = array("R","D");
			$r_d2 = array("Fresh","Damage");
			$this->db->where_in('tblhistory.TType', $r_d);
			$this->db->where_in('tblhistory.TType2', $r_d2);
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblhistory.NetChallanAmt >',"0.00");
			$this->db->where('tblhistory.TransDate2 >=', $from_date);
			$this->db->where('tblhistory.TransDate2 <=', $to_date);
			$this->db->group_by("tblhistory.ItemID");
			$this->db->order_by("tblhistory.ItemID ASC");
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function get_commulative_data($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '(Transdate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59")';
			
			$sql1 .= ' AND OrderStatus ="C" AND PlantID = "'.$selected_company.'" AND FY = "'.$fy.'"';
			
			$sql ='SELECT '.db_prefix().'ordermaster.* FROM '.db_prefix().'ordermaster WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			if(empty($result)){
				return $result;
			}
			
			$order_ids = array();
			$item_ids = array();
			foreach ($result as $key => $value) {
				# code...
				array_push($order_ids, $value["OrderID"]);
			}
			
			$this->db->select('ItemID,description');
			$this->db->from(db_prefix() . 'history');
			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'history.FY', $fy);
			//$this->db->where_in("$order_ids");
			$this->db->distinct();
			$this->db->where_in('OrderID',$order_ids);
			$result2 = $this->db->get()->result_array();
			return $result2;
			
		}
		
		
		
		public function get_body_commulative_data($data)
		{  
			$from_date = $data["from_date"];
			$to_date = $data["to_date"];
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '(date BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59")';
			
			$sql1 .= ' AND OrderStatus ="C" AND PlantID = "'.$selected_company.'" AND FY = "'.$fy.'"';
			
			$sql ='SELECT '.db_prefix().'ordermaster.* FROM '.db_prefix().'ordermaster WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			if(empty($result)){
				return $result;
			}
			
			$order_ids = array();
			$item_ids = array();
			foreach ($result as $key => $value) {
				# code...
				array_push($order_ids, $value["OrderID"]);
			}
			
			$this->db->select('ItemID,description');
			$this->db->from(db_prefix() . 'history');
			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'history.FY', $fy);
			//$this->db->where_in("$order_ids");
			$this->db->distinct();
			$this->db->where_in('OrderID',$order_ids);
			$result2 = $this->db->get()->result_array();
			return $result2;
			
		}
		
		//-----------------------------------------------------
		public function get_reported_by_staff($id){
			$this->db->select('*');
			$this->db->where('job_position', $id);
			$this->db->where('active', '1');
			$records = $this->db->get(db_prefix() . 'staff')->result();
			return $records;
		}
		
		public function GetSOList($id){
			$selected_company = $this->session->userdata('root_company');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$this->db->select('*');
			$this->db->where('job_position', $id);
			$this->db->where('active', '1');
			$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			$records = $this->db->get(db_prefix() . 'staff')->result_array();
			return $records;
		}
		public function GetPartyList(){
			$selected_company = $this->session->userdata('root_company');
			$SubActGroupID = '100056';
			$this->db->select('*');
			$this->db->where('SubActGroupID1', $SubActGroupID);
			$this->db->where('PlantID', $selected_company);
			$this->db->order_by('company', 'ASC');
			$records = $this->db->get(db_prefix() . 'clients')->result_array();
			return $records;
		}
		
		//-----------------------------------------------------
		public function get_state_name($state_id)
		{
			$this->db->select('state_name');
			$this->db->where('short_name', $state_id);
			$state_name = $this->db->get(db_prefix() . 'xx_statelist')->row();
			return $state_name;
		}
		
		//-----------------------------------------------------
		public function GetPartyName($AccountID)
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('company');
			$this->db->where('AccountID', $AccountID);
			$this->db->where('PlantID', $selected_company);
			$AccountName = $this->db->get(db_prefix() . 'clients')->row();
			return $AccountName;
		}
		
		//-----------------------------------------------------
		public function get_client_type_name($client_type){
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('name');
			$this->db->where('id', $client_type);
			$this->db->where('PlantID', $selected_company);
			$client_type_name = $this->db->get(db_prefix() . 'customers_groups')->row();
			return $client_type_name;
		}
		
		//-----------------------------------------------------
		public function get_item_group_name($item_group){
			
			$item_group_array = explode(",",$item_group);
			$this->db->select('name');
			$this->db->where_in('id', $item_group_array);
			$item_group_names = $this->db->get(db_prefix() . 'items_sub_groups')->result_array();
			$item_group_name = array();
			foreach ($item_group_names as $key => $value) 
			{
				array_push($item_group_name, $value["name"]);
			}
			$item_group_name_s = implode(", ", $item_group_name);
			return $item_group_name_s;
		}
		
		//-----------------------------------------------------
		public function GetOrderVsDispatchData($filterdata){
			
			$fromDateNew = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$toDateNew = to_sql_date($filterdata["to_date"]).' 23:59:59';
			$State = $filterdata["states"];
			$DistType = $filterdata["client_type"];
			$staff_id = $filterdata["staff_id"];
			$AccountID = $filterdata["AccountID"];
			$selected_company = $this->session->userdata('root_company');
			
			// $this->db->select(db_prefix().'ordermaster.OrderID,'.db_prefix().'salesmaster.SalesID,'.db_prefix().'salesmaster.Transdate,'.db_prefix().'salesmaster.BillAmt,'.db_prefix().'clients.company,'.db_prefix().'ordermaster.Transdate AS OrderDate,'.db_prefix().'ordermaster.OrderAmt,'.db_prefix().'challanmaster.gatepasstime,(Select SUM(tblhistory.eOrderQty * tblhistory.SaleRate) from tblhistory where tblhistory.OrderID = tblordermaster.OrderID) AS OrderAmount');
			// $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
			// if($State){
			// $this->db->where(db_prefix() . 'clients.state',$State);
			// }
			// if($DistType){
			// $this->db->where(db_prefix() . 'clients.DistributorType',$DistType);
			// }
			// $this->db->join(db_prefix() . 'salesmaster', db_prefix() . 'salesmaster.OrderID = ' . db_prefix() . 'ordermaster.OrderID AND  '.db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND  '.db_prefix() . 'ordermaster.FY = ' . db_prefix() . 'salesmaster.FY','LEFT');
			// $this->db->join(db_prefix() . 'challanmaster', db_prefix() . 'challanmaster.ChallanID = ' . db_prefix() . 'ordermaster.ChallanID AND  '.db_prefix() . 'challanmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND  '.db_prefix() . 'challanmaster.FY = ' . db_prefix() . 'ordermaster.FY','LEFT');
			// $this->db->where(db_prefix() . 'ordermaster.Transdate BETWEEN "'. $fromDateNew. '" AND "'. $toDateNew.'"');
			// $this->db->where(db_prefix() . 'ordermaster.PlantID',$selected_company);
			// $this->db->order_by(db_prefix() . 'ordermaster.OrderID','ASC');
			// $DATA = $this->db->get(db_prefix() . 'ordermaster')->result_array();
			// return $DATA;
			
			$this->db->select(
			db_prefix().'ordermaster.OrderID, '.
			db_prefix().'salesmaster.SalesID, '.
			db_prefix().'salesmaster.Transdate, '.
			db_prefix().'salesmaster.BillAmt, '.
			db_prefix().'clients.company, '.
			db_prefix().'ordermaster.Transdate AS OrderDate, '.
			db_prefix().'ordermaster.OrderAmt, '.
			db_prefix().'challanmaster.gatepasstime, '.
			'SUM((tblhistory.OrderQty * tblhistory.SaleRate)-tblhistory.DiscAmt) AS OrderAmount'
			);
			$this->db->join(db_prefix() . 'clients', 
			db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID 
			AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID'
			);
			if($State){
				$this->db->where(db_prefix() . 'clients.state', $State);
			}
			if($DistType){
				$this->db->where(db_prefix() . 'clients.DistributorType', $DistType);
			}
			if($staff_id){
				$this->db->where(db_prefix() . 'ordermaster.UserID', $staff_id);
			}
			
			if($AccountID){
				$this->db->where(db_prefix() . 'clients.AccountID',$AccountID);
			}
			$this->db->join(db_prefix() . 'salesmaster', 
			db_prefix() . 'salesmaster.OrderID = ' . db_prefix() . 'ordermaster.OrderID 
			AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID 
			AND ' . db_prefix() . 'ordermaster.FY = ' . db_prefix() . 'salesmaster.FY', 'LEFT'
			);
			$this->db->join(db_prefix() . 'challanmaster', 
			db_prefix() . 'challanmaster.ChallanID = ' . db_prefix() . 'ordermaster.ChallanID 
			AND ' . db_prefix() . 'challanmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID 
			AND ' . db_prefix() . 'challanmaster.FY = ' . db_prefix() . 'ordermaster.FY', 'LEFT'
			);
			$this->db->join('tblhistory', 'tblhistory.OrderID = ' . db_prefix() . 'ordermaster.OrderID', 'LEFT');
			$this->db->where(db_prefix() . 'ordermaster.Transdate BETWEEN "'. $fromDateNew. '" AND "'. $toDateNew.'"');
			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
			$this->db->group_by(db_prefix() . 'ordermaster.OrderID');  // Grouping to aggregate SUM()
			$this->db->order_by(db_prefix() . 'ordermaster.OrderID', 'ASC');
			$DATA = $this->db->get(db_prefix() . 'ordermaster')->result_array();
			return $DATA;
			
		}
		
		//-----------------------------------------------------
		public function GetOrderVsDispatchItemWiseData($filterdata){
			
			$fromDateNew = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$toDateNew = to_sql_date($filterdata["to_date"]).' 23:59:59';
			$State = $filterdata["states"];
			$DistType = $filterdata["client_type"];
			$AccountID = $filterdata["AccountID"];
			$staff_id = $filterdata["staff_id"];
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select(db_prefix().'history.ItemID,'.db_prefix().'items.description,'.db_prefix().'items.case_qty,SUM('.db_prefix().'history.OrderQty) AS OrdQty,SUM('.db_prefix().'history.BilledQty) AS BillQty');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND  '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			if($State || $DistType || $AccountID){
				$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND  '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			}
			if($State){
				$this->db->where(db_prefix() . 'clients.state',$State);
			}
			if($AccountID){
				$this->db->where(db_prefix() . 'clients.AccountID',$AccountID);
			}
			if($DistType){
				$this->db->where(db_prefix() . 'clients.DistributorType',$DistType);
			}
			
			if($staff_id){
				$this->db->where(db_prefix() . 'history.UserID', $staff_id);
			}
			$this->db->where(db_prefix() . 'history.TransDate2 BETWEEN "'. $fromDateNew. '" AND "'. $toDateNew.'"');
			$this->db->where(db_prefix() . 'history.PlantID',$selected_company);
			$this->db->where(db_prefix() . 'history.TType','O');
			$this->db->where(db_prefix() . 'history.TType2','Order');
			$this->db->group_by(db_prefix() . 'history.ItemID');
			$DATA = $this->db->get(db_prefix() . 'history')->result_array();
			return $DATA;
		}
		
		function ItemList_New(){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$maingroup = array('1');
			
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			
			$this->db->select(db_prefix() . 'items.*, SUM(tblstockmaster.OQty) AS OQty');
			$this->db->from(db_prefix() . 'items');
			$this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'inner');
			$this->db->join('tblitems_main_groups', 'tblitems_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_group_id', 'inner');
			$this->db->join(db_prefix() .'stockmaster',db_prefix() .'stockmaster.ItemID = '.db_prefix() .'items.item_code AND '.db_prefix() .'stockmaster.PlantID = '.db_prefix() .'items.PlantID AND '.db_prefix() .'stockmaster.FY = "'.$fy.'" AND '.db_prefix() .'stockmaster.cnfid = "1"','INNER');
			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'items_main_groups.id !=', '1');
			$this->db->group_by(db_prefix() . 'items.item_code'); // to avoid duplicates
			$this->db->order_by(db_prefix() . 'items.description', 'ASC');
			$records = $this->db->get()->result_array();
			// echo $this->db->last_query();die;
			// echo "<pre>";print_r($records);die;
			// Fetch stock data for ALL items in one query
			$itemIds = array_column($records, 'item_code');
			if(!empty($itemIds)){
				$this->db->select('ItemID,TType,TType2,CaseQty,SUM(BilledQty) as BilledQty');
				$this->db->from(db_prefix().'history');
				$this->db->where(db_prefix().'history.PlantID', $selected_company);
				// $this->db->where(db_prefix().'history.GodownID', $GodownID);
				$this->db->where_in(db_prefix().'history.ItemID', $itemIds);
				$this->db->where(db_prefix().'history.BillID is NOT NULL', NULL, FALSE);
				$this->db->where(db_prefix().'history.FY', $fy);
				$this->db->group_by('ItemID,TType,TType2');
				$stockData = $this->db->get()->result_array();
				// echo $this->db->last_query();die;
				// echo "<pre>";print_r($stockData);die;
				// Re-index stock data by ItemID
				$stockMap = [];
				foreach($stockData as $row){
					$stockMap[$row['ItemID']][] = $row;
				}
				
				// Attach stock data to items
				foreach($records as &$each){
					$each['itemStocks'] = isset($stockMap[$each['item_code']]) ? $stockMap[$each['item_code']] : [];
				}
			}
			
			return $records;
			
		}
		
		// Item Wise Stock report
		public function GetStockReport($ItemID)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			
			$this->db->select('ItemID,TType,TType2,CaseQty,SUM(BilledQty) AS BilledQty');
			$this->db->from(db_prefix() .'history');
			$this->db->where(db_prefix() .'history.PlantID', $selected_company);
			$this->db->where(db_prefix() .'history.GodownID', $GodownID);
			$this->db->where(db_prefix() .'history.ItemID', $ItemID);
			$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);
			$this->db->where(db_prefix() .'history.FY', $fy);
			$this->db->group_by('ItemID,TType,TType2');
			return $this->db->get()->result_array();
		}
		
		
		public function GetGroupWiseItemSaleReportBodyData($filterdata)
		{ 
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql = 'SELECT SUM(tblhistory.BilledQty) AS BillQty,SUM(tblhistory.DiscAmt) AS DiscAmt,tblhistory.ItemID,tblhistory.CaseQty,AVG(tblhistory.SaleRate) AS SaleRate,
			tblitems.description,tblitems.SubGrpID1 as subgroup_id,tblitems.weight,tblitems.local_supply_in,tblitems.unit
			FROM tblhistory 
			INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
			INNER JOIN tblitems_sub_groups ON tblitems_sub_groups.id = tblitems.SubGrpID1
			WHERE tblhistory.PlantID = "'.$selected_company.'" AND tblhistory.FY = "'.$fy.'" AND 
			tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND 
			tblhistory.TType = "O" AND tblhistory.TType2 = "Order" AND tblhistory.TransID IS NOT NULL
			Group BY tblhistory.ItemID ORDER BY tblitems.description ASC';
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
		}
		
		
		public function GetGroupList($filterdata)
		{ 
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql = 'SELECT tblhistory.ItemID,tblitems.SubGrpID1 as subgroup_id,tblitems_sub_groups.name
			FROM tblhistory 
			INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
			INNER JOIN tblitems_sub_groups ON tblitems_sub_groups.id = tblitems.SubGrpID1
			WHERE tblhistory.PlantID = "'.$selected_company.'" AND tblhistory.FY = "'.$fy.'" AND 
			tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND 
			tblhistory.TType = "O" AND tblhistory.TType2 = "Order" AND tblhistory.TransID IS NOT NULL
			Group BY tblitems.SubGrpID1 ORDER BY tblitems_sub_groups.name ASC';
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		
		public function GetItemGroupWiseSaleReportBodyData($filterdata)
		{ 
			$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			// $sql = 'select ItemGroup AS GroupName,SUM(Qty) Total_unit,sum(Total_Sale) Total_Sale, Sum(TotalWeight)TotalWeight From (select b.description,c.name ItemGroup, Sum(BilledQty) Qty,Avg(SaleRate) Rate,Sum(BilledQty)*Avg(SaleRate) Amount,Sum(BilledQty)*Avg(b.weight) Weight FROM tblhistory as a 
			// INNER join tblitems as b on b.PlantID=a.PlantID and b.item_code=a.ItemID
			// INNER join tblitems_sub_groups as c on c.id=b.subGroup_ID 
			// where a.PlantID='.$selected_company.' and a.FY="'.$fy.'" AND cast(a.TransDate2 as date) BETWEEN "'.$from_date.'" AND "'.$to_date.'" and TType2="Order" and transId is not null GROUP BY b.description,c.name ORDER by c.name,b.description asc)x group by ItemGroup';
			$sql = 'select ItemGroup AS GroupName,SUM(Qty) Total_unit,SUM(Amount) Amount,SUM(DiscAmt) DiscAmt,sum(Total_Sale) Total_Sale, Sum(TotalWeight)TotalWeight From (select b.description,c.name ItemGroup,e.name Division, Sum(BilledQty) Qty,Sum(BilledQty)/Avg(CaseQty) "Total_Sale",Avg(SaleRate) Rate,Sum(DiscAmt) as DiscAmt,Sum(BilledQty)*Avg(SaleRate) Amount,Sum(BilledQty)*Avg(b.weight) TotalWeight FROM tblhistory as a LEFT join tblitems as b on b.PlantID=a.PlantID and b.item_code=a.ItemID LEFT join tblitems_sub_groups as c on c.id=b.SubGrpID1 LEFT join tblitems_groups as e on e.id=b.group_id  where a.PlantID='.$selected_company.' and a.FY="'.$fy.'" AND cast(a.TransDate2 as date) BETWEEN "'.$from_date.'" AND "'.$to_date.'" and TType2="Order" and transId is not null GROUP BY b.description,c.name,e.name ORDER by e.name,c.name,b.description asc)x group by ItemGroup';
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
		}
		
		public function GetItemDivisionWiseSaleReportBodyData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			// $sql = 'SELECT tblitems_groups.name AS GroupName,SUM((tblhistory.BilledQty/ tblhistory.CaseQty)) AS SumCaseQty,
			// Sum(tblhistory.BilledQty)*Avg(tblitems.weight) AS TotalWeight
			// FROM `tblhistory` 
			// INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
			// INNER JOIN tblitems_groups ON tblitems_groups.id= tblitems.group_id 
			// WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransID IS NOT NULL AND 
			// tblhistory.TType = "O" AND tblhistory.TType2 = "Order" AND 
			// tblhistory.TransDate2 BETWEEN "'.$from_date.'" AND "'.$to_date.'" 
			// GROUP BY tblitems.group_id;';
			$sql = 'select Division AS Division,SUM(Qty) Total_unit,sum(DiscAmt) DiscAmt,sum(Amount) Amount,sum(Total_Sale) Total_Sale, Sum(TotalWeight)TotalWeight From (select b.description,c.name ItemGroup,e.name Division, Sum(BilledQty) Qty,Sum(BilledQty)/Avg(CaseQty) "Total_Sale",Avg(SaleRate) Rate,Sum(DiscAmt) as DiscAmt,Sum(BilledQty)*Avg(SaleRate) Amount,Sum(BilledQty)*Avg(b.weight) TotalWeight FROM tblhistory as a LEFT join tblitems as b on b.PlantID=a.PlantID and b.item_code=a.ItemID LEFT join tblitems_sub_groups as c on c.id=b.SubGrpID1 LEFT join tblitems_groups as e on e.id=b.group_id  where a.PlantID='.$selected_company.' and a.FY="'.$fy.'" AND cast(a.TransDate2 as date) BETWEEN "'.$from_date.'" AND "'.$to_date.'" and TType2="Order" and transId is not null GROUP BY b.description,c.name,e.name ORDER by e.name,c.name,b.description asc)x group by Division';
			
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
		}
		
		
		public function get_client_data()
		{    
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblclients.userid as userid,tblclients.AccountID as AccountID,tblclients.company,tblclients.phonenumber,tblclients.state,tblclients.address,tblclients.StationName,tblclients.city,tblclients.active,tblcontacts.FLNO1,tblcontacts.expiry_licence');
			$this->db->join('tblcontacts', 'tblclients.AccountID=tblcontacts.AccountID AND tblclients.PlantID=tblcontacts.PlantID', 'INNER');
			$this->db->where('tblcontacts.expiry_licence IS NOT NULL');
			$this->db->where('tblclients.SubActGroupID1', "100056");
			$this->db->where('tblclients.PlantID', $selected_company);
			$this->db->order_by('tblcontacts.expiry_licence ASC');
			
			$result= $this->db->get(db_prefix() . 'clients')->result_array();
			return $result;
		}
		
		// Party Item Wise Report body data
		public function GetPartyCDReportBodyData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblsalesmaster.*,tblclients.company');
			$this->db->join('tblclients', 'tblclients.AccountID=tblsalesmaster.AccountID AND tblclients.PlantID=tblsalesmaster.PlantID', 'INNER');
			$this->db->where('tblsalesmaster.cd_applicable',"Y");
			// $this->db->where('tblsalesmaster.DiscAmt >',"0");
			if(!empty($AccountID))
			{
				$this->db->where('tblsalesmaster.AccountID', $AccountID);
			}
			$this->db->where('tblsalesmaster.PlantID', $selected_company);
			$this->db->where(db_prefix().'salesmaster.Transdate>=',$from_date.' 00:00:00');
			$this->db->where(db_prefix().'salesmaster.Transdate<=',$to_date.' 23:59:59');
			$this->db->order_by('tblsalesmaster.Transdate ASC');
			
			$result= $this->db->get(db_prefix() . 'salesmaster')->result_array();
			return $result;
			
		}
		
		// get Item List
		function ItemListFG(){
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$maingroup = array('1');
			
			$this->db->select(db_prefix() . 'items.*');
			$this->db->from(db_prefix() . 'items');
			$this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'inner');
			$this->db->join('tblitems_main_groups', 'tblitems_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_group_id', 'inner');
			$this->db->where_in('tblitems_main_groups.id', $maingroup);
			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
			$this->db->order_by(db_prefix() . 'items.description', 'ASC');
			return $records = $this->db->get()->result_array();
			
			
		}
		
		// public function GetSaleItemFlowReportBodyData($filterdata)
		// { 
		
		// $from_date = to_sql_date($filterdata["from_date"]);
		// $to_date = to_sql_date($filterdata["to_date"]);
		// $ItemID = $filterdata["ItemID"];
		// $fy = $this->session->userdata('finacial_year');
		// $selected_company = $this->session->userdata('root_company');
		
		// $sql = 'SELECT tblclients.company,tblhistory.TransID,tblhistory.TransDate,tblhistory.TransDate2,( SELECT SUM(history.BilledQty) FROM tblhistory AS history WHERE history.TransID = tblhistory.TransID AND history.ItemID = "'.$ItemID.'" AND TType="O" AND TType2="Order") AS BilledQty,
		// ( SELECT SUM(history.BilledQty*avg(tblitems.weight)) FROM tblhistory AS history WHERE history.TransID = tblhistory.TransID AND history.ItemID = "FG0001" AND TType="O" AND TType2="Order") AS TotalWeight,
		// ( SELECT SUM(history.BilledQty/tblhistory.CaseQty) FROM tblhistory AS history WHERE history.TransID = tblhistory.TransID AND history.ItemID = "FG0001" AND TType="O" AND TType2="Order") AS totCaseQty
		// FROM '.db_prefix() . 'history
		// INNER JOIN tblclients ON tblclients.AccountID = tblhistory.AccountID
		// INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID
		// WHERE '.db_prefix() . 'history.FY = '.$fy.'  AND '.db_prefix() . 'history.PlantID = '.$selected_company.' AND '.db_prefix() . 'history.ItemID = "'.$ItemID.'" AND '.db_prefix() . 'history.TType = "O" AND tblhistory.TType2 = "Order" AND tblhistory.TransID IS NOT NULL
		// AND tblhistory.TransDate2 BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
		// GROUP BY tblhistory.TransID,tblclients.AccountID ORDER BY tblhistory.TransDate2 ASC ';
		
		// $result = $this->db->query($sql)->result_array();
		
		// return $result;
		// }
		
		public function GetSaleItemFlowReportBodyData($filterdata)
		{
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$ItemID = $filterdata["ItemID"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$dbprefix = db_prefix();  // If you have a table prefix
			
			// Subquery 1: BilledQty for selected ItemID
			$subquery_billedQty = $this->db
			->select('TransID, SUM(BilledQty) AS BilledQty')
			->from($dbprefix . 'history')
			->where('ItemID', $ItemID)
			->where('TType', 'O')
			->where('TType2', 'Order')
			->group_by('TransID')
			->get_compiled_select();
			
			// Subquery 2: TotalWeight for FG0001
			$subquery_totalWeight = $this->db
			->select('h2.TransID, SUM(h2.BilledQty * i2.weight) AS TotalWeight')
			->from($dbprefix . 'history h2')
			->join('tblitems i2', 'i2.item_code = h2.ItemID', 'inner')
			->where('h2.ItemID', $ItemID)
			->where('h2.TType', 'O')
			->where('h2.TType2', 'Order')
			->group_by('h2.TransID')
			->get_compiled_select();
			
			// Subquery 3: totCaseQty for FG0001
			$subquery_totCaseQty = $this->db
			->select('TransID, SUM(BilledQty / CASE WHEN CaseQty = 0 THEN 1 ELSE CaseQty END) AS totCaseQty')
			->from($dbprefix . 'history')
			->where('ItemID', $ItemID)
			->where('TType', 'O')
			->where('TType2', 'Order')
			->group_by('TransID')
			->get_compiled_select();
			
			// Main query with LEFT JOINs to subqueries
			$this->db->select('
			c.company,
			h.TransID,
			h.TransDate,
			h.TransDate2,
			bq.BilledQty,
			tw.TotalWeight,
			cq.totCaseQty
			');
			$this->db->from($dbprefix . 'history h');
			$this->db->join('tblclients c', 'c.AccountID = h.AccountID', 'inner');
			$this->db->join('tblitems i', 'i.item_code = h.ItemID', 'inner');
			
			$this->db->join("($subquery_billedQty) bq", 'bq.TransID = h.TransID', 'left');
			$this->db->join("($subquery_totalWeight) tw", 'tw.TransID = h.TransID', 'left');
			$this->db->join("($subquery_totCaseQty) cq", 'cq.TransID = h.TransID', 'left');
			
			// WHERE conditions
			$this->db->where('h.FY', $fy);
			$this->db->where('h.PlantID', $selected_company);
			$this->db->where('h.ItemID', $ItemID);
			$this->db->where('h.TType', 'O');
			$this->db->where('h.TType2', 'Order');
			$this->db->where('h.TransID IS NOT NULL');
			$this->db->where('h.TransDate2 >=', $from_date . ' 00:00:00');
			$this->db->where('h.TransDate2 <=', $to_date . ' 23:59:59');
			
			$this->db->group_by(['h.TransID', 'c.AccountID']);
			$this->db->order_by('h.TransDate2', 'ASC');
			
			$query = $this->db->get();
			return $query->result_array();
		}
		
		
		
		public function TopItem($data = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if(!empty($data)){
				$from_date = to_sql_date($data["from_date"]);
				$to_date = to_sql_date($data["to_date"]);
				}else{
				$from_date = date('Y-m-d');
				$to_date = date('Y-m-d');
			}
			
			$this->db->select(db_prefix().'history.ItemID, SUM(NetChallanAmt) as TotalSale,'.db_prefix().'items.description as description_name');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.ItemID');
			$this->db->order_by("TotalSale", "DESC");
			$this->db->limit(1);
			return $this->db->get(db_prefix().'history')->row();
		}
		
		public function TopSaleAmtParty($data ="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if(!empty($data)){
				$from_date = to_sql_date($data["from_date"]);
				$to_date = to_sql_date($data["to_date"]);
				}else{
				$from_date = date('Y-m-d');
				$to_date = date('Y-m-d');
			}
			
			$this->db->select(db_prefix().'history.ItemID, SUM(NetChallanAmt) as TotalSale,'.db_prefix().'clients.company');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.AccountID');
			$this->db->order_by("TotalSale", "DESC");
			$this->db->limit(1);
			return $this->db->get(db_prefix().'history')->row();
		}
		public function TotalSaleAmtByAnyParty($data ="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if(!empty($data)){
				$from_date = to_sql_date($data["from_date"]);
				$to_date = to_sql_date($data["to_date"]);
				}else{
				$from_date = date('Y-m-d');
				$to_date = date('Y-m-d');
			}
			
			$this->db->select('SUM(NetChallanAmt) as TotalSale');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->order_by("TotalSale", "DESC");
			$this->db->limit(1);
			return $this->db->get(db_prefix().'history')->row();
		}
		
		public function GetTotalSaleAmt()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('SUM(NetChallanAmt) as TotalSale');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', date('Y-m-01').' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', date('Y-m-d').' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->order_by("TotalSale", "DESC");
			$this->db->limit(1);
			return $this->db->get(db_prefix().'history')->row();
		}
		
		public function GetTotalSaleRtnAmt()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('SUM(NetChallanAmt) as TotalSale,tblhistory.TType2');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', date('Y-m-01').' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', date('Y-m-d').' 23:59:59');
			$this->db->where('tblhistory.TType ', 'R');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->order_by("TotalSale", "DESC");
			$this->db->group_by('TType2');
			$result =  $this->db->get(db_prefix().'history')->result_array();
			$FreshRtn = 0;
			$DmgRtn = 0;
			foreach($result as $value){
				if($value["TType2"] == "Damage"){
					$DmgRtn = $value["TotalSale"];
				}
				if($value["TType2"] == "Fresh"){
					$FreshRtn = $value["TotalSale"];
				}
			}
			$response->FreshRtn = $FreshRtn;
			$response->DmgRtn = $DmgRtn;
			return $response;
		}
		public function TodaysSale()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('SUM(NetChallanAmt) as TotalSale');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate2 >=', date('Y-m-d').' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', date('Y-m-d').' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->order_by("TotalSale", "DESC");
			$this->db->limit(1);
			return $this->db->get(db_prefix().'history')->row();
		}
		public function AvgInvoiceAmtInCurrentMonth($data = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if(!empty($data)){
				$from_date = to_sql_date($data["from_date"]);
				$to_date = to_sql_date($data["to_date"]);
				}else{
				$from_date = date('Y-m-d');
				$to_date = date('Y-m-d');
			}
			$this->db->select('SUM(BillAmt) / COUNT(*) AS AvgAmt,COUNT(*) AS TotalInvoice');
			$this->db->where('tblsalesmaster.PlantID',$selected_company);
			$this->db->where('tblsalesmaster.FY',$fy);
			$this->db->where('tblsalesmaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblsalesmaster.Transdate <=', $to_date.' 23:59:59');
			$this->db->limit(1);
			return $this->db->get(db_prefix().'salesmaster')->row();
		}
		public function NewParties($data = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if(!empty($data)){
				$from_date = to_sql_date($data["from_date"]);
				$to_date = to_sql_date($data["to_date"]);
				}else{
				$from_date = date('Y-m-d');
				$to_date = date('Y-m-d');
			}
			
			$this->db->select('COUNT(*) AS NewParty');
			$this->db->where('tblclients.PlantID',$selected_company);
			$this->db->where('tblclients.SubActGroupID1','100056');
			$this->db->where('tblclients.datecreated >=', $from_date.' 00:00:00');
			$this->db->where('tblclients.datecreated <=', $to_date.' 23:59:59');
			$this->db->limit(1);
			return $this->db->get(db_prefix().'clients')->row();
		}
		public function GetTotalGstAmt($data = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if(!empty($data)){
				$from_date = to_sql_date($data["from_date"]);
				$to_date = to_sql_date($data["to_date"]);
				}else{
				$from_date = date('Y-m-d');
				$to_date = date('Y-m-d');
			}
			
			$this->db->select('SUM(cgstamt + sgstamt + igstamt) AS TotalGST');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->limit(1);
			return $this->db->get(db_prefix().'history')->row();
		}
		public function CustomerCount()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql = "SELECT COUNT(*) AS TotalCustomer FROM tblclients where SubActGroupID1 = '100056' ";
			$result = $this->db->query($sql)->row();
			return $result;
		}
		public function ItemCount()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql = "SELECT COUNT(*) AS TotalItem FROM tblitems where MainGrpID='1' ";
			$result = $this->db->query($sql)->row();
			return $result;
		}
		public function GetTotalPendingOrder()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if(!empty($data)){
				$from_date = to_sql_date($data["from_date"]);
				$to_date = to_sql_date($data["to_date"]);
				}else{
				$from_date = date('Y-m-d');
				$to_date = date('Y-m-d');
			}
			
			$this->db->select('COUNT(tblordermaster.OrderID) AS TotalCount');
			$this->db->where('tblordermaster.PlantID',$selected_company);
			$this->db->where('tblordermaster.FY',$fy);
			$this->db->where('tblordermaster.OrderStatus',"O");
			$this->db->where('tblordermaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblordermaster.Transdate <=', $to_date.' 23:59:59');
			$this->db->where('tblordermaster.SalesID IS NULL');
			//$this->db->limit(1);
			return $this->db->get(db_prefix().'ordermaster')->row();
		}
		public function GetTopSellingItem($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// $month_input = $filterdata['month']; // Example: '2024-11'
			// $date = $month_input.'-01';//your given date
			// $first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			// $from_date = date("Y-m-d",$first_date_find);
			
			// $last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			// $last_date = date("Y-m-d",$last_date_find);
			
			// $Currentdate = date('Y-m-d');
			// if($last_date > $Currentdate){
			// $to_date = $Currentdate;
			// }else{
			// $to_date = $last_date;
			// }
			
			if(!empty($filterdata["from_date"])){
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			
			$ItemCount = $filterdata["MaxCount"];
			$state = $filterdata["state"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$Items = $filterdata["Items"];
			
			$chart = [];
			if($SubGroup){
				$this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items.description as description_name');
				}else{
				$this->db->select(db_prefix().'items_sub_groups.id as ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items_sub_groups.name as description_name');
			}
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			
			if($SubGroup){
				
				}else{
				$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($state)){
				$this->db->where('tblclients.state', $state);
			}
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				$this->db->group_by('tblhistory.ItemID');
				}else{
				$this->db->group_by('tblitems.SubGrpID1');
			}
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->order_by("total_qty", "DESC");
			
			$this->db->limit($ItemCount);
			$TopItem = $this->db->get(db_prefix().'history')->result_array();
			//return $TopItem;
			$itemIDs = [];
			$i=0;
			foreach ($TopItem as $key => $value) {
				array_push($itemIDs,$value['ItemID']);
				array_push($chart, [
				'name' 		=> $value['description_name'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				$i++;
			}
			
			
			/*if (!empty($itemIDs)) {
				if($ReportType == "Itemwise"){
				$this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty, SUM(NetChallanAmt) as total_amt,'.db_prefix().'items.description as description_name,tblhistory.AccountID');
				}else{
				$this->db->select(db_prefix().'items_sub_groups.id as ItemID, SUM(BilledQty) as total_qty, SUM(NetChallanAmt) as total_amt,'.db_prefix().'items_sub_groups.name as description_name,tblhistory.AccountID');
				}
				
				$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
				$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
				if($ReportType == "Groupwise"){
				$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
				}
				$this->db->where('tblhistory.PlantID',$selected_company);
				$this->db->where('tblhistory.FY',$fy);
				$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
				$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
				if($ReportType == "Itemwise"){
				
				$this->db->where_in('tblhistory.ItemID', $itemIDs);
				}else{
				$this->db->where_in('tblitems_sub_groups.id', $itemIDs);
				}
				$this->db->where('tblhistory.TType ', 'O');
				$this->db->where('tblhistory.TType2 ', 'Order');
				$this->db->where('tblhistory.TransID IS NOT NULL');
				if(!empty($state)){
				$this->db->where('tblclients.state', $state);
				}
				
				if(!empty($SubGroup) && $ReportType == "Groupwise"){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				}
				
				if(!empty($SubGroup) && $ReportType == "Itemwise"){
				$this->db->where_in('tblitems.item_code', $Items);
				}
				if($ReportType == "Itemwise"){
				$this->db->group_by('tblhistory.AccountID,tblhistory.ItemID');
				}else{
				$this->db->group_by('tblhistory.AccountID,tblitems.SubGrpID1');
				}
				$this->db->order_by("tblhistory.AccountID", "DESC");
				$ItemCustomers = $this->db->get(db_prefix().'history')->result_array();
				} else {
				$ItemCustomers = [];
				}
				$AccountIDs = [];
				$itemData = [];
				foreach ($ItemCustomers as $key => $value) {
				array_push($AccountIDs,$value['AccountID']);
				if (!in_array($value['description'], $itemIDs)) {
				$itemData[$value['ItemID']] = $value['description_name'];
				}
				}
				$AccountIDs = array_unique($AccountIDs);
				if (!empty($AccountIDs)) {
				$this->db->select('tblclients.*');
				//Trade Payable
				$this->db->where('tblclients.PlantID', $selected_company);
				$this->db->where_in('tblclients.AccountID', $AccountIDs);
				$this->db->order_by('tblclients.company', 'ASC');
				$customerList = $this->db->get('tblclients')->result_array();
				} else {
				$customerList = [];
			}*/
			
			$data = [
			'ChartData' => $chart,
			/*'ItemCustomers' => $ItemCustomers,
				'customerList' => $customerList,
			'itemIDs' => $itemData,*/
			];
			
			return $data;
		}
		public function GetTopSellingItemInventory($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// $month_input = $filterdata['month']; // Example: '2024-11'
			// $date = $month_input.'-01';//your given date
			// $first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			// $from_date = date("Y-m-d",$first_date_find);
			
			// $last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			// $last_date = date("Y-m-d",$last_date_find);
			
			// $Currentdate = date('Y-m-d');
			// if($last_date > $Currentdate){
			// $to_date = $Currentdate;
			// }else{
			// $to_date = $last_date;
			// }
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$ItemCount = $filterdata["MaxCount"];
			$state = $filterdata["state"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$Items = $filterdata["Items"];
			
			$chart = [];
			if($SubGroup){
				$this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items.description as description_name');
				}else{
				$this->db->select(db_prefix().'items_sub_groups.id as ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items_sub_groups.name as description_name');
			}
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			
			if($SubGroup){
				
				}else{
				$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($state)){
				$this->db->where('tblclients.state', $state);
			}
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				$this->db->group_by('tblhistory.ItemID');
				}else{
				$this->db->group_by('tblitems.SubGrpID1');
			}
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->order_by("total_qty", "DESC");
			
			$this->db->limit($ItemCount);
			$TopItem = $this->db->get(db_prefix().'history')->result_array();
			//return $TopItem;
			$itemIDs = [];
			$i=0;
			foreach ($TopItem as $key => $value) {
				array_push($itemIDs,$value['ItemID']);
				array_push($chart, [
				'name' 		=> $value['description_name'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				$i++;
			}
			
			
			
			$data = [
			'ChartData' => $chart,
			];
			
			return $data;
		}
		function ItemListData($filterdata){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$maingroup = array('1');
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$SubGroup = $filterdata["SubGroup"];
			$MaxCount = $filterdata["MaxCount"];
			$ReportIn = $filterdata["ReportIn"];
			
			$this->db->select('tblitems.item_code,tblitems.description');
			$this->db->from(db_prefix() . 'items');
			$this->db->join('tblitems_sub_groups', 'tblitems_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'inner');
			$this->db->join('tblitems_main_groups', 'tblitems_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_group_id', 'inner');
			$this->db->where_in('tblitems_main_groups.id', $maingroup);
			if(!empty($SubGroup)){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
			$this->db->order_by(db_prefix() . 'items.description', 'ASC');
			return $records = $this->db->get()->result_array();
			
			
		}
		public function GetSalesItemByFilter($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$SubGroup = $filterdata["SubGroup"];
			$MaxCount = $filterdata["MaxCount"];
			$ReportIn = $filterdata["ReportIn"];
			
			$chart = [];
			
			$this->db->select(db_prefix().'history.ItemID,'.db_prefix().'items.description, SUM(NetChallanAmt) as TotalAmt, SUM(BilledQty) as TotalQty');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			if(!empty($SubGroup)){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.ItemID');
			
			// $this->db->limit($MaxCount);
			
			$data = $this->db->get(db_prefix().'history')->result_array();
			
			return $data;
		}
		public function GetSalesReturnReport($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			if(!empty($filterdata["from_date"])){
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			$SubGroup = $filterdata["SubGroup"];
			$MaxCount = $filterdata["MaxCount"];
			$ReportIn = $filterdata["ReportIn"];
			
			$chart = [];
			
			$this->db->select(db_prefix().'history.ItemID,'.db_prefix().'items.description, SUM(NetChallanAmt) as ReturnAmt, SUM(BilledQty) as ReturnQty');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblhistory.ItemID', $filterdata["Items"]);
			}
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType', 'R');
			$this->db->where('tblhistory.TType2', $filterdata["TType2"]);
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.ItemID');
			
			if($ReportIn == "amount"){
				$this->db->order_by("ReturnAmt", "DESC");
				}else{
				$this->db->order_by("ReturnQty", "DESC");
			}
			if($filterdata["Items"]){
				
				}else{
				$this->db->limit($MaxCount);
			}
			$data = $this->db->get(db_prefix().'history')->result_array();
			return $data;
		}
		
		public function GetSalesReturnReportDetail($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$SubGroup = $filterdata["SubGroup"];
			$MaxCount = $filterdata["MaxCount"];
			$ReportIn = $filterdata["ReportIn"];
			$ReportType = $filterdata["ReportType"];
			$Sort = $filterdata["Sort"];
			
			
			$this->db->select(db_prefix().'history.AccountID,tblclients.company,'.db_prefix().'history.ItemID,'.db_prefix().'items.description, SUM(NetChallanAmt) as ReturnAmt, SUM(BilledQty) as ReturnQty');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			if(!empty($SubGroup)){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'R');
			if($ReportType == "fresh"){
				$this->db->where('tblhistory.TType2 ', 'Fresh');
				}else{
				$this->db->where('tblhistory.TType2 ', 'Damage');
			}
			$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.AccountID,tblhistory.ItemID');
			
			if($ReportIn == "amount"){
				if($Sort == "Highest"){
					$this->db->order_by("ReturnAmt", "DESC");
					}else{
					$this->db->order_by("ReturnAmt", "ASC");
				}
				}else{
				if($Sort == "Highest"){
					$this->db->order_by("ReturnQty", "DESC");
					}else{
					$this->db->order_by("ReturnQty", "ASC");
				}
			}
			
			$data = $this->db->get(db_prefix().'history')->result_array();
			
			return $data;
		}
		
		public function GetTopSellingCustomer($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			// $month_input = $filterdata['month']; // Example: '2024-11'
			// $date = $month_input.'-01';//your given date
			// $first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			// $from_date = date("Y-m-d",$first_date_find);
			
			// $last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			// $last_date = date("Y-m-d",$last_date_find);
			
			// $Currentdate = date('Y-m-d');
			// if($last_date > $Currentdate){
			// $to_date = $Currentdate;
			// }else{
			// $to_date = $last_date;
			// }
			
			if(!empty($filterdata["from_date"])){
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			$CustomerCount = $filterdata["MaxCount"];
			$state = $filterdata["state"];
			$SubGroup = $filterdata["SubGroup"];
			$Items = $filterdata["Items"];
			
			$chart = [];
			
			$this->db->select(db_prefix().'history.AccountID, SUM(NetChallanAmt) as total_amt,'.db_prefix().'clients.company');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($state)){
				$this->db->where('tblclients.state', $state);
			}
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->group_by('tblhistory.AccountID');
			$this->db->order_by("total_amt", "DESC");
			$this->db->limit($CustomerCount);
			$TopCustomer = $this->db->get(db_prefix().'history')->result_array();
			
			$ActIDs = [];
			foreach ($TopCustomer as $key => $value) {
				array_push($ActIDs,$value['AccountID']);
				array_push($chart, [
				'name' 		=> $value['company'],
				'y' 		=>	(int)$value['total_amt'],
				'z' 		=> 100,
				'label' 		=> "Amount"
				]);
			}
			
			
			
			if (!empty($ActIDs)) {
				$this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty, SUM(NetChallanAmt) as total_amt,'.db_prefix().'items.description as description_name,tblhistory.AccountID,'.db_prefix().'clients.company');
				$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
				$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
				$this->db->where('tblhistory.PlantID',$selected_company);
				$this->db->where('tblhistory.FY',$fy);
				$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
				$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
				$this->db->where_in('tblhistory.AccountID', $ActIDs);
				$this->db->where('tblhistory.TType ', 'O');
				$this->db->where('tblhistory.TType2 ', 'Order');
				$this->db->where('tblhistory.TransID IS NOT NULL');
				if(!empty($state)){
					$this->db->where('tblclients.state', $state);
				}
				$this->db->group_by('tblhistory.AccountID,tblhistory.ItemID');
				$this->db->order_by("total_amt", "DESC");
				$ItemCustomers = $this->db->get(db_prefix().'history')->result_array();
				} else {
				$ItemCustomers = [];
			}
			
			// print_r($ItemCustomers);die;
			$ItemIDs = [];
			$CustomerData = [];
			foreach ($ItemCustomers as $key => $value) {
				array_push($ItemIDs,$value['ItemID']);
				if (!in_array($value['company'], $ActIDs)) {
					$CustomerData[$value['AccountID']] = $value['company'];
				}
			}
			
			
			$ItemIDs = array_unique($ItemIDs);
			if (!empty($ItemIDs)) {
				$this->db->select('tblitems.*');
				//Trade Payable
				$this->db->where('tblitems.PlantID', $selected_company);
				$this->db->where_in('tblitems.item_code', $ItemIDs);
				$this->db->order_by('tblitems.description', 'ASC');
				$ItemList = $this->db->get('tblitems')->result_array();
				} else {
				$ItemList = [];
			}
			
			
			// print_r($ItemList);die;
			$data = [
			'ChartData' => $chart,
			'Transaction' => $ItemCustomers,
			'ItemList' => $ItemList,
			'CustomerIDs' => $CustomerData,
			];
			return $data;
		}
		
		public function GetItemList($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$SubGroup = $filterdata["SubGroup"]; 
			
			$SubGroup = implode(',', array_map('intval', $SubGroup));
			// print_r($SubGroup);die;
			
			$sql1 = "";
			if(!empty($filterdata["SubGroup"])){
				$sql1 .= ' AND tblitems.SubGrpID1 IN('.$SubGroup.') ';
			}
			$sql = 'SELECT  tblitems.PlantID,tblitems.item_code,tblitems.description,tblitems.case_qty,tblitems.unit,
			(SELECT SUM(tblstockmaster.OQty) AS OQty FROM tblstockmaster WHERE tblstockmaster.ItemID=tblitems.item_code AND tblstockmaster.PlantID = '.$selected_company.' AND tblstockmaster.FY = "'.$fy.'" AND tblstockmaster.cnfid = "1" GROUP BY tblstockmaster.ItemID,tblstockmaster.PlantID,tblstockmaster.FY) AS OQty
			FROM `tblitems` 
			WHERE tblitems.MainGrpID = "1" AND tblitems.isactive = "Y" AND tblitems.PlantID = '.$selected_company.$sql1;
			
			$sql .= ' ORDER BY tblitems.SubGrpID1 ASC';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		
		public function GetStockData($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($filterdata["from_date"]).' 00:00:00';
			$to_date = to_sql_date($filterdata["to_date"]).' 23:59:59';
			
			$SubGroup = $filterdata["SubGroup"]; 
			
			$SubGroup = implode(',', array_map('intval', $SubGroup));
			
			$sql = 'SELECT tblhistory.*,tblitems.item_code FROM `tblhistory` 
			INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
			WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransDate2 BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND tblhistory.BillID IS NOT NULL AND tblitems.MainGrpID = "1"';
			
			if(!empty($filterdata["SubGroup"])){
				$sql1 .= ' AND tblitems.SubGrpID1 IN('.$SubGroup.') ';
			}
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function get_item_open_qty($filterdata)
		{
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$SubGroup = $filterdata["SubGroup"]; 
			
			$SubGroup = implode(',', array_map('intval', $SubGroup));
			$from_date_value = '20'.$fy.'-04-01';
			
			if($from_date == $from_date_value){
				$day_before = $from_date_value;
				}else{
				$day_before = date( 'Y-m-d', strtotime( $from_date . ' -1 day' ) );
			}
			$first_date = $from_date_value;
			
			$sql = 'SELECT tblhistory.TType,tblhistory.TType2,tblhistory.ItemID,
			(SELECT SUM(tblstockmaster.OQty) FROM tblstockmaster WHERE tblstockmaster.ItemID=tblhistory.ItemID AND tblstockmaster.PlantID = '.$selected_company.' AND tblstockmaster.FY = "'.$fy.'" AND tblstockmaster.cnfid = "1" GROUP BY tblstockmaster.ItemID,tblstockmaster.PlantID,tblstockmaster.FY) AS OQty,
			SUM(tblhistory.BilledQty)as billsum FROM `tblhistory` 
			INNER JOIN tblitems ON tblitems.item_code=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
			WHERE tblhistory.PlantID = '.$selected_company.' AND tblhistory.FY = "'.$fy.'" AND tblhistory.TransDate2 BETWEEN "'.$first_date.' 00:00:00" AND "'.$day_before.' 23:59:59" AND tblhistory.BillID IS NOT NULL AND tblitems.MainGrpID = "1" ';
			
			if(!empty($filterdata["SubGroup"])){
				$sql1 .= ' AND tblitems.SubGrpID1 IN('.$SubGroup.') ';
			}	
			
			$sql .= ' GROUP BY tblhistory.ItemID,tblhistory.TType,tblhistory.TType2 ';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function GetClientsForPerformReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$Route = $data["Route"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblclients.*');
			
			$this->db->join(db_prefix() . 'accountroutes', '' . db_prefix() . 'accountroutes.AccountID = ' . db_prefix() . 'clients.AccountID');
			$this->db->where('tblclients.PlantID ', $selected_company);
			$this->db->where('tblclients.SubActGroupID1 ', '100056');
			if($Route != ''){
				$this->db->where('tblaccountroutes.RouteID ', $Route);
			}
			$this->db->group_by('tblclients.AccountID');
			$this->db->order_by('tblclients.company','ASC');
			return $this->db->get('tblclients')->result_array();
			
		}
		
		public function GetSaleByDate($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'salesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'salesmaster.PlantID="'.$selected_company.'" GROUP BY tblordermaster.AccountID2';
			
			$sql ='SELECT tblordermaster.AccountID2 AS AccountID,COALESCE(SUM(BillAmt),0) As BillAmt FROM '.db_prefix().'salesmaster
			INNER JOIN tblordermaster ON tblordermaster.OrderID = tblsalesmaster.OrderID 
			WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function GetSaleReturnByDate($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'salesreturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'salesreturn.PlantID="'.$selected_company.'" GROUP BY AccountID2';
			
			$sql ='SELECT tblsalesreturn.AccountID2 AS AccountID,COALESCE(SUM(BillAmt),0) As BillAmt FROM '.db_prefix().'salesreturn WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function GetPaymentByDate($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'accountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'accountledger.PlantID="'.$selected_company.'" AND '.db_prefix().'accountledger.PassedFrom="PAYMENTS" GROUP BY AccountID';
			
			$sql ='SELECT AccountID,COALESCE(SUM(Amount),0) AS Amount FROM '.db_prefix().'accountledger WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function GetCratesByDate($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'accountcrates.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'accountcrates.PlantID="'.$selected_company.'"  AND PassedFrom !="OPENCRATES" GROUP BY AccountID,TType';
			
			$sql ='SELECT AccountID,TType,COALESCE(SUM(Qty),0) As Qty  FROM '.db_prefix().'accountcrates WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function DayBeforeTransactionCrate($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$FirstDate = '20'.$fy.'-04-01';
			if($from_date == $FirstDate){
                $FromDate = $FirstDate;
                $ToDate = to_sql_date($data["from_date"]);
				return;
				}else{
                $FromDate = $FirstDate;
                $ToDate = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
				$sql1 = '('.db_prefix().'accountcrates.Transdate BETWEEN "'.$FirstDate.' 00:00:00" AND "'.$ToDate.' 23:59:59") AND '.db_prefix().'accountcrates.PlantID="'.$selected_company.'"  AND PassedFrom !="OPENCRATES"  GROUP BY AccountID,TType';
				
				$sql ='SELECT AccountID,TType,COALESCE(SUM(Qty),0) As Qty  FROM '.db_prefix().'accountcrates WHERE '.$sql1;
				$result = $this->db->query($sql)->result_array();
				return $result;
			}
			
		}
		public function GetOpeningCrates($data)
		{  
			
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$FirstDate = '20'.$fy.'-04-01';
			
			$FromDate = $FirstDate;
			$sql1 = '('.db_prefix().'accountcrates.Transdate BETWEEN "'.$FirstDate.' 00:00:00" AND "'.date('Y-m-d').' 23:59:59") AND '.db_prefix().'accountcrates.PlantID="'.$selected_company.'" AND PassedFrom ="OPENCRATES"  GROUP BY AccountID,TType';
			
			$sql ='SELECT AccountID,TType,COALESCE(SUM(Qty),0) As Qty  FROM '.db_prefix().'accountcrates WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
			
			
		}
		
		public function CityWiseSales($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if(!empty($filterdata["from_date"])){
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			$ItemCount = $filterdata["MaxCount"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$Items = $filterdata["Items"];
			
			$chart = [];
			$Production = [];
			$this->db->select(db_prefix().'xx_citylist.city_name,tblxx_citylist.latitude,tblxx_citylist.longitude, SUM(BilledQty) as total_qty');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'xx_citylist', 'tblxx_citylist.id = tblclients.city','INNER');
			
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType', 'O');
			$this->db->where('tblhistory.TType2', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->group_by('tblxx_citylist.city_name');
			$this->db->order_by("total_qty", "DESC");
			
			$this->db->limit($ItemCount);
			$TopItem = $this->db->get(db_prefix().'history')->result_array();
			
			$i=0;
			foreach ($TopItem as $key => $value) {
				array_push($chart, [
				'name' 		=> $value['city_name'],
				'lat' 		=>	(float)$value['latitude'],
				'lon' 		=>	(float)$value['longitude'],
				'sales' 		=>	(int)$value['total_qty'],
				]);
				$i++;
			}
			
			
			return $chart;
		}
		public function CityWiseSalesNew($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if(!empty($filterdata["from_date"])){
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$chart = [];
			$Production = [];
			$this->db->select(db_prefix().'xx_citylist.city_name,tblxx_citylist.latitude,tblxx_citylist.longitude, SUM(BilledQty) as total_qty');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'xx_citylist', 'tblxx_citylist.id = tblclients.city','INNER');
			
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType', 'O');
			$this->db->where('tblhistory.TType2', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->group_by('tblxx_citylist.city_name');
			$this->db->order_by("total_qty", "DESC");
			
			$this->db->limit($ItemCount);
			$TopItem = $this->db->get(db_prefix().'history')->result_array();
			
			$i=0;
			foreach ($TopItem as $key => $value) {
				array_push($chart, [
				'name' 		=> $value['city_name'],
				'lat' 		=>	(float)$value['latitude'],
				'lon' 		=>	(float)$value['longitude'],
				'sales' 		=>	(int)$value['total_qty'],
				]);
				$i++;
			}
			
			
			return $chart;
		}
		public function CityWiseCustomers()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$chart = [];
			$this->db->select(db_prefix().'xx_citylist.city_name,tblxx_citylist.latitude,tblxx_citylist.longitude,COUNT(*) as Customers');
			
			$this->db->join(db_prefix() . 'xx_citylist', 'tblxx_citylist.id = tblclients.city','INNER');
			
			$this->db->where('tblclients.PlantID',$selected_company);
			$this->db->group_by('tblxx_citylist.city_name');
			$this->db->order_by("Customers", "DESC");
			
			$TopItem = $this->db->get(db_prefix().'clients')->result_array();
			
			$i=0;
			foreach ($TopItem as $key => $value) {
				array_push($chart, [
				'name' 		=> $value['city_name'],
				'lat' 		=>	(float)$value['latitude'],
				'lon' 		=>	(float)$value['longitude'],
				'sales' 		=>	(int)$value['Customers'],
				]);
				$i++;
			}
			
			
			return $chart;
		}
		public function GetFreshVsDamageSalesReturn($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			if(!empty($filterdata["from_date"])){
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
				}else{
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
			}
			$SubGroup = $filterdata["SubGroup"];
			$MaxCount = $filterdata["MaxCount"];
			$ReportIn = $filterdata["ReportIn"];
			
			$chart = [];
			
			$this->db->select(db_prefix().'history.ItemID,'.db_prefix().'items.description,Year(tblhistory.TransDate2) as year,MONTHNAME(tblhistory.TransDate2) as month,MONTH(tblhistory.TransDate2) as MonthNumber, SUM(NetChallanAmt) as ReturnAmt, SUM(BilledQty) as ReturnQty');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblhistory.ItemID', $filterdata["Items"]);
			}
			
			$this->db->where('tblhistory.TType', 'R');
			$this->db->where('tblhistory.TType2', $filterdata["TType2"]);
			$this->db->where('tblhistory.TransID IS NOT NULL');
			
			if($ReportIn == "amount"){
				$this->db->order_by("ReturnAmt", "DESC");
				}else{
				$this->db->order_by("ReturnQty", "DESC");
			}
			$this->db->group_by(array("year","month"));
			
			$data = $this->db->get(db_prefix().'history')->result_array();
			return $data;
		}
		
		
		// MainItemGroup Table Data
		public function get_MainItemGroup_data(){
			
			$this->db->select(db_prefix() . 'items_main_groups.*');
			$this->db->from(db_prefix() . 'items_main_groups');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}
		
		public function TotalSaleAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('ROUND(COALESCE(SUM(tblhistory.NetChallanAmt),0),2) as NetChallanAmt');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			if(!empty($TradeType) || !empty($Station) || !empty($City)){
			    $this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID');
			}
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblhistory.TType', 'O');
			$this->db->where('tblhistory.TType2', 'Order');
			$Transaction = $this->db->get('tblhistory')->row();
			return $Transaction->NetChallanAmt;
		}
		public function TotalDiscAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('ROUND(COALESCE(SUM(tblhistory.DiscAmt),0),2) as DiscAmt');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblhistory.TType', 'O');
			$this->db->where('tblhistory.TType2', 'Order');
			$Transaction = $this->db->get('tblhistory')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->DiscAmt;
		}
		public function TotalFreshRtnAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('ROUND(COALESCE(SUM(tblhistory.NetChallanAmt),0),2) as NetChallanAmt');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblhistory.TType', 'R');
			$this->db->where('tblhistory.TType2', 'Fresh');
			$Transaction = $this->db->get('tblhistory')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->NetChallanAmt;
		}
		public function TotalDamageRtnAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('ROUND(COALESCE(SUM(tblhistory.NetChallanAmt),0),2) as NetChallanAmt');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblhistory.TType', 'R');
			$this->db->where('tblhistory.TType2', 'Damage');
			$Transaction = $this->db->get('tblhistory')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->NetChallanAmt;
		}
		public function TotalOrders($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('COUNT(*) as Total');
			
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
			$this->db->where('tblordermaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblordermaster.Transdate <=', $to_date.' 23:59:59');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblclients.AccountID', $AccountID);
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$Transaction = $this->db->get('tblordermaster')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->Total;
		}
		public function TotalInvoice($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('COUNT(*) as Total');
			
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
			$this->db->where('tblordermaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblordermaster.Transdate <=', $to_date.' 23:59:59');
			$this->db->where('tblordermaster.SalesID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblclients.AccountID', $AccountID);
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$Transaction = $this->db->get('tblordermaster')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->Total;
		}
		public function AvgOrderValue($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('ROUND(AVG(tblordermaster.OrderAmt),2) as AvgOrderAmt');
			
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
			$this->db->where('tblordermaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblordermaster.Transdate <=', $to_date.' 23:59:59');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblclients.AccountID', $AccountID);
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$Transaction = $this->db->get('tblordermaster')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->AvgOrderAmt;
		}
		public function AvgInvoiceValue($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('ROUND(AVG(tblordermaster.OrderAmt),2) as AvgInvoiceAmt');
			
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
			$this->db->where('tblordermaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblordermaster.Transdate <=', $to_date.' 23:59:59');
			$this->db->where('tblordermaster.SalesID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblclients.AccountID', $AccountID);
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$Transaction = $this->db->get('tblordermaster')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->AvgInvoiceAmt;
		}
		
		
		public function TotalSoldQty($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select('ROUND(COALESCE(SUM(tblhistory.BilledQty),0),0) as BilledQty');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->where('tblhistory.TType', 'O');
			$this->db->where('tblhistory.TType2', 'Order');
			$Transaction = $this->db->get('tblhistory')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			return $Transaction->BilledQty;
		}
		
		
		public function GetTopCustomer($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select(db_prefix().'history.AccountID, SUM(NetChallanAmt) as total_amt,'.db_prefix().'clients.company');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->group_by('tblhistory.AccountID');
			$this->db->order_by("total_amt", "DESC");
			$this->db->limit('10');
			$Transaction = $this->db->get('tblhistory')->result_array();
			
			
			$chart = [];
			// print_r($TransTypes);die;
			foreach ($Transaction as $name => $value) {
				$chart[] = [
				'name' 		=> $value['company'],
				'y' 		=>	(float)$value['total_amt'],
				'z'     => 100,
				'label' => "Total"
				];
			}
			return $chart;
		}
		public function GetTopGroupItem($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblitems_sub_groups.id as ItemID,tblitems_sub_groups.name as ItemName,SUM(NetChallanAmt) as total_amt');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,SUM(NetChallanAmt) as total_amt');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(NetChallanAmt) as total_amt');
			}
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
				}else{
				$this->db->group_by(db_prefix() . 'items.item_code');
			}
			$this->db->order_by("total_amt", "DESC");
			$this->db->limit('10');
			$Transaction = $this->db->get('tblhistory')->result_array();
			
			
			$chart = [];
			// print_r($TransTypes);die;
			foreach ($Transaction as $name => $value) {
				$chart[] = [
				'name' 		=> $value['ItemName'],
				'y' 		=>	(float)$value['total_amt'],
				'z'     => 100,
				'label' => "Total"
				];
			}
			return $chart;
		}
		
		public function GetStationWiseTopSale($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select(db_prefix().'StationMaster.StationName, SUM(NetChallanAmt) as total_amt');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'StationMaster', 'tblStationMaster.id = tblclients.StationName','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->group_by('tblStationMaster.id');
			$this->db->order_by("total_amt", "DESC");
			$this->db->limit('10');
			$Transaction = $this->db->get('tblhistory')->result_array();
			
			
			$chart = [];
			// print_r($TransTypes);die;
			foreach ($Transaction as $name => $value) {
				$chart[] = [
				'name' 		=> $value['StationName'],
				'y' 		=>	(float)$value['total_amt'],
				'z'     => 100,
				'label' => "Total"
				];
			}
			return $chart;
		}
		public function GetCityWiseTopSale($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			$this->db->select(db_prefix().'xx_citylist.city_name, SUM(NetChallanAmt) as total_amt');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'xx_citylist', 'tblxx_citylist.id = tblclients.city','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'O');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($TradeType)){
				$this->db->where('tblclients.Trade_Type', $TradeType);
			}
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($Station)){
				$this->db->where('tblclients.StationName', $Station);
			}
			if(!empty($City)){
				$this->db->where('tblclients.city', $City);
			}
			$this->db->group_by('tblxx_citylist.id');
			$this->db->order_by("total_amt", "DESC");
			$this->db->limit('10');
			$Transaction = $this->db->get('tblhistory')->result_array();
			
			
			$chart = [];
			// print_r($TransTypes);die;
			foreach ($Transaction as $name => $value) {
				$chart[] = [
				'name' 		=> $value['city_name'],
				'y' 		=>	(float)$value['total_amt'],
				'z'     => 100,
				'label' => "Total"
				];
			}
			return $chart;
		}
		public function GetMonthWiseSale($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			// Build months (Apr–Mar of selected FY)
			$Months = [];
			$start_year = 2000 + (int)$fy;  // Example: FY = 24 → 2024
			$end_year   = $start_year + 1;
			$currentYear = date('Y');
			$currentMonth = date('n');
			
			// April to Dec (start year)
			for ($i = 4; $i <= 12; $i++) {
				if ($start_year > $currentYear || ($start_year == $currentYear && $i > $currentMonth)) break;
				$date = "$start_year-$i-01";
				$Months[] = date("M-Y", strtotime($date));
			}
			
			// Jan to Mar (end year)
			for ($i = 1; $i <= 3; $i++) {
				if ($end_year > $currentYear || ($end_year == $currentYear && $i > $currentMonth)) break;
				$date = "$end_year-$i-01";
				$Months[] = date("M-Y", strtotime($date));
			}
			
			// Query sales data
			$this->db->select('DATE_FORMAT(tblhistory.TransDate, "%b-%Y") as month, tblsalesmaster.BT, SUM(NetChallanAmt) as total_amt');
			$this->db->join(db_prefix() . 'salesmaster', db_prefix() . 'salesmaster.SalesID = ' . db_prefix() . 'history.TransID AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TType','O');
			$this->db->where('tblhistory.TType2','Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			
			if(!empty($TradeType)) $this->db->where('tblclients.Trade_Type', $TradeType);
			if(!empty($AccountID)) $this->db->where('tblhistory.AccountID', $AccountID);
			if(!empty($MainItemGroup)) $this->db->where('tblitems.MainGrpID', $MainItemGroup);
			if(!empty($SubGroup1)) $this->db->where('tblitems.SubGrpID1', $SubGroup1);
			if(!empty($SubGroup2)) $this->db->where('tblitems.SubGrpID2', $SubGroup2);
			if(!empty($ItemID)) $this->db->where('tblhistory.ItemID', $ItemID);
			if(!empty($Station)) $this->db->where('tblclients.StationName', $Station);
			if(!empty($City)) $this->db->where('tblclients.city', $City);
			
			$this->db->group_by("YEAR(tblhistory.TransDate), MONTH(tblhistory.TransDate), tblsalesmaster.BT");
			$Transaction = $this->db->get('tblhistory')->result_array();
			
			// Prepare output for line chart
			$Taxable = array_fill(0, count($Months), 0);
			$NonTaxable = array_fill(0, count($Months), 0);
			
			foreach ($Transaction as $row) {
				$month = $row['month'];
				$amt   = (float)$row['total_amt'];
				$bt    = $row['BT']; // T or B
				$index = array_search($month, $Months);
				
				if ($index !== false) {
					if ($bt == 'T') {
						$Taxable[$index] = $amt;
						} elseif ($bt == 'B') {
						$NonTaxable[$index] = $amt;
					}
				}
			}
			
			$ReturnData = [
			'Months' => $Months,
			'Sales' => [
            ['name' => 'Taxable', 'data' => $Taxable],
            ['name' => 'Non-Taxable', 'data' => $NonTaxable]
			]
			];
			
			return $ReturnData;
		}
		
		public function GetTopCustomerReturnRate($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			// ---------- 1. Get Total Sales ----------
			$this->db->select('tblhistory.AccountID, SUM(NetChallanAmt) as sale_amt, tblclients.company');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');
			$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType','O'); // Sale
			$this->db->where('tblhistory.TType2','Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			
			if(!empty($TradeType)) $this->db->where('tblclients.Trade_Type', $TradeType);
			if(!empty($AccountID)) $this->db->where('tblhistory.AccountID', $AccountID);
			if(!empty($MainItemGroup)) $this->db->where('tblitems.MainGrpID', $MainItemGroup);
			if(!empty($SubGroup1)) $this->db->where('tblitems.SubGrpID1', $SubGroup1);
			if(!empty($SubGroup2)) $this->db->where('tblitems.SubGrpID2', $SubGroup2);
			if(!empty($ItemID)) $this->db->where('tblhistory.ItemID', $ItemID);
			if(!empty($Station)) $this->db->where('tblclients.StationName', $Station);
			if(!empty($City)) $this->db->where('tblclients.city', $City);
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			$this->db->group_by('tblhistory.AccountID');
			$SalesData = $this->db->get('tblhistory')->result_array();
			
			// ---------- 2. Get Total Returns ----------
			$this->db->select('tblhistory.AccountID, SUM(NetChallanAmt) as return_amt');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');
			$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType','R'); // Return
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($TradeType)) $this->db->where('tblclients.Trade_Type', $TradeType);
			if(!empty($AccountID)) $this->db->where('tblhistory.AccountID', $AccountID);
			if(!empty($MainItemGroup)) $this->db->where('tblitems.MainGrpID', $MainItemGroup);
			if(!empty($SubGroup1)) $this->db->where('tblitems.SubGrpID1', $SubGroup1);
			if(!empty($SubGroup2)) $this->db->where('tblitems.SubGrpID2', $SubGroup2);
			if(!empty($ItemID)) $this->db->where('tblhistory.ItemID', $ItemID);
			if(!empty($Station)) $this->db->where('tblclients.StationName', $Station);
			if(!empty($City)) $this->db->where('tblclients.city', $City);
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			$this->db->group_by('tblhistory.AccountID');
			$ReturnData = $this->db->get('tblhistory')->result_array();
			
			// ---------- 3. Merge ----------
			$returns = [];
			foreach ($ReturnData as $row) {
				$returns[$row['AccountID']] = (float)$row['return_amt'];
			}
			
			$Report = [];
			foreach ($SalesData as $row) {
				$AccountID = $row['AccountID'];
				$SaleAmt   = (float)$row['sale_amt'];
				$ReturnAmt = isset($returns[$AccountID]) ? $returns[$AccountID] : 0;
				
				if ($SaleAmt > 0) {
					$ReturnRate = ($ReturnAmt / $SaleAmt) * 100;
					} else {
					$ReturnRate = 0;
				}
				
				$Report[] = [
				'company' => $row['company'],
				'sale'    => $SaleAmt,
				'return'  => $ReturnAmt,
				'rate'    => $ReturnRate
				];
			}
			
			// ---------- 4. Sort by Return Rate ----------
			usort($Report, function($a, $b) {
				return $b['rate'] <=> $a['rate'];
			});
			
			// ---------- 5. Limit Top 10 ----------
			$Report = array_slice($Report, 0, 10);
			
			// ---------- 6. Prepare Chart Data ----------
			$chart = [];
			foreach ($Report as $r) {
				$chart[] = [
				'name' => $r['company'] . " (" . round($r['rate'],2) . "%)", 
				'y'    => (float)$r['return'],  // Return Amount for chart size
				'sale' => $r['sale'],
				'return'=> $r['return'],
				'rate' => round($r['rate'],2)
				];
			}
			
			return $chart;
		}
		public function GetTopReturnRateByItemGroup($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$TradeType = $filterdata["TradeType"];
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$Station = $filterdata["Station"];
			$City = $filterdata["City"];
			
			// ---------- 1. Get Total Sales ----------
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblitems_sub_groups.id as ItemID,tblitems_sub_groups.name as ItemName,SUM(NetChallanAmt) as sale_amt');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,SUM(NetChallanAmt) as sale_amt');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(NetChallanAmt) as sale_amt');
			}
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			
			$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID','INNER');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType','O'); // Sale
			$this->db->where('tblhistory.TType2','Order');
			$this->db->where('tblhistory.TransID IS NOT NULL');
			
			if(!empty($TradeType)) $this->db->where('tblclients.Trade_Type', $TradeType);
			if(!empty($AccountID)) $this->db->where('tblhistory.AccountID', $AccountID);
			if(!empty($MainItemGroup)) $this->db->where('tblitems.MainGrpID', $MainItemGroup);
			if(!empty($SubGroup1)) $this->db->where('tblitems.SubGrpID1', $SubGroup1);
			if(!empty($SubGroup2)) $this->db->where('tblitems.SubGrpID2', $SubGroup2);
			if(!empty($ItemID)) $this->db->where('tblhistory.ItemID', $ItemID);
			if(!empty($Station)) $this->db->where('tblclients.StationName', $Station);
			if(!empty($City)) $this->db->where('tblclients.city', $City);
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
				}else{
				$this->db->group_by(db_prefix() . 'items.item_code');
			}
			$SalesData = $this->db->get('tblhistory')->result_array();
			
			// ---------- 2. Get Total Returns ----------
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblitems_sub_groups.id as ItemID,tblitems_sub_groups.name as ItemName,SUM(NetChallanAmt) as return_amt');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,SUM(NetChallanAmt) as return_amt');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(NetChallanAmt) as return_amt');
			}
			
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');
			$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID','INNER');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType','R'); // Return
			$this->db->where('tblhistory.TransID IS NOT NULL');
			if(!empty($TradeType)) $this->db->where('tblclients.Trade_Type', $TradeType);
			if(!empty($AccountID)) $this->db->where('tblhistory.AccountID', $AccountID);
			if(!empty($MainItemGroup)) $this->db->where('tblitems.MainGrpID', $MainItemGroup);
			if(!empty($SubGroup1)) $this->db->where('tblitems.SubGrpID1', $SubGroup1);
			if(!empty($SubGroup2)) $this->db->where('tblitems.SubGrpID2', $SubGroup2);
			if(!empty($ItemID)) $this->db->where('tblhistory.ItemID', $ItemID);
			if(!empty($Station)) $this->db->where('tblclients.StationName', $Station);
			if(!empty($City)) $this->db->where('tblclients.city', $City);
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
				}else{
				$this->db->group_by(db_prefix() . 'items.item_code');
			}
			$ReturnData = $this->db->get('tblhistory')->result_array();
			
			// ---------- 3. Merge ----------
			$returns = [];
			foreach ($ReturnData as $row) {
				$returns[$row['ItemID']] = (float)$row['return_amt'];
			}
			
			$Report = [];
			foreach ($SalesData as $row) {
				$ItemID = $row['ItemID'];
				$SaleAmt   = (float)$row['sale_amt'];
				$ReturnAmt = isset($returns[$ItemID]) ? $returns[$ItemID] : 0;
				
				if ($SaleAmt > 0) {
					$ReturnRate = ($ReturnAmt / $SaleAmt) * 100;
					} else {
					$ReturnRate = 0;
				}
				
				$Report[] = [
				'ItemName' => $row['ItemName'],
				'sale'    => $SaleAmt,
				'return'  => $ReturnAmt,
				'rate'    => $ReturnRate
				];
			}
			
			// ---------- 4. Sort by Return Rate ----------
			usort($Report, function($a, $b) {
				return $b['rate'] <=> $a['rate'];
			});
			
			// ---------- 5. Limit Top 10 ----------
			$Report = array_slice($Report, 0, 10);
			
			// ---------- 6. Prepare Chart Data ----------
			$chart = [];
			foreach ($Report as $r) {
				if($r['rate'] > 0){
					$chart[] = [
					'name' => $r['ItemName'] . " (" . round($r['rate'],2) . "%)", 
					'y'    => (float)$r['return'],  // Return Amount for chart size
					'sale' => $r['sale'],
					'return'=> $r['return'],
					'rate' => round($r['rate'],2)
					];
				}
			}
			
			return $chart;
		}
		
		
	}
