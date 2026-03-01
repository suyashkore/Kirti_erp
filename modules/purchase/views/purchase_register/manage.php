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
                    					<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Purchase Register</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
						<div class="_buttons">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label for="act_name">AccountID</label>
										<input type="text" name="act_name" id="act_name" class="form-control" value="<?php echo $value; ?>" <?php if(isset($cd_notes_details)){ echo "disabled";} ?>>
                                        
									</div>
								</div>
								<div class="col-md-4">
									<br>
									<div class="form-group">
										<input type="text" name="account_full_name" id="account_full_name" class="form-control" value="<?php echo $value; ?>">
									</div>
								</div>
							</div>
							
							<div class="row">
								
								<div class="col-md-2">
									<div class="form-group">
										<small class="req text-danger"> </small>
										<label class="form-label">Main Item Group</label>
										<select class="selectpicker" name="MainItemGroup" id="MainItemGroup" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
											<option value=""></option>   
											<?php
											foreach ($items_main_groups as $key => $value) {
											?>
											<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>   
											<?php   
											}
										?>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<small class="req text-danger"> </small>
										<label class="form-label">Item Subgroup1 </label>
										<select class="selectpicker" name="Subgroup" id="Subgroup" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
											<option value=""></option>   
											
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<small class="req text-danger"> </small>
										<label class="form-label">Item Subgroup2 </label>
										<select class="selectpicker" name="Subgroup2" id="Subgroup2" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
											<option value=""></option>   
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label for="act_name">ItemID</label>
										<input type="text" name="item_code" class="form-control" id="item_code">
									</div>
								</div>
								
								<div class="col-md-3">
									<br>
									<div class="form-group">
										<input type="text" name="item_fill_name" id="item_fill_name" class="form-control" value="">
									</div>
								</div>
							</div>
							
							
							<div class="row"> 
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
								<div class="col-md-2">
									
									<?php echo render_date_input('from_date','FROM',$from_date);  ?>
								</div>
								
								<div class="col-md-2">
									
									<?php echo render_date_input('to_date','TO',$to_date); ?>
								</div>
								
								<div class="col-md-2">
									<br>
									<div class="form-group">
										<select name="report_type" id="report_type" class="form-control">
											<option value="1">Detailed</option>
											<option value="2">Summary</option>
											<option value="3">ItemDetails</option>
										</select>
									</div>
								</div>
								<div class="col-md-1">
									<br>
									<div class="custom_button">
										<button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Show</button>
									</div>
								</div>
								<div class="col-md-2">
									<br>
									<div class="custom_button">
										<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
										<!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
									</div>
								</div>   
								<div class="col-md-1">
									<br>
									<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
								</div>
								
								
							</div>
							
							
						</div>
						<div class="clearfix"></div>
						
						<?php
							//print_r($company_detail);
						?>
						<span id="searchh3" style="display:none;">Please wait exporting data...</span>
						<div class="fixTableHead load_data">
							
						</div>
						<span id="searchh" style="display:none;">Loading.....</span>
						
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- AccountID List Model-->

<div class="modal fade Account_List" id="Account_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="padding:5px 10px;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Accounts List</h4>
			</div>
			<div class="modal-body" style="padding:0px 5px !important">
				
				<div class="table-Account_List tableFixHead2">
					<table class="tree table table-striped table-bordered table-Account_List tableFixHead2" id="table-Account_List" width="100%">
						<thead>
							<tr>
								<th class="sortablePop" style="text-align:left;">AccountID</th>
								<th class="sortablePop" style="text-align:left;">Account Name</th>
								<th class="sortablePop" style="text-align:left;">State</th>
								<th class="sortablePop" style="text-align:left;">City</th>
							</tr>
						</thead>
						<tbody id="ListTableBody">
							
						</tbody>
					</table>   
				</div>
			</div>
			<div class="modal-footer" style="padding:0px;">
				<input type="text" id="myInput1" onkeyup="myFunction1()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Item List Model-->

<div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="padding:5px 10px;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Item List</h4>
			</div>
			<div class="modal-body" style="padding:0px 5px !important">
				
				<div class="table-Item_List tableFixHead2">
					<table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table-Item_List" width="100%">
						<thead>
							<tr>
								<th class="sortablePop2" style="text-align:left;">ItemID</th>
								<th class="sortablePop2" style="text-align:left;">Item Name</th>
								<th class="sortablePop2" style="text-align:left;">HSN Code</th>
								<th class="sortablePop2" style="text-align:left;">Item Division</th>
								<th class="sortablePop2" style="text-align:left;">Item MainGroup</th>
								<th class="sortablePop2" style="text-align:left;">Item Group</th>
							</tr>
						</thead>
						<tbody id="ListTableBody1">
							
						</tbody>
					</table>   
				</div>
			</div>
			<div class="modal-footer" style="padding:0px;">
				<input type="text" id="myInput2" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<style>
	
	#table-Account_List td:hover {
    cursor: pointer;
	}
	#table-Account_List tr:hover {
    background-color: #ccc;
	}
	
	#table-Item_List td:hover {
    cursor: pointer;
	}
	#table-Item_List tr:hover {
    background-color: #ccc;
	}
	
	.table-Account_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
	.table-Account_List thead th { position: sticky; top: 0; z-index: 1; }
	.table-Account_List tbody th { position: sticky; left: 0; }
	
	.table-Item_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
	.table-Item_List thead th { position: sticky; top: 0; z-index: 1; }
	.table-Item_List tbody th { position: sticky; left: 0; }
	
	table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
	
	.fixTableHead  { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
	.fixTableHead thead th { position: sticky; top: 0; z-index: 1; }
	.fixTableHead tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.fixTableHead table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	.fixTableHead th     { background: #50607b;color: #fff !important; }
</style>

<?php init_tail(); ?>
<!--new update -->
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		$("#act_name").focus(function(){
			$('#act_name').val('');
			$('#account_full_name').val('');
			$("#report_type").children().remove();
			// APPEND OR INSERT DATA TO SELECT ELEMENT.
			
			if($('#Subgroup').val() == '' && $('#item_code').val() == ''){
				$('#report_type').append('<option value="1">Detailed</option>');
				$('#report_type').append('<option value="2">Summary</option>');
			}
			$('#report_type').append('<option value="3">ItemDetails</option>');
			$("#report_type").selectpicker("refresh");
			$('.load_data').html('');
		});
		$("#item_code").focus(function(){
			$('#item_code').val('');
			$('#item_fill_name').val('');
			$("#report_type").children().remove();
			// APPEND OR INSERT DATA TO SELECT ELEMENT.
			if($('#Subgroup').val() == ''){
				$('#report_type').append('<option value="1">Detailed</option>');
				$('#report_type').append('<option value="2">Summary</option>');
			}
			$('#report_type').append('<option value="3">ItemDetails</option>');
			$("#report_type").selectpicker("refresh");
			$('.load_data').html('');
		});
		$("#act_name").dblclick(function(){
			//$('#myInput1').focus();
			$('#Account_List').modal('show');
			$('#Account_List').on('shown.bs.modal', function () {
				$('#myInput1').val('');
				$('#myInput1').focus();
			})
			var AccountID = "";
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/AccountListPopUp",
				method:"POST",
				cache: false,
				data:{AccountID:AccountID,},
				success:function(data){
					if(empty(data)){
						
						}else{
						$("#ListTableBody").html(data);
						$('.get_AccountID').on('click',function(){ 
							AccountID = $(this).attr("data-id");
							
							$.ajax({
								url:"<?php echo admin_url(); ?>purchase/GetAccountDetailByID",
								dataType:"JSON",
								method:"POST",
								data:{AccountID:AccountID},
								beforeSend: function () {
									$('.searchh2').css('display','block');
									$('.searchh2').css('color','blue');
								},
								complete: function () {
									$('.searchh2').css('display','none');
								},
								success:function(data){
									$('#act_name').val(data.AccountID);
									$('#account_full_name').val(data.company);
									var ItemID = $('#item_code').val();
									if(ItemID !==""){
										$("#report_type").children().remove();
										// APPEND OR INSERT DATA TO SELECT ELEMENT.
										$('#report_type').append('<option value="3">ItemDetails</option>');
										$("#report_type").selectpicker("refresh");
									}
								}
							});
							$('#Account_List').modal('hide');
						});
					}
				}
			});
		});
		
		$("#item_code").dblclick(function(){
			//$('#myInput1').focus();
			$('#Item_List').modal('show');
			$('#Item_List').on('shown.bs.modal', function () {
				$('#myInput2').val('');
				$('#myInput2').focus();
			})
			var MainGroupID = $("#MainItemGroup").val();
			var Subgroup = $("#Subgroup").val();
			var Subgroup2 = $("#Subgroup2").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/ItemListPopUp",
				method:"POST",
				cache: false,
				data:{MainGroupID:MainGroupID,Subgroup:Subgroup,Subgroup2:Subgroup2},
				success:function(data){
					if(empty(data)){
                        
					}else{
						$("#ListTableBody1").html(data);
						$('.get_ItemID').on('click',function(){ 
							ItemID = $(this).attr("data-id");
							
							$.ajax({
								url:"<?php echo admin_url(); ?>purchase/GetItemDetailByID",
								dataType:"JSON",
								method:"POST",
								data:{ItemID:ItemID},
								beforeSend: function () {
									$('.searchh2').css('display','block');
									$('.searchh2').css('color','blue');
								},
								complete: function () {
									$('.searchh2').css('display','none');
								},
								success:function(data){
									$('#item_code').val(data.item_code);
									$('#item_fill_name').val(data.description);
									$("#report_type").children().remove();
									// APPEND OR INSERT DATA TO SELECT ELEMENT.
									$('#report_type').append('<option value="3">ItemDetails</option>');
									$("#report_type").selectpicker("refresh");
								}
							});
							$('#Item_List').modal('hide');
						});
					}
				}
			});
		});
		
		// Initialize For Account
		$( "#act_name" ).autocomplete({
			
			source: function( request, response ) {
				$.ajax({
					url: "<?=base_url()?>admin/purchase/accountlist",
					type: 'post',
					dataType: "json",
					data: {
						search: request.term
					},
					success: function( data ) {
						response( data );
					}
				});
			},
			select: function (event, ui) {
				
				
				$('#act_name').val(ui.item.value); // display the selected text
				$('#account_full_name').val(ui.item.label); // display the selected text
				
				return false;      
				
			}
		});
		
		$('#act_name').on('blur',function(){
			
			var act_id = $(this).val();
			if(act_id == ""){
				
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>purchase/get_account_details",
					dataType:"JSON",
					method:"POST",
					cache: false,
					data:{act_id:act_id,},
					
					success:function(data){
						var ItemID = $('#item_code').val();
						if (data == 0 || data == null){
							if(act_id !== ""){
								alert("AccountID not found..."); 
							}
							$('#act_name').val('');
							$('#account_full_name').val('');
							if(ItemID == ""){
								$("#report_type").children().remove();
								// APPEND OR INSERT DATA TO SELECT ELEMENT.
								
								if($('#Subgroup').val() == ''){
									$('#report_type').append('<option value="1">Detailed</option>');
									$('#report_type').append('<option value="2">Summary</option>');
								}
								$('#report_type').append('<option value="3">ItemDetails</option>');
								$("#report_type").selectpicker("refresh");
							}
							
							}else{
							$('#account_full_name').val(data.company);
							
							$("#report_type").children().remove();
							// APPEND OR INSERT DATA TO SELECT ELEMENT.
							$('#report_type').append('<option value="3">ItemDetails</option>');
							
							$("#report_type").selectpicker("refresh");
							$('#search_data').focus();
							
						}
						
						$('#account_full_name').val(data.company);
						$('#search_data').focus();
						
					}
				});
			}
		})
		
		$('#item_code').on('blur',function(){
			
			var ItemID = $(this).val();
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/get_item_details",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{ItemID:ItemID,},
				
				success:function(data){
					var AccounID = $('#act_name').val();
					if (data == 0 || data == null){
						if(ItemID !== ""){
							alert("ItemID not found...");
						}
						
						$('#item_code').val('');
						$('#item_fill_name').val('');
						if(AccounID == ""){
							$("#report_type").children().remove();
							// APPEND OR INSERT DATA TO SELECT ELEMENT.
							if($('#Subgroup').val() == ''){
								$('#report_type').append('<option value="1">Detailed</option>');
								$('#report_type').append('<option value="2">Summary</option>');
							}
							$('#report_type').append('<option value="3">ItemDetails</option>');
							$("#report_type").selectpicker("refresh");
						}
						
						}else{
						$('#item_fill_name').val(data.description);
						
						$("#report_type").children().remove();
						// APPEND OR INSERT DATA TO SELECT ELEMENT.
						$('#report_type').append('<option value="3">ItemDetails</option>');
						
						$("#report_type").selectpicker("refresh");
						$('#search_data').focus();
						
					}
					
					
				}
			});
			
		})
		
		$('#report_type').on('change',function(){
			var report_type = $(this).val();
			if(report_type == 3){
				var ItemID = $('#item_code').val();
				var AccounID = $('#act_name').val();
				var MainItemGroup = $('#MainItemGroup').val();
				if(ItemID == "" && MainItemGroup == ""){
					alert("Enter ItemID Or Select Main Group"); 
					$("#report_type").children().remove();
					// APPEND OR INSERT DATA TO SELECT ELEMENT.
					$('#report_type').append('<option value="1">Detailed</option>');
					$('#report_type').append('<option value="2">Summary</option>');
					$('#report_type').append('<option value="3">ItemDetails</option>');
					$("#report_type").selectpicker("refresh");
				}
			}
		});
		$('#MainItemGroup').on('change', function() {
			var MainItemGroup = $(this).val();
			if(MainItemGroup){
			    $("#report_type").children().remove();
    			// APPEND OR INSERT DATA TO SELECT ELEMENT.
    			$('#report_type').append('<option value="3">ItemDetails</option>');
    			$("#report_type").selectpicker("refresh");
    			var url = "<?php echo base_url(); ?>admin/invoice_items/GetSubgroup1Data";
    			jQuery.ajax({
    				type: 'POST',
    				url:url,
    				data: {MainItemGroup: MainItemGroup},
    				dataType:'json',
    				success: function(data) {
    					$("#Subgroup").find('option').remove();
    					$("#Subgroup").selectpicker("refresh");
    					$("#Subgroup").append(new Option('None selected', ''));
    					for (var i = 0; i < data.length; i++) {
    						$("#Subgroup").append(new Option(data[i].name, data[i].id));
    					}
    					$('.selectpicker').selectpicker('refresh');
    				}
    			});
			}else{
			    $("#Subgroup2").children().remove();
			    $("#Subgroup2").selectpicker("refresh");
			    $("#report_type").children().remove();
				// APPEND OR INSERT DATA TO SELECT ELEMENT.
				$('#report_type').append('<option value="1">Detailed</option>');
				$('#report_type').append('<option value="2">Summary</option>');
				$('#report_type').append('<option value="3">ItemDetails</option>');
				$("#report_type").selectpicker("refresh");
			}
		});
		$('#Subgroup').on('change',function(){
			var Subgroup = $(this).val();
			$("#Subgroup2").children().remove();
			$("#Subgroup2").selectpicker("refresh");
			if(Subgroup !== ""){
    			var url = "<?php echo base_url(); ?>admin/invoice_items/GetSubGroup2ByGroupID";
    			jQuery.ajax({
    				type: 'POST',
    				url:url,
    				data: {SubGroup1: Subgroup},
    				dataType:'json',
    				success: function(data) {
    					$("#Subgroup2").find('option').remove();
    					$("#Subgroup2").selectpicker("refresh");
    					$("#Subgroup2").append(new Option('None selected', ''));
    					for (var i = 0; i < data.length; i++) {
    						$("#Subgroup2").append(new Option(data[i].name, data[i].id));
    					}
    					$('.selectpicker').selectpicker('refresh');
    				}
    			});
			}
		});
		
		// Initialize For Account
		$( "#item_code" ).autocomplete({
			source: function( request, response ) {
			    var MainGroupID = $("#MainItemGroup").val();
			    var Subgroup = $("#Subgroup").val();
			    var Subgroup2 = $("#Subgroup2").val();
				$.ajax({
					url: "<?=base_url()?>admin/purchase/itemlist",
					type: 'post',
					dataType: "json",
					data: {
						search: request.term,Subgroup2:Subgroup2,Subgroup:Subgroup,MainGroupID:MainGroupID
					},
					success: function( data ) {
						response( data );
					}
				});
			},
			select: function (event, ui) {
				
				
				$('#item_code').val(ui.item.value); // display the selected text
				$('#item_fill_name').val(ui.item.label); // display the selected text
				$('#search_data').focus();
				return false;      
				
			}
		});
		
		$('#search_data').on('click',function(){
			var from_date = $("#from_date").val();
			var to_date = $("#to_date").val();
			var report_type = $("#report_type").val();
			var accountID = $("#act_name").val();
			var accountName = $("#account_full_name").val();
			var ItemID = $("#item_code").val();
			var Itemname = $("#item_fill_name").val();
			var MainGroupID = $("#MainItemGroup").val();
			var Subgroup = $("#Subgroup").val();
			var Subgroup2 = $("#Subgroup2").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>purchase/get_purchase_data",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{from_date:from_date, to_date:to_date, report_type:report_type,accountID:accountID,
				ItemID:ItemID,accountName:accountName,Itemname:Itemname,
				MainGroupID:MainGroupID,Subgroup:Subgroup,Subgroup2:Subgroup2},
				beforeSend: function () {
					
					$('#searchh').css('display','block');
					$('.load_data').css('display','none');
					
				},
				complete: function () {
					$('.load_data').css('display','');
					$('#searchh').css('display','none');
				},
				success:function(data){
					$('.load_data').html(data);
				}
			});
			
		});
	});
	
	$("#caexcel").click(function(){
        var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		var report_type = $("#report_type").val();
		var accountID = $("#act_name").val();
		var accountName = $("#account_full_name").val();
		var ItemID = $("#item_code").val();
		var Itemname = $("#item_fill_name").val();
		var MainGroupID = $("#MainItemGroup").val();
		var Subgroup = $("#Subgroup").val();
		var Subgroup2 = $("#Subgroup2").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>purchase/export_purchase_register",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, report_type:report_type,accountID:accountID,
				ItemID:ItemID,accountName:accountName,Itemname:Itemname,
				MainGroupID:MainGroupID,Subgroup:Subgroup,Subgroup2:Subgroup2},
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
<style>
    input[type=checkbox], input[type=radio] {
    margin: 4px 4px 0px;
    line-height: normal;
	}
</style>
<script type="text/javascript">
	function printPage(){
		
		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .print_item_h{ background: #505f7b;colr:#fff;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
		var print_data = stylesheet+tableData
		newWin= window.open("");
		newWin.document.write(print_data);
		newWin.print();
		newWin.close();
	};
</script>
<script>
    function myFunction1() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
		
        filter = input.value.toUpperCase();
        table = document.getElementById("table-Account_List");
        tr = table.getElementsByTagName("tr");
        for (i = 2; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					}else if(td1) {
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						}else if(td2) {
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else if(td3) {
							txtValue = td3.textContent || td3.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
								}else {
								tr[i].style.display = "none";
							}
						}       
					}
				}}
		}
	}
    

    
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput2");
		
        filter = input.value.toUpperCase();
        table = document.getElementById("table-Item_List");
        tr = table.getElementsByTagName("tr");
        for (i = 2; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            td2 = tr[i].getElementsByTagName("td")[2];
            td3 = tr[i].getElementsByTagName("td")[3];
            td4 = tr[i].getElementsByTagName("td")[4];
            td5 = tr[i].getElementsByTagName("td")[5];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					}else if(td1) {
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						}else if(td2) {
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else if(td3) {
							txtValue = td3.textContent || td3.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
								}else if(td4) {
								txtValue = td4.textContent || td4.innerText;
								if (txtValue.toUpperCase().indexOf(filter) > -1) {
									tr[i].style.display = "";
									}else if(td5) {
									txtValue = td5.textContent || td5.innerText;
									if (txtValue.toUpperCase().indexOf(filter) > -1) {
										tr[i].style.display = "";
										}else {
										tr[i].style.display = "none";
									}
								}       
							}
						}}}}
		}
	}
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
    var table = $("#daily_report tbody");
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

        // Check if both values are valid dates in dd/mm/yyyy
        var dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;

        var isDate = dateRegex.test(valA) && dateRegex.test(valB);
        if (isDate) {
            var partsA = valA.split('/');
            var partsB = valB.split('/');

            var dateA = new Date(partsA[2], partsA[1] - 1, partsA[0]); // yyyy, mm-1, dd
            var dateB = new Date(partsB[2], partsB[1] - 1, partsB[0]);

            return ascending ? dateA - dateB : dateB - dateA;
        }

        // Check numeric comparison
        if ($.isNumeric(valA) && $.isNumeric(valB)) {
            return ascending ? valA - valB : valB - valA;
        }

        // Default string comparison
        return ascending
            ? valA.localeCompare(valB)
            : valB.localeCompare(valA);
    });

    table.append(rows);
});

	});
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table-Account_List tbody");
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
	$(document).on("click", ".sortablePop2", function () {
		var table = $("#table-Item_List tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop2").removeClass("asc desc");
		$(".sortablePop2 span").remove();
		
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


