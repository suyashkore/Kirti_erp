<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="row">
                <div class="col-md-8">
                    <div class="panel-body">
					<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Vehicle Return Expense</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
                        <div class="row">
                            <?php
                                $fy = $this->session->userdata('finacial_year');
                                $prefix = "VRT".$fy;
                                $next_Vehicle_rtn_number = get_option('next_vrt_number');
                                $vehicle_rtn_number = str_pad($next_Vehicle_rtn_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                                $date = date('Y-m-d');
                                $vchlDate = _d($date);
                                $date_attrs = array();
                                $date_attrs['disabled'] = true;
							?>
							<input type="hidden" name="row_count_exp" value="0" id="row_count_exp">
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="number">ReturnID</label>
                                    <div class="input-group"><span class="input-group-addon"><?php echo $prefix;?></span>
                                        <input type="text" readonly id="vehicle_return_id" name="vehicle_return_id" class="form-control" value="<?php echo $next_Vehicle_rtn_number; ?>" >
                                        <input type="hidden" id="NextVRtnID" name="NextVRtnID" class="form-control" value="<?php echo $next_Vehicle_rtn_number; ?>" >
                                        <input type="hidden" id="vehicle_return_id_hidden" name="vehicle_return_id_hidden" class="form-control"  >
									</div>
								</div>
							</div>
                            <div class="col-md-3">
                                <label class="control-label">Vhl Return Date</label>
            				    <div class="form-group" app-field-wrapper="from_date">
									<div class="input-group date">
										<input type="text" id="from_date" name="from_date" disabled class="form-control datepicker" value="<?= $vchlDate;?>" autocomplete="off"><div class="input-group-addon">
											<i class="fa fa-calendar calendar-icon"></i>
										</div>
									</div>
								</div>
							</div>
                            
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="challan_n">ChallanID</label>
                                    <input type="text" id="challan_n" name="challan_n" class="form-control" value="" readonly>
								</div>
							</div>
                            <div class="col-md-3">
                                <label class="control-label">Challan Date</label>
            				    <?php echo render_date_input('to_date','','',$date_attrs); ?>
							</div>
                            <div class="clearfix"></div>
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="vehicle_number">Vehicle No.</label>
                                    <input type="text" id="vehicle_number" name="vehicle_number" class="form-control" value="" readonly>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="vehicle_capc">Vehicle Capacity</label>
                                    <input type="text" id="vehicle_capc" name="vehicle_capc" class="form-control" value="" readonly>
								</div>
							</div>
                            
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="challan_crates">Challan Crates</label>
                                    <input type="text" id="challan_crates" name="challan_crates" class="form-control" value="" readonly>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="refund_crates">Returned Crates</label>
                                    <input type="text" id="refund_crates" name="refund_crates" class="form-control" value="" readonly>
								</div>
							</div>
                            <div class="clearfix"></div>
                            
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="driver_name">Challan Driver</label>
                                    <input type="text" id="driver_name" name="driver_name" class="form-control" value="" readonly>
									<input type="hidden" readonly="" class="form-control" name="driver_id" id="driver_id"  aria-invalid="false">
								</div>
							</div>
                            
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="loder_name">Challan Loader</label>
                                    <input type="text" id="loder_name" name="loder_name" class="form-control" value="" readonly>
									<input type="hidden" readonly="" class="form-control" name="loder_id" id="loder_id"  aria-invalid="false">
								</div>
							</div>
                            
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="salesman_name">Challan Sales Man</label>
                                    <input type="text" id="salesman_name" name="salesman_name" class="form-control" value="" readonly>
									<input type="hidden" readonly="" class="form-control" name="salesman_id" id="salesman_id"  aria-invalid="false">
									<input type="hidden"  class="form-control" name="UserIDName" id="UserIDName"  aria-invalid="false">
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
                                    <label for="total_expense1">Total Expense</label>
                                    <input type="text" id="total_expense1" name="total_expense1" class="form-control" value="" readonly>
									<input type="hidden" readonly="" class="form-control" name="total_expense" id="total_expense"  aria-invalid="false">
								</div>
							</div>
                            <div class="clearfix"></div>
							<div class="col-md-3">
								
								<?php $value =  date('d/m/Y H:m');
									// echo render_datetime_input('Act_datetime','Actual Entry DateTime',$value); 
								?>
								<div class="form-group" app-field-wrapper="Act_datetime">
									<label for="Act_datetime" class="control-label">Actual Entry DateTime</label>
									<div class="input-group date"><input type="text" id="Act_datetime" name="Act_datetime" class="form-control datetimepicker" value="<?= $value;?>"  disabled autocomplete="off">
										<div class="input-group-addon">
											<i class="fa fa-calendar calendar-icon"></i>
										</div>
									</div>
								</div>
							</div>
                            <div class="col-md-9" style="margin-top:2%;">
                                <a href="#" class="btn btn-info VrtList" id="VrtList" style="margin-right: 25px;">Show Return List</a>
                                <!--<a href="#" class="btn btn-warning chlList" id="chlList" style="margin-right: 25px;">Show Challan List</a>-->
							</div>
                            
                            
						</div>
						
						
						<div class="row col-md-12">
							
							<!-- Expenses Details -->
							<div class="" id="expense_detail" >
								<table class="table table-striped table-bordered expense_details_tbl " id="expense_details_tbl" width="70%">
									<thead id="thead">
										<tr>
											<th style="width: 125px;">AccountID</th>
											<th>AccoutName</th>
											<th>Address</th>
											<th style="width: 80px;">ExpenseAmt</th>
										</tr>
									</thead>
									<tbody id="tbody">
										<tr class="accounts" id="row">
											<td id="AccountIDTD_exp" style="width: 125px;"><input type="text" name="AccountID_exp" id="AccountID_exp" style="width: 125px;"></td>
											<td style="padding:1px 5px !important;"><span id="party_name_exp"></span><input type="hidden" name="party_name_exp_val" id="party_name_exp_val"></td>
											<td style="padding:1px 5px !important;"><span id="address_exp"></span><input type="hidden" name="address_exp_val" id="address_exp_val" value="" ></td>
											<td style="width: 80px;" class="expamts"><input type="text" name="expamt" id="expamt" value="" onblur="calculate_expense();"  style="width: 80px;text-align:right;" onkeypress="return isNumber(event);"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						
						
						<div class="row">
							<div class="col-md-12 mtop15">
								<div class="btn-bottom-toolbar text-right" style="width: 100%;">
									<?php if (has_permission_new('Vehicle_Expense', '', 'create')) {
									?>
									<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
									<?php
										}else{
									?>
									<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
									<?php
									}?>
									
									<?php if (has_permission_new('Vehicle_Expense', '', 'edit')) {
									?>
									<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
									<?php
										}else{
									?>
									<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
									<?php
									}?>
									<button type="button" class="btn btn-default cancel" id="cancel">Cancel</button>
								</div>
							</div>
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="transfer-modal_return_list">
	<div class="modal-dialog modal-xl" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Vehicle Return List</h4>
			</div>
			<div class="modal-body" style="padding:5px;">
				
				<div class="row">
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
					?>    
					<div class="col-md-2">
						<?php
							echo render_date_input('from_date2','From',$from_date);
						?>
					</div>
					<div class="col-md-2">
						<?php
							echo render_date_input('to_date2','To',$to_date);
						?>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">Expense Status</label>
							<select name="IsExpenses" id="IsExpenses" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
								<option value="N">Pending</option>
								<option value="Y">Complete</option>
								<option value="">All</option>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<br>
						<button class="btn btn-info pull-left mleft5 search_data" id="search_data_vehicle_return"><?php echo _l('rate_filter'); ?></button>
					</div>
					<div class="col-md-3">
						<!--<br>
						<input type="text" id="myInput2" onkeyup="myFunction3()" placeholder="Search for names.." title="Type in a name" style="float: right;">-->
					</div>
					<div class="col-md-12">
						<span id="searchh11" style="display:none;">Please wait fetching data...</span>
						<div class="table_vehicle_return">
							
							<table class="tree table table-striped table-bordered table_vehicle_return" id="table_vehicle_return" width="100%">
								
								<thead>
									<tr >
										<th class="sortablePop" style="padding:0px 3px !important;">ReturnNo.</th>
										<th class="sortablePop" style="padding:0px 3px !important;">ReturnDate</th>
										<th class="sortablePop" style="padding:0px 3px !important;">ChallanID.</th>
										<th class="sortablePop" style="padding:0px 3px !important;">ChallanDate</th>
										<th class="sortablePop" style="padding:0px 3px !important;">Vehicle No.</th>
										<th class="sortablePop" style=" text-align:center;">DriverName</th>
										<th class="sortablePop" style=" text-align:center;">Route</th>
										<th class="sortablePop" style=" text-align:center;">Crates</th>
										<th class="sortablePop" style=" text-align:center;">Cases</th>
										<th class="sortablePop" style=" text-align:center;">Return Crates</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>   
						</div>
						<span id="searchh3" style="display:none;">Loading.....</span>
						
					</div>
				</div>
			</div>
			<div class="modal-footer" style="padding:0px;">
				<input type="text" id="myInput2"  autofocus="1" name='myInput2' onkeyup="myFunction3()" placeholder="Search for names.."  style="float: left;width: 100%;">
			</div>
			
		</div>
	</div>
</div>

<div class="modal fade" id="transfer-modal">
	<div class="modal-dialog modal-xl" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"> Challan List</h4>
			</div>
			<div class="modal-body" style="padding:5px;">
				
				<div class="row">
					<div class="col-md-2">
						<div class="form-group" app-field-wrapper="from_date">
							<label for="from_date" class="control-label">From</label>
							
							<div class="input-group date">
								<input type="text" id="from_date1" name="from_date1" class="form-control datepicker" value="<?php echo $from_date;?>" >
								<div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-md-2">
						<div class="form-group" app-field-wrapper="to_date">
							<label for="to_date" class="control-label">To</label>
							
							<div class="input-group date">
								<input type="text" id="to_date1" name="to_date1" class="form-control datepicker" value="<?php echo $to_date;?>">
								<div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="challan_route" class="control-label" ><small class="req text-danger"> </small> Route</label>
							<select class="selectpicker" name="challan_route" id="challan_route" data-width="100%"  data-action-box="true"   data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
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
					</div>
					
					<div class="col-md-2">
						<br>
						<button class="btn btn-info pull-left mleft5 search_data" id="search_data">Search</button>
					</div>
					
					<div class="col-md-3">
						<!--<br>
						<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">-->
					</div>
					
					<div class="col-md-12">
						
						<div class="table_adj_report">
							
							<table class="tree table table-striped table-bordered table_adj_report" id="table_adj_report" width="100%">
								
								<thead>
									
									<tr>
										<th style="padding:0px 3px !important;">Challan No.</th>
										<th style="padding:0px 3px !important;">Challan Date</th>
										<th style=" text-align:center;">Route</th>
										<th style=" text-align:center;">Vehicle Type</th>
										<th style=" text-align:center;">Vehicle No</th>
										<th style=" text-align:center;">Driver Name</th>
										<th style=" text-align:center;">Crates</th>
										<th style=" text-align:center;">Cases</th>
										<th style=" text-align:center;">Challan Amt</th>
										
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>   
						</div>
						<span id="searchh2" style="display:none;">
							Loading.....
						</span>
						
					</div>
				</div>
			</div>
			
			<div class="modal-footer" style="padding:0px;">
				<input type="text" id="myInput1"  autofocus="1" name='myInput1' onkeyup="myFunction2()" placeholder="Search for names.."  style="float: left;width: 100%;">
			</div>
			
			
		</div>
	</div>
</div>

<style>
	.table_adj_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table_adj_report thead th { position: sticky; top: 0; z-index: 1; }
	.table_adj_report tbody th { position: sticky; left: 0; }
	
	.No-left {
    padding-left:0px;
	}
	.No-right {
    padding-right:0px;
	}
	#table_adj_report tr:hover {
    background-color: #ccc;
	}
	
	#table_adj_report td:hover {
    cursor: pointer;
	}
	
	.table_vehicle_return { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table_vehicle_return thead th { position: sticky; top: 0; z-index: 1; }
	.table_vehicle_return tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	th     { background: #50607b;color: #fff !important; }
	
	.fixed_header1 { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
	.fixed_header1 thead th { position: sticky; top: 0; z-index: 1; }
	.fixed_header1 tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.fixed_header1 table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 0px 0px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	.fixed_header1 th     { background: #50607b;color: #fff !important; }
	
	
	#table_vehicle_return tr:hover {
    background-color: #ccc;
	}
	
	#table_vehicle_return td:hover {
    cursor: pointer;
	}
</style>
<style>
    table.dataTable tbody td {
    padding: 4px 4px !important;
    font-size: 11px;
	}
</style>
<?php init_tail(); ?>
<script>
	$("#search_data_vehicle_return").click(function(){ 
        var from_date = $('#from_date2').val();
        var to_date = $('#to_date2').val();
        var IsExpenses = $('#IsExpenses').val();
		
        $.ajax({
			url:"<?php echo admin_url(); ?>VehRtn/vehicle_return_model_expenses",
			dataType:"html",
			method:"POST",
			data:{from_date:from_date,to_date:to_date,IsExpenses:IsExpenses},
			beforeSend: function () {
				
				$('#searchh3').css('display','block');
				$('.table_vehicle_return tbody').css('display','none');
				
			},
			complete: function () {
				
				$('.table_vehicle_return tbody').css('display','');
				$('#searchh3').css('display','none');
			},
			success:function(data){
				$('#table_vehicle_return tbody').html(data);
                $('.get_VehicleRtnID').on('click',function(){ 
                    VRtnID = $(this).attr("data-id");
                    $('#transfer-modal_return_list').modal('hide');
                    $.ajax({
						url:"<?php echo admin_url(); ?>VehRtn/GetDetail",
						dataType:"JSON",
						method:"POST",
						data:{VRtnID:VRtnID},
						beforeSend: function () {
							$('#searchh11').css('display','block');
							$('#searchh11').css('color','blue');
						},
						complete: function () {
							$('#searchh11').css('display','none');
						},
						success:function(response){
							
							let VRtnDetails = response.ChallanDetails;
							let CratesDetails = response.CratesDetails;
							//alert(response.ReturnID);
							$('#vehicle_return_id').val(VRtnDetails.ReturnID.slice(-6));
							$('#vehicle_return_id_hidden').val(VRtnDetails.ReturnID);
							$('#challan_n').val(VRtnDetails.ChallanID);
							$('#route_code').val(VRtnDetails.RouteID);
							$('#route_name').val(VRtnDetails.name);
							$('#routekm').val(VRtnDetails.KM);
							$('#vehicle_number').val(VRtnDetails.VehicleID);
							$('#driver_id').val(VRtnDetails.DriverID);
							if(VRtnDetails.driver_fn !== null){
								var DName = VRtnDetails.driver_fn +' '+VRtnDetails.driver_ln;
								}else{
								var DName = '';
							}
							$('#driver_name').val(DName);
							if(VRtnDetails.loader_fn !== null){
								var LName = VRtnDetails.loader_fn +' '+VRtnDetails.loader_ln;
								}else{
								var LName = '';
							}
							$('#loder_id').val(VRtnDetails.LoaderID);
							$('#loder_name').val(LName);
							$('#salesman_id').val(VRtnDetails.SalesmanID);
							if(VRtnDetails.Salesman_fn !== null){
								var SName = VRtnDetails.Salesman_fn +' '+VRtnDetails.Salesman_ln;
								}else{
								var SName = '';
							}
							$('#salesman_name').val(SName);
							$('#vehicle_capc').val(VRtnDetails.VehicleCapacity);
							$('#challan_crates').val(VRtnDetails.Crates);
							$('#refund_crates').val(VRtnDetails.return_crates);
							var ChlDate = VRtnDetails.Transdate.substring(0, 10);
							var ChldateNew = ChlDate.split("-").reverse().join("/");
							$('#to_date').val(ChldateNew);
							
							var VRtnDate = VRtnDetails.returnTransdate.substring(0, 10);
							var VRtndateNew = VRtnDate.split("-").reverse().join("/");
							$('#from_date').val(VRtndateNew);
							// $('#challan_n').attr('readonly', true);
							
							var Act_entry_date = VRtnDetails.Act_entry_datetime.substring(0, 10);
							var Act_entry_time = VRtnDetails.Act_entry_datetime.substring(10, 16);
							var Act_date = Act_entry_date.split("-").reverse().join("/");
							$('#Act_datetime').val(Act_date+Act_entry_time);
							
							var TotalRow = $("#row_count_exp").val();
							var crRow = parseInt(TotalRow);
							
							for (var A = 1; A <= crRow; A++) {
								var id = 'row_exp'+A;
								document.getElementById(id).remove();
							}
							
							$("#row_count_exp").val('0');
							
							// Expenses table
							let ExpenseDetails = response.ExpenseDetails;
							var iii = 1;
							var html4 = '';
							var TotalExpAmt = 0;
							$.each(ExpenseDetails, function (column8, value8) {
								if(value8['expense_Amount'] == null ){
									
									}else{
									html4 += '<tr class="accounts" id="row_exp'+iii+'">';
									html4 += '<td style="width: 125px;" id="AccountIDTD_exp'+iii+'"><input type="text" name="AccountID_exp'+iii+'" style="width: 125px;" id="AccountID_exp'+iii+'" value="'+value8['Aid']+'"></td>';
									if(value8['firstname'] == null){
										var Name = value8['company'];
										if(value8['address'] == null){
											var Address = '';
											}else{
											var Address = value8['address'];
										}
										}else{
										var Name = value8['firstname']+' '+value8['lastname'];
										if(value8['address'] == null){
											var Address = '';
											}else{
											var Address = value8['current_address'];
										}
									}
									html4 += '<td style="padding:1px 5px !important;">'+Name+'</td>';
									html4 += '<td style="padding:1px 5px !important;">'+Address+'</td>';
									html4 += '<td class="expamts" style="width: 80px;"><input type="text" name="expamt'+iii+'" id="expamt'+iii+'" onblur="calculate_expense();"  value="'+value8['expense_Amount']+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
									html4 += '</tr>';
									
									TotalExpAmt += parseFloat(value8['expense_Amount']);
									
									iii++;
								}
							})
							
							$('#total_expense1').val(parseFloat(TotalExpAmt).toFixed(2));
							$('#total_expense').val(parseFloat(TotalExpAmt).toFixed(2));
							$('#expense_details_tbl tbody').append(html4);
							$("#row_count_exp").val(iii-1);
							
                            $('.saveBtn').hide();
                            $('.updateBtn').show();
                            $('.saveBtn2').hide();
                            $('.updateBtn2').show();
                            $('.printBtn').show();
						}
					});
				});
			}
		});
	});
	
    $('.get_challan_id').on('click',function(){ 
		
        $('#transfer-modal').modal('hide');
		$('#cancel').click();
        challan_id = $(this).attr("data-id");
        myFunction(challan_id);
	});
    
	
	function myFunction(challan_id) { 
        $.ajax({
			url:"<?php echo admin_url(); ?>VehRtn/unique_challan_details",
			dataType:"JSON",
			method:"POST",
			data:{challan_id:challan_id},
			
			success:function(data){
				$('#challan_n').val(data.ChallanID);
				$('#route_code').val(data.RouteID);
				$('#route_name').val(data.name);
				$('#routekm').val(data.KM);
				$('#vehicle_number').val(data.VehicleID);
				$('#driver_id').val(data.DriverID);
				$('#driver_name').val(data.driver_fn);
				$('#loder_id').val(data.LoaderID);
				$('#loder_name').val(data.loader_fn);
				$('#salesman_id').val(data.SalesmanID);
				$('#salesman_name').val(data.Salesman_fn);
				$('#challan_crates').val(data.Crates);
				$('#vehicle_capc').val(data.VehicleCapacity);
				var ChallanDate = data.Transdate.substring(0, 10);
                var ChallanDateNew = ChallanDate.split("-").reverse().join("/");
                $('#to_date').val(ChallanDateNew);
				$('#case_depo').val(0.00);
				$('#case_depo1').val(0.00);
				$('#check_depo').val(0.00);
				$('#total_expense').val(0.00);
				$('#total_expense1').val(0.00);
				$('#fresh_ret_amt').val(0.00);
				$('#fresh_ret_amt1').val(0.00);
				$('#NERT_trans').val(0.00);
				
				var TotalRow = $("#row_count_exp").val();
				var crRow = parseInt(TotalRow);
				
				for (var A = 1; A <= crRow; A++) {
					var id = 'row_exp'+A;
					document.getElementById(id).remove();
				}
				
				$("#row_count_exp").val('0');
			}
		});
	}
	
	// Expense Amt calculation
    function calculate_expense(){
        var p = $(".table.expense_details_tbl tbody tr.accounts");
        var totalExpense = 0;
        $.each(p, function() {
            
            var expAmt = $(this).find("td.expamts input[type='text']").val();
            var AccountID = $(this).find("td#AccountIDTD_exp input[type='text']").val();
            if(expAmt !== "" && AccountID !== ""){
                totalExpense = parseInt(totalExpense) + parseInt(expAmt);
			}
		})
        $("#total_expense").val(totalExpense.toFixed(2));
        $("#total_expense1").val(totalExpense.toFixed(2));
	}
	
	$(document).ready(function(){
		$('.updateBtn').hide();
        $('.updateBtn2').hide();
		
		$("#VrtList").click(function(){
            $('#transfer-modal_return_list').find('button[type="submit"]').prop('disabled', false);
			$('#transfer-modal_return_list').modal('show');
			$('#transfer-modal_return_list').on('shown.bs.modal', function () {
				$('#myInput2').focus();
			})
		});
		
		$("#chlList").click(function(){
			$('#transfer-modal').modal('show');
			$('#transfer-modal').on('shown.bs.modal', function () {
				$('#myInput1').focus();
			})
		})
		
		$("#search_data").click(function(){ 
			var from_date = $('#from_date1').val();
			var to_date = $('#to_date1').val();
			var challan_route = $('#challan_route').val();
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/challan_details_model_new",
				dataType:"html",
				method:"POST",
				data:{from_date:from_date,to_date:to_date,challan_route:challan_route},
				beforeSend: function () {
					$('#searchh2').css('display','block');
					$('.table_adj_report tbody').css('display','none');
				},
				complete: function () {
					$('.table_adj_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					$('#table_adj_report tbody').html(data);
					$('.get_challan_id').on('click',function(){ 
						$('#transfer-modal').modal('hide');
						$('#cancel').click();
						challan_id = $(this).attr("data-id");
						myFunction(challan_id);
					});
				}
			});
		});
	});
	
	// For Expenses
    // Initialize For Account
	$("#AccountID_exp").autocomplete({
        
        source: function( request, response ) {
			
			$.ajax({
				url: "<?=base_url()?>admin/VehRtn/staffaccountlist",
				type: 'post',
				dataType: "json",
				data: {
					search: request.term
				},
				success: function( data ) {
					response( data );
				}
			});
		},
        select: function (event, ui) {
			
			//alert(Conform);
			var ChallanID = $('#challan_n').val();
			if(empty(ChallanID)){
				alert('please select challanID');
				$("#AccountID_exp").focus();
				return false;
				}else{
                $('#AccountID_exp').val(ui.item.value);
                $('#party_name_exp').html(ui.item.label);
                $('#party_name_exp_val').val(ui.item.label);
                $('#address_exp').html(ui.item.address);
                $('#address_exp_val').val(ui.item.address);
                $("#expamt").focus();
                return false;
			}
		}
	});
	$('#AccountID_exp').on('blur', function () {
		var AccountID = $(this).val();
		if(empty(AccountID)){
			
            }else{
			$.ajax({
				url: "<?=base_url()?>admin/VehRtn/get_staffAccount_Details",
				type: 'post',
				dataType: "json",
				data: {
					AccountID: AccountID,
				},
				success: function( data ) {
					if(empty(data)){
						alert('AccountID not found.');
						$("#AccountID_exp").val('');
						$("#AccountID_exp").focus();
                        }else{
						if(data.company == null){
							var fullname = data.firstname+' '+data.lastname;
							var address = data.current_address;
                            }else{
							var fullname = data.company;
							var address = data.address;
						}
						$('#AccountID_exp').val(data.AccountID); // display the selected text
						$('#party_name_exp_val').val(fullname); // display the selected text
						$('#party_name_exp').html(fullname); // display the selected text
						$('#address_exp_val').val(address); // display the selected text
						$('#address_exp').html(address); // display the selected text
						$("#expamt").focus();
					}
				}
			});
		}
	});
	$('#AccountID_exp').on('focus', function () {
		$('#AccountID_exp').val(''); 
		$('#party_name_exp_val').val('');
		$('#party_name_exp').html(''); 
		$('#address_exp_val').val(''); 
		$('#address_exp').html(''); 
	})
	// For Expense Details
    $('#expamt').on('blur', function () {
        
        var AccountID_exp =document.getElementById("AccountID_exp").value;
        var party_name_exp_val =document.getElementById("party_name_exp_val").value;
        var address_exp_val =document.getElementById("address_exp_val").value;
        var expamt =document.getElementById("expamt").value;
        
        var table=document.getElementById("expense_details_tbl");
        
        var table_len=(table.rows.length) - 1;
        var html = '';
        
        if(AccountID_exp == "" || AccountID_exp == null){
            alert('please add AccountID');
            $('#AccountID_exp').val('');
            $('#AccountID_exp').focus();
			}if(expamt == "" || expamt == '0'){
            alert('please add amount');
            $('#expamt').val('');
			}else{
            var count = $("#row_count_exp").val();
            var new_count = parseInt(count) + 1;
            $("#row_count_exp").val(new_count);
            html += '<tr class="accounts" id="row_exp'+table_len+'">';
            html += '<td><input type="text" name="AccountID_exp'+table_len+'" style="width: 125px;" id="AccountID_exp'+table_len+'" value="'+AccountID_exp+'"></td>';
            html += '<td style="padding:1px 5px !important;">'+party_name_exp_val+'</td>';
            html += '<td style="padding:1px 5px !important;">'+address_exp_val+'</td>';
            
            html += '<td class="expamts"><input type="text" name="expamt'+table_len+'" id="expamt'+table_len+'"  onblur="calculate_expense();" value="'+expamt+'" style="width: 80px;text-align:right;" onkeypress="return isNumber(event)"></td>';
            html += '</tr>';
            var row = table.insertRow(table_len).outerHTML=html;
            
            document.getElementById("AccountID_exp").value="";
            document.getElementById("party_name_exp_val").value="";
            document.getElementById("address_exp_val").value="";
            document.getElementById("expamt").value="";
            
            document.getElementById("party_name_exp").innerHTML="";
            document.getElementById("address_exp").innerHTML ="";
            
		}
	});
	
	
	// Focus On VehicleRtnID
	$('#cancel').on('click',function(){
		//alert('hello');
		var NextVRtnID = $("#NextVRtnID").val();
		$("#vehicle_return_id").val(NextVRtnID);
		$("#vehicle_return_id_hidden").val('');
		$("#challan_n").val('');
		$("#route_code").val('');
		$("#route_name").val('');
		$("#routekm").val('0.00');
		$("#vehicle_number").val('');
		$("#vehicle_capc").val('0.00');
		$("#driver_id").val('');
		$("#driver_name").val('');
		$("#loder_id").val('');
		$("#loder_name").val('');
		$("#salesman_id").val('');
		$("#salesman_name").val('');
		$("#challan_crates").val('0.00');
		$("#refund_crates").val('0.00');
		$("#fresh_ret_amt1").val('0.00');
		$("#case_depo1").val('0.00');
		$("#check_depo").val('0.00');
		$("#NERT_trans").val('0.00');
		$("#total_expense").val('0.00');
		$("#total_expense1").val('0.00');
		// $('#challan_n').attr('readonly', false);
		
		var TotalRow = $("#row_count_exp").val();
		var crRow = parseInt(TotalRow);
		
		for (var A = 1; A <= crRow; A++) {
			var id = 'row_exp'+A;
			document.getElementById(id).remove();
		}
		
		$("#row_count_exp").val('0');
		
		$('.saveBtn').show();
		$('.saveBtn2').show();
		$('.updateBtn').hide();
		$('.updateBtn2').hide();
		$('.printBtn').hide();
	});
	
	
	// Save New Item
	$('.saveBtn').on('click',function(){ 
		
		// Ganeral Data
        var refund_crates = $("#refund_crates").val();
        var from_date = $("#from_date").val();
        var challan_n = $("#challan_n").val();
        var vehicle_number = $("#vehicle_number").val();
        var Act_datetime = $("#Act_datetime").val();
        if(challan_n !== ''){
			// Expenses Details
            var ExpCount = $("#row_count_exp").val();
            var ExpArray = new Array();
            for (i=1;i<=ExpCount;i++) {
                var id= 'AccountID_exp'+i;
                var ExpAccountID = document.getElementById(id).value;
                var id2= 'expamt'+i;
                var ExpAmt = document.getElementById(id2).value;
                var ii = i - 1;
				ExpArray[ii]=new Array();
				ExpArray[ii][0]=ExpAccountID;
				ExpArray[ii][1]=ExpAmt;
			}
			var ExpSerializedArr = JSON.stringify(ExpArray);
            
			
            $.ajax({
                url:"<?php echo admin_url(); ?>VehRtn/SaveVehRtnExpense",
                dataType:"JSON",
                method:"POST",
                data:{refund_crates:refund_crates,from_date:from_date,challan_n:challan_n,ExpSerializedArr:ExpSerializedArr,vehicle_number:vehicle_number,
					ExpCount:ExpCount,Act_datetime:Act_datetime
				},
                beforeSend: function () {
					$('.searchh3').css('display','block');
					$('.searchh3').css('color','blue');
				},
                complete: function () {
					$('.searchh3').css('display','none');
				},
                success:function(data){
                    if(data == false){
                        
						}else if(data == 'Created'){
                        alert_float('warning', 'VehRtn Already created for this challan...');
						$('#vehicle_return_id').val(data);
						$("#NextVRtnID").val(data);
						$("#vehicle_return_id_hidden").val('');
						$("#challan_n").val('');
						$("#route_code").val('');
						$("#route_name").val('');
						$("#routekm").val('0.00');
						$("#vehicle_number").val('');
						$("#vehicle_capc").val('0.00');
						$("#driver_id").val('');
						$("#driver_name").val('');
						$("#loder_id").val('');
						$("#loder_name").val('');
						$("#salesman_id").val('');
						$("#salesman_name").val('');
						$("#challan_crates").val('0.00');
						$("#refund_crates").val('0.00');
						$("#fresh_ret_amt1").val('0.00');
						$("#case_depo1").val('0.00');
						$("#check_depo").val('0.00');
						$("#NERT_trans").val('0.00');
						$("#total_expense").val('0.00');
						$("#total_expense1").val('0.00');
						// Expenses
						var TotalRow = $("#row_count_exp").val();
                        //var crRow = parseInt(TotalRow) - 1;
						for (var A = 1; A <= TotalRow; A++) {
							var id = 'row_exp'+A;
                            document.getElementById(id).remove();
						}
                        $("#row_count_exp").val('0');
						
						$("#search_data_vehicle_return").click();
						$("#search_data").click();
						
						$('.saveBtn').show();
						$('.saveBtn2').show();
						$('.updateBtn').hide();
						$('.updateBtn2').hide();
						}else{
						alert_float('success', 'Record created successfully...');
						$('#vehicle_return_id').val(data);
						$("#NextVRtnID").val(data);
						$("#vehicle_return_id_hidden").val('');
						$("#challan_n").val('');
						$("#route_code").val('');
						$("#route_name").val('');
						$("#routekm").val('0.00');
						$("#vehicle_number").val('');
						$("#vehicle_capc").val('0.00');
						$("#driver_id").val('');
						$("#driver_name").val('');
						$("#loder_id").val('');
						$("#loder_name").val('');
						$("#salesman_id").val('');
						$("#salesman_name").val('');
						$("#challan_crates").val('0.00');
						$("#refund_crates").val('0.00');
						$("#fresh_ret_amt1").val('0.00');
						$("#case_depo1").val('0.00');
						$("#check_depo").val('0.00');
						$("#NERT_trans").val('0.00');
						$("#total_expense").val('0.00');
						$("#total_expense1").val('0.00');
						
						// Expenses
						var TotalRow = $("#row_count_exp").val();
                        //var crRow = parseInt(TotalRow) - 1;
						for (var A = 1; A <= TotalRow; A++) {
							var id = 'row_exp'+A;
                            document.getElementById(id).remove();
						}
                        $("#row_count_exp").val('0');
						
						$("#search_data_vehicle_return").click();
						$("#search_data").click();
						
						$('.saveBtn').show();
						$('.saveBtn2').show();
						$('.updateBtn').hide();
						$('.updateBtn2').hide();
					}
					
				}
			});
			}else{
            alert('please select Challan...');
            $('#challan_n').focus();
		}
		
	});
	
	// Update Exiting VRtnID
	$('.updateBtn').on('click',function(){ 
		
		// Ganeral Data
        var refund_crates = $("#refund_crates").val();
        var VRtnID = $("#vehicle_return_id_hidden").val();
        var from_date = $("#from_date").val();
        var challan_n = $("#challan_n").val();
        var vehicle_number = $("#vehicle_number").val();
        var Act_datetime = $("#Act_datetime").val();
        if(challan_n !== ''){
			// Expenses Details
            var ExpCount = $("#row_count_exp").val();
            var ExpArray = new Array();
            for (i=1;i<=ExpCount;i++) {
                var id = 'AccountID_exp'+i;
                var ExpAccountID = document.getElementById(id).value;
                var id2 = 'expamt'+i;
                var ExpAmt = document.getElementById(id2).value;
                var ii = i - 1;
				ExpArray[ii]=new Array();
				ExpArray[ii][0]=ExpAccountID;
				ExpArray[ii][1]=ExpAmt;
			}
			var ExpSerializedArr = JSON.stringify(ExpArray);
            
			
            $.ajax({
                url:"<?php echo admin_url(); ?>VehRtn/UpdateVehRtnExpense",
                dataType:"JSON",
                method:"POST",
                data:{refund_crates:refund_crates,from_date:from_date,challan_n:challan_n,ExpSerializedArr:ExpSerializedArr,vehicle_number:vehicle_number,
					ExpCount:ExpCount,VRtnID:VRtnID,Act_datetime:Act_datetime
				},
                beforeSend: function () {
					$('.searchh4').css('display','block');
					$('.searchh4').css('color','blue');
				},
                complete: function () {
					$('.searchh4').css('display','none');
				},
                success:function(data){
                    if(data == false){
                        
						}else{
						alert_float('success', 'Record updated successfully...');
						$('#vehicle_return_id').val(data);
						$("#NextVRtnID").val(data);
						$("#challan_n").val('');
						$("#route_code").val('');
						$("#route_name").val('');
						$("#routekm").val('0.00');
						$("#vehicle_number").val('');
						$("#vehicle_capc").val('0.00');
						$("#driver_id").val('');
						$("#driver_name").val('');
						$("#loder_id").val('');
						$("#loder_name").val('');
						$("#salesman_id").val('');
						$("#salesman_name").val('');
						$("#challan_crates").val('0.00');
						$("#refund_crates").val('0.00');
						$("#fresh_ret_amt1").val('0.00');
						$("#case_depo1").val('0.00');
						$("#check_depo").val('0.00');
						$("#NERT_trans").val('0.00');
						$("#total_expense").val('0.00');
						$("#total_expense1").val('0.00');
						// Expenses
						var TotalRow = $("#row_count_exp").val();
                        //var crRow = parseInt(TotalRow) - 1;
						for (var A = 1; A <= TotalRow; A++) {
							var id = 'row_exp'+A;
                            document.getElementById(id).remove();
						}
                        $("#row_count_exp").val('0');
						
						$("#search_data_vehicle_return").click();
						$("#search_data").click();
						
						$('.saveBtn').show();
						$('.saveBtn2').show();
						$('.updateBtn').hide();
						$('.updateBtn2').hide();
					}
					
				}
			});
		}
        
	});
	
	function myFunction3() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput2");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_vehicle_return");
		tbody = table.getElementsByTagName("tbody")[0]; // Get the first tbody element
		tr = tbody.getElementsByTagName("tr"); 
		for (i = 0; i < tr.length; i++) 
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
	
	function isNumber(evt) {
        
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_adj_report");
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
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_vehicle_return tbody");
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