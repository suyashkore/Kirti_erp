<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="route_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Edit Route</span>
                    <span class="add-title">Add Route</span>
				</h4>
			</div>
            <?php echo form_open('admin/route_master/manage',array('id'=>'vehicle_form')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php echo render_input('name','Route Name'); ?>
					</div>
                    <div class="col-md-4">
                        <?php echo render_input('km','Route KM'); ?>
					</div>
                    <!--<div class="col-md-4">
                        <?php //echo render_date('start_date','Start Date');
						echo render_date_input('start_date','Start Date'); ?>
					</div>-->
                    
                    <!--<div class="col-md-3">
                        <div class="form-group">
						<label class="form-label"><small class="req text-danger">* </small>Type</label>
						<select class="selectpicker" name="type" id="type" data-width="100%" data-none-selected-text="-- Select --" data-live-search="false">
						<option value="own">own</option> 
						<option value="Other">Other</option> 
						</select>
                        </div>
                        
					</div>-->
                    
                    
                    
                    
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label"><small class="req text-danger">* </small> Is Active? </label>
                            <select class="selectpicker" name="status" id="status" data-width="100%"  data-live-search="true" data-none-selected-text="None Selected" required>
                                <option value="1" selected>Active</option> 
                                <option value="0">Deactive</option> 
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="form-label"><small class="req text-danger">* </small>Route Points</label>
							<select class="form-control selectpicker" name="routepoints[]" multiple id="routepoints" data-width="100%" data-none-selected-text="None Selected" data-live-search="true" required>
								<?php
									foreach($RoutePoints as $key => $value){
									?>
									<option value="<?= $value['id']?>"><?= $value['PointName']?></option> 
									<?php
									}
								?>
							</select>
						</div>
					</div>
					
                    
                    <div class="col-md-12">
                        <!--<div class="alert alert-warning affect-warning hide">
                            <?php echo _l('changing_items_affect_warning'); ?>
						</div>-->
                        
						
						<div class="clearfix mbot15"></div>
						
						
						
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
				<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
<script>
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if(typeof(jQuery) != 'undefined'){
        init_item_js();
		} else {
		window.addEventListener('load', function () {
			var initItemsJsInterval = setInterval(function(){
				if(typeof(jQuery) != 'undefined') {
					init_item_js();
					clearInterval(initItemsJsInterval);
				}
			}, 1000);
		});
	}
	// Items add/edit
	function manage_invoice_items(form) {
		var data = $(form).serialize();
		
		var url = form.action;
		$.post(url, data).done(function (response) {
			response = JSON.parse(response);
			if (response.success == true) {
				
                // Is general items view
                $('.table-route-table').DataTable().ajax.reload(null, false);
				
				alert_float('success', response.message);
				location.reload(true);
			}
			$('#route_modal').modal('hide');
			}).fail(function (data) {
			alert_float('danger', data.responseText);
		});
		return false;
	}
	function init_item_js() {
		// Add item to preview from the dropdown for invoices estimates
		$("body").on('change', 'select[name="item_select"]', function () {
			var itemid = $(this).selectpicker('val');
			if (itemid != '') {
				add_item_to_preview(itemid);
			}
		});
		
		// Items modal show action
		$("body").on('show.bs.modal', '#route_modal', function (event) {
			
			$('.affect-warning').addClass('hide');
			
			var $itemModal = $('#route_modal');
			$('input[name="itemid"]').val('');
			$itemModal.find('input').not('input[type="hidden"]').val('');
			$itemModal.find('textarea').val('');
			$itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
			$('select[name="tax2"]').selectpicker('val', '').change();
			$('select[name="tax"]').selectpicker('val', '').change();
			$itemModal.find('.add-title').removeClass('hide');
			$itemModal.find('.edit-title').addClass('hide');
			
			var id = $(event.relatedTarget).data('id');
			// If id found get the text from the datatable
			if (typeof (id) !== 'undefined') {
				
				$('.affect-warning').removeClass('hide');
				$('input[name="itemid"]').val(id);
				
				requestGetJSON('route_master/get_route_by_id/' + id).done(function (response) {
					$itemModal.find('input[name="name"]').val(response.name);
					$itemModal.find('input[name="km"]').val(response.km);
					//$itemModal.find('input[name="start_date"]').val(response.start_date);
					//$itemModal.find('#type').selectpicker('val', response.type);
					
					$itemModal.find('#status').selectpicker('val', response.status);
					
					var RoutePoints = response.RoutePoints.map(function(point) {
						return point.PointID;
					});
					
					// Set the selected values in the selectpicker
					$itemModal.find('#routepoints').selectpicker('val', RoutePoints);
					
					init_selectpicker();
					init_color_pickers();
					init_datepicker();
					
					$itemModal.find('.add-title').addClass('hide');
					$itemModal.find('.edit-title').removeClass('hide');
					validate_item_form();
				});
				
			}
		});
		
		$("body").on("hidden.bs.modal", '#route_modal', function (event) {
			$('#item_select').selectpicker('val', '');
		});
		
		validate_item_form();
	}
	function validate_item_form(){
		// Set validation for invoice item form
		appValidateForm($('#vehicle_form'), {
			name: 'required',
			capacity: 'required',
			km: 'required',
			
			
			
		}, manage_invoice_items);
	}
</script>
