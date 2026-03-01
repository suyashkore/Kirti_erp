<?php

defined('BASEPATH') or exit('No direct script access allowed');

$has_permission_delete = has_permission('staff', '', 'delete');
$has_permission_edit   = has_permission('staff', '', 'edit');
$has_permission_create = has_permission('staff', '', 'create');

$custom_fields = get_custom_fields('staff', [
    'show_on_table' => 1,
    ]);
$aColumns = [
    'id',
    'staff_id',
    'voucher_id',
    'actcode',
    'actname',
    'drcr_type',
    'debit_amt',
    
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'contra';
$join         = [];
/*$i            = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_' . $i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_' . $i;
    }
    array_push($aColumns, 'ctable_' . $i . '.value as ' . $select_as);
    
    $i++;
}*/
            // Fix for big queries. Some hosting have max_join_limit
/*if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}*/

//$where = hooks()->apply_filters('staff_table_sql_where', []);
$where = [];

//$department_id = $this->ci->input->post('hrm_deparment');

//if(isset($department_id) && strlen($department_id) > 0){

    /*$where[] = ' AND departmentid IN (select 
        departmentid 
        from    (select * from '.db_prefix().'departments
        order by '.db_prefix().'departments.parent_id, '.db_prefix().'departments.departmentid) departments_sorted,
        (select @pv := '.$department_id.') initialisation
        where   find_in_set(parent_id, @pv)
        and     length(@pv := concat(@pv, ",", departmentid)) OR departmentid = '.$department_id.')';*/
//}

//if($this->ci->input->post('status_work')){
    /*$where_status = '';
    $status = $this->ci->input->post('status_work');
        foreach ($status as $statues) {
            if($status != '')
            {
                if($where_status == ''){
                    $where_status .= ' status_work = "'.$statues. '"';
                }else{
                    $where_status .= ' or status_work = "' .$statues.'"';
                }
            }
        }
        if($where_status != '')
        {

            array_push($where, 'AND '. $where_status);
        }*/
//}

//if($this->ci->input->post('staff_role')){
    /*$where_role = '';
    $staff_role      = $this->ci->input->post('staff_role');
        foreach ($staff_role as $staff_id) {
            if($staff_id != '')
            {
                if($where_role == ''){
                    $where_role .= '( '.db_prefix().'staff.role = '.$staff_id;
                }else{
                    $where_role .= ' or '.db_prefix().'staff.role = '.$staff_id;
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

        }*/
            
//}


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where);
   

$output  = $result['output'];
$rResult = $result['rResult'];

/*foreach ($rResult as $aRow) {
    $row = [];
    $row[] = $aRow['id'];
    $row[] = $aRow['staff_id'];
    $row[] = $aRow['actcode'];
    $row[] = $aRow['actname'];
    $row[] = $aRow['drcr_type'];
    $row[] = $aRow['debit_amt'];
    $row[] = $aRow['credit_amt'];*/
    $checked = 'checked';
            /*if ($aRow['active'] == 1) {
                $checked = 'checked';
            }
*/
            $status = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="" name="onoffswitch" class="onoffswitch-checkbox" id="c_" data-id="c_" ' . $checked . '>
                <label class="onoffswitch-label" for="c_"></label>
            </div>';

            // For exporting
            $status .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
$row = [];
    $row[] = "1";
    $row[] = "0185";
    $row[] = "Own";
    $row[] = "170";
    $row[] = "28-03-2021";
    $row[] = $status;
    $row[] = '<a href="' . admin_url('vehicle/vehicle_manage') . '" >' ."<i class='fa fa-pencil-square' style='font-size: 25px;'></i>". '</a>  &nbsp;&nbsp;&nbsp;<a href="' . admin_url('vehicle/vehicle_manage'). '" >' ."<i class='fa fa-trash' style='font-size: 25px;color: red;'></i>". '</a>';
    
    
    $row = hooks()->apply_filters('staff_table_sql_where', $row, $aRow);

    $output['aaData'][] = $row;

    $row = [];
    $row[] = "2";
    $row[] = "0321";
    $row[] = "Own";
    $row[] = "250";
    $row[] = "29-06-2020";
    $row[] = $status;
    $row[] = '<a href="' . admin_url('vehicle/vehicle_manage') . '" >' ."<i class='fa fa-pencil-square' style='font-size: 25px;'></i>". '</a>  &nbsp;&nbsp;&nbsp;<a href="' . admin_url('vehicle/vehicle_manage'). '" >' ."<i class='fa fa-trash' style='font-size: 25px;color: red;'></i>". '</a>';
    
    
    $row = hooks()->apply_filters('staff_table_sql_where', $row, $aRow);

    $output['aaData'][] = $row;

    $row = [];
    $row[] = "3";
    $row[] = "1264";
    $row[] = "Own";
    $row[] = "60";
    $row[] = "16-09-2017";
    $row[] = $status;
    $row[] = '<a href="' . admin_url('vehicle/vehicle_manage') . '" >' ."<i class='fa fa-pencil-square' style='font-size: 25px;'></i>". '</a>  &nbsp;&nbsp;&nbsp;<a href="' . admin_url('vehicle/vehicle_manage'). '" >' ."<i class='fa fa-trash' style='font-size: 25px;color: red;'></i>". '</a>';
    
    
    $row = hooks()->apply_filters('staff_table_sql_where', $row, $aRow);

    $output['aaData'][] = $row;

    $row = [];
    $row[] = "4";
    $row[] = "1541";
    $row[] = "Own";
    $row[] = "140";
    $row[] = "16-09-2019";
    $row[] = $status;
    $row[] = '<a href="' . admin_url('vehicle/vehicle_manage') . '" >' ."<i class='fa fa-pencil-square' style='font-size: 25px;'></i>". '</a>  &nbsp;&nbsp;&nbsp;<a href="' . admin_url('vehicle/vehicle_manage'). '" >' ."<i class='fa fa-trash' style='font-size: 25px;color: red;'></i>". '</a>';
    
    
    $row = hooks()->apply_filters('staff_table_sql_where', $row, $aRow);

    $output['aaData'][] = $row;
//}
