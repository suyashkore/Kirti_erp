<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?php echo $title; ?>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open($this->uri->uri_string()); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->company_name : ''); ?>
                                <?php echo render_input('company_name', 'Company Name', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->comp_short : ''); ?>
                                <?php echo render_input('comp_short', 'Short Code', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->gst : ''); ?>
                                <?php echo render_input('gst', 'GST No', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->mobile1 : ''); ?>
                                <?php echo render_input('mobile1', 'Mobile No', $value, 'number'); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->BusinessEmail : ''); ?>
                                <?php echo render_input('BusinessEmail', 'Email ID', $value, 'email'); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->state : ''); ?>
                                <?php echo render_input('state', 'State', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->city : ''); ?>
                                <?php echo render_input('city', 'City', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <?php $value = (isset($company) ? $company->pincode : ''); ?>
                                <?php echo render_input('pincode', 'Pincode', $value, 'number'); ?>
                            </div>
                            <div class="col-md-12">
                                <?php $value = (isset($company) ? $company->address : ''); ?>
                                <?php echo render_textarea('address', 'Address', $value); ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="Y" <?php if(isset($company) && $company->status == 1){ echo 'selected'; } ?>>Active</option>
                                        <option value="n" <?php if(isset($company) && $company->status == 2){ echo 'selected'; } ?>>Deactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>
