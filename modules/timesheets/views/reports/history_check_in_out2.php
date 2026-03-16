<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
/*tr {*/
/*width: 100%;*/
/*display: inline-table;*/
/*table-layout: fixed;*/
/*}*/

/*table{*/
/* height:450px;              */
/* display: block;*/
/*  width=100%;*/
/*}*/
/*tbody{*/
/*  overflow-y: scroll;      */
/*  height: 400px;            */
  /*width: 100%;*/
/*  position: absolute;*/
/*}*/

/*td,*/
/*th {*/
/*  min-width: 100px;*/
  /*height: 50px;*/
  /*border: dashed 1px lightblue;*/
/*  overflow: hidden;*/
/*  text-overflow: ellipsis;*/
/*  max-width: 100px;*/
/*}*/
</style>
<div id="history_check_in_out2" class="hide reports_fr2 history_check_in_out2">
<div class="r1t1" id="r1t1">
    
<?php
    
    $table_data = array(
							array(
                                'name'=>'Staff Name',
                                'th_attrs'=>array('class'=>'staff_name', 'id'=>'th-staff_name')
                                ),
                                
                                array(
                                    'name'=>"Date",
                                    'th_attrs'=>array('class'=>'staff_code', 'id'=>'th-staff_code')
                                ),
                                array(
                                    'name'=>'In',
                                    'th_attrs'=>array('class'=>'mobile', 'id'=>'th-mobile')
                                ),
                                array(
                                    'name'=>'loc. In',
                                    'th_attrs'=>array('class'=>'State', 'id'=>'th-State')
                                ),
                                array(
                                    'name'=>'loc. Out',
                                    'th_attrs'=>array('class'=>'report_to', 'id'=>'th-report_to')
                                ),
                                array(
                                    'name'=>'Km Trav.',
                                    'th_attrs'=>array('class'=>'departments', 'id'=>'th-departments')
                                ),
                                array(
                                    'name'=>'Area Vis.',
                                    'th_attrs'=>array('class'=>'designation', 'id'=>'th-designation')
                                ),
								
								array(
                                    'name'=>'Vis.Loc.',
                                    'th_attrs'=>array('class'=>'hr_active', 'id'=>'th-hr_active')
                                    ),
                                                          
								);
								
								render_datatable($table_data,'history_check_in_out_report2',
									array('customizable-table'),
									array(
										'id'=>'table-history_check_in_out_report2',
										'data-last-order-identifier'=>'history_check_in_out_report2',
										'data-default-order'=>get_table_last_order('history_check_in_out_report2'),
									)); ?>    

    <!--<table class="table table-history_check_in_out_report2 scroll-responsive">
    <thead>
      <tr>
        
         <th>Staff Name</th>
         <th>Date</th>
         <th>In</th>
         <th>Loc.In</th>
         <th>Out</th>
         <th>Loc.Out</th>
         <th>Km Trav.</th>
         <th>Area Vis.</th>
         <th>Vis.Loc.</th>
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
   </tfoot>
</table>-->
</div>
<div class="r1t2" id="r1t2">
    
    <table class="table table-history_check_in_out_report3 scroll-responsive">
    <thead>
      <tr>
         
         <!--<th style="width:9%;">Date</th>-->
         <!--<th style="width:5%;">Day On</th>-->
         <!--<th style="width:5%;">Loc. In</th>-->
         <!--<th style="width:15%;">Loc. In Name</th>-->
         <!--<th style="width:5%;">Day Off</th>-->
         <!--<th style="width:5%;">Loc. Out</th>-->
         <!--<th style="width:15%;">Loc. Out Name</th>-->
         <!--<th style="width:5%;">Km Trav.</th>-->
         <!--<th style="width:20%;">Area Vis.</th>-->
         <!--<th style="width:5%;">Vis. Loc.</th>-->
         
         <th>Date</th>
         <th>Day On</th>
         <!--<th style="width:5%;">Loc. In</th>-->
         <th>Loc. In Name</th>
         <th>Day Off</th>
         <!--<th style="width:5%;">Loc. Out</th>-->
         <th>Loc. Out Name</th>
         <th>Km Trav.</th>
         <th>Area Vis.</th>
         <th>Vis. Loc.</th>
      </tr>
   </thead>
   <tbody></tbody>
   <tfoot>
      
      <!--<td style="width:9%;"></td>-->
      <!--<td style="width:5%;"></td>-->
      <!--<td style="width:5%;"></td>-->
      <!--<td style="width:15%;"></td>-->
      <!--<td style="width:5%;"></td>-->
      <!--<td style="width:5%;"></td>-->
      <!--<td style="width:15%;"></td>-->
      <!--<td style="width:5%;"></td>-->
      <!--<td style="width:20%;"></td>-->
      <!--<td style="width:5%;"></td>-->
      
      <td></td>
      <td></td>
      <!--<td style="width:5%;"></td>-->
      <td></td>
      <td></td>
      <!--<td style="width:5%;"></td>-->
      <td></td>
      <td></td>
      <td></td>
      <td></td>
   </tfoot>
</table>
</div>
</div>
