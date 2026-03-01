<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
	.tablehead{
	background-color:#415164 !important;
	
	}
	.tablehead>th {
    padding: 5px 2px;
	min-width: 100px;
	border: 0px !important;
	color: #fff !important;
	}
	
	.pup100 {
    width: 70%;
    margin: 0px auto auto;
    height: auto;
    background: #fff;
    overflow: hidden;
	}
</style>
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
								
								<div class="tab-content">
									
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
											
											<div class="col-md-2">
												<?php
													$selected_company = $this->session->userdata('root_company');
													$fy = $this->session->userdata('finacial_year');
													if($selected_company == 1){
														
														$new_purchase_orderNumbar = get_option('next_QC_entry_number_for_cspl');
													}
													
													$format = get_option('invoice_number_format');
													
													$prefix = "IQC";
													$__number = $new_purchase_orderNumbar;
													$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
													
													
													$_Entry_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
												?> 
												<div class="form-group">
													<label for="number"> 
														Entry No. 
													</label>
													<div class="input-group">
														<span class="input-group-addon">
															<?php
																echo $prefix;
															?>
														</span>
														<input type="text" name="qc_no" id="qc_no" class="form-control " value="<?php if(isset($order_details)){ echo substr($order_details->QC_no,5);}else{ echo $_Entry_number; } ?>" <?php if(isset($order_details)){ echo "disabled";} ?>>
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
												<?php $order_date = (isset($order_details) ? _d(substr($order_details->TransDate,0,10)) : _d($date));
													$attribute = array('readOnly'=>true);
												echo render_date_input('TransDate','Entry Date',$order_date,$attribute); ?>
												
											</div>
											
											<div class="col-md-2">
												<label for="PO_number"><?php echo _l('PO No.'); ?></label>
												<select name="PO_number" <?php if(isset($order_details)){echo "disabled";}?> onchange="GetPOData(this.value)" id="PO_number" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo "Non selected" ?>" >
													<?php
														if(!isset($order_details))
														{
														?>
														<option value="">Non Selected</option>
														<?php
														}
														if(!isset($order_details))
														{
															foreach($POData as $PO) { ?>
															<option  value="<?php echo $PO['PurchID']; ?>" ><?php echo $PO['PurchID']; ?></option>
															<?php 
															}
														}
														if(isset($order_details))
														{
															foreach($Itemdata as $Item) { ?>
															<option  value="<?php echo $Item['OrderID']; ?>" ><?php echo $Item['OrderID']; ?></option>
														<?php }} ?>
														
												</select>  
											</div>
											
											<div class="col-md-2">
												<label for="item">Item</label>
												<select name="item" <?php if(isset($order_details)){echo "disabled";}?> onchange="GetQCParameter(this.value)" id="item" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo "Non selected" ?>" >
													<?php
														if(!isset($order_details))
														{
														?>
														<option value="">Non Selected</option>
														<?php
														}
														if(isset($order_details))
														{
															foreach($Itemdata as $Item) { ?>
															<option  value="<?php echo $Item['item_code']; ?>" ><?php echo $Item['description']."(".$Item['item_code'].")"; ?></option>
														<?php }} ?>
												</select>  
											</div>
										</div>
										<div class="row">
											
											<div class="col-md-3">
												<div class="form-group">
													<label for="VendorID">VendorID</label>
													<input type="text" readonly="" value="<?php if(isset($order_details)){echo $order_details->AccountID;}?>" class="form-control" name="VendorID" id="VendorID"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-3 ">
												<div class="form-group">
													<label for="Vendor_name">Vendor Name</label>
													<input type="text" readonly="" value="<?php if(isset($order_details)){echo $order_details->company;}?>" class="form-control" name="Vendor_name" id="Vendor_name"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="Vendor_mobile">Vendor Mobile</label>
													<input type="text" value="<?php if(isset($order_details)){echo $order_details->phonenumber;}?>" readonly="" class="form-control" name="Vendor_mobile" id="Vendor_mobile"  aria-invalid="false">
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="Vendor_location">Vendor Location</label>
													<input type="text" readonly="" class="form-control" name="Vendor_location" id="Vendor_location" value="<?php if(isset($order_details)){echo $order_details->state;}?>"  aria-invalid="false">
												</div>
											</div>
											
											<div class="col-md-1">
												<br>
												<span></span><a href="#" class="btn btn-warning edit-new-order">View List</a>
											</div>
										</div>
										<div class="row">
											
											
										</div> 
										
										
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-body mtop10">
						<div class="row col-md-12">
							<p class="bold p_style"><?php echo "Parameter Check List" ?></p>
							<hr class="hr_style"/>
							<div class="" id="example">
							</div>
							
							<div style="margin-top:50px;" class="" id="example2">
							</div>
							<?php echo form_hidden('QC_detail'); ?>
							
							
							<div class="col-md-8 ">
							</div>
							
							
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mtop15">
							<div class="btn-bottom-toolbar text-right" style="width: 100%;">
								
								<?php
									if(isset($order_details))
									{
										if (has_permission_new('Item_QC', '', 'edit'))
										{ 
										?>
										<button type="button"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
											<?php echo _l('Update'); ?>
										</button>
										<?php
										}
									}
									else
									{
										if (has_permission_new('Item_QC', '', 'create')){
										?>
										
										<button type="button"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
											<?php echo _l('submit'); ?>
										</button>
										<?php
										}
									}
								?>
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
	<div class="modal-dialog pup100" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Item Wise List</h4>
			</div>
			<div class="modal-body" style="padding:5px;">
				
				<div class="row">
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
					<div class="col-md-3">
						<?php
							echo render_date_input('from_date','From',$from_date);
						?>
					</div>
					<div class="col-md-3">
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
						<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
					</div>
					<div class="col-md-12">
						
						<div class="table_damage_report">
							
							<table class="tree table table-striped table-bordered table_damage_report" id="table_damage_report" width="100%">
								
								<thead>
									
									<tr style="white-space: nowrap;">
										<th align="center">QC No</th>
										<th align="center">QC Date</th>
										<th align="center">PO No</th>
										<th align="center">Item Name</th>
										<th align="center">UserID</th>
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
<?php init_tail(); ?>
<?php $this->load->view('QC/ItemQC_js.php'); ?>
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

<script>
	function GetPOData(PO_number){
		var PO_number = $('#PO_number').val();
		if(PO_number == '')
		{
			var dataObject2 = []; 
			hot.loadData(dataObject2);
			
			
		}
		else
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/GetPODataIsnpectionDone",
				dataType:"JSON",
				method:"POST",
				data:{PO_number:PO_number},
				
				success:function(rtndata){
					$('#item').html(rtndata.Itemlist);
					$('select[id="item"]').selectpicker('refresh');
					
					if (rtndata.purchmastertbl.AccountID !== null) {
						$('#VendorID').val(rtndata.purchmastertbl.AccountID);
						$('#Vendor_name').val(rtndata.purchmastertbl.company);
						$('#Vendor_mobile').val(rtndata.purchmastertbl.phonenumber);
						$('#Vendor_location').val(rtndata.purchmastertbl.state);
						} else {
						$('#VendorID').val('');
						$('#Vendor_name').val('');
						$('#Vendor_mobile').val('');
						$('#Vendor_location').val('');
						
					}
					
					var dataObject2 = [];   
					
					hot.loadData(dataObject2);
					
				}
			});
		}
	}
	function GetQCParameter(itemid){
		var PO_number = $('#PO_number').val();
		if(itemid == '')
		{
			var dataObject2 = []; 
			hot.loadData(dataObject2);
			
		}
		else
		{
			var dataObject2 = [];
			hot.loadData(dataObject2);
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/GetQCParameterByItem",
				dataType:"JSON",
				method:"POST",
				data:{itemid:itemid,PO_number:PO_number},
				
				success:function(rtndata){
					
					if(rtndata.mastertbl.length > 0)
					{
						if(rtndata.qcItemtbl.length > 0)
						{
							alert('This Item QC Is Already Done');
							$('select[name=item]').val('');
							$('.selectpicker').selectpicker('refresh');
						}
						else
						{
							hot.loadData(rtndata.mastertbl);
						}
					}
					else
					{
						hot.loadData(dataObject2);
					}
				}
			});
		}
	}
	
	function formatDate(inputDate) {
		const date = new Date(inputDate);
		
		const year = date.getFullYear();
		const month = String(date.getMonth() + 1).padStart(2, '0');
		const day = String(date.getDate()).padStart(2, '0');
	
	return `${year}/${month}/${day}`;
	}
	</script>
	
	<script>
		$(document).ready(function(){
			
			function load_data(from_date,to_date)
			{
				$.ajax({
					url:"<?php echo admin_url(); ?>purchase/load_data_for_qc_entry",
					dataType:"JSON",
					method:"POST",
					data:{from_date:from_date, to_date:to_date},
					beforeSend: function () {
						
						$('#searchh2').css('display','block');
						$('.table_damage_report tbody').css('display','none');
						
					},
					complete: function () {
						
						$('.table_damage_report tbody').css('display','');
						$('#searchh2').css('display','none');
					},
					success:function(data){
						$('.table_damage_report tbody').html(data);
					}
				});
			}
			
			$('#search_data').on('click',function(){
				var from_date = $("#from_date").val();
				var to_date = $("#to_date").val();
				load_data(from_date,to_date);
				
			});
			
		});
	</script>
	
	<script>
		function myFunction2() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("myInput1");
			filter = input.value.toUpperCase();
			table = document.getElementById("table_contra_report");
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
			
			$('#TransDate').datetimepicker({
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
</html>


