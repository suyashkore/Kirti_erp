<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="hsn_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Edit HSN</span>
                    <span class="add-title">Add HSN</span>
                </h4>
            </div>
            <?php echo form_open('admin/hsn_master/manage',array('id'=>'hsn_form')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php echo render_input('name','HSN Code'); ?>
                    </div>
                    <div class="col-md-4">
                        <?php echo render_input('hsndesc','HSN Description'); ?>
                    </div>
                    
                    <!--<div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label"><small class="req text-danger">* </small> Is Active? </label>
                            <select class="selectpicker" name="status" id="status" data-width="100%" data-none-selected-text="-- Select --" data-live-search="false" required>
                                <option value="1" selected>Active</option> 
                                <option value="0">Deactive</option> 
                            </select>
                        </div>
                         
                    </div>-->
                    
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
function manage_hsn(form) {
    var data = $(form).serialize();

    var url = form.action;
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
          
                $('.table-hsn-table').DataTable().ajax.reload(null, false);
            
            alert_float('success', response.message);
        }
        $('#hsn_modal').modal('hide');
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
    $("body").on('show.bs.modal', '#hsn_modal', function (event) {

        $('.affect-warning').addClass('hide');

        var $itemModal = $('#hsn_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $itemModal.find('input[name="name"]').attr('readonly', false);
        $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');

        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);

            requestGetJSON('hsn_master/get_hsn_by_id/' + id).done(function (response) {
                $itemModal.find('input[name="name"]').val(response.name);
                $itemModal.find('input[name="name"]').attr('readonly', true);
                
                $itemModal.find('input[name="hsndesc"]').val(response.hsndesc);
                
               
                init_selectpicker();
                init_color_pickers();
                init_datepicker();

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_hsn_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#hsn_modal', function (event) {
        $('#item_select').selectpicker('val', '');
    });

   validate_hsn_form();
}
function validate_hsn_form(){
    // Set validation for invoice item form
    appValidateForm($('#hsn_form'), {
        name: 'required',
        hsndesc: 'required',
        
        
        
    }, manage_hsn);
}
</script>
