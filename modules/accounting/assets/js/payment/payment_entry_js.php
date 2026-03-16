<script type="text/javascript">
	var commodity_type_value, data;
	(function($) {
		"use strict";
		
		acc_init_currency();
		appValidateForm($('#payment-entry-form'), {
			payment_date: {
				remote: {
					url: site_url + "admin/misc/checkpayment_val",
					type: 'post',
					data: {
						payment_date: function() {
							return $('input[name="payment_date"]').val();
						},
						VoucheriD: function() {
							return $('input[name="VoucheriD"]').val();
						}
					}
				}
			},
			payment_number: 'required',
			ganeral_account: 'required',
			/*payment_date1: {
				remote: {
				url: site_url + "admin/misc/checkpayment_val",
				type: 'post',
				data: {
				payment_date: function() {
				return $('input[name="payment_date1"]').val();
				},
				VoucheriD: function() {
				return $('input[name="VoucheriD"]').val();
				}
				}
				}
			},*/
		});
		const TypeData = [
		{
			id: "Against",
			label: "Against",
		},
		{
			id: "New",
			label: "New",
		},
		{
			id: "Advance",
			label: "Advance",
		},
		{
			id: "On Account",
			label: "On Account",
		},
		];  
		<?php if(isset($payment_entry))
			{ ?>
			const OrderData = [
			<?php
				foreach($payment_entry->details as $each)
				{
				?>
				{
					id: "<?= $each['TransID']?>",
					label: "<?= $each['TransID']?>",
				},
				<?php
				}
			?>
			];
			
			<?php }else{ ?>
			const OrderData = [
			{
				id: "",
				label: "",
			}, 
			];
			<?php
			}
		?>
		
		<?php if(isset($payment_entry)){ ?>
			data = <?php echo json_encode($payment_entry->details); ?>
			<?php }else{ ?>
			data = [
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			{"AccountID":"","company":"","debit":"","description":""},
			
            ];
		<?php } ?>
		
		var hotElement1 = document.querySelector('#receipt_entry_container');
		
		var commodity_type = new Handsontable(hotElement1, {
			contextMenu: true,
			manualRowMove: true,
			autoWrapRow: true,
			rowHeights: 10,
			stretchH: 'all',
			defaultRowHeight: 10,
			minRows: 100,
			licenseKey: 'non-commercial-and-evaluation',
			width: '100%',
			height:'400px',
			rowHeaders: true,
			autoColumnSize: {
				samplingRatio: 10
			},
			filters: true,
			manualRowResize: true,
			manualColumnResize: true,
			columnHeaderHeight: 10,
			colWidths: [50, 250,80,80,50,80,50,350],
			rowHeights: 10,
			rowHeaderWidth: [20],
			columns: [
			{
				type: 'text',
				data: 'AccountID',
			},
			{
				data: 'company',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: <?php echo json_encode($account_to_select); ?>
				}
				
			},
			{
				type: 'numeric',
				data: 'ClosingBalance',                 
			},
			{
				data: 'against',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
					data: TypeData,
				},
				renderer: function (instance, td, row, col, prop, value, cellProperties) {                      
					td.textContent = value || '';  
					return td;  
				},    
				afterRenderer: function (td, row, col, prop, value) {                                           
					const selectedValue = TypeData.find(item => item.label === value);                       
					if (selectedValue) {
						td.textContent = selectedValue.label;
					}
				}                         
			},
			{
				data: 'bill',
				renderer: customDropdownRenderer,
				editor: "chosen",
				chosenOptions: {
                    data:  OrderData
				},
				renderer: function (instance, td, row, col, prop, value, cellProperties) {
					td.textContent = value || '';  
					return td; 
				},    
				afterRenderer: function (td, row, col, prop, value) {                        
					const selectedValue = OrderData.find(item => item.label === value);
					if (selectedValue) {
                        td.textContent = selectedValue.label;
					}                      
				}                           
			},
			{
				type: 'numeric',
				data: 'pendingAmt',                 
			},
			{
				type: 'numeric',
				data: 'debit',
				numericFormat: {
					pattern: '0.00',
				},
			},
			{
				type: 'text',
				data: 'description',
			},
			
			],
			colHeaders: [
			'<?php echo "AccountID"; ?>',
			'<?php echo "Account Name"; ?>',
			'<?php echo "Closing Balance";?>',
			'<?php echo "Type"; ?>',
			'<?php echo "Bill"; ?>',
			'<?php echo "Pending Amount"; ?>',
			'<?php echo "Amount"; ?>',
			/*'<?php echo _l('credit'); ?>',*/
			'<?php echo "Narration"; ?>'
			],
			data: data,
			afterChange: (changes) => {
				if(changes != null){
					changes.forEach(([row, prop, oldValue, newValue]) => {
						if(prop == 'AccountID'){
							var AccountName = commodity_type.getDataAtCell(row,1);
							if(AccountName == ""){
									if(newValue == null || newValue == ""){
										commodity_type.setDataAtCell(row,1, '0');
										commodity_type.setDataAtCell(row,2, '0');
										commodity_type.setDataAtCell(row,3, '0');
									} else {
								 
										$.post(admin_url + 'accounting/AccountChange/' + encodeURIComponent(newValue)).done(function(response){
											response = JSON.parse(response);
											if(response.value == null){
												alert('AccountID Not available...');
											}else{
												commodity_type.setDataAtCell(row,1, response.value.AccountID);
												commodity_type.setDataAtCell(row,2, '');
												commodity_type.setDataAtCell(row,3, '');
											}
										});
									}								 
							}
						}
						if(prop == 'company'){
							if(newValue !== '' || newValue !== null){
								const AccountID = newValue; 
								$.ajax({
									url: "<?php echo admin_url(); ?>accounting/getclosing_balance",     
									method: 'POST',
									data: { AccountID: AccountID },
									dataType: 'JSON',
									success: function(response) {
										// Update the closing balance column (column index 2)
										commodity_type.setDataAtCell(row, 2, parseFloat(response.closing_balance).toFixed(2));
									},		
								});
								commodity_type.setDataAtCell(row,0,newValue);
								commodity_type.setDataAtCell(row,2,''); 
								commodity_type.setDataAtCell(row,3,''); 
							}else{
								commodity_type.setDataAtCell(row,0, '0');
								commodity_type.setDataAtCell(row,2, '0');
								commodity_type.setDataAtCell(row,3, '0');
							}
						}
						if(prop == 'against')
						{ 
							if(newValue == 'New' || newValue == 'Advance' || newValue == 'On Account')
							{
								const cellProperties = commodity_type.getCellMeta(row, 4);  
								cellProperties.editor = 'text';  
								commodity_type.setDataAtCell(row, 4, '');
								commodity_type.setDataAtCell(row, 5, 0.00);
								commodity_type.render();                 
							}
							else if(newValue == 'Against' || newValue == 'AGAINST')
							{
								commodity_type.setDataAtCell(row, 4, '');
								commodity_type.setDataAtCell(row, 6, 0.00);                
								commodity_type.render();
								
								var AccountID = commodity_type.getDataAtCell(row, 1);                    
								
								$.ajax({
									url: "<?php echo admin_url(); ?>accounting/purchaseIds",  
									method: "POST",
									data: { AccountID: AccountID },
									dataType: "JSON",
									success: function(clientData) {                                        
										
										if (clientData && Array.isArray(clientData.AccountID)) 
										{   
											const purchaseArray = clientData.AccountID;                      
											const cellProperties = commodity_type.getCellMeta(row, 4); 
											
											cellProperties.editor = 'chosen';   
											
											cellProperties.chosenOptions = {
												data: purchaseArray.map(function(purchase) {
													return {
														id: purchase.PurchID,  
														label: purchase.PurchID+'('+purchase.Amount+')('+purchase.Date+')' 
													};
												})
											};                                   
											commodity_type.render(); 
											
											commodity_type.addHook('afterChange', function(changes, source) {                                   
												changes.forEach(function([row, prop, oldValue, newValue]) {                                        
													if (prop == 'bill') 
													{ 
														const selectedSale = purchaseArray.find(purchase => purchase.PurchID === newValue);
														if (selectedSale) 
														{      
															const BillID = selectedSale.PurchID;           
															$.ajax({
																url: "<?php echo admin_url(); ?>accounting/fetchPurchBillDetails",  
																method: "POST",
																data: { BillID: BillID ,AccountID:AccountID},
																dataType: "JSON",
																success: function(response) {                                                                                         
																	// if (response.info && response.info.length > 0) 
																	// {                                                           
																		// const totalAmount = response.info.reduce((sum, currentItem) => 
																		// {
																			// return sum + parseFloat(currentItem.Amount);
																		// }, 0);
																		
																		// const totaldiff = response.CreditEntry.Amount - totalAmount;
																		// commodity_type.setDataAtCell(row, 5, totaldiff.toFixed(2));                                                            
																	// } 
																	// else if(!empty(response.CreditEntry) && response.info.length == 0)
																	// {
																		// const remainingAmt = response.CreditEntry.Amount;
																		// commodity_type.setDataAtCell(row, 5, parseFloat(remainingAmt).toFixed(2));     
																	// }   
																	// else 
																	// {
																		// commodity_type.setDataAtCell(row, 5, 0.00);
																	// }
																	commodity_type.setDataAtCell(row, 5, response.total_pending_amt);
																},
																error: function() {
																	//alert('Error fetching BillID details.');
																}
															});                                          
														}
													}                                                                           
												});
											});
										} 
										else {
											alert('No sales data found for this AccountID.');
										}
									},
									error: function() {
										alert('Error fetching sales data.');
									}
								});                      
							}     
							
							// if(newValue == "")
							// {                     
								// commodity_type.setDataAtCell(0, 3, oldValue);                     
							// }                    
						}
						
						if(prop == 'debit')
						{     
							var type = commodity_type.getDataAtCell(row,3);              
							if(type == "Against" || type == "AGAINST")
							{
								const pendingValue = commodity_type.getDataAtCell(row, 5);
								const newAmount = commodity_type.getDataAtCell(row, 6);
								if(newAmount > pendingValue)
								{
									alert('Enter amount less than pending amount.');
									commodity_type.setDataAtCell(row, 6, pendingValue);                                                            
								}
							}               
						}
					})
					var payment_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
					var total_debit = 0, total_credit = 0;
					
					$.each(payment_entry, function(index, value) {
						if(value[6] != '' && value[6] != null){
							total_debit += parseFloat(value[6]);
						}
					});
					
					$('.total_debit').html(format_money(total_debit));
				}
			}
		});
		commodity_type_value = commodity_type;
		
		$('.payment-entry-form-submiter').on('click', function() {
			$('input[name="payment_entry"]').val(JSON.stringify(commodity_type_value.getData()));
			var payment_entry = JSON.parse($('input[name="payment_entry"]').val());
			var total_debit = 0, total_credit = 0;
			var chk = 0;
			$.each(payment_entry, function(index, value) {
				if(value[6] != '' && value[6] != null){
					total_debit += parseFloat(value[6]);
				}
				
				if((value[3] == 'Against' || value[3] == 'AGAINST') && (value[3] == null || value[3] == '')){
					alert('Please Select Bill');
					chk = 1;
					return;
				}
				
				var pendingAmt = parseFloat(value[5]);
				var newAmt = parseFloat(value[6]);
				
				if ((value[3] == 'Against' || value[3] == 'AGAINST') && newAmt > pendingAmt) {
					alert('Please Enter Amount Less Then Or Equal To Pending Amount');
					chk = 1;
					return;
				}
			});
			
			total_debit = total_debit.toFixed(2);
			if(total_debit > 0){
				if(chk == 0){
					$('#payment-entry-form').submit();
				}
	    		$('input[name="amount"]').val(total_debit);
			}else{
	    		alert('<?php echo _l('you_must_fill_out_at_least_two_detail_lines'); ?>');
			}
		});
	})(jQuery);
	
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
				value.push(optionsList[index].label);
			}
		}
		value = value.join(", ");
		
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}
	
	function calculate_amount_total(){
		"use strict";
		var payment_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
		var total_debit = 0, total_credit = 0;
		$.each(payment_entry, function(index, value) {
			if(value[3] != ''){
				total_debit += parseFloat(value[3]);
			}
		});
		
		$('.total_debit').html(format_money(total_debit));
		$('.total_credit').html(format_money(total_credit));
	}
	
	// Set the currency for accounting
	function acc_init_currency() {
		"use strict";
		
		var selectedCurrencyId = <?php echo html_entity_decode($currency->id); ?>;
		
		requestGetJSON('misc/get_currency/' + selectedCurrencyId)
		.done(function(currency) {
			// Used for formatting money
			accounting.settings.currency.decimal = currency.decimal_separator;
			accounting.settings.currency.thousand = currency.thousand_separator;
			accounting.settings.currency.symbol = currency.symbol;
			accounting.settings.currency.format = currency.placement == 'after' ? '%v %s' : '%s%v';
		});
	}
	
</script>