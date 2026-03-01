<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Delay Orders</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
						    <div class="col-md-2">
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
									echo render_date_input('from_date','From Date',$from_date);        
								?>
							</div>
							<div class="col-md-2">
								<?php          
									echo render_date_input('date','To Date',$to_date);            
								?>
							</div>
							<div class="col-md-2">
								<?php 
									echo render_select( 'states',$states,array( 'short_name','state_name'), 'client_state','',array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
								?>
							</div>
							<div class="col-md-2">
								<?php echo render_select('dist_type',$dist_type,array('id','name'),'Dist Type'); ?>
							</div>
							<div class="col-md-2">
								<?php echo render_select('StationName',$StationList,array('id','StationName'),'Station Name'); ?>
							</div>
							<div class="clearfix"></div>
							
							<div class="col-md-8">
								<div class="custom_button">
									<button class="btn btn-info search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
									<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
									<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="production_report" href="#" id="caexcel" style="font-size:12px;"><span>Export Order</span></a>
								</div>
							</div>
							<div class="col-md-4">
								<input type="text" id="myInput1" onkeyup="myFunction1()" placeholder="Search.." class="form-control" style="float: right;">
							</div>
							<div class="clo-md-12">
							    <span id="searchh3" style="display:none;">please wait exporting data.....</span>
							</div>
							<div class="col-md-12">
							    <div class="fixed_header">
    							<table class="table table-striped table-bordered" id="export_table_to_excel_1" width="100%">
    								<thead>
    									<tr class="only_print">
    										<th colspan="15"><?php echo $selected_company_details->FIRMNAME; ?></th>
    									</tr>
    									<tr class="only_print">
    										<th colspan="15"><?php echo $selected_company_details->ADDRESS1.", ".$selected_company_details->ADDRESS2; ?></th>
    									</tr>
    									<tr>
    									    <th>Sr.No.</th>
    									    <th>OrderID</th>
    									    <th>Order Date</th>
    									    <th>Delivery Date</th>
    									    <th>Dispatch Date</th>
    									    <th>Delay Time</th>
    									    <th>Party Name</th>
    									    <th>Station Name</th>
    									    <th>State Name</th>
    									    <th>Vehicle No</th>
    									    <th>Driver Name</th>
    									    <th>Created By</th>
    									</tr>
    								</thead>
    								<tbody id="pending_data">
    								    
    								</tbody>
    							</table>
    							</div>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<?php init_tail(); ?>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var dates = $("#date").val();
			var state = $("#states").val();
			var dist_type = $("#dist_type").val();
			var StationName = $("#StationName").val();
			load_data(from_date,dates,state,dist_type,StationName);
			
		});
		function load_data(from_date,dates,state,dist_type,StationName)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>order/GetDelayOrders",
				//dataType:"JSON",
				method:"POST",
				data:{from_date:from_date,dates:dates, state:state, dist_type:dist_type,StationName:StationName},
				beforeSend: function () {
					$('#searchh').css('display','block');
					$('#pending_data').css('display','none');
				},
				complete: function () {
					$('#pending_data').css('display','');
					$('#searchh').css('display','none');
				},
				success:function(data){
					$('#pending_data').html(data);
				}
			});
		}
	});
</script>
<style>
	.fixed_header { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
	.fixed_header thead th { position: sticky; top: 0; z-index: 1; }
	.fixed_header tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.fixed_header table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	.fixed_header th     { background: #50607b;color: #fff !important; }
	
</style>
<style>
	@media screen {
	.only_print{
	display:none !important;
	}
    }
</style>