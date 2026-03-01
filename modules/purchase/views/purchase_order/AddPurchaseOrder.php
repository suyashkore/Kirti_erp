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

<!-- =============================================
	 LIST MODAL
============================================= -->
<div class="modal fade" id="ListModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Purchase Order List</h5>
				<!-- <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button> -->
			</div>
			<div class="modal-body">
				<div class="row mb-2">
					<div class="col-md-2">
						<label for="from_date_modal" class="control-label"><small>From Date</small></label>
						<input type="text" id="from_date_modal" class="form-control" placeholder="From Date"
							autocomplete="off">
					</div>
					<div class="col-md-2">
						<label for="to_date_modal" class="control-label"><small>To Date</small></label>
						<input type="text" id="to_date_modal" class="form-control" placeholder="To Date"
							autocomplete="off">
					</div>
					<div class="col-md-2">
						<label for="filter_category" class="control-label"><small>Category</small></label>
						<select id="filter_category" class="form-control selectpicker" data-live-search="true"
							title="All Categories">
							<option value="">All Categories</option>
							<?php
							if (!empty($item_category_list)):
								foreach ($item_category_list as $cat):
									echo '<option value="' . $cat['id'] . '">' . $cat['CategoryName'] . '</option>';
								endforeach;
							endif;
							?>
						</select>
					</div>
					<div class="col-md-2">
						<label class="control-label"><small>&nbsp;</small></label>
						<button class="btn btn-info btn-block" id="show_filter_btn" type="button" style="width: 88PX; background-color: revert-layer;">
							<i class="fa fa-search"></i> Show
						</button>
					</div>
					<div class="col-md-4">
						<label for="myInput1" class="control-label"><small>Search</small></label>
						<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for anything.."
							class="form-control">
					</div>
				</div>

				<!-- MAIN ORDER TABLE -->
				<div class="table-list">
					<table class="table table-bordered" id="table_ListModal">
						<thead>
							<tr>
								<th class="sortable">PurchID</th>
								<th class="sortable">Category</th>
								<th class="sortable">Purchase Location</th>
								<th class="sortable">Vendor Name</th>
								<th class="sortable">Order Date</th>
								<th class="sortable">Delivery Location</th>
								<th class="sortable">Delivery From</th>
								<th class="sortable">Delivery To</th>
								<th class="sortable">GSTIN</th>
								<th class="sortable">Total Weight</th>
								<th class="sortable">Total Qty</th>
								<th class="sortable">Net Amt</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb"
								style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i
												class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Purchase Order</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<br>
						<form action="<?= admin_url('purchase/savepurchase'); ?>" method="post" id="main_save_form">
							<input type="hidden" name="form_mode" id="form_mode" value="add">
							<input type="hidden" name="update_id" id="update_id" value="">
							<div class="row">
<div class="col-md-2 mbot5">
									<div class="form-group" app-field-wrapper="vendor_id">
										<label for="vendor_id" class="control-label"><small class="req text-danger">*
											</small> Vendor Name</label>
										<select name="vendor_id" id="vendor_id" class="form-control selectpicker"
											data-live-search="true" app-field-label="Vendor Name" required
											onchange="getVendorDetailsLocation();">
											<option value="" selected>None selected</option>
											<?php
											if (!empty($vendor_list)):
												foreach ($vendor_list as $value):
													echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' (' . $value['AccountID'] . ')</option>';
												endforeach;
											endif;
											?>
										</select>
										<input type="hidden" name="vendor_gst_no" id="vendor_gst_no"
											class="form-control" readonly>
										<input type="hidden" name="vendor_country" id="vendor_country"
											class="form-control" readonly>
										<input type="hidden" name="vendor_state" id="vendor_state" class="form-control"
											readonly>
										<input type="hidden" name="vendor_address" id="vendor_address"
											class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 mbot5">
									<div class="form-group">
										<label>Quotation No</label>
										<select name="vendor_quote_no" id="vendor_quote_no"
											class="form-control selectpicker" data-live-search="true"
											title="Select Quotation No" onchange="onVendorQuoteNoChange(this.value);">
											<option value="">Select Quotation No</option>
										</select>
									</div>
								</div>

								<div class="col-md-2 mbot5">
									<div class="form-group" app-field-wrapper="item_type">
										<label for="item_type" class="control-label"><small class="req text-danger">*
											</small> Item / Service</label>
										<select name="item_type" id="item_type" class="form-control selectpicker"
											data-live-search="true" app-field-label="Item / Service" required
											onchange="getCustomDropdownList('item_type', this.value, 'item_category');">
											<option value="" selected>None selected</option>
											<?php
											if (!empty($item_type)):
												foreach ($item_type as $value):
													echo '<option value="' . $value['id'] . '">' . $value['ItemTypeName'] . '</option>';
												endforeach;
											endif;
											?>
										</select>
									</div>
								</div>
								<div class="col-md-2 mbot5">
									<div class="form-group" app-field-wrapper="item_category">
										<label for="item_category" class="control-label"><small
												class="req text-danger">* </small> Order Category</label>
										<select name="item_category" id="item_category"
											class="form-control selectpicker" data-live-search="true"
											app-field-label="Category" required onchange="getNextQuotationNo();">
											<option value="" selected>None selected</option>
										</select>
									</div>
								</div>

								<div class="col-md-2 mbot5">
									<div class="form-group" app-field-wrapper="order_no">
										<label for="order_no" class="control-label"><small class="req text-danger">*
											</small>Order No</label>
										<input type="text" name="order_no" id="order_no" class="form-control"
											app-field-label="order_no" required readonly placeholder="Auto Generated">
										<input type="hidden" name="quotation_no" id="quotation_no" class="form-control"
											app-field-label="Quotation No" readonly placeholder="Auto Generated">
									</div>
								</div>
								<div class="col-md-2 mbot5">
									<div class="form-group" app-field-wrapper="quotation_date">
										<?= render_date_input('quotation_date', 'Order Date', date('d/m/Y'), []); ?>
									</div>
								</div>

								<div class="col-md-2 mbot5">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Currency</label>
										<select name="currency" id="currency" class="form-control selectpicker"
											required>
											<option value=""></option>
											<?php foreach ($currencies as $key => $value) { ?>
												<option value="<?= $value['id']; ?>" <?= ($value['isdefault'] == 1) ? 'selected' : ''; ?>><?= $value['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="col-md-2 mbot5">
									<div class="form-group" app-field-wrapper="purchase_location">
										<label for="purchase_location" class="control-label"><small
												class="req text-danger">* </small> Purchase Location</label>
										<select name="purchase_location" id="purchase_location"
											class="form-control selectpicker" data-live-search="true"
											app-field-label="Purchase Location" required>
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

								

								<div class="col-md-2 mbot5">
									<div class="form-group" app-field-wrapper="vendor_location">
										<label for="vendor_location" class="control-label"><small
												class="req text-danger">* </small> Vendor Location</label>
										<select name="vendor_location" id="vendor_location"
											class="form-control selectpicker" data-live-search="true" required>
											<option value=""></option>
											<?php if (isset($vendor_locations)) {
												foreach ($vendor_locations as $loc) { ?>
													<option value="<?= $loc['id']; ?>"><?= $loc['name']; ?></option>
												<?php }
											} ?>
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
									<div class="form-group" app-field-wrapper="vendor_quote_date">
										<?= render_date_input('vendor_quote_date', 'Quotation Date', date('d/m/Y'), ['readonly' => true]); ?>
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
													<select id="item_id"
														class="form-control fixed_row dynamic_row dynamic_item selectpicker"
														data-live-search="true" app-field-label="Item Name"
														onchange="getItemDetails(this.value, '');">
														<option value="" selected disabled>Select Item</option>
													</select>
												</td>
												<td><input type="text" id="hsn_code" class="form-control fixed_row"
														readonly tabindex="-1"></td>
												<td><input type="text" id="uom" class="form-control fixed_row" readonly
														tabindex="-1"></td>
												<td><input type="tel" id="unit_weight" class="form-control fixed_row"
														min="0" step="0.01" readonly tabindex="-1"></td>
												<td><input type="tel" id="min_qty" class="form-control fixed_row"
														min="0" step="0.01" onchange="calculateAmount('');"></td>
												<td><input type="tel" id="max_qty" class="form-control fixed_row"
														min="0" step="0.01" onchange="calculateAmount('');"></td>
												<td><input type="tel" id="disc_amt" class="form-control fixed_row"
														min="0" step="0.01" onchange="calculateAmount('');"></td>
												<td><input type="tel" id="unit_rate" class="form-control fixed_row"
														min="0" step="0.01" onchange="calculateAmount('');"></td>
												<td><input type="tel" id="gst" class="form-control fixed_row" min="0"
														max="100" step="0.01" readonly tabindex="-1"></td>
												<td><input type="tel" id="amount" class="form-control fixed_row"
														readonly tabindex="-1"></td>
												<td>
													<button type="button" class="btn btn-success" onclick="addRow();"><i
															class="fa fa-plus"></i></button>
												</td>
											</tr>
										</thead>
										<tbody id="items_body"></tbody>
									</table>
								</div>

								<div class="col-md-6"></div>
								<div class="col-md-12">
									<div class="row">

										<!-- LEFT SIDE - OTHER INFORMATION -->
										<div class="col-md-6">
											<div class="row">
												<div class="col-md-12">
													<h4 class="bold p_style">Other Information</h4>
													<hr class="hr_style">
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Vendor Doc No</label>
														<input type="text" name="vendor_doc_no" id="vendor_doc_no"
															class="form-control" placeholder="Enter vendor doc number">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group" app-field-wrapper="vendor_doc_date">
														<?= render_date_input('vendor_doc_date', 'Vendor Doc Date', date('d/m/Y'), []); ?>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label><small class="req text-danger">*</small>Payment
															Terms</label>
														<select name="payment_terms" id="payment_terms"
															class="form-control selectpicker" data-live-search="true"
															required>
															<option value=""></option>
															<option value="Credit">Credit</option>
															<option value="Advance">Advance</option>
															<option value="OnDelivery">On Delivery</option>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group" app-field-wrapper="freight_terms">
														<label for="freight_terms" class="control-label"><small
																class="req text-danger">* </small> Freight Terms</label>
														<select name="freight_terms" id="freight_terms"
															class="form-control selectpicker" data-live-search="true"
															app-field-label="Freight Terms" required>
															<option value="" selected>None selected</option>
															<?php
															if (!empty($FreightTerms)):
																foreach ($FreightTerms as $value):
																	echo '<option value="' . $value['Id'] . '">' . $value['FreightTerms'] . '</option>';
																endforeach;
															endif;
															?>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label><small class="req text-danger">*</small>Broker </label>
														<select name="broker" id="broker"
															class="form-control selectpicker" data-live-search="true"
															required>
															<option value="" selected>None selected</option>
															<?php
															if (!empty($Broker)):
																foreach ($Broker as $value):
																	echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . '</option>';
																endforeach;
															endif;
															?>
														</select>
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group">
														<label>Attachment</label>
														<span id="attachment_link_container"
															style="margin-left:10px;"></span>
														<input type="file" name="attachment" id="attachment"
															class="form-control">
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Internal Remarks</label>
														<textarea name="internal_remarks" id="internal_remarks"
															class="form-control" rows="2"
															placeholder="Enter internal remarks..."></textarea>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Document Remark</label>
														<textarea name="document_remark" id="document_remark"
															class="form-control" rows="2"
															placeholder="Enter document remarks..."></textarea>
													</div>
												</div>
											</div>
										</div>

										<!-- RIGHT SIDE - TOTAL SUMMARY -->
										<div class="col-md-6">
											<div class="row">
												<div class="col-md-12">
													<h4 class="bold p_style">Total Summary</h4>
													<hr class="hr_style">
												</div>

												<!-- Hidden inputs -->
												<input type="hidden" name="total_weight" id="total_weight_hidden"
													value="0">
												<input type="hidden" name="total_qty" id="total_qty_hidden" value="0">
												<input type="hidden" name="item_total_amt" id="item_total_amt_hidden"
													value="0">
												<input type="hidden" name="total_disc_amt" id="disc_amt_hidden"
													value="0">
												<input type="hidden" name="taxable_amt" id="taxable_amt_hidden"
													value="0">
												<input type="hidden" name="cgst_amt" id="cgst_amt_hidden" value="0">
												<input type="hidden" name="sgst_amt" id="sgst_amt_hidden" value="0">
												<input type="hidden" name="igst_amt" id="igst_amt_hidden" value="0">
												<input type="hidden" name="round_off_amt" id="round_off_amt_hidden"
													value="0">
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
														<div class="total-display" id="item_total_amt_display">0.00
														</div>
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

												<!-- NET AMOUNT -->
												<div class="col-md-12">
													<div class="total-label-row" style="border-top: 2px solid #007bff;
														padding-top: 12px;
														margin-top: 8px;
														background-color: #f0f8ff;
														padding: 12px;
														border-radius: 4px;">
														<label
															style="font-size: 16px; color: #007bff; font-weight: 700; padding-right: 15px;">
															Net Amt:
														</label>
														<div class="total-display highlight" id="net_amt_display"
															style="font-size: 16px;">
															0.00
														</div>
													</div>
												</div>

											</div>
										</div>

									</div>
								</div>

								<div class="col-md-12"
									style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
									<button type="submit"
										class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>">
										<i class="fa fa-save"></i> Save
									</button>
									<button type="button" class="btn btn-primary printPdfBtn updateBtn"
										style="display: none; margin-right:8px;" onclick="printPurchaseOrderPdf();">
										<i class="fa fa-print"></i> Print PDF
									</button>
									<button type="submit"
										class="btn btn-success updateBtn <?= (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>"
										style="display: none;">
										<i class="fa fa-save"></i> Update
									</button>
									<script>
										function printPurchaseOrderPdf() {
											var orderNo = $('#order_no').val();
											if (!orderNo) {
												alert_float('warning', 'Order No not found!');
												return;
											}
											var url = "<?= admin_url('purchase/PurchOrderPrint/'); ?>" + orderNo;
											window.open(url, '_blank');
										}
									</script>
									<button type="button" class="btn btn-danger" onclick="ResetForm();">
										<i class="fa fa-refresh"></i> Reset
									</button>
									<button type="button" class="btn btn-info" onclick="showPurchaseOrderList();">
										<i class="fa fa-list"></i> Show List
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php init_tail(); ?>

<script>
	// =============================================
	// RESET FORM
	// =============================================
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
		$('#attachment_link_container').html('');
		$('#attachment').show();
	}

	// =============================================
	// ONLY NUMBERS & DOT ALLOWED IN TEL INPUTS
	// =============================================
	$(document).on('input', 'input[type="tel"]', function () {
		this.value = this.value
			.replace(/[^0-9.]/g, '')
			.replace(/(\..*?)\..*/g, '$1');
	});

	// =============================================
	// VALIDATE REQUIRED FIELDS
	// =============================================
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
	// ADD ROW TO ITEMS TABLE
	// =============================================
	function addRow(row = null) {
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
			<select id="item_id${next_id}" name="item_id[]" class="form-control dynamic_row${next_id} dynamic_item selectpicker"
				data-live-search="true" app-field-label="Item Name"
				onchange="getItemDetails(this.value, '${next_id}');">${item_option}</select>
		</td>
		<td><input type="text"  name="hsn_code[]"    id="hsn_code${next_id}"    class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
		<td><input type="text"  name="uom[]"         id="uom${next_id}"         class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
		<td><input type="tel"   name="unit_weight[]" id="unit_weight${next_id}" class="form-control unit-weight dynamic_row${next_id}" min="0" step="0.01" readonly tabindex="-1"></td>
		<td><input type="tel"   name="min_qty[]"     id="min_qty${next_id}"     class="form-control min-qty dynamic_row${next_id}"     min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
		<td><input type="tel"   name="max_qty[]"     id="max_qty${next_id}"     class="form-control max-qty dynamic_row${next_id}"     min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
		<td><input type="tel"   name="disc_amt[]"    id="disc_amt${next_id}"    class="form-control disc-amt dynamic_row${next_id}"    min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
		<td><input type="tel"   name="unit_rate[]"   id="unit_rate${next_id}"   class="form-control unit-rate dynamic_row${next_id}"   min="0" step="0.01" onchange="calculateAmount(${next_id})"></td>
		<td><input type="tel"   name="gst[]"         id="gst${next_id}"         class="form-control gst-percent dynamic_row${next_id}" min="0" max="100" step="0.01" readonly tabindex="-1"></td>
		<td><input type="tel"   name="amount[]"      id="amount${next_id}"      class="form-control dynamic_row${next_id}" readonly tabindex="-1"></td>
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

	// =============================================
	// CALCULATE AMOUNT FOR ONE ROW
	// =============================================
	function calculateAmount(row) {
		var minQty = parseFloat($('#min_qty' + row).val()) || 0;
		var unitRate = parseFloat($('#unit_rate' + row).val()) || 0;
		var discAmt = parseFloat($('#disc_amt' + row).val()) || 0;
		var gstPercent = parseFloat($('#gst' + row).val()) || 0;

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

	// =============================================
	// CALCULATE ALL TOTALS
	// =============================================
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

			var rowTaxableAmt = (rate - discAmt) * qty;
			var rowGstAmt = rowTaxableAmt * (gstPercent / 100);

			totalWeight += weight * qty;
			totalQty += qty;
			itemTotalAmt += rate * qty;
			totalDiscAmt += discAmt * qty;
			totalTaxableAmt += rowTaxableAmt;
			totalGstAmt += rowGstAmt;
		});

		$('#total_weight_display').text(totalWeight.toFixed(2));
		$('#total_qty_display').text(totalQty.toFixed(2));
		$('#item_total_amt_display').text(itemTotalAmt.toFixed(2));
		$('#disc_amt_display').text(totalDiscAmt.toFixed(2));
		$('#taxable_amt_display').text(totalTaxableAmt.toFixed(2));


		var vendorState = $('#vendor_state').val().trim().toUpperCase();
		var cgstAmt = 0, sgstAmt = 0, igstAmt = 0;

		// GST calculation: If vendor_state is MAHARASHTRA, use CGST+SGST, else IGST
		if (vendorState === 'MAHARASHTRA') {
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

	// =============================================
	// GET REQUIRED FIELDS
	// =============================================
	function get_required_fields(form_id) {
		let fields = [];
		$('#' + form_id + ' [required]').each(function () {
			fields.push($(this).attr('id'));
		});
		return fields;
	}

	// =============================================
	// CUSTOM DROPDOWN LIST (ITEM TYPE -> CATEGORY)
	// =============================================
	function getCustomDropdownList(parent_id, parent_value, child_id, selected_value = null, callback = null) {
		$.ajax({
			url: "<?= admin_url(); ?>ItemMaster/GetCustomDropdownList",
			type: 'POST',
			dataType: 'json',
			data: { parent_id: parent_id, parent_value: parent_value, child_id: child_id },
			success: function (response) {
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
					alert_float('danger', response.message);
				}
			}
		});
	}

	// =============================================
	// GET NEXT QUOTATION NO & ITEM LIST
	// =============================================
	function getNextQuotationNo(callback = null) {
		var categoryId = $('#item_category').val();
		if (!categoryId) { $('#quotation_no').val(''); return; }
		$.ajax({
			url: '<?= admin_url('purchase/Quotation/getNextQuotationNo'); ?>',
			type: 'POST',
			data: { category_id: categoryId },
			dataType: 'json',
			success: function (response) {
				if (response.success == true) {
					if ($('#form_mode').val() == 'add') {
						$('#quotation_no').val(response.quote_no);
					}
					var html = '<option value="" selected disabled>Select Item</option>';
					$.each(response.item_list, function (index, val) {
						html += '<option value="' + val.ItemId + '">' + val.ItemName + '</option>';
					});
					$('#item_id').html(html);
					$('.selectpicker').selectpicker('refresh');
				} else {
					$('#quotation_no').val('');
				}
				if (callback) callback();
			},
			error: function () { $('#quotation_no').val(''); }
		});

		if ($('#form_mode').val() == 'add') {
			$.ajax({
				url: '<?= admin_url('purchase/getNextOrderNo'); ?>',
				type: 'POST',
				data: { category_id: categoryId },
				dataType: 'json',
				success: function (response) {
					if (response.success == true) {
						$('#order_no').val(response.order_no);
					} else {
						$('#order_no').val('');
					}
				},
				error: function () { $('#order_no').val(''); }
			});
		}
	}

	// =============================================
	// GET VENDOR DETAILS + SHIPPING LOCATIONS
	// =============================================
	function getVendorDetailsLocation(callback = null) {
		var vendorId = $('#vendor_id').val();
		if (!vendorId) {
			$('#vendor_location').html('<option value="" selected disabled>None selected</option>');
			$('.selectpicker').selectpicker('refresh');
			return;
		}

		function loadShippingLocations(cb) {
			$.ajax({
				url: '<?= admin_url('purchase/GetVendorShippingLocations'); ?>',
				type: 'POST',
				data: { AccountID: vendorId },
				dataType: 'json',
				success: function (res) {
					var html = '<option value="" selected disabled>None selected</option>';
					if (res.status === 'success' && res.locations && res.locations.length > 0) {
						$.each(res.locations, function (i, loc) {
							html += '<option value="' + loc.id + '">' + loc.city + '</option>';
						});
					}
					$('#vendor_location').html(html);
					if ($.fn.selectpicker !== undefined) $('#vendor_location').selectpicker('refresh');
					if (cb) cb();
				},
				error: function () { if (cb) cb(); }
			});
		}

		$.ajax({
			url: '<?= admin_url('purchase/GetVendorDetails'); ?>',
			type: 'POST',
			data: { AccountID: vendorId },
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success' && response.data) {
					var d = response.data;
					$('#vendor_gst_no').val(d.gst_no || '');
					$('#vendor_country').val(d.country || '');
					$('#vendor_state').val(d.state || '');
					$('#vendor_address').val(d.address || '');
					calculateTotals();
				}
				loadShippingLocations(callback);
			},
			error: function () { loadShippingLocations(callback); }
		});

		$.ajax({
			url: '<?= admin_url('purchase/GetQuotationMaster'); ?>',
			type: 'POST',
			data: { AccountID: vendorId },
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success' && response.data) {
					var dataArr = response.data;
					var $quoteSelect = $('#vendor_quote_no');
					$quoteSelect.empty();
					$quoteSelect.append('<option value="">Select Quotation No</option>');
					if (Array.isArray(dataArr)) {
						dataArr.forEach(function (item) {
							if (item.QuotatioonID) {
								$quoteSelect.append('<option value="' + item.QuotatioonID + '">' + item.QuotatioonID + '</option>');
							}
						});
					} else if (dataArr.QuotatioonID) {
						$quoteSelect.append('<option value="' + dataArr.QuotatioonID + '">' + dataArr.QuotatioonID + '</option>');
					}
					$quoteSelect.selectpicker('refresh');
					calculateTotals();
				}
				loadShippingLocations(callback);
			},
			error: function () { loadShippingLocations(callback); }
		});
	}

	// =============================================
	// GET ITEM DETAILS
	// =============================================
	function getItemDetails(itemId, id = '') {
		var isDuplicate = false;
		$('.dynamic_item').not('#item_id' + id).each(function () {
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
			url: '<?= admin_url('purchase/GetItemDetails'); ?>',
			type: 'POST',
			data: { item_id: itemId },
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success' && response.data) {
					var data = response.data;
					$('#hsn_code' + id).val(data.hsn_code || '');
					$('#uom' + id).val(data.unit || '');
					$('#unit_weight' + id).val(Number(data.UnitWeight) || 0);
					$('#gst' + id).val(Number(data.tax) || 0);
					$('#min_qty' + id).focus();
					if (data.gst_no) $('#vendor_gst_no').val(data.gst_no);
					if (data.country) $('#vendor_country').val(data.country);
					if (data.state) $('#vendor_state').val(data.state);
					if (data.address) $('#vendor_address').val(data.address);
				} else {
					$('.fixed_row').val('');
					$('.selectpicker').selectpicker('refresh');
				}
				calculateTotals();
			},
			error: function (xhr, status, err) { console.log('Error fetching item details:', err); }
		});
	}

	// =============================================
	// COLLECT ITEMS JSON
	// =============================================
	function collectItemsJson() {
		var items = [];
		$('#items_body tr').each(function () {
			var row = $(this);
			var item_id = row.find('select[name="item_id[]"]').val() || '';
			if (!item_id) return;
			items.push({
				item_id: item_id,
				item_uid: row.find('input[name="item_uid[]"]').val() || '0',
				hsn_code: row.find('input[name="hsn_code[]"]').val() || '',
				uom: row.find('input[name="uom[]"]').val() || '',
				unit_weight: row.find('input[name="unit_weight[]"]').val() || '0',
				min_qty: row.find('input[name="min_qty[]"]').val() || '0',
				max_qty: row.find('input[name="max_qty[]"]').val() || '0',
				disc_amt: row.find('input[name="disc_amt[]"]').val() || '0',
				unit_rate: row.find('input[name="unit_rate[]"]').val() || '0',
				gst: row.find('input[name="gst[]"]').val() || '0',
				amount: row.find('input[name="amount[]"]').val() || '0'
			});
		});
		return items;
	}

	// =============================================
	// FORM SUBMIT
	// =============================================
	$('#main_save_form').on('submit', function (e) {
		e.preventDefault();

		let form_mode = $('#form_mode').val();
		let required_fields = get_required_fields('main_save_form');
		let validated = validate_fields(required_fields);
		if (validated === false) return;

		var items = collectItemsJson();
		if (items.length === 0) {
			alert_float('warning', 'Please add at least one item.');
			return;
		}

		var form_data = new FormData(this);
		form_data.set('items_json', JSON.stringify(items));
		form_data.append(
			'<?= $this->security->get_csrf_token_name(); ?>',
			$('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		);

		if (form_mode == 'edit') {
			form_data.set('PurchID', $('#order_no').val());
			$.ajax({
				url: "<?= admin_url(); ?>purchase/UpdatePurchaseOrder",
				method: "POST",
				dataType: "JSON",
				data: form_data,
				contentType: false,
				cache: false,
				processData: false,
				beforeSend: function () { $('button[type=submit]').attr('disabled', true); },
				complete: function () { $('button[type=submit]').attr('disabled', false); },
				success: function (response) {
					if (response.success == true) {
						alert_float('success', response.message);
						ResetForm();
					} else {
						alert_float('warning', response.message);
					}
				}
			});
		} else {
			$.ajax({
				url: "<?= admin_url(); ?>purchase/savepurchase",
				method: "POST",
				dataType: "JSON",
				data: form_data,
				contentType: false,
				cache: false,
				processData: false,
				beforeSend: function () { $('button[type=submit]').attr('disabled', true); },
				complete: function () { $('button[type=submit]').attr('disabled', false); },
				success: function (response) {
					if (response.success == true) {
						alert_float('success', response.message);
						ResetForm();
						loadPurchaseOrderList();
					} else {
						alert_float('warning', response.message);
					}
				}
			});
		}
	});

	// =============================================
	// GET DETAILS FOR EDIT
	// =============================================
	function getDetails(id) {
		ResetForm();
		$.ajax({
			url: "<?= admin_url(); ?>purchase/GetPurchaseOrderDetails",
			method: "POST",
			dataType: "JSON",
			data: { id: id },
			success: function (response) {
				if (response.success == true) {
					let d = response.data;
					$('#update_id').val(id);
					$('#item_type').val(d.ItemType);
					getCustomDropdownList('item_type', d.ItemType, 'item_category', d.ItemCategory, function () {
						$('#item_category').val(d.ItemCategory);
						$('.selectpicker').selectpicker('refresh');
						getNextQuotationNo(function () {
						if (d.QuatationID) {
								if ($('#vendor_quote_no option[value="' + d.QuatationID + '"]').length === 0) {
									$('#vendor_quote_no').append('<option value="' + d.QuatationID + '">' + d.QuatationID + '</option>');
								}
								$('#vendor_quote_no').val(d.QuatationID);
								$('#vendor_quote_no').selectpicker('refresh');
							}
							$('#items_body').empty();
							$('#row_id').val(0);
							let history = response.data.history;
							if (Array.isArray(history) && history.length > 0) {
								for (var i = 0; i < history.length; i++) {
									addRow(2);
									let idx = i + 1;
									$('#item_uid' + idx).val(history[i].id);
									$('#item_id' + idx).val(history[i].ItemID);
									getItemDetails(history[i].ItemID, idx);
									$('#min_qty' + idx).val(Number(history[i].OrderQty));
									$('#max_qty' + idx).val(Number(history[i].OrderQty));
									$('#unit_rate' + idx).val(Number(history[i].BasicRate));
									$('#disc_amt' + idx).val(Number(history[i].DiscAmt));
									$('#amount' + idx).val(Number(history[i].NetOrderAmt));
									$('.selectpicker').selectpicker('refresh');
									calculateAmount(idx);
								}
							}
						});
					});

					$('#order_no').val(d.PurchID);
					$('#quotation_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
					$('#purchase_location').val(d.PurchaseLocation);
					$('#vendor_id').val(d.AccountID);

					getVendorDetailsLocation(function () {
						$('#vendor_location').val(d.DeliveryLocation);
						$('#vendor_location').selectpicker('refresh');
					});

					$('#delivery_from').val(moment(d.DeliveryFrom).format('DD/MM/YYYY'));
					$('#delivery_to').val(moment(d.DeliveryTo).format('DD/MM/YYYY'));
					$('#payment_terms').val(d.PaymentTerms);
					$('#freight_terms').val(d.FreightTerms);
					$('#vendor_quote_date').val(moment(d.TransDate).format('DD/MM/YYYY') || '');
					$('#vendor_doc_no').val(d.VendorDocNo || '');
					$('#vendor_doc_date').val(moment(d.VendorDocDate).format('DD/MM/YYYY') || '');
					$('#internal_remarks').val(d.Internal_Remarks || '');
					$('#document_remark').val(d.Document_Remark || '');
					$('#currency').val(d.CurrencyID || '');

					if (d.Attachment) {
						var attachUrl = '<?= base_url(); ?>' + d.Attachment;
						var viewHtml = '<a href="' + attachUrl + '" target="_blank" style="font-weight:bold; color:#007bff; text-decoration:underline;">View</a>';
						$('#attachment_link_container').html(viewHtml);
					} else {
						$('#attachment_link_container').html('');
					}

					$('.selectpicker').selectpicker('refresh');
					$('#form_mode').val('edit');
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('#ListModal').modal('hide');
				} else {
					alert_float('warning', response.message);
				}
			}
		});
	}

	// =============================================
	// CLICK HANDLER FOR LIST ROWS
	// =============================================
	$(document).on('click', '#table_ListModal .get_Details', function () {
		var id = $(this).data('id');
		if (id) {
			getDetails(id);
			$('#ListModal').modal('hide');
		}
	});
</script>

<script>
	// =============================================
	// LIST MODAL - SORT & SEARCH
	// =============================================
	$(document).on("click", ".sortable", function () {
		var table = $("#table_ListModal tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");

		$(".sortable").removeClass("asc desc");
		$(".sortable span").remove();
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

	// =============================================
	// SHOW PURCHASE ORDER LIST
	// =============================================
	function showPurchaseOrderList() {
		var today = moment();
		var firstOfMonth = moment().startOf('month');

		$('#from_date_modal').val(firstOfMonth.format('DD/MM/YYYY'));
		$('#to_date_modal').val(today.format('DD/MM/YYYY'));
		$('#filter_category').val('');  // ✅ Category reset

		$('#ListModal').modal('show');

		setTimeout(function () {
			$('#filter_category').selectpicker('refresh');
		}, 300);

		loadPurchaseOrderList();
	}

	// =============================================
	// LOAD PURCHASE ORDER LIST (AJAX) - WITH CATEGORY FILTER
	// =============================================
	function loadPurchaseOrderList() {
		var from_date = $('#from_date_modal').val();
		var to_date = $('#to_date_modal').val();
		var category = $('#filter_category').val(); // ✅ Category filter value

		// if (!from_date || !to_date) {
		// 	alert_float('warning', 'Please select both From Date and To Date.');
		// 	return;
		// }

		$('#table_ListModal tbody').html(
			'<tr><td colspan="12" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
		);

		$.ajax({
			url: '<?= admin_url('purchase/get_purchase_order_data'); ?>',
			type: 'GET',
			data: {
				from_date: from_date,
				to_date: to_date,
				category: category
			},
			dataType: 'json',
			success: function (data) {
				var html = '';
				if (Array.isArray(data) && data.length > 0) {
					$.each(data, function (i, row) {
						html += `<tr class="get_Details order-main-row" data-id="${row.id}" style="cursor:pointer;">
						<td>${row.PurchID || ''}</td>
						<td>${row.CategoryName || ''}</td>
						<td>${row.LocationName || ''}</td>
						<td>${row.company || ''}</td>
						<td>${row.TransDate ? moment(row.TransDate).format('DD/MM/YYYY') : ''}</td>
						<td>${row.ShippingCityName || ''}</td>
						<td>${row.DeliveryFrom ? moment(row.DeliveryFrom).format('DD/MM/YYYY') : ''}</td>
						<td>${row.DeliveryTo ? moment(row.DeliveryTo).format('DD/MM/YYYY') : ''}</td>
						<td>${row.GSTIN || ''}</td>
						<td>${row.TotalWeight || '0.00'}</td>
						<td>${row.TotalQuantity || '0.00'}</td>
						<td>${row.NetAmt || '0.00'}</td>
					</tr>`;
					});
				} else {
					html = '<tr><td colspan="12" class="text-center text-muted">No records found for selected filter.</td></tr>';
				}
				$('#table_ListModal tbody').html(html);
			},
			error: function () {
				$('#table_ListModal tbody').html(
					'<tr><td colspan="12" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
				);
			}
		});
	}

	// =============================================
	// SHOW BUTTON CLICK - Date + Category Filter
	// =============================================
	$(document).on('click', '#show_filter_btn', function () {
		var from_date = $('#from_date_modal').val();
		var to_date = $('#to_date_modal').val();

		if (!from_date || !to_date) {
			alert_float('warning', 'Please select both From Date and To Date.');
			return;
		}

		var from_m = moment(from_date, 'DD/MM/YYYY');
		var to_m = moment(to_date, 'DD/MM/YYYY');

		if (from_m.isAfter(to_m)) {
			alert_float('warning', 'From Date cannot be greater than To Date.');
			return;
		}

		loadPurchaseOrderList();
	});

	// =============================================
	// VENDOR QUOTE NO CHANGE
	// =============================================
	function onVendorQuoteNoChange(quoteId) {
		if (!quoteId) {
			$('#vendor_quote_date').val('');
			return;
		}
		$.ajax({
        	url: '<?= admin_url('purchase/Quotation/GetQuotationDetails'); ?>',
			type: 'POST',
			data: { id: quoteId },
			dataType: 'json',
			success: function (response) {
				if (response.success === true) {
					$('#items_body').html('');
					$('#row_id').val(0);
					let d = response.data;
					$('#item_type').val(d.ItemType);
					getCustomDropdownList('item_type', d.ItemType, 'item_category', d.ItemCategory, function(){
						getNextQuotationNo(function(){
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
					});
					$('#vendor_quote_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
					$('#purchase_location').val(d.PurchaseLocation);
					// $('#vendor_id').val(d.AccountID);
					// getVendorDetailsLocation(function(){
						$('#vendor_location').val(d.DeliveryLocation);
					//   $('#broker_id').val(d.BrokerID);
					// });
					// $('#quotation_validity').val(d.Validity.split(' ')[0]);
					$('#delivery_from').val(moment(d.DeliveryFrom).format('DD/MM/YYYY'));
					$('#delivery_to').val(moment(d.DeliveryTo).format('DD/MM/YYYY'));
					$('#payment_terms').val(d.PaymentTerms);
					$('#freight_terms').val(d.FreightTerms);

					$('.selectpicker').selectpicker('refresh');
				} else {
					$('#vendor_quote_date').val('');
				}
			},
			error: function () {
				$('#vendor_quote_date').val('');
			}
		});
	}

	// =============================================
	// DOCUMENT READY
	// =============================================
	$(document).ready(function () {
		if ($.fn.datetimepicker) {
			$('#from_date_modal, #to_date_modal').datetimepicker({
				format: 'd/m/Y',
				timepicker: false,
				scrollMonth: false,
				scrollInput: false
			});
		}
	});
</script>

<style>
	.sub-table-row td {
		padding: 0 !important;
	}

	.sub-table-wrapper {
		padding: 10px 20px;
	}

	.sub-table-wrapper table {
		font-size: 11px;
	}

	.sub-table-wrapper th {
		background: #2e6da4 !important;
		color: #fff !important;
	}

	.sub-table-wrapper td {
		background: #eef4ff;
	}

	.get_Details:hover {
		background-color: #e8f4ff !important;
		cursor: pointer;
	}
</style>