<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Pending Orders</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="_buttons">
							<div class="row">  
								<div class="col-md-2">
									<?php 
										$fy = $this->session->userdata('finacial_year');
										$fy_new  = $fy + 1;
										$lastdate_date = '20'.$fy_new.'-03-31';
										$firstdate_date = '20'.$fy_new.'-04-01';
										$curr_date = date('Y-m-d');
										$curr_date_new    = new DateTime($curr_date);
										$last_date_yr = new DateTime($lastdate_date);
										if($last_date_yr < $curr_date_new){
											$to_date = '31/03/20'.$fy_new;
											$from_date = '01/03/20'.$fy_new;
											}else{
											$from_date = "01/".date('m')."/".date('Y');
											$to_date = date('d/m/Y');
										}
										echo render_date_input('from_date','From Date',$from_date);        
									?>
								</div>
								<div class="col-md-2">
									<?php          
										echo render_date_input('date','To Date',$to_date);            
									?>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="order_type">Order Type</label>
										<select class=" form-control" id="order_type" name="order_type">
											<option value="O">Pending</option>
											<option value="C">Cancel</option>
											<option value="all">All</option>
										</select>
									</div>
									
								</div>
								<div class="col-md-3">
									<?php 
										
										echo render_select( 'states',$states,array( 'short_name','state_name'), 'client_state','',array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
										
									?>
								</div>
								<div class="col-md-2">
									<?php echo render_select('dist_type',$dist_type,array('id','name'),'Dist Type'); ?>
									
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="sort_by">Sort By</label>
										<select class=" form-control" id="sort_by" name="sort_by">
											<option value="OrderID">OrderID Wise</option>
											<option value="Dispatch">Dispatch Date</option>
											<option value="Punch">Order Punch Date</option>
										</select>
									</div>
									
								</div>
								<span id="searchh4" style="display:none;">please wait data saving.....</span>
							</div>
							
						</div>
						<div class="clearfix"></div>
						<div class="row">
							<div class="col-md-8">
								<div class="custom_button">
									<button class="btn btn-info search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
									<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print Order</a>
									
									<a id="dlink" style="display:none;"></a>
									<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="production_report" href="#" id="caexcel" style="font-size:12px;"><span>Export Order</span></a>
									
									<?php
										if (has_permission_new('OrderCancel', '', 'edit')) {
										?>
										<a class="btn btn-default update" name="update" id="update">Save</a>
										<a class="btn btn-default reset" name="reset" id="reset">Reset Remark</a>
									<?php } ?>
								</div>
								<span id="searchh3" style="display:none;">please wait exporting data.....</span>
							</div>
							<div class="col-md-4">
								<input type="text" id="myInput1" onkeyup="myFunction1()" placeholder="Search.." class="form-control" style="float: right;">
							</div>
						</div>
						<div class="fixed_header">
							
							<table class="table table-striped table-bordered only_print" id="export_table_to_excel_1" width="100%">
								<thead>
									<tr>
										<th colspan="15"><?php echo $selected_company_details->FIRMNAME; ?></th>
									</tr>
									<tr>
										<th colspan="15"><?php echo $selected_company_details->ADDRESS1.", ".$selected_company_details->ADDRESS2; ?></th>
									</tr>
									<!--<tr>
										<th colspan="15">Pending/Cancelled Orders as on <span id="date_filter_val"></span> <?php echo date('H:i:s')?></th>
									</tr>-->
									<tr>
										<th>SrNo.</th>
										<th style="width:10%;">OrderId</th>
										<th style="width:13%;">Transdate</th>
										<th style="width:13%;">Expected date</th>
										<!--<th>AccountID</th>-->
										<th style="width:8%;">SO Name</th>
										<th style="width:17%;">Bill To Party</th>
										<th style="width:17%;">Ship To Party</th>
										<th style="width:10%;">Station</th>
										<th style="width:10%;">Dist Type</th>
										<th style="width:4%;">State</th>
										<th style="width:8%;">OpenBal Amt</th>
										<th style="width:9%;">Netorder Amt</th>
										<th style="width:5%;">Status</th>
										<th style="width:5%;">UserID</th>
										<th style="width:10%;">remark</th>
									</tr>
								</thead>
								<tbody >
								</tbody>
							</table> 
							
							<!--<table class="table table-striped table-bordered fixed_header" id="pending_data" width="100%">-->
							<table class="table table-striped fixed_header" id="pending_data" width="100%">
								<thead>
									<tr class="only_print">
										<th colspan="16"><?php echo $selected_company_details->FIRMNAME; ?></th>
									</tr>
									<tr class="only_print">
										<th colspan="16"><?php echo $selected_company_details->ADDRESS1.", ".$selected_company_details->ADDRESS2; ?></th>
									</tr>
									<!--<tr class="only_print">
										<th colspan="16">Pending/Cancelled Orders as on <span id="date_filter_val"></span> <?php echo date('H:i:s')?></th>
									</tr>-->
									<tr>
										<th class="sortablePop" style="width:3%;text-align:center;" >SrNo</th>
										<th style="width:3%;text-align:center;" class="dontprint">Tag</th>
										<th class="sortablePop" style="width:10%;text-align:center;">OrderID</th>
										<th class="sortablePop" style="width:12%;text-align:center;">Order Date</th>
										<th class="sortablePop" style="width:13%;">Expected Date</th>
										<th class="sortablePop" style="width:8%;">SO Name</th>
										<th class="sortablePop" style="width:18%;">Bill To Party</th>
										<th class="sortablePop" style="width:18%;">Ship To Party</th>
										<th class="sortablePop" style="width:11%;">Station</th>
										<th class="sortablePop" style="width:8%;">Dist Type</th>
										<th class="sortablePop" style="width:4%;">State</th>
										<th class="sortablePop" style="width:8%;">Cls. Bal Amt</th>
										<th class="sortablePop" style="width:10%;text-align:center;">Order Amt</th>
										<th class="sortablePop" style="width:5%;">Cancel ?</th>
										<th class="sortablePop" style="width:4%;">UserID</th>
										<th style="width:15%;">Remark (if any)</th>
									</tr>
								</thead>
								<tbody >
								</tbody>
							</table>  
							<span id="searchh" style="display:none;">
                                Loading.....
							</span>
						</div>
						
						<div class="row">
						    <div class="col-md-6">
								<div class="custom_button">
									<a class="btn btn-default" href="javascript:void(0);" onclick="printItems();">Print Items</a>
									<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="production_report" href="#" id="caexcelItem"><span>Export Items</span></a>
								</div>
							</div>
							<div class="col-md-4">
								<input type="text" id="myInput2" onkeyup="myFunction2()" placeholder="Search.." class="form-control" style="float: right;">
							</div>
							<div class="col-md-2">
							</div>
							<div class="col-md-10">
								<div class="fixed_header1">
									<table class="table table-striped fixed_header1" id="export_table_to_excel_2" width="100%">
										<thead>
											<tr  class="only_print">
												<th colspan="15"><?php echo $selected_company_details->FIRMNAME; ?></th>
											</tr>
											<tr  class="only_print">
												<th colspan="15"><?php echo $selected_company_details->ADDRESS1.", ".$selected_company_details->ADDRESS2; ?></th>
											</tr>
											<tr>
												<th class="sortablePop2" style="width:8%;">NickName</th>
												<th class="sortablePop2" style="width:21%;">ItemName</th>
												<th class="sortablePop2" style="width:5%;text-align:center;">Pack</th>
												<th class="sortablePop2" style="width:6%;text-align:center;">Order Qty (CS/CR)</th>
												<th class="sortablePop2" style="width:25%;text-align:center;">Order Qty (In Units)</th>
												<th class="sortablePop2" style="width:25%;text-align:center;">Bowl Qty</th>
												<th class="sortablePop2" style="width:10%;text-align:center;">CurrStock</th>
												<th style="width:25%;text-align:center;">Remark</th>
												
											</tr>
										</thead>
										<tbody >
										</tbody>
									</table>   
									<span id="searchh2" style="display:none;">
										Loading.....
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php init_tail(); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/onlyprint.css" media="print" type="text/css">
<style>
	.fixed_header { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
	.fixed_header thead th { position: sticky; top: 0; z-index: 1; }
	.fixed_header tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.fixed_header table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	.fixed_header th     { background: #50607b;color: #fff !important; }
	
	.fixed_header1 { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
	.fixed_header1 thead th { position: sticky; top: 0; z-index: 1; }
	.fixed_header1 tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.fixed_header1 table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	.fixed_header1 th     { background: #50607b;color: #fff !important; }
</style>
<style>
	@media screen {
	.only_print{
	display:none !important;
	}
    }
</style>

<!--new update -->

<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		
		function load_data(from_date,dates,order_type,state,dist_type)
		{
			var sort_by = $("#sort_by").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>order/load_data",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date,dates:dates, order_type:order_type, state:state, dist_type:dist_type,sort_by:sort_by},
				beforeSend: function () {
					
					$('#searchh').css('display','block');
					$('#pending_data tbody').css('display','none');
					
				},
				complete: function () {
					
					$('#pending_data tbody').css('display','');
					$('#searchh').css('display','none');
				},
				success:function(data){
					var html = '';
					var html1 = '';
					var sr = 1;
					var net_total = 0.00;
					var open_total = 0.00;
					
					if(data.length){
						for(var count = 0; count < data.length; count++)
						{
							var bal_new = parseFloat(data[count].bal1) + parseFloat(data[count].balance);
							var bal = parseFloat(data[count].bal1) + parseFloat(data[count].bal2) + parseFloat(data[count].bal3) + parseFloat(data[count].bal4) + parseFloat(data[count].bal5) + parseFloat(data[count].bal6) + parseFloat(data[count].bal7) + parseFloat(data[count].bal8) + parseFloat(data[count].bal9) + parseFloat(data[count].bal10) + parseFloat(data[count].bal11) + parseFloat(data[count].bal12) + parseFloat(data[count].bal13);
							html += '<tr >';
							var url = admin_url + 'order/order/' + data[count].OrderID;
							var url_new = admin_url + 'order/order/' + data[count].OrderID;
							var SrNo = parseInt(count) + 1;
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="srno" style="width:3%;text-align:center;" >'+SrNo +'</td>';
							html += '<td class="table_data dontprint" data-row_id="'+data[count].OrderID+'" data-column_name="tag" style="width:3%;" ><input type="checkbox" class="selected_ord_id" name="selected_ord_id" onclick="select_ord(this.value)" value="'+data[count].OrderID+'"></td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderid" style="width:10%;text-align:center;"><a href="'+url_new+'" target="_blank" >'+data[count].OrderID+'</a></td>';
							
							var date = data[count].Transdate.substring(0, 10);
							var yourdate = date.split("-").reverse().join("/");
							
							if(data[count].Dispatchdate !== null && data[count].Dispatchdate !== ''){
								var Dispatchdate = data[count].Dispatchdate.substring(0, 10);
								var Dispatchtime = data[count].Dispatchdate.substring(10, 19);
								
								Dispatchdate = Dispatchdate.split("-").reverse().join("/");
								Dispatchdate = Dispatchdate+Dispatchtime;
								}else{
								Dispatchdate = '';
							}
							
							if(data[count].SOID == null){
								var SOName = '';
								}else{
								var strp = data[count].SOID.search("/");
								if(strp > 1){
									var SOName = data[count].SOID.substr(0, strp);
									}else{
									var SOName = data[count].SOID;
								}
							}
							
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:12%;text-align:center;">'+yourdate+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:12%;text-align:center;">'+Dispatchdate+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:18%;">'+SOName+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:18%;">'+data[count].AccountName+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:18%;">'+data[count].ShipToAccountName+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:11%;">'+data[count].StationName+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:8%;">'+data[count].dist_Type+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:4%;text-align:left;">'+data[count].StateName+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="width:8%;text-align:right;">'+bal_new.toFixed(2)+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderamt" style="width:10%;text-align:right;">'+data[count].OrderAmt+'</td>';
							
							if(data[count].OrderStatus == "C"){
							    var cc = "checked";
							    var c = "Yes"
							    var status = "Cancel";
							    var StaffName = data[count].CancelStaffName;
								}else {
							    var cc = "";
							    var c = "";
							    var status = "Open";
							    var StaffName = data[count].CreateStaffName;
							}
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="cancel" style="width:5%;text-align:center;"><input type="checkbox" class="cancel_ord_id" name="cancel_ord_id" value="'+data[count].OrderID+'" '+cc+'></td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="cancel" style="width:5%;text-align:center;">'+StaffName+'</td>';
							if(data[count].remark == null){
								var remark = ' ';
								}else{
								var remark = data[count].remark;
							}
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" style="width:15%;text-align:center;"><input type="text" class="cancel_ord_id_re" id="'+data[count].OrderID+'" name="cancel_ord_id_re" value="'+remark+'" style="width:100%;height:25px;font-size:12px;"></td>';
							
							
							html1 += '<tr >';
							
							var url = admin_url + 'order/order/' + data[count].OrderID;
							var url_new = admin_url + 'order/order/' + data[count].OrderID;
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="srno" style="width:3%;text-align:center;" >'+SrNo +'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderid" style="width:10%;text-align:center;font-size:14px;"><a href="'+url_new+'" target="_blank" >'+data[count].OrderID+'</a></td>';
							
							var date = data[count].Transdate.substring(0, 10);
							var yourdate = date.split("-").reverse().join("/");
							
							if(data[count].Dispatchdate !== null && data[count].Dispatchdate !== ''){
								var Dispatchdate = data[count].Dispatchdate.substring(0, 10);
								var Dispatchtime = data[count].Dispatchdate.substring(10, 19);
								
								Dispatchdate = Dispatchdate.split("-").reverse().join("/");
								Dispatchdate = Dispatchdate+Dispatchtime;
								}else{
								Dispatchdate = '';
							}
							var balsum = parseFloat(data[count].bal1) + parseFloat(data[count].bal2) + parseFloat(data[count].bal3) + parseFloat(data[count].bal4) + parseFloat(data[count].bal5) + parseFloat(data[count].bal6) + parseFloat(data[count].bal7) + parseFloat(data[count].bal8) + parseFloat(data[count].bal9) + parseFloat(data[count].bal10) + parseFloat(data[count].bal11) + parseFloat(data[count].bal12) + parseFloat(data[count].bal13);
							
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:12%;text-align:center;font-size:14px;">'+yourdate+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:12%;text-align:center;font-size:14px;">'+Dispatchdate+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:10%;text-align:center;font-size:14px;">'+data[count].SOID+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:18%;font-size:14px;">'+data[count].AccountName+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:18%;font-size:14px;">'+data[count].ShipToAccountName+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:11%;text-align:left;font-size:14px;">'+data[count].StationName+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:8%;text-align:left;font-size:14px;">'+data[count].dist_Type+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:4%;text-align:left;font-size:14px;">'+data[count].StateName+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="width:8%;text-align:right;font-size:14px;">'+bal_new.toFixed(2)+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderamt" style="width:10%;text-align:right;font-size:14px;">'+data[count].OrderAmt+'</td>';
							net_total += parseFloat(data[count].OrderAmt);
							open_total += parseFloat(bal_new);
							
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" contenteditable style="width:5%;text-align:center;font-size:14px;">'+status+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" contenteditable style="width:5%;text-align:center;font-size:14px;">'+StaffName+'</td>';
							html1 += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" contenteditable style="width:10%;text-align:center;font-size:14px;">'+data[count].remark+'</td>';
							sr++;
						}
						
						
						html1 += '</tr>';  
						
						html1 += '<tr>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td style="text-align:center;">Total</td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td style="text-align:right;">'+ open_total.toFixed(2) +'</td>';
						html1 += '<td style="text-align:right;">'+ net_total.toFixed(2) +'</td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '<td></td>';
						html1 += '</tr>';
						}else {
						html += '<tr>';
						html += '<td colspan="12"> No Data Found...</td>';
						html += '</tr>';
						html1 += '<tr>';
						html1 += '<td colspan="11"> No Data Found...</td>';
						html1 += '</tr>';
					}
					
					$('#pending_data tbody').html(html);
					$('#export_table_to_excel_1 tbody').html(html1);
				}
			});
			
			$.ajax({
				url:"<?php echo admin_url(); ?>order/load_data_items",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date,dates:dates, order_type:order_type, state:state, dist_type:dist_type},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('#export_table_to_excel_2 tbody').css('display','none');
					
				},
				complete: function () {
					
					$('#export_table_to_excel_2 tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					var html = '';
					var total_cases = 0;
					var total_unitqty = 0;
					var total_bowlqty = 0;
					var total_taxableamt = 0.00;
					var total_netamt = 0.00;
					if(data.length){
						for(var count = 0; count < data.length; count++)
						{
							/*var stock = parseFloat(data[count].OQty) + parseFloat(data[count].PQty) - parseFloat(data[count].PRQty) - parseFloat(data[count].IQty) + parseFloat(data[count].PRDQty) + parseFloat(data[count].gtiqty) - parseFloat(data[count].gtoqty) - parseFloat(data[count].SQty) + parseFloat(data[count].SRQty) - parseFloat(data[count].DQTY) - parseFloat(data[count].ADJQTY); 
								var stockInCase = stock / data[count].CaseQty
							stockInCase = parseInt(stockInCase);*/
							html += '<tr >';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:8%;">'+data[count].Item_code+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:21%;">'+data[count].description+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:5%;text-align:right;">'+data[count].CaseQty+'</td>';
							var ordqty = data[count].OrderQty / data[count].CaseQty;
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:6%;text-align:right;">'+ordqty.toFixed(1)+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="prdplan" style="width:25%;text-align:right;">'+data[count].OrderQty+'</td>';
							if(data[count].BowlQty != 'NA'){
								var bowlqty = parseFloat(data[count].OrderQty / data[count].BowlQty).toFixed(2);
								total_bowlqty += parseFloat(bowlqty);
								}else{
								var bowlqty = 'NA';
							}
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="bowlqty" style="width:25%;text-align:right;">'+bowlqty+'</td>';
							//   html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="width:8%;text-align:center;">'+data[count].OrderAmt+'</td>';
							//   html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:5%;text-align:center;">'+data[count].taxName+'</td>';
							//   html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="width:8%;text-align:center;">'+data[count].NetOrderAmt+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderamt" style="width:8.5%;text-align:right;">'+parseFloat(data[count].StockBal).toFixed(2)+'</td>';
							
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" style="width:25%;text-align:right;"></td>';
							
							total_taxableamt += parseFloat(data[count].OrderAmt);
							total_netamt += parseFloat(data[count].NetOrderAmt);
							total_cases += ordqty;
							total_unitqty += parseFloat(data[count].OrderQty);
						}
						var total_tax = total_netamt - total_taxableamt;
						// console.log(total_bowlqty);
						html += '<tr >';
						html += '<td></td>';
						html += '<td style="text-align:center;">Total</td>';
						html += '<td></td>';
						html += '<td style="text-align:right;">'+ total_cases.toFixed(1) +'</td>';
						// html += '<td style="text-align:right;">'+ total_taxableamt.toFixed(2) +'</td>';
						// html += '<td style="text-align:right;">'+ total_tax.toFixed(2) +'</td>';
						// html += '<td style="text-align:right;">'+ total_netamt.toFixed(2) +'</td>';
						html += '<td style="text-align:right;">'+ parseFloat(total_unitqty).toFixed(2) +'</td>';
						html += '<td style="text-align:right;">'+ parseFloat(total_bowlqty).toFixed(2) +'</td>';
						html += '<td></td>';
						html += '<td></td>';
						html += '</tr >';
						}else {
						html += '<tr>';
						html += '<td colspan="11"> No Data Found...</td>';
						html += '</tr>';
					}
					
					$('#export_table_to_excel_2 tbody').html(html);
				}
			});
		}
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var dates = $("#date").val();
			// alert(dates);
			var order_type = $("#order_type").val();
			var state = $("#states").val();
			var dist_type = $("#dist_type").val();
			var yourdate1 = dates.split("-").reverse().join("-");
			$("#date_filter_val").html(yourdate1);
			$("#date_filter_val1").html(yourdate1);
			load_data(from_date,dates,order_type,state,dist_type);
			
		});
		$("#caexcel").click(function(){
			var from_date = $("#from_date").val();
			var dates = $("#date").val();
			// alert(dates);
			var order_type = $("#order_type").val();
			var state = $("#states").val();
			var dist_type = $("#dist_type").val();
			var selected = [];
			$('input[type=checkbox]:checked').each(function () {
                
                var value = $(this).attr("value");
                selected.push(value);
			});
			let selected_ids = selected.toString();
			
			$.ajax({
				url:"<?php echo admin_url(); ?>order/export_pending_order",
				method:"POST",
				data:{from_date:from_date,dates:dates, order_type:order_type, state:state, dist_type:dist_type,selected_ids:selected_ids},
				beforeSend: function () {
					
					$('#searchh3').css('display','block');   
				},
				complete: function () {
					$('#searchh3').css('display','none');
				},
				success:function(data){
					response = JSON.parse(data);
					window.location.href = response.site_url+response.filename;
				}
			});
		});
		$("#caexcelItem").click(function(){
			var from_date = $("#from_date").val();
			var dates = $("#date").val();
			// alert(dates);
			var order_type = $("#order_type").val();
			var state = $("#states").val();
			var dist_type = $("#dist_type").val();
			var selected = [];
			$('input[type=checkbox]:checked').each(function () {
                
                var value = $(this).attr("value");
                selected.push(value);
			});
			let selected_ids = selected.toString();
			
			$.ajax({
				url:"<?php echo admin_url(); ?>order/export_pending_order_item",
				method:"POST",
				data:{from_date:from_date,dates:dates, order_type:order_type, state:state, dist_type:dist_type,selected_ids:selected_ids},
				beforeSend: function () {
					
					$('#searchh3').css('display','block');   
				},
				complete: function () {
					$('#searchh3').css('display','none');
				},
				success:function(data){
					response = JSON.parse(data);
					window.location.href = response.site_url+response.filename;
				}
			});
		});
		$('#order_type').on('change',function(){
			var val = $(this).val();
			
			if(val == "all" || val == "C"){
				$('#update').css("display", "none");
				}else{
				$('#update').css("display", "");
			}
		});
		
		$(document).on('click', '.update', function(){
			
			var selected = [];
			$('input[name=cancel_ord_id]:checked').each(function () {
                var value = $(this).attr("value");
                selected.push(value);
			});
			let selected_ids = selected.toString();
			var unselected = [];
			$('input[name=cancel_ord_id]:unchecked').each(function () {
                
                var value = $(this).attr("value");
                unselected.push(value);
			});
			let unselected_ids = unselected.toString();
			var selected_id_remark = [];
			
			var remarkMissing = false;
			$('input[name=cancel_ord_id_re]').each(function () {
                var id = $(this).attr("id");
                if(selected.includes(id)){
                    var value = $(this).val();
					if (value === null || value === undefined || value.trim() === "") {
						remarkMissing = true;
						$(this).focus(); // focus on missing field
						return false; // break loop
					}
                    selected_id_remark.push(value);
				}
			});
			
			if (remarkMissing) {
				alert("Please enter remark for selected order.");
				return false; // stop execution
			}
			
			let selected_ids_remarks = selected_id_remark.toString();
			
			var unselected_id_remark = [];
			$('input[name=cancel_ord_id_re]').each(function () {
                
                var id = $(this).attr("id");
                if(unselected.includes(id)){
                    var value = $(this).val();
                    unselected_id_remark.push(value);
				}
                
			});
			let unselected_ids_remarks = unselected_id_remark.toString();
			
			$.ajax({
				url:"<?php echo admin_url(); ?>order/update_order_status",
				method:"POST",
				data:{selected_ids:selected_ids, selected_ids_remarks:selected_ids_remarks,unselected_ids:unselected_ids,unselected_ids_remarks:unselected_ids_remarks},
				beforeSend: function () {
					$('#searchh4').css('display','block');
					$("#update").addClass("disabled");
				},
				complete: function () {
					$('#searchh4').css('display','none');
				},
				success:function(data)
				{
					setInterval('refreshPage()', 1000);
				}
			})
		});
		
		
		$(document).on('click', '.reset', function(){
			var selected = [];
			$('input[name=cancel_ord_id]:checked').each(function () {
                var value = $(this).attr("value");
                selected.push(value);
			});
			let selected_ids = selected.toString();
			var unselected = [];
			$('input[name=cancel_ord_id]:unchecked').each(function () {
                var value = $(this).attr("value");
                unselected.push(value);
			});
			let unselected_ids = unselected.toString();
			var selected_id_remark = [];
			$('input[name=cancel_ord_id_re]').each(function () {
                
                var id = $(this).attr("id");
                if(selected.includes(id)){
                    var value = $(this).val();
                    selected_id_remark.push(value);
				}
                
			});
			let selected_ids_remarks = selected_id_remark.toString();
			var unselected_id_remark = [];
			$('input[name=cancel_ord_id_re]').each(function () {
                var id = $(this).attr("id");
                if(unselected.includes(id)){
                    var value = $(this).val();
                    unselected_id_remark.push(value);
				}
			});
			let unselected_ids_remarks = unselected_id_remark.toString();
			
			$.ajax({
				url:"<?php echo admin_url(); ?>order/reset_order_status",
				method:"POST",
				data:{selected_ids:selected_ids, selected_ids_remarks:selected_ids_remarks,unselected_ids:unselected_ids,unselected_ids_remarks:unselected_ids_remarks},
				success:function(data)
				{
					setInterval('refreshPage()', 1000);
				}
			})
		});
	});
	
	function refreshPage() {
		location.reload(true);
	}
	
</script>
<script>
    function select_ord(value){
        
        var from_date = $("#from_date").val();
        var dates = $("#date").val();
        var order_type = $("#order_type").val();
        var state = $("#states").val();
        var dist_type = $("#dist_type").val();
        var yourdate1 = dates.split("-").reverse().join("-");
        $("#date_filter_val").html(yourdate1);
        $("#date_filter_val2").html(yourdate1);
        var selected = [];
        $('input[type=checkbox]:checked').each(function () {
			
			var value = $(this).attr("value");
			selected.push(value);
		});
        let selected_ids = selected.toString();
		//alert(text);
        
        $.ajax({
			url:"<?php echo admin_url(); ?>order/load_data2",
			dataType:"JSON",
			method:"POST",
			data:{from_date:from_date,dates:dates, order_type:order_type, state:state, dist_type:dist_type, selected_ids:selected_ids},
			
			success:function(data){
				var html = '';
				var sr = 1;
				var net_total = 0.00;
				var open_total = 0.00;
				if(data.length){
					for(var count = 0; count < data.length; count++)
					{
						html += '<tr >';
						
						var url = admin_url + 'order/order/' + data[count].OrderID;
						var url_new = admin_url + 'order/order/' + data[count].OrderID;
						var SrNo = parseInt(count) + 1;
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="srno" style="width:3%;text-align:center;" >'+SrNo +'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderid" style="width:10%;text-align:center;"><a href="'+url_new+'"  target="_blank" >'+data[count].OrderID+'</a></td>';
						
						var date = data[count].Transdate.substring(0, 10);
						var yourdate = date.split("-").reverse().join("/");
						if(data[count].Dispatchdate !== null && data[count].Dispatchdate !== ''){
							var Dispatchdate = data[count].Dispatchdate.substring(0, 10);
							var Dispatchtime = data[count].Dispatchdate.substring(10, 19);
							
							Dispatchdate = Dispatchdate.split("-").reverse().join("/");
							Dispatchdate = Dispatchdate+Dispatchtime;
							}else{
							Dispatchdate = '';
						}
						var bal_new2 = parseFloat(data[count].bal1) + parseFloat(data[count].balance);
						var balsum2 = parseFloat(data[count].bal1) + parseFloat(data[count].bal2) + parseFloat(data[count].bal3) + parseFloat(data[count].bal4) + parseFloat(data[count].bal5) + parseFloat(data[count].bal6) + parseFloat(data[count].bal7) + parseFloat(data[count].bal8) + parseFloat(data[count].bal9) + parseFloat(data[count].bal10) + parseFloat(data[count].bal11) + parseFloat(data[count].bal12) + parseFloat(data[count].bal13);
						
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:8%;text-align:center;">'+yourdate+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:8%;text-align:center;">'+Dispatchdate+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="date" style="width:8%;text-align:center;">'+data[count].SOID+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:18%;">'+data[count].AccountName+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:18%;">'+data[count].ShipToAccountName+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:11%;text-align:left;">'+data[count].StationName+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:10%;text-align:center;">'+data[count].dist_Type+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:4%;text-align:left;">'+data[count].StateName+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="width:9%;text-align:right;">'+bal_new2.toFixed(2)+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderamt" style="width:11%;text-align:right;">'+data[count].OrderAmt+'</td>';
						net_total += parseFloat(data[count].OrderAmt);
						open_total += parseFloat(bal_new2);
						
						if(data[count].OrderStatus == "C"){
						    var cc = "checked";
						    var c = "Yes"
						    var status = "Cancel";
						    var StaffName = data[count].CancelStaffName;
							}else {
						    var cc = "";
						    var c = "";
						    var status = "Open";
						    var StaffName = data[count].CreateStaffName;
						}
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" contenteditable style="width:15%;text-align:center;">'+status+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" contenteditable style="width:15%;text-align:center;">'+StaffName+'</td>';
						html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" contenteditable style="width:10%;text-align:center;font-size:14px;">'+data[count].remark+'</td>';
						sr++;
						html += '</tr>';  
					}
					html += '<tr>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td style="text-align:center;">Total</td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td style="text-align:right;">'+ open_total.toFixed(2) +'</td>';
					html += '<td style="text-align:right;">'+ net_total.toFixed(2) +'</td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '</tr>';
					}else {
					html += '<tr>';
					html += '<td colspan="11"> No Data Found...</td>';
					html += '</tr>';
				}
				
				$('#export_table_to_excel_1 tbody').html(html);
				
			}
		});
		
		
        $.ajax({
			url:"<?php echo admin_url(); ?>order/load_data_items2",
			dataType:"JSON",
			method:"POST",
			data:{from_date:from_date,dates:dates, order_type:order_type, state:state, dist_type:dist_type, selected_ids:selected_ids},
			beforeSend: function () {
				
				$('#searchh2').css('display','block');
				$('#export_table_to_excel_2 tbody').css('display','none');
				
			},
			complete: function () {
				
				$('#export_table_to_excel_2 tbody').css('display','');
				$('#searchh2').css('display','none');
			},
			success:function(data){
				var html = '';
				var total_cases = 0;
				var total_unitqty = 0;
				var total_bowlqty = 0;
				var total_taxableamt = 0;
				var total_netamt = 0;
				if(data.length){
					for(var count = 0; count < data.length; count++)
					{
						if(data[count].NetOrderAmt == "0.00"){
							
							}else{
							/* var stock2 = parseFloat(data[count].OQty) + parseFloat(data[count].PQty) - parseFloat(data[count].PRQty) - parseFloat(data[count].IQty) + parseFloat(data[count].PRDQty) + parseFloat(data[count].gtiqty) - parseFloat(data[count].gtoqty) - parseFloat(data[count].SQty) + parseFloat(data[count].SRQty) - parseFloat(data[count].DQTY) - parseFloat(data[count].ADJQTY); 
								var stockInCase2 = stock2 / data[count].CaseQty;
							stockInCase2 = parseInt(stockInCase2);*/
							html += '<tr >';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="accountname" style="width:8%;">'+data[count].Item_code+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:20%;">'+data[count].description+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:5%;text-align:right;">'+data[count].CaseQty+'</td>';
							var ordqty = data[count].OrderQty / data[count].CaseQty;
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:6%;text-align:right;">'+ordqty.toFixed(1)+'</td>';
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="prdplan" style="width:25%;text-align:right;">'+data[count].OrderQty+'</td>';
							if(data[count].BowlQty != 'NA'){
								var bowlqty = parseFloat(data[count].OrderQty / data[count].BowlQty).toFixed(2);
								total_bowlqty += parseFloat(bowlqty);
								}else{
								var bowlqty = 'NA';
							}
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="BowlQty" style="width:25%;text-align:right;">'+bowlqty+'</td>';
							/*html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="width:8%;text-align:right;">'+data[count].OrderAmt+'</td>';
								html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="station" style="width:5%;text-align:center;">'+data[count].taxName+'</td>';
								html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="closebalamt" style="width:8%;text-align:right;">'+data[count].NetOrderAmt+'</td>';
							*/html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="orderamt" style="width:8.5%;text-align:right;">'+parseFloat(data[count].StockBal).toFixed(2)+'</td>';
							
							html += '<td class="table_data" data-row_id="'+data[count].OrderID+'" data-column_name="remark" style="width:25%;text-align:right;"></td>';
							html += '</tr >';
							total_taxableamt += parseFloat(data[count].OrderAmt);
							total_netamt += parseFloat(data[count].NetOrderAmt);
							total_cases += ordqty;
							total_unitqty += parseFloat(data[count].OrderQty);
						}
					}
					var total_tax = total_netamt - total_taxableamt;
					html += '<tr >';
					html += '<td></td>';
					html += '<td style="text-align:center;">Total</td>';
					html += '<td></td>';
					html += '<td style="text-align:right;">'+ total_cases.toFixed(1) +'</td>';
					/*html += '<td style="text-align:right;">'+ total_taxableamt.toFixed(2) +'</td>';
						html += '<td style="text-align:right;">'+ total_tax.toFixed(2) +'</td>';
					html += '<td style="text-align:right;">'+ total_netamt.toFixed(2) +'</td>';*/
					html += '<td style="text-align:right;">'+ total_unitqty.toFixed(2) +'</td>';
					html += '<td style="text-align:right;">'+ total_bowlqty.toFixed(2) +'</td>';
					html += '<td></td>';
					html += '<td></td>';
					html += '</tr >';
					}else {
					html += '<tr>';
					html += '<td colspan="11"> No Data Found...</td>';
					html += '</tr>';
				}
				
				$('#export_table_to_excel_2 tbody').html(html);
			}
		});
	}
</script>
<script>
    function myFunction1() 
    {          
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("pending_data");
        tr = table.getElementsByTagName("tr");
        for (i = 4; i < tr.length; i++) 
        {
            tr[i].style.display = "none"; 
            td = tr[i].getElementsByTagName("td"); 
			
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;                
                    if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
                        tr[i].style.display = "";  
                        break; 
					}
				}
			}
		}
	}
	function myFunction2() 
    {          
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput2");
        filter = input.value.toUpperCase();
        table = document.getElementById("export_table_to_excel_2");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) 
        {
            tr[i].style.display = "none"; 
            td = tr[i].getElementsByTagName("td"); 
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;                
                    if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
                        tr[i].style.display = "";  
                        break; 
					}
				}
			}
		}
	}
	
</script>
<script type="text/javascript">
	function printPage(){
        
		var date = $("#date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table border="1" cellpadding="0" cellspacing="0" style="font-size:13px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        
        var print_data = stylesheet+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
	function printItems(){
        
		var date = $("#date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        tableData2= '<table border="1" cellpadding="0" cellspacing="0" style="font-size:13px;">'+document.getElementsByTagName('table')[2].innerHTML+'</table>';
        
        var print_data = stylesheet+tableData2
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
</script>


<script>
    //table to excel (multiple table)
    var array1 = new Array();
    var n = 2; //Total table
    for ( var x=1; x<=n; x++ ) {
        array1[x-1] = 'export_table_to_excel_' + x;
	}
    var tablesToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
		, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>'
		, templateend = '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>'
		, body = '<body>'
		, tablevar = '<table>{table'
		, tablevarend = '}</table>'
		, bodyend = '</body></html>'
		, worksheet = '<x:ExcelWorksheet><x:Name>'
		, worksheetend = '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>'
		, worksheetvar = '{worksheet'
		, worksheetvarend = '}'
		, base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
		, format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
		, wstemplate = ''
		, tabletemplate = '';
		
        return function (table, name, filename) {
            var tables = table;
            var wstemplate = '';
            var tabletemplate = '';
			
            wstemplate = worksheet + worksheetvar + '0' + worksheetvarend + worksheetend;
            for (var i = 0; i < tables.length; ++i) {
                tabletemplate += tablevar + i + tablevarend;
			}
			
            var allTemplate = template + wstemplate + templateend;
            var allWorksheet = body + tabletemplate + bodyend;
            var allOfIt = allTemplate + allWorksheet;
			
            var ctx = {};
            ctx['worksheet0'] = name;
            for (var k = 0; k < tables.length; ++k) {
                var exceltable;
                if (!tables[k].nodeType) exceltable = document.getElementById(tables[k]);
                ctx['table' + k] = exceltable.innerHTML;
			}
			
            document.getElementById("dlink").href = uri + base64(format(allOfIt, ctx));;
            document.getElementById("dlink").download = filename;
            document.getElementById("dlink").click();
		}
	})();
</script>
<script>
    $(document).ready(function(){
		var maxEndDate = new Date('Y/m/d');
		var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
		
		var year = "20"+fin_y;
		var cur_y = new Date().getFullYear().toString().substr(-2);
		if(cur_y => fin_y){
			var year2 = parseInt(fin_y) + parseInt(1);
			var year2_new = "20"+year2;
			
			var e_dat = new Date(year2_new+'/03/31');
			var maxEndDate_new = e_dat;
			}else{
			var maxEndDate_new = maxEndDate;
		}
		
		var minStartDate = new Date(year, 03);
		
		$('#date,#from_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false
		});
	});
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#pending_data tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop").removeClass("asc desc");
		$(".sortablePop span").remove();
		
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		
		rows.sort(function (a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();
			
			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
				} else {
				return ascending
                ? valA.localeCompare(valB)
                : valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});
	$(document).on("click", ".sortablePop2", function () {
		var table = $("#export_table_to_excel_2 tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop2").removeClass("asc desc");
		$(".sortablePop2 span").remove();
		
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		
		rows.sort(function (a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();
			
			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
				} else {
				return ascending
                ? valA.localeCompare(valB)
                : valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});
</script> 