<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .form-section { margin-bottom: 30px; padding: 15px; background: #f9f9f9; border-left: 4px solid #50607b; }
    .section-title { font-size: 16px; font-weight: 600; color: #50607b; margin-bottom: 15px; }
    .required::after { content: " *"; color: red; }
    .add-row-btn { margin-top: 10px; }
    .table-responsive table { font-size: 12px; }
    .dynamic-row { margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-left: 3px solid #ddd; }
    .dynamic-row .row { margin-bottom: 5px; }
    .btn-remove-row { color: #d9534f; cursor: pointer; margin-left: 10px; }
    .readonly-field { background-color: #ecf0f1; }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s" style="margin-top: 1.5rem;">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item"><a href="<?= admin_url('customer_master'); ?>">Customer Master</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><b><?php echo $title; ?></b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">

                        <?php echo form_open_multipart(admin_url('customer_master/customer' . (!empty($customer) ? '/' . $customer->id : '')), array('class' => 'form-horizontal')); ?>

                        <!-- MAIN INFORMATION SECTION -->
                        <div class="form-section">
                            <div class="section-title"><i class="fa fa-info-circle"></i> Main Information</div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label required">Customer Category</label>
                                <div class="col-md-6">
                                    <select name="customer_category" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <?php if (!empty($customer_groups)) { ?>
                                            <?php foreach ($customer_groups as $group) { ?>
                                                <option value="<?php echo htmlspecialchars($group['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->customer_category == $group['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($group['name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Customer Code</label>
                                <div class="col-md-6">
                                    <input type="text" name="customer_code" class="form-control" value="<?php echo $customer_code ?? (!empty($customer) ? htmlspecialchars($customer->customer_code) : ''); ?>" readonly>
                                    <small class="help-block">Auto-generated</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Customer Name</label>
                                <div class="col-md-6">
                                    <input type="text" name="customer_name" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->customer_name) : ''; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Favouring Name</label>
                                <div class="col-md-6">
                                    <input type="text" name="favouring_name" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->favouring_name) : ''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- GENERAL INFORMATION SECTION -->
                        <div class="form-section">
                            <div class="section-title"><i class="fa fa-map-marker"></i> General Information</div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label required">Country</label>
                                <div class="col-md-6">
                                    <select name="country" class="form-control" id="country_select" required onchange="loadStates()">
                                        <option value="">-- Select Country --</option>
                                        <?php if (!empty($countries)) { ?>
                                            <?php foreach ($countries as $country) { ?>
                                                <option value="<?php echo htmlspecialchars($country['country_id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->country == $country['country_id']) || $country['country_name'] == 'India' ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($country['country_name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">State</label>
                                <div class="col-md-6">
                                    <select name="state" class="form-control" id="state_select" required onchange="loadCities()">
                                        <option value="">-- Select State --</option>
                                        <?php if (!empty($states)) { ?>
                                            <?php foreach ($states as $state) { ?>
                                                <option value="<?php echo htmlspecialchars($state['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->state == $state['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($state['state_name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">City</label>
                                <div class="col-md-6">
                                    <select name="city" class="form-control" id="city_select" required>
                                        <option value="">-- Select City --</option>
                                        <?php if (!empty($cities)) { ?>
                                            <?php foreach ($cities as $city) { ?>
                                                <option value="<?php echo htmlspecialchars($city['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->city == $city['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($city['city_name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Address</label>
                                <div class="col-md-6">
                                    <textarea name="address" class="form-control" rows="3" required><?php echo !empty($customer) ? htmlspecialchars($customer->address) : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Pin Code</label>
                                <div class="col-md-6">
                                    <input type="text" name="pin_code" class="form-control" placeholder="6 digits" pattern="[0-9]{6}" value="<?php echo !empty($customer) ? htmlspecialchars($customer->pin_code) : ''; ?>" required>
                                    <small class="help-block">6 digits only</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Mobile No</label>
                                <div class="col-md-6">
                                    <input type="tel" name="mobile_no" class="form-control" placeholder="10 digits" pattern="[0-9]{10}" value="<?php echo !empty($customer) ? htmlspecialchars($customer->mobile_no) : ''; ?>">
                                    <small class="help-block">10 digits</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Alternate Mobile No</label>
                                <div class="col-md-6">
                                    <input type="tel" name="alternate_mobile_no" class="form-control" placeholder="10 digits" pattern="[0-9]{10}" value="<?php echo !empty($customer) ? htmlspecialchars($customer->alternate_mobile_no) : ''; ?>">
                                    <small class="help-block">10 digits</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Email ID</label>
                                <div class="col-md-6">
                                    <input type="email" name="email_id" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->email_id) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Website</label>
                                <div class="col-md-6">
                                    <input type="url" name="website" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->website) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">FSSAI Licence No</label>
                                <div class="col-md-6">
                                    <input type="text" name="fssai_licence_no" class="form-control" placeholder="14 digits" pattern="[0-9]{14}" value="<?php echo !empty($customer) ? htmlspecialchars($customer->fssai_licence_no) : ''; ?>" required>
                                    <small class="help-block">14 digits - Number only</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">FSSAI Validity Date</label>
                                <div class="col-md-6">
                                    <input type="date" name="fssai_validity_date" class="form-control" value="<?php echo !empty($customer) && $customer->fssai_validity_date ? $customer->fssai_validity_date : ''; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Territory</label>
                                <div class="col-md-6">
                                    <select name="territory" class="form-control" required>
                                        <option value="">-- Select Territory --</option>
                                        <?php if (!empty($territories)) { ?>
                                            <?php foreach ($territories as $territory) { ?>
                                                <option value="<?php echo htmlspecialchars($territory['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->territory == $territory['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($territory['name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Broker</label>
                                <div class="col-md-6">
                                    <select name="broker" class="form-control" required onchange="loadBrokerPersons()">
                                        <option value="">-- Select Broker --</option>
                                        <?php if (!empty($brokers)) { ?>
                                            <?php foreach ($brokers as $broker) { ?>
                                                <option value="<?php echo htmlspecialchars($broker['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->broker == $broker['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($broker['name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Broker Person</label>
                                <div class="col-md-6">
                                    <select name="broker_person" class="form-control" id="broker_person_select" required>
                                        <option value="">-- Select Broker Person --</option>
                                        <?php if (!empty($broker_persons)) { ?>
                                            <?php foreach ($broker_persons as $person) { ?>
                                                <option value="<?php echo htmlspecialchars($person['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->broker_person == $person['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($person['name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Freight Term</label>
                                <div class="col-md-6">
                                    <select name="freight_term" class="form-control" required>
                                        <option value="">-- Select Freight Term --</option>
                                        <?php if (!empty($freight_terms)) { ?>
                                            <?php foreach ($freight_terms as $freight) { ?>
                                                <option value="<?php echo htmlspecialchars($freight['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->freight_term == $freight['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($freight['name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- CONTACT DETAILS SECTION -->
                        <div class="form-section">
                            <div class="section-title"><i class="fa fa-phone"></i> Contact Details 
                                <button type="button" class="btn btn-xs btn-primary add-row-btn" onclick="addContactRow()">
                                    <i class="fa fa-plus"></i> Add Row
                                </button>
                            </div>
                            <div id="contacts_container">
                                <?php if (!empty($contacts)) { ?>
                                    <?php foreach ($contacts as $idx => $contact) { ?>
                                        <div class="dynamic-row" id="contact_row_<?php echo $idx; ?>">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>Name</label>
                                                    <input type="text" name="contact[<?php echo $idx; ?>][name]" class="form-control" value="<?php echo htmlspecialchars($contact['name']); ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Designation</label>
                                                    <input type="text" name="contact[<?php echo $idx; ?>][designation]" class="form-control" value="<?php echo htmlspecialchars($contact['designation']); ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Mobile Number</label>
                                                    <input type="tel" name="contact[<?php echo $idx; ?>][mobile_number]" class="form-control" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($contact['mobile_number']); ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Phone Number</label>
                                                    <input type="tel" name="contact[<?php echo $idx; ?>][phone_number]" class="form-control" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($contact['phone_number']); ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Email</label>
                                                    <input type="email" name="contact[<?php echo $idx; ?>][email]" class="form-control" value="<?php echo htmlspecialchars($contact['email']); ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Send Email</label>
                                                    <input type="checkbox" name="contact[<?php echo $idx; ?>][send_email]" value="1" <?php echo $contact['send_email'] ? 'checked' : ''; ?>>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Send SMS</label>
                                                    <input type="checkbox" name="contact[<?php echo $idx; ?>][send_sms]" value="1" <?php echo $contact['send_sms'] ? 'checked' : ''; ?>>
                                                </div>
                                                <div class="col-md-4" style="text-align: right; padding-top: 25px;">
                                                    <a href="javascript:void(0);" class="btn-remove-row" onclick="removeContactRow(<?php echo $idx; ?>)">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- LOCATION INFORMATION SECTION -->
                        <div class="form-section">
                            <div class="section-title"><i class="fa fa-building"></i> Location Information 
                                <button type="button" class="btn btn-xs btn-primary add-row-btn" onclick="addLocationRow()">
                                    <i class="fa fa-plus"></i> Add Row
                                </button>
                            </div>
                            <div id="locations_container">
                                <?php if (!empty($locations)) { ?>
                                    <?php foreach ($locations as $idx => $location) { ?>
                                        <div class="dynamic-row" id="location_row_<?php echo $idx; ?>">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label>State</label>
                                                    <select name="location[<?php echo $idx; ?>][state]" class="form-control">
                                                        <option value="">-- Select State --</option>
                                                        <?php if (!empty($states)) { ?>
                                                            <?php foreach ($states as $state) { ?>
                                                                <option value="<?php echo htmlspecialchars($state['id']); ?>" 
                                                                    <?php echo $location['state'] == $state['id'] ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($state['state_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>City</label>
                                                    <select name="location[<?php echo $idx; ?>][city]" class="form-control">
                                                        <option value="">-- Select City --</option>
                                                        <?php if (!empty($cities)) { ?>
                                                            <?php foreach ($cities as $city) { ?>
                                                                <option value="<?php echo htmlspecialchars($city['id']); ?>" 
                                                                    <?php echo $location['city'] == $city['id'] ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($city['city_name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Location</label>
                                                    <select name="location[<?php echo $idx; ?>][location_master]" class="form-control">
                                                        <option value="">-- Select Location --</option>
                                                        <?php if (!empty($locations_master)) { ?>
                                                            <?php foreach ($locations_master as $loc) { ?>
                                                                <option value="<?php echo htmlspecialchars($loc['id']); ?>" 
                                                                    <?php echo $location['location_master'] == $loc['id'] ? 'selected' : ''; ?>>
                                                                    <?php echo htmlspecialchars($loc['name']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Pincode</label>
                                                    <input type="text" name="location[<?php echo $idx; ?>][pincode]" class="form-control" pattern="[0-9]{6}" value="<?php echo htmlspecialchars($location['pincode']); ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <label>Address</label>
                                                    <textarea name="location[<?php echo $idx; ?>][address]" class="form-control" rows="2"><?php echo htmlspecialchars($location['address']); ?></textarea>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Mobile</label>
                                                    <input type="tel" name="location[<?php echo $idx; ?>][mobile]" class="form-control" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($location['mobile']); ?>">
                                                </div>
                                                <div class="col-md-2" style="text-align: right; padding-top: 25px;">
                                                    <a href="javascript:void(0);" class="btn-remove-row" onclick="removeLocationRow(<?php echo $idx; ?>)">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- ACCOUNTING & BANKING INFORMATION SECTION -->
                        <div class="form-section">
                            <div class="section-title"><i class="fa fa-bank"></i> Accounting & Banking Information</div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label required">Default Currency</label>
                                <div class="col-md-6">
                                    <select name="default_currency" class="form-control" required>
                                        <option value="">-- Select Currency --</option>
                                        <?php if (!empty($currencies)) { ?>
                                            <?php foreach ($currencies as $currency) { ?>
                                                <option value="<?php echo htmlspecialchars($currency['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->default_currency == $currency['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($currency['name'] . ' (' . $currency['symbol'] . ')'); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">GST Category</label>
                                <div class="col-md-6">
                                    <select name="gst_category" class="form-control" required>
                                        <option value="Regular" <?php echo (!empty($customer) && $customer->gst_category == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                                        <option value="Composite" <?php echo (!empty($customer) && $customer->gst_category == 'Composite') ? 'selected' : ''; ?>>Composite</option>
                                        <option value="SEZ" <?php echo (!empty($customer) && $customer->gst_category == 'SEZ') ? 'selected' : ''; ?>>SEZ</option>
                                        <option value="Exempt" <?php echo (!empty($customer) && $customer->gst_category == 'Exempt') ? 'selected' : ''; ?>>Exempt</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">GSTIN</label>
                                <div class="col-md-6">
                                    <input type="text" name="gstin" class="form-control" placeholder="15 digits" pattern="[0-9]{15}" value="<?php echo !empty($customer) ? htmlspecialchars($customer->gstin) : ''; ?>">
                                    <small class="help-block">15 digits - GSTIN format validation</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">PAN</label>
                                <div class="col-md-6">
                                    <input type="text" name="pan" class="form-control" placeholder="11 digits" pattern="[A-Z0-9]{10}[A-Z]" value="<?php echo !empty($customer) ? htmlspecialchars($customer->pan) : ''; ?>">
                                    <small class="help-block">11 characters - PAN format</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">IFSC Code</label>
                                <div class="col-md-6">
                                    <input type="text" name="ifsc_code" class="form-control" placeholder="11 characters" maxlength="11" value="<?php echo !empty($customer) ? htmlspecialchars($customer->ifsc_code) : ''; ?>">
                                    <small class="help-block">11 characters</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Bank Name</label>
                                <div class="col-md-6">
                                    <input type="text" name="bank_name" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->bank_name) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Branch Name</label>
                                <div class="col-md-6">
                                    <input type="text" name="branch_name" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->branch_name) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Account Number</label>
                                <div class="col-md-6">
                                    <input type="text" name="account_number" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->account_number) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Account Holder Name</label>
                                <div class="col-md-6">
                                    <input type="text" name="account_holder_name" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->account_holder_name) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Temporary Account Type</label>
                                <div class="col-md-6">
                                    <select name="temporary_account_type" class="form-control">
                                        <option value="">-- Select Type --</option>
                                        <option value="Savings" <?php echo (!empty($customer) && $customer->temporary_account_type == 'Savings') ? 'selected' : ''; ?>>Savings</option>
                                        <option value="Current" <?php echo (!empty($customer) && $customer->temporary_account_type == 'Current') ? 'selected' : ''; ?>>Current</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Temporary Account Number</label>
                                <div class="col-md-6">
                                    <input type="text" name="temporary_account_number" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->temporary_account_number) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">TAN Number</label>
                                <div class="col-md-6">
                                    <input type="text" name="tan_number" class="form-control" value="<?php echo !empty($customer) ? htmlspecialchars($customer->tan_number) : ''; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">TDS</label>
                                <div class="col-md-6">
                                    <select name="tds" class="form-control">
                                        <option value="">-- Select TDS --</option>
                                        <?php if (!empty($tds_master)) { ?>
                                            <?php foreach ($tds_master as $tds) { ?>
                                                <option value="<?php echo htmlspecialchars($tds['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->tds == $tds['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($tds['rate']); ?>%
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- CREDIT & PAYMENT CONTROL INFORMATION SECTION -->
                        <div class="form-section">
                            <div class="section-title"><i class="fa fa-credit-card"></i> Credit & Payment Control Information</div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label required">Credit Limit</label>
                                <div class="col-md-6">
                                    <input type="number" name="credit_limit" class="form-control" step="0.01" value="<?php echo !empty($customer) ? $customer->credit_limit : ''; ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Current Outstanding</label>
                                <div class="col-md-6">
                                    <input type="number" name="current_outstanding" class="form-control readonly-field" value="<?php echo !empty($customer) ? $customer->current_outstanding : '0'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Open Order Value</label>
                                <div class="col-md-6">
                                    <input type="number" name="open_order_value" class="form-control readonly-field" value="<?php echo !empty($customer) ? $customer->open_order_value : '0'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Available Limit</label>
                                <div class="col-md-6">
                                    <input type="number" name="available_limit" class="form-control readonly-field" value="<?php echo !empty($customer) ? $customer->available_limit : '0'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Average Payment Day</label>
                                <div class="col-md-6">
                                    <input type="number" name="average_payment_day" class="form-control readonly-field" value="<?php echo !empty($customer) ? $customer->average_payment_day : '0'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Average Rebate</label>
                                <div class="col-md-6">
                                    <input type="number" name="average_rebate" class="form-control readonly-field" value="<?php echo !empty($customer) ? $customer->average_rebate : '0'; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Payment Terms</label>
                                <div class="col-md-6">
                                    <select name="payment_terms" class="form-control" required>
                                        <option value="">-- Select Payment Terms --</option>
                                        <?php if (!empty($payment_terms)) { ?>
                                            <?php foreach ($payment_terms as $terms) { ?>
                                                <option value="<?php echo htmlspecialchars($terms['id']); ?>" 
                                                    <?php echo (!empty($customer) && $customer->payment_terms == $terms['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($terms['name']); ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Payment Cycle</label>
                                <div class="col-md-6">
                                    <select name="payment_cycle" class="form-control" required>
                                        <option value="Due Date" <?php echo (!empty($customer) && $customer->payment_cycle == 'Due Date') ? 'selected' : ''; ?>>Due Date</option>
                                        <option value="Document Date" <?php echo (!empty($customer) && $customer->payment_cycle == 'Document Date') ? 'selected' : ''; ?>>Document Date</option>
                                        <option value="Posting Date" <?php echo (!empty($customer) && $customer->payment_cycle == 'Posting Date') ? 'selected' : ''; ?>>Posting Date</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label required">Payment Cycle Type</label>
                                <div class="col-md-6">
                                    <select name="payment_cycle_type" class="form-control" required>
                                        <option value="Weekly" <?php echo (!empty($customer) && $customer->payment_cycle_type == 'Weekly') ? 'selected' : ''; ?>>Weekly</option>
                                        <option value="Monthly" <?php echo (!empty($customer) && $customer->payment_cycle_type == 'Monthly') ? 'selected' : ''; ?>>Monthly</option>
                                        <option value="Due Date" <?php echo (!empty($customer) && $customer->payment_cycle_type == 'Due Date') ? 'selected' : ''; ?>>Due Date</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- OTHER INFORMATION SECTION -->
                        <div class="form-section">
                            <div class="section-title"><i class="fa fa-file-o"></i> Other Information</div>
                            
                            <div class="form-group">
                                <label class="col-md-3 control-label">Additional Information</label>
                                <div class="col-md-6">
                                    <textarea name="additional_information" class="form-control" rows="4"><?php echo !empty($customer) ? htmlspecialchars($customer->additional_information) : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Attachment</label>
                                <div class="col-md-6">
                                    <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    <small class="help-block">Allowed: jpg, jpeg, png, pdf, doc, docx</small>
                                    <?php if (!empty($customer) && !empty($customer->attachment)) { ?>
                                        <br><a href="<?php echo base_url('uploads/customer_master/' . $customer->attachment); ?>" target="_blank">
                                            <i class="fa fa-download"></i> <?php echo htmlspecialchars($customer->attachment); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Created By</label>
                                <div class="col-md-6">
                                    <input type="text" name="created_by" class="form-control readonly-field" value="<?php echo !empty($customer) ? htmlspecialchars($customer->created_by) : get_staff_user_id(); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Created On</label>
                                <div class="col-md-6">
                                    <input type="text" name="created_at" class="form-control readonly-field" value="<?php echo !empty($customer) && $customer->created_at ? date('d-m-Y H:i:s', strtotime($customer->created_at)) : date('d-m-Y H:i:s'); ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Updated By</label>
                                <div class="col-md-6">
                                    <input type="text" name="updated_by" class="form-control readonly-field" value="<?php echo !empty($customer) ? htmlspecialchars($customer->updated_by) : ''; ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Updated On</label>
                                <div class="col-md-6">
                                    <input type="text" name="updated_at" class="form-control readonly-field" value="<?php echo !empty($customer) && $customer->updated_at ? date('d-m-Y H:i:s', strtotime($customer->updated_at)) : ''; ?>" readonly>
                                </div>
                            </div>

                            

                            <div class="form-group">
                                <label class="col-md-3 control-label">Blocked</label>
                                <div class="col-md-6">
                                    <input type="checkbox" name="is_blocked" value="1" <?php echo (!empty($customer) && $customer->is_blocked) ? 'checked' : ''; ?>>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Blocked Reason</label>
                                <div class="col-md-6">
                                    <textarea name="blocked_reason" class="form-control" rows="3"><?php echo !empty($customer) ? htmlspecialchars($customer->blocked_reason) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- FORM BUTTONS -->
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> <?php echo !empty($customer) ? 'Update Customer' : 'Save Customer'; ?>
                                </button>
                                <a href="<?php echo admin_url('customer_master'); ?>" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
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
var contactRowCounter = <?php echo !empty($contacts) ? count($contacts) : 0; ?>;
var locationRowCounter = <?php echo !empty($locations) ? count($locations) : 0; ?>;

function addContactRow() {
    const row = `
        <div class="dynamic-row" id="contact_row_${contactRowCounter}">
            <div class="row">
                <div class="col-md-3">
                    <label>Name</label>
                    <input type="text" name="contact[${contactRowCounter}][name]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Designation</label>
                    <input type="text" name="contact[${contactRowCounter}][designation]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Mobile Number</label>
                    <input type="tel" name="contact[${contactRowCounter}][mobile_number]" class="form-control" pattern="[0-9]{10}">
                </div>
                <div class="col-md-3">
                    <label>Phone Number</label>
                    <input type="tel" name="contact[${contactRowCounter}][phone_number]" class="form-control" pattern="[0-9]{10}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label>Email</label>
                    <input type="email" name="contact[${contactRowCounter}][email]" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Send Email</label>
                    <input type="checkbox" name="contact[${contactRowCounter}][send_email]" value="1">
                </div>
                <div class="col-md-2">
                    <label>Send SMS</label>
                    <input type="checkbox" name="contact[${contactRowCounter}][send_sms]" value="1">
                </div>
                <div class="col-md-4" style="text-align: right; padding-top: 25px;">
                    <a href="javascript:void(0);" class="btn-remove-row" onclick="removeContactRow(${contactRowCounter})">
                        <i class="fa fa-trash"></i> Remove
                    </a>
                </div>
            </div>
        </div>
    `;
    document.getElementById('contacts_container').insertAdjacentHTML('beforeend', row);
    contactRowCounter++;
}

function removeContactRow(index) {
    const row = document.getElementById(`contact_row_${index}`);
    if (row) {
        row.remove();
    }
}

function addLocationRow() {
    const row = `
        <div class="dynamic-row" id="location_row_${locationRowCounter}">
            <div class="row">
                <div class="col-md-3">
                    <label>State</label>
                    <select name="location[${locationRowCounter}][state]" class="form-control">
                        <option value="">-- Select State --</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>City</label>
                    <select name="location[${locationRowCounter}][city]" class="form-control">
                        <option value="">-- Select City --</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Location</label>
                    <select name="location[${locationRowCounter}][location_master]" class="form-control">
                        <option value="">-- Select Location --</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Pincode</label>
                    <input type="text" name="location[${locationRowCounter}][pincode]" class="form-control" pattern="[0-9]{6}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <label>Address</label>
                    <textarea name="location[${locationRowCounter}][address]" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-2">
                    <label>Mobile</label>
                    <input type="tel" name="location[${locationRowCounter}][mobile]" class="form-control" pattern="[0-9]{10}">
                </div>
                <div class="col-md-2" style="text-align: right; padding-top: 25px;">
                    <a href="javascript:void(0);" class="btn-remove-row" onclick="removeLocationRow(${locationRowCounter})">
                        <i class="fa fa-trash"></i> Remove
                    </a>
                </div>
            </div>
        </div>
    `;
    document.getElementById('locations_container').insertAdjacentHTML('beforeend', row);
    locationRowCounter++;
}

function removeLocationRow(index) {
    const row = document.getElementById(`location_row_${index}`);
    if (row) {
        row.remove();
    }
}

function loadStates() {
    // This would typically load states based on selected country via AJAX
    // For now, using the pre-loaded states
}

function loadCities() {
    // This would typically load cities based on selected state via AJAX
    // For now, using the pre-loaded cities
}

function loadBrokerPersons() {
    // This would typically load broker persons based on selected broker via AJAX
    // For now, using the pre-loaded broker persons
}
</script>
