<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
                <div class="row">
                  <div class="col-md-12">
                  <h4 class="no-margin font-bold">Add New User</h4>
                  </div>
                  </div>
                  <?php echo form_open('admin/accounts_master/manage_staff',array('id'=>'other_staff')); ?>
                <div class="row">
                    <?php $value = (isset($account_detail) ? $account_detail->AccountID : ''); ?>
                        <?php
                        $date_attrs = array();
                            if(isset($account_detail)){
                                
                                $date_attrs['disabled'] = true;
                                $opening_bal = get_opening_bal($value);
                                $bal_on_bill = get_bal_on_bill($value);
                                
                            }
                            
                        ?>
                    <div class="col-md-2">
                        <input type="hidden" value="<?php echo $value; ?>" name="edit_account_id">
                        
                        <?php echo render_input('account_id','Account ID',$value,'',$date_attrs); ?>
                    </div>
                    
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->firstname : ''); ?>
                        <?php echo render_input('firstname','First Name',$value); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->lastname : ''); ?>
                        <?php echo render_input('lastname','Last Name',$value); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->SubActGroupID : ''); 
                        
                        ?>
                        <?php echo render_select('Account_Group',$account_subgroup,array('SubActGroupID','SubActGroupName'),'ActGroup Name',$value,$date_attrs); ?>
                    </div>
                    <div class="col-md-2">
                        <?php
                       $date_attrs_for_designation = '';
                            if(isset($account_detail)){
                                if($account_detail->SubActGroupID !== "30000004"){
                                    $date_attrs_for_designation = 'disabled';
                                }
                            }
                       ?> 
                      <div class="form-group">
						<label for="Account_sldtype" class="control-label">Working As</label>
						<select class="form-control " name="Account_sldtype" data-live-search="true" id="Account_sldtype" <?php echo $date_attrs_for_designation; ?>>
                		
                			<?php
                			    foreach ($user_work_on as $key => $value) {
                                    # code...
                                    ?>
                                    <option value="<?php echo $value["SLDTypeID"]; ?>" <?php if($account_designation->SLDTypeID ==$value["SLDTypeID"] ) echo "selected"; ?>><?php echo $value["SLDTypeName"]; ?></option>
                                <?php
                                }
                			?>					    
                		</select>
					 </div>
                    </div>
                    <div class="col-md-2 username">
                        <?php $value = (isset($account_detail) ? $account_detail->username : ''); ?>
                        <?php echo render_input('username','UserName',$value); ?>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->phonenumber : ''); ?>
                        <?php echo render_input('ctrlAccountId','CtrlAccountID',$value,''); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->phonenumber : ''); ?>
                        <?php echo render_input('phonenumber','Mobile No',$value); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->mobile2 : ''); ?>
                        <div class="form-group">
                            <label for="mobile2" class="control-label">Mobile No2</label>
                            <input type="text" maxlength="10" pattern="[6789][0-9]{9}" id="mobile2" name="mobile2" class="form-control" autocomplete="off" value="<?php echo $value; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->email : '');?>
                        <?php echo render_input('email','Email ID',$value,'email'); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->peremail : '');?>
                        <?php echo render_input('peremail','Personal Email',$value,'email'); ?>
                    </div>
                    <div class="col-md-2">
    					<div class="form-group">
    						<label for="sex" class="control-label"><?php echo _l('hr_sex'); ?></label>
    						<select name="sex" class="selectpicker" id="sex" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
    							<option value=""></option>                  
    							<option value="<?php echo 'male'; ?>" <?php if(isset($account_detail) && $account_detail->sex == 'male'){echo 'selected';} ?>><?php echo _l('male'); ?></option>
    							<option value="<?php echo 'female'; ?>" <?php if(isset($account_detail) && $account_detail->sex == 'female'){echo 'selected';} ?>><?php echo _l('female'); ?></option>
    						</select>
    					</div>
					</div>
                </div>
                <div class="row">
                    
                    
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->state : ''); ?>
                        <?php echo render_select('state',$state_list,array('short_name','state_name'),'State',$value); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value=( isset($account_detail) ? $account_detail->city : ''); ?>
                        <?php
                            $city_name = get_city_name_by_state_id($account_detail->state);
                            ?>
                        <div class="form-group">
                            <label for="city" class="control-label"> City</label>
                                                
                            <select class="form-control city" name="city" id="city" >
                                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                <?php foreach($city_name as $cn){ ?>
                            		<option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$client->city){ echo 'selected'; }?>><?php echo $cn["city_name"]; ?></option>
                            	<?php } ?>						    
                            </select>
                                                
                        </div>
                    </div>
                    <div class="col-md-2">
                         <?php $value = (isset($account_detail) ? $account_detail->current_address : ''); ?>
                        <?php echo render_input('current_address','Address',$value); ?>
                    </div>
                    <div class="col-md-2">
                         <?php $value = (isset($account_detail) ? $account_detail->home_town : ''); ?>
                        <?php echo render_input('home_town','Address2',$value); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->pincode : ''); ?>
                        <?php echo render_input('pin','Pin',$value); ?>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->StationName : ''); ?>
                        <?php echo render_input('station_name','Station Name',$value); ?>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-md-2">
                        <?php //echo render_input('opening_bal','Opening Bal.'); ?>
                        <label for="opening_bal" class="control-label">Opening Bal.</label>
                            <input type="text" maxlength="12"  name="opening_bal" pattern="[0-9]" id="opening_bal" class="form-control" value="<?php echo $opening_bal->BAL1; ?>" <?php if(isset($account_detail)) echo "disabled";?>>
                            <span class="opening_bal_denger" style="color:red;"></span>
                    </div>
                    <div class="col-md-2">
                        <?php $value = (isset($account_detail) ? $account_detail->Aadhaarno : ''); ?>
                        <label for="aadhaar" class="control-label">Aadhar number</label>
                            <input type="text" maxlength="12"  name="aadhaar" pattern="[0-9] {10}" id="aadhaar" class="form-control" value="<?php echo $value?>">
                            <span class="aadhar_denger" style="color:red;"></span>
                    </div>
                    <div class="col-md-2">
                       
                        <?php $value = (isset($account_detail) ? $account_detail->Pan : ''); ?>
                        <label for="pan_number" class="control-label">PAN number</label>
                            <input type="text" maxlength="10"  name="pan_number" pattern="[0-9 A-Z] {10}" id="pan_number" class="form-control" value="<?php echo $value?>">
                            <span class="pan_denger" style="color:red;"></span>
                    </div>
                    <div class="col-md-2">
						<?php
							$account_number = (isset($account_detail) ? $account_detail->account_number : '');?>
							<label for="account_number" class="control-label">Account Number</label>
							<input type="tel" minlenght="9" maxlength="18"  name="account_number" pattern="[0-9] {10}" id="account_number" class="form-control" value="<?php echo $account_number?>">
							
					</div>
					<div class="col-md-2">
						<?php $name_account = (isset($account_detail) ? $account_detail->name_account : '');
							echo render_input('name_account','Bank Account Holder Name',$name_account, 'text'); ?>
					</div>
					<div class="col-md-2">
						<?php
							$issue_bank = (isset($account_detail) ? $account_detail->issue_bank : '');
							echo render_input('issue_bank','Bank Name',$issue_bank, 'text'); ?>
					</div>
                    
                    
                    
                </div>
                <div class="row">
                    <div class="col-md-2">
    					<?php				
						    $selected = '1';
							$comp_assigned = unserialize($member->staff_comp);
						?>
                        <div class="form-group">
                    		<label for="workplace" class="control-label">Select Company</label>
                    		<select name="company_id1[]" class="selectpicker" id="company_id1" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true"> 
                    		    <option value=""></option>
                    				<?php foreach ($rootcompany as $key => $value) {
                                         ?>
                                        <option value="<?php echo $value["id"]?>" <?php if(in_array($value["id"], $comp_assigned)) { echo "selected"; }?>><?php echo $value["company_name"]?></option>
                                    <?php         
                                        }
                                    ?>
				    	    </select>
						</div>
        			</div>
                    <div class="col-md-2">
						<div class="form-group">
                            <label for="active" class="control-label"><?php echo _l('hr_status_work'); ?></label>
                            <select name="active" class="selectpicker" id="active" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                <option value="1" <?php if(isset($account_detail) && $account_detail->active == '1'){echo 'selected';} ?>>Active</option>
                                <option value="0" <?php if(isset($account_detail) && $account_detail->active == '0'){echo 'selected';} ?>>De-Active</option>
                                                
                            </select>
                        </div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<label for="login_access" class="control-label">ERP Access</label>
							<select name="login_access" class="selectpicker" id="login_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													
								<option value="No" <?php if(isset($account_detail) && $account_detail->login_access == 'No'){echo 'selected';} ?>>No</option>
								<option value="Yes" <?php if(isset($account_detail) && $account_detail->login_access == 'Yes'){echo 'selected';} ?>>Yes</option>
							</select>
						</div>
					</div>
										
					<div class="col-md-2">
						<div class="form-group">
							<label for="app_access" class="control-label">SO App Access</label>
							<select name="app_access" class="selectpicker" id="app_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													
								<option value="No" <?php if(isset($account_detail) && $account_detail->app_access == 'No'){echo 'selected';} ?>>No</option>
								<option value="Yes" <?php if(isset($account_detail) && $account_detail->app_access == 'Yes'){echo 'selected';} ?>>Yes</option>
							</select>
						</div>
					</div>
										
					<div class="col-md-2">
						<?php 
							$date = (isset($account_detail) ? _d(substr($account_detail->datecreated,0,10)) : '');
							echo render_date_input('datecreated','Start Date',$date,'date'); ?>
					</div>
					<div class="col-md-2">
					    <div id="password_field">
						    <label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
								<div class="input-group" >
									<input type="password" class="form-control password" name="password"  autocomplete="off">
									<span class="input-group-addon">
										<a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
									</span>
									<span class="input-group-addon">
										<a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
									</span>
								</div>
							<?php if(isset($member)){ ?>
							    <p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
									<?php if($member->last_password_change != NULL){ ?>
										<?php echo _l('staff_add_edit_password_last_changed'); ?>:
										<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_password_change); ?>">
											<?php echo time_ago($member->last_password_change); ?>
										</span>
									<?php } } ?>
					</div>
					</div>
                </div>
                <div class="row">
                    <div class="col-md-7" >
                        <?php if(count($departments) > 0){ ?>
                                 <label for="departments"><?php echo _l('staff_add_edit_departments'); ?></label>
                                 <br>
                              <?php } ?>

                              <?php foreach($departments as $department){ ?>
                                 <div class="checkbox checkbox-primary col-md-4">
                                    <?php
                                          $checked = '';
                                          if(isset($member)){
                                            foreach ($staff_departments as $staff_department) {
                                             if($staff_department['departmentid'] == $department['departmentid']){
                                              $checked = ' checked';
                                           }
                                        }
                                     }
                                     ?>
                                     <input type="checkbox" id="dep_<?php echo html_entity_decode($department['departmentid']); ?>" name="departments[]" value="<?php echo html_entity_decode($department['departmentid']); ?>"<?php echo html_entity_decode($checked); ?>>
                                     <label for="dep_<?php echo html_entity_decode($department['departmentid']); ?>"><?php echo html_entity_decode($department['name']); ?></label>
                                 </div>
                              <?php } ?>
                    </div>
                    
                </div>
                <!--<div class="row">
                    
                    <div class="col-md-3">
                        <?php $value = (isset($account_detail) ? $account_detail->kms : ''); ?>
                        <?php echo render_input('km','KM',$value); ?>
                    </div>
                    
                    <div class="col-md-3">
                        
                        <?php $value1 = (isset($account_detail) ? $bal_on_bill->BalancesYN : '');
                       
                        ?>
                        <div class="form-group">
						<label for="bal_on_bill" class="control-label">Balance on Bill</label>
						<select class="form-control " name="bal_on_bill" data-live-search="true" id="bal_on_bill">
						    
						    <option value="Y" <?php if($value1 == "Y") echo "selected";?>>Yes</option>
						    <option value="N" <?php if($value1 == "N") echo "selected";?>>No</option>
						</select>
						</div>
                    </div>
                    
                </div>-->
                <!--<div class="row">
                    
                    <div class="col-md-3">
                        <?php $value = (isset($account_detail) ? $account_detail->Blockyn : ''); ?>
                        <?php //echo render_input('block_ac','Block A/C'); ?>
                        <div class="form-group">
						<label for="block_ac" class="control-label">Block A/C</label>
						<select class="form-control " name="block_ac" data-live-search="true" id="block_ac">
						    <option value="N" <?php if($value == "N") echo "selected"; ?>>No</option>
						    <option value="Y" <?php if($value == "Y") echo "selected"; ?>>Yes</option>
						</select>
						</div>
                    </div>
                   
                </div>-->
                
                <!--<div class="row">
                 
                    <div class="col-md-3">
                        <?php $value = 24;
                                $date_attrs = array();
                                $date_attrs['disabled'] = true;
                  
                        ?>
                        <?php echo render_select('distributor_type',$distributor_type,array('id','name'),'Distributor Type',$value,$date_attrs); ?>
                      
                    </div>
                </div>-->
                <div class="row">
                    
                    <div class="col-md-1" style="margin-top: 10px;">
                       <br>
                    <?php
                    if (has_permission_new('other_staff_master', '', 'create')) {
                        ?>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    <?php } ?>
                    </div>
                    <div class="col-md-2" style="margin-top: 10px;">
                       <br>
                       <?php $redUrl = admin_url('accounts_master/staff_master'); 
                       if (has_permission_new('other_staff_master', '', 'view')) { ?>
                        <a href="<?php echo $redUrl;?>" class="btn btn-info">staff list</a>
                        <?php } ?> 
                    </div>
                </div>
                
                  <?php echo form_close(); ?>
                
            
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>

$(function(){
        'use strict';
        appValidateForm($('#other_staff'), {
            
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
            firstname: 'required',
            lastname: 'required',
            Account_Group: 'required',
            state: 'required',
            city: 'required',
            phonenumber: {
				required: true,
				remote: {
					url: site_url + "admin/misc/staff_mobile_exists",
					type: 'post',
					data: {
						phonenumber: function() {
							return $('input[name="phonenumber"]').val();
						},
						memberid: function() {
							return $('input[name="edit_account_id"]').val();
						},
					}
				}
			},
		
        });
        
    $('#state').on('change', function() {
                var id = $(this).val();
                //alert(roleid);
                var url = "<?php echo base_url(); ?>admin/hr_profile/select_city";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {id: id},
                        dataType:'json',
                        success: function(data) {
                           
                            $(".city").html(data);
                            //alert(data);
                            
                        }
                    });
            });
    $('#Account_Group').on('change', function() {
                var id = $(this).val();
                //alert(roleid);
                if(id == "30000004"){
                    $( "#Account_sldtype" ).prop( "disabled", false );
                }else{
                    $( "#Account_sldtype" ).prop( "disabled", true );
                }
                if(id == "30000006" || id == "30000004"){
                    $(".username").css("display", "");
                }else{
                    $(".username").css("display", "none");
                }
            });
    
    $('#mobile_no').keyup(function(e) {
            e.preventDefault();
            if(!$('#mobile_no').val().match('[0-9]{10}'))  {
                
                $(".mob_denger").text("Enter valid 10 digit mobile number");
            }else{
                $(".mob_denger").text(" ");
            }
            

        });
        
        $('#pan_number').keyup(function(e) {
            e.preventDefault();
            if(!$('#pan_number').val().match('[0-9 A-Z]{10}'))  {
                
                $(".pan_denger").text("Enter valid PAN number");
            }else{
                $(".pan_denger").text(" ");
            }
            

        });
        
        $('#aadhaar').keyup(function(e) {
            e.preventDefault();
            if(!$('#aadhaar').val().match('[0-9]{12}'))  {
                
                $(".aadhar_denger").text("Enter valid 12 digit Aadhar number");
            }else{
                $(".aadhar_denger").text(" ");
            }
            

        });
        
        
    });
    $("#password_field").css("display", "none");
    $('#login_access').on('change', function() {
				var erp_access = $(this).val();
				var app_access = $("#app_access").val();
				if(erp_access == "Yes" || app_access == "Yes"){
				    $("#password_field").css("display", "");
				}else {
				    $("#password_field").css("display", "none");
				}
				
				
			});
			
			$('#app_access').on('change', function() {
				var app_access = $(this).val();
				var erp_access = $("#login_access").val();
				if(erp_access == "Yes" || app_access == "Yes"){
				    $("#password_field").css("display", "");
				}else {
				    $//("").css("display", "inline-table");
				    $("#password_field").css("display", "none");
				}
				
				
			});
</script>
</body>
</html>