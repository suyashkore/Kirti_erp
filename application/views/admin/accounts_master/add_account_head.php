<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-9">
        <div class="panel_s">
          <div class="panel-body">
                
                  <?php echo form_open('admin/accounts_master/',array('id'=>'accounting_head')); ?>
                <div class="row">
                    <div class="col-md-3">
                        <?php $value = (isset($account_detail) ? $account_detail->AccountID : ''); ?>
                        <?php
                        $date_attrs = array();
                        $date_attrs2 = array();
                            if(isset($account_detail)){
                                
                                $date_attrs['disabled'] = true;
                                $opening_bal = get_opening_bal($value);
                                $bal_on_bill = get_bal_on_bill($value);
                                
                            }
                            if(is_admin() || has_permission_new('account_head', '', 'edit')){
                                
                            }else{
                                $date_attrs2['disabled'] = true;
                            }
                            
                        ?>
                       
                       
                        <input type="hidden" value="<?php echo $value; ?>" name="edit_account_id">
                        
                        <?php echo render_input('account_id','Account ID',$value,'',$date_attrs); ?>
                    </div>
                    <div class="col-md-3">
                        <?php $value = (isset($account_detail) ? $account_detail->company : ''); ?>
                        <?php echo render_input('account_name','Account Name',$value); ?>
                    </div>
                    
                    <div class="col-md-3">
                       <?php $value = (isset($account_detail) ? $account_detail->SubActGroupID : ''); ?>
                        <?php echo render_select('Account_Group',$account_subgroup,array('SubActGroupID','SubActGroupName'),'ActGroup Name',$value,$date_attrs2); ?>
                    </div>
                    <!--<div class="col-md-3">
                        <?php $value = 24;
                                $date_attrs1 = array();
                                $date_attrs1['disabled'] = true;
                  
                        ?>
                        <?php echo render_select('distributor_type',$distributor_type,array('id','name'),'Distributor Type',$value,$date_attrs1); ?>
                      
                    </div>-->
                    
                    <div class="col-md-3">
                        <?php
                            $staff_user_id = $this->session->userdata('staff_user_id');
                        ?>
                        <?php //echo render_input('opening_bal','Opening Bal.'); ?>
                        <label for="opening_bal" class="control-label">Opening Bal.</label>
                            <input type="text" maxlength="12"  name="opening_bal" pattern="[0-9]" id="opening_bal" class="form-control" value="<?php echo $account_detail->BAL1; ?>" <?php if(isset($account_detail) && $staff_user_id !== "3"){ echo "disabled";}?>>
                            <span class="opening_bal_denger" style="color:red;"></span>
                    </div>
                </div>
                
                
                <div class="row">
                    
                    
                    
                    <!--<div class="col-md-3">
                        <?php echo render_input('security_dep','Security Dep.'); ?>
                    </div>-->
                    <div class="col-md-3">
                        <?php $value = (isset($account_detail) ? $account_detail->Blockyn : ''); ?>
                        <div class="form-group">
						<label for="block_ac" class="control-label">Block A/C</label>
						<select class="form-control " name="block_ac" data-live-search="true" id="block_ac">
						    <option value="N" <?php if($value == "N") echo "selected";?>>No</option>
						    <option value="Y" <?php if($value == "Y") echo "selected";?>>Yes</option>
						</select>
						</div>
                    </div>
                    <div class="col-md-3">
                        <?php $value1 = $bal_on_bill->BalancesYN; 
                        
                        ?>
                        <div class="form-group">
						<label for="bal_on_bill" class="control-label">Balance on Bill</label>
						<select class="form-control " name="bal_on_bill" data-live-search="true" id="bal_on_bill">
						    
						    <option value="Y" <?php if($value1 == "Y") echo "selected";?>>Yes</option>
						    <option value="N" <?php if($value1 == "N") echo "selected";?>>No</option>
						</select>
						</div>
                    </div>
                    <div class="col-md-3">
                        <?php $value = (isset($account_detail) ? $account_detail->StartDate : date('Y-m-d')); 
                        if(isset($account_detail)){
                            $new_val = substr($value,0,10);
                        }else{
                            $new_val = $value;
                        }
                        
                        ?>
                        
                        <?php echo render_date_input('start_date','Start Date',$new_val,$date_attrs2); ?>
                    </div>
                </div>
               
                <div class="row">
                    
                    <div class="col-md-1" >
                       <br>
                    <?php
                        if(isset($account_detail) && has_permission_new('account_head', '', 'edit')){
                    ?>
                    <button type="submit" class="btn btn-info">Update</button>
                    <?php
                        }
                    if( has_permission_new('account_head', '', 'create') && !isset($account_detail)) { ?>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    <?php } ?>
                    </div>
                     <div class="col-md-2" style="margin-top: 10px;">
                       <br>
                       <?php $redUrl = admin_url('accounts_master/manage_accounts'); 
                       if(has_permission_new('account_head', '', 'view')) { ?>
                        <!--<a href="<?php echo $redUrl;?>" class="btn btn-info">Account list</a>-->
                        <?php } ?>
                    </div>
                </div>
                
                  <?php echo form_close(); ?>
            
          </div>
        </div>
        
      </div>
      
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
$(function(){
        'use strict';
        appValidateForm($('#accounting_head'), {
            
            account_id: {
				required: true,
				remote: {
					url: site_url + "admin/misc/accountID_exists",
					type: 'post',
					data: {
						account_id: function() {
							return $('input[name="account_id"]').val();
						},
					}
				}
			},
            account_name: 'required',
            Account_Group: 'required',
            
        });
        
        $('#opening_bal').keyup(function(e) {
            e.preventDefault();
            if(!$('#opening_bal').val().match('[0-9]'))  {
                
                $(".opening_bal_denger").text("Enter only digit...");
            }else{
                $(".opening_bal_denger").text(" ");
            }
        });
    });
</script>
 
</body>
</html>