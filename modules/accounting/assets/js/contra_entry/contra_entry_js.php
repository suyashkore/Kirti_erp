<script type="text/javascript">
	var commodity_type_value, data;
	(function($) {
		"use strict";
		
		acc_init_currency();
		appValidateForm($('#contra-entry-form'), {
			contra_date: {
				remote: {
					url: site_url + "admin/misc/checkcontra_val",
					type: 'post',
					data: {
						contra_date: function() {
							return $('input[name="contra_date"]').val();
						},
						VoucheriD: function() {
							return $('input[name="VoucheriD"]').val();
						}
					}
				}
			},
			contra_number: 'required',
			TransType: 'required',
			ganeral_account: 'required',
			/*contra_date1: {
				remote: {
				url: site_url + "admin/misc/checkcontra_val",
				type: 'post',
				data: {
				contra_date: function() {
				return $('input[name="contra_date1"]').val();
				},
				VoucheriD: function() {
				return $('input[name="VoucheriD"]').val();
				}
				}
				}
			},*/
		});
		
		<?php if(isset($contra_entry)){ ?>
			data = <?php echo json_encode($contra_entry->details); ?>
			<?php }else{ ?>
			data = [];
		<?php } ?>
		
		var hotElement1 = document.querySelector('#contra_entry_container');
		
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
			colWidths: [50, 100, 100, 250],
			rowHeights: 10,
			rowHeaderWidth: [20],
			columns: [
			{
				type: 'text',
				data: 'AccountID',
				readOnly: true,
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
				data: 'amount',
				numericFormat: {
					pattern: '0,0.00',
				},
			},
			{
				type: 'text',
				data: 'description',
			},
			
			],
			colHeaders: [
			'<?php echo "AccountID"; ?>',
			'<?php echo _l('acc_account'); ?>',
			'<?php echo _l('Amount'); ?>',
			'<?php echo "Narration"; ?>'
			],
			data: data,
			afterChange: (changes) => {
				if(changes != null){
					changes.forEach(([row, prop, oldValue, newValue]) => {
						if(prop == 'company'){
							if(newValue !== '' || newValue !== null){
								commodity_type.setDataAtCell(row,0,newValue);
								commodity_type.setDataAtCell(row,2,''); 
								commodity_type.setDataAtCell(row,3,''); 
							}
						}
					});
					var contra_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
					var total_amt = 0;
					
					$.each(contra_entry, function(index, value) {
						if(value[2] != '' && value[2] != null){
							if(value[0] == '' || value[0] == null){
								
								}else{
								total_amt += parseFloat(value[2]);
							}
						}
					});
					
					$('.total_amt').html(format_money(total_amt));
				}
			}
		});
		commodity_type_value = commodity_type;
		
		$('.contra-entry-form-submiter').on('click', function() {
			$('input[name="contra_entry"]').val(JSON.stringify(commodity_type_value.getData()));
			var contra_entry = JSON.parse($('input[name="contra_entry"]').val());
			var total_amt = 0;
			$.each(contra_entry, function(index, value) {
				if(value[2] != '' && value[2] != null){
					
					if(value[0] == '' || value[0] == null){
						
						}else{
						total_amt += parseFloat(value[2]);
					}
				}
			});
			
			if(parseFloat(total_amt.toFixed(2)) > 0){
				$('input[name="amount"]').val(parseFloat(total_amt.toFixed(2)));
				$('#contra-entry-form').submit();
				}else{
				alert('<?php echo _l('you_must_fill_out_at_least_two_detail_lines'); ?>');
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
		var contra_entry = JSON.parse(JSON.stringify(commodity_type_value.getData()));
		var total_debit = 0, total_credit = 0;
		$.each(contra_entry, function(index, value) {
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