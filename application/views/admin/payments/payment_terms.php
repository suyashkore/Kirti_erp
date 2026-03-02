<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <style>
                            .switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #28a745;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 24px;
}

.slider.round:before {
  border-radius: 50%;
}
                            @media (max-width: 767px) {
                                .mobile-menu-btn { display: block !important; margin-bottom: 10px; width: 100%; text-align: left; }
                                .custombreadcrumb { display: none !important; }
                                .custombreadcrumb.open { display: block !important; }
                                .custombreadcrumb li { display: block; padding: 8px 10px; border-bottom: 1px solid #eee; }
                                .custombreadcrumb li a { display: block; }
                                .custombreadcrumb li+li:before { content: none; }
                            }
                            .mobile-menu-btn { display: none; }
                        </style>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Payment Terms</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        
                        <?php echo form_open(admin_url('payment_terms/create'), ['id' => 'paymentTermsForm']); ?>
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Payment Terms Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" placeholder="Enter Code" required>
                                </div>
                                <div class="form-group">
                                    <label for="days">No. of Days <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="days" name="days" placeholder="Enter Days" required>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Payment Terms Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description" required>
                                </div>
                                <div class="form-group">
                                    <label for="due_date_based_on">Due Date Based On <span class="text-danger">*</span></label>
                                    <select class="form-control" id="due_date_based_on" name="due_date_based_on" required>
                                        <option value="">--Select--</option>
                                        <option value="Posting Date">Posting Date</option>
                                        <option value="Document Date">Document Date</option>
                                        <option value="Entry Date">Entry Date</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Blocked <span style="color: red;">*</span></label>
                            <div style="margin-top: 8px;">
                                <label class="switch">
                                    <input type="checkbox" name="isactive" id="isactive_chk">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>


                        </div>

                        <div class="text-right">
                            <hr>
                            <button type="submit" class="btn btn-success">Create</button>
                            <button type="button" class="btn btn-danger cancelBtn">Cancel</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>

                <div class="panel_s" style="margin-top:15px;">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dt-table" id="payment_terms_table" data-order-col="0" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th style="background-color : #50607b; color: #fff;">Sr No.</th>
                                        <th style="background-color : #50607b; color: #fff;">Payment Terms Code</th>
                                        <th style="background-color : #50607b; color: #fff;">Payment Terms Description</th>
                                        <th style="background-color : #50607b; color: #fff;">No. of Days</th>
                                        <th style="background-color : #50607b; color: #fff;">Due Date Based On</th>
                                        <th style="background-color : #50607b; color: #fff;">Blocked</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($payment_terms)): ?>
                                        <?php $i = 1; foreach($payment_terms as $row): ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $row->code; ?></td>
                                            <td><?php echo $row->desc; ?></td>
                                            <td><?php echo $row->days; ?></td>
                                            <td><?php echo $row->base; ?></td>
                                            <td><?php echo $row->blocked; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="6" class="text-center">No records found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        // Form Validation
        appValidateForm($('#paymentTermsForm'), {
            code: 'required',
            description: 'required',
            days: 'required',
            due_date_based_on: 'required'
        });

        // Cancel Button Action
        $('.cancelBtn').on('click', function() {
            $('#paymentTermsForm')[0].reset();
        });

        // Initialize DataTable with search & export buttons (same behaviour as Location Master) ✅
        if (typeof initDataTableInline === 'function') {
            initDataTableInline($('#payment_terms_table'));
        } else {
            initDataTableInline();
        }
    });
</script>
<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { padding: 8px !important; white-space: nowrap; border:1px solid #ddd !important; font-size:13px; vertical-align: middle !important;}
    
    /* Pagination Styling to match theme */
    .pagination > .active > a, 
    .pagination > .active > a:focus, 
    .pagination > .active > a:hover, 
    .pagination > .active > span, 
    .pagination > .active > span:focus, 
    .pagination > .active > span:hover {
        background-color: #28b8da;
        border-color: #28b8da;
    }
</style>
