<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.table-list { overflow: auto; max-height: 55vh; width:100%; position:relative; top: 0px; }
.table-list thead th { position: sticky; top: 0; z-index: 1; }
.table-list tbody th { position: sticky; left: 0; }
table { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important; font-size:11px; line-height:1.42857143 !important; vertical-align: middle !important;}
th { background: #50607b; color: #fff !important; }
.tableFixHead2 { overflow: auto; max-height: 50vh; }
.sortable, .get_Details { cursor: pointer; }
.total-label-row { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #eee; }
.total-label-row .total-display { flex: 1; padding: 0; text-align: right; font-weight: 600; }
.fixed-td { max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.get_Details.processing { pointer-events: none; opacity: 0.9; }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li><li class="breadcrumb-item active" aria-current="page"><b>Inwards</b></li>
							</ol>
						</nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
							<input type="hidden" name="form_mode" id="form_mode" value="add">
							<input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_id">
                    <label for="vendor_id" class="control-label"><small class="req text-danger">* </small> Vendor Name</label>
                    <select name="vendor_id" id="vendor_id" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor Name" required onchange="getVendorDetails(this.value);">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($vendor_list)) :
                        foreach ($vendor_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
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
                  <div class="form-group" app-field-wrapper="purchase_order">
                    <label for="purchase_order" class="control-label"><small class="req text-danger">* </small> Purchase Order</label>
                    <select name="purchase_order" id="purchase_order" class="form-control selectpicker" data-live-search="true" app-field-label="Category" required onchange="getOrderDetails(this.value);">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="inwards_no">
                    <label for="inwards_no" class="control-label"><small class="req text-danger">* </small> Inwards No</label>
										<input type="text" name="inwards_no" id="inwards_no" class="form-control" app-field-label="Inwards No" required readonly placeholder="Auto Generated">
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="inwards_date">
                    <?= render_date_input('inwards_date','Inwards Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="gatein_no">
                    <label for="gatein_no" class="control-label">Gate In</label>
                    <select name="gatein_no" id="gatein_no" class="form-control selectpicker" data-live-search="true" app-field-label="Gate In">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($gatein_list)) :
                        foreach ($gatein_list as $value) :
                          echo '<option value="' . $value['GateINID'] . '">' . $value['VehicleNo'] . ' ('.$value['GateINID'].')</option>';
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
												<th>Item Name</th>
												<th>HSN Code</th>
												<th>UOM</th>
												<th>Unit Wt (Kg)</th>
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
                        <label>Total Wt (Kg):</label>
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
        <h4 class="modal-title">Purchase Inwards List</h4>
      </div>
      <div class="modal-body" style="padding:5px 5px !important">
        <form action="" method="post" id="filter_list_form">
          <div class="row">
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="from_date">
                <label for="from_date" class="control-label">From Date</label>
                <div class="input-group date">
                  <input type="text" id="from_date" name="from_date" class="form-control datepicker filterInput" value="<?= date("01/m/Y")?>" app-field-label="From Date" onchange="resetForm();">
                  <div class="input-group-addon">
                    <i class="fa-regular fa-calendar calendar-icon"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="to_date">
                <label for="to_date" class="control-label">To Date</label>
                <div class="input-group date">
                  <input type="text" id="to_date" name="to_date" class="form-control datepicker filterInput" value="<?= date("d/m/Y")?>" app-field-label="To Date" onchange="resetForm();">
                  <div class="input-group-addon">
                    <i class="fa-regular fa-calendar calendar-icon"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-5 mbot5" style="padding-top: 20px;">
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
                <th class="sortable">Inwards No</th>
                <th class="sortable">Order No</th>
                <th class="sortable">Inwards Date</th>
                <th class="sortable">Vendor</th>
                <th class="sortable">Wt (Kg)</th>
                <th class="sortable">Amt</th>
              </tr>
            </thead>
            <tbody>
              <?php
							if(!empty($inwards_list)):
								foreach ($inwards_list as $key => $value):
								?>
								<tr class="get_Details" data-id="<?= $value["id"]; ?>" onclick="getDetails(<?= $value['id']; ?>)">
									<td><?= $value["InwardsID"];?></td>
                  <td><?= $value["OrderID"];?></td>
									<td><?= date('d/m/Y', strtotime($value["TransDate"]));?></td>
									<td><?= $value["company"];?> - <?= $value["AccountID"];?></td>
									<td><?= $value["TotalWeight"];?></td>
									<td><?= $value["NetAmt"];?></td>
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

<?php init_tail(); ?>
<script>
  $(document).ready(function() {
    setTimeout(() => {
      $('#filter_list_form').submit();
    }, 800);
  })

	function ResetForm(){
		$('#main_save_form')[0].reset();
		$('#form_mode').val('add');
		$('#update_id').val('');
		$('.updateBtn').hide();
		$('.saveBtn').show();
    $('#items_body').html('');
    $('.total-display').text('0.00');
    $('#row_id').val(0);
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
    $('#purchase_order').html('<option value="" selected>None selected</option>');
    $('#gatein_no').html(`
      <option value="" selected>None selected</option>
      <?php
      if (!empty($gatein_list)) :
        foreach ($gatein_list as $value) :
          echo '<option value="' . $value['GateINID'] . '">' . $value['VehicleNo'] . ' ('.$value['GateINID'].')</option>';
        endforeach;
      endif;
      ?>
    `);
		$('.selectpicker').selectpicker('refresh');
	}

  $(document).on('input', 'input[type="tel"]', function () {
    this.value = this.value
        .replace(/[^0-9.]/g, '')        // allow digits + dot
        .replace(/(\..*?)\..*/g, '$1'); // allow only one dot
  });
	
	function validate_fields(fields){ 
		let data = {};
		for(let i = 0; i < fields.length; i++){
			let value = $('#' + fields[i]).val();

			if(value === '' || value === null){
				let label = fields[i].replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
				alert_float('warning', 'Please enter ' + label);
				$('#'+fields[i]).focus();
				return false;
			} else {
				data[fields[i]] = value.trim();
			}
		}
		return data;
	}

	function addRow(row = null){
    $('#item_id').focus();
		var row_id = $('#row_id').val();
		var next_id = parseInt(row_id) + 1;
		if(row == null){
			let fields = ['item_id', 'min_qty', 'max_qty', 'disc_amt', 'unit_rate'];
			let data = validate_fields(fields);
			if (data === false) {
				return false;
			}

			var row_btn = `<button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove();" style="float:right; padding: 2px; width: 30px;"><i class="fa fa-xmark"></i></button>`;
		}else{
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
		if(row == null){
			$('#item_id'+next_id).val($('#item_id').val());
			$('#hsn_code'+next_id).val($('#hsn_code').val());
			$('#uom'+next_id).val($('#uom').val());
			$('#unit_weight'+next_id).val($('#unit_weight').val());
      $('#min_qty'+next_id).val($('#min_qty').val());
      $('#max_qty'+next_id).val($('#max_qty').val());
      $('#disc_amt'+next_id).val($('#disc_amt').val());
      $('#unit_rate'+next_id).val($('#unit_rate').val());
      $('#gst'+next_id).val($('#gst').val());
      $('#amount'+next_id).val($('#amount').val());
      $('.fixed_row').val('');
      calculateAmount(next_id);
		}
		$('#row_id').val(next_id);
    $('.selectpicker').selectpicker('refresh');
	}

  function calculateAmount(row) {
    var minQty = parseFloat($('#min_qty'+row).val()) || 0;
    var unitRate = parseFloat($('#unit_rate'+row).val()) || 0;
    var discAmt = parseFloat($('#disc_amt'+row).val()) || 0;
    var gstPercent = parseFloat($('#gst'+row).val()) || 0;

    var taxableAmt = (unitRate - discAmt) * minQty;
    var gstAmt = taxableAmt * (gstPercent / 100);
    var netAmt = taxableAmt + gstAmt;
    if(isNaN(netAmt) || netAmt < 0){
      netAmt = 0;
    }

    $('#amount'+row).val(netAmt.toFixed(2));
    calculateTotals();
    if((row == '' || row == null) && minQty > 0 && unitRate > 0 && discAmt >= 0 && gstPercent >= 0){
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

    $('#items_body tr').each(function () {
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
    var vendorState = $('#vendor_state').val().trim().toUpperCase();
    var cgstAmt = 0, sgstAmt = 0, igstAmt = 0;
    var cgstPercent = 0, sgstPercent = 0, igstPercent = 0;
    var totalGstPercent = 0;
    if (totalTaxableAmt > 0) {
      $('#items_body tr').each(function () {
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
    if (vendorState === '<?= $company_detail->state; ?>') {
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

	function get_required_fields(form_id){
		let fields = [];
		$('#' + form_id + ' [required]').each(function(){
				fields.push($(this).attr('id'));
		});
		return fields;
	}

  function getVendorDetails(vendor_id, callback = null) {
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/getVendorDetails'); ?>',
      type: 'POST',
      data: { vendor_id: vendor_id },
      dataType: 'json',
      success: function (response) {
        if (response.success == true) {
          var data = response.data;
          $('#vendor_gst_no').val(data.GSTIN || '');
          $('#vendor_country').val(data.country_name || '');
          $('#vendor_state').val(data.billing_state || '');
          $('#vendor_address').val(data.billing_address || '');
          
          html = '<option value="" selected disabled>None selected</option>';
          $.each(response.order_list, function (index, loc) {
            html += `<option value="${loc.PurchID}">${loc.PurchID}</option>`;
						});
          $('#purchase_order').html(html);

          $('.selectpicker').selectpicker('refresh');
          // calculateTotals();
          if (callback) {
            callback();
          }
        }
      }
    });
  }

  function getItemDetails(itemId, id=''){
    var isDuplicate = false;

    $('.dynamic_item').not('#item_id'+id).each(function () {
      if ($(this).val() == itemId && itemId != '') {
        isDuplicate = true;
        return false;
      }
    });
    if (isDuplicate) {
      alert_float('warning', 'Please select other item, this item already selected.');
      $('#item_id'+id).val('').focus();
      $('.selectpicker').selectpicker('refresh');
      $('.fixed_row').val('');
      $('.dynamic_row'+id).val('');
      return;
    }

    $.ajax({
      url: '<?= admin_url('purchase/GetItemDetails'); ?>',
      type: 'POST',
      data: { item_id: itemId },
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success' && response.data) {
          var data = response.data;
          $('#hsn_code'+id).val(data.hsn_code || '');
          $('#uom'+id).val(data.unit || '');
          $('#unit_weight'+id).val(Number(data.UnitWeight) || 0);
          $('#gst'+id).val(Number(data.tax) || 0);
          $('#min_qty'+id).focus();
        }else{
          $('.fixed_row').val('');
          $('.selectpicker').selectpicker('refresh');
        }
        calculateTotals();
      },
      error: function (xhr, status, err) {
        console.log('Error fetching item details:', err);
      }
    });
  }

  function getOrderDetails(order_id, callback = null) {
    let form_mode = $('#form_mode').val();
    $('#items_body').html('');
    $('#row_id').val(0);
    $.ajax({
			url:"<?= admin_url(); ?>purchase/Inwards/GetOrderDetails",
			method:"POST",
			dataType:"JSON",
			data: {
				order_id: order_id,
			},
			success: function(response){
				if(response.success == true){
          var html = '<option value="" selected disabled>Select Item</option>';
          $.each(response.item_list, function (index, val) {
            html += '<option value="' + val.ItemId + '">' + val.ItemName + '</option>';
						});
          $('#item_id').html(html);
          $('.selectpicker').selectpicker('refresh');

          if(form_mode == 'add'){
            $('#inwards_no').val(response.inwards_no);
            
            let history = response.data.history;
            for(var i = 0; i < history.length; i++){
              addRow(2);
              $('#item_id'+(i+1)).val(history[i].ItemID);
              getItemDetails(history[i].ItemID, (i+1));
              $('#min_qty'+(i+1)).val(Number(history[i].OrderQty));
              $('#max_qty'+(i+1)).val(Number(history[i].OrderQty));
              $('#unit_rate'+(i+1)).val(Number(history[i].BasicRate));
              $('#disc_amt'+(i+1)).val(Number(history[i].DiscAmt));
              $('#amount'+(i+1)).val(Number(history[i].NetOrderAmt));
              $('.selectpicker').selectpicker('refresh');
              calculateAmount(i+1);
            }
          }
				}else{
					alert_float('warning', response.message);
				}

        if(callback){
          callback();
        }
			}
		});
  }
	
	$('#main_save_form').on('submit', function(e) {
		e.preventDefault();

		let form_mode = $('#form_mode').val();
		
		let required_fields = get_required_fields('main_save_form');
		let validated = validate_fields(required_fields);

		if(validated === false){
			return;
		}
		
		var form_data = new FormData(this);
		form_data.append(
			'<?= $this->security->get_csrf_token_name(); ?>',
			$('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		);
		if(form_mode == 'edit'){
			form_data.append('update_id', $('#update_id').val());
		}

		$.ajax({
			url:"<?= admin_url(); ?>purchase/Inwards/SaveInwards",
			method:"POST",
			dataType:"JSON",
			data:form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function () {
				$('button[type=submit]').attr('disabled', true);
			},
			complete: function () {
				$('button[type=submit]').attr('disabled', false);
			},
			success: function(response){
				if(response.success == true){
					alert_float('success', response.message);
					let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getDetails(${response.data.id})">
						<td>${response.data.InwardsID}</td>
            <td>${response.data.OrderID}</td>
            <td>${moment(response.data.TransDate).format('DD/MM/YYYY')}</td>
            <td>${response.data.company}</td>
            <td>${response.data.TotalWeight}</td>
            <td>${response.data.NetAmt}</td>
					</tr>`;
					if(form_mode == 'edit'){
						$('.get_Details[data-id="'+response.data.id+'"]').replaceWith(html);
					}else{
						$('#table_ListModal tbody').prepend(html);
					}
					ResetForm();
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	});

	function getDetails(id, row){
    let $row = $(row);
    if ($row.hasClass('processing')) return;
    $row.addClass('processing');

		ResetForm();
		$.ajax({
			url:"<?= admin_url(); ?>purchase/Inwards/GetInwardsDetails",
			method:"POST",
			dataType:"JSON",
			data: {
				id: id,
			},
      complete: function(){
        $row.removeClass('processing');
      },
			success: function(response){
				if(response.success == true){
					$('#form_mode').val('edit');
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('#ListModal').modal('hide');

					let d = response.data;
          $('#print_pdf').attr('href', '<?= admin_url('purchase/Inwards/PrintPDF/'); ?>'+d.InwardsID);
					$('#update_id').val(id);
          // if(!$('#vendor_id option[value="'+d.AccountID+'"]').length){
            $('#vendor_id').html(`<option value="${d.AccountID}" selected>${d.company} (${d.AccountID})</option>`);
            if(d.gatein_no != null){
              $('#gatein_no').html(`<option value="${d.gatein_no}" selected>${d.VehicleNo} (${d.gatein_no})</option>`);
            }
            
          // }
          // if(!$('#purchase_order option[value="'+d.OrderID+'"]').length){
            $('#purchase_order').html(`<option value="${d.OrderID}" selected>${d.OrderID}</option>`);
          // }
          getOrderDetails(d.OrderID, function(){
            let history = response.data.history;
            for(var i = 0; i < history.length; i++){
              addRow(2);
              $('#item_uid'+(i+1)).val(history[i].id);
              $('#item_id'+(i+1)).val(history[i].ItemID);
              getItemDetails(history[i].ItemID, (i+1));
              $('#min_qty'+(i+1)).val(Number(history[i].OrderQty));
              $('#max_qty'+(i+1)).val(Number(history[i].OrderQty));
              $('#unit_rate'+(i+1)).val(Number(history[i].BasicRate));
              $('#disc_amt'+(i+1)).val(Number(history[i].DiscAmt));
              $('#amount'+(i+1)).val(Number(history[i].NetOrderAmt));
              $('.selectpicker').selectpicker('refresh');
              calculateAmount(i+1);
            }
          });
          $('#inwards_no').val(d.InwardsID);
          $('#inwards_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
          
          $('.selectpicker').selectpicker('refresh');
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	}

  $('#filter_list_form').submit(function(e){
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
        url: "<?= admin_url('purchase/Inwards/ListFilter') ?>",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function(res){
          let json = JSON.parse(res);
          if(!json.success){
            $('#searchBtn').prop('disabled', false);
            if(offset === 0){
              $('#table_ListModal tbody').html(
                '<tr><td colspan="10" class="text-center">No Data Found</td></tr>'
              );
            }
            return;
          }
          if(offset === 0){
            totalRecords = parseInt(json.total) || 0;
          }
          if(json.rows && json.rows.length > 0){
            appendRows(json.rows);
            loadedRecords += json.rows.length;
            offset += limit;
          }
          updateProgress(loadedRecords, totalRecords);
          if(loadedRecords >= totalRecords){
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

  function appendRows(rows){
    let html = '';
    let status_list = {1 : 'Pending', 2 :'Cancel', 3 :'Expired', 4 :'Approved', 5 :'Inprogress', 6 :'Complete', 7 :'Partially Complete'};
    rows.forEach(function(row){
      html += `<tr class="get_Details" data-id="${row.id}" onclick="getDetails(${row.id}, this)">
        <td class="text-center">${row.InwardsID}</td>
        <td class="text-center">${row.OrderID}</td>
        <td>${moment(row.TransDate).format('DD/MM/YYYY')}</td>
        <td>${row.vendor_name} (${row.AccountID})</td>
        <td class="text-center">${Number(row.TotalWeight / 100) || '-'}</td>
        <td class="text-center">${Number(row.NetAmt) || '-'}</td>
      </tr>`;
    });
    $('#table_ListModal tbody').append(html);
  }

  function updateProgress(loaded, total){
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%')
  }
</script>
<script>
  $(document).on("click", ".sortable", function () {
		var table = $("#table_ListModal tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		$(".sortable").removeClass("asc desc");
		$(".sortable span").remove();
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		rows.sort(function (a, b) {
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