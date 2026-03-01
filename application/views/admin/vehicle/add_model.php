<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="vehicle_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<span class="edit-title">Edit Vehicle</span>
					<span class="add-title">Add Vehicle</span>
				</h4>
			</div>
			<?php echo form_open('admin/vehicles/manage', array('id' => 'vehicle_form', 'enctype' => 'multipart/form-data')); ?>
			<?php echo form_hidden('itemid'); ?>
			<div class="modal-body" style="padding: 0.5rem 1.25rem;">
				<div class="row">
					<div class="col-md-12">
						<h4 class="bold">Basic Information</h4>
						<hr class="hr_style">
					</div>
					<?php
					$validate = array('required' => true);
					?>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="VehicleOwnerCode">
							<label for="VehicleOwnerCode" class="control-label"><small class="req text-danger">* </small>Vehicle Owner Code</label>
							<input type="text" id="VehicleOwnerCode" name="VehicleOwnerCode" class="form-control" value="<?php $i = 1;
																															echo 'V0' . str_pad($i, 4, '0', STR_PAD_LEFT); ?>" readonly>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="VehicleOwnerName">
							<label for="VehicleOwnerName" class="control-label"><small class="req text-danger">* </small>Vehicle Owner Name</label>
							<input type="text" id="VehicleOwnerName" required name="VehicleOwnerName" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="PAN">
							<label for="PAN" class="control-label">PAN</label>
							<input type="text" id="PAN" name="PAN" class="form-control text-uppercase" maxlength="10" onblur="validatePAN()">
							<small id="panError" class="text-danger" style="display:none;">Invalid PAN format (ABCDE1234F)</small>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="mobile1" class="control-label">Mobile 1</label>
							<input type="text" id="mobile1" name="mobile1" class="form-control" maxlength="10" onkeypress="return isNumberOnly(event)" onblur="validateMobile(this, 'mobile1Error')">
							<small id="mobile1Error" class="text-danger" style="display:none;">Mobile number must be 10 digits</small>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="mobile2" class="control-label">Mobile 2</label>
							<input type="text" id="mobile2" name="mobile2" class="form-control" maxlength="10" onkeypress="return isNumberOnly(event)" onblur="validateMobile(this, 'mobile2Error')">
							<small id="mobile2Error" class="text-danger" style="display:none;">Mobile number must be 10 digits</small>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="Tds">
							<label for="Tds" class="control-label">TDS</label>
							<select name="Tds" id="Tds" class="selectpicker form-control Tds">
								<option value="">Non Selected</option>
								<option value="1">Yes</option>
								<option value="0">No</option>
							</select>
						</div>
					</div>
					<div class="col-md-3" id="TdsSec" style="display:none;">
						<div class="form-group" app-field-wrapper="TdsSec">
							<label for="Tds" class="control-label">Tds Section</label>
							<select class="selectpicker display-block" data-width="100%" name="Tdsselection" id="Tdsselection" data-none-selected-text="Non Selected">
								<option value="">Non Selected</option>
								<?php foreach ($Tdssection as $w): ?>
									<option value="<?= $w['TDSCode'] ?>"><?= $w['TDSName'] ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-3" id="TdsPercent1" style="display:none;">
						<div class="form-group" app-field-wrapper="TdsPercent">
							<label for="Tds" class="control-label">TDS Rate (%)</label>
							<select class="selectpicker display-block" data-width="100%" name="TdsPercent" id="TdsPercent" data-none-selected-text="Non Selected">
								<option value="">Non Selected</option>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-12">
						<h4 class="bold">Vehicle Detail</h4>
						<hr class="hr_style">
						<div class="table-responsive">
							<table width="100%" class="table">
								<thead>
									<tr>
										<th>Vehicle Number</th>
										<th>IFSC Code</th>
										<th>Bank Name</th>
										<th>Branch Name</th>
										<th>Account Number</th>
										<th>Account Holder Name</th>
										<th>Account Type</th>
										<th>Prefer Transporter</th>
										<th>Driver Name</th>
										<th>Mobile No.</th>
										<th>Licence No.</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody id="VehicleDetailtbody">
									<tr>
										<td><input type="text" id="VehicleNo" name="VehicleNo" class="form-control text-uppercase" maxlength="13" onblur="validateVehicleNo()">
											<small id="vehicleError" class="text-danger" style="display:none;">
												Invalid Vehicle Number (Example: KA01AB1234)
											</small>
										</td>
										<td><input type="text" name="IFSC[]" class="form-control text-uppercase ifsc-input" value="" maxlength="11" minlength="11"></td>
										<td><input type="text" name="BankName[]" class="form-control bank-name" readonly></td>
<td><input type="text" name="BranchName[]" class="form-control branch-name" readonly></td>

										<td><input type="text" name="AccountNo" class="form-control account-no" value=""></td>
										<td><input type="text" id="AccountName" name="AccountName" class="form-control" value="" readonly></td>
										<td><input type="text" id="AccountType" name="AccountType" class="form-control text-uppercase" value=""></td>
										<td>
											<select id="PreferTransporter" class="form-control selectpicker"
												data-live-search="true">
												<option value="">None selected</option>
											</select>
										</td>
										<td>
											<select id="DriverID" class="form-control selectpicker"
												data-live-search="true">
												<option value="">None selected</option>
												<?php foreach ($vehicle as $driver) { ?>
													<option value="<?= $driver['VehicleID'] ?>">
														<?= $driver['VehicleID'] . ' ' . $driver['VehicleID'] ?>
													</option>
												<?php } ?>
											</select>
										</td>
										<td><input type="text" id="DriverMobile" name="DriverMobile" class="form-control text-uppercase" value="" maxlength="10"
												onkeypress="return isNumberOnly(event)"
												onblur="validateMobile(this, 'mobile3Error')">
											<small id="mobile3Error" class="text-danger" style="display:none;">
												Mobile number must be 10 digits
											</small>
										</td>
										<td><input type="text" id="LicenceNo" name="LicenceNo" class="form-control text-uppercase" value=""></td>

										<td><a class="btn btn-success" onclick="addVehicleDetail()"><i class="fa fa-plus"></i></a></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>





					<div class="col-md-3">
						<?php
						$value2 = date('d/m/Y');
						//echo render_date_input('StartDate','Start Date',$value2,'text',$validate);
						?>
						<div class="form-group" app-field-wrapper="StartDate">
							<label for="StartDate" class="control-label"><small class="req text-danger">* </small>Start Date</label>
							<div class="input-group date"><input type="text" id="StartDate" name="StartDate" class="form-control datepicker" value="<?= date('d/m/Y'); ?>" required autocomplete="off">
								<div class="input-group-addon"> <i class="fa fa-calendar calendar-icon"></i></div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="form-label"><small class="req text-danger">* </small>Vehicle Type</label>
							<select class="selectpicker" required name="VehicleTypeID" id="VehicleTypeID" data-width="100%" data-none-selected-text="None Selected" data-live-search="true">
								<option value="0">Own</option>
								<option value="1">Transport</option>
								<option value="2">Rental</option>
							</select>
						</div>

					</div>

					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="brand">
							<label for="brand" class="control-label"> <small class="req text-danger">* </small>Brand</label>
							<input type="text" id="brand" name="brand" required class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="model">
							<label for="model" class="control-label"> <small class="req text-danger">* </small>Model</label>
							<input type="text" id="model" name="model" required class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="form-label"><small class="req text-danger">* </small> Fuel Type</label>
							<select class="selectpicker" name="fuel_type" id="fuel_type" data-width="100%" data-none-selected-text="None selected" data-live-search="true" required>
								<option value="CNG">CNG</option>
								<option value="Diesel">Diesel</option>
								<option value="Petrol">Petrol</option>
								<option value="LPG">LPG</option>
								<option value="Electric">Electric</option>
							</select>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="fuel_capacity">
							<label for="fuel_capacity" class="control-label"> <small class="req text-danger">* </small>Fuel Tank Capacity</label>
							<input type="text" id="fuel_capacity" name="fuel_capacity" required onkeypress="return isNumber2(event)" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="mileage">
							<label for="mileage" class="control-label"> <small class="req text-danger">* </small>Mileage</label>
							<input type="text" id="mileage" name="mileage" onkeypress="return isNumber2(event)" required class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="excel_type">
							<label for="excel_type" class="control-label"> <small class="req text-danger">* </small>Excel Type</label>
							<input type="text" id="excel_type" name="excel_type" onkeypress="return isNumber2(event)" required class="form-control" value="">
						</div>
					</div>

					<div class="col-md-3">
						<?php // echo render_input('VehicleCapacity','Crate Capacity','',$validate); 
						?>
						<div class="form-group" app-field-wrapper="VehicleCapacity"><label for="VehicleCapacity" class="control-label">Crate Capacity</label><input type="text" id="VehicleCapacity" onkeypress="return isNumber2(event)" name="VehicleCapacity" class="form-control" value=""></div>
					</div>

					<div class="col-md-3">
						<?php //echo render_input('VehicleCapacityCase','Case Capacity','',$validate); 
						?>
						<div class="form-group" app-field-wrapper="VehicleCapacityCase"><label for="VehicleCapacityCase" class="control-label">Case Capacity</label><input type="Array" id="VehicleCapacityCase" name="VehicleCapacityCase" onkeypress="return isNumber2(event)" class="form-control" value=""></div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="fitnesscertificate">
							<label for="fitnesscertificate" class="control-label">Fitness Certificate</label>
							<input type="file" id="fitnesscertificate" name="fitnesscertificate" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<?php
						//echo render_date_input('fitness_exp_date','Fitness Expiry Date',date('d/m/Y'),'text'); 
						?>
						<div class="form-group" app-field-wrapper="fitness_exp_date"><label for="fitness_exp_date" class="control-label">Fitness Expiry Date</label>
							<div class="input-group date"><input type="text" id="fitness_exp_date" name="fitness_exp_date" class="form-control datepicker" value="<?= date('d/m/Y'); ?>" autocomplete="off">
								<div class="input-group-addon">
									<i class="fa fa-calendar calendar-icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="pollutioncertificate">
							<label for="pollutioncertificate" class="control-label">Pollution Certificate</label>
							<input type="file" id="pollutioncertificate" name="pollutioncertificate" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<?php
						//echo render_date_input('pollution_exp_date','Pollution Expiry Date',date('d/m/Y'),'text'); 
						?>
						<div class="form-group" app-field-wrapper="pollution_exp_date"><label for="pollution_exp_date" class="control-label">Pollution Expiry Date</label>
							<div class="input-group date"><input type="text" id="pollution_exp_date" name="pollution_exp_date" class="form-control datepicker" value="<?= date('d/m/Y'); ?>" autocomplete="off">
								<div class="input-group-addon">
									<i class="fa fa-calendar calendar-icon"></i>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="taxduedate">
							<label for="taxduedate" class="control-label"> <small class="req text-danger">* </small>Motor Vehicle Tax Due Date</label>
							<div class="input-group date">
								<input type="text" id="taxduedate" required name="taxduedate" class="form-control datepicker" value="<?= date('d/m/Y'); ?>" autocomplete="off">
								<div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i></div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="insuranceno">
							<label for="insuranceno" class="control-label"> <small class="req text-danger">* </small>Insurance No.</label>
							<input type="text" id="insuranceno" name="insuranceno" required class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="insurancetakenby">
							<label for="insurancetakenby" class="control-label"> <small class="req text-danger">* </small>Insurance Taken By</label>
							<input type="text" id="insurancetakenby" name="insurancetakenby" required class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="duedate">
							<label for="duedate" class="control-label"> <small class="req text-danger">* </small>Due Date</label>
							<div class="input-group date">
								<input type="text" id="duedate" required name="duedate" class="form-control datepicker" value="<?= date('d/m/Y'); ?>" autocomplete="off">
								<div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i></div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="form-label"><small class="req text-danger">* </small> Is Active? </label>
							<select class="selectpicker" name="ActiveYN" id="ActiveYN" data-width="100%" data-none-selected-text="-- Select --" data-live-search="false" required>
								<option value="1" selected>Available</option>
								<option value="0">Deactive</option>
								<option value="2">In-Maintenance</option>
								<option value="3">Legal</option>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="col-md-12">
						<h4 class="bold">Other Information</h4>
						<hr class="hr_style">
					</div>
					<div class="col-md-6">
						<div class="form-group" app-field-wrapper="InternalRemarks">
							<label for="InternalRemarks" class="control-label">Internal Remarks</label>
							<textarea
								id="InternalRemarks"
								name="InternalRemarks"
								class="form-control"></textarea>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group" app-field-wrapper="DocumentRemarks">
							<label for="DocumentRemarks" class="control-label">Document Remarks</label>
							<textarea
								id="DocumentRemarks"
								name="DocumentRemarks"
								class="form-control"></textarea>
						</div>
					</div>



					<div class="col-md-12">
						<h4 class="bold">Upload Attachmants</h4>
						<hr class="hr_style">
					</div>

					<div class="col-md-4">
						<div class="form-group" app-field-wrapper="rcbook">
							<label for="rcbook" class="control-label">R/C Book</label>
							<input type="file" id="rcbook" name="rcbook" class="form-control">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group" app-field-wrapper="pancard">
							<label for="pancard" class="control-label">PAN Card</label>
							<input type="file" id="pancard" name="pancard" class="form-control">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group" app-field-wrapper="bankdetails">
							<label for="bankdetails" class="control-label">Bank Details</label>
							<input type="file" id="bankdetails" name="bankdetails" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group" app-field-wrapper="tdsdeclaration">
							<label for="tdsdeclaration" class="control-label">TDS Declaration</label>
							<input type="file" id="tdsdeclaration" name="tdsdeclaration" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group" app-field-wrapper="other">
							<label for="other" class="control-label">Other Files</label>
							<input type="file" id="other" name="other" class="form-control" value="">
						</div>
					</div>


					<div class="col-md-12">
						<!--<div class="alert alert-warning affect-warning hide">
							<?php echo _l('changing_items_affect_warning'); ?>
						</div>-->


						<div class="clearfix mbot15"></div>



					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>

<script>
	// Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
	if (typeof(jQuery) != 'undefined') {
		init_item_js();
	} else {
		window.addEventListener('load', function() {
			var initItemsJsInterval = setInterval(function() {
				if (typeof(jQuery) != 'undefined') {
					init_item_js();
					clearInterval(initItemsJsInterval);
				}
			}, 1000);
		});
	}
	// Items add/edit
	function manage_invoice_items(form) {
		var data = $(form).serialize();

		var url = form.action;
		$.post(url, data).done(function(response) {
			response = JSON.parse(response);
			if (response.success == true) {
				/*var item_select = $('#item_select');
					if ($("body").find('.accounting-template').length > 0) {
					if (!item_select.hasClass('ajax-search')) {
                    var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                    if (group.length == 0) {
					var _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
					if (item_select.find('[data-group-id="0"]').length == 0) {
					item_select.find('option:first-child').after(_option);
					} else {
					item_select.find('[data-group-id="0"]').after(_option);
					}
                    } else {
					group.prepend('<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>');
                    }
					}
					if (!item_select.hasClass('ajax-search')) {
                    item_select.selectpicker('refresh');
					} else {
					
                    item_select.contents().filter(function () {
					return !$(this).is('.newitem') && !$(this).is('.newitem-divider');
                    }).remove();
					
                    var clonedItemsAjaxSearchSelect = item_select.clone();
                    item_select.selectpicker('destroy').remove();
                    $("body").find('.items-select-wrapper').append(clonedItemsAjaxSearchSelect);
                    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
					}
					
					add_item_to_preview(response.item.itemid);
				} else {*/
				// Is general items view
				$('.table-vehicle-table').DataTable().ajax.reload(null, false);
				//}
				alert_float('success', response.message);
			}
			$('#vehicle_modal').modal('hide');
		}).fail(function(data) {
			alert_float('danger', data.responseText);
		});
		return false;
	}

	function init_item_js() {
		// Add item to preview from the dropdown for invoices estimates
		$("body").on('change', 'select[name="item_select"]', function() {
			var itemid = $(this).selectpicker('val');
			if (itemid != '') {
				add_item_to_preview(itemid);
			}
		});

		// Items modal show action
		$("body").on('show.bs.modal', '#vehicle_modal', function(event) {

			$('.affect-warning').addClass('hide');

			var $itemModal = $('#vehicle_modal');
			$('input[name="itemid"]').val('');
			$itemModal.find('input').not('input[type="hidden"]').val('');
			$itemModal.find('textarea').val('');
			$itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
			$('select[name="tax2"]').selectpicker('val', '').change();
			$('select[name="tax"]').selectpicker('val', '').change();
			$itemModal.find('.add-title').removeClass('hide');
			$itemModal.find('.edit-title').addClass('hide');

			var id = $(event.relatedTarget).data('id');
			// If id found get the text from the datatable
			if (typeof(id) !== 'undefined') {

				$('.affect-warning').removeClass('hide');
				$('input[name="itemid"]').val(id);

				requestGetJSON('vehicles/get_vehicle_by_id/' + id).done(function(response) {
					$itemModal.find('input[name="VehicleID"]').val(response.VehicleID);
					$itemModal.find('input[name="VehicleCapacity"]').val(response.VehicleCapacity);
					$itemModal.find('input[name="insuranceno"]').val(response.insuranceno);
					$itemModal.find('input[name="insurancetakenby"]').val(response.insurancetakenby);
					var duedate = new Date(response.duedate);
					// Format the date as DD/MM/YYYY
					var duedate = duedate.toLocaleDateString('en-GB');
					$itemModal.find('input[name="duedate"]').val(duedate);

					var taxduedate = new Date(response.taxduedate);
					// Format the date as DD/MM/YYYY
					var taxduedate = taxduedate.toLocaleDateString('en-GB');
					$itemModal.find('input[name="taxduedate"]').val(taxduedate);

					var fitness_exp_date = new Date(response.fitness_exp_date);
					var fitness_exp_date = fitness_exp_date.toLocaleDateString('en-GB');
					$itemModal.find('input[name="fitness_exp_date"]').val(fitness_exp_date);

					var pollution_exp_date = new Date(response.pollution_exp_date);
					var pollution_exp_date = pollution_exp_date.toLocaleDateString('en-GB');
					$itemModal.find('input[name="pollution_exp_date"]').val(pollution_exp_date);

					$itemModal.find('#DriverID').selectpicker('val', response.DriverID);
					$itemModal.find('input[name="brand"]').val(response.brand);
					$itemModal.find('input[name="model"]').val(response.model);
					$itemModal.find('#fuel_type').selectpicker('val', response.fuel_type);
					$itemModal.find('input[name="fuel_capacity"]').val(response.fuel_capacity);
					$itemModal.find('input[name="excel_type"]').val(response.excel_type);
					$itemModal.find('input[name="mileage"]').val(response.mileage);
					$itemModal.find('input[name="VehicleCapacityCase"]').val(response.VehicleCapacityCase);

					var dateObject = new Date(response.StartDate);
					// Format the date as DD/MM/YYYY
					var formattedDate = dateObject.toLocaleDateString('en-GB');
					$itemModal.find('input[name="StartDate"]').val(formattedDate);


					$itemModal.find('#VehicleTypeID').selectpicker('val', response.VehicleTypeID);

					$itemModal.find('#ActiveYN').selectpicker('val', response.ActiveYN);


					init_selectpicker();
					init_color_pickers();
					init_datepicker();

					$itemModal.find('.add-title').addClass('hide');
					$itemModal.find('.edit-title').removeClass('hide');
					// validate_item_form();
				});

			}
		});

		$("body").on("hidden.bs.modal", '#vehicle_modal', function(event) {
			$('#item_select').selectpicker('val', '');
		});

		// validate_item_form();
	}
	// function validate_item_form(){
	// Set validation for invoice item form
	// appValidateForm($('#vehicle_form'), {
	// VehicleID: 'required',
	// VehicleCapacity: 'required',
	// VehicleTypeID: 'required',
	// StartDate: 'required',



	// }, manage_invoice_items);
	// }
</script>

<?php init_tail(); ?>

<script>
	function validatePAN() {
		const panInput = document.getElementById('PAN');
		const error = document.getElementById('panError');

		// User hasn't typed anything yet → don't show error
		if (panInput.value.trim() === '') {
			error.style.display = 'none';
			panInput.classList.remove('is-invalid', 'is-valid');
			return;
		}

		panInput.value = panInput.value.toUpperCase();

		const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

		if (!panRegex.test(panInput.value)) {
			error.style.display = 'block';
			panInput.classList.add('is-invalid');
			panInput.classList.remove('is-valid');
		} else {
			error.style.display = 'none';
			panInput.classList.remove('is-invalid');
			panInput.classList.add('is-valid');
		}
	}
	// Allow numbers only
	function isNumberOnly(event) {
		return event.charCode >= 48 && event.charCode <= 57;
	}

	// Validate on blur
	function validateMobile(input, errorId) {
		const error = document.getElementById(errorId);

		// If user never typed anything → don't show error
		if (input.value.trim() === '') {
			error.style.display = 'none';
			input.classList.remove('is-invalid', 'is-valid');
			return;
		}

		// Exactly 10 digits
		if (!/^[0-9]{10}$/.test(input.value)) {
			error.style.display = 'block';
			input.classList.add('is-invalid');
			input.classList.remove('is-valid');
		} else {
			error.style.display = 'none';
			input.classList.remove('is-invalid');
			input.classList.add('is-valid');
		}
	}

	function validateVehicleNo() {
		const input = document.getElementById('VehicleNo');
		const error = document.getElementById('vehicleError');

		input.value = input.value.toUpperCase().trim();

		const value = input.value;
		const regex = /^[A-Z]{2}[0-9]{1,2}[A-Z]{1,2}[0-9]{4}$/;

		if (value.length < 9) {
			error.innerText = 'Invalid Vehicle Number (Example: KA01AB1234)';
			error.style.display = 'block';
			input.classList.add('is-invalid');
			input.classList.remove('is-valid');
			return false;
		}


		if (!regex.test(value)) {
			error.innerText = 'Invalid Vehicle Number (Example: KA01AB1234)';
			error.style.display = 'block';
			input.classList.add('is-invalid');
			input.classList.remove('is-valid');
			return false;
		}

		error.style.display = 'none';
		input.classList.remove('is-invalid');
		input.classList.add('is-valid');
		return true;
	}

	$(document).ready(function() {
		function getSubGroupsByMain(Tdsselection) {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/gettdspercent",
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
							$('#TdsPercent').append('<option value="' + item.rate + '">' + item.rate + '</option>');
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
		checkTds();
		$('#Tds').on('change', function() {
			checkTds();
		});
	});

	function addVehicleDetail() {
		let newRow = `
    <tr class="addedtr_VehicleDetail">
		
       <td><input type="text" id="VehicleNo" name="VehicleNo[]" class="form-control text-uppercase" maxlength="13" onblur="validateVehicleNo()">
												<small id="vehicleError" class="text-danger" style="display:none;">
													Invalid Vehicle Number (Example: KA01AB1234)
												</small>
											</td>

		<td><input type="text" name="IFSC[]" class="form-control text-uppercase ifsc-input" value="" maxlength="11" minlength="11"></td>
											<td><input type="text" name="BankName[]" class="form-control branch-name" value="" readonly></td>
											<td><input type="text" name="BranchName[]" class="form-control branch-name" value="" readonly></td>
											<td><input type="text" name="AccountNo[]" class="form-control account-no" value=""></td>
											<td><input type="text" name="AccountName[]" class="form-control account-name" value="" readonly></td>
											<td><input type="text" id="AccountType" name="AccountType[]" class="form-control text-uppercase" value=""></td>
											<td>
        <select id="PreferTransporter" class="form-control selectpicker"
                data-live-search="true">
            <option value="">None selected</option>
        </select>
    </td>
											<td>
        <select id="DriverID" class="form-control selectpicker"
                data-live-search="true">
            <option value="">None selected</option>
            <?php foreach ($vehicle as $driver) { ?>
                <option value="<?= $driver['VehicleID'] ?>">
                    <?= $driver['VehicleID'] . ' ' . $driver['VehicleID'] ?>
                </option>
            <?php } ?>
        </select>
    </td>
											<td><input type="text" id="DriverMobile" name="DriverMobile[]" class="form-control text-uppercase" value="" maxlength="10"
													onkeypress="return isNumberOnly(event)"
													onblur="validateMobile(this, 'mobile3Error')">
												<small id="mobile3Error" class="text-danger" style="display:none;">
													Mobile number must be 10 digits
												</small>
											</td>
											<td><input type="text" id="LicenceNo" name="LicenceNo[]" class="form-control text-uppercase" value=""></td>	
        <td>
            <button type="button" class="btn btn-danger removebtn_vehicleDetail">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>`;

		$("#VehicleDetailtbody").append(newRow);

		$('.selectpicker').selectpicker('refresh');

		clearVehicleInputs();
	}

	function clearVehicleInputs() {
		$('#vehicleInputRow').find('input').val('');
		$('#vehicleInputRow').find('select').val('').selectpicker('refresh');
	}

	$(document).on('click', '.removebtn_vehicleDetail', function() {
		$(this).closest('tr').remove();
	});

	// $(document).on('blur', '.ifsc-input', function () {
	// 	let ifsc_code = $(this).val();
	// 	$.ajax({
	// 		url: "<?php echo admin_url(); ?>Vehicles/fetchBankDetailsFromIFSC",
	// 		method: "POST",
	// 		dataType: 'json',
	// 		data: {
	// 			ifsc_code: ifsc_code
	// 		},
	// 		beforeSend: function() {
	// 			$('.searchh6').css('display', 'block');

	// 			$('.searchh6').css('color', 'blue');
	// 		},
	// 		complete: function() {
	// 			$('.searchh6').css('display', 'none');
	// 		},
	// 		success: function(data) {
	// 			if (data == "Not Found") {
	// 				alert("Enter valid IFSC Code");
	// 				$('#BankName').val("");
	// 				$('#BranchName').val("");
	// 				$('#bank_address').val("");
	// 			} else {
	// 				$('#BankName').val(data.BANK);
	// 				$('#BranchName').val(data.BRANCH);
	// 				$('#bank_address').val(data.ADDRESS);
	// 			}
	// 		}
	// 	});
	// });

	$(document).on('blur', '.ifsc-input', function () {

    let $row = $(this).closest('tr');
    let ifsc_code = $(this).val().trim();

    if (ifsc_code === '') {
        $row.find('.bank-name').val('');
        $row.find('.branch-name').val('');
        return;
    }

    $.ajax({
        url: "<?php echo admin_url(); ?>Vehicles/fetchBankDetailsFromIFSC",
        method: "POST",
        dataType: 'json',
        data: {
            ifsc_code: ifsc_code
        },
        success: function (data) {

            if (!data || data === "Not Found") {
                alert("Enter valid IFSC Code");
                $row.find('.bank-name').val('');
                $row.find('.branch-name').val('');
            } else {
                $row.find('.bank-name').val(data.BANK);
                $row.find('.branch-name').val(data.BRANCH);
            }
        },
        error: function () {
            alert("IFSC verification failed");
        }
    });
});


	// $(document).on('blur', '.account-no', function () { 
	// 	let bank_ac_no = $(this).val();
	// 	var ifsc_code = $('.ifsc-input').val();
	// 	$.ajax({
	// 		url: "<?php echo admin_url(); ?>Vehicles/verifyBankAccount",
	// 		method: "POST",
	// 		dataType: 'json',
	// 		data: {
	// 			bank_ac_no: bank_ac_no,
	// 			ifsc_code: ifsc_code
	// 		},
	// 		beforeSend: function() {
	// 			$('.searchh6').css('display', 'block');

	// 			$('.searchh6').css('color', 'blue');
	// 		},
	// 		complete: function() {
	// 			$('.searchh6').css('display', 'none');
	// 		},
	// 		success: function(data) {
	// 			if (data.success == false) {
	// 				alert("Bank account not verified");
	// 				$row.find('.bank-name').val('');
    //             	$row.find('.branch-name').val('');
    //         	} else {
    //             	$row.find('.bank-name').val(data.BANK);
    //             	$row.find('.branch-name').val(data.BRANCH);
	// 			}
	// 		}
	// 	});
	// });

	$(document).on('blur', '.account-no', function () {

    let $row = $(this).closest('tr');
    let bank_ac_no = $(this).val().trim();
    let ifsc_code = $row.find('.ifsc-input').val().trim();

    if (bank_ac_no === '' || ifsc_code === '') {
        return;
    }

    $.ajax({
        url: "<?php echo admin_url(); ?>Vehicles/verifyBankAccount",
        method: "POST",
        dataType: 'json',
        data: {
            bank_ac_no: bank_ac_no,
            ifsc_code: ifsc_code
        },
        success: function (data) {

            if (!data || data.success === false) {
                alert("Bank account not verified");
                $row.find('.bank-name').val('');
                $row.find('.branch-name').val('');
            } else {
                // Optional overwrite if API returns data
                if (data.BANK) {
                    $row.find('.bank-name').val(data.BANK);
                }
                if (data.BRANCH) {
                    $row.find('.branch-name').val(data.BRANCH);
                }
            }
        },
        error: function () {
            alert("Account verification failed");
        }
    });
});

</script>
<style>
	#vehicle_modal .modal-dialog {
		width: 95%;
		height: 90vh;
		margin: 30px auto;
	}

	#vehicle_modal .modal-content {
		height: 100%;
		overflow: hidden;
		/* IMPORTANT */
	}

	/* Fixed header */
	#vehicle_modal .modal-header {
		height: auto;
	}

	/* Fixed footer */
	#vehicle_modal .modal-footer {
		height: auto;
	}

	/* Scroll only modal body */
	#vehicle_modal .modal-body {
		max-height: calc(90vh - 140px);
		/* header + footer height */
		overflow-y: auto;
	}
</style>