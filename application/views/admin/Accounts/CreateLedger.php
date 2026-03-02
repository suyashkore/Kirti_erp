<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
								<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
								<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
								<li class="breadcrumb-item active" aria-current="page"><b>Ledger</b></li>
							</ol>
						</nav>
						<hr class="hr_style">
						<form action="" method="post" id="account_ledger_form">
							<div class="row">
								<input type="hidden" name="form_mode" id="form_mode" value="add">
								<div class="col-md-4 mbot5">
									<div class="form-group">
										<label for="account_group2" class="control-label"><small class="req text-danger">* </small> Account Category</label>
										<select id="account_group2" required name="account_group2" class="form-control selectpicker" data-live-search="true" onchange="getNextLedgerCode(this.value)">
											<option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($accountSubGroup2)) :
                        foreach ($accountSubGroup2 as $value) :
                          echo '<option value="' . $value['SubActGroupID'] . '">' . $value['SubActGroupName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
										</select>
									</div>
								</div>
								<div class="col-md-4 mbot5">
									<div class="form-group">
										<label for="account_code" class="control-label"><small class="req text-danger">* </small> Account Code</label>
										<input type="text" id="account_code" name="account_code" class="form-control" required readonly>
									</div>
								</div>
								<div class="col-md-4 mbot5">
									<div class="form-group">
										<label for="account_name" class="control-label"><small class="req text-danger">* </small> Account Name</label>
										<input type="text" id="account_name" name="account_name" class="form-control" required>
									</div>
								</div>
								<div class="col-md-4 mbot5">
									<div class="form-group">
										<label for="opening_balance" class="control-label">Opening Balance</label>
										<input type="tel" id="opening_balance" name="opening_balance" class="form-control">
									</div>
								</div>
                <div class="col-md-4 mbot5">
                  <div class="form-group">
                    <label for="gst" class="control-label">GST</label>
                    <select name="gst" id="gst" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($gst)) :
                        foreach ($gst as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['taxrate'] . '%</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4 mbot5">
                  <div class="form-group">
                    <label for="hsn" class="control-label">HSN</label>
                    <select name="hsn" id="hsn" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
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
								<div class="col-md-12">
									<hr>
								</div>
                <div class="col-md-4 mbot5">
                  <div class="form-group">
                    <label for="is_bank" class="control-label">Is Bank Details</label>
                    <select name="is_bank" id="is_bank" class="form-control selectpicker" onchange="(this.value == 'Y') ? $('.bankDetails').show() : $('.bankDetails').hide();">
                      <option value="Y">Yes</option>
                      <option value="N" selected>No</option>
                    </select>
                  </div>
                </div>
								<div class="col-md-4 mbot5 bankDetails">
									<div class="form-group">
										<label for="ifsc_code" class="control-label">IFSC Code <img src="<?= base_url('assets/plugins/lightbox/images/loading.gif');?>" alt="Loader" style="width: 10px; display: none;" id="ifsc_code_loader"></label>
										<input type="text" id="ifsc_code" name="ifsc_code" class="form-control" maxlength="11" minlength="11" onchange="validateIFSC(this.value)" style="text-transform: uppercase;">
									</div>
								</div>
								<div class="col-md-4 mbot5 bankDetails">
									<div class="form-group">
										<label for="bank_name" class="control-label">Bank Name</label>
										<input type="text" id="bank_name" name="bank_name" class="form-control" readonly>
									</div>
								</div>
								<div class="col-md-4 mbot5 bankDetails">
									<div class="form-group">
										<label for="branch_name" class="control-label">Branch Name</label>
										<input type="text" id="branch_name" name="branch_name" class="form-control" readonly>
									</div>
								</div>
								<div class="col-md-4 mbot5 bankDetails">
									<div class="form-group">
										<label for="bank_address" class="control-label">Bank Address</label>
										<input type="text" id="bank_address" name="bank_address" class="form-control" readonly>
									</div>
								</div>
								<div class="col-md-4 mbot5 bankDetails">
									<div class="form-group">
										<label for="account_number" class="control-label">Account Number <img src="<?= base_url('assets/plugins/lightbox/images/loading.gif');?>" alt="Loader" style="width: 10px; display: none;" id="account_number_loader"></label>
										<input type="tel" id="account_number" name="account_number" class="form-control" onchange="validateAccountNumber(this.value)">
									</div>
								</div>
								<div class="col-md-4 mbot5 bankDetails">
									<div class="form-group">
										<label for="account_holder_name" class="control-label">Account Holder Name</label>
										<input type="text" id="account_holder_name" name="account_holder_name" class="form-control" readonly>
									</div>
								</div>
                <div class="col-md-4 mbot5">
                  <div class="form-group">
                    <label for="is_active" class="control-label">Is Active</label>
                    <select name="is_active" id="is_active" class="form-control selectpicker" onchange="(this.value == 'N') ? $('.isActive').show() : $('.isActive').hide();">
                      <option value="Y" selected>Yes</option>
                      <option value="N">No</option>
                    </select>
                  </div>
                </div>
								<div class="col-md-4 mbot5 isActive">
									<div class="form-group">
										<label for="blocked_reason" class="control-label">Blocked Reason</label>
                    <textarea id="blocked_reason" name="blocked_reason" class="form-control"></textarea>
									</div>
								</div>
								<!-- <div class="clearfix"></div> -->
							</div>
							
							<div class="row mtop7"> 
								<div class="col-md-12">
									<button type="submit" class="btn btn-success saveBtn <?= (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
									<button type="submit" class="btn btn-success updateBtn <?= (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
									<button type="button" class="btn btn-danger" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
                  <button type="button" class="btn btn-info" onclick="$('#ListModal').modal('show');"><i class="fa fa-list"></i> Show List</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="width: 80vw !important;">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Account Ledger List</h4>
      </div>
      <div class="modal-body" style="padding:0px 5px !important">
        
        <div class="table-ListModal tableFixHead2" style="overflow-x: auto;">
          <table class="tree table table-striped table-bordered table-ListModal tableFixHead2" id="table_ListModal" width="100%">
            <thead>
              <tr>
                <th class="sortablePop">Account Code</th>
                <th class="sortablePop">Account Name</th>
                <th class="sortablePop">Main Group</th>
                <th class="sortablePop">Sub Group 1</th>
                <th class="sortablePop">Sub Group 2</th>
                <th class="sortablePop">IsActive</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($all_ledger as $key => $value) {
              ?>
              <tr class="get_LedgerDetails" data-id="<?= $value["AccountID"]; ?>" onclick="getLedgerDetails('<?= $value['AccountID']; ?>')">
                <td><?= $value["AccountID"];?></td>
                <td><?= $value["company"];?></td>
                <td><?= $value["ActMainGroupName"];?></td>
                <td><?= $value["ActSubGroup1Name"];?></td>
                <td><?= $value["ActSubGroup2Name"];?></td>
                <td><?= ($value["IsActive"] == 'Y') ? 'Yes' : 'No';?></td>
              </tr>
              <?php }
              ?>
            </tbody>
          </table>   
        </div>
      </div>
      <div class="modal-footer" style="padding:0px;">
        <input type="text" id="myInput1"  name='myInput1' onkeyup="myFunction2()" placeholder="Search for names.."  style="float: left;width: 100%;">
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<script>
	$(document).on('input', 'input[type="tel"]', function () {
		this.value = this.value.replace(/[^0-9]/g, '');
	});
	
	function ResetForm(){
		$('.saveBtn').show();
		$('.updateBtn').hide();
		$('#account_ledger_form')[0].reset();
    $('.bankDetails, .isActive').hide();
		$('.selectpicker').selectpicker('refresh');
		$('#form_mode').val('add');
	}
</script>
<script type="text/javascript" language="javascript">
	function validateIFSC(ifsc){
		var regex = /^[A-Za-z]{4}[0-9]{7}$/;
		if(!regex.test(ifsc)){
			$('#ifsc_code').focus();
			alert_float('warning', 'Please enter valid IFSC Code');
			return false;
		}
		$.ajax({
			url: '<?= admin_url(); ?>Accounts/fetchBankDetailsFromIFSC',
			type: 'POST',
			dataType: 'json',
			data: {
				ifsc_code: ifsc,
				'<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
			},
      beforeSend: function(){
        $('#ifsc_code_loader').show();
      },
      complete: function(){
        $('#ifsc_code_loader').hide();
      },
			success: function(response){
				if(response.success){
					$('#bank_name').val(response.data.BANK);
					$('#branch_name').val(response.data.BRANCH);
          $('#bank_address').val(response.data.ADDRESS);
				}else{
          alert_float('warning', response.message);
        }
			}
		});
	}

  function validateAccountNumber(accountNumber){
    let ifsc = $('#ifsc_code').val();
    if(accountNumber.length < 10){
      $('#account_number').focus();
      alert_float('warning', 'Please enter valid Account Number');
      return false;
    }
    
    if(ifsc !== '' && ifsc !== null){
      $.ajax({
        url: '<?= admin_url(); ?>Accounts/verifyBankAccount',
        type: 'POST',
        dataType: 'json',
        data: {
          bank_ac_no: accountNumber,
          ifsc_code: ifsc,
          '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
        },
        beforeSend: function(){
          $('#account_number_loader').show();
        },
        complete: function(){
          $('#account_number_loader').hide();
        },
        success: function(response){
          if(response.success == true){
            $('#account_holder_name').val(response.data.full_name);
          }else{
            alert_float('warning', response.message);
          }
        }
      });
    }
  }

  function getNextLedgerCode(accountSubGroupId2){
    let add_mode = $('#form_mode').val();
    if(add_mode == 'edit'){
      return false;
    }
    $.ajax({
      url: '<?= admin_url(); ?>Accounts/NextLedgerCode',
      type: 'POST',
      dataType: 'json',
      data: {
        accountSubGroupId2: accountSubGroupId2,
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      success: function(response){
        if(response.success){
          $('#account_code').val(response.data);
        }
      }
    });
  }

	function validate_fields(fields){ 
		let data = {};
		for(let i = 0; i < fields.length; i++){
			let value = $('#' + fields[i]).val();

			if(value === '' || value === null){
				let label = fields[i].replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
				$('#'+fields[i]).focus();
				alert_float('warning', 'Please enter ' + label);
				return false;
			} else {
				data[fields[i]] = value.trim();
			}
		}
		return data;
	}

	$('#account_ledger_form').submit(function(e){
		e.preventDefault();
		var form_data = new FormData(this);
		form_data.append(
			'<?= $this->security->get_csrf_token_name(); ?>',
			$('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
		);

    let fields = ['account_group2', 'account_code', 'account_name'];
    let data = validate_fields(fields);
    if(data === false){
      return false;
    }

		$.ajax({
			url:"<?= admin_url(); ?>Accounts/SaveLedger",
			method:"POST",
			dataType:"JSON",
			data:form_data,
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function () {
				$('.saveBtn, .updateBtn').attr('disabled', true);
			},
			complete: function () {
				$('.saveBtn, .updateBtn').attr('disabled', false);
			},
			success: function(response){
				if(response.success == true){
					alert_float('success', response.message);
          let html = `<tr class="get_LedgerDetails" data-id="${response.data.AccountID}" onclick="getLedgerDetails('${response.data.AccountID}')">
            <td>${response.data.AccountID}</td>
            <td>${response.data.company}</td>
            <td>${response.data.ActMainGroupName}</td>
            <td>${response.data.ActSubGroup1Name}</td>
            <td>${response.data.ActSubGroup2Name}</td>
            <td>${response.data.IsActive == 'Y' ? 'Yes' : 'No'}</td>
          </tr>`;
          if($('#form_mode').val() == 'edit'){
            $('.get_LedgerDetails[data-id="'+response.data.AccountID+'"]').replaceWith(html);
          }else{
            $('#table_ListModal tbody').prepend(html);
          }
					ResetForm();
				}else{
					alert_float('warning', response.message);
				}
			}
		});
	});
	
  function getLedgerDetails(ledgerId){
    $.ajax({
      url: '<?= admin_url(); ?>Accounts/GetLedgerDetails',
      type: 'POST',
      dataType: 'json',
      data: {
        ledgerId: ledgerId,
        '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      },
      success: function(response){
        if(response.success){
          $('#account_group2').val(response.data.ActSubGroupID2);
          $('#account_code').val(response.data.AccountID);
          $('#account_name').val(response.data.company);
          if(response.data.IFSC == null){
            $('.bankDetails').hide();
            $('#is_bank').val('N');
          }else{
            $('.bankDetails').show();
            $('#is_bank').val('Y');
          }
          $('#ifsc_code').val(response.data.IFSC);
          $('#bank_name').val(response.data.BankName);
          $('#branch_name').val(response.data.BranchName);
          $('#bank_address').val(response.data.BankAddress);
          $('#account_number').val(response.data.AccountNo);
          $('#account_holder_name').val(response.data.HolderName);
          $('#is_active').val(response.data.IsActive);
          if(response.data.IsActive == 'N'){
            $('.isActive').show();
          }else{
            $('.isActive').hide();
          }
          $('#blocked_reason').val(response.data.DeActiveReason);
          $('#ListModal').modal('hide');
          $('.selectpicker').selectpicker('refresh');
          $('.saveBtn').hide();
          $('.updateBtn').show();
          $('#form_mode').val('edit');
        }
      }
    });
  }
</script>

<script>
  function myFunction2() {
    var input = document.getElementById("myInput1");
    var filter = input.value.toUpperCase();
    var table = document.getElementById("table_ListModal");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
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
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_ListModal tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop").removeClass("asc desc");
		$(".sortablePop span").remove();
		
		// Add sort classes and arrows
		$(this).addClass(ascending ? "asc" : "desc");
		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
		
		rows.sort(function (a, b) {
			var valA = $(a).find("td").eq(index).text().trim();
			var valB = $(b).find("td").eq(index).text().trim();
			
			if ($.isNumeric(valA) && $.isNumeric(valB)) {
				return ascending ? valA - valB : valB - valA;
				} else {
				return ascending
                ? valA.localeCompare(valB)
                : valB.localeCompare(valA);
			}
		});
		table.append(rows);
	});
</script>
<style>
  #table_ListModal tbody tr { cursor: pointer; }
	table { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important; font-size:11px; line-height:1.42857143!important; vertical-align: middle !important;}
	th { background: #50607b; color: #fff !important; }
  .bankDetails, .isActive { display: none; }
  .limit-words { max-width: 22ch; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>