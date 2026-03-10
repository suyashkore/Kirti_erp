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
@media screen and (max-width: 767px) {
  #header ul { display: none !important; }
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
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li><li class="breadcrumb-item active" aria-current="page"><b>Inwards ASN</b></li>
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
                  <div class="form-group" app-field-wrapper="asn_no">
                    <label for="asn_no" class="control-label"><small class="req text-danger">* </small> ASN No</label>
										<input type="text" name="asn_no" id="asn_no" class="form-control" app-field-label="ASN No" required readonly placeholder="Auto Generated">
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="asn_date">
                    <?= render_date_input('asn_date','ASN Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vehicle_no">
                    <label for="vehicle_no" class="control-label"><small class="req text-danger">* </small> Vehicle No</label>
										<input type="text" name="vehicle_no" id="vehicle_no" class="form-control" app-field-label="Vehicle No" required title="Examples: 25 BH 1234 AH or MH 01 AB 1234" style="text-transform: uppercase;" onchange="vehicleNoValidation('vehicle_no');">
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="phone_no">
                    <label for="phone_no" class="control-label"><small class="req text-danger">* </small> Phone No</label>
										<input type="text" name="phone_no" id="phone_no" class="form-control" app-field-label="Phone No" required placeholder="">
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
												<th>UOM</th>
												<th>Unit Wt (Kg)</th>
												<th>Order Qty</th>
												<th>Disc Amt</th>
												<th>Unit Rate</th>
												<th>GST %</th>
												<th>ASN Qty</th>
												<th>ASN Wt</th>
												<th>Amount</th>
                        <th></th>
											</tr>
                      <tr style="display: none;">
                        <td style="width: 250px;">
                          <select id="item_id" class="form-control fixed_row dynamic_row dynamic_item selectpicker" data-live-search="true" app-field-label="Item Name" onchange="getItemDetails(this.value, '');">
                            <option value="" selected disabled>Select Item</option>
                          </select>
                        </td>
                        <td><input type="text" id="uom" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="unit_weight" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="min_qty" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="disc_amt" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="unit_rate" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="gst" class="form-control fixed_row" readonly tabindex="-1"></td>
                        <td><input type="tel" id="asn_qty" class="form-control fixed_row"></td>
                        <td><input type="tel" id="asn_weight" class="form-control fixed_row"></td>
                        <td><input type="tel" id="amount" class="form-control fixed_row" readonly tabindex="-1"></td>
                      </tr>
										</thead>
										<tbody id="items_body">

                    </tbody>
									</table>
                </div>
                
                <div class="col-sm-9 mbot5"></div>
                <div class="col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="final_asn_amount">
                    <label for="final_asn_amount" class="control-label"><small class="req text-danger">* </small> ASN / Invoice Amt</label>
										<input type="tel" name="final_asn_amount" id="final_asn_amount" class="form-control" app-field-label="ASN No" required placeholder="">
                  </div>
                </div>
                
                <div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
                  <a href="#" class="btn btn-primary updateBtn" id="print_pdf" style="display: none;" target="_blank"><i class="fa fa-print"></i> Print PDF</a>
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
									<button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
									<button type="button" class="btn btn-danger" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
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
    $('#asn_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate,
        timepicker: false
    });
});
  
  

  $(document).ready(function() {
    // setTimeout(() => {
    //   $('#filter_list_form').submit();
    // }, 800);
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

  function vehicleNoValidation(vehicleNoId){
		let vehicleNo = $('#' + vehicleNoId).val().toUpperCase().trim();
    vehicleNo = vehicleNo.replace(/\s+/g, ' ');

		var bhRegex = /^[0-9]{2}\s?BH\s?[0-9]{4}\s?[A-Z]{1,2}$/;
		var rtoRegex = /^[A-Z]{2}\s?[0-9]{2}\s?[A-Z]{1,2}\s?[0-9]{4}$/;

		if (!bhRegex.test(vehicleNo) && !rtoRegex.test(vehicleNo)) {
			alert_float('warning', 'Please enter a valid Vehicle No. Examples: 25 BH 1234 AH or MH 01 AB 1234');
			$('#' + vehicleNoId).focus();
			return false;
		}
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
          <input type="hidden" name="item_id[]" id="item_id${next_id}" value="0">
          <input type="text" name="item_name[]" id="item_name${next_id}" readonly class="form-control dynamic_row${next_id}">
        </td>
        <td><input type="text" name="uom[]" id="uom${next_id}" class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="unit_weight[]" id="unit_weight${next_id}" class="form-control unit-weight dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="min_qty[]" id="min_qty${next_id}" class="form-control min-qty dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="disc_amt[]" id="disc_amt${next_id}" class="form-control disc-amt dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="unit_rate[]" id="unit_rate${next_id}" class="form-control unit-rate dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="gst[]" id="gst${next_id}" class="form-control gst-percent dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><input type="tel" name="asn_qty[]" id="asn_qty${next_id}" class="form-control asn-qty dynamic_row${next_id}" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="asn_weight[]" id="asn_weight${next_id}" class="form-control asn-weight dynamic_row${next_id}" oninput="calculateAmount(${next_id})"></td>
        <td><input type="tel" name="amount[]" id="amount${next_id}" class="form-control row-amount dynamic_row${next_id}" readonly tabindex="-1"></td>
        <td><button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove();" style="float:right; padding: 2px; width: 30px;"><i class="fa fa-xmark"></i></button></td>
      </tr>
		`);
    
		$('#row_id').val(next_id);
	}

  function calculateAmount(row) {
    var minQty = parseFloat($('#min_qty'+row).val()) || 0;
    var unitRate = parseFloat($('#unit_rate'+row).val()) || 0;
    var discAmt = parseFloat($('#disc_amt'+row).val()) || 0;
    var gstPercent = parseFloat($('#gst'+row).val()) || 0;
    var asnQty = parseFloat($('#asn_qty'+row).val()) || 0;
    var unitWt = parseFloat($('#unit_weight'+row).val()) || 0;

    var taxableAmt = (unitRate - discAmt) * asnQty;
    var gstAmt = taxableAmt * (gstPercent / 100);
    var netAmt = taxableAmt + gstAmt;
    var wt = unitWt * asnQty;
    if(isNaN(wt) || wt < 0){
      wt = 0;
    }
    $('#asn_weight'+row).val(wt.toFixed(2));
    
    if(isNaN(netAmt) || netAmt < 0){
      netAmt = 0;
    }
    $('#amount'+row).val(netAmt.toFixed(2));
    
    calculateTotals();
  }

  function calculateTotals() {
    var totalAmt = 0;

    $('#items_body tr').each(function () {
      var row = $(this);
      var amt = parseFloat(row.find('.row-amount').val()) || 0;

      totalAmt += amt;
    });
    $('#final_asn_amount').val(totalAmt.toFixed(2));
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
      url: '<?= admin_url('purchase/Inwards/getVendorDetailsASN'); ?>',
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

  function getItemDetails(itemId, id='', callback = null) {
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
          // $('#asn_qty'+id).focus();
        }else{
          $('.fixed_row').val('');
        }
        calculateAmount(id);
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
          let item_array = [];
          $.each(response.item_list, function (index, val) {
            item_array[val.ItemId] = val.ItemName;
					});
          console.log(item_array);

          if(form_mode == 'add'){
            $('#inwards_no').val(response.inwards_no);
            
            let history = response.data.history;
            for(var i = 0; i < history.length; i++){
              addRow(2);
              $('#item_id'+(i+1)).val(history[i].ItemID);
              $('#item_name'+(i+1)).val(item_array[history[i].ItemID]);
              getItemDetails(history[i].ItemID, (i+1));
              $('#min_qty'+(i+1)).val(Number(history[i].OrderQty));
              $('#asn_qty'+(i+1)).val(Number(history[i].OrderQty));
              $('#unit_rate'+(i+1)).val(Number(history[i].BasicRate));
              $('#disc_amt'+(i+1)).val(Number(history[i].DiscAmt));
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
    
		let vehicleNo = vehicleNoValidation('vehicle_no');

		if(vehicleNo === false){
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
			url:"<?= admin_url(); ?>purchase/Inwards/SaveASN",
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