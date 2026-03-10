<script>
	
	function removeCommas(str) {
		"use strict";
		return(str.replace(/,/g,''));
	}
	$('.edit-new-order').on('click', function(){
		$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
		$('#transfer-modal').modal('show');
		//init_journal_entry_table();
	});
	
	$( "#vendor" ).change(function() {
		if(this.value != 0){
			var AccountID = $(this).val();
			var old_AccountID = $('#old_vendor_id').val();
			if(empty(old_AccountID)){
				if(empty(AccountID)){
					
					}else{
                    $.post(admin_url + 'purchase/get_vendor_data/'+this.value).done(function(response){
						
                        var last_month = <?php echo date("n", strtotime("previous month")); ?>;
                        response = JSON.parse(response);
                        var ActBal = parseFloat(response.vendor.BAL1) + parseFloat(response.vendor.BAL2)+parseFloat(response.vendor.BAL3)+parseFloat(response.vendor.BAL4)+parseFloat(response.vendor.BAL5)+parseFloat(response.vendor.BAL6)+parseFloat(response.vendor.BAL7)+parseFloat(response.vendor.BAL8)+parseFloat(response.vendor.BAL9)+parseFloat(response.vendor.BAL10)+parseFloat(response.vendor.BAL11)+parseFloat(response.vendor.BAL12)+parseFloat(response.vendor.BAL13);
						
                        $("#c_name").val(response.vendor.company);
						$("#item_associated").val(response.vendor.items);
                        $("#gst_num").val(response.vendor.vat);
                        if(ActBal> 0){
                            var new_ActBal = ActBal.toFixed(2)+'Dr';
							}else{
                            var new_ActBal = ActBal.toFixed(2)+'Cr';
						}
                        $("#c_balance").val(new_ActBal);
                        //   alert(bal_data)
                        $("#address").val(response.vendor.address);
                        $("#station_n").val(response.vendor.StationName);
                        $("#address2").val(response.vendor.Address3);
                        $("#city").val(response.vendor.city_name);
                        $("#state_c").val(response.vendor.state);
                        $("#state_f").val(response.vendor.state_name);
                        $("#new_vendor_code").val(response.vendor.AccountID);
                        $("#purchase_id_check").val('');
                        $("#purchase_id_store").val('');
						var dataObject2 = []; 
						hot.loadData(dataObject2);
						
						var ItemsOptions = response.vendor.Listitems.map(function(Listitems) {
					return {
						id: Listitems.item_code,
						label: Listitems.label,
						// item_code: Listitems.item_code,
					};
				});
				let colIndex = hot.propToCol('item_code'); // Replace 'id' with your dropdown column property
				let rowCount = hot.countRows();
				
				console.log(ItemsOptions);
				for (let row = 0; row < rowCount; row++) {
					hot.setCellMeta(row, colIndex, 'chosenOptions', { data: ItemsOptions });
				}
				
				// Re-render the table after applying changes
				hot.render();
				
						
						
						$('input[name="total_mn"]').val('');
						$('.selectpicker').selectpicker('refresh');
						$('input[name="total_mn"]').val('');
						$('input[name="dc_total"]').val('');
						$('input[name="CGST_amt"]').val('0.00');
							$('input[name="SGST_AMT"]').val('0.00');
							$('input[name="IGST_amt"]').val('0.00');
							$('input[name="Freight_AMT"]').val('0.00');
							$('input[name="Other_amt"]').val('0.00');
						$('input[name="Round_OFF"]').val('0.00');
						$('input[name="Invoice_amt"]').val('0.00');
						
						
						
						
						$('select[name="Freight_2"]').val('');
						$('.selectpicker').selectpicker('refresh');
						$('select[name="Other_ac1"]').val('');
						$('.selectpicker').selectpicker('refresh');
						$('input[name="Freight_1"]').val('');
						$('input[name="Other_ac"]').val('');
					});
				}
				}else{
				if(old_AccountID == AccountID){
					
					}else{
					var Conform = myFunction();
					if(Conform == true){
						$.post(admin_url + 'purchase/get_vendor_data/'+this.value).done(function(response){
							
							var last_month = <?php echo date("n", strtotime("previous month")); ?>;
							
							response = JSON.parse(response);
							var ActBal = parseFloat(response.vendor.BAL1) + parseFloat(response.vendor.BAL2)+parseFloat(response.vendor.BAL3)+parseFloat(response.vendor.BAL4)+parseFloat(response.vendor.BAL5)+parseFloat(response.vendor.BAL6)+parseFloat(response.vendor.BAL7)+parseFloat(response.vendor.BAL8)+parseFloat(response.vendor.BAL9)+parseFloat(response.vendor.BAL10)+parseFloat(response.vendor.BAL11)+parseFloat(response.vendor.BAL12)+parseFloat(response.vendor.BAL13);
							
							$("#c_name").val(response.vendor.company);
							$("#item_associated").val(response.vendor.items);
							$("#gst_num").val(response.vendor.vat);
							if(ActBal> 0){
								var new_ActBal = ActBal.toFixed(2)+'Dr';
								}else{
								var new_ActBal = ActBal.toFixed(2)+'Cr';
							}
							$("#c_balance").val(new_ActBal);
							//   alert(bal_data)
							$("#address").val(response.vendor.address);
							$("#station_n").val(response.vendor.StationName);
							$("#address2").val(response.vendor.Address3);
							$("#city").val(response.vendor.city_name);
							$("#state_c").val(response.vendor.state);
							$("#state_f").val(response.vendor.state_name);
							$("#new_vendor_code").val(response.vendor.AccountID);
							$("#purchase_id_check").val('');
							$("#purchase_id_store").val('');
							
							
						var ItemsOptions = response.vendor.Listitems.map(function(Listitems) {
					return {
						id: Listitems.item_code,
						label: Listitems.label,
						item_code: Listitems.item_code,
					};
				});
				let colIndex = hot.propToCol('item_code'); // Replace 'id' with your dropdown column property
				let rowCount = hot.countRows();
				for (let row = 0; row < rowCount; row++) {
					hot.setCellMeta(row, colIndex, 'chosenOptions', { data: ItemsOptions });
				}
				
				// Re-render the table after applying changes
				hot.render();
							
							<?php if(!empty($pur_order_detail)){ ?>
								var Count = 10;
								<?php }else{ ?>
								var Count = "<?php count($pur_order_detail) ?>";
							<?php }?>
							for(var row_index = 0; row_index <= Count; row_index++) {
								hot.setDataAtCell(row_index,0, '');
								hot.setDataAtCell(row_index,1, '');
								hot.setDataAtCell(row_index,2, '');
								hot.setDataAtCell(row_index,3, '');
								hot.setDataAtCell(row_index,4, '');
								hot.setDataAtCell(row_index,5, '');
								hot.setDataAtCell(row_index,6, '');
								hot.setDataAtCell(row_index,7, '');
								hot.setDataAtCell(row_index,8, '');
								hot.setDataAtCell(row_index,9, '');
								hot.setDataAtCell(row_index,10, '');
								hot.setDataAtCell(row_index,11, '');
								hot.setDataAtCell(row_index,12, '');
								hot.setDataAtCell(row_index,13, '');
								hot.setDataAtCell(row_index,14, '');
							}
							
							$('input[name="CGST_amt"]').val('0.00');
							$('input[name="SGST_AMT"]').val('0.00');
							$('input[name="IGST_amt"]').val('0.00');
							$('input[name="Freight_AMT"]').val('0.00');
							$('input[name="Other_amt"]').val('0.00');
						});
						}else{
						$('#vendor').val(old_AccountID);
					}
				}
			}
			
		}
	});
	
	
	$(function(){
		"use strict";
		validate_purorder_form();
		function validate_purorder_form(selector) {
			
			selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;
			
			appValidateForm($(selector), {
				pur_order_name: 'required',
				old_purRtnDate: 'required',
				purch_rtn_date: {
					remote: {
						url: site_url + "admin/misc/checkpurchRtn_val",
						type: 'post',
						data: {
							purch_rtn_date: function() {
								return $('input[name="purch_rtn_date"]').val();
							},
							old_purRtnDate: function() {
								return $('input[name="old_purRtnDate"]').val();
							},
							PurRtnID: function() {
								return $('input[name="PurRtnID"]').val();
							}
						}
					}
				},
				vendor: 'required',
				invoce_n: 'required',
			});
		}
		
		
	});
	
	function myFunction() {
		let text = "Do you really want to change account?";
		if (confirm(text) == true) {
			/*text = "You pressed OK!";*/
			return true;
			} else {
			//text = "You canceled!";
			return false;
		}
		
	}
	
	<?php if(!isset($pur_order_detail)){
	?>	
	
	// for PURCHRTn Order
	
	function numberWithCommas(x) {
		"use strict";
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	<?php if(!empty($pur_order_detail)){ ?>
		var dataObject = <?php echo html_entity_decode($pur_order_detail); ?>;
		<?php }else{ ?>
		var dataObject = [
		
		]; 
	<?php }?>
	// var dataObject = <?php echo html_entity_decode($pur_order_detail); ?>;
	
    //console.log(dataObject)
    //console.log(<?php echo json_encode($item_code); ?>)
	var hotElement = document.querySelector('#example');
    var hotElementContainer = hotElement.parentNode;
	
	
    var hotSettings = {
		data: dataObject,
		columns: [
        {
			data: 'item_code',
			renderer: customDropdownRenderer,
			editor: "chosen",
			
			width: 50,
			chosenOptions: {
				data: <?php echo json_encode($items); ?>
			}
		},
        { 
			data: 'description',
			type: 'text',
			width: 150,
			readOnly: true
		},
        {
			data: 'case_qty',
			type: 'text',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        {
			data: 'BillID',
			type: 'text',
			width: 70,
			readOnly: true
		},
		
        {
			data: 'SaleRate',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
		{
			data: 'PurchRate',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
			
		},
        {
			data: 'Cases',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			
		},
        {
			data: 'OrderQty',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 80,
			readOnly: true
		},
        {
			data: 'ChallanAmt',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        {
			data: 'DiscPerc',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        {
			data: 'DiscAmt',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
		},
		
		{
			data: 'cgst',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
		},
		{
			data: 'sgst',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
		},
		{
			data: 'igst',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        
        {
			data: 'sub_total',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 70,
			readOnly: true
		}
		
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		//   autoWrapRow: true,
		//   rowHeights: 30,
		columnHeaderHeight: 40,
		minRows: 10,
		maxRows: 40,
		rowHeaders: true,
		colWidths: [200,10,100,50,100,50,100,50,100,100],
		colHeaders: [
        '<?php echo _l('ItemID'); ?>',
        '<?php echo _l('ItemName'); ?>',
        '<?php echo _l('Pack'); ?>',
        '<?php echo _l('PurchId'); ?>',
        '<?php echo _l('PurchQty'); ?>',
        '<?php echo _l('PurchRate'); ?>',
        '<?php echo _l('RtnCases'); ?>',
        '<?php echo _l('InUnits'); ?>',
        '<?php echo _l('Amount'); ?>',
        '<?php echo _l('Disc%'); ?>',
        '<?php echo _l('DiscAmt'); ?>',
        '<?php echo _l('CGST%'); ?>',
        '<?php echo _l('SGST%'); ?>',
        '<?php echo _l('IGST%'); ?>',
        '<?php echo _l('NetAmt'); ?>',
		],
		columnSorting: {
			indicator: true
		},
		autoColumnSize: {
			samplingRatio: 23
		},
		//   dropdownMenu: true,
		mergeCells: true,
		contextMenu: true,
		manualRowMove: true,
		manualColumnMove: true,
		multiColumnSorting: {
			indicator: true
		},
		filters: true,
		manualRowResize: true,
		manualColumnResize: true
	};
    
	var hot = new Handsontable(hotElement, hotSettings);
	<?php if(!empty($pur_order_detail)){ ?>
		var count_data = 2;
		<?php }else{?>
		var count_data = 1; 
	<?php }?>
	hot.addHook('afterChange', function(changes, src) {
		
		if(changes !== null){
			changes.forEach(([row, prop, oldValue, newValue]) => {
				vendor_id = $("#vendor").val();
				
					var VendorItems = $("#item_associated").val()
				if(prop == 'item_code'){
					vendor_id = $("#vendor").val();
					if(vendor_id == ''){
						alert("Please Select vendor");return false;
						}else{
						//alert(newValue);
						if(newValue !== "" && newValue !== null){
							
							<?php if(!empty($pur_order_detail)){ ?>
								var vendor_code = $('#vendor_code').val();
								<?php }else{ ?>
								var vendor_code = $("#vendor").find('option:selected').attr('data-id');
							<?php }?>
							$.ajax({
								url:"<?php echo admin_url(); ?>purchase/items_purchaseid_check/"+newValue+"/"+vendor_code,
								dataType:"JSON",
								method:"POST",
								success: function(response) {
									data =  response
									
									var html = '';
									for(var count = 0; count < data.length; count++)
									{
										
										html += '<tr class="purchase_id" data-href="#" data-id="'+data[count].OrderID+'">';
										html += '<td style="text-align:center;">'+data[count].FY+'</td>';
										html += '<td style="text-align:center;">'+data[count].OrderID+'</td>';
										var date = data[count].TransDate.substring(0, 10)
										var date_new = date.split("-").reverse().join("/");
										
										html += '<td  style="text-align:center;">'+date_new+'</td>';
										html += '<td  style="text-align:left;">'+data[count].Invoiceno +'</td>';
										var date2 = data[count].Invoicedate
										
										html += '<td  style="text-align:center;">'+date2+'</td>';
										html += '<td style="text-align:right;">'+data[count].BilledQty+'</td>';
										html += '<td >'+data[count].CaseQty+'</td>';
										html += '<td >'+data[count].Cases+'</td>';
										html += '<td >'+data[count].PurchRate+'</td>';
										html += '<td >'+data[count].gst+'</td>';
										html += '<td >'+data[count].cgst+'</td>';
										html += '<td >'+data[count].sgst+'</td>';
										html += '<td >'+data[count].igst+'</td>';
										html += '<td >'+data[count].NetOrderAmt+'</td>';
										html += '<td ></td>';
										html += '</tr>';
									}
									
									$('.table_purchase_report tbody').html(html);
									$('#retuen_pur_id_modal').modal('show');
									$('.purchase_id').on('click', function(){
										var pur_id = $(this).attr("data-id");
										if(count_data == 1){
											$('#purchase_id_store').val(pur_id);
											$('#purchase_id_check').val(pur_id);
											}else{
											$('#purchase_id_store').val(pur_id);
										}
										$('#retuen_pur_id_modal').modal('hide');
										
										
										p_id_check = $('#purchase_id_check').val(); 
										p_id_store = $('#purchase_id_store').val(); 
										
										if(pur_id == p_id_check){
											$.post(admin_url + 'purchase/items_change_purchaseId/'+newValue+'/'+p_id_store).done(function(response){
												
												response = JSON.parse(response);
												hot.setDataAtCell(row,1, response.value.description);
												hot.setDataAtCell(row,2, response.value.case_qty);
												hot.setDataAtCell(row,3, response.value.TransID);
												
												hot.setDataAtCell(row,4, response.value.BilledQty);
												hot.setDataAtCell(row,5, response.value.BasicRate);
												
												hot.setDataAtCell(row,6, '0.00');
												hot.setDataAtCell(row,7, '0.00');
												hot.setDataAtCell(row,8, '0.00');
												
												hot.setDataAtCell(row,9, '0.00');
												hot.setDataAtCell(row,10, '0.00');
												state_id = $('#state_c').val();
												if(state_id == 'UP'){
													cgst =  response.value.taxrate/2;
													sgst =  response.value.taxrate/2;
													igst = 0.00;
													}else{
													cgst =  0.00;
													sgst =  0.00;
													igst = response.value.taxrate; 
												}
												hot.setDataAtCell(row,11, cgst);
												hot.setDataAtCell(row,12, sgst);
												hot.setDataAtCell(row,13, igst);
												hot.setDataAtCell(row,14, '0.00');
												
											});
											
											count_data++;   
											$(hot.setDataAtCell(row,6)).focus();
											}else{
											alert("Multiple GST and single PurchID only accepted..");
											hot.setDataAtCell(row,0, '');
											hot.setDataAtCell(row,1, '');
											hot.setDataAtCell(row,2, '');
											hot.setDataAtCell(row,3, '');
											hot.setDataAtCell(row,4, '');
											hot.setDataAtCell(row,5, '');
											hot.setDataAtCell(row,6, '');
											hot.setDataAtCell(row,7, '');
											hot.setDataAtCell(row,8, '');
											hot.setDataAtCell(row,9, '');
											hot.setDataAtCell(row,10, '');
											hot.setDataAtCell(row,11, '');
											hot.setDataAtCell(row,12, '');
											hot.setDataAtCell(row,13, '');
											hot.setDataAtCell(row,14, '');
										}
										
									});
								}
							});
							}else{
							
							hot.setDataAtCell(row,1, '');
							hot.setDataAtCell(row,2, '');
							hot.setDataAtCell(row,3, '');
							hot.setDataAtCell(row,4, '');
							hot.setDataAtCell(row,5, '');
							hot.setDataAtCell(row,6, '');
							hot.setDataAtCell(row,7, '');
							hot.setDataAtCell(row,8, '');
							hot.setDataAtCell(row,9, '');
							hot.setDataAtCell(row,10, '');
							hot.setDataAtCell(row,11, '');
							hot.setDataAtCell(row,12, '');
							hot.setDataAtCell(row,13, '');
							hot.setDataAtCell(row,14, '');
						}
						
					}
					}else if(prop == 'Cases'){
					
					//hot.setDataAtCell(row,6, newValue);
					var pack_val = hot.getDataAtCell(row,2);
					var max_val = hot.getDataAtCell(row,4);
					if(max_val < newValue){
						alert('please enter less than or equal to purchase quentity..');
						}else{
						
						//hot.setDataAtCell(row,7, newValue*pack_val);
						hot.setDataAtCell(row,7, newValue);
						
						var state = $("#state_c").val();
						if(state == 'UP'){
							var cgst = hot.getDataAtCell(row,11)
							var sgst = hot.getDataAtCell(row,12)
							gst = parseFloat(cgst)+parseFloat(sgst);
							//console.log(gst+"gst")
							amount =  newValue*hot.getDataAtCell(row,5);
							hot.setDataAtCell(row,8, amount);
							// console.log(amount+"amount")
							
							var prec = (gst*amount)/100;
							//  console.log(prec+"prec")
							//  console.log(parseFloat(prec)+amount)
							hot.setDataAtCell(row,14, (parseFloat(prec)+amount));
							}else{
							var gst = hot.getDataAtCell(row,13)
							amount =  newValue*hot.getDataAtCell(row,5);
							hot.setDataAtCell(row,8, amount);
							var prec = (gst*amount)/100;
							hot.setDataAtCell(row,14, (parseFloat(prec)+amount));
							
						}
					}
					}else if(prop == 'sub_total'){ 
					
					var grand_total = 0;
					var total_cgst = 0;
					var total_sgst = 0;
					var total_igst = 0;
					var total_amunt = 0;
					var total_money = 0;
					var full_total_d = 0;
					var total_d = 0;
					var gstAMTSUM = 0;
					
					round_off_v =0;
					
					round_off_v =0;
					for (var row_index = 0; row_index <= 40; row_index++) {
						var gstamtfrRow = 0;
						var subtotal = 0;
						var grandtotal = 0;
						
						if(parseFloat(hot.getDataAtCell(row_index, 8)) > 0){
							total_amunt += (parseFloat(hot.getDataAtCell(row_index, 8)));
							subtotal = (parseFloat(hot.getDataAtCell(row_index, 8)));
						}
						if(parseFloat(hot.getDataAtCell(row_index, 14)) > 0){
							grand_total += (parseFloat(hot.getDataAtCell(row_index, 14)));
							grandtotal = (parseFloat(hot.getDataAtCell(row_index, 14)));
						}
						
						gstamtfrRow = parseFloat(grandtotal) - parseFloat(subtotal);
						gstAMTSUM += gstamtfrRow;
						if(parseFloat(hot.getDataAtCell(row_index, 11)) > 0){
							total_cgst += (parseFloat(hot.getDataAtCell(row_index, 11)));
						}
						if(parseFloat(hot.getDataAtCell(row_index, 12)) > 0){
							total_sgst += (parseFloat(hot.getDataAtCell(row_index, 12)));
						}
						if(parseFloat(hot.getDataAtCell(row_index, 13)) > 0){
							total_igst += (parseFloat(hot.getDataAtCell(row_index, 13)));
						}
					}
					var FinalAmt = grand_total;
					
					var grand_total_with = FinalAmt;
					grand_total_roundAmt = Math.round(FinalAmt);
					round_offAmt =  grand_total_roundAmt - grand_total_with;
					
					var state = $("#state_c").val();
					if(state == 'UP'){
						total_cgst_amt  = parseFloat(gstAMTSUM / 2);
						total_sgst_amt  = parseFloat(gstAMTSUM / 2);
						total_igst_amt = 0;
						}else{
						total_igst_amt  = parseFloat(gstAMTSUM);
						total_sgst_amt =0;
						total_cgst_amt =0;
					}
					
					$('input[name="total_mn"]').val(total_amunt.toFixed(2));
					$('input[name="Round_OFF"]').val(round_offAmt.toFixed(2));
					$('input[name="CGST_amt"]').val(total_cgst_amt.toFixed(2));
					$('input[name="SGST_AMT"]').val(total_sgst_amt.toFixed(2));
					$('input[name="IGST_amt"]').val(total_igst_amt.toFixed(2));
					
					var Freight_AMT = $('#Freight_AMT').val();
					var Other_amt = $('#Other_amt').val();
					var all_total = parseFloat(grand_total_roundAmt)+parseFloat(Freight_AMT)+parseFloat(Other_amt);
					$('input[name="Invoice_amt"]').val(all_total);
					
					
				}
			});
		}
	});
	
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
		}
        return true;
	}
	
    $('#Other_amt').click(function(e) {
        var Other_amt = $(this).val();
        $('#Other_amt_hidden').val(Other_amt)
	});
    $('#Other_amt').on('blur', function() {
        var InvAmt = $('input[name="Invoice_amt"]').val();
        var Freight_AMT = $('#Freight_AMT').val();
        var Other_amt = $('#Other_amt_hidden').val();
        c_value1 = $(this).val();
        if(c_value1 == ''){
			c_value1 = 0;
		}
        if(Other_amt == ''){
			Other_amt = 0;
		}
        var newAmt =parseFloat(InvAmt)+parseFloat(c_value1)-parseFloat(Other_amt);
        $('input[name="Invoice_amt"]').val(newAmt);
	});
	
	
    $('#Freight_2').on('change', function() {
        $.post(admin_url + 'purchase/get_accounts_freightid/'+$(this).val()).done(function(result){
  	        result_data = JSON.parse(result);
  	        $('input[name="Freight_1"]').val(result_data.items.AccountID);
		})
	});
    
    $('#Other_ac1').on('change', function() {
        $.post(admin_url + 'purchase/get_accounts_othertid/'+$(this).val()).done(function(result){
  	        result_data = JSON.parse(result);
  	        $('input[name="Other_ac"]').val(result_data.items.AccountID);
		})
	});
	
	
    $('#Freight_AMT').click(function(e) {
        var Freight_AMT = $(this).val();
        $('#Freight_AMT_hidden').val(Freight_AMT)
	});
	
    $('#Freight_AMT').on('blur', function() {
        var Freight_AMT = $('#Freight_AMT_hidden').val();
        var InvAmt = $('input[name="Invoice_amt"]').val();
        var Other_amt = $('#Other_amt').val();
        c_value2 = $(this).val();
        if(c_value2 == ''){
			c_value2 = 0;
		}
        if(Freight_AMT == ''){
			Freight_AMT = 0;
		}
		var newAmt =parseFloat(InvAmt)+parseFloat(c_value2)-parseFloat(Freight_AMT);
		$('input[name="Invoice_amt"]').val(newAmt);
	});
	
    $('.save_detail').on('click', function() {
		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
	});
	
	<?php }else{ ?>
	
	// For Edit PURCHRTN 
	
	function numberWithCommas(x) {
		"use strict";
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	// var dataobjj = 'test'; 
	var dataObject1 = <?php echo html_entity_decode($pur_order_detail); ?>;
	//console.log(dataObject);
	var hotElement1 = document.querySelector('#example');
    var hotElementContainer1 = hotElement1.parentNode;
    var hotSettings1 = {
		data: dataObject1,
		columns: [
      	
        {
			data: 'item_code',
			renderer: customDropdownRenderer,
			editor: "chosen",
			
			width: 50,
			chosenOptions: {
				data: <?php echo json_encode($items); ?>
			}
		},
        {
			data: 'description',
			type: 'text',
			width: 150,
			readOnly: true
		},
        {
			data: 'case_qty',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 40,
			readOnly: true
		},
        {
			data: 'BillID',
			width: 60,
			readOnly: true
		},
        {
			data: 'PurchQty',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        {
			data: 'BasicRate',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        
        {
			data: 'Cases',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			
		},
        {
			data: 'BilledQty',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
		},
        {
			data: 'ChallanAmt',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        {
			data: 'DiscPerc',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        {
			data: 'DiscAmt',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
		},
		
		{
			data: 'cgst',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
		},
        {
			data: 'sgst',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 60,
			readOnly: true
		},
        {
			data: 'igst',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 50,
			readOnly: true
		},
        
        {
			data: 'Net_total',
			type: 'numeric',
			numericFormat: {
				pattern: '0.00'
			},
			width: 70,
			readOnly: true
		}
        
		
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		//   autoWrapRow: true,
		//   rowHeights: 30,
		columnHeaderHeight: 40,
		minRows: 10,
		maxRows: 40,
		rowHeaders: true,
		colWidths: [200,10,100,50,100,50,100,50,100,100],
		colHeaders: [
        '<?php echo _l('ItemID'); ?>',
        '<?php echo _l('ItemName'); ?>',
        '<?php echo _l('Pack'); ?>',
        '<?php echo _l('PurchId'); ?>',
        '<?php echo _l('PurchQty'); ?>',
        '<?php echo _l('PurchRate'); ?>',
        '<?php echo _l('RtnCases'); ?>',
        '<?php echo _l('InUnits'); ?>',
        '<?php echo _l('Amount'); ?>',
        '<?php echo _l('Disc%'); ?>',
        '<?php echo _l('DiscAmt'); ?>',
        '<?php echo _l('CGST%'); ?>',
        '<?php echo _l('SGST%'); ?>',
        '<?php echo _l('IGST%'); ?>',
        '<?php echo _l('NetAmt'); ?>',
		],
		columnSorting: {
			indicator: true
		},
		autoColumnSize: {
			samplingRatio: 23
		},
		//   dropdownMenu: true,
		mergeCells: true,
		contextMenu: true,
		manualRowMove: true,
		manualColumnMove: true,
		multiColumnSorting: {
			indicator: true
		},
		filters: true,
		manualRowResize: true,
		manualColumnResize: true
	};
    
	
	
	var hot = new Handsontable(hotElement1, hotSettings1);
	<?php if(!empty($pur_order_detail)){ ?>
		var count_data = 1;
		<?php }else{?>
		var count_data = 2; 
	<?php }?>
	hot.addHook('afterChange', function(changes, src) {
		
		if(changes !== null){
			changes.forEach(([row, prop, oldValue, newValue]) => {
				
				//if(newValue != ''){
				if(prop == 'item_code'){
					if(newValue != ''){
						vendor_id = $("#vendor").val();
						if(vendor_id == ''){
							alert("Please Select vendor");return false;
							}else{
							//alert(newValue);
							if(newValue !== "" && newValue !== null){
								
								<?php if(!empty($pur_order_detail)){ ?>
									var vendor_code = $('#new_vendor_code').val();
									<?php }else{ ?>
									var vendor_code = $("#vendor").find('option:selected').attr('data-id');
								<?php }?>
								$.ajax({
									url:"<?php echo admin_url(); ?>purchase/items_purchaseid_check/"+newValue+"/"+vendor_code,
									dataType:"JSON",
									method:"POST",
									success: function(response) {
										data =  response
										
										var html = '';
										for(var count = 0; count < data.length; count++)
										{
											
											html += '<tr class="purchase_id" data-href="#" data-id="'+data[count].OrderID+'">';
											html += '<td style="text-align:center;">'+data[count].FY+'</td>';
											html += '<td style="text-align:center;">'+data[count].OrderID+'</td>';
											var date = data[count].TransDate.substring(0, 10)
											var date_new = date.split("-").reverse().join("/");
											
											html += '<td  style="text-align:center;">'+date_new+'</td>';
											html += '<td  style="text-align:left;">'+data[count].Invoiceno +'</td>';
											var date2 = data[count].Invoicedate
											
											html += '<td  style="text-align:center;">'+date2+'</td>';
											html += '<td style="text-align:right;">'+data[count].BilledQty+'</td>';
											html += '<td >'+data[count].CaseQty+'</td>';
											html += '<td >'+data[count].Cases+'</td>';
											html += '<td >'+data[count].PurchRate+'</td>';
											html += '<td >'+data[count].gst+'</td>';
											html += '<td >'+data[count].cgst+'</td>';
											html += '<td >'+data[count].sgst+'</td>';
											html += '<td >'+data[count].igst+'</td>';
											html += '<td >'+data[count].NetOrderAmt+'</td>';
											html += '<td ></td>';
											html += '</tr>';
										}
										
										$('.table_purchase_report tbody').html(html);
										$('#retuen_pur_id_modal').modal('show');
										$('.purchase_id').on('click', function(){
											var pur_id = $(this).attr("data-id");
											if(count_data == 1){
												$('#purchase_id_store').val(pur_id);
												$('#purchase_id_check').val(pur_id);
												}else{
												$('#purchase_id_store').val(pur_id);
											}
											$('#retuen_pur_id_modal').modal('hide');
											
											
											p_id_check = $('#purchase_id_check').val(); 
											p_id_store = $('#purchase_id_store').val(); 
											if(p_id_check == p_id_store){
												$.post(admin_url + 'purchase/items_change_purchaseId/'+newValue+'/'+p_id_store).done(function(response){
													
													response = JSON.parse(response);
													hot.setDataAtCell(row,1, response.value.description);
													hot.setDataAtCell(row,2, response.value.case_qty);
													hot.setDataAtCell(row,3, response.value.TransID);
													
													hot.setDataAtCell(row,4, response.value.BilledQty);
													hot.setDataAtCell(row,5, response.value.BasicRate);
													
													hot.setDataAtCell(row,6, '0.00');
													hot.setDataAtCell(row,7, '0.00');
													hot.setDataAtCell(row,8, '0.00');
													
													hot.setDataAtCell(row,9, '0.00');
													hot.setDataAtCell(row,10, '0.00');
													state_id = $('#state_c').val();
													if(state_id == 'UP'){
														cgst =  response.value.taxrate/2;
														sgst =  response.value.taxrate/2;
														igst = 0.00;
														}else{
														cgst =  0.00;
														sgst =  0.00;
														igst = response.value.taxrate; 
													}
													hot.setDataAtCell(row,11, cgst);
													hot.setDataAtCell(row,12, sgst);
													hot.setDataAtCell(row,13, igst);
													hot.setDataAtCell(row,14, '0.00');
													
												});
												
												count_data++;   
												$(hot.setDataAtCell(row,6)).focus();
												}else{
												alert("Multiple GST and single PurchID only accepted..");
												hot.setDataAtCell(row,0, '');
												hot.setDataAtCell(row,1, '');
												hot.setDataAtCell(row,2, '');
												hot.setDataAtCell(row,3, '');
												hot.setDataAtCell(row,4, '');
												hot.setDataAtCell(row,5, '');
												hot.setDataAtCell(row,6, '');
												hot.setDataAtCell(row,7, '');
												hot.setDataAtCell(row,8, '');
												hot.setDataAtCell(row,9, '');
												hot.setDataAtCell(row,10, '');
												hot.setDataAtCell(row,11, '');
												hot.setDataAtCell(row,12, '');
												hot.setDataAtCell(row,13, '');
												hot.setDataAtCell(row,14, '');
											}
											
										});
									}
								});
								}else{
								
								hot.setDataAtCell(row,1, '');
								hot.setDataAtCell(row,2, '');
								hot.setDataAtCell(row,3, '');
								hot.setDataAtCell(row,4, '');
								hot.setDataAtCell(row,5, '');
								hot.setDataAtCell(row,6, '');
								hot.setDataAtCell(row,7, '');
								hot.setDataAtCell(row,8, '');
								hot.setDataAtCell(row,9, '');
								hot.setDataAtCell(row,10, '');
								hot.setDataAtCell(row,11, '');
								hot.setDataAtCell(row,12, '');
								hot.setDataAtCell(row,13, '');
								hot.setDataAtCell(row,14, '');
							}
						}
					}
					}else if(prop == 'Cases'){
					
					var ItemID = hot.getDataAtCell(row,0);
					if(ItemID !== "" && ItemID !== null){
						//hot.setDataAtCell(row,6, newValue);
						var pack_val = hot.getDataAtCell(row,2);
						var max_val = hot.getDataAtCell(row,4);
						if(max_val < newValue){
							alert('please enter less than or equal to purchase quentity..');
							}else{
							
							//hot.setDataAtCell(row,7, newValue*pack_val);
							hot.setDataAtCell(row,7, newValue);
							
							var state = $("#state_c").val();
							if(state == 'UP'){
								var cgst = hot.getDataAtCell(row,11)
								var sgst = hot.getDataAtCell(row,12)
								gst = parseFloat(cgst)+parseFloat(sgst);
								//console.log(gst+"gst")
								amount =  newValue*hot.getDataAtCell(row,5);
								hot.setDataAtCell(row,8, amount);
								// console.log(amount+"amount")
								
								var prec = (gst*amount)/100;
								//  console.log(prec+"prec")
								//  console.log(parseFloat(prec)+amount)
								hot.setDataAtCell(row,14, (parseFloat(prec)+amount));
								}else{
								var gst = hot.getDataAtCell(row,13)
								amount =  newValue*hot.getDataAtCell(row,5);
								hot.setDataAtCell(row,8, amount);
								var prec = (gst*amount)/100;
								hot.setDataAtCell(row,14, (parseFloat(prec)+amount));
								
							}
						}
					}
					
					}else if(prop == 'Net_total'){
					
					var grand_total = 0;
					var total_cgst = 0;
					var total_sgst = 0;
					var total_igst = 0;
					var total_amunt = 0;
					var total_money = 0;
					var full_total_d = 0;
					var total_d = 0;
					var gstAMTSUM = 0;
					
					round_off_v =0;
					
					round_off_v =0;
					for (var row_index = 0; row_index <= 40; row_index++) {
						var gstamtfrRow = 0;
						var subtotal = 0;
						var grandtotal = 0;
						if(parseFloat(hot.getDataAtCell(row_index, 8)) > 0){
							total_amunt += (parseFloat(hot.getDataAtCell(row_index, 8)));
							subtotal = (parseFloat(hot.getDataAtCell(row_index, 8)));
						}
						if(parseFloat(hot.getDataAtCell(row_index, 14)) > 0){
							grand_total += (parseFloat(hot.getDataAtCell(row_index, 14)));
							grandtotal = (parseFloat(hot.getDataAtCell(row_index, 14)));
						}
						gstamtfrRow = parseFloat(grandtotal) - parseFloat(subtotal);
						gstAMTSUM += gstamtfrRow;
						if(parseFloat(hot.getDataAtCell(row_index, 11)) > 0){
							total_cgst = (parseFloat(hot.getDataAtCell(row_index, 11)));
						}
						if(parseFloat(hot.getDataAtCell(row_index, 12)) > 0){
							total_sgst = (parseFloat(hot.getDataAtCell(row_index, 12)));
						}
						if(parseFloat(hot.getDataAtCell(row_index, 13)) > 0){
							total_igst = (parseFloat(hot.getDataAtCell(row_index, 13)));
						}
					}
					var FinalAmt = grand_total;
					
					var grand_total_with = FinalAmt;
					grand_total_roundAmt = Math.round(FinalAmt);
					round_offAmt = grand_total_roundAmt - grand_total_with;
					var state = $("#state_c").val();
					if(state == 'UP'){
						total_cgst_amt  = parseFloat(gstAMTSUM / 2);
						total_sgst_amt  = parseFloat(gstAMTSUM / 2);
						total_igst_amt = 0;
						}else{
						total_igst_amt  = parseFloat(gstAMTSUM);
						total_sgst_amt =0;
						total_cgst_amt =0;
					}
					
					$('input[name="total_mn"]').val(total_amunt.toFixed(2));
					$('input[name="Round_OFF"]').val(round_offAmt.toFixed(2));
					$('input[name="CGST_amt"]').val(total_cgst_amt.toFixed(2));
					$('input[name="SGST_AMT"]').val(total_sgst_amt.toFixed(2));
					$('input[name="IGST_amt"]').val(total_igst_amt.toFixed(2));
					var Freight_AMT = $('#Freight_AMT').val();
					var Other_amt = $('#Other_amt').val();
					var all_total = parseFloat(grand_total_roundAmt)+parseFloat(Freight_AMT)+parseFloat(Other_amt);
					$('input[name="Invoice_amt"]').val(all_total);
				}
				
				
				//}
			});
		}
	});
	
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
		}
        return true;
	}
    $('#Other_amt').click(function(e) {
        var Other_amt = $(this).val();
        $('#Other_amt_hidden').val(Other_amt)
	});
    
    $('#Other_amt').on('blur', function() {
        var InvAmt = $('input[name="Invoice_amt"]').val();
        var Freight_AMT = $('#Freight_AMT').val();
        var Other_amt = $('#Other_amt_hidden').val();
        c_value1 = $(this).val();
        if(c_value1 == ''){
			c_value1 = 0;
		}
        if(Other_amt == ''){
			Other_amt = 0;
		}
		var newAmt =parseFloat(InvAmt)+parseFloat(c_value1)-parseFloat(Other_amt);
		$('input[name="Invoice_amt"]').val(newAmt);
	});
	
	
    $('#Freight_2').on('change', function() {
        $.post(admin_url + 'purchase/get_accounts_freightid/'+$(this).val()).done(function(result){
			result_data = JSON.parse(result);
			$('input[name="Freight_1"]').val(result_data.items.AccountID);
		})
	});
    
    $('#Other_ac1').on('change', function() {
        $.post(admin_url + 'purchase/get_accounts_othertid/'+$(this).val()).done(function(result){
			result_data = JSON.parse(result);
			$('input[name="Other_ac"]').val(result_data.items.AccountID);
		})
	});
	
	
    $('#Freight_AMT').click(function(e) {
        var Freight_AMT = $(this).val();
        $('#Freight_AMT_hidden').val(Freight_AMT)
	});
	
    $('#Freight_AMT').on('blur', function() {
        var Freight_AMT = $('#Freight_AMT_hidden').val();
        var InvAmt = $('input[name="Invoice_amt"]').val();
        var Other_amt = $('#Other_amt').val();
        c_value2 = $(this).val();
        if(c_value2 == ''){
			c_value2 = 0;
		}
        if(Freight_AMT == ''){
			Freight_AMT = 0;
		}
		var newAmt =parseFloat(InvAmt)+parseFloat(c_value2)-parseFloat(Freight_AMT);
		$('input[name="Invoice_amt"]').val(newAmt);
	});
    
    $('.save_detail').on('click', function() {
        $('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
	});
	
	<?php } ?>
	function customRenderer(instance, td, row, col, prop, value, cellProperties) {
		"use strict";
		Handsontable.renderers.TextRenderer.apply(this, arguments);
		if(td.innerHTML != ''){
			td.innerHTML = td.innerHTML + '%'
			td.className = 'htRight';
		}
	}
	
	function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
		"use strict";
		var selectedId;
		var optionsList = cellProperties.chosenOptions.data;
		
		if(typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {
			Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
			return td;
		}
		
		var values = (value + "").split("|");
		value = [];
		for (var index = 0; index < optionsList.length; index++) {
			
			if (values.indexOf(optionsList[index].id + "") > -1) {
				selectedId = optionsList[index].item_code;
				value.push(optionsList[index].id);
			}
		}
		value = value.join(", ");
		
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}
	
</script>

<script type="text/javascript">
    $('#Freight_AMT').on('keypress',function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
		}
        var input = $(this).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
            event.preventDefault();
		}
	});
    
    $('#Other_amt').on('keypress',function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
            event.preventDefault();
		}
        var input = $(this).val();
        if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
            event.preventDefault();
		}
	});
</script>