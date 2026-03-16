<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
        
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form')) ;?>
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                 
                  <h4 class="no-margin">Add New Receipt Voucher</h4>
                  <hr class="hr-panel-heading" />
           
                     <div class="row filter_by">

                           <?php  $pro = $this->account_model->get_staff();
//print_r($pro);
                    
                    $actcode = array("PNBCC","PNBCF");
                    $actname = array("PNB CC ACOUNT / CBU","PNB COVID19FUND ACCOUNT");
                    $actmode = array("DR","CR");

                           ?>

                            <div  class="col-md-2 leads-filter-column pull-left">

                            <?php echo render_input('voucher_id','Voucher ID','2','number');
                     
                     ?> 
                   </div>

                          <div  class="col-md-2 leads-filter-column pull-left">
                            <?php
                             $today = date("Y-m-d");  
                             
                            $age = array("autocomplete"=>"on");
    ?>
                          <?php echo render_date_input('date','Voucher Date', $today , $age);
                          ?>
                        </div>
                        <?php 
                        $Payment_mode = array("CASH IN HAND","CASH PARK ROAD OFFICE","CASH DIFFERENCE","OBC BANK / SHARE APPLICATION","PARK ROAD / CASH");
                    $Payment_code = array("CASH","CPRO","CD","OBSA","PRC");
                        ?>

                        <div  class="col-md-3 leads-filter-column pull-left form-group">

                              <label>Payment Code</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Payment Code">
                                    <?php foreach($Payment_code as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>  
                        <div  class="col-md-3 leads-filter-column pull-left form-group">

                              <label>Payment Mode</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Payment Mode">
                                    <?php foreach($Payment_mode as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 

                            

                        

                        <div class="clearfix"></div>
                             
                            <div  class="col-md-2 leads-filter-column pull-left form-group">

                              <label>Account Code</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Account Code">
                                    <?php foreach($actcode as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>

                            <div  class="col-md-3 leads-filter-column pull-left form-group">

                              <label>Account Name</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Account Name">
                                    <?php foreach($actname as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>

                            <div  class="col-md-2 leads-filter-column pull-left form-group">

                              <label>DR/CR</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Select Mode">
                                    <?php foreach($actmode as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 

                             <div  class="col-md-3 leads-filter-column pull-left">

                            <?php echo render_input('amount','Amount','','number');
                    
                     ?> 
                   </div>
                   
                   <div  class="col-md-4 leads-filter-column pull-left">

                 
                  <?php echo render_textarea('note','Narration','',array('rows'=>2),array()); ?>

                    </div>
                    <div  class="col-md-4 leads-filter-column pull-left">
                    <button type="submit" class="btn btn-info" style="margin-top: 7%;"><?php echo _l('submit'); ?></button>
                  </div>
                            
                        </div>

               </div>
            </div>
         </div>


         
         <?php echo form_close(); ?>
      </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                       
                        <h4 class="no-margin">Manage Receipt Voucher</h4>
                        <hr class="hr-panel-heading">
                        <?php
                        $voucher_ids = array("1","2","3","4","5","6","7","8")
                        ?>
                        <div class="row">
                            <div class="col-md-3 pull-left">
                               
                                 <select name="staff_role" id="staff_role" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Filter By Voucher ID">
                                    <?php foreach($voucher_ids as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>
                             <div class="col-md-3 pull-left">
                               
                                <div class="form-group" app-field-wrapper="validity_end_date">
                                    <div class="input-group date">
                                        <input type="text" id="validity_end_date" name="validity_end_date" class="form-control datepicker" value="" autocomplete="off" placeholder="Filter By Date">
                                            <div class="input-group-addon">
                                            <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="clearfix"></div>
                        
                        <div class="clearfix"></div>
                        <?php
                        $table_data = array(
                            _l('staff_id'),
                            _l('staff_dt_name'),
                            "VoucherID",
                            "ActCode",
                            "ActName",
                            "Amount",
                            "Payment Code",
                            "PaymentMode",
                            "Narration",
                            "Action",                            
                            );
                        $custom_fields = get_custom_fields('staff',array('show_on_table'=>1));
                        foreach($custom_fields as $field){
                            array_push($table_data,$field['name']);
                        }
                        render_datatable($table_data,'table_contra');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete_staff" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('hrm/delete_staff',array('delete_staff_form'))); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('delete_staff'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="delete_id">
                    <?php echo form_hidden('id'); ?>
                </div>
                <p><?php echo _l('delete_staff_info'); ?></p>
                <?php
                echo render_select('transfer_data_to',$staff_members,array('staffid',array('firstname','lastname')),'staff_member',get_staff_user_id(),array(),array(),'','',false);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-danger _delete"><?php echo _l('confirm'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
$(function(){
//combotree
    var tree_dep = $('#hrm_derpartment_tree').comboTree({
              source : <?php echo ''.$dep_tree; ?>
            });
    
//combotree end 
     var StaffServerParams = {
        "status_work": "[name='status_work[]']",
        "hrm_deparment": "input[name='hrm_deparment']",
        "staff_role": "[name='staff_role[]']",
    };
    table_staff = $('table.table-table_contra');
    initDataTable(table_staff,admin_url + 'account/table_receipts', '','', StaffServerParams);
    
    $.each(StaffServerParams, function() {
            $('#status_work').on('change', function() {
                table_staff.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });
    //combotree department
     $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_staff.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
    //staff role
    $.each(StaffServerParams, function() {
            $('#staff_role').on('change', function() {
                table_staff.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });
})
</script>
</body>
</html>
