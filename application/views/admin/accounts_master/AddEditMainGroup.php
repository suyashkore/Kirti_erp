<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Accounts Main Group</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Main Group...</div>
								<div class="searchh4" style="display:none;">Please wait update Main Group...</div>
							</div>
							<br>
							<div class="col-md-3">
								<?php
								$nextMainGroupID = $lastId;
								?>
								<?php //echo render_input('MainGroupID','MainGroupID',$nextMainGroupID,'text'); 
								?>
								<div class="form-group" app-field-wrapper="MainGroupID">
									<small class="req text-danger">* </small>
									<label for="MainGroupID" class="control-label">Main Group ID</label>
									<input type="text" id="MainGroupID" name="MainGroupID" required class="form-control" value="<?= $nextMainGroupID; ?>" readonly>
								</div>
								<span id="lblError" style="color: red"></span>
								<input type="hidden" id="NextMainGroupID" name="NextMainGroupID" class="form-control" value="<?php echo $nextMainGroupID; ?>">
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="MainGroupName">Main Group Name</label>
									<input type="text" name="MainGroupName" id="MainGroupName" class="form-control" value="" required>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="GroupType">Group Type</label>
									<select name="GroupType" id="GroupType" class="form-control selectpicker">
										<option value=""></option>
										<option value="A">Assets</option>
										<option value="L">Liability</option>
										<option value="I">Income</option>
										<option value="E">Expense</option>
									</select>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Movement">Movement</label>
									<select name="Movement" id="Movement" class="form-control selectpicker">
										<option value=""></option>
										<?php
										foreach ($account_group_mov as $key => $value) {
										?>
											<option value="<?php echo $value["ActGroupMovementID"]; ?>"><?php echo $value["ActGroupMovementName"]; ?></option>
										<?php
										}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="MainGroupID">
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
							<br>
							<br>
							<div class="col-md-12">
								<div class="action-buttons text-left">
									<?php if (has_permission('MainGroup', '', 'create')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
									<?php
									} ?>
									<?php if (has_permission('MainGroup', '', 'edit')) {
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

						<div class="modal fade MainGroup_List" id="MainGroup_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Main Group List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-MainGroup_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-MainGroup_List tableFixHead2" id="table_MainGroup_List" width="100%">
												<thead>
													<tr>
														<th class="sortablePop" style="text-align:left;">Main Group ID </th>
														<th class="sortablePop" style="text-align:left;">Main Group Name</th>
														<th class="sortablePop" style="text-align:left;">Group Type</th>
														<th class="sortablePop" style="text-align:left;">Movement</th>
														<th class="sortablePop" style="text-align:left;">IsActive</th>

													</tr>
												</thead>
												<tbody id="MainGroupTableBody">
													<?php
													foreach ($table_data as $key => $value) {
													?>
														<tr class="get_MainGroup" data-id="<?php echo $value["ActGroupID"]; ?>">
															<td><?php echo $value['ActGroupID']; ?></td>
															<td><?php echo $value["ActGroupName"]; ?></td>
															<?php
															if ($value["ActGroupTypeID"] == "A") {
																$groupType = "Assets";
															} elseif ($value["ActGroupTypeID"] == "L") {
																$groupType = "Liability";
															} elseif ($value["ActGroupTypeID"] == "I") {
																$groupType = "Income";
															} elseif ($value["ActGroupTypeID"] == "E") {
																$groupType = "Expense";
															} else {
																$groupType = "";
															}
															?>
															<td><?php echo $groupType; ?></td>
															<?php
															if ($value["ActGroupMovementID"] == "B") {
																$movement = "BALANCE SHEET";
															} elseif ($value["ActGroupMovementID"] == "P") {
																$movement = "PROFIT & LOSS A/C";
															} elseif ($value["ActGroupMovementID"] == "T") {
																$movement = "TRADING A/C";
															}
															?>
															<td><?php echo $movement; ?></td>
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
		$("#MainGroupID").dblclick(function() {
			$('#MainGroup_List').modal('show');
			$('#MainGroup_List').on('shown.bs.modal', function() {
				$('#myInput1').focus();
			})
		});

		// Empty and open create mode
		$("#MainGroupID").focus(function() {
			var NextMainGroupID = $('#NextMainGroupID').val();
			$('#MainGroupID').val(NextMainGroupID);
			$('#MainGroupName').val('');
			$('#GroupType').val('').selectpicker('refresh');
			$('#Movement').val('').selectpicker('refresh');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		// Cancel selected data
		$(".cancelBtn").click(function() {
			var NextMainGroupID = $('#NextMainGroupID').val();
			$('#MainGroupID').val(NextMainGroupID).prop('disabled', false);
			$('#MainGroupName').val('').prop('readonly', false).prop('disabled', false);
			$('#GroupType').val('').prop('disabled', false).selectpicker('refresh');
			$('#Movement').val('').prop('disabled', false).selectpicker('refresh');
			$('#IsActive').val('Y').prop('disabled', false).selectpicker('refresh');


			$('#updateWarning').hide();
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		$(document).on('click', '.get_MainGroup', function() {
			MainGroupID = $(this).attr("data-id");
			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/get_account_Maingroup_details",
				dataType: "JSON",
				method: "POST",
				data: {
					MainGroupID: MainGroupID
				},
				beforeSend: function() {
					$('.searchh2').css('display', 'block');
					$('.searchh2').css('color', 'blue');
				},
				complete: function() {
					$('.searchh2').css('display', 'none');
				},
				success: function(data) {

					$('#MainGroupID').val(data.MainGroupID);
					$('#MainGroupName').val(data.MainGroupName);
					$('#GroupType').val(data.GroupType).selectpicker('refresh');
					$('#Movement').val(data.Movement).selectpicker('refresh');
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
						$('#MainGroupID').prop('disabled', true);
						$('#MainGroupName').prop('readonly', true);
						$('#GroupType').prop('disabled', true).selectpicker('refresh');
						$('#Movement').prop('disabled', true).selectpicker('refresh');
						$('#IsActive').prop('disabled', true).selectpicker('refresh');

					} else {

						// Hide warning
						$('#updateWarning').hide();

						// Enable update button
						$('.updateBtn').prop('disabled', false).show();
						$('.updateBtn2').prop('disabled', false).show();

						// Editable fields
						$('#MainGroupName').prop('readonly', false);
						$('#GroupType').prop('disabled', false).selectpicker('refresh');
						$('#Movement').prop('disabled', false).selectpicker('refresh');
						$('#IsActive').prop('disabled', false).selectpicker('refresh');
					}

					// Common for both cases
					$('.saveBtn').hide();
					$('.saveBtn2').hide();
				}

			});
			$('#MainGroup_List').modal('hide');
		});

		// Save New MainGroup
		$('.saveBtn').on('click', function() {
			MainGroupID = $('#MainGroupID').val();
			MainGroupName = $('#MainGroupName').val();
			GroupType = $('#GroupType').val();
			Movement = $('#Movement').val();
			IsActive = $('#IsActive').val();


			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/SaveMainGroup",
				dataType: "JSON",
				method: "POST",
				data: {
					MainGroupID: MainGroupID,
					MainGroupName: MainGroupName,
					GroupType: GroupType,
					Movement: Movement,
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
						var NextMainGroupID = $('#NextMainGroupID').val();
						var newGroupID = parseInt(NextMainGroupID) + 1;
						$('#MainGroupID').val(newGroupID);
						$('#NextMainGroupID').val(newGroupID);
						$('#MainGroupName').val('');
						$('#GroupType').val('').selectpicker('refresh');
						$('#Movement').val('').selectpicker('refresh');
						$('#IsActive').val('Y').selectpicker('refresh');


						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshMainGroupList();
					} else {
						alert_float('warning', data.message);
						$('#MainGroupName').focus();
						return;
					}
				}
			});
		});
		// Update Exiting Item Division
		$('.updateBtn').on('click', function() {
			MainGroupID = $('#MainGroupID').val();
			MainGroupName = $('#MainGroupName').val();
			GroupType = $('#GroupType').val();
			Movement = $('#Movement').val();
			IsActive = $('#IsActive').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>accounts_master/UpdateMainGroup",
				dataType: "JSON",
				method: "POST",
				data: {
					MainGroupID: MainGroupID,
					MainGroupName: MainGroupName,
					GroupType: GroupType,
					Movement: Movement,
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
						var NextMainGroupID = $('#NextMainGroupID').val();
						$('#MainGroupID').val(NextMainGroupID);
						$('#MainGroupName').val('');
						$('#GroupType').val('').selectpicker('refresh');
						$('#Movement').val('').selectpicker('refresh');
						$('#IsActive').val('Y').selectpicker('refresh');

						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshMainGroupList();
					} else {
						alert_float('warning', data.message || 'Something went wrong...');
					}
					//    location.reload();
				}
			});
		});
	});
	$('.showListBtn').on('click', function() {
		$('#MainGroup_List').modal('show');

		$('#MainGroup_List').on('shown.bs.modal', function() {
			$('#myInput1').focus();
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_MainGroup_List");
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
		var table = $("#table_MainGroup_List tbody");
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

	function refreshMainGroupList() {
		$('#MainGroupTableBody').load(location.href + ' #MainGroupTableBody > *');
	}
</script>

<style>
	#item_code1 {
		text-transform: uppercase;
	}

	#table_MainGroup_List td:hover {
		cursor: pointer;
	}

	#table_MainGroup_List tr:hover {
		background-color: #ccc;
	}

	.table-MainGroup_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-MainGroup_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-MainGroup_List tbody th {
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

	#MainGroupName {
		text-transform: uppercase;
	}
</style>