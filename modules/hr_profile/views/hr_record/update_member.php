<div class="modal fade" id="appointmentModal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                <h4 class="modal-title"><?php echo html_entity_decode($title); ?></h4>
            </div>

     <?php echo form_open_multipart(admin_url('hr_profile/add_edit_member/'.$staffid), array('id' => 'add_edit_member')); ?>
            <div class="modal-body">
              <ul class="nav nav-tabs" role="tablist">
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
                  
               </ul>

               <div class="tab-content">
                  <div class="manage_staff hide">
                    <?php 
                    if(isset($manage_staff)){
                        echo form_hidden('manage_staff');
                    }
                     ?>
                  </div>
                  <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">

                        

                        <div class="row">
                           

                           <div class="col-md-2">
                              <?php  $hr_codes = (isset($member) ? $member->staff_identifi : ''); ?>
                              <div class="form-group" app-field-wrapper="staff_identifi">
                                 <label for="staff_identifi" class="control-label"><?php echo _l('hr_staff_code'); ?></label>
                                 <input type="text" id="staff_identifi" name="staff_identifi" readonly class="form-control" value="<?php echo html_entity_decode($hr_codes) ?>" aria-invalid="false" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ; }  ?>>
                              </div>
                           </div> 
                           <div class="col-md-2">
											<?php $value = (isset($member) ? $member->AccountID : ''); ?>
												<label for="AccountID" class="control-label">AccountID</label>
												<input type="text"  id="AccountID" name="AccountID" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
											    
										</div>
                            <div class="col-md-4">
                                <?php $value = (isset($member) ? $member->firstname : ''); ?>
                           <?php $lastname = (isset($member) ? $member->lastname : ''); ?>
                           <?php $attrs = (isset($member) ? array() : array('autofocus'=>true)); ?>
                              <?php echo render_input('firstname','hr_firstname',$value,'text',$attrs); ?>
                           </div>
                           <div class="col-md-4">
                              <?php echo render_input('lastname','hr_lastname',$lastname,'text',$attrs); ?>
                           </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
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
										<?php echo render_select('role',$roles_value,array('roleid','name'),'staff_add_edit_role',$selected); ?>
										</div>
                            
                            <div class="col-md-4">
                             <?php 
                             $username = (isset($member) ? $member->username : ''); 
                             if($member->username == ""){
                                 $attrs = array();
                             }else {
                                 
                                 $attrs = array('disabled'=>true);
                             }
                             echo render_input('username','UserName', $username,'text',$attrs); ?>
                           </div>

                           <div class="col-md-4">
                             <?php 
                             $birthday = (isset($member) ? $member->birthday : ''); 
                             echo render_date_input('birthday','hr_hr_birthday',_d($birthday)); ?>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-4">
                               <?php $value = (isset($member) ? $member->email : ''); ?>
                               <div class="form-group" app-field-wrapper="email">
                                <label for="email1" class="control-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
                              </div>
                           </div>
                           
                           <div class="col-md-4">
                               <?php $value = (isset($member) ? $member->peremail : ''); ?>
                               <div class="form-group" app-field-wrapper="peremail">
                                <label for="peremail" class="control-label">Personal Email</label>
                                <input type="email" id="peremail" name="peremail" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                              <div class="form-group">
                                  <?php //echo render_input('phonenumber','staff_add_edit_phonenumber',$value); ?>
                                <label for="phonenumber" class="control-label">Mobile No.</label>
								<input type="tel" maxlength="10" pattern="[6789][0-9]{9}" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="<?php echo html_entity_decode($value) ?>" <?php if(!is_admin() && !has_permission('hrm_hr_records','', 'edit') && !has_permission('hrm_hr_records','', 'create')){ echo 'disabled' ;}  ?>>
								<span class="mob_denger" style="color:red;"></span>
                              </div>
                              
                           </div>
                           
                           <div class="col-md-4">
											
								<?php
											
									$selected = '1';
			
										$comp_assigned = unserialize($member->staff_comp);
								?>
                                <div class="form-group">
                    				<label for="workplace" class="control-label">Select Company</label>
                    				<select name="company_id1[]" class="selectpicker" id="company_id1" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true"> 
                    					<option value=""></option>
                    					
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
							
							<div class="col-md-4">
                              <div class="form-group">
                                  <label for="active" class="control-label"><?php echo _l('hr_status_work'); ?></label>
                                  <select name="active" class="selectpicker" id="active" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                    <option value="1" <?php if(isset($member) && $member->active == '1'){echo 'selected';} ?>>Active</option>
                                    <option value="0" <?php if(isset($member) && $member->active == '0'){echo 'selected';} ?>>De-Active</option>
                                    
                                 </select>
                              </div>
                           </div>
                           
                           <div class="col-md-4">
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


                        </div>
                        
                       <!-- <div class="row for_rsm">
									   <div class="col-md-4">
									       <div class="form-group">
												<label for="asm_list_reported_to_rsm" class="control-label">ASM </label>
												<select class="form-control asm_list selectpicker" name="asm_list_reported_to_rsm[]" data-live-search="true" multiple="true" id="asm_list_reported_to_rsm">
                								   
                                    <?php 
                                       $selected = array();
                                       if($member->job_position == "4"){
                                       foreach($staff_hierarchy as $sh){  
                                          array_push($selected, $sh['report_by']);
                                        } } ?>
                						<?php foreach($not_assigned_an_selected_asm as $n_asm){ ?> 
                                      <option value="<?php echo html_entity_decode($n_asm['staffid']); ?>" <?php if($member->job_position == "4"){ if(in_array($n_asm['staffid'], $selected)) echo "selected"; } ?>><?php echo $n_asm["firstname"]." ".$n_asm["lastname"]; ?></option>
                                   <?php } ?>
                								    
                								    
                								</select>
											</div>
									   </div>
									</div>-->
									
								<!--	<div class="row for_asm">
									   <div class="col-md-4">
									       <div class="form-group ">
												<label for="rsm_list_reported_by_asm" class="control-label">RSM</label>
												<select class="form-control  selectpicker" name="rsm_list_reported_by_asm" data-live-search="true"  id="rsm_list_reported_by_asm">
                							<option value="">Nothing selected</option>	
                                      
                                   
                                   <?php foreach($rsm_list as $rsm){ ?> 
                                      <option value="<?php echo html_entity_decode($rsm['staffid']); ?>" <?php if($member->job_position == "5"){ if($rsm['staffid'] == $staff_hierarchy[0]['report_to']) echo "selected"; } ?>><?php echo $rsm["firstname"]." ".$rsm["lastname"]; ?></option>
                                   <?php } ?>
                                   
                								    
                								</select>
											</div>
									   </div>
									   
									   <div class="col-md-4">
									   <div class="form-group">
												<label for="ase_list_reported_to_asm" class="control-label">ASE</label>
												<select class="form-control selectpicker" name="ase_list_reported_to_asm[]" data-live-search="true" multiple="true" id="ase_list_reported_to_asm">
                								      
                                   
                                                <?php 
                                       $selected = array();
                                       if($member->job_position == "5"){
                                       foreach($staff_hierarchy as $sh){  
                                          array_push($selected, $sh['report_by']);
                                        } } ?>
                						<?php foreach($not_assigned_an_selected_ase as $n_ase){ ?> 
                                      <option value="<?php echo html_entity_decode($n_ase['staffid']); ?>" <?php if(in_array($n_ase['staffid'], $selected)) echo "selected";?>><?php echo $n_ase["firstname"]." ".$n_ase["lastname"]; ?></option>
                                   <?php } ?>
                                   
                								</select>
											</div>
										</div>
											
									</div>-->
									
									<!--<div class="row for_ase">
									   
									   
									   <div class="col-md-4">
									   <div class="form-group">
												<label for="asm_list_reported_by_ase" class="control-label">ASM</label>
												<select class="form-control  selectpicker" name="asm_list_reported_by_ase" data-live-search="true" id="asm_list_reported_by_ase">
                								
                                       <option value="">Nothing selected</option>
                                        <?php foreach($asm_list as $asm){ ?> 
                                      <option value="<?php echo html_entity_decode($asm['staffid']); ?>" <?php if($member->job_position == "6"){ if($asm['staffid'] == $staff_hierarchy[0]['report_to']) echo "selected"; } ?>><?php echo $asm["firstname"]." ".$asm["lastname"]; ?></option>
                                   <?php } ?>
                								    
                								</select>
											</div>
										</div>
										
										<div class="col-md-4">
									    <div class="form-group">
												<label for="so_list_reported_to_ase" class="control-label">SO</label>
												<select class="form-control selectpicker" name="so_list_reported_to_ase[]" data-live-search="true" multiple="true" id="so_list_reported_to_ase">
                						
                                   
                                   
                                               <?php 
                                       $selected = array();
                                       if($member->job_position == "6"){
                                       foreach($staff_hierarchy as $sh){  
                                          array_push($selected, $sh['report_by']);
                                        } }?>
                						<?php foreach($not_assigned_an_selected_so as $n_so){ ?> 
                                      <option value="<?php echo html_entity_decode($n_so['staffid']); ?>" <?php if(in_array($n_so['staffid'], $selected)) echo "selected";?>><?php echo $n_so["firstname"]." ".$n_so["lastname"]; ?></option>
                                   <?php } ?>
                								    
                								</select>
											</div>
										</div>
										
											
									</div>-->
									
									<!--<div class="row for_so">
									   
										<div class="col-md-4">
									    <div class="form-group">
												<label for="ase_list_reported_by_so" class="control-label">ASE</label>
												<select class="form-control selectpicker" name="ase_list_reported_by_so" data-live-search="true" id="ase_list_reported_by_so">
                								    
                                       <option value="">Nothing selected</option>
                                   <?php foreach($ase_list as $ase){ ?> 
                                      <option value="<?php echo html_entity_decode($ase['staffid']); ?>" <?php if($member->job_position == "8"){ if($ase['staffid'] == $staff_hierarchy[0]['report_to']) echo "selected"; } ?>><?php echo $ase["firstname"]." ".$ase["lastname"]; ?></option>
                                   <?php } ?>
                								    
                								</select>
											</div>
										</div>
										
										<div class="col-md-4">
									    <div class="form-group">
												<label for="distributor_list_reported_to_so" class="control-label">Distributor</label>
												<select class="form-control selectpicker" name="distributor_list_reported_to_so[]" data-live-search="true" multiple="true" id="distributor_list_reported_to_so">
                					
                                              <?php 
                                       $selected = array();
                                       if($member->job_position == "8"){
                                           foreach($staff_hierarchy as $sh){  
                                          array_push($selected, $sh['report_by']);
                                        } 
                                       }
                                       ?>
                						<?php foreach($not_assigned_an_selected_dist as $n_dist){ ?> 
                                      <option value="<?php echo html_entity_decode($n_dist['AccountID']); ?>" <?php if(in_array($n_dist['AccountID'], $selected)) echo "selected";?>><?php echo $n_dist["company"]; ?></option>
                                   <?php } ?>
                								    
                								</select>
											</div>
										</div>
										
											
									</div>-->
									
									<!--<div class="row for_tsi">
									   
										<div class="col-md-4">
										   
									    <div class="form-group">
												<label for="so_list_reported_by_tsi" class="control-label">SO</label>
												<select class="form-control selectpicker" name="so_list_reported_by_tsi" data-live-search="true" id="so_list_reported_by_tsi">
                								    
                                      <option value="">Nothing selected</option>
                                   <?php foreach($allso_list as $so){ ?> 
                                      <option value="<?php echo html_entity_decode($so['staffid']); ?>" <?php if($member->job_position == "7"){ if($so['staffid'] == $staff_hierarchy[0]['report_to']) echo "selected"; } ?>><?php echo $so["firstname"]." ".$so["lastname"]; ?></option>
                                   <?php } ?>
                								    
                								</select>
											</div>
										</div>
										
										
										
											
									</div>-->

                        <div class="row">
                            
                           <!--<div class="col-md-4">
                              <div class="form-group">
                                <label for="workplace" class="control-label"><?php echo _l('hr_hr_workplace'); ?></label>
                                <select name="workplace" class="selectpicker" id="workplace" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                  <option value=""></option>                  
                                  <?php foreach($workplace as $w){ ?>

                                    <option value="<?php echo html_entity_decode($w['id']); ?>" <?php if(isset($member) && $member->workplace == $w['id']){echo 'selected';} ?>><?php echo html_entity_decode($w['name']); ?></option>

                                 <?php } ?>
                              </select>
                              </div>
                           </div>-->

                           
                        </div>

                        <div class="row">
                           

                           <div class="col-md-4">
                              <!--teamanage -->
                              <?php if(has_permission('hrm_hr_records','', 'edit') || has_permission('hrm_hr_records','', 'create')){ ?>
                                <?php $value = (isset($member) ? $member->team_manage : ''); ?>
                                <?php echo render_select('team_manage',$list_staff,array('staffid','full_name'),'Report To',$value); ?>
                             <?php } ?>
                             <!--teamanage -->
                           </div>
                           
                           <div class="col-md-4">
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
							            <div class="col-md-4">
                							<div class="form-group">
                							    <?php
                							    $city_name = get_city_name_by_state_id($member->state);
                							    //print_r($city_name);
                							    ?>
                								<label for="city" class="control-label"><span style="color:#fc2d42;font-size:11px;">*</span> City</label>
                								
                								<select class="form-control city" name="city" id="city" required>
                								    <?php foreach($city_name as $cn){ ?>
                								    <option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$member->city){ echo 'selected'; }?>><?php echo $cn["city_name"]; ?></option>
                								    	<?php } ?>
                								    
                								    
                								</select>
                								
                							</div>
						                </div>
						                
						                <div class="col-md-4">
                							<div class="form-group">
                							    <?php
                							    $quarter_name = get_hedquarter_name_by_state_id($member->state);
                							    //print_r($city_name);
                							    ?>
                								<!--<label for="headqurter" class="control-label"><span style="color:#fc2d42;font-size:11px;">*</span> Head Quarter</label>
                								
                								<select class="form-control headqurter" name="headqurter" id="headqurter" required>
                								    <?php foreach($quarter_name as $cn){ ?>
                								    <option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$member->headqurter){ echo 'selected'; }?>><?php echo $cn["name"]; ?></option>
                								    	<?php } ?>
                								    
                								    
                								</select>-->
                								<?php
                								$quarter_list = array();
                								foreach($quarter_name as $cn){
                								    if($cn['id']==$member->headqurter){
                								        $selected = $cn['id'];
                								    }
                								    
                								}
                								
                							if(is_admin() || get_option('staff_members_create_inline_customer_groups') == '1'){
                                              echo render_select_with_input_group('headqurter',$quarter_name,array('id','name'),'Head Quarter',$selected,'<a href="'.admin_url('hr_profile/setting?group=hedquarter').'" class="add_quarter" data-toggle="modal"><i class="fa fa-plus"></i></a>',array('data-actions-box'=>false),array(),'','',false);
                                              } else {
                                                echo render_select('headqurter',$quarter_name,array('id','name'),'Head Quarter',$selected,array('multiple'=>false,'data-actions-box'=>false),array(),'','',false);
                                              }
                							
                							?>
                								
                							</div>
						                </div>
						                
						                <div class="col-md-4">
											<div class="form-group">
												<label for="login_access" class="control-label">ERP Access</label>
												<select name="login_access" class="selectpicker" id="login_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value="Yes" <?php if(isset($member) && $member->login_access == 'Yes'){echo 'selected';} ?>>Yes</option>
													<option value="No" <?php if(isset($member) && $member->login_access == 'No'){echo 'selected';} ?>>No</option>
													</select>
											</div>
										</div>
										
										<div class="col-md-4">
											<div class="form-group">
												<label for="app_access" class="control-label">SO App Access</label>
												<select name="app_access" class="selectpicker" id="app_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
													<option value="Yes" <?php if(isset($member) && $member->app_access == 'Yes'){echo 'selected';} ?>>Yes</option>
													<option value="No" <?php if(isset($member) && $member->app_access == 'No'){echo 'selected';} ?>>No</option>
													</select>
											</div>
										</div>
										
										<div class="col-md-4">
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
                        </div>  

                        <div class="row">
                            
                            
                            
                           <!--<div class="col-md-6">
                             <div class="form-group">
                                 <label for="hourly_rate"><?php echo _l('staff_hourly_rate'); ?></label>
                                 <div class="input-group">
                                   <input type="number" name="hourly_rate" value="<?php if(isset($member)){echo html_entity_decode($member->hourly_rate);} else {echo 0;} ?>" id="hourly_rate" class="form-control">
                                    <span class="input-group-addon">
                                       <?php echo html_entity_decode($base_currency->symbol); ?>
                                    </span>
                                 </div>
                              </div>
                           </div>-->
                           <?php
                           //print_r($state);
                           ?>
                           
                                        
										
						                <div class="col-md-6">
                                <?php if(get_option('disable_language') == 0){ ?>
                                 <div class="form-group">
                                     <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?></label>
                                     <select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                       <option value=""><?php echo _l('system_default_string'); ?></option>
                                       <?php foreach($this->app->get_available_languages() as $availableLanguage){
                                         $selected = '';
                                         if(isset($member)){
                                           if($member->default_language == $availableLanguage){
                                             $selected = 'selected';
                                          }
                                       }
                                       ?>
                                       <option value="<?php echo html_entity_decode($availableLanguage); ?>" <?php echo html_entity_decode($selected); ?>><?php echo ucfirst($availableLanguage); ?></option>
                                    <?php } ?>
                                    </select>
                                 </div>
                                 <?php } ?>
                              </div>
                           <!--<div class="col-md-6">
                                 <div class="form-group">
                                   <label for="direction"><?php echo _l('document_direction'); ?></label>
                                   <select class="selectpicker" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
                                     <option value="" <?php if(isset($member) && empty($member->direction)){echo 'selected';} ?>></option>
                                     <option value="ltr" <?php if(isset($member) && $member->direction == 'ltr'){echo 'selected';} ?>>LTR</option>
                                     <option value="rtl" <?php if(isset($member) && $member->direction == 'rtl'){echo 'selected';} ?>>RTL</option>
                                  </select>
                                  </div>
                              </div>-->
                        </div>

                        

<div class="row">
                        <?php if(is_admin() || has_permission('hrm_hr_records','', 'edit')){ ?>
                           <!--
                           <div class="form-group">
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
                                <div class="col-md-6">
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
                           <!--</div>-->
                        <?php } ?>

<div class="col-md-3">  
                           <div class="picture-container pull-left">
                              <div class="picture pull-left">
                                <?php echo staff_profile_image($member->staffid,array('img','img-responsive','staff-profile-image-thumb','picture-src'),'thumb', ['id' => 'wizardPicturePreview']); ?>
                                <?php 
                                 echo staff_profile_image($member->staffid,array('img','img-responsive','staff-profile-image-thumb','picture-src'),'thumb', ['id' => 'wizardPicturePreview']);
                                 ?>
                                <input type="file" name="profile_image" class="form-control" id="profile_image" accept=".png, .jpg, .jpeg">
                              </div>
                           </div>
                        </div>

                        <div class="clearfix"></div>
                      </div>  
                        
                        <?php $rel_id = (isset($member) ? $member->staffid : false); ?>
                        <?php echo render_custom_fields('staff',$rel_id); ?>

                        <!--<div class="row">
                           <div class="col-md-12">
                              <hr class="hr-10" />
                             

                              <?php if(!isset($member) && total_rows(db_prefix().'emailtemplates',array('slug'=>'new-staff-created','active'=>0)) === 0){ ?>
                                  <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
                                    <label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
                                 </div>
                              <?php } ?>
                           </div>
                        </div>-->
                    <div id="password_field">
                        <?php if(!isset($member) || is_admin() || !is_admin() && $member->admin == 0) { ?>
                         <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                         <input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1"/>
                         <input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/>
                         <div class="clearfix form-group"></div>
                         
                             <label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?>/Reset Password</label>
                         <div class="input-group">
                          <input type="password" class="form-control password" name="password" autocomplete="off">
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
                        <?php } ?>
                        </div>
                  </div>

                  <div role="tabpanel" class="tab-pane " id="tab_staff_contact">

                     <div class="row">
                        <!--<div class="col-md-6">
                          <?php 
                          $home_town = (isset($member) ? $member->home_town : '');
                          echo render_input('home_town','hr_hr_home_town',$home_town,'text'); ?> 
                        </div>-->
                        
                        <div class="col-md-3">
                           <div class="form-group">
                              <label for="marital_status" class="control-label"><?php echo _l('hr_hr_marital_status'); ?></label>
                              <select name="marital_status" class="selectpicker" id="marital_status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                <option value=""></option>                  
                                <option value="<?php echo 'single'; ?>" <?php if(isset($member) && $member->marital_status == 'single'){echo 'selected';} ?>><!--<?php echo _l('single'); ?>-->single</option>
                                <option value="<?php echo 'married'; ?>" <?php if(isset($member) && $member->marital_status == 'married'){echo 'selected';} ?>><?php echo _l('married'); ?></option>
                             </select>
                          </div>
                       </div>
                       
                       <div class="col-md-3">
                              <div class="form-group">
                                  <label for="sex" class="control-label"><?php echo _l('hr_sex'); ?></label>
                                  <select name="sex" class="selectpicker" id="sex" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                    <option value=""></option>                  
                                    <option value="<?php echo 'male'; ?>" <?php if(isset($member) && $member->sex == 'male'){echo 'selected';} ?>><?php echo _l('male'); ?></option>
                                    <option value="<?php echo 'female'; ?>" <?php if(isset($member) && $member->sex == 'female'){echo 'selected';} ?>><?php echo _l('female'); ?></option>
                                 </select>
                              </div>
                           </div>
                       
                       <div class="col-md-6">
                          <?php 
                          $current_address = (isset($member) ? $member->current_address : '');
                          echo render_textarea('current_address','hr_current_address',$current_address,'text'); ?>
                        </div>
                        <!--<div class="col-md-4">
                          <?php
                          $nation = (isset($member) ? $member->nation : '');
                          echo render_input('nation','hr_hr_nation',$nation,'text'); ?>
                        </div>-->

                       
                     </div>

                     

                     <!--<div class="row">
                        <div class="col-md-4">
                         <?php
                         $birthplace = (isset($member) ? $member->birthplace : '');
                         echo render_input('birthplace','hr_hr_birthplace',$birthplace,'text'); ?> 
                        </div>
                        <div class="col-md-4">
                          <?php 
                          $religion = (isset($member) ? $member->religion : '');
                          echo render_input('religion','hr_hr_religion',$religion,'text'); ?>
                        </div>
                        <div class="col-md-4">
                          <?php 
                          $identification = (isset($member) ? $member->identification : '');
                          echo render_input('identification','hr_citizen_identification',$identification,'text'); ?>
                        </div>
                     </div>-->

                     <!--<div class="row">
                        
                        <div class="col-md-4">
                          <?php
                          $days_for_identity = (isset($member) ? $member->days_for_identity : '');
                          echo render_date_input('days_for_identity','hr_license_date',_d($days_for_identity)); ?>
                        </div>
                        <div class="col-md-4">
                          <?php
                          $place_of_issue = (isset($member) ? $member->place_of_issue : '');
                          echo render_input('place_of_issue','hr_hr_place_of_issue',$place_of_issue, 'text'); ?>
                        </div>
                        <div class="col-md-4">
                          <?php 
                          $resident = (isset($member) ? $member->resident : '');
                          echo render_input('resident','hr_hr_resident',$resident,'text'); ?>
                        </div>
                     </div> -->

                     <div class="row">
                        <div class="col-md-4">
                           <?php
                           $pan_number = (isset($member) ? $member->pan_number : '');
                           //echo render_input('pan_number','PAN number',$pan_number, 'text'); ?>
                           <label for="pan_number" class="control-label">PAN number</label>
							<input type="tel" maxlength="10"  name="pan_number" pattern="[0-9 A-Z] {10}" id="pan_number" class="form-control" value="<?php echo $pan_number?>">
							<span class="pan_denger" style="color:red;"></span>
                        </div>
                        <div class="col-md-4">
                           <?php
                           $aadhar_number = (isset($member) ? $member->aadhar_number : '');
                           //echo render_input('aadhar_number','Aadhar number',$aadhar_number, 'text'); ?>
                           <label for="aadhar_number" class="control-label">Aadhar number</label>
							<input type="tel" maxlength="12"  name="aadhar_number" pattern="[0-9] {10}" id="aadhar_number" class="form-control" value="<?php echo $aadhar_number?>">
							<span class="aadhar_denger" style="color:red;"></span>
                        </div>
                        <div class="col-md-4">
                           <?php
                           $account_number = (isset($member) ? $member->account_number : '');
                           //echo render_input('account_number','hr_bank_account_number',$account_number, 'text'); ?>
                           <label for="account_number" class="control-label">Account number</label>
							<input type="tel" minlenght="9" maxlength="18"  name="account_number" pattern="[0-9] {10}" id="account_number" class="form-control" value="<?php echo $account_number?>">
							<span class="actnumber_denger" style="color:red;"></span>
                        </div>
                        
                     </div>

                     <div class="row">
                        
                        <div class="col-md-4">
                           <?php
                           $name_account = (isset($member) ? $member->name_account : '');
                           echo render_input('name_account','Bank account holder',$name_account, 'text'); ?>
                        </div>
                        <div class="col-md-4">
                          <?php
                          $issue_bank = (isset($member) ? $member->issue_bank : '');
                          echo render_input('issue_bank','hr_bank_name',$issue_bank, 'text'); ?>
                        </div>
                     </div>
                     </br></br>

                     <!--<div class="row">
                        
                        <div class="col-md-4">
                           <?php
                           $Personal_tax_code = (isset($member) ? $member->Personal_tax_code : '');
                           echo render_input('Personal_tax_code','hr_Personal_tax_code',$Personal_tax_code, 'text'); ?>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php echo _l('staff_add_edit_facebook'); ?></label>
                              <input type="text" class="form-control" name="facebook" value="<?php if(isset($member)){echo html_entity_decode($member->facebook);} ?>">
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="linkedin" class="control-label"><i class="fa fa-linkedin"></i> <?php echo _l('staff_add_edit_linkedin'); ?></label>
                              <input type="text" class="form-control" name="linkedin" value="<?php if(isset($member)){echo html_entity_decode($member->linkedin);} ?>">
                           </div>
                        </div>
                     </div>-->

                     

                     <!--<div class="row">
                        <div class="col-md-4">
                           <div class="form-group">
                             <label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php echo _l('staff_add_edit_skype'); ?></label>
                             <input type="text" class="form-control" name="skype" value="<?php if(isset($member)){echo html_entity_decode($member->skype);} ?>">
                           </div>
                        </div>

                     </div>-->

                  </div>


            </div>
<style>
    .control-label, label{
        margin-bottom: 4px;
    }
    .nav-tabs{
       margin-bottom: 4px; 
    }
</style>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('update'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div><!-- /.modal-content -->

    </div>
</div>
<?php 
  require('modules/hr_profile/assets/js/hr_record/add_update_staff_js.php');
 ?>
<?php //$this->load->view('hr_record/group_model'); ?>