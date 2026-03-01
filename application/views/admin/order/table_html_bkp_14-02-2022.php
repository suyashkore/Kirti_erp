<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
  "Order No.",
  "Order Date",
  "Invoice",
  "Invoice Date",
  "Challan",
  "BillAmt",
  "Party Name",/*
  "CS/CR",*/
  /*array(
    'name'=>_l('invoice_estimate_year'),
    'th_attrs'=>array('class'=>'not_visible')
  ),*/
  "Amount",
  /*array(
    'name'=>_l('invoice_dt_table_heading_client'),
    'th_attrs'=>array('class'=>(isset($client) ? 'not_visible' : ''))
  ),*/
  /*_l('project'),
  _l('tags'),
  _l('invoice_dt_table_heading_duedate'),*/
  "Order Type");
/*$custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));
foreach($custom_fields as $field){
  array_push($table_data,$field['name']);
}*/
$table_data = hooks()->apply_filters('order_table_columns', $table_data);
render_datatable($table_data, (isset($class) ? $class : 'order'));
?>
