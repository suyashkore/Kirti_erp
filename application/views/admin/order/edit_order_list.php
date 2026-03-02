<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php if(has_permission('items','','delete')){ ?>
            
            <!--<a href="#" data-table=".table-invoice-items" class="hide bulk-actions-btn table-btn">Export</a>-->
             <!--<a href="#" data-toggle="modal" data-table=".table-invoice-items" data-target="#items_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>-->
             <div class="modal fade bulk_actions" id="items_bulk_actions" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
               <div class="modal-content">
                <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
               </div>
               <div class="modal-body">
                 <?php if(has_permission('leads','','delete')){ ?>
                   <div class="checkbox checkbox-danger">
                    <input type="checkbox" name="mass_delete" id="mass_delete">
                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                  </div>
                  <!-- <hr class="mass_delete_separator" /> -->
                <?php } ?>
              </div>
              <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
               <a href="#" class="btn btn-info" onclick="items_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
             </div>
           </div>
           <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
       </div>
       <!-- /.modal -->
     <?php } ?>
     <?php hooks()->do_action('before_items_page_content'); ?>
     
      <div class="clearfix"></div>
      
      <div class="_buttons">
         <div class="col-md-3">
               <?php 
            
            //echo render_select( 'states',$states,array( 'id',array( 'state_name')), 'client_state',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
        echo render_date_input('date','Date','');          
            ?>
         </div>
         
        <!-- <div class="col-md-3">
               <?php 
            
             echo render_select('distributor_id',$groups,array('id','name'),'customer_groups',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                        
            ?>
         </div>-->
         
                  
      </div>
      <div class="clearfix"></div>
      <div class="col-md-3">
      <button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
      </div>
      <div class="clearfix"></div>
      <hr class="hr-panel-heading" />
    
    <?php
    $table_data = [];

    if(has_permission('ratemaster','','delete')) {
      //$table_data[] = '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="invoice-items"><label></label></div>';
    }

    $table_data = array(
  "Order No.",
 /* "Order Date",*/
  /*"Invoice",
  "Invoice Date",
  "Challan",*/
  "Party Name",
  "Station",
  "State",
  "DistType",/*
  "CS/CR",*/
  
  "NetOrderAmt",
  /*"OpenBalAmt",
  */
  "Order Type"/*,
  "Remark1"*/);

    /*$cf = get_custom_fields('items');
    foreach($cf as $custom_field) {
      array_push($table_data,$custom_field['name']);
    }*/
    render_datatable($table_data,'edit-order'); ?>
  </div>
</div>
</div>
</div>
</div>
</div>


<?php init_tail(); ?>
<script>

  $(function(){

    $('#search_data').on('click',function(){
    
    var notSortableAndSearchableItemColumns = [];
    <?php if(has_permission('items','','delete')){ ?>
      notSortableAndSearchableItemColumns.push(0);
    <?php } ?>
    
    
	var CustomersServerParams = {};

	CustomersServerParams['date'] = '[name="date"]';
	//CustomersServerParams['distributor_id'] = '[name="distributor_id"]';
	var dates = $("#date").val();
	//var distributor_id = $("#distributor_id").val();
	//alert(customer_type);
	if (dates.trim()!=''){
	    if ($.fn.DataTable.isDataTable('.table-edit-order')) {
	 		$('.table-edit-order').DataTable().destroy();
	 	} 
	  initDataTable('.table-edit-order', admin_url+'order/edit_order_table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'DESC'))); ?>);
       
	}
            else{
              alert("Please select Date First");
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
