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
   @media print {
    body { margin: 0; padding: 0; }
    .table-list {
      max-height: none !important;
      overflow: visible !important;
    }
    table {
      width: 100% !important;
      table-layout: fixed !important;
      border-collapse: collapse !important;
    }
    th, td {
      border: 1px solid #000 !important;
      word-wrap: break-word !important;
      font-size: 9px !important;
    }
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Vendor List</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <form action="" method="post" id="filter_list_form">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="state">
                    <label for="state" class="control-label">State</label>
                    <select name="state" id="state" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="State">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($state_list)) :
                        foreach ($state_list as $value) :
                          echo '<option value="' . $value['state_name'] . '">' . $value['state_name'] .'</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="IsActive">
                    <label for="IsActive" class="control-label">Is Active</label>
                    <select name="IsActive" id="IsActive" class="form-control selectpicker filterInput" app-field-label="Is Active">
                      <option value="Y">Yes</option>
                      <option value="N">No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5" style="padding-top: 20px;">
                  <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
                  <button type="button" class="btn btn-info exportBtn" onclick="exportTableToExcel()" style="display: none;"><i class="fa fa-file-excel"></i> Excel</button>
                  <button type="button" class="btn btn-info exportBtn" onclick="printPage();" style="display: none;"><i class="fa fa-print"></i> Print</button>
                </div>
                <div class="col-md-3 mbot5" style="padding-top: 20px;"></div>
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
    <td colspan="10">
        <h5 style="text-align:center;">
            <span style="font-size:13px; font-weight:700;">Vendors List</span><br>
            <span style="font-size:11px; font-weight:700;"><?= $company_detail->company_name ?? ''; ?></span><br>
            <span style="font-size:10px; font-weight:600;"><?= $company_detail->address ?? ''; ?></span>
        </h5>
    </td>
</tr>
<tr class="mainHead" style="display: none;">
    <td colspan="10">
        <span class="report_for" style="font-size:10px;"></span>
    </td>
</tr>
                      <tr>
                        <th class="sortable">Vendor No</th>
                        <th class="sortable">Vendor Name</th>
                        <th class="sortable">Favouring Name</th>
                        <th class="sortable">PAN No</th>
                        <th class="sortablePop">GSTIN</th>
                        <th class="sortablePop">State</th>
                        <th class="sortablePop">Pincode</th>
                        <th class="sortablePop">Mobile</th>
                        <th class="sortablePop">Email</th>
                        <th class="sortablePop">Is Active</th>
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
<!-- <script>
  
  $(document).ready(function() {
    resetForm();
    $('#filter_list_form').submit();
  })

  function resetForm() {
    $('.exportBtn').hide();
    $('#table-list tbody').html('');

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

  $('#filter_list_form').submit(function(e) {
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
      form_data.append('limit', limit);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );

      $.ajax({
        url: "<?= admin_url('purchase/ListFilter') ?>",
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
                '<tr><td colspan="14" class="text-center">No Data Found</td></tr>'
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

  function appendRows(rows) {
    let html = '';
    rows.forEach(function(row) {
        html += `<tr>
            <td class="text-center">${row.AccountID || ''}</td>
            <td>${row.customer_name || ''}</td>
            <td>${row.FavouringName || ''}</td>
            <td>${row.PAN || ''}</td>
            <td>${row.GSTIN || ''}</td>
            <td>${row.state || ''}</td>
            <td>${row.billing_zip || ''}</td>
            <td>${row.MobileNo || ''}</td>
            <td>${row.Email || ''}</td>
            <td>${row.IsActive === 'Y' ? 'Yes' : (row.IsActive === 'N' ? 'No' : '')}</td>
        </tr>`;
    });
    $('#table-list tbody').append(html);
}

  function updateProgress(loaded, total) {
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
      url: "<?= admin_url('purchase/ListExportExcel') ?>",
      type: 'POST',
      data: formData,
      dataType: 'json',
      processData: false,
      contentType: false,
      cache: false,
      success: function(res) {

        $('.exportBtn').prop('disabled', false);

        if (res.success) {
          window.location.href = res.file_url; // download file
        } else {
          console.log(res);
        }
      },
      error: function() {
        $('.exportBtn').prop('disabled', false);
      }
    });
  }
</script>
<script>
  $(document).on("click", ".sortable", function() {
    var table = $("#table-list tbody");
    var rows = table.find("tr").toArray();
    var index = $(this).index();
    var ascending = !$(this).hasClass("asc");
    $(".sortable").removeClass("asc desc");
    $(".sortable span").remove();
    // Add sort classes and arrows
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
    let filterParts = [];

    let stateVal = $('#state').find('option:selected').text().trim();
    let isActiveVal = $('#IsActive').find('option:selected').text().trim();

    if (stateVal && stateVal !== 'None selected') {
        filterParts.push('<b>State : </b>' + stateVal);
    }
    if (isActiveVal) {
        filterParts.push('<b>Is Active : </b>' + isActiveVal);
    }

    // ✅ Inject filter text into the report header
    $('.report_for').html(filterParts.join('&nbsp;&nbsp;|&nbsp;&nbsp;'));

    $('.mainHead').show();

    // ✅ Store the printable area HTML only (not full body swap)
    var printContents = document.getElementById("printableArea").innerHTML;

    // ✅ Create a hidden iframe for printing instead of replacing body
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);

    iframe.contentDocument.open();
    iframe.contentDocument.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                @page { size: A4 landscape; margin: 10mm; }
                body { margin: 0; font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; table-layout: auto; }
                th, td {
                    border: 1px solid #000 !important;
                    padding: 2px 4px !important;
                    font-size: 8px !important;
                    white-space: nowrap;
                    vertical-align: middle;
                }
                th {
                    background: #50607b !important;
                    color: #fff !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            </style>
        </head>
        <body>${printContents}</body>
        </html>
    `);
    iframe.contentDocument.close();

    iframe.contentWindow.focus();
    iframe.contentWindow.print();

    // ✅ Remove iframe after print — no body replacement, so selectpicker stays intact
    setTimeout(function() {
        document.body.removeChild(iframe);
        $('.mainHead').hide();
    }, 1000);
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
</script> -->

<?php init_tail(); ?>
<script>
  let currentFilterHtml = '';

  $(document).ready(function() {
    resetForm();
    $('#filter_list_form').submit();
  });

  function resetForm() {
    // ✅ Hide export/print buttons until data is loaded
    $('.exportBtn').hide();
    $('#table-list tbody').html('');

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

  $('#filter_list_form').submit(function(e) {
    e.preventDefault();

    // ✅ Capture filter values at Show click time
    let filterParts = [];
    let stateVal = $('#state').find('option:selected').text().trim();
    let isActiveVal = $('#IsActive').find('option:selected').text().trim();

    if (stateVal && stateVal !== 'None selected') {
      filterParts.push('<b>State : </b>' + stateVal);
    }
    if (isActiveVal) {
      filterParts.push('<b>Is Active : </b>' + isActiveVal);
    }
    currentFilterHtml = filterParts.join('&nbsp;&nbsp;|&nbsp;&nbsp;');

    let form = this;
    let limit = 100;
    let offset = 0;
    let totalRecords = 0;
    let loadedRecords = 0;

    $('#searchBtn').prop('disabled', true);
    // ✅ Hide buttons while loading new data
    $('.exportBtn').hide();
    $('#table-list tbody').html('');

    function fetchChunk() {
      var form_data = new FormData(form);
      form_data.append('offset', offset);
      form_data.append('limit', limit);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );

      $.ajax({
        url: "<?= admin_url('purchase/ListFilter') ?>",
        type: "POST",
        data: form_data,
        processData: false,
        contentType: false,
        success: function(res) {
          let json = JSON.parse(res);

          if (!json.success) {
            $('#searchBtn').prop('disabled', false);
            if (offset === 0) {
              // ✅ No data — keep buttons hidden
              $('.exportBtn').hide();
              $('#table-list tbody').html(
                '<tr><td colspan="10" class="text-center">No Data Found</td></tr>'
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

            // ✅ Only show export/print if rows actually exist
            if (loadedRecords > 0) {
              $('.exportBtn').show();
            } else {
              $('.exportBtn').hide();
            }
            return;
          }

          fetchChunk();
        },
        error: function() {
          $('#searchBtn').prop('disabled', false);
          $('.exportBtn').hide();
        }
      });
    }

    fetchChunk();
  });

  function appendRows(rows) {
    let html = '';
    rows.forEach(function(row) {
      html += `<tr>
          <td class="text-center">${row.AccountID || ''}</td>
          <td>${row.customer_name || ''}</td>
          <td>${row.FavouringName || ''}</td>
          <td>${row.PAN || ''}</td>
          <td>${row.GSTIN || ''}</td>
          <td>${row.state || ''}</td>
          <td>${row.billing_zip || ''}</td>
          <td>${row.MobileNo || ''}</td>
          <td>${row.Email || ''}</td>
          <td>${row.IsActive === 'Y' ? 'Yes' : (row.IsActive === 'N' ? 'No' : '')}</td>
      </tr>`;
    });
    $('#table-list tbody').append(html);
  }

  function updateProgress(loaded, total) {
    let percent = total > 0 ? Math.floor((loaded / total) * 100) : 0;
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
      url: "<?= admin_url('purchase/ListExportExcel') ?>",
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
</script>

<script>
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
    $('.report_for').html(currentFilterHtml);
    $('.mainHead').show();

    // ✅ Build print HTML without document.write()
    var tableHTML = document.getElementById("printableArea").innerHTML;

    var printStyles = `
      <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { margin: 0; font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; table-layout: auto; }
        th, td {
          border: 1px solid #000 !important;
          padding: 2px 4px !important;
          font-size: 8px !important;
          white-space: nowrap;
          vertical-align: middle;
        }
        th {
          background: #50607b !important;
          color: #fff !important;
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
        }
      </style>
    `;

    // ✅ Use Blob URL instead of document.write() to avoid violation warning
    var fullHTML = `<!DOCTYPE html><html><head>${printStyles}</head><body>${tableHTML}</body></html>`;
    var blob = new Blob([fullHTML], { type: 'text/html' });
    var blobURL = URL.createObjectURL(blob);

    var iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.top = '-9999px';
    iframe.style.left = '-9999px';
    iframe.style.width = '1px';
    iframe.style.height = '1px';
    iframe.src = blobURL;

    document.body.appendChild(iframe);

    iframe.onload = function() {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();

      // ✅ Cleanup after print dialog closes
      setTimeout(function() {
        document.body.removeChild(iframe);
        URL.revokeObjectURL(blobURL);
        $('.mainHead').hide();
      }, 1000);
    };
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