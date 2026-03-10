<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content" >
		
		<!-- Filet row-->
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
				$from_date = date('01/m/Y');
				$to_date = date('d/m/Y');
			}
		?> 
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Dashboard</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-3">
										<?php
											echo render_date_input('from_date2','From Date',$from_date);
										?>
									</div>
									<div class="col-md-3">
										<?php
											echo render_date_input('to_date2','To Date',$to_date);
										?>
									</div>
									<div class="col-md-3" style="display:none">
										<div class="form-group">
											<label for="TradeType" class="control-label" ><small class="req text-danger"></small> Trade Type</label>
											<select class="selectpicker" name="TradeType" id="TradeType" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
												<option value=""></option>
												<option value="General">General Trade</option>
												<option value="Modern">Modern Trade</option>
												
												
											</select>
										</div> 
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="AccountID" class="control-label" ><small class="req text-danger"></small> Vendor</label>
											
											<!-- <select class="selectpicker" name="AccountID" id="AccountID" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php //echo _l('dropdown_non_selected_tex'); ?>">
												<option value=""></option>
												<?php //foreach($AllPartyList as $key => $value){ ?>
												<option value="<?php //echo $value["AccountID"]?>"><?php //echo $value["company"]?></option>
												<?php //}  ?>        										
											</select> -->
											
											<select class="selectpicker" name="AccountID" id="AccountID" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
												
											</select>
										</div> 
									</div>
									
									<div class="clearfix"></div>
									
									<div class="col-md-4">
										<div class="form-group">
											<label class="form-label">Main Item Group</label>
											<select class="selectpicker" name="MainItemGroup" id="MainItemGroup" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
												<option value=""></option>   
												<?php foreach ($MainItemGroup as $key => $value){ ?>
													<option <?php if($value['id'] == '2'){echo "selected";}?> value="<?php echo $value['id'];?>"><?php echo $value['name']; ?></option>   
													<?php   
													}
												?>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label" for="SubGroup1">Sub-Group 1</label>
											<select class="selectpicker display-block" data-width="100%" id="SubGroup1" name="SubGroup1" data-none-selected-text="None selected" data-live-search="true" >
												<option value="">None selected</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label" for="SubGroup2">Sub-Group 2</label>
											<select class="selectpicker display-block" data-width="100%" id="SubGroup2" name="SubGroup2" data-none-selected-text="None selected" data-live-search="true" >
												<option value="">None selected</option>
											</select>
										</div>
									</div>
									
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label" for="ItemID">Item</label>
											<select class="selectpicker display-block" data-width="100%" id="ItemID" name="ItemID" data-none-selected-text="None selected" data-live-search="true" >
												<option value="">None selected</option>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label" for="ItemType">Item Type</label>
											<select class="selectpicker display-block" data-width="100%" id="ItemType" name="ItemType" data-none-selected-text="None selected">
												<option value="">All</option>
												<option value="Taxable">Taxable</option>
												<option value="NonTaxable">Non Taxable</option>
											</select>
										</div>
									</div>
									
									<div class="col-md-4" style="display:none">
										<div class="form-group">
											<label for="Station" class="control-label" ><small class="req text-danger"></small> Station</label>
											<select class="selectpicker" name="Station" id="Station" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
												<option value=""></option>
												<?php
													foreach($StationList as $key => $value) {
													?>
													<option value="<?php echo $value["id"]?>"><?php echo $value["StationName"]?></option>
													<?php
													}
												?>
											</select>
										</div> 
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="City" class="control-label" ><small class="req text-danger"></small> City</label>
											<!-- <select class="selectpicker" name="City" id="City" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php //echo _l('dropdown_non_selected_tex'); ?>">
												<option value=""></option>
												<?php
													// foreach($CityList as $key => $value) {
												// ?>
												// <option value="<?php //echo $value["id"]?>"><?php //echo $value["city_name"]?></option>
												// <?php
													// }
												?>
											</select> -->
											<select class="selectpicker" name="City" id="City" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"></select>
										</div> 
									</div>
									<div class="col-md-2" style="margin-top:20px;">
										<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button> 
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="row">
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg2">
											<div class="col-md-12">
												<p class="TotalVendorSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="TotalVendor"></p>
												<p class="title"><?php echo _l('Total Vendor'); ?></p>
											</div>
										</div>
									</div>
									
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="TotalOrdersSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="TotalOrders"></p>
												<p class="title"><?php echo _l('Total Orders'); ?></p>
											</div>
										</div>
									</div>
									
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg2">
											<div class="col-md-12">
												<p class="TotalPurchaseEntrySpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="TotalPurchaseEntry"></p>
												<p class="title"><?php echo _l('Total Purchase Entry'); ?></p>
											</div>
										</div>
									</div>
									
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="TotalPurchaseInvoiceSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="TotalPurchaseInvoice"></p>
												<p class="title"><?php echo _l('Total Invoice'); ?></p>
											</div>
										</div>
									</div>
									
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg2">
											<div class="col-md-12">
												<p class="TotalPurchaseAmountSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="TotalPurchaseAmount"></p>
												<p class="title"><?php echo _l('Total Purchase Amt'); ?></p>
											</div>
										</div>
									</div>
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="TotalPurchaseQuantitySpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="TotalPurchaseQuantity"></p>
												<p class="title"><?php echo _l('Total Purchase Qty'); ?></p>
											</div>
										</div>
									</div>
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg2">
											<div class="col-md-12">
												<p class="AvgOrderValueSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="AvgOrderValue"></p>
												<p class="title"><?php echo _l('Avg Order Value'); ?></p>
											</div>
										</div>
									</div>
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="AvgOrderQtySpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="AvgOrderQty"></p>
												<p class="title"><?php echo _l('Avg Order Qty'); ?></p>
											</div>
										</div>
									</div>
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg2">
											<div class="col-md-12">
												<p class="PurchaseReturnAmountSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="PurchaseReturnAmount"></p>
												<p class="title"><?php echo _l('Purchase Return Amt'); ?></p>
											</div>
										</div>
									</div>
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="PurchaseReturnQtySpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="PurchaseReturnQty"></p>
												<p class="title"><?php echo _l('Purchase Return Qty'); ?></p>
											</div>
										</div>
									</div>
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg2">
											<div class="col-md-12">
												<p class="AvgReturnOrderSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="AvgReturnOrder"></p>
												<p class="title"><?php echo _l('Avg Return Order'); ?></p>
											</div>
										</div>
									</div>
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="PurchaseGstAmountSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="PurchaseGstAmount"></p>
												<p class="title"><?php echo _l('GST Amount'); ?></p>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6 TopCustomer Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="TopCustomerSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure TopCustomerFigure">
							<div id="TopCustomer"></div>
						</figure>
					</div>
				</div>
			</div>
			<div class="col-md-6 TopGroupItem Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="TopGroupItemSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure TopGroupItemFigure">
							<div id="TopGroupItem"></div>
						</figure>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 Padding_right MonthlyStockLevel">
				<div class="panel_s">
					<div class="panel-body">
						<p class="MonthlyPurchaseSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure MonthlyPurchaseFigure">
							<div id="MonthlyPurchase"></div>
						</figure>
					</div>
				</div>
			</div>
			<div class="col-md-12 Padding_right DayWiseStockLevel">
				<div class="panel_s">
					<div class="panel-body">
						<p class="DailyPurchaseSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure DailyPurchaseFigure">
							<div id="DailyPurchase"></div>
						</figure>
					</div>
				</div>
			</div>
			
			<div class="col-md-6 TopPurchaseRateByItemGroup Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="TopPurchaseRateByItemGroupSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure TopPurchaseRateByItemGroupFigure">
							<div id="TopPurchaseRateByItemGroup"></div>
						</figure>
					</div>
				</div>
			</div>
			<div class="col-md-6 TopPurchaseRateByVendor Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="TopPurchaseRateByVendorSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure TopPurchaseRateByVendorFigure">
							<div id="TopPurchaseRateByVendor"></div>
						</figure>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</div>

<style>
	.CrateLedger { overflow: auto;max-height: 58vh;position:relative;top: 0px; }
	.CrateLedger thead th { position: sticky; top: 0; z-index: 1; }
	.CrateLedger tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.CrateLedger table  { border-collapse: collapse; }
	.CrateLedger th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.CrateLedger th     { background: #50607b;color: #fff !important; }
	
	
</style>
<style>
	@import url("https://code.highcharts.com/css/highcharts.css");
	
	/*	.highcharts-pie-series .highcharts-point {
	stroke: #ede;
	stroke-width: 2px;
	}
	#wrapper{
	background: #fff;
	}
	.highcharts-pie-series .highcharts-data-label-connector {
	stroke: silver;
	stroke-dasharray: 2, 2;
	stroke-width: 2px;
	}
	
	.highcharts-figure,
	.highcharts-data-table table {
	min-width: 320px;
	max-width: 600px;
	margin: 1em auto;
	}
	
	.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #ebebeb;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
	}
	
	.highcharts-data-table caption {
	padding: 1em 0;
	font-size: 1.2em;
	color: #555;
	}
	
	.highcharts-data-table th {
	font-weight: 600;
	padding: 0.5em;
	}
	
	.highcharts-data-table td,
	.highcharts-data-table th,
	.highcharts-data-table caption {
	padding: 0.5em;
	}
	
	.highcharts-data-table thead tr,
	.highcharts-data-table tr:nth-child(even) {
	background: #f8f8f8;
	}
	
	.highcharts-data-table tr:hover {
	background: #f1f7ff;
	}
	
	.highcharts-description {
	margin: 0.3rem 10px;
	}
	
	*/
	.Padding_right{
	padding-right:1px;
	}
	.Padding_left{
	padding-left:1px;
	}
	.Padding_left_right{
	padding-left:1px;
	padding-right:1px;
	}
	.Padding_right .panel_s .panel-body{
	padding:1px;
	min-height:300px;
	max-height:600px;
	}
	.Padding_left .panel_s .panel-body{
	padding:1px;
	min-height:300px;
	max-height:600px;
	}
	.Padding_left_right .panel_s .panel-body{
	padding:1px;
	min-height:300px;
	max-height:600px;
	}
	.highcharts-credits {
	display: none;
	}
	.table-table_staff tbody{
	display: block;
	max-height: 450px;
	overflow-y: scroll;
	width: calc(100% - -8.9em);
	}
	.table-table_staff thead, .table-table_staff tbody tr{
	display: table;
	table-layout: fixed;
	width: 100%;
	
	}
	.table-table_staff thead{
	width: calc(100% - -5.9em);
	}
	.table-table_staff thead{
	position: relative;
	}
	.table-table_staff thead th:last-child:after{
	content: ' ';
	position: absolute;
	background-color: #337ab7;
	width: 1.3em;
	height: 38px;
	right: -1.3em;
	top: 0;
	border-bottom: 2px solid #ddd;
	}
	
	/*.staff_name{*/
	/*width:21%;*/
	/*}*/
	.table-table_staff th td{padding: 32px -20px 12px 14px;
	}
	
	.fontsize{
	font-size:13px;
	}
	.fontsize2{
	font-size:15px;
	}
	
	thead tr:nth-child(2) th {
	top: 20px; /* Offset for the second row to appear below the first */
	}
</style>

<style>
	.table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
	.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
	.table-daily_report tbody th { position: sticky; left: 0; }
	
	
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 0px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
	color: #fff !important; }
	
	.custdesg{
	height:55px;
	}
	.col-md-3.col-sm-3.col-xs-12.quick-stats-invoices {
	padding: 0px 2px 0px 2px;
	}
	.imgsize{
	font-size:40px;
	display: block;
	margin: 0;
	color: #fff;
	}
	.panel_s{
	margin-bottom:5px !important;
	}
	.labeltxt{
	font-size: 20px;
	color: #fff;
	text-align: center;
	/*font-weight: 700;*/
	margin:0px;
	}
	.SpinnerCSS{
	font-size: 80px;
	color: #FF425C;
	text-align: center;
	margin:0px;
	}
	.title{
	font-size: 13px;
	color: #fff;
	text-align: center;
	/*font-weight: 700;*/
	margin:0px;
	}
	.numstyl{
	text-align: left;
	display: block;
	font-size: 14px;
	}
	/*.mtop5 {
	margin-top: 4px;
	margin-bottom: 2px;
	}*/
	.mtop5 {
	margin-top: 1px;
	margin-bottom: 1px;
	}
	.bg1{
	background-image: linear-gradient(to right,#008385 0,#008385 100%);
	background-repeat: repeat-x;
	}
	.bg2{
	background-image: linear-gradient(to right,#FF425C 0,#FF425C 100%);
	background-repeat: repeat-x;
	}
	.bg3{
	background-image: linear-gradient(to right,#FF864A 0,#FF864A 100%);
	background-repeat: repeat-x;
	}
	.bg4{
	background-image: linear-gradient(to right,#11A578 0,#11A578 100%);
	background-repeat: repeat-x;
	}
	.top_stats_wrapper{
	margin-top: 0px;
	border-radius: 5px;
	padding:0px !important;
	margin-bottom: 5px !important;
	}
	.top_stats_wrapper:hover{
	box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.4);
	}
</style>

<?php init_tail(); ?>
<script>
	
	$(document).ready(function(){
		LoadPartyList();
		LoadCityList();     
		$('#MainItemGroup').change();
	})
	
	function LoadPartyList()
	{    
		// debugger;
		var FromDate = $("#from_date2").val();
		var ToDate = $("#to_date2").val();
		
		var url = "<?php echo base_url(); ?>purchase/GetPartyListDateWise";
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: {           
				FromDate: FromDate,
				ToDate: ToDate
			},
			dataType: 'json',
			success: function(response) {
				$("#AccountID").find('option').remove();
				$("#AccountID").selectpicker("refresh");
				$("#AccountID").append(new Option('None selected', ''));
				for (var i = 0; i < response.length; i++) {
					$("#AccountID").append(new Option(response[i].company, response[i].AccountID));
				}
				$('.selectpicker').selectpicker('refresh');
			},        
		});
	}
	
	function LoadCityList()
	{    
		var FromDate = $("#from_date2").val();
		var ToDate = $("#to_date2").val();
		
		var url = "<?php echo base_url(); ?>purchase/GetPartyCityListByFilter";
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: {FromDate: FromDate,ToDate: ToDate  },
			dataType: 'json',
			success: function(response) {
				$("#City").find('option').remove();
				$("#City").selectpicker("refresh");
				$("#City").append(new Option('None selected', ''));
				for (var i = 0; i < response.length; i++) {
					$("#City").append(new Option(response[i].city_name, response[i].city));
				}
				$('.selectpicker').selectpicker('refresh');
			}, 
		});
	}
	
	
	// Date change event handlers
	$('#from_date2').on('change', function(){
		LoadPartyList();
		LoadCityList();     
	});
	
	$('#to_date2').on('change', function(){
		LoadPartyList();
		LoadCityList();     
	});
 	 
	
	$('#MainItemGroup').on('change', function() {
		var MainItemGroup = $(this).val();
		var from_date = $("#from_date2").val();
		var to_date = $("#to_date2").val();
		//alert(from_date);
		var url = "<?php echo base_url(); ?>purchase/GetSubgroup1Data";
		jQuery.ajax({
			type: 'POST',
			url:url,
			data: {MainItemGroup: MainItemGroup, from_date: from_date, to_date: to_date,},
			dataType:'json',
			success: function(data) {
				$("#SubGroup1").find('option').remove();
				$("#SubGroup1").selectpicker("refresh");
				$("#SubGroup1").append(new Option('None selected', ''));
				for (var i = 0; i < data.length; i++) {
					$("#SubGroup1").append(new Option(data[i].name, data[i].id));
				}
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
	
	$('#SubGroup1').on('change', function() {
		var SubGroup1 = $(this).val();
		var from_date = $("#from_date2").val();
		var to_date = $("#to_date2").val();
		//alert(roleid);
		var url = "<?php echo base_url(); ?>purchase/GetSubgroup2Data";
		jQuery.ajax({
			type: 'POST',
			url:url,
			data: {SubGroup1: SubGroup1, from_date: from_date,
			to_date: to_date,},
			dataType:'json',
			success: function(data) {
				$("#SubGroup2").find('option').remove();
				$("#SubGroup2").selectpicker("refresh");
				$("#SubGroup2").append(new Option('None selected', ''));
				for (var i = 0; i < data.length; i++) {
					$("#SubGroup2").append(new Option(data[i].name, data[i].id));
				}
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
	$('#SubGroup2').on('change', function() {
		var SubGroup2 = $(this).val();
		var from_date = $("#from_date2").val();
		var to_date = $("#to_date2").val();
		//alert(roleid);
		var url = "<?php echo base_url(); ?>purchase/GetItemBySubgroup2Data";
		jQuery.ajax({
			type: 'POST',
			url:url,
			data: {SubGroup2: SubGroup2, from_date: from_date,to_date: to_date,},
			dataType:'json',
			success: function(data) {
				$("#ItemID").find('option').remove();
				$("#ItemID").selectpicker("refresh");
				$("#ItemID").append(new Option('None selected', ''));
				for (var i = 0; i < data.length; i++) {
					$("#ItemID").append(new Option(data[i].description, data[i].item_code));
				}
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
	

</script> 

<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		$('#MainItemGroup').change();
		function GetCountersValue(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/GetDashboardCounters",
				dataType:"JSON",
				method:"POST",
				data:{
					from_date: from_date,
					to_date: to_date,
					TradeType: TradeType,
					AccountID: AccountID,
					MainItemGroup: MainItemGroup,
					SubGroup1: SubGroup1,
					SubGroup2: SubGroup2,
					ItemID: ItemID,
					ItemType: ItemType,
					Station: Station,
					City: City,
				},
				beforeSend: function () {
					// Show all counter spinners
					$('.TotalVendorSpinner').show();
					$('#TotalVendor').hide();
					$('.TotalOrdersSpinner').show();
					$('#TotalOrders').hide();
					$('.TotalPurchaseEntrySpinner').show();
					$('#TotalPurchaseEntry').hide();
					$('.TotalPurchaseInvoiceSpinner').show();
					$('#TotalPurchaseInvoice').hide();
					$('.TotalPurchaseAmountSpinner').show();
					$('#TotalPurchaseAmount').hide();
					$('.TotalPurchaseQuantitySpinner').show();
					$('#TotalPurchaseQuantity').hide();
					$('.AvgOrderValueSpinner').show();
					$('#AvgOrderValue').hide();
					$('.AvgOrderQtySpinner').show();
					$('#AvgOrderQty').hide();
					$('.PurchaseReturnAmountSpinner').show();
					$('#PurchaseReturnAmount').hide();
					$('.PurchaseReturnQtySpinner').show();
					$('#PurchaseReturnQty').hide();
					$('.AvgReturnOrderSpinner').show();
					$('#AvgReturnOrder').hide();
					$('.PurchaseGstAmountSpinner').show();
					$('#PurchaseGstAmount').hide();
				},
				complete: function () {
					// Hide all counter spinners
					$('.TotalVendorSpinner').hide();
					$('#TotalVendor').show();
					$('.TotalOrdersSpinner').hide();
					$('#TotalOrders').show();
					$('.TotalPurchaseEntrySpinner').hide();
					$('#TotalPurchaseEntry').show();
					$('.TotalPurchaseInvoiceSpinner').hide();
					$('#TotalPurchaseInvoice').show();
					$('.TotalPurchaseAmountSpinner').hide();
					$('#TotalPurchaseAmount').show();
					$('.TotalPurchaseQuantitySpinner').hide();
					$('#TotalPurchaseQuantity').show();
					$('.AvgOrderValueSpinner').hide();
					$('#AvgOrderValue').show();
					$('.AvgOrderQtySpinner').hide();
					$('#AvgOrderQty').show();
					$('.PurchaseReturnAmountSpinner').hide();
					$('#PurchaseReturnAmount').show();
					$('.PurchaseReturnQtySpinner').hide();
					$('#PurchaseReturnQty').show();
					$('.AvgReturnOrderSpinner').hide();
					$('#AvgReturnOrder').show();
					$('.PurchaseGstAmountSpinner').hide();
					$('#PurchaseGstAmount').show();
				},
				success:function(returndata){
					var TotalVendor = returndata.TotalVendor;
					var TotalOrders = returndata.TotalOrders;
					var TotalPurchaseEntry = returndata.TotalPurchaseEntry;
					var TotalPurchaseInvoice = returndata.TotalPurchaseInvoice;
					var TotalPurchaseAmount = returndata.TotalPurchaseAmount;
					var TotalPurchaseQuantity = returndata.TotalPurchaseQuantity;
					var AvgOrderValue = returndata.AvgOrderValue;
					var AvgOrderQty = returndata.AvgOrderQty;
					var PurchaseReturnAmount = returndata.PurchaseReturnAmount;
					var PurchaseReturnQty = returndata.PurchaseReturnQty;
					var PurchaseGstAmount = returndata.PurchaseGstAmount;
					var AvgReturnOrder = returndata.AvgReturnOrder;
					
					$("#TotalVendor").html(TotalVendor ?? "0");
					$("#TotalOrders").html(TotalOrders ?? "0");
					$("#TotalPurchaseEntry").html(TotalPurchaseEntry ?? "0");
					$("#TotalPurchaseInvoice").html(TotalPurchaseInvoice ?? "0");
					$("#TotalPurchaseAmount").html(TotalPurchaseAmount ?? "0");
					$("#TotalPurchaseQuantity").html(TotalPurchaseQuantity ?? "0");
					$("#AvgOrderValue").html(AvgOrderValue ?? "0");
					$("#AvgOrderQty").html(AvgOrderQty ?? "0");
					$("#PurchaseReturnAmount").html(PurchaseReturnAmount ?? "0");
					$("#PurchaseReturnQty").html(PurchaseReturnQty ?? "0");
					$("#PurchaseGstAmount").html(PurchaseGstAmount ?? "0");
					$("#AvgReturnOrder").html(AvgReturnOrder ?? "0");
				}
			});
		}
		$('#search_data').on('click',function(){
			var from_date = $("#from_date2").val();
			var to_date = $("#to_date2").val();
			var TradeType = $("#TradeType").val();
			var AccountID = $("#AccountID").val();
			var MainItemGroup = $("#MainItemGroup").val();
			var SubGroup1 = $("#SubGroup1").val();
			var SubGroup2 = $("#SubGroup2").val();
			var ItemID = $("#ItemID").val();
			var ItemType = $("#ItemType").val();
			var Station = $("#Station").val();
			var City = $("#City").val();
			
			GetCountersValue(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City);
			TopCustomer(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City);
			TopGroupItem(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City);
			MonthlyPurchase(TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City);
			DailyPurchase(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City);
			TopPurchaseRateByItemGroup(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City);
			TopPurchaseRateByVendor(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City);
			
		});
		
		function TopCustomer(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City){
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/GetTopCustomer",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					TradeType: TradeType,
					AccountID: AccountID,
					MainItemGroup: MainItemGroup,
					SubGroup1: SubGroup1,
					SubGroup2: SubGroup2,
					ItemID: ItemID,
					ItemType: ItemType,
					Station: Station,
					City: City,
				},
				beforeSend: function () {
					$('.TopCustomerSpinner').show();
					$('.TopCustomerFigure').hide();
				},
				complete: function () {
					$('.TopCustomerSpinner').hide();
					$('.TopCustomerFigure').show();
				},
				success: function (returndata) {
					Highcharts.chart('TopCustomer', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Top Vendor From '+from_date+' To '+to_date+'</b>'
						},
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Total '
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Total : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: returndata.TransData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								
							}
						}]
					});
				}
			});
		}
		function TopGroupItem(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City) {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/GetTopGroupItem",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					TradeType: TradeType,
					AccountID: AccountID,
					MainItemGroup: MainItemGroup,
					SubGroup1: SubGroup1,
					SubGroup2: SubGroup2,
					ItemID: ItemID,
					ItemType: ItemType,
					Station: Station,
					City: City,
				},
				beforeSend: function () {
					$('.TopGroupItemSpinner').show();
					$('.TopGroupItemFigure').hide();
				},
				complete: function () {
					$('.TopGroupItemSpinner').hide();
					$('.TopGroupItemFigure').show();
				},
				success: function (returndata) {
					Highcharts.chart('TopGroupItem', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Top Group/Items From '+from_date+' To '+to_date+'</b>'
						},
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Total '
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Total : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: returndata.TransData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								
							}
						}]
					});
				}
			});
		}
		function MonthlyPurchase(TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City) {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/GetMonthlyPurchase",
				dataType: "JSON",
				method: "POST",
				data: {
					TradeType: TradeType,
					AccountID: AccountID,
					MainItemGroup: MainItemGroup,
					SubGroup1: SubGroup1,
					SubGroup2: SubGroup2,
					ItemID: ItemID,
					ItemType: ItemType,
					Station: Station,
					City: City,
				},
				beforeSend: function () {
					$('.MonthlyPurchaseSpinner').show();
					$('.MonthlyPurchaseFigure').hide();
				},
				complete: function () {
					$('.MonthlyPurchaseSpinner').hide();
					$('.MonthlyPurchaseFigure').show();
				},
				success: function (returndata) {
					Highcharts.chart('MonthlyPurchase', {
						chart: {
							type: 'line',
							height: 253,
						},
						title: {
							text: 'Monthly Purchase'
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: returndata.Months // Set dynamically from server
						},
						yAxis: {
							title: {
								text: 'Amount'
							}
						},
						plotOptions: {
							line: {
								dataLabels: {
									enabled: true
								},
								enableMouseTracking: false
							}
						},
						series: returndata.Purchase
					});
				}
			});
		}
		function DailyPurchase(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City) {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/GetDailyPurchase",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					TradeType: TradeType,
					AccountID: AccountID,
					MainItemGroup: MainItemGroup,
					SubGroup1: SubGroup1,
					SubGroup2: SubGroup2,
					ItemID: ItemID,
					ItemType: ItemType,
					Station: Station,
					City: City,
				},
				beforeSend: function () {
					$('.DailyPurchaseSpinner').show();
					$('.DailyPurchaseFigure').hide();
				},
				complete: function () {
					$('.DailyPurchaseSpinner').hide();
					$('.DailyPurchaseFigure').show();
				},
				success: function (returndata) {
					Highcharts.chart('DailyPurchase', {
						chart: {
							type: 'line',
							height: 253,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Daily Purchase Between '+from_date+' To '+to_date+'</b>'
						},
						xAxis: {
							categories: returndata.Days // Set dynamically from server
						},
						yAxis: {
							title: {
								text: 'Total'
							}
						},
						plotOptions: {
							line: {
								dataLabels: {
									enabled: true
								},
								enableMouseTracking: false
							}
						},
						series: returndata.Purchase
					});
				}
			});
		}
		
		function TopPurchaseRateByItemGroup(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City) {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/GetTopPurchaseRateByItemGroup",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					TradeType: TradeType,
					AccountID: AccountID,
					MainItemGroup: MainItemGroup,
					SubGroup1: SubGroup1,
					SubGroup2: SubGroup2,
					ItemID: ItemID,
					ItemType: ItemType,
					Station: Station,
					City: City,
				},
				beforeSend: function () {
					$('.TopPurchaseRateByItemGroupSpinner').show();
					$('.TopPurchaseRateByItemGroupFigure').hide();
				},
				complete: function () {
					$('.TopPurchaseRateByItemGroupSpinner').hide();
					$('.TopPurchaseRateByItemGroupFigure').show();
				},
				success: function (returndata) {
					Highcharts.chart('TopPurchaseRateByItemGroup', {
						chart: {
							type: 'pie',
							height: 400,
							backgroundColor: '#f8f9fa',
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Top Group/Item Wise Purchase Rate From ' + from_date + ' To ' + to_date + '</b>'
						},
						tooltip: {
							useHTML: true,
							headerFormat: '<span style="font-size:14px"><b>{point.name}</b></span><br/>',
							pointFormat: '<span style="color:{point.color}">●</span> <b>{point.percentage:.1f}%</b><br/>Purchase: <b>{point.y:,.0f}</b></b>'
						},
						plotOptions: {
							pie: {
								innerSize: '50%',   // donut effect
								depth: 45,          // 3D style
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b><br/>{point.percentage:.1f}%',
									style: {
										fontSize: '12px',
										fontWeight: 'bold'
									}
								},
								showInLegend: true
							}
						},
						legend: {
							align: 'center',
							verticalAlign: 'bottom',
							layout: 'horizontal'
						},
						series: [{
							name: 'Purchase Rate',
							colorByPoint: true,
							data: returndata.TransData
						}]
					});
					
				}
			});
		}
		function TopPurchaseRateByVendor(from_date,to_date,TradeType,AccountID,MainItemGroup,SubGroup1,SubGroup2,ItemID,ItemType,Station,City) {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/GetTopPurchaseRateByVendor",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					TradeType: TradeType,
					AccountID: AccountID,
					MainItemGroup: MainItemGroup,
					SubGroup1: SubGroup1,
					SubGroup2: SubGroup2,
					ItemID: ItemID,
					ItemType: ItemType,
					Station: Station,
					City: City,
				},
				beforeSend: function () {
					$('.TopPurchaseRateByVendorSpinner').show();
					$('.TopPurchaseRateByVendorFigure').hide();
				},
				complete: function () {
					$('.TopPurchaseRateByVendorSpinner').hide();
					$('.TopPurchaseRateByVendorFigure').show();
				},
				success: function (returndata) {
					Highcharts.chart('TopPurchaseRateByVendor', {
						chart: {
							type: 'pie',
							height: 400,
							backgroundColor: '#f8f9fa',
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Top Vendor Wise Purchase Rate From ' + from_date + ' To ' + to_date + '</b>'
						},
						tooltip: {
							useHTML: true,
							headerFormat: '<span style="font-size:14px"><b>{point.name}</b></span><br/>',
							pointFormat: '<span style="color:{point.color}">●</span> <b>{point.percentage:.1f}%</b><br/>Purchase: <b>{point.y:,.0f}</b></b>'
						},
						plotOptions: {
							pie: {
								innerSize: '50%',   // donut effect
								depth: 45,          // 3D style
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b><br/>{point.percentage:.1f}%',
									style: {
										fontSize: '12px',
										fontWeight: 'bold'
									}
								},
								showInLegend: true
							}
						},
						legend: {
							align: 'center',
							verticalAlign: 'bottom',
							layout: 'horizontal'
						},
						series: [{
							name: 'Purchase Rate',
							colorByPoint: true,
							data: returndata.TransData
						}]
					});
					
				}
			});
		}
		
		$('#search_data').click();
		
	});
	
</script>
<script type="text/javascript">
	function isNumber(evt) {
	evt = (evt) ? evt : window.event;
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	if (charCode = 46 && charCode > 31 
	&& (charCode < 48 || charCode > 57)){
		return false;
	}
	return true;
} 
	function printPage(){
		
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
		var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->FIRMNAME; ?></td></tr><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="9">Sales Report : '+from_date+' To '+to_date+'</td>';
		heading_data += '</tr>';
		heading_data += '</tbody></table>';
		var print_data = stylesheet+heading_data+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
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
			var e_dat2 = new Date(year2+'/03/31');
			var maxEndDate_new = e_dat2;
		}
		
		var minStartDate = new Date(year, 03);
		
		
		$('#from_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false
		});
		
		$('#to_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false,
			showOtherMonths: false,
			pickTime: false,
			orientation: "left",
		});
		
		$(document).on("click", ".sortable", function () {
			var table = $("#table-daily_report tbody");
			var rows = table.find("tr").toArray();
			var index = $(this).index();
			var ascending = !$(this).hasClass("asc");
			
			
			// Remove existing sort classes and reset arrows
			$(".sortable").removeClass("asc desc");
			$(".sortable span").remove();
			
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
		
		
	});
</script>