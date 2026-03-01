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
            					<li class="breadcrumb-item active" aria-current="page"><b>Point Master</b></li>
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
									$nextPointID = $lastId + 1;
								?>
								<div class="form-group" app-field-wrapper="PointID">
									<small class="req text-danger">* </small>
									<label for="PointID" class="control-label">Point ID</label>
									<input type="text" id="PointID" name="PointID" class="form-control" value="<?= $nextPointID;?>" onkeypress="return isNumber(event)">
								</div>
								
								<input type="hidden" id="NextPointID" name="NextPointID" class="form-control" value="<?php echo $nextPointID; ?>">
							</div>
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="PointName">
									<small class="req text-danger">* </small>
									<label for="PointName" class="control-label">Point Name</label>
									<input type="text" id="PointName" name="PointName" class="form-control" value="">
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
							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label class="form-label">State</label>
									<select class="selectpicker" name="State" id="State" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
										<option value=""></option>   
										<?php
											foreach ($state as $key => $value) {
											?>
											<option value="<?php echo $value['short_name'];?>"><?php echo $value['state_name'];?></option>  
											<?php   
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<small class="req text-danger">* </small>
									<label class="form-label">City</label>
									<select class="selectpicker" name="City" id="City" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
										<option value=""></option>   
										
									</select>
								</div>
							</div>
							
							
							<div class="clearfix"></div>
							<br><br>
							<div class="col-md-12">
								<?php if (has_permission('PointMaster', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission('PointMaster', '', 'edit')) {
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
						
						<div class="modal fade PointMaster_List" id="PointMaster_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Point List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
										
										<div class="table-PointMaster_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-PointMaster_List tableFixHead2" id="table_PointMaster_List" width="100%">
												<thead>
													<tr>
														<th style="text-align:left;" class="sortablePop">Point ID </th>
														<th style="text-align:left;" class="sortablePop">Point Name</th>
														<th style="text-align:left;" class="sortablePop">State Name</th>
														<th style="text-align:left;" class="sortablePop">City</th>
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
														<tr class="get_PointMaster" data-id="<?php echo $value["id"]; ?>">
															<td><?php echo $value['id'];?></td>
															<td><?php echo $value['PointName'];?></td>
															<td><?php echo $value['StateName'];?></td>
															<td><?php echo $value['CityName'];?></td>
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
        $("#PointID").dblclick(function(){
            $('#PointMaster_List').modal('show');
            $('#PointMaster_List').on('shown.bs.modal', function () {
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
        $("#PointID").focus(function(){
            var NextPointID = $('#NextPointID').val();
            $('#PointID').val(NextPointID);
            $('#PointName').val('');
            $('select[name=State]').val('');
            $('.selectpicker').selectpicker('refresh'); 
			$("#City").find('option').remove();
			$("#City").selectpicker("refresh");
			$('select[name=status]').val('1');
            $('.selectpicker').selectpicker('refresh'); 
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            
		});
        
		// Cancel selected data
        $(".cancelBtn").click(function(){
            var NextPointID = $('#NextPointID').val();
            $('#PointID').val(NextPointID);
            $('#PointName').val('');
            $('select[name=State]').val('');
            $('.selectpicker').selectpicker('refresh');  
			$("#City").find('option').remove();
			$("#City").selectpicker("refresh"); 
			$('select[name=status]').val('1');
            $('.selectpicker').selectpicker('refresh'); 
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            
		});
        
		// On Blur ItemID Get All Date
        $('#PointID').blur(function(){ 
            PointID = $(this).val();
            if(PointID == ''){
                var NextPointID = $('#NextPointID').val();
                $('#PointID').val(NextPointID);
				}else{
                $.ajax({
					url:"<?php echo admin_url(); ?>route_master/GetPointMasterDetailByID",
					dataType:"JSON",
					method:"POST",
					data:{PointID:PointID},
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
							var NextPointID = $('#NextPointID').val();
							$('#PointID').val(NextPointID);
							$('#PointName').val('');
							$('select[name=State]').val('');
							$('.selectpicker').selectpicker('refresh');  
							$("#City").find('option').remove();
							$("#City").selectpicker("refresh"); 
							$('select[name=status]').val('1');
							$('.selectpicker').selectpicker('refresh'); 
							$('.saveBtn').show();
							$('.saveBtn2').show();
							$('.updateBtn').hide();
							$('.updateBtn2').hide();
							}else{
							$('select[name=State]').val(data.state);
							$('.selectpicker').selectpicker('refresh');   
							$('#PointName').val(data.PointName);
							let CityList = data.CityList;
							$("#City").find('option').remove();
							$("#City").selectpicker("refresh");
							$("#City").append(new Option('None selected', ''));
							for (var i = 0; i < CityList.length; i++) {
								
								$("#City").append(new Option(CityList[i].city_name, CityList[i].id));
							}
							$('select[name=City]').val(data.city);
							$('.selectpicker').selectpicker('refresh');  
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
        
        $('.get_PointMaster').on('click',function(){ 
            PointID = $(this).attr("data-id");
            $.ajax({
                url:"<?php echo admin_url(); ?>route_master/GetPointMasterDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{PointID:PointID},
                beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
                complete: function () {
					$('.searchh2').css('display','none');
				},
                success:function(data){
					$('#PointID').val(data.id);
					$('select[name=State]').val(data.state);
					$('.selectpicker').selectpicker('refresh');   
					$('#PointName').val(data.PointName);
					let CityList = data.CityList;
					$("#City").find('option').remove();
					$("#City").selectpicker("refresh");
					$("#City").append(new Option('None selected', ''));
					for (var i = 0; i < CityList.length; i++) {
						
						$("#City").append(new Option(CityList[i].city_name, CityList[i].id));
					}
					$('select[name=City]').val(data.city);
					$('.selectpicker').selectpicker('refresh');  
					$('select[name=status]').val(data.status);
					$('.selectpicker').selectpicker('refresh');  
					
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
				}
			});
            $('#PointMaster_List').modal('hide');
		});
        
		// Save New State
        $('.saveBtn').on('click',function(){ 
            PointID = $('#PointID').val();
            PointName = $('#PointName').val();
            State = $('#State').val();
            City = $('#City').val();
            status = $('#status').val();
            if(PointID == '' || PointID == null){
				alert('Point ID Cannot Be Null');
				}else if(PointName == '' || PointName == null){
				alert('Enter Point Name');
				}else if(State == '' || State == null){
				alert('Select State');
				}else if(City == '' || City == null){
				alert('Select City');
				}else if(status == '' || status == null){
				alert('Select Active Status');
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>route_master/SavePointMaster",
					dataType:"JSON",
					method:"POST",
					data:{PointID:PointID,PointName:PointName,State:State,City:City,status:status
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
						}
					}
				});
			}
		});
		// Update Exiting Item
		$('.updateBtn').on('click',function(){ 
			 PointID = $('#PointID').val();
            PointName = $('#PointName').val();
            State = $('#State').val();
            City = $('#City').val();
            status = $('#status').val();
            if(PointID == '' || PointID == null){
				alert('Point ID Cannot Be Null');
				}else if(PointName == '' || PointName == null){
				alert('Enter Point Name');
				}else if(State == '' || State == null){
				alert('Select State');
				}else if(City == '' || City == null){
				alert('Select City');
				}else if(status == '' || status == null){
				alert('Select Active Status');
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>route_master/UpdatePointMaster",
					dataType:"JSON",
					method:"POST",
					data:{PointID:PointID,PointName:PointName,State:State,City:City,status:status
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
		table = document.getElementById("table_PointMaster_List");
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
		var table = $("#table_PointMaster_List tbody");
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
	#table_PointMaster_List td:hover {
    cursor: pointer;
	}
	#table_PointMaster_List tr:hover {
    background-color: #ccc;
	}
	
    .table-PointMaster_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-PointMaster_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-PointMaster_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>