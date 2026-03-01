<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" >
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
					    <div class="row">
					        <nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>HR</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Daily Attendance Register</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
					        <div class="col-md-4">
								<?php $value =  date('d/m/Y'); 
								?>
								<?php echo render_date_input('date','Attendance Date',$value,''); ?>
							</div>
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="Department">
									<small class="req text-danger"> </small>
									<label for="Department" class="form-label">Department</label>
									<select name="Department" id="Department" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
										<option value="">None selected</option>
										<?php
											foreach ($departments as $key => $value) {
											?>
											<option value="<?php echo $value['departmentid'];?>"><?php echo $value['name']; ?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
							
							<div class="col-md-4" style="margin-top:10px;">
								<br>
								<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button> 
								&nbsp<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export</span></a>
							</div>
							
							<div class="clearfix"></div>
					        <div class="col-md-12">
					            <div class="table-daily_attendance_report table has-calculations">
									<table class="tree table table-striped table-bordered table-daily_attendance_report" id="table-daily_attendance_report">
										<thead>
											<tr>
												<th width="5%">Sr. No.</th>
												<th width="10%">AccountID</th>
												<th width="55%">Staff Name</th>
												<th width="15%">Check In</th>
												<th width="15%">Check Out</th>
											</tr>
										</thead>
										<tbody id="Attedancebody">
											<?php
												$i = 1;
												foreach($list_staff as $key=>$val){
												?>
												<tr class="StaffWiseAttendance" data-id="<?= $val['departmentid']?>">
													<td style="text-align:center;"><?php echo $i;?></td>
													<td style="text-align:center;"><?php echo $val["AccountID"];?></td>
													<td class="AccountID"><?php echo $val["firstname"]." ".$val["lastname"];?><input type="hidden" name="AccountID[]" id="AccountID" value="<?php echo $val["AccountID"];?>"></td>
													<td class="CheckInTime"><input type="time" name="CheckINTime[]" id="CheckINTime" class="form-control" ></td>
													<td class="CheckOutTime"><input type="time" name="CheckOutTime[]" id="CheckOutTime" class="form-control" ></td>
												</tr>
												<?php
													$i++;
												} 
											?>
										</tbody>
									</table>
								</div>
							</div>
							
							<div class="col-md-12">
								<div class="btn-bottom-toolbar bottom-transaction text-right">
									<button class="btn btn-info mleft5" id="submit" type="button">Update Attendance</button>
								</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<style>
		.table-daily_attendance_report          { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
		.table-daily_attendance_report thead th { position: sticky; top: 0; z-index: 1; }
		.table-daily_attendance_report tbody th { position: sticky; left: 0; }
		
		
		table  { border-collapse: collapse; width: 100%; }
		th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
		th     { background: #50607b;
		color: #fff !important; }
	</style>
	<?php init_tail(); ?>
	<script>
		// $(document).ready(function(){
		// $('#Department').on('change', function() {
		// var selectedDept = $(this).val();
		
		// if (selectedDept === "") {
		// $('#Attedancebody tr').show();
		// } else {
		// $('#Attedancebody tr').hide();
		// $('#Attedancebody tr[data-id="' + selectedDept + '"]').show();
		// }
		// });
		// });
		
		
	</script>
	<script>
		//================ Submit Production Order =====================================
		// Save New Transfer
		$('#submit').on('click',function(){ 
			var date = $('#date').val();
			var Department = $('#Department').val();
			
			if(date == ""){
				alert('Please Select Date');
				}else{
				fg = $(".table.has-calculations tbody#Attedancebody tr.StaffWiseAttendance");
				var UserAttendanceArray = new Array();
				let i = 0;
				$.each(fg, function() {
					var AccountID = $(this).find("td.AccountID input").val();
					var CheckInTime = $(this).find("td.CheckInTime input").val();
					var CheckOutTime = $(this).find("td.CheckOutTime input").val();
					UserAttendanceArray[i]=new Array();
					UserAttendanceArray[i][0]=AccountID;
					UserAttendanceArray[i][1]=CheckInTime;
					UserAttendanceArray[i][2]=CheckOutTime;
					i++;
				});
				var UserAttendanceString = JSON.stringify(UserAttendanceArray);
				$.ajax({
					url:"<?php echo admin_url(); ?>hr_profile/SaveUserAttendance",
					//dataType:"JSON",
					method:"POST",
					data:{UserAttendanceString:UserAttendanceString,date:date,Department:Department},
					//data:{fd:fd},
					beforeSend: function () {
						$('.searchh3').css('display','block');
						$('.searchh3').css('color','blue');
					},
					complete: function () {
						$('.searchh3').css('display','none');
					},
					success:function(data){
						if(data >0){
							var msg = "Total "+ data + " Record Updated Successfully";
							alert_float('success', msg);
							}else{
							alert_float('warning', '0 Record Updated, Please Try Again.');
						}
					}
				});
			}
		});
		
		$(document).ready(function(){
			$('#search_data').on('click',function(){
				var Department = $("#Department").val();
				$.ajax({
					url:"<?php echo admin_url(); ?>hr_profile/GetStaffByDepartment",
					dataType:"JSON",
					method:"POST",
					data:{Department:Department},
					success:function(returndata){
						$('#Attedancebody').html(returndata);
						var date = $("#date").val();
						load_data(date);
					}
				});
				
			});
			
			var date = $("#date").val();
			load_data(date);
			function load_data(date)
			{
				$.ajax({
					url:"<?php echo admin_url(); ?>hr_profile/GetUserAttendance",
					dataType:"JSON",
					method:"POST",
					data:{date:date},
					beforeSend: function () {
						$('.searchh3').css('display','block');
						$('.searchh3').css('color','blue');
					},
					complete: function () {
						$('.searchh3').css('display','none');
					},
					success: function(data) {
						var rows = $("#table-daily_attendance_report tbody#Attedancebody tr.StaffWiseAttendance");
						if (data.length > 0) {
							rows.each(function() {
								var AccountID = $(this).find("td.AccountID input").val();
								var CheckInTimeInput = $(this).find("td.CheckInTime input");
								var CheckOutTimeInput = $(this).find("td.CheckOutTime input");
								
								data.forEach(function(Attendance) {
									var AccountID2 = Attendance.AccountID;
									if (AccountID == AccountID2) {
										// Parse and format CheckInTime
										var InDateTime = Attendance.InDateTime;
										var timeIn = formatTime(InDateTime); // Convert to `HH:MM` format
										
										// Parse and format CheckOutTime
										var CheckOutTime = Attendance.OutDateTime;
										var timeOut = formatTime(CheckOutTime); // Convert to `HH:MM` format
										// console.log(timeOut);
										// Set the input values
										CheckInTimeInput.val(timeIn);
										CheckOutTimeInput.val(timeOut);
									}
								});
							});
							} else {
							rows.each(function() {
								var CheckInTimeInput = $(this).find("td.CheckInTime input");
								var CheckOutTimeInput = $(this).find("td.CheckOutTime input");
								CheckInTimeInput.val('');
								CheckOutTimeInput.val('');
							});
						}
					}
				});
			}
			$('#date').on('blur',function(){
				var date = $("#date").val();
				load_data(date);
				
			});
			
			
			
		});
		
		$("#caexcel").click(function(){
			var date = $("#date").val();
			var Department = $("#Department").val();
			
			$.ajax({
				url:"<?php echo admin_url(); ?>hr_profile/ExportAttendanceRegister",
				method:"POST",
				data:{date:date,Department:Department},
				beforeSend: function () {
					$('#searchh2').css('display','block');
				},
				complete: function () {
					$('#searchh2').css('display','none');
				},
				success:function(data){
					response = JSON.parse(data);
					window.location.href = response.site_url+response.filename;
				}
			});
		});
		
		
		function formatTime(dateTime) {
			if (!dateTime) return '';
			
			// Split the datetime into date and time components
			var parts = dateTime.split(' '); // ["2024-12-05", "19:10:00.000000"]
			if (parts.length < 2) return '';
			
			var timePart = parts[1]; // Extract the time part: "19:10:00.000000"
			var timeWithoutMicroseconds = timePart.split('.')[0]; // Remove microseconds: "19:10:00"
			var timeComponents = timeWithoutMicroseconds.split(':'); // Split into [HH, MM, SS]
			
			if (timeComponents.length < 2) return '';
			
			var hours = parseInt(timeComponents[0], 10); // Extract hours (e.g., "19")
			var minutes = parseInt(timeComponents[1], 10); // Extract minutes (e.g., "10")
			
			// Ensure hours and minutes are valid numbers
			if (isNaN(hours) || isNaN(minutes)) {
				console.error("Invalid time format:", dateTime);
				return '';
			}
			// Convert to `HH:MM` format for <input type="time">
			return hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
		}
	</script>
