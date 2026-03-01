<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.table-list { overflow: auto; max-height: 55vh; width:100%; position:relative; top: 0px; }
.table-list thead th { position: sticky; top: 0; z-index: 1; }
.table-list tbody th { position: sticky; left: 0; }
table { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important; font-size:11px; line-height:1.42857143 !important; vertical-align: middle !important;}
th { background: #50607b; color: #fff !important; }
.sortable { cursor: pointer; }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li><li class="breadcrumb-item active" aria-current="page"><b>Vehicle In</b></li>
							</ol>
						</nav>
            <hr class="hr_style">
            <form action="" method="post" id="main_save_form">
							<input type="hidden" name="form_mode" id="form_mode" value="add">
							<input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
								<div class="col-md-4 mbot5">
                  <div class="form-group" app-field-wrapper="vehicle_in_no">
                    <label for="vehicle_in_no" class="control-label">Vehicle In No</label>
										<input type="text" name="vehicle_in_no" id="vehicle_in_no" class="form-control" app-field-label="Vehicle In No" required readonly value="Auto Generated">
                  </div>
                </div>
								<div class="col-md-4 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_id">
                    <label for="vendor_id" class="control-label">Vendor</label>
                    <select name="vendor_id" id="vendor_id" class="form-control selectpicker" data-live-search="true" app-field-label="Vendor" required>
                      <option value="" selected>None selected</option>
                      <option value="V123">Test Vendor</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
								<div class="col-md-4 mbot5">
                  <div class="form-group" app-field-wrapper="inwards_id">
                    <label for="inwards_id" class="control-label">Inward List</label>
                    <select name="inwards_id" id="inwards_id" class="form-control selectpicker" data-live-search="true" app-field-label="Inwards" required>
                      <option value="" selected>None selected</option>
                      <option value="I123">Test Inward</option>
                      <?php
                      if (!empty($inwards)) :
                        foreach ($inwards as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
								<div class="col-md-4 mbot5">
                  <div class="form-group" app-field-wrapper="vehicle_no">
                    <label for="vehicle_no" class="control-label">Vehicle No</label>
										<input type="text" name="vehicle_no" id="vehicle_no" class="form-control" app-field-label="Vehicle No" title="Examples: 25 BH 1234 AH or MH 01 AB 1234" style="text-transform: uppercase;" required onchange="vehicleNoValidation('vehicle_no');">
                  </div>
                </div>
								<div class="col-md-6 mbot5">
                  <div class="form-group" app-field-wrapper="date_time">
                    <label for="date_time" class="control-label">In Date Time</label>
										<input type="datetime-local" name="date_time" id="date_time" class="form-control" app-field-label="In Date Time" required>
                  </div>
                </div>
                <div class="col-md-12 mbot5">
                  <button type="submit" class="btn btn-success saveBtn <?php echo (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
									<button type="submit" class="btn btn-success updateBtn <?php echo (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
									<button type="button" class="btn btn-danger" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
									<button type="button" class="btn btn-info" onclick="$('#ListModal').modal('show');"><i class="fa fa-list"></i> Show List</button>
                </div>
              </div>
            </form>
          </div>
        </div>  
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Vehicle In From <?= date('01-m-Y')?> | <a href="<?= admin_url('purchase/Vehiclein/Premises');?>" style="font-size: 12px">View More</a></h4>
      </div>
      <div class="modal-body" style="padding:0px 5px !important">
        
        <div class="table-ListModal tableFixHead2">
          <table class="tree table table-striped table-bordered table-ListModal tableFixHead2" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortablePop">Vendor</th>
                <th class="sortablePop">Inward</th>
                <th class="sortablePop">Vehicle No</th>
                <th class="sortablePop">In Date Time</th>
              </tr>
            </thead>
            <tbody>
              <?php
							if(!empty($item_list)):
								foreach ($item_list as $key => $value):
								?>
								<tr class="get_Details" data-id="<?= $value["id"]; ?>" onclick="getDetails(<?= $value['id']; ?>)">
									<td><?= $value["ItemID"];?></td>
									<td><?= $value["ItemName"];?></td>
									<td><?= $value["ItemName"];?></td>
									<td><?= ($value["IsActive"] == 'Y') ? 'Yes' : 'No';?></td>
								</tr>
								<?php
								endforeach;
							endif;
              ?>
            </tbody>
          </table>   
        </div>
      </div>
      <div class="modal-footer" style="padding:0px;">
        <input type="text" id="myInput1"  name='myInput1' onkeyup="myFunction2()" placeholder="Search for names.."  style="float: left;width: 100%;">
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
	function ResetForm(){
		$('#main_save_form')[0].reset();
		$('#form_mode').val('add');
		$('#update_id').val('');
		$('.updateBtn').hide();
		$('.saveBtn').show();
		$('.selectpicker').selectpicker('refresh');
	}
	
	function validate_fields(fields){ 
		let data = {};
		for(let i = 0; i < fields.length; i++){
			let value = $('#' + fields[i]).val();

			if(value === '' || value === null){
				let label = fields[i].replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
				alert_float('warning', 'Please enter ' + label);
				$('#'+fields[i]).focus();
				return false;
			} else {
				data[fields[i]] = value.trim();
			}
		}
		return data;
	}

	function get_required_fields(form_id){
		let fields = [];
		$('#' + form_id + ' [required]').each(function(){
				fields.push($(this).attr('id'));
		});
		return fields;
	}

	function vehicleNoValidation(vehicleNoId){
		let vehicleNo = $('#' + vehicleNoId).val().toUpperCase().trim();
    vehicleNo = vehicleNo.replace(/\s+/g, ' ');

		var bhRegex = /^[0-9]{2}\s?BH\s?[0-9]{4}\s?[A-Z]{1,2}$/;
		var rtoRegex = /^[A-Z]{2}\s?[0-9]{2}\s?[A-Z]{1,2}\s?[0-9]{4}$/;

		if (!bhRegex.test(vehicleNo) && !rtoRegex.test(vehicleNo)) {
			alert_float('warning', 'Please enter a valid Vehicle No. Examples: 25 BH 1234 AH or MH 01 AB 1234');
			$('#' + vehicleNoId).focus();
			return false;
		}
	}
	
	$('#main_save_form').on('submit', function(e) {
		e.preventDefault();

		let form_mode = $('#form_mode').val();
		
		let required_fields = get_required_fields('main_save_form');
		let validated = validate_fields(required_fields);

		if(validated === false){
			return;
		}
		let vehicleNo = vehicleNoValidation('vehicle_no');

		if(vehicleNo === false){
			return;
		}
		
		var form_data = new FormData(this);
		form_data.append(
			'<?= $this->security->get_csrf_token_name(); ?>',
			$('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		);
		if(form_mode == 'edit'){
			form_data.append('update_id', $('#update_id').val());
		}

		$.ajax({
			url:"<?= admin_url(); ?>purchase/Vehiclein/SaveVehicleIn",
			method:"POST",
			dataType:"JSON",
			data:form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function () {
				$('button[type=submit]').attr('disabled', true);
			},
			complete: function () {
				$('button[type=submit]').attr('disabled', false);
			},
			success: function(response){
				if(response.success == true){
					alert_float('success', response.message);
					let html = `<tr class="get_Details" data-id="${response.data.update_id}" onclick="getDetails(${response.data.update_id})">
						<td>${response.data.vendor_id}</td>
						<td>${response.data.inwards_id}</td>
						<td>${response.data.vehicle_no}</td>
						<td>${response.data.date_time}</td>
					</tr>`;
					if(form_mode == 'edit'){
						$('.get_Details[data-id="'+response.data.update_id+'"]').replaceWith(html);
					}else{
						$('#table_ListModal tbody').prepend(html);
					}
					ResetForm();
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	});

	function getDetails(id){
		ResetForm();
		$.ajax({
			url:"<?= admin_url(); ?>purchase/Vehiclein/GetVehicleInDetails",
			method:"POST",
			dataType:"JSON",
			data: {
				id: id,
			},
			success: function(response){
				if(response.success == true){
					let d = response.data;
					$('#vendor_id').val(d.VendorID);
					$('#inwards_id').val(d.InwardsID);
					$('#vehicle_no').val(d.VehicleNo);
					$('#date_time').val(d.DateTime);

					$('.selectpicker').selectpicker('refresh');
					$('#form_mode').val('edit');
					$('#update_id').val(id);
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('#ListModal').modal('hide');
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	}
</script>
<script>
  $(document).on("click", ".sortable", function () {
		var table = $("#table_ListModal tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		$(".sortable").removeClass("asc desc");
		$(".sortable span").remove();
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		rows.sort(function (a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();
			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
				} else {
				return ascending ? valA.localeCompare(valB) : valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});

  function myFunction2() {
    var input = document.getElementById("myInput1");
    var filter = input.value.toUpperCase();

    var table = document.getElementById("table_ListModal");
    var tbody = table.getElementsByTagName("tbody")[0];
    var tr = tbody.getElementsByTagName("tr");

    for (var i = 0; i < tr.length; i++) {
      var tds = tr[i].getElementsByTagName("td");
      var rowMatch = false;

      for (var j = 0; j < tds.length; j++) {
        var txtValue = tds[j].textContent || tds[j].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          rowMatch = true;
          break;
        }
      }
      tr[i].style.display = rowMatch ? "" : "none";
    }
  }
</script>