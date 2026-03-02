<?php

defined('BASEPATH') or exit('No direct script access allowed');
$selected_company = $this->ci->session->userdata('root_company');
$aColumns = ['name'];

$sIndexColumn = 'id';
$sTable       = db_prefix().'customers_groups';
$where        = [];
array_push($where, 'AND ' . db_prefix() . 'customers_groups.PlantID ='. $selected_company);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0 ; $i < count($aColumns) ; $i++) {
        $_data = '<a href="#" data-toggle="modal" data-target="#customer_group_modal" data-id="' . $aRow['id'] . '">' . $aRow[$aColumns[$i]] . '</a>';
        if (has_permission_new('distributor_type', '', 'edit')) {
            $row[] = $_data;
        }else{
            $row[] = $aRow[$aColumns[$i]];
        }
    }
    // $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#customer_group_modal', 'data-id' => $aRow['id']]);
    // $opt_edit_delete   = $options . icon_btn('clients/delete_group/' . $aRow['id'], 'remove', 'btn-danger _delete');
    // $only_delete = icon_btn('clients/delete_group/' . $aRow['id'], 'remove', 'btn-danger _delete');
    

$options = '<a href="#" class="btn btn-default btn-icon"
    data-toggle="modal"
    data-target="#customer_group_modal"
    data-id="'.$aRow['id'].'">
    <i class="fa fa-pen-to-square"></i>
</a>';

$only_delete = '<a href="'.admin_url('clients/delete_group/'.$aRow['id']).'"
    class="btn btn-danger _delete btn-icon">
    <i class="fa fa-trash-can" style="color:white;"></i>
</a>';

$opt_edit_delete = $options . $only_delete;




    if (has_permission_new('distributor_type', '', 'edit') && !has_permission_new('distributor_type', '', 'delete')) {
        $row[]   = $options;
    }
    if (has_permission_new('distributor_type', '', 'edit') && has_permission_new('distributor_type', '', 'delete')) {
        $row[]   = $opt_edit_delete;
    }
    if (has_permission_new('distributor_type', '', 'delete')) {
        $row[]   = $only_delete;
    }
    if (!has_permission_new('distributor_type', '', 'delete') && !has_permission_new('distributor_type', '', 'edit')) {
        $row[]   = "";
    }
    $output['aaData'][] = $row;
}
