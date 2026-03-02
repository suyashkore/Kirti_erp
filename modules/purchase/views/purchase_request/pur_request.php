<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <h4 class="customer-profile-group-heading"><?php echo _l($title). ' '._l('purchase_request') ; ?></h4>
                  <?php if(isset($pur_request)){
                           echo form_open(admin_url('purchase/pur_request/'.$pur_request->id),array('id'=>'add_edit_pur_request-form'));
                        }else{
                           echo form_open(admin_url('purchase/pur_request'),array('id'=>'add_edit_pur_request-form'));
                        }?>
                <div class="row">
                  <div class="col-md-12">
                    <p class="bold p_style" ><?php echo _l('information'); ?></p>
                    <hr class="hr_style"  />
                  </div>

                  <div class=" col-md-12">
                  <div class="panel panel-success">
                    <div class="panel-heading"><?php echo _l('purchase_request_header'); ?></div>
                    <div class="panel-body">
                      <div class="col-md-4">
                       <?php
                          $prefix = get_purchase_option('pur_request_prefix');
                          $next_number = get_purchase_option('next_pr_number');
                          $number = (isset($pur_request) ? $pur_request->number : $next_number);
                          echo form_hidden('number',$number); ?> 
                           
                      <?php $pur_rq_code = ( isset($pur_request) ? $pur_request->pur_rq_code : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('Y'));
                      echo render_input('pur_rq_code','pur_rq_code',$pur_rq_code ,'text',array('readonly' => '')); ?>
                    </div>
                    <div class="col-md-8">
                      <?php $pur_rq_name = ( isset($pur_request) ? $pur_request->pur_rq_name : '');
                      echo render_input('pur_rq_name','pur_rq_name', $pur_rq_name); ?>
                    </div>

                    <div class="col-md-6 form-group">
                      <label for="project"><?php echo _l('project'); ?></label>
                        <select name="project" id="project" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach($projects as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_request) && $s['id'] == $pur_request->project){ echo 'selected'; } ?>><?php echo html_entity_decode($s['name']); ?></option>
                            <?php } ?>
                        </select>
                        <br><br>
                    </div>

                    <div class="col-md-6 form-group">
                      <label for="type"><?php echo _l('type'); ?></label>
                        <select name="type" id="type" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <option value="capex" <?php if(isset($pur_request) && $pur_request->type == 'capex'){ echo 'selected';} ?>><?php echo _l('capex'); ?></option>
                          <option value="opex" <?php if(isset($pur_request) && $pur_request->type == 'opex'){ echo 'selected';} ?>><?php echo _l('opex'); ?></option>
                        </select>
                        <br><br>
                    </div>

                    <div class="col-md-6 form-group">
                      <label for="department"><?php echo _l('department'); ?></label>
                        <select name="department" id="department" class="selectpicker" onchange="department_change(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach($departments as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['departmentid']); ?>" <?php if(isset($pur_request) && $s['departmentid'] == $pur_request->department){ echo 'selected'; } ?>><?php echo html_entity_decode($s['name']); ?></option>
                            <?php } ?>
                        </select>
                        <br><br>
                    </div>

                    <div class="col-md-4 form-group">
                      <label for="requester"><?php echo _l('requester'); ?></label>
                        <select name="requester" id="requester" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                          <option value=""></option>
                          <?php foreach($staffs as $s) { ?>
                            <option value="<?php echo html_entity_decode($s['staffid']); ?>" <?php if(isset($pur_request) && $s['staffid'] == $pur_request->requester){ echo 'selected'; }elseif($s['staffid'] == get_staff_user_id()){ echo 'selected'; } ?>><?php echo html_entity_decode($s['lastname'] . ' '. $s['firstname']); ?></option>
                            <?php } ?>
                        </select>
                        <br><br>
                    </div>

                    <div class="col-md-2">
                      <div class="checkbox checkbox-primary pull-right mtop25">
                        <input onchange="from_list_items(this); return false" type="checkbox" id="from_items" name="from_items" value="1" <?php if(isset($pur_request)){ echo 'disabled';} ?> <?php if(isset($pur_request) && $pur_request->from_items == 0){ echo ''; }else{ echo 'checked'; } ?>>
                        <label for="from_items"><?php echo _l('from_list_items'); ?>
                      
                        </label>
                      </div>
                    </div>

                    <div class="col-md-12">
                      <?php $rq_description = ( isset($pur_request) ? $pur_request->rq_description : '');
                      echo render_textarea('rq_description','rq_description',$rq_description); ?>
                    </div>

                    </div>
                  </div>
                  </div>

                  
                  
                  <div class="col-md-12">
                    <p class="bold p_style"><?php echo _l('pur_detail'); ?></p>
                    <hr class="hr_style"  />
                    <div class="col-md-12 " id="example">
                      <hr>
                    </div>

                  </div>
                  </div>
                  <?php echo form_hidden('request_detail'); ?>
                    <div class="clearfix"></div>
                    <button id="sm_btn" class="btn btn-info save_detail pull-right"><?php echo _l('submit'); ?></button>
                  <?php echo form_close(); ?>
               </div>
            </div>
            
         </div>
        
      </div>
   </div>
</div>

<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/pur_request_js.php';?>
