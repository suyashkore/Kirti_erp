<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .tableFixHead2          { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
	.tableFixHead2 tbody th { position: sticky; left: 0; }
	
	
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
    color: #fff !important; }
    
	
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<div class="row">
								<!--<div class="col-md-4 text-center">
								</div>-->
								<div class="col-md-12 text-centerr"  >
									<nav aria-label="breadcrumb" >
										<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
											<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
											<li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
											<li class="breadcrumb-item active" aria-current="page"><b>Vehicle Master</b></li>
											
										</ol>
									</nav>
									<hr style="margin-Bottom:12px !important;">
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<?php hooks()->do_action('before_items_page_content'); ?>
						<?php if(has_permission_new('vehiclemaster','','create')){ ?>
							<div class="_buttons">
								<a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#vehicle_modal">Add Vehicle</a>
							</div>
							<div class="clearfix"></div>
							<hr class="hr-panel-heading" />
						<?php } ?>
						<div class="row">
							
							<div class="col-md-6">
								<div class="custom_button">
									<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
									<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
									<!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
								</div>
							</div>
							<div class="col-md-6">
								<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
							</div>
							
							<div class="col-md-12">
								<div class="tableFixHead2">
									<table class="table table-striped table-bordered tableFixHead2" width="100%" id="user_list">
										<thead>
											<tr>
												<th class="sortablePop">Vehicle RegNo</th>
												<th class="sortablePop">Vehicle Type</th>
												<th class="sortablePop">Vehicle Capacity</th>
												<th class="sortablePop">Driver Name</th>
												<th class="sortablePop">Brand</th>
												<th class="sortablePop">Model</th>
												<th class="sortablePop">Fuel Type</th>
												<th class="sortablePop">Fuel Capacity</th>
												<th class="sortablePop">Mileage</th>
												<th class="sortablePop">Excel Type</th>
												<th class="sortablePop">Fitness Expiry Date</th>
												<th class="sortablePop">Pollution Expiry Date</th>
												<th class="sortablePop">Insurance No.</th>
												<th class="sortablePop">Start Day</th>
												<th class="sortablePop">Status</th>
												<th class="hide_in_print">Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												foreach ($vehicle_data as $key => $value) {
												?>
												<tr>
													<td><?php echo $value["VehicleID"];?></td>
													<td><?php 
														if($value["VehicleTypeID"] == "0"){
															echo "Own";
														}elseif($value["VehicleTypeID"] == "1"){
															echo "Transport";
														}elseif($value["VehicleTypeID"] == "2"){
															echo "Rental";
														}else{
															echo "";
														}
													?></td>
													<td><?php echo $value["VehicleCapacity"];?></td>
													<td><?php echo $value["firstname"]." ".$value["lastname"];?></td>
													<td><?php echo $value["brand"];?></td>
													<td><?php echo $value["model"];?></td>
													<td><?php echo $value["fuel_type"];?></td>
													<td><?php echo $value["fuel_capacity"];?></td>
													<td><?php echo $value["mileage"];?></td>
													<td><?php echo $value["excel_type"];?></td>
													<td><?php echo substr(_d($value['fitness_exp_date']),0,10);?></td>
													<td><?php echo substr(_d($value['pollution_exp_date']),0,10);?></td>
													<td><?php echo $value["insuranceno"];?></td>
													<td><?php echo substr(_d($value['StartDate']),0,10);?></td>
													<?php if($value['ActiveYN'] == "1"){
														$status = "Available";
														}elseif($value['ActiveYN'] == "0"){
														$status = "Deactive";
														} elseif($value['ActiveYN'] == "2"){
														$status = "In-Maintenance";
														} elseif($value['ActiveYN'] == "3"){
														$status = "Legal";
														}  else{
														$status = "";
													} 
													?>
													<td><?php echo $status;?></td>
													<?php  if (has_permission_new('vehiclemaster', '', 'edit')) {
														$action = '<a href="#" data-toggle="modal" data-target="#vehicle_modal" data-id="' . $value['VehicleID'] . '"><i class="fa fa-pencil"></i></a>';
													}
													if (has_permission_new('vehiclemaster', '', 'delete')) {
														//$action .= '  <a href="' . admin_url('vehicles/delete/' . $value['VehicleID']) . '" class="text-danger _delete"><i class="fa fa-trash"></i></a>';
													}?>
													<td class="hide_in_print"><?php echo $action;?></td>
												</tr>
												<?php
												}
											?>
										</tbody>
									</table>
								</div>
								<span id="searchh3" style="display:none;">Please wait data exporting.....</span>
							</div>
							
						</div>
						<?php
							/*$table_data = [];
								
								$table_data = array_merge($table_data, array(
								'Vehicle RegNo',
								"Vehicle Type",
								"vehicle Capacity",
								"Start Day",
								'Active'
								));
								
								$cf = get_custom_fields('items',array('show_on_table'=>1));
								foreach($cf as $custom_field) {
								array_push($table_data,$custom_field['name']);
								}
								$table_data = array_merge($table_data, array(
								
								'Action'
								));
							render_datatable($table_data,'vehicle-table'); */
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('admin/vehicle/add_model'); ?>





<?php init_tail(); ?>
<script>
	$(function(){
		
		var notSortableAndSearchableItemColumns = [];
		<?php if(has_permission_new('vehiclemaster','','delete')){ ?>
			//notSortableAndSearchableItemColumns.push(0);
		<?php } ?>
		
		initDataTable('.table-vehicle-table', admin_url+'vehicles/table', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,'undefined',[0,'DESC']);
		
		
		
		
	});
	
</script>
<script>
    
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("user_list");
		tr = table.getElementsByTagName("tr");
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else {
					tr[i].style.display = "none";
				}
			}       
		}
	}
	
	function isNumber2(evt) {
		evt = evt || window.event;
		var charCode = evt.which || evt.keyCode;
		
		// Allow decimal point (charCode 46) and numbers (charCodes 48 to 57)
		if (charCode !== 46 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		
		return true;
	}
	
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	}
	
	
	function isCharacterOrNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		
		// Allow backspace or tab keys
		if (charCode === 8 || charCode === 9 || charCode === 32) {
			return true;
		}
		
		// Check if the character is a letter (A-Z, a-z) or number (0-9)
		if ((charCode >= 48 && charCode <= 57) || // numbers 0-9
        (charCode >= 65 && charCode <= 90) || // uppercase letters A-Z
        (charCode >= 97 && charCode <= 122)) { // lowercase letters a-z
			return true;
		}
		
		return false;
	}
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#user_list tbody");
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
	
</script>

<script>
	$("#caexcel").click(function(){
		var data_val = "data";
		$.ajax({
			url:"<?php echo admin_url(); ?>Vehicles/export_VehicleMaster",
			method:"POST",
			data:{data_val:data_val,},
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
	
	
</script>
<script type="text/javascript">
	function printPage(){
        
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .hide_in_print{ display:none; }</style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="6"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="6"><?php echo $company_detail->address; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td colspan="6" style="text-align:center;">Vehicles details </td>';
		heading_data += '</tr>';
		heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
</script>
<style type="text/css">
	body{
    overflow: hidden;
	}
</style>
</body>
</html>
