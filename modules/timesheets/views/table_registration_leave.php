<?php

defined('BASEPATH') or exit('No direct script access allowed');
$this->ci->load->model('timesheets_model');
$user_id = get_staff_user_id();

$aColumns = [
    '1',
    db_prefix().'timesheets_requisition_leave.id',
    db_prefix().'timesheets_requisition_leave.id',
    db_prefix().'timesheets_requisition_leave.staff_id',
    db_prefix().'timesheets_requisition_leave.followers_id',
    db_prefix().'timesheets_requisition_leave.subject',
    db_prefix().'timesheets_requisition_leave.reason',
    db_prefix().'timesheets_requisition_leave.start_time',
    db_prefix().'timesheets_requisition_leave.end_time',
    '(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM '.db_prefix().'timesheets_approval_details WHERE rel_id = '.db_prefix().'timesheets_requisition_leave.id and '.db_prefix().'timesheets_approval_details.rel_type = IF('.db_prefix().'timesheets_requisition_leave.rel_type = 1,"Leave", IF('.db_prefix().'timesheets_requisition_leave.rel_type = 2,"Late_early", IF('.db_prefix().'timesheets_requisition_leave.rel_type = 3,"Go_out", IF('.db_prefix().'timesheets_requisition_leave.rel_type = 4,"Go_on_bussiness", IF('.db_prefix().'timesheets_requisition_leave.rel_type = 5,"quit_job", "")))))) as approver',
    db_prefix().'timesheets_requisition_leave.status',
    db_prefix().'timesheets_requisition_leave.datecreated',
    db_prefix().'timesheets_requisition_leave.id',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'timesheets_requisition_leave';
$join = ['LEFT JOIN '.db_prefix().'staff b ON b.staffid = '.db_prefix().'timesheets_requisition_leave.staff_id',
'LEFT JOIN '.db_prefix().'roles ON '.db_prefix().'roles.roleid = b.role',
];
$where = [];

if(!is_admin() && !has_permission('leave_management','','view')){
    array_push($where, ' AND ('.get_staff_user_id() .' in (select staffid from '.db_prefix().'timesheets_approval_details where rel_type = "Leave" and rel_id = '.db_prefix().'timesheets_requisition_leave.id) or '.db_prefix().'timesheets_requisition_leave.staff_id = '.get_staff_user_id().')');
}

if($this->ci->input->post('status_filter')){
    $where_status = '';
    $status = $this->ci->input->post('status_filter');
    foreach ($status as $statues) {

        if($status != '')
        {
            if($where_status == ''){
                $where_status .= ' AND (status = "'.$statues. '"';
            }else{
                $where_status .= ' OR status = "' .$statues.'"';
            }
        }
    }
    if($where_status != '')
    {   
        $where_status .= ')';
        array_push($where,  $where_status);
    }
}

if($this->ci->input->post('department_filter')){
    $where_dpm = '';
    $department = $this->ci->input->post('department_filter');
    foreach ($department as $statues) {
        if($department != '')
        {
            if($where_dpm == ''){
                $where_dpm = ' AND (staff_id IN (SELECT staffid FROM '.db_prefix().'staff_departments WHERE departmentid = '.$statues.')';
                
            }else{
                $where_dpm .= 'OR staff_id IN (SELECT staffid FROM '.db_prefix().'staff_departments WHERE departmentid = '.$statues.')';
            }
        }
    }
    if($where_dpm != '')
    {   
        $where_dpm .= ')';
        array_push($where, $where_dpm);
    }
}

if($this->ci->input->post('rel_type_filter')){
    $where_rel_type = '';
    $rel_type = $this->ci->input->post('rel_type_filter');
    foreach ($rel_type as $statues) {
        if($rel_type != '')
        {
            if($where_rel_type == ''){
                $where_rel_type .= ' AND (rel_type = "'.$statues. '"';
            }else{
                $where_rel_type .= ' or rel_type = "' .$statues.'"';
            }
        }
    }
    if($where_rel_type != '')
    {
        $where_rel_type .= ')';
        array_push($where, $where_rel_type);
    }
}

if($this->ci->input->post('chose')){
    $chose = $this->ci->input->post('chose');
    $sql_where = '';
    if($chose != 'all'){
        if($sql_where != ''){
            $sql_where .= ' AND ('.get_staff_user_id().' IN (SELECT staffid FROM '.db_prefix().'timesheets_approval_details where '.db_prefix().'timesheets_approval_details.rel_type IN ("Leave","late","early","Go_out","Go_on_bussiness") AND '.db_prefix().'timesheets_approval_details.rel_id = '.db_prefix().'timesheets_requisition_leave.id ))';
        }else{
            $sql_where .= '('.get_staff_user_id().' IN (SELECT staffid FROM '.db_prefix().'timesheets_approval_details where '.db_prefix().'timesheets_approval_details.rel_type IN ("Leave","late","early","Go_out","Go_on_bussiness") AND '.db_prefix().'timesheets_approval_details.rel_id = '.db_prefix().'timesheets_requisition_leave.id ))';
        }
    }else{
        $sql_where = '';
    }
    if($sql_where != '')
    {
        array_push($where, 'AND '. $sql_where);
    }
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'timesheets_requisition_leave.rel_type',db_prefix().'timesheets_requisition_leave.subject', 'b.firstname','reason', db_prefix().'timesheets_requisition_leave.followers_id', db_prefix().'timesheets_requisition_leave.status as status', db_prefix().'timesheets_requisition_leave.datecreated']);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];   
    $row[] = $aRow[db_prefix().'timesheets_requisition_leave.id'];  

    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow[db_prefix().'timesheets_requisition_leave.id'] . '"><label></label></div>';

    $row[] = '<div class="row">'
    .'<a class="col-md-10" href="'.admin_url('timesheets/requisition_detail'. '/' . ($aRow[db_prefix().'timesheets_requisition_leave.id']) ).'">' . $aRow['subject'] . '</a>'
    .'</div>';

    $row[] = '<a data-toggle="tooltip" data-title="' .get_staff_full_name($aRow[db_prefix().'timesheets_requisition_leave.staff_id']) . '" href="' . admin_url('profile/' . $aRow[db_prefix().'timesheets_requisition_leave.staff_id']) . '">' . staff_profile_image($aRow[db_prefix().'timesheets_requisition_leave.staff_id'], [
        'staff-profile-image-small',
    ]) . ' ' . get_staff_full_name($aRow[db_prefix().'timesheets_requisition_leave.staff_id']) . '</a><span class="hide">' . get_staff_full_name($aRow[db_prefix().'timesheets_requisition_leave.staff_id']) . '</span>';


    $row[] = _d($aRow[db_prefix().'timesheets_requisition_leave.start_time']);  
    $row[] = _d($aRow[db_prefix().'timesheets_requisition_leave.end_time']);  

    $list_member_approve = [];

    $membersOutput = '';

    $members       = explode(',', $aRow['approver']);
    $list_member = '';
    $exportMembers = '';
    foreach ($members as $key => $member_id) {
        if ($member_id != '') {
            $member_name = get_staff_full_name($member_id);
            $list_member .= '<li class="text-success mbot10 mtop"><a href="' . admin_url('profile/' . $member_id) . '" class="avatar cover-image text-align-left">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
            ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member_name,
            ]) .' '.$member_name. '</a></li>';
            if($key <= 2){
                $membersOutput .= '<span class="avatar cover-image brround">' .
                staff_profile_image($member_id, [
                    'staff-profile-image-small mright5',
                ], 'small', [
                    'data-toggle' => 'tooltip',
                    'data-title'  => $member_name,
                ]) . '</span>';
            }
            // For exporting
            $exportMembers .= $member_name . ', ';
            $list_member_approve[] = $member_id;
        }
    }
    if(count($members) > 3){
        $membersOutput .= '<span class="avatar bg-secondary brround avatar-none">+'. (count($members) - 3) .'</span>';
    }

    $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';

    $membersOutput1 = '<div class="task-info task-watched task-info-watched">
    <h5>
    <div class="btn-group">
    <span class="task-single-menu task-menu-watched">
    <div class="avatar-list avatar-list-stacked" data-toggle="dropdown">'.$membersOutput.'</div>
    <ul class="dropdown-menu list-staff" role="menu">
    <li class="dropdown-plus-title">
    '. _l('approver') .'
    </li>
    '.$list_member.'
    </ul>
    </span>
    </div>
    </h5>
    </div>';




    $liss = '';
    $approce = '';
    
    $row[] = $membersOutput;
    if($aRow[db_prefix().'timesheets_requisition_leave.followers_id'] != 0){
       $row[] = '<a data-toggle="tooltip" data-title="' .get_staff_full_name($aRow[db_prefix().'timesheets_requisition_leave.followers_id']) . '" href="' . admin_url('profile/' . $aRow[db_prefix().'timesheets_requisition_leave.followers_id']) . '">' . staff_profile_image($aRow[db_prefix().'timesheets_requisition_leave.followers_id'], [
        'staff-profile-image-small',
    ]) . ' ' . get_staff_full_name($aRow[db_prefix().'timesheets_requisition_leave.followers_id']) . '</a><span class="hide">' . get_staff_full_name($aRow[db_prefix().'timesheets_requisition_leave.followers_id']) . '</span>';
   }else{
    $row[] = '';
}

$row[] = $aRow['reason'];
if($aRow['rel_type'] == 1){
    $rel_type = 'Leave';
    $row[] = '<p>'. _l('Leave') .'</p>';
}else if($aRow['rel_type'] == 2 ){
   $rel_type = 'late';
   $row[] = '<p>'. _l('late') .'</p>';
}else if($aRow['rel_type'] == 3 ){
   $rel_type = 'Go_out';
   $row[] = '<p>'. _l('Go_out') .'</p>';
}else if($aRow['rel_type'] == 4 ){
   $rel_type = 'Go_on_bussiness';
   $row[] = '<p>'. _l('Go_on_bussiness') .'</p>';
}
else if($aRow['rel_type'] == 6){
   $rel_type = 'early';
   $row[] = '<p>'. _l('early') .'</p>';
}
else{
   $rel_type = 'quit_job'; 
   $row[] = '<p>'. _l('quit_job') .'</p>';
}            
if($aRow['status'] == 0){
    $row[] = '<span class="label label-primary  mr-1 mb-1 mt-1">'. _l('Create') .'</span>';            
}else if($aRow['status'] == 1){
   $row[] = '<span class="label label-success  mr-1 mb-1 mt-1">'. _l('approved') .'</span>';
}else{
    $row[] = '<span class="label label-danger  mr-1 mb-1 mt-1">'. _l('Reject') .'</span>';
}

$row[] = _d(date('Y-m-d', strtotime($aRow[db_prefix().'timesheets_requisition_leave.datecreated'])));  

$action_option = '<div class="row">';
if(in_array($user_id, $list_member_approve)){
    $data_check_approve_status = $this->ci->timesheets_model->check_approval_details(($aRow[db_prefix().'timesheets_requisition_leave.id']),$rel_type); 
    if(isset($data_check_approve_status['staffid'])){
        if($data_check_approve_status['staffid']){
            if(in_array($user_id, $data_check_approve_status['staffid'])){
               $action_option .='<span data-placement="top" data-toggle="tooltip" data-title="'._l('approve').'" onclick="approve_request('.($aRow[db_prefix().'timesheets_requisition_leave.id']).',\''.$rel_type.'\');" class="btn btn-success btn-icon mleft5"><i class="fa fa-check"></i></span>';
               $action_option .='<span data-placement="top" data-toggle="tooltip" data-title="'._l('deny').'" onclick="deny_request('.($aRow[db_prefix().'timesheets_requisition_leave.id']).',\''.$rel_type.'\');" class="btn btn-primary btn-icon"><i class="fa fa-ban"></i></span>';
           }                    
       }
   }
}   

if(is_admin() || $aRow['status'] == 0){                     
    $action_option .='<a id="delete-insurance" data-placement="top" data-toggle="tooltip" data-title="'._l('delete').'" href="'.admin_url('timesheets/delete_requisition'. '/' . ($aRow[db_prefix().'timesheets_requisition_leave.id']) ).'" class="btn btn-danger btn-icon _delete">' . '<i class="fa fa-remove"></i>' . '</a>';
}
$row[] = $action_option.'</div>';       

$row['DT_RowClass'] = 'has-row-options';
$output['aaData'][] = $row;
}


