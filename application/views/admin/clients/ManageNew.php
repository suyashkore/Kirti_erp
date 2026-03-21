<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" style="min-height:1px">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        // Determine form action: add or update (if editing an existing client)
                        $form_action = isset($client) && !empty($client->userid) ? admin_url('clients/client/' . $client->userid) : admin_url('clients/client');
                        ?>
                        <form id="manage-client-form" method="post" action="<?= $form_action ?>"
                            enctype="multipart/form-data">
                            <input type="hidden" name="client_id" value="<?= isset($client) ? $client->userid : '' ?>">
                            <style>
                            @media (max-width: 767px) {
                                .mobile-menu-btn {
                                    display: block !important;
                                    margin-bottom: 10px;
                                    width: 100%;
                                    text-align: left;
                                }

                                .custombreadcrumb {
                                    display: none !important;
                                }

                                .custombreadcrumb.open {
                                    display: block !important;
                                }

                                .custombreadcrumb li {
                                    display: block;
                                    padding: 8px 10px;
                                    border-bottom: 1px solid #eee;
                                }

                                .custombreadcrumb li a {
                                    display: block;
                                }

                                .custombreadcrumb li+li:before {
                                    content: none;
                                }
                            }

                            .mobile-menu-btn {
                                display: none;
                            }
                            </style>
                            <button class="btn btn-default mobile-menu-btn"
                                onclick="$('.custombreadcrumb').toggleClass('open')">
                                <i class="fa fa-bars"></i> Menu
                            </button>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb custombreadcrumb"
                                    style="background-color:#fff !important; margin-Bottom:0px !important; display: flex; flex-wrap: wrap;">
                                    <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i
                                                    class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                    <li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
                                    <li class="breadcrumb-item active" aria-current="page"><b>Customer</b></li>
                                </ol>
                            </nav>
                            <hr class="hr_style">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                    <div class="searchh3" style="display:none;">Please wait while creating new record.
                                    </div>
                                    </style>
                        </form>
                    </div>
                </div>

                <!-- Top Fields - Main Information -->
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Customer
                                Category</label>
                            <select class="selectpicker form-control" id="groups_in"
                                data-none-selected-text="None selected" name="groups_in" data-width="100%"
                                data-live-search="true" title="Select Customer Category">
                                <option value=""></option>
                                <?php foreach ($getcustomergroups as $key => $value) { ?>
                                <option value="<?php echo $value['SubActGroupID']; ?>">
                                    <?php echo $value['SubActGroupName']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Customer
                                Code</label>
                            <input type="text" class="form-control" id="AccountID" name="AccountID" readonly>
                            <?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>
                            <?php if (is_admin()) {
										$is_admin = 1;
									} else {
										$is_admin = 0;
									} ?>
                            <input type="hidden" id="HiddenVendorCode" name="HiddenVendorCode">
                            <input type="hidden" name="staffid" value="<?php echo $staff_user_id; ?>" id="staffid">
                            <input type="hidden" name="is_admin" value="<?php echo $is_admin; ?>" id="is_admin">
                            <input type="hidden" name="PlantID"
                                value="<?php echo $this->session->userdata('root_company'); ?>" id="PlantID">
                            <input type="hidden" name="userid" id="userid" value="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Customer
                                Name</label>
                            <input type="text" class="form-control" id="AccoountName" name="AccoountName" readonly>
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
                <!-- BILLING INFORMATION SECTION -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="bold p_style">Billing Information</h4>
                        <hr class="hr_style">
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>PAN No</label>
                            <input type="text" class="form-control" id="Pan" name="Pan"
                                style="text-transform:uppercase;" pattern="[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}"
                                maxlength="10" minlength="10" required>
                            <span class="pan_denger" style="color:red;"></span>
                            <!-- <small class="form-text text-muted">Format: AAAAA0000A</small> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>GSTIN</label>
                            <input type="text" class="form-control" id="vat" name="vat"
                                pattern="([0-9]){2}([A-Za-z]){5}([0-9]){4}([A-Za-z]){1}([0-9]{1})([0-9A-Za-z]){2}"
                                maxlength="15" minlength="15" onchange="verifyGSTIN()" required>
                            <span class="gst_denger" style="color:red;"></span>
                            <!-- <small class="form-text text-muted">15 digit GST number</small> -->
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Organisation
                                Type</label>
                            <select class="selectpicker form-control" id="organisation_type" name="organisation_type"
                                data-width="100%" data-live-search="true" data-none-selected-text="None selected">
                                <option value=""></option>
                                <?php
                                $orgType = [
                                    'P' => 'Proprietorship',
                                    'F' => 'Partnership',
                                    'C' => 'Private Limited',
                                    'T' => 'Society / Trust / Club',
                                    'G' => 'Government Department/Body',
                                    'L' => 'Local Authority',
                                    'H' => 'Hindu Undivided Family (HUF)'
                                ];
                                foreach ($orgType as $key => $value) {
                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>GST
                                Category</label>
                            <select class="selectpicker form-control" id="gst_type" name="gst_type" data-width="100%"
                                data-live-search="true" data-none-selected-text="None selected">
                                <option value=""></option>
                                <option value="Registered">Registered</option>
                                <option value="Un-Registered">Un-Registered</option>
                                <option value="Composition">Composition</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Billing
                                Country</label>
                            <select class="selectpicker form-control" id="country" name="country" data-width="100%"
                                data-live-search="true" data-none-selected-text="None selected" required>
                                <option value="India">India</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Billing
                                Pincode</label>
                            <input type="text" class="form-control" id="zip" name="zip"
                                onkeypress="return isNumber(event)" maxlength="6" minlength="6" pattern="[0-9]{6}"
                                required>
                            <!-- <small class="form-text text-muted">6 digit pincode</small> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Billing
                                State</label>
                            <select class="selectpicker form-control" id="state" name="state" data-width="100%"
                                data-live-search="true" data-none-selected-text="None selected" required>
                                <option value=""></option>
                                <?php foreach ($state as $st) { ?>
                                <option value="<?php echo $st['short_name']; ?>">
                                    <?php echo $st['state_name']; ?>
                                </option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="hiddenState" id="hiddenState" value="">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Billing
                                City</label>
                            <select class="selectpicker form-control" id="city" name="city" data-width="100%"
                                data-live-search="true" data-none-selected-text="None selected" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Mobile</label>
                            <input type="text" class="form-control" id="phonenumber" name="phonenumber"
                                onkeypress="return isNumber(event)" maxlength="10" minlength="10" pattern="[0-9]{10}"
                                required>
                            <!-- <small class="form-text text-muted">10 digit mobile number</small> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Alternate Mobile</label>
                            <input type="text" class="form-control" id="altphonenumber" name="altphonenumber"
                                onkeypress="return isNumber(event)" maxlength="10" minlength="10" pattern="[0-9]{10}">
                            <!-- <small class="form-text text-muted">10 digit mobile number</small> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" required>
                            <span class="email_error" style="color:red;"></span>
                            <!-- <small class="form-text text-muted">Valid email address required</small> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">TDS</label>
                            <select class="selectpicker" id="Tds" name="Tds" data-width="100%">
                                <option value="">Non Selected</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Billing Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-2" id="TdsSec" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">TDS Section</label>
                            <select class="selectpicker" id="Tdsselection" name="Tdsselection" data-width="100%">
                                <option value="">Non Selected</option>
                                <?php if (isset($Tdssection)) {
											foreach ($Tdssection as $w): ?>
                                <option value="<?= $w['TDSCode'] ?>"><?= $w['TDSName'] ?></option>
                                <?php endforeach;
										} ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2" id="TdsPercent1" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">TDS Rate (%)</label>
                            <select class="selectpicker" id="TdsPercent" name="TdsPercent" data-width="100%">
                                <option value="">Non Selected</option>
                            </select>
                        </div>
                    </div>


                </div>
                <!-- Contact Details Section -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="bold p_style">Contact Details</h4>
                        <hr class="hr_style">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="contactTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Mobile Number </th>
                                        <th>Email </th>
                                        <th>Send SMS</th>
                                        <th>Send Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="contacttbody">
                                    <tr>
                                        <td><input type="text" id="cp_name" class="form-control cp_name_input">
                                        </td>
                                        <td>
                                            <select class="selectpicker cp_designation_input" id="cp_designation"
                                                name="cp_designation" data-width="100%">
                                                <option value="">Non Selected</option>
                                                <?php foreach ($position as $key => $value) { ?>
                                                <option value="<?php echo $value['position_id']; ?>">
                                                    <?php echo $value['position_name']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input type="text" id="cp_mobile" class="form-control cp_mobile_input"
                                                maxlength="10" minlength="10" pattern="[0-9]{10}"
                                                onkeypress="return isNumber(event)" required
                                                title="10 digit mobile number required"></td>
                                        <td><input type="email" id="cp_email" class="form-control cp_email_input"
                                                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" required
                                                title="Valid email address required"></td>
                                        <td class="text-center"><input type="checkbox" id="cp_send_sms"
                                                class="cp_sms_checkbox"><label></label></td>
                                        <td class="text-center"><input type="checkbox" id="cp_send_email"
                                                class="cp_email_checkbox"><label></label></td>
                                        <td><button type="button" class="btn btn-success" onclick="addContactRow()"><i
                                                    class="fa fa-plus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- <small class="form-text text-muted">
										<strong>Note:</strong> Mobile Number and Email are required. Phone Number is optional. At least one of "Send SMS" or "Send Email" must be selected.
									</small> -->
                    </div>
                </div>
                <!-- Location Table -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="bold p_style">Location Information</h4>
                        <hr class="hr_style">
                        <div class="table-responsive" style="overflow: visible !important;">
                            <table class="table table-bordered" id="locationTable"
                                style="position: relative; z-index: 1;">
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
                                <tbody id="locationtbody">
                                    <tr>

                                        <td><input type="text" class="form-control" name="loc_pincode[]"
                                                onkeypress="return isNumber(event)" maxlength="6" required></td>

                                        <td><textarea class="form-control" name="loc_address[]" rows="1"></textarea>
                                        </td>
                                        <td>
                                            <select class="selectpicker form-control loc_state" name="loc_state[]"
                                                required>
                                                <option value="">Select State</option>
                                                <?php foreach ($state as $st) { ?>
                                                <option value="<?php echo $st['short_name']; ?>">
                                                    <?php echo $st['state_name']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="selectpicker form-control loc_city" name="loc_city[]"
                                                required>
                                                <option value="">Select City</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control" name="loc_mobile[]"
                                                onkeypress="return isNumber(event)" maxlength="10" required>
                                        </td>
                                        <td><button type="button" class="btn btn-success" id="add_location_btn"
                                                onclick="addLocationRow()"><i class="fa fa-plus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Banking & Payment / Other Information -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="bold p_style">Credit / Payment Bank Information</h4>
                        <hr class="hr_style">
                    </div>


                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Payment Terms</label>
                            <select class="selectpicker form-control" id="payment_terms" name="payment_terms" required
                                data-live-search="true" data-none-selected-text="None selected">
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
                            <select class="selectpicker form-control" id="payment_cycle_type" name="payment_cycle_type"
                                required data-live-search="true" data-none-selected-text="None selected">
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
                            <select class="selectpicker form-control" id="payment_cycle" name="payment_cycle" required
                                data-live-search="true" data-none-selected-text="None selected">
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
                            <input type="number" class="form-control" id="credit_days" name="credit_days" value="0"
                                required>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Credit
                                Limit</label>
                            <input type="text" id="MaxCrdAmt" name="MaxCrdAmt" class="form-control numbersOnly"
                                required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Current Outstanding</label>
                            <input type="text" name="current_outstanding" id="current_outstanding" class="form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Open Order Value</label>
                            <input type="text" name="open_order_value" id="open_order_value" class="form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Available Limit</label>
                            <input type="text" name="available_limit" id="available_limit" class="form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Average Payment Day</label>
                            <input type="text" name="avg_payment_day" id="avg_payment_day" class="form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Average Rebate</label>
                            <input type="text" name="avg_rebate" id="avg_rebate" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Default Currency</label>
                            <select name="default_currency" id="default_currency" class="selectpicker form-control"
                                required data-live-search="true" data-none-selected-text="None selected">
                                <option value=""></option>
                                <?php foreach ($currencies as $key => $value) { ?>
                                <option value="<?php echo $value['id']; ?>"
                                    <?php echo ($value['isdefault'] == 1) ? 'selected' : ''; ?>>
                                    <?php echo $value['name']; ?>
                                </option>
                                <?php } ?>
                            </select>
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
                            <label class="control-label">Is Bank Detail</label>
                            <select name="is_bank_detail" id="is_bank_detail" class="form-control selectpicker"
                                data-live-search="true" data-none-selected-text="None selected">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 bank_detail_section" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" maxlength="11">
                        </div>
                    </div>

                    <div class="col-md-2 bank_detail_section" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="col-md-2 bank_detail_section" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">Branch Name</label>
                            <input type="text" name="branch_name" id="branch_name" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="col-md-2 bank_detail_section" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">Bank Address</label>
                            <input type="text" name="bank_address" id="bank_address" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="col-md-2 bank_detail_section" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-2 bank_detail_section" style="display:none;">
                        <div class="form-group">
                            <label class="control-label">Account Holder Name</label>
                            <input type="text" name="account_holder_name" id="account_holder_name" class="form-control"
                                readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Is Active</label>
                            <select name="Blockyn" id="Blockyn" class="form-control selectpicker"
                                data-live-search="true" data-none-selected-text="None selected">
                                <option value=""></option>
                                <option value="Y">Yes</option>
                                <option value="N">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Blocked Reason</label>
                            <textarea name="blocked_reason" id="blocked_reason" class="form-control"></textarea>
                        </div>
                    </div>




                </div>

                <!-- Other Information -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="bold p_style">Other Information</h4>
                        <hr class="hr_style">
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Broker</label>
                            <select name="broker" id="broker" class="selectpicker form-control" data-live-search="true"
                                data-none-selected-text="None selected" required>
                                <option value=""></option>
                                <?php foreach ($Broker as $key => $value) { ?>
                                <option value="<?php echo $value['SubActGroupID']; ?>">
                                    <?php echo $value['SubActGroupName']; ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label"><small class="req text-danger">* </small>Broker
                                Person</label>
                            <select name="broker_person" id="broker_person" class="selectpicker form-control"
                                data-live-search="true" data-none-selected-text="None selected" required>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">TAN Number</label>
                            <input type="text" id="tan_number" name="tan_number" class="form-control" required>
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
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">FSSAI Lic No.</label>
                            <input type="text" class="form-control" id="FLNO1" name="FLNO1" maxlength="14"
                                minlength="14" onkeypress="return isNumber(event)" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <?php $value2 = date('d/m/Y'); ?>
                        <?php echo render_date_input('expiry_licence', 'FSSAI Validity Date', $value2); ?>
                    </div>
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
                            <label class="control-label">Website</label>
                            <input type="text" class="form-control" id="website" name="website">
                        </div>
                    </div>



                    <!-- <div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Temporary Account Type</label>
										<select name="temp_account_type" id="temp_account_type" class="selectpicker form-control"><option value="">Select</option></select>
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Temporary Account Number</label>
										<input type="text" name="temp_account_number" id="temp_account_number" class="form-control">
									</div>
								</div> -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Attachment</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label">Additional Information</label>
                            <textarea name="additional_info" id="additional_info" class="form-control"></textarea>
                        </div>
                    </div>



                </div>
                <!-- Audit Information -->
                <!-- <div class="row">
								<div class="col-md-12"><h4 class="bold p_style">Audit Information</h4><hr class="hr_style"></div>
								<div class="col-md-2">
									<div class="form-group"><label class="control-label">Created By</label><input type="text" id="created_by" class="form-control" readonly></div>
								</div>
								<div class="col-md-2">
									<div class="form-group"><label class="control-label">Created On</label><input type="text" id="created_on" class="form-control" readonly></div>
								</div>
								<div class="col-md-2">
									<div class="form-group"><label class="control-label">Updated By</label><input type="text" id="updated_by" class="form-control" readonly></div>
								</div>
								<div class="col-md-2">
									<div class="form-group"><label class="control-label">Updated On</label><input type="text" id="updated_on" class="form-control" readonly></div>
								</div>
							</div> -->
                <div class="clearfix"></div>
                <br><br>
                <div class="row">
                    <div class="btn-bottom-toolbar text-right" style="left:-214px">
                        <div class="col-md-12">
                            <div class="action-buttons">
                                <?php if (has_permission('customers', '', 'create')) { ?>
                                <button type="submit" class="btn btn-success saveBtn"
                                    formaction="<?= admin_url('clients/client') ?>">
                                    <i class="fa fa-save"></i> Save
                                </button>
                                <?php } ?>
                                <?php if (has_permission('customers', '', 'edit')) { ?>
                                <button type="submit" class="btn btn-success updateBtn" style="display:none;"
                                    formaction="<?= admin_url('clients/client/' . (isset($client) ? $client->userid : '')) ?>">
                                    <i class="fa fa-save"></i> Update
                                </button>
                                <?php } ?>
                                <button type="button" class="btn btn-warning cancelBtn">
                                    <i class="fa fa-refresh"></i> Reset
                                </button>
                                <!-- Show All button: opens Account List and fetches all accounts -->
                                <button type="button" class="btn btn-info showAllBtn" title="Show All Accounts">
                                    <i class="fa fa-list"></i> Show All
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <!-- Iteme List Model-->

                    <div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog"
                        data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Account List</h4>
                                </div>
                                <div class="modal-body" style="padding:0px 5px !important">

                                    <div class="table-Account_List tableFixHead2">
                                        <table
                                            class="tree table table-striped table-bordered table-Account_List tableFixHead2"
                                            id="table_Account_List" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:left;" class="sortablePop">Customer
                                                        Code</th>
                                                    <th style="text-align:left;" class="sortablePop">Customer Name</th>
                                                    <th style="text-align:left;" class="sortablePop">Favouring Name
                                                    </th>
                                                    <th style="text-align:left;" class="sortablePop">PAN NO
                                                    </th>
                                                    <th style="text-align:left;" class="sortablePop">GSTIN Type</th>
                                                    <th style="text-align:left;" class="sortablePop">Organisation Type
                                                    </th>
                                                    <th style="text-align:left;" class="sortablePop">GST Type</th>
                                                    <th style="text-align:left;" class="sortablePop">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="customertlistbody">

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
    let selectedIds = [];
    let selectedNames = [];

    if (selectedPosition) {
        // Handle comma-separated position IDs
        let positionList = selectedPosition.toString().split(',').map(p => p.trim()).filter(p => p);

        // For each position ID, find matching record
        positionList.forEach(function(posId) {
            positionData.forEach(function(pos) {
                if (pos.position_id == posId || pos.position_name == posId) {
                    if (selectedIds.indexOf(pos.position_id) === -1) { // Avoid duplicates
                        selectedIds.push(pos.position_id);
                        selectedNames.push(pos.position_name);
                    }
                }
            });
        });

        // Add selected positions at the top
        if (selectedIds.length > 0) {
            let selectedDisplay = selectedNames.join(', '); // Comma-separated display
            html = '<option value="' + selectedIds.join(',') + '" selected>' + selectedDisplay + '</option>' + html;
        }
    }

    // Add all positions (skip already-added selectedIds)
    positionData.forEach(function(pos) {
        if (selectedIds.indexOf(pos.position_id) === -1) {
            html += '<option value="' + pos.position_id + '">' + pos.position_name + '</option>';
        }
    });
    return html;
}

// Location Table Population Functions
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
    // Click add location button - ADJUST SELECTOR AS PER YOUR HTML
    $('#add_location_btn').trigger('click');

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
        url: "<?php echo base_url(); ?>admin/clients/GetCity",
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

// Populate Contact Details table from GST response (promoters & principal contact)
function populateContactTableFromGSTData(apiResponse) {
    if (!apiResponse || !apiResponse.data || !apiResponse.data.details) return;
    let details = apiResponse.data.details;
    let promoters = details.promoters || [];
    let contactPrincipal = (details.contact_details && details.contact_details.principal) ? details.contact_details
        .principal : {};
    let email = contactPrincipal.email || apiResponse.data.email || '';
    let rawMobile = contactPrincipal.mobile || apiResponse.data.mobile || '';
    let mobile = rawMobile ? rawMobile.replace(/\D/g, '').slice(-10) : '';

    // Remove existing added contact rows and clear inputs
    $("#contacttbody tr.addedtr_contact").remove();
    $("#cp_name").val('');
    $("#cp_designation").val('');
    $("#cp_mobile").val('');
    $("#cp_email").val('');
    $("#cp_send_email").prop('checked', false);
    $("#cp_send_sms").prop('checked', false);

    function esc(s) {
        return s ? s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, "&#39;")
            .replace(/\"/g, '&quot;') : '';
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
            "<td><select class='selectpicker form-control cp_designation_input' name='cp_designation[]' data-width='100%'>" +
            getPositionOptions() + "</select></td>");
        newRow.append("<td><input type='text' name='cp_mobile[]' class='form-control' value='" + esc(
                mobile) +
            "' maxlength='10' minlength='10' pattern='[0-9]{10}' onkeypress='return isNumber(event)' required></td>"
        );
        newRow.append("<td><input type='email' name='cp_email[]' class='form-control' value='" + esc(
            email) + "' pattern='[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}' required></td>");
        newRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_sms[]' " +
            (sendSMSChecked ? '' : '') + "><label></label></div></td>");
        newRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_email[]' " +
            (sendEmailChecked ? '' : '') + "><label></label></div></td>");
        newRow.append(
            "<td><a href='#' class='btn btn-danger removebtn_contact'><i class='fa fa-times'></i></a></td>"
        );
        $("#contacttbody").append(newRow);
        newRow.find('.selectpicker').selectpicker();
        newRow.find('.selectpicker').selectpicker('refresh');
    });

    // If no promoters present, add principal as a contact row so user sees it
    if (promoters.length === 0 && (email || mobile)) {
        var principalRow = $("<tr class='addedtr_contact'></tr>");
        principalRow.append("<td><input type='text' name='cp_name[]' class='form-control' value='Principal'></td>");
        principalRow.append(
            "<td><select class='selectpicker form-control cp_designation_input' name='cp_designation[]' data-width='100%'>" +
            getPositionOptions() + "</select></td>");
        principalRow.append("<td><input type='text' name='cp_mobile[]' class='form-control' value='" + esc(mobile) +
            "' maxlength='10' minlength='10' pattern='[0-9]{10}' onkeypress='return isNumber(event)' required></td>"
        );
        principalRow.append("<td><input type='email' name='cp_email[]' class='form-control' value='" + esc(email) +
            "' pattern='[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}' required></td>");
        principalRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_sms[]' " + (
                mobile ? '' : '') + "><label></label></div></td>");
        principalRow.append(
            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_email[]' " + (
                email ? '' : '') + "><label></label></div></td>");
        principalRow.append(
            "<td><a href='#' class='btn btn-danger removebtn_contact'><i class='fa fa-times'></i></a></td>");
        $("#contacttbody").append(principalRow);
        principalRow.find('.selectpicker').selectpicker();
        principalRow.find('.selectpicker').selectpicker('refresh');
    }
}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 &&
        (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}


$(document).ready(function() {
    // Handle filter dropdowns outside table
    $('#filter_state').on('change', function() {
        var StateID = $(this).val();
        var url = "<?php echo base_url(); ?>admin/clients/GetCity";
        if (StateID !== '') {
            jQuery.ajax({
                type: 'POST',
                url: url,
                data: {
                    StateID: StateID
                },
                dataType: 'json',
                success: function(data) {
                    $("#filter_city").find('option').remove();
                    $("#filter_city").append(new Option('None selected', ""));
                    for (var i = 0; i < data.length; i++) {
                        $("#filter_city").append(new Option(data[i].city_name, data[i]
                            .id));
                    }
                    $('#filter_city').selectpicker('refresh');
                }
            });
        } else {
            $("#filter_city").find('option').remove();
            $("#filter_city").append(new Option('None selected', ""));
            $('#filter_city').selectpicker('refresh');
        }
    });

    function getSubGroupsByMain(Tdsselection) {
        $.ajax({
            url: "<?php echo admin_url(); ?>clients/gettdspercent",
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
                        $('#TdsPercent').append('<option value="' + item.rate + '">' +
                            item.rate + '</option>');
                    });
                } else {
                    $('#TdsPercent').append('<option value="">Non Selected</option>');
                }
                $('.selectpicker').selectpicker('refresh');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown);
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
    $('#Tds').on('change', function() {
        checkTds();
    });

    // Bank Detail Show/Hide
    function checkBankDetail() {
        if ($('#is_bank_detail').val() === "1") {
            $('.bank_detail_section').show();
        } else {
            $('.bank_detail_section').hide();
        }
    }
    $('#is_bank_detail').on('change', function() {
        checkBankDetail();
    });

    // Is Active - Blocked Reason Show/Hide
    function checkBlockedReason() {
        // Clear all existing rows and rebuild from GSTIN data
        $('#locationtbody').empty();

        if (gstinData && gstinData.details && gstinData.details.contact_details && gstinData.details
            .contact_details.additional) {
            let additionalContacts = gstinData.details.contact_details.additional;

            additionalContacts.forEach(function(location) {
                let fullAddress = location.address || '';
                let mobileRaw = location.mobile || '';
                let mobile = mobileRaw.replace(/\D/g, '').slice(-10);

                let addressParts = fullAddress ? fullAddress.split(',').map(part => part.trim()) : [];
                let pincode = '';
                let state = '';
                let city = '';

                // Extract pincode if present as last token
                if (addressParts.length > 0) {
                    let last = addressParts[addressParts.length - 1];
                    if (/^\d{6}$/.test(last)) {
                        pincode = last;
                        addressParts.pop();
                    }
                }

                // Extract state and city if present
                if (addressParts.length > 0) {
                    state = addressParts[addressParts.length - 1];
                    addressParts.pop();
                }
                if (addressParts.length > 0) {
                    city = addressParts[addressParts.length - 1];
                    addressParts.pop();
                }

                let addressWithoutPinStateCity = addressParts.join(', ');

                // Build row HTML (use server-side state options)
                let row = `
						<tr class="addedtr_location">
							<td><input type="text" class="form-control" name="loc_pincode[]" value="${pincode}" onkeypress="return isNumber(event)" maxlength="6"></td>
							<td><textarea class="form-control" name="loc_address[]" rows="1">${addressWithoutPinStateCity || fullAddress}</textarea></td>
							<td>
								<select class="form-control loc_state" name="loc_state[]">
									<option value="">Select State</option>
									<?php foreach ($state as $st) { ?>
										<option value="<?php echo $st['short_name']; ?>"><?php echo $st['state_name']; ?></option>
									<?php } ?>
								</select>
							</td>
							<td>
								<select class="form-control loc_city" name="loc_city[]">
									<option value="">Select City</option>
								</select>
							</td>
							<td><input type="text" class="form-control" name="loc_mobile[]" value="${mobile}" onkeypress="return isNumber(event)" maxlength="10"></td>
							<td><button type="button" class="btn btn-danger removeLocationBtn"><i class="fa fa-times"></i></button></td>
						</tr>
						`;

                $('#locationtbody').append(row);

                let $newRow = $('#locationtbody tr:last');
                let $stateSelect = $newRow.find('.loc_state');
                let $citySelect = $newRow.find('.loc_city');

                // Try to select state by matching text
                if (state) {
                    $stateSelect.find('option').each(function() {
                        if ($(this).text().toLowerCase().indexOf(state.toLowerCase()) !== -
                            1) {
                            $stateSelect.val($(this).val());
                            return false;
                        }
                    });
                }

                // Populate cities for selected state and select matching city if available
                let stateVal = $stateSelect.val();
                if (stateVal) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>admin/clients/GetCity",
                        type: 'POST',
                        data: {
                            StateID: stateVal
                        },
                        dataType: 'json',
                        success: function(cityData) {
                            $citySelect.empty();
                            $citySelect.append(new Option('Select City', ''));
                            cityData.forEach(function(cityItem) {
                                $citySelect.append(new Option(cityItem
                                    .city_name, cityItem.id));
                            });
                            if (city) {
                                $citySelect.find('option').each(function() {
                                    if ($(this).text().toLowerCase().indexOf(
                                            city.toLowerCase()) !== -1) {
                                        $citySelect.val($(this).val());
                                        return false;
                                    }
                                });
                            }
                        }
                    });
                }
            });

            // If no locations were added, keep a single empty row
            if ($('#locationtbody tr').length === 0) {
                addLocationRow();
            }

            alert_float('success', 'Location data populated from GSTIN');
        }
        $('.selectpicker').selectpicker('refresh');

        if (data.Tds == "1") {
            $('#TdsPercent1').show();
            $('#TdsSec').show();
            $('select[name=Tds]').val(data.Tds);
            $('.selectpicker').selectpicker('refresh');
            $('select[name=Tdsselection]').val(data.Tdsselection);
            $('.selectpicker').selectpicker('refresh');

            $.ajax({
                url: "<?php echo admin_url(); ?>clients/gettdspercent",
                dataType: "JSON",
                method: "POST",
                data: {
                    Tdsselection: data.Tdsselection
                },
                success: function(res) {
                    $('#TdsPercent').empty();
                    $('#TdsPercent').append('<option value="">Non Selected</option>');
                    $.each(res, function(index, item) {
                        $('#TdsPercent').append('<option value="' + item.rate + '">' +
                            item.rate + '</option>');
                    });
                    $('select[name=TdsPercent]').val(data.TdsPercent);
                    $('.selectpicker').selectpicker('refresh');
                }
            });
        } else {
            $('#TdsPercent1').hide();
            $('#TdsSec').hide();
            $('select[name=Tds]').val('0');
        }
        $('select[name=default_currency]').val(data.default_currency);
        $('select[name=temp_account_type]').val(data.temp_account_type);
        $('select[name=payment_terms]').val(data.payment_terms);
        $('select[name=payment_cycle]').val(data.payment_cycle);
        $('select[name=payment_cycle_type]').val(data.payment_cycle_type);
        $('.selectpicker').selectpicker('refresh');

        $('#city').selectpicker('val', data.city);
        $('.selectpicker').selectpicker('refresh');

        $('select[name=groups_in]').val(data.DistributorType);
        $('.selectpicker').selectpicker('refresh');

        $('select[name=state]').val(data.state);
        $('.selectpicker').selectpicker('refresh');
        $("#hiddenState").val(data.state);

        <?php
				if (has_permission_new('openingbaledit', '', 'edit')) {
					?>
        var is_accessable = 1;
        <?php
				} else {
					?>
        var is_accessable = 0;
        <?php
				}
				?>
        if (is_accessable == "0") {}

        $('select[name=is_bank_detail]').val(data.is_bank_detail);
        checkBankDetail();
        $('.selectpicker').selectpicker('refresh');

        var contactdetails = data.contactdetails;
        populateContactData(contactdetails);

        $('.saveBtn').hide();
        $('.updateBtn').show();
        $('.saveBtn2').hide();
        $('.updateBtn2').show();
    }

    $("#AccountID").focus(function() {
        ResetForm();
    });

    $("#AccountID").dblclick(function() {
        $('#Account_List').modal('show');
        $.ajax({
            url: "<?php echo admin_url(); ?>clients/GetAllCustomerList",
            dataType: "JSON",
            method: "POST",
            beforeSend: function() {
                $('.searchh2').css('display', 'block');
                $('.searchh2').css('color', 'blue');
            },
            complete: function() {
                $('.searchh2').css('display', 'none');
            },
            success: function(data) {
                $('#customertlistbody').html(data);
                $('.get_AccountID').on('click', function() {
                    AccountID = $(this).attr("data-id");
                    $.ajax({
                        url: "<?php echo admin_url(); ?>clients/GetComprehensiveAccountData",
                        dataType: "JSON",
                        method: "POST",
                        data: {
                            AccountID: AccountID
                        },
                        beforeSend: function() {
                            $('.searchh2').css('display', 'block');
                            $('.searchh2').css('color', 'blue');
                            // Toggle buttons immediately when record is clicked
                            $('.saveBtn').hide();
                            $('.updateBtn').show();
                            $('.saveBtn2').hide();
                            $('.updateBtn2').show();
                        },
                        complete: function() {
                            $('.searchh2').css('display', 'none');
                        },
                        success: function(response) {
                            console.log('Comprehensive data response:',
                                response);
                            if (response.status === 'success') {
                                PopulateFormFromComprehensiveData(
                                    response.data);
                            } else {
                                alert_float('error', response.message ||
                                    'Failed to fetch account data');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('AJAX Error:', textStatus,
                                errorThrown);
                            alert_float('error',
                                'Error fetching account data');
                        }
                    });
                    $('#Account_List').modal('hide');
                });
            }
        });
        $('#Account_List').on('shown.bs.modal', function() {
            $('#myInput1').val('');
            $('#myInput1').focus();
        })

    });

    // Show All button click - fetch and show all accounts (same behaviour as AccountID dblclick)
    $('.showAllBtn').on('click', function() {
        $('#Account_List').modal('show');
        $.ajax({
            url: "<?php echo admin_url(); ?>clients/GetAllCustomerList",
            dataType: "JSON",
            method: "POST",
            beforeSend: function() {
                $('.searchh2').css('display', 'block');
                $('.searchh2').css('color', 'blue');
            },
            complete: function() {
                $('.searchh2').css('display', 'none');
            },
            success: function(data) {
                $('#customertlistbody').html(data);
                $('.get_AccountID').on('click', function() {
                    AccountID = $(this).attr("data-id");
                    $.ajax({
                        url: "<?php echo admin_url(); ?>clients/GetComprehensiveAccountData",
                        dataType: "JSON",
                        method: "POST",
                        data: {
                            AccountID: AccountID
                        },
                        beforeSend: function() {
                            $('.searchh2').css('display', 'block');
                            $('.searchh2').css('color', 'blue');
                            // Toggle buttons immediately when record is clicked
                            $('.saveBtn').hide();
                            $('.updateBtn').show();
                            $('.saveBtn2').hide();
                            $('.updateBtn2').show();
                        },
                        complete: function() {
                            $('.searchh2').css('display', 'none');
                        },
                        success: function(response) {
                            console.log('Comprehensive data response:',
                                response);
                            if (response.status === 'success') {
                                PopulateFormFromComprehensiveData(
                                    response.data);
                            } else {
                                alert_float('error', response.message ||
                                    'Failed to fetch account data');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('AJAX Error:', textStatus,
                                errorThrown);
                            alert_float('error',
                                'Error fetching account data');
                        }
                    });
                    $('#Account_List').modal('hide');
                });
            }
        });

        $('#Account_List').on('shown.bs.modal', function() {
            $('#myInput1').val('');
            $('#myInput1').focus();
        });
    });

    $("#firstname, #lastname").keypress(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == "") {
            $("#lblError").html("");
        } else {
            var regex = /^[A-Za-z\s]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });

    $("#AccoountName").keypress(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == "") {
            $("#lblError").html("");
        } else {
            var regex = /^[A-Za-z0-9\s]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });

    $("#AccountID").keypress(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == "") {
            $("#lblError").html("");
        } else {
            var regex = /^[A-Za-z0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });

    $("#vat").keypress(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == "") {
            $("#lblError").html("");
        } else {
            var regex = /^[A-Za-z0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });

    $("#Pan").keypress(function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == "") {
            $("#lblError").html("");
        } else {
            var regex = /^[A-Za-z0-9]+$/;
            var isValid = regex.test(String.fromCharCode(keyCode));
            return isValid;
        }
    });

    function fetchAccountHolderName() {
        var bank_ac_no = $('#account_number').val();
        var ifsc_code = $('#ifsc_code').val();
        if (bank_ac_no != '' && ifsc_code != '') {
            $.ajax({
                url: "<?php echo admin_url(); ?>purchase/verifyBankAccount",
                method: "POST",
                dataType: 'json',
                data: {
                    bank_ac_no: bank_ac_no,
                    ifsc_code: ifsc_code
                },
                beforeSend: function() {
                    $('.searchh2').css('display', 'block');
                    $('.searchh2').css('color', 'blue');
                },
                complete: function() {
                    $('.searchh2').css('display', 'none');
                },
                success: function(data) {
                    if (data.success == false) {
                        alert_float('danger', "Bank account not verified");
                        $('#account_holder_name').val('');
                        $('#account_number').val('');
                    } else {
                        $('#account_holder_name').val(data.data.full_name);
                    }
                }
            });
        }
    }

    /**
     * Populate form with comprehensive account data from all tables
     * @param {Object} comprehensiveData - Contains clientDetails, shippingData, bankData, contactData
     */
    function PopulateFormFromComprehensiveData(comprehensiveData) {
        try {
            // First, clear all existing data
            ResetForm();

            // 1. POPULATE CLIENT DETAILS (from tblclients)
            if (comprehensiveData && comprehensiveData.clientDetails) {
                var client = comprehensiveData.clientDetails;

                $('#AccountID').val(client.AccountID || '');
                $('#AccoountName').val(client.company || '');
                $('#favouring_name').val(client.FavouringName || '');
                $('#Pan').val(client.PAN || '');
                $('#vat').val(client.GSTIN || '');
                $('#organisation_type').val(client.OrganisationType || '');
                $('#gst_type').val(client.GSTType || '');
                $('#groups_in').val(client.ActSubGroupID2 || '');

                // Handle Billing Country - if value is "0" or empty, set to "India"
                var countryValue = client.billing_country || '0';
                if (countryValue === '0' || countryValue === '' || countryValue === 'India') {
                    $('#country').val('India');
                } else {
                    $('#country').val(countryValue);
                }

                $('#zip').val(client.billing_zip || '');
                $('#state').val(client.billing_state || '');

                // After setting state, load cities for that state
                var stateVal = client.billing_state || '';
                if (stateVal) {
                    $.ajax({
                        url: "<?php echo base_url(); ?>admin/clients/GetCity",
                        type: 'POST',
                        data: {
                            StateID: stateVal
                        },
                        dataType: 'json',
                        success: function(cityData) {
                            $("#city").find('option').remove();
                            $("#city").append(new Option('Select City', ''));
                            if (cityData && cityData.length > 0) {
                                cityData.forEach(function(city) {
                                    $("#city").append(new Option(city.city_name, city.id));
                                });
                                // Set the city value after options are loaded
                                if (client.billing_city) {
                                    $('#city').val(client.billing_city);
                                }
                            }
                            $('.selectpicker').selectpicker('refresh');
                        }
                    });
                }

                $('#address').val(client.billing_address || '');
                $('#phonenumber').val(client.MobileNo || '');
                $('#altphonenumber').val(client.AltMobileNo || '');
                $('#email').val(client.Email || '');

                // Payment and credit fields
                $('#payment_terms').val(client.PaymentTerms || '');
                $('#payment_cycle_type').val(client.PaymentCycleType || '');
                $('#payment_cycle').val(client.PaymentCycle || '');
                $('#credit_days').val(client.GraceDay || '0');
                $('#MaxCrdAmt').val(client.CreditLimit || '');

                // Other fields
                $('#Tds').val(client.IsTDS == 'Y' ? '1' : '0');
                if (client.IsTDS == 'Y') {
                    $('#TdsSec').show();
                    $('#TdsPercent1').show();
                    $('#Tdsselection').val(client.TDSSection || '');
                    
                    // Load TDS rates first, then set the value
                    var TDSSection = client.TDSSection || '';
                    if (TDSSection) {
                        $.ajax({
                            url: "<?php echo admin_url(); ?>clients/gettdspercent",
                            dataType: "JSON",
                            method: "POST",
                            data: {
                                Tdsselection: TDSSection
                            },
                            success: function(data) {
                                $('#TdsPercent').empty();
                                if (data && data.length > 0) {
                                    $('#TdsPercent').append('<option value="">Non Selected</option>');
                                    $.each(data, function(index, item) {
                                        $('#TdsPercent').append('<option value="' + item.rate + '">' +
                                            item.rate + '</option>');
                                    });
                                    // Set the value after options are loaded
                                    $('#TdsPercent').val(client.TDSPer || '');
                                    $('#TdsPercent').selectpicker('refresh');
                                } else {
                                    $('#TdsPercent').append('<option value="">Non Selected</option>');
                                    $('#TdsPercent').selectpicker('refresh');
                                }
                            },
                            error: function() {
                                $('#TdsPercent').empty().append('<option value="">Non Selected</option>');
                                $('#TdsPercent').selectpicker('refresh');
                            }
                        });
                    }
                }

                $('#tan_number').val(client.TAN || '');
                $('#priority').val(client.PriorityID || '');
                $('#FLNO1').val(client.FSSAINo || '');
                $('#territory').val(client.TerritoryID || '');
                $('#website').val(client.website || '');
                $('#additional_info').val(client.AdditionalInfo || '');
                $('#Blockyn').val(client.IsActive || '');
                $('#blocked_reason').val(client.DeActiveReason || '');
                $('#default_currency').val(client.default_currency || '');
                $('#freight_terms').val(client.FreightTerms || '');

                // Populate attachment if exists
                if (client.Attachment && client.Attachment.trim() !== '') {
                    var attachmentPath = client.Attachment;
                    var fileName = attachmentPath.split('/').pop(); // Get filename from path
                    var fullUrl = '<?php echo base_url(); ?>' + attachmentPath;
                    
                    // Create attachment display with download link and clear button
                    var attachmentHtml = '<div style="padding: 12px; background-color: #e8f4f8; border-left: 4px solid #0275d8; border-radius: 4px; height: 100%;">' +
                        '<label style="margin: 0; margin-bottom: 8px; display: block; font-weight: 600; color: #333;"><i class="fa fa-file"></i> Download Attachment:</label>' +
                        '<a href="' + fullUrl + '" download="' + fileName + '" oncontextmenu="return true;" style="color: #0275d8; text-decoration: none; font-weight: 500; cursor: pointer; display: block; word-break: break-all; margin-bottom: 8px;" title="Right-click to download">' +
                        '<i class="fa fa-download"></i> ' + fileName + '</a>' +
                        '<button type="button" class="btn btn-danger btn-sm clearAttachmentBtn" style="width: 25%; margin-top: 8px;" title="Remove this attachment">' +
                        '<i class="fa fa-trash"></i> Remove</button>' +
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

                // Refresh selectpicker for dropdowns
                $('.selectpicker').selectpicker('refresh');

                console.log('Client details populated successfully');
            }

            // 2. POPULATE CONTACT DETAILS (from tblcontacts)
            if (comprehensiveData && comprehensiveData.contactData && comprehensiveData.contactData.length >
                0) {
                // Clear existing contact rows (except first one)
                $('#contacttbody tr.addedtr_contact').remove();

                // Populate first row or create new rows
                comprehensiveData.contactData.forEach(function(contact, index) {
                    if (index === 0) {
                        // Use first row - rebuild designation select to handle multiple positions
                        $('#cp_name').val(contact.firstname || '');

                        // Rebuild the designation select for the first row to support multiple designations
                        var designationSelectHtml = getPositionOptions(contact.PositionID || '');
                        $('#cp_designation').closest('td').html(
                            "<select class='selectpicker cp_designation_input' id='cp_designation' name='cp_designation' data-width='100%'>" +
                            designationSelectHtml + "</select>");
                        $('#cp_designation').selectpicker();
                        $('#cp_designation').selectpicker('refresh');

                        $('#cp_mobile').val(contact.phonenumber || '');
                        $('#cp_email').val(contact.email || '');
                        $('#cp_send_sms').prop('checked', contact.IsSmsYN === 'Y');
                        $('#cp_send_email').prop('checked', contact.IsEmailYN === 'Y');
                    } else {
                        // Add additional rows with complete data
                        var newRow = $("<tr class='addedtr_contact'></tr>");
                        newRow.append(
                            "<td><input type='text' name='cp_name[]' class='form-control' value='" +
                            (contact.firstname || '') + "'></td>");
                        newRow.append(
                            "<td><select class='selectpicker form-control cp_designation_input' name='cp_designation[]' data-width='100%'>" +
                            getPositionOptions(contact.PositionID || '') + "</select></td>");
                        newRow.append(
                            "<td><input type='text' name='cp_mobile[]' class='form-control' value='" +
                            (contact.phonenumber || '') +
                            "' maxlength='10' minlength='10' pattern='[0-9]{10}' onkeypress='return isNumber(event)' required></td>"
                            );
                        newRow.append(
                            "<td><input type='email' name='cp_email[]' class='form-control' value='" +
                            (contact.email || '') +
                            "' pattern='[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}' required></td>"
                            );
                        newRow.append(
                            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_sms[]' " +
                            (contact.IsSmsYN === 'Y' ? 'checked' : '') +
                            "><label></label></div></td>");
                        newRow.append(
                            "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_email[]' " +
                            (contact.IsEmailYN === 'Y' ? 'checked' : '') +
                            "><label></label></div></td>");
                        newRow.append(
                            "<td><a href='#' class='btn btn-danger removebtn_contact'><i class='fa fa-times'></i></a></td>"
                            );
                        $("#contacttbody").append(newRow);
                        newRow.find('.selectpicker').selectpicker();
                        newRow.find('.selectpicker').selectpicker('refresh');
                    }
                });

                console.log('Contact details populated successfully');
            }

            // 3. POPULATE SHIPPING/LOCATION DATA (from tblclientwiseshippingdata)
            if (comprehensiveData && comprehensiveData.shippingData && comprehensiveData.shippingData.length >
                0) {
                // Clear existing location rows (except first one)
                $('#locationtbody tr.addedtr_location').remove();

                // Populate locations with proper city loading
                comprehensiveData.shippingData.forEach(function(location, index) {
                    var pincode = location.ShippingPin || '';
                    var address = location.ShippingAdrees || '';
                    var state = location.ShippingState || '';
                    var city = location.ShippingCity || '';
                    var mobile = location.MobileNo || '';

                    if (index === 0) {
                        // Use first row
                        var $firstRow = $('#locationtbody tr:first');
                        $firstRow.find('input[name="loc_pincode[]"]').val(pincode);
                        $firstRow.find('textarea[name="loc_address[]"]').val(address);
                        $firstRow.find('select[name="loc_state[]"]').val(state);
                        $firstRow.find('input[name="loc_mobile[]"]').val(mobile);

                        // Load cities for first row
                        if (state) {
                            var $citySelect = $firstRow.find('select[name="loc_city[]"]');
                            $.ajax({
                                url: "<?php echo base_url(); ?>admin/clients/GetCity",
                                type: 'POST',
                                data: {
                                    StateID: state
                                },
                                dataType: 'json',
                                success: function(cityData) {
                                    $citySelect.empty();
                                    $citySelect.append(new Option('Select City', ''));
                                    if (cityData && cityData.length > 0) {
                                        cityData.forEach(function(c) {
                                            $citySelect.append(new Option(c
                                                .city_name, c.id));
                                        });
                                        $citySelect.val(city);
                                    }
                                    $citySelect.selectpicker('refresh');
                                }
                            });
                        }
                    } else {
                        // Add new row with all data
                        var newRow = `
                                <tr class="addedtr_location">
                                    <td><input type="text" class="form-control" name="loc_pincode[]" value="${pincode}" onkeypress="return isNumber(event)" maxlength="6" required></td>
                                    <td><textarea name="loc_address[]" class="form-control" rows="1">${address}</textarea></td>
                                    <td>
                                        <select class="selectpicker form-control loc_state" name="loc_state[]" required>
                                            <option value="">Select State</option>
                                            <?php foreach ($state as $st) { ?>
                                            <option value="<?php echo $st['short_name']; ?>"><?php echo $st['state_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="selectpicker form-control loc_city" name="loc_city[]" required>
                                            <option value="">Select City</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="loc_mobile[]" value="${mobile}" onkeypress="return isNumber(event)" maxlength="10" required></td>
                                    <td><button type="button" class="btn btn-danger removeLocationBtn"><i class="fa fa-times"></i></button></td>
                                </tr>`;

                        $('#locationtbody').append(newRow);

                        // Populate state and city for new row
                        var $newRow = $('#locationtbody tr:last');
                        if (state) {
                            $newRow.find('select[name="loc_state[]"]').val(state);

                            // Load cities with delay to ensure row is in DOM
                            setTimeout(function() {
                                var $citySelect = $newRow.find('select[name="loc_city[]"]');
                                $.ajax({
                                    url: "<?php echo base_url(); ?>admin/clients/GetCity",
                                    type: 'POST',
                                    data: {
                                        StateID: state
                                    },
                                    dataType: 'json',
                                    success: function(cityData) {
                                        $citySelect.empty();
                                        $citySelect.append(new Option('Select City',
                                            ''));
                                        if (cityData && cityData.length > 0) {
                                            cityData.forEach(function(c) {
                                                $citySelect.append(
                                                    new Option(c
                                                        .city_name, c.id
                                                        ));
                                            });
                                            $citySelect.val(city);
                                        }
                                        $citySelect.selectpicker('refresh');
                                    }
                                });
                            }, 100);
                        }

                        // Refresh selectpickers for new row
                        $newRow.find('.selectpicker').selectpicker('refresh');
                    }
                });

                // Refresh all selectpicker instances
                $('#locationtbody select').selectpicker('refresh');

                console.log('Shipping/Location details populated successfully');
            }

            // 4. POPULATE BANK DETAILS (from tblBankMaster)
            if (comprehensiveData && comprehensiveData.bankData && comprehensiveData.bankData.length > 0) {
                // Use first bank record (modify if multiple banks needed)
                var bank = comprehensiveData.bankData[0];

                $('#is_bank_detail').val('1');
                $('.bank_detail_section').show();
                $('#ifsc_code').val(bank.IFSC || '');
                $('#bank_name').val(bank.BankName || '');
                $('#branch_name').val(bank.BranchName || '');
                $('#bank_address').val(bank.BankAddress || '');
                $('#account_number').val(bank.AccountNo || '');
                $('#account_holder_name').val(bank.HolderName || '');

                $('.selectpicker').selectpicker('refresh');

                console.log('Bank details populated successfully');
            } else {
                $('#is_bank_detail').val('0');
                $('.bank_detail_section').hide();
            }

            // 5. POPULATE BROKER DATA (from tblPartyBrokerMaster)
            if (comprehensiveData && comprehensiveData.broker && comprehensiveData.broker.length > 0) {
                var brokerData = comprehensiveData.broker[0];

                $('#broker').val(brokerData.BrokerID || '');
                $('#broker_person').val(brokerData.BrokerContactID || '');

                $('.selectpicker').selectpicker('refresh');

                console.log('Broker details populated successfully');
            }

            console.log('Form populated completely with all account data');
            alert_float('success', 'Account details loaded successfully');

            // Toggle buttons - show UPDATE, hide SAVE
            $('.saveBtn').hide();
            $('.updateBtn').show();
            $('.saveBtn2').hide();
            $('.updateBtn2').show();

        } catch (error) {
            console.error('Error populating form:', error);
            alert_float('error', 'Error loading account data: ' + error.message);
        }
    }


    $('#ifsc_code').blur(function() {
        var ifsc_code = $('#ifsc_code').val();
        if (ifsc_code != '') {
            $.ajax({
                url: "<?php echo admin_url(); ?>purchase/fetchBankDetailsFromIFSC",
                method: "POST",
                dataType: 'json',
                data: {
                    ifsc_code: ifsc_code
                },
                beforeSend: function() {
                    $('.searchh2').css('display', 'block');
                    $('.searchh2').css('color', 'blue');
                },
                complete: function() {
                    $('.searchh2').css('display', 'none');
                },
                success: function(data) {
                    if (data == "Not Found") {
                        alert_float('danger', "Enter valid IFSC Code");
                        $('#bank_name').val("");
                        $('#branch_name').val("");
                        $('#bank_address').val("");
                    } else {
                        $('#bank_name').val(data.BANK);
                        $('#branch_name').val(data.BRANCH);
                        $('#bank_address').val(data.ADDRESS);
                        fetchAccountHolderName();
                    }
                }
            });
        }
    });

    $('#account_number').blur(function() {
        fetchAccountHolderName();
    });

    // PAN Verification on blur event
    $("#Pan").on("blur", function() {
        let panNo = $(this).val().trim();
        // Map 4th PAN character to organisation type and set default
        if (panNo && panNo.length >= 4) {
            let ch = panNo.charAt(3).toUpperCase();
            // const panMap = {
            //     'P': 'Proprietorship',
            //     'F': 'Partnership',
            //     'C': 'Private Limited',
            //     'H': 'Hindu Undivided Family (HUF)',
            //     'T': 'Society / Trust / Club',
            //     'G': 'Government Department/Body',
            //     'L': 'Local Authority'
            // };
            // if (panMap[ch]) {
                $('#organisation_type').val(ch);
                $('.selectpicker').selectpicker('refresh');
            // }
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
            return $(this).parent().find('#Pan').length > 0;
        });
        if (panLabel) {
            panLabel.html(
                '<small class="req text-danger">* </small>PAN <span style="color: blue; font-size: 12px;"><i>(Verifying...)</i></span>'
            );
        }

        $.ajax({
            url: "<?php echo admin_url('clients/verify_pan'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                pan: panNo.toUpperCase()
            },
            timeout: 30000,
            success: function(res) {
                if (res.status === 'success') {
                    $("#AccoountName").val(res.data.full_name || '');
                    alert_float('success', 'PAN No verified successfully');

                    let panLabel = $('label').filter(function() {
                        return $(this).parent().find('#Pan').length > 0;
                    });
                    if (panLabel) {
                        panLabel.html('<small class="req text-danger">* </small>PAN');
                    }

                    let gstinLabel = document.querySelector('label[for="vat"]') || $(
                        'label').filter(function() {
                        return $(this).parent().find('#vat').length > 0;
                    });
                    if (gstinLabel) {
                        $(gstinLabel).html(
                            '<small class="req text-danger">* </small>GSTIN <span style="color: blue; font-size: 12px;"><i>(Fetching Data...)</i></span>'
                        );
                    }

                    // Fetch GSTIN by PAN
                    $.ajax({
                        url: "<?php echo admin_url('clients/get_gstin_by_pan'); ?>",
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

                                    $(".gst_denger").html(gstinDropdown)
                                        .css("color", "green").show();
                                    $("#vat").val(gstinList[0].gstin);
                                    $('#gstin_select').selectpicker(
                                        'refresh');

                                    alert_float('success',
                                        'GSTIN fetched and filled automatically'
                                    );

                                    // AUTO VERIFY GSTIN
                                    verifyGSTIN();
                                } else if (gstinList.length > 1) {
                                    $("#vat").hide();

                                    let gstinDropdown = `
											<select id="gstin_select" class="selectpicker form-control" onchange="handleGstinSelection()" data-width="100%" style='margin-top:5px;'>
											<option value="">Select GSTN NO</option>
										`;

                                    gstinList.forEach(function(item) {
                                        gstinDropdown +=
                                            `<option value="${item.gstin}" data-state="${item.state}">${item.gstin}</option>`;
                                    });

                                    gstinDropdown += `</select>`;

                                    $(".gst_denger").html(gstinDropdown)
                                        .css("color", "green").show();
                                    $('#gstin_select').selectpicker(
                                        'refresh');

                                    let gstinLabel = $('label').filter(
                                        function() {
                                            return $(this).parent()
                                                .find('#vat').length >
                                                0;
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

                                    $(".gst_denger").html(gstinDropdown)
                                        .css("color", "orange").show();
                                    $('#gstin_select').selectpicker(
                                        'refresh');

                                    let gstinLabel = $('label').filter(
                                        function() {
                                            return $(this).parent()
                                                .find('#vat').length >
                                                0;
                                        });
                                    if (gstinLabel) {
                                        gstinLabel.html(
                                            '<small class="req text-danger">* </small>GSTIN'
                                        );
                                    }

                                    alert_float('warning',
                                        'No GSTIN found for this PAN');
                                }
                            } else {
                                console.log('No GSTIN found:', gstRes
                                    .message);
                                let gstinLabel = $('label').filter(
                                    function() {
                                        return $(this).parent().find(
                                            '#vat').length > 0;
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
                                return $(this).parent().find('#vat')
                                    .length > 0;
                            });
                            if (gstinLabel) {
                                gstinLabel.html(
                                    '<small class="req text-danger">* </small>GSTIN'
                                );
                            }
                        }
                    });
                } else {
                    $(".pan_denger").html("✗ " + res.message).css("color", "red");
                    alert_float('danger', res.message || 'PAN verification failed');

                    let panLabel = $('label').filter(function() {
                        return $(this).parent().find('#Pan').length > 0;
                    });
                    if (panLabel) {
                        panLabel.html('<small class="req text-danger">* </small>PAN');
                    }

                    if (res.message === 'Invalid PAN') {
                        $("#Pan").val('');
                        $(".pan_denger").text("");
                    }
                }
            },
            error: function(xhr, status, error) {
                $(".pan_denger").html("✗ Verification failed").css("color", "red");
                alert_float('danger', 'PAN verification failed');

                let panLabel = $('label').filter(function() {
                    return $(this).parent().find('#Pan').length > 0;
                });
                if (panLabel) {
                    panLabel.html('<small class="req text-danger">* </small>PAN');
                }
            }
        });
    });
});

$('#shipping_state').on('change', function() {
    var StateID = $(this).val();
    var url = "<?php echo base_url(); ?>admin/clients/GetCity";
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: {
            StateID: StateID
        },
        dataType: 'json',
        success: function(data) {
            $("#shipping_city").find('option').remove();
            $("#shipping_city").selectpicker("refresh");
            for (var i = 0; i < data.length; i++) {
                $("#shipping_city").append(new Option(data[i].city_name, data[i].id));
            }
            $('.selectpicker').selectpicker('refresh');
        }
    });
});

function ResetForm() {
    var HiddenAccountID = $('#HiddenAccountID').val();
    $('#AccountID').val(HiddenAccountID);
    $('#favouring_name').val('');
    $('#AccoountName').val('');
    $('#phonenumber').val('');
    $('#altphonenumber').val('');
    $('#email').val('');
    $('#vat').val('');
    $('#tan_number').val('');
    $('#address').val('');
    $('#zip').val('');
    $('#FLNO1').val('');
    $('#Pan').val('');
    $('#MaxCrdAmt').val('');
    $('.selectpicker').selectpicker('refresh');
    $('#website').val('');
    $('#send_email').prop('checked', false);
    $('#send_sms').prop('checked', false);

    $('#ifsc_code').val('');
    $('#bank_name').val('');
    $('#branch_name').val('');
    $('#bank_address').val('');
    $('#account_number').val('');
    $('#account_holder_name').val('');
    $('#temp_account_number').val('');
    $('#attachment').val('');
    $('.attachment-display-col').remove();

    $('#current_outstanding').val('');
    $('#open_order_value').val('');
    $('#available_limit').val('');
    $('#avg_payment_day').val('');
    $('#avg_rebate').val('');

    $('#additional_info').val('');
    $('#blocked_reason').val('');
    $('#created_by').val('');
    $('#created_on').val('');
    $('#updated_by').val('');
    $('#updated_on').val('');

    $('#Blockyn').prop('checked', false);

    var today = new Date().toLocaleDateString('en-GB');
    $('#expiry_licence').val(today);

    $('select[name=gst_type]').val('1');
    $('.selectpicker').selectpicker('refresh');

    $('select[name=title]').val('Owner');
    $('.selectpicker').selectpicker('refresh');

    $('select[name=country]').val('1');
    $('select[name=territory]').val('');
    $('select[name=broker]').val('');
    $('select[name=broker_person]').val('');
    $('select[name=freight_term]').val('');
    $('select[name=priority]').val('');
    $('select[name=organisation_type]').val('');
    $('#credit_days').val('0');
    $('select[name=is_bank_detail]').val('0');
    $('select[name=Tds]').val('');
    $('#TdsPercent1').hide();
    $('#TdsSec').hide();
    $('select[name=Tdsselection]').val('');
    $('select[name=default_currency]').val('');
    $('select[name=temp_account_type]').val('');
    $('select[name=payment_terms]').val('');

    $("#city").children().remove();
    $('.selectpicker').selectpicker('refresh');

    $('select[name=groups_in]').val('');
    $('.selectpicker').selectpicker('refresh');

    $('select[name=state]').val('');
    $('.selectpicker').selectpicker('refresh');
    $("#hiddenState").val("");

    $('select[name=filter_state]').val('');
    $('select[name=filter_city]').val('');
    $('select[name=filter_location]').val('');
    $('.selectpicker').selectpicker('refresh');

    $("#addresstbody tr.addedtr").remove();
    $(".opening_bal").prop("readonly", false);

    $("#contacttbody tr.addedtr_contact").remove();
    $("#locationtbody tr.addedtr_location").remove();

    $('.saveBtn').show();
    $('.updateBtn').hide();
    $('.saveBtn2').show();
    $('.updateBtn2').hide();
}

$(".cancelBtn").click(function() {
    ResetForm();
    // Reload page after 1 second to ensure complete refresh
    setTimeout(function() {
        location.reload();
    }, 0000);
});

function populateContactData(contactdetails) {
    $("#contacttbody tr.addedtr_contact").remove();

    $("#cp_name").val('');
    $("#cp_designation").val('');
    $("#cp_mobile").val('');
    $("#cp_email").val('');
    $("#cp_send_email").prop('checked', false);
    $("#cp_send_sms").prop('checked', false);

    if (contactdetails) {
        contactdetails.forEach(function(cdata) {
            var newRow = $("<tr class='addedtr_contact'></tr>");
            newRow.append("<td><input type='hidden' name='cp_id[]' value='" + cdata.id +
                "'><input type='text' name='cp_name[]' class='form-control' value='" + cdata.Name +
                "'></td>");
            newRow.append(
                "<td><select class='selectpicker form-control cp_designation_input' name='cp_designation[]' data-width='100%'>" +
                getPositionOptions(cdata.PositionID || cdata.Designation) + "</select></td>");
            newRow.append("<td><input type='text' name='cp_mobile[]' class='form-control' value='" + cdata
                .Mobile + "' maxlength='10' onkeypress='return isNumber(event)'></td>");
            newRow.append("<td><input type='text' name='cp_email[]' class='form-control' value='" + cdata
                .Email + "'></td>");
            newRow.append(
                "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_sms[]' " +
                (cdata.SendSMS == 1 ? 'checked' : '') + "><label></label></div></td>");
            newRow.append(
                "<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_email[]' " +
                (cdata.SendEmail == 1 ? 'checked' : '') + "><label></label></div></td>");
            newRow.append(
                "<td><a href='#' class='btn btn-danger removebtn_contact'><i class='fa fa-times'></i></a></td>"
            );
            $("#contacttbody").append(newRow);
            newRow.find('.selectpicker').selectpicker();
            newRow.find('.selectpicker').selectpicker('refresh');
        });
    }
}

// NEW FUNCTION - Populate Location Data from GSTIN API
function populateLocationDataFromGSTIN(gstinData) {
    // Clear existing location rows except the first one
    $("#locationtbody tr.addedtr_location").remove();

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
                    "<td><select class='form-control loc_state' name='loc_state[]' required>";
                stateDropdown += "<option value=''>Select State</option>";
                <?php foreach ($state as $st) { ?>
                stateDropdown +=
                    "<option value='<?php echo $st['short_name']; ?>'><?php echo $st['state_name']; ?></option>";
                <?php } ?>
                stateDropdown += "</select></td>";
                newRow.append(stateDropdown);

                // Add city dropdown (will be populated after state selection)
                newRow.append(
                    "<td><select class='form-control loc_city' name='loc_city[]' required><option value=''>Select City</option></select></td>"
                );

                // Add mobile
                newRow.append("<td><input type='text' class='form-control' name='loc_mobile[]' value='" +
                    mobile + "' onkeypress='return isNumber(event)' maxlength='10' required></td>");

                // Add remove button
                newRow.append(
                    "<td><button type='button' class='btn btn-danger removeLocationBtn'><i class='fa fa-times'></i></button></td>"
                );

                // Append row to table
                $("#locationtbody").append(newRow);

                // Now set the state value and trigger city population
                let stateSelect = newRow.find('.loc_state');
                let citySelect = newRow.find('.loc_city');

                // Find matching state
                stateSelect.find('option').each(function() {
                    if ($(this).text().toLowerCase().indexOf(state.toLowerCase()) !== -1) {
                        stateSelect.val($(this).val());

                        // Fetch cities for this state
                        let stateId = $(this).val();
                        $.ajax({
                            url: "<?php echo base_url(); ?>admin/clients/GetCity",
                            type: 'POST',
                            data: {
                                StateID: stateId
                            },
                            dataType: 'json',
                            success: function(cityData) {
                                citySelect.empty();
                                citySelect.append(new Option('Select City', ''));

                                cityData.forEach(function(cityItem) {
                                    citySelect.append(new Option(cityItem
                                        .city_name, cityItem.id));
                                });

                                // Now find and set the matching city
                                citySelect.find('option').each(function() {
                                    if ($(this).text().toLowerCase().indexOf(
                                            city.toLowerCase()) !== -1) {
                                        citySelect.val($(this).val());
                                        return false;
                                    }
                                });
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

$('.saveBtn').on('click', function() {
    AccountID = $('#AccountID').val();
    userid = $('#userid').val() || $('#staffid').val() || 0;
    favouring_name = $('#favouring_name').val();
    AccoountName = $('#AccoountName').val();
    phonenumber = $('#phonenumber').val();
    altphonenumber = $('#altphonenumber').val();
    email = $('#email').val();
    website = $('#website').val();
    vat = $('#vat').val();
    groups_in = $('#groups_in').val();
    state = $('#state').val();
    city = $('#city').val();
    address = $('#address').val();
    zip = $('#zip').val();
    tan_number = $('#tan_number').val();
    FLNO1 = $('#FLNO1').val();
    gsttype = $('#gst_type').val();
    Pan = $('#Pan').val();
    MaxCrdAmt = $('#MaxCrdAmt').val();
    Blockyn = $('#Blockyn').val();
    expiry_licence = $('#expiry_licence').val();
    country = $('#country').val();
    territory = $('#territory').val();
    broker = $('#broker').val();
    broker_person = $('#broker_person').val();
    freight_term = $('#freight_terms').val();
    priority = $('#priority').val();
    attachment = document.getElementById('attachment').files[0] || null;

    organisation_type = $('#organisation_type').val();
    credit_days = $('#credit_days').val();
    is_bank_detail = $('#is_bank_detail').val();

    ifsc_code = $('#ifsc_code').val();
    bank_name = $('#bank_name').val();
    branch_name = $('#branch_name').val();
    bank_address = $('#bank_address').val();
    account_number = $('#account_number').val();
    account_holder_name = $('#account_holder_name').val();
    temp_account_type = $('#temp_account_type').val();
    temp_account_number = $('#temp_account_number').val();
    Tds = $('#Tds').val();
    TdsPercent = '';
    Tdsselection = '';
    if (Tds === '1') {
        TdsPercent = $('#TdsPercent').val();
        Tdsselection = $('#Tdsselection').val();
    }
    default_currency = $('#default_currency').val();
    payment_terms = $('#payment_terms').val();
    payment_cycle = $('#payment_cycle').val();
    payment_cycle_type = $('#payment_cycle_type').val();
    additional_info = $('#additional_info').val();
    blocked_reason = $('#blocked_reason').val();

    let Contact = [];
    // Collect contact rows regardless of whether inputs use array name attributes
    $("#contacttbody tr").each(function() {
        let Name = $(this).find("input[name='cp_name[]']").val() || $(this).find('.cp_name_input')
    .val();
        let Designation = $(this).find("select[name='cp_designation[]']").val() || $(this).find(
                "select[name='cp_designation']").val() || $(this).find("input[name='cp_designation[]']")
            .val() || $(this).find('.cp_designation_input').val();
        let Mobile = $(this).find("input[name='cp_mobile[]']").val() || $(this).find('.cp_mobile_input')
            .val();
        let Email = $(this).find("input[name='cp_email[]']").val() || $(this).find('.cp_email_input')
            .val();
        let SendEmail = $(this).find("input[name='cp_send_email[]']").is(':checked') ? 1 : $(this).find(
            '.cp_email_checkbox').is(':checked') ? 1 : 0;
        let SendSMS = $(this).find("input[name='cp_send_sms[]']").is(':checked') ? 1 : $(this).find(
            '.cp_sms_checkbox').is(':checked') ? 1 : 0;

        // Skip empty rows
        if (!Name && !Designation && !Mobile && !Email) return;

        var DesignationToSave = Designation;
        if (DesignationToSave) {
            var posMatch = positionData.find(function(p) {
                return p.position_id == DesignationToSave || p.position_name ==
                    DesignationToSave;
            });
            if (posMatch) DesignationToSave = posMatch.position_id;
        }

        Contact.push({
            Name: Name,
            Designation: DesignationToSave,
            Mobile: Mobile,
            Email: Email,
            SendEmail: SendEmail,
            SendSMS: SendSMS
        });
    });
    let ContactData = JSON.stringify(Contact);

    // Validate contact rows: designation required and at least one send option
    // for (let i = 0; i < Contact.length; i++) {
    //     let c = Contact[i];
    //     if (!c.Designation || c.Designation.toString().trim() === '') {
    //         alert_float('warning', 'Contact designation is required for ' + (c.Name || ('contact #' + (i +
    //             1))));
    //         return;
    //     }
    //     if (c.SendEmail != 1 && c.SendSMS != 1) {
    //         alert_float('warning', 'At least one of Send SMS or Send Email must be selected for ' + (c
    //             .Name || ('contact #' + (i + 1))));
    //         return;
    //     }
    // }

    let ShippingData = [];
    $("#locationtbody tr").each(function() {
        let state = $(this).find("select[name='loc_state[]']").val();
        let city = $(this).find("select[name='loc_city[]']").val();
        let address = $(this).find("textarea[name='loc_address[]']").val();
        let pincode = $(this).find("input[name='loc_pincode[]']").val();
        let mobile = $(this).find("input[name='loc_mobile[]']").val();

        if (state || city || address || pincode || mobile) {
            ShippingData.push({
                state: state,
                city: city,
                address: address,
                pincode: pincode,
                mobile: mobile,
                location_type: '',
                route: ''
            });
        }
    });
    let ShippingData_JSON = JSON.stringify(ShippingData);

    if (AccountID == '') {
        alert_float('warning', 'please enter Customer id');
        $('#AccountID').focus();
    } else if ($.trim(AccoountName) == '') {
        alert_float('warning', 'please enter Customer Name');
        $('#AccountName').focus();
    } else if (!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#Pan').val() !== "") {
        alert_float('warning', 'Enter valid PAN number');
        $('#Pan').focus();
    } else if (gsttype == '') {
        alert_float('warning', 'please select Gst Type');
        $('.saveBtn').removeAttr('disabled');
        $('#gst_type').focus();
    } else if (parseInt(gsttype) == '1' && vat == '') {
        alert_float('warning', 'Enter valid GST number');
        $('.saveBtn').removeAttr('disabled');
        $('#gst_type').focus();
    } else if (!$('#vat').val().match('[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z][0-9][0-9A-Za-z]{2}') && $('#vat')
        .val() !== '') {
        alert_float('warning', "Enter valid GST no..");
        $('#vat').focus();
    } else if (phonenumber == '') {
        alert_float('warning', 'please  enter mobile number');
        $('#phonenumber').focus();
    } else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
        alert_float('warning', 'Enter valid Mobile number');
        $('#phonenumber').focus();
    } else if (!$('#email').val().match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/) && $('#email').val() !== "") {
        alert_float('warning', 'Enter valid Email-id');
        $('.saveBtn').removeAttr('disabled');
        $('#email').focus();
    } else if (state == '') {
        alert_float('warning', 'please select State');
        $('#state').focus();
    } else if (city == '') {
        alert_float('warning', 'please select City');
        $('#city').focus();
    } else if (Contact.length === 0) {
        alert_float('warning', 'Please add at least one Contact Detail');
        $('#contacttbody').focus();
    } else if (ShippingData.length === 0) {
        alert_float('warning', 'Please add at least one Location Information');
        $('#locationtbody').focus();
    } else if (country == '') {
        alert_float('warning', 'Please select Billing Country');
        $('#country').focus();
    } else if (is_bank_detail == '' || is_bank_detail == null) {
        alert_float('warning', 'Please select Bank Details option');
        $('#is_bank_detail').focus();
    } else if (!attachment) {
        alert_float('warning', 'Please upload an attachment file');
        $('#attachment').focus();
    } else {
        // Use FormData to include file attachment
        var formData = new FormData();
        formData.append('AccountID', AccountID);
        formData.append('AccoountName', AccoountName);
        formData.append('favouring_name', favouring_name);
        formData.append('phonenumber', phonenumber);
        formData.append('altphonenumber', altphonenumber);
        formData.append('email', email);
        formData.append('website', website);
        formData.append('vat', vat);
        formData.append('groups_in', groups_in);
        formData.append('state', state);
        formData.append('city', city);
        formData.append('address', address);
        formData.append('zip', zip);
        formData.append('tan_number', tan_number);
        formData.append('FLNO1', FLNO1);
        formData.append('Pan', Pan);
        formData.append('MaxCrdAmt', MaxCrdAmt);
        formData.append('Blockyn', Blockyn);
        formData.append('userid', userid);
        formData.append('gsttype', gsttype);
        formData.append('expiry_licence', expiry_licence);
        formData.append('ContactData', ContactData);
        formData.append('ShippingData', ShippingData_JSON);
        formData.append('country', country);
        formData.append('territory', territory);
        formData.append('broker', broker);
        formData.append('broker_person', broker_person);
        formData.append('freight_term', freight_term);
        formData.append('priority', priority);
        formData.append('organisation_type', organisation_type);
        formData.append('credit_days', credit_days);
        formData.append('is_bank_detail', is_bank_detail);
        formData.append('ifsc_code', ifsc_code);
        formData.append('bank_name', bank_name);
        formData.append('branch_name', branch_name);
        formData.append('account_number', account_number);
        formData.append('bank_address', bank_address);
        formData.append('account_holder_name', account_holder_name);
        formData.append('temp_account_type', temp_account_type);
        formData.append('temp_account_number', temp_account_number);
        formData.append('Tds', Tds);
        formData.append('TdsPercent', TdsPercent);
        formData.append('Tdsselection', Tdsselection);
        formData.append('default_currency', default_currency);
        formData.append('payment_terms', payment_terms);
        formData.append('payment_cycle', payment_cycle);
        formData.append('payment_cycle_type', payment_cycle_type);
        formData.append('additional_info', additional_info);
        formData.append('blocked_reason', blocked_reason);
        // attachment file
        if (attachment) {
            formData.append('attachment', attachment);
        }
        // CSRF token (if present)
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfVal = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';
        formData.append(csrfName, csrfVal);

        $.ajax({
            url: "<?php echo admin_url(); ?>clients/SaveAccountID",
            dataType: "JSON",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.searchh3').css('display', 'block');
                $('.searchh3').css('color', 'blue');
            },
            complete: function() {
                $('.searchh3').css('display', 'none');
            },
            success: function(data) {
                if (data && data.success) {
                    $('#HiddenAccountID').val(data.account_id || '');
                    if (data.attachment) {
                        // alert_float('success', 'Record created successfully. File: ' + data.attachment);
                        alert_float('success', 'Record created successfully...');

                    } else {
                        alert_float('success', 'Record created successfully...');
                    }
                    ResetForm();
                    setTimeout(function() {
                        location.reload();
                    }, 0000);
                } else {
                    var msg = (data && data.message) ? data.message : 'Something went wrong...';
                    alert_float('warning', msg);
                    ResetForm();
                }
            },
            error: function(xhr, status, err) {
                console.error('SaveAccountID error', status, err, xhr.responseText);
                var errorMsg = 'Error saving record';
                try {
                    if (xhr.responseText) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    }
                } catch (e) {
                    errorMsg = xhr.responseText || 'Error saving record';
                }
                alert_float('danger', errorMsg);
                ResetForm();
            }
        });
    }
});

$('.updateBtn').on('click', function() {
    AccountID = $('#AccountID').val();
    userid = $('#userid').val() || $('#staffid').val() || 0;
    favouring_name = $('#favouring_name').val();
    AccoountName = $('#AccoountName').val();
    phonenumber = $('#phonenumber').val();
    altphonenumber = $('#altphonenumber').val();
    email = $('#email').val();
    website = $('#website').val();
    gsttype = $('#gst_type').val();
    vat = $('#vat').val();
    groups_in = $('#groups_in').val();
    state = $('#state').val();
    city = $('#city').val();
    address = $('#address').val();
    zip = $('#zip').val();
    tan_number = $('#tan_number').val();
    FLNO1 = $('#FLNO1').val();
    Pan = $('#Pan').val();
    MaxCrdAmt = $('#MaxCrdAmt').val();
    Blockyn = $('#Blockyn').val();
    expiry_licence = $('#expiry_licence').val();
    country = $('#country').val();
    territory = $('#territory').val();
    broker = $('#broker').val();
    broker_person = $('#broker_person').val();
    freight_term = $('#freight_terms').val();
    priority = $('#priority').val();
    attachment = document.getElementById('attachment').files[0] || null;

    organisation_type = $('#organisation_type').val();
    credit_days = $('#credit_days').val();
    is_bank_detail = $('#is_bank_detail').val();

    ifsc_code = $('#ifsc_code').val();
    bank_name = $('#bank_name').val();
    branch_name = $('#branch_name').val();
    bank_address = $('#bank_address').val();
    account_number = $('#account_number').val();
    account_holder_name = $('#account_holder_name').val();
    temp_account_type = $('#temp_account_type').val();
    temp_account_number = $('#temp_account_number').val();
    Tds = $('#Tds').val();
    TdsPercent = '';
    Tdsselection = '';
    if (Tds === '1') {
        TdsPercent = $('#TdsPercent').val();
        Tdsselection = $('#Tdsselection').val();
    }
    default_currency = $('#default_currency').val();
    payment_terms = $('#payment_terms').val();
    payment_cycle = $('#payment_cycle').val();
    payment_cycle_type = $('#payment_cycle_type').val();
    additional_info = $('#additional_info').val();
    blocked_reason = $('#blocked_reason').val();

    let Contact = [];
    // Collect contact rows irrespective of name attribute presence; preserve id when present
    $("#contacttbody tr").each(function() {
        let id = $(this).find("input[name='cp_id[]']").val() || '';
        let Name = $(this).find("input[name='cp_name[]']").val() || $(this).find('.cp_name_input')
    .val();
        let Designation = $(this).find("select[name='cp_designation[]']").val() || $(this).find(
                "select[name='cp_designation']").val() || $(this).find("input[name='cp_designation[]']")
            .val() || $(this).find('.cp_designation_input').val();
        let Mobile = $(this).find("input[name='cp_mobile[]']").val() || $(this).find('.cp_mobile_input')
            .val();
        let Email = $(this).find("input[name='cp_email[]']").val() || $(this).find('.cp_email_input')
            .val();
        let SendEmail = $(this).find("input[name='cp_send_email[]']").is(':checked') ? 1 : $(this).find(
            '.cp_email_checkbox').is(':checked') ? 1 : 0;
        let SendSMS = $(this).find("input[name='cp_send_sms[]']").is(':checked') ? 1 : $(this).find(
            '.cp_sms_checkbox').is(':checked') ? 1 : 0;

        if (!Name && !Designation && !Mobile && !Email) return;

        var DesignationToSave = Designation;
        if (DesignationToSave) {
            var posMatch = positionData.find(function(p) {
                return p.position_id == DesignationToSave || p.position_name ==
                    DesignationToSave;
            });
            if (posMatch) DesignationToSave = posMatch.position_id;
        }

        Contact.push({
            id: id,
            Name: Name,
            Designation: DesignationToSave,
            Mobile: Mobile,
            Email: Email,
            SendEmail: SendEmail,
            SendSMS: SendSMS
        });
    });
    let ContactData = JSON.stringify(Contact);

    // Validate contact rows: designation required and at least one send option
    // for (let i = 0; i < Contact.length; i++) {
    //     let c = Contact[i];
    //     if (!c.Designation || c.Designation.toString().trim() === '') {
    //         alert_float('warning', 'Contact designation is required for ' + (c.Name || ('contact #' + (i +
    //             1))));
    //         return;
    //     }
    //     if (c.SendEmail != 1 && c.SendSMS != 1) {
    //         alert_float('warning', 'At least one of Send SMS or Send Email must be selected for ' + (c
    //             .Name || ('contact #' + (i + 1))));
    //         return;
    //     }
    // }

    let ShippingData = [];
    $("#locationtbody tr").each(function() {
        let state = $(this).find("select[name='loc_state[]']").val();
        let city = $(this).find("select[name='loc_city[]']").val();
        let address = $(this).find("textarea[name='loc_address[]']").val();
        let pincode = $(this).find("input[name='loc_pincode[]']").val();
        let mobile = $(this).find("input[name='loc_mobile[]']").val();

        if (state || city || address || pincode || mobile) {
            ShippingData.push({
                state: state,
                city: city,
                address: address,
                pincode: pincode,
                mobile: mobile,
                location_type: '',
                route: ''
            });
        }
    });
    let ShippingData_JSON = JSON.stringify(ShippingData);

    if (AccountID == '') {
        alert_float('warning', 'please enter AccountID');
        $('#AccountID').focus();
    } else if ($.trim(AccoountName) == '') {
        alert_float('warning', 'please enter Account Name');
        $('#AccountName').focus();
    } else if (!$('#Pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}') && $('#Pan').val() !== "") {
        alert_float('warning', 'Enter valid PAN number');
        $('#Pan').focus();
    } else if (gsttype == '') {
        alert_float('warning', 'please select Gst Type');
        $('.saveBtn').removeAttr('disabled');
        $('#gst_type').focus();
    } else if (parseInt(gsttype) == '1' && vat == '') {
        alert_float('warning', 'Enter valid GST number');
        $('.saveBtn').removeAttr('disabled');
        $('#gst_type').focus();
    } else if (!$('#vat').val().match('[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z][0-9][0-9A-Za-z]{2}') && $('#vat')
        .val() !== '') {
        alert_float('warning', "Enter valid GST no..");
        $('#vat').focus();
    } else if (phonenumber == '') {
        alert_float('warning', 'please  enter mobile number');
        $('#phonenumber').focus();
    } else if (!$('#phonenumber').val().match('[0-9]{10}') && $('#phonenumber').val() !== "") {
        alert_float('warning', 'Enter valid Mobile number');
        $('#phonenumber').focus();
    } else if (!$('#email').val().match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/) && $('#email').val() !== "") {
        alert_float('warning', 'Enter valid Email-id');
        $('.saveBtn').removeAttr('disabled');
        $('#email').focus();
    } else if (state == '') {
        alert_float('warning', 'please select State');
        $('#state').focus();
    } else if (city == '') {
        alert_float('warning', 'please select City');
        $('#city').focus();
    } else if (Contact.length === 0) {
        alert_float('warning', 'Please add at least one Contact Detail');
        $('#contacttbody').focus();
    } else if (ShippingData.length === 0) {
        alert_float('warning', 'Please add at least one Location Information');
        $('#locationtbody').focus();
    } else if (country == '') {
        alert_float('warning', 'Please select Billing Country');
        $('#country').focus();
    } else if (is_bank_detail == '' || is_bank_detail == null) {
        alert_float('warning', 'Please select Bank Details option');
        $('#is_bank_detail').focus();
        // } else if (!attachment) {
        //     alert_float('warning', 'Please upload an attachment file');
        //     $('#attachment').focus();
    } else {
        // Use FormData to include file attachment for update
        var formDataUpd = new FormData();
        formDataUpd.append('userid', userid);
        formDataUpd.append('AccountID', AccountID);
        formDataUpd.append('AccoountName', AccoountName);
        formDataUpd.append('favouring_name', favouring_name);
        formDataUpd.append('phonenumber', phonenumber);
        formDataUpd.append('altphonenumber', altphonenumber);
        formDataUpd.append('email', email);
        formDataUpd.append('website', website);
        formDataUpd.append('vat', vat);
        formDataUpd.append('groups_in', groups_in);
        formDataUpd.append('state', state);
        formDataUpd.append('city', city);
        formDataUpd.append('address', address);
        formDataUpd.append('zip', zip);
        formDataUpd.append('tan_number', tan_number);
        formDataUpd.append('FLNO1', FLNO1);
        formDataUpd.append('Pan', Pan);
        formDataUpd.append('MaxCrdAmt', MaxCrdAmt);
        formDataUpd.append('Blockyn', Blockyn);
        formDataUpd.append('expiry_licence', expiry_licence);
        formDataUpd.append('ContactData', ContactData);
        formDataUpd.append('gsttype', gsttype);
        formDataUpd.append('ShippingData', ShippingData_JSON);
        formDataUpd.append('country', country);
        formDataUpd.append('territory', territory);
        formDataUpd.append('broker', broker);
        formDataUpd.append('broker_person', broker_person);
        formDataUpd.append('freight_term', freight_term);
        formDataUpd.append('priority', priority);
        formDataUpd.append('organisation_type', organisation_type);
        formDataUpd.append('credit_days', credit_days);
        formDataUpd.append('is_bank_detail', is_bank_detail);
        formDataUpd.append('ifsc_code', ifsc_code);
        formDataUpd.append('bank_name', bank_name);
        formDataUpd.append('branch_name', branch_name);
        formDataUpd.append('bank_address', bank_address);
        formDataUpd.append('account_number', account_number);
        formDataUpd.append('account_holder_name', account_holder_name);
        formDataUpd.append('temp_account_type', temp_account_type);
        formDataUpd.append('temp_account_number', temp_account_number);
        formDataUpd.append('Tds', Tds);
        formDataUpd.append('TdsPercent', TdsPercent);
        formDataUpd.append('Tdsselection', Tdsselection);
        formDataUpd.append('default_currency', default_currency);
        formDataUpd.append('payment_terms', payment_terms);
        formDataUpd.append('payment_cycle', payment_cycle);
        formDataUpd.append('payment_cycle_type', payment_cycle_type);
        formDataUpd.append('additional_info', additional_info);
        formDataUpd.append('blocked_reason', blocked_reason);
        if (attachment) {
            formDataUpd.append('attachment', attachment);
        }
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfVal = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';
        formDataUpd.append(csrfName, csrfVal);

        $.ajax({
            url: "<?php echo admin_url(); ?>clients/UpdateAccountID",
            dataType: "JSON",
            method: "POST",
            data: formDataUpd,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('.searchh4').css('display', 'block');
                $('.searchh4').css('color', 'blue');
            },
            complete: function() {
                $('.searchh4').css('display', 'none');
            },
            success: function(data) {
                if (data && data.success) {
                    if (data.attachment) {
                        // alert_float('success', 'Record updated successfully. File: ' + data.attachment);
                        alert_float('success', 'Record updated successfully...');
                    } else {
                        alert_float('success', 'Record updated successfully...');
                    }
                    ResetForm();
                    setTimeout(function() {
                        location.reload();
                    }, 0000);
                } else {
                    var msg = (data && data.message) ? data.message : 'Something went wrong...';
                    alert_float('warning', msg);
                    ResetForm();
                }
            },
            error: function(xhr, status, err) {
                console.error('UpdateAccountID error', status, err, xhr.responseText);
                var errorMsg = 'Error updating record';
                try {
                    if (xhr.responseText) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    }
                } catch (e) {
                    errorMsg = xhr.responseText || 'Error updating record';
                }
                alert_float('danger', errorMsg);
                ResetForm();
            }
        });
    }
});

$('#state').on('change', function() {
    var StateID = $(this).val();
    var url = "<?php echo base_url(); ?>admin/clients/GetCity";
    jQuery.ajax({
        type: 'POST',
        url: url,
        data: {
            StateID: StateID
        },
        dataType: 'json',
        success: function(data) {
            $("#city").find('option').remove();
            $("#city").selectpicker("refresh");
            for (var i = 0; i < data.length; i++) {
                $("#city").append(new Option(data[i].city_name, data[i].id));
            }
            $('.selectpicker').selectpicker('refresh');
        }
    });
});

function addContactRow() {
    var Name = $("#cp_name").val();
    var Designation = $("#cp_designation").val();
    var Mobile = $("#cp_mobile").val();
    var Email = $("#cp_email").val();
    var SendEmail = $("#cp_send_email").is(':checked') ? 1 : 0;
    var SendSMS = $("#cp_send_sms").is(':checked') ? 1 : 0;

    if (Mobile == '') {
        alert_float('warning', 'Mobile Number is required');
        return;
    }

    if (Mobile.length != 10) {
        alert_float('warning', 'Mobile Number must be exactly 10 digits');
        return;
    }

    if (Email == '') {
        alert_float('warning', 'Email is required');
        return;
    }

    if (!Email.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
        alert_float('warning', 'Enter valid Email address');
        return;
    }

    if (SendEmail == 0 && SendSMS == 0) {
        alert_float('warning', 'At least one of "Send SMS" or "Send Email" must be selected');
        return;
    }

    var newRow = $("<tr class='addedtr_contact'></tr>");
    newRow.append("<td><input type='text' name='cp_name[]' class='form-control' value='" + Name + "'></td>");
    newRow.append(
        "<td><select class='selectpicker form-control cp_designation_input' name='cp_designation[]' data-width='100%'>" +
        getPositionOptions(Designation) + "</select></td>");
    newRow.append("<td><input type='text' name='cp_mobile[]' class='form-control' value='" + Mobile +
        "' maxlength='10' minlength='10' pattern='[0-9]{10}' onkeypress='return isNumber(event)' required></td>"
    );
    newRow.append("<td><input type='email' name='cp_email[]' class='form-control' value='" + Email +
        "' pattern='[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}' required></td>");
    newRow.append("<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_sms[]' " + (
        SendSMS == 1 ? 'checked' : '') + "><label></label></div></td>");
    newRow.append("<td class='text-center'><div class='checkbox'><input type='checkbox' name='cp_send_email[]' " + (
        SendEmail == 1 ? 'checked' : '') + "><label></label></div></td>");
    newRow.append("<td><a href='#' class='btn btn-danger removebtn_contact'><i class='fa fa-times'></i></a></td>");
    $("#contacttbody").append(newRow);

    // Refresh selectpicker for newly added row
    newRow.find('.selectpicker').selectpicker();
    newRow.find('.selectpicker').selectpicker('refresh');

    $("#cp_name").val('');
    $("#cp_designation").val('');
    $("#cp_mobile").val('');
    $("#cp_email").val('');
    $("#cp_send_email").prop('checked', false);
    $("#cp_send_sms").prop('checked', false);
}

$(document).on('click', '.removebtn_contact', function() {
    $(this).closest('tr').remove();
});

function addLocationRow() {
    var newRow = `
            <tr class="addedtr_location">
                <td><input type="text" class="form-control" name="loc_pincode[]" onkeypress="return isNumber(event)" maxlength="6" required></td>
                <td><textarea name="loc_address[]" class="form-control" rows="1"></textarea></td>
                <td>
                    <select class="selectpicker form-control loc_state" name="loc_state[]" required>
                        <option value="">Select State</option>
                        <?php foreach ($state as $st) { ?>
                        <option value="<?php echo $st['short_name']; ?>">
                            <?php echo $st['state_name']; ?></option>
                        <?php } ?>
                    </select>
                </td>
                <td>
                    <select class="selectpicker form-control loc_city" name="loc_city[]" required>
                        <option value="">Select City</option>
                    </select>
                </td>
                <td><input type="text" class="form-control" name="loc_mobile[]" onkeypress="return isNumber(event)" maxlength="10" required></td>
                <td><button type="button" class="btn btn-danger removeLocationBtn"><i class="fa fa-times"></i></button></td>
            </tr>
        `;
    $("#locationtbody").append(newRow);
    // Initialize/refresh selectpicker for newly added selects
    $("#locationtbody .selectpicker").selectpicker();
}

$(document).on('click', '.removeLocationBtn', function(e) {
    e.preventDefault();
    $(this).closest('tr').remove();
});

$(document).on('change', '.loc_state', function() {
    var StateID = $(this).val();
    var cityDropdown = $(this).closest('tr').find('select.loc_city');
    var url = "<?php echo base_url(); ?>admin/clients/GetCity";
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
            }
        });
    } else {
        cityDropdown.find('option').remove();
        cityDropdown.append(new Option('None selected', ""));
    }
});

$(document).on("click", ".sortablePop", function() {
    var table = $("#table_Account_List tbody");
    var rows = table.find("tr").toArray();
    var index = $(this).index();
    var ascending = !$(this).hasClass("asc");

    $(".sortablePop").removeClass("asc desc");
    $(".sortablePop span").remove();

    $(this).addClass(ascending ? "asc" : "desc");
    $(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');

    rows.sort(function(a, b) {
        var valA = $(a).find("td").eq(index).text().trim();
        var valB = $(b).find("td").eq(index).text().trim();

        if ($.isNumeric(valA) && $.isNumeric(valB)) {
            return ascending ? valA - valB : valB - valA;
        } else {
            return ascending ? valA.localeCompare(valB) : valB.localeCompare(valA);
        }
    });
    table.append(rows);
});

// Verify GSTIN and call KYC API
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
        url: "<?php echo admin_url('clients/verify_gstin_kyc'); ?>",
        type: "POST",
        dataType: "json",
        data: {
            gstin: gstinNo.toUpperCase(),
            userid: $('#userid').val() || $('#userid').attr('value') || 0
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

            if (response.status === 'duplicate') {
                let msgText = response.message || 'GSTIN already exists in the system';
                // Only show the floating alert for duplicates; avoid inserting inline messages
                alert_float('warning', msgText);

                // Set GST Category to Un-Registered when GSTIN is duplicate
                $('select[name=gst_type]').val('Un-Registered');
                $('.selectpicker').selectpicker('refresh');

                // Do not append inline .gstin-verify-msg element (prevents overlap over inputs)
                return;
            } else if (response.status === 'success') {
                // Clear old data before populating new data
                $('#AccoountName').val('');
                $('#favouring_name').val('');
                $('#email').val('');
                $('#phonenumber').val('');
                $('#address').val('');
                $('#zip').val('');
                $('#state').val('').selectpicker('refresh');
                $('#city').val('').selectpicker('refresh');

                // Clear contact and location tables
                $("#contacttbody tr.addedtr_contact").remove();
                $("#locationtbody tr.addedtr_location").remove();

                let msgText = response.message || 'GSTIN verified successfully';
                alert_float('success', msgText);

                // Set GST Category to Registered when GSTIN verification succeeds
                $('select[name=gst_type]').val('Registered');
                $('.selectpicker').selectpicker('refresh');

                if (response.data && response.data.fallback === false) {
                    var detailsCheck = response.data.details || {};
                    var hasRelevant = false;
                    if (detailsCheck.business_name && detailsCheck.business_name.toString().trim() !==
                        '') hasRelevant = true;
                    if (detailsCheck.contact_details && detailsCheck.contact_details.principal) {
                        var p = detailsCheck.contact_details.principal;
                        if ((p.address && p.address.toString().trim() !== '') || (p.email && p.email
                                .toString().trim() !== '') || (p.mobile && p.mobile.toString()
                                .trim() !== '')) hasRelevant = true;
                    }
                    if (!hasRelevant) {
                        alert_float('warning', 'Data not found');
                    }
                }

                if (response.data && response.data.details) {
                    let details = response.data.details;
                    let contactDetails = details.contact_details;
                    console.log('Contact Details:', contactDetails);

                    // Populate company name from GSTIN response
                    if (details.business_name) {
                        $('#AccoountName').val(details.business_name);
                    }

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
                                        url: "<?php echo base_url(); ?>admin/clients/GetCity",
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
                                                    console.log('Setting city:',
                                                        city.id);
                                                    if ($('#city option[value="' +
                                                            city.id + '"]')
                                                        .length === 0) {
                                                        $('#city').append(
                                                            new Option(city
                                                                .city_name, city
                                                                .id));
                                                    }
                                                    $('#city option[data-temp="1"]')
                                                        .remove();
                                                    $('#city').selectpicker(
                                                        'refresh');
                                                    $('#city').selectpicker('val',
                                                        city.id);
                                                    $('#city').selectpicker(
                                                        'refresh');
                                                    $('#city').trigger('change');
                                                    $('#city').selectpicker(
                                                        'refresh');
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
                            $('#address').val(fullAddress);

                            let tokens = fullAddress.split(',').map(function(p) {
                                return p.trim();
                            }).filter(function(p) {
                                return p.length > 0;
                            });
                            for (let i = tokens.length - 1; i >= 0; i--) {
                                if (/^\d{6}$/.test(tokens[i])) {
                                    $('#zip').val(tokens[i]);
                                    tokens.splice(i, 1);
                                    break;
                                }
                            }

                            let stateDetected = false;
                            for (let i = tokens.length - 1; i >= 0 && !stateDetected; i--) {
                                let tok = tokens[i].toLowerCase();
                                $('#state option').each(function() {
                                    if ($(this).text().toLowerCase().indexOf(tok) !== -1) {
                                        $('#state').val($(this).val());
                                        $('#state').selectpicker('refresh');
                                        stateDetected = true;
                                        return false;
                                    }
                                });
                                if (stateDetected) {
                                    tokens.splice(i, 1);
                                }
                            }

                            if (stateDetected) {
                                let stateId = $('#state').val();
                                if (stateId) {
                                    $.ajax({
                                        url: "<?php echo base_url(); ?>admin/clients/GetCity",
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
                                                        if ($('#city option[value="' +
                                                                cityData[j].id + '"]')
                                                            .length === 0) {
                                                            $('#city').append(new Option(
                                                                cityData[j]
                                                                .city_name,
                                                                cityData[j].id));
                                                            $('#city').selectpicker(
                                                                'refresh');
                                                        }
                                                        $('#city').selectpicker('val',
                                                            cityData[j].id);
                                                        $('#city').selectpicker('refresh');
                                                        $('#city').trigger('change');
                                                        return;
                                                    }
                                                    if (cname.indexOf(tok) !== -1 || tok
                                                        .indexOf(cname) !== -1) {
                                                        if ($('#city option[value="' +
                                                                cityData[j].id + '"]')
                                                            .length === 0) {
                                                            $('#city').append(new Option(
                                                                cityData[j]
                                                                .city_name,
                                                                cityData[j].id));
                                                            $('#city').selectpicker(
                                                                'refresh');
                                                        }
                                                        $('#city').selectpicker('val',
                                                            cityData[j].id);
                                                        $('#city').selectpicker('refresh');
                                                        $('#city').trigger('change');
                                                        return;
                                                    }
                                                    var words = cname.split(/\s+/);
                                                    if (words.indexOf(tok) !== -1) {
                                                        if ($('#city option[value="' +
                                                                cityData[j].id + '"]')
                                                            .length === 0) {
                                                            $('#city').append(new Option(
                                                                cityData[j]
                                                                .city_name,
                                                                cityData[j].id));
                                                            $('#city').selectpicker(
                                                                'refresh');
                                                        }
                                                        $('#city').selectpicker('val',
                                                            cityData[j].id);
                                                        $('#city').selectpicker('refresh');
                                                        $('#city').trigger('change');
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

                            $('#address').val(formattedAddress);
                            if (principal.address) {
                                $('#address').val(principal.address);
                            }
                        }
                    } else {
                        console.log('No principal contact details found');
                    }
                } else {
                    console.log('No data or details in response');
                }

                if (details.business_name) {
                    $('#AccoountName').val(details.business_name);
                }

                if (details.gstin) {
                    $('#vat').val(details.gstin);
                }
                if (details.pan_number) {
                    $('#Pan').val(details.pan_number);
                }

                // POPULATE LOCATION TABLE FROM GSTIN DATA
                populateLocationDataFromGSTIN(response.data);

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

                // Set GST Category to Un-Registered when GSTIN verification fails
                $('select[name=gst_type]').val('Un-Registered');
                $('.selectpicker').selectpicker('refresh');

                let gstinDropdown = $('#gstin_select');
                if (gstinDropdown.length > 0) {
                    gstinDropdown.after(
                        '<small style="color: red; display: block; margin-top: 8px; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-times"></i> ' +
                        msgText + '</small>');
                } else {
                    $('.gst_denger').append(
                        '<br><small style="color: red; font-weight: bold;" class="gstin-verify-msg"><i class="fa fa-times"></i> ' +
                        msgText + '</small>');
                }
            }

            // Refresh all selectpicker instances after populating data
            $('.selectpicker').selectpicker('refresh');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log("AJAX Error:", textStatus, errorThrown);
            console.log("Response:", jqXHR.responseText);
            $('.gstin-verify-msg').remove();
            alert_float('error', 'Error verifying GSTIN: ' + textStatus);

            // On AJAX error, set GST category to Un-Registered
            $('select[name=gst_type]').val('Un-Registered');
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

// Handle GSTIN selection from dropdown
function handleGstinSelection() {
    let selectedGstin = $('#gstin_select').val();
    if (selectedGstin) {
        $("#vat").val(selectedGstin);
        alert_float('success', 'GSTIN selected');
        verifyGSTIN();
    }
}
</script>

<script type="text/javascript">
$('#groups_in').on('change', function() {
    var selectedCustomerType = $(this).val();

    // Clear if nothing selected
    if (!selectedCustomerType) {
        $('#AccountID').val('');
        $('#HiddenVendorCode').val('');
        return;
    }

    var $select = $(this);
    $select.prop('disabled', true);

    $.ajax({
        url: "<?php echo admin_url('clients/GetNextCustomerCode'); ?>",
        method: 'POST',
        dataType: 'json',
        data: {
            ActSubGroupID2: selectedCustomerType
        },
        success: function(response) {
            if (response && response.status === 'success' && response.next_code) {
                $('#AccountID').val(response.next_code);
                $('#HiddenVendorCode').val(response.ActSubGroupID2 || selectedCustomerType);
                console.log('Customer Code generated: ' + response.next_code + ' (Category: ' + (
                        response.category_name || '') + ', Count: ' + (response.count || '') +
                    ')');
            } else {
                $('#AccountID').val('');
                $('#HiddenVendorCode').val('');
                alert_float('warning', (response && response.message) ? response.message :
                    'Failed to generate customer code');
            }
        },
        error: function(xhr) {
            console.error('Error generating customer code:', xhr.responseText || xhr.statusText);
            alert_float('error', 'Error generating customer code. Please try again.');
            $('#AccountID').val('');
            $('#HiddenVendorCode').val('');
        },
        complete: function() {
            $select.prop('disabled', false);
        }
    });
});

$('#MaxCrdAmt,#kms,.opening_bal,#crate_limit,#credit_days').on('keypress', function(event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
        event.preventDefault();
    }
});
</script>
<style>
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

/* Location Table Dropdown Fix */
#locationTable select {
    position: relative;
    z-index: 10;
}

#locationTable td {
    position: relative;
}

/* Reduce width of pincode inputs */
#zip {
    max-width: 140px;
    width: 100%;
    max-width: 140px;
    box-sizing: border-box;
    display: inline-block;
}

#locationTable th:nth-child(1),
#locationTable td:nth-child(1) {
    width: 8%;
    max-width: 8%;
}

#locationTable th:nth-child(2),
#locationTable td:nth-child(2) {
    width: 500px;
    max-width: 500px;
}

#locationTable th:nth-child(3),
#locationTable td:nth-child(3) {
    width: 250px;
    max-width: 250px;
}

#contactTable th:nth-child(1),
#contactTable td:nth-child(1) {
    width: 350px;
    max-width: 350px;
}

#contactTable th:nth-child(1),
#contactTable td:nth-child(2) {
    width: 350px;
    max-width: 350px;
}
</style>