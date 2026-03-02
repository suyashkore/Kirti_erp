<div class="card-body">

	<div class="row">
		<div class="col-md-12">
			<h4><?php echo _l('hr_hr_company_training'); ?></h4>
		</div>
	</div>
	<br>


	<table class="table dt-table">
		<thead>
			<th class="hide"><?php echo _l('ID'); ?></th>
			<th><?php echo _l('name'); ?></th>
			<th><?php echo _l('hr_training_result'); ?></th>
			<th><?php echo _l('hr_status_label'); ?></th>
		</thead>
		<tbody>

			<?php if(isset($staff_training_result)){ ?>

				<tr>
					<td class="hide">0</td>
					<td><b><?php echo html_entity_decode($list_training_allocation->training_name); ?></b></td>

					<td>
						<?php
						if((int)$list_training_allocation->training_type == 1){
							echo _l('hr_basic_training'); 
						}
						if((int)$list_training_allocation->training_type == 2){
							echo _l('hr_professiona_training'); 
						}
						if((int)$list_training_allocation->training_type == 3){
							echo _l('hr_skill_training'); 
						}
						if((int)$list_training_allocation->training_type == 4){
							echo _l('hr_management_training'); 
						}

						echo ': '.html_entity_decode($training_program_point) .'/'.html_entity_decode($training_allocation_min_point) ;
						?>
					</td>
					<td>
						<?php 
						if($complete == 0){
							echo ' <span class="label label-success "> '._l('hr_complete').' </span>';
						}else{
							echo ' <span class="label label-primary"> '._l('hr_not_yet_complete').' </span>';
						}
						 ?>
					</td>
				</tr>

				<?foreach ($staff_training_result as $key => $value) {
					?>
					<tr>
						<td class="hide"><?php echo html_entity_decode($key+1); ?></td>
						<td><a href="<?php echo admin_url('hr_profile/participate/index/'.$value['training_id'].'/'.hr_get_training_hash($value['training_id'])); ?>"><?php echo '&nbsp;&nbsp;&nbsp;+'. html_entity_decode($value['training_name']); ?></a></td>
						<td><?php echo _l('hr_point').': '. html_entity_decode($value['total_point']).'/'. html_entity_decode($value['total_question_point']); ?></td>
						<td></td>

					</tr>

			<?php }} ?>

		</tbody>
	</table>

	<div class="row">
		<div class="col-md-12">
			<h4><?php echo _l('hr_hr_more_training'); ?></h4>
		</div>
	</div>
	<br>

	<?php 
	if(has_permission('hrm_dependent_person', '', 'create') || ($member->staffid == get_staff_user_id())){
		?>
		<button class="btn btn-info" type="button" onclick="create_trainings();"><?php echo _l('hr_more_training_sessions'); ?></button>
	<?php } ?>
	<div class="clearfix"></div>
	<br>

	<div class="card-body">
		<?php
		$table_data = array(
			_l('hr_training_programs_name'),
			_l('hr_hr_training_places'),
			_l('hr_time_to_start'),
			_l('hr_time_to_end'),
			_l('hr_training_result'),
			_l('hr_degree'),
			_l('hr_notes'),                             
		);
		render_datatable($table_data,'table_education',array(), array('data-page-length' => '10'));
		?>
	</div>

	<div class="modal fade" id="education_sidebar" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title-training"><?php echo _l('hr_update_training_sessions'); ?></span>
						<span class="add-title-training"><?php echo _l('hr_more_training_sessions'); ?></span>
					</h4>
				</div>
				<?php echo form_open_multipart(admin_url('hr_profile/save_update_education'),array('class'=>'save_update_education')); ?>
				<div class="modal-body">
					<input type="hidden" name="id" value="">
					<div class="row">
						<div class="col-md-12">
							<?php echo render_input('training_programs_name','hr_training_programs_name','','text'); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<?php echo render_input('training_places','hr_hr_training_places','','text'); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 pl-0">
							<?php  echo render_datetime_input('training_time_from','hr_time_to_start',''); ?>
						</div>
						<div class="col-md-6 pr-0">
							<?php  echo render_datetime_input('training_time_to','hr_time_to_end',''); ?>
						</div>
					</div>                 
					<div class="row">       
						<div class="col-md-12">
							<?php echo render_textarea('training_result','hr_training_result','',array(),array(),'','tinymce'); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<?php echo render_input('degree','hr_degree','','text'); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<?php 
							echo render_textarea('notes','hr_notes','');
							?> 
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
				<?php echo form_close(); ?>                 
			</div>
		</div>
	</div>

</div>