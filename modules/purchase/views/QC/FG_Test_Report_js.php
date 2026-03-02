
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
	
	function numberWithCommas(x) {
		"use strict";
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	const dummyData = [
	{
		id: "Yes",
		label: "Yes",
	},
	{
		id: "No",
		label: "No",
	},
	
	
	];
	<?php if(!empty($order_entry_detail)){ ?>
		var dataObject = <?php echo html_entity_decode($order_entry_detail); ?>;
		<?php }else{ ?>
		var dataObject = []; 
	<?php }?>
	
    var hotElement = document.querySelector('#example');
    var hotElementContainer = hotElement.parentNode;
	
	
    var hotSettings = {
		data: dataObject,
		columns: [
        
		 { 
            data: 'ItemID',
            renderer: customDropdownRenderer2,
            editor: "chosen",
            chosenOptions: {
				data:  <?php echo json_encode($item_code); ?>
			}
		},
		{
			data: 'batch_no',
			type: 'text',
		},
		{
			data: 'taste',
			type: 'text',
		},
		{
			data: 'smell',
			type: 'text',
			
		},
		{
			data: 'appearance',
			type: 'text',
			
		},
		{
			data: 'moisture',
			type: 'text',
			
		},
		// {
			// data: 'ash',
			// type: 'text',
			
		// },
		// {
			// data: 'salt',
			// type: 'text',
		// },
		// {
			// data: 'f_m',
			// type: 'text',
			
		// },
		{
			data: 'sign',
			type: 'text',
			
		},
		{
			data: 'remark',
			type: 'text',
			
		},
        
		],
		licenseKey: 'non-commercial-and-evaluation',
		stretchH: 'all',
		width: '100%',
		height:'420px',
		//   autoWrapRow: true,
		//   rowHeights: 30,
		columnHeaderHeight: 40,
		minRows: 25,
		maxRows: 70,
		rowHeaders: true,
		colWidths: [100,50,70,70,70,100,100,100,100,100],
		colHeaders: [
		'<?php echo _l('Varient Name'); ?>',
		'<?php echo _l('Batch No'); ?>',
        '<?php echo _l('Taste'); ?>',
        '<?php echo _l('Smell'); ?>',
        '<?php echo _l('Appearance'); ?>',
        '<?php echo _l('Moisture(Daily)'); ?>',
        // '<?php echo _l('ASH'); ?>',
        // '<?php echo _l('Salt %'); ?>',
        // '<?php echo _l('F.M'); ?>',
        '<?php echo _l('Sign'); ?>',
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
	
	
	var hot = new Handsontable(hotElement, hotSettings);
	hot.addHook('afterChange', function(changes, src) {
		
		if(changes !== null){
			
			changes.forEach(([row, prop, oldValue, newValue]) => {
				
				
				
			});
		}
		
	});
	
	
	$('.save_detail').on('click', function() {
		$('input[name="QC_detail"]').val(JSON.stringify(hot.getData()));   
	});
	
	
	function customRenderer(instance, td, row, col, prop, value, cellProperties) {
		"use strict";
		Handsontable.renderers.TextRenderer.apply(this, arguments);
		if(td.innerHTML != ''){
			td.innerHTML = td.innerHTML + ''
			td.className = 'htRight';
		}
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