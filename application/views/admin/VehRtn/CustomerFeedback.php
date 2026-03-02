<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-10">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Customer Feedback</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait create new entry...</div>
								<div class="searchh4" style="display:none;">Please wait update entry...</div>
							</div>
							<br>
							<div class="col-md-2">
								<?php
									$NextEntryID = end($table_data)['id'] + 1;
								?>
								<div class="form-group" app-field-wrapper="EntryID">
									<small class="req text-danger">* </small>
									<label for="EntryID" class="control-label">Entry No</label>
									<input type="text" id="EntryID" name="EntryID" class="form-control" value="<?php echo $NextEntryID; ?>">
								</div>
								<input type="hidden" id="NextEntryID" name="NextEntryID" class="form-control" value="<?php echo $NextEntryID; ?>">
							</div>
							<div class="col-md-3">
								<?php 
									$current_date = date('d/m/Y');
									echo render_date_input('Date','Date',$current_date);          
								?>
								<input type="hidden" id="ToDayDate" name="ToDayDate" class="form-control" value="<?php echo $current_date; ?>">
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Driver">Driver </label>
									<select class="form-control selectpicker" data-live-search="true" name="Driver" id="Driver" aria-invalid="false">
										<option value="" >None Selected</option>
										<?php
											foreach ($DriverList as $key => $value) {
											?>
											<option value="<?php echo $value["AccountID"]?>" ><?php echo $value["firstname"]." ".$value["lastname"];?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Customer">Customer </label>
									<select class="form-control selectpicker" data-live-search="true" name="Customer" id="Customer" aria-invalid="false">
										<option value="" >None Selected</option>
										<?php
											foreach ($CustomerList as $key => $value) {
											?>
											<option value="<?php echo $value["AccountID"]?>" ><?php echo $value["company"];?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="row">	
							<div class="col-md-2">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Quality">Quality Issue </label>
									<select class="form-control selectpicker" name="Quality" id="Quality" aria-invalid="false">
										<option value="N" >No</option>
										<option value="Y" >Yes</option>
									</select>
								</div>
							</div>	
							<div class="col-md-3" style="display:none;" id="QualityRemarkDiv">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="QualityRemark">Quality Issue Remark</label>
									<input type="text" class="form-control" name="QualityRemark" id="QualityRemark" >
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Quantity">Quantity Issue </label>
									<select class="form-control selectpicker" name="Quantity" id="Quantity" aria-invalid="false">
										<option value="N" >No</option>
										<option value="Y" >Yes</option>
									</select>
								</div>
							</div>
							
							<div class="col-md-3" style="display:none;" id="QuantityRemarkDiv">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="QuantityRemark">Quantity Issue Remark</label>
									<input type="text" class="form-control" name="QuantityRemark" id="QuantityRemark" >
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group" app-field-wrapper="Dispatcher">
									<small class="req text-danger">* </small>
									<label for="Dispatcher" class="control-label">Dispatcher Name</label>
									<input type="text" id="Dispatcher" name="Dispatcher" class="form-control" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="Delivery_Status">Delivery Status </label>
									<select class="form-control selectpicker" name="Delivery_Status" id="Delivery_Status" aria-invalid="false">
										<option value="On Time">On Time</option>
										<option value="Late Supply">Late Supply</option>
									</select>
								</div>
							</div>
							
							<div class="col-md-3" style="display:none;" id="DeliveryRemarkDiv">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="DeliveryRemark">Delivery Status Remark</label>
									<input type="text" class="form-control" name="DeliveryRemark" id="DeliveryRemark" >
								</div>
							</div>
						</div>
						
						
						<div class="row">
							<div class="col-md-5">
								<div class="form-group" app-field-wrapper="Driver_Behaviour">
									<small class="req text-danger">* </small>
									<label for="Driver_Behaviour" class="control-label">Driver Behaviour</label>
									<input type="text" id="Driver_Behaviour" name="Driver_Behaviour" class="form-control" value="">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" app-field-wrapper="Other">
									<small class="req text-danger">* </small>
									<label for="Other" class="control-label">Other</label>
									<input type="text" id="Other" name="Other" class="form-control" value="">
								</div>
							</div>
						</div>	
						
						<div class="clearfix"></div>
						<br>
						<div class="row">	
							<div class="col-md-12">
								<?php if (has_permission_new('CustomerFeedback', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission_new('CustomerFeedback', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
								<?php
								}?>
								
								<button type="button" class="btn btn-default cancelBtn" >Cancel</button>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="padding:5px 10px;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Feedback List</h4>
			</div>
			<div class="modal-body" style="padding:0px 5px !important">
				
				<div class="table-Account_List tableFixHead2">
					<table class="tree table table-striped table-bordered table-Account_List tableFixHead2" id="table_Account_List" width="100%">
						<thead>
							<tr>
								<th class="sortable" style="text-align:left;">Sr. No.</th>
								<th class="sortable" style="text-align:left;">Entry ID </th>
								<th class="sortable" style="text-align:left;">Date</th>
								<th class="sortable" style="text-align:left;">Driver Name</th>
								<th class="sortable" style="text-align:left;">Customer</th>
								<th class="sortable" style="text-align:left;">Quality Issue</th>
								<th class="sortable" style="text-align:left;">Quantity Issue</th>
								<th class="sortable" style="text-align:left;">Dispatcher Name</th>
								<th class="sortable" style="text-align:left;">Delivery Status</th>
								<th class="sortable" style="text-align:left;">Driver Behaviour</th>
								<th class="sortable" style="text-align:left;">Other</th>
							</tr>
						</thead>
						<tbody id="customertlistbody">
							
						</tbody>
					</table>   
				</div>
			</div>
			<div class="modal-footer" style="padding:0px;">
				<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>

<?php init_tail(); ?>
<script>
    $(document).ready(function(){
		
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
		
		$('#Quality').on('change', function () {
			if ($(this).val() === 'Y') {
				$('#QualityRemarkDiv').show();
				} else {
				$('#QualityRemarkDiv').hide();
				$('#QualityRemark').val('');
			}
		});
		$('#Quantity').on('change', function () {
			if ($(this).val() === 'Y') {
				$('#QuantityRemarkDiv').show();
				} else {
				$('#QuantityRemarkDiv').hide();
				$('#QuantityRemark').val('');
			}
		});
		$('#Delivery_Status').on('change', function () {
			if ($(this).val() === 'Late Supply') {
				$('#DeliveryRemarkDiv').show();
				} else {
				$('#DeliveryRemarkDiv').hide();
				$('#DeliveryRemark').val('');
			}
		});
		
		function ResetForm()
		{
			var NextEntryID = $('#NextEntryID').val();
			var ToDayDate = $('#ToDayDate').val();
			$('#EntryID').val(NextEntryID);
			
			$('#Driver').val(''); 
			$('#Quality').val('N'); 
			$('#QualityRemarkDiv').hide();
			$('#QualityRemark').val('');
			$('#Quantity').val('N'); 
			$('#QuantityRemarkDiv').hide();
			$('#QuantityRemark').val('');
			$('#Customer').val(''); 
			$('#Delivery_Status').val('On Time'); 
			$(".selectpicker").selectpicker("refresh"); 
			$('#DeliveryRemarkDiv').hide();
			$('#DeliveryRemark').val('');
			$('#Dispatcher').val('');          
			$('#Driver_Behaviour').val('');          
			$('#Other').val('');          
			
			
			$('#Date').val(ToDayDate);          
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
		}
		// Empty and open create mode
		$("#EntryID").focus(function(){
			ResetForm();
		});
        
		// Cancel selected data
		$(".cancelBtn").click(function(){
			ResetForm();
		});
        
		$("#EntryID").dblclick(function(){
			$('#Account_List').modal('show');
			$.ajax({
				url:"<?php echo admin_url(); ?>VehRtn/GetAllFeedbackList",
				dataType:"JSON",
				method:"POST",
				beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');
				},
				success:function(data){
					$('#customertlistbody').html(data);
					$('.get_EntryID').on('click',function(){ 
                		EntryID = $(this).attr("data-id");
                		$('#EntryID').val(EntryID).blur();
                		$('#Account_List').modal('hide');
					});
				}
			});
			$('#Account_List').on('shown.bs.modal', function () {
                $('#myInput1').val('');
                $('#myInput1').focus();
			})
			
		});
		//======================= On Blur ItemID Get All Date ==========================
		$('#EntryID').blur(function(){ 
			EntryID = $(this).val();
			if(EntryID == ''){
				
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/GetFeedbackDetailByID",
					dataType:"JSON",
					method:"POST",
					data:{EntryID:EntryID},
					beforeSend: function () {
						$('.searchh2').css('display','block');
						$('.searchh2').css('color','blue');
					},
					complete: function () {
						$('.searchh2').css('display','none');
					},
					success:function(data){
						init_selectpicker();
						if(data == null){
							ResetForm();
							}else{
							
							$('#Driver').val(data.DriverID);
							$('#Customer').val(data.AccountID);  
							$('#Quality').val(data.Quality); 
							if (data.Quality === 'Y') {
								$('#QualityRemarkDiv').show();
								$('#QualityRemark').val(data.QualityRemark);
								} else {
								$('#QualityRemarkDiv').hide();
								$('#QualityRemark').val('');
							}
							
							$('#Quantity').val(data.Quantity);
							
							if (data.Quantity === 'Y') {
								$('#QuantityRemarkDiv').show();
								$('#QuantityRemark').val(data.QuantityRemark);
								} else {
								$('#QuantityRemarkDiv').hide();
								$('#QuantityRemark').val('');
							}
							
							$('#Delivery_Status').val(data.Delivery_Status);
							
							if (data.Delivery_Status === 'Late Supply') {
								$('#DeliveryRemarkDiv').show();
								$('#DeliveryRemark').val(data.DeliveryRemark);
								} else {
								$('#DeliveryRemarkDiv').hide();
								$('#DeliveryRemark').val('');
							}
							
							$('#Dispatcher').val(data.Dispatcher);          
							$('#Driver_Behaviour').val(data.Driver_Behaviour);           
							$('#Other').val(data.Other);     
							$(".selectpicker").selectpicker("refresh"); 
							
							var EntryDate = data.TransDate.substring(0, 10);
							var DateNew = EntryDate.split("-").reverse().join("/");
							$('#Date').val(DateNew);
							$('.saveBtn').hide();
							$('.updateBtn').show();
							$('.saveBtn2').hide();
							$('.updateBtn2').show();
						} 
					}
				});
			}
			
		});
		
		
		//===================== Save New RestRecord =================================
		$('.saveBtn').on('click',function(){ 
			EntryID = $('#EntryID').val();
			Driver = $('#Driver').val();
			Date = $('#Date').val();
			Quality = $('#Quality').val();
			Quantity = $('#Quantity').val();
			QuantityRemark = $('#QuantityRemark').val();
			QualityRemark = $('#QualityRemark').val();
			DeliveryRemark = $('#DeliveryRemark').val();
			Customer = $('#Customer').val();
			Delivery_Status = $('#Delivery_Status').val();
			Dispatcher = $('#Dispatcher').val();
			Driver_Behaviour = $('#Driver_Behaviour').val();
			Other = $('#Other').val();
			if(EntryID == ""){
				alert("Please Refresh Page, something went wrong");
				}else if(Date == ""){
				alert("Please Select Date");
				}else if(Driver == ""){
				alert("Please Select Driver Name");
				}else if(Customer == ""){
				alert("Please Select Customer Name");
				}else if(Quality == ""){
				alert("Please Select Quality Issue");
				}else if(Quality == "Y" && QualityRemark == ""){
				alert("Please Enter Quality Issue Remark");
				}else if(Quantity == ""){
				alert("Please Select Quantity Issue");
				}else if(Quantity == "Y" && QuantityRemark == ""){
				alert("Please Enter Quantity Issue Remark");
				}else if(Dispatcher == ""){
				alert("Please Enter Dispatcher Name");
				}else if(Delivery_Status == ""){
				alert("Please Select Delivery Status");
				}else if(Delivery_Status == "Late Supply" && DeliveryRemark == ""){
				alert("Please Enter Delivery Status Remark");
				}else if(Driver_Behaviour == ""){
				alert("Please Enter Driver Behaviour");
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/SaveCustomerFeedback",
					dataType:"JSON",
					method:"POST",
					data:{EntryID:EntryID,Date:Date,Driver:Driver,Customer:Customer,Quality:Quality,Quantity:Quantity,Dispatcher:Dispatcher,Delivery_Status:Delivery_Status,Driver_Behaviour:Driver_Behaviour,Other:Other,QualityRemark:QualityRemark,QuantityRemark:QuantityRemark,DeliveryRemark:DeliveryRemark
					},
					beforeSend: function () {
						$('.searchh3').css('display','block');
						$('.searchh3').css('color','blue');
					},
					complete: function () {
						$('.searchh3').css('display','none');
					},
					success:function(data){
						if(data == true){
							alert_float('success', 'Record created successfully...');
							location.reload();
							}else{
							alert_float('warning', 'Something went wrong...');
							ResetForm();
						}
					}
				});
			}
		});
		//===================== Update Exiting Item ====================================
		$('.updateBtn').on('click',function(){ 
			EntryID = $('#EntryID').val();
			Driver = $('#Driver').val();
			Date = $('#Date').val();
			Quality = $('#Quality').val();
			Quantity = $('#Quantity').val();
			Customer = $('#Customer').val();
			Delivery_Status = $('#Delivery_Status').val();
			Dispatcher = $('#Dispatcher').val();
			Driver_Behaviour = $('#Driver_Behaviour').val();
			Other = $('#Other').val();
			
			QuantityRemark = $('#QuantityRemark').val();
			QualityRemark = $('#QualityRemark').val();
			DeliveryRemark = $('#DeliveryRemark').val();
			
			if(EntryID == ""){
				alert("Please Refresh Page, something went wrong");
				}else if(Date == ""){
				alert("Please Select Date");
				}else if(Driver == ""){
				alert("Please Select Driver Name");
				}else if(Customer == ""){
				alert("Please Select Customer Name");
				}else if(Quality == ""){
				alert("Please Select Quality Issue");
				}else if(Quality == "Y" && QualityRemark == ""){
				alert("Please Enter Quality Issue Remark");
				}else if(Quantity == ""){
				alert("Please Select Quantity Issue");
				}else if(Quantity == "Y" && QuantityRemark == ""){
				alert("Please Enter Quantity Issue Remark");
				}else if(Dispatcher == ""){
				alert("Please Enter Dispatcher Name");
				}else if(Delivery_Status == ""){
				alert("Please Select Delivery Status");
				}else if(Delivery_Status == "Late Supply" && DeliveryRemark == ""){
				alert("Please Enter Delivery Status Remark");
				}else if(Driver_Behaviour == ""){
				alert("Please Enter Driver Behaviour");
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/UpdateCustomerFeedback",
					dataType:"JSON",
					method:"POST",
					data:{EntryID:EntryID,Date:Date,Driver:Driver,Customer:Customer,Quality:Quality,Quantity:Quantity,Dispatcher:Dispatcher,Delivery_Status:Delivery_Status,Driver_Behaviour:Driver_Behaviour,Other:Other,QualityRemark:QualityRemark,QuantityRemark:QuantityRemark,DeliveryRemark:DeliveryRemark
					},
					beforeSend: function () {
						$('.searchh4').css('display','block');
						$('.searchh4').css('color','blue');
					},
					complete: function () {
						$('.searchh4').css('display','none');
					},
					success:function(data){
						if(data == true){
							alert_float('success', 'Record updated successfully...');
							location.reload();
							}else{
							alert_float('warning', 'Something went wrong...');
							ResetForm();
						}
					}
				});
			}
		});
		
		var EntryIDSession = "<?php echo $this->session->userdata('EntryID') ?>";
        if(EntryIDSession !== ""){
            $('#EntryID').val(EntryIDSession).blur();
		}
		
		<?php $this->session->unset_userdata('EntryID');?>
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_RestRecord_List");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						}else{
						tr[i].style.display = "none";
					} 
				}   
			}
		}
	}
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_RestRecord_List tbody");
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

<style>
	
	#item_code1 {
    text-transform: uppercase;
	}
	#table_RestRecord_List td:hover {
    cursor: pointer;
	}
	#table_RestRecord_List tr:hover {
    background-color: #ccc;
	}
	
    .table-RestRecord_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-RestRecord_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-RestRecord_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>
<style>
	
	#AccountID {
    text-transform: uppercase;
	}
	#Pan {
    text-transform: uppercase;
	}
	#vat {
    text-transform: uppercase;
	}
	#table_Account_List td:hover {
    cursor: pointer;
	}
	#table_Account_List tr:hover {
    background-color: #ccc;
	}
	
    .table-Account_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Account_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Account_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>