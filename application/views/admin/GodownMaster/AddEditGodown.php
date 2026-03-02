<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Godown Master</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<?php //echo form_open('admin/accounts_master/manage_account_group',array('id'=>'account_group_form')); 
						?>
						<div class="row">
							<div class="col-md-12">
								<div class="searchh" style="display:none;">Please wait Exporting data...</div>
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Godown...</div>
								<div class="searchh4" style="display:none;">Please wait update Godown...</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="GodownCode">Godown Code</label>
									<input type="text" name="GodownCode" id="GodownCode" class="form-control" value="">

								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="GodownName">Godown Name</label>
									<input type="text" name="GodownName" id="GodownName" class="form-control" value="">
									<input type="hidden" name="form_mode" id="form_mode" value="add">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Location" class="control-label">Location</label>
									<select class="selectpicker display-block" data-width="100%" id="state" name="state" data-none-selected-text="<?php echo 'Non Selected'; ?>" data-live-search="true">
										<option value=""></option>
										<!-- <?php 
										// foreach ($locations as $st) { ?>
											<option value="
											<?
											// php echo $st['id']; 
											?>
											">
											<?php 
											// echo $st['LocationName']; 
											?>
											</option>
										<?php 
										// } 
										?> -->
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="PINCode" class="control-label">PIN Code</label>
									<input type="text" id="PINCode" name="PINCode" class="form-control" maxlength="6" onkeypress="return isNumberOnly(event)">
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="state" class="control-label">State</label>
									<select class="selectpicker display-block" data-width="100%" id="state" name="state" data-none-selected-text="<?php echo 'Non Selected'; ?>" data-live-search="true">
										<option value=""></option>
										<?php foreach ($state as $st) { ?>
											<option value="<?php echo $st['short_name']; ?>"><?php echo $st['state_name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="city" class="control-label">City</label>
									<select class="selectpicker display-block" data-width="100%" id="city" name="city" data-none-selected-text="<?php echo 'Non Selected'; ?>" data-live-search="true">
										<option value=""></option>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="ItemDivisionID">
									<label for="IsActive" class="control-label">Is Active ?</label>
									<select id="IsActive" class="form-control selectpicker" data-live-search="true">
										<option value="N">No</option>
										<option value="Y">Yes</option>
									</select>
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<label for="Address">Address</label>
									<textarea
										id="Address"
										name="Address"
										class="form-control"></textarea>
								</div>
							</div>

						</div>


						<div class="row">
							<div class="col-md-12">
								<?php if (has_permission('account_groups', '', 'create')) {
								?>
									<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php
								} else {
								?>
									<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								} ?>

								<?php if (has_permission('account_groups', '', 'edit')) {
								?>
									<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
								<?php
								} else {
								?>
									<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
								<?php
								} ?>

								<button type="button" class="btn btn-default cancelBtn" style="margin-right: 25px;">Cancel</button>
								<?php
								$staffID = $this->session->userdata('staff_user_id');
								if ($staffID == '3') {
								?>
									<button type="button" class="btn btn-danger DeleteBtn" style="margin-right: 25px;">Delete</button>
								<?php
								}
								?>


								<button class="btn btn-default  " href="javascript:void(0);" onclick="printPage();" style="margin-right: 25px;">Print</button>
								<button class="btn btn-default  " href="#" id="caexcel">Export</button>

							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="table-GodownList tableFixHead">
									<table class="tree table table-striped table-bordered table-GodownList tableFixHead" id="table_GodownList1" width="100%">
										<thead>
											<tr style="display:none;">
												<td colspan="9">
													<h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5>
												</td>
											</tr>
											<tr>
												<th id="sl" style="text-align:left;">Godown Code </th>
												<th style="text-align:left;">Godown Name</th>
												<th style="text-align:left;">PIN Code</th>
												<th style="text-align:left;">State</th>
												<th style="text-align:left;">City</th>
												<th style="text-align:left;">Address</th>
												<th style="text-align:left;">IsActive</th>
											</tr>
										</thead>
										<tbody id="tbodyid">
											<?php
											foreach ($TableData as $key => $value) {
											?>
												<tr class="get_GodownCode" data-id="<?php echo $value["GodownCode"]; ?>">
													<td><?php echo $value["GodownCode"]; ?></td>
													<td><?php echo $value["GodownName"]; ?></td>
													<td><?php echo $value["Pincode"]; ?></td>
													<td><?php echo $value["StateCode"]; ?></td>
													<td><?php echo $value["CityID"]; ?></td>
													<td><?php echo $value["Address"]; ?></td>
													<td><?php echo $value["IsActive"]; ?></td>

												</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="clearfix"></div>

						<div class="modal fade GodownList" id="GodownList" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">GodownList <?php echo $staffID; ?></h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-GodownList tableFixHead2">
											<table class="tree table table-striped table-bordered table-GodownList tableFixHead2" id="table_GodownList" width="100%">
												<thead>
													<tr style="display:none;">
														<td colspan="9">
															<h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5>
														</td>
													</tr>
													<tr>
														<th id="sl" style="text-align:left;">Godown Code </th>
														<th style="text-align:left;">Godown Name</th>
														<th style="text-align:left;">PIN Code</th>
														<th style="text-align:left;">State</th>
														<th style="text-align:left;">City</th>
														<th style="text-align:left;">Address</th>
														<th style="text-align:left;">IsActive</th>
													</tr>
												</thead>
												<tbody>
													<?php
													foreach ($TableData as $key => $value) {
													?>
														<tr class="get_GodownCode" data-id="<?php echo $value["GodownCode"]; ?>">
															<td><?php echo $value["GodownCode"]; ?></td>
															<td><?php echo $value["GodownName"]; ?></td>
															<td><?php echo $value["Pincode"]; ?></td>
															<td><?php echo $value["StateCode"]; ?></td>
															<td><?php echo $value["CityID"]; ?></td>
															<td><?php echo $value["Address"]; ?></td>
															<td><?php echo $value["IsActive"]; ?></td>
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
<!--new update -->


<script type="text/javascript" language="javascript">
	$(document).ready(function() {

		$("#caexcel").click(function() {
			act = '1';
			$.ajax({
				url: "<?php echo admin_url(); ?>GodownMaster/exportGodown",
				method: "POST",
				data: {
					act: act
				},
				beforeSend: function() {
					$('#searchh').css('display', 'block');
				},
				complete: function() {
					$('#searchh').css('display', 'none');
				},
				success: function(data) {
					response = JSON.parse(data);
					window.location.href = response.site_url + response.filename;
				}
			});
		});

		$("#GodownCode").dblclick(function() {
			$('#GodownList').modal('show');
			$('#GodownList').on('shown.bs.modal', function() {
				$('#myInput1').focus();
			})
		});
		$('.updateBtn').hide();
		$('.updateBtn2').hide();
		$('.DeleteBtn').hide();

		// Focus on GroupID
		$('#GodownCode').on('focus', function() {
			$('#GodownCode').val('');
			$('#GodownName').val('');
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
			$('.DeleteBtn').hide();
		});

		// Cancel selected data
		$(".cancelBtn").click(function() {
			$('#GodownCode').val('');
			$('#GodownName').val('');
			$('#PINCode').val('');
			$('#Address').val('');

			// Reset dropdowns
			$('#state').val('').selectpicker('refresh');

			$('#city')
				.empty()
				.append('<option value=""></option>')
				.selectpicker('refresh');

			$('#IsActive').val('Y').selectpicker('refresh');
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
			$('.DeleteBtn').hide();
		});

		// Get Group Detail by Group ID
		$('#GodownCode').on('blur', function() {
			var GodownCode = $(this).val();
			if (GodownCode == "") {
				$('.saveBtn').show();
				$('.saveBtn2').show();
				$('.updateBtn').hide();
				$('.updateBtn2').hide();
				$('.DeleteBtn').hide();
				$('#GodownName').val('');
			} else {
				$.ajax({
					url: "<?php echo admin_url(); ?>GodownMaster/GetAccountDetails",
					dataType: "JSON",
					method: "POST",
					cache: false,
					data: {
						GodownCode: GodownCode,
					},
					beforeSend: function() {
						$('#searchh2').css('display', 'block');
					},
					complete: function() {
						$('#searchh2').css('display', 'none');
					},
					success: function(data) {
						if (empty(data)) {
							$('.saveBtn').show();
							$('.saveBtn2').show();
							$('.updateBtn').hide();
							$('.updateBtn2').hide();
							$('.DeleteBtn').hide();
							$('#GodownName').val('');
						} else {
							$('#GodownName').val(data.GodownName);
							$('.saveBtn').hide();
							$('.updateBtn').show();
							$('.DeleteBtn').show();
							$('.saveBtn2').hide();
							$('.updateBtn2').show();
						}
					}
				});
			}
		})

		// Initialize For GroupID
		$("#GodownCode").autocomplete({
			source: function(request, response) {
				// Fetch data
				$.ajax({
					url: "<?= base_url() ?>admin/GodownMaster/getAccountSerch",
					type: 'post',
					dataType: "json",
					data: {
						search: request.term
					},
					success: function(data) {
						response(data);
					}
				});
			},
			select: function(event, ui) {
				$('#GodownCode').val(ui.item.value); // display the selected text
				$('#GodownName').val(ui.item.label); // display the selected text
				$('.saveBtn').hide();
				$('.updateBtn').show();
				$('.DeleteBtn').show();
				$('.saveBtn2').hide();
				$('.updateBtn2').show();
				$('#GodownName').focus();
				return false;
			}
		});


		$('.get_GodownCode').on('click', function() {
			GodownCode = $(this).attr("data-id");
			$.ajax({
				url: "<?php echo admin_url(); ?>GodownMaster/GetAccountDetails",
				dataType: "JSON",
				method: "POST",
				cache: false,
				data: {
					GodownCode: GodownCode,
				},
				beforeSend: function() {
					$('#searchh2').css('display', 'block');
				},
				complete: function() {
					$('#searchh2').css('display', 'none');
				},
				success: function(data) {
					if (empty(data)) {
						$('.saveBtn').show();
						$('.saveBtn2').show();
						$('.updateBtn').hide();
						$('.DeleteBtn').hide();
						$('.updateBtn2').hide();
						$('#GodownCode').val('');
						$('#GodownName').val('');
					} else {
						$('#GodownCode').val(data.GodownCode);
						$('#GodownName').val(data.GodownName);
						$('#PINCode').val(data.Pincode);
						$('#state').val(data.StateCode).selectpicker('refresh');
						$('#IsActive').val(data.IsActive).selectpicker('refresh');
						$('#state').trigger('change', {
							cityID: data.CityID
						});
						$('#Address').val(data.Address);


						$('.saveBtn').hide();
						$('.updateBtn').show();
						$('.DeleteBtn').show();
						$('.saveBtn2').hide();
						$('.updateBtn2').show();
					}
				}
			});
			$('#GodownList').modal('hide');
		});

		// Save New Account
		$('.saveBtn').on('click', function() {
			GodownCode = $('#GodownCode').val();
			GodownName = $('#GodownName').val();
			Pincode = $('#PINCode').val();
			State = $('#state').val();
			City = $('#city').val();
			Address = $('#Address').val();
			IsActive = $('#IsActive').val();
			$.ajax({
				url: "<?php echo admin_url(); ?>GodownMaster/SaveAccount",
				dataType: "JSON",
				method: "POST",
				data: {
					GodownCode: GodownCode,
					GodownName: GodownName,
					Pincode: Pincode,
					State: State,
					City: City,
					Address: Address,
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
						$('.saveBtn').show();
						$('.saveBtn2').show();
						$('.updateBtn').hide();
						$('.updateBtn2').hide();
						$('.DeleteBtn').hide();
						$('#GodownCode').val('');
						$('#GodownName').val('');
						$('#PINCode').val('');
			$('#Address').val('');

			// Reset dropdowns
			$('#state').val('').selectpicker('refresh');

			$('#city')
				.empty()
				.append('<option value=""></option>')
				.selectpicker('refresh');

			$('#IsActive').val('Y').selectpicker('refresh');
						reloadtable();
					} else {
						alert_float('warning', 'Something went wrong...');
					}
				}
			});
		});
		
		// Update Exiting Item
		$('.updateBtn').on('click', function() {
			GodownCode = $('#GodownCode').val();
			GodownName = $('#GodownName').val();
			Pincode = $('#PINCode').val();
			State = $('#state').val();
			City = $('#city').val();
			Address = $('#Address').val();
			IsActive = $('#IsActive').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>GodownMaster/UpdateAccount",
				dataType: "JSON",
				method: "POST",
				data: {
					GodownCode: GodownCode,
					GodownName: GodownName,
					Pincode: Pincode,
					State: State,
					City: City,
					Address: Address,
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
						$('.saveBtn').show();
						$('.saveBtn2').show();
						$('.updateBtn').hide();
						$('.updateBtn2').hide();
						$('.DeleteBtn').hide();
						$('#GodownCode').val('');
						$('#GodownName').val('');
						$('#PINCode').val('');
			$('#Address').val('');

			// Reset dropdowns
			$('#state').val('').selectpicker('refresh');

			$('#city')
				.empty()
				.append('<option value=""></option>')
				.selectpicker('refresh');

			$('#IsActive').val('Y').selectpicker('refresh');
						reloadtable();
					} else {
						alert_float('warning', 'Something went wrong...');
					}
				}
			});
		});
		function reloadtable() {
			GodownCode = '11';
			$.ajax({
				url: "<?php echo admin_url(); ?>GodownMaster/GetList",
				dataType: "JSON",
				method: "POST",
				data: {
					GodownCode: GodownCode
				},
				/* beforeSend: function () {
					$('.searchh4').css('display','block');
					$('.searchh4').css('color','blue');
					},
					complete: function () {
					$('.searchh4').css('display','none');
				},*/
				success: function(data) {
					$('#tbodyid').html('')
					$('#tbodyid').html(data);
				}
			});
		}

		// Delete Account
		$('.DeleteBtn').on('click', function() {
			GodownCode = $('#GodownCode').val();
			$.ajax({
				url: "<?php echo admin_url(); ?>GodownMaster/DeleteAccount",
				dataType: "JSON",
				method: "POST",
				data: {
					GodownCode: GodownCode
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
						alert_float('success', 'Record Deleted successfully...');
						$('.saveBtn').show();
						$('.saveBtn2').show();
						$('.updateBtn').hide();
						$('.updateBtn2').hide();
						$('.DeleteBtn').hide();
						$('#GodownCode').val('');
						$('#GodownName').val('');
						reloadtable();
					} else {
						alert_float('warning', data);
					}
				}
			});
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_GodownList");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			td3 = tr[i].getElementsByTagName("td")[3];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else if (td1) {
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					} else if (td2) {
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
						} else if (td3) {
							txtValue = td3.textContent || td3.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
							} else {
								tr[i].style.display = "none";
							}
						}
					}
				}
			}
		}
	}
</script>
<script>
	// Allow numbers only
	function isNumberOnly(event) {
		return event.charCode >= 48 && event.charCode <= 57;
	}

	/* ================================
	   PIN CODE → STATE → CITY AUTO FILL
	   ================================ */

	// $('#PINCode').on('blur', function () {

	//     let pincode = $(this).val().trim();
	//     if (pincode.length !== 6) return;

	//     fetch('https://api.zippopotam.us/IN/' + pincode)
	//         .then(response => {
	//             if (!response.ok) throw new Error('Invalid PIN');
	//             return response.json();
	//         })
	//         .then(data => {

	//             /* ------------------------
	//                Extract API values
	//             ------------------------ */
	//             let apiState = data.places[0].state.trim();          // Maharashtra
	//             let apiPlace = data.places[0]['place name'].trim(); // Umri

	//             /* ------------------------
	//                Normalize state names
	//             ------------------------ */
	//             if (apiState.toLowerCase() === 'new delhi') {
	//                 apiState = 'Delhi';
	//             }

	//             /* ------------------------
	//                Set STATE dropdown
	//             ------------------------ */
	//             let stateSet = false;

	//             $('#state option').each(function () {
	//                 if ($(this).text().trim().toLowerCase() === apiState.toLowerCase()) {
	//                     $('#state').val($(this).val()).selectpicker('refresh');
	//                     stateSet = true;
	//                 }
	//             });

	//             if (!stateSet) {
	//                 alert_float('warning', 'State not found for PIN');
	//                 return;
	//             }

	//             /* ------------------------
	//                Trigger city load
	//             ------------------------ */
	//             $('#state').trigger('change');

	//             /* ------------------------
	//                Wait for city dropdown
	//             ------------------------ */
	//             let waitCity = setInterval(function () {

	//                 if ($('#city option').length > 1) {

	//                     clearInterval(waitCity);

	//                     let cityMatched = false;
	//                     let apiPlaceLower = apiPlace.toLowerCase();

	//                     $('#city option').each(function () {

	//                         let cityText = $(this).text().trim().toLowerCase();

	//                         // Exact match
	//                         if (cityText === apiPlaceLower) {
	//                             $('#city').val($(this).val()).selectpicker('refresh');
	//                             cityMatched = true;
	//                         }

	//                         // Partial match
	//                         if (
	//                             cityText.includes(apiPlaceLower) ||
	//                             apiPlaceLower.includes(cityText)
	//                         ) {
	//                             $('#city').val($(this).val()).selectpicker('refresh');
	//                             cityMatched = true;
	//                         }
	//                     });

	//                     /* ------------------------
	//                        Fallback: first city
	//                     ------------------------ */
	//                     if (!cityMatched) {
	//                         $('#city').prop('selectedIndex', 0).selectpicker('refresh');
	//                     }
	//                 }

	//             }, 300); // check every 300ms
	//         })
	//         .catch(() => {
	//             alert_float('warning', 'PIN code not found');
	//         });
	// });


	// $('#PINCode').on('blur', function () {

	//     let pincode = $(this).val().trim();
	//     if (pincode.length !== 6) return;

	//     fetch('https://api.zippopotam.us/IN/' + pincode)
	//         .then(res => {
	//             if (!res.ok) throw new Error();
	//             return res.json();
	//         })
	//         .then(data => {

	//             let apiState   = data.places[0].state.trim();
	//             let apiVillage = data.places[0]['place name'].trim();
	//             let apiDistrict = (data.places[0].county || '').trim();

	//             /* Normalize Delhi */
	//             if (apiState.toLowerCase() === 'new delhi') {
	//                 apiState = 'Delhi';
	//             }

	//             /* ---------------- STATE ---------------- */
	//             let stateMatched = false;
	//             $('#state option').each(function () {
	//                 if ($(this).text().trim().toLowerCase() === apiState.toLowerCase()) {
	//                     $('#state').val($(this).val()).selectpicker('refresh');
	//                     stateMatched = true;
	//                 }
	//             });

	//             if (!stateMatched) {
	//                 alert_float('warning', 'State not found');
	//                 return;
	//             }

	//             /* Load cities */
	//             $('#state').trigger('change');

	//             /* ---------------- CITY ---------------- */
	//             let waitCity = setInterval(function () {

	//                 if ($('#city option').length > 1) {

	//                     clearInterval(waitCity);

	//                     let matched = false;
	//                     let village = apiVillage.toLowerCase();
	//                     let district = apiDistrict.toLowerCase();

	//                     $('#city option').each(function () {
	//                         let cityText = $(this).text().trim().toLowerCase();

	//                         // 1️⃣ Exact village
	//                         if (cityText === village) {
	//                             $('#city').val($(this).val()).selectpicker('refresh');
	//                             matched = true;
	//                         }

	//                         // 2️⃣ District match (Nanded)
	//                         if (!matched && cityText === district) {
	//                             $('#city').val($(this).val()).selectpicker('refresh');
	//                             matched = true;
	//                         }

	//                         // 3️⃣ Partial match
	//                         if (!matched && (
	//                             cityText.includes(village) ||
	//                             cityText.includes(district)
	//                         )) {
	//                             $('#city').val($(this).val()).selectpicker('refresh');
	//                             matched = true;
	//                         }
	//                     });

	//                     // 4️⃣ Final fallback
	//                     if (!matched) {
	//                         $('#city').prop('selectedIndex', 0).selectpicker('refresh');
	//                     }
	//                 }

	//             }, 300);
	//         })
	//         .catch(() => {
	//             alert_float('warning', 'PIN code not found');
	//         });
	// });



	// --------------------
	// PIN CODE → STATE → CITY AUTO FILL (using Indian Postal API)
	// --------------------
	// $('#PINCode').on('blur', function () {
	//     let pincode = $(this).val().trim();
	//     if (pincode.length !== 6) return;

	//     fetch('https://api.postalpincode.in/pincode/' + pincode)
	//         .then(res => res.json())
	//         .then(data => {
	//             if (!data || data.length === 0 || data[0].Status !== "Success") {
	//                 alert_float('warning', 'PIN code not found');
	//                 return;
	//             }

	//             let postOffice = data[0].PostOffice[0];
	//             let apiState    = postOffice.State.trim();
	//             let apiDistrict = postOffice.District.trim();
	//             let apiCity     = postOffice.Name.trim(); // post office name is usable as city

	//             // Normalize Delhi if returned differently
	//             if (apiState.toLowerCase() === 'new delhi') apiState = 'Delhi';

	//             // ------------------------
	//             // Select the STATE in dropdown
	//             // ------------------------
	//             let stateMatched = false;
	//             $('#state option').each(function () {
	//                 if ($(this).text().trim().toLowerCase() === apiState.toLowerCase()) {
	//                     $('#state').val($(this).val()).selectpicker('refresh');
	//                     stateMatched = true;
	//                 }
	//             });

	//             if (!stateMatched) {
	//                 alert_float('warning', 'State not found');
	//                 return;
	//             }

	//             // Store city/district for later
	//             window._pinCity = { city: apiCity, district: apiDistrict };

	//             // Trigger state change to load cities
	//             $('#state').trigger('change');

	//         })
	//         .catch(() => {
	//             alert_float('warning', 'PIN code not found');
	//         });
	// });

	// Triggered when state dropdown changes
	// 

	$('#PINCode').on('blur', function() {
		let pincode = $(this).val().trim();
		if (pincode.length !== 6) return;

		fetch('https://api.postalpincode.in/pincode/' + pincode)
			.then(res => res.json())
			.then(data => {
				if (!data || data[0].Status !== "Success") {
					alert_float('warning', 'PIN code not found');
					return;
				}

				let postOffice = data[0].PostOffice[0];

				let apiState = postOffice.State.trim();
				let apiCity = postOffice.District.trim(); // ⬅️ USE DISTRICT (best match)

				// Normalize Delhi
				if (apiState.toLowerCase() === 'new delhi') apiState = 'Delhi';

				// Select state
				let stateMatched = false;
				$('#state option').each(function() {
					if ($(this).text().trim().toLowerCase() === apiState.toLowerCase()) {
						$('#state').val($(this).val()).selectpicker('refresh');
						stateMatched = true;
					}
				});

				if (!stateMatched) {
					alert_float('warning', 'State not found');
					return;
				}

				// 🔥 Pass city NAME to state change
				$('#state').trigger('change', [{
					cityName: apiCity
				}]);
			});
	});


	$('#state').on('change', function(e, cityData = null) {

		let stateID = $(this).val();
		let url = "<?php echo admin_url(); ?>GodownMaster/GetCityListByStateID";

		$.ajax({
			type: 'POST',
			url: url,
			data: {
				id: stateID
			},
			dataType: 'json',
			success: function(data) {

				$("#city").empty().append('<option value=""></option>');

				$.each(data, function(i, v) {
					$('#city').append(
						'<option value="' + v.id + '">' + v.city_name + '</option>'
					);
				});

				// =========================
				// EDIT MODE (City ID)
				// =========================
				if (cityData && cityData.cityID) {
					$('#city').val(cityData.cityID);
				}

				// =========================
				// PIN MODE (City NAME)
				// =========================
				if (cityData && typeof cityData.cityName === 'string' && cityData.cityName !== '') {

    let target = cityData.cityName.toLowerCase();

    $('#city option').each(function () {
        let txt = $(this).text();
        if (txt && txt.toLowerCase().includes(target)) {
            $('#city').val($(this).val());
            return false;
        }
    });
}

				$('#city').selectpicker('refresh');
			}
		});
	});
</script>

<style>
	#table_GodownList td:hover {
		cursor: pointer;
	}

	#GodownCode {
		text-transform: uppercase;
	}

	#table_GodownList tr:hover {
		background-color: #ccc;
	}

	.table-GodownList {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-GodownList thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-GodownList tbody th {
		position: sticky;
		left: 0;
	}

	.table-GodownList1 {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-GodownList1 thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-GodownList1 tbody th {
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
</style>

<style type="text/css">
	body {
		overflow: hidden;
	}
</style>