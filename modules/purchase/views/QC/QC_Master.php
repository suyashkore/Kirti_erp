<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-7">
				<div class="panel_s">
					<div class="panel-body">
						
							<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>QC</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>QC Master</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
						<?php hooks()->do_action('before_items_page_content'); ?>
						<div class="row">
							<?php echo form_open('admin/purchase/QC_Master',array('id'=>'master_form')); ?>
							<?php echo form_hidden('itemid'); ?>
							<div class="col-md-4">
								<div class="form-group">
									<label>Main Item Group</label>
									<select id="main_group" onchange="cleardata2()" required name="main_group" class="selectpicker" data-width="100%" data-none-selected-text="None selected" data-live-search="true" tabindex="-98">
										
										<option value="">Non selected</option>
										<?php 
											foreach($ItemMainGroups as $each)
											{
											?>
											<option value="<?= $each['id']?>"><?= $each['name']?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Sub-Group 1</label>
									<select id="sub_group1" required name="sub_group1" class="selectpicker" data-width="100%" data-none-selected-text="None selected" data-live-search="true" tabindex="-98">
										<option value="">None selected</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Sub-Group 2</label>
									<select id="sub_group2" required name="sub_group2" class="selectpicker" data-width="100%" data-none-selected-text="None selected" data-live-search="true" tabindex="-98">
										<option value="">None selected</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Item</label>
									<select id="item_id1" onchange="cleardata()" multiple required name="item_id1[]" class="selectpicker" data-width="100%" data-none-selected-text="None selected" data-live-search="true" tabindex="-98">
										<option value="">None selected</option>
									</select>
								</div>
							</div>
							<div class="col-md-10">
								<table class="table items table-striped table-bordered" width="100%">
									
									<thead>
										<tr> 
											<th style="width:40%">Parameter</th>
											<th style="width:15%">Min Range</th>
											<th style="width:15%">Max Range</th>
											<th style="width:20%">Validation</th>
											<th style="width:10%">Action</th>
										</tr>
									</thead>
									<tbody id="qctbody">
										
										<tr>
											
											<td><select id="parameter_id1" name="parameter_id1" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
												<option value="">Non selected</option>
												<?php 
													foreach($parameter_data as $each)
													{
													?>
													<option value="<?= $each['id']?>"><?= $each['parameter_name']?></option>
													<?php
													}
												?>
											</select></td>
											<td>
												<input type="text" id="min_range1" name="min_range1" class="form-control min_range" >
											</td>
											<td>
												<input type="text" id="max_range1" name="max_range1" class="form-control max_range">
											</td>
											
											<td>
												<select  id="validation1" name="validation1" class="form-control">
													<option value="Yes">Yes</option>
													<option value="No">No</option>
												</select>
											</td>
											<td><a href="#" class="btn btn-success " onclick="addRow()" style="float:right;padding: 2px;width: 30px; float:right;" ><i class="fa fa-plus"></i></a></td>
										</tr>
										
									</tbody>
								</table>
							</div>
							<div class="btn-bottom-toolbar text-right">
								
								<div class="btn-group dropup">
									
									<?php if (has_permission('qc_master', '', 'create')) {
									?>
									<button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
									<?php
										}else{
									?>
									<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
									<?php
									}?>
									
									<?php if (has_permission('qc_master', '', 'edit')) {
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
							<?php echo form_close(); ?>
						</div>
						
						<div class="row">
							<hr>
							<div class="col-md-8">
								<div class="custom_button">
									<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
									<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
									<!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
								</div>    
							</div>
							<div class="col-md-4">
								<input type="text" id="myInput1" class="form-control" onkeyup="myFunction2()" placeholder="Search" title="" style="float: right;">
							</div>
							<div class="col-md-12">
								
								<div class="tableFixHead2">
									<table class="table table-striped table-bordered tableFixHead2" width="100%" id="user_list">
										<thead>
											<tr style="display:none;">
												<th style="text-align:center;" colspan="4"><?php echo $company_detail->company_name; ?></th>
											</tr>
											<tr style="display:none;">
												<th style="text-align:center;" colspan="4"><?php echo $company_detail->address; ?></th>
											</tr>
											<tr style="display:none;">
												<th colspan="4" style="text-align:center;">QC Master </th>
											</tr>
											<tr>
												<th class="sortablePop">ItemId</th>
												<th class="sortablePop">Item Name</th>
												<th class="sortablePop">Unit</th>
												<th class="sortablePop">Sub-Group Name</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php
												foreach ($table_data as $key => $value) {
												?>
												<tr>
													<td><?php echo $value["ItemID"];?></td>
													<td><?php echo $value["description"];?></td>
													<td><?php echo $value["unit"];?></td>
													<td><?php echo $value["subgroup_name"];?></td>
													<td><button type="button" class="" Onclick="GetQCMaster('<?= $value["ItemID"];?>')"><i class="fa fa-edit"></i></button> &nbsp <button type="button" class="" Onclick="DeleteQCMaster('<?= $value["ItemID"];?>')"><i class="fa fa-trash"></i></button></td>
													
												</tr>
												<?php
												}
											?>
										</tbody>
									</table>
								</div>
								<span id="searchh3" style="display:none;">Please wait data exporting.....</span>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
    
	.tableFixHead2          { overflow: auto;max-height: 40vh;width:100%;position:relative;top: 0px; }
	.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
	.tableFixHead2 tbody th { position: sticky; left: 0; }
	
	
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
    color: #fff !important; }
    
</style>



<?php init_tail(); ?>

<script>
	$(document).ready(function(){
		
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
	});
	function cleardata(){
	    var item_id1 = $('#item_id1').val();
	    if(item_id1 == '')
	    {
			$('.addedtr').remove();
		    $('.saveBtn').show();
			$('.updateBtn').hide();
			$('.saveBtn2').show();
			$('.updateBtn2').hide();
		}
	    else
	    {
	        
	        $.ajax({
                url:"<?php echo admin_url(); ?>purchase/GetQCMasterDetailByItemID",
                dataType:"JSON",
                method:"POST",
                data:{item_id:item_id1},
                beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
                complete: function () {
					$('.searchh2').css('display','none');
				},
                success:function(data){
                    
					if(data.length > 0){
						$('.addedtr').remove();
						for(var count = 0; count < data.length; count++)
						{
							var tblid = data[count].id;
							var para_id = data[count].para_id;
							var min_range = data[count].min_range;
							var max_range = data[count].max_range;
							var validation = data[count].validation;
							
							
							var lasttr = $('#qctbody tr:last td').find("select").attr('id');
							var num= lasttr.match(/-?\d+\.?\d*/);
							var newcount = parseInt(num)+parseInt(1);
							
							var all_parameter= <?= json_encode($parameter_data)?>;
							
							markup= "<tr class='addedtr'>";
							markup += "<td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='parameter_id[]' id='parameter_id"+newcount+"' required value='"+para_id+"' required class='form-control selectpicker' data-live-search='true'></select></td>";
							markup += "<td><input type='text' name='min_range[]' id='min_range"+newcount+"' required value='"+min_range+"' class='form-control min_range'></td>";
							markup += "<td><input type='text' name='max_range[]' id='max_range"+newcount+"' required value='"+max_range+"'  class='form-control max_range'></td>";
							markup += "<td><select name='validation[]' id='validation"+newcount+"' required   class='form-control inputheight'><option value='Yes'>Yes</option><option value='No'>No</option></select></td>";
							markup += "<td></td></tr>";
							tableBody= $("#qctbody");
							tableBody.append(markup);
							
							for (var i= 0; i < all_parameter.length; i++) {
                                $("#parameter_id"+newcount).append(new Option(all_parameter[i].parameter_name, all_parameter[i].id));
							}
							
							$("#parameter_id"+newcount).val(para_id);
							$("#parameter_id"+newcount).selectpicker('refresh');
							
							$("#validation"+newcount).val(validation);
							$("#validation"+newcount).selectpicker('refresh');
							
						}
						
						$('.saveBtn').hide();
						$('.updateBtn').show();
						$('.saveBtn2').hide();
						$('.updateBtn2').show();
						
						
						}else{
						$('.addedtr').remove();
						$('.saveBtn').show();
						$('.updateBtn').hide();
						$('.saveBtn2').show();
						$('.updateBtn2').hide();
					}
				}
			});
		}
	}
	function cleardata2(){
	    var item_id1 = $('#item_id1').val('');
		$("#item_id1").children().remove();
		$("#item_id1").append('<option value="">None selected</option>');
		$("#item_id1").selectpicker('refresh');
		
	    var subgroup1 = $('#sub_group1').val('');
		$("#sub_group1").children().remove();
		$("#sub_group1").append('<option value="">None selected</option>');
		$("#sub_group1").selectpicker('refresh');
		
	    var sub_group2 = $('#sub_group2').val('');
		$("#sub_group2").children().remove();
		$("#sub_group2").append('<option value="">None selected</option>');
		$("#sub_group2").selectpicker('refresh');
		cleardata();
		var main_group = $('#main_group').val();
		if(main_group != '' && main_group != null){
			$.ajax({
				type: 'POST',
				url:'<?php echo base_url(); ?>admin/invoice_items/GetSubgroup1Data',
				data: {MainItemGroup: main_group},
				dataType:'json',
				success: function(data) {
					$("#sub_group1").find('option').remove();
					$("#sub_group1").selectpicker("refresh");
					$("#sub_group1").append(new Option('None selected', ''));
					for (var i = 0; i < data.length; i++) {
						$("#sub_group1").append(new Option(data[i].name, data[i].id));
					}
					$('.selectpicker').selectpicker('refresh');
				}
			});
			
		}
	}	
	
	$('#sub_group1').on('change', function() {
		$('#sub_group2').val('');
		$("#sub_group2").children().remove();
		$("#sub_group2").append('<option value="">None selected</option>');
		$("#sub_group2").selectpicker('refresh');
		
		$('#item_id1').val('');
		$("#item_id1").children().remove();
		$("#item_id1").append('<option value="">None selected</option>');
		$("#item_id1").selectpicker('refresh');
		cleardata();
		var SubGroup1 = $(this).val();
		//alert(roleid);
		var url = "<?php echo base_url(); ?>admin/invoice_items/GetSubgroup2Data";
		jQuery.ajax({
			type: 'POST',
			url:url,
			data: {SubGroup1: SubGroup1},
			dataType:'json',
			success: function(data) {
				$("#sub_group2").find('option').remove();
				$("#sub_group2").selectpicker("refresh");
				$("#sub_group2").append(new Option('None selected', ''));
				for (var i = 0; i < data.length; i++) {
					$("#sub_group2").append(new Option(data[i].name, data[i].id));
				}
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
	$('#sub_group2').on('change', function() {
		$('#item_id1').val('');
		$("#item_id1").children().remove();
		$("#item_id1").append('<option value="">None selected</option>');
		$("#item_id1").selectpicker('refresh');
		cleardata();
		
		var main_group = $('#main_group').val();
		var SubGroup1 =  $('#sub_group1').val();
		var SubGroup2 = $(this).val();
		//alert(roleid);
		$.ajax({
			url:"<?php echo admin_url(); ?>purchase/GetItemListbyGroups",
			dataType:"JSON",
			method:"POST",
			data:{main_group:main_group,SubGroup1:SubGroup1,SubGroup2:SubGroup2},
			beforeSend: function () {
				$('.searchh2').css('display','block');
				$('.searchh2').css('color','blue');
			},
			complete: function () {
				$('.searchh2').css('display','none');
			},
			success:function(data){
				$("#item_id1").children().remove();
				$("#item_id1").append('<option value="">Non selected</option>');
				for (var i = 0; i < data.length; i++) {
					$("#item_id1").append('<option value="'+data[i]["item_code"]+'">'+data[i]["description"]+'</option>');
				}
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
	
	function addRow(){
		var item_id= $("#item_id1").val();
		var parameter_id= $("#parameter_id1").val();
		var min_range= $("#min_range1").val();
		var max_range = $("#max_range1").val();
		var validation= $("#validation1").val();
		if(item_id != '' && parameter_id !='' && min_range !='' && max_range !='' && validation !='' ){
			var lasttr= $('#qctbody tr:last td').find("select").attr('id');
			var num= lasttr.match(/-?\d+\.?\d*/);
			var newcount= parseInt(num)+parseInt(1);
			
			var all_item = <?= json_encode($item_list) ?>;
			var all_parameter= <?= json_encode($parameter_data)?>;
			
			markup= "<tr class='addedtr'>";
			markup += "<td><select name='parameter_id[]' id='parameter_id"+newcount+"' required value='"+parameter_id+"' required class='form-control selectpicker' data-live-search='true'></select></td>";
			markup += "<td><input type='text' name='min_range[]' id='min_range"+newcount+"' required value='"+min_range+"' class='form-control min_range'></td>";
			markup += "<td><input type='text' name='max_range[]' id='max_range"+newcount+"' required value='"+max_range+"'  class='form-control max_range'></td>";
			markup += "<td><select name='validation[]' id='validation"+newcount+"' required   class='form-control inputheight'><option value='Yes'>Yes</option><option value='No'>No</option></select></td>";
			markup += "<td><a href='#' style='float:right;padding: 2px;width: 30px; float:right;' style='float:right' id='removebtn' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td></tr>";
			tableBody= $("#qctbody");
			tableBody.append(markup);
			
			for (var i= 0; i < all_parameter.length; i++) {
				$("#parameter_id"+newcount).append(new Option(all_parameter[i].parameter_name, all_parameter[i].id));
			}
			
			$("#parameter_id"+newcount).val(parameter_id);
			$("#parameter_id"+newcount).selectpicker('refresh');
			
			$("#validation"+newcount).val(validation);
			$("#validation"+newcount).selectpicker('refresh');
			
			var parameter_id= $("#parameter_id1").val('');
			var min_range= $("#min_range1").val('');
			var max_range= $("#max_range1").val('');
			var validation= $("#validation1").val('Yes');
			$('#parameter_id1').selectpicker('refresh');
			
		}
		else
		{
			alert('All Fields Are Required');
		}
		
	}
	
	function GetQCMaster(item_id1){
		$.ajax({
			url:"<?php echo admin_url(); ?>purchase/GetQCMasterDetailByItemID_edit",
			dataType:"JSON",
			method:"POST",
			data:{item_id:item_id1},
			beforeSend: function () {
				$('.searchh2').css('display','block');
				$('.searchh2').css('color','blue');
			},
			complete: function () {
				$('.searchh2').css('display','none');
			},
			success:function(data){
				
				if(data){
					$('.addedtr').remove();
					
					$('select[name=main_group]').val(data.MainGrpID);
					$('.selectpicker').selectpicker('refresh');
					
					$("#sub_group1").find('option').remove();
					$("#sub_group1").selectpicker("refresh");
					$("#sub_group1").append(new Option('None selected', ''));
					for (var i = 0; i < data.SubGroup1List.length; i++) {
						$("#sub_group1").append(new Option(data.SubGroup1List[i].name, data.SubGroup1List[i].id));
					}
					$("#sub_group1").val(data.SubGrpID1);
					$('.selectpicker').selectpicker('refresh');
					
					$("#sub_group2").find('option').remove();
					$("#sub_group2").selectpicker("refresh");
					$("#sub_group2").append(new Option('None selected', ''));
					for (var i = 0; i < data.SubGroup2List.length; i++) {
						$("#sub_group2").append(new Option(data.SubGroup2List[i].name, data.SubGroup2List[i].id));
					}
					$("#sub_group2").val(data.SubGrpID2);
					$('.selectpicker').selectpicker('refresh');
					
					$("#item_id1").children().remove();
					$("#item_id1").append('<option value="">None selected</option>');
					for (var i = 0; i < data.ItemList.length; i++) {
						$("#item_id1").append('<option value="'+data.ItemList[i]["item_code"]+'">'+data.ItemList[i]["description"]+'</option>');
					}
					$("#item_id1").val(data.item_code);
					$('.selectpicker').selectpicker('refresh');
					
					for(var count = 0; count < data.MasterList.length; count++)
					{
						var tblid = data.MasterList[count].id;
						var para_id = data.MasterList[count].para_id;
						var min_range = data.MasterList[count].min_range;
						var max_range = data.MasterList[count].max_range;
						var validation = data.MasterList[count].validation;
						
						
						var lasttr = $('#qctbody tr:last td').find("select").attr('id');
						var num= lasttr.match(/-?\d+\.?\d*/);
						var newcount = parseInt(num)+parseInt(1);
						
						var all_parameter= <?= json_encode($parameter_data)?>;
						
						markup= "<tr class='addedtr'>";
						markup += "<td><input type='hidden' name='addtblid[]' value='"+tblid+"'><select name='parameter_id[]' id='parameter_id"+newcount+"' required value='"+para_id+"' required class='form-control selectpicker' data-live-search='true'></select></td>";
						markup += "<td><input type='text' name='min_range[]' id='min_range"+newcount+"' required value='"+min_range+"' class='form-control min_range'></td>";
						markup += "<td><input type='text' name='max_range[]' id='max_range"+newcount+"' required value='"+max_range+"'  class='form-control max_range'></td>";
						markup += "<td><select name='validation[]' id='validation"+newcount+"' required   class='form-control inputheight'><option value='Yes'>Yes</option><option value='No'>No</option></select></td>";
						markup += "<td><a href='#' style='float:right;' class='btn btn-danger' onclick='DeleteQCMasterParameter(\"" + tblid + "\", \"" + data.item_code + "\")'><i class='fa fa-trash'></i></a></td></tr>";
						tableBody= $("#qctbody");
						tableBody.append(markup);
						
						for (var i= 0; i < all_parameter.length; i++) {
							$("#parameter_id"+newcount).append(new Option(all_parameter[i].parameter_name, all_parameter[i].id));
						}
						
						$("#parameter_id"+newcount).val(para_id);
						$("#parameter_id"+newcount).selectpicker('refresh');
						
						$("#validation"+newcount).val(validation);
						$("#validation"+newcount).selectpicker('refresh');
						
					}
					
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('.saveBtn2').hide();
					$('.updateBtn2').show();
					
					
					}else{
					$('.addedtr').remove();
					$('.saveBtn').show();
					$('.updateBtn').hide();
					$('.saveBtn2').show();
					$('.updateBtn2').hide();
				}
			}
		});
	}
	function DeleteQCMaster(item_id1){
		
		if(confirm("Are you sure you want to delete")){
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/DeleteQCMaster",
				dataType:"JSON",
				method:"POST",
				data:{item_id:item_id1},
				beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');
				},
				success:function(data){
					if(data == true){
						alert('Data Deleted Successfully');
						window.location.reload();
					}
				}
			});
		}
	}
	function DeleteQCMasterParameter(tblid,item_id){
		if(confirm("Are you sure you want to delete")){
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/DeleteQCMasterParameter",
				dataType:"JSON",
				method:"POST",
				data:{id:tblid},
				beforeSend: function () {
					$('.searchh2').css('display','block');
					$('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');
				},
				success:function(data){
					if(data == true){
						alert('Data Deleted Successfully');
						GetQCMaster(item_id);
					}
				}
			});
		}
	}
	
</script>
<script>
    
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("user_list");
		tr = table.getElementsByTagName("tr");
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[1];
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
	$("#caexcel").click(function(){
		var data_val = "data";
		$.ajax({
			url:"<?php echo admin_url(); ?>purchase/export_QC_Master",
			method:"POST",
			data:{data_val:data_val,},
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
<script type="text/javascript">
	$("#qctbody").on('click','.removebtn',function(){
        $(this).parent().parent().remove();
	});
	function printPage(){ 
        
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .hide_in_print{ display:none; }</style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[1].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="4"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="4"><?php echo $company_detail->address; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="4">QC Master List</td>';
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
	$('.updateBtn').on('click',function(){ 
		item_id1 = $('#item_id1').val();
		
		let parameterdataArr = [];
		var i = 1;
		var parameterdata = $("select[name='parameter_id[]']")
		.map(function(){return $(this).val();}).get();
		parameterdata.forEach(function callback(value, index) {
			if(value != "")
			{
				var addtblid = $("input[name='addtblid[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var parameter_id = $("select[name='parameter_id[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var min_range = $("input[name='min_range[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var max_range = $("input[name='max_range[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var validation = $("select[name='validation[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var ii = i - 1;
				parameterdataArr[ii]=new Array();
				parameterdataArr[ii][0]=addtblid;
				parameterdataArr[ii][1]=parameter_id;
				parameterdataArr[ii][2]=min_range;
				parameterdataArr[ii][3]=max_range;
				parameterdataArr[ii][4]=validation;
				i++;
			}
		});
		
		let parameterdataArraylength = parameterdataArr.length;
		var parameterdataSerializedArr = JSON.stringify(parameterdataArr);
		
		if(item_id1 == ''){
            alert('please Select Item');
            $('#item_id1').focus();
			}else if(parameterdataArraylength < 1){
			alert('Please Add Parameter Values')
			}else {
            $.ajax({
                url:"<?php echo admin_url(); ?>purchase/UpdateQCMaster",
                dataType:"JSON",
                method:"POST",
                data:{item_id1:item_id1,parameterdataSerializedArr:parameterdataSerializedArr
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
						alert('Record updated successfully...');
						
						$('#item_id1').val('');
						$('select[name=main_group]').val('');
						$('.selectpicker').selectpicker('refresh');
						$('select[name=item_id1]').val('');
						$('.selectpicker').selectpicker('refresh');
						
						cleardata2();
						$(".addedtr").remove();
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        window.location.reload();
						}else{
						alert_float('warning', 'Something went wrong...');
					}
				}
			});
		}
		
	});
	
	$('.saveBtn').on('click',function(){ 
		item_id1 = $('#item_id1').val();
		
		let parameterdataArr = [];
		var i = 1;
		var parameterdata = $("select[name='parameter_id[]']")
		.map(function(){return $(this).val();}).get();
		parameterdata.forEach(function callback(value, index) {
			if(value != "")
			{
				var addtblid = $("input[name='addtblid[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var parameter_id = $("select[name='parameter_id[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var min_range = $("input[name='min_range[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var max_range = $("input[name='max_range[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var validation = $("select[name='validation[]']")
				.map(function(){return $(this).val();}).get()[index];
				
				var ii = i - 1;
				parameterdataArr[ii]=new Array();
				parameterdataArr[ii][0]=addtblid;
				parameterdataArr[ii][1]=parameter_id;
				parameterdataArr[ii][2]=min_range;
				parameterdataArr[ii][3]=max_range;
				parameterdataArr[ii][4]=validation;
				i++;
			}
		});
		
		let parameterdataArraylength = parameterdataArr.length;
		var parameterdataSerializedArr = JSON.stringify(parameterdataArr);
		
		if(item_id1 == ''){
            alert('please Select Item');
            $('#item_id1').focus();
			}else if(parameterdataArraylength < 1){
			alert('Please Add Parameter Values')
			}else {
            $.ajax({
                url:"<?php echo admin_url(); ?>purchase/UpdateQCMaster",
                dataType:"JSON",
                method:"POST",
                data:{item_id1:item_id1,parameterdataSerializedArr:parameterdataSerializedArr
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
						alert('Record updated successfully...');
						
						$('#item_id1').val('');
						$('select[name=main_group]').val('');
						$('.selectpicker').selectpicker('refresh');
						$('select[name=item_id1]').val('');
						$('.selectpicker').selectpicker('refresh');
						
						cleardata2();
						$(".addedtr").remove();
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        window.location.reload();
						}else{
						alert_float('warning', 'Something went wrong...');
					}
				}
			});
		}
		
	});
	$(".cancelBtn").click(function(){
		
		
		
		$('select[name=main_group]').val('');
		$('.selectpicker').selectpicker('refresh');
		
		$('select[name=item_id1]').val('');
		$('.selectpicker').selectpicker('refresh');
		
		$('select[name=parameter_id1]').val('');
		$('.selectpicker').selectpicker('refresh');
		
		$('select[name=validation1]').val('Yes');
		$('.selectpicker').selectpicker('refresh');
		$('#min_range1').val('');
		$('#max_range1').val('');
		
		cleardata2();
		$(".addedtr").remove();            
		$('.saveBtn').show();
		$('.updateBtn').hide();
		$('.saveBtn2').show();
		$('.updateBtn2').hide();
		
	});
</script>
<script type="text/javascript">
	$('.max_range,.min_range').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
			event.preventDefault();
		}
	});
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#user_list tbody");
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
<style type="text/css">
	body{
    overflow: hidden;
	}
</style>
</body>
</html>