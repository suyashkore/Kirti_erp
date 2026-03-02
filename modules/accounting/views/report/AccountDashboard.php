<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		
		<!-- Filter row-->
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
				} else {
				$from_date = date('01/m/Y');
				$to_date = date('d/m/Y');
			}
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Account</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Dashboard</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-3">
										<?php
											echo render_date_input('from_date2','From Date',$from_date);
										?>
									</div>
									<div class="col-md-3">
										<?php
											echo render_date_input('to_date2','To Date',$to_date);
										?>
									</div>
									<!--<div class="col-md-3">
										<div class="form-group">
										<label for="TradeType" class="control-label"><small class="req text-danger"></small>Period</label>
										<?php //$val = date("Y-m");?>
										<input type="month" id="period" name="period" class="form-control" value="<?php //echo $val;?>"> 
										</div> 
									</div>   -->
									
									<div class="col-md-2" style="margin-top:20px;">
										<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button> 
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="row">
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
										<div class="top_stats_wrapper custdesg bg2">
											<div class="col-md-12">
												<p class="JournalSpinner mtop5 labeltxt" style="display: none;">
												<i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="JournalEntryAmt"></p>
												<p class="title"><?php echo _l('Total Journal Entry'); ?></p>
											</div>
										</div>
									</div>
									
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="ContraSpinner mtop5 labeltxt" style="display: none;">
												<i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="ContraEntryAmt"></p>
												<p class="title"><?php echo _l('Total Contra Entry'); ?></p>
											</div>
										</div>
									</div>
									
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="ReceiptSpinner mtop5 labeltxt" style="display: none;">
												<i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="ReceiptEntryAmt"></p>
												<p class="title"><?php echo _l('Total Receipt Entry'); ?></p>
											</div>
										</div>
									</div>
									
									<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
										<div class="top_stats_wrapper custdesg bg1">
											<div class="col-md-12">
												<p class="PaymentSpinner mtop5 labeltxt" style="display: none;">
												<i class="fa fa-spinner fa-spin"></i></p>
												<p class="mtop5 labeltxt" id="PaymentEntryAmt"></p>
												<p class="title"><?php echo _l('Total Payment Entry'); ?></p>
											</div>
										</div>
									</div>
									
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="month">
									<label for="month" class="control-label">Receipt Calendar</label>
									<?php $val = date("Y-m");?>
									<input type="month" id="payDuemonth" name="payDuemonth" class="form-control" value="<?php echo $val;?>">
								</div>								
							</div>
							<div class="col-md-12">
								<figure class="highcharts-figure">
									<div id="container_ReceiptsCollectioncalander"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="month">
									<label for="month" class="control-label">Payment Calendar</label>
									<?php $val = date("Y-m");?>
									<input type="month" id="payCalmonth" name="payCalmonth" class="form-control" value="<?php echo $val;?>">
								</div>								
							</div>
							<div class="col-md-12">
								<figure class="highcharts-figure">
									<div id="container_PaymentCollectioncalander"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-md-12 Padding_left ReceiptPayment">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="ReceiptPaymentSpinner mtop5 SpinnerCSS" style="display: none;" ><i class="fa fa-spinner fa-spin"></i></p>
				        <div class="row ReceiptPaymentFigure">
						    <div class="col-md-12">
						        <figure class="highcharts-figure">
									<div id="ReceiptPayment"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			<!-- Monthly Credit Out Amount   -->
			<div class="col-md-6 MonthWiseCreditOutAmt Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="MonthWiseCreditOutAmtSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure MonthWiseCrdFigure">
							<div id="MonthWiseCreditOutAmt"></div>
						</figure>
					</div>
				</div> 
			</div>
			<!-- Creditors end -->
			
			<!-- Debtors  -->
			<div class="col-md-6 MonthWiseDebtAmt Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="MonthWiseDebtAmtSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure MonthWiseDebtFigure">
							<div id="MonthWiseDebtAmt"></div>
						</figure>
					</div>
				</div>
			</div>
			<!-- Debtors end -->
			
			<div class="clearfix"></div>
			
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<p class="BillsReceivableSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<div class="row">
							<div class="col-md-12">
								<figure class="highcharts-figure">
									<div id="BillsReceivable"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<p class="BillsPayableSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<div class="row">
							<div class="col-md-12">
								<figure class="highcharts-figure">
									<div id="BillsPayable"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<p class="BillsReceivableAgeingSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<div class="row">
							<div class="col-md-12">
								<figure class="highcharts-figure">
									<div id="BillsReceivableAgeing"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel_s">
					<div class="panel-body">
						<p class="BillsPayableAgeingSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<div class="row">
							<div class="col-md-12">
								<figure class="highcharts-figure">
									<div id="BillsPayableAgeing"></div>
								</figure>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			<div class="col-md-6 MonthWiseClosingBal Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="MonthWiseClosingAmtSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure MonthWiseClosingBalFigure">
							<div id="MonthWiseClosingBal"></div>  
						</figure>
					</div>
				</div>
			</div> 
			<div class="col-md-6 Padding_left_right CashAndEquivalant">
	            <div class="panel_s">
					<div class="panel-body">
					    <p class="CashAndEquivalantSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
				        <figure class="highcharts-figure CashAndEquivalantFigure">
							<div id="CashAndEquivalant"></div>
						</figure>
					</div>
				</div>
			</div>
			
			<div class="clearfix"></div>
			<div class="col-md-6 Expenses Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="ExpensesSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure ExpensesFigure">
							<div id="Expenses"></div>  
						</figure>
					</div>
				</div>
			</div> 
			<div class="col-md-6 MonthWiseSalePurchase Padding_right">
				<div class="panel_s top_stats_wrapper">
					<div class="panel-body">
						<p class="MonthWiseSalePurchSpinner mtop5 SpinnerCSS" style="display: none;"><i class="fa fa-spinner fa-spin"></i></p>
						<figure class="highcharts-figure MonthlySalePurchaseFigure">
							<div id="MonthWiseSalePurchase"></div>  
						</figure>
					</div>
				</div>
			</div> 
			
			<div class="clearfix"></div>
			
			
			
		</div>		
	</div>
</div>

<style>
	.highcharts-credits {
	display: none;
	}
	
	.custdesg{
	height:55px;
	}
	.col-md-3.col-sm-3.col-xs-12.quick-stats-invoices {
	padding: 0px 2px 0px 2px;
	}
	.panel_s{
	margin-bottom:5px !important;
	}
	.labeltxt{
	font-size: 20px;
	color: #fff;
	text-align: center;
	margin:0px;
	}
	.title{
	font-size: 13px;
	color: #fff;
	text-align: center;
	margin:0px;
	}
	.mtop5 {
	margin-top: 1px;
	margin-bottom: 1px;
	}
	.bg1{
	background-image: linear-gradient(to right,#008385 0,#008385 100%);
	background-repeat: repeat-x;
	}
	.bg2{
	background-image: linear-gradient(to right,#FF425C 0,#FF425C 100%);
	background-repeat: repeat-x;
	}
	.top_stats_wrapper{
	margin-top: 0px;
	border-radius: 5px;
	padding:0px !important;
	margin-bottom: 5px !important;
	}
	.top_stats_wrapper:hover{
	box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.4);
	}
	.SpinnerCSS {
	text-align: center;
	padding: 20px;
	color: #666;
	}
</style>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/heatmap.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>


<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/themes/adaptive.js"></script>
<?php init_tail(); ?>
<script type="text/javascript">
	
	function generateChartData(data) {
		const firstWeekday = new Date(data[0].date).getDay(),
		monthLength = data.length,
		lastElement = data[monthLength - 1].date,
		lastWeekday = new Date(lastElement).getDay(),
		lengthOfWeek = 6,
		emptyTilesFirst = firstWeekday,
		chartData = [];
		
		for (let emptyDay = 0; emptyDay < emptyTilesFirst; emptyDay++) {
			chartData.push({
				x: emptyDay,
				y: 5,
				value: null,
				date: null,
				custom: {
					empty: true
				}
			});
		}
		
		for (let day = 1; day <= monthLength; day++) {
			const date = data[day - 1].date;
			const xCoordinate = (emptyTilesFirst + day - 1) % 7;
			const yCoordinate = Math.floor((firstWeekday + day - 1) / 7);
			const id = day;
			const temperature = data[day - 1].temperature;
			
			chartData.push({
				x: xCoordinate,
				y: 5 - yCoordinate,
				value: temperature,
				date: new Date(date).getTime(),
				custom: {
					monthDay: id
				}
			});
		}
		
		return chartData;
	}
	
</script> 
<script type="text/javascript" language="javascript">
	
	$(document).ready(function(){
		
		function GetCountersValue(from_date, to_date) {
			$.ajax({				 
				url: '<?php echo admin_url('accounting/GetDashboardCounters'); ?>',
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date, 
				},
				beforeSend: function () {
					$('.JournalSpinner').show();
					$('#JournalEntryAmt').hide();
					$('.ContraSpinner').show();
					$('#ContraEntryAmt').hide();
					$('.ReceiptSpinner').show();
					$('#ReceiptEntryAmt').hide();
					$('.PaymentSpinner').show();
					$('#PaymentEntryAmt').hide();
				},
				success: function(returndata) {
					var JournalEntryAmt = returndata.JournalEntryAmt || "0";
					var ContraEntryAmt = returndata.ContraEntryAmt || "0";
					var ReceiptEntryAmt = returndata.ReceiptEntryAmt || "0";
					var PaymentEntryAmt = returndata.PaymentEntryAmt || "0";
					
					$("#JournalEntryAmt").html(JournalEntryAmt);
					$("#ContraEntryAmt").html(ContraEntryAmt);
					$("#ReceiptEntryAmt").html(ReceiptEntryAmt);
					$("#PaymentEntryAmt").html(PaymentEntryAmt);
					
					$('.JournalSpinner').hide();
					$('#JournalEntryAmt').show();
					$('.ContraSpinner').hide();
					$('#ContraEntryAmt').show();
					$('.ReceiptSpinner').hide();
					$('#ReceiptEntryAmt').show();
					$('.PaymentSpinner').hide();
					$('#PaymentEntryAmt').show();
				},
				error: function(xhr, status, error) {
					console.error("Error fetching counters:", error);
					$("#JournalEntryAmt").html("0");
					$("#ContraEntryAmt").html("0");
					$("#ReceiptEntryAmt").html("0");
					$("#PaymentEntryAmt").html("0");
					
					$('.JournalSpinner').hide();
					$('#JournalEntryAmt').show();
					$('.ContraSpinner').hide();
					$('#ContraEntryAmt').show();
					$('.ReceiptSpinner').hide();
					$('#ReceiptEntryAmt').show();
					$('.PaymentSpinner').hide();
					$('#PaymentEntryAmt').show();
				}
			});
		}
		function BillsReceivable(from_date,to_date,ChartType,MaxCount)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>sale_reports/GetTopBillsReceivableReport",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date,to_date:to_date,ChartType:ChartType,MaxCount:MaxCount},
				beforeSend: function () {
					$('.BillsReceivableSpinner').show();
				},
				complete: function () {
					$('.BillsReceivableSpinner').hide();
				},
				success:function(returndata){
					
					Highcharts.chart('BillsReceivable', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Top Bills Receivable '+from_date+' To '+to_date+'</b><br><a style="color:#008ece;" href="<?= admin_url('Sale_reports/BillsReceivableReport')?>" target="_blank">Click To Get Detailed Report </a>',
							style: {
								fontSize: '12px'  // ⬅️ Increased font size
							}
						},
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
								style: {
									fontSize: '12px'  // ⬅️ Increased font size
								}
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Amount',
								style: {
									fontSize: '12px'  // ⬅️ Increased font size
								}
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Amount : <b>{point.y:.1f} </b>',
							style: {
								fontSize: '12px'  // ⬅️ Increased font size
							}
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: returndata.ChartData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								style: {
									fontSize: '12px'  // ⬅️ Increased font size
								},
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								
							}
						}]
					});
					
				}
			});
		}
		function BillsReceivableAgeing(from_date,to_date,ReportType)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>sale_reports/GetBillsReceivableReportDaywiseChart",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{from_date:from_date, to_date:to_date,ReportType:ReportType},
				beforeSend: function () {
					$('.BillsReceivableAgeingSpinner').show();
				},
				complete: function () {
					$('.BillsReceivableAgeingSpinner').hide();
				},
				success:function(data){
					Highcharts.chart('BillsReceivableAgeing', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Bills Receivable '+ReportType+' From '+from_date+' To '+to_date+'</b>',
							style: {
								fontSize: '12px'  // ⬅️ Increased font size
							}
						},
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Amount'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Amount : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: data,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								
							}
						}]
					});
				}
			});
		}
		function BillsPayable(from_date,to_date,ReportType,DueOn)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/GetBillsPayableReportChart",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{from_date:from_date, to_date:to_date,ReportType:ReportType,DueOn:DueOn},
				beforeSend: function () {
					$('.BillsPayableSpinner').show();
				},
				complete: function () {
					$('.BillsPayableSpinner').hide();
				},
				success:function(data){
					Highcharts.chart('BillsPayable', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Top Bills Payable '+from_date+' To '+to_date+'</b><br><a style="color:#008ece;" href="<?= admin_url('purchase/BillsPayableReport')?>" target="_blank">Click To Get Detailed Report </a>',
							style: {
								fontSize: '12px'  // ⬅️ Increased font size
							}
						},
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Amount'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Amount : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: data,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								
							}
						}]
					});
				}
			});
		}
		function BillsPayableAgeing(from_date,to_date,ReportType,DueOn)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/GetBillsPayableReportDaywiseChart",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{from_date:from_date, to_date:to_date,ReportType:ReportType,DueOn:DueOn},
				beforeSend: function () {
					$('.BillsPayableAgeingSpinner').show();
				},
				complete: function () {
					$('.BillsPayableAgeingSpinner').hide();
				},
				success:function(data){
					Highcharts.chart('BillsPayableAgeing', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: ''
						},
						subtitle: {
							text: '<b>Bills Payable '+ReportType+' From '+from_date+' To '+to_date+'</b>',
							style: {
								fontSize: '12px'  // ⬅️ Increased font size
							}
						},
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Amount'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Amount : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: data,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								
							}
						}]
					});
				}
			});
		}
		
		function MonthWiseCreditOutAmt() {
			$.ajax({
				url: '<?php echo admin_url('accounting/get_monthly_payable_amounts'); ?>',
				dataType: "JSON",
				method: "POST",
				data: { },
				beforeSend: function () {
					$('.MonthWiseCreditOutAmtSpinner').show();
					$('.MonthWiseCrdFigure').hide();
				},
				complete: function () {
					$('.MonthWiseCreditOutAmtSpinner').hide();
					$('.MonthWiseCrdFigure').show();
				},
				success: function (returndata) {
					
					if (!returndata || !returndata.Creditors_Months || returndata.Creditors_Months.length === 0) {
						$('#MonthWiseCreditOutAmt').html('<div style="text-align:center;padding:50px;color:#666">No invoice data available for the selected period.</div>');
						return;
					}
					
					Highcharts.chart('MonthWiseCreditOutAmt', {
						chart: {
							type: 'line',
							height: 300,
						},
						title: {
							text: 'Creditors Outstanding By Month',
							style: {
								fontSize: '16px'
							}
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: returndata.Creditors_Months,
							title: {
								text: 'Months',
								style: {
									fontSize: '12px'
								}
							},
							labels: {
								style: {
									fontSize: '11px'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Creditors Outstanding(₹)',
								style: {
									fontSize: '12px'
								}
							},
							labels: {
								formatter: function() {
									return '₹' + Highcharts.numberFormat(this.value, 0);
								},
								style: {
									fontSize: '11px'
								}
							}
						},
						tooltip: {
							pointFormat: '{series.name}: <b>₹{point.y:,.2f}</b>',
							style: {
								fontSize: '12px'
							}
						},
						plotOptions: {
							line: {
								dataLabels: {
									enabled: true,
									formatter: function() {
										return '₹' + Highcharts.numberFormat(this.y, 0);
									},
									style: {
										fontSize: '10px',
										fontWeight: 'bold'
									}
								},
								enableMouseTracking: true
							}
						},
						series: returndata.CreditOut_Amt
					});
				},
				error: function(xhr, status, error) {
					console.error("Error loading invoice data:", error);
					$('#MonthWiseCreditOutAmt').html('<div style="text-align:center;padding:50px;color:red">Error loading invoice data. Please try again.</div>');
				}
			});
		} 
		
		function MonthWiseDebtAmt() {
			$.ajax({
				url: '<?php echo admin_url('accounting/getMonthly_Due_amounts'); ?>',
				dataType: "JSON",
				method: "POST",
				data: { },
				beforeSend: function () {
					$('.MonthWiseDebtAmtSpinner').show();
					$('.MonthWiseDebtFigure').hide();
				},
				complete: function () {
					$('.MonthWiseDebtAmtSpinner').hide();
					$('.MonthWiseDebtFigure').show();
				},
				success: function (returndata) {
					
					if (!returndata || !returndata.Debtors_Months || returndata.Debtors_Months.length === 0) {
						$('#MonthWiseDebtAmt').html('<div style="text-align:center;padding:50px;color:#666">No invoice data available for the selected period.</div>');
						return;
					}
					
					Highcharts.chart('MonthWiseDebtAmt', {
						chart: {
							type: 'line',
							height: 300,
						},
						title: {
							text: 'Debtors Outstanding By Month',
							style: {
								fontSize: '16px'
							}
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: returndata.Debtors_Months,
							title: {
								text: 'Months',
								style: {
									fontSize: '12px'
								}
							},
							labels: {
								style: {
									fontSize: '11px'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Debtors Outstanding(₹)',
								style: {
									fontSize: '12px'
								}
							},
							labels: {
								formatter: function() {
									return '₹' + Highcharts.numberFormat(this.value, 0);
								},
								style: {
									fontSize: '11px'
								}
							}
						},
						tooltip: {
							pointFormat: '{series.name}: <b>₹{point.y:,.2f}</b>',
							style: {
								fontSize: '12px'
							}
						},
						plotOptions: {
							line: {
								dataLabels: {
									enabled: true,
									formatter: function() {
										return '₹' + Highcharts.numberFormat(this.y, 0);
									},
									style: {
										fontSize: '10px',
										fontWeight: 'bold'
									}
								},
								enableMouseTracking: true
							}
						},
						series: returndata.DebtOut_Amt
					});
				},
				error: function(xhr, status, error) {
					console.error("Error loading invoice data:", error);
					$('#MonthWiseDebtAmt').html('<div style="text-align:center;padding:50px;color:red">Error loading invoice data. Please try again.</div>');
				}
			});
		} 
		
		
		function MonthWiseClosingBal() {
			$.ajax({
				url: '<?php echo admin_url('accounting/getMonthWiseClosingBalance'); ?>',
				dataType: "JSON",
				method: "POST",
				data: { },
				beforeSend: function () {
					$('.MonthWiseClosingAmtSpinner').show();
					$('.MonthWiseClosingBalFigure').hide();
				},
				complete: function () {
					$('.MonthWiseClosingAmtSpinner').hide();
					$('.MonthWiseClosingBalFigure').show();
				},
				success: function (returndata) {
					if (!returndata || returndata.length === 0) {
						$('#MonthWiseClosingBal').html('<div style="text-align:center;padding:50px;color:#666">No cash closing balance data available for the selected period.</div>');
						return;
					}
					
					// Prepare the data for Highcharts
					var series = [];
					var categories = [];
					
					// The returndata is an array of accounts, each account has a name and an array of data (month and balance)
					// We need to extract the months from the first account's data for the categories
					if (returndata.length > 0) {
						categories = returndata[0].data.map(function(item) {
							return item.month;
						});
					}
					
					// Now, for each account, we want to create a series
					returndata.forEach(function(account) {
						var accountData = account.data.map(function(item) {
							return parseFloat(item.balance);
						});
						
						series.push({
							name: account.name,
							data: accountData,
							type: 'line'
						});
					});
					
					// Now, create the chart
					Highcharts.chart('MonthWiseClosingBal', {
						chart: {
							type: 'line',
							height: 300,
						},
						title: {
							text: 'Cash and Cash Equivalents - Monthly Closing Balance',
							style: {
								fontSize: '16px'
							}
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: categories,
							title: {
								text: 'Months',
								style: {
									fontSize: '12px'
								}
							},
							labels: {
								style: {
									fontSize: '11px'
								}
							}
						},
						yAxis: {
							title: {
								text: 'Closing Balance (₹)',
								style: {
									fontSize: '12px'
								}
							},
							labels: {
								formatter: function() {
									return '₹' + Highcharts.numberFormat(this.value, 0);
								},
								style: {
									fontSize: '11px'
								}
							}
						},
						tooltip: {
							shared: true,
							crosshairs: true,
							formatter: function() {
								var tooltip = '<b>' + this.x + '</b>';
								this.points.forEach(function(point) {
									tooltip += '<br/>' + point.series.name + ': ' + 
									'₹' + Highcharts.numberFormat(point.y, 2);
								});
								return tooltip;
							},
							style: {
								fontSize: '12px'
							}
						},
						plotOptions: {
							line: {
								dataLabels: {
									enabled: true,
									formatter: function() {
										return '₹' + Highcharts.numberFormat(this.y, 0);
									},
									style: {
										fontSize: '10px',
										fontWeight: 'bold'
									}
								},
								enableMouseTracking: true
							}
						},
						series: series,
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle',
							itemStyle: {
								fontSize: '11px'
							}
						},
						responsive: {
							rules: [{
								condition: {
									maxWidth: 500
								},
								chartOptions: {
									legend: {
										layout: 'horizontal',
										align: 'center',
										verticalAlign: 'bottom'
									}
								}
							}]
						}
					});
				},
				error: function(xhr, status, error) {
					console.error("Error loading cash closing balance data:", error);
					$('#MonthWiseClosingBal').html('<div style="text-align:center;padding:50px;color:red">Error loading cash closing balance data. Please try again.</div>');
				}
			});
		}
		
		
		function MonthWiseSalePurchase() {
			$.ajax({
				url: '<?php echo admin_url('accounting/getMonthly_Sale_Purch'); ?>',
				dataType: "JSON",
				method: "POST",
				data: { },
				beforeSend: function () {
					$('.MonthWiseSalePurchSpinner').show();
					$('#MonthlySalePurchaseFigure').hide();
				},
				complete: function () {
					$('.MonthWiseSalePurchSpinner').hide();
					$('#MonthlySalePurchaseFigure').show();
				},
				success: function (returndata) {
					if (!returndata || returndata.months.length === 0) {
						$('#MonthWiseSalePurchase').html('<div style="text-align:center;padding:50px;color:#666">No sale/purchase data available for the selected period.</div>');
						return;
					}
					
					Highcharts.chart('MonthWiseSalePurchase', {
						chart: { type: 'line', height: 300 },
						title: { text: 'Monthly Sales vs Purchase', style: { fontSize: '16px' } },
						xAxis: {
							categories: returndata.months,
							title: { text: 'Months' }
						},
						yAxis: {
							title: { text: 'Amount (₹)' },
							labels: {
								formatter: function () { return '₹' + Highcharts.numberFormat(this.value, 0); }
							}
						},
						tooltip: {
							shared: true,
							crosshairs: true,
							formatter: function () {
								var tooltip = '<b>' + this.x + '</b>';
								this.points.forEach(function (point) {
									tooltip += '<br/>' + point.series.name + ': ₹' + Highcharts.numberFormat(point.y, 2);
								});
								return tooltip;
							}
						},
						series: returndata.series,
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle'
						},
						responsive: {
							rules: [{
								condition: { maxWidth: 500 },
								chartOptions: {
									legend: {
										layout: 'horizontal',
										align: 'center',
										verticalAlign: 'bottom'
									}
								}
							}]
						}
					});
				},
				error: function (xhr, status, error) {
					console.error("Error loading monthly sale/purchase data:", error);
					$('#MonthWiseSalePurchase').html('<div style="text-align:center;padding:50px;color:red">Error loading data. Please try again.</div>');
				}
			});
		}
		function Expenses(from_date,to_date) {
			$.ajax({
				url: '<?php echo admin_url('accounting/getExpensesChart'); ?>',
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{from_date:from_date, to_date:to_date},
				beforeSend: function () {
					$('.ExpensesSpinner').show();
					$('.ExpensesFigure').hide();
				},
				complete: function () {
					$('.ExpensesSpinner').hide();
					$('.ExpensesFigure').show();
				},
				success: function (response) {
					Highcharts.chart('Expenses', {
						chart: {
							type: 'pie', height: 300
						},
						
						title: {
							text: 'Expense Distribution (%) From '+from_date+' To '+to_date
						},
						
						subtitle: {
							text: 'Main Group → Sub-Group1 → Sub-Group2 → Account'
						},
						
						plotOptions: {
							pie: {
								borderRadius: 5,
								dataLabels: [{
									enabled: true,
									format: '{point.name}'
									}, {
									enabled: true,
									distance: '-30%',
									format: '{point.percentage:.1f}%',
									filter: {
										property: 'percentage',
										operator: '>',
										value: 3
									}
								}]
							}
						},
						
						tooltip: {
							headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
							pointFormat:
							'<span style="color:{point.color}">{point.name}</span><br>' +
							'<b>₹ {point.y:,.2f}</b><br>' +
							'<b>{point.percentage:.2f}%</b>'
						},
						
						series: response.series,
						
						drilldown: {
							breadcrumbs: {
								position: { align: 'right' }
							},
							series: response.drilldown
						}
					});
					
				},
				error: function (xhr, status, error) {
					console.error("Error loading :", error);
					$('#Expenses').html('<div style="text-align:center;padding:50px;color:red">Error loading data. Please try again.</div>');
				}
			});
		}
		
		
		
		
		
		
		
		function load_ReceiptsCollectionChart(Month) {
			$.ajax({				 
				url: '<?php echo admin_url('accounting/GetCalenderMonthlyDueData'); ?>',
				dataType: "JSON",
				method: "POST",
				data: { Month: Month },
				beforeSend: function () {
					$('#container_ReceiptsCollectioncalander').html('<div style="text-align:center;padding:50px;">Loading collection amounts calendar...</div>');
				},
				success: function(returndata) {
					if (!returndata || returndata.length === 0) {
						$('#container_ReceiptsCollectioncalander').html('<div style="text-align:center;padding:50px;color:#666">No collection data available for the selected period.</div>');
						return;
					}
					
					const data = returndata;
					const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
					const chartData = generateChartData(data);
					
					if (chartData.length === 0) {
						$('#container_ReceiptsCollectioncalander').html('<div style="text-align:center;padding:50px;color:#666">No calendar data to display.</div>');
						return;
					}
					
					Highcharts.chart('container_ReceiptsCollectioncalander', {
						chart: {
							type: 'heatmap',
							height: 367,
						},
						title: {
							text: 'Day Wise Receipt Amount - ' + Month,
							align: 'left'
						},
						accessibility: {
							landmarkVerbosity: 'one'
						},
						tooltip: {
							enabled: true,
							outside: true,
							zIndex: 20,
							headerFormat: '',
							pointFormat: '{#unless point.custom.empty}<b>Collection Date: {point.date:%A, %b %e, %Y}</b><br/>Collection Amount: ₹ {point.value:.2f}{/unless}',
							nullFormat: 'No collection'
						},
						xAxis: {
							categories: weekdays,
							opposite: true,
							lineWidth: 26,
							offset: 13,
							lineColor: 'rgba(27, 26, 37, 0.2)',
							labels: {
								rotation: 0,
								y: 20,
								style: {
									textTransform: 'uppercase',
									fontWeight: 'bold'
								}
							},
							accessibility: {
								description: 'weekdays',
								rangeDescription: 'X Axis is showing all 7 days of the week, starting with Sunday.'
							}
						},
						yAxis: {
							min: 0,
							max: 5,
							accessibility: {
								description: 'weeks'
							},
							visible: false
						},
						legend: {
							align: 'right',
							layout: 'vertical',
							verticalAlign: 'middle',
							title: {
								text: 'Collection Amount (₹)'
							}
						},
						colorAxis: {
							min: 0,
							minColor: '#FFFFFF',
							maxColor: Highcharts.getOptions().colors[0],
							stops: [
							[0, '#FFFFFF'],
							[0.3, '#CBDFC8'],
							[0.6, '#F3E99E'],
							[0.9, '#F9A05C'],
							[1, '#FF425C']
							],
							labels: {
								format: '₹{value}',
								style: {
									color: '#000000'
								}
							}
						},
						series: [{
							keys: ['x', 'y', 'value', 'date', 'id'],
							data: chartData,
							nullColor: 'rgba(196, 196, 196, 0.2)',
							borderWidth: 2,
							borderColor: 'rgba(196, 196, 196, 0.2)',
							dataLabels: [{
								enabled: true,
								format: '{#unless point.custom.empty}₹{point.value:.0f}{/unless}',
								style: {
									textOutline: 'none',
									fontWeight: 'normal',
									fontSize: '0.8rem',
									color: '#000000'
								},
								y: 4
								}, {
								enabled: true,
								align: 'left',
								verticalAlign: 'top',
								format: '{#unless point.custom.empty}{point.custom.monthDay}{/unless}',
								backgroundColor: 'rgba(255,255,255,0.7)',
								padding: 2,
								style: {
									textOutline: 'none',
									color: 'rgba(70, 70, 92, 1)',
									fontSize: '0.7rem',
									fontWeight: 'bold',
								},
								x: 1,
								y: 1
							}]
						}]
					});
				},
				error: function(xhr, status, error) {
					console.error("AJAX Error:", error);
					$('#container_ReceiptsCollectioncalander').html('<div style="text-align:center;padding:50px;color:red">Error loading collection data. Please check console for details.</div>');
				}
			});
		}
		
		function load_PaymentsCollectionChart(Month) {
			$.ajax({				 
				url: '<?php echo admin_url('accounting/GetCalenderMonthlyPaymentData'); ?>',
				dataType: "JSON",
				method: "POST",
				data: {	Month: Month},
				beforeSend: function () {
					$('#container_PaymentCollectioncalander').html('<div style="text-align:center;padding:50px;">Loading payment amounts calendar...</div>');
				},
				success: function(returndata) {
					if (!returndata || returndata.length === 0) {
						$('#container_PaymentCollectioncalander').html('<div style="text-align:center;padding:50px;color:#666">No payment data available for the selected period.</div>');
						return;
					}
					
					const data = returndata;
					const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
					const chartData = generateChartData(data);
					
					if (chartData.length === 0) {
						$('#container_PaymentCollectioncalander').html('<div style="text-align:center;padding:50px;color:#666">No calendar data to display.</div>');
						return;
					}
					
					Highcharts.chart('container_PaymentCollectioncalander', {
						chart: {
							type: 'heatmap',
							height: 367,
						},
						title: {
							text: 'Day Wise Payment Amount - ' + Month,
							align: 'left'
						},
						accessibility: {
							landmarkVerbosity: 'one'
						},
						tooltip: {
							enabled: true,
							outside: true,
							zIndex: 20,
							headerFormat: '',
							pointFormat: '{#unless point.custom.empty}<b>Payment Date: {point.date:%A, %b %e, %Y}</b><br/>Payment Amount: ₹ {point.value:.2f}{/unless}',
							nullFormat: 'No payment'
						},
						xAxis: {
							categories: weekdays,
							opposite: true,
							lineWidth: 26,
							offset: 13,
							lineColor: 'rgba(27, 26, 37, 0.2)',
							labels: {
								rotation: 0,
								y: 20,
								style: {
									textTransform: 'uppercase',
									fontWeight: 'bold'
								}
							},
							accessibility: {
								description: 'weekdays',
								rangeDescription: 'X Axis is showing all 7 days of the week, starting with Sunday.'
							}
						},
						yAxis: {
							min: 0,
							max: 5,
							accessibility: {
								description: 'weeks'
							},
							visible: false
						},
						legend: {
							align: 'right',
							layout: 'vertical',
							verticalAlign: 'middle',
							title: {
								text: 'Payment Amount (₹)'
							}
						},
						colorAxis: {
							min: 0,
							minColor: '#FFFFFF',
							maxColor: Highcharts.getOptions().colors[2],
							stops: [
							[0, '#FFFFFF'],
							[0.3, '#D4E6F1'],
							[0.6, '#7FB3D5'],
							[0.9, '#3498DB'],
							[1, '#2E86C1']
							],
							labels: {
								format: '₹{value}',
								style: {
									color: '#000000'
								}
							}
						},
						series: [{
							keys: ['x', 'y', 'value', 'date', 'id'],
							data: chartData,
							nullColor: 'rgba(196, 196, 196, 0.2)',
							borderWidth: 2,
							borderColor: 'rgba(196, 196, 196, 0.2)',
							dataLabels: [{
								enabled: true,
								format: '{#unless point.custom.empty}₹{point.value:.0f}{/unless}',
								style: {
									textOutline: 'none',
									fontWeight: 'normal',
									fontSize: '0.8rem',
									color: '#000000'
								},
								y: 4
								}, {
								enabled: true,
								align: 'left',
								verticalAlign: 'top',
								format: '{#unless point.custom.empty}{point.custom.monthDay}{/unless}',
								backgroundColor: 'rgba(255,255,255,0.7)',
								padding: 2,
								style: {
									textOutline: 'none',
									color: 'rgba(70, 70, 92, 1)',
									fontSize: '0.7rem',
									fontWeight: 'bold',
								},
								x: 1,
								y: 1
							}]
						}]
					});
				},
				error: function(xhr, status, error) {
					console.error("AJAX Error:", error);
					$('#container_PaymentCollectioncalander').html('<div style="text-align:center;padding:50px;color:red">Error loading payment data. Please check console for details.</div>');
				}
			});
		}
		
		function ReceiptPayment(from_date, to_date) {
			$.ajax({
				url: "<?php echo admin_url(); ?>accounting/GetReceiptPayment",
				dataType: "JSON",
				method: "POST",
				data: {
					from_date: from_date,
					to_date: to_date,
				},
				beforeSend: function () {
					$('.ReceiptPaymentSpinner').show();
					$('.ReceiptPaymentFigure').hide();
				},
				complete: function () {
					$('.ReceiptPaymentSpinner').hide();
					$('.ReceiptPaymentFigure').show();
				},
				success: function (returndata) {
					// Assuming returndata.months is an array like ["Apr-2025", "May-2025", ..., "Mar-2026"]
					// And returndata.series is the series data accordingly
					
					Highcharts.chart('ReceiptPayment', {
						chart: {
							type: 'line',
							height: 253,
						},
						title: {
							text: 'Receipt Payment Records'
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories: returndata.Dates // Set dynamically from server
						},
						yAxis: {
							title: {
								text: 'Amount'
							}
						},
						plotOptions: {
							line: {
								dataLabels: {
									enabled: true
								},
								enableMouseTracking: false
							}
						},
						series: returndata.TotalReceiptPayment
					});
				}
			});
		}
		
		function CashAndEquivalant()
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>accounting/GetCashAndEquivalant",
				dataType:"JSON",
				method:"POST",
				beforeSend: function () {
					$('.CashAndEquivalantSpinner').show();
					$('.CashAndEquivalantFigure').hide();
				},
				complete: function () {
					$('.CashAndEquivalantSpinner').hide();
					$('.CashAndEquivalantFigure').show();
				},
				success:function(returndata){
					Highcharts.chart('CashAndEquivalant', {
						chart: {
							type: 'column',
							height: 300,
						},
						title: {
							text: '<b>Cash and Cash Equivalents - Closing Balance</b>'
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							type: 'category',
							labels: {
								autoRotation: [-45, -90],
							}
						},
						yAxis: {
							min: null,
							title: {
								text: 'Total'
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: 'Total : <b>{point.y:.1f} </b>'
						},
						series: [{
							name: 'Population',
							colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
							colorByPoint: true,
							groupPadding: 0,
							data: returndata.RtnData,
							dataLabels: {
								enabled: true,
								rotation: -90,
								color: '#FFFFFF',
								inside: true,
								verticalAlign: 'top',
								format: '{point.y:.1f}', // one decimal
								y: 10, // 10 pixels down from the top
								
							}
						}]
					});
					
				}
			});
		}
		
		$('#search_data').on('click', function(){
			var from_date = $("#from_date2").val();
			var to_date = $("#to_date2").val();		 
			
			GetCountersValue(from_date, to_date);	
			MonthWiseCreditOutAmt(from_date, to_date);  // Creditors Outstanding.
			MonthWiseDebtAmt(from_date, to_date);       //Debtors Outstanding.
			BillsReceivable(from_date,to_date,'Bar','15');
			BillsPayable(from_date,to_date,'Overdue','DESC');
			BillsPayableAgeing(from_date,to_date,'Overdue','DESC');
			BillsReceivableAgeing(from_date,to_date,'Overdue');
			Expenses(from_date,to_date);
			MonthWiseClosingBal(); 
			MonthWiseSalePurchase(); 
			CashAndEquivalant(); 
			ReceiptPayment(from_date, to_date);
			
		});
		
		$('#payDuemonth').on('change', function() {		 
			var Month = $(this).val();
			load_ReceiptsCollectionChart(Month);
		});
		
		$('#payCalmonth').on('change', function() {		 
			var Month = $(this).val();
			load_PaymentsCollectionChart(Month);
		});
		
		// Initial load  
		var initial_from_date = $("#from_date2").val();
		var initial_to_date = $("#to_date2").val();
		var Duemonth = $("#payDuemonth").val();		  
		var Calmonth = $("#payCalmonth").val();		 
		
		load_ReceiptsCollectionChart(Duemonth);
		load_PaymentsCollectionChart(Calmonth);
		
		GetCountersValue(initial_from_date, initial_to_date);
		MonthWiseCreditOutAmt(initial_from_date, initial_to_date);  // Creditors Outstanding.
		MonthWiseDebtAmt(initial_from_date, initial_to_date);       //Debtors Outstanding.
		
		BillsReceivable(initial_from_date,initial_to_date,'Bar','15');
		BillsPayable(initial_from_date,initial_to_date,'Overdue','DESC');
		BillsPayableAgeing(initial_from_date,initial_to_date,'Overdue','DESC');
		BillsReceivableAgeing(initial_from_date,initial_to_date,'Overdue');
		Expenses(initial_from_date,initial_to_date);
		MonthWiseClosingBal(); 
		MonthWiseSalePurchase(); 
		CashAndEquivalant(); 
		ReceiptPayment(initial_from_date, initial_to_date);
		
		
		
	});
</script>