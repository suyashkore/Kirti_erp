<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="panel_s">
       <div class="panel-body">
        <h4><?php echo _l('attendance_sheet') ?>
        <hr>
      </h4>
      <div class="horizontal-tabs mb-5">
      </div>
      <input type="hidden" name="current_month" value="<?php echo date('Y-m'); ?>">
      <?php 
      if (has_permission('attendance_management', '', 'view') || is_admin()) {
       ?>
       <div class="row filter_by">
        <div class="col-md-2 leads-filter-column">
          <?php echo render_input('month_timesheets','month',date('Y-m'), 'month'); ?>
        </div>
        <div class="col-md-2 leads-filter-column">
          <?php echo render_select('department_timesheets',$departments,array('departmentid', 'name'),'department'); ?>
        </div>
        <div class="col-md-2 leads-filter-column">
          <?php echo render_select('job_position_timesheets',$roles,array('roleid', 'name'),'role'); ?>
        </div>
        <div class="col-md-2 leads-filter-column">
          <?php echo render_select('staff_timesheets[]',$staffs,array('staffid',array('firstname','lastname')),'staff','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
        </div>
        <div class="col-md-3 leads-filter-column">
          <!--<?php echo render_select('state_timesheets',$state,array('id',array('name')),'hr_state','',array('data-actions-box'=>true),array(),'','',false); ?>-->
        <?php echo render_select('state_timesheets',$state,array('short_name', 'state_name'),'hr_state'); ?>
        </div>
        <div class="col-md-1 mtop25">
          <button type="button" class="btn btn-info timesheets_filter">Show</button>
        </div>                         
      </div>
    <?php } ?>
    <?php echo form_open(admin_url('timesheets/manage_timesheets'),array('id'=>'timesheets-form')); ?>
    <hr class="hr-panel-heading no-margin"/>               
    <div class="row mtop15">
      <div class="col-md-8 line-suggestion">
        <!--<button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('p_x_timekeeping'); ?>" class="btn" >AL</button>
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('W_x_timekeeping'); ?>" class="btn" >W</button>
        --><!--<button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('A_x_timekeeping'); ?>" class="btn" >U</button>
        --><button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('Le_x_timekeeping'); ?>" class="btn" >HO</button>
        <!--<button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('E_x_timekeeping'); ?>" class="btn" >E</button>
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('L_x_timekeeping'); ?>" class="btn" >L</button>
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('CT_x_timekeeping'); ?>" class="btn" >B</button>
        --><button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('OM_x_timekeeping'); ?>" class="btn" >SI</button>
        <!--<button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('TS_x_timekeeping'); ?>" class="btn" >M</button>
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('H_x_timekeeping'); ?>" class="btn" >ME</button>
        --><button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('NS_x_timekeeping'); ?>" class="btn" >WO</button>
       <!--  <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php //echo _l('EB_x_timekeeping'); ?>" class="btn" >EB</button>
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php //echo _l('UB_x_timekeeping'); ?>" class="btn" >UB</button> -->
        <!--<button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('P_timekeeping'); ?>" class="btn" >P</button>-->
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('AB_x_timekeeping'); ?>" class="btn" >AB</button>
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('NR_x_timekeeping'); ?>" class="btn" >NR</button>
        <button type="button" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo _l('PP_x_timekeeping'); ?>" class="btn" >P</button>
        <div class="clearfix"></div>
      </div>
      <div class="col-md-4">
        <a href="javascript:void(0)" class="btn btn-default pull-right mtop5 mleft10 export_excel">
          <i class="fa fa-file-excel"></i> <?php echo _l('export_to_excel'); ?>
        </a>
        <?php if($data_timekeeping_form == 'timekeeping_manually'){ ?>
          <!--<button type="button" onclick="open_check_in_out();" class="btn btn-info pull-right display-block mtop5 check_in_out_timesheet" data-toggle="tooltip" title="" data-original-title="<?php echo _l('check_in').' / '._l('check_out'); ?>"><?php echo _l('check_in'); ?> / <?php echo _l('check_out'); ?></button>-->
        <?php }elseif($data_timekeeping_form == 'csv_clsx'){ ?>
          <button type="button" class="btn btn-info pull-right display-block mtop5 check_in_out_timesheet" data-toggle="modal" data-target="#import_timesheets_modal" data-original-title="<?php echo _l('import_timesheets'); ?>"><?php echo _l('import_timesheets'); ?></button>
        <?php } ?>
      </div>
      <div class="clearfix"></div>
      <br>
        
      <div class="col-md-12">
          <div id='loader_filter' style='display: none;'>
          <p style="fontsize:15px;font-weight:700;">Please wait Loading Data...</p>
        </div>
        <div id='loader_export' style='display: none;'>
          <p style="fontsize:15px;font-weight:700;">Please wait Exporting Data to Excel...</p>
        </div>
        <div class="form">    
          <div class="hot handsontable htColumnHeaders" id="example">
          </div>
          <?php echo form_hidden('time_sheet'); ?>
          <?php echo form_hidden('month', date('m-Y')); ?>
          <?php echo form_hidden('latch'); ?>
          <?php echo form_hidden('unlatch'); ?>
        </div>
        <hr class="hr-panel-heading" />
        <?php 
        if($check_latch_timesheet){ 
          $latched = '';
          $latch = 'hide';
        }else{
          $latched = 'hide';
          $latch = '';              
        } ?>


        <?php if(is_admin() || has_permission('timesheets_timekeeping','','edit')) {?>

         <!-- <button class="btn btn-danger pull-right unlatch_time_sheet mleft5 <?php echo html_entity_decode($latched); ?>" id="btn_unlatch" onclick="return confirm('<?php echo _l('timekeeping_unlatch'); ?>')"><?php echo _l('reopen_attendance'); ?></button>

          <button class="btn btn-info pull-right latch_time_sheet mleft5 <?php echo html_entity_decode($latch); ?>" id="btn_latch" onclick="return confirm('<?php echo _l('timekeeping_latch'); ?>')"><?php echo _l('close_attendance'); ?></button>
-->
          <?php
          $data_timekeeping_form = get_timesheets_option('timekeeping_form');
         ?>
         
        <?php } ?>
      </div>
    </div>
    <?php echo form_hidden('is_edit', 0); ?>
    <?php echo form_close(); ?>

         
  </div>
</div>
</div>
<div class="clearfix"></div>
</div>
</div>
</div>


<?php init_tail(); ?>
<style>
    .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9
    {
        padding-right: 5px;
    padding-left: 5px;
    }
</style>
</body>

</html>
<?php require 'modules/timesheets/assets/js/timesheets.php'; ?>