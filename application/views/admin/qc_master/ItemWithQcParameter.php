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
								<li class="breadcrumb-item active text-capitalize"><b>QC Parameter</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Items</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<form action="" method="post" id="item_with_qc_parameter_form">
							<div class="row">
								<div class="col-md-12">
									<div class="searchh2" style="display:none;">Please wait fetching data...</div>
									<div class="searchh3" style="display:none;">Please wait Item linking...</div>
									<div class="searchh4" style="display:none;">Please wait update Item linking...</div>
								</div>
								<br>
								
								<input type="hidden" name="form_mode" id="form_mode" value="add">
								
								<div class="col-md-4">
									<div class="form-group">
										<label>Item</label>
										<?php // print_r($items_list);?>
										<select id="item_id" onchange="GetQcParameterByItemID(this.value)" required name="item_id" class="selectpicker" data-width="100%" data-none-selected-text="None selected" data-live-search="true" tabindex="-98">
											<option value="" selected disabled>Select Item</option>
											<?php 
												foreach($items_list as $value)
												{
													echo '<option value="'.$value['ItemID'].'">'.$value['ItemName'].'</option>';
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-12">
									<input type="hidden" id="row_id" value="0">
									<table class="table items table-striped table-bordered" width="100%">
										<thead>
											<tr> 
												<th style="width:40%">Parameter</th>
												<th style="width:10%">Min Value</th>
												<th style="width:10%">Max Value</th>
												<th style="width:10%">Base Value</th>
												<th style="width:10%">Calculation By</th>
												<th style="width:10%">Status</th>
												<th style="width:10%">Action</th>
											</tr>
										</thead>
										<tr id="fixed_row">
											<td>
												<select id="parameter_id" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
													<option value="" selected disabled>Select QC Parameter</option>
													<?php 
														foreach($parameters_list as $key => $value)
														{
															echo '<option value="'.$key.'">'.$value.'</option>';
														}
													?>
												</select>
											</td>
											<td>
												<input type="tel" id="min_value" class="form-control min_value" >
											</td>
											<td>
												<input type="tel" id="max_value" class="form-control max_value">
											</td>
											<td>
												<input type="tel" id="base_value" class="form-control base_value">
											</td>
											<td>
												<select id="calculation_by" class="form-control">
													<option value="1" selected>%</option>
													<option value="2">₹</option>
												</select>
											</td>
											<td>
												<select id="status" class="form-control">
													<option value="Y" selected>Yes</option>
													<option value="N">No</option>
												</select>
											</td>
											<td>
												<a href="#" class="btn btn-success" onclick="addRow()" style="float:right;padding: 2px;width: 30px; float:right;"><i class="fa fa-plus"></i></a>
											</td>
										</tr>
										<tbody id="qctbody"></tbody>
									</table>
								</div>
							</div>
							
							<div class="row mtop7"> 
								<div class="col-md-12">
									<div class="action-buttons">
										<?php if (has_permission_new('qc_parameter', '', 'create')) { ?>
										<button type="submit" class="btn btn-success btn-group-custom saveBtn">
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
										<button type="submit" class="btn btn-success btn-group-custom updateBtn"><i class="fa fa-save"></i> Update</button>
										<?php } else { ?>
										<button type="button" class="btn btn-success btn-group-custom updateBtn2 disabled" style="margin-right: 25px;"><i class="fa fa-save"></i> Update</button>
										<?php } ?>
										
										<button type="button" class="btn btn-warning cancelBtn"><i class="fa fa-refresh"></i> Reset</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php init_tail(); ?>

<script>
	$(document).on('input', 'input[type="tel"]', function () {
	    this.value = this.value.replace(/[^0-9]/g, '');
	});

	$(document).ready(function(){
		$('.updateBtn, .updateBtn2').hide();
	});

	// Reset form data
	$(".cancelBtn").click(function(){
		ResetForm();
	});
	
	function ResetForm(){
		$('.saveBtn, .saveBtn2').show();
		$('.updateBtn, .updateBtn2').hide();
		$('#qctbody').html('');
		$('#item_with_qc_parameter_form')[0].reset();
		$('#calculation_by').val('1');
		$('#status').val('Y');
		$('.selectpicker').selectpicker('refresh');
		$('#form_mode').val('add');
		$('#row_id').val(0);
	}
</script>
<script type="text/javascript" language="javascript">
	function validate_fields(fields){ 
		let data = {};
		for(let i = 0; i < fields.length; i++){
			let value = $('#' + fields[i]).val();

			if(value === '' || value === null){
				let label = fields[i].replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
				$('#'+fields[i]).focus();
				alert_float('warning', 'Please enter ' + label);
				return false;
			} else {
				data[fields[i]] = value.trim();
			}
		}
		return data;
	}

	function addRow(row = null){
		var row_id = $('#row_id').val();
		var next_id = parseInt(row_id) + 1;
		if(row == null){
			let fields = ['parameter_id', 'min_value', 'max_value', 'base_value', 'calculation_by', 'status'];
			let data = validate_fields(fields);
			if (data === false) {
				return false;
			}
			let min_value = parseFloat($('#min_value').val());
			let max_value = parseFloat($('#max_value').val());
			if(min_value > max_value){
				alert_float('warning', 'Min value should be less than max value');
				return false;
			}

			var row_btn = `<a href="#" class="btn btn-danger" onclick="$(this).closest('tr').remove();" style="float:right;padding: 2px;width: 30px; float:right;"><i class="fa fa-xmark"></i></a>`;
		}else{
			var row_btn = '';
		}
		
		$('#qctbody').append(`
			<tr>
				<td>
					<input type="hidden" name="update_id[]" id="update_id${next_id}" value="">
					<select id="parameter_id${next_id}" name="parameter_id[]" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98" required>
						<option value="" selected disabled>Select QC Parameter</option>
						<?php 
							foreach($parameters_list as $key => $value)
							{
								echo '<option value="'.$key.'">'.$value.'</option>';
							}
						?>
					</select>
				</td>
				<td>
					<input type="tel" id="min_value${next_id}" name="min_value[]" required class="form-control min_value" >
				</td>
				<td>
					<input type="tel" id="max_value${next_id}" name="max_value[]" required class="form-control max_value">
				</td>
				<td>
					<input type="tel" id="base_value${next_id}" name="base_value[]" required class="form-control base_value">
				</td>
				<td>
					<select id="calculation_by${next_id}" name="calculation_by[]" required class="form-control">
						<option value="1" selected>%</option>
						<option value="2">₹</option>
					</select>
				</td>
				<td>
					<select id="status${next_id}" name="status[]" required class="form-control">
						<option value="Y" selected>Yes</option>
						<option value="N">No</option>
					</select>
				</td>
				<td>${row_btn}</td>
			</tr>
		`);
		if(row == null){
			$('#parameter_id'+next_id).val($('#parameter_id').val());
			$('#min_value'+next_id).val($('#min_value').val());
			$('#max_value'+next_id).val($('#max_value').val());
			$('#base_value'+next_id).val($('#base_value').val());
			$('#calculation_by'+next_id).val($('#calculation_by').val());
			$('#status'+next_id).val($('#status').val());
			$('#parameter_id, #min_value, #max_value, #base_value').val('');
			$('#calculation_by').val('1');
			$('#status').val('Y');
		}
		$('#row_id').val(next_id);
		$('.selectpicker').selectpicker('refresh');
	}

	$('#item_with_qc_parameter_form').submit(function(e){
		e.preventDefault();
		var form_data = new FormData(this);
		form_data.append(
			'<?= $this->security->get_csrf_token_name(); ?>',
			$('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		);

		$.ajax({
			url:"<?php echo admin_url(); ?>QC_Parameter/SaveQcParameterItem",
			method:"POST",
			dataType:"JSON",
			data:form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function () {
				$('.searchh3').css('display','block');
				$('.searchh3').css('color','blue');
			},
			complete: function () {
				$('.searchh3').css('display','none');
			},
			success: function(response){
				if(response.success == true){
					alert_float('success', response.message);
					ResetForm();
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	});
	
	// Get QC Parameter By Item ID
	function GetQcParameterByItemID(itemID){
		$('#parameter_id, #min_value, #max_value, #base_value').val('');
		$('#calculation_by').val('1');
		$('#status').val('Y');
		$.ajax({
			url:"<?php echo admin_url(); ?>QC_Parameter/GetQcParameterByItemID",
			dataType:"JSON",
			method:"POST",
			cache: false,
			data:{itemID:itemID},
			
			success:function(data){
				if(empty(data)){
					$('.selectpicker').selectpicker('refresh');
					$('#form_mode').val('add');
					$('.saveBtn, .saveBtn2').show();
					$('.updateBtn, .updateBtn2').hide();
					$('#qctbody').html('');
					$('#row_id').val(0);
				}else{
					$('#row_id').val(0);
					$('#form_mode').val('edit');
					$('.saveBtn, .saveBtn2').hide();
					$('.updateBtn, .updateBtn2').show();
					$('#qctbody').html('');
					for(var i = 0; i < data.length; i++){
						addRow(2);
						$('#update_id'+(i+1)).val(data[i].id);
						$('#parameter_id'+(i+1)).val(data[i].ItemParameterID);
						$('#min_value'+(i+1)).val(data[i].MinValue);
						$('#max_value'+(i+1)).val(data[i].MaxValue);
						$('#base_value'+(i+1)).val(data[i].BaseValue);
						$('#calculation_by'+(i+1)).val(data[i].CalculationBy);
						$('#status'+(i+1)).val(data[i].Status);
					}
					$('.selectpicker').selectpicker('refresh');
				}
			}
		});
	}
	
</script>

<style>
	table { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important; font-size:11px; line-height:1.42857143!important; vertical-align: middle !important;}
	th { background: #50607b; color: #fff !important; }
</style>
<style type="text/css">
	body {overflow: hidden;}
</style>

