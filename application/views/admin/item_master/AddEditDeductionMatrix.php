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
								<li class="breadcrumb-item active text-capitalize"><b>Item Master</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Deduction Matrix</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<form action="" method="post" id="deduction_matrix_form">
							<div class="row">
								<input type="hidden" name="form_mode" id="form_mode" value="add">
								<div class="col-md-4">
									<div class="form-group">
										<label for="item_id" class="control-label">Item</label>
										<select id="item_id" onchange="GetQcParameterByItemID(this.value)" required name="item_id" class="form-control selectpicker" data-live-search="true">
											<option value="" selected disabled>None selected</option>
											<?php 
												foreach($items_list as $value)
												{
													echo '<option value="'.$value['ItemID'].'">'.$value['ItemName'].'</option>';
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="qc_parameter" class="control-label">QC Parameters</label>
										<select id="qc_parameter" onchange="getQcParameterDetails(this.value)" required name="qc_parameter" class="form-control selectpicker" data-live-search="true">
											<option value="" selected disabled>None selected</option>
										</select>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="min_value" class="control-label">Min Value</label>
										<input type="tel" id="min_value" name="min_value" class="form-control" required readonly>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="max_value" class="control-label">Max Value</label>
										<input type="tel" id="max_value" name="max_value" class="form-control" required readonly>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="base_value" class="control-label">Base Value</label>
										<input type="tel" id="base_value" name="base_value" class="form-control" required readonly>
									</div>
								</div>

								<div class="col-md-8">
									<input type="hidden" id="row_id" value="0">
									<table class="table items table-striped table-bordered" width="100%">
										<thead>
											<tr> 
												<th style="width:45%">Value</th>
												<th style="width:45%">Deduction <span id="calculation_by"></span></th>
												<th style="width:10%">Action</th>
											</tr>
										</thead>
										<tr id="fixed_row">
											<td>
												<input type="tel" id="value" class="form-control" >
											</td>
											<td>
												<input type="tel" id="deduction" class="form-control">
											</td>
											<td>
												<button type="button" class="btn btn-success" onclick="addRow()" style="float:right; padding: 2px; width: 30px;"><i class="fa fa-plus"></i></button>
											</td>
										</tr>
										<tbody id="qctbody"></tbody>
									</table>
								</div>
							</div>
							
							<div class="row mtop7"> 
								<div class="col-md-12">
									<button type="submit" class="btn btn-success saveBtn <?php echo (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
									<button type="submit" class="btn btn-success updateBtn <?php echo (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
									<button type="button" class="btn btn-danger" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
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
    this.value = this.value
		.replace(/[^0-9.]/g, '')   // allow digits and dot
		.replace(/(\..*?)\./g, '$1'); // allow only one dot
	});
	
	function ResetForm(){
		$('.saveBtn').show();
		$('.updateBtn').hide();
		$('#qctbody').html('');
		$('#deduction_matrix_form')[0].reset();
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
			let fields = ['value', 'deduction'];
			let data = validate_fields(fields);
			if (data === false) {
				return false;
			}

			var row_btn = `<button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove();" style="float:right; padding: 2px; width: 30px;"><i class="fa fa-xmark"></i></button>`;
		}else{
			var row_btn = '';
		}
		
		$('#qctbody').append(`
			<tr>
				<td>
					<input type="hidden" name="update_id[]" id="update_id${next_id}" value="">
					<input type="tel" id="value${next_id}" name="value[]" required class="form-control value" >
				</td>
				<td>
					<input type="tel" id="deduction${next_id}" name="deduction[]" required class="form-control deduction">
				</td>
				<td>${row_btn}</td>
			</tr>
		`);
		if(row == null){
			$('#value'+next_id).val($('#value').val());
			$('#deduction'+next_id).val($('#deduction').val());
			$('#value, #deduction').val('');
			$('#value').focus();
		}
		$('#row_id').val(next_id);
	}

	$('#deduction_matrix_form').submit(function(e){
		e.preventDefault();
		var form_data = new FormData(this);
		form_data.append(
			'<?= $this->security->get_csrf_token_name(); ?>',
			$('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		);

		$.ajax({
			url:"<?php echo admin_url(); ?>ItemMaster/SaveDeductionMatrix",
			method:"POST",
			dataType:"JSON",
			data:form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function () {
				$('.saveBtn, .updateBtn').attr('disabled', true);
			},
			complete: function () {
				$('.saveBtn, .updateBtn').attr('disabled', false);
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
		$.ajax({
			url:"<?php echo admin_url(); ?>ItemMaster/GetQcParameterByItemID",
			dataType:"JSON",
			method:"POST",
			data:{
				itemID:itemID,
				'<?php echo $this->security->get_csrf_token_name(); ?>':
				'<?php echo $this->security->get_csrf_hash(); ?>'
			},
			success:function(data){
				if(data.success == true){
					let html = `<option value="" selected disabled>None selected</option>`;
					for(var i = 0; i < data.data.length; i++){
						html += `<option value="${data.data[i].ItemParameterID}">${data.data[i].ItemParameterName}</option>`;
					}
					$('#qc_parameter').html(html);
					$('.selectpicker').selectpicker('refresh');
				}
			}
		});
	}
	
	// Get QC Parameter Details & deduction matrix
	function getQcParameterDetails(qcParameterID){
		let itemID = $('#item_id').val();
		$.ajax({
			url:"<?php echo admin_url(); ?>ItemMaster/GetQcParameterDetails",
			dataType:"JSON",
			method:"POST",
			data:{
				itemID:itemID,
				qcParameterID:qcParameterID,
				'<?php echo $this->security->get_csrf_token_name(); ?>':
				'<?php echo $this->security->get_csrf_hash(); ?>'
			},
			success:function(res){
				if(res.success == true){
					let pd = res.data.parameterDetails;
					$('#max_value').val(pd.MaxValue);
					$('#min_value').val(pd.MinValue);
					$('#base_value').val(pd.BaseValue);
					$('#calculation_by').html(pd.CalculationBy == 1 ? 'in %' : 'in ₹');
					$('#value').focus();
					$('#qctbody').html('');
					$('#row_id').val(0);
					let dm = res.data.deductionMatrixList;
					for(var i = 0; i < dm.length; i++){
						addRow(2);
						$('#update_id'+(i+1)).val(dm[i].id);
						$('#value'+(i+1)).val(dm[i].Value);
						$('#deduction'+(i+1)).val(dm[i].Deduction);
					}
					if(dm.length > 0){
						$('#form_mode').val('edit');
					}else{
						$('#form_mode').val('add');
					}
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