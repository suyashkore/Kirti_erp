<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="panel_s">
          <div class="panel-body">
            <style>
              @media (max-width: 767px) {
                .mobile-menu-btn { display: block !important; margin-bottom: 10px; width: 100%; text-align: left; }
                .custombreadcrumb { display: none !important; }
                .custombreadcrumb.open { display: block !important; }
                .custombreadcrumb li { display: block; padding: 8px 10px; border-bottom: 1px solid #eee; }
                .custombreadcrumb li a { display: block; }
                .custombreadcrumb li+li:before { content: none; }
              }
              .mobile-menu-btn { display: none; }
            </style>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Item Master</b></li>
              </ol>
            </nav>
            <hr class="hr_style"><br>
            <input type="hidden" name="form_mode" id="form_mode" value="add">
            <input type="hidden" name="item_id" id="item_id" value="">
            <form action="" method="post" id="item-form" autocomplete="off" enctype="multipart/form-data" novalidate>
              <div class="row">
                <div class="col-md-12 mbot10">
                  <h4 class="bold">Main Information</h4>
                  <hr class="hr_style">
                </div>
                <div class="col-md-3 mbot5">
                  <label for="item_type" class="control-label">
                    <small class="req text-danger">* </small>Item Type
                  </label>
                  <select name="item_type" id="item_type" class="form-control selectpicker" data-live-search="true" required onchange="getCustomDropdownList('item_type', this.value, 'item_category'); getCustomDropdownList('item_type', this.value, 'item_main_group');">
                    <option value="" selected disabled>None selected</option>
                    <?php
                    if (!empty($types)) : 
                      foreach ($types as $value) :
                        echo '<option value="' . $value['id'] . '">' . $value['ItemTypeName'] . '</option>';
                        endforeach;
                    endif;
                    ?>
                  </select>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="main_item_group">
                    <label for="main_item_group" class="control-label">
                      <small class="req text-danger">* </small>Item Main Group
                    </label>
                    <select name="item_main_group" id="item_main_group" class="form-control selectpicker" data-live-search="true" required onchange="getCustomDropdownList('item_main_group', this.value, 'item_sub_group1')">
                        <option value="" selected disabled>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_sub_group1">
                    <label for="item_sub_group1" class="control-label">
                      <small class="req text-danger">* </small>Item Sub Group 1
                    </label>
                    <select name="item_sub_group1" id="item_sub_group1" class="form-control selectpicker" data-live-search="true" required onchange="getCustomDropdownList('item_sub_group1', this.value, 'item_sub_group2')">
                      <option value="" selected disabled>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_sub_group2">
                    <label for="item_sub_group2" class="control-label">
                      <small class="req text-danger">* </small>Item Sub Group 2
                    </label>
                    <select name="item_sub_group2" id="item_sub_group2" required class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_division">
                    <label for="item_division" class="control-label">
                      <small class="req text-danger">* </small>Item Division
                    </label>
                    <select name="item_division" id="item_division" required class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
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
                <div class="col-md-3 mbot5">
                  <label for="item_category" class="control-label">
                    <small class="req text-danger">* </small>Item Category
                  </label>
                  <select name="item_category" id="item_category" required class="form-control selectpicker" data-live-search="true" onchange="getNextItemCode(this.value);">
                    <option value="" selected disabled>None selected</option>
                  </select>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="item_code">
                    <label for="item_code" class="control-label">
                      <small class="req text-danger">* </small>Item Code
                    </label>
                    <input type="text" id="item_code" required name="item_code" class="form-control" value="" readonly>
                  </div>
                </div>
                <div class="col-md-4 mbot5">
                  <div class="form-group" app-field-wrapper="item_name">
                    <label for="item_name" class="control-label">
                      <small class="req text-danger">* </small>Item Name
                    </label>
                    <input type="text" id="item_name" required name="item_name" class="form-control" value="" placeholder="Enter Item Name">
                  </div>
                </div>
                <div class="col-md-5 mbot5">
                  <div class="form-group" app-field-wrapper="item_description">
                    <label for="item_description" class="control-label">
                      Item Description
                    </label>
                    <textarea type="text" id="item_description" name="item_description" class="form-control" rows="2" value="" placeholder="Enter Item Description"></textarea>
                  </div>
                </div>
                
                <div class="col-md-12 mbot10">
                  <h4 class="bold">General Information</h4>
                  <hr class="hr_style">
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="hsn">
                    <label for="hsn" class="control-label">
                      <small class="req text-danger">* </small>HSN
                    </label>
                    <select name="hsn" id="hsn" required class="form-control selectpicker" data-live-search="true">
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
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="gst">
                    <label for="gst" class="control-label">
                      <small class="req text-danger">* </small>GST
                    </label>
                    <select name="gst" id="gst" required class="form-control selectpicker" data-live-search="true">
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
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="uom">
                    <label for="uom" class="control-label">
                      <small class="req text-danger">* </small>UOM
                    </label>
                    <select name="uom" id="uom" required class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($uom)) :
                        foreach ($uom as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['ShortCode'] . ' - ' . $value['UnitName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="packing_quantity">
                    <label for="packing_quantity" class="control-label">
                      <small class="req text-danger">* </small>Packing Quantity
                    </label>
                    <input type="tel" id="packing_quantity" required name="packing_quantity" class="form-control" value="1" min="1" placeholder="123" onkeypress="return isNumberOnly(event)">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="unit_weight">
                    <label for="unit_weight" class="control-label">
                      <small class="req text-danger">* </small>Unit Weight
                    </label>
                    <input type="tel" id="unit_weight" required name="unit_weight" class="form-control" value="" min="1" placeholder="123" onkeypress="return isFloatOnly(event, this)">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="unit_weight_in">
                    <label for="unit_weight_in" class="control-label">
                      <small class="req text-danger">* </small>Unit Weight In
                    </label>
                    <select name="unit_weight_in" id="unit_weight_in" required class="form-control selectpicker">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($weight_unit)) :
                        foreach ($weight_unit as $value) :
                          echo '<option value="' . $value['ShortCode'] . '">' . $value['UnitName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="packing_weight">
                    <label for="packing_weight" class="control-label">Packing Weight</label>
                    <input type="tel" id="packing_weight" name="packing_weight" class="form-control" value="" placeholder="123.00" onkeypress="return isFloatOnly(event, this)">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="brand">
                    <label for="brand" class="control-label">Brand</label>
                    <select name="brand" id="brand" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($brand)) :
                        foreach ($brand as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['BrandName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="priority">
                    <label for="priority" class="control-label">Priority</label>
                    <select name="priority" id="priority" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($priority)) :
                        foreach ($priority as $value) :
                          echo '<option value="' . $value['id'] . '">' . $value['PriorityName'] . '</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="upper_tolerence">
                    <label for="upper_tolerence" class="control-label">Upper Tolerence</label>
                    <input type="tel" id="upper_tolerence" name="upper_tolerence" class="form-control" value="" placeholder="123.00" onkeypress="return isFloatOnly(event, this)">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="down_tolerence">
                    <label for="down_tolerence" class="control-label">Down Tolerence</label>
                    <input type="tel" id="down_tolerence" name="down_tolerence" class="form-control" value="" placeholder="123.00" onkeypress="return isFloatOnly(event, this)">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="unloading_rate">
                    <label for="unloading_rate" class="control-label">Unloading Rate</label>
                    <input type="tel" id="unloading_rate" name="unloading_rate" class="form-control" value="" placeholder="123.00" onkeypress="return isFloatOnly(event, this)">
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="bag_applicable">
                    <label for="bag_applicable" class="control-label">Is Bag Applicable</label>
                    <select name="bag_applicable" id="bag_applicable" class="form-control selectpicker">
                      <option value="Y" selected>Yes</option>
                      <option value="N">No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="bom_applicable">
                    <label for="bom_applicable" class="control-label">Is BOM Applicable</label>
                    <select name="bom_applicable" id="bom_applicable" class="form-control selectpicker">
                      <option value="Y">Yes</option>
                      <option value="N" selected>No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 mbot5">
                  <div class="form-group" app-field-wrapper="is_active">
                    <label for="is_active" class="control-label">Is Active</label>
                    <select name="is_active" id="is_active" class="form-control selectpicker">
                      <option value="Y" selected>Yes</option>
                      <option value="N">No</option>
                    </select>
                  </div>
                </div>
                  
                <div class="col-md-12 mbot10">
                  <h4 class="bold">Quality Information</h4>
                  <hr class="hr_style">
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="quality_managed">
                    <label for="quality_managed" class="control-label">Quality Managed</label>
                    <select name="quality_managed" id="quality_managed" class="form-control selectpicker">
                      <option value="Y">Yes</option>
                      <option value="N" selected>No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="batch_managed">
                    <label for="batch_managed" class="control-label">Batch Managed</label>
                    <select name="batch_managed" id="batch_managed" class="form-control selectpicker">
                      <option value="Y">Yes</option>
                      <option value="N" selected>No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="batch_managed_method">
                    <label for="batch_managed_method" class="control-label">Batch Managed Type</label>
                    <select name="batch_managed_method" id="batch_managed_method" class="form-control selectpicker">
                      <option value="" selected>None</option>
                      <option value="A">Auto</option>
                      <option value="M">Manual</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="self_life">
                    <label for="self_life" class="control-label">Self Life (in Days)</label>
                    <input type="tel" id="self_life" name="self_life" class="form-control" value="" placeholder="123" onkeypress="return isNumberOnly(event)">
                  </div>
                </div>

                <div class="col-md-12 mbot10">
                  <h4 class="bold">Planing Information</h4>
                  <hr class="hr_style">
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="max_level">
                    <label for="max_level" class="control-label">Maximum Level</label>
                    <input type="tel" id="max_level" name="max_level" class="form-control" value="" placeholder="123" onkeypress="return isNumberOnly(event)">
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="min_level">
                    <label for="min_level" class="control-label">MinimumLevel</label>
                    <input type="tel" id="min_level" name="min_level" class="form-control" value="" placeholder="123" onkeypress="return isNumberOnly(event)">
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="reorder_level">
                    <label for="reorder_level" class="control-label">Reorder Level</label>
                    <input type="tel" id="reorder_level" name="reorder_level" class="form-control" value="" placeholder="123" onkeypress="return isNumberOnly(event)">
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="reorder_quantity">
                    <label for="reorder_quantity" class="control-label">Reorder Quantity</label>
                    <input type="tel" id="reorder_quantity" name="reorder_quantity" class="form-control" value="" placeholder="123.00" onkeypress="return isFloatOnly(event, this)">
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="mrp">
                    <label for="mrp" class="control-label">MRP</label>
                    <input type="tel" id="mrp" name="mrp" class="form-control" value="" placeholder="123.00" onkeypress="return isFloatOnly(event, this)">
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="prefer_vendor">
                    <label for="prefer_vendor" class="control-label">Prefer Vendor</label>
                    <select name="prefer_vendor" id="prefer_vendor" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="vendor_part_no">
                    <label for="vendor_part_no" class="control-label">Vendor Part No</label>
                    <input type="text" id="vendor_part_no" name="vendor_part_no" class="form-control" value="" placeholder="Part No">
                  </div>
                </div>

                <div class="col-md-12 mbot10">
                    <h4 class="bold">Information</h4>
                    <hr class="hr_style">
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="hindi_name">
                    <label for="hindi_name" class="control-label">Devanagari Name</label>
                    <input type="text" id="hindi_name" name="hindi_name" class="form-control" value="" placeholder="Devanagari Name">
                  </div>
                </div>
                <div class="col-md-3 mbot5">
                  <div class="form-group" app-field-wrapper="attachment">
                    <label for="attachment" class="control-label">
                      Attachment
                      <a href="" id="imgLink" target="_blank" style="display: none; font-size: 70%;">( View )</a>
                    </label>
                    <input type="file" id="attachment" name="attachment" accept="image/*" class="form-control">
                  </div>
                </div>
                <div class="col-md-6 mbot5">
                  <div class="form-group" app-field-wrapper="additional_info">
                    <label for="additional_info" class="control-label">Additional Information</label>
                    <textarea id="additional_info" name="additional_info" class="form-control" rows="2" placeholder="Additional Information"></textarea>
                  </div>
                </div>

              </div>
              <div class="col-md-12 bottom-action-bar">
                <button type="submit" class="btn btn-success saveBtn <?php echo (has_permission_new('items', '', 'create')) ? '' : 'disabled'; ?>"><i class="fa fa-save"></i> Save</button>
                <button type="submit" class="btn btn-success updateBtn <?php echo (has_permission_new('items', '', 'edit')) ? '' : 'disabled'; ?>" style="display: none;"><i class="fa fa-save"></i> Update</button>
                <button type="button" class="btn btn-danger" onclick="ResetForm();"><i class="fa fa-refresh"></i> Reset</button>
                <button type="button" class="btn btn-info" onclick="$('#ItemListModal').modal('show');"><i class="fa fa-list"></i> Show List</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="ItemListModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding:5px 10px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Item List</h4>
      </div>
      <div class="modal-body" style="padding:0px 5px !important">
        
        <div class="table-ItemListModal tableFixHead2">
          <table class="tree table table-striped table-bordered table-ItemListModal tableFixHead2" id="table_ItemListModal" width="100%">
            <thead>
              <tr>
                <th class="sortablePop">Item Code</th>
                <th class="sortablePop">Item Name</th>
                <th class="sortablePop">Type</th>
                <th class="sortablePop">Main Group</th>
                <th class="sortablePop">Group1</th>
                <th class="sortablePop">Group2</th>
                <th class="sortablePop">Category</th>
                <th class="sortablePop">Division</th>
                <th class="sortablePop">HSN</th>
                <th class="sortablePop">GST</th>
                <th class="sortablePop">Unit</th>
                <th class="sortablePop">IsActive</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($item_list as $key => $value) {
              ?>
              <tr class="get_ItemDetails" data-id="<?= $value["id"]; ?>" onclick="getItemDetails(<?= $value['id']; ?>)">
                <td><?= $value["ItemID"];?></td>
                <td><?= $value["ItemName"];?></td>
                <td><?= $value["ItemTypeName"];?></td>
                <td><?= $value["main_group_name"];?></td>
                <td><?= $value["sub_group1_name"];?></td>
                <td><?= $value["sub_group2_name"];?></td>
                <td><?= $value["CategoryName"];?></td>
                <td><?= $value["division_name"];?></td>
                <td><?= $value["hsn_code"];?></td>
                <td><?= $value["taxrate"];?>%</td>
                <td><?= $value["ShortCode"];?></td>
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
    function isNumberOnly(event) {
      return event.charCode >= 48 && event.charCode <= 57;
    }

    function isFloatOnly(event, element) {
      const charCode = event.charCode;
      // Allow numbers (0–9)
      if (charCode >= 48 && charCode <= 57) {
        return true;
      }
      // Allow ONE dot (.)
      if (charCode === 46) {
        // Block if dot already exists
        if (element.value.includes('.')) {
          return false;
        }
        return true;
      }
      // Block everything else
      return false;
    }

    function ResetForm(){
      $('.saveBtn').show();
      $('.updateBtn').hide();
      $('#form_mode').val('add');
      $('#item-form')[0].reset();
      $('#item_type, #item_category').attr('disabled', false);
      $('#imgLink').attr('href', '').hide();
      $('.selectpicker').selectpicker('refresh');
    }

    $("#item_name").dblclick(function(){
			$('#ItemListModal').modal('show');
		}); 

    $('#packing_quantity, #unit_weight').on('input', function() {
      var packing_quantity = parseFloat($('#packing_quantity').val() || 1);
      var unit_weight = parseFloat($('#unit_weight').val() || 1);
      var quantity = packing_quantity * unit_weight;
      $('#packing_weight').val(quantity);
    })

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
            let html = `<option value="" selected disabled>None selected</option>`;

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
          }else{
            alert_float('danger', response.message);
          }
        }
      });
    }

    function getNextItemCode(category_id){
      $.ajax({
        url:"<?= admin_url(); ?>ItemMaster/GetNextItemCode",
        type: 'POST',
        dataType: 'json',
        data: {
          category_id: category_id
        },
        success: function(response){
          if(response.success == true){
            $('#item_code').val(response.data);
          }else{
            alert_float('danger', response.message);
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

    function get_required_fields(form_id){
        let fields = [];
        $('#' + form_id + ' [required]').each(function(){
            fields.push($(this).attr('id'));
        });
        return fields;
    }

    $('#item-form').submit(function(e){
      e.preventDefault();

      let form_mode = $('#form_mode').val();
      
      let required_fields = get_required_fields('item-form');
      let validated = validate_fields(required_fields);

      if(validated === false){
        return;
      }
      
      var form_data = new FormData(this);
      form_data.append(
        '<?= $this->security->get_csrf_token_name(); ?>',
        $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
      );
      if(form_mode == 'edit'){
        form_data.append('item_id', $('#item_id').val());
      }

      $.ajax({
        url:"<?= admin_url(); ?>ItemMaster/SaveItemMaster",
        method:"POST",
        dataType:"JSON",
        data:form_data,
        contentType: false,
        cache: false,
        processData: false,
				beforeSend: function () {
          $('button[type=submit]').attr('disabled', true);
				},
				complete: function () {
          $('button[type=submit]').attr('disabled', false);
				},
        success: function(response){
          if(response.success == true){
            alert_float('success', response.message);
            let html = `<tr class="get_ItemDetails" data-id="${response.data.id}" onclick="getItemDetails(${response.data.id})">
              <td>${response.data.ItemID}</td>
              <td>${response.data.ItemName}</td>
              <td>${response.data.ItemTypeName}</td>
              <td>${response.data.main_group_name}</td>
              <td>${response.data.sub_group1_name}</td>
              <td>${response.data.sub_group2_name}</td>
              <td>${response.data.CategoryName}</td>
              <td>${response.data.division_name}</td>
              <td>${response.data.hsn_code}</td>
              <td>${response.data.taxrate}%</td>
              <td>${response.data.ShortCode}</td>
              <td>${response.data.IsActive == 'Y' ? 'Yes' : 'No'}</td>
            </tr>`;
            if(form_mode == 'edit'){
              $('.get_ItemDetails[data-id="'+response.data.id+'"]').replaceWith(html);
            }else{
              $('#table_ItemListModal tbody').prepend(html);
            }
            ResetForm();
          }else{
            alert_float('warning', response.message);
          }
        }
      });
    });
    
    function getItemDetails(item_id){
      $('.saveBtn').hide();
      $('.updateBtn').show();
      $('#item_id').val(item_id);
      $('#form_mode').val('edit');
      $('#imgLink').attr('href', '').hide();
      $('#item_type, #item_category').attr('disabled', true);

      $.ajax({
        url:"<?= admin_url(); ?>ItemMaster/GetItemDetails",
        method:"POST",
        dataType:"JSON",
        data: {
          item_id: item_id
        },
        success: function(response){
          if(response.success == true){
            let d = response.data;
            $('#item_type').val(d.ItemTypeID);
            getCustomDropdownList('item_type', d.ItemTypeID, 'item_main_group', d.MainGrpID, function(){
              getCustomDropdownList('item_main_group', d.MainGrpID, 'item_sub_group1', d.SubGrpID1, function(){
                getCustomDropdownList( 'item_sub_group1', d.SubGrpID1, 'item_sub_group2', d.SubGrpID2, function(){
                  getCustomDropdownList('item_type', d.ItemTypeID, 'item_category', d.ItemCategoryCode);
                });
              });
            });
            
            $('#item_division').val(d.DivisionID);
            $('#item_code').val(d.ItemID);
            $('#item_name').val(d.ItemName);
            $('#item_description').val(d.description);
            $('#hsn').val(d.hsn_code);
            $('#gst').val(d.tax);
            $('#uom').val(d.unit);
            $('#packing_quantity').val(d.PackingQty);
            $('#unit_weight').val(d.UnitWeight);
            $('#unit_weight_in').val(d.UnitWeightIn);
            $('#packing_weight').val(d.PackingWeight);
            $('#brand').val(d.BrandID);
            $('#priority').val(d.PriorityID);
            $('#upper_tolerence').val(d.UpperTolerence);
            $('#down_tolerence').val(d.DownTolerence);
            $('#unloading_rate').val(d.UnloadingRate);
            $('#bag_applicable').val(d.IsBagApplicable);
            $('#bom_applicable').val(d.IsBOMApplicable);
            $('#is_active').val(d.IsActive);
            $('#quality_managed').val(d.QCManage);
            $('#batch_managed').val(d.BatchManage);
            $('#batch_managed_method').val(d.BatchManageType);
            $('#self_life').val(d.ItemLife);
            $('#max_level').val(d.MaxStockLevel);
            $('#min_level').val(d.MinStockLevel);
            $('#reorder_level').val(d.ReOrderLevel);
            $('#reorder_quantity').val(d.MinOrderQty);
            $('#mrp').val(d.MRP);
            $('#prefer_vendor').val(d.PreferVendorID);
            $('#vendor_part_no').val(d.VendorPartNo);
            $('#hindi_name').val(d.HindiName);
            $('#additional_info').val(d.AdditionalInformation);
            if(d.Attachment != null){
              $('#imgLink').attr('href', '<?= base_url(); ?>'+d.Attachment).show();
            }

            $('.selectpicker').selectpicker('refresh');
            $('#ItemListModal').modal('hide');
          }else{
            alert_float('warning', response.message);
          }
        }
      });
    };
</script>
<script>
  function myFunction2() {
    var input = document.getElementById("myInput1");
    var filter = input.value.toUpperCase();
    var table = document.getElementById("table_ItemListModal");
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
		var table = $("#table_ItemListModal tbody");
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
  .bottom-action-bar {
    position: fixed; bottom: 0; left: 0; right: 0; background: #fff; padding: 10px 20px 10px 0px; margin-top: 10px; box-shadow: 0 -2px 0px rgba(0,0,0,0.1); z-index: 2; text-align: right;
  }
	#table_ItemListModal td:hover { cursor: pointer; }
	#table_ItemListModal tr:hover { background-color: #ccc; }
	.table-ItemListModal { overflow: auto; max-height: 65vh; width:100%; position:relative; top: 0px; }
	.table-ItemListModal thead th { position: sticky; top: 0; z-index: 1; }
	.table-ItemListModal tbody th { position: sticky; left: 0; }
	table { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important; font-size:11px; line-height:1.42857143!important; vertical-align: middle !important;}
	th { background: #50607b; color: #fff !important; }
</style>
