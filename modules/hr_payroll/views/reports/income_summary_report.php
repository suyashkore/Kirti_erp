<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
tr {
width: 100%;
display: inline-table;
table-layout: fixed;
}

table{
 height:400px;              // <-- Select the height of the table
 display: block;
}
tbody{
  overflow-y: scroll;      
  height: 300px;            //  <-- Select the height of the body
  width: 100%;
  position: absolute;
  /*text-align: center;*/
}
</style>
<div id="income_summary_report" class="hide reports">
	<table class="table table-income_summary_report scroll-responsive">
		<thead>
			<tr>
				<th><?php echo _l('ps_pay_slip_number'); ?></th>
				<th><?php echo _l('employee_name'); ?></th>
				<th><?php echo _l('department_name'); ?></th>
				
				<th><?php echo _l('month_1'); ?></th>
				<th><?php echo _l('month_2'); ?></th>
				<th><?php echo _l('month_3'); ?></th>
				<th><?php echo _l('month_4'); ?></th>
				<th><?php echo _l('month_05'); ?></th>
				<th><?php echo _l('month_6'); ?></th>
				<th><?php echo _l('month_7'); ?></th>
				<th><?php echo _l('month_8'); ?></th>
				<th><?php echo _l('month_9'); ?></th>
				<th><?php echo _l('month_10'); ?></th>
				<th><?php echo _l('month_11'); ?></th>
				<th><?php echo _l('month_12'); ?></th>
				<th><?php echo _l('average_income'); ?></th>

			</tr>
		</thead>
		<tbody></tbody>
		<tfoot>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			 
		</tfoot>
		
	</table>
</div>
