<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" style="min-height:1px">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <style>
                        @media (max-width: 767px) {
                            .mobile-menu-btn {
                                display: block !important;
                                margin-bottom: 10px;
                                width: 100%;
                                text-align: left;
                            }

                            .custombreadcrumb {
                                display: none !important;
                            }

                            .custombreadcrumb.open {
                                display: block !important;
                            }

                            .custombreadcrumb li {
                                display: block;
                                padding: 8px 10px;
                                border-bottom: 1px solid #eee;
                            }

                            .custombreadcrumb li a {
                                display: block;
                            }

                            .custombreadcrumb li+li:before {
                                content: none;
                            }
                        }

                        .mobile-menu-btn {
                            display: none;
                        }

                        .menu-item-master ul.nav-second-level {
                            max-height: 220px;
                            overflow-y: auto;
                        }

                        /* Form design improvements */
                        .company-form .form-group {
                            margin-bottom: 12px;
                        }

                        .company-form h4 {
                            margin-top: 6px;
                            margin-bottom: 8px;
                            font-size: 16px;
                        }

                        .company-form .p_style {
                            font-weight: 600;
                        }

                        .action-buttons {
                            display: flex;
                            gap: 8px;
                            justify-content: flex-end;
                        }

                        .action-buttons .btn {
                            min-width: 120px;
                        }

                        @media (max-width: 767px) {
                            .action-buttons {
                                flex-direction: column-reverse;
                                align-items: stretch;
                            }

                            .action-buttons .btn {
                                width: 100%;
                            }
                        }

                       /* Location table wrapper - vertical scroll साठी */
                        .table-responsive {
                            overflow-x: auto;
                            overflow-y: auto;
                            max-height: 350px; 
                        }

                        #locationTable thead tr th {
                            position: sticky;
                            top: 0;
                            z-index: 2;
                            background: #50607b;
                        }

                        #locationTable {
                            table-layout: fixed;
                            width: 100%;
                            min-width: 1100px; 
                        }

                        #locationTable th,
                        #locationTable td {
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            padding: 4px 5px !important;
                        }

                        #locationTable th:nth-child(1),
                        #locationTable td:nth-child(1) { width: 120px; min-width: 120px; } /* State */

                        #locationTable th:nth-child(2),
                        #locationTable td:nth-child(2) { width: 120px; min-width: 120px; } /* City */

                        #locationTable th:nth-child(3),
                        #locationTable td:nth-child(3) { width: 110px; min-width: 110px; } /* Location */

                        #locationTable th:nth-child(4),
                        #locationTable td:nth-child(4) { width: 150px; min-width: 150px; } /* Address */

                        #locationTable th:nth-child(5),
                        #locationTable td:nth-child(5) { width: 80px;  min-width: 80px;  } /* Pincode */

                        #locationTable th:nth-child(6),
                        #locationTable td:nth-child(6) { width: 110px; min-width: 110px; } /* Mobile */

                        #locationTable th:nth-child(7),
                        #locationTable td:nth-child(7) { width: 120px; min-width: 120px; } /* FSSAI No */

                        #locationTable th:nth-child(8),
                        #locationTable td:nth-child(8) { width: 130px; min-width: 130px; } /* FSSAI Expiry */

                        #locationTable th:nth-child(9),
                        #locationTable td:nth-child(9) { width: 90px;  min-width: 90px;  } /* Status */

                        #locationTable th:nth-child(10),
                        #locationTable td:nth-child(10){ width: 50px;  min-width: 50px;  } /* Action */

                        @media (max-width: 991px) {
                            #locationTable {
                                table-layout: fixed; 
                            }
                        }
                        </style>

                        <button class="btn btn-default mobile-menu-btn"
                            onclick="$('.custombreadcrumb').toggleClass('open')">
                            <i class="fa fa-bars"></i> Menu
                        </button>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb"
                                style="background-color:#fff !important; margin-Bottom:0px !important; display: flex; flex-wrap: wrap;">
                                <li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i
                                                class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Company Master</b></li>
                            </ol>
                        </nav>

                        <hr class="hr_style">

                        <form id="companyForm" method="post"
                            action="<?php echo admin_url('company_master/company' . (isset($company) && $company ? '/' . $company->id : '')); ?>">
                            <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                            <input type="hidden" name="id" id="company_id_input"
                                value="<?php echo isset($company) && $company ? $company->id : ''; ?>">
                            <input type="hidden" name="UserID" id="created_by"
                                value="<?php echo $this->session->userdata('staff_user_id'); ?>">

                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold p_style">Company Information</h4>
                                    <hr class="hr_style">
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="company_id">
                                        <label for="comp_short" class="control-label">Short Code</label>
                                        <input type="text" id="comp_short" name="comp_short" placeholder="Enter the short code and press Tab."
                                            value="<?php echo isset($company) && $company ? htmlspecialchars($company->comp_short) : ''; ?>"
                                            class="form-control" autocomplete="off">
                                        <!-- <small style="display:block;color:#666;margin-top:4px;">Enter the short code and -->
                                            <!-- press Enter/Tab. -->
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="company_name">
                                        <small class="req text-danger">* </small>
                                        <label for="company_name" class="control-label">Company Name</label>
                                        <input type="text" id="company_name" name="company_name" class="form-control"
                                            value="<?php echo isset($company) && $company ? htmlspecialchars($company->company_name) : ''; ?>"
                                            required placeholder="Enter Company Name">
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="state">
                                        <small class="req text-danger">* </small>
                                        <label for="state" class="form-label">State</label>
                                        <select name="state" id="state" class="selectpicker form-control"
                                            data-width="100%" data-none-selected-text="None selected"
                                            data-live-search="true">
                                            <option value="">None selected</option>
                                            <?php foreach ($state as $key => $value) { ?>
                                            <option value="<?php echo $value['short_name'];?>"
                                                <?php echo (isset($company) && $company && $company->state == $value['short_name']) ? 'selected' : ''; ?>>
                                                <?php echo $value['state_name'];?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="city">
                                        <small class="req text-danger">* </small>
                                        <label for="city" class="form-label">City</label>
                                        <select class="form-control city selectpicker" data-width="100%"
                                            data-none-selected-text="None selected" name="city" id="city"
                                            data-live-search="true">
                                            <option value="">None selected</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="address">
                                        <small class="req text-danger">* </small>
                                        <label for="address" class="control-label">Address</label>
                                        <textarea id="address" name="address" class="form-control" rows="1" required
                                            placeholder="Enter Address"><?php echo isset($company) && $company ? htmlspecialchars($company->address) : ''; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="pincode">
                                        <small class="req text-danger">* </small>
                                        <label for="pincode" class="control-label">Pincode</label>
                                        <input type="text" name="pincode" id="pincode" class="form-control"
                                            value="<?php echo isset($company) && $company ? htmlspecialchars($company->pincode) : ''; ?>"
                                            required placeholder="Enter Pincode" pattern="^[0-9]{6}$"
                                            title="Pincode should be exactly 6 digits" maxlength="6" minlength="6"
                                            onkeypress="return isNumber(event)" inputmode="numeric">
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="gst_no">
                                        <small class="req text-danger">* </small>
                                        <label for="gst_no" class="control-label">GST No</label>
                                        <input type="text" name="gst_no" id="gst_no" class="form-control"
                                            value="<?php echo isset($company) && $company ? htmlspecialchars($company->gst) : ''; ?>"
                                            required placeholder="Enter GST Number" pattern="[0-9A-Z]{15}"
                                            title="GST should be 15 characters" maxlength="15">
                                        <span class="gst_denger" style="color:red;"></span>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="mobile">
                                        <small class="req text-danger">* </small>
                                        <label for="mobile" class="control-label">Mobile No</label>
                                        <input type="tel" name="mobile" id="mobile" class="form-control"
                                            value="<?php echo isset($company) && $company ? htmlspecialchars($company->mobile1) : ''; ?>"
                                            required placeholder="Enter Mobile Number" pattern="^[0-9]{10}$"
                                            title="Mobile should be exactly 10 digits" maxlength="10" minlength="10"
                                            onkeypress="return isNumber(event)" inputmode="numeric">
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="email">
                                        <small class="req text-danger">* </small>
                                        <label for="email" class="control-label">Email ID</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            value="<?php echo isset($company) && $company ? htmlspecialchars($company->BusinessEmail) : ''; ?>"
                                            required placeholder="Enter Email ID"
                                            pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}">
                                        <span class="email_error" style="color:red;"></span>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="form-group" app-field-wrapper="status">
                                        <small class="req text-danger">* </small>
                                        <label for="status" class="control-label">Status</label>
                                        <select name="status" id="status" class="form-control selectpicker" required>
                                            <option value="">Select Status</option>
                                            <option value="active"
                                                <?php echo (isset($company) && $company && $company->status == 'Y') ? 'selected' : (isset($company) ? '' : 'selected'); ?>>
                                                Active</option>
                                            <option value="deactive"
                                                <?php echo (isset($company) && $company && $company->status == 'n') ? 'selected' : ''; ?>>
                                                Deactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="bold p_style">Location Information</h4>
                                    <hr class="hr_style">
                                </div>

                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table width="100%" class="table" id="locationTable">
                                            <thead>
                                                <tr>
                                                    <th>State</th>
                                                    <th>City</th>
                                                    <th>Location</th>
                                                    <th>Address</th>
                                                    <th>Pincode</th>
                                                    <th>Mobile</th>
                                                    <th>FSSAI No</th>
                                                    <th>FSSAI Expiry Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="locationtbody">
                                                <?php if (isset($locations) && is_array($locations) && count($locations) > 0) { ?>
                                                <?php foreach ($locations as $loc) { ?>
                                                <tr>
                                                        <input type="hidden" name="loc_id[]" value="<?php echo htmlspecialchars($loc['id'] ?? ''); ?>">
                                                    <td>
                                                        <select name="loc_state[]"
                                                            class="selectpicker form-control loc_state"
                                                            data-width="100%" data-none-selected-text="None selected"
                                                            data-live-search="true" data-container="body">
                                                            <option value="">None selected</option>
                                                            <?php foreach ($state as $key => $value) { ?>
                                                            <?php
																		$stVal = htmlspecialchars($value['short_name'] ?? $value['name'] ?? '');
																		$stTxt = htmlspecialchars($value['state_name'] ?? $value['name'] ?? '');
																		$sel = (isset($loc['StateCode']) && $loc['StateCode'] === ($value['short_name'] ?? $value['name'])) ? 'selected' : '';
																	?>
                                                            <option value="<?php echo $stVal; ?>" <?php echo $sel; ?>>
                                                                <?php echo $stTxt; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>

                                                    <td>
                                                        <select class="form-control city selectpicker loc_city"
                                                            data-width="100%" data-none-selected-text="None selected"
                                                            name="loc_city[]" data-live-search="true"
                                                            data-container="body"
                                                            data-selected-city="<?php echo (int)($loc['CityID'] ?? 0); ?>">
                                                            <option value="">None selected</option>
                                                        </select>
                                                    </td>

                                                    <td><input type="text" name="loc_location[]" class="form-control"
                                                            value="<?php echo htmlspecialchars($loc['LocationName'] ?? ''); ?>"
                                                            placeholder="Location" required></td>
                                                    <td><textarea name="loc_address[]" class="form-control" placeholder="Address"
                                                            rows="1" required><?php echo htmlspecialchars($loc['Address'] ?? ''); ?></textarea>
                                                    </td>
                                                    <td><input type="text" name="loc_pincode[]" class="form-control"
                                                            maxlength="6" minlength="6"
                                                            value="<?php echo htmlspecialchars($loc['PinCode'] ?? ''); ?>"
                                                            placeholder="Pincode" onkeypress="return isNumber(event)" required>
                                                    </td>
                                                    <td><input type="text" name="loc_mobile[]" class="form-control"
                                                            maxlength="10" minlength="10"
                                                            value="<?php echo htmlspecialchars($loc['MobileNo'] ?? ''); ?>"
                                                            placeholder="Mobile" onkeypress="return isNumber(event)" required>
                                                    </td>
                                                    <td><input type="text" name="loc_fssai[]" class="form-control"
                                                            value="<?php echo htmlspecialchars($loc['fssai_no'] ?? ''); ?>"
                                                            placeholder="FSSAI No" required></td>
                                                    <td><input type="date" name="loc_expiry_date[]" class="form-control"
                                                            value="<?php echo htmlspecialchars($loc['fssai_no_expiry'] ?? date('Y-m-d')); ?>" required>
                                                    </td>
                                                    <td>
                                                        <select name="loc_status[]" class="form-control selectpicker"
                                                            data-width="100%" data-container="body" required>
                                                            <option value="active"
                                                                <?php echo (($loc['IsActive'] ?? 'Y') === 'Y') ? 'selected' : ''; ?>>
                                                                Active</option>
                                                            <option value="deactive"
                                                                <?php echo (($loc['IsActive'] ?? 'Y') === 'N') ? 'selected' : ''; ?>>
                                                                Deactive</option>
                                                        </select>
                                                    </td>

                                                  
                                                    <td>
                                                        <a class="btn btn-danger btn-sm"
                                                            onclick="removeLocationRow(this)"><i
                                                                class="fa fa-minus"></i></a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                <?php } else { ?>
                                                <tr>
                                                        <input type="hidden" name="loc_id[]" value="">

                                                    <td>
                                                        <select name="loc_state[]"
                                                            class="selectpicker form-control loc_state"
                                                            data-width="100%" data-none-selected-text="None selected"
                                                            data-live-search="true" data-container="body" required>
                                                            <option value="">None selected</option>
                                                            <?php foreach ($state as $key => $value) { ?>
                                                            <option
                                                                value="<?php echo htmlspecialchars($value['short_name'] ?? $value['name'] ?? ''); ?>">
                                                                <?php echo htmlspecialchars($value['state_name'] ?? $value['name'] ?? ''); ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control city selectpicker loc_city"
                                                            data-width="100%" data-none-selected-text="None selected"
                                                            name="loc_city[]" data-live-search="true"
                                                            data-container="body" required>
                                                            <option value="">None selected</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="loc_location[]" class="form-control"
                                                            placeholder="Location" required></td>
                                                    <td><textarea name="loc_address[]" class="form-control" placeholder="Address"
                                                            rows="1" required></textarea></td>

                                                    <td><input type="text" name="loc_pincode[]" class="form-control"
                                                            maxlength="6" minlength="6" placeholder="Pincode"
                                                            onkeypress="return isNumber(event)" required></td>

                                                    <td><input type="text" name="loc_mobile[]" class="form-control"
                                                            maxlength="10" minlength="10" placeholder="Mobile"
                                                            onkeypress="return isNumber(event)" required></td>

                                                <td><input type="text" name="loc_fssai[]" class="form-control"  
                                                            placeholder="FSSAI No" required></td>
                                                    <td><input type="date" name="loc_expiry_date[]"
                                                            class="form-control" value="<?php echo date('Y-m-d'); ?>" required></td>
                                                   
                                                    <td>
                                                        <select name="loc_status[]" class="form-control selectpicker"
                                                            data-width="100%" data-container="body" required>
                                                            <option value="active">Active</option>
                                                            <option value="deactive">Deactive</option>
                                                        </select>
                                                    </td>
                                                
                                                    
                                                    <td><a class="btn btn-success" onclick="addLocationRow()"><i
                                                                class="fa fa-plus"></i></a></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="action-buttons">
                                        <button type="submit" id="btnSubmitCompany"
                                            class="btn btn-success btn-group-custom">
                                            <i class="fa fa-save"></i>
                                            <span
                                                id="submitLabel"><?php echo isset($company) && $company ? 'Update' : 'Save'; ?>
                                                </span>
                                        </button>
                                        <button type="reset" class="btn btn-warning">
                                            <i class="fa fa-refresh"></i> Reset
                                        </button>
                                        <button type="button" class="btn btn-info" id="btnShowList">
                                            <i class="fa fa-list"></i> Show List
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="modal fade" id="companyListModal" tabindex="-1" role="dialog"
                            aria-labelledby="companyListModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="companyListModalLabel">Company List</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="companyListTable">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Short Code</th>
                                                        <th>Company Name</th>
                                                        <th>State</th>
                                                        <th>CityID</th>
                                                        <th>GST</th>
                                                        <th>Mobile</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="companyListTbody">
                                                    <tr>
                                                        <td colspan="9">Click "Show List" to load data...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                            data-dismiss="modal">Close</button>
                                    </div>

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

<style>
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

.modal-header {
    padding: 8px 15px !important;
}

.modal-header .modal-title {
    margin: 0 !important;
    padding: 0 !important;
    font-size: 15px !important;
}
</style>

<script>
function isNumber(e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    if ((charCode < 48 || charCode > 57) && charCode != 46) return false;
    return true;
}

// Toast helper: Perfex => alert_float / else toastr / else alert
function showToast(type, msg) {
    if (typeof alert_float === 'function') {
        // type: success / danger / warning / info
        alert_float(type, msg);
        return;
    }
    if (typeof toastr !== 'undefined') {
        if (type === 'danger') toastr.error(msg);
        else if (type === 'success') toastr.success(msg);
        else if (type === 'warning') toastr.warning(msg);
        else toastr.info(msg);
        return;
    }
    alert(msg);
}

function addLocationRow() {
	var today = new Date().toISOString().split('T')[0];
    var newRow = `
	<tr>
            <input type="hidden" name="loc_id[]" value="">

		<td>
			<select name="loc_state[]" class="selectpicker form-control loc_state" data-width="100%" data-none-selected-text="None selected" data-live-search="true" data-container="body" required>
				<option value="">None selected</option>
				<?php foreach ($state as $key => $value) { ?>
					<option value="<?php echo htmlspecialchars($value['short_name'] ?? $value['name'] ?? ''); ?>"><?php echo htmlspecialchars($value['state_name'] ?? $value['name'] ?? ''); ?></option>
				<?php } ?>
			</select>
		</td>
		<td>
			<select class="form-control city selectpicker loc_city" data-width="100%" data-none-selected-text="None selected" name="loc_city[]" data-live-search="true" data-container="body" required>
				<option value="">None selected</option>
			</select>
		</td>
		<td><input type="text" name="loc_location[]" class="form-control" placeholder="Location" required></td>
		<td><textarea name="loc_address[]" class="form-control" rows="1" placeholder="Address" required></textarea></td>
		<td><input type="text" name="loc_pincode[]" class="form-control" maxlength="6" minlength="6" placeholder="Pincode" onkeypress="return isNumber(event)" required></td>
		<td><input type="text" name="loc_mobile[]" class="form-control" maxlength="10" minlength="10" placeholder="Mobile" onkeypress="return isNumber(event)" required></td>
		<td><input type="text" name="loc_fssai[]" class="form-control" placeholder="FSSAI No" required></td>
        <td><input type="date" name="loc_expiry_date[]" class="form-control" value="${today}" required></td>
		<td>
			<select name="loc_status[]" class="form-control selectpicker" data-width="100%" data-container="body" required>
				<option value="active">Active</option>
				<option value="deactive">Deactive</option>
			</select>
		</td>
		<td><a class="btn btn-danger btn-sm" onclick="removeLocationRow(this)"><i class="fa fa-minus"></i></a></td>
	</tr>`;
    $("#locationtbody").append(newRow);
    $("#locationtbody tr:last .selectpicker").selectpicker('refresh');
}

function addInitialLocationRow() {
    $('#locationtbody').empty();
	var today = new Date().toISOString().split('T')[0];
    var initRow = `
	<tr>
            <input type="hidden" name="loc_id[]" value="">

		<td>
			<select name="loc_state[]" class="selectpicker form-control loc_state" data-width="100%" data-none-selected-text="None selected" data-live-search="true" data-container="body" required>
				<option value="">None selected</option>
				<?php foreach ($state as $key => $value) { ?>
					<option value="<?php echo htmlspecialchars($value['short_name'] ?? $value['name'] ?? ''); ?>"><?php echo htmlspecialchars($value['state_name'] ?? $value['name'] ?? ''); ?></option>
				<?php } ?>
			</select>
		</td>
		<td>
			<select class="form-control city selectpicker loc_city" data-width="100%" data-none-selected-text="None selected" name="loc_city[]" data-live-search="true" data-container="body" required>
				<option value="">None selected</option>
			</select>
		</td>
		<td><input type="text" name="loc_location[]" class="form-control" placeholder="Location" required></td>
		<td><textarea name="loc_address[]" class="form-control" rows="1" required></textarea></td>
		<td><input type="text" name="loc_pincode[]" class="form-control" maxlength="6" minlength="6" placeholder="Pincode" onkeypress="return isNumber(event)" required></td>
		<td><input type="text" name="loc_mobile[]" class="form-control" maxlength="10" minlength="10" placeholder="Mobile" onkeypress="return isNumber(event)" required></td>
				<td><input type="text" name="loc_fssai[]" class="form-control" placeholder="FSSAI No" required></td>
		<td><input type="date" name="loc_expiry_date[]" class="form-control" value="${today}" required></td>
        <td>
			<select name="loc_status[]" class="form-control selectpicker" data-width="100%" data-container="body" required>
				<option value="active">Active</option>
				<option value="deactive">Deactive</option>
			</select>
		</td>

		<td><a class="btn btn-success" onclick="addLocationRow()"><i class="fa fa-plus"></i></a></td>
	</tr>`;
    $("#locationtbody").append(initRow);
    $("#locationtbody tr:last .selectpicker").selectpicker('refresh');
}

function removeLocationRow(btn) {
    $(btn).closest('tr').remove();
    refreshLocationActions();
}

function refreshLocationActions() {
    var $rows = $('#locationtbody').find('tr');
    var isUpdateMode = $('#submitLabel').text().trim() === 'Update';
    
    $rows.each(function(idx) {
        var $actionTd = $(this).find('td').last();
        if (idx === $rows.length - 1) {
            // Last row always shows plus button
            $actionTd.html(
                '<a class="btn btn-success" onclick="addLocationRow()"><i class="fa fa-plus"></i></a>');
        } else {
            // Other rows: show minus only if in Save mode, hide if in Update mode
            if (isUpdateMode) {
                $actionTd.html('');  // No button in Update mode
            } else {
                $actionTd.html(
                    '<a class="btn btn-danger btn-sm" onclick="removeLocationRow(this)"><i class="fa fa-minus"></i></a>'
                );
            }
        }
    });
}

function loadCitiesIntoDropdown($cityDD, stateCode, selectedCityId) {
    if (!stateCode) {
        $cityDD.find('option').remove();
        $cityDD.append(new Option('None selected', ""));
        $cityDD.selectpicker('refresh');
        return;
    }

    $.post("<?php echo base_url(); ?>admin/company_master/GetCity", {
        StateID: stateCode
    }, function(data) {
        $cityDD.find('option').remove();
        $cityDD.append(new Option('None selected', ""));
        for (var i = 0; i < data.length; i++) {
            $cityDD.append(new Option(data[i].city_name, data[i].id));
        }
        if (selectedCityId) $cityDD.val(selectedCityId);
        $cityDD.selectpicker('refresh');
    }, 'json');
}

function populateCompanyForm(resp) {
    var c = resp.company || {};

    $('#comp_short').val((c.comp_short || '').toUpperCase());
    $('#company_name').val(c.company_name || '');
    $('#address').val(c.address || '');
    $('#pincode').val(c.pincode || '');
    $('#gst_no').val(c.gst || '');
    $('#mobile').val(c.mobile1 || '');
    $('#email').val(c.BusinessEmail || '');

    // Status (DB: 1/2)
    var statusText = (parseInt(c.status, 10) === 1) ? 'active' : 'deactive';
    $('#status').val(statusText).selectpicker('refresh');

    // State + city
    var stateCode = c.state || '';
    $('#state').val(stateCode);
    $('#state').selectpicker('refresh');

    loadCitiesIntoDropdown($('#city'), stateCode, (c.CityID || ''));

    // id + action + label
    $('#company_id_input').val(c.id || '');
    $('#companyForm').attr('action', "<?php echo admin_url('company_master/company'); ?>" + '/' + (c.id || ''));
    $('#submitLabel').text('Update');

    // locations
    var locs = resp.locations || [];
    $('#locationtbody').empty();

    if (locs.length === 0) {
        addInitialLocationRow();
        refreshLocationActions();
        return;
    }

    for (var j = 0; j < locs.length; j++) {
        var L = locs[j];

        var newRow = '<tr>';
        newRow += '<input type="hidden" name="loc_id[]" value="' + (L.id || '') + '">'; // ✅ Location ID

        newRow +=
            '<td><select name="loc_state[]" class="selectpicker form-control loc_state" data-width="100%" data-none-selected-text="None selected" data-live-search="true" data-container="body">';
        newRow += '<option value="">None selected</option>';
        <?php foreach ($state as $key => $value) { ?>
        newRow += '<option value="<?php echo htmlspecialchars($value['short_name'] ?? $value['name'] ?? ''); ?>"' +
            (L.StateCode == '<?php echo addslashes($value['short_name'] ?? $value['name'] ?? ''); ?>' ? ' selected' :
                '') +
            '><?php echo addslashes(htmlspecialchars($value['state_name'] ?? $value['name'] ?? '')); ?></option>';
        <?php } ?>
        newRow += '</select></td>';

        newRow +=
            '<td><select class="form-control city selectpicker loc_city" data-width="100%" data-none-selected-text="None selected" name="loc_city[]" data-live-search="true" data-container="body"></select></td>';
        newRow += '<td><input type="text" name="loc_location[]" class="form-control" value="' + (L.LocationName || '') +
            '" placeholder="Location"></td>';
        newRow += '<td><textarea name="loc_address[]" class="form-control" rows="1">' + (L.Address || '') +
            '</textarea></td>';
        newRow +=
            '<td><input type="text" name="loc_pincode[]" class="form-control" maxlength="6" minlength="6" value="' + (L
                .PinCode || '') + '" placeholder="Pincode" onkeypress="return isNumber(event)"></td>';
        newRow +=
            '<td><input type="text" name="loc_mobile[]" class="form-control" maxlength="10" minlength="10" value="' + (L
                .MobileNo || '') + '" placeholder="Mobile" onkeypress="return isNumber(event)"></td>';
                 newRow += '<td><input type="text" name="loc_fssai[]" class="form-control" value="' + (L.fssai_no || '') +
            '" placeholder="FSSAI No"></td>';
        newRow += '<td><input type="date" name="loc_expiry_date[]" class="form-control" value="' + (L.fssai_no_expiry ||
            '') + '"></td>';
        newRow +=
            '<td><select name="loc_status[]" class="form-control selectpicker" data-width="100%" data-container="body">';
        newRow += '<option value="active"' + (((L.IsActive || 'Y') === 'Y') ? ' selected' : '') + '>Active</option>';
        newRow += '<option value="deactive"' + (((L.IsActive || 'Y') === 'N') ? ' selected' : '') +
        '>Deactive</option>';
        newRow += '</select></td>';
       
        newRow += '<td></td>';
        newRow += '</tr>';

        $('#locationtbody').append(newRow);
        var $lastRow = $('#locationtbody tr:last');
        $lastRow.find('.selectpicker').selectpicker('refresh');

        // load cities for loc row
        loadCitiesIntoDropdown($lastRow.find('select.loc_city'), (L.StateCode || ''), (L.CityID || ''));
    }

    refreshLocationActions();
}

// Short code  fetch
function fetchByShortCode(shortCode) {
    shortCode = (shortCode || '').trim().toUpperCase();
    if (!shortCode) return;

    var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>";
    var csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";

    $.ajax({
        type: 'POST',
        url: "<?php echo admin_url('company_master/get_company_by_shortcode'); ?>",
        dataType: 'json',
        data: {
            [csrfName]: csrfHash,
            comp_short: shortCode
        },
        success: function(resp) {
            if (!resp) {
                showToast('danger', 'Server response invalid');
                return;
            }
            if (resp.status === 'success') {
                populateCompanyForm(resp);
                return;
            }
            if (resp.status === 'not_found') {
                showToast('warning', 'No data found. Ready for new entry.');
                // Clear all fields for new entry
                $('#company_name').val('');
                $('#address').val('');
                $('#pincode').val('');
                $('#gst_no').val('');
                $('#mobile').val('');
                $('#email').val('');
                $('#state').val('').selectpicker('refresh');
                $("#city").find('option').remove();
                $("#city").append(new Option('None selected', ""));
                $('#city').selectpicker('refresh');
                $('#status').val('active').selectpicker('refresh');
                
                // Reset locations
                addInitialLocationRow();
                refreshLocationActions();
                
                $('#company_id_input').val('');
                $('#companyForm').attr('action', "<?php echo admin_url('company_master/company'); ?>");
                $('#submitLabel').text('Save');
                return;
            }
            showToast('danger', resp.message || 'Failed');
        },
        error: function(xhr) {
            showToast('danger', 'Server error while searching by short code');
            console.log(xhr.responseText);
        }
    });
}

$(document).ready(function() {

	// Form validation
	$('#companyForm').on('submit', function(e) {
		var isValid = true;
		var errorMsg = '';
		
		// Validate company section fields (all required)
		var companyFields = ['comp_short', 'company_name', 'state', 'city', 'address', 'pincode', 'gst_no', 'mobile', 'email', 'status'];
		for (var i = 0; i < companyFields.length; i++) {
			var fieldId = companyFields[i];
			var $field = $('#' + fieldId);
			var val = $field.val();
			
			if (val === null || val === undefined || (typeof val === 'string' && val.trim() === '')) {
				isValid = false;
				errorMsg = 'All company fields are required';
				break;
			}
		}
		
		// Validate email format
		if (isValid) {
			var email = $('#email').val();
			var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!emailRegex.test(email)) {
				isValid = false;
				errorMsg = 'Please enter a valid email address';
			}
		}
		
		// Validate pincode (exactly 6 digits)
		if (isValid) {
			var pincode = $('#pincode').val();
			if (!/^[0-9]{6}$/.test(pincode)) {
				isValid = false;
				errorMsg = 'Pincode must be exactly 6 digits';
			}
		}
		
		// Validate mobile (exactly 10 digits)
		if (isValid) {
			var mobile = $('#mobile').val();
			if (!/^[0-9]{10}$/.test(mobile)) {
				isValid = false;
				errorMsg = 'Mobile number must be exactly 10 digits';
			}
		}
		
		// Validate location rows
		if (isValid) {
			var $locRows = $('#locationtbody').find('tr');
			if ($locRows.length === 0) {
				isValid = false;
				errorMsg = 'At least one location is required';
			} else {
				// Check each location row for required fields
				$locRows.each(function() {
					var $row = $(this);
					var locState = $row.find('select[name="loc_state[]"]').val();
					var locCity = $row.find('select[name="loc_city[]"]').val();
					var locLocation = $row.find('input[name="loc_location[]"]').val();
					var locAddress = $row.find('textarea[name="loc_address[]"]').val();
					var locPincode = $row.find('input[name="loc_pincode[]"]').val();
					var locMobile = $row.find('input[name="loc_mobile[]"]').val();
					var locStatus = $row.find('select[name="loc_status[]"]').val();
					var locFssai = $row.find('input[name="loc_fssai[]"]').val();
					var locExpiry = $row.find('input[name="loc_expiry_date[]"]').val();
					
					// Skip validation for the last row if it's empty (the +/- button row)
					var isLastRow = ($row.index() === $locRows.length - 1);
					var isEmpty = !locState && !locCity && !locLocation && !locAddress && !locPincode && !locMobile && !locStatus && !locFssai && !locExpiry;
					
					if (isLastRow && isEmpty) {
						return true; // Skip last empty row
					}
					
					if (!locState || !locCity || !locLocation || !locAddress || !locPincode || !locMobile || !locStatus || !locFssai || !locExpiry) {
						isValid = false;
						errorMsg = 'All location fields are required';
						return false;
					}
					
					// Validate location pincode
					if (!/^[0-9]{6}$/.test(locPincode)) {
						isValid = false;
						errorMsg = 'Location pincode must be exactly 6 digits';
						return false;
					}
					
					// Validate location mobile
					if (!/^[0-9]{10}$/.test(locMobile)) {
						isValid = false;
						errorMsg = 'Location mobile must be exactly 10 digits';
						return false;
					}
				});
			}
		}
		
		if (!isValid) {
			e.preventDefault();
			showToast('danger', errorMsg || 'All fields are required.');
		}
	});

    // Short code uppercase while typing
    $('#comp_short').on('input', function() {
        this.value = (this.value || '').toUpperCase();
    });

    // Short code search on blur + Enter
    $('#comp_short').on('blur', function() {
        var v = $(this).val();
        if (v && v.trim() !== '') fetchByShortCode(v);
    });
    $('#comp_short').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            fetchByShortCode($(this).val());
        }
    });

$('#comp_short').off('dblclick').on('dblclick', function() {
    $('#btnShowList').trigger('click');
});

    // Show list modal
    $('#btnShowList').on('click', function() {
        $('#companyListModal').modal('show');
        $('#companyListTbody').html('<tr><td colspan="9">Loading...</td></tr>');

        var csrfName = "<?php echo $this->security->get_csrf_token_name(); ?>";
        var csrfHash = "<?php echo $this->security->get_csrf_hash(); ?>";

        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('company_master/get_company_list'); ?>",
            dataType: 'json',
            data: {
                [csrfName]: csrfHash
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function(res) {
                if (!res || res.status !== 'success') {
                    $('#companyListTbody').html(
                        '<tr><td colspan="9">Failed to load data.</td></tr>');
                    return;
                }

                var data = res.data || [];
                if (data.length === 0) {
                    $('#companyListTbody').html(
                        '<tr><td colspan="9">No records found.</td></tr>');
                    return;
                }

                var rows = '';
                for (var i = 0; i < data.length; i++) {
                    var r = data[i];
                    var statusText = (parseInt(r.status, 10) === 1) ? 'Active' : 'Deactive';

                    rows += '<tr data-id="' + (r.id || '') + '">' +
                        '<td>' + (r.id || '') + '</td>' +
                        '<td>' + (r.comp_short || '') + '</td>' +
                        '<td>' + (r.company_name || '') + '</td>' +
                        '<td>' + (r.state || '') + '</td>' +
                        '<td>' + (r.CityID || '') + '</td>' +
                        '<td>' + (r.gst || '') + '</td>' +
                        '<td>' + (r.mobile1 || '') + '</td>' +
                        '<td>' + (r.BusinessEmail || '') + '</td>' +
                        '<td>' + statusText + '</td>' +
                        '</tr>';
                }

                $('#companyListTbody').html(rows);

                // Row click => load by id
                $('#companyListTbody').off('click', 'tr').on('click', 'tr', function() {
                    var id = $(this).data('id');
                    if (!id) return;

                    var csrfName2 =
                        "<?php echo $this->security->get_csrf_token_name(); ?>";
                    var csrfHash2 =
                        "<?php echo $this->security->get_csrf_hash(); ?>";

                    $.ajax({
                        type: 'POST',
                        url: "<?php echo admin_url('company_master/get_company'); ?>",
                        dataType: 'json',
                        data: {
                            [csrfName2]: csrfHash2,
                            id: id
                        },
                        success: function(resp) {
                            if (!resp || resp.status !== 'success') {
                                showToast('danger',
                                    'Failed to load company details'
                                    );
                                return;
                            }
                            populateCompanyForm(resp);
                            $('#companyListModal').modal('hide');
                        },
                        error: function(xhr) {
                            showToast('danger',
                                'Server error while loading company details'
                                );
                            console.log(xhr.responseText);
                        }
                    });
                });
            },
            error: function(xhr) {
                $('#companyListTbody').html(
                    '<tr><td colspan="9">Server error. Check Network tab.</td></tr>');
                console.log('AJAX Error:', xhr.status, xhr.responseText);
            }
        });
    });

    // State change => load cities
    $('#state').on('change', function() {
        var StateID = $(this).val();
        var url = "<?php echo base_url(); ?>admin/company_master/GetCity";
        if (StateID !== '') {
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    StateID: StateID
                },
                dataType: 'json',
                success: function(data) {
                    $("#city").find('option').remove();
                    $("#city").append(new Option('None selected', ""));
                    for (var i = 0; i < data.length; i++) {
                        $("#city").append(new Option(data[i].city_name, data[i].id));
                    }
                    $('#city').selectpicker('refresh');
                }
            });
        } else {
            $("#city").find('option').remove();
            $("#city").append(new Option('None selected', ""));
            $('#city').selectpicker('refresh');
        }
    });

    // loc_state change => load loc_city
    $(document).on('change', '.loc_state', function() {
        var StateID = $(this).val();
        var $cityDropdown = $(this).closest('tr').find('select.loc_city');
        loadCitiesIntoDropdown($cityDropdown, StateID, '');
    });

    // Existing rows (edit case) => load cities by data-selected-city
    $('#locationtbody tr').each(function() {
        var $row = $(this);
        var stateCode = $row.find('.loc_state').val();
        var selectedCity = $row.find('select.loc_city').attr('data-selected-city') || '';
        loadCitiesIntoDropdown($row.find('select.loc_city'), stateCode, selectedCity);
    });

    refreshLocationActions();

    // Reset: full reset + single initial row
    $('#companyForm').on('reset', function() {
        setTimeout(function() {
            $('#state').val('').selectpicker('refresh');
            $('#city').find('option').remove();
            $('#city').append(new Option('None selected', ""));
            $('#city').selectpicker('refresh');

            $('#comp_short').val('');
            $('#company_name').val('');
            $('#address').val('');
            $('#pincode').val('');
            $('#gst_no').val('');
            $('#mobile').val('');
            $('#email').val('');

            $('#status').val('active').selectpicker('refresh');

            addInitialLocationRow();
            refreshLocationActions();

            $('#company_id_input').val('');
            $('#companyForm').attr('action',
                "<?php echo admin_url('company_master/company'); ?>");
            $('#submitLabel').text('Save Company');

            $('.selectpicker').selectpicker('refresh');
        }, 10);
    });
});


// Short Code input  DOUBLE CLICK => Show List modal open
$('#comp_short').off('dblclick').on('dblclick', function() {
    $('#btnShowList').trigger('click');
});


$('#comp_short').on('click', function () {
    $('#companyForm')[0].reset();   // native reset
});

</script>