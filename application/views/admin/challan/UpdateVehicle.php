<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-9">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Change Vehicle</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
						<?php //echo form_open('admin/accounts_master/',array('id'=>'accounting_head')); ?>
						
						<div class="row">
							
							<div class="col-md-3">
								<div class="form-group">
									<label for="block_ac" class="control-label">Select Challan</label>
									<select class="selectpicker" data-width="100%" data-action-box="true" tabindex="-98" name="ChallanID" data-live-search="true" id="ChallanID">
										<option value="">Select Challan</option>
										<?php
											foreach($ChallanList as $chl){
											?>
											<option value="<?php echo $chl["ChallanID"]; ?>"><?php echo $chl["ChallanID"]; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="VehicleNo">Vehicle No</label>
									<input type="text" class="form-control" name="VehicleNo" id="VehicleNo" value="" disabled>
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									<label for="NewVehicleNo">New Vehicle No</label>
									<input type="text" class="form-control" name="NewVehicleNo" id="NewVehicleNo" value="">
								</div>
							</div>
							
							<div class="col-md-3">
								<div class="form-group">
									<label for="DriverName">Driver Name</label>
									<input type="text" class="form-control" name="DriverName" id="DriverName" value="" disabled>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="challan_driver" class="control-label"><small class="req text-danger"> </small>New Driver Name</label>
									<select class="selectpicker" name="challan_driver" id="challan_driver" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<option value=""></option>
										<?php
											foreach ($DriverList as $key => $value) {
											?>
											<option value="<?php echo $value["AccountID"]?>" <?php if(isset($challan) && $challan->DriverID == $value['AccountID']){echo 'selected';} ?>><?php echo $value["firstname"]." ".$value["lastname"];?></option>
											<?php
											}
										?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="row">
							
							<div class="col-md-12">
								<?php if (has_permission('change_vehicle', '', 'edit')) {
								?>
								<button type="button" class="btn btn-info saveBtn" onclick="this.disabled = true;" style="margin-right: 25px;">Update</button>
								<?php
								}?>
							</div>
							
						</div>
						
					</div>
				</div>
				
			</div>
			
		</div>
	</div>
</div>

<?php init_tail(); ?>


<script>
    $(document).ready(function(){
        
        $('#ChallanID').on('change',function(){ 
            ChallanID = $(this).val();
            //alert(ChallanID);
			if(ChallanID == ""){
				$('#NewVehicleNo').val('');
				$('#VehicleNo').val('');
				$('#DriverName').val('');
				$('select[name=challan_driver]').val('');
				$('.selectpicker').selectpicker('refresh');
				}else{
				$.ajax({
					url:"<?php echo admin_url(); ?>Challan/GetVehicleByChallan",
					dataType:"JSON",
					method:"POST",
					data:{ChallanID:ChallanID},
					beforeSend: function () {
						$('.searchh2').css('display','block');
						$('.searchh2').css('color','blue');
					},
					complete: function () {
						$('.searchh2').css('display','none');
					},
					success:function(data){
						$('#VehicleNo').val(data.VehicleID);
						$('#DriverName').val(data.Driver);
					}
				});
			}
		});
        
        // Update Exiting Item
        $('.saveBtn').on('click',function(){ 
            challan_driver = $('#challan_driver').val();
            NewVehicleNo = $('#NewVehicleNo').val();
            ChallanID = $('#ChallanID').val();
            VehicleNo = $('#VehicleNo').val();
            
            if(ChallanID == ""){
                alert('please select Challan');
                $('.saveBtn').removeAttr('disabled');
                $('#ChallanID').focus();
				}else if(NewVehicleNo == '' && challan_driver == ''){
                alert('Please Enter Vehicle No Or Driver Name');
                $('.saveBtn').removeAttr('disabled');
				}else{
                $.ajax({
                    url:"<?php echo admin_url(); ?>Challan/UpdateVehicle",
                    dataType:"JSON",
                    method:"POST",
                    data:{NewVehicleNo:NewVehicleNo,VehicleNo:VehicleNo,ChallanID:ChallanID,challan_driver:challan_driver,
					},
                    beforeSend: function () {
						$('.searchh3').css('display','block');
						$('.searchh3').css('color','blue');
					},
                    complete: function () {
						$('.searchh3').css('display','none');
					},
                    success:function(data){
						if(data == true){
							alert_float('success', 'Record updated successfully...');
							$('#NewVehicleNo').val('');
                            $('#VehicleNo').val('');
                            $('#DriverName').val('');
							$('select[name=ChallanID]').val('');
							$('.selectpicker').selectpicker('refresh');
							$('select[name=challan_driver]').val('');
							$('.selectpicker').selectpicker('refresh');
							$('.saveBtn').removeAttr('disabled');
							}else{
							$('.saveBtn').removeAttr('disabled');
							alert_float('warning', 'Data not updated...');
						}
					}
				});
			}
            
		});
	});
</script>

</body>
</html>