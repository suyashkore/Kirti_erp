<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
  <div class="col-md-4">
      <select name="leave_filter_staff[]" class="selectpicker" id="leave_filter_staff" onchange="filter_hanson();" data-width="100%" data-none-selected-text="<?php echo _l('staff'); ?>" data-live-search="true" multiple> 
         <?php 
          foreach ($staff as $value) { ?>
                    <option value="<?php echo html_entity_decode($value['staffid']); ?>"><?php echo html_entity_decode($value['staffid']).' # '.$value['firstname'].' '.$value['lastname']; ?></option> 
          <?php } ?>  
      </select>
  </div>
    <div class="col-md-4">
      <select name="leave_filter_department[]" class="selectpicker" id="leave_filter_department" onchange="filter_hanson();" data-width="100%" data-none-selected-text="<?php echo _l('department'); ?>" data-live-search="true" multiple> 
         <?php 
                 foreach ($department as $value) { ?>
                    <option value="<?php echo html_entity_decode($value['departmentid']); ?>"><?php echo html_entity_decode($value['name']); ?></option> 
          <?php } ?>  
      </select>
  </div>
    <div class="col-md-4">
      <select name="leave_filter_roles[]" class="selectpicker" id="leave_filter_roles" onchange="filter_hanson();" data-width="100%" data-none-selected-text="<?php echo _l('role'); ?>" data-live-search="true" multiple> 
         <?php 
                 foreach ($role as $value) { ?>
                    <option value="<?php echo html_entity_decode($value['roleid']); ?>"><?php echo html_entity_decode($value['name']); ?></option> 
          <?php } ?>  
      </select>
  </div>
  <div class="clearfix"></div>
  <br>
  <br>
  <div class="clearfix"></div>
<div class="col-md-12">
   <?php  echo form_open(admin_url('timesheets/set_leave'), array('id'=>'setting-leave-form')); 
          echo form_hidden('leave_of_the_year_data');  ?>
           <div class="hot handsontable htColumnHeaders" id="example">
           </div>
           <div class="clearfix"></div> 
           <hr>
           <div class="col-md-12 mtop5">
             <button class="btn btn-primary save_leave_table pull-right" onclick="get_data_hanson();" ><?php echo _l('save'); ?></button>
           </div>   
  <?php echo form_close(); ?>
  
  </div>
</div>



