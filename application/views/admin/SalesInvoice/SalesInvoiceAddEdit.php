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
                <li class="breadcrumb-item active" aria-current="page"><b>Sales Invoice</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="customer_id">
                    <label for="customer_id" class="control-label"><small class="req text-danger">* </small> Customer Name</label>
                    <select name="customer_id" id="customer_id" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor Name" required onchange="getCustomerDetailsLocation();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($customer_list)) :
                        foreach ($customer_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' - ' . $value['billing_state'] . ' (' . $value['AccountID'] . ')</option>';
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
                  <div class="form-group" app-field-wrapper="deliveryorder_id">
                    <label for="deliveryorder_id" class="control-label">Delivery Order List</label>
                    <select name="deliveryorder_id" id="deliveryorder_id" class="form-control selectpicker" data-live-search="true" app-field-label="Delivery Order List" onchange="getDeliveryOrderListDetails(this.value)">
                      <option value="" selected>None selected</option>
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
                  <div class="form-group" app-field-wrapper="OrderID">
                    <label for="OrderID" class="control-label"><small class="req text-danger">* </small> Invoice ID</label>
                    <input type="text" name="OrderID" id="OrderID" class="form-control" app-field-label="Order ID" readonly value="<?=  $NextSINumber; ?>">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="invoice_date">
                    <?= render_date_input('invoice_date', 'Invoice Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="SalesLocation">
                    <label for="SalesLocation" class="control-label"><small class="req text-danger">* </small> Dispatch From</label>
                    <select name="SalesLocation" id="SalesLocation" class="form-control selectpicker" data-live-search="true" app-field-label="SalesLocation" required>
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
                  <div class="form-group" app-field-wrapper="gate_no">
                    <label for="gate_no" class="control-label"><small class="req text-danger">* </small> Gate Entry No</label>
                    <select name="gate_no" id="gate_no" class="form-control selectpicker" data-live-search="true" app-field-label="Category">
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
                        <th style="width:100px;">HSN Code</th>
                        <th style="width:70px;">UOM</th>
                        <th style="width:80px;">Dispatch Qty</th>
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

                <div class="col-md-6"></div>

                <!-- <div class="col-md-6"></div> -->
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

                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="button" class="btn btn-primary printBtn" style="display: none;" onclick="printSalesInvoicePdf();"><i class="fa fa-print"></i> Print PDF</button>
                  <script>
                    // Print PDF function
                    function printSalesInvoicePdf() {
                      var OrderID = $('#OrderID').val();
                      if (!OrderID) {
                        alert_float('warning', 'Order ID not found!');
                        return;
                      }
                      var url = "<?= admin_url('SalesInvoice/SalesInvoicePrint/'); ?>" + OrderID;
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
          <h4 class="modal-title">Sale Invoice List</h4>
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

      <div class="modal-body" style="padding:0px 5px !important">

        <div class="table-ListModal tableFixHead2" style="overflow-x:auto;">
          <table class="tree table table-striped table-bordered table-ListModal tableFixHead2" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortablePop">Invoice ID</th>
                <th class="sortablePop">Invoice Date</th>
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
              if (!empty($salesinvoice_list)):
                foreach ($salesinvoice_list as $key => $value):
              ?>
                  <tr class="get_Details" data-id="<?= $value["id"]; ?>" onclick="getSalesInvoiceDetails(<?= $value['id']; ?>)" data-category="<?= $value["CategoryID"]; ?>">
  <td><?= $value["InvoiceID"]; ?></td>
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
                  <tr class="ledger-row" id="ledger-<?= $value['InvoiceID']; ?>" style="display:none;">
  <td colspan="14">
     <div class="ledger-container" id="ledger-data-<?= $value['InvoiceID']; ?>">
        Loading...
     </div>
  </td>
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
    $lastdate_date = '20'.$fy_new.'-03-31';
    $curr_date = date('Y-m-d');
    $curr_date_new = new DateTime($curr_date);
    $last_date_yr = new DateTime($lastdate_date);
    if($last_date_yr < $curr_date_new){
        $max_date_php = $lastdate_date;
    } else {
        $max_date_php = $curr_date;
    }
?>
<?php init_tail(); ?>
<script>

$(document).ready(function(){
    var fin_y   = "<?php echo $this->session->userdata('finacial_year'); ?>";
    var year    = "20" + fin_y;
    var cur_y   = new Date().getFullYear().toString().substr(-2);

    // Min date: April 1st of FY start year
    var minStartDate = new Date(year, 3, 1); // month index 3 = April

    // Max date: March 31 of FY end year, OR today if still within FY
    var maxEndDate;
    if (parseInt(cur_y) > parseInt(fin_y)) {
        var fy_new   = parseInt(fin_y) + 1;
        var fy_new_s = "20" + fy_new;
        maxEndDate   = new Date(fy_new_s + '/03/31');
    } else {
        maxEndDate = new Date();
    }

    // Order Date — restricted within FY, up to today or March 31
    $('#invoice_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate,
        timepicker: false
    });
});

  
  function toggleLedger(icon, invoiceID)
{
    var row = $('#ledger-'+invoiceID);

    if(row.is(':visible')){
        row.hide();
        $(icon).removeClass('fa-chevron-down').addClass('fa-chevron-right');
        return;
    }

    row.show();
    $(icon).removeClass('fa-chevron-right').addClass('fa-chevron-down');

    var container = $('#ledger-data-'+invoiceID);

    if(container.data('loaded')) return;

    $.ajax({
        url: "<?= admin_url('SalesInvoice/getLedgerByVoucher'); ?>",
        type: "POST",
        data: {VoucherID: invoiceID},
        success:function(res){
            container.html(res);
            container.data('loaded',true);
        }
    });
}
  function getDeliveryOrderListDetails(deliveryorder_id) {
    if (!deliveryorder_id) return;
    $.ajax({
      url: "<?= admin_url(); ?>SalesInvoice/getDeliveryOrderListDetails",
      type: "POST",
      data: {
        deliveryorder_id: deliveryorder_id
      },
      dataType: "json",
      success: function(response) {
        if (!response.success) return;
        let d = response.data;

        $('#SalesLocation').val(d.DispatchFrom).selectpicker('refresh');
        $('#item_category').val(d.CategoryID).selectpicker('refresh');
        $('#advreg').val(d.AdvRegType).selectpicker('refresh');
        $('#gate_no').val(d.GateINID);

        $('#customer_location').val(d.CustLocationID).selectpicker('refresh');

        $('#items_body').html('');
        $('#row_id').val(0);

        if (d.history && d.history.length > 0) {
          function processRow(index) {
            if (index >= d.history.length) {
              calculateTotals();
              return;
            }

            var item = d.history[index];

            addRow(2);
            var row = parseInt($('#row_id').val());

            $('#item_uid' + row).val(item.id || 0);
            $('#sr' + row).val(index + 1);
            $('#so_no' + row).val(item.OrderID || '');
            $('#item_id' + row).val(item.ItemID || '');
            $('#unit_rate' + row).val(Number(item.BasicRate || 0).toFixed(2));
            $('#disc_amt' + row).val(Number(item.DiscAmt || 0).toFixed(2));
            $('#dispatch_qty' + row).val(Number(item.OrderQty || 0).toFixed(0));

            var originalBalance = Number(item.BalanceQty || 0) + Number(item.OrderQty || 0);
            $('#min_qty' + row).val(originalBalance.toFixed(0));

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
                  $('#hsn_code' + row).val(res.data.hsn_code || '');
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

        $('.selectpicker').selectpicker('refresh');
        $('#form_mode').val('add');
        $('.saveBtn').show();
        $('.updateBtn').hide();
        $('.printBtn').hide();
        $('#ListModal').modal('hide');

      }
    });
  }

  function fmt(val) {
    var n = parseFloat(val);
    return isNaN(n) ? '0.00' : n.toFixed(2);
  }

  $('.printBtn').hide();

  function formatDate(dateString) {
    let d = new Date(dateString);
    return ("0" + d.getDate()).slice(-2) + "/" +
      ("0" + (d.getMonth() + 1)).slice(-2) + "/" +
      d.getFullYear();
  }

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
    $('#deliveryorder_id').val('').prop('disabled', false);
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
        <td><input type="text" name="sr[]" id="sr${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1" style="width:50px;"></td>
        <td><input type="text" name="so_no[]" id="so_no${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td style="min-width:200px;">
          <input type="hidden" name="item_uid[]" id="item_uid${next_id}" value="0">
          <input type="hidden" name="item_id[]" id="item_id${next_id}">
          <input type="text" name="item_name[]" id="item_name${next_id}" class="form-control" readonly tabindex="-1" placeholder="Item Name">
        </td>
        <td><input type="text" name="hsn_code[]" id="hsn_code${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1" style="width:100px;"></td>
        <td><input type="text" name="uom[]" id="uom${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1" style="width:70px;"></td>
        <input type="hidden" name="unit_weight[]" id="unit_weight${next_id}" class="form-control unit-weight dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1">
        <td><input type="tel" name="dispatch_qty[]" id="dispatch_qty${next_id}" class="form-control dispatch-qty dynamic_row${next_id}" min="0" step="0.01" onkeyup="calculateAmount(${next_id})" readonly style="width:80px;"></td>
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
      $('#hsn_code' + next_id).val($('#hsn_code').val());
      $('#uom' + next_id).val($('#uom').val());
      $('#dispatch_qty' + next_id).val($('#dispatch_qty').val());
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

  function getCustomerDetailsLocation(callback = null) {
    var CustomerId = $('#customer_id').val();

    if (!CustomerId) {
      $('#customer_location').html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }
    $.ajax({
      url: '<?= admin_url('SalesInvoice/getCustomerDetailsLocation'); ?>',
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
        // DELIVERY ORDER ROPDOWN
        // -----------------------------
        html = '<option value="" selected disabled>None selected</option>';
        $.each(response.deliveryorder_list, function(index, loc) {
          html += `<option value="${loc.OrderID}">${loc.OrderID}</option>`;
        });
        $('#deliveryorder_id').html(html).selectpicker('refresh');

        if (callback) callback();
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

    var form_data = new FormData(this);
    form_data.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );
    if (form_mode == 'edit') {
      form_data.append('update_id', $('#update_id').val());
    }

    $.ajax({
      url: "<?= admin_url(); ?>SalesInvoice/SaveSalesInvoice",
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
          let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getSalesInvoiceDetails(${response.data.id})">
						<td>${response.data.InvoiceID}</td>
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

  function getSalesInvoiceDetails(id) {
    ResetForm();
    $.ajax({
      url: "<?= admin_url(); ?>SalesInvoice/getSalesInvoiceDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        id: id
      },
      success: function(response) {
        if (response.success == true) {
          let d = response.data;

          $('#update_id').val(id);
          $('#OrderID').val(d.InvoiceID);
          
          $('#invoice_date').val(moment(d.InvoiceDate).format('DD/MM/YYYY'));
          $('#SalesLocation').val(d.SalesLocation).selectpicker('refresh');
          $('#item_category').val(d.CategoryID).selectpicker('refresh');
         
          $('#gate_no').val(d.GateINID);
          $('#customer_id').val(d.AccountID).selectpicker('refresh');

          getCustomerDetailsLocation(function() {
            $('#deliveryorder_id').val(d.DeliveryOrderID).selectpicker('refresh');
            $('#customer_location').val(d.CustLocationID).selectpicker('refresh');
          });

          $('#customer_address').val(d.CustAddress);

          $('#items_body').html('');
          $('#row_id').val(0);

          if (d.history && d.history.length > 0) {

            function processRow(index) {
              if (index >= d.history.length) {
                calculateTotals();
                return;
              }

              var item = d.history[index];

              addRow(2);
              var row = parseInt($('#row_id').val());

              $('#item_uid' + row).val(item.id || 0);
              $('#sr' + row).val(index + 1);
              $('#so_no' + row).val(item.OrderID || '');
              $('#item_id' + row).val(item.ItemID || '');
              $('#unit_rate' + row).val(Number(item.BasicRate || 0).toFixed(2));
              $('#disc_amt' + row).val(Number(item.DiscAmt || 0).toFixed(2));
              $('#dispatch_qty' + row).val(Number(item.OrderQty || 0).toFixed(0));

              var originalBalance = Number(item.BalanceQty || 0) + Number(item.OrderQty || 0);
              $('#min_qty' + row).val(originalBalance.toFixed(0));

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
                    $('#hsn_code' + row).val(res.data.hsn_code || '');
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
  .modal-header {
    padding: 0;
    border-bottom: none;
  }

  .custom-header .header-top {
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .custom-header .modal-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }

  .close-btn {
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
  }

  .header-filters {
    padding: 10px;
    display: flex;
    align-items: flex-end;
    gap: 25px;
  }

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

  .filter-group .form-control {
    height: 36px;
    min-width: 200px;
  }

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

  #table_ListModal tbody tr:hover {
    background-color: rgb(171, 174, 176);
  }
</style>