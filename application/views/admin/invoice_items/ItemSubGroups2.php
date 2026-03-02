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
								<li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Item Sub Group 2</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Item Sub Group 1...</div>
								<div class="searchh4" style="display:none;">Please wait update Item Sub Group 1...</div>
							</div>
							<br>
							<div class="col-md-3">
								<?php
								$nextSubGroup2ID = $lastId + 1;
								?>
								<?php //echo render_input('ItemDivisionID','ItemDivisionID',$nextSubGroup2ID,'text'); 
								?>
								<div class="form-group" app-field-wrapper="SubGroupCode">
									<small class="req text-danger">* </small>
									<label for="SubGroupCode" class="control-label">Sub Group 2 Code</label>
									<input type="text" id="SubGroupCode" name="SubGroupCode" class="form-control" value="<?= $nextSubGroup2ID; ?>" readonly>
								</div>
								<span id="lblError" style="color: red"></span>
								<input type="hidden" id="nextSubGroup2ID" name="nextSubGroup2ID" class="form-control" value="<?php echo $nextSubGroup2ID; ?>">
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="SubGroupName">Sub Group 2 Name</label>
									<input type="text" name="SubGroupName" id="SubGroupName" class="form-control" value="" required>
								</div>
							</div>
							 <div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="MainGroup" class="control-label">Main Group Name</label>
									<select id="MainGroup" class="form-control selectpicker" data-live-search="true">
										<option value=""></option>
										<?php foreach ($MainGroup as $mg) {
                                        ?>
                                            <option value="<?php echo $mg['id']; ?>">
                                                <?php echo $mg['name'];?>
                                            </option>
                                        <?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="SubGroup1" class="control-label">Sub Group 1</label>
									<select id="SubGroup1" class="form-control selectpicker" data-live-search="true">
										<option value=""></option>
									</select>
								</div>
							</div>                            
							<div class="col-md-3">
								<div class="form-group">
									<label for="IsActive" class="control-label">Is Active ?</label>
									<select id="IsActive" class="form-control selectpicker" data-live-search="true">
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>

							<div class="clearfix"></div>
							<br><br>
							<div class="col-md-12">
								<div class="action-buttons text-left">
									<?php if (has_permission('ItemSubGroup2', '', 'create')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
									<?php
									} ?>

									<?php if (has_permission('ItemSubGroup2', '', 'edit')) {
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

						<div class="modal fade ItemSubGroup2_List" id="ItemSubGroup2_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Item Sub Group 2 List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-ItemSubGroup2_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-ItemSubGroup2_List tableFixHead2" id="table_ItemSubGroup2_List" width="100%">
												<thead>
													<tr>
														<th class="sortablePop" style="text-align:left;">Sub Group Code</th>
														<th class="sortablePop" style="text-align:left;">Sub Group Name</th>
														<th class="sortablePop" style="text-align:left;">Main Group Name</th>
														<th class="sortablePop" style="text-align:left;">Sub Group 1 Name</th>
														<th class="sortablePop" style="text-align:left;">IsActive</th>
													</tr>
												</thead>
												<tbody id="SubGroup2TableBody">
													<?php
													foreach ($table_data as $key => $value) {
													?>
														<tr class="get_ItemSubGroup2" data-id="<?php echo $value["id"]; ?>">
															<td><?php echo $value['id']; ?></td>
															<td><?php echo $value['name']; ?></td>
															<td><?php echo $value['MainGroupName']; ?></td>
															<td><?php echo $value['SubGroup1Name']; ?></td>
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
		$("#SubGroupCode").dblclick(function() {
			$('#ItemSubGroup2_List').modal('show');
			$('#ItemSubGroup2_List').on('shown.bs.modal', function() {
				$('#myInput1').focus();
			})
		});

		// Empty and open create mode
		$("#SubGroupCode").focus(function() {
			var nextSubGroup2ID = $('#nextSubGroup2ID').val();
			$('#SubGroupCode').val(nextSubGroup2ID);
			$('#SubGroupName').val('');
			$('#MainGroup').val('').selectpicker('refresh');
			$('#SubGroup1').val('').selectpicker('refresh');
            $('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		// Cancel selected data
		$(".cancelBtn").click(function() {
			var nextSubGroup2ID = $('#nextSubGroup2ID').val();
			$('#SubGroupCode').val(nextSubGroup2ID);
			$('#SubGroupName').val('');
			$('#MainGroup').val('').selectpicker('refresh');
			$('#SubGroup1').val('').selectpicker('refresh');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		$('#MainGroup').on('change', function() {
			var MainGroup = $(this).val();
			//alert(roleid);
			var url = "<?php echo base_url(); ?>admin/invoice_items/GetSubgroup1Data";
			jQuery.ajax({
				type: 'POST',
				url:url,
				data: {MainGroup: MainGroup},
				dataType:'json',
				success: function(data) {
					$("#SubGroup1").find('option').remove();
					$("#SubGroup1").selectpicker("refresh");
					$("#SubGroup1").append(new Option('', ''));
					for (var i = 0; i < data.length; i++) {
						$("#SubGroup1").append(new Option(data[i].name, data[i].id));
					}
					$('.selectpicker').selectpicker('refresh');
				}
			});
		});

		$(document).on('click', '.get_ItemSubGroup2', function() {
			SubGroupCode = $(this).attr("data-id");
			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/GetItemSubGroup2DetailByID",
				dataType: "JSON",
				method: "POST",
				data: {
					SubGroupCode: SubGroupCode
				},
				beforeSend: function() {
					$('.searchh2').css('display', 'block');
					$('.searchh2').css('color', 'blue');
				},
				complete: function() {
					$('.searchh2').css('display', 'none');
				},
				success: function(data) {
					$('#SubGroupCode').val(data.SubGroupCode);
					$('#SubGroupName').val(data.SubGroupName);
					$('#MainGroup').val(data.MainGroup).selectpicker('refresh');
					$('#SubGroup1').val(data.SubGroup1).selectpicker('refresh');
					$('#IsActive').val(data.IsActive).selectpicker('refresh');

					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
				}
			});
			$('#ItemSubGroup2_List').modal('hide');
		});

		// Save New Form
		$('.saveBtn').on('click', function() {
			SubGroupCode = $('#SubGroupCode').val();
			SubGroupName = $('#SubGroupName').val();
			MainGroup = $('#MainGroup').val();
			SubGroup1 = $('#SubGroup1').val();
			IsActive = $('#IsActive').val();


			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/SaveItemSubGroup2",
				dataType: "JSON",
				method: "POST",
				data: {
					SubGroupCode: SubGroupCode,
					SubGroupName: SubGroupName,
					MainGroup: MainGroup,
					SubGroup1: SubGroup1,
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
						$('#SubGroupCode').val(newGroupID);
						$('#nextSubGroup2ID').val(newGroupID);
						$('#SubGroupName').val('');
						$('#MainGroup').val('').selectpicker('refresh');
						$('#SubGroup1').val('').selectpicker('refresh');
						$('#IsActive').val('Y').selectpicker('refresh');

						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshSubGroupList();
					} else {
						alert_float('warning', data.message);
						$('#SubGroupName').focus();
						return;
					}
				}
			});
		});
		// Update Exiting Form
		$('.updateBtn').on('click', function() {
			SubGroupCode = $('#SubGroupCode').val();
			SubGroupName = $('#SubGroupName').val();
			MainGroup = $('#MainGroup').val();
			SubGroup1 = $('#SubGroup1').val();
			IsActive = $('#IsActive').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/UpdateItemSubGroup2",
				dataType: "JSON",
				method: "POST",
				data: {
					SubGroupCode: SubGroupCode,
					SubGroupName: SubGroupName,
					MainGroup: MainGroup,
					SubGroup1: SubGroup1,
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
						$('#SubGroupCode').val(nextSubGroup2ID);
						$('#SubGroupName').val('');
						$('#MainGroup').val('').selectpicker('refresh');
						$('#SubGroup1').val('');
						$('#IsActive').val('Y').selectpicker('refresh');

						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshSubGroupList();
					} else {
						alert_float('warning', data.message || 'Something went wrong...');
					}
				}
			});
		});
	});
	$('.showListBtn').on('click', function() {
		$('#ItemSubGroup2_List').modal('show');

		$('#ItemSubGroup2_List').on('shown.bs.modal', function() {
			$('#myInput1').focus();
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_ItemSubGroup2_List");
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
		var table = $("#table_ItemSubGroup2_List tbody");
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

	function refreshSubGroupList() {
		$('#SubGroup2TableBody').load(location.href + ' #SubGroup2TableBody > *');
	}
</script>

<style>
	#item_code1 {
		text-transform: uppercase;
	}

	#table_ItemSubGroup2_List td:hover {
		cursor: pointer;
	}

	#table_ItemSubGroup2_List tr:hover {
		background-color: #ccc;
	}

	.table-ItemSubGroup2_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-ItemSubGroup2_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-ItemSubGroup2_List tbody th {
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
	#SubGroupName {
		text-transform: uppercase;
	}
</style>