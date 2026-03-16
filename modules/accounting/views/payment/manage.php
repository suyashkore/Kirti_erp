  <?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
        
         <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form')) ;?>
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                 
                  <h4 class="no-margin">Add New Entry Payment Voucher</h4>
                  <hr class="hr-panel-heading" />
           
                     <div class="row filter_by">
                    <?php
                    $next_transaction_number = get_option('next_receipt_number');
                    $format = get_option('invoice_number_format');
                    $prefix = "RPT";
                    $__number = $next_transaction_number;
                    $_transaction_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    ?>
                          

                    <div  class="col-md-3 leads-filter-column pull-left">

                    
                        <div class="form-group">
                           <label for="number">
                              Challan Number
                             <!-- <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('invoice_number_not_applied_on_draft') ?>" data-placement="top"></i>-->
                        </label>
                           <div class="input-group">
                              <span class="input-group-addon">
                              <?php
                                echo $prefix;
                              ?>
                              </span>
                              <input type="text" name="number" class="form-control" value="<?php echo ($_is_draft) ? 'DRAFT' : $_transaction_number; ?>" disabled >
                              <input type="hidden" name="voucher_id" value="<?php echo $__number; ?>">
                              <input type="hidden" name="voucher_type" value="SALESRECEIPT">
                              <input type="hidden" name="narretion" value="By SALESRECEIPT <?php echo $prefix.'-'.$_transaction_number; ?>">
                           </div>
                        </div>
                        
                   </div>
                   
                          <div  class="col-md-3 leads-filter-column pull-left">
                            <?php
                             $today = date("Y-m-d");  
                             
                            $age = array("autocomplete"=>"on");
    ?>
                          <?php echo render_date_input('date','Voucher Date', $today , $age);
                          ?>
                        </div>
                        
                    <div  class="col-md-3 leads-filter-column pull-left form-group">

                              <label>DR/CR</label>
                                  <select name="staff" id="staff" data-live-search="true" class="selectpicker"  data-actions-box="false" data-width="100%" data-none-selected-text="Select Mode">
                                    <option>Select Mode</option>
                                    <option value="DR">DR</option>
                                    <option value="CR">CR</option>
                                  </select>
                    </div> 
                    
                        
                    <div class="clearfix"></div>
                             
                    <div  class="col-md-4 leads-filter-column pull-left">
                   
                   <?php echo render_select('client',$dist_list,array('userid','company'),'Distibutor', '', array(), '', '', false); ?>

                    </div>
                            

                    <div  class="col-md-4 leads-filter-column pull-left">

                            <?php echo render_input('amount','Amount','','number');
                    
                     ?> 
                   </div>
                   
                   <!--<div  class="col-md-4 leads-filter-column pull-left">

                 
                  <?php //echo render_textarea('note','Narration','',array('rows'=>2),array()); ?>

                    </div>-->
                    <div class="clearfix"></div>
                    <div  class="col-md-4 leads-filter-column pull-left">
                    <button type="submit" class="btn btn-info">Add New Entry</button>
                  </div>
                            
                        </div>

               </div>
            </div>
         </div>


         
         <?php echo form_close(); ?>
      </div>

      <
      <div class="btn-bottom-pusher"></div>
   </div>
</div>

<?php init_tail(); ?>


</body>
</html>

