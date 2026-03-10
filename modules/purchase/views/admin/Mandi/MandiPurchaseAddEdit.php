<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* ===== TABLE SCROLL FIX ===== */
.table-list { overflow: auto; max-height: 55vh; width:100%; position:relative; top: 0px; }
.table-list thead th { position: sticky; top: 0; z-index: 1; }
.table-list tbody th { position: sticky; left: 0; }

.items-table-wrapper {
  overflow-x: auto;
  overflow-y: visible;
  width: 100%;
  -webkit-overflow-scrolling: touch;
  position: relative;
}

table { border-collapse: collapse; width: 100%; }
th, td { padding: 3px 5px !important; white-space: nowrap; border:1px solid #ccc !important; font-size:11px; line-height:1.42857143 !important; vertical-align: middle !important;}
th { background: #50607b; color: #fff !important; }

#items_table { min-width: 1600px; width: 100%; table-layout: fixed; }

#items_table th:nth-child(1),  #items_table td:nth-child(1)  { width: 90px;  min-width: 90px;  }
#items_table th:nth-child(2),  #items_table td:nth-child(2)  { width: 200px; min-width: 200px; }
#items_table th:nth-child(3),  #items_table td:nth-child(3)  { width: 110px; min-width: 110px; }
#items_table th:nth-child(4),  #items_table td:nth-child(4)  { width: 70px;  min-width: 70px;  }
#items_table th:nth-child(5),  #items_table td:nth-child(5)  { width: 95px;  min-width: 95px;  }
#items_table th:nth-child(6),  #items_table td:nth-child(6)  { width: 90px;  min-width: 90px;  }
#items_table th:nth-child(7),  #items_table td:nth-child(7)  { width: 100px; min-width: 100px; }
#items_table th:nth-child(8),  #items_table td:nth-child(8)  { width: 110px; min-width: 110px; }
#items_table th:nth-child(9),  #items_table td:nth-child(9)  { width: 100px; min-width: 100px; }
#items_table th:nth-child(10), #items_table td:nth-child(10) { width: 90px;  min-width: 90px;  }
#items_table th:nth-child(11), #items_table td:nth-child(11) { width: 100px; min-width: 100px; }
#items_table th:nth-child(12), #items_table td:nth-child(12) { width: 85px;  min-width: 85px;  }
#items_table th:nth-child(13), #items_table td:nth-child(13) { width: 90px;  min-width: 90px;  }
#items_table th:nth-child(14), #items_table td:nth-child(14) { width: 75px;  min-width: 75px;  }
#items_table th:nth-child(15), #items_table td:nth-child(15) { width: 90px;  min-width: 90px;  }
#items_table th:nth-child(16), #items_table td:nth-child(16) { width: 90px;  min-width: 90px;  }
#items_table th:nth-child(17), #items_table td:nth-child(17) { width: 60px;  min-width: 60px;  }

#items_table input.form-control,
#items_table select.form-control {
  width: 100% !important;
  box-sizing: border-box;
  padding: 2px 4px !important;
  font-size: 11px !important;
  height: 26px !important;
}

#items_table .bootstrap-select { width: 100% !important; }
#items_table .bootstrap-select > .dropdown-toggle {
  width: 100% !important;
  height: 26px !important;
  padding: 2px 24px 2px 4px !important;
  font-size: 11px !important;
  line-height: 1.4 !important;
}

.tableFixHead2 { overflow: auto; max-height: 50vh; }
.sortable, .get_Details { cursor: pointer; }
.total-label-row { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #eee; }
.total-label-row .total-display { flex: 1; padding: 0; text-align: right; font-weight: 600; }
.fixed-td { max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.get_Details.processing { pointer-events: none; opacity: 0.9; }

/* Loading overlay for auto-load */
#autoLoadOverlay {
  display: none;
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(255,255,255,0.85);
  z-index: 9999;
  text-align: center;
  padding-top: 200px;
  font-size: 18px;
  color: #50607b;
}

@media only screen and (max-width: 600px) { #header ul { display: none !important; } }
</style>

<!-- PHP TDS rates JS embed - HEAD -->
<script>
  var tdsRateMap = {};
  <?php
  $tds_map_built = [];
  if (!empty($tds_code_list)) :
    foreach ($tds_code_list as $v) :
      if (!isset($tds_map_built[$v['TDSCode']])) :
        $tds_map_built[$v['TDSCode']] = $v['rate'] ?? 0;
        echo 'tdsRateMap["' . addslashes($v['TDSCode']) . '"] = ' . (floatval($v['rate'] ?? 0)) . ';' . "\n";
      endif;
    endforeach;
  endif;
  ?>
</script>

<!-- Auto Load Overlay -->
<div id="autoLoadOverlay">
  <div>
    <i class="fa fa-spinner fa-spin fa-2x"></i><br><br>
    <span>Loading record, please wait...</span>
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
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Mandi Purchase</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">

                <!-- * PO No. -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="purchase_order">
                    <label for="purchase_order" class="control-label"><small class="req text-danger">* </small> PO No.</label>
                    <input type="text" name="purchase_order" id="purchase_order" class="form-control" value="<?php echo $PurchID; ?>" app-field-label="PO No." required readonly>
                  </div>
                </div>

                <!-- * Document Date -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="inwards_date">
                    <?= render_date_input('inwards_date','Document Date', date('d/m/Y'), []); ?>
                  </div>
                </div>

                <!-- * Center/Location -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="location_id">
                    <label for="location_id" class="control-label"><small class="req text-danger">* </small>Center/Location</label>
                    <select name="location_id" id="location_id" class="form-control selectpicker"
                        data-live-search="true" app-field-label="Center" required
                        onchange="getGodownByLocation(this.value);">
                      <option value="" selected>None selected</option>
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

                <!-- * Godown -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="godown_id">
                    <label for="godown_id" class="control-label"><small class="req text-danger">* </small>Godown</label>
                    <select name="godown_id" id="godown_id" class="form-control selectpicker" data-live-search="true" app-field-label="Warehouse" required>
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>

                <!-- * Item ID -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="item_id_header">
                    <label for="item_id_header" class="control-label"><small class="req text-danger">* </small> Item ID</label>
                    <select name="item_id_header" id="item_id_header" class="form-control selectpicker" data-live-search="true" app-field-label="Item ID" required>
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($Items)):
                        foreach ($Items as $value):
                          echo '<option value="' . $value['ItemID'] . '">' . $value['ItemName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>

                <!-- TDS Code + TDS Rate -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="tds_code">
                    <label for="tds_code" class="control-label">TDS Code</label>
                    <select name="tds_code" id="tds_code" class="form-control selectpicker" data-live-search="true" app-field-label="TDS Code" onchange="onTdsCodeChange(this.value);">
                      <option value="" selected>None selected</option>
                      <?php
                      $shown_tds = [];
                      if (!empty($tds_code_list)) :
                        foreach ($tds_code_list as $value) :
                          if (!in_array($value['TDSCode'], $shown_tds)) :
                            $shown_tds[] = $value['TDSCode'];
                            echo '<option value="' . $value['TDSCode'] . '">' . $value['TDSName'] . ' (' . $value['TDSCode'] . ')</option>';
                          endif;
                        endforeach;
                      endif;
                      ?>
                    </select>
                    <input type="hidden" id="tds_per_h" name="tds_per_h" class="form-control" value="0" placeholder="TDS Rate %" readonly style="margin-top:4px; background:#f5f5f5; font-weight:bold;">
                  </div>
                </div>

                <!-- Vehicle No. -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vehicle_no">
                    <label for="vehicle_no" class="control-label">Vehicle No.</label>
                    <input type="text" name="vehicle_no" id="vehicle_no" class="form-control" placeholder="MH12AB3432" app-field-label="Vehicle No.">
                  </div>
                </div>

                <!-- Hidden: TDS Per for header row -->
                <input type="hidden" id="tds_per_header" value="0">

                <div class="col-md-12 mbot5">
                  <h4 class="bold p_style">Purchase order detail:</h4>
                  <hr class="hr_style">
                </div>

                <!-- ===== ITEMS TABLE ===== -->
                <div class="col-md-12 mbot5">
                  <input type="hidden" id="row_id" value="0">
                  <div class="items-table-wrapper">
                    <table width="100%" class="table" id="items_table">
                      <thead>
                        <tr style="text-align: center;">
                          <th>Doc No.</th>
                          <th>Vendor Code &amp; Name</th>
                          <th>Payment Term</th>
                          <th>Bag</th>
                          <th>Weight / Bag</th>
                          <th>Loose (kg)</th>
                          <th>Qty (Quintal)</th>
                          <th>Rate / Quintal</th>
                          <th>Value</th>
                          <th>Brokerage</th>
                          <th>Market Levy</th>
                          <th>Round Off</th>
                          <th>Gross</th>
                          <th>TDS %</th>
                          <th>TDS Amt</th>
                          <th>Net Amt</th>
                          <th>Action</th>
                        </tr>
                        <tr>
                          <td><input type="text" id="doc_no" class="form-control fixed_row" placeholder="Doc No."></td>
                          <td>
                            <select id="vendor_id" class="form-control fixed_row dynamic_row dynamic_item selectpicker" data-live-search="true" data-container="body" app-field-label="Vendor Code & Name" onchange="getItemDetails(this.value, '');">
                              <option value="" selected disabled>Select Vendor</option>
                              <?php
                              if (!empty($vendor_list)):
                                  foreach ($vendor_list as $value):
                                      echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . '- ' . $value['billing_state'] . '- (' . $value['AccountID'] . ')</option>';
                                  endforeach;
                              endif;
                              ?>
                            </select>
                          </td>
                          <td><input type="text" id="payment_term" class="form-control fixed_row" placeholder="Payment Term" readonly></td>
                          <td><input type="tel" id="bag" class="form-control fixed_row" min="0" step="1" placeholder="Bag" oninput="calculateAmountHeader()"></td>
                          <td><input type="tel" id="weight_per_bag" class="form-control fixed_row" min="0" step="0.01" placeholder="Wt./Bag (kg)" oninput="calculateAmountHeader()"></td>
                          <td><input type="tel" id="loose_kg" class="form-control fixed_row" min="0" step="0.01" placeholder="Loose (kg)" oninput="calculateAmountHeader()"></td>
                          <td><input type="tel" id="qty_quintal" class="form-control fixed_row" min="0" step="0.01" placeholder="Qty (Qtl)" readonly tabindex="-1"></td>
                          <td><input type="tel" id="rate_quintal" class="form-control fixed_row" min="0" step="0.01" placeholder="Rate/Qtl" oninput="calculateAmountHeader()"></td>
                          <td><input type="tel" id="value" class="form-control fixed_row" placeholder="Value" readonly tabindex="-1"></td>
                          <td><input type="tel" id="brokerage" class="form-control fixed_row" min="0" step="0.01" placeholder="Brokerage" oninput="calculateAmountHeader()"></td>
                          <td><input type="tel" id="market_levy" class="form-control fixed_row" min="0" step="0.01" placeholder="Market Levy" oninput="calculateAmountHeader()"></td>
                          <td><input type="tel" id="round_off" class="form-control fixed_row" placeholder="Round Off" readonly tabindex="-1"></td>
                          <td><input type="tel" id="gross" class="form-control fixed_row" placeholder="Gross" readonly tabindex="-1"></td>
                          <td><input type="tel" id="tds_per_input" class="form-control fixed_row" min="0" step="0.01" placeholder="TDS %" oninput="calculateAmountHeader()" readonly></td>
                          <td><input type="tel" id="tds_amt" class="form-control fixed_row" placeholder="TDS Amt" readonly tabindex="-1"></td>
                          <td><input type="tel" id="net_amt_row" class="form-control fixed_row" placeholder="Net Amt" readonly tabindex="-1"></td>
                          <td>
                            <button type="button" class="btn btn-success btn-sm" onclick="addRow();"><i class="fa fa-plus"></i></button>
                          </td>
                        </tr>
                      </thead>
                      <tbody id="items_body"></tbody>
                    </table>
                  </div>
                </div>

                <!-- Total Summary -->
                <div class="col-md-6"></div>
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <h4 class="bold p_style">Total Summary</h4>
                      <hr class="hr_style">
                    </div>

                    <input type="hidden" name="total_qty_quintal" id="total_qty_quintal_hidden" value="0">
                    <input type="hidden" name="total_value" id="total_value_hidden" value="0">
                    <input type="hidden" name="total_brokerage" id="total_brokerage_hidden" value="0">
                    <input type="hidden" name="total_market_levy" id="total_market_levy_hidden" value="0">
                    <input type="hidden" name="total_gross_value" id="total_gross_value_hidden" value="0">
                    <input type="hidden" name="tds" id="tds_hidden" value="0">
                    <input type="hidden" name="total_net_value" id="total_net_value_hidden" value="0">

                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row">
                        <label>Qty (Quintal):</label>
                        <div class="total-display" id="total_qty_quintal_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Total Value:</label>
                        <div class="total-display" id="total_value_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Total Brokerage:</label>
                        <div class="total-display" id="total_brokerage_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Total Market Levy:</label>
                        <div class="total-display" id="total_market_levy_display">0.00</div>
                      </div>
                    </div>

                    <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="total-label-row">
                        <label>Total Gross Value:</label>
                        <div class="total-display" id="total_gross_value_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>TDS:</label>
                        <div class="total-display" id="tds_display">0.00</div>
                      </div>
                      <div class="total-label-row">
                        <label>Total Net Value:</label>
                        <div class="total-display" id="total_net_value_display">0.00</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Fixed Bottom Buttons -->
                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <!-- Back to List button - only show when redirected from list page -->
                  <a href="<?= admin_url('purchase/Mandi/list'); ?>" class="btn btn-default" id="backToListBtn" style="display:none;">
                    <i class="fa fa-arrow-left"></i> Back to List
                  </a>
                  <a href="#" class="btn btn-primary updateBtn" id="print_pdf" style="display: none;" target="_blank" onclick="printMandiPurchaseOrderPdf();"><i class="fa fa-print"></i> Print PDF</a>
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

<!-- ===== LIST MODAL ===== -->
<div class="modal fade" id="ListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="width: 90vw;">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Mandi Purchase List</h4>
      </div>
      <div class="modal-body" style="padding:5px 5px !important">
        <form action="" method="post" id="filter_list_form">
          <div class="row">
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="from_date">
                    <?= render_date_input('from_date','From Date', date('01/m/Y'), []); ?>
                  </div>
              <!-- <div class="form-group">
                <label class="control-label">From Date</label>
                <div class="input-group date">
                  <input type="text" id="from_date" name="from_date" class="form-control datepicker" value="<?= date("01/m/Y") ?>">
                  <div class="input-group-addon"><i class="fa-regular fa-calendar calendar-icon"></i></div>
                </div>
              </div> -->
            </div>
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="to_date">
                    <?= render_date_input('to_date','To Date', date('d/m/Y'), []); ?>
                  </div>
              <!-- <div class="form-group">
                <label class="control-label">To Date</label>
                <div class="input-group date">
                  <input type="text" id="to_date" name="to_date" class="form-control datepicker" value="<?= date("d/m/Y") ?>">
                  <div class="input-group-addon"><i class="fa-regular fa-calendar calendar-icon"></i></div>
                </div>
              </div> -->
            </div>
            <div class="col-md-5 mbot5" style="padding-top:20px;">
              <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
            </div>
            <div class="col-md-3 mbot5" style="padding-top:20px;">
              <input type="search" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search..." title="Type in a table">
            </div>
          </div>
        </form>

        <div class="progress" style="margin-bottom: 5px; height: 3px;">
          <div id="fetchProgress" class="progress-bar" style="width:0%"></div>
        </div>

        <div class="tableFixHead2">
          <table class="table table-striped table-bordered" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortable">#</th>
                <th class="sortable">Order ID</th>
                <th class="sortable">Order Date</th>
                <th class="sortable">Trans Date</th>
                <th class="sortable">Center / Location</th>
                <th class="sortable">Warehouse</th>
                <th class="sortable">Item</th>
                <th class="sortable">Vehicle No.</th>
                <th class="sortable">Final Amt</th>
              </tr>
            </thead>
            <tbody id="table_ListModal_body"></tbody>
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

        $('#inwards_date').datetimepicker({
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

  // =============================================
  // PRINT PDF
  // =============================================
  function printMandiPurchaseOrderPdf() {
    var orderNo = $('#purchase_order').val();
    if (!orderNo) { alert_float('warning', 'Order No not found!'); return; }
    var url = "<?= admin_url('purchase/Mandi/printMandiPurchaseOrderPdf/'); ?>" + orderNo;
    window.open(url, '_blank');
  }

  // =============================================
  // INITIALIZE SELECTPICKERS + AUTO LOAD FROM URL
  // =============================================
  $(document).ready(function() {
    $('#vendor_id').selectpicker({ container: 'body', liveSearch: true });
    $('#tds_code').selectpicker({ liveSearch: true });

    // ✅ Check if URL has order_id & id (redirect from List page)
    var urlParams  = new URLSearchParams(window.location.search);
    var orderIdUrl = urlParams.get('order_id');
    var idUrl      = urlParams.get('id');

    if (orderIdUrl && idUrl) {
      // Show Back to List button only when redirected from list page
      $('#backToListBtn').show();
      // Show loading overlay
      $('#autoLoadOverlay').show();
      // Auto load the record
      getMandiRecord(idUrl, orderIdUrl, null);
    }
  });

  // =============================================
  // REFRESH SELECTPICKERS
  // =============================================
  function refreshSelectpickers() {
    $('#vendor_id').selectpicker('refresh');
    $('#tds_code').selectpicker('refresh');
    $('#items_body .selectpicker').each(function() {
      var $el = $(this);
      if (!$el.data('selectpicker')) {
        $el.selectpicker({ container: 'body', liveSearch: true });
      } else {
        $el.selectpicker('refresh');
      }
    });
    $('.selectpicker').not('#items_table .selectpicker').selectpicker('refresh');
  }

  // =============================================
  // TDS CODE SELECT → tds_per_h rate show
  // =============================================
  function onTdsCodeChange(tdsCode) {
    if (tdsCode && tdsRateMap.hasOwnProperty(tdsCode)) {
      $('#tds_per_h').val(tdsRateMap[tdsCode]);
    } else {
      $('#tds_per_h').val('0');
    }
    calculateTotals();
  }

  // =============================================
  // RESET FORM
  // =============================================
  function ResetForm(skipNewID) {
    $('#main_save_form')[0].reset();
    $('#form_mode').val('add');
    $('#update_id').val('');
    $('.updateBtn').hide();
    $('.saveBtn').show();
    $('#items_body').html('');
    $('.total-display').text('0.00');
    $('#row_id').val(0);
    $('#tds_per_header').val(0);
    $('#tds_per_h').val('0');

    // ✅ Hide Back to List button on reset
    $('#backToListBtn').hide();

    $('#vendor_id').html(`
      <option value="" selected>None selected</option>
      <?php
      if (!empty($vendor_list)) :
        foreach ($vendor_list as $value) :
          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
        endforeach;
      endif;
      ?>
    `);

    $('#location_id').val('');
    $('#godown_id').html('<option value="" selected>None selected</option>');

    $('#item_id_header').selectpicker('destroy');
    $('#item_id_header').html(`
      <option value="" selected>None selected</option>
      <?php
      if (!empty($Items)):
        foreach ($Items as $value):
          echo '<option value="' . $value['ItemID'] . '">' . $value['ItemName'] . '</option>';
        endforeach;
      endif;
      ?>
    `);
    $('#item_id_header').selectpicker();

    $('#tds_code').val('');
    $('#vehicle_no').val('');
    $('#payment_term').val('');
    refreshSelectpickers();

    // Clear URL parameters (without page reload)
    if (window.history && window.history.replaceState) {
      var cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
      window.history.replaceState({}, document.title, cleanUrl);
    }

    if (!skipNewID) {
      $.ajax({
        url: '<?= admin_url("purchase/Mandi/getNewPurchaseID"); ?>',
        type: 'POST',
        dataType: 'json',
        data: { '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val() },
        success: function(response) {
          if (response.success == true) { $('#purchase_order').val(response.PurchID); }
        }
      });
    }
  }

  $(document).on('input', 'input[type="tel"]', function () {
    this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
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

  // =============================================
  // HEADER ROW - CALCULATION
  // =============================================
  function calculateAmountHeader() {
    var bag        = parseFloat($('#bag').val()) || 0;
    var wtPerBag   = parseFloat($('#weight_per_bag').val()) || 0;
    var looseKg    = parseFloat($('#loose_kg').val()) || 0;
    var rateQ      = parseFloat($('#rate_quintal').val()) || 0;
    var brokerage  = parseFloat($('#brokerage').val()) || 0;
    var marketLevy = parseFloat($('#market_levy').val()) || 0;
    var tdsPer     = parseFloat($('#tds_per_input').val()) || 0;

    var totalKg      = (bag * wtPerBag) + looseKg;
    var qtyQ         = totalKg / 100;
    var value        = qtyQ * rateQ;
    var gross        = value + brokerage + marketLevy;
    var grossRounded = Math.round(gross);
    var roundOff     = grossRounded - gross;
    var tdsAmt       = (grossRounded * tdsPer) / 100;
    var netAmt       = grossRounded - tdsAmt;

    $('#qty_quintal').val(qtyQ.toFixed(4));
    $('#value').val(value.toFixed(2));
    $('#round_off').val(roundOff.toFixed(2));
    $('#gross').val(grossRounded.toFixed(2));
    $('#tds_amt').val(tdsAmt.toFixed(2));
    $('#net_amt_row').val(netAmt.toFixed(2));
    calculateTotals();
  }

  // =============================================
  // VENDOR SELECT → AJAX → PaymentTerms & TDSPer
  // =============================================
  function getItemDetails(vendorId, id) {
    var isDuplicate = false;
    $('.dynamic_item').not('#vendor_id' + id).each(function () {
      if ($(this).val() == vendorId && vendorId != '') { isDuplicate = true; return false; }
    });
    if (isDuplicate) {
      alert_float('warning', 'Please select other vendor, this vendor already selected.');
      $('#vendor_id' + id).val('').focus();
      refreshSelectpickers();
      return;
    }

    if (vendorId === '' || vendorId === null) {
      if (id === '' || id === 0) {
        $('#payment_term').val(''); $('#tds_per_input').val(''); $('#tds_amt').val(''); $('#tds_per_header').val(0);
      } else {
        $('#payment_term' + id).val(''); $('#tds_per_input' + id).val(''); $('#tds_amt' + id).val('');
      }
      calculateTotals();
      return;
    }

    $.ajax({
      url: '<?= admin_url("purchase/Mandi/getVendorTerms"); ?>',
      type: 'POST',
      dataType: 'json',
      data: {
        vendor_id: vendorId,
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      success: function (response) {
        if (response.success == true) {
          var payTerm = response.data.PaymentTerms || '';
          var tdsPer  = parseFloat(response.data.TDSPer) || 0;
          if (id === '' || id === 0) {
            $('#payment_term').val(payTerm); $('#tds_per_input').val(tdsPer); $('#tds_per_header').val(tdsPer);
            calculateAmountHeader();
          } else {
            $('#payment_term' + id).val(payTerm); $('#tds_per_input' + id).val(tdsPer);
            calculateAmount(id);
          }
        } else {
          if (id === '' || id === 0) { $('#payment_term').val(''); $('#tds_per_input').val(''); $('#tds_amt').val(''); }
          else { $('#payment_term' + id).val(''); $('#tds_per_input' + id).val(''); $('#tds_amt' + id).val(''); }
          alert_float('warning', response.message || 'Vendor details not found.');
          calculateTotals();
        }
      },
      error: function () { alert_float('warning', 'Error fetching vendor details. Please try again.'); }
    });
  }

  function addRow(row) {
    $('#vendor_id').focus();
    var row_id  = $('#row_id').val();
    var next_id = parseInt(row_id) + 1;

    if (row == null || row === undefined) {
      let fields = ['vendor_id', 'bag', 'weight_per_bag', 'rate_quintal'];
      let data = validate_fields(fields);
      if (data === false) return false;
    }

    var row_btn    = `<button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove(); calculateTotals();" style="padding: 2px 6px;"><i class="fa fa-xmark"></i></button>`;
    let item_option = $('#vendor_id').html();

    $('#items_body').append(`
      <tr>
        <td><input type="text" name="doc_no[]" id="doc_no${next_id}" class="form-control" placeholder="Doc No." value="${$('#doc_no').val()}"></td>
        <td>
          <input type="hidden" name="item_uid[]" id="item_uid${next_id}" value="0">
          <select id="vendor_id${next_id}" name="vendor_id[]" class="form-control dynamic_item selectpicker" data-live-search="true" data-container="body" onchange="getItemDetails(this.value, '${next_id}');">${item_option}</select>
        </td>
        <td><input type="text" name="payment_term[]" id="payment_term${next_id}" class="form-control" placeholder="Payment Term" readonly value="${$('#payment_term').val()}"></td>
        <td><input type="tel" name="bag[]" id="bag${next_id}" class="form-control bag" min="0" step="1" placeholder="Bag" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="weight_per_bag[]" id="weight_per_bag${next_id}" class="form-control weight-per-bag" min="0" step="0.01" placeholder="Wt./Bag (kg)" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="loose_kg[]" id="loose_kg${next_id}" class="form-control loose-kg" min="0" step="0.01" placeholder="Loose (kg)" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="qty_quintal[]" id="qty_quintal${next_id}" class="form-control qty-quintal" placeholder="Qty (Qtl)" readonly tabindex="-1"></td>
        <td><input type="tel" name="rate_quintal[]" id="rate_quintal${next_id}" class="form-control rate-quintal" min="0" step="0.01" placeholder="Rate/Qtl" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="value[]" id="value${next_id}" class="form-control row-value" placeholder="Value" readonly tabindex="-1"></td>
        <td><input type="tel" name="brokerage[]" id="brokerage${next_id}" class="form-control row-brokerage" min="0" step="0.01" placeholder="Brokerage" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="market_levy[]" id="market_levy${next_id}" class="form-control row-market-levy" min="0" step="0.01" placeholder="Market Levy" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="round_off[]" id="round_off${next_id}" class="form-control row-round-off" placeholder="Round Off" readonly tabindex="-1"></td>
        <td><input type="tel" name="gross[]" id="gross${next_id}" class="form-control row-gross" placeholder="Gross" readonly tabindex="-1"></td>
        <td><input type="tel" name="tds_per_input[]" id="tds_per_input${next_id}" class="form-control row-tds-per" min="0" step="0.01" placeholder="TDS %" oninput="calculateAmount(${next_id})" readonly></td>
        <td><input type="tel" name="tds_amt[]" id="tds_amt${next_id}" class="form-control row-tds" placeholder="TDS Amt" readonly tabindex="-1"></td>
        <td><input type="tel" name="net_amt_row[]" id="net_amt_row${next_id}" class="form-control row-net-amt" placeholder="Net Amt" readonly tabindex="-1"></td>
        <td>${row_btn}</td>
      </tr>
    `);

    $('#vendor_id' + next_id).selectpicker({ container: 'body', liveSearch: true });

    if (row == null || row === undefined) {
      $(`#vendor_id${next_id}`).val($('#vendor_id').val());
      $(`#bag${next_id}`).val($('#bag').val());
      $(`#weight_per_bag${next_id}`).val($('#weight_per_bag').val());
      $(`#loose_kg${next_id}`).val($('#loose_kg').val());
      $(`#rate_quintal${next_id}`).val($('#rate_quintal').val());
      $(`#brokerage${next_id}`).val($('#brokerage').val());
      $(`#market_levy${next_id}`).val($('#market_levy').val());
      $(`#tds_per_input${next_id}`).val($('#tds_per_input').val());
      $(`#payment_term${next_id}`).val($('#payment_term').val());
      $('.fixed_row').val('');
      calculateAmount(next_id);
    }

    $('#vendor_id' + next_id).selectpicker('refresh');
    $('#row_id').val(next_id);
  }

  // =============================================
  // DYNAMIC ROWS - CALCULATION
  // =============================================
  function calculateAmount(row) {
    var bag        = parseFloat($('#bag'+row).val()) || 0;
    var wtPerBag   = parseFloat($('#weight_per_bag'+row).val()) || 0;
    var looseKg    = parseFloat($('#loose_kg'+row).val()) || 0;
    var rateQ      = parseFloat($('#rate_quintal'+row).val()) || 0;
    var brokerage  = parseFloat($('#brokerage'+row).val()) || 0;
    var marketLevy = parseFloat($('#market_levy'+row).val()) || 0;
    var tdsPer     = parseFloat($('#tds_per_input'+row).val()) || 0;

    var totalKg      = (bag * wtPerBag) + looseKg;
    var qtyQ         = totalKg / 100;
    var value        = qtyQ * rateQ;
    var gross        = value + brokerage + marketLevy;
    var grossRounded = Math.round(gross);
    var roundOff     = grossRounded - gross;
    var tdsAmt       = (grossRounded * tdsPer) / 100;
    var netAmt       = grossRounded - tdsAmt;

    $('#qty_quintal'+row).val(qtyQ.toFixed(4));
    $('#value'+row).val(value.toFixed(2));
    $('#round_off'+row).val(roundOff.toFixed(2));
    $('#gross'+row).val(grossRounded.toFixed(2));
    $('#tds_amt'+row).val(tdsAmt.toFixed(2));
    $('#net_amt_row'+row).val(netAmt.toFixed(2));
    calculateTotals();
  }

  // =============================================
  // TOTALS
  // =============================================
  function calculateTotals() {
    var totalQtyQ       = parseFloat($('#qty_quintal').val())  || 0;
    var totalValue      = parseFloat($('#value').val())        || 0;
    var totalBrokerage  = parseFloat($('#brokerage').val())    || 0;
    var totalMarketLevy = parseFloat($('#market_levy').val())  || 0;
    var totalGross      = parseFloat($('#gross').val())        || 0;

    $('#items_body tr').each(function () {
      var row = $(this);
      totalQtyQ       += parseFloat(row.find('.qty-quintal').val())     || 0;
      totalValue      += parseFloat(row.find('.row-value').val())       || 0;
      totalBrokerage  += parseFloat(row.find('.row-brokerage').val())   || 0;
      totalMarketLevy += parseFloat(row.find('.row-market-levy').val()) || 0;
      totalGross      += parseFloat(row.find('.row-gross').val())       || 0;
    });

    var tdsPerH  = parseFloat($('#tds_per_h').val()) || 0;
    var tdsAmtH  = (totalGross * tdsPerH) / 100;
    var netAmtH  = totalGross - tdsAmtH;

    $('#total_qty_quintal_display').text(totalQtyQ.toFixed(4));
    $('#total_value_display').text(totalValue.toFixed(2));
    $('#total_brokerage_display').text(totalBrokerage.toFixed(2));
    $('#total_market_levy_display').text(totalMarketLevy.toFixed(2));
    $('#total_gross_value_display').text(totalGross.toFixed(2));
    $('#tds_display').text(tdsAmtH.toFixed(2));
    $('#total_net_value_display').text(netAmtH.toFixed(2));

    $('#total_qty_quintal_hidden').val(totalQtyQ.toFixed(4));
    $('#total_value_hidden').val(totalValue.toFixed(2));
    $('#total_brokerage_hidden').val(totalBrokerage.toFixed(2));
    $('#total_market_levy_hidden').val(totalMarketLevy.toFixed(2));
    $('#total_gross_value_hidden').val(totalGross.toFixed(2));
    $('#tds_hidden').val(tdsAmtH.toFixed(2));
    $('#total_net_value_hidden').val(netAmtH.toFixed(2));
  }

  function get_required_fields(form_id) {
    let fields = [];
    $('#' + form_id + ' [required]').each(function(){ fields.push($(this).attr('id')); });
    return fields;
  }

  // =============================================
  // BUILD JSON DATA FOR SAVE
  // =============================================
  function buildFormJSON() {
    let jsonData = { items: [] };
    var headerVendor = $('#vendor_id').val();
    if (headerVendor && headerVendor !== '') {
      jsonData.items.push({
        doc_no: $('#doc_no').val(), vendor_id: headerVendor, payment_term: $('#payment_term').val(),
        bag: parseFloat($('#bag').val()) || 0, weight_per_bag: parseFloat($('#weight_per_bag').val()) || 0,
        loose_kg: parseFloat($('#loose_kg').val()) || 0, qty_quintal: parseFloat($('#qty_quintal').val()) || 0,
        rate_quintal: parseFloat($('#rate_quintal').val()) || 0, value: parseFloat($('#value').val()) || 0,
        brokerage: parseFloat($('#brokerage').val()) || 0, market_levy: parseFloat($('#market_levy').val()) || 0,
        round_off: parseFloat($('#round_off').val()) || 0, gross: parseFloat($('#gross').val()) || 0,
        tds_per: parseFloat($('#tds_per_input').val()) || 0, tds_amt: parseFloat($('#tds_amt').val()) || 0,
        net_amt: parseFloat($('#net_amt_row').val()) || 0
      });
    }
    $('#items_body tr').each(function() {
      var $row = $(this);
      jsonData.items.push({
        doc_no: $row.find('[name="doc_no[]"]').val(), vendor_id: $row.find('[name="vendor_id[]"]').val(),
        payment_term: $row.find('[name="payment_term[]"]').val(),
        bag: parseFloat($row.find('[name="bag[]"]').val()) || 0,
        weight_per_bag: parseFloat($row.find('[name="weight_per_bag[]"]').val()) || 0,
        loose_kg: parseFloat($row.find('[name="loose_kg[]"]').val()) || 0,
        qty_quintal: parseFloat($row.find('[name="qty_quintal[]"]').val()) || 0,
        rate_quintal: parseFloat($row.find('[name="rate_quintal[]"]').val()) || 0,
        value: parseFloat($row.find('[name="value[]"]').val()) || 0,
        brokerage: parseFloat($row.find('[name="brokerage[]"]').val()) || 0,
        market_levy: parseFloat($row.find('[name="market_levy[]"]').val()) || 0,
        round_off: parseFloat($row.find('[name="round_off[]"]').val()) || 0,
        gross: parseFloat($row.find('[name="gross[]"]').val()) || 0,
        tds_per: parseFloat($row.find('[name="tds_per_input[]"]').val()) || 0,
        tds_amt: parseFloat($row.find('[name="tds_amt[]"]').val()) || 0,
        net_amt: parseFloat($row.find('[name="net_amt_row[]"]').val()) || 0
      });
    });
    return jsonData;
  }

  // =============================================
  // FORM SUBMIT
  // =============================================
  $('#main_save_form').on('submit', function(e) {
    e.preventDefault();
    let form_mode       = $('#form_mode').val();
    let required_fields = get_required_fields('main_save_form');
    let validated       = validate_fields(required_fields);
    if (validated === false) return;

    let jsonPayload = buildFormJSON();
    var form_data   = new FormData(this);
    form_data.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
    form_data.append('form_json', JSON.stringify(jsonPayload));
    if (form_mode == 'edit') { form_data.append('update_id', $('#update_id').val()); }

    $.ajax({
      url: "<?= admin_url(); ?>purchase/Mandi/SaveMandiPurchase",
      method: "POST", dataType: "JSON", data: form_data,
      contentType: false, cache: false, processData: false,
      beforeSend: function () { $('button[type=submit]').attr('disabled', true); },
      complete:   function () { $('button[type=submit]').attr('disabled', false); },
      success: function(response) {
        if (response.success == true) {
          alert_float('success', response.message);
          setTimeout(function(){ ResetForm(); }, 2000);
        } else {
          alert_float('warning', response.message);
        }
      }
    });
  });

  // =============================================
  // MANDI LIST (Modal)
  // =============================================
  $('#filter_list_form').on('submit', function(e) {
    e.preventDefault();
    $('#searchBtn').prop('disabled', true);
    $('#table_ListModal_body').html('<tr><td colspan="9" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

    $.ajax({
      url: '<?= admin_url("purchase/Mandi/GetMandiDetails"); ?>',
      type: 'POST', dataType: 'json',
      data: {
        from_date: $('#from_date').val(), to_date: $('#to_date').val(),
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      success: function(response) {
        $('#searchBtn').prop('disabled', false);
        $('#fetchProgress').css('width', '100%');
        setTimeout(function(){ $('#fetchProgress').css('width', '0%'); }, 500);

        if (!response.success || !response.data || response.data.length === 0) {
          $('#table_ListModal_body').html('<tr><td colspan="9" class="text-center text-muted">No records found.</td></tr>');
          return;
        }
        var html = '';
        $.each(response.data, function(i, row) {
          var orderDate = row.OrderDate ? moment(row.OrderDate).format('DD/MM/YYYY') : '-';
          var transDate = row.TransDate ? moment(row.TransDate).format('DD/MM/YYYY') : '-';
          var finalAmt  = row.FinalAmt  ? parseFloat(row.FinalAmt).toFixed(2) : '0.00';
          html += `<tr class="get_Details" style="cursor:pointer;" onclick="getMandiRecord(${row.id}, '${row.OrderID}', this)">
            <td>${i+1}</td><td>${row.OrderID||'-'}</td><td>${orderDate}</td><td>${transDate}</td>
            <td>${row.CenterLocation||'-'}</td><td>${row.WarehouseID||'-'}</td><td>${row.ItemID||'-'}</td>
            <td>${row.VehicleNo||'-'}</td><td class="text-right">${finalAmt}</td></tr>`;
        });
        $('#table_ListModal_body').html(html);
      },
      error: function() {
        $('#searchBtn').prop('disabled', false);
        $('#table_ListModal_body').html('<tr><td colspan="9" class="text-center text-danger">Error loading data.</td></tr>');
      }
    });
  });

  // =============================================
  // ROW CLICK → Load record into form
  // (Works from both Modal click AND URL auto-load)
  // =============================================
  function getMandiRecord(id, orderId, rowEl) {
    // rowEl can be null when called from URL auto-load
    var $row = rowEl ? $(rowEl).closest('tr') : null;
    if ($row && $row.hasClass('processing')) return;
    if ($row) $row.addClass('processing');

    $.ajax({
      url: '<?= admin_url("purchase/Mandi/GetMandiDetailsall"); ?>',
      type: 'POST', dataType: 'json',
      data: {
        id: id, order_id: orderId,
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      complete: function() {
        if ($row) $row.removeClass('processing');
        // Hide loading overlay after complete
        $('#autoLoadOverlay').hide();
      },
      success: function(response) {
        if (response.status && response.data && response.data.length > 0) {
          var d  = response.data[0];
          var d1 = response.data1 || [];

          ResetForm(true);
          $('#form_mode').val('edit');
          $('#update_id').val(id);
          $('.saveBtn').hide();
          $('.updateBtn').show();
          $('#ListModal').modal('hide');

          // Header fields
          $('#purchase_order').val(d.OrderID || '');
          if (d.TransDate) $('#inwards_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
          if (d.VehicleNo) $('#vehicle_no').val(d.VehicleNo);

          // TDS Code + rate auto-fill
          if (d.TDSCode) {
            $('#tds_code').val(d.TDSCode);
            $('#tds_code').selectpicker('refresh');
            onTdsCodeChange(d.TDSCode);
          }

          // Item ID
          if (d.ItemID) {
            $('#item_id_header').val(d.ItemID);
            $('#item_id_header').selectpicker('refresh');
          }

          // Location → Godown AJAX
          if (d.CenterLocation) {
            $('#location_id').val(d.CenterLocation);
            refreshSelectpickers();
            $.ajax({
              url: '<?= admin_url("purchase/Mandi/getGodownByLocation"); ?>',
              type: 'POST', dataType: 'json',
              data: {
                location_id: d.CenterLocation,
                '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
              },
              success: function(gRes) {
                if (gRes.success && gRes.data.length > 0) {
                  var gHtml = '<option value="">None selected</option>';
                  $.each(gRes.data, function(i, v) { gHtml += '<option value="' + v.id + '">' + v.GodownName + '</option>'; });
                  $('#godown_id').selectpicker('destroy');
                  $('#godown_id').html(gHtml);
                  $('#godown_id').selectpicker();
                  if (d.WarehouseID) { $('#godown_id').val(d.WarehouseID); $('#godown_id').selectpicker('refresh'); }
                }
              }
            });
          }

          // Items table from data1
          if (d1.length > 0) {
            var first = d1[0];
            var firstTdsPer = parseFloat(first.Gross || 0) > 0
              ? ((parseFloat(first.HistoryTDSAmt || 0) / parseFloat(first.Gross)) * 100).toFixed(2)
              : 0;

            $('#vendor_id').val(first.VendorID || '');
            refreshSelectpickers();
            $('#doc_no').val(first.DocumentNo || '');
            $('#payment_term').val(first.PaymentTerm || '');
            $('#bag').val(first.BagQty || '');
            $('#weight_per_bag').val(first.WeightPerBag || '');
            $('#loose_kg').val(first.LooseKG || '');
            $('#rate_quintal').val(first.RatePerQuintal || '');
            $('#brokerage').val(first.Brokerage || '');
            $('#market_levy').val(first.MarketLevy || '');
            $('#tds_per_input').val(firstTdsPer);
            $('#tds_per_header').val(firstTdsPer);
            $('#qty_quintal').val(parseFloat(first.QtyQuintal || 0).toFixed(4));
            $('#value').val(parseFloat(first.Value || 0).toFixed(2));
            $('#round_off').val(parseFloat(first.RoundOff || 0).toFixed(2));
            $('#gross').val(parseFloat(first.Gross || 0).toFixed(2));
            $('#tds_amt').val(parseFloat(first.HistoryTDSAmt || 0).toFixed(2));
            $('#net_amt_row').val(parseFloat(first.NetAmt || 0).toFixed(2));

            var vendorOptions = $('#vendor_id').html();

            for (var i = 1; i < d1.length; i++) {
              (function(item) {
                var row_id = parseInt($('#row_id').val()) + 1;
                $('#row_id').val(row_id);
                var tdsPer = parseFloat(item.Gross || 0) > 0
                  ? ((parseFloat(item.HistoryTDSAmt || 0) / parseFloat(item.Gross)) * 100).toFixed(2)
                  : 0;

                $('#items_body').append(`
                  <tr>
                    <td><input type="text" name="doc_no[]" id="doc_no${row_id}" class="form-control" placeholder="Doc No." value="${item.DocumentNo || ''}"></td>
                    <td>
                      <input type="hidden" name="item_uid[]" id="item_uid${row_id}" value="${item.id || 0}">
                      <select id="vendor_id${row_id}" name="vendor_id[]" class="form-control dynamic_item selectpicker" data-live-search="true" data-container="body" onchange="getItemDetails(this.value, '${row_id}');">${vendorOptions}</select>
                    </td>
                    <td><input type="text" name="payment_term[]" id="payment_term${row_id}" class="form-control" placeholder="Payment Term" readonly value="${item.PaymentTerm || ''}"></td>
                    <td><input type="tel" name="bag[]" id="bag${row_id}" class="form-control bag" min="0" step="1" placeholder="Bag" value="${item.BagQty || ''}" oninput="calculateAmount(${row_id})"></td>
                    <td><input type="tel" name="weight_per_bag[]" id="weight_per_bag${row_id}" class="form-control weight-per-bag" min="0" step="0.01" placeholder="Wt./Bag (kg)" value="${item.WeightPerBag || ''}" oninput="calculateAmount(${row_id})"></td>
                    <td><input type="tel" name="loose_kg[]" id="loose_kg${row_id}" class="form-control loose-kg" min="0" step="0.01" placeholder="Loose (kg)" value="${item.LooseKG || ''}" oninput="calculateAmount(${row_id})"></td>
                    <td><input type="tel" name="qty_quintal[]" id="qty_quintal${row_id}" class="form-control qty-quintal" placeholder="Qty (Qtl)" readonly tabindex="-1" value="${parseFloat(item.QtyQuintal || 0).toFixed(4)}"></td>
                    <td><input type="tel" name="rate_quintal[]" id="rate_quintal${row_id}" class="form-control rate-quintal" min="0" step="0.01" placeholder="Rate/Qtl" value="${item.RatePerQuintal || ''}" oninput="calculateAmount(${row_id})"></td>
                    <td><input type="tel" name="value[]" id="value${row_id}" class="form-control row-value" placeholder="Value" readonly tabindex="-1" value="${parseFloat(item.Value || 0).toFixed(2)}"></td>
                    <td><input type="tel" name="brokerage[]" id="brokerage${row_id}" class="form-control row-brokerage" min="0" step="0.01" placeholder="Brokerage" value="${item.Brokerage || ''}" oninput="calculateAmount(${row_id})"></td>
                    <td><input type="tel" name="market_levy[]" id="market_levy${row_id}" class="form-control row-market-levy" min="0" step="0.01" placeholder="Market Levy" value="${item.MarketLevy || ''}" oninput="calculateAmount(${row_id})"></td>
                    <td><input type="tel" name="round_off[]" id="round_off${row_id}" class="form-control row-round-off" placeholder="Round Off" readonly tabindex="-1" value="${parseFloat(item.RoundOff || 0).toFixed(2)}"></td>
                    <td><input type="tel" name="gross[]" id="gross${row_id}" class="form-control row-gross" placeholder="Gross" readonly tabindex="-1" value="${parseFloat(item.Gross || 0).toFixed(2)}"></td>
                    <td><input type="tel" name="tds_per_input[]" id="tds_per_input${row_id}" class="form-control row-tds-per" min="0" step="0.01" placeholder="TDS %" value="${tdsPer}" oninput="calculateAmount(${row_id})"></td>
                    <td><input type="tel" name="tds_amt[]" id="tds_amt${row_id}" class="form-control row-tds" placeholder="TDS Amt" readonly tabindex="-1" value="${parseFloat(item.HistoryTDSAmt || 0).toFixed(2)}"></td>
                    <td><input type="tel" name="net_amt_row[]" id="net_amt_row${row_id}" class="form-control row-net-amt" placeholder="Net Amt" readonly tabindex="-1" value="${parseFloat(item.NetAmt || 0).toFixed(2)}"></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove(); calculateTotals();" style="padding:2px 6px;"><i class="fa fa-xmark"></i></button></td>
                  </tr>`);

                $('#vendor_id' + row_id).selectpicker({ container: 'body', liveSearch: true });
                $('#vendor_id' + row_id).val(item.VendorID || '');
                $('#vendor_id' + row_id).selectpicker('refresh');
              })(d1[i]);
            }
          }

          setTimeout(function(){ calculateTotals(); }, 400);
          refreshSelectpickers();
          alert_float('success', 'Record loaded: ' + orderId);

        } else {
          alert_float('warning', response.message || 'Record not found.');
        }
      },
      error: function() {
        $('#autoLoadOverlay').hide();
        alert_float('warning', 'Error loading record. Please try again.');
      }
    });
  }

  // AUTO LOAD LIST MODAL
  $('#ListModal').on('shown.bs.modal', function() { $('#filter_list_form').submit(); });

  // =============================================
  // GODOWN BY LOCATION
  // =============================================
  function getGodownByLocation(location_id) {
    $('#godown_id').html('<option value="" selected>None selected</option>');
    if (!location_id) { refreshSelectpickers(); return; }
    $.ajax({
      url: '<?= admin_url("purchase/Mandi/getGodownByLocation"); ?>',
      type: 'POST', dataType: 'json',
      data: {
        location_id: location_id,
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      success: function(response) {
        if (response.success == true && response.data.length > 0) {
          var html = '<option value="">None selected</option>';
          $.each(response.data, function(index, val) { html += '<option value="' + val.id + '">' + val.GodownName + '</option>'; });
          $('#godown_id').selectpicker('destroy');
          $('#godown_id').html(html);
          $('#godown_id').selectpicker();
        } else {
          $('#godown_id').selectpicker('destroy');
          $('#godown_id').html('<option value="">None selected</option>');
          $('#godown_id').selectpicker();
          if (response.success == false) { alert_float('warning', response.message); }
        }
      },
      error: function() { $('#godown_id').html('<option value="">None selected</option>'); refreshSelectpickers(); }
    });
  }

  // TABLE SORT
  $(document).on("click", ".sortable", function () {
    var table = $("#table_ListModal tbody"), rows = table.find("tr").toArray();
    var index = $(this).index(), ascending = !$(this).hasClass("asc");
    $(".sortable").removeClass("asc desc"); $(".sortable span").remove();
    $(this).addClass(ascending ? "asc" : "desc");
    $(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
    rows.sort(function (a, b) {
      var valA = $(a).find("td").eq(index).text().trim();
      var valB = $(b).find("td").eq(index).text().trim();
      if ($.isNumeric(valA) && $.isNumeric(valB)) { return ascending ? valA - valB : valB - valA; }
      return ascending ? valA.localeCompare(valB) : valB.localeCompare(valA);
    });
    table.append(rows);
  });

  // TABLE SEARCH
  function myFunction2() {
    var filter = document.getElementById("myInput1").value.toUpperCase();
    var tr = document.getElementById("table_ListModal_body").getElementsByTagName("tr");
    for (var i = 0; i < tr.length; i++) {
      var tds = tr[i].getElementsByTagName("td"), rowMatch = false;
      for (var j = 0; j < tds.length; j++) {
        if ((tds[j].textContent || tds[j].innerText).toUpperCase().indexOf(filter) > -1) { rowMatch = true; break; }
      }
      tr[i].style.display = rowMatch ? "" : "none";
    }
  }

  // VEHICLE NO VALIDATION
  document.getElementById('vehicle_no').addEventListener('input', function () { this.value = this.value.toUpperCase(); });
  document.getElementById('vehicle_no').addEventListener('blur', function () {
    var vehicleNo = this.value.trim();
    var pattern   = /^[A-Z]{2}[0-9]{2}[A-Z]{1,2}[0-9]{4}$/;
    var oldError  = document.getElementById('vehicle_no_error');
    if (oldError) oldError.remove();
    if (vehicleNo !== '' && !pattern.test(vehicleNo)) {
      var error = document.createElement('span');
      error.id = 'vehicle_no_error'; error.style.color = 'red'; error.style.fontSize = '12px';
      error.innerText = 'Invalid format! Use: MH12DF1234';
      this.parentNode.appendChild(error); this.style.borderColor = 'red';
    } else { this.style.borderColor = ''; }
  });
</script>