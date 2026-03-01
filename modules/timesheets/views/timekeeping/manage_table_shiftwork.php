<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
  <div class="row">
   <?php if($this->session->flashdata('debug')){ ?>
     <div class="col-lg-12">
      <div class="alert alert-warning">
       <?php echo $this->session->flashdata('debug'); ?>
     </div>
   </div>
 <?php } ?>
 <div class="col-md-12">
  <div class="panel_s">
   <div class="panel-body">
		<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>HR</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Work Shift Table</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
     <!--<h4><i class=" fa fa-fax"></i> <?php echo _l($title); ?></h4>        
     <div class="clearfix"></div>-->
     <div class="clearfix">
      <?php if(is_admin()){ ?>
       <div class="row filter_by">
        <div class="col-md-2 leads-filter-column">
          <?php echo render_input('month_timesheets','month',date('Y-m'), 'month'); ?>
        </div>
        <div class="col-md-3 leads-filter-column">
          <?php echo render_select('department_timesheets_s',$departments,array('departmentid', 'name'),'department'); ?>
        </div>
        <div class="col-md-3 leads-filter-column">
          <?php echo render_select('role_timesheets_s',$job_position,array('roleid', 'name'),'role'); ?>
        </div>
        <div class="col-md-3 leads-filter-column">
          <?php echo render_select('staff_timesheets_s[]',$staffs,array('staffid',array('firstname','lastname')),'staff','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false); ?>
        </div>
        <div class="col-md-1 mtop25">
          <button type="button" class="btn btn-info shift_work_filter pull-right"><?php echo _l('filter'); ?></button>
        </div>

      </div>
    <?php } ?>
  </div>
  <div class="clearfix"></div>
  <div class="hot handsontable htColumnHeaders" id="example">                 
  </div>
</div>    
</div>
</div>
</div>
<div class="clearfix"></div>
</div>
<?php echo form_close(); ?>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<div id="new_version"></div>
<?php init_tail(); ?>
<?php require 'modules/timesheets/assets/js/shiftwork_js.php'; ?>
</body>
</html>
