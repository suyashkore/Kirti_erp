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
    padding: 8px 0;
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

  select:disabled,
  input:disabled,
  .bootstrap-select.disabled .btn,
  .bootstrap-select > .disabled {
    background-color: #e9ecef !important;
    cursor: not-allowed !important;
    opacity: 0.7 !important;
  }

  .btn-add-row-disabled {
    opacity: 0.6 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
  }

  /* Status Badge Styles */
  .status-badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
    color: #fff;
  }
  .status-1  { background-color: #6c757d; }
  .status-2  { background-color: #dc3545; }
  .status-3  { background-color: #fd7e14; }
  .status-4  { background-color: #0d6efd; }
  .status-5  { background-color: #0dcaf0; color: #000 !important; }
  .status-6  { background-color: #198754; }
  .status-7  { background-color: #ffc107; color: #000 !important; }

  /* ✅ FULL PAGE LOADER OVERLAY */
  #autoLoadOverlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.85);
    z-index: 99999;
  }
  #autoLoadOverlay.show {
    display: block;
  }
  #autoLoadOverlay .loader-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
  }
  .loader-spinner {
    display: inline-block;
    width: 56px;
    height: 56px;
    border: 6px solid #dee2e6;
    border-top-color: #007bff;
    border-radius: 50%;
    animation: loaderSpin 0.8s linear infinite;
  }
  .loader-text {
    display: block;
    margin-top: 14px;
    font-size: 13px;
    font-weight: 600;
    color: #50607b;
    letter-spacing: 0.5px;
  }
  @keyframes loaderSpin {
    to { transform: rotate(360deg); }
  }
</style>

<!-- ✅ FULL PAGE LOADER OVERLAY -->
<div id="autoLoadOverlay">
  <div class="loader-center">
    <div class="loader-spinner"></div>
    <span class="loader-text" id="loader-message">Loading, please wait...</span>
  </div>
</div>

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
                <li class="breadcrumb-item active" aria-current="page"><b>Quotation</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="item_type">
                    <label for="item_type" class="control-label"><small class="req text-danger">* </small> Item / Service</label>
                    <select name="item_type" id="item_type" class="form-control selectpicker" data-live-search="true" app-field-label="Item / Service" required onchange="getCustomDropdownList('item_type', this.value, 'item_category');">
                      <option value="" selected>None selected</option>
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
                  <div class="form-group" app-field-wrapper="item_category">
                    <label for="item_category" class="control-label"><small class="req text-danger">* </small> Category</label>
                    <select name="item_category" id="item_category" class="form-control selectpicker" data-live-search="true" app-field-label="Category" required onchange="getNextQuotationNo();">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="quotation_no">
                    <label for="quotation_no" class="control-label"><small class="req text-danger">* </small> Quotation No</label>
                    <input type="text" name="quotation_no" id="quotation_no" class="form-control" app-field-label="Quotation No" required readonly placeholder="Auto Generated">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="quotation_date">
                    <?= render_date_input('quotation_date', 'Quotation Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="purchase_location">
                    <label for="purchase_location" class="control-label"><small class="req text-danger">* </small> Purchase Location</label>
                    <select name="purchase_location" id="purchase_location" class="form-control selectpicker" data-live-search="true" app-field-label="Purchase Location" required onchange="checkHeaderFieldsAndToggleItem();">
                      <option value="" selected>None selected</option>
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
                  <div class="form-group" app-field-wrapper="freight_terms">
                    <label for="freight_terms" class="control-label"><small class="req text-danger">* </small> Freight Terms</label>
                    <select name="freight_terms" id="freight_terms" class="form-control selectpicker" data-live-search="true" app-field-label="Freight Terms" required onchange="checkHeaderFieldsAndToggleItem();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($FreightTerms)) :
                        foreach ($FreightTerms as $value) :
                          echo '<option value="' . $value['Id'] . '">' . $value['FreightTerms'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_id">
                    <label for="vendor_id" class="control-label"><small class="req text-danger">* </small> Vendor Name</label>
                    <select name="vendor_id" id="vendor_id" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor Name" required onchange="getVendorDetailsLocation();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($vendor_list)) :
                        foreach ($vendor_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' - ' . $value['billing_state'] . '' . ' (' . $value['AccountID'] . ')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                    <input type="hidden" name="vendor_gst_no" id="vendor_gst_no" class="form-control" readonly>
                    <input type="hidden" name="vendor_country" id="vendor_country" class="form-control" readonly>
                    <input type="hidden" name="vendor_state" id="vendor_state" class="form-control" readonly>
                    <input type="hidden" name="vendor_address" id="vendor_address" class="form-control" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_location">
                    <label for="vendor_location" class="control-label"><small class="req text-danger">* </small> Vendor Location</label>
                    <select name="vendor_location" id="vendor_location" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor Location" required onchange="checkHeaderFieldsAndToggleItem();">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="broker_id">
                    <label for="broker_id" class="control-label">Broker Name</label>
                    <select name="broker_id" id="broker_id" class="form-control selectpicker" data-live-search="true" app-field-label="Broker Name">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="delivery_from">
                    <?= render_date_input('delivery_from', 'Delivery From', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="delivery_to">
                    <?= render_date_input('delivery_to', 'Delivery To', date('d/m/Y', strtotime('+10 days')), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="payment_terms">
                    <label for="payment_terms" class="control-label"><small class="req text-danger">* </small> Payment Terms</label>
                    <select name="payment_terms" id="payment_terms" class="form-control selectpicker" data-live-search="true" app-field-label="Payment Terms" required onchange="checkHeaderFieldsAndToggleItem();">
                      <option value="" selected>None selected</option>
                      <option value="Credit">Credit</option>
                      <option value="Advance">Advance</option>
                      <option value="OnDelivery">On Delivery</option>
                    </select>
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
                        <th>Item Name</th>
                        <th>HSN Code</th>
                        <th>UOM</th>
                        <th>Unit Wt (Kg)</th>
                        <th>Min Qty</th>
                        <th>Max Qty</th>
                        <th>Disc Amt/Unit</th>
                        <th>Unit Rate</th>
                        <th>GST %</th>
                        <th>Amount</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td style="width: 250px;">
                          <select id="item_id" class="form-control fixed_row dynamic_row dynamic_item selectpicker" data-live-search="true" app-field-label="Item Name" onchange="getItemDetails(this.value, '');" disabled>
                            <option value="" selected disabled>Select Item</option>
                          </select>
                        </td>
                        <td><input type="text" id="hsn_code" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="text" id="uom" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="unit_weight" class="form-control fixed_row" min="0" step="0.01" readonly tabindex="-1"></td>
                        <td><input type="tel" id="min_qty" class="form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');"></td>
                        <td><input type="tel" id="max_qty" class="form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');"></td>
                        <td><input type="tel" id="disc_amt" class="form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');"></td>
                        <td><input type="tel" id="unit_rate" class="form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');"></td>
                        <td><input type="tel" id="gst" class="form-control fixed_row" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
                        <td><input type="tel" id="amount" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td>
                          <button type="button" class="btn btn-success" id="addRowBtn" onclick="addRow();" disabled>
                            <i class="fa fa-plus"></i>
                          </button>
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

                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row"><label>Total Wt (Kg):</label><div class="total-display" id="total_weight_display">0.00</div></div>
                      <div class="total-label-row"><label>Total Qty:</label><div class="total-display" id="total_qty_display">0.00</div></div>
                      <div class="total-label-row"><label>Item Total:</label><div class="total-display" id="item_total_amt_display">0.00</div></div>
                      <div class="total-label-row"><label>Total Disc:</label><div class="total-display" id="disc_amt_display">0.00</div></div>
                      <div class="total-label-row"><label>Taxable Amt:</label><div class="total-display" id="taxable_amt_display">0.00</div></div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row"><label>CGST Amt:</label><div class="total-display" id="cgst_amt_display">0.00</div></div>
                      <div class="total-label-row"><label>SGST Amt:</label><div class="total-display" id="sgst_amt_display">0.00</div></div>
                      <div class="total-label-row"><label>IGST Amt:</label><div class="total-display" id="igst_amt_display">0.00</div></div>
                      <div class="total-label-row"><label>Round Off:</label><div class="total-display" id="round_off_amt_display">0.00</div></div>
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <div class="total-label-row" style="border-top: 2px solid #007bff; padding-top: 12px; margin-top: 8px; background-color: #f0f8ff; padding: 12px; border-radius: 4px;">
                        <label style="font-size: 16px; color: #007bff; font-weight: 700; padding-right: 15px;">Net Amt:</label>
                        <div class="total-display highlight" id="net_amt_display" style="font-size: 16px;">0.00</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('PurchaseQuotation', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="button" class="btn btn-warning printBtn" id="printBtn" onclick="printQuotation();" style="display: none; background-color: #000; color: #fff; border-color: #000;"><i class="fa fa-print"></i> Print PDF</button>
                  <button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('PurchaseQuotation', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
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

<!-- List Modal -->
<div class="modal fade" id="ListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="width: 80vw;">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Purchase Quotation List</h4>
      </div>
      <div class="modal-body" style="padding: 5px 5px !important">
        <form action="" method="post" id="filter_list_form">
          <div class="row">
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="from_date">
                <?= render_date_input('from_date', 'From Date', date('01/m/Y'), []); ?>
              </div>
            </div>
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="to_date">
                <?= render_date_input('to_date', 'To Date', date('d/m/Y'), []); ?>
              </div>
            </div>
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="category_id">
                <label for="category_id" class="control-label">Item Category</label>
                <select name="category_id" id="category_id" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Category" onchange="resetForm();">
                  <option value="" selected>All</option>
                  <?php
                  if (!empty($quotation_category)) :
                    foreach ($quotation_category as $value) :
                      echo '<option value="' . $value['id'] . '">' . $value['CategoryCode'] . '</option>';
                    endforeach;
                  endif;
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-2 mbot5">
              <div class="form-group">
                <label class="control-label">Purchase Location</label>
                <select name="filter_location_id" id="filter_location_id" class="form-control selectpicker" data-live-search="true">
                  <option value="">All</option>
                  <?php
                  if (!empty($purchaselocation)):
                    foreach ($purchaselocation as $value):
                      echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                    endforeach;
                  endif;
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-3 mbot5" style="padding-top: 20px;">
              <input type="hidden" name="vendor_id">
              <input type="hidden" name="broker_id">
              <input type="hidden" name="status">
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
                <th class="sortable">Quotation No</th>
                <th class="sortable">Purchase Location</th>
                <th class="sortable">Quotation Date</th>
                <th class="sortable">Category</th>
                <th class="sortable">Vendor</th>
                <th class="sortable">Total Wt</th>
                <th class="sortable">Total Qty</th>
                <th class="sortable">Item Total</th>
                <th class="sortable">Total Disc</th>
                <th class="sortable">Taxable Amt</th>
                <th class="sortable">CGST Amt</th>
                <th class="sortable">SGST Amt</th>
                <th class="sortable">IGST Amt</th>
                <th class="sortable">Net Amt</th>
                <th class="sortable">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($quotation_list)):
                foreach ($quotation_list as $key => $value):
              ?>
                <tr class="get_Details" data-id="<?= $value["id"]; ?>" onclick="getDetails('<?= $value["QuotatioonID"]; ?>', this)">
                  <td><?= $value["QuotatioonID"]; ?></td>
                  <td><?= date('d/m/Y', strtotime($value["TransDate"])); ?></td>
                  <td><?= $value["company"]; ?></td>
                  <td><?= $value["TotalWeight"]; ?></td>
                  <td><?= $value["NetAmt"]; ?></td>
                  <td class="text-center"><?= getStatusBadgePhp($value["Status"]); ?></td>
                </tr>
              <?php
                endforeach;
              endif;
              ?>
            </tbody>
          </table>
        </div>
        <br>
      </div>
    </div>
  </div>
</div>

<?php
function getStatusBadgePhp($status) {
  $map = [
    '1' => ['label' => 'Pending',             'class' => 'status-1'],
    '2' => ['label' => 'Cancel',              'class' => 'status-2'],
    '3' => ['label' => 'Expired',             'class' => 'status-3'],
    '4' => ['label' => 'Approved',            'class' => 'status-4'],
    '5' => ['label' => 'In-Progress',         'class' => 'status-5'],
    '6' => ['label' => 'Completed',           'class' => 'status-6'],
    '7' => ['label' => 'Partially Completed', 'class' => 'status-7'],
  ];
  $s = $map[(string)$status] ?? null;
  if (!$s) return '<span class="status-badge status-1">Unknown</span>';
  return '<span class="status-badge ' . $s['class'] . '">' . $s['label'] . '</span>';
}
?>

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

  // =============================================
  // ✅ LOADER SHOW / HIDE FUNCTIONS
  // =============================================
  function showLoader(msg) {
    $('#loader-message').text(msg || 'Loading, please wait...');
    $('#autoLoadOverlay').addClass('show');
  }
  function hideLoader() {
    $('#autoLoadOverlay').removeClass('show');
  }

  // ========== STATUS LABEL HELPER (JS) ==========
  function getStatusLabel(status) {
    var map = {
      '1': 'Pending', '2': 'Cancel', '3': 'Expired',
      '4': 'Approved', '5': 'In-Progress', '6': 'Completed',
      '7': 'Partially Completed'
    };
    return map[String(status)] || 'Unknown';
  }

  // ========== HEADER FIELDS CHECK ==========
  var requiredHeaderFields = [
    'item_type', 'item_category', 'quotation_no', 'quotation_date',
    'purchase_location', 'freight_terms', 'vendor_id', 'vendor_location', 'payment_terms'
  ];

  function checkHeaderFieldsAndToggleItem() {
    var allFilled = true;
    for (var i = 0; i < requiredHeaderFields.length; i++) {
      var val = $('#' + requiredHeaderFields[i]).val();
      if (!val || val.toString().trim() === '') { allFilled = false; break; }
    }
    if (allFilled) {
      $('#item_id').prop('disabled', false);
      $('#addRowBtn').prop('disabled', false);
      $('#addRowBtn').removeClass('btn-add-row-disabled');
    } else {
      $('#item_id').prop('disabled', true);
      $('#addRowBtn').prop('disabled', true);
      $('#addRowBtn').addClass('btn-add-row-disabled');
    }
    $('.selectpicker').selectpicker('refresh');
  }

  // ========== DOCUMENT READY ==========
  $(document).ready(function() {
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

    $('#quotation_date').datetimepicker({
      format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false,
      onChangeDateTime: function() { checkHeaderFieldsAndToggleItem(); }
    });
    $('#delivery_from').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });

    var fy_end_year = "20" + (parseInt(fin_y) + 1);
    var fyEndDate = new Date(fy_end_year + '/03/31');
    $('#delivery_to').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: fyEndDate, timepicker: false });
    $('#from_date').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });
    $('#to_date').datetimepicker({ format: 'd/m/Y', minDate: minStartDate, maxDate: maxEndDate, timepicker: false });

    checkHeaderFieldsAndToggleItem();

    setTimeout(() => { $('#filter_list_form').submit(); }, 800);

    // ✅ URL  quotation_id   auto-load
    var urlParams = new URLSearchParams(window.location.search);
    var quotationId = urlParams.get('quotation_id');
    if (quotationId) {
      showLoader('Loading Quotation...');
      setTimeout(function() {
        loadQuotationByQuotationNo(quotationId);
      }, 500);
    }
  });

  // ========== URL  Quotation Load ==========
  function loadQuotationByQuotationNo(quotationNo) {
    showLoader('Loading Quotation...');
    var dummyRow = $('<tr data-id="">');
    getDetails(quotationNo, dummyRow[0]);
    if (window.history.replaceState) {
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  }

  // ========== RESET FORM ==========
  function ResetForm() {
    $('#main_save_form')[0].reset();
    $('#form_mode').val('add');
    $('#update_id').val('');
    $('.updateBtn').hide();
    $('.saveBtn').show();
    $('.selectpicker').selectpicker('refresh');
    $('#items_body').html('');
    $('.total-display').text('0.00');
    $('#row_id').val(0);
    $('#item_id').prop('disabled', true);
    $('#addRowBtn').prop('disabled', true);
    $('#addRowBtn').addClass('btn-add-row-disabled');
    $('#printBtn').hide();
    $('.selectpicker').selectpicker('refresh');
  }

  // ========== INPUT SANITIZE ==========
  $(document).on('input', 'input[type="tel"]', function() {
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
  });

  // ========== VALIDATE FIELDS ==========
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

  // ========== ADD ROW ==========
  function addRow(row = null) {
    var allFilled = true;
    for (var i = 0; i < requiredHeaderFields.length; i++) {
      var val = $('#' + requiredHeaderFields[i]).val();
      if (!val || val.toString().trim() === '') { allFilled = false; break; }
    }
    if (!allFilled && row == null) {
      alert_float('warning', 'Please fill all header fields before adding items.');
      return false;
    }

    $('#item_id').focus();
    var row_id = $('#row_id').val();
    var next_id = parseInt(row_id) + 1;

    if (row == null) {
      let fields = ['item_id', 'min_qty', 'max_qty', 'disc_amt', 'unit_rate'];
      let data = validate_fields(fields);
      if (data === false) return false;
      var row_btn = `<button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove(); calculateTotals();" style="float:right; padding: 2px; width: 30px;"><i class="fa fa-xmark"></i></button>`;
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
        <td><input type="text" name="hsn_code[]" id="hsn_code${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="text" name="uom[]" id="uom${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="unit_weight[]" id="unit_weight${next_id}" class="form-control unit-weight dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="min_qty[]" id="min_qty${next_id}" class="form-control min-qty dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="max_qty[]" id="max_qty${next_id}" class="form-control max-qty dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="disc_amt[]" id="disc_amt${next_id}" class="form-control disc-amt dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="unit_rate[]" id="unit_rate${next_id}" class="form-control unit-rate dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="gst[]" id="gst${next_id}" class="form-control gst-percent dynamic_row${next_id}" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="amount[]" id="amount${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td>${row_btn}</td>
      </tr>
    `);

    if (row == null) {
      $('#item_id' + next_id).val($('#item_id').val());
      $('#hsn_code' + next_id).val($('#hsn_code').val());
      $('#uom' + next_id).val($('#uom').val());
      $('#unit_weight' + next_id).val($('#unit_weight').val());
      $('#min_qty' + next_id).val($('#min_qty').val());
      $('#max_qty' + next_id).val($('#max_qty').val());
      $('#disc_amt' + next_id).val($('#disc_amt').val());
      $('#unit_rate' + next_id).val($('#unit_rate').val());
      $('#gst' + next_id).val($('#gst').val());
      $('#amount' + next_id).val($('#amount').val());
      $('.fixed_row').val('');
      calculateAmount(next_id);
    }

    $('#row_id').val(next_id);
    $('.selectpicker').selectpicker('refresh');
    checkHeaderFieldsAndToggleItem();
  }

  // ========== CALCULATE AMOUNT ==========
  function calculateAmount(row) {
    var minQty = parseFloat($('#min_qty' + row).val()) || 0;
    var unitRate = parseFloat($('#unit_rate' + row).val()) || 0;
    var discAmt = parseFloat($('#disc_amt' + row).val()) || 0;
    var gstPercent = parseFloat($('#gst' + row).val()) || 0;
    $('#max_qty' + row).val(minQty + 2);

    var taxableAmt = (unitRate - discAmt) * minQty;
    var gstAmt = taxableAmt * (gstPercent / 100);
    var netAmt = taxableAmt + gstAmt;
    if (isNaN(netAmt) || netAmt < 0) netAmt = 0;

    $('#amount' + row).val(netAmt.toFixed(2));
    calculateTotals();

    if ((row == '' || row == null) && minQty > 0 && unitRate > 0 && discAmt >= 0 && gstPercent >= 0) {
      addRow();
    }
  }

  // ========== CALCULATE TOTALS ==========
  function calculateTotals() {
    var totalWeight = 0, totalQty = 0, itemTotalAmt = 0, totalDiscAmt = 0, totalTaxableAmt = 0, totalGstAmt = 0;

    $('#items_body tr').each(function() {
      var row = $(this);
      var qty        = parseFloat(row.find('.min-qty').val()) || 0;
      var weight     = parseFloat(row.find('.unit-weight').val()) || 0;
      var rate       = parseFloat(row.find('.unit-rate').val()) || 0;
      var discAmt    = parseFloat(row.find('.disc-amt').val()) || 0;
      var gstPercent = parseFloat(row.find('.gst-percent').val()) || 0;

      var rowTaxableAmt = (rate - discAmt) * qty;
      var rowGstAmt     = rowTaxableAmt * (gstPercent / 100);

      totalWeight     += weight * qty;
      totalQty        += qty;
      itemTotalAmt    += rate * qty;
      totalDiscAmt    += discAmt * qty;
      totalTaxableAmt += rowTaxableAmt;
      totalGstAmt     += rowGstAmt;
    });

    $('#total_weight_display').text(totalWeight.toFixed(2));
    $('#total_qty_display').text(totalQty.toFixed(2));
    $('#item_total_amt_display').text(itemTotalAmt.toFixed(2));
    $('#disc_amt_display').text(totalDiscAmt.toFixed(2));
    $('#taxable_amt_display').text(totalTaxableAmt.toFixed(2));

    var vendorState = $('#vendor_state').val().trim().toUpperCase();
    var cgstAmt = 0, sgstAmt = 0, igstAmt = 0;

    if (vendorState === '<?= strtoupper($company_detail->state ?? ''); ?>') {
      cgstAmt = totalGstAmt / 2;
      sgstAmt = totalGstAmt / 2;
    } else {
      igstAmt = totalGstAmt;
    }

    $('#cgst_amt_display').text(cgstAmt.toFixed(2));
    $('#sgst_amt_display').text(sgstAmt.toFixed(2));
    $('#igst_amt_display').text(igstAmt.toFixed(2));

    var netAmtBeforeRound = totalTaxableAmt + totalGstAmt;
    var netAmtRounded = Math.round(netAmtBeforeRound);
    var roundOffAmt = netAmtRounded - netAmtBeforeRound;

    $('#round_off_amt_display').text(roundOffAmt.toFixed(2));
    $('#net_amt_display').text(netAmtRounded.toFixed(2));

    $('#total_weight_hidden').val(totalWeight.toFixed(2));
    $('#total_qty_hidden').val(totalQty.toFixed(2));
    $('#item_total_amt_hidden').val(itemTotalAmt.toFixed(2));
    $('#disc_amt_hidden').val(totalDiscAmt.toFixed(2));
    $('#taxable_amt_hidden').val(totalTaxableAmt.toFixed(2));
    $('#cgst_amt_hidden').val(cgstAmt.toFixed(2));
    $('#sgst_amt_hidden').val(sgstAmt.toFixed(2));
    $('#igst_amt_hidden').val(igstAmt.toFixed(2));
    $('#round_off_amt_hidden').val(roundOffAmt.toFixed(2));
    $('#net_amt_hidden').val(netAmtRounded.toFixed(2));
  }

  function get_required_fields(form_id) {
    let fields = [];
    $('#' + form_id + ' [required]').each(function() { fields.push($(this).attr('id')); });
    return fields;
  }

  // ========== GET CUSTOM DROPDOWN LIST ==========
  function getCustomDropdownList(parent_id, parent_value, child_id, selected_value = null, callback = null) {
    $.ajax({
      url: "<?= admin_url(); ?>ItemMaster/GetCustomDropdownList",
      type: 'POST',
      dataType: 'json',
      data: { parent_id: parent_id, parent_value: parent_value, child_id: child_id },
      success: function(response) {
        if (response.success == true) {
          let html = `<option value="" selected disabled>None selected</option>`;
          for (var i = 0; i < response.data.length; i++) {
            html += `<option value="${response.data[i].id}">${response.data[i].name}</option>`;
          }
          $('#' + child_id).html(html);
          if (selected_value) $('#' + child_id).val(selected_value);
          $('.selectpicker').selectpicker('refresh');
          if (callback) callback();
        } else {
          $('#' + child_id).html('<option value="" selected disabled>None selected</option>');
          $('.selectpicker').selectpicker('refresh');
        }
        checkHeaderFieldsAndToggleItem();
      }
    });
  }

  // ========== GET NEXT QUOTATION NO ==========
  function getNextQuotationNo(callback = null) {
    var categoryId = $('#item_category').val();
    if (!categoryId) { $('#quotation_no').val(''); checkHeaderFieldsAndToggleItem(); return; }
    $.ajax({
      url: '<?= admin_url('purchase/Quotation/getNextQuotationNo'); ?>',
      type: 'POST',
      data: { category_id: categoryId },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          if ($('#form_mode').val() == 'add') { $('#quotation_no').val(response.quote_no); }
          var html = '<option value="" selected disabled>Select Item</option>';
          $.each(response.item_list, function(index, val) {
            html += '<option value="' + val.ItemId + '">' + val.ItemName + '</option>';
          });
          $('#item_id').html(html);
          $('.selectpicker').selectpicker('refresh');
        } else { $('#quotation_no').val(''); }
        if (callback) callback();
        checkHeaderFieldsAndToggleItem();
      },
      error: function() { $('#quotation_no').val(''); checkHeaderFieldsAndToggleItem(); }
    });
  }

  // ========== GET VENDOR DETAILS & LOCATION ==========
  function getVendorDetailsLocation(callback = null) {
    var vendorId = $('#vendor_id').val();
    if (!vendorId) {
      $('#vendor_location').html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      checkHeaderFieldsAndToggleItem();
      return;
    }
    $.ajax({
      url: '<?= admin_url('purchase/Quotation/getVendorDetailsLocation'); ?>',
      type: 'POST',
      data: { vendor_id: vendorId },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var data = response.data;
          $('#vendor_gst_no').val(data.GSTIN || '');
          $('#vendor_country').val(data.country_name || '');
          $('#vendor_state').val(data.billing_state || '');
          $('#vendor_address').val(data.billing_address || '');

          var html = '<option value="" selected disabled>None selected</option>';
          $.each(response.location, function(index, loc) {
            if (loc.id == null || loc.id == '') return;
            html += '<option value="' + loc.id + '">' + loc.city + '</option>';
          });
          $('#vendor_location').html(html);

          html = '<option value="" selected disabled>None selected</option>';
          $.each(response.broker_list, function(index, loc) {
            if (loc.AccountID == null || loc.AccountID == '') return;
            html += `<option value="${loc.AccountID}">${loc.company} - ${loc.billing_state} (${loc.AccountID})</option>`;
          });
          $('#broker_id').html(html);

          $('.selectpicker').selectpicker('refresh');
          calculateTotals();
          if (callback) callback();
        }
        checkHeaderFieldsAndToggleItem();
      }
    });
  }

  // ========== GET ITEM DETAILS ==========
  function getItemDetails(itemId, id = '', extraData = []) {
    var isDuplicate = false;
    $('.dynamic_item').not('#item_id' + id).each(function() {
      if ($(this).val() == itemId && itemId != '') { isDuplicate = true; return false; }
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
      url: '<?= admin_url('purchase/GetItemDetails'); ?>',
      type: 'POST',
      data: { item_id: itemId },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success' && response.data) {
          var data = response.data;
          $('#hsn_code' + id).val(data.hsn_code || '');
          $('#uom' + id).val(data.unit || '');
          $('#unit_weight' + id).val(Number(data.UnitWeight) || 0);
          if (extraData && extraData.gst != null) {
            $('#gst' + id).val(extraData.gst);
          } else {
            $('#gst' + id).val(Number(data.tax) || 0);
          }
          $('#min_qty' + id).focus();
        } else {
          $('.fixed_row').val('');
          $('.selectpicker').selectpicker('refresh');
        }
        calculateTotals();
      },
      error: function(xhr, status, err) { console.log('Error fetching item details:', err); }
    });
  }

  // ========== MAIN FORM SUBMIT ==========
  $('#main_save_form').on('submit', function(e) {
    e.preventDefault();
    let form_mode = $('#form_mode').val();
    let required_fields = get_required_fields('main_save_form');
    let validated = validate_fields(required_fields);
    if (validated === false) return;

    var form_data = new FormData(this);
    form_data.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );
    if (form_mode == 'edit') { form_data.append('update_id', $('#update_id').val()); }

    $.ajax({
      url: "<?= admin_url(); ?>purchase/Quotation/SaveQuotation",
      method: "POST",
      dataType: "JSON",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function() { $('button[type=submit]').attr('disabled', true); },
      complete: function() { $('button[type=submit]').attr('disabled', false); },
      success: function(response) {
        if (response.success == true) {
          alert_float('success', response.message);
          let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getDetails('${response.data.QuotatioonID}', this)">
            <td class="text-center">${response.data.QuotatioonID}</td>
            <td>${moment(response.data.TransDate).format('DD/MM/YYYY')}</td>
            <td>${response.data.category_name}</td>
            <td class="fixed-td" title="${response.data.AccountID} - ${response.data.company}">${response.data.AccountID} - ${response.data.company}</td>
            <td class="text-center">${Number(response.data.TotalWeight / 100) || '-'}</td>
            <td class="text-center">${Number(response.data.TotalQuantity) || '-'}</td>
            <td class="text-center">${Number(response.data.ItemAmt) || '-'}</td>
            <td class="text-center">${Number(response.data.DiscAmt) || '-'}</td>
            <td class="text-center">${Number(response.data.TaxableAmt) || '-'}</td>
            <td class="text-center">${Number(response.data.CGSTAmt) || '-'}</td>
            <td class="text-center">${Number(response.data.SGSTAmt) || '-'}</td>
            <td class="text-center">${Number(response.data.IGSTAmt) || '-'}</td>
            <td class="text-center">${Number(response.data.RoundOffAmt) || '-'}</td>
            <td class="text-center">${Number(response.data.NetAmt) || '-'}</td>
            <td class="text-center">${getStatusLabel(response.data.Status)}</td>
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

  // =============================================
  // ✅ GET DETAILS - LOADER 
  // =============================================
  function getDetails(QuotatioonID, row) {
    let $row = $(row);
    if ($row.hasClass('processing')) return;
    $row.addClass('processing');

    ResetForm();

    // ✅ AJAX  show 
    showLoader('Fetching quotation details...');

    $.ajax({
      url: "<?= admin_url(); ?>purchase/Quotation/GetQuotationDetails",
      method: "POST",
      dataType: "JSON",
      data: { quotation_no: QuotatioonID },
      complete: function() {
        // ✅ AJAX complete  processing class 
        $row.removeClass('processing');
      },
      success: function(response) {
        if (response.success == true) {
          let d = response.data;
          $('#update_id').val(d.id);
          $('#item_type').val(d.ItemType);

          getCustomDropdownList('item_type', d.ItemType, 'item_category', d.ItemCategory, function() {
            getNextQuotationNo(function() {
              let history = response.data.history;
              for (var i = 0; i < history.length; i++) {
                addRow(2);
                $('#item_uid' + (i + 1)).val(history[i].id);
                $('#item_id' + (i + 1)).val(history[i].ItemID);
                let gstPec = parseFloat(history[i].cgst) + parseFloat(history[i].sgst) + parseFloat(history[i].igst);
                getItemDetails(history[i].ItemID, (i + 1), { 'gst': gstPec });
                $('#min_qty' + (i + 1)).val(Number(history[i].OrderQty));
                $('#max_qty' + (i + 1)).val(Number(history[i].OrderQty));
                $('#unit_rate' + (i + 1)).val(Number(history[i].BasicRate));
                $('#disc_amt' + (i + 1)).val(Number(history[i].DiscAmt));
                $('#amount' + (i + 1)).val(Number(history[i].NetOrderAmt));
                $('.selectpicker').selectpicker('refresh');
                calculateAmount(i + 1);
              }
              // ✅   loader hide 
              hideLoader();
            });
          });

          $('#quotation_no').val(d.QuotatioonID);
          $('#quotation_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
          $('#purchase_location').val(d.PurchaseLocation);
          $('#vendor_id').val(d.AccountID);

          getVendorDetailsLocation(function() {
            $('#vendor_location').val(d.DeliveryLocation);
            $('#broker_id').val(d.BrokerID);
            checkHeaderFieldsAndToggleItem();
          });

          $('#delivery_from').val(moment(d.DeliveryFrom).format('DD/MM/YYYY'));
          $('#delivery_to').val(moment(d.DeliveryTo).format('DD/MM/YYYY'));
          $('#payment_terms').val(d.PaymentTerms);
          $('#freight_terms').val(d.FreightTerms);

          $('.selectpicker').selectpicker('refresh');
          $('#form_mode').val('edit');
          $('.saveBtn').hide();

          if (parseInt(d.Status) === 6 || parseInt(d.Status) === 7 || parseInt(d.Status) === 2) {
            $('.updateBtn').show().prop('disabled', true).addClass('btn-add-row-disabled');
            alert_float('warning', 'This record is locked. Update not allowed.');
          } else {
            $('.updateBtn').show().prop('disabled', false).removeClass('btn-add-row-disabled');
          }

          $('#printBtn').show().data('print-id', d.id);
          $('#ListModal').modal('hide');
          checkHeaderFieldsAndToggleItem();

        } else {
          // ✅ Error   loader hide 
          hideLoader();
          alert_float('warning', response.message);
        }
      },
      error: function() {
        hideLoader();
        $row.removeClass('processing');
        alert_float('danger', 'Error fetching quotation details.');
      }
    });
  }

  // ========== FILTER LIST FORM SUBMIT ==========
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
        url: "<?= admin_url('purchase/Quotation/ListFilter') ?>",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function(res) {
          let json = JSON.parse(res);
          if (!json.success) {
            $('#searchBtn').prop('disabled', false);
            if (offset === 0) {
              $('#table_ListModal tbody').html('<tr><td colspan="15" class="text-center">No Data Found</td></tr>');
            }
            return;
          }
          if (offset === 0) { totalRecords = parseInt(json.total) || 0; }
          if (json.rows && json.rows.length > 0) {
            appendRows(json.rows);
            loadedRecords += json.rows.length;
            offset += limit;
          }
          updateProgress(loadedRecords, totalRecords);
          if (loadedRecords >= totalRecords) {
            $('#searchBtn').prop('disabled', false);
            $('#fetchProgress').css('width', '0%');
            return;
          }
          fetchChunk();
        }
      });
    }
    fetchChunk();
  });

  // ========== APPEND ROWS ==========
  function appendRows(rows) {
    let html = '';
    rows.forEach(function(row) {
      html += `<tr class="get_Details" data-id="${row.id}" onclick="getDetails('${row.QuotatioonID}', this)">
        <td class="text-center">${row.QuotatioonID}</td>
        <td class="text-center">${row.location_name}</td>
        <td>${moment(row.TransDate).format('DD/MM/YYYY')}</td>
        <td>${row.category_name}</td>
        <td class="fixed-td" title="${row.vendor_name} - ${row.AccountID}">${row.vendor_name} - ${row.AccountID}</td>
        <td class="text-center">${Number(row.TotalQuantity) || '-'}</td>
        <td class="text-center">${Number(row.TotalWeight / 100).toFixed(3) || '-'}</td>
        <td class="text-center">${Number(row.ItemAmt) || '-'}</td>
        <td class="text-center">${Number(row.DiscAmt) || '-'}</td>
        <td class="text-center">${Number(row.TaxableAmt) || '-'}</td>
        <td class="text-center">${Number(row.CGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.SGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.IGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.NetAmt) || '-'}</td>
        <td class="text-center">${getStatusLabel(row.Status)}</td>
      </tr>`;
    });
    $('#table_ListModal tbody').append(html);
  }

  function updateProgress(loaded, total) {
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%');
  }
</script>

<script>
  // ========== TABLE SORT ==========
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
      if ($.isNumeric(valA) && $.isNumeric(valB)) { return ascending ? valA - valB : valB - valA; }
      else { return ascending ? valA.localeCompare(valB) : valB.localeCompare(valA); }
    });
    table.append(rows);
  });

  // ========== TABLE SEARCH ==========
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
        if (txtValue.toUpperCase().indexOf(filter) > -1) { rowMatch = true; break; }
      }
      tr[i].style.display = rowMatch ? "" : "none";
    }
  }

  function printQuotation() {
    var quotation_no = $('#quotation_no').val();
    if (!quotation_no) { alert_float('warning', 'Quotation No not found!'); return; }
    var url = "<?= admin_url('purchase/Quotation/printquotationPdf/'); ?>" + quotation_no;
    window.open(url, '_blank');
  }
</script>