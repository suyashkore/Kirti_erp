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

#table-list tbody tr:hover{
  background-color: rgb(171, 174, 176);
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
                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Sales</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Sales Quotation List</b></li>
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
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="SalesLocation">
                    <label for="SalesLocation" class="control-label">Sales Location</label>
                    <select name="SalesLocation" id="SalesLocation" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Sales Location">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($saleslocation)) :
                        foreach ($saleslocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] .'</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
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
                <div class="col-md-2 mbot5">
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
                    <select name="status" id="status" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Status">
                      <option value="">All</option>
                      <?php
                      $status = [1 => 'Pending', 2 => 'Cancel', 3 => 'Expired', 4 => 'Approved', 5 => 'Complete', 6 => 'In Progress', 7 => 'Partially Complete'];
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
                  <button type="submit" class="btn btn-success <?= (has_permission_new('salesQuotationList', '', 'view')) ? '' : 'disabled'; ?>" id="searchBtn" onclick="applyClientFilter()"><i class="fa fa-list"></i> Show</button>
                  <button type="button" class="btn btn-info exportBtn <?= (has_permission_new('salesQuotationList', '', 'export')) ? '' : 'disabled'; ?>" onclick="exportTableToExcel()" style="display: none;"><i class="fa fa-file-excel"></i> Excel</button>
                  <button type="button" class="btn btn-info exportBtn <?= (has_permission_new('salesQuotationList', '', 'print')) ? '' : 'disabled'; ?>" onclick="printPage();" style="display: none;"><i class="fa fa-print"></i> Print</button>
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
                        <td colspan="17">
                          <h5 style="text-align:center;">
                            <span style="font-size:15px; font-weight:700;"><?= $company_detail->company_name ?? ''; ?></span><br>
                            <span style="font-size:10px; font-weight:600;"><?= $company_detail->address ?? ''; ?></span>
                          </h5>
                        </td>
                      </tr>
                      <tr class="mainHead" style="display: none;">
                        <td colspan="17">
                          <span class="report_for" style="font-size:10px;"></span>
                        </td>
                      </tr>
                      <tr>
                        <th class="sortable">Quotation No</th>
                        <th class="sortable">Quotation Date</th>
                        <th class="sortable">Customer Name</th>
                        <th class="sortable">Broker Name</th>
                        <th class="sortable">Sales Location</th>
                        <th class="sortable">Quotation Qty</th>
                        <th class="sortable">Quotation Wt</th>
                        <th class="sortable">Order Qty</th>
                        <th class="sortable">Order Wt</th>
                        <th class="sortable">Item Amt</th>
                        <th class="sortable">Disc Amt</th>
                        <th class="sortable">Taxable Amt</th>
                        <th class="sortable">CGST Amt</th>
                        <th class="sortable">SGST Amt</th>
                        <th class="sortable">IGST Amt</th>
                        <th class="sortable">Quotation Amt</th>
                        <th class="sortable">Status</th>
                        <th>Action</th>
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

<!-- ===== Confirm Cancel Modal ===== -->
<div class="modal fade" id="confirmCancelModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document" style="margin-top: 15%;">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#f0ad4e; padding:10px 15px;">
        <h5 class="modal-title" style="color:#fff; font-size:14px; font-weight:700;">
          <i class="fa fa-exclamation-triangle"></i> Confirm Cancel
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="padding: 20px 15px;">
        <i class="fa fa-exclamation-circle" style="font-size:40px; color:#f0ad4e;"></i>
        <p style="margin-top:12px; font-size:13px; font-weight:600;">Are you sure you want to cancel</p>
        <p style="font-size:14px; font-weight:700; color:#f0ad4e;">Quotation No: <span id="cancelQuotationId"></span> ?</p>
        <p style="font-size:11px; color:#888;">This action cannot be undone.</p>
      </div>
      <div class="modal-footer" style="padding: 8px 15px; text-align:center;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
          <i class="fa fa-ban"></i> No, Go Back
        </button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmCancelBtn">
          <i class="fa fa-check"></i> Yes, Cancel It
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ===== Confirm Partially Complete Modal ===== -->
<div class="modal fade" id="confirmPartialModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document" style="margin-top: 15%;">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#5bc0de; padding:10px 15px;">
        <h5 class="modal-title" style="color:#fff; font-size:14px; font-weight:700;">
          <i class="fa fa-info-circle"></i> Confirm Partially Complete
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="padding: 20px 15px;">
        <i class="fa fa-info-circle" style="font-size:40px; color:#5bc0de;"></i>
        <p style="margin-top:12px; font-size:13px; font-weight:600;">Are you sure you want to mark as Partially Complete</p>
        <p style="font-size:14px; font-weight:700; color:#5bc0de;">Quotation No: <span id="partialQuotationId"></span> ?</p>
        <p style="font-size:11px; color:#888;">This action cannot be undone.</p>
      </div>
      <div class="modal-footer" style="padding: 8px 15px; text-align:center;">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
          <i class="fa fa-ban"></i> No, Go Back
        </button>
        <button type="button" class="btn btn-info btn-sm" id="confirmPartialBtn">
          <i class="fa fa-check"></i> Yes, Confirm
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ===== Success Modal ===== -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document" style="margin-top: 15%;">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#5cb85c; padding:10px 15px;">
        <h5 class="modal-title" style="color:#fff; font-size:14px; font-weight:700;">
          <i class="fa fa-check-circle"></i> Success
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="padding: 20px 15px;">
        <i class="fa fa-check-circle" style="font-size:40px; color:#5cb85c;"></i>
        <p style="margin-top:12px; font-size:13px; font-weight:600;" id="successModalMsg"></p>
      </div>
      <div class="modal-footer" style="padding: 8px 15px; text-align:center;">
        <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">
          <i class="fa fa-check"></i> OK
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ===== Error Modal ===== -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document" style="margin-top: 15%;">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#d9534f; padding:10px 15px;">
        <h5 class="modal-title" style="color:#fff; font-size:14px; font-weight:700;">
          <i class="fa fa-times-circle"></i> Error
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:1;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="padding: 20px 15px;">
        <i class="fa fa-times-circle" style="font-size:40px; color:#d9534f;"></i>
        <p style="margin-top:12px; font-size:13px; font-weight:600;" id="errorModalMsg"></p>
      </div>
      <div class="modal-footer" style="padding: 8px 15px; text-align:center;">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">
          <i class="fa fa-check"></i> OK
        </button>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>

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
     var locationFilter = $('#SalesLocation').val();
    var fromDate       = parseDate($('#from_date').val());
    var toDate         = parseDate($('#to_date').val());

    $('#table-list tbody tr.no-data-row').remove();

    var visibleCount = 0;

    $('#table-list tbody tr').each(function() {
        var $row = $(this);

        var rowStatus   = $row.data('status')   ? String($row.data('status'))   : '';
        var rowCustomer = $row.data('customer') ? String($row.data('customer')) : '';
        var rowBroker   = $row.data('broker')   ? String($row.data('broker'))   : '';
        var rowLocation = $row.data('location') ? String($row.data('location')) : '';
        var rowDate     = parseDate($row.data('date'));

        var dateMatch     = (!fromDate || !toDate) || (rowDate >= fromDate && rowDate <= toDate);
        var customerMatch = !customerFilter || rowCustomer === customerFilter;
        var brokerMatch   = !brokerFilter   || rowBroker === brokerFilter;
         var locationMatch = !locationFilter || rowLocation === locationFilter; 
        var statusMatch   = !statusFilter   || rowStatus === statusFilter;

        var isVisible = dateMatch && customerMatch && brokerMatch && locationMatch && statusMatch;
        $row.toggle(isVisible);

        if (isVisible) visibleCount++;
    });

    if (visibleCount === 0 && $('#table-list tbody tr').length > 0) {
        $('#table-list tbody').append(
            '<tr class="no-data-row"><td colspan="17" class="text-center">No Data Found</td></tr>'
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
    $('#status').val('4').selectpicker('refresh');
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
        form_data.append('limit', limit);
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
                            '<tr><td colspan="17" class="text-center">No Data Found</td></tr>'
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
    let status_list = {1:'Pending', 2:'Cancel', 3:'Expired', 4:'Approved', 5:'Complete', 6:'In Progress', 7:'Partially Complete'};

    rows.forEach(function(row){
        var d = new Date(row.TransDate);
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var yyyy = d.getFullYear();
        var dateStr = dd + '/' + mm + '/' + yyyy;

        // ---- Action column logic ----
        // Cancel btn: visible when Status is Pending(1) or Approved(4)
        // Partially Complete btn: visible when Status is In Progress(6)
        let actionTd = '';
        if (row.Status == 1 || row.Status == 4) {
            actionTd = `<td class="text-center">
                <button type="button" class="btn btn-danger btn-xs"
                    onclick="cancelQuotation('${row.QuotationID}')"
                    title="Cancel Quotation">
                    <i class="fa fa-times"></i> Cancel
                </button>
            </td>`;
        } else if (row.Status == 6) {
            actionTd = `<td class="text-center">
                <button type="button" class="btn btn-info btn-xs"
                    onclick="partiallyCompleteQuotation('${row.QuotationID}')"
                    title="Mark as Partially Complete">
                    <i class="fa fa-check-square-o"></i> Partially
                </button>
            </td>`;
        } else {
            actionTd = `<td></td>`;
        }

        html += `<tr data-status="${row.Status || ''}"
            data-customer="${row.AccountID || ''}"
            data-broker="${row.BrokerID || ''}"
            data-location="${row.SalesLocation || ''}"
            data-date="${dateStr}"
            style="cursor:pointer;"
            onclick="window.open('<?= admin_url('SalesQuotation'); ?>?id=${row.id}', '_blank')">
            <td class="text-center">${row.QuotationID}</td>
            <td>${moment(row.TransDate).format('DD/MM/YYYY')}</td>
            <td>${row.customer_name} - ${row.billing_state} (${row.AccountID})</td>
            <td>${row.broker_name || ''} - ${row.broker_state} (${row.BrokerID || ''})</td>
            <td>${row.SalesLocationName || ''}</td>
            <td class="text-center">${Number(row.TotalQuantity) || '-'}</td>
            <td class="text-center">${Number(row.TotalWeight / 100) || '-'}</td>
            <td class="text-center">${Number(row.so_total_qty) || '-'}</td>
            <td class="text-center">${Number(row.so_total_weight / 100) || '-'}</td>
            <td class="text-center">${Number(row.ItemAmt) || '-'}</td>
            <td class="text-center">${Number(row.DiscAmt) || '-'}</td>
            <td class="text-center">${Number(row.TaxableAmt) || '-'}</td>
            <td class="text-center">${Number(row.CGSTAmt) || '-'}</td>
            <td class="text-center">${Number(row.SGSTAmt) || '-'}</td>
            <td class="text-center">${Number(row.IGSTAmt) || '-'}</td>
            <td class="text-center">${Number(row.NetAmt) || '-'}</td>
            <td class="text-center">${status_list[row.Status] || 'Pending'}</td>
            ${actionTd}
        </tr>`;
    });
    $('#table-list tbody').append(html);
}

// ===== Cancel Quotation =====
function cancelQuotation(quotationId) {
    $('#cancelQuotationId').text(quotationId);
    $('#confirmCancelModal').modal('show');

    $('#confirmCancelBtn').off('click').on('click', function () {
        $('#confirmCancelModal').modal('hide');

        let csrfName  = '<?= $this->security->get_csrf_token_name(); ?>';
        let csrfToken = $('input[name="' + csrfName + '"]').val();

        $.ajax({
            url: '<?= admin_url("SalesQuotation/cancelQuotation"); ?>',
            type: 'POST',
            data: {
                quotation_id: quotationId,
                [csrfName]: csrfToken
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    showSuccessModal('Quotation No: ' + quotationId + ' cancelled successfully!');
                } else {
                    showErrorModal(res.message || 'Something went wrong!');
                }
            },
            error: function () {
                showErrorModal('Server error. Please try again.');
            }
        });
    });
}

// ===== Partially Complete Quotation =====
function partiallyCompleteQuotation(quotationId) {
    $('#partialQuotationId').text(quotationId);
    $('#confirmPartialModal').modal('show');

    $('#confirmPartialBtn').off('click').on('click', function () {
        $('#confirmPartialModal').modal('hide');

        let csrfName  = '<?= $this->security->get_csrf_token_name(); ?>';
        let csrfToken = $('input[name="' + csrfName + '"]').val();

        $.ajax({
            url: '<?= admin_url("SalesQuotation/partiallyCompleteQuotation"); ?>',
            type: 'POST',
            data: {
                quotation_id: quotationId,
                [csrfName]: csrfToken
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    showSuccessModal('Quotation No: ' + quotationId + ' marked as Partially Complete successfully!');
                } else {
                    showErrorModal(res.message || 'Something went wrong!');
                }
            },
            error: function () {
                showErrorModal('Server error. Please try again.');
            }
        });
    });
}

// ===== Modal Helpers =====
function showSuccessModal(message) {
    $('#successModalMsg').text(message);
    $('#successModal').modal('show');
    // Refresh list after closing success modal
    $('#successModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
        $('#filter_list_form').submit();
    });
}

function showErrorModal(message) {
    $('#errorModalMsg').text(message);
    $('#errorModal').modal('show');
}

function updateProgress(loaded, total){
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
          window.location.href = res.file_url;
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