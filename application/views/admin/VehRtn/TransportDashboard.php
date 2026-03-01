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
            					<li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
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
        							<div class="col-md-3">
        								<div class="form-group">
        									<label for="Route" class="control-label" ><small class="req text-danger"></small> Route</label>
        									<select class="selectpicker" name="Route" id="Route" data-width="100%"  data-action-box="true"  <?php echo $rstates; ?> data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        										<option value=""></option>
        										<?php
        											foreach($routes as $key => $value) {
        											?>
        											<option value="<?php echo $value["RouteID"]?>"><?php echo $value["name"]?></option>
        											<?php
        											}
        										?>
        										
        									</select>
        								</div> 
        							</div>
        							
        							<div class="col-md-3">
        								<div class="form-group">
        									<label class="form-label"><small class="req text-danger"> </small>Vehicle Type</label>
        									<select class="selectpicker"  required name="VehicleType" id="VehicleType" data-width="100%" data-none-selected-text="None Selected" data-live-search="true">
        										<option value="">None Selected</option> 
        										<option value="0">Own</option> 
        										<option value="1">Transport</option> 
        										<option value="2">Rental</option> 
        									</select>
        								</div>
        							</div>
							        <div class="clearfix"></div>
							        <div class="col-md-3">
        								<div class="form-group">
        									<label for="vehicle" class="control-label"><small class="req text-danger"></small> Vehicle</label>
        									<select class="selectpicker" name="challan_vehicle" id="vehicle" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo "None Selected"; ?>">
        										<option value="">None Selected</option>
        										
        									</select>
        								</div>
        							</div>
        							<div class="col-md-3">
        								<div class="form-group">
        									<label class="form-label"><small class="req text-danger"> </small> Fuel Type</label>
        									<select class="selectpicker" name="fuel_type" id="fuel_type" data-width="100%" data-none-selected-text="None selected" data-live-search="true" required>
        										<option value=""></option>
        										<option value="CNG" >CNG</option>
        										<option value="Diesel" >Diesel</option>
        										<option value="Petrol" >Petrol</option>
        										<option value="LPG" >LPG</option>
        										<option value="Electric" >Electric</option>
        									</select>
        								</div>
        							</div>
        							
        							<div class="col-md-3" style="margin-top:20px;">
        								<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button> 
        							</div>
							    </div>
							</div>
							<div class="col-md-6">
							    <div class="row">
							        <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
        								<div class="top_stats_wrapper custdesg bg2">
        									<div class="col-md-12">
        									    <p class="TotalTripsSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="TotalTrips"></p>
        										<p class="title"><?php echo _l('Total Trips'); ?></p>
        									</div>
        								</div>
        							</div>
        							
							        <div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
        								<div class="top_stats_wrapper custdesg bg1">
        									<div class="col-md-12">
        									    <p class="TotalVehiclesSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="TotalVehicles"><?php echo count($Vehicles); ?></p>
        										<p class="title"><?php echo _l('Total Vehicles'); ?></p>
        									</div>
        								</div>
        							</div>
        							
        							<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
        								<div class="top_stats_wrapper custdesg bg2">
        									<div class="col-md-12">
        									    <p class="TotalTravelSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="TotalTravel"></p>
        										<p class="title">Distance Travel <span style="font-size:10px;">(Km)</span></p>
        									</div>
        								</div>
        							</div>
        							
        							<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
        								<div class="top_stats_wrapper custdesg bg1">
        									<div class="col-md-12">
        									    <p class="TotalExpSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="TotalExp"></p>
        										<p class="title">Total Expenses</p>
        									</div>
        								</div>
        							</div>
        							<div class="clearfix"></div>
        							<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
        								<div class="top_stats_wrapper custdesg bg1">
        									<div class="col-md-12">
        									    <p class="ExpensePerKmSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="ExpensePerKm"></p>
        										<p class="title">Expense/KM</p>
        									</div>
        								</div>
        							</div>
        							<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
        								<div class="top_stats_wrapper custdesg bg2">
        									<div class="col-md-12">
        									    <p class="FuelConsumptionSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="FuelConsumption"></p>
        										<p class="title"><?php echo _l('Fuel Consumption (₹)'); ?></p>
        									</div>
        								</div>
        							</div>
        							
        							<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
        								<div class="top_stats_wrapper custdesg bg1">
        									<div class="col-md-12">
        									    <p class="MileageGapSpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="MileageGap"></p>
        										<p class="title">Mileage Gap</p>
        									</div>
        								</div>
        							</div>
        							
        							<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
        								<div class="top_stats_wrapper custdesg bg2">
        									<div class="col-md-12">
        									    <p class="FuelEfficiencySpinner mtop5 labeltxt" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
        										<p class="mtop5 labeltxt" id="FuelEfficiency"></p>
        										<p class="title">Fuel Efficiency</p>
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
	       
	        <!-- First Row -->
	        
	        <div class="col-md-4 count_of_trip_by_route_fuel_vehicle_type Padding_right">
	            <div class="panel_s top_stats_wrapper">
					<div class="panel-body">
					    <p class="count_of_trip_by_route_fuel_vehicle_typeSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure count_of_trip_by_route_fuel_vehicle_typeFigure">
							<div id="count_of_trip_by_route_fuel_vehicle_type"></div>
						</figure>
					</div>
				</div>
			</div>
			
			<div class="col-md-4 Padding_left_right highest_delivery_station">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="highest_delivery_stationSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure highest_delivery_stationFigure">
							<div id="highest_delivery_station"></div>
						</figure>
					</div>
				</div>
			</div>
			
			<div class="col-md-4 Padding_left top_five_mileage_vehicle">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="top_five_mileage_vehicleSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure top_five_mileage_vehicleFigure">
							<div id="top_five_mileage_vehicle"></div>
						</figure>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<!-- Second Row -->
			
			<div class="col-md-4  Padding_right Distribution_of_expenses">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="Distribution_of_expensesSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure Distribution_of_expensesFigure">
							<div id="Distribution_of_expenses"></div>
						</figure>
					</div>
				</div>
			</div>
			
			<div class="col-md-8 Padding_left Expenses_by_month">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="Expenses_by_monthSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <div class="row Expenses_by_monthFigure">
							<div class="col-md-4">
								<div class="form-group" style="display: flex; align-items: center;">
									<label style="margin-right: 20px;margin-left: 20px;"><input type="radio" name="FilterType" value="Route" checked> Route</label>
									<label style="margin-right: 20px;"><input type="radio" name="FilterType" value="VehicleType"> Vehicle Type</label>
									<label><input type="radio" name="FilterType" value="FuelType"> Fuel Type</label>
								</div>
							</div>
						    <div class="col-md-12">
						        <figure class="highcharts-figure">
									<div id="Expenses_by_month"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			
			<!-- Third Row -->
			<div class="col-md-4 Padding_right Highest_maintance_charge_vehicles">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="Highest_maintance_charge_vehiclesSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure Highest_maintance_charge_vehiclesFigure">
							<div id="Highest_maintance_charge_vehicles"></div>
						</figure>
					</div>
				</div>
			</div>
			<div class="col-md-4 Padding_left_right Delivery_efficience_by_driver">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="Delivery_efficience_by_driverSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure Delivery_efficience_by_driverFigure">
							<div id="Delivery_efficience_by_driver"></div>
						</figure>
					</div>
				</div>
			</div>
			
	        <div class="col-md-4 Padding_left Total_vehicle_by_vehicle_type">
	            <div class="panel_s">
					<div class="panel-body" >
					    <p class="Total_vehicle_by_vehicle_typeSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure Total_vehicle_by_vehicle_typeFigure">
							<div id="Total_vehicle_by_vehicle_type"></div>
						</figure>
					</div>
				</div>
			</div>
			
			
			<div class="clearfix"></div>
			<div class="col-md-4 Padding_right Total_vehicle_by_fuel_type">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="Total_vehicle_by_fuel_typeSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure Total_vehicle_by_fuel_typeFigure">
							<div id="Total_vehicle_by_fuel_type"></div>
						</figure>
					</div>
				</div>
			</div>
			<div class="col-md-8 Padding_left_right Fleet_utilization">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="Fleet_utilizationSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure Fleet_utilizationFigure">
							<div id="Fleet_utilization"></div>
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
    	font-size: 23px;
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
<!--new update -->
<!--<script src="https://code.highcharts.com/dashboards/datagrid.js"></script>
	<script src="https://code.highcharts.com/dashboards/dashboards.js"></script>
<script src="https://code.highcharts.com/dashboards/modules/layout.js"></script>-->

<script>
    $('#SubGroup').on('change',function(){
		var SubGroup = $("#SubGroup").val();
		$.ajax({
			url:"<?php echo admin_url(); ?>sale_reports/GetGroupWiseItemList",
			dataType:"JSON",
			method:"POST",
			data:{SubGroup:SubGroup},
			beforeSend: function () {
			},
			complete: function () {
			},
			success:function(data){
			    let ItemList = data;
    			$("#Items").children().remove();
    			for (var i = 0; i < ItemList.length; i++) {
    				$("#Items").append('<option value="'+ItemList[i]["item_code"]+'">'+ItemList[i]["description"]+'</option>');
				}
    			$('.selectpicker').selectpicker('refresh');
			}
		});
	});
	
</script>
<script>
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	}
</script>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		function GetCountersValue(from_date,to_date,Route,VehicleType,fuel_type,vehicle)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetTransportCounters",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,Route:Route,VehicleType:VehicleType,fuel_type:fuel_type,vehicle:vehicle},
				beforeSend: function () {
				    $('.TotalTripsSpinner').show();
				    $('#TotalTrips').hide();
				    $('.TotalVehiclesSpinner').show();
				    $('#TotalVehicles').hide();
				    $('.TotalTravelSpinner').show();
				    $('#TotalTravel').hide();
				    $('.TotalExpSpinner').show();
				    $('#TotalExp').hide();
				    $('.ExpensePerKmSpinner').show();
				    $('#ExpensePerKm').hide();
				    $('.FuelConsumptionSpinner').show();
				    $('#FuelConsumption').hide();
				    $('.MileageGapSpinner').show();
				    $('#MileageGap').hide();
				    $('.FuelEfficiencySpinner').show();
				    $('#FuelEfficiency').hide();
				},
				complete: function () {
				    $('.TotalTripsSpinner').hide();
				    $('#TotalTrips').show();
				    $('.TotalVehiclesSpinner').hide();
				    $('#TotalVehicles').show();
				    $('.TotalTravelSpinner').hide();
				    $('#TotalTravel').show();
				    $('.TotalExpSpinner').hide();
				    $('#TotalExp').show();
				    $('.ExpensePerKmSpinner').hide();
				    $('#ExpensePerKm').show();
				    $('.FuelConsumptionSpinner').hide();
				    $('#FuelConsumption').show();
				    $('.MileageGapSpinner').hide();
				    $('#MileageGap').show();
				    $('.FuelEfficiencySpinner').hide();
				    $('#FuelEfficiency').show();
				},
				success:function(returndata){
					var TotalTrips = returndata.TotalTrips;
					var TotalVehicles = returndata.TotalVehicles;
					var FuelConsumption = returndata.FuelConsumption;
					var MileageGap = returndata.MileageGap;
					var FuelEfficiency = returndata.FuelEfficiency;
					var TotalTravel = parseFloat(returndata.TotalTravel) || 0;
					var TotalExp = parseFloat(returndata.TotalExp) || 0;
					
					var expensePerKm = 0;

					if(TotalTravel > 0) {
						expensePerKm = TotalExp / TotalTravel;
					}

					$("#TotalTrips").html(TotalTrips ?? "0");
					$("#MileageGap").html(parseFloat(MileageGap).toFixed(2) ?? "0");
					$("#FuelEfficiency").html(parseFloat(FuelEfficiency).toFixed(2) ?? "0");
					$("#TotalVehicles").html(TotalVehicles ?? "0");
					$("#TotalExp").html(TotalExp ?? "0");
					$("#ExpensePerKm").html(expensePerKm.toFixed(2));
					$("#FuelConsumption").html((FuelConsumption ?? "0"));
				// 	$("#TotalTravel").html((TotalTravel ?? "0") + " Kms");
					$("#TotalTravel").html((TotalTravel ?? "0"));
				}
			});
		}
		$('#search_data').on('click',function(){
			// var month = $("#month").val();
			var from_date = $("#from_date2").val();
			var to_date = $("#to_date2").val();
			var Route = $("#Route").val();
			var VehicleType = $("#VehicleType").val();
			var fuel_type = $("#fuel_type").val();
			var vehicle = $("#vehicle").val();
			var FilterType = $('input[name="FilterType"]:checked').val();
			
			GetCountersValue(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			
			// load_data(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			// load_data2(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			Total_vehicle_by_vehicle_type(from_date,to_date);
			Total_vehicle_by_fuel_type(from_date,to_date);
			count_of_trip_by_route_fuel_vehicle_type(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			highest_delivery_station(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			top_five_mileage_vehicle(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			Delivery_efficience_by_driver(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			Distribution_of_expenses(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			Expenses_by_month(from_date,to_date,Route,VehicleType,fuel_type,vehicle,FilterType);
			Highest_maintance_charge_vehicles(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			Fleet_utilization(from_date,to_date,Route,VehicleType,fuel_type,vehicle);
			
		});
		function load_data(from_date,to_date,Route,VehicleType,fuel_type,vehicle)
		{
			$.ajax({
				url: "<?php echo admin_url(); ?>VehRtn/GetStandard_VS_ActualMileage",
				dataType: "JSON",
				method: "POST",
				data:{from_date:from_date, to_date:to_date,Route:Route,VehicleType:VehicleType,fuel_type:fuel_type,vehicle:vehicle},
				success: function (returndata) {
					
					Highcharts.chart('container', {
						chart: {
							type: 'column'
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Standard VS Actual Mileage '+from_date+' To '+to_date+'</b>'
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
								text: 'Mileage'
							}
						},
						tooltip: {
							pointFormat: 'Mileage : <b>{point.y:.1f} </b>'
						},
						plotOptions: {
							column: {
								pointPadding: 0.2,
								borderWidth: 0
							}
						},
						series: [
						{
							name: 'Standard',
							data: returndata.Standard,
						},
						{
							name: 'Actual',
							data: returndata.Actual,
						}
						]
					});
					
				}
			});
		}
		function load_data2(from_date,to_date,Route,VehicleType,fuel_type,vehicle)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetMileageReportChart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,Route:Route,VehicleType:VehicleType,fuel_type:fuel_type,vehicle:vehicle},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table-daily_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table-daily_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					Highcharts.chart('container2', {
						chart: {
							type: 'column'
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Top Mileage By Driverwise '+from_date+' To '+to_date+'</b>'
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
								text: 'Mileage'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Milegae : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: data,
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
		function Total_vehicle_by_vehicle_type(from_date, to_date) {
			$.ajax({
				url: "<?php echo admin_url(); ?>VehRtn/GetVehicleTypeChart",
				dataType: "JSON",
				method: "POST",
				data: { from_date: from_date, to_date: to_date },
				beforeSend: function () {
					$('.Total_vehicle_by_vehicle_typeSpinner').show();
					$('.Total_vehicle_by_vehicle_typeFigure').hide();
				},
				complete: function () {
					$('.Total_vehicle_by_vehicle_typeSpinner').hide();
					$('.Total_vehicle_by_vehicle_typeFigure').show();
				},
				success: function (returndata) {
					// Calculate total
					let total = 0;
					returndata.VchTypes.forEach(function(point) {
						total += point.y;
					});
					
					Highcharts.chart('Total_vehicle_by_vehicle_type', {
						chart: {
							type: 'pie',
							height: 250,
							events: {
								render: function () {
									var chart = this,
									width = chart.plotWidth,
									height = chart.plotHeight;
									
									// Remove existing label if any
									if (chart.customLabel) {
										chart.customLabel.destroy();
									}
									
									// Add the total label in the center
									chart.customLabel = chart.renderer.text(
									'Total<br>' + total,
									chart.plotLeft + width / 2,
									chart.plotTop + height / 2
									).css({
										color: '#000000',
										fontSize: '10px',
										textAlign: 'center'
										}).attr({
										align: 'center'
									}).add();
								}
							}
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Total Vehicles By Vehicle Type</b>'
						},
						plotOptions: {
							pie: {
								innerSize: '50%', // Donut shape
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.y:.1f}',
									connectorColor: 'silver'
								},
								showInLegend: true
							}
						},
						tooltip: {
							pointFormat: 'Vehicles: <b>{point.y:.1f}</b> ({point.percentage:.1f}%)'
						},
						series: [{
							name: 'Vehicles',
							data: returndata.VchTypes,
							colors: ['#119EFA', '#15f34f', '#ef370dc7', '#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#50B432', '#0d91efc7', '#ED561B']
						}]
					});
				}
			});
		}
		
		
		
		function Total_vehicle_by_fuel_type(from_date, to_date) {
			$.ajax({
				url: "<?php echo admin_url(); ?>VehRtn/GetVehicleFuelTypeChart",
				dataType: "JSON",
				method: "POST",
				data: { from_date: from_date, to_date: to_date },
				beforeSend: function () {
					$('.Total_vehicle_by_fuel_typeSpinner').show();
					$('.Total_vehicle_by_fuel_typeFigure').hide();
				},
				complete: function () {
					$('.Total_vehicle_by_fuel_typeSpinner').hide();
					$('.Total_vehicle_by_fuel_typeFigure').show();
				},
				success: function (returndata) {
					// Calculate total
					let total = 0;
					returndata.VchTypes.forEach(function(point) {
						total += point.y;
					});
					
					Highcharts.chart('Total_vehicle_by_fuel_type', {
						chart: {
							type: 'pie',
							height: 250, 
							events: {
								render: function () {
									var chart = this,
									width = chart.plotWidth,
									height = chart.plotHeight;
									
									// Remove existing label if any
									if (chart.customLabel) {
										chart.customLabel.destroy();
									}
									
									// Add the total label in the center
									chart.customLabel = chart.renderer.text(
									'Total<br>' + total,
									chart.plotLeft + width / 2,
									chart.plotTop + height / 2
									).css({
										color: '#000000',
										fontSize: '10px',
										textAlign: 'center'
										}).attr({
										align: 'center'
									}).add();
								}
							}
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Total Vehicles By Fuel Type</b>'
						},
						plotOptions: {
							pie: {
								innerSize: '50%', // Donut shape
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.y:.1f}',
									connectorColor: 'silver'
								},
								showInLegend: true
							}
						},
						tooltip: {
							pointFormat: 'Vehicles: <b>{point.y:.1f}</b> ({point.percentage:.1f}%)'
						},
						series: [{
							name: 'Vehicles',
							data: returndata.VchTypes,
							colors: ['#119EFA', '#15f34f', '#ef370dc7', '#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#50B432', '#0d91efc7', '#ED561B']
						}]
					});
				}
			});
		}
		
		function count_of_trip_by_route_fuel_vehicle_type(from_date, to_date, Route, VehicleType, fuel_type, vehicle) {
			$.ajax({
				url: "<?php echo admin_url(); ?>VehRtn/GetTotalChallanByRouteChart",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					Route: Route,
					VehicleType: VehicleType,
					fuel_type: fuel_type,
					vehicle: vehicle
				},
				beforeSend: function () {
					$('.count_of_trip_by_route_fuel_vehicle_typeSpinner').show();
					$('.count_of_trip_by_route_fuel_vehicle_typeFigure').hide();
				},
				complete: function () {
					$('.count_of_trip_by_route_fuel_vehicle_typeSpinner').hide();
					$('.count_of_trip_by_route_fuel_vehicle_typeFigure').show();
				},
				success: function (returndata) {
					// Calculate total
					let total = 0;
					returndata.VchTypes.forEach(function(point) {
						total += point.y;
					});
					
					Highcharts.chart('count_of_trip_by_route_fuel_vehicle_type', {
						chart: {
							type: 'pie',
							height: 250,
							events: {
								render: function () {
									var chart = this,
									width = chart.plotWidth,
									height = chart.plotHeight;
									
									// Remove existing label if any
									if (chart.customLabel) {
										chart.customLabel.destroy();
									}
									
									// Add the total label in the center
									chart.customLabel = chart.renderer.text(
									'Total<br>' + total,
									chart.plotLeft + width / 2,
									chart.plotTop + height / 2
									).css({
										color: '#000000',
										fontSize: '10px',
										textAlign: 'center'
										}).attr({
										align: 'center'
									}).add();
								}
							}
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Count Of Trips By Route ' + from_date + ' To ' + to_date + '</b>'
						},
						plotOptions: {
							pie: {
								innerSize: '50%', // Donut shape
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.y:.1f}',
									connectorColor: 'silver'
								},
								showInLegend: true
							}
						},
						tooltip: {
							pointFormat: 'Challans: <b>{point.y:.1f}</b> ({point.percentage:.1f}%)'
						},
						series: [{
							name: 'Challans',
							data: returndata.VchTypes,
							colors: ['#119EFA', '#15f34f', '#ef370dc7', '#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#50B432', '#0d91efc7', '#ED561B']
						}]
					});
				}
			});
		}
		
		function highest_delivery_station(from_date,to_date,Route,VehicleType,fuel_type,vehicle)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetHighestDeliveryStationChart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,Route:Route,VehicleType:VehicleType,fuel_type:fuel_type,vehicle:vehicle},
				beforeSend: function () {
					$('.highest_delivery_stationSpinner').show();
					$('.highest_delivery_stationFigure').hide();
				},
				complete: function () {
					$('.highest_delivery_stationSpinner').hide();
					$('.highest_delivery_stationFigure').show();
				},
				success:function(returndata){
					Highcharts.chart('highest_delivery_station', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Highest Delivery Stations '+from_date+' To '+to_date+'</b>'
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
								text: 'Total'
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
							data: returndata.VchTypes,
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
		
		function top_five_mileage_vehicle(from_date,to_date,Route,VehicleType,fuel_type,vehicle)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/Get_top_five_mileage_vehicle",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,Route:Route,VehicleType:VehicleType,fuel_type:fuel_type,vehicle:vehicle},
				beforeSend: function () {
					$('.top_five_mileage_vehicleSpinner').show();
					$('.top_five_mileage_vehicleFigure').hide();
				},
				complete: function () {
					$('.top_five_mileage_vehicleSpinner').hide();
					$('.top_five_mileage_vehicleFigure').show();
				},
				success:function(returndata){
					Highcharts.chart('top_five_mileage_vehicle', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Best 5 Mileage Vehicle '+from_date+' To '+to_date+'</b>'
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
								text: 'Total'
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
							data: returndata.VchTypes,
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
		function Highest_maintance_charge_vehicles(from_date,to_date,Route,VehicleType,fuel_type,vehicle)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/Get_Highest_maintance_charge_vehicles",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,Route:Route,VehicleType:VehicleType,fuel_type:fuel_type,vehicle:vehicle},
				beforeSend: function () {
					$('.Highest_maintance_charge_vehiclesSpinner').show();
					$('.Highest_maintance_charge_vehiclesFigure').hide();
				},
				complete: function () {
					$('.Highest_maintance_charge_vehiclesSpinner').hide();
					$('.Highest_maintance_charge_vehiclesFigure').show();
				},
				success:function(returndata){
					Highcharts.chart('Highest_maintance_charge_vehicles', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Highest Maintance Vehicles '+from_date+' To '+to_date+'</b>'
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
								text: 'Total'
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
							data: returndata.VchTypes,
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
		function Delivery_efficience_by_driver(from_date, to_date, Route, VehicleType, fuel_type, vehicle) {
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetTotalChallanByDriverChart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,Route:Route,VehicleType:VehicleType,fuel_type:fuel_type,vehicle:vehicle},
				beforeSend: function () {
					$('.Delivery_efficience_by_driverSpinner').show();
					$('.Delivery_efficience_by_driverFigure').hide();
				},
				complete: function () {
					$('.Delivery_efficience_by_driverSpinner').hide();
					$('.Delivery_efficience_by_driverFigure').show();
				},
				success:function(returndata){
					Highcharts.chart('Delivery_efficience_by_driver', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Delivery Efficiency By Driver '+from_date+' To '+to_date+'</b>'
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
								text: 'Total'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Delivery : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: returndata.TotalChallan,
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
		
		
		function Distribution_of_expenses(from_date,to_date,Route,VehicleType,fuel_type,vehicle)
		{
			$.ajax({
				url: "<?php echo admin_url(); ?>VehRtn/ExpensesCalculationBydate",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					Route: Route,
					VehicleType: VehicleType,
					fuel_type: fuel_type,
					vehicle: vehicle
				},
				beforeSend: function () {
					$('.Distribution_of_expensesSpinner').show();
					$('.Distribution_of_expensesFigure').hide();
				},
				complete: function () {
					$('.Distribution_of_expensesSpinner').hide();
					$('.Distribution_of_expensesFigure').show();
				},
				success: function (returndata) {
					// Calculate total expenses
					let total = 0;
					returndata.Expenses.forEach(function(point) {
						total += point.y;
					});
					
					Highcharts.chart('Distribution_of_expenses', {
						chart: {
							type: 'pie',
							height: 300,
							events: {
								render: function () {
									var chart = this,
									width = chart.plotWidth,
									height = chart.plotHeight;
									
									// Remove existing label if any
									if (chart.customLabel) {
										chart.customLabel.destroy();
									}
									
									// Add the total label in the center
									chart.customLabel = chart.renderer.text(
									'Total<br>' + total.toFixed(1),
									chart.plotLeft + width / 2,
									chart.plotTop + height / 2
									).css({
										color: '#000000',
										fontSize: '10px',
										textAlign: 'center'
										}).attr({
										align: 'center'
									}).add();
								}
							}
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Distribution Of Expenses ' + from_date + ' To ' + to_date + '</b>'
						},
						plotOptions: {
							pie: {
								innerSize: '50%', // Donut shape
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true,
									format: '<b>{point.name}</b>: {point.y:.1f}',
									connectorColor: 'silver'
								},
								showInLegend: true
							}
						},
						tooltip: {
							pointFormat: 'Amount: <b>{point.y:.1f}</b> ({point.percentage:.1f}%)'
						},
						series: [{
							name: 'Expenses',
							data: returndata.Expenses,
							colors: ['#119EFA', '#15f34f', '#ef370dc7', '#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4', '#50B432', '#0d91efc7', '#ED561B']
						}]
					});
				}
			});
			
		}
		function Expenses_by_month(from_date, to_date, Route, VehicleType, fuel_type, vehicle,FilterType) {
			$.ajax({
				url: "<?php echo admin_url(); ?>VehRtn/GetMonthWiseExpenses",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					Route: Route,
					VehicleType: VehicleType,
					fuel_type: fuel_type,
					vehicle: vehicle,
					FilterType: FilterType,
				},
				beforeSend: function () {
					$('.Expenses_by_monthSpinner').show();
					$('.Expenses_by_monthFigure').hide();
				},
				complete: function () {
					$('.Expenses_by_monthSpinner').hide();
					$('.Expenses_by_monthFigure').show();
				},
				success: function (returndata) {
					// Assuming returndata.months is an array like ["Apr-2025", "May-2025", ..., "Mar-2026"]
					// And returndata.series is the series data accordingly
					
					Highcharts.chart('Expenses_by_month', {
						chart: {
							type: 'line',
							height: 253,
						},
						title: {
							text: 'Expenses By Month'
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: returndata.Months // Set dynamically from server
						},
						yAxis: {
							title: {
								text: 'Expenses'
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
						series: returndata.TotalExpense
					});
				}
			});
		}
		function Fleet_utilization(from_date, to_date, Route, VehicleType, fuel_type, vehicle) {
			$.ajax({
				url: "<?php echo admin_url(); ?>VehRtn/GetFleetUtilization",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
					Route: Route,
					VehicleType: VehicleType,
					fuel_type: fuel_type,
					vehicle: vehicle,
				},
				beforeSend: function () {
					$('.Fleet_utilizationSpinner').show();
					$('.Fleet_utilizationFigure').hide();
				},
				complete: function () {
					$('.Fleet_utilizationSpinner').hide();
					$('.Fleet_utilizationFigure').show();
				},
				success: function (returndata) {
					// Assuming returndata.months is an array like ["Apr-2025", "May-2025", ..., "Mar-2026"]
					// And returndata.series is the series data accordingly
					
					Highcharts.chart('Fleet_utilization', {
						chart: {
							type: 'line',
							height: 253,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Fleet Utilization ' + from_date + ' To ' + to_date + '</b>'
						},
						xAxis: {
							categories: returndata.Dates // Set dynamically from server
						},
						yAxis: {
							title: {
								text: 'Utilization %'
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
						series: returndata.TotalExpense
					});
				}
			});
		}
		
		$('#search_data').click();
		
		$('input[name="FilterType"]').on('change', function() {
			var from_date = $("#from_date2").val();
			var to_date = $("#to_date2").val();
			var Route = $("#Route").val();
			var VehicleType = $("#VehicleType").val();
			var fuel_type = $("#fuel_type").val();
			var vehicle = $("#vehicle").val();
			var FilterType = $('input[name="FilterType"]:checked').val();
			Expenses_by_month(from_date,to_date,Route,VehicleType,fuel_type,vehicle,FilterType);
		});
	});
	
	$('#VehicleType').on('change', function() {
		var VehicleType = $(this).val();
		var from_date = $("#from_date2").val();
		var to_date = $("#to_date2").val();
		var url = "<?php echo base_url(); ?>admin/VehRtn/GetVehiclesByType";
		$("#vehicle").find('option').remove();
		$("#vehicle").selectpicker("refresh");
		
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {VehicleType: VehicleType, from_date:from_date, to_date:to_date },
            dataType:'json',
            success: function(data) {
                
				$("#vehicle").append(new Option('None Selected', ''));
                for (var i = 0; i < data.length; i++) {
                    $("#vehicle").append(new Option(data[i].VehicleID, data[i].VehicleID));
				}
                $('.selectpicker').selectpicker('refresh');
			}
		});
	});
</script>
<script type="text/javascript">
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