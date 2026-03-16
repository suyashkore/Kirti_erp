<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style type="text/css">
  .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover {
    background: #02a9f4 !important;
    color: #fff !important;
    border-radius: 3px !important;
  }
</style>
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
                          
                            <div class="form-group">
                              <div class="input-group date">
                                <input type="text" name="Vehicle_return" id="Vehicle_return" placeholder="VRT21300001" class="form-control">
                                <div class="input-group-addon">
                                  <i class="fa fa-search search-icon"></i>
                                </div>
                              </div>
                            </div>
                          </div>
                           <div  class="col-md-3 leads-filter-column pull-left">

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
                        $challan_no = array("CHALLAN NO.","CHL20300907","CHL20300906","CHL20300905","CHL20300904","CHL20300903","CHL20300902","CHL20300901","CHL20300900");
                        ?>
                          
                          <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <select name="challan_no" id="challan_no" data-live-search="true" class="selectpicker" data-actions-box="false" data-width="100%" data-none-selected-text="Select Vehicle ">
                                    <?php foreach($challan_no as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>

                            <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="challan_date" id="challan_date" placeholder="Challan date">
                            </div>

                            <div class="clearfix"></div>

                            <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="route" id="route" placeholder="Route">
                            </div>

                            <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="vehicle_name" id="vehicle_name" placeholder="Vehicle">
                            </div>

                            <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="vehicle_cap" id="vehicle_cap" placeholder="Vehicle capacity">
                            </div>

                          <div class="clearfix"></div>

                            <div  class="col-md-2 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="driver_id" id="driver_id" placeholder="Driver ID">
                            </div>

                            <div  class="col-md-4 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="driver_name" id="driver_name" placeholder="Driver Name">
                            </div>

                            <div  class="col-md-2 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="loader_id" id="loader_id" placeholder="Loader ID">
                            </div>

                            <div  class="col-md-4 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="loader_name" id="loader_name" placeholder="Loader Name">
                            </div>

                        <div class="clearfix"></div>

                            <div  class="col-md-2 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="sales_id" id="sales_id" placeholder="Sales ID">
                            </div>

                            <div  class="col-md-4 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="sales_name" id="sales_name" placeholder="SalesMan Name">
                            </div>

                            <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="challan_crates" id="challan_crates" placeholder="Challan Crates">
                            </div>

                            <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="challan_cases" id="challan_cases" placeholder="Challan Cases">
                            </div>

                           

                          
                        </div>
                        <hr class="hr-panel-heading" />
                        <h4 class="no-margin"><?php echo _l('advanced_options'); ?></h4>
                  <hr class="hr-panel-heading" />

                  <div class="row">
                     <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="return_crates" id="return_crates" placeholder="Return Crates">
                      </div>
                     <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="return_cases" id="return_cases" placeholder="Return Cases">
                      </div>

                      <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="return_amt" id="return_amt" placeholder="Return Amt">
                      </div>

                      <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="Cash_Deposite" id="Cash_Deposite" placeholder="Cash Deposite">
                      </div>

                      <div  class="col-md-3 leads-filter-column pull-left form-group">
                            <!-- <label>Vehicle</label> -->
                              <input type="text" class="form-control" readonly="true" name="Total_Expense" id="Total_Expense" placeholder="Total Expense">
                      </div>

                     
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

                <div class="horizontal-scrollable-tabs preview-tabs-top">
  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
  <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
  <div class="horizontal-tabs">
    <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
      <li role="presentation" class="active">
        <a href="#Crate_Details" aria-controls="Crate_Details" role="tab" data-toggle="tab">Crate Details</a>
      </li>
      <li role="presentation">
        <a href="#Stock_Return" aria-controls="Stock_Return" role="tab" data-toggle="tab">Fresh Stock Return</a>
      </li>
      <li role="presentation">
        <a href="#Payment_Receipts" aria-controls="Payment_Receipts" role="tab" data-toggle="tab">Payment Receipts</a>
      </li>
      <li role="presentation">
        <a href="#Expense_Details" aria-controls="Expense_Details" role="tab" data-toggle="tab">Expense Details</a>
      </li>
    </ul>
  </div>
</div>
<br>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="Crate_Details">
    <table class="table dt-table">
       <thead>
        <tr>
          <th>Sr.No.</th>
          <th>AccountID</th>
          <th>AccountName</th>
          <th>Address</th>
          <th>OpeningCrate</th>
          <th>ChallanCrate</th>
          <th>ReturnCrate</th>
          <th>BalanceCrate</th>
        </tr>
       </thead>
       <tbody>
        <?php foreach($holiday as $d) {?>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          <?php } ?>
       </tbody>
      </table>
  </div>
  <div role="tabpanel" class="tab-pane" id="Stock_Return">
    <table class="table dt-table">
       <thead>
        <tr>
          <th>AccountID</th>
          <th>AccountName</th>
          <th>SalesID</th>
          <th>ReceiptID</th>
          <th>CGST</th>
          <th>SGST</th>
          <th>IGST</th>
          <th>Item List</th>
          <th>RtnItemTotal</th>
          <th>RtnAmount</th>
          
        </tr>
       </thead>
       <tbody>
          <?php foreach($event_break as $d) {?>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          <?php } ?>
       </tbody>
      </table>
  </div>
  <div role="tabpanel" class="tab-pane" id="Payment_Receipts">
    <table class="table dt-table">
       <thead>
        <tr>
          <th>Sr.No.</th>
          <th>AccountID</th>
          <th>AccountName</th>
          <th>Address</th>
          <th>Recp.Amt</th>
          
        </tr>
       </thead>
       <tbody>
          <?php foreach($unexpected_break as $d) {?>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              
            </tr>
          <?php } ?>

       </tbody>
      </table>
  </div>

  <div role="tabpanel" class="tab-pane" id="Expense_Details">
    <table class="table dt-table">
       <thead>
        <tr>
          <th>Sr.No.</th>
          <th>AccountID</th>
          <th>AccountName</th>
          <th>Expense Amt</th>
          <th>Action</th>
          
        </tr>
       </thead>
       <tbody>
          <?php foreach($unexpected_break as $d) {?>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              
            </tr>
          <?php } ?>

       </tbody>
      </table>
  </div>


</div>
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

    $('#challan_no').on('change', function() {
                table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();

                var selectedval = $(this).children("option:selected").val(); 
                if (selectedval !== "CHALLAN NO" && selectedval !== "") {
                    $('#challan_date').val("26-may-2020"); 
                    $('#route').val("OUT STATION");
                    $('#vehicle_name').val("XXX");
                    $('#vehicle_cap').val("250");
                    $('#driver_id').val("BPS");
                    $('#driver_name').val("BIPIN KUMAR SHUKLA - DRIVER");
                    $('#loader_id').val("JMC");
                    $('#loader_name').val("JOSHI MASALA CO.");
                    $('#sales_id').val("JMC");
                    $('#sales_name').val("JOSHI MASALA CO.");
                    $('#challan_crates').val("240");
                    $('#challan_cases').val("0");
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
