<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table-headquarter          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-headquarter thead th { position: sticky; top: 0; z-index: 1; }
.table-headquarter tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div>
	<!--<div class="_buttons">
		<?php if(is_admin() || has_permission('hrm_hedquarter','','create')) {?>
			<a href="#" onclick="new_headquarter(); return false;" class="btn btn-info pull-left display-block">
				<?php echo _l('hr_hr_add'); ?>
			</a>
		<?php } ?>
	</div>-->
	<?php if(is_admin() || has_permission('hrm_hedquarter','','create')) {?>
	<div class="row">
	    <div class="col-md-12">
	        <h4>Add New Head Quarter</h4>
	    </div>
	</div>
	<div class="clearfix"></div>
	<br>
	
	<?php echo form_open(admin_url('hr_profile/headquarter'), array('id' => 'add_workplace' )); ?>
	<div class="row">
	    <div class="col-md-4">
	        <div class="form-group">
				<label for="state" class="control-label"><?php echo _l('hr_state'); ?></label>
				    <select name="state_id" class="selectpicker" id="state" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
						<option value=""></option>                  
						<?php foreach($state as $s){ ?>

						<option value="<?php echo html_entity_decode($s['short_name']); ?>" <?php if(isset($member) && $member->state_id == $s['id']){echo 'selected';} ?>><?php echo html_entity_decode($s['state_name']); ?></option>

						<?php } ?>
					</select>
			</div>
		</div>
		
		<div class="col-md-4">
	        <div class="form-group">
				<label for="state" class="control-label">Head Quarter Name</label>
				<input type="text" name="name" class="form-control">
			</div>
		</div>
		
		<div class="col-md-2">
		    
		    <div class="form-group">
		    <label for="state" class="control-label" style="margin-bottom: 14px;"></label>
		    <button type="submit" class="btn btn-info form-control"><?php echo _l('submit'); ?></button>
		    
		    </div>
		</div>
			    
	</div>
	<?php echo form_close(); ?>
	<?php } ?>
	    <!--<div class="row">
            <div class="col-md-6">
                <div class="custom_button">&nbsp;&nbsp;
                    <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-headquarter" href="#" id="caexcel"><span>Export to excel</span></a>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                </div>
            </div>
            <div class="col-md-6">
                <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
            </div>
        </div>-->
	    <div class="table-headquarter tableFixHead2">
            <table class="tree table table-striped table-bordered table-headquarter tableFixHead2 " id="table-headquarter" width="100%">
        		<thead>
        		    <th>State Name</th>
        			<th>Head Quarter</th>
        			<!--<th><?php echo _l('hr_workplace_address'); ?></th>
        			<th><?php echo _l('hr_latitude_lable'); ?></th>
        			<th><?php echo _l('hr_longitude_lable'); ?></th>-->
        			<th><?php echo _l('Status'); ?></th>
        			<th><?php echo _l('options'); ?></th>
        		</thead>
        		<tbody>
        			<?php foreach($hedquarter as $w){ ?>
        				<tr>
        				    <td><?php echo get_state_name_by_id($w['state_id']);?></td>
        					<td><?php echo html_entity_decode($w['name']); ?></td>
        					<td><?php if($w['status'] == "1"){ echo "Active"; }else{ echo "Deactive"; }?></td>
        					<!--<td><?php echo html_entity_decode($w['workplace_address']); ?></td>
        					<td><?php echo html_entity_decode($w['latitude']); ?></td>
        					<td><?php echo html_entity_decode($w['longitude']); ?></td>-->
        					<td>
        						<?php if(is_admin() || has_permission('hrm_setting','','edit')) {?>
        							<a href="#" onclick="edit_workplace(this,<?php echo html_entity_decode($w['id']); ?>); return false" data-name="<?php echo html_entity_decode($w['name']); ?>" data-state="<?php echo html_entity_decode($w['state_id']); ?>" data-status="<?php echo html_entity_decode($w['status']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
        						<?php } ?>
        
        						<?php if(is_admin() || has_permission('hrm_setting','','delete')) {?>
        							<a href="<?php echo admin_url('hr_profile/delete_headquarter/'.$w['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
        						<?php } ?>
        					</td>
        				</tr>
        			<?php } ?>
        		</tbody>
	        </table> 
              
        </div>
	       
	<div class="modal" id="new_headquarter" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<?php echo form_open(admin_url('hr_profile/headquarter'), array('id' => 'add_workplace' )); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title">Edit Head Quarter</span>
						<span class="add-title"><?php echo _l('hr_new_workplace'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional_workplace"></div>   
							<div class="form">     
								<?php 
								echo render_input('name','Head Quarter'); ?>
							</div>
						</div>
						<div class="col-md-12">
                	        <div class="form-group">
                				<label for="state" class="control-label"><?php echo _l('hr_state'); ?></label>
                				    <select name="state_id" class="selectpicker" id="state" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                						<option value=""></option>                  
                						<?php foreach($state as $s){ ?>
                
                						<option value="<?php echo html_entity_decode($s['short_name']); ?>" <?php if(isset($member) && $member->state_id == $s['id']){echo 'selected';} ?>><?php echo html_entity_decode($s['state_name']); ?></option>
                
                						<?php } ?>
                					</select>
                			</div>
                		</div>
		                <div class="col-md-12">
		                    <div class="form-group">
                				<label for="status" class="control-label">Status</label>
                				    <select name="status" class="selectpicker" id="status" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                						<option value="1">Active</option>                  
                						<option value="0">Deactive</option>
                					</select>
                			</div>
		                </div>
						<!--<div class="col-md-12">
							<?php echo render_textarea('workplace_address', 'hr_workplace_address') ?>
						</div>
						<div class="col-md-6">

							<?php echo render_input('latitude', 'hr_latitude_lable', '', 'number') ?>
						</div>
						<div class="col-md-6">
							<?php echo render_input('longitude', 'hr_longitude_lable', '', 'number') ?>
						</div>-->

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
<script>
    function edit_workplace(invoker,id){
        'use strict';

        $('#additional_workplace').html('');
        $('#additional_workplace').append(hidden_input('id',id));

        $('#new_headquarter input[name="name"]').val($(invoker).data('name'));
        //$('#new_headquarter select[name="state_id"]').val($(invoker).data('state'));
        $('#new_headquarter select[name=state_id]').val($(invoker).data('state'));
        $('.selectpicker').selectpicker('refresh');
        $('#new_headquarter select[name=status]').val($(invoker).data('status'));
        $('.selectpicker').selectpicker('refresh');

        $('#new_headquarter').modal('show');
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
    }
</script>
</body>
</html>
