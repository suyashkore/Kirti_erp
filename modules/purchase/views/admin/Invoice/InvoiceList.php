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
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li><li class="breadcrumb-item active" aria-current="page"><b>Invoice List</b></li>
							</ol>
						</nav>
            <hr class="hr_style">
            <form action="" method="post" id="filter_list_form">
              <div class="row">
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="from_date">
                    <label for="from_date" class="control-label">From Date</label>
                    <input type="date" name="from_date" id="from_date" value="<?= date("Y-m-01")?>" class="form-control filterInput" app-field-label="From Date" onchange="resetForm();">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="to_date">
                    <label for="to_date" class="control-label">To Date</label>
                    <input type="date" name="to_date" id="to_date" value="<?= date("Y-m-d")?>" class="form-control filterInput" app-field-label="To Date" onchange="resetForm();">
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="vendor">
                    <label for="vendor" class="control-label">Vendor</label>
                    <select name="vendor" id="vendor" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Vendor" onchange="resetForm();">
                      <option value="" selected>None selected</option>
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
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="broker">
                    <label for="broker" class="control-label">Broker</label>
                    <select name="broker" id="broker" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Broker" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($broker)) :
                        foreach ($broker as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
								<div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="status">
                    <label for="status" class="control-label">Status</label>
                    <select name="status" id="status" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Status" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
											$status = [1 => 'Pending', 2 =>'Cancel', 3 =>'Expired', 4 =>'Approved', 5 =>'Inprogress', 6 =>'Complete', 7 =>'Partially Complete'];
                      if (!empty($status)) :
                        foreach ($status as $key => $value) :
                          echo '<option value="' . $key . '" '.($key == 1 ? 'selected' : '').'>' . $value . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-9 mbot5" style="padding-top: 20px;">
                  <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
                  <?php
                  if (has_permission_new('PurchaseInvoiceList', '', 'export')) {
                    echo '<button type="button" class="btn btn-info exportBtn" onclick="exportTableToExcel()" style="display: none;"><i class="fa fa-file-excel"></i> Excel</button> ';
                  }
                  if (has_permission_new('PurchaseInvoiceList', '', 'print')) {
                    echo '<button type="button" class="btn btn-info exportBtn" onclick="printPage();" style="display: none;"><i class="fa fa-print"></i> Print</button>';
                  }
                  ?>
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
                        <th class="sortable">Sr. No.</th>
                        <th class="sortable">Invoice No</th>
                        <th class="sortable">Invoice Date</th>
                        <th class="sortable">Vendor Invoice No</th>
                        <th class="sortable">Vendor Invoice Date</th>
                        <th class="sortable">Vendor Name</th>
                        <th class="sortable">Broker Name</th>
                        <th class="sortable">Weight</th>
                        <th class="sortable">Taxable Amount</th>
                        <th class="sortable">CGST Amount</th>
                        <th class="sortable">SGST Amount</th>
                        <th class="sortable">IGST Amount</th>
                        <th class="sortable">Invoice Amount</th>
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
    $('#item_list_form').submit();
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
        url: "<?= admin_url('ItemMaster/ItemListFilter') ?>", // referance controller function
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
                  '<tr><td colspan="12" class="text-center">No Data Found</td></tr>'
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
        <td class="text-center">${row.ItemID}</td>
        <td>${row.ItemName}</td>
        <td>${row.ItemTypeName}</td>
        <td>${row.main_group_name}</td>
        <td>${row.sub_group1_name}</td>
        <td>${row.sub_group2_name}</td>
        <td>${row.CategoryName}</td>
        <td class="text-center">${row.hsn_code}</td>
        <td class="text-center">${parseFloat(row.taxrate)}</td>
        <td class="text-center">${parseFloat(row.taxrate)}</td>
        <td class="text-center">${parseFloat(row.taxrate)}</td>
        <td class="text-center">${parseFloat(row.taxrate)}</td>
        <td class="text-center">${parseFloat(row.taxrate)}</td>
        <td class="text-center">${parseFloat(row.taxrate)}</td>
      </tr>`;
    });
    $('#table-list tbody').append(html);
  }

  function updateProgress(loaded, total){
    let percent = Math.floor((loaded / total) * 100);
    $('#fetchProgress').css('width', percent + '%')
  }

  function exportTableToExcel() {

    let formData = new FormData(document.getElementById('item_list_form'));
    formData.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );

    $('.exportBtn').prop('disabled', true);

    $.ajax({
      url: "<?= admin_url('ItemMaster/ItemListExportExcel') ?>", // referance controller function
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