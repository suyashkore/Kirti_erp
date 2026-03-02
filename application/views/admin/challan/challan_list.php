<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            
            <div class="panel_s">
               <div class="panel-body">
                  <nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Challan List</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
                    <div class="row">
                       <div class="col-md-2">
                            <?php //$cur_date = date('d/m/Y'); 
                            //$first_date = "01/".date('m')."/".date('Y');
                            ?>
                        <?php
                            $fy = $this->session->userdata('finacial_year');
                            $fy_new  = $fy + 1;
                            $lastdate_date = '20'.$fy_new.'-03-31';
                            $firstdate_date = '20'.$fy_new.'-04-01';
                            $curr_date = date('Y-m-d');
                            $curr_date_new    = new DateTime($curr_date);
                            $last_date_yr = new DateTime($lastdate_date);
                            if($last_date_yr < $curr_date_new){
                                $to_date = '31/03/20'.$fy_new;
                                $from_date = '01/03/20'.$fy_new;
                            }else{
                                $from_date = "01/".date('m')."/".date('Y');
                                $to_date = date('d/m/Y');
                            }
                        ?>
                          <?php echo render_date_input('from_date','from_date',$from_date); ?>
                        </div>
                        <div class="col-md-2">
                          <?php echo render_date_input('to_date','to_date',$to_date); ?>
                        </div>
                        
                        <div class="col-md-2">
                            <br>
                          <button type="button" class="btn-tr btn btn-info search">Search</button>
                        </div>
                   </div>
                  
                  <div class="clearfix mtop20"></div>
                  <?php
                  $table_data = array();
                     $_table_data = array(
                      /*'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',*/
                       array(
                         'name'=>"Challan No.",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                        array(
                         'name'=>"E-Way Bill",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                        array(
                         'name'=>"E-Way Bill Date",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                         array(
                         'name'=>"Party Name",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                        array(
                         'name'=>"Station",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-active')
                        ),
                        array(
                         'name'=>"State",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-station')
                        ),
                        /*array(
                         'name'=>"City",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-station')
                        ),*/
                         array(
                         'name'=>"Vehicle",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-groups')
                        ),
                         
                         /*array(
                         'name'=>"Route",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-state')
                        ),*/
                        array(
                         'name'=>"Total Qty",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-town')
                        ),
                        // array(
                         // 'name'=>"Crate",
                         // 'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-sales_person')
                        // ),
                        array(
                         'name'=>"Bill Amt",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-sales_person')
                        ),
                        array(
                         'name'=>"Created At",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-sales_person')
                        ),
                        array(
                         'name'=>"Updated At",
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
                     
                     /*array_push($table_data,array(
                         'name'=>_l('date_created'),
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-date-created')
                        ));*/

                     $table_data = hooks()->apply_filters('customers_table_columns', $table_data);

                     render_datatable($table_data,'challan',[],[
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
       
       
       CustomersServerParams['from_date'] = '[name="from_date"]';
	   CustomersServerParams['to_date'] = '[name="to_date"]';
	    
       var tAPI = initDataTable('.table-challan', admin_url+'challan/table', [], [], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'desc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
       
       /*$('input[name="from_date"]').on('change',function(){
           tAPI.ajax.reload();
       });*/
       $('.search').on('click',function(){
           tAPI.ajax.reload();
       });
       
       /*$('input[name="to_date"]').on('change',function(){
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
             $.post(admin_url + 'enquiry/bulk_action', data).done(function() {
              window.location.reload();
          });
         },50);
       }
   }
</script>

 <script>
    $(document).ready(function(){
    var maxEndDate = new Date('Y/m/d');
    var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
    
    var year = "20"+fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);
    if(cur_y => fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        
        var e_dat = new Date(year2_new+'/03/31');
        
        var maxEndDate_new = e_dat;
    }else{
        var e_dat2 = new Date(year2+'/03/31');
        var maxEndDate_new = e_dat2;
    }
    
    var minStartDate = new Date(year, 03);
   
    
    $('#from_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    $('#to_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false,
        showOtherMonths: false,
        pickTime: false,
            orientation: "left",
    });
    
    });
</script> 

</body>
</html>
