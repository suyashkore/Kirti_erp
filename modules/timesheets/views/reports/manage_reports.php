<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();  ?>
<div id="wrapper" >
 <div class="content">
  <div class="row">
   <div class="col-md-12">
    <div class="panel_s">
     <div class="panel-body">
      <div class="row">
          <div class="col-md-12 border-right">
            <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('reports'); ?></h4>
            <hr />
          </div>
        <!-- Table report -->
        <div class="col-md-4 border-right">
            <p><a href="#" class="font-medium" onclick="init_report(this,'history_check_in_out2'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> Start / End Day & Visiting Area </a></p>
        </div>
        <div class="col-md-4 border-right">
            <p><a href="#" class="font-medium" onclick="init_report(this,'travel_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> Travel Report </a></p>
        </div>
        
     
</div>
<div id="report" class="hide">
 <hr class="hr-panel-heading" />
 <!--<div class="row">
                   
</div>-->

<?php if(has_permission('report_management', '', 'view') || is_admin()){ ?>
 
<!-- Start End DAy report -->
<div class="row table1 hide">
    <div class="col-md-12 ">
    <center><h4 class="title_table">Check in/out & Location Visit History</h4></center> 
    </div>
  <div class="staff_filter_div col-md-3 ">
   <div class="form-group">
     <label for="staff_filter"><?php echo _l('staff'); ?></label>
     <select name="staff_filter" class="selectpicker" data-live-search="true"  data-width="100%" data-none-selected-text="<?php echo _l('invoice_status_report_all'); ?>">
      <option value="">All</option>
      <?php foreach($staff as $item){ ?>
       <option value="<?php echo html_entity_decode($item['staffid']); ?>"><?php echo html_entity_decode($item['firstname']).' '.$item['lastname']; ?></option>
     <?php } ?>
   </select>
  </div>
 </div>
 
    <div class="col-md-4">
        <div class="bg-light-gray border-radius-4">
            <div class="form-group" id="report-time1">
                <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                   <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                   <option value="today">Today</option>
                   <option value="this_month" selected><?php echo _l('this_month'); ?></option>
                   <option value="1"><?php echo _l('last_month'); ?></option>
                   <option value="this_year"><?php echo _l('this_year'); ?></option>
                   <option value="last_year"><?php echo _l('last_year'); ?></option>
                   <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                   <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                   <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                   <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                </select>
            </div>
            <div id="date-range" class="hide mbot15">
            <div class="row">
             <div class="col-md-6">
              <?php echo render_date_input('report-from','report_sales_from_date'); ?>
            </div>
            <div class="col-md-6">
              <?php echo render_date_input('report-to','report_sales_to_date'); ?>
            </div>
          </div>
        </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <br>
        <span class="pull-bot">
            <button name="add" class="btn show_result1 btn-success" onclick="history_check_in_out_report2(); return false;" type="button">Show</button>
        </span>
    </div>

</div>

<!-- travel report  -->
<div class="row table2 hide">
    <div class="col-md-12 ">
    <center><h4 class="title_table">Travel Distance Reports</h4></center> 
    </div>
  <div class="staff_filter2_div col-md-3 ">
   <div class="form-group">
     <label for="staff_filter2"><?php echo _l('staff'); ?></label>
     <select name="staff_filter2" class="selectpicker" data-live-search="true"  data-width="100%" data-none-selected-text="<?php echo _l('invoice_status_report_all'); ?>">
      <option value="">All</option>
      <?php foreach($staff as $item){ ?>
       <option value="<?php echo html_entity_decode($item['staffid']); ?>"><?php echo html_entity_decode($item['firstname']).' '.$item['lastname']; ?></option>
     <?php } ?>
   </select>
  </div>
 </div>
 
    <div class="col-md-4">
        <div class="bg-light-gray border-radius-4">
            <div class="form-group" id="report-time2">
                <label for="months-report2"><?php echo _l('period_datepicker'); ?></label><br />
                <select class="selectpicker" name="months-report2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                   <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                   <option value="today">Today</option>
                   <option value="this_month" selected><?php echo _l('this_month'); ?></option>
                   <option value="1"><?php echo _l('last_month'); ?></option>
                   <option value="this_year"><?php echo _l('this_year'); ?></option>
                   <option value="last_year"><?php echo _l('last_year'); ?></option>
                   <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                   <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                   <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                   <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                </select>
            </div>
            <div id="date-range2" class="hide mbot15">
            <div class="row">
             <div class="col-md-6">
              <?php echo render_date_input('report-from2','report_sales_from_date'); ?>
            </div>
            <div class="col-md-6">
              <?php echo render_date_input('report-to2','report_sales_to_date'); ?>
            </div>
          </div>
        </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <br>
        <span class="pull-bot">
            <button name="add" class="btn show_result2 btn-success" onclick="travel_reports(); return false;" type="button">Show</button>
        </span>
    </div>

</div>
<!-- workplace - root -->
<?php } ?>
<?php $this->load->view('reports/annual_leave_report.php'); ?>
<?php $this->load->view('reports/working_hours.php'); ?>              
<?php $this->load->view('reports/manage_requisition_report.php'); ?>
<?php $this->load->view('reports/general_public_report.php'); ?> 
<?php $this->load->view('reports/history_check_in_out.php'); ?> 
<?php $this->load->view('reports/history_check_in_out2.php'); ?> 
<?php $this->load->view('reports/travel_report.php'); ?> 
<?php $this->load->view('reports/check_in_out_progress_according_to_the_route.php'); ?> 
<?php $this->load->view('reports/report_of_leave.php'); ?> 
<?php $this->load->view('reports/leave_by_department.php'); ?> 
<?php $this->load->view('reports/ratio_check_in_out_by_workplace.php'); ?> 
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<?php require 'modules/timesheets/assets/js/report_js.php'; ?>
</body>
</html>

