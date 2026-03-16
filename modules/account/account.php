<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Human Resources Management
Description: Human Resource Management module for Perfex
Version: 2.3.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('ACCOUNT_MODULE_NAME', 'hrm');

define('HRM_MODULE_UPLOAD_FOLDER', module_dir_path(ACCOUNT_MODULE_NAME, 'uploads'));

hooks()->add_action('admin_init', 'account_permissions');
hooks()->add_action('app_admin_head', 'account_add_head_components');
hooks()->add_action('app_admin_footer', 'account_add_footer_components');
hooks()->add_action('admin_init', 'account_module_init_menu_items');

/**
* Register activation module hook
*/
register_activation_hook(ACCOUNT_MODULE_NAME, 'account_module_activation_hook');

function account_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(ACCOUNT_MODULE_NAME, [ACCOUNT_MODULE_NAME]);


$CI = & get_instance();
$CI->load->helper(ACCOUNT_MODULE_NAME . '/hrm');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function account_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission('account', '', 'view')) {

        $CI->app_menu->add_sidebar_menu_item('ACCOUNT', [
                'name'     => 'Accounts',
                'icon'     => 'fa fa-user-circle',
                'href'     => admin_url('#'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'hrm_dashboard',
                'name'     => _l('dashboard'),
                'icon'     => 'fa fa-home',
                
                'href'     => admin_url('account'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_ledger',
                'name'     => 'Account Ledger',
                'icon'     => 'fa fa-book',
                'href'     => admin_url('account/account_ledger'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_contra',
                'name'     => 'Contra',
                'icon'     => 'fa fa-book',
                'href'     => admin_url('account/manage_countra'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_journal',
                'name'     => 'Journal',
                'icon'     => 'fa fa-file-text-o',
                'href'     => admin_url('account/manage_journal'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_receipts',
                'name'     => 'Receipts',
                'icon'     => 'fa fa-file',
                'href'     => admin_url('account/manage_receipts'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_payment',
                'name'     => 'Payments',
                'icon'     => 'fa fa-dollar',
                'href'     => admin_url('account/manage_payment'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_group_wise_balance',
                'name'     => 'Account Group Wise Balances',
                'icon'     => 'fa fa-address-book',
                'href'     => admin_url('account/account_group_wise_balance'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_daily_balance',
                'name'     => 'Account Ledger Daily Balances',
                'icon'     => 'fa fa-file-text-o',
                'href'     => admin_url('account/account_daily_balance'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_receipts',
                'name'     => 'Account Monitor',
                'icon'     => 'fa fa-address-book',
                'href'     => admin_url('account/account_monitor'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_receipts',
                'name'     => 'Credit/Debit Note',
                'icon'     => 'fa fa-book',
                'href'     => admin_url('account/credit_debit_note'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_receipts',
                'name'     => 'Trial Balances',
                'icon'     => 'fa fa-bar-chart',
                'href'     => admin_url('account/trial_balance'),
        ]);
         $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_voucher',
                'name'     => 'Voucher Register',
                'icon'     => 'fa fa-file',
                'href'     => admin_url('account/manage_voucher'),
        ]);
        $CI->app_menu->add_sidebar_children_item('ACCOUNT', [
                'slug'     => 'account_tcs_reports',
                'name'     => 'TCS Report',
                'icon'     => 'fa fa-address-book',
                
                'href'     => admin_url('account/tcs_reports'),
        ]);
        
        
        
       
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


function account_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];

    // register_staff_capabilities('account', $capabilities, _l('hrm'));
}


function account_add_head_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    
    echo '<link href="' . module_dir_url('account','assets/css/style.css') .'"  rel="stylesheet" type="text/css" />';
    echo '<link href="' . module_dir_url('account','assets/plugins/ComboTree/style.css') .'"  rel="stylesheet" type="text/css" />';

    if ($viewuri == '/admin/account') {
        echo '<script src="'.module_dir_url('account', 'assets/plugins/highcharts/highcharts.js').'"></script>';
        echo '<script src="'.module_dir_url('account', 'assets/plugins/highcharts/modules/variable-pie.js').'"></script>';
        echo '<script src="'.module_dir_url('account', 'assets/plugins/highcharts/modules/export-data.js').'"></script>';
        echo '<script src="'.module_dir_url('account', 'assets/plugins/highcharts/modules/accessibility.js').'"></script>';
        echo '<script src="'.module_dir_url('account', 'assets/plugins/highcharts/modules/exporting.js').'"></script>';
        echo '<script src="'.module_dir_url('account', 'assets/plugins/highcharts/highcharts-3d.js').'"></script>';
    }
    
    if ($viewuri == '/admin/account/timekeeping?group=allocate_shiftwork' || $viewuri == '/admin/account/payroll?group=payroll_type' || $viewuri == '/admin/account/timekeeping?group=table_shiftwork' || $viewuri == '/admin/account/insurances' || strpos($viewuri, 'payroll') !== false ) {
        echo '<script src="'.module_dir_url('account', 'assets/plugins/handsontable/handsontable.full.min.js').'"></script>';
        echo '<link href="' . base_url('modules/account/assets/plugins/handsontable/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
    }

    if ($viewuri == '/admin/account/insurances') {
        echo '<link href="' . base_url('modules/account/assets/css/datepicker.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if (strpos($viewuri, '/admin/account/member/') !== false) {
        echo '<link href="' . base_url('modules/account/assets/css/member.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if ($viewuri == '/admin/account/payroll?group=payroll_type') {
        echo '<link href="' . base_url('modules/account/assets/css/newpayrolltype.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if (strpos($viewuri, '/admin/account/payroll_table') !== false) {
        echo '<link href="' . base_url('modules/account/assets/css/newpayrolltable.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
    if (strpos($viewuri, '/admin/account/profile/') !== false) {
        echo '<link href="' . base_url('modules/account/assets/css/profile.css') .'"  rel="stylesheet" type="text/css" />';
    }
    
}


function account_add_footer_components(){
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];

    echo '<script src="'.module_dir_url('account', 'assets/plugins/ComboTree/comboTreePlugin.js').'"></script>';
    echo '<script src="'.module_dir_url('account', 'assets/plugins/ComboTree/icontains.js').'"></script>';

    if (strpos($viewuri, '/admin/account/setting?group=workplace') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/workplace.js').'"></script>';
    }

    if (strpos($viewuri, 'payslip') !== false || $viewuri == '/admin/account/payroll') {
        echo '<script src="'.module_dir_url('account', 'assets/js/payslip.js').'"></script>';
    }
    
    if (strpos($viewuri, 'payroll') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/payroll.js').'"></script>';
        echo '<script src="'.module_dir_url('account', 'assets/js/payrollincludes.js').'"></script>';
        echo '<script src="'.module_dir_url('account', 'assets/js/payslip.js').'"></script>';
    }
    
    if (strpos($viewuri, 'job_position') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/jobposition.js').'"></script>';
    }
    
    if (strpos($viewuri, 'contract_type') !== false || $viewuri == '/admin/account/setting') {
        echo '<script src="'.module_dir_url('account', 'assets/js/contracttype.js').'"></script>';
    }
    
    if (strpos($viewuri, 'allowance_type') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/allowancetype.js').'"></script>';
    }
    
    if (strpos($viewuri, '/admin/account/member') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/member.js').'"></script>';
    }

    if (strpos($viewuri, '/admin/account/contract/') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/contract.js').'"></script>';
    }

    if (strpos($viewuri, 'manage_staff') !== false || $viewuri == '/admin/account/staff_infor') {
        echo '<script src="'.module_dir_url('account', 'assets/js/managestaff.js').'"></script>';
    }
    
    if (strpos($viewuri, 'manage_setting') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/managesetting.js').'"></script>';
    }
    
    if (strpos($viewuri, 'manage_dayoff') !== false || strpos($viewuri, 'timekeeping') !== false) {
        echo '<script src="'.module_dir_url('account', 'assets/js/managedayoff.js').'"></script>';
    }
}
