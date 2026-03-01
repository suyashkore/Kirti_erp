<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-9">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Accounts Sub Group 2</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Sub Group 2...</div>
								<div class="searchh4" style="display:none;">Please wait update Sub Group 2...</div>
							</div>
							<br>
							<div class="col-md-4">
								<?php
								$nextSubGroup2ID = $lastId;
								?>
								<?php //echo render_input('SubGroup2ID','SubGroup2ID',$nextSubGroup2ID,'text'); 
								?>
								<div class="form-group" app-field-wrapper="SubGroup2ID">
									<small class="req text-danger">* </small>
									<label for="SubGroup2ID" class="control-label">Account Sub Group 2 Code</label>
									<input type="text" id="SubGroup2ID" name="SubGroup2ID" required class="form-control" value="<?= $nextSubGroup2ID; ?>">
								</div>
								<span id="lblError" style="color: red"></span>
								<input type="hidden" id="nextSubGroup2ID" name="nextSubGroup2ID" class="form-control" value="<?php echo $nextSubGroup2ID; ?>">
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="SubGroupName2">Account Sub Group 2 Name</label>
									<input type="text" name="SubGroupName2" id="SubGroupName2" class="form-control" value="" required>
								</div>
							</div>

							<div class="col-md-4">
                                <div class="form-group" app-field-wrapper="ShortCode">
                                    <small class="req text-danger">* </small>
                                    <label for="ShortCode" class="control-label">Short Code</label>
                                    <input type="text" id="ShortCode" name="ShortCode" class="form-control" value="" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '');">
                                </div>
                            </div>
							<!-- <div class="clearfix"></div> -->

							<div class="col-md-4">
								<div class="form-group">
									<label for="GroupFor">Group For</label>
									<select name="GroupFor" id="GroupFor" class="selectpicker display-block" data-width="100%" data-none-selected-text="None Selected" data-live-search="true">
										<option value=""></option>
										<option value="Account">Group For Account Head</option>
										<option value="Customer">Group For Customer</option>
										<option value="Vendor">Group For Vendor</option>
										<option value="Staff">Group For Staff</option>
										<option value="Broker">Group For Broker</option>
										<option value="Transporter">Group For Transporter</option>
										<option value="VehicleOwner">Group For Vehicle Owner</option>
									</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="MainGroup">Account Main Group Name</label>
									<select name="MainGroup" id="MainGroup" class="form-control selectpicker">
										<option value=""></option>
										<?php
										foreach ($AccountMainGroup as $key => $value) {
										?>
											<option value="<?php echo $value["ActGroupID"];
															?>"><?php echo $value["ActGroupName"]; ?></option>
										<?php
										}
										?>

									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="AccountSubGroupID1">Account Sub Group 1 Name</label>
									<select name="AccountSubGroupID1" id="AccountSubGroupID1" class="selectpicker display-block" data-width="100%" data-live-search="true">
										<option value=""></option>
										<?php
										foreach ($AccountSubGroupID1 as $key => $value) {
										?>
											<option value="<?php echo $value["SubActGroupID1"]; ?>"><?php echo $value["SubActGroupName"]; ?></option>
										<?php
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="IsActive" class="control-label">Is Active ?</label>
									<select id="IsActive" class="form-control selectpicker" data-live-search="true">
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>

							<div class="clearfix"></div>
							<br>
							<div class="col-md-12">
								<span id="updateWarning"
									class="text-warning"
									style="display:none; font-size:13px; margin-bottom:6px;">
									⚠ This record is locked and cannot be updated.
								</span>
							</div>
							<br><br>
							<div class="col-md-12">
								<div class="action-buttons text-left">
									<?php if (has_permission('account_subgroups2', '', 'create')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
									<?php
									} ?>

									<?php if (has_permission('account_subgroups2', '', 'edit')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom updateBtn"><i class="fa fa-save"></i> Update</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom updateBtn2 disabled"><i class="fa fa-save"></i> Update</button>
									<?php
									} ?>



									<button type="reset" class="btn btn-warning cancelBtn">
										<i class="fa fa-refresh"></i> Reset
									</button>

									<button type="button" class="btn btn-info showListBtn" id="btnShowItemDivisionList">
										<i class="fa fa-list"></i> Show List
									</button>
								</div>
							</div>

						</div>

						<div class="clearfix"></div>
						<!-- Iteme List Model-->

						<div class="modal fade SubGroup2_List" id="SubGroup2_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<!-- <div class="modal-dialog modal-md" role="document"> -->
								<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Account Sub Group 2 List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-SubGroup2_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-SubGroup2_List tableFixHead2" id="table_SubGroup2_List" width="100%">
												<thead>
													<tr>
														<th class="sortablePop" style="text-align:left;">Account Sub Group 2 Code </th>
														<th class="sortablePop" style="text-align:left;">Account Sub Group 2 Name</th>
														<th class="sortablePop" style="text-align:left;">Short Code</th>
														<th class="sortablePop" style="text-align:left;">Account Sub Group 1 Name</th>
														<th class="sortablePop" style="text-align:left;">Account Main Group Name</th>
														<th class="sortablePop" style="text-align:left;">IsActive</th>
													</tr>
												</thead>
												<tbody id="SubGroup1TableBody">
													<?php
													foreach ($AccountSubGroupID2 as $key => $value) {
													?>
														<tr class="get_SubGroup2" data-id="<?php echo $value["SubActGroupID"]; ?>">
															<td><?php echo $value['SubActGroupID']; ?></td>
															<td><?php echo $value["SubActGroupName"]; ?></td>
															<td><?php echo $value["ShortCode"]; ?></td>
															<td><?php echo $value["SubActGroupName1"]; ?></td>
															<td><?php echo $value["ActGroupNameMain"]; ?></td>
															<td><?= $value['IsActive'] == 'Y' ? 'Yes' : 'No'; ?></td>

														</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="modal-footer" style="padding:0px;">
										<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
									</div>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<script>
	$(document).ready(function() {


		$('.updateBtn').hide();
		$('.updateBtn2').hide();
		$("#SubGroup2ID").dblclick(function() {
			$('#SubGroup2_List').modal('show');
			$('#SubGroup2_List').on('shown.bs.modal', function() {
				$('#myInput1').focus();
			})
		});

		// Empty and open create mode
		$("#SubGroup2ID").focus(function() {
			var nextSubGroup2ID = $('#nextSubGroup2ID').val();
			$('#SubGroup2ID').val(nextSubGroup2ID);
			$('#SubGroupName2').val('');
			$('#ShortCode').val('');
			$('#MainGroup').val('').selectpicker('refresh');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		// Cancel selected data
		$(".cancelBtn").click(function() {
			var nextSubGroup2ID = $('#nextSubGroup2ID').val();
			$('#SubGroup2ID').val(nextSubGroup2ID).prop('readonly', false).prop('disabled', false);
			$('#SubGroupName2').val('').prop('disabled', false).prop('readonly', false);
			$('#ShortCode').val('').prop('disabled', false).prop('readonly', false);
			$('#GroupFor').val('').prop('disabled', false).selectpicker('refresh');
			$('#MainGroup').val('').prop('disabled', false).selectpicker('refresh');
			$('#AccountSubGroupID1').prop('disabled', false).val('').selectpicker('refresh');
			$('#IsActive').val('Y').prop('disabled', false).selectpicker('refresh');

			$('#updateWarning').hide();
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});


		// On Blur SubGroup2ID Get All Date
		$('#SubGroup2ID').blur(function() {
			SubGroup2ID = $(this).val();
			if (SubGroup2ID == '') {
				var nextSubGroup2ID = $('#nextSubGroup2ID').val();
				$('#SubGroup2ID').val(nextSubGroup2ID);
			} else {
				$.ajax({
					url: "<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details2",
					dataType: "JSON",
					method: "POST",
					data: {
						account_subgroupID: SubGroup2ID
					},
					beforeSend: function() {
						$('.searchh2').css('display', 'block');
						$('.searchh2').css('color', 'blue');
					},
					complete: function() {
						$('.searchh2').css('display', 'none');
					},
					success: function(data) {

						if (data == null) {
							var nextSubGroup2ID = $('#nextSubGroup2ID').val();
							$('#SubGroup2ID').val(nextSubGroup2ID);
							$('#SubGroupName2').val('');
							$('#ShortCode').val('');
							$('#GroupFor').val('');
							$('#MainGroup').val('').selectpicker('refresh');
							$('#AccountSubGroupID1').val('').selectpicker('refresh');
							$('#IsActive').val('').selectpicker('refresh');


							$('.saveBtn').show();
							$('.updateBtn').hide();
							$('.saveBtn2').show();
							$('.updateBtn2').hide();
						} else {
							$('#SubGroup2ID').val(data.SubActGroupID);
							$('#SubGroupName2').val(data.SubActGroupName);
							$('#ShortCode').val(data.ShortCode);
							var GroupFor = '';
							if (data.IsAccountHead == 'Y') {
								GroupFor = 'Account';
							}
							if (data.IsCustomer == 'Y') {
								GroupFor = 'Customer';
							}
							if (data.IsVendor == 'Y') {
								GroupFor = 'Vendor';
							}
							if (data.IsStaff == 'Y') {
								GroupFor = 'Staff';
							}
							if (data.IsBroker == 'Y') {
								GroupFor = 'Broker';
							}
							if (data.IsTransporter == 'Y') {
								GroupFor = 'Transporter';
							}
							if (data.IsVehicleOwner == 'Y') {
								GroupFor = 'VehicleOwner';
							}
							$('#GroupFor').val(GroupFor).selectpicker('refresh');
							$('#MainGroup').val(data.ActGroupID).selectpicker('refresh');
							GetChangedData(data.ActGroupID, data.SubActGroupID1);
							$('#IsActive').val(data.IsActive).selectpicker('refresh');

							// =============================
							// EDIT CONTROL BASED ON IsEditYN
							// =============================
							if (data.IsEditYN === 'N') {

								// Show warning span
								$('#updateWarning').show();

								// Disable update button (do NOT hide)
								$('.updateBtn').prop('disabled', true).show();
								$('.updateBtn2').prop('disabled', true).show();

								// Make fields read-only / disabled
								$('#SubGroup2ID').prop('disabled', true);
								$('#SubGroupName2').prop('readonly', true);
								$('#ShortCode').prop('readonly', true);
								$('#GroupFor').prop('disabled', true).selectpicker('refresh');
								$('#MainGroup').prop('disabled', true).selectpicker('refresh');
								$('#AccountSubGroupID1').prop('disabled', true).selectpicker('refresh');
								$('#IsActive').prop('disabled', true).selectpicker('refresh');

							} else {

								// Hide warning
								$('#updateWarning').hide();

								// Enable update button
								$('.updateBtn').prop('disabled', false).show();
								$('.updateBtn2').prop('disabled', false).show();

								// Editable fields
								$('#SubGroupName2').val(data.SubActGroupName);
								$('#SubGroupName2').prop('disabled', false).selectpicker('refresh');
								$('#ShortCode').prop('disabled', false).selectpicker('refresh');
								$('#GroupFor').prop('disabled', false).selectpicker('refresh');
								$('#MainGroup').prop('disabled', false).selectpicker('refresh');
								$('#AccountSubGroupID1').prop('disabled', false).selectpicker('refresh');
								$('#IsActive').prop('disabled', false).selectpicker('refresh');
							}

							// Common for both cases
							$('.saveBtn').hide();
							$('.saveBtn2').hide();

						}
					}
				});
			}

		});

		$(document).on('click', '.get_SubGroup2', function() {
			SubGroup2ID = $(this).attr("data-id");
			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details2",
				dataType: "JSON",
				method: "POST",
				data: {
					account_subgroupID: SubGroup2ID
				},
				beforeSend: function() {
					$('.searchh2').css('display', 'block');
					$('.searchh2').css('color', 'blue');
				},
				complete: function() {
					$('.searchh2').css('display', 'none');
				},
				success: function(data) {

					$('#SubGroup2ID').val(data.SubActGroupID);
					$('#SubGroupName2').val(data.SubActGroupName);
					$('#ShortCode').val(data.ShortCode);
					var GroupFor = '';
					if (data.IsAccountHead == 'Y') {
						GroupFor = 'Account';
					}
					if (data.IsCustomer == 'Y') {
						GroupFor = 'Customer';
					}
					if (data.IsVendor == 'Y') {
						GroupFor = 'Vendor';
					}
					if (data.IsStaff == 'Y') {
						GroupFor = 'Staff';
					}
					if (data.IsBroker == 'Y') {
						GroupFor = 'Broker';
					}
					if (data.IsTransporter == 'Y') {
						GroupFor = 'Transporter';
					}
					if (data.IsVehicleOwner == 'Y') {
						GroupFor = 'VehicleOwner';
					}
					$('#GroupFor').val(GroupFor).selectpicker('refresh');
					$('#MainGroup').val(data.ActGroupID).selectpicker('refresh');
					$('.selectpicker').selectpicker('refresh');
					GetChangedData(data.ActGroupID, data.SubActGroupID1);
					$('#IsActive').val(data.IsActive).selectpicker('refresh');

					// =============================
					// EDIT CONTROL BASED ON IsEditYN
					// =============================
					if (data.IsEditYN === 'N') {

						// Show warning span
						$('#updateWarning').show();

						// Disable update button (do NOT hide)
						$('.updateBtn').prop('disabled', true).show();
						$('.updateBtn2').prop('disabled', true).show();

						// Make fields read-only / disabled
						$('#SubGroup2ID').prop('disabled', true);
						$('#SubGroupName2').prop('readonly', true);
						$('#ShortCode').prop('readonly', true);
						$('#GroupFor').prop('disabled', true).selectpicker('refresh');
						$('#MainGroup').prop('disabled', true).selectpicker('refresh');
						$('#AccountSubGroupID1').prop('disabled', true).selectpicker('refresh');
						$('#IsActive').prop('disabled', true).selectpicker('refresh');

					} else {

						// Hide warning
						$('#updateWarning').hide();

						// Enable update button
						$('.updateBtn').prop('disabled', false).show();
						$('.updateBtn2').prop('disabled', false).show();

						// Editable fields
						$('#SubGroup2ID').val(data.SubActGroupID).prop('disabled', false).prop('readonly', false);
						$('#SubGroupName2').val(data.SubActGroupName).prop('disabled', false).prop('readonly', false);
						$('#ShortCode').val(data.ShortCode).prop('disabled', false).prop('readonly', false);
						$('#GroupFor').prop('disabled', false).selectpicker('refresh');
						$('#MainGroup').prop('disabled', false).selectpicker('refresh');
						$('#AccountSubGroupID1').prop('disabled', false).selectpicker('refresh');
						$('#IsActive').prop('disabled', false).selectpicker('refresh');
					}

					// Common for both cases
					$('.saveBtn').hide();
					$('.saveBtn2').hide();


				}
			});
			$('#SubGroup2_List').modal('hide');
		});


		$('#MainGroup').on('change', function() {
			var AccountMainGroupID = $(this).val();
			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/GetAccountSubGroupID1",
				dataType: "JSON",
				method: "POST",
				cache: false,
				data: {
					AccountMainGroupID: AccountMainGroupID,
				},
				success: function(data) {
					$("#AccountSubGroupID1").children().remove();
					$("#AccountSubGroupID1").append('<option value=""></option>');
					for (var i = 0; i < data.length; i++) {
						$("#AccountSubGroupID1").append('<option value="' + data[i]["SubActGroupID1"] + '">' + data[i]["SubActGroupName"] + '</option>');
					}
					$('.selectpicker').selectpicker('refresh');
					$("#AccountSubGroupID2").children().remove();
					$('.selectpicker').selectpicker('refresh');
				}
			});
		})

		function GetChangedData(AccountMainGroupID, selectedSubGroup1 = null) {
			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/GetAccountSubGroupID1",
				dataType: "JSON",
				method: "POST",
				cache: false,
				data: {
					AccountMainGroupID: AccountMainGroupID,
				},
				success: function(data) {

					let $ddl = $("#AccountSubGroupID1");
					$ddl.empty();
					$ddl.append('<option value=""></option>');

					for (let i = 0; i < data.length; i++) {
						$ddl.append(
							'<option value="' + data[i].SubActGroupID1 + '">' +
							data[i].SubActGroupName +
							'</option>'
						);
					}

					if (selectedSubGroup1) {
						$ddl.val(selectedSubGroup1);
					}

					$ddl.selectpicker('refresh');
				}
			});
		}

		$('#ShortCode').blur(function(){ 
			ShortCode = $(this).val();
			if(ShortCode == ''){
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>accounts_master/CheckShortCodeExit",
					dataType:"JSON",
					method:"POST",
					data:{ShortCode:ShortCode},
					beforeSend: function () {
						$('.searchh2').css('display','block');
						$('.searchh2').css('color','blue');
					},
					complete: function () {
						$('.searchh2').css('display','none');
					},
					success:function(data){
						if(data){
							alert_float('warning',"ShortCode Already Exist.");
							$('#ShortCode').val("").focus();
						}
					}
				});
			}
		});


		// Save New Sub Group 2
		$('.saveBtn').on('click', function() {
			SubGroup2ID = $('#SubGroup2ID').val();
			SubGroupName2 = $('#SubGroupName2').val();
			ShortCode = $('#ShortCode').val();
			GroupFor = $('#GroupFor').val();
			AccountSubGroupID1 = $('#AccountSubGroupID1').val();
			IsActive = $('#IsActive').val();


			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/SaveSubGroup2",
				dataType: "JSON",
				method: "POST",
				data: {
					SubGroupID: SubGroup2ID,
					SubGroupName: SubGroupName2,
					ShortCode: ShortCode,
					GroupFor: GroupFor,
					AccountSubGroupID1: AccountSubGroupID1,
					IsActive: IsActive
				},
				beforeSend: function() {
					$('.searchh3').css('display', 'block');
					$('.searchh3').css('color', 'blue');
				},
				complete: function() {
					$('.searchh3').css('display', 'none');
				},
				success: function(data) {
					if (data == true) {
						alert_float('success', 'Record created successfully...');
						var nextSubGroup2ID = $('#nextSubGroup2ID').val();
						var newGroupID = parseInt(nextSubGroup2ID) + 1;
						$('#SubGroup2ID').val(newGroupID);
						$('#nextSubGroup2ID').val(newGroupID);
						$('#SubGroupName2').val('');
						$('#ShortCode').val('');
						$('#GroupFor').val('').selectpicker('refresh');
						$('#MainGroup').val('').selectpicker('refresh');
						$('#AccountSubGroupID1').val('').selectpicker('refresh');
						$('#IsActive').val('Y').selectpicker('refresh');


						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshSubGroup2List();
					} else {
						alert_float('warning', data.message);
						$('#SubGroupName2').focus();
						return;
					}
				}
			});
		});
		// Update Exiting Sub Group 2
		$('.updateBtn').on('click', function() {
			SubGroup2ID = $('#SubGroup2ID').val();
			SubGroupName2 = $('#SubGroupName2').val();
			ShortCode = $('#ShortCode').val();
			GroupFor = $('#GroupFor').val();
			AccountSubGroupID1 = $('#AccountSubGroupID1').val();
			IsActive = $('#IsActive').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/UpdateSubGroup2",
				dataType: "JSON",
				method: "POST",
				data: {
					SubGroupID: SubGroup2ID,
					SubGroupName: SubGroupName2,
					ShortCode: ShortCode,
					GroupFor: GroupFor,
					AccountSubGroupID1: AccountSubGroupID1,
					IsActive: IsActive
				},
				beforeSend: function() {
					$('.searchh4').css('display', 'block');
					$('.searchh4').css('color', 'blue');
				},
				complete: function() {
					$('.searchh4').css('display', 'none');
				},
				success: function(data) {
					if (data == true) {
						alert_float('success', 'Record updated successfully...');
						var nextSubGroup2ID = $('#nextSubGroup2ID').val();
						$('#SubGroup2ID').val(nextSubGroup2ID);
						$('#SubGroupName2').val('');
						$('#ShortCode').val('');
						$('#GroupFor').val('').selectpicker('refresh');
						$('#MainGroup').val('').selectpicker('refresh');
						$('#AccountSubGroupID1').val('').selectpicker('refresh');
						$('#IsActive').val('Y').selectpicker('refresh');

						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshSubGroup2List();
					} else {
						alert_float('warning', data.message || 'Something went wrong...');
					}
				}
			});
		});
	});
	$('.showListBtn').on('click', function() {
		$('#SubGroup2_List').modal('show');

		$('#SubGroup2_List').on('shown.bs.modal', function() {
			$('#myInput1').focus();
		});
	});
</script>


<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_SubGroup2_List");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else if (td1) {
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					} else {
						tr[i].style.display = "none";
					}
				}
			}
		}
	}

	$(document).on("click", ".sortablePop", function() {
		var table = $("#table_SubGroup2_List tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");


		// Remove existing sort classes and reset arrows
		$(".sortablePop").removeClass("asc desc");
		$(".sortablePop span").remove();

		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');

		rows.sort(function(a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();

			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
			} else {
				return ascending ?
					valA.localeCompare(valB) :
					valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});
</script>
<script>
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31 &&
			(charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}

	function refreshSubGroup2List() {
		$('#SubGroup1TableBody').load(location.href + ' #SubGroup1TableBody > *');
	}
</script>

<style>
	#item_code1 {
		text-transform: uppercase;
	}

	#table_SubGroup2_List td:hover {
		cursor: pointer;
	}

	#table_SubGroup2_List tr:hover {
		background-color: #ccc;
	}

	.table-SubGroup2_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-SubGroup2_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-SubGroup2_List tbody th {
		position: sticky;
		left: 0;
	}

	table {
		border-collapse: collapse;
		width: 100%;
	}

	th,
	td {
		padding: 1px 5px !important;
		white-space: nowrap;
		border: 1px solid !important;
		font-size: 11px;
		line-height: 1.42857143 !important;
		vertical-align: middle !important;
	}

	th {
		background: #50607b;
		color: #fff !important;
	}

	#SubGroupName2, #ShortCode {
		text-transform: uppercase;
	}
</style>