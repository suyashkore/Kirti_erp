<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
	    <div class="row" style="display:none;">
		<div class="col-md-12">
		    <table id="print_table">
			     <thead>
			         <tr>
			             <th align="center" colspan="15"><?php echo $company_detail->company_name; ?></th>
			         </tr>
			         <tr>
			             <th align="center" colspan="15"><?php echo $company_detail->address; ?></th>
			         </tr>
			     </thead>
			 <tbody>
			         <tr>
			             <td colspan="15"><b>PurchaseRtn Details</b></td>
			         </tr>
			         <tr>
			             <td colspan="3"><b>Bill No : </b><?php echo $purchase_details->PurchRtnID ; ?></td>
			             <td colspan="3"><b>Date : </b><?php echo _d(substr($purchase_details->Transdate,0,10)) ; ?></td>
			             <td colspan="4"><b>GSTIN : </b><?php echo $purchase_details->vat; ?></td>
			             <td colspan="5"><b>Mobile No : </b><?php echo $purchase_details->phonenumber;?></td>
			         </tr>
			         <tr>
			             <td colspan="3"><b>AccountID : </b><?php echo $purchase_details->AccountID; ?></td>
			             <td colspan="3"><b>Name : </b> <?php echo $purchase_details->company; ?></td>
			             <td colspan="4"><b>Station : </b><?php echo $purchase_details->StationName; ?></td>
			             <td colspan="5"><b>State : </b><?php echo $purchase_details->state_name; ?></td>
			         </tr>
			                 
			         <tr class="print_item_h">
			             <td align="center">ItemID</td>
			             <td align="center">Item Name</td>
			             <td align="center">Pack</td>
			             <td align="center">PurchID</td>
			             <td align="center">PurchQty</td>
			             <td align="center">PurchRate</td>
			             <td align="center">RtnCases</td>
			             <td align="center">InUnits</td>
			             <td align="center">Amount</td>
			             <td align="center">Disc%</td>
			             <td align="center">DiscAmt</td>
			             <td align="center">CGST%</td>
			             <td align="center">SGST%</td>
			             <td align="center">IGST%</td>
			             <td align="center">NetAmount</td>
			         </tr>
			     <?php
                    if(isset($purchase_history)){
                        foreach ($purchase_history as $key => $value) {
                    ?>
                            <tr>
                                <td ><?php echo $value["ItemID"]; ?></td>
                                <td ><?php echo $value["description"]; ?></td>
                                <td ><?php echo $value["CaseQty"]; ?></td>
                                <td ><?php echo $value["BillID"]; ?></td>
                                <td ><?php echo $value["PurchQty"]; ?></td>
                                <td ><?php echo round($value["PurchRate"],2); ?></td>
                                <td ><?php echo $value["BilledQty"]; ?></td>
                                <td ><?php echo $value["BilledQty"] * $value["CaseQty"]; ?></td>
                                
                                <td ><?php echo $value["ChallanAmt"]; ?></td>
                                <td ><?php echo $value["DiscPerc"]; ?></td>
                                <td ><?php echo $value["DiscAmt"]; ?></td>
                                <td align="center"><?php echo $value["cgst"]; ?></td>
                                <td align="center"><?php echo $value["sgst"]; ?></td>
                                <td align="center"><?php echo $value["igst"]; ?></td>
                                <?php $Nettotal = ($value["ChallanAmt"]) + ($value["cgstamt"] + $value["sgstamt"] + $value["igstamt"]); ?>
                                <td align="right"><?php echo $Nettotal; ?></td>
                            </tr>    
                <?php
                        }
                    }
                ?>
                        <tr>
                            <td colspan="14" align="right">Gross Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->Purchamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">Disc Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->Discamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">CGST Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->cgstamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">SGST Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->sgstamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">IGST Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->igstamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">Freight Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->Frtamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">Other Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->Othamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">RoundOFF Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->RoundOffAmt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
                        <tr>
                            <td colspan="14" align="right">Net Amt</td>
                            <?php $value = (isset($purchase_details) ? $purchase_details->Invamt : '0.00'); ?>
                            <td align="right"><?php echo $value; ?></td>
                        </tr>
			</tbody>
		</table>
		</div>
	</div>
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
			
			?>
			<div class="col-md-12">
        <div class="panel_s accounting-template estimate">
    <div class="row">
        <div class="col-md-12">
        <div class="panel-body">
      
           <?php
                  $customer_custom_fields = false;
                  if(total_rows(db_prefix().'customfields',array('fieldto'=>'pur_order','active'=>1)) > 0 ){
                       $customer_custom_fields = true;
                  }
                   ?>
            <div class="tab-content">
                <?php if($customer_custom_fields) { ?>
                 <div role="tabpanel" class="tab-pane" id="custom_fields">
                    <?php $rel_id=( isset($pur_order) ? $pur_order->id : false); ?>
                    <?php echo render_custom_fields( 'pur_order',$rel_id); ?>
                 </div>
                <?php } ?>
                <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row">
                   <div class="col-md-2">
                    
                             <div class="form-group ">
                          <?php $prefix = get_purchase_option('pur_order_prefix');
                                $next_number = get_purchase_option('next_po_number');
                          $pur_order_number = (isset($pur_order) ? $pur_order->pur_order_number : $prefix.'-'.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('M-Y'));
                          
                          $number = (isset($pur_order) ? $pur_order->number : $next_number);
                          echo form_hidden('number',$number); ?> 
                          
                          <label for="pur_order_number">  PurchRtn No.</label>
                          
                             <input type="text" readonly="" name="purchRtnID" id="purchRtnID" class="form-control receiptsid" value="<?php echo html_entity_decode($purchase_details->PurchRtnID); ?>">
                          
                        </div>
                        </div>
                       
                        <div class="col-md-2 ">
                         <div class="form-group">
                            <label for="estimate"></label>
                           <input type="text" readonly="" class="form-control" name="gst_num" id="gst_num" value="<?= $purchase_details->vat?>"  aria-invalid="false">
                         </div>
                        </div>
                       <div class="col-md-2 ">
                         <div class="form-group">
                            <label for="estimate"></label>
                            <?php 
                            $actBal = $purchase_details->BAL1 + $purchase_details->BAL2 + $purchase_details->BAL3 + $purchase_details->BAL4 +$purchase_details->BAL5 +$purchase_details->BAL6 + $purchase_details->BAL7 + $purchase_details->BAL8 +$purchase_details->BAL9 + $purchase_details->BAL10 + $purchase_details->BAL11 + $purchase_details->BAL12 + $purchase_details->BAL13;
                                if($actBal > 0){
                                    $actBal_new = number_format($actBal,2)."Dr";
                                }else{
                                    $actBal_new = number_format($actBal,2)."Cr";
                                }             
                            ?>
                            <input type="text" readonly="" class="form-control" name="c_balance" id="c_balance" value="<?= $actBal_new ?>" aria-invalid="false">
                           
                          </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estimate"></label>
                                <input type="text" readonly="" class="form-control" name="station_n" id="station_n"  value="<?= $purchase_details->StationName?>" aria-invalid="false">
                            </div>  
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estimate"></label>
                                <input type="text" readonly="" class="form-control" name="city" id="city"  value="<?= $purchase_details->city?>" aria-invalid="false">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-md-2">
                          <input type="hidden" name="vendor_code" id="vendor_code" value="<?php echo $purchase_details->AccountID; ?>">
                          <input type="hidden" name="new_vendor_code" id="new_vendor_code" value="<?php echo $purchase_details->AccountID; ?>">
                          <input type="hidden" name="old_vendor_id" id="old_vendor_id" value="<?php echo $purchase_details->userid; ?>">
                              <div class="form-group">
                                <label for="vendor_code">PurchRtn To</label>
                                <!--<input type="text" readonly="" class="form-control" name="vendor" id="vendor" value="<?php echo $purchase_details->AccountID; ?>" >-->
                                <select name="vendor"  id="vendor" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                                    <option value=""></option>
                                    <?php foreach($vendors as $s) { ?>
                                    <option value="<?php echo html_entity_decode($s['userid']); ?>"  <?php if($purchase_details->AccountID == $s['AccountID']){ echo 'selected'; }?>><?php echo html_entity_decode($s['company']); ?></option>
                                    <?php } ?>
                                </select>             
                               </div>
                        </div>

                        <div class="col-md-3 ">
                            <div class="form-group">
                                <label for="estimate"></label>
                                <input type="text" readonly="" class="form-control" name="c_name" id="c_name" value="<?= $purchase_details->company?>"  aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-3 ">
                         <div class="form-group">
                            <label for="estimate"></label>
                           <input type="text" readonly="" class="form-control" name="address" id="address" value="<?= $purchase_details->address?>" aria-invalid="false">
                         </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estimate"></label>
                            <input type="text" readonly="" class="form-control" name="address2" id="address2" value="<?= $purchase_details->Address3?>"  aria-invalid="false">
                            </div>
                        </div>
                      
                    </div>
                    <div class="row">
                        
                        <div class="col-md-2">
                             <?php 
                               $order_date = (isset($purchase_details) ? _d(substr($purchase_details->Transdate,0,10)) : '');
                               $PurchID = (isset($purchase_details) ? $purchase_details->PurchRtnID : ''); ?>
                               <input type="hidden" name="old_purRtnDate" id="old_purRtnDate" value="<?php echo $order_date; ?>">
                               <input type="hidden" name="PurRtnID" id="PurRtnID" value="<?php echo $PurchID; ?>">
                            <div class="form-group" app-field-wrapper="purch_rtn_date">
                                <label for="purch_rtn_date" class="control-label">PurchRtn Date</label>
                            <div class="input-group date">
                               <input type="text"  id="purch_rtn_date" name="purch_rtn_date" class="form-control datepicker" value="<?php echo $order_date;?>" autocomplete="off">
                               <div class="input-group-addon">
                                <i class="fa fa-calendar calendar-icon"></i>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="estimate"></label>
                                <input type="text" readonly="" class="form-control" name="state_c" id="state_c" value="<?= $purchase_details->state?>" aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="estimate"></label>
                                <input type="text" readonly="" class="form-control" name="state_f" id="state_f" value="<?= $purchase_details->state_name?>" aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-1">
                          <br>
                          <span></span><a href="#" class="btn btn-warning edit-new-order">View List</a>
                        </div>
                        <div class="col-md-2" >
                            <br>
                            <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                        </div>
                      
                    </div> 
                   

              </div>
            </div>
        </div>
        </div>
        </div>
          <div class="panel-body mtop10">
        <div class="row col-md-12">
        <p class="bold p_style"><?php echo _l('pur_order_detail'); ?></p>
        <hr class="hr_style"/>
         <div class="" id="example">
         </div>
         <?php echo form_hidden('pur_order_detail'); ?>
        
         <div class="col-md-8">
              <table class="table text-left">
               <tbody>
             <tr id="discount_area">
             <td class="td_style" >
                 <?php $value = $purchase_details->FrtAccountID; ?>
                           <label style="float: left; padding: 9px;width:85px;" for="<?php echo _l('Freight A/c'); ?>"><?php echo _l('Freight A/c'); ?></label>  
                        
                            <input  type="text" readonly class=" text-right" name="Freight_1" style="border-radius: 5px;" size="7" value="<?php echo $value; ?>">
                               <!--<input  type="text" disabled="true" class=" text-right" name="Freight_2" size="11" value="">-->
                        <select size="11" name="Freight_2" id="Freight_2" class="selectpicker"  data-live-search="true"  data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value=""></option>
                        <?php foreach($accounts as $s) { ?>
                        <option value="<?php echo html_entity_decode($s['AccountID']); ?>" <?php if('209' == $s['AccountID']){ echo 'selected';} ?>><?php echo html_entity_decode($s['company']); ?></option>
                        <?php } ?>
                        </select>
                       
                         
                          
                     </td>
                     </tr>
                     <tr  id="discount_area">
                      <td class="td_style" >
                          <?php $valueOtherAct = $purchase_details->OthAccountID; ?>
                           <label style="float: left; padding: 9px;width:85px;" for="<?php echo _l('Other A/c'); ?>"><?php echo _l('Other A/c'); ?></label>  
                     
                                
                              <input  type="text" readonly class=" text-right" name="Other_ac" size="7" style="border-radius: 5px;" value="<?php echo $valueOtherAct; ?>">
                               <!--<input  type="text" disabled="true" class=" text-right" name="Other_ac1" size="11" value="">-->
                              <select size="11" name="Other_ac1" id="Other_ac1" class="selectpicker"  data-live-search="true"  data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value=""></option>
                        <?php foreach($accounts as $s) { ?>
                        <option value="<?php echo html_entity_decode($s['AccountID']); ?>" <?php if($valueOtherAct == $s['AccountID']){ echo 'selected';} ?>><?php echo html_entity_decode($s['company']); ?></option>
                        <?php } ?>
                        </select>
                       
                         
                          
                     </td>
                  </tr>
                  </tbody>
                  </table>
         </div>
         <div class="col-md-4 ">
            <table class="table text-right">
               <tbody>
                  <tr id="total_td">
                     
                     <td width="50%" id="total_td">
                      <label style="float: left; padding: 9px;width: 92px;" for="<?php echo _l('subtotal'); ?>"><?php echo _l('subtotal'); ?></label>  
                       <div class="input-group" id="discount-total">
                                
                              <input type="text" readonly class="form-control text-right" name="total_mn" value="<?= $purchase_details->Purchamt;?>">

                          </div>
                     </td>
                    
                  </tr>
                 
                  
                  <tr id="discount_area">
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Discount AMT</label>  
                          <div class="input-group" id="discount-total">
                             <input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->Discamt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="dc_total">

                             

                          </div>
                     </td>
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Freight AMT</label>  
                          <div class="input-group" id="discount-total">
                              <input type="hidden"  id="Freight_AMT_hidden">
                             <input type="text"  class="form-control pull-left text-right"  value="<?= $purchase_details->Frtamt?>"  data-type="currency" name="Freight_AMT" id="Freight_AMT">

                          </div>
                     </td>
                  </tr>
                  <tr id="discount_area">
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">CGST AMT</label>  
                          <div class="input-group" id="discount-total">
                              <input type="hidden"  id="Other_amt_hidden">
                             <input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->cgstamt; } ?>" class="form-control pull-left text-right" data-type="currency" name="CGST_amt">
                              
                          </div>
                     </td>
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Other AMT</label>  
                          <div class="input-group" id="discount-total">
                             <input type="text" value="<?= $purchase_details->Othamt; ?>"  class="form-control pull-left text-right"  data-type="currency" id="Other_amt" name="Other_amt">

                             

                          </div>
                     </td>
                  </tr>
                  <tr id="discount_area">
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">SGST AMT</label>  
                          <div class="input-group" id="discount-total">
                             <input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->sgstamt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="SGST_AMT">

                             

                          </div>
                     </td>
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Round OFF</label>  
                          <div class="input-group" id="discount-total">
                             <input type="text" readonly value="<?php if(isset($purchase_details)){ echo $purchase_details->RoundOffAmt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="Round_OFF">

                            

                          </div>
                     </td>
                  </tr>
                  <tr id="discount_area">
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">IGST AMT</label>  
                          <div class="input-group" id="discount-total">
                             <input type="text" readonly  value="<?php if(isset($purchase_details)){ echo $purchase_details->igstamt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="IGST_amt">

                          

                          </div>
                     </td>
                         <td>  
                     <label style="float: left; padding: 9px 9px 9px 0px;width: 92px;" for="<?php echo _l('subtotal'); ?>">Invoice AMT</label>  
                          <div class="input-group" id="discount-total">
                             <input type="text" value="<?php if(isset($purchase_details)){ echo $purchase_details->Invamt; } ?>" class="form-control pull-left text-right" onchange="dc_total_change(this); return false;" data-type="currency" name="Invoice_amt">
                        <?php $data_total =  json_decode($pur_order_detail);
                      //  print_r($data_total);?>
                           <input type="hidden" id="purchase_id_store" name="purchase_id_store" >
                        <input type="hidden" id="purchase_id_check" name = "purchase_id_check" value="<?php echo $data_total[0]->BillID; ?>"> 

                          </div>
                     </td>
                  </tr>
                 

               </tbody>
            </table>
         </div> 
         
        </div>
        </div>
        <div class="row">
          <div class="col-md-12 mtop15">
            <div class="btn-bottom-toolbar text-right" style="width: 100%;">
                <?php if (has_permission_new('purchase-return', '', 'edit')){
                $selected_company = $this->session->userdata('root_company');
                $fy = $this->session->userdata('finacial_year');
                $fy_new  = $fy + 1;
                $first_date = '20'.$fy.'-04-01';
                $lastdate_date = '20'.$fy_new.'-03-31';
                $curr_date = date('Y-m-d');
                $lgstaff = $this->session->userdata('staff_user_id');
                $Purch_date = substr($purchase_details->Transdate,0,10);
                
                $Purch_date_new    = new DateTime($Purch_date);
                $first_date_yr = new DateTime($first_date);
                $last_date_yr = new DateTime($lastdate_date);
                $curr_date_new = new DateTime($curr_date);
                
                /*$sql = 'SELECT * FROM tblpurchasereturn WHERE PlantID = '.$selected_company.' AND FY LIKE "'.$fy.'" ORDER BY tblpurchasereturn.PurchRtnID DESC ';
                $result_data = $this->db->query($sql)->row();
                $lastdate = substr($result_data->Transdate,0,10);*/
                
                if($curr_date_new > $last_date_yr){
                    $lastdate = $lastdate_date;
                }else{
                    $lastdate = date('Y-m-d');
                }
                
                $this->db->select('*');
                $this->db->where('plant_id', $selected_company);
                $this->db->where('year', $fy);
                $this->db->where('staff_id', $lgstaff);
                $this->db->LIKE('feature', "purchase-return");
                $this->db->LIKE('capability', "view");
                $this->db->from(db_prefix() . 'staff_permissions');
                $result2 = $this->db->get()->row();
                $day = $result2->days;
                
                if($day == 0){
                            $return = '';
                        }else{
                            
                            $days = '- '.$day.' days';
                            $tillDate = date('Y-m-d', strtotime($lastdate. $days));
                            $tillDate_new = new DateTime($tillDate);
                            
                            if ($Purch_date_new < $tillDate_new) {
                                $return = 'disabled';
                            }else{
                                $return = '';
                            }
                        }
            ?>
            <?php if($return == "disabled"){
            ?>
            <a href="#" class="btn btn-info <?php echo $return;?>">Update</a>
            <?php
            }else{
            ?>
            <button type="button"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
                 Update
                  </button>
            <?php
            }?>
                  
                  <?php
                  }?>
                </div>
             <!--<div class="panel-body bottom-transaction">
                
                <div id="vendor_data">
                  
                </div>

                
             </div>-->
               <div class="btn-bottom-pusher"></div>
          </div>
        </div>
        </div>

			</div>
			<?php echo form_close(); ?>
			
		</div>
	</div>
</div>
</div>
<input type="hidden" id="purchase_id_store">
<input type="hidden" id="purchase_id_check">
<div class="modal fade" id="retuen_pur_id_modal">
   <div class="modal-dialog modal-xl" style=" max-width: 1530px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Purchase List</h4>
         </div>
         
         
         <div class="modal-body" style="">
            
            <div class="row">
               
                 
            <div class="table_purchase_report">
             
              <table class="tree table table-striped table-bordered table_purchase_report" id="table_purchase_report" width="100%">
                  
                <thead>
                    
                  <tr>
                    <th style="width:1% ">FY</th>
                    <th style="width:7% ">PurchID</th>
                    <th style="width:5% ">ReceiptDate</th>
                     <th style="width:5% text-align:left;">InvoceNo</th>
                    <!--<th style="width:15% text-align:left;">PurchasedForm</th>-->
                    <th style="width:5% text-align:left;">InvoceDate</th>
                    <th style="width:5% text-align:left;">PurchQty</th>
                    <th style="width:3% text-align:left;">CaseQty</th>
                    <th style="width:5% text-align:left;">Cases</th>
                    <th style="width:5% text-align:left;">PurchRate</th>
                    <th style="width:5% text-align:left;">GST%</th>
                    <th style="width:5% text-align:left;">CGST%</th>
                    <th style="width:5% text-align:left;">SGST%</th>
                    <th style="width:5% text-align:left;">IGST%</th>
                    <th style="width:5% text-align:left;">Amount</th>
                    <th style="width:5% text-align:left;">RtnQty</th>
                    
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
                    
                </div>
              </div>
            
              
         </div>
        
      </div>
   </div>
</div>
<div class="modal fade" id="transfer-modal">
   <div class="modal-dialog modal-xl" style=" max-width: 1230px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Purchase Return List</h4>
         </div>
         
         
         <div class="modal-body" style="padding:5px;">
            
            <div class="row">
               <?php
                    $fy = $this->session->userdata('finacial_year');
                    $fy_new  = $fy + 1;
                    $lastdate_date = '20'.$fy_new.'-03-31';
                    $firstdate_date = '20'.$fy_new.'-04-01';
                    $curr_date = date('Y-m-d');
                    $curr_date_new    = new DateTime($curr_date);
                    $last_date_yr = new DateTime($lastdate_date);
                    if($last_date_yr < $curr_date_new){
                        $to_date = '31/03/20'.$fy_new;
                        $from_date = '01/03/20'.$fy_new;
                    }else{
                        $from_date = "01/".date('m')."/".date('Y');
                        $to_date = date('d/m/Y');
                    }
            ?>  
                <div class="col-md-3">
                    <?php
                   echo render_date_input('from_date','From',$from_date);
                   ?>
                </div>
                <div class="col-md-3">
                    <?php
                   echo render_date_input('to_date','To',$to_date);
                   ?>
                </div>
                <div class="col-md-3">
                    <br>
                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
                </div>
                <div class="col-md-3">
                    <br>
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
                </div>
                <div class="col-md-12">
                 
            <div class="table_purchase_report1">
             
              <table class="tree table table-striped table-bordered table_purchase_report1" id="table_purchase_report1" width="100%">
                  
                <thead>
                    <tr style="display:none;">
                      <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>
                  <tr>
                    <th style="width:1% ">BT</th>
                    <th style="width:7% ">PurchRtnID</th>
                    <th style="width:5% ">TransDate</th>
                    <th style="width:15% text-align:left;">Return To</th>
                    <th style="width:5% text-align:left;">ReturnAmt</th>
                    <th style="width:3% text-align:left;">DscAmt</th>
                    <!--<th style="width:5% text-align:left;">CGSTAmt</th>-->
                    <!--<th style="width:5% text-align:left;">SGSTAmt</th>-->
                    <th style="width:5% text-align:left;">IGSTAmt</th>
                    <th style="width:5% text-align:left;">ExcAmt</th>
                    <th style="width:5% text-align:left;">vatAmt</th>
                    <th style="width:5% text-align:left;">FrtAmt</th>
                    <th style="width:5% text-align:left;">OthAmt</th>
                    <th style="width:5% text-align:left;">NetReturn Amt</th>
                    
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
                    
                </div>
              </div>
            
              
         </div>
        
      </div>
   </div>
</div>



<?php init_tail(); ?>

</body>
<style>
    .table_purchase_report1 { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table_purchase_report1 thead th { position: sticky; top: 0; z-index: 1; }
.table_purchase_report1 tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.table_purchase_report1 table  { border-collapse: collapse; width: 100%; }
.table_purchase_report1 th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
.table_purchase_report1 th     { background: #50607b;color: #fff !important; }


#table_purchase_report tr:hover {
    background-color: #ccc;
}

#table_purchase_report td:hover {
    cursor: pointer;
}
#table_purchase_report1 tr:hover {
    background-color: #ccc;
}

#table_purchase_report1 td:hover {
    cursor: pointer;
}
</style>
<script type="text/javascript">
 function printPage(){
      
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .print_item_h{ background: #505f7b;colr:#fff;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
    var print_data = stylesheet+tableData
   newWin= window.open("");
   newWin.document.write(print_data);
   newWin.print();
   newWin.close();
    };
 </script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  function load_data(from_date,to_date)
  {
    $.ajax({
      url:"<?php echo admin_url(); ?>purchase/load_data_for_purchaseRtn",
      dataType:"JSON",
      method:"POST",
      data:{from_date:from_date, to_date:to_date},
      beforeSend: function () {
               
        $('#searchh2').css('display','block');
        $('.table_purchase_report1 tbody').css('display','none');
        
     },
      complete: function () {
                            
        $('.table_purchase_report1 tbody').css('display','');
        $('#searchh2').css('display','none');
     },
      success:function(data){
        var html = '';
      
        for(var count = 0; count < data.length; count++)
        {
           
          var url = "'<?php echo admin_url() ?>purchase/purchaseRtn_list/"+data[count].PurchRtnID+"'";
        html += '<tr onclick="location.href='+url+'">';
        html += '<td style="text-align:center;">'+data[count].BT+'</td>';
        html += '<td style="text-align:center;">'+data[count].PurchRtnID+'</td>';
        var date = data[count].Transdate.substring(0, 10)
        var date_new = date.split("-").reverse().join("/");
          
          html += '<td  style="text-align:center;">'+date_new+'</td>';
          html += '<td >'+data[count].AccountName+'</td>';
          html += '<td style="text-align:right;">'+data[count].Purchamt+'</td>';
          html += '<td >'+data[count].Discamt+'</td>';
        //   html += '<td >'+data[count].cgstamt+'</td>';
        //   html += '<td >'+data[count].sgstamt+'</td>';
          html += '<td >'+data[count].igstamt+'</td>';
          html += '<td ></td>';
          html += '<td ></td>';
          html += '<td >'+data[count].Frtamt+'</td>';
          html += '<td >'+data[count].Othamt+'</td>';
          html += '<td >'+data[count].Invamt+'</td>';
          html += '</tr>';
        }
         $('.table_purchase_report1 tbody').html(html);
      
      }
    });
  }
  
 $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "Sales Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
        load_data(from_date,to_date);
        
 });

});
</script>

<script>
    function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_contra_report");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[3];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
<script>
    $('.add-new-transfer').on('click', function(){
    $('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
      $('#transfer-modal').modal('show');
      init_journal_entry_table();
    });
</script>

<script>
    $(document).ready(function(){
    var maxEndDate = new Date('Y/m/d');
    var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
    
    var year = "20"+fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);
    if(cur_y > fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        
        var e_dat = new Date(year2_new+'/03/31');
        var maxEndDate_new = e_dat;
    }else{
         var maxEndDate_new = maxEndDate;
    }
    
    var minStartDate = new Date(year, 03);
   
    $('#purch_rtn_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    });
</script> 
<script>
    $(document).ready(function(){
    var maxEndDate = new Date('Y/m/d');
    var fin_y = "<?php echo $this->session->userdata('finacial_year')?>";
    
    var year = "20"+fin_y;
    var cur_y = new Date().getFullYear().toString().substr(-2);
    if(cur_y => fin_y){
        var year2 = parseInt(fin_y) + parseInt(1);
        var year2_new = "20"+year2;
        
        var e_dat = new Date(year2_new+'/03/31');
        
        var maxEndDate_new = e_dat;
    }else{
        var e_dat2 = new Date(year2+'/03/31');
        var maxEndDate_new = e_dat2;
    }
    
    var minStartDate = new Date(year, 03);
   
    
    $('#from_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    $('#to_date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false,
        showOtherMonths: false,
        pickTime: false,
            orientation: "left",
    });
    
    });
</script>
</html>
<?php require 'modules/purchase/assets/js/pur_order_return_js.php';?>


 