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
            					<li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Vehicle Return Report</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
						<div class="_buttons">
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
											$from_date = date('01/m/Y');
											$to_date = date('d/m/Y');
										}
									?>
									<?php 
										$current_date = date('d/m/Y');
										echo render_date_input('from_date','From Date',$from_date);          
									?>
								</div>
								<div class="col-md-2">
									<?php 
										echo render_date_input('to_date','To Date',$to_date);          
									?>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Is Vehicle Return ?</label>
										<select name="IsReturn" id="IsReturn" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
											<option value="Y">Yes</option>
											<option value="N">No</option>
											<option value="">All</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Mileage Detail Status</label>
										<select name="IsMileage" id="IsMileage" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
											<option value="">All</option>
											<option value="Y">Yes</option>
											<option value="N">No</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Crate Detail Status</label>
										<select name="IsCrate" id="IsCrate" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
											<option value="">All</option>
											<option value="Y">Yes</option>
											<option value="N">No</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Payment Detail Status</label>
										<select name="IsPayment" id="IsPayment" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
											<option value="">All</option>
											<option value="Y">Yes</option>
											<option value="N">No</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Other Exp. Detail Status</label>
										<select name="IsExpenses" id="IsExpenses" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
											<option value="">All</option>
											<option value="Y">Yes</option>
											<option value="N">No</option>
										</select>
									</div>
								</div>
								
								<div class="col-md-7" style="margin-top:10px;">
									<br>
									
									<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
								</div>
							</div>
							
						</div>
						<div class="clearfix"></div>
						<br>
						<div class="row">
							<div class="col-md-9">
								<div class="custom_button">
									<a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 10px;"  tabindex="0" aria-controls="table-purchase_request" href="#" id="caexcel"><span>Export to excel</span></a>
									<a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 10px;margin-left:10px;"  onclick="printPage();">Print</a>
								</div>
							</div>
							
							<div class="col-md-3">
								<input type="text" id="myInput1" onkeyup="myFunction2()" class="form-control" placeholder="Search for names.." title="Type in a name" style="float: right;">
							</div>
							<div class="col-md-12">
								<span id="searchh3" style="display:none;">please wait exporting data....</span>
							</div>
						</div>
						
						<?php
							//print_r($company_detail);
						?>
						<div class="table-daily_report">
							
							<table class="tree table table-striped table-bordered table-daily_report" id="table-daily_report" width="100%">
								
								<thead>
									<tr>
										<th class="sortable"  style="text-align:left;">Sr. No.</th>
										<th class="sortable" data-column="ChallanID" style="text-align:left;">Challan ID </th>
										<th class="sortable" data-column="ChallanDate" style="text-align:left;">Challan Date </th>
										<th class="sortable" data-column="RtnID" style="text-align:left;">Return ID</th>
										<th class="sortable" data-column="RtnDate" style="text-align:left;">Return Date</th>
										<th class="sortable" data-column="VehType" style="text-align:left;">Vehicle Type</th>
										<th class="sortable" data-column="VehNo" style="text-align:left;">Vehicle No</th>
										<th class="sortable" data-column="Driver" style="text-align:left;">Driver Name</th>
										<th class="sortable" data-column="Crate" style="text-align:left;">Crates</th>
										<th class="sortable" data-column="Cases" style="text-align:left;">Cases</th>
										<th class="sortable" data-column="MileageDetail" style="text-align:left;">Mileage Detail</th>
										<th class="sortable" data-column="CrateDetail" style="text-align:left;">Crate Detail</th>
										<th class="sortable" data-column="PaymentDetail" style="text-align:left;">Payment Detail</th>
										<th class="sortable" data-column="ExpenseDetail" style="text-align:left;">Expense Detail</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>   
						</div>
						<span id="searchh2" style="display:none;">Loading.....</span>
						<span id="searchh11" style="display:none;">please wait exporting data....</span>
						
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
    .table-daily_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
	.table-daily_report tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.table-daily_report table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	.table-daily_report th     { background: #50607b;color: #fff !important; }
</style>


<?php init_tail(); ?>
<!--new update -->
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
<script>
    function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table-daily_report");
		tbody = table.getElementsByTagName("tbody")[0]; // Get the first tbody element
		tr = tbody.getElementsByTagName("tr"); 
		for (i = 0; i < tr.length; i++) 
        {
            tr[i].style.display = "none"; 
            td = tr[i].getElementsByTagName("td"); 
			
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;                
                    if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
                        tr[i].style.display = "";  
                        break; 
					}
				}
			}
		}
	}
	
</script>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		
		function load_data(from_date,to_date,IsReturn,IsMileage,IsCrate,IsPayment,IsExpenses)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/LoadPendingVehicleRetuernList",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,IsReturn:IsReturn,IsMileage:IsMileage,IsCrate:IsCrate,IsPayment:IsPayment,IsExpenses:IsExpenses},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table-daily_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table-daily_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					$('tbody').html(data);
				}
			});
		}
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var IsReturn = $("#IsReturn").val();
			var IsMileage = $("#IsMileage").val();
			var IsCrate = $("#IsCrate").val();
			var IsPayment = $("#IsPayment").val();
			var IsExpenses = $("#IsExpenses").val();
			var msg = "Report "+from_date +" To " + to_date;
			$(".report_for").text(msg);
			load_data(from_date,to_date,IsReturn,IsMileage,IsCrate,IsPayment,IsExpenses);
			
		});
		
		
		
	});
	
	
	$("#caexcel").click(function(){
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		var IsReturn = $("#IsReturn").val();
		var IsMileage = $("#IsMileage").val();
		var IsCrate = $("#IsCrate").val();
		var IsPayment = $("#IsPayment").val();
		var IsExpenses = $("#IsExpenses").val();
		
		$.ajax({
			url:"<?php echo admin_url(); ?>VehRtn/export_PendingVehicleReturnReport",
			method:"POST",
			data:{from_date:from_date, to_date:to_date,IsReturn:IsReturn,IsMileage:IsMileage,IsCrate:IsCrate,IsPayment:IsPayment,IsExpenses:IsExpenses},
			beforeSend: function () {
				$('#searchh11').css('display','block');  
				
			},
			complete: function () {
				$('#searchh11').css('display','none');  
			},
			success:function(data){
				response = JSON.parse(data);
				window.location.href = response.site_url+response.filename;
			}
		});
	});
	
	
	
</script>
<script type="text/javascript">
	function printPage(){
        
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
		var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="9">Pending Vehicle Return Report From : '+from_date+' To '+to_date+'</td>';
		heading_data += '</tr>';
		heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
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
	
	
	$(document).on("click", ".sortable", function () {
		var table = $("#table-daily_report tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortable").removeClass("asc desc");
		$(".sortable span").remove();
		
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