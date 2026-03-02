<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
  <div class="form-group">
    <div class="checkbox checkbox-primary">
      <input onchange="purchase_order_setting(this); return false" type="checkbox" id="purchase_order_setting" name="purchase_setting[purchase_order_setting]" <?php if(get_purchase_option('purchase_order_setting') == 1 ){ echo 'checked';} ?> value="purchase_order_setting">
      <label for="purchase_order_setting"><?php echo _l('create_purchase_order_non_create_purchase_request_quotation'); ?>

      <a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('purchase_order_tooltip'); ?>"></i></a>
      </label>
    </div>
  </div>
  <div class="form-group">
    <div class="checkbox checkbox-primary">
      <input onchange="item_by_vendor(this); return false" type="checkbox" id="item_by_vendor" name="purchase_setting[item_by_vendor]" <?php if(get_purchase_option('item_by_vendor') == 1 ){ echo 'checked';} ?> value="item_by_vendor">
      <label for="item_by_vendor"><?php echo _l('load_item_by_vendor'); ?>

      </label>
    </div>
  </div>


  <?php echo form_open_multipart(admin_url('purchase/reset_data'), array('id'=>'reset_data')); ?>
  <div class="_buttons">
      <?php if (is_admin()) { ?>
          <div class="row">
              <div class="col-md-12">
                  <button type="button" class="btn btn-danger intext-btn" onclick="reset_data(this); return false;" ><?php echo _l('reset_data'); ?></button>
                  <a href="#" class="input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo _l('reset_data_title_pur'); ?>"></i></a>
              </div>
          </div>
      <?php } ?>
  </div>
  <?php echo form_close(); ?>

  <hr>
</div>

<?php echo form_open_multipart(admin_url('purchase/pur_order_setting'),array('id'=>'pur_order_setting-form')); ?>
<div class="col-md-6">
	<?php echo render_input('pur_order_prefix','pur_order_prefix',get_purchase_option('pur_order_prefix')); ?>
</div>

<div class="col-md-6">
  <?php echo render_input('pur_request_prefix','pur_request_prefix',get_purchase_option('pur_request_prefix')); ?>
</div>

<div class="col-md-6">
  <?php echo render_input('pur_inv_prefix','pur_inv_prefix',get_purchase_option('pur_inv_prefix')); ?>
</div>

<div class="col-md-6 form-group">
  <label for="create_invoice_by"><?php echo _l('create_invoice_by'); ?></label>
  <select name="create_invoice_by" id="create_invoice_by" class="selectpicker" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
      <option value="contract" <?php if(get_purchase_option('create_invoice_by') == 'contract'){ echo 'selected'; } ?> ><?php echo _l('contract'); ?></option>
      <option value="pur_order" <?php if(get_purchase_option('create_invoice_by') == 'pur_order'){ echo 'selected'; } ?> ><?php echo _l('pur_order'); ?></option>
  </select>
</div>
<div class="col-md-12">
  <hr>
</div>
	
<div class="col-md-6">
  <?php echo render_textarea('terms_and_conditions', 'terms_and_conditions', get_purchase_option('terms_and_conditions')); ?>
</div>

<div class="col-md-6">
  <?php echo render_textarea('vendor_note', 'vendor_note', get_purchase_option('vendor_note')); ?>
</div>

<?php if(get_po_logo() == ''){ ?>
  <div class="col-md-6 form-group">
    <label for="po_logo"><?php echo _l('po_logo'); ?></label>
    <input type="file" class="form-control" name="po_logo" accept="image/*" />
  </div>
<?php } else { ?>
<div class="col-md-5">
  <?php echo get_po_logo(500, "img img-responsive"); ?>
</div>
<?php if(has_permission('purchase','','delete') || is_admin()){ ?>
          <div class="col-md-6 text-left">
            <a href="<?php echo admin_url('purchase/remove_po_logo'); ?>" data-toggle="tooltip" title="<?php echo _l('remove_po_logo'); ?>" class="_delete text-danger"><i class="fa fa-remove"></i></a>
          </div>
        <?php } ?>
<?php } ?>

<div class="col-md-12">
  <hr>
</div>

	<button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
	<?php echo form_close(); ?>

<div class="clearfix"></div>


