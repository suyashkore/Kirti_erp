<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-10">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Creating new Entry...</div>
								<div class="searchh4" style="display:none;">Please wait updating Entry...</div>
							</div>
							<br>
							<?php
								$returndate = $EntryData->Act_entry_datetime;
								$gatepasstime = $EntryData->gatepasstime;
								$returndateTime = new DateTime($returndate);
								$gatepassTime = new DateTime($gatepasstime);
								
								// Calculate the difference
								$interval = $returndateTime->diff($gatepassTime);
								
								// Get the total difference in hours and minutes
								$hours = $interval->days * 24 + $interval->h; // Total hours including days
								$minutes = $interval->i; // Minutes
								$Duration = $hours.":".$minutes;
							?>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="VehRtnNo">
									<label for="VehRtnNo" class="control-label">Vehicle Return No. </label>
									<input type="text" id="VehRtnNo" name="VehRtnNo" class="form-control" value="<?= $EntryData->ReturnID?>" readonly>
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="VehRtnDate">
									<label for="VehRtnNo" class="control-label">Vehicle Retrun Datetime</label>
									<input type="text" id="VehRtnNo" name="VehRtnNo" class="form-control" value="<?= substr(_d($EntryData->Act_entry_datetime),0,19)?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="ChallanNo">
									<label for="ChallanNo" class="control-label">Challan No</label>
									<input type="text" id="ChallanNo" name="ChallanNo" class="form-control" value="<?= $EntryData->ChallanID?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="ChallanDate">
									<label for="ChallanDate" class="control-label">Challan Date</label>
									<input type="text" id="ChallanDate" name="ChallanDate" class="form-control" value="<?= substr(_d($EntryData->Challandate),0,19)?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="DriverName">
									<label for="DriverName" class="control-label">Driver Name</label>
									<input type="text" id="DriverName" name="DriverName" class="form-control" value="<?= $EntryData->Driver?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="VehicleNo">
									<label for="VehicleNo" class="control-label">Vehicle No</label>
									<input type="text" id="VehicleNo" name="VehicleNo" class="form-control" value="<?= $EntryData->VehicleID?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Route">
									<label for="Route" class="control-label">Route</label>
									<input type="text" id="Route" name="Route" class="form-control" value="<?= $EntryData->routename?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="no_of_drop">
									<label for="no_of_drop" class="control-label">No of Drop Points</label>
									<input type="text" id="no_of_drop" name="no_of_drop" onkeypress="return isNumber(event)" class="form-control" value="<?= $EntryData->Total_Drop?>"  readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="out_meter_reading">
									<label for="out_meter_reading" class="control-label">Out Meter Reading</label>
									<input type="text" onblur="GetDistantance(),GetMileage()" onkeypress="return isNumber(event)"  id="out_meter_reading" name="out_meter_reading" class="form-control" value="<?= $EntryDetail->out_meter_reading?>">
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="in_meter_reading">
									<label for="in_meter_reading" class="control-label">In Meter Reading</label>
									<input type="text" onblur="GetDistantance(),GetMileage()" onkeypress="return isNumber(event)" id="in_meter_reading" name="in_meter_reading" class="form-control" value="<?= $EntryDetail->in_meter_reading?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="DeliveryTime">
									<label for="DeliveryTime" class="control-label">Delivery Time(HH:MM)</label>
									<input type="text" id="DeliveryTime" name="DeliveryTime" class="form-control" value="<?= $Duration?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="DistanceTravel">
									<label for="DistanceTravel" class="control-label">Distance Travel</label>
									<input type="text" id="DistanceTravel" onblur="GetMileage()" onkeypress="return isNumber(event)" name="DistanceTravel" class="form-control" value="<?= $EntryDetail->DistanceTravel?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Diesel">
									<label for="Diesel" class="control-label">Diesel in Ltr</label>
									<input type="text" id="Diesel" name="Diesel" onblur="GetMileage()" class="form-control" value="<?= $EntryDetail->Diesel?>" >
								</div>
							</div>
							
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Diesel_value">
									<label for="Diesel_value" class="control-label">Diesel Value </label>
									<input type="text" id="Diesel_value" name="Diesel_value" class="form-control" value="<?= $EntryDetail->Diesel_value?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Fooding">
									
									<?php $value = $EntryDetail->Fooding ? $EntryDetail->Fooding : 0;?>
									<label for="Fooding" class="control-label">Fooding Expense</label>
									<input type="text" id="Fooding" name="Fooding" class="form-control" value="<?= $value?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Toll">
									<?php $value = $EntryDetail->Toll ? $EntryDetail->Toll : 0;?>
									<label for="Toll" class="control-label">Toll Expense</label>
									<input type="text" id="Toll" name="Toll" class="form-control" value="<?= $value?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Phone">
									<?php $value = $EntryDetail->Phone ? $EntryDetail->Phone : 0;?>
									<label for="Phone" class="control-label">Phone Expense</label>
									<input type="text" id="Phone" name="Phone" class="form-control" value="<?= $value?>" maxlength="10" minlength="10">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Police">
									<?php $value = $EntryDetail->Police ? $EntryDetail->Police : 0;?>
									<label for="Police" class="control-label">Police Expense</label>
									<input type="text" id="Police" name="Police" class="form-control" value="<?= $value?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Misc_Expense">
									<?php $value = $EntryDetail->Misc_Expense ? $EntryDetail->Misc_Expense : 0;?>
									<label for="Misc_Expense" class="control-label">Misc. Expense</label>
									<input type="text" id="Misc_Expense" name="Misc_Expense" class="form-control" value="<?= $value?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Expense_repairing">
									<?php $value = $EntryDetail->Expense_repairing ? $EntryDetail->Expense_repairing : 0;?>
									<label for="Expense_repairing" class="control-label">Expense for repairing</label>
									<input type="text" id="Expense_repairing" name="Expense_repairing" class="form-control" value="<?= $value?>" >
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Standard_Mileage">
									<label for="Standard_Mileage" class="control-label">Standard Mileage</label>
									<input type="text" id="Standard_Mileage" name="Standard_Mileage" class="form-control" value="<?= $EntryData->mileage?>" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Actual_Mileage">
									<label for="Actual_Mileage" class="control-label">Actual Mileage</label>
									<input type="text" id="Actual_Mileage" name="Actual_Mileage" class="form-control" value="<?= $EntryDetail->Actual_Mileage?>" readonly>
								</div>
							</div>
							<div class="clearfix"></div>
							<br><br>
							<div class="col-md-12">
								<?php if (has_permission('TransportEntry', '', 'create')) {
									if(empty($EntryDetail)){
									?>
									<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
									<?php
									}}else{
									if(empty($EntryDetail)){
									?>
									<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
									<?php
									}}?>
									
									<?php if (has_permission('TransportEntry', '', 'edit')) {
										if(!empty($EntryDetail)){
										?>
										<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
										<?php
										}}else{
										if(!empty($EntryDetail)){
										?>
										<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
										<?php
										}}?>
										
							</div>
						</div>
						
						<div class="clearfix"></div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<script>
    $(document).ready(function(){
		
        
		// Save New ItemDivision
        $('.saveBtn').on('click',function(){ 
            VehRtnNo = $('#VehRtnNo').val();
            out_meter_reading = $('#out_meter_reading').val();
            in_meter_reading = $('#in_meter_reading').val();
            DistanceTravel = $('#DistanceTravel').val();
            Diesel = $('#Diesel').val();
            Diesel_value = $('#Diesel_value').val();
            Fooding = $('#Fooding').val();
            Toll = $('#Toll').val();
            Phone = $('#Phone').val();
            Police = $('#Police').val();
            Misc_Expense = $('#Misc_Expense').val();
            Expense_repairing = $('#Expense_repairing').val();
            Actual_Mileage = $('#Actual_Mileage').val();
			
			if(out_meter_reading == '' || out_meter_reading == null){
				alert('Please Enter Out Meter Reading');
				}else if(in_meter_reading == '' || in_meter_reading == null){
				alert('Please Enter In Meter Reading');
				}else if(DistanceTravel == '' || DistanceTravel == null){
				alert('Please Enter Distance Travel');
				}else if(Diesel == '' || Diesel == null){
				alert('Please Enter Diesel in Ltr');
				}else if(Diesel_value == '' || Diesel_value == null){
				alert('Please Enter Diesel Value');
				}else{
				
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/SaveTransportEntry",
					dataType:"JSON",
					method:"POST",
					data:{VehRtnNo:VehRtnNo,out_meter_reading:out_meter_reading,in_meter_reading:in_meter_reading,DistanceTravel:DistanceTravel,Diesel:Diesel,Diesel_value:Diesel_value,Fooding:Fooding,Fooding:Fooding,Toll:Toll,Phone:Phone,Police:Police,Misc_Expense:Misc_Expense,Expense_repairing:Expense_repairing,Actual_Mileage:Actual_Mileage
					},
					beforeSend: function () {
						$('.searchh3').css('display','block');
						$('.searchh3').css('color','blue');
					},
					complete: function () {
						$('.searchh3').css('display','none');
					},
					success:function(data){
						if(data == true){
							alert_float('success', 'Record created successfully...');
							location.reload();
							}else{
							alert_float('warning', 'Something went wrong...');
						}
					}
				});
			}
		});
		// Update Exiting Item Division
        $('.updateBtn').on('click',function(){ 
            VehRtnNo = $('#VehRtnNo').val();
            out_meter_reading = $('#out_meter_reading').val();
            in_meter_reading = $('#in_meter_reading').val();
            DistanceTravel = $('#DistanceTravel').val();
            Diesel = $('#Diesel').val();
            Diesel_value = $('#Diesel_value').val();
            Fooding = $('#Fooding').val();
            Toll = $('#Toll').val();
            Phone = $('#Phone').val();
            Police = $('#Police').val();
            Misc_Expense = $('#Misc_Expense').val();
            Expense_repairing = $('#Expense_repairing').val();
            Actual_Mileage = $('#Actual_Mileage').val();
            
			
			if(out_meter_reading == '' || out_meter_reading == null){
				alert('Please Enter Out Meter Reading');
				}else if(in_meter_reading == '' || in_meter_reading == null){
				alert('Please Enter In Meter Reading');
				}else if(DistanceTravel == '' || DistanceTravel == null){
				alert('Please Enter Distance Travel');
				}else if(Diesel == '' || Diesel == null){
				alert('Please Enter Diesel in Ltr');
				}else if(Diesel_value == '' || Diesel_value == null){
				alert('Please Enter Diesel Value');
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/UpdateTransportEntry",
					dataType:"JSON",
					method:"POST",
					data:{VehRtnNo:VehRtnNo,out_meter_reading:out_meter_reading,in_meter_reading:in_meter_reading,DistanceTravel:DistanceTravel,Diesel:Diesel,Diesel_value:Diesel_value,Fooding:Fooding,Fooding:Fooding,Toll:Toll,Phone:Phone,Police:Police,Misc_Expense:Misc_Expense,Expense_repairing:Expense_repairing,Actual_Mileage:Actual_Mileage
					},
					beforeSend: function () {
						$('.searchh4').css('display','block');
						$('.searchh4').css('color','blue');
					},
					complete: function () {
						$('.searchh4').css('display','none');
					},
					success:function(data){
						if(data == true){
							alert_float('success', 'Record updated successfully...');
							location.reload();
							}else{
							alert_float('warning', 'Something went wrong...');
						}
				}
			});
		}
	});
});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_ItemDivision_List");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						}else{
						tr[i].style.display = "none";
					} 
				}   
			}
		}
	}
	</script>
	<script type="text/javascript">
		$('#Diesel,#Diesel_value,#Fooding,#Toll,#Phone,#Police,#Misc_Expense,#Expense_repairing').on('keypress',function (event) {
			if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
				event.preventDefault();
			}
			var input = $(this).val();
			if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
				event.preventDefault();
			}
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
		function GetDistantance() {
			var out_reading = $('#out_meter_reading').val();
			var in_reading = $('#in_meter_reading').val();
			
			if(out_reading != '' && out_reading != null && in_reading != '' && in_reading != null){
				if(parseFloat(in_reading) < parseFloat(out_reading)){
					alert('In Meter Reading Should Be Greater Than Out Meter Reading')
					$('#DistanceTravel').val('');
					$('#in_meter_reading').val('');
					$('#in_meter_reading').focus();
					}else{
					var travel = parseFloat(in_reading)-parseFloat(out_reading);
					
					$('#DistanceTravel').val(travel);
				}
				}else{
				$('#DistanceTravel').val('');
			}
		}
		function GetMileage() {
			var DistanceTravel = $('#DistanceTravel').val();
			var Diesel = $('#Diesel').val();
			
			if(DistanceTravel != '' && DistanceTravel != null && Diesel != '' && Diesel != null){
				var Actual_Mileage = parseFloat(DistanceTravel)/parseFloat(Diesel);
				$('#Actual_Mileage').val(Actual_Mileage.toFixed(2));
				}else{
				$('#Actual_Mileage').val('');
			}
		}
	</script>
	
	<style>
		
		#item_code1 {
		text-transform: uppercase;
		}
		#table_ItemDivision_List td:hover {
		cursor: pointer;
		}
		#table_ItemDivision_List tr:hover {
		background-color: #ccc;
		}
		
		.table-ItemDivision_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
		.table-ItemDivision_List thead th { position: sticky; top: 0; z-index: 1; }
		.table-ItemDivision_List tbody th { position: sticky; left: 0; }
		table  { border-collapse: collapse; width: 100%; }
		th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
		th     { background: #50607b;
		color: #fff !important; }
	</style>	