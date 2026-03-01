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
            					<li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Mileage Report</b></li>
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
											$from_date = date('01/m/Y');
											$to_date = date('d/m/Y');
										}
									?>
									<?php 
										$current_date = date('d/m/Y');
										echo render_date_input('from_date','From Date',$from_date);          
									?>
								</div>
								<div class="col-md-2">
									<?php 
										echo render_date_input('to_date','To Date',$to_date);          
									?>
								</div>
								
								<div class="col-md-2">
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
								<div class="col-md-2">
									<div class="form-group">
										<label for="vehicle" class="control-label"><small class="req text-danger"></small> Vehicle</label>
										<select class="selectpicker" name="challan_vehicle" id="vehicle" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo "None Selected"; ?>">
											<option value="">None Selected</option>
											<?php
												foreach ($vehicle as $key => $value) {
												?>
												<!--<option value="<?php echo $value["VehicleID"]?>"><?php echo $value["VehicleID"]?></option>-->
												<?php
												}
												
											?>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="estimate">Driver </label>
										<select class="form-control selectpicker" name="driver" id="driver" aria-invalid="false">
											<option value="" >None Selected</option>
											<?php
												foreach ($DriverList as $key => $value) {
												?>
												<option value="<?php echo $value["AccountID"]?>" ><?php echo $value["firstname"]." ".$value["lastname"];?></option>
												<?php
												}
											?>
										</select>
									</div>
								</div>
								
								<div class="col-md-2">
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
								
			                <div class="col-md-2">
                                <div class="form-group">
                                    <label for="Route" class="control-label" ><small class="req text-danger"></small> Route</label>
                                    <select class="selectpicker" name="Route" id="Route" data-width="100%"  data-action-box="true"  data-hide-disabled="true" data-live-search="true" data-none-selected-text="None Selected">
										<option value=""></option>
										<?php
											foreach($routes as $key => $value) {
											?>
                                            <option value="<?php echo $value["RouteID"]?>" ><?php echo $value["name"]?></option>
											<?php
											}
										?>
										
									</select>
								</div>    
                                <?php
									//print_r($route_ids);
								?>
							</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label for="Station">Station </label>
										<select class="form-control selectpicker" name="Station" id="Station" aria-invalid="false"  data-live-search="true">
											<option value="" >None Selected</option>
											<?php
												foreach ($StationList as $key => $value) {
												?>
												<option value="<?php echo $value["id"]?>" ><?php echo $value["StationName"];?></option>
												<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-2" style="margin-top:10px;">
									<br>
									<button class="btn btn-info pull-left mleft5 search_data " id="search_data"><?php echo _l('rate_filter'); ?></button>
								</div>
							</div>
							
						</div>
						<div class="clearfix"></div>
						<br>
						<div class="row">
							<div class="col-md-9">
								
								
								<div class="custom_button">
									
									<a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 10px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
									
									
									
									<a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
								</div>
							</div>
							<div class="col-md-3">
								<input type="text" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search" title="Type in a name" style="float: right;">
								
							</div>
							<div class="col-md-12">
								<span id="searchh11" style="display:none;">please wait exporting data....</span>
							</div>
						</div>
						
						<?php
							//print_r($company_detail);
						?>
						<div class="table-daily_report">
							
							<table class="tree table table-striped table-bordered table-daily_report" id="table-daily_report" width="100%">
								
								<thead>
									<tr>
										<th class="sortable" style="text-align:left;">Sr No.</th>
										<th class="sortable" style="text-align:left;">ChallanID</th>
										<th class="sortable" style="text-align:left;">Challan Date</th>
										<th class="sortable" style="text-align:left;">ReturnID</th>
										<th class="sortable" style="text-align:left;">Return Date</th>
										<th class="sortable" style="text-align:left;">Total Time</th>
										<th class="sortable" style="text-align:left;">Vehicle No</th>
										<th class="sortable" style="text-align:left;">Driver Name</th>
										<th class="sortable" style="text-align:left;">Route</th>
										<th class="sortable" style="text-align:left;">Station</th>
										<th class="sortable" style="text-align:left;">Out Meter Reading</th>
										<th class="sortable" style="text-align:left;">In Meter Reading</th>
										<th class="sortable" style="text-align:left;">Distance Travel</th>
										<th class="sortable" style="text-align:left;">API Distance Travel</th>
										<th class="sortable" style="text-align:left;">Diesel In(Ltr)</th>
										<th class="sortable" style="text-align:left;">Standard Mileage</th>
										<th class="sortable" style="text-align:left;">Actual Mileage</th>
										<th class="sortable" style="text-align:left;">Diesel Value</th>
										<th class="sortable" style="text-align:left;">Loading Rate</th>
										<th class="sortable" style="text-align:left;">Fooding Exp.</th>
										<th class="sortable" style="text-align:left;">Toll Exp.</th>
										<th class="sortable" style="text-align:left;">Phone Exp.</th>
										<th class="sortable" style="text-align:left;">Police Exp.</th>
										<th class="sortable" style="text-align:left;">Misc. Exp.</th>
										<th class="sortable" style="text-align:left;">Repairing Exp.</th>
										<th class="sortable" style="text-align:left;">Total Expense</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>   
						</div>
						<span id="searchh2" style="display:none;">Loading.....</span>
						<div class="clearfix"></div>
						<br>
						<div class="row">
							<div class="col-md-6">
								<div class="panel_s">
									<div class="panel-body" style="max-height: 600px;">
										<div class="row">
											<div class="col-md-12">
												<figure class="highcharts-figure">
													<div id="container"></div>
												</figure>
											</div>
										</div>
										
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel_s">
									<div class="panel-body" style="max-height: 600px;">
										<div class="row">
											<div class="col-md-12">
												<figure class="highcharts-figure">
													<div id="container2"></div>
												</figure>
											</div>
										</div>
										
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel_s">
									<div class="panel-body" style="max-height: 600px;">
										<div class="row">
											<div class="col-md-12">
												<figure class="highcharts-figure">
													<div id="container3"></div>
												</figure>
											</div>
										</div>
										
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel_s">
									<div class="panel-body" style="max-height: 600px;">
										<div class="row">
											<div class="col-md-12">
												<figure class="highcharts-figure">
													<div id="container4"></div>
												</figure>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
    .table-daily_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
	.table-daily_report tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.table-daily_report table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	.table-daily_report th     { background: #50607b;color: #fff !important; }
</style>


<?php init_tail(); ?>
<!--new update -->
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>

<script>
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-daily_report");
		var tbody = table.getElementsByTagName("tbody")[0];
		var tr = tbody.getElementsByTagName("tr");
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
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		
		function load_data(from_date,to_date,vehicle,driver,fuel_type,VehicleType,Route,Station)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetMileageReport",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,vehicle:vehicle,driver:driver,fuel_type:fuel_type,VehicleType:VehicleType,Route:Route,Station:Station},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table-daily_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table-daily_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					$('tbody').html(data);
				}
			});
		}
		function load_data_chart(from_date,to_date,vehicle,driver,fuel_type,VehicleType,Route)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetMileageReportChart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,vehicle:vehicle,driver:driver,fuel_type:fuel_type,VehicleType:VehicleType,Route:Route},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table-daily_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table-daily_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					Highcharts.chart('container', {
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
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetMileageReportChartVehicleWise",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,vehicle:vehicle,driver:driver,fuel_type:fuel_type,VehicleType:VehicleType,Route:Route},
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
							text: '<b>Top Mileage By Vehicle Wise '+from_date+' To '+to_date+'</b>'
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
			
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetDriverOutTimeReportChart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,vehicle:vehicle,driver:driver,fuel_type:fuel_type,VehicleType:VehicleType,Route:Route},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table-daily_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table-daily_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					Highcharts.chart('container3', {
						chart: {
							type: 'column'
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Total Driver Out Time From '+from_date+' To '+to_date+'</b>'
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
								text: 'Hours'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Hours : <b>{point.y:.1f} </b>'
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
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetVehicleOutTimeReportChart",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,vehicle:vehicle,driver:driver,fuel_type:fuel_type,VehicleType:VehicleType,Route:Route},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table-daily_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table-daily_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					Highcharts.chart('container4', {
						chart: {
							type: 'column'
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Total Vehicle Out Time From '+from_date+' To '+to_date+'</b>'
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
							pointFormat: 'Hours : <b>{point.y:.1f} </b>'
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
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var VehicleType = $("#VehicleType").val();
			var vehicle = $("#vehicle").val();
			var driver = $("#driver").val();
			var fuel_type = $("#fuel_type").val();
			var Route = $("#Route").val();
			var Station = $("#Station").val();
			load_data(from_date,to_date,vehicle,driver,fuel_type,VehicleType,Route,Station);
			load_data_chart(from_date,to_date,vehicle,driver,fuel_type,VehicleType,Route);
			
		});
		
		
		
	});
	
	
	
</script>
<script type="text/javascript">
	function printPage(){
        
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
		var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="9">Mileage Report From : '+from_date+' To '+to_date+'</td>';
		heading_data += '</tr>';
		heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
	
	$("#caexcel").click(function(){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		var vehicle = $("#vehicle").val();
		var driver = $("#driver").val();
		var fuel_type = $("#fuel_type").val();
		var Route = $("#Route").val();
		var Station = $("#Station").val();
		var VehicleType = $("#VehicleType").val();
		// if(vehicle == '' || vehicle == null){
		// alert('Please Select Vehicle');
		// }else{
		$.ajax({
			url:"<?php echo admin_url(); ?>VehRtn/export_MileageReport",
			method:"POST",
			data:{from_date:from_date, to_date:to_date,vehicle:vehicle,driver:driver,fuel_type:fuel_type,VehicleType:VehicleType,Route:Route,Station:Station},
			beforeSend: function () {
				$('#searchh11').css('display','block');  
				
			},
			complete: function () {
				$('#searchh11').css('display','none');  
			},
			success:function(data){
				response = JSON.parse(data);
				window.location.href = response.site_url+response.filename;
			}
		});
		// }
	});
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
		
	});
	$('#VehicleType').on('change', function() {
		var VehicleType = $(this).val();
		var url = "<?php echo base_url(); ?>admin/VehRtn/GetVehiclesByType";
		$("#vehicle").find('option').remove();
		$("#vehicle").selectpicker("refresh");
		
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {VehicleType: VehicleType},
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
</script> 