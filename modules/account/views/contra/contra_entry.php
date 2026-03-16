  <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
        
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form')) ;?>
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                 
                  <h4 class="no-margin">Add New Contra Voucher</h4>
                  <hr class="hr-panel-heading" />
           
                     <div class="row filter_by">

                           <?php  $pro = $this->account_model->get_staff();
//print_r($pro);
                    
                    $actcode = array("PNBCC","PNBCF");
                    $actname = array("PNB CC ACOUNT / CBU","PNB COVID19FUND ACCOUNT");
                    $actmode = array("DR","CR");

                           ?>

                            <div  class="col-md-2 leads-filter-column pull-left">

                            <?php echo render_input('voucher_id','Voucher ID','1','number');
                     
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

                        <div  class="col-md-2 leads-filter-column pull-left form-group">

                              <label>Account Code</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Account Code">
                                    <?php foreach($actcode as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>

                            <div  class="col-md-4 leads-filter-column pull-left form-group">

                              <label>Account Name</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Account Name">
                                    <?php foreach($actname as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div>

                        <div class="clearfix"></div>
                             

                            <div  class="col-md-2 leads-filter-column pull-left form-group">

                              <label>DR/CR</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker" multiple="false" data-actions-box="false" data-width="100%" data-none-selected-text="Select Mode">
                                    <?php foreach($actmode as $s) { ?>
                                      <option value="<?php echo htmlspecialchars($s); ?>"><?php echo htmlspecialchars($s); ?></option>
                                      <?php } ?>
                                  </select>
                            </div> 

                             <div  class="col-md-4 leads-filter-column pull-left">

                            <?php echo render_input('amount','Amount','','number');
                    
                     ?> 
                   </div>
                   
                   <div  class="col-md-4 leads-filter-column pull-left">

                 
                  <?php echo render_textarea('note','Narration','',array('rows'=>2),array()); ?>

                    </div>
                    <div  class="col-md-4 leads-filter-column pull-left">
                    <button type="submit" class="btn btn-info">Add New Entry</button>
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
                <?php
                        $table_data = array(
                            "Sr.No.",
                            "Account Code",
                            "Account Name",
                            "DR/CR",
                            "Debit Amount",
                            "Credit Amoun",
                            "Narration",
                            "Action",
                                                        
                            );
                        render_datatable($table_data,'table_contra');
                  ?> 

               </div>
            </div>
        </div>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
</div>

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
    initDataTable(table_staff,admin_url + 'account/table_contra_entry', '','', StaffServerParams);
    
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
