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
								<li class="breadcrumb-item active" aria-current="page"><b>Item Sub Group 1</b></li>
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
								$nextSubGroup1ID = $lastId + 1;
								?>
								<?php //echo render_input('ItemDivisionID','ItemDivisionID',$nextSubGroup1ID,'text'); 
								?>
								<div class="form-group" app-field-wrapper="SubGroupCode">
									<small class="req text-danger">* </small>
									<label for="SubGroupCode" class="control-label">Sub Group Code</label>
									<input type="text" id="SubGroupCode" name="SubGroupCode" class="form-control" value="<?= $nextSubGroup1ID; ?>" readonly>
								</div>
								<span id="lblError" style="color: red"></span>
								<input type="hidden" id="nextSubGroup1ID" name="nextSubGroup1ID" class="form-control" value="<?php echo $nextSubGroup1ID; ?>">
							</div>
							<div class="col-md-5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="SubGroupName">Sub Group Name</label>
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
									<?php if (has_permission('ItemSubGroup1', '', 'create')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
									<?php
									} ?>

									<?php if (has_permission('ItemSubGroup1', '', 'edit')) {
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

						<div class="modal fade ItemSubGroup1_List" id="ItemSubGroup1_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Item Sub Group 1 List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-ItemSubGroup1_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-ItemSubGroup1_List tableFixHead2" id="table_ItemSubGroup1_List" width="100%">
												<thead>
													<tr>
														<th class="sortablePop" style="text-align:left;">Sub Group Code</th>
														<th class="sortablePop" style="text-align:left;">Sub Group Name</th>
														<th class="sortablePop" style="text-align:left;">Main Group Name</th>
														<th class="sortablePop" style="text-align:left;">IsActive</th>

													</tr>
												</thead>
												<tbody id="FormTableBody">
													<?php
													foreach ($table_data as $key => $value) {
													?>
														<tr class="get_ItemSubGroup1" data-id="<?php echo $value["id"]; ?>">
															<td><?php echo $value['id']; ?></td>
															<td><?php echo $value['name']; ?></td>
															<td><?php echo $value['MainGroupName']; ?></td>
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
			$('#ItemSubGroup1_List').modal('show');
			$('#ItemSubGroup1_List').on('shown.bs.modal', function() {
				$('#myInput1').focus();
			})
		});

		// Empty and open create mode
		$("#SubGroupCode").focus(function() {
			var nextSubGroup1ID = $('#nextSubGroup1ID').val();
			$('#SubGroupCode').val(nextSubGroup1ID);
			$('#SubGroupName').val('');
			$('#MainGroup').val('').selectpicker('refresh');
            $('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		// Cancel selected data
		$(".cancelBtn").click(function() {
			var nextSubGroup1ID = $('#nextSubGroup1ID').val();
			$('#SubGroupCode').val(nextSubGroup1ID);
			$('#SubGroupName').val('');
			$('#MainGroup').val('').selectpicker('refresh');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		$(document).on('click', '.get_ItemSubGroup1', function() {
			SubGroupCode = $(this).attr("data-id");
			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/GetSubGroupDetailByID",
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
					$('#IsActive').val(data.IsActive).selectpicker('refresh');

					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
				}
			});
			$('#ItemSubGroup1_List').modal('hide');
		});

		// Save New Form
		$('.saveBtn').on('click', function() {
			SubGroupCode = $('#SubGroupCode').val();
			SubGroupName = $('#SubGroupName').val();
			MainGroup = $('#MainGroup').val();
			IsActive = $('#IsActive').val();


			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/SaveSubGroup",
				dataType: "JSON",
				method: "POST",
				data: {
					SubGroupCode: SubGroupCode,
					SubGroupName: SubGroupName,
					MainGroup: MainGroup,
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
						var nextSubGroup1ID = $('#nextSubGroup1ID').val();
						var newGroupID = parseInt(nextSubGroup1ID) + 1;
						$('#SubGroupCode').val(newGroupID);
						$('#nextSubGroup1ID').val(newGroupID);
						$('#SubGroupName').val('');
						$('#MainGroup').val('').selectpicker('refresh');
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
			IsActive = $('#IsActive').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/UpdateSubGroup",
				dataType: "JSON",
				method: "POST",
				data: {
					SubGroupCode: SubGroupCode,
					SubGroupName: SubGroupName,
					MainGroup: MainGroup,
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
						var nextSubGroup1ID = $('#nextSubGroup1ID').val();
						$('#SubGroupCode').val(nextSubGroup1ID);
						$('#SubGroupName').val('');
						$('#MainGroup').val('').selectpicker('refresh');
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
		$('#ItemSubGroup1_List').modal('show');

		$('#ItemSubGroup1_List').on('shown.bs.modal', function() {
			$('#myInput1').focus();
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_ItemSubGroup1_List");
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
		var table = $("#table_ItemSubGroup1_List tbody");
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
		$('#FormTableBody').load(location.href + ' #FormTableBody > *');
	}
</script>

<style>
	#item_code1 {
		text-transform: uppercase;
	}

	#table_ItemSubGroup1_List td:hover {
		cursor: pointer;
	}

	#table_ItemSubGroup1_List tr:hover {
		background-color: #ccc;
	}

	.table-ItemSubGroup1_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-ItemSubGroup1_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-ItemSubGroup1_List tbody th {
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