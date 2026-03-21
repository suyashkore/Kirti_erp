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

  .total-label-row {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .total-label-row .total-display {
    flex: 1;
    padding: 0;
    text-align: right;
    font-weight: 600;
  }

  .modal-header {
    padding: 0;
    border-bottom: none;
  }

  .custom-header .header-top {
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .custom-header .modal-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
  }

  .close-btn {
    background: transparent;
    border: none;
    font-size: 24px;
    cursor: pointer;
  }

  .header-filters {
    padding: 10px;
    display: flex;
    align-items: flex-end;
    gap: 25px;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
  }

  .filter-group label {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
  }

  .filter-group .form-control {
    height: 36px;
    min-width: 200px;
  }

  .search-btn {
    background: #1fa0d8;
    color: #fff;
    border: none;
    padding: 8px 20px;
    font-weight: 600;
    border-radius: 4px;
  }

  .search-btn:hover {
    background: #168ac0;
  }

  #conveyor_table th:nth-child(4),
  #conveyor_table td:nth-child(4) {
    width: 0%;
    max-width: 0%;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Conveyor</b></li>
              </ol>
            </nav>
            <hr class="hr_style">
            <br>
            <form action="" method="post" id="main_save_form">
              <input type="hidden" name="form_mode" id="form_mode" value="add">
              <div class="row">
                <div class="col-md-6 mbot5">
                  <div class="form-group" app-field-wrapper="PlantLocation">
                    <label for="PlantLocation" class="control-label"><small class="req text-danger">* </small> Plant Location</label>
                    <select name="PlantLocation" id="PlantLocation" class="form-control selectpicker" data-live-search="true" app-field-label="Plant Location" required onchange="getGodownDropdown();">
                      <option value="" selected>None selected</option>
                      <?php
                      if (!empty($plantlocation)) :
                        foreach ($plantlocation as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['LocationName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 mbot5">
                  <div class="form-group" app-field-wrapper="Godown">
                    <label for="Godown" class="control-label"><small class="req text-danger">* </small> Godown / Warehouse Name</label>
                    <select name="Godown" id="Godown" class="form-control selectpicker" data-live-search="true" app-field-label="Godown Name" onchange="getConveyorDetails(this.value)" required>
                      <option value="" selected>None selected</option>
                    </select>
                  </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 mbot5">
                  <h4 class="bold">Conveyors:</h4>
                  <hr class="hr_style">
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 mbot5">
                  <input type="hidden" id="row_id" value="0">
                  <table width="100%" class="table" id="conveyor_table">
                    <thead>
                      <tr style="text-align: center;">
                        <th>Conveyor Name</th>
                        <th>Conveyor ID</th>
                        <th>Is Active</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td><input type="text" id="conveyor_name" class="form-control fixed_row"></td>
                        <td><input type="text" id="conveyor_id" class="form-control fixed_row"></td>
                        <td><select id="IsActive" class="form-control fixed_row dynamic_row dynamic_item selectpicker" app-field-label="Is Active">
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                          </select></td>
                        <td>
                          <button type="button" class="btn btn-success" onclick="addRow();" tabindex="4"><i class="fa fa-plus"></i></button>
                        </td>
                      </tr>
                    </thead>
                    <tbody id="conveyor_body">

                    </tbody>
                  </table>
                </div>

                <div class="col-md-12" style="text-align: right;">
                  <button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('Conveyor', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                  <button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('Conveyor', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
                  <button type="button" class="btn btn-warning" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
  function ResetForm() {
    $('#main_save_form')[0].reset();
    $('#form_mode').val('add');
    $('.updateBtn').hide();
    $('.saveBtn').show(); // <-- Always Save on reset
    $('.selectpicker').selectpicker('refresh');
    $('#conveyor_body').html('');
    $('#row_id').val(0);
  }

  // Get Godown Dropdown On Plant Location
  function getGodownDropdown(callback = null) {
    var PlantLocation = $('#PlantLocation').val();

    // ===== CLEAR TABLE & RESET FORM STATE ON PLANT CHANGE =====
    $('#conveyor_body').html('');
    $('#row_id').val(0);
    $('#form_mode').val('add');
    $('.updateBtn').hide();
    $('.saveBtn').show();

    if (!PlantLocation) {
      $('#Godown').html('<option value="" selected disabled>None selected</option>');
      $('.selectpicker').selectpicker('refresh');
      return;
    }
    $.ajax({
      url: '<?= admin_url('Conveyor/getGodownDropdown'); ?>',
      type: 'POST',
      data: {
        PlantLocation: PlantLocation
      },
      dataType: 'json',
      success: function(response) {
        if (response.success == true) {
          var html = '<option value="" selected disabled>None selected</option>';
          $.each(response.Godown, function(index, loc) {
            if (loc.id == null || loc.id == '') return;
            html += '<option value="' + loc.id + '">' + loc.GodownName + '</option>';
          });
          $('#Godown').html(html);
          $('.selectpicker').selectpicker('refresh');
          if (callback) callback();
        }
      }
    });
  }


  $(document).on('input', 'input[type="tel"]', function() {
    this.value = this.value
      .replace(/[^0-9.]/g, '')
      .replace(/(\..*?)\..*/g, '$1');
  });

  function validate_fields(fields) {
    let data = {};
    for (let i = 0; i < fields.length; i++) {
      let value = $('#' + fields[i]).val();

      if (value === '' || value === null) {
        let label = fields[i].replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        alert_float('warning', 'Please enter ' + label);
        $('#' + fields[i]).focus();
        return false;
      } else {
        data[fields[i]] = value.trim();
      }
    }
    return data;
  }

  function addRow(row = null) {
    $('#conveyor_name').focus();
    var row_id = $('#row_id').val();
    var next_id = parseInt(row_id) + 1;
    if (row == null) {
      let fields = ['conveyor_name', 'conveyor_id'];
      let data = validate_fields(fields);
      if (data === false) {
        return false;
      }

      var row_btn = `<button type="button" class="btn btn-danger" onclick="$(this).closest('tr').remove();" style="float:right; padding: 2px; width: 30px;"><i class="fa fa-xmark"></i></button>`;
    } else {
      var row_btn = '';
    }
    let IsActive_option = '<option value="Y">Yes</option><option value="N">No</option>';
    $('#conveyor_body').append(`
      <tr>
        <td style="width: 250px;">
          <input type="hidden" name="update_id[]" id="update_id${next_id}">
          <input type="text" name="conveyor_name[]" id="conveyor_name${next_id}" class="form-control dynamic_row${next_id}">
        </td>
        <td><input type="text" name="conveyor_id[]" id="conveyor_id${next_id}" class="form-control dynamic_row${next_id}"></td>
        <td> <select id="IsActive${next_id}" name="IsActive[]" class="form-control dynamic_row${next_id} dynamic_item selectpicker" app-field-label="Item Name">${IsActive_option}</select></td>
        <td></td>
      </tr>
    `);
    if (row == null) {
      $('#conveyor_name' + next_id).val($('#conveyor_name').val());
      $('#conveyor_id' + next_id).val($('#conveyor_id').val());
      $('#IsActive' + next_id).val($('#IsActive').val());
      $('.selectpicker').selectpicker('refresh');
      $('.fixed_row').val('');

      $('#IsActive').val('Y').selectpicker('refresh');
    }
    $('#row_id').val(next_id);
  }

  function get_required_fields(form_id) {
    let fields = [];
    $('#' + form_id + ' [required]').each(function() {
      fields.push($(this).attr('id'));
    });
    return fields;
  }

  // Get Conveyor Details
  function getConveyorDetails(Godown) {
    // ===== CLEAR TABLE FIRST ON EVERY GODOWN CHANGE =====
    $('#conveyor_body').html('');
    $('#row_id').val(0);
    $('#form_mode').val('add');
    $('.updateBtn').hide();
    $('.saveBtn').show();

    if (!Godown) return; // <-- exit if none selected

    var PlantLocation = $('#PlantLocation').val();
    $.ajax({
      url: "<?php echo admin_url(); ?>Conveyor/getConveyorDetails",
      dataType: "JSON",
      method: "POST",
      data: {
        Godown: Godown,
        PlantLocation: PlantLocation
      },
      success: function(res) {
        if (res.success == true) {
          let data = res.data;
          if (data.length > 0) {
            $.each(data, function(i, row) {
              addRow(2);
              var idx = parseInt($('#row_id').val());
              $('#update_id' + idx).val(row.id);
              $('#conveyor_name' + idx).val(row.ConveyorName);
              $('#conveyor_id' + idx).val(row.ShortCode);
              $('#IsActive' + idx).val(row.IsActive);
              $('.selectpicker').selectpicker('refresh');
            });
            $('#form_mode').val('edit');
            $('.saveBtn').hide();
            $('.updateBtn').show();
          } else {
            $('#form_mode').val('add');
            $('.updateBtn').hide();
            $('.saveBtn').show();
          }
        }
      }
    });
  }

  $('#conveyor_id').blur(function() {
    ConveyorID = $(this).val();
    if (ConveyorID == '') {} else {
      $.ajax({
        url: "<?php echo admin_url(); ?>Conveyor/CheckConveyorIDExit",
        dataType: "JSON",
        method: "POST",
        data: {
          ConveyorID: ConveyorID
        },
        beforeSend: function() {
          $('.searchh2').css('display', 'block');
          $('.searchh2').css('color', 'blue');
        },
        complete: function() {
          $('.searchh2').css('display', 'none');
        },
        success: function(data) {
          if (data) {
            alert_float('warning', "Conveyor ID Already Exist.");
            $('#conveyor_id').val("").focus();
          }
        }
      });
    }
  });

  $('#main_save_form').on('submit', function(e) {
    e.preventDefault();

    let form_mode = $('#form_mode').val();
    let required_fields = get_required_fields('main_save_form');
    let validated = validate_fields(required_fields);
    if (validated === false) return;

    var form_data = new FormData(this);
    form_data.append(
      '<?= $this->security->get_csrf_token_name(); ?>',
      $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
    );


    $.ajax({
      url: "<?= admin_url(); ?>Conveyor/SaveConveyor",
      method: "POST",
      dataType: "JSON",
      data: form_data,
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function() {
        $('button[type=submit]').attr('disabled', true);
      },
      complete: function() {
        $('button[type=submit]').attr('disabled', false);
      },
      success: function(response) {
        if (response.success == true) {
          alert_float('success', response.message);
          ResetForm();
        } else {
          alert_float('warning', response.message);
        }
      }
    });
  });
</script>