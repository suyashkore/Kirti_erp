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
                <li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li><li class="breadcrumb-item active" aria-current="page"><b>Item List</b></li>
							</ol>
						</nav>
            <hr class="hr_style">
            <form action="" method="post" id="item_list_form">
              <div class="row">
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_type">
                    <label for="item_type" class="control-label">Item Type</label>
                    <select name="item_type" id="item_type" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Type" onchange="resetForm(); getCustomDropdownList('item_type', this.value, 'item_category'); getCustomDropdownList('item_type', this.value, 'item_main_group');">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($types)) : 
                        $i = 1;
                        foreach ($types as $value) :
                          if($i == 1){
                            echo '<option value="' . $value['id'] . '" selected>' . $value['ItemTypeName'] . '</option>';
                            $i++;
                          }else{
                            echo '<option value="' . $value['id'] . '">' . $value['ItemTypeName'] . '</option>';
                          }
                          endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="main_item_group">
                    <label for="main_item_group" class="control-label">Item Main Group</label>
                    <select name="item_main_group" id="item_main_group" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Main Group" onchange="resetForm(); getCustomDropdownList('item_main_group', this.value, 'item_sub_group1')">
                        <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_sub_group1">
                    <label for="item_sub_group1" class="control-label">Item Sub Group 1</label>
                    <select name="item_sub_group1" id="item_sub_group1" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Sub Group 1" onchange="resetForm(); getCustomDropdownList('item_sub_group1', this.value, 'item_sub_group2')">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_sub_group2">
                    <label for="item_sub_group2" class="control-label">Item Sub Group 2</label>
                    <select name="item_sub_group2" id="item_sub_group2" app-field-label="Sub Group 2" class="form-control selectpicker filterInput" data-live-search="true" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_division">
                    <label for="item_division" class="control-label">Item Division</label>
                    <select name="item_division" id="item_division" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Division" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($division)) :
                        foreach ($division as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_category">
                    <label for="item_category" class="control-label">Item Category</label>
                    <select name="item_category" id="item_category" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="Category" onchange="resetForm(); getNextItemCode(this.value);">
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="hsn">
                    <label for="hsn" class="control-label">HSN</label>
                    <select name="hsn" id="hsn" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="HSN" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($hsn)) :
                        foreach ($hsn as $value) :
                          echo '<option value="' . $value['name'] . '">' . $value['name'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="gst">
                    <label for="gst" class="control-label">GST</label>
                    <select name="gst" id="gst" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="GST" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($gst)) :
                        foreach ($gst as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['name'] . '%</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 col-sm-3 mbot5">
                  <div class="form-group" app-field-wrapper="unit">
                    <label for="unit" class="control-label">UOM</label>
                    <select name="unit" id="unit" class="form-control selectpicker filterInput" data-live-search="true" app-field-label="UOM" onchange="resetForm();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($unit)) :
                        foreach ($unit as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-sm-6 mbot5" style="padding-top: 20px;">
                  <button type="submit" class="btn btn-success" id="searchBtn"><i class="fa fa-list"></i> Show</button>
                  <button type="button" class="btn btn-info exportBtn" onclick="exportTableToExcel()" style="display: none;"><i class="fa fa-file-excel"></i> Excel</button>
                  <button type="button" class="btn btn-info exportBtn" onclick="printPage();" style="display: none;"><i class="fa fa-print"></i> Print</button>
                </div>
              </div>
            </form>
            <div class="progress" style="margin-bottom: 5px; height: 3px;">
              <div id="fetchProgress" class="progress-bar" style="width:0%"></div>
            </div>

            <div class="row">
              <div class="col-md-9"></div>
              <div class="col-md-3">
                <input type="search" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search..." title="Type in a table">
              </div>
              <div class="col-md-12">
                <div class="table-list" id="printableArea">
                  <table class="table table-striped table-bordered table-list" id="table-list" width="100%">
                    <thead>
                      <tr class="mainHead" style="display: none;">
                        <td colspan="13">
                          <h5 style="text-align:center;">
                            <span style="font-size:15px; font-weight:700;"><?= $company_detail->company_name; ?></span><br>
                            <span style="font-size:10px; font-weight:600;"><?= $company_detail->address; ?></span>
                          </h5>
                        </td>
                      </tr>
                      <tr class="mainHead" style="display: none;">
                        <td colspan="13">
                          <span class="report_for" style="font-size:10px;"></span>
                        </td>
                      </tr>
                      <tr>
                        <th class="sortable">Item Code</th>
                        <th class="sortable">Item Name</th>
                        <th class="sortable">Type</th>
                        <th class="sortable">Main Group</th>
                        <th class="sortable">Group1</th>
                        <th class="sortable">Group2</th>
                        <th class="sortable">Category</th>
                        <th class="sortable">Division</th>
                        <th class="sortable">HSN</th>
                        <th class="sortable">GST</th>
                        <th class="sortable">UOM</th>
                        <th class="sortable">Packing Wt</th>
                        <th class="sortable">IsActive</th>
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
    $('#item_type').change();
    $('#item_list_form').submit();
  })

  function resetForm(){
    $('.exportBtn').hide();
    $('#table-list tbody').html('');
    
    let filterHtml = '';
    $('.filterInput').each(function () {
      let label = $(this).attr('app-field-label') || '';
      let value = $(this).val();

      if (value) {
        value = $(this).find('option:selected').text();
        filterHtml += `<b>${label} : </b> ${value}, `;
      }
    });

    filterHtml = filterHtml.replace(/, $/, '');
    $('.report_for').html(filterHtml);
  }

  function getCustomDropdownList(parent_id, parent_value, child_id, selected_value = null, callback = null){
    $.ajax({
      url:"<?= admin_url(); ?>ItemMaster/GetCustomDropdownList",
      type: 'POST',
      dataType: 'json',
      data: {
        parent_id: parent_id,
        parent_value: parent_value,
        child_id: child_id
      },
      success: function(response){
        if(response.success == true){
          let html = `<option value="" selected>None selected</option>`;

          for(var i = 0; i < response.data.length; i++){
            html += `<option value="${response.data[i].id}">${response.data[i].name}</option>`;
          }

          $('#'+child_id).html(html);
          if(selected_value){
            $('#'+child_id).val(selected_value);
          }
          $('.selectpicker').selectpicker('refresh');
          if(callback){
            callback();
          }
        }
      }
    });
  }

  $('#item_list_form').submit(function(e){
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
        url: "<?= admin_url('ItemMaster/ItemListFilter') ?>",
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
        <td>${row.division_name}</td>
        <td class="text-center">${row.hsn_code}</td>
        <td class="text-center">${parseFloat(row.taxrate)}%</td>
        <td class="text-center">${row.ShortCode}</td>
        <td class="text-center">${row.PackingWeight} ${row.UnitWeightIn}</td>
        <td class="text-center">${row.IsActive == 'Y' ? 'Yes' : 'No'}</td>
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
      url: "<?= admin_url('ItemMaster/ItemListExportExcel') ?>",
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