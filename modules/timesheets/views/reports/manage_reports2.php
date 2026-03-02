<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            
            <div class="panel_s">
               <div class="panel-body">
                  
                  <div class="clearfix"></div>
                  <hr class="hr-panel-heading" />
                  
                  <div class="row ">
                    
                    <!--<div class="col-md-3">
                      <?php echo render_select('status',$staff,array('staffid',array('firstname','lastname')),'Staff','',array(),array(),'','',false); ?>
                    </div>-->
                    <div class="col-md-3 leads-filter-column">
                    <?php
                    $selected_staff =  $this->uri->segment('5');
                    ?>
                    <?php echo render_select('staff',$staff,array('staffid',array('firstname','lastname')),'staff',$selected_staff,array(),array(),'','',false); ?>
                    </div>
                    <div class="col-md-2 leads-filter-column">
                     <?php
                    $selected_date =  $this->uri->segment('4');
                    ?>
                     <?php echo render_date_input('report_date','Date',$selected_date); ?>
                    </div>
                    <!--<div class="col-md-3 leads-filter-column">
                      <?php echo render_select('responsible_admin',$staff_list,array('staffid',array('firstname','lastname')),'responsible_admin'); ?>
                    </div>
                    
                    <div class="col-md-2 leads-filter-column">
                      
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Non selected</option>
                                <option value="1">Active</option>
                                <option value="0">DeActive</option>
                            </select>
                        </div>
                        
                    </div>-->
                               
                </div>
                  
                  <hr class="hr-panel-heading" />
                 
                  
                  <div class="clearfix mtop20"></div>
                  <?php
                    
                     
                     $table_data = array();
                     $_table_data = array(
                      
                        array(
                         'name'=>"Staff Name",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                         /*array(
                         'name'=>"Firm Name",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),*/
                         
                         /*array(
                         'name'=>'KM',
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-active')
                        ),*/
                        array(
                         'name'=>"location",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-station')
                        ),
                        array(
                         'name'=>"Battery",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-station')
                        ),
                        array(
                         'name'=>"Mobile",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-groups')
                        ),
                        array(
                         'name'=>"GPS Status",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-state')
                        ),
                        array(
                         'name'=>"API Time",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-town')
                        ),
                        array(
                         'name'=>"Date",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-sales_person')
                        ),
                      );
                     foreach($_table_data as $_t){
                      array_push($table_data,$_t);
                     }

                     $custom_fields = get_custom_fields('customers',array('show_on_table'=>1));
                     foreach($custom_fields as $field){
                      array_push($table_data,$field['name']);
                     }
                     
                     
                     $table_data = hooks()->apply_filters('customers_table_columns', $table_data);

                     render_datatable($table_data,'clients',[],[
                           'data-last-order-identifier' => 'customers',
                           'data-default-order'         => get_table_last_order('customers'),
                     ]);
                     ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
       var CustomersServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
       CustomersServerParams['staff'] = '[name="staff"]';
       CustomersServerParams['report_date'] = '[name="report_date"]';
       /*CustomersServerParams['division'] = '[name="division"]';
       CustomersServerParams['responsible_admin'] = '[name="responsible_admin"]';
       CustomersServerParams['status'] = '[name="status"]';*/
       
       var tAPI = initDataTable('.table-clients', admin_url+'timesheets/travel_report_table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
       $('select[name="staff"]').on('change',function(){
           tAPI.ajax.reload();
       });
       $('input[name="report_date"]').on('change',function(){
           tAPI.ajax.reload();
       });
       /*$('select[name="division"]').on('change',function(){
           tAPI.ajax.reload();
       });
       $('select[name="responsible_admin"]').on('change',function(){
           tAPI.ajax.reload();
       });
       $('select[name="status"]').on('change',function(){
           tAPI.ajax.reload();
       });*/
       
   });
   function customers_bulk_action(event) {
       var r = confirm(app.lang.confirm_action_prompt);
       if (r == false) {
           return false;
       } else {
           var mass_delete = $('#mass_delete').prop('checked');
           var ids = [];
           var data = {};
           if(mass_delete == false || typeof(mass_delete) == 'undefined'){
               data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
               if (data.groups.length == 0) {
                   data.groups = 'remove_all';
               }
           } else {
               data.mass_delete = true;
           }
           var rows = $('.table-clients').find('tbody tr');
           $.each(rows, function() {
               var checkbox = $($(this).find('td').eq(0)).find('input');
               if (checkbox.prop('checked') == true) {
                   ids.push(checkbox.val());
               }
           });
           data.ids = ids;
           $(event).addClass('disabled');
           setTimeout(function(){
             $.post(admin_url + 'clients/bulk_action', data).done(function() {
              window.location.reload();
          });
         },50);
       }
   }
</script>
</body>
</html>
