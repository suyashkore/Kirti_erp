<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('purchase', '', 'delete');
$selected_company = $this->ci->session->userdata('root_company');
$custom_fields = get_table_custom_fields('vendors');
$this->ci->db->query("SET sql_mode = ''");


$aColumns = [
    '1',
    db_prefix().'clients.AccountID as AccountID',
    'company','itemdivision','state','address','StationName','city',db_prefix().'clients.active as actstatus',
    '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM '.db_prefix().'customers_groups WHERE '.db_prefix().'customers_groups.id = '.db_prefix().'clients.DistributorType) as customerGroups'
];

$sIndexColumn = 'company';
$sTable       = db_prefix().'clients';
$where        = [];
// Add blank where all filter can be stored
$filter = [];

$join = [
    'INNER JOIN '.db_prefix().'contacts ON '.db_prefix().'clients.PlantID='.db_prefix().'contacts.PlantID AND '.db_prefix().'clients.AccountID='.db_prefix().'contacts.AccountID',
    ];


array_push($where, 'AND ' . db_prefix() . 'clients.PlantID ='. $selected_company);
array_push($where, 'AND ' . db_prefix() . 'clients.SubActGroupID =50003002');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix().'contacts.id as contact_id',
    'lastname',
    db_prefix().'clients.zip as zip',
    'registration_confirmed',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Bulk actions
    // $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['AccountID'] . '"><label></label></div>';
    // User id
    $row[] = $aRow['AccountID'];

    // Company
    $companyy  = $aRow['company'];
    $isPerson = false;

    if ($companyy == '') {
        $companyy  = _l('no_company_view_profile');
        $isPerson = true;
    }

    $url = admin_url('purchase/vendor/' . $aRow['AccountID']);

    if ($isPerson && $aRow['contact_id']) {
        $url .= '?contactid=' . $aRow['contact_id'];
    }

    $companyy = '<a href="' . $url . '">' . $companyy . '</a>';

    $company .= '<div class="row-options">';
    $company .= '<a href="' . $url . '">' . _l('view') . '</a>';

    if ($aRow['registration_confirmed'] == 0 && is_admin()) {
        $company .= ' | <a href="' . admin_url('purchase/confirm_registration/' . $aRow['AccountID']) . '" class="text-success bold">' . _l('confirm_registration') . '</a>';
    }
    if (!$isPerson) {
        $company .= ' | <a href="' . admin_url('purchase/vendor/' . $aRow['AccountID'] . '?group=contacts') . '">' . _l('customer_contacts') . '</a>';
    }
    if ($hasPermissionDelete) {
        $company .= ' | <a href="' . admin_url('purchase/delete_vendor/' . $aRow['AccountID']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $company .= '</div>';

    $row[] = $companyy;
 $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
    <input type="checkbox"' . ($aRow['registration_confirmed'] == 0 ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'clients/change_client_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['userid'] . '" data-id="' . $aRow['userid'] . '" ' . ($aRow[db_prefix().'clients.active'] == 1 ? 'checked' : '') . '>
    <label class="onoffswitch-label" for="' . $aRow['userid'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow[db_prefix().'clients.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    //$row[] = $toggleActive;
    // Primary contact
    $row[] =$aRow['StationName'];
    // $row[] = ($aRow['contact_id'] ? '<a href="' . admin_url('pur_vendor/client/' . $aRow['userid'] . '?contactid=' . $aRow['contact_id']) . '" target="_blank">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>' : '');
    $city_name = get_city_name($aRow['city']);
        if($city_name->city_name){
            $city = $city_name->city_name;
        }else{
            $city = $aRow['city'];
        }
        $row[] = $city;
    // Primary contact email
    // $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');
$state_name = $aRow['state'];
    $row[] = $state_name;
    //$row[] = $aRow['state'];
    $address = explode(' ', $aRow['address']);
    $i = 1;
    $string = "";
    foreach($address as $aa){
        if($i%2 == 0){
            $string = $string." ".$aa."\n";
        }else {
            $string = $string." ".$aa;
        }
        
        $i++;
    }
    $row[] = nl2br($aRow['address']);
    
    if($aRow['actstatus'] == 1){
        $status = "Active";
    }else{
        $status = "DeActive";
    }
    $row[] = $status;
    //$row[] = _dt($aRow['datecreated']);

    
    $row['DT_RowClass'] = 'has-row-options';

    if ($aRow['registration_confirmed'] == 0) {
        $row['DT_RowClass'] .= ' alert-info requires-confirmation';
        $row['Data_Title']  = _l('customer_requires_registration_confirmation');
        $row['Data_Toggle'] = 'tooltip';
    }

    $row = hooks()->apply_filters('customers_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
