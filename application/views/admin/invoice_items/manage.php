<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <style>
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
                                <li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Item Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        
                        <br>
                        
                        <div class="_buttons">
                            <a href="<?= admin_url('Invoice_items/add_item'); ?>" class="btn btn-info pull-left">
    Add Item
</a>
						</div>
                        <div class="clearfix mbot15"></div>
                        <div class="table-responsive">
                            <!-- dt-table class initializes DataTables which provides pagination, search, etc. -->
                            <table class="table table-bordered table-striped dt-table" id="location_table">
                                <thead>
                                    <tr>
                                        				
                                        <th style="background-color : #50607b; ">Item ID</th>
                                        <th style="background-color : #50607b; " >Item Name</th>
                                        <th style="background-color : #50607b; " >Unit</th>
                                        <th style="background-color : #50607b; " >Main Group</th>
                                        <th style="background-color : #50607b; " >SubGroup1 Name</th>
                                        <th style="background-color : #50607b; " >SubGroup2 Name</th>
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