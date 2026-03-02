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
                <p><a href="#" class="font-medium" ><i class="fa fa-caret-down" aria-hidden="true"></i> Start / End Day & Visiting Area </a></p>
            </div>
            <div class="col-md-4 border-right">
                <p><a href="#" class="font-medium" ><i class="fa fa-caret-down" aria-hidden="true"></i> Travel Report </a></p>
            </div>
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

