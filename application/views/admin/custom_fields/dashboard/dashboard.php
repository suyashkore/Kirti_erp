<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<?php
	$fy = $this->session->userdata('finacial_year');
	$this->load->model('hr_profile/hr_profile_model');
	$this->load->model('Dashboard_model');
	$data_dash = $this->hr_profile_model->get_hr_profile_dashboard_data();
	
	$staff_departments_chart = json_encode($this->hr_profile_model->staff_chart_by_departments());
	$staff_chart_by_job_positions = json_encode($this->hr_profile_model->staff_chart_by_job_positions());
	$top_five_customers = json_encode($this->Dashboard_model->top_five_customer());
	$TopFiveVendor = json_encode($this->Dashboard_model->TopFiveVendor());
	$top_five_skus = json_encode($this->Dashboard_model->top_five_skus());
	$TopFivePurchaseItems = json_encode($this->Dashboard_model->TopFivePurchaseItems());
	$TopFivePurchaseItemsPM = json_encode($this->Dashboard_model->TopFivePurchaseItemsPM());
	$totalMonthlyPurchase = json_encode($this->Dashboard_model->totalMonthlyPurchase());
	$totalMonthlySale = json_encode($this->Dashboard_model->totalMonthlySale());
	// print_r($ProductionVsSale);die;
?>

<div id="wrapper">
    <div class="screen-options-area"></div>
    <div class="screen-options-btn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="tw-w-5 tw-h-5 ltr:tw-mr-1 rtl:tw-ml-1">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>

        <?= _l('dashboard_options'); ?>
    </div>
    <div class="content">
        <div class="row">
            <?php $this->load->view('admin/includes/alerts'); ?>

            <?php hooks()->do_action('before_start_render_dashboard_content'); ?>

            <div class="clearfix"></div>

            <div class="col-md-12 mtop20" data-container="top-12">
                <?php render_dashboard_widgets('top-12'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard_top_container'); ?>

            <div class="col-md-6" data-container="middle-left-6">
                <?php render_dashboard_widgets('middle-left-6'); ?>
            </div>
            <div class="col-md-6" data-container="middle-right-6">
                <?php render_dashboard_widgets('middle-right-6'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard_half_container'); ?>

            <div class="col-md-8" data-container="left-8">
                <?php render_dashboard_widgets('left-8'); ?>
            </div>
            <div class="col-md-4" data-container="right-4">
                <?php render_dashboard_widgets('right-4'); ?>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-4" data-container="bottom-left-4">
                <?php render_dashboard_widgets('bottom-left-4'); ?>
            </div>
            <div class="col-md-4" data-container="bottom-middle-4">
                <?php render_dashboard_widgets('bottom-middle-4'); ?>
            </div>
            <div class="col-md-4" data-container="bottom-right-4">
                <?php render_dashboard_widgets('bottom-right-4'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard'); ?>
        </div>
    </div>
</div>
<script>
    app.calendarIDs = '<?= json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
</body>

<script>
	$(document).ready(function(){
		
		chartOfDoubleBamboo('ProductionVsSales');
		PurchaseVsSales('PurchaseVsSales');
		staff_chart_by_age('top_five_skus',<?php echo html_entity_decode($top_five_skus); ?>, <?php echo json_encode('Top Five Selling Items (FY - ' . $fy . ')'); ?>,'Qty');
		staff_chart_by_age('TopFivePurchaseItems',<?php echo html_entity_decode($TopFivePurchaseItems); ?>, <?php echo json_encode('Top Five Purchase Items RM (FY - ' . $fy . ')'); ?>,'Qty');
		staff_chart_by_age('TopFivePurchaseItemsPM',<?php echo html_entity_decode($TopFivePurchaseItemsPM); ?>, <?php echo json_encode('Top Five Purchase Items PM (FY - ' . $fy . ')'); ?>,'Qty');
		staff_chart_by_age('totalMonthlyPurchase',<?php echo html_entity_decode($totalMonthlyPurchase); ?>, <?php echo json_encode('Month Wise Purchase (FY - ' . $fy . ')'); ?>,'Amount');
		staff_chart_by_age('totalMonthlySale',<?php echo html_entity_decode($totalMonthlySale); ?>, <?php echo json_encode('Month Wise Sale (FY - ' . $fy . ')'); ?>,'Amount');
		staff_chart_by_age('top_five_customers',<?php echo html_entity_decode($top_five_customers); ?>, <?php echo json_encode('Top Five Customers (FY - ' . $fy . ')'); ?>,'Amount');
		staff_chart_by_age('TopFiveVendorChart',<?php echo html_entity_decode($TopFiveVendor); ?>, <?php echo json_encode('Top Five Vendor (FY - ' . $fy . ')'); ?>,'Amount');
		
		
		staff_chart_by_age('staff_chart_by_job_positions',<?php echo html_entity_decode($staff_chart_by_job_positions); ?>, <?php echo json_encode('Staff Ratio By Designations'); ?>,'Staff');
		staff_chart_by_age('staff_departments_chart',<?php echo html_entity_decode($staff_departments_chart); ?>, <?php echo json_encode('Staff Ratio By Departments'); ?>,'Staff');
		
		function staff_chart_by_age(id, value, title_c,yAxis_title){
			Highcharts.setOptions({
				chart: {
					style: {
						fontFamily: 'inherit !important',
						fontWeight:'normal',
						fill: 'black'
					}
				},
				colors: [ '#119EFA','#ef370dc7','#15f34f','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B']
			});
			Highcharts.chart(id, {
				chart: {
					backgroundcolor: '#fcfcfc8a',
					type: 'column'
				},
				accessibility: {
					description: null
				},
				title: {
					text: title_c
				},
				credits: {
					enabled: false
				},
				tooltip: {
					pointFormat: '<span style="color:{series.color}">'+<?php echo json_encode('{point.label}'); ?>+'</span>: <b>{point.y}</b> <br/>',
					shared: true
				},
				legend: {
					enabled: false
				},
				xAxis: {
					type: 'category'
				},
				yAxis: {
					min: 0,
					title: {
						text: yAxis_title
					}
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						depth: 35,
						dataLabels: {
							enabled: true,
							format: '{point.name}'
						}        
					}
				},
				series: [{
					name: "",
					colorByPoint: true,
					data: value,
					
				}]
			});
		}
		
		
		function chartOfDoubleBamboo(id){
			var value = <?php echo html_entity_decode($ProductionVsSale); ?>;
			Highcharts.chart(id, {
				chart: {
					type: 'column'
				},
				title: {
					text: ''
				},
				subtitle: {
					text: '<b>PRODUCTION VS SALES (FY - <?= $fy?>)</b>'
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
						text: 'Qty In(Unit)'
					}
				},
				tooltip: {
					pointFormat: 'QTY : <b>{point.y:.1f} </b>'
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [
				{
					name: 'Production',
					data: value.Production,
				},
				{
					name: 'Sales',
					data: value.Sales,
				}
				]
			});
		}
		function PurchaseVsSales(id){
			var value = <?php echo html_entity_decode($PurchaseVsSale); ?>;
			Highcharts.chart(id, {
				chart: {
					type: 'column'
				},
				title: {
					text: ''
				},
				subtitle: {
					text: '<b>PURCHASE VS SALES (FY - <?= $fy?>)</b>'
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
						text: 'Qty In(Unit)'
					}
				},
				tooltip: {
					pointFormat: 'QTY : <b>{point.y:.1f} </b>'
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				series: [
				{
					name: 'Opening+Purchase',
					data: value.Purchase,
				},
				{
					name: 'Sales',
					data: value.Sales,
				},
				// {
					// name: 'Opening',
					// data: value.OpeningQty,
				// }
				]
			});
		}
		
	}); 
</script>

</html>