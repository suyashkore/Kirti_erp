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
                <li class="breadcrumb-item active text-capitalize"><b>Sales</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Sales Order</b></li>
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
                    <select name="item_category" id="item_category" class="form-control selectpicker" data-live-search="true" app-field-label="Category" required onchange="getNextSalesOrderNo();">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="OrderID">
                    <label for="OrderID" class="control-label"><small class="req text-danger">* </small> Order ID</label>
                    <input type="text" name="OrderID" id="OrderID" class="form-control" app-field-label="Order ID" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="order_date">
                    <?= render_date_input('order_date', 'Order Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="sales_location">
                    <label for="sales_location" class="control-label"><small class="req text-danger">* </small> Sales Location</label>
                    <select name="sales_location" id="sales_location" class="form-control selectpicker" data-live-search="true" app-field-label="Purchase Location" required>
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($saleslocation)) :
                        foreach ($saleslocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="customer_id">
                    <label for="customer_id" class="control-label"><small class="req text-danger">* </small> Customer Name</label>
                    <select name="customer_id" id="customer_id" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor Name" required onchange="getCustomerDetailsLocation();">
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
                    <input type="hidden" name="customer_address" id="customer_address" class="form-control" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="customer_location">
                    <label for="customer_location" class="control-label"><small class="req text-danger">* </small> Customer Location</label>
                    <select name="customer_location" id="customer_location" class="form-control selectpicker" data-live-search="true" app-field-label="Customer Location" required>
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="billing_state">
                    <label for="billing_state" class="control-label"><small class="req text-danger">* </small> Billing State</label>
                    <input type="text" name="billing_state" id="billing_state" class="form-control" app-field-label="Billing State" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="GSTIN">
                    <label for="GSTIN" class="control-label"><small class="req text-danger">* </small> GSTIN</label>
                    <input type="text" name="GSTIN" id="GSTIN" class="form-control" app-field-label="GSTIN" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="broker_id">
                    <label for="broker_id" class="control-label"><small class="req text-danger">* </small> Broker Name</label>
                    <select name="broker_id" id="broker_id" class="form-control selectpicker" data-live-search="true" app-field-label="Broker Name" required>
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="quotation_id">
                    <label for="quotation_id" class="control-label">Quotation List</label>
                    <select name="quotation_id" id="quotation_id" class="form-control selectpicker" data-live-search="true" app-field-label="Quotation List" onchange="getQuotationDetails(this.value)">
                      <option value="" selected>None selected</option>
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
                        <th>Unit Weight</th>
                        <th>Min Qty</th>
                        <th>Max Qty</th>
                        <th>Disc Amt</th>
                        <th>Unit Rate</th>
                        <th>GST %</th>
                        <th>Amount</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td style="width: 250px;">
                          <select id="item_id" class="form-control fixed_row dynamic_row dynamic_item selectpicker" data-live-search="true" app-field-label="Item Name" onchange="getItemDetails(this.value, '');">
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
                          <button type="button" class="btn btn-success" onclick="addRow();"><i class="fa fa-plus"></i></button>
                        </td>
                      </tr>
                    </thead>
                    <tbody id="items_body">

                    </tbody>
                  </table>
                </div>


                <!-- <div class="row"> -->

                <!-- LEFT SIDE (6 columns) -->
                <div class="col-md-6">
                  <div class="row">

                    <!-- Freight Terms -->
                    <div class="col-md-6 mbot5">
                      <div class="form-group" app-field-wrapper="freight_terms">
                        <label for="freight_terms" class="control-label">
                          <small class="req text-danger">* </small> Freight Terms
                        </label>
                        <select name="freight_terms" id="freight_terms"
                          class="form-control selectpicker"
                          data-live-search="true"
                          app-field-label="Freight Terms"
                          required>
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

                    <!-- Payment Terms -->
                    <div class="col-md-6 mbot5">
                      <div class="form-group" app-field-wrapper="payment_terms">
                        <label for="payment_terms" class="control-label">
                          <small class="req text-danger">* </small> Payment Terms
                        </label>
                        <select name="payment_terms" id="payment_terms"
                          class="form-control selectpicker"
                          data-live-search="true"
                          required>
                          <option value="" selected>None selected</option>
                          <option value="Credit">Credit</option>
                          <option value="Advance">Advance</option>
                          <option value="OnDelivery">On Delivery</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 mbot5">
                      <div class="form-group" app-field-wrapper="delivery_from">
                        <?= render_date_input('delivery_from', 'Delivery From', date('d/m/Y'), []); ?>
                      </div>
                    </div>
                    <div class="col-md-6 mbot5">
                      <div class="form-group" app-field-wrapper="delivery_to">
                        <?= render_date_input('delivery_to', 'Delivery To', date('d/m/Y', strtotime('+10 days')), []); ?>
                      </div>
                    </div>

                  </div>
                </div>

                <div class="col-md-6"></div>
                <div class="col-md-6">
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

                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="button" class="btn btn-primary printBtn style="display: none;" onclick="printPurchaseOrderPdf();"><i class="fa fa-print"></i> Print PDF</button>
                  <script>
                    // Print PDF function
                    function printPurchaseOrderPdf() {
                      var OrderID = $('#OrderID').val();
                      if (!OrderID) {
                        alert_float('warning', 'Order ID not found!');
                        return;
                      }
                      var url = "<?= admin_url('SalesOrder/SalesOrderPrint/'); ?>" + OrderID;
                      window.open(url, '_blank');
                    }
                  </script>
                  <button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
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

<div class="modal fade" id="ListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">

    <div class="modal-content">
      <div class="modal-header custom-header">

        <div class="header-top">
          <h4 class="modal-title">Sale Order List</h4>
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
          <!-- <div class="col-md-2"> -->
          <!-- <div class="form-group" app-field-wrapper="customer_id"> -->
          <div class="filter-group">
            <label for="customer_id" class="control-label">Category</label>
            <!-- <select name="customer_id" id="customer_id" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Customer"> -->
            <select name="filter_category"
              id="filter_category"
              class="form-control selectpicker filterInput"
              data-live-search="true">

              <option value="" selected>None selected</option>
              <?php
              if (!empty($category_list)) :
                foreach ($category_list as $value) :
                  echo '<option value="' . $value['id'] . '">' . $value['CategoryName'] . ' </option>';
                endforeach;
              endif;
              ?>
            </select>
          </div>
          <!-- </div> -->

          <!-- Search Button -->
          <div class="filter-group" style="align-self:flex-end;">
            <button type="button" class="btn btn-success" id="searchBtn">
              <i class="fa fa-list"></i> Show
            </button>

          </div>

        </div>

      </div>



      <div class="modal-body" style="padding:0px 5px !important">

        <div class="table-ListModal tableFixHead2" style="overflow-x:auto;">
          <table class="tree table table-striped table-bordered table-ListModal tableFixHead2" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortablePop">Quotation No</th>
                <th class="sortablePop">Quotation Date</th>
                <th class="sortablePop">Item Category</th>


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
              if (!empty($order_list)):
                foreach ($order_list as $key => $value):
              ?>
                  <tr class="get_Details" data-id="<?= $value["id"]; ?>" onclick="getDetails(<?= $value['id']; ?>)" data-category="<?= $value["ItemCategory"]; ?>">
                    <td><?= $value["OrderID"]; ?></td>
                    <td><?= date('d/m/Y', strtotime($value["TransDate"])); ?></td>
                    <td><?= $value["CategoryName"]; ?></td>


                    <td><?= $value["company"]; ?></td>
                    <td><?= $value["TotalWeight"]; ?></td>

                    <td><?= $value["TotalQuantity"]; ?></td>
                    <td><?= $value["ItemAmt"]; ?></td>
                    <td><?= $value["DiscAmt"]; ?></td>
                    <td><?= $value["TaxableAmt"]; ?></td>

                    <td><?= $value["CGSTAmt"]; ?></td>
                    <td><?= $value["SGSTAmt"]; ?></td>
                    <td><?= $value["IGSTAmt"]; ?></td>
                    <td><?= $value["RoundOffAmt"]; ?></td>


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

<?php init_tail(); ?>
<script>
  $('.printBtn').hide();
  $('#searchBtn').on('click', function() {

    var fromDate = $('#from_date').val();
    var toDate = $('#to_date').val();
    var categoryId = $('#filter_category').val(); // FIXED

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

      var rowCategoryId = $(this).data('category'); // from data-category

      var dateMatch = (rowDate >= from && rowDate <= to);
      var categoryMatch = (!categoryId || categoryId == rowCategoryId);

      if (dateMatch && categoryMatch) {
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
  }

  function calculateAmount(row) {
    var minQty = parseFloat($('#min_qty' + row).val()) || 0;
    var unitRate = parseFloat($('#unit_rate' + row).val()) || 0;
    var discAmt = parseFloat($('#disc_amt' + row).val()) || 0;
    var gstPercent = parseFloat($('#gst' + row).val()) || 0;

    var taxableAmt = (unitRate - discAmt) * minQty;
    var gstAmt = taxableAmt * (gstPercent / 100);
    var netAmt = taxableAmt + gstAmt;
    if (isNaN(netAmt) || netAmt < 0) {
      netAmt = 0;
    }

    $('#amount' + row).val(netAmt.toFixed(2));
    calculateTotals();
    if ((row == '' || row == null) && minQty > 0 && unitRate > 0 && discAmt >= 0 && gstPercent >= 0) {
      addRow();
    }
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
      var qty = parseFloat(row.find('.min-qty').val()) || 0;
      var weight = parseFloat(row.find('.unit-weight').val()) || 0;
      var rate = parseFloat(row.find('.unit-rate').val()) || 0;
      var discAmt = parseFloat(row.find('.disc-amt').val()) || 0;
      var gstPercent = parseFloat(row.find('.gst-percent').val()) || 0;

      var rowTotalWeight = weight * qty;
      var rowItemTotal = rate * qty;
      var rowTotalDisc = discAmt * qty;
      var rowNetRate = rate - discAmt;
      var rowTaxableAmt = rowNetRate * qty;
      var rowGstAmt = rowTaxableAmt * (gstPercent / 100);

      totalWeight += rowTotalWeight;
      totalQty += qty;
      itemTotalAmt += rowItemTotal;
      totalDiscAmt += rowTotalDisc;
      totalTaxableAmt += rowTaxableAmt;
      totalGstAmt += rowGstAmt;
    });

    // Update Display Labels
    $('#total_weight_display').text(totalWeight.toFixed(2));
    $('#total_qty_display').text(totalQty.toFixed(2));
    $('#item_total_amt_display').text(itemTotalAmt.toFixed(2));
    $('#disc_amt_display').text(totalDiscAmt.toFixed(2));
    $('#taxable_amt_display').text(totalTaxableAmt.toFixed(2));

    // GST Calculation based on State
    var customerState = $('#customer_state').val();

    // .trim().toUpperCase();
    var cgstAmt = 0,
      sgstAmt = 0,
      igstAmt = 0;
    var cgstPercent = 0,
      sgstPercent = 0,
      igstPercent = 0;
    var totalGstPercent = 0;
    if (totalTaxableAmt > 0) {
      $('#items_body tr').each(function() {
        var row = $(this);
        var qty = parseFloat(row.find('.min-qty').val()) || 0;
        var rate = parseFloat(row.find('.unit-rate').val()) || 0;
        var discAmt = parseFloat(row.find('.disc-amt').val()) || 0;
        var gstPercent = parseFloat(row.find('.gst-percent').val()) || 0;
        var rowNetRate = rate - discAmt;
        var rowTaxableAmt = rowNetRate * qty;
        totalGstPercent += (rowTaxableAmt / totalTaxableAmt) * gstPercent;
      });
    }
    totalGstPercent = Math.round(totalGstPercent * 100) / 100;
    if (customerState === '<?= $company_detail->state ?>') {
      cgstAmt = totalGstAmt / 2;
      sgstAmt = totalGstAmt / 2;
      cgstPercent = sgstPercent = totalGstPercent / 2;
    } else {
      igstAmt = totalGstAmt;
      igstPercent = totalGstPercent;
    }

    $('#cgst_amt_display').text(cgstAmt.toFixed(2));
    $('#sgst_amt_display').text(sgstAmt.toFixed(2));
    $('#igst_amt_display').text(igstAmt.toFixed(2));
    // $('#cgst_percent_label').text((cgstPercent ? cgstPercent.toFixed(2) : '0') + '%');
    // $('#sgst_percent_label').text((sgstPercent ? sgstPercent.toFixed(2) : '0') + '%');
    // $('#igst_percent_label').text((igstPercent ? igstPercent.toFixed(2) : '0') + '%');

    var netAmtBeforeRound = totalTaxableAmt + totalGstAmt;
    var netAmtRounded = Math.round(netAmtBeforeRound);
    var roundOffAmt = netAmtRounded - netAmtBeforeRound;

    $('#round_off_amt_display').text(roundOffAmt.toFixed(2));
    $('#net_amt_display').text(netAmtRounded.toFixed(2));

    // Update Hidden Inputs for Form Submission
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
    $('#' + form_id + ' [required]').each(function() {
      fields.push($(this).attr('id'));
    });
    return fields;
  }

  function getCustomDropdownList(parent_id, parent_value, child_id, selected_value = null, callback = null) {
    $.ajax({
      url: "<?= admin_url(); ?>ItemMaster/GetCustomDropdownList",
      type: 'POST',
      dataType: 'json',
      data: {
        parent_id: parent_id,
        parent_value: parent_value,
        child_id: child_id
      },
      success: function(response) {
        if (response.success == true) {
          let html = `<option value="" selected disabled>None selected</option>`;

          for (var i = 0; i < response.data.length; i++) {
            html += `<option value="${response.data[i].id}">${response.data[i].name}</option>`;
          }

          $('#' + child_id).html(html);
          if (selected_value) {
            $('#' + child_id).val(selected_value);
          }
          $('.selectpicker').selectpicker('refresh');
          if (callback) {
            callback();
          }
        } else {
          alert_float('danger', response.message);
        }
      }
    });
  }

  function getNextSalesOrderNo(callback = null) {
    var categoryId = $('#item_category').val();
    if (!categoryId) {
      $('#OrderID').val('');
      return;
    }
    $.ajax({
      url: '<?= admin_url('SalesOrder/getNextSalesOrderNo'); ?>',
      type: 'POST',
      data: {
        category_id: categoryId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          let form_mode = $('#form_mode').val();
          if (form_mode == 'add') {
            $('#OrderID').val(response.Order_no);
          }
          var html = '<option value="" selected disabled>Select Item</option>';
          $.each(response.item_list, function(index, val) {
            html += '<option value="' + val.ItemId + '">' + val.ItemName + '</option>';
          });
          $('#item_id').html(html);
          $('.selectpicker').selectpicker('refresh');
        } else {
          $('#OrderID').val('');
        }
        if (callback) {
          callback();
        }
      },
      error: function() {
        $('#OrderID').val('');
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
      url: '<?= admin_url('SalesOrder/getCustomerDetailsLocation'); ?>',
      type: 'POST',
      data: {
        customer_id: CustomerId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var data = response.data;
          $('#customer_gst_no').val(data.gst_no || '');
          $('#customer_country').val(data.country || '');
          $('#customer_state').val(data.state || '');
          $('#customer_address').val(data.address || '');

          $('#GSTIN').val(data.gst_no || '');
          $('#billing_state').val(data.state || '');



          var html = '<option value="" selected disabled>None selected</option>';
          $.each(response.location, function(index, loc) {
            if (loc.id == null || loc.id == '') return;
            html += '<option value="' + loc.id + '">' + loc.city + '</option>';
          });
          $('#customer_location').html(html);


          html = '<option value="" selected disabled>None selected</option>';
          $.each(response.broker_list, function(index, loc) {
            if (loc.AccountID == null || loc.AccountID == '') return;
            html += `<option value="${loc.AccountID}">${loc.company} (${loc.AccountID})</option>`;
          });
          $('#broker_id').html(html);


          html = '<option value="" selected disabled>None selected</option>';
          $.each(response.quotation_list, function(index, loc) {
            html += `<option value="${loc.QuotationID}">${loc.QuotationID}</option>`;
          });
          $('#quotation_id').html(html);

          // html = '<option value="" selected disabled>None selected</option>';
          // $.each(response.quotation_list, function(index, loc) {
          //   if (loc.QuotationID == null || loc.QuotationID == '') return;
          //   html += `<option value="${loc.QuotationID}">${loc.QuotationID}</option>`;
          // });

          // $('#quotation_id').html(html).prop('disabled', false);




          $('.selectpicker').selectpicker('refresh');
          calculateTotals();
          if (callback) {
            callback();
          }
        }
      }
    });
  }

  function getQuotationDetails(quotation_id) {

    if (!quotation_id) return;
    $.ajax({
      url: "<?= admin_url(); ?>SalesOrder/getQuotationDetails",
      type: "POST",
      data: {
        quotation_id: quotation_id,
      },
      dataType: "json",
      success: function(response) {

        if (!response.success) return;

        let items = response.items;
        let d = response.data;


        // STEP 1: Set Item Type
        $('#item_type').val(d.ItemType);
        $('.selectpicker').selectpicker('refresh');

        // STEP 2: Load Category
        getCustomDropdownList(
          'item_type',
          d.ItemType,
          'item_category',
          d.ItemCategory,
          function() {

            // STEP 3: After category load → load item dropdown
            getNextSalesOrderNo(function() {
              let OrderID = $('#quotation_id').val();
              if (!OrderID) return;

              $.ajax({
                url: "<?= admin_url(); ?>SalesOrder/GetHistoryDetails",
                method: "POST",
                dataType: "JSON",
                data: {
                  OrderID: OrderID
                },

                success: function(response) {

                  if (response.success === true) {

                    let items = response.items;

                    // Clear old rows
                    $('#items_body').html('');
                    $('#row_id').val(0);

                    // Loop items and add rows
                    for (let i = 0; i < items.length; i++) {

                      addRow(2); // create empty row
                      let row = i + 1;

                      $('#item_id' + row).val(items[i].ItemID);
                      getItemDetails(items[i].ItemID, row);

                      $('#min_qty' + row).val(items[i].OrderQty);
                      $('#max_qty' + row).val(items[i].OrderQty);
                      $('#unit_rate' + row).val(items[i].BasicRate);
                      $('#disc_amt' + row).val(items[i].DiscAmt);

                      calculateAmount(row);
                    }

                    $('.selectpicker').selectpicker('refresh');
                    calculateTotals();

                  } else {
                    alert_float('warning', response.message);
                  }
                }
              });
            });
          }
        );
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
      url: '<?= admin_url('SalesOrder/GetItemDetails'); ?>',
      type: 'POST',
      data: {
        item_id: itemId
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success' && response.data) {
          var data = response.data;
          $('#hsn_code' + id).val(data.hsn_code || '');
          $('#uom' + id).val(data.unit || '');
          $('#unit_weight' + id).val(Number(data.UnitWeight) || 0);
          $('#gst' + id).val(Number(data.tax) || 0);
          $('#min_qty' + id).focus();
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
      url: "<?= admin_url(); ?>SalesOrder/SaveSalesOrder",
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
          let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getDetails(${response.data.id})">
						<td>${response.data.OrderID}</td>
            <td>${moment(response.data.TransDate).format('DD/MM/YYYY')}</td>
            <td>${response.data.CategoryName}</td>
            <td>${response.data.company}</td>
            <td>${response.data.TotalWeight}</td>
            <td>${response.data.TotalQuantity}</td>
            <td>${response.data.ItemAmt}</td>
            <td>${response.data.DiscAmt}</td>
            <td>${response.data.TaxableAmt}</td>
            <td>${response.data.CGSTAmt}</td>
            <td>${response.data.SGSTAmt}</td>
            <td>${response.data.IGSTAmt}</td>
            <td>${response.data.RoundOffAmt}</td>
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


  function getDetails(id) {
    ResetForm();
    $.ajax({
      url: "<?= admin_url(); ?>SalesOrder/GetSalesOrderDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        id: id,
      },
      success: function(response) {
        if (response.success == true) {
          let d = response.data;
          $('#update_id').val(id);
          $('#item_type').val(d.ItemType);
          getCustomDropdownList('item_type', d.ItemType, 'item_category', d.ItemCategory, function() {
            getNextSalesOrderNo(function() {
              let history = response.data.history;
              for (var i = 0; i < history.length; i++) {
                addRow(2);
                $('#item_uid' + (i + 1)).val(history[i].id);
                $('#item_id' + (i + 1)).val(history[i].ItemID);
                getItemDetails(history[i].ItemID, (i + 1));
                $('#min_qty' + (i + 1)).val(Number(history[i].OrderQty));
                $('#max_qty' + (i + 1)).val(Number(history[i].OrderQty));
                $('#unit_rate' + (i + 1)).val(Number(history[i].BasicRate));
                $('#disc_amt' + (i + 1)).val(Number(history[i].DiscAmt));
                $('#amount' + (i + 1)).val(Number(history[i].NetOrderAmt));
                $('.selectpicker').selectpicker('refresh');
                calculateAmount(i + 1);
              }
            });
          });


          $('#OrderID').val(d.OrderID);
          $('#order_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
          $('#sales_location').val(d.SalesLocation);

          $('#customer_id').val(d.AccountID);
          getCustomerDetailsLocation(function() {
            $('#customer_location').val(d.DeliveryLocation);
            $('#broker_id').val(d.BrokerID);

            $('#quotation_id').empty();
            $('#quotation_id').append(
              '<option value="' + response.data.QuotationID + '">' +
              response.data.QuotationID +
              '</option>'
            );
            $('#quotation_id').selectpicker('refresh');

          });

          // $('#quotation_id').val(d.QuotationID).selectpicker('refresh');
          $('#delivery_from').val(moment(d.DeliveryFrom).format('DD/MM/YYYY'));
          $('#delivery_to').val(moment(d.DeliveryTo).format('DD/MM/YYYY'));
          $('#payment_terms').val(d.PaymentTerms);
          $('#freight_terms').val(d.FreightTerms);

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
</style>