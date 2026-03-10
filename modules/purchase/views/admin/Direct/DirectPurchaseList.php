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
.fixed-td { max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li><li class="breadcrumb-item active" aria-current="page"><b>Direct Order List</b></li>
							</ol>
						</nav>
            <hr class="hr_style">
            <form action="" method="post" id="filter_list_form">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="from_date">
                    <?= render_date_input('from_date','From Date', date('01/m/Y'), []); ?>
                  </div>
                  <!-- <div class="form-group" app-field-wrapper="from_date">
                    <label for="from_date" class="control-label">From Date</label>
                    <div class="input-group date">
                      <input type="text" id="from_date" name="from_date" class="form-control datepicker filterInput" value="<?= date("01/m/Y")?>" app-field-label="From Date" onchange="resetForm();">
                      <div class="input-group-addon">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                      </div>
                    </div>
                  </div> -->
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="to_date">
                    <?= render_date_input('to_date','To Date', date('d/m/Y'), []); ?>
                  </div>
                  <!-- <div class="form-group" app-field-wrapper="to_date">
                    <label for="to_date" class="control-label">To Date</label>
                    <div class="input-group date">
                      <input type="text" id="to_date" name="to_date" class="form-control datepicker filterInput" value="<?= date("d/m/Y")?>" app-field-label="To Date" onchange="resetForm();">
                      <div class="input-group-addon">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                      </div>
                    </div>
                  </div> -->
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="center_location">
                    <label for="center_location" class="control-label">Center Location</label>
                    <select name="center_location" id="center_location" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Center Location" onchange="getGodownList(this.value); resetForm();">
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
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="warehouse_id">
                    <label for="warehouse_id" class="control-label">Warehouse</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Warehouse" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="purchase_type">
                    <label for="purchase_type" class="control-label">Purchase Type</label>
                    <select name="purchase_type" id="purchase_type" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Purchase Type" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($item_type)) :
                        foreach ($item_type as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['ItemTypeName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_id">
                    <label for="vendor_id" class="control-label">Vendor</label>
                    <select name="vendor_id" id="vendor_id" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Vendor" onchange="getVendorDetailsLocation(); resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($vendor_list)) :
                        foreach ($vendor_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="broker_id">
                    <label for="broker_id" class="control-label">Broker</label>
                    <select name="broker_id" id="broker_id" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Broker" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-7 mbot5" style="padding-top: 20px;">
                  <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
                  <button type="button" class="btn btn-info exportBtn" onclick="exportTableToExcel()" style="display: none;"><i class="fa fa-file-excel"></i> Excel</button>
                  <button type="button" class="btn btn-info exportBtn" onclick="printPage();" style="display: none;"><i class="fa fa-print"></i> Print</button>
                </div>
                <div class="col-md-3 mbot5" style="padding-top: 20px;">
                  <input type="search" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search..." title="Type in a table">
                </div>
              </div>
            </form>
            <div class="progress" style="margin-bottom: 5px; height: 3px;">
              <div id="fetchProgress" class="progress-bar" style="width:0%"></div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="table-list" id="printableArea">
                  <table class="table table-striped table-bordered table-list" id="table-list" width="100%">
                    <thead>
                      <tr class="mainHead" style="display: none;">
                        <td colspan="8">
                          <h5 style="text-align:center;">
                            <span style="font-size:15px; font-weight:700;"><?= $company_detail->company_name ?? ''; ?></span><br>
                            <span style="font-size:10px; font-weight:600;"><?= $company_detail->address ?? ''; ?></span>
                          </h5>
                        </td>
                      </tr>
                      <tr class="mainHead" style="display: none;">
                        <td colspan="8">
                          <span class="report_for" style="font-size:10px;"></span>
                        </td>
                      </tr>
                      <tr>
                        <th class="sortable">Order No</th>
                        <th class="sortable">Order Date</th>
                        <th class="sortable">Center Location</th>
                        <th class="sortable">Warehouse</th>
                        <th class="sortable">Vendor</th>
                        <th class="sortable">Broker</th>
                        <th class="sortable">Type</th>
                        <th class="sortable">TDS Section</th>
                        <th class="sortable">TDS Rate</th>
                        <th class="sortable">Total Amt</th>
                        <th class="sortable">Disc Amt</th>
                        <th class="sortable">CGST Amt</th>
                        <th class="sortable">SGST Amt</th>
                        <th class="sortable">IGST Amt</th>
                        <th class="sortable">Freight Amt</th>
                        <th class="sortable">Other Amt</th>
                        <th class="sortable">Raound Off</th>
                        <th class="sortable">TDS Amt</th>
                        <th class="sortable">Final Amt</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>   
                </div>
              </div>
            </div>
          </div>
        </div>  
      </div>
    </div>
  </div>
</div>
<?php
$fy = $this->session->userdata('finacial_year');
$fy_new = $fy + 1;
$lastdate_date = '20' . $fy_new . '-03-31';
$curr_date = date('Y-m-d');
$curr_date_new = new DateTime($curr_date);
$last_date_yr = new DateTime($lastdate_date);
if ($last_date_yr < $curr_date_new) {
    $max_date_php = $lastdate_date;
} else {
    $max_date_php = $curr_date;
}
?>

<?php init_tail(); ?>
<script>
    $(document).ready(function() {
        var fin_y = "<?php echo $this->session->userdata('finacial_year'); ?>";
        var year = "20" + fin_y;
        var cur_y = new Date().getFullYear().toString().substr(-2);

        // Min date: April 1st of FY start year
        var minStartDate = new Date(year, 3, 1); // month index 3 = April

        // Max date: March 31 of FY end year, OR today if still within FY
        var maxEndDate;
        if (parseInt(cur_y) > parseInt(fin_y)) {
            var fy_new = parseInt(fin_y) + 1;
            var fy_new_s = "20" + fy_new;
            maxEndDate = new Date(fy_new_s + '/03/31');
        } else {
            maxEndDate = new Date();
        }

        $('#from_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate,
            timepicker: false
        });
        $('#to_date').datetimepicker({
            format: 'd/m/Y',
            minDate: minStartDate,
            maxDate: maxEndDate,
            timepicker: false
        });
    });
  $(document).ready(function() {
    resetForm();
    $('#filter_list_form').submit();
  })

  function resetForm(){
    $('.exportBtn').hide();
    $('#table-list tbody').html('');
    
    let filterHtml = '';
    $('.filterInput').each(function () {
      let label = $(this).attr('app-field-label') || '';
      let value = $(this).val();

      if ($(this).is('select') && value) {
        value = $(this).find('option:selected').text().trim();
      } 
      else if ($(this).is('input, textarea')) {
        value = $(this).val().trim();
      }

      if (value) {
        filterHtml += `<b>${label} : </b> ${value}, `;
      }
    });

    filterHtml = filterHtml.replace(/, $/, '');
    $('.report_for').html(filterHtml);

    $('.selectpicker').selectpicker('refresh');
  }

  function getVendorDetailsLocation() {
    var vendorId = $('#vendor_id').val();
    $.ajax({
      url: '<?= admin_url('purchase/Quotation/getVendorDetailsLocation'); ?>',
      type: 'POST',
      data: { vendor_id: vendorId },
      dataType: 'json',
      success: function (response) {
        if (response.success == true) {
          html = '<option value="" selected>None selected</option>';
          $.each(response.broker_list, function (index, loc) {
            if(loc.AccountID == null || loc.AccountID == '') return;
            html += `<option value="${loc.AccountID}">${loc.company} (${loc.AccountID})</option>`;
					});
          $('#broker_id').html(html);

          $('.selectpicker').selectpicker('refresh');
        }
      }
    });
  }

  function getGodownList(location_id, callback = null) {
    $.ajax({
      url: '<?= admin_url('purchase/Direct/GetGodownListByLocation'); ?>',
      type: 'POST',
      data: { location_id: location_id },
      dataType: 'json',
      success: function (response) {
        if(response.success == true){
          var list = response.godown_list;
          var html = '<option value="" selected>None selected</option>';
          $.each(list, function (index, loc) {
            html += `<option value="${loc.id}">${loc.GodownName}</option>`;
          })
          $('#warehouse_id').html(html);
          $('.selectpicker').selectpicker('refresh');
        }else{
          $('#warehouse_id').html('<option value="" selected>None selected</option>');
          $('.selectpicker').selectpicker('refresh');
        }
        if(callback){ callback(); }
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
    $('#table-list tbody').html('');

    function fetchChunk() {
      var form_data = new FormData(form);
      form_data.append('offset', offset);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );

      $.ajax({
        url: "<?= admin_url('purchase/Direct/ListFilter') ?>",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function(res){
          let json = JSON.parse(res);
          if(!json.success){
            $('#searchBtn').prop('disabled', false);
            if(offset === 0){
              $('#table-list tbody').html(
                '<tr><td colspan="17" class="text-center">No Data Found</td></tr>'
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
            $('.exportBtn').show();
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
      html += `<tr>
        <td class="text-center">${row.OrderID}</td>
        <td>${moment(row.OrderDate).format('DD/MM/YYYY')}</td>
        <td>${row.LocationName}</td>
        <td>${row.GodownName}</td>
        <td>${row.VendorName}</td>
        <td>${row.BrokerName || '-'}</td>
        <td>${row.ItemTypeName}</td>
        <td>${row.TDSName}</td>
        <td>${row.TDSRate}%</td>
        <td class="text-center">${Number(row.PurchaseAmt) || '-'}</td>
        <td class="text-center">${Number(row.DiscAmt) || '-'}</td>
        <td class="text-center">${Number(row.CGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.SGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.IGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.FreightAmt) || '-'}</td>
        <td class="text-center">${Number(row.OtherAmt) || '-'}</td>
        <td class="text-center">${Number(row.RoundOff) || '-'}</td>
        <td class="text-center">${Number(row.TDSAmt) || '-'}</td>
        <td class="text-center">${Number(row.FinalAmt) || '-'}</td>
      </tr>`;
    });
    $('#table-list tbody').append(html);
  }

  function updateProgress(loaded, total){
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%')
  }

  function exportTableToExcel() {

    let formData = new FormData(document.getElementById('filter_list_form'));
    formData.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );

    $('.exportBtn').prop('disabled', true);

    $.ajax({
      url: "<?= admin_url('purchase/Direct/ListExportExcel') ?>",
      type: 'POST',
      data: formData,
      dataType: 'json',
      processData: false,
      contentType: false,
      cache: false,
      success: function (res) {

        $('.exportBtn').prop('disabled', false);

        if (res.success) {
          window.location.href = res.file_url;   // download file
        } else {
          console.log(res);
        }
      },
      error: function () {
        $('.exportBtn').prop('disabled', false);
      }
    });
  }
</script>
<script>
  $(document).on("click", ".sortable", function () {
		var table = $("#table-list tbody");
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

  function printPage() {
    $('.mainHead').show();
    var printContents = document.getElementById("printableArea").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    $('.mainHead').hide();
  }

  function myFunction2() {
    var input = document.getElementById("myInput1");
    var filter = input.value.toUpperCase();

    var table = document.getElementById("table-list");
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