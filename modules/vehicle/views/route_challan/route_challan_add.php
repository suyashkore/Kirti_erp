<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <?php
            if(isset($expense)){
             echo form_hidden('is_edit','true');
            }
            ?>
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form','class'=>'dropzone dropzone-manual')) ;?>
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  
                  <h4 class="no-margin"><?php echo $title; ?></h4>
                  <hr class="hr-panel-heading" />
                  
                 

                     <div class="row filter_by">

                           <?php  $pro = $this->vehicle_model->get_staff();
//print_r($pro);
                           ?>

                          <div  class="col-md-3 leads-filter-column pull-left">
                          <?php 
                          $attr = array(
                            'placeholder' => 'CHL20300903'
                          )
                          ?>

                            <!-- <?php echo render_input('Vehicle_RegNo','','','text',$attr);
                           
                           ?>  -->
                           <div class="form-group">
                            <div class="input-group date">
                           <input type="text" name="Vehicle_RegNo" id="Vehicle_RegNo" placeholder="CHL20300903" class="form-control">
                           <div class="input-group-addon">
                            <i class="fa fa-search search-icon"></i>
                        </div>
                        </div>
                        </div>
                          </div>
                           <div  class="col-md-2 leads-filter-column pull-left">

                            <?php 
                            $today = date("Y-m-d");  
                          $attr = array(
                            'placeholder' => 'Challan Date'
                          )
                          ?>
                            <?php echo render_date_input('date','', $today ,$attr);
                            ?>
                          </div>
                          <?php
                        $Route = array("GHAZIABAD","GORAKHNATH","GULAHRIYA","KASYA","KHADDA","LACHHIPUR","MAHRAJGANJ","MAHUADIH")
                        ?>
                          
                          <div  class="col-md-3 leads-filter-column pull-left form-group">
                             <!-- <label>Route</label> -->
                              <select name="staff_role[]" id="staff_role" data-live-search="true" multiple="false" class="selectpicker"  data-actions-box="false" data-width="100%" data-none-selected-text="Select Route ">
                                    <?php foreach($Route as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>


                          <?php
                        $vehicle = array("Select Vehicle","UP53BT6925","UP53BT7412","UP53BT7413","UP53BT7689","UP53BT8305","UP53BT9991","UP53CT6144","UP53CT8785");
                        ?>
                          
                          <div  class="col-md-2 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <select name="select_vehicle" id="select_vehicle" data-live-search="true" class="selectpicker" data-actions-box="false" data-width="100%" data-none-selected-text="Select Vehicle ">
                                    <?php foreach($vehicle as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>
                            <div  class="col-md-2 leads-filter-column pull-left">
                               <?php 
                          $attr = array(
                            'placeholder' => 'Vehicle Capacity'
                          )
                          ?>

                            <?php echo render_input('Vehicle_Capacity','','','text', $attr);
                           
                           ?> 
                          </div>

                          
                          <?php
                          $driver_id = array("Driver id","BPS","SHA","ADX2")
                         
                          ?>
                          
                           <div  class="col-md-2 leads-filter-column pull-left form-group">

                              <!-- <label>Driver ID</label> -->
                                  <select name="driver_id" id="driver_id" data-live-search="true" class="selectpicker" data-actions-box="false" data-width="100%" data-none-selected-text="Driver id">
                                    <?php foreach($driver_id as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>
                            <div  class="col-md-4 leads-filter-column pull-left">

                           <?php 
                          $attr = array(
                            'placeholder' => 'Driver Name'
                          )
                          ?>

                            <?php echo render_input('driver_name','','','text', $attr);
                           
                           ?> 
                          </div>

                          <?php
                          $loader_id = array("Loader Id ","JMC","SDS","BAKM")
                         
                          ?>

                          <div  class="col-md-2 leads-filter-column pull-left form-group">

                              <!-- <label>Loader ID</label> -->
                                  <select name="loader_id" id="loader_id" data-live-search="true" class="selectpicker" data-actions-box="false" data-width="100%" data-none-selected-text="Loader Id">
                                    <?php foreach($loader_id as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>
                            <div  class="col-md-4 leads-filter-column pull-left">

                          <?php 
                          $attr = array(
                            'placeholder' => 'Loader Name'
                          )
                          ?>
                            <?php echo render_input('loader_name','','','text', $attr);
                           
                           ?> 
                          </div>

                          <div  class="col-md-2 leads-filter-column pull-left form-group">

                             <!--  <label>Sales Man ID</label> -->
                                  <select name="staff4" id="staff4" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Salesman Id">
                                    <?php foreach($vehicle_type as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>
                            <div  class="col-md-4 leads-filter-column pull-left">

                            <?php 
                          $attr = array(
                            'placeholder' => 'Sales Man Name'
                          )
                          ?>

                            <?php echo render_input('Vehicle_Capacity','','','text', $attr);
                           
                           ?> 
                          </div>

                          
                        </div>
                        <hr class="hr-panel-heading" />
                        <h4 class="no-margin"><?php echo _l('advanced_options'); ?></h4>
                  <hr class="hr-panel-heading" />

                  <div class="row">
                     <div class="col-md-4">
                      <!-- <label>Challan Value</label> -->
                      <?php 
                          $attr = array(
                            'placeholder' => 'Challan Value'
                          )
                          ?>
                        <?php echo render_input('challan_value','','','Number',$attr);
                           
                           ?> 
                     </div>
                     <div class="col-md-4">
                      <?php 
                          $attr = array(
                            'placeholder' => 'Total Cases'
                          )
                          ?>
                        <?php echo render_input('cases','','','Number',$attr);
                           
                           ?> 
                     </div>

                     <div class="col-md-4">
                      <?php 
                          $attr = array(
                            'placeholder' => 'Total Crates'
                          )
                          ?>
                        <?php echo render_input('crates','','','text',$attr);
                           
                           ?> 
                     </div>

                     <div id="txtAge" style="display: none;">Age is something</div>
                     
                  </div>


                  <?php $rel_id = (isset($expense) ? $expense->expenseid : false); ?>
                  <?php echo render_custom_fields('expenses',$rel_id); ?>
                  <div class="btn-bottom-toolbar text-right">
                     <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                  </div> 
               </div>
            </div>
         </div>
         

         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">


                <?php
                                    $table_data = array(
                                        "Tag",
                                        "OrderNo",
                                        "AccountName",
                                        "StateID",
                                        "Ordertype",
                                        "Products",
                                        "Crates",
                                        "Cases",
                                        "OrderAmt",
                                        "SaleAmt",
                                        "TCSPer",
                                        "TCSAmt", 
                                                          
                                        );
                                    $custom_fields = get_custom_fields('staff',array('show_on_table'=>1));
                                    foreach($custom_fields as $field){
                                        array_push($table_data,$field['name']);
                                    }
                                    render_datatable($table_data,'table_contract');
                                    ?>

               </div>
            </div>
          </div>
         <?php echo form_close(); ?>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
</div>
<?php $this->load->view('admin/expenses/expense_category'); ?>
<?php init_tail(); ?>
<script>

  

  

    var tree_dep = $('#hrm_derpartment_tree').comboTree({
              source : <?php echo ''.$dep_tree; ?>
            });

    /* var ContractsServerParams = {
        "hrm_deparment": "input[name='hrm_deparment']",
        "hrm_staff"    : "select[name='staff[]']",
        "validity_start_date": "input[name='validity_start_date']",
        "validity_end_date": "input[name='validity_end_date']",
     };
        $.each($('._hidden_inputs._filters input'),function(){
            ContractsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });*/

    //combotree end
     var StaffServerParams = {
        "status_work": "[name='status_work[]']",
        "hrm_deparment": "input[name='hrm_deparment']",
        "staff_role": "[name='staff_role[]']",
    };

    table_contract = $('table.table-table_contract');
    /*initDataTable(table_contract, admin_url+'vehicle/table_challan', undefined, undefined, ContractsServerParams,<?php echo hooks()->apply_filters('contracts_table_default_order', json_encode(array(1,'asc'))); ?>);*/

    initDataTable(table_contract,admin_url + 'vehicle/table_challan', '','', StaffServerParams);

    //staff role
    $.each(StaffServerParams, function() {
            $('#staff_role').on('change', function() {
                table_contract.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });

    
     $('#driver_id').on('change', function() {
                
                var selectedval = $(this).children("option:selected").val(); 
                if (selectedval == "BPS") {
                    $('#driver_name').val("BIPIN KUMAR SHUKLA - DRIVER"); 
                    
                } 

                if (selectedval == "SHA") {
                    $('#driver_name').val("SHAILENDRA YADAV / DRIVER"); 
                    
                } 

                if (selectedval == "ADX2") {
                    $('#driver_name').val("ADITYA"); 
                    
                } 
            });

     $('#loader_id').on('change', function() {
                
                var selectedval = $(this).children("option:selected").val(); 
                if (selectedval == "JMC") {
                    $('#loader_name').val("JOSHI MASALA CO."); 
                    
                } 

                if (selectedval == "SDS") {
                    $('#loader_name').val("SUKHDEV SINGH / SALES"); 
                    
                } 

                if (selectedval == "BAKM") {
                    $('#loader_name').val("BAKEWELL MACHINES"); 
                    
                } 
            });

    /*//combotree department
     $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_contract.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
     $('#staff').on('change', function() {
                table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
            });
    $('#validity_start_date').on('change', function() {
                    table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
                    });
    $('#validity_end_date').on('change', function() {
                    table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
                });*/
    init_hrm_contract();
    //init table contract view
    function init_hrm_contract(id) {
    load_small_table_item(id, '#hrm_contract', 'hrmcontractid', 'vehicle/get_hrm_contract_data_ajax', '.table-table_contract');
    }


    
</script>
<script type="text/javascript">
    $(document).ready(function () {
    var the_checkbox = $('#chkBoxHelp');
    the_checkbox.click(function () {
        if ($(this).is(':checked')) {
            $("#txtAge").dialog({
                close: function () {
                    the_checkbox.prop('checked', false);
                }
            });
        } else {
            $("#txtAge").dialog('close');
        }
    });
});
</script>
<script>
   var customer_currency = '';
   Dropzone.options.expenseForm = false;
   var expenseDropzone;
   init_ajax_project_search_by_customer_id();
   var selectCurrency = $('select[name="currency"]');
   <?php if(isset($customer_currency)){ ?>
     var customer_currency = '<?php echo $customer_currency; ?>';
   <?php } ?>
     $(function(){
        $('body').on('change','#project_id', function(){
          var project_id = $(this).val();
          if(project_id != '') {
           if (customer_currency != 0) {
             selectCurrency.val(customer_currency);
             selectCurrency.selectpicker('refresh');
           } else {
             set_base_currency();
           }
         } else {
          do_billable_checkbox();
        }
      });

     if($('#dropzoneDragArea').length > 0){
        expenseDropzone = new Dropzone("#expense-form", appCreateDropzoneOptions({
          autoProcessQueue: false,
          clickable: '#dropzoneDragArea',
          previewsContainer: '.dropzone-previews',
          addRemoveLinks: true,
          maxFiles: 1,
          success:function(file,response){
           response = JSON.parse(response);
           if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
             window.location.assign(response.url);
           }
         },
       }));
     }

     appValidateForm($('#expense-form'),{
      category:'required',
      date:'required',
      amount:'required',
      currency:'required',
      repeat_every_custom: { min: 1},
    },expenseSubmitHandler);

     $('input[name="billable"]').on('change',function(){
       do_billable_checkbox();
     });

      $('#repeat_every').on('change',function(){
         if($(this).selectpicker('val') != '' && $('input[name="billable"]').prop('checked') == true){
            $('.billable_recurring_options').removeClass('hide');
          } else {
            $('.billable_recurring_options').addClass('hide');
          }
     });

     // hide invoice recurring options on page load
     $('#repeat_every').trigger('change');

      $('select[name="clientid"]').on('change',function(){
       customer_init();
       do_billable_checkbox();
       $('input[name="billable"]').trigger('change');
     });

     <?php if(!isset($expense)) { ?>
        $('select[name="tax"], select[name="tax2"]').on('change', function () {

            delay(function(){
                var $amount = $('#amount'),
                taxDropdown1 = $('select[name="tax"]'),
                taxDropdown2 = $('select[name="tax2"]'),
                taxPercent1 = parseFloat(taxDropdown1.find('option[value="'+taxDropdown1.val()+'"]').attr('data-percent')),
                taxPercent2 = parseFloat(taxDropdown2.find('option[value="'+taxDropdown2.val()+'"]').attr('data-percent')),
                total = $amount.val();

                if(total == 0 || total == '') {
                    return;
                }

                if($amount.attr('data-original-amount')) {
                  total = $amount.attr('data-original-amount');
                }

                total = parseFloat(total);

                if(taxDropdown1.val() || taxDropdown2.val()) {

                    $('#tax_subtract').removeClass('hide');

                    var totalTaxPercentExclude = taxPercent1;
                    if(taxDropdown2.val()){
                      totalTaxPercentExclude += taxPercent2;
                    }

                    var totalExclude = accounting.toFixed(total - exclude_tax_from_amount(totalTaxPercentExclude, total), app.options.decimal_places);
                    $('#tax_subtract_total').html(accounting.toFixed(totalExclude, app.options.decimal_places));
                } else {
                   $('#tax_subtract').addClass('hide');
                }
                if($('#tax1_included').prop('checked') == true) {
                    subtract_tax_amount_from_expense_total();
                }
              }, 200);
        });

        $('#amount').on('blur', function(){
          $(this).removeAttr('data-original-amount');
          if($(this).val() == '' || $(this).val() == '') {
              $('#tax1_included').prop('checked', false);
              $('#tax_subtract').addClass('hide');
          } else {
            var tax1 = $('select[name="tax"]').val();
            var tax2 = $('select[name="tax2"]').val();
            if(tax1 || tax2) {
                setTimeout(function(){
                    $('select[name="tax2"]').trigger('change');
                }, 100);
            }
          }
        })

        $('#tax1_included').on('change', function() {

          var $amount = $('#amount'),
          total = parseFloat($amount.val());

          // da pokazuva total za 2 taxes  Subtract TAX total (136.36) from expense amount
          if(total == 0) {
              return;
          }

          if($(this).prop('checked') == false) {
              $amount.val($amount.attr('data-original-amount'));
              return;
          }

          subtract_tax_amount_from_expense_total();
        });
      <?php } ?>
    });

    function subtract_tax_amount_from_expense_total(){
         var $amount = $('#amount'),
         total = parseFloat($amount.val()),
         taxDropdown1 = $('select[name="tax"]'),
         taxDropdown2 = $('select[name="tax2"]'),
         taxRate1 = parseFloat(taxDropdown1.find('option[value="'+taxDropdown1.val()+'"]').attr('data-percent')),
         taxRate2 = parseFloat(taxDropdown2.find('option[value="'+taxDropdown2.val()+'"]').attr('data-percent'));

         var totalTaxPercentExclude = taxRate1;
         if(taxRate2) {
          totalTaxPercentExclude+= taxRate2;
        }

        if($amount.attr('data-original-amount')) {
          total = parseFloat($amount.attr('data-original-amount'));
        }

        $amount.val(exclude_tax_from_amount(totalTaxPercentExclude, total));

        if($amount.attr('data-original-amount') == undefined) {
          $amount.attr('data-original-amount', total);
        }
    }

    function customer_init(){
        var customer_id = $('select[name="clientid"]').val();
        var projectAjax = $('select[name="project_id"]');
        var clonedProjectsAjaxSearchSelect = projectAjax.html('').clone();
        var projectsWrapper = $('.projects-wrapper');
        projectAjax.selectpicker('destroy').remove();
        projectAjax = clonedProjectsAjaxSearchSelect;
        $('#project_ajax_search_wrapper').append(clonedProjectsAjaxSearchSelect);
        init_ajax_project_search_by_customer_id();
        if(!customer_id){
           set_base_currency();
           projectsWrapper.addClass('hide');
         }
       $.get(admin_url + 'expenses/get_customer_change_data/'+customer_id,function(response){
         if(customer_id && response.customer_has_projects){
           projectsWrapper.removeClass('hide');
         } else {
           projectsWrapper.addClass('hide');
         }
         var client_currency = parseInt(response.client_currency);
         if (client_currency != 0) {
           customer_currency = client_currency;
           do_billable_checkbox();
         } else {
           customer_currency = '';
           set_base_currency();
         }
       },'json');
     }
     function expenseSubmitHandler(form){

      selectCurrency.prop('disabled',false);

      $('select[name="tax2"]').prop('disabled',false);
      $('input[name="billable"]').prop('disabled',false);
      $('input[name="date"]').prop('disabled',false);

      $.post(form.action, $(form).serialize()).done(function(response) {
        response = JSON.parse(response);
        if (response.expenseid) {
         if(typeof(expenseDropzone) !== 'undefined'){
          if (expenseDropzone.getQueuedFiles().length > 0) {
            expenseDropzone.options.url = admin_url + 'expenses/add_expense_attachment/' + response.expenseid;
            expenseDropzone.processQueue();
          } else {
            window.location.assign(response.url);
          }
        } else {
          window.location.assign(response.url);
        }
      } else {
        window.location.assign(response.url);
      }
    });
      return false;
    }
    function do_billable_checkbox(){
      var val = $('select[name="clientid"]').val();
      if(val != ''){
        $('.billable').removeClass('hide');
        if ($('input[name="billable"]').prop('checked') == true) {
          if($('#repeat_every').selectpicker('val') != ''){
            $('.billable_recurring_options').removeClass('hide');
          } else {
            $('.billable_recurring_options').addClass('hide');
          }
          if(customer_currency != ''){
            selectCurrency.val(customer_currency);
            selectCurrency.selectpicker('refresh');
          } else {
            set_base_currency();
         }
       } else {
        $('.billable_recurring_options').addClass('hide');
        // When project is selected, the project currency will be used, either customer currency or base currency
        if($('#project_id').selectpicker('val') == ''){
            set_base_currency();
        }
      }
    } else {
      set_base_currency();
      $('.billable').addClass('hide');
      $('.billable_recurring_options').addClass('hide');
    }
   }
   function set_base_currency(){
    selectCurrency.val(selectCurrency.data('base'));
    selectCurrency.selectpicker('refresh');
   }
</script>
</body>
</html>
