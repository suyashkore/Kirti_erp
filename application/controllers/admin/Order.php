<?php

	

	defined('BASEPATH') or exit('No direct script access allowed');

	

	class Order extends AdminController

	{

		public function __construct()

		{

			parent::__construct();

			$this->load->model('invoices_model');

			$this->load->model('order_model');

			$this->load->model('credit_notes_model');

		}

		

		/* Get all invoices in case user go on index page */

		public function index($id = '')

		{

			if (!has_permission_new('orders', '', 'create') ) {

				access_denied('invoices');

			}

			$this->order();

		}

		

		public function itemlist(){

			$this->load->model('invoice_items_model');

			// POST data

			$postData = $this->input->post();

			

			// Get data

			$data = $this->invoice_items_model->getitem($postData);

			

			echo json_encode($data);

		}

		

		/* Get item by id / ajax */

		public function get_remark_by_orderid($id)

		{

			if ($this->input->is_ajax_request()) {

				$order                   = $this->order_model->get2($id);

				

				

				echo json_encode($order);

			}

		}

		

		/* Edit or update items / ajax request /*/

		public function remark_update()

		{

			if (has_permission_new('orders', '', 'edit')) {

				if ($this->input->post()) {

					$data = $this->input->post();

					if ($data['itemid'] == '') {

						if (!has_permission_new('orders', '', 'create')) {

							header('HTTP/1.0 400 Bad error');

							echo _l('access_denied');

							die;

						}

						$id      = $this->invoice_items_model->add($data);

						$success = false;

						$message = '';

						if ($id) {

							$success = true;

							$message = _l('added_successfully', _l('sales_item'));

						}

						echo json_encode([

                        'success' => $success,

                        'message' => $message,

                        'item'    => $this->invoice_items_model->get($id),

						]);

						} else {

						if (!has_permission_new('orders', '', 'edit')) {

							header('HTTP/1.0 400 Bad error');

							echo _l('access_denied');

							die;

						}

						$success = $this->order_model->remark_update($data);

						$message = '';

						if ($success) {

							$message = _l('updated_successfully', _l('sales_item'));

						}

						echo json_encode([

                        'success' => $success,

                        'message' => $message,

						]);

					}

				}

			}

		}

		

		public function itemlist_using_itemcode(){

			$this->load->model('invoice_items_model');

			// POST data

			$postData = $this->input->post();

			

			// Get data

			$data = $this->invoice_items_model->getitem_using_itemcode($postData);

			

			echo json_encode($data);

		}

		

		public function GetItemDetailByID(){

			$this->load->model('invoice_items_model');

			// POST data

			$postData = $this->input->post();

			// Get data

			$data = $this->invoice_items_model->getItemDetailsByID($postData);

			echo json_encode($data);

		}

		

		/* List all invoices datatables */

		public function SaleList($id = '')

		{

			if (!has_permission_new('sale_list', '', 'view')) {

				access_denied('order list');

			}

			

			close_setup_menu();

			

			$this->load->model('payment_modes_model');

			$data['payment_modes']        = $this->payment_modes_model->get('', [], true);

			$data['invoiceid']            = $id;

			$data['title']                = "Sale List";

			$data['invoices_years']       = $this->invoices_model->get_invoices_years();

			$data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

			$data['invoices_statuses']    = $this->invoices_model->get_statuses();

			$data['bodyclass']            = 'invoices-total-manual';

			

			

			$this->load->model('accounts_master_model');

			$data['company_detail'] = $this->accounts_master_model->get_company_detail();

			

			

			$this->load->view('admin/order/manage', $data);

		}

		

		/* List all invoices datatables */

		public function pending_orders2($id = '')

		{

			if (!has_permission_new('sale_list', '', 'view')) {

				access_denied('orders');

			}

			

			close_setup_menu();

			

			$data['title']                = "Order List";

			$order = $this->order_model->get2($id);

			// echo "<pre>"; print_r($order->items);die;

			$data['order'] = $order;

			$this->load->view('admin/order/order_details', $data);

		}

		

		/* List all invoices datatables */

		public function order_details($id = '')

		{

			if (!has_permission_new('sale_list', '', 'view')) {

				access_denied('orders');

			}

			

			close_setup_menu();

			

			$data['title']                = "Order Details";

			$order = $this->order_model->get2($id);

			$data['selected_company_details']    = $this->order_model->get_selected_company_details();

			$data['order'] = $order;

			$this->load->view('admin/order/order_details', $data);

		}

		

//================== Pending Order Page Load ===================================

	public function pending_orders($id = '')

	{

		if (!has_permission_new('pending_orders', '', 'view')) {

			access_denied('orders');

		}

		

		close_setup_menu();

		

		$this->load->model('payment_modes_model');

		$data['payment_modes']        = $this->payment_modes_model->get('', [], true);

		$data['invoiceid']            = $id;

		$data['title']                = "Pending Order";

		$data['invoices_years']       = $this->invoices_model->get_invoices_years();

		$data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

		$data['invoices_statuses']    = $this->invoices_model->get_statuses();

		$data['states']    = $this->order_model->get_state_list();

		$data['dist_type']    = $this->order_model->get_distributor_type();

		$data['selected_company_details']    = $this->order_model->get_selected_company_details();

		$data['bodyclass']            = 'invoices-total-manual';

		$this->load->view('admin/order/pendig_order_list2', $data);

	}

//================== Let Delivey Order Page Load ===============================

	public function DelayDelivery($id = '')

	{

		if (!has_permission_new('DelayDelivery', '', 'view')) {

			access_denied('orders');

		}

		

		$data['title']                = "Delay Delivery Order";

		$data['states']    = $this->order_model->get_state_list();

		$data['dist_type']    = $this->order_model->get_distributor_type();

		$data['StationList']    = $this->order_model->StationMasterList();

		$data['selected_company_details']    = $this->order_model->get_selected_company_details();

		$this->load->view('admin/order/DelayOrderList', $data);

	}

//================== Load Delay Order List =====================================

    public function GetDelayOrders()

	{

		$data = array(

    		'from_date' => $this->input->post('from_date'),

    		'dates' => $this->input->post('dates'),

    		'state'  => $this->input->post('state'),

    		'dist_type'  => $this->input->post('dist_type'),

    		'StationName'  => $this->input->post('StationName')

		);

		$data = $this->order_model->GetDelayOrders($data);

		$html = "";

		$SrNo = 1;

		foreach($data as $key=>$val){

		    if($val["GetPassTime"] > $val["Dispatchdate"] || empty($val["GetPassTime"])){

		        if($val["GetPassTime"]){

		            $to   = new DateTime(substr($val["GetPassTime"],0,19));

		        }else{

		            $to = date('Y-m-d H:i:s');

		        }

		        $from = new DateTime(substr($val["Dispatchdate"],0,19));

                $diff = $from->diff($to);

                $DelayTime = "";

                if($diff->days > 0){

                    $DelayTime .= $diff->days * 24 + $diff->h;

                }

                if($diff->h <= 24 && $diff->h > 0){

                    $DelayTime .= $diff->h." hrs ";

                }

                if($diff->i){

                    $DelayTime .= $diff->i." min ";

                }

                if($diff->s){

                    $DelayTime .= $diff->s." sec ";

                }

                //echo $diff->h . " hours, " . $diff->i . " minutes, " . $diff->s . " seconds";

                

		        $html .= '<tr>';

    		    $html .= '<td>'.$SrNo.'</td>';

    		    $html .= '<td>'.$val["OrderID"].'</td>';

    		    $html .= '<td>'._d(substr($val["Transdate"],0,19)).'</td>';

    		    $html .= '<td>'._d(substr($val["Dispatchdate"],0,19)).'</td>';

    		    $html .= '<td>'._d(substr($val["GetPassTime"],0,19)).'</td>';

    		    $html .= '<td>'.$DelayTime.'</td>';

    		    $html .= '<td>'.$val["AccountName"].'</td>';

    		    $html .= '<td>'.$val["StationName"].'</td>';

    		    $html .= '<td>'.$val["StateName"].'</td>';

    		    $html .= '<td>'.$val["VehicleID"].'</td>';

    		    $html .= '<td>'.$val["DriverID"].'</td>';

    		    $html .= '<td>'.$val["CreateStaffName"].'</td>';

    		    $html .= '</tr>';

    		    $SrNo++;

		    }

		}

		echo $html;

	}

		

		public function load_data()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'dates' => $this->input->post('dates'),

			'order_type'  => $this->input->post('order_type'),

			'state'  => $this->input->post('state'),

			'dist_type'  => $this->input->post('dist_type'),

			'sort_by'  => $this->input->post('sort_by'),

			);

			$data = $this->order_model->load_data($data);

			echo json_encode($data);

		}

		

		public function load_data_items()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'dates' => $this->input->post('dates'),

			'order_type'  => $this->input->post('order_type'),

			'state'  => $this->input->post('state'),

			'dist_type'  => $this->input->post('dist_type')

			);

			$data = $this->order_model->load_data_items($data);

			echo json_encode($data);

		}

		

		public function load_data2()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'dates' => $this->input->post('dates'),

			'order_type'  => $this->input->post('order_type'),

			'state'  => $this->input->post('state'),

			'dist_type'  => $this->input->post('dist_type'),

			'selected_ids'  => $this->input->post('selected_ids')

			);

			$data = $this->order_model->load_data2($data);

			echo json_encode($data);

		}

		

		public function load_data_items2()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'dates' => $this->input->post('dates'),

			'order_type'  => $this->input->post('order_type'),

			'state'  => $this->input->post('state'),

			'dist_type'  => $this->input->post('dist_type'),

			'selected_ids'  => $this->input->post('selected_ids')

			);

			$data = $this->order_model->load_data_items2($data);

			echo json_encode($data);

		}

		

		public function update_order_status()

		{	

			$data = $this->order_model->update_order_status($this->input->post('selected_ids'),$this->input->post('selected_ids_remarks'),$this->input->post('unselected_ids'),$this->input->post('unselected_ids_remarks'));

			echo json_encode($data);

			

		}

		

		public function reset_order_status()

		{

			/*$data = array(

				$this->input->post('table_column') => $this->input->post('value'),

				$this->input->post('table_column') => $this->input->post('value')

			);*/

			

			// echo $selected_id=$this->input->post('selected_ids');

			//   $selected_id = $this->input->post('selected_ids_remarks');

			//   $unselected_ids = $this->input->post('unselected_ids');

			//   $unselected_ids_remarks = $this->input->post('unselected_ids_remarks');

			//  exit();

			

			$data = $this->order_model->reset_order_status($this->input->post('selected_ids'),$this->input->post('selected_ids_remarks'),$this->input->post('unselected_ids'),$this->input->post('unselected_ids_remarks'));

			echo json_encode($data);

			

		}

		

		

		

		public function edit_order_table()

		{

			if (!has_permission_new('orders', '', 'view')) {

				ajax_access_denied();

			}

			if ($this->input->is_ajax_request()) {

				if($this->input->post()){

					$this->app->get_table_data('edit_order');

				}

			}

		}

		

		

		

		public function table($clientid = '')

		{

			

			$this->load->model('payment_modes_model');

			$data['payment_modes'] = $this->payment_modes_model->get('', [], true);

			

			$this->app->get_table_data(($this->input->get('recurring') ? 'recurring_invoices' : 'order2'), [

            'clientid' => $clientid,

            'data'     => $data,

			]);

		}

		

		//=========================================== Get Shipping Details =============

		public function GetShippingDetails()

		{

			$ShippingID =  $this->input->post('Ship_to');

			$AccountID = "";

			$ShippingDetails  = $this->order_model->GetShippingAddress($AccountID,$ShippingID);

			echo json_encode($ShippingDetails);

		}

		

		//=========================================== Get Shipping Address List =============

		public function GetShippingAddressList()

		{

			$ShipToParty =  $this->input->post('ShipToParty');

			$ShippingDetails  = $this->order_model->GetShippingAddress($ShipToParty);

			echo json_encode($ShippingDetails);

		}

		

		public function client_change_data($customer_id, $current_invoice = '')

		{

			if ($this->input->is_ajax_request()) {

				$this->load->model('projects_model');

				$this->load->model('invoice_items_model');

				$data                     = [];

				$fy = $this->session->userdata('finacial_year');

				$selected_company = $this->session->userdata('root_company');

				

				$data['client_details']  = $this->clients_model->get($customer_id);

				$data['client_actbal']  = $this->order_model->get_accbal($customer_id,$selected_company,$fy);

				$data['client_last_bill']  = $this->order_model->get_last_bill_on($customer_id,$selected_company,$fy);

				$data['client_last_deposit']  = $this->order_model->get_last_deposit_on($customer_id,$selected_company,$fy);

				$data['shipping_address'] = $this->order_model->GetShippingAddress($customer_id);

				$data['billing_shipping'] = '';

				$data['client_currency']  = $this->clients_model->get_customer_default_currency($customer_id);

				$data['client_route']  = $this->clients_model->getroutebyclient($customer_id);

				$data['client_details']->routes = $data['client_route']->routes;

				$data['location_details'] = $this->clients_model->get_location_type($customer_id);

				$data['client_details']->location_type = $data['location_details']->LocationTypeID;

				$client_item_div  = $this->clients_model->getclientitem_division($customer_id);

				$pending_order  = $this->order_model->check_pending_order($customer_id);

				$data['client_details']->pending_order = $pending_order;

				$item_div_ids = array();

				if(empty($client_item_div)){

					

					}else{

					foreach ($client_item_div as $key => $value) {

						# code...

						array_push($item_div_ids, $value["ItemDivID"]);

					}

				}

				

				$data['client_details']->itemdivision = $item_div_ids;

				

				$data['customer_groups_name'] = $this->clients_model->get_customer_groups_name($data['client_details']->DistributorType);

				

				

				echo json_encode($data);

			}

		}

		

		public function update_number_settings($id)

		{

			$response = [

            'success' => false,

            'message' => '',

			];

			if (has_permission_new('orders', '', 'edit')) {

				$affected_rows = 0;

				

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'invoices', [

                'prefix' => $this->input->post('prefix'),

				]);

				if ($this->db->affected_rows() > 0) {

					$affected_rows++;

				}

				

				if ($affected_rows > 0) {

					$response['success'] = true;

					$response['message'] = _l('updated_successfully', _l('invoice'));

				}

			}

			echo json_encode($response);

			die;

		}

		

		public function validate_invoice_number()

		{

			$isedit          = $this->input->post('isedit');

			$number          = $this->input->post('number');

			$date            = $this->input->post('date');

			$original_number = $this->input->post('original_number');

			$number          = trim($number);

			$number          = ltrim($number, '0');

			

			if ($isedit == 'true') {

				if ($number == $original_number) {

					echo json_encode(true);

					die;

				}

			}

			

			if (total_rows(db_prefix() . 'invoices', [

            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),

            'number' => $number,

            'status !=' => Invoices_model::STATUS_DRAFT,

			]) > 0) {

				echo 'false';

				} else {

				echo 'true';

			}

		}

		

		public function add_note($rel_id)

		{

			if ($this->input->post() && user_can_view_invoice($rel_id)) {

				$this->misc_model->add_note($this->input->post(), 'invoice', $rel_id);

				echo $rel_id;

			}

		}

		

		public function get_notes($id)

		{

			if (user_can_view_invoice($id)) {

				$data['notes'] = $this->misc_model->get_notes($id, 'invoice');

				$this->load->view('admin/includes/sales_notes_template', $data);

			}

		}

		

		public function pause_overdue_reminders($id)

		{

			if (has_permission_new('orders', '', 'edit')) {

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 1]);

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function resume_overdue_reminders($id)

		{

			if (has_permission_new('orders', '', 'edit')) {

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 0]);

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function mark_as_cancelled($id)

		{

			if (!has_permission_new('orders', '', 'edit') && !has_permission_new('invoices', '', 'create')) {

				access_denied('invoices');

			}

			

			$success = $this->invoices_model->mark_as_cancelled($id);

			

			if ($success) {

				set_alert('success', _l('invoice_marked_as_cancelled_successfully'));

			}

			

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function unmark_as_cancelled($id)

		{

			if (!has_permission_new('orders', '', 'edit') && !has_permission_new('invoices', '', 'create')) {

				access_denied('order');

			}

			$success = $this->invoices_model->unmark_as_cancelled($id);

			if ($success) {

				set_alert('success', _l('invoice_unmarked_as_cancelled'));

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		

		

		

		

		public function get_bill_expense_data($id)

		{

			$this->load->model('expenses_model');

			$expense = $this->expenses_model->get($id);

			

			$expense->qty              = 1;

			$expense->long_description = clear_textarea_breaks($expense->description);

			$expense->description      = $expense->name;

			$expense->rate             = $expense->amount;

			if ($expense->tax != 0) {

				$expense->taxname = [];

				array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);

			}

			if ($expense->tax2 != 0) {

				array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);

			}

			echo json_encode($expense);

		}

		

		/* Add new invoice or update existing */

		public function order($id = '')

		{

			if (!has_permission_new('orders', '', 'view')) {

				access_denied('order');

			}

			$this->load->model('invoice_items_model');

			$this->load->model('taxes_model');

			$this->load->model('currencies_model');

			$this->load->model('clients_model');

			if ($this->input->post()) {

				$order_data = $this->input->post();

				

				if ($id == '') {

					if (!has_permission_new('orders', '', 'create')) {

						access_denied('order');

					}

					$id = $this->order_model->AddNewOrder($order_data);

					if ($id) {

						set_alert('success', _l('added_successfully', 'Order'));

						$redUrl = admin_url('order/pending_orders2/' . $id);

						

						if (isset($order_data['save_and_record_payment'])) {

							$this->session->set_userdata('record_payment', true);

							} elseif (isset($order_data['save_and_send_later'])) {

							$this->session->set_userdata('send_later', true);

						}

						redirect($redUrl);

						}else{

						set_alert('warning', 'something went wrong');

						$redUrl = admin_url('order');

						redirect($redUrl);

					}

					} else {

					if (!has_permission_new('orders', '', 'edit')) {

						access_denied('order');

					}

					$success = $this->order_model->OrderUpdate($order_data, $id);

					if ($success == false) {

						set_alert('warning', "Stock not available...");

						redirect(admin_url('order/order/' . $id));

						}else{

						set_alert('success', _l('updated_successfully', "Order"));

						redirect(admin_url('order/pending_orders2/' . $id));

					}

				}

			}

			if ($id == '') {

				$title                  = _l('create_new_order');

				$data['billable_tasks'] = [];

				} else {

				$order = $this->order_model->get2($id); // for edit order

				if (!$order) {

					blank_page(_l('order_not_found'));

				}

				$data['order']        = $order;

				// echo "<pre>";

					// print_r($order->items_free_dist);

				// die;

				$data['edit']           = true;

				$title = "Edit Order";

				$data['custitems_groups'] = $this->invoice_items_model->get_custitem_groups($order->AccountID);

			    $data['client_detail'] = $this->clients_model->get($order->AccountID);

				

			    $AccountID = "";

	            $data['ShippingDetails']  = $this->order_model->GetShippingAddress($order->AccountID2);

	            $data['customer_groups_name'] = $this->clients_model->get_customer_groups_name($data['client_detail']->DistributorType);

			}

			if ($this->input->get('customer_id')) {

				$data['customer_id'] = $this->input->get('customer_id');

			}

			

			$data['taxes'] = $this->taxes_model->get();

			$data['items_groups'] = $this->invoice_items_model->get_groups();

			$data['currencies'] = $this->currencies_model->get();

			

			$data['base_currency'] = $this->currencies_model->get_base_currency();

			$data['staff']     = $this->staff_model->get('', ['active' => 1]);

			$data['rootcompany'] = $this->clients_model->get_rootcompany();

			$data['item_data'] = $this->invoice_items_model->get2();

			// Customer groups

			$data['groups'] = $this->clients_model->get_groups();

			$data['PartyList'] = $this->clients_model->GetPartyList();

			// Get TCS per

			$this->load->model('tcs_master_model');

			$data['tcs'] = $this->tcs_master_model->get();

			$data['title']     = $title;

			$data['bodyclass'] = 'invoice';

			$this->load->view('admin/order/order', $data);

		}

		

		/* Get all invoice data used when user click on invoiec number in a datatable left side*/

		public function get_order_data_ajax($id)

		{

			

			if (!$id) {

				die(_l('invoice_not_found'));

			}

			

			$order = $this->order_model->get2($id);

			

			

			if (!$order) {

				echo "Order Not Found";

				die;

			}

			

			

			

			$data['order'] = $order;

			

			$this->load->view('admin/order/invoice_preview_template', $data);

		}

		

		public function apply_credits($invoice_id)

		{

			$total_credits_applied = 0;

			foreach ($this->input->post('amount') as $credit_id => $amount) {

				$success = $this->credit_notes_model->apply_credits($credit_id, [

				'invoice_id' => $invoice_id,

				'amount'     => $amount,

				]);

				if ($success) {

					$total_credits_applied++;

				}

			}

			

			if ($total_credits_applied > 0) {

				update_invoice_status($invoice_id, true);

				set_alert('success', _l('invoice_credits_applied'));

			}

			redirect(admin_url('order/list_orders/' . $invoice_id));

		}

		

		public function get_invoices_total()

		{

			if ($this->input->post()) {

				load_invoices_total_template();

			}

		}

		

		/* Record new inoice payment view */

		public function record_invoice_payment_ajax($id)

		{

			$this->load->model('payment_modes_model');

			$this->load->model('payments_model');

			$data['payment_modes'] = $this->payment_modes_model->get('', [

            'expenses_only !=' => 1,

			]);

			$data['invoice']  = $this->invoices_model->get($id);

			$data['payments'] = $this->payments_model->get_invoice_payments($id);

			$this->load->view('admin/invoices/record_payment_template', $data);

		}

		

		/* Record new inoice  */

		public function crate_invoice_by_ajax($id)

		{

			$this->load->model('payment_modes_model');

			$this->load->model('payments_model');

			$data['payment_modes'] = $this->payment_modes_model->get('', [

            'expenses_only !=' => 1,

			]);

			$order  = $this->order_model->get($id);

			//echo "<pre>";

			$addedfrom = !DEFINED('CRON') ? get_staff_user_id() : 0;

			$invoicedata=array(

			"sent"=>$order->sent,

			"datesend"=>$order->datesend,

			"clientid"=>$order->clientid,

			"deleted_customer_name"=>$order->deleted_customer_name,

			"order_id"=>$order->number,

			"order_type"=>$order->order_type,

			"dist_comp"=>$order->dist_comp,

			"dist_sale_agent"=>$order->dist_sale_agent,

			"prefix"=>'INV-',

			"number_format"=>$order->number_format,

			"datecreated"=>date('Y-m-d H:i:s'),

			"date"=>date('Y-m-d'),

			"currency"=>$order->currency,

			"subtotal"=>$order->subtotal,

			"total_tax"=>$order->total_tax,

			"total"=>$order->total,

			"total_cases"=>$order->total_cases,

			"adjustment"=>$order->adjustment,

			"addedfrom"=>$addedfrom,

			"hash"=>$order->hash,

			"status"=>$order->status,

			"allowed_payment_modes"=>$order->allowed_payment_modes,

			"token"=>$order->token,

			"discount_percent"=>$order->discount_percent,

			"discount_total"=>$order->discount_total,

			"discount_type"=>$order->discount_type,

			"sale_agent"=>$order->sale_agent,

			"billing_street"=>$order->billing_street,

			"billing_city"=>$order->billing_city,

			"billing_state"=>$order->billing_state,

			"billing_zip"=>$order->billing_zip,

			"billing_country"=>$order->billing_country,

			"shipping_street"=>$order->shipping_street,

			"shipping_state"=>$order->shipping_state,

			"shipping_city"=>$order->shipping_city,

			"shipping_zip"=>$order->shipping_zip,

			"shipping_country"=>$order->shipping_country,

			"include_shipping"=>$order->include_shipping,

			"show_shipping_on_invoice"=>$order->show_shipping_on_invoice,

			"show_quantity_as"=>$order->show_quantity_as,

			"subscription_id"=>$order->subscription_id,

			"short_link"=>$order->short_link,

			"project_id"=>$order->project_id

			);

			

            $items = $order->items;

            /*foreach ($items as $key => $value) {

				# code...

				echo $value["description"];

				echo "<br>";

				

			}*/

            //print_r($items);

            //echo $items[0]['rel_type'];

            //die;

            $this->db->insert(db_prefix() . 'invoices', $invoicedata);

            $invoice_id = $this->db->insert_id();

            if($invoice_id){

                

                $this->db->where('id', $invoice_id);

                $this->db->update(db_prefix() . 'invoices', [

				'number' => $invoice_id,

				]);

                

                foreach ($items as $key => $item) {

					# code...

					

					$itemdata=array(

					"rel_id"=>$invoice_id,

					"rel_type"=>$item['rel_type'],

					"description"=>$item['description'],

					"long_description"=>$item['long_description'],

					"hsn_code"=>$item['hsn_code'],

					"qty"=>$item['qty'],

					"pack_qty"=>$item['pack_qty'],

					"rate"=>$item['rate'],

					"total_amt"=>$item['total_amt'],

					"discount_amt"=>$item['discount_amt'],

					"taxable_amt"=>$item['taxable_amt'],

					"gst"=>$item['gst'],

					"gst_amt"=>$item['gst_amt'],

					"unit"=>$item['unit'],

					"grand_total"=>$item['grand_total'],

					"item_order"=>$item['item_order']

					);

					$this->db->insert(db_prefix() . 'itemable', $itemdata);

				}        

				

			}

			

			redirect(admin_url('order/get_order_data_ajax/' . $id));

			

		}

		

		/* This is where invoice payment record $_POST data is send */

		public function record_payment()

		{

			if (!has_permission_new('orders', '', 'create')) {

				access_denied('Record Payment');

			}

			if ($this->input->post()) {

				$this->load->model('payments_model');

				$id = $this->payments_model->process_payment($this->input->post(), '');

				if ($id) {

					set_alert('success', _l('invoice_payment_recorded'));

					redirect(admin_url('payments/payment/' . $id));

					} else {

					set_alert('danger', _l('invoice_payment_record_failed'));

				}

				redirect(admin_url('order/list_orders/' . $this->input->post('invoiceid')));

			}

		}

		

		/* Send invoice to email */

		public function send_to_email($id)

		{

			$canView = user_can_view_invoice($id);

			if (!$canView) {

				access_denied('Invoices');

				} else {

				if (!has_permission_new('orders', '', 'view') && !has_permission_new('orders', '', 'view_own') && $canView == false) {

					access_denied('Order');

				}

			}

			

			try {

				$statementData = [];

				if ($this->input->post('attach_statement')) {

					$statementData['attach'] = true;

					$statementData['from']   = to_sql_date($this->input->post('statement_from'));

					$statementData['to']     = to_sql_date($this->input->post('statement_to'));

				}

				

				$success = $this->invoices_model->send_invoice_to_client(

                $id,

                '',

                $this->input->post('attach_pdf'),

                $this->input->post('cc'),

                false,

                $statementData

				);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			// In case client use another language

			load_admin_language();

			if ($success) {

				set_alert('success', _l('invoice_sent_to_client_success'));

				} else {

				set_alert('danger', _l('invoice_sent_to_client_fail'));

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		/* Delete invoice payment*/

		public function delete_payment($id, $invoiceid)

		{

			if (!has_permission_new('payments', '', 'delete')) {

				access_denied('payments');

			}

			$this->load->model('payments_model');

			if (!$id) {

				redirect(admin_url('payments'));

			}

			$response = $this->payments_model->delete($id);

			if ($response == true) {

				set_alert('success', _l('deleted', _l('payment')));

				} else {

				set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));

			}

			redirect(admin_url('order/list_orders/' . $invoiceid));

		}

		

		/* Delete invoice */

		public function delete($id)

		{

			if (!has_permission_new('orders', '', 'delete')) {

				access_denied('order');

			}

			if (!$id) {

				redirect(admin_url('order/list_orders'));

			}

			$success = $this->invoices_model->delete($id);

			

			if ($success) {

				set_alert('success', _l('deleted', _l('invoice')));

				} else {

				set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));

			}

			if (strpos($_SERVER['HTTP_REFERER'], 'list_orders') !== false) {

				redirect(admin_url('order/list_orders'));

				} else {

				redirect($_SERVER['HTTP_REFERER']);

			}

		}

		

		public function delete_attachment($id)

		{

			$file = $this->misc_model->get_file($id);

			if ($file->staffid == get_staff_user_id() || is_admin()) {

				echo $this->invoices_model->delete_attachment($id);

				} else {

				header('HTTP/1.0 400 Bad error');

				echo _l('access_denied');

				die;

			}

		}

		

		/* Will send overdue notice to client */

		public function send_overdue_notice($id)

		{

			$canView = user_can_view_invoice($id);

			if (!$canView) {

				access_denied('Order');

				} else {

				if (!has_permission_new('orders', '', 'view') && !has_permission_new('orders', '', 'view_own') && $canView == false) {

					access_denied('Order');

				}

			}

			

			$send = $this->invoices_model->send_invoice_overdue_notice($id);

			if ($send) {

				set_alert('success', _l('invoice_overdue_reminder_sent'));

				} else {

				set_alert('warning', _l('invoice_reminder_send_problem'));

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		/* Generates invoice PDF and senting to email of $send_to_email = true is passed */

		public function pdf($id)

		{

			if (!$id) {

				redirect(admin_url('order/list_orders'));

			}

			

			$canView = user_can_view_invoice($id);

			if (!$canView) {

				access_denied('Order');

				} else {

				if (!has_permission_new('orders', '', 'view') && !has_permission_new('invoices', '', 'view_own') && $canView == false) {

					access_denied('Order');

				}

			}

			

			$invoice        = $this->invoices_model->get($id);

			$invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);

			$invoice_number = format_invoice_number($invoice->id);

			

			try {

				$pdf = invoice_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);

		}

		

		public function mark_as_sent($id)

		{

			if (!$id) {

				redirect(admin_url('order/list_orders'));

			}

			if (!user_can_view_invoice($id)) {

				access_denied('Invoice Mark As Sent');

			}

			

			$success = $this->invoices_model->set_invoice_sent($id, true);

			

			if ($success) {

				set_alert('success', _l('invoice_marked_as_sent'));

				} else {

				set_alert('warning', _l('invoice_marked_as_sent_failed'));

			}

			

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function get_due_date()

		{

			if ($this->input->post()) {

				$date    = $this->input->post('date');

				$duedate = '';

				if (get_option('invoice_due_after') != 0) {

					$date    = to_sql_date($date);

					$d       = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));

					$duedate = _d($d);

					echo $duedate;

				}

			}

		}

		public function export_order_list()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$data = array(

				'from_date' => $this->input->post('from_date'),

				'to_date'  => $this->input->post('to_date')

				);

				$data = $this->order_model->load_data_for_order($data);  

				$this->load->model('sale_reports_model');

				$selected_company_details    = $this->sale_reports_model->get_company_detail();

				

				$writer = new XLSXWriter();

				//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

				//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

				//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

				//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				$msg = "Filtes Date From ".$this->input->post('from_date')." To " .$this->input->post('to_date');

				$filter = array($msg);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter);

				

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

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$set_col_tk = [];

				$set_col_tk["Order No."] =  'Order No.';

				$set_col_tk["Order Date"] = 'Order Date';

				$set_col_tk["SalesID"] = 'SalesID';

				$set_col_tk["SalesDate"] = 'SalesDate';

				$set_col_tk["Challan"] = 'Challan';

				$set_col_tk["Party Name"] = 'Party Name';

				$set_col_tk["OrderAmt"] = 'OrderAmt';

				$set_col_tk["BillAmt"] = 'BillAmt';

				$set_col_tk["Order Type"] = 'Order Type';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				$selected_company = $this->session->userdata('root_company');

				$orderSum = 0;

				$saleSum = 0;

				foreach ($data as $k => $value) {

					

					$list_add = [];

					$list_add[] = $value["OrderID"];

					$orderdate = _d(substr($value["Transdate"],0,10));

					$list_add[] = $orderdate;

					$list_add[] = $value["SalesID"];

					$saledate = _d(substr($value["SaleDate"],0,10));

					$list_add[] = $saledate;

					$list_add[] = $value["ChallanID"];

					$account_name = get_account_name($value['AccountID'],$selected_company);

					$list_add[] = $account_name->company;

					$list_add[] = $value['OrderAmt'];

					$orderSum += $value['OrderAmt'];

					$list_add[] = $value['BillAmt'];

					$saleSum += $value['BillAmt'];

					$list_add[] = $value['order_type'];

					

					$writer->writeSheetRow('Sheet1', $list_add);

					

				}

				

    	        $list_add = [];

    			$list_add[] = 'Total';

    			$list_add[] = '';

    			$list_add[] = '';

    			$list_add[] = '';

    			$list_add[] = '';

    			$list_add[] = '';

    			$list_add[] = $orderSum;

    			$list_add[] = $saleSum;

    			$list_add[] = '';

    			

    			$writer->writeSheetRow('Sheet1', $list_add);

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'SaleReport.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		public function load_data_for_order(){

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date')

			);

			$selected_company = $this->session->userdata('root_company');

			$data1 = $this->order_model->load_data_for_order($data);

			// echo count($data1);die;

			$html ='';

			$ordersum = 0;

			$salesum = 0;

			$sr = 1;

			foreach($data1 as $value){

				$html.= '<tr>';

                $numberOutput = '<a href="' . admin_url('order/order_details/' . $value['OrderID']) . '" target="_blank" style="font-size: 11px;">' . $value['OrderID'] . '</a>';

                $numberOutput1 = '<a href="' . admin_url('order/order/' . $value['OrderID']) . '" target="_blank" style="font-size: 11px;">' . $value['OrderID'] . '</a>';

				$html.= '<td style="text-align:center;">'.$sr.'</td>';

				$html.= '<td>'.$numberOutput1.'</td>';

				$html.= '<td style="text-align:center;">'.date("d/m/Y", strtotime(substr($value['Transdate'],0,10))).'</td>';

				$html.= '<td style="text-align:center;">'.$value['SalesID'].'</td>';

				$html.= '<td style="text-align:center;">'.date("d/m/Y", strtotime(substr($value['SaleDate'],0,10))).'</td>';

				

				

				$html.= '<td style="text-align:center;">'.$value['ChallanID'].'</td>';

				$account_name = get_account_name($value['AccountID'],$selected_company);

				$html.= '<td>'.$account_name->company.'</td>';

				$html.= '<td style="text-align:right;">'.$value['OrderAmt'].'</td>';

				$ordersum += $value['OrderAmt'];

				$html.= '<td style="text-align:right;">'.$value['BillAmt'].'</td>';

				$salesum += $value['BillAmt'];

				$html.= '<td style="text-align:center;">'.$value['order_type'].'</td>';

				

				$html.= '</tr>';

				$sr++;

			}

			$html.= '<tr>';

			$html.= '<td colspan="7">Total</td>';

			$html.= '<td style="text-align:right;">'.$ordersum.'</td>';

			$html.= '<td style="text-align:right;">'.$salesum.'</td>';

			$html.= '<td></td>';

			$html.= '</tr>';

			echo json_encode($html);

		}

		

		public function export_pending_order()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$data = array(

				'from_date' => $this->input->post('from_date'),

				'dates' => $this->input->post('dates'),

				'order_type'  => $this->input->post('order_type'),

				'state'  => $this->input->post('state'),

				'dist_type'  => $this->input->post('dist_type'),

				'selected_ids'  => $this->input->post('selected_ids')

				);

				if(empty($data['selected_ids'])){

					$data_array = $this->order_model->load_data($data); 

					}else{

					$data_array = $this->order_model->load_data2($data); 

				}

				

				$this->load->model('sale_reports_model');

				$selected_company_details    = $this->sale_reports_model->get_company_detail();

				

				$writer = new XLSXWriter();

				//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

				//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

				//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

				//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 11);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 11);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				$distributor_id = $this->input->post('dist_type');

				$state_id = $this->input->post('state');

				$order_type = $this->input->post('order_type');

				$dates = $this->input->post('dates');

				

				$data_state_name  = $this->db->get_where('tblxx_statelist',array('short_name'=>$state_id))->row_array(); 

				$data_distributor_name  = $this->db->get_where('tblcustomers_groups',array('id'=>$distributor_id))->row_array();

				

				$msg = "Pending Order For Date : ".$dates." State " .$data_state_name["state_name"]. " Order type :".$order_type.", Distributor Type : ".$data_distributor_name["name"];

				$filter = array($msg);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 11);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter);

				

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

				$writer->writeSheetRow('Sheet1', $list_add);

				

				

				$set_col_tk = [];

				$set_col_tk["SrNo"] =  'Sr. No.';

				$set_col_tk["OrderId"] =  'OrderID';

				$set_col_tk["Transdate"] = 'Transdate';

				$set_col_tk["Expected Date"] = 'Expected Date';

				$set_col_tk["SONAME"] = 'SO Name';

				$set_col_tk["Bill To Party"] = 'Bill To Party';

				$set_col_tk["Ship To Party"] = 'Ship To Party';

				$set_col_tk["Station"] = 'Station';

				$set_col_tk["Dist Type"] = 'Dist Type';

				$set_col_tk["State"] = 'State';

				$set_col_tk["Close BalAmt"] = 'Closing Balance Amt';

				$set_col_tk["orderAmt"] = 'Order Amt';

				$set_col_tk["UserID"] = 'UserID';

				$set_col_tk["status"] = 'Status';

				$set_col_tk["Remark (if any)"] = 'Remark (if any)';

				$writer_header = $set_col_tk;

				$writer->writeSheetRow('Sheet1', $writer_header);

				

				$j = 4;

				$i = 1;

				$BalTotal = 0;

				$OrderSum = 0;

				foreach ($data_array as $k => $value) {

					$bal_new = $value["bal1"] + $value["balance"];

					$BalTotal += $bal_new;

					$list_add = [];

					$list_add[] = $i;

					$list_add[] = $value["OrderID"];

					$date = _d(substr($value["Transdate"],0,10));

					$list_add[] = $date;

					$Dispatchdate = _d(substr($value["Dispatchdate"],0,10));

					$list_add[] = $Dispatchdate;

					$list_add[] = $value["SOID"];

					$list_add[] = $value["AccountName"];

					$list_add[] = $value["ShipToAccountName"];

					

					$list_add[] = $value["StationName"];

					$list_add[] = $value["dist_Type"];

					$list_add[] = $value["StateName"];

					$list_add[] = $bal_new;

					if($value["OrderStatus"] == "C"){

						$cc = "checked";

						$c = "Yes";

						$status = "Cancel";

						$UserID = $value["CancelStaffName"];

						}else{

						$cc = "";

						$c = "";

						$status = "Open";

						$UserID = $value["CreateStaffName"];

					}

					$list_add[] = $value["OrderAmt"];

					$list_add[] = $UserID;

					$OrderSum += $value["OrderAmt"];

					

					$list_add[] = $status;

					$list_add[] = $value["remark"];

					

					$writer->writeSheetRow('Sheet1', $list_add);

					$j++;

					$i++;

				}

				

				// Total row

				$list_add = [];

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "Total";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = $BalTotal;

				$list_add[] = $OrderSum;

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$writer->writeSheetRow('Sheet1', $list_add);

				

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'PendingOrder_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		public function export_pending_order_item()

		{

			if(!class_exists('XLSXReader_fin')){

				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');

			}

			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			

			if($this->input->post()){

				

				$data = array(

				'from_date' => $this->input->post('from_date'),

				'dates' => $this->input->post('dates'),

				'order_type'  => $this->input->post('order_type'),

				'state'  => $this->input->post('state'),

				'dist_type'  => $this->input->post('dist_type'),

				'selected_ids'  => $this->input->post('selected_ids')

				);

				if(empty($data['selected_ids'])){

					$data_array = $this->order_model->load_data($data); 

					}else{

					$data_array = $this->order_model->load_data2($data); 

				}

				

				$this->load->model('sale_reports_model');

				$selected_company_details    = $this->sale_reports_model->get_company_detail();

				

				$writer = new XLSXWriter();

				//$style_c = array('fill' => '#FFFFFF', 'height'=>30, 'font-size' => 18, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

				//$style = array('fill' => '#FFFFFF', 'height'=>25, 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000', 'text-align' => 'center', 'font-weight' => '700');

				//$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

				//$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

				

				$company_name = array($selected_company_details->company_name);

				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 11);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_name);

				

				$address = $selected_company_details->address;

				$company_addr = array($address,);

				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 11);  //merge cells

				$writer->writeSheetRow('Sheet1', $company_addr);

				

				$distributor_id = $this->input->post('dist_type');

				$state_id = $this->input->post('state');

				$order_type = $this->input->post('order_type');

				$dates = $this->input->post('dates');

				

				$data_state_name  = $this->db->get_where('tblxx_statelist',array('short_name'=>$state_id))->row_array(); 

				$data_distributor_name  = $this->db->get_where('tblcustomers_groups',array('id'=>$distributor_id))->row_array();

				

				$msg = "Pending Order For Date : ".$dates." State " .$data_state_name["state_name"]. " Order type :".$order_type.", Distributor Type : ".$data_distributor_name["name"];

				$filter = array($msg);

				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 11);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter);

				

				

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

				$writer->writeSheetRow('Sheet1', $list_add);

				$j++;

				$j++;

				$filter = array("Pending Order Item Data");

				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells

				$writer->writeSheetRow('Sheet1', $filter);

				

				$set_col_tk1 = [];

				$set_col_tk1["ItemID"] =  'ItemID';

				$set_col_tk1["ItemName"] =  'ItemName';

				$set_col_tk1["Pack"] =  'Pack';

				$set_col_tk1["Order Qty (CS/CR)"] =  'Order Qty (CS/CR)';

				$set_col_tk1["Order Qty (Unit)"] =  'Order Qty (Unit)';

				$set_col_tk1["Bowl Qty"] =  'Bowl Qty';

				$set_col_tk1["CurrStock"] =  'CurrStock';

				$set_col_tk1["Production Plan"] =  'Production Plan';

				$set_col_tk1["Remark"] =  'Remark';

				$writer_header1 = $set_col_tk1;

				$writer->writeSheetRow('Sheet1', $writer_header1);

				

				$data = array(

				'from_date' => $this->input->post('from_date'),

				'dates' => $this->input->post('dates'),

				'order_type'  => $this->input->post('order_type'),

				'state'  => $this->input->post('state'),

				'dist_type'  => $this->input->post('dist_type'),

				'selected_ids'  => $this->input->post('selected_ids')

				);

				if(empty($data['selected_ids'])){

					$data_items = $this->order_model->load_data_items($data);

					}else{

					$data_items = $this->order_model->load_data_items2($data); 

				}

				

				$total_cases = 0;

				$total_unitqty = 0;

				$total_bowlqty = 0;

				$total_taxableamt = 0.00;

				$total_netamt = 0.00;

				foreach ($data_items as $k1 => $value1) {

					if($value1["NetOrderAmt"] == "0.00"){

						

						}else{

						

						$list_add = [];

						$list_add[] = $value1["Item_code"];

						$list_add[] = $value1["description"];

						$list_add[] = $value1["CaseQty"];

						$ordqty = $value1["OrderQty"] / $value1["CaseQty"];

						$list_add[] = $ordqty;

						$list_add[] = $value1["OrderQty"];

						if($value1["BowlQty"] != 'NA'){

							$bowlqty = $value1["OrderQty"] / $value1["BowlQty"];

							$total_bowlqty += $total_bowlqty;

							}else{

							$bowlqty = 'NA';

						}

						$list_add[] = $bowlqty;

						$list_add[] = round($value1["StockBal"], 2);

						$total_taxableamt += $value1["OrderAmt"];

						$total_netamt += $value1["NetOrderAmt"];

						$total_cases += $ordqty;

						$total_unitqty += $value1["OrderQty"];

						

						$list_add[] = '';

						$list_add[] = '';

						$writer->writeSheetRow('Sheet1', $list_add);

					}

					

				}

				$total_tax = $total_netamt - $total_taxableamt;

				// Total row

				$list_add = [];

				$list_add[] = "";

				$list_add[] = "Total";

				$list_add[] = "";

				$list_add[] = $total_cases;

				$list_add[] = $total_unitqty;

				$list_add[] = $total_bowlqty;

				$list_add[] = "";

				$list_add[] = "";

				$list_add[] = "";

				$writer->writeSheetRow('Sheet1', $list_add);

				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');

				foreach($files as $file){

					if(is_file($file)) {

						unlink($file); 

					}

				}

				$filename = 'PendingOrderItem_Report.xlsx';

				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));

				echo json_encode([

    			'site_url'          => site_url(),

    			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,

				]);

				die;

			}

		}

		

		public function LimitExceededOrders()

		{

			if (!has_permission_new('LimitExceededOrders', '', 'view')) {

				access_denied('orders');

			}

			close_setup_menu();

			$data['title']                = "Limit Exceeded Orders";

			$data['PlantDetail'] = $this->order_model->get_selected_company_details();

			$data['Staff'] = $this->order_model->LimitApproverStaff();

			$data['PartyList'] = $this->clients_model->GetPartyList();

			$data['bodyclass']            = 'invoices-total-manual';

			$this->load->view('admin/order/LimitExceededOrders', $data);

		}

		

		public function GetLimitExceededOrders()

		{

			$data = array(

			'from_date' => $this->input->post('from_date'),

			'to_date'  => $this->input->post('to_date'),

			'Status'  => $this->input->post('Status'),

			'Approver'  => $this->input->post('Approver'),

			'AccountID'  => $this->input->post('AccountID'),

			);

			$result = $this->order_model->GetLimitExceededOrders($data);

			$html ='';

			$ordersum = 0;

			$salesum = 0;

			$sr = 1;

			foreach($result as $value){

				$html.= '<tr>';

				$html.= '<td style="text-align:center;">'.$sr.'</td>';

				if($value['credit_apply'] == 'Y'){

					$html.= '<td style="text-align:center;"><input type="checkbox" name="OrderID" value="'.$value['OrderID'].'"></td>';

				}else{

					$html.= '<td style="text-align:center;"></td>';

				}

				$html.= '<td style="text-align:center;"><a href="'.admin_url('order/order/').$value['OrderID'].'" target="_blank">'.$value['OrderID'].'</a></td>';

				$html.= '<td style="text-align:center;">'._d(substr($value['Transdate'],0,10)).'</td>';

				$html.= '<td style="text-align:center;">'.$value['AccountName'].'</td>';

				$html.= '<td style="text-align:center;">'.$value['ShipToAccountName'].'</td>';

				$html.= '<td style="text-align:center;">'.$value['OrderAmt'].'</td>';

				$html.= '<td style="text-align:center;">'.$value['ApprovedBy'].'</td>';

				

				$html.= '</tr>';

				$sr++;

			}

			echo json_encode($html);

		}

		

		public function UpdateLimitExceededOrders()

		{

			$order_ids = $this->input->post('order_ids');

			

			$i=0;

			foreach($order_ids as $key => $OrderID){

				$OrderData = array(

				"credit_apply" => "N",

				"credit_approved_by" => $this->session->userdata('username'),

				);

				// for history

				$this->db->where(db_prefix() . 'ordermaster.OrderID', $OrderID);

				if($this->db->update(db_prefix() . 'ordermaster', $OrderData)){

					$i++;

				}

			}

			if($i > 0){

				echo json_encode(true);

				}else{

				echo json_encode(false);

			}

			
		}

	}

