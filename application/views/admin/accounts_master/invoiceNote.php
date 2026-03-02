<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
    <!-- Panel body -->
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                  <h4 class="no-margin font-bold"></h4>
                  <hr>
                  
                </div>
            </div>
            
                <?php echo form_open('admin/accounts_master/InvoiceNote',array('id'=>'user_master_form')); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="login_access" class="control-label">Invoice Note</label>
                           <textarea name="comment" placeholder="<?php echo _l('task_single_add_new_comment'); ?>"
                    id="task_comment" rows="12" class="form-control ays-ignore"><?php echo $Getnote->note; ?></textarea>
                        </div>
                        
                       
					    <div class="clearfix"></div>
					    <br></br>
					    <div class="col-md-12">
					   <?php
                        if (has_permission_new('user_master', '', 'edit')) {
                        ?>
                            <div class="add_button" id="add_button">
                                <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Save</button>
                            </div>
                        <?php }else{
                        echo "<span style='color:red'>Your not permitted to update record..</span>";
                        } ?>
                        </div>
                   </div> 
                <?php echo form_close(); ?>
                
          </div>
        </div>
      </div>
      <!-- Panel body-->
      
      <!-- pane end-->
    </div>
  </div>
</div>

<?php init_tail(); ?>
