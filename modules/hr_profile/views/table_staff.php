<?php

defined('BASEPATH') or exit('No direct script access allowed');

$has_permission_delete = has_permission_new('hrm_hr_records', '', 'delete');
$has_permission_edit   = has_permission_new('hrm_hr_records', '', 'edit');
$has_permission_create = has_permission_new('hrm_hr_records', '', 'create');

$custom_fields = get_custom_fields('staff', [
	'show_on_table' => 1,
]);
$aColumns = [

	'firstname',
	'staff_comp',
	'phonenumber',
	'state',
	'staffid',
	'team_manage',
	db_prefix().'hr_job_position.position_name',
	'active',
	'status_work',
];
$sIndexColumn = 'staffid';
$sTable       = db_prefix().'staff';
$join         = [
	'LEFT JOIN '.db_prefix().'roles ON '.db_prefix().'roles.roleid = '.db_prefix().'staff.role',
	'LEFT JOIN '.db_prefix().'hr_job_position ON '.db_prefix().'hr_job_position.position_id = '.db_prefix().'staff.job_position',
	
];
$i            = 0;
foreach ($custom_fields as $field) {
	$select_as = 'cvalue_' . $i;
	if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
		$select_as = 'date_picker_cvalue_' . $i;
	}
	array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
	array_push($join, 'LEFT JOIN '.db_prefix().'customfieldsvalues as ctable_' . $i . ' ON '.db_prefix().'staff.staffid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
	$i++;
}
if (count($custom_fields) > 4) {
	@$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$where = hooks()->apply_filters('staff_table_sql_where', []);
$where = array();

$department_id = $this->ci->input->post('hr_profile_deparment');
if(isset($department_id) && strlen($department_id) > 0){

	$departmentgroup = $this->ci->hr_profile_model->get_staff_in_deparment($department_id);
	if (count($departmentgroup) > 0) {

		$where[] = 'AND '.db_prefix().'staff.staffid IN (SELECT staffid FROM '.db_prefix().'staff_departments WHERE departmentid IN (' . implode(', ', $departmentgroup) . '))';
	}

}

if($this->ci->input->post('status_work')){
	$where_status = '';
	$status = $this->ci->input->post('status_work');
	foreach ($status as $statues) {
		if($status != '')
		{
			if($where_status == ''){
				$where_status .= ' ('.db_prefix().'staff.status_work in ("'.$statues.'")';
			}else{
				$where_status .= ' or '.db_prefix().'staff.status_work in ("'.$statues.'")';
			}
		}
	}
	if($where_status != '')
	{
		$where_status .= ')';
		if($where != ''){
			array_push($where, 'AND'. $where_status);
		}else{
			array_push($where, $where_status);
		}
		
	}
}          

if($this->ci->input->post('hr_profile_report_to')){
	
	$hr_profile_report_to = $this->ci->input->post('hr_profile_report_to');
	
		$where_report_to = ' AND '.db_prefix().'staff.team_manage = "'.$hr_profile_report_to.'"';
			array_push($where, $where_report_to);
} 

if($this->ci->input->post('status')){
	
	$status = $this->ci->input->post('status');
	if($status == "all"){
	    $status = ' AND '.db_prefix().'staff.active = "0"';
		array_push($where, $status);
	}
	if($status == "1"){
	    $status = ' AND '.db_prefix().'staff.active = "1"';
		array_push($where, $status);
	}
	if($status == "0"){
	    $status = ' AND '.db_prefix().'staff.active = "0"';
		array_push($where, $status);
	}
		
} 
    if(is_admin()){
        
    }else{
        $only_nonadmin = ' AND '.db_prefix().'staff.admin = "0"';
    }
    
	array_push($where, $only_nonadmin);

if($this->ci->input->post('hr_profile_state')){
	
	$hr_profile_state = $this->ci->input->post('hr_profile_state');
	
		$where_state = ' AND '.db_prefix().'staff.state = "'.$hr_profile_state.'"';
			array_push($where, $where_state);
} 



if($this->ci->input->post('staff_role')){
	$where_role = '';
	$staff_role      = $this->ci->input->post('staff_role');
	foreach ($staff_role as $staff_id) {
		if($staff_id != '')
		{
			if($where_role == ''){
				$where_role .= '( '.db_prefix().'staff.job_position in ('.$staff_id.')';
			}else{
				$where_role .= ' or '.db_prefix().'staff.job_position in ('.$staff_id.')';
			}
		}
	}

	if($where_role != '')
	{
		$where_role .= ' )';
		if($where_role != ''){
			array_push($where, 'AND '. $where_role);
		}else{
			array_push($where, $where_role);
		}

	}
	
}

if($this->ci->input->post('staff_type')){
	$where_staff_type = '';
	$staff_type      = $this->ci->input->post('staff_type');
	foreach ($staff_type as $stafftype_id) {
		if($stafftype_id != '')
		{
			if($where_staff_type == ''){
				$where_staff_type .= '( '.db_prefix().'staff.role in ('.$stafftype_id.')';
			}else{
				$where_staff_type .= ' or '.db_prefix().'staff.role in ('.$stafftype_id.')';
			}
		}
	}

	if($where_staff_type != '')
	{
		$where_staff_type .= ' )';
		if($where_staff_type != ''){
			array_push($where, 'AND '. $where_staff_type);
		}else{
			array_push($where, $where_staff_type);
		}

	}
	
}


$manages = $this->ci->input->post('staff_teammanage');
if(isset($manages) && strlen($manages) > 0){

	$where[] = '  AND staffid IN (select 
	staffid 
	from    (select * from '.db_prefix().'staff as s
	order by s.team_manage, s.staffid) departments_sorted,
	(select @pv := '.$manages.') initialisation
	where   find_in_set(team_manage, @pv)
	and     length(@pv := concat(@pv, ",", staffid)) OR staffid ='.$manages.')';
}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'firstname',
	'phonenumber',
	'state',
	'staff_identifi',
	'profile_image',
	'lastname',
	db_prefix().'staff.staffid',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
			$_data = '<div style="width:100%;">'.$aRow[strafter($aColumns[$i], 'as ')].'</div>';
		} else {
			$_data = '<div style="width:100%;">'.$aRow[$aColumns[$i]].'</div>';
		}
		if($aColumns[$i] == 'staff_comp'){
		    $staff_company = unserialize($aRow['staff_comp']);
		    $stf_comp_name = "";
		    $j =1;
		    foreach ($staff_company as $key => $value) {
                # code...
                $name = get_comp_name_by_id($value);
                
                if($j>1){
                    $stf_comp_name .= ",";
                }
                $stf_comp_name .= $name;
                $j++;
                
            }

			//$_data = '<div style="width:100%;">'.$stf_comp_name.'</div>';
			$_data = '<div style="width:100%;">'.$stf_comp_name.'</div>';
		}elseif($aColumns[$i] == 'birthday'){
			$_data = '<div style="width:100%;">'._d($aRow['birthday']).'</div>';
		}elseif($aColumns[$i] == 'last_login'){
			$_data = '<div style="width:100%;">'._d($aRow['last_login']).'</div>';
		}/*
		elseif($aColumns[$i] == 'sex'){
			$_data = _l($aRow['sex']);
        
        }*//*elseif($aColumns[$i] == 'status_work'){
			//$_data = _l($aRow['status_work']);
			$status_work= $aRow['status_work'];
			if($status_work == "inactivity"){
			    $_data = "Not Working";
			}
			if($status_work == "working") {
			    $_data = "Working";
			}
			if($status_work == "maternity_leave") {
			    $_data = "Maternity leave ";
			}
			//$_data = "hello";
		}     */    
		elseif ($aColumns[$i] == 'active') {
			/*$checked = '';
			if ($aRow['active'] == 1) {
				$checked = 'checked';
			}
			$_data = '<div style="width:100%;"><div class="onoffswitch">
			<input type="checkbox" ' . (($aRow['staffid'] == get_staff_user_id() || (is_admin($aRow['staffid']) || !has_permission_new('hrm_hr_records', '', 'edit')) && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'hr_profile/change_staff_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['staffid'] . '" data-id="' . $aRow['staffid'] . '" ' . $checked . '>
			<label class="onoffswitch-label" for="c_' . $aRow['staffid'] . '"></label>
			</div></div>';

			$_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';*/
            if($aRow['active'] == 1){
                $status = "Active";
            }else{
                $status = "In-active";
            }
            $_data = $status;
		} elseif ($aColumns[$i] == 'firstname') {
			
			/*$_data = '<div style="width:100%;"><a href="#" onclick="hr_profile_update_staff_manage_view(' . $aRow['staffid'] . ');return false;">' . staff_profile_image($aRow['staffid'], [
				'staff-profile-image-small',
			]) . '</a>';*/
			if (has_permission_new('hrm_hr_records', '', 'view')  ) {
				$_data = $aRow['firstname'] . ' ' . $aRow['lastname'];
			}
			if (has_permission_new('hrm_hr_records', '', 'edit')) {
			$_data = '<a href="' . admin_url('hr_profile/new_member/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
			
			
			}

			//$_data .= '</div>';
		} elseif ($aColumns[$i] == 'email') {
			$_data = '<div style="width:100%;"><a href="mailto:' . $_data . '">' . $_data . '</a></div>';
		} elseif ($aColumns[$i] == 'state') {
		    $state = '<div style="width:100%;">'.get_state_name_by_id($aRow['state']).'</div>';
			$_data = $state;
		} elseif ($aColumns[$i] == 'staffid') {
		    if($aRow['team_manage'] !== "" && $aRow['team_manage'] == null){
		        $team_man = "";
		    }else{
		        $team_man = '<div style="width:100%;">'.get_staff_full_name($aRow['team_manage']).'</div>';
		    }
		    
			$_data = $team_man;
		} elseif ($aColumns[$i] == 'team_manage') {
			if($aRow['staffid'] != ''){
				$team = $this->ci->hr_profile_model->get_staff_departments($aRow['staffid']);
				$str = '';
				$j = 0;
				foreach ($team as $value) {
					$j++;
					$str .= '<div style="width:100%;"><span class="label label-tag tag-id-1" style="border:0px;"><span class="tag">'.$value['name'];
					
					$str .= '</span><span class="hide">, </span></span>&nbsp';
					if($j%2 == 0){
						$str .= '<br><br/></div>';
					}
					
				}
				$_data = $str;
			}
			else{
				$_data = '';
			}
		}/*elseif($aColumns[$i] == 'nation'){
			//$_data = '<div class="checkbox"><input type="checkbox" value="' . $aRow['staffid'] . '"><label></label></div>';
			$_data = '';
		}*/
		else {
			if (strpos($aColumns[$i], 'date_picker_') !== false) {
				$_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
			}
		}
		$row[] = $_data;
	}

	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
