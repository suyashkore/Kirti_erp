<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
        <div class="row" style="display:none;">
			<div class="col-md-12">
				<table id="print_table" style="border-bottom:none;">
					<thead>
						<tr>
							<th align="center" colspan="5"><?php echo $company_detail->company_name; ?></th>
						</tr>
						<tr>
							<th align="center" colspan="5"><?php echo $company_detail->address; ?></th>
						</tr>
					</thead>
					<tbody id="print_tablebody">
					</tbody>
					
				</table>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Stock Transfer</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
						<div class="row">
							<div class="col-md-12">
								<div class="searchh" style="display:none;">Please wait Deleting data...</div>
								<div class="searchh2" style="display:none;">Please wait fetching data...</div>
								<div class="searchh3" style="display:none;">Please wait Create new Stock transfer...</div>
								<div class="searchh4" style="display:none;">Please wait update Stock transfer...</div>
							</div>
							<div class="col-md-4">
								<?php
									$selected_company = $this->session->userdata('root_company');
									$fy = $this->session->userdata('finacial_year');
									if($selected_company == 1){
										$new_TRNSNumber = get_option('next_trns_number_for_cspl');
										}elseif($selected_company == 2){
										$new_TRNSNumber = get_option('next_trns_number_for_cff');
										}elseif($selected_company == 3){
										$new_TRNSNumber = get_option('next_trns_number_for_cbu');
										}elseif($selected_company == 4){
										$new_TRNSNumber = get_option('next_trns_number_for_cbupl');
									}
									$format = get_option('invoice_number_format');
									$prefix = "TRS".$fy;
									$_newTRNSNumber = str_pad($new_TRNSNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
								?>
								<div class="form-group">
									<label for="AccountID">TrnsID</label>
									<input type="text" name="TransID" id="TransID" class="form-control TransID" value="<?php echo $prefix.$_newTRNSNumber; ?>">
									<input type="hidden" name="TransIDHidden" id="TransIDHidden" value="<?php echo $prefix.$_newTRNSNumber; ?>">
									<input type="hidden" name="row_count" value="0" id="row_count">      
									<input type="hidden" name="updated_record" value=" " id="updated_record">
									<input type="hidden" name="new_record" value=" " id="new_record">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<?php $value = date('d/m/Y');?>
									<?php echo render_date_input( 'Transdate', 'Date',$value,'text'); ?>
								</div>
							</div>
							<div class="clearfix"></div>
							
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="TrnsFrom">
									<label for="TrnsFrom" class="form-label">TransferFrom</label>
									<select name="TrnsFrom" id="TrnsFrom" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<!--<option value="">Non Selected</option>-->
										<?php
											foreach ($TableData as $key => $value) {
											?>
											<option value="<?php echo $value['AccountID'];?>"><?php echo $value['AccountName'];?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
							
							<div class="col-md-4">
								<div class="form-group" app-field-wrapper="TrnsTo">
									<label for="TrnsTo" class="form-label">TransferTo</label>
									<select name="TrnsTo" id="TrnsTo" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
										<option value="">Non Selected</option>
										<?php
											foreach ($TableData as $key => $value) {
											?>
											<option value="<?php echo $value['AccountID'];?>"><?php echo $value['AccountName'];?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
							<input type="hidden" class="form-control" name="PRDR_ItemID_list" id="PRDR_ItemID_list" value="">
							<input type="hidden" value="1" name="countof_record" id="countof_record">
						</div>
						
						
						<div class="row">
							<div class="col-md-12">
								<table class="table table-striped table-bordered ItemTable" id="ItemTable" width="100%">
									<thead>
										<tr>
											<th>ItemID</th>
											<th>ItemName</th>
											<th>Pack</th>
											<th>Unit</th>
											<th>Stock(Unit)</th>
											<th>Qty(Unit)</th>
											<th>Case Qty</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody id="tbody">
										<tr class="accounts" id="row">
											<td id="AccountIDTD" style="width: 125px;"><input type="text" name="ItemID" style="width: 125px;" id="ItemID"></td>
											<td style="padding:1px 5px !important;"><span id="ItemName"></span><input type="hidden" name="ItemName_val" id="ItemName_val" value="" ></td>
											<td style="padding:1px 5px !important;text-align: center;"><span id="Pack"></span><input type="hidden" name="Pack_val" id="Pack_val" value="" ></td>
											<td style="padding:1px 5px !important;text-align: center;"><span id="Unit"></span><input type="hidden" name="Unit_val" id="Unit_val"></td>
											<td style="padding:1px 5px !important;text-align: center;"><span id="stock"></span><input type="hidden" name="stock_val" id="stock_val"></td>
											<td class="qty" style="width: 80px;">
												<input type="text" name="qty_val" id="qty_val" style="width: 80px;text-align: right;" >
												<input type="hidden" name="colno" id="colno" value="">
											</td>
											<td class="caseqty" style="width: 80px;"></td>
											<td class="actionbtn" style="width: 80px;"></td>
											
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						
						<div class="row"> 
							<div class="col-md-12">
								<?php if (has_permission_new('StockTransfer', '', 'create')) {
								?>
								<button type="button" class="btn btn-info saveBtn" id= 'AddTrans' style="margin-right: 25px;">Save</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
								<?php
								}?>
								
								<?php if (has_permission_new('StockTransfer', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info updateBtn" id="updateBtn" style="margin-right: 25px;">Update</button>
								<?php
									}else{
								?>
								<button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
								<?php
								}?>
								
								<button type="button" class="btn btn-default cancelBtn" style="margin-right: 25px;">Cancel</button>
								<a class="btn btn-default printbtn" href="javascript:void(0);" onclick="printPage();">Print</a>
								<?php
									$staffID = $this->session->userdata('staff_user_id');
									if($staffID == '4'){
									?>
									<button type="button" class="btn btn-danger DeleteBtn" style="margin-right: 25px;">Delete</button>
									<?php
									}
								?>
								
							</div>
						</div>
						
						
						
						<div class="clearfix"></div>
						
						<div class="modal fade Item_List" id="Item_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Item List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
										
										<div class="table-Item_List tableFixHead2">
											<table class="tree table table-striped table-bordered table-Item_List tableFixHead2" id="table_Item_List" width="100%">
												<thead>
													<tr>
														<th style="text-align:left;" class="sortablePop2">Item Code</th>
														<th style="text-align:left;" class="sortablePop2">Item Name</th>
														<th style="text-align:left;" class="sortablePop2">MeasuredIn</th>
														<th style="text-align:left;" class="sortablePop2">Division Name</th>
														<th style="text-align:left;" class="sortablePop2">Group Name</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($Itemdata as $key => $value) {
														?>
														<tr class="get_ItemID" data-id="<?php echo $value["item_code"]; ?>">
															<td><?php echo $value['item_code'];?></td>
															<td><?php echo $value['description'];?></td>
															<td><?php echo $value["unit"];?></td>
															<td><?php echo $value["group_name"];?></td>
															<td><?php echo $value["subgroup_name"];?></td>
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
						
						<!--Transfer Record-->
						<div class="modal fade Trans_List" id="Trans_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header" style="padding:5px 10px;">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title">Transfer List</h4>
									</div>
									<div class="modal-body" style="padding:0px 5px !important">
										
										<div class="table-Trans_List tableFixHead">
											<table class="tree table table-striped table-bordered table-Trans_List tableFixHead" id="table_Trans_List" width="100%">
												<thead>
													<tr>
														<th style="text-align:left;" class="sortablePop">TransID</th>
														<th style="text-align:left;" class="sortablePop">TransDate</th>
														<th style="text-align:left;" class="sortablePop">From</th>
														<th style="text-align:left;" class="sortablePop">To</th>
													</tr>
												</thead>
												<tbody>
													<?php
														foreach ($TransData as $key => $value) {
														?>
														<tr class="get_Trans" data-id="<?php echo $value["TransID"]; ?>">
															<td><?php echo $value['TransID'];?></td>
															<td><?php echo $value['Transdate'];?></td>
															<td><?php echo $value["TransFrom"];?></td>
															<td><?php echo $value["TransTo"];?></td>
														</tr>
													<?php } ?>
												</tbody>
											</table>   
										</div>
									</div>
									<div class="modal-footer" style="padding:0px;">
										<input type="text" id="myInput2" onkeyup="myFunction3()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
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
<!--new update -->

<style>
    
    input[type=text]{
	height: 29px !important;
    }
</style>

<script type="text/javascript">
	$('#qty_val').on('keypress',function (event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
		var input = $(this).val();
		if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
			event.preventDefault();
		}
	});
</script>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		
		$("#ItemID" ).autocomplete({
			source: function( request, response ) {
				// Fetch data
				$.ajax({
					url: "<?=base_url()?>admin/GodownMaster/itemlist",
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
                
                $('#ItemID').val(ui.item.value);
			}
		}); 
		// blur from ItemID 
		$('#ItemID').on('blur',function(){
			
			var ItemID = $(this).val();
			var FromID = $('#TrnsFrom').val();
			var TrnsTo = $('#TrnsTo').val();
			if(ItemID == "" || ItemID == null){
				
				}else{
				if(FromID == "" || FromID == null){
					alert('please select Transfer from..');
					$('#ItemID').val('');
					}else{
					if(TrnsTo == "" || TrnsTo == null){
						alert('please select Transfer to..');
						$('#ItemID').val('');
						}else{
						var PRDR_ItemID_list = $("#PRDR_ItemID_list").val();
						let PRDR_ItemID_list_array = PRDR_ItemID_list.split(",");
						if(PRDR_ItemID_list_array.includes(ItemID.toUpperCase())){
							alert("item already added");
                            $('#ItemID').val('');
                            $('#ItemName_val').val('');
                            $('#Pack_val').val("");
                            $('#Unit_val').val("");
                            $('#stock_val').val("");
                            $('#ItemName').html('');
                            $('#Pack').html("");
                            $('#Unit').html("");
                            $('#stock').html("");
                            $('#ItemID').focus();
							}else{
							$.ajax({
								url:"<?php echo admin_url(); ?>GodownMaster/GetItemDetails",
								dataType:"JSON",
								method:"POST",
								cache: false,
								data:{ItemID:ItemID,FromID:FromID},
								
								success:function(data){
									if(empty(data)){
										alert('Item not found...');
										$('#ItemID').val('');
										$('#ItemName_val').val('');
										$('#Pack_val').val("");
										$('#Unit_val').val("");
										$('#stock_val').val("");
										$('#ItemName').html('');
										$('#Pack').html("");
										$('#Unit').html("");
										$('#stock').html("");
										$('#ItemID').focus();
										}else{
										
										$('#ItemID').val(data.item_code);
										$('#ItemName_val').val(data.Name);
										$('#ItemName').html(data.Name);
										$('#Pack_val').val(data.case_qty);
										$('#Pack').html(data.case_qty);
										$('#Unit_val').val(data.unit);
										$('#Unit').html(data.unit);
										// var StockInCases = parseFloat(data.ItemStocks) / parseFloat(data.case_qty);
										var StockInCases = parseFloat(data.ItemStocks);
										$('#stock_val').val(parseFloat(StockInCases).toFixed(2));
										$('#stock').html(parseFloat(StockInCases).toFixed(2));
										$('#qty_val').focus();
									}
								}
							});
						}
					}
				}
			}
		}) 
		
		// blur from ItemID 
		$('.get_ItemID').on('click',function(){
			
			var ItemID = $(this).attr("data-id");
			var FromID = $('#TrnsFrom').val();
			var TrnsTo = $('#TrnsTo').val();
			if(ItemID == "" || ItemID == null){
				
				}else{
				if(FromID == "" || FromID == null){
					alert('please select Transfer from..');
					$('#ItemID').val('');
					}else{
					if(TrnsTo == "" || TrnsTo == null){
						alert('please select Transfer to..');
						$('#ItemID').val('');
						}else{
						var PRDR_ItemID_list = $("#PRDR_ItemID_list").val();
						let PRDR_ItemID_list_array = PRDR_ItemID_list.split(",");
						if(PRDR_ItemID_list_array.includes(ItemID.toUpperCase())){
							alert("item already added");
                            $('#ItemID').val('');
                            $('#ItemName_val').val('');
                            $('#Pack_val').val("");
                            $('#Unit_val').val("");
                            $('#stock_val').val("");
                            $('#ItemName').html('');
                            $('#Pack').html("");
                            $('#Unit').html("");
                            $('#stock').html("");
                            $('#ItemID').focus();
							}else{
							$.ajax({
								url:"<?php echo admin_url(); ?>GodownMaster/GetItemDetails",
								dataType:"JSON",
								method:"POST",
								cache: false,
								data:{ItemID:ItemID,FromID:FromID},
								
								success:function(data){
									if(empty(data)){
										alert('Item not found...');
										$('#ItemID').val('');
										$('#ItemName_val').val('');
										$('#Pack_val').val("");
										$('#Unit_val').val("");
										$('#stock_val').val("");
										$('#ItemName').html('');
										$('#Pack').html("");
										$('#Unit').html("");
										$('#stock').html("");
										$('#ItemID').focus();
										}else{
										
										$('#ItemID').val(data.item_code);
										$('#ItemName_val').val(data.Name);
										$('#ItemName').html(data.Name);
										$('#Pack_val').val(data.case_qty);
										$('#Pack').html(data.case_qty);
										$('#Unit_val').val(data.unit);
										$('#Unit').html(data.unit);
										var StockInCases = parseFloat(data.ItemStocks) / parseFloat(data.case_qty);
										$('#stock_val').val(parseFloat(StockInCases).toFixed(2));
										$('#stock').html(parseFloat(StockInCases).toFixed(2));
										$('#qty_val').focus();
									}
								}
							});
							$('#Item_List').modal('hide');
						}
					}
				}
			}
		})
		
		// Focus from ItemID 
		$('#ItemID').on('focus',function(){
			$('#ItemID').val('');
			$('#ItemName_val').val('');
			$('#Pack_val').val("");
			$('#Unit_val').val("");
			$('#stock_val').val("");
			$('#ItemName').html('');
			$('#Pack').html("");
			$('#Unit').html("");
			$('#stock').html("");
			$('#qty_val').val("");
			$('#caseqty_val').val("");
		})
		
		$('#qty_val').on('keypress',function (event) {
			var unit = $('#Unit_val').val();
			if(unit == "Kgs"){
				if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
					event.preventDefault();
				}
				var input = $(this).val();
				if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
					event.preventDefault();
				}
				}else if(unit == "Pcs"){
				event = (event) ? event : window.event;
				var charCode = (event.which) ? event.which : event.keyCode;
				if (charCode > 31 && (charCode < 48 || charCode > 57)) {
					return false;
				}
				return true;
			}
		});
		
		$('#qty_val').on('blur', function () {
			
			var stock_val = $("#stock_val").val();
			var Unit_val = $("#Unit_val").val();
			var Pack_val = $("#Pack_val").val();
			var ItemName_val = $("#ItemName_val").val();
			var ItemID = $("#ItemID").val();
			var qty_val = $(this).val();
			//alert(item_id);
			if(ItemID == "" || ItemID == null ){
				//alert("Select ItemID.");
				$('#ItemID').focus();
				}else if(ItemName_val == "" || ItemName_val == null ){
				alert("Select Item Name.");
				$('#ItemID').focus();
				}else if(qty_val == "" || qty_val == null ){
				alert("Please Add Quantity.");
				
				}else if(parseFloat(qty_val) > parseFloat(stock_val)){
				alert('Item Stock not available...');
				
				}else{
				add_row();
			}  
		});
		
		function add_row()
		{  
			var ItemID =document.getElementById("ItemID").value;
			
			var ItemName_val =document.getElementById("ItemName_val").value;
			var Pack_val =document.getElementById("Pack_val").value;
			var Unit_val =document.getElementById("Unit_val").value;
			var stock_val =document.getElementById("stock_val").value;
			var qty_val =document.getElementById("qty_val").value;
			
			var CaseQty = parseFloat(qty_val) / parseFloat(Pack_val);
			CaseQty = parseFloat(CaseQty).toFixed(2);
			var countof_record = document.getElementById("countof_record").value;
			
			var table = document.getElementById("ItemTable");
			var table_len=(table.rows.length)-1;
			var html = '';
			html += "<tr id='row"+table_len+"'>";
			html += "<td id='ItemID"+table_len+"'>"+ItemID+" <input type='hidden' name='ItemID_val"+table_len+"' id='ItemID_val"+table_len+"' value='"+ItemID+"'></td>";
			html += "<td id='ItemName"+table_len+"'>"+ItemName_val+" <input type='hidden' name='ItemName_val"+table_len+"' id='ItemName_val"+table_len+"' value='"+ItemName_val+"'></td>";
			html += "<td id='Pack"+table_len+"' style='text-align: center;'>"+Pack_val+" <input type='hidden' name='Pack_val"+table_len+"' id='Pack_val"+table_len+"' value='"+Pack_val+"'></td>";
			html += "<td id='Unit"+table_len+"' style='text-align: center;'>"+Unit_val+" <input type='hidden' name='Unit_val"+table_len+"' id='Unit_val"+table_len+"' value='"+Unit_val+"'></td>";
			html += "<td id='stock"+table_len+"' style='text-align: center;'>"+stock_val+" <input type='hidden' name='stock_val"+table_len+"' id='stock_val"+table_len+"' value='"+stock_val+"'></td>";
			html += "<td id='qty"+table_len+"' style='text-align: center;'>"+qty_val+" <input type='hidden' name='qty_val"+table_len+"' id='qty_val"+table_len+"' value='"+qty_val+"'></td>";
			html += "<td id='caseqty"+table_len+"' style='text-align: center;'>"+CaseQty+" <input type='hidden' name='caseqty_val"+table_len+"' id='caseqty_val"+table_len+"' value='"+CaseQty+"'></td>";
			html += '<td><button type="button" name="edit" id="remove" class="btn btn-xs btn-danger remove" value="remove"><i class="fa fa-trash " style="font-size:16px;"></i></button><input type="hidden" name="rownum" id="rownum" value="'+countof_record+'"></td>';
			html += '</tr>';
			var row = table.insertRow(table_len).outerHTML=html;
			var temp1 = parseFloat(countof_record) + parseFloat(1);
			document.getElementById("countof_record").value=temp1;
			
			var new_rec = $('#PRDR_ItemID_list').val();
			new_rec = new_rec +","+ ItemID
			$('#PRDR_ItemID_list').val(new_rec);
			
			$(this).parents("tr").find(".edit_row").show();  
			$(this).parents("tr").find(".btn-cancel").remove();  
			$(this).parents("tr").find(".btn-update").remove();
			
			document.getElementById("ItemID").value="";
			document.getElementById("ItemName_val").value="";
			document.getElementById("Pack_val").value="";
			document.getElementById("Unit_val").value="";
			document.getElementById("stock_val").value="";
			document.getElementById("qty_val").value="";
			
			document.getElementById("ItemName").innerHTML="";
			document.getElementById("Pack").innerHTML="";
			document.getElementById("Unit").innerHTML="";
			document.getElementById("stock").innerHTML="";
			
			document.getElementById("ItemID").focus(); 
		}
		
		$('#tbody').on('click', '.remove', function () {
			// Getting all the rows next to the 
			// row containing the clicked button
			var child = $(this).closest('tr').nextAll();
			
			// Iterating across all the rows 
			// obtained to change the index
			child.each(function () {
				// Getting <tr> id.
				var id = $(this).attr('id');
				// Getting the <p> inside the .row-index class.
				var idx = $(this).children('.row-index').children('p');
				// Gets the row number from <tr> id.
				var dig = parseInt(id.substring(1));
				// Modifying row index.
				idx.html(`Row ${dig - 1}`);
				// Modifying row id.
				$(this).attr('id', `R${dig - 1}`);
			});
			var  no = $(this).parents("tr").find('input[name="rownum"]').val();
			var ItemID =$(this).parents("tr").find('input[name="ItemID_val'+no+'"]').val();
			
			// Removing the current row.
			$(this).closest('tr').remove();
			
			// Decreasing the total number of rows by 1.
			var countof_record = $("#countof_record").val();
			var new_cont = countof_record -1;
			$("#countof_record").val(new_cont);
			
			var PRDR_ItemID_list = $('#PRDR_ItemID_list').val();
			var ItemID = ','+ItemID;
			var PRDR = PRDR_ItemID_list.replace(ItemID, "");
			$('#PRDR_ItemID_list').val(PRDR);
			
		});
		
		// Save New Transfer
		$('#AddTrans').on('click',function(){ 
			//alert('hello');
			var TrnsFrom = $('#TrnsFrom').val();
			var TrnsTo = $('#TrnsTo').val();
			
			if(TrnsFrom == "" || TrnsFrom == null){
				alert('please select Transfer from..');
				}else{
				if(TrnsTo == "" || TrnsTo == null){
					alert('please select Transfer To..');
					}else{
					var ItemCount = $("#countof_record").val();
					var ItemArray = new Array();
					for (i=1;i<ItemCount;i++) {
						var id= 'ItemID_val'+i;
						var ItemID = document.getElementById(id).value;
						var id2= 'ItemName_val'+i;
						var ItemName = document.getElementById(id2).value;
						var id3= 'Pack_val'+i;
						var Pack = document.getElementById(id3).value;
						var id4= 'Unit_val'+i;
						var Unit = document.getElementById(id4).value;
						var id4= 'qty_val'+i;
						var qty = document.getElementById(id4).value;
						var ii = i - 1;
						ItemArray[ii]=new Array();
						ItemArray[ii][0]=ItemID;
						ItemArray[ii][1]=ItemName;
						ItemArray[ii][2]=Pack;
						ItemArray[ii][3]=Unit;
						ItemArray[ii][4]=qty;
					}
					var ItemSerializedArr = JSON.stringify(ItemArray);
					var Transdate = $("#Transdate").val();
					// console.log(ItemCount);
					if(parseFloat(ItemCount)>1){
						$.ajax({
							url:"<?php echo admin_url(); ?>GodownMaster/SaveTransfer",
							dataType:"JSON",
							method:"POST",
							data:{ItemSerializedArr:ItemSerializedArr,Transdate:Transdate,TrnsFrom:TrnsFrom,TrnsTo:TrnsTo,ItemCount:ItemCount},
							beforeSend: function () {
								$('.searchh3').css('display','block');
								$('.searchh3').css('color','blue');
							},
							complete: function () {
								$('.searchh3').css('display','none');
							},
							success:function(data){
								if(data == false){
									
									}else{
									alert_float('success', 'Record created successfully...');
									$('#TransID').val(data);
									$("#TransIDHidden").val(data);
									
									var TotalRow = $("#countof_record").val();
									//var crRow = parseInt(TotalRow) - 1;
									for (var A = 1; A < TotalRow; A++) {
										var id = 'row'+A;
										document.getElementById(id).remove();
									}
									$('select[name=TrnsFrom]').val('');
									$('.selectpicker').selectpicker('refresh');
									$('select[name=TrnsTo]').val('');
									$('.selectpicker').selectpicker('refresh');
									$("#countof_record").val('1');
									$("#PRDR_ItemID_list").val('');
									var today = new Date();
									var dd = String(today.getDate()).padStart(2, '0');
									var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
									var yyyy = today.getFullYear();
									
									today = dd + '/' + mm + '/' + yyyy;
									$("#Transdate").val(today);
									$('.saveBtn').show();
									$('.saveBtn2').show();
									$('.updateBtn').hide();
									$('.updateBtn2').hide();
									$('.printbtn').hide();
									$('.printBtn').hide();
								}
								
							}
						});
						}else{
						alert('Enter Atleast One Item');
					}
				}    
			}
		});
		
		// Update Transfer
		$('#updateBtn').on('click',function(){ 
			var TrnsFrom = $('#TrnsFrom').val();
			var TrnsTo = $('#TrnsTo').val();
			if(TrnsFrom == "" || TrnsFrom == null){
				alert('please select Transfer from..');
				}else{
				if(TrnsTo == "" || TrnsTo == null){
					alert('please select Transfer To..');
					}else{
					var ItemCount = $("#countof_record").val();
					var ItemArray = new Array();
					for (i=1;i<ItemCount;i++) {
						var id= 'ItemID_val'+i;
						var ItemID = document.getElementById(id).value;
						var id2= 'ItemName_val'+i;
						var ItemName = document.getElementById(id2).value;
						var id3= 'Pack_val'+i;
						var Pack = document.getElementById(id3).value;
						var id4= 'Unit_val'+i;
						var Unit = document.getElementById(id4).value;
						var id4= 'qty_val'+i;
						var qty = document.getElementById(id4).value;
						var ii = i - 1;
						ItemArray[ii]=new Array();
						ItemArray[ii][0]=ItemID;
						ItemArray[ii][1]=ItemName;
						ItemArray[ii][2]=Pack;
						ItemArray[ii][3]=Unit;
						ItemArray[ii][4]=qty;
					}
					var ItemSerializedArr = JSON.stringify(ItemArray);
					var Transdate = $("#Transdate").val();
					var TransID = $("#TransID").val();
					if(parseFloat(ItemCount)>1){
						$.ajax({
							url:"<?php echo admin_url(); ?>GodownMaster/UpdateTransfer",
							dataType:"JSON",
							method:"POST",
							data:{TransID:TransID,ItemSerializedArr:ItemSerializedArr,Transdate:Transdate,TrnsFrom:TrnsFrom,TrnsTo:TrnsTo,ItemCount:ItemCount},
							beforeSend: function () {
								$('.searchh4').css('display','block');
								$('.searchh4').css('color','blue');
							},
							complete: function () {
								$('.searchh4').css('display','none');
							},
							success:function(data){
								if(data == false){
									
									}else{
									alert_float('success', 'Record updated successfully...');
									$('#TransID').val(data);
									$("#TransIDHidden").val(data);
									$('#TrnsFrom').prop('disabled', false);
									var TotalRow = $("#countof_record").val();
									//var crRow = parseInt(TotalRow) - 1;
									for (var A = 1; A < TotalRow; A++) {
										var id = 'row'+A;
										document.getElementById(id).remove();
									}
									$('select[name=TrnsFrom]').val('');
									$('.selectpicker').selectpicker('refresh');
									$('select[name=TrnsTo]').val('');
									$('.selectpicker').selectpicker('refresh');
									$("#countof_record").val('1');
									$("#PRDR_ItemID_list").val('');
									var today = new Date();
									var dd = String(today.getDate()).padStart(2, '0');
									var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
									var yyyy = today.getFullYear();
									
									today = dd + '/' + mm + '/' + yyyy;
									$("#Transdate").val(today);
									$('.saveBtn').show();
									$('.saveBtn2').show();
									$('.updateBtn').hide();
									$('.updateBtn2').hide();
									$('.printbtn').hide();
									$('.printBtn').hide();
								}
								
							}
						});
						}else{
						alert('Enter Atleast One Item');
					}
				}    
			}
		});
		
		// Get Trans Details On blur
		$('#TransID').on('blur',function(){
			var TransID = $(this).val();
			$('#ItemID').focus();
            $.ajax({
				url:"<?php echo admin_url(); ?>GodownMaster/GetTransDetails",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{TransID:TransID,},
				beforeSend: function () {
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');    
				},
				success:function(data){
                    if(empty(data)){
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('.printbtn').hide();
                        $('.DeleteBtn').hide();
                        $('#AccountName').val('');
                        $('#TrnsFrom').prop('disabled', false);
						}else{
                        $('#TransID').val(data.TransID);
                        var TransDate = data.Transdate2.substring(0, 10);
                        var TransDateNew = TransDate.split("-").reverse().join("/");
                        $('#Transdate').val(TransDateNew);
                        $('select[name=TrnsFrom]').val(data.TransFrom);
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=TrnsTo]').val(data.TransTo);
                        $('.selectpicker').selectpicker('refresh');
                        $('#TrnsFrom').prop('disabled', true);
                        let ItemDetails = data.ItemS;
                        var new_rec = '';
                        countof_record = 0;
                        var html = '';
                        for (var index = 0; index < ItemDetails.length; index++) {
                            var CaseQty = parseFloat(ItemDetails[index].BilledQty) / parseFloat(ItemDetails[index].CaseQty);
                            CaseQty = parseFloat(CaseQty).toFixed(2);
                            var Cases = parseFloat(ItemDetails[index].BilledQty);
                            
                            new_rec = new_rec +","+ ItemDetails[index].ItemID;
							
                            //var stockQty = parseFloat($OQty) + parseFloat($PQty) - parseFloat($PRQty) - parseFloat($IQty) + parseFloat($PRDQty) - parseFloat($SQty) + parseFloat($SRTQty) - parseFloat($AQty) + parseFloat($GIQty) - parseFloat($GOQty);
                            
                            // var StockVal =   parseFloat(ItemDetails[index].Stock) / parseFloat(ItemDetails[index].CaseQty);
                            var StockVal =   parseFloat(ItemDetails[index].Stock) + Cases;;
                            
                            countof_record = index + 1;
                            html += "<tr id='row"+countof_record+"'>";
                            html += "<td id='ItemID"+countof_record+"'>"+ItemDetails[index].ItemID+" <input type='hidden' name='ItemID_val"+countof_record+"' id='ItemID_val"+countof_record+"' value='"+ItemDetails[index].ItemID+"'></td>";
                            html += "<td id='ItemName"+countof_record+"'>"+ItemDetails[index].description+" <input type='hidden' name='ItemName_val"+countof_record+"' id='ItemName_val"+countof_record+"' value='"+ItemDetails[index].description+"'></td>";
                            html += "<td id='Pack"+countof_record+"' style='text-align:center'>"+parseFloat(ItemDetails[index].CaseQty)+" <input type='hidden' name='Pack_val"+countof_record+"' id='Pack_val"+countof_record+"' value='"+ItemDetails[index].CaseQty+"'></td>";
                            html += "<td id='Unit"+countof_record+"'>"+ItemDetails[index].unit+" <input type='hidden' name='Unit_val"+countof_record+"' id='Unit_val"+countof_record+"' value='"+ItemDetails[index].unit+"'></td>";
                            html += "<td id='stock"+countof_record+"' style='text-align:center'>"+parseFloat(StockVal).toFixed(2)+" <input type='hidden' name='stock_val"+countof_record+"' id='stock_val"+countof_record+"' value='"+StockVal+"'></td>";
                            html += "<td id='qty"+countof_record+"'><input type='text' style='text-align:center;width: 80px;' name='qty_val"+countof_record+"' id='qty_val"+countof_record+"' value='"+Cases+"' onchange='myFunction("+countof_record+")' onkeyup='recalculateCaseQty("+countof_record+")'></td>";
							
                            html += "<td id='caseqty"+countof_record+"' style='text-align: center;'>"+CaseQty+" <input type='hidden' name='caseqty_val"+countof_record+"' id='caseqty_val"+countof_record+"' value='"+CaseQty+"'></td>";
                            html += '<td><input type="hidden" name="rownum" id="rownum" value="'+countof_record+'"></td>';
                            html += '</tr>';
						}
                        countof_record++;
                        $('#countof_record').val(countof_record);
                        $('#row_count').val(countof_record);
                        $('#PRDR_ItemID_list').val(new_rec);
                        $('#ItemTable tbody').append(html);
                        //$('#print_table tbody').append('');
                        $('#print_table tbody').html(data.Print);
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.DeleteBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                        $('.printbtn').show();
					}
				}
			});
		})
		
		// Get Trans Details On Click
		$('.get_Trans').on('click',function(){
			var TransID = $(this).attr("data-id");
			$('#ItemID').focus();
            $.ajax({
				url:"<?php echo admin_url(); ?>GodownMaster/GetTransDetails",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{TransID:TransID,},
				beforeSend: function () {
                    $('.searchh2').css('display','block');
                    $('.searchh2').css('color','blue');
				},
				complete: function () {
					$('.searchh2').css('display','none');    
				},
				success:function(data){
                    if(empty(data)){
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('.printbtn').hide();
                        $('.DeleteBtn').hide();
                        $('#AccountName').val('');
                        $('#TrnsFrom').prop('disabled', false);
						}else{
                        $('#TransID').val(data.TransID);
                        var TransDate = data.Transdate.substring(0, 10);
                        var TransDateNew = TransDate.split("-").reverse().join("/");
                        $('#Transdate').val(TransDateNew);
                        $('select[name=TrnsFrom]').val(data.TransFrom);
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=TrnsTo]').val(data.TransTo);
                        $('.selectpicker').selectpicker('refresh');
                        $('#TrnsFrom').prop('disabled', true);
                        let ItemDetails = data.ItemS;
                        var new_rec = '';
                        countof_record = 0;
                        var html = '';
                        for (var index = 0; index < ItemDetails.length; index++) {
                            var CaseQty = parseFloat(ItemDetails[index].BilledQty) / parseFloat(ItemDetails[index].CaseQty);
                            CaseQty = parseFloat(CaseQty).toFixed(2);
                            var Cases = parseFloat(ItemDetails[index].BilledQty);
                            
                            new_rec = new_rec +","+ ItemDetails[index].ItemID;
                            // var StockVal =   parseFloat(ItemDetails[index].Stock) / parseFloat(ItemDetails[index].CaseQty);
                            var StockVal =   parseFloat(ItemDetails[index].Stock) + Cases;
                            
                            
                            countof_record = index + 1;
                            html += "<tr id='row"+countof_record+"'>";
                            html += "<td id='ItemID"+countof_record+"'>"+ItemDetails[index].ItemID+" <input type='hidden' name='ItemID_val"+countof_record+"' id='ItemID_val"+countof_record+"' value='"+ItemDetails[index].ItemID+"'></td>";
                            html += "<td id='ItemName"+countof_record+"'>"+ItemDetails[index].description+" <input type='hidden' name='ItemName_val"+countof_record+"' id='ItemName_val"+countof_record+"' value='"+ItemDetails[index].description+"'></td>";
                            html += "<td id='Pack"+countof_record+"' style='text-align:center'>"+parseFloat(ItemDetails[index].CaseQty)+" <input type='hidden' name='Pack_val"+countof_record+"' id='Pack_val"+countof_record+"' value='"+ItemDetails[index].CaseQty+"'></td>";
                            html += "<td id='Unit"+countof_record+"'>"+ItemDetails[index].unit+" <input type='hidden' name='Unit_val"+countof_record+"' id='Unit_val"+countof_record+"' value='"+ItemDetails[index].unit+"'></td>";
                            html += "<td id='stock"+countof_record+"' style='text-align:center'>"+parseFloat(StockVal).toFixed(2)+" <input type='hidden' name='stock_val"+countof_record+"' id='stock_val"+countof_record+"' value='"+StockVal+"'></td>";
                            html += "<td id='qty"+countof_record+"'><input type='text' style='text-align:center;width: 80px;' name='qty_val"+countof_record+"' id='qty_val"+countof_record+"' value='"+Cases+"' onchange='myFunction("+countof_record+")' onkeyup='recalculateCaseQty("+countof_record+")'></td>";
                            html += "<td id='caseqty"+countof_record+"' style='text-align: center;'>"+CaseQty+" <input type='hidden' name='caseqty_val"+countof_record+"' id='caseqty_val"+countof_record+"' value='"+CaseQty+"'></td>";
                            html += '<td><input type="hidden" name="rownum" id="rownum" value="'+countof_record+'"></td>';
                            html += '</tr>';
						}
                        countof_record++;
                        $('#countof_record').val(countof_record);
                        $('#row_count').val(countof_record);
                        $('#PRDR_ItemID_list').val(new_rec);
                        $('#ItemTable tbody').append(html);
                        //$('#print_table tbody').html('');
                        $('#print_table tbody').html(data.Print);
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.DeleteBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                        $('.printbtn').show();
					}
				}
			});
            $('#Trans_List').modal('hide');
		})
		
		// Delete Account
        $('.DeleteBtn').on('click',function(){ 
            var TransID = $('#TransID').val();
            $.ajax({
                url:"<?php echo admin_url(); ?>GodownMaster/DeleteTransfer",
                dataType:"JSON",
                method:"POST",
                data:{TransID:TransID
				},
                beforeSend: function () {
					$('.searchh').css('display','block');
					$('.searchh').css('color','blue');
				},
                complete: function () {
					$('.searchh').css('display','none');
				},
                success:function(data){
					if(data == false){
                        alert_float('warning', 'unable to delete transfer');    
						}else{
						alert_float('success', 'Record Deleted successfully...');
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('.printbtn').hide();
                        $('.DeleteBtn').hide();
                        $('#TransID').val(data);
                        $('#TrnsFrom').prop('disabled', false);
                        var TotalRow = $("#countof_record").val();
						//var crRow = parseInt(TotalRow) - 1;
						for (var A = 1; A < TotalRow; A++) {
							var id = 'row'+A;
							document.getElementById(id).remove();
						}
						$('select[name=TrnsFrom]').val('');
						$('.selectpicker').selectpicker('refresh');
						$('select[name=TrnsTo]').val('');
						$('.selectpicker').selectpicker('refresh');
						$("#countof_record").val('1');
						$("#PRDR_ItemID_list").val('');
						var today = new Date();
						var dd = String(today.getDate()).padStart(2, '0');
						var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
						var yyyy = today.getFullYear();
						
						today = dd + '/' + mm + '/' + yyyy;
						$("#Transdate").val(today);
						
					}
				}
			});
		});
		
		$("#caexcel").click(function(){
			act = '1';
			$.ajax({
				url:"<?php echo admin_url(); ?>GodownMaster/exportGodown",
				method:"POST",
				data:{act:act},
				beforeSend: function () {
					$('#searchh').css('display','block');
				},
				complete: function () {
					$('#searchh').css('display','none');    
				},
				success:function(data){
					response = JSON.parse(data);
					window.location.href = response.site_url+response.filename;
				}
			});
		});
		
		$("#ItemID").dblclick(function(){
			$('#Item_List').modal('show');
			$('#Item_List').on('shown.bs.modal', function () {
				$('#myInput1').val('');
				$('#myInput1').focus();
			})
		});
		
		$("#TransID").dblclick(function(){
			$('#Trans_List').modal('show');
			$('#Trans_List').on('shown.bs.modal', function () {
				$('#myInput2').val('');
				$('#myInput2').focus();
			})
		});
		
		
		$('.updateBtn').hide();
		$('.updateBtn2').hide();
		$('.printbtn').hide();
		$('.DeleteBtn').hide();
		
		// Check Transfer From
		$('#TrnsFrom').on('change',function(){
			var TrnsFrom = $(this).val();
			var TrnsTo = $('#TrnsTo').val();
			if(TrnsFrom == TrnsTo){
				alert('please select transfer From different to transfer To...');
				$('select[name=TrnsFrom]').val('');
				$('.selectpicker').selectpicker('refresh');
				}else{
				var TotalRow = $("#countof_record").val();
				for (var A = 1; A < TotalRow; A++) {
					var id = 'row'+A;
					document.getElementById(id).remove();
				}
				$("#countof_record").val('1');
				$("#PRDR_ItemID_list").val('');
			}
			//alert(TrnsFrom);
			
		});
		
		// Check Transfer From
		$('#TrnsTo').on('change',function(){
			var TrnsTo = $(this).val();
			var TrnsFrom = $('#TrnsFrom').val();
			if(TrnsFrom == TrnsTo){
				alert('please select transfer To different to transfer From...');
				$('select[name=TrnsTo]').val('');
				$('.selectpicker').selectpicker('refresh');
			}
			//alert(TrnsTo);
			
		});
		
		// Focus on AccountID
		$('#TransID').on('focus',function(){
			var ORGTrandID = $('#TransIDHidden').val();
			$('#TransID').val(ORGTrandID);
			var TotalRow = $("#countof_record").val();
			$('#TrnsFrom').prop('disabled', false);
			//var crRow = parseInt(TotalRow) - 1;
			for (var A = 1; A < TotalRow; A++) {
				var id = 'row'+A;
				document.getElementById(id).remove();
			}
			$('select[name=TrnsFrom]').val('');
			$('.selectpicker').selectpicker('refresh');
			$('select[name=TrnsTo]').val('');
			$('.selectpicker').selectpicker('refresh');
			$("#countof_record").val('1');
			$("#PRDR_ItemID_list").val('');
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
			$('.printbtn').hide();
			$('.DeleteBtn').hide();
		});
		
		// Cancel selected data
		$(".cancelBtn").click(function(){
			var ORGTrandID = $('#TransIDHidden').val();
			$('#TransID').val(ORGTrandID);
			var TotalRow = $("#countof_record").val();
			$('#TrnsFrom').prop('disabled', false);
			//var crRow = parseInt(TotalRow) - 1;
			for (var A = 1; A < TotalRow; A++) {
				var id = 'row'+A;
				document.getElementById(id).remove();
			}
			$('select[name=TrnsFrom]').val('');
			$('.selectpicker').selectpicker('refresh');
			$('select[name=TrnsTo]').val('');
			$('.selectpicker').selectpicker('refresh');
			$("#countof_record").val('1');
			$("#PRDR_ItemID_list").val('');
			$('.saveBtn').show();
			$('.saveBtn2').show();
			$('.updateBtn').hide();
			$('.updateBtn2').hide();
			$('.printbtn').hide();
			$('.DeleteBtn').hide();
		});
		
	});
	
</script>
<script>
    function myFunction(value) {
        var thisValue = $("#qty_val"+value).val();
        var stockValue = $("#stock_val"+value).val();
        if(parseFloat(thisValue) > parseFloat(stockValue)){
            alert('please enter less than stock value');
            $("#qty_val"+value).val('0');
			recalculateCaseQty(value);
		}
	}
	
	function recalculateCaseQty(rowId) {
    let qty = parseFloat($("#qty_val" + rowId).val()) || 0;   // Entered qty
    let pack = parseFloat($("#Pack_val" + rowId).val()) || 1; // Case size

    // Recalculate case qty
    let newCaseQty = qty / pack;
    newCaseQty = newCaseQty.toFixed(2);

    // Update td text
    $("#caseqty" + rowId).html(
        newCaseQty + " <input type='hidden' name='caseqty_val" + rowId + "' id='caseqty_val" + rowId + "' value='" + newCaseQty + "'>"
    );
}
</script>

<script>
    function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_Item_List");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			td3 = tr[i].getElementsByTagName("td")[3];
            if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						} else if(td2){
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else if(td3){
							txtValue = td3.textContent || td3.innerText;
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
	}
    
    function myFunction3() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput2");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_Trans_List");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			td3 = tr[i].getElementsByTagName("td")[3];
            if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						} else if(td2){
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else if(td3){
							txtValue = td3.textContent || td3.innerText;
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
	}
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_Trans_List tbody");
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
		var table = $("#table_Item_List tbody");
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
<script>
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
		}
        return true;
	}
</script>

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

<style>
    #table_Item_List td:hover {
	cursor: pointer;
    }
	#AccountID{
    text-transform: uppercase;
	}
	#table_Item_List tr:hover {
    background-color: #ccc;
	}
	#table_Trans_List tr:hover {
    background-color: #ccc;
	}
	#table_Trans_List td:hover {
	cursor: pointer;
    }
	
    .table-Item_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Item_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Item_List tbody th { position: sticky; left: 0; }
    
    .table-Trans_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Trans_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Trans_List tbody th { position: sticky; left: 0; }
    
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>



