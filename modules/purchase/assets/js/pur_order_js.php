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
			$.post(admin_url + 'purchase/get_vendor_data/'+this.value).done(function(response){
				var last_month = <?php echo date("n", strtotime("previous month")); ?>;
				response = JSON.parse(response);
				if(response.vendor.IsTDS == 1){
					TDS = 'Y';
				}else{
					TDS = 'N';
				}
				$("#IsTDS").val(TDS);
				$("#TDSPer").val(response.vendor.TDSPer);
				$("#TDSSec").val(response.vendor.TDSName);
				$("#TDSCode").val(response.vendor.TDSCode);
				
				
				$("#po_number").find('option').remove();
				$("#po_number").selectpicker("refresh");
				$("#po_number").append(new Option('None selected', ''));
				for (var i = 0; i < response.PendingOrder.length; i++) {
                    $("#po_number").append(new Option(response.PendingOrder[i].PurchID, response.PendingOrder[i].PurchID));
				}
				$('.selectpicker').selectpicker('refresh');
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
				$("#address").val(response.vendor.address);
				$("#station_n").val(response.vendor.StationName);
				$("#address2").val(response.vendor.Address3);
				$("#city").val(response.vendor.city_name);
				$("#state_c").val(response.vendor.state);
				$("#state_f").val(response.vendor.state_name);
				
				var dataObject2 = []; 
				hot.loadData(dataObject2);
				
				$('input[name="total_mn"]').val('');
				$('input[name="dc_total"]').val('');
				$('input[name="CGST_amt"]').val('');
				$('input[name="SGST_AMT"]').val('');
				$('input[name="IGST_amt"]').val('');
				$('input[name="Round_OFF"]').val('');
				$('input[name="Invoice_amt"]').val('');
				
			});
			}else{
			var dataObject2 = []; 
			hot.loadData(dataObject2);
			
			$("#IsTDS").val('');
			$("#TDSPer").val('');
			$("#TDSSec").val('');
			$("#TDSCode").val('');
			$("#po_number").find('option').remove();
			$("#po_number").selectpicker("refresh");
			
			$("#c_name").val('');
			$("#item_associated").val('');
			$("#gst_num").val('');
			$("#c_balance").val('');
			$("#address").val('');
			$("#station_n").val('');
			$("#address2").val('');
			$("#city").val('');
			$("#state_c").val('');
			$("#state_f").val('');
			
			$('input[name="total_mn"]').val('');
			$('input[name="dc_total"]').val('');
			$('input[name="CGST_amt"]').val('');
			$('input[name="SGST_AMT"]').val('');
			$('input[name="IGST_amt"]').val('');
			$('input[name="Round_OFF"]').val('');
			$('input[name="Invoice_amt"]').val('');
		}
	});
	
	function dc_percent_change(invoker){
		"use strict";
		var total_mn = $('input[name="total_mn"]').val();
		var t_mn = parseFloat(removeCommas(total_mn));
		var rs = (t_mn*invoker.value)/100;
		var tax_order_amount = $('input[name="tax_order_amount"]').val();
		
		if(tax_order_amount == ''){
			tax_order_amount = '0';
		}
		
		var grand_total = t_mn - rs + parseFloat(removeCommas(tax_order_amount));
		
		$('input[name="grand_total"]').val(numberWithCommas(grand_total));
		
		$('input[name="dc_total"]').val(numberWithCommas(rs));
		$('input[name="after_discount"]').val(numberWithCommas(t_mn - rs));
		
	}
	
	function tax_percent_change(invoker){
		"use strict";
		var total_mn = $('input[name="total_mn"]').val();
		var t_mn = parseFloat(removeCommas(total_mn));
		var rs = (t_mn*invoker.value)/100;
		var dc_total = $('input[name="dc_total"]').val();
		if(dc_total == ''){
			dc_total = '0';
		}
		
		var grand_total = t_mn + rs - parseFloat(removeCommas(dc_total));
		
		$('input[name="tax_order_amount"]').val(numberWithCommas(rs));
		$('input[name="grand_total"]').val(numberWithCommas(grand_total));
	}
	
	function dc_total_change(invoker){
		"use strict";
		var total_mn = $('input[name="total_mn"]').val();
		var t_mn = parseFloat(removeCommas(total_mn));
		var rs = t_mn - parseFloat(removeCommas(invoker.value));
		
		var tax_order_amount = $('input[name="tax_order_amount"]').val();
		
		if(tax_order_amount == ''){
			tax_order_amount = '0';
		}
		
		var grand_total = rs + parseFloat(removeCommas(tax_order_amount));
		
		$('input[name="grand_total"]').val(numberWithCommas(grand_total));
		
		$('input[name="after_discount"]').val(numberWithCommas(rs));
	}
	
	$(function(){
		"use strict";
		validate_purorder_form();
		function validate_purorder_form(selector) {
			
			selector = typeof(selector) == 'undefined' ? '#pur_order-form' : selector;
			
			appValidateForm($(selector), {
				pro_orderid: 'required',
				pur_order_number: 'required',
				prd_date: {
					remote: {
						url: site_url + "admin/misc/checkpurch_val",
						type: 'post',
						data: {
							order_date: function() {
								return $('input[name="prd_date"]').val();
							},
							PurchID: function() {
								return $('input[name="pur_order_number"]').val();
							}
						}
					}
				},
				vendor: 'required',
				invoce_n: 'required',
				GodownID: 'required',
			});
		}
		
		
	});
	
	<?php if(!isset($pur_order)){
	?>	
	
	
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
	
    
	var hotElement = document.querySelector('#example');
    var hotElementContainer = hotElement.parentNode;
	
	
    var hotSettings = {
		data: dataObject,
		columns: [
        {
			data: 'id',
			renderer: customDropdownRenderer,
			editor: "chosen",
			readOnly: true,
			width: 80,
			chosenOptions: {
				data: <?php echo json_encode($item_code); ?>
			},
		},
        { 
			data: 'description',
			type: 'text',
			width: 150,
			readOnly: true
		},
        {
			data: 'name',
			type: 'text',
			
			width: 150,
			
			readOnly: true
			
		},
        {
			data: 'CaseQty',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 50,
			readOnly: true
		},
        {
			data: 'OrderQty',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 60,
			validator: requiredValidator,
		},
        {
			data: 'Cases',
			type: 'numeric',
			width: 50,
			readOnly: true
			
		},
		{
			data: 'PurchRate',
			type: 'numeric',
			width: 60,
			validator: requiredValidator,
			
		},
        {
			data: 'Disc',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 50,
		},
        {
			data: 'DiscAmt',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 60,
		},
		{
			data: 'batch_no',
			type: 'text',
			width: 90,
		},
        {
			data: 'mfg_date',
			type: 'date',
			width: 70,
			
		},
        {
			data: 'expiry_date',
			type: 'date',
			width: 70,
			
		},
        {
			data: 'gst',
			type: 'text',
			width: 50,
			readOnly: true
		},
		{
			data: 'cgstamt',
			type: 'numeric',
			
			width: 60,
			readOnly: true
		},
		{
			data: 'sgstamt',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 60,
			readOnly: true
		},
		{
			data: 'igstamt',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 50,
			readOnly: true
		},
        
        {
			data: 'ChallanAmt',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 90,
			readOnly: true
		},
		
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		height:'400px',
		//   autoWrapRow: true,
		//   rowHeights: 30,
		columnHeaderHeight: 40,
		minRows: 50,
		maxRows: 50,
		rowHeaders: true,
		colWidths: [50,10,100,50,100,50,100,50,100,100],
		colHeaders: [
        '<?php echo _l('ItemID'); ?>',
        '<?php echo _l('ItemName'); ?>',
        '<?php echo _l('MainItemGroupName'); ?>',
        '<?php echo _l('CaseQty'); ?>',
        '<?php echo _l('Qty'); ?>',
        '<?php echo _l('Cases'); ?>',
        '<?php echo _l('PurchRate'); ?>',
        '<?php echo _l('Disc%'); ?>',
        '<?php echo _l('DiscAmt'); ?>',
        '<?php echo _l('Batch No.'); ?>',
        '<?php echo _l('Mfg Date'); ?>',
        '<?php echo _l('Exp Date'); ?>',
        '<?php echo _l('GST%'); ?>',
        '<?php echo _l('CGSTAMT'); ?>',
        '<?php echo _l('SGSTAMT'); ?>',
        '<?php echo _l('IGSTAMT'); ?>',
        '<?php echo _l('Amount'); ?>',
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
	function requiredValidator(value, callback) {
        if (value === null || value === undefined || value === '') {
            callback(false);
			} else {
            callback(true);
		}
	}
	hot.addHook('afterChange', function(changes, src) {
		if(changes !== null){
			changes.forEach(([row, prop, oldValue, newValue]) => {
				var count = 1; 
				// if(newValue != ''){
				vendor_id = $("#vendor").val();
				
				// $.post(admin_url + 'purchase/items_vendor_check_tcs/'+vendor_id).done(function(result){
				// result_data = JSON.parse(result);
				// })
				if(prop == 'id'){
					vendor_id = $("#vendor").val();
					if(newValue == null &&  newValue == ''){
						hot.setDataAtCell(row,12, '0');
						hot.setDataAtCell(row,13, '0');
						hot.setDataAtCell(row,14, '0');
						hot.setDataAtCell(row,15, '0');
						hot.setDataAtCell(row,16, '0');
						hot.setDataAtCell(row,1, '');
						hot.setDataAtCell(row,2, '');
						hot.setDataAtCell(row,3, '0');
						hot.setDataAtCell(row,4, '0');
						hot.setDataAtCell(row,5, '0');
						hot.setDataAtCell(row,6, '0');
						hot.setDataAtCell(row,7, '0');
						hot.setDataAtCell(row,8, '0');
						
						}else{
						if(vendor_id == ''){
							alert("Please Select vendor");return false;
							}else{
							$.post(admin_url + 'purchase/items_vendor_check/'+newValue+'/'+vendor_id).done(function(response){
								response = JSON.parse(response);
								
								//if(response.items > ''){
								//   if(count == 1){
								
								//  }
								count++;
								$.post(admin_url + 'purchase/items_change/'+newValue).done(function(response){
									
									response = JSON.parse(response);
									
									
									var item_associated = $("#item_associated").val().split(',');
									
									var itemid = response.value.item_code;
									
									// Check if itemid is present in the item_associated array
									if (item_associated.includes(itemid)) {
										hot.setDataAtCell(row,1, response.value.description);
										hot.setDataAtCell(row,2, response.value.name);
										hot.setDataAtCell(row,3, response.value.case_qty);
										hot.setDataAtCell(row,4, '0');
										hot.setDataAtCell(row,5, '0');
										hot.setDataAtCell(row,6, '0');
										hot.setDataAtCell(row,7, '0.00');
										hot.setDataAtCell(row,8, '0.00');
										hot.setDataAtCell(row,12, response.value.taxrate);
										hot.setDataAtCell(row,13, '');
										hot.setDataAtCell(row,14, '');
										hot.setDataAtCell(row,15, '');
										hot.setDataAtCell(row,16, '');
										/*hot.setDataAtCell(row,5, response.value.purchase_price*hot.getDataAtCell(row,4));*/
										
										$('#last-purchase-modal').modal('show');
										var html = '';
										for(var count2 = 0; count2 < response.value.old_rate.length; count2++)
										{
											html += '<tr>';
											html += '<td style="text-align:center;">'+response.value.old_rate[count2].OrderID+'</td>';
											var date = response.value.old_rate[count2].TransDate.substring(0, 10)
											var date_new = date.split("-").reverse().join("/");
											
											html += '<td  style="text-align:center;">'+date_new+'</td>';
											html += '<td >'+response.value.old_rate[count2].company+'</td>';
											html += '<td style="text-align:right;">'+response.value.old_rate[count2].BasicRate+'</td>';
											html += '</tr>';
										}
										$('.table_purchase_report_old tbody').html(html);
										
										count++; 
										} else {
										alert('This Item Is Not Linked With Vendor');
										hot.setDataAtCell(row,0, '');
									}
								});
								/*}else{
									alert('Selected Item Division not assign to Vendor..')
								}*/
							});
							
						}
					}
					
					}else if(prop == 'PurchRate'){
					
					// hot.setDataAtCell(row,13, newValue*hot.getDataAtCell(row,5));
					var state = $("#state_c").val();
					if(state == 'UP'){
						var gst = hot.getDataAtCell(row,12);
						var new_v =  hot.getDataAtCell(row,4)*newValue;
						var dis_per =  hot.getDataAtCell(row,7);
						var disc_amt =   parseFloat(dis_per) * parseFloat(new_v)/100;
						hot.setDataAtCell(row,8, disc_amt);
						// alert(new_v)
						var prec = (gst*(new_v-disc_amt))/100;
						var devide_gst = prec/2
						hot.setDataAtCell(row,13, devide_gst.toFixed(2));
						hot.setDataAtCell(row,14, devide_gst.toFixed(2));
						hot.setDataAtCell(row,15, '0');
						hot.setDataAtCell(row,16, (parseFloat(new_v-disc_amt).toFixed(2)));
						}else{
						var gst = hot.getDataAtCell(row,12)
						var new_v =  hot.getDataAtCell(row,4)*newValue;
						var dis_per =  hot.getDataAtCell(row,7);
						var disc_amt =   parseFloat(dis_per) * parseFloat(new_v)/100;
						var prec = (gst*(new_v-disc_amt))/100;
						
						hot.setDataAtCell(row,13, '0');
						hot.setDataAtCell(row,14, '0');
						hot.setDataAtCell(row,15, prec.toFixed(2));
						hot.setDataAtCell(row,16, (parseFloat(new_v-disc_amt).toFixed(2)));
					}
					updateValue();
					}else if(prop == 'OrderQty'){
					vendor_id = $("#vendor").val();
					// if(newValue !== oldValue){
					hot.setDataAtCell(row,5, parseFloat(newValue/hot.getDataAtCell(row,3)).toFixed(2));
					var dis_per =  hot.getDataAtCell(row,7);
					var disc_amt =   parseFloat(dis_per) * parseFloat(hot.getDataAtCell(row,6)*newValue)/100;
					hot.setDataAtCell(row,8, disc_amt);
					var state = $("#state_c").val();
					if(state == 'UP'){
						var gst = hot.getDataAtCell(row,12)
						var new_v =  hot.getDataAtCell(row,6)*newValue
						var prec = (gst*(new_v-disc_amt))/100;
						var devide_gst = prec/2
						hot.setDataAtCell(row,13, devide_gst.toFixed(2));
						hot.setDataAtCell(row,14, devide_gst.toFixed(2));
						hot.setDataAtCell(row,15, '0');
						hot.setDataAtCell(row,16, (parseFloat(new_v-disc_amt).toFixed(2)));
						}else{
						var gst = hot.getDataAtCell(row,12)
						var new_v =  hot.getDataAtCell(row,6)*newValue
						var prec = (gst*(new_v-disc_amt))/100;
						hot.setDataAtCell(row,13, '0');
						hot.setDataAtCell(row,14, '0');
						hot.setDataAtCell(row,15, prec.toFixed(2));
						hot.setDataAtCell(row,16, (parseFloat(new_v-disc_amt).toFixed(2)));
					}
					updateValue();
					// }
				}
				else if(prop == 'Disc'){
					// if(newValue != oldValue)
					// {
					var TaxableAmt = hot.getDataAtCell(row,6)*hot.getDataAtCell(row,4);
					var disc_amt =   parseFloat(newValue) * parseFloat(TaxableAmt)/100;
					var NewTxableAmt =   parseFloat(TaxableAmt) - parseFloat(disc_amt);
					hot.setDataAtCell(row,8, parseFloat(disc_amt).toFixed(2));
					var state = $("#state_c").val();
					if(state == 'UP'){
						var gst = hot.getDataAtCell(row,12);
						var prec = (gst*NewTxableAmt)/100;
						var devide_gst = prec/2
						hot.setDataAtCell(row,13, parseFloat(devide_gst).toFixed(2));
						hot.setDataAtCell(row,14, parseFloat(devide_gst).toFixed(2));
						hot.setDataAtCell(row,15, '0');
						hot.setDataAtCell(row,16, parseFloat(NewTxableAmt).toFixed(2));
						}else{
						var gst = hot.getDataAtCell(row,12);
						var prec = (gst*NewTxableAmt)/100;
						hot.setDataAtCell(row,13, '0');
						hot.setDataAtCell(row,14, '0');
						hot.setDataAtCell(row,15, parseFloat(prec).toFixed(2));
						hot.setDataAtCell(row,16, parseFloat(NewTxableAmt).toFixed(2));
					}
					updateValue();
					// }
				}
				else if(prop == 'DiscAmt'){
					if(newValue != oldValue)
					{
						var TaxableAmt = hot.getDataAtCell(row,6)*hot.getDataAtCell(row,4);
						var NewTxableAmt =   parseFloat(TaxableAmt) - parseFloat(newValue);
						var disc_per =   parseFloat(newValue) / parseFloat(TaxableAmt)*100;
						if(!isNaN(disc_per))
						{
							hot.setDataAtCell(row,7, parseFloat(disc_per).toFixed(2));
						}
						var state = $("#state_c").val();
						if(state == 'UP'){
							var gst = hot.getDataAtCell(row,12);
							var prec = (gst*NewTxableAmt)/100;
							var devide_gst = prec/2
							hot.setDataAtCell(row,13, parseFloat(devide_gst).toFixed(2));
							hot.setDataAtCell(row,14, parseFloat(devide_gst).toFixed(2));
							hot.setDataAtCell(row,15, '0');
							hot.setDataAtCell(row,16, parseFloat(NewTxableAmt).toFixed(2));
							}else{
							var gst = hot.getDataAtCell(row,12);
							var prec = (gst*NewTxableAmt)/100;
							hot.setDataAtCell(row,13, '0');
							hot.setDataAtCell(row,14, '0');
							hot.setDataAtCell(row,15, parseFloat(prec).toFixed(2));
							hot.setDataAtCell(row,16, parseFloat(NewTxableAmt).toFixed(2));
						}
						updateValue();
					}
					}else if(prop == 'ChallanAmt'){
					// if(newValue != oldValue && oldValue != null)
					// {
					// var Rate =   parseFloat(newValue) / hot.getDataAtCell(row,5);
					// hot.setDataAtCell(row,6, parseFloat(Rate).toFixed(2));
					
					updateValue();
					// }
					}else if(prop == 'cgstamt'){
					updateValue();
					}else if(prop == 'igstamt'){
					updateValue();
				}
			});
		}
	});
	function updateValue(){
		var grand_total = 0;
		var total_cgst = 0;
		var total_sgst = 0;
		var total_igst = 0;
		var totalDisc = 0;
		for (var row_index = 0; row_index <= 40; row_index++) {
			if(parseFloat(hot.getDataAtCell(row_index, 4)) > 0 && parseFloat(hot.getDataAtCell(row_index, 6)) > 0){
				// grand_total += (parseFloat(hot.getDataAtCell(row_index, 13)));
				grand_total += (parseFloat(hot.getDataAtCell(row_index, 4)))*(parseFloat(hot.getDataAtCell(row_index, 6)));
			}
			if(parseFloat(hot.getDataAtCell(row_index, 13)) > 0){
				total_cgst += (parseFloat(hot.getDataAtCell(row_index, 13)));
			}
			if(parseFloat(hot.getDataAtCell(row_index, 14)) > 0){
				total_sgst += (parseFloat(hot.getDataAtCell(row_index, 14)));
			}
			if(parseFloat(hot.getDataAtCell(row_index, 15)) > 0){
				total_igst += (parseFloat(hot.getDataAtCell(row_index, 15)));
			}
			if(parseFloat(hot.getDataAtCell(row_index, 8)) > 0){
				totalDisc += (parseFloat(hot.getDataAtCell(row_index, 8)));
			}
			
			
			
			
			// Charges Calculation
			if(parseFloat(hot2.getDataAtCell(row_index, 4)) > 0){
				grand_total += (parseFloat(hot2.getDataAtCell(row_index, 3)))*(parseFloat(hot2.getDataAtCell(row_index, 4)));
			}
			if(parseFloat(hot2.getDataAtCell(row_index, 6)) > 0){
				total_cgst += (parseFloat(hot2.getDataAtCell(row_index, 6)));
			}
			if(parseFloat(hot2.getDataAtCell(row_index, 7)) > 0){
				total_sgst += (parseFloat(hot2.getDataAtCell(row_index, 7)));
			}
			if(parseFloat(hot2.getDataAtCell(row_index, 8)) > 0){
				total_igst += (parseFloat(hot2.getDataAtCell(row_index, 8)));
			}
		}
		
		var FinalAmt = (grand_total - totalDisc + total_igst + total_sgst + total_cgst);
		
		var IsTDS = $('#IsTDS').val();
		var TDSPer = $('#TDSPer').val();
		var TDS_amt = 0;
		if(IsTDS == 'Y')
		{
			if(parseFloat(TDSPer) > 0){
				TDS_amt = (FinalAmt*TDSPer)/100;
			}
		}
		
		var grand_total_with_tcs = parseFloat(FinalAmt)+TDS_amt;;
		
		grand_total_roundAmt = Math.round(grand_total_with_tcs);
		round_offAmt =  grand_total_roundAmt - grand_total_with_tcs;
		
		$('input[name="dc_total"]').val(totalDisc.toFixed(2));
		$('input[name="Round_OFF"]').val(round_offAmt.toFixed(2));
		$('input[name="total_mn"]').val(grand_total.toFixed(2));
		$('input[name="CGST_amt"]').val(total_cgst.toFixed(2));
		$('input[name="TDS_amt"]').val(TDS_amt.toFixed(2));
		$('input[name="SGST_AMT"]').val(total_sgst.toFixed(2));
		$('input[name="IGST_amt"]').val(total_igst.toFixed(2));
		
		
		var all_total = parseFloat(grand_total_roundAmt);
		$('input[name="Invoice_amt"]').val(Math.round(all_total).toFixed(2));  
	}
	function isNumber(evt) {
		console.log('test')
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	}
	$('#Other_ac1').on('change', function() {
		//  alert($(this).val())
		$.post(admin_url + 'purchase/get_accounts_othertid/'+$(this).val()).done(function(result){
			result_data = JSON.parse(result);
			// console.log(result_data)
			
			$('input[name="Other_ac"]').val(result_data.items.AccountID);
		})
	});
	$('#Freight_AMT').click(function(e) {
		var Freight_AMT = $(this).val();
		$('#Freight_AMT_hidden').val(Freight_AMT)
	});
	
	$('#Freight_AMT').on('blur', function() {
		updateValue();
	});
	
	$('.save_detail').on('click', function() {
		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
	});
	
	<?php } else{ ?>
	
	
	
	function numberWithCommas(x) {
		"use strict";
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	// var dataobjj = 'test'; 
	var dataObject = <?php echo html_entity_decode($pur_order_detail); ?>;
	console.log(dataObject);
	var hotElement = document.querySelector('#example');
	var hotElementContainer = hotElement.parentNode;
	var hotSettings = {
		data: dataObject,
		columns: [
		{
			data: 'id',
			type: 'numeric',
			
		},
		{
			data: 'pur_order',
			type: 'numeric',
			
		},
		{
			data: 'item_code',
			renderer: customDropdownRenderer,
			editor: "chosen",
			width: 100,
			chosenOptions: {
				data: <?php echo json_encode($items); ?>
			},
			
		},
		{
			data: 'description',
			type: 'text',
			width: 150,
			readOnly: true
		},
		{
			data: 'unit_id',
			renderer: customDropdownRenderer,
			editor: "chosen",
			width: 50,
			chosenOptions: {
				data: <?php echo json_encode($units); ?>
			},
			readOnly: true
			
		},
		
		{
			data: 'quantity',
			type: 'numeric',
			
		},
		{
			data: 'Disc',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 50,
		},
		{
			data: 'DiscAmt',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 50,
		},
		
		{
			data: 'tax',
			renderer: customDropdownRenderer,
			editor: "chosen",
			multiSelect:true,
			width: 50,
			chosenOptions: {
				multiple: true,
				data: <?php echo json_encode($taxes); ?>
			}
		},
		{
			data: 'unit_price',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 90,
		},
		
		{
			data: 'total',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			},
			width: 90,
			readOnly: true
		},
		{
			data: 'discount_money',
			type: 'numeric',
			numericFormat: {
				pattern: '0,0'
			}
		},
		{
			data: 'total_money',
			type: 'numeric',
			width: 90,
			numericFormat: {
				pattern: '0,0'
			}
			
		}
		
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		autoWrapRow: true,
		rowHeights: 30,
		columnHeaderHeight: 40,
		minRows: 10,
		maxRows: 40,
		rowHeaders: true,
		colWidths: [0,0,200,50,100,50,100,50,100,50,100,100],
		colHeaders: [
		'',
		'',
		'<?php echo _l('ItemID'); ?>',
		'<?php echo _l('ItemName'); ?>',
		'<?php echo _l('MainItemGroupName'); ?>',
		'<?php echo _l('CaseQty'); ?>',
		'<?php echo _l('PurchRate'); ?>',
		'<?php echo _l('Cases'); ?>',
		'<?php echo _l('Qty'); ?>',
		'<?php echo _l('Disc%'); ?>',
		'<?php echo _l('DiscAmt'); ?>',
		'<?php echo _l('GST%'); ?>',
		'<?php echo _l('CGSTAMT'); ?>',
		'<?php echo _l('SGSTAMT'); ?>',
		'<?php echo _l('IGSTAMT'); ?>',
		'<?php echo _l('Amount'); ?>',
		],
		columnSorting: {
			indicator: true
		},
		autoColumnSize: {
			samplingRatio: 23
		},
		dropdownMenu: true,
		mergeCells: true,
		contextMenu: true,
		manualRowMove: true,
		manualColumnMove: true,
		multiColumnSorting: {
			indicator: true
		},
		hiddenColumns: {
			columns: [0,1],
			indicators: true
		},
		filters: true,
		manualRowResize: true,
		manualColumnResize: true
	};
	
	
	var hot = new Handsontable(hotElement, hotSettings);
	hot.addHook('afterChange', function(changes, src) {
		if(changes !== null){
			changes.forEach(([row, prop, oldValue, newValue]) => {
				if(newValue != ''){
					if(prop == 'item_code'){
						$.post(admin_url + 'purchase/items_change/'+newValue).done(function(response){
							
							response = JSON.parse(response);
							hot.setDataAtCell(row,3, response.value.long_description);
							hot.setDataAtCell(row,4, response.value.unit_id);
							hot.setDataAtCell(row,5, response.value.purchase_price);
							hot.setDataAtCell(row,7, response.value.purchase_price*hot.getDataAtCell(row,6));
						});
						}else if(prop == 'quantity'){
						hot.setDataAtCell(row,7, newValue*hot.getDataAtCell(row,5));
						hot.setDataAtCell(row,9, newValue*hot.getDataAtCell(row,5));
						hot.setDataAtCell(row,12, newValue*hot.getDataAtCell(row,5));
						}else if(prop == 'unit_price'){
						hot.setDataAtCell(row,7, newValue*hot.getDataAtCell(row,6));
						hot.setDataAtCell(row,9, newValue*hot.getDataAtCell(row,6));
						hot.setDataAtCell(row,12, newValue*hot.getDataAtCell(row,6));
						}else if(prop == 'tax'){
						$.post(admin_url + 'purchase/tax_change/'+newValue).done(function(response){
							response = JSON.parse(response);
							hot.setDataAtCell(row,9, (response.total_tax*parseFloat(hot.getDataAtCell(row,7)))/100 + parseFloat(hot.getDataAtCell(row,7)));
							hot.setDataAtCell(row,12, (response.total_tax*parseFloat(hot.getDataAtCell(row,7)))/100 + parseFloat(hot.getDataAtCell(row,7)));
						});
						}else if(prop == 'discount_%'){
						hot.setDataAtCell(row,11, (newValue*parseFloat(hot.getDataAtCell(row,9)))/100 );
						
						}else if(prop == 'discount_money'){
						hot.setDataAtCell(row,12, (parseFloat(hot.getDataAtCell(row,9)) - newValue));
						}else if(prop == 'total_money'){
						var total_money = 0;
						for (var row_index = 0; row_index <= 40; row_index++) {
							if(parseFloat(hot.getDataAtCell(row_index, 12)) > 0){
								total_money += (parseFloat(hot.getDataAtCell(row_index, 12)));
							}
						}
						$('input[name="total_mn"]').val(numberWithCommas(total_money));
					}
				}
			});
		}
	});
	$('.save_detail').on('click', function() {
		$('input[name="pur_order_detail"]').val(JSON.stringify(hot.getData()));   
	});
	
	id = $('select[name="vendor"]').val();
	$.post(admin_url + 'purchase/estimate_by_vendor/'+id).done(function(response){
		response = JSON.parse(response);
		$('select[name="estimate"]').html('');
		$('select[name="estimate"]').append(response.result);
		$('select[name="estimate"]').val(<?php echo html_entity_decode($pur_order->estimate); ?>).change();
		$('select[name="estimate"]').selectpicker('refresh');
		$('#vendor_data').html('');
		$('#vendor_data').append(response.ven_html);
		
		
	});
	
	var total_money = 0;
	for (var row_index = 0; row_index <= 40; row_index++) {
		if(parseFloat(hot.getDataAtCell(row_index, 12)) > 0){
			total_money += (parseFloat(hot.getDataAtCell(row_index, 12)));
		}
		
		
	}
	$('input[name="total_mn"]').val(numberWithCommas(total_money));
	
	
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
				selectedId = optionsList[index].id;
				value.push(optionsList[index].item_code);
			}
		}
		value = value.join(", ");
		
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}
	
	function customDropdownRenderer2(instance, td, row, col, prop, value, cellProperties) {
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
				selectedId = optionsList[index].id;
				value.push(optionsList[index].label);
			}
		} 
		value = value.join(", ");
		// if(value != ''){
		// console.log(td);
		// }
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

<script>
	<?php if(!empty($charges_detail)){ ?>
		var dataObject2 = <?php echo html_entity_decode($charges_detail); ?>;
		<?php }else{ ?>
		var dataObject2 = []; 
	<?php }?>
	
	var hotElement2 = document.querySelector('#example2');
	var hotElementContainer2 = hotElement2.parentNode;
	
	
	var hotSettings2 = {
		data: dataObject2,
		columns: [
		{
			data: 'AccountID',
			type: 'text',
			width: 50,
			readOnly:true,
			
		},
		{ 
			data: 'Account_name',
			renderer: customDropdownRenderer2,
			editor: "chosen",
			width: 150,
			readOnly:false,
			chosenOptions: {
				data:  <?php echo json_encode($acc_head); ?>
			}
		},
		{
			data: 'hsn',
			type: 'numeric',
			width: 50,
			readOnly: true
		},
		{
			data: 'qty',
			type: 'numeric',
			width: 50,
			readOnly: false
		},
		{
			data: 'rate',
			type: 'numeric',
			width: 50,
			readOnly: false
		},
		{
			data: 'Gst',
			type: 'numeric',
			width: 50,
			
		},
		{
			data: 'cgst_amt',
			type: 'numeric',
			width: 50,
			readOnly: true
			
		},
		{
			data: 'sgst_amt',
			type: 'numeric',
			width: 50,
			readOnly: true
		},
		{
			data: 'igst_amt',
			type: 'numeric',
			width: 50,
			readOnly: true
			
		},
		{
			data: 'NetAmt',
			type: 'numeric',
			width: 50,
			readOnly: true
			
		},
		{
			data: 'remark',
			type: 'text',
			width: 100,
			readOnly: true
			
		},
		
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		// height:'300',
		autoWrapRow: true,
		rowHeights: 30,
		columnHeaderHeight: 40,
		minRows: 6,
		maxRows: 70,
		rowHeaders: true,
		colWidths: [200,10,100,50,100],
		colHeaders: [
		'<?php echo _l('AccountID'); ?>',
		'<?php echo _l('Account Name'); ?>',
		'<?php echo _l('HSN'); ?>',
		'<?php echo _l('Qty'); ?>',
		'<?php echo _l('Rate'); ?>',
		'<?php echo _l('GST %'); ?>',
		'<?php echo _l('CGSTAMT'); ?>',
		'<?php echo _l('SGSTAMT'); ?>',
		'<?php echo _l('IGSTAMT'); ?>',
		'<?php echo _l('Amount'); ?>',
		'<?php echo _l('Remark'); ?>',
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
	
	var hot2 = new Handsontable(hotElement2, hotSettings2);
	hot2.addHook('afterChange', function(changes, src) {
		if(changes !== null){
			
			changes.forEach(([row, prop, oldValue, newValue]) => {
				vendor_id = $("#VendorID").val();
				if(prop == 'Account_name'){
					$.post(admin_url + 'purchase/account_change_by_AccountID/'+newValue).done(function(response){
						
						response = JSON.parse(response);
						hot2.setDataAtCell(row,0, response.value.AccountID);
						hot2.setDataAtCell(row,2, response.value.hsn);
						hot2.setDataAtCell(row,3, '0');
						hot2.setDataAtCell(row,4, '0');
						hot2.setDataAtCell(row,5, response.value.Gst);
						hot2.setDataAtCell(row,6, '');
						hot2.setDataAtCell(row,7, '');
						hot2.setDataAtCell(row,8, '');
						hot2.setDataAtCell(row,9, '');
						
					});
				}
				if(prop == 'qty'){
					if(vendor_id == ''){
						if(newValue !== 0)
						{
							hot2.setDataAtCell(row,3, '0');
							hot2.setDataAtCell(row,4, '');
							hot2.setDataAtCell(row,6, '');
							hot2.setDataAtCell(row,7, '');
							hot2.setDataAtCell(row,8, '');
							hot2.setDataAtCell(row,9, '');
							alert('Please Select Vendor');
						}
					}
					else
					{
						
						var selectedOption = $('#vendor option:selected');
						var vendorstate = $('#state_c').val();
						
						var rate = hot2.getDataAtCell(row,4);
						var gst_per = hot2.getDataAtCell(row,5);
						
						amount = rate*newValue;
						var gst_amt = amount*gst_per/100;
						
						var invAmt = amount+gst_amt;
						
						
						if(vendorstate == 'UP')
						{
							var gst_amt = gst_amt/2;
							hot2.setDataAtCell(row,6,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,7,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,8,'0');
						}
						else
						{
							hot2.setDataAtCell(row,8,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,7,'0');
							hot2.setDataAtCell(row,6,'0');
						}
						hot2.setDataAtCell(row,9,invAmt.toFixed(3));
						
					}
					
				}
				
				if(prop == 'rate'){
					if(vendor_id == ''){
						if(newValue !== 0)
						{
							hot2.setDataAtCell(row,3, '0');
							hot2.setDataAtCell(row,4, '0');
							hot2.setDataAtCell(row,6, '');
							hot2.setDataAtCell(row,7, '');
							hot2.setDataAtCell(row,8, '');
							hot2.setDataAtCell(row,9, '');
							alert('Please Select Vendor');
						}
					}
					else
					{
						
						var selectedOption = $('#vendor option:selected');
						var vendorstate = $('#state_c').val();
						
						var qty = hot2.getDataAtCell(row,3);
						var gst_per = hot2.getDataAtCell(row,5);
						
						amount = qty*newValue;
						var gst_amt = amount*gst_per/100;
						
						var invAmt = amount+gst_amt;
						
						
						if(vendorstate == 'UP')
						{
							var gst_amt = gst_amt/2;
							hot2.setDataAtCell(row,6,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,7,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,8,'0');
						}
						else
						{
							hot2.setDataAtCell(row,8,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,7,'0');
							hot2.setDataAtCell(row,6,'0');
						}
						hot2.setDataAtCell(row,9,invAmt.toFixed(3));
						
					}
					
				}
				
				if(prop == 'Gst'){
					if(vendor_id == ''){
						if(newValue !== 0)
						{
							hot2.setDataAtCell(row,3, '0');
							hot2.setDataAtCell(row,4, '0');
							hot2.setDataAtCell(row,6, '0');
							hot2.setDataAtCell(row,7, '');
							hot2.setDataAtCell(row,8, '');
							hot2.setDataAtCell(row,9, '');
							alert('Please Select Vendor');
						}
					}
					else
					{
						
						var selectedOption = $('#vendor option:selected');
						var vendorstate = $('#state_c').val();
						
						var qty = hot2.getDataAtCell(row,3);
						var rate = hot2.getDataAtCell(row,4);
						
						amount = qty*rate;
						var gst_amt = amount*newValue/100;
						
						var invAmt = amount+gst_amt;
						
						
						if(vendorstate == 'UP')
						{
							var gst_amt = gst_amt/2;
							hot2.setDataAtCell(row,6,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,7,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,8,'0');
						}
						else
						{
							hot2.setDataAtCell(row,8,gst_amt.toFixed(3));
							hot2.setDataAtCell(row,7,'0');
							hot2.setDataAtCell(row,6,'0');
						}
						hot2.setDataAtCell(row,9,invAmt.toFixed(3));
						
					}
					
				}
				updateValue();
			});
		}
	});
	$('.save_detail').on('click', function() { 
		$('input[name="charges_details"]').val(JSON.stringify(hot2.getData()));   
	});
</script>	