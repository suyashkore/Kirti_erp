<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="panel_s">
          <div class="panel-body">
		  <nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Stack Master</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new Stack...</div>
                        <div class="searchh4" style="display:none;">Please wait update Stack...</div>
                    </div>
                    <br>
                    <div class="col-md-3">
							<div class="form-group" app-field-wrapper="StackCode">
                            <small class="req text-danger">* </small>
                            <label for="StackCode" class="control-label">Stack Code</label>
                            <input type="text" id="StackCode" name="StackCode" class="form-control" value="" oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '')" required>
                        </div>
                    </div>
                    <div class="col-md-3">
								<div class="form-group">
                                    <small class="req text-danger">* </small>
									<label for="StackName">Stack Name</label>
									<input type="text" name="StackName" id="StackName" class="form-control" value="" required>
									<input type="hidden" name="form_mode" id="form_mode" value="add">
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
                                        <small class="req text-danger">* </small>
									<label for="GodownName" class="control-label">Godown Name</label>
									<select class="selectpicker display-block" data-width="100%" id="GodownName" name="GodownName" data-live-search="true" required>
										<option value="" disabled selected>-- Select Godown Name --</option>
										<?php foreach ($godown as $gd) { ?>
										<option value="<?php echo $gd['id']; ?>">
                                            <?php echo $gd['GodownName']; ?>
                                        </option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
                                        <small class="req text-danger">* </small>
									<label for="ChamberName" class="control-label">Chamber Name</label>
									<select class="selectpicker display-block" data-width="100%" id="ChamberName" name="ChamberName" data-live-search="true" required>
										<option value="" disabled selected>-- Select Chamber Name --</option>
										<?php foreach ($chamber as $ch) { ?>
										<option value="<?php echo $ch['id']; ?>">
                                            <?php echo $ch['ChamberName']; ?>
                                        </option>
										<?php } ?>
									</select>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
									<label for="length" class="control-label">Length (cm)</label>
									<input type="text" id="length" name="length" class="form-control" onkeypress="return isNumber(event)">
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
									<label for="Width" class="control-label">Width (cm)</label>
									<input type="text" id="Width" name="Width" class="form-control" onkeypress="return isNumber(event)">
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
									<label for="Height" class="control-label">Height (cm)</label>
									<input type="text" id="Height" name="Height" class="form-control" onkeypress="return isNumber(event)">
								</div>
							</div><div class="col-md-3">
								<div class="form-group">
									<label for="Margin" class="control-label">Margin (cm)</label>
									<input type="text" id="Margin" name="Margin" class="form-control" onkeypress="return isNumber(event)">
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
									<label for="TotalArea" class="control-label">Total Area (cm²)</label>
									<input type="text" id="TotalArea" name="TotalArea" class="form-control" readonly>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
									<label for="UtilizeArea" class="control-label">Utilize Area (cm²)</label>
									<input type="text" id="UtilizeArea" name="UtilizeArea" class="form-control" readonly>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
									<label for="volume" class="control-label">Volume (cm³)</label>
									<input type="text" id="volume" name="volume" class="form-control" readonly>
								</div>
							</div>
                            <div class="col-md-3">
								<div class="form-group">
									<label for="Capacity" class="control-label">Capacity</label>
									<input type="text" id="Capacity" name="Capacity" class="form-control" readonly>
								</div>
							</div>
                    <div class="col-md-3">
                        <div class="form-group" app-field-wrapper="IsActive">
                            <label for="IsActive" class="control-label">Is Active ?</label>
                            <select id="IsActive" class="form-control selectpicker" data-live-search="true">
								<option value="Y">Yes</option>
                                <option value="N">No</option>
							</select>
                        </div>
                    </div>                    
                    <div class="clearfix"></div>
                    <br><br>
                    <div class="col-md-12">
                                <div class="action-buttons text-left">
                                    <?php if (has_permission('Stack', '', 'create')) {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
                                    <?php
                                    } else {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
                                    <?php
                                    } ?>

                                    <?php if (has_permission('Stack', '', 'edit')) {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom updateBtn"><i class="fa fa-save"></i> Update</button>
                                    <?php
                                    } else {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom updateBtn2 disabled"><i class="fa fa-save"></i> Update</button>
                                    <?php
                                    } ?>



                                    <button type="reset" class="btn btn-warning cancelBtn">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>

                                    <button type="button" class="btn btn-info showListBtn" id="btnShowItemDivisionList">
                                        <i class="fa fa-list"></i> Show List
                                    </button>
                                </div>
                            </div>
                </div>
                
                <div class="clearfix"></div>
            <!-- Iteme List Model-->
            
                <div class="modal fade StackMaster_List" id="StackMaster_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Stack Master List</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                            
                            <div class="table-StackMaster_List tableFixHead2">
                                <table class="tree table table-striped table-bordered table-StackMaster_List tableFixHead2" id="table_StackMaster_List" width="100%">
                                    <thead>
                                        <tr>
                                            <th  class="sortablePop" style="text-align:left;">Stack Code </th>
                                            <th  class="sortablePop" style="text-align:left;">Stack Name</th>
                                            <th  class="sortablePop" style="text-align:left;">Godown Name</th>
                                            <th  class="sortablePop" style="text-align:left;">Chamber Name</th>
                                            <th  class="sortablePop" style="text-align:left;">Length</th>
                                            <th  class="sortablePop" style="text-align:left;">Width</th>
                                            <th  class="sortablePop" style="text-align:left;">Height</th>
                                            <th  class="sortablePop" style="text-align:left;">Margin</th>
                                            <th  class="sortablePop" style="text-align:left;">Total Area</th>
                                            <th  class="sortablePop" style="text-align:left;">Utilize Area</th>
                                            <th  class="sortablePop" style="text-align:left;">volume</th>
                                            <th  class="sortablePop" style="text-align:left;">Capacity
                                            <th  class="sortablePop" style="text-align:left;">Is Active ?</th>

                                        </tr>
                                    </thead>
                                    <tbody id="StackMastertableBody">
                                    <?php
                                    foreach ($table_data as $key => $value) {
                                    ?>
                                        <tr class="get_StackMaster" data-id="<?php echo $value["StackCode"]; ?>">
                                           <td><?php echo $value["StackCode"]; ?></td>
                                           <td><?php echo $value["StackName"]; ?></td>
                                           <td><?php echo $value["GodownName"]; ?></td>
                                           <td><?php echo $value["ChamberName"]; ?></td>
											<td><?php echo $value["length"]; ?></td>
											<td><?php echo $value["width"]; ?></td>
											<td><?php echo $value["height"]; ?></td>
											<td><?php echo $value["margin"]; ?></td>
											<td><?php echo $value["total_area"]; ?></td>
											<td><?php echo $value["utilize_area"]; ?></td>
											<td><?php echo $value["volume"]; ?></td>
											<td><?php echo $value["capacity"]; ?></td>
                                            <td><?= $value['IsActive'] == 'Y' ? 'Yes' : 'No'; ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>   
                            </div>
                        </div>
                        <div class="modal-footer" style="padding:0px;">
                            <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
                        </div>
                        </div>
                    <!-- /.modal-content -->
                    </div>
                <!-- /.modal-dialog -->
                </div>
            <!-- /.modal -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>

<script>

function calculateChamberValues() {

    let length  = parseFloat($('#length').val()) || 0;
    let width   = parseFloat($('#Width').val()) || 0;
    let height  = parseFloat($('#Height').val()) || 0;
    let margin  = parseFloat($('#Margin').val()) || 0;

    // Total Area
    let totalArea = length * width;

    // Utilize Area
    let utilizeArea = totalArea - margin;
    if (utilizeArea < 0) utilizeArea = 0;

    // volume
    let volume = length * width * height;

    // Capacity
    let capacity = volume;

    // Set values (fixed to 2 decimals)
    $('#TotalArea').val(totalArea.toFixed(2));
    $('#UtilizeArea').val(utilizeArea.toFixed(2));
    $('#volume').val(volume.toFixed(2));
    $('#Capacity').val(capacity.toFixed(2));
}

$('#length, #Width, #Height, #Margin').on('input', function () {
    calculateChamberValues();
});

    $(document).ready(function(){
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
        $("#StackCode").dblclick(function(){
            $('#StackMaster_List').modal('show');
            $('#StackMaster_List').on('shown.bs.modal', function () {
              $('#myInput1').focus();
            })
        });
        
    // Cancel selected data
        $(".cancelBtn").click(function(){
            $('#StackCode').val('');
            $('#StackName').val('');
            $('#GodownName').val('').selectpicker('refresh');
            $('#ChamberName').val('').selectpicker('refresh');
			$('#length').val('');
            $('#Width').val('');
            $('#Height').val('');
            $('#Margin').val('');
            $('#TotalArea').val('');
            $('#UtilizeArea').val('');
            $('#volume').val('');
            $('#Capacity').val('');
            $('#IsActive').val('Y').selectpicker('refresh');

            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            
        });
        
    // On Blur Chamber Code Get All Data
    $('#StackCode').on('blur', function () {

    let StackCode = $(this).val().trim();

    // =========================
    // EMPTY → RESET FORM
    // =========================
    if (StackCode === '') {

        $('#StackName').val('');
        $('#GodownName').val('').selectpicker('refresh');
        $('#ChamberName').val('').selectpicker('refresh');
		$('#length').val('');
        $('#Width').val('');
        $('#Height').val('');
        $('#Margin').val('');
        $('#TotalArea').val('');
        $('#UtilizeArea').val('');
        $('#volume').val('');
        $('#Capacity').val('');
        $('#IsActive').val('Y').selectpicker('refresh');

        $('.saveBtn, .saveBtn2').show();
        $('.updateBtn, .updateBtn2').hide();
        return;
    }

    // =========================
    // FETCH STACK DETAILS
    // =========================
    $.ajax({
        url: "<?php echo admin_url(); ?>Stack/GetStackMasterDetailByID",
        dataType: "JSON",
        method: "POST",
        data: { StackCode: StackCode },

        beforeSend: function () {
            $('.searchh2').show().css('color', 'blue');
        },
        complete: function () {
            $('.searchh2').hide();
        },

        success: function (data) {

            // =========================
            // NOT FOUND → SAVE MODE
            // =========================
            if (!data || $.isEmptyObject(data)) {

                $('#StackName').val('');
                $('#GodownName').val('').selectpicker('refresh');
                $('#ChamberName').val('').selectpicker('refresh');
                $('#length').val('');
                $('#Width').val('');
                $('#Height').val('');
                $('#Margin').val('');
                $('#TotalArea').val('');
                $('#UtilizeArea').val('');
                $('#volume').val('');
                $('#Capacity').val('');
                $('#IsActive').val('Y').selectpicker('refresh');

                $('.saveBtn, .saveBtn2').show();
                $('.updateBtn, .updateBtn2').hide();
            }
            // =========================
            // FOUND → UPDATE MODE
            // =========================
            else {

                $('#StackCode').val(data.StackCode);
                $('#StackName').val(data.StackName);
                $('#GodownName').val(data.GodownName).selectpicker('refresh');
                $('#ChamberName').val(data.ChamberName).selectpicker('refresh');
                $('#length').val(data.Length);
                $('#Width').val(data.Width);
                $('#Height').val(data.Height);
                $('#Margin').val(data.Margin);
                $('#TotalArea').val(data.TotalArea);
                $('#UtilizeArea').val(data.UtilizeArea);
                $('#volume').val(data.volume);
                $('#Capacity').val(data.Capacity);
                $('#IsActive').val(data.IsActive).selectpicker('refresh');


                $('.saveBtn, .saveBtn2').hide();
                $('.updateBtn, .updateBtn2').show();
            }
        }
    });
});

        
        $(document).on('click', '.get_StackMaster', function () {
            StackCode = $(this).attr("data-id");
            $.ajax({
                url:"<?php echo admin_url(); ?>Stack/GetStackMasterDetailByID",
                dataType:"JSON",
                method:"POST",
                data:{StackCode:StackCode},
                beforeSend: function () {
                $('.searchh2').css('display','block');
                $('.searchh2').css('color','blue');
                },
                complete: function () {
                $('.searchh2').css('display','none');
                },
                success:function(data){
                        $('#StackCode').val(data.StackCode);
                        $('#StackName').val(data.StackName);
                        $('#GodownName').val(data.GodownName).selectpicker('refresh');
                        $('#ChamberName').val(data.ChamberName).selectpicker('refresh');
                        $('#length').val(data.Length);
                        $('#Width').val(data.Width);
                        $('#Height').val(data.Height);
                        $('#Margin').val(data.Margin);
                        $('#TotalArea').val(data.TotalArea);
                        $('#UtilizeArea').val(data.UtilizeArea);
                        $('#volume').val(data.volume);
                        $('#Capacity').val(data.Capacity);
                        $('#IsActive').val(data.IsActive).selectpicker('refresh');

                       
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                }
            });
            $('#StackMaster_List').modal('hide');
        });
        
    // Save New ItemDivision
        $('.saveBtn').on('click',function(){ 
            StackCode = $('#StackCode').val();
            StackName = $('#StackName').val();
            GodownName = $('#GodownName').val();
            ChamberName = $('#ChamberName').val();
			length = $('#length').val();
            Width = $('#Width').val();
            Height = $('#Height').val();
            Margin = $('#Margin').val();
            TotalArea =  length * Width;
            UtilizeArea = TotalArea - Margin;
            volume = length * Width * Height;
            Capacity = length * Width * Height;
            IsActive = $('#IsActive').val();

            
            $.ajax({
                url:"<?php echo admin_url(); ?>Stack/SaveStackMaster",
                dataType:"JSON",
                method:"POST",
                data:{StackCode:StackCode,
				StackName:StackName,
                GodownName:GodownName,
                ChamberName:ChamberName,
                length: length,
				Width:Width,
                Height:Height,
                Margin:Margin,
                TotalArea:TotalArea,
                Volume:volume,
                Capacity:Capacity,
                UtilizeArea:UtilizeArea,
                IsActive:IsActive
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
                       
                       alert_float('success', 'Record created successfully...');
                        $('#StackCode').val('');
                        $('#StackName').val('');
                        $('#GodownName').val('').selectpicker('refresh');
                        $('#ChamberName').val('').selectpicker('refresh');
                        $('#length').val('');
                        $('#Width').val('');
                        $('#Height').val('');
                        $('#Margin').val('');
                        $('#TotalArea').val('');
                        $('#UtilizeArea').val('');
                        $('#volume').val('');
                        $('#Capacity').val('');
                        $('#IsActive').val('Y').selectpicker('refresh');
                       
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                         refreshItemDivisionList();
                   }else{
                       alert_float('warning', data.message || 'Something went wrong...');
                   }
                //    location.reload();
                }
            });
        });
    // Update Exiting Item Division
        $('.updateBtn').on('click',function(){ 
            StackCode = $('#StackCode').val();
            StackName = $('#StackName').val();
            GodownName = $('#GodownName').val();
            ChamberName = $('#ChamberName').val();
            length = $('#length').val();
            Width = $('#Width').val();
            Height = $('#Height').val();
            Margin = $('#Margin').val();
            TotalArea =  length * Width;
            UtilizeArea = TotalArea - Margin;
            volume = length * Width * Height;
            Capacity = length * Width * Height;
            IsActive = $('#IsActive').val();
            
            $.ajax({
                url:"<?php echo admin_url(); ?>Stack/UpdateStackMaster",
                dataType:"JSON",
                method:"POST",
                data:{StackCode:StackCode,
				StackName:StackName,
                ChamberName:ChamberName,
                GodownName:GodownName,
                length: length,
				Width:Width,
                Height:Height,
                Margin:Margin,
                TotalArea:TotalArea,
                Volume:volume,
                Capacity:Capacity,
                UtilizeArea:UtilizeArea,
                IsActive:IsActive
                },
                beforeSend: function () {
                $('.searchh4').css('display','block');
                $('.searchh4').css('color','blue');
                },
                complete: function () {
                $('.searchh4').css('display','none');
                },
                success:function(data){
                   if(data == true){
                       alert_float('success', 'Record updated successfully...');
                        $('#StackCode').val('');
                        $('#StackName').val('');
                        $('#GodownName').val('').selectpicker('refresh');
                        $('#ChamberName').val('').selectpicker('refresh');
						$('#length').val('');
                        $('#Width').val('');
                        $('#Height').val('');
                        $('#Margin').val('');
                        $('#TotalArea').val('');
                        $('#UtilizeArea').val('');
                        $('#volume').val('');
                        $('#Capacity').val('');
                        $('#IsActive').val('Y').selectpicker('refresh'); 

                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                         refreshItemDivisionList();
                   }else{
                       alert_float('warning', data.message || 'Something went wrong...');
                   }
                }
            });
        });
    });
    $('.showListBtn').on('click', function () {
    $('#StackMaster_List').modal('show');

    $('#StackMaster_List').on('shown.bs.modal', function () {
        $('#myInput1').focus();
    });
});
$('#ChamberCode').on('click', function () {
    $('.cancelBtn').trigger('click');
});

</script>

<script>
     function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_StackMaster_List");
  tr = table.getElementsByTagName("tr");
   for (i = 1; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td1){
         txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else{
           tr[i].style.display = "none";
      } 
    }   
  }
}
}

$(document).on("click", ".sortablePop", function () {
		var table = $("#table_StackMaster_List tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop").removeClass("asc desc");
		$(".sortablePop span").remove();
		
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		
		rows.sort(function (a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();
			
			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
				} else {
				return ascending
                ? valA.localeCompare(valB)
                : valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});
 </script>
<script>
    function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode = 46 && charCode > 31 
            && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
    }

    function refreshItemDivisionList() {
    $('#StackMastertableBody').load(location.href + ' #StackMastertableBody > *');
}


</script>

<style>

#item_code1 {
    text-transform: uppercase;
}
#table_StackMaster_List td:hover {
    cursor: pointer;
}
#table_StackMaster_List tr:hover {
    background-color: #ccc;
}

    .table-StackMaster_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-StackMaster_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-StackMaster_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
    #StackCode {
		text-transform: uppercase;
	}
    #StackName {
		text-transform: uppercase;
	}
</style>