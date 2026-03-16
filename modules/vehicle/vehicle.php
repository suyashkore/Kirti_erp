<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Vehicle Master
Description: Vehicle Master module for Perfex
Version: 2.3.0
Requires at least: 2.3.*
Author: Global Infocloud Pvt. Ltd.
Author URI: http://globalinfocloud.com
*/

define('VEHICLE_MODULE_NAME', 'hrm');

define('HRM_MODULE_UPLOAD_FOLDER', module_dir_path(VEHICLE_MODULE_NAME, 'uploads'));

hooks()->add_action('admin_init', 'vehicle_permissions');
hooks()->add_action('app_admin_head', 'vehicle_add_head_components');
hooks()->add_action('app_admin_footer', 'vehicle_add_footer_components');
hooks()->add_action('admin_init', 'vehicle_module_init_menu_items');

/**
* Register activation module hook
*/
register_activation_hook(VEHICLE_MODULE_NAME, 'vehicle_module_activation_hook');

function vehicle_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(VEHICLE_MODULE_NAME, [VEHICLE_MODULE_NAME]);


$CI = & get_instance();
$CI->load->helper(VEHICLE_MODULE_NAME . '/hrm');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function vehicle_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('vehicle', '', 'view')) {

        $CI->app_menu->add_sidebar_menu_item('VEHICLE', [
                'name'     => 'Vehicle Master',
                'icon'     => 'fa fa-user-circle',
                'href'     => admin_url('#'),
        ]);
        $CI->app_menu->add_sidebar_children_item('VEHICLE', [
                'slug'     => 'vehicle_dashboard',
                'name'     => _l('dashboard'),
                'icon'     => 'fa fa-home',
                
                'href'     => admin_url('vehicle'),
        ]);
        $CI->app_menu->add_sidebar_children_item('VEHICLE', [
                'slug'     => 'vehicle_manage',
                'name'     => 'Vehicle',
                'icon'     => 'fa fa-address-book',
                
                'href'     => admin_url('vehicle/vehicle_manage'),
        ]);
        $CI->app_menu->add_sidebar_children_item('VEHICLE', [
                'slug'     => 'vehicle_route_vehicle',
                'name'     => 'Route Master',
                'icon'     => 'fa fa-file',
                'href'     => admin_url('vehicle/route_manage'),
        ]);
        $CI->app_menu->add_sidebar_children_item('VEHICLE', [
                'slug'     => 'vehicle_route_challan',
                'name'     => 'Route Challan',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('vehicle/route_challan_add'),
        ]);
         $CI->app_menu->add_sidebar_children_item('VEHICLE', [
                'slug'     => 'VehicleReturn',
                'name'     => 'Vehicle Return',
                'icon'     => 'fa fa-address-book',
                'href'     => admin_url('vehicle/VehicleReturn'),
        ]);

         $CI->app_menu->add_sidebar_children_item('VEHICLE', [
                'slug'     => 'GatePass',
                'name'     => ' Gate Pass',
                'icon'     => 'fa fa-file',
                'href'     => admin_url('#'),
        ]);
       /* $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Credit/Debit Note',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Account Monitor',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Account Ledger Daily Balances',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Account Group Wise Balances',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_payroll',
                'name'     => 'Payments',
                'icon'     => 'fa fa-dollar',
                'href'     => admin_url('account/payroll'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Receipts',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Journal',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Contra',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_insurrance',
                'name'     => 'Account Ledger',
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('account/insurances'),
        ]);*/

        

        /*$CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_staff',
                'name'     => _l('staff'),
                'icon'     => 'fa fa-address-book',
                
                'href'     => admin_url('hrm/staff_infor'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_staff_contract',
                'name'     => _l('staff_contract'),
                'icon'     => 'fa fa-file',
                'href'     => admin_url('hrm/contracts'),
        ]);
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_insurrance',
                'name'     => _l('insurrance'),
                'icon'     => 'fa fa-medkit',
                'href'     => admin_url('hrm/insurances'),
        ]);
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('HRM', [
                    'slug'     => 'hrm_timekeeping',
                    'name'     => _l('timekeeping'),
                    'icon'     => 'fa fa fa-pencil',
                    'href'     => admin_url('hrm/timekeeping'),
            ]);
        }

        if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('HRM', [
                'slug'     => 'hrm_payroll',
                'name'     => _l('payroll'),
                'icon'     => 'fa fa-dollar',
                'href'     => admin_url('hrm/payroll'),
        ]);
        }

        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('HRM', [
                    'slug'     => 'hrm_setting',
                    'name'     => _l('setting'),
                    'icon'     => 'fa fa-cog',
                    'href'     => admin_url('hrm/setting'),
            ]);
        }*/
    }
}


function vehicle_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];

    // register_staff_capabilities('vehicle', $capabilities, _l('hrm'));
}


function vehicle_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    
    echo '<link href="' . module_dir_url('vehicle','assets/css/style.css') .'"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url('vehicle','assets/plugins/ComboTree/style.css') .'"  rel="stylesheet" type="text/css" />';

    if ($viewuri == '/admin/vehicle') {
        echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/highcharts/highcharts.js').'"></script>';
        echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/highcharts/modules/variable-pie.js').'"></script>';
        echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/highcharts/modules/export-data.js').'"></script>';
        echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/highcharts/modules/accessibility.js').'"></script>';
        echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/highcharts/modules/exporting.js').'"></script>';
        echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/highcharts/highcharts-3d.js').'"></script>';
    }
    
    if ($viewuri == '/admin/vehicle/timekeeping?group=allocate_shiftwork' || $viewuri == '/admin/vehicle/payroll?group=payroll_type' || $viewuri == '/admin/vehicle/timekeeping?group=table_shiftwork' || $viewuri == '/admin/vehicle/insurances' || strpos($viewuri, 'payroll') !== false ) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/handsontable/handsontable.full.min.js').'"></script>';
        echo '<link href="' . base_url('modules/vehicle/assets/plugins/handsontable/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
    }

    if ($viewuri == '/admin/vehicle/insurances') {
        echo '<link href="' . base_url('modules/vehicle/assets/css/datepicker.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if (strpos($viewuri, '/admin/vehicle/member/') !== false) {
        echo '<link href="' . base_url('modules/vehicle/assets/css/member.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if ($viewuri == '/admin/vehicle/payroll?group=payroll_type') {
        echo '<link href="' . base_url('modules/vehicle/assets/css/newpayrolltype.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if (strpos($viewuri, '/admin/vehicle/payroll_table') !== false) {
        echo '<link href="' . base_url('modules/vehicle/assets/css/newpayrolltable.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if (strpos($viewuri, '/admin/vehicle/profile/') !== false) {
        echo '<link href="' . base_url('modules/vehicle/assets/css/profile.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
}


function vehicle_add_footer_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/ComboTree/comboTreePlugin.js').'"></script>';
    echo '<script src="'.module_dir_url('vehicle', 'assets/plugins/ComboTree/icontains.js').'"></script>';

    if (strpos($viewuri, '/admin/vehicle/setting?group=workplace') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/workplace.js').'"></script>';
    }

    if (strpos($viewuri, 'payslip') !== false || $viewuri == '/admin/vehicle/payroll') {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/payslip.js').'"></script>';
    }
    
    if (strpos($viewuri, 'payroll') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/payroll.js').'"></script>';
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/payrollincludes.js').'"></script>';
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/payslip.js').'"></script>';
    }
    
    if (strpos($viewuri, 'job_position') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/jobposition.js').'"></script>';
    }
    
    if (strpos($viewuri, 'contract_type') !== false || $viewuri == '/admin/vehicle/setting') {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/contracttype.js').'"></script>';
    }
    
    if (strpos($viewuri, 'allowance_type') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/allowancetype.js').'"></script>';
    }
    
    if (strpos($viewuri, '/admin/vehicle/member') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/member.js').'"></script>';
    }

    if (strpos($viewuri, '/admin/vehicle/contract/') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/contract.js').'"></script>';
    }

    if (strpos($viewuri, 'manage_staff') !== false || $viewuri == '/admin/vehicle/staff_infor') {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/managestaff.js').'"></script>';
    }
    
    if (strpos($viewuri, 'manage_setting') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/managesetting.js').'"></script>';
    }
    
    if (strpos($viewuri, 'manage_dayoff') !== false || strpos($viewuri, 'timekeeping') !== false) {
        echo '<script src="'.module_dir_url('vehicle', 'assets/js/managedayoff.js').'"></script>';
    }
}
