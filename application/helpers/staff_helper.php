<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @since  2.3.3
 * Get available staff permissions, modules can use the filter too to hook permissions
 * @param  array  $data additional data passed from view role.php and member.php
 * @return array
 */
 function get_available_staff_permissions($data = [])
{
    $viewGlobalName = _l('permission_view') . '(' . _l('permission_global') . ')';

    $v =[
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $v_c = [
        'view'     => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $v_c_e = [
        'view'     => $viewGlobalName,
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $v_c_e_d = [
        'view'     => $viewGlobalName,
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => _l('permission_delete'),
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $v_e = [
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => _l('permission_edit'),
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $v_e_d = [
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $v_c_e_p_ex = [
        'view'     =>  $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    $v_p_ex = [
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    $all = [
        'view'     => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    
    $withNotApplicableViewOwn = array_merge(['view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')]], $v_c_e_d);

    $corePermissionsNew = [];
    $masterArray = [
        'company_master' => ['Company Master', $v_c_e_d],
        'customers' => [_l('clients'), $v_c_e],
        'broker_master' => ['Broker', $v_c_e],
        'FreightTerm' => ['Freight Terms Master', $v_c_e],
        'location_form' => ['Location Master', $v_c_e_p_ex],
        'payment_terms' => ['Payment Terms Master', $v_c_e_p_ex],
        'Territory' => ['Territory Master', $v_c_e],
        'currencies' => ['Currency Master', $all],
        'route_master' => ['Route Master', $all],
        'PointMaster' => ['Point Master', $v_c_e],
        'StationMaster' => ['Station Master', $v_c_e],
        'tdsMaster' => ['TDS Master', $v_c_e],
        'TransportMaster' => ['Transport Master', $v_c_e],
        'Chamber' => ['Chamber Master', $v_c_e],
        'Stack' => ['Stack Master', $v_c_e],
        'Lot' => ['Lot Master', $v_c_e],
        'Country' => ['Country Master', $v],
        'Form' => ['Form Master', $v_c_e],
    ];
    foreach ($masterArray as $key => $value) {
        $corePermissionsNew[$key] = [
            'name'          => $value[0],
            'main_menu'     => 'Masters',
            'capabilities'  => $value[1],
        ];
    }

    $inventoryArray = [
        'ItemType' => ['Item Type Master', $v_c_e],
        'items' => ['Item Master', $v_c_e],
        'itemsmaingrp' => ['ItemMain Group', $v_c_e],
        'itemssubgrp' => ['Item SubGroup 1', $v_c_e],
        'itemssubgrp2' => ['Item SubGroup 2', $v_c_e],
        'ItemDivision' => ['Item Division', $v_c_e],
        'ItemCategory' => ['Item Category Master', $v_c_e],
        'hsn_master' => ['HSN Master', $all],
        'GSTMaster' => ['GST Master', $v_c_e],
        'UnitMaster' => ['Unit Master', $v_c_e],
        'WeightUnitMaster' => ['Weight Master', $v_c_e],
        'brandMaster' => ['Brand Master', $v_c_e],
        'priorityMaster' => ['Priority Master', $v_c_e],
        'ItemList' => ['Item List', $v_p_ex],
        'GodownMaster' => ['Godown Master', $v_c_e]
    ];
    foreach ($inventoryArray as $key => $value) {
        $corePermissionsNew[$key] = [
            'name'          => $value[0],
            'main_menu'     => 'Inventory',
            'capabilities'  => $value[1],
        ];
    }

    $qcArray = [
        'qcUnit' => ['QC Unit', $all],
        'qcMaster' => ['QC Parameters', $v_c_e],
        'itemWiseQc' => ['Item Wise QC', $v_c_e],
        'deductionMatrix' => ['Deduction Matrix', $v_c_e],
        'finishGoodTest' => ['Finish Good Test', $v_c_e_p_ex],
        'metalDetectorQC' => ['Metal Detector QC', $v_c_e_p_ex],
    ];
    foreach ($qcArray as $key => $value) {
        $corePermissionsNew[$key] = [
            'name'          => $value[0],
            'main_menu'     => 'QC',
            'capabilities'  => $value[1],
        ];
    }

    $transactionArray = [
        'salesQuotation' => ['Sales Quotation', $v_c_e],
        'salesQuotationList' => ['Sales Quotation List', $v_p_ex],
        'salesOrder' => ['Sales Order', $v_c_e],
        'salesOrderList' => ['Sales Order List', $v_p_ex],
        'deliveryOrder' => ['Delivery Order', $v_c_e],
        'pendingDeliveryOrder' => ['Pending Delivery Order', $v_p_ex],
        'limitExceedDeliveryOrder' => ['Limit Exceed Order', $v_p_ex],
        'salesInvoice' => ['Sales Invoice', $v_c_e],
        'salesInvoiceList' => ['Sales Invoice List', $v_p_ex],
        'changeVehicle' => ['Change Vehicle', $v_e],
        'salesTradeSettlement' => ['Sales Trade Settlement', $v_c_e],
        'stockTransfer' => ['Stock Transfer', $v_c_e],
    ];
    foreach ($transactionArray as $key => $value) {
        $corePermissionsNew[$key] = [
            'name'          => $value[0],
            'main_menu'     => 'Transaction',
            'capabilities'  => $value[1],
        ];
    }

    $transportArray = [
      'vehicleMaster' => ['Vehicle Master', $v_c_e_d],
      'Vehicle_transaction' => ['Vehicle Transaction', $v_c_e_d],
      'PendingVehicleReturnList' => ['Vehicle Return Report', $v_p_ex],
      'Vehicle_Expense' => ['Vehicle Return Expense', $v_c_e_d],
      'FinalVehicleReport' => ['Final Vehicle Report', $v_p_ex],
      'RestRecordReport' => ['Driver Rest Report', $v_p_ex],
      'VehicleMaintenanceReport' => ['Vehicle Maintenance Report', $v_p_ex],
      'DelayDelivery' => ['Delay Deliveries', $v_p_ex],
      'VehicleInPremises' => ['Vehicle In Premises', $v_p_ex],
    ];
    foreach ($transportArray as $key => $value) {
        $corePermissionsNew[$key] = [
            'name'          => $value[0],
            'main_menu'     => 'Transport',
            'capabilities'  => $value[1],
        ];
    }

    $hrArray = [
        'hrmDashboard' => ['Dashboard', $v],
		'hrmStaffMembers' => ['Staff Members', $v_c_e],
		'hrmStaffList' => ['Staff List', $v_p_ex],
		'hrmAttendanceSheet' => ['Attendance Sheet', $v_p_ex],
		'hrmClaimExpenses' => ['Claim Expenses', $v_c_e_p_ex],
		'hrmJobDepartments' => ["Job Departments", $v_c_e_p_ex],
		'hrmJobDesignation' => ["Job Designation", $v_c_e_p_ex],
		'hrmShiftCategories' => ["Shift Categories", $all],
		'hrmShift' => ["Shift", $all],
		'hrmWorkShift' => ["Work Shift Table", $v_c_e_p_ex],
		'hrmAnnualLeave' => ["Annual Leave & Holiday", $v_c_e_p_ex],
		'hrmLeave' => ["Leave", $v_c_e_p_ex],
    ];
    foreach ($hrArray as $key => $value) {
        $corePermissionsNew[$key] = [
            'name'          => $value[0],
            'main_menu'     => 'HR',
            'capabilities'  => $value[1],
        ];
    }
    
    return hooks()->apply_filters('staff_permissions', $corePermissionsNew, $data);
}

function get_dist_name($id)
{
    $CI = &get_instance();
    $CI->db->select('company')->from(db_prefix() . 'clients')->where('userid', $id);

    return $CI->db->get()->row()->company;
}

/**
 * Get staff by ID or current logged in staff
 * @param  mixed $id staff id
 * @return mixed
 */
function get_staff($id = null)
{
    if (empty($id) && isset($GLOBALS['current_user'])) {
        return $GLOBALS['current_user'];
    }

    // Staff not logged in
    if (empty($id)) {
        return null;
    }

    if (!class_exists('staff_model', false)) {
        get_instance()->load->model('staff_model');
    }

    return get_instance()->staff_model->get($id);
}

/**
 * Return staff profile image url
 * @param  mixed $staff_id
 * @param  string $type
 * @return string
 */
function staff_profile_image_url($staff_id, $type = 'small')
{
    $url = base_url('assets/images/user-placeholder.jpg');

    if ((string) $staff_id === (string) get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $staff = $GLOBALS['current_user'];
    } else {
        $CI = & get_instance();
        $CI->db->select('profile_image')
        ->where('staffid', $staff_id);

        $staff = $CI->db->get(db_prefix() . 'staff')->row();
    }

    if ($staff) {
        if (!empty($staff->profile_image)) {
            $profileImagePath = 'uploads/staff_profile_images/' . $staff_id . '/' . $type . '_' . $staff->profile_image;
            if (file_exists($profileImagePath)) {
                $url = base_url($profileImagePath);
            }
        }
    }

    return $url;
}

/**
 * Staff profile image with href
 * @param  boolean $id        staff id
 * @param  array   $classes   image classes
 * @param  string  $type
 * @param  array   $img_attrs additional <img /> attributes
 * @return string
 */
function staff_profile_image($id, $classes = ['staff-profile-image'], $type = 'small', $img_attrs = [])
{
    $url = base_url('assets/images/user-placeholder.jpg');

    $id = trim($id);

    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . e($val) . '" ';
    }

    $blankImageFormatted = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';

    if ((string) $id === (string) get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $result = $GLOBALS['current_user'];
    } else {
        $CI     = & get_instance();
        $result = $CI->app_object_cache->get('staff-profile-image-data-' . $id);

        if (!$result) {
            $CI->db->select('profile_image,firstname,lastname');
            $CI->db->where('staffid', $id);
            $result = $CI->db->get(db_prefix() . 'staff')->row();
            $CI->app_object_cache->add('staff-profile-image-data-' . $id, $result);
        }
    }

    if (!$result) {
        return $blankImageFormatted;
    }

    if ($result && $result->profile_image !== null) {
        $profileImagePath = 'uploads/staff_profile_images/' . $id . '/' . $type . '_' . $result->profile_image;
        if (file_exists($profileImagePath)) {
            $profile_image = '<img ' . $_attributes . ' src="' . base_url($profileImagePath) . '" class="' . implode(' ', $classes) . '" />';
        } else {
            return $blankImageFormatted;
        }
    } else {
        $profile_image = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';
    }

    return $profile_image;
}

/**
 * Get staff full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $tmpStaffUserId = get_staff_user_id();
    if ($userid == '' || $userid == $tmpStaffUserId) {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->firstname . ' ' . $GLOBALS['current_user']->lastname;
        }
        $userid = $tmpStaffUserId;
    }

    $CI = & get_instance();

    $staff = $CI->app_object_cache->get('staff-full-name-data-' . $userid);

    if (!$staff) {
        $CI->db->where('staffid', $userid);
        $staff = $CI->db->select('firstname,lastname')->from(db_prefix() . 'staff')->get()->row();
        $CI->app_object_cache->add('staff-full-name-data-' . $userid, $staff);
    }

    return $staff ? $staff->firstname . ' ' . $staff->lastname : '';
}

/**
 * Get staff default language
 * @param  mixed $staffid
 * @return mixed
 */
function get_staff_default_language($staffid = '')
{
    if (!is_numeric($staffid)) {
        // checking for current user if is admin
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->default_language;
        }

        $staffid = get_staff_user_id();
    }
    $CI = & get_instance();
    $CI->db->select('default_language');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('staffid', $staffid);
    $staff = $CI->db->get()->row();
    if ($staff) {
        return $staff->default_language;
    }

    return '';
}

function get_staff_recent_search_history($staff_id = null)
{
    $recentSearches = get_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches');

    if ($recentSearches == '') {
        $recentSearches = [];
    } else {
        $recentSearches = json_decode($recentSearches);
    }

    return $recentSearches;
}

function update_staff_recent_search_history($history, $staff_id = null)
{
    $totalRecentSearches = hooks()->apply_filters('total_recent_searches', 5);
    $history             = array_reverse($history);
    $history             = array_unique($history);
    $history             = array_splice($history, 0, $totalRecentSearches);

    update_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches', json_encode($history));

    return $history;
}


/**
 * Check if user is staff member
 * In the staff profile there is option to check IS NOT STAFF MEMBER eq like contractor
 * Some features are disabled when user is not staff member
 * @param  string  $staff_id staff id
 * @return boolean
 */
function is_staff_member($staff_id = '')
{
    $CI = & get_instance();
    if ($staff_id == '') {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->is_not_staff === '0';
        }
        $staff_id = get_staff_user_id();
    }

    $CI->db->where('staffid', $staff_id)
    ->where('is_not_staff', 0);

    return $CI->db->count_all_results(db_prefix() . 'staff') > 0 ? true : false;
}

/* Custom functions start */
function get_staff_permission($id)
{
    if (empty($id) && isset($GLOBALS['current_user'])) {
        return $GLOBALS['current_user'];
    }
    // Staff not logged in
    if (empty($id)) {
        return null;
    }

    $CI = & get_instance();
    $CI->db->select('*');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('staffid', $id);
    $staff_details =  $CI->db->get()->row();
    
    $CI->db->select(db_prefix() . 'setup.FIRMNAME,'.db_prefix() . 'setup.YEARFROM,'.db_prefix() . 'setup.YEARTO,'.db_prefix() . 'setup.PlantID');
    $CI->db->from(db_prefix() . 'setup');
    $CI->db->where(db_prefix() . 'setup.Status', 'Y');
    $CI->db->order_by(db_prefix() . 'setup.FY,'.db_prefix() . 'setup.PlantID',"desc");
    if($staff_details->admin == "1"){
        
    }else{
        $CI->db->join(db_prefix() . 'staff_permissions', '' . db_prefix() . 'staff_permissions.plant_id = ' . db_prefix() . 'setup.PlantID AND '. db_prefix() . 'staff_permissions.year = ' . db_prefix() . 'setup.FY');
        $CI->db->where(db_prefix() . 'staff_permissions.staff_id', $id);
        $CI->db->group_by(db_prefix() . 'staff_permissions.year,'.db_prefix() . 'staff_permissions.plant_id');
        
    }
    return $CI->db->get()->result_array();
}

/**
 * Get Root Company name
 */
function get_root_company_name($compid = '')
{
    
    $CI = & get_instance();
    $selected_year = $CI->session->userdata("finacial_year");
    $CI->db->select('FIRMNAME,FY');
    $CI->db->from(db_prefix() . 'setup');
    $CI->db->where('PlantID', $compid);
    $CI->db->where('FY', $selected_year);
    $company_data = $CI->db->get()->row();
    if ($company_data) {
        return $company_data->FIRMNAME."(".$company_data->FY.")";
    }
}

function get_days_new($feature_name = '')
{
    $CI = & get_instance();
    $selected_year = $CI->session->userdata("finacial_year");
    $selected_company = $CI->session->userdata("root_company");
    $curr_user = $GLOBALS['current_user'];
    $CI->db->select('days');
    //$CI->db->from(db_prefix() . 'staff_permissions');
    $CI->db->LIKE('feature', $feature_name);
    $CI->db->where('staff_id', $curr_user);
    $CI->db->where('plant_id', $selected_company);
    $CI->db->where('year', $selected_year);
    return $CI->db->get(db_prefix() . 'staff_permissions')->row();
}

function get_route_name($routeid = '',$selected_company)
{
    $CI = & get_instance();
    $selected_company = $CI->session->userdata("root_company");
    $CI->db->where('PlantID', $selected_company);
    $CI->db->where('RouteID', $routeid);
    $route_data = $CI->db->get(db_prefix() . 'route')->row();
    if ($route_data) {
        return $route_data->name;
    }
}

function get_account_details($ChallanID = '',$selected_company)
{
    $CI = & get_instance();
    $CI->db->select('AccountID');
    $CI->db->from(db_prefix() . 'ordermaster');
    $CI->db->where('ChallanID', $ChallanID);
    $CI->db->where('PlantID', $selected_company);
    $acc_data = $CI->db->get()->result_array();
    return $acc_data;
}

function get_travel_detail_by_staff_id($staff_id = '',$date)
{
    $CI = & get_instance();
    $CI->db->select('*');
    $CI->db->from(db_prefix() . 'travel_report');
    $CI->db->where('staff_id', $staff_id);
    $CI->db->where('date', $date);
    $travel_data = $CI->db->get()->result_array();
    return $travel_data;
}

function get_party_detail($AccountID = '',$selected_company)
{
    $CI = & get_instance();
    $CI->db->select('company,state,StationName,city');
    $CI->db->from(db_prefix() . 'clients');
    $CI->db->where('AccountID', $AccountID);
    $CI->db->where('PlantID', $selected_company);
    $acc_details = $CI->db->get()->row();
    return $acc_details;
    /*if ($route_data) {
        return $route_data->name;
    }*/
}

/**
 * Get Root Company name
 
 */
function get_all_root_company($compid = '')
{
    $CI = & get_instance();
    $CI->db->select('company_name,id');
    $CI->db->from(db_prefix() . 'rootcompany');
    //$CI->db->where('id', $compid);
    return $CI->db->get()->result_array();
    /*if ($company_data) {
        return $company_data;
    }*/
}
