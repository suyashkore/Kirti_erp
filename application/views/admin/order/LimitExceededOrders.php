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
            					<li class="breadcrumb-item active" aria-current="page"><b>Limit Exceeded Orders</b></li>
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
										<label class="form-label"><small class="req text-danger"> </small> Party</label>
										<select class="selectpicker" name="AccountID" id="AccountID" data-width="100%" data-none-selected-text="None selected" data-live-search="true" >
										<option value="" >None selected</option>
											<?php
												foreach($PartyList as $each){
												?>
        										<option value="<?= $each['AccountID']?>" ><?= $each['company']?></option>
												<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="form-label"><small class="req text-danger"> </small> Status</label>
										<select class="selectpicker" name="Status" id="Status" data-width="100%" data-none-selected-text="None selected" data-live-search="true" >
											
											<option value="Pending" >Pending For Approval</option>
											<option value="Approved" >Approved Orders</option>
										</select>
									</div>
								</div>
								<div class="col-md-2" id="ApproverDiv" style="display:none">
									<div class="form-group">
										<label class="form-label"><small class="req text-danger"> </small> Approver</label>
										<select class="selectpicker" name="Approver" id="Approver" data-width="100%" data-none-selected-text="None selected" data-live-search="true" >
										<option value="" >None selected</option>
											<?php
												foreach($Staff as $each){
												?>
        										<option value="<?= $each['ID']?>" ><?= $each['label']?></option>
												<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-md-2" style="margin-top:10px;">
									<br>
									
									<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
								</div>
							</div>
							
						</div>
						<div class="clearfix"></div>
						
						<div class="row">
							
							<div class="col-md-7">
								<div class="custom_button">
									<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
									<?php
										if (has_permission_new('LimitExceededOrders', '', 'edit')) {
										?>
										<button type="button" id="submit_button" class="btn btn-primary">Approve Order</button>
									<?php } ?>
								</div>
							</div>
							<div class="col-md-5">
								<input type="text" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
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
										<th class="sortable" style="text-align:left;">SrNo</th>
										<th style="text-align:left;">Tag</th>
										<th class="sortable" style="text-align:left;">OrderID</th>
										<th class="sortable" style="text-align:left;">Order Date</th>
										<th class="sortable" style="text-align:left;">Bill To Party</th>
										<th class="sortable" style="text-align:left;">Ship To Party</th>
										<th class="sortable" style="text-align:left;">Order Amt</th>
										<th class="sortable" style="text-align:left;">Approved By</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>   
						</div>
						<span id="searchh2" style="display:none;">Loading.....</span>
						
						
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

 <script>
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
<!--new update -->

<script>
	$(document).on('click', '#submit_button', function (e) {
		e.preventDefault();
		
		// Get selected order IDs
		var selectedOrders = [];
		$('input[name="OrderID"]:checked').each(function () {
			selectedOrders.push($(this).val());
		});
		
		// Check if at least one checkbox is selected
		if (selectedOrders.length === 0) {
			alert('Please select at least one order.');
			return false;
		}
		
		
		// Send via AJAX
		$.ajax({
			url: "<?php echo admin_url(); ?>order/UpdateLimitExceededOrders",
			type: 'POST',
			data: {
				order_ids: selectedOrders,
			},
			success: function (response) {
				if (response) {
					alert_float('success','Updated Successfully');
					$('#search_data').click();
					}else{
					alert_float('warning','Somthing Went Wrong');
				}
			},
			error: function (xhr) {
				alert('Something went wrong.');
			}
		});
	});
	
    function myFunction2() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table-daily_report");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) 
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
		
		function load_data(from_date,to_date,Status,Approver,AccountID)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>order/GetLimitExceededOrders",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,Status:Status,Approver:Approver,AccountID:AccountID},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('.table-daily_report tbody').css('display','none');
					
				},
				complete: function () {
					
					$('.table-daily_report tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					var html = '';
					$('tbody').html(data);
				}
			});
		}
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var Status = $("#Status").val();
			var Approver = $("#Approver").val();
			var AccountID = $("#AccountID").val();
			
			if(Status == 'Approved'){
				$('#submit_button').hide();
				}else{
				$('#submit_button').show();
			}
			var msg = "Limit Exceeded Order Report "+from_date +" To " + to_date;
			$(".report_for").text(msg);
			load_data(from_date,to_date,Status,Approver,AccountID);
			
		});
		$('#Status').on('change',function(){
			var Status = $("#Status").val();
			$("#Approver").val('');
			$('.selectpicker').selectpicker('refresh');
			if(Status == 'Approved'){
				$('#ApproverDiv').show();
				}else{
				$('#ApproverDiv').hide();
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
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->FIRMNAME; ?></td></tr><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="9">Limit Exceeded Orders : '+from_date+' To '+to_date+'</td>';
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
</script> 