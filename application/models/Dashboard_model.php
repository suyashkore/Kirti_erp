<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     * Used in home dashboard page
     * Return all upcoming events this week
     */
    public function get_upcoming_events()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday this week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday this week'));

        $this->db->where("(start BETWEEN '$monday_this_week' and '$sunday_this_week')");
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');
        $this->db->order_by('start', 'desc');
        $this->db->limit(6);

        return $this->db->get(db_prefix() . 'events')->result_array();
    }

    /**
     * @param  integer (optional) Limit upcoming events
     * @return integer
     * Used in home dashboard page
     * Return total upcoming events next week
     */
    public function get_upcoming_events_next_week()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday next week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday next week'));
        $this->db->where("(start BETWEEN '$monday_this_week' and '$sunday_this_week')");
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');

        return $this->db->count_all_results(db_prefix() . 'events');
    }

    /**
     * @param  mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays weekly payment statistics (chart)
     */
    public function get_weekly_payments_statistics($currency)
    {
        $all_payments                 = [];
        $has_permission_payments_view = staff_can('view',  'payments');
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE)');
        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Current week
        $all_payments[] = $this->db->get()->result_array();
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ');

        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Last Week
        $all_payments[] = $this->db->get()->result_array();

        $chart = [
            'labels'   => get_weekdays(),
            'datasets' => [
                [
                    'label'           => _l('this_week_payments'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
                [
                    'label'           => _l('last_week_payments'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor'     => '#c53da9',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
            ],
        ];


        for ($i = 0; $i < count($all_payments); $i++) {
            foreach ($all_payments[$i] as $payment) {
                $payment_day = date('l', strtotime($payment['date']));
                $x           = 0;
                foreach (get_weekdays_original() as $day) {
                    if ($payment_day == $day) {
                        $chart['datasets'][$i]['data'][$x] += $payment['amount'];
                    }
                    $x++;
                }
            }
        }

        return $chart;
    }


    /**
     * @param  mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays monthly payment statistics (chart)
     */
    public function get_monthly_payments_statistics($currency)
    {
        $all_payments                 = [];
        $has_permission_payments_view = staff_can('view',  'payments');
        $this->db->select('SUM(amount) as total, MONTH(' . db_prefix() . 'invoicepaymentrecords.date) as month');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEAR(' . db_prefix() . 'invoicepaymentrecords.date) = YEAR(CURRENT_DATE)');
        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        $this->db->group_by('month');

        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        $all_payments = $this->db->get()->result_array();

        for ($i = 1; $i <= 12; $i++) {
            if (!isset($all_payments[$i])) {
                $all_payments[$i]['total'] = 0;
                $all_payments[$i]['month'] = $i;
            }
            $all_payments[$i]['label'] = _l(date("F", mktime(0, 0, 0, $i, 1)));
        }
        usort($all_payments, function($a, $b) {
            return (int) $a['month'] <=> (int) $b['month'];
        });

        $chart = [
            'labels'   => array_column($all_payments, 'label'),
            'datasets' => [
                [
                    'label'           => _l('report_sales_type_income'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => array_column($all_payments, 'total'),
                ],
            ],
        ];
        return $chart;
    }

    public function projects_status_stats()
    {
        $this->load->model('projects_model');
        $statuses = $this->projects_model->get_project_statuses();
        $colors   = get_system_favourite_colors();

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $has_permission = staff_can('view',  'projects');
        $sql            = '';
        foreach ($statuses as $status) {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'projects';
            $sql .= ' WHERE status=' . $status['id'];
            if (!$has_permission) {
                $sql .= ' AND id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }

        $result = [];
        if ($sql != '') {
            // Remove the last UNION ALL
            $sql    = substr($sql, 0, -10);
            $result = $this->db->query($sql)->result();
        }

        foreach ($statuses as $key => $status) {
            array_push($_data['statusLink'], admin_url('projects?status=' . $status['id']));
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $result[$key]->total);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }

    public function leads_status_stats()
    {
        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        $result = get_leads_summary();

        foreach ($result as $status) {
            if ($status['color'] == '') {
                $status['color'] = '#737373';
            }
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            if (!isset($status['junk']) && !isset($status['lost'])) {
                array_push($_data['statusLink'], admin_url('leads?status=' . $status['id']));
            }
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $status['total']);
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by department (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_department()
    {
        $this->load->model('departments_model');
        $departments = $this->departments_model->get();
        $colors      = get_system_favourite_colors();
        $chart       = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];

        $i = 0;
        foreach ($departments as $department) {
            if (!is_admin()) {
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    $departments_ids      = [];
                    if (count($staff_deparments_ids) == 0) {
                        $departments = $this->departments_model->get();
                        foreach ($departments as $department) {
                            array_push($departments_ids, $department['departmentid']);
                        }
                    } else {
                        $departments_ids = $staff_deparments_ids;
                    }
                    if (count($departments_ids) > 0) {
                        $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                    }
                }
            }
            $this->db->where_in('status', [
                1,
                2,
                4,
            ]);

            $this->db->where('department', $department['departmentid']);
            $this->db->where(db_prefix() . 'tickets.merged_ticket_id IS NULL', null, false);
            $total = $this->db->count_all_results(db_prefix() . 'tickets');

            if ($total > 0) {
                $color = '#333';
                if (isset($colors[$i])) {
                    $color = $colors[$i];
                }
                array_push($chart['labels'], $department['name']);
                array_push($_data['backgroundColor'], $color);
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
                array_push($_data['data'], $total);
            }
            $i++;
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by status (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_status()
    {
        $this->load->model('tickets_model');
        $statuses             = $this->tickets_model->get_ticket_status();
        $_statuses_with_reply = [
            1,
            2,
            4,
        ];

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        foreach ($statuses as $status) {
            if (in_array($status['ticketstatusid'], $_statuses_with_reply)) {
                if (!is_admin()) {
                    if (get_option('staff_access_only_assigned_departments') == 1) {
                        $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                        $departments_ids      = [];
                        if (count($staff_deparments_ids) == 0) {
                            $departments = $this->departments_model->get();
                            foreach ($departments as $department) {
                                array_push($departments_ids, $department['departmentid']);
                            }
                        } else {
                            $departments_ids = $staff_deparments_ids;
                        }
                        if (count($departments_ids) > 0) {
                            $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                        }
                    }
                }

                $this->db->where('status', $status['ticketstatusid']);
                $this->db->where(db_prefix() . 'tickets.merged_ticket_id IS NULL', null, false);
                $total = $this->db->count_all_results(db_prefix() . 'tickets');
                if ($total > 0) {
                    array_push($chart['labels'], ticket_status_translate($status['ticketstatusid']));
                    array_push($_data['statusLink'], admin_url('tickets/index/' . $status['ticketstatusid']));
                    array_push($_data['backgroundColor'], $status['statuscolor']);
                    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['statuscolor'], -20));
                    array_push($_data['data'], $total);
                }
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }
    
    // custom code start
    
    //============================== Top Five Vendor List ==========================
		public function TopFiveVendor()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = '20'.$fy.'-04-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = [ '#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00'];
			
			$this->db->select('tblpurchasemaster.AccountID, SUM(tblpurchasemaster.Invamt) as TotalPurchase,tblclients.company as company_name');
			$this->db->join('tblclients', 'tblclients.AccountID = tblpurchasemaster.AccountID AND tblclients.PlantID = tblpurchasemaster.PlantID');
			$this->db->where('tblpurchasemaster.PlantID',$selected_company);
			$this->db->where('tblpurchasemaster.FY',$fy);
			$this->db->where('tblpurchasemaster.Transdate >=', $minvalue);
			$this->db->where('tblpurchasemaster.Transdate <=', $maxvalue);
			$this->db->group_by('tblpurchasemaster.AccountID');
			$this->db->order_by("TotalPurchase", "DESC");
			$this->db->limit(5);
			$VendorWisePurchase = $this->db->get(db_prefix().'purchasemaster')->result_array();
			
			$color_index=0;
			foreach ($VendorWisePurchase as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
    				'name' 		=> $value['company_name'],
    				'color' 	=> $color_data[$color_index],
    				'y' 		=>	(int)$value['TotalPurchase'],
    				'z' 		=> 100,
    				'label' 		=> "Purchase Amt"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
    				'name' 		=> $value['company_name'],
    				'color' 	=> $color_data[$color_index],
    				'y' 		=> (int)$value['TotalPurchase'],
    				'z' 		=> 100,
    				'label' 		=> "Purchase Amt"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		//============================= Top Five Purchase Items ========================
		public function TopFivePurchaseItems()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = '20'.$fy.'-04-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = ['#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70' ,
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			
			$this->db->select('tblhistory.ItemID, SUM(BilledQty/CaseQty) as TotalPurchaseQty,tblitems.description as description_name,tblhistory.ItemID');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate2 >=', $minvalue);
			$this->db->where('tblhistory.TType ', 'P');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblitems.MainGrpID ', '2');
			//$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.ItemID');
			$this->db->order_by("TotalPurchaseQty", "DESC");
			$this->db->limit(5);
			$TopFivePurchaseItems = $this->db->get(db_prefix().'history')->result_array();
			
			$color_index=0;
			foreach ($TopFivePurchaseItems as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
    				'name' 		=> $value['description_name'],
    				'ItemID' 		=> $value['ItemID'],
    				'color' 	=> $color_data[$color_index],
    				'y' 		=>	(int)$value['TotalPurchaseQty'],
    				'z' 		=> 100,
    				'label' 		=> "Qty"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
    				'name' 		=> $value['description_name'],
    				'ItemID' 	=> $value['ItemID'],
    				'color' 	=> $color_data[$color_index],
    				'y' 		=> (int)$value['TotalPurchaseQty'],
    				'z' 		=> 100,
    				'label' 		=> "Qty"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		public function TopFivePurchaseItemsPM()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = '20'.$fy.'-04-01 00:00:00';
			$maxvalue = date('Y-m-d')." 23:59:59";
			$chart = [];
			$color_data = ['#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
			'#63b598', '#ce7d78', '#ea9e70' ,
			'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];
			
			$this->db->select('tblhistory.ItemID, SUM(BilledQty/CaseQty) as TotalPurchaseQty,tblitems.description as description_name,tblhistory.ItemID');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate2 >=', $minvalue);
			$this->db->where('tblhistory.TType ', 'P');
			$this->db->where('tblhistory.TType2 ', 'Order');
			$this->db->where('tblitems.MainGrpID ', '3');
			//$this->db->where('tblhistory.TransID IS NOT NULL');
			$this->db->group_by('tblhistory.ItemID');
			$this->db->order_by("TotalPurchaseQty", "DESC");
			$this->db->limit(5);
			$TopFivePurchaseItems = $this->db->get(db_prefix().'history')->result_array();
			
			$color_index=0;
			foreach ($TopFivePurchaseItems as $key => $value) {
				if(isset($color_data[$color_index])){
					array_push($chart, [
    				'name' 		=> $value['description_name'],
    				'ItemID' 		=> $value['ItemID'],
    				'color' 	=> $color_data[$color_index],
    				'y' 		=>	(int)$value['TotalPurchaseQty'],
    				'z' 		=> 100,
    				'label' 		=> "Qty"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
    				'name' 		=> $value['description_name'],
    				'ItemID' 	=> $value['ItemID'],
    				'color' 	=> $color_data[$color_index],
    				'y' 		=> (int)$value['TotalPurchaseQty'],
    				'z' 		=> 100,
    				'label' 		=> "Qty"
					]);
				}
				$color_index++;
			}
			return $chart;
		}
		
		public function total_staff(){
			
			$selected_company = $this->session->userdata('root_company'); 
			$this->db->select();
			$this->db->from(db_prefix() . 'staff');
			$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);
			// $this->db->where(db_prefix() . 'staff.SubActGroupID', '1000054');
			return $this->db->get()->result_array();
		}
		public function total_Active_staff(){
			
			$selected_company = $this->session->userdata('root_company'); 
			$this->db->select();
			$this->db->from(db_prefix() . 'staff');
			$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);
			// $this->db->where(db_prefix() . 'staff.SubActGroupID', '1000054');
			$this->db->where(db_prefix() . 'staff.active', '1');
			return $this->db->get()->result_array();
		}
		public function total_customer(){
			
			$selected_company = $this->session->userdata('root_company'); 
			$this->db->select();
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.SubActGroupID', '1000012');
			return $this->db->get()->result_array();
		}
		public function total_Active_customer(){
			
			$selected_company = $this->session->userdata('root_company'); 
			$this->db->select();
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.SubActGroupID', '1000012');
			$this->db->where(db_prefix() . 'clients.active', '1');
			return $this->db->get()->result_array();
		}
		public function total_vendor(){
			
			$selected_company = $this->session->userdata('root_company'); 
			
			$SubActGroupID = array('100023');
			$this->db->select('GROUP_CONCAT(QUOTE(SubActGroupID)) as SubActGroupIDs');
			$this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $SubActGroupID);
			$this->db->where(db_prefix() . 'accountgroupssub.IsVendor', 'Y');
			$Data = $this->db->get('tblaccountgroupssub')->row();
			
			$commaSeparatedSubActGroupIDs = $Data->SubActGroupIDs;
			
			$SubActGroupIDsArray = explode(',', str_replace("'", '', $commaSeparatedSubActGroupIDs));
			$this->db->select();
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'clients.SubActGroupID', $SubActGroupIDsArray);
			return $this->db->get()->result_array();
		}
		public function total_Active_vendor(){
			$selected_company = $this->session->userdata('root_company'); 
			
			$SubActGroupID = array('100023');
			$this->db->select('GROUP_CONCAT(QUOTE(SubActGroupID)) as SubActGroupIDs');
			$this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $SubActGroupID);
			$this->db->where(db_prefix() . 'accountgroupssub.IsVendor', 'Y');
			$Data = $this->db->get('tblaccountgroupssub')->row();
			
			$commaSeparatedSubActGroupIDs = $Data->SubActGroupIDs;
			
			$SubActGroupIDsArray = explode(',', str_replace("'", '', $commaSeparatedSubActGroupIDs));
			
			$this->db->select();
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'clients.SubActGroupID', $SubActGroupIDsArray);
			$this->db->where(db_prefix() . 'clients.active', '1');
			return $this->db->get()->result_array();
		}
		public function total_ledgerAcc(){
			
			$selected_company = $this->session->userdata('root_company'); 
			
			$SubActGroupID = array('100023');
			$this->db->select('GROUP_CONCAT(QUOTE(SubActGroupID)) as SubActGroupIDs');
			$this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $SubActGroupID);
			$this->db->where(db_prefix() . 'accountgroupssub.IsVendor', 'Y');
			$Data = $this->db->get('tblaccountgroupssub')->row();
			
			$commaSeparatedSubActGroupIDs = $Data->SubActGroupIDs;
			
			$SubActGroupIDsArray = explode(',', str_replace("'", '', $commaSeparatedSubActGroupIDs));
			$SubActGroupIDsArray[] = '1000012';
			
			$this->db->select();
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_not_in(db_prefix() . 'clients.SubActGroupID', $SubActGroupIDsArray);
			return $this->db->get()->result_array();
		}
		public function total_Active_ledgerAcc(){
			
			$selected_company = $this->session->userdata('root_company'); 
			
			$SubActGroupID = array('100023');
			$this->db->select('GROUP_CONCAT(QUOTE(SubActGroupID)) as SubActGroupIDs');
			$this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $SubActGroupID);
			$this->db->where(db_prefix() . 'accountgroupssub.IsVendor', 'Y');
			$Data = $this->db->get('tblaccountgroupssub')->row();
			
			$commaSeparatedSubActGroupIDs = $Data->SubActGroupIDs;
			
			$SubActGroupIDsArray = explode(',', str_replace("'", '', $commaSeparatedSubActGroupIDs));
			$SubActGroupIDsArray[] = '1000012';
			// print_r($SubActGroupIDsArray);die;
			$this->db->select();
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_not_in(db_prefix() . 'clients.SubActGroupID', $SubActGroupIDsArray);
			$this->db->where(db_prefix() . 'clients.active', '1');
			return $this->db->get()->result_array();
		}
		public function Prod_VS_Sales($filterdata)
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
			
			
			
			if($SubGroup){
			    $this->db->select(db_prefix().'production_stage.ItemID, SUM(tblproduction_stage.Qty) as total_qty,'.db_prefix().'items.description as description_name');
				}else{
			    $this->db->select(db_prefix().'items_sub_groups.id as ItemID, SUM(tblproduction_stage.Qty) as total_qty,'.db_prefix().'items_sub_groups.name as description_name');
			}
			$this->db->join('tblproduction', 'tblproduction.pro_order_id = tblproduction_stage.ProductionID');
			$this->db->join('tblitems', 'tblitems.item_code = tblproduction_stage.ItemID');
			if($SubGroup){
				
				}else{
			    $this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				$this->db->group_by('tblproduction_stage.ItemID');
				}else{
			    $this->db->group_by('tblitems.SubGrpID1');
			}
			
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			
			$this->db->where('tblproduction.PlantID', $selected_company);
			$this->db->where('tblproduction.FY', $fy);
			
			$this->db->where('tblproduction_stage.Stage', "Packing");
			
			$this->db->where("tblproduction_stage.TransDate BETWEEN '$from_date' AND '$to_date'");
			$this->db->order_by("total_qty", "DESC");
			$this->db->limit($ItemCount);
			$TopProduction = $this->db->get('tblproduction_stage')->result_array();
			$group_byList = array();
			foreach($TopProduction as $val){
			    array_push($group_byList,$val["ItemID"]);
			}
			$i=0;
			foreach ($TopProduction as $key => $value) {
				array_push($Production, [
				'name' 		=> $value['description_name'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				$i++;
			}
			
			// Sale Qty 
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
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				if($group_byList){
			        $this->db->where_in('tblhistory.ItemID', $group_byList);
				}
				$this->db->group_by('tblhistory.ItemID');
				}else{
			    if($group_byList){
			        $this->db->where_in('tblitems.SubGrpID1', $group_byList);
				}
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
			
			$i=0;
			foreach ($TopItem as $key => $value) {
				array_push($chart, [
				'name' 		=> $value['description_name'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				$i++;
			}
			
			
			$data = [
			'Sales' => $chart,
			'Production' => $Production,
			];
			
			return $data;
		}
		
		public function Purchase_VS_Sales($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			if(empty($filterdata["from_date"])){
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
				}else{
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
			}
			
			$ItemCount = $filterdata["MaxCount"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$Items = $filterdata["Items"];
			
			$chart = [];
			$Purchase = [];
			$OQtyChart = [];
			
			$totalOQtyMap = [];
			
			if($SubGroup){
			    $this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items.description as description_name,tblitems.SubGrpID1 AS SubGroupID');
				}else{
			    $this->db->select(db_prefix().'items_sub_groups.id as ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items_sub_groups.name as description_name,tblitems_sub_groups.id AS SubGroupID');
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
			$this->db->where('tblhistory.TType ', 'P');
			$this->db->where('tblhistory.TType2 ', 'Purchase');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblitems.MainGrpID','1');
			
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
			$TopPurchase = $this->db->get('tblhistory')->result_array();
			$i=0;
			
			$SubGroup_arr = array();
			foreach ($TopPurchase as $key => $value) {
				array_push($SubGroup_arr,$value['ItemID']);
				array_push($Purchase, [
				'name' 		=> $value['description_name'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				$i++;
			}
			$SubGroup_arr = array_unique($SubGroup_arr);
			if(count($SubGroup_arr)>0){
				// print_r($SubGroup_arr);die;
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
				
				if($SubGroup){
					$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
					$this->db->where_in('tblhistory.ItemID', $SubGroup_arr);
					$this->db->group_by('tblhistory.ItemID');
					}else{
					$this->db->where_in('tblitems.SubGrpID1', $SubGroup_arr);
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
				
				$i=0;
				foreach ($TopItem as $key => $value) {
					array_push($chart, [
					'name' 		=> $value['description_name'],
					'y' 		=>	(int)$value['total_qty'],
					'z' 		=> 100,
					'label' 		=> "Qty"
					]);
					$i++;
				}
				
				
				$this->db->select(db_prefix().'items_sub_groups.id as ItemID, SUM(OQty) as total_qty,'.db_prefix().'items_sub_groups.name as description_name');
				
				$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'stockmaster.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'stockmaster.PlantID');
				
				$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');
				$this->db->where('tblstockmaster.PlantID',$selected_company);
				$this->db->where('tblstockmaster.FY',$fy);
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup_arr);
				$this->db->group_by('tblitems.SubGrpID1');
				
				$this->db->order_by("total_qty", "DESC");
				
				$this->db->limit($ItemCount);
				$TotalOQty = $this->db->get(db_prefix().'stockmaster')->result_array();
				
				$i=0;
				$OpeningQtyMap = [];
				foreach ($TotalOQty as $key => $value) {
					$OpeningQtyMap[$value['description_name']] = (int)$value['total_qty'];
					array_push($OQtyChart, [
					'name' 		=> $value['description_name'],
					'y' 		=>	(int)$value['total_qty'],
					'z' 		=> 100,
					'label' 		=> "Qty"
					]);
					$i++;
				}
			}
			
			// Opening + Purchase
			foreach ($Purchase as &$pItem) {
				$desc = $pItem['name'];
				if (isset($OpeningQtyMap[$desc])) {
					$pItem['y'] += $OpeningQtyMap[$desc];  // Add opening qty
				}
			}
			unset($pItem);
			
			$data = [
			'Sales' => $chart,
			'Purchase' => $Purchase,
			// 'OpeningQty' => $OQtyChart,
			];
			
			return $data;
		}
		
		public function top_five_customer()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$minvalue = '20'.$fy.'-04-01 00:00:00';
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
			$minvalue = '20'.$fy.'-04-01 00:00:00';
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
			$minvalue = '20'.$fy.'-04-01 00:00:00';
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
					'label' 		=> "Amount ₹"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['Month'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['PurchAmt'],
					'z' 		=> 100,
					'label' 		=> "Amount ₹"
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
			$minvalue = '20'.$fy.'-04-01 00:00:00';
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
					'label' 		=> "Amount ₹"
					]);
					}else{
					$color_index = 0;
					array_push($chart, [
					'name' 		=> $value['Month'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['SaleAmt'],
					'z' 		=> 100,
					'label' 		=> "Amount ₹"
					]);
				}
				$color_index++;
			}
			
			return $chart;
		}
		public function LateOntimeDeliveries()
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$Currant_datetime = date('Y-m-d H:i:s');
			
			$this->db->select("
			SUM(CASE 
			WHEN tblchallanmaster.GatepassTime <= tblordermaster.Dispatchdate 
			THEN 1 ELSE 0 END
			) AS OnTimeDelivery,
			
			SUM(CASE 
			WHEN tblchallanmaster.GatepassTime > tblordermaster.Dispatchdate 
			THEN 1 ELSE 0 END
			) AS LateDelivery
			");
			
			$this->db->from(db_prefix().'ordermaster');
			$this->db->join(db_prefix().'challanmaster',db_prefix().'challanmaster.ChallanID = '.db_prefix().'ordermaster.ChallanID','inner'
			);
			$this->db->where('tblordermaster.Dispatchdate IS NOT NULL', null, false);
			$this->db->where('tblchallanmaster.Gatepassuserid IS NOT NULL', null, false);
			$this->db->where('tblchallanmaster.GatepassTime IS NOT NULL', null, false);
			$this->db->where('tblordermaster.PlantID', $selected_company);
			$this->db->where('tblordermaster.FY', $FY);
			
			
			$result = $this->db->get()->row_array();
			
			return $result;
		}
}
