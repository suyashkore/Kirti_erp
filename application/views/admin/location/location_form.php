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
                                <li class="breadcrumb-item active" aria-current="page"><b>Location Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        
                        <?php echo form_open(admin_url('location_master/manage'), ['id' => 'locationForm']); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <?php echo render_input('location_description', 'Location Description*', '', 'text', ['required' => true, 'placeholder' => '']); ?>
                            </div>
                            <div class="col-md-3">
                                <?php 
                                    $types = [
                                        ['id' => 'Party Location', 'name' => 'Party Location'],
                                        ['id' => 'Our Location', 'name' => 'Our Location']
                                    ];
                                    echo render_select('location_type', $types, ['id', 'name'], 'Location Type*', 'Party Location', ['required' => true]); 
                                ?>
                            </div>
                            <div class="col-md-3">
                                <?php 
                                    $groups = [
                                        ['id' => 'DETAIL', 'name' => 'DETAIL'],
                                        ['id' => 'DOC', 'name' => 'DOC']
                                    ];
                                    // 'DETAIL' is selected by default
                                    echo render_select('group_name', $groups, ['id', 'name'], 'Group Name', 'DETAIL'); 
                                ?>
                            </div>
                            <div class="col-md-3">
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
                        
                        <br>
                        <div class="text-right">
                            <button type="submit" class="btn btn-success">Create</button>
                            <button type="button" class="btn btn-danger cancelBtn">Cancel</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>

                <div class="panel_s" style="margin-top:15px;">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <!-- dt-table class initializes DataTables which provides pagination, search, etc. -->
                            <table class="table table-bordered table-striped dt-table" id="location_table">
                                <thead>
                                    <tr>
                                        <th style="background-color : #50607b; ">Sr No.</th>
                                        <th style="background-color : #50607b; " >Location Description</th>
                                        <th style="background-color : #50607b; " >Party Location</th>
                                        <th style="background-color : #50607b; " >Blocked</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($locations)){ 
                                        $i = 1;
                                        foreach($locations as $loc){ ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $loc['description']; ?></td>
                                            <td><?php echo $loc['type']; ?></td>
                                            <td><?php echo $loc['blocked']; ?></td>
                                        </tr>
                                    <?php } } ?>
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
        appValidateForm($('#locationForm'), {
            location_description: 'required',
            location_type: 'required'
        });

        // Cancel Button Action
        $('.cancelBtn').on('click', function() {
            $('#locationForm')[0].reset();
            // Reset selectpickers
            $('select[name="location_type"]').val('Party Location').selectpicker('refresh');
            $('select[name="group_name"]').val('DETAIL').selectpicker('refresh');
        });
    });
</script>
<style>
	#AccountID {
    text-transform: uppercase;
	}
	#Pan {
    text-transform: uppercase;
	}
	#vat {
    text-transform: uppercase;
	}
	#table_Account_List td:hover {
    cursor: pointer;
	}
	#table_Account_List tr:hover {
    background-color: #ccc;
	}
	
    .table-Account_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Account_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Account_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>