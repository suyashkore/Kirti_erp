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
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Gate In List</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
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
                <div class="col-md-9 mbot5" style="padding-top: 20px;">
                  <button type="button" class="btn btn-info exportBtn" id="exportExcelBtn"><i class="fa fa-file-excel"></i> Excel</button>
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
                        <td colspan="13">
                          <h5 style="text-align:center;">
                            <span style="font-size:15px; font-weight:700;"><?= $company_detail->company_name ?? ''; ?></span><br>
                            <span style="font-size:10px; font-weight:600;"><?= $company_detail->address ?? ''; ?></span>
                          </h5>
                        </td>
                      </tr>
                      <tr class="mainHead" style="display: none;">
                        <td colspan="13">
                          <span class="report_for" style="font-size:10px;"></span>
                        </td>
                      </tr>
                      <tr>
                        <th class="sortable">GateIN ID</th>
                        <th class="sortable">Trans Date</th>
                        <th class="sortable">Location</th>
                        <th class="sortable">ASN ID</th>
                        <th class="sortable">ASN Date</th>
                        <th class="sortable">Inward ID</th>
                        <th class="sortable">Vehicle No</th>
                        <th class="sortable">Driver Name</th>
                        <th class="sortable">Driver Mobile No</th>
                        <th class="sortable">Status</th>
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
<?php init_tail(); ?>
<script>
  $(document).ready(function() {
    resetForm();
    $('#filter_list_form').submit();
  });

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


  $('#filter_list_form').submit(function(e){
    e.preventDefault();

    let form = this;
    let limit = 1;
    let offset = 0;
    let totalRecords = 0;
    let loadedRecords = 0;
    $('#searchBtn').prop('disabled', true);
    $('.exportBtn').hide(); // Hide export buttons on new search
    $('#table-list tbody').html('');

    function fetchChunk() {
      var form_data = new FormData(form);
      form_data.append('offset', offset);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );

      $.ajax({
        url: "<?= admin_url('purchase/Vehiclein/GateInListFilter') ?>",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function(res){
          let json = JSON.parse(res);
          if(!json.success){
            $('#searchBtn').prop('disabled', false);
            if(offset === 0){
              // No data found - show message, hide export buttons
              $('#table-list tbody').html(
                '<tr><td colspan="9" class="text-center">No Data Found</td></tr>'
              );
              $('.exportBtn').hide();
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
            $('#fetchProgress').css('width', '0%');
            $('.exportBtn').show(); // Show export buttons only when data is loaded
            return;
          }
          fetchChunk();
        }
      });
    }
    fetchChunk();
  });

  // Helper: format date or return '-' if null/empty/invalid
  function formatDate(val) {
    if (!val || val === null || val === '' || val === 'null' || val === 'undefined') {
      return '-';
    }
    var d = moment(val);
    return d.isValid() ? d.format('DD/MM/YYYY') : '-';
  }

  function appendRows(rows){
    let html = '';
    let status_list = {1 : 'Gate IN Generated', 2 :'Gross Weight Captured', 3 :'Conveyor Assigned', 4 :'QC Stack Captured', 5 :'Tare Weight Captured', 6 :'Gate Out Pass Generated', 7 :'Vehicle Exit', 8 :'QC', 9 :'Purchase Invoice Generated', 10 :'Complete'};
    rows.forEach(function(row){
      html += `<tr onclick="window.open('<?= admin_url('purchase/Inwards/Details/') ?>${row.GateINID}', '_blank')" style="cursor:pointer;">
        <td class="fixed-td" title="${row.GateINID || ''} - ${row.GateINID || ''}">${row.GateINID || ''}</td>
        <td>${formatDate(row.TransDate)}</td>
        <td class="fixed-td" title="${row.LocationName} - ${row.LocationID}">${row.LocationName || '-'}</td>
        <td class="text-center">${row.ASNID || '-'}</td>
        <td class="text-center">${formatDate(row.ASNDate)}</td>
        <td class="text-center">${row.InwardID || '-'}</td>
        <td class="text-center">${row.VehicleNo || '-'}</td>
        <td class="text-center">${row.DriverName || '-'}</td>
        <td class="text-center">${row.DriverMobileNo || '-'}</td>
        <td class="text-center">${status_list[row.status] || 'Pending'}</td>
      </tr>`;
    });
    $('#table-list tbody').append(html);
  }

  function updateProgress(loaded, total){
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%');  
  }

  // Excel Export Button Logic
  $(document).ready(function() {
    $('#exportExcelBtn').on('click', function() {
      let formData = new FormData(document.getElementById('filter_list_form'));
      formData.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );
      $('#exportExcelBtn').prop('disabled', true);
      $.ajax({
        url: "<?= admin_url('purchase/Vehiclein/GateInListExportExcel') ?>",
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        cache: false,
        success: function (res) {
          $('#exportExcelBtn').prop('disabled', false);
          if (res.success && res.file_url) {
            window.location.href = res.file_url;
          } else {
            alert(res.message || 'Failed to export Excel.');
          }
        },
        error: function () {
          $('#exportExcelBtn').prop('disabled', false);
          alert('Error exporting Excel.');
        }
      });
    });
  });
</script>
<script>
  $(document).on("click", ".sortable", function () {
    var table = $("#table-list tbody");
    var rows = table.find("tr").toArray();
    var index = $(this).index();
    var ascending = !$(this).hasClass("asc");
    $(".sortable").removeClass("asc desc");
    $(".sortable span").remove();
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