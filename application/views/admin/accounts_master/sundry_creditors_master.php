<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
          <div class="row">
              <div class="col-md-12" style="margin-bottom:10px;">
              <h4 class="no-margin font-bold">SUNDRY CREDITORS</h4>
              <!--<hr class="hr-panel-heading" />-->
              </div>
              
                <div class="col-md-3 leads-filter-column">
                    
                <?php echo render_select('distributor_state',$state,array('id','state_name'),'distributor_state'); ?>
                </div>
                
                <div class="col-md-2 leads-filter-column">
                      <!--<?php echo render_select('status',$staffs,array('staffid',array('firstname','lastname')),'status','',array(),array(),'','',false); ?>-->
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Non selected</option>
                                <option value="1">Active</option>
                                <option value="0">DeActive</option>
                            </select>
                        </div>
                        
                    </div>
            
          </div>
        
    <?php
    $table_data = [];

    /*if(has_permission('items','','delete')) {
      $table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="invoice-items"><label></label></div>';
    }*/

    $table_data = array_merge($table_data, array(
        'AccountID',
      "Account Name",
      
      "Station",
      "City",
      "State"
      /*_l('invoice_item_long_description'),*/
     /* _l('invoice_items_list_rate'),
      _l('tax_1'),
      _l('tax_2'),*/
      /*_l('unit')*/
      /*"Start Day",*/
      /*'Active'*/
      ));

    
    $table_data = array_merge($table_data, array(
        
      'Action'
      ));
    render_datatable($table_data,'route-table'); ?>
  </div>
</div>
</div>
</div>
</div>
</div>
<?php $this->load->view('admin/route_master/add_model'); ?>




<?php init_tail(); ?>
<script>
  $(function(){

    var notSortableAndSearchableItemColumns = [];
    <?php if(has_permission('items','','delete')){ ?>
      notSortableAndSearchableItemColumns.push(0);
    <?php } ?>
    
    var CustomersServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       
       CustomersServerParams['distributor_state'] = '[name="distributor_state"]';
       CustomersServerParams['status'] = '[name="status"]';
       
    var tAPI = initDataTable('.table-route-table', admin_url+'Accounts_master/sundry_creditors_table', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,CustomersServerParams,[1,'asc']);
    
    $('select[name="distributor_state"]').on('change',function(){
           tAPI.ajax.reload();
       });
    $('select[name="status"]').on('change',function(){
           tAPI.ajax.reload();
       });
       
    if(get_url_param('groups_modal')){
       // Set time out user to see the message
       setTimeout(function(){
         $('#groups').modal('show');
       },1000);
     }
     
    
    // Item Division Add 
     $('#new-item-group-insert').on('click',function(){
      var group_name = $('#item_group_name').val();
      if(group_name != ''){
        $.post(admin_url+'invoice_items/add_group',{name:group_name}).done(function(){
         window.location.href = admin_url+'invoice_items?groups_modal=true';
       });
      }
    });
    
    if(get_url_param('main_groups_modal')){
       // Set time out user to see the message
       setTimeout(function(){
         $('#maingroups').modal('show');
       },1000);
     }
     
     if(get_url_param('sub_groups_modal')){
       // Set time out user to see the message
       setTimeout(function(){
         $('#subgroups').modal('show');
       },1000);
     }
    
    
   
    

     $('body').on('click','.edit-item-group',function(e){
      e.preventDefault();
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
    });

     $('body').on('click','.update-item-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'invoice_items/update_group/'+group_id,{name:name}).done(function(){
         window.location.href = admin_url+'invoice_items';
       });
      }
    });
    
     $('body').on('click','.edit-item-main-group',function(e){
      e.preventDefault();
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.main_group_edit').toggleClass('hide');
      tr.find('.main_group_edit input').val(tr.find('.group_name_plain_text').text());
    });

     $('body').on('click','.update-item-main-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.main_group_edit input').val();
      if(name != ''){
        $.post(admin_url+'invoice_items/update_main_group/'+group_id,{name:name}).done(function(){
         //window.location.href = admin_url+'invoice_items';
         window.location.href = admin_url+'invoice_items?main_groups_modal=true';
       });
      }
    });
    
   });
  function items_bulk_action(event) {
    if (confirm_delete()) {
      var mass_delete = $('#mass_delete').prop('checked');
      var ids = [];
      var data = {};

      if(mass_delete == true) {
        data.mass_delete = true;
      }

      var rows = $('.table-invoice-items').find('tbody tr');
      $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') === true) {
          ids.push(checkbox.val());
        }
      });
      data.ids = ids;
      $(event).addClass('disabled');
      setTimeout(function() {
        $.post(admin_url + 'invoice_items/bulk_action', data).done(function() {
          window.location.reload();
        }).fail(function(data) {
          alert_float('danger', data.responseText);
        });
      }, 200);
    }
  }
 </script>
</body>
</html>
