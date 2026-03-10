<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
				echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
			?>
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="row">
						<div class="col-md-12">
							<div class="panel-body">
							    <nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="https://goodmorning.globalinfocloud.in/admin/"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Purchase Order</b></li>
									</ol>
								</nav>
                                <hr class="hr_style">
								<br>
								<?php
									$customer_custom_fields = false;
									if(total_rows(db_prefix().'customfields',array('fieldto'=>'pur_order','active'=>1)) > 0 ){
										$customer_custom_fields = true;
									}
								?>
								<div class="tab-content">
									<?php if($customer_custom_fields) { ?>
										<div role="tabpanel" class="tab-pane" id="custom_fields">
											<?php $rel_id=( isset($pur_order) ? $pur_order->id : false); ?>
											<?php echo render_custom_fields( 'pur_order',$rel_id); ?>
										</div>
									<?php } ?>
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
										    <div class="col-md-9">
										        <div class="row">
										            <div class="col-md-3">
														<div class="form-group ">
															<?php $prefix = get_purchase_option('pur_order_prefix');
																$next_number = get_purchase_option('next_po_number');
																$pur_order_number = (isset($pur_order) ? $pur_order->pur_order_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('M-Y'));
																
																$number = (isset($pur_order) ? $pur_order->number : $next_number);
															    echo form_hidden('number',$number); ?> 
															<label for="pur_order_number">PO No.</label>
															<input type="text" readonly="" class="form-control" name="pur_order_number" value="<?php echo html_entity_decode($purchase_details->PurchID); ?>">
														</div>
													</div>
													<div class="col-md-3">
														<input type="hidden" name="trans_date" id="trans_date" value="<?php echo $purchase_details->Transdate;?>">
														<?php $value = (isset($purchase_details) ? _d(substr($purchase_details->Transdate,0,10)) : '');?>
														<?php echo render_date_input('prd_date','PO Date',$value); ?>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="estimate">GST</label>
															<input type="text" readonly="" class="form-control" name="gst_num" id="gst_num" value="<?= $purchase_details->vat?>"  aria-invalid="false">
														</div>
													</div>
													<div class="col-md-3"> 
														<div class="form-group"> 
															<label for="IsTDS">TDS Applicable ?</label>
															<input type="text" readonly="" class="form-control" name="IsTDS" id="IsTDS"  aria-invalid="false">
														</div>
													</div>
													<div class="clearfix"></div>
													<div class="col-md-4">
														<input type="hidden" name="vendor_code" id="vendor_code" value="<?php echo $purchase_details->Vendor; ?>">
														<div class="form-group">
															<label for="vendor_code"><small class="req text-danger">*</small><?php echo _l('vendor'); ?></label>
															<!--<input type="text" readonly="" class="form-control" name="vendor" id="vendor" value="<?php echo $purchase_details->Vendor; ?>" >-->
															<select name="vendor"  id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
																<option value=""></option>
																<?php foreach($vendors as $s) { ?>
																	<option value="<?php echo html_entity_decode($s['AccountID']); ?>"  <?php if($purchase_details->Vendor == $s['AccountID']){ echo 'selected'; }?>><?php echo html_entity_decode($s['company'])." - ".html_entity_decode($s['AccountID']); ?></option>
																<?php } ?>
															</select>             
														</div>
													</div>
													<div class="col-md-2" >
														<input type="hidden" value="<?= $purchase_details->items?>" class="form-control" name="item_associated" id="item_associated"  aria-invalid="false">
														<div class="form-group">
															<label for="state_f">State Name</label>
															<input type="text" readonly="" class="form-control" name="state_f" id="state_f" value="<?= $purchase_details->state; ?>" aria-invalid="false">
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="estimate">City</label>
															<input type="text" readonly="" class="form-control" name="city" id="city"  value="<?= $purchase_details->city_name?>" aria-invalid="false">
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="ContactPersonName"><small class="req text-danger">*</small>Contact Person Name</label>
															<input type="text" class="form-control" name="ContactPersonName" id="ContactPersonName"  aria-invalid="false" value="<?= $purchase_details->ContactPersonName ?>" required>
														</div>
													</div>
													<div class="clearfix"></div>
													<div class="col-md-2">
														<div class="form-group">
															<label for="ContactMobileNo"><small class="req text-danger">*</small>Contact Number</label>
															<input type="text" class="form-control" name="ContactMobileNo" id="ContactMobileNo" 
																pattern="\d{10}" maxlength="10" minlength="10"
																title="Please enter a 10-digit phone number" value="<?= $purchase_details->ContactMobileNo ?>"  required>
														</div>
													</div>
													
													<div class="col-md-2">
														<div class="form-group" style="margin-left:10px;">
															<label for="status">Status</label>
															<select class="form-control" class="selectpicker form-control" name="status" id="status">
																<?php
																	if(isset($purchase_details))
																	{
																		if($purchase_details->cur_status == 'Pending')
																		{
																		?>
																		<option value="Pending">Pending</option>
																		<?php
																			if (has_permission_new('purchase-order-po-approve', '', 'edit'))
																			{
																			?>
																			<option value="Approved">Approve</option>
																			<?php
																			}
																		?>
																		<option value="Cancel">Cancel</option>
																		<?php
																		}
																		if($purchase_details->cur_status == 'Cancel')
																		{
																		?>
																		<option value="Cancel">Cancel</option>
																		<?php
																		}
																		
																		if($purchase_details->cur_status == 'Approved')
																		{
																		?>
																		<option value="Approved">Approved</option>
																		<option value="InProgress">InProgress</option>
																		<option value="Completed">Complete</option>
																		<?php
																		}
																		if($purchase_details->cur_status == 'InProgress')
																		{
																		?>
																		<option value="InProgress">InProgress</option>
																		<option value="Completed">Complete</option>
																		<?php
																		}
																		if($purchase_details->cur_status == 'Completed')
																		{
																		?>
																		<option value="Completed">Completed</option>
																		<?php
																		}
																	}
																?>
															</select>
														</div>
													</div>
													<div class="col-md-2" style="margin-top: 1.5%;">
														<div class="form-group" >
															<div class="checkbox">
																<?php 
																$isChecked = empty($purchase_details->ShipToAddress) ? true : false;
																?>
																<input type="checkbox" name="useVendorAddress" id="useVendorAddress" 
																	   <?php echo $isChecked ? 'checked="checked"' : ''; ?> style="margin-right: 5px;">
																<label for="useVendorAddress" style="margin-bottom: 0; font-weight: normal;">
																	Same as Bill Address
																</label>
															</div>
														</div>
													</div> 
													
													<div class="col-md-3" id="shipToPartyContainer">
														<div class="form-group">
															<label for="ShipToParty">Ship To Party</label>
															<select name="ShipToParty" id="ShipToParty" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true">
																<option value="">None selected</option>
																<?php 
																$selectedShipToParty = isset($purchase_details->ShipToParty) ? $purchase_details->ShipToParty : '';
																foreach($PartyList as $party) { 
																?>
																	<option value="<?php echo html_entity_decode($party['AccountID']); ?>" 
																		<?php if($selectedShipToParty == $party['AccountID']) { echo 'selected'; } ?>>
																		<?php echo html_entity_decode($party['company']); ?>
																	</option>
																<?php } ?> 
															</select>  
														</div>	
													</div>
													
													<div class="col-md-3" id="shipToAddressContainer">
														<div class="form-group">
															<label for="ShipToAddress">Ship To Address</label>
															<select name="ShipToAddress" id="ShipToAddress" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true">
																<option value="">None selected</option>
																<?php  
																// Pre-load shipping addresses for the selected party
																if(isset($purchase_details->ShipToAddressList) && is_array($purchase_details->ShipToAddressList) && !empty($purchase_details->ShipToAddressList)) {
																	foreach($purchase_details->ShipToAddressList as $address) {
																		$shippingAddress = $address["ShippingAdrees"] ?? '';
																		$city = $address["city_name"] ?? '';
																		$state = $address["ShippingState"] ?? $address["state_name"] ?? '';
																		$pin = $address["ShippingPin"] ?? '';
																		
																		$leble = $shippingAddress . " " . $city . " (" . $state . ") - " . $pin;
																		$selected = (isset($purchase_details->ShipToAddress) && $purchase_details->ShipToAddress == $address["id"]) ? 'selected' : '';
																?>
																<option value="<?php echo $address["id"];?>" <?php echo $selected;?>>
																	<?php echo $leble;?>
																</option>
																<?php
																	}
																}
																?>
															</select>
														</div>
													</div>
										        </div>
										    </div>
										    
										    <div class="col-md-3">
										        <div class="row">
										            <div class="col-md-12">
    													<label for="address">Address</label>
    													<textarea class="form-control" readonly="" class="form-control" name="address" id="address"><?php echo htmlspecialchars($purchase_details->address); ?></textarea>
    												</div>
    												<div class="col-md-12">
    													<label for="TermsOfDelivery">Terms of Delivery</label>
    													<textarea class="form-control" name="TermsOfDelivery" id="TermsOfDelivery"><?php echo htmlspecialchars($purchase_details->DeliveryTerms); ?></textarea>
    												</div>
										        </div>
										    </div>
								        </div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body mtop10">
						<div class="row col-md-12">
							<p class="bold p_style"><?php echo _l('pur_order_detail'); ?></p>
							<div class="" id="example">
							</div>
							<?php echo form_hidden('pur_order_detail'); ?>
							
							<div class="col-md-2 ">
								
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px;width: 92px;" for="<?php echo _l('subtotal'); ?>"><?php echo _l('subtotal'); ?></label>  
									<input type="text" readonly class="form-control text-right" name="total_mn" value="<?= $purchase_details->Purchamt?>">
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Discount AMT</label>  
									<input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->Discamt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="dc_total">
									
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">CGST AMT</label>  
									<input type="hidden"  id="Other_amt_hidden">
									<input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->cgstamt; } ?>" class="form-control pull-left text-right" data-type="currency" name="CGST_amt" id="CGST_amt">
									
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">SGST AMT</label>  
									<input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->sgstamt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="SGST_AMT" id="SGST_AMT">
									
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">IGST AMT</label>  
									<input type="text" readonly  value="<?php if(isset($purchase_details)){ echo $purchase_details->igstamt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="IGST_amt" id="IGST_amt">
									
								</div>
							</div>
							<div class="col-md-2 "> 
								<div class="input-group" id="">
									<label  style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="TDS_amt">Tds Amt</label>  
									<input type="text" readonly  value="<?php if(isset($purchase_details)){ echo $purchase_details->tdsamt; } ?>" class="form-control pull-left text-right" data-type="currency" name="TDS_amt">
									
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Round OFF</label>  
									<input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->RoundOffAmt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="Round_OFF">
									
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Invoice AMT</label>  
									<input type="text" value="<?php if(isset($purchase_details)){ echo $purchase_details->Invamt; } ?>" class="form-control pull-left text-right"  data-type="currency" name="Invoice_amt" readonly>
									
								</div>
							</div> 
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="btn-bottom-toolbar text-right" style="width: 100%;">
								<a href="#" class="btn btn-warning edit-new-order">View List</a>
								<?php if (has_permission_new('purchase-order-po', '', 'edit')){
									if($purchase_details->cur_status == 'Pending')
									{
										
										$staff_id= $this->session->userdata('username');
										if (has_permission_new('purchase-order-po-approve', '', 'edit')){
										?>
										<a href="<?php echo admin_url() ?>purchase/ApprovePO/<?php echo $purchase_details->PurchID?>" class="btn-tr btn btn-success mleft10 ">
										Approve</a>
										<?php
										}
									}
								?>
								<button type="button"  class="btn btn-default POPrint"><i class="fa fa-print"></i> Print</button>
								<?php
									if($purchase_details->cur_status == 'Pending')
									{
									?>
									<button type="submit"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
										Update
									</button>
									<?php
									}
									}else{
								?>
								<button type="button"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
									Update
								</button>
								<?php
								}?>
							</div>
							
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>
				</div>
				
			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>
<div class="modal fade" id="transfer-modal">
	<div class="modal-dialog modal-xl" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Puchase Order List</h4>
			</div>
			
			<div class="modal-body" style="padding:5px;">
				<?php
                    $fy = $this->session->userdata('finacial_year');
                    $fy_new  = $fy + 1;
                    $lastdate_date = '20'.$fy_new.'-03-31';
                    $firstdate_date = '20'.$fy_new.'-04-01';
                    $curr_date = date('Y-m-d');
                    $curr_date_new    = new DateTime($curr_date);
                    $last_date_yr = new DateTime($lastdate_date);
                    if($last_date_yr < $curr_date_new){
                        $to_date = '31/03/20'.$fy_new;
                        $from_date = '01/03/20'.$fy_new;
						}else{
                        $from_date = "01/".date('m')."/".date('Y');
                        $to_date = date('d/m/Y');
					}
				?> 
				<div class="row">
					
					<div class="col-md-2">
						<?php
							echo render_date_input('from_date','From',$from_date);
						?>
					</div>
					<div class="col-md-2">
						<?php
							echo render_date_input('to_date','To',$to_date);
						?>
					</div>
					<div class="col-md-3">
						<br>
						<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
					</div>
					<div class="col-md-3">
						<br>
						<input type="text" id="myInput1" class="form-control" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
					</div>
					<div class="col-md-12">
						
						<div class="table_purchase_report">
							
							<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
								
								<thead>
									<tr style="display:none;">
										<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
									</tr>
									<tr>
										
										<th style="width:1% ">BT</th>
										<th style="width:7% ">PO No.</th>
										<th style="width:5% ">PO Date</th>
										<th style="width:15% text-align:left;">Purchased From</th>
										<th style="width:5% text-align:left;">Purchase Amt</th>
										<th style="width:3% text-align:left;">Disc Amt</th>
										<th style="width:5% text-align:left;">CGST Amt</th>
										<th style="width:5% text-align:left;">SGST Amt</th>
										<th style="width:5% text-align:left;">IGST Amt</th>
										<th style="width:5% text-align:left;">Inv. Amt</th>
										
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>   
						</div>
						<span id="searchh2" style="display:none;">
							Loading.....
						</span>
						
					</div>
				</div>
				
			</div>
			
			
		</div>
	</div>
</div>

<div class="modal fade" id="last-purchase-modal">
	<div class="modal-dialog modal-md" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Last Puchases Entry Details</h4>
			</div>
			
			<div class="modal-body" style="padding:5px;">
				
				<div class="row">
					
					<div class="col-md-12">
						
						<div class="last_purchase_details">
							
							<table class="tree table table-striped table-bordered last_purchase_details" id="last_purchase_details" width="100%">
								
								<thead>
									<tr>
										<th style="width:25% ">Purch Entry No.</th>
										<th style="width:25% ">Purch Entry Date</th>
										<th style="width:25% text-align:left;">Purchased From</th>
										<th style="width:25% text-align:left;">Basic Purch Rate</th>
										<th style="width:25% text-align:left;">GST(%)</th>
										<th style="width:25% text-align:left;">Net Purch Rate</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>   
						</div>
						
					</div>
				</div>
				
			</div>
			
			
		</div>
	</div>
</div>

<?php init_tail(); ?>
</body>
<style>
    .table_purchase_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table_purchase_report thead th { position: sticky; top: 0; z-index: 1; }
	.table_purchase_report tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.table_purchase_report table  { border-collapse: collapse; width: 100%; }
	.table_purchase_report th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.table_purchase_report th     { background: #50607b;color: #fff !important; }
	
	
	#table_purchase_report tr:hover {
	background-color: #ccc;
	}
	
	#table_purchase_report td:hover {
	cursor: pointer;
	}
	.last_purchase_details { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.last_purchase_details thead th { position: sticky; top: 0; z-index: 1; }
	.last_purchase_details tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.last_purchase_details table  { border-collapse: collapse; width: 100%; }
	.last_purchase_details th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.last_purchase_details th     { background: #50607b;color: #fff !important; }
	#last_purchase_details tr:hover {
	background-color: #ccc;
	}
	
	#last_purchase_details td:hover {
	cursor: pointer;
	}
</style>
<script type="text/javascript" language="javascript" >
	$('.POPrint').on('click', function() {
        var PONumber = '<?php echo $purchase_details->PurchID; ?>';
        
		var Link = '<?php echo admin_url(); ?>challan/OtherPOpdf/'+PONumber+'?output_type=I';
		window.open(Link,'_blank');
		
	})
	
	$(document).ready(function(){
		
		function load_data(from_date,to_date)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/load_data_for_purchaseOrder",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table_purchase_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table_purchase_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					var html = '';
					
					for(var count = 0; count < data.length; count++)
					{
						
						var url = "'<?php echo admin_url() ?>purchase/EditPurchaseOrder/"+data[count].PurchID+"'";
						html += '<tr onclick="location.href='+url+'">';
						html += '<td style="text-align:center;">'+data[count].BT+'</td>';
						html += '<td style="text-align:center;">'+data[count].PurchID+'</td>';
						var date = data[count].Transdate.substring(0, 10)
						var date_new = date.split("-").reverse().join("/");
						
						html += '<td  style="text-align:center;">'+date_new+'</td>';
						html += '<td >'+data[count].AccountName+'</td>';
						html += '<td style="text-align:right;">'+data[count].Purchamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].Discamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].cgstamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].sgstamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].igstamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].Invamt+'</td>';
						html += '</tr>';
					}
					$('.table_purchase_report tbody').html(html);
					
				}
			});
		}
		
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var msg = "Sales Report "+from_date +" To " + to_date;
			$(".report_for").text(msg);
			load_data(from_date,to_date);
			
		});
		updateValue();
	});
</script>

<script>
    function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_purchase_report");
		tr = table.getElementsByTagName("tr");
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[3];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else {
					tr[i].style.display = "none";
				}
			}       
		}
	}
</script>
<script>
    $('.add-new-transfer').on('click', function(){
		$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
		$('#transfer-modal').modal('show');
		init_journal_entry_table();
	});
</script>
<script>
    $(document).ready(function(){
		var maxEndDate = new Date('Y/m/d');
		var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
		
		var year = "20"+fin_y;
		var cur_y = new Date().getFullYear().toString().substr(-2);
		if(cur_y > fin_y){
			var year2 = parseInt(fin_y) + parseInt(1);
			var year2_new = "20"+year2;
			
			var e_dat = new Date(year2_new+'/03/31');
			var maxEndDate_new = e_dat;
			}else{
			var maxEndDate_new = maxEndDate;
		}
		
		var minStartDate = new Date(year, 03);
		
		$('#prd_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false
		});
	});
</script> 
<script>
    $(document).ready(function(){
		var maxEndDate = new Date('Y/m/d');
		var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
		
		var year = "20"+fin_y;
		var cur_y = new Date().getFullYear().toString().substr(-2);
		if(cur_y => fin_y){
			var year2 = parseInt(fin_y) + parseInt(1);
			var year2_new = "20"+year2;
			
			var e_dat = new Date(year2_new+'/03/31');
			
			var maxEndDate_new = e_dat;
			}else{
			var e_dat2 = new Date(year2+'/03/31');
			var maxEndDate_new = e_dat2;
		}
		
		var minStartDate = new Date(year, 03);
		
		
		$('#from_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false
		});
		
		$('#to_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false,
			showOtherMonths: false,
			pickTime: false,
            orientation: "left",
		});
		
	});
</script>
<script type="text/javascript">
$(document).ready(function() {
    // Function to toggle Ship To Party and Ship To Address visibility
    function toggleShipToAddress() {
        if ($('#useVendorAddress').is(':checked')) {
            $('#shipToAddressContainer').hide();
            $('#shipToPartyContainer').hide();
            $('#ShipToAddress').removeAttr('required');
            $('#ShipToParty').removeAttr('required');
            // Clear values when hidden
            $('#ShipToAddress').val('');
            $('#ShipToParty').val('');
        } else {
            $('#shipToAddressContainer').show();
            $('#shipToPartyContainer').show();
            $('#ShipToAddress').attr('required', 'required');
            $('#ShipToParty').attr('required', 'required');
        }
        $('.selectpicker').selectpicker('refresh');
    }
    
    // Initial state based on checkbox
    toggleShipToAddress();
    
    // Bind the change event
    $('#useVendorAddress').change(function() {
        toggleShipToAddress();
    });

    // ShipToParty change event - load shipping addresses
    $('#ShipToParty').on('change', function() {
        var ShipToParty = $(this).val();
        var BillToParty = $("#vendor").val();
        var url = "<?php echo admin_url(); ?>purchase/GetShippingAddressList";
        
        console.log('ShipToParty changed:', ShipToParty);
        console.log('BillToParty:', BillToParty);
        
        if(!BillToParty || BillToParty == ""){
            alert("Please Select Vendor First");
            $("#ShipToParty").val('');
			$('.selectpicker').selectpicker('refresh');
            return;
        }
        
        if(!ShipToParty || ShipToParty == "") {
            $("#ShipToAddress").children().remove();
            $("#ShipToAddress").append('<option value="">None selected</option>');
            $('.selectpicker').selectpicker('refresh');
            return;
        }
        
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: { 
                ShipToParty: ShipToParty 
            },
            dataType: 'json',
            beforeSend: function() {
                console.log('Loading shipping addresses for party:', ShipToParty);
            },
            success: function(data) {
                console.log('Shipping addresses received:', data);
                
                $("#ShipToAddress").children().remove();
                // $("#ShipToAddress").append('<option value="">None selected</option>');
                
                if(data && data.length > 0) {
                    // Reset shipping details
                    $('#shipping_state').val("");
                    $('#shipping_city').val("");
                    $('#ship_to_act_gst').val(data[0]["vat"] || "");
                    
                    for (var i = 0; i < data.length; i++) {
                        var address = data[i];
                        var shippingAddress = address.ShippingAdrees || '';
                        var city = address.city_name || '';
                        var state = address.ShippingState || address.state_name || '';
                        var pin = address.ShippingPin || '';
                        
                        // Create display text exactly like order template
                        var leble = shippingAddress + " " + city + " (" + state + ") - " + pin;
                        $("#ShipToAddress").append('<option value="' + address.id + '">' + leble + '</option>');
                    }
                } else {
                    $("#ShipToAddress").append('<option value="">No addresses found</option>');
                    console.warn('No shipping addresses found for this party');
                }
                $('.selectpicker').selectpicker('refresh');
            },
            error: function(xhr, status, error) {
                
                $("#ShipToAddress").children().remove();
                $("#ShipToAddress").append('<option value="">Error loading addresses</option>');
                $('.selectpicker').selectpicker('refresh');
            }
        });
    });

    // ShipToAddress change event - get shipping details
    $('#ShipToAddress').on('change', function() {
        var ShipToAddress = $(this).val();
        var url = "<?php echo admin_url(); ?>purchase/GetShippingDetails";
        
        if(!ShipToAddress || ShipToAddress == "") {
            return;
        }
        
        jQuery.ajax({
            type: 'POST',
            url: url,
            data: { 
                ShipToAddress: ShipToAddress 
            },
            dataType: 'json',
            success: function(data) {
                
                if(data) {
                    $('#shipping_state').val(data.state_name || '');
                    $('#shipping_city').val(data.city_name || '');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error in GetShippingDetails:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    });
});
</script>
</html>
<?php require 'modules/purchase/assets/js/pur_order_po_js.php';?>


