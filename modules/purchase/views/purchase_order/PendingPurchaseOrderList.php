<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			
			<div class="col-md-12">
				<div class="panel_s accounting-template estimate">
					<div class="row">
						<div class="col-md-10"> 
							<div class="panel-body">
								<nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url()?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Pending Purchase Orders List</b></li>
									</ol>
								</nav>
                                <hr class="hr_style">
								<br>
								
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
									<div class="col-md-2">
										<div class="form-group">
											<label for="status">Status</label>
											<select class="form-control" name="status" id="status">
												<option value="">Both</option>
												<option value="Approved">Approved</option>
												<option value="InProgress">InProgress</option>
												
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label for="ReportType">Report Type</label>
											<select class="form-control" name="ReportType" id="ReportType">
												<option selected value="BillWise">Bill Wise</option>
												<option value="ItemWise">Item Wise</option>
												
											</select>
										</div>
									</div>
									<div class="col-md-1">
										<br>
										<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('Show'); ?></button>
									</div>
									<div class="col-md-1">
										<br>
										<div class="custom_button" style="margin-left:12px;">
											<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
											
										</div>
									</div>
									<div class="col-md-4" >
										<br>
										<input type="text" id="myInput1" class="form-control" onkeyup="myFunction2()" placeholder="Search.." title="Type in a name" style="float: right;">
										
									</div>
									<div class="clearfix"></div>
									<div class="row">
										
										
										<div class="col-md-12">
											<span id="searchh3" style="display:none;">please wait exporting data....</span>
										</div>
									</div>
									<div class="col-md-12">
										
										<div class="table_purchase_report" id="TableReport">
											
											
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

<!--Extension Modal-->
<div class="modal fade" id="transfer-modal">
	<div class="modal-dialog pup100" >
		<div class="modal-content" style="width: 119%;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Extend Date</h4>
			</div>
			<?php echo form_open('admin/purchase/ItemDateExtension',array('id'=>'ItemDateExtension')); ?>
			<div class="modal-body modalbody" style="">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-striped table-bordered tableFixHead2" width="100%" id="Extension">
							<tr>
								<td>PO.No</td>
								<td><input type="hidden" name="eventId" id="eventId"/>
								<span id="idHolder"></span></td>
								<td>PO. Date</td>
								<td>
									<input type="hidden" name="order_date" id="order_date"/>
									<span id="idHolder3"></span>
								</td>
								<td>Expected Delivery Date</td>
								<td><span id="idHolder2"></span></td>
							</tr>
							
							<tr>
								<td>Item Name</td>
								<td ><input type="hidden" name="ItemID" id="ItemID"/>
								<span id="idHolder4"></span></td>
								<td>Vendor Name</td>
								<td colspan="3"><span id="idHolder1"></span></td>
							</tr>
						</table>
					</div>
					<div class="col-md-4">
						<?php $value = _d(date('Y-m-d')); ?>
						<?php echo render_date_input('extension_date','Extension Date',$value); ?>
					</div>
					<div class="col-md-8">
						<?php echo render_textarea('extension_remark','Extension Remark',array(),array()); ?>
					</div>
					
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
			</div>
			<?php echo form_close(); ?>	
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
		var ReportType = $("#ReportType").val();
		var msg = "Purchase Order Report "+from_date +" To " + to_date;
		$(".report_for").text(msg);
		load_data(from_date,to_date,status,ReportType);
		
		function load_data(from_date,to_date,status,ReportType)
		{
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/load_data_for_PendingpurchaseOrder",
				dataType:"JSON",
				method:"POST",
				data:{from_date:from_date, to_date:to_date,status:status,ReportType:ReportType},
				beforeSend: function () {
					
					$('#searchh2').css('display','block');
					$('#TableReport tbody').css('display','none');
					
				},
				complete: function () {
					
					$('#TableReport tbody').css('display','');
					$('#searchh2').css('display','none');
				},
				success:function(data){
					
					$('#TableReport').html(data);
					
				}
			});
		}
		
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var status = $("#status").val();
			var ReportType = $("#ReportType").val();
			var msg = "Purchase Order Report "+from_date +" To " + to_date;
			$(".report_for").text(msg);
			load_data(from_date,to_date,status,ReportType);
			
		});
		
	});
</script>
<script type="text/javascript">
	function ExtendeDate(PurchID,PurchDate,ExpiryDate,ItemID,ItemName,VendorName) {
		$('#idHolder1').html(VendorName);
		$('#idHolder2').html(ExpiryDate);
		$('#idHolder3').html(PurchDate);
		$('#idHolder4').html(ItemName);
		$(".modal-body #order_date").val(PurchDate);
		$(".modal-body #ItemID").val(ItemID);
		$(".modal-body #eventId").val( PurchID );
        $('#idHolder').html( PurchID );
		$('#transfer-modal').modal('show');
	}
	function CompletePendingOrder(purchID) {
		if (confirm("Are you sure you want to complete this order?")) {
			$.ajax({
				url: "<?php echo admin_url(); ?>purchase/CompletePendingOrder",
				type: 'POST',
				data: { PurchID: purchID },
				success: function(response) {
					alert("Order completed successfully!");
					location.reload();
				},
				error: function(error) {
					alert("An error occurred while completing the order.");
				}
			});
		}
	}
	function printPage(){
        
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
		
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->FIRMNAME; ?></td></tr><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="9">Purchase Order Report : '+from_date+' To '+to_date+'</td>';
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

