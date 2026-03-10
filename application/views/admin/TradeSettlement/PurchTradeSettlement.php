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
                <li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Purchase Trade Settlement</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="PartyID">
                    <label for="PartyID" class="control-label"><small class="req text-danger">* </small>Party / Vendor Name</label>
                    <select name="PartyID" id="PartyID" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor Name" required onchange="getBookingDetails();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($vendor_list)) :
                        foreach ($vendor_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' (' . $value['AccountID'] . ')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="BookingID">
                    <label for="BookingID" class="control-label">Order ID</label>
                    <select name="BookingID" id="BookingID" class="form-control selectpicker" data-live-search="true" app-field-label="Booking ID" onchange="getBookingListDetails(this.value)">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="BookingDate">
                    <?= render_date_input('BookingDate', 'Order Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="BookingRate">
                    <label for="BookingRate" class="control-label"><small class="req text-danger">* </small>Order Rate</label>
                    <input type="text" name="BookingRate" id="BookingRate" class="form-control" app-field-label="Booking Rate" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="BookingWt">
                    <label for="BookingWt" class="control-label"><small class="req text-danger">* </small>Order Weight(KG)</label>
                    <input type="text" name="BookingWt" id="BookingWt" class="form-control" app-field-label="Booking Weight" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="InwardWt">
                    <label for="InwardWt" class="control-label"><small class="req text-danger">* </small>Inward Weight(KG)</label>
                    <input type="text" name="InwardWt" id="InwardWt" class="form-control" app-field-label="Inward Weight" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="TodaysRate">
                    <label for="TodaysRate" class="control-label"><small class="req text-danger">* </small>Todays Rate</label>
                    <input type="text" name="TodaysRate" id="TodaysRate" class="form-control" app-field-label="Todays Rate" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ShortageQty">
                    <label for="ShortageQty" class="control-label"><small class="req text-danger">* </small>Shortage Quantity</label>
                    <input type="text" name="ShortageQty" id="ShortageQty" class="form-control" app-field-label="Shortage Quantity " readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ShortageAmt">
                    <label for="ShortageAmt" class="control-label"><small class="req text-danger">* </small>Shortage Amt</label>
                    <input type="text" name="ShortageAmt" id="ShortageAmt" class="form-control" app-field-label="Shortage Amount">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="Status">
                    <label for="Status" class="control-label"><small class="req text-danger">* </small>Status</label>
                    <select name="Status" id="Status" class="form-control selectpicker" app-field-label="Status" required>
                      <option value="">None selected</option>
                      <option value="1">Completed</option>
                      <option value="2">Partial Completed</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ShortageInvoice">
                    <label for="ShortageInvoice" class="control-label">Generate Shortage Invoice</label>
                    <select name="ShortageInvoice" id="ShortageInvoice" class="form-control selectpicker" app-field-label="ShortageInvoice">
                      <option value="">None selected</option>
                      <option value="Y">Yes</option>
                      <option value="N">No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="DeliveryCharges">
                    <label for="DeliveryCharges" class="control-label">Delivery Charged?</label>
                    <select name="DeliveryCharges" id="DeliveryCharges" class="form-control selectpicker" app-field-label="DeliveryCharges">
                      <option value="">None selected</option>
                      <option value="Y">Yes</option>
                      <option value="N">No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4 mbot5">
                  <div class="form-group" app-field-wrapper="Remark">
                    <label for="Remark" class="control-label"><small class="req text-danger">* </small>Remark</label>
                    <textarea name="Remark" id="Remark" class="form-control" app-field-label="Remark" rows="1" required></textarea>
                  </div>
                </div>

                <div class="col-md-12 mbot5">
                  <h4>Summary:</h4>
                  <hr class="hr_style">
                </div>
                <div class="col-md-12 mbot5">
                  <input type="hidden" id="row_id" value="0">
                  <table width="100%" class="table" id="items_table">
                    <thead>
                      <tr style="text-align: center;">
                        <th style="width:100px;">Order ID</th>
                        <th style="width:100px;">GATE PASS</th>
                        <th style="width:100px;">Inward Date</th>
                        <th style="width:100px;">AccountID</th>
                        <th style="width:100px;">PartyName</th>
                        <th style="width:100px;">BookingID</th>
                        <th style="width:100px;">ItemID</th>
                        <th style="width:100px;">ItemName</th>
                        <th style="width:100px;">Net Weight (Qtl)</th>
                        <th style="width:100px;">Total Bag</th>
                        <th style="width:100px;">Total Katta</th>
                        <th style="width:100px;">Total Layer</th>
                        <th style="width:100px;">Status</th>
                      </tr>
                    </thead>
                    <tbody id="items_body">
                    </tbody>
                    <tfoot>
                      <tr id="total_row">
                        <td colspan="8"><b>Total</b></td>
                        <td id="total_weight"><b>0.00</b></td>
                        <td id="total_bag"><b>0.00</b></td>
                        <td id="total_katta"><b>0.00</b></td>
                        <td colspan="2"></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>



                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="button" class="btn btn-warning" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
                </div>
              </div>
            </form>
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

        $('#BookingDate').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate,
            timepicker: false
        });
    });

  // Get The Customer Dropdrop
  function getBookingDetails(callback = null) {
    var PartyID = $('#PartyID').val();

    if (!PartyID) {
      $('#BookingID').html('<option value="" selected>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }
    $.ajax({
      url: '<?= admin_url('TradeSettlement/getBookingDetails'); ?>',
      type: 'POST',
      data: {
        PartyID: PartyID
      },
      dataType: 'json',
      success: function(response) {

        if (!response.success) return;

        let data = response.vendor_data || [];

        // -----------------------------
        // BOOKING ID DROPDOWN
        // -----------------------------
        let html = '<option value="" selected disabled>None selected</option>';

        $.each(data, function(index, item) {
          // Example: show InvoiceID + DeliveryOrderID for better clarity
          html += `<option value="${item.PurchID}">${item.PurchID}</option>`;
        });

        $('#BookingID').html(html);
        $('.selectpicker').selectpicker('refresh');

        if (callback) callback();
      }
    });
  }


  function getBookingListDetails(InvoiceID) {
    if (!InvoiceID) return;
    const bookingType = $('#BookingType').val();
    $.ajax({
      url: "<?= admin_url(); ?>TradeSettlement/getBookingListDetails",
      type: "POST",
      data: {
        InvoiceID: InvoiceID
      },
      dataType: "json",
      success: function(response) {
        if (!response.success) return;
        
          let d = response.data;
          let i = response.inward_data;

          $('#BookingDate').val(moment(d.TransDate).format('DD/MM/YYYY'));
          $('#BookingRate').val(d.ItemAmt);
          $('#TodaysRate').val(d.ItemAmt);
          $('#BookingWt').val(d.TotalWeight);
          $('#InwardWt').val(i.TotalWeight);

          calculateShortage();

          $('#item_category').val(d.CategoryID).selectpicker('refresh');
          $('#advreg').val(d.AdvRegType).selectpicker('refresh');
          $('#gate_no').val(d.GateINID);

          $('#customer_location').val(d.CustLocationID).selectpicker('refresh');

          $('#items_body').html('');
          $('#row_id').val(0);

          if (i) {

            addRow(2);
            var row = parseInt($('#row_id').val());

            $('#order_id' + row).text(i.InwardsID || '');
            $('#gate_pass' + row).text(i.GatePass || '');
            $('#inward_date' + row).text(moment(i.TransDate).format('DD/MM/YYYY'));
            $('#account_id' + row).text(i.AccountID || '');
            $('#party_name' + row).text(i.company || '');
            $('#booking_id' + row).text(i.OrderID || '');

            if (i.history && i.history.length > 0) {
              $('#item_id' + row).text(i.history[0].ItemID || '');
              $('#item_name' + row).text(i.history[0].item_name || '');
            }

            $('#total_weight' + row).text(i.TotalWeight || 0);
            $('#total_bag' + row).text(i.BagWeight || 0);
            $('#total_katta' + row).text(i.TotalKatta || 0);
            $('#total_layer' + row).text(i.TotalLayer || 0);
            $('#status' + row).text(i.gate_Status || '');
            calculateTotals();


            // show total row
            $('#total_row').show();
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



  function calculateShortage() {

    let bookingWt = parseFloat($('#BookingWt').val()) || 0;
    let inwardWt = parseFloat($('#InwardWt').val()) || 0;
    let rate = parseFloat($('#TodaysRate').val()) || 0;

    // Calculate shortage quantity
    let shortageQty = bookingWt - inwardWt;

    if (shortageQty < 0) shortageQty = 0;

    // Calculate shortage amount
    let shortageAmt = shortageQty * rate;

    $('#ShortageQty').val(shortageQty.toFixed(2));
    $('#ShortageAmt').val(shortageAmt.toFixed(2));

    // Auto-select Status based on weight comparison
  if (bookingWt > 0 && bookingWt === inwardWt) {
    $('#Status').val('1').selectpicker('refresh'); // Completed
  } else if (inwardWt > 0 && inwardWt < bookingWt) {
    $('#Status').val('2').selectpicker('refresh'); // Partial Completed
  } else {
    $('#Status').val('').selectpicker('refresh');  // Reset
  }
  }

  $('.printBtn').hide();

  function formatDate(dateString) {
    let d = new Date(dateString);
    return ("0" + d.getDate()).slice(-2) + "/" +
      ("0" + (d.getMonth() + 1)).slice(-2) + "/" +
      d.getFullYear();
  }

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
    $('#total_row').hide();
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
<tr id="row${next_id}">
 <td id="order_id${next_id}"></td>
  <td id="gate_pass${next_id}"></td>
  <td id="inward_date${next_id}"></td>
  <td id="account_id${next_id}"></td>
  <td id="party_name${next_id}"></td>
  <td id="booking_id${next_id}"></td>
  <td id="item_id${next_id}"></td>
  <td id="item_name${next_id}"></td>
  <td class="net_weight" id="total_weight${next_id}"></td>
<td class="total_bag" id="total_bag${next_id}"></td>
<td class="total_katta" id="total_katta${next_id}"></td>
<td id="total_layer${next_id}"></td>
  <td id="status${next_id}"></td>
</tr>
`);
    $('#row_id').val(next_id);
    $('.selectpicker').selectpicker('refresh');
  }



  function calculateTotals() {
    let totalWeight = 0;
    let totalBag = 0;
    let totalKatta = 0;

    $('.net_weight').each(function() {
      totalWeight += parseFloat($(this).text()) || 0;
    });

    $('.total_bag').each(function() {
      totalBag += parseFloat($(this).text()) || 0;
    });

    $('.total_katta').each(function() {
      totalKatta += parseFloat($(this).text()) || 0;
    });

    $('#total_weight').text(totalWeight.toFixed(2));
    $('#total_bag').text(totalBag.toFixed(2));
    $('#total_katta').text(totalKatta.toFixed(2));
  }

  function get_required_fields(form_id) {
    let fields = [];
    $('#' + form_id + ' [required]').each(function() {
      fields.push($(this).attr('id'));
    });
    return fields;
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

  #total_row {
    display: none;
  }
</style>