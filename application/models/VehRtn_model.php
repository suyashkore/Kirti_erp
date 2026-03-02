<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class VehRtn_model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		public function get_company_detail()
		{  
			
			$selected_company = $this->session->userdata('root_company');
			
			
			$sql ='SELECT '.db_prefix().'rootcompany.*
			FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
			$result = $this->db->query($sql)->row();
			
			return $result;
		}
		
		public function GetDriverList()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'staff.*');
			$this->db->where(db_prefix() . 'staff.SubActGroupID', '1000159');
			$this->db->order_by('firstname,lastname', 'ASC');
			return $this->db->get(db_prefix() . 'staff')->result_array();
		}
		public function getvehicle()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'vehicle.*');
			$this->db->where(db_prefix() . 'vehicle.PlantID', $selected_company);
			$this->db->order_by('VehicleID', 'ASC');
			return $this->db->get(db_prefix() . 'vehicle')->result_array();
		}
		// VehicleRtn List
		public function vehicle_return_table($data,$type=""){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$IsCrate = $data["IsCrate"];
			$IsPayment = $data["IsPayment"];
			$IsExpenses = $data["IsExpenses"];
			
			$regExp ="'.*;s:[0-9]+:'".$selected_company."'.*'";
			$regExp1 ="'.*;s:[0-9]+:";
			$regExp2 =".*'";
			
			$this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.IsCrateRcvd,tblvehiclereturn.IsRtnRcvd,tblvehiclereturn.IsPaymentRcvd,tblvehiclereturn.IsExpRcvd,tblvehiclereturn.Crates  as return_crates,tblvehiclereturn.Transdate as returnTransdate,tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln,(
			SELECT sm.StationName 
			FROM tblordermaster om
			INNER JOIN tblclients c ON c.AccountID = om.AccountID
			LEFT JOIN tblStationMaster sm ON sm.id = c.StationName
			WHERE om.ChallanID = tblvehiclereturn.ChallanID 
			AND om.PlantID = tblvehiclereturn.PlantID
			LIMIT 1
			) AS Station');
			$this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
            
            $this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID AND users_table_a.staff_comp REGEXP '.$regExp1.'"'.$selected_company.'"'.$regExp2.'', 'left');
            $this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID AND users_table_b.staff_comp REGEXP '.$regExp1.'"'.$selected_company.'"'.$regExp2.'', 'left');
            $this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID AND users_table_c.staff_comp REGEXP '.$regExp1.'"'.$selected_company.'"'.$regExp2.'', 'left');
            $this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
            $this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
            $this->db->where('tblvehiclereturn.Transdate  BETWEEN "'. $from_date. ' 00:00:00" and "'. $to_date.' 23:59:59"');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.FY', $year);
			
			if(!empty($IsCrate)){
				$this->db->where('tblvehiclereturn.IsCrateRcvd', $IsCrate);
			}
			if(!empty($type) && $type == "Crates"){
				$this->db->where('tblvehiclereturn.IsRtnRcvd', 'Y');
			}
			
			if(!empty($IsPayment)){
				$this->db->where('tblvehiclereturn.IsPaymentRcvd', $IsPayment);
			}
			if(!empty($type) && $type == "Payments"){
				$this->db->where('tblvehiclereturn.IsCrateRcvd', 'Y');
			}
			
			if(!empty($IsExpenses)){
				$this->db->where('tblvehiclereturn.IsExpRcvd', $IsExpenses);
			}
			if(!empty($type) && $type == "Expenses"){
				$this->db->where('tblvehiclereturn.IsPaymentRcvd', 'Y');
			}
            $this->db->order_by('tblvehiclereturn.ReturnID','desc');
            return $this->db->get('tblvehiclereturn')->result_array();
            // echo $this->db->last_query();die; 
		}
		
		// Challan List for Not crate vehicle RTN
		
		public function challan_model_table($data){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$challan_route = $data["challan_route"];
			
			$challanIDS = array();
			$this->db->select('*');
			$this->db->where('tblvehiclereturn.PlantID', $selected_company);
			$this->db->where('tblvehiclereturn.FY', $year);
			$vehRtnChallanID = $this->db->get('tblvehiclereturn')->result_array();
            foreach ($vehRtnChallanID as $key => $value) {
				array_push($challanIDS, $value["ChallanID"]);
			}
			if(empty($challanIDS)){
				//return null;
				$this->db->select('tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln,(
				SELECT sm.StationName 
				FROM tblordermaster om
				INNER JOIN tblclients c ON c.AccountID = om.AccountID
				LEFT JOIN tblStationMaster sm ON sm.id = c.StationName
				WHERE om.ChallanID = tblchallanmaster.ChallanID 
				AND om.PlantID = tblchallanmaster.PlantID
				LIMIT 1
				) AS Station');
				$this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
				$this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
				$this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
				$this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
				$this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
				//$this->db->where_not_in('tblchallanmaster.ChallanID', $challanIDS);
				$this->db->where('tblchallanmaster.Transdate  BETWEEN "'. $from_date. ' 00:00:00" and "'. $to_date.' 23:59:59"');
				$this->db->where('tblchallanmaster.PlantID', $selected_company);
				$this->db->where('tblchallanmaster.FY', $year);
				if($challan_route != ''){
					$this->db->where('tblchallanmaster.RouteID', $challan_route);
				}
				$this->db->group_by('tblchallanmaster.ChallanID');
				$this->db->order_by('tblchallanmaster.ChallanID','desc');
				return $this->db->get('tblchallanmaster')->result_array();
				}else{
				$this->db->select('tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln,(
				SELECT sm.StationName 
				FROM tblordermaster om
				INNER JOIN tblclients c ON c.AccountID = om.AccountID
				LEFT JOIN tblStationMaster sm ON sm.id = c.StationName
				WHERE om.ChallanID = tblchallanmaster.ChallanID 
				AND om.PlantID = tblchallanmaster.PlantID
				LIMIT 1
				) AS Station');
				$this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
				$this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
				$this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
				$this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
				$this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
				$this->db->where_not_in('tblchallanmaster.ChallanID', $challanIDS);
				$this->db->where('tblchallanmaster.Transdate  BETWEEN "'. $from_date. ' 00:00:00" and "'. $to_date.' 23:59:59"');
				$this->db->where('tblchallanmaster.PlantID', $selected_company);
				$this->db->where('tblchallanmaster.FY', $year);
				if($challan_route != ''){
					$this->db->where('tblchallanmaster.RouteID', $challan_route);
				}
				$this->db->where('tblchallanmaster.gatepasstime IS NOT NULL');
				$this->db->group_by('tblchallanmaster.ChallanID');
				$this->db->order_by('tblchallanmaster.ChallanID','desc');
				return $this->db->get('tblchallanmaster')->result_array();
			}
		}
		
		// Get Vehicle Detail;s
		
		public function GetDetails($VRtnID){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
            $this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.Crates as return_crates,tblvehiclereturn.Transdate as returnTransdate,tblvehiclereturn.Act_entry_datetime,tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name,tblroute.KM,tblvehicle.VehicleCapacity,tblvehicle.mileage, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln, users_table_d.firstname as UserID_fn, users_table_d.lastname AS UserID_ln');
            $this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
            $this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
            $this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
            $this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
            $this->db->join('tblstaff users_table_d', 'tblvehiclereturn.UserID = users_table_d.AccountID', 'left');
            $this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
            $this->db->join('tblvehicle ', 'tblchallanmaster.VehicleID = tblvehicle.VehicleID', 'left');
            $this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.ReturnID', $VRtnID);
            $this->db->where('tblvehiclereturn.FY', $year);
            $challanDetails =  $this->db->get('tblvehiclereturn')->row();
            $result = array();
            if($challanDetails){
                $result['ChallanDetails'] = $challanDetails;
                $result['CratesDetails'] = $this->GetCrateDetails($VRtnID);
                $result['SaleRtnDetails'] = $this->GetSaleRtnDetails($challanDetails->ChallanID);
                $result['PaymentsDetails'] = $this->GetPaymentsDetails($VRtnID);
                $result['ExpenseDetails'] = $this->GetExpenseDetails($VRtnID);
                $result['TransportDetails'] = $this->TransportEntry_Detail($VRtnID);
			}
            return $result;
		}
		
		public function GetExpenseDetails($VRtnID){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			
			$this->db->select('tblvehiclereturn.ReturnID,tblstaff.firstname,tblstaff.lastname,tblstaff.current_address,expense_d.Amount as expense_Amount,expense_d.AccountID as Aid,tblclients.company,tblclients.address');
			
			$this->db->join('tblaccountledger expense_d', 'tblvehiclereturn.ReturnID = expense_d.VoucherID AND expense_d.TType = "D" AND expense_d.PassedFrom = "VEHRTNEXP" AND expense_d.PlantID = '.$selected_company.' AND expense_d.FY = '.$year, 'left');
			$this->db->join('tblstaff ', 'expense_d.AccountID = tblstaff.AccountID ', 'left');
			$this->db->join('tblclients ', 'tblclients.AccountID = expense_d.AccountID AND tblclients.PlantID = '.$selected_company, 'left');   
			$this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
			$this->db->where('tblvehiclereturn.ReturnID', $VRtnID);
			$this->db->where('tblvehiclereturn.FY', $year);
			return $this->db->get('tblvehiclereturn')->result_array();
		}
		
		public function GetPaymentsDetails($VRtnID){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			
			$this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.Crates as return_crates,tblclients.company,tblclients.address,tblvehiclereturn.Transdate as returnTransdate,tblchallanmaster.*,payment_recipt.Amount as payment_recipt_Amount,payment_recipt.AccountID as Aid,COALESCE(tblDamageCurrency.Amount,0) AS payment_damage_Amount');
			$this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
			$this->db->join('tblDamageCurrency ', 'tblDamageCurrency.ChallanID = tblvehiclereturn.ChallanID  AND tblDamageCurrency.ReturnID = tblvehiclereturn.ReturnID', 'left');
			
			$this->db->join('tblaccountledger payment_recipt', 'tblvehiclereturn.ReturnID = payment_recipt.VoucherID AND payment_recipt.TType = "C"  AND payment_recipt.PassedFrom = "VEHRTNPYMTS" AND payment_recipt.PlantID = '.$selected_company.' AND payment_recipt.FY = '.$year, 'left');
			$this->db->join('tblclients ', 'payment_recipt.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
			$this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
			$this->db->where('tblvehiclereturn.ReturnID', $VRtnID);
			$this->db->where('tblvehiclereturn.FY', $year);
			return $this->db->get('tblvehiclereturn')->result_array();
		}
		public function GetSaleRtnDetails($ChallanID){
			$result = array();
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$item_unq = array();
			$response = array();
			
			$this->db->select('*');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $year);
			$this->db->where('tblhistory.BillID ', $ChallanID);
			$this->db->where('tblhistory.TType ', "O");
			$this->db->where('tblhistory.TType2 ', "Order");
			$itemlist_data = $this->db->get('tblhistory')->result_array();
			foreach ($itemlist_data as $key => $value) {
				if(!in_array($value["ItemID"], $item_unq)){
					array_push($item_unq, $value["ItemID"]);
				}
			}
			
			$this->db->select('tblordermaster.*,tblclients.company,tblclients.state');
			$this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblordermaster.PlantID = tblclients.PlantID ', 'left');
			$this->db->where('tblordermaster.PlantID LIKE', $selected_company);
			$this->db->where('tblordermaster.ChallanID', $ChallanID);
			$this->db->where('tblordermaster.FY', $year);
			$Orderdata = $this->db->get('tblordermaster')->result_array();
			
			$this->db->select('*');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $year);
			$this->db->where('tblhistory.BillID ', $ChallanID);
			$this->db->where('tblhistory.TType ', "O");
			$this->db->where('tblhistory.TType2 ', "Order");
			$ItemOrderData = $this->db->get('tblhistory')->result_array();
			
			
			$this->db->select('BilledQty,ChallanAmt,NetChallanAmt,igstamt,cgstamt,sgstamt,TransID,ItemID');
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.FY', $year);
			$this->db->where('tblhistory.BillID ', $ChallanID);
			$this->db->where('tblhistory.TType ', "R");
			$this->db->where('tblhistory.TType2 ', "Fresh");
			$ItemRtnData = $this->db->get('tblhistory')->result_array();
			
			$response["itemhead"] = $item_unq;
			$response["Orderdata"] = $Orderdata;
			$response["ItemOrderData"] = $ItemOrderData;
			$response["ItemRtnData"] = $ItemRtnData;
			return $response;
		}
		
		public function GetCrateDetails($id){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.Transdate AS VTransDate,tblvehiclereturn.Crates,tblvehiclereturn.ChallanID');
			$this->db->where('tblvehiclereturn.PlantID ', $selected_company);
			$this->db->where('tblvehiclereturn.ReturnID', $id);
			$this->db->where('tblvehiclereturn.FY', $year);
			$data = $this->db->get('tblvehiclereturn')->row();
			$challanID = $data->ChallanID;
			$firstDate = '20'.$year.'-04-01';
			$TransDate = substr($data->VTransDate,0,10);
			$TransDate2 = substr($data->VTransDate, 0, 19); // Original date string
			$oneSecondBefore = date('Y-m-d H:i:s', strtotime($TransDate2) - 1); // Subtract 1 second
			
			$this->db->select('SUM(tblaccountcrates.Qty) AS VRtnCrates,tblaccountcrates.AccountID AS act_id,tblclients.company,tblclients.address');
			
			$this->db->join('tblclients ', 'tblaccountcrates.AccountID = tblclients.AccountID AND tblclients.PlantID = tblaccountcrates.PlantID');
			$this->db->where('tblaccountcrates.PlantID', $selected_company);
			$this->db->where('tblaccountcrates.VoucherID', $id);
			$this->db->where('tblaccountcrates.PassedFrom', 'VEHRTNCRATES');
			$this->db->where('tblaccountcrates.TType', 'C');
			$this->db->where('tblaccountcrates.FY', $year);
			$this->db->group_by('tblaccountcrates.AccountID');
			$VRtnCrates = $this->db->get('tblaccountcrates')->result_array();
			
			if(empty($VRtnCrates)){
				$this->db->select('COALESCE("") as VRtnCrates,tblordermaster.AccountID as act_id,tblclients.company,tblclients.address');
				$this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = tblordermaster.PlantID');
				$this->db->where('tblordermaster.PlantID', $selected_company);
				$this->db->where('tblordermaster.FY', $year);
				$this->db->where('tblordermaster.ChallanID', $challanID);
				$this->db->group_by('tblordermaster.AccountID');
				$VRtnCrates = $this->db->get('tblordermaster')->result_array();
			}
			
			$AccountIDs = array();
			foreach($VRtnCrates as $value_data){
				array_push($AccountIDs, $value_data["act_id"]);
			}
			
			// For Challan Crates
			$this->db->select('sum(Crates) as CHLCrates,AccountID');
			$this->db->where('tblordermaster.PlantID', $selected_company);
			$this->db->where('tblordermaster.FY', $year);
			$this->db->where('tblordermaster.ChallanID', $challanID);
			$this->db->group_by('AccountID');
			$ChlCrates = $this->db->get('tblordermaster')->result_array();
			
			if($AccountIDs){
                // For Opening balance
				$this->db->select('sum(Qty) as CROQty,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where_in('tblaccountcrates.AccountID', $AccountIDs);
				$this->db->where('tblaccountcrates.PassedFrom =', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'C');
				$this->db->where('tblaccountcrates.Transdate  BETWEEN "'. $firstDate. ' 00:00:00" and "'. $TransDate.' 23:59:59"');
				$this->db->group_by('AccountID');
				$CROQty = $this->db->get('tblaccountcrates')->result_array();
				
				$this->db->select('sum(Qty) as DROQty,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where_in('tblaccountcrates.AccountID', $AccountIDs);
				$this->db->where('tblaccountcrates.PassedFrom =', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'D');
				$this->db->where('tblaccountcrates.Transdate  BETWEEN "'. $firstDate. ' 00:00:00" and "'. $TransDate.' 23:59:59"');
				$this->db->group_by('AccountID');
				$DROQty = $this->db->get('tblaccountcrates')->result_array();
				
				// For Credit / Debit Crates
				$this->db->select('sum(Qty) as CRQty,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where_in('tblaccountcrates.AccountID', $AccountIDs);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'C');
				$this->db->where('tblaccountcrates.Transdate  BETWEEN "'. $firstDate. ' 00:00:00" and "'. $TransDate.' 23:59:59"');
				$this->db->group_by('AccountID');
				$CRQty = $this->db->get('tblaccountcrates')->result_array();
				
				$this->db->select('sum(Qty) as DRQty,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where_in('tblaccountcrates.AccountID', $AccountIDs);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'D');
				$this->db->where('tblaccountcrates.Transdate  BETWEEN "'. $firstDate. ' 00:00:00" AND "'. $TransDate.' 23:59:59"');
				$this->db->group_by('AccountID');
				$DRQty = $this->db->get('tblaccountcrates')->result_array();
				
				// For Credit / Debit Crates
				$this->db->select('sum(Qty) as CRQty,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where_in('tblaccountcrates.AccountID', $AccountIDs);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'C');
				$this->db->where('tblaccountcrates.ChallanID !=', $challanID);
				$this->db->where('tblaccountcrates.Transdate  BETWEEN "'. $firstDate. ' 00:00:00" and "'. $oneSecondBefore.'"');
				$this->db->group_by('AccountID');
				$CRQtyP = $this->db->get('tblaccountcrates')->result_array();
				
				$this->db->select('sum(Qty) as DRQty,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where_in('tblaccountcrates.AccountID', $AccountIDs);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'D');
				$this->db->where('tblaccountcrates.ChallanID !=', $challanID);
				$this->db->where('tblaccountcrates.Transdate  BETWEEN "'. $firstDate. ' 00:00:00" AND "'. $oneSecondBefore.'"');
				$this->db->group_by('AccountID');
				$DRQtyP = $this->db->get('tblaccountcrates')->result_array();
			}
			
			
			$i = 0;
			foreach($VRtnCrates as $value_data){
				$ChlCR = '';
				$balance = 0;
				$OQTY = 0;
				$CD = 0;
				foreach($ChlCrates as $value5){
					if(strtoupper($value_data["act_id"])==strtoupper($value5["AccountID"])){
						$ChlCR = $value5["CHLCrates"];
					}
				}
				
				if($AccountIDs){
					foreach($CROQty as $value1){
						if(strtoupper($value_data["act_id"])==strtoupper($value1["AccountID"])){
							$CROQ = $value1["CROQty"];
						}
					}
					foreach($DROQty as $value2){
						if(strtoupper($value_data["act_id"])==strtoupper($value2["AccountID"])){
							$DROQ = $value2["DROQty"];
						}
					}
					foreach($CRQty as $value3){
						if(strtoupper($value_data["act_id"])==strtoupper($value3["AccountID"])){
							$CRQ = $value3["CRQty"];
						}
					}
					foreach($DRQty as $value4){
						if(strtoupper($value_data["act_id"])==strtoupper($value4["AccountID"])){
							$DRQ = $value4["DRQty"];
						}
					}
					foreach($CRQtyP as $value3){
						if(strtoupper($value_data["act_id"])==strtoupper($value3["AccountID"])){
							$CRQP = $value3["CRQty"];
						}
					}
					foreach($DRQtyP as $value4){
						if(strtoupper($value_data["act_id"])==strtoupper($value4["AccountID"])){
							$DRQP = $value4["DRQty"];
						}
					}
					}else{
					$CROQ = 0;
					$DROQ = 0;
					$CRQ = 0;
					$DRQ = 0;
					$CRQP = 0;
					$DRQP = 0;
				}
				
				
				$OQTY = $DROQ - $CROQ;
				$CD =   $DRQ - $CRQ;
				$CD2 =   $DRQP - $CRQP;
				$OQTY = $CD2 + $OQTY;
				$balance = $CD + $OQTY;
				
				$VRtnCrates[$i]['OQty'] = $OQTY;
				$VRtnCrates[$i]['Qty'] = $OQTY;
				$VRtnCrates[$i]['CHLCrates'] = $ChlCR;
				if($ChlCR == ""){
					$ChlCR = 0;
				}
				$newBal = $OQTY + $ChlCR - $value_data["VRtnCrates"];
				// $VRtnCrates[$i]['balance_crates'] = $balance;
				$VRtnCrates[$i]['balance_crates'] = $newBal;
				/*$VRtnCrates[$i]['DROQ'] = $DROQ;
					$VRtnCrates[$i]['CROQ'] = $CROQ;
					$VRtnCrates[$i]['DRQ'] = $DRQ;
				$VRtnCrates[$i]['CRQ'] = $CRQ;*/
				$i++;
			}
			
			return $VRtnCrates;
			
			
			/*
				$this->db->select('tblvehiclereturn.ReturnID,tblordermaster.AccountID as act_id,tblchallanmaster.PlantID,tblchallanmaster.ChallanID,tblchallanmaster.FY,SUM(tblordermaster.Crates) AS CHLCrates,tblordermaster.AccountID,tblordermaster.PlantID,tblordermaster.FY,tblvehiclereturn.Crates as return_crates,tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,opening_crates.Qty,opening_crates.TType');
				$this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
				
				$this->db->join('tblordermaster ', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
				//  $this->db->join('tblaccountcrates crate_data', 'tblvehiclereturn.ReturnID = crate_data.VoucherID AND crate_data.TType = "C"  AND crate_data.PassedFrom = "VehicleReturn" AND crate_data.PlantID = '.$selected_company.' AND crate_data.FY = '.$year, 'left');
				$this->db->join('tblaccountcrates opening_crates', 'opening_crates.PassedFrom = "OPENCRATES" AND tblordermaster.AccountID = opening_crates.AccountID AND opening_crates.PlantID = '.$selected_company.' AND opening_crates.FY = '.$year, 'left');
				
				$this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
				$this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
				$this->db->where('tblvehiclereturn.ReturnID', $id);
				$this->db->where('tblvehiclereturn.FY', $year);
				$this->db->group_by('tblordermaster.AccountID');
				$Accountdata = $this->db->get('tblvehiclereturn')->result_array();
				
				foreach($Accountdata as $key=>$value){
                foreach($VRtnCrates as $value_data){
				if($value['act_id'] ==$value_data['AccountID']){
				$Accountdata[$key]['VRtnCrates'] = $value_data['VRtnCrates'];
				}
                }
				}
				
				$i = 0;
				foreach($Accountdata as $value){
                
				$this->db->select('sum(Qty) as credit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'C');
				$this->db->group_by('AccountID');
				$credit_crate = $this->db->get('tblaccountcrates')->result_array();
				
				$this->db->select('sum(Qty) as debit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'D');
				$this->db->group_by('AccountID');
				$debit_crate = $this->db->get('tblaccountcrates')->result_array();
				
				$balance = $debit_crate[0]['debit_crate'] - $credit_crate[0]['credit_crate'];
				
				
				if($value['TType'] == 'D'){
				$Accountdata[$i]['balance_crates'] = $balance+$value['Qty'];
				}else{
				$Accountdata[$i]['balance_crates'] = $balance-$value['Qty'];
				}
				$i++; }
			return $Accountdata;*/
			
		}
		public function get_all_crate_vehicle_return($id){
			
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			
            // $this->db->select('tblvehiclereturn.ReturnID,tblordermaster.AccountID as act_id,tblchallanmaster.*,tblordermaster.*,tblvehiclereturn.Crates as return_crates,tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,crate_data.Qty as crate_data_qty,opening_crates.Qty,opening_crates.TType');
            $this->db->select('tblvehiclereturn.ReturnID,tblordermaster.AccountID as act_id,tblchallanmaster.*,tblordermaster.*,tblvehiclereturn.Crates as return_crates,tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,opening_crates.Qty,opening_crates.TType');
            $this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
			
            $this->db->join('tblordermaster ', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
            //  $this->db->join('tblaccountcrates crate_data', 'tblvehiclereturn.ReturnID = crate_data.VoucherID AND crate_data.TType = "C"  AND crate_data.PassedFrom = "VehicleReturn" AND crate_data.PlantID = '.$selected_company.' AND crate_data.FY = '.$year, 'left');
            $this->db->join('tblaccountcrates opening_crates', 'opening_crates.PassedFrom = "OPENCRATES" AND tblordermaster.AccountID = opening_crates.AccountID AND opening_crates.PlantID = '.$selected_company.' AND opening_crates.FY = '.$year, 'left');
			
            $this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.ReturnID', $id);
            $this->db->where('tblvehiclereturn.FY', $year);
            $data = $this->db->get('tblvehiclereturn')->result_array();
            
            
            
            $this->db->select('crate_data.AccountID as act_id,crate_data.Qty as crate_data_qty,tblordermaster.OrderID AS ORD');
            $this->db->join('tblaccountcrates crate_data', 'tblvehiclereturn.ReturnID = crate_data.VoucherID AND crate_data.TType = "C"  AND crate_data.PassedFrom = "VEHRTNCRATES" AND crate_data.PlantID = '.$selected_company.' AND crate_data.FY = '.$year, 'left');
            $this->db->join('tblordermaster ', 'tblvehiclereturn.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.ReturnID', $id);
            $this->db->where('tblvehiclereturn.FY', $year);
            $data_next = $this->db->get('tblvehiclereturn')->result_array();
			
            foreach($data as $key=>$value){
                foreach($data_next as $value_data){
                    if($value['act_id'] ==$value_data['act_id'] && $value['OrderID'] == $value_data['ORD']){
						
                        $data[$key]['crate_data_qty'] = $value_data['crate_data_qty'];
					}
				}
			}
			$i = 0;
			foreach($data as $value){
                
				$this->db->select('sum(Qty) as credit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'C');
				$this->db->group_by('AccountID');
				$credit_crate = $this->db->get('tblaccountcrates')->result_array();
				
				$this->db->select('sum(Qty) as debit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.FY', $year);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'D');
				$this->db->group_by('AccountID');
				$debit_crate = $this->db->get('tblaccountcrates')->result_array();
				
				$balance = $debit_crate[0]['debit_crate'] - $credit_crate[0]['credit_crate'];
				
				
				if($value['TType'] == 'D'){
					$data[$i]['balance_crates'] = $balance+$value['Qty'];
					}else{
					$data[$i]['balance_crates'] = $balance-$value['Qty'];
				}
			$i++; }
            return $data;
		}
		// Get Account List For Crates
		function GetAccountlistForCrates($postData){
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$where_clients = '';
			
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				$this->db->select(db_prefix() . 'clients.*');
				$where_clients .= '(company LIKE "%' . $q . '%" ESCAPE \'!\' OR StationName LIKE "%' . $q . '%" ESCAPE \'!\' OR tblclients.AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR address LIKE "%' . $q. '%" ESCAPE \'!\' OR Address3 LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'clients.active = 1 AND ' . db_prefix() . 'clients.SubActGroupID1 = 100056';
				$this->db->where($where_clients);
				$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
				$records = $this->db->get(db_prefix() . 'clients')->result();
				
				foreach($records as $row ){
					$response[] = array("label"=>$row->company,"value"=>$row->AccountID,"address"=>$row->address);
				}
			}
			return $response;
		}
		
		// Get Account List For Crates
		function GetAccountDetailsForCrates($postData){
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$where_clients = '';
			
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				$this->db->select(db_prefix() . 'clients.*');
				$where_clients .= '(company LIKE "%' . $q . '%" ESCAPE \'!\' OR StationName LIKE "%' . $q . '%" ESCAPE \'!\' OR tblclients.AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR address LIKE "%' . $q. '%" ESCAPE \'!\' OR Address3 LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'clients.active = 1 AND ' . db_prefix() . 'clients.SubActGroupID = 60001004';
				$this->db->where($where_clients);
				$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
				$records = $this->db->get(db_prefix() . 'clients')->result();
				
				foreach($records as $row ){
					$response[] = array("label"=>$row->company,"value"=>$row->AccountID,"address"=>$row->address);
				}
			}
			return $response;
		}
		
		// Get Account List For Expenses
		function GetAccountlistForExpenses($postData){
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$year = $this->session->userdata('finacial_year');
			$where_clients = '';
			
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				$this->db->select(db_prefix() . 'clients.*');
				$where_clients .= '(company LIKE "%' . $q . '%" ESCAPE \'!\' OR StationName LIKE "%' . $q . '%" ESCAPE \'!\' OR tblclients.AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR address LIKE "%' . $q. '%" ESCAPE \'!\' OR Address3 LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'clients.active = 1 AND ' . db_prefix() . 'clients.SubActGroupID IN("30000001","30000005")';
				$this->db->where($where_clients);
				$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
				$records = $this->db->get(db_prefix() . 'clients')->result();
				
				foreach($records as $row ){
					$response[] = array("label"=>$row->company,"value"=>$row->AccountID,"address"=>$row->address);
				}
				$where_clients = '';   
				$this->db->select(db_prefix() . 'staff.*');
				$where_clients .= '(AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR lastname LIKE "%' . $q . '%" ESCAPE \'!\' ) ';
				$this->db->where($where_clients);
				$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
				$records1 = $this->db->get(db_prefix() . 'staff')->result();
				foreach($records1 as $row ){
					$fullname = $row->firstname." ".$row->lastname;
					$response[] = array("label"=>$fullname,"value"=>$row->AccountID,"address"=>$row->current_address);
				}
			}
			return $response;
		}
		public function getCustomerList()
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$SubgroupIDS = array("1000012");
			
			$this->db->select(db_prefix() . 'clients.*');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'clients.SubActGroupID', $SubgroupIDS);
			$result =  $this->db->get(db_prefix() . 'clients')->result_array();
			
			return $result;
			
			
		}
		// Get AccountDetails for Expenses
		public function getAccountDetailsForExpenses($postData)
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$AccountID = $postData['AccountID'];
			$SubgroupIDS = array("30000001","30000005");
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			
			$this->db->select(db_prefix() . 'clients.*');
			$this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			//$this->db->where_in(db_prefix() . 'clients.SubActGroupID', $SubgroupIDS);
			$result =  $this->db->get(db_prefix() . 'clients')->row();
			if($result){
				return $result;
				}else{
				$this->db->select(db_prefix() . 'staff.*');
				$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
				$this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
				$result =  $this->db->get(db_prefix() . 'staff')->row();
				return $result;
			}
			
		}
		
		// Get Account Details For Crates
		public function getAccountDetails($postData)
		{
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$AccountID = $postData['AccountID'];
			$ChallanID = $postData['ChallanID'];
			// AccountDetails
            $this->db->select('tblclients.*,SUM(tblordermaster.Crates) AS CHLCrates');
            $this->db->join('tblordermaster ', 'tblordermaster.AccountID = tblclients.AccountID AND tblordermaster.PlantID = tblclients.PlantID AND tblordermaster.ChallanID = "'.$ChallanID.'" AND tblordermaster.FY = '.$year, 'left');
            $this->db->where('tblclients.PlantID', $selected_company);
            $this->db->where('tblclients.AccountID', $AccountID);
            $AccountDetails = $this->db->get('tblclients')->row();
            
            $this->db->select('sum(Qty) as credit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $AccountID);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType', 'C');
            $this->db->where('tblaccountcrates.FY', $year);
            $this->db->group_by('AccountID');
            $credit_crate = $this->db->get('tblaccountcrates')->row();
			
            $this->db->select('sum(Qty) as debit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $AccountID);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'D');
            $this->db->where('tblaccountcrates.FY', $year);
            $this->db->group_by('AccountID');
            $debit_crate = $this->db->get('tblaccountcrates')->row();
            
            $this->db->select('sum(Qty) as Qty,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $AccountID);
            $this->db->where('tblaccountcrates.PassedFrom =', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'D');
            $this->db->where('tblaccountcrates.FY', $year);
            $this->db->group_by('AccountID');
            $debitOQty = $this->db->get('tblaccountcrates')->row();
            
            $this->db->select('sum(Qty) as Qty,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $AccountID);
            $this->db->where('tblaccountcrates.PassedFrom =', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'C');
            $this->db->where('tblaccountcrates.FY', $year);
            $this->db->group_by('AccountID');
            $CreditOQty = $this->db->get('tblaccountcrates')->row();
            
            $OQty = $debitOQty->Qty - $CreditOQty->Qty;
            $balance = $OQty + ($debit_crate->debit_crate - $credit_crate->credit_crate);
            $result = array();
            
            $result["Address"] = $AccountDetails->address;
            $result["company"] = $AccountDetails->company;
            $result["AccountID"] = $AccountDetails->AccountID;
            $result["OQty"] = $balance;
            $result["BQty"] = $balance;
            $result["CHLCrates"] = $AccountDetails->CHLCrates;
			return $result;
		}
		
		function staffgetaccounts($postData){
			
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$year = $this->session->userdata('finacial_year');
			$where_clients = '';
			
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				$this->db->select(db_prefix() . 'staff.*');
				$where_clients .= '(AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR lastname LIKE "%' . $q . '%" ESCAPE \'!\' ) ';
				$this->db->where($where_clients);
				$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
				$records = $this->db->get(db_prefix() . 'staff')->result();
				foreach($records as $row ){
					$fullname = $row->firstname." ".$row->lastname;
					$response[] = array("label"=>$fullname,"value"=>$row->AccountID,"address"=>$row->current_address);
				}
			}
			return $response;
		}
		
		public function get_staffAccount_Details($postData)
		{
			$selected_company = $this->session->userdata('root_company');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$year = $this->session->userdata('finacial_year');
			$AccountID = $postData['AccountID'];
			$this->db->select(db_prefix() . 'staff.*');
			$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			$this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
			$result =  $this->db->get(db_prefix() . 'staff')->row();
			return $result;
		}
		
		public function challan_unique_data($data){
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			
            $this->db->select('tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name,tblroute.KM,tblvehicle.VehicleCapacity,tblvehicle.mileage, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln');
            $this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
            $this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
            $this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
            $this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
            $this->db->join('tblvehicle ', 'tblchallanmaster.VehicleID = tblvehicle.VehicleID', 'left');
            $this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
            $this->db->where('tblchallanmaster.PlantID LIKE', $selected_company);
            $this->db->where('tblchallanmaster.ChallanID', $data['challan_id']);
            $this->db->where('tblchallanmaster.FY', $year);
            $this->db->group_by('tblchallanmaster.ChallanID');
            return $this->db->get('tblchallanmaster')->row_array();
		}
		
		
		
		
		public function challan_all_data($data){
			
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$year = $this->session->userdata('finacial_year');
			$ChallanID = $data['challan_id'];
			// for sale return
			$this->db->select('tblchallanmaster.*,tblordermaster.*,tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,tblclients.state');
			$this->db->join('tblordermaster ', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
			$this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
			$this->db->where('tblchallanmaster.PlantID LIKE', $selected_company);
			$this->db->where('tblchallanmaster.ChallanID', $ChallanID);
			$this->db->where('tblchallanmaster.FY', $year);
			$data = $this->db->get('tblchallanmaster')->result_array();
            
			$i = 0;
			$item_unq = array(); 
			foreach($data as $value){
				// item list using challan id
				
				$this->db->select('*');
				$this->db->where('tblhistory.PlantID', $selected_company);
				$this->db->where('tblhistory.FY', $year);
				$this->db->where('tblhistory.AccountID', $value['AccountID']);
				$this->db->where('tblhistory.BillID ', $value['ChallanID']);
				$this->db->where('tblhistory.TType ', "O");
				$this->db->where('tblhistory.TType2 ', "Order");
				$itemlist_data = $this->db->get('tblhistory')->result_array();
				$item_list_ary = array();
				
				foreach ($itemlist_data as $key => $value) {
					# code...
					array_push($item_list_ary, $value["ItemID"]);
                    if(!in_array($value["ItemID"], $item_unq)){
						array_push($item_unq, $value["ItemID"]);
					}
				}
				$data[$i]['itemdetails'] = $itemlist_data;
				$i++; 
			}
			$response["data"] = $data;
			$response["itemhead"] = $item_unq;
			
			// For Payment and crates
			$this->db->select('tblchallanmaster.*,tblordermaster.*,SUM(tblordermaster.Crates) as crates_data,tblclients.company,tblclients.address,tblclients.state,ShipTo.company as ShipTocompany,tblclients.address as ShipToaddress,tblclients.state as ShipTostate,tblaccountcrates.Qty,tblaccountcrates.TType');
			$this->db->join('tblordermaster ', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
			$this->db->join('tblaccountcrates ', 'tblaccountcrates.PassedFrom = "OPENCRATES" AND tblordermaster.AccountID = tblaccountcrates.AccountID AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.FY = '.$year, 'left');
			$this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
			$this->db->join('tblclients AS ShipTo', 'tblordermaster.AccountID2 = ShipTo.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
			$this->db->where('tblchallanmaster.PlantID LIKE', $selected_company);
			$this->db->where('tblchallanmaster.ChallanID', $ChallanID);
			$this->db->where('tblchallanmaster.FY', $year);
			$this->db->group_by('tblordermaster.AccountID');
			$cratesandpayments = $this->db->get('tblchallanmaster')->result_array();
			
			$j = 0;
			foreach($cratesandpayments as $value){
				// credit crated
				$this->db->select('sum(Qty) as credit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID2']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'C');
				$this->db->group_by('AccountID');
				$credit_crate = $this->db->get('tblaccountcrates')->result_array();
                
                // debit crated
				$this->db->select('sum(Qty) as debit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID2']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'D');
				$this->db->group_by('AccountID');
				$debit_crate = $this->db->get('tblaccountcrates')->result_array();
				
				// credit crated
				$this->db->select('sum(Qty) as credit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID2']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.TType LIKE', 'C');
				$this->db->where('tblaccountcrates.ChallanID !=', $ChallanID);
				$this->db->group_by('AccountID');
				$credit_crate2 = $this->db->get('tblaccountcrates')->result_array();
                
                // debit crated
				$this->db->select('sum(Qty) as debit_crate,AccountID');
				$this->db->where('tblaccountcrates.PlantID', $selected_company);
				$this->db->where('tblaccountcrates.AccountID', $value['AccountID2']);
				$this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
				$this->db->where('tblaccountcrates.ChallanID !=', $ChallanID);
				$this->db->where('tblaccountcrates.TType LIKE', 'D');
				$this->db->group_by('AccountID');
				$debit_crate2 = $this->db->get('tblaccountcrates')->result_array();
				
				
				
                // balance crates
				$balance = $debit_crate[0]['debit_crate'] - $credit_crate[0]['credit_crate'];
				
				$OQty = $value['Qty'] + ($debit_crate2[0]['debit_crate'] - $credit_crate2[0]['credit_crate']);
				$cratesandpayments[$j]['Qty'] = $OQty;
				
				if($value['TType'] == 'D'){
					$cratesandpayments[$j]['balance_crates'] = $balance+$value['Qty'];
					$cratesandpayments[$j]['balance_crates_org'] = $balance+$value['Qty'];
					}else{
					$cratesandpayments[$j]['balance_crates'] = $balance-$value['Qty'];
					$cratesandpayments[$j]['balance_crates_org'] = $balance-$value['Qty'];
				}
				$j++;
			}
			$response["cratesandpayments"] = $cratesandpayments;
			return $response;
		}
		
		public function increment_next_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year'); 
			$selected_company = $this->session->userdata('root_company');
            if($selected_company == 1){
                $this->db->where('name', 'next_vehicle_return_number_for_cspl');
                
				}elseif($selected_company == 2){
                $this->db->where('name', 'next_vehicle_return_number_for_cff');
				
				}elseif($selected_company == 3){
                $this->db->where('name', 'next_vehicle_return_number_for_cbu');
                
			}
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		public function increment_next_number_vehicle()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year'); 
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('name', 'next_vrt_number');
            
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		public function get_stock_item($id)
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
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $FY);
			$this->db->where('ItemID', $id);
			$this->db->where('GodownID',$GodownID);
			return $this->db->get(db_prefix() . 'stockmaster')->row();
		}
		public function get_acc_bal($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('AccountID', $id);
			return $this->db->get(db_prefix() . 'accountbalances')->row();
		}
		
		public function GetPreLedger($VoucheID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $VoucheID);
			return $this->db->get(db_prefix() . 'accountledger')->result_array();
		}
		public function GetPreLedgerExpense($VoucheID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $VoucheID);
			$this->db->where('PassedFrom', 'VEHRTNEXP');
			return $this->db->get(db_prefix() . 'accountledger')->result_array();
		}
		public function GetPreLedgerPaymentReceipt($VoucheID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $VoucheID);
			$this->db->where('PassedFrom', 'VEHRTNPYMTS');
			return $this->db->get(db_prefix() . 'accountledger')->result_array();
		}
		
		public function GetVRtnDetails($VRtnID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('ReturnID', $VRtnID);
			return $this->db->get(db_prefix() . 'vehiclereturn')->row();
		}
		
		public function CheckVehRtnForChallan($ChallanID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('ChallanID', $ChallanID);
			return $this->db->get(db_prefix() . 'vehiclereturn')->row();
		}
		
		public function GetSaleRtn($VRtnID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('OrderID', $VRtnID);
			return $this->db->get(db_prefix() . 'history')->result_array();
		}
		
		//Function for fetching previous account crates details
		public function GetPreviousAccountCratesDetails($passedFrom, $VRtnID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('PassedFrom', $passedFrom);
			$this->db->where('VoucherID', $VRtnID);
			return $this->db->get(db_prefix() . 'accountcrates')->result_array();
		}
		
		public function load_data($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$status = $data["status"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'vehiclereturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'" AND tblchallanmaster.gatepasstime IS NOT NULL';
			
			if(!empty($status)){
				$sql1 .= ' AND '.db_prefix().'vehiclereturn.status = "'.$status.'" ';
			}
			$sql1 .= ' ORDER BY ChallanID ASC';
			
			$sql ='SELECT '.db_prefix().'vehiclereturn.*,tblchallanmaster.Transdate AS Challandate,tblchallanmaster.VehicleID,tblroute.name as routename,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS Driver FROM '.db_prefix().'vehiclereturn
			INNER JOIN tblchallanmaster on tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID
			INNER JOIN tblroute on tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID =  tblchallanmaster.PlantID
			LEFT JOIN tblstaff on tblstaff.AccountID = tblchallanmaster.DriverID
			WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function GetMileageReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$vehicle = $data["vehicle"];
			$driver = $data["driver"];
			$Route = $data["Route"];
			$Station = $data["Station"];
			$fuel_type = $data["fuel_type"];
			$VehicleType = $data["VehicleType"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// Base condition
			$sql1 = '('.db_prefix().'vehiclereturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") 
			AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'"';
			
			if (!empty($vehicle)) {
				$sql1 .= ' AND '.db_prefix().'challanmaster.VehicleID = "'.$vehicle.'" ';
			}
			if (!empty($driver)) {
				$sql1 .= ' AND '.db_prefix().'challanmaster.DriverID = "'.$driver.'" ';
			}
			if (isset($Route) && $Route !== '') {
				$sql1 .= ' AND '.db_prefix().'challanmaster.RouteID = "'.$Route.'" ';
			}
			if (!empty($fuel_type)) {
				$sql1 .= ' AND '.db_prefix().'vehicle.fuel_type = "'.$fuel_type.'" ';
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$sql1 .= ' AND '.db_prefix().'vehicle.VehicleTypeID = "'.$VehicleType.'" ';
			}
			
			// Station filter via EXISTS (safer for multiple orders)
			if (!empty($Station)) {
				$sql1 .= ' AND EXISTS (
				SELECT 1 FROM tblordermaster om
				INNER JOIN tblclients c ON c.AccountID = om.AccountID
				WHERE om.ChallanID = tblvehiclereturn.ChallanID 
				AND om.PlantID = tblvehiclereturn.PlantID
				AND c.StationName = "'.$Station.'"
				)';
			}
			
			$sql = 'SELECT '.db_prefix().'vehiclereturn.*,
			tblchallanmaster.Transdate AS Challandate,
			tblchallanmaster.VehicleID,
			tblchallanmaster.gatepasstime,
			tblroute.name as routename,
			CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS Driver,
			tblvehicle.mileage,
			(
			SELECT COUNT(DISTINCT AccountID2) 
			FROM '.db_prefix().'ordermaster 
			WHERE '.db_prefix().'ordermaster.ChallanID = '.db_prefix().'vehiclereturn.ChallanID 
			AND '.db_prefix().'ordermaster.PlantID = '.db_prefix().'vehiclereturn.PlantID
			) as Total_Drop,
			(
			SELECT sm.StationName 
			FROM tblordermaster om
			INNER JOIN tblclients c ON c.AccountID = om.AccountID
			LEFT JOIN tblStationMaster sm ON sm.id = c.StationName
			WHERE om.ChallanID = tblvehiclereturn.ChallanID 
			AND om.PlantID = tblvehiclereturn.PlantID
			LIMIT 1
			) AS Station
            FROM '.db_prefix().'vehiclereturn
            INNER JOIN tblchallanmaster ON tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID
            INNER JOIN tblroute ON tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID
            INNER JOIN tblstaff ON tblstaff.AccountID = tblchallanmaster.DriverID
            LEFT JOIN tblvehicle ON tblvehicle.VehicleID = tblchallanmaster.VehicleID
            WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			
			if (!empty($data)) {
				foreach ($data as &$each) {
					$each['Detail'] = $this->TransportEntry_Detail($each['ReturnID']);
					// Station is already fetched in the main query as $each['Station']
				}
			}
			return $data;
		}
		public function GetVehicleLoadedReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$vehicle = $data["vehicle"];
			$Route = $data["Route"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'challanmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND tblchallanmaster.gatepasstime IS NOT NULL AND '.db_prefix().'challanmaster.PlantID="'.$selected_company.'"';
			if(!empty($vehicle)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.VehicleID = "'.$vehicle.'" ';
			}
			if (isset($Route) && $Route !== '') {
				$sql1 .= ' AND '.db_prefix().'challanmaster.RouteID = "'.$Route.'" ';
			}
			$sql ='SELECT '.db_prefix().'challanmaster.*,tblroute.name as routename,tblvehicle.VehicleCapacity
			FROM '.db_prefix().'challanmaster
			INNER JOIN tblroute on tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID =  tblchallanmaster.PlantID
			LEFT JOIN tblvehicle on tblvehicle.VehicleID = tblchallanmaster.VehicleID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			return $data;
		}
		public function GetMileageReportChart($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$vehicle = $data["vehicle"];
			$driver = $data["driver"];
			$fuel_type = $data["fuel_type"];
			$Route = $data["Route"];
			$VehicleType = $data["VehicleType"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'vehiclereturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'"';
			if(!empty($vehicle)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.VehicleID = "'.$vehicle.'" ';
			}
			if(!empty($driver)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.DriverID = "'.$driver.'" ';
			}
			
			if(!empty($fuel_type)){
				$sql1 .= ' AND '.db_prefix().'vehicle.fuel_type = "'.$fuel_type.'" ';
			}
			
			if (isset($Route) && $Route !== '') {
				$sql1 .= ' AND '.db_prefix().'challanmaster.RouteID = "'.$Route.'" ';
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$sql1 .= ' AND '.db_prefix().'vehicle.VehicleTypeID = "'.$VehicleType.'" ';
			}
			$sql1 .= ' GROUP BY tblchallanmaster.DriverID ORDER BY AvgMileage DESC';
			
			$sql ='SELECT tblchallanmaster.DriverID,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS Driver,SUM(tbltransport_entry.DistanceTravel) / NULLIF(SUM(tbltransport_entry.Diesel), 0) AS AvgMileage FROM `tblchallanmaster` 
			INNER JOIN tblvehiclereturn ON tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID 
			INNER JOIN tbltransport_entry ON tbltransport_entry.VehRtn_no = tblvehiclereturn.ReturnID 
			INNER JOIN tblstaff ON tblstaff.AccountID = tblchallanmaster.DriverID
			LEFT JOIN tblvehicle on tblvehicle.VehicleID = tblchallanmaster.VehicleID
			WHERE '.$sql1;
			
			$Challan = $this->db->query($sql)->result_array();
			
			return $Challan;
		}
		
		public function GetStandard_VS_ActualMileage($filterdata)
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
			$vehicle = $filterdata["vehicle"];
			$driver = $filterdata["driver"];
			$fuel_type = $filterdata["fuel_type"];
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			
			
			$Standard = [];
			$Actual = [];
			
			
			$this->db->select(db_prefix().'challanmaster.VehicleID,SUM(tbltransport_entry.DistanceTravel) / NULLIF(SUM(tbltransport_entry.Diesel), 0) AS AvgMileage,tblvehicle.mileage');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID');
			$this->db->join('tbltransport_entry', 'tbltransport_entry.VehRtn_no = tblvehiclereturn.ReturnID');
			$this->db->where('tblvehiclereturn.PlantID', $selected_company);
			$this->db->where("tblvehiclereturn.Transdate BETWEEN '$from_date' AND '$to_date'");
			
			if(!empty($vehicle)){ 
				$this->db->where('tblchallanmaster.VehicleID', $vehicle); 
			}
			if(!empty($driver)){ 
				$this->db->where('tblchallanmaster.DriverID', $driver);
			}
			if(!empty($fuel_type)){ 
				$this->db->where('tblvehicle.fuel_type', $fuel_type);
			}
			if (isset($Route) && $Route !== '') { 
				$this->db->where('tblchallanmaster.RouteID', $Route); 
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID', $VehicleType); 
			}
			$this->db->group_by('tblchallanmaster.VehicleID');
			$Challan = $this->db->get('tblchallanmaster')->result_array();
			// echo "<pre>";print_r($Challan);die;
			$i=0;
			foreach ($Challan as $key => $value) {
				array_push($Actual, [
				'name' 		=> $value['VehicleID'],
				'y' 		=>	(float)$value['AvgMileage'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				
				array_push($Standard, [
				'name' 		=> $value['VehicleID'],
				'y' 		=>	(float)$value['mileage'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				$i++;
			}
			
			
			$data = [
			'Standard' => $Standard,
			'Actual' => $Actual,
			];
			
			return $data;
		}
		public function Get_top_five_mileage_vehicle($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$chart = [];
			
			$this->db->select(db_prefix().'challanmaster.VehicleID, SUM(tbltransport_entry.DistanceTravel) / NULLIF(SUM(tbltransport_entry.Diesel), 0) AS AvgMileage, tblvehicle.mileage');
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID');
			$this->db->join('tbltransport_entry', 'tbltransport_entry.VehRtn_no = tblvehiclereturn.ReturnID');
			$this->db->where('tblvehiclereturn.PlantID', $selected_company);
			$this->db->where("tblvehiclereturn.Transdate BETWEEN '$from_date' AND '$to_date'");
			
			if (!empty($vehicle)) { 
				$this->db->where('tblchallanmaster.VehicleID', $vehicle); 
			}
			if (!empty($filterdata["driver"])) { 
				$this->db->where('tblchallanmaster.DriverID', $filterdata["driver"]);
			}
			if (!empty($fuel_type)) { 
				$this->db->where('tblvehicle.fuel_type', $fuel_type);
			}
			if (isset($Route) && $Route !== '') { 
				$this->db->where('tblchallanmaster.RouteID', $Route); 
			}
			if (isset($VehicleType) && $VehicleType !== '') {
				$this->db->where('tblvehicle.VehicleTypeID', $VehicleType); 
			}
			
			$this->db->group_by('tblchallanmaster.VehicleID');
			$Challan = $this->db->get('tblchallanmaster')->result_array();
			
			// Sort vehicles by AvgMileage in descending order
			usort($Challan, function($a, $b) {
				return (float)$b['AvgMileage'] <=> (float)$a['AvgMileage'];
			});
			
			// Get top 5 vehicles
			$topVehicles = array_slice($Challan, 0, 5);
			
			// Prepare chart data only for top 5 vehicles
			foreach ($topVehicles as $value) {
				$avgMileage = (float)$value['AvgMileage'];
				
				$chart[] = [
				'name'   => $value['VehicleID'], // You may replace this with vehicle name if available
				'y'      => $avgMileage,
				'z'      => 100,
				'label'  => "Avg Mileage"
				];
			}
			
			return $chart;
		}
		
		public function GetMileageGap($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			
			$vehicle = $filterdata["vehicle"];
			$fuel_type = $filterdata["fuel_type"];
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			
			
			$Standard = [];
			$Actual = [];
			
			
			$this->db->select('SUM(tbltransport_entry.DistanceTravel) / NULLIF(SUM(tbltransport_entry.Diesel), 0) AS AvgMileage,AVG(tblvehicle.mileage) As ExpectedAvg');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID');
			$this->db->join('tbltransport_entry', 'tbltransport_entry.VehRtn_no = tblvehiclereturn.ReturnID');
			$this->db->where('tblvehiclereturn.PlantID', $selected_company);
			$this->db->where("tblvehiclereturn.Transdate BETWEEN '$from_date' AND '$to_date'");
			
			if(!empty($vehicle)){ 
				$this->db->where('tblchallanmaster.VehicleID', $vehicle); 
			}
			if(!empty($fuel_type)){ 
				$this->db->where('tblvehicle.fuel_type', $fuel_type);
			}
			if (isset($Route) && $Route !== '') { 
				$this->db->where('tblchallanmaster.RouteID', $Route); 
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID', $VehicleType); 
			}
			$Record = $this->db->get('tblchallanmaster')->row();
			
			
			$data = [
			'MileageGap' => ($Record->ExpectedAvg - $Record->AvgMileage),
			];
			
			return $data;
		}
		public function GetDriverOutTimeReportChart($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$vehicle = $data["vehicle"];
			$driver = $data["driver"];
			$fuel_type = $data["fuel_type"];
			$Route = $data["Route"];
			$VehicleType = $data["VehicleType"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'vehiclereturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'"';
			if(!empty($vehicle)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.VehicleID = "'.$vehicle.'" ';
			}
			if(!empty($driver)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.DriverID = "'.$driver.'" ';
			}
			
			if(!empty($fuel_type)){
				$sql1 .= ' AND '.db_prefix().'vehicle.fuel_type = "'.$fuel_type.'" ';
			}
			
			if (isset($Route) && $Route !== '') {
				$sql1 .= ' AND '.db_prefix().'challanmaster.RouteID = "'.$Route.'" ';
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$sql1 .= ' AND '.db_prefix().'vehicle.VehicleTypeID = "'.$VehicleType.'" ';
			}
			$sql1 .= ' GROUP BY tblchallanmaster.DriverID ORDER BY TotalHour DESC';
			
			$sql ='SELECT tblchallanmaster.DriverID,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS Driver,SUM(TIMESTAMPDIFF(HOUR, tblchallanmaster.gatepasstime, tblvehiclereturn.Transdate)) AS TotalHour FROM `tblvehiclereturn`
			INNER JOIN tblchallanmaster ON tblchallanmaster.ChallanID  = tblvehiclereturn.ChallanID 
			INNER JOIN tblstaff ON tblstaff.AccountID = tblchallanmaster.DriverID
			LEFT JOIN tblvehicle on tblvehicle.VehicleID = tblchallanmaster.VehicleID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			
			return $data;
		}
		public function GetVehicleOutTimeReportChart($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$vehicle = $data["vehicle"];
			$driver = $data["driver"];
			$fuel_type = $data["fuel_type"];
			$Route = $data["Route"];
			$VehicleType = $data["VehicleType"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'vehiclereturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'"';
			if(!empty($vehicle)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.VehicleID = "'.$vehicle.'" ';
			}
			if(!empty($driver)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.DriverID = "'.$driver.'" ';
			}
			
			if(!empty($fuel_type)){
				$sql1 .= ' AND '.db_prefix().'vehicle.fuel_type = "'.$fuel_type.'" ';
			}
			
			if (isset($Route) && $Route !== '') {
				$sql1 .= ' AND '.db_prefix().'challanmaster.RouteID = "'.$Route.'" ';
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$sql1 .= ' AND '.db_prefix().'vehicle.VehicleTypeID = "'.$VehicleType.'" ';
			}
			$sql1 .= ' GROUP BY tblchallanmaster.VehicleID ORDER BY TotalHour DESC';
			
			$sql ='SELECT tblchallanmaster.VehicleID,SUM(TIMESTAMPDIFF(HOUR, tblchallanmaster.gatepasstime, tblvehiclereturn.Transdate)) AS TotalHour FROM `tblvehiclereturn`
			INNER JOIN tblchallanmaster ON tblchallanmaster.ChallanID  = tblvehiclereturn.ChallanID
			INNER JOIN tblvehicle on tblvehicle.VehicleID = tblchallanmaster.VehicleID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			
			return $data;
		}
		public function GetMileageReportChartVehicleWise($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$vehicle = $data["vehicle"];
			$driver = $data["driver"];
			$fuel_type = $data["fuel_type"];
			$Route = $data["Route"];
			$VehicleType = $data["VehicleType"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'vehiclereturn.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'"';
			if(!empty($vehicle)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.VehicleID = "'.$vehicle.'" ';
			}
			if(!empty($driver)){
				$sql1 .= ' AND '.db_prefix().'challanmaster.DriverID = "'.$driver.'" ';
			}
			if(!empty($fuel_type)){
				$sql1 .= ' AND '.db_prefix().'vehicle.fuel_type = "'.$fuel_type.'" ';
			}
			
			if (isset($Route) && $Route !== '') {
				$sql1 .= ' AND '.db_prefix().'challanmaster.RouteID = "'.$Route.'" ';
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$sql1 .= ' AND '.db_prefix().'vehicle.VehicleTypeID = "'.$VehicleType.'" ';
			}
			$sql1 .= ' GROUP BY tblchallanmaster.VehicleID ORDER BY AvgMileage DESC';
			
			$sql ='SELECT tblchallanmaster.VehicleID,SUM(tbltransport_entry.DistanceTravel) / NULLIF(SUM(tbltransport_entry.Diesel), 0) AS AvgMileage FROM `tblchallanmaster` INNER JOIN tblvehiclereturn ON tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID INNER JOIN tbltransport_entry ON tbltransport_entry.VehRtn_no = tblvehiclereturn.ReturnID INNER JOIN tblstaff ON tblstaff.AccountID = tblchallanmaster.DriverID
			LEFT JOIN tblvehicle on tblvehicle.VehicleID = tblchallanmaster.VehicleID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			
			return $data;
		}
		
		public function GetDataForTransportEntry($VehRtn)
		{  
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = ''.db_prefix().'vehiclereturn.ReturnID="'.$VehRtn.'" AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'" ';
			
			
			$sql ='SELECT '.db_prefix().'vehiclereturn.*,tblchallanmaster.Transdate AS Challandate,tblchallanmaster.VehicleID,tblchallanmaster.gatepasstime,tblroute.name as routename,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS Driver,tblvehicle.mileage,
			(SELECT COUNT(DISTINCT AccountID2) FROM '.db_prefix().'ordermaster WHERE '.db_prefix().'ordermaster.ChallanID = '.db_prefix().'vehiclereturn.ChallanID AND '.db_prefix().'ordermaster.PlantID = tblvehiclereturn.PlantID) as Total_Drop
			FROM '.db_prefix().'vehiclereturn
			INNER JOIN tblchallanmaster on tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID
			INNER JOIN tblroute on tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID =  tblchallanmaster.PlantID
			LEFT JOIN tblvehicle on tblvehicle.VehicleID = tblchallanmaster.VehicleID
			LEFT JOIN tblstaff on tblstaff.AccountID = tblchallanmaster.DriverID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->row();
			if(!empty($data)){
				$data->Detail = $this->TransportEntry_Detail($VehRtn);
			}
			return $data;
		}
		
		public function TransportEntry_Detail($VRtnID)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('VehRtn_no', $VRtnID);
			return $this->db->get(db_prefix() . 'transport_entry')->row();
		}
		
		// Add New Transport Entry
		public function SaveTransportEntry($data,$VehRtn_no)
		{
			$this->db->insert(db_prefix() . 'transport_entry', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				$this->db->where('ReturnID', $VehRtn_no);
				$this->db->update(db_prefix() . 'vehiclereturn', ['status'=>'Completed']);
				return true;    
				}else{
				return false;
			}
		}
		
		public function UpdateTransportEntry($data,$VehRtn_no)
		{
			$this->db->where('VehRtn_no', $VehRtn_no);
			$this->db->update(db_prefix() . 'transport_entry', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				
				$this->db->where('ReturnID', $VehRtn_no);
				$this->db->update(db_prefix() . 'vehiclereturn', ['status'=>'Completed']);
				return true;
				}else{
				return false;
			}
		}
		
		public function GetDmgCurrencyReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$Customer = $data["Customer"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'DamageCurrency.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'"';
			
			if(!empty($Customer)){
				$sql1 .= ' AND '.db_prefix().'DamageCurrency.AccountID = "'.$Customer.'" ';
			}
			$sql1 .= ' ORDER BY ChallanID ASC';
			
			$sql ='SELECT '.db_prefix().'DamageCurrency.*,tblchallanmaster.Transdate AS Challandate,tblvehiclereturn.Transdate AS ReturnDate,tblclients.company FROM '.db_prefix().'DamageCurrency
			INNER JOIN tblchallanmaster on tblchallanmaster.ChallanID = tblDamageCurrency.ChallanID
			INNER JOIN tblvehiclereturn on tblvehiclereturn.ReturnID = tblDamageCurrency.ReturnID
			INNER JOIN tblclients on tblclients.AccountID = tblDamageCurrency.AccountID
			WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function LoadPendingVehicleRetuernList($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$IsReturn = $data["IsReturn"];
			$IsMileage = $data["IsMileage"];
			$IsCrate = $data["IsCrate"];
			$IsPayment = $data["IsPayment"];
			$IsExpenses = $data["IsExpenses"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('tblchallanmaster.*,tblroute.name, users_table_a.firstname as driver_fn, CONCAT(users_table_a.firstname, " ", users_table_a.lastname) AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln,tblvehiclereturn.ReturnID,tblvehiclereturn.IsCrateRcvd,tblvehiclereturn.IsRtnRcvd,tblvehiclereturn.IsPaymentRcvd,tblvehiclereturn.IsExpRcvd,tblvehiclereturn.Crates  as return_crates,tblvehiclereturn.Transdate as returnTransdate');
			$this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
			$this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
			$this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
			$this->db->join('tblroute', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID AND tblvehiclereturn.PlantID = tblchallanmaster.PlantID', 'LEFT');
			if($IsReturn == "Y"){
				$this->db->where('tblvehiclereturn.Transdate  BETWEEN "'. $from_date. ' 00:00:00" and "'. $to_date.' 23:59:59"');
				}else{
				$this->db->where('tblchallanmaster.Transdate  BETWEEN "'. $from_date. ' 00:00:00" and "'. $to_date.' 23:59:59"');
			}
			$this->db->where('tblchallanmaster.PlantID', $selected_company);
			$this->db->where('tblchallanmaster.FY', $fy);
			if (!empty($IsReturn)) {
				if ($IsReturn == "Y") {
					$this->db->where('tblvehiclereturn.ChallanID IS NOT NULL'); // Ensures matching records
					} elseif ($IsReturn == "N") {
					$this->db->where('tblvehiclereturn.ChallanID IS NULL'); // Ensures non-matching records
				}
			}
			if(!empty($IsMileage)){
				$this->db->where('tblvehiclereturn.IsRtnRcvd', $IsMileage);
			}
			if(!empty($IsCrate)){
				$this->db->where('tblvehiclereturn.IsCrateRcvd', $IsCrate);
			}
			if(!empty($IsPayment)){
				$this->db->where('tblvehiclereturn.IsPaymentRcvd', $IsPayment);
			}
			if(!empty($IsExpenses)){
				$this->db->where('tblvehiclereturn.IsExpRcvd', $IsExpenses);
			}
			$this->db->where('tblchallanmaster.gatepasstime IS NOT NULL');
			$this->db->group_by('tblchallanmaster.ChallanID');
			$this->db->order_by('tblchallanmaster.ChallanID','desc');
			return $this->db->get('tblchallanmaster')->result_array();
		}
		
		public function LoadFinalVehicleReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'challanmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'vehiclereturn.PlantID="'.$selected_company.'"  ORDER BY tblchallanmaster.ChallanID DESC';
			
			$sql ='SELECT tblclients.company,'.db_prefix().'vehiclereturn.*,tblvehiclereturn.Crates AS InCrates,tblvehiclereturn.Transdate AS VehicleIndate,tblchallanmaster.Transdate AS Challandate,tblchallanmaster.VehicleType,tblchallanmaster.VehicleID,tblchallanmaster.gatepasstime,tblchallanmaster.Crates AS OutCrates,tblroute.name as routename,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS Driver,tblvehicle.mileage,tblordermaster.OrderID,tblordermaster.Dispatchdate,tblordermaster.OrderAmt,tbltransport_entry.out_meter_reading,tbltransport_entry.in_meter_reading
			FROM '.db_prefix().'ordermaster
			INNER JOIN tblclients on tblclients.AccountID = tblordermaster.AccountID
			INNER JOIN tblchallanmaster on tblchallanmaster.ChallanID = tblordermaster.ChallanID
			INNER JOIN tblvehiclereturn on tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID
			LEFT JOIN tbltransport_entry on tbltransport_entry.VehRtn_no = tblvehiclereturn .ReturnID
			LEFT JOIN tblroute on tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID =  tblchallanmaster.PlantID
			LEFT JOIN tblstaff on tblstaff.AccountID = tblchallanmaster.DriverID
			LEFT JOIN tblvehicle on tblvehicle.VehicleID = tblchallanmaster.VehicleID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			return $data;
		}
		
		
		/* public function GetVehiclesByType($VehicleType)
			{
			$this->db->select(db_prefix() . 'vehicle.*');
			$this->db->where(db_prefix() . 'vehicle.VehicleTypeID', $VehicleType);
			$this->db->order_by(db_prefix() . 'vehicle.VehicleID', 'ASC');
			return $this->db->get('tblvehicle')->result_array();
		} */
		public function GetVehiclesByType($data)
		{
			$from_date = to_sql_date($data["FromDate"])." 00:00:00";
			$to_date   = to_sql_date($data["ToDate"])." 23:59:59";
			$VehicleType = $data["VehicleType"];
			
			$this->db->select('tblvehicle.VehicleID, tblvehicle.VehicleTypeID');
			$this->db->from('tblordermaster');  
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'LEFT');
			
			if (!empty($data["FromDate"])) {
				$this->db->where('tblordermaster.Transdate >=', $from_date);
				$this->db->where('tblordermaster.Transdate <=', $to_date);
			}
			
			if (!empty($VehicleType)) {
				$this->db->where('tblvehicle.VehicleTypeID', $VehicleType);
			}
			$this->db->group_by('tblvehicle.VehicleID');
			$this->db->order_by('tblvehicle.VehicleID', 'ASC');
			
			return $this->db->get()->result_array();
		}
		public function GetPremisesReport()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'vehicle.VehicleID');
			$this->db->where(db_prefix() . 'vehicle.PlantID', $selected_company);
			$this->db->order_by('tblvehicle.VehicleID', 'ASC');
			
			$data = $this->db->get(db_prefix() . 'vehicle')->result_array();
			// echo $this->db->last_query();die;
			$todate = date('Y-m-d H:i:s');
			foreach ($data as &$each) {
				$this->db->select('tblvehiclereturn.Act_entry_datetime as EntryTime');
				$this->db->join('tblvehiclereturn ', 'tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID AND tblvehiclereturn.PlantID = tblchallanmaster.PlantID', 'left');
				$this->db->where(db_prefix() . 'challanmaster.PlantID', $selected_company);
				$this->db->where(db_prefix() . 'challanmaster.VehicleID', $each['VehicleID']);
				$this->db->order_by('tblvehiclereturn.Act_entry_datetime', 'DESC');
				$Entry = $this->db->get(db_prefix() . 'challanmaster')->row();
				
				$fromdate = isset($Entry->EntryTime) ? substr($Entry->EntryTime, 0, 19) : null;
				
				
				$each['EntryTime'] = $Entry->EntryTime;
				
				if ($fromdate) {
					$this->db->select('gatepasstime');
					$this->db->where('PlantID', $selected_company);
					$this->db->where('VehicleID', $each['VehicleID']);
					$this->db->where('gatepasstime >=', $fromdate);
					$this->db->where('gatepasstime <=', $todate);
					$this->db->order_by('gatepasstime', 'DESC');
					$Gatpass = $this->db->get('tblchallanmaster')->row();
					
					$each['GateoutTime'] = $Gatpass->gatepasstime ?? null;
					} else {
					$each['GateoutTime'] = null;
				}
			}
			return $data;
		}
		public function StationList()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'StationMaster.*');
			$this->db->where(db_prefix() . 'StationMaster.status', '1');
			$this->db->order_by('StationName', 'ASC');
			return $this->db->get(db_prefix() . 'StationMaster')->result_array();
		}
		
		public function SaveRestEntry($data)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$this->db->insert(db_prefix() . 'DriverRestRecord', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		public function SaveCustomerFeedback($data)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$this->db->insert(db_prefix() . 'CustomerFeedback', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		public function get_All_Feedback_Record(){
			
			$this->db->select(db_prefix() . 'CustomerFeedback.*');
			$this->db->from(db_prefix() . 'CustomerFeedback');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}
		public function get_All_Rest_Record(){
			
			$this->db->select(db_prefix() . 'DriverRestRecord.*');
			$this->db->from(db_prefix() . 'DriverRestRecord');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}
		public function getRest_Record_ByID($ID){
			
			$this->db->select(db_prefix() . 'DriverRestRecord.*');
			$this->db->from(db_prefix() . 'DriverRestRecord');
			$this->db->where(db_prefix() . 'DriverRestRecord.id', $ID);
			$data = $this->db->get()->row();
			
			return $data;
		}
		public function get_Feedback_Record_ByID($ID){
			
			$this->db->select(db_prefix() . 'CustomerFeedback.*');
			$this->db->from(db_prefix() . 'CustomerFeedback');
			$this->db->where(db_prefix() . 'CustomerFeedback.id', $ID);
			$data = $this->db->get()->row();
			
			return $data;
		}
		
		public function UpdateRestRecord($data,$ID)
		{
			$UserID = $this->session->userdata('username');
			$i = 0;
			$this->db->where('id', $ID);
			$this->db->update(db_prefix() . 'DriverRestRecord', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		public function UpdateCustomerFeedback($data,$ID)
		{
			$UserID = $this->session->userdata('username');
			$i = 0;
			$this->db->where('id', $ID);
			$this->db->update(db_prefix() . 'CustomerFeedback', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		public function LoadRestRecordReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$Driver = $data["Driver"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'DriverRestRecord.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") ';
			if(!empty($Driver)){
				$sql1 .= ' AND tblDriverRestRecord.DriverID = "'.$Driver.'"';
			}
			$sql1 .= 'ORDER BY tblDriverRestRecord.id DESC';
			$sql ='SELECT '.db_prefix().'DriverRestRecord.*,tblstaff.firstname,tblstaff.lastname
			FROM '.db_prefix().'DriverRestRecord
			INNER JOIN tblstaff on tblstaff.AccountID = tblDriverRestRecord.DriverID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			return $data;
		}
		
		public function LoadCustomerFeedbackReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$Driver = $data["Driver"];
			$Customer = $data["Customer"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'CustomerFeedback.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") ';
			
			if(!empty($Driver)){
				$sql1 .= ' AND tblCustomerFeedback.DriverID = "'.$Driver.'"';
			}
			if(!empty($Customer)){
				$sql1 .= ' AND tblCustomerFeedback.AccountID = "'.$Customer.'"';
			}
			$sql1 .= ' ORDER BY tblCustomerFeedback.id DESC';
			$sql ='SELECT '.db_prefix().'CustomerFeedback.*,tblstaff.firstname,tblstaff.lastname,tblclients.company
			FROM '.db_prefix().'CustomerFeedback
			INNER JOIN tblstaff on tblstaff.AccountID = tblCustomerFeedback.DriverID
			INNER JOIN tblclients on tblclients.AccountID = tblCustomerFeedback.AccountID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			return $data;
		}
		public function GetAllFeedbackList()
		{  
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 .= ' ORDER BY tblCustomerFeedback.id DESC';
			$sql ='SELECT '.db_prefix().'CustomerFeedback.*,tblstaff.firstname,tblstaff.lastname,tblclients.company
			FROM '.db_prefix().'CustomerFeedback
			INNER JOIN tblstaff on tblstaff.AccountID = tblCustomerFeedback.DriverID
			INNER JOIN tblclients on tblclients.AccountID = tblCustomerFeedback.AccountID'.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			return $data;
		}
		
		public function GetVehicleTypeChart()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$chart = [];
			
			$this->db->select('tblvehicle.VehicleTypeID, COUNT(*) as total_qty');
			$this->db->from(db_prefix() . 'vehicle');
			// $this->db->where('tblvehicle.ActiveYN','1');
			
			$this->db->group_by('tblvehicle.VehicleTypeID');
			
			$this->db->order_by("total_qty", "DESC");
			$Vehicles = $this->db->get()->result_array();
			//return $TopItem;
			$i=0;
			foreach ($Vehicles as $key => $value) {
				$VchType = 'Other';
				
				if($value['VehicleTypeID'] == 0){
					$VchType = 'Own';
				}
				if($value['VehicleTypeID'] == 1){
					$VchType = 'Transport';
				}
				if($value['VehicleTypeID'] == 2){
					$VchType = 'Rental';
				}
				array_push($chart, [
				'name' 		=> $VchType,
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Total"
				]);
				$i++;
			}
			
			
			return $chart;
		}
		
		public function GetVehicleFuelTypeChart()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$chart = [];
			
			$this->db->select('tblvehicle.fuel_type, COUNT(*) as total_qty');
			$this->db->from(db_prefix() . 'vehicle');
			// $this->db->where('tblvehicle.ActiveYN','1');
			
			$this->db->group_by('tblvehicle.fuel_type');
			
			$this->db->order_by("total_qty", "DESC");
			$Vehicles = $this->db->get()->result_array();
			//return $TopItem;
			$i=0;
			foreach ($Vehicles as $key => $value) {
				
				array_push($chart, [
				'name' 		=> $value['fuel_type'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Total"
				]);
				$i++;
			}
			
			
			return $chart;
		}
		public function GetTotalChallanByRouteChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			$chart = [];
			
			$this->db->select('tblroute.name, COUNT(*) as total_qty');
			$this->db->from(db_prefix() . 'vehiclereturn');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tblvehiclereturn.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblvehiclereturn.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$this->db->group_by('tblchallanmaster.RouteID');
			
			$this->db->order_by("total_qty", "DESC");
			$Vehicles = $this->db->get()->result_array();
			//return $TopItem;
			$i=0;
			foreach ($Vehicles as $key => $value) {
				
				array_push($chart, [
				'name' 		=> $value['name'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Total"
				]);
				$i++;
			}
			
			return $chart;
		}
		public function GetTotalChallanByDriverChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$chart = [];
			
			$this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name, COUNT(*) as total_qty');
			$this->db->from(db_prefix() . 'challanmaster');
			
			$this->db->join('tblstaff', 'tblstaff.AccountID = tblchallanmaster.DriverID AND tblstaff.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'LEFT');
			
			$this->db->where('tblchallanmaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblchallanmaster.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$this->db->group_by('tblchallanmaster.DriverID');
			
			$this->db->order_by("total_qty", "DESC");
			$this->db->limit("5");
			$Vehicles = $this->db->get()->result_array();
			//return $TopItem;
			$i=0;
			foreach ($Vehicles as $key => $value) {
				array_push($chart, [
				'name' 		=> $value['name'],
				'y' 		=>	(int) $value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Total"
				]);
				$i++;
			}
			
			return $chart;
		}
		public function GetHighestDeliveryStationChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$chart = [];
			
			$this->db->select('tblStationMaster.StationName, COUNT(*) as total_qty');
			$this->db->from(db_prefix() . 'ordermaster');
			
			$this->db->join('tblclients', 'tblclients.AccountID = tblordermaster.AccountID AND tblclients.PlantID = tblordermaster.PlantID', 'INNER');
			$this->db->join('tblStationMaster', 'tblStationMaster.id = tblclients.StationName', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'LEFT');
			
			$this->db->where('tblordermaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblordermaster.Transdate <=', $to_date.' 23:59:59');
			$this->db->where('tblordermaster.SalesID IS NOT NULL');
			
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			
			$this->db->group_by('tblStationMaster.id');
			
			$this->db->order_by("total_qty", "DESC");
			$this->db->limit("5");
			$Vehicles = $this->db->get()->result_array();
			//return $TopItem;
			$i=0;
			foreach ($Vehicles as $key => $value) {
				
				array_push($chart, [
				'name' 		=> $value['StationName'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Total"
				]);
				$i++;
			}
			
			return $chart;
		}
		
		public function GetTotalTripMonthWise($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$vehicle = $filterdata["vehicle"];
			$fuel_type = $filterdata["fuel_type"];
			$start_year = 2000 + $fy;       // 2000 + 25 = 2025
			$end_year   = $start_year + 1;  // 2026
			
			// Define financial year range
			$from_date = $start_year . '-04-01 00:00:00'; // 1st April 2025
			$to_date   = $end_year . '-03-31 23:59:59';   // 31st March 2026
			
			$chart = [];
			
			$this->db->select('DATE_FORMAT(tblchallanmaster.Transdate, "%b-%Y") as month, COUNT(*) as total_qty');
			$this->db->from(db_prefix() . 'challanmaster');
			
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'LEFT');
			$this->db->where('tblchallanmaster.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblchallanmaster.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$this->db->group_by("YEAR(tblchallanmaster.Transdate), MONTH(tblchallanmaster.Transdate)");
			$this->db->order_by("YEAR(tblchallanmaster.Transdate), MONTH(tblchallanmaster.Transdate)", "ASC");
			
			$this->db->order_by("total_qty", "DESC");
			$Vehicles = $this->db->get()->result_array();
			//return $TopItem;
			$i=0;
			foreach ($Vehicles as $key => $value) {
				
				array_push($chart, [
				'name' 		=> $value['month'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Total"
				]);
				$i++;
			}
			
			return $chart;
		}
		public function GetTotalExpenseMonthWise($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			$FilterType = $filterdata["FilterType"];
			$start_year = 2000 + $fy; 
			$end_year   = $start_year + 1;
			
			// Define financial year range
			$from_date = $start_year . '-04-01 00:00:00'; // 1st April 2025
			$to_date   = $end_year . '-03-31 23:59:59';   // 31st March 2026
			
			
			$this->db->select('DATE_FORMAT(tbltransport_entry.Transdate, "%b-%Y") as month');
			$this->db->select('ROUND(SUM(Diesel_value),2) as FuelExp');
			$this->db->select('ROUND(SUM(Fooding),2) as FoodingExp');
			$this->db->select('ROUND(SUM(Toll),2) as TollExp');
			$this->db->select('ROUND(SUM(Phone),2) as PhoneExp');
			$this->db->select('ROUND(SUM(Police),2) as PoliceExp');
			$this->db->select('ROUND(SUM(Misc_Expense),2) as MiscExp');
			$this->db->select('ROUND(SUM(Expense_repairing),2) as RepairingExp');
			
			// Select based on FilterType
			if ($FilterType == 'Route') {
				$this->db->select('tblroute.RouteID, tblroute.name AS RouteName');
				} elseif ($FilterType == 'VehicleType') {
				$this->db->select('tblvehicle.VehicleTypeID');
				}elseif ($FilterType == 'FuelType') {
				$this->db->select('tblvehicle.fuel_type');
			}
			$this->db->from(db_prefix() . 'transport_entry');
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ReturnID = tbltransport_entry.VehRtn_no', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tbltransport_entry.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tbltransport_entry.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$this->db->group_by("YEAR(tbltransport_entry.Transdate), MONTH(tbltransport_entry.Transdate)");
			if ($FilterType == 'Route') {
				$this->db->group_by("tblchallanmaster.RouteID");
			}
			if ($FilterType == 'VehicleType') {
				$this->db->group_by("tblvehicle.VehicleTypeID");
			}
			if ($FilterType == 'FuelType') {
				$this->db->group_by("tblvehicle.fuel_type");
			}
			$this->db->order_by("YEAR(tbltransport_entry.Transdate), MONTH(tbltransport_entry.Transdate)", "ASC");
			$rows = $this->db->get()->result_array();
			
			$chartData = [];
			$Months = [];
			$currentYear = date('Y');
			$currentMonth = date('n'); // numeric month without leading zero, e.g. 9 for September
			
			// Months from April to December of the start year
			for ($i = 4; $i <= 12; $i++) {
				if ($start_year > $currentYear || ($start_year == $currentYear && $i > $currentMonth)) {
					break;
				}
				$date = "$start_year-$i-01";
				$Months[] = date("M-Y", strtotime($date));
			}
			
			// Months from January to March of the end year
			for ($i = 1; $i <= 3; $i++) {
				if ($end_year > $currentYear || ($end_year == $currentYear && $i > $currentMonth)) {
					break;
				}
				$date = "$end_year-$i-01";
				$Months[] = date("M-Y", strtotime($date));
			}
			
			
			// Prepare grouped data
			$groupData = [];
			foreach ($rows as $row) {
				if ($FilterType == 'Route') {
					$groupId = $row['RouteID'];
					$groupName = $row['RouteName'];
					} elseif ($FilterType == 'VehicleType') {
					$groupId = $row['VehicleTypeID'];
					if($row['VehicleTypeID'] == 0){
						$groupName = 'Own';
						}elseif($row['VehicleTypeID'] == 1){
						$groupName = 'Transport';
						}elseif($row['VehicleTypeID'] == 2){
						$groupName = 'Rental';
						}else{
						$groupName = '';
					}
					} elseif ($FilterType == 'FuelType') {
					$groupId = $row['fuel_type'];
					$groupName = $row['fuel_type'];
					} else {
					continue; // Skip if FilterType is neither
				}
				
				$AllExp = $row['FuelExp'] + $row['FoodingExp'] + $row['TollExp'] + $row['PhoneExp'] + $row['PoliceExp'] + $row['MiscExp'] + $row['RepairingExp'];
				
				if (!isset($groupData[$groupId])) {
					$groupData[$groupId] = [
					'name' => $groupName,
					'data' => array_fill(0, count($Months), 0)
					];
				}
				
				// Find index of month
				$index = array_search($row['month'], $Months);
				if ($index !== false) {
					$groupData[$groupId]['data'][$index] = (float) round($AllExp,2);
				}
			}
			
			$series = array_values($groupData);
			
			$ReturnData = [
			'chartData' => $series,
			'Months' => $Months,
			];
			
			return $ReturnData;
		}
		public function GetFleetUtilization($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]); // format: Y-m-d
			$to_date = to_sql_date($filterdata["to_date"]);     // format: Y-m-d
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$this->db->select('DATE(tblchallanmaster.Transdate) as date');
			$this->db->select('ROUND(SUM(VehicleCapacity),2) as Capacity');
			$this->db->select('ROUND(SUM(Crates),2) as Crates');
			$this->db->select('ROUND(SUM(Cases),2) as Cases');
			
			$this->db->from(db_prefix() . 'challanmaster');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			
			$this->db->where('tblchallanmaster.Transdate >=', $from_date . ' 00:00:00');
			$this->db->where('tblchallanmaster.Transdate <=', $to_date . ' 23:59:59');
			
			if(isset($Route) && $Route !== ''){
				$this->db->where('tblchallanmaster.RouteID', $Route);
			}
			if(!empty($vehicle)) {
				$this->db->where('tblchallanmaster.VehicleID', $vehicle);
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID', $VehicleType);
			}
			if(!empty($fuel_type)) {
				$this->db->where('tblvehicle.fuel_type', $fuel_type);
			}
			
			$this->db->group_by('DATE(tblchallanmaster.Transdate)');
			$this->db->order_by('DATE(tblchallanmaster.Transdate)', 'ASC');
			
			$rows = $this->db->get()->result_array();
			// echo "<pre>";print_r($rows);die;
			// Generate list of dates between from_date and to_date with format d-m-Y
			$Dates = [];
			$begin = new DateTime($from_date);
			$end = new DateTime($to_date);
			$end->modify('+1 day'); // include end date
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval, $end);
			
			foreach ($daterange as $date) {
				$Dates[] = $date->format('d-M');
			}
			
			// Prepare utilization data with all dates initialized to 0
			$utilizationData = array_fill(0, count($Dates), 0);
			
			foreach ($rows as $row) {
				$rowDate = date('d-M', strtotime($row['date']));
				
				$index = array_search($rowDate, $Dates);
				if ($index !== false) {
					$capacity = (float)$row['Capacity'];
					$crates = (float)$row['Crates'];
					$cases = (float)$row['Cases'];
					
					if ($capacity > 0) {
						$utilization = (($cases + $crates) / $capacity) * 100;
						$utilization = round($utilization, 2);
						} else {
						$utilization = 0;
					}
					
					$utilizationData[$index] = $utilization;
				}
			}
			
			// Prepare the final series data
			$series = [
			[
            'name' => 'Fleet Utilization',
            'data' => $utilizationData
			]
			];
			
			$ReturnData = [
			'chartData' => $series,
			'Dates' => $Dates
			];
			
			return $ReturnData;
		}
		
		
		public function FuelConsumptionBydate($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			
			$this->db->select('ROUND(SUM(tbltransport_entry.Diesel_value),2) AS FuelConsumption');
			$this->db->from(db_prefix() . 'transport_entry');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ReturnID = tbltransport_entry.VehRtn_no', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tbltransport_entry.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tbltransport_entry.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$Fuel = $this->db->get()->row();
			
			
			return $Fuel;
		}
		public function FuelEfficiencyBydate($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			
			$vehicle = $filterdata["vehicle"];
			$fuel_type = $filterdata["fuel_type"];
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			
			
			$Standard = [];
			$Actual = [];
			
			
			$this->db->select('COALESCE(SUM(tbltransport_entry.DistanceTravel) / NULLIF(SUM(tbltransport_entry.Diesel), 0),0) AS AvgMileage');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ChallanID = tblchallanmaster.ChallanID');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID');
			$this->db->join('tbltransport_entry', 'tbltransport_entry.VehRtn_no = tblvehiclereturn.ReturnID');
			$this->db->where('tblvehiclereturn.PlantID', $selected_company);
			$this->db->where("tblvehiclereturn.Transdate BETWEEN '$from_date' AND '$to_date'");
			
			if(!empty($vehicle)){ 
				$this->db->where('tblchallanmaster.VehicleID', $vehicle); 
			}
			if(!empty($fuel_type)){ 
				$this->db->where('tblvehicle.fuel_type', $fuel_type);
			}
			if (isset($Route) && $Route !== '') { 
				$this->db->where('tblchallanmaster.RouteID', $Route); 
			}
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID', $VehicleType); 
			}
			$Record = $this->db->get('tblchallanmaster')->row();
			
			
			$data = [
			'FuelEfficiency' => $Record->AvgMileage,
			];
			
			return $data;
		}
		public function TotalReturnEntryBydate($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$this->db->select('COUNT(*) as TotalEntry');
			$this->db->from(db_prefix() . 'vehiclereturn');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tblvehiclereturn.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tblvehiclereturn.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$Entry = $this->db->get()->row();
			
			
			return $Entry;
		}
		public function TotalVehiclesByFilter($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			
			$this->db->select('COUNT(*) as TotalEntry');
			$this->db->from(db_prefix() . 'vehicle');
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$Entry = $this->db->get()->row();
			
			
			return $Entry;
		}
		public function DistanceTravelBydate($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$this->db->select('ROUND(SUM(DistanceTravel),2) as TotalTravel');
			$this->db->from(db_prefix() . 'transport_entry');
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ReturnID = tbltransport_entry.VehRtn_no', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tbltransport_entry.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tbltransport_entry.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$Entry = $this->db->get()->row();
			
			
			return $Entry;
		}
		public function ExpensesCalculationBydate($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$this->db->select('ROUND(SUM(Diesel_value),2) as FuelExp,ROUND(SUM(Fooding),2) as FoodingExp,ROUND(SUM(Toll),2) as TollExp,ROUND(SUM(Phone),2) as PhoneExp,ROUND(SUM(Police),2) as PoliceExp,ROUND(SUM(Misc_Expense),2) as MiscExp,ROUND(SUM(Expense_repairing),2) as RepairingExp');
			$this->db->from(db_prefix() . 'transport_entry');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ReturnID = tbltransport_entry.VehRtn_no', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tbltransport_entry.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tbltransport_entry.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$Entry = $this->db->get()->row();
			
			
			$chart = [];
			
			if ($Entry) {
				$expenses = [
				'Fuel'      => $Entry->FuelExp,
				'Fooding'   => $Entry->FoodingExp,
				'Toll'      => $Entry->TollExp,
				'Phone'     => $Entry->PhoneExp,
				'Police'    => $Entry->PoliceExp,
				'Misc'      => $Entry->MiscExp,
				'Repairing' => $Entry->RepairingExp
				];
				
				foreach ($expenses as $name => $value) {
					$chart[] = [
					'name'  => $name,
					'y'     => (float)$value,
					'z'     => 100,
					'label' => "Total"
					];
				}
			}
			return $chart;
		}
		public function Get_Highest_maintance_charge_vehicles($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			$chart = [];
			
			$this->db->select('tblvehicle.VehicleID,ROUND(SUM(Expense_repairing),2) as RepairingExp');
			$this->db->from(db_prefix() . 'transport_entry');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ReturnID = tbltransport_entry.VehRtn_no', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tbltransport_entry.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tbltransport_entry.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			
			$this->db->group_by('tblchallanmaster.VehicleID');
			
			$this->db->order_by("RepairingExp", "DESC");
			$this->db->limit("5");
			$Vehicles = $this->db->get()->result_array();
			// echo "<pre>";print_r($Vehicles);die;
			// echo $this->db->last_query();die;
			//return $TopItem;
			$i=0;
			foreach ($Vehicles as $key => $value) {
				
				array_push($chart, [
				'name' 		=> $value['VehicleID'],
				'y' 		=>	(int)$value['RepairingExp'],
				'z' 		=> 100,
				'label' 		=> "Total"
				]);
				$i++;
			}
			
			return $chart;
		}
		
		public function ExpensesCalculationCounter($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$VehicleType = $filterdata["VehicleType"];
			$fuel_type = $filterdata["fuel_type"];
			$vehicle = $filterdata["vehicle"];
			
			// $this->db->select('ROUND(SUM(Diesel_value),2) as FuelExp,ROUND(SUM(Fooding),2) as FoodingExp,ROUND(SUM(Toll),2) as TollExp,ROUND(SUM(Phone),2) as PhoneExp,ROUND(SUM(Police),2) as PoliceExp,ROUND(SUM(Misc_Expense),2) as MiscExp,ROUND(SUM(Expense_repairing),2) as RepairingExp');
			$this->db->select('ROUND(SUM(Diesel_value) + SUM(Fooding) + SUM(Toll) + SUM(Phone) + SUM(Police) + SUM(Misc_Expense) + SUM(Expense_repairing), 2) as TotalExp');
			$this->db->from(db_prefix() . 'transport_entry');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ReturnID = tbltransport_entry.VehRtn_no', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblroute', 'tblroute.RouteID = tblchallanmaster.RouteID AND tblroute.PlantID = tblchallanmaster.PlantID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->where('tbltransport_entry.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tbltransport_entry.Transdate <=', $to_date.' 23:59:59');
			if (isset($Route) && $Route !== '') {
				$this->db->where('tblchallanmaster.RouteID ', $Route);
			}
			if(!empty($vehicle)){
				$this->db->where('tblchallanmaster.VehicleID ', $vehicle);
			}
			
			if(isset($VehicleType) && $VehicleType !== ''){
				$this->db->where('tblvehicle.VehicleTypeID ', $VehicleType);
			}
			if(!empty($fuel_type)){
				$this->db->where('tblvehicle.fuel_type ', $fuel_type);
			}
			$Entry = $this->db->get()->row();
			
			return $Entry;
		}
		
		public function LoadVehicleMaintenanceReport($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			
			$this->db->select('tbltransport_entry.Transdate,tblchallanmaster.VehicleID,tblchallanmaster.ChallanID,Expense_repairing,Repairing_Expense_Remark,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS DriverName');
			$this->db->from(db_prefix() . 'transport_entry');
			
			$this->db->join('tblvehiclereturn', 'tblvehiclereturn.ReturnID = tbltransport_entry.VehRtn_no', 'INNER');
			$this->db->join('tblchallanmaster', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID', 'INNER');
			$this->db->join('tblvehicle', 'tblvehicle.VehicleID = tblchallanmaster.VehicleID', 'INNER');
			$this->db->join('tblstaff', 'tblstaff.AccountID = tblchallanmaster.DriverID', 'INNER');
			$this->db->where('tbltransport_entry.Transdate >=', $from_date.' 00:00:00');
			$this->db->where('tbltransport_entry.Transdate <=', $to_date.' 23:59:59');
			$this->db->where('tbltransport_entry.Expense_repairing >', 0);
			$this->db->order_by('tbltransport_entry.Transdate ', 'ASC');
			
			$Entry = $this->db->get()->result_array();
			
			return $Entry;
		}
		
		
		public function get_All_VehicleMaintenance_Record(){
			
			$this->db->select(db_prefix() . 'VehicleMaintenanceRecord.*');
			$this->db->from(db_prefix() . 'VehicleMaintenanceRecord');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}
		
		public function SaveMaintenanceEntry($data)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$this->db->insert(db_prefix() . 'VehicleMaintenanceRecord', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		public function UpdateMaintenanceEntry($data,$ID)
		{
			$UserID = $this->session->userdata('username');
			$i = 0;
			$this->db->where('id', $ID);
			$this->db->update(db_prefix() . 'VehicleMaintenanceRecord', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		public function getMaintenanceEntry_ByID($ID){
			
			$this->db->select(db_prefix() . 'VehicleMaintenanceRecord.*');
			$this->db->from(db_prefix() . 'VehicleMaintenanceRecord');
			$this->db->where(db_prefix() . 'VehicleMaintenanceRecord.id', $ID);
			$data = $this->db->get()->row();
			
			return $data;
		}
		
		public function LoadVehicleMaintenanceEntryReport($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$Driver = $data["Driver"];
			$VehicleID = $data["VehicleID"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'VehicleMaintenanceRecord.TransDate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") ';
			if(!empty($VehicleID)){
				$sql1 .= ' AND tblVehicleMaintenanceRecord.VehicleID = "'.$VehicleID.'"';
			}
			if(!empty($Driver)){
				$sql1 .= ' AND tblVehicleMaintenanceRecord.DriverID = "'.$Driver.'"';
			}
			$sql1 .= 'ORDER BY tblVehicleMaintenanceRecord.id DESC';
			$sql ='SELECT '.db_prefix().'VehicleMaintenanceRecord.*,tblstaff.firstname,tblstaff.lastname
			FROM '.db_prefix().'VehicleMaintenanceRecord
			INNER JOIN tblstaff on tblstaff.AccountID = tblVehicleMaintenanceRecord.DriverID
			WHERE '.$sql1;
			
			$data = $this->db->query($sql)->result_array();
			return $data;
		}
		
	}
?>