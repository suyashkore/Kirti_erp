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
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i
                                                class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Goods Receive Note (GRN)</b>
                                </li>
                            </ol>
                        </nav>
                        <hr class="hr_style">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                                <div class="searchh3" style="display:none;">Please wait creating new GRN...</div>
                                <div class="searchh4" style="display:none;">Please wait updating GRN...</div>
                            </div>
                        </div>

                        <!-- GRN Form Start -->
                        <form id="manage-grn-form" method="post" enctype="multipart/form-data"
                            action="<?= admin_url('purchase/Grn/AddEditGRN' . (isset($grn) && !empty($grn->grn_id) ? '/' . $grn->grn_id : '')) ?>">
                            <?php if (isset($grn) && !empty($grn->grn_id)) { ?>
                                <input type="hidden" name="grn_id" value="<?= $grn->grn_id ?>">
                            <?php } ?>

                            <!-- Top Fields -->
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><small class="req text-danger">* </small>GRN No</label>
                                        <input type="text" class="form-control" id="grn_no" name="grn_no" readonly placeholder="Auto Generated">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="grn_date">
                                        <?= render_date_input('grn_date', 'GRN Date', date('d/m/Y'), []); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><small class="req text-danger">* </small>Purchase Location</label>
                                        <select class="selectpicker form-control" id="location" name="location"
                                            data-width="100%" data-live-search="true" data-none-selected-text="None selected">
                                            <option value=""></option>
                                            <?php if (isset($purchaselocation))
                                                foreach ($purchaselocation as $location) { ?>
                                                    <option value="<?= $location['id'] ?>"><?= $location['LocationName'] ?></option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="posting_date">
                                        <?= render_date_input('posting_date', 'Posting Date', date('d/m/Y'), []); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="arrival_date">
                                        <?= render_date_input('arrival_date', 'Arrival Date', date('d/m/Y'), []); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="vendor_id">
                                        <label for="vendor_id" class="control-label"><small class="req text-danger">* </small> Vendor Name</label>
                                        <select name="vendor_id" id="vendor_id" class="form-control selectpicker"
                                            data-live-search="true" app-field-label="Vendor Name" required
                                            onchange="getVendorDetailsLocation();">
                                            <option value="" selected>None selected</option>
                                        </select>
                                        <input type="hidden" name="vendor_gst_no" id="vendor_gst_no" class="form-control" readonly>
                                        <input type="hidden" name="vendor_country" id="vendor_country" class="form-control" readonly>
                                        <input type="hidden" name="vendor_state" id="vendor_state" class="form-control" readonly>
                                        <input type="hidden" name="vendor_address" id="vendor_address" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><small class="req text-danger">* </small>Purchase Order No</label>
                                        <select class="selectpicker form-control" id="po_no" name="po_no"
                                            data-width="100%" data-live-search="true"
                                            data-none-selected-text="None selected" onchange="getPODetails(this.value)">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><small class="req text-danger">* </small>Vendor Location</label>
                                        <!--<select class="selectpicker form-control" id="vendor_location" name="vendor_location"-->
                                        <!--    data-width="100%" data-live-search="true" data-none-selected-text="None selected">-->
                                        <!--    <option value=""></option>-->
                                        <input type="text" name="vendor_location" id="vendor_location" class="form-control" readonly>

                                        <!--</select>-->
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><small class="req text-danger">* </small>Category</label>
                                        <input type="text" class="form-control" id="grn_category" name="grn_category" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" app-field-wrapper="vendor_doc_date" readonly>
                                       <?= render_date_input('vendor_doc_date', 'Vendor Doc Date', date('d/m/Y'), ['disabled' => true]); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Vendor Doc Amount</label>
                                        <input type="text" class="form-control" id="vendor_doc_amount" name="vendor_doc_amount"
                                            onkeypress="return isNumberDecimal(event)" placeholder="0.00" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Vendor Doc No</label>
                                        <input type="text" class="form-control" id="vendor_doc_no" name="vendor_doc_no" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><small class="req text-danger">* </small>Broker</label>
                                        <input type="text" class="form-control" id="broker_id" name="broker_id" readonly placeholder="Broker Name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label"><small class="req text-danger">* </small>Payment Terms</label>
                                        <input type="text" class="form-control" id="payment_terms" name="payment_terms" readonly placeholder="Payment Terms">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Vehicle No</label>
                                        <input type="text" class="form-control" id="vehicle_no" name="vehicle_no"
                                            placeholder="MH01AB1234" style="text-transform:uppercase;" readonly>
                                        <span class="vehicle_error" style="color:red;font-size:11px;"></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Vendor Dispatch Weight</label>
                                        <input type="text" class="form-control" id="vendor_dispatch_weight"
                                            name="vendor_dispatch_weight" onkeypress="return isNumberDecimal(event)" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <select class="selectpicker form-control" id="grn_status" name="grn_status" data-width="100%">
                                            <option value="Open">Open</option>
                                            <option value="Close">Close</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Sections -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div>

                                        <!-- ITEM SECTION -->
                                        <div id="tab_items">
                                            <h4 class="bold p_style">Item Details</h4>
                                            <hr class="hr_style">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="itemTable">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:60px;">S.N.</th>
                                                            <th style="min-width:200px;">Item Name</th>
                                                            <th style="min-width:140px;">PO Original Qty</th>
                                                            <th style="min-width:140px;">PO Balance Qty</th>
                                                            <th style="min-width:120px;">UOM</th>
                                                            <th style="min-width:120px;">Total Bag</th>
                                                            <th style="min-width:130px;">Receipt Qty</th>
                                                            <th style="min-width:130px;">Unit Price</th>
                                                            <th style="min-width:120px;">Receipt UOM</th>
                                                            <th style="min-width:150px;">Rebate Settlement</th>
                                                            <th style="min-width:120px;">Rate</th>
                                                            <th style="min-width:120px;">Rate UOM</th>
                                                            <th style="min-width:120px;">Calc Rate</th>
                                                            <th style="min-width:120px;">GST %</th>
                                                            <th style="min-width:130px;">Amount</th>
                                                            <th style="width:80px;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="itemTableBody">
                                                        <tr>
                                                            <td class="sn-cell text-center">1</td>
                                                            <td>
                                                                <input type="hidden" class="item_id_hidden" name="item_id[]">
                                                                <input type="text" class="form-control item_name_display"
                                                                    name="item_name_display[]" readonly placeholder="Item Name">
                                                            </td>
                                                            <td><input type="text" class="form-control po_orig_qty" name="po_orig_qty[]" readonly></td>
                                                            <td><input type="text" class="form-control po_bal_qty" name="po_bal_qty[]" readonly></td>
                                                            <td><input type="text" class="form-control item_uom" name="item_uom[]" readonly></td>
                                                            <td><input type="text" class="form-control total_bag" name="total_bag[]"
                                                                    onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
                                                            <td><input type="text" class="form-control receipt_qty" name="receipt_qty[]"
                                                                    onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
                                                            <td><input type="text" class="form-control unit_price" name="unit_price[]"
                                                                    onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
                                                            <td><input type="text" class="form-control receipt_uom" name="receipt_uom[]"></td>
                                                            <td>
                                                                <select class="selectpicker form-control rebate_settlement"
                                                                    name="rebate_settlement[]" data-width="100%">
                                                                    <option value="No">No</option>
                                                                    <option value="Yes">Yes</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" class="form-control item_rate" name="item_rate[]"
                                                                    onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
                                                            <td><input type="text" class="form-control rate_uom" name="rate_uom[]"
                                                                    onkeypress="return isNumberDecimal(event)"></td>
                                                            <td><input type="text" class="form-control calc_rate" name="calc_rate[]" readonly></td>
                                                            <td><input type="text" class="form-control gst_percent" name="gst_percent[]" readonly></td>
                                                            <td><input type="text" class="form-control item_amount" name="item_amount[]" readonly></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-success addItemRow" title="Add Row">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr style="background:#f5f5f5;">
                                                            <td colspan="14" class="text-right"><strong>Total Amount:</strong></td>
                                                            <td><input type="text" class="form-control" id="total_amount" name="total_amount"
                                                                    readonly style="font-weight:bold;background:#fff8dc;"></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- PURCHASE & DELIVERY DETAILS SECTION -->
                                        <div id="tab_purchase_delivery">
                                            <h4 class="bold p_style">Purchase & Delivery Details</h4>
                                            <hr class="hr_style">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Business Unit</label>
                                                        <select class="selectpicker form-control" id="business_unit" name="business_unit"
                                                            data-width="100%" data-live-search="true" data-none-selected-text="None selected">
                                                            <option value=""></option>
                                                            <?php if (isset($business_units))
                                                                foreach ($business_units as $bu) { ?>
                                                                    <option value="<?= $bu['id'] ?>"><?= $bu['name'] ?></option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label"><small class="req text-danger">* </small>Freight Terms</label>
                                                        <input type="text" class="form-control" id="freight_terms" name="freight_terms"
                                                            readonly placeholder="Freight Terms">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label"><small class="req text-danger">* </small>Currency</label>
                                                        <input type="text" class="form-control" id="currency" name="currency"
                                                            readonly placeholder="Currency">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Gate IN No</label>
                                                        <input type="text" class="form-control" id="gate_entry_no" name="gate_entry_no" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group" app-field-wrapper="gate_entry_date">
                                                        <?= render_date_input('gate_entry_date', 'Gate In Date', date('d/m/Y'),  ['disabled' => true]); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- FREIGHT SECTION -->
                                        <div id="tab_freight">
                                            <h4 class="bold p_style">Freight Information</h4>
                                            <hr class="hr_style">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Freight Payable To Whom</label>
                                                        <select class="selectpicker form-control" id="freight_payable_to"
                                                            name="freight_payable_to" data-width="100%" data-live-search="true"
                                                            data-none-selected-text="None selected">
                                                            <option value=""></option>
                                                            <option value="Vendor">Vendor</option>
                                                            <option value="Transporter">Transporter</option>
                                                            <option value="Self">Self</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Transporter Name</label>
                                                        <select class="selectpicker form-control" id="transporter_id"
                                                            name="transporter_id" data-width="100%" data-live-search="true"
                                                            data-none-selected-text="None selected"
                                                            onchange="getTransporterPAN(this.value)">
                                                            <option value=""></option>
                                                            <?php if (isset($transporters))
                                                                foreach ($transporters as $t) { ?>
                                                                    <option value="<?= $t['AccountID'] ?>"><?= $t['Company'] ?> - <?= $t['AccountID'] ?></option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Transporter PAN</label>
                                                        <input type="text" class="form-control" id="transporter_pan"
                                                            name="transporter_pan" readonly style="text-transform:uppercase;">
                                                    </div>
                                                </div>

                                                <!-- VEHICLE OWNER -->
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Vehicle Own
                                                            <small id="back_to_list_link" style="display:none; color:#337ab7; cursor:pointer; font-weight:normal; margin-left:6px;"
                                                                onclick="cancelManualVehicleOwner()">
                                                                <i class="fa fa-arrow-left"></i> Back to list
                                                            </small>
                                                        </label>
                                                        <div id="vehicle_owner_dropdown_wrap">
                                                            <select class="selectpicker form-control" id="vehicle_owner_select"
                                                                data-width="100%" data-live-search="true"
                                                                data-none-selected-text="None selected"
                                                                onchange="onVehicleOwnerSelect(this.value)">
                                                                <option value=""></option>
                                                                <option value="__manual__">-- Type manually --</option>
                                                                <?php if (isset($vehicle_owner))
                                                                    foreach ($vehicle_owner as $t) { ?>
                                                                        <option value="<?= $t['AccountID'] ?>"><?= $t['Company'] ?> - <?= $t['AccountID'] ?></option>
                                                                    <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div id="vehicle_owner_manual_wrap" style="display:none;">
                                                            <input type="text" class="form-control" id="vehicle_owner_manual"
                                                                placeholder="Type vehicle owner name..."
                                                                oninput="$('#vehicle_owner').val(this.value); $('#vehicle_pan').val('');">
                                                        </div>
                                                        <input type="hidden" id="vehicle_owner" name="vehicle_owner">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Vehicle PAN</label>
                                                        <input type="text" class="form-control" id="vehicle_pan"
                                                            name="vehicle_pan" style="text-transform:uppercase;">
                                                    </div>
                                                </div>

                                                <!-- TDS FREIGHT -->
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">TDS Freight</label>
                                                        <select class="selectpicker form-control" id="tds_freight"
                                                            name="tds_freight" data-width="100%">
                                                            <option value="No">No</option>
                                                            <option value="Yes">Yes</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- TDS CODE -->
                                                <div class="col-md-2" id="tds_code_wrap" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">TDS Code</label>
                                                        <input type="text" class="form-control" id="tds_code"
                                                            name="tds_code" readonly placeholder="TDS Code"
                                                            style="background:#fffbe6; font-weight:bold;">
                                                    </div>
                                                </div>

                                                <!-- TDS PERCENT -->
                                                <div class="col-md-2" id="tds_percent_wrap" style="display:none;">
                                                    <div class="form-group">
                                                        <label class="control-label">TDS Percent (%)</label>
                                                        <input type="text" class="form-control" id="tds_percent"
                                                            name="tds_percent" readonly placeholder="0.00"
                                                            style="background:#fffbe6; font-weight:bold;">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Total Freight</label>
                                                        <input type="text" class="form-control" id="total_freight"
                                                            name="total_freight" onkeypress="return isNumberDecimal(event)"
                                                            oninput="calcFreight()" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Freight in Cash</label>
                                                        <input type="text" class="form-control" id="freight_in_cash"
                                                            name="freight_in_cash" onkeypress="return isNumberDecimal(event)"
                                                            oninput="calcFreight()" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Freight Payable</label>
                                                        <input type="text" class="form-control" id="freight_payable"
                                                            name="freight_payable" readonly placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Freight TDS Amount</label>
                                                        <input type="text" class="form-control" id="freight_tds_amount"
                                                            name="freight_tds_amount" onkeypress="return isNumberDecimal(event)"
                                                            placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- OTHER INFORMATION SECTION -->
                                        <div id="tab_other">
                                            <h4 class="bold p_style">Other Information</h4>
                                            <hr class="hr_style">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Internal Remarks</label>
                                                        <textarea class="form-control" id="internal_remarks"
                                                            name="internal_remarks" rows="2"
                                                            placeholder="Enter internal remarks..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Document Remark</label>
                                                        <textarea class="form-control" id="document_remark"
                                                            name="document_remark" rows="2"
                                                            placeholder="Enter document remarks..."></textarea>
                                                    </div>
                                                </div>

                                                <!-- ATTACHMENT SECTION -->
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            Attachment
                                                            <a id="attachment_view_btn" href="#" target="_blank"
                                                                style="display:none; margin-left:8px;"
                                                                class="btn btn-xs btn-primary">
                                                                <i class="fa fa-eye"></i> View
                                                            </a>
                                                        </label>
                                                        <input type="file" class="form-control" id="attachment" name="attachment">
                                                        <small class="text-muted">Allowed: PDF, JPG, PNG, DOC, XLSX (Max 5MB)</small>
                                                        <div id="existing_attachment_wrap" style="display:none; margin-top:6px;">
                                                            <span class="text-muted" style="font-size:11px;">Current file: </span>
                                                            <a id="existing_attachment_link" href="#" target="_blank"
                                                                style="font-size:11px; font-weight:bold; color:#337ab7;">
                                                                <i class="fa fa-paperclip"></i>
                                                                <span id="existing_attachment_name"></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div><!-- end sections wrapper -->
                                </div>
                            </div><!-- end sections row -->

                            <!-- Action Buttons -->
                            <br>
                            <div class="row">
                                <div class="btn-bottom-toolbar text-right" style="left:-230px">
                                    <div class="col-md-12">
                                        <div class="action-buttons">
                                            <?php if (has_permission('grn', '', 'create')) { ?>
                                                <button type="button" class="btn btn-success saveBtn">
                                                    <i class="fa fa-save"></i> Save
                                                </button>
                                            <?php } ?>
                                            <?php if (has_permission('grn', '', 'edit')) { ?>
                                                <button type="button" class="btn btn-success updateBtn" style="display:none;">
                                                    <i class="fa fa-save"></i> Update
                                                </button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-warning cancelBtn">
                                                <i class="fa fa-refresh"></i> Reset
                                            </button>
                                            <button type="button" class="btn btn-info showAllBtn" title="Show All GRNs">
                                                <i class="fa fa-list"></i> Show All
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form><!-- End GRN Form -->

                        <!-- GRN List Modal -->
                        <div class="modal fade" id="GRN_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="padding:5px 10px;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title">GRN List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">
                                        <div class="table-GRN_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered" id="table_GRN_List" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="sortablePop">GRN No</th>
                                                        <th class="sortablePop">GRN Date</th>
                                                        <th class="sortablePop">Vendor Name</th>
                                                        <th class="sortablePop">PO No</th>
                                                        <th class="sortablePop">Location</th>
                                                        <th class="sortablePop">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="grnlistbody">
                                                    <tr>
                                                        <td colspan="6" class="text-center">Click "Show All" to load GRN list</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="padding:0px;">
                                        <input type="text" id="grnSearchInput" onkeyup="searchGRNTable()"
                                            placeholder="Search GRN..." title="Search" style="float:left;width:100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.modal -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$fy = $this->session->userdata('finacial_year');
$fy_new = $fy + 1;
$lastdate_date = '20' . $fy_new . '-03-31';
$curr_date = date('Y-m-d');
$curr_date_new = new DateTime($curr_date);
$last_date_yr = new DateTime($lastdate_date);
if ($last_date_yr < $curr_date_new) {
    $max_date_php = $lastdate_date;
} else {
    $max_date_php = $curr_date;
}
?>

<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        var fin_y = "<?php echo $this->session->userdata('finacial_year'); ?>";
        var year = "20" + fin_y;
        var cur_y = new Date().getFullYear().toString().substr(-2);

        var minStartDate = new Date(year, 3, 1);
        var maxEndDate;
        if (parseInt(cur_y) > parseInt(fin_y)) {
            var fy_new = parseInt(fin_y) + 1;
            var fy_new_s = "20" + fy_new;
            maxEndDate = new Date(fy_new_s + '/03/31');
        } else {
            maxEndDate = new Date();
        }

        $('#arrival_date').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });
        $('#posting_date').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });
        $('#grn_date').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });
        $('#vendor_doc_date').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });
        $('#gate_entry_date').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });
    });

    // ========================
    // DATE HELPER FUNCTIONS
    // ========================
    function toServerDate(ddmmyyyy) {
        if (!ddmmyyyy) return '';
        var parts = ddmmyyyy.split('-');
        if (parts.length === 3 && parts[2].length === 4) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return ddmmyyyy;
    }

    function toDisplayDate(yyyymmdd) {
        if (!yyyymmdd) return '';
        var parts = yyyymmdd.split('-');
        if (parts.length === 3 && parts[0].length === 4) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return yyyymmdd;
    }

    function getTodayDisplay() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        return dd + '-' + mm + '-' + yyyy;
    }

    function initDatepickers() {
        $('.datepicker-input').datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2000:2099',
            autoSize: true,
            showButtonPanel: true,
            onSelect: function (dateText) {
                $(this).val(dateText).trigger('change');
            }
        });
        $('.datepicker-input').on('blur', function () {
            var val = $(this).val().trim();
            if (!val) return;
            var pattern = /^\d{2}-\d{2}-\d{4}$/;
            if (!pattern.test(val)) {
                $(this).val('');
                alert_float('warning', 'Invalid date format. Use DD-MM-YYYY (e.g. 23-02-2026)');
            }
        });
    }

    // ========================
    // ITEM TABLE - Dynamic Row
    // ========================
    $(document).on('click', '.addItemRow', function () {
        var rowCount = $('#itemTable tbody tr').length + 1;
        var newRow = `<tr>
            <td class="sn-cell text-center">${rowCount}</td>
            <td>
                <input type="hidden" class="item_id_hidden" name="item_id[]">
                <input type="text" class="form-control item_name_display" name="item_name_display[]" readonly placeholder="Item Name">
            </td>
            <td><input type="text" class="form-control po_orig_qty" name="po_orig_qty[]" readonly></td>
            <td><input type="text" class="form-control po_bal_qty" name="po_bal_qty[]" readonly></td>
            <td><input type="text" class="form-control item_uom" name="item_uom[]" readonly></td>
            <td><input type="text" class="form-control total_bag" name="total_bag[]" onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
            <td><input type="text" class="form-control receipt_qty" name="receipt_qty[]" onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
            <td><input type="text" class="form-control unit_price" name="unit_price[]" onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
            <td><input type="text" class="form-control receipt_uom" name="receipt_uom[]"></td>
            <td>
                <select class="selectpicker form-control rebate_settlement" name="rebate_settlement[]" data-width="100%">
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </td>
            <td><input type="text" class="form-control item_rate" name="item_rate[]" onkeypress="return isNumberDecimal(event)" oninput="calcItemAmount(this)"></td>
            <td><input type="text" class="form-control rate_uom" name="rate_uom[]" onkeypress="return isNumberDecimal(event)"></td>
            <td><input type="text" class="form-control calc_rate" name="calc_rate[]" readonly></td>
            <td><input type="text" class="form-control gst_percent" name="gst_percent[]" readonly></td>
            <td><input type="text" class="form-control item_amount" name="item_amount[]" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger removeItemRow"><i class="fa fa-trash"></i></button>
            </td>
        </tr>`;
        $('#itemTable tbody').append(newRow);
        $('.selectpicker').selectpicker('refresh');
    });

    $(document).on('click', '.removeItemRow', function () {
        $(this).closest('tr').remove();
        reIndexItemTable();
        calcTotalAmount();
    });

    function reIndexItemTable() {
        $('#itemTable tbody tr').each(function (index) {
            $(this).find('.sn-cell').text(index + 1);
        });
    }

    $(document).on('change', '.item_name_select', function () {
        var selected = $(this).find('option:selected');
        var row = $(this).closest('tr');
        row.find('.item_uom').val(selected.data('uom') || '');
        row.find('.gst_percent').val(selected.data('gst') || '');
        row.find('.unit_price').val(selected.data('price') || '');
        calcItemAmount(row.find('.unit_price')[0]);
    });

    function calcItemAmount(el) {
        var row = $(el).closest('tr');
        var qty = parseFloat(row.find('.receipt_qty').val()) || 0;
        var price = parseFloat(row.find('.unit_price').val()) || 0;
        var rate = parseFloat(row.find('.item_rate').val()) || 0;
        var gst = parseFloat(row.find('.gst_percent').val()) || 0;

        var calcRate = price > 0 && rate > 0 ? (price * rate / 100).toFixed(2) : '';
        row.find('.calc_rate').val(calcRate);

        var amount = qty * price;
        var gstAmount = amount * (gst / 100);
        var totalAmt = (amount + gstAmount).toFixed(2);
        row.find('.item_amount').val(totalAmt);

        calcTotalAmount();
    }

    function calcTotalAmount() {
        var total = 0;
        $('#itemTable tbody .item_amount').each(function () {
            total += parseFloat($(this).val()) || 0;
        });
        $('#total_amount').val(total.toFixed(2));
    }

    // ========================
    // FREIGHT CALCULATION
    // ========================
    function calcFreight() {
        var total = parseFloat($('#total_freight').val()) || 0;
        var cash = parseFloat($('#freight_in_cash').val()) || 0;
        var payable = total - cash;
        $('#freight_payable').val(payable >= 0 ? payable.toFixed(2) : '0.00');
    }

    // ========================
    // TDS FREIGHT TOGGLE
    // ========================
    $('#tds_freight').on('change', function () {
        if ($(this).val() === 'Yes') {
            $('#tds_code_wrap').show();
            $('#tds_percent_wrap').show();
        } else {
            $('#tds_code_wrap').hide();
            $('#tds_percent_wrap').hide();
            $('#tds_code').val('');
            $('#tds_percent').val('');
        }
    });

    // ========================
    // LOCATION CHANGE → VENDOR LIST FETCH + FULL RESET
    // ========================
    $('#location').on('change', function () {
        var selectedLocation = $(this).val();

        // --- Vendor dropdown reset & fetch from API ---
        $('#vendor_id').html('<option value="" selected>None selected</option>');
        $('#vendor_id').selectpicker('refresh');

        if (selectedLocation) {
            $.ajax({
                url: '<?= admin_url("purchase/Grn/GetVendorByLocation") ?>',
                type: 'POST',
                data: { location_id: selectedLocation },
                dataType: 'json',
                beforeSend: function () {
                    $('#vendor_id').html('<option value="" disabled selected>Loading vendors...</option>');
                    $('#vendor_id').selectpicker('refresh');
                },
                success: function (res) {
                    var html = '<option value="" selected>None selected</option>';
                    if (res.status === 'success' && res.data && res.data.length > 0) {
                        $.each(res.data, function (i, v) {
                            html += '<option value="' + v.AccountID + '">' + v.company + ' (' + v.AccountID + ')</option>';
                        });
                    } else {
                        html = '<option value="" disabled selected>No vendors found for this location</option>';
                    }
                    $('#vendor_id').html(html);
                    $('#vendor_id').selectpicker('refresh');
                },
                error: function () {
                    $('#vendor_id').html('<option value="" disabled selected>Error loading vendors</option>');
                    $('#vendor_id').selectpicker('refresh');
                    alert_float('danger', 'Error loading vendor list. Please try again.');
                }
            });
        }

        // --- Reset all dependent fields ---
        $('#vendor_gst_no').val('');
        $('#vendor_country').val('');
        $('#vendor_state').val('');
        $('#vendor_address').val('');

        $('#vendor_location').html('<option value="" selected disabled>None selected</option>');
        $('#vendor_location').selectpicker('refresh');

        $('#po_no').html('<option value="" selected disabled>None selected</option>');
        $('#po_no').selectpicker('refresh');

        $('#grn_category').val('');
        $('#vendor_doc_no').val('');
        $('#vendor_doc_amount').val('');
        $('#broker_id').val('');
        $('#payment_terms').val('');
        $('#freight_terms').val('');
        $('#currency').val('');

        $('#itemTable tbody tr:not(:first)').remove();
        $('#itemTable tbody tr:first input').val('');
        $('#itemTable tbody tr:first select').val('No').selectpicker('refresh');
        calcTotalAmount();

        $('#total_freight').val('');
        $('#freight_in_cash').val('');
        $('#freight_payable').val('');
        $('#freight_tds_amount').val('');
        $('#freight_payable_to').val('').selectpicker('refresh');
        $('#transporter_id').val('').selectpicker('refresh');
        $('#transporter_pan').val('');

        $('#vehicle_no').val('');
        $('.vehicle_error').text('');
        $('#vendor_dispatch_weight').val('');
        $('#vehicle_owner').val('');
        $('#vehicle_pan').val('');
        $('#vehicle_owner_select').val('').selectpicker('refresh');
        $('#vehicle_owner_manual').val('');
        $('#vehicle_owner_dropdown_wrap').show();
        $('#vehicle_owner_manual_wrap').hide();
        $('#back_to_list_link').hide();

        $('#tds_freight').val('No').selectpicker('refresh');
        $('#tds_code_wrap').hide();
        $('#tds_percent_wrap').hide();
        $('#tds_code').val('');
        $('#tds_percent').val('');

        $('#gate_entry_no').val('');
        $('#internal_remarks').val('');
        $('#document_remark').val('');
        $('#business_unit').val('').selectpicker('refresh');

        $('#attachment').val('');
        $('#existing_attachment_wrap').hide();
        $('#existing_attachment_name').text('');
        $('#existing_attachment_link').attr('href', '#');
        $('#attachment_view_btn').hide().attr('href', '#');

        $('#grn_status').val('Open').selectpicker('refresh');
    });

    // =============================================
    // GET VENDOR DETAILS + TDS + SHIPPING LOCATIONS
    // =============================================
    function getVendorDetailsLocation(callback) {
        callback = callback || null;
        var vendorId = $('#vendor_id').val();

        if (!vendorId) {
            $('#vendor_location').html('<option value="" selected disabled>None selected</option>');
            if ($.fn.selectpicker !== undefined) $('#vendor_location').selectpicker('refresh');
            $('#tds_freight').val('No').selectpicker('refresh');
            $('#tds_code_wrap').hide();
            $('#tds_percent_wrap').hide();
            $('#tds_code').val('');
            $('#tds_percent').val('');
            return;
        }

        // --- Vendor Details (GST, TDS etc.) ---
        $.ajax({
            url: '<?= admin_url('purchase/GetVendorDetails'); ?>',
            type: 'POST',
            data: { AccountID: vendorId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success' && response.data) {
                    var d = response.data;
                    $('#vendor_gst_no').val(d.gst_no || '');
                    $('#vendor_country').val(d.country || '');
                    $('#vendor_state').val(d.state || '');
                    $('#vendor_address').val(d.address || '');

                    if (d.is_tds_applicable === 'Y') {
                        $('#tds_freight').val('Yes').selectpicker('refresh');
                        $('#tds_code_wrap').show();
                        $('#tds_code').val(d.TDS || '');
                        $('#tds_percent_wrap').show();
                        $('#tds_percent').val(d.tds_percent || '');
                    } else {
                        $('#tds_freight').val('No').selectpicker('refresh');
                        $('#tds_code_wrap').hide();
                        $('#tds_percent_wrap').hide();
                        $('#tds_code').val('');
                        $('#tds_percent').val('');
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error('GetVendorDetails Error:', error, xhr.responseText);
            }
        });

        // --- Vendor Shipping Locations ---
        $.ajax({
            url: '<?= admin_url('purchase/GetVendorShippingLocations'); ?>',
            type: 'POST',
            data: { AccountID: vendorId },
            dataType: 'json',
            beforeSend: function () {
                $('#vendor_location').html('<option value="" disabled selected>Loading...</option>');
                if ($.fn.selectpicker !== undefined) $('#vendor_location').selectpicker('refresh');
            },
            success: function (res) {
                var html = '<option value="" selected disabled>None selected</option>';
                if (res.status === 'success' && res.locations && res.locations.length > 0) {
                    $.each(res.locations, function (i, loc) {
                        html += '<option value="' + loc.id + '">' + loc.city + '</option>';
                    });
                } else {
                    html = '<option value="" disabled selected>No locations found</option>';
                }
                $('#vendor_location').html(html);
                if ($.fn.selectpicker !== undefined) $('#vendor_location').selectpicker('refresh');
                if (callback) callback();
            },
            error: function (xhr, status, error) {
                console.error('GetVendorShippingLocations Error:', error, xhr.responseText);
                $('#vendor_location').html('<option value="" disabled selected>Error loading locations</option>');
                if ($.fn.selectpicker !== undefined) $('#vendor_location').selectpicker('refresh');
                if (callback) callback();
            }
        });

        // --- Purchase Orders ---
        $.ajax({
            url: '<?= admin_url('purchase/Grn/getpurchaseorder'); ?>',
            type: 'POST',
            data: { AccountID: vendorId, purchaselocation: $('#location').val() },
            dataType: 'json',
            beforeSend: function () {
                $('#po_no').html('<option value="" disabled selected>Loading...</option>');
                if ($.fn.selectpicker !== undefined) $('#po_no').selectpicker('refresh');
            },
            success: function (res) {
                var html = '<option value="" selected disabled>None selected</option>';
                if (res.status === 'success' && res.locations && res.locations.length > 0) {
                    $.each(res.locations, function (i, po) {
                        html += '<option value="' + po.po_no + '">' + po.po_no + '</option>';
                    });
                } else {
                    html = '<option value="" disabled selected>No PO found for this vendor</option>';
                }
                $('#po_no').html(html);
                if ($.fn.selectpicker !== undefined) $('#po_no').selectpicker('refresh');
            },
            error: function (xhr, status, error) {
                console.error('getpurchaseorder Error:', error, xhr.responseText);
                $('#po_no').html('<option value="" disabled selected>Error loading POs</option>');
                if ($.fn.selectpicker !== undefined) $('#po_no').selectpicker('refresh');
            }
        });

        // --- Currency ---
        $.ajax({
            url: '<?= admin_url('purchase/getCurrency'); ?>',
            type: 'POST',
            data: { AccountID: vendorId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#currency').val(response.data.currency_name);
                }
            }
        });
    }

    // ========================
    // VEHICLE NUMBER VALIDATION
    // ========================
    $('#vehicle_no').on('blur', function () {
        var vehicleNo = $(this).val().trim().toUpperCase();
        $(this).val(vehicleNo);
        if (vehicleNo === '') { $('.vehicle_error').text(''); return; }
        var pattern = /^[A-Z]{2}[0-9]{2}[A-Z]{1,2}[0-9]{4}$/;
        var patternSpaced = /^[A-Z]{2}\s[0-9]{2}\s[A-Z]{1,2}\s[0-9]{4}$/;
        if (!pattern.test(vehicleNo) && !patternSpaced.test(vehicleNo.replace(/\s+/g, ' '))) {
            $('.vehicle_error').text('Invalid vehicle number format (e.g. MH01AB1234)');
        } else {
            $('.vehicle_error').text('');
        }
    });

    // ========================
    // VALIDATION
    // ========================
    function validateGRNForm() {
        var grn_date      = $('#grn_date').val();
        var location      = $('#location').val();
        var posting_date  = $('#posting_date').val();
        var arrival_date  = $('#arrival_date').val();
        var vendor_id     = $('#vendor_id').val();
        var po_no         = $('#po_no').val();
        var broker_id     = $('#broker_id').val();
        var payment_terms = $('#payment_terms').val();

        if (!grn_date)      { alert_float('warning', 'Please enter GRN Date');            $('#grn_date').focus();      return false; }
        if (!location)      { alert_float('warning', 'Please select Location');           $('#location').focus();      return false; }
        if (!posting_date)  { alert_float('warning', 'Please enter Posting Date');        $('#posting_date').focus();  return false; }
        if (!arrival_date)  { alert_float('warning', 'Please enter Arrival Date');        $('#arrival_date').focus();  return false; }
        if (!vendor_id)     { alert_float('warning', 'Please select Vendor');             $('#vendor_id').focus();     return false; }
        if (!po_no)         { alert_float('warning', 'Please select Purchase Order No');  $('#po_no').focus();         return false; }
        if (!broker_id)     { alert_float('warning', 'Please select Broker');             $('#broker_id').focus();     return false; }
        if (!payment_terms) { alert_float('warning', 'Please select Payment Terms');      $('#payment_terms').focus(); return false; }

        var hasItem = false;
        $('#itemTable tbody .item_id_hidden').each(function () {
            if ($(this).val()) { hasItem = true; return false; }
        });
        if (!hasItem) { alert_float('warning', 'Please add at least one item'); return false; }

        return true;
    }

    // ========================
    // COLLECT ITEM DATA
    // ========================
    function collectItemData() {
        var items = [];
        $('#itemTable tbody tr').each(function () {
            var itemId = $(this).find('.item_id_hidden').val();
            if (!itemId) return;
            items.push({
                item_id:           itemId,
                item_name:         $(this).find('.item_name_display').val(),
                po_orig_qty:       $(this).find('.po_orig_qty').val(),
                po_bal_qty:        $(this).find('.po_bal_qty').val(),
                item_uom:          $(this).find('.item_uom').val(),
                total_bag:         $(this).find('.total_bag').val(),
                receipt_qty:       $(this).find('.receipt_qty').val(),
                unit_price:        $(this).find('.unit_price').val(),
                receipt_uom:       $(this).find('.receipt_uom').val(),
                rebate_settlement: $(this).find('.rebate_settlement').val(),
                item_rate:         $(this).find('.item_rate').val(),
                rate_uom:          $(this).find('.rate_uom').val(),
                calc_rate:         $(this).find('.calc_rate').val(),
                gst_percent:       $(this).find('.gst_percent').val(),
                item_amount:       $(this).find('.item_amount').val()
            });
        });
        return items;
    }

    // ========================
    // BUILD FORM DATA
    // ========================
    function buildFormData() {
        var fd = new FormData();
        fd.append('grn_no',                $('#grn_no').val());
        fd.append('grn_date',              toServerDate($('#grn_date').val()));
        fd.append('posting_date',          toServerDate($('#posting_date').val()));
        fd.append('arrival_date',          toServerDate($('#arrival_date').val()));
        fd.append('vendor_doc_date',       toServerDate($('#vendor_doc_date').val()));
        fd.append('gate_entry_date',       toServerDate($('#gate_entry_date').val()));
        fd.append('location',              $('#location').val());
        fd.append('vendor_id',             $('#vendor_id').val());
        fd.append('vendor_location',       $('#vendor_location').val());
        fd.append('grn_category',          $('#grn_category').val());
        fd.append('po_no',                 $('#po_no').val());
        fd.append('vendor_doc_amount',     $('#vendor_doc_amount').val());
        fd.append('vendor_doc_no',         $('#vendor_doc_no').val());
        fd.append('broker_id',             $('#broker_id').val());
        fd.append('payment_terms',         $('#payment_terms').val());
        fd.append('vehicle_no',            $('#vehicle_no').val());
        fd.append('vendor_dispatch_weight', $('#vendor_dispatch_weight').val());
        fd.append('grn_status',            $('#grn_status').val());
        fd.append('business_unit',         $('#business_unit').val());
        fd.append('freight_terms',         $('#freight_terms').val());
        fd.append('currency',              $('#currency').val());
        fd.append('gate_entry_no',         $('#gate_entry_no').val());
        fd.append('freight_payable_to',    $('#freight_payable_to').val());
        fd.append('transporter_id',        $('#transporter_id').val());
        fd.append('transporter_pan',       $('#transporter_pan').val());
        fd.append('vehicle_owner',         $('#vehicle_owner').val());
        fd.append('vehicle_pan',           $('#vehicle_pan').val());
        fd.append('tds_freight',           $('#tds_freight').val());
        fd.append('tds_code',              $('#tds_code').val());
        fd.append('tds_percent',           $('#tds_percent').val());
        fd.append('total_freight',         $('#total_freight').val());
        fd.append('freight_in_cash',       $('#freight_in_cash').val());
        fd.append('freight_payable',       $('#freight_payable').val());
        fd.append('freight_tds_amount',    $('#freight_tds_amount').val());
        fd.append('internal_remarks',      $('#internal_remarks').val());
        fd.append('document_remark',       $('#document_remark').val());
        fd.append('total_amount',          $('#total_amount').val());
        fd.append('ItemData',              JSON.stringify(collectItemData()));

        var attachFile = document.getElementById('attachment').files[0];
        if (attachFile) fd.append('attachment', attachFile);

        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfVal  = $('input[name="' + csrfName + '"]').val() || '<?= $this->security->get_csrf_hash(); ?>';
        fd.append(csrfName, csrfVal);
        return fd;
    }

    // ========================
    // SAVE
    // ========================
    $('.saveBtn').on('click', function (e) {
        e.preventDefault();
        if (!validateGRNForm()) return;
        var fd = buildFormData();
        $.ajax({
            url: '<?= admin_url("purchase/Grn/SaveGRN") ?>',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () { $('.searchh3').show(); $('.saveBtn').attr('disabled', 'disabled'); },
            complete:   function () { $('.searchh3').hide(); $('.saveBtn').removeAttr('disabled'); },
            success: function (data) {
                if (data && data.success) {
                    alert_float('success', 'GRN created successfully!');
                    resetGRNForm();
                    setTimeout(function () { location.reload(); }, 500);
                } else {
                    alert_float('warning', (data && data.message) ? data.message : 'Something went wrong.');
                }
            },
            error: function (xhr) {
                console.error('SaveGRN error', xhr.responseText);
                alert_float('danger', 'Error saving GRN. Please try again.');
            }
        });
    });

    // ========================
    // UPDATE
    // ========================
    $('.updateBtn').on('click', function (e) {
        e.preventDefault();
        if (!validateGRNForm()) return;
        var grnId = $('#grn_no').val();
        var fd = buildFormData();
        $.ajax({
            url: '<?= admin_url("purchase/Grn/UpdateGRN/") ?>' + grnId,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function () { $('.searchh4').show(); $('.updateBtn').attr('disabled', 'disabled'); },
            complete:   function () { $('.searchh4').hide(); $('.updateBtn').removeAttr('disabled'); },
            success: function (data) {
                if (data && data.success) {
                    alert_float('success', 'GRN updated successfully!');
                    resetGRNForm();
                    setTimeout(function () { location.reload(); }, 500);
                } else {
                    alert_float('warning', (data && data.message) ? data.message : 'Something went wrong.');
                }
            },
            error: function (xhr) {
                console.error('UpdateGRN error', xhr.responseText);
                alert_float('danger', 'Error updating GRN. Please try again.');
            }
        });
    });

    // ========================
    // RESET
    // ========================
    function resetGRNForm() {
        $('#manage-grn-form')[0].reset();
        $('select.selectpicker').val('').selectpicker('refresh');
        $('#itemTable tbody tr:not(:first)').remove();
        $('#itemTable tbody tr:first input').val('');
        $('#itemTable tbody tr:first select').val('').selectpicker('refresh');
        $('#total_amount').val('');
        $('#freight_payable').val('');

        // Vendor dropdown reset to empty (no static list anymore)
        $('#vendor_id').html('<option value="" selected>None selected</option>');
        $('#vendor_id').selectpicker('refresh');

        // TDS reset
        $('#tds_code_wrap').hide();
        $('#tds_percent_wrap').hide();
        $('#tds_code').val('');
        $('#tds_percent').val('');

        // Attachment reset
        $('#existing_attachment_wrap').hide();
        $('#existing_attachment_name').text('');
        $('#existing_attachment_link').attr('href', '#');
        $('#attachment_view_btn').hide().attr('href', '#');

        $('.saveBtn').show().removeAttr('disabled');
        $('.updateBtn').hide().removeAttr('disabled');
        $('.vehicle_error').text('');
        $('#grn_no').val('');

        // Vehicle Owner reset
        $('#vehicle_owner_dropdown_wrap').show();
        $('#vehicle_owner_select').val('').selectpicker('refresh');
        $('#vehicle_owner_manual_wrap').hide();
        $('#back_to_list_link').hide();
        $('#vehicle_owner_manual').val('');
        $('#vehicle_owner').val('');
        $('#vehicle_pan').val('');

        var todayDisplay = getTodayDisplay();
        $('#grn_date').val(todayDisplay).datepicker('setDate', new Date());
        $('#posting_date').val(todayDisplay).datepicker('setDate', new Date());
        $('#arrival_date').val(todayDisplay).datepicker('setDate', new Date());
        $('#vendor_doc_date').val(todayDisplay).datepicker('setDate', new Date());

        // GRN No re-fetch after reset
        $.ajax({
            url: '<?= admin_url("purchase/Grn/GetNextGRNNo") ?>',
            method: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res && res.grn_no) $('#grn_no').val(res.grn_no);
            }
        });
    }

    $('.cancelBtn').on('click', function () {
        resetGRNForm();
    });

    // ========================
    // SHOW ALL GRNs
    // ========================
    $('.showAllBtn').on('click', function () {
        $('#GRN_List').modal('show');
        $.ajax({
            url: '<?= admin_url("purchase/Grn/GetAllGRNList") ?>',
            method: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('.searchh2').show();
                $('#grnlistbody').html('<tr><td colspan="6" class="text-center">Loading...</td></tr>');
            },
            complete: function () { $('.searchh2').hide(); },
            success: function (data) {
                $('#grnlistbody').html(data);
                attachGRNRowClicks();
            },
            error: function () {
                $('#grnlistbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading GRN list.</td></tr>');
            }
        });
        $('#GRN_List').on('shown.bs.modal', function () {
            $('#grnSearchInput').val('').focus();
        });
    });

    function attachGRNRowClicks() {
        $('#table_GRN_List').off('click', '.get_GRN_ID').on('click', '.get_GRN_ID', function () {
            var grnId = $(this).data('id');
            $.ajax({
                url: '<?= admin_url("purchase/Grn/GetGRNDetailByID") ?>',
                method: 'POST',
                data: { grn_id: grnId },
                dataType: 'json',
                beforeSend: function () { $('.searchh2').show(); $('.saveBtn').hide(); $('.updateBtn').show(); },
                complete:   function () { $('.searchh2').hide(); },
                success: function (response) {
                    if (response && response.data) {
                        populateGRNData(response.data);
                        $('#GRN_List').modal('hide');
                    }
                },
                error: function () { alert_float('danger', 'Error loading GRN details.'); }
            });
        });
    }

    // ========================
    // POPULATE GRN DATA
    // ========================
    function populateGRNData(data) {
        try {
            var grn = data.grnDetails || data;
            if (Array.isArray(grn)) grn = grn[0];

            // Basic fields
            $('#grn_no').val(grn.grn_no || '');
            $('#grn_date').val(toDisplayDate(grn.grn_date || ''));
            $('#posting_date').val(toDisplayDate(grn.posting_date || ''));
            $('#arrival_date').val(toDisplayDate(grn.arrival_date || ''));
            $('#vendor_doc_date').val(toDisplayDate(grn.vendor_doc_date || ''));
            $('#gate_entry_date').val(toDisplayDate(grn.gate_entry_date || ''));
            $('#vendor_doc_amount').val(grn.vendor_doc_amount || '');
            $('#vendor_doc_no').val(grn.vendor_doc_no || '');
            $('#vehicle_no').val(grn.vehicle_no || '');
            $('#vendor_dispatch_weight').val(grn.vendor_dispatch_weight || '');
            $('#gate_entry_no').val(grn.gate_entry_no || '');
            $('#grn_category').val(grn.grn_category || '');
            $('#total_amount').val(grn.total_amount || '');
            $('#internal_remarks').val(grn.internal_remarks || '');
            $('#document_remark').val(grn.document_remark || '');
            $('#broker_id').val(grn.broker_id || '');
            $('#payment_terms').val(grn.payment_terms || '');
            $('#freight_terms').val(grn.freight_terms || '');
            $('#currency').val(grn.currency || '');
            $('#total_freight').val(grn.total_freight || '');
            $('#freight_in_cash').val(grn.freight_in_cash || '');
            $('#freight_payable').val(grn.freight_payable || '');
            $('#freight_tds_amount').val(grn.freight_tds_amount || '');

            // Location select
            if (grn.location) {
                $('#location').val(grn.location).selectpicker('refresh');

                // Load vendor list for this location, then set vendor
                $.ajax({
                    url: '<?= admin_url("purchase/Grn/GetVendorByLocation") ?>',
                    type: 'POST',
                    data: { location_id: grn.location },
                    dataType: 'json',
                    success: function (res) {
                        var html = '<option value="" selected>None selected</option>';
                        if (res.status === 'success' && res.data && res.data.length > 0) {
                            $.each(res.data, function (i, v) {
                                html += '<option value="' + v.AccountID + '">' + v.company + ' (' + v.AccountID + ')</option>';
                            });
                        }
                        $('#vendor_id').html(html);
                        if (grn.vendor_id) {
                            $('#vendor_id').val(grn.vendor_id).selectpicker('refresh');
                        } else {
                            $('#vendor_id').selectpicker('refresh');
                        }

                        // Load vendor shipping locations
                        if (grn.vendor_id) {
                            $.ajax({
                                url: '<?= admin_url('purchase/GetVendorShippingLocations'); ?>',
                                type: 'POST',
                                data: { AccountID: grn.vendor_id },
                                dataType: 'json',
                                success: function (res2) {
                                    var html2 = '<option value="" selected disabled>None selected</option>';
                                    if (res2.status === 'success' && res2.locations && res2.locations.length > 0) {
                                        $.each(res2.locations, function (i, loc) {
                                            html2 += '<option value="' + loc.id + '">' + loc.city + '</option>';
                                        });
                                    }
                                    $('#vendor_location').html(html2);
                                    if (grn.vendor_location) {
                                        $('#vendor_location').val(grn.vendor_location).selectpicker('refresh');
                                    } else {
                                        $('#vendor_location').selectpicker('refresh');
                                    }
                                }
                            });

                            // Load PO list for vendor + location
                            $.ajax({
                                url: '<?= admin_url('purchase/Grn/getpurchaseorder'); ?>',
                                type: 'POST',
                                data: { AccountID: grn.vendor_id, purchaselocation: grn.location },
                                dataType: 'json',
                                success: function (res3) {
                                    var html3 = '<option value="" selected disabled>None selected</option>';
                                    if (res3.status === 'success' && res3.locations && res3.locations.length > 0) {
                                        $.each(res3.locations, function (i, po) {
                                            html3 += '<option value="' + po.po_no + '">' + po.po_no + '</option>';
                                        });
                                    }
                                    $('#po_no').html(html3);
                                    if (grn.po_no) {
                                        $('#po_no').val(grn.po_no).selectpicker('refresh');
                                    } else {
                                        $('#po_no').selectpicker('refresh');
                                    }
                                }
                            });
                        }
                    }
                });
            }

            // GRN Status
            if (grn.grn_status) $('#grn_status').val(grn.grn_status).selectpicker('refresh');

            // Simple selects
            var simpleSelects = ['business_unit', 'freight_payable_to', 'transporter_id'];
            simpleSelects.forEach(function (f) {
                if (grn[f] !== undefined && grn[f] !== null && grn[f] !== '') {
                    $('#' + f).val(grn[f]).selectpicker('refresh');
                }
            });

            // Transporter PAN
            if (grn.transporter_id) {
                $.ajax({
                    url: admin_url + 'purchase/Grn/transport/' + grn.transporter_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        $('#transporter_pan').val(res && res.pan ? res.pan.toUpperCase() : '');
                    },
                    error: function () { $('#transporter_pan').val(''); }
                });
            } else {
                $('#transporter_pan').val('');
            }

            // Vehicle PAN
            if (grn.vehicle_owner) {
                $.ajax({
                    url: admin_url + 'purchase/Grn/transport/' + grn.vehicle_owner,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        $('#vehicle_pan').val(res && res.pan ? res.pan.toUpperCase() : '');
                    },
                    error: function () { $('#vehicle_pan').val(''); }
                });
            } else {
                $('#vehicle_pan').val('');
            }

            // TDS fields
            var tdsVal = (grn.tds_freight || '').trim();
            if (tdsVal === 'Yes' || tdsVal === 'Ye') {
                $('#tds_freight').val('Yes').selectpicker('refresh');
                $('#tds_code_wrap').show();
                $('#tds_percent_wrap').show();
                if (grn.tds_code && grn.tds_code.trim() !== '') {
                    $('#tds_code').val(grn.tds_code);
                    $('#tds_percent').val(grn.tds_percent || '');
                } else if (grn.vendor_id) {
                    $.ajax({
                        url: '<?= admin_url('purchase/GetVendorDetails'); ?>',
                        type: 'POST',
                        data: { AccountID: grn.vendor_id },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status === 'success' && response.data) {
                                var d = response.data;
                                $('#tds_code').val(d.TDS || d.tds_code || '');
                                $('#tds_percent').val(d.tds_percent || '');
                            }
                        }
                    });
                }
            } else {
                $('#tds_freight').val('No').selectpicker('refresh');
                $('#tds_code_wrap').hide();
                $('#tds_percent_wrap').hide();
                $('#tds_code').val('');
                $('#tds_percent').val('');
            }

            // Vehicle Owner
            var voVal = grn.vehicle_owner || '';
            $('#vehicle_owner').val(voVal);
            if (voVal) {
                var $opt = $('#vehicle_owner_select option[value="' + voVal + '"]');
                if ($opt.length) {
                    $('#vehicle_owner_dropdown_wrap').show();
                    $('#vehicle_owner_select').val(voVal).selectpicker('refresh');
                    $('#vehicle_owner_manual_wrap').hide();
                    $('#vehicle_owner_manual').val('');
                } else {
                    $('#vehicle_owner_dropdown_wrap').hide();
                    $('#vehicle_owner_manual_wrap').show();
                    $('#back_to_list_link').show();
                    $('#vehicle_owner_manual').val(voVal);
                }
            } else {
                $('#vehicle_owner_dropdown_wrap').show();
                $('#vehicle_owner_select').val('').selectpicker('refresh');
                $('#vehicle_owner_manual_wrap').hide();
                $('#vehicle_owner_manual').val('');
            }

            // Attachment
            var attachFile = grn.attachment || grn.Attachment || '';
            if (attachFile) {
                var attachUrl = '<?= base_url('uploads/grn/') ?>' + attachFile;
                $('#attachment_view_btn').attr('href', attachUrl).show();
                $('#existing_attachment_wrap').show();
                $('#existing_attachment_name').text(attachFile);
                $('#existing_attachment_link').attr('href', attachUrl);
            } else {
                $('#attachment_view_btn').hide().attr('href', '#');
                $('#existing_attachment_wrap').hide();
                $('#existing_attachment_link').attr('href', '#');
                $('#existing_attachment_name').text('');
            }

            // Item rows
            if (data.itemData && data.itemData.length > 0) {
                $('#itemTable tbody tr:not(:first)').remove();
                data.itemData.forEach(function (item, idx) {
                    if (idx === 0) {
                        fillItemRow($('#itemTable tbody tr:first'), item);
                    } else {
                        $('.addItemRow').trigger('click');
                        fillItemRow($('#itemTable tbody tr:last'), item);
                    }
                });
            }

            $('.saveBtn').hide();
            $('.updateBtn').show();
            alert_float('success', 'GRN details loaded successfully!');
            $('html, body').animate({ scrollTop: $('#manage-grn-form').offset().top - 100 }, 500);

        } catch (e) {
            console.error('PopulateGRNData error:', e);
            alert_float('danger', 'Error loading GRN data: ' + e.message);
        }
    }

    // ========================
    // GRN LIST SEARCH
    // ========================
    function searchGRNTable() {
        var input  = document.getElementById('grnSearchInput');
        var filter = input.value.toUpperCase();
        var table  = document.getElementById('table_GRN_List');
        var tr     = table.getElementsByTagName('tr');
        for (var i = 1; i < tr.length; i++) {
            var tds   = tr[i].getElementsByTagName('td');
            var found = false;
            for (var j = 0; j < tds.length; j++) {
                if (tds[j] && tds[j].textContent.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            tr[i].style.display = found ? '' : 'none';
        }
    }

    // ========================
    // TABLE SORT
    // ========================
    $(document).on('click', '.sortablePop', function () {
        var table     = $('#table_GRN_List tbody');
        var rows      = table.find('tr').toArray();
        var index     = $(this).index();
        var ascending = !$(this).hasClass('asc');
        $('.sortablePop').removeClass('asc desc').find('span').remove();
        $(this).addClass(ascending ? 'asc' : 'desc')
               .append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
        rows.sort(function (a, b) {
            var vA = $(a).find('td').eq(index).text().trim();
            var vB = $(b).find('td').eq(index).text().trim();
            if ($.isNumeric(vA) && $.isNumeric(vB)) return ascending ? vA - vB : vB - vA;
            return ascending ? vA.localeCompare(vB) : vB.localeCompare(vA);
        });
        table.append(rows);
    });

    // ========================
    // UTILITY
    // ========================
    function isNumberDecimal(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode === 46) return true;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
        return true;
    }

    // ========================
    // DOCUMENT READY
    // ========================
    $(document).ready(function () {
        initDatepickers();

        var todayDisplay = getTodayDisplay();
        if (!$('#grn_date').val())        $('#grn_date').val(todayDisplay).datepicker('setDate', new Date());
        if (!$('#posting_date').val())    $('#posting_date').val(todayDisplay).datepicker('setDate', new Date());
        if (!$('#arrival_date').val())    $('#arrival_date').val(todayDisplay).datepicker('setDate', new Date());
        if (!$('#vendor_doc_date').val()) $('#vendor_doc_date').val(todayDisplay).datepicker('setDate', new Date());
        if (!$('#gate_entry_date').val()) $('#gate_entry_date').val(todayDisplay).datepicker('setDate', new Date());

        $.ajax({
            url: '<?= admin_url("purchase/Grn/GetNextGRNNo") ?>',
            method: 'POST',
            dataType: 'json',
            success: function (res) {
                if (res && res.grn_no) $('#grn_no').val(res.grn_no);
            }
        });
    });

    // ========================
    // PO SELECT → GET PO DETAILS
    // ========================
    function getPODetails(poId) {
        if (!poId) {
            $('#itemTable tbody tr:not(:first)').remove();
            var firstRow = $('#itemTable tbody tr:first');
            firstRow.find('input').val('');
            firstRow.find('.rebate_settlement').val('No').selectpicker('refresh');
            calcTotalAmount();
            $('#grn_category').val('');
            return;
        }

        $.ajax({
            url: '<?= admin_url("purchase/Grn/GetPODetailsByID") ?>',
            type: 'POST',
            data: { po_id: poId },
            dataType: 'json',
            beforeSend: function () { $('.searchh2').show(); },
            complete:   function () { $('.searchh2').hide(); },
            success: function (res) {
                if (res.status !== 'success' || !res.data) {
                    alert_float('warning', 'No PO details found.');
                    return;
                }

                var poHeader = res.data.header;
                var poItems  = res.data.items;

                if (Array.isArray(poHeader)) poHeader = poHeader[0];

                if (poHeader) {
                    if (poHeader.VendorDocNo      !== undefined) $('#vendor_doc_no').val(poHeader.VendorDocNo);
                    if (poHeader.VendorDocAmt      !== undefined) $('#vendor_doc_amount').val(poHeader.VendorDocAmt);
                    if (poHeader.VendorDocWeight      !== undefined) $('#vendor_dispatch_weight').val(poHeader.VendorDocWeight);
                    if (poHeader.BrokerID         !== undefined) $('#broker_id').val(poHeader.Company);
                    if (poHeader.CategoryName         !== undefined) $('#grn_category').val(poHeader.CategoryName);
                    if (poHeader.VehicleNo         !== undefined) $('#vehicle_no').val(poHeader.VehicleNo);
                    if (poHeader.PaymentTerms     !== undefined) $('#payment_terms').val(poHeader.PaymentTerms);
                    if (poHeader.city_name     !== undefined) $('#vendor_location').val(poHeader.city_name);
                    if (poHeader.FreightTerms     !== undefined) $('#freight_terms').val(poHeader.freight_terms);
                    if (poHeader.Internal_Remarks !== undefined) $('#internal_remarks').val(poHeader.Internal_Remarks);
                    if (poHeader.Document_Remark  !== undefined) $('#document_remark').val(poHeader.Document_Remark);
                    if (poHeader.GateINID  !== undefined) $('#gate_entry_no').val(poHeader.GateINID);
                    if (poHeader.VendorDocDate) {
                        var vdd = poHeader.VendorDocDate.split(' ')[0];
                        $('#gate_entry_date').val(toDisplayDate(vdd));
                    }
                     if (poHeader.GateInDate) {
                        var vdd = poHeader.GateInDate.split(' ')[0];
                        $('#gate_entry_date').val(toDisplayDate(vdd));
                    }
                }

                if (poItems && poItems.length > 0) {
                    $('#itemTable tbody tr:not(:first)').remove();
                    poItems.forEach(function (item, idx) {
                        if (idx === 0) {
                            fillItemRow($('#itemTable tbody tr:first'), item);
                        } else {
                            $('.addItemRow').trigger('click');
                            fillItemRow($('#itemTable tbody tr:last'), item);
                        }
                    });
                    calcTotalAmount();
                }
            },
            error: function (xhr, status, error) {
                console.error('GetPODetailsByID Error:', error, xhr.responseText);
                alert_float('danger', 'Error loading PO details.');
            }
        });
    }

    function fillItemRow(row, item) {
        row.find('.item_id_hidden').val(item.ItemID || item.item_id || '');
        row.find('.item_name_display').val(item.ItemName || item.item_name || '');
        row.find('.po_orig_qty').val(item.OrderQty || item.po_orig_qty || '');
        row.find('.po_bal_qty').val(item.eOrderQty || item.po_bal_qty || '');
        row.find('.item_uom').val(item.SuppliedIn || item.receipt_uom || '');
        row.find('.total_bag').val(item.CaseQty || item.total_bag || '');
        var receiptQty = parseFloat(item.BilledQty || item.receipt_qty || 0);
        row.find('.receipt_qty').val(receiptQty > 0 ? receiptQty : '');
        row.find('.unit_price').val(item.BasicRate || item.unit_price || '');
        row.find('.receipt_uom').val(item.WeightUnit || item.receipt_uom || '');
        row.find('.rebate_settlement').val(item.rebate_settlement || 'No').selectpicker('refresh');
        row.find('.item_rate').val(item.SaleRate || item.item_rate || '');
        row.find('.rate_uom').val(item.WeightUnit || item.rate_uom || '');
        var basicRate = parseFloat(item.BasicRate || 0);
        var saleRate  = parseFloat(item.SaleRate  || 0);
        var calcRate  = (basicRate > 0 && saleRate > 0) ? (basicRate * saleRate / 100).toFixed(2) : '';
        row.find('.calc_rate').val(calcRate);
        var gstTotal = (parseFloat(item.cgst || 0) + parseFloat(item.sgst || 0)).toFixed(2);
        row.find('.gst_percent').val(gstTotal > 0 ? gstTotal : (item.gst_percent || ''));
        row.find('.item_amount').val(item.OrderAmt || item.item_amount || '');
    }

    // ========================
    // TRANSPORTER PAN
    // ========================
    function getTransporterPAN(transporter_id) {
        if (!transporter_id) { $('#transporter_pan').val(''); return; }
        $.ajax({
            url: admin_url + 'purchase/Grn/transport/' + transporter_id,
            type: 'GET',
            dataType: 'json',
            beforeSend: function () { $('#transporter_pan').val('Loading...'); },
            success: function (response) {
                $('#transporter_pan').val(response && response.pan ? response.pan.toUpperCase() : '');
            },
            error: function () {
                $('#transporter_pan').val('');
                alert('Failed to fetch Transporter PAN.');
            }
        });
    }

    // ========================
    // VEHICLE OWNER - Dropdown ↔ Manual Toggle
    // ========================
    function onVehicleOwnerSelect(val) {
        if (val === '__manual__') {
            $('#vehicle_owner_dropdown_wrap').hide();
            $('#vehicle_owner_manual_wrap').show();
            $('#back_to_list_link').show();
            $('#vehicle_owner_manual').val('').focus();
            $('#vehicle_owner').val('');
            $('#vehicle_pan').val('');
            $('#vehicle_owner_select').val('').selectpicker('refresh');
        } else if (val) {
            $('#vehicle_owner').val(val);
            $('#vehicle_owner_manual_wrap').hide();
            $('#vehicle_owner_dropdown_wrap').show();
            $.ajax({
                url: admin_url + 'purchase/Grn/transport/' + val,
                type: 'GET',
                dataType: 'json',
                beforeSend: function () { $('#vehicle_pan').val('Loading...'); },
                success: function (response) {
                    $('#vehicle_pan').val(response && response.pan ? response.pan.toUpperCase() : '');
                },
                error: function () { $('#vehicle_pan').val(''); }
            });
        } else {
            $('#vehicle_owner').val('');
            $('#vehicle_pan').val('');
        }
    }

    function cancelManualVehicleOwner() {
        $('#vehicle_owner_manual_wrap').hide();
        $('#back_to_list_link').hide();
        $('#vehicle_owner_dropdown_wrap').show();
        $('#vehicle_owner_manual').val('');
        $('#vehicle_owner').val('');
        $('#vehicle_pan').val('');
        $('#vehicle_owner_select').val('').selectpicker('refresh');
    }

    $(document).on('input', '#vehicle_owner_manual', function () {
        $('#vehicle_owner').val($(this).val());
    });

</script>

<style>
    /* === GRN FORM STYLES === */
    #grn_no    { text-transform: uppercase; font-weight: bold; }
    #vehicle_no { text-transform: uppercase; }

    .datepicker-input { background-color: #fff; cursor: pointer; }

    .ui-datepicker          { font-size: 12px; z-index: 9999 !important; }
    .ui-datepicker-header   { background: #50607b; color: #fff; border: none; }
    .ui-datepicker th       { color: #50607b; }
    .ui-datepicker td .ui-state-active { background: #50607b; }

    .table-GRN_List          { overflow: auto; max-height: 65vh; width: 100%; position: relative; }
    .table-GRN_List thead th { position: sticky; top: 0; z-index: 1; }

    table    { border-collapse: collapse; width: 100%; }
    th, td   { padding: 1px 5px !important; white-space: nowrap; border: 1px solid !important; font-size: 11px; line-height: 1.42857143 !important; vertical-align: middle !important; }
    th       { background: #50607b; color: #fff !important; }

    #table_GRN_List td:hover  { cursor: pointer; }
    #table_GRN_List tr:hover  { background-color: #ccc; }

    #itemTable       { min-width: 1400px; }
    #itemTable tfoot td { background: #f5f5f5; }

    #total_amount    { background: #fff8dc !important; font-weight: bold; }
    #freight_payable { background: #f0fff0 !important; font-weight: bold; }
    #tds_percent     { background: #fffbe6 !important; font-weight: bold; }

    #attachment_view_btn {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 3px;
        vertical-align: middle;
        text-decoration: none;
    }
    #attachment_view_btn:hover { opacity: 0.85; }

    .action-buttons          { display: flex; gap: 8px; justify-content: flex-end; }
    .action-buttons .btn     { min-width: 120px; }

    @media (max-width: 767px) {
        .action-buttons              { flex-direction: column-reverse; align-items: stretch; }
        .action-buttons .btn         { width: 100%; }
    }

    .form-group      { position: relative; }
    .table-responsive { overflow-x: auto; }
    .req             { font-size: 12px; }

    .p_style {
        color: #ee5770;
        margin-bottom: 5px;
        margin-top: 5px;
    }

    .hr_style {
        border-top: 2px solid #50607b;
        margin-top: 5px;
        margin-bottom: 10px;
    }

    #vehicle_owner { background-color: #fff; }
</style>