<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>

.table-insurance_cost_summary_report tbody{
  display: block;
  max-height: 350px;
  overflow-y: scroll;
}
.table-insurance_cost_summary_report thead, .table-insurance_cost_summary_report tbody tr{
  display: table;
  table-layout: fixed;
  width: 100%;
}
.table-insurance_cost_summary_report thead{
  width: calc(100% - 1.1em);
}
.table-insurance_cost_summary_report thead{
  position: relative;
}
.table-insurance_cost_summary_report thead th:last-child:after{
  content: ' ';
  position: absolute;
  background-color: #337ab7;
  width: 1.3em;
  height: 38px;
  right: -1.3em;
  top: 0;
  border-bottom: 2px solid #ddd;
}
</style>
<div id="insurance_cost_summary_report" class="hide reports">
	<table class="table table-insurance_cost_summary_report scroll-responsive">
		<thead>
			<tr>
				
				<th><?php echo _l('department_name'); ?></th>
				
				<th><?php echo _l('ps_total_insurance'); ?></th>

			</tr>
		</thead>
		<tbody></tbody>
		<tfoot>
		
			<td></td>
			<td></td>

		</tfoot>
		
	</table>
</div>
