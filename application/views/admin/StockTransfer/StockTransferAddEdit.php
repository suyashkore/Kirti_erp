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
                <li class="breadcrumb-item active" aria-current="page"><b>Stock Transfer</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="TransferNo">
                    <label for="TransferNo" class="control-label"><small class="req text-danger">* </small> Transfer No</label>
                    <input type="text" name="TransferNo" id="TransferNo" class="form-control" app-field-label="Transfer No" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="TransferDate">

                    <?= render_date_input('TransferDate', 'Transfer Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="VehicleNo">
                    <label for="VehicleNo" class="control-label"><small class="req text-danger">* </small> Vehicle No</label>
                    <select name="VehicleNo" id="VehicleNo" class="form-control selectpicker" data-live-search="true" app-field-label="Vehicle No" required onchange="getVehicleDetails();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($VehicleNo)) :
                        foreach ($VehicleNo as $value) :
                          echo '<option value="' . $value['VehicleNo'] . '">' . $value['VehicleNo'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="DriverName">
                    <label for="DriverName" class="control-label"><small class="req text-danger">* </small> Driver Name</label>
                    <input type="text" name="DriverName" id="DriverName" class="form-control" app-field-label="Driver Name" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="Distance">
                    <label for="Distance" class="control-label"><small class="req text-danger">* </small> Distance (Km)</label><a href="https://einvoice1.gst.gov.in/Others/GetPinCodeDistance" target="_blank"> Get Dist.</a>
                    <input type="text" name="Distance" id="Distance" class="form-control" app-field-label="Distance">
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="FromLocation">
                    <label for="FromLocation" class="control-label"><small class="req text-danger">* </small> From Location</label>
                    <select name="FromLocation" id="FromLocation" class="form-control selectpicker" data-live-search="true" app-field-label="From Location" required onchange="getPlantLocationDetails('from'); syncLocationOptions('from');">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($FromPlantLocation)) :
                        foreach ($FromPlantLocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="FromGodown">
                    <label for="FromGodown" class="control-label"><small class="req text-danger">* </small> Godown</label>
                    <select name="FromGodown" id="FromGodown" class="form-control selectpicker" data-live-search="true" app-field-label="From Godown" required onchange="getPincode('from'); loadChambers('from');">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="FromPincode">
                    <label for="FromPincode" class="control-label"><small class="req text-danger">* </small> Pincode</label>
                    <input type="text" name="FromPincode" id="FromPincode" class="form-control" app-field-label="From Pincode" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="FromChamber">
                    <label for="FromChamber" class="control-label">Chamber</label>
                    <select name="FromChamber" id="FromChamber" class="form-control selectpicker" data-live-search="true" app-field-label="From Chamber" onchange="loadStacks('from');">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="FromStack">
                    <label for="FromStack" class="control-label">Stack</label>
                    <select name="FromStack" id="FromStack" class="form-control selectpicker" data-live-search="true" app-field-label="From Stack" onchange="loadLots('from');">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="FromLot">
                    <label for="FromLot" class="control-label">Lot</label>
                    <select name="FromLot" id="FromLot" class="form-control selectpicker" data-live-search="true" app-field-label="From Lot">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <!-- To Location -->

                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ToLocation">
                    <label for="ToLocation" class="control-label"><small class="req text-danger">* </small> To Location</label>
                    <select name="ToLocation" id="ToLocation" class="form-control selectpicker" data-live-search="true" app-field-label="To Location" required onchange="getPlantLocationDetails('to'); syncLocationOptions('to');">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($ToPlantLocation)) :
                        foreach ($ToPlantLocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ToGodown">
                    <label for="ToGodown" class="control-label"><small class="req text-danger">* </small> Godown</label>
                    <select name="ToGodown" id="ToGodown" class="form-control selectpicker" data-live-search="true" app-field-label="To Godown" required onchange="getPincode('to'); loadChambers('to');">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ToPincode">
                    <label for="ToPincode" class="control-label"><small class="req text-danger">* </small> Pincode</label>
                    <input type="text" name="ToPincode" id="ToPincode" class="form-control" app-field-label="From Pincode" readonly>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ToChamber">
                    <label for="ToChamber" class="control-label">Chamber</label>
                    <select name="ToChamber" id="ToChamber" class="form-control selectpicker" data-live-search="true" app-field-label="To Chamber" onchange="loadStacks('to');">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ToStack">
                    <label for="ToStack" class="control-label">Stack</label>
                    <select name="ToStack" id="ToStack" class="form-control selectpicker" data-live-search="true" app-field-label="To Stack" onchange="loadLots('to');">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="ToLot">
                    <label for="ToLot" class="control-label">Lot</label>
                    <select name="ToLot" id="ToLot" class="form-control selectpicker" data-live-search="true" app-field-label="To Lot">
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
                        <th>Current Stock Qty</th>
                        <th>Qty</th>
                        <th>Total Wt</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td style="width: 250px;">
                          <select id="item_id" class="form-control fixed_row dynamic_row dynamic_item selectpicker" data-live-search="true" app-field-label="Item Name" onchange="getItemDetails(this.value, '');">
                            <option value="" selected disabled>Select Item</option>
                            <?php
                            if (!empty($Items)) :
                              foreach ($Items as $value) :
                                echo '<option value="' . $value['ItemID'] . '">' . $value['ItemName'] . '</option>';
                              endforeach;
                            endif;
                            ?>
                          </select>
                        </td>
                        <td><input type="text" id="hsn_code" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="text" id="uom" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="unit_weight" class="form-control fixed_row" min="0" step="0.01" readonly tabindex="-1"></td>
                        <td><input type="tel" id="current_stock_qty" class="form-control fixed_row" min="0" step="0.01" readonly tabindex="-1"></td>
                        <td><input type="tel" id="qty" class="form-control fixed_row" min="0" step="0.01" onchange="calculateAmount('');" oninput="liveStockPreview('');"></td>
                        <td><input type="tel" id="total_wt" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td>
                          <button type="button" class="btn btn-success" onclick="addRow();"><i class="fa fa-plus"></i></button>
                        </td>
                      </tr>
                    </thead>
                    <tbody id="items_body">

                    </tbody>
                  </table>
                </div>

                <div class="col-md-6">
                  <div class="row" id="ewayBillSection">
                    <div class="col-md-12">
                      <h4 class="bold p_style">E Way Bill Details</h4>
                      <hr class="hr_style">
                    </div>
                    <div class="col-md-12">
                      <div class="col-md-4 mbot5">
                        <div class="form-group">
                          <label><small class="req text-danger">*</small> E Way Bill No</label>
                          <input type="text" id="EWayBillNo" class="form-control" readonly>
                        </div>
                      </div>
                      <div class="col-md-4 mbot5">
                        <?= render_date_input('EWayBillDate', 'E Way Bill Date', '', [], ['readonly' => true]); ?>
                      </div>
                      <div class="col-md-4 mbot5">
                        <?= render_date_input('EWayBillExpiryDate', 'E Way Bill Expiry Date', '', [], ['readonly' => true]); ?>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <h4 class="bold p_style">Total Summary</h4>
                      <hr class="hr_style">
                    </div>

                    <!-- Hidden inputs to store values for form submission -->
                    <input type="hidden" name="total_weight" id="total_weight_hidden" value="0">
                    <input type="hidden" name="total_qty" id="total_qty_hidden" value="0">

                    <!-- LEFT COLUMN -->
                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row">
                        <label>Total Weight:</label>
                        <div class="total-display" id="total_weight_display">0.00</div>
                      </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="col-md-6 col-sm-6 col-xs-6">

                      <div class="total-label-row">
                        <label>Total Qty:</label>
                        <div class="total-display" id="total_qty_display">0.00</div>
                      </div>

                    </div>

                  </div>
                </div>
                <!-- E-Way Bill Lock Warning -->
                <div class="col-md-12" style="margin-top: 8px;">
                  <span id="eway_lock_warning"
                    style="display:none; color:#c0392b; font-size:13px; font-weight:600;">
                    ⚠ This record is locked and cannot be updated because an E-Way Bill has been generated.
                  </span>
                </div>
                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('stockTransfer', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="button" class="btn btn-primary printBtn <?= (has_permission_new('stockTransfer', '', 'print')) ? '' : 'disabled'; ?>" style="display: none;" onclick="printStockTransferPdf();"><i class="fa fa-print"></i> Print PDF</button>
                  <script>
                    // Print PDF function
                    function printStockTransferPdf() {
                      var TransferID = $('#TransferNo').val();
                      if (!TransferID) {
                        alert_float('warning', 'Transfer ID not found!');
                        return;
                      }
                      var url = "<?= admin_url('StockTransfer/StockTransferPrint/'); ?>" + TransferID;
                      window.open(url, '_blank');
                    }
                  </script>
                  <button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('stockTransfer', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
                  <button type="button" class="btn eWayBillBtn" style="display:none; background-color:#6f42c1; color:#fff; border:none;" onclick="generateEWayBill();"><i class="fa fa-truck"></i> Generate E-Way Bill</button>
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
          <h4 class="modal-title">Stock Transfer List</h4>
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
            <button type="button" class="btn btn-success" id="showBtn">
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
                <th class="sortablePop">Stock Transfer No</th>
                <th class="sortablePop">Transfer Date</th>
                <th class="sortablePop">From Location</th>
                <th class="sortablePop">To Location</th>
                <th class="sortablePop">Distance</th>
                <th class="sortablePop">Vehicle No</th>
                <th class="sortablePop">Driver Name</th>
                <th class="sortablePop">Total Qty</th>
                <th class="sortablePop">Total Wt</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (!empty($StockTransfer_list)):
                foreach ($StockTransfer_list as $key => $value):
              ?>
                  <tr class="get_Details" data-id="<?= $value["id"]; ?>" onclick="getStockTransferDetails(<?= $value['id']; ?>)">
                    <td><?= $value["TransferID"]; ?></td>
                    <td><?= date('d/m/Y', strtotime($value["TransferDate"])); ?></td>
                    <td><?= $value["FromLocation"]; ?></td>
                    <td><?= $value["ToLocation"]; ?></td>
                    <td><?= $value["Distance"]; ?></td>
                    <td><?= $value["VehicleNo"]; ?></td>
                    <td><?= $value["DriverName"]; ?></td>
                    <td><?= $value["TotalQuantity"]; ?></td>
                    <td><?= $value["TotalWeight"]; ?></td>
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
    $('#ewayBillSection').hide();

    syncLocationOptions();

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
    $('#TransferDate').datetimepicker({
      format: 'd/m/Y',
      minDate: minStartDate,
      maxDate: maxEndDate,
      timepicker: false
    });

  });

  // Helper: safely format a number to 2 decimal places
  function fmt(val) {
    var n = parseFloat(val);
    return isNaN(n) ? '0.00' : n.toFixed(2);
  }

  $('#ListModal').on('shown.bs.modal', function() {
    $('#showBtn').trigger('click');
  });
  $('#showBtn').on('click', function() {

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

      $(this).toggle(dateMatch);
    });
  });


  $(document).ready(function() {
    getNextSTNo();
  });

  function getNextSTNo(callback = null) {
    $.ajax({
      url: '<?= admin_url('StockTransfer/getNextSTNo'); ?>',
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          let form_mode = $('#form_mode').val();
          if (form_mode == 'add') {
            $('#TransferNo').val(response.NextSTNo).prop('readonly', true);
          }
        } else {
          $('#TransferNo').val('');
        }
        if (callback) callback();
      },
      error: function() {
        $('#TransferNo').val('').prop('readonly', true);
      }
    });
  }

  function ResetForm() {
    $('#main_save_form')[0].reset();
    $('#form_mode').val('add');
    $('#update_id').val('');
    $('.updateBtn').hide();
    $('.printBtn').hide();
    getNextSTNo();

    $('.eWayBillBtn').hide();
    $('.saveBtn').show();
    $('.selectpicker').selectpicker('refresh');
    $('#items_body').html('');
    $('.total-display').text('0.00');
    $('#row_id').val(0);
    
    $('#ewayBillSection').hide();

    $('#eway_lock_warning').hide();
    $('#main_save_form input:not([type="hidden"])').prop('readonly', false);
    $('#main_save_form select').prop('disabled', false).selectpicker('refresh');
    $('#main_save_form textarea').prop('readonly', false);
    $('#items_body input').prop('readonly', false);
    $('#items_body select').prop('disabled', false).selectpicker('refresh');

    $('#hsn_code, #uom, #unit_weight, #current_stock_qty, #total_wt').prop('readonly', true);
    $('#DriverName, #FromPincode, #ToPincode').prop('readonly', true);
  }

  $(document).on('input', '#Distance', function() {
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

  function getPlantLocationDetails(type, callback = null) {
    var locationId = (type === 'from') ? $('#FromLocation').val() : $('#ToLocation').val();
    var godownTarget = (type === 'from') ? '#FromGodown' : '#ToGodown';

    if (!locationId) {
      $(godownTarget).html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }

    var postData = {
      FromLocation: locationId,
      type: type
    };

    if (type === 'to') {
      var fromLocationID = $('#FromGodown option:selected').data('location-id');
      if (fromLocationID) {
        postData.ExcludeLocationID = fromLocationID;
      }
    }

    $.ajax({
      url: '<?= admin_url('StockTransfer/getPlantLocationDetails'); ?>',
      type: 'POST',
      data: postData,
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var html = '<option value="" selected disabled>None selected</option>';
          $.each(response.Godown, function(index, godown) {
            if (godown.id == null || godown.id == '') return;
            html += '<option value="' + godown.id + '" data-location-id="' + godown.LocationID + '">' +
              godown.GodownName + '</option>';
          });
          $(godownTarget).html(html);
          $('.selectpicker').selectpicker('refresh');
          if (callback) callback();
        }
      }
    });
  }

  function syncLocationOptions(changedType) {
    var fromSel = $('#FromLocation');
    var toSel = $('#ToLocation');

    var fromVal = fromSel.val();
    var toVal = toSel.val();

    fromSel.find('option').show().prop('disabled', false);
    toSel.find('option').show().prop('disabled', false);

    if (fromVal) {
      toSel.find('option[value="' + fromVal + '"]').hide().prop('disabled', true);

      if (toVal == fromVal) {
        toSel.val('');
        $('#ToGodown').html('<option value="" selected disabled>None selected</option>');
        $('#ToPincode').val('');
      }
    }

    if (toVal && toVal != fromVal) {
      fromSel.find('option[value="' + toVal + '"]').hide().prop('disabled', true);
      if (fromVal == toVal) {
        fromSel.val('');
        $('#FromGodown').html('<option value="" selected disabled>None selected</option>');
        $('#FromPincode').val('');
      }
    }

    $('.selectpicker').selectpicker('refresh');
  }

  var fromGodownData = {};
  var toGodownData = {};

  function getPincode(type, callback = null) {
    var godownId = (type === 'from') ? $('#FromGodown').val() : $('#ToGodown').val();
    var pincodeTarget = (type === 'from') ? '#FromPincode' : '#ToPincode';

    if (!godownId) {
      $(pincodeTarget).val('');
      return;
    }

    $.ajax({
      url: '<?= admin_url('StockTransfer/getPincode'); ?>',
      type: 'POST',
      data: {
        FromGodown: godownId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          $(pincodeTarget).val(response.Pincode);
          if (callback) callback();
        }
      }
    });
  }

  function loadChambers(type, selectedChamber = null, callback = null) {
    var godownId = (type === 'from') ? $('#FromGodown').val() : $('#ToGodown').val();
    var chamberTarget = (type === 'from') ? '#FromChamber' : '#ToChamber';
    var stackTarget = (type === 'from') ? '#FromStack' : '#ToStack';
    var lotTarget = (type === 'from') ? '#FromLot' : '#ToLot';

    $(stackTarget).html('<option value="" selected disabled>None selected</option>');
    $(lotTarget).html('<option value="" selected disabled>None selected</option>');
    $('.selectpicker').selectpicker('refresh');

    if (!godownId) {
      $(chamberTarget).html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }

    $.ajax({
      url: '<?= admin_url('StockTransfer/getChambers'); ?>',
      type: 'POST',
      data: {
        GodownID: godownId
      },
      dataType: 'json',
      success: function(response) {
        var html = '<option value="" selected disabled>None selected</option>';
        if (response.success && response.data.length > 0) {
          $.each(response.data, function(i, row) {
            html += '<option value="' + row.id + '">' + row.ChamberName + '</option>';
          });
        }
        $(chamberTarget).html(html);
        if (selectedChamber) $(chamberTarget).val(selectedChamber);
        $('.selectpicker').selectpicker('refresh');
        if (callback) callback();
      }
    });
  }

  function loadStacks(type, selectedStack = null, callback = null) {
    var godownId = (type === 'from') ? $('#FromGodown').val() : $('#ToGodown').val();
    var chamberId = (type === 'from') ? $('#FromChamber').val() : $('#ToChamber').val();
    var stackTarget = (type === 'from') ? '#FromStack' : '#ToStack';
    var lotTarget = (type === 'from') ? '#FromLot' : '#ToLot';

    // Reset downstream
    $(lotTarget).html('<option value="" selected disabled>None selected</option>');
    $('.selectpicker').selectpicker('refresh');

    if (!godownId || !chamberId) {
      $(stackTarget).html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }

    $.ajax({
      url: '<?= admin_url('StockTransfer/getStacks'); ?>',
      type: 'POST',
      data: {
        GodownID: godownId,
        ChamberID: chamberId
      },
      dataType: 'json',
      success: function(response) {
        var html = '<option value="" selected disabled>None selected</option>';
        if (response.success && response.data.length > 0) {
          $.each(response.data, function(i, row) {
            html += '<option value="' + row.id + '">' + row.StackName + '</option>';
          });
        }
        $(stackTarget).html(html);
        if (selectedStack) $(stackTarget).val(selectedStack);
        $('.selectpicker').selectpicker('refresh');
        if (callback) callback();
      }
    });
  }

  function loadLots(type, selectedLot = null, callback = null) {
    var godownId = (type === 'from') ? $('#FromGodown').val() : $('#ToGodown').val();
    var chamberId = (type === 'from') ? $('#FromChamber').val() : $('#ToChamber').val();
    var stackId = (type === 'from') ? $('#FromStack').val() : $('#ToStack').val();
    var lotTarget = (type === 'from') ? '#FromLot' : '#ToLot';

    if (!godownId || !chamberId || !stackId) {
      $(lotTarget).html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }

    $.ajax({
      url: '<?= admin_url('StockTransfer/getLots'); ?>',
      type: 'POST',
      data: {
        GodownID: godownId,
        ChamberID: chamberId,
        StackID: stackId
      },
      dataType: 'json',
      success: function(response) {
        var html = '<option value="" selected disabled>None selected</option>';
        if (response.success && response.data.length > 0) {
          $.each(response.data, function(i, row) {
            html += '<option value="' + row.id + '">' + row.LotName + '</option>';
          });
        }
        $(lotTarget).html(html);
        if (selectedLot) $(lotTarget).val(selectedLot);
        $('.selectpicker').selectpicker('refresh');
        if (callback) callback();
      }
    });
  }

  function getVehicleDetails() {

    var VehicleNo = $('#VehicleNo').val();

    if (VehicleNo == '') {
      alert_float('warning', 'Please select Vehicle No');
      return;
    }

    $.ajax({
      url: "<?php echo admin_url(); ?>StockTransfer/getVehicleDetails",
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

          $('#DriverName').val(vehicle.DriverName || '');
        } else {
          $('#DriverName').val('');
        }
      }
    });
  }

  function addRow(row = null) {
    $('#item_id').focus();
    var row_id = $('#row_id').val();
    var next_id = parseInt(row_id) + 1;
    if (row == null) {
      let fields = ['item_id', 'qty'];
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
        <td><input type="tel" name="current_stock_qty[]" id="current_stock_qty${next_id}" class="form-control unit-weight dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1"></td>
        <td><input type="tel" name="qty[]" id="qty${next_id}" class="form-control min-qty dynamic_row${next_id}" min="0" step="0.01" onchange="calculateAmount(${next_id})" oninput="liveStockPreview(${next_id})"></td>
        <td><input type="tel" name="total_wt[]" id="total_wt${next_id}" class="form-control row-total-wt dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td></td>
      </tr>
    `);
    if (row == null) {
      $('#item_id' + next_id).val($('#item_id').val());
      $('#hsn_code' + next_id).val($('#hsn_code').val());
      $('#uom' + next_id).val($('#uom').val());
      $('#unit_weight' + next_id).val($('#unit_weight').val());
      $('#current_stock_qty' + next_id)
        .val($('#current_stock_qty').val())
        .attr('data-stock', $('#current_stock_qty').attr('data-stock'))
        .css('color', $('#current_stock_qty').css('color'));
      $('#qty' + next_id).val($('#qty').val());
      $('#total_wt' + next_id).val($('#total_wt').val());
      $('.fixed_row').val('');
      calculateAmount(next_id);
    }
    $('#row_id').val(next_id);
    $('.selectpicker').selectpicker('refresh');
  }

  function calculateAmount(row) {
    var qty = parseFloat($('#qty' + row).val()) || 0;
    var unitWt = parseFloat($('#unit_weight' + row).val()) || 0;
    var maxStock = parseFloat($('#current_stock_qty' + row).attr('data-stock')) || 0;

    if (qty > maxStock) {
      alert_float('warning', 'Qty cannot exceed available stock of ' + fmt(maxStock));
      $('#qty' + row).val(fmt(maxStock));
      qty = maxStock;
    }

    var rowTotalWt = unitWt * qty;
    $('#total_wt' + row).val(fmt(rowTotalWt));

    var remaining = maxStock - qty;
    if (maxStock > 0) {
      $('#current_stock_qty' + row)
        .val(fmt(remaining))
        .css('color', remaining <= 0 ? 'red' : (remaining < maxStock * 0.2 ? 'orange' : 'green'));
    }

    calculateTotals();

    if ((row === '' || row === null) && qty > 0) {
      addRow();
    }
  }

  function liveStockPreview(row) {
    var qty = parseFloat($('#qty' + row).val()) || 0;
    var maxStock = parseFloat($('#current_stock_qty' + row).attr('data-stock')) || 0;

    if (maxStock <= 0) return;

    var remaining = maxStock - qty;

    if (remaining < 0) remaining = 0;

    $('#current_stock_qty' + row)
      .val(fmt(remaining))
      .css('color', remaining <= 0 ? 'red' : (remaining < maxStock * 0.2 ? 'orange' : 'green'));
  }

  function calculateTotals() {
    var totalWeight = 0;
    var totalQty = 0;

    $('#items_body tr').each(function() {
      var row = $(this);
      var qty = parseFloat(row.find('.min-qty').val()) || 0;
      var weight = parseFloat(row.find('.unit-weight').val()) || 0;

      totalWeight += weight * qty;
      totalQty += qty;
    });

    $('#total_weight_display').text(fmt(totalWeight));
    $('#total_qty_display').text(fmt(totalQty));

    $('#total_weight_hidden').val(fmt(totalWeight));
    $('#total_qty_hidden').val(fmt(totalQty));
  }

  function get_required_fields(form_id) {
    let fields = [];
    $('#' + form_id + ' [required]').each(function() {
      fields.push($(this).attr('id'));
    });
    return fields;
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
      url: '<?= admin_url('StockTransfer/GetItemDetails'); ?>',
      type: 'POST',
      data: {
        item_id: itemId
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success' && response.data) {
          var data = response.data;
          var stockQty = parseFloat(data.CurrentStockQty || 0);

          $('#hsn_code' + id).val(data.hsn_code || '');
          $('#uom' + id).val(data.unit || '');
          $('#unit_weight' + id).val(parseFloat(data.UnitWeight || 0).toFixed(0));
          $('#gst' + id).val(parseFloat(data.tax || 0).toFixed(0));
          if (stockQty <= 0) {

            $('#current_stock_qty' + id)
              .val('Out of Stock')
              .css('color', 'red')
              .attr('data-stock', 0);
            $('#qty' + id)
              .val('')
              .prop('readonly', true)
              .prop('disabled', true)
              .css('background-color', '#f5f5f5');
          } else {

            $('#current_stock_qty' + id)
              .val(fmt(stockQty))
              .css('color', 'green')
              .attr('data-stock', stockQty);
            $('#qty' + id)
              .val('')
              .prop('readonly', false)
              .prop('disabled', false)
              .css('background-color', '')
              .attr('max', stockQty);
            $('#qty' + id).focus();
          }

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

  function generateEWayBill() {
    var TransferID = $('#TransferNo').val();
    var updateId = $('#update_id').val();

    if (!updateId) {
      alert_float('warning', 'Please save the Stock Transfer first before generating E-Way Bill.');
      return;
    }

    var itemList = [];
    $('#items_body tr').each(function() {
      var row = $(this);
      var rowIndex = row.find('select[name="item_id[]"]').attr('id').replace('item_id', '');

      var itemName = row.find('select[name="item_id[]"] option:selected').text().trim();
      var itemId = row.find('select[name="item_id[]"]').val();
      var hsnCode = row.find('input[name="hsn_code[]"]').val();
      var qty = parseFloat(row.find('input[name="qty[]"]').val()) || 0;
      var uom = row.find('input[name="uom[]"]').val();

      if (!itemId || qty <= 0) return;


      itemList.push({
        productName: itemName,
        productDesc: itemName + ' - ' + itemId,
        hsnCode: parseInt(hsnCode) || 0,
        quantity: qty,
        qtyUnit: uom || 'NOS',
        sgstRate: 0,
        cgstRate: 0,
        igstRate: 0,
        cessRate: 0,
        taxableAmount: 0,
      });

    });

    if (itemList.length === 0) {
      alert_float('warning', 'No valid items found to generate E-Way Bill.');
      return;
    }

    GetDataForEWayBill(function(DataForBill) {

      var payload = {
        supplyType: 'O',
        subSupplyType: '1',
        subSupplyDesc: ' ',
        docType: 'INV',

        docNo: DataForBill.TransferID || TransferID,
        // docNo: 'ST251011',
        docDate: moment().format('DD/MM/YYYY'),

        fromTrdName: DataForBill.FromGodown || 'welton',
        fromAddr1: DataForBill.FromAddress || '2ND CROSS NO 59  19  A',
        fromAddr2: ' ',
        fromPlace: DataForBill.FromCity || 'FRAZER TOWN',
        fromPincode: parseInt(DataForBill.FromPincode) || 560001,
        fromStateCode: parseInt(DataForBill.FromStateCode) || 26,
        actFromStateCode: parseInt(DataForBill.ActFromStateCode) || 26,

        dispatchFromGSTIN: DataForBill.FromGSTIN || '29AAAAA1303P1ZV',
        dispatchFromTradeName: DataForBill.FromCompany || 'ABC Traders',

        toGstin: '05AAACH6188F1ZM',
        toTrdName: DataForBill.ToGodown || 'sthuthya',
        toAddr1: DataForBill.ToAddress || 'Shree Nilaya',
        toAddr2: ' ',
        toPlace: DataForBill.ToCity || 'Beml Nagar',
        toPincode: parseInt(DataForBill.ToPincode) || 263652,
        toStateCode: parseInt(DataForBill.ToStateCode) || 5,

        actToStateCode: parseInt(DataForBill.ActToStateCode) || 5,

        shipToGSTIN: DataForBill.ToGSTIN || '29ALSPR1722R1Z3',
        shipToTradeName: DataForBill.ToCompany || 'XYZ Traders',

        transactionType: 1,
        totalValue: 0,
        cgstValue: 0,
        sgstValue: 0,
        igstValue: 0,
        cessValue: 0,
        cessNonAdvolValue: 0,
        totInvValue: 0,

        transMode: '1',
        transDistance: DataForBill.Distance || '2487',
        transporterName: DataForBill.TransporterName || $('#DriverName').val() || 'Dummy',
        transporterId: '05AAACG0904A1ZL',
        transDocNo: '12',
        transDocDate: moment().format('DD/MM/YYYY'),
        vehicleNo: DataForBill.VehicleNo || $('#VehicleNo').val() || 'APR3214',
        vehicleType: 'R',

        itemList: itemList,
      };

      $.ajax({
        url: "<?= admin_url(); ?>EWayBillAPI",
        method: "POST",
        dataType: "JSON",
        data: {
          payload: JSON.stringify(payload),
          id: TransferID,
          TransferID: updateId,
        },
        beforeSend: function() {
          $('.eWayBillBtn').attr('disabled', true).text('Generating...');
        },
        complete: function() {
          $('.eWayBillBtn').attr('disabled', false).html('<i class="fa fa-truck"></i> Generate E-Way Bill');
        },
        success: function(response) {
          if (response.success && response.ewayBillNo) {
            alert_float('success', 'E-Way Bill generated! EWB No: ' + response.ewayBillNo +
              ' | Valid Upto: ' + response.validUpto);
              ResetForm();
          } else if (response.success === false) {
            var msg = response.message || response.info || 'Failed to generate E-Way Bill';
            if (response.error_code) msg = '[' + response.error_code + '] ' + msg;
            alert_float('warning', msg);
          } else {
            alert_float('warning', response.message || 'Failed to generate E-Way Bill');
          }
        },
        error: function(xhr) {
          alert_float('danger', 'Server error while generating E-Way Bill');
          console.error(xhr.responseText);
        }
      });
    });
  }


  function getStockTransferDetails(id) {
    ResetForm();
    $.ajax({
      url: "<?= admin_url(); ?>StockTransfer/GetStockTransferDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        id: id
      },
      success: function(response) {
        if (response.success == true) {
          let d = response.data;

          $('#update_id').val(id);
          $('#TransferNo').val(d.TransferID);
          $('#TransferDate').val(moment(d.TransferDate).format('DD/MM/YYYY'));
          $('#VehicleNo').val(d.VehicleNo).selectpicker('refresh');
          $('#DriverName').val(d.DriverName);
          $('#Distance').val(d.Distance);
          $('#total_qty').val(d.TotalQuantity);
          $('#total_weight').val(d.TotalWeight);


          // From Location
          $('#FromLocation').val(d.FromLocationID).selectpicker('refresh');
          syncLocationOptions('from');

          getPlantLocationDetails('from', function() {
            $('#FromGodown').val(d.FromWHID).selectpicker('refresh');
            getPincode('from');

            loadChambers('from', null, function() {
              $('#FromChamber').val(d.FromChamberID).selectpicker('refresh');

              loadStacks('from', null, function() {
                $('#FromStack').val(d.FromStackID).selectpicker('refresh');

                loadLots('from', null, function() {
                  $('#FromLot').val(d.FromLotID).selectpicker('refresh');
                });
              });
            });
          });

          // To Location
          $('#ToLocation').val(d.ToLocationID).selectpicker('refresh');
          syncLocationOptions('to');

          getPlantLocationDetails('to', function() {
            $('#ToGodown').val(d.ToWHID).selectpicker('refresh');
            getPincode('to');

            loadChambers('to', null, function() {
              $('#ToChamber').val(d.ToChamberID).selectpicker('refresh');

              loadStacks('to', null, function() {
                $('#ToStack').val(d.ToStackID).selectpicker('refresh');

                loadLots('to', null, function() {
                  $('#ToLot').val(d.ToLotID).selectpicker('refresh');
                });
              });
            });
          });

          // E Way Bill Details
          if (d.isEwayBill === 'Y') {

            $('#ewayBillSection').show();

            $('#EWayBillNo').val(d.EwayBillNo).prop('readonly', true);

            $('#EWayBillDate')
              .val(moment(d.EWayBillDate).format('DD/MM/YYYY'))
              .prop('readonly', true);

            $('#EWayBillExpiryDate')
              .val(moment(d.EwayBillExpDate).format('DD/MM/YYYY'))
              .prop('readonly', true);

          } else {

            $('#ewayBillSection').hide();

          }

          $('#items_body').html('');
          $('#row_id').val(0);

          if (d.history && d.history.length > 0) {



            function processRow(index) {
              if (index >= d.history.length) {
                calculateTotals();
                if (d.isEwayBill === 'Y') {
                  $('#items_body input').prop('readonly', true);
                  $('#items_body select').prop('disabled', true).selectpicker('refresh');
                }
                return;
              }

              var item = d.history[index];
              addRow(2);
              var row = parseInt($('#row_id').val());

              $('#item_uid' + row).val(item.id || 0);
              $('#item_id' + row).val(item.ItemID || '');

              var savedQty = parseFloat(item.OrderQty || 0);

              $.ajax({
                url: '<?= admin_url('StockTransfer/GetItemDetails'); ?>',
                type: 'POST',
                data: {
                  item_id: item.ItemID
                },
                dataType: 'json',
                success: function(res) {
                  if (res.status === 'success' && res.data) {
                    var currentStock = parseFloat(res.data.CurrentStockQty || 0);

                    var fullAvailableStock = currentStock + savedQty;

                    $('#hsn_code' + row).val(res.data.hsn_code || '');
                    $('#uom' + row).val(res.data.unit || '');
                    $('#unit_weight' + row).val(Number(res.data.UnitWeight || 0).toFixed(2));

                    $('#current_stock_qty' + row)
                      .attr('data-stock', fullAvailableStock)
                      .val(fmt(fullAvailableStock - savedQty))
                      .css('color', (fullAvailableStock - savedQty) <= 0 ? 'red' : 'green');

                    $('#qty' + row).val(fmt(savedQty));

                    calculateAmount(row);
                  }
                  processRow(index + 1);
                },
                error: function() {
                  processRow(index + 1);
                }
              });
            }
            processRow(0);
          }

          $('.selectpicker').selectpicker('refresh');
          $('#form_mode').val('edit');
          $('.saveBtn').hide();
          $('.printBtn').show();
          $('.updateBtn').show();
          $('.eWayBillBtn').show();
          $('#ListModal').modal('hide');
          if (d.isEwayBill === 'Y') {
            $('#eway_lock_warning').show();

            // Disable buttons
            $('.updateBtn').prop('disabled', true);
            $('.eWayBillBtn').prop('disabled', true);

            // Lock all form inputs
            $('#main_save_form input:not([type="hidden"])').prop('readonly', true);
            $('#main_save_form select').prop('disabled', true).selectpicker('refresh');
            $('#main_save_form textarea').prop('readonly', true);

          } else {
            $('#eway_lock_warning').hide();

            $('#DriverName, #FromPincode, #ToPincode').prop('readonly', true);

            $('.updateBtn').prop('disabled', false);
            $('.eWayBillBtn').prop('disabled', false);
          }

        } else {
          alert_float('warning', response.message);
        }
      }
    });
  }

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
    if (form_mode == 'edit') {
      form_data.append('update_id', $('#update_id').val());
    }

    $.ajax({
      url: "<?= admin_url(); ?>StockTransfer/SaveStockTransfer",
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
          let d = response.data;
          let html = `<tr class="get_Details" data-id="${d.id}" onclick="getStockTransferDetails(${d.id})">
            <td>${d.TransferID}</td>
            <td>${moment(d.TransferDate).format('DD/MM/YYYY')}</td>
            <td>${d.FromLocation}</td>
            <td>${d.ToLocation}</td>
            <td>${d.Distance}</td>
            <td>${d.VehicleNo}</td>
            <td>${d.DriverName}</td>
            <td>${d.TotalQuantity}</td>
            <td>${d.TotalWeight}</td>
          </tr>`;
          if (form_mode == 'edit') {
            $('.get_Details[data-id="' + d.id + '"]').replaceWith(html);
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
      url: "<?= admin_url(); ?>StockTransfer/GetStockTransferDetails",
      method: "POST",
      dataType: "JSON",
      data: {
        id: id
      },
      success: function(response) {
        if (response.success == true) {
          let d = response.data;
          $('#update_id').val(id);
          let history = response.data.history;
          for (var i = 0; i < history.length; i++) {
            addRow(2);
            $('#item_uid' + (i + 1)).val(history[i].id);
            $('#item_id' + (i + 1)).val(history[i].ItemID);
            getItemDetails(history[i].ItemID, (i + 1));
            $('#qty' + (i + 1)).val(fmt(history[i].OrderQty));
            $('#max_qty' + (i + 1)).val(fmt(history[i].OrderQty));
            $('#unit_rate' + (i + 1)).val(fmt(history[i].BasicRate));
            $('#disc_amt' + (i + 1)).val(fmt(history[i].DiscAmt));
            $('#amount' + (i + 1)).val(fmt(history[i].NetOrderAmt));
            $('.selectpicker').selectpicker('refresh');
            calculateAmount(i + 1);
          }

          $('#OrderID').val(d.OrderID);
          $('#order_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
          $('#sales_location').val(d.SalesLocation);
          $('#customer_id').val(d.AccountID);

          $('#delivery_from').val(moment(d.DeliveryFrom).format('DD/MM/YYYY'));
          $('#delivery_to').val(moment(d.DeliveryTo).format('DD/MM/YYYY'));
          $('#payment_terms').val(d.PaymentTerms);
          $('#freight_terms').val(d.FreightTerms);

          $('.selectpicker').selectpicker('refresh');
          $('#form_mode').val('edit');
          $('.saveBtn').hide();
          $('.printBtn').show();
          $('.updateBtn').show();
          $('.eWayBillBtn').show();
          $('#ListModal').modal('hide');
        } else {
          alert_float('warning', response.message);
        }
      }
    });
  }


  var DataForBill = {};

  function GetDataForEWayBill(callback) {
    var id = $('#update_id').val();
    $.ajax({
      url: "<?= admin_url(); ?>StockTransfer/GetDataForEWayBill",
      method: "POST",
      dataType: "JSON",
      data: {
        id: id
      },
      success: function(response) {
        if (response.success == true) {
          DataForBill = response.data;

          if (callback) callback(DataForBill);
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

  .eWayBillBtn:hover {
    background: #5a32a3;
  }
</style>