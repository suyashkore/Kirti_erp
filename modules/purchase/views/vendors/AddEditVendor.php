<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper"style="min-height:1px">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb"
                                style="background-color:#fff !important; margin-Bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i
                                                class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Vendor Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                                <div class="searchh3" style="display:none;">Please wait Create new Vendor...</div>
                                <div class="searchh4" style="display:none;">Please wait update Vendor...</div>
                            </div>
                        </div>

                        <!-- Vendor Form Start -->
                        <form id="manage-vendor-form" method="post" enctype="multipart/form-data" action="<?= admin_url('purchase/AddEditVendor' . (isset($vendor) && !empty($vendor->userid) ? '/' . $vendor->userid : '')) ?>">
                        <?php if (isset($vendor) && !empty($vendor->userid)) { ?>
                            <input type="hidden" name="vendor_id" value="<?= $vendor->userid ?>">
                        <?php } ?>

                        <!-- Top Fields -->
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label"><small class="req text-danger">* </small>Vendor
                                        Category</label>
                                    <select class="selectpicker form-control" id="vendor_type"
                                        data-none-selected-text="None selected" name="vendor_type" data-width="100%"
                                        data-live-search="true" title="Select Vendor Category">
                                        <option value=""></option>
                                        <?php foreach ($VendorType as $type) { ?>
                                        <option value="<?php echo $type['SubActGroupID']; ?>">
                                            <?php echo $type['SubActGroupName']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label"><small class="req text-danger">* </small>Vendor
                                        Code</label>
                                    <input type="text" class="form-control" id="AccountID" name="AccountID" readonly>
                                    <input type="hidden" id="HiddenVendorCode" name="HiddenVendorCode">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"><small class="req text-danger">* </small>Vendor
                                        Name</label>
                                    <input type="text" class="form-control" id="AccountName" name="AccountName"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"><small class="req text-danger">* </small>Favouring
                                        Name</label>
                                    <input type="text" class="form-control" id="favouring_name" name="favouring_name">
                                </div>
                            </div>
                        </div>

                        <!-- Tabs -->
                        <div class="row">
                            <div class="col-md-12">
                                <!-- tabs removed: showing all sections sequentially -->

                                <div class="tab-content">
                                    <!-- GENERAL TAB -->
                                    <div role="tabpanel" class="tab-pane active" id="general">
                                        <br>
                                        <!-- Billing Information -->
                                        <h4 class="bold p_style">Billing Information</h4>
                                        <hr class="hr_style">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>PAN No</label>
                                                    <input type="text" class="form-control" id="pan" name="pan"
                                                        style="text-transform:uppercase;">
                                                    <span class="pan_denger" style="color:red;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>GSTIN</label>
                                                    <input type="text" class="form-control" id="vat" name="vat"
                                                        maxlength="15">
                                                    <span class="gst_denger" style="color:red;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Organisation Type</label>
                                                    <select class="selectpicker form-control" id="organisation_type"
                                                        name="organisation_type" data-width="100%"
                                                        data-live-search="true" data-none-selected-text="None selected">
                                                        <option value=""></option>
                                                        <option value="Proprietorship">Proprietorship</option>
                                                        <option value="Partnership">Partnership</option>
                                                        <option value="Partnership Firm">Partnership Firm</option>
                                                        <option value="Limited Liability Partnership (LLP)">Limited
                                                            Liability Partnership (LLP)</option>
                                                        <option value="Private Limited">Private Limited</option>
                                                        <option value="Public Limited">Public Limited</option>
                                                        <option value="One Person Company (OPC)">One Person Company
                                                            (OPC)</option>
                                                        <option value="Hindu Undivided Family (HUF)">Hindu Undivided
                                                            Family (HUF)
                                                        </option>
                                                        <option value="Society / Trust / Club">Society / Trust / Club
                                                        </option>
                                                        <option value="Government Department/Body">Government
                                                            Department/Body</option>
                                                        <option value="Local Authority">Local Authority</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">GST Category</label>
                                                    <select class="selectpicker form-control" id="gst_type"
                                                        name="gst_type" data-live-search="true"
                                                        data-none-selected-text="None selected" data-width="100%">
                                                        <option value=""></option>
                                                        <option value="1">Registered</option>
                                                        <option value="2">Un-Registered</option>
                                                        <option value="3">Composition</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>Billing Country</label>
                                                    <select class="selectpicker form-control" id="billing_country"
                                                        name="billing_country" data-width="100%" data-live-search="true"
                                                        data-none-selected-text="None selected">
                                                        <option value=""></option>
                                                        <?php foreach ($country as $co) { ?>
                                                        <option value="<?php echo $co['country_id']; ?>">
                                                            <?php echo $co['long_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>Billing State</label>
                                                    <select class="selectpicker form-control" id="billing_state" name="billing_state"
                                                        data-width="100%" data-live-search="true"
                                                        data-none-selected-text="None selected">
                                                        <option value=""></option>
                                                        <?php foreach ($state as $st) { ?>
                                                        <option value="<?php echo $st['short_name']; ?>">
                                                            <?php echo $st['state_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>Billing City</label>
                                                    <select class="selectpicker form-control" id="billing_city" name="billing_city"
                                                        data-width="100%" data-live-search="true"
                                                        data-none-selected-text="None selected">
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>Billing Pincode</label>
                                                    <input type="text" class="form-control" id="billing_zip" name="billing_zip"
                                                        onkeypress="return isNumber(event)" maxlength="6">
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>Mobile</label>
                                                    <input type="text" class="form-control" id="phonenumber"
                                                        name="phonenumber" onkeypress="return isNumber(event)"
                                                        maxlength="10">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Telephone</label>
                                                    <input type="text" class="form-control" id="telephone"
                                                        name="telephone">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label"><small class="req text-danger">*
                                                        </small>Email</label>
                                                    <input type="email" class="form-control" id="email" name="email">
                                                    <span class="email_error" style="color:red;"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">TDS</label>
                                                    <select class="selectpicker form-control" id="Tds" name="Tds"
                                                        data-width="100%">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <!-- Billing Address moved to end of Billing Information -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Billing Address</label>
                                                    <textarea class="form-control" id="billing_address" name="billing_address"
                                                        rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="TdsSec" style="display:none;">
                                                <div class="form-group">
                                                    <label class="control-label">TDS Section</label>
                                                    <select class="selectpicker form-control" id="Tdsselection"
                                                        name="Tdsselection" data-width="100%">
                                                        <option value="">Non Selected</option>
                                                        <?php if(isset($Tdssection)){ foreach($Tdssection as $w): ?>
                                                        <option value="<?= $w['TDSCode']?>"><?= $w['TDSName']?></option>
                                                        <?php endforeach; } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="TdsPercent1" style="display:none;">
                                                <div class="form-group">
                                                    <label class="control-label">TDS Rate (%)</label>
                                                    <select class="selectpicker form-control" id="TdsPercent"
                                                        name="TdsPercent" data-width="100%">
                                                        <option value="">Non Selected</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Contact Details Section -->
                                        <h4 class="bold p_style">Contact Details</h4>
                                        <hr class="hr_style">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="contactTable">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Designation</th>
                                                        <th>Mobile Number</th>
                                                        <th>Email</th>
                                                        <th>Send SMS</th>
                                                        <th>Send Email</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                        <td><input type="text" class="form-control"
                                                            name="cp_name[]"></td>
                                                        <td>
                                                            <select class="selectpicker form-control cp_designation_input" id="cp_designation" name="cp_designation"
                                                                data-width="100%">
                                                                <option value="">Non Selected</option>
                                                                <?php foreach ($position as $key => $value) { ?>
                                                                <option value="<?php echo $value['position_id']; ?>">
                                                                    <?php echo $value['position_name']; ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control" maxlength="10"
                                                            onkeypress="return isNumber(event)"
                                                            name="cp_mobile[]"></td>
                                                        <td><input type="email" class="form-control"
                                                            name="cp_email[]"></td>
                                                        <td class="text-center"><input type="checkbox"
                                                            name="cp_send_sms[]" value="1"></td>
                                                        <td class="text-center"><input type="checkbox"
                                                            name="cp_send_email[]" value="1"></td>
                                                        <td><button type="button"
                                                            class="btn btn-success addContactRow"><i
                                                                class="fa fa-plus"></i></button></td>
                                                        </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Location Table -->
                                        <h4 class="bold p_style">Location Information</h4>
                                        <hr class="hr_style">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="locationTable">
                                                <thead>
                                                    <tr>
                                                        <th>Pincode</th>
                                                        <th>Address</th>
                                                        <th>State</th>
                                                        <th>City</th>
                                                        <th>Mobile</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td><input type="text" class="form-control"
                                                                name="loc_pincode[]"></td>
                                                        <td><textarea class="form-control" name="loc_address[]"
                                                                rows="1"></textarea></td>
                                                        <td>
                                                            <select class="form-control loc_state selectpicker"
                                                                name="loc_state[]">
                                                                <option value="">Select State</option>
                                                                <?php foreach ($state as $st) { ?>
                                                                <option value="<?php echo $st['short_name']; ?>">
                                                                    <?php echo $st['state_name']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control loc_city selectpicker"
                                                                name="loc_city[]" data-live-search="true"
                                                                data-width="100%">
                                                                <option value="">Select City</option>
                                                            </select>
                                                        </td>

                                                        <td><input type="text" class="form-control" name="loc_mobile[]">
                                                        </td>
                                                        <td><button type="button"
                                                                class="btn btn-success addLocationRow"><i
                                                                    class="fa fa-plus"></i></button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Accounting Information -->
                                        <div id="banking_payment_other_wrapper">

                                            <!-- Banking & Payment / Other Information -->
                                            <h4 class="bold p_style">Banking / Payment & Other Information</h4>
                                            <hr class="hr_style">

                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Is Bank Detail</label>
                                                        <select name="is_bank_detail" id="is_bank_detail"
                                                            class="form-control selectpicker">
                                                            <option value="0" selected>No</option>
                                                            <option value="1">Yes</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 bank_detail_section" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">IFSC Code</label>
                                                        <input type="text" name="ifsc_code" id="ifsc_code"
                                                            class="form-control" maxlength="11">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 bank_detail_section" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">Bank Name</label>
                                                        <input type="text" name="bank_name" id="bank_name"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 bank_detail_section" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">Branch Name</label>
                                                        <input type="text" name="bank_branch" id="bank_branch"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 bank_detail_section" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">Bank Address</label>
                                                        <input type="text" name="bank_address" id="bank_address"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 bank_detail_section" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">Account Number</label>
                                                        <input type="text" name="account_number" id="account_number"
                                                            class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-2 bank_detail_section" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">Account Holder Name</label>
                                                        <input type="text" name="account_holder_name"
                                                            id="account_holder_name" class="form-control" >
                                                    </div>
                                                </div>

                                                <!-- Credit & Payment Control -->
                                                <!-- <div role="tabpanel" class="tab-pane active" id="credit_payment">
        <br>
        <div class="row"> -->
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Payment Terms</label>
                                                        <select class="selectpicker form-control" id="pay_term"
                                                            data-live-search="true"
                                                            data-none-selected-text="None selected" name="pay_term">
                                                            <option value=""></option>
                                                            <option value="Credit">Credit</option>
                                                            <option value="Advance">Advance</option>
                                                            <option value="OnDelivery">On Delivery</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Payment Cycle Type</label>
                                                        <select class="selectpicker form-control"
                                                            data-live-search="true"
                                                            data-none-selected-text="None selected"
                                                            id="payment_cycle_type" name="payment_cycle_type">
                                                            <option value=""></option>
                                                             <option value="Weekly">Weekly</option>
                                                            <option value="Monthly">Monthly</option>
                                                            <option value="Due Date">Due Date</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Payment Cycle</label>
                                                        <select class="selectpicker form-control" id="payment_cycle"
                                                            data-live-search="true"
                                                            data-none-selected-text="None selected"
                                                            name="payment_cycle">
                                                            <option value=""></option>
                                                             <option value="Due Date">Due Date</option>
                                                            <option value="Document Date">Document Date</option>
                                                            <option value="Posting Date">Posting Date</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Grace Days</label>
                                                        <input type="text" class="form-control" id="credit_days"
                                                            name="credit_days" value="0">
                                                    </div>
                                                </div>
                                                <!-- </div> -->
                                                <!-- </div> -->

                                                <!-- Other Information -->
                                                <!-- <div role="tabpanel" class="tab-pane active" id="other_info"> -->
                                                <!-- <br> -->

                                                <!-- <div class="row"> -->
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Website</label>
                                                        <input type="text" class="form-control" id="website"
                                                            name="website">
                                                    </div>
                                                </div>

                                                     <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="control-label">Freight Terms</label>
                                                            <select class="selectpicker form-control" id="freight_terms" name="freight_terms"
                                                                data-live-search="true" data-none-selected-text="None selected">
                                                                <option value=""></option>
                                                                <?php foreach ($FreightTerms as $key => $value) { ?>
                                                                <option value="<?php echo $value['Id']; ?>">
                                                                    <?php echo $value['FreightTerms']; ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">FSSAI Lic No.</label>
                                                        <input type="text" class="form-control" id="food_lic_n"
                                                            name="food_lic_n">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Priority</label>
                                                        <select class="selectpicker form-control" id="priority" name="priority"
                                                            data-live-search="true" data-none-selected-text="None selected">
                                                            <option value=""></option>
                                                            <?php foreach ($Priority as $key => $value) { ?>
                                                                <option value="<?php echo $value['id']; ?>">
                                                                <?php echo $value['PriorityName']; ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- </div> -->

                                              <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label"><small class="req text-danger">*
                                                            </small>Territory</label>
                                                        <select name="territory" id="territory" class="selectpicker form-control"
                                                            data-live-search="true" data-none-selected-text="None selected" required>
                                                            <option value=""></option>
                                                            <?php foreach ($Territory as $key => $value) { ?>
                                                            <option value="<?php echo $value['Id']; ?>">
                                                                <?php echo $value['TerritoryDescription']; ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Attachment</label>
                                                        <input type="file" class="form-control" id="attachment"
                                                            name="attachment">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Is Active ?</label>
                                                        <select name="blocked" class="selectpicker form-control">
                                                            <option value="1">Yes</option>
                                                            <option value="0">NO</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Additional Information</label>
                                                        <textarea class="form-control" id="additional_info"
                                                            name="additional_info" rows="2"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr>
                                            <!-- </div> -->

                                        </div>

                                        <!-- <div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Created by</label>
													<input type="text" class="form-control" id="created_by" readonly>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Created on</label>
													<input type="text" class="form-control" id="created_on" readonly>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Last Modified by</label>
													<input type="text" class="form-control" id="modified_by" readonly>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label class="control-label">Last Modified on</label>
													<input type="text" class="form-control" id="modified_on" readonly>
												</div>
											</div>
										</div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons (bottom toolbar) -->
                        <br>
                        <div class="row">
                            <div class="btn-bottom-toolbar text-right" style="left:-214px">
                                <div class="col-md-12">
                                    <div class="action-buttons"  >
                                        <?php if (has_permission('vendors', '', 'create')) { ?>
                                        <button type="submit" class="btn btn-success saveBtn" formaction="<?= admin_url('purchase/AddEditVendor') ?>">
                                            <i class="fa fa-save"></i> Save
                                        </button>
                                        <?php } ?>
                                        <?php if (has_permission('vendors', '', 'edit')) { ?>
                                        <button type="submit" class="btn btn-success updateBtn" style="display:none;" formaction="<?= admin_url('purchase/AddEditVendor/' . (isset($vendor) ? $vendor->userid : '')) ?>">
                                            <i class="fa fa-save"></i> Update
                                        </button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-warning cancelBtn">
                                            <i class="fa fa-refresh"></i> Reset
                                        </button>
                                        <!-- Show All button: opens Vendor List and fetches all vendors -->
                                        <button type="button" class="btn btn-info showAllBtn" title="Show All Vendors">
                                            <i class="fa fa-list"></i> Show All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form><!-- End Vendor Form -->

                        <!-- Vendor List Modal -->
                        <div class="modal fade Vendor_List" id="Vendor_List" tabindex="-1" role="dialog"
                            data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="padding:5px 10px;">
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Vendor List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">

                                        <div class="table-Vendor_List tableFixHead2">
                                            <table
                                                class="tree table table-striped table-bordered table-Vendor_List tableFixHead2"
                                                id="table_Vendor_List" width="100%">
                                                <thead>
                                                     <tr>
                                                            <th style="text-align:left;" class="sortablePop">Vendor
                                                                Code</th>
                                                            <th style="text-align:left;" class="sortablePop">Vendor Name</th>
                                                            <th style="text-align:left;" class="sortablePop">Favouring Name
                                                            </th>
                                                            <th style="text-align:left;" class="sortablePop">PAN NO 
                                                            </th>
                                                            <th style="text-align:left;" class="sortablePop">GSTIN                                                                Type</th>
                                                            <th style="text-align:left;" class="sortablePop">Organisation Type</th>
                                                            <th style="text-align:left;" class="sortablePop">GST Type</th>
                                                            <th style="text-align:left;" class="sortablePop">Status</th>
                                                        </tr>
                                                </thead>
                                                <tbody id="vendorlistbody">
                                                    <?php
													if (!empty($table_data)) {
														foreach ($table_data as $key => $value) {
														?>
                                                    <tr class="get_AccountID" style="cursor:pointer;"
                                                        data-id="<?php echo $value["AccountID"]; ?>">
                                                        <td><?php echo $value['AccountID']; ?></td>
                                                        <td><?php echo $value['company']; ?></td>
                                                        <td><?php echo isset($value['FavouringName']) ? $value['FavouringName'] : ''; ?></td>
                                                        <td><?php echo isset($value['PAN']) ? $value['PAN'] : (isset($value['Pan']) ? $value['Pan'] : ''); ?></td>
                                                        <td><?php echo isset($value['GSTIN']) ? $value['GSTIN'] : (isset($value['vat']) ? $value['vat'] : ''); ?></td>
                                                        <td><?php echo isset($value['organisation_type']) ? $value['organisation_type'] : ''; ?></td>
                                                        <td><?php echo isset($value['gst_type']) ? $value['gst_type'] : ''; ?></td>
                                                        <td><?php
																if ($value['active'] == '1') {
																	$status = 'Active';
																} else {
																	$status = 'Inactive';
																}
																echo $status; ?></td>
                                                    </tr>
                                                    <?php 
														}
													}
													?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="padding:0px;">
                                        <input type="text" id="myInput1" onkeyup="myFunction2()"
                                            placeholder="Search for names.." title="Type in a name"
                                            style="float: left;width: 100%;">
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
// Initialize position data for dynamic row creation
var positionData = <?php echo json_encode($position); ?>;

function getPositionOptions(selectedPosition = '') {
    let html = '<option value="">Non Selected</option>';
    let selectedId = '';
    let selectedName = '';

    if (selectedPosition) {
        // selectedPosition can be either id or name; find matching record
        positionData.forEach(function(pos) {
            if (pos.position_id == selectedPosition || pos.position_name == selectedPosition) {
                selectedId = pos.position_id;
                selectedName = pos.position_name;
            }
        });

        if (selectedId) {
            html = '<option value="' + selectedId + '" selected>' + selectedName + '</option>';
        }
    }

    // Add all positions (skip already-added selectedId)
    positionData.forEach(function(pos) {
        if (pos.position_id != selectedId) {
            html += '<option value="' + pos.position_id + '">' + pos.position_name + '</option>';
        }
    });
    return html;
}

// Dynamic Rows Logic + Generic Validation
// Add/Remove dynamic contact rows (inputs in new rows are required by default)
$(document).on('click', '.addContactRow', function() {
    var newRow = `<tr>
            <td><input type="text" class="form-control" name="cp_name[]" data-dyn="1"></td>
            <td><select class="selectpicker form-control cp_designation_input" name="cp_designation[]" data-width="100%" data-dyn="1">` + getPositionOptions() + `</select></td>
            <td><input type="text" class="form-control" maxlength="10" onkeypress="return isNumber(event)" name="cp_mobile[]" data-dyn="1"></td>
            <td><input type="email" class="form-control" name="cp_email[]" data-dyn="1"></td>
            <td class="text-center"><input type="checkbox" name="cp_send_sms[]" value="1"></td>
            <td class="text-center"><input type="checkbox" name="cp_send_email[]" value="1"></td>
            <td><button type="button" class="btn btn-danger removeRow"><i class="fa fa-trash"></i></button></td>
        </tr>`;
    $('#contactTable tbody').append(newRow);
});

// Add/Remove dynamic location rows (inputs in new rows are required by default)
$(document).on('click', '.addLocationRow', function() {
    var stateOptions =
        `<?php foreach ($state as $st) { ?><option value="<?php echo $st['short_name']; ?>"><?php echo $st['state_name']; ?></option><?php } ?>`;
    var newRow = `<tr>
                <td><input type="text" class="form-control" name="loc_pincode[]" data-dyn="1"></td>

            <td><textarea class="form-control " name="loc_address[]" rows="1" data-dyn="1"></textarea></td>
            <td>
                <select class="form-control loc_state selectpicker" name="loc_state[]" data-dyn="1">
                    <option value="">Select State</option>
                    ${stateOptions}
                </select>
            </td>
            <td>
                    <select class="form-control loc_city selectpicker" name="loc_city[]" data-live-search="true" data-width="100%">
                        <option value="">Select City</option>
                    </select>
                </td>
            <td><input type="text" class="form-control" name="loc_mobile[]" data-dyn="1"></td>
            <td><button type="button" class="btn btn-danger removeRow"><i class="fa fa-trash"></i></button></td>
        </tr>`;
    $('#locationTable tbody').append(newRow);
    // Initialize selectpicker for newly added selects
    $('.selectpicker').selectpicker('refresh');
});

// GL rows (if any)
$(document).on('click', '.addGlRow', function() {
    var rowCount = $('#glTable tbody tr').length + 1;
    var newRow = `<tr>
            <td>${rowCount}</td>
            <td><input type="text" class="form-control" name="gl_account_type[]" data-dyn="1"></td>
            <td><input type="text" class="form-control" name="gl_account[]" data-dyn="1"></td>
            <td><button type="button" class="btn btn-danger removeRow"><i class="fa fa-trash"></i></button></td>
        </tr>`;
    $('#glTable tbody').append(newRow);
});

// Remove row and re-index GL table
$(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
    $('#glTable tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
});

// City Dropdown Logic for Location Table
$(document).on('change', '.loc_state', function() {
    var StateID = $(this).val();
    var cityDropdown = $(this).closest('tr').find('select.loc_city');
    var url = "<?php echo base_url(); ?>purchase/GetCity";
    if (StateID !== '') {
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: {
                StateID: StateID
            },
            dataType: 'json',
            success: function(data) {
                cityDropdown.find('option').remove();
                cityDropdown.append(new Option('None selected', ""));
                for (var i = 0; i < data.length; i++) {
                    cityDropdown.append(new Option(data[i].city_name, data[i].id));
                }
                cityDropdown.selectpicker('refresh');
            }
        });
    } else {
        cityDropdown.find('option').remove();
        cityDropdown.append(new Option('None selected', ""));
        cityDropdown.selectpicker('refresh');
    }
});
// Validation disabled - form accepts all input
function validateForm() {
    var AccountID = $('#AccountID').val();
    var AccountName = $('#AccountName').val();
    var pan = $('#pan').val();
    var vat = $('#vat').val();
    var phonenumber = $('#phonenumber').val();
    var email = $('#email').val();
    var state = $('#billing_state').val();
    var city = $('#billing_city').val();
    var vendor_type = $('#vendor_type').val();
    var gst_type = $('select[name=gst_type]').val();

    // Required field validations
    if (AccountID == '') {
        alert_float('warning', 'Please enter Vendor Code');
        $('#AccountID').focus();
        return false;
    } 
    else if ($.trim(AccountName) == '') {
        alert_float('warning', 'Please enter Vendor Name');
        $('#AccountName').focus();
        return false;
    } 
    else if (vendor_type == '' || vendor_type == null) {
        alert_float('warning', 'Please select Vendor Category');
        $('#vendor_type').focus();
        return false;
    }
    else if (!$('#pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#pan').val() !== "") {
        alert_float('warning', 'Enter valid PAN number');
        $('#pan').focus();
        return false;
    } 
    else if (gst_type == '') {
        alert_float('warning', 'Please select GST Type');
        $('#gst_type').focus();
        return false;
    } 
    else if (parseInt(gst_type) == '1' && vat == '') {
        alert_float('warning', 'Enter valid GST number for Registered vendors');
        $('#vat').focus();
        return false;
    } 
    else if (!$('#vat').val().match('[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z][0-9][0-9A-Za-z]{2}') && $('#vat').val() !== '') {
        alert_float('warning', "Enter valid GST number");
        $('#vat').focus();
        return false;
    } 
    else if (phonenumber == '') {
        alert_float('warning', 'Please enter mobile number');
        $('#phonenumber').focus();
        return false;
    } 
    else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
        alert_float('warning', 'Enter valid Mobile number (10 digits)');
        $('#phonenumber').focus();
        return false;
    } 
    else if (!$('#email').val().match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/) && $('#email').val() !== "") {
        alert_float('warning', 'Enter valid Email address');
        $('#email').focus();
        return false;
    } 
    else if (state == '' || state == null) {
        alert_float('warning', 'Please select State');
        $('#billing_state').focus();
        return false;
    } 
    else if (city == '' || city == null) {
        alert_float('warning', 'Please select City');
        $('#billing_city').focus();
        return false;
    }
    
    return true;
}

// Contact row validation disabled
function validateContactRows() {
    return true;
}
</script>

<script>
// Handle Vendor Category selection and populate Vendor Code
$('#vendor_type').on('change', function() {
    var selectedVendorType = $(this).val();
    if (selectedVendorType) {
        // AJAX call to get next vendor code based on category count
        $.ajax({
            url: "<?php echo admin_url(); ?>purchase/GetNextVendorCode",
            type: 'POST',
            dataType: 'JSON',
            data: {
                ActSubGroupID2: selectedVendorType
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Display the auto-generated vendor code
                    $('#AccountID').val(response.next_code);
                    // Store the ActSubGroupID2 in hidden field for form submission
                    $('#HiddenVendorCode').val(response.ActSubGroupID2);
                    console.log('Vendor Code generated: ' + response.next_code + ' (Category: ' + response.category_name + ', Count: ' + response.count + ')');
                } else {
                    alert_float('warning', response.message || 'Failed to generate vendor code');
                    $('#AccountID').val('');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error generating vendor code:', error);
                alert_float('error', 'Error generating vendor code. Please try again.');
                $('#AccountID').val('');
            }
        });
    } else {
        // Clear if nothing selected
        $('#AccountID').val('');
        $('#HiddenVendorCode').val('');
    }
});

$(document).ready(function() {
    function getSubGroupsByMain(Tdsselection, callback) {
        $.ajax({
            url: "<?php echo admin_url(); ?>purchase/gettdspercent",
            dataType: "JSON",
            method: "POST",
            data: {
                Tdsselection: Tdsselection
            },
            beforeSend: function() {
                $('.searchh2').css('display', 'block').css('color', 'blue');
            },
            complete: function() {
                $('.searchh2').css('display', 'none');
            },
            success: function(data) {
                $('#TdsPercent').empty();

                if (data && data.length > 0) {
                    $('#TdsPercent').append('<option value="">Non Selected</option>');
                    $.each(data, function(index, item) {
                        $('#TdsPercent').append('<option value="' + item.rate + '">' + item.rate + '</option>');
                    });
                } else {
                    $('#TdsPercent').append('<option value="">Non Selected</option>');
                }
                $('.selectpicker').selectpicker('refresh');
                if (typeof callback === 'function') {
                    callback();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown);
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }
    $('#Tdsselection').on('change', function() {
        var Tdsselection = $(this).val();
        getSubGroupsByMain(Tdsselection);
    });

    function checkTds() {
        if ($('#Tds').val() === "1") {
            $('#TdsPercent1').show();
            $('#TdsSec').show();
        } else {
            $('#TdsPercent1').hide();
            $('#TdsSec').hide();
        }
    }
    checkTds();
    $('#Tds').on('change', function() {
        checkTds();
    });



    $("#AccountID").keypress(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == "") {
            $("#lblError").html("");
        } else {
            //Regex for Valid Characters i.e. Alphabets and Numbers.
            var regex = /^[A-Za-z0-9]+$/;
            //Validate TextBox value against the Regex.
            var isValid = regex.test(String.fromCharCode(keyCode));
            if (!isValid) {
                $("#lblError").html("Only Alphabets and Numbers allowed.");
            } else {
                $("#lblError").html("");
            }
            return isValid;
        }
    });

    $("#AccountName").keypress(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == "") {
            $("#lblError").html("");
        } else {
            var regex = /^[A-Za-z0-9\s]+$/; // Updated regex to allow letters and spaces
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });

    $("#email").on("input", function() {
        var email = $(this).val();
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Regular expression pattern for email validation

        if (!regex.test(email)) {
            $(".email_error").html(
                "Enter a valid email address."); // Display error message if email is invalid
        } else {
            $(".email_error").html(""); // Clear error message if email is valid
        }
    });

    $("#vat").on("input", function() {
        var gstNumber = $(this).val();
        var regex = /^([0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z][0-9][0-9A-Za-z]{2})?$/;

        if (!regex.test(gstNumber)) {
            $(".gst_denger").html("Enter valid GST no..");
        } else {
            $(".gst_denger").html("");
        }
    });

    // Show All button click - fetch and show all vendors
    $('.showAllBtn').on('click', function() {
        $('#Vendor_List').modal('show');
        $.ajax({
            url: "<?php echo admin_url(); ?>purchase/GetAllVendorList",
            dataType: "JSON",
            method: "POST",
            beforeSend: function() {
                $('.searchh2').css('display', 'block');
                $('.searchh2').css('color', 'blue');
                $('#vendorlistbody').html('<tr><td colspan="8" style="text-align:center;">Loading vendors...</td></tr>');
            },
            complete: function() {
                $('.searchh2').css('display', 'none');
            },
            success: function(data) {
                console.log('Vendor list loaded:', data);
                $('#vendorlistbody').html(data);
                AttachVendorRowClicks();
            },
            error: function(xhr, status, error) {
                console.log('Error loading vendors:', error);
                $('#vendorlistbody').html('<tr><td colspan="8" style="text-align:center; color:red;">Error loading vendors. Please try again.</td></tr>');
                alert_float('error', 'Error loading vendor list');
            }
        });
        $('#Vendor_List').on('shown.bs.modal', function() {
            $('#myInput1').val('');
            $('#myInput1').focus();
        })
    });

    // Double-click on Vendor Code to show vendor list
    $("#AccountID").dblclick(function() {
        $('#Vendor_List').modal('show');
        $.ajax({
            url: "<?php echo admin_url(); ?>purchase/GetAllVendorList",
            dataType: "JSON",
            method: "POST",
            beforeSend: function() {
                $('.searchh2').css('display', 'block');
                $('.searchh2').css('color', 'blue');
                $('#vendorlistbody').html('<tr><td colspan="8" style="text-align:center;">Loading vendors...</td></tr>');
            },
            complete: function() {
                $('.searchh2').css('display', 'none');
            },
            success: function(data) {
                console.log('Vendor list loaded:', data);
                $('#vendorlistbody').html(data);
                AttachVendorRowClicks();
            },
            error: function(xhr, status, error) {
                console.log('Error loading vendors:', error);
                $('#vendorlistbody').html('<tr><td colspan="8" style="text-align:center; color:red;">Error loading vendors. Please try again.</td></tr>');
                alert_float('error', 'Error loading vendor list');
            }
        });
        $('#Vendor_List').on('shown.bs.modal', function() {
            $('#myInput1').val('');
            $('#myInput1').focus();
        })
    });

    // Double-click on Vendor Name to show vendor list
    $("#AccountName").dblclick(function() {
        $('#Vendor_List').modal('show');
        $.ajax({
            url: "<?php echo admin_url(); ?>purchase/GetAllVendorList",
            dataType: "JSON",
            method: "POST",
            beforeSend: function() {
                $('.searchh2').css('display', 'block');
                $('.searchh2').css('color', 'blue');
                $('#vendorlistbody').html('<tr><td colspan="8" style="text-align:center;">Loading vendors...</td></tr>');
            },
            complete: function() {
                $('.searchh2').css('display', 'none');
            },
            success: function(data) {
                console.log('Vendor list loaded:', data);
                $('#vendorlistbody').html(data);
                AttachVendorRowClicks();
            },
            error: function(xhr, status, error) {
                console.log('Error loading vendors:', error);
                $('#vendorlistbody').html('<tr><td colspan="8" style="text-align:center; color:red;">Error loading vendors. Please try again.</td></tr>');
                alert_float('error', 'Error loading vendor list');
            }
        });
        $('#Vendor_List').on('shown.bs.modal', function() {
            $('#myInput1').val('');
            $('#myInput1').focus();
        })
    });

    // Attach click handlers to vendor table rows
    function AttachVendorRowClicks() {
        // Use delegated event handling so dynamically-loaded rows work reliably
        $('#table_Vendor_List').off('click', '.get_AccountID').on('click', '.get_AccountID', function(e) {
            e.preventDefault();
            var AccountID = $(this).attr("data-id");
            console.log('Fetching vendor details for: ' + AccountID);

            $.ajax({
                url: "<?php echo admin_url(); ?>purchase/GetVendorDetailByID",
                dataType: "JSON",
                method: "POST",
                data: { AccountID: AccountID },
                beforeSend: function() {
                    $('.searchh2').css('display', 'block').css('color', 'blue');
                    $('.saveBtn').hide();
                    $('.updateBtn').show();
                },
                complete: function() {
                    $('.searchh2').css('display', 'none');
                },
                success: function(response) {
                    console.log('Vendor data received:', response);
                    // Accept either raw object or { data: {...} } wrapper
                    var data = response && response.data ? response.data : response;
                    if (Array.isArray(data) && data.length > 0) data = data[0];
                    PopulateVendorData(data);
                    $('#Vendor_List').modal('hide');

                    // Scroll to top of form to show populated data
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: $("#manage-vendor-form").offset().top - 100
                        }, 500);
                    }, 500);
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    alert_float('error', 'Error loading vendor data: ' + error);
                }
            });
        });
    }

    // Populate vendor form with selected vendor data
    function PopulateVendorData(responseData) {
        // Extract vendor details from nested structure
        var data = responseData.clientDetails || responseData;
        
        if (data && data.AccountID) {
            try {
                // Populate basic fields
                $('#AccountID').val(data.AccountID);
                $('#HiddenVendorCode').val(data.AccountID);
                $('#AccountName').val(data.company || data.CompanyName || '');
                $('#favouring_name').val(data.FavouringName || data.FavouringName || '');
                $('#pan').val(data.PAN || data.Pan || '');
                $('#vat').val(data.GSTIN || data.vat || '');
                $('#email').val(data.Email || data.email || '');
                $('#phonenumber').val(data.MobileNo || data.phonenumber || data.Phone || '');
                $('#telephone').val(data.AltMobileNo || data.AltMobileNo || data.Phone || '');
                $('#billing_address').val(data.address || data.billing_address || '');
                $('#billing_zip').val(data.zip || data.billing_zip || data.PostalCode || '');
                var isActiveRaw = data.IsActive || data.active || '';
                        var active = (isActiveRaw === 'Y' || isActiveRaw === 'y' || isActiveRaw === '1' || isActiveRaw === 1 || isActiveRaw === true) ? '1' : '0';
                        $('select[name="blocked"]').val(active).selectpicker('refresh');
            // =============================================                
                // Populate vendor type/category - try multiple possible field names
                var categoryId = data.ActSubGroupID2 || data.ActSubGroupID || data.SubActGroupID || data.SubActGroupID2 || data.ActSubGroupID1 || null;
                if (categoryId) {
                    $('#vendor_type').val(categoryId).selectpicker('refresh');
                }
                $('#billing_country').val(data.billing_country).selectpicker('refresh');

                // Populate state
                if (data.state || data.billing_state) {
                    var stateId = data.state || data.billing_state;
                    $('#billing_state').val(stateId).selectpicker('refresh');
                    
                    // Load cities for selected state
                    $.ajax({
                        url: "<?php echo admin_url(); ?>purchase/GetCityListByStateID",
                        type: 'POST',
                        data: { id: stateId },
                        dataType: 'json',
                        success: function(cities) {
                            $("#city").children().remove();
                            $.each(cities, function(index, value) {
                                $('#billing_city').append('<option value="' + value.id + '">' +
                                    value.city_name + '</option>');
                            });
                            if (data.city || data.billing_city) {
                                $('#billing_city').val(data.city || data.billing_city);
                            }
                            $('#billing_city').selectpicker('refresh');
                        }
                    });
                }
                
                // Populate select fields
                var selectFields = ['Tds', 'gst_type', 'organisation_type', 'pay_term', 'payment_cycle_type',
                                   'payment_cycle', 'freight_terms', 'priority', 'territory', 'blocked'];

                $.each(selectFields, function(index, field) {
                    if (typeof data[field] !== 'undefined' && data[field] !== null && data[field] !== '') {
                        var selector = 'select[name="' + field + '"]';
                        $(selector).val(data[field]).selectpicker('refresh');
                    }
                });

                // Ensure GST Category is set — backend may return different key names (gst_type/gsttype/GSTType/etc.)
                var gstVal = (typeof data.gst_type !== 'undefined' ? data.gst_type : (typeof data.gsttype !== 'undefined' ? data.gsttype : (typeof data.GSTType !== 'undefined' ? data.GSTType : (typeof data.GST !== 'undefined' ? data.GST : (typeof data.gstType !== 'undefined' ? data.gstType : null)))));
                if (gstVal !== null && typeof gstVal !== 'undefined' && gstVal !== '') {
                    $('select[name=gst_type]').val(gstVal).selectpicker('refresh');
                }

                // Populate additional fields
                $('#organisation_type').val(data.OrganisationType || '').selectpicker('refresh');
                $('#territory').val(data.TerritoryID || data.territory || '').selectpicker('refresh');
                $('#priority').val(data.PriorityID || data.priority || '').selectpicker('refresh');
                $('#pay_term').val(data.PaymentTerms || data.pay_term || '').selectpicker('refresh');
                $('#payment_cycle_type').val(data.PaymentCycleType || data.payment_cycle_type || '').selectpicker('refresh');
                $('#payment_cycle').val(data.PaymentCycle || data.payment_cycle || '').selectpicker('refresh');
                $('#freight_terms').val(data.FreightTerms || data.freight_terms || '').selectpicker('refresh');
                $('#credit_days').val(data.GraceDay || data.credit_days || '0');
                $('#website').val(data.website || '');
                $('#additional_info').val(data.AdditionalInfo || data.additional_info || '');
                $('#food_lic_n').val(data.FSSAINo || data.food_lic_n || '');
                
                // Populate TDS settings
                var tdsFlag = false;
                if (typeof data.IsTDS !== 'undefined') {
                    tdsFlag = (data.IsTDS === 'Y' || data.IsTDS === '1' || data.IsTDS === 1 || data.IsTDS === true);
                }
                if (!tdsFlag && (typeof data.Tds !== 'undefined')) {
                    tdsFlag = (data.Tds === '1' || data.Tds === 1 || data.Tds === 'Y');
                }

                if (tdsFlag) {
                    $('#Tds').val('1').selectpicker('refresh');
                    $('#TdsSec').show();
                    $('#TdsPercent1').show();

                    // Determine TDS section from multiple possible backend keys
                    var tdsSectionVal = data.TDSSection || data.TDSSectionID || data.TDSCode || data.Tdsselection || data.TDSSelection || data.TdsSection || data.TDS || null;
                    if (tdsSectionVal) {
                        $('#Tdsselection').val(tdsSectionVal).selectpicker('refresh');
                        // Load percent options then set percent value
                        getSubGroupsByMain(tdsSectionVal, function() {
                            var tdsPercentVal = data.TDSPer || data.TDSPercent || data.TdsPercent || data.TDSRate || data.tds_percent || data.tdsPercent || '';
                            if (tdsPercentVal) {
                                $('#TdsPercent').val(tdsPercentVal).selectpicker('refresh');
                            }
                        });
                    } else {
                        // No section provided, try set percent directly from available keys
                        var tdsPercentVal = data.TDSPer || data.TDSPercent || data.TdsPercent || data.TDSRate || data.tds_percent || data.tdsPercent || '';
                        if (tdsPercentVal) {
                            $('#TdsPercent').val(tdsPercentVal).selectpicker('refresh');
                        }
                    }
                } else {
                    $('#Tds').val('0').selectpicker('refresh');
                    $('#TdsSec').hide();
                    $('#TdsPercent1').hide();
                }

                // Populate bank details (from bankData array)
                if (responseData.bankData && responseData.bankData.length > 0) {
                    var bankRecord = responseData.bankData[0]; // Get first bank record
                    $('#is_bank_detail').val('1').selectpicker('refresh');
                    $('.bank_detail_section').show();
                    $('#ifsc_code').val(bankRecord.IFSC || '');
                    $('#bank_name').val(bankRecord.BankName || '');
                    $('#bank_branch').val(bankRecord.BranchName || '');
                    $('#bank_address').val(bankRecord.BankAddress || '');
                    $('#account_number').val(bankRecord.AccountNo || '');
                    $('#account_holder_name').val(bankRecord.HolderName || '');
                }

                // Populate attachment if exists
                if (data.Attachment && data.Attachment.trim() !== '') {
                    var attachmentPath = data.Attachment;
                    var fileName = attachmentPath.split('/').pop(); // Get filename from path
                    var fullUrl = '<?php echo base_url(); ?>' + attachmentPath;
                    
                     // Create attachment display with download link and clear button
                    var attachmentHtml = '<div style="padding: 12px; background-color: #e8f4f8; border-left: 4px solid #0275d8; border-radius: 4px; height: 100%;">' +
                        '<label style="margin: 0; margin-bottom: 8px; display: block; font-weight: 600; color: #333;"><i class="fa fa-file"></i> Download Attachment:</label>' +
                        '<a href="' + fullUrl + '" download="' + fileName + '" oncontextmenu="return true;" style="color: #0275d8; text-decoration: none; font-weight: 500; cursor: pointer; display: block; word-break: break-all; margin-bottom: 8px;" title="Right-click to download">' +
                        '<i class="fa fa-download"></i> ' + fileName + '</a>' +
                        '<button type="button" class="btn btn-danger btn-sm clearAttachmentBtn" style="width: 20    %;" title="Remove this attachment">' +
                        '<i class="fa fa-trash"></i>Remove</button>' +
                        '</div>';
                    
                    // Insert as adjacent column after Additional Information
                    var additionalInfoCol = $('#additional_info').closest('.col-md-4');
                    additionalInfoCol.find('.attachment-display-col').remove(); // Remove old display
                    var attachmentCol = '<div class="col-md-4 attachment-display-col">' + attachmentHtml + '</div>';
                    additionalInfoCol.after(attachmentCol);
                    
                    // Clear attachment on Clear button click (using event delegation)
                    $(document).off('click', '.clearAttachmentBtn').on('click', '.clearAttachmentBtn', function() {
                        $('#attachment').val('');
                        $('.attachment-display-col').remove();
                        alert_float('info', 'Attachment cleared. You can upload a new one if needed.');
                    });
                }

                // Populate contact details table (from contactData array)
                if (responseData.contactData && responseData.contactData.length > 0) {
                    populateContactDetailsTable(responseData.contactData);
                }

                // Populate location/shipping details table (from shippingData array)
                if (responseData.shippingData && responseData.shippingData.length > 0) {
                    populateLocationDetailsTable(responseData.shippingData);
                }
                
                // Update button states
                $('.saveBtn').hide();
                $('.updateBtn').show();
                
                console.log('Vendor data populated successfully');
                alert_float('success', 'Vendor details loaded successfully!');
            } catch (e) {
                console.log('Error populating data:', e);
                alert_float('error', 'Error populating vendor data: ' + e.message);
            }
        } else {
            alert_float('warning', 'Invalid vendor data received');
        }
    }

    // Populate contact details table from vendorData
    function populateContactDetailsTable(contactData) {
        // Clear existing contact rows (except the first empty template row)
        $("#contactTable tbody tr").not(':first').remove();
        
        // Initialize selectpickers for new rows after DOM insertion
        setTimeout(function() {
            $("#contactTable tbody select.selectpicker").selectpicker('refresh');
        }, 100);
        
        // Directly build and insert all contact rows
        var rowsHtml = '';
        contactData.forEach(function(contact, index) {
            var smsChecked = contact.IsSmsYN === 'Y' ? 'checked' : '';
            var emailChecked = contact.IsEmailYN === 'Y' ? 'checked' : '';
            
            rowsHtml += '<tr>' +
                '<td><input type="text" class="form-control" name="cp_name[]" value="' + (contact.firstname || '') + '"></td>' +
                '<td><select class="selectpicker form-control cp_designation_input" name="cp_designation[]" data-width="100%">' + getPositionOptions(contact.PositionID || '') + '</select></td>' +
                '<td><input type="text" class="form-control" maxlength="10" onkeypress="return isNumber(event)" name="cp_mobile[]" value="' + (contact.phonenumber || '') + '"></td>' +
                '<td><input type="email" class="form-control" name="cp_email[]" value="' + (contact.email || '') + '"></td>' +
                '<td class="text-center"><input type="checkbox" name="cp_send_sms[]" value="1" ' + smsChecked + '></td>' +
                '<td class="text-center"><input type="checkbox" name="cp_send_email[]" value="1" ' + emailChecked + '></td>' +
                '<td><button type="button" class="btn btn-danger removeContactBtn"><i class="fa fa-times"></i></button></td>' +
                '</tr>';
        });
        
        // Insert all rows at once
        if (rowsHtml) {
            $("#contactTable tbody").append(rowsHtml);
        }
        
        console.log('Contact Details: ' + contactData.length + ' rows populated');
    }

    // Populate location/shipping details table from vendorData
    function populateLocationDetailsTable(shippingData) {
        // Clear existing location rows (except the first empty template row)
        $("#locationTable tbody tr").not(':first').remove();
        
        // Get state options from the template row's state select
        let templateStateSelect = $("#locationTable tbody tr:first select[name='loc_state[]']");
        let stateOptionsHtml = templateStateSelect.html();
        
        // Directly build and insert all location rows
        var rowsHtml = '';
        shippingData.forEach(function(location, index) {
            rowsHtml += '<tr>' +
                '<td><input type="text" class="form-control" name="loc_pincode[]" value="' + (location.ShippingPin || '') + '"></td>' +
                '<td><textarea class="form-control" name="loc_address[]" rows="1">' + (location.ShippingAdrees || '') + '</textarea></td>' +
                '<td><select class="form-control loc_state selectpicker" name="loc_state[]" data-width="100%">' +
                    stateOptionsHtml +
                '</select></td>' +
                '<td><select class="form-control loc_city selectpicker" name="loc_city[]" data-live-search="true" data-width="100%">' +
                    '<option value="">Select City</option></select></td>' +
                '<td><input type="text" class="form-control" name="loc_mobile[]" value="' + (location.MobileNo || '') + '"></td>' +
                '<td><button type="button" class="btn btn-danger removeLocationBtn"><i class="fa fa-times"></i></button></td>' +
                '</tr>';
        });
        
        // Insert all rows at once
        if (rowsHtml) {
            $("#locationTable tbody").append(rowsHtml);
        }
        
        // Now refresh selectpickers for all new rows and populate state/city data
        setTimeout(function() {
            $("#locationTable tbody select.selectpicker").selectpicker('refresh');
            
            // Populate state and city for each location
            let allRows = $("#locationTable tbody tr").not(":first");
            shippingData.forEach(function(location, index) {
                let row = allRows.eq(index);
                
                if (location.ShippingState) {
                    let stateSelect = row.find('select[name="loc_state[]"]');
                    stateSelect.val(location.ShippingState);
                    stateSelect.selectpicker('refresh');
                    
                    // Load cities for this state
                    let citySelect = row.find('select[name="loc_city[]"]');
                    $.ajax({
                        url: "<?php echo admin_url(); ?>purchase/GetCityListByStateID",
                        type: 'POST',
                        data: { id: location.ShippingState },
                        dataType: 'json',
                        success: function(cities) {
                            citySelect.empty();
                            citySelect.append(new Option('Select City', ''));
                            
                            cities.forEach(function(city) {
                                citySelect.append(new Option(city.city_name, city.id));
                            });
                            
                            // Set city if available
                            if (location.ShippingCity) {
                                citySelect.val(location.ShippingCity);
                            }
                            citySelect.selectpicker('refresh');
                        }
                    });
                }
            });
        }, 100);
        
        console.log('Location Details: ' + shippingData.length + ' rows populated');
    }
    $('#billing_state').on('change', function() {
        var id = $(this).val();
        var url = "<?php echo admin_url(); ?>purchase/GetCityListByStateID";
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: { id: id },
            dataType: 'json',
            success: function(data) {
                // ✅ billing_city clear 
                $('#billing_city').empty();
                $('#billing_city').append('<option value="">None selected</option>');
                
                $.each(data, function(index, value) {
                    $('#billing_city').append(
                        '<option value="' + value.id + '">' + value.city_name + '</option>'
                    );
                });
                
                // ✅ billing_city selectpicker refresh
                if (typeof $('#billing_city').selectpicker === 'function') {
                    try {
                        $('#billing_city').selectpicker('refresh');
                    } catch (e) {
                        $('#billing_city').selectpicker();
                    }
                }
            }
        });
    });

    function ResetForm() {
        var HiddenVendorCode = $("#HiddenVendorCode").val();
        $('#AccountID').val('');
        $('#AccountName').val('');
        $('#billing_address').val('');
        $('#billing_zip').val('');
        $('#phonenumber').val('');
        $('#email').val('');
        $('#vat').val('');
        $('#pan').val('');
        $('#vendor_type').val('');
        $('#gst_type').val('');
        $('#organisation_type').val('');
        $('#billing_state').val('');
        $('#billing_city').val('');
        $('#billing_country').val('');
        // $('#credit_days').val('');
        $('#is_bank_detail').val('');
        $('#ifsc_code').val('');
        $('#bank_name').val('');
        $('#bank_branch').val('');
        $('#bank_address').val('');
        $('#account_number').val('');
        $('#gst_type').val('');
        $('#account_holder_name').val('');
        $('#pay_term').val('');
        $('#payment_cycle_type').val('');
        // $('#credit_days').val('');
        $('#website').val('');
        $('#freight_terms').val('');
        $('#food_lic_n').val('');
        $('#priority').val('');
        $('#territory').val('');
        $('#attachment').val('');
        $('#additional_info').val('');
        $('#payment_cycle').val('');
        $('#favouring_name').val('');
        $('#telephone').val('');
        $('select[name=vendor_type]').attr('disabled', false);
        $('.selectpicker').selectpicker('refresh');

        $('#credit_days').val('');
        $('#pay_term').val('');
        $('.selectpicker').selectpicker('refresh');

        $('#food_lic_n').val('');
        $('.saveBtn').removeAttr('disabled');

        $('select[name=gst_type]').val('1');
        $('.selectpicker').selectpicker('refresh');

        $('select[name=state]').val('');
        $('.selectpicker').selectpicker('refresh');

        $('select[name=Tds]').val('0');
        checkTds();
        $('select[name=Tds]').val('');
        $('.selectpicker').selectpicker('refresh');

        $("#city").children().remove();
        $('.selectpicker').selectpicker('refresh');

        $('input[name=blocked][value=0]').prop('checked', true);

        // Clear dynamic tables
        $('#contactTable tbody tr:not(:first)').remove();
        $('#contactTable tbody input').val('');
        $('#locationTable tbody tr:not(:first)').remove();
        $('#locationTable tbody input, #locationTable tbody textarea').val('');
        $('#glTable tbody tr:not(:first)').remove();
        $('#glTable tbody input').val('');

        // Clear attachment
        $('#attachment').val('');
        $('.attachment-display-col').remove();

        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
    }
    // Empty and open create mode
    $("#AccountID").focus(function() {
        ResetForm();
    });

    // Cancel selected data
    $(".cancelBtn").click(function() {
        ResetForm();
        // Reload entire form/page
        // setTimeout(function() {
        //     location.reload();
        // }, 0000);
    });



    // Row click handling moved to delegated handler in AttachVendorRowClicks()

    // Collect Contact Details from table
    function collectContactData() {
        let ContactData = [];
        $("#contactTable tbody tr").each(function() {
            let Name = $(this).find("input[name='cp_name[]']").val();
            let Designation = $(this).find("select[name='cp_designation[]']").val();
            let Mobile = $(this).find("input[name='cp_mobile[]']").val();
            let Email = $(this).find("input[name='cp_email[]']").val();
            let SendSMS = $(this).find("input[name='cp_send_sms[]']").is(':checked') ? 1 : 0;
            let SendEmail = $(this).find("input[name='cp_send_email[]']").is(':checked') ? 1 : 0;

            // Skip empty rows
            if (!Name && !Designation && !Mobile && !Email) return;

            ContactData.push({
                Name: Name,
                Designation: Designation,
                Mobile: Mobile,
                Email: Email,
                SendSMS: SendSMS,
                SendEmail: SendEmail
            });
        });
        return ContactData;
    }

    // Collect Location Information from table
    function collectLocationData() {
        let LocationData = [];
        $("#locationTable tbody tr").each(function() {
            let Pincode = $(this).find("input[name='loc_pincode[]']").val();
            let Address = $(this).find("textarea[name='loc_address[]']").val();
            let State = $(this).find("select[name='loc_state[]']").val();
            let City = $(this).find("select[name='loc_city[]']").val();
            let Mobile = $(this).find("input[name='loc_mobile[]']").val();

            // Skip empty rows
            if (!Pincode && !Address && !State && !City && !Mobile) return;

            LocationData.push({
                pincode: Pincode,
                address: Address,
                state: State,
                city: City,
                mobile: Mobile
            });
        });
        return LocationData;
    }

    // Save New Vendor - Form submission with validation and AJAX
    $('.saveBtn').on('click', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return false;
        }

        // Collect Contact Details
        let ContactData = collectContactData();
        let ContactDataJSON = JSON.stringify(ContactData);

        // Collect Location Information
        let LocationData = collectLocationData();
        let LocationDataJSON = JSON.stringify(LocationData);

        // Collect all form data
        var AccountID = $('#AccountID').val();
        var AccountName = $('#AccountName').val();
        var favouring_name = $('#favouring_name').val();
        var pan = $('#pan').val();
        var vat = $('#vat').val();
        var vendor_type = $('#vendor_type').val();
        var gst_type = $('select[name=gst_type]').val();
        var phonenumber = $('#phonenumber').val();
        var telephone = $('#telephone').val();
        var email = $('#email').val();
        var state = $('#billing_state').val();
        var city = $('#billing_city').val();
        var address = $('#billing_address').val();
        var zip = $('#billing_zip').val();
        var credit_days = $('#credit_days').val();
        var pay_term = $('#pay_term').val();
        var Tds = $('select[name=Tds]').val();
        var TdsPercent = '';
        var Tdsselection = '';
        
        if (Tds === '1' || Tds === 1) {
            TdsPercent = $('select[name=TdsPercent]').val();
            Tdsselection = $('select[name=Tdsselection]').val();
        }
        
        var organisation_type = $('select[name=organisation_type]').val();
        var billing_country = $('#billing_country').val();
        var food_lic_n = $('#food_lic_n').val();
        var website = $('#website').val();
        var freight_terms = $('#freight_terms').val();
        var priority = $('#priority').val();
        var territory = $('#territory').val();
        var additional_info = $('#additional_info').val();
        var ifsc_code = $('#ifsc_code').val();
        var bank_name = $('#bank_name').val();
        var bank_branch = $('#bank_branch').val();
        var bank_address = $('#bank_address').val();
        var account_number = $('#account_number').val();
        var account_holder_name = $('#account_holder_name').val();
        var is_bank_detail = $('#is_bank_detail').val();
        var payment_cycle_type = $('select[name=payment_cycle_type]').val();
        var payment_cycle = $('select[name=payment_cycle]').val();
        // Read Is Active select (name="blocked"). Convert 1 -> 'Y', 0 -> 'N'
        var blockedVal = $('select[name=blocked]').val();
        var active = (blockedVal === '1' || blockedVal === 1) ? 'Y' : 'N';

        // Get attachment file if present
        var attachment = document.getElementById('attachment').files[0] || null;

        // Prepare form data
        var formData = new FormData();
        formData.append('AccountID', AccountID);
        formData.append('AccountName', AccountName);
        formData.append('favouring_name', favouring_name);
        formData.append('Pan', pan);
        formData.append('vat', vat);
        formData.append('vendor_type', vendor_type);
        formData.append('gsttype', gst_type);
        formData.append('phonenumber', phonenumber);
        formData.append('telephone', telephone);
        formData.append('email', email);
        formData.append('state', state);
        formData.append('city', city);
        formData.append('address', address);
        formData.append('zip', zip);
        formData.append('credit_days', credit_days);
        formData.append('pay_term', pay_term);
        formData.append('Tds', Tds);
        formData.append('TdsPercent', TdsPercent);
        formData.append('Tdsselection', Tdsselection);
        formData.append('organisation_type', organisation_type);
        formData.append('billing_country', billing_country);
        formData.append('FLNO1', food_lic_n);
        formData.append('website', website);
        formData.append('freight_terms', freight_terms);
        formData.append('priority', priority);
        formData.append('territory', territory);
        formData.append('additional_info', additional_info);
        formData.append('ifsc_code', ifsc_code);
        formData.append('bank_name', bank_name);
        formData.append('bank_branch', bank_branch);
        formData.append('bank_address', bank_address);
        formData.append('account_number', account_number);
        formData.append('account_holder_name', account_holder_name);
        formData.append('is_bank_detail', is_bank_detail);
        formData.append('payment_cycle_type', payment_cycle_type);
        formData.append('payment_cycle', payment_cycle);
        formData.append('active', active);
        formData.append('ContactData', ContactDataJSON);
        formData.append('LocationData', LocationDataJSON);
        formData.append('ItemData', JSON.stringify([]));
        formData.append('DisData', JSON.stringify([]));

        // Append attachment file if present
        if (attachment) {
            formData.append('attachment', attachment);
        }

        // Add CSRF token
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfVal = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';
        formData.append(csrfName, csrfVal);

        // Send AJAX request
        $.ajax({
            url: "<?php echo admin_url(); ?>purchase/SaveVendor",
            dataType: "JSON",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.searchh3').css('display', 'block');
                $('.searchh3').css('color', 'blue');
                $('.saveBtn').attr('disabled', 'disabled');
            },
            complete: function() {
                $('.searchh3').css('display', 'none');
                $('.saveBtn').removeAttr('disabled');
            },
            success: function(data) {
                if (data && data.success) {
                    alert_float('success', 'Vendor created successfully...');
                    ResetForm();
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 0000);
                } else {
                    var msg = (data && data.message) ? data.message : 'Something went wrong...';
                    alert_float('warning', msg);
                    $('.saveBtn').removeAttr('disabled');
                }
            },
            error: function(xhr, status, err) {
                console.error('SaveVendor error', status, err, xhr.responseText);
                var errorMsg = 'Error saving vendor';
                try {
                    if (xhr.responseText) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    }
                } catch(e) {
                    errorMsg = xhr.responseText || 'Error saving vendor';
                }
                alert_float('danger', errorMsg);
                $('.saveBtn').removeAttr('disabled');
            }
        });
    });

    // Update Existing Vendor - Form submission with validation and AJAX
    $('.updateBtn').on('click', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return false;
        }

        // Collect Contact Details
        let ContactData = collectContactData();
        let ContactDataJSON = JSON.stringify(ContactData);

        // Collect Location Information
        let LocationData = collectLocationData();
        let LocationDataJSON = JSON.stringify(LocationData);

        // Collect all form data
        var AccountID = $('#AccountID').val();
        var AccountName = $('#AccountName').val();
        var favouring_name = $('#favouring_name').val();
        var pan = $('#pan').val();
        var vat = $('#vat').val();
        var vendor_type = $('#vendor_type').val();
        var gst_type = $('select[name=gst_type]').val();
        var phonenumber = $('#phonenumber').val();
        var telephone = $('#telephone').val();
        var email = $('#email').val();
        var state = $('#billing_state').val();
        var city = $('#billing_city').val();
        var address = $('#billing_address').val();
        var zip = $('#billing_zip').val();
        var credit_days = $('#credit_days').val();
        var pay_term = $('#pay_term').val();
        var Tds = $('select[name=Tds]').val();
        var TdsPercent = '';
        var Tdsselection = '';
        
        if (Tds === '1' || Tds === 1) {
            TdsPercent = $('select[name=TdsPercent]').val();
            Tdsselection = $('select[name=Tdsselection]').val();
        }
        
        var organisation_type = $('select[name=organisation_type]').val();
        var billing_country = $('#billing_country').val();
        var food_lic_n = $('#food_lic_n').val();
        var website = $('#website').val();
        var freight_terms = $('#freight_terms').val();
        var priority = $('#priority').val();
        var territory = $('#territory').val();
        var additional_info = $('#additional_info').val();
        var ifsc_code = $('#ifsc_code').val();
        var bank_name = $('#bank_name').val();
        var bank_branch = $('#bank_branch').val();
        var bank_address = $('#bank_address').val();
        var account_number = $('#account_number').val();
        var account_holder_name = $('#account_holder_name').val();
        var is_bank_detail = $('#is_bank_detail').val();
        var payment_cycle_type = $('select[name=payment_cycle_type]').val();
        var payment_cycle = $('select[name=payment_cycle]').val();
        // Read Is Active select (name="blocked"). Convert 1 -> 'Y', 0 -> 'N'
        var blockedVal = $('select[name=blocked]').val();
        var active = (blockedVal === '1' || blockedVal === 1) ? 'Y' : 'N';

        // Get attachment file if present
        var attachment = document.getElementById('attachment').files[0] || null;

        // Prepare form data
        var formData = new FormData();
        formData.append('AccountID', AccountID);
        formData.append('AccountName', AccountName);
        formData.append('favouring_name', favouring_name);
        formData.append('Pan', pan);
        formData.append('vat', vat);
        formData.append('vendor_type', vendor_type);
        formData.append('gsttype', gst_type);
        formData.append('phonenumber', phonenumber);
        formData.append('telephone', telephone);
        formData.append('email', email);
        formData.append('state', state);
        formData.append('city', city);
        formData.append('address', address);
        formData.append('zip', zip);
        formData.append('credit_days', credit_days);
        formData.append('pay_term', pay_term);
        formData.append('Tds', Tds);
        formData.append('TdsPercent', TdsPercent);
        formData.append('Tdsselection', Tdsselection);
        formData.append('organisation_type', organisation_type);
        formData.append('billing_country', billing_country);
        formData.append('FLNO1', food_lic_n);
        formData.append('website', website);
        formData.append('freight_terms', freight_terms);
        formData.append('priority', priority);
        formData.append('territory', territory);
        formData.append('additional_info', additional_info);
        formData.append('ifsc_code', ifsc_code);
        formData.append('bank_name', bank_name);
        formData.append('bank_branch', bank_branch);
        formData.append('bank_address', bank_address);
        formData.append('account_number', account_number);
        formData.append('account_holder_name', account_holder_name);
        formData.append('is_bank_detail', is_bank_detail);
        formData.append('payment_cycle_type', payment_cycle_type);
        formData.append('payment_cycle', payment_cycle);
        formData.append('active', active);
        formData.append('ContactData', ContactDataJSON);
        formData.append('LocationData', LocationDataJSON);
        formData.append('ItemData', JSON.stringify([]));
        formData.append('DisData', JSON.stringify([]));

        // Append attachment file if present
        if (attachment) {
            formData.append('attachment', attachment);
        }

        // Add CSRF token
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfVal = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';
        formData.append(csrfName, csrfVal);

        // Send AJAX request
        $.ajax({
            url: "<?php echo admin_url(); ?>purchase/UpdateVendor/<?php echo isset($vendor) ? $vendor->userid : ''; ?>",
            dataType: "JSON",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.searchh4').css('display', 'block');
                $('.searchh4').css('color', 'blue');
                $('.updateBtn').attr('disabled', 'disabled');
            },
            complete: function() {
                $('.searchh4').css('display', 'none');
                $('.updateBtn').removeAttr('disabled');
            },
            success: function(data) {
                if (data && data.success) {
                    alert_float('success', 'Vendor updated successfully...');
                    ResetForm();
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 0000);
                } else {
                    var msg = (data && data.message) ? data.message : 'Something went wrong...';
                    alert_float('warning', msg);
                    $('.updateBtn').removeAttr('disabled');
                }
            },
            error: function(xhr, status, err) {
                console.error('UpdateVendor error', status, err, xhr.responseText);
                var errorMsg = 'Error updating vendor';
                try {
                    if (xhr.responseText) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    }
                } catch(e) {
                    errorMsg = xhr.responseText || 'Error updating vendor';
                }
                alert_float('danger', errorMsg);
                $('.updateBtn').removeAttr('disabled');
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Toggle Bank Detail Fields
    function toggleBankDetails() {
        var isBankDetail = $('#is_bank_detail').val();
        if (isBankDetail === '1' || isBankDetail === 1) {
            $('.bank_detail_section').show();
        } else {
            $('.bank_detail_section').hide();
            // Clear bank fields when hiding
            $('#ifsc_code').val('');
            $('#bank_name').val('');
            $('#bank_branch').val('');
            $('#bank_address').val('');
            $('#account_number').val('');
            $('#account_holder_name').val('');
        }
    }

    // On page load, check the current value and toggle
    toggleBankDetails();

    // On change of Is Bank Detail select
    $('#is_bank_detail').on('change', function() {
        toggleBankDetails();
    });

    $("#vat").on("blur", function() {
        let GstNo = $(this).val().trim();
        if (GstNo === "") return;

        $.ajax({
            url: "<?php echo admin_url('purchase/validate_gst_mastergst'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                GstNo: GstNo
            },
            success: function(res) {
                if (res.Status) {
                    $("#AccountName").val(res.GstData.legalName);
                    $("#address").val(res.GstData.address1);
                    $("#zip").val(res.GstData.pinCode);
                } else {
                    alert('Invalid Gst No.');
                    $("#vat").val('');
                    $("#AccountName").val('');
                }

            },
        });
    });
});
</script>
<script>
function myFunction2() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput1");
    filter = input.value.toUpperCase();
    table = document.getElementById("table_Vendor_List");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        td1 = tr[i].getElementsByTagName("td")[1];
        td2 = tr[i].getElementsByTagName("td")[2];
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
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    }
}
</script>
<script>
$('#ifsc_code').blur(function() {
    var ifsc_code = $('#ifsc_code').val();
    $.ajax({
        url: "<?php echo admin_url(); ?>purchase/fetchBankDetailsFromIFSC",
        method: "POST",
        dataType: 'json',
        data: {
            ifsc_code: ifsc_code
        },
        beforeSend: function() {
            $('.searchh6').css('display', 'block');

            $('.searchh6').css('color', 'blue');
        },
        complete: function() {
            $('.searchh6').css('display', 'none');
        },
        success: function(data) {
            if (data == "Not Found") {
                alert("Enter valid IFSC Code");
                $('#bank_name').val("");
                $('#bank_branch').val("");
                $('#bank_address').val("");
            } else {
                $('#bank_name').val(data.BANK);
                $('#bank_branch').val(data.BRANCH);
                $('#bank_address').val(data.ADDRESS);
            }
        }
    });
});

$('#bank_ac_no').blur(function() {
    var bank_ac_no = $('#bank_ac_no').val();
    var ifsc_code = $('#ifsc_code').val();
    $.ajax({
        url: "<?php echo admin_url(); ?>purchase/verifyBankAccount",
        method: "POST",
        dataType: 'json',
        data: {
            bank_ac_no: bank_ac_no,
            ifsc_code: ifsc_code
        },
        beforeSend: function() {
            $('.searchh6').css('display', 'block');

            $('.searchh6').css('color', 'blue');
        },
        complete: function() {
            $('.searchh6').css('display', 'none');
        },
        success: function(data) {
            if (data.success == false) {
                alert("Bank account not verified");

                $('#ac_holder_name').val('');
                $('#bank_ac_no').val('');
            } else {
                $('#ac_holder_name').val(data.data.full_name);
            }
        }
    });
});

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode = 46 && charCode > 31 &&
        (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

$(document).on("click", ".sortablePop", function() {
    var table = $("#table_Vendor_List tbody");
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

// ========== PAN BLUR EVENT HANDLER (ADAPTED FOR AddEditVendor) ==========
$("#pan").on("blur", function() {
    let panNo = $(this).val().trim();
    // Map 4th PAN character to organisation type and set default
    if (panNo && panNo.length >= 4) {
        let ch = panNo.charAt(3).toUpperCase();
        const panMap = {
            'P': 'Proprietorship',
            'F': 'Partnership',
            'C': 'Private Limited',
            'H': 'Hindu Undivided Family (HUF)',
            'T': 'Society / Trust / Club',
            'G': 'Government Department/Body',
            'L': 'Local Authority'
        };
        if (panMap[ch]) {
            $('#organisation_type').val(panMap[ch]);
            $('.selectpicker').selectpicker('refresh');
        }
    }
    if (panNo === "") {
        $(".pan_denger").text("");
        return;
    }

    let panPattern = /^[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}$/;
    if (!panPattern.test(panNo)) {
        $(".pan_denger").text("Invalid PAN format").css("color", "red");
        return;
    }

    let panLabel = $('label').filter(function() {
        return $(this).parent().find('#pan').length > 0;
    });
    if (panLabel) {
        panLabel.html(
            '<small class="req text-danger">* </small>PAN <span style="color: blue; font-size: 12px;"><i>(Verifying...)</i></span>'
        );
    }

    $.ajax({
        url: "<?php echo admin_url('purchase/verify_pan'); ?>",
        type: "POST",
        dataType: "json",
        data: {
            pan: panNo.toUpperCase()
        },
        timeout: 30000,
        success: function(res) {
            if (res.status === 'success') {
                $("#AccountName").val(res.data.full_name || '');
                alert_float('success', 'PAN No verified successfully');

                let panLabel = $('label').filter(function() {
                    return $(this).parent().find('#pan').length > 0;
                });
                if (panLabel) {
                    panLabel.html('<small class="req text-danger">* </small>PAN');
                }

                let gstinLabel = document.querySelector('label[for="vat"]') || $('label').filter(
                    function() {
                        return $(this).parent().find('#vat').length > 0;
                    });
                if (gstinLabel) {
                    $(gstinLabel).html(
                        '<small class="req text-danger">* </small>GSTIN <span style="color: blue; font-size: 12px;"><i>(Fetching Data...)</i></span>'
                    );
                }

                // Fetch GSTIN by PAN
                $.ajax({
                    url: "<?php echo admin_url('purchase/get_gstin_by_pan'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        pan: panNo.toUpperCase()
                    },
                    timeout: 30000,
                    success: function(gstRes) {
                        if (gstRes.status === 'success') {
                            let gstinList = gstRes.data.gstin_list || [];

                            if (gstinList.length === 1) {
                                $("#vat").hide();

                                let gstinDropdown = `
								<select id="gstin_select" class="selectpicker form-control" onchange="verifyGSTIN()" data-width="100%" style='margin-top:5px;'>
								<option value="${gstinList[0].gstin}" selected>${gstinList[0].gstin}</option>
							`;

                                gstinDropdown += `</select>`;

                                $(".gst_denger").html(gstinDropdown).css("color",
                                    "green").show();
                                $("#vat").val(gstinList[0].gstin);
                                $('#gstin_select').selectpicker('refresh');

                                alert_float('success',
                                    'GSTIN fetched and filled automatically');

                                // AUTO VERIFY GSTIN
                                verifyGSTIN();
                            } else if (gstinList.length > 1) {
                                $("#vat").hide();

                                let gstinDropdown = `
								<select id="gstin_select" class="selectpicker form-control" onchange="handleGstinSelection()" data-width="100%" style='margin-top:5px;'>
								<option value="">Select GSTIN NO</option>
							`;

                                gstinList.forEach(function(item) {
                                    gstinDropdown +=
                                        `<option value="${item.gstin}" data-state="${item.state}">${item.gstin}</option>`;
                                });

                                gstinDropdown += `</select>`;

                                $(".gst_denger").html(gstinDropdown).css("color",
                                    "green").show();
                                $('#gstin_select').selectpicker('refresh');

                                let gstinLabel = $('label').filter(function() {
                                    return $(this).parent().find('#vat')
                                        .length > 0;
                                });
                                if (gstinLabel) {
                                    gstinLabel.html(
                                        '<small class="req text-danger">* </small>GSTIN'
                                    );
                                }
                            } else {
                                $("#vat").hide();

                                let gstinDropdown = `
								<select id="gstin_select" class="selectpicker form-control" data-width="100%" style='margin-top:5px;' disabled>
								<option value="">NO GSTIN FOUND</option>
							`;

                                gstinDropdown += `</select>`;

                                $(".gst_denger").html(gstinDropdown).css("color",
                                    "orange").show();
                                $('#gstin_select').selectpicker('refresh');

                                let gstinLabel = $('label').filter(function() {
                                    return $(this).parent().find('#vat')
                                        .length > 0;
                                });
                                if (gstinLabel) {
                                    gstinLabel.html(
                                        '<small class="req text-danger">* </small>GSTIN'
                                    );
                                }

                                alert_float('warning', 'No GSTIN found for this PAN');
                            }
                        } else {
                            console.log('No GSTIN found:', gstRes.message);
                            let gstinLabel = $('label').filter(function() {
                                return $(this).parent().find('#vat').length > 0;
                            });
                            if (gstinLabel) {
                                gstinLabel.html(
                                    '<small class="req text-danger">* </small>GSTIN'
                                );
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('GSTIN fetch error:', error);
                        let gstinLabel = $('label').filter(function() {
                            return $(this).parent().find('#vat').length > 0;
                        });
                        if (gstinLabel) {
                            gstinLabel.html(
                                '<small class="req text-danger">* </small>GSTIN');
                        }
                    }
                });
            } else {
                $(".pan_denger").html("✗ " + res.message).css("color", "red");
                alert_float('danger', res.message || 'PAN verification failed');

                let panLabel = $('label').filter(function() {
                    return $(this).parent().find('#pan').length > 0;
                });
                if (panLabel) {
                    panLabel.html('<small class="req text-danger">* </small>PAN');
                }

                if (res.message === 'Invalid PAN') {
                    $("#pan").val('');
                    $(".pan_denger").text("");
                }
            }
        },
        error: function(xhr, status, error) {
            $(".pan_denger").html("✗ Verification failed").css("color", "red");
            alert_float('danger', 'PAN verification failed');

            let panLabel = $('label').filter(function() {
                return $(this).parent().find('#pan').length > 0;
            });
            if (panLabel) {
                panLabel.html('<small class="req text-danger">* </small>PAN');
            }
        }
    });
});

// ========== VERIFY GSTIN FUNCTION (ADAPTED FOR AddEditVendor) ==========
function verifyGSTIN() {
    let gstinNo = $('#vat').val();

    // Check from dropdown if exists
    if ($('#gstin_select').length > 0) {
        gstinNo = $('#gstin_select').val();
    }

    if (!gstinNo || gstinNo.length !== 15) {
        alert_float('warning', 'GSTIN must be 15 characters');
        return;
    }

    $('.gstin-verify-msg').remove();

    $.ajax({
        url: "<?php echo admin_url('purchase/verify_gstin_kyc'); ?>",
        type: "POST",
        dataType: "json",
        data: {
            gstin: gstinNo.toUpperCase()
        },
        beforeSend: function() {
            let gstinDropdown = $('#gstin_select');
            if (gstinDropdown.length > 0) {
                gstinDropdown.after(
                    '<small style="color: blue; display: block; margin-top: 5px;" class="gstin-verify-msg"><i>Verifying...</i></small>'
                );
            } else {
                $('.gst_denger').append(
                    '<br><small style="color: blue;" class="gstin-verify-msg"><i>Verifying...</i></small>'
                );
            }
        },
        success: function(response) {
            console.log('GSTIN Response:', response);
            $('.gstin-verify-msg').remove();

            if (response.status === 'success') {
                let msgText = response.message || 'GSTIN verified successfully';
                alert_float('success', msgText);

                // Set GST Category to Registered (value '1') when GSTIN verification succeeds
                $('select[name=gst_type]').val('1');
                $('.selectpicker').selectpicker('refresh');

                if (response.data && response.data.fallback === false) {
                    var detailsCheck = response.data.details || {};
                    var hasRelevant = false;
                    if (detailsCheck.business_name && detailsCheck.business_name.toString().trim() !== '')
                        hasRelevant = true;
                    if (detailsCheck.contact_details && detailsCheck.contact_details.principal) {
                        var p = detailsCheck.contact_details.principal;
                        if ((p.address && p.address.toString().trim() !== '') || (p.email && p.email
                                .toString().trim() !== '') || (p.mobile && p.mobile.toString().trim() !==
                                '')) hasRelevant = true;
                    }
                    if (!hasRelevant) {
                        alert_float('warning', 'Data not found');
                    }
                }

                if (response.data && response.data.details) {
                    let details = response.data.details;
                    let contactDetails = details.contact_details;
                    console.log('Contact Details:', contactDetails);

                    if (contactDetails && contactDetails.principal) {
                        let principal = contactDetails.principal;
                        console.log('Principal data:', principal);

                        if (principal.email) {
                            console.log('Setting email:', principal.email);
                            $('#email').val(principal.email);
                        }

                        if (principal.mobile) {
                            let mobile = principal.mobile.replace(/\D/g, '').slice(-10);
                            console.log('Setting mobile:', mobile);
                            $('#phonenumber').val(mobile);
                        }

                        populateLocationTableFromGSTData(response);
                        populateContactTableFromGSTData(response);

                        if (principal.address) {
                            let fullAddress = principal.address;
                            let addressParts = fullAddress.split(',').map(function(part) {
                                return part.trim();
                            });
                            console.log('Address parts:', addressParts);

                            if (addressParts.length > 0) {
                                let lastElement = addressParts[addressParts.length - 1];
                                if (/^\d{6}$/.test(lastElement)) {
                                    console.log('Setting pincode:', lastElement);
                                    $('#zip').val(lastElement);
                                }
                            }

                            if (addressParts.length > 1) {
                                let stateElement = addressParts[addressParts.length - 2];
                                console.log('Looking for state:', stateElement);
                                $('#state option').each(function() {
                                    if ($(this).text().indexOf(stateElement) !== -1) {
                                        console.log('Setting state:', $(this).val());
                                        $('#state').val($(this).val());
                                        $('#state').selectpicker('refresh');
                                        return false;
                                    }
                                });
                            }

                            if (addressParts.length > 2) {
                                let cityElement = addressParts[addressParts.length - 3];
                                console.log('Looking for city:', cityElement);
                                if ($('#state').val()) {
                                    let stateId = $('#state').val();
                                    $.ajax({
                                        url: "<?php echo base_url(); ?>purchase/GetCity",
                                        type: 'POST',
                                        data: {
                                            StateID: stateId
                                        },
                                        dataType: 'json',
                                        success: function(cityData) {
                                            console.log('Cities fetched:', cityData);
                                            cityData.forEach(function(city) {
                                                if (city.city_name.toLowerCase()
                                                    .indexOf(cityElement
                                                        .toLowerCase()) !== -1) {
                                                    console.log('Setting city:', city
                                                        .id);
                                                    if ($('#city option[value="' + city
                                                            .id + '"]').length === 0) {
                                                        $('#city').append(new Option(
                                                            city.city_name, city
                                                            .id));
                                                    }
                                                    $('#city option[data-temp="1"]')
                                                        .remove();
                                                    $('#city').selectpicker('refresh');
                                                    $('#city').selectpicker('val', city
                                                        .id);
                                                    $('#city').selectpicker('refresh');
                                                    $('#city').trigger('change');
                                                    $('#city').selectpicker('refresh');
                                                    return false;
                                                }
                                            });
                                        },
                                        error: function(err) {
                                            console.log('City fetch error:', err);
                                        }
                                    });
                                }
                            }

                            let formattedAddress = addressParts.join('\n');
                            console.log('Setting address:', formattedAddress);
                            $('#billing_address').val(fullAddress);

                            let tokens = fullAddress.split(',').map(function(p) {
                                return p.trim();
                            }).filter(function(p) {
                                return p.length > 0;
                            });
                            for (let i = tokens.length - 1; i >= 0; i--) {
                                if (/^\d{6}$/.test(tokens[i])) {
                                    $('#billing_zip').val(tokens[i]);
                                    tokens.splice(i, 1);
                                    break;
                                }
                            }

                            let stateDetected = false;
                            for (let i = tokens.length - 1; i >= 0 && !stateDetected; i--) {
                                let tok = tokens[i].toLowerCase();
                                $('#billing_state option').each(function() {
                                    if ($(this).text().toLowerCase().indexOf(tok) !== -1) {
                                        $('#billing_state').val($(this).val());
                                        $('#billing_state').selectpicker('refresh');
                                        stateDetected = true;
                                        return false;
                                    }
                                });
                                if (stateDetected) {
                                    tokens.splice(i, 1);
                                }
                            }

                            if (stateDetected) {
                                let stateId = $('#billing_state').val();
                                if (stateId) {
                                    $.ajax({
                                        url: "<?php echo base_url(); ?>purchase/GetCity",
                                        type: 'POST',
                                        data: {
                                            StateID: stateId
                                        },
                                        dataType: 'json',
                                        success: function(cityData) {
                                            for (let i = tokens.length - 1; i >= 0; i--) {
                                                let tok = tokens[i].toLowerCase();
                                                for (let j = 0; j < cityData.length; j++) {
                                                    let cname = cityData[j].city_name
                                                        .toLowerCase();
                                                    if (cname === tok) {
                                                        if ($('#billing_city option[value="' + cityData[
                                                                j].id + '"]').length === 0) {
                                                            $('#billing_city').append(new Option(
                                                                cityData[j].city_name,
                                                                cityData[j].id));
                                                            $('#billing_city').selectpicker('refresh');
                                                        }
                                                        $('#billing_city').selectpicker('val', cityData[
                                                            j].id);
                                                        $('#billing_city').selectpicker('refresh');
                                                        $('#billing_city').trigger('change');
                                                        return;
                                                    }
                                                    if (cname.indexOf(tok) !== -1 || tok
                                                        .indexOf(cname) !== -1) {
                                                        if ($('#billing_city option[value="' + cityData[
                                                                j].id + '"]').length === 0) {
                                                            $('#billing_city').append(new Option(
                                                                cityData[j].city_name,
                                                                cityData[j].id));
                                                            $('#billing_city').selectpicker('refresh');
                                                        }
                                                        $('#billing_city').selectpicker('val', cityData[
                                                            j].id);
                                                        $('#billing_city').selectpicker('refresh');
                                                        $('#billing_city').trigger('change');
                                                        return;
                                                    }
                                                    var words = cname.split(/\s+/);
                                                    if (words.indexOf(tok) !== -1) {
                                                        if ($('#billing_city option[value="' + cityData[
                                                                j].id + '"]').length === 0) {
                                                            $('#billing_city').append(new Option(
                                                                cityData[j].city_name,
                                                                cityData[j].id));
                                                            $('#billing_city').selectpicker('refresh');
                                                        }
                                                        $('#billing_city').selectpicker('val', cityData[
                                                            j].id);
                                                        $('#billing_city').selectpicker('refresh');
                                                        $('#billing_city').trigger('change');
                                                        return;
                                                    }
                                                }
                                            }
                                        },
                                        error: function(err) {
                                            console.log('City fetch error:', err);
                                        }
                                    });
                                }
                            }

                            $('#billing_address').val(formattedAddress);
                            if (principal.address) {
                                $('#billing_address').val(principal.address);
                            }
                        }
                    } else {
                        console.log('No principal contact details found');
                    }
                } else {
                    console.log('No data or details in response');
                }

                if (details.business_name) {
                    $('#AccountName').val(details.business_name);
                }

                if (details.gstin) {
                    $('#vat').val(details.gstin);
                }
                if (details.pan_number) {
                    $('#pan').val(details.pan_number);
                }

                // POPULATE CONTACT AND LOCATION TABLES FROM GSTIN DATA
                populateGSTINContactsAndLocations(response.data);

                let gstinDropdown = $('#gstin_select');
                if (gstinDropdown.length > 0) {
                    gstinDropdown.after(
                        '<small style="color: green; display: block; margin-top: 8px; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-check"></i> ' +
                        msgText + '</small>');
                } else {
                    $('.gst_denger').append(
                        '<br><small style="color: green; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-check"></i> ' +
                        msgText + '</small>');
                }
            } else {
                let msgText = response.message || 'GSTIN verification failed';
                alert_float('error', msgText);

                // Set GST Category to Un-Registered (value '2') when GSTIN verification fails
                $('select[name=gst_type]').val('2');
                $('.selectpicker').selectpicker('refresh');

                let gstinDropdown = $('#gstin_select');
                if (gstinDropdown.length > 0) {
                    // gstinDropdown.after(
                    //     '<small style="color: red; display: block; margin-top: 8px; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-times"></i> ' +
                    //     msgText + '</small>');
                } else {
                    // $('.gst_denger').append(
                    //     '<br><small style="color: red; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-times"></i> ' +
                    //     msgText + '</small>');
                }
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("AJAX Error:", textStatus, errorThrown);
            console.log("Response:", jqXHR.responseText);
            $('.gstin-verify-msg').remove();
            alert_float('error', 'Error verifying GSTIN: ' + textStatus);

            // On AJAX error, set GST category to Un-Registered
            $('select[name=gst_type]').val('2');
            $('.selectpicker').selectpicker('refresh');

            let gstinDropdown = $('#gstin_select');
            if (gstinDropdown.length > 0) {
                gstinDropdown.after(
                    '<small style="color: red; display: block; margin-top: 8px; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-times"></i> Error verifying GSTIN</small>'
                );
            } else {
                $('.gst_denger').append(
                    '<br><small style="color: red; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-times"></i> Error verifying GSTIN</small>'
                );
            }
        }
    });
}

// ========== HANDLE GSTIN SELECTION FROM DROPDOWN ==========
function handleGstinSelection() {
    let selectedGstin = $('#gstin_select').val();
    if (selectedGstin) {
        $("#vat").val(selectedGstin);
        alert_float('success', 'GSTIN selected');
        verifyGSTIN();
    }
}

// ========== POPULATE CONTACTS AND LOCATIONS FROM GSTIN VERIFICATION RESPONSE ==========
function populateGSTINContactsAndLocations(gstinData) {
    if (!gstinData || !gstinData.details) {
        return;
    }

    let details = gstinData.details;

    // Populate Contact Details from Promoters
    if (details.promoters && details.promoters.length > 0) {
        // Clear contact table (except first row)
        $("#contactTable tbody tr").not(':first').remove();

        let contactsHtml = '';
        details.promoters.forEach(function(promoter, index) {
            let name = promoter ? promoter.trim() : '';
            if (!name) return;

            contactsHtml += '<tr>' +
                '<td><input type="text" class="form-control" name="cp_name[]" value="' + name + '"></td>' +
                '<td><select class="selectpicker form-control cp_designation_input" name="cp_designation[]" data-width="100%">' + getPositionOptions() + '</select></td>' +
                '<td><input type="text" class="form-control" maxlength="10" onkeypress="return isNumber(event)" name="cp_mobile[]" value="' + (details.contact_details && details.contact_details.principal && details.contact_details.principal.mobile || '') + '"></td>' +
                '<td><input type="email" class="form-control" name="cp_email[]" value="' + (details.contact_details && details.contact_details.principal && details.contact_details.principal.email || '') + '"></td>' +
                '<td class="text-center"><input type="checkbox" name="cp_send_sms[]" value="1"></td>' +
                '<td class="text-center"><input type="checkbox" name="cp_send_email[]" value="1"></td>' +
                '<td><button type="button" class="btn btn-danger removeBtn"><i class="fa fa-times"></i></button></td>' +
                '</tr>';
        });

        if (contactsHtml) {
            $("#contactTable tbody").append(contactsHtml);
        }

        console.log('GSTIN Contacts: ' + details.promoters.length + ' rows populated');
    }

    // Populate Location Details from Additional Addresses
    if (details.contact_details && details.contact_details.additional && details.contact_details.additional.length > 0) {
        // Clear location table (except first row)
        $("#locationTable tbody tr").not(':first').remove();

        let additionalAddresses = details.contact_details.additional;
        let templateStateSelect = $("#locationTable tbody tr:first select[name='loc_state[]']");
        let stateOptionsHtml = templateStateSelect.html();

        let locationsHtml = '';
        additionalAddresses.forEach(function(location, index) {
            let fullAddress = location.address || '';
            let mobile = location.mobile || '';

            // Parse address to extract pincode
            let parts = fullAddress.split(',').map(p => p.trim()).filter(p => p.length > 0);
            let pincode = '';
            if (parts.length > 0) {
                let lastPart = parts[parts.length - 1];
                if (/^\d{6}$/.test(lastPart)) {
                    pincode = lastPart;
                }
            }

            // Assume state is Maharashtra (MH) from GSTIN (Gujarat = GJ, etc.)
            let stateCode = gstinData.gstin ? gstinData.gstin.substring(0, 2) : 'MH';

            locationsHtml += '<tr>' +
                '<td><input type="text" class="form-control" name="loc_pincode[]" value="' + pincode + '"></td>' +
                '<td><textarea class="form-control" name="loc_address[]" rows="1">' + fullAddress + '</textarea></td>' +
                '<td><select class="form-control loc_state selectpicker" name="loc_state[]" data-width="100%">' +
                    stateOptionsHtml +
                '</select></td>' +
                '<td><select class="form-control loc_city selectpicker" name="loc_city[]" data-live-search="true" data-width="100%">' +
                    '<option value="">Select City</option></select></td>' +
                '<td><input type="text" class="form-control" name="loc_mobile[]" value="' + mobile + '"></td>' +
                '<td><button type="button" class="btn btn-danger removeBtn"><i class="fa fa-times"></i></button></td>' +
                '</tr>';
        });

        if (locationsHtml) {
            $("#locationTable tbody").append(locationsHtml);
        }

        // Refresh selectpickers
        setTimeout(function() {
            $("#locationTable tbody select.selectpicker").selectpicker('refresh');
            console.log('GSTIN Locations: ' + additionalAddresses.length + ' rows populated');
        }, 100);
    }
}

// ========== HELPER FUNCTIONS (ADAPTED FOR AddEditVendor) ==========

// Populate Location Table from GST Data
function populateLocationTableFromGSTData(apiResponse) {
    if (!apiResponse || !apiResponse.data || !apiResponse.data.details) {
        return;
    }

    let contactDetails = apiResponse.data.details.contact_details;
    if (!contactDetails || !contactDetails.additional) {
        return;
    }

    let additionalAddresses = contactDetails.additional;

    additionalAddresses.forEach(function(location, index) {
        let fullAddress = location.address || '';
        let mobile = location.mobile || '';

        // Parse address
        let parts = fullAddress.split(',').map(p => p.trim()).filter(p => p.length > 0);
        let pincode = '',
            state = '',
            city = '';

        if (parts.length >= 3) {
            let lastPart = parts[parts.length - 1];
            if (/^\d{6}$/.test(lastPart)) {
                pincode = lastPart;
                state = parts[parts.length - 2];
                city = parts[parts.length - 3];
            }
        }

        // Add row with delay
        setTimeout(function() {
            addLocationRowWithData(fullAddress, mobile, pincode, state, city);
        }, index * 500);
    });
}

function addLocationRowWithData(address, mobile, pincode, stateName, cityName) {
    // Click add location button
    $('.addLocationRow').trigger('click');

    setTimeout(function() {
        let lastRow = $('#locationTable tbody tr:last');

        // Set values
        lastRow.find('textarea[name="loc_address[]"]').val(address);
        lastRow.find('input[name="loc_mobile[]"]').val(mobile);
        lastRow.find('input[name="loc_pincode[]"]').val(pincode);

        // Set state
        if (stateName) {
            let stateSelect = lastRow.find('select[name="loc_state[]"]');
            stateSelect.find('option').each(function() {
                if ($(this).text().toLowerCase().indexOf(stateName.toLowerCase()) !== -1) {
                    let stateValue = $(this).val();
                    stateSelect.val(stateValue);
                    stateSelect.selectpicker('refresh');

                    // Load cities
                    setTimeout(function() {
                        loadCitiesForLocation(lastRow, stateValue, cityName);
                    }, 200);
                    return false;
                }
            });
        }
    }, 300);
}

function loadCitiesForLocation(rowElement, stateId, cityName) {
    $.ajax({
        url: "<?php echo base_url(); ?>purchase/GetCity",
        type: 'POST',
        data: {
            StateID: stateId
        },
        dataType: 'json',
        success: function(cityData) {
            let citySelect = rowElement.find('select[name="loc_city[]"]');
            citySelect.empty();
            citySelect.append(new Option('Select City', ''));

            cityData.forEach(function(city) {
                citySelect.append(new Option(city.city_name, city.id));
            });

            citySelect.selectpicker('refresh');

            // Select matching city
            if (cityName) {
                citySelect.find('option').each(function() {
                    if ($(this).text().toLowerCase().indexOf(cityName.toLowerCase()) !== -1) {
                        citySelect.val($(this).val());
                        citySelect.selectpicker('refresh');
                        return false;
                    }
                });
            }
        }
    });
}

// Populate Contact Details table from GST response
function populateContactTableFromGSTData(apiResponse) {
    if (!apiResponse || !apiResponse.data || !apiResponse.data.details) return;
    let details = apiResponse.data.details;
    let promoters = details.promoters || [];
    let contactPrincipal = (details.contact_details && details.contact_details.principal) ? details.contact_details
        .principal : {};
    let email = contactPrincipal.email || apiResponse.data.email || '';
    let rawMobile = contactPrincipal.mobile || apiResponse.data.mobile || '';
    let mobile = rawMobile ? rawMobile.replace(/\D/g, '').slice(-10) : '';

    // Remove existing added contact rows (but keep the first empty row)
    $("#contactTable tbody tr.addedtr_contact").remove();

    function esc(s) {
        return s ? s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, "&#39;").replace(
            /\"/g, '&quot;') : '';
    }

    promoters.forEach(function(name) {
        name = name ? name.toString().trim() : '';
        if (!name) return;
        let sendEmailChecked = email ? 'checked' : '';
        let sendSMSChecked = mobile ? 'checked' : '';
        var newRow = $("<tr class='addedtr_contact'></tr>");
        newRow.append("<td><input type='text' name='cp_name[]' class='form-control' value='" + esc(name) +
            "'></td>");
        newRow.append(
            "<td><select class='selectpicker form-control cp_designation_input' name='cp_designation[]' data-width='100%'>" + getPositionOptions() + "</select></td>");
        newRow.append("<td><input type='text' name='cp_mobile[]' class='form-control' value='" + esc(
                mobile) +
            "' maxlength='10' minlength='10' pattern='[0-9]{10}' onkeypress='return isNumber(event)' required></td>"
        );

        newRow.append("<td><input type='email' name='cp_email[]' class='form-control' value='" + esc(
            email) + "' pattern='[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}' required></td>");
        newRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_sms[]' value='1' " +
            (sendSMSChecked ? '' : '') + "> <label></label></div></td>");
        newRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_email[]' value='1' " +
            (sendEmailChecked ? '' : '') + "><label></label></div></td>");
        newRow.append(
            "<td><button type='button' class='btn btn-danger'><i class='fa fa-times'></i></button></td>");
        $("#contactTable tbody").append(newRow);
    });

    // If no promoters present, add principal as a contact row so user sees it
    if (promoters.length === 0 && (email || mobile)) {
        var principalRow = $("<tr class='addedtr_contact'></tr>");
        principalRow.append(
            "<td><input type='text' name='cp_name[]' class='form-control' value='Principal'></td>");
        principalRow.append("<td><select class='selectpicker form-control cp_designation_input' name='cp_designation[]' data-width='100%'>" + getPositionOptions() + "</select></td>");
        principalRow.append("<td><input type='text' name='cp_mobile[]' class='form-control' value='" + esc(
                mobile) +
            "' maxlength='10' minlength='10' pattern='[0-9]{10}' onkeypress='return isNumber(event)' required></td>"
        );

        principalRow.append("<td><input type='email' name='cp_email[]' class='form-control' value='" + esc(email) +
            "' pattern='[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}' required></td>");
        principalRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_sms[]' value='1' " +
            (mobile ? '' : '') + "><label></label></div></td>");
        principalRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_email[]' value='1' " +
            (email ? '' : '') + "><label></label></div></td>");
        principalRow.append(
            "<td><button type='button' class='btn btn-danger'><i class='fa fa-times'></i></button></td>");
        $("#contactTable tbody").append(principalRow);
    }
}

// Populate Location Data from GSTIN
function populateLocationDataFromGSTIN(gstinData) {
    // Clear existing location rows (but keep the first empty row)
    $("#locationTable tbody tr.addedtr_location").remove();

    if (gstinData && gstinData.details && gstinData.details.contact_details && gstinData.details.contact_details
        .additional) {
        let additionalContacts = gstinData.details.contact_details.additional;

        additionalContacts.forEach(function(location, index) {
            if (location.address && location.mobile) {
                // Parse the address to extract pincode, state, and city
                let fullAddress = location.address;
                let addressParts = fullAddress.split(',').map(part => part.trim());

                let pincode = '';
                let state = '';
                let city = '';
                let addressWithoutPinStateCity = '';

                // Extract pincode (last element if 6 digits)
                if (addressParts.length > 0) {
                    let lastElement = addressParts[addressParts.length - 1];
                    if (/^\d{6}$/.test(lastElement)) {
                        pincode = lastElement;
                        addressParts.pop(); // Remove pincode from array
                    }
                }

                // Extract state (now last element after removing pincode)
                if (addressParts.length > 0) {
                    state = addressParts[addressParts.length - 1];
                    addressParts.pop(); // Remove state from array
                }

                // Extract city (now last element after removing pincode and state)
                if (addressParts.length > 0) {
                    city = addressParts[addressParts.length - 1];
                    addressParts.pop(); // Remove city from array
                }

                // Remaining parts form the address
                addressWithoutPinStateCity = addressParts.join(', ');

                // Clean mobile number (extract last 10 digits)
                let mobile = location.mobile.replace(/\D/g, '').slice(-10);

                // Create new row
                let newRow = $("<tr class='addedtr_location'></tr>");


                // Add pincode
                newRow.append("<td><input type='text' class='form-control' name='loc_pincode[]' value='" +
                    pincode + "' onkeypress='return isNumber(event)' maxlength='6' required></td>");

                // Add address
                newRow.append("<td><textarea class='form-control' name='loc_address[]' rows='1'>" +
                    addressWithoutPinStateCity + "</textarea></td>");

                // Add state dropdown
                let stateDropdown =
                    "<td><select class='form-control loc_state selectpicker' name='loc_state[]' required>";
                stateDropdown += "<option value=''>Select State</option>";
                // State options should already exist in the main form's state dropdown
                $('#state option').each(function(i) {
                    if (i > 0) { // Skip the first empty option
                        stateDropdown += "<option value='" + $(this).val() + "'>" + $(this).text() +
                            "</option>";
                    }
                });
                stateDropdown += "</select></td>";
                newRow.append(stateDropdown);

                // Add city dropdown (will be populated after state selection)
                newRow.append(
                    "<td><select class='form-control loc_city selectpicker' name='loc_city[]' required><option value=''>Select City</option></select></td>"
                );


                // Add mobile
                newRow.append("<td><input type='text' class='form-control' name='loc_mobile[]' value='" +
                    mobile + "' onkeypress='return isNumber(event)' maxlength='10' required></td>");

                // Add remove button
                newRow.append(
                    "<td><button type='button' class='btn btn-danger removeLocationBtn'><i class='fa fa-times'></i></button></td>"
                );

                // Append row to table
                $("#locationTable tbody").append(newRow);

                // Refresh selectpickers
                newRow.find('.selectpicker').selectpicker('refresh');

                // Now set the state value and trigger city population
                let stateSelect = newRow.find('.loc_state');
                let citySelect = newRow.find('.loc_city');

                // Find matching state
                stateSelect.find('option').each(function() {
                    if ($(this).text().toLowerCase().indexOf(state.toLowerCase()) !== -1) {
                        stateSelect.val($(this).val());
                        stateSelect.selectpicker('refresh');

                        // Fetch cities for this state
                        let stateId = $(this).val();
                        $.ajax({
                            url: "<?php echo base_url(); ?>purchase/GetCity",
                            type: 'POST',
                            data: {
                                StateID: stateId
                            },
                            dataType: 'json',
                            success: function(cityData) {
                                citySelect.empty();
                                citySelect.append(new Option('Select City', ''));

                                cityData.forEach(function(cityItem) {
                                    citySelect.append(new Option(cityItem.city_name,
                                        cityItem.id));
                                });

                                // Now find and set the matching city
                                citySelect.find('option').each(function() {
                                    if ($(this).text().toLowerCase().indexOf(city
                                            .toLowerCase()) !== -1) {
                                        citySelect.val($(this).val());
                                        return false;
                                    }
                                });
                                citySelect.selectpicker('refresh');
                            }
                        });

                        return false;
                    }
                });
            }
        });

        alert_float('success', 'Location data populated from GSTIN');
    }
}

$(document).on('click', '.removeLocationBtn', function(e) {
    e.preventDefault();
    $(this).closest('tr').remove();
});
$(document).on('click', '.removeContactBtn', function() {
    $(this).closest('tr').remove();
});
</script>
<style>
#AccountID {
    text-transform: uppercase;
}

#pan {
    text-transform: uppercase;
}

#table_Vendor_List td:hover {
    cursor: pointer;
}

#table_Vendor_List tr:hover {
    background-color: #ccc;
}

.table-Vendor_List {
    overflow: auto;
    max-height: 65vh;
    width: 100%;
    position: relative;
    top: 0px;
}

.table-Vendor_List thead th {
    position: sticky;
    top: 0;
    z-index: 1;
}

.table-Vendor_List tbody th {
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

/* Contact Details Table Column Widths */
#contactTable th:nth-child(1),
#contactTable td:nth-child(1) {
    width: 20%;
    min-width: 120px;
}

#contactTable th:nth-child(2),
#contactTable td:nth-child(2) {
    width: 29%;
    min-width: 100px;
}



/* Location Information Table Column Widths */
#locationTable th:nth-child(1),
#locationTable td:nth-child(1) {
    width: 8%;
    min-width: 8%;
}

#locationTable th:nth-child(2),
#locationTable td:nth-child(2) {
    width: 500px;
    min-width: 500px;
}

#locationTable th:nth-child(3),
#locationTable td:nth-child(3) {
    width: 250px;
    max-width: 250px;
}


/* Validation highlight */
.required-error {
    border: 2px solid #d9534f !important;
    background: #fff6f6 !important;
}

/* Dropdown fix - prevent dropdown from overlaying tables */
.form-group {
    position: relative;
}

.table-responsive {
    overflow: visible;
    position: relative;
    z-index: 1;
}

/* Bootstrap dropdown menu should not be constrained */
.bootstrap-select>.dropdown-toggle {
    overflow: visible;
}

select {
    position: relative;
    z-index: 9999;
}

/* Action Buttons Styling */
.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.action-buttons .btn {
    min-width: 120px;
}

@media (max-width: 767px) {
    .action-buttons {
        flex-direction: column-reverse;
        align-items: stretch;
    }

    .action-buttons .btn {
        width: 100%;
    }
}
</style>