<script>
	var addnewkpi;
	var table_registration_leave = $('table.table-table_registration_leave');
	var table_additional_timesheets  = $('table.table-table_additional_timesheets');
	var rest_time = 0;
	var time = 0;
	var hour_working;
	var number_day_off = <?php echo html_entity_decode($number_day_off); ?>;
	(function(){
		"use strict";
		
		$('div[id="number_days_off_2"]').addClass('hide');
		$('.fc-datetimepicker').datetimepicker();
		
		var requisitionServerParams = {
			"status_filter": "[name='status_filter[]']",
			"rel_type_filter": "[name='rel_type_filter[]']",
			"chose": "[name='chose']",
			"department_filter": "[name='department_filter[]']",
		};
		
		var table_contract = $('.table-table_contract');
		initDataTable(table_registration_leave,admin_url + 'timesheets/table_registration_leave', [0], [0],requisitionServerParams, [1, 'desc']);
		
		//hide first column
		var hidden_columns = [0];
		$(table_registration_leave).DataTable().columns(hidden_columns).visible(false, false);
		$.each(requisitionServerParams, function() {
			$('#status_filter').on('change', function() {
				table_registration_leave.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
			
			$('#rel_type_filter').on('change', function() {
				table_registration_leave.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
			
			$('#chose').on('change', function() {
				table_registration_leave.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
			
			$('#department_filter').on('change', function() {
				table_registration_leave.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
		});
		
		var addtimesheetServerParams = {
			"status_filter_ats": "[name='status_filter_ats[]']",
			"rel_type_filter_ats": "[name='rel_type_filter_ats[]']",
			"chose_ats": "[name='chose_ats']",
			"department_ats": "[name='department_ats[]']",
		};
		
		initDataTable(table_additional_timesheets,admin_url + 'timesheets/table_additional_timesheets', [0], [0],addtimesheetServerParams, [3, 'desc']); 
		$.each(addtimesheetServerParams, function() {
			$('#status_filter_ats').on('change', function() {
				table_additional_timesheets.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
			
			$('#rel_type_filter_ats').on('change', function() {
				table_additional_timesheets.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
			
			$('#chose_ats').on('change', function() {
				table_additional_timesheets.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
			
			$('#department_ats').on('change', function() {
				table_additional_timesheets.DataTable().ajax.reload()
				.columns.adjust()
				.responsive.recalc();
			});
		});
		
		$('#staff').on('change', function() {
			$('select[name="type_of_leave"]').val('');
			$('select[name="type_of_leave"]').selectpicker('refresh');
			$('select[name="type_of_leave"]').change();
			// console.log();
			
			if($(this).val() !== ''){
				var staffid = $(this).val();
				$.ajax({
					url:"<?php echo admin_url(); ?>timesheets/GetAnnualLeave",
					dataType:"JSON",
					method:"POST",
					cache: false,
					data:{staffid:staffid},
					success:function(data){
						$('#days_off').html('Number of days off:'+data.days_off);
						if (data.number_day_off == 0) {
							$('#number_day_off').html('Number of leave days remaining in the year: ' + data.number_day_off)
							.css('color', 'red');
							} else {
							$('#number_day_off').html('Number of leave days remaining in the year: ' + data.number_day_off)
							.css('color', 'black');
						}
						$('#number_day_offinput').val(data.number_day_off);
					}
				});
			}
		});
		$('#type_of_leave').on('change', function() {
			var staff = $('#staff').val();
			if(staff == ''){
				alert('Pleasr Select Staff');
				$('select[name="type_of_leave"]').val('');
				$('select[name="type_of_leave"]').selectpicker('refresh');
			}
			var value = $('select[name="type_of_leave"]').val();
			if(value == 8){
				$('div[id="number_days_off_2"]').removeClass('hide');
				// var number_day_off = <?php echo html_entity_decode($number_day_off); ?>;
				var number_day_off = $('#number_day_offinput').val();
				if($('input[name="number_of_leaving_day"]').val() > number_day_off){
					$('button[type="submit"]').attr('disabled', 'true');
					}else{
					$('button[type="submit"]').removeAttr('disabled');
				}
				}else{
				$('div[id="number_days_off_2"]').addClass('hide');
				$('button[type="submit"]').removeAttr('disabled');
			}
			
		});
		
		$('#rel_type').on('change', function() {
			
			$('.approx-fr').addClass('hide');
			$('#leave_').removeClass('col-md-6').addClass('col-md-12');
			var value = $(this).children("option:selected").val();
			
			$('#number_of_leaving_day').val(0);
			$('.btn-submit').removeAttr('disabled');    
			/*đi trễ về sơm , ra ngoài*/
			if(value == 2 || value == 6 || value == 3)
			{
				appValidateForm($('#requisition-form'), {
					subject: 'required',
					start_time:  'required'
				});
				$("div.end_time").addClass('hide');
				$('#requisition-form input[name="end_time"]').val("<?php echo html_entity_decode($current_date) ?>");
				
				$("div.start_time").removeClass('col-md-6');
				$("div.start_time").addClass('col-md-12');
				}else{
				$("div.end_time").removeClass('hide');
				$("div.start_time").addClass('col-md-6');
				$("div.start_time").removeClass('col-md-12');
			}
			//Leave
			if(value == 1)
			{
				appValidateForm($('#requisition-form'), {
					subject: 'required',
					start_time:  'required',
					number_of_leaving_day:  'required',
					end_time: 'required'
				});
				$('.approx-fr').removeClass('hide');
				$('#number_of_leaving_day').val(0.5);
				var val = $('select[name="type_of_leave"]').val();
				if(val == 8){
					$('div[id="number_days_off_2"]').removeClass('hide');
					}else{
					$('div[id="number_days_off_2"]').addClass('hide');
				}
				
				$('div[id="number_days_off"]').removeClass('hide');
				
				$('div[id="type_of_leave"]').removeClass('hide');
				$('div[id="type"]').addClass('col-md-6');
				$('div[id="type"]').removeClass('col-md-12');
				$("div.end_time").removeClass('hide');
				$("div.start_time").removeClass('hide');
				
				$('.date_input').removeClass('hide');
				$('.datetime_input').addClass('hide');
				}else{
				$('div[id="number_days_off"]').addClass('hide');
				$('div[id="number_days_off_2"]').addClass('hide');
				$('div[id="type_of_leave"]').addClass('hide');
				$('div[id="type"]').removeClass('col-md-6');
				$('div[id="type"]').addClass('col-md-12');
				$('.date_input').addClass('hide');
				$('.datetime_input').removeClass('hide');
			}
			
			/*đi công tác*/
			if(value == 4)
			{
				appValidateForm($('#requisition-form'), {
					subject: 'required',
					start_time:  'required',
					end_time: 'required'
				});
				$('div[id="advance_payment_rq"]').removeClass('hide');
				$("div.start_time").removeClass('hide');
				$('div[id="div_according_to_the_plan"]').removeClass('hide');
				}else{
				$('div[id="div_according_to_the_plan"]').addClass('hide');
				$('div[id="advance_payment_rq"]').addClass('hide');
			}
			
			/*nghi viêc*/
			
			if(value == 5){
				/*set date value because input is required*/
				$('#requisition-form input[name="start_time"]').val("<?php echo html_entity_decode($current_date) ?>");
				$('#requisition-form input[name="end_time"]').val("<?php echo html_entity_decode($current_date) ?>");
				
				$("div.start_time ").addClass('hide');
				$("div.end_time").addClass('hide'); 
			}
		});
		
		appValidateForm($('#requisition-form'), {
			subject: 'required',
			staff:  'required',
			rel_type:  'required',
			type_of_leave:  'required',
			start_time:  'required',
			number_of_leaving_day:  'required',
			end_time: 'required'
			
		});
		
		$("body").on('change', '#rel_type', function() {
			var rel_type = $('select[name="rel_type"]').val();
			if(rel_type == '1'){
				$('#leave_handover_recipients').removeClass('hide');
			}
			else{
				$('#leave_handover_recipients').addClass('hide');
			}
		});
		
		<?php if(isset($additional_timesheets_id)){ ?>
			view_additional_timesheets(<?php echo html_entity_decode($additional_timesheets_id); ?>);
		<?php } ?>
		
		addnewkpi = $('.new-kpi-al').children().length;
		
		$("body").on('click', '.new_kpi', function() {
			//get position row
			var idrow = $(this).parents('.new-kpi-al').find('.get_id_row').attr("value");
			if ($(this).hasClass('disabled')) { return false; }
			
			var newkpi = $(this).parents('.new-kpi-al').find('#new_kpi').eq(0).clone().appendTo($(this).parents('.new-kpi-al'));
			
			newkpi.find('button[data-toggle="dropdown"]').remove();
			
			
			newkpi.find('label[for="used_to[0]"]').remove();
			newkpi.find('input[id="used_to[0]"]').attr('name', 'used_to[' + addnewkpi + ']').val('');
			newkpi.find('input[id="used_to[0]"]').attr('id', 'used_to[' + addnewkpi + ']').val('');
			
			
			newkpi.find('input[id="amoun_of_money[0]"]').attr('name', 'amoun_of_money[' + addnewkpi + ']').val('');
			newkpi.find('input[id="amoun_of_money[0]"]').attr('id', 'amoun_of_money[' + addnewkpi + ']').val('');
			newkpi.find('label[for="amoun_of_money[0]"]').remove();
			
			newkpi.find('div[name="button_add_kpi"]').removeAttr("style");
			
			newkpi.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
			newkpi.find('button[name="add"]').removeClass('new_kpi').addClass('remove_kpi').removeClass('btn-success').addClass('btn-danger');
			
			newkpi.find('select').selectpicker('val', '');
			addnewkpi++;
			
			$("input[data-type='currency']").on({
				keyup: function() {        
					formatCurrency($(this));
				},
				blur: function() { 
					formatCurrency($(this), "blur");
				}
			});
		});
		
		$("body").on('click', '.remove_kpi', function() {
			$(this).parents('#new_kpi').remove();
		});
		
		$("input[data-type='currency']").on({
			keyup: function() {        
				formatCurrency($(this));
			},
			blur: function() { 
				formatCurrency($(this), "blur");
			}
		});
		
		
		$("#additional_day").change(function(){
			$.post(admin_url + 'timesheets/get_time_working',{date:$("#additional_day").val()}).done(function(response){
				response = JSON.parse(response);
				rest_time = parseInt(response.rest_time);
				hour_working = parseFloat(response.hour_working);
			});
		});
		
		
		$("#time_in").change(function(){
			var time_in = $("#time_in").val();
			var time_out = $("#time_out").val();
			if(time_out != '' && time_in != ''){
				
				if($('#timekeeping_type').val() == 'W'){
					if(timeToSeconds(time_in+':00') >= timeToSeconds('12:00:00') && timeToSeconds(time_in+':00') <= timeToSeconds('13:00:00')){
						time = (timeToSeconds(time_out+':00') - timeToSeconds('13:00:00')) / 3600;
						}else if(timeToSeconds(time_in+':00') >= timeToSeconds('13:00:00')){
						time = (timeToSeconds(time_out+':00') - timeToSeconds(time_in+':00')) / 3600;
						}else{
						time = (timeToSeconds(time_out+':00') - timeToSeconds(time_in+':00') - rest_time) / 3600;
					}
					}else{
					time = (timeToSeconds(time_out+':00') - timeToSeconds(time_in+':00')) / 3600;
				}
				if(time > hour_working){
					$('#timekeeping_value').val(hour_working);
					}else{
					if(time < 0){
						$('#timekeeping_value').val(0);
						}else{
						$('#timekeeping_value').val(time.toFixed(2));
					}
				}
			}
		});
		
		
		
		$("#time_out").change(function(){
			var time_in = $("#time_in").val();
			var time_out = $("#time_out").val();
			if(time_out != '' && time_in != ''){
				if($('#timekeeping_type').val() == 'W'){      
					if(timeToSeconds(time_in+':00') >= timeToSeconds('12:00:00') && timeToSeconds(time_in+':00') <= timeToSeconds('13:00:00')){
						time = (timeToSeconds(time_out+':00') - timeToSeconds('13:00:00')) / 3600;
						}else if(timeToSeconds(time_in+':00') >= timeToSeconds('13:00:00') || timeToSeconds(time_out+':00') <= timeToSeconds('12:00:00')){
						time = (timeToSeconds(time_out+':00') - timeToSeconds(time_in+':00')) / 3600;
						}else{
						time = (timeToSeconds(time_out+':00') - timeToSeconds(time_in+':00') - rest_time) / 3600;
					}
					}else{
					time = (timeToSeconds(time_out+':00') - timeToSeconds(time_in+':00')) / 3600;
				}
				if(time > hour_working){
					$('#timekeeping_value').val(hour_working);
					}else{
					if(time < 0){
						$('#timekeeping_value').val(0);
						}else{
						$('#timekeeping_value').val(time.toFixed(2));
					}
				}
			}
		});
		
		
		
		// $("#number_of_leaving_day").change(function(){
			// get_day_from_date();
		// });
		$("#start_time").change(function(){
			get_day_from_date();
		});
		
		$("#end_time").change(function(){
			get_day_from_date();
		});
		function get_day_from_date(){
			var data = {};
			data.start_time = $("#start_time").val();
			data.end_time = $("#end_time").val();
			// console.log($("#end_time").val());
			data.number_of_leaving_day = $("#number_of_leaving_day").val();
			data.staffid = $('input[name="userid"]').val();
			
			$.post(admin_url + 'timesheets/calculate_number_days_off',data).done(function(response){
				response = JSON.parse(response);
				$('input[name="number_of_leaving_day"]').val(response);
				$('#number_days_off label').text('<?php echo _l('Number_of_leaving_day'); ?>: '+response);
				// var number_day_off = <?php echo html_entity_decode($number_day_off); ?>;
				var number_day_off = $('#number_day_offinput').val();;
				var value = $('select[name="type_of_leave"]').val();
				if(value == 8){
					if(response > number_day_off){
						$('button[type="submit"]').attr('disabled', 'true');
						}else{
						$('button[type="submit"]').removeAttr('disabled');
					}
				}
			});
		}
		
		$("#timekeeping_type").change(function(){
			var value = $("#timekeeping_type").val();
			if(value == 'OT'){
				$("#overtime_setting").removeClass('hide');
				}else{
				$("#overtime_setting").addClass('hide');
			}
		});
		
		var data_send_mail = {};
		<?php if(isset($send_mail_approve)){ ?>
			data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
			$.post(admin_url+'timesheets/send_mail', data_send_mail).done(function(response){
			});
		<?php } ?>
	})(jQuery);
	
	
	function btn_additional_timesheets(){
		"use strict";
		$('#additional_timesheets_modalss').modal();   
	}
	
	function new_requisition(){
		"use strict";
		$('#requisition_m').modal('show');
		$('.edit-title').addClass('hide');
		$('.add-title').removeClass('hide');
	}
	
	function add_requisition(){
		"use strict";
		$subject  = $('#subject').val();
		$approver_id = $('select[name="staff[]"]').val();
		$rel_type = $('select[name="rel_type"]').val();
		$type_of_leave = $('select[name="type_of_leave"]').val();
		$start_date = $("#start_date").val();
		$end_date = $("#end_date").val();
		$file = $("#file").val();
		if(typeof $end_date === 'undefined')
		{
			$end_date = $start_date;
			
			}else{
			$end_date = $("#end_date").val();
		}
		$reason = $("#reason").val();
		$followers_id = $('select[name="follower[]"]').val();
		
		if($approver_id != '' && $followers_id != ''){
			
			var formData = new FormData();
			formData.append("subject", $subject);
			formData.append("approver_id", $approver_id);
			formData.append("followers_id", $followers_id);
			formData.append("rel_type", $rel_type);
			formData.append("type_of_leave", $type_of_leave);
			formData.append("start_time", $start_date);
			formData.append("end_time", $end_date);
			formData.append("reason", $reason);
			formData.append("file", $file);
			formData.append("csrf_token_name", $('input[name="csrf_token_name"]').val());
			
			$.ajax({ 
				url: admin_url + 'timesheets/add_requisition_ajax', 
				method: 'post', 
				data: formData, 
				contentType: false, 
				processData: false
				
				}).done(function(response) {
				response = JSON.parse(response);
				
				if(response.success == true){
					alert_float('success', "<?php echo _l('Add_requisition_success') ; ?>");
					$('#requisition_m').removeClass('sidebar-open');
					table_registration_leave.DataTable().ajax.reload().columns.adjust().responsive.recalc();
					
					}else if(response.success == false){
					alert_float('danger', "<?php echo _l('Add_requisition_success') ; ?>");
					$('#requisition_m').removeClass('sidebar-open');
					table_registration_leave.DataTable().ajax.reload().columns.adjust().responsive.recalc();
					}else{
					alert_float('warning', "<?php echo _l('Requisition_information_already_exists') ; ?>");
				}
			});
			return false;
			}else{
			alert_float('danger', "<?php echo _l('please_check_again') ; ?>");
		}
	}           
	function staff_bulk_actions(){
		"use strict";
		$('#table_registration_leave_bulk_actions').modal('show');
	}
	
	// Leads bulk action
	function staff_delete_bulk_action(event) {
		"use strict";
		if (confirm_delete()) {
			var mass_delete = $('#mass_delete').prop('checked');
			
			if(mass_delete == true){
				var ids = [];
				var data = {};
				
				data.mass_delete = true;
				data.rel_type = 'timesheets_requisition';
				
				var rows = $('#table-table_registration_leave').find('tbody tr');
				$.each(rows, function() {
					var checkbox = $($(this).find('td').eq(0)).find('input');
					if (checkbox.prop('checked') === true) {
						ids.push(checkbox.val());
					}
				});
				
				data.ids = ids;
				$(event).addClass('disabled');
				setTimeout(function() {
					$.post(admin_url + 'timesheets/timesheets_delete_bulk_action', data).done(function() {
						window.location.reload();
						}).fail(function(data) {
						$('#table_registration_leave_bulk_actions').modal('hide');
						alert_float('danger', data.responseText);
					});
				}, 200);
				}else{
				window.location.reload();
			}
		}
	}
	
	function view_additional_timesheets(id){
		"use strict";
		$.post(admin_url+'timesheets/get_data_additional_timesheets/'+id).done(function(response){
			response = JSON.parse(response);
			$('#additional_timesheets_modal').html('');
			
			$('#additional_timesheets_modal').append(response.html);
			
			$('#additional_timesheets_modal').modal('show');
			$('select[name="approver_c"]').selectpicker('refresh');
		});
	}
	
	
	function formatNumber(n) {
		"use strict";
		return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	function formatCurrency(input, blur) {
		"use strict";
		
		var input_val = input.val();
		
		if (input_val === "") { return; }
		
		var original_len = input_val.length;
		
		var caret_pos = input.prop("selectionStart");
		
		if (input_val.indexOf(".") >= 0) {
			
			var decimal_pos = input_val.indexOf(".");
			var left_side = input_val.substring(0, decimal_pos);
			var right_side = input_val.substring(decimal_pos);
			
			left_side = formatNumber(left_side);
			
			right_side = formatNumber(right_side);
			
			right_side = right_side.substring(0, 2);
			input_val = left_side + "." + right_side;
			
		} 
		else {
			input_val = formatNumber(input_val);
			input_val = input_val;
		}
		input.val(input_val);
		var updated_len = input_val.length;
		caret_pos = updated_len - original_len + caret_pos;
		input[0].setSelectionRange(caret_pos, caret_pos);
	}
	
	function timeToSeconds(time) {
		"use strict";
		time = time.split(/:/);
		var Seconds = parseInt(time[0] * 3600) + parseInt(time[1] * 60) + parseInt(time[2]);
		return Seconds;
	}
	function input_method(){
		"use strict";
		$('#input_method_modal').modal('show');
	}
	
	
	
	function approve_request(id, rel_type){
		"use strict";
		change_request_approval_status(id,1,rel_type);
	}
	
	function deny_request(id, rel_type){
		"use strict";
		change_request_approval_status(id,2,rel_type);
	}
	
	function change_request_approval_status(id, status, rel_type){
		"use strict";
		var data = {};
		data.rel_id = id;
		data.approve = status;
		data.rel_type = rel_type;
		data.note = '';
		$.post(admin_url + 'timesheets/approve_request/' + id, data).done(function(response){
			response = JSON.parse(response); 
			if (response.success === true || response.success == 'true') {
				alert_float('success', response.message);
				window.location.reload();
			}
		});
	}
	
	function change_approx(el, next){
		"use strict";
		var date1 = $(el).val();
		var date2 = $('input[name="'+next+'"]').val();
		var arr_date1 = date1.split(" ");
		var arr_date2 = date2.split(" ");
		var dates1 = arr_date1[0].split("-");
		var dates2 = arr_date2[0].split("-");
		var check = 0;
	}
	
	function get_date(el){
		"use strict";
		var val = $(el).val();
		if(val < 0.5){
			$(el).closest('.form-group').addClass('has-error');
			alert_float('warning', '<?php echo _l('please_enter_a_value_greater_than_or_equal_to_0.5') ?>k');
			$('.btn-submit').attr('disabled', true);    
			return ;
		}
		else{
			$(el).closest('.form-group').removeClass('has-error');
			$('.btn-submit').removeAttr('disabled');    
		}
		var data = {};
		data.staffid = $('input[name="userid"]').val();
		data.startdate = $('input[name="start_time"]').val();
		data.enddate = $('input[name="end_time"]').val();
		data.number_of_days = $('input[name="number_of_leaving_day"]').val();
		$.post(admin_url+'timesheets/get_date_leave',data).done(function(response){
			response = JSON.parse(response);
			$('input[name="end_time"]').val(response.end_date);
			get_day_from_date2();
		});
		
	}
	
	function get_day_from_date2(){
			var data = {};
			data.start_time = $("#start_time").val();
			data.end_time = $("#end_time").val();
			data.number_of_leaving_day = $("#number_of_leaving_day").val();
			data.staffid = $('input[name="userid"]').val();
			
			$.post(admin_url + 'timesheets/calculate_number_days_off',data).done(function(response){
				response = JSON.parse(response);
				$('input[name="number_of_leaving_day"]').val(response);
				$('#number_days_off label').text('<?php echo _l('Number_of_leaving_day'); ?>: '+response);
				// var number_day_off = <?php echo html_entity_decode($number_day_off); ?>;
				var number_day_off = $('#number_day_offinput').val();;
				var value = $('select[name="type_of_leave"]').val();
				if(value == 8){
					if(response > number_day_off){
						$('button[type="submit"]').attr('disabled', 'true');
						}else{
						$('button[type="submit"]').removeAttr('disabled');
					}
				}
			});
		}
	
	function send_request_approve(id, addedfrom){
		"use strict";
		var data = {};
		data.rel_id = id;
		data.rel_type = 'additional_timesheets';
		data.addedfrom = addedfrom;
		$("body").append('<div class="dt-loader"></div>');
		$.post(admin_url + 'timesheets/send_request_approve', data).done(function(response){
			response = JSON.parse(response);
			if(response.type == 'choose'){
				$("body").find('.dt-loader').remove();
				if (response.success === true || response.success == 'true') {
					alert_float('success', response.message);
					window.location.reload();
					}else{
					alert_float('warning', response.message);
					window.location.reload();
				}
				}else if(response.type == 'not_choose'){
				$("body").find('.dt-loader').remove();
				$('#choose_approver').html('');
				alert_float('success', response.message);
				$('#choose_approver').append(response.html);
				$('.selectpicker').selectpicker({});
			}
		});
	}
	function choose_approver(id, addedfrom){
		"use strict";
		var data = {};
		data.rel_id = id;
		data.rel_type = 'additional_timesheets';
		data.addedfrom = addedfrom;
		data.staffid = $('#approver_c').val();
		if(data.staffid != ''){
			$("body").append('<div class="dt-loader"></div>');
			$.post(admin_url + 'timesheets/choose_approver', data).done(function(response){
				response = JSON.parse(response);
				$("body").find('.dt-loader').remove();
				if (response.success === true || response.success == 'true') {
					alert_float('success', response.message);
					window.location.reload();
					}else{
					alert_float('warning', response.message);
					window.location.reload();
				}
				
			});
			}else if(data.staffid == ''){
			alert_float('warning', '<?php echo _l('please_choose_approver'); ?>');
		}
	}
	
	$("#requisition-form").submit(function(e) {
		"use strict";
		var value = $('#rel_type').val();
		if(value == 1){
			var type_of_leave = $('select[name="type_of_leave"]').val();
			if(type_of_leave == 8){
				var number_of_leaving_day = $('#number_of_leaving_day').val();
				var number_day_offinput = $('#number_day_offinput').val();
				if(number_day_offinput == 0) {        
					alert_float('warning', '<?php echo _l('cannot_create_number_of_days_remaining_is_0'); ?>');
					return false;   
				}
				if(number_of_leaving_day == 0){
					$('.btn-submit').attr('disabled', true); 
				}
				else{
					$('.btn-submit').removeAttr('disabled');    
				}
			}
			else{
				$('.btn-submit').removeAttr('disabled');          
			}
		}
		else{
			$('.btn-submit').removeAttr('disabled');    
		}
		if($("#requisition-form").valid()){
			$('.btn-submit').text('Processing ...');   
			$('.btn-submit').attr('disabled', true);    
		} 
	});
	
	$("#edit_timesheets-form").submit(function(e) {
		"use strict";
		if($("#edit_timesheets-form").valid()){
			$('.btn-additional-timesheets').text('Processing ...');   
			$('.btn-additional-timesheets').attr('disabled', true); 
		}
	});
	appValidateForm($('#edit_timesheets-form'), {
		additional_day: 'required'
	});
	
	$('input[name="start_time_s"]').change(function(){
		var rel_type = $('select[name="rel_type"]').val();
		if(rel_type == 3 || rel_type == 2 || rel_type == 6){
			return false;
		}
		var fit_start_time  = $(this).val(); 
		var fit_end_time    = $('input[name="end_time_s"]').val(); 
		if(new Date(datetimeToDate(fit_start_time)).getTime() > new Date(datetimeToDate(fit_end_time)).getTime())
		{
			alert_float('warning', 'From Date must be less than or equal to To Date');
			$(this).val(fit_end_time);
		}
	});
	$('input[name="end_time_s"]').change(function(){
		var rel_type = $('select[name="rel_type"]').val();
		if(rel_type == 3 || rel_type == 2 || rel_type == 6){
			return false;
		}
		var fit_start_time    = $('input[name="start_time_s"]').val(); 
		var fit_end_time  = $(this).val();
		if(new Date(datetimeToDate(fit_start_time)).getTime() > new Date(datetimeToDate(fit_end_time)).getTime())
		{
			alert_float('warning', 'To Date must be greater than or equal to From Date');
			$(this).val(fit_start_time);
		}
	});
	
	$('input[name="start_time"]').change(function(){
		var rel_type = $('select[name="rel_type"]').val();
		if(rel_type == 3 || rel_type == 2 || rel_type == 6){
			return false;
		}
		var fit_start_time  = $(this).val(); 
		var fit_end_time    = $('input[name="end_time"]').val(); 
		if(new Date(datetimeToDate(fit_start_time)).getTime() > new Date(datetimeToDate(fit_end_time)).getTime())
		{
			alert_float('warning', 'From Date must be less than or equal to To Date');
			$(this).val(fit_end_time);
		}
	});
	$('input[name="end_time"]').change(function(){
		var rel_type = $('select[name="rel_type"]').val();
		if(rel_type == 3 || rel_type == 2 || rel_type == 6){
			return false;
		}
		var fit_start_time    = $('input[name="start_time"]').val(); 
		var fit_end_time  = $(this).val();
		if(new Date(datetimeToDate(fit_start_time)).getTime() > new Date(datetimeToDate(fit_end_time)).getTime())
		{
			alert_float('warning', 'To Date must be greater than or equal to From Date');
			$(this).val(fit_start_time);
		}
	});
	
	function datetimeToDate(datetime){
		var currentTime = new Date(datetime);
		var month = currentTime.getMonth() + 1;
		var date = currentTime.getDate();
		var year = currentTime.getFullYear();
		return year + '-' + month + '-' + date;
	}
</script>