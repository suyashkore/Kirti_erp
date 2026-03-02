<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(is_admin() || has_permission('timesheets_timekeeping','','create')){ ?>
 <a href="#" onclick="new_leave(); return false;" class="btn btn-info" data-toggle="sidebar-right" data-target=".leave_modal-add-edit-modal"><?php echo _l('new'); ?></a>
<?php } ?>
<br/><br/>
<div class="clearfix"></div>
<br>
<div id="unexpected_break">
  <table class="table dt-table">
   <thead>
    <tr>
      <th><?php echo _l('break_date'); ?></th>
      <th><?php echo _l('leave_reason'); ?></th>
      <th><?php echo _l('leave_type'); ?></th>
      <th><?php echo _l('department'); ?></th>
      <th><?php echo _l('role'); ?></th>
      <th><?php echo _l('repeat_by_year'); ?></th>
      <th><?php echo _l('add_from'); ?></th>
      <th><?php echo _l('options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php          
    $this->load->model('roles_model');
    $this->load->model('departments_model');
    foreach($holiday as $d) {?>
      <tr>
        <td><?php echo _d($d['break_date']); ?></td>
        <td><?php echo html_entity_decode($d['off_reason']); ?></td>
        <td><?php echo _l($d['off_type']); ?></td>
        <td><?php 
        if($d['department']){
          if($d['department']!=''){
            $list_department = explode(',', $d['department']);
            $department_name = '';
            foreach ($list_department as $key => $value) {
              $data_department = $this->departments_model->get($value);
              if($data_department){
                $department_name .= $data_department->name.', ';
              }
            }
            if($department_name != ''){
              $department_name = rtrim($department_name, ', ');
            }
            echo html_entity_decode($department_name); 
          }
        }
        ?></td>
        <td><?php
        if($d['position']){
          if($d['position']!=''){
            $list_position = explode(',', $d['position']);
            $role_name = '';
            foreach ($list_position as $key => $value) {
              $data_role = $this->roles_model->get($value);
              if($data_role){
                $role_name .= $data_role->name.', ';
              }
            }
            if($role_name != ''){
              $role_name = rtrim($role_name, ', ');
            }
            echo html_entity_decode($role_name); 
          }
        }
        ?>                  
      </td>
      <td><?php
      if((int)$d['repeat_by_year'] == 1){
        echo _l('on'); 
      }
      if((int)$d['repeat_by_year'] == 0){
        echo _l('off');   
      }
      ?></td>
      <td><a href="<?php echo admin_url('timesheets/member/'.$d["add_from"]); ?>">
        <?php echo staff_profile_image($d['add_from'],[
          'staff-profile-image-small mright5',
        ], 'small', [
          'data-toggle' => 'tooltip',
          'data-title'  => get_staff_full_name($d['add_from']),
        ]); ?>
      </a></td>
      <td>
        <a href="#" onclick="edit_day_off(this,<?php echo html_entity_decode($d['id']); ?>); return false" data-off_reason="<?php echo html_entity_decode($d['off_reason']); ?>" data-off_type="<?php echo html_entity_decode($d['off_type']); ?>" data-break_date="<?php echo html_entity_decode($d['break_date']); ?>" data-timekeeping="<?php echo html_entity_decode($d['timekeeping']); ?>" data-department="<?php echo html_entity_decode($d['department']); ?>"
          data-repeat_by_year="<?php echo $d['repeat_by_year']; ?>" 
          data-position="<?php echo html_entity_decode($d['position']); ?>" class="btn btn-default btn-icon" data-toggle="sidebar-right" data-target=".leave_modal_update-edit-modal"><i class="fa fa-pencil-square-o"></i></a>
          <a href="<?php echo admin_url('timesheets/delete_day_off/'.$d['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
        </td>
      </tr>
    <?php } ?>

  </tbody>
</table>
</div>
<div class="modal fade" id="leave_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">
          <span class="edit-title"><?php echo _l('edit_break_date'); ?></span>
          <span class="add-title"><?php echo _l('new_break_date'); ?></span>
        </h4>
      </div>
      <?php echo form_open(admin_url('timesheets/day_off'),array('id'=>'leave_modal-form')); ?>     
      <input type="hidden" name="id">         
      <div class="modal-body">
        <div id="additional_leave"></div> 
        <div class="row">
          <div class="col-md-6">
            <?php echo render_date_input('break_date','break_date',''); ?> 
          </div>                          
          <div class="col-md-6">
            <label for="leave_type" class="control-label"><?php echo _l('leave_type'); ?></label>
            <select name="leave_type" class="selectpicker" id="leave_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
              <option value=""></option>                  
              <option value="holiday"><?php echo _l('holiday'); ?></option>
              <option value="event_break"><?php echo _l('event_break'); ?></option>
              <option value="unexpected_break"><?php echo _l('unexpected_break'); ?></option>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <?php echo render_textarea('leave_reason','leave_reason','') ?> 
          </div>
        </div>

        <div class="row">
         <div class="col-md-6">
          <label for="department[]"><?php echo _l('department'); ?></label>
          <select name="department[]" id="department[]" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>" multiple data-hide-disabled="true">  
            <?php foreach($department as $dpm){ ?>
              <option value="<?php echo html_entity_decode($dpm['departmentid']); ?>"><?php echo html_entity_decode($dpm['name']); ?></option>
            <?php } ?>
          </select>
        </div> 
        <div class="col-md-6">
          <label for="position[]"><?php echo _l('role'); ?></label>
          <select name="position[]" id="position[]" class="selectpicker" multiple data-width="100%" data-none-selected-text="<?php echo _l('all'); ?>" data-hide-disabled="true">
            <?php
            foreach($role as $dpm){ ?>
              <option value="<?php echo html_entity_decode($dpm['roleid']); ?>"><?php echo html_entity_decode($dpm['name']); ?></option>
            <?php } ?>   
          </select>
        </div> 
      </div>
      <div class="row">
        <div class="col-md-12">
          <br>
          <div class="checkbox">              
            <input type="checkbox" class="capability" name="repeat_by_year" value="1">
            <label><?php echo _l('automatically_repeat_by_year'); ?></label>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>                 
  </div>
</div>
</div>



