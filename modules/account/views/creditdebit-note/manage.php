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
                        <!-- <?php if(has_permission('hrm','','create')){ { ?>
                        <div class="_buttons">
                            <a href="<?php echo admin_url('hrm/contract'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_contract'); ?></a>
                        </div>
                        <?php } ?> -->
                        
                        <h4 class="no-margin">Credit/Debit Note Report</h4>
                  <hr class="hr-panel-heading" />
                       


                        <div class="clearfix"></div>
                        <br>
                        <?php } ?>

                        <div class="row">
  
                           <?php // $pro = $this->account_model->get_staff();
                           $notetype = array("Credit Note","Debit Note");
                           $accounttype = array("GST Account","Non-GST Account");
                           ?>
                           <div  class="col-md-3 leads-filter-column pull-left">
                          
                                  <select name="notetype" id="notetype" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Note Type">
                                    <?php foreach($notetype as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 

                            <div  class="col-md-3 leads-filter-column pull-left">
                          
                                  <select name="accounttype" id="accounttype" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Account Type">
                                    <?php foreach($accounttype as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 

                            <div  class="col-md-2 leads-filter-column pull-right">
                                <div class="form-group" app-field-wrapper="validity_end_date">
                                    <div class="input-group date">
                                        <input type="text" id="validity_end_date" name="validity_end_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('validity_end_date') ?>">
                                            <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                    </div>
                                </div>
                            </div> 
                            <div  class="col-md-2 leads-filter-column pull-right">
                                <div class="form-group" app-field-wrapper="validity_start_date">
                                    <div class="input-group date">
                                        <input type="text" id="validity_start_date" name="validity_start_date" class="form-control datepicker" value="" autocomplete="off" placeholder="<?php echo _l('validity_start_date') ?>">
                                            <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                    </div>
                                </div>
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
                                        "Sl.No.",
                                        "CD Note",
                                        "CD Date",
                                        "Cr/Dr",
                                        "Account Name ",
                                        "GSTIN",
                                        "HSN/SAC",
                                        "Rate",
                                        "Qty.",
                                        "CGST %",
                                        "CGST Amt",
                                        "SGST % ",
                                        "SGST Amt",
                                        "IGST %",
                                        "IGST Amt",
                                        "Cr/Dr Amt",

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
    initDataTable(table_contract, admin_url+'hrm/table_contract', undefined, undefined, ContractsServerParams,<?php echo hooks()->apply_filters('contracts_table_default_order', json_encode(array(1,'asc'))); ?>);

    //combotree department
     $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_contract.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
     $('#notetype').on('change', function() {
                table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
            });
     $('#accounttype').on('change', function() {
                table_contract.DataTable().ajax.reload().columns.adjust().responsive.recalc();
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
