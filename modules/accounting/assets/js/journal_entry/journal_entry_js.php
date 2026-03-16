<script type="text/javascript">
	var commodity_type_value, data;
	(function($) {
		"use strict";
		
		acc_init_currency();
		appValidateForm($('#journal-entry-form'), {
			journal_date: {
				remote: {
					url: site_url + "admin/misc/checkjournal_val",
					type: 'post',
					data: {
						journal_date: function() {
							return $('input[name="journal_date"]').val();
						},
						VoucheriD: function() {
							return $('input[name="VoucheriD"]').val();
						}
					}
				}
			},
			number: 'required',
			TransType: 'required',
			ganeral_account: 'required',
			/*journal_date1: {
				remote: {
				url: site_url + "admin/misc/checkjournal_val",
				type: 'post',
				data: {
				journal_date: function() {
				return $('input[name="journal_date1"]').val();
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
		<?php if(isset($journal_entry))
			{ ?>
			const OrderData = [
			<?php
				foreach($journal_entry->details as $each)
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
		<?php if(isset($journal_entry)){ ?>
			data = <?php echo json_encode($journal_entry->details); ?>
			<?php }else{ ?>
			data = [];
		<?php } ?>
		
		var hotElement1 = document.querySelector('#journal_entry_container');
		
		var commodity_type = new Handsontable(hotElement1, {
			contextMenu: true,
			manualRowMove: true,
			autoWrapRow: true,
			width: '100%',
			height:'400px',
			rowHeights: 5,
			stretchH: 'all',
			defaultRowHeight: 5,
			minRows: 100,
			licenseKey: 'non-commercial-and-evaluation',
			rowHeaders: true,
			autoColumnSize: {
				samplingRatio: 5
			},
			filters: true,
			manualRowResize: true,
			manualColumnResize: true,
			columnHeaderHeight: 10,
			colWidths: [50, 250,80,80,50,80,50,350],
			rowHeights: 5,
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
				data: 'amount',
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
								commodity_type.setDataAtCell(row,0, '');
								commodity_type.setDataAtCell(row,2, '');
								commodity_type.setDataAtCell(row,3, '');
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
									url: "<?php echo admin_url(); ?>accounting/FetchAllIds",  
									method: "POST",
									data: { AccountID: AccountID },
									dataType: "JSON",
									success: function(clientData) {                                        
										
										if (clientData && Array.isArray(clientData.AccountID) && clientData.AccountID.length > 0) 
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
										else if (clientData && Array.isArray(clientData.AccountIDSale)&& clientData.AccountIDSale.length > 0) 
										{   
											const SaleArray = clientData.AccountIDSale;                      
											const cellProperties = commodity_type.getCellMeta(row, 4); 
											
											cellProperties.editor = 'chosen';   
											
											cellProperties.chosenOptions = {
												data: SaleArray.map(function(Sale) {
													return {
														id: Sale.SalesID,  
														label: Sale.SalesID+'('+Sale.Amount+')('+Sale.Date+')'
													};
												})
											};   
											console.log(SaleArray);
											commodity_type.render(); 
											
											commodity_type.addHook('afterChange', function(changes, source) {                                   
												changes.forEach(function([row, prop, oldValue, newValue]) {                                        
													if (prop == 'bill') 
													{ 
														const selectedSale = SaleArray.find(Sale => Sale.SalesID === newValue);
														if (selectedSale) 
														{      
															const BillID = selectedSale.SalesID;           
															$.ajax({
																url: "<?php echo admin_url(); ?>accounting/fetchBillDetails",  
																method: "POST",
																data: { BillID: BillID ,AccountID:AccountID},
																dataType: "JSON",
																success: function(response) {  
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
						
					});
					var journal_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
					var total_amt = 0;
					
					$.each(journal_entry, function(index, value) {
						if(value[6] != '' && value[6] != null){
							if(value[0] == '' || value[0] == null){
								
								}else{
								total_amt += parseFloat(value[6]);
							}
						}
						
					});
					
					$('.total_amt').html(format_money(total_amt));
				}
			}
		});
		commodity_type_value = commodity_type;
		
		$('.journal-entry-form-submiter').on('click', function() {
			$('input[name="journal_entry"]').val(JSON.stringify(commodity_type_value.getData()));
			var journal_entry = JSON.parse($('input[name="journal_entry"]').val());
			var total_amt = 0;
			$.each(journal_entry, function(index, value) {
				if(value[6] != '' && value[6] != null){
					total_amt += parseFloat(value[6]);
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
			
			if(parseFloat(total_amt.toFixed(2)) > 0){
				$('input[name="amount"]').val(parseFloat(total_amt.toFixed(2)));
				$('#journal-entry-form').submit();
				}else{
				alert('<?php echo "Fill Atlest One Row"; ?>');
			}
			
		});
	})(jQuery);
	
	
	function customDropdownRenderer1(instance, td, row, col, prop, value, cellProperties) {
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
		
		if(selectedId == "C"){
			instance.setCellMeta(row,4,'readOnly',true);
			instance.setCellMeta(row,5,'readOnly',false);
		}
		if(selectedId == "D"){
			instance.setCellMeta(row,5,'readOnly',true);
			instance.setCellMeta(row,4,'readOnly',false);
		}
		
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
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
				value.push(optionsList[index].label);
			}
		}
		value = value.join(", ");
		
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}
	
	function calculate_amount_total(){
		"use strict";
		var journal_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
		var total_debit = 0, total_credit = 0;
		$.each(journal_entry, function(index, value) {
			if(value[4] != ''){
				total_debit += parseFloat(value[4]);
			}
			if(value[5] != ''){
				total_credit += parseFloat(value[5]);
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