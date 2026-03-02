<script>
	var salesChart;
	var groupsChart;
	var paymentMethodsChart;
	var customersTable;
	var report_from = $('input[name="report-from"]');
	var report_to = $('input[name="report-to"]');

	var date_range = $('#date-range');
	var report_from2 = $('input[name="report-from2"]');
	var report_to2 = $('input[name="report-to2"]');

	var date_range2 = $('#date-range2');
	var report_from_choose = $('#report-time');
	var fnServerParams = {
		"report_months": '[name="months-report"]',
		"report_months2": '[name="months-report2"]',
		"report_from": '[name="report-from"]',
		"report_to": '[name="report-to"]',
		"report_from2": '[name="report-from2"]',
		"report_to2": '[name="report-to2"]',
		/*"role_filter": "[name='role[]']",
		"department_filter": "[name='department[]']",*/
		"staff_filter": "[name='staff_filter']",
		"staff_filter2": "[name='staff_filter2']",
		/*"rel_type": "[name='rel_type[]']",
		"months_filter": "[name='months-report']",
		"staff_fillter": "[name='staff_fillter']",
		"months_2_report": "[name='months_2_report']",
		"staff_2_fillter": "[name='staff_2_fillter[]']",
		"staff_2_fillter2": "[name='staff_2_fillter2']",
		"roles_2_fillter": "[name='roles_2_fillter[]']",
		"type_2_fillter": '[name="type_2_fillter"]',*/
	};
	(function(){
		"use strict";
		init_datepicker();
		report_from.on('change', function() {
			var val = $(this).val();
			var report_to_val = report_to.val();
			if (val != '') {
				report_to.attr('disabled', false);
				if (report_to_val != '') {
					//gen_reports();
				}
			} else {
				report_to.attr('disabled', true);
			}
		});

		report_to.on('change', function() {
			var val = $(this).val();
			if (val != '') {
			//	gen_reports();
			}
		});
		
		report_from2.on('change', function() {
			var val2 = $(this).val();
			var report_to_val2 = report_to2.val();
			if (val2 != '') {
				report_to2.attr('disabled', false);
				if (report_to_val2 != '') {
					//gen_reports();
				}
			} else {
				report_to2.attr('disabled', true);
			}
		});

		report_to2.on('change', function() {
			var val2 = $(this).val();
			if (val2 != '') {
			//	gen_reports();
			}
		});
		
		$('select[name="months-report"]').on('change', function() {
			var val = $(this).val();
			report_to.attr('disabled', true);
			report_to.val('');
			report_from.val('');
			if (val == 'custom') {
				date_range.addClass('fadeIn').removeClass('hide');
				return;
			} else {
				if (!date_range.hasClass('hide')) {
					date_range.removeClass('fadeIn').addClass('hide');
				}
			}
			//gen_reports();
		});
		$('select[name="months-report2"]').on('change', function() {
			var val2 = $(this).val();
			report_to2.attr('disabled', true);
			report_to2.val('');
			report_from2.val('');
			if (val2 == 'custom') {
				date_range2.addClass('fadeIn').removeClass('hide');
				return;
			} else {
				if (!date_range2.hasClass('hide')) {
					date_range2.removeClass('fadeIn').addClass('hide');
				}
			}
		//	gen_reports();
		});

		/*$('select[name="months-report"]').on('change', function() {
			var val = $(this).val();
			report_to.attr('disabled', true);
			report_to.val('');
			report_from.val('');
			if (val == 'custom') {
				date_range.addClass('fadeIn').removeClass('hide');
				return;
			} else {
				if (!date_range.hasClass('hide')) {
					date_range.removeClass('fadeIn').addClass('hide');
				}
			}
			gen_reports();
		});*/
	/*	$('select[name="staff_2_fillter[]"],select[name="department_2_fillter[]"],select[name="workplace_2_fillter[]"],select[name="route_point_2_fillter[]"],select[name="word_shift_2_fillter[]"],select[name="type_2_fillter"],select[name="roles_2_fillter[]"],select[name="role[]"],select[name="department[]"],select[name="staff[]"],select[name="rel_type[]"],select[name="year_requisition"],select[name="role[]"], select[name="months_2_report"]').on('change', function() {
			gen_reports();
		});*/
		
		/*$('select[name="staff_2_fillter2"]').on('change', function() {
		    var dd = $('select[name="staff_2_fillter2"]').val();
		    if(dd == ""){
		       $('#second_table').addClass('hide');
		       $('#first_table').removeClass('hide');
		       gen_reports();
		    } else {
		        $('#second_table').removeClass('hide');
		        $('#first_table').addClass('hide');
		        gen_reports();
		    }
		});*/
	
	})(jQuery);
	var current_type = '';
	var list_fillter = {};
	function init_report(e, type) {
		"use strict";
		current_type = type;
		var report_wrapper = $('#report');
        report_wrapper.removeClass('hide');
        $('head title').html($(e).text());
        if(type == 'history_check_in_out2'){
            
			$('.table1').removeClass('hide');
			$('#history_check_in_out2').removeClass('hide');
			$('.r1t2').addClass('hide');
			$('.r2t1').addClass('hide');
			$('.r2t2').addClass('hide');
			$('.r1t1').removeClass('hide');
			$('.table2').addClass('hide');
			$('.travel_report').addClass('hide');
			$('select[name="staff_filter"]').selectpicker('refresh');
			$('select[name="months-report"]').selectpicker('refresh');
		} 
		else if(type == 'travel_report'){
			$('.table2').removeClass('hide');
			$('.travel_report').removeClass('hide');
			$('.r1t1').addClass('hide');
			$('.r1t2').addClass('hide');
			$('.r2t2').addClass('hide');
			$('.r2t1').removeClass('hide');
			$('.table1').addClass('hide');
			$('#history_check_in_out2').addClass('hide');
			$('select[name="staff_filter2"]').selectpicker('refresh');
			$('select[name="months-report2"]').selectpicker('refresh');
		}
		//gen_reports();
	}

	 // Main generate report function
	 function gen_reports() {
	 	"use strict";
	 	if(current_type != ''){
	 		switch(current_type){
	 			 
	 			case 'history_check_in_out2':
	 			history_check_in_out_report2();
	 			break;  
	 			case 'travel_report':
	 			travel_reports();
	 			break;
	 		}
	 	}
	 }


	 function history_check_in_out_report2(){
	 	"use strict";
	 	$('.title_table').text('Check in/out & Location Visit History');
	 	if ($.fn.DataTable.isDataTable('.table-history_check_in_out_report2')) {
	 		$('.table-history_check_in_out_report2').DataTable().destroy();
	 	} 
	 	var dd = $('select[name="staff_filter"]').val();
	 	if(dd == ""){
	 	    
	 	$('#r1t2').addClass('hide');
		$('#r1t1').removeClass('hide');
	 	  initDataTable('.table-history_check_in_out_report2', admin_url + 'timesheets/history_check_in_out_report2', false, false, fnServerParams, [1, 'desc']);
	 	
	 	}else{
	 	    staff_table();
	 	}
	 }
	 
	 function staff_table(){
	 	"use strict";
	 	$('.title_table').text('Check in/out & Location Visit History');
	    $("caption").remove();
	 	if ($.fn.DataTable.isDataTable('.table-history_check_in_out_report3')) {
	 		$('.table-history_check_in_out_report3').DataTable().destroy();
	 	} 
	 	
	 	$('#r1t1').addClass('hide');
	 	$('#r2t1').addClass('hide');
	 	$('#r2t2').addClass('hide');
	 	$('#r1t2').removeClass('hide');
	 	var staff_id = $('select[name="staff_filter"]').val();
	 	$.ajax({
            url: "<?=admin_url()?>timesheets/staff_details",
            type: 'post',
            dataType: "json",
            data: {
              staff_id: staff_id
            },
            success: function( data ) {
              
              $('.table-history_check_in_out_report3').append('<caption style="caption-side: top"> <span style="font-size:15px;font-weight:700;">Staff Name : </span>'+data.full_name+' </caption>');
            }
          });
		 initDataTable('.table-history_check_in_out_report3', admin_url + 'timesheets/history_check_in_out_report3', false, false, fnServerParams, [1, 'desc']);
	 	
	 }
	 function travel_reports(){
	 	"use strict";
	 	$('.title_table').text('Travel Distance Reports');
	 	var dd = $('select[name="staff_filter2"]').val();
	 	if(dd == ""){
	 	    
	 	$('#r1t1').addClass('hide');
	 	$('#r1t2').addClass('hide');
	 	$('#r2t2').addClass('hide');
	 	$('#r2t1').removeClass('hide');
		if ($.fn.DataTable.isDataTable('.table-travel_report')) {
	 		$('.table-travel_report').DataTable().destroy();
	 	}
	 	  initDataTable('.table-travel_report', admin_url + 'timesheets/travel_report', false, false, fnServerParams, [1, 'desc']);
	 	
	 	}else{
	 	    
	 	    $('#r1t1').addClass('hide');
	 	    $('#r1t2').addClass('hide');
	 	    $('#r2t1').addClass('hide');
		    $('#r2t2').removeClass('hide');
    		if ($.fn.DataTable.isDataTable('.table-travel_report2')) {
    	 		$('.table-travel_report2').DataTable().destroy();
    	 	}
	 	    $("caption").remove();
	 	    var staff_id = $('select[name="staff_filter2"]').val();
    	 	$.ajax({
                url: "<?=admin_url()?>timesheets/staff_details",
                type: 'post',
                dataType: "json",
                data: {
                  staff_id: staff_id
                },
                success: function( data ) {
                  //response( data );
                  $('.table-travel_report2').append('<caption style="caption-side: top"> <span style="font-size:15px;font-weight:700;">Staff Name : </span>'+data.full_name+' &nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:15px;font-weight:700;">State Name : </span> '+data.state_name +' &nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:15px;font-weight:700;">City Name : </span> '+data.city_name +' &nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:15px;font-weight:700;">Role Name : </span> '+data.staff_role +' &nbsp;&nbsp;&nbsp;&nbsp; <span style="font-size:15px;font-weight:700;">Report To : </span> '+data.report_to +'</caption>');
                }
              });
	 	    initDataTable('.table-travel_report2', admin_url + 'timesheets/travel_report2', false, false, fnServerParams, [1, 'desc']);
	 	}
	 	
	 }
	 

	</script>
