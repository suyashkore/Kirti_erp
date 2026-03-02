<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Timesheet Attendance Management
Description: An complete attendance management system application with timesheet mostly work with attendance, leave, holiday and shift
Version: 1.1.2
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

define('TIMESHEETS_MODULE_NAME', 'timesheets');
define('TIMESHEETS_MODULE_UPLOAD_FOLDER', module_dir_path(TIMESHEETS_MODULE_NAME, 'uploads'));
define('TIMESHEETS_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER', module_dir_path(TIMESHEETS_MODULE_NAME, 'uploads/contracts/'));
define('TIMESHEETS_JOB_POSIITON_ATTACHMENTS_UPLOAD_FOLDER', module_dir_path(TIMESHEETS_MODULE_NAME, 'uploads/job_position/'));
define('TIMESHEETS_PATH', 'modules/timesheets/uploads/');
define('TIMESHEETS_PAYSLIPS', 'modules/timesheets/uploads/payslips/');
define('TIMESHEETS_REVISION', 112);

define('PAY_SLIP', FCPATH );

hooks()->add_action('admin_init', 'timesheets_permissions');
hooks()->add_action('admin_init', 'timesheets_module_init_menu_items');
hooks()->add_action('app_admin_head', 'timesheets_add_head_components');
hooks()->add_action('app_admin_footer', 'timesheets_load_js');
hooks()->add_action('app_search', 'timesheets_load_search');
hooks()->add_action('before_cron_run', 'timesheets_cron_approval');
hooks()->add_action('before_cron_run', 'auto_checkout_cron');
hooks()->add_action('before_cron_run', 'auto_remider_checkin');
hooks()->add_action('after_render_top_search', 'after_render_top_search_timesheets');
register_merge_fields('timesheets/merge_fields/attendance_notice_merge_fields');
register_merge_fields('timesheets/merge_fields/remind_user_check_in_merge_fields');
register_merge_fields('timesheets/merge_fields/send_request_approval_merge_fields');

/*Attendance export excel path*/
define('TIMESHEETS_PATH_EXPORT_FILE', 'modules/timesheets/uploads/attendance/');

/**
* Register activation module hook
*/
register_activation_hook(TIMESHEETS_MODULE_NAME, 'timesheets_module_activation_hook');

function timesheets_module_activation_hook()
{
	$CI = &get_instance();
	require_once(__DIR__ . '/install.php');
}
/**
 * { function_description }
 */
function timesheets_cron_approval()
{
	$CI = &get_instance();
	
	$hour_now                       = date('G');
	if ($hour_now != 23) {
		return;
	}

	$CI->load->model('emails_model');
	$CI->db->select('*');
	$CI->db->from(db_prefix() . 'timesheets_approval_details');
	$CI->db->where('approval_deadline = "'.date('Y-m-d', strtotime(date('Y-m-d').' +1 day')).'"'); // We dont need approval with no 

	$approval_details = $CI->db->get()->result_array();
	$is_rejected = [];
	$is_rejected['rel_id'] = 0;
	$is_rejected['rel_type'] = '';
	foreach ($approval_details as $key => $value) {
		if($value['approve'] == '-1'){
			$is_rejected['rel_id'] = $value['rel_id'];
			$is_rejected['rel_type'] = $value['rel_type'];
		}else{
			if($value['approve'] != '1' && $is_rejected['rel_id'] != $value['rel_id'] && $is_rejected['rel_type'] != $value['rel_type']){
				$email = get_staff_email_by_id($value['staffid']);
				$link = '';

				switch ($value['rel_type']) {
					case 'hr_planning':
					$link = 'timesheets/hr_planning?tab=hr_planning_proposal#' . $value['rel_id'];
					break;

					case 'candidate_evaluation':
					$CI->db->where('id', $value['rel_id']);
					$evaluation = $CI->db->get(db_prefix() . 'rec_evaluation')->row();
					$link = 'recruitment/candidate/' . $evaluation->candidate.'?evaluation='.$value['rel_id'];
					break;
					case 'recruitment_campaign':
					$link = 'recruitment/recruitment_campaign/' . $value['rel_id'];
					break;
					case 'Leave':
					$link = 'timesheets/requisition_detail/' . $value['rel_id'];
					break;
					case 'Late_early':
					$link = 'timesheets/requisition_detail/' . $value['rel_id'];
					break;
					case 'Go_out':
					$link = 'timesheets/requisition_detail/' . $value['rel_id'];
					break;
					case 'Go_on_bussiness':
					$link = 'timesheets/requisition_detail/' . $value['rel_id'];
					break;
					case 'additional_timesheets':
					$link = 'timesheets/requisition_manage?tab=additional_timesheets&additional_timesheets_id='.$value['rel_id'];
					break;
					case 'recruitment_proposal':
					$link = 'recruitment/recruitment_proposal/' . $value['rel_id'];
					break;
					case 'quit_job':
					$link = 'timesheets/requisition_detail/' . $value['rel_id'];
					break;
				}
				$body = '<span>Hi '.get_staff_full_name($value['staffid']).'</span><br /><br /><span>You have a approval reminder expires <a href="'.admin_url($link).'">Link</a></span><br /><br />';
				$CI->emails_model->send_simple_email($email, _l('approval_reminder_expires'), $body);
			}
		}
	}

	$CI->db->select('*');
	$CI->db->from(db_prefix() . 'timesheets_approval_details');
	$CI->db->where('approval_deadline <= "'.date('Y-m-d').'"');
	$CI->db->where('approve IS NULL');
	$approval_overdue = $CI->db->get()->result_array();

	foreach ($approval_overdue as $k => $val) {
		$CI->db->where('id', $val['id']);
		$CI->db->update(db_prefix() . 'timesheets_approval_details', [
			'approve' => '-1',
			'date' => date('Y-m-d H:i:s')
		]);
	}
	return;

}

register_language_files(TIMESHEETS_MODULE_NAME, [TIMESHEETS_MODULE_NAME]);


$CI = & get_instance();
$CI->load->helper(TIMESHEETS_MODULE_NAME . '/timesheets');

/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function timesheets_module_init_menu_items()
{  
	$CI = &get_instance();
	if (has_permission_new('attendance_management', '', 'view_own') ||
		has_permission_new('attendance_management', '', 'view') ||
		has_permission_new('leave_management', '', 'view_own') ||
		has_permission_new('leave_management', '', 'view') ||
		has_permission_new('table_shiftwork_management', '', 'view_own') ||
		has_permission_new('table_shiftwork_management', '', 'view') ||
		has_permission_new('timesheets_shift_management', '', 'view_own') ||
		has_permission_new('timesheets_shift_management', '', 'view') ||
		has_permission_new('timesheets_shift_categories_management', '', 'view_own') ||
		has_permission_new('timesheets_shift_categories_management', '', 'view') ||
		has_permission_new('report_management', '', 'view_own') ||
		has_permission_new('report_management', '', 'view') ||
		has_permission_new('setting_management', '', 'view_own') ||
		has_permission_new('setting_management', '', 'view') ||
		is_admin()) {
		/*$CI->app_menu->add_sidebar_menu_item('timesheets', [
			'name'     => _l('timesheets_and_leave'),
			'icon'     => 'fa fa-user-circle',
			'position' => 30,
		]);*/
	// transfer menu
	/*if (has_permission('attendance_management', '', 'view_own') || has_permission('attendance_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('timesheets', [
			'slug'     => 'timesheets_timekeeping',
			'name'     => _l('attendance_sheet'),
			'href'     => admin_url('timesheets/timekeeping'),
			'icon'     => 'fa fa-pencil-square-o',
			'position' =>1,
		]); 
	}*/
	//
	
	/*if (has_permission('attendance_management', '', 'view_own') || has_permission('attendance_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('timesheets', [
			'slug'     => 'timesheets_timekeeping',
			'name'     => _l('attendance'),
			'href'     => admin_url('timesheets/attendance'),
			'icon'     => 'fa fa-pencil-square-o',
			'position' =>2,
		]); 
	}*/
	// transfer menu
	
	/*if (has_permission('leave_management', '', 'view_own') || has_permission('leave_management', '', 'view') || is_admin()) {          
		$CI->app_menu->add_sidebar_children_item('timesheets', [
			'slug'     => 'timesheets_timekeeping_mnrh',
			'name'     => _l('leave'),
			'icon'     => 'fa fa-clipboard',
			'href'     => admin_url('timesheets/requisition_manage') ,
			'position' => 2,

		]);
	}*/
	
	//
	//transfer menu
	/*if (has_permission('route_management', '', 'view_own') || has_permission('route_management', '', 'view') || is_admin()) {  
		$allow_attendance_by_route = 0;
		$data_by_route = get_timesheets_option('allow_attendance_by_route');
		if($data_by_route){
			$allow_attendance_by_route = $data_by_route;
		}  
		if($allow_attendance_by_route == 1){
			$CI->app_menu->add_sidebar_children_item('timesheets', [
				'slug'     => 'timesheets_route_management',
				'name'     => _l('route_management'),
				'icon'     => 'fa fa-map-signs',
				'href'     => admin_url('timesheets/route_management?tab=route') ,
				'position' => 3,

			]);
		}      
	}*/
	// Transfer menu
	
	/*if (has_permission('table_shiftwork_management', '', 'view_own') || has_permission('table_shiftwork_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('timesheets', [
			'slug'     => 'timesheets_table_shiftwork',
			'name'     => _l('shiftwork'),
			'href'     => admin_url('timesheets/table_shiftwork'),
			'icon'     => 'fa fa-ticket',
			'position' =>4,
		]);
	}
	if (has_permission('table_shiftwork_management', '', 'view_own') || has_permission('table_shiftwork_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('timesheets', [
			'slug'     => 'timesheets_shift_management',
			'name'     => _l('shift_management'),
			'href'     => admin_url('timesheets/shift_management'),
			'icon'     => 'fa fa-calendar',
			'position' =>4,
		]);
	}
	if (has_permission('table_shiftwork_management', '', 'view_own') || has_permission('table_shiftwork_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('timesheets', [
			'slug'     => 'timesheets_shift_type',
			'name'     => _l('shift_type'),
			'href'     => admin_url('timesheets/manage_shift_type'),
			'icon'     => 'fa fa-magic',
			'position' => 5,
		]);
	}
	$data_attendance_by_coordinates = get_timesheets_option('allow_attendance_by_coordinates');
	if($data_attendance_by_coordinates){
		if($data_attendance_by_coordinates == 1){
			if (has_permission('table_workplace_management', '', 'view_own') || has_permission('table_workplace_management', '', 'view') || is_admin()) {
				$CI->app_menu->add_sidebar_children_item('timesheets', [
					'slug'     => 'timesheets_workplace_mgt',
					'name'     => _l('workplace_mgt'),
					'href'     => admin_url('timesheets/workplace_mgt?group=workplace_assign'),
					'icon'     => 'fa fa-street-view',
					'position' => 7,
				]);
			}
		}
	}


	if (has_permission('report_management', '', 'view_own') || has_permission('report_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('timesheets', [
			'slug'     => 'timesheets-report',
			'name'     => _l('reports'),
			'href'     => admin_url('timesheets/reports'),
			'icon'     => 'fa fa-line-chart',
			'position' =>8,
		]);
	}*/
	
}
}

function timesheets_load_js(){
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri,'/admin/timesheets/manage_shift_type') === false)) {
		echo '<script src="'.base_url('modules/timesheets/assets/js/shift_type.js').'?v=' . TIMESHEETS_REVISION.'"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/timekeeping') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/setting') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/add_allocation_shiftwork') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/table_shiftwork') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/shift_management') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/shift_manage.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
	}
	if (!(strpos($viewuri, '/admin/timesheets/workplace_mgt') === false)) {
		if (!(strpos($viewuri, '/admin/timesheets/workplace_mgt?group=workplace_assign') === false)) {
			echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/workplace_assign.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
		}
		else{
			echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/workplace.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
		}
	}

	if (!(strpos($viewuri, '/admin/timesheets/reports') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
	}
	
	$data_timekeeping_form = get_timesheets_option('timekeeping_form');
	if($data_timekeeping_form == 'timekeeping_manually'){
		echo '<script src="'.base_url('modules/timesheets/assets/js/check_in_out_ts.js').'?v=' . TIMESHEETS_REVISION.'"></script>';
		require "modules/timesheets/views/timekeeping/check_in_out.php";
	}
	if (!(strpos($viewuri, '/admin/timesheets/route_management?tab=route_point') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/route_point.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
	}
	if (!(strpos($viewuri, '/admin/timesheets/route_management?tab=route') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}
	else {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/route.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
	}
	if (!(strpos($viewuri, '/admin/timesheets/setting?group=default_settings') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/default_settings.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
	}
	if (!(strpos($viewuri, '/admin/timesheets/requisition_detail') === false)) {
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/requisition_detail.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
	}
	if (!(strpos($viewuri, '/admin/timesheets/route_management?tab=map') === false)) {
		$googlemap_api_key = '';
		$api_key = get_timesheets_option('googlemap_api_key');
		if($api_key){
			$googlemap_api_key = $api_key;
		}	
		echo '<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>';
		echo '<script src="https://maps.googleapis.com/maps/api/js?key='.$googlemap_api_key.'&callback=initMap&libraries=&v=weekly" defer></script>';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/js/route_point.js') . '?v=' . TIMESHEETS_REVISION.'"></script>';
	}
}

function timesheets_add_head_components(){
	$CI = &get_instance();
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri, '/admin/timesheets/timekeeping') === false)) {
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/css/attendance.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/table_shiftwork') === false)) {
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/setting') === false)) {
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/admin/timesheets/add_allocation_shiftwork') === false)) {
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME,'assets/css/add_allocate_shiftwork.css') .'?v=' . TIMESHEETS_REVISION. '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
	}
	$data_timekeeping_form = get_timesheets_option('timekeeping_form');
	if($data_timekeeping_form == 'timekeeping_manually'){
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME,'assets/css/style.css') .'?v=' . TIMESHEETS_REVISION. '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/admin/timesheets/requisition_detail') === false)) {
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME,'assets/css/requisition_detail.css') .'?v=' . TIMESHEETS_REVISION. '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/admin/timesheets/route_management?tab=route') === false)) {
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . module_dir_url(TIMESHEETS_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME,'assets/css/route.css') .'?v=' . TIMESHEETS_REVISION. '"  rel="stylesheet" type="text/css" />';

	}
	if (!(strpos($viewuri, '/admin/timesheets/route_management?tab=map') === false)) {
		echo '<link href="' . module_dir_url(TIMESHEETS_MODULE_NAME,'assets/css/map.css') .'?v=' . TIMESHEETS_REVISION. '"  rel="stylesheet" type="text/css" />';
	}
}

function timesheets_permissions()
{
	$capabilities = [];

	// Attendance
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	register_staff_capabilities('attendance_management', $capabilities, _l('attendance_sheet'),'HR');

	// Leave
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	register_staff_capabilities('leave_management', $capabilities, _l('leave_management'),'HR');
   
   // Work Shift Table
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	register_staff_capabilities('timesheets_table_shiftwork', $capabilities, _l('shiftwork'),'HR');
	
	// Work Shift Managment
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	register_staff_capabilities('table_shiftwork_management', $capabilities, _l('shift_management'),'HR');
	
	// Manage shift Type
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	register_staff_capabilities('timesheets_shift_type', $capabilities, _l('shift_type'),'HR');
	
   
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	//register_staff_capabilities('route_management', $capabilities, _l('route_management'),'HR');
	// Additional timesheets
	/*$capabilities['capabilities'] = [
		'view_own' => _l('permission_view'),
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')]
	];

	register_staff_capabilities('additional_timesheets_management', $capabilities, _l('additional_timesheets_management'),'HR');
*/
	

	// Report
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	register_staff_capabilities('report_management', $capabilities, "Timesheet Reports",'HR');
	// Workplace
	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
	];
	//register_staff_capabilities('table_workplace_management', $capabilities, _l('table_workplace_management'),'HR');
}

function after_render_top_search_timesheets(){
	$CI = &get_instance();
	$data_timekeeping_form = get_timesheets_option('timekeeping_form');
	if($data_timekeeping_form == 'timekeeping_manually'){
		echo '<li class="dropdown">
		<a href="#" class="check_in_out_timesheet" data-toggle="tooltip" title="" onclick="open_check_in_out();" data-placement="bottom" data-original-title="'._l('check_in').' / '._l('check_out').'"><i class="fa fa-history fa-fw fa-lg"></i>
		<span class="label bg-warning hide">0</span>
		</a>' ;
		echo '</li>';
	}
}

function checkout_after_client_logout(){
	$CI = &get_instance();
	$auto_checkout = get_timesheets_option('auto_checkout');
	if($auto_checkout == 1){
		// Standard logout end of shift
		$auto_checkout_type = get_timesheets_option('auto_checkout_type');
		if($auto_checkout_type == 2){
			auto_checkout(get_staff_user_id(), date('Y-m-d H:i:s'));
		}
	}
}
function auto_checkout_cron(){
	$CI = &get_instance();
	$CI->load->model('timesheets/timesheets_model');
	$CI->load->model('departments_model');
	$auto_checkout = get_timesheets_option('auto_checkout');
	$result_list = [];
	if($auto_checkout == 1){
		$current_date = date('Y-m-d H:i:s');
		$auto_checkout_type = get_timesheets_option('auto_checkout_type');
		$result_list = $CI->timesheets_model->get_hour_auto_checkout_type($auto_checkout_type);				
		foreach ($result_list as $k_list => $item) {
			if(strtotime($current_date) >= strtotime($item['effective_time'])){
				$data['staff_id'] = $item['staffid'];
				$data['date'] = $item['time_checkout'];
				$data['route_point_id'] = 0;
				$data['type_check'] = 2;
				$data['type'] = 'W';
				$CI->db->insert(db_prefix().'check_in_out', $data);
				$insert_id = $CI->db->insert_id();
				if ($insert_id) {
					$res = $CI->timesheets_model->add_check_in_out_value_to_timesheet($item['staffid'], $item['checkout_date']);
				}  
			}
		}
	}
	return;
}

function auto_remider_checkin(){
	$CI = &get_instance();
	$CI->load->model('timesheets/timesheets_model');
	$CI->load->model('departments_model');
	$send_notification_if_check_in_forgotten = get_timesheets_option('send_notification_if_check_in_forgotten');
	$result_list = [];
	if($send_notification_if_check_in_forgotten == 1){
		$current_date = date('Y-m-d H:i:s');
		$value_minute = get_timesheets_option('send_notification_if_check_in_forgotten_value');
		$result_list = $CI->timesheets_model->get_datetime_send_notification_forgotten_value($value_minute);
		foreach ($result_list as $k_list => $item) {
			if(strtotime($current_date) >= strtotime($item['effective_time'])){
				$CI->timesheets_model->send_mail_remider_check_in($item['staffid']);
			}
		}
	}
	return;
}


