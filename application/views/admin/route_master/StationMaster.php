<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row" style="margin-top:1.5rem;">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Station Master</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Item ID Group...</div>
								<div class="searchh4" style="display:none;">Please wait update Item ID Group...</div>
							</div>
							<br>
							<div class="col-md-2">
								<?php
									$nextStationID = $lastId + 1;
								?>
								<div class="form-group" app-field-wrapper="StationID">
									<small class="req text-danger">* </small>
									<label for="StationID" class="control-label">Station ID</label>
									<input type="text" id="StationID" name="StationID" class="form-control" value="<?= $nextStationID;?>" onkeypress="return isNumber(event)">
								</div>
								
								<input type="hidden" id="NextStationID" name="NextStationID" class="form-control" value="<?php echo $nextStationID; ?>">
							</div>
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="StationName">
									<small class="req text-danger">* </small>
									<label for="StationName" class="control-label">Station Name</label>
									<input type="text" id="StationName" name="StationName" class="form-control" value="">
								</div>							
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label class="form-label"><small class="req text-danger">* </small> Is Active? </label>
									<select class="selectpicker" name="status" id="status" data-width="100%" data-none-selected-text="-- Select --" data-live-search="false" required>
										<option value="1" selected>Active</option> 
										<option value="0">Deactive</option> 
									</select>
								</div>
								
							</div>
							
							
							<div class="clearfix"></div>
							<br><br>
							<div class="col-md-12">
								<?php if (has_permission('StationMaster', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission('StationMaster', '', 'edit')) {
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
						
						<div class="clearfix"></div>
						<!-- Iteme List Model-->
						
						<div class="modal fade StationMaster_List" id="StationMaster_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Station List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
										
										<div class="table-StationMaster_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-StationMaster_List tableFixHead2" id="table_StationMaster_List" width="100%">
												<thead>
													<tr>
														<th style="text-align:left;" class="sortablePop">Station ID </th>
														<th style="text-align:left;" class="sortablePop">Station Name</th>
														<th style="text-align:left;" class="sortablePop">Status</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($table_data as $key => $value) {
															if($value['status'] == 1){
																$status ="Active";
																}else{
																$status ="Deactive";
															}
														?>
														<tr class="get_StationMaster" data-id="<?php echo $value["id"]; ?>">
															<td><?php echo $value['id'];?></td>
															<td><?php echo $value['StationName'];?></td>
															<td><?php echo $status;?></td>
														</tr>
													<?php } ?>
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
						<!-- /.modal -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<script>
    $(document).ready(function(){
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
        $("#StationID").dblclick(function(){
            $('#StationMaster_List').modal('show');
            $('#StationMaster_List').on('shown.bs.modal', function () {
				$('#myInput1').focus();
			})
		});
		
		$('#State').on('change', function() {
			var StateID = $(this).val();
			//alert(roleid);
			var url = "<?php echo base_url(); ?>admin/clients/GetCity";
			jQuery.ajax({
				type: 'POST',
				url:url,
				data: {StateID: StateID},
				dataType:'json',
				success: function(data) {
					$("#City").find('option').remove();
					$("#City").selectpicker("refresh");
					for (var i = 0; i < data.length; i++) {
						$("#City").append(new Option(data[i].city_name, data[i].id));
					}
					$('.selectpicker').selectpicker('refresh');
				}
			});
		});
        
		// Empty and open create mode
        $("#StationID").focus(function(){
            var NextStationID = $('#NextStationID').val();
            $('#StationID').val(NextStationID);
            $('#StationName').val('');
            
			$('select[name=status]').val('1');
            $('.selectpicker').selectpicker('refresh'); 
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            
		});
        
		// Cancel selected data
        $(".cancelBtn").click(function(){
            var NextStationID = $('#NextStationID').val();
            $('#StationID').val(NextStationID);
            $('#StationName').val('');
            
			$('select[name=status]').val('1');
            $('.selectpicker').selectpicker('refresh'); 
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            
		});
        
		// On Blur ItemID Get All Date
        $('#StationID').blur(function(){ 
            StationID = $(this).val();
            if(StationID == ''){
                var NextStationID = $('#NextStationID').val();
                $('#StationID').val(NextStationID);
				}else{
                $.ajax({
					url:"<?php echo admin_url(); ?>route_master/GetStationMasterDetailByID",
					dataType:"JSON",
					method:"POST",
					data:{StationID:StationID},
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
							var NextStationID = $('#NextStationID').val();
							$('#StationID').val(NextStationID);
							$('#StationName').val('');
							
							$('select[name=status]').val('1');
							$('.selectpicker').selectpicker('refresh'); 
							$('.saveBtn').show();
							$('.saveBtn2').show();
							$('.updateBtn').hide();
							$('.updateBtn2').hide();
							}else{   
							$('#StationName').val(data.StationName);
							
							$('select[name=status]').val(data.status);
							$('.selectpicker').selectpicker('refresh'); 
							$('.saveBtn').hide();
							$('.updateBtn').show();
							$('.saveBtn2').hide();
							$('.updateBtn2').show();
						} 
					}
				});
			}
            
		});
        
        $('.get_StationMaster').on('click',function(){ 
            StationID = $(this).attr("data-id");
            $.ajax({
                url:"<?php echo admin_url(); ?>route_master/GetStationMasterDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{StationID:StationID},
                beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
                complete: function () {
					$('.searchh2').css('display','none');
				},
                success:function(data){
					$('#StationID').val(data.id);
					
					$('#StationName').val(data.StationName);
					
					$('select[name=status]').val(data.status);
					$('.selectpicker').selectpicker('refresh');  
					
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
				}
			});
            $('#StationMaster_List').modal('hide');
		});
        
		// Save New State
        $('.saveBtn').on('click',function(){ 
            StationID = $('#StationID').val();
            StationName = $('#StationName').val();
            State = $('#State').val();
            City = $('#City').val();
            status = $('#status').val();
            if(StationID == '' || StationID == null){
				alert('Station ID Cannot Be Null');
				}else if(StationName == '' || StationName == null){
				alert('Enter Station Name');
				}else if(status == '' || status == null){
				alert('Select Active Status');
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>route_master/SaveStationMaster",
					dataType:"JSON",
					method:"POST",
					data:{StationID:StationID,StationName:StationName,status:status
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
							}else if(data === 'exist'){
							alert_float('warning', 'Station Name Is Already Exist');
							}else{
							alert_float('warning', 'Something went wrong...');
						}
					}
				});
			}
		});
		// Update Exiting Item
		$('.updateBtn').on('click',function(){ 
			StationID = $('#StationID').val();
            StationName = $('#StationName').val();
            
            status = $('#status').val();
            if(StationID == '' || StationID == null){
				alert('Station ID Cannot Be Null');
				}else if(StationName == '' || StationName == null){
				alert('Enter Station Name');
				}else if(status == '' || status == null){
				alert('Select Active Status');
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>route_master/UpdateStationMaster",
					dataType:"JSON",
					method:"POST",
					data:{StationID:StationID,StationName:StationName,status:status
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
							}else if(data === 'exist'){
							alert_float('warning', 'Station Name Is Already Exist');
							}else{
							alert_float('warning', 'Something went wrong...');
						}
					}
				});
			}
		});
	});
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_StationMaster_List");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						}else if(td2){
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else{
							tr[i].style.display = "none";
						} 
					}  
				}
			}
		}
	}
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_StationMaster_List tbody");
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
<script>
    function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode = 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	}
</script>
<style>
	
	#item_code1 {
    text-transform: uppercase;
	}
	#table_StationMaster_List td:hover {
    cursor: Stationer;
	}
	#table_StationMaster_List tr:hover {
    background-color: #ccc;
	}
	
    .table-StationMaster_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-StationMaster_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-StationMaster_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>