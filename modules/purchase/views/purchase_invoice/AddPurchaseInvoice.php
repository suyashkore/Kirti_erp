<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

	<div class="content">

		<div class="row">

			<?php

				echo form_open($this->uri->uri_string(),array('id'=>'pur_invoice-form','class'=>'_transaction_form','enctype'=>'multipart/form-data'));

			?>

			<div class="col-md-12">

				<div class="panel_s">

					<div class="panel-body">

						<nav aria-label="breadcrumb">

							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">

								<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>

								<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>

								<li class="breadcrumb-item active" aria-current="page"><b>Add Purchase Invoice</b></li>

							</ol>

						</nav>

						<hr class="hr_style">

<style>
					.pi-form .form-group {
						margin-bottom: 15px;
					}

					.pi-form h4 {
						margin-top: 20px;
						margin-bottom: 12px;
						font-size: 16px;
						font-weight: 600;
					}

					.pi-form hr.hr_style {
						margin-top: 8px;
						margin-bottom: 15px;
						border: none;
						border-top: 2px solid #ddd;
					}

					.pi-form label {
						font-weight: 500;
						margin-bottom: 6px;
						display: block;
					}

					.pi-form .form-control {
						border: 1px solid #ddd;
						padding: 8px 12px;
						height: auto;
						font-size: 13px;
					}

					.pi-form .form-control:focus {
						border-color: #80bdff;
						box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
					}

					.pi-form textarea.form-control {
						resize: vertical;
						min-height: 60px;
					}

					.pi-form .selectpicker {
						height: 36px !important;
					}

					.req.text-danger {
						color: #dc3545;
						font-weight: bold;
						margin-right: 3px;
					}

					.table-responsive {
						border: 1px solid #ddd;
						border-radius: 4px;
						margin-top: 15px;
						overflow-x: auto;
						max-height: 500px;
						overflow-y: auto;
					}

					#items_table {
						table-layout: fixed;
						margin-bottom: 0;
						width: 100%;
						min-width: 1600px;
					}

					#items_table thead {
						background-color: #50607b;
						color: #fff;
						font-weight: 600;
						font-size: 12px;
						position: sticky;
						top: 0;
						z-index: 10;
					}

					#items_table th {
						padding: 8px 5px !important;
						text-align: left;
						vertical-align: middle;
						border: 1px solid #ddd;
						white-space: nowrap;
						line-height: 1.42857143;
						overflow: hidden;
						text-overflow: ellipsis;
					}

					#items_table td {
						padding: 5px !important;
						border: 1px solid #ddd;
						vertical-align: middle;
						font-size: 12px;
						line-height: 1.42857143;
					}

					/* Column Width Specifications */
					#items_table th:nth-child(1),
					#items_table td:nth-child(1) {
						width: 5%;
						min-width: 50px;
					}

					#items_table th:nth-child(2),
					#items_table td:nth-child(2) {
						width: 12%;
						min-width: 120px;
					}

					#items_table th:nth-child(3),
					#items_table td:nth-child(3) {
						width: 8%;
						min-width: 80px;
					}

					#items_table th:nth-child(4),
					#items_table td:nth-child(4) {
						width: 8%;
						min-width: 80px;
					}

					#items_table th:nth-child(5),
					#items_table td:nth-child(5) {
						width: 7%;
						min-width: 70px;
					}

					#items_table th:nth-child(6),
					#items_table td:nth-child(6) {
						width: 7%;
						min-width: 70px;
					}

					#items_table th:nth-child(7),
					#items_table td:nth-child(7) {
						width: 7%;
						min-width: 70px;
					}

					#items_table th:nth-child(8),
					#items_table td:nth-child(8) {
						width: 8%;
						min-width: 80px;
					}

					#items_table th:nth-child(9),
					#items_table td:nth-child(9) {
						width: 7%;
						min-width: 70px;
					}

					#items_table th:nth-child(10),
					#items_table td:nth-child(10) {
						width: 8%;
						min-width: 80px;
					}

					#items_table th:nth-child(11),
					#items_table td:nth-child(11) {
						width: 7%;
						min-width: 70px;
					}

					#items_table th:nth-child(12),
					#items_table td:nth-child(12) {
						width: 7%;
						min-width: 70px;
					}

					#items_table th:nth-child(13),
					#items_table td:nth-child(13) {
						width: 8%;
						min-width: 80px;
					}

					#items_table th:nth-child(14),
					#items_table td:nth-child(14) {
						width: 6%;
						min-width: 60px;
					}

					#items_table th:nth-child(15),
					#items_table td:nth-child(15) {
						width: 8%;
						min-width: 80px;
					}

					#items_table th:nth-child(16),
					#items_table td:nth-child(16) {
						width: 6%;
						min-width: 60px;
					}

					#items_table td input,
					#items_table td select,
					#items_table td textarea {
						width: 100% !important;
						border: 1px solid #ccc !important;
						padding: 6px !important;
						margin: 0 !important;
						font-size: 12px !important;
					}

					#items_table td textarea {
						resize: vertical;
						min-height: 35px;
						white-space: normal;
					}

					#items_table td input[readonly],
					#items_table td select[readonly] {
						background-color: #e9ecef;
						cursor: not-allowed;
					}

					#items_table .btn-sm {
						padding: 4px 8px;
						font-size: 12px;
					}

					@media (max-width: 767px) {
						.pi-form {
							font-size: 13px;
						}

						.table-responsive {
							max-height: 400px;
							overflow-y: auto;
						}

						#items_table {
							table-layout: fixed;
							min-width: 1400px;
							font-size: 11px;
						}

						#items_table thead {
							position: sticky;
							top: 0;
							z-index: 10;
						}

						#items_table th,
						#items_table td {
							padding: 4px 3px !important;
							white-space: nowrap;
						}

						#items_table td input,
						#items_table td select,
						#items_table td textarea {
							padding: 4px !important;
							font-size: 11px !important;
						}
					}
				</style>

						<form id="pur_invoice-form" method="post" class="pi-form">

							<!-- GENERAL DETAILS SECTION -->
							<div class="row">
								<div class="col-md-12">
									<h4 class="bold p_style">General Details</h4>
									<hr class="hr_style">
								</div>

									<div class="col-md-2 col-sm-6 col-xs-12">
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
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Purchase Order No</label>
										<select name="purchase_order_no" id="purchase_order_no" class="form-control selectpicker" data-live-search="true" required>
											<option value="">-- Select PO --</option>
											<?php if(isset($purchase_orders)) { foreach($purchase_orders as $po) { ?>
												<option value="<?php echo $po['id']; ?>"><?php echo $po['po_no']; ?></option>
											<?php }} ?>
										</select>
										<input type="text" name="purchase_order_no_text" id="purchase_order_no_text" class="form-control" style="display:none;" placeholder="Enter PO No" autocomplete="off">
									</div>
								</div>
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Plant</label>
										<select name="plant" id="plant" class="form-control selectpicker" data-live-search="true" required>
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

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Posting Date</label>
										<input type="date" name="posting_date" id="posting_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Arrival Date</label>
										<input type="date" name="arrival_date" id="arrival_date" class="form-control">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group" app-field-wrapper="item_type">
										<label for="item_type" class="control-label"><small class="req text-danger">*
											</small> Item / Service</label>
										<input type="text" name="item_type" id="item_type" class="form-control" app-field-label="Item / Service" placeholder="Enter Item / Service" autocomplete="off">

									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>PO Based</label>
										<select name="po_based" id="po_based" class="form-control">
											<option value="No">No</option>
											<option value="Yes">Yes</option>
										</select>
									</div>
								</div>

							

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Vendor Location</label>
										<select name="vendor_location" id="vendor_location" class="form-control selectpicker" data-live-search="true" required>
											<option value="">-- Select Location --</option>
											<?php if(isset($vendor_locations)) { foreach($vendor_locations as $loc) { ?>
												<option value="<?php echo $loc['id']; ?>"><?php echo $loc['name']; ?></option>
											<?php }} ?>
										</select>
									</div>
								</div>

								

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Purchase Category</label>
										<input type="text" name="purchase_category" id="purchase_category" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Doc Date</label>
										<input type="date" name="vendor_doc_date" id="vendor_doc_date" class="form-control">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Doc Amount</label>
										<input type="number" step="0.01" name="vendor_doc_amount" id="vendor_doc_amount" class="form-control">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Doc No</label>
										<input type="text" name="vendor_doc_no" id="vendor_doc_no" class="form-control">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Broker</label>
										<input type="text" name="broker" id="broker" class="form-control" placeholder="Broker name">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Lorry No</label>
										<input type="text" name="lorry_no" id="lorry_no" class="form-control" placeholder="Vehicle number">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Payment Terms</label>
									<input type="text" name="payment_terms" id="payment_terms" class="form-control" placeholder="Vehicle number">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Hold Payment</label>
										<select name="hold_payment" id="hold_payment" class="form-control" required>
											<option value="No" selected>No</option>
											<option value="Yes">Yes</option>
										</select>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>TDS Code</label>
										<input type="text" name="tds_code" id="tds_code" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Dispatch Weight</label>
										<input type="number" step="0.01" name="vendor_dispatch_weight" id="vendor_dispatch_weight" class="form-control" placeholder="Weight in kg">
									</div>
								</div>

							</div>

							<!-- ITEMS SECTION -->
							<div class="row">
								<div class="col-md-12">
									<h4 class="bold p_style">Items / Services</h4>
									<hr class="hr_style">
								</div>

								<div class="col-md-12">
									<div class="table-responsive">
										<table width="100%" class="table" id="items_table">
											<thead>
												<tr>
													<th>S.N.</th>
													<th>Item Name</th>
													<th>PO Original Qty</th>
													<th>PO Balance Qty</th>
													<th>UOM</th>
													<th>Total Bag</th>
													<th>Receipt Qty</th>
													<th>Unit Price</th>
													<th>Receipt UOM</th>
													<th>Rebate Settlement</th>
													<th>Rate</th>
													<th>Rate UOM</th>
													<th>Calc Rate</th>
													<th>GST %</th>
													<th>Amount</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody id="items_body">
												<!-- Rows will be added here dynamically -->
											</tbody>
										</table>
									</div>
									<!-- <div style="margin-top: 15px;">
										<button type="button" id="add-item-row-btn" class="btn btn-primary btn-sm">
											<i class="fa fa-plus"></i> Add Row
										</button>
									</div> -->
								</div>
							</div>

							<!-- PURCHASE & DELIVERY DETAILS SECTION -->
							<div class="row">
								<div class="col-md-12">
									<h4 class="bold p_style">Purchase & Delivery Details</h4>
									<hr class="hr_style">
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Business Unit</label>
										<select name="business_unit" id="business_unit" class="form-control selectpicker" data-live-search="true">
											<option value="">-- Select Business Unit --</option>
										    <?php if(isset($vendor_locations)) { foreach($vendor_locations as $loc) { ?>
												<option value="<?php echo $loc['id']; ?>"><?php echo $loc['name']; ?></option>
											<?php }} ?>
										</select>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Freight Terms</label>
										<input type="text" name="freight_terms" id="freight_terms" class="form-control">
									
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
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

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Gate Entry No</label>
										<input type="text" name="gate_entry_no" id="gate_entry_no" class="form-control">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Gate Entry Date</label>
										<input type="date" name="gate_entry_date" id="gate_entry_date" class="form-control">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Quotation No</label>
										<input type="text" name="vendor_quote_no" id="vendor_quote_no" class="form-control" placeholder="Enter quotation number">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Quotation Date</label>
										<input type="date" name="vendor_quote_date" id="vendor_quote_date" class="form-control">
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Ref Doc No</label>
										<input type="text" name="ref_doc_no" id="ref_doc_no" class="form-control" placeholder="Enter reference document number">
									</div>
								</div>
							</div>

							<!-- VENDOR DETAILS SECTION -->
							<div class="row">
								<div class="col-md-12">
									<h4 class="bold p_style">Vendor Details</h4>
									<hr class="hr_style">
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Country</label>
										<input type="text" name="vendor_country" id="vendor_country" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>State</label>
										<input type="text" name="vendor_state" id="vendor_state" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>City</label>
										<input type="text" name="vendor_city" id="vendor_city" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Pincode</label>
										<input type="text" name="vendor_pincode" id="vendor_pincode" class="form-control" readonly>
									</div>
								</div>

								

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>GST No</label>
										<input type="text" name="vendor_gst_no" id="vendor_gst_no" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>PAN</label>
										<input type="text" name="vendor_pan" id="vendor_pan" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Payment Cycle Type</label>
										<input type="text" name="vendor_payment_cycle_type" id="vendor_payment_cycle_type" class="form-control" readonly>
									</div>
								</div>

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Payment Cycle</label>
										<input type="text" name="vendor_payment_cycle" id="vendor_payment_cycle" class="form-control" readonly>
									</div>
								</div>
								<div class="col-md-6 col-sm-12 col-xs-12">
									<div class="form-group">
										<label>Address</label>
										<textarea name="vendor_address" id="vendor_address" class="form-control" rows="1" readonly></textarea>
									</div>
								</div>
							</div>

							<!-- OTHER INFORMATION SECTION -->
							<div class="row">
								<div class="col-md-12">
									<h4 class="bold p_style">Other Information</h4>
									<hr class="hr_style">
								</div>

								<div class="col-md-4 col-sm-12 col-xs-12">
									<div class="form-group">
										<label>Internal Remarks</label>
										<textarea name="internal_remarks" id="internal_remarks" class="form-control" rows="3" placeholder="Enter internal remarks..."></textarea>
									</div>
								</div>

								<div class="col-md-4 col-sm-12 col-xs-12">
									<div class="form-group">
										<label>Document Remark</label>
										<textarea name="document_remark" id="document_remark" class="form-control" rows="3" placeholder="Enter document remarks..."></textarea>
									</div>
								</div>

								<div class="col-md-4 col-sm-12 col-xs-12">
									<div class="form-group">
										<label>Attachment</label>
										<input type="file" name="attachment" id="attachment" class="form-control">
										<small style="display:block;color:#666;margin-top:4px;">Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</small>
									</div>
								</div>
							</div>
                      <br>
							<!-- ACTION BUTTONS -->
							<div class="row">
								<div class="col-md-12">
									<div class="btn-bottom-toolbar text-right" style="left: -231px;">
										<a href="<?= admin_url('purchase/purchase_invoices'); ?>" class="btn btn-info">
											<i class="fa fa-list"></i> Show All
										</a>
										<button type="reset" class="btn btn-warning">
											<i class="fa fa-refresh"></i> Reset
										</button>
										<?php if (has_permission_new('purchase-invoice', '', 'create')){ ?>
										<button type="submit" class="btn btn-success">
											<i class="fa fa-save"></i> Save & Submit
										</button>
										<?php } ?>
									</div>
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
$(document).ready(function(){
	// Function to handle PO number change
	// function handlePurchaseOrderNoChange(poNo) {
	// 	if (!poNo) return;
	// 	// Example API call with PO number (replace URL and logic as needed)
	// 	   $.ajax({
	// 		   url: '<?= admin_url('purchase/GetvandocDetails'); ?>',
	// 		   type: 'POST',
	// 		   data: { purchase_order_no: poNo },
	// 		   dataType: 'json',
	// 		   success: function(res) {
	// 			   if (res && res.success && Array.isArray(res.data) && res.data.length > 0) {
	// 				   var d = res.data[0];
	// 				   // Format date to dd-mm-yyyy
	// 				   var formattedDate = '';
	// 				   var formattedDate1 = '';
	// 				   if (d.qutotationdate) {
	// 					   var dateObj = new Date(d.qutotationdate);
	// 					   var day = ('0' + dateObj.getDate()).slice(-2);
	// 					   var month = ('0' + (dateObj.getMonth() + 1)).slice(-2);
	// 					   var year = dateObj.getFullYear();
	// 					   formattedDate = day + '-' + month + '-' + year;
	// 				   }
					   
	// 				   $('#vendor_doc_no').val(d.VendorDocNo || '');
	// 				   $('#vendor_quote_no').val(d.QuatationID || '');
	// 				   $('#vendor_quote_date').val(formattedDate);
	// 				   $('#freight_terms').val(d.FreightTerms || '');
	// 				   $('#broker').val(d.BrokerName || '');
	// 				   $('#payment_terms').val(d.PaymentTerms || '');
	// 				   $('#purchase_category').val(d.CategoryName || '');
	// 				   $('#item_type').val(d.ItemTypeName || '');

	// 			   } else {
	// 				   $('#vendor_doc_no').val('');
	// 				   $('#vendor_quote_no').val('');
	// 				   $('#vendor_quote_date').val('');
	// 				   $('#freight_terms').val('');
	// 				   $('#broker').val('');
	// 				   $('#payment_terms').val('');
	// 				   $('#purchase_category').val('');
	// 				   $('#item_type').val('');
	// 			   }
	// 		   },
	// 		   error: function() {
	// 			   // Handle error if needed
	// 		   }
	// 	   });
	// }

	
	
	function handlePurchaseOrderNoChange(poNo) {
    if (!poNo) return;
    $.ajax({
        url: '<?= admin_url('purchase/GetvandocDetails'); ?>',
        type: 'POST',
        data: { purchase_order_no: poNo },
        dataType: 'json',
        success: function(res) {
            if (res && res.success && Array.isArray(res.data) && res.data.length > 0) {
                var d = res.data[0];

                // qutotationdate → vendor_quote_date (yyyy-mm-dd format for input type="date")
                var quoteDate = '';
                if (d.qutotationdate) {
                    quoteDate = d.qutotationdate.split(' ')[0]; // "2026-02-19"
                }

                // TransDate → vendor_doc_date
                var transDate = '';
                if (d.TransDate) {
                    transDate = d.TransDate.split(' ')[0]; // "2026-02-19"
                }

                $('#vendor_doc_no').val(d.VendorDocNo || '');
                $('#vendor_quote_no').val(d.QuatationID || '');
                $('#vendor_quote_date').val(quoteDate);      // qutotationdate
                $('#vendor_doc_date').val(transDate);         // TransDate
                $('#freight_terms').val(d.FreightTerms || '');
                $('#broker').val(d.BrokerName || '');
                $('#payment_terms').val(d.PaymentTerms || '');
                $('#purchase_category').val(d.CategoryName || '');
                $('#item_type').val(d.ItemTypeName || '');
                $('#vendor_doc_amount').val(d.NetAmt || '');

            } else {
                $('#vendor_doc_no').val('');
                $('#vendor_quote_no').val('');
                $('#vendor_quote_date').val('');
                $('#vendor_doc_date').val('');
                $('#freight_terms').val('');
                $('#broker').val('');
                $('#payment_terms').val('');
                $('#purchase_category').val('');
                $('#item_type').val('');
                $('#vendor_doc_amount').val('');

            }
        },
        error: function() {
            // Handle error
        }
    });
}
	// Bind onchange for dropdown
	$(document).on('change', '#purchase_order_no', function() {
		var poNo = $(this).val();
		handlePurchaseOrderNoChange(poNo);
	});

	// Bind onchange for input box
	$(document).on('change', '#purchase_order_no_text', function() {
		var poNo = $(this).val();
		handlePurchaseOrderNoChange(poNo);
	});

	// Vendor Name change - fetch and populate vendor location and details
	window.getVendorDetailsLocation = function() {
		var vendorId = $('#vendor_id').val();
		if (!vendorId) {
			$('#vendor_location').val('').selectpicker('refresh');
			$('#business_unit').val('').selectpicker('refresh');
			$('#vendor_gst_no, #vendor_pan, #vendor_country, #vendor_state, #vendor_city, #vendor_pincode, #vendor_address, #vendor_payment_cycle_type, #vendor_payment_cycle').val('');
			return;
		}
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
				// Set both dropdowns with the same options
				$('#vendor_location').html(html);
				if ($.fn.selectpicker !== undefined) $('#vendor_location').selectpicker('refresh');
				$('#business_unit').html(html);
				if ($.fn.selectpicker !== undefined) $('#business_unit').selectpicker('refresh');
			},
			error: function () {}
		});

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
					$('#vendor_pincode').val(d.postal_code || '');
					$('#vendor_pan').val(d.pan || '');
					$('#vendor_city').val(d.city || '');
					$('#tds_code').val(d.TDS || '');
					
					calculateTotals();
				}
				loadShippingLocations(callback);
			},
			error: function () { loadShippingLocations(callback); }
		});


		$.ajax({
			url: '<?= admin_url('purchase/GetquotationDetails'); ?>',
			type: 'POST',
			data: { AccountID: vendorId },
			dataType: 'json',
			success: function (res) {
				var html = '<option value="" selected disabled>None selected</option>';
				if (res.status === 'success' && res.locations && res.locations.length > 0) {
					$.each(res.locations, function (i, loc) {
						html += '<option value="' + loc.PurchID + '">' + loc.PurchID + '</option>';
					});
					$('#purchase_order_no').html(html).closest('.form-group').find('.bootstrap-select').show();
					$('#purchase_order_no').show();
					if ($.fn.selectpicker !== undefined) $('#purchase_order_no').selectpicker('refresh');
					$('#purchase_order_no_text').hide();
				} else {
					// No data, show input box
					$('#purchase_order_no').hide();
					$('#purchase_order_no_text').show();
					// Hide bootstrap-select dropdown if present
					$('#purchase_order_no').closest('.form-group').find('.bootstrap-select').hide();
				}
				if (cb) cb();
			},
			error: function () { if (cb) cb(); }
		});
	};
	// Initialize selectpicker for all dropdowns
	$('.selectpicker').selectpicker();

	// Default today's date for empty date inputs on load
	var today = new Date().toISOString().slice(0,10);
	$('input[type="date"]').each(function(){
		if(!$(this).val()){
			$(this).val(today);
		}
	});

	// Add initial empty row on page load
	addInitialItemRow();
	
	// Function to add a new item row
	function addItemRow() {
		var newRow = `
		<tr>
			<td class="sn"></td>
			<td>
				<select class="form-control item-select selectpicker" name="item_name[]" data-width="100%" data-live-search="true" data-container="body" required>
					<option value="">-- Select Item --</option>
					<?php if(isset($items)) { foreach($items as $item) { ?>
						<option value="<?php echo $item["id"]; ?>" data-uom="<?php echo $item["uom"]; ?>" data-gst="<?php echo isset($item["gst_percent"]) ? $item["gst_percent"] : '0'; ?>">
							<?php echo $item["name"]; ?>
						</option>
					<?php }} ?>
				</select>
			</td>
			<td><input type="text" class="form-control po-original-qty" name="po_original_qty[]" readonly></td>
			<td><input type="text" class="form-control po-balance-qty" name="po_balance_qty[]" readonly></td>
			<td><input type="text" class="form-control uom" name="uom[]" readonly></td>
			<td><input type="number" step="0.01" class="form-control total-bag" name="total_bag[]"></td>
			<td><input type="number" step="0.01" class="form-control receipt-qty" name="receipt_qty[]"></td>
			<td><input type="number" step="0.01" class="form-control unit-price" name="unit_price[]"></td>
			<td><input type="text" class="form-control receipt-uom" name="receipt_uom[]"></td>
			<td>
				<select name="rebate_settlement[]" class="form-control">
					<option value="No">No</option>
					<option value="Yes">Yes</option>
				</select>
			</td>
			<td><input type="number" step="0.01" class="form-control rate" name="rate[]"></td>
			<td><input type="number" step="0.01" class="form-control rate-uom" name="rate_uom[]"></td>
			<td><input type="text" class="form-control calc-rate" name="calc_rate[]" readonly></td>
			<td><input type="text" class="form-control gst-percent" name="gst_percent[]" readonly></td>
			<td><input type="text" class="form-control amount" name="amount[]" readonly></td>
			<td class="action-cell"></td>
		</tr>`;
		
		$("#items_body").append(newRow);
		$("#items_body tr:last .item-select").selectpicker('refresh');
		bindRowEvents();
		updateActionButtons();
	}

	// Function to add initial item row
	function addInitialItemRow() {
		$('#items_body').empty();
		var initRow = `
		<tr>
			<td class="sn"></td>
			<td>
				<select class="form-control item-select selectpicker" name="item_name[]" data-width="100%" data-live-search="true" data-container="body" required>
					<option value="">-- Select Item --</option>
					<?php if(isset($items)) { foreach($items as $item) { ?>
						<option value="<?php echo $item["id"]; ?>" data-uom="<?php echo $item["uom"]; ?>" data-gst="<?php echo isset($item["gst_percent"]) ? $item["gst_percent"] : '0'; ?>">
							<?php echo $item["name"]; ?>
						</option>
					<?php }} ?>
				</select>
			</td>
			<td><input type="text" class="form-control po-original-qty" name="po_original_qty[]" readonly></td>
			<td><input type="text" class="form-control po-balance-qty" name="po_balance_qty[]" readonly></td>
			<td><input type="text" class="form-control uom" name="uom[]" readonly></td>
			<td><input type="number" step="0.01" class="form-control total-bag" name="total_bag[]"></td>
			<td><input type="number" step="0.01" class="form-control receipt-qty" name="receipt_qty[]"></td>
			<td><input type="number" step="0.01" class="form-control unit-price" name="unit_price[]"></td>
			<td><input type="text" class="form-control receipt-uom" name="receipt_uom[]"></td>
			<td>
				<select name="rebate_settlement[]" class="form-control">
					<option value="No">No</option>
					<option value="Yes">Yes</option>
				</select>
			</td>
			<td><input type="number" step="0.01" class="form-control rate" name="rate[]"></td>
			<td><input type="number" step="0.01" class="form-control rate-uom" name="rate_uom[]"></td>
			<td><input type="text" class="form-control calc-rate" name="calc_rate[]" readonly></td>
			<td><input type="text" class="form-control gst-percent" name="gst_percent[]" readonly></td>
			<td><input type="text" class="form-control amount" name="amount[]" readonly></td>
			<td class="action-cell"></td>
		</tr>`;
		
		$("#items_body").append(initRow);
		$("#items_body tr:last .item-select").selectpicker('refresh');
		bindRowEvents();
		updateActionButtons();
	}

	// Update action buttons: every row gets remove except last which gets add
	function updateActionButtons(){
		$('#items_body tr').each(function(i){
			var $cell = $(this).find('td.action-cell');
			$cell.empty();
			$(this).find('.sn').text(i+1);
			if($(this).is($('#items_body tr').last())){
				$cell.append('<a href="#" class="btn btn-success btn-sm add-item-btn"><i class="fa fa-plus"></i></a>');
			} else {
				$cell.append('<a href="#" class="btn btn-danger btn-sm remove-item-btn"><i class="fa fa-trash"></i></a>');
			}
		});
	}

	// Function to remove item row
	function removeItemRow(btn) {
		$(btn).closest('tr').remove();
	}

	// Bind events for row interactions
	function bindRowEvents() {
		// Item selection change - populate UOM and GST
		$(document).off('change', '.item-select').on('change', '.item-select', function(){
			var row = $(this).closest('tr');
			var $this = $(this);
			var selectedOption = $this.find('option:selected');
			var uom = selectedOption.data('uom') || '';
			var gst = selectedOption.data('gst') || '0';
			
			row.find('.uom').val(uom);
			row.find('.gst-percent').val(gst);
			
			var itemId = $(this).val();
			var poId = $('#purchase_order_no').val();
	
		});

		// Calculate amount on input change
		$(document).off('keyup change', '.receipt-qty, .unit-price, .rate-uom').on('keyup change', '.receipt-qty, .unit-price, .rate-uom', function(){
			calculateAmount($(this).closest('tr'));
		});
	}

	// Calculate Amount based on inputs
	function calculateAmount(row) {
		var qty = parseFloat(row.find('.receipt-qty').val()) || 0;
		var price = parseFloat(row.find('.unit-price').val()) || 0;
		var rateUom = parseFloat(row.find('.rate-uom').val()) || 0;
		var gst = parseFloat(row.find('.gst-percent').val()) || 0;
		
		// Calculate Rate: Receipt Qty * Unit Price
		var rate = qty * price;
		row.find('.rate').val(rate.toFixed(2));
		
		// Calculate Calc Rate: Rate * Rate UOM
		var calcRate = rate * rateUom;
		row.find('.calc-rate').val(calcRate.toFixed(2));
		
		// Calculate Amount: Calc Rate + GST
		var gstAmount = (calcRate * gst) / 100;
		var amount = calcRate + gstAmount;
		row.find('.amount').val(amount.toFixed(2));
	}

	// Add Item Row using old click handler for backwards compatibility
	$(document).on('click', '#add-item-row', function(){
		addItemRow();
	});

	// Add Item Row using new button ID
	$(document).on('click', '#add-item-row-btn', function(){
		addItemRow();
	});

	// Delegated handler for dynamically-added add buttons
	$(document).on('click', '.add-item-btn', function(e){
		e.preventDefault();
		addItemRow();
	});

	// Delegated handler for remove buttons
	$(document).on('click', '.remove-item-btn', function(e){
		e.preventDefault();
		var btn = this;
		removeItemRow(btn);
		if($('#items_body tr').length === 0){
			addInitialItemRow();
		} else {
			updateActionButtons();
		}
	});

	// Vendor Change - Load Vendor Details
	$('#vendor').on('change', function(){
		var vendorId = $(this).val();
		
		if(!vendorId) {
			// Clear vendor details
			$('#vendor_gst_no').val('');
			$('#vendor_pan').val('');
			$('#vendor_country').val('');
			$('#vendor_state').val('');
			$('#vendor_city').val('');
			$('#vendor_pincode').val('');
			$('#vendor_address').val('');
			$('#vendor_payment_cycle_type').val('');
			$('#vendor_payment_cycle').val('');
			return;
		}
		
		// Load vendor details via AJAX
		$.ajax({
			url: '<?php echo admin_url('purchase/GetVendorDetails'); ?>',
			type: 'POST',
			data: { vendor_id: vendorId },
			dataType: 'json',
			success: function(data) {
				if(data) {
					$('#vendor_gst_no').val(data.gst_no || '');
					$('#vendor_pan').val(data.pan || '');
					$('#vendor_country').val(data.country || '');
					$('#vendor_state').val(data.state || '');
					$('#vendor_city').val(data.city || '');
					$('#vendor_pincode').val(data.pincode || '');
					$('#vendor_address').val(data.address || '');
					$('#vendor_payment_cycle_type').val(data.payment_cycle_type || '');
					$('#vendor_payment_cycle').val(data.payment_cycle || '');
				}
			}
		});
	});

	// Lorry Number validation
	$('#lorry_no').on('change', function(){
		var v = $(this).val();
		var re = /^[A-Za-z0-9\-\s]+$/;
		if(v && !re.test(v)){
			alert('Invalid vehicle number');
			$(this).focus();
		}
	});

	// Form Submission
	$('#pur_invoice-form').on('submit', function(e){
		
		// Validate at least one item row
		if($('#items_body tr').length === 0) {
			alert('Please add at least one item to the purchase invoice');
			return false;
		}

		// Collect item data
		var itemsData = [];
		$('#items_body tr').each(function(){
			var row = $(this);
			var itemName = row.find('.item-select').val();
			
			if(itemName) {
				itemsData.push({
					item_id: itemName,
					po_original_qty: row.find('.po-original-qty').val(),
					po_balance_qty: row.find('.po-balance-qty').val(),
					uom: row.find('.uom').val(),
					total_bag: row.find('.total-bag').val(),
					receipt_qty: row.find('.receipt-qty').val(),
					unit_price: row.find('.unit-price').val(),
					receipt_uom: row.find('.receipt-uom').val(),
					rebate_settlement: row.find('select[name="rebate_settlement[]"]').val(),
					rate: row.find('.rate').val()
				});
			}
		});

		// Add items data to form
		$('<input>').attr({
			type: 'hidden',
			name: 'items_json',
			value: JSON.stringify(itemsData)
		}).appendTo('#pur_invoice-form');

		// Submit form
		return true;
	});

	// Initialize on page load
	bindRowEvents();

	// Form reset handler - reinitialize form controls
	$('#pur_invoice-form').on('reset', function() {
		setTimeout(function(){
			// reset selects
			$('.selectpicker').each(function(){
				$(this).val('');
				$(this).selectpicker('refresh');
			});

			// reset text inputs and textareas
			$('#pur_invoice-form').find('input[type="text"], input[type="number"], textarea').val('');

			// reset file input
			$('#attachment').val('');

			// reset vendor detail readonly fields
			$('#vendor_gst_no, #vendor_pan, #vendor_country, #vendor_state, #vendor_city, #vendor_pincode, #vendor_address, #vendor_payment_cycle_type, #vendor_payment_cycle').val('');

			// reset date fields to today
			var today = new Date().toISOString().slice(0,10);
			$('#posting_date').val(today);
			$('#vendor_quote_date').val('');
			$('#arrival_date').val('');

			// clear item rows and re-add initial
			addInitialItemRow();

			// refresh any selectpickers again
			$('.selectpicker').selectpicker('refresh');
		}, 10);
	});
});
</script>

<style>
	.nav-tabs > li > a {
		padding: 10px 15px;
		border-radius: 4px 4px 0 0;
	}
	
	.nav-tabs > li.active > a {
		background-color: #f5f5f5;
		border: 1px solid #ddd;
		border-bottom-color: transparent;
	}

	.form-group label {
		font-weight: 500;
		margin-bottom: 8px;
	}

	.table-responsive {
		border: 1px solid #ddd;
		border-radius: 4px;
	}

	#items_table thead {
		background-color: #50607b;
	}

	#items_table td input,
	#items_table td select {
		width: 100%;
		border: 1px solid #ddd;
		padding: 6px;
		border-radius: 3px;
	}

	#items_table td input[readonly],
	#items_table td select[readonly] {
		background-color: #e9ecef;
		cursor: not-allowed;
	}

	.req.text-danger {
		color: #dc3545;
		font-weight: bold;
	}

	.btn-bottom-toolbar {
		padding: 15px 0;
		border-top: 1px solid #ddd;
		margin-top: 20px;
	}

	.btn-bottom-toolbar .btn {
		margin-left: 5px;
	}
</style>

</body>


</html>
