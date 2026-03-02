<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="row">
						<div class="col-md-12"> 
							<div class="panel-body">
								<nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Purchase Entry List</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
								
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
										<div class="form-group">
											<label for="status">Status</label>
											<select class="selectpicker" data-live-search = "true" data-width="100%" name="status" id="status">
												<option value="">All</option>
												<option value="Pending">Pending</option>
												<option value="Approve">Approve</option>
												<option value="Completed">Completed</option>
												<option value="Cancel">Cancel</option>
												
											</select>
										</div>
									</div>
									<div class=""clearfix></div>
									<div class="col-md-8">
									    <br>
										<div class="custom_button " style="margin-left:12px;">
										    <a class="btn btn-info search_data" href="#" id="search_data"><span>Show</span></a>
											<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
											<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
										</div>
									</div>
									<div class="col-md-4" >
									    <br>
										<input type="text" id="myInput1" class="form-control" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
									</div>
									<div class="clearfix"></div>
									<div class="col-md-12">
										<span id="searchh3" style="display:none;">please wait exporting data....</span>
									</div>
									<div class="col-md-12">
										
										<div class="table_purchase_report">
											
											<table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
												
												<thead>
													<tr style="display:none;">
														<td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
													</tr>
													<tr>
														<th class="sortable" style="width:7% ">Purch Entry No.</th>
														<th class="sortable" style="width:5% ">Date</th>
														<th class="sortable" style="width:15% text-align:left;">Purchased From</th>
														<th class="sortable" style="width:5% text-align:left;">InvoiceNo</th>
														<th class="sortable" style="width:5% text-align:left;">Inv. Date</th>
														<th class="sortable" style="width:5% " align="right">Purchase Amt</th>
														<th class="sortable" style="width:3% " align="right">Disc Amt</th>
														<th class="sortable" style="width:5% " align="right">Other Charges</th>
														<th class="sortable" style="width:5% " align="right">CGST Amt</th>
														<th class="sortable" style="width:5% " align="right">SGST Amt</th>
														<th class="sortable" style="width:5% " align="right">IGST Amt</th>
														<th class="sortable" style="width:5% " align="right">Tds Amt</th>
														<th class="sortable" style="width:5% " align="right">Invamt</th>
														<th class="sortable" style="width:6% text-align:left;">Status</th>
														<th class="sortable" style="width:6% text-align:left;">Is Deduction</th>
														<th class="sortable" style="width:6% text-align:left;">Remark</th>
														
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
				
			</div>
			
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
</style>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		var status = $("#status").val();
		var msg = "Purchase Order Report "+from_date +" To " + to_date;
		$(".report_for").text(msg);
		load_data(from_date,to_date,status);
		
		function load_data(from_date,to_date,status)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/load_data_for_purchase",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,status:status},
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
						var deduction = 'No';
						var remark = '';
						if(data[count].is_deduction == 'Y'){
							deduction = 'Yes';	
							remark = data[count].remark;
						}
						var url = "<?php echo admin_url(); ?>purchase/EditPurchaseEntry/" + data[count].PurchID;
						html += '<tr onclick = "window.open('+"'"+url+"'"+')">';
						html += '<td  style="text-align:center;">'+data[count].PurchID+'</td>';
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
						html += '<td >'+data[count].cur_status+'</td>';
						html += '<td >'+deduction+'</td>';
						html += '<td >'+remark+'</td>';
						html += '</tr>';
					}
					$('.table_purchase_report tbody').html(html);
					
				}
			});
		}
		
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var status = $("#status").val();
			var msg = "Purchase Order Report "+from_date +" To " + to_date;
			$(".report_for").text(msg);
			load_data(from_date,to_date,status);
			
		});
		
	});
</script>
<script type="text/javascript">
	function printPage(){
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var status = $("#status").val();
		if(status == '')
		{
			status = 'All';
		}
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->FIRMNAME; ?></td></tr><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="9">Purchase Entry Report : '+from_date+' To '+to_date+' - Status : '+status+'</td>';
		heading_data += '</tr>';
		heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
	
	$("#caexcel").click(function(){
		
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		var status = $("#status").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>purchase/export_purchase_Entries",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,status:status},
            beforeSend: function () {
                $('#searchh3').css('display','block');
                
			},
            complete: function () {
                
                $('#searchh3').css('display','none');
			},
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
			}
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
			td = tr[i].getElementsByTagName("td")[2];
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
		
		$(document).on("click", ".sortable", function () {
		var table = $("#table_purchase_report tbody");
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
	});
</script>
</html>

