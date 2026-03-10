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
								
							<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>QC</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>In Process Plant QC</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
								<div class="tab-content">
									
									<div role="tabpanel" class="tab-pane active" id="general_infor">
										<div class="row">
											
											<div class="col-md-2">
												<?php
													$selected_company = $this->session->userdata('root_company');
													$fy = $this->session->userdata('finacial_year');
													if($selected_company == 1){
														
														$new_purchase_orderNumbar = get_option('next_QC_In_process_plant_number_for_cspl');
													}
													
													$format = get_option('invoice_number_format');
													
													$prefix = "IPP";
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
														<input type="text" name="qc_no" id="qc_no" class="form-control " value="<?php if(isset($order_details)){ echo substr($order_details->entry_no,5);}else{ echo $_Entry_number; } ?>" <?php if(isset($order_details)){ echo "disabled";} ?>>
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
											
											
										</div>
										<div class="row">
											
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
							<p class="bold p_style"><?php echo "In Process QC" ?></p>
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
										if (has_permission_new('In_Process_QC', '', 'edit'))
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
										if (has_permission_new('In_Process_QC', '', 'create')){
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
				<h4 class="modal-title">In-Process Plant List</h4>
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
										<th class="sortablePop" align="center">Entry No</th>
										<th class="sortablePop" align="center">Entry Date</th>
										<th class="sortablePop" align="center">UserID</th>
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
<?php $this->load->view('QC/In_Process_QC_js.php'); ?>
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
					url:"<?php echo admin_url(); ?>purchase/load_data_for_process_plant_entry",
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
		
		$(document).on("click", ".sortablePop", function () {
		var table = $("#table_damage_report tbody");
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


