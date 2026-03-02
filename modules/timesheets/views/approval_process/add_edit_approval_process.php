<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
  <div class="row">
   <div class="col-md-12" >
    <div class="panel_s">
     <div class="panel-body">
      <div class="row">
       <div class="col-md-12">
        <h4 class="no-margin font-bold"><i class="fa fa-address-card-o" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
        <hr/>

      </div>
    </div>
    <?php $setting = []; ?>
    <?php echo form_open('timesheets/approval_process',array('id'=>'approval-setting-form')); ?>
    <?php $value = (isset($approval_setting)) ? $approval_setting->id : ''; ?>
    <?php echo form_hidden('approval_setting_id', $value); ?>
    <div class="row">
      <div class="col-md-6">
        <?php $value = (isset($approval_setting)) ? $approval_setting->name : ''; ?>
        <?php echo render_input('name','subject',$value,'text'); ?>
      </div>
      <div class="col-md-6">
        <?php $related = [ 
          3 => ['id' => 'additional_timesheets', 'name' => _l('additional_timesheets')],
          4 => ['id' => 'Leave', 'name' => _l('Leave')],
          5 => ['id' => 'late', 'name' => _l('late')],
          6 => ['id' => 'early', 'name' => _l('early')],
          7 => ['id' => 'Go_out', 'name' => _l('Go_out')],
          8 => ['id' => 'Go_on_bussiness', 'name' => _l('Go_on_bussiness')],
        ]; 
        $value = (isset($approval_setting)) ? $approval_setting->related : '';
        ?>
      </div>
      <div class="col-md-6">

        <?php echo render_select('related',$related,array('id','name'),'task_single_related',$value); ?>
      </div>
      <div class="col-md-6">
        <?php $selected = (isset($approval_setting)) ? explode(',', $approval_setting->departments) : '';  ?>
        <?php echo render_select('departments[]',$departments,array('departmentid','name'),'departments',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
      </div>
      <div class="col-md-6">
        <?php $selected = (isset($approval_setting)) ? explode(',', $approval_setting->job_positions) : ''; ?>
        <?php echo render_select('job_positions[]',$job_positions,array('roleid','name'),'role',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
      </div>

      <div class="col-md-6">

        <?php $choose_when_approving = 0;
        if(isset($approval_setting)){
          $choose_when_approving = $approval_setting->choose_when_approving;
        } 
        ?>
        <div id="notification_recipient" class="notification_recipient">
          <div class="select-placeholder form-group">
            <label for="notification_recipient[]"><?php echo _l('notification_recipient'); ?></label>
            <select name="notification_recipient[]" id="notification_recipient[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple="true" data-action-box="true" data-hide-disabled="true" data-live-search="true">
              <?php foreach($staffs as $val){
               $selected = '';
               ?>
               <option value="<?php echo html_entity_decode($val['staffid']); ?>">
                 <?php echo get_staff_full_name($val['staffid']); ?>
               </option>
             <?php } ?>
           </select>
         </div> 
       </div>
     </div>
     <div class="col-md-6">

      <?php
      $number_day_approval = 0;
      if(isset($approval_setting)){
        $number_day_approval = $approval_setting->number_day_approval;
      }
      echo render_input('number_day_approval','maximum_number_of_days_to_sign',$number_day_approval,'number'); ?>
    </div>

    <div class="col-md-12 mtop5">
      <strong><?php echo _l('approval_process'); ?></strong>
      <div class="checkbox checkbox-inline checkbox-primary pull-right">
        <input type="checkbox" name="choose_when_approving" id="choose_when_approving" value="1" <?php if($choose_when_approving == 1){echo 'checked';} ?>>
        <label for="choose_when_approving"><?php echo _l('choose_when_approving'); ?></label>
      </div>
      <div class="clearfix"></div>
      <br>
      <hr>
      <br>
    </div>

    <div class="list_approve mleft15 mtop15 <?php if($choose_when_approving == 1){echo 'hide';} ?>">
      <hr/>

      <?php if(!isset($approval_setting)) { ?>
        <div id="item_approve">
          <div class="row">
            <div class="col-md-11">                            
              <div id="is_staff_0">
                <div class="select-placeholder form-group">
                  <label for="staff[0]"><?php echo _l('staff'); ?></label>
                  <select name="staff[0]" id="staff[0]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                    <option value=""></option>
                    <?php foreach($staffs as $val){
                     $selected = '';
                     ?>
                     <option value="<?php echo html_entity_decode($val['staffid']); ?>">
                       <?php echo get_staff_full_name($val['staffid']); ?>
                     </option>
                   <?php } ?>
                 </select>


               </div> 
             </div>
           </div>
           <div class="col-md-1 content-nowrap">
            <span class="pull-bot">
              <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
            </span>
          </div>
        </div>
      </div>
    <?php }else{ 
      $setting = json_decode($approval_setting->setting);
      ?>
      <?php foreach ($setting as $key => $value) { ?>
        <div class="col-md-12 mleft2">
          <div id="item_approve">                            

            <div class="row">                              
              <div class="col-md-11">                              
                <div id="is_staff_<?php echo html_entity_decode($key); ?>">
                  <div class="select-placeholder form-group">
                    <label for="staff[<?php echo html_entity_decode($key); ?>]"><?php echo _l('staff'); ?></label>
                    <select name="staff[<?php echo html_entity_decode($key); ?>]" id="staff[<?php echo html_entity_decode($key); ?>]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                      <option value=""></option>
                      <?php foreach($staffs as $val){
                        $selected = '';
                        if($val['staffid'] == $value->staff){
                          $selected = 'selected';
                        }
                        ?>
                        <option value="<?php echo html_entity_decode($val['staffid']); ?>" <?php echo html_entity_decode($selected); ?>>
                         <?php echo get_staff_full_name($val['staffid']); ?>
                       </option>
                     <?php } ?>
                   </select>
                 </div> 
               </div>
             </div>
             <div class="col-md-1 content-nowrap">
              <span class="pull-bot">
                <?php if($key != 0){ ?>
                  <button name="add" class="btn remove_vendor_requests btn-danger" data-ticket="true" type="button"><i class="fa fa-minus"></i></button>
                <?php }else{ ?>
                  <button name="add" class="btn new_vendor_requests btn-success" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                <?php } ?>
              </span>
            </div>
          </div>

        </div>
      </div>
    <?php }
  } ?>
</div>
</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/timesheets/assets/js/add_edit_approval_process_js.php'; ?>
</body>
</html>