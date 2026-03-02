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
                <li class="breadcrumb-item active text-capitalize"><b>Sales</b></li><li class="breadcrumb-item active" aria-current="page"><b>Sales Quotation List</b></li>
							</ol>
						</nav>
            <hr class="hr_style">
            <form action="" method="post" id="filter_list_form">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="from_date">
                    <label for="from_date" class="control-label">From Date</label>
                    <div class="input-group date">
                      <input type="text" id="from_date" name="from_date" class="form-control datepicker filterInput" value="<?= date("01/m/Y")?>" app-field-label="From Date" onchange="applyClientFilter();">
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
                      <input type="text" id="to_date" name="to_date" class="form-control datepicker filterInput" value="<?= date("d/m/Y")?>" app-field-label="To Date" onchange="applyClientFilter();">
                      <div class="input-group-addon">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                      </div>
                    </div>
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="customer_id">
                    <label for="customer_id" class="control-label">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Customer" onchange="getCustomerDetailsLocation();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($customer_list)) :
                        foreach ($customer_list as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
								<div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="broker_id">
                    <label for="broker_id" class="control-label">Broker</label>
                    <select name="broker_id" id="broker_id" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Broker">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="status">
                    <label for="status" class="control-label">Status</label>
                    <select name="status" id="status" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Status" onchange="applyClientFilter();">
                      <option value="" selected>None selected</option>
                      <?php
											$status = [1 => 'Pending', 2 =>'Cancel', 3 =>'Expired', 4 =>'Approved', 5 =>'Inprogress', 6 =>'Complete', 7 =>'Partially Complete'];
                      if (!empty($status)) :
                        foreach ($status as $key => $value) :
                          echo '<option value="' . $key . '">' . $value . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-9 mbot5" style="padding-top: 20px;">
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
                        <th class="sortable">Quotation No</th>
                        <th class="sortable">Quotation Date</th>
                        <th class="sortable">Customer Name</th>
                        <th class="sortable">Broker Name</th>
                        <th class="sortable">Quotation Amt</th>
                        <th class="sortable">Quotation Wt</th>

                        <th class="sortable">TotalQuantity</th>
                        <th class="sortable">ItemAmt</th>
                        <th class="sortable">DiscAmt</th>
                        <th class="sortable">TaxableAmt</th>
                        <th class="sortable">CGSTAmt</th>
                        <th class="sortable">SGSTAmt</th>
                        <th class="sortable">IGSTAmt</th>
                        <th class="sortable">RoundOffAmt</th>

                        <th class="sortable">Sales Wt</th>
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

$(document).on('change', '.filterInput', function() {
    applyClientFilter();
});

$('#myInput1').on('keyup', function() {
    var filter = this.value.toUpperCase();
    $('#table-list tbody tr').each(function() {
        var text = $(this).text().toUpperCase();
        $(this).toggle(text.indexOf(filter) > -1);
    });
});

function applyClientFilter() {
    var statusFilter   = $('#status').val();
    var customerFilter = $('#customer_id').val();
    var brokerFilter   = $('#broker_id').val();
    var fromDate       = parseDate($('#from_date').val());
    var toDate         = parseDate($('#to_date').val());

    $('#table-list tbody tr.no-data-row').remove();

    var visibleCount = 0;

    $('#table-list tbody tr').each(function() {
        var $row = $(this);

        var rowStatus   = $row.data('status')   ? String($row.data('status'))   : '';
        var rowCustomer = $row.data('customer') ? String($row.data('customer')) : '';
        var rowBroker   = $row.data('broker')   ? String($row.data('broker'))   : '';
        var rowDate     = parseDate($row.data('date'));

        var dateMatch     = (!fromDate || !toDate) || (rowDate >= fromDate && rowDate <= toDate);
        var customerMatch = !customerFilter || rowCustomer === customerFilter;
        var brokerMatch   = !brokerFilter   || rowBroker === brokerFilter;
        var statusMatch   = !statusFilter   || rowStatus === statusFilter;

        var isVisible = dateMatch && customerMatch && brokerMatch && statusMatch;
        $row.toggle(isVisible);

        if (isVisible) visibleCount++;
    });

    // ✅ कोणताही row दिसत नसेल तर "No Data Found" दाखवा
    if (visibleCount === 0 && $('#table-list tbody tr').length > 0) {
        $('#table-list tbody').append(
            '<tr class="no-data-row"><td colspan="16" class="text-center">No Data Found</td></tr>'
        );
    }
}

function parseDate(dateStr) {
    if (!dateStr) return null;
    var parts = dateStr.split('/');
    return new Date(parts[2], parts[1] - 1, parts[0]);
}



  $(document).ready(function() {
    resetForm();
     $('#status').val('').selectpicker('refresh');
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

  function getCustomerDetailsLocation() {
    var customerId = $('#customer_id').val();
    $.ajax({
      url: '<?= admin_url('SalesQuotation/getCustomerDetailsLocation'); ?>',
      type: 'POST',
      data: { customer_id: customerId },
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

  $('#filter_list_form').submit(function(e){
    e.preventDefault();

    let form = this;
    let limit = 100;
    let offset = 0;
    let totalRecords = 0;
    let loadedRecords = 0;
    
    $('#searchBtn').prop('disabled', true);
    $('#table-list tbody').html('');

    function fetchChunk() {
        var form_data = new FormData(form);
        form_data.append('offset', offset);
        form_data.append('limit', limit); // ✅ limit pan send करा
        form_data.append(
            '<?= $this->security->get_csrf_token_name(); ?>',
            $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
        );

        $.ajax({
            url: "<?= admin_url('SalesQuotation/ListFilter') ?>",
            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            success: function(res) {
                let json = JSON.parse(res);
                
                if (!json.success) {
                    $('#searchBtn').prop('disabled', false);
                    if (offset === 0) {
                        $('#table-list tbody').html(
                            '<tr><td colspan="16" class="text-center">No Data Found</td></tr>'
                        );
                    }
                    return;
                }

                if (offset === 0) {
                    totalRecords = parseInt(json.total) || 0;
                }

                if (json.rows && json.rows.length > 0) {
                    appendRows(json.rows);
                    loadedRecords += json.rows.length;
                    offset += limit; // ✅ 100 ने वाढेल
                }

                updateProgress(loadedRecords, totalRecords);

                if (loadedRecords >= totalRecords) {
                    $('#searchBtn').prop('disabled', false);
                    $('#fetchProgress').css('width', '0%');
                    $('.exportBtn').show();
                    return;
                }

                fetchChunk(); // ✅ अजून records असतील तरच recursive call
            }
        });
    }

    fetchChunk();
});

  function appendRows(rows){
    let html = '';
    let status_list = {1 : 'Pending', 2 :'Cancel', 3 :'Expired', 4 :'Approved', 5 :'Inprogress', 6 :'Complete', 7 :'Partially Complete'};
    rows.forEach(function(row){
      var d = new Date(row.TransDate);
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var yyyy = d.getFullYear();
        var dateStr = dd + '/' + mm + '/' + yyyy;

      html += `<tr data-status="${row.Status || ''}"
            data-customer="${row.AccountID || ''}"
            data-broker="${row.BrokerID || ''}"
            data-date="${dateStr}">
        <td class="text-center">${row.QuotationID}</td>
        <td>${moment(row.TransDate).format('DD/MM/YYYY')}</td>
        <td>${row.customer_name} (${row.AccountID})</td>
        <td>${row.broker_name || ''} (${row.BrokerID || ''})</td>
        <td class="text-center">${Number(row.NetAmt) || '-'}</td>
        <td class="text-center">${Number(row.TotalWeight / 100) || '-'}</td>

        <td class="text-center">${Number(row.TotalQuantity) || '-'}</td>
        <td class="text-center">${Number(row.ItemAmt) || '-'}</td>
        <td class="text-center">${Number(row.DiscAmt) || '-'}</td>
        <td class="text-center">${Number(row.TaxableAmt) || '-'}</td>
        <td class="text-center">${Number(row.CGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.SGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.IGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.RoundOffAmt) || '-'}</td>

        <td class="text-center">${Number(row.so_total_weight / 100) || '-'}</td>
        <td class="text-center">${status_list[row.Status] || 'Pending'}</td>
      </tr>`;
    });
    $('#table-list tbody').append(html);
    applyClientFilter();
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
      url: "<?= admin_url('SalesQuotation/ListExportExcel') ?>",
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