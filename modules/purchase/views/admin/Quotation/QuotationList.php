<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
  .table-list {
    overflow: auto;
    max-height: 55vh;
    width: 100%;
    position: relative;
    top: 0px;
  }

  .table-list thead th {
    position: sticky;
    top: 0;
    z-index: 1;
  }

  .table-list tbody th {
    position: sticky;
    left: 0;
  }

  table {
    border-collapse: collapse;
    width: 100%;
  }

  th,
  td {
    padding: 1px 5px !important;
    white-space: nowrap;
    border: 1px solid !important;
    font-size: 11px;
    line-height: 1.42857143 !important;
    vertical-align: middle !important;
  }

  th {
    background: #50607b;
    color: #fff !important;
  }

  .sortable {
    cursor: pointer;
  }

  .fixed-td {
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('success')) : ?>
              <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-check-circle"></i> <?= $this->session->flashdata('success'); ?>
              </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
              <div class="alert alert-danger alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <i class="fa fa-times-circle"></i> <?= $this->session->flashdata('error'); ?>
              </div>
            <?php endif; ?>

            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Quotation List</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <form action="" method="post" id="filter_list_form">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="from_date">
                    <?= render_date_input('from_date', 'From Date', date('01/m/Y'), []); ?>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="to_date">
                    <?= render_date_input('to_date', 'To Date', date('d/m/Y'), []); ?>
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
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' (' . $value['AccountID'] . ')</option>';
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
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="status">
                    <label for="status" class="control-label">Status</label>
                    <select name="status" id="status" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Status" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      $status = [1 => 'Pending', 2 => 'Cancel', 3 => 'Expired', 4 => 'Approved', 5 => 'Inprogress', 6 => 'Complete', 7 => 'Partially Complete'];
                      if (!empty($status)) :
                        foreach ($status as $key => $value) :
                          echo '<option value="' . $key . '" ' . ($key == 1 ? 'selected' : '') . '>' . $value . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-9 mbot5" style="padding-top: 20px;">
                  <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
                  <?php
                  if (has_permission_new('PurchaseQuotationList', '', 'export')) {
                    echo '<button type="button" class="btn btn-info exportBtn" onclick="exportTableToExcel()" style="display: none;"><i class="fa fa-file-excel"></i> Excel</button> ';
                  }
                  if (has_permission_new('PurchaseQuotationList', '', 'print')) {
                    echo '<button type="button" class="btn btn-info exportBtn" onclick="printPage();" style="display: none;"><i class="fa fa-print"></i> Print</button>';
                  }
                  ?>
                  <!-- Create PO Button - will be enabled only when a checkbox is selected -->
                  <button type="button" class="btn btn-warning" id="createPoBtn" onclick="createPO()" style="display: none;" disabled>
                    <i class="fa fa-plus-circle"></i> Create PO
                  </button>
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
                        <td colspan="18">
                          <h5 style="text-align:center;">
                            <span style="font-size:15px; font-weight:700;"><?= $company_detail->company_name ?? ''; ?></span><br>
                            <span style="font-size:10px; font-weight:600;"><?= $company_detail->address ?? ''; ?></span>
                          </h5>
                        </td>
                      </tr>
                      <tr class="mainHead" style="display: none;">
                        <td colspan="18">
                          <span class="report_for" style="font-size:10px;"></span>
                        </td>
                      </tr>
                      <tr>
                        <th style="text-align:center;">
                          <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" title="Select All Pending">
                        </th>
                        <th class="sortable">Quotation No</th>
                        <th class="sortable">Quotation Date</th>
                        <th class="sortable">Category</th>
                        <th class="sortable">Vendor</th>
                        <th class="sortable">Broker</th>
                        <th class="sortable">Total Wt</th>
                        <th class="sortable">Total Qty</th>
                        <th class="sortable">Item Total</th>
                        <th class="sortable">Total Disc</th>
                        <th class="sortable">Taxable Amt</th>
                        <th class="sortable">CGST Amt</th>
                        <th class="sortable">SGST Amt</th>
                        <th class="sortable">IGST Amt</th>
                        <th class="sortable">Round Off</th>
                        <th class="sortable">Net Amt</th>
                        <th class="sortable">Order Wt</th>
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
  // Auto-dismiss flash alerts after 4 seconds
  setTimeout(function() {
    $('.alert').fadeOut('slow');
  }, 4000);

  $(document).ready(function() {
    var fin_y = "<?php echo $this->session->userdata('finacial_year'); ?>";
    var year = "20" + fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);

    var minStartDate = new Date(year, 3, 1);

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
  });

  function resetForm() {
    $('.exportBtn').hide();
    $('#createPoBtn').hide().prop('disabled', true);
    $('#table-list tbody').html('');
    $('#selectAll').prop('checked', false);

    let filterHtml = '';
    $('.filterInput').each(function() {
      let label = $(this).attr('app-field-label') || '';
      let value = $(this).val();

      if ($(this).is('select') && value) {
        value = $(this).find('option:selected').text().trim();
      } else if ($(this).is('input, textarea')) {
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
      data: {
        vendor_id: vendorId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          html = '<option value="" selected>None selected</option>';
          $.each(response.broker_list, function(index, loc) {
            if (loc.AccountID == null || loc.AccountID == '') return;
            html += `<option value="${loc.AccountID}">${loc.company} (${loc.AccountID})</option>`;
          });
          $('#broker_id').html(html);
          $('.selectpicker').selectpicker('refresh');
        }
      }
    });
  }

  $('#filter_list_form').submit(function(e) {
    e.preventDefault();

    let form = this;
    let limit = 1;
    let offset = 0;
    let totalRecords = 0;
    let loadedRecords = 0;
    $('#searchBtn').prop('disabled', true);
    $('#table-list tbody').html('');
    $('#selectAll').prop('checked', false);

    function fetchChunk() {
      var form_data = new FormData(form);
      form_data.append('offset', offset);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );

      $.ajax({
        url: "<?= admin_url('purchase/Quotation/ListFilter') ?>",
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
                '<tr><td colspan="18" class="text-center">No Data Found</td></tr>'
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
            offset += limit;
          }
          updateProgress(loadedRecords, totalRecords);
          if (loadedRecords >= totalRecords) {
            $('#searchBtn').prop('disabled', false);
            $('#fetchProgress').css('width', '0%');
            $('.exportBtn').show();
            if ($('.row-select').length > 0) {
              $('#createPoBtn').show();
            }
            return;
          }
          fetchChunk();
        }
      });
    }
    fetchChunk();
  });

  function appendRows(rows) {
    let html = '';
    let status_list = {
      1: 'Pending',
      2: 'Cancel',
      3: 'Expired',
      4: 'Approved',
      5: 'Inprogress',
      6: 'Complete',
      7: 'Partially Complete'
    };

    rows.forEach(function(row) {
      let checkboxTd = '';
      if (row.Status == 1) {
        let rowData = encodeURIComponent(JSON.stringify(row));
        checkboxTd = `<td class="text-center">
          <input type="checkbox" class="row-select" value="${row.QuotatioonID}" data-row="${rowData}">
        </td>`;
      } else {
        checkboxTd = `<td></td>`;
      }

      html += `<tr>
        ${checkboxTd}
        <td class="text-center">${row.QuotatioonID}</td>
        <td>${moment(row.TransDate).format('DD/MM/YYYY')}</td>
        <td>${row.category_name}</td>
        <td class="fixed-td" title="${row.vendor_name} - ${row.AccountID}">${row.vendor_name} - ${row.AccountID}</td>
        <td class="fixed-td" title="${row.broker_name || ''} - ${row.BrokerID || ''}">${row.broker_name || ''} - ${row.BrokerID || ''}</td>
        <td class="text-center">${Number(row.TotalWeight / 100) || '-'}</td>
        <td class="text-center">${Number(row.TotalQuantity) || '-'}</td>
        <td class="text-center">${Number(row.ItemAmt) || '-'}</td>
        <td class="text-center">${Number(row.DiscAmt) || '-'}</td>
        <td class="text-center">${Number(row.TaxableAmt) || '-'}</td>
        <td class="text-center">${Number(row.CGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.SGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.IGSTAmt) || '-'}</td>
        <td class="text-center">${Number(row.RoundOffAmt) || '-'}</td>
        <td class="text-center">${Number(row.NetAmt) || '-'}</td>
        <td class="text-center">${Number(row.po_total_weight / 100) || '-'}</td>
        <td class="text-center">${status_list[row.Status] || 'Pending'}</td>
      </tr>`;
    });
    $('#table-list tbody').append(html);
  }

  function toggleSelectAll(source) {
    $('.row-select:visible').prop('checked', source.checked).trigger('change');
  }

  $(document).on('change', '.row-select', function() {
    let checkedCount = $('.row-select:checked').length;
    $('#createPoBtn').prop('disabled', checkedCount === 0);
  });

  function createPO() {
    let selectedRows = [];
    $('.row-select:checked').each(function() {
      let rowData = JSON.parse(decodeURIComponent($(this).attr('data-row')));
      selectedRows.push(rowData);
    });

    if (selectedRows.length === 0) {
      alert('Please select at least one Quotation.');
      return;
    }

    let csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    let csrfToken = $('input[name="' + csrfName + '"]').val();

    let form = $('<form>', {
      method: 'POST',
      action: '<?= admin_url('purchase/Quotation/create_po') ?>'
    });

    form.append($('<input>', {
      type: 'hidden',
      name: csrfName,
      value: csrfToken
    }));

    selectedRows.forEach(function(row) {
      form.append($('<input>', {
        type: 'hidden',
        name: 'quotation_ids[]',
        value: row.QuotatioonID
      }));
    });

    $('body').append(form);
    form.submit();
  }

  function updateProgress(loaded, total) {
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%');
  }

  function exportTableToExcel() {
    let formData = new FormData(document.getElementById('filter_list_form'));
    formData.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );

    $('.exportBtn').prop('disabled', true);

    $.ajax({
      url: "<?= admin_url('purchase/Quotation/ListExportExcel') ?>",
      type: 'POST',
      data: formData,
      dataType: 'json',
      processData: false,
      contentType: false,
      cache: false,
      success: function(res) {
        $('.exportBtn').prop('disabled', false);
        if (res.success) {
          window.location.href = res.file_url;
        } else {
          console.log(res);
        }
      },
      error: function() {
        $('.exportBtn').prop('disabled', false);
      }
    });
  }

  $(document).on("click", ".sortable", function() {
    var table = $("#table-list tbody");
    var rows = table.find("tr").toArray();
    var index = $(this).index();
    var ascending = !$(this).hasClass("asc");
    $(".sortable").removeClass("asc desc");
    $(".sortable span").remove();
    $(this).addClass(ascending ? "asc" : "desc");
    $(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
    rows.sort(function(a, b) {
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