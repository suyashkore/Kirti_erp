<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="_filters _hidden_inputs hidden">
                        <?php
                       
                        echo form_hidden('draft');
                        echo form_hidden('valid');
                        echo form_hidden('invalid');
                        
                        foreach($staff as $s) { 
                            echo form_hidden('contracts_by_staff_'.$s['staffid']);
                        }
                        foreach($contract_type as $type){
                            echo form_hidden('contracts_by_type_'.$type['id_contracttype']);
                        }
                        foreach($duration as $d){
                            echo form_hidden('contracts_by_duration_'.$d['duration'].'_'.$d['unit']);
                        }
                    ?>
                </div>
                    <div class="panel-body">
                        
                        <h4 class="no-margin">Account Ledger</h4>
                  <hr class="hr-panel-heading" />
                       
                        <div class="clearfix"></div>
                        <br>
                      
                        <div class="row">
  
                           
                            <div  class="col-md-3 leads-filter-column pull-left">
                                <div class="form-group" app-field-wrapper="validity_start_date">
                                    <div class="input-group date">
                                        <input type="text" id="validity_start_date" name="validity_start_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('validity_start_date') ?>">
                                            <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                    </div>
                                </div>
                            </div> 

                            <div  class="col-md-3 leads-filter-column pull-left">
                                <div class="form-group" app-field-wrapper="validity_end_date">
                                    <div class="input-group date">
                                        <input type="text" id="validity_end_date" name="validity_end_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('validity_end_date') ?>">
                                            <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                    </div>
                                </div>
                            </div> 
                            
                            
                        </div>
                        <br>
                        <div class="row">
                            <?php // $pro = $this->account_model->get_staff();
                           $type = array("CASH","APX2","AGS","AS","ASP","AAS","ANILX2","ABE","ANILB","ABD","ANG","ANS")
                           ?>
                            <div  class="col-md-2 leads-filter-column pull-left">
                          
                                  <select name="staff[]" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Account ID">
                                    <?php foreach($type as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 

                            <div  class="col-md-2 leads-filter-column pull-left">
                          
                                  <input type="text" name="account_name" id="account_name" readonly="" class="form-control" placeholder="Account Name">
                            </div> 

                            <div  class="col-md-2 leads-filter-column pull-left">
                          
                                  <input type="text" name="address1" id="address1" readonly="" class="form-control" placeholder="Address1">
                            </div> 

                            <div  class="col-md-2 leads-filter-column pull-left">
                          
                                  <input type="text" name="address2" id="address2" readonly="" class="form-control" placeholder="Address2">
                            </div> 

                            <div  class="col-md-2 leads-filter-column pull-left">
                          
                                  <input type="text" name="address3" id="address3" readonly="" class="form-control" placeholder="Address3">
                            </div> 

                            <div  class="col-md-2 leads-filter-column pull-left">
                          
                                  <input type="text" name="address4" id="address4" readonly="" class="form-control" placeholder="Address4">
                            </div> 
                            
                        </div>
                        
                        <br>

                        <div class="row">
                           <div class="col-md-12" id="small-table">
                              <div class="panel_s">
                                 <div class="panel-body">
                                    <div class="clearfix"></div>
                                     <?php echo form_hidden('hrmcontractid',$hrmcontractid); ?>
                                      <!-- if hrmcontract id found in url -->
                                    <?php
                                    $table_data = array(
                                        _l('id'),
                                        "VoucherType",
                                        "VoucherID",
                                        "TranDate",
                                        "Narration",
                                        "Debit",
                                        "Credit",
                                        "Balance",
                                        "DRCR",
                                        "Action",
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
                           <div class="col-md-7 small-table-right-col">
                              <div id="hrm_contract" class="hide">
                              </div>
                           </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>

    var tree_dep = $('#hrm_derpartment_tree').comboTree({
              source : <?php echo ''.$dep_tree; ?>
            });

     var ContractsServerParams = {
        "hrm_deparment": "input[name='hrm_deparment']",
        "hrm_staff"    : "select[name='staff[]']",
        "validity_start_date": "input[name='validity_start_date']",
        "validity_end_date": "input[name='validity_end_date']",
     };
        $.each($('._hidden_inputs._filters input'),function(){
            ContractsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });

    table_contract = $('table.table-table_contract');
    initDataTable(table_contract, admin_url+'account/table_account_ledger', undefined, undefined, ContractsServerParams,<?php echo hooks()->apply_filters('contracts_table_default_order', json_encode(array(1,'asc'))); ?>);

    //combotree department
     $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_contract.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
     $('#staff').on('change', function() {
                table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();

                var selectedval = $(this).children("option:selected").val(); 
                if (selectedval !== "") {
                    $('#account_name').val("CASH IN HAND"); 
                    $('#address1').val("Gorakhpur");
                    $('#address2').val("Gorakhpur");
                    $('#address3').val("Uttar Pradesh");
                    $('#address4').val("India");
                } 


                   
                
            });
    $('#validity_start_date').on('change', function() {
                    table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
                    });
    $('#validity_end_date').on('change', function() {
                    table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
                });
    init_hrm_contract();
    //init table contract view
    function init_hrm_contract(id) {
    load_small_table_item(id, '#hrm_contract', 'hrmcontractid', 'account/get_hrm_contract_data_ajax', '.table-table_contract');
    }


    
</script>
</body>
</html>
