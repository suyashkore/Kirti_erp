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
	@media screen and (max-width: 600px) {
		#header ul {
			display: none !important;
		}
	}
</style>

<!-- =============================================
	 LIST MODAL
============================================= -->
<div class="modal fade" id="ListModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Purchase Invoice List</h5>
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
						<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search" class="form-control">
					</div>
				</div>

				<!-- MAIN ORDER TABLE -->
				<div class="table-list">
					<table class="table table-bordered" id="table_ListModal">
						<thead>
							<tr>
								<th class="sortable">InvoiceID</th>
								<th class="sortable">Invoice Date</th>
								<th class="sortable">Vehicle No</th>
								<th class="sortable">GateINID</th>
								<th class="sortable">GateIn Date</th>
								<th class="sortable">VendorName</th>
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
			<?php
				//echo form_open($this->uri->uri_string(),array('id'=>'pur_invoice-form','class'=>'_transaction_form','enctype'=>'multipart/form-data'));
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
						<form action="<?= admin_url('purchase/Invoice/UpdateInvoice');?>" method="post" id="main_save_form" enctype="multipart/form-data">
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
							<input type="hidden" name="form_mode" id="form_mode" value="add">
							<input type="hidden" name="update_id" id="update_id" value="">
                        <?php
				            //echo form_open($this->uri->uri_string(),array('id'=>'pur_inv-form','class'=>'_transaction_form'));
			            ?>
							<!-- GENERAL DETAILS SECTION -->
							<div class="row">
									<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group" app-field-wrapper="VendorID">
										<label for="VendorID" class="control-label"><small class="req text-danger">*
											</small> Vendor Name</label>
										<select name="VendorID" id="VendorID" class="form-control selectpicker"
											data-live-search="true" app-field-label="Vendor Name" required>
											<option value="">None selected</option>
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
										<label><small class="req text-danger">*</small>InwardID/Vehicle No</label>
										<select name="InwardID" id="InwardID" class="form-control selectpicker" data-live-search="true" required>
											<option value="">None selected</option>
											<?php if(isset($purchase_orders)) { foreach($purchase_orders as $po) { ?>
												<option value="<?php echo $po['id']; ?>"><?php echo $po['po_no']; ?></option>
											<?php }} ?>
										</select>
									</div>
								</div>
								
								<?php
								    $selected_company = $this->session->userdata('root_company');
								    $FY = $this->session->userdata('finacial_year');
								?>
								<div class="col-md-2 col-sm-6 col-xs-12">
            					    <div class="form-group">
                                        <label for="InvoiceID"><small class="req text-danger">*</small>InvoiceID</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">IN<span id="prefix_year"><?php echo $FY.$selected_company;?></span></span>
                                            <input type="text" name="InvoiceID" id="InvoiceID" class="form-control" value="">
                                            <input type="hidden" name="InvoiceID_Hidden" id="InvoiceID_Hidden" value="">
                                        </div>
                                    </div>
                                </div>
								
								<!--<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Invoice Date</label>
										<input type="date" name="posting_date" id="posting_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
									</div>
								</div>-->
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group" app-field-wrapper="InvoiceDate">
										<?= render_date_input('InvoiceDate', 'Invoice Date', date('d/m/Y'), []); ?>
									</div>
								</div>

								<!--<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Arrival Date</label>
										<input type="date" name="arrival_date" id="arrival_date" class="form-control">
									</div>
								</div>-->

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group" app-field-wrapper="item_type">
										<label for="item_type" class="control-label"><small class="req text-danger">*</small> Item / Service</label>
										<input type="text" readonly name="item_type" id="item_type" class="form-control" app-field-label="Item / Service" placeholder="Enter Item / Service" autocomplete="off">

									</div>
								</div>
								
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Purchase Category</label>
										<input type="text" name="purchase_category" id="purchase_category" class="form-control" readonly>
									</div>
								</div>
								<div class="clearifx"></div>


							
                                <div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Purchase Location</label>
										<input type="text" readonly name="PurchLocation" id="PurchLocation" class="form-control">
									</div>
								</div>
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Vendor Location</label>
										<input type="text" readonly name="VendorLocation" id="VendorLocation" class="form-control">
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
										<label>Vendor State</label>
										<input type="text" name="vendor_state" id="vendor_state" class="form-control" readonly>
										<input type="hidden" name="Company_state" id="Company_state" value="<?php echo $RootCompanyDetails->state_code;?>">
									</div>
								</div>
								
								
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Lorry No</label>
										<input type="text" name="lorry_no" id="lorry_no" readonly class="form-control" placeholder="Vehicle number">
									</div>
								</div>
								
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Hold Payment</label>
										<select name="hold_payment" id="hold_payment" class="form-control" required>
											<option value="N">No</option>
											<option value="Y">Yes</option>
										</select>
									</div>
								</div>
								
                                <div class="clearfix"></div>
                                
                                <div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>GateIN No</label>
										<input type="text" name="gate_entry_no" id="gate_entry_no" class="form-control" readonly>
									</div>
								</div>
								
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group" app-field-wrapper="gate_entry_date">
										<?= render_date_input('gate_entry_date', 'GateIN Date', date('d/m/Y'), []); ?>
									</div>
								</div>
                                <div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Doc No</label>
										<input type="text" name="vendor_doc_no" id="vendor_doc_no" class="form-control">
									</div>
								</div>
								
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group" app-field-wrapper="VendorDocDate">
										<?= render_date_input('VendorDocDate', 'Vendor Doc Date', date('d/m/Y'), []); ?>
									</div>
								</div>
								

								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Doc Amt</label>
										<input type="text"  name="vendor_doc_amount" id="vendor_doc_amount" class="form-control">
									</div>
								</div>
                                <div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>Vendor Disp. Wt(kg)</label>
										<input type="text"  name="vendor_dispatch_weight" id="vendor_dispatch_weight" class="form-control" placeholder="Weight in kg">
									</div>
								</div>
								
								<div class="clearfix"></div>
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Broker</label>
										<input type="text" name="BrokerID" id="BrokerID" class="form-control" placeholder="Broker name">
									</div>
								</div>
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>TDS Section</label>
										<input type="text" name="tds_code" id="tds_code" class="form-control" readonly>
									</div>
								</div>
								
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label>TDS Rate(%)</label>
										<input type="text" name="tds_rate" id="tds_rate" class="form-control" readonly>
									</div>
								</div>
								
								<div class="col-md-2 col-sm-6 col-xs-12">
									<div class="form-group">
										<label><small class="req text-danger">*</small>Freight Terms</label>
										<select name="frt_terms" id="frt_terms" class="form-control selectpicker"
											data-live-search="true" app-field-label="Freight Terms" >
											<option value="" selected>None selected</option>
										    <?php foreach($FreightTerms as $key=>$val){
										    ?>
										    <option value="<?php echo $val["Id"];?>" ><?php echo $val["FreightTerms"];?></option>
										    <?php
										    }?>
										</select>
									</div>
								</div>
                                <div class="clearfix"></div>
								

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
													<th width="5%">Sr.No.</th>
													<th width="35%">Item Name</th>
													<th width="7%">UOM</th>
													<th width="7%">HSN</th>
													<th width="7%">Unit Wt(kg)</th>
													<th width="7%">Unit Price</th>
													<th width="8%">Inward Qty</th>
													<th width="7%">Unit DiscAmt</th>
													<th width="7%">GST %</th>
													<th width="10%">Amt</th>
												</tr>
											</thead>
											<tbody id="items_body">
												<!-- Rows will be added here dynamically -->
											</tbody>
										</table>
									</div>
								</div>
							</div>
							
							<!-- OTHER INFORMATION SECTION -->
							<div class="row">
								<div class="col-md-6">
									<h4 class="bold p_style">Other Information</h4>
									<hr class="hr_style">
									<div class="row">
									    <div class="col-md-6 col-sm-12 col-xs-12">
        									<div class="form-group">
        										<label>Internal Remarks</label>
        										<textarea name="internal_remarks" id="internal_remarks" class="form-control" rows="3" placeholder="Enter internal remarks..."></textarea>
        									</div>
        								</div>
        
        								<div class="col-md-6 col-sm-12 col-xs-12">
        									<div class="form-group">
        										<label>Document Remark</label>
        										<textarea name="document_remark" id="document_remark" class="form-control" rows="3" placeholder="Enter document remarks..."></textarea>
        									</div>
        								</div>
        								
        								<div class="col-md-6 col-sm-12 col-xs-12">
        									<div class="form-group">
        										<label>Attachment</label>
        										<input type="file" name="attachment" id="attachment" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
        										<small style="display:block;color:#666;margin-top:4px;">formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</small>
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
                      <br>
							<!-- ACTION BUTTONS -->
							<div class="row">
								<div class="col-md-12" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;">
									<a href="#" class="btn btn-primary updateBtn" id="print_pdf" style="display: none;" target="_blank"><i class="fa fa-print"></i> Print PDF</a>
									<button type="button" class="btn btn-success saveBtn <?= (has_permission_new('purchase-invoice', '', 'create')) ? '' : 'disabled'; ?>" id="SaveBtn"><i class="fa fa-save"></i> Save</button>
									<button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('purchase-invoice', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
									<button type="button" class="btn btn-danger" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
									<button type="button" class="btn btn-info" onclick="showPurchaseOrderList();"><i class="fa fa-list"></i> Show List</button>
								</div>
							</div>
						</form>
						<?php //echo form_close(); ?>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

<?php init_tail(); ?>

<script>
  function ResetForm(){
		$('#main_save_form')[0].reset();
		$('#form_mode').val('add');
		$('#update_id').val('');
		$('.updateBtn').hide();
		$('.saveBtn').show();
    $('#items_body').html('');
    $('.total-display').text('0.00');
    $('#VendorID').html(`
      <option value="" selected>None selected</option>
      <?php
      if (!empty($vendor_list)):
				foreach ($vendor_list as $value):
					echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' (' . $value['AccountID'] . ')</option>';
				endforeach;
			endif;
			?>
    `);
		$('.selectpicker').selectpicker('refresh');
	}

    // =============================================
	// CLICK HANDLER FOR LIST ROWS
	// =============================================
	$(document).on('click', '#table_ListModal .get_Details', function () {
		var InvoiceID = $(this).data('id');
		if (InvoiceID) {
			getDetails(InvoiceID);
			$('#ListModal').modal('hide');
		}
	});

	function getDetails(InvoiceID) {
		ResetForm();
		$.ajax({
			url: "<?= admin_url(); ?>purchase/Invoice/GetPurchaseInvoiceDetails",
			method: "POST",
			dataType: "JSON",
			data: { InvoiceID: InvoiceID },
			success: function (response) {
				//if (response.success == true) {
				$('.saveBtn').hide();
				$('.updateBtn').show();
				$('#form_mode').val('edit');
				$('#print_pdf').attr('href', '<?= admin_url('purchase/Invoice/PrintPDF/'); ?>'+response.InvoiceID);
				$('#update_id').val(response.id);
				$('#VendorID').empty();
				$('#VendorID').selectpicker('refresh');
				$('#VendorID').append('<option value="'+response.AccountID+'" disabled>'+response.VendorName+'</option>');
				$('#VendorID').val(response.AccountID);
				$('#VendorID').selectpicker('refresh');
				
				$('#InwardID').empty();
				$('#InwardID').selectpicker('refresh');
				var lebel = response.InwardID+"("+response.VehicleNo+")";
				$('#InwardID').append('<option value="'+response.InwardID+'" disabled>'+lebel+'</option>');
				$('#InwardID').val(response.InwardID);
				$('#InwardID').selectpicker('refresh');
				
				let dateStr1 = response.TransDate;
				let parts1 = dateStr1.split(" ")[0].split("-");
				let formattedDate1 = parts1[2] + "/" + parts1[1] + "/" + parts1[0];
				$('#InvoiceDate').val(formattedDate1);
				$('#item_type').val(response.ItemTypeName);
				$('#purchase_category').val(response.CategoryName);
				$('#PurchLocation').val(response.LocationName);
				$('#VendorLocation').val(response.city_name);
				$('#vendor_gst_no').val(response.GSTIN);
				$('#vendor_state').val(response.billing_state);
				$('#lorry_no').val(response.VehicleNo);
				$('#hold_payment').val(response.HoldPayment);
				$('#hold_payment').selectpicker('refresh');
				$('#gate_entry_no').val(response.GateINID);
				let dateStr = response.GateInDate;
				let parts = dateStr.split(" ")[0].split("-");
				let formattedDate = parts[2] + "/" + parts[1] + "/" + parts[0];
				$('#gate_entry_date').val(formattedDate);
				$('#vendor_doc_no').val(response.VendorDocNo);
				if(response.VendorDocDate){
						let dateStr2 = response.VendorDocDate;
						let parts2 = dateStr2.split(" ")[0].split("-");
						let formattedDate2 = parts2[2] + "/" + parts2[1] + "/" + parts2[0];
						$('#VendorDocDate').val(formattedDate2);
				}
				
				$('#vendor_doc_amount').val(response.VendorDocAmt);
				$('#vendor_dispatch_weight').val(response.VendorDocWeight);
				$('#BrokerID').val(response.BrokerID);
				$('#tds_code').val(response.TDSSection);
				$('#tds_rate').val(response.TDSPercentage);
				$('#frt_terms').val(response.FreightTerms);
				$('#frt_terms').selectpicker('refresh');
				$('#InvoiceID').val(response.ShortInvoiceID);
				$('#InvoiceID_Hidden').val(response.InvoiceID);
				$('#internal_remarks').val(response.Internal_Remarks);
				$('#document_remark').val(response.Document_Remark);
				let ItemDetails = response.ItemList;
				var html = '';
				var srno = 1;
				let TotalWt = 0;let Totalqty = 0;let TotalItemAmt = 0;
				let TotalDiscAmt = 0;let TotalTaxableAmt = 0;let TotalCGSTAmt = 0;let TotalSGSTAmt = 0;let TotalIGSTAmt = 0;let TotalNetAmt = 0
				ItemDetails.forEach(Items => {
					let Wt = parseFloat(Items.UnitWeight) * parseFloat(Items.OrderQty);
					TotalWt += parseFloat(Wt);
					Totalqty += parseFloat(Items.OrderQty);
					
					let ItemAmt = parseFloat(Items.BasicRate) * parseFloat(Items.OrderQty);
					TotalItemAmt += ItemAmt;
					
					let DiscAmt = parseFloat(Items.DiscAmt) * parseFloat(Items.OrderQty);
					TotalDiscAmt += DiscAmt;
					let TaxableAmt = parseFloat(ItemAmt) - parseFloat(DiscAmt);
					TotalTaxableAmt += TaxableAmt;
					let GSTPer = Items.TotalGST;
					let GSTAmt = parseFloat(TaxableAmt) * (GSTPer/100);
					TotalCGSTAmt += GSTAmt/2;
					TotalSGSTAmt += GSTAmt/2;
					TotalNetAmt += (TaxableAmt + GSTAmt);
					
					html += '<tr>';
					html += '<td>'+srno+'</td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.ItemName+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.SuppliedIn+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.hsn_code+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.UnitWeight+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.BasicRate+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.OrderQty+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.DiscAmt+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.TotalGST+'"></td>';
					html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.NetOrderAmt+'"></td>';
					html += '<tr>';
					srno++;
				});
				$("#items_body").html(html);
				$("#total_weight_display").html(TotalWt);
				$("#total_qty_display").html(Totalqty);
				$("#item_total_amt_display").html(TotalItemAmt);
				$("#disc_amt_display").html(TotalDiscAmt);
				$("#taxable_amt_display").html(TotalTaxableAmt);
				$("#cgst_amt_display").html(TotalCGSTAmt);
				$("#sgst_amt_display").html(TotalSGSTAmt);
				$("#igst_amt_display").html(TotalIGSTAmt);
				$("#round_off_amt_display").html(0.00);
				$("#net_amt_display").html(TotalNetAmt);
			}
		})
	}
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

		
		$('#table_ListModal tbody').html(
			'<tr><td colspan="12" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>'
		);

		$.ajax({
			url: '<?= admin_url('purchase/invoice/GetInvoiceList'); ?>',
			type: 'POST',
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
						html += `<tr class="get_Details order-main-row" data-id="${row.InvoiceID}" style="cursor:pointer;">
						<td>${row.InvoiceID || ''}</td>
						<td>${row.TransDate ? moment(row.TransDate).format('DD/MM/YYYY') : ''}</td>
						<td>${row.VehicleNo || ''}</td>
						<td>${row.GateINID || ''}</td>
						<td>${row.GateInDate ? moment(row.GateInDate).format('DD/MM/YYYY') : ''}</td>
						<td>${row.VendorName || ''}</td>
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

	$("#SaveBtn").on('click', function() {
		var VendorID = $("#VendorID").val();
		var InwardID = $("#InwardID").val();
		var InvoiceDate = $("#InvoiceDate").val();
		var vendor_doc_no = $("#vendor_doc_no").val();
		var VendorDocDate = $("#VendorDocDate").val();
		var vendor_doc_amount = $("#vendor_doc_amount").val();
		var vendor_dispatch_weight = $("#vendor_dispatch_weight").val();
		var internal_remarks = $("#internal_remarks").val();
		var document_remark = $("#document_remark").val();
		var vendor_state = $("#vendor_state").val();
		var Company_state = $("#Company_state").val();
		var hold_payment = $("#hold_payment").val();
		$.ajax({
			url: '<?= admin_url('purchase/Invoice/AddNewPurchInvoice'); ?>',
			type: 'POST',
			data: { VendorID: VendorID,InwardID:InwardID,InvoiceDate:InvoiceDate,vendor_doc_no:vendor_doc_no,VendorDocDate:VendorDocDate,
							vendor_state:vendor_state,Company_state:Company_state,hold_payment:hold_payment,
							vendor_doc_amount:vendor_doc_amount,vendor_dispatch_weight:vendor_dispatch_weight,internal_remarks:internal_remarks,document_remark:document_remark},
			dataType: 'json',
			success: function(response) {
				if (response.status == true) {
					alert_float('success', response.message);
					location.reload(true);
				} else {
					alert_float('warning', response.message);
				}
			},
			error: function() {
					// Handle error
			}
		});      
	});

	$('#main_save_form').on('submit', function(e) {
		e.preventDefault();

		let form_mode = $('#form_mode').val();
		
		// let required_fields = get_required_fields('main_save_form');
		// let validated = validate_fields(required_fields);

		// if(validated === false){
		// 	return;
		// }
		
		var form_data = new FormData(this);
		form_data.append(
			'<?= $this->security->get_csrf_token_name(); ?>',
			$('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		);
		if(form_mode == 'edit'){
			form_data.append('update_id', $('#update_id').val());
		}

		$.ajax({
			url:"<?= admin_url(); ?>purchase/Invoice/UpdateInvoice",
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
					// let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getDetails(${response.data.id})">
					// 	<td>${response.data.InwardsID}</td>
          //   <td>${response.data.OrderID}</td>
          //   <td>${moment(response.data.TransDate).format('DD/MM/YYYY')}</td>
          //   <td>${response.data.company}</td>
          //   <td>${response.data.TotalWeight}</td>
          //   <td>${response.data.NetAmt}</td>
					// </tr>`;
					// if(form_mode == 'edit'){
					// 	$('.get_Details[data-id="'+response.data.id+'"]').replaceWith(html);
					// }else{
					// 	$('#table_ListModal tbody').prepend(html);
					// }
					ResetForm();
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	});

  $("#VendorID").on('change', function() {
		var VendorID = $(this).val();
		$.ajax({
            url: '<?= admin_url('purchase/Invoice/GetPendingInwardList'); ?>',
            type: 'POST',
            data: { VendorID: VendorID },
            dataType: 'json',
            success: function(res) {
                var html = '<option value="">None selected</option>';
				if (res.status == true && res.InwardList && res.InwardList.length > 0) {
					$.each(res.InwardList, function (i, loc) {
					    var lebel = loc.InwardsID+"("+loc.VehicleNo+")";
						html += '<option value="' + loc.InwardsID + '">' + lebel + '</option>';
					});
				}
				// Set both dropdowns with the same options
				$('#InwardID').html(html);
				$('#InwardID').selectpicker('refresh');
				let VendorDetails = res.VendorDetails;
				$('#vendor_gst_no').val(VendorDetails.GSTIN);
				$('#vendor_state').val(VendorDetails.billing_state);
				$('#tds_code').val(VendorDetails.TDSSection);
				$('#tds_rate').val(VendorDetails.TDSPer);
				$('#frt_terms').val(VendorDetails.FreightTerms);
				$('#frt_terms').selectpicker('refresh');
            },
            error: function() {
                // Handle error
            }
        });
	});
	
	$("#InwardID").on('change', function() {
		var InwardID = $(this).val();
		$.ajax({
            url: '<?= admin_url('purchase/Invoice/GetInwardDetails'); ?>',
            type: 'POST',
            data: { InwardID: InwardID },
            dataType: 'json',
            success: function(res) {
                let InwardDetail = res.InwardDetails;
                $('#InvoiceID').val(InwardDetail.NextInvoiceID);
                $('#PurchLocation').val(InwardDetail.LocationName);
                $('#item_type').val(InwardDetail.ItemTypeName);
                $('#purchase_category').val(InwardDetail.CategoryName);
                $('#lorry_no').val(InwardDetail.VehicleNo);
                $('#VendorLocation').val(InwardDetail.city_name);
                $('#BrokerID').val(InwardDetail.BrokerName);
                let dateStr = InwardDetail.GateINDate;

                let parts = dateStr.split(" ")[0].split("-");
                let formattedDate = parts[2] + "/" + parts[1] + "/" + parts[0];
                $('#gate_entry_date').val(formattedDate);
                $('#gate_entry_no').val(InwardDetail.GateINID);
                let ItemDetails = InwardDetail.ItemList;
                var html = '';
                var srno = 1;
                let TotalWt = 0;let Totalqty = 0;let TotalItemAmt = 0;
                let TotalDiscAmt = 0;let TotalTaxableAmt = 0;let TotalCGSTAmt = 0;let TotalSGSTAmt = 0;let TotalIGSTAmt = 0;let TotalNetAmt = 0
                ItemDetails.forEach(Items => {
                    let Wt = parseFloat(Items.UnitWeight) * parseFloat(Items.OrderQty);
                    TotalWt += parseFloat(Wt);
                    Totalqty += parseFloat(Items.OrderQty);
                    
                    let ItemAmt = parseFloat(Items.BasicRate) * parseFloat(Items.OrderQty);
                    TotalItemAmt += ItemAmt;
                    
                    let DiscAmt = parseFloat(Items.DiscAmt) * parseFloat(Items.OrderQty);
                    TotalDiscAmt += DiscAmt;
                    let TaxableAmt = parseFloat(ItemAmt) - parseFloat(DiscAmt);
                    TotalTaxableAmt += TaxableAmt;
                    let GSTPer = Items.TotalGST;
                    let GSTAmt = parseFloat(TaxableAmt) * (GSTPer/100);
                    TotalCGSTAmt += GSTAmt/2;
                    TotalSGSTAmt += GSTAmt/2;
                    TotalNetAmt += (TaxableAmt + GSTAmt);
                    
                    html += '<tr>';
                    html += '<td>'+srno+'</td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.ItemName+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.SuppliedIn+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.hsn_code+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.UnitWeight+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.BasicRate+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.OrderQty+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.DiscAmt+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.TotalGST+'"></td>';
                    html += '<td><input readonly type="text" class="form-control name="ItemID" id="ItemID" value="'+Items.NetOrderAmt+'"></td>';
                    html += '<tr>';
                    srno++;
                });
                $("#items_body").html(html);
                $("#total_weight_display").html(TotalWt);
                $("#total_qty_display").html(Totalqty);
                $("#item_total_amt_display").html(TotalItemAmt);
                $("#disc_amt_display").html(TotalDiscAmt);
                $("#taxable_amt_display").html(TotalTaxableAmt);
                $("#cgst_amt_display").html(TotalCGSTAmt);
                $("#sgst_amt_display").html(TotalSGSTAmt);
                $("#igst_amt_display").html(TotalIGSTAmt);
                $("#round_off_amt_display").html(0.00);
                $("#net_amt_display").html(TotalNetAmt);
            },
            error: function() {
                // Handle error
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
