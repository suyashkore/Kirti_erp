<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" style="min-height:1px">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<style>
							@media (max-width: 767px) {
								.mobile-menu-btn {
									display: block !important;
									margin-bottom: 10px;
									width: 100%;
									text-align: left;
								}

								.custombreadcrumb {
									display: none !important;
								}

								.custombreadcrumb.open {
									display: block !important;
								}

								.custombreadcrumb li {
									display: block;
									padding: 8px 10px;
									border-bottom: 1px solid #eee;
								}

								.custombreadcrumb li a {
									display: block;
								}

								.custombreadcrumb li+li:before {
									content: none;
								}
							}

							.mobile-menu-btn {
								display: none;
							}
						</style>
						<button class="btn btn-default mobile-menu-btn"
							onclick="$('.custombreadcrumb').toggleClass('open')">
							<i class="fa fa-bars"></i> Menu
						</button>
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb"
								style="background-color:#fff !important; margin-Bottom:0px !important; display: flex; flex-wrap: wrap;">
								<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i
												class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Vehicle Owner Master</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait while fetching data.</div>
								<div class="searchh3" style="display:none;">Please wait while creating new record.</div>
								</style>
							</div>
						</div>

						<!-- Top Fields - Main Information -->
						<div class="row">
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label"><small class="req text-danger">* </small>Owner Groups</label>
									<select class="selectpicker form-control" id="groups_in"
										data-none-selected-text="None selected" name="groups_in" data-width="100%"
										data-live-search="true" title="Select Vehicle Owner Group">
										<option value=""></option>
										<?php foreach ($getvehicleownergroups as $key => $value) { ?>
											<option
												value="<?php echo $value['SubActGroupID']; ?>"
												data-shortcode="<?php echo $value['ShortCode']; ?>">
												<?php echo $value['SubActGroupName']; ?>
											</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<input type="hidden" id="HiddenVehicleOwnerCode" name="HiddenVehicleOwnerCode">
									<label class="control-label"><small class="req text-danger">* </small>Owner Code</label>
									<input type="text" class="form-control" id="AccountID" name="AccountID" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label"><small class="req text-danger">* </small>Owner Name</label>
									<input type="text" class="form-control" id="AccoountName" name="AccoountName" value="" readonly>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="PAN">
									<label for="Pan" class="control-label">
										<small class="req text-danger">*</small> PAN
									</label>

									<input type="text" id="Pan" name="Pan" class="form-control text-uppercase" maxlength="10" onblur="validatePAN()">
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label for="phonenumber" class="control-label">
										<small class="req text-danger">*</small> Owner Mobile
									</label>
									<input type="text" id="phonenumber" name="phonenumber" class="form-control" maxlength="10" oninput="this.value = this.value.replace(/\D/g, '')">
								</div>
							</div>

							<div class="clearfix"></div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">IFSC Code</label>
									<input type="text" name="ifsc_code" id="ifsc_code" class="form-control"
										maxlength="11">
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">Bank Name</label>
									<input type="text" name="bank_name" id="bank_name" class="form-control" readonly>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">Branch Name</label>
									<input type="text" name="branch_name" id="branch_name" class="form-control"
										readonly>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">Bank Address</label>
									<input type="text" name="bank_address" id="bank_address" class="form-control"
										readonly>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">Account Number</label>
									<input type="text" name="account_number" id="account_number" class="form-control">
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">Account Holder Name</label>
									<input type="text" name="account_holder_name" id="account_holder_name"
										class="form-control" readonly>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">TDS</label>
									<select class="selectpicker" id="Tds" name="Tds" data-width="100%">
										<option value="0">No</option>
										<option value="1">Yes</option>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">Is Active</label>
									<select name="IsActive" id="IsActive" class="form-control selectpicker"
										data-live-search="true">
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>
							<div class="col-md-2" id="TdsSec" style="display:none;">
								<div class="form-group">
									<label class="control-label">TDS Section</label>
									<select class="selectpicker" id="Tdsselection" name="Tdsselection"
										data-width="100%">
										<option value="">Non Selected</option>
										<?php if (isset($Tdssection)) {
											foreach ($Tdssection as $w): ?>
												<option value="<?= $w['TDSCode'] ?>"><?= $w['TDSName'] ?></option>
										<?php endforeach;
										} ?>
									</select>
								</div>
							</div>
							<div class="col-md-2" id="TdsPercent1" style="display:none;">
								<div class="form-group">
									<label class="control-label">TDS Rate (%)</label>
									<select class="selectpicker" id="TdsPercent" name="TdsPercent" data-width="100%">
										<option value="">Non Selected</option>
									</select>
								</div>
							</div>
							<div class="clearfix"></div>


							<div class="col-md-12">
								<h4 class="bold p_style">Vehicles List</h4>
								<hr class="hr_style">
								<div class="table-responsive">
									<table class="table table-bordered" id="contactTable">
										<thead>
											<tr>
												<th>Vehicle No</th>
												<th>Prefer Transport</th>
												<th>Driver Name</th>
												<th>Driver Mobile</th>
												<th>Licence No</th>
												<th>RC Book</th>
												<th>Capacity (MT)</th>
												<th>Vehicle Type</th>
												<th>Is Active</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="contacttbody">
											<tr id="vehicleInputRow" class="emptyRow">

												<td>
													<input type="text" id="VehicleNo" name="VehicleNo" class="form-control text-uppercase"
														maxlength="13">
												</td>

												<td>
													<select class="selectpicker" id="PreferTransport" name="PreferTransport" data-width="100%" data-container="body">
														<option value="">None Selected</option>
														<?php foreach ($transporter as $key => $value) { ?>
															<option value="<?php echo $value['AccountID']; ?>">
																<?php echo $value['company']; ?>-(<?php echo $value['AccountID']; ?>)</option>
														<?php } ?>
													</select>
												</td>

												<td><input type="text" id="DriverName" name="DriverName" class="form-control"></td>

												<td>
													<input type="text" id="DriverMobile" name="DriverMobile" class="form-control" maxlength="10" oninput="this.value = this.value.replace(/\D/g, '')">
												</td>

												<td><input type="text" id="LicenceNo" name="LicenceNo" class="form-control text-uppercase"></td>

												<!-- <td>
													<input type="file" id="RCBook" class="rc_file" name="RCBook" style="display:none !important;">

													<button type="button" class="btn btn-info btn-sm"
														onclick="$('#RCBook').click()">
														<i class="fa fa-upload"></i>
													</button>

													<button type="button" class="btn btn-secondary btn-sm"
														id="viewRC" style="display:none">
														<i class="fa fa-eye"></i>
													</button>

												</td>
												 -->
												<td>
    <input type="file" class="rc_file" name="RCBook" style="display:none">

    <button type="button" class="btn btn-info btn-sm uploadBtn">
        <i class="fa fa-upload"></i>
    </button>

    <button type="button" class="btn btn-secondary btn-sm viewBtn" style="display:none">
        <i class="fa fa-eye"></i>
    </button>
</td>


												<td>
													<input type="text" id="Capacity" name="Capacity"
														class="form-control">
												</td>

												<td>
													<select class="selectpicker" id="VehicleType" name="VehicleType" data-width="100%" data-container="body">
														<option value="">None Selected</option>
														<option value="0">Diesel</option>
														<option value="1">CNG</option>
														<option value="2">Petrol</option>
														<option value="3">Other</option>
													</select>
												</td>

												<td>
													<select id="VehicleIsActive" name="VehicleIsActive[]" class="selectpicker" data-width="100%" data-container="body">
														<option value="Y">Yes</option>
														<option value="N">No</option>
													</select>
												</td>

												<td>
													<!-- <button type="button" class="btn btn-success" onclick="addVehicleRow()"> -->
														<!-- <i class="fa fa-plus"></i>
													</button> -->
													<button type="button" class="btn btn-success addMoreRow">
    <i class="fa fa-plus"></i>
</button>

												</td>

											</tr>

										</tbody>
									</table>
								</div>
							</div>


							<div class="clearfix"></div>
							<br>
							<div class="col-md-12">
							</div>
							<br><br>
							<div class="col-md-12 sticky-actions">
								<div class="action-buttons text-right">
									<?php if (has_permission('VehicleMaster', '', 'create')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
									<?php
									} ?>

									<?php if (has_permission('VehicleMaster', '', 'edit')) {
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

									<button type="button" class="btn btn-info showAllBtn" id="btnShowItemDivisionList">
										<i class="fa fa-list"></i> Show List
									</button>
								</div>
							</div>

						</div>

						<div class="clearfix"></div>
						<!-- Iteme List Model-->

						<div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog"
							data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal"
											aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Vechile Owner List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-Account_List tableFixHead2">
											<table
												class="tree table table-striped table-bordered table-Account_List tableFixHead2"
												id="table_Account_List" width="100%">
												<thead>
													<tr>
														<th style="text-align:left;" class="sortablePop">Vehicle Owner Code</th>
														<th style="text-align:left;" class="sortablePop">Vehicle Owner Name</th>
														<th style="text-align:left;" class="sortablePop">PAN NO</th>
														<th style="text-align:left;" class="sortablePop">Mobile No</th>
														<th style="text-align:left;" class="sortablePop">TDS</th>
														<th style="text-align:left;" class="sortablePop">Is Active</th>
													</tr>
												</thead>
												<tbody id="customertlistbody">

												</tbody>
											</table>
										</div>
									</div>
									<div class="modal-footer" style="padding:0px;">
										<input type="text" id="myInput1" onkeyup="myFunction2()"
											placeholder="Search for names.." title="Type in a name"
											style="float: left;width: 100%;">
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
	// Initialize position data for dynamic row creation
	var positionData = <?php echo json_encode($transporter); ?>;

	function getPositionOptions(selected = '') {
		let html = '<option value="">None Selected</option>';

		positionData.forEach(function(pos) {
			let selectedAttr = (pos.AccountID == selected) ? 'selected' : '';
			html += `<option value="${pos.AccountID}" ${selectedAttr}>
                    ${pos.company}-(${pos.AccountID})
                 </option>`;
		});

		return html;
	}

	function getVehicleOptions(selected = '') {

		const vehicles = [{
				value: "0",
				label: "Diesel"
			},
			{
				value: "1",
				label: "CNG"
			},
			{
				value: "2",
				label: "Petrol"
			},
			{
				value: "3",
				label: "Other"
			}
		];

		let html = '<option value="">None Selected</option>';

		vehicles.forEach(v => {

			let selectedAttr =
				(String(v.value) === String(selected)) ?
				'selected' :
				'';

			html += `<option value="${v.value}" ${selectedAttr}>
                    ${v.label}
                 </option>`;
		});

		return html;
	}
	function getIsActiveOptions(selectedValue) {

    selectedValue = (selectedValue || '').toString().trim().toUpperCase();

    if (selectedValue !== 'Y' && selectedValue !== 'N') {
        selectedValue = 'Y';
    }

    return `
        <option value="Y" ${selectedValue === 'Y' ? 'selected' : ''}>Yes</option>
        <option value="N" ${selectedValue === 'N' ? 'selected' : ''}>No</option>
    `;
}


	// let rcFileURL = "";
	// $("#RCBook").on("change", function(e) {
	// 	if (this.files.length) {
	// 		rcFileURL = URL.createObjectURL(this.files[0]);
	// 		$("#viewRC").show();
	// 	}
	// });
	// $("#viewRC").on("click", function() {
	// 	if (rcFileURL) {
	// 		window.open(rcFileURL, "_blank");
	// 	}
	// });

	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 31 &&
			(charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}

	function validatePAN() {
		const panInput = document.getElementById('Pan');
		if (!panInput) return true;

		let value = panInput.value.trim().toUpperCase();
		panInput.value = value;

		const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]$/;

		if (value === '') {
			panInput.style.border = "";
			return true;
		}

		if (!panRegex.test(value)) {

			alert_float('danger', 'Invalid PAN format (Example: ABCDE1234F)');
			panInput.style.border = "2px solid red";
			panInput.focus(); // focus without blue outline
			return false;
		}

		// valid
		panInput.style.border = "2px solid #28a745";
		return true;
	}

	// PAN Verification on blur event
	$("#Pan").on("blur", function() {
		let panNo = $(this).val().trim();
		// Map 4th PAN character to organisation type and set default
		if (panNo && panNo.length >= 4) {
			let ch = panNo.charAt(3).toUpperCase();
			const panMap = {
				'P': 'Proprietorship',
				'F': 'Partnership',
				'C': 'Private Limited',
				'H': 'Hindu Undivided Family (HUF)',
				'T': 'Society / Trust / Club',
				'G': 'Government Department/Body',
				'L': 'Local Authority'
			};
		}
		if (panNo === "") {
			$(".pan_denger").text("");
			return;
		}

		let panPattern = /^[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}$/;
		if (!panPattern.test(panNo)) {
			$(".pan_denger").text("Invalid PAN format").css("color", "red");
			return;
		}

		

				$.ajax({
					url: "<?php echo admin_url(); ?>VehicleMaster/CheckPanExit",
					dataType: "JSON",
					method: "POST",
					data: {
						Pan: panNo
					},
					beforeSend: function() {
						$('.searchh2').css('display', 'block');
						$('.searchh2').css('color', 'blue');
					},
					complete: function() {
						$('.searchh2').css('display', 'none');
					},
					success: function(data) {
						if (data) {
							alert_float('warning', "Pan Already Exist.");
							$('#Pan').val("").focus();
							return;
						} else {
							let panLabel = $('label').filter(function() {
			return $(this).parent().find('#Pan').length > 0;
		});



							$.ajax({
								url: "<?php echo admin_url('clients/verify_pan'); ?>",
								type: "POST",
								dataType: "json",
								data: {
									pan: panNo.toUpperCase()
								},
								timeout: 30000,
								success: function(res) {
									if (res.status === 'success') {
										$("#AccoountName").val(res.data.full_name || '');
										alert_float('success', 'PAN No verified successfully');

										let panLabel = $('label').filter(function() {
											return $(this).parent().find('#Pan').length > 0;
										});
										if (panLabel) {
											panLabel.html('<small class="req text-danger">* </small>PAN');
										}

										let gstinLabel = document.querySelector('label[for="vat"]') || $(
											'label').filter(function() {
											return $(this).parent().find('#vat').length > 0;
										});
										if (gstinLabel) {
											$(gstinLabel).html(
												'<small class="req text-danger">* </small>GSTIN <span style="color: blue; font-size: 12px;"><i>(Fetching Data...)</i></span>'
											);
										}
									} else {
										$(".pan_denger").html("✗ " + res.message).css("color", "red");
										alert_float('danger', res.message || 'PAN verification failed');

										let panLabel = $('label').filter(function() {
											return $(this).parent().find('#Pan').length > 0;
										});
										if (panLabel) {
											panLabel.html('<small class="req text-danger">* </small>PAN');
										}

										if (res.message === 'Invalid PAN') {
											$("#Pan").val('');
											$(".pan_denger").text("");
										}
									}
								},
								error: function(xhr, status, error) {
									$(".pan_denger").html("✗ Verification failed").css("color", "red");
									alert_float('danger', 'PAN verification failed');

									let panLabel = $('label').filter(function() {
										return $(this).parent().find('#Pan').length > 0;
									});
									if (panLabel) {
										panLabel.html('<small class="req text-danger">* </small>PAN');
									}
								}
							});
						}
					}
				});
	
	});

	$(document).on('change', '#IsActive', function() {

		applyVehicleActiveState();

	});

	function applyVehicleActiveState() {

    let parentValue = $('#IsActive').val();

    $('select[name="VehicleIsActive[]"]').each(function() {

        let currentValue = $(this).val();

        if (parentValue === 'N') {

            // Parent inactive → force all to N
            $(this).val('N');
            $(this).prop('disabled', true);

        } else {

            // Parent active
            $(this).prop('disabled', false);

            // ✅ If value is blank → default to Y
            if (!currentValue) {
                $(this).val('Y');
            }
        }

        $(this).selectpicker('refresh');

    });

}



	$('.updateBtn').hide();
	$('.updateBtn2').hide();
	// Handle filter dropdowns outside table

	function getSubGroupsByMain(Tdsselection) {
		$.ajax({
			url: "<?php echo admin_url(); ?>VehicleMaster/gettdspercent",
			dataType: "JSON",
			method: "POST",
			data: {
				Tdsselection: Tdsselection
			},
			beforeSend: function() {
				$('.searchh2').css('display', 'block').css('color', 'blue');
			},
			complete: function() {
				$('.searchh2').css('display', 'none');
			},
			success: function(data) {
				$('#TdsPercent').empty();


				if (data && data.length > 0) {
					$('#TdsPercent').append('<option value="">Non Selected</option>');
					$.each(data, function(index, item) {
						$('#TdsPercent').append('<option value="' + item.rate + '">' +
							item.rate + '</option>');
					});
				} else {
					$('#TdsPercent').append('<option value="">Non Selected</option>');
				}
				$('.selectpicker').selectpicker('refresh');
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log("AJAX Error:", textStatus, errorThrown);
			}
		});
	}
	$('#Tdsselection').on('change', function() {
		var Tdsselection = $(this).val();
		getSubGroupsByMain(Tdsselection);
	});

	function checkTds() {
		if ($('#Tds').val() === "1") {
			$('#TdsPercent1').show();
			$('#TdsSec').show();
		} else {
			$('#TdsPercent1').hide();
			$('#TdsSec').hide();
		}
	}
	$('#Tds').on('change', function() {
		checkTds();
	});

	$("#AccountID").dblclick(function() {
		$('#Account_List').modal('show');
		$.ajax({
			url: "<?php echo admin_url(); ?>VehicleMaster/GetAllVehicleOwnerList",
			dataType: "JSON",
			method: "POST",
			beforeSend: function() {
				$('.searchh2').css('display', 'block');
				$('.searchh2').css('color', 'blue');
			},
			complete: function() {
				$('.searchh2').css('display', 'none');
			},
			success: function(data) {
				$('#customertlistbody').html(data);
				$('.get_AccountID').on('click', function() {
					AccountID = $(this).attr("data-id");
					$.ajax({
						url: "<?php echo admin_url(); ?>VehicleMaster/GetComprehensiveAccountData",
						dataType: "JSON",
						method: "POST",
						data: {
							AccountID: AccountID
						},
						beforeSend: function() {
							$('.searchh2').css('display', 'block');
							$('.searchh2').css('color', 'blue');
							// Toggle buttons immediately when record is clicked
							$('.saveBtn').hide();
							$('.updateBtn').show();
							$('.saveBtn2').hide();
							$('.updateBtn2').show();
						},
						complete: function() {
							$('.searchh2').css('display', 'none');
						},
						success: function(response) {
							console.log('Comprehensive data response:', response);
							if (response.status === 'success') {
								PopulateFormFromComprehensiveData(response.data);
							} else {
								alert_float('error', response.message || 'Failed to fetch account data');
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log('AJAX Error:', textStatus, errorThrown);
							alert_float('error', 'Error fetching account data');
						}
					});
					$('#Account_List').modal('hide');
				});
			}
		});
		$('#Account_List').on('shown.bs.modal', function() {
			$('#myInput1').val('');
			$('#myInput1').focus();
		})

	});

	// Show All button click - fetch and show all accounts (same behaviour as AccountID dblclick)
	$('.showAllBtn').on('click', function() {
		$('#Account_List').modal('show');
		$.ajax({
			url: "<?php echo admin_url(); ?>VehicleMaster/GetAllVehicleOwnerList",
			dataType: "JSON",
			method: "POST",
			beforeSend: function() {
				$('.searchh2').css('display', 'block');
				$('.searchh2').css('color', 'blue');
			},
			complete: function() {
				$('.searchh2').css('display', 'none');
			},
			success: function(data) {
				$('#customertlistbody').html(data);
				$('.get_AccountID').on('click', function() {
					AccountID = $(this).attr("data-id");
					$.ajax({
						url: "<?php echo admin_url(); ?>VehicleMaster/GetComprehensiveAccountData",
						dataType: "JSON",
						method: "POST",
						data: {
							AccountID: AccountID
						},
						beforeSend: function() {
							$('.searchh2').css('display', 'block');
							$('.searchh2').css('color', 'blue');
							// Toggle buttons immediately when record is clicked
							$('.saveBtn').hide();
							$('.updateBtn').show();
							$('.saveBtn2').hide();
							$('.updateBtn2').show();
						},
						complete: function() {
							$('.searchh2').css('display', 'none');
						},
						success: function(response) {
							console.log('Comprehensive data response:', response);
							if (response.status === 'success') {
								PopulateFormFromComprehensiveData(response.data);
							} else {
								alert_float('error', response.message || 'Failed to fetch account data');
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.log('AJAX Error:', textStatus, errorThrown);
							alert_float('error', 'Error fetching account data');
						}
					});
					$('#Account_List').modal('hide');
				});
			}
		});

		$('#Account_List').on('shown.bs.modal', function() {
			$('#myInput1').val('');
			$('#myInput1').focus();
		});
	});

	$("#AccoountName").keypress(function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode == "") {
			$("#lblError").html("");
		} else {
			var regex = /^[A-Za-z0-9\s]+$/;
			var isValid = regex.test(String.fromCharCode(keyCode));
			return isValid;
		}
	});

	$("#AccountID").keypress(function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode == "") {
			$("#lblError").html("");
		} else {
			var regex = /^[A-Za-z0-9]+$/;
			var isValid = regex.test(String.fromCharCode(keyCode));
			return isValid;
		}
	});

	function fetchAccountHolderName() {
		var bank_ac_no = $('#account_number').val();
		var ifsc_code = $('#ifsc_code').val();
		if (bank_ac_no != '' && ifsc_code != '') {
			$.ajax({
				url: "<?php echo admin_url(); ?>VehicleMaster/verifyBankAccount",
				method: "POST",
				dataType: 'json',
				data: {
					bank_ac_no: bank_ac_no,
					ifsc_code: ifsc_code
				},
				beforeSend: function() {
					$('.searchh2').css('display', 'block');
					$('.searchh2').css('color', 'blue');
				},
				complete: function() {
					$('.searchh2').css('display', 'none');
				},
				success: function(data) {
					if (data.success == false) {
						alert_float('danger', "Bank account not verified");
						$('#account_holder_name').val('');
						$('#account_number').val('');
					} else {
						$('#account_holder_name').val(data.data.full_name);
					}
				}
			});
		}
	}

	/**
	 * Populate form with comprehensive account data from all tables
	 * @param {Object} comprehensiveData - Contains clientDetails, shippingData, bankData, contactData
	 */
	function PopulateFormFromComprehensiveData(comprehensiveData) {
		try {
			// First, clear all existing data
			ResetForm();

			// Log incoming data for debugging
			console.log('=== COMPREHENSIVE DATA RECEIVED ===');
			console.log('Full data object:', comprehensiveData);
			console.log('Clients data:', comprehensiveData.clientDetails);
			console.log('Bank data:', comprehensiveData.bankData);
			console.log('Vehicle data:', comprehensiveData.vehicle);
			console.log('===================================');

			// 1. POPULATE CLIENT DETAILS (from tblclients)
			if (comprehensiveData && comprehensiveData.clientDetails) {
				var client = comprehensiveData.clientDetails;

				$('#AccountID').val(client.AccountID || '');
				$('#AccoountName').val(client.company || '');
				$('#Pan').val(client.PAN || '');
				$('#phonenumber').val(client.MobileNo || '');

				// Other fields
				$('#Tds').val(client.IsTDS == 'Y' ? '1' : '0');
				checkTds(); // Ensure visibility is toggled

				if (client.IsTDS == 'Y') {
					$('#Tdsselection').val(client.TDSSection || '');
					$('.selectpicker').selectpicker('refresh');

					// Fetch TDS Percent options asynchronously
					var selectedTdsSection = client.TDSSection;
					if (selectedTdsSection) {
						$.ajax({
							url: "<?php echo admin_url(); ?>VehicleMaster/gettdspercent",
							dataType: "JSON",
							method: "POST",
							data: {
								Tdsselection: selectedTdsSection
							},
							success: function(data) {
								$('#TdsPercent').empty();
								if (data && data.length > 0) {
									$('#TdsPercent').append('<option value="">Non Selected</option>');
									$.each(data, function(index, item) {
										$('#TdsPercent').append('<option value="' + item.rate + '">' +
											item.rate + '</option>');
									});
								} else {
									$('#TdsPercent').append('<option value="">Non Selected</option>');
								}

								// Set the value AFTER options are populated
								if (client.TDSPer) {
									$('#TdsPercent').val(client.TDSPer);
								}
								$('#TdsPercent').selectpicker('refresh');
							}
						});
					}
				}

				$('#IsActive').val(client.IsActive || '');
				$('#IsActive').selectpicker('refresh');

				// Populate Vehicle Owner Category (groups_in) from ActSubGroupID2
				// ActSubGroupID2 is the transporter/customer category
				if (client.ActSubGroupID2) {
					$('#groups_in').selectpicker('val', client.ActSubGroupID2);
					// Disable the groups_in dropdown in edit mode (prevent category change)
					$('#groups_in').prop('disabled', true);
					console.log('Transport Category populated: ' + client.ActSubGroupID2);
				}

				// Refresh selectpicker for dropdowns
				$('.selectpicker').selectpicker('refresh');

				console.log('Client details populated successfully');
			}

			// 2. POPULATE BANK DETAILS (from tblBankMaster)
			if (comprehensiveData && comprehensiveData.bankData && comprehensiveData.bankData.length > 0) {
				// Use first bank record (modify if multiple banks needed)
				var bank = comprehensiveData.bankData[0];

				$('#ifsc_code').val(bank.IFSC || '');
				$('#bank_name').val(bank.BankName || '');
				$('#branch_name').val(bank.BranchName || '');
				$('#bank_address').val(bank.BankAddress || '');
				$('#account_number').val(bank.AccountNo || '');
				$('#account_holder_name').val(bank.HolderName || '');

				console.log('Bank details populated successfully');
			} else {
				$('#ifsc_code').val('');
				$('#bank_name').val('');
				$('#branch_name').val('');
				$('#bank_address').val('');
				$('#account_number').val('');
				$('#account_holder_name').val('')

				alert_float('Warning', 'Bank Details Not Populated');
			}



if (comprehensiveData && comprehensiveData.vehicle && comprehensiveData.vehicle.length > 0) {

    // Remove all rows except emptyRow
    $("#contacttbody tr").not(".emptyRow").remove();

    comprehensiveData.vehicle.forEach(function(vehicle) {

        var newRow = $("<tr class='addedVehicleRow'></tr>");

        var idField = vehicle.id
            ? "<input type='hidden' name='id[]' value='" + vehicle.id + "'>"
            : "<input type='hidden' name='id[]' value=''>";

        newRow.append("<td>" + idField +
            "<input type='text' name='VehicleNo[]' class='form-control' value='" + (vehicle.VehicleNo || '') + "'></td>");

        newRow.append("<td><select class='selectpicker form-control' name='PreferTransport[]' data-width='100%' data-container='body'>" +
            getPositionOptions(vehicle.TransporterID) + "</select></td>");

        newRow.append("<td><input type='text' name='DriverName[]' class='form-control' value='" + (vehicle.DriverName || '') + "'></td>");

        newRow.append("<td><input type='text' name='DriverMobile[]' class='form-control' value='" +
            (vehicle.DriverMobileNo || '') +
            "' oninput='this.value = this.value.replace(/\\D/g, \"\")' maxlength='10'></td>");

        newRow.append("<td><input type='text' name='LicenceNo[]' class='form-control' value='" +
            (vehicle.LicenceNo || '') + "'></td>");

        newRow.append(`
            <td>
                <input type="file" name="RCBook[]" class="rc_file" style="display:none">

                <button type="button" class="btn btn-info btn-sm uploadBtn">
                    <i class="fa fa-upload"></i>
                </button>

                ${vehicle.RcBook ? `
                    <button type="button" class="btn btn-secondary btn-sm"
                        onclick="window.open('${BASE_URL + vehicle.RcBook}','_blank')">
                        <i class="fa fa-eye"></i>
                    </button>` : ""}
            </td>
        `);

        newRow.append("<td><input type='text' name='Capacity[]' class='form-control' value='" +
            (vehicle.Capacity || '') + "'></td>");

        newRow.append("<td><select class='selectpicker' name='VehicleType[]' data-width='100%' data-container='body'>" +
            getVehicleOptions(vehicle.VehicleType) + "</select></td>");

        newRow.append("<td><select name='VehicleIsActive[]' class='selectpicker' data-width='100%' data-container='body'>" +
           getIsActiveOptions(vehicle.IsActive)+ "</select></td>");

        newRow.append("<td><button type='button' class='btn btn-danger removeVehicleRow'><i class='fa fa-times'></i></button></td>");

        // 🔥 Append AFTER emptyRow
        $("#contacttbody .emptyRow").after(newRow);

        let normalizedValue = (vehicle.IsActive || '').toString().trim().toUpperCase();

if (normalizedValue !== 'Y' && normalizedValue !== 'N') {
    normalizedValue = 'Y';
}

newRow.find("select[name='VehicleIsActive[]']")
      .val(normalizedValue);

newRow.find('.selectpicker').selectpicker('refresh');

    });

    // 🔥 Make sure empty row is cleared
    $(".emptyRow").find("input").val('');
    $(".emptyRow").find("select").val('').selectpicker('refresh');

    console.log('Vehicle details populated from second row successfully');
}


			console.log('Form populated completely with all account data');
			alert_float('success', 'Account details loaded successfully');

			// Toggle buttons - show UPDATE, hide SAVE
			$('.saveBtn').hide();
			$('.updateBtn').show();
			$('.saveBtn2').hide();
			$('.updateBtn2').show();

		} catch (error) {
			console.error('Error populating form:', error);
			alert_float('error', 'Error loading account data: ' + error.message);
		}
		applyVehicleActiveState();
	}


	$('#ifsc_code').blur(function() {
		var ifsc_code = $('#ifsc_code').val();
		if (ifsc_code != '') {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/fetchBankDetailsFromIFSC",
				method: "POST",
				dataType: 'json',
				data: {
					ifsc_code: ifsc_code
				},
				beforeSend: function() {
					$('.searchh2').css('display', 'block');
					$('.searchh2').css('color', 'blue');
				},
				complete: function() {
					$('.searchh2').css('display', 'none');
				},
				success: function(data) {
					if (data == "Not Found") {
						alert_float('danger', "Enter valid IFSC Code");
						$('#bank_name').val("");
						$('#branch_name').val("");
						$('#bank_address').val("");
					} else {
						$('#bank_name').val(data.BANK);
						$('#branch_name').val(data.BRANCH);
						$('#bank_address').val(data.ADDRESS);
						fetchAccountHolderName();
					}
				}
			});
		}
	});

	$('#account_number').blur(function() {
		fetchAccountHolderName();
	});

	function ResetForm() {
		var HiddenAccountID = $('#HiddenAccountID').val();
		$('#AccountID').val(HiddenAccountID);
		$('#AccoountName').val('');
		$('#phonenumber').val('');
		$('#Pan').val('');
		$('#groups_in').val('');
		$('#IsActive').val('Y').selectpicker('refresh');


		$('#ifsc_code').val('');
		$('#bank_name').val('');
		$('#branch_name').val('');
		$('#bank_address').val('');
		$('#account_number').val('');
		$('#account_holder_name').val('');

		$("#VehicleNo").val('');
		$("#PreferTransport").val('').selectpicker('refresh');
		$("#DriverName").val('');
		$("#DriverMobile").val('');
		$("#LicenceNo").val('');
		$("#RCBook").val('');
		$("#Capacity").val('');
		$("#VehicleType").val('').selectpicker('refresh');
		$('#VehicleIsActive').val('Y').selectpicker('refresh').prop('disabled', false);

		// $('select[name=IsActive]').val('Y');
		$('select[name=Tds]').val('0');
		$('#TdsPercent1').hide();
		$('#TdsSec').hide();
		$('select[name=Tdsselection]').val('');



		// Reset transporter category and ensure it's enabled in create/new mode
		$('#groups_in').prop('disabled', false);
		$('#groups_in').selectpicker('val', '');
		$('.selectpicker').selectpicker('refresh');
		
		let emptyRow = $("#contacttbody .emptyRow");
		$("#contacttbody tr").not(".emptyRow").remove();
		emptyRow.find(".viewBtn").hide();




		$('.saveBtn').show();
		$('.updateBtn').hide();
		$('.saveBtn2').show();
		$('.updateBtn2').hide();
	}

	// Clear the entire form including HiddenAccountID and AccountID
	function ClearFormCompletely() {
		ResetForm();
		$('#HiddenAccountID').val('');
		$('#AccountID').val('');
		$('.selectpicker').selectpicker('refresh');
	}

	$(".cancelBtn").click(function() {
		ResetForm();
	});


	$('#groups_in').on('change', function() {
		var selectedVehicleOwnerType = $(this).val();

		// Clear if nothing selected
		if (!selectedVehicleOwnerType) {
			$('#AccountID').val('');
			$('#HiddenVehicleOwnerCode').val('');
			return;
		}

		var $select = $(this);
		$select.prop('disabled', true);

		$.ajax({
			url: "<?php echo admin_url('VehicleMaster/GetNextVehicleOwnerCode'); ?>",
			method: 'POST',
			dataType: 'json',
			data: {
				ActSubGroupID2: selectedVehicleOwnerType
			},
			success: function(response) {
				if (response && response.status === 'success' && response.next_code) {
					$('#AccountID').val(response.next_code);
					$('#HiddenVehicleOwnerCode').val(response.ActSubGroupID2 || selectedVehicleOwnerType);
					console.log('Vehicle Owner Code generated: ' + response.next_code + ' (Vehicle: ' + (
							response.VehicleOwner_name || '') + ', Count: ' + (response.count || '') +
						')');
				} else {
					$('#AccountID').val('');
					$('#HiddenVehicleOwnerCode').val('');
					alert_float('warning', (response && response.message) ? response.message :
						'Failed to generate customer code');
				}
			},
			error: function(xhr) {
				console.error('Error generating customer code:', xhr.responseText || xhr.statusText);
				alert_float('error', 'Error generating customer code. Please try again.');
				$('#AccountID').val('');
				$('#HiddenVehicleOwnerCode').val('');
			},
			complete: function() {
				$select.prop('disabled', false);
			}
		});
	});

	$('.saveBtn').on('click', function() {
		groups_in = $('#groups_in').val();
		AccountID = $('#AccountID').val();
		AccoountName = $('#AccoountName').val();
		Pan = $('#Pan').val();
		phonenumber = $('#phonenumber').val();

		Tds = $('#Tds').val();
		TdsPercent = '';
		Tdsselection = '';
		if (Tds === '1') {
			TdsPercent = $('#TdsPercent').val();
			Tdsselection = $('#Tdsselection').val();
		}

		let Vehicle = [];
		var formData = new FormData();


		$("#contacttbody tr").each(function(index) {

			let row = $(this);

			let VehicleNo = row.find("#VehicleNo, input[name='VehicleNo[]']").val();
			let PreferTransport = row.find("#PreferTransport, select[name='PreferTransport[]']").val();
			let DriverName = row.find("#DriverName, input[name='DriverName[]']").val();
			let DriverMobile = row.find("#DriverMobile, input[name='DriverMobile[]']").val();
			let LicenceNo = row.find("#LicenceNo, input[name='LicenceNo[]']").val();
			let Capacity = row.find("#Capacity, input[name='Capacity[]']").val();
			let VehicleType = row.find("#VehicleType, select[name='VehicleType[]']").val();
			// let VehicleIsActive = row.find("#VehicleIsActive, input[name='VehicleIsActive[]']").val();

			let VehicleIsActive = row.find("#VehicleIsActive, select[name='VehicleIsActive[]']").val();


			if (!VehicleNo) return;

			Vehicle.push({
				VehicleNo,
				PreferTransport,
				DriverName,
				DriverMobile,
				LicenceNo,
				Capacity,
				VehicleType,
				VehicleIsActive
			});
			// Upload RC file separately
			let fileInput = row.find(".rc_file")[0];

			if (fileInput && fileInput.files.length > 0) {
				formData.append("RCBook[" + index + "]", fileInput.files[0]);
			}
		});

		let VehicleData = JSON.stringify(Vehicle);

		ifsc_code = $('#ifsc_code').val();
		bank_name = $('#bank_name').val();
		branch_name = $('#branch_name').val();
		bank_address = $('#bank_address').val();
		account_number = $('#account_number').val();
		account_holder_name = $('#account_holder_name').val();

		IsActive = $('#IsActive').val();

		if (AccountID == '') {
			alert_float('warning', 'please enter Customer id');
			$('#AccountID').focus();
		} else if ($.trim(AccoountName) == '') {
			alert_float('warning', 'please enter Customer Name');
			$('#AccountName').focus();
		} else if (!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#Pan').val() !== "") {
			alert_float('warning', 'Enter valid PAN number');
			$('#Pan').focus();
		} else if (phonenumber == '') {
			alert_float('warning', 'please  enter mobile number');
			$('#phonenumber').focus();
		} else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
			alert_float('warning', 'Enter valid Mobile number');
			$('#phonenumber').focus();
		}
		//  else if (Vehicle.length === 0) {
		// 	alert_float('warning', 'Please add at least one Contact Detail');
		// 	$('#contacttbody').focus();
		// }
		else {
			// Use FormData to include file attachment
			formData.append('AccountID', AccountID);
			formData.append('AccoountName', AccoountName);
			formData.append('phonenumber', phonenumber);
			formData.append('groups_in', groups_in);
			formData.append('Pan', Pan);
			formData.append('IsActive', IsActive);

			formData.append('ifsc_code', ifsc_code);
			formData.append('bank_name', bank_name);
			formData.append('branch_name', branch_name);
			formData.append('account_number', account_number);
			formData.append('bank_address', bank_address);
			formData.append('account_holder_name', account_holder_name);
			formData.append('Tds', Tds);
			formData.append('TdsPercent', TdsPercent);
			formData.append('Tdsselection', Tdsselection);

			formData.append('VehicleData', VehicleData);


			// CSRF token (if present)
			var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
			var csrfVal = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';
			formData.append(csrfName, csrfVal);

			$.ajax({
				url: "<?php echo admin_url(); ?>VehicleMaster/SaveAccountID",
				dataType: "JSON",
				method: "POST",
				data: formData,
				processData: false,
				contentType: false,
				beforeSend: function() {
					$('.searchh3').css('display', 'block');
					$('.searchh3').css('color', 'blue');
				},
				complete: function() {
					$('.searchh3').css('display', 'none');
				},
				success: function(data) {
					if (data && data.success) {
						$('#HiddenAccountID').val(data.account_id || '');

						alert_float('success', data.message || 'Record created successfully...');
						ResetForm();
						// $("#contacttbody .addedVehicleRow").remove();
						$("#contacttbody tr").not(".emptyRow").remove();
						// $("#viewRC").hide();


					} else {
						var msg = (data && data.message) ? data.message : 'Something went wrong...';
						alert_float('warning', msg);
						ResetForm();
					}
				},
				error: function(xhr, status, err) {
					console.error('SaveAccountID error', status, err, xhr.responseText);
					var errorMsg = 'Error saving record';
					try {
						if (xhr.responseText) {
							var response = JSON.parse(xhr.responseText);
							if (response.message) {
								errorMsg = response.message;
							}
						}
					} catch (e) {
						errorMsg = xhr.responseText || 'Error saving record';
					}
					alert_float('danger', errorMsg);
					ResetForm();
				}
			});
		}
	});

	$('.updateBtn').on('click', function() {
		AccountID = $('#AccountID').val();
		AccoountName = $('#AccoountName').val();
		phonenumber = $('#phonenumber').val();
		groups_in = $('#groups_in').val();
		Pan = $('#Pan').val();
		IsActive = $('#IsActive').val();

		ifsc_code = $('#ifsc_code').val();
		bank_name = $('#bank_name').val();
		branch_name = $('#branch_name').val();
		bank_address = $('#bank_address').val();
		account_number = $('#account_number').val();
		account_holder_name = $('#account_holder_name').val();

		Tds = $('#Tds').val();
		TdsPercent = '';
		Tdsselection = '';
		if (Tds === '1') {
			TdsPercent = $('#TdsPercent').val();
			Tdsselection = $('#Tdsselection').val();
		}

		let Vehicle = [];
		var formDataUpd = new FormData();


		$("#contacttbody tr").each(function(index) {

			let row = $(this);

			let VehicleNo = row.find("#VehicleNo, input[name='VehicleNo[]']").val();
			let PreferTransport = row.find("#PreferTransport, select[name='PreferTransport[]']").val();
			let DriverName = row.find("#DriverName, input[name='DriverName[]']").val();
			let DriverMobile = row.find("#DriverMobile, input[name='DriverMobile[]']").val();
			let LicenceNo = row.find("#LicenceNo, input[name='LicenceNo[]']").val();
			let Capacity = row.find("#Capacity, input[name='Capacity[]']").val();
			let VehicleType = row.find("#VehicleType, select[name='VehicleType[]']").val();
			// let VehicleIsActive = row.find("#VehicleIsActive, input[name='VehicleIsActive[]']").val();

			let VehicleIsActive = row.find("#VehicleIsActive, select[name='VehicleIsActive[]']").val();


			if (!VehicleNo) return;

			Vehicle.push({
				VehicleNo,
				PreferTransport,
				DriverName,
				DriverMobile,
				LicenceNo,
				Capacity,
				VehicleType,
				VehicleIsActive
			});
			// Upload RC file separately
			let fileInput = row.find(".rc_file")[0];

			if (fileInput && fileInput.files.length > 0) {
				formDataUpd.append("RCBook[" + index + "]", fileInput.files[0]);
			}
		});

		let VehicleData = JSON.stringify(Vehicle);

		if (AccountID == '') {
			alert_float('warning', 'please enter AccountID');
			$('#AccountID').focus();
		} else if ($.trim(AccoountName) == '') {
			alert_float('warning', 'please enter Account Name');
			$('#AccountName').focus();
		} else if (!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#Pan').val() !== "") {
			alert_float('warning', 'Enter valid PAN number');
			$('#Pan').focus();
		} else if (phonenumber == '') {
			alert_float('warning', 'please  enter mobile number');
			$('#phonenumber').focus();
		} else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
			alert_float('warning', 'Enter valid Mobile number');
			$('#phonenumber').focus();
		}
		// else if (Vehicle.length === 0) {
		// 	alert_float('warning', 'Please add at least one Vehicle Detail');
		// 	$('#contacttbody').focus();
		// }
		else {
			// Use FormData to include file attachment for update
			// var formDataUpd = new FormData();
			formDataUpd.append('AccountID', AccountID);
			formDataUpd.append('AccoountName', AccoountName);
			formDataUpd.append('phonenumber', phonenumber);
			formDataUpd.append('groups_in', groups_in);
			formDataUpd.append('Pan', Pan);
			formDataUpd.append('IsActive', IsActive);

			formDataUpd.append('ifsc_code', ifsc_code);
			formDataUpd.append('bank_name', bank_name);
			formDataUpd.append('branch_name', branch_name);
			formDataUpd.append('bank_address', bank_address);
			formDataUpd.append('account_number', account_number);
			formDataUpd.append('account_holder_name', account_holder_name);

			formDataUpd.append('Tds', Tds);
			formDataUpd.append('TdsPercent', TdsPercent);
			formDataUpd.append('Tdsselection', Tdsselection);

			formDataUpd.append('VehicleData', VehicleData);



			var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
			var csrfVal = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';
			formDataUpd.append(csrfName, csrfVal);

			$.ajax({
				url: "<?php echo admin_url(); ?>VehicleMaster/UpdateAccountID",
				dataType: "JSON",
				method: "POST",
				data: formDataUpd,
				processData: false,
				contentType: false,
				beforeSend: function() {
					$('.searchh4').css('display', 'block');
					$('.searchh4').css('color', 'blue');
				},
				complete: function() {
					$('.searchh4').css('display', 'none');
				},
				success: function(data) {
					if (data && data.success) {
						// Show success toast for updated record
						alert_float('success', data.message || 'Record updated successfully...');
						ResetForm();
						// $("#contacttbody .addedVehicleRow").remove();
						$("#contacttbody tr").not(".emptyRow").remove();
						// $("#viewRC").hide();



					} else {
						var msg = (data && data.message) ? data.message : 'Something went wrong...';
						alert_float('warning', msg);
						ResetForm();
					}
				},
				error: function(xhr, status, err) {
					console.error('UpdateAccountID error', status, err, xhr.responseText);
					var errorMsg = 'Error updating record';
					try {
						if (xhr.responseText) {
							var response = JSON.parse(xhr.responseText);
							if (response.message) {
								errorMsg = response.message;
							}
						}
					} catch (e) {
						errorMsg = xhr.responseText || 'Error updating record';
					}
					alert_float('danger', errorMsg);
					ResetForm();
				}
			});
		}
	});

	$(document).on("click", ".uploadBtn", function() {
    $(this).siblings(".rc_file").click();
});


	$(document).on("change", ".rc_file", function() {


		let btn = $(this).siblings(".viewBtn");

		btn.data("url", URL.createObjectURL(this.files[0]));

		btn.show();

	});

	$(document).on("click", ".viewBtn", function() {

		window.open($(this).data("url"), "_blank");

	});

$(document).on("click", ".addMoreRow", function () {

    let emptyRow = $("#contacttbody .emptyRow");

    let VehicleNo = emptyRow.find("#VehicleNo").val();
    let PreferTransport = emptyRow.find("#PreferTransport").val();
    let DriverName = emptyRow.find("#DriverName").val();
    let DriverMobile = emptyRow.find("#DriverMobile").val();
    let LicenceNo = emptyRow.find("#LicenceNo").val();
    let Capacity = emptyRow.find("#Capacity").val();
    let VehicleType = emptyRow.find("#VehicleType").val();
    let VehicleIsActive = emptyRow.find("#VehicleIsActive").val();

    let rcInput = emptyRow.find(".rc_file")[0];
    let rcFile = rcInput.files.length ? rcInput.files[0] : null;

    if (!VehicleNo) {
        alert_float('warning', 'Please enter Vehicle No');
        return;
    }


    let newRow = `
        <tr class="addedVehicleRow">
            <td><input type="text" name="VehicleNo[]" value="${VehicleNo}" class="form-control"></td>

            <td>
                <select class="selectpicker" name="PreferTransport[]" data-width="100%" data-container="body">
                    ${getPositionOptions(PreferTransport)}
                </select>
            </td>

            <td><input type="text" name="DriverName[]" value="${DriverName}" class="form-control"></td>

            <td><input type="text" name="DriverMobile[]" value="${DriverMobile}" class="form-control" maxlength="10"></td>

            <td><input type="text" name="LicenceNo[]" value="${LicenceNo}" class="form-control"></td>

            <td>
                <input type="file" name="RCBook[]" class="rc_file" style="display:none">

                <button type="button" class="btn btn-info btn-sm uploadBtn">
                    <i class="fa fa-upload"></i>
                </button>

                <button type="button" class="btn btn-secondary btn-sm viewBtn" style="display:${rcFile ? 'inline-block' : 'none'}">
                    <i class="fa fa-eye"></i>
                </button>
            </td>

            <td><input type="text" name="Capacity[]" value="${Capacity}" class="form-control"></td>

            <td>
                <select class="selectpicker" name="VehicleType[]" data-width="100%" data-container="body">
                    ${getVehicleOptions(VehicleType)}
                </select>
            </td>

            <td>
                <select class="selectpicker" name="VehicleIsActive[]" data-width="100%" data-container="body">
                    ${getIsActiveOptions(VehicleIsActive)}
                </select>
            </td>

            <td>
                <button type="button" class="btn btn-danger removeVehicleRow">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    `;

    $("#contacttbody").append(newRow);


    // Move file input element itself
if (rcFile) {
    let newRowElement = $("#contacttbody tr:last");
    // Move the actual file input to new row
    newRowElement.find(".rc_file").replaceWith($(rcInput));
    // Show view button
    newRowElement.find(".viewBtn")
        .data("url", URL.createObjectURL(rcFile))
        .show();
    // Add fresh empty file input back to emptyRow
    emptyRow.find("td:eq(5)").prepend(
        `<input type="file" class="rc_file" name="RCBook" style="display:none">`
    );
	emptyRow.find(".viewBtn").hide();
}

    //  Clear empty row
    emptyRow.find("input").val('');
    emptyRow.find("select").val('').selectpicker('refresh');
	emptyRow.find("#VehicleIsActive").val('Y');
    emptyRow.find(".rc_file").val('');
    $("#viewRC").hide();

    $('.selectpicker').selectpicker('refresh');
});


	$(document).on("click", ".removeVehicleRow", function() {
		$(this).closest("tr").remove();
	});

	function markInvalid(field) {

		field.addClass("is-invalid");

		field.focus();

		return false;

	}

	function clearInvalid(field) {

		field.removeClass("is-invalid");

	}


	$(document).on("click", ".sortablePop", function () {

    var table = $("#table_Account_List tbody");
    var rows = table.find("tr").toArray();
    var index = $(this).index();
    var ascending = !$(this).hasClass("asc");

    $(".sortablePop").removeClass("asc desc");
    $(".sortablePop span").remove();

    $(this).addClass(ascending ? "asc" : "desc");
    $(this).append(
        ascending
            ? ' <span>▲</span>'
            : ' <span>▼</span>'
    );

    rows.sort(function (a, b) {

        var A = $(a).children("td").eq(index).text().toUpperCase();
        var B = $(b).children("td").eq(index).text().toUpperCase();

        if ($.isNumeric(A) && $.isNumeric(B)) {
            return ascending ? A - B : B - A;
        } else {
            if (A < B) return ascending ? -1 : 1;
            if (A > B) return ascending ? 1 : -1;
            return 0;
        }
    });

    $.each(rows, function (i, row) {
        table.append(row);
    });
});

</script>
<script>
	const BASE_URL = "<?= base_url(); ?>";


	// Remove Click
	$(document).on("click", ".removeVehicleRow", function() {
		$(this).closest("tr").remove();
		$("#viewRC").hide();
	});
</script>
<style>
	.is-invalid {
		border: 2px solid red !important;
	}

	#AccountID {
		text-transform: uppercase;
	}

	#Pan {
		text-transform: uppercase;
	}

	#Pan:focus {
		box-shadow: none !important;
	}


	#vat {
		text-transform: uppercase;
	}

	#table_Account_List td:hover {
		cursor: pointer;
	}

	#table_Account_List tr:hover {
		background-color: #ccc;
	}

	.table-Account_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-Account_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-Account_List tbody th {
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

	#contactTable th,
	#contactTable td {
		width: 150px;
		max-width: 150px;
	}

	#contactTable th:nth-child(1),
	#contactTable td:nth-child(1) {
		width: 50px;
		max-width: 50px;
	}

	#contactTable th:nth-child(2),
	#contactTable td:nth-child(2) {
		width: 65px;
		max-width: 65px;
	}

	#contactTable th:nth-child(3),
	#contactTable td:nth-child(3) {
		width: 100px;
		max-width: 100px;
	}

	#contactTable th:nth-child(4),
	#contactTable td:nth-child(4) {
		width: 52px;
		max-width: 52px;
	}

	#contactTable th:nth-child(5),
	#contactTable td:nth-child(5) {
		width: 72px;
		max-width: 72px;
	}

	#contactTable th:nth-child(6),
	#contactTable td:nth-child(6) {
		width: 33px;
		max-width: 33px;
	}

	#contactTable th:nth-child(7),
	#contactTable td:nth-child(7) {
		width: 40px;
		max-width: 40px;
	}


	#contactTable th:nth-child(8),
	#contactTable td:nth-child(8) {
		width: 80px;
		max-width: 80px;
	}

	#contactTable th:nth-child(9),
	#contactTable td:nth-child(9) {
		width: 50px;
		max-width: 50px;
	}

	#contactTable th:nth-child(10),
	#contactTable td:nth-child(10) {
		width: 16px;
		max-width: 16px;
	}

	#person_table thead,
	#person_table tbody tr {
		display: table;
		width: 100%;
		table-layout: fixed;
	}

	#person_table tbody {
		display: block;
		max-height: 252px;
		/* approx height for 10 rows */
		overflow-y: scroll;
	}

	#person_table thead {
		width: 100%;
	}

	#person_table .checkbox-col {
		width: 40px;
		/* adjust: 30–50px */
		text-align: center;
	}

	#person_table thead th.checkbox-col,
	#person_table tbody td.checkbox-col {
		min-width: 40px;
		max-width: 40px;
	}

	#person_table thead {
		padding-right: 17px;
		/* scrollbar width (Windows) */
	}

	.sticky-actions {
		position: sticky;
		bottom: 0;
		z-index: 10;
		background: #fff;
		padding-top: 10px;
		/* padding-bottom: 10px; */
		border-top: 1px solid #ddd;
	}

	#ifsc_code {
		text-transform: uppercase;
	}
</style>