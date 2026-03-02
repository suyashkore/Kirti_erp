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
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Purchase Entry</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
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
											
											<div class="col-md-2">
												<?php
													$selected_company = $this->session->userdata('root_company');
													$fy = $this->session->userdata('finacial_year');
													if($selected_company == 1){
														
														$new_purchase_orderNumbar = get_option('next_purchase_number_for_cspl');
														}elseif($selected_company == 2){
														$new_purchase_orderNumbar = get_option('next_purchase_number_for_cff');
														}elseif($selected_company == 3){
														$new_purchase_orderNumbar = get_option('next_purchase_number_for_cbu');
														}elseif($selected_company == 4){
														$new_purchase_orderNumbar = get_option('next_purchase_number_for_cbupl');
													}
													
													$format = get_option('invoice_number_format');
													
													$prefix = get_purchase_option('pur_order_prefix');
													if ($format == 1) {
														$__number = $new_purchase_orderNumbar;
														$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
														} else if($format == 2) {
														
														$__number = $new_purchase_orderNumbar;
														$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>/';
														
														} else if($format == 3) {
														
														$__number = $new_purchase_orderNumbar;
														
														} else if($format == 4) {
														
														$yyyy = date('Y');
														$mm = date('m');
														$__number = $new_purchase_orderNumbar;						
													}
													
													$_production_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
												?> 
												<div class="form-group">
													<label for="number"> 
														PO Entry No.
													</label>
													<div class="input-group">
														<span class="input-group-addon">
															<?php
																echo $prefix;
															?>
														</span>
														<input type="text" name="pro_orderid" id="pro_orderid" class="form-control receiptsid" value="<?php echo ($_is_draft) ? 'DRAFT' : $_production_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>
													</div>
												</div>
												
											</div>   
											
											<div class="col-md-2">
												<?php
													$fy = $this->session->userdata('finacial_year');
													$fy_new  = $fy + 1;
													$lastdate_date = '20'.$fy_new.'-03-31';
													$curr_date = date('Y-m-d');
													$curr_date_new    = new DateTime($curr_date);
													$last_date_yr = new DateTime($lastdate_date);
													if($last_date_yr < $curr_date_new){
														$date = $lastdate_date;
														}else{
														$date = date('Y-m-d');
													}
												?>
												<?php $order_date = (isset($pur_order) ? _d($pur_order->order_date) : _d($date));
												echo render_date_input('prd_date','PO Entry Date',$order_date); ?>
												
											</div>
											
											<div class="col-md-2">
												<div class="form-group">
													<label for="estimate">GST</label>
													<input type="text" readonly="" class="form-control" name="gst_num" id="gst_num"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="estimate">Balance</label>
													<input type="text" readonly="" class="form-control" name="c_balance" id="c_balance"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-3 ">
												<div class="form-group">
													<label for="estimate">Address</label>
													<input type="text" readonly="" class="form-control" name="address" id="address"  aria-invalid="false">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-2">
												<label for="vendor"><?php echo _l('Vendor Name'); ?></label>
												<select name="vendor" id="vendor" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
													<option value=""></option>
													<?php foreach($vendors as $s) { ?>
														<option value="<?php echo html_entity_decode($s['AccountID']); ?>" ><?php echo html_entity_decode($s['company'])." - ".html_entity_decode($s['AccountID']); ?></option>
													<?php } ?>
												</select>  
												<br><br>
											</div>
											<div class="col-md-2">
												<label for="po_number"><?php echo _l('P.O No.'); ?></label>
												<select name="po_number" required id="po_number" onchange="GetPOItems(this.value)" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="None Selected" >
													<option value=""></option>
													<!--<?php foreach($pendingOrder_list as $s) { ?>
														<option value="<?php echo $s['PurchID']; ?>" ><?php echo $s['PurchID']; ?></option>
													<?php } ?>-->
												</select>  
												<br><br>
											</div>
											<div class="col-md-3" style="display:none;">
												<div class="form-group">
													<label for="estimate">Vendor Name</label>
													<input type="text" readonly="" class="form-control" name="c_name" id="c_name"  aria-invalid="false">
													<input type="hidden" readonly="" class="form-control" name="item_associated" id="item_associated"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="estimate">State Name</label>
													<input type="text" readonly="" class="form-control" name="state_f" id="state_f"  aria-invalid="false">
													<input type="hidden" name="state_c" id="state_c" value="">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="estimate">City</label>
													<input type="text" readonly="" class="form-control" name="city" id="city"  aria-invalid="false">
												</div>
											</div>
											
											<div class="col-md-3">
												<div class="form-group">
													<label for="estimate">Address 2</label>
													<input type="text" readonly="" class="form-control" name="address2" id="address2"  aria-invalid="false">
												</div>
											</div>
											
										</div>
										<div class="row">
											<div class="col-md-2">
												<div class="form-group" app-field-wrapper="GodownID">
													<small class="req text-danger"> </small>
													<label for="GodownID" class="form-label">Godown Name</label> 
													<select name="GodownID" id="GodownID" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
														<!--<option value="">Non Selected</option>-->
														<?php
															foreach ($GodownData as $key => $value) {
																if($value['AccountID'] == "CSPL" || $value['AccountID'] == "RM"){
																?>
																<option <?php if($value['AccountID'] == "RM"){echo "selected";}?> value="<?php echo $value['AccountID'];?>"><?php echo $value['AccountName'];?></option>
																<?php
																}
															}
														?>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="IsTDS">TDS Applicable ?</label>
													<input type="text" readonly="" class="form-control" value="" name="IsTDS" id="IsTDS"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="TDSSec">TDS Section</label>
													<input type="text" readonly="" class="form-control" value="" name="TDSSec" id="TDSSec"  aria-invalid="false">
													<input type="hidden" readonly="" class="form-control" value="" name="TDSCode" id="TDSCode"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label for="TDSPer">TDS %</label>
													<input type="text" readonly="" class="form-control" value="" name="TDSPer" id="TDSPer"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
												<label for="invoce_n">Invoice Number</label>
												<input type="text"  class="form-control" name="invoce_n" id="invoce_n"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-2">
												<?php $order_date = (isset($pur_order) ? _d($pur_order->order_date) : _d(date('Y-m-d')));
												echo render_date_input('invoce_date','Invoice Date',$order_date); ?>
											</div>
											
											<div class="col-md-1" style="display:none;">
												<div class="form-group">
													<label for="estimate">State</label>
													
												</div>
											</div>
											
											<!--<div class="col-md-1">
												<br>
												<span></span>
											</div>-->
											
										</div> 
										
										
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body mtop10">
						<div class="row col-md-12">
							<p class="bold p_style"><?php echo _l('pur_order_detail'); ?></p>
							<hr class="hr_style"/>
							<div class="" id="example">
							</div>
							<p class="bold p_style" style="margin-top:50px;"><?php echo _l('Freight & Other Charges'); ?></p>
							<hr class="hr_style"/>
							<div class=""  id="example2">
							</div>
							<?php echo form_hidden('pur_order_detail'); ?>
							
							<?php echo form_hidden('charges_details'); ?>
							
							<div class="col-md-2 ">
								
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px;width: 92px;" for="PurchaseAmt">PurchaseAmt</label>  
									<input type="text" readonly class="form-control text-right" name="total_mn" value="">
								</div>
							</div>
							
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Discount AMT</label>  
									<input type="text" readonly value="<?php if(isset($pur_order)){ echo app_format_money($pur_order->discount_total,''); } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="dc_total">
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">CGST AMT</label>  
									<input type="text" readonly value="<?php if(isset($pur_order)){ echo app_format_money($pur_order->discount_total,''); } ?>" class="form-control pull-left text-right" data-type="currency" name="CGST_amt">
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">SGST AMT</label>  
									<input type="text" readonly value="<?php if(isset($pur_order)){ echo app_format_money($pur_order->discount_total,''); } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="SGST_AMT">
								</div>
							</div>
							
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">IGST AMT</label>  
									<input type="text" readonly  value="<?php if(isset($pur_order)){ echo app_format_money($pur_order->discount_total,''); } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="IGST_amt">
								</div>
							</div>
							<div class="col-md-2 "> 
								<div class="input-group" id="">
									<label  style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="TDS_amt">Tds Amt</label>  
									<input type="text" readonly  value="<?php if(isset($pur_order)){ echo $pur_order->tdsamt; } ?>" class="form-control pull-left text-right" data-type="currency" name="TDS_amt">
									
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Round OFF</label>  
									<input type="text" readonly value="<?php if(isset($pur_order)){ echo app_format_money($pur_order->discount_total,''); } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="Round_OFF">
								</div>
							</div>
							<div class="col-md-2 ">
								<div class="input-group" id="discount-total">
									<label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Invoice AMT</label>  
									<input type="text" readonly value="<?php if(isset($pur_order)){ echo $pur_order->invamt; } ?>" class="form-control pull-left text-right" data-type="currency" name="Invoice_amt">
								</div>
							</div> 
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="panel-body bottom-transaction">
								
								<div id="vendor_data">
									
								</div>
								
								<div class="btn-bottom-toolbar text-right" style="width: 100%;">
									
									<a href="#" class="btn btn-warning edit-new-order">View List</a>
									<?php if (has_permission_new('purchase-order', '', 'create')){
									?>
									
									<button type="button"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
										<?php echo _l('submit'); ?>
									</button>
									<?php
									}?>
								</div>
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
<style>
	/*    @media (min-width: 768px)*/ 
	/*        .modal-xl {*/
	/*    width: 90%;*/
	/*    max-width: 1230px;*/
	/*}*/
</style>
<div class="modal fade" id="transfer-modal">
	<div class="modal-dialog modal-xl" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Puchase Entry List</h4>
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
							echo render_date_input('from_date','From Date',$from_date);
						?>
					</div>
					<div class="col-md-2">
						<?php
							echo render_date_input('to_date','To Date',$to_date);
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
									<tr>
										<th class="sortablePop" style="width:1% ">BT</th>
										<th class="sortablePop" style="width:7% ">PO Entry No.</th>
										<th class="sortablePop" style="width:5% ">PO Entry Date</th>
										<th class="sortablePop" style="width:15% text-align:left;">Purchased From</th>
										<th class="sortablePop" style="width:5% text-align:left;">Invoce No.</th>
										<th class="sortablePop" style="width:5% text-align:left;">Vendor Inv. Date</th>
										<th class="sortablePop" style="width:5% text-align:left;">Purchase Amt</th>
										<th class="sortablePop" style="width:3% text-align:left;">Disc Amt</th>
										<th class="sortablePop" style="width:3% text-align:left;">Other Charges</th>
										<th class="sortablePop" style="width:5% text-align:left;">CGST Amt</th>
										<th class="sortablePop" style="width:5% text-align:left;">SGST Amt</th>
										<th class="sortablePop" style="width:5% text-align:left;">IGST Amt</th>
										<th class="sortablePop" style="width:5% text-align:left;">Tds Amt</th>
										<th class="sortablePop" style="width:5% text-align:left;">Inv. Amt</th>
										
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
	<div class="modal-dialog modal-xl" style=" max-width: 1230px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Last Puchases</h4>
			</div>
			
			<div class="modal-body" style="padding:5px;">
				
				<div class="row">
					
					<div class="col-md-12">
						
						<div class="table_purchase_report_old">
							
							<table class="tree table table-striped table-bordered table_purchase_report_old" id="table_purchase_report_old" width="100%">
								
								<thead>
									<tr>
										<th style="width:25% ">Purchase No.</th>
										<th style="width:25% ">Date</th>
										<th style="width:25% text-align:left;">Purchased From</th>
										<th style="width:25% text-align:left;">Rate</th>
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
<script type="text/javascript">
	$('#tcs_pre_data').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
			event.preventDefault();
		}
	});
</script>
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
</style>
<script type="text/javascript" language="javascript" >
	function GetPOItems(PoNumber)
	{
		if(PoNumber == '')
		{
			var dataObject2 = []; 
			hot.loadData(dataObject2);
			
			// $('#vendor').val('');
			// $('.selectpicker').selectpicker('refresh');
			// $('#c_name').val('');
			// $('#gst_num').val('');
			// $('#c_balance').val('');
			// $('#city').val('');
			// $('#address').val('');
			// $('#address2').val('');
			// $('#state_c').val('');
			// $('#state_f').val('');
			
			
			$('input[name="total_mn"]').val('');
			$('input[name="Other_amt"]').val('');
			$('input[name="Freight_AMT"]').val('');
			$('input[name="dc_total"]').val('');
			$('input[name="Round_OFF"]').val('');
			$('input[name="total_mn"]').val('');
			$('input[name="CGST_amt"]').val('');
			$('input[name="SGST_AMT"]').val('');
			$('input[name="IGST_amt"]').val('');
			$('input[name="Invoice_amt"]').val('');
		}
		else
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/GetPOData",
				dataType:"JSON",
				method:"POST",
				data:{PoNumber:PoNumber},
				
				success:function(rtndata){
					console.log(rtndata);
					
					var dataObject2 = [];   
					
					if (rtndata.ordertbl !== null) {
						// $('#vendor').val(rtndata.ordertbl.userid);
						// $('.selectpicker').selectpicker('refresh');
						
						$("#item_associated").val(rtndata.ordertbl.items);
						// $('#vendor').change();
						
						$('input[name="total_mn"]').val(rtndata.ordertbl.Purchamt);
						$('input[name="dc_total"]').val(rtndata.ordertbl.Discamt);
						$('input[name="CGST_amt"]').val(rtndata.ordertbl.cgstamt);
						$('input[name="SGST_AMT"]').val(rtndata.ordertbl.sgstamt);
						$('input[name="IGST_amt"]').val(rtndata.ordertbl.igstamt);
						$('input[name="Round_OFF"]').val(rtndata.ordertbl.RoundOffAmt);
						$('input[name="Invoice_amt"]').val(rtndata.ordertbl.Invamt);
						
						} else {
						$('#vendor').val('');
						$('.selectpicker').selectpicker('refresh');
						
						$("#item_associated").val('');
						$('#c_name').val('');
						$('#gst_num').val('');
						$('#c_balance').val('');
						$('#city').val('');
						$('#address').val('');
						$('#address2').val('');
						$('#state_c').val('');
						$('#state_f').val('');
						
						
						$('input[name="total_mn"]').val('');
						$('input[name="Other_amt"]').val('');
						$('input[name="Freight_AMT"]').val('');
						$('input[name="dc_total"]').val('');
						$('input[name="Round_OFF"]').val('');
						$('input[name="total_mn"]').val('');
						$('input[name="CGST_amt"]').val('');
						$('input[name="SGST_AMT"]').val('');
						$('input[name="IGST_amt"]').val('');
						$('input[name="Invoice_amt"]').val('');
					}
					
					if(rtndata.historytbl.length > 0)
					{
						hot.loadData(rtndata.historytbl);
					}
					else
					{
						hot.loadData(dataObject2);
					}
					
					updateValue();
				}
			});
		}
	}
	$(document).ready(function(){
		
		function load_data(from_date,to_date)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/load_data_for_purchase",
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
						
						var url = "'<?php echo admin_url() ?>purchase/EditPurchaseEntry/"+data[count].PurchID+"'";
						html += '<tr onclick="location.href='+url+'">';
						html += '<td style="text-align:center;">'+data[count].BT+'</td>';
						html += '<td style="text-align:center;">'+data[count].PurchID+'</td>';
						var date = data[count].Transdate.substring(0, 10)
						var date_new = date.split("-").reverse().join("/");
						
						html += '<td  style="text-align:center;">'+date_new+'</td>';
						html += '<td >'+data[count].AccountName+'</td>';
						html += '<td  style="text-align:left;">'+data[count].Invoiceno +'</td>';
						var date2 = data[count].Invoicedate.substring(0, 10)
						var date_new2 = date2.split("-").reverse().join("/");
						html += '<td  style="text-align:center;">'+date_new2+'</td>';
						html += '<td style="text-align:right;">'+data[count].Purchamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].Discamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].OtherCharges+'</td>';
						html += '<td style="text-align:right;">'+data[count].cgstamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].sgstamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].igstamt+'</td>';
						html += '<td style="text-align:right;">'+data[count].TdsAmt+'</td>';
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
			var msg = from_date +" To " + to_date;
			$(".report_for").text(msg);
			load_data(from_date,to_date);
			
		});
		
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
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_purchase_report tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop").removeClass("asc desc");
		$(".sortablePop span").remove();
		
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		
		rows.sort(function (a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();
			
			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
				} else {
				return ascending
                ? valA.localeCompare(valB)
                : valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});
</script>
</html>
<?php require 'modules/purchase/assets/js/pur_order_js.php';?>

