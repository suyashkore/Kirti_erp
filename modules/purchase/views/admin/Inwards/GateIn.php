<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.table-list { overflow: auto; max-height: 55vh; width:100%; position:relative; top: 0px; }
.table-list thead th { position: sticky; top: 0; z-index: 1; }
.table-list tbody th { position: sticky; left: 0; }
table { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important; font-size:11px; line-height:1.42857143 !important; vertical-align: middle !important;}
th { background: #50607b; color: #fff !important; }
.tableFixHead2 { overflow: auto; max-height: 50vh; }
.sortable, .get_Details { cursor: pointer; }
.total-label-row { display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #eee; }
.total-label-row .total-display { flex: 1; padding: 0; text-align: right; font-weight: 600; }
.fixed-td { max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.get_Details.processing { pointer-events: none; opacity: 0.9; }
@media screen and (max-width: 767px) {
  #header ul { display: none !important; }
}
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
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li><li class="breadcrumb-item active" aria-current="page"><b>Gate In</b></li>
							</ol>
						</nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form" enctype="multipart/form-data">
							<input type="hidden" name="form_mode" id="form_mode" value="add">
							<input type="hidden" name="update_id" id="update_id" value="">
              <div class="row">
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="location_id">
                    <label for="location_id" class="control-label">Location</label>
                    <select name="location_id" id="location_id" class="form-control selectpicker" data-live-search="true" app-field-label="Location" onchange="getGateInNo(this.value);">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($purchaselocation)) :
                        foreach ($purchaselocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="gatein_no">
                    <label for="gatein_no" class="control-label"><small class="req text-danger">* </small> GateIn No</label>
										<input type="text" name="gatein_no" id="gatein_no" class="form-control" app-field-label="GateIn No" required readonly>
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="gatein_date">
                    <?= render_date_input('gatein_date','Gate In Date', date('d/m/Y'), []); ?>
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="vehicle_no">
                    <label for="vehicle_no" class="control-label"><small class="req text-danger">* </small> Vehicle No</label>
										<input type="text" name="vehicle_no" id="vehicle_no" class="form-control" app-field-label="Vehicle No" required title="Examples: 25 BH 1234 AH or MH 01 AB 1234" style="text-transform: uppercase;" onchange="vehicleNoValidation('vehicle_no');">
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="driver_name">
                    <label for="driver_name" class="control-label"><small class="req text-danger">* </small> Driver Name</label>
										<input type="text" name="driver_name" id="driver_name" class="form-control" app-field-label="Driver Name" required placeholder="">
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="phone_no">
                    <label for="phone_no" class="control-label"><small class="req text-danger">* </small> Phone No</label>
										<input type="text" name="phone_no" id="phone_no" class="form-control" app-field-label="Phone No" required placeholder="" onchange="isPhoneNoValid('phone_no');">
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="vehicle_image">
                    <label for="vehicle_image" class="control-label"><small class="req text-danger">* </small> Vehicle Image 
                      <a href="" id="imgLink" target="_blank" style="display: none; font-size: 70%;">( View )</a></label>
                    <input type="file" name="vehicle_image" id="vehicle_image" class="form-control" app-field-label="Vehicle Image" accept="image/*">
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="driver_image">
                    <label for="driver_image" class="control-label"><small class="req text-danger">* </small> Driver Image 
                      <a href="" id="imgLink2" target="_blank" style="display: none; font-size: 70%;">( View )</a></label>
                    <input type="file" name="driver_image" id="driver_image" class="form-control" app-field-label="Driver Image" accept="image/*">
                  </div>
                </div>
                
                <div class="col-md-12">
                  <!-- <a href="#" class="btn btn-primary updateBtn" id="print_pdf" style="display: none;" target="_blank"><i class="fa fa-print"></i> Print PDF</a> -->
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
									<button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
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
  <div class="modal-dialog" role="document" style="width: 80vw;">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Gate In List</h4>
      </div>
      <div class="modal-body" style="padding:5px 5px !important">
        <form action="" method="post" id="filter_list_form">
          <div class="row">
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="from_date">
                <label for="from_date" class="control-label">From Date</label>
                <div class="input-group date">
                  <input type="text" id="from_date" name="from_date" class="form-control datepicker filterInput" value="<?= date("01/m/Y")?>" app-field-label="From Date">
                  <div class="input-group-addon">
                    <i class="fa-regular fa-calendar calendar-icon"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2 mbot5">
              <div class="form-group" app-field-wrapper="to_date">
                <label for="to_date" class="control-label">To Date</label>
                <div class="input-group date">
                  <input type="text" id="to_date" name="to_date" class="form-control datepicker filterInput" value="<?= date("d/m/Y")?>" app-field-label="To Date">
                  <div class="input-group-addon">
                    <i class="fa-regular fa-calendar calendar-icon"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-5 mbot5" style="padding-top: 20px;">
              <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
            </div>
            <div class="col-md-3 mbot5" style="padding-top: 20px;">
              <input type="search" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search..." title="Type in a table">
            </div>
          </div>
        </form>
        <div class="progress" style="margin-bottom: 5px; height: 3px; display: none;">
          <div id="fetchProgress" class="progress-bar" style="width:0%"></div>
        </div>
        <div class="table-ListModal tableFixHead2">
          <table class="tree table table-striped table-bordered table-ListModal tableFixHead2" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortable">Location</th>
                <th class="sortable">Gate In No</th>
                <th class="sortable">Gate In Date</th>
                <th class="sortable">Vechile No</th>
                <th class="sortable">Driver</th>
                <th class="sortable">Phone</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>   
        </div>
        <br>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
  $(document).ready(function() {
    setTimeout(() => {
      $('#filter_list_form').submit();
    }, 800);
  })

	function ResetForm(){
		$('#main_save_form')[0].reset();
		$('#form_mode').val('add');
		$('#update_id').val('');
		$('.updateBtn').hide();
		$('.saveBtn').show();
    $('#location_id').attr('disabled', false);
    $('#imgLink, #imgLink2').attr('href', '').hide();
		$('.selectpicker').selectpicker('refresh');
	}

  $(document).on('input', 'input[type="tel"]', function () {
    this.value = this.value
        .replace(/[^0-9.]/g, '')        // allow digits + dot
        .replace(/(\..*?)\..*/g, '$1'); // allow only one dot
  });

  function getGateInNo(locationId){
    if($('#form_mode').val() == 'edit'){
      return;
    }
    $.ajax({
      url:"<?= admin_url(); ?>purchase/Inwards/GetGateInNo",
      method:"POST",
      dataType:"JSON",
      data: {
        location_id: locationId,
      },
      success: function(response){
        if(response.success == true){
          $('#gatein_no').val(response.data);
        }else{
          $('#gatein_no').val('');
        }
      }
    })
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

  function isPhoneNoValid(phoneNoId){
    let phoneNo = $('#' + phoneNoId).val();
    var phoneno = /^\d{10}$/;
    if (!phoneno.test(phoneNo)) {
      alert_float('warning', 'Please enter a valid Phone No.');
      $('#' + phoneNoId).focus();
      return false;
    }
  }

	function get_required_fields(form_id){
		let fields = [];
		$('#' + form_id + ' [required]').each(function(){
				fields.push($(this).attr('id'));
		});
		return fields;
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

    let phoneNo = isPhoneNoValid('phone_no');
    if(phoneNo === false){
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
			url:"<?= admin_url(); ?>purchase/Inwards/SaveGateIn",
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
					let html = `<tr class="get_Details" data-id="${response.data.id}" onclick="getDetails(${response.data.id})">
            <td>${response.data.LocationName}</td>
            <td class="text-center">${response.data.GateINID}</td>
            <td>${moment(response.data.TransDate).format('DD/MM/YYYY')}</td>
            <td>${response.data.VehicleNo}</td>
            <td>${response.data.DriverName}</td>
            <td>${response.data.DriverMobileNo}</td>
					</tr>`;
					if(form_mode == 'edit'){
						$('.get_Details[data-id="'+response.data.id+'"]').replaceWith(html);
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

	function getDetails(id, row){
    let $row = $(row);
    if ($row.hasClass('processing')) return;
    $row.addClass('processing');

		ResetForm();
		$.ajax({
			url:"<?= admin_url(); ?>purchase/Inwards/GetGateInDetails",
			method:"POST",
			dataType:"JSON",
			data: {
				id: id,
			},
      complete: function(){
        $row.removeClass('processing');
      },
			success: function(response){
				if(response.success == true){
					$('#form_mode').val('edit');
					$('.saveBtn').hide();
					$('.updateBtn').show();
					$('#ListModal').modal('hide');
          $('#location_id').attr('disabled', true);

					let d = response.data;
					$('#update_id').val(id);
          $('#location_id').val(d.LocationID);
          $('#gatein_no').val(d.GateINID);
          $('#gatein_date').val(moment(d.TransDate).format('DD/MM/YYYY'));
          $('#vehicle_no').val(d.VehicleNo);
          $('#driver_name').val(d.DriverName);
          $('#phone_no').val(d.DriverMobileNo);
          if(d.VehicleImage != null){
            $('#imgLink').attr('href', '<?= base_url(); ?>'+d.VehicleImage).show();
          }
          if(d.DriverImage != null){
            $('#imgLink2').attr('href', '<?= base_url(); ?>'+d.DriverImage).show();
          }
          
          $('.selectpicker').selectpicker('refresh');
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	}

  $('#filter_list_form').submit(function(e){
    e.preventDefault();

    let form = this;
    let limit = 1;
    let offset = 0;
    let totalRecords = 0;
    let loadedRecords = 0;
    $('#searchBtn').prop('disabled', true);
    $('#table_ListModal tbody').html('');

    function fetchChunk() {
      var form_data = new FormData(form);
      form_data.append('offset', offset);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );

      $.ajax({
        url: "<?= admin_url('purchase/Inwards/GateInListFilter') ?>",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function(res){
          let json = JSON.parse(res);
          if(!json.success){
            $('#searchBtn').prop('disabled', false);
            if(offset === 0){
              $('#table_ListModal tbody').html(
                '<tr><td colspan="10" class="text-center">No Data Found</td></tr>'
              );
            }
            return;
          }
          if(offset === 0){
            totalRecords = parseInt(json.total) || 0;
          }
          if(json.rows && json.rows.length > 0){
            appendRows(json.rows);
            loadedRecords += json.rows.length;
            offset += limit;
          }
          updateProgress(loadedRecords, totalRecords);
          if(loadedRecords >= totalRecords){
            $('#searchBtn').prop('disabled', false);
            $('#fetchProgress').css('width', '0%')
            return;
          }
          fetchChunk();
          
        }
      });
    }
    fetchChunk();
  });

  function appendRows(rows){
    let html = '';
    rows.forEach(function(row){
      html += `<tr class="get_Details" data-id="${row.id}" onclick="getDetails(${row.id}, this)">
        <td>${row.LocationName}</td>
        <td class="text-center">${row.GateINID}</td>
        <td>${moment(row.TransDate).format('DD/MM/YYYY')}</td>
        <td>${row.VehicleNo}</td>
        <td>${row.DriverName}</td>
        <td>${row.DriverMobileNo}</td>
      </tr>`;
    });
    $('#table_ListModal tbody').append(html);
  }

  function updateProgress(loaded, total){
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%')
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