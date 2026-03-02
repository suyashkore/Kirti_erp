<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-9">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Create Ledger</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
						<?php //echo form_open('admin/accounts_master/',array('id'=>'accounting_head')); ?>
						<div class="row">
							<div class="col-md-3">
								<?php 
									$selected_company = $this->session->userdata('root_company');
									$FY = $this->session->userdata('finacial_year');
								?>
								<?php
									$date_attrs = array();
									$date_attrs2 = array();
									if(isset($account_detail)){
										
										$date_attrs['disabled'] = true;
										$opening_bal = get_opening_bal($value);
									}
									if(is_admin() || has_permission_new('account_head', '', 'edit')){
										
										}else{
										$date_attrs2['disabled'] = true;
									}
								?>
								<?php
									$next_cust_number = (int) get_option('next_account_ledger_number');
									
									$prefix = "L";
									
									$next_cust_number = $prefix.str_pad($next_cust_number,5,'0',STR_PAD_LEFT);
								?>
								
								
								<input type="hidden" value="" name="edit_account_id">
								
								<?php echo render_input('account_id','Account ID',$next_cust_number,'',$date_attrs); ?>
								
								<input type="hidden" id="PlantID" name="PlantID" class="form-control" value="<?php echo $selected_company;?>">
							</div>
							<div class="col-md-3">
								<?php $value = (isset($account_detail) ? $account_detail->company : ''); ?>
								<div class="form-group" app-field-wrapper="account_name">
									<small class="req text-danger">* </small>
									<label for="account_name" class="control-label">Account Name</label>
									<input type="text" id="account_name" name="account_name" class="form-control" value="<?= $value?>">
								</div>		
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="MainAccount_Group">
									<small class="req text-danger">* </small>
									<label for="MainAccount_Group" class="control-label">Account Main Group</label>
									<div class="dropdown bootstrap-select bs3 open" style="width: 100%;">
										<select id="MainAccount_Group" name="MainAccount_Group" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
											<option value=""></option>
											<?php foreach($account_group as $group)
												{
												?>
												<option value="<?= $group['ActGroupID']?>"><?= $group['ActGroupName']?></option>
												<?php
												}
											?>
										</select>
									</div>
								</div>	
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="SubAccount_Group1">
									<small class="req text-danger">* </small>
									<label for="SubAccount_Group1" class="control-label">Account Sub Group 1</label>
									<div class="dropdown bootstrap-select bs3 open" style="width: 100%;">
										<select id="SubAccount_Group1" name="SubAccount_Group1" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
											<option value=""></option>
											
										</select>
									</div>
								</div>	
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="SubAccount_Group2">
									<small class="req text-danger">* </small>
									<label for="SubAccount_Group2" class="control-label">Account Sub Group 2</label>
									<div class="dropdown bootstrap-select bs3 open" style="width: 100%;">
										<select id="SubAccount_Group2" name="SubAccount_Group2" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
											<option value=""></option>
											
										</select>
									</div>
								</div>	
							</div>
							
							
							<div class="col-md-3">
								<?php
									$staff_user_id = $this->session->userdata('staff_user_id');
								?>
								<?php //echo render_input('opening_bal','Opening Bal.'); ?>
								<label for="opening_bal" class="control-label">Opening Balance</label>
								<input type="text" maxlength="12"  name="opening_bal" pattern="[0-9]" id="opening_bal" class="form-control" value="" >
								<span class="opening_bal_denger" style="color:red;"></span>
								<?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>
								<input type="hidden" name="staffid" value="<?php echo $staff_user_id; ?>" id="staffid">
							</div>
							<div class="col-md-3">
								<label for="closing_bal" class="control-label">Closing Balance</label>
								<input type="text" maxlength="12"  name="closing_bal" pattern="[0-9]" id="closing_bal" class="form-control" value="" >
							</div>
							<div class="col-md-3">
								<label for="ad_code" class="control-label">AD Code</label>
								<input type="text" maxlength="12"  name="ad_code" id="ad_code" class="form-control" value="" >
							</div>
							<div class="clearfix"></div> 
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="ifsc">
									<label for="ifsc" class="control-label">IFSC</label>
									<input type="text" name="ifsc" onblur="getBankDetail(this.value)" id="ifsc" class="form-control text-uppercase" value="">
									<span class="ifsc_danger" style="color:red;"></span>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="accname">
									<small class="req text-danger"> </small>
									<label for="accname" class="control-label">Account Name</label>
									<input type="text" name="accname" id="accname" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="accountno">
									<small class="req text-danger"> </small>
									<label for="accountno" class="control-label">Account Number</label>
									<input type="text" name="accountno" id="accountno" onkeypress="return isNumber(event)" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="accounttype">
									<small class="req text-danger"> </small>
									<label for="accounttype" class="control-label">Account Type</label>
									<select name="accounttype" id="accounttype" class="form-control" >
										<option value="">select</option>
										<option value="CC">CC</option>
										<option value="OD">OD</option>
										<option value="CA">CA</option>
										<option value="TL">TL</option>
										<option value="LC">LC</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="bankname">
									<small class="req text-danger"> </small>
									<label for="bankname" class="control-label">Bank Name</label>
									<input type="text" name="bankname" id="bankname" readonly class="form-control" value="">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" app-field-wrapper="bankaddress">
									<small class="req text-danger"> </small>
									<label for="bankaddress" class="control-label">Bank Address</label>
									<input type="text" name="bankaddress" id="bankaddress" readonly class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
								<small class="req text-danger">* </small>
									<label class="control-label" for="tax"><?php echo _l('GST %'); ?></label>
									<select class="selectpicker display-block" data-width="100%" id="tax" name="tax" data-none-selected-text="Non Selected">
										<?php foreach($taxes as $tax){ ?>
											<option value="<?php echo $tax['id']; ?>" ><?php echo $tax['taxrate']; ?>%</option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row">
							<!--<div class="col-md-3">
								<?php echo render_input('security_dep','Security Dep.'); ?>
							</div>-->
							<div class="col-md-3">
								<?php $value = (isset($account_detail) ? $account_detail->Blockyn : ''); ?>
								<div class="form-group">
									<label for="block_ac" class="control-label">Block A/C</label>
									<select class="form-control " name="block_ac" data-live-search="true" id="block_ac">
										<option value="N" <?php if($value == "N") echo "selected";?>>No</option>
										<option value="Y" <?php if($value == "Y") echo "selected";?>>Yes</option>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<?php $value = (isset($account_detail) ? $account_detail->StartDate : date('Y-m-d')); 
									if(isset($account_detail)){
										$new_val = substr($value,0,10);
										}else{
										$new_val = $value;
									}
									
								?>
								
								<?php echo render_date_input('start_date','Start Date',$new_val,$date_attrs2); ?>
							</div>
							<div class="col-md-3">
								<small class="req text-danger">* </small>
								<label for="payment_term" class="control-label">Payment Terms (In Days)</label>
								<input type="text" maxlength="12" onkeypress="return isNumber(event)"  name="payment_term" id="payment_term" class="form-control" value="" >
							</div>
							<div class="col-md-3">
								<div class="form-group">
								<small class="req text-danger"> </small>
									<label class="control-label" for="hsn_code">HSN Code</label>
									<select class="selectpicker display-block" data-width="100%" id="hsn_code" name="hsn_code" data-none-selected-text="Non Selected">
										<?php foreach($hsn_data as $hsn){ ?>
											<option value="<?php echo $hsn['name']; ?>" ><?php echo $hsn['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row">
							
							<div class="col-md-12">
								<?php if (has_permission('account_head', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn"  style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission('account_head', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtn"  style="margin-right: 25px;">Update</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
								<?php
								}?>
								
								<button type="button" class="btn btn-default cancelBtn" >Cancel</button>
							</div>
							
						</div>
						
						<?php //echo form_close(); ?>
						
						<div class="clearfix"></div>
						<!-- Account Head List Model-->
						
						<div class="modal fade AccountHead_List" id="AccountHead_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">AccountHead List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
										
										<div class="table-AccountHead_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-AccountHead_List tableFixHead2" id="table_AccountHead_List" width="100%">
												<thead>
													<tr>
														<th class="sortablePop" style="text-align:left;">AccountID</th>
														<th class="sortablePop" style="text-align:left;">Account Name</th>
														<th class="sortablePop" style="text-align:left;">MainGroup Name</th>
														<th class="sortablePop" style="text-align:left;">SubGroup1 Name</th>
														<th class="sortablePop" style="text-align:left;">SubGroup2 Name</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($AccountHead as $key => $value) {
														?>
														<tr class="get_AccountID" data-id="<?php echo $value["AccountID"]; ?>">
															<td><?php echo $value['AccountID'];?></td>
															<td><?php echo $value['company'];?></td>
															<td><?php echo $value["MainGroupName"];?></td>
															<td><?php echo $value["SubActGroupName1"];?></td>
															<td><?php echo $value["SubActGroupName2"];?></td>
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
	function getBankDetail(ifsccode){
		var xhr = new XMLHttpRequest();
		var url = 'https://ifsc.razorpay.com/' + ifsccode;
		
		xhr.onreadystatechange = function() {
			if (xhr.readyState === 4 && xhr.status === 200) {
				var bankDetails = JSON.parse(xhr.responseText);
				var bankName = bankDetails.BANK;
				var bankAddress = bankDetails.ADDRESS;
				
				// Display the bank name and address
				document.getElementById('bankname').value = bankName;
				document.getElementById('bankaddress').value = bankAddress;
				} else if (xhr.readyState === 4 && xhr.status !== 200) {
				// Handle error
				alert('Invalid IFSC Code');
				$('#ifsc').val('');
				$('#bankname').val('');
				$('#bankaddress').val('');
			}
		};
		
		xhr.open('GET', url, true);
		xhr.send();
	}
</script>
<script>
	$('#MainAccount_Group').change(function() {
		var Account_Group = $(this).val();
		$('select[name=SubAccount_Group1]').html('');
		$('select[name=SubAccount_Group2]').html('');
		$('.selectpicker').selectpicker('refresh');
		
		if(Account_Group != ""){
			$.ajax({
				url: "<?php echo admin_url(); ?>Accounts_master/GetSubGroupOneByMainGroupId",
				dataType:'JSON',
				type: 'post',
				data : {Account_Group:Account_Group},
				success: function(data) {
					var optionsHTML = '<option value="">Non selected</option>';
					$.each(data, function(index, option) {
						optionsHTML += '<option value="' + option.SubActGroupID1 + '">' + option.SubActGroupName + '</option>';
					});
					$('select[name=SubAccount_Group1]').html(optionsHTML);
					$('.selectpicker').selectpicker('refresh');
				},
			});
			}else{
			$('select[name=SubAccount_Group1]').html('');
			$('select[name=SubAccount_Group2]').html('');
			$('.selectpicker').selectpicker('refresh');
		}
	});
	
	$('#SubAccount_Group1').change(function() {
		var SubAccount_Group1 = $(this).val();
		
		
		$('select[name=SubAccount_Group2]').html('');
		$('.selectpicker').selectpicker('refresh');
		
		if(SubAccount_Group1 != ""){
			$.ajax({
				url: "<?php echo admin_url(); ?>Accounts_master/GetSubGroupTwoBySubAccount_Group1",
				dataType:'JSON',
				type: 'post',
				data : {SubAccount_Group1:SubAccount_Group1},
				success: function(data) {
					var optionsHTML = '<option value="">Non selected</option>';
					$.each(data, function(index, option) {
						optionsHTML += '<option value="' + option.SubActGroupID + '">' + option.SubActGroupName + '</option>';
					});
					$('select[name=SubAccount_Group2]').html(optionsHTML);
					$('.selectpicker').selectpicker('refresh');
				},
			});
			}else{
			$('select[name=SubAccount_Group2]').html('');
			$('.selectpicker').selectpicker('refresh');
		}
	});
	
	
</script>
<script>
	$(function(){
        'use strict';
        appValidateForm($('#accounting_head'), {
            
            account_id: {
				required: true,
				remote: {
					url: site_url + "admin/misc/accountID_exists",
					type: 'post',
					data: {
						account_id: function() {
							return $('input[name="account_id"]').val();
						},
					}
				}
			},
			account_name: 'required',
			Account_Group: 'required',
			
		});
		
		
	});
</script>

<script>
	$(document).ready(function(){
		$('.updateBtn').hide();
		$('.updateBtn2').hide();
		
		
		$("#account_id").dblclick(function(){
			$('#AccountHead_List').modal('show');
			$('#AccountHead_List').on('shown.bs.modal', function () {
				$('#myInput1').val('');
				$('#myInput1').focus();
			})
			
		});
		// ItemID Typing Validation
		$("#account_id").keypress(function (e) {
			var keyCode = e.keyCode || e.which;
			if(keyCode == ""){
				$("#lblError").html("");
				}else{
				//Regex for Valid Characters i.e. Alphabets and Numbers.
				var regex = /^[A-Za-z0-9]+$/;
				//Validate TextBox value against the Regex.
				var isValid = regex.test(String.fromCharCode(keyCode));
				if (!isValid) {
					$("#lblError").html("Only Alphabets and Numbers allowed.");
					}else{
					$("#lblError").html("");
				}
				return isValid;
			}
		});
		
		$("#account_name").keypress(function (e) {
			var keyCode = e.keyCode || e.which;
			if (keyCode == "") {
				$("#lblError").html("");
				} else {
				var regex = /^[A-Za-z0-9\s]+$/; // Updated regex to allow letters and spaces
				var isValid = regex.test(String.fromCharCode(keyCode));
				return isValid;
			}
		});
		
		// Empty and open create mode
		$("#account_id").focus(function(){
			$('#account_id').val('');
			$('#account_name').val('');
			$('#opening_bal').val('');
			$('#ifsc').val('');
			$('#bankname').val('');
			$('#bankaddress').val('');
			$('#accname').val('');
			$('#accountno').val('');
			$('#accounttype').val('');
			$('#closing_bal').val('');
			$('#ad_code').val('');
			$('#payment_term').val('');
			$('#hsn_code').val('');
			$('.selectpicker').selectpicker('refresh');
			$('#tax').val('');
			$('.selectpicker').selectpicker('refresh');
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = today.getFullYear();
			
			today = dd + '/' + mm + '/' + yyyy;
			$('#start_date').val(today);
			
			
			$('select[name=MainAccount_Group]').val('');
			$('select[name=SubAccount_Group1]').html('');
			$('select[name=SubAccount_Group2]').html('');
			$('.selectpicker').selectpicker('refresh');
			
			$('select[name=block_ac]').val('N');
			$('.selectpicker').selectpicker('refresh');
			
			// var staffid = $('#staffid').val();
			// if(staffid !== "3"){
			// }
				$("#opening_bal").prop("readonly", false);
			$('.saveBtn').removeAttr('disabled');           
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
		});
		
		// Cancel selected data
		$(".cancelBtn").click(function(){
			
			$('#account_id').val('');
			$('#account_name').val('');
			$('#opening_bal').val('');
			$('#ifsc').val('');
			$('#bankname').val('');
			$('#bankaddress').val('');
			$('#accname').val('');
			$('#accountno').val('');
			$('#accounttype').val('');
			$('#closing_bal').val('');
			$('#ad_code').val('');
			$('#payment_term').val('');
			$('#hsn_code').val('');
			$('.selectpicker').selectpicker('refresh');
			$('#tax').val('');
			$('.selectpicker').selectpicker('refresh');
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = today.getFullYear();
			
			today = dd + '/' + mm + '/' + yyyy;
			$('#start_date').val(today);
			
			
			$('select[name=MainAccount_Group]').val('');
			$('select[name=SubAccount_Group1]').html('');
			$('select[name=SubAccount_Group2]').html('');
			$('.selectpicker').selectpicker('refresh');
			
			$('select[name=block_ac]').val('N');
			$('.selectpicker').selectpicker('refresh');
			
			// var staffid = $('#staffid').val();
			// if(staffid !== "3"){
			// }
				$("#opening_bal").prop("readonly", false);
			$('.saveBtn').removeAttr('disabled');           
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
		});
		
		// On Blur ItemID Get All Date
		$('#account_id').blur(function(){ 
			AccountID = $(this).val();
			if(AccountID == ''){
				
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>Accounts_master/GetAccountDetailByID",
					dataType:"JSON",
					method:"POST",
					data:{AccountID:AccountID},
					beforeSend: function () {
						$('.searchh2').css('display','block');
						$('.searchh2').css('color','blue');
					},
					complete: function () {
						$('.searchh2').css('display','none');
					},
					success:function(data){
						init_selectpicker();
						if(data == null){
							
                			$('#account_name').val('');
                			$('#opening_bal').val('');
                			$('#ifsc').val('');
                			$('#bankname').val('');
                			$('#bankaddress').val('');
                			$('#accname').val('');
                			$('#accountno').val('');
                			$('#accounttype').val('');
                			$('#closing_bal').val('');
                			$('#ad_code').val('');
                			$('#payment_term').val('');
                			$('#hsn_code').val('');
							$('.selectpicker').selectpicker('refresh');
                			$('#tax').val('');
                			$('.selectpicker').selectpicker('refresh');
                			var today = new Date();
                			var dd = String(today.getDate()).padStart(2, '0');
                			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                			var yyyy = today.getFullYear();
                			
                			today = dd + '/' + mm + '/' + yyyy;
                			$('#start_date').val(today);
                			
                			
                			$('select[name=MainAccount_Group]').val('');
                			$('select[name=SubAccount_Group1]').html('');
                			$('select[name=SubAccount_Group2]').html('');
                			$('.selectpicker').selectpicker('refresh');
                			
                			$('select[name=block_ac]').val('N');
                			$('.selectpicker').selectpicker('refresh');
                			
                			// var staffid = $('#staffid').val();
                			// if(staffid !== "3"){
							// }
                				$("#opening_bal").prop("readonly", false);
                			$('.saveBtn').removeAttr('disabled');           
                			$('.saveBtn').show();
                			$('.saveBtn2').show();
                			$('.updateBtn').hide();
                			$('.updateBtn2').hide();
						}else{
							$('#account_id').val(data.AccountID);
							$('#account_name').val(data.company);
							$('#opening_bal').val(data.BAL1);
							$('#ifsc').val(data.ifsc_code);
							$('#bankname').val(data.bank_name);
							$('#bankaddress').val(data.bank_add);
							$('#accname').val(data.acc_name);
							$('#accountno').val(data.acc_no);
							$('#accounttype').val(data.acc_type);
							$('#closing_bal').val(data.closing_bal);
							$('#ad_code').val(data.ad_code);
							$('#payment_term').val(data.payment_term);
							$('#hsn_code').val(data.hsn_code);
							$('.selectpicker').selectpicker('refresh');
							$('#tax').val(data.tax);
							$('.selectpicker').selectpicker('refresh');
							if(data.StartDate !== null){
								var date = data.StartDate.substring(0, 10)
								var date_new = date.split("-").reverse().join("/");
								$('#start_date').val(date_new);
							}
							$('select[name=MainAccount_Group]').val(data.ActGroupID);
							
							var optionsHTMLSubGrp1 = '<option value="">Non selected</option>';
							$.each(data.SubGroupData1, function(index, option) {
								optionsHTMLSubGrp1 += '<option value="' + option.SubActGroupID1 + '">' + option.SubActGroupName + '</option>';
							});
							$('select[name=SubAccount_Group1]').html(optionsHTMLSubGrp1);
							$('select[name=SubAccount_Group1]').val(data.SubActGroupID1);
							
							var optionsHTMLSubGrp2 = '<option value="">Non selected</option>';
							$.each(data.SubGroupData2, function(index, option) {
								optionsHTMLSubGrp2 += '<option value="' + option.SubActGroupID + '">' + option.SubActGroupName + '</option>';
							});
							$('select[name=SubAccount_Group2]').html(optionsHTMLSubGrp2);
							$('select[name=SubAccount_Group2]').val(data.SubActGroupID);
							
							
							$('.selectpicker').selectpicker('refresh');
							
							$('select[name=block_ac]').val(data.Blockyn);
							$('.selectpicker').selectpicker('refresh');
							<?php
							    if(has_permission_new('openingbaledit', '', 'edit')){
							?>
							    var is_accessable = 1;
							<?php
							    }else{
							 ?>
							    var is_accessable = 0;
							 <?php
							    }
							?>
        					if(is_accessable == "0"){
        						$("#opening_bal").prop("readonly", true);
        					}
							$('.saveBtn').hide();
							$('.updateBtn').show();
							$('.saveBtn2').hide();
							$('.updateBtn2').show();
						} 
					}
				});
			}
			
		});
		
		$('.get_AccountID').on('click',function(){ 
			AccountID = $(this).attr("data-id");
			$.ajax({
				url:"<?php echo admin_url(); ?>Accounts_master/GetAccountDetailByID",
				dataType:"JSON",
				method:"POST",
				data:{AccountID:AccountID},
				beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');
				},
				success:function(data){
					init_selectpicker();
					$('#account_id').val(data.AccountID);
					$('#account_name').val(data.company);
					$('#opening_bal').val(data.BAL1);
					$('#ifsc').val(data.ifsc_code);
					$('#bankname').val(data.bank_name);
					$('#bankaddress').val(data.bank_add);
					$('#accname').val(data.acc_name);
					$('#accountno').val(data.acc_no);
					$('#accounttype').val(data.acc_type);
					$('#closing_bal').val(data.closing_bal);
					$('#ad_code').val(data.ad_code);
					$('#payment_term').val(data.payment_term);
					$('#hsn_code').val(data.hsn_code);
					$('.selectpicker').selectpicker('refresh');
					$('#tax').val(data.tax);
					$('.selectpicker').selectpicker('refresh');
					if(data.StartDate !== null){
						var date = data.StartDate.substring(0, 10)
						var date_new = date.split("-").reverse().join("/");
						$('#start_date').val(date_new);
					}
					$('select[name=MainAccount_Group]').val(data.ActGroupID);
					
					var optionsHTMLSubGrp1 = '<option value="">Non selected</option>';
					$.each(data.SubGroupData1, function(index, option) {
						optionsHTMLSubGrp1 += '<option value="' + option.SubActGroupID1 + '">' + option.SubActGroupName + '</option>';
					});
					$('select[name=SubAccount_Group1]').html(optionsHTMLSubGrp1);
					$('select[name=SubAccount_Group1]').val(data.SubActGroupID1);
					
					var optionsHTMLSubGrp2 = '<option value="">Non selected</option>';
					$.each(data.SubGroupData2, function(index, option) {
						optionsHTMLSubGrp2 += '<option value="' + option.SubActGroupID + '">' + option.SubActGroupName + '</option>';
					});
					$('select[name=SubAccount_Group2]').html(optionsHTMLSubGrp2);
					$('select[name=SubAccount_Group2]').val(data.SubActGroupID);
					$('.selectpicker').selectpicker('refresh');
					
					$('select[name=block_ac]').val(data.Blockyn);
					$('.selectpicker').selectpicker('refresh');
					<?php
							    if(has_permission_new('openingbaledit', '', 'edit')){
							?>
							    var is_accessable = 1;
							<?php
							    }else{
							 ?>
							    var is_accessable = 0;
							 <?php
							    }
							?>
					if(is_accessable == "0"){
						$("#opening_bal").prop("readonly", true);
					}
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
				}
			});
			$('#AccountHead_List').modal('hide');
		});
		
		// Save New Item
		$('.saveBtn').on('click',function(){ 
			AccountID = $('#account_id').val();
			company = $('#account_name').val();
			MainAccount_Group = $('#MainAccount_Group').val();
			SubAccount_Group1 = $('#SubAccount_Group1').val();
			SubAccount_Group2 = $('#SubAccount_Group2').val();
			BAL1 = $('#opening_bal').val();
			Blockyn = $('#block_ac').val();
			StartDate = $('#start_date').val();
			
			ifsc = $('#ifsc').val();
			bankname = $('#bankname').val();
			bankaddress = $('#bankaddress').val();
			accname = $('#accname').val();
			accountno = $('#accountno').val();
			accounttype = $('#accounttype').val();
			closing_bal =	$('#closing_bal').val();
			ad_code = $('#ad_code').val();
			payment_term =	$('#payment_term').val();
			tax =	$('#tax').val();
			hsn_code =	$('#hsn_code').val();
			if(AccountID == ''){
				alert('please enter AccountID');
				$('.saveBtn').removeAttr('disabled');
				$('#account_id').focus();
				}else if($.trim(company) == ""){
				alert('please enter Account Name');
				$('.saveBtn').removeAttr('disabled');
				$('#account_name').focus();
				}else if(MainAccount_Group == ""){
				alert('please Select Account Group');
				$('.saveBtn').removeAttr('disabled');
				$('#MainAccount_Group').focus();
				}else if(SubAccount_Group1 == ""){
				alert('please Select Sub Account Group 1');
				$('.saveBtn').removeAttr('disabled');
				$('#SubAccount_Group1').focus();
				}else if(SubAccount_Group2 == ""){
				alert('please Select Sub Account Group 2');
				$('.saveBtn').removeAttr('disabled');
				$('#SubAccount_Group2').focus();
				}else if(payment_term == ''){
				alert('Please Enter Payment Terms');
				$('#payment_term').focus();
				}else if(tax == ''){
				alert('Please Select GST');
				$('#tax').focus();
				}else{
				if(ifsc == ''){
					var ret = true;
					}else{
					if(accname == ''){
						alert('please  enter Account Name');
						$('#accname').focus();
						}else if(accountno == ''){
						alert('please  enter Account No.');
						$('#accountno').focus();
						}else if(accounttype == ''){
						alert('please  enter Account Type');
						$('#accounttype').focus();
						}else if(bankname == ''){
						alert('please Enter Correct IFSC Code');
						$('#ifsc').focus();
						}else if(bankaddress == ''){
						alert('please Enter Correct IFSC Code');
						$('#ifsc').focus();
						}else{
						var ret = true;
					}
				}
				if(ret == true){ 
					$.ajax({
						url:"<?php echo admin_url(); ?>Accounts_master/SaveHeadAccountID",
						dataType:"JSON",
						method:"POST",
						data:{AccountID:AccountID,company:company,BAL1:BAL1,Blockyn:Blockyn,MainAccount_Group:MainAccount_Group,
							SubAccount_Group1:SubAccount_Group1,SubAccount_Group2:SubAccount_Group2,StartDate:StartDate,
							ifsc:ifsc,bankname:bankname,bankaddress:bankaddress,accname:accname,accountno:accountno,accounttype:accounttype,
							closing_bal:closing_bal,ad_code:ad_code,payment_term:payment_term,hsn_code:hsn_code,tax:tax
						},
						beforeSend: function () {
							$('.searchh3').css('display','block');
							$('.searchh3').css('color','blue');
						},
						complete: function () {
							$('.searchh3').css('display','none');
						},
						success:function(data){
							if(data == true){
								alert_float('success', 'Record created successfully...');
                    			$('#account_id').val('<?= $next_ACNO_ledger??''?>');
                    			$('#account_name').val('');
                    			$('#opening_bal').val('');
                    			$('#ifsc').val('');
                    			$('#bankname').val('');
                    			$('#bankaddress').val('');
                    			$('#accname').val('');
                    			$('#accountno').val('');
                    			$('#accounttype').val('');
                    			$('#closing_bal').val('');
                    			$('#ad_code').val('');
                    			$('#payment_term').val('');
                    			$('#hsn_code').val('');
								$('.selectpicker').selectpicker('refresh');
                    			$('#tax').val('');
                    			$('.selectpicker').selectpicker('refresh');
                    			var today = new Date();
                    			var dd = String(today.getDate()).padStart(2, '0');
                    			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    			var yyyy = today.getFullYear();
                    			
                    			today = dd + '/' + mm + '/' + yyyy;
                    			$('#start_date').val(today);
                    			
                    			
                    			$('select[name=MainAccount_Group]').val('');
                    			$('select[name=SubAccount_Group1]').html('');
                    			$('select[name=SubAccount_Group2]').html('');
                    			$('.selectpicker').selectpicker('refresh');
                    			
                    			$('select[name=block_ac]').val('N');
                    			$('.selectpicker').selectpicker('refresh');
                    			
                    			// var staffid = $('#staffid').val();
                    			// if(staffid !== "3"){
								// }
                    				$("#opening_bal").prop("readonly", false);
                    			$('.saveBtn').removeAttr('disabled');           
                    			$('.saveBtn').show();
                    			$('.saveBtn2').show();
                    			$('.updateBtn').hide();
                    			$('.updateBtn2').hide();
								location.reload();
								}else{
								$('.saveBtn').removeAttr('disabled');
								alert_float('warning', 'Something went wrong...');
							}
						}
					}); 
				}
			}
		});
		// Update Exiting Item
		$('.updateBtn').on('click',function(){ 
			AccountID = $('#account_id').val();
			company = $('#account_name').val();
			MainAccount_Group = $('#MainAccount_Group').val();
			SubAccount_Group1 = $('#SubAccount_Group1').val();
			SubAccount_Group2 = $('#SubAccount_Group2').val();
			BAL1 = $('#opening_bal').val();
			Blockyn = $('#block_ac').val();
			StartDate = $('#start_date').val();
			
			ifsc = $('#ifsc').val();
			bankname = $('#bankname').val();
			bankaddress = $('#bankaddress').val();
			accname = $('#accname').val();
			accountno = $('#accountno').val();
			accounttype = $('#accounttype').val();
			closing_bal =	$('#closing_bal').val();
			ad_code = $('#ad_code').val();
			payment_term =	$('#payment_term').val();
			hsn_code =	$('#hsn_code').val();
			tax =	$('#tax').val();
			
			if(AccountID == ''){
				alert('please enter AccountID');
				$('.saveBtn').removeAttr('disabled');
				$('#account_id').focus();
				}else if($.trim(company) == ""){
				alert('please enter Account Name');
				$('.saveBtn').removeAttr('disabled');
				$('#account_name').focus();
				}else if(MainAccount_Group == ""){
				alert('please Select Account Group');
				$('.saveBtn').removeAttr('disabled');
				$('#MainAccount_Group').focus();
				}else if(SubAccount_Group1 == ""){
				alert('please Select Sub Account Group 1');
				$('.saveBtn').removeAttr('disabled');
				$('#SubAccount_Group1').focus();
				}else if(SubAccount_Group2 == ""){
				alert('please Select Sub Account Group 2');
				$('.saveBtn').removeAttr('disabled');
				$('#SubAccount_Group2').focus();
				}else if(payment_term == ''){
				alert('Please Enter Payment Terms');
				$('#payment_term').focus();
				}else if(tax == ''){
				alert('Please Select GST');
				$('#tax').focus();
				}else{
				if(ifsc == ''){
					var ret = true;
					}else{
					if(accname == ''){
						alert('please  enter Account Name');
						$('#accname').focus();
						}else if(accountno == ''){
						alert('please  enter Account No.');
						$('#accountno').focus();
						}else if(accounttype == ''){
						alert('please  enter Account Type');
						$('#accounttype').focus();
						}else if(bankname == ''){
						alert('please Enter Correct IFSC Code');
						$('#ifsc').focus();
						}else if(bankaddress == ''){
						alert('please Enter Correct IFSC Code');
						$('#ifsc').focus();
						}else{
						var ret = true;
					}
				}
				if(ret == true){
					$.ajax({
						url:"<?php echo admin_url(); ?>Accounts_master/UpdateAccountID",
						dataType:"JSON",
						method:"POST",
						data:{AccountID:AccountID,company:company,BAL1:BAL1,Blockyn:Blockyn,
							StartDate:StartDate,MainAccount_Group:MainAccount_Group,SubAccount_Group1:SubAccount_Group1,SubAccount_Group2:SubAccount_Group2,ifsc:ifsc,bankname:bankname,bankaddress:bankaddress,accname:accname,accountno:accountno,accounttype:accounttype,closing_bal:closing_bal,ad_code:ad_code,payment_term:payment_term,hsn_code:hsn_code,tax:tax
						},
						beforeSend: function () {
							$('.searchh3').css('display','block');
							$('.searchh3').css('color','blue');
						},
						complete: function () {
							$('.searchh3').css('display','none');
						},
						success:function(data){
							if(data == true){
								alert_float('success', 'Record updated successfully...');
								
                    			$('#account_id').val('');
                    			$('#account_name').val('');
                    			$('#opening_bal').val('');
                    			$('#ifsc').val('');
                    			$('#bankname').val('');
                    			$('#bankaddress').val('');
                    			$('#accname').val('');
                    			$('#accountno').val('');
                    			$('#accounttype').val('');
                    			$('#closing_bal').val('');
                    			$('#ad_code').val('');
                    			$('#payment_term').val('');
                    			$('#hsn_code').val('');
								$('.selectpicker').selectpicker('refresh');
                    			$('#tax').val('');
                    			$('.selectpicker').selectpicker('refresh');
                    			var today = new Date();
                    			var dd = String(today.getDate()).padStart(2, '0');
                    			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                    			var yyyy = today.getFullYear();
                    			
                    			today = dd + '/' + mm + '/' + yyyy;
                    			$('#start_date').val(today);
                    			
                    			
                    			$('select[name=MainAccount_Group]').val('');
                    			$('select[name=SubAccount_Group1]').html('');
                    			$('select[name=SubAccount_Group2]').html('');
                    			$('.selectpicker').selectpicker('refresh');
                    			
                    			$('select[name=block_ac]').val('N');
                    			$('.selectpicker').selectpicker('refresh');
                    			
                    			// var staffid = $('#staffid').val();
                    			// if(staffid !== "3"){
								// }
                    				$("#opening_bal").prop("readonly", false);
                    			$('.saveBtn').removeAttr('disabled');           
                    			$('.saveBtn').show();
                    			$('.saveBtn2').show();
                    			$('.updateBtn').hide();
                    			$('.updateBtn2').hide();
								location.reload();
								}else{
								$('.updateBtn').removeAttr('disabled');
								alert_float('warning', 'Data not updated...');
							}
						}
					});
				}
			}
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_AccountHead_List");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			td3 = tr[i].getElementsByTagName("td")[3];
			td4 = tr[i].getElementsByTagName("td")[4];
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
							}else if(td3){
							txtValue = td3.textContent || td3.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
								}else if(td4){
								txtValue = td4.textContent || td4.innerText;
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
		}
	}
</script>
<script>
	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	}
</script>

<script type="text/javascript">
	$('#opening_bal').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
			event.preventDefault();
		}
	});
	$('#closing_bal').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
			event.preventDefault();
		}
	});
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_AccountHead_List tbody");
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
	
	#account_id {
	text-transform: uppercase;
	}
	#table_AccountHead_List td:hover {
	cursor: pointer;
	}
	#table_AccountHead_List tr:hover {
	background-color: #ccc;
	}
	
	.table-AccountHead_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
	.table-AccountHead_List thead th { position: sticky; top: 0; z-index: 1; }
	.table-AccountHead_List tbody th { position: sticky; left: 0; }
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
	color: #fff !important; }
</style>

</body>
</html>