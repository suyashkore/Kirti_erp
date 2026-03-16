<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * get status modules wh
 * @param  string $module_name 
 * @return boolean             
 */
function acc_get_status_modules($module_name){
	$CI             = &get_instance();

	$sql = 'select * from '.db_prefix().'modules where module_name = "'.$module_name.'" AND active =1 ';
	$module = $CI->db->query($sql)->row();
	if($module){
		return true;
	}else{
		return false;
	}
}

function get_account_name_for_voucher($acc_id = '',$selected_company)
{
    
    $CI = &get_instance();

    $CI->db->select('company');
    $CI->db->where('AccountID', $acc_id);
    $CI->db->where('PlantID', $selected_company);
    $Acc_name = $CI->db->get(db_prefix() . 'clients')->row();

    return $Acc_name;
    
}
function get_staff_name_for_voucher($acc_id = '',$selected_company)
{
    
    $CI = &get_instance();

    $CI->db->select('firstname,lastname');
    $CI->db->where('AccountID', $acc_id);
    //$CI->db->where('PlantID', $selected_company);
    $Acc_name = $CI->db->get(db_prefix() . 'staff')->row();

    return $Acc_name;
    
}
function get_for_voucher($voucherID='', $amt='',$voucher_type='')
{
    $CI = &get_instance();
    $selected_company = $CI->session->userdata('root_company');
    $fy = $CI->session->userdata('finacial_year');
    $sql = 'SELECT tblaccountledger.*,tblclients.company,tblclients.address  FROM `tblaccountledger` 
        INNER JOIN tblclients ON tblclients.AccountID=tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
        WHERE tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$fy.'" AND tblaccountledger.VoucherID ="'.$voucherID.'" AND tblaccountledger.PassedFrom = "'.$voucher_type.'" AND tblaccountledger.TType = "D" AND tblaccountledger.Amount = "'.$amt.'"';
        //$sql .= ' ORDER BY tblaccountledger.VoucherID,tblaccountledger.AccountID ASC';
    $result = $CI->db->query($sql)->row();
    return $result;
    /*$CI->db->select('*');
    $CI->db->where('VoucherID', $vaid);
    $CI->db->where('Amount', $amt);
    $CI->db->where('TType', 'D');
    $Acc = $CI->db->get(db_prefix() . 'accountledger')->row();
  // print_r($Acc); exit();
    return $Acc;*/
}

/**
 * check account exists
 * @param  string $key_name 
 * @return boolean or integer           
 */
function acc_account_exists($key_name){
	$CI             = &get_instance();

	$CI->load->model('accounting/accounting_model');

	if(get_option('acc_add_default_account') == 0){
        $CI->accounting_model->add_default_account();
    }

    if(get_option('acc_add_default_account_new') == 0){
        $CI->accounting_model->add_default_account_new();
    }

	$sql = 'select * from '.db_prefix().'acc_accounts where key_name = "'.$key_name.'"';
	$account = $CI->db->query($sql)->row();

	if($account){
		return $account->id;
	}else{
		return false;
	}
}