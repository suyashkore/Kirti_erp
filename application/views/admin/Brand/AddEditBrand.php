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
								<li class="breadcrumb-item active" aria-current="page"><b>Brand Master</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Brand...</div>
								<div class="searchh4" style="display:none;">Please wait update Brand...</div>
							</div>
							<br>
							<div class="col-md-3">
								<?php
								$nextBrandID = $lastId + 1;
								?>
								<?php //echo render_input('ItemDivisionID','ItemDivisionID',$nextBrandID,'text'); 
								?>
								<div class="form-group" app-field-wrapper="BrandID">
									<small class="req text-danger">* </small>
									<label for="BrandID" class="control-label">Brand ID</label>
									<input type="text" id="BrandID" name="BrandID" class="form-control" value="<?= $nextBrandID; ?>" readonly>
								</div>
								<span id="lblError" style="color: red"></span>
								<input type="hidden" id="nextBrandID" name="nextBrandID" class="form-control" value="<?php echo $nextBrandID; ?>">
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="BrandCode">Brand Code</label>
									<input type="text" name="BrandCode" id="BrandCode" class="form-control" value="" oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '')">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="BrandName">Brand Name</label>
									<input type="text" name="BrandName" id="BrandName" class="form-control" value="">
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
							<br><br>
							<div class="col-md-12">
								<div class="action-buttons text-left">
									<?php if (has_permission('Brand', '', 'create')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
									<?php
									} ?>

									<?php if (has_permission('Brand', '', 'edit')) {
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

						<div class="modal fade Brand_List" id="Brand_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Brand List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-Brand_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-Brand_List tableFixHead2" id="table_Brand_List" width="100%">
												<thead>
													<tr>
														<th class="sortablePop" style="text-align:left;">Brand ID </th>
														<th class="sortablePop" style="text-align:left;">Brand Code </th>
														<th class="sortablePop" style="text-align:left;">Brand Name</th>
														<th class="sortablePop" style="text-align:left;">IsActive</th>

													</tr>
												</thead>
												<tbody id="BrandTableBody">
													<?php
													foreach ($table_data as $key => $value) {
													?>
														<tr class="get_Brand" data-id="<?php echo $value["id"]; ?>">
															<td><?php echo $value['id']; ?></td>
															<td><?php echo $value['BrandID']; ?></td>
															<td><?php echo $value['BrandName']; ?></td>
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
		$("#BrandID").dblclick(function() {
			$('#Brand_List').modal('show');
			$('#Brand_List').on('shown.bs.modal', function() {
				$('#myInput1').focus();
			})
		});

		// Empty and open create mode
		$("#BrandID").focus(function() {
			var nextBrandID = $('#nextBrandID').val();
			$('#BrandID').val(nextBrandID);
			$('#BrandCode').val('');
			$('#BrandName').val('');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		// Cancel selected data
		$(".cancelBtn").click(function() {
			var nextBrandID = $('#nextBrandID').val();
			$('#BrandID').val(nextBrandID);
			$('#BrandCode').val('');
			$('#BrandName').val('');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		$('#BrandCode').blur(function () {
    var BrandCode = $(this).val().trim();

    if (BrandCode === '') return;

    $.ajax({
        url: "<?php echo admin_url(); ?>Brand/GetBrandDetailByCode",
        dataType: "JSON",
        method: "POST",
        data: { BrandCode: BrandCode },
        beforeSend: function () {
            $('.searchh2').show().css('color', 'blue');
        },
        complete: function () {
            $('.searchh2').hide();
        },
        success: function (data) {
            if (data === null) {
				var nextBrandID = $('#nextBrandID').val();
				$('#BrandID').val(nextBrandID);
				$('#BrandName').val('');
				$('#IsActive').val('Y').selectpicker('refresh');
                // NEW ENTRY
                $('.saveBtn').show();
                $('.updateBtn').hide();
            } else {
                $('#BrandID').val(data.BrandID);
                $('#BrandCode').val(data.BrandCode);
                $('#BrandName').val(data.BrandName);
                $('#IsActive').val(data.IsActive).selectpicker('refresh');

                $('.saveBtn').hide();
                $('.updateBtn').show();
            }
        }
    });
});


		$(document).on('click', '.get_Brand', function() {
			BrandID = $(this).attr("data-id");
			$.ajax({
				url: "<?php echo admin_url(); ?>Brand/GetBrandDetailByID",
				dataType: "JSON",
				method: "POST",
				data: {
					BrandID: BrandID
				},
				beforeSend: function() {
					$('.searchh2').css('display', 'block');
					$('.searchh2').css('color', 'blue');
				},
				complete: function() {
					$('.searchh2').css('display', 'none');
				},
				success: function(data) {
					$('#BrandID').val(data.BrandID);
					$('#BrandCode').val(data.BrandCode);
					$('#BrandName').val(data.BrandName);
					$('#IsActive').val(data.IsActive).selectpicker('refresh');


					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
				}
			});
			$('#Brand_List').modal('hide');
		});

		// Save New Brand
		$('.saveBtn').on('click', function() {
			BrandID = $('#BrandID').val();
			BrandCode = $('#BrandCode').val();
			BrandName = $('#BrandName').val();
			IsActive = $('#IsActive').val();


			$.ajax({
				url: "<?php echo admin_url(); ?>Brand/SaveBrand",
				dataType: "JSON",
				method: "POST",
				data: {
					BrandID: BrandID,
					BrandCode: BrandCode,
					BrandName: BrandName,
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
						var nextBrandID = $('#nextBrandID').val();
						var newGroupID = parseInt(nextBrandID) + 1;
						$('#BrandID').val(newGroupID);
						$('#nextBrandID').val(newGroupID);
						$('#BrandCode').val('');
						$('#BrandName').val('');
						$('#IsActive').val('Y').selectpicker('refresh');

						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshBrandList();
					} else {
						alert_float('warning', data.message);
						$('#BrandCode').focus();
						return;
					}
				}
			});
		});
		// Update Exiting Brand
		$('.updateBtn').on('click', function() {
			BrandID = $('#BrandID').val();
			BrandCode = $('#BrandCode').val();
			BrandName = $('#BrandName').val();
			IsActive = $('#IsActive').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>Brand/UpdateBrand",
				dataType: "JSON",
				method: "POST",
				data: {
					BrandID: BrandID,
					BrandCode: BrandCode,
					BrandName: BrandName,
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
						var nextBrandID = $('#nextBrandID').val();
						$('#BrandID').val(nextBrandID);
						$('#BrandCode').val('');
						$('#BrandName').val('');
						$('#IsActive').val('Y').selectpicker('refresh');

						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshBrandList();
					} else {
						alert_float('warning', data.message || 'Something went wrong...');
					}
				}
			});
		});
	});
	$('.showListBtn').on('click', function() {
		$('#Brand_List').modal('show');

		$('#Brand_List').on('shown.bs.modal', function() {
			$('#myInput1').focus();
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_Brand_List");
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
		var table = $("#table_Brand_List tbody");
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

	function refreshBrandList() {
		$('#BrandTableBody').load(location.href + ' #BrandTableBody > *');
	}
</script>

<style>
	#item_code1 {
		text-transform: uppercase;
	}

	#table_Brand_List td:hover {
		cursor: pointer;
	}

	#table_Brand_List tr:hover {
		background-color: #ccc;
	}

	.table-Brand_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-Brand_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-Brand_List tbody th {
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
	#BrandCode {
		text-transform: uppercase;
	}
	#BrandName {
		text-transform: uppercase;
	}
</style>