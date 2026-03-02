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
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>QC</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Parameter</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Parameter...</div>
								<div class="searchh4" style="display:none;">Please wait update Parameter...</div>
							</div>
							<br>
							
							<input type="hidden" name="form_mode" id="form_mode" value="add">
							<input type="hidden" name="nextId" id="nextId" value="<?= $next_id;?>">
							<div class="col-md-3">
								<div class="form-group">
									<label for="parameter_id">Parameter Id</label>
									<input type="text" name="parameter_id" id="parameter_id" class="form-control" value="<?= $next_id;?>" onkeypress="return isNumber(event)">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="parameter_name">Parameter Name</label>
									<input type="text" name="parameter_name" id="parameter_name" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="isActive">Active</label>
									<select name="isActive" id="isActive" class="selectpicker display-block" data-width="100%" data-none-selected-text="None Selected" data-live-search="true">
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row mtop7"> 
							<div class="col-md-12">
								<div class="action-buttons">
									<?php if (has_permission_new('qc_parameter', '', 'create')) { ?>
									<button type="button" class="btn btn-success btn-group-custom saveBtn">
										<i class="fa fa-save"></i>
										<span id="submitLabel">Save</span>
									</button>
									<?php } else { ?>
									<button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled">
										<i class="fa fa-save"></i>
										<span id="submitLabel">Save</span>
									</button>
									<?php } ?>
									
									<?php if (has_permission_new('qc_parameter', '', 'edit')) { ?>
									<button type="button" class="btn btn-success btn-group-custom updateBtn"><i class="fa fa-save"></i> Update</button>
									<?php } else { ?>
									<button type="button" class="btn btn-success btn-group-custom updateBtn2 disabled" style="margin-right: 25px;"><i class="fa fa-save"></i> Update</button>
									<?php } ?>
									
									<button type="button" class="btn btn-warning cancelBtn"><i class="fa fa-refresh"></i> Reset</button>
                					<button type="button" class="btn btn-info" onclick="$('#QcParameterModel').modal('show');"><i class="fa fa-list"></i> Show List</button>
								</div>
							</div>
							
						</div>
						
						<?php //echo form_close(); ?>
						
						<div class="clearfix"></div>
						<!-- Account Head List Model-->
						
						<div class="modal fade QcParameterModel" id="QcParameterModel" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">QC Parameter</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
										
										<div class="table-QcParameterModel tableFixHead2">
											<table class="tree table table-striped table-bordered table-QcParameterModel tableFixHead2" id="table_QcParameterModel" width="100%">
												<thead>
													<tr>
														<th class="sortablePop">Parameter ID</th>
														<th class="sortablePop">Parameter Name</th>
														<th class="sortablePop">Active</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($parameters_list as $key => $value) {
														?>
														<tr class="get_ParameterID" data-id="<?= $value["ItemParameterID"]; ?>">
															<td><?= $value["ItemParameterID"];?></td>
															<td><?= $value["ItemParameterName"];?></td>
															<td><?= ($value["IsActive"] == 'Y') ? 'Yes' : 'No';?></td>
														</tr>
													<?php } ?>
												</tbody>
											</table>   
										</div>
									</div>
									<div class="modal-footer" style="padding:0px;">
										<input type="text" id="myInput1"  name='myInput1' onkeyup="myFunction2()" placeholder="Search for names.."  style="float: left;width: 100%;">
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

<script>
    function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}
</script>
<script type="text/javascript" language="javascript" >
	// $(document).ready(function(){
		
		$('.updateBtn').hide();
		$('.updateBtn2').hide();  
		
		$("#parameter_id").dblclick(function(){
			$('#QcParameterModel').modal('show');
			$('#QcParameterModel').on('shown.bs.modal', function () {
				$('#myInput1').val('');
				$('#myInput1').focus();
			})
		});  
		
		// Cancel selected data
        $(".cancelBtn").click(function(){
            $('.saveBtn').show();
			$('.updateBtn').hide();
			$('.saveBtn2').show();
			$('.updateBtn2').hide();
			$('#parameter_id').val($('#nextId').val());
			$('#parameter_name').val('');
			$('select[name=isActive]').val('Y');
			$('.selectpicker').selectpicker('refresh');
			$('#form_mode').val('add');
		});
		
		// focus in Parameter ID 
		$('#parameter_id').on('change',function(){
			var parameter_id = $(this).val();
			GetQcParameterByID(parameter_id);
		});
		
		$('.get_ParameterID').on('click',function(){ 
            parameterID = $(this).attr("data-id");
			GetQcParameterByID(parameterID);
            $('#QcParameterModel').modal('hide');
		});

		function GetQcParameterByID(parameterID){
			$.ajax({
				url:"<?php echo admin_url(); ?>QC_Parameter/GetQcParameterByID",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{parameterID:parameterID},
				
				success:function(data){
                    if(empty(data)){
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
						$('#parameter_id').val($('#nextId').val());
						$('#parameter_name').val('');
						$('select[name=isActive]').val('Y');
                        $('.selectpicker').selectpicker('refresh');
                        $('#form_mode').val('add');
					}else{
                        $('#parameter_id').val(data.ItemParameterID);
                        $('#parameter_name').val(data.ItemParameterName);
                        $('select[name=isActive]').val(data.IsActive);
                        $('.selectpicker').selectpicker('refresh');
                        $('#form_mode').val('edit');
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
					}
				}
			});
		}

		function validate_fields(fields){ 
			let data = {};
			for(let i = 0; i < fields.length; i++){
				let value = $('#' + fields[i]).val();

				if(value === '' || value === null){
					let label = fields[i].replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
					alert_float('warning', 'Please enter ' + label);
					return false;
				} else {
					data[fields[i]] = value.trim();
				}
			}
			return data;
		}
		
		// Save New QC Parameter
        $('.saveBtn').on('click',function(){
			let fields = ['parameter_id', 'parameter_name', 'isActive'];
			let data = validate_fields(fields);
			if (data === false) {
				return false;
			}
			$.ajax({
				url:"<?php echo admin_url(); ?>QC_Parameter/SaveQcParameter",
				dataType:"JSON",
				method:"POST",
				data:{
					parameter_id : '',
					parameter_name : data.parameter_name,
					isActive : data.isActive
				},
				beforeSend: function () {
					$('.searchh3').css('display','block');
					$('.searchh3').css('color','blue');
				},
				complete: function () {
					$('.searchh3').css('display','none');
				},
				success:function(response){
					if(response.success == true){
						alert_float('success', response.message);
						
						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						$('#parameter_id').val(parseFloat(response.insert_id) + 1);
						$('#nextId').val(parseFloat(response.insert_id) + 1);
						$('#parameter_name').val('');
						$('select[name=isActive]').val('Y');
						$('.selectpicker').selectpicker('refresh');
						$('#form_mode').val('add');
						$('#table_QcParameterModel tbody').append(`<tr class="get_ParameterID" data-id="${response.insert_id}">
							<td>${response.insert_id}</td>
							<td>${data.parameter_name}</td>
							<td>${(data.isActive == 'Y') ? 'Yes' : 'No'}</td>
						</tr>`);
					}else{
						alert_float('warning', response.message);
					}
				}
			});
		}); 
		
		// Update Exiting Item
        $('.updateBtn').on('click',function(){ 
            let fields = ['parameter_id', 'parameter_name', 'isActive'];
			let data = validate_fields(fields);
			if (data === false) {
				return false;
			}
			$.ajax({
				url:"<?php echo admin_url(); ?>QC_Parameter/SaveQcParameter",
				dataType:"JSON",
				method:"POST",
				data:{
					parameter_id : data.parameter_id,
					parameter_name : data.parameter_name,
					isActive : data.isActive
				},
				beforeSend: function () {
					$('.searchh4').css('display','block');
					$('.searchh4').css('color','blue');
				},
				complete: function () {
					$('.searchh4').css('display','none');
				},
				success:function(response){
					if(response.success == true){
						alert_float('success', response.message);
						
						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
						$('#parameter_id').val($('#nextId').val());
						$('#parameter_name').val('');
						$('select[name=isActive]').val('Y');
						$('.selectpicker').selectpicker('refresh');
						$('#form_mode').val('add');

						let $row = $('#table_QcParameterModel tbody').find('tr.get_ParameterID[data-id="' + data.parameter_id + '"]');
						$row.html(`
							<td>${data.parameter_id}</td>
							<td>${data.parameter_name}</td>
							<td>${(data.isActive == 'Y') ? 'Yes' : 'No'}</td>
						`);

					}else{
						alert_float('warning', response.message);
					}
				}
			});
		});
		
	// });
	
</script>

<script>
    function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_QcParameterModel");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
            if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						} else if(td2){
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else{
							tr[i].style.display = "none";
						} 
					}
				}   
			}
		}
	}
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_QcParameterModel tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop").removeClass("asc desc");
		$(".sortablePop span").remove();
		
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		
		rows.sort(function (a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();
			
			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
				} else {
				return ascending
                ? valA.localeCompare(valB)
                : valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});
</script>
<style>
	#account_id { text-transform: uppercase;}
	#table_QcParameterModel td:hover {cursor: pointer;}
	#table_QcParameterModel tr:hover {background-color: #ccc;}
	.table-QcParameterModel { overflow: auto; max-height: 65vh; width:100%; position:relative; top: 0px; }
	.table-QcParameterModel thead th { position: sticky; top: 0; z-index: 1; }
	.table-QcParameterModel tbody th { position: sticky; left: 0; }
	table { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important; font-size:11px; line-height:1.42857143!important; vertical-align: middle !important;}
	th { background: #50607b; color: #fff !important; }
</style>
<style type="text/css">
	body {overflow: hidden;}
</style>

