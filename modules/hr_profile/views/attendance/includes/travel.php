<div class="card-body">

	<div class="row">
		<div class="col-md-12">
			<h4>Travel Distance Report</h4>
			<?php
			/*echo "<pre>";
			print_r($travel_distance);*/
			?>
		</div>
	</div>
	<br>


	<table class="table dt-table">
		<thead>
			<th class="hide"><?php echo _l('ID'); ?></th>
			<th>Date</th>
			<th>Travel Distance</th>
			<!--<th><?php echo _l('hr_status_label'); ?></th>-->
		</thead>
		<tbody>


				<? foreach ($travel_distance as $key => $value) {
					?>
					<tr>
						<td class="hide"></td>
						<td><?php echo $value["travDate"]; ?></td>
						<td><?php echo $value["location_trav"]; ?></td>
						<!--<td></td>-->

					</tr>

			<?php } ?>

		</tbody>
	</table>

	

	

</div>