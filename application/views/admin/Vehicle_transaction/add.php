<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" style="min-height:1px">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <style>
                            @media (max-width: 767px) {
                                .mobile-menu-btn { display: block !important; margin-bottom: 10px; width: 100%; text-align: left; }
                                .custombreadcrumb { display: none !important; }
                                .custombreadcrumb.open { display: block !important; }
                                .custombreadcrumb li { display: block; padding: 8px 10px; border-bottom: 1px solid #eee; }
                                .custombreadcrumb li a { display: block; }
                                .custombreadcrumb li+li:before { content: none; }
                            }
                            .mobile-menu-btn { display: none; }
                        </style>
                        <button class="btn btn-default mobile-menu-btn" onclick="$('.custombreadcrumb').toggleClass('open')">
                            <i class="fa fa-bars"></i> Menu
                        </button>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important; display: flex; flex-wrap: wrap;">
                                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Vehicle Transaction</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait while fetching data.</div>
                                <div class="searchh3" style="display:none;">Please wait while creating new record.</div>
                                <div class="searchh4" style="display:none;">Please wait while updating data.</div>
                            </div>
                        </div>
                        
                        <form action="<?php echo admin_url('vehicle_transaction/add'); ?>" method="post">
                            
                            <!-- Main Information Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold p_style">Main Information</h4>
                                    <hr class="hr_style">
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="plant" class="control-label">Plant</label>
                                        <select name="plant" id="plant" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($plants as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="category" class="control-label">Category</label>
                                        <select name="category" id="category" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($categories as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="sale_type" class="control-label">Type</label>
                                        <div>
                                            <label class="radio-inline"><input type="radio" name="sale_type" value="Retail" checked> Retail</label>
                                            <label class="radio-inline"><input type="radio" name="sale_type" value="Loose"> Loose</label>
                                            <label class="radio-inline"><input type="radio" name="sale_type" value="Other"> Other</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="delivery_date" class="control-label">Delivery Date</label>
                                        <input type="date" id="delivery_date" name="delivery_date" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- General Information Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold p_style">General Information</h4>
                                    <hr class="hr_style">
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="advance_regular" class="control-label">Advance/Regular</label>
                                        <select name="advance_regular" id="advance_regular" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($advance_regular as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="dispatch_from" class="control-label">Dispatch From</label>
                                        <select name="dispatch_from" id="dispatch_from" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($dispatch_from as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="customer_name_manual" class="control-label">Enter Customer Name</label>
                                        <input type="text" id="customer_name_manual" name="customer_name_manual" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="customer_id" class="control-label">Customer</label>
                                        <select name="customer_id" id="customer_id" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($customers as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="consignee_id" class="control-label">Consignee</label>
                                        <select name="consignee_id" id="consignee_id" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($consignees as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="consignee_location" class="control-label">Consignee Party Location</label>
                                        <select name="consignee_location" id="consignee_location" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($locations as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="shift_to_location" class="control-label">Shift To Party Location</label>
                                        <select name="shift_to_location" id="shift_to_location" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($locations as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="gate_entry_no" class="control-label">Gate Entry No.</label>
                                        <input type="text" id="gate_entry_no" name="gate_entry_no" class="form-control" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address_display" class="control-label">Address Display</label>
                                        <div style="min-height: 50px; border: 1px solid #ccc; background: #f9f9f9; padding: 10px; border-radius: 3px;">
                                            <!-- Address will be displayed here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transport Detail Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold p_style">Transport Detail</h4>
                                    <hr class="hr_style">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="transport_by_customer" class="control-label">Transport Arrange by Customer</label>
                                        <div>
                                            <label class="radio-inline"><input type="radio" name="transport_by_customer" value="No" checked> No</label>
                                            <label class="radio-inline"><input type="radio" name="transport_by_customer" value="Yes"> Yes</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="truck_no" class="control-label">Truck No.</label>
                                        <select name="truck_no" id="truck_no" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($trucks as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <small class="req text-danger">* </small>
                                        <label for="transport_id" class="control-label">Transport</label>
                                        <select name="transport_id" id="transport_id" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach($transporters as $key => $val): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="lr_number" class="control-label">LR Number</label>
                                        <input type="text" id="lr_number" name="lr_number" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="driver_name" class="control-label">Driver Name</label>
                                        <input type="text" id="driver_name" name="driver_name" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="mobile_no" class="control-label">Mobile No.</label>
                                        <input type="text" id="mobile_no" name="mobile_no" class="form-control" value="" maxlength="10" onkeypress="return isNumber(event)">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="license_no" class="control-label">License No.</label>
                                        <input type="text" id="license_no" name="license_no" class="form-control" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="lr_date" class="control-label">LR Date</label>
                                        <input type="date" id="lr_date" name="lr_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="freight_rate" class="control-label">Freight Rate</label>
                                        <input type="text" id="freight_rate" name="freight_rate" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="total_qty_freight" class="control-label">Total Qty/Freight</label>
                                        <input type="text" id="total_qty_freight" name="total_qty_freight" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="to_pay_customer" class="control-label">To Pay by Customer</label>
                                        <input type="text" id="to_pay_customer" name="to_pay_customer" class="form-control" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="payable_cash" class="control-label">Payable in Cash</label>
                                        <input type="text" id="payable_cash" name="payable_cash" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="payable_bank" class="control-label">Payable through Bank</label>
                                        <input type="text" id="payable_bank" name="payable_bank" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="payable_after_delivery" class="control-label">Payable after Delivery</label>
                                        <input type="text" id="payable_after_delivery" name="payable_after_delivery" class="form-control" value="">
                                    </div>
                                </div>
                            </div>


                            <!-- Action Buttons -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <a href="<?php echo admin_url('vehicle_transaction'); ?>" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back to List</a>
                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Create</button>
                                    <button type="button" class="btn btn-danger" onclick="clearForm()"><i class="fa fa-trash"></i> Clear</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
function clearForm() {
    if(confirm('Are you sure you want to clear the form?')) {
        document.querySelector('form').reset();
    }
}
</script>
