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

  .tableFixHead2 {
    overflow: auto;
    max-height: 50vh;
  }

  .sortable,
  .get_Details {
    cursor: pointer;
  }

  .total-label-row {
    display: flex;
    align-items: center;
    padding: 2px 0;
    border-bottom: 1px solid #eee;
  }

  .total-label-row .total-display {
    flex: 1;
    padding: 0;
    text-align: right;
    font-weight: 600;
  }

  .fixed-td {
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .get_Details.processing {
    pointer-events: none;
    opacity: 0.9;
  }

  @media only screen and (max-width: 600px) {
    #header ul {
      display: none !important;
    }
  }

  .num-form-control {
    text-align: right !important;
    font-weight: bold !important;
    width: 50% !important;
    margin-left: auto;
    box-shadow: none !important;
  }

  .num2-form-control {
    width: 100% !important;
    padding: 0px 2px !important;
  }

  .num-form-control[readonly],
  .num2-form-control[readonly] {
    background: transparent !important;
    border: none !important;
    outline: none !important;
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
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Direct</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="po_no">
                    <label for="po_no" class="control-label"><small class="req text-danger">* </small> PO No</label>
                    <input type="text" name="po_no" id="po_no" class="form-control" value="<?= $po_no; ?>" app-field-label="Purchase Order No" required readonly placeholder="Auto Generated">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="order_date">
                    <?= render_date_input('order_date', 'Document Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="center_location">
                    <label for="center_location" class="control-label"><small class="req text-danger">* </small> Center Location</label>
                    <select name="center_location" id="center_location" class="form-control selectpicker" data-live-search="true" app-field-label="Center Location" required onchange="getGodownList(this.value);">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($purchaselocation)) :
                        foreach ($purchaselocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="warehouse_id">
                    <label for="warehouse_id" class="control-label"><small class="req text-danger">* </small> Warehouse</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-control selectpicker" data-live-search="true" app-field-label="Warehouse" required>
                      <option value="" selected disabled>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="purchase_type">
                    <label for="purchase_type" class="control-label"><small class="req text-danger">* </small> Purchase Type</label>
                    <select name="purchase_type" id="purchase_type" class="form-control selectpicker" data-live-search="true" app-field-label="Purchase Type" required onchange="getItemList(this.value);">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($item_type)) :
                        foreach ($item_type as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['ItemTypeName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="payment_terms">
                    <label for="payment_terms" class="control-label"><small class="req text-danger">* </small> Payment Terms</label>
                    <select name="payment_terms" id="payment_terms" class="form-control selectpicker" data-live-search="true" app-field-label="Payment Terms" required>
                      <option value="" selected disabled>None selected</option>
                      <option value="Credit">Credit</option>
                      <option value="Advance">Advance</option>
                      <option value="OnDelivery">On Delivery</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_id">
                    <label for="vendor_id" class="control-label"><small class="req text-danger">* </small> Vendor Name</label>
                    <select name="vendor_id" id="vendor_id" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor Name" required onchange="getVendorDetailsLocation(this.value);">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor_list)) :
                        foreach ($vendor_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' - ' . $value['billing_state'] . ' (' . $value['AccountID'] . ')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_gst">
                    <label for="vendor_gst" class="control-label">Vendor GST</label>
                    <input type="text" name="vendor_gst" id="vendor_gst" class="form-control" app-field-label="Vendor GST" readonly placeholder="">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_state">
                    <label for="vendor_state" class="control-label">State</label>
                    <input type="text" name="vendor_state" id="vendor_state" class="form-control" app-field-label="Vendor State" readonly placeholder="">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="closing_balance">
                    <label for="closing_balance" class="control-label">Closing Balance</label>
                    <input type="text" name="closing_balance" id="closing_balance" class="form-control" app-field-label="Closing Balance" readonly placeholder="">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="broker_id">
                    <label for="broker_id" class="control-label">Broker Name</label>
                    <select name="broker_id" id="broker_id" class="form-control selectpicker" data-live-search="true" app-field-label="Broker Name">
                      <option value="" selected disabled>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_doc_no">
                    <label for="vendor_doc_no" class="control-label">Vendor Doc No</label>
                    <input type="text" name="vendor_doc_no" id="vendor_doc_no" class="form-control" app-field-label="Vendor Doc No" placeholder="">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_doc_date">
                    <?= render_date_input('vendor_doc_date', 'Vendor Doc Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_doc_amt">
                    <label for="vendor_doc_amt" class="control-label">Vendor Doc Amount</label>
                    <input type="text" name="vendor_doc_amt" id="vendor_doc_amt" class="form-control" app-field-label="Vendor Doc Amount" placeholder="">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="tds_id">
                    <label for="tds_id" class="control-label">TDS Section</label>
                    <select name="tds_id" id="tds_id" class="form-control selectpicker" data-live-search="true" app-field-label="TDS Section" onchange="getTDSDetails(this.value);">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($tds_list)) :
                        foreach ($tds_list as $value) :
                          echo '<option value="' . $value['TDSCode'] . '">' . $value['TDSName'] . ' (' . $value['TDSCode'] . ')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="tds_percentage">
                    <label for="tds_percentage" class="control-label">TDS %</label>
                    <select name="tds_percentage" id="tds_percentage" class="form-control selectpicker" data-live-search="true" app-field-label="TDS %">
                      <option value="" selected disabled>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ledger_group_id">
                    <label for="ledger_group_id" class="control-label"><small class="req text-danger">* </small> Ledger Group</label>
                    <select name="ledger_group_id" id="ledger_group_id" class="form-control selectpicker" data-live-search="true" app-field-label="Ledger Group" required>
                      <option value="" selected disabled>None selected</option>
                      <option value="1000020">Purchase Accounts</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ledger_id">
                    <label for="ledger_id" class="control-label"><small class="req text-danger">* </small> Ledger</label>
                    <select name="ledger_id" id="ledger_id" class="form-control selectpicker" data-live-search="true" app-field-label="Ledger Name" required>
                      <option value="" selected disabled>None selected</option>
                      <option value="PURCH">Purchase Account</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-12 mbot5">
                  <h4 class="bold p_style">Items:</h4>
                  <hr class="hr_style">
                </div>
                <div class="col-md-12 mbot5">
                  <input type="hidden" id="row_id" value="0">
                  <table width="100%" class="table" id="items_table">
                    <thead>
                      <tr style="text-align: center;">
                        <th>Item Name</th>
                        <th>Item Group</th>
                        <th>HSN Code</th>
                        <th>UOM</th>
                        <th>Unit Wt (Kg)</th>
                        <th>Qty</th>
                        <th>Unit Rate</th>
                        <th>Disc Amt</th>
                        <th>GST %</th>
                        <th>CGSTAMT</th>
                        <th>SGSTAMT</th>
                        <th>IGSTAMT</th>
                        <th>Amount</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td style="width: 250px;">
                          <select id="item_id" class="form-control fixed_row dynamic_row dynamic_item selectpicker" data-live-search="true" app-field-label="Item Name" onchange="getItemDetails(this.value, '');">
                            <option value="" selected disabled>Select Item</option>
                          </select>
                        </td>
                        <td><input type="text" id="item_group" class="form-control num2-form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="text" id="hsn_code" class="form-control num2-form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="text" id="uom" class="form-control num2-form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="unit_weight" class="form-control num2-form-control fixed_row" min="0" step="0.01" readonly tabindex="-1"></td>
                        <td style="min-width: 80px;"><input type="tel" id="min_qty" class="form-control num2-form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');"></td>
                        <td style="min-width: 80px;"><input type="tel" id="unit_rate" class="form-control num2-form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');"></td>
                        <td style="min-width: 80px;"><input type="tel" id="disc_amt" class="form-control num2-form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');"></td>
                        <td><input type="tel" id="gst" class="form-control num2-form-control fixed_row" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
                        <td><input type="tel" id="cgstamt" class="form-control num2-form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="sgstamt" class="form-control num2-form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="igstimt" class="form-control num2-form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="amount" class="form-control num2-form-control fixed_row" readonly tabindex="-1"></td>
                        <td>
                          <button type="button" class="btn btn-success" onclick="addRow();"><i class="fa fa-plus"></i></button>
                        </td>
                      </tr>
                    </thead>
                    <tbody id="items_body"></tbody>
                  </table>
                </div>

                <div class="col-md-6"></div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <h4 class="bold p_style">Total Summary</h4>
                      <hr class="hr_style">
                    </div>

                    <!-- LEFT COLUMN -->
                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row">
                        <label>Purchase Amt:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="purchase_amount" id="purchase_amount" value="0">
                      </div>

                      <div class="total-label-row">
                        <label>Discount Amt:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="discount_amount" id="discount_amount" value="0">
                      </div>

                      <div class="total-label-row">
                        <label>CGST Amt:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="total_cgst_amount" id="total_cgst_amount" value="0">
                      </div>

                      <div class="total-label-row">
                        <label>SGST Amt:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="total_sgst_amount" id="total_sgst_amount" value="0">
                      </div>

                      <div class="total-label-row">
                        <label>IGST Amt:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="total_igst_amount" id="total_igst_amount" value="0">
                      </div>

                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row">
                        <label>Freight Amt:</label>
                        <input type="tel" class="form-control num-form-control" name="total_freight_amount" id="total_freight_amount" value="0" oninput="calculateTotals()" ;>
                      </div>

                      <div class="total-label-row">
                        <label>Other Amt:</label>
                        <input type="tel" class="form-control num-form-control" name="other_amount" id="other_amount" value="0" oninput="calculateTotals()" ;>
                      </div>

                      <div class="total-label-row">
                        <label>Round Off:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="roundoff_amount" id="roundoff_amount" value="0">
                      </div>
                      <div class="total-label-row">
                        <label>TDS Amt:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="tds_amount" id="tds_amount" value="0">
                      </div>

                    </div>

                    <!-- FULL WIDTH - NET AMOUNT -->
                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <div class="total-label-row" style="border-top: 2px solid #007bff; padding-top: 12px; margin-top: 8px; background-color: #f0f8ff; padding: 12px; border-radius: 4px;">
                        <label style="font-size: 16px; color: #007bff; font-weight: 700; padding-right: 15px;">Final Amount:</label>
                        <input type="tel" class="form-control num-form-control" readonly name="final_amount" id="final_amount" value="0">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <a href="#" class="btn btn-primary updateBtn" id="print_pdf" style="display: none;" target="_blank"><i class="fa fa-print"></i> Print PDF</a>
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
                  <button type="button" class="btn btn-danger" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
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

<div class="modal fade" id="ListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="width: 80vw;">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Direct Purchse Order List</h4>
      </div>
      <div class="modal-body" style="padding:5px 5px !important">
        <form action="" method="post" id="filter_list_form">
          <div class="row">
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="from_date">
                <?= render_date_input('from_date', 'From Date', date('01/m/Y'), []); ?>
              </div>
              <!-- <div class="form-group" app-field-wrapper="from_date">
                <label for="from_date" class="control-label">From Date</label>
                <div class="input-group date">
                  <input type="text" id="from_date" name="from_date" class="form-control datepicker filterInput" value="<?= date("01/m/Y") ?>" app-field-label="From Date" onchange="resetForm();">
                  <div class="input-group-addon">
                    <i class="fa-regular fa-calendar calendar-icon"></i>
                  </div>
                </div>
              </div> -->
            </div>
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="to_date">
                    <?= render_date_input('to_date','To Date', date('d/m/Y'), []); ?>
                  </div>
              <!-- <div class="form-group" app-field-wrapper="to_date">
                <label for="to_date" class="control-label">To Date</label>
                <div class="input-group date">
                  <input type="text" id="to_date" name="to_date" class="form-control datepicker filterInput" value="<?= date("d/m/Y") ?>" app-field-label="To Date" onchange="resetForm();">
                  <div class="input-group-addon">
                    <i class="fa-regular fa-calendar calendar-icon"></i>
                  </div>
                </div>
              </div> -->
            </div>
            <div class="col-md-5 mbot5" style="padding-top: 20px;">
              <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
            </div>
            <div class="col-md-3 mbot5" style="padding-top: 20px;">
              <input type="search" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search..." title="Type in a table">
            </div>
          </div>
        </form>
        <div class="progress" style="margin-bottom: 5px; height: 3px;">
          <div id="fetchProgress" class="progress-bar" style="width:0%"></div>
        </div>
        <div class="table-ListModal tableFixHead2">
          <table class="tree table table-striped table-bordered table-ListModal tableFixHead2" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortable">Order No</th>
                <th class="sortable">Order Date</th>
                <th class="sortable">Center Location</th>
                <th class="sortable">Warehouse</th>
                <th class="sortable">Vendor</th>
                <th class="sortable">Amt</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <br>
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

        $('#order_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate,
            timepicker: false
        });

        $('#vendor_doc_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate,
            timepicker: false
        });
        $('#from_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate,
            timepicker: false
        });
        $('#to_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate,
            timepicker: false
        });
    });
  $(document).ready(function() {
    setTimeout(() => {
      $('#filter_list_form').submit();
    }, 800);
  })

  function ResetForm() {
    $('#main_save_form')[0].reset();
    $('#form_mode').val('add');
    $('#update_id').val('');
    $('.updateBtn').hide();
    $('.saveBtn').show();
    $('#items_body').html('');
    $('.total-display').text('0.00');
    $('#row_id').val(0);
    $('#vendor_id').html(`
      <option value="" selected disabled>None selected</option>
      <?php
      if (!empty($vendor_list)) :
        foreach ($vendor_list as $value) :
          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' (' . $value['AccountID'] . ')</option>';
        endforeach;
      endif;
      ?>
    `);
    $('#purchase_order').html('<option value="" selected disabled>None selected</option>');
    $('#gatein_no').html(`
      <option value="" selected disabled>None selected</option>
      <?php
      if (!empty($gatein_list)) :
        foreach ($gatein_list as $value) :
          echo '<option value="' . $value['GateINID'] . '">' . $value['VehicleNo'] . ' (' . $value['GateINID'] . ')</option>';
        endforeach;
      endif;
      ?>
    `);
    $('.selectpicker').selectpicker('refresh');
  }

  $(document).on('input', 'input[type="tel"]', function() {
    this.value = this.value
      .replace(/[^0-9.]/g, '') // allow digits + dot
      .replace(/(\..*?)\..*/g, '$1'); // allow only one dot
  });

  function validate_fields(fields) {
    let data = {};
    for (let i = 0; i < fields.length; i++) {
      let value = $('#' + fields[i]).val();
      console.log(value);
      console.log(fields[i]);

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
      let fields = ['item_id', 'min_qty', 'disc_amt', 'unit_rate'];
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
        <td style="width: 250px;">
          <input type="hidden" name="item_uid[]" id="item_uid${next_id}" value="0">
          <select id="item_id${next_id}" name="item_id[]" class="form-control dynamic_row${next_id} dynamic_item selectpicker" data-live-search="true" app-field-label="Item Name" onchange="getItemDetails(this.value, '${next_id}');">${item_option}</select>
        </td>
        <td><input type="text" name="item_group[]" id="item_group${next_id}" class="form-control num2-form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="text" name="hsn_code[]" id="hsn_code${next_id}" class="form-control num2-form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="text" name="uom[]" id="uom${next_id}" class="form-control num2-form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="unit_weight[]" id="unit_weight${next_id}" class="form-control num2-form-control unit-weight dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="min_qty[]" id="min_qty${next_id}" class="form-control num2-form-control min-qty dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="unit_rate[]" id="unit_rate${next_id}" class="form-control num2-form-control unit-rate dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="disc_amt[]" id="disc_amt${next_id}" class="form-control num2-form-control disc-amt dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="gst[]" id="gst${next_id}" class="form-control num2-form-control gst-percent dynamic_row${next_id}" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="cgst[]" id="cgst${next_id}" class="form-control num2-form-control cgst-amt dynamic_row${next_id}" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="sgst[]" id="sgst${next_id}" class="form-control num2-form-control sgst-amt dynamic_row${next_id}" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="igst[]" id="igst${next_id}" class="form-control num2-form-control igst-amt dynamic_row${next_id}" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="amount[]" id="amount${next_id}" class="form-control num2-form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td>${row_btn}</td>
      </tr>
		`);
    if (row == null) {
      $('#item_id' + next_id).val($('#item_id').val());
      $('#item_group' + next_id).val($('#item_group').val());
      $('#hsn_code' + next_id).val($('#hsn_code').val());
      $('#uom' + next_id).val($('#uom').val());
      $('#unit_weight' + next_id).val($('#unit_weight').val());
      $('#unit_rate' + next_id).val($('#unit_rate').val());
      $('#min_qty' + next_id).val($('#min_qty').val());
      $('#disc_amt' + next_id).val($('#disc_amt').val());
      $('#gst' + next_id).val($('#gst').val());
      $('#cgst' + next_id).val($('#cgst').val());
      $('#sgst' + next_id).val($('#sgst').val());
      $('#igst' + next_id).val($('#igst').val());
      $('#amount' + next_id).val($('#amount').val());
      $('.fixed_row').val('');
      calculateAmount(next_id);
    }
    $('#row_id').val(next_id);
    $('.selectpicker').selectpicker('refresh');
  }

  function calculateAmount(row) {
    var minQty = parseFloat($('#min_qty' + row).val()) || 0;
    var unitRate = parseFloat($('#unit_rate' + row).val()) || 0;
    var discAmt = parseFloat($('#disc_amt' + row).val()) || 0;
    var gstPercent = parseFloat($('#gst' + row).val()) || 0;

    var netAmt = (unitRate * minQty);
    $('#amount' + row).val(netAmt.toFixed(2));

    var taxableAmt = netAmt - discAmt;
    var gstAmt = taxableAmt * (gstPercent / 100);

    var vendorState = $('#vendor_state').val().trim().toUpperCase();
    if (vendorState === '<?= $company_detail->state; ?>') {
      $('#cgst' + row).val(gstAmt.toFixed(2) / 2);
      $('#sgst' + row).val(gstAmt.toFixed(2) / 2);
      $('#igst' + row).val(0.00);
    } else {
      $('#cgst' + row).val(0.00);
      $('#sgst' + row).val(0.00);
      $('#igst' + row).val(gstAmt.toFixed(2));
    }

    calculateTotals();
    if ((row == '' || row == null) && minQty > 0 && unitRate > 0 && discAmt != '') {
      addRow();
    }
  }

  function calculateTotals() {
    var itemTotalAmt = totalDiscAmt = totalGstAmt = totalCGSTAmt = totalSGSTAmt = totalIGSTAmt = 0;
    $('#items_body tr').each(function() {
      var row = $(this);
      var qty = parseFloat(row.find('.min-qty').val()) || 0;
      var rate = parseFloat(row.find('.unit-rate').val()) || 0;
      var discAmt = parseFloat(row.find('.disc-amt').val()) || 0;
      var cgstAmt = parseFloat(row.find('.cgst-amt').val()) || 0;
      var sgstAmt = parseFloat(row.find('.sgst-amt').val()) || 0;
      var igstAmt = parseFloat(row.find('.igst-amt').val()) || 0;
      var rowItemTotal = rate * qty;

      itemTotalAmt += rowItemTotal;
      totalDiscAmt += discAmt;
      totalCGSTAmt += cgstAmt;
      totalSGSTAmt += sgstAmt;
      totalIGSTAmt += igstAmt;
    });
    $('#purchase_amount').val(itemTotalAmt.toFixed(2));
    $('#discount_amount').val(totalDiscAmt.toFixed(2));
    $('#total_cgst_amount').val(totalCGSTAmt.toFixed(2));
    $('#total_sgst_amount').val(totalSGSTAmt.toFixed(2));
    $('#total_igst_amount').val(totalIGSTAmt.toFixed(2));

    var total_freight_amount = parseFloat($('#total_freight_amount').val()) || 0;
    var other_amount = parseFloat($('#other_amount').val()) || 0;

    let amt = itemTotalAmt - totalDiscAmt + totalCGSTAmt + totalSGSTAmt + totalIGSTAmt + total_freight_amount + other_amount;

    var roundoff = Math.round(amt);
    var roundOffAmt = roundoff - amt;
    $('#roundoff_amount').val(roundOffAmt);

    var tds_amount = parseFloat($('#tds_amount').val()) || 0;

    let final_amount = amt + roundOffAmt - tds_amount;
    $('#final_amount').val(final_amount);
  }

  function get_required_fields(form_id) {
    let fields = [];
    $('#' + form_id + ' [required]').each(function() {
      fields.push($(this).attr('id'));
    });
    return fields;
  }

  function getVendorDetailsLocation(vendorId, callback = null) {
    $.ajax({
      url: '<?= admin_url('purchase/Quotation/getVendorDetailsLocation'); ?>',
      type: 'POST',
      data: {
        vendor_id: vendorId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var data = response.data;
          $('#vendor_gst').val(data.GSTIN || '');
          $('#vendor_state').val(data.billing_state || '');
          $('#tds_id').val(data.TDSSection || '');
          getTDSDetails(data.TDSSection, function() {
            $('#tds_percentage').val(data.TDSPer || '');
            $('.selectpicker').selectpicker('refresh');
          });

          html = '<option value="" selected disabled>None selected</option>';
          $.each(response.broker_list, function(index, loc) {
            if (loc.AccountID == null || loc.AccountID == '') return;
            html += `<option value="${loc.AccountID}">${loc.company} - ${loc.billing_state} (${loc.AccountID})</option>`;
          });
          $('#broker_id').html(html);

          $('.selectpicker').selectpicker('refresh');
          calculateTotals();
          if (callback) {
            callback();
          }
        }
      }
    });
  }

  function getTDSDetails(tds_code, callback = null) {
    $.ajax({
      url: '<?= admin_url('purchase/Direct/GetTDSDetailsByCode'); ?>',
      type: 'POST',
      data: {
        tds_code: tds_code
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var list = response.per_list;
          var html = '<option value="" selected disabled>None selected</option>';
          $.each(list, function(index, loc) {
            html += `<option value="${loc.rate}">${loc.rate}% ${loc.description}</option>`;
          })
          $('#tds_percentage').html(html);
          $('.selectpicker').selectpicker('refresh');
        } else {
          $('#tds_percentage').html('<option value="" selected disabled>None selected</option>');
          $('.selectpicker').selectpicker('refresh');
        }
        if (callback) {
          callback();
        }
      }
    })
  }

  function getGodownList(location_id, callback = null) {
    $.ajax({
      url: '<?= admin_url('purchase/Direct/GetGodownListByLocation'); ?>',
      type: 'POST',
      data: {
        location_id: location_id
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var list = response.godown_list;
          var html = '<option value="" selected disabled>None selected</option>';
          $.each(list, function(index, loc) {
            html += `<option value="${loc.id}">${loc.GodownName}</option>`;
          })
          $('#warehouse_id').html(html);
          $('.selectpicker').selectpicker('refresh');
        } else {
          $('#warehouse_id').html('<option value="" selected disabled>None selected</option>');
          $('.selectpicker').selectpicker('refresh');
        }
        if (callback) {
          callback();
        }
      }
    });
  }

  function getItemList(item_type, callback = null) {
    $.ajax({
      url: '<?= admin_url('purchase/Direct/GetItemListByType'); ?>',
      type: 'POST',
      data: {
        item_type: item_type
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var list = response.item_list;
          var html = '<option value="" selected disabled>None selected</option>';
          $.each(list, function(index, loc) {
            html += `<option value="${loc.ItemID}">${loc.ItemName} (${loc.ItemID})</option>`;
          })
          $('#item_id').html(html);
          $('.selectpicker').selectpicker('refresh');
        } else {
          $('#item_id').html('<option value="" selected disabled>None selected</option>');
          $('.selectpicker').selectpicker('refresh');
        }
        if (callback) {
          callback();
        }
      }
    });
  }

  function getItemDetails(itemId, id = '') {
    var isDuplicate = false;

    $('.dynamic_item').not('#item_id' + id).each(function() {
      if ($(this).val() == itemId && itemId != '') {
        isDuplicate = true;
        return false;
      }
    });
    if (isDuplicate) {
      alert_float('warning', 'Please select other item, this item already selected.');
      $('#item_id' + id).val('').focus();
      $('.selectpicker').selectpicker('refresh');
      $('.fixed_row').val('');
      $('.dynamic_row' + id).val('');
      return;
    }

    $.ajax({
      url: '<?= admin_url('purchase/Direct/GetItemDetailsById'); ?>',
      type: 'POST',
      data: {
        item_id: itemId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var data = response.data;
          $('#item_group' + id).val(data.DivisionName || '');
          $('#hsn_code' + id).val(data.hsn_code || '');
          $('#uom' + id).val(data.unit || '');
          $('#unit_weight' + id).val(Number(data.UnitWeight) || 0);
          $('#gst' + id).val(Number(data.tax) || 0);
          $('#min_qty' + id).focus();
          calculateAmount(id);
        } else {
          $('.fixed_row').val('');
          $('.selectpicker').selectpicker('refresh');
        }
        calculateTotals();
      },
      error: function(xhr, status, err) {
        console.log('Error fetching item details:', err);
      }
    });
  }

  $('#main_save_form').on('submit', function(e) {
    e.preventDefault();

    let form_mode = $('#form_mode').val();

    let required_fields = get_required_fields('main_save_form');
    let validated = validate_fields(required_fields);

    if (validated === false) {
      return;
    }

    var form_data = new FormData(this);
    form_data.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );
    if (form_mode == 'edit') {
      form_data.append('update_id', $('#update_id').val());
    }

    $.ajax({
      url: "<?= admin_url(); ?>purchase/Direct/SaveDirectPurchase",
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
          let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getDetails(${response.data.id})">
            <td class="text-center">${response.data.OrderID}</td>
            <td>${moment(response.data.OrderDate).format('DD/MM/YYYY')}</td>
            <td>${response.data.LocationName}</td>
            <td>${response.data.GodownName}</td>
            <td>${response.data.VendorName}</td>
            <td class="text-center">${Number(response.data.FinalAmt) || '-'}</td>
					</tr>`;
          if (form_mode == 'edit') {
            $('.get_Details[data-id="' + response.data.id + '"]').replaceWith(html);
          } else {
            $('#table_ListModal tbody').prepend(html);
          }
          ResetForm();
        } else {
          alert_float('warning', response.message);
        }
      }
    });
  });

  function getDetails(id, row) {
    let $row = $(row);
    if ($row.hasClass('processing')) return;
    $row.addClass('processing');

    ResetForm();
    $.ajax({
      url: "<?= admin_url(); ?>purchase/Direct/GetDirectOrderDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        id: id,
      },
      complete: function() {
        $row.removeClass('processing');
      },
      success: function(response) {
        if (response.success == true) {
          $('#form_mode').val('edit');
          $('.saveBtn').hide();
          $('.updateBtn').show();
          $('#ListModal').modal('hide');

          let d = response.data;
          $('#print_pdf').attr('href', '<?= admin_url('purchase/Direct/PrintPDF/'); ?>' + d.OrderID);
          $('#update_id').val(id);

          $('#po_no').val(d.OrderID);
          $('#order_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
          $('#center_location').val(d.CenterLocation);
          getGodownList(d.CenterLocation, function() {
            $('#warehouse_id').val(d.WarehouseID);
          });
          $('#purchase_type').val(d.ItemType);
          $('#vendor_id').val(d.AccountID);
          $('#vendor_state').val(d.state);
          getVendorDetailsLocation(d.AccountID, function() {
            $('#broker_id').val(d.BrokerID);
          });
          getItemList(d.ItemType, function() {
            let history = response.data.history;
            for (var i = 0; i < history.length; i++) {
              addRow(2);
              $('#item_uid' + (i + 1)).val(history[i].id);
              $('#item_id' + (i + 1)).val(history[i].ItemID);
              getItemDetails(history[i].ItemID, (i + 1));
              $('#min_qty' + (i + 1)).val(Number(history[i].OrderQty));
              $('#unit_rate' + (i + 1)).val(Number(history[i].BasicRate));
              $('#disc_amt' + (i + 1)).val(Number(history[i].DiscAmt));
              calculateAmount(i + 1);
            }
          });
          $('#payment_terms').val(d.PaymentTerms);
          $('#vendor_doc_no').val(d.VendorDocNo);
          $('#vendor_doc_date').val(moment(d.VendorDocDate).format('DD/MM/YYYY'));
          $('#vendor_doc_amt').val(d.VendorDocAmt);
          $('#tds_id').val(d.TDSCode);
          getTDSDetails(d.TDSCode, function() {
            $('#tds_percentage').val(d.TDSRate);
          });
          $('#ledger_group_id').val(d.LeaderGroupID);
          $('#ledger_id').val(d.LeaderID);
          $('#total_freight_amount').val(d.FreightAmt);
          $('#other_amount').val(d.OtherAmt);
          calculateTotals();

          $('.selectpicker').selectpicker('refresh');
        } else {
          alert_float('warning', response.message);
        }
      }
    });
  }

  $('#filter_list_form').submit(function(e) {
    e.preventDefault();

    let form = this;
    let limit = 1;
    let offset = 0;
    let totalRecords = 0;
    let loadedRecords = 0;
    $('#searchBtn').prop('disabled', true);
    $('#table_ListModal tbody').html('');

    function fetchChunk() {
      var form_data = new FormData(form);
      form_data.append('offset', offset);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );

      $.ajax({
        url: "<?= admin_url('purchase/Direct/ListFilter') ?>",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function(res) {
          let json = JSON.parse(res);
          if (!json.success) {
            $('#searchBtn').prop('disabled', false);
            if (offset === 0) {
              $('#table_ListModal tbody').html(
                '<tr><td colspan="10" class="text-center">No Data Found</td></tr>'
              );
            }
            return;
          }
          if (offset === 0) {
            totalRecords = parseInt(json.total) || 0;
          }
          if (json.rows && json.rows.length > 0) {
            appendRows(json.rows);
            loadedRecords += json.rows.length;
            offset += limit;
          }
          updateProgress(loadedRecords, totalRecords);
          if (loadedRecords >= totalRecords) {
            $('#searchBtn').prop('disabled', false);
            $('#fetchProgress').css('width', '0%')
            return;
          }
          fetchChunk();

        }
      });
    }
    fetchChunk();
  });

  function appendRows(rows) {
    let html = '';
    rows.forEach(function(row) {
      html += `<tr class="get_Details" data-id="${row.id}" onclick="getDetails(${row.id}, this)">
        <td class="text-center">${row.OrderID}</td>
        <td>${moment(row.OrderDate).format('DD/MM/YYYY')}</td>
        <td>${row.LocationName}</td>
        <td>${row.GodownName}</td>
        <td>${row.VendorName}</td>
        <td class="text-center">${Number(row.FinalAmt) || '-'}</td>
      </tr>`;
    });
    $('#table_ListModal tbody').append(html);
  }

  function updateProgress(loaded, total) {
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%')
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
    // Add sort classes and arrows
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