<?php defined('BASEPATH') or exit('No direct script access allowed');

?>
<div class="modal fade" id="remark_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('invoice_item_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('invoice_item_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/order/remark_update',array('id'=>'invoice_item_form')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                
                <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Remark</label>
                            <input type="text" name="remark" id="remark" class="form-control">
                        </div>
                        
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
<?php

$table_data = array(
  "Order No.",
  "Order Date",
  /*"Invoice",
  "Invoice Date",
  "Challan",*/
  "Party Name",
  "Station",
  "State",
  "DistType",/*
  "CS/CR",*/
  
  "NetOrderAmt",
  "OpenBalAmt",
  
  "Order Type",
  "Remark");
/*$custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));
foreach($custom_fields as $field){
  array_push($table_data,$field['name']);
}*/
$table_data = hooks()->apply_filters('order_table_columns', $table_data);
render_datatable($table_data, (isset($class) ? $class : 'pending_order'));
?>

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
                $('.table-pending_order').DataTable().ajax.reload(null, false);
            
            alert_float('success', response.message);
        }
        $('#remark_modal').modal('hide');
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
    $("body").on('show.bs.modal', '#remark_modal', function (event) {

        $('.affect-warning').addClass('hide');

        var $itemModal = $('#remark_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');
        //remove it
        $itemModal.find('input[name="item_code1"]').removeAttr("disabled");
        
        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {
            //add disabled
                $itemModal.find('input[name="item_code1"]').attr('disabled', 'disabled');
            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);

            requestGetJSON('order/get_remark_by_orderid/' + id).done(function (response) {
                $itemModal.find('input[name="remark"]').val(response.remark);
                

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_item_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#remark_modal', function (event) {
        $('#item_select').selectpicker('val', '');
    });

   validate_item_form();
}
function validate_item_form(){
    // Set validation for invoice item form
    appValidateForm($('#invoice_item_form'), {
        remark: 'required',
        
        
    }, manage_invoice_items);
}
  </script>
