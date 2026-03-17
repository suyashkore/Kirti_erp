<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
  .table-list {
    overflow: auto;
    max-height: 55vh;
    width: 100%;
    position: relative;
    top: 0px;
  }

  .table-list thead th {
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .table-list tbody th {
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

  .sortable {
    cursor: pointer;
  }

  .total-label-row {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .total-label-row .total-display {
    flex: 1;
    padding: 0;
    text-align: right;
    font-weight: 600;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Transactions</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Delivery Order</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="OrderID">
                    <label for="OrderID" class="control-label"><small class="req text-danger">* </small> Order ID</label>
                    <input type="text" name="OrderID" id="OrderID" class="form-control" app-field-label="Order ID" readonly>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="delivery_order_date">
                    <?= render_date_input('delivery_order_date', 'Delivery Order Date', date('d/m/Y'), []); ?>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="DispatchFrom">
                    <label for="DispatchFrom" class="control-label"><small class="req text-danger">* </small> Dispatch From</label>
                    <select name="DispatchFrom" id="DispatchFrom" class="form-control selectpicker" data-live-search="true" app-field-label="DispatchFrom" required>
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($dispatchlocation)) :
                        foreach ($dispatchlocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="item_category">
                    <label for="item_category" class="control-label"><small class="req text-danger">* </small>Sales Category</label>
                    <select name="item_category" id="item_category" class="form-control selectpicker" data-live-search="true" app-field-label="Category">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($category_list)) :
                        foreach ($category_list as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['CategoryName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="advreg">
                    <label for="advreg" class="control-label">Advance / Regualar</label>
                    <select name="advreg" id="advreg" class="form-control selectpicker" app-field-label="advreg">
                      <option value="" selected>None selected</option>
                      <option value="Y">Advance</option>
                      <option value="N">Regular</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="gate_no">
                    <label for="gate_no" class="control-label"><small class="req text-danger">* </small> Gate Entry No</label>
                    <select name="gate_no" id="gate_no" class="form-control selectpicker" data-live-search="true" app-field-label="Category" onchange="getGateEntryDetails();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($gate_in)) :
                        foreach ($gate_in as $value) :
                          echo '<option value="' . $value['GateINID'] . '">' . $value['GateINID'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>

                <div class="clearfix"></div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="customer_id">
                    <label for="customer_id" class="control-label"><small class="req text-danger">* </small> Customer Name</label>
                    <select name="customer_id" id="customer_id" class="form-control selectpicker" data-live-search="true" app-field-label="Customer Name" required onchange="getCustomerDetailsLocation();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($customer_list)) :
                        foreach ($customer_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' (' . $value['AccountID'] . ')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                    <input type="hidden" name="customer_gst_no" id="customer_gst_no" class="form-control" readonly>
                    <input type="hidden" name="customer_country" id="customer_country" class="form-control" readonly>
                    <input type="hidden" name="customer_state" id="customer_state" class="form-control" readonly>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="customer_location">
                    <label for="customer_location" class="control-label"><small class="req text-danger">* </small> Customer Location</label>
                    <select name="customer_location" id="customer_location" class="form-control selectpicker" data-live-search="true" app-field-label="Customer Location" required>
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="consignee_name">
                    <label for="consignee_name" class="control-label"><small class="req text-danger">* </small> Consignee Name</label>
                    <input type="text" name="consignee_name" id="consignee_name" class="form-control" app-field-label="Consignee Name">
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="consignee_location">
                    <label for="consignee_location" class="control-label"><small class="req text-danger">* </small> Consignee Location</label>
                    <select name="consignee_location" id="consignee_location" class="form-control selectpicker" data-live-search="true" app-field-label="Cosignee Location" required>
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>



                <div class="col-md-6 mbot5">
                  <div class="form-group" app-field-wrapper="customer_address">
                    <label for="customer_address" class="control-label"><small class="req text-danger">* </small> Customer Address</label>
                    <textarea name="customer_address" id="customer_address" class="form-control" app-field-label="Customer Address" rows="2" required>
                    </textarea>
                  </div>
                </div>


                <div class="col-md-6 mbot5">
                  <div class="form-group" app-field-wrapper="consignee_address">
                    <label for="consignee_address" class="control-label"><small class="req text-danger">* </small> Consignee Address</label>
                    <textarea name="consignee_address" id="consignee_address" class="form-control" app-field-label="Cosignee Address" rows="2" required>
                    </textarea>
                  </div>
                </div>

                <div class="col-md-12 mbot5">
                  <h4 class="bold p_style">Items / Services:</h4>
                  <hr class="hr_style">
                </div>
                <div class="col-md-12 mbot5">
                  <input type="hidden" id="row_id" value="0">
                  <table width="100%" class="table" id="items_table">
                    <thead>
                      <tr style="text-align: center;">
                        <th style="width:50px;">Sr No.</th>
                        <th>SO No.</th>
                        <th>Item Name</th>
                        <th style="width:70px;">UOM</th>
                        <th style="width:80px;">Dispatch Qty</th>
                        <th style="width:80px;">Balance Qty</th>
                        <th style="width:80px;">GST %</th>
                        <th style="width:80px;">Unit Rate</th>
                        <th style="width:80px;">Disc Amt</th>
                        <th style="width:90px;">Total Amount</th>
                        <th style="width:90px;">Total Weight</th>
                      </tr>
                    </thead>
                    <tbody id="items_body">

                    </tbody>
                  </table>
                </div>

                <div class="col-md-8">
                  <div class="row">
                    <div class="col-md-12 mbot5">
                      <h4 class="bold p_style">Transporter Details:</h4>
                      <hr class="hr_style">
                    </div>
                    <div class="col-md-3 mbot5">
                      <div class="form-group" app-field-wrapper="trans_arranged">
                        <label for="trans_arranged" class="control-label"><small class="req text-danger">* </small>Trans Arranged</label>
                        <select name="trans_arranged" id="trans_arranged"
                          class="form-control selectpicker"
                          app-field-label="Transporter Arranged"
                          required>
                          <option value="0">No</option>
                          <option value="1">Yes</option>
                        </select>
                      </div>
                    </div>
                    <div id="transporter_section">
                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="VehicleNo">
                          <label for="VehicleNo" class="control-label">
                            <small class="req text-danger">* </small>Vehicle No
                          </label>
                          <select name="VehicleNo" id="VehicleNo"
                            class="form-control selectpicker" data-live-search="true"
                            app-field-label="Vehicle No" required
                            onchange="getVehicleDetails();">
                            <option value="" selected>None selected</option>
                            <?php
                            if (!empty($vehicle_no_list)) :
                              foreach ($vehicle_no_list as $value) :
                                echo '<option value="' . $value['VehicleNo'] . '">' . $value['VehicleNo'] . '</option>';
                              endforeach;
                            endif;
                            ?>
                          </select>
                          <input type="text" name="VehicleNoManual" id="VehicleNoManual"
                            class="form-control" placeholder="Vehicle No (from Gate Entry)"
                            style="display:none;">
                        </div>
                      </div>
                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="transporter_name">
                          <label for="transporter_name" class="control-label"><small class="req text-danger">* </small>Transporter Name</label>
                          <select name="transporter_name" id="transporter_name" class="form-control selectpicker" data-live-search="true" app-field-label="Transporter Name" required>
                            <option value="" selected>None selected</option>
                            <?php
                            if (!empty($transporter_list)) :
                              foreach ($transporter_list as $value) :
                                echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' (' . $value['AccountID'] . ')</option>';
                              endforeach;
                            endif;
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="lr_no">
                          <label for="lr_no" class="control-label">LR No</label>
                          <input type="text" name="lr_no" id="lr_no" class="form-control" required>
                        </div>
                      </div>

                      <div class="col-md-3 mbot5 lr_date">
                        <div class="form-group" app-field-wrapper="lr_date">
                          <?= render_date_input('lr_date', 'LR Date', date('d/m/Y'), []); ?>
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="driver_name">
                          <label for="driver_name" class="control-label">Driver Name</label>
                          <input type="text" name="driver_name" id="driver_name" class="form-control" required>
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="mobile">
                          <label for="mobile" class="control-label">Mobile No</label>
                          <input type="text" name="mobile" id="mobile" class="form-control" required>
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="license_no">
                          <label for="license_no" class="control-label">License No</label>
                          <input type="text" name="license_no" id="license_no" class="form-control" required>
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="freight_rate">
                          <label for="freight_rate" class="control-label">Freight Rate</label>
                          <input type="text" name="freight_rate" id="freight_rate" class="form-control" data-no-required="true">
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="total_qty">
                          <label for="total_qty" class="control-label">Total Quantity</label>
                          <input type="text" name="total_qty" id="total_qty" class="form-control" readonly data-no-required="true">
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="total_freight">
                          <label for="total_freight" class="control-label">Total Freight</label>
                          <input type="text" name="total_freight" id="total_freight" class="form-control" data-no-required="true">
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="by_customer">
                          <label for="by_customer" class="control-label">To Pay By Customer</label>
                          <input type="text" name="by_customer" id="by_customer" class="form-control" data-no-required="true">
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="pay_cash">
                          <label for="pay_cash" class="control-label">Payable in Cash</label>
                          <input type="text" name="pay_cash" id="pay_cash" class="form-control" data-no-required="true">
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="pay_bank">
                          <label for="pay_bank" class="control-label">Payable Throght Bank</label>
                          <input type="text" name="pay_bank" id="pay_bank" class="form-control" data-no-required="true">
                        </div>
                      </div>

                      <div class="col-md-3 mbot5">
                        <div class="form-group" app-field-wrapper="after_delivery">
                          <label for="after_delivery" class="control-label">Payable After Delivery</label>
                          <input type="text" name="after_delivery" id="after_delivery" class="form-control" data-no-required="true">
                        </div>
                      </div>

                    </div>


                  </div>
                </div>

                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">
                      <h4 class="bold p_style">Total Summary</h4>
                      <hr class="hr_style">
                    </div>

                    <!-- Hidden inputs to store values for form submission -->
                    <input type="hidden" name="total_weight" id="total_weight_hidden" value="0">
                    <input type="hidden" name="total_qty" id="total_qty_hidden" value="0">
                    <input type="hidden" name="item_total_amt" id="item_total_amt_hidden" value="0">
                    <input type="hidden" name="total_disc_amt" id="disc_amt_hidden" value="0">
                    <input type="hidden" name="taxable_amt" id="taxable_amt_hidden" value="0">
                    <input type="hidden" name="cgst_amt" id="cgst_amt_hidden" value="0">
                    <input type="hidden" name="sgst_amt" id="sgst_amt_hidden" value="0">
                    <input type="hidden" name="igst_amt" id="igst_amt_hidden" value="0">
                    <input type="hidden" name="round_off_amt" id="round_off_amt_hidden" value="0">
                    <input type="hidden" name="net_amt" id="net_amt_hidden" value="0">

                    <!-- LEFT COLUMN -->
                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row">
                        <label>Total Weight:</label>
                        <div class="total-display" id="total_weight_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Total Qty:</label>
                        <div class="total-display" id="total_qty_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Item Total:</label>
                        <div class="total-display" id="item_total_amt_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Total Disc:</label>
                        <div class="total-display" id="disc_amt_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Taxable Amt:</label>
                        <div class="total-display" id="taxable_amt_display">0.00</div>
                      </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row">
                        <label>CGST Amt:</label>
                        <div class="total-display" id="cgst_amt_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>SGST Amt:</label>
                        <div class="total-display" id="sgst_amt_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>IGST Amt:</label>
                        <div class="total-display" id="igst_amt_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Round Off:</label>
                        <div class="total-display" id="round_off_amt_display">0.00</div>
                      </div>
                    </div>

                    <!-- FULL WIDTH - NET AMOUNT -->
                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <div class="total-label-row" style="border-top: 2px solid #007bff; padding-top: 12px; margin-top: 8px; background-color: #f0f8ff; padding: 12px; border-radius: 4px;">
                        <label style="font-size: 16px; color: #007bff; font-weight: 700; padding-right: 15px;">Net
                          Amt:</label>
                        <div class="total-display highlight" id="net_amt_display" style="font-size: 16px;">0.00</div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- </div> -->

                <!-- Invoice Lock Warning -->
                <div class="col-md-12" style="margin-top: 8px;">
                  <span id="invoice_lock_warning"
                    style="display:none; color:#c0392b; font-size:13px; font-weight:600;">
                    ⚠ This record is locked and cannot be updated because an Invoice has been created.
                  </span>
                </div>

                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('deliveryOrder', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('deliveryOrder', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
                  <button type="button" class="btn btn-warning" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
                  <button type="button" class="btn btn-info" onclick="$('#ListModal').modal('show');"><i class="fa fa-list"></i> Show List</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Customer SO Selection Modal -->
<div class="modal fade" id="CustomerSOModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding:10px;">
        <h4 class="modal-title" style="margin:0;">Select Sales Orders for Customer: <span id="modal_customer_name"></span></h4>
        <button type="button" class="close" data-dismiss="modal" style="color:black; opacity:1; margin-top:-28px;">&times;</button>
      </div>
      <div class="modal-body" style="padding:10px;">
        <h4>Valid Tab
          <table class="table table-bordered table-striped" id="valid_so_table" width="100%">
            <thead>
              <tr style="text-align:center;">
                <th>Sr. No.</th>
                <th style="width:40px;"><input type="checkbox" id="select_all_valid"></th>
                <th>SO No.</th>
                <th>SO Date</th>
                <th>SO Expiry Date</th>
                <th>Item Name</th>
                <th>Order Qty</th>
                <th>Balance Qty</th>
                <th>UOM</th>
                <th>Rate with GST</th>
                <th>Rate of UOM</th>
                <th>Sales Order Amount With GST</th>
                <th>Balance Amount With GST</th>
              </tr>
            </thead>
            <tbody id="valid_so_body">
              <tr class="get_Details">
              </tr>
            </tbody>
          </table>

          <h4>Invalid Tab
            <table class="table table-bordered table-striped" id="invalid_so_table" width="100%">
              <thead>
                <tr style="text-align:center;">
                  <th>Sr. No.</th>
                  <th style="width:40px;"><input type="checkbox" id="select_all_invalid"></th>
                  <th>SO No.</th>
                  <th>SO Date</th>
                  <th>SO Expiry Date</th>
                  <th>Item Name</th>
                  <th>Order Qty</th>
                  <th>Balance Qty</th>
                  <th>UOM</th>
                  <th>Rate with GST</th>
                  <th>Rate of UOM</th>
                  <th>Sales Order Amount With GST</th>
                </tr>
              </thead>
              <tbody id="invalid_so_body">
                <tr class="get_Details">
                </tr>
              </tbody>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="confirm_so_btn">
          <i class="fa fa-check"></i> Confirm Selection
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <i class="fa fa-times"></i> Cancel
        </button>
      </div>
    </div>
  </div>
</div>

<select id="so_list" name="so_list[]" multiple style="display:none;"></select>

<div class="modal fade" id="ListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header custom-header">
        <div class="header-top">
          <h4 class="modal-title">Delivery Order List </h4>
          <button type="button" class="close-btn"
            data-dismiss="modal">&times;</button>
        </div>
        <div class="header-filters">

          <!-- From Date -->
          <div class="filter-group">
            <label>From Date</label>
            <div class="input-group">
              <input type="text"
                id="from_date"
                name="from_date"
                class="form-control datepicker"
                value="<?= date("01/m/Y") ?>">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
            </div>
          </div>

          <!-- To Date -->
          <div class="filter-group">
            <label>To Date</label>
            <div class="input-group">
              <input type="text"
                id="to_date"
                name="to_date"
                class="form-control datepicker"
                value="<?= date("d/m/Y") ?>">
              <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </div>
            </div>
          </div>

          <!-- Search Button -->
          <div class="filter-group" style="align-self:flex-end;">
            <button type="button" class="btn btn-success" id="searchBtn">
              <i class="fa fa-list"></i> Show
            </button>
          </div>

        </div>
      </div>

      <div class="modal-body" style="padding:0px 5px !important; overflow-y:auto; max-height:65vh;">
        <div class="table-ListModal tableFixHead2" style="overflow-x:auto;">
          <table class="tree table table-striped table-bordered table-ListModal tableFixHead2" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortablePop">Order ID</th>
                <th class="sortablePop">Delivery Order Date</th>
                <th class="sortablePop">Sales Category</th>
                <th class="sortablePop">Customer</th>
                <th class="sortablePop">Total Weight</th>
                <th class="sortablePop">Total Qty</th>
                <th class="sortablePop">Item Total</th>
                <th class="sortablePop">Total Disc</th>
                <th class="sortablePop">Taxable Amt</th>
                <th class="sortablePop">CGST Amt</th>
                <th class="sortablePop">SGST Amt</th>
                <th class="sortablePop">IGST Amt</th>
                <th class="sortablePop">Round Off</th>
                <th class="sortablePop">Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($deliveryorder_list)):
                foreach ($deliveryorder_list as $key => $value):
              ?>
                  <tr class="get_Details" data-id="<?= $value["id"]; ?>" onclick="getDeliveryOrderDetails(<?= $value['id']; ?>)" data-category="<?= $value["CategoryID"]; ?>">
                    <td><?= $value["OrderID"]; ?></td>
                    <td><?= date('d/m/Y', strtotime($value["TransDate"])); ?></td>
                    <td><?= $value["CategoryName"]; ?></td>
                    <td><?= $value["company"]; ?></td>
                    <td><?= $value["TotalWt"]; ?></td>
                    <td><?= $value["TotalQty"]; ?></td>
                    <td><?= $value["ItemTotal"]; ?></td>
                    <td><?= $value["TotalDisc"]; ?></td>
                    <td><?= $value["TaxAmt"]; ?></td>
                    <td><?= $value["CGSTAmt"]; ?></td>
                    <td><?= $value["SGSTAmt"]; ?></td>
                    <td><?= $value["IGSTAmt"]; ?></td>
                    <td><?= $value["RoundOff"]; ?></td>
                    <td><?= $value["NetAmt"]; ?></td>
                  </tr>
              <?php
                endforeach;
              endif;
              ?>
            </tbody>

          </table>
        </div>
      </div>
      <div class="modal-footer" style="padding:0px;">
        <input type="text" id="myInput1" name='myInput1' onkeyup="myFunction2()" placeholder="Search for names.." style="float: left;width: 100%;">
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
  $(document).ready(function() {
    var fin_y = "<?php echo $this->session->userdata('finacial_year'); ?>";
    var year = "20" + fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);

    // Min date: April 1st of FY start year
    var minStartDate = new Date(year, 3, 1); // month index 3 = April

    // Max date: March 31 of FY end year, OR today if still within FY
    var maxEndDate;
    if (parseInt(cur_y) > parseInt(fin_y)) {
      var fy_new = parseInt(fin_y) + 1;
      var fy_new_s = "20" + fy_new;
      maxEndDate = new Date(fy_new_s + '/03/31');
    } else {
      maxEndDate = new Date();
    }

    // Order Date — restricted within FY, up to today or March 31
    $('#delivery_order_date').datetimepicker({
      format: 'd/m/Y',
      minDate: minStartDate,
      maxDate: maxEndDate,
      timepicker: false
    });

    // Delivery From — same FY restriction
    $('#lr_date').datetimepicker({
      format: 'd/m/Y',
      minDate: minStartDate,
      maxDate: maxEndDate,
      timepicker: false
    });


  });


  $(document).ready(function() {
    getNextDONo();
  });

  function getNextDONo(callback = null) {
    $.ajax({
      url: '<?= admin_url('DeliveryOrder/getNextDONo'); ?>',
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          let form_mode = $('#form_mode').val();
          if (form_mode == 'add') {
            $('#OrderID').val(response.NextDONo).prop('readonly', true);
          }
        } else {
          $('#OrderID').val('');
        }
        if (callback) callback();
      },
      error: function() {
        $('#OrderID').val('').prop('readonly', true);
      }
    });
  }

  function renumberRows() {
    $('#items_body tr').each(function(index) {
      $(this).find('input[name="sr[]"]').val(index + 1);
    });
  }

  function getItemsDetailsonSOAppend(callback) {
    let OrderIDs = $('#so_list').val();
    if (!OrderIDs || OrderIDs.length === 0) {
      if (callback) callback();
      return;
    }
    $.ajax({
      url: "<?= admin_url(); ?>DeliveryOrder/GetHistoryDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        OrderID: OrderIDs
      },
      success: function(response) {
        if (response.success === true) {
          let items = response.items;

          loadItemDropdown(function() {
            for (let i = 0; i < items.length; i++) {
              addRow(2);
              let row = parseInt($('#row_id').val());

              $('#sr' + row).val(row);
              $('#so_no' + row).val(items[i].OrderID);
              $('#item_id' + row).val(items[i].ItemID);
              $('#item_id' + row).selectpicker('refresh');
              $('#dispatch_qty' + row).val('');
              $('#balance_qty' + row).val(Number(items[i].BalanceQty).toFixed(0));
              $('#min_qty' + row).val(Number(items[i].BalanceQty).toFixed(2));
              $('#unit_rate' + row).val(Number(items[i].BasicRate).toFixed(2));
              $('#disc_amt' + row).val(Number(items[i].DiscAmt).toFixed(2));
              $('#total_weight' + row).val('0.00');
              $('#total_amt' + row).val('0.00');
              getItemDetails(items[i].ItemID, row);
            }
            $('.selectpicker').selectpicker('refresh');
            if (callback) callback();
          });
        } else {
          alert_float('warning', response.message);
          if (callback) callback();
        }
      }
    });
  }

  function getGateEntryDetails() {
    var gateId = $('#gate_no').val();
    if (!gateId) return;

    $.ajax({
      url: "<?php echo admin_url(); ?>DeliveryOrder/getGateEntryDetails",
      dataType: "JSON",
      method: "POST",
      data: {
        gate_id: gateId
      },

      beforeSend: function() {
        $('.searchh2').show().css('color', 'blue');
      },
      complete: function() {
        $('.searchh2').hide();
      },

      success: function(response) {
        if (response.status !== 'success') {
          alert_float('warning', response.message || 'Gate entry not found.');
          return;
        }

        $('#driver_name').val(response.driver_name || '');
        $('#lr_no').val(response.vehicle_no || '');
        $('#mobile').val(response.mobile || '');
        $('#license_no').val(response.license_no || '');

        if (response.vehicle_in_list) {
          $('#VehicleNoManual').hide().val('').removeAttr('required');
          $('#VehicleNo').closest('.bootstrap-select').show();
          $('#VehicleNo').attr('required', true)
            .val(response.vehicle_no)
            .selectpicker('refresh');

          if (response.transporter_id) {
            $('#transporter_name').val(response.transporter_id).selectpicker('refresh');
          }

        } else {
          $('#VehicleNo').closest('.bootstrap-select').hide();
          $('#VehicleNo').removeAttr('required').val('').selectpicker('refresh');

          $('#VehicleNoManual').show()
            .attr('required', true)
            .val(response.vehicle_no);
        }
      }
    });
  }

  function validateFreightFields() {
    var totalFreight = parseFloat($('#total_freight').val()) || 0;

    if (totalFreight <= 0) return true;

    var payCash = parseFloat($('#pay_cash').val()) || 0;
    var payBank = parseFloat($('#pay_bank').val()) || 0;
    var afterDel = parseFloat($('#after_delivery').val()) || 0;

    var totalSplit = payCash + payBank + afterDel;

    if (totalSplit > totalFreight) {
      alert_float('warning',
        'Sum of Payable in Cash (' + payCash.toFixed(2) + ') + ' +
        'Payable Through Bank (' + payBank.toFixed(2) + ') + ' +
        'Payable After Delivery (' + afterDel.toFixed(2) + ') = ' +
        totalSplit.toFixed(2) +
        ' cannot exceed Total Freight (' + totalFreight.toFixed(2) + ').'
      );
      return false;
    }

    return true;
  }

  // Trigger on each field change
  $('#pay_cash, #pay_bank, #after_delivery').on('input change', function() {
    validateFreightFields();
  });

  // Also trigger when Total Freight itself changes
  $('#total_freight').on('input change', function() {
    validateFreightFields();
  });
  $(document).ready(function() {

    // Select All VALID
    $('#select_all_valid').on('change', function() {
      $('.valid_checkbox').prop('checked', $(this).prop('checked'));
    });

    // Select All INVALID
    $('#select_all_invalid').on('change', function() {
      $('.invalid_checkbox').prop('checked', $(this).prop('checked'));
    });

  });

  function formatDate(dateString) {
    let d = new Date(dateString);
    return ("0" + d.getDate()).slice(-2) + "/" +
      ("0" + (d.getMonth() + 1)).slice(-2) + "/" +
      d.getFullYear();
  }

  $('#confirm_so_btn').on('click', function() {

    let selectedOrders = [];
    $('#valid_so_body .row_checkbox:checked, #invalid_so_body .row_checkbox:checked')
      .each(function() {
        selectedOrders.push($(this).val());
      });

    if (selectedOrders.length === 0) {
      alert('Please select at least one Sales Order.');
      return;
    }

    var alreadyPresentSoNos = [];
    $('#items_body tr').each(function() {
      var soVal = $(this).find('input[name="so_no[]"]').val();
      if (soVal) alreadyPresentSoNos.push(soVal.trim());
    });

    var historyToSoNo = {};
    $('#valid_so_body tr, #invalid_so_body tr').each(function() {
      var cb = $(this).find('.row_checkbox');
      if (cb.length) {
        var histId = cb.val().trim();
        var soNoTxt = $(this).find('td').eq(2).text().trim(); // 3rd col = SO No
        historyToSoNo[histId] = soNoTxt;
      }
    });

    var newIds = selectedOrders.filter(function(histId) {
      var soNo = historyToSoNo[histId] || histId;
      return alreadyPresentSoNos.indexOf(soNo) === -1 &&
        alreadyPresentSoNos.indexOf(histId) === -1;
    });

    var selectedSoNos = selectedOrders.map(function(histId) {
      return historyToSoNo[histId] || histId;
    });

    $('#items_body tr').each(function() {
      var soVal = $(this).find('input[name="so_no[]"]').val();
      if (!soVal) return;
      soVal = soVal.trim();
      if (selectedSoNos.indexOf(soVal) === -1 && selectedOrders.indexOf(soVal) === -1) {
        $(this).remove();
      }
    });

    $('#CustomerSOModal').modal('hide');

    if (newIds.length === 0) {
      renumberRows();
      calculateTotals();
      return;
    }

    $('#so_list').html('');
    newIds.forEach(function(id) {
      $('#so_list').append(
        `<option value="${id}" selected>${id}</option>`
      );
    });

    getItemsDetailsonSOAppend(function() {
      renumberRows();
      calculateTotals();
    });
  });
  $('#CustomerSOModal').on('shown.bs.modal', function() {
    calculateModalRates();
  });

  function calculateModalRates() {

    $('#valid_so_body tr, #invalid_so_body tr').each(function() {

      var row = $(this);

      var unitRate = parseFloat(row.data('unit-rate')) || 0;
      var discAmt = parseFloat(row.data('discount')) || 0;
      var gstPercent = parseFloat(row.data('gst')) || 0;
      var qty = parseFloat(row.data('qty')) || 0;
      var rowNetRate = unitRate - discAmt;
      var rowTaxable = rowNetRate * qty;
      var rowGst = rowTaxable * (gstPercent / 100);
      var rateWithGst = rowNetRate + (rowNetRate * gstPercent / 100);

      row.find('.rate-of-uom').text(rowNetRate.toFixed(2));
      row.find('.rate-with-gst').text(rateWithGst.toFixed(2));

    });
  }
  $(document).ready(function() {

    function toggleTransporter() {
      var value = $('#trans_arranged').val();

      if (value == "0") {
        $('#transporter_section').slideDown();
        $('#transporter_section')
          .find('input:not([role="combobox"]):not([type="search"]):not([style*="display:none"]):not([style*="display: none"]):not([data-no-required]):not([readonly]), select')
          .prop('required', true);
      } else {
        $('#transporter_section').slideUp();
        $('#transporter_section').find('input, select').prop('required', false);
      }
    }

    toggleTransporter();

    $('#trans_arranged').on('change', function() {
      toggleTransporter();
    });

  });

  function getVehicleDetails() {

    var VehicleNo = $('#VehicleNo').val();

    if (VehicleNo == '') {
      alert_float('warning', 'Please select Vehicle No');
      return;
    }

    $.ajax({
      url: "<?php echo admin_url(); ?>DeliveryOrder/getVehicleDetails",
      dataType: "JSON",
      method: "POST",
      data: {
        VehicleNo: VehicleNo
      },

      beforeSend: function() {
        $('.searchh2').show().css('color', 'blue');
      },
      complete: function() {
        $('.searchh2').hide();
      },

      success: function(response) {

        if (response.status === "success" && response.data.length > 0) {

          var vehicle = response.data[0];

          $('#transporter_name').val(vehicle.TransporterID).selectpicker('refresh');
          $('#driver_name').val(vehicle.DriverName || '');
          $('#mobile').val(vehicle.DriverMobileNo || '');
          $('#license_no').val(vehicle.LicenceNo || '');
          $('#lr_no').val(vehicle.VehicleNo || '');

          if (vehicle.GateINID) {
            var gateOption = $('#gate_no option[value="' + vehicle.GateINID + '"]');

            if (gateOption.length > 0) {
              $('#gate_no').val(vehicle.GateINID).selectpicker('refresh');
            } else {
              $('#gate_no').append(
                $('<option>', {
                  value: vehicle.GateINID,
                  text: vehicle.GateINID
                })
              );
              $('#gate_no').val(vehicle.GateINID).selectpicker('refresh');
            }
          }

        } else {
          $('#driver_name').val('');
          $('#mobile').val('');
          $('#license_no').val('');
        }
      }
    });
  }

  $('#ListModal').on('shown.bs.modal', function() {
    $('#searchBtn').trigger('click');
  });

  $('#searchBtn').on('click', function() {

    var fromDate = $('#from_date').val();
    var toDate = $('#to_date').val();

    if (!fromDate || !toDate) {
      alert_float('warning', 'Please select both From Date and To Date');
      return;
    }

    function parseDate(dateStr) {
      var parts = dateStr.split('/');
      return new Date(parts[2], parts[1] - 1, parts[0]);
    }

    var from = parseDate(fromDate);
    var to = parseDate(toDate);

    $('#table_ListModal tbody tr').each(function() {

      var rowDateText = $(this).find('td').eq(1).text().trim();
      var rowDate = parseDate(rowDateText);

      var rowCategoryId = $(this).data('category');

      var dateMatch = (rowDate >= from && rowDate <= to);

      if (dateMatch) {
        $(this).show();
      } else {
        $(this).hide();
      }

    });

  });



  function ResetForm() {
    $('#main_save_form')[0].reset();
    $('#form_mode').val('add');
    $('#update_id').val('');
    $('#quotation_id').val('').prop('disabled', false);
    $('.updateBtn').hide();
    $('.printBtn').hide();
    $('.saveBtn').show();
    $('.selectpicker').selectpicker('refresh');
    $('#items_body').html('');
    $('.total-display').text('0.00');
    $('#row_id').val(0);
    getNextDONo();

    $('#invoice_lock_warning').hide();
    $('#main_save_form input:not([type="hidden"])').prop('readonly', false);
    $('#main_save_form select').prop('disabled', false).selectpicker('refresh');
    $('#main_save_form textarea').prop('readonly', false);
  }
  $(document).on('input', 'input[type="tel"]', function() {
    this.value = this.value
      .replace(/[^0-9.]/g, '')
      .replace(/(\..*?)\..*/g, '$1');
  });

  function validate_fields(fields) {
    let data = {};
    for (let i = 0; i < fields.length; i++) {
      let value = $('#' + fields[i]).val();

      if (value === '' || value === null) {
        let label = fields[i].replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        alert_float('warning', 'Please enter ' + label);
        $('#' + fields[i]).focus();
        return false;
      } else {
        data[fields[i]] = value.trim();
      }
    }
    return data;
  }

  function addRow(row = null) {
    $('#item_id').focus();
    var row_id = $('#row_id').val();
    var next_id = parseInt(row_id) + 1;
    if (row == null) {
      let fields = ['item_id', 'min_qty', 'max_qty', 'disc_amt', 'unit_rate'];
      let data = validate_fields(fields);
      if (data === false) {
        return false;
      }

      var row_btn = `<button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove();" style="float:right; padding: 2px; width: 30px;"><i class="fa fa-xmark"></i></button>`;
    } else {
      var row_btn = '';
    }
    let item_option = $('#item_id').html();
    $('#items_body').append(`
      <tr>
        <td><input type="text" name="sr[]" id="sr${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1" style="width:50px;"></td>
        <td><input type="text" name="so_no[]" id="so_no${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>

        
        <td style="min-width:200px;">
          <input type="hidden" name="item_uid[]" id="item_uid${next_id}" value="0">
          <input type="hidden" name="item_id[]" id="item_id${next_id}">
          <input type="text" name="item_name[]" id="item_name${next_id}" class="form-control" readonly tabindex="-1" placeholder="Item Name">
        </td>

        <td><input type="text" name="uom[]" id="uom${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1" style="width:70px;"></td>
        
        <input type="hidden" name="unit_weight[]" id="unit_weight${next_id}" class="form-control unit-weight dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1">
        <td><input type="tel" name="dispatch_qty[]" id="dispatch_qty${next_id}" class="form-control dispatch-qty dynamic_row${next_id}" min="0" step="0.01" onkeyup="calculateAmount(${next_id})" style="width:80px;"></td>
        <td><input type="tel" name="balance_qty[]" id="balance_qty${next_id}" class="form-control balance-qty dynamic_row${next_id}" min="0" step="0.01" readonly onkeyup="calculateAmount(${next_id})" style="width:80px;"></td>
        
        
        <input type="hidden" name="min_qty[]" id="min_qty${next_id}" class="form-control min-qty dynamic_row${next_id}" min="0" step="0.01">
        <td><input type="tel" name="gst[]" id="gst${next_id}" class="form-control gst-percent dynamic_row${next_id}" min="0" max="100" step="0.01" readonly tabindex="-1" style="width:80px;"></td>
        <td><input type="tel" name="unit_rate[]" id="unit_rate${next_id}" class="form-control unit-rate dynamic_row${next_id}" min="0" step="0.01" readonly style="width:80px;"></td>
        <td><input type="tel" name="disc_amt[]" id="disc_amt${next_id}" class="form-control disc-amt dynamic_row${next_id}" min="0" step="0.01" readonly style="width:80px;"></td>
        
        <td><input type="tel" name="total_amt[]" id="total_amt${next_id}" class="form-control total-amount dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1" style="width:90px;"></td>
        <td><input type="tel" name="total_weight[]" id="total_weight${next_id}" class="form-control total-weight dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1" style="width:90px;"></td>
      </tr>
		`);
    if (row == null) {
      $('#sr' + next_id).val($('#sr').val());
      $('#so_no' + next_id).val($('#so_no').val());
      $('#item_id' + next_id).val($('#item_id').val());
      $('#item_name' + next_id).val($('#item_name').val());
      $('#uom' + next_id).val($('#uom').val());
      $('#dispatch_qty' + next_id).val($('#dispatch_qty').val());
      $('#balance_qty' + next_id).val($('#balance_qty').val());
      $('#total_weight' + next_id).val($('#total_weight').val());
      $('#total_amt' + next_id).val($('#total_amt').val());

      $('#disc_amt' + next_id).val($('#disc_amt').val());
      $('#unit_rate' + next_id).val($('#unit_rate').val());
      $('#gst' + next_id).val($('#gst').val());
      $('.fixed_row').val('');
      calculateAmount(next_id);
    }
    $('#row_id').val(next_id);
    $('.selectpicker').selectpicker('refresh');
  }

  function calculateAmount(row) {
    var dispatchQty = parseFloat($('#dispatch_qty' + row).val()) || 0;
    var unitRate = parseFloat($('#unit_rate' + row).val()) || 0;
    var discAmt = parseFloat($('#disc_amt' + row).val()) || 0;
    var gstPercent = parseFloat($('#gst' + row).val()) || 0;
    var unitWeight = parseFloat($('#unit_weight' + row).val()) || 0;
    var minQty = parseFloat($('#min_qty' + row).val()) || 0;

    // Balance Qty = Original OrderQty - Dispatch Qty
    var newBalance = minQty - dispatchQty;
    if (newBalance < 0) {
      alert_float('warning', 'Dispatch Qty cannot be greater than Balance Qty!');
      $('#dispatch_qty' + row).val(minQty);
      dispatchQty = minQty;
      newBalance = 0;
    }
    $('#balance_qty' + row).val(newBalance.toFixed(0));

    // Total Weight
    var totalWeight = unitWeight * dispatchQty;
    $('#total_weight' + row).val(totalWeight.toFixed(2));

    // Total Amount
    var netRate = unitRate - discAmt;
    var taxableAmt = netRate * dispatchQty;
    var gstAmt = taxableAmt * (gstPercent / 100);
    var totalAmt = taxableAmt + gstAmt;

    $('#total_amt' + row).val(totalAmt.toFixed(2));

    calculateTotals();
  }

  function calculateTotals() {
    var totalWeight = 0;
    var totalQty = 0;
    var itemTotalAmt = 0;
    var totalDiscAmt = 0;
    var totalTaxableAmt = 0;
    var totalGstAmt = 0;

    $('#items_body tr').each(function() {
      var row = $(this);
      var dispatchQty = parseFloat(row.find('.dispatch-qty').val()) || 0;
      var unitWeight = parseFloat(row.find('[id^="unit_weight"]').val()) || 0;
      var unitRate = parseFloat(row.find('.unit-rate').val()) || 0;
      var discAmt = parseFloat(row.find('.disc-amt').val()) || 0;
      var gstPercent = parseFloat(row.find('.gst-percent').val()) || 0;

      var rowWeight = unitWeight * dispatchQty;
      var rowItemTotal = unitRate * dispatchQty;
      var rowTotalDisc = discAmt * dispatchQty;
      var rowNetRate = unitRate - discAmt;
      var rowTaxable = rowNetRate * dispatchQty;
      var rowGst = rowTaxable * (gstPercent / 100);

      totalWeight += rowWeight;
      totalQty += dispatchQty;
      itemTotalAmt += rowItemTotal;
      totalDiscAmt += rowTotalDisc;
      totalTaxableAmt += rowTaxable;
      totalGstAmt += rowGst;
    });

    $('#total_weight_display').text(totalWeight.toFixed(2));
    $('#total_qty_display').text(totalQty.toFixed(2));
    $('#item_total_amt_display').text(itemTotalAmt.toFixed(2));
    $('#disc_amt_display').text(totalDiscAmt.toFixed(2));
    $('#taxable_amt_display').text(totalTaxableAmt.toFixed(2));

    $('#total_qty').val(totalQty.toFixed(2));

    var customerState = $('#customer_state').val();
    var cgstAmt = 0,
      sgstAmt = 0,
      igstAmt = 0;

    if (totalTaxableAmt > 0) {
      // Weighted average GST across all rows
      var totalGstPercent = 0;
      $('#items_body tr').each(function() {
        var row = $(this);
        var dispatchQty = parseFloat(row.find('.dispatch-qty').val()) || 0;
        var unitRate = parseFloat(row.find('.unit-rate').val()) || 0;
        var discAmt = parseFloat(row.find('.disc-amt').val()) || 0;
        var gstPercent = parseFloat(row.find('.gst-percent').val()) || 0;
        var rowTaxable = (unitRate - discAmt) * dispatchQty;
        totalGstPercent += (rowTaxable / totalTaxableAmt) * gstPercent;
      });
      totalGstPercent = Math.round(totalGstPercent * 100) / 100;

      if (customerState === '<?= $company_detail->state ?>') {
        cgstAmt = totalGstAmt / 2;
        sgstAmt = totalGstAmt / 2;
      } else {
        igstAmt = totalGstAmt;
      }
    }

    $('#cgst_amt_display').text(cgstAmt.toFixed(2));
    $('#sgst_amt_display').text(sgstAmt.toFixed(2));
    $('#igst_amt_display').text(igstAmt.toFixed(2));

    var netAmtRaw = totalTaxableAmt + totalGstAmt;
    var netAmt = Math.round(netAmtRaw);
    var roundOff = netAmt - netAmtRaw;

    $('#round_off_amt_display').text(roundOff.toFixed(2));
    $('#net_amt_display').text(netAmt.toFixed(2));

    // Hidden inputs for form submission
    $('#total_weight_hidden').val(totalWeight.toFixed(2));
    $('#total_qty_hidden').val(totalQty.toFixed(2));
    $('#item_total_amt_hidden').val(itemTotalAmt.toFixed(2));
    $('#disc_amt_hidden').val(totalDiscAmt.toFixed(2));
    $('#taxable_amt_hidden').val(totalTaxableAmt.toFixed(2));
    $('#cgst_amt_hidden').val(cgstAmt.toFixed(2));
    $('#sgst_amt_hidden').val(sgstAmt.toFixed(2));
    $('#igst_amt_hidden').val(igstAmt.toFixed(2));
    $('#round_off_amt_hidden').val(roundOff.toFixed(2));
    $('#net_amt_hidden').val(netAmt.toFixed(2));
  }

  function get_required_fields(form_id) {
    let fields = [];
    $('#' + form_id + ' [required]').each(function() {
      fields.push($(this).attr('id'));
    });
    return fields;
  }

  function getItemsDetailsonSO(callback = null) {
    let OrderIDs = $('#so_list').val();
    if (!OrderIDs || OrderIDs.length === 0) {
      $('#items_body').html('');
      $('#row_id').val(0);
      calculateTotals();
      return;
    }
    $.ajax({
      url: "<?= admin_url(); ?>DeliveryOrder/GetHistoryDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        OrderID: OrderIDs
      },
      success: function(response) {
        if (response.success === true) {
          let items = response.items;

          $('#items_body').html('');
          $('#row_id').val(0);

          loadItemDropdown(function() {

            for (let i = 0; i < items.length; i++) {
              addRow(2);
              let row = parseInt($('#row_id').val());

              $('#sr' + row).val(row);
              $('#so_no' + row).val(items[i].OrderID);

              $('#item_id' + row).val(items[i].ItemID);
              $('#item_id' + row).selectpicker('refresh');
              $('#dispatch_qty' + row).val('');
              $('#balance_qty' + row).val(Number(items[i].BalanceQty).toFixed(0));
              $('#min_qty' + row).val(Number(items[i].BalanceQty).toFixed(2));
              $('#unit_rate' + row).val(Number(items[i].BasicRate).toFixed(2));
              $('#disc_amt' + row).val(Number(items[i].DiscAmt).toFixed(2));
              $('#total_weight' + row).val('0.00');
              $('#total_amt' + row).val('0.00');

              getItemDetails(items[i].ItemID, row);
            }

            $('.selectpicker').selectpicker('refresh');
            calculateTotals();
          });

        } else {
          alert_float('warning', response.message);
        }
      }
    });
  }

  function loadItemDropdown(callback = null) {

    $.ajax({
      url: '<?= admin_url('DeliveryOrder/getItem'); ?>',
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          let form_mode = $('#form_mode').val();

          $('#items_body .dynamic_item').each(function() {
            var currentVal = $(this).val();
            $(this).html(html);
            $(this).val(currentVal);
            $(this).selectpicker('refresh');
          });
        }
        if (callback) callback();
      }
    });
  }


  function getCustomerDetailsLocation(callback = null) {
    var CustomerId = $('#customer_id').val();

    if (!CustomerId) {
      $('#customer_location').html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }
    $.ajax({
      url: '<?= admin_url('DeliveryOrder/getCustomerDetailsLocation'); ?>',
      type: 'POST',
      data: {
        customer_id: CustomerId
      },
      dataType: 'json',
      success: function(response) {

        if (!response.success) return;

        var data = response.data;

        // -----------------------------
        // CUSTOMER DETAILS
        // -----------------------------
        $('#customer_gst_no').val(data.gst_no || '');
        $('#customer_country').val(data.country || '');
        $('#customer_state').val(data.state || '');
        $('#customer_address').val(data.address || '');

        $('#consignee_name').val(
          (data.company || '') +
          (data.AccountID ? ' - (' + data.AccountID + ')' : '')
        );

        $('#consignee_address').val(data.address || '');
        $('#GSTIN').val(data.gst_no || '');
        $('#billing_state').val(data.state || '');

        // -----------------------------
        // LOCATION DROPDOWN
        // -----------------------------
        var html = '<option value="" selected disabled>None selected</option>';

        $.each(response.location, function(index, loc) {
          if (!loc.id) return;
          html += '<option value="' + loc.id + '">' + loc.city + '</option>';
        });

        $('#customer_location').html(html);
        $('#consignee_location').html(html);

        $('.selectpicker').selectpicker('refresh');

        // -----------------------------
        // SALES ORDERS TABLE
        // -----------------------------

        let validHtml = '';
        let invalidHtml = '';

        if (response.valid_orders.length > 0) {

          $.each(response.valid_orders, function(index, value) {

            validHtml += `
                <tr class="get_Details"
                    data-unit-rate="${value.BasicRate || 0}"
                    data-discount="${value.DiscAmt || 0}"
                    data-gst="${value.item_tax || 0}"
                    data-qty="${value.TotalQuantity || 0}">

                    <td>${index + 1}</td>
                    <td><input type="checkbox" class="row_checkbox valid_checkbox" value="${value.history_id}"></td>
                    <td>${value.OrderID}</td>
                    <td>${formatDate(value.DeliveryFrom)}</td>
                    <td>${formatDate(value.DeliveryTo)}</td>
                    <td>${value.item_name || ''}</td>
                    <td>${value.Item_Order_Qty || 0}</td>
                    <td>${(value.Item_Order_Qty || 0) - (value.UsedQty || 0)}</td>
                    <td>${value.item_unit || ''}</td>
                    <td class="rate-with-gst">0.00</td>
                    <td class="rate-of-uom">0.00</td>
                    <td>${value.OrderAmt || 0}</td>
                    <td>${value.UsedAmt || 0}</td>
                </tr>
            `;
          });
        }

        if (response.invalid_orders.length > 0) {

          $.each(response.invalid_orders, function(index, value) {

            invalidHtml += `
                <tr class="get_Details"
                    data-unit-rate="${value.BasicRate || 0}"
                    data-discount="${value.DiscAmt || 0}"
                    data-gst="${value.item_tax || 0}"
                    data-qty="${value.TotalQuantity || 0}">

                    <td>${index + 1}</td>
                    <td><input type="checkbox" class="row_checkbox invalid_checkbox" value="${value.history_id}"></td>
                    <td>${value.OrderID}</td>
                    <td>${formatDate(value.DeliveryFrom)}</td>
                    <td>${formatDate(value.DeliveryTo)}</td>
                    <td>${value.item_name || ''}</td>
                    <td>${value.Item_Order_Qty || 0}</td>
                    <td>${(value.Item_Order_Qty || 0) - (value.UsedQty || 0)}</td>
                    <td>${value.item_unit || ''}</td>
                    <td class="rate-with-gst">0.00</td>
                    <td class="rate-of-uom">0.00</td>
                    <td>${value.OrderAmt || 0}</td>
                </tr>
            `;
          });
        }

        $("#valid_so_body").html(validHtml);
        $("#invalid_so_body").html(invalidHtml);

        calculateModalRates();

        // Show modal only if not edit mode
        if (!callback) {
          $('#modal_customer_name').text(CustomerId);

          var alreadySelectedSoNos = [];
          $('#items_body tr').each(function() {
            var soNo = $(this).find('input[name="so_no[]"]').val();
            if (soNo) alreadySelectedSoNos.push(soNo.trim());
          });

          $('#CustomerSOModal').modal('show');

          $('#CustomerSOModal').one('shown.bs.modal', function() {
            if (alreadySelectedSoNos.length > 0) {
              $('#valid_so_body .row_checkbox, #invalid_so_body .row_checkbox').each(function() {
                var rowSoNo = $(this).closest('tr').find('td').eq(2).text().trim();

                var checkboxVal = $(this).val().trim();
                if (alreadySelectedSoNos.indexOf(rowSoNo) !== -1 ||
                  alreadySelectedSoNos.indexOf(checkboxVal) !== -1) {
                  $(this).prop('checked', true);
                } else {
                  $(this).prop('checked', false);
                }
              });
            } else {
              $('#valid_so_body .row_checkbox, #invalid_so_body .row_checkbox')
                .prop('checked', false);
              $('#select_all_valid, #select_all_invalid').prop('checked', false);
            }
            calculateModalRates();
          });
        }

        if (callback) callback();
      }
    });
  }

  $('#CustomerSOModal').on('hidden.bs.modal', function() {
    $('#valid_so_body .row_checkbox, #invalid_so_body .row_checkbox')
      .prop('checked', false);
    $('#select_all_valid, #select_all_invalid').prop('checked', false);
  });

  $(document).on('changed.bs.select', 'select.dynamic_item', function(e, clickedIndex, isSelected, previousValue) {

    var itemId = $(this).val();

    if (!itemId) {
      console.log("Ignored empty selection");
      return;
    }

    var selectId = this.id || '';
    var id = selectId.replace('item_id', '');

    console.log("Selected Item ID:", itemId);

    getItemDetails(itemId, id);
  });


  function getItemDetails(itemId, id = '') {

    if (!itemId) return;

    var isDuplicate = false;

    $('.dynamic_item').not('#item_id' + id).each(function() {
      if ($(this).val() == itemId) {
        isDuplicate = true;
        return false;
      }
    });

    if (isDuplicate) {
      alert_float('warning', 'Please select other item, this item already selected.');
      $('#item_id' + id).val('').selectpicker('refresh');
      $('.dynamic_row' + id).val('');
      return;
    }

    $.ajax({
      url: '<?= admin_url('DeliveryOrder/GetItemDetails'); ?>',
      type: 'POST',
      data: {
        item_id: itemId
      },
      dataType: 'json',
      success: function(response) {

        if (response.status === 'success' && response.data) {
          var data = response.data;

          $('#item_id' + id).val(itemId);
          $('#item_name' + id).val(data.ItemName || '');
          $('#uom' + id).val(data.unit || '');
          $('#unit_weight' + id).val(Number(data.UnitWeight) || 0);
          $('#gst' + id).val(Number(data.tax) || 0);
          $('#min_qty' + id).focus();
        }
        calculateTotals();
      }
    });
  }

  $('#main_save_form').on('submit', function(e) {
    e.preventDefault();

    $(this).find('input[role="combobox"], input[type="search"]').removeAttr('required');

    let form_mode = $('#form_mode').val();

    let required_fields = get_required_fields('main_save_form');
    let validated = validate_fields(required_fields);

    if (validated === false) {
      return;
    }

    if (!validateFreightFields()) return;

    var form_data = new FormData(this);
    form_data.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );
    if (form_mode == 'edit') {
      form_data.append('update_id', $('#update_id').val());
    }

    $.ajax({
      url: "<?= admin_url(); ?>DeliveryOrder/SaveDeliveryOrder",
      method: "POST",
      dataType: "JSON",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function() {
        $('button[type=submit]').attr('disabled', true);
      },
      complete: function() {
        $('button[type=submit]').attr('disabled', false);
      },
      success: function(response) {
        if (response.success == true) {
          alert_float('success', response.message);
          ResetForm();
          let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getDeliveryOrderDetails(${response.data.id})">
						<td>${response.data.OrderID}</td>
            <td>${moment(response.data.TransDate).format('DD/MM/YYYY')}</td>
            <td>${response.data.CategoryName}</td>
            <td>${response.data.company}</td>
            <td>${response.data.TotalWt}</td>
            <td>${response.data.TotalQty}</td>
            <td>${response.data.ItemTotal}</td>
            <td>${response.data.TotalDisc}</td>
            <td>${response.data.TaxAmt}</td>
            <td>${response.data.CGSTAmt}</td>
            <td>${response.data.SGSTAmt}</td>
            <td>${response.data.IGSTAmt}</td>
            <td>${response.data.RoundOff}</td>
            <td>${response.data.NetAmt}</td>
					</tr>`;
          if (form_mode == 'edit') {
            $('.get_Details[data-id="' + response.data.id + '"]').replaceWith(html);
          } else {
            $('#table_ListModal tbody').prepend(html);
          }

        } else {
          alert_float('warning', response.message);
        }
      }
    });
  });

  function getDeliveryOrderDetails(id) {
    ResetForm();
    $.ajax({
      url: "<?= admin_url(); ?>DeliveryOrder/GetDeliveryOrderDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        id: id
      },
      success: function(response) {
        if (response.success == true) {
          let d = response.data;

          $('#update_id').val(id);
          $('#OrderID').val(d.OrderID);
          $('#delivery_order_date').val(moment(d.DODate).format('DD/MM/YYYY'));
          $('#DispatchFrom').val(d.DispatchFrom).selectpicker('refresh');
          $('#item_category').val(d.CategoryID).selectpicker('refresh');
          $('#advreg').val(d.AdvRegType).selectpicker('refresh');
          $('#gate_no').val(d.GateINID);
          $('#customer_id').val(d.AccountID).selectpicker('refresh');

          getCustomerDetailsLocation(function() {
            $('#customer_location').val(d.CustLocationID).selectpicker('refresh');
            $('#consignee_location').val(d.ConsigneeLocation).selectpicker('refresh');
          });

          $('#consignee_name').val(d.ConsigneeName);
          $('#customer_address').val(d.CustAddress);
          $('#consignee_address').val(d.ConsigneeAddress);

          $('#trans_arranged').val(d.TransArranged).selectpicker('refresh');
          $('#transporter_name').val(d.TransporterID).selectpicker('refresh');
          $('#VehicleNo').val(d.VehicleNo).selectpicker('refresh');
          $('#lr_no').val(d.LRNo);
          $('#lr_date').val(d.LRDate ? moment(d.LRDate).format('DD/MM/YYYY') : '');
          $('#driver_name').val(d.DriverName);
          $('#mobile').val(d.MobileNo);
          $('#license_no').val(d.LicenseNo);
          $('#freight_rate').val(d.FreightRate);
          $('#total_freight').val(d.TotalFreight);
          $('#by_customer').val(d.ToPayByCust);
          $('#pay_cash').val(d.PayInCash);
          $('#pay_bank').val(d.PayByBank);
          $('#after_delivery').val(d.PayAfterDelivery);

          $('#items_body').html('');
          $('#row_id').val(0);

          if (d.history && d.history.length > 0) {

            function processRow(index) {
              if (index >= d.history.length) {
                calculateTotals();
                return;
              }

              var item = d.history[index];
              var isLocked = item.is_locked == 1;
              addRow(2);
              var row = parseInt($('#row_id').val());

              $('#item_uid' + row).val(item.id || 0);
              $('#sr' + row).val(index + 1);
              $('#so_no' + row).val(item.OrderID || '');
              $('#item_id' + row).val(item.ItemID || '');
              $('#unit_rate' + row).val(Number(item.BasicRate || 0).toFixed(2));
              $('#disc_amt' + row).val(Number(item.DiscAmt || 0).toFixed(2));
              $('#dispatch_qty' + row).val(Number(item.OrderQty || 0).toFixed(0));
              $('#balance_qty' + row).val(Number(item.BalanceQty || 0).toFixed(0));

              var originalBalance = Number(item.BalanceQty || 0) + Number(item.OrderQty || 0);
              $('#min_qty' + row).val(originalBalance.toFixed(0));

              if (isLocked) {
                $('#dispatch_qty' + row)
                  .prop('readonly', true)
                  .prop('disabled', false)
                  .css({
                    'background-color': '#f5f5f5',
                    'cursor': 'not-allowed',
                    'border-color': '#ccc'
                  })
                  .off('keyup');

                $('#dispatch_qty' + row).closest('tr').css({
                  'border-left': '3px solid #e74c3c',
                  'background-color': '#fff8f8'
                });
              }

              $.ajax({
                url: '<?= admin_url('DeliveryOrder/GetItemDetails'); ?>',
                type: 'POST',
                data: {
                  item_id: item.ItemID
                },
                dataType: 'json',
                success: function(res) {
                  if (res.status === 'success' && res.data) {
                    $('#item_name' + row).val(res.data.ItemName || '');
                    $('#uom' + row).val(res.data.unit || '');
                    $('#unit_weight' + row).val(Number(res.data.UnitWeight || 0).toFixed(2));
                    $('#gst' + row).val(Number(res.data.tax || 0));
                  }
                  calculateAmount(row);
                  processRow(index + 1);
                },
                error: function() {
                  calculateAmount(row);
                  processRow(index + 1);
                }
              });
            }
            processRow(0);
          }

          // ── Invoice Lock: disable entire form if invoice exists ──
          if (d.is_invoice_locked == 1) {

            // Show warning message
            $('#invoice_lock_warning').show();

            // Lock all form inputs
            $('#main_save_form input:not([type="hidden"])').prop('readonly', true);
            $('#main_save_form select').prop('disabled', true).selectpicker('refresh');
            $('#main_save_form textarea').prop('readonly', true);

            // Lock all dispatch qty fields in items table
            $('#items_body input').prop('readonly', true);

            // Hide Save/Update buttons
            $('.saveBtn').hide();
            $('.updateBtn').hide();

          } else {
            $('#invoice_lock_warning').hide();
            $('.updateBtn').show();
          }

          $('.selectpicker').selectpicker('refresh');
          $('#form_mode').val('edit');
          $('.saveBtn').hide();
          $('.updateBtn').show();
          $('.printBtn').show();
          $('#ListModal').modal('hide');

        } else {
          alert_float('warning', response.message);
        }
      }
    });
  }
</script>


<script>
  $(document).on("click", ".sortable", function() {
    var table = $("#table_ListModal tbody");
    var rows = table.find("tr").toArray();
    var index = $(this).index();
    var ascending = !$(this).hasClass("asc");
    $(".sortable").removeClass("asc desc");
    $(".sortable span").remove();

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

  function myFunction2() {
    var input = document.getElementById("myInput1");
    var filter = input.value.toUpperCase();

    var table = document.getElementById("table_ListModal");
    var tbody = table.getElementsByTagName("tbody")[0];
    var tr = tbody.getElementsByTagName("tr");

    for (var i = 0; i < tr.length; i++) {
      var tds = tr[i].getElementsByTagName("td");
      var rowMatch = false;

      for (var j = 0; j < tds.length; j++) {
        var txtValue = tds[j].textContent || tds[j].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          rowMatch = true;
          break;
        }
      }
      tr[i].style.display = rowMatch ? "" : "none";
    }
  }
</script>
<style>
  /* Remove default padding */
  .modal-header {
    padding: 0;
    border-bottom: none;
  }

  /* Blue Gradient Top Bar */
  .custom-header .header-top {
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  /* Title */
  .custom-header .modal-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }

  /* Close Button */
  .close-btn {
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
  }

  /* Filter Section */
  .header-filters {
    padding: 10px;
    display: flex;
    align-items: flex-end;
    gap: 25px;
  }

  /* Each Filter */
  .filter-group {
    display: flex;
    flex-direction: column;
  }

  .filter-group label {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
  }

  /* Input Styling */
  .filter-group .form-control {
    height: 36px;
    min-width: 200px;
  }

  /* Search Button */
  .search-btn {
    background: #1fa0d8;
    color: #fff;
    border: none;
    padding: 8px 20px;
    font-weight: 600;
    border-radius: 4px;
  }

  .search-btn:hover {
    background: #168ac0;
  }

  #table_ListModal tbody tr {
    cursor: pointer;
  }

  tr.locked-row td input {
    background-color: #f5f5f5 !important;
    cursor: not-allowed !important;
    color: #999 !important;
  }

  #table_ListModal tbody tr:hover {
    background-color: rgb(171, 174, 176);
  }
</style>