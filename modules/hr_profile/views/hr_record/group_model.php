<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="head_quarter_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Edit Head Quarter</span>
                    <span class="add-title">Add Head Quarter</span>
                </h4>
            </div>
            <?php echo form_open('admin/hr_profile/head_quarter',array('id'=>'head-quarter-modal')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','head_quarter_name'); ?>
                        <input type="hidden" name="state_id" id="state_id" value="">
                        <input type="hidden" name="status" id="status" value="1">
                        <?php echo form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load',function(){
       appValidateForm($('#head-quarter-modal'), {
        name: 'required',
        state_id: 'required'
        
    }, manage_customer_groups);

       $('#head_quarter_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
        $('#head_quarter_modal .add-title').removeClass('hide');
        $('#head_quarter_modal .edit-title').addClass('hide');
        $('#head_quarter_modal input[name="id"]').val('');
        $('#head_quarter_modal input[name="name"]').val('');
        var state_id = $('select[name="state"]').val('');
        //$('#head_quarter_modal input[name="state_id"]').val(state_id);
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#head_quarter_modal input[name="id"]').val(group_id);
            $('#head_quarter_modal .add-title').addClass('hide');
            $('#head_quarter_modal .edit-title').removeClass('hide');
            $('#head_quarter_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
   });
    function manage_customer_groups(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                if($.fn.DataTable.isDataTable('.table-customer-groups')){
                    $('.table-customer-groups').DataTable().ajax.reload();
                }
                //if($('body').hasClass('dynamic-create-groups') && typeof(response.id) != 'undefined') {
                    /*var groups = $('select[name="headqurter"]');
                    groups.prepend('<option value="'+response.id+'">'+response.name+'</option>');
                    groups.selectpicker('refresh');*/
                    $('#headqurter').append('<option value="' + response.id + '">' + response.name + '</option>');
                    $("#headqurter").selectpicker("refresh");
                //}
                alert_float('success', response.message);
            }
            $('#head_quarter_modal').modal('hide');
        });
        return false;
    }

</script>
