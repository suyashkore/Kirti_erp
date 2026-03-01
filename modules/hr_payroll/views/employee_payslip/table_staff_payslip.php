<?php

defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency();

$aColumns = [
	db_prefix().'hrp_payslip_details.month',
	'pay_slip_number',
	'gross_pay',
	'total_deductions',
	'income_tax_paye',
	'it_rebate_value',
	'commission_amount',
	'bonus_kpi',
	'total_insurance',
	'net_pay',
	'total_cost',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'hrp_payslip_details';

$join = [
	'LEFT JOIN ' . db_prefix() . 'hrp_payslips ON ' . db_prefix() . 'hrp_payslip_details.payslip_id = ' . db_prefix() . 'hrp_payslips.id',
];

$where  = [];
$filter = [];


if($this->ci->input->post('memberid')){
	$where_staff = '';
	$staffs = $this->ci->input->post('memberid');
	if($staffs != '')
	{
		if($where_staff == ''){
			$where_staff .= ' where '.db_prefix().'hrp_payslip_details.staff_id = "'.$staffs. '"';
		}else{
			$where_staff .= ' or '.db_prefix().'hrp_payslip_details.staff_id = "' .$staffs.'"';
		}
	}
	if($where_staff != '')
	{
		array_push($where, $where_staff);
	}
}
array_push($where, 'AND '.db_prefix().'hrp_payslips.payslip_status = "payslip_closing"');


// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'hrp_payslip_details.id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['id'];

	if (has_permission('hrm_contract', '', 'view') || is_admin()) {
		$subjectOutput = '<a href="#" onclick="member_view_payslip(' . $aRow['id'] . ');return false;">' . $aRow['pay_slip_number'] . '</a>';
	}else{
		$subjectOutput = $aRow['pay_slip_number'];
	}

	$subjectOutput .= '<div class="row-options">';
		$subjectOutput .= '<a href="#" onclick="member_view_payslip(' . $aRow['id'] . ');return false;">' . _l('hr_view') .' </a>';
	$subjectOutput .= '</div>';

	$row[] = $subjectOutput;

	$row[] = date('m-Y',strtotime($aRow[db_prefix().'hrp_payslip_details.month']));
	$row[] = app_format_money($aRow['gross_pay'], '');
	$row[] = app_format_money($aRow['total_deductions'], '');
	$row[] = app_format_money($aRow['income_tax_paye'], '');
	$row[] = app_format_money($aRow['it_rebate_value'],'');
	$row[] = app_format_money($aRow['commission_amount'], '');
	$row[] = app_format_money($aRow['bonus_kpi'], '');
	$row[] = app_format_money($aRow['total_insurance'], '');
	$row[] = app_format_money($aRow['net_pay'], '');
	$row[] = app_format_money($aRow['total_cost'], '');

	$row['DT_RowClass'] = 'has-row-options';
	
	$output['aaData'][] = $row;
}
