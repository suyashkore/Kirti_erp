<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
          <div class="row">
              <div class="col-md-12">
              <h4 class="no-margin font-bold">Accounts List</h4>
              </div>
            <div class="col-md-3">
              <?php echo render_select('ft_account',$accounts,array('AccountID','company'),'acc_account'); ?>
            </div>
           <!-- <div class="col-md-3">
              <?php echo render_select('ft_parent_account',$accounts,array('id','name', 'account_type_name'),'parent_account'); ?>
            </div>-->
            <!--<div class="col-md-3">
              <?php echo render_select('ft_type',$account_types,array('ActGroupID','ActGroupName'),'type'); ?>
            </div>-->
            <div class="col-md-3">
              <?php echo render_select('ft_detail_type',$detail_types,array('SubActGroupID','SubActGroupName'),'SubActGroup Name'); ?>
            </div>
            <div class="col-md-3">
              <?php $active = [ 
                    1 => ['id' => 'all', 'name' => _l('all')],
                    2 => ['id' => '1', 'name' => _l('is_active_export')],
                    3 => ['id' => '0', 'name' => _l('is_not_active_export')],
                  ]; 
                  ?>
                  <?php echo render_select('ft_active',$active,array('id','name'),'staff_dt_active', 'all', array(), array(), '', '', false); ?>
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
      
      "Subgroup",
      "Main Group",
      /*_l('invoice_item_long_description'),*/
     /* _l('invoice_items_list_rate'),
      _l('tax_1'),
      _l('tax_2'),*/
      /*_l('unit')*/
      /*"Start Day",*/
      /*'Active'*/
      ));

    
    $table_data = array_merge($table_data, array(
        
      'Blocked'
      ));
    render_datatable($table_data,'route-table'); ?>
  </div>
</div>
</div>
</div>
</div>
</div>
<?php //$this->load->view('admin/route_master/add_model'); ?>




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
       
       CustomersServerParams['ft_account'] = '[name="ft_account"]';
       //CustomersServerParams['ft_type'] = '[name="ft_type"]';
       CustomersServerParams['ft_detail_type'] = '[name="ft_detail_type"]';
       CustomersServerParams['ft_active'] = '[name="ft_active"]';
       
    var tAPI = initDataTable('.table-route-table', admin_url+'Accounts_master/table', notSortableAndSearchableItemColumns, notSortableAndSearchableItemColumns,CustomersServerParams,[1,'asc']);
    
    $('select[name="ft_account"]').on('change',function(){
           tAPI.ajax.reload();
       });
    /*$('select[name="ft_type"]').on('change',function(){
           tAPI.ajax.reload();
       });*/
    $('select[name="ft_detail_type"]').on('change',function(){
           tAPI.ajax.reload();
       });
       
    $('select[name="ft_active"]').on('change',function(){
           tAPI.ajax.reload();
       });
    
   });
  
 </script>
</body>
</html>
