<?php

defined('BASEPATH') or exit('No direct script access allowed');
$has_permission_delete = has_permission('staff', '', 'delete');
$has_permission_edit   = has_permission('staff', '', 'edit');
$has_permission_create = has_permission('staff', '', 'create');
$custom_fields = get_custom_fields('staff', [
	'show_on_table' => 1,
]);
$aColumns = [
	db_prefix().'rec_transfer_records.staffid',
	db_prefix().'rec_transfer_records.firstname',  
	db_prefix().'rec_transfer_records.staff_identifi',
	db_prefix().'rec_transfer_records.birthday',
	db_prefix().'rec_transfer_records.staffid',
];
$sIndexColumn = 'lastname';
$sTable       = db_prefix().'rec_transfer_records';
$join         = [];
$i            = 0;

$join         = [
	'LEFT JOIN '.db_prefix().'staff on '.db_prefix().'staff.staffid = '.db_prefix().'rec_transfer_records.staffid',
];



$where = array();
$where = [];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'profile_image',
	db_prefix().'rec_transfer_records.id',
	db_prefix().'staff.lastname',
	db_prefix().'staff.firstname',
	db_prefix().'rec_transfer_records.staffid',
	db_prefix().'staff.staff_identifi',
	db_prefix().'staff.birthday',
	db_prefix().'staff.job_position',
]);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['staff_identifi'];  
	$_data = '<a href="' . admin_url('hr_profile/member/' . $aRow[db_prefix().'rec_transfer_records.staffid']) . '">' . staff_profile_image($aRow[db_prefix().'rec_transfer_records.staffid'], [
		'staff-profile-image-small',
	]) . '</a>';
	$_data .= ' <a href="' . admin_url('hr_profile/member/' . $aRow[db_prefix().'rec_transfer_records.staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
	$row[] = $_data; 


	$name_position = '';
	if($aRow['job_position']){
		if($aRow['job_position'] != ''){
			$position = $this->ci->hr_profile_model->get_job_position($aRow['job_position']); 
			if(isset($position)){
				if(isset($position->position_name)){
					$name_position = $position->position_name;
				} 
			} 
		}
	}
	$row[] = $name_position;  

	$name_department = '';
	if($aRow[db_prefix().'rec_transfer_records.staffid']){
		if($aRow[db_prefix().'rec_transfer_records.staffid'] != ''){
			$department = $this->ci->hr_profile_model->get_department_by_staffid($aRow[db_prefix().'rec_transfer_records.staffid']);    
			if(isset($department)){
				$name_department = $department->name;
			}        
		}
	}
	$row[] = $name_department;


	$percent = $this->ci->get_percent_complete($aRow['staffid']);



	$back_ground = 'bg-green';
	if($percent < 33.33) {
		$back_ground = 'bg-danger';
	}
	if(($percent >= 33.33) && ($percent <=80)){
		$back_ground = 'bg-secondary';
	} 
	if($percent>0){
		$percen_w = $percent;
	}
	else{
		$percen_w = 15;
		$percent = 0;
	}
	$data_progress = '<div class="col-md-12 d-flex justify-content-between align-items-center">
	<div class="progress-bar '.$back_ground.' task-progress-bar-ins-427" id="427" style    =     "width:'.$percen_w.'%; border-radius: 1em;">'.$percent.'%</div>
	</div>
	</div>';
	$row[] = $data_progress; 
	if($percent<100){
		$output['aaData'][] = $row;
	} 
}
