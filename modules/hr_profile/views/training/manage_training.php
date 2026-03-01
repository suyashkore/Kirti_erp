<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php if($this->session->flashdata('debug')){ ?>
				<div class="col-lg-12">
					<div class="alert alert-warning">
						<?php echo html_entity_decode($this->session->flashdata('debug')); ?>
					</div>
				</div>
			<?php } ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="horizontal-scrollable-tabs  mb-5">
							<div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
							<div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>

							<div class="horizontal-tabs mb-4">
								<ul class="nav nav-tabs nav-tabs-horizontal">
									<?php
									foreach($tab as $key =>  $group){
										?>
										<li class="<?php if($key == 0){echo 'active';} ?>">
										<a href="<?php echo admin_url('hr_profile/training?group='.$group); ?>" data-group="<?php echo html_entity_decode($group); ?>">
											<?php 
											if($group == 'training_library'){
												echo _l('hr__training_library'); 
											}elseif($group == 'training_program'){
												echo _l('hr__training_program');
											}

											?></a>

										</li>
										<?php } ?>
									</ul>
								</div>

								<?php $this->load->view($tabs['view']); ?>

							</div>
						</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
		<?php init_tail(); ?>
		
		<?php hooks()->do_action('settings_tab_footer', $tab); ?>

		<?php 
			$viewuri = $_SERVER['REQUEST_URI'];
			if(!(strpos($viewuri,'admin/hr_profile/training?group=training_program') === false) || !(strpos($viewuri,'admin/hr_profile/training') === false)){
				require('modules/hr_profile/assets/js/training/training_program_js.php');
			}
		 ?>
	</body>
	</html>
