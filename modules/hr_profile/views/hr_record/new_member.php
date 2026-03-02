<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>-->
<style>

.picture {
    width: 100px;
    height: 100px;
    background-color: #999999;
    border: 4px solid #CCCCCC;
    color: #FFFFFF;
    border-radius: 50%;
    margin: 0px auto;
    overflow: hidden;
    transition: all 0.2s;
    -webkit-transition: all 0.2s;
    position: relative;
    left: 1px;
    margin: 0px;
}
.hidden {
        display: none;
      }
</style>

<div id="wrapper" >
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
					    <?php 
                  $title = '';
                  $staffid = '';
                  if(isset($member)){
                    $title .= _l('hr_update_staff_profile');
                    $staffid    = $member->staffid;

                    echo form_hidden('memberid',$staffid);
                    echo form_hidden('isedit');

                  }else{
                    $title .= _l('add_staff_profile');

                  }
                 ?>
						<?php 
						if(isset($member)){
						    echo form_open_multipart(admin_url('hr_profile/add_edit_member/'.$member->staffid), array('id' => 'add_edit_member','autocomplete'=> "off"));
						}else{
						    echo form_open_multipart(admin_url('hr_profile/add_edit_member'), array('id' => 'add_edit_member','autocomplete'=> "off"));
						}
						 ?>
						<div class="modal-body">
							<!--<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active">
									<a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
										<?php echo _l('staff_profile_string'); ?>
									</a>
								</li>
								<li role="presentation">
									<a href="#tab_staff_contact" aria-controls="tab_staff_contact" role="tab" data-toggle="tab">
										<?php echo _l('hr_staff_profile_related_info'); ?>
									</a>
								</li>
							</ul>-->

							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="tab_staff_profile">
									<div class="row">
										

										<div class="col-md-2">
										    
											<?php  $hr_codes = (isset($member) ? $member->staff_identifi : $staff_code); ?>
											<div class="form-group" app-field-wrapper="staff_identifi">
												<label for="staff_identifi" class="control-label"><?php echo _l('hr_staff_code'); ?></label>
												<input type="text" id="staff_identifi1" name="staff_identifi1" class="form-control" value="<?php echo html_entity_decode($hr_codes) ?>" aria-invalid="false" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ; }  ?> disabled>
											    <input type="hidden" id="staff_identifi" name="staff_identifi" value="<?php echo html_entity_decode($hr_codes) ?>">
											</div>
										</div>
										
									    <div class="col-md-2">
											<?php $value = (isset($member) ? $member->AccountID : ''); ?>
											<div class="form-group" app-field-wrapper="AccountID">
												<label for="AccountID" class="control-label">AccountID</label>
												<input type="text"  id="AccountID" name="AccountID" class="form-control" autocomplete="off" value="<?php echo $value; ?>" <?php if(isset($member)){ echo 'disabled' ;}  ?>>
											<?php
											    if(isset($member)){
											        ?>
											        <input type="hidden" name="AccountID" id="AccountID" value="<?php echo $value; ?>">
											        <?php
											    }
											?>
											</div>   
										</div>
										<!--<div class="col-md-2">
											<?php $value = (isset($member) ? $member->ctrlAccountId : ''); ?>
												<label for="ctrlAccountId" class="control-label">Ctrl AccountID</label>
												<input type="text"  id="ctrlAccountId" name="ctrlAccountId" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
											    
										</div>-->
										
										
										<div class="col-md-2">
										    <?php $value = (isset($member) ? $member->firstname : ''); ?>
										    <?php $attrs = (isset($member) ? array() : array('autofocus'=>true)); ?>
											<?php echo render_input('firstname','hr_firstname',$value,'text',$attrs); ?>
										</div>
										<div class="col-md-2">
										    <?php $value = (isset($member) ? $member->lastname : ''); ?>
											<?php echo render_input('lastname','hr_lastname',$value,'text',$attrs); ?>
										</div>
										
										<!--<div class="col-md-2">
										    <?php
                                                hooks()->do_action('staff_render_permissions');
                                                $selected = '';
                                                foreach($roles_value as $role){
                                                 if(isset($member)){
                                                  if($member->role == $role['roleid']){
                                                   $selected = $role['roleid'];
                                                 }
                                                } else {
                                                $default_staff_role = get_option('default_staff_role');
                                                if($default_staff_role == $role['roleid'] ){
                                                 $selected = $role['roleid'];
                                                }
                                                }
                                                }
                                        ?>
										<?php echo render_select('role',$roles_value,array('roleid','name'),'Role',$selected); ?>
										</div>-->
										<?php
										if($member->admin == "1" && isset($member)){}else{
										    
										  ?>
										 <div class="col-md-2">
								    <?php 
                                        $selected =( isset($member) ? $member->SubActGroupID : '');
                                        echo render_select( 'SubActGroupID',$SubGroup,array( 'SubActGroupID',array( 'SubActGroupName')), 'Account Group',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                                    ?>
											<!--<div class="form-group" app-field-wrapper="SubActGroupID">
												<label for="SubActGroupID" class="control-label">AccountGroup</label>
												<select name="SubActGroupID" class="selectpicker" id="SubActGroupID" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value=""></option>
													<option value="1002503" <?php if(isset($member) && $member->SubActGroupID == '1002503'){echo 'selected';} ?>>DRIVER & LOADER STAFF</option>
													<option value="1002504" <?php if(isset($member) && $member->SubActGroupID == '1002504'){echo 'selected';} ?>>MAINTENANCE STAFF</option>
													<option value="1002506" <?php if(isset($member) && $member->SubActGroupID == '1002506'){echo 'selected';} ?>>MESS STAFF</option>
													<option value="30000006" <?php if(isset($member) && $member->SubActGroupID == '30000006'){echo 'selected';} ?>>OFFICE STAFF</option>
													<option value="30000004" <?php if(isset($member) && $member->SubActGroupID == '30000004'){echo 'selected';} ?>>OTHER STAFF</option>
													<option value="10022005" <?php if(isset($member) && $member->SubActGroupID == '10022005'){echo 'selected';} ?>>PACKING STAFF</option>
													<option value="10022004" <?php if(isset($member) && $member->SubActGroupID == '10022004'){echo 'selected';} ?>>PRODUCTION STAFF</option>
													<option value="30001002" <?php if(isset($member) && $member->SubActGroupID == '30001002'){echo 'selected';} ?>>SALES STAFF</option>
													<option value="30000007" <?php if(isset($member) && $member->SubActGroupID == '30000007'){echo 'selected';} ?>>SECURITY STAFF</option>
													
												</select>
											</div>-->
										</div>
										<?php
										}
										?>
										<div class="col-md-2">
										    <?php $value = (isset($member) ? $member->SLDTypeID : ''); ?>
										   <?php echo render_select('sldtype',$SLDTYPE,array('SLDTypeID','SLDTypeName'),'SLDType',$value); ?> 
										 </div>
										<!--<div class="col-md-2">
                                            <?php $attrs2 = (isset($member) ? array('disabled'=>true) : array()); ?>
									        	<?php $value = (isset($member) ? $member->username : ''); ?>
											<?php echo render_input('username','UserID',$value,'text'); ?>
										</div>-->
										
									</div>

									<div class="row">
										<div class="col-md-2">
											<?php $value = (isset($member) ? $member->email : ''); ?>
											<?php echo render_input('email','Email',$value,'text'); ?>
											<!--<div class="form-group" app-field-wrapper="ABC">
												<label for="ABC" class="control-label">OfficeEmail</label>
												<input type="text" id="ABC" name="ABC" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" >
											</div>-->
										</div>
										<div class="col-md-2">
											<?php $value = (isset($member) ? $member->peremail : ''); ?>
											<div class="form-group" app-field-wrapper="peremail">
												<label for="peremail" class="control-label">Personal Email</label>
												<input type="text" id="peremail" name="peremail" class="form-control" value="<?php echo html_entity_decode($value) ?>" >
										    </div>
										</div>
										<div class="col-md-2">
											<?php $value = (isset($member) ? $member->phonenumber : ''); ?>
											<div class="form-group" app-field-wrapper="peremail">
												<label for="phonenumber" class="control-label">Mobile No.</label>
												<input type="tel" maxlength="10" pattern="[6789][0-9]{9}" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" >
											    <span class="mob_denger" style="color:red;"></span>
											</div>
										</div>
										
										<div class="col-md-2">
											<?php $value = (isset($member) ? $member->mobile2 : ''); ?>
												<label for="mobile2" class="control-label">Mobile No2.</label>
												<input type="tel" maxlength="10" pattern="[6789][0-9]{9}" id="mobile2" name="mobile2" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
											    
										</div>
										<div class="col-md-2">
											<?php 
											$birthday = (isset($member) ? $member->birthday : ''); 
											echo render_date_input('birthday','hr_hr_birthday',_d($birthday)); ?>
										</div>
										<div class="col-md-2">
											<?php $value = (isset($member) ? $member->BAL1 : ''); ?>
											<?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>
												<label for="openingbal" class="control-label">Opening Bal</label>
												<input type="text"  id="openingbal" name="openingbal" class="form-control" autocomplete="off" <?php if(isset($member) && $staff_user_id !== "3"){ echo "disabled";}?> value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin()){ echo 'disabled' ;}  ?>>
											    
										</div>
									</div>


                                    <!-- 2nd row started -->
                                    <div class="row">
                                        
                                        <div class="col-md-2">
											<div class="form-group">
												<label for="state" class="control-label"><?php echo _l('hr_state'); ?></label>
												<select name="state" class="selectpicker" id="state" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value=""></option>                  
													<?php foreach($state as $s){ ?>
														<option value="<?php echo html_entity_decode($s['short_name']); ?>" <?php if(isset($member) && $member->state == $s['short_name']){echo 'selected';} ?>><?php echo html_entity_decode($s['state_name']); ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										
										<div class="col-md-2">
										    <?php
										    $city_name = get_city_name_by_state_id($member->state);
										    ?>
                							<div class="form-group">
                								<label for="city" class="control-label"><span style="color:#fc2d42;font-size:11px;">*</span> City</label>
                								
                								<select class="form-control city " name="city" id="city" required data-live-search="true">
                								    <option value="">Select City</option>
                								    <?php foreach($city_name as $cn){ ?>
                								    <option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$member->city){ echo 'selected'; }?>><?php echo $cn["city_name"]; ?></option>
                								    	<?php } ?>
                								</select>
                								
                							</div>
						                </div>
						                <div class="col-md-2">
												<?php 
												$pincode = (isset($member) ? $member->pincode : '');
												echo render_input('pincode','PIN',$pincode,'text'); ?>
											</div>
						                <div class="col-md-3">
												<?php 
												$current_address = (isset($member) ? $member->current_address : '');
												//echo render_textarea('current_address','hr_current_address',$current_address,'text'); 
											    echo render_input('current_address','hr_current_address',$current_address,'text'); ?>
											</div>

											<div class="col-md-3">
												<?php 
												$hometown_address = (isset($member) ? $member->home_town : '');
											    echo render_input('home_town','Address2',$hometown_address,'text'); ?>
											</div>
											
						                
                                    </div>
                                    
                                    <!-- 2nd row end-->
                                    
                                    <!-- 3rd row start-->
                                    <div class="row">
                                        <div class="col-md-2">
											<?php $value = (isset($member) ? $member->stationName : ''); ?>
												<label for="stationName" class="control-label">StationName</label>
												<input type="text"  id="stationName" name="stationName" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>> 
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label for="job_position" class="control-label"><?php echo _l('hr_hr_job_position'); ?></label>
												<select name="job_position" class="selectpicker" id="job_position" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value=""></option> 
													<?php foreach($positions as $p){ ?> 
														<option value="<?php echo html_entity_decode($p['position_id']); ?>" <?php if(isset($member) && $member->job_position == $p['position_id']){echo 'selected';} ?>><?php echo html_entity_decode($p['position_name']); ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="col-md-2">
										    <?php
										    $hq_name = get_hedquarter_name_by_state_id($member->state);
										    ?>
										    <div class="form-group" app-field-wrapper="headqurter">
                								<label for="headqurter" class="control-label">Head Quarter</label>
                								<select class="form-control selectpicker" name="headqurter" id="headqurter" data-live-search="true">
                								    <option value="">Select Headquarter</option>
                								    <?php foreach($hq_name as $cn){ ?>
                								    <option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$member->headqurter){ echo 'selected'; }?>><?php echo $cn["name"]; ?></option>
                								    	<?php } ?>
                								</select>
                								
                							</div>
                							<!--<div class="form-group">
                								<label for="headqurter" class="control-label"><span style="color:#fc2d42;font-size:11px;">*</span> Head Quarter</label>
                								
                								<select class="form-control headqurter" name="headqurter" id="headqurter" required data-live-search="true">
                								    <option value="">Select City</option>
                								</select>
                							</div>-->
                							<?php
                							/*if(is_admin() || get_option('staff_members_create_inline_customer_groups') == '1'){
                                              echo render_select_with_input_group('headqurter','','','Head Quarter',$selected,'<a href="#" class="add_quarter" data-toggle="modal" data-target="#head_quarter_modal"><i class="fa fa-plus"></i></a>',array('data-actions-box'=>false),array(),'','',false);
                                              } else {
                                                echo render_select('headqurter','','','Head Quarter',$selected,array('multiple'=>false,'data-actions-box'=>false),array(),'','',false);
                                              }*/
                							 //echo render_select('headqurter','','','Head Quarter',$selected,array('multiple'=>false,'data-actions-box'=>false),array(),'','',false);
                							?>
						                </div>
						                <div class="col-md-3">
    										<?php
        								//$s_attrs = array('data-none-selected-text'=>'Select Company', 'multiple'=>'true');
                                            //$selected = '';
                                            //echo render_select('company_id1[]',$rootcompany,array('id','company_name'),'Select Company',$selected,$s_attrs);--> ?>
                                            <?php
											
									$selected = '1';
			
										$comp_assigned = unserialize($member->staff_comp);
								?>
                                <div class="form-group" app-field-wrapper="company_id1">
                    				<label for="company_id1" class="control-label">Select Company</label>
                    				<select name="company_id1[]" class="selectpicker" id="company_id1" data-width="100%"  multiple="true"> 
                    					
                    					<?php foreach ($rootcompany as $key => $value) {
                                             # code...
                                             ?>
                                            <option value="<?php echo $value["id"]?>" <?php if(in_array($value["id"], $comp_assigned)) { echo "selected"; }?>><?php echo $value["company_name"]?></option>
                                        <?php         
                                             
                                         }
                                         ?>
				
									</select>
								</div>
        								</div>
        								
        								<div class="col-md-3">
											<!--teamanage -->
											<?php if(has_permission('hrm_hr_records','', 'edit') || has_permission('hrm_hr_records','', 'create')){ ?>
												<?php $value = (isset($member) ? $member->team_manage : ''); ?>
												<?php //echo render_select('team_manage',$list_staff,array('staffid','full_name'),'Report To',$value); ?>
												<div class="form-group">
    												<label for="team_manage" class="control-label">Report To</label>
    												<select name="team_manage" class="selectpicker" id="team_manage" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
    													<option value=""></option> 
    													<?php
    													foreach ($list_staff as $key => $value) {
    													    ?>
    													        <option value="<?php echo $value['staffid']; ?>" <?php if(isset($member) && $member->team_manage == $value['staffid']){echo 'selected';} ?>><?php echo $value['firstname']. " ".$value['lastname']; ?></option>
    													
    													    <?php
    													} ?> 
    													
    												</select>
    											</div>
											<?php } ?>
											<!--teamanage -->
										</div>
						                
                                    </div>
                                    <!-- 3rd row end -->
                                    
                                    <!-- 4th row start-->
									<div class="row">
									        <div class="col-md-2">
    											<div class="form-group">
    												<label for="sex" class="control-label"><?php echo _l('hr_sex'); ?></label>
    												<select name="sex" class="selectpicker" id="sex" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
    													<option value=""></option>                  
    													<option value="<?php echo 'male'; ?>" <?php if(isset($member) && $member->sex == 'male'){echo 'selected';} ?>><?php echo _l('male'); ?></option>
    													<option value="<?php echo 'female'; ?>" <?php if(isset($member) && $member->sex == 'female'){echo 'selected';} ?>><?php echo _l('female'); ?></option>
    												</select>
    											</div>
										       </div>
									        <div class="col-md-2">
												<?php
												$pan_number = (isset($member) ? $member->pan_number : '');
												//echo render_input('pan_number','PAN number',$pan_number, 'text'); ?>
												<label for="pan_number" class="control-label">PAN number</label>
												<input type="text" maxlength="10" minlength="10"  name="pan_number" pattern="[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}" id="pan_number" class="form-control" value="<?php echo $pan_number?>">
												<span class="pan_denger" style="color:red;"></span>
											</div>
											
											<div class="col-md-2">
												<?php
												$aadhar_number = (isset($member) ? $member->aadhar_number : '');
												//echo render_input('aadhar_number','Aadhar number',$aadhar_number, 'text'); ?>
												<label for="aadhar_number" class="control-label">Aadhar number</label>
												<input type="tel" maxlength="12"  name="aadhar_number" pattern="[0-9] {10}" id="aadhar_number" class="form-control" value="<?php echo $aadhar_number?>">
												<span class="aadhar_denger" style="color:red;"></span>
											</div>
											
											<div class="col-md-2">
												<?php
												$account_number = (isset($member) ? $member->account_number : '');
												//echo render_input('account_number','hr_bank_account_number',$account_number, 'text'); ?>
												<label for="account_number" class="control-label">Account number</label>
												<input type="tel" minlenght="9" maxlength="18"  name="account_number" pattern="[0-9] {10}" id="account_number" class="form-control" value="<?php echo $account_number?>">
												<span class="actnumber_denger" style="color:red;"></span>
											</div>
											<div class="col-md-2">
												<?php
												$name_account = (isset($member) ? $member->name_account : '');
												echo render_input('name_account','Bank account holder',$name_account, 'text'); ?>
											</div>
											<div class="col-md-2">
												<?php
												$issue_bank = (isset($member) ? $member->issue_bank : '');
												echo render_input('issue_bank','hr_bank_name',$issue_bank, 'text'); ?>
											</div>
									</div>
									<!-- 4th row end-->

									
									<div class="row">
									    
									    
										<!--<div class="col-md-2">
											<?php $value = (isset($member) ? $member->max_credit : ''); ?>
												<label for="max_credit" class="control-label">MaxCredit</label>
												<input type="text"  id="max_credit" name="max_credit" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
											    
										</div>

											<div class="col-md-2">
											<div class="form-group">
                                              <label for="blockact" class="control-label">Block A/c</label>
                                              <select name="blockact" class="selectpicker" id="blockact" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                                <option value="N" <?php if(isset($member) && $member->blockact == 'N'){echo 'selected';} ?>>NO</option>
                                                <option value="Y" <?php if(isset($member) && $member->blockact == 'Y'){echo 'selected';} ?>>YES</option>
                                                
                                             </select>
                                          </div>
										</div>

										<div class="col-md-2">
											<?php $value = (isset($member) ? $member->sec_dep : ''); ?>
												<label for="sec_dep" class="control-label">Security Deposit</label>
												<input type="text"  id="sec_dep" name="sec_dep" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
											    
										</div>

										<div class="col-md-2">
											<?php $value = (isset($member) ? $member->maxday : ''); ?>
												<label for="maxday" class="control-label">Max Day</label>
												<input type="text"  id="maxday" name="maxday" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
											    
										</div>

										<div class="col-md-2">
											<div class="form-group">
                                              <label for="bal_on_bill" class="control-label">Bal on Bill</label>
                                              <select name="bal_on_bill" class="selectpicker" id="bal_on_bill" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                                
                                                <option value="Y" <?php if(isset($member) && $member->bal_on_bill == 'Y'){echo 'selected';} ?>>YES</option>
                                                <option value="N" <?php if(isset($member) && $member->bal_on_bill == 'N'){echo 'selected';} ?>>NO</option>
                                                
                                             </select>
                                          </div>
										</div>-->

									</div>

									<div class="row">
									    
									    <!--<div class="col-md-2">
											<div class="form-group">
                                              <label for="supp_on_adv" class="control-label">Supply on ADV</label>
                                              <select name="supp_on_adv" class="selectpicker" id="supp_on_adv" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                                <option value="N" <?php if(isset($member) && $member->supp_on_adv == 'N'){echo 'selected';} ?>>NO</option>
                                                <option value="Y" <?php if(isset($member) && $member->supp_on_adv == 'Y'){echo 'selected';} ?>>YES</option>
                                                
                                                
                                             </select>
                                          </div>
										</div>-->
										
										<div class="col-md-2">
											<?php $literacy = (isset($member) ? $member->literacy : ''); ?> 
											<div class="form-group">
												<label for="literacy" class="control-label"><?php echo _l('hr_hr_literacy'); ?></label>
												<select name="literacy" id="literacy" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('hr_not_required'); ?>">
													<option value=""></option>
													<option value="primary_level" <?php if($literacy == 'primary_level'){ echo 'selected'; } ?> ><?php echo _l('hr_primary_level'); ?></option>
													<option value="intermediate_level" <?php if($literacy == 'intermediate_level'){ echo 'selected'; } ?> ><?php echo _l('hr_intermediate_level'); ?></option>
													<option value="college_level" <?php if($literacy == 'college_level'){ echo 'selected'; } ?> ><?php echo _l('hr_college_level'); ?></option>
													<option value="masters" <?php if($literacy == 'masters'){ echo 'selected'; } ?> ><?php echo _l('hr_masters'); ?></option>
													<option value="doctor" <?php if($literacy == 'doctor'){ echo 'selected'; } ?> ><?php echo _l('hr_Doctor'); ?></option>
													<option value="bachelor" <?php if($literacy == 'bachelor'){ echo 'selected'; } ?> ><?php echo _l('hr_bachelor'); ?></option>
													<option value="engineer" <?php if($literacy == 'engineer'){ echo 'selected'; } ?> ><?php echo _l('hr_Engineer'); ?></option>
													<option value="university" <?php if($literacy == 'university'){ echo 'selected'; } ?> ><?php echo _l('hr_university'); ?></option>
													<option value="intermediate_vocational" <?php if($literacy == 'intermediate_vocational'){ echo 'selected'; } ?> ><?php echo _l('hr_intermediate_vocational'); ?></option>
													<option value="college_vocational" <?php if($literacy == 'college_vocational'){ echo 'selected'; } ?> ><?php echo _l('hr_college_vocational'); ?></option>
													<option value="in-service" <?php if($literacy == 'in-service'){ echo 'selected'; } ?> ><?php echo _l('hr_in-service'); ?></option>
													<option value="high_school" <?php if($literacy == 'high_school'){ echo 'selected'; } ?> ><?php echo _l('hr_high_school'); ?></option>
													<option value="intermediate_level_pro" <?php if($literacy == 'intermediate_level_pro'){ echo 'selected'; } ?> ><?php echo _l('hr_intermediate_level_pro'); ?></option>
												</select>
											</div>
										</div>
										
										<!--<div class="col-md-2">
											<?php $no_show = (isset($member) ? $member->no_show : ''); ?> 
											<div class="form-group">
												<label for="no_show" class="control-label">No show</label>
												<select name="no_show" id="no_show" class="selectpicker" data-width="100%" >
													
													<option value="N" <?php if($no_show == 'N'){ echo 'selected'; } ?> >No</option>
													<option value="Y" <?php if($no_show == 'Y'){ echo 'selected'; } ?> >Yes</option>
													</select>
											</div>
										</div>-->
										

										<!--<div class="col-md-2">
										    <?php 
											$km = (isset($member) ? $member->km : '');
											echo render_input('km','KM',$pincode,'text'); ?>
										</div>-->
										<div class="col-md-2">
												<div class="form-group">
													<label for="marital_status" class="control-label"><?php echo _l('hr_hr_marital_status'); ?></label>
													<select name="marital_status" class="selectpicker" id="marital_status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
														<option value=""></option>                  
														<option value="<?php echo 'single'; ?>" <?php if(isset($member) && $member->marital_status == 'single'){echo 'selected';} ?>><!--<?php echo _l('single'); ?>-->single</option>
														<option value="<?php echo 'married'; ?>" <?php if(isset($member) && $member->marital_status == 'married'){echo 'selected';} ?>><?php echo _l('married'); ?></option>
													</select>
												</div>
										</div>
										
										<div class="col-md-2">
											<div class="form-group">
                                              <label for="active" class="control-label"><?php echo _l('hr_status_work'); ?></label>
                                              <select name="active" class="selectpicker" id="active" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                                <option value="1" <?php if(isset($member) && $member->active == '1'){echo 'selected';} ?>>Active</option>
                                                <option value="0" <?php if(isset($member) && $member->active == '0'){echo 'selected';} ?>>De-Active</option>
                                                
                                             </select>
                                          </div>
										</div>

										<!--<div class="col-md-2">
											<div class="form-group">
												<label for="login_access" class="control-label">ERP Access</label>
												<select name="login_access" class="selectpicker" id="login_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													
													<option value="No" <?php if(isset($member) && $member->login_access == 'No'){echo 'selected';} ?>>No</option>
													<option value="Yes" <?php if(isset($member) && $member->login_access == 'Yes'){echo 'selected';} ?>>Yes</option>
													</select>
											</div>
										</div>-->
										
										<div class="col-md-2">
											<div class="form-group">
												<label for="app_access" class="control-label">SO App Access</label>
												<select name="app_access" class="selectpicker" id="app_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													
													<option value="No" <?php if(isset($member) && $member->app_access == 'No'){echo 'selected';} ?>>No</option>
													<option value="Yes" <?php if(isset($member) && $member->app_access == 'Yes'){echo 'selected';} ?>>Yes</option>
													</select>
											</div>
										</div>
										
										<div class="col-md-2">
										    <?php 
											$date = (isset($member) ? _d(substr($member->StartDate,0,10)) : '');
											echo render_date_input('datecreated','Started Date',$date,'date'); ?>
										</div>
										
										<div class="col-md-2">
										    <?php 
											$IMEI = (isset($member) ? $member->DiveceID : '');
											echo render_input('DiveceID','DeviceID',$IMEI); ?>
										</div>
										
										
									</div>

									<div class="row">
									    <div class="col-md-2">
											<div class="form-group">
												<label for="Movement" class="control-label">Movement</label>
												<select name="Movement" class="selectpicker" id="Movement" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value="No" <?php if(isset($member) && $member->Movement == 'No'){echo 'selected';} ?>>No</option>
													<option value="Yes" <?php if(isset($member) && $member->Movement == 'Yes'){echo 'selected';} ?>>Yes</option>
												</select>
											</div>
										</div>
                                        <div class="col-md-3">
											<div class="form-group">
												<label for="OfficeID" class="control-label">Office Location</label>
												<select name="OfficeID" class="selectpicker" id="OfficeID" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
    												    <option value=""></option>
    												<?php foreach ($OfficeLocation as $key => $value) { ?>
        												<option value="<?php echo $value['AccountID']; ?>" <?php if(isset($member) && $member->OfficeID == $value['AccountID']){echo 'selected';} ?>><?php echo $value['OfficeName']; ?></option>
        											<?php	} ?> 
												</select>
											</div>
										</div>
                                        
										
										<div class="col-md-4">
								<?php //if(!isset($member) || is_admin() || !is_admin() && $member->admin == 0) { ?>
										<!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
										<input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1"/>
										<input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>
										<div class="clearfix form-group" style="margin-top:-20px;"></div>
										<div id="password_field">
										    <label for="password" class="control-label">Create Password for APP</label>
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
												<?php //echo _l('staff_add_edit_password_last_changed'); ?>
												<!--<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_password_change); ?>">
													<?php echo time_ago($member->last_password_change); ?>
												</span>-->
											<?php } } ?>
										</div>
										
										
										<?php //} ?>

									</div>

										    
											
									</div>

									
                                    </div>
									


									
									<div class="row">
										    


									    <div class="col-md-7" >
									   <?php if(is_admin() || has_permission('hrm_hr_records','', 'edit')){ ?>
										<!--<div class="form-group">
											<div class="row">
												<div class="col-md-6">
													<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('staff_email_signature_help'); ?>"></i>
													<?php $value = (isset($member) ? $member->email_signature : ''); ?>
													<?php echo render_textarea('email_signature','settings_email_signature',$value, ['data-entities-encode'=>'true']); ?>
												</div>
												<div class="col-md-6">
													<?php
													$orther_infor = (isset($member) ? $member->orther_infor : '');
													echo render_textarea('orther_infor','hr_orther_infor',$orther_infor); ?>
												</div>
											</div>
                                                -->
											
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
                            
                            <div class="col-md-2"> 
									<?php
									if(isset($staff_cover_image) && $staff_cover_image != false){
										$link_cover_image = 'uploads/staff_profile_images/' . $member->staffid . '/thumb_'.$member->profile_image;
										$image_exist = file_exists(FCPATH .$link_cover_image); 
									}else{
										$image_exist = false;
									}
									?>
								    <div class="picture-container pull-left">
									<div class="picture pull-left">
										<img src="<?php if(isset($staff_cover_image) && isset($image_exist) && isset($link_cover_image)){ echo  base_url($link_cover_image); }else{  echo site_url(HR_PROFILE_PATH.'none_avatar.jpg'); } ?>" class="picture-src" id="wizardPicturePreview" title="">
										<input type="file" name="profile_image" class="form-control" id="profile_image" accept=".png, .jpg, .jpeg">
									</div>
								    </div>
							    </div>
									<?php } ?> 
									</div>
									

							

									
								
							</div>

									<div class="clearfix"></div>
									
									</div>
									

									<?php $rel_id = (isset($member) ? $member->staffid : false); ?>
									<?php echo render_custom_fields('staff',$rel_id); ?>

									<div class="row">
										<div class="col-md-2">
											<?php if(!isset($member) && total_rows(db_prefix().'emailtemplates',array('slug'=>'new-staff-created','active'=>0)) === 0){ ?>
												<!--<div class="checkbox checkbox-primary">
													<input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
													<label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
												</div>-->
											<?php } ?>
										</div>
									</div>


							<div class="modal-footer">
								<!--<a href="<?php echo admin_url('hr_profile/staff_infor'); ?>"  class="btn btn-default mr-2 "><?php echo _l('hr_close'); ?></a>-->
								<?php if(isset($member)){
								if (has_permission_new('hrm_hr_records', '', 'edit')) {
								    ?>
								<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
								<?php }else{
								    ?>
								    <a href="#" class="btn btn-info disabled">Update</a>
								    <?php
								} 
								    
								}else{
									if (has_permission_new('hrm_hr_records', '', 'create')) {
									?>
								<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
								<?php }else{
								?>
								<a href="#" class="btn btn-info disabled"><?php echo _l('submit'); ?></a>
								<?php
								}}?>
							</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			</div>
<!--<style>	
    .control-label, label{
        margin-bottom: 4px;
    }
    .nav-tabs{
       margin-bottom: 4px; 
    }
</style>-->

		</div>
		<div class="btn-bottom-pusher"></div>
	</div>
	<?php init_tail(); ?>
	<?php 
	 //require('modules/hr_profile/assets/js/hr_record/add_staff_js.php');
	if(isset($member)){
	    require('modules/hr_profile/assets/js/hr_record/only_edit_staff_js.php');
	}else{
	   require('modules/hr_profile/assets/js/hr_record/only_add_js.php');
	}
	
	?>
	<?php $this->load->view('hr_record/group_model'); ?>
</body>
</html>