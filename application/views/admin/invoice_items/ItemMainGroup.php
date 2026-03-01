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
								<li class="breadcrumb-item active" aria-current="page"><b>Item Main Group Master</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Item Main Group...</div>
								<div class="searchh4" style="display:none;">Please wait update Item Main Group...</div>
							</div>
							<br>
							<div class="col-md-3">
								<?php
								$nextMainGroupID = $lastId + 1;
								?>
								<?php //echo render_input('ItemDivisionID','ItemDivisionID',$nextMainGroupID,'text'); 
								?>
								<div class="form-group" app-field-wrapper="MainGroupCode">
									<small class="req text-danger">* </small>
									<label for="MainGroupCode" class="control-label">Main Group Code</label>
									<input type="text" id="MainGroupCode" name="MainGroupCode" class="form-control" value="<?= $nextMainGroupID; ?>" readonly>
								</div>
								<span id="lblError" style="color: red"></span>
								<input type="hidden" id="nextMainGroupID" name="nextMainGroupID" class="form-control" value="<?php echo $nextMainGroupID; ?>">
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
									<label for="ItemType" class="control-label">Item Type</label>
									<select id="ItemType" class="form-control selectpicker" data-live-search="true">
										<option value=""></option>
										<?php foreach ($itemtype as $it) {
                                        ?>
                                            <option value="<?php echo $it['id']; ?>">
                                                <?php echo $it['ItemTypeName'];?>
                                            </option>
                                        <?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
                                <div class="form-group" app-field-wrapper="Prefix">
                                    <small class="req text-danger">* </small>
                                    <label for="Prefix" class="control-label">Prefix</label>
                                    <input type="text" id="Prefix" name="Prefix" class="form-control" value="" oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '');">
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
									<?php if (has_permission('ItemMainGroup', '', 'create')) {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
									<?php
									} else {
									?>
										<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
									<?php
									} ?>

									<?php if (has_permission('ItemMainGroup', '', 'edit')) {
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

						<div class="modal fade ItemMainGroup_List" id="ItemMainGroup_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Item Main Group List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">

										<div class="table-ItemMainGroup_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-ItemMainGroup_List tableFixHead2" id="table_ItemMainGroup_List" width="100%">
												<thead>
													<tr>
														<th class="sortablePop" style="text-align:left;">Main Group Code</th>
														<th class="sortablePop" style="text-align:left;">Main Group Name</th>
														<th class="sortablePop" style="text-align:left;">Item Type</th>
														<th class="sortablePop" style="text-align:left;">Prefix</th>
														<th class="sortablePop" style="text-align:left;">IsActive</th>

													</tr>
												</thead>
												<tbody id="FormTableBody">
													<?php
													foreach ($table_data as $key => $value) {
													?>
														<tr class="get_ItemMainGroup" data-id="<?php echo $value["id"]; ?>">
															<td><?php echo $value['id']; ?></td>
															<td><?php echo $value['name']; ?></td>
															<td><?php echo $value['ItemTypeName']; ?></td>
															<td><?php echo $value['prefix']; ?></td>
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
		$("#MainGroupCode").dblclick(function() {
			$('#ItemMainGroup_List').modal('show');
			$('#ItemMainGroup_List').on('shown.bs.modal', function() {
				$('#myInput1').focus();
			})
		});

		// Empty and open create mode
		$("#MainGroupCode").focus(function() {
			var nextMainGroupID = $('#nextMainGroupID').val();
			$('#MainGroupCode').val(nextMainGroupID);
			$('#MainGroupName').val('');
			$('#Prefix').val('');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		// Cancel selected data
		$(".cancelBtn").click(function() {
			var nextMainGroupID = $('#nextMainGroupID').val();
			$('#MainGroupCode').val(nextMainGroupID);
			$('#MainGroupName').val('');
			$('#ItemType').val('').selectpicker('refresh');
			$('#Prefix').val('');
			$('#IsActive').val('Y').selectpicker('refresh');

			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();

		});

		$(document).on('click', '.get_ItemMainGroup', function() {
			MainGroupCode = $(this).attr("data-id");
			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/GetMainGroupDetailByID",
				dataType: "JSON",
				method: "POST",
				data: {
					MainGroupCode: MainGroupCode
				},
				beforeSend: function() {
					$('.searchh2').css('display', 'block');
					$('.searchh2').css('color', 'blue');
				},
				complete: function() {
					$('.searchh2').css('display', 'none');
				},
				success: function(data) {
					$('#MainGroupCode').val(data.MainGroupCode);
					$('#MainGroupName').val(data.MainGroupName);
					$('#ItemType').val(data.ItemType).selectpicker('refresh');
					$('#Prefix').val(data.Prefix);
					$('#IsActive').val(data.IsActive).selectpicker('refresh');


					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
				}
			});
			$('#ItemMainGroup_List').modal('hide');
		});

		$('#Prefix').blur(function(){ 
			Prefix = $(this).val();
			if(Prefix == ''){
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>invoice_items/CheckPrefixExit",
					dataType:"JSON",
					method:"POST",
					data:{Prefix:Prefix},
					beforeSend: function () {
						$('.searchh2').css('display','block');
						$('.searchh2').css('color','blue');
					},
					complete: function () {
						$('.searchh2').css('display','none');
					},
					success:function(data){
						if(data){
							alert("Prefix Already Exist.");
							$('#Prefix').val("");
						}
					}
				});
			}
		});

		// Save New Form
		$('.saveBtn').on('click', function() {
			MainGroupCode = $('#MainGroupCode').val();
			MainGroupName = $('#MainGroupName').val();
			ItemType = $('#ItemType').val();
			Prefix = $('#Prefix').val();
			IsActive = $('#IsActive').val();


			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/SaveMainGroup",
				dataType: "JSON",
				method: "POST",
				data: {
					MainGroupCode: MainGroupCode,
					MainGroupName: MainGroupName,
					ItemType: ItemType,
					Prefix: Prefix,
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
						var nextMainGroupID = $('#nextMainGroupID').val();
						var newGroupID = parseInt(nextMainGroupID) + 1;
						$('#MainGroupCode').val(newGroupID);
						$('#nextMainGroupID').val(newGroupID);
						$('#MainGroupName').val('');
						$('#ItemType').val('').selectpicker('refresh');
						$('#Prefix').val('');
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
		// Update Exiting Form
		$('.updateBtn').on('click', function() {
			MainGroupCode = $('#MainGroupCode').val();
			MainGroupName = $('#MainGroupName').val();
			ItemType = $('#ItemType').val();
			Prefix = $('#Prefix').val();
			IsActive = $('#IsActive').val();

			$.ajax({
				url: "<?php echo admin_url(); ?>invoice_items/UpdateMainGroup",
				dataType: "JSON",
				method: "POST",
				data: {
					MainGroupCode: MainGroupCode,
					MainGroupName: MainGroupName,
					ItemType: ItemType,
					Prefix: Prefix,
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
						var nextMainGroupID = $('#nextMainGroupID').val();
						$('#MainGroupCode').val(nextMainGroupID);
						$('#MainGroupName').val('');
						$('#ItemType').val('');
						$('#Prefix').val('');
						$('#IsActive').val('Y').selectpicker('refresh');

						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						refreshMainGroupList();
					} else {
						alert_float('warning', data.message || 'Something went wrong...');
					}
				}
			});
		});
	});
	$('.showListBtn').on('click', function() {
		$('#ItemMainGroup_List').modal('show');

		$('#ItemMainGroup_List').on('shown.bs.modal', function() {
			$('#myInput1').focus();
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_ItemMainGroup_List");
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
		var table = $("#table_ItemMainGroup_List tbody");
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
		$('#FormTableBody').load(location.href + ' #FormTableBody > *');
	}
</script>

<style>
	#item_code1 {
		text-transform: uppercase;
	}

	#table_ItemMainGroup_List td:hover {
		cursor: pointer;
	}

	#table_ItemMainGroup_List tr:hover {
		background-color: #ccc;
	}

	.table-ItemMainGroup_List {
		overflow: auto;
		max-height: 65vh;
		width: 100%;
		position: relative;
		top: 0px;
	}

	.table-ItemMainGroup_List thead th {
		position: sticky;
		top: 0;
		z-index: 1;
	}

	.table-ItemMainGroup_List tbody th {
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
	#MainGroupName,#Prefix {
		text-transform: uppercase;
	}
</style>