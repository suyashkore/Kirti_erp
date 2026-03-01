<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Driver Rest Record</b></li>
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
							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label for="estimate">Driver </label>
									<select class="form-control selectpicker" name="Driver" id="Driver" aria-invalid="false">
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
							<div class="col-md-2">
								<div class="form-group" app-field-wrapper="Amount">
									<small class="req text-danger">* </small>
									<label for="Amount" class="control-label">Amount</label>
									<input type="text" id="Amount" name="Amount" class="form-control" value="">
								</div>
							</div>
							
							
							<div class="clearfix"></div>
							<br>
							<div class="col-md-12">
								<?php if (has_permission_new('DriverRestRecord', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission_new('DriverRestRecord', '', 'edit')) {
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
<?php init_tail(); ?>

<script>
	
	$(document).on('keypress', '#Amount', function (event) {
		if (event.which < 48 || event.which > 57) {
			event.preventDefault();
		}
		
	});
    $(document).ready(function(){
		
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
		
		function ResetForm()
		{
			var NextEntryID = $('#NextEntryID').val();
			var ToDayDate = $('#ToDayDate').val();
			$('#EntryID').val(NextEntryID);
			$('#Amount').val('');          
			
			
			$('#Date').val(ToDayDate);  
			$('#Driver').val(''); 
			$("#Driver").selectpicker("refresh");         
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
        
		//======================= On Blur ItemID Get All Date ==========================
		$('#EntryID').blur(function(){ 
			EntryID = $(this).val();
			if(EntryID == ''){
				
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/GetRestRecordDetailByID",
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
							$('#Amount').val(data.Amount);
							$('#Driver').val(data.DriverID);
							$("#Driver").selectpicker("refresh");
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
		$('.saveBtn').on('click', function (e) {
			e.preventDefault();
			e.preventDefault(); // optional, if form submit is involved
			var $btn = $(this);
			
			EntryID = $('#EntryID').val();
			Amount = $('#Amount').val();
			Driver = $('#Driver').val();
			Date = $('#Date').val();
			if(EntryID == ""){
				alert("Please Refresh Page, something went wrong");
				}else if(Amount == ""){
				alert("Please Enter Amount");
				}else if(Date == ""){
				alert("Please Select Date");
				}else if(Driver == ""){
				alert("Please Select Driver Name");
				}else{
				
				if ($btn.prop('disabled')) {
					return; // already clicked once, do nothing
				}
				$btn.prop('disabled', true).text('Please wait...');
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/SaveRestEntry",
					dataType:"JSON",
					method:"POST",
					data:{EntryID:EntryID,Amount:Amount,Driver:Driver,Date:Date
					},
					beforeSend: function () {
						$('.searchh3').css('display','block');
						$('.searchh3').css('color','blue');
					},
					complete: function () {
						$('.searchh3').css('display','none');
						 $btn.prop('disabled', false).text('SAVE');
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
			Amount = $('#Amount').val();
			Driver = $('#Driver').val();
			Date = $('#Date').val();
			if(EntryID == ""){
				alert("Please Refresh Page, something went wrong");
				}else if(Amount == ""){
				alert("Please Enter Amount");
				}else if(Date == ""){
				alert("Please Select Date");
				}else if(Driver == ""){
				alert("Please Select Driver Name");
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>VehRtn/UpdateRestRecord",
					dataType:"JSON",
					method:"POST",
					data:{EntryID:EntryID,Amount:Amount,Driver:Driver,Date:Date
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