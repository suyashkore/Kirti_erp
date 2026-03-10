<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
.tableFixHead2 { overflow: auto; max-height: 65vh; }
table { border-collapse: collapse; width: 100%; }
th, td { padding: 4px 6px !important; white-space: nowrap; border:1px solid #ccc !important; font-size:11px; line-height:1.42857143 !important; vertical-align: middle !important;}
th { background: #50607b; color: #fff !important; position: sticky; top: 0; z-index: 1; }
.sortable { cursor: pointer; }
.get_Details { cursor: pointer; }
.get_Details:hover { background-color: #eaf3fb !important; }
.get_Details.processing { pointer-events: none; opacity: 0.7; }
.progress { margin-bottom: 5px; height: 3px; }
@media only screen and (max-width: 600px) { #header ul { display: none !important; } }
</style>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Mandi Purchase List</b></li>
              </ol>
            </nav>
            <hr class="hr_style">

            <!-- Filter Form -->
            <form action="" method="post" id="filter_list_form">

              <!-- Hidden Company Info -->
              <input type="hidden" id="cname" value="<?= $COMPANY[0]['company_name'] ?>">
              <input type="hidden" id="add"   value="<?= $COMPANY[0]['address'] ?>">
              <input type="hidden" id="gst"   value="<?= $COMPANY[0]['gst'] ?>">
              <input type="hidden" id="email" value="<?= $COMPANY[0]['BusinessEmail'] ?>">
              <input type="hidden" id="no"    value="<?= $COMPANY[0]['mobile1'] ?>">

              <div class="row">

                <!-- From Date -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="from_date">
                    <?= render_date_input('from_date','From Date', date('01/m/Y'), []); ?>
                  </div>
                  <!-- <div class="form-group">
                    <label class="control-label">From Date</label>
                    <div class="input-group date">
                      <input type="text" id="from_date" name="from_date" class="form-control datepicker" value="<?= date("01/m/Y") ?>">
                      <div class="input-group-addon"><i class="fa-regular fa-calendar calendar-icon"></i></div>
                    </div>
                  </div> -->
                </div>

                <!-- To Date -->
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="to_date">
                    <?= render_date_input('to_date','To Date', date('d/m/Y'), []); ?>
                  </div>
                  <!-- <div class="form-group">
                    <label class="control-label">To Date</label>
                    <div class="input-group date">
                      <input type="text" id="to_date" name="to_date" class="form-control datepicker" value="<?= date("d/m/Y") ?>">
                      <div class="input-group-addon"><i class="fa-regular fa-calendar calendar-icon"></i></div>
                    </div>
                  </div> -->
                </div>

                <!-- Center / Location -->
                <div class="col-md-2 mbot5">
                  <div class="form-group">
                    <label class="control-label">Center / Location</label>
                    <select name="filter_location_id" id="filter_location_id" class="form-control selectpicker" data-live-search="true">
                      <option value="">All Locations</option>
                      <?php
                      if (!empty($purchaselocation)):
                        foreach ($purchaselocation as $value):
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>

                <!-- Godown -->
                <div class="col-md-2 mbot5">
                  <div class="form-group">
                    <label class="control-label">Godown</label>
                    <select name="filter_godown_id" id="filter_godown_id" class="form-control selectpicker" data-live-search="true">
                      <option value="">All Godowns</option>
                      <?php
                      if (!empty($godown_list)):
                        foreach ($godown_list as $value):
                          echo '<option value="' . $value['id'] . '">' . $value['GodownName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>

                <!-- Item ID -->
                <div class="col-md-2 mbot5">
                  <div class="form-group">
                    <label class="control-label">Item ID</label>
                    <select name="filter_item_id" id="filter_item_id" class="form-control selectpicker" data-live-search="true">
                      <option value="">All Items</option>
                      <?php
                      if (!empty($Items)):
                        foreach ($Items as $value):
                          echo '<option value="' . $value['ItemID'] . '">' . $value['ItemName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>

                <!-- Show / Reset -->
                <div class="col-md-2 mbot5" style="padding-top:20px;">
                  <button type="submit" class="btn btn-success" id="searchBtn">
                    <i class="fa fa-search"></i> Show
                  </button>
                  <button type="button" class="btn btn-default" onclick="resetFilters();">
                    <i class="fa fa-refresh"></i> Reset
                  </button>
                </div>

              </div>

              <!-- Print/Excel + Search -->
              <div class="row" style="margin-bottom:5px;">
                <div class="col-md-3 mbot5" style="padding-top:5px;">
                  <button type="button" class="btn btn-info btn-sm" onclick="printTable();" title="Print Table">
                    <i class="fa fa-print"></i> Print
                  </button>
                  &nbsp;
                  <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel();" title="Export to Excel">
                    <i class="fa fa-file-excel-o"></i> Excel
                  </button>
                </div>
                <div class="col-md-3 col-md-offset-6 mbot5" style="padding-top:5px;">
                  <input type="search" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search in table..." title="Type in a table">
                </div>
              </div>

            </form>

            <!-- Progress Bar -->
            <div class="progress">
              <div id="fetchProgress" class="progress-bar" style="width:0%"></div>
            </div>

            <!-- List Table -->
            <div class="tableFixHead2">
              <table class="table table-striped table-bordered" id="table_ListModal" width="100%">
                <thead>
                  <tr>
                    <th class="sortable">#</th>
                    <th class="sortable">Order ID</th>
                    <th class="sortable">Order Date</th>
                    <th class="sortable">Trans Date</th>
                    <th class="sortable">Center / Location</th>
                    <th class="sortable">Warehouse</th>
                    <th class="sortable">Item</th>
                    <th class="sortable">Vehicle No.</th>
                    <th class="sortable">Final Amt</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="table_ListModal_body">
                  <tr><td colspan="10" class="text-center text-muted">Loading...</td></tr>
                </tbody>
              </table>
            </div>

            <div id="printableArea" style="display:none;"></div>

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

  // =============================================
  // COMPANY INFO
  // =============================================
  function getCompanyInfo() {
    return {
      name    : $('#cname').val() || '',
      address : $('#add').val()   || '',
      phone   : $('#no').val()    || '',
      email   : $('#email').val() || '',
      gst     : $('#gst').val()   || ''
    };
  }

  // =============================================
  // ACTIVE FILTERS
  // =============================================
  function getActiveFilters() {
    var filters = [];
    var fromDate = $('#from_date').val();
    var toDate   = $('#to_date').val();
    if (fromDate || toDate) {
      filters.push({ label: 'Period', value: (fromDate || '-') + ' to ' + (toDate || '-') });
    }
    if ($('#filter_location_id').val()) {
      filters.push({ label: 'Location', value: $('#filter_location_id option:selected').text().trim() });
    }
    if ($('#filter_godown_id').val()) {
      filters.push({ label: 'Godown', value: $('#filter_godown_id option:selected').text().trim() });
    }
    if ($('#filter_item_id').val()) {
      filters.push({ label: 'Item', value: $('#filter_item_id option:selected').text().trim() });
    }
    return filters;
  }

  function getFilterString() {
    var af = getActiveFilters();
    if (af.length === 0) return 'All Records';
    var parts = [];
    $.each(af, function(i, f) { parts.push('<b>' + f.label + ':</b> ' + f.value); });
    return parts.join(' &nbsp;&nbsp;|&nbsp;&nbsp; ');
  }

  function getFilterStringPlain() {
    var af = getActiveFilters();
    if (af.length === 0) return 'All Records';
    var parts = [];
    $.each(af, function(i, f) { parts.push(f.label + ': ' + f.value); });
    return parts.join('   |   ');
  }

  // =============================================
  // INIT
  // =============================================
  $(document).ready(function () {
    $('#filter_location_id').selectpicker({ liveSearch: true });
    $('#filter_godown_id').selectpicker({ liveSearch: true });
    $('#filter_item_id').selectpicker({ liveSearch: true });
    $('#filter_list_form').submit();
  });

  // =============================================
  // LOCATION → GODOWN AJAX
  // =============================================
  function filterGodownByLocation(location_id) {
    $('#filter_godown_id').selectpicker('destroy');
    $('#filter_godown_id').html('<option value="">All Godowns</option>');
    $('#filter_godown_id').selectpicker();
    if (!location_id) return;
    $.ajax({
      url: '<?= admin_url("purchase/Mandi/getGodownByLocation"); ?>',
      type: 'POST', dataType: 'json',
      data: {
        location_id: location_id,
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      success: function (response) {
        if (response.success && response.data.length > 0) {
          var html = '<option value="">All Godowns</option>';
          $.each(response.data, function (i, v) {
            html += '<option value="' + v.id + '">' + v.GodownName + '</option>';
          });
          $('#filter_godown_id').selectpicker('destroy');
          $('#filter_godown_id').html(html);
          $('#filter_godown_id').selectpicker();
        }
      }
    });
  }

  // =============================================
  // RESET
  // =============================================
  function resetFilters() {
    $('#from_date').val('<?= date("01/m/Y") ?>');
    $('#to_date').val('<?= date("d/m/Y") ?>');
    $('#filter_location_id').val('').selectpicker('refresh');
    $('#filter_godown_id').selectpicker('destroy');
    $('#filter_godown_id').html('<option value="">All Godowns</option>');
    $('#filter_godown_id').selectpicker();
    $('#filter_item_id').val('').selectpicker('refresh');
    $('#myInput1').val('');
    $('#filter_list_form').submit();
  }

  // =============================================
  // FETCH LIST
  // =============================================
  $('#filter_list_form').on('submit', function (e) {
    e.preventDefault();
    $('#searchBtn').prop('disabled', true);
    $('#table_ListModal_body').html('<tr><td colspan="10" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
    $.ajax({
      url: '<?= admin_url("purchase/Mandi/GetMandiDetails"); ?>',
      type: 'POST', dataType: 'json',
      data: {
        from_date       : $('#from_date').val(),
        to_date         : $('#to_date').val(),
        filter_location : $('#filter_location_id').val(),
        filter_godown   : $('#filter_godown_id').val(),
        filter_item     : $('#filter_item_id').val(),
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      success: function (response) {
        $('#searchBtn').prop('disabled', false);
        $('#fetchProgress').css('width', '100%');
        setTimeout(function () { $('#fetchProgress').css('width', '0%'); }, 500);
        if (!response.success || !response.data || response.data.length === 0) {
          $('#table_ListModal_body').html('<tr><td colspan="10" class="text-center text-muted">No records found.</td></tr>');
          return;
        }
        var html = '';
        $.each(response.data, function (i, row) {
          var orderDate = row.OrderDate ? moment(row.OrderDate).format('DD/MM/YYYY') : '-';
          var transDate = row.TransDate ? moment(row.TransDate).format('DD/MM/YYYY') : '-';
          var finalAmt  = row.FinalAmt  ? parseFloat(row.FinalAmt).toFixed(2) : '0.00';
          var editUrl   = '<?= admin_url("purchase/Mandi/index"); ?>?order_id=' + encodeURIComponent(row.OrderID) + '&id=' + encodeURIComponent(row.id);
          html += '<tr class="get_Details" onclick="redirectToEdit(' + row.id + ', \'' + row.OrderID + '\', this)">' +
            '<td>' + (i + 1) + '</td>' +
            '<td>' + (row.OrderID || '-') + '</td>' +
            '<td>' + orderDate + '</td>' +
            '<td>' + transDate + '</td>' +
            '<td>' + (row.CenterLocation || '-') + '</td>' +
            '<td>' + (row.WarehouseID || '-') + '</td>' +
            '<td>' + (row.ItemID || '-') + '</td>' +
            '<td>' + (row.VehicleNo || '-') + '</td>' +
            '<td class="text-right">' + finalAmt + '</td>' +
            '<td style="text-align:center;">' +
              '<a href="' + editUrl + '" class="btn btn-xs btn-primary" title="Edit Record" onclick="event.stopPropagation();">' +
                '<i class="fa fa-edit"></i> Edit' +
              '</a>' +
            '</td>' +
          '</tr>';
        });
        $('#table_ListModal_body').html(html);
      },
      error: function () {
        $('#searchBtn').prop('disabled', false);
        $('#table_ListModal_body').html('<tr><td colspan="10" class="text-center text-danger">Error loading data.</td></tr>');
      }
    });
  });

  // =============================================
  // ROW CLICK
  // =============================================
  function redirectToEdit(id, orderId, rowEl) {
    var $row = $(rowEl).closest('tr');
    if ($row.hasClass('processing')) return;
    $row.addClass('processing');
    $row.find('td').css('background-color', '#d4edda');
    window.location.href = '<?= admin_url("purchase/Mandi/index"); ?>?order_id=' + encodeURIComponent(orderId) + '&id=' + encodeURIComponent(id);
  }

  // =============================================
  // SORT
  // =============================================
  $(document).on("click", ".sortable", function () {
    var table = $("#table_ListModal tbody"), rows = table.find("tr").toArray();
    var index = $(this).index(), ascending = !$(this).hasClass("asc");
    $(".sortable").removeClass("asc desc"); $(".sortable span").remove();
    $(this).addClass(ascending ? "asc" : "desc");
    $(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
    rows.sort(function (a, b) {
      var valA = $(a).find("td").eq(index).text().trim();
      var valB = $(b).find("td").eq(index).text().trim();
      if ($.isNumeric(valA) && $.isNumeric(valB)) { return ascending ? valA - valB : valB - valA; }
      return ascending ? valA.localeCompare(valB) : valB.localeCompare(valA);
    });
    table.append(rows);
  });

  // =============================================
  // TABLE SEARCH
  // =============================================
  function myFunction2() {
    var filter = document.getElementById("myInput1").value.toUpperCase();
    var tr = document.getElementById("table_ListModal_body").getElementsByTagName("tr");
    for (var i = 0; i < tr.length; i++) {
      var tds = tr[i].getElementsByTagName("td"), rowMatch = false;
      for (var j = 0; j < tds.length; j++) {
        if ((tds[j].textContent || tds[j].innerText).toUpperCase().indexOf(filter) > -1) { rowMatch = true; break; }
      }
      tr[i].style.display = rowMatch ? "" : "none";
    }
  }

  // =============================================
  // PRINT
  // =============================================
  function printPage() {
    var printContents = document.getElementById("printableArea").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
  }

  function printTable() {
    var C = getCompanyInfo();
    var today = new Date().toLocaleDateString('en-IN');
    var filterStr = getFilterString();

    var tableHtml = '<table class="data-table"><thead><tr>';
    $('#table_ListModal thead th').each(function () {
      var txt = $(this).text().replace(/[↑↓▲▼]/g, '').trim();
      if (txt !== 'Action') tableHtml += '<th>' + txt + '</th>';
    });
    tableHtml += '</tr></thead><tbody>';
    var rowIdx = 0;
    $('#table_ListModal tbody tr:visible').each(function () {
      tableHtml += '<tr class="' + (rowIdx % 2 === 1 ? 'alt' : '') + '">';
      var cells = $(this).find('td');
      cells.each(function (i) {
        if (i < cells.length - 1) tableHtml += '<td>' + $(this).text().trim() + '</td>';
      });
      tableHtml += '</tr>';
      rowIdx++;
    });
    tableHtml += '</tbody></table>';

    var fullHtml =
      '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Mandi Purchase List</title>' +
      '<style>' +
        '* { margin:0; padding:0; box-sizing:border-box; }' +
        'body { font-family:Arial,sans-serif; font-size:10px; color:#222; background:#fff; }' +
        '.pw { width:100%; padding:6mm; }' +
        '.co-block { text-align:center; border-bottom:2px solid #50607b; padding-bottom:5px; margin-bottom:5px; }' +
        '.co-name  { font-size:15px; font-weight:bold; color:#50607b; margin-bottom:2px; }' +
        '.co-addr  { font-size:8.5px; color:#555; margin-bottom:2px; }' +
        '.co-meta  { font-size:8.5px; color:#444; }' +
        '.co-meta span { margin:0 10px; }' +
        '.rpt-name { font-size:12px; font-weight:bold; color:#50607b; margin-top:4px; }' +
        '.f-bar { font-size:8.5px; color:#333; background:#f0f4f8; border:1px solid #dde3eb; border-radius:3px; padding:3px 8px; margin-bottom:5px; }' +
        '.f-bar b { color:#50607b; }' +
        '.f-lbl { font-weight:bold; color:#50607b; margin-right:5px; }' +
        '.data-table { width:100%; border-collapse:collapse; font-size:9px; }' +
        '.data-table thead th { background:#50607b; color:#fff; padding:4px 5px; text-align:left; border:1px solid #3d4f63; }' +
        '.data-table tbody td { padding:3px 5px; border:1px solid #ccc; vertical-align:middle; }' +
        '.data-table tbody tr.alt td { background:#f0f4f8; }' +
        '.pf { margin-top:7px; font-size:8px; color:#999; display:flex; justify-content:space-between; border-top:1px solid #ddd; padding-top:3px; }' +
        '@page { size:A4 landscape; margin:8mm; }' +
        '@media print { body{-webkit-print-color-adjust:exact;print-color-adjust:exact;} thead{display:table-header-group;} tbody tr{page-break-inside:avoid;} }' +
      '</style></head><body><div class="pw">' +
      '<div class="co-block">' +
        '<div class="co-name">' + C.name + '</div>' +
        '<div class="co-addr">' + C.address + '</div>' +
        '<div class="co-meta">' +
          '<span>&#128222; ' + C.phone + '</span>' +
          '<span>&#9993; ' + C.email + '</span>' +
          '<span>&#128196; GST: ' + C.gst + '</span>' +
        '</div>' +
        '<div class="rpt-name">Mandi Purchase List</div>' +
      '</div>' +
      '<div class="f-bar"><span class="f-lbl">Filters:</span>' + filterStr + '</div>' +
      tableHtml +
      '<div class="pf"><span>Generated by System</span><span>Mandi Purchase List | ' + today + '</span></div>' +
      '</div></body></html>';

    $('#printableArea').html(fullHtml);
    printPage();
  }

  // =============================================
  // EXCEL EXPORT
  // CORRECT CDN: unpkg.com/xlsx-js-style@1.2.0
  // =============================================
  function exportToExcel() {
    if (typeof XLSXStyle !== 'undefined') {
      doExcelExport();
      return;
    }
    // Step 1: Load base XLSX first
    if (typeof XLSX === 'undefined') {
      var s1 = document.createElement('script');
      s1.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
      s1.onload = function () { loadXlsxStyle(); };
      document.head.appendChild(s1);
    } else {
      loadXlsxStyle();
    }
  }

  function loadXlsxStyle() {
    var s2 = document.createElement('script');
    // CORRECT unpkg URL for xlsx-js-style
    s2.src = 'https://unpkg.com/xlsx-js-style@1.2.0/dist/xlsx-js-style.min.js';
    s2.onload = function () { doExcelExport(); };
    s2.onerror = function () {
      // Fallback: use plain XLSX without styling
      console.warn('xlsx-js-style failed to load, using plain XLSX');
      doExcelExportPlain();
    };
    document.head.appendChild(s2);
  }

  // =============================================
  // EXCEL WITH STYLING (xlsx-js-style)
  // =============================================
  function doExcelExport() {
    var XS = (typeof XLSXStyle !== 'undefined') ? XLSXStyle : XLSX;

    var C = getCompanyInfo();
    var today = new Date().toLocaleDateString('en-IN');
    var filterStr = getFilterStringPlain();

    var headers = [];
    $('#table_ListModal thead th').each(function () {
      var txt = $(this).text().replace(/[↑↓▲▼]/g, '').trim();
      if (txt !== 'Action') headers.push(txt);
    });
    var maxCol = headers.length - 1;

    // ---- Cell Styles ----
    var sCoName = {
      font      : { bold: true, sz: 14, color: { rgb: '50607b' } },
      alignment : { horizontal: 'center', vertical: 'center', wrapText: true }
    };
    var sCoSub = {
      font      : { sz: 10, color: { rgb: '444444' } },
      alignment : { horizontal: 'center', vertical: 'center', wrapText: true }
    };
    var sTitle = {
      font      : { bold: true, sz: 12, color: { rgb: '50607b' } },
      alignment : { horizontal: 'center', vertical: 'center' }
    };
    var sDate = {
      font      : { sz: 9, color: { rgb: '777777' } },
      alignment : { horizontal: 'center', vertical: 'center' }
    };
    var sFilter = {
      font      : { sz: 9, color: { rgb: '333333' } },
      fill      : { patternType: 'solid', fgColor: { rgb: 'EEF2F8' } },
      alignment : { horizontal: 'left', vertical: 'center', wrapText: true }
    };
    var sBlank = { font: { sz: 6 } };
    var sTblHeader = {
      font      : { bold: true, sz: 10, color: { rgb: 'FFFFFF' } },
      fill      : { patternType: 'solid', fgColor: { rgb: '50607b' } },
      alignment : { horizontal: 'center', vertical: 'center' },
      border    : {
        top    : { style: 'thin', color: { rgb: '3d4f63' } },
        bottom : { style: 'thin', color: { rgb: '3d4f63' } },
        left   : { style: 'thin', color: { rgb: '3d4f63' } },
        right  : { style: 'thin', color: { rgb: '3d4f63' } }
      }
    };
    var sTblEven = {
      font   : { sz: 9 },
      border : {
        top    : { style: 'thin', color: { rgb: 'cccccc' } },
        bottom : { style: 'thin', color: { rgb: 'cccccc' } },
        left   : { style: 'thin', color: { rgb: 'cccccc' } },
        right  : { style: 'thin', color: { rgb: 'cccccc' } }
      }
    };
    var sTblOdd = {
      font   : { sz: 9 },
      fill   : { patternType: 'solid', fgColor: { rgb: 'F0F4F8' } },
      border : {
        top    : { style: 'thin', color: { rgb: 'cccccc' } },
        bottom : { style: 'thin', color: { rgb: 'cccccc' } },
        left   : { style: 'thin', color: { rgb: 'cccccc' } },
        right  : { style: 'thin', color: { rgb: 'cccccc' } }
      }
    };

    // ---- Header rows definition ----
    // r=0  Company Name
    // r=1  Address
    // r=2  Phone | Email | GST
    // r=3  Report Title
    // r=4  Print Date
    // r=5  (blank)
    // r=6  Filters
    // r=7  (blank)
    // r=8  Table headers
    // r=9+ Data

    var headerDefs = [
      { v: C.name,                                                              s: sCoName  },
      { v: C.address,                                                           s: sCoSub   },
      { v: 'Ph: ' + C.phone + '   |   ' + C.email + '   |   GST: ' + C.gst,  s: sCoSub   },
      { v: 'Mandi Purchase List',                                               s: sTitle   },
      { v: 'Print Date: ' + today,                                              s: sDate    },
      { v: '',                                                                  s: sBlank   },
      { v: 'Filters: ' + filterStr,                                             s: sFilter  },
      { v: '',                                                                  s: sBlank   }
    ];

    var wsData = [];
    headerDefs.forEach(function(hd) { wsData.push([hd.v]); });
    wsData.push(headers); // row 8

    var dataStartRow = wsData.length; // 9
    $('#table_ListModal tbody tr:visible').each(function () {
      var row = [];
      var cells = $(this).find('td');
      cells.each(function (i) {
        if (i < cells.length - 1) row.push($(this).text().trim());
      });
      if (row.length > 0) wsData.push(row);
    });

    var ws = XS.utils.aoa_to_sheet(wsData);

    // Merges: rows 0-4 and 6 merge across all columns
    ws['!merges'] = [
      { s:{r:0,c:0}, e:{r:0,c:maxCol} },
      { s:{r:1,c:0}, e:{r:1,c:maxCol} },
      { s:{r:2,c:0}, e:{r:2,c:maxCol} },
      { s:{r:3,c:0}, e:{r:3,c:maxCol} },
      { s:{r:4,c:0}, e:{r:4,c:maxCol} },
      { s:{r:6,c:0}, e:{r:6,c:maxCol} }
    ];

    // Apply styles to header section (col 0, rows 0-7)
    headerDefs.forEach(function(hd, r) {
      var ref = XS.utils.encode_cell({ r: r, c: 0 });
      if (ws[ref]) ws[ref].s = hd.s;
    });

    // Apply styles to table header row (row 8)
    headers.forEach(function(h, c) {
      var ref = XS.utils.encode_cell({ r: 8, c: c });
      if (ws[ref]) ws[ref].s = sTblHeader;
    });

    // Apply styles to data rows
    for (var r = dataStartRow; r < wsData.length; r++) {
      var isOdd = (r - dataStartRow) % 2 === 1;
      for (var c = 0; c <= maxCol; c++) {
        var ref = XS.utils.encode_cell({ r: r, c: c });
        if (ws[ref]) ws[ref].s = isOdd ? sTblOdd : sTblEven;
      }
    }

    // Column widths
    ws['!cols'] = headers.map(function (h) { return { wch: Math.max(h.length + 4, 16) }; });

    // Row heights
    ws['!rows'] = [
      { hpt: 22 }, // 0 Company Name
      { hpt: 15 }, // 1 Address
      { hpt: 15 }, // 2 Ph/Email/GST
      { hpt: 18 }, // 3 Title
      { hpt: 13 }, // 4 Date
      { hpt: 4  }, // 5 blank
      { hpt: 14 }, // 6 Filters
      { hpt: 4  }, // 7 blank
      { hpt: 16 }  // 8 Table Header
    ];

    var wb = XS.utils.book_new();
    XS.utils.book_append_sheet(wb, ws, 'Mandi Purchase');

    var d = new Date();
    XS.writeFile(wb, 'MandiPurchase_' + d.getDate() + '-' + (d.getMonth()+1) + '-' + d.getFullYear() + '.xlsx');
  }

  // =============================================
  // FALLBACK: Plain XLSX (no styling)
  // =============================================
  function doExcelExportPlain() {
    var C = getCompanyInfo();
    var today = new Date().toLocaleDateString('en-IN');
    var filterStr = getFilterStringPlain();
    var headers = [];
    $('#table_ListModal thead th').each(function () {
      var txt = $(this).text().replace(/[↑↓▲▼]/g, '').trim();
      if (txt !== 'Action') headers.push(txt);
    });
    var maxCol = headers.length - 1;
    var data = [
      [C.name],
      [C.address],
      ['Ph: ' + C.phone + '   |   ' + C.email + '   |   GST: ' + C.gst],
      ['Mandi Purchase List'],
      ['Print Date: ' + today],
      [''],
      ['Filters: ' + filterStr],
      [''],
      headers
    ];
    $('#table_ListModal tbody tr:visible').each(function () {
      var row = [];
      var cells = $(this).find('td');
      cells.each(function (i) {
        if (i < cells.length - 1) row.push($(this).text().trim());
      });
      if (row.length > 0) data.push(row);
    });
    var ws = XLSX.utils.aoa_to_sheet(data);
    ws['!merges'] = [
      { s:{r:0,c:0}, e:{r:0,c:maxCol} },
      { s:{r:1,c:0}, e:{r:1,c:maxCol} },
      { s:{r:2,c:0}, e:{r:2,c:maxCol} },
      { s:{r:3,c:0}, e:{r:3,c:maxCol} },
      { s:{r:4,c:0}, e:{r:4,c:maxCol} },
      { s:{r:6,c:0}, e:{r:6,c:maxCol} }
    ];
    ws['!cols'] = headers.map(function (h) { return { wch: Math.max(h.length + 4, 16) }; });
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Mandi Purchase');
    var d = new Date();
    XLSX.writeFile(wb, 'MandiPurchase_' + d.getDate() + '-' + (d.getMonth()+1) + '-' + d.getFullYear() + '.xlsx');
  }

</script>