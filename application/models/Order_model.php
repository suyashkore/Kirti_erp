<?php

	

	defined('BASEPATH') or exit('No direct script access allowed');

	

	class Order_model extends App_Model

	{

		const STATUS_UNPAID = 1;

		

		const STATUS_PAID = 2;

		

		const STATUS_PARTIALLY = 3;

		

		const STATUS_OVERDUE = 4;

		

		const STATUS_CANCELLED = 5;

		

		const STATUS_DRAFT = 6;

		

		private $statuses = [

        self::STATUS_UNPAID,

        self::STATUS_PAID,

        self::STATUS_PARTIALLY,

        self::STATUS_OVERDUE,

        self::STATUS_CANCELLED,

        self::STATUS_DRAFT,

		];

		

		private $shipping_fields = [

        'shipping_street',

        'shipping_city',

        'shipping_city',

        'shipping_state',

        'shipping_zip',

        'shipping_country',

		];

		

		public function __construct()

		{

			parent::__construct();

		}

		

		public function get_statuses()

		{

			return $this->statuses;

		}

		

		public function get_sale_agents()

		{

			return $this->db->query('SELECT DISTINCT(sale_agent) as sale_agent, CONCAT(firstname, \' \', lastname) as full_name FROM ' . db_prefix() . 'invoices JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'invoices.sale_agent WHERE sale_agent != 0')->result_array();

		}

		

		/**

			* Get invoice by id

			* @param  mixed $id

			* @return array|object

		*/

		// public function get($id = '', $where = [])

		// {

		// 	$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'order.id as id, ' . db_prefix() . 'currencies.name as currency_name');

		// 	$this->db->from(db_prefix() . 'order');

		// 	$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'order.currency', 'left');

		// 	$this->db->where($where);

		// 	if (is_numeric($id)) {

		// 		$this->db->where(db_prefix() . 'order' . '.id', $id);

		// 		$invoice = $this->db->get()->row();

		// 		if ($invoice) {

		// 			$invoice->total_left_to_pay = get_invoice_total_left_to_pay($invoice->id, $invoice->total);

					

		// 			$invoice->items       = get_items_by_type2('order', $id);

		// 			$invoice->attachments = $this->get_attachments($id);

					

		// 			if ($invoice->project_id != 0) {

		// 				$this->load->model('projects_model');

		// 				$invoice->project_data = $this->projects_model->get($invoice->project_id);

		// 			}

					

		// 			$invoice->visible_attachments_to_customer_found = false;

		// 			foreach ($invoice->attachments as $attachment) {

		// 				if ($attachment['visible_to_customer'] == 1) {

		// 					$invoice->visible_attachments_to_customer_found = true;

							

		// 					break;

		// 				}

		// 			}

					

		// 			$client          = $this->clients_model->get($invoice->clientid);

		// 			$invoice->client = $client;

		// 			if (!$invoice->client) {

		// 				$invoice->client          = new stdClass();

		// 				$invoice->client->company = $invoice->deleted_customer_name;

		// 			}

					

		// 			$this->load->model('payments_model');

		// 			$invoice->payments = $this->payments_model->get_invoice_payments($id);

					

		// 			$this->load->model('email_schedule_model');

		// 			$invoice->scheduled_email = $this->email_schedule_model->get($id, 'invoice');

		// 		}

				

		// 		return hooks()->apply_filters('get_invoice', $invoice);

		// 	}

			

		// 	$this->db->order_by('number,YEAR(date)', 'desc');

			

		// 	return $this->db->get()->result_array();

		// }

		

		public function get2($id = '', $where = [])

		{

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');

			$this->db->from(db_prefix() . 'ordermaster');

			$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');

			$this->db->where($where);

			if ($id) {

				$this->db->where(db_prefix() . 'ordermaster.OrderID', $id);

				$this->db->where(db_prefix() . 'ordermaster.FY', $fy);

				$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

				$order = $this->db->get()->row();

				if ($order) {

					

					$client          = $this->clients_model->get($order->AccountID);

					$order->client = $client;

					$client2          = $this->clients_model->get($order->AccountID2);

					$order->client2 = $client2;

					$accbal = $this->get_accbal($order->AccountID,$selected_company,$fy);

					$order->accbal = $accbal;

					$last_billed_on = $this->get_last_bill_on($order->AccountID,$selected_company,$fy);

					$order->last_billed_on = $last_billed_on;

					$last_deposit_on = $this->get_last_deposit_on($order->AccountID,$selected_company,$fy);

					$order->last_deposit_on = $last_deposit_on;

					$item          = $this->get_order_items($order->OrderID,$selected_company,$fy);

					$item_free_dist          = $this->get_order_items_free_distribution($order->OrderID,$selected_company,$fy);

					$itemStocks          = $this->GetItemStock($order->OrderID,$selected_company,$fy);

					$order->items = $item;

					$order->items_free_dist = $item_free_dist;

					$order->itemStocks = $itemStocks;

					if($order->ChallanID !== null){

						$SaleDetails = $this->SaleDetails($order->SalesID,$selected_company,$fy);

						$ChallanDetails = $this->ChallanDetails($order->ChallanID,$selected_company,$fy);

						$order->ChallanDetails = $ChallanDetails;

						$order->SaleDetails = $SaleDetails;

					}

				}

				

				return hooks()->apply_filters('get_invoice', $order);

			}

			

			//$this->db->order_by('YEAR(date)', 'desc');

			$this->db->where(db_prefix() . 'ordermaster.FY', $fy);

			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

			return $this->db->get()->result_array();

		}

		

		// public function check_pending_order($customer_id = '')

		// {

		// 	$fy = $this->session->userdata('finacial_year');

		// 	$selected_company = $this->session->userdata('root_company');

			

		// 	$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');

		// 	$this->db->from(db_prefix() . 'ordermaster');

		// 	$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');

			

		// 	$this->db->where(db_prefix() . 'ordermaster.AccountID', $customer_id);

		// 	$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');

		// 	$this->db->where(db_prefix() . 'ordermaster.ChallanID', null);

		// 	$this->db->where(db_prefix() . 'ordermaster.FY', $fy);

		// 	$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

		// 	$order_data = $this->db->get()->result_array();

		// 	if(empty($order_data)){

		// 		return true; 

		// 		}else{

		// 		if($selected_company == "1"){

		// 			$TaxItems = 0;

		// 			$NonTaxItems = 0;

		// 			foreach ($order_data as $key => $value) {

		// 				if($value['OrderType']=="TaxItems"){

		// 					$TaxItems = 1;

		// 				}

		// 				if($value['OrderType']=="NonTaxItems"){

		// 					$NonTaxItems = 1;

		// 				}

		// 			}

		// 			if($TaxItems == "1" && $NonTaxItems == "1"){

		// 				return false;

		// 			}

		// 			if($TaxItems == "1"){

		// 				return 'NonTaxItems';

		// 			}

		// 			if($NonTaxItems == "1"){

		// 				return 'TaxItems';

		// 			}

		// 			}else{

		// 			return false;

		// 		}

		// 	}

		// }

		//==================== Get Delay Order List ====================================

		public function GetDelayOrders($data)

		{  

			$from_date = to_sql_date($data["from_date"]);

			$dates = to_sql_date($data["dates"]);

			$state = $data["state"];

			$dist_type = $data["dist_type"];

			$StationName = $data["StationName"];

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			$sql1 = 'tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND tblordermaster.OrderStatus IN("O") AND tblordermaster.FY = '.$fy; 

			

			if ($StationName){

				$sql1 .= ' AND tblclients.StationName = "'.$StationName.'"';

			}

			if ($state){

				$sql1 .= ' AND tblclients.state = "'.$state.'"';

			}

			if ($dist_type){

				$sql1 .= ' AND tblclients.DistributorType = '.$dist_type.'';

			}

			$sql1 .= ' AND tblordermaster.PlantID = '.$selected_company.'';

			$sql ='SELECT tblordermaster.*,tblclients.company AS AccountName,tblStationMaster.StationName,tblclients.state AS StateName,

			tblchallanmaster.GetPassTime,tblchallanmaster.Gatepassuserid,VehicleID,DriverID,

			ShipAddrs.company AS ShipToAccountName,tblcustomers_groups.name AS dist_Type,

			IFNULL(CONCAT(CreateStaff.firstname," ",CreateStaff.lastname),"") AS CreateStaffName

			FROM tblordermaster 

			INNER JOIN tblclients ON tblclients.AccountID = tblordermaster.AccountID AND tblclients.PlantID = tblordermaster.PlantID

			LEFT JOIN tblchallanmaster ON tblchallanmaster.ChallanID = tblordermaster.ChallanID

			LEFT JOIN tblStationMaster ON tblStationMaster.id = tblclients.StationName

			LEFT JOIN tblcustomers_groups ON tblcustomers_groups.id = tblclients.DistributorType

			LEFT JOIN tblclients AS ShipAddrs ON ShipAddrs.AccountID = tblordermaster.AccountID2 AND ShipAddrs.PlantID = tblordermaster.PlantID

			LEFT JOIN tblstaff AS CreateStaff ON CreateStaff.AccountID = tblordermaster.UserID

			WHERE '.$sql1;

			$result = $this->db->query($sql)->result_array();

			return $result;

		}
		
	public function check_pending_order($customer_id = '')
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');
			$this->db->from(db_prefix() . 'ordermaster');
			$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
			
			$this->db->where(db_prefix() . 'ordermaster.AccountID', $customer_id);
			$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
			$this->db->where(db_prefix() . 'ordermaster.ChallanID', null);
			$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
			$order_data = $this->db->get()->result_array();
			if(empty($order_data)){
				return true; 
				}else{
				if($selected_company == "1"){
					$TaxItems = 0;
					$NonTaxItems = 0;
					foreach ($order_data as $key => $value) {
						if($value['OrderType']=="TaxItems"){
							$TaxItems = 1;
						}
						if($value['OrderType']=="NonTaxItems"){
							$NonTaxItems = 1;
						}
					}
					if($TaxItems == "1" && $NonTaxItems == "1"){
						return false;
					}
					if($TaxItems == "1"){
						return 'NonTaxItems';
					}
					if($NonTaxItems == "1"){
						return 'TaxItems';
					}
					}else{
					return false;
				}
			}
		}

		public function load_data($data)

		{  

			$from_date = to_sql_date($data["from_date"]);

			$dates = to_sql_date($data["dates"]);

			$order_type = $data["order_type"];

			$state = $data["state"];

			$dist_type = $data["dist_type"];

			$sort_by = $data["sort_by"];

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			

			if($order_type == "all"){

				$sql1 = 'tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND tblordermaster.OrderStatus IN("C","O") AND tblordermaster.ChallanID IS null AND tblordermaster.FY = '.$fy. ' AND '; 

			}

			if($order_type == "O"){

				$sql1 = 'tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND tblordermaster.OrderStatus IN("O") AND tblordermaster.ChallanID IS null AND tblordermaster.FY = '.$fy. ' AND '; 

			}

			if($order_type == "C"){

				$sql1 = 'tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND tblordermaster.OrderStatus IN("C") AND tblordermaster.ChallanID IS null AND tblordermaster.FY = '.$fy. ' AND '; 

			}

			if ($state){

				$sql1 .= ' AND tblclients.state = "'.$state.'"';

			}

			

			if ($dist_type){

				$sql1 .= ' AND tblclients.DistributorType = '.$dist_type.'';

			}

			

			$sql1 .= 'tblordermaster.PlantID = '.$selected_company.'';

			

			if (!empty($sort_by)) {

				if($sort_by == 'Dispatch'){

					$sql1 .= '  ORDER BY tblordermaster.Dispatchdate,tblordermaster.OrderID ASC';

				}

				if($sort_by == 'Punch'){

					$sql1 .= '  ORDER BY tblordermaster.Transdate,tblordermaster.OrderID ASC';

				}

				if($sort_by == 'OrderID'){

					$sql1 .= '  ORDER BY tblordermaster.OrderID ASC';

				}

			}

			

			/*$sql ='SELECT '.db_prefix().'ordermaster.*,IFNULL(BAL1,0.00) as bal1,IFNULL(BAL2,0.00) as bal2,IFNULL(BAL3,0.00) as bal3,IFNULL(BAL4,0.00) as bal4,IFNULL(BAL5,0.00) as bal5,IFNULL(BAL6,0.00) as bal6,IFNULL(BAL7,0.00) as bal7,IFNULL(BAL8,0.00) as bal8,IFNULL(BAL9,0.00) as bal9,IFNULL(BAL10,0.00) as bal10,IFNULL(BAL11,0.00) as bal11,IFNULL(BAL12,0.00) as bal12,IFNULL(BAL13,0.00) as bal13,

				(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName,

				(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID2 AND '.db_prefix().'clients.PlantID = '.$selected_company.') as ShipToAccountName,

				(SELECT GROUP_CONCAT(StationName SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as StationName,

				(SELECT GROUP_CONCAT(CONCAT ( firstname," ",lastname ) SEPARATOR ",") FROM '.db_prefix().'customer_admins 

				LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_admins.staff_id 

				WHERE '.db_prefix().'customer_admins.customer_id = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'customer_admins.company_id = '.$selected_company.') as SOID,

				(SELECT '.db_prefix().'xx_statelist.short_name FROM  '.db_prefix().'xx_statelist

				INNER JOIN '.db_prefix().'clients ON '.db_prefix().'xx_statelist.short_name = '.db_prefix().'clients.state

				WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.'  GROUP BY '.db_prefix().'xx_statelist.short_name) as StateName,

				(SELECT '.db_prefix().'customers_groups.name FROM  '.db_prefix().'customers_groups

				INNER JOIN '.db_prefix().'clients ON '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType

				WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.' GROUP BY '.db_prefix().'customers_groups.name) as dist_Type 

				FROM '.db_prefix().'ordermaster 

				LEFT JOIN '.db_prefix().'accountbalances 

				ON '.db_prefix().'ordermaster.AccountID = '.db_prefix().'accountbalances.AccountID AND '.db_prefix().'ordermaster.PlantID = '.db_prefix().'accountbalances.PlantID AND '.db_prefix().'ordermaster.FY = '.db_prefix().'accountbalances.FY

			WHERE '.$sql1;*/

			

			$sql ='SELECT tblordermaster.*,IFNULL(tblaccountbalances.BAL1,0.00) as bal1,tblclients.company AS AccountName,tblclients.StationName,tblclients.state AS StateName,

			ShipAddrs.company AS ShipToAccountName,CONCAT(tblstaff.firstname," ",tblstaff.lastname) AS SOID,tblcustomers_groups.name AS dist_Type,

			IFNULL(CONCAT(CancelStaff.firstname," ",CancelStaff.lastname),"") AS CancelStaffName,

			IFNULL(CONCAT(CreateStaff.firstname," ",CreateStaff.lastname),"") AS CreateStaffName

			FROM '.db_prefix().'ordermaster 

			INNER JOIN tblclients ON tblclients.AccountID = tblordermaster.AccountID AND tblclients.PlantID = tblordermaster.PlantID

			LEFT JOIN tblcustomers_groups ON tblcustomers_groups.id = tblclients.DistributorType

			LEFT JOIN tblclients AS ShipAddrs ON ShipAddrs.AccountID = tblordermaster.AccountID2 AND ShipAddrs.PlantID = tblordermaster.PlantID

			LEFT JOIN tblcustomer_admins ON tblcustomer_admins.customer_id = tblordermaster.AccountID AND tblcustomer_admins.company_id = tblordermaster.PlantID

			LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_admins.staff_id

			LEFT JOIN tblstaff AS CancelStaff ON CancelStaff.AccountID = tblordermaster.CancelUserID

			LEFT JOIN tblstaff AS CreateStaff ON CreateStaff.AccountID = tblordermaster.UserID

			LEFT JOIN tblaccountbalances 

			ON tblordermaster.AccountID = tblaccountbalances.AccountID AND tblordermaster.PlantID = tblaccountbalances.PlantID AND 

			tblordermaster.FY = tblaccountbalances.FY

			WHERE '.$sql1;

			

			

			$result = $this->db->query($sql)->result_array();

			$i = 0;

			$fy_to = $fy + 1;

			$from_date = '20'.$fy.'-04-01';

			$to_date = '20'.$fy_to.'-03-31';

			foreach($result as $value){

				// credit crated

                $this->db->select('sum(Amount) as credit_bal,AccountID');

                $this->db->where('tblaccountledger.PlantID', $selected_company);

                $this->db->where('tblaccountledger.FY', $fy);

                $this->db->where('tblaccountledger.TType', 'C');

                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);

                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');

                $this->db->group_by('AccountID');

                $credit_bal = $this->db->get('tblaccountledger')->result_array();

                

				// Debit crated

                $this->db->select('sum(Amount) as debit_bal,AccountID');

                $this->db->where('tblaccountledger.PlantID', $selected_company);

                $this->db->where('tblaccountledger.FY', $fy);

                $this->db->where('tblaccountledger.TType', 'D');

                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);

                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');

                $this->db->group_by('AccountID');

                $debit_bal = $this->db->get('tblaccountledger')->result_array();

				$balance = $debit_bal[0]['debit_bal'] - $credit_bal[0]['credit_bal'];

				$result[$i]['balance'] = $balance;

				$i++; 

			}    

			return $result;

		}

		

		public function load_data_items($data)

		{  

			$from_date = to_sql_date($data["from_date"]);

			$dates = to_sql_date($data["dates"]);

			$order_type = $data["order_type"];

			$state = $data["state"];

			$dist_type = $data["dist_type"];

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			if($selected_company == "1"){

				$GodownID = 'CSPL';

				}else if($selected_company == "2"){

				$GodownID = 'CFF';

				}else if($selected_company == "3"){

				$GodownID = 'CBUPL';

			}

			

			if($order_type == "all"){

				$sql1 = ''.db_prefix().'ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND OrderStatus IN("C","O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 

				}if($order_type == "O"){

				$sql1 = ''.db_prefix().'ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND OrderStatus IN("O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 

			}

			if($order_type == "C"){

				$sql1 = ''.db_prefix().'ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND OrderStatus IN("C") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 

			}

			if (empty($state)) {

				

				}else {

				$sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.state = "'.$state.'" AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';

			}

			

			if (empty($dist_type)) {

				

				}else {

				$sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.DistributorType = '.$dist_type.' AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';

			}

			

			//$sql1 .= ' AND  '.db_prefix().'stockmaster.FY = "'.$fy.'"';

			$sql1 .= ' AND '.db_prefix().'history.PlantID = '.$selected_company.' GROUP BY '.db_prefix().'history.ItemID,'.db_prefix().'history.CaseQty';

			$sql1 .= '  ORDER BY '.db_prefix().'items.SubGrpID1';

			

			$sql ='SELECT SUM('.db_prefix().'history.OrderAmt) AS OrderAmt,SUM(IFNULL('.db_prefix().'history.eOrderQty, '.db_prefix().'history.OrderQty)) AS OrderQty,SUM('.db_prefix().'history.NetOrderAmt) AS NetOrderAmt,'.db_prefix().'history.ItemID AS Item_code,CaseQty,

			'.db_prefix().'stockmaster.OQty,'.db_prefix().'items.description,'.db_prefix().'history.CaseQty,

			(SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE '.db_prefix().'items.tax = '.db_prefix().'taxes.id) as taxName,

			COALESCE(ROUND((SELECT tblrecipe.qty FROM tblrecipe WHERE tblitems.item_code = tblrecipe.item_code 

            AND tblrecipe.status = "Y"), 2), "NA") AS BowlQty

			FROM '.db_prefix().'history 

			INNER JOIN '.db_prefix().'ordermaster ON '.db_prefix().'history.OrderID = '.db_prefix().'ordermaster.OrderID

			INNER JOIN '.db_prefix().'items ON '.db_prefix().'history.ItemID = '.db_prefix().'items.item_code AND '.db_prefix().'history.PlantID = '.db_prefix().'items.PlantID 

			LEFT JOIN '.db_prefix().'stockmaster ON '.db_prefix().'history.ItemID = '.db_prefix().'stockmaster.ItemID AND '.db_prefix().'history.PlantID = '.db_prefix().'stockmaster.PlantID AND '.db_prefix().'history.FY = '.db_prefix().'stockmaster.FY AND tblstockmaster.GodownID = "'.$GodownID.'" AND tblstockmaster.cnfid = "1" 

			WHERE '.$sql1;

			$result = $this->db->query($sql)->result_array();

			$itemIds = array();

			foreach ($result as $key => $value) {

				array_push($itemIds, $value["Item_code"]);

			}

			$from_date = '20'.$fy.'-04-01 00:00:00';

			$this->db->select('ItemID,TType,TType2,CaseQty,SUM(BilledQty) AS BilledQty');

			$this->db->from(db_prefix() .'history');

			$this->db->where(db_prefix() .'history.PlantID', $selected_company);

			$this->db->where(db_prefix() .'history.FY', $fy);

			$this->db->where(db_prefix() .'history.GodownID', $GodownID);

			$this->db->where_in(db_prefix() .'history.ItemID', $itemIds);

			$this->db->where(db_prefix() .'history.TransDate2 BETWEEN "'. $from_date. '" AND "'. $dates. ' 23:59:59" ');

			$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);

			$this->db->group_by('ItemID,TType,TType2');

			$StockData = $this->db->get()->result_array();

			$i = 0;

			foreach ($result as $key1 => $value1) {

				$PQty = 0;

				$PRQty = 0;

				$IQty = 0;

				$PRDQty = 0;

				$SQty = 0;

				$SRTQty = 0;

				$AQty = 0;

				$GIQty = 0;

				$GOQty = 0;

				

				foreach ($StockData as $key2 => $value2) {

					if($value1["Item_code"] == $value2["ItemID"]){

						

						if($value2['TType'] == 'P'){

							$PQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'N'){

							$PRQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'A' && $value2['TType2'] == 'Issue'){

							$IQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'B'){

							$PRDQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'O' && $value2['TType2'] == 'Order'){

							$SQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'R' && $value2['TType2'] == 'Fresh'){

							$SRTQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'X'  && $value2['TType2'] == 'Free distribution'){

							$AQty += $value2['BilledQty'];

							}elseif($value2['TType'] == 'X'  && $value2['TType2'] == 'Free Distribution'){

							$AQty += $value2['BilledQty'];

							}elseif($value2['TType'] == 'X' && $value2['TType2'] == 'Stock Adjustment'){

							$AQty += $value2['BilledQty'];

							}elseif($value2['TType'] == 'X' && $value2['TType2'] == 'Stock Damaged'){

							$AQty += $value2['BilledQty'];

							}elseif($value2['TType'] == 'X' && $value2['TType2'] == 'Promotional Activity'){

							$AQty += $value2['BilledQty'];

							}elseif($value2['TType'] == 'T' && $value2['TType2'] == 'In'){

							$GIQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'T' && $value2['TType2'] == 'Out'){

							$GOQty = $value2['BilledQty'];

						}

						

					}

				}

				$stockQty = $value1['OQty'] + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty - $GOQty + $GIQty;

				$stockQtyInCase = $stockQty / $value1['CaseQty'];

				$result[$i]['StockBal'] = $stockQtyInCase;

				

				$i++;

			}

			return $result;

		}

		

		public function update_order_status($selected_ids,$selected_ids_remarks,$unselected_ids,$unselected_ids_remarks)

		{

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			$selected_ids_array = explode(',', $selected_ids);

			$selected_ids_remarks_array = explode(',', $selected_ids_remarks);

			$unselected_ids_array = explode(',', $unselected_ids);

			$unselected_ids_remarks_array = explode(',', $unselected_ids_remarks);

			

			$i = 0;

			

			$this->db->select('OrderID');

			$this->db->from(db_prefix() .'ordermaster');

			$this->db->where(db_prefix() .'ordermaster.PlantID', $selected_company);

			$this->db->where(db_prefix() .'ordermaster.FY', $fy);

			$this->db->where_in(db_prefix() .'ordermaster.OrderID', $selected_ids_array);

			$this->db->where(db_prefix() . 'ordermaster.SalesID IS NULL', NULL, FALSE);

			$PendingData = $this->db->get()->result_array();

			

			foreach($selected_ids_array as $id)

			{

				$data1 = array(

				"OrderStatus" => "C",

				"remark" => $selected_ids_remarks_array[$i],

				"CancelUserID" => $this->session->userdata('username'),

				"CancelTransDate" => date("Y-m-d H:i:s"),

				); 

				foreach($PendingData as $IDS){

					if($IDS["OrderID"] == $id){

						$this->db->where(db_prefix() . 'ordermaster.OrderID', $id);

						$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

						$this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 

						$this->db->update(db_prefix() . 'ordermaster', $data1);

						$i++;

                        $data1_history = array(

						"TType2" => "Cancel",

						"TType" => "C",

                        );

						// for history

                        $this->db->where_in(db_prefix() . 'history.OrderID', $id);

                        $this->db->where(db_prefix() . 'history.PlantID', $selected_company);

                        $this->db->LIKE(db_prefix() . 'history.FY', $fy); 

                        $this->db->update(db_prefix() . 'history', $data1_history);

					}

				} 

			}

			

			

			

			//return $selected_ids_array;

			$j=0;

			foreach($unselected_ids_array as $id)

			{

                $data2 = array(

				"OrderStatus" => "O",

				"remark" => $unselected_ids_remarks_array[$j],

                ); 

				$this->db->where(db_prefix() . 'ordermaster.OrderID', $id);

				$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

				$this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 

				$this->db->update(db_prefix() . 'ordermaster', $data2);

                $j++;

			}

			

            $data2_history = array(

			"TType2" => "Order",

            );

            // for history 

			$this->db->where_in(db_prefix() . 'history.OrderID', $unselected_ids_array);

			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

			$this->db->LIKE(db_prefix() . 'history.FY', $fy); 

			$this->db->update(db_prefix() . 'history', $data2_history);

			return $unselected_ids_array;

			

		}

		

		public function reset_order_status($selected_ids,$selected_ids_remarks,$unselected_ids,$unselected_ids_remarks)

		{

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			$selected_ids_array = explode(',', $selected_ids);

			//  $selected_ids_remarks_array = explode(',', $selected_ids_remarks);

			$unselected_ids_array = explode(',', $unselected_ids);

			//  $unselected_ids_remarks_array = explode(',', $unselected_ids_remarks);

			

			$selected_ids_remarks_array = explode(',', "");

			$unselected_ids_remarks_array = explode(',', "");

			// print_r($selected_ids_array); exit();

			//$selected_ids_remarks_array2 = array();

			/*foreach($selected_ids_remarks_array as $key => $link) 

				{ 

				if($link === ' ') 

				{ 

                unset($selected_ids_remarks_array[$key]); 

				}else{

                array_push($selected_ids_remarks_array2, $link);

				}

			} */

			$i = 0;

			foreach($selected_ids_array as $id)

			{

				$data1 = array(

				"OrderStatus" => "C",

				"remark" => $selected_ids_remarks_array2[$i],

				); 

				

				$this->db->where(db_prefix() . 'ordermaster.OrderID', $id);

				$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

				$this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 

				$this->db->update(db_prefix() . 'ordermaster', $data1);

				//$aa= $this->db->last_query(); print($aa); //exit();

                $i++;

			}

			$j=0;

			foreach($unselected_ids_array as $id)

			{ 

				$data2 = array(

				"OrderStatus" => "O",

				"remark" => $unselected_ids_remarks_array[$j],

				); 

				

				$this->db->where(db_prefix() . 'ordermaster.OrderID', $id);

				$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

				$this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy); 

				$this->db->update(db_prefix() . 'ordermaster', $data2);

				//$aa= $this->db->last_query(); print($aa); 

                $j++;

			}

			// print_r($id); exit();

			return $unselected_ids_array;

			

		}

		

		public function load_data2($data)

		{  

			$from_date = to_sql_date($data["from_date"]);

			$dates = to_sql_date($data["dates"]);

			$order_type = $data["order_type"];

			$state = $data["state"];

			$dist_type = $data["dist_type"];

			$selected_ids = $data["selected_ids"];

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			

			if($order_type == "all"){

				$sql1 = 'tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND tblordermaster.OrderStatus IN("C","O") AND tblordermaster.ChallanID IS NULL AND tblordermaster.FY = '.$fy; 

				}if($order_type == "O"){

				$sql1 = 'tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND tblordermaster.OrderStatus IN("O") AND tblordermaster.ChallanID IS NULL AND tblordermaster.FY = '.$fy; 

			}

			if($order_type == "C"){

				$sql1 = 'tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND tblordermaster.OrderStatus IN("C") AND tblordermaster.ChallanID IS NULL AND tblordermaster.FY = '.$fy; 

			}

			if ($state){

				$sql1 .= ' AND tblclients.state = "'.$state.'"';

			}

			

			if ($dist_type){

				$sql1 .= ' AND tblclients.DistributorType = '.$dist_type.'';

			}

			

			if (empty($selected_ids)) {

				

				}else {

				$ids = explode(",",$selected_ids);   

				$sql1 .= ' AND tblordermaster.OrderID IN ("'.implode('","',$ids).'")';

			}

			

			$sql1 .= ' ORDER BY tblordermaster.Transdate DESC';

			

			/*$sql ='SELECT '.db_prefix().'ordermaster.*,IFNULL(BAL1,0.00) as bal1,IFNULL(BAL2,0.00) as bal2,IFNULL(BAL3,0.00) as bal3,IFNULL(BAL4,0.00) as bal4,IFNULL(BAL5,0.00) as bal5,IFNULL(BAL6,0.00) as bal6,IFNULL(BAL7,0.00) as bal7,IFNULL(BAL8,0.00) as bal8,IFNULL(BAL9,0.00) as bal9,IFNULL(BAL10,0.00) as bal10,IFNULL(BAL11,0.00) as bal11,IFNULL(BAL12,0.00) as bal12,IFNULL(BAL13,0.00) as bal13,

				(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName,

				(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID2 AND '.db_prefix().'clients.PlantID = '.$selected_company.') as ShipToAccountName,

				(SELECT GROUP_CONCAT(StationName SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as StationName,

				(SELECT GROUP_CONCAT(CONCAT ( firstname," ",lastname ) SEPARATOR ",") FROM '.db_prefix().'customer_admins 

				LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_admins.staff_id 

				WHERE '.db_prefix().'customer_admins.customer_id = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'customer_admins.company_id = '.$selected_company.') as SOID,

				(SELECT '.db_prefix().'xx_statelist.short_name

				FROM  '.db_prefix().'xx_statelist

				INNER JOIN '.db_prefix().'clients 

				ON '.db_prefix().'xx_statelist.short_name = '.db_prefix().'clients.state

				WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as StateName,

				(SELECT '.db_prefix().'customers_groups.name

				FROM  '.db_prefix().'customers_groups

				INNER JOIN '.db_prefix().'clients 

				ON '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType

				WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as dist_Type

				FROM '.db_prefix().'ordermaster 

				INNER JOIN '.db_prefix().'accountbalances 

				ON '.db_prefix().'ordermaster.AccountID = '.db_prefix().'accountbalances.AccountID AND '.db_prefix().'ordermaster.PlantID = '.db_prefix().'accountbalances.PlantID AND '.db_prefix().'ordermaster.FY = '.db_prefix().'accountbalances.FY

			WHERE '.$sql1;*/

			

			$sql ='SELECT tblordermaster.*,IFNULL(tblaccountbalances.BAL1,0.00) as bal1,tblclients.company AS AccountName,tblclients.StationName,tblclients.state AS StateName,

			ShipAddrs.company AS ShipToAccountName,CONCAT(tblstaff.firstname," ",tblstaff.lastname) AS SOID,tblcustomers_groups.name AS dist_Type,

			IFNULL(CONCAT(CancelStaff.firstname," ",CancelStaff.lastname),"") AS CancelStaffName,

			IFNULL(CONCAT(CreateStaff.firstname," ",CreateStaff.lastname),"") AS CreateStaffName

			FROM '.db_prefix().'ordermaster 

			INNER JOIN tblclients ON tblclients.AccountID = tblordermaster.AccountID AND tblclients.PlantID = tblordermaster.PlantID

			LEFT JOIN tblcustomers_groups ON tblcustomers_groups.id = tblclients.DistributorType

			LEFT JOIN tblclients AS ShipAddrs ON ShipAddrs.AccountID = tblordermaster.AccountID2 AND ShipAddrs.PlantID = tblordermaster.PlantID

			LEFT JOIN tblcustomer_admins ON tblcustomer_admins.customer_id = tblordermaster.AccountID AND tblcustomer_admins.company_id = tblordermaster.PlantID

			LEFT JOIN tblstaff ON tblstaff.staffid = tblcustomer_admins.staff_id

			LEFT JOIN tblstaff AS CancelStaff ON CancelStaff.AccountID = tblordermaster.CancelUserID

			LEFT JOIN tblstaff AS CreateStaff ON CreateStaff.AccountID = tblordermaster.UserID

			LEFT JOIN tblaccountbalances 

			ON tblordermaster.AccountID = tblaccountbalances.AccountID AND tblordermaster.PlantID = tblaccountbalances.PlantID AND tblordermaster.FY = tblaccountbalances.FY

			WHERE '.$sql1;

			

			$result = $this->db->query($sql)->result_array();

			

			$i = 0;

			$fy_to = $fy + 1;

			$from_date = '20'.$fy.'-04-01';

			$to_date = '20'.$fy_to.'-03-31';

			foreach($result as $value){

				

				// credit crated

                $this->db->select('sum(Amount) as credit_bal,AccountID');

                $this->db->where('tblaccountledger.PlantID', $selected_company);

                $this->db->where('tblaccountledger.FY', $fy);

                $this->db->where('tblaccountledger.TType', 'C');

                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);

                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');

                $this->db->group_by('AccountID');

                $credit_bal = $this->db->get('tblaccountledger')->result_array();

                

				// Debit crated

                $this->db->select('sum(Amount) as debit_bal,AccountID');

                $this->db->where('tblaccountledger.PlantID', $selected_company);

                $this->db->where('tblaccountledger.FY', $fy);

                $this->db->where('tblaccountledger.TType', 'D');

                $this->db->where('tblaccountledger.AccountID', $value["AccountID"]);

                $this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');

                $this->db->group_by('AccountID');

                $debit_bal = $this->db->get('tblaccountledger')->result_array();

				$balance = $debit_bal[0]['debit_bal'] - $credit_bal[0]['credit_bal'];

				$result[$i]['balance'] = $balance;

				

				$i++; 

				

			}  

			return $result;

		}

		

		public function load_data_items2($data)

		{  

			$from_date = to_sql_date($data["from_date"]);

			$dates = to_sql_date($data["dates"]);

			$order_type = $data["order_type"];

			$state = $data["state"];

			$dist_type = $data["dist_type"];

			$selected_ids = $data["selected_ids"];

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			

			if($order_type == "all"){

				$sql1 = ''.db_prefix().'ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND OrderStatus IN("C","O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 

				}if($order_type == "O"){

				$sql1 = ''.db_prefix().'ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND OrderStatus IN("O") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 

			}

			if($order_type == "C"){

				$sql1 = ''.db_prefix().'ordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$dates.' 23:59:00" AND OrderStatus IN("C") AND '.db_prefix().'ordermaster.ChallanID IS null AND '.db_prefix().'ordermaster.FY = '.$fy; 

			}

			if (empty($state)) {

				

				}else {

				$sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.state = "'.$state.'" AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';

			}

			

			if (empty($dist_type)) {

				

				}else {

				$sql1 .= ' AND '.db_prefix().'ordermaster.AccountID = (SELECT '.db_prefix().'clients.AccountID FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.DistributorType = '.$dist_type.' AND '.db_prefix().'clients.AccountID = '.db_prefix().'ordermaster.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.')';

			}

			

			if (empty($selected_ids)) {

				

				}else {

				$ids = explode(",",$selected_ids);   

				$sql1 .= ' AND '.db_prefix().'history.OrderID IN ("'.implode('","',$ids).'")';

			}

			

			$sql1 .= ' GROUP BY '.db_prefix().'history.ItemID';

			$sql1 .= ' ORDER BY '.db_prefix().'items.SubGrpID1';

			

			$sql ='SELECT SUM('.db_prefix().'history.OrderAmt) AS OrderAmt,SUM(IFNULL('.db_prefix().'history.eOrderQty, '.db_prefix().'history.OrderQty)) AS OrderQty,SUM('.db_prefix().'history.NetOrderAmt) AS NetOrderAmt,

			'.db_prefix().'stockmaster.OQty,'.db_prefix().'history.ItemID AS Item_code,CaseQty,'.db_prefix().'items.description,

			(SELECT GROUP_CONCAT(taxrate SEPARATOR ",") FROM '.db_prefix().'taxes WHERE '.db_prefix().'items.tax = '.db_prefix().'taxes.id) as taxName,

			COALESCE(ROUND((SELECT tblrecipe.qty / OrderQty FROM tblrecipe WHERE tblitems.item_code = tblrecipe.item_code 

            AND tblrecipe.status = "Y"), 2), "NA") AS BowlQty

			FROM '.db_prefix().'history 

			INNER JOIN '.db_prefix().'ordermaster ON '.db_prefix().'history.OrderID = '.db_prefix().'ordermaster.OrderID

			INNER JOIN '.db_prefix().'items ON '.db_prefix().'history.ItemID = '.db_prefix().'items.item_code AND '.db_prefix().'history.PlantID = '.db_prefix().'items.PlantID 

			LEFT JOIN '.db_prefix().'stockmaster ON '.db_prefix().'history.ItemID = '.db_prefix().'stockmaster.ItemID AND '.db_prefix().'history.PlantID = '.db_prefix().'stockmaster.PlantID AND '.db_prefix().'history.FY = '.db_prefix().'stockmaster.FY 

			WHERE '.$sql1;

			$result = $this->db->query($sql)->result_array();

			

			$itemIds = array();

			foreach ($result as $key => $value) {

				array_push($itemIds, $value["Item_code"]);

			}

			$from_date = '20'.$fy.'-04-01 00:00:00';

			$this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');

			$this->db->from(db_prefix() .'history');

			$this->db->where(db_prefix() .'history.PlantID', $selected_company);

			$this->db->where(db_prefix() .'history.FY', $fy);

			$this->db->where_in(db_prefix() .'history.ItemID', $itemIds);

			$this->db->where(db_prefix() .'history.TransDate2 BETWEEN "'. $from_date. '" AND "'. $dates. ' 23:59:00" ');

			$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);

			$this->db->group_by('ItemID,TType,TType2');

			$StockData = $this->db->get()->result_array();

			$i = 0;

			foreach ($result as $key1 => $value1) {

				$PQty = 0;

                $PRQty = 0;

                $IQty = 0;

                $PRDQty = 0;

                $SQty = 0;

                $SRTQty = 0;

                $AQty = 0;

                $GIQty = 0;

                $GOQty = 0;

				foreach ($StockData as $key2 => $value2) {

					if($value1["Item_code"] == $value2["ItemID"]){

						

						if($value2['TType'] == 'P'){

							$PQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'N'){

							$PRQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'A'){

							$IQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'B'){

							$PRDQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'O' && $value2['TType2'] == 'Order'){

							$SQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'R' && $value2['TType2'] == 'Fresh'){

							$SRTQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'X'){

							$AQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'T' && $value2['TType2'] == 'In'){

							$GIQty = $value2['BilledQty'];

							}elseif($value2['TType'] == 'T' && $value2['TType2'] == 'Out'){

							$GOQty = $value2['BilledQty'];

						}

					}

				}

				$stockQty = $value1['OQty'] + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty - $GOQty + $GIQty;

				$stockQtyInCase = $stockQty / $value1['CaseQty'];

				$result[$i]['StockBal'] = $stockQtyInCase;

				

				$i++;

			}

			return $result;

		}

		

		public function getorder_by_challan($id = '', $where = [])

		{

			$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');

			$this->db->from(db_prefix() . 'ordermaster');

			$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');

			//$this->db->where($where);

			

			

			//$this->db->order_by('YEAR(date)', 'desc');

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

			$this->db->LIKE(db_prefix() . 'ordermaster.FY', $fy);

			$this->db->where(db_prefix() . 'ordermaster.ChallanID', $id);

			//$this->db->or_where(db_prefix() . 'ordermaster.ChallanID', "");

			return $this->db->get()->result_array();

		}

		

		public function get_order_items($orderID,$PlantID,$FY)

		{

			if($PlantID == "1"){

				$GodownID = 'CSPL';

				}else if($PlantID == "2"){

				$GodownID = 'CFF';

				}else if($PlantID == "3"){

				$GodownID = 'CBUPL';

			}

			

			$this->db->select(db_prefix() .'history.*,'.db_prefix() .'stockmaster.OQty,'.db_prefix() . 'items.description,'.db_prefix() . 'items.hsn_code');

			//$this->db->select(db_prefix() .'history.*');

			$this->db->from(db_prefix() .'history');

			$this->db->join(db_prefix() .'items', db_prefix() .'items.item_code = '.db_prefix() .'history.ItemID AND '.db_prefix() .'items.PlantID = '.db_prefix() .'history.PlantID');

			$this->db->join(db_prefix() .'stockmaster', db_prefix() .'stockmaster.ItemID = '.db_prefix() .'history.ItemID AND '.db_prefix() .'stockmaster.PlantID = '.db_prefix() .'history.PlantID AND '.db_prefix() .'stockmaster.FY = '.db_prefix() .'history.FY AND '.db_prefix() .'stockmaster.cnfid = "1" AND '.db_prefix() .'stockmaster.GodownID = "'.$GodownID.'"','LEFT');

			$this->db->where(db_prefix() .'history.OrderID', $orderID);

			$this->db->where(db_prefix() .'history.TType', 'O');

			$this->db->where(db_prefix() .'history.TType2', 'Order');

			$this->db->where(db_prefix() .'history.PlantID', $PlantID);

			//$this->db->where(db_prefix() .'history.NetOrderAmt !=', '0.00');

			//$this->db->where(db_prefix() .'items.PlantID', $PlantID);

			$this->db->where(db_prefix() .'history.FY', $FY);

			//$this->db->where(db_prefix() .'stockmaster.FY', $FY);

			//$this->db->where(db_prefix() .'stockmaster.PlantID', $PlantID);

			return $this->db->get()->result_array();

		}

		public function get_order_items_free_distribution($orderID,$PlantID,$FY)

		{

			if($PlantID == "1"){

				$GodownID = 'CSPL';

				}else if($PlantID == "2"){

				$GodownID = 'CFF';

				}else if($PlantID == "3"){

				$GodownID = 'CBUPL';

			}

			

			$this->db->select(db_prefix() .'history.*,'.db_prefix() .'stockmaster.OQty,'.db_prefix() . 'items.description,'.db_prefix() . 'items.hsn_code');

			$this->db->from(db_prefix() .'history');

			$this->db->join(db_prefix() .'items', db_prefix() .'items.item_code = '.db_prefix() .'history.ItemID AND '.db_prefix() .'items.PlantID = '.db_prefix() .'history.PlantID');

			$this->db->join(db_prefix() .'stockmaster', db_prefix() .'stockmaster.ItemID = '.db_prefix() .'history.ItemID AND '.db_prefix() .'stockmaster.PlantID = '.db_prefix() .'history.PlantID AND '.db_prefix() .'stockmaster.FY = '.db_prefix() .'history.FY AND '.db_prefix() .'stockmaster.cnfid = "1" AND '.db_prefix() .'stockmaster.GodownID = "'.$GodownID.'"','LEFT');

			$this->db->where(db_prefix() .'history.OrderID', $orderID);

			$this->db->where(db_prefix() .'history.TType', 'O');

			$this->db->where(db_prefix() .'history.TType2', 'Free Distribution');

			$this->db->where(db_prefix() .'history.PlantID', $PlantID);

			//$this->db->where(db_prefix() .'history.NetOrderAmt !=', '0.00');

			//$this->db->where(db_prefix() .'items.PlantID', $PlantID);

			$this->db->where(db_prefix() .'history.FY', $FY);

			//$this->db->where(db_prefix() .'stockmaster.FY', $FY);

			//$this->db->where(db_prefix() .'stockmaster.PlantID', $PlantID);

			return $this->db->get()->result_array();

		}

		public function GetItemStock($orderID,$PlantID,$FY)

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

			

			$this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');

			$this->db->from(db_prefix() .'history');

			$this->db->where(db_prefix() .'history.PlantID', $selected_company);

			$this->db->where(db_prefix() .'history.GodownID', $GodownID);

			$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);

			$this->db->where(db_prefix() .'history.FY', $fy);

			$this->db->group_by('ItemID,TType,TType2');

			return $this->db->get()->result_array();

		}

		

		public function get_state_list()

		{

			$this->db->order_by('state_name', 'ASC');

			return $this->db->get(db_prefix() . 'xx_statelist')->result_array();

		}

		

		public function get_selected_company_details()

		{

			$selected_company = $this->session->userdata('root_company');

			$selected_year = $this->session->userdata('finacial_year');

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $selected_year);

			return $this->db->get(db_prefix() . 'setup')->row();

		}

		

		public function get_distributor_type()

		{

			$selected_company = $this->session->userdata('root_company');

			$this->db->where('PlantID', $selected_company);

			

			return $this->db->get(db_prefix() . 'customers_groups')->result_array();

		}

		//==================== Station Master List =====================================

		public function StationMasterList()

		{

			$this->db->select('tblStationMaster.*');

			return $this->db->get(db_prefix() . 'StationMaster')->result_array();

		}

		

		/* public function get_accbal($AccountID,$PlantID,$FY)

			{

			$this->db->where('AccountID', $AccountID);

			$this->db->where('PlantID', $PlantID);

			$this->db->where('FY', $FY);

			

			return $this->db->get(db_prefix() . 'accountbalances')->row();

		}*/

		

		

		

		public function get_accbal($AccountID,$PlantID,$FY){

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			$Obal = 0;

			

			$sql = '';

			$sql .= 'SELECT SUM(Amount) as dramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';

			$sql .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'" AND tblaccountledger.TType = "D"';

			$result1 = $this->db->query($sql)->row();

			

			$sql2 = '';

			$sql2 .= 'SELECT SUM(Amount) as cramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';

			$sql2 .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'" AND tblaccountledger.TType = "C"';

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

			return $bal;

		}

		public function SaleDetails($SalesID,$PlantID,$FY)

		{

			$this->db->select('SalesID,irn,ackno,Transdate');

			$this->db->where('SalesID', $SalesID);

			$this->db->where('PlantID', $PlantID);

			$this->db->LIKE('FY', $FY);

			return $this->db->get(db_prefix() . 'salesmaster')->row();

		}

		public function ChallanDetails($ChallanID,$PlantID,$FY)

		{

			$this->db->select('ChallanID,GetPassTime,gatepasstime,Gatepassuserid,ChallanAmt');

			$this->db->where('ChallanID', $ChallanID);

			$this->db->where('PlantID', $PlantID);

			$this->db->LIKE('FY', $FY);

			return $this->db->get(db_prefix() . 'challanmaster')->row();

		}

		public function get_last_bill_on($AccountID,$PlantID,$FY)

		{

			$this->db->where('AccountID', $AccountID);

			$this->db->where('PlantID', $PlantID);

			$this->db->where('FY', $FY);

			$this->db->where('TType', 'D');

			$this->db->where('PassedFrom', 'SALE');

			$this->db->order_by('Transdate', 'DESC');

			return $this->db->get(db_prefix() . 'accountledger')->row();

		}

		

		public function get_last_deposit_on($AccountID,$PlantID,$FY)

		{

			$this->db->where('AccountID', $AccountID);

			$this->db->where('PlantID', $PlantID);

			$this->db->where('FY', $FY);

			$this->db->where('TType', 'C');

			$this->db->order_by('Transdate', 'DESC');

			return $this->db->get(db_prefix() . 'accountledger')->row();

		}

		//========================== Get Shipping Address ==============================

		public function GetShippingAddress($Customer_id = "",$ShippingID = "")

		{

			$this->db->select('tblclients.AccountID,tblclients.vat,tblclientwiseshippingdata.id,tblclientwiseshippingdata.ShippingState,tblclientwiseshippingdata.ShippingCity,

			tblclientwiseshippingdata.ShippingAdrees,tblclientwiseshippingdata.ShippingPin,tblxx_statelist.state_name,tblxx_citylist.city_name');

			$this->db->join(db_prefix() .'clientwiseshippingdata', db_prefix() .'clientwiseshippingdata.AccountID = '.db_prefix() .'clients.AccountID',"LEFT");

			$this->db->join(db_prefix() .'xx_statelist', db_prefix() .'xx_statelist.short_name = '.db_prefix() .'clientwiseshippingdata.ShippingState',"LEFT");

			$this->db->join(db_prefix() .'xx_citylist', db_prefix() .'xx_citylist.id = '.db_prefix() .'clientwiseshippingdata.ShippingCity',"LEFT");

			if($ShippingID){

				$this->db->where('tblclientwiseshippingdata.id', $ShippingID);

				return $this->db->get(db_prefix() . 'clients')->row();

				}else{

				$this->db->where('tblclients.AccountID', $Customer_id);

				$this->db->order_by('tblclientwiseshippingdata.IsBilling', 'DESC');

				return $this->db->get(db_prefix() . 'clients')->result_array();

			}

		}

		

		public function check_invoice_generate($id)

		{

			$this->db->where('order_id', $id);

			

			return $this->db->get(db_prefix() . 'invoices')->row();

		}

		

		public function mark_as_cancelled($id)

		{

			$isDraft = $this->is_draft($id);

			

			$this->db->where('id', $id);

			$this->db->update(db_prefix() . 'invoices', [

            'status' => self::STATUS_CANCELLED,

            'sent'   => 1,

			]);

			

			if ($this->db->affected_rows() > 0) {

				if ($isDraft) {

					$this->change_invoice_number_when_status_draft($id);

				}

				

				$this->log_invoice_activity($id, 'invoice_activity_marked_as_cancelled');

				

				hooks()->do_action('invoice_marked_as_cancelled', $id);

				

				return true;

			}

			

			return false;

		}

		

		public function unmark_as_cancelled($id)

		{

			$this->db->where('id', $id);

			$this->db->update(db_prefix() . 'invoices', [

            'status' => self::STATUS_UNPAID,

			]);

			

			if ($this->db->affected_rows() > 0) {

				$this->log_invoice_activity($id, 'invoice_activity_unmarked_as_cancelled');

				

				return true;

			}

			

			return false;

		}

		public function remark_update($data)

		{

			$itemid = $data['itemid'];

			unset($data['itemid']);

			

			$this->db->where('OrderID', $itemid);

			$this->db->update(db_prefix() . 'ordermaster', $data);

			

			if ($this->db->affected_rows() > 0) {

				$this->log_invoice_activity($itemid, 'remark updated');

				

				return true;

			}

			

			return false;

		}

		

		/**

			* Get this invoice generated recurring invoices

			* @since  Version 1.0.1

			* @param  mixed $id main invoice id

			* @return array

		*/

		public function get_invoice_recurring_invoices($id)

		{

			$this->db->select('id');

			$this->db->where('is_recurring_from', $id);

			$invoices           = $this->db->get(db_prefix() . 'invoices')->result_array();

			$recurring_invoices = [];

			

			foreach ($invoices as $invoice) {

				$recurring_invoices[] = $this->get($invoice['id']);

			}

			

			return $recurring_invoices;

		}

		

		/**

			* Get invoice total from all statuses

			* @since  Version 1.0.2

			* @param  mixed $data $_POST data

			* @return array

		*/

		public function get_invoices_total($data)

		{

			$this->load->model('currencies_model');

			

			if (isset($data['currency'])) {

				$currencyid = $data['currency'];

				} elseif (isset($data['customer_id']) && $data['customer_id'] != '') {

				$currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);

				if ($currencyid == 0) {

					$currencyid = $this->currencies_model->get_base_currency()->id;

				}

				} elseif (isset($data['project_id']) && $data['project_id'] != '') {

				$this->load->model('projects_model');

				$currencyid = $this->projects_model->get_currency($data['project_id'])->id;

				} else {

				$currencyid = $this->currencies_model->get_base_currency()->id;

			}

			

			$result            = [];

			$result['due']     = [];

			$result['paid']    = [];

			$result['overdue'] = [];

			

			$has_permission_view                = has_permission('invoices', '', 'view');

			$has_permission_view_own            = has_permission('invoices', '', 'view_own');

			$allow_staff_view_invoices_assigned = get_option('allow_staff_view_invoices_assigned');

			$noPermissionsQuery                 = get_invoices_where_sql_for_staff(get_staff_user_id());

			

			for ($i = 1; $i <= 3; $i++) {

				$select = 'id,total';

				if ($i == 1) {

					$select .= ', (SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id)) as outstanding';

					} elseif ($i == 2) {

					$select .= ',(SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid=' . db_prefix() . 'invoices.id) as total_paid';

				}

				$this->db->select($select);

				$this->db->from(db_prefix() . 'invoices');

				$this->db->where('currency', $currencyid);

				// Exclude cancelled invoices

				$this->db->where('status !=', self::STATUS_CANCELLED);

				// Exclude draft

				$this->db->where('status !=', self::STATUS_DRAFT);

				

				if (isset($data['project_id']) && $data['project_id'] != '') {

					$this->db->where('project_id', $data['project_id']);

					} elseif (isset($data['customer_id']) && $data['customer_id'] != '') {

					$this->db->where('clientid', $data['customer_id']);

				}

				

				if ($i == 3) {

					$this->db->where('status', self::STATUS_OVERDUE);

					} elseif ($i == 1) {

					$this->db->where('status !=', self::STATUS_PAID);

				}

				

				if (isset($data['years']) && count($data['years']) > 0) {

					$this->db->where_in('YEAR(date)', $data['years']);

					} else {

					$this->db->where('YEAR(date)', date('Y'));

				}

				

				if (!$has_permission_view) {

					$whereUser = $noPermissionsQuery;

					$this->db->where('(' . $whereUser . ')');

				}

				

				$invoices = $this->db->get()->result_array();

				

				foreach ($invoices as $invoice) {

					if ($i == 1) {

						$result['due'][] = $invoice['outstanding'];

						} elseif ($i == 2) {

						$result['paid'][] = $invoice['total_paid'];

						} elseif ($i == 3) {

						$result['overdue'][] = $invoice['total'];

					}

				}

			}

			$currency             = get_currency($currencyid);

			$result['due']        = array_sum($result['due']);

			$result['paid']       = array_sum($result['paid']);

			$result['overdue']    = array_sum($result['overdue']);

			$result['currency']   = $currency;

			$result['currencyid'] = $currencyid;

			

			return $result;

		}

//================== Add New Order =============================================

		public function AddNewOrder($data, $expense = false)

		{

			$order_data_new = array();

			$selected_company = $this->session->userdata('root_company');

			$finacial_year = $this->session->userdata('finacial_year');

			$order_data_new["PlantID"] = $selected_company;

			$order_data_new["FY"] = $finacial_year;

			$client = $this->GetClientData($data["clientid"]);

			$rootcompany = $this->clients_model->get_rootcompany();

			if($selected_company == 1){

				$next_order_number = get_option('next_order_number_for_gf');

			}/*elseif($selected_company == 2){

				$next_order_number = get_option('next_order_number_for_cff');

				}elseif($selected_company == 3){

				$next_order_number = get_option('next_order_number_for_cbu');

			}*/

			$new_orderID = "ORD".$finacial_year.str_pad($next_order_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);

			$order_data_new["OrderID"] = $new_orderID;

			$order_data_new["AccountID"] = $data["clientid"];

			if($data["act_gst"] !== NULL && $data["act_gst"] !== ''){

				$order_data_new["GSTNO"] = $data["act_gst"];

			}

			

			$order_data_new["Crates"] = $data["total_crates"];

			$order_data_new["Cases"] = $data["total_cases"];

			$order_data_new["buyer_ord_no"] = $data["buyer_ord_no"];

			$order_data_new["buyer_ord_date"] = to_sql_date($data['buyer_ord_date'])." ".date("H:i:s");

			$order_data_new["OrderStatus"] = 'O';

			if($client->cd == '' || $client->cd == null)

			{

				$cd = "N";

			}else

			{

				$cd = $client->cd;

			}

			$order_data_new["cd_applicable"] = $cd;

			$order_data_new["OrderType"] = $data["taxes1"];

			$order_data_new["order_type"] = $data["order_type"];

			$order_data_new["AccountID2"] = $data["ShipToParty"];

			if($data["ship_to_act_gst"] !== NULL && $data["ship_to_act_gst"] !== ''){

				$order_data_new["Gstin2"] = $data["ship_to_act_gst"];

			}

			$order_data_new["ShipTo"] = $data["Ship_to"];

			

			$order_data_new["Transdate"] = to_sql_date($data['date1'])." ".date("H:i:s");

			$order_data_new["Dispatchdate"] = to_sql_date(substr($data['DispatchDate'],0,10))." ".substr($data['DispatchDate'],11,5).":00";

			$order_data_new['UserID'] = $this->session->userdata('username');

			$order_data_new['cnfid'] = 1;

			$client_state = $data["customer_state_id"];

			//echo $client_state;

			$Transdate = to_sql_date($data['date1'])." ".date("H:i:s");

			

			$items = $data['newitems'];

			

			$clientdata = $this->GetClientData($data["clientid"]);

			

			$ExistingItemsIDs = array();

			foreach($items as $exitemkey => $exitemvalue)

			{

				array_push($ExistingItemsIDs,$exitemvalue['item_code1']);

			}

			$GetMultipleScheme = $this->GetMultipleSchemeData($clientdata->DistributorType,$clientdata->state,$data['date1'],$ExistingItemsIDs);

			

			$i = 0;

			foreach ($GetMultipleScheme as $Scheme) {

				${'TotalBilledQty' . $i} = 0; 

				$i++;

			}

			

			$SingleFreeItem = array();

			$SingleDiscItem = array();

			foreach ($items as $exkey => $exvalue) 

			{

				$Item = $exvalue['item_code1'];

				$i = 0;

				$IsApplyMultipleScheme = 0;

				foreach($GetMultipleScheme as $Scheme){

					$SchemeItems = $Scheme["ItemID"];

					$SchemeItemsArr = explode(',',$SchemeItems);

					if(in_array($Item,$SchemeItemsArr)){

						${'TotalBilledQty' . $i} += $exvalue["qty"];

						$IsApplyMultipleScheme++;

						break;

					}

					$i++;

				}

				

				$IsFreeItem = 0;

				if($IsApplyMultipleScheme == 0){

					$ChkSingleScheme = $this->GetSchemeData($clientdata->DistributorType,$clientdata->state,$data["transdate"],$exvalue["item_code1"]);

					if(!empty($ChkSingleScheme)){

						foreach($ChkSingleScheme as $each){

							if($exvalue["qty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

								$Disc_pkt = floor($exvalue["qty"] / $each['SlabQty']) * $each['Disc_pkt'];

								$SingleFreeItem[$each['FreeItemID']] = $Disc_pkt;

								$IsFreeItem++;

								break;

							}

						}

					}

				}

				if($IsApplyMultipleScheme == 0 && $IsFreeItem == 0){

					$getDiscscheme = $this->GetDiscSchemeData($clientdata->DistributorType,$clientdata->state,$data['date1'],$exvalue["item_code1"]);

					if(!empty($getDiscscheme)){

						foreach($getDiscscheme as $each){

							if($exvalue["qty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

								$SingleDiscItem[$exvalue["item_code1"]] = $each["DiscPerc"];

								break;

							}

						}

					}

				}

			}

			

			// echo "<pre>";print_r($SingleFreeItem);die;

			

			$i = 0;

			foreach($GetMultipleScheme as $Scheme){

				$GetMultipleScheme[$i]["TotalBilledQty"] = ${'TotalBilledQty' . $i};

				$i++;

			}

			

			

			// echo "<pre>";print_r($GetMultipleScheme);die;

			if($order_data_new["OrderType"] == "TaxItems" || $order_data_new["OrderType"] == "NonTaxItems"){

				if($this->db->insert(db_prefix() . 'ordermaster', $order_data_new)){

					$this->increment_next_number();

					$TotalSaleAmt = 0;

					$TotalDiscAmt = 0;

					$TotalCGSTAmt = 0;

					$TotalSGSTAmt = 0;

					$TotalIGSTAmt = 0;

					$TotalDiscOnSaleAmt = 0;

					foreach ($items as $key => $item) 

					{

						$SaleAmt =  $item["qty"] * $item["rate"]; // $item["pack_qty"] * Add If Needed

						$TotalSaleAmt += $SaleAmt;

						$Salerate =  ($item["rate"] + ($item["rate"] * ($item["taxrate1"] /100)));

						

						

						// $getDiscscheme = $this->GetDiscSchemeData($clientdata->DistributorType,$clientdata->state,$data['date1'],$item["item_code1"]);

						// if(!empty($getDiscscheme)){

						// foreach($getDiscscheme as $each){

						// if($item["qty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

						// $item["dis"] = $each['DiscPerc'];

						// $item["dis_amt1"] = ($SaleAmt * $item["dis"] / 100);

						// }

						// }

						// }

						

						if (array_key_exists($item["item_code1"], $SingleDiscItem)) {

							$item["dis"] = $SingleDiscItem[$item["item_code1"]];

							$item["dis_amt1"] = ($exSaleAmt * $item["dis"] / 100);

						}

						

						$TaxableAmt = $SaleAmt - $item["dis_amt1"];

						$TotalDiscAmt += $item["dis_amt1"];

						

						$item_data = array();

						$item_data["PlantID"] = $selected_company;

						$item_data["FY"] = $this->session->userdata('finacial_year');

						$item_data["OrderID"] = $new_orderID;

						$item_data["TType"] = "O";

						$item_data["TType2"] = 'Order';

						$item_data["AccountID"] = $data["clientid"];

						$item_data["ItemID"] = $item["item_code1"];

						$item_data["BasicRate"] = $item["rate"];

						$item_data["SuppliedIn"] = $item["items_cs_cr"];

						$item_data["OrderQty"] = $item["qty"]; // $item["pack_qty"] * Add If Needed

						$item_data["eOrderQty"] = $item["qty"]; // $item["pack_qty"] * Add If Needed

						$item_data["SaleRate"] = $Salerate;

						$item_data["DiscPerc"] = $item["dis"];

						$item_data["DiscAmt"] = $item["dis_amt1"];

						$CGST = 0;

						$SGST = 0;

						$IGST = 0;

						$CGSTAmt = 0;

						$SGSTAmt = 0;

						$IGSTAmt = 0;

						if($client_state == $rootcompany[0]["state_code"]){

							$CGST = ($item["taxrate1"] /2);

							$SGST = ($item["taxrate1"] /2);

							$CGSTAmt = $TaxableAmt * ($CGST /100);

							$SGSTAmt = $TaxableAmt * ($SGST /100);

							}else {

							$IGST = $item["taxrate1"];

							$IGSTAmt = $TaxableAmt * ($IGST /100);

						}

						$item_data["cgst"] = $CGST;

						$item_data["sgst"] = $SGST;

						$item_data["igst"] = $IGST;

						$item_data["cgstamt"] = $CGSTAmt;

						$item_data["sgstamt"] = $SGSTAmt;

						$item_data["igstamt"]= $IGSTAmt;

						$TotalCGSTAmt += $CGST;

						$TotalSGSTAmt += $SGST;

						$TotalIGSTAmt += $IGSTAmt;

						$NetOrderAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt + $IGSTAmt;

						$item_data["CaseQty"] = $item["pack_qty"];

						$item_data["OrderAmt"] = $SaleAmt;

						$item_data["NetOrderAmt"] = $NetOrderAmt;

						$item_data["Ordinalno"] = $item["order"];

						$item_data["UserID"] = $this->session->userdata('username');

						$item_data["TransDate"] = $Transdate;

						

						$this->db->insert(db_prefix().'history', $item_data);

						

					}

					

					foreach($GetMultipleScheme as $each){

						if($each["TotalBilledQty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

							$Disc_pkt = floor($each["TotalBilledQty"] / $each['SlabQty']) * $each['Disc_pkt'];

							

							$Item_data = $this->db->get_where('tblitems',array('item_code'=>$each['FreeItemID']))->row_array();

							if($Item_data['outst_supply_in'] == 'CS'){

								$CS_CR_QTY = $Item_data['case_qty'];

								}else{

								$CS_CR_QTY = $Item_data['crate_qty'];

							}

							

							$ItemRate =  $this->get_rate_master_data_by_id2($each['FreeItemID'],$clientdata->DistributorType,$clientdata->state);

							$Rate = $ItemRate->assigned_rate;

							$new_item_data = array(

							"PlantID" =>$selected_company,

							"FY" =>$this->session->userdata('finacial_year'),

							"OrderID" =>$new_orderID,

							"TType" =>"O",

							"TType2" =>'Free Distribution',

							"AccountID" =>$data["clientid"],

							"ItemID" =>$each['FreeItemID'],

							"BasicRate" =>$Rate,

							"SuppliedIn" =>$Item_data['outst_supply_in'],

							"OrderQty" => $Disc_pkt,

							"eOrderQty" => $Disc_pkt,

							"SaleRate" =>0,

							"DiscPerc" =>100,

							"DiscAmt" =>0,

							"cgst" =>0,

							"cgstamt" =>0,

							"sgst" =>0,

							"sgstamt" =>0,

							"igst" =>0,

							"igstamt" =>0,

							"CaseQty" =>$CS_CR_QTY,

							"OrderAmt" =>0,

							"NetOrderAmt" =>0,

							"UserID" =>$this->session->userdata('username'),

							"TransDate" =>$Transdate,

							);

							$this->db->insert(db_prefix() . 'history', $new_item_data);

						}

					}

					

					foreach($SingleFreeItem as $ItemID => $FreeQty){

						$Item_data = $this->db->get_where('tblitems',array('item_code'=>$ItemID))->row_array();

						if($Item_data['outst_supply_in'] == 'CS'){

							$CS_CR_QTY = $Item_data['case_qty'];

						}else{

							$CS_CR_QTY = $Item_data['crate_qty'];

						}

						$ItemRate =  $this->get_rate_master_data_by_id2($ItemID,$clientdata->DistributorType,$clientdata->state);

						$Rate = $ItemRate->assigned_rate;

						$new_item_data = array(

						"PlantID" =>$selected_company,

						"FY" =>$this->session->userdata('finacial_year'),

						"OrderID" =>$new_orderID,

						"TType" =>"O",

						"TType2" =>'Free Distribution',

						"AccountID" =>$data["clientid"],

						"ItemID" =>$ItemID,

						"BasicRate" =>$Rate,

						"SuppliedIn" =>$Item_data['outst_supply_in'],

						"OrderQty" => $FreeQty,

						"eOrderQty" => $FreeQty,

						"SaleRate" =>0,

						"DiscPerc" =>100,

						"DiscAmt" =>0,

						"cgst" =>0,

						"cgstamt" =>0,

						"sgst" =>0,

						"sgstamt" =>0,

						"igst" =>0,

						"igstamt" =>0,

						"CaseQty" =>$CS_CR_QTY,

						"OrderAmt" =>0,

						"NetOrderAmt" =>0,

						"UserID" =>$this->session->userdata('username'),

						"TransDate" =>$Transdate,

						);

						$this->db->insert(db_prefix() . 'history', $new_item_data);

					}

					

					$TotalGSTAmt = ($TotalCGSTAmt + $TotalSGSTAmt + $TotalIGSTAmt);

					$NetOrderAmt = $TotalSaleAmt + $TotalGSTAmt - $TotalDiscAmt - $TotalDiscOnSaleAmt;

					if($data["istcs"] == "1"){

						$TCSAmt = $NetOrderAmt * 0.001;

						}else{

						$TCSAmt = 0;

					}

					$OrderUpdate = array(

    				'DiscAmt'=>$TotalDiscAmt,

    				'DiscOnSaleAmt'=>$TotalDiscOnSaleAmt,

    				'OrderAmt'=>$NetOrderAmt,

    				'tcsAmt'=>$TCSAmt,

    				'total_tax'=>$TotalGSTAmt,

					);

					$this->db->where('PlantID', $selected_company);

					$this->db->where('FY', $finacial_year);

					$this->db->where('OrderID', $new_orderID);

					$this->db->update(db_prefix() . 'ordermaster', $OrderUpdate);

				}

				return $new_orderID;

			}else{

				return false;

			}

		}

//======================== Update Order ========================================

	public function OrderUpdate($data, $id)

	{	

	    $rootcompany = $this->clients_model->get_rootcompany();

		$selected_company = $this->session->userdata('root_company');

		if($selected_company == "1"){

			$GodownID = 'CSPL';

			}else if($selected_company == "2"){

			$GodownID = 'CFF';

			}else if($selected_company == "3"){

			$GodownID = 'CBUPL';

		}

		$fy = $this->session->userdata('finacial_year');

		$exiteditems = $data["items"]; 

		$free_items = $data["free_items"]; 

		// echo '<pre>';

		// print_r($data);die;

		$newitems = $data["newitems"]; 

		$OrderDetails = $this->GetOrderDetails($id);

		$TransDate2 = $OrderDetails->SaleDate;

		$orderAmt = $OrderDetails->OrderAmt;

		$challanAmt = $OrderDetails->ChallanAmt;

		$Ocrates = $OrderDetails->Crates;

		$Ocases = $OrderDetails->Cases;

		$Ccrates = $OrderDetails->CCrates;

		$Ccases = $OrderDetails->CCases;

		$NewChallanAmt = $challanAmt - $orderAmt;

		$newCrates = $Ccrates - $Ocrates;

		$newCases = $Ccases - $Ocases;

		$exItemCount = count($exiteditems);

		$newCount = $exItemCount + 1;

		$items_free = array();

		

		// echo"<pre>";print_r($ExistingItemsIDs);die;

		foreach($free_items as $exkey => $exvalue)

		{

			array_push($items_free,$exvalue['item_code1']);

		}

		// Delete All Free Item Of This Order Id

		if(count($items_free) >0){

			$this->db->where('OrderID', $id);  

			$this->db->where('TType', 'O');  

			$this->db->where('TType2', 'Free Distribution');  

			$this->db->where_in('ItemID', $items_free);

			$this->db->delete(db_prefix() . 'history');

		}

		$clientdata = $this->GetClientData($data["cust_id"]);

		

		$ExistingItemsIDs = array();

		$TotalItemQty = 0;

		foreach($exiteditems as $exitemkey => $exitemvalue)

		{

			array_push($ExistingItemsIDs,$exitemvalue['item_code1']);

			$TotalItemQty += $exitemvalue["qty"];

		}

		foreach($newitems as $newitemkey => $newitemvalue)

		{

			array_push($ExistingItemsIDs,$newitemvalue['item_code1']);

			$TotalItemQty += $newitemvalue["qty"];

		}

		$GetMultipleScheme = $this->GetMultipleSchemeData($clientdata->DistributorType,$clientdata->state,$data['transdate'],$ExistingItemsIDs);

		

		$i = 0;

		foreach ($GetMultipleScheme as $Scheme) {

			${'TotalBilledQty' . $i} = 0; 

			$i++;

		}

		

		// echo "<pre>";print_r($GetMultipleScheme);die;

		$SingleFreeItem = array();

		$SingleDiscItem = array();

		foreach ($exiteditems as $exkey => $exvalue) 

		{

			$Item = $exvalue['item_code1'];

			$i = 0;

			$IsApplyMultipleScheme = 0;

			foreach($GetMultipleScheme as $Scheme){

				$SchemeItems = $Scheme["ItemID"];

				$SchemeItemsArr = explode(',',$SchemeItems);

				if(in_array($Item,$SchemeItemsArr)){

					${'TotalBilledQty' . $i} += $exvalue["qty"];

					$IsApplyMultipleScheme++;

					break;

				}

				$i++;

			}

			

			$IsFreeItem = 0;

			if($IsApplyMultipleScheme == 0){

				$ChkSingleScheme = $this->GetSchemeData($clientdata->DistributorType,$clientdata->state,$data["transdate"],$exvalue["item_code1"]);

				if(!empty($ChkSingleScheme)){

					foreach($ChkSingleScheme as $each){

						if($exvalue["qty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

							$Disc_pkt = floor($exvalue["qty"] / $each['SlabQty']) * $each['Disc_pkt'];

							$SingleFreeItem[$each['FreeItemID']] = $Disc_pkt;

							$IsFreeItem++;

							break;

						}

					}

				}

			}

			if($IsApplyMultipleScheme == 0 && $IsFreeItem == 0){

				$getDiscscheme = $this->GetDiscSchemeData($clientdata->DistributorType,$clientdata->state,$data['transdate'],$exvalue["item_code1"]);

				if(!empty($getDiscscheme)){

					foreach($getDiscscheme as $each){

						if($exvalue["qty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

							$SingleDiscItem[$exvalue["item_code1"]] = $each["DiscPerc"];

							break;

						}

					}

				}

			}

			

			

		}

		foreach ($newitems as $newkey => $newvalue) 

		{

			$Item = $newvalue['item_code1'];

			$i = 0;

			$IsApplyMultipleScheme = 0;

			foreach($GetMultipleScheme as $Scheme){

				$SchemeItems = $Scheme["ItemID"];

				$SchemeItemsArr = explode(',',$SchemeItems);

				if(in_array($Item,$SchemeItemsArr)){

					${'TotalBilledQty' . $i} += $newvalue["qty"];

					$IsApplyMultipleScheme++;

					break;

				}

				$i++;

			}

			$IsFreeItem = 0;

			if($IsApplyMultipleScheme == 0){

				$ChkSingleScheme = $this->GetSchemeData($clientdata->DistributorType,$clientdata->state,$data["transdate"],$newvalue["item_code1"]);

				if(!empty($ChkSingleScheme)){

					foreach($ChkSingleScheme as $each){

						if($newvalue["qty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

							$Disc_pkt = floor($newvalue["qty"] / $each['SlabQty']) * $each['Disc_pkt'];

							$SingleFreeItem[$each['FreeItemID']] = $Disc_pkt;

							$IsFreeItem++;

							break;

						}

					}

				}

			}

			if($IsApplyMultipleScheme == 0 && $IsFreeItem == 0){

				$getDiscscheme = $this->GetDiscSchemeData($clientdata->DistributorType,$clientdata->state,$data['transdate'],$newvalue["item_code1"]);

				if(!empty($getDiscscheme)){

					foreach($getDiscscheme as $each){

						if($newvalue["qty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

							$SingleDiscItem[$newvalue["item_code1"]] = $each["DiscPerc"];

							break;

						}

					}

				}

			}

		}

		

		$i = 0;

		foreach($GetMultipleScheme as $Scheme){

			$GetMultipleScheme[$i]["TotalBilledQty"] = ${'TotalBilledQty' . $i};

			$i++;

		}

		

		// echo"<pre>";print_r($SingleDiscItem);die;

		$TCS = $data["tcstotal"];

		$total_cases = $data["total_cases"];

		$total_crates = $data["total_crates"];

		$newCrates = $newCrates + $total_crates;

		$newCases = $newCases + $total_cases;

		

		

		$order_data = array(

		"Crates" =>$data["total_crates"],

		"Cases" =>$data["total_cases"],

		"buyer_ord_no" =>$data["buyer_ord_no"],

		"buyer_ord_date" =>to_sql_date($data['buyer_ord_date'])." ".date("H:i:s"),

		"UserID2" =>$this->session->userdata('username'),

		"Lupdate" =>date('Y-m-d H:i:s'),

		"isEdited" =>1,

		);

		if($data["ship_to_act_gst"] !== NULL && $data["ship_to_act_gst"] !== ''){

			$order_data["Gstin2"] = $data["ship_to_act_gst"];

		}

		$order_data["ShipTo"] = $data["Ship_to"];

		$order_data["Dispatchdate"] = to_sql_date(substr($data['DispatchDate'],0,10))." ".substr($data['DispatchDate'],11,5).":00";

		//Update Order Master

		$this->db->where('PlantID', $selected_company);

		$this->db->where('FY', $fy);

		$this->db->where('OrderID', $id);

		$this->db->update(db_prefix() . 'ordermaster', $order_data);

		

		//update exiting items    

		unset($data["items"]);

		$TotalSaleAmt = 0;

		$TotalDiscAmt = 0;

		$TotalSaleDiscAmt = 0;

		$TotalCGSTAmt = 0;

		$TotalSGSTAmt = 0;

		$TotalIGSTAmt = 0;

		$TotalOrderAmt = 0;

		//echo "<pre>";

		foreach ($exiteditems as $exkey => $exvalue) 

		{

			$Salerate =  ($exvalue["rate"] + ($exvalue["rate"] * ($exvalue["taxrate1"] /100)));

			$exSaleAmt =  $exvalue["qty"] * $exvalue["rate"]; // $exvalue["pack_qty"] * Add if Needed



			if (array_key_exists($exvalue["item_code1"], $SingleDiscItem)) {

				$exvalue["dis"] = $SingleDiscItem[$exvalue["item_code1"]];

				$exvalue["dis_amt1"] = ($exSaleAmt * $exvalue["dis"] / 100);

			}

			

			$TotalSaleAmt += $exSaleAmt;

			$TotalDiscAmt += $exvalue["dis_amt1"];

			$exTaxableAmt = $exSaleAmt - $exvalue["dis_amt1"];

			$exsalerate = $exvalue["rate"] * ($exvalue["taxrate1"] /100);

			if(empty($exvalue["ereason"])){

				$lupdate = "";

				$UserID2 = "";

			}else {

				$lupdate = date('Y-m-d H:i:s');

				$UserID2 = $this->session->userdata('username');

			}

			$lupdate = date('Y-m-d H:i:s');

			$UserID2 = $this->session->userdata('username');

			$CGST = 0;

			$SGST = 0;

			$IGST = 0;

			$CGSTAmt = 0;

			$SGSTAmt = 0;

			$IGSTAmt = 0;

			if($data["customer_state_id"] == $rootcompany[0]["state_code"]){

				$CGST = ($exvalue["taxrate1"] /2);

				$SGST = ($exvalue["taxrate1"] /2);

				$CGSTAmt = $exTaxableAmt * ($CGST /100);

				$SGSTAmt = $exTaxableAmt * ($CGST /100);

			}else {

				$IGST = $exvalue["taxrate1"];

				$IGSTAmt = $exTaxableAmt * ($IGST /100);

			}

			$TotalCGSTAmt += $CGSTAmt;

			$TotalSGSTAmt += $SGSTAmt;

			$TotalIGSTAmt += $IGSTAmt;

			$TotalGST = ($CGSTAmt + $SGSTAmt + $IGSTAmt);

			$TotalTax += $TotalGST;

			$exNetOrderAmt = $exTaxableAmt + $TotalGST;

			$TotalOrderAmt += $exNetOrderAmt;

			if($OrderDetails->SalesID !== NULL){

				$updated_item_data = array(

				// "eOrderQty" => $exvalue["qty"], // $exvalue["pack_qty"] * Add If Needed

				"ereason" => $exvalue["ereason"],

				"BilledQty" => $exvalue["qty"], // $exvalue["pack_qty"] * Add If Needed

				"DiscPerc" =>$exvalue["dis"],

				"DiscAmt" =>$exvalue["dis_amt1"],

				"GodownID" =>$GodownID,

				"cgst" =>$CGST,

				"cgstamt" =>$CGSTAmt,

				"sgst" =>$SGST,

				"sgstamt" =>$SGSTAmt,

				"igst" =>$IGST,

				"igstamt" =>$IGSTAmt,

				"CaseQty" =>$exvalue["pack_qty"],

				"ChallanAmt" =>$exSaleAmt,

				"NetChallanAmt" =>$exNetOrderAmt,

				/*"OrderAmt" =>$exSaleAmt,

				"NetOrderAmt" =>$exNetOrderAmt,*/

				"UserID2" =>$UserID2,

				"Lupdate" =>$lupdate,

				);

			}else{

				$updated_item_data = array(

				"eOrderQty" => $exvalue["qty"],// $exvalue["pack_qty"] * add if needed

				"ereason" => $exvalue["ereason"],

				"DiscPerc" =>$exvalue["dis"],

				"DiscAmt" =>$exvalue["dis_amt1"],

				"SaleRate" =>$Salerate,

				"cgst" =>$CGST,

				"cgstamt" =>$CGSTAmt,

				"sgst" =>$SGST,

				"sgstamt" =>$SGSTAmt,

				"igst" =>$IGST,

				"igstamt" =>$IGSTAmt,

				"CaseQty" =>$exvalue["pack_qty"],

				"OrderAmt" =>$exSaleAmt,

				"NetOrderAmt" =>$exNetOrderAmt,

				"UserID2" =>$UserID2,

				"Lupdate" =>$lupdate,

				);

			} 

			//print_r($updated_item_data);

			$this->db->where('PlantID', $selected_company);

			$this->db->where('FY', $fy);

			$this->db->where('id', $exvalue["itemid"]);

			$this->db->where('OrderID', $id);

			$this->db->where('TType', 'O');

			$this->db->where('TType2', 'Order');

			$this->db->update(db_prefix() . 'history', $updated_item_data);

		}  

		//die;

		// Insert new Items in exiting order

		

		unset($data["newitems"]);

		foreach ($newitems as $newkey => $newvalue) {

			$SaleAmt = $newvalue["qty"] * $newvalue["rate"];// $newvalue["pack_qty"] * Add If Needed

			if (array_key_exists($newvalue["item_code1"], $SingleDiscItem)) {

				$newvalue["dis"] = $SingleDiscItem[$newvalue["item_code1"]];

				$newvalue["dis_amt1"] = ($SaleAmt * $newvalue["dis"] / 100);

			}

			$TaxableAmt = $SaleAmt - $newvalue["dis_amt1"];

			$gstamt = $TaxableAmt * ($newvalue["taxrate1"] /100);

			$TotalSaleAmt += $SaleAmt;

			$TotalDiscAmt += $newvalue["dis_amt1"];

			$TotalTax += $gstamt;

			$peritemgst = $newvalue["rate"] * $newvalue["taxrate1"] /100;

			$NetOrderAmt = $TaxableAmt + $gstamt;

			$TotalOrderAmt += $exNetOrderAmt;

			$salerate = ($newvalue["rate"] + ($newvalue["rate"] * ($newvalue["taxrate1"] /100)));

			$CGST = 0;$SGST = 0;$IGST = 0;$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;

			if($data["customer_state_id"] == $rootcompany[0]["state_code"]){

				$CGST = ($newvalue["taxrate1"] /2);

				$SGST = ($newvalue["taxrate1"] /2);

				$CGSTAmt = $TaxableAmt * ($CGST /100);

				$SGSTAmt = $TaxableAmt * ($SGST /100);

			}else {

				$IGST = $newvalue["taxrate1"];

				$IGSTAmt = $TaxableAmt * ($IGST /100);

			}

			$TotalCGSTAmt += $CGSTAmt;

			$TotalSGSTAmt += $SGSTAmt;

			$TotalIGSTAmt += $IGSTAmt;

			if($OrderDetails->SalesID !== NULL){

				$new_item_data = array(

				"PlantID" =>$selected_company,

				"FY" =>$fy,

				"OrderID" =>$id,

				"TType" =>"O",

				"TType2" =>'Order',

				"BillID"=>$OrderDetails->ChallanID,

				"TransID"=>$OrderDetails->SalesID,

				"GodownID" =>$GodownID,

				"TransDate2"=>$TransDate2,

				"AccountID" =>$data["cust_id"],

				"GodownID" =>$GodownID,

				"ItemID" =>$newvalue["item_code1"],

				"BasicRate" =>$newvalue["rate"],

				"SuppliedIn" =>$newvalue["items_cs_cr"],

				// "OrderQty" =>$newvalue["qty"],// $newvalue["pack_qty"] *  Add If Needed

				// "eOrderQty" =>$newvalue["qty"],// $newvalue["pack_qty"] *  Add If Needed

				"OrderQty" =>0,

				"eOrderQty" =>0,

				"BilledQty" =>$newvalue["qty"],// $newvalue["pack_qty"] *  Add If Needed

				"SaleRate" =>$salerate,

				"DiscPerc" =>$newvalue["dis"],

				"DiscAmt" =>$newvalue["dis_amt1"],

				"cgst" =>$CGST,

				"cgstamt" =>$CGSTAmt,

				"sgst" =>$SGST,

				"sgstamt" =>$SGSTAmt,

				"igst" =>$IGST,

				"igstamt" =>$IGSTAmt,

				"CaseQty" =>$newvalue["pack_qty"],

				/*"OrderAmt" =>$SaleAmt,

				"NetOrderAmt" =>$NetOrderAmt,*/

				"OrderAmt" =>0,

				"NetOrderAmt" =>0,

				"ChallanAmt" =>$SaleAmt,

				"NetChallanAmt" =>$NetOrderAmt,

				"Ordinalno" =>$newCount,

				"UserID" =>$this->session->userdata('username'),

				"TransDate" =>to_sql_date($data['transdate'])." ".date("H:i:s"),

				);

				$newCount++;

			}else{

				$new_item_data = array(

				"PlantID" =>$selected_company,

				"FY" =>$fy,

				"OrderID" =>$id,

				"TType" =>"O",

				"TType2" =>'Order',

				"AccountID" =>$data["cust_id"],

				"GodownID" =>$GodownID,

				"ItemID" =>$newvalue["item_code1"],

				"BasicRate" =>$newvalue["rate"],

				"SuppliedIn" =>$newvalue["items_cs_cr"],

				"OrderQty" => 0,// $newvalue["pack_qty"] *  Add If Needed

				"eOrderQty" => $newvalue["qty"],// $newvalue["pack_qty"] *  Add If Needed

				"SaleRate" =>$salerate,

				"DiscPerc" =>$newvalue["dis"],

				"DiscAmt" =>$newvalue["dis_amt1"],

				"cgst" =>$CGST,

				"cgstamt" =>$CGSTAmt,

				"sgst" =>$SGST,

				"sgstamt" =>$SGSTAmt,

				"igst" =>$IGST,

				"igstamt" =>$IGSTAmt,

				"CaseQty" =>$newvalue["pack_qty"],

				"OrderAmt" =>$SaleAmt,

				"NetOrderAmt" =>$NetOrderAmt,

				"Ordinalno" =>$newCount,

				"UserID" =>$this->session->userdata('username'),

				"TransDate" =>to_sql_date($data['transdate'])." ".date("H:i:s"),

				);

				$newCount++;

			} 

			$this->db->insert(db_prefix() . 'history', $new_item_data);

			

		}

		

		foreach($GetMultipleScheme as $each){

			if($each["TotalBilledQty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){

				$Disc_pkt = floor($each["TotalBilledQty"] / $each['SlabQty']) * $each['Disc_pkt'];

				

				$Item_data = $this->db->get_where('tblitems',array('item_code'=>$each['FreeItemID']))->row_array();

				if($Item_data['outst_supply_in'] == 'CS'){

					$CS_CR_QTY = $Item_data['case_qty'];

				}else{

					$CS_CR_QTY = $Item_data['crate_qty'];

				}

				

				$ItemRate =  $this->get_rate_master_data_by_id2($each['FreeItemID'],$clientdata->DistributorType,$clientdata->state);

				$Rate = $ItemRate->assigned_rate;

				

				$new_item_data = array(

				"PlantID" =>$selected_company,

				"FY" =>$fy,

				"OrderID" =>$id,

				"TType" =>"O",

				"TType2" =>'Free Distribution',

				"AccountID" =>$data["cust_id"],

				"GodownID" =>$GodownID,

				"ItemID" =>$each['FreeItemID'],

				"BasicRate" =>$Rate,

				"SuppliedIn" =>$Item_data['outst_supply_in'],

				"OrderQty" => $Disc_pkt,

				"eOrderQty" => $Disc_pkt,

				"SaleRate" =>0,

				"DiscPerc" =>100,

				"DiscAmt" =>0,

				"cgst" =>0,

				"cgstamt" =>0,

				"sgst" =>0,

				"sgstamt" =>0,

				"igst" =>0,

				"igstamt" =>0,

				"CaseQty" =>$CS_CR_QTY,

				"OrderAmt" =>0,

				"NetOrderAmt" =>0,

				"Ordinalno" =>$newCount,

				"UserID" =>$this->session->userdata('username'),

				"TransDate" =>to_sql_date($data['transdate'])." ".date("H:i:s"),

				"TransDate2"=>$TransDate2,

				);

				if ($OrderDetails->SalesID !== NULL) {

					$new_item_data['BillID'] = $OrderDetails->ChallanID;

					$new_item_data['TransID'] = $OrderDetails->SalesID;

					$new_item_data['ChallanAmt'] = 0;

					$new_item_data['NetChallanAmt'] = 0;

				}

				

				$newCount++;

				

				$this->db->insert(db_prefix() . 'history', $new_item_data);

			}

		}

		

		foreach($SingleFreeItem as $ItemID => $FreeQty){

			

			$Item_data = $this->db->get_where('tblitems',array('item_code'=>$ItemID))->row_array();

			if($Item_data['outst_supply_in'] == 'CS'){

				$CS_CR_QTY = $Item_data['case_qty'];

			}else{

				$CS_CR_QTY = $Item_data['crate_qty'];

			}

			

			$ItemRate =  $this->get_rate_master_data_by_id2($ItemID,$clientdata->DistributorType,$clientdata->state);

			$Rate = $ItemRate->assigned_rate;

			$new_item_data = array(

			"PlantID" =>$selected_company,

			"FY" =>$fy,

			"OrderID" =>$id,

			"TType" =>"O",

			"TType2" =>'Free Distribution',

			"AccountID" =>$data["cust_id"],

			"GodownID" =>$GodownID,

			"ItemID" =>$ItemID,

			"BasicRate" =>$Rate,

			"SuppliedIn" =>$Item_data['outst_supply_in'],

			"OrderQty" => $FreeQty,

			"eOrderQty" => $FreeQty,

			"SaleRate" =>0,

			"DiscPerc" =>100,

			"DiscAmt" =>0,

			"cgst" =>0,

			"cgstamt" =>0,

			"sgst" =>0,

			"sgstamt" =>0,

			"igst" =>0,

			"igstamt" =>0,

			"CaseQty" =>$CS_CR_QTY,

			"OrderAmt" =>0,

			"NetOrderAmt" =>0,

			"Ordinalno" =>$newCount,

			"UserID" =>$this->session->userdata('username'),

			"TransDate" =>to_sql_date($data['transdate'])." ".date("H:i:s"),

			"TransDate2"=>$TransDate2,

			);

			if ($OrderDetails->SalesID !== NULL) {

				$new_item_data['BillID'] = $OrderDetails->ChallanID;

				$new_item_data['TransID'] = $OrderDetails->SalesID;

				$new_item_data['ChallanAmt'] = 0;

				$new_item_data['NetChallanAmt'] = 0;

			}

			

			$newCount++;

			

			$this->db->insert(db_prefix() . 'history', $new_item_data);

		}

		

		// Update Order Master

		$NetGSTAmt = ($TotalSGSTAmt + $TotalCGSTAmt + $TotalIGSTAmt);

		$NetOrderAmt = $TotalSaleAmt + $NetGSTAmt - $TotalDiscAmt - $TotalSaleDiscAmt;

		$NewTCSAmt = 0;

		if($TCS>0){

			$NewTCSAmt = $NetOrderAmt * 0.001;

		}

		$OrderUpdate =array(

		"DiscAmt"=>$TotalDiscAmt,

		"DiscOnSaleAmt"=>$TotalSaleDiscAmt,

		"tcsAmt"=>$NewTCSAmt,

		"OrderAmt"=>$NetOrderAmt,

		"total_tax"=>$NetGSTAmt,

		);

		$this->db->where('PlantID', $selected_company);

		$this->db->where('FY', $fy);

		$this->db->where('OrderID', $id);

		$this->db->update(db_prefix() . 'ordermaster', $OrderUpdate);

		

		if($OrderDetails->SalesID !== NULL){

			$NewChallanAmt2 = $NewChallanAmt + $NetOrderAmt;

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $fy);

			$this->db->where('ChallanID', $OrderDetails->ChallanID);

			$this->db->update(db_prefix() . 'challanmaster', [

			'ChallanAmt' =>$NewChallanAmt2,

			'Crates' =>$newCrates,

			'Cases' =>$newCases,

			'UserID2' =>$this->session->userdata('username'),

			'Lupdate' =>date('Y-m-d H:i:s'),

			]);

			

			

			// Exiting order in Challan

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $fy);

			$this->db->where('VoucherID', $OrderDetails->SalesID);

			$this->db->delete(db_prefix() . 'accountledger');

			

			$NetOrderAmt = $NetOrderAmt + $Y;

			$RndAmt = round($NetOrderAmt);

			$roundup2 = $NetOrderAmt - $RndAmt;

			$round_variation = $roundup2;

			

			// update Sales Master table

			$salesdataUpdate =array(

			"tcsAmt"=>$NewTCSAmt,

			"SaleAmt"=>$TotalSaleAmt,

			"DiscAmt"=>$TotalDiscAmt,

			"sgstamt"=>$TotalSGSTAmt,

			"cgstamt"=>$TotalCGSTAmt,

			"igstamt"=>$TotalIGSTAmt,

			"DiscOnSaleAmt"=>$TotalSaleDiscAmt,

			"BillAmt"=>$NetOrderAmt,

			"RndAmt"=>round($NetOrderAmt),

			"UserID2"=>$this->session->userdata('username'),

			"Lupdate"=>date('Y-m-d H:i:s')

			);

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $fy);

			$this->db->where('SalesID', $data["SalesID"]);

			$this->db->update(db_prefix() . 'salesmaster', $salesdataUpdate);

			

			$Reconsile_Arr =array(

			"Amount"=>round($NetOrderAmt),

			"UserID"=>$this->session->userdata('username')

			);

			$this->db->where('TransID', $data["SalesID"]);

			$this->db->update(db_prefix() . 'ReconsileMaster', $Reconsile_Arr);

			

			// update Crates 

			$getCratesDetails = $this->getCratesDetails($OrderDetails->SalesID);

			$create_ledgerdata = array(

			"Qty"=>$data["total_crates"],

			"UserID2"=>$this->session->userdata('username'),

			"Lupdate"=>date('Y-m-d H:i:s')

			);

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $fy);

			$this->db->where('VoucherID', $data["SalesID"]);

			$this->db->update(db_prefix() . 'accountcrates', $create_ledgerdata);

			

			

			$narration = "By SalesID ".$OrderDetails->SalesID."/".$data["ChallanID"]; 

			$narration_tcs = "TCS@0.1000% on SalesID ".$OrderDetails->SalesID."/".$data["ChallanID"];

			$Ord = 0;

			// new Create ledger and update Account balance

			$ledgerdata_credit=array(

			"PlantID"=>$selected_company,

			"FY"=>$fy,

			"Transdate"=>$data["SalesDate"],

			"TransDate2"=>date('Y-m-d H:i:s'),

			"VoucherID"=>$data["SalesID"],

			"AccountID"=>"SALE",

			"EffectOn" => $data["cust_id"],

			"TType"=>"C",

			"Amount"=>$TotalSaleAmt,

			"Narration"=>$narration,

			"PassedFrom"=>"SALE",

			"OrdinalNo"=>$Ord,

			"UserID"=>$this->session->userdata('username')

			);

			$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);

			$Ord++;

			// CGST,SGST and IGST ledger insert

			if($TotalCGSTAmt > 0){

				$acct_name1 = "SGST";

				$acct_name2 = "CGST";

				$ledgerdata_credit_sgst=array(

				"PlantID"=>$selected_company,

				"FY"=>$fy,

				"Transdate"=>$data["SalesDate"],

				"TransDate2"=>date('Y-m-d H:i:s'),

				"VoucherID"=>$OrderDetails->SalesID,

				"AccountID"=>$acct_name1,

				"EffectOn" => $data["cust_id"],

				"TType"=>"C",

				"Amount"=>$TotalCGSTAmt,

				"Narration"=>$narration,

				"PassedFrom"=>"SALE",

				"OrdinalNo"=>$Ord,

				"UserID"=>$this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);

				$Ord++;

				$ledgerdata_credit_cgst=array(

				"PlantID"=>$selected_company,

				"FY"=>$fy,

				"Transdate"=>$data["SalesDate"],

				"TransDate2"=>date('Y-m-d H:i:s'),

				"VoucherID"=>$OrderDetails->SalesID,

				"AccountID"=>$acct_name2,

				"EffectOn" => $data["cust_id"],

				"TType"=>"C",

				"Amount"=>$TotalCGSTAmt,

				"Narration"=>$narration,

				"PassedFrom"=>"SALE",

				"OrdinalNo"=>$Ord,

				"UserID"=>$this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);

				$Ord++;

				}else{

				$acct_name3 = "IGST";

				$ledgerdata_credit_igst=array(

				"PlantID"=>$selected_company,

				"FY"=>$fy,

				"Transdate"=>$data["SalesDate"],

				"TransDate2"=>date('Y-m-d H:i:s'),

				"VoucherID"=>$OrderDetails->SalesID,

				"AccountID"=>$acct_name3,

				"EffectOn" => $data["cust_id"],

				"TType"=>"C",

				"Amount"=>$TotalIGSTAmt,

				"Narration"=>$narration,

				"PassedFrom"=>"SALE",

				"OrdinalNo"=>$Ord,

				"UserID"=>$this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);

				$Ord++;

			}

			// Party account ledger insert

			$ledgerdata_debit=array(

			"PlantID"=>$selected_company,

			"FY"=>$fy,

			"Transdate"=>$data["SalesDate"],

			"TransDate2"=>date('Y-m-d H:i:s'),

			"VoucherID"=>$OrderDetails->SalesID,

			"AccountID"=>$data["cust_id"],

			"EffectOn" => 'SALE',

			"TType"=>"D",

			"Amount"=>$RndAmt,

			"Narration"=>$narration,

			"PassedFrom"=>"SALE",

			"OrdinalNo"=>$Ord,

			"UserID"=>$this->session->userdata('username')

			);

			$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);

			$Ord++;

			if($TotalDiscAmt > 0){

				// Discount account ledger insert

				$ledgerdata_debit=array(

				"PlantID"=>$selected_company,

				"FY"=>$fy,

				"Transdate"=>$data["SalesDate"],

				"TransDate2"=>date('Y-m-d H:i:s'),

				"VoucherID"=>$OrderDetails->SalesID,

				"AccountID"=>"DISC",

				"EffectOn" => $data["cust_id"],

				"TType"=>"D",

				"Amount"=>$TotalDiscAmt,

				"Narration"=>$narration,

				"PassedFrom"=>"SALE",

				"OrdinalNo"=>$Ord,

				"UserID"=>$this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);

				$Ord++;

			}

			

			if($TotalSaleDiscAmt > 0){

				// Discount ON Sale account ledger insert

				$ledgerdata_debit=array(

				"PlantID"=>$selected_company,

				"FY"=>$fy,

				"Transdate"=>$data["SalesDate"],

				"TransDate2"=>date('Y-m-d H:i:s'),

				"VoucherID"=>$OrderDetails->SalesID,

				"AccountID"=>"DISC",

				"EffectOn" => $data["cust_id"],

				"TType"=>"D",

				"Amount"=>$TotalSaleDiscAmt,

				"Narration"=>$narration,

				"PassedFrom"=>"SALE",

				"OrdinalNo"=>$Ord,

				"UserID"=>$this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);

				$Ord++;

			}

			

			// TCS ledger insert

			if($data["istcs"] == "1"){

				$ledgerdata_tcs=array(

				"PlantID"=>$selected_company,

				"FY"=>$fy,

				"Transdate"=>$data["SalesDate"],

				"TransDate2"=>date('Y-m-d H:i:s'),

				"VoucherID"=>$OrderDetails->SalesID,

				"AccountID"=>"TCS",

				"EffectOn" => $data["cust_id"],

				"TType"=>"C",

				"Amount"=>$NewTCSAmt,

				"Narration"=>$narration_tcs,

				"PassedFrom"=>"SALE",

				"OrdinalNo"=>$Ord,

				"UserID"=>$this->session->userdata('username'),

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);

				$Ord++;

				$ledgerdata_tcs=array(

				"PlantID"=>$selected_company,

				"FY"=>$fy,

				"Transdate"=>$data["SalesDate"],

				"TransDate2"=>date('Y-m-d H:i:s'),

				"VoucherID"=>$OrderDetails->SalesID,

				"AccountID"=>$data["cust_id"],

				"EffectOn" => 'TCS',

				"TType"=>"D",

				"Amount"=>$NewTCSAmt,

				"Narration"=>$narration_tcs,

				"PassedFrom"=>"SALE",

				"OrdinalNo"=>$Ord,

				"UserID"=>$this->session->userdata('username'),

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);

				$Ord++;

			}

			

			if($round_variation >=0){

				$rTType = "D";

				$round_variation_new = abs($round_variation);

				}else{

				$rTType = "C";

				$round_variation_new = abs($round_variation);

			}

			$ledgerdata_roundoff =array(

			"PlantID"=>$selected_company,

			"FY"=>$fy,

			"Transdate"=>$data["SalesDate"],

			"TransDate2"=>date('Y-m-d H:i:s'),

			"VoucherID"=>$OrderDetails->SalesID,

			"AccountID"=>"ROUNDOFF",

			"EffectOn" => $data["cust_id"],

			"TType"=>$rTType,

			"Amount"=>$round_variation_new,

			"Narration"=>$narration,

			"PassedFrom"=>"SALE",

			"OrdinalNo"=>$Ord,

			"UserID"=>$this->session->userdata('username')

			);

			$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);

		}

		//die;

		// Delete item in exiting order

		$remove_item = $data["removed_items"];

		foreach ($remove_item as $deletedvalue) {

			$this->db->where('id', $deletedvalue);

			$this->db->delete(db_prefix() . 'history');

		}

		return $id;

	}

		

		public function CheckStockQty($ItemID)

		{

			$selected_company = $this->session->userdata('root_company');

			if($selected_company == "1"){

				$GodownID = 'CSPL';

				}else if($selected_company == "2"){

				$GodownID = 'CFF';

				}else if($selected_company == "3"){

				$GodownID = 'CBUPL';

			}

			$fy = $this->session->userdata('finacial_year');

			

			$this->db->select('*');

			$this->db->from(db_prefix() . 'stockmaster');

			$this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'stockmaster.FY', $fy);

			$this->db->where('GodownID',$GodownID);

			$this->db->where(db_prefix() . 'stockmaster.ItemID ', $ItemID);

			return $this->db->get()->row();

		}

		public function getCratesDetails($SalesID)

		{

			$selected_company = $this->session->userdata('root_company');

			$fy = $this->session->userdata('finacial_year');

			$this->db->select(db_prefix() .'accountcrates.*');

			$this->db->from(db_prefix() .'accountcrates');

			$this->db->where(db_prefix() .'accountcrates.VoucherID', $SalesID);

			$this->db->where(db_prefix() .'accountcrates.PlantID', $selected_company);

			$this->db->where(db_prefix() .'accountcrates.FY', $fy);

			return $this->db->get()->row();

		}

		public function get_ledgerDetails($SalesID)

		{

			$selected_company = $this->session->userdata('root_company');

			$fy = $this->session->userdata('finacial_year');

			$this->db->select(db_prefix() .'accountledger.*');

			$this->db->from(db_prefix() .'accountledger');

			$this->db->where(db_prefix() .'accountledger.VoucherID', $SalesID);

			$this->db->where(db_prefix() .'accountledger.PlantID', $selected_company);

			$this->db->where(db_prefix() .'accountledger.FY', $fy);

			return $this->db->get()->result_array();

		}

		public function get_acc_bal($id)

		{

			$selected_company = $this->session->userdata('root_company');

			$fy = $this->session->userdata('finacial_year');

			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $fy);

			$this->db->WHERE('AccountID', $id);

			

			return $this->db->get(db_prefix() . 'accountbalances')->row();

		}

		function getStocksDetails($id){

			

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			if($selected_company == "1"){

				$GodownID = 'CSPL';

				}else if($selected_company == "2"){

				$GodownID = 'CFF';

				}else if($selected_company == "3"){

				$GodownID = 'CBUPL';

			}

			

			$this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');

			$this->db->from(db_prefix() .'history');

			$this->db->where(db_prefix() .'history.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'history.ItemID ', $id);

			$this->db->where(db_prefix() . 'history.BillID IS NOT NULL', NULL, FALSE);

			$this->db->where(db_prefix() .'history.FY', $fy);

			$this->db->where(db_prefix() .'history.GodownID', $GodownID);

			$this->db->group_by('ItemID,TType,TType2');

			return $this->db->get()->result_array();

		}

		

		public function GetOrderDetails($OrderID)

		{

			$selected_company = $this->session->userdata('root_company');

			$fy = $this->session->userdata('finacial_year');

			

			$this->db->select(db_prefix() . 'ordermaster.OrderAmt,'.db_prefix() . 'ordermaster.Crates,'.db_prefix() . 'ordermaster.Cases,'.db_prefix() . 'challanmaster.Crates AS CCrates,'.db_prefix() . 'challanmaster.Cases AS CCases,'.db_prefix() . 'ordermaster.SalesID,

			tblsalesmaster.ChallanID,tblsalesmaster.cgstamt,tblsalesmaster.igstamt,tblsalesmaster.BillAmt,tblsalesmaster.SaleAmt,tblsalesmaster.Transdate AS SaleDate,tblchallanmaster.ChallanAmt');

			$this->db->from(db_prefix() . 'ordermaster');

			$this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.SalesID = ' . db_prefix() . 'ordermaster.SalesID AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'salesmaster.FY = ' . db_prefix() . 'ordermaster.FY','LEFT');

			$this->db->join(db_prefix() . 'challanmaster', '' . db_prefix() . 'challanmaster.ChallanID = ' . db_prefix() . 'ordermaster.ChallanID AND ' . db_prefix() . 'challanmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'challanmaster.FY = ' . db_prefix() . 'ordermaster.FY','LEFT');

			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'ordermaster.FY', $fy);

			$this->db->where(db_prefix() . 'ordermaster.OrderID ', $OrderID);

			return $this->db->get()->row();

		}

		

		public function GetItemDetails($OrderID)

		{

			$selected_company = $this->session->userdata('root_company');

			$fy = $this->session->userdata('finacial_year');

			if($selected_company == "1"){

				$GodownID = 'CSPL';

				}else if($selected_company == "2"){

				$GodownID = 'CFF';

				}else if($selected_company == "3"){

				$GodownID = 'CBUPL';

			}

			

			$this->db->select(db_prefix() . 'history.*, '.db_prefix() . 'stockmaster.*');

			$this->db->from(db_prefix() . 'history');

			$this->db->join(db_prefix() . 'stockmaster', '' . db_prefix() . 'stockmaster.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'stockmaster.PlantID = ' . db_prefix() . 'history.PlantID AND ' . db_prefix() . 'stockmaster.FY = ' . db_prefix() . 'history.FY');

			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'history.FY', $fy);

			//$this->db->where(db_prefix() . 'history.GodownID',$GodownID);

			$this->db->where(db_prefix() . 'stockmaster.GodownID',$GodownID);

			$this->db->where(db_prefix() . 'history.OrderID ', $OrderID);

			return $this->db->get()->result_array();

		}

		//=================== Increment Next Order Number =============================

		public function increment_next_number()

		{

			$selected_company = $this->session->userdata('root_company');

			$FY = $this->session->userdata('finacial_year');

			if($selected_company == 1){

				$this->db->where('name', 'next_order_number_for_gf');

				}/*elseif($selected_company == 2){

				$this->db->where('name', 'next_order_number_for_cff');

				}elseif($selected_company == 3){

				$this->db->where('name', 'next_order_number_for_cbu');

				}elseif($selected_company == 4){

				$this->db->where('name', 'next_order_number_for_cbupl');

				}

			*/

			$this->db->set('value', 'value+1', false);

			$this->db->WHERE('FY', $FY);

			$this->db->update(db_prefix() . 'options');

		}

		

		/**

			* @since  2.7.0

			*

			* Decrement the invoies next number

			*

			* @return void

		*/

		public function decrement_next_number()

		{

			$this->db->where('name', 'next_order_number');

			$this->db->set('value', 'value-1', false);

			$this->db->update(db_prefix() . 'options');

		}

		

		public function load_data_for_order($data){

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			$this->db->select('tblordermaster.OrderID,tblordermaster.Transdate,tblordermaster.SalesID,tblsalesmaster.Transdate AS SaleDate,tblordermaster.ChallanID,

			tblordermaster.AccountID,tblordermaster.OrderAmt,tblordermaster.order_type,tblsalesmaster.BillAmt,tblsalesmaster.RndAmt');

			$this->db->from(db_prefix() . 'ordermaster');

			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'ordermaster.FY', $fy);

			$this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.OrderID = ' . db_prefix() . 'ordermaster.OrderID AND ' . db_prefix() . 'salesmaster.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'salesmaster.FY = ' . db_prefix() . 'ordermaster.FY');

			$this->db->where(db_prefix() . 'ordermaster.ChallanID is NOT NULL', NULL, FALSE);

			$this->db->where( db_prefix() . 'salesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');

			$this->db->order_by( db_prefix() .'ordermaster.OrderID','DESC');

			return $this->db->get()->result_array();

		}

		public function GetClientData($AccountID)

		{

			

			$PlantID = $this->session->userdata('root_company');

			

			$this->db->select(db_prefix() . 'clients.*');

			$this->db->where('AccountID', $AccountID);

			$this->db->where('PlantID', $PlantID);

			$this->db->from(db_prefix() . 'clients');

			$data =  $this->db->get()->row();

			return $data;

		}

		public function GetSchemeData($dist_type,$state,$date,$itemid)

		{

			// $distTypeStr = implode(',', $dist_type);

			// echo $distTypeStr;die;

			$date = to_sql_date($date);

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			$sql = 'SELECT * 

			FROM `tblschemedetails`

			INNER JOIN tblschememaster ON tblschememaster.SchemeID = tblschemedetails.SchemeID

			WHERE ("'.$date.' 00:00:00" BETWEEN tblschememaster.StartDate AND tblschememaster.EndDate) AND tblschemedetails.ItemID = "'.$itemid.'" AND  FIND_IN_SET("'.$dist_type.'", tblschemedetails.DistributorType) AND tblschemedetails.StateID = "'.$state.'" AND tblschemedetails.Disc_type="free_distribution" AND tblschememaster.Approve ="Y" AND tblschemedetails.ActYN = "Y" AND tblschememaster.SchemeType = "Single" Order by tblschemedetails.SlabQty DESC';

			return $this->db->query($sql)->result_array();

			

		}

		public function GetDiscSchemeData($dist_type,$state,$date,$itemid)

		{

			// $distTypeStr = implode(',', $dist_type);

			// echo $distTypeStr;die;

			$date = to_sql_date($date);

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			$sql = 'SELECT * 

			FROM `tblschemedetails`

			INNER JOIN tblschememaster ON tblschememaster.SchemeID = tblschemedetails.SchemeID

			WHERE ("'.$date.' 00:00:00" BETWEEN tblschememaster.StartDate AND tblschememaster.EndDate) AND tblschemedetails.ItemID = "'.$itemid.'" AND  FIND_IN_SET("'.$dist_type.'", tblschemedetails.DistributorType) AND tblschemedetails.StateID = "'.$state.'" AND tblschemedetails.Disc_type="disc" AND tblschememaster.Approve ="Y" AND tblschemedetails.ActYN = "Y" AND tblschememaster.SchemeType = "Single" Order by tblschemedetails.SlabQty DESC';

			return $this->db->query($sql)->result_array();

			

		}

		

		

		public function GetMultipleSchemeData($dist_type, $state, $date, $itemids_array)

		{

			$date = to_sql_date($date);

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			// Build FIND_IN_SET conditions for each item

			$findSet = array_map(function($item) {

				return 'FIND_IN_SET("' . $item . '", tblschemedetails.ItemID)';

			}, $itemids_array);

			

			$findSetStr = implode(' OR ', $findSet);

			

			// Build final SQL query

			$sql = 'SELECT * 

            FROM `tblschemedetails`

            INNER JOIN tblschememaster 

			ON tblschememaster.SchemeID = tblschemedetails.SchemeID

            WHERE ("' . $date . ' 00:00:00" BETWEEN tblschememaster.StartDate AND tblschememaster.EndDate)

			AND (' . $findSetStr . ')

			AND FIND_IN_SET("' . $dist_type . '", tblschemedetails.DistributorType)

			AND tblschemedetails.StateID = "' . $state . '"

			AND tblschemedetails.Disc_type = "free_distribution"

			AND tblschememaster.Approve = "Y"

			AND tblschemedetails.ActYN = "Y"

			AND tblschememaster.SchemeType = "Multiple"

            ORDER BY tblschemedetails.SlabQty DESC';

			

			return $this->db->query($sql)->result_array();

		}

		public function GetLimitExceededOrders($data)

		{

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

			$Status = $data["Status"];

			$Approver = $data["Approver"];

			$AccountID = $data["AccountID"];

			

			$this->db->select('*,tblclients.company AS AccountName,ShipAddrs.company AS ShipToAccountName,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS ApprovedBy');

			$this->db->from(db_prefix() . 'ordermaster');

			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblordermaster.AccountID AND tblclients.PlantID = tblordermaster.PlantID', 'left');

			$this->db->join(db_prefix() . 'clients  AS ShipAddrs', 'ShipAddrs.AccountID = tblordermaster.AccountID2 AND ShipAddrs.PlantID = tblordermaster.PlantID', 'left');

			$this->db->join(db_prefix() . 'staff', 'tblstaff.AccountID = tblordermaster.credit_approved_by AND tblstaff.PlantID = tblordermaster.PlantID', 'left');

			

			$this->db->where(db_prefix() . 'ordermaster.credit_exceed', 'Y');

			if($Status == 'Approved'){

				$this->db->where(db_prefix() . 'ordermaster.credit_apply', 'N');

				if(!empty($Approver)){

					$this->db->where(db_prefix() . 'ordermaster.credit_approved_by', $Approver);

				}

				}else{

				$this->db->where(db_prefix() . 'ordermaster.credit_apply', 'Y');

			}

			if(!empty($AccountID)){

				$this->db->where(db_prefix() . 'ordermaster.AccountID', $AccountID);

			}

			$this->db->where('tblordermaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');

			if($Status != 'Approved'){

				$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');

				$this->db->where(db_prefix() . 'ordermaster.ChallanID', null);

			}

			$this->db->where(db_prefix() . 'ordermaster.FY', $fy);

			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

			return $this->db->get()->result_array();

		}

		public function LimitApproverStaff()

		{

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			$this->db->select('credit_approved_by As ID,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS label');

			$this->db->from(db_prefix() . 'ordermaster');

			$this->db->join(db_prefix() . 'staff', 'tblstaff.AccountID = tblordermaster.credit_approved_by AND tblstaff.PlantID = tblordermaster.PlantID', 'INNER');

			

			$this->db->where(db_prefix() . 'ordermaster.FY', $fy);

			$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);

			$this->db->group_by(db_prefix() . 'ordermaster.credit_approved_by');

			return $this->db->get()->result_array();

		}


		
		public function GetPartyList()
		{
			$selected_company = $this->session->userdata('root_company');
			$SubActGroupID = '100056';
			$this->db->select('*');
			$this->db->where('SubActGroupID1', $SubActGroupID);
			$this->db->where('PlantID', $selected_company);
			$this->db->order_by('company', 'ASC');
			$records = $this->db->get(db_prefix() . 'clients')->result_array();
			return $records;
		}
		

		public function get_rate_master_data_by_id2($item_id, $distributor_id, $state_id)

		{

			$curDate = date('Y-m-d H:i:s');

			$this->db->select('*');

			$this->db->where('state_id', $state_id);

			$this->db->where('distributor_id', $distributor_id);

			$this->db->where('item_id', $item_id);

			$this->db->where('effective_date <=', $curDate);

			$this->db->from(db_prefix() . 'rate_master');

			$this->db->order_by('effective_date DESC, id DESC');

			$data =  $this->db->get()->row();

			

			if(empty($data)){

				$this->db->select(db_prefix() . 'ratehistory2.BasicRate AS assigned_rate');

				$this->db->where('StateID', $state_id);

				$this->db->where('DistributorType', $distributor_id);

				$this->db->where('ItemID', $item_id);

				$this->db->where('EffDate <=', $curDate);

				$this->db->order_by('EffDate', 'DESC');

				$this->db->from(db_prefix() . 'ratehistory2');

				$data2 =  $this->db->get()->row();

				return $data2;

				}else{

				return $data;

			}

			

		}

	}
