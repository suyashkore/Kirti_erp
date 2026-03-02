<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
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
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Item Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        
                        <br>
                        
                        <div class="row">
                    <div class="col-md-12">
                        <h4 class="bold">Main Information</h4>
                        <hr class="hr_style">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="item_type">
                            <label for="item_type" class="control-label">
                                <small class="req text-danger">* </small>Item Type
                            </label>
                            <select name="item_type" id="item_type" class="form-control selectpicker" required>
                                <option value="Inventory">Inventory</option>
                                <option value="Services">Services</option>
                                <option value="Assets">Assets</option>
                            </select>

                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="main_item_group">
                            <label for="main_item_group" class="control-label">
                                <small class="req text-danger">* </small>Item Main Group
                            </label>
                            <select name="item_main_group" id="item_main_group" required class="form-control selectpicker">
                                <option value=""></option>
                                <?php if (!empty($item_main_groups)) : ?>
                                    <?php foreach ($item_main_groups as $group) : ?>
                                        <option value="<?= $group->id ?>">
                                            <?= $group->name ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="item_sub_group">
                            <label for="item_sub_group" class="control-label">
                                <small class="req text-danger">* </small>Item Sub Group
                            </label>
                            <select name="item_sub_group" id="item_sub_group" required class="form-control selectpicker">
                                <option value=""></option>
                                <?php if (!empty($item_sub_groups)) : ?>
                                    <?php foreach ($item_sub_groups as $group) : ?>
                                        <option value="<?= $group['name'] ?>">
                                            <?= $group['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemCategory">
                            <label for="ItemCategory" class="control-label">
                                <small class="req text-danger">* </small>Item Category
                            </label>
                            <select name="ItemCategory" id="ItemCategory" required class="form-control selectpicker">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemCode">
                            <label for="ItemCode" class="control-label">Item Code</label>
                            <input type="text" id="ItemCode" required name="ItemCode" class="form-control" value="dummy01" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemName">
                            <label for="ItemName" class="control-label">Item Name</label>
                            <input type="text" id="ItemName" required name="ItemName" class="form-control" value="">
                        </div>
                    </div>

                    <div class="col-md-12">

                        <div class="alert alert-warning affect-warning hide">

                            <?= _l('changing_items_affect_warning'); ?>

                        </div>

                        <?= render_input('description', 'invoice_item_add_edit_description'); ?>
                        <?= render_textarea('long_description', 'invoice_item_long_description'); ?>

                    </div>
                    <div class="col-md-12">
                        <h4 class="bold">General Information</h4>
                        <hr class="hr_style">
                    </div>

                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="UOM">
                            <label for="UOM" class="control-label">
                                <small class="req text-danger">* </small>UOM
                            </label>
                            <select name="UOM"
                                id="UOM"
                                required
                                class="form-control selectpicker"
                                data-live-search="true">

                                <option value=""></option>

                                <?php if (!empty($units)) { ?>
                                    <?php foreach ($units as $unit) { ?>
                                        <option value="<?= $unit['measured_in']; ?>">
                                            <?= $unit['measured_in']; ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>

                            </select>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="HSN">
                            <label for="HSN" class="control-label">
                                <small class="req text-danger">* </small>HSN
                            </label>
                            <select name="HSN" id="HSN" required class="form-control selectpicker">
                                <option value=""></option>
                                <?php if (!empty($hsn_data)) { ?>
                                    <?php foreach ($hsn_data as $data) { ?>
                                        <option value="<?= $data['name']; ?>">
                                            <?= $data['name']; ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="GST">
                            <label for="GST" class="control-label">
                                <small class="req text-danger">* </small>GST
                            </label>
                            <select name="GST" id="GST" required class="form-control selectpicker">
                                <option value=""></option>
                                <?php foreach ($taxes as $tax) { ?>
                                    <option value="<?php echo $tax['id']; ?>"><?php echo $tax['taxrate']; ?>%</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="Brand">
                            <label for="Brand" class="control-label">Brand</label>
                            <select name="Brand" id="Brand" class="form-control selectpicker">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="Priority">
                            <label for="Priority" class="control-label">Priority</label>
                            <select name="Priority" id="Priority" class="form-control selectpicker">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="PackingQuantity">
                            <label for="PackingQuantity" class="control-label">
                                <small class="req text-danger">* </small>Packing Quantity
                            </label>
                            <input type="text" id="PackingQuantity" required name="PackingQuantity" class="form-control" value="" onkeypress="return isNumberOnly(event)">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemWeight">
                            <label for="ItemWeight" class="control-label">
                                <small class="req text-danger">* </small>Item Weight
                            </label>
                            <input type="text" id="ItemWeight" required name="ItemWeight" class="form-control" value="" onkeypress="return isFloatOnly(event, this)">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemWeightUnit">
                            <label for="ItemWeightUnit" class="control-label">
                                <small class="req text-danger">* </small>Item Weight Unit
                            </label>
                            <select name="ItemWeightUnit" id="ItemWeightUnit" required class="form-control selectpicker">
                                <option value=""></option>
                                <?php if (!empty($units)) { ?>
                                    <?php foreach ($units as $unit) { ?>
                                        <option value="<?= $unit['measured_in']; ?>">
                                            <?= $unit['measured_in']; ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="ItemWeightPackaging">
                            <label for="ItemWeightPackaging" class="control-label">Item Weight Packaging</label>
                            <input type="text" id="ItemWeightPackaging" name="ItemWeightPackaging" class="form-control" value="" onkeypress="return isFloatOnly(event, this)">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="UpperTolerence">
                            <label for="UpperTolerence" class="control-label">Upper Tolerence</label>
                            <input type="text" id="UpperTolerence" name="UpperTolerence" class="form-control" value="" onkeypress="return isFloatOnly(event, this)">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="DownTolerence">
                            <label for="DownTolerence" class="control-label">Down Tolerence</label>
                            <input type="text" id="DownTolerence" name="DownTolerence" class="form-control" value="" onkeypress="return isFloatOnly(event, this)">

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="UnloadingRate">
                            <label for="UnloadingRate" class="control-label">Unloading Rate</label>
                            <input type="text" id="UnloadingRate" name="UnloadingRate" class="form-control" value="" onkeypress="return isFloatOnly(event, this)">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="IsBagApplicable">
                            <label for="IsBagApplicable" class="control-label">Is Bag Applicable</label>
                            <select name="IsBagApplicable" id="IsBagApplicable" class="form-control selectpicker">
                                <option value="Yes" selected>Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Blocked <span style="color: red;">*</span></label>
                            <div style="margin-top: 8px;">
                                <label class="switch">
                                    <input type="checkbox" name="isactive" id="isactive_chk">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>







                    <div class="col-md-12">
                        <h4 class="bold">Quality Information</h4>
                        <hr class="hr_style">
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="QualityManaged">
                            <label for="QualityManaged" class="control-label">Quality Managed ?</label>
                            <div class="radio-lg">
                                <label class="radio-inline">
                                    <input type="radio" name="QualityManaged" value="Yes"> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="QualityManaged" value="No"> No
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="BatchManaged">
                            <label for="BatchManaged" class="control-label">Batch Managed ?</label>
                            <div class="radio-lg">
                                <label class="radio-inline">
                                    <input type="radio" name="BatchManaged" value="Yes"> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="BatchManaged" value="No"> No
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="BatchManaged1">
                            <label for="BatchManaged1" class="control-label">Batch Managed</label>
                            <select name="BatchManaged1" id="BatchManaged1" class="form-control selectpicker">
                                <option value=""></option>
                                <option value="Auto">Auto</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="SelfLife">
                            <label for="SelfLife" class="control-label">Self Life (in Days)</label>
                            <input type="text" id="SelfLife" name="SelfLife" class="form-control" value="" onkeypress="return isNumberOnly(event)">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <h4 class="bold">Planing Information</h4>
                        <hr class="hr_style">
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="MaxLevel">
                            <label for="MaxLevel" class="control-label">Maximum Level</label>
                            <input type="text" id="MaxLevel" name="MaxLevel" class="form-control" value="" onkeypress="return isNumberOnly(event)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="MinLevel">
                            <label for="MinLevel" class="control-label">MinimumLevel</label>
                            <input type="text" id="MinLevel" name="MinLevel" class="form-control" value="" onkeypress="return isNumberOnly(event)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="RecorderLevel">
                            <label for="RecorderLevel" class="control-label">Reorder Level</label>
                            <input type="text" id="RecorderLevel" name="RecorderLevel" class="form-control" value="" onkeypress="return isNumberOnly(event)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="ReorderQuantity">
                            <label for="ReorderQuantity" class="control-label">Reorder Quantity</label>
                            <input type="text" id="ReorderQuantity" name="ReorderQuantity" class="form-control" value="" onkeypress="return isFloatOnly(event, this)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="MRP">
                            <label for="MRP" class="control-label">MRP</label>
                            <input type="text" id="MRP" name="MRP" class="form-control" value="" onkeypress="return isFloatOnly(event, this)">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="PreferVendor">
                            <label for="PreferVendor" class="control-label">Prefer Vendor</label>
                            <select name="PreferVendor" id="PreferVendor" required class="form-control selectpicker">
                                <option value=""></option>
                                <?php if (!empty($vendors_data)) : ?>
                                    <?php foreach ($vendors_data as $group) : ?>
                                        <option value="<?= $group['company'] ?>">
                                            <?= $group['company'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="VendorPartNo">
                            <label for="VendorPartNo" class="control-label">Vendor Part No</label>
                            <input type="text" id="VendorPartNo" name="VendorPartNo" class="form-control" value="" onkeypress="return isNumberOnly(event)">
                        </div>
                    </div>



                    <div class="col-md-12">
                        <h4 class="bold">Accounting Information</h4>
                        <hr class="hr_style">
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="ItemValuation">
                            <label for="ItemValuation" class="control-label">Item valuation</label>
                            <select name="ItemValuation" id="ItemValuation" class="form-control selectpicker">
                                <option value="Moving Average Cost" selected>Moving Average Cost</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="ItemAccounting">
                            <label for="ItemAccounting" class="control-label">Item accounting</label>
                            <select name="ItemAccounting" id="ItemAccounting" class="form-control selectpicker">
                                <option value="Item Level" selected>Item Level</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
    <div class="form-group" app-field-wrapper="AdvPaymentDiscount">
        <label class="control-label">Advance Payment Discount</label>

        <div class="row">
            <div class="col-md-6">
                <select name="AdvPaymentDiscountType"
                        id="AdvPaymentDiscountType"
                        class="form-control selectpicker">
                    <option value=""></option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="col-md-6">
                <input type="text"
                       id="AdvPaymentDiscountValue"
                       name="AdvPaymentDiscountValue"
                       class="form-control"
                       onkeypress="return isFloatOnly(event, this)">
            </div>
        </div>
    </div>
</div>

                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="GSTApplicabilty">
                            <label for="GSTApplicabilty" class="control-label">GST Applicabilty</label>
                            <select name="GSTApplicabilty" id="GSTApplicabilty" class="form-control selectpicker">
                                <option value="GST Applicable(Regular)" selected>GST Applicable(Regular)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="ClassificationCategory">
                            <label for="ClassificationCategory" class="control-label">Classification Category</label>
                            <select name="ClassificationCategory" id="ClassificationCategory" class="form-control selectpicker">
                                <option value=""></option>
                                <?php
                                foreach ($MainItemGroup as $value) {
                                ?>
                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" app-field-wrapper="HSNCode">
                            <label for="HSNCode" class="control-label">HSN Code</label>
                            <select name="HSNCode" id="HSNCode" class="form-control selectpicker">
                                <option value=""></option>
                                <?php
                                foreach ($hsn_data as $value) {
                                ?>
                                    <option value="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <h4 class="bold">GL Account Detail</h4>
                        <hr class="hr_style">
                    </div>
                    <div class="col-md-12">
                        <div class="tableFixHead2">
                            <table class="table table-striped table-bordered tableFixHead2" width="100%" id="user_list">
                                <thead>

                                    <tr>
                                        <th class="sortablePop">Sr No.</th>
                                        <th class="sortablePop">GL Account Type</th>
                                        <th class="sortablePop">GL Account</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Inventory Account</td>
                                        <td> <select name="" id="" class="form-control selectpicker">
                                                <option value="">A030500/Inventory - Soya Crud Oil</option>
                                            </select></td>

                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Consumption Account</td>
                                        <td> <select name="" id="" class="form-control selectpicker">
                                                <option value="">E01010500/Consumption - Soya Crud Oil</option>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>COGP Account</td>
                                        <td> <select name="" id="" class="form-control selectpicker">
                                                <option value="">E01010500/Consumption - Soya Crud Oil</option>
                                            </select></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="AddInfo">
                            <label for="AddInfo" class="control-label">Additional Information</label>
                            <textarea
                                id="AddInfo"
                                name="AddInfo"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="HindiItemName">
                            <label for="HindiItemName" class="control-label">Hindi Item Name</label>
                            <input type="text" id="HindiItemName" name="HindiItemName" class="form-control" value="">
                        </div>
                    </div>





                    <div class="col-md-12">
                        <h4 class="bold">Information</h4>
                        <hr class="hr_style">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="AddInfo">
                            <label for="AddInfo" class="control-label">Additional Information</label>
                            <textarea
                                id="AddInfo"
                                name="AddInfo"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="HindiItemName">
                            <label for="HindiItemName" class="control-label">Hindi Item Name</label>
                            <input type="text" id="HindiItemName" name="HindiItemName" class="form-control" value="">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" app-field-wrapper="Attachment">
                            <label for="Attachment" class="control-label">Attachment</label>
                            <input type="file" id="Attachment" name="Attachment" class="form-control">
                        </div>
                    </div>





                    <div class="col-md-12">

                        <div class="form-group">

                            <label for="rate" class="control-label">

                                <?= _l('invoice_item_add_edit_rate_currency', e($base_currency->name) . ' <small>(' . _l('base_currency_string') . ')</small>'); ?></label>

                            <input type="number" id="rate" name="rate" class="form-control" value="">

                        </div>

                        <?php

                        foreach ($currencies as $currency) {

                            if ($currency['isdefault'] == 0 && total_rows(db_prefix() . 'clients', ['default_currency' => $currency['id']]) > 0) { ?>

                                <div class="form-group">

                                    <label

                                        for="rate_currency_<?= e($currency['id']); ?>"

                                        class="control-label">

                                        <?= e(_l('invoice_item_add_edit_rate_currency', $currency['name'])); ?></label>

                                    <input type="number"

                                        id="rate_currency_<?= e($currency['id']); ?>"

                                        name="rate_currency_<?= e($currency['id']); ?>"

                                        class="form-control" value="">

                                </div>

                        <?php }
                        }

                        ?>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label class="control-label"

                                        for="tax"><?= _l('tax_1'); ?></label>

                                    <select class="selectpicker display-block" data-width="100%" name="tax"

                                        data-none-selected-text="<?= _l('no_tax'); ?>">

                                        <option value=""></option>

                                        <?php foreach ($taxes as $tax) { ?>

                                            <option

                                                value="<?= e($tax['id']); ?>"

                                                data-subtext="<?= e($tax['name']); ?>">

                                                <?= e($tax['taxrate']); ?>%

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label class="control-label"

                                        for="tax2"><?= _l('tax_2'); ?></label>

                                    <select class="selectpicker display-block" disabled data-width="100%" name="tax2"

                                        data-none-selected-text="<?= _l('no_tax'); ?>">

                                        <option value=""></option>

                                        <?php foreach ($taxes as $tax) { ?>

                                            <option

                                                value="<?= e($tax['id']); ?>"

                                                data-subtext="<?= e($tax['name']); ?>">

                                                <?= e($tax['taxrate']); ?>%

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                            </div>

                        </div>

                        <div class="clearfix mbot15"></div>

                        <?= render_input('unit', 'unit'); ?>

                        <div id="custom_fields_items">

                            <?= render_custom_fields('items'); ?>

                        </div>

                        <?= render_select('group_id', $items_groups, ['id', 'name'], 'item_group'); ?>

                        <?php hooks()->do_action('before_invoice_item_modal_form_close'); ?>

                    </div>

                </div>
                <div class="col-md-12 text-right bottom-action-bar">
    <button type="submit" class="btn btn-success">Save</button>
    <button type="button" class="btn btn-danger cancelBtn">Cancel</button>
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
    function isNumberOnly(event) {
        return event.charCode >= 48 && event.charCode <= 57;
    }

    function isFloatOnly(event, element) {
        const charCode = event.charCode;

        // Allow numbers (0–9)
        if (charCode >= 48 && charCode <= 57) {
            return true;
        }

        // Allow ONE dot (.)
        if (charCode === 46) {
            // Block if dot already exists
            if (element.value.includes('.')) {
                return false;
            }
            return true;
        }

        // Block everything else
        return false;
    }
    $(function() {
        $('#UOM').selectpicker('refresh');
    });
</script>
<style>
    .bottom-action-bar {
    position: fixed;
    bottom: 0;
    right: 0;
    width: 100%;
    background: #fff;
    padding: 10px 20px;
    z-index: 999;
    box-shadow: 0 -2px 6px rgba(0,0,0,0.1);
}
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #28a745;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 24px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>