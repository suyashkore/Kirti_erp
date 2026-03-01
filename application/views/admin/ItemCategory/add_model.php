<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<Style>
	.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #28a745;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 24px;
}

.slider.round:before {
  border-radius: 50%;
}

	</style>
<div class="modal fade" id="item_category_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">
					<!-- <span class="edit-title">Edit Customer Category</span> -->
					<span class="add-title">Add Item Category</span>
				</h4>
			</div>
			<div class="modal-body" style="padding: 0.5rem 1.25rem;">
				<div class="row">
					<?php
					$validate = array('required' => true);
					?>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="CategoryCode">
							<label for="CategoryCode" class="control-label"><small class="req text-danger">* </small>Item Category Code</label>
							<input type="text" id="CategoryCode" required name="CategoryCode" class="form-control" value="" readonly>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group" app-field-wrapper="CategoryName">
							<label for="CategoryName" class="control-label"><small class="req text-danger">* </small>Item Category Description</label>
							<input type="text" id="CategoryName" required name="CategoryName" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-3">
                        <div class="form-group" app-field-wrapper="ItemType">
                            <label class="control-label">Item Type</label>
							<select class="selectpicker display-block" data-width="100%" id="ItemType" name="ItemType" data-live-search="true">
										<option value=""></option>
										<option value="Inventory">Inventory</option>
										<option value="Service">Service</option>
									</select>

                            
                        </div>
                    </div>
					<div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Blocked <span style="color: red;">*</span></label>
                            <div style="margin-top: 8px;">
                                <label class="switch">
                                    <input type="checkbox" name="isactive" id="isactive_chk">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>


					
				</div>
				<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-info">Save</button>
					</div>
			</div>
		</div>