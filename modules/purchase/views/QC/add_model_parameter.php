<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="route_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Edit Parameter</span>
                    <span class="add-title">Add Parameter</span>
                </h4>
            </div>
            <?php echo form_open('admin/purchase/manage_parameter',array('id'=>'vehicle_form')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                      <div class="col-md-4">
                        <?php echo render_input('name','Parameter Name'); ?>
                    </div>
                    <div class="col-md-4">
                        <label for="name" class="control-label"> <small class="req text-danger">* </small>Unit</label>
                        <select id="unit" name="unit" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true" tabindex="-98">
                            <option value="">Non selected</option>
                            <?php 
                            foreach($unit_data as $each)
                            {
                                ?>
                                <option value="<?= $each['id']?>"><?= $each['unit_name']?></option>
                                <?php
                            }
                            ?>
                            </select>
                    </div>
                    
                   
                        
                    <div class="col-md-12">
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

            requestGetJSON('purchase/get_parameter_by_id/' + id).done(function (response) {
                $itemModal.find('input[name="name"]').val(response.parameter_name);
                $itemModal.find('select[name="unit"]').val(response.unit_id);
                $('.selectpicker').selectpicker('refresh');
               
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
        unit: 'required',
        
        
        
    }, manage_invoice_items);
}
</script>