<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!--<h4 class="customer-profile-group-heading"><?php echo _l('vendor_add_edit_profile'); ?></h4>-->
<style>
    #pan, #vat {
    text-transform: uppercase;
}
</style>
<div class="row">
    
  
   <?php echo form_open($this->uri->uri_string(),array('class'=>'vendor-form','autocomplete'=>'off')); ?>
   <?php 
   echo form_hidden('userid',( isset($client) ? $client->AccountID : '') ); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      
      <div class="tab-content">
        
        <div class="row">
            <div class="col-md-3">
                <?php
                if(isset($client)){
                    $attr_actID = array('disabled'=>true);
                }else{
                    $attr_actID = array('autofocus'=>true);
                }
                    
                ?>
                <?php $vendor_code = ( isset($client) ? $client->AccountID : '');
                echo render_input('vendor_code','AccountID',$vendor_code,'text',$attr_actID); ?>
            </div>
            <div class="col-md-3">
                <?php $value=( isset($client) ? $client->company : ''); ?>
                <?php //$attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                <?php echo render_input( 'company', 'Account Name',$value,'text'); ?>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="Blockyn">GST Type</label>
                    <?php $value=( isset($client) ? $client->gsttype : ''); ?>
                    <select name="gst_type" id="gst_type" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                        <option value="1" <?php if($value == "1"){ echo "selected"; }?>>Registered</option>
                        <option value="2" <?php if($value == "2"){ echo "selected"; }?>>Un-registered</option>
                        <option value="2" <?php if($value == "3"){ echo "selected"; }?>>Composition</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <!--<div class="form-group">
                    <small class="req text-danger">* </small>
                    <label for="">Account Group</label>
                    <input type="text" name="account_group" id="account_group" value="SUNDRY CREDITORS" class="form-control" disabled="disabled">
                </div>-->
                <?php //print_r($state);
                    $selected =( isset($client) ? $client->SubActGroupID : '');
                    echo render_select( 'account_group',$SubGroup,array( 'SubActGroupID',array( 'SubActGroupName')), 'Account Group',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                ?>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <?php //print_r($state);
                    $selected =( isset($client) ? $client->state : '');
                    echo render_select( 'state',$state,array( 'short_name',array( 'state_name')), 'client_state',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                ?>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    <?php $value=( isset($client) ? $client->city : ''); ?>
                    <?php
                    $city_name = get_city_name_by_state_id($client->state);
                    ?>
                    <label for="city" class="control-label">City/District</label>
                    <select class="form-control city" name="city" id="city" >
                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                        <?php foreach($city_name as $cn){ ?>
                        <option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$client->city){ echo 'selected'; }?>><?php echo $cn["city_name"]; ?></option>
                      <?php } ?>                
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <?php $value=( isset($client) ? $client->address : ''); ?>
                <?php echo render_input( 'address', 'Address',$value); ?>
            </div>
            <div class="col-md-3">
                <?php $value=( isset($client) ? $client->Address3 : ''); ?>
                <?php echo render_input( 'address2', 'Address2',$value); ?>
            </div>
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <?php $value=( isset($client) ? $client->zip : ''); ?>
                <?php //echo render_input( 'zip', 'client_postal_code',$value); ?>
                <div class="form-group" app-field-wrapper="zip">
                    <label for="zip" class="control-label">Pin Code</label>
                    <input type="text"  name="zip" class="form-control" onchange="validateZipCode" value="<?= $value; ?>" maxlength="6" minlength="6" onkeypress="return isNumber(event)">
                </div>
            </div>
            
            <div class="col-md-3">
                
                <?php $value=( isset($client) ? $client->phone_number : ''); ?>
                <?php //echo render_input( 'Mobile_number', 'Mobile number',$value); ?>
                <div class="form-group" app-field-wrapper="Mobile_number">
                    <label for="Mobile_number" class="control-label">Mobile Number</label>
                    <input type="text" id="Mobile_number-num" name="Mobile_number" class="form-control" value="<?= $value;?>" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
                </div>
            </div>
            <div class="col-md-3">
                <?php $value=( isset($client) ? $client->altphonenumber : ''); ?>
                <?php //echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>
                <div class="form-group" app-field-wrapper="phonenumber">
                    <label for="phonenumber" class="control-label">Alternative Mobile</label>
                    <input type="text" id="phonenumber" name="phonenumber" class="form-control" value="<?= $value;?>" maxlength="10" minlength="10" onkeypress="return isNumber(event)">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Email Id</label>
                    <input type="email" name="email" id="email" value="<?php echo html_entity_decode($client->email); ?>" class="form-control">
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <?php $value = ( isset($client) ? $client->vat : ''); ?>
                <div class="form-group" app-field-wrapper="vat">
                    <label for="vat" class="control-label">GST Number</label>
                    <input type="text" id="vat" name="vat" class="form-control" 
                    pattern="([0-9]){2}([A-Za-z]){5}([0-9]){4}([A-Za-z]){1}([0-9]{1})([0-9A-Za-z]){2}" maxlength="15" minlength="15" value="<?= $value; ?>">
                    <span class="gst_denger" style="color:red;"></span>
                </div>
                
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">PAN</label>
                    <input type="text" name="pan" maxlength="10" minlength="10" pattern="[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}" id="pan" value="<?php echo html_entity_decode($client->Pan); ?>" class="form-control">
                    <span class="pan_denger" style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group" app-field-wrapper="adhaar">
                    <label for="adhaar">AADHAAR</label>
                    <input type="text" name="adhaar" maxlength="12" minlength="12" pattern="[0-9] {10}" id="adhaar" onkeypress="return isNumber(event)" value="<?php echo html_entity_decode($client->Aadhaarno); ?>" class="form-control">
                    <span class="aadhar_denger" style="color:red;"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Food Lic No</label>
                    <input type="text" name="food_lic_n" id="food_lic_n" maxlength="14" minlength="14" onkeypress="return isNumber(event)" value="<?php echo html_entity_decode($client->FLNO1); ?>" class="form-control">
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <?php
                    if(isset( $client) && !is_admin()){
                        $ss = "disabled";
                    } ?>
                    <?php $staff_user_id = $this->session->userdata('staff_user_id'); ?>
                <div class="form-group">
                    <label for="">Opening Balance</label>
                    <input type="text" name="opening_b" id="opening_b" value="<?php echo html_entity_decode($client->BAL1); ?>" class="form-control" <?php if(isset($client) && $staff_user_id !== "3"){ echo "disabled";}?>>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?php $value=( isset($client) ? $client->active : ''); ?>
                  <label for="active">status</label>
                  <select name="active" id="active" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                        <option value="1" <?php if($value == "1"){ echo "selected"; }?>>Yes</option>
                        <option value="0" <?php if($value == "0"){ echo "selected"; }?>>No</option>
                  </select>
                </div>
            </div>
            <div class="col-md-3">
                <?php
                    if(isset( $client) && is_admin()){
                        
                    }else{
                        $attr_date = array('disabled'=>true);
                    }
                    
                ?>
                <?php $value= (isset($client) ? _d(substr($client->StartDate,0,10)) : _d(date('Y-m-d'))); ?>
                <?php echo render_date_input( 'Satrt_date', 'Start Date',$value,'text',$attr_date); ?>
               <!-- <input type="hidden" name="Satrt_date" value="<?php echo $value; ?>">-->
            </div>
            
            
            
            
        </div>
         <!--<div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">-->
           
            
            <!--Billing and Shipping address details-->
            
            
            <!--<div class="row">
                <div class="col-md-6">
                    <h4 class="no-mtop"><?php echo _l('billing_address'); ?> <a href="#" class="pull-right billing-same-as-customer"><small class="font-medium-xs"><?php echo _l('customer_billing_same_as_profile'); ?></small></a></h4>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <?php $selected=( isset($client) ? $client->billing_country : '' ); 
                            $selected =( isset($client) ? $client->billing_country : $customer_default_country);?>
                            <?php echo render_select( 'billing_country',$countries,array( 'country_id',array( 'short_name')), 'billing_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                        
                        </div>
                        <div class="col-md-6">
                            <?php $value=( isset($client) ? $client->billing_state : ''); ?>
                            <?php $selected =( isset($client) ? $client->billing_state : '');
                            echo render_select( 'billing_state',$state,array( 'short_name',array( 'state_name')), 'client_state',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                             ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php $value=( isset($client) ? $client->billing_city : ''); ?>
                                <?php
                                $city_name = get_city_name_by_state_id($client->billing_state);
                                ?>
                                <label for="city" class="control-label">City</label>
                                            
                                <select class="form-control " name="billing_city" id="billing_city" >
                                    <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                    <?php foreach($city_name as $cn){ ?>
                                    <option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$client->billing_city){ echo 'selected'; }?>><?php echo $cn["city_name"]; ?></option>
                                  <?php } ?>                
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php $value=( isset($client) ? $client->billing_zip : ''); ?>
                            <?php echo render_input( 'billing_zip', 'billing_zip',$value); ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php $value=( isset($client) ? $client->billing_street : ''); ?>
                            <?php echo render_textarea( 'billing_street', 'billing_street',$value); ?>
                        </div>
                    </div>
                       
                </div>
                <div class="col-md-6">
                        <h4 class="no-mtop">
                           <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('customer_shipping_address_notice'); ?>"></i>
                           <?php echo _l('shipping_address'); ?> <a href="#" class="pull-right customer-copy-billing-address"><small class="font-medium-xs"><?php echo _l('customer_billing_copy'); ?></small></a>
                        </h4>
                        <hr />
                    
                     <div class="row">
                        <div class="col-md-6">
                            <?php $selected=( isset($client) ? $client->shipping_country : '' ); 
                            $selected =( isset($client) ? $client->shipping_country : $customer_default_country);?>
                            <?php echo render_select( 'shipping_country',$countries,array( 'country_id',array( 'short_name')), 'shipping_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                            
                        </div>
                        <div class="col-md-6">
                            <?php $value=( isset($client) ? $client->shipping_state : ''); ?>
                            <?php  $selected =( isset($client) ? $client->shipping_state : '');
                             echo render_select( 'shipping_state',$state,array( 'short_name',array( 'state_name')), 'client_state',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                             ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php $value=( isset($client) ? $client->shipping_city : ''); ?>
                                <?php
                                $city_name = get_city_name_by_state_id($client->shipping_state);
                                ?>
                                <label for="city" class="control-label"> City</label>
                                            
                                <select class="form-control " name="shipping_city" id="shipping_city" >
                                    <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                    <?php foreach($city_name as $cn){ ?>
                                    <option value="<?php echo $cn['id'];?>" <?php if($cn['id']==$client->shipping_city){ echo 'selected'; }?>><?php echo $cn["city_name"]; ?></option>
                                  <?php } ?>                
                                </select> 
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php $value=( isset($client) ? $client->shipping_zip : ''); ?>
                            <?php echo render_input( 'shipping_zip', 'shipping_zip',$value); ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php $value=( isset($client) ? $client->shipping_street : ''); ?>
                            <?php echo render_textarea( 'shipping_street', 'shipping_street',$value); ?>
                        </div>
                    </div>
                   
                </div>
                     <?php if(isset($client) &&
                        (total_rows(db_prefix().'invoices',array('clientid'=>$client->AccountID)) > 0 || total_rows(db_prefix().'estimates',array('clientid'=>$client->AccountID)) > 0 || total_rows(db_prefix().'creditnotes',array('clientid'=>$client->AccountID)) > 0)){ ?>
                     <div class="col-md-12">
                        <div class="alert alert-warning">
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_all_other_transactions" id="update_all_other_transactions">
                              <label for="update_all_other_transactions">
                              <?php echo _l('customer_update_address_info_on_invoices'); ?><br />
                              </label>
                           </div>
                           <b><?php echo _l('customer_update_address_info_on_invoices_help'); ?></b>
                           <div class="checkbox checkbox-default">
                              <input type="checkbox" name="update_credit_notes" id="update_credit_notes">
                              <label for="update_credit_notes">
                              <?php echo _l('customer_profile_update_credit_notes'); ?><br />
                              </label>
                           </div>
                        </div>
                     </div>
                     <?php } ?>
                  </div>-->
              
         <!--</div>-->
         <?php if(isset($client)){ ?>
         <!--<div role="tabpanel" class="tab-pane" id="vendor_admins">
            <?php if (has_permission('purchase', '', 'create') || has_permission('purchase', '', 'edit')) { ?>
            <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_admin'); ?></a>
            <?php } ?>
            <table class="table dt-table">
               <thead>
                  <tr>
                     <th><?php echo _l('staff_member'); ?></th>
                     <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                     <?php if(has_permission('purchase','','create') || has_permission('purchase','','edit')){ ?>
                     <th><?php echo _l('options'); ?></th>
                     <?php } ?>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($customer_admins as $c_admin){ ?>
                  <tr>
                     <td><a href="<?php echo admin_url('profile/'.$c_admin['staff_id']); ?>">
                        <?php echo staff_profile_image($c_admin['staff_id'], array(
                           'staff-profile-image-small',
                           'mright5'
                           ));
                           echo get_staff_full_name($c_admin['staff_id']); ?></a>
                     </td>
                     <td data-order="<?php echo html_entity_decode($c_admin['date_assigned']); ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                     <?php if(has_permission('customers','','create') || has_permission('customers','','edit')){ ?>
                     <td>
                        <a href="<?php echo admin_url('purchase/delete_vendor_admin/'.$client->AccountID.'/'.$c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                     <?php } ?>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>-->
         <?php } ?>
         <!--<div role="tabpanel" class="tab-pane" id="billing_and_shipping">
            
         </div>-->
      </div>
   </div>
   <?php echo form_close(); ?>
</div>
<?php if(isset($client)){ ?>
<?php if (has_permission('customers', '', 'create') || has_permission('customers', '', 'edit')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('purchase/assign_vendor_admins/'.$client->AccountID)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
               $selected = array();
               foreach($customer_admins as $c_admin){
                  array_push($selected,$c_admin['staff_id']);
               }
               echo render_select('customer_admins[]',$staff,array('staffid',array('firstname','lastname')),'',$selected,array('multiple'=>true),array(),'','',false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>
<?php } ?>
  