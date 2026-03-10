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
                <li class="breadcrumb-item active text-capitalize"><b>Transport</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Vehicle In Premises</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <form action="" method="post" id="filter_list_form">
              <div class="row" style="padding-top:20px;">

                <div class="col-md-2 mbot5">
                  <div class="form-group">
                    <label for="from_date" class="control-label">From Date</label>
                    <div class="input-group date">
                      <input type="text" id="from_date" name="from_date" class="form-control datepicker filterInput" value="<?= date("01/m/Y") ?>">
                      <div class="input-group-addon">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group">
                    <label for="to_date" class="control-label">To Date</label>
                    <div class="input-group date">
                      <input type="text" id="to_date" name="to_date" class="form-control datepicker filterInput" value="<?= date("d/m/Y") ?>">
                      <div class="input-group-addon">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-2 mbot5">
                  <div class="form-group">
                    <label for="status" class="control-label">Status</label>
                    <select name="status" id="status" class="form-control selectpicker filterInput" data-live-search="true">
                      <option value="" selected>None selected</option>
                      <?php
                      $status = [1 => 'GateIn', 2 => 'GrossWeight', 3 => 'Conveyor', 4 => 'StackQC', 5 => 'TareWeight', 6 => 'GateOut', 7 => 'GateExit',];
                      foreach ($status as $key => $value): ?>
                        <option value="<?= $key ?>"><?= $value ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-1 mbot5">
                  <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div>
                      <button type="button" class="btn btn-success btn-block" onclick="applyClientFilter();">
                        <i class="fa fa-list"></i> Show
                      </button>
                    </div>
                  </div>

                </div>
                <div class="clearfix"></div>

                <div class="col-md-9 mbot5">
                  <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div>
                      <button type="button" class="btn btn-info" onclick="printPage();"><i class="fa fa-print"></i> Print</button>
                      <button type="button" class="btn btn-info" onclick="exportToExcel();"><i class="fa-solid fa-file-excel"></i> Excel</button>
                      <button type="button" class="btn btn-info" onclick="exportToCSV();"><i class="fa-solid fa-file-csv"></i> CSV</button>
                    </div>
                  </div>
                </div>

                <div class="col-md-3 mbot5">
                  <div class="form-group">
                    <label for="myInput1" class="control-label">Search</label>
                    <input type="search" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search..." title="Type in table">
                  </div>
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
                        <th class="sortable">Sr. No</th>
                        <th class="sortable">Vehicle No</th>
                        <th class="sortable">Gate In Number</th>
                        <th class="sortable">Location</th>
                        <th class="sortable">Entry Time</th>
                        <th class="sortable">Exit Time</th>
                        <th class="sortable">Total Hours (H:M)</th>
                        <th class="sortable">Vehicle Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (!empty($GateMaster)):
                        foreach ($GateMaster as $key => $value):
                          $entry = new DateTime($value["EntryTime"]);

                          if (!empty($value["ExitTime"])) {
                            $exit       = new DateTime($value["ExitTime"]);
                            $diff       = $entry->diff($exit);
                            $totalHours = str_pad(($diff->days * 24) + $diff->h, 2, '0', STR_PAD_LEFT)
                              . ':' . str_pad($diff->i, 2, '0', STR_PAD_LEFT);
                            $exitDisplay  = $exit->format('d/m/Y H:i:s');
                          } else {
                            $totalHours  = '-';
                            $exitDisplay = '-';
                          }
                      ?>
                          <tr data-status="<?= $value['status']; ?>" data-date="<?= $entry->format('d/m/Y'); ?>" data-exit-date="<?= !empty($value['ExitTime']) ? $exit->format('d/m/Y') : ''; ?>">
                            <td><?= $key + 1 ?></td>
                            <td><?= $value["VehicleNo"]; ?></td>
                            <td><?= $value["GateINID"]; ?></td>
                            <td><?= $value["Location"]; ?></td>
                            <td><?= $entry->format('d/m/Y H:i:s'); ?></td>
                            <td><?= $exitDisplay; ?></td>
                            <td><?= $totalHours; ?></td>
                            <td><?= $value["status_text"]; ?></td>
                          </tr>
                      <?php
                        endforeach;
                      endif;
                      ?>
                    </tbody>
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

<!-- xlsx-js-style for Excel export with full cell styling support -->
<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

<script>
  function applyClientFilter() {
    var statusFilter = $('#status').val();
    var fromDate = parseDate($('#from_date').val());
    var toDate = parseDate($('#to_date').val());

    $('#table-list tbody tr.no-data-row').remove();

    var visibleCount = 0;

    $('#table-list tbody tr').each(function() {
      var $row = $(this);
      var rowStatus = $row.data('status') ? String($row.data('status')) : '';
      var rowEntryDate = parseDate($row.data('date'));
      var rowExitDate = parseDate($row.data('exit-date'));

      // From Date filters Entry Date
      var fromMatch = !fromDate || (rowEntryDate && rowEntryDate >= fromDate);

      // To Date filters Exit Date — if no exit date yet, still show the row
      var toMatch = !toDate || !rowExitDate || (rowExitDate <= toDate);

      var statusMatch = !statusFilter || rowStatus === statusFilter;

      var isVisible = fromMatch && toMatch && statusMatch;
      $row.toggle(isVisible);
      if (isVisible) visibleCount++;
    });

    if (visibleCount === 0 && $('#table-list tbody tr').length > 0) {
      $('#table-list tbody').append(
        '<tr class="no-data-row"><td colspan="8" class="text-center">No Data Found</td></tr>'
      );
    }
  }

  function parseDate(dateStr) {
    if (!dateStr) return null;
    var parts = dateStr.split('/');
    return new Date(parts[2], parts[1] - 1, parts[0]);
  }

  function handleExport(value) {
    if (!value) return;

    if (value === 'print') {
      printPage();
    } else if (value === 'excel') {
      exportToExcel();
    } else if (value === 'csv') {
      exportToCSV();
    }
  }

  function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;
    const dd = String(d.getDate()).padStart(2, '0');
    const mm = String(d.getMonth() + 1).padStart(2, '0');
    const yyyy = d.getFullYear();
    const hh = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    const ss = String(d.getSeconds()).padStart(2, '0');
    return `${dd}/${mm}/${yyyy} ${hh}:${min}:${ss}`;
  }


  function resetForm() {
    $('#table-list tbody').html('');
    $('#recordCount').hide();

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

  function appendRows(rows) {
    const statusMap = {
      1: 'Gate In Generate',
      2: 'Gross Weight Capture',
      3: 'Conveyor Selected',
      4: 'QC Stack Details Added',
      5: 'Tare Weight Capture',
      6: 'Gate Out Generated',
      7: 'Vehicle Exit Premises',
    };

    let html = '';
    rows.forEach(function(row, index) {
      const srNo = $('#table-list tbody tr').length + index + 1;
      const statusLabel = statusMap[row.Status] || 'Unknown';
      const entryDisplay = formatDate(row.EntryTime);
      const exitDisplay = row.ExitTime ? formatDate(row.ExitTime) : '-';
      const totalTime = row.ExitTime ? calculateTime(row.EntryTime, row.ExitTime) : '-';

      html += `<tr data-status="${row.status}" data-date="${entryDisplay.substring(0, 10)}" data-exit-date="${row.ExitTime ? exitDisplay.substring(0, 10) : ''}">
        <td class="text-center">${srNo}</td>
        <td>${row.VehicleNo}</td>
        <td class="text-center">${row.GateINID}</td>
        <td class="text-center">${row.Location ?? ''}</td>
        <td>${entryDisplay}</td>
        <td>${exitDisplay}</td>
        <td>${totalTime}</td>
        <td>${statusLabel}</td>
      </tr>`;
    });
    $('#table-list tbody').append(html);
  }

  function updateProgress(loaded, total) {
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%');
  }

  function calculateTime(entryTime, exitTime) {
    if (!entryTime || !exitTime) return "00:00";
    const start = new Date(entryTime);
    const end = new Date(exitTime);
    let diffMs = end - start;
    if (diffMs < 0) return "00:00";
    const totalMinutes = Math.floor(diffMs / (1000 * 60));
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    const formattedHours = String(hours).padStart(2, '0');
    const formattedMinutes = String(minutes).padStart(2, '0');
    return `${formattedHours}:${formattedMinutes}`;
  }

  //Export Helpers

  /**
   * Collects visible table rows into an array of objects.
   * Skips rows that are hidden by the search filter.
   */
  function getTableData() {
    const headers = [];
    $('#table-list thead tr:last-child th').each(function() {
      headers.push($(this).text().replace(/[\u2191\u2193]/g, '').trim()); // strip sort arrows
    });

    const rows = [];
    $('#table-list tbody tr:visible').each(function() {
      const row = {};
      $(this).find('td').each(function(i) {
        row[headers[i]] = $(this).text().trim();
      });
      rows.push(row);
    });

    return {
      headers,
      rows
    };
  }

  /**
   * Export to Excel (.xlsx) using SheetJS.
   * Includes company name and report title in the header rows.
   */
  function exportToExcel() {
    const {
      headers,
      rows
    } = getTableData();

    if (rows.length === 0) {
      alert('No data available to export.');
      return;
    }

    const companyName = '<?= addslashes($company_detail->company_name ?? '') ?>';
    const companyAddr = '<?= addslashes($company_detail->address ?? '') ?>';
    const reportTitle = 'Vehicle In Premises Report';
    const exportDate = 'Generated: ' + new Date().toLocaleString();
    const colCount = headers.length;

    const wb = XLSX.utils.book_new();

    //  Build raw data array 
    const wsData = [];

    // Row 0: Company Name (centered, merged)
    const nameRow = new Array(colCount).fill('');
    nameRow[0] = companyName;
    wsData.push(nameRow);

    // Row 1: Company Address (centered, merged)
    const addrRow = new Array(colCount).fill('');
    addrRow[0] = companyAddr;
    wsData.push(addrRow);

    // Row 2: Report Title (left) + Generated date (right) — NO merge
    const titleRow = new Array(colCount).fill('');
    titleRow[0] = reportTitle;
    titleRow[colCount - 1] = exportDate;
    wsData.push(titleRow);

    // Row 3: blank spacer
    wsData.push(new Array(colCount).fill(''));

    // Row 4: Column headers
    wsData.push(headers);

    // Row 5+: Data
    rows.forEach(r => wsData.push(headers.map(h => r[h] ?? '')));

    const ws = XLSX.utils.aoa_to_sheet(wsData);

    // Column widths 
    ws['!cols'] = headers.map(h => {
      const maxLen = Math.max(h.length, ...rows.map(r => String(r[h] ?? '').length));
      return {
        wch: Math.min(maxLen + 4, 40)
      };
    });

    // Merges 
    ws['!merges'] = [{
        s: {
          r: 0,
          c: 0
        },
        e: {
          r: 0,
          c: colCount - 1
        }
      }, // Company Name
      {
        s: {
          r: 1,
          c: 0
        },
        e: {
          r: 1,
          c: colCount - 1
        }
      }, // Company Address
    ];

    // Style helpers 
    const blackBorder = {
      top: {
        style: 'thin',
        color: {
          rgb: '000000'
        }
      },
      bottom: {
        style: 'thin',
        color: {
          rgb: '000000'
        }
      },
      left: {
        style: 'thin',
        color: {
          rgb: '000000'
        }
      },
      right: {
        style: 'thin',
        color: {
          rgb: '000000'
        }
      },
    };

    function setCell(r, c, value, style) {
      const ref = XLSX.utils.encode_cell({
        r,
        c
      });
      ws[ref] = {
        v: value,
        t: typeof value === 'number' ? 'n' : 's',
        s: style
      };
    }

    // Row 0: Company Name — centered, bold, large
    setCell(0, 0, companyName, {
      alignment: {
        horizontal: 'center',
        vertical: 'center'
      },
      font: {
        bold: true,
        sz: 14
      },
    });
    for (let c = 1; c < colCount; c++) {
      setCell(0, c, '', {
        alignment: {
          horizontal: 'center'
        }
      });
    }

    // Row 1: Company Address — centered 
    setCell(1, 0, companyAddr, {
      alignment: {
        horizontal: 'center',
        vertical: 'center'
      },
      font: {
        sz: 10
      },
    });
    for (let c = 1; c < colCount; c++) {
      setCell(1, c, '', {
        alignment: {
          horizontal: 'center'
        }
      });
    }

    // Row 2: Title left, Date right 
    setCell(2, 0, reportTitle, {
      alignment: {
        horizontal: 'left',
        vertical: 'center'
      },
      font: {
        bold: true,
        sz: 11
      },
    });
    for (let c = 1; c < colCount - 1; c++) {
      setCell(2, c, '', {});
    }
    setCell(2, colCount - 1, exportDate, {
      alignment: {
        horizontal: 'right',
        vertical: 'center'
      },
      font: {
        sz: 10
      },
    });

    // Row 3: Blank spacer
    for (let c = 0; c < colCount; c++) {
      setCell(3, c, '', {});
    }

    // Row 4: Column headers dark bg, white bold, black border 
    headers.forEach((h, c) => {
      setCell(4, c, h, {
        font: {
          bold: true,
          color: {
            rgb: 'FFFFFF'
          },
          sz: 11
        },
        fill: {
          fgColor: {
            rgb: '50607B'
          },
          patternType: 'solid'
        },
        alignment: {
          horizontal: 'center',
          vertical: 'center'
        },
        border: blackBorder,
      });
    });

    // Rows 5+: Data rows  black border
    rows.forEach((r, rowIdx) => {
      headers.forEach((h, c) => {
        setCell(5 + rowIdx, c, r[h] ?? '', {
          alignment: {
            vertical: 'center'
          },
          border: blackBorder,
        });
      });
    });

    XLSX.utils.book_append_sheet(wb, ws, 'Vehicle In Premises');

    const fileName = 'Vehicle_In_Premises_' + new Date().toISOString().slice(0, 10) + '.xlsx';
    XLSX.writeFile(wb, fileName);
  }


  function exportToCSV() {
    const {
      headers,
      rows
    } = getTableData();

    if (rows.length === 0) {
      alert('No data available to export.');
      return;
    }

    const companyName = '<?= addslashes($company_detail->company_name ?? '') ?>';
    const companyAddr = '<?= addslashes($company_detail->address ?? '') ?>';

    const escapeCSV = val => {
      const str = String(val ?? '');
      return str.includes(',') || str.includes('"') || str.includes('\n') ?
        `"${str.replace(/"/g, '""')}"` :
        str;
    };

    let csv = '';

    // Company name and address as header rows
    if (companyName) csv += escapeCSV(companyName) + '\r\n';
    if (companyAddr) csv += escapeCSV(companyAddr) + '\r\n';
    if (companyName || companyAddr) csv += '\r\n';

    // Column headers row
    csv += headers.map(escapeCSV).join(',') + '\r\n';

    // Data rows — one column per header
    rows.forEach(r => {
      csv += headers.map(h => escapeCSV(r[h] ?? '')).join(',') + '\r\n';
    });

    const blob = new Blob(['\uFEFF' + csv], {
      type: 'text/csv;charset=utf-8;'
    });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    const fileName = 'Vehicle_In_Premises_' + new Date().toISOString().slice(0, 10) + '.csv';

    link.setAttribute('href', url);
    link.setAttribute('download', fileName);
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }
</script>

<script>
  // Sort
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

  // Print 
  function printPage() {
    $('.mainHead').show();
    var printContents = document.getElementById("printableArea").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // restore event handlers after innerHTML swap
  }

  // Search / Filter
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