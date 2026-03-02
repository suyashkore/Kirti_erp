<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'customer-master-form')); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr class="hr-panel-heading" />

                        <!-- Main Information -->
                        <h4 class="bold">Main Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_select('customer_category_id', $customer_groups, array('id', 'name'), 'Customer Category', isset($customer) ? $customer->customer_category_id : '', array('required' => 'true')); ?>
                                <?php echo render_input('customer_code', 'Customer Code', isset($customer) ? $customer->customer_code : $customer_code, 'text', array('readonly' => 'true')); ?>
                                <?php echo render_input('customer_name', 'Customer Name', isset($customer) ? $customer->customer_name : '', 'text', array('required' => 'true')); ?>
                                <?php echo render_input('favouring_name', 'Favouring Name', isset($customer) ? $customer->favouring_name : '', 'text', array('required' => 'true')); ?>
                            </div>
                        </div>
                        <hr />

                        <!-- General Information -->
                        <h4 class="bold">General Information</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_select('country', $countries, array('country_id', array('short_name'), 'iso2'), 'Country', isset($customer) ? $customer->country : 102, array('required' => 'true')); ?>
                                <?php echo render_select('state', $states, array('state_id', 'state_name'), 'State', isset($customer) ? $customer->state : '', array('required' => 'true')); ?>
                                <?php echo render_select('city', $cities, array('id', 'name'), 'City', isset($customer) ? $customer->city : '', array('required' => 'true')); ?>
                                <?php echo render_textarea('address', 'Address', isset($customer) ? $customer->address : '', array('required' => 'true')); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('pin_code', 'Pin Code', isset($customer) ? $customer->pin_code : '', 'number', array('required' => 'true', 'minlength' => 6, 'maxlength' => 6)); ?>
                                <?php echo render_input('mobile_no', 'Mobile No', isset($customer) ? $customer->mobile_no : '', 'number', array('minlength' => 10, 'maxlength' => 10)); ?>
                                <?php echo render_input('alternate_mobile_no', 'Alternate Mobile No', isset($customer) ? $customer->alternate_mobile_no : '', 'number', array('minlength' => 10, 'maxlength' => 10)); ?>
                                <?php echo render_input('email', 'Email ID', isset($customer) ? $customer->email : '', 'email'); ?>
                                <?php echo render_input('website', 'Website', isset($customer) ? $customer->website : '', 'url'); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('fssai_licence_no', 'FSSAI Licence No', isset($customer) ? $customer->fssai_licence_no : '', 'number', array('required' => 'true', 'maxlength' => 14)); ?>
                                <?php echo render_date_input('fssai_validity_date', 'FSSAI Validity Date', isset($customer) ? _d($customer->fssai_validity_date) : '', array('required' => 'true')); ?>
                                <?php echo render_select('territory_id', $territories, array('id', 'name'), 'Territory', isset($customer) ? $customer->territory_id : '', array('required' => 'true')); ?>
                                <?php echo render_select('broker_id', $brokers, array('id', 'name'), 'Broker', isset($customer) ? $customer->broker_id : '', array('required' => 'true')); ?>
                                <?php echo render_select('broker_person_id', $broker_persons, array('id', 'name'), 'Broker Person', isset($customer) ? $customer->broker_person_id : '', array('required' => 'true')); ?>
                                <?php echo render_select('freight_term_id', $freight_terms, array('id', 'name'), 'Freight Term', isset($customer) ? $customer->freight_term_id : '', array('required' => 'true')); ?>
                            </div>
                        </div>
                        <hr />

                        <!-- Contact Detail -->
                        <h4 class="bold">Contact Detail</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="contact-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Mobile Number</th>
                                        <th>Phone Number</th>
                                        <th>Email</th>
                                        <th>Send Email</th>
                                        <th>Send SMS</th>
                                        <th><button type="button" class="btn btn-success btn-sm" onclick="add_contact_row()"><i class="fa fa-plus"></i></button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($contacts) && count($contacts) > 0){ 
                                        foreach($contacts as $k => $contact){ ?>
                                        <tr>
                                            <td><input type="text" name="contact[<?php echo $k; ?>][name]" class="form-control" value="<?php echo $contact['name']; ?>"></td>
                                            <td><input type="text" name="contact[<?php echo $k; ?>][designation]" class="form-control" value="<?php echo $contact['designation']; ?>"></td>
                                            <td><input type="number" name="contact[<?php echo $k; ?>][mobile]" class="form-control" maxlength="10" value="<?php echo $contact['mobile']; ?>"></td>
                                            <td><input type="number" name="contact[<?php echo $k; ?>][phone]" class="form-control" maxlength="10" value="<?php echo $contact['phone']; ?>"></td>
                                            <td><input type="email" name="contact[<?php echo $k; ?>][email]" class="form-control" value="<?php echo $contact['email']; ?>"></td>
                                            <td class="text-center"><input type="checkbox" name="contact[<?php echo $k; ?>][send_email]" value="1" <?php if($contact['send_email'] == 1) echo 'checked'; ?>></td>
                                            <td class="text-center"><input type="checkbox" name="contact[<?php echo $k; ?>][send_sms]" value="1" <?php if($contact['send_sms'] == 1) echo 'checked'; ?>></td>
                                            <td><button type="button" class="btn btn-danger btn-sm" onclick="remove_row(this)"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                        <hr />

                        <!-- Location Information -->
                        <h4 class="bold">Location Information</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="location-table">
                                <thead>
                                    <tr>
                                        <th>State</th>
                                        <th>City</th>
                                        <th>Location</th>
                                        <th>Address</th>
                                        <th>Pincode</th>
                                        <th>Mobile</th>
                                        <th><button type="button" class="btn btn-success btn-sm" onclick="add_location_row()"><i class="fa fa-plus"></i></button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($locations) && count($locations) > 0){ 
                                        foreach($locations as $k => $loc){ ?>
                                        <tr>
                                            <td>
                                                <select name="location[<?php echo $k; ?>][state]" class="form-control">
                                                    <option value="">Select State</option>
                                                    <?php foreach($states as $state){ ?>
                                                        <option value="<?php echo $state['state_id']; ?>" <?php if($loc['state'] == $state['state_id']) echo 'selected'; ?>><?php echo $state['state_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="location[<?php echo $k; ?>][city]" class="form-control">
                                                    <option value="">Select City</option>
                                                    <?php foreach($cities as $city){ ?>
                                                        <option value="<?php echo $city['id']; ?>" <?php if($loc['city'] == $city['id']) echo 'selected'; ?>><?php echo $city['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="location[<?php echo $k; ?>][location_id]" class="form-control">
                                                    <option value="">Select Location</option>
                                                    <?php foreach($locations_master as $lm){ ?>
                                                        <option value="<?php echo $lm['id']; ?>" <?php if($loc['location_id'] == $lm['id']) echo 'selected'; ?>><?php echo $lm['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td><textarea name="location[<?php echo $k; ?>][address]" class="form-control"><?php echo $loc['address']; ?></textarea></td>
                                            <td><input type="number" name="location[<?php echo $k; ?>][pincode]" class="form-control" maxlength="6" value="<?php echo $loc['pincode']; ?>"></td>
                                            <td><input type="number" name="location[<?php echo $k; ?>][mobile]" class="form-control" maxlength="10" value="<?php echo $loc['mobile']; ?>"></td>
                                            <td><button type="button" class="btn btn-danger btn-sm" onclick="remove_row(this)"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                        <hr />

                        <!-- Accounting & Banking Information -->
                        <h4 class="bold">Accounting & Banking Information</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_select('default_currency', $currencies, array('id', 'name'), 'Default Currency', isset($customer) ? $customer->default_currency : '', array('required' => 'true')); ?>
                                <?php 
                                    $gst_categories = [
                                        ['id' => 'Regular', 'name' => 'Regular'],
                                        ['id' => 'Composite', 'name' => 'Composite'],
                                        ['id' => 'Unregistered', 'name' => 'Unregistered']
                                    ];
                                    echo render_select('gst_category', $gst_categories, array('id', 'name'), 'GST Category', isset($customer) ? $customer->gst_category : '', array('required' => 'true')); 
                                ?>
                                <div class="form-group">
                                    <label for="gstin" class="control-label">GSTIN</label>
                                    <div class="input-group">
                                        <input type="text" id="gstin" name="gstin" class="form-control" maxlength="15" value="<?php echo isset($customer) ? $customer->gstin : ''; ?>">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" onclick="fetch_gst_data()">Fetch</button>
                                        </span>
                                    </div>
                                </div>
                                <?php echo render_input('pan', 'PAN', isset($customer) ? $customer->pan : '', 'text', array('maxlength' => 10)); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ifsc_code" class="control-label">IFSC Code</label>
                                    <div class="input-group">
                                        <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" maxlength="11" value="<?php echo isset($customer) ? $customer->ifsc_code : ''; ?>">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" onclick="fetch_bank_details()">Fetch</button>
                                        </span>
                                    </div>
                                </div>
                                <?php echo render_input('bank_name', 'Bank Name', isset($customer) ? $customer->bank_name : ''); ?>
                                <?php echo render_input('branch_name', 'Branch Name', isset($customer) ? $customer->branch_name : ''); ?>
                                <?php echo render_input('account_number', 'Account Number', isset($customer) ? $customer->account_number : ''); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('account_holder_name', 'Account Holder Name', isset($customer) ? $customer->account_holder_name : ''); ?>
                                <?php 
                                    $temp_acc_types = [['id'=>'Type1', 'name'=>'Type 1'], ['id'=>'Type2', 'name'=>'Type 2']];
                                    echo render_select('temp_account_type', $temp_acc_types, array('id', 'name'), 'Temporary Account Type', isset($customer) ? $customer->temp_account_type : ''); 
                                ?>
                                <?php echo render_input('temp_account_number', 'Temporary Account Number', isset($customer) ? $customer->temp_account_number : ''); ?>
                                <?php echo render_input('tan_number', 'TAN Number', isset($customer) ? $customer->tan_number : ''); ?>
                                <?php echo render_select('tds_id', $tds_master, array('id', 'name'), 'TDS', isset($customer) ? $customer->tds_id : ''); ?>
                            </div>
                        </div>
                        <hr />

                        <!-- Credit & Payment Control Information -->
                        <h4 class="bold">Credit & Payment Control Information</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_input('credit_limit', 'Credit Limit', isset($customer) ? $customer->credit_limit : '', 'number', array('required' => 'true')); ?>
                                <?php echo render_input('current_outstanding', 'Current Outstanding', '0.00', 'text', array('readonly' => 'true')); ?>
                                <?php echo render_input('open_order_value', 'Open Order Value', '0.00', 'text', array('readonly' => 'true')); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('available_limit', 'Available Limit', '0.00', 'text', array('readonly' => 'true')); ?>
                                <?php echo render_input('avg_payment_day', 'Average Payment Day', '0', 'text', array('readonly' => 'true')); ?>
                                <?php echo render_input('avg_rebate', 'Average Rebate', '0.00', 'text', array('readonly' => 'true')); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_select('payment_terms_id', $payment_terms, array('id', 'name'), 'Payment Terms', isset($customer) ? $customer->payment_terms_id : ''); ?>
                                <?php 
                                    $cycles = [['id'=>'Due Date', 'name'=>'Due Date'], ['id'=>'Document Date', 'name'=>'Document Date'], ['id'=>'Posting Date', 'name'=>'Posting Date']];
                                    echo render_select('payment_cycle', $cycles, array('id', 'name'), 'Payment Cycle', isset($customer) ? $customer->payment_cycle : ''); 
                                ?>
                                <?php 
                                    $cycle_types = [['id'=>'Weekly', 'name'=>'Weekly'], ['id'=>'Monthly', 'name'=>'Monthly'], ['id'=>'Due Date', 'name'=>'Due Date']];
                                    echo render_select('payment_cycle_type', $cycle_types, array('id', 'name'), 'Payment Cycle Type', isset($customer) ? $customer->payment_cycle_type : ''); 
                                ?>
                            </div>
                        </div>
                        <hr />

                        <!-- Other Information -->
                        <h4 class="bold">Other Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_textarea('additional_information', 'Additional Information', isset($customer) ? $customer->additional_information : ''); ?>
                                <div class="form-group">
                                    <label for="attachment" class="control-label">Attachment</label>
                                    <input type="file" name="attachment" class="form-control">
                                    <?php if(isset($customer) && $customer->attachment){ echo '<a href="'.base_url('uploads/customer_master/'.$customer->attachment).'" target="_blank">View File</a>'; } ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="checkbox checkbox-danger">
                                    <input type="checkbox" name="blocked" id="blocked" value="1" <?php if(isset($customer) && $customer->blocked == 1) echo 'checked'; ?>>
                                    <label for="blocked">Blocked</label>
                                </div>
                                <?php echo render_textarea('blocked_reason', 'Blocked Reason', isset($customer) ? $customer->blocked_reason : ''); ?>
                                
                                <?php if(isset($customer)){ ?>
                                    <p>Created By: <?php echo get_staff_full_name($customer->created_by); ?></p>
                                    <p>Created On: <?php echo _dt($customer->created_at); ?></p>
                                    <p>Updated By: <?php echo get_staff_full_name($customer->updated_by); ?></p>
                                    <p>Updated On: <?php echo _dt($customer->updated_at); ?></p>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var contact_row = <?php echo isset($contacts) ? count($contacts) : 0; ?>;
    var location_row = <?php echo isset($locations) ? count($locations) : 0; ?>;

    function add_contact_row() {
        var html = '<tr>';
        html += '<td><input type="text" name="contact['+contact_row+'][name]" class="form-control"></td>';
        html += '<td><input type="text" name="contact['+contact_row+'][designation]" class="form-control"></td>';
        html += '<td><input type="number" name="contact['+contact_row+'][mobile]" class="form-control" maxlength="10"></td>';
        html += '<td><input type="number" name="contact['+contact_row+'][phone]" class="form-control" maxlength="10"></td>';
        html += '<td><input type="email" name="contact['+contact_row+'][email]" class="form-control"></td>';
        html += '<td class="text-center"><input type="checkbox" name="contact['+contact_row+'][send_email]" value="1"></td>';
        html += '<td class="text-center"><input type="checkbox" name="contact['+contact_row+'][send_sms]" value="1"></td>';
        html += '<td><button type="button" class="btn btn-danger btn-sm" onclick="remove_row(this)"><i class="fa fa-trash"></i></button></td>';
        html += '</tr>';
        $('#contact-table tbody').append(html);
        contact_row++;
    }

    function add_location_row() {
        // Note: You should populate options dynamically via JS or clone a hidden template for better performance
        var html = '<tr>';
        html += '<td><select name="location['+location_row+'][state]" class="form-control"><option value="">Select State</option><?php foreach($states as $state){ echo "<option value=\'".$state['state_id']."\'>".$state['state_name']."</option>"; } ?></select></td>';
        html += '<td><select name="location['+location_row+'][city]" class="form-control"><option value="">Select City</option><?php foreach($cities as $city){ echo "<option value=\'".$city['id']."\'>".$city['name']."</option>"; } ?></select></td>';
        html += '<td><select name="location['+location_row+'][location_id]" class="form-control"><option value="">Select Location</option><?php foreach($locations_master as $lm){ echo "<option value=\'".$lm['id']."\'>".$lm['name']."</option>"; } ?></select></td>';
        html += '<td><textarea name="location['+location_row+'][address]" class="form-control"></textarea></td>';
        html += '<td><input type="number" name="location['+location_row+'][pincode]" class="form-control" maxlength="6"></td>';
        html += '<td><input type="number" name="location['+location_row+'][mobile]" class="form-control" maxlength="10"></td>';
        html += '<td><button type="button" class="btn btn-danger btn-sm" onclick="remove_row(this)"><i class="fa fa-trash"></i></button></td>';
        html += '</tr>';
        $('#location-table tbody').append(html);
        location_row++;
    }

    function remove_row(btn) {
        $(btn).closest('tr').remove();
    }

    function fetch_gst_data() {
        var gstin = $('#gstin').val();
        if(gstin.length == 15) {
            // Implement AJAX call to your API here
            alert('API Integration required for GSTIN: ' + gstin);
            // On success, populate PAN and other fields
            // $('#pan').val(gstin.substring(2, 12));
        } else {
            alert('Invalid GSTIN');
        }
    }

    function fetch_bank_details() {
        var ifsc = $('#ifsc_code').val();
        if(ifsc.length == 11) {
            // Implement AJAX call to bank API
            alert('API Integration required for IFSC: ' + ifsc);
        }
    }

    // Dependent Dropdowns for Country -> State -> City
    $('select[name="country"]').on('change', function() {
        var country_id = $(this).val();
        // Ajax call to fetch states
        // $.get(admin_url + 'misc/get_states/' + country_id, function(response) { ... });
    });

    $('select[name="state"]').on('change', function() {
        var state_id = $(this).val();
        // Ajax call to fetch cities
    });
</script>
</body>
</html>