<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">

						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb"
								style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i
												class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>HR</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Staff members</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait while fetching data.</div>
								<div class="searchh3" style="display:none;">Please wait while creating new record.</div>
								<div class="searchh4" style="display:none;">Please wait while updating data.</div>
							</div>
							<br>
							<div class="col-md-12 mbot5">
								<h4 class="bold p_style">Personal Information:</h4>
								<hr class="hr_style">
							</div>
							<?php
								$next_staff_code = get_option('next_staff_number');
								$prefix = "STF";
								$new_next_staff_code = str_pad($next_staff_code, 5, '0', STR_PAD_LEFT);
							?>
							<div class="col-md-2 mbot5">
								<input type="hidden" name="hidden_staff_code" id="hidden_staff_code" value="<?php echo $new_next_staff_code;?>">
								<div class="form-group" app-field-wrapper="AccountID">
									<small class="req text-danger">* </small>
									<label for="AccountID" class="control-label">StaffID</label>
									<div class="input-group">
										<span class="input-group-addon">
											<?php echo $prefix; ?>
										</span>
										<input type="text" id="AccountID" name="AccountID" class="form-control" value="<?php echo $new_next_staff_code;?>" autocomplete="off" />
										<input type="hidden" id="userid" name="userid" class="form-control" value="" autocomplete="off" />
									</div>
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="firstname">
									<small class="req text-danger">* </small>
									<label for="firstname" class="control-label">First Name</label>
									<input type="text" id="firstname" name="firstname" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="lastname">
									<small class="req text-danger">* </small>
									<label for="lastname" class="control-label">Last Name</label>
									<input type="text" id="lastname" name="lastname" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="FatherName">
									<label for="FatherName" class="control-label">Father Name</label>
									<input type="text" id="FatherName" name="FatherName" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="MotherName">
									<label for="MotherName" class="control-label">Mother Name</label>
									<input type="text" id="MotherName" name="MotherName" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="SpouseName">
									<label for="SpouseName" class="control-label">Spouse Name</label>
									<input type="text" id="SpouseName" name="SpouseName" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="Department">
									<small class="req text-danger">* </small>
									<label for="Department" class="form-label">Department</label>
									<select name="Department" id="Department" class="selectpicker form-control" data-width="100%"
										data-none-selected-text="None selected" data-live-search="true">
										<option value="">None selected</option>
										<?php
										foreach ($departments as $key => $value) {
										?>
										<option value="<?php echo $value['departmentid'];?>">
											<?php echo $value['name']; ?>
										</option>
										<?php
										}
										?>
									</select>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="designation">
									<small class="req text-danger">* </small>
									<label for="designation" class="form-label">Designation</label>
									<select name="designation" id="designation" class="selectpicker form-control" data-width="100%"
										data-none-selected-text="None Selected" data-live-search="true">
										<option value="">None Selected</option>

									</select>
								</div>
							</div>


							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="StaffType">
									<small class="req text-danger">* </small>
									<label for="StaffType" class="form-label">Staff Type</label>
									<select name="StaffType" id="StaffType" class="selectpicker form-control" data-width="100%"
										data-none-selected-text="None Selected" data-live-search="true">
										<option value="">None Selected</option>
										<?php
                                foreach ($StaffType as $key => $value) {
                            ?>
										<option value="<?php echo $value['SubActGroupID'];?>">
											<?php echo $value['SubActGroupName']; ?>
										</option>
										<?php
                                }
                            ?>
									</select>
								</div>
							</div>


							<div class="col-md-2 mbot5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="EmpType" class="control-label">Employment Type</label>
									<select name="EmpType" class="selectpicker" id="EmpType" data-width="100%"
										data-none-selected-text="None selected" data-live-search="true">
										<option value="">None selected</option>
										<option value="OFF-ROLL">OFF-ROLL</option>
										<option value="ON-ROLL">ON-ROLL</option>
									</select>
								</div>
							</div>


							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="reporting_to">
									<small class="req text-danger">* </small>
									<label for="reporting_to" class="form-label">Reporting To</label>
									<select name="reporting_to" id="reporting_to" class="selectpicker form-control" data-width="100%"
										data-none-selected-text="None Selected" data-live-search="true">
										<option value="">None Selected</option>
										<?php
                                foreach ($list_staff as $key => $value) {
                            ?>
										<option value="<?php echo $value['staffid'];?>">
											<?php echo $value['firstname']. " ".$value['lastname']; ?>
										</option>
										<?php
                                }
                            ?>
									</select>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="EmpCode">
									<label for="EmpCode" class="control-label">Employee Code</label>
									<input type="text" id="EmpCode" name="EmpCode" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>
								<div class="form-group">
									<label for="">Opening Bal. Amount</label>
									<input type="text" name="opening_b" id="opening_b" value="0" class="form-control" <?php
										if(isset($client) && $staff_user_id !=="3" ){ echo "disabled" ;}?> >
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="phonenumber">
									<small class="req text-danger">* </small>
									<label for="phonenumber" class="control-label">Mobile No.</label>
									<input type="text" id="phonenumber" name="phonenumber" class="form-control" value="" maxlength="10"
										minlength="10" onkeypress="return isNumber(event)">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="altphonenumber">
									<label for="altphonenumber" class="control-label">Alt Mobile No.</label>
									<input type="text" id="altphonenumber" name="altphonenumber" class="form-control" value=""
										maxlength="10" minlength="10" onkeypress="return isNumber(event)">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="EmergencyContact">
									<label for="EmergencyContact" class="control-label">Emg. Mobile No.</label>
									<input type="text" id="EmergencyContact" name="EmergencyContact" class="form-control" value=""
										maxlength="10" minlength="10" onkeypress="return isNumber(event)">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="email">
									<label for="email" class="control-label">Email</label>
									<input type="text" id="email" name="email" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="peremail">
									<label for="peremail" class="control-label">Personal Email</label>
									<input type="text" id="peremail" name="peremail" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-12 mbot5">
								<h4 class="bold p_style">Address Information:</h4>
								<hr class="hr_style">
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="state">
									<small class="req text-danger">* </small>
									<label for="state" class="form-label">Current State</label>
									<select name="state" id="state" class="selectpicker form-control" data-width="100%"
										data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<?php
                                foreach ($state as $key => $value) {
                            ?>
										<option value="<?php echo $value['short_name'];?>">
											<?php echo $value['state_name'];?>
										</option>
										<?php
                                }
                            ?>
									</select>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="city" class="control-label">Current City</label>
									<select class="form-control city selectpicker" data-width="100%"
										data-none-selected-text="None Selected" name="city" id="city" data-live-search="true">
										<option value="">None selected</option>
									</select>

								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="zip">
									<label for="zip" class="control-label">Local Pin Code</label>
									<input type="text" name="zip" id="zip" class="form-control" onchange="validateZipCode" value=""
										maxlength="6" minlength="6" onkeypress="return isNumber(event)">
								</div>
							</div>


							<div class="col-md-3 mbot5">
								<?php echo render_input( 'address1', 'Current Address Line 1'); ?>
							</div>

							<div class="col-md-3 mbot5">
								<?php echo render_input( 'address2', 'Current Address Line 2'); ?>
							</div>

							<!--<div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="state">
                            <small class="req text-danger">* </small>
                            <label for="state" class="form-label">State</label>
                            <select name="state" id="state" class="selectpicker form-control" data-width="100%" data-none-selected-text="Non Selected" data-live-search="true">
                                <option value="">Non Selected</option>
                            <?php
                                foreach ($state as $key => $value) {
                            ?>
                                  <option value="<?php echo $value['short_name'];?>"><?php echo $value['state_name'];?></option>
                            <?php
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mbot5">
                        <div class="form-group">
                           <small class="req text-danger">* </small>
                            <label for="city" class="control-label">City</label>
                            <select class="form-control city selectpicker" data-width="100%" data-none-selected-text="None Selected" name="city" id="city" data-live-search="true">
                                <option value="">None selected</option>
                            </select>
                                
                        </div>
                    </div>-->

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="state">
									<small class="req text-danger">* </small>
									<label for="Permanentstate" class="form-label">Permanent State</label>
									<select name="Permanentstate" id="Permanentstate" class="selectpicker form-control" data-width="100%"
										data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<?php
                                foreach ($state as $key => $value) {
                            ?>
										<option value="<?php echo $value['short_name'];?>">
											<?php echo $value['state_name'];?>
										</option>
										<?php
                                }
                            ?>
									</select>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Permanentcity" class="control-label">Permanent City</label>
									<select class="form-control city selectpicker" data-width="100%"
										data-none-selected-text="None Selected" name="Permanentcity" id="Permanentcity"
										data-live-search="true">
										<option value="">None selected</option>
									</select>

								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="PermanentAddressPincode">
									<label for="PermanentAddressPincode" class="control-label">Permanent Pin code</label>
									<input type="text" id="PermanentAddressPincode" name="PermanentAddressPincode" maxlength="6"
										minlength="6" onkeypress="return isNumber(event)" class="form-control" value="">
								</div>
							</div>


							<div class="col-md-3 mbot5">
								<div class="form-group" app-field-wrapper="PermanentAddress">
									<label for="PermanentAddress" class="control-label">Permanent Address Line 1</label>
									<input type="text" id="PermanentAddress" name="PermanentAddress" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-3 mbot5">
								<div class="form-group" app-field-wrapper="PermanentAddressline2">
									<label for="PermanentAddressline2" class="control-label">Permanent Address Line 2</label>
									<input type="text" id="PermanentAddressline2" name="PermanentAddressline2" class="form-control"
										value="">
								</div>
							</div>

							<!--<div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="PermanentAddressPincode">
                            <label for="PermanentAddressPincode" class="control-label">Permanent Address Pincode</label>
                            <input type="text" id="PermanentAddressPincode" name="PermanentAddressPincode" maxlength="6" minlength="6" onkeypress="return isNumber(event)" class="form-control" value="">
                        </div>
                    </div>-->

							<div class="clearfix"></div>
							<div class="col-md-12 mbot5">
								<h4 class="bold p_style">Bank Information:</h4>
								<hr class="hr_style">
							</div>
							<div class="col-md-2 mbot5">
								<label for="ifsc" class="control-label">IFSC <img src="<?= base_url('assets/plugins/lightbox/images/loading.gif');?>" alt="Loader" style="width: 10px; display: none;" id="ifsc_code_loader"></label>
								<input type="text" name="ifsc" id="ifsc" class="form-control" value="<?php echo $account_number??''?>" onchange="validateIFSC(this.value)">
							</div>


							<div class="col-md-2 mbot5">
								<?php
						echo render_input('issue_bank','Bank Name',$issue_bank??'', 'text'); ?>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="BranchName">
									<label for="BranchName" class="control-label">Branch Name</label>
									<input type="text" id="BranchName" name="BranchName" class="form-control"
										value="<?php echo $BranchName??''?>">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="BranchAddress">
									<label for="BranchAddress" class="control-label">Branch Address</label>
									<input type="text" id="BranchAddress" name="BranchAddress" class="form-control"
										value="<?php echo $BranchAddress??''?>">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<label for="account_number" class="control-label">Account No. <img src="<?= base_url('assets/plugins/lightbox/images/loading.gif');?>" alt="Loader" style="width: 10px; display: none;" id="account_number_loader"></label>
								<input type="tel" minlenght="9" maxlength="18" name="account_number" pattern="[0-9] {10}"
									id="account_number" class="form-control" value="<?php echo $account_number ?? ''?>" onchange="validateAccountNumber(this.value)">
								<span class="actnumber_denger" style="color:red;"></span>
							</div>

							<div class="col-md-2 mbot5">
								<?php	echo render_input('name_account','Account Holder Name','', 'text'); ?>
							</div>


							<div class="clearfix"></div>

							<div class="col-md-12 mbot5">
								<h4 class="bold p_style">Other Information:</h4>
								<hr class="hr_style">
							</div>

							<div class="col-md-2 mbot5">
								<label for="sex">Sex</label>
								<select name="sex" id="sex" class="selectpicker form-control sex">
									<option value="male">Male</option>
									<option value="female">Female</option>
								</select>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group">
									<label for="marital_status" class="control-label">
										<?php echo _l('Marital Status'); ?>
									</label>
									<select name="marital_status" class="selectpicker" id="marital_status" data-width="100%"
										data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value="<?php echo 'single'; ?>">Single</option>
										<option value="<?php echo 'married'; ?>">
											<?php echo _l('Married'); ?>
										</option>
									</select>
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<?php 
                            //$curDate = date('d/m/Y');
                        echo render_date_input( 'birthday', 'Date of Birth','',['class' => 'date']); ?>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group">
									<label for="BloodGroup" class="control-label">Blood Group</label>
									<select name="BloodGroup" class="selectpicker" id="BloodGroup" data-width="100%"
										data-none-selected-text="None selected" data-live-search="true">
										<option value="">None selected</option>
										<!-- Common Blood Groups -->
										<option value="A+">A+</option>
										<option value="A-">A-</option>
										<option value="B+">B+</option>
										<option value="B-">B-</option>
										<option value="AB+">AB+</option>
										<option value="AB-">AB-</option>
										<option value="O+">O+</option>
										<option value="O-">O-</option>

										<!-- Rare Blood Groups -->
										<option value="A2">A2</option>
										<option value="A2+">A2+</option>
										<option value="A2-">A2-</option>
										<option value="A2B+">A2B+</option>
										<option value="A2B-">A2B-</option>
										<option value="Bombay (hh)">Bombay (hh)</option>
										<option value="Rh null">Rh null</option>
										<option value="In(Lu)">In(Lu)</option>
										<option value="Kell negative">Kell negative</option>
										<option value="Duffy negative">Duffy negative</option>
									</select>
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="NomineeName">
									<label for="NomineeName" class="control-label">Nominee Name</label>
									<input type="text" id="NomineeName" name="NomineeName" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<?php 
						//$curDate = date('d/m/Y');
						echo render_date_input('NomineeDob','Nominee DOB','',[]); ?>
							</div>
							<div class="col-md-2" style="display:None;">
								<div class="form-group">
									<label for="app_access" class="control-label">SO App Access</label>
									<select name="app_access" class="selectpicker" id="app_access" data-width="100%"
										data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value="No">No</option>
										<option value="Yes">Yes</option>
									</select>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group">
									<label for="literacy" class="control-label">
										<?php echo _l('Academic Level'); ?>
									</label>
									<select name="literacy" id="literacy" class="selectpicker" data-width="100%"
										data-none-selected-text="None selected">
										<option value=""></option>
										<option value="primary_level">
											<?php echo _l('hr_primary_level'); ?>
										</option>
										<option value="intermediate_level">
											<?php echo _l('hr_intermediate_level'); ?>
										</option>
										<option value="college_level">
											<?php echo _l('hr_college_level'); ?>
										</option>
										<option value="masters">
											<?php echo _l('hr_masters'); ?>
										</option>
										<option value="doctor">
											<?php echo _l('hr_Doctor'); ?>
										</option>
										<option value="bachelor">
											<?php echo _l('hr_bachelor'); ?>
										</option>
										<option value="engineer">
											<?php echo _l('hr_Engineer'); ?>
										</option>
										<option value="university">
											<?php echo _l('hr_university'); ?>
										</option>
										<option value="intermediate_vocational">
											<?php echo _l('hr_intermediate_vocational'); ?>
										</option>
										<option value="college_vocational">
											<?php echo _l('hr_college_vocational'); ?>
										</option>
										<option value="in-service">
											<?php echo _l('hr_in-service'); ?>
										</option>
										<option value="high_school">
											<?php echo _l('hr_high_school'); ?>
										</option>
										<option value="intermediate_level_pro">
											<?php echo _l('hr_intermediate_level_pro'); ?>
										</option>
									</select>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="TechnicalEduction">
									<label for="TechnicalEduction" class="control-label">Technical Education</label>
									<input type="text" id="TechnicalEduction" name="TechnicalEduction" class="form-control" value="">
								</div>
							</div>

							<!--<div class="col-md-2 mbot5">
					    <?php echo render_input('DeviceID','Device ID'); ?>
					</div>-->

							<!--<div class="col-md-2 mbot5">
						<?php 
						//$curDate = date('d/m/Y');
						echo render_date_input('datecreated','Started Date','',['class' => 'date']); ?>
					</div>-->

							<!--<div class="col-md-2 mbot5">
						<div class="form-group">
                            <label for="active" class="control-label"><?php echo _l('Is Active ?'); ?></label>
                            <select name="active" class="selectpicker" id="active" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                                <option value="1">Active</option>
                                <option value="0">De-Active</option>
                            </select>
                        </div>
					</div>-->

							<div class="col-md-2 mbot5">
								<div class="form-group">
									<label for="">Working Hours</label>
									<input type="text" name="working_hour" id="working_hour" value="0" class="form-control"
										onkeypress="return isNumber(event)">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<?php 
						//$curDate = date('d/m/Y');
						echo render_date_input('datecreated','Started Date','',['class' => 'date']); ?>
							</div>
							<div class="col-md-2 mbot5">
								<?php 
						echo render_date_input('DateOfLeaving','Date Of Leaving','',[]); ?>
							</div>
							<div class="col-md-2 mbot5">
								<?php 
						echo render_date_input('LastWorkingDay','Last Working Day','',[]); ?>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="Pan">
									<label for="Pan" class="control-label">PAN No.</label>
									<input type="text" maxlength="10" minlength="10" name="Pan" pattern="[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}"
										id="Pan" class="form-control" value="">
									<span class="pan_denger" style="color:red;"></span>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="Aadhaarno">
									<label for="aadhaar" class="control-label">Aadhaar No.</label>
									<input type="text" maxlength="12" minlength="12" name="Aadhaarno" pattern="[0-9] {12}" id="Aadhaarno"
										class="form-control numbersOnly" onkeypress="return isNumber(event)" value="">
									<span class="aadhar_denger" style="color:red;"></span>
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group">
									<label for="IsPF" class="control-label">Is PF ?</label>
									<select name="IsPF" class="selectpicker" id="IsPF" data-width="100%"
										data-none-selected-text="None selected">
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group">
									<label for="IsESIC" class="control-label">Is ESIC ?</label>
									<select name="IsESIC" class="selectpicker" id="IsESIC" data-width="100%"
										data-none-selected-text="None selected">
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>

							<!--<div class="col-md-2 mbot5">
						<?php 
						echo render_date_input('DateOfLeaving','Date Of Leaving','',''); ?>
					</div>
					<div class="col-md-2 mbot5">
						<?php 
						echo render_date_input('LastWorkingDay','Last Working Day','',''); ?>
					</div>-->

							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="UAN_No">
									<label for="UAN_No" class="control-label">UAN No</label>
									<input type="text" id="UAN_No" name="UAN_No" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-2 mbot5">
								<div class="form-group" app-field-wrapper="ESI_No">
									<label for="ESI_No" class="control-label">ESI No</label>
									<input type="text" id="ESI_No" name="ESI_No" class="form-control" value="">
								</div>
							</div>

							<!--<div class="col-md-2 mbot5">
						<?php 
						echo render_date_input('DateOfLeaving','Date Of Leaving','',''); ?>
					</div>
					<div class="col-md-2 mbot5">
						<?php 
						echo render_date_input('LastWorkingDay','Last Working Day','',''); ?>
					</div>-->

							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="ResignationReason">
									<label for="ResignationReason" class="control-label">Reason Of Resignation</label>
									<input type="text" id="ResignationReason" name="ResignationReason" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="Remark">
									<label for="Remark" class="control-label">Remarks</label>
									<input type="text" id="Remark" name="Remark" class="form-control" value="">
								</div>
							</div>

							<div class="col-md-2 mbot5">
								<div class="form-group">
									<label for="active" class="control-label">
										<?php echo _l('Is Active ?'); ?>
									</label>
									<select name="active" class="selectpicker" id="active" data-width="100%"
										data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value="1">Active</option>
										<option value="0">De-Active</option>
									</select>
								</div>
							</div>

							<div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
								<?php if (has_permission('hrm_hr_records', '', 'create')) { ?>
								<button type="button" class="btn btn-success saveBtn" onclick="this.disabled = true;"><i class="fa fa-save"></i> Save</button>
								<?php }else{ ?>
								<button type="button" class="btn btn-success saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
								<?php } ?>

								<?php if (has_permission('hrm_hr_records', '', 'edit')) { ?>
								<button type="button" class="btn btn-success updateBtn" onclick="this.disabled = true;"><i class="fa fa-save"></i> Update</button>
								<?php }else{ ?>
								<button type="button" class="btn btn-success updateBtn2 disabled"><i class="fa fa-save"></i> Update</button>
								<?php } ?>
                <button type="button" class="btn btn-danger cancelBtn"><i class="fa fa-refresh"></i> Reset</button>
                <button type="button" class="btn btn-info" onclick="$('#AccountID').trigger('dblclick');"><i class="fa fa-list"></i> Show List</button>
							</div>

							<?php if(isset($member)){ ?>
							<p class="text-muted">
								<?php echo _l('staff_add_edit_password_note'); ?>
							</p>
							<?php if($member->last_password_change != NULL){ ?>
							<?php //echo _l('staff_add_edit_password_last_changed'); ?>
							<!--<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($member->last_password_change); ?>">
											<?php echo time_ago($member->last_password_change); ?>
										</span>-->
							<?php } } ?>


							<div class="clearfix"></div>
							<!-- Iteme List Model-->

							<div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog" data-keyboard="false"
								data-backdrop="static">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header" style="padding:5px 10px;">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
													aria-hidden="true">&times;</span></button>
											<h4 class="modal-title">Account List</h4>
										</div>
										<div class="modal-body" style="padding:0px 5px !important">

											<div class="table-Account_List tableFixHead2">
												<table class="tree table table-striped table-bordered table-Account_List tableFixHead2"
													id="table_Account_List" width="100%">
													<thead>
														<tr>
															<th class="sortablePop" style="text-align:left;">StaffCode</th>
															<th class="sortablePop" style="text-align:left;">Full Name</th>
															<th class="sortablePop" style="text-align:left;">Staff Type</th>
															<th class="sortablePop" style="text-align:left;">Department</th>
															<th class="sortablePop" style="text-align:left;">Designation</th>
															<th class="sortablePop" style="text-align:left;">Mobile No.</th>
															<th class="sortablePop" style="text-align:left;">Active</th>
														</tr>
													</thead>
													<tbody id="stafflistbody">

												</table>
											</div>
										</div>
										<div class="modal-footer" style="padding:0px;">
											<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.."
												title="Type in a name" style="float: left;width: 100%;">
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
		$(document).ready(function () {
			$('.saveBtn').show();
			$('.updateBtn').hide();
			$('.saveBtn2').show();
			$('.updateBtn2').hide();

			$("#AccountID").dblclick(function () {
				$('#Account_List').modal('show');
				$.ajax({
					url: "<?php echo admin_url(); ?>hr_profile/GetAllStaffList",
					dataType: "JSON",
					method: "POST",
					beforeSend: function () {
						$('.searchh2').css('display', 'block');
						$('.searchh2').css('color', 'blue');
					},
					complete: function () {
						$('.searchh2').css('display', 'none');
					},
					success: function (data) {
						$('#stafflistbody').html(data);

						$('.get_AccountID').on('click', function () {
							AccountID = $(this).attr("data-id");
							$.ajax({
								url: "<?php echo admin_url(); ?>hr_profile/GetAccountDetailByID",
								dataType: "JSON",
								method: "POST",
								data: { AccountID: AccountID },
								beforeSend: function () {
									$('.searchh2').css('display', 'block');
									$('.searchh2').css('color', 'blue');
								},
								complete: function () {
									$('.searchh2').css('display', 'none');
								},
								success: function (data) {
									if (data == null) {
										ResetForm();
									} else {
										SetFormData(data);
									}
								}
							});
							$('#Account_List').modal('hide');
						});
					}
				});
				$('#Account_List').on('shown.bs.modal', function () {
					$('#myInput1').val('');
					$('#myInput1').focus();
				})
			});
			// AccountID Typing Validation
			$("#AccountID").keypress(function (e) {
				var keyCode = e.keyCode || e.which;
				if (keyCode == "") {
					$("#lblError").html("");
				} else {
					var regex = /^[A-Za-z0-9]+$/;
					var isValid = regex.test(String.fromCharCode(keyCode));
					return isValid;
				}
			});



			// Pan Number Typing Validation
			$('#Pan').keyup(function (e) {
				var val = $('#Pan').val();
				if (val == "") {
					$(".pan_denger").text(" ");
				} else {
					e.preventDefault();
					if (!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}')) {
						$(".pan_denger").text("Enter valid PAN number");
					} else {
						$(".pan_denger").text(" ");
					}
				}
			});

			$('#phonenumber').keyup(function (e) {
				e.preventDefault();
				if (!$('#phonenumber').val().match('[0-9]{10}')) {

					$(".mob_denger").text("Enter valid 10 digit mobile number");
				} else {
					$(".mob_denger").text(" ");
				}
			});

			$('#Aadhaarno').keyup(function (e) {
				e.preventDefault();
				if (!$('#Aadhaarno').val().match('[0-9]{12}')) {
					$(".aadhar_denger").text("Enter valid 12 digit Aadhar number");
				} else {
					$(".aadhar_denger").text(" ");
				}
			});

			$('#account_number').keyup(function (e) {
				e.preventDefault();
				if (!$('#account_number').val().match('[0-9]{9}')) {

					$(".actnumber_denger").text("Enter valid Account number");
				} else {
					$(".actnumber_denger").text(" ");
				}
			});
		});

		//on change of department
		$('#Department').on('change', function () {
			var value = $("#Department").val();
			$.ajax({
				url: "<?php echo admin_url(); ?>hr_profile/job_position_by_id",
				dataType: "JSON",
				method: "POST",
				cache: false,
				data: { value: value, },
				success: function (data) {
					var optionsHTMLHead = '<option value="">Non selected</option>';
					$.each(data, function (index, option) {
						optionsHTMLHead += '<option value="' + option.position_id + '">' + option.position_name + '</option>';
					});
					$('select[name=designation]').html(optionsHTMLHead);
					$('.selectpicker').selectpicker('refresh');
				}
			});
		})

		
		function validateIFSC(ifsc){
			var regex = /^[A-Za-z]{4}[0-9]{7}$/;
			if(!regex.test(ifsc)){
				$('#ifsc').focus();
				alert_float('warning', 'Please enter valid IFSC Code');
				return false;
			}
			$.ajax({
				url: '<?= admin_url(); ?>Accounts/fetchBankDetailsFromIFSC',
				type: 'POST',
				dataType: 'json',
				data: {
					ifsc_code: ifsc,
					'<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
				},
				beforeSend: function(){
					$('#ifsc_code_loader').show();
				},
				complete: function(){
					$('#ifsc_code_loader').hide();
				},
				success: function(response){
					if(response.success){
						$('#issue_bank').val(response.data.BANK);
						$('#BranchName').val(response.data.BRANCH);
						$('#BranchAddress').val(response.data.ADDRESS);
					}else{
						alert_float('warning', response.message);
					}
				}
			});
		}

		function validateAccountNumber(accountNumber){
			let ifsc = $('#ifsc').val();
			if(accountNumber.length < 10){
				$('#account_number').focus();
				alert_float('warning', 'Please enter valid Account Number');
				return false;
			}
			
			if(ifsc !== '' && ifsc !== null){
				$.ajax({
					url: '<?= admin_url(); ?>Accounts/verifyBankAccount',
					type: 'POST',
					dataType: 'json',
					data: {
						bank_ac_no: accountNumber,
						ifsc_code: ifsc,
						'<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
					},
					beforeSend: function(){
						$('#account_number_loader').show();
					},
					complete: function(){
						$('#account_number_loader').hide();
					},
					success: function(response){
						if(response.success == true){
							$('#name_account').val(response.data.full_name);
						}else{
							alert_float('warning', response.message);
						}
					}
				});
			}
		}

		function ResetForm() {
			var HiddenStaffCode = $('#hidden_staff_code').val();
			$('#AccountID').val(HiddenStaffCode);
			$('#firstname').val('');
			$('#lastname').val('');
			$('#Department').val('');
			$('.selectpicker').selectpicker('refresh');
			$("#designation").children().remove();
			$('.selectpicker').selectpicker('refresh');
			$('#StaffType').val('');
			$('.selectpicker').selectpicker('refresh');
			$('#reporting_to').val('');
			$('.selectpicker').selectpicker('refresh');
			$('#email').val('');
			$('#peremail').val('');
			$('#phonenumber').val('');
			$('#altphonenumber').val('');
			$('#state').val('');
			$('.selectpicker').selectpicker('refresh');

			$("#city").children().remove();
			$('.selectpicker').selectpicker('refresh');

			$('#zip').val('');
			$('#address1').val('');
			$('#address2').val('');
			$('#Pan').val('');
			$('#Aadhaarno').val('');
			$('#ifsc').val('');
			$('#account_number').val('');
			$('#name_account').val('');
			$('#issue_bank').val('');
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = today.getFullYear();
			today = dd + '/' + mm + '/' + yyyy;
			$('#birthday').val(today);
			$('#opening_b').val(0.00);

			$('#sex').selectpicker('val', 'male');
			$('.selectpicker').selectpicker('refresh')
			$('#literacy').selectpicker('val', '');
			$('.selectpicker').selectpicker('refresh');

			$('#marital_status').selectpicker('val', 'single');
			$('.selectpicker').selectpicker('refresh');
			$('#active').selectpicker('val', '1');
			$('.selectpicker').selectpicker('refresh');
			$('#IsPF').selectpicker('val', 'N');
			$('.selectpicker').selectpicker('refresh');
			$('#IsESIC').selectpicker('val', 'N');
			$('.selectpicker').selectpicker('refresh');
			$('#working_hour').val('0');

			$('#app_access').selectpicker('val', 'No');
			$('.selectpicker').selectpicker('refresh');
			$('#datecreated').val(today);
			$('#DeviceID').val('');
			$('#opening_b').removeAttr('disabled');

			$('#EmpCode').val('');
			$('#FatherName').val('');
			$('#MotherName').val('');
			$('#SpouseName').val('');
			$('#EmergencyContact').val('');
			$('#PermanentAddress').val('');
			$('#PermanentAddressPincode').val('');
			$('#BloodGroup').val('');
			$('#NomineeName').val('');
			$('#NomineeDob').val(today);
			$('#DateOfLeaving').val(today);
			$('#LastWorkingDay').val(today);
			$('#ResignationReason').val('');
			$('#TechnicalEduction').val('');
			$('#UAN_No').val('');
			$('#ESI_No').val('');
			$('#EmpType').val('');
			$('#Remark').val('');
			$('#BranchAddress').val('');
			$('#BranchName').val('');
			$('#Permanentstate').val('');
			$('#Permanentcity').val('');
			$('#PermanentAddressline2').val('');
			$('.selectpicker').selectpicker('refresh');


			$('.saveBtn').removeAttr('disabled');
			$('.updateBtn').removeAttr('disabled');
			$('.saveBtn').show();
			$('.updateBtn').hide();
			$('.saveBtn2').show();
			$('.updateBtn2').hide();
		}
		// Empty and open create mode
		$("#AccountID").focus(function () {
			ResetForm();
		});

		// Cancel selected data
		$(".cancelBtn").click(function () {
			ResetForm();
		});
		//========================== Set Form Data =====================================
		function SetFormData(data) {
			$('#AccountID').val(data.AccountID.substring(3, 8));
			$('#userid').val(data.staffid);
			$('#firstname').val(data.firstname);
			$('#lastname').val(data.lastname);
			$('select[id=Department]').val(data.departmentid);
			$('.selectpicker').selectpicker('refresh');

			let DesignationList = data.DesignationList;
			$("#designation").children().remove();
			for (var i = 0; i < DesignationList.length; i++) {
				$("#designation").append('<option value="' + DesignationList[i]["position_id"] + '">' + DesignationList[i]["position_name"] + '</option>');
			}
			$('.selectpicker').selectpicker('refresh');

			$('#designation').selectpicker('val', data.job_position);
			$('.selectpicker').selectpicker('refresh');

			$('#StaffType').val(data.SubActGroupID);
			$('.selectpicker').selectpicker('refresh');
			$('#reporting_to').val(data.team_manage);
			$('.selectpicker').selectpicker('refresh');

			$('#opening_b').val(data.BAL1);
			var staffid = $('#staffid').val();
			if (staffid !== "3") {
				$('#opening_b').attr('disabled', 'disabled');
			}
			$('#phonenumber').val(data.phonenumber);
			$('#altphonenumber').val(data.mobile2);

			$('#email').val(data.email);
			$('#peremail').val(data.peremail);

			$('#state').val(data.state);
			$('.selectpicker').selectpicker('refresh');

			let CityList = data.CityList;
			$("#city").children().remove();
			for (var i = 0; i < CityList.length; i++) {
				$("#city").append('<option value="' + CityList[i]["id"] + '">' + CityList[i]["city_name"] + '</option>');
			}
			$("#Permanentcity").children().remove();
			for (var i = 0; i < CityList.length; i++) {
				$("#Permanentcity").append('<option value="' + CityList[i]["id"] + '">' + CityList[i]["city_name"] + '</option>');
			}

			$('#city').selectpicker('val', data.city);
			$('#Permanentcity').selectpicker('val', data.Permanentcity);
			$('.selectpicker').selectpicker('refresh');

			$('#address1').val(data.current_address);
			$('#address2').val(data.home_town);
			$('#zip').val(data.pincode);
			$('#Pan').val(data.pan_number);
			$('#Aadhaarno').val(data.aadhar_number);
			$('#ifsc').val(data.ifsc);
			$('#account_number').val(data.account_number);
			$('#name_account').val(data.name_account);
			$('#issue_bank').val(data.issue_bank);
			$('#sex').selectpicker('val', data.sex);
			$('.selectpicker').selectpicker('refresh');
			$('#marital_status').selectpicker('val', data.marital_status);
			$('.selectpicker').selectpicker('refresh');
			if (data.birthday !== null) {
				var date = data.birthday.substring(0, 10);
				var date_new = date.split("-").reverse().join("/");
				$('#birthday').val(date_new);
			} else {
				$('#birthday').val('');
			}
			$('#app_access').selectpicker('val', data.app_access);
			$('.selectpicker').selectpicker('refresh');
			$('#literacy').selectpicker('val', data.literacy);
			$('.selectpicker').selectpicker('refresh');
			$('#DeviceID').val(data.DiveceID);
			if (data.StartDate == '' || data.StartDate == null) {
				$('#datecreated').val('');
			} else {
				var date = data.StartDate.substring(0, 10);
				var date_new = date.split("-").reverse().join("/");
				$('#datecreated').val(date_new);
			}
			$('#active').selectpicker('val', data.active);
			$('.selectpicker').selectpicker('refresh');
			$('#IsPF').selectpicker('val', data.IsPF);
			$('.selectpicker').selectpicker('refresh');
			$('#IsESIC').selectpicker('val', data.IsESIC);
			$('.selectpicker').selectpicker('refresh');

			$('#working_hour').val(data.working_hour);

			$('#EmpCode').val(data.EmpCode);
			$('#FatherName').val(data.FatherName);
			$('#MotherName').val(data.MotherName);
			$('#SpouseName').val(data.SpouseName);
			$('#EmergencyContact').val(data.EmergencyContact);
			$('#PermanentAddress').val(data.PermanentAddress);
			$('#PermanentAddressPincode').val(data.PermanentAddressPincode);
			$('#BloodGroup').val(data.BloodGroup);
			$('#NomineeName').val(data.NomineeName);
			if (data.NomineeDob == '' || data.NomineeDob == null) {
				$('#NomineeDob').val('');
			} else {
				var date = data.NomineeDob.substring(0, 10);
				var date_new = date.split("-").reverse().join("/");
				$('#NomineeDob').val(date_new);
			}
			if (data.DateOfLeaving == '' || data.DateOfLeaving == null) {
				$('#DateOfLeaving').val('');
			} else {
				var date = data.DateOfLeaving.substring(0, 10);
				var date_new = date.split("-").reverse().join("/");
				$('#DateOfLeaving').val(date_new);
			}
			if (data.LastWorkingDay == '' || data.LastWorkingDay == null) {
				$('#LastWorkingDay').val('');
			} else {
				var date = data.LastWorkingDay.substring(0, 10);
				var date_new = date.split("-").reverse().join("/");
				$('#LastWorkingDay').val(date_new);
			}
			$('#ResignationReason').val(data.ResignationReason);
			$('#TechnicalEduction').val(data.TechnicalEduction);
			$('#UAN_No').val(data.UAN_No);
			$('#ESI_No').val(data.ESI_No);
			$('#EmpType').val(data.EmpType);
			$('#Remark').val(data.Remark);
			$('#BranchAddress').val(data.BranchAddress);
			$('#BranchName').val(data.BranchName);
			$('#Permanentstate').val(data.Permanentstate);
			$('#Permanentcity').val(data.Permanentcity);
			$('#PermanentAddressline2').val(data.PermanentAddressline2);
			$('.selectpicker').selectpicker('refresh');


			$('.updateBtn').removeAttr('disabled');
			$('.saveBtn').removeAttr('disabled');
			$('.saveBtn').hide();
			$('.updateBtn').show();
			$('.saveBtn2').hide();
			$('.updateBtn2').show();
		}
		// On Blur ItemID Get All Date
		$('#AccountID').blur(function () {
			AccountID = "STF" + $(this).val();
			if (AccountID == '') {

			} else {
				$.ajax({
					url: "<?php echo admin_url(); ?>hr_profile/GetAccountDetailByID",
					dataType: "JSON",
					method: "POST",
					data: { AccountID: AccountID },
					beforeSend: function () {
						$('.searchh2').css('display', 'block');
						$('.searchh2').css('color', 'blue');
					},
					complete: function () {
						$('.searchh2').css('display', 'none');
					},
					success: function (data) {
						if (data == null) {
							ResetForm();
						} else {
							SetFormData(data);
						}
					}
				});
			}
		});



		// Save New Item
		$('.saveBtn').on('click', function () {
			AccountID = $('#AccountID').val();
			firstname = $('#firstname').val();
			lastname = $('#lastname').val();
			Department = $('#Department').val();
			designation = $('#designation').val();
			StaffType = $('#StaffType').val();
			reporting_to = $('#reporting_to').val();
			opening_b = $('#opening_b').val();
			phonenumber = $('#phonenumber').val();
			altphonenumber = $('#altphonenumber').val();
			email = $('#email').val();
			peremail = $('#peremail').val();
			state = $('#state').val();
			city = $('#city').val();
			address1 = $('#address1').val();
			address2 = $('#address2').val();
			zip = $('#zip').val();
			Pan = $('#Pan').val();
			Aadhaarno = $('#Aadhaarno').val();
			ifsc = $('#ifsc').val();
			issue_bank = $('#issue_bank').val();
			account_number = $('#account_number').val();
			name_account = $('#name_account').val();
			sex = $('#sex').val();
			marital_status = $('#marital_status').val();
			birthday = $('#birthday').val();
			literacy = $('#literacy').val();
			app_access = $('#app_access').val();
			DeviceID = $('#DeviceID').val();
			datecreated = $('#datecreated').val();
			active = $('#active').val();
			IsPF = $('#IsPF').val();
			IsESIC = $('#IsESIC').val();
			working_hour = $('#working_hour').val();
			password = $('#password').val();

			EmpCode = $('#EmpCode').val();
			FatherName = $('#FatherName').val();
			MotherName = $('#MotherName').val();
			SpouseName = $('#SpouseName').val();
			EmergencyContact = $('#EmergencyContact').val();
			PermanentAddress = $('#PermanentAddress').val();
			PermanentAddressPincode = $('#PermanentAddressPincode').val();
			BloodGroup = $('#BloodGroup').val();
			NomineeName = $('#NomineeName').val();
			NomineeDob = $('#NomineeDob').val();
			DateOfLeaving = $('#DateOfLeaving').val();
			LastWorkingDay = $('#LastWorkingDay').val();
			ResignationReason = $('#ResignationReason').val();
			TechnicalEduction = $('#TechnicalEduction').val();
			UAN_No = $('#UAN_No').val();
			ESI_No = $('#ESI_No').val();
			EmpType = $('#EmpType').val();
			Remark = $('#Remark').val();
			BranchName = $('#BranchName').val();
			BranchAddress = $('#BranchAddress').val();
			Permanentstate = $('#Permanentstate').val();
			Permanentcity = $('#Permanentcity').val();
			PermanentAddressline2 = $('#PermanentAddressline2').val();
			if (AccountID == '') {
				alert('please enter AccountID');
				$('#AccountID').focus();
				$('.saveBtn').removeAttr('disabled');
			} else if (firstname == '') {
				alert('Please Enter Firstname');
				$('.saveBtn').removeAttr('disabled');
				$('#lastname').focus();
			} else if (lastname == '') {
				alert('Please Enter Lastname');
				$('.saveBtn').removeAttr('disabled');
				$('#lastname').focus();
			} else if (Department == '') {
				alert('Please select Department');
				$('.saveBtn').removeAttr('disabled');
				$('#Department').focus();
			} else if (designation == '') {
				alert('Please Select Designation');
				$('.saveBtn').removeAttr('disabled');
				$('#designation').focus();
			} else if (StaffType == '') {
				alert('Please Select Staff Type');
				$('.saveBtn').removeAttr('disabled');
				$('#StaffType').focus();
			} else if (reporting_to == '') {
				alert('Please Select Reporting To');
				$('.saveBtn').removeAttr('disabled');
				$('#reporting_to').focus();
			} else if (state == '') {
				alert('Please select current state');
				$('.saveBtn').removeAttr('disabled');
				$('#state').focus();
			} else if (city == '') {
				alert('Please select current city');
				$('.saveBtn').removeAttr('disabled');
				$('#city').focus();
			} else if (Permanentstate == '') {
				alert('Please select permanent state');
				$('.saveBtn').removeAttr('disabled');
				$('#Permanentstate').focus();
			} else if (Permanentcity == '') {
				alert('Please select permanent City');
				$('.saveBtn').removeAttr('disabled');
				$('#Permanentcity').focus();
			} else if (phonenumber == '') {
				alert('please  enter mobile number');
				$('.saveBtn').removeAttr('disabled');
				$('#phonenumber').focus();
			} else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
				alert('Enter valid Mobile number');
				$('.saveBtn').removeAttr('disabled');
				$('#phonenumber').focus();
			} else if (!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#Pan').val() !== "") {
				alert('Enter valid PAN number');
				$('.saveBtn').removeAttr('disabled');
				$('#Pan').focus();
			} else if (!$('#Aadhaarno').val().match('[0-9]{12}') && $('#Aadhaarno').val() !== "") {
				alert('Enter valid Aadhar number');
				$('.saveBtn').removeAttr('disabled');
				$('#Aadhaarno').focus();
			} else if (EmpType == '') {
				alert('please  Select Employee Type');
				$('.saveBtn').removeAttr('disabled');
				$('#EmpType').focus();
			} else {
				$.ajax({
					url: "<?php echo admin_url(); ?>hr_profile/SaveAccountID",
					dataType: "JSON",
					method: "POST",
					data: {
						AccountID: AccountID, firstname: firstname, lastname: lastname, Department: Department, designation: designation, StaffType: StaffType,
						reporting_to: reporting_to, opening_b: opening_b, phonenumber: phonenumber, altphonenumber: altphonenumber, email: email, peremail: peremail,
						state: state, city: city, address1: address1, address2: address2, zip: zip, Pan: Pan, Aadhaarno: Aadhaarno,
						ifsc: ifsc, issue_bank: issue_bank, account_number: account_number, name_account: name_account, sex: sex, marital_status: marital_status,
						birthday: birthday, literacy: literacy, active: active, working_hour: working_hour, app_access: app_access, datecreated: datecreated, IsPF: IsPF, IsESIC: IsESIC,
						DeviceID: DeviceID, password: password, EmpCode: EmpCode, FatherName: FatherName, MotherName: MotherName, SpouseName: SpouseName, EmergencyContact: EmergencyContact, PermanentAddress: PermanentAddress, PermanentAddressPincode: PermanentAddressPincode, BloodGroup: BloodGroup, NomineeName: NomineeName, NomineeDob: NomineeDob, DateOfLeaving: DateOfLeaving, LastWorkingDay: LastWorkingDay, ResignationReason: ResignationReason, TechnicalEduction: TechnicalEduction, UAN_No: UAN_No, ESI_No: ESI_No, EmpType: EmpType, Remark: Remark, BranchAddress: BranchAddress, BranchName: BranchName, Permanentstate: Permanentstate, Permanentcity: Permanentcity, PermanentAddressline2: PermanentAddressline2
					},
					beforeSend: function () {
						$('.searchh3').css('display', 'block');
						$('.searchh3').css('color', 'blue');
					},
					complete: function () {
						$('.searchh3').css('display', 'none');
					},
					success: function (data) {
						if (data) {
							alert('Record created successfully...');
							$('#hidden_staff_code').val(data);
							ResetForm();
						} else {
							$('.saveBtn').removeAttr('disabled');
							alert_float('warning', 'Something went wrong...');
							ResetForm();
						}
					}
				});
			}

		});
		// Update Exiting Item
		$('.updateBtn').on('click', function () {
			userID = $('#userid').val();
			AccountID = $('#AccountID').val();
			firstname = $('#firstname').val();
			lastname = $('#lastname').val();
			Department = $('#Department').val();
			designation = $('#designation').val();
			StaffType = $('#StaffType').val();
			reporting_to = $('#reporting_to').val();
			opening_b = $('#opening_b').val();
			phonenumber = $('#phonenumber').val();
			altphonenumber = $('#altphonenumber').val();
			email = $('#email').val();
			peremail = $('#peremail').val();
			state = $('#state').val();
			city = $('#city').val();
			address1 = $('#address1').val();
			address2 = $('#address2').val();
			zip = $('#zip').val();
			Pan = $('#Pan').val();
			Aadhaarno = $('#Aadhaarno').val();
			ifsc = $('#ifsc').val();
			issue_bank = $('#issue_bank').val();
			account_number = $('#account_number').val();
			name_account = $('#name_account').val();
			sex = $('#sex').val();
			marital_status = $('#marital_status').val();
			birthday = $('#birthday').val();
			literacy = $('#literacy').val();
			app_access = $('#app_access').val();
			DeviceID = $('#DeviceID').val();
			datecreated = $('#datecreated').val();
			active = $('#active').val();
			IsPF = $('#IsPF').val();
			IsESIC = $('#IsESIC').val();
			working_hour = $('#working_hour').val();
			password = $('#password').val();

			EmpCode = $('#EmpCode').val();
			FatherName = $('#FatherName').val();
			MotherName = $('#MotherName').val();
			SpouseName = $('#SpouseName').val();
			EmergencyContact = $('#EmergencyContact').val();
			PermanentAddress = $('#PermanentAddress').val();
			PermanentAddressPincode = $('#PermanentAddressPincode').val();
			BloodGroup = $('#BloodGroup').val();
			NomineeName = $('#NomineeName').val();
			NomineeDob = $('#NomineeDob').val();
			DateOfLeaving = $('#DateOfLeaving').val();
			LastWorkingDay = $('#LastWorkingDay').val();
			ResignationReason = $('#ResignationReason').val();
			TechnicalEduction = $('#TechnicalEduction').val();
			UAN_No = $('#UAN_No').val();
			ESI_No = $('#ESI_No').val();
			EmpType = $('#EmpType').val();
			Remark = $('#Remark').val();
			BranchAddress = $('#BranchAddress').val();
			BranchName = $('#BranchName').val();
			Permanentstate = $('#Permanentstate').val();
			Permanentcity = $('#Permanentcity').val();
			PermanentAddressline2 = $('#PermanentAddressline2').val();

			if (AccountID == '') {
				alert('please enter AccountID');
				$('#AccountID').focus();
				$('.updateBtn').removeAttr('disabled');
			} else if (firstname == '') {
				alert('Please Enter Firstname');
				$('.updateBtn').removeAttr('disabled');
				$('#lastname').focus();
			} else if (lastname == '') {
				alert('Please Enter Lastname');
				$('.updateBtn').removeAttr('disabled');
				$('#lastname').focus();
			} else if (Department == '') {
				alert('Please select Department');
				$('.updateBtn').removeAttr('disabled');
				$('#Department').focus();
			} else if (designation == '') {
				alert('Please Select Designation');
				$('.updateBtn').removeAttr('disabled');
				$('#designation').focus();
			} else if (StaffType == '') {
				alert('Please Select Staff Type');
				$('.updateBtn').removeAttr('disabled');
				$('#StaffType').focus();
			} else if (reporting_to == '') {
				alert('Please Select Reporting To');
				$('.updateBtn').removeAttr('disabled');
				$('#reporting_to').focus();
			} else if (state == '') {
				alert('please select current State');
				$('.updateBtn').removeAttr('disabled');
				$('#state').focus();
			} else if (city == '') {
				alert('please select current City');
				$('.updateBtn').removeAttr('disabled');
				$('#city').focus();
			} else if (Permanentstate == '') {
				alert('Please select permanent state');
				$('.updateBtn').removeAttr('disabled');
				$('#Permanentstate').focus();
			} else if (Permanentcity == '') {
				alert('Please select permanent City');
				$('.updateBtn').removeAttr('disabled');
				$('#Permanentcity').focus();
			} else if (phonenumber == '') {
				alert('please  enter mobile number');
				$('.updateBtn').removeAttr('disabled');
				$('#phonenumber').focus();
			} else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
				alert('Enter valid Mobile number');
				$('.updateBtn').removeAttr('disabled');
				$('#phonenumber').focus();
			} else if (!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#Pan').val() !== "") {
				alert('Enter valid PAN number');
				$('.updateBtn').removeAttr('disabled');
				$('#Pan').focus();
			} else if (!$('#Aadhaarno').val().match('[0-9]{12}') && $('#Aadhaarno').val() !== "") {
				alert('Enter valid Aadhar number');
				$('.updateBtn').removeAttr('disabled');
				$('#Aadhaarno').focus();
			} else if (EmpType == '') {
				alert('please  Select Employee Type');
				$('.updateBtn').removeAttr('disabled');
				$('#EmpType').focus();
			} else {
				$.ajax({
					url: "<?php echo admin_url(); ?>hr_profile/UpdateAccountID",
					dataType: "JSON",
					method: "POST",
					data: {
						userID: userID, AccountID: AccountID, firstname: firstname, lastname: lastname, Department: Department, designation: designation, StaffType: StaffType,
						reporting_to: reporting_to, opening_b: opening_b, phonenumber: phonenumber, altphonenumber: altphonenumber, email: email, peremail: peremail,
						state: state, city: city, address1: address1, address2: address2, zip: zip, Pan: Pan, Aadhaarno: Aadhaarno,
						ifsc: ifsc, issue_bank: issue_bank, account_number: account_number, name_account: name_account, sex: sex, marital_status: marital_status,
						birthday: birthday, literacy: literacy, active: active, working_hour: working_hour, app_access: app_access, datecreated: datecreated, IsESIC: IsESIC, IsPF: IsPF,
						DeviceID: DeviceID, password: password, EmpCode: EmpCode, FatherName: FatherName, MotherName: MotherName, SpouseName: SpouseName, EmergencyContact: EmergencyContact, PermanentAddress: PermanentAddress, PermanentAddressPincode: PermanentAddressPincode, BloodGroup: BloodGroup, NomineeName: NomineeName, NomineeDob: NomineeDob, DateOfLeaving: DateOfLeaving, LastWorkingDay: LastWorkingDay, ResignationReason: ResignationReason, TechnicalEduction: TechnicalEduction, UAN_No: UAN_No, ESI_No: ESI_No, EmpType: EmpType, Remark: Remark, BranchAddress: BranchAddress, BranchName: BranchName, Permanentstate: Permanentstate, Permanentcity: Permanentcity, PermanentAddressline2: PermanentAddressline2
					},
					beforeSend: function () {
						$('.searchh4').css('display', 'block');
						$('.searchh4').css('color', 'blue');
					},
					complete: function () {
						$('.searchh4').css('display', 'none');
					},
					success: function (data) {
						if (data == true) {
							alert('Record updated successfully...');
							ResetForm();
						} else {
							alert('warning', 'there is no changes');
							$('.updateBtn').removeAttr('disabled');
							ResetForm();
						}
					}
				});
			}

		});

		$('#state').on('change', function () {
			var StateID = $(this).val();
			var url = "<?php echo base_url(); ?>admin/clients/GetCity";
			jQuery.ajax({
				type: 'POST',
				url: url,
				data: { StateID: StateID },
				dataType: 'json',
				success: function (data) {
					$("#city").find('option').remove();
					$("#city").selectpicker("refresh");
					for (var i = 0; i < data.length; i++) {
						$("#city").append(new Option(data[i].city_name, data[i].id));
					}
					$('.selectpicker').selectpicker('refresh');
				}
			});
		});

		$('#Permanentstate').on('change', function () {
			var StateID = $(this).val();
			var url = "<?php echo base_url(); ?>admin/clients/GetCity";
			jQuery.ajax({
				type: 'POST',
				url: url,
				data: { StateID: StateID },
				dataType: 'json',
				success: function (data) {
					$("#Permanentcity").find('option').remove();
					$("#Permanentcity").selectpicker("refresh");
					for (var i = 0; i < data.length; i++) {
						$("#Permanentcity").append(new Option(data[i].city_name, data[i].id));
					}
					$('.selectpicker').selectpicker('refresh');
				}
			});
		});



		var app_access = $("#app_access").val();
		var erp_access = $("#login_access").val();
		if (app_access == "No") {
			$("#password_field").css("display", "none");
		}
		$('#app_access').on('change', function () {
			var app_access = $(this).val();
			if (app_access == "Yes") {
				$("#password_field").css("display", "");
			} else {
				$//("").css("display", "inline-table");
				$("#password_field").css("display", "none");
			}
		});

	</script>

	<script>
		function myFunction2() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("myInput1");
			filter = input.value.toUpperCase();
			table = document.getElementById("table_Account_List");
			tr = table.getElementsByTagName("tr");
			for (i = 1; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[0];
				td1 = tr[i].getElementsByTagName("td")[1];
				td2 = tr[i].getElementsByTagName("td")[2];
				td3 = tr[i].getElementsByTagName("td")[3];
				td4 = tr[i].getElementsByTagName("td")[4];
				td5 = tr[i].getElementsByTagName("td")[5];
				if (td) {
					txtValue = td.textContent || td.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
					} else if (td1) {
						txtValue = td1.textContent || td1.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
						} else if (td2) {
							txtValue = td2.textContent || td2.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
							} else if (td3) {
								txtValue = td3.textContent || td3.innerText;
								if (txtValue.toUpperCase().indexOf(filter) > -1) {
									tr[i].style.display = "";
								} else if (td4) {
									txtValue = td4.textContent || td4.innerText;
									if (txtValue.toUpperCase().indexOf(filter) > -1) {
										tr[i].style.display = "";

									} else if (td5) {
										txtValue = td5.textContent || td5.innerText;
										if (txtValue.toUpperCase().indexOf(filter) > -1) {
											tr[i].style.display = "";

										} else {
											tr[i].style.display = "none";
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$(document).on("click", ".sortablePop", function () {
			var table = $("#table_Account_List tbody");
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
	<script>
		function validateZipCode(elementValue) {
			var zipCodePattern = /^\d{5}$|^\d{5}-\d{4}$/;
			return zipCodePattern.test(elementValue);
		}
	</script>
	<script>
		function isNumber(evt) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode = 46 && charCode > 31
				&& (charCode < 48 || charCode > 57)) {
				return false;
			}
			return true;
		}
	</script>

	<script type="text/javascript">
		$('#MaxCrdAmt,#kms,.opening_bal').on('keypress', function (event) {
			if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
				event.preventDefault();
			}
			var input = $(this).val();
			if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
				event.preventDefault();
			}
		});
	</script>
	<script>
		$(function () {
			$('#birthday, #NomineeDob, #datecreated, #DateOfLeaving, #LastWorkingDay')
				.attr('placeholder', 'dd/mm/yyyy');
		});
	</script>

	<style>
		.btn-bottom-toolbar {
			width: 98% !important;
			margin-left: -20px;
		}

		#AccountID {
			text-transform: uppercase;
		}

		#Pan {
			text-transform: uppercase;
		}

		#vat {
			text-transform: uppercase;
		}

		#table_Account_List td:hover {
			cursor: pointer;
		}

		#table_Account_List tr:hover {
			background-color: #ccc;
		}

		.itemdivisioncomp .btn-default {
			height: 25px !important;
			padding: 0px 10px !important;
			font-size: 12px !important;
		}

		.table-Account_List {
			overflow: auto;
			max-height: 65vh;
			width: 100%;
			position: relative;
			top: 0px;
		}

		.table-Account_List thead th {
			position: sticky;
			top: 0;
			z-index: 1;
		}

		.table-Account_List tbody th {
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
		.p_style{
			margin-top: 0px;
			margin-bottom: 5px;
			color: #d81b60;
		}
	</style>