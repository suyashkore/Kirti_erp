<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
  <div class="panel_s mbot10">
   <div class="panel-body _buttons">

    <?php if(has_permission('purchase','','create')){ ?>
     <a href="<?php echo admin_url('purchase/estimate'); ?>" class="btn btn-info pull-left new"><?php echo _l('create_new_estimate'); ?></a>
   <?php } ?>
   <div class="col-md-3">
     <select name="pur_request[]" id="pur_request" class="selectpicker pull-right mright10" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('purchase_request'); ?>">
       <?php foreach($pur_request as $s) { ?>
        <option value="<?php echo html_entity_decode($s['id']); ?>" ><?php echo html_entity_decode($s['pur_rq_code'].' - '.$s['pur_rq_name']); ?></option>
      <?php } ?>
     </select>
   </div>

   <div class="col-md-3">
     <select name="vendor[]" id="vendor" class="selectpicker pull-right mright10" multiple data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('vendor'); ?>">
       <?php foreach($vendors as $s) { ?>
        <option value="<?php echo html_entity_decode($s['userid']); ?>" ><?php echo html_entity_decode($s['company']); ?></option>
      <?php } ?>
     </select>
   </div>

  <div class="display-block text-right"> 
    <a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_estimate_view('.table-pur_estimates','#estimate'); return false;" data-toggle="tooltip" title="<?php echo _l('estimates_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
  </div>

</div>
</div>
<div class="row">
  <div class="col-md-12" id="small-table">
    <div class="panel_s">
      <div class="panel-body">
        <!-- if estimateid found in url -->
        <?php echo form_hidden('estimateid',$estimateid); ?>
         <?php $this->load->view('quotations/table_html'); ?>
      </div>
    </div>
  </div>
  <div class="col-md-7 small-table-right-col">
    <div id="estimate" class="hide">
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="send_quotation" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <?php echo form_open_multipart(admin_url('purchase/send_quotation'),array('id'=>'send_quotation-form')); ?>
      <div class="modal-content modal_withd">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">
                  <span><?php echo _l('send_a_quote'); ?></span>
              </h4>
          </div>
          <div class="modal-body">
              <div id="additional_quo"></div>
              <div class="row">
                <div class="col-md-12 form-group">
                  <label for="vendors"><span class="text-danger">* </span><?php echo _l('vendor'); ?></label>
                    <select name="vendors[]" id="vendor" class="selectpicker" required multiple="true"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <?php foreach($vendors as $s) { ?>
                        <option value="<?php echo html_entity_decode($s['userid']); ?>"><?php echo html_entity_decode($s['company']); ?></option>
                          <?php } ?>
                    </select>
                    <br>
                </div>     
                
                <div class="col-md-12">
                  <label for="subject"><span class="text-danger">* </span><?php echo _l('subject'); ?></label>
                  <?php echo render_input('subject','','','',array('required' => 'true')); ?>
                </div>
                <div class="col-md-12">
                  <label for="attachment"><?php echo _l('attachment'); ?></label>
                  <?php echo render_input('attachment','','','file'); ?>
                </div>
                <div class="col-md-12">
                  <?php echo render_textarea('content','content','',array(),array(),'','tinymce') ?>
                </div>     
                <div id="type_care">
                  
                </div>        
              </div>
          </div>
          <div class="modal-footer">
              <button type=""class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
              <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
          </div>
      </div><!-- /.modal-content -->
          <?php echo form_close(); ?>
      </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
