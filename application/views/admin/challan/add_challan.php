<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
		    
		    <?php
    $state_detail = get_state_detail($order->client->state);
    
		   
	//$inv_item = get_item_by_order_id($order->OrderID,$order->PlantID,$order->FY);
	
   ?>
		    
        <div class="panel_s invoice accounting-template">
            <div class="panel-body">
                
            <?php
                echo form_open($this->uri->uri_string(),array('id'=>'challan_form','class'=>'_transaction_form invoice-form'));
            ?>
            
            <div class="col-md-4">
			            <div class="row">
			                <div class="col-md-6">
			                    <?php
			    
			   $selected_company = $this->session->userdata('root_company');
            if($selected_company == 1){
                
                $next_challan_number = get_option('next_challan_number_for_cspl');
            }elseif($selected_company == 2){
                $next_challan_number = get_option('next_challan_number_for_cff');
            }elseif($selected_company == 3){
                $next_challan_number = get_option('next_challan_number_for_cbu');
            }
            
               
               $format = get_option('invoice_number_format');

               if(isset($invoice)){
                  $format = $invoice->number_format;
               }

              
                $prefix = "CHL";
               if ($format == 1) {
                 $__number = $next_challan_number;
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = '<span id="prefix">' . $invoice->prefix . '</span>';
                 }
               } else if($format == 2) {
                 if(isset($invoice)){
                   $__number = $invoice->number;
                   $prefix = $invoice->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_year">' .date('Y',strtotime($invoice->date)).'</span>/';
                 } else {
                  $__number = $next_challan_number;
                  $prefix = $prefix.'<span id="prefix_year">'.date('Y').'</span>/';
                }
               } else if($format == 3) {
                  if(isset($invoice)){
                   $yy = date('y',strtotime($invoice->date));
                   $__number = $invoice->number;
                   $prefix = '<span id="prefix">'. $invoice->prefix . '</span>';
                 } else {
                  $yy = date('y');
                  $__number = $next_challan_number;
                }
               } else if($format == 4) {
                  if(isset($invoice)){
                   $yyyy = date('Y',strtotime($invoice->date));
                   $mm = date('m',strtotime($invoice->date));
                   $__number = $invoice->number;
                   $prefix = '<span id="prefix">'. $invoice->prefix . '</span>';
                 } else {
                  $yyyy = date('Y');
                  $mm = date('m');
                  $__number = $next_challan_number;
                }
               }

               $_is_draft = (isset($invoice) && $invoice->status == Invoices_model::STATUS_DRAFT) ? true : false;
               $_challan_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
               $isedit = isset($invoice) ? 'true' : 'false';
               $data_original_number = isset($invoice) ? $invoice->number : 'false';

               ?>
               
                        <div class="form-group">
                           <label for="number">
                              Challan Number
                             <!-- <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('invoice_number_not_applied_on_draft') ?>" data-placement="top"></i>-->
                        </label>
                           <div class="input-group">
                              <span class="input-group-addon">
                              <?php
                                echo $prefix;
                              ?>
                              </span>
                              <input type="text" name="number1" id="number1" class="form-control number1" value="<?php echo ($_is_draft) ? 'DRAFT' : $_challan_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>
                              <?php if($format == 3) { ?>
                              <span class="input-group-addon">
                                 <span id="prefix_year" class="format-n-yy"><?php echo $yy; ?></span>
                              </span>
                              <?php } else if($format == 4) { ?>
                               <span class="input-group-addon">
                                 <span id="prefix_month" class="format-mm-yyyy"><?php echo $mm; ?></span>
                                 /
                                 <span id="prefix_year" class="format-mm-yyyy"><?php echo $yyyy; ?></span>
                              </span>
                              <?php } ?>
                           </div>
                        </div>
			                   
			                </div>
			                <div class="col-md-6">
			                    <div class="form-group">
                                <input type="hidden" name="number" class="form-control" value="<?php echo ($_is_draft) ? 'DRAFT' : $_challan_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>
                                  
                                    <?php 
                                    
                                    $value = _d(date('Y-m-d'));
                                    echo render_date_input('date','Date',$value,$date_attrs); ?>
                                    
                                </div>
			                </div>
			                <div class="col-md-12">
			                    
                                <div class="form-group">
                                    <label for="challan_route" class="control-label"><small class="req text-danger">* </small> Route</label>
                                    <select class="selectpicker" name="challan_route" id="challan_route" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php
                                        foreach ($routes as $key => $value) {
                                            # code...
                                            ?>
                                            <option value="<?php echo $value["RouteID"]?>" <?php if(isset($challan) && $challan->route == $value['RouteID']){echo 'selected';} ?>><?php echo $value["name"]?></option>
                                            <?php
                                        }
                                    ?>
                                    </select>
                                </div>
			                </div>
			                <div class="col-md-6">
			                
                                <div class="form-group">
                                    <label for="challan_vehicle" class="control-label"><small class="req text-danger">* </small> Vehicle</label>
                                    <select class="selectpicker" name="challan_vehicle" id="challan_vehicle" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <option value="TV">Transport Vehicle</option>
                                    <?php
                                        foreach ($vehicle as $key => $value) {
                                            # code...
                                            ?>
                                            <option value="<?php echo $value["VehicleID"]?>" <?php if(isset($challan) && $challan->vehicle == $value['VehicleID']){echo 'selected';} ?>><?php echo $value["VehicleID"]?></option>
                                            <?php
                                        }
                                    ?>
                                    </select>
                                </div>
			                </div>
			                <div class="col-md-6" id="custom_vehicle_number">
			                    <div class="form-group">
                                    <label for="number"><small class="req text-danger">* </small> Vehicle No.</label>
                                    <?php $value = (isset($challan) ? $challan->vahicle_capacity : ''); ?>
                                    <input type="text" class="form-control" name="vahicle_number" id="vahicle_number" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                    
                                    
                                </div>
			                </div>
			                <div class="col-md-6" id="capacity_div">
			                    <div class="form-group">
                                    <label for="number">Capacity</label>
                                    <input type="text" class="form-control" name="vahicle_capacity" id="vahicle_capacity" disabled>
                                   <!-- <input type="hidden" name="vahicle_capacity" id="vahicle_capacity" value="">-->
                                </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-md-4">
			            <div class="row">
			                <div class="col-md-12">
			                    <div class="form-group" app-field-wrapper="challan_driver">
                                    <label for="challan_driver" class="control-label"> <small class="chldr text-danger">* </small>Driver</label>
                                    <?php $value = (isset($challan) ? $challan->challan_driver : ''); ?>
                                    <input type="text" class="form-control" name="challan_driver" id="challan_driver" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Loader</label>
                                    <input type="text" class="form-control" name="challan_loader" id="challan_loader" style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Sales Man</label>
                                    <input type="text" class="form-control" name="challan_sales_man" id="challan_sales_man" style="text-transform:uppercase">
                                </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-md-4">
			            <div class="row">
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Challan Value</label>
                                    <input type="text" class="form-control" name="txtchalanvalue1" id="txtchalanvalue1" value="<?php echo $order->OrderAmt; ?>" disabled >
                                    <input type="hidden" name="txtchalanvalue" id="txtchalanvalue" value="<?php echo $order->OrderAmt; ?>">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Total Cases</label>
                                    <input type="text" class="form-control" name="txtCases" id="txtCases" value="<?php echo $order->Cases; ?>" disabled>
                                    <input type="hidden" name="txtCases" id="txtCases" value="<?php echo $order->Cases; ?>">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Total Crates</label>
                                    <input type="text" class="form-control" name="txtCrates" id="txtCrates" value="<?php echo $order->Crates; ?>" disabled>
                                    <input type="hidden" name="txtCrates" id="txtCrates" value="<?php echo $order->Crates; ?>">
                                </div>
                                <input type="hidden" name="order_id[]" id="order_id" value="<?php echo $order->OrderID; ?>">
			                </div>
			            </div>
			        </div>
			        <div class="col-md-12">
			        <div class="row">
                        <div class="col-md-12 mtop15">
			            
			                <div class="btn-bottom-toolbar text-right">
			                    <button type="submit" class="btn-tr btn btn-info invoice-form-submit transaction-submit"><?php echo _l('submit'); ?></button>
			                </div>
			              
                        </div>
                    </div>
                </div>
            <?php echo form_close(); ?>
                <hr class="hr-panel-heading2" />
                <div class="col-md-12">
            <h4 style="text-align: center;">Order Detail</h4>
            </div>
                
                <div class="col-md-12">
               <table style="width: 100%;" id="order_table" border="1" cellspacing="0" cellpadding="2">
                   <tr>
                       <td width="20%">Order No. </td>
                       <td width="30%"><?php echo $order->OrderID; ?></td>
                       <td width="20%">Order Date :</td>
                       <td width="30%"> : <?php echo $order->date; ?></td>
                   </tr>
                   <tr>
                      <td>State </td>
                       <td> : <?php echo $state_detail->state_name; ?></td>
                       <td>State Code </td>
                       <td> : <?php echo $state_detail->short_name; ?></td>
                   </tr>
                   <tr>
                      <td>Date of Supply </td>
                       <td>: <?php echo $order->date; ?></td>
                       <td>Place of Supply </td>
                       <td>: <?php echo $order->client->shipping_street." ".$order->client->shipping_city; ?></td>
                   </tr>
                   <tr style="background: #415164;">
                       <td  colspan="2" style="text-align:center;color:#fff;border-color:#333;">Detail of Receiver / Billed to</td>
                       <td  colspan="2" style="text-align:center;color:#fff;border-color:#333;">Details of Consignee/Shipped to</td>
                   </tr>
                   <tr>
                   <td >Name </td>
                   <td >: <?php echo $order->client->company; ?></td>
                   <td >Name </td>
                   <td >: <?php echo $order->client->company; ?></td>
                   </tr>
                   <tr>
                   <td >Mob </td>
                   <td>: <?php echo $order->client->altnumber; ?></td>
                   <td >Mob </td>
                   <td >: <?php echo $order->client->phonenumber; ?></td>
                   </tr>
                   <tr>
                   <td >Address </td>
                   <td >: <?php echo $order->client->billing_street." ".$order->client->billing_city." ".$order->client->billing_zip; ?> </td>
                   <td >Address </td>
                   <td >: <?php echo $order->client->shipping_street." ".$order->client->shipping_city." ".$order->client->shipping_zip; ?></td>
                   </tr>
                   
                   <tr>
                   <td >GSTIN </td>
                   <td>: <?php echo $order->client->vat; ?></td>
                   
                   <td>GSTIN </td>
                   <td >: <?php echo $order->client->vat; ?></td>
                   
                   </tr>
                   <tr>
                   <td >State </td>
                   <td >: <?php echo $state_detail->state_name; ?></td>
                   
                   <td >State </td>
                   <td >: <?php echo $state_detail->state_name; ?></td>
                   
                   </tr>
                </table>
                <br>
                <?php
                
                
                    $rowspan = 'rowspan="2"';
                    $item_name_width = "25%";
                    $hsn_width = "12%";
                    if($order->client->state == "UP"){
                        $rowspan = 'rowspan="2"';
                        $item_name_width = "19%";
                        $hsn_width = "8%";
                        
                    }
                ?>
                <table style="width: 100%;" id="order_table">
                <tr style="background: #415164;">
                <td width="1%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Sr. No.</td>
                <td width="<?php echo $item_name_width;  ?>" <?php echo $rowspan; ?> style="color:#fff;border-color:#333;">Product Descripotion</td>
                <td width="<?php echo $hsn_width;  ?>" <?php echo $rowspan; ?> style="color:#fff;border-color:#333;">HSN Code</td>
                <td width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Pkg</td>
                <td width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Qty.</td>
                <td width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Rate</td>
                <td width="8%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Amt</td>
                <td width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Disc. Amt</td>
                <td width="8%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Taxable Amt</td>
                <?php if($order->client->state == "UP"){
                    ?>
                <td colspan="2" style="text-align:center;color:#fff;border-color:#333;">CGST</td>
                <td colspan="2" style="text-align:center;color:#fff;border-color:#333;">SGST</td>
                <?php 
                    
                } else{
                    ?>
                <td colspan="2" style="text-align:center;color:#fff;border-color:#333;">IGST</td>
            <?php } ?>
                <td <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Total Amt</td>
                </tr>
                <?php
                if($order->client->state == "UP"){
                    ?>
                    <tr style="background: #415164;color: #fff;">
                
                    <td style="text-align:center;color:#fff;border-color:#333;">Rate</td>
                    <td style="text-align:center;color:#fff;border-color:#333;">Amt</td>
                    <td style="text-align:center;color:#fff;border-color:#333;">Rate</td>
                    <td style="text-align:center;color:#fff;border-color:#333;">Amt</td>
                    </tr>   
               <?php 
                    
                }else {
                ?>
                    <tr style="background: #415164;">
                    
                    <td style="text-align:center;color:#fff;border-color:#333;">Rate</td>
                    <td style="text-align:center;color:#fff;border-color:#333;">Amt</td>
                    
                    </tr> 
                <?php 
                    
                }
                ?>
                <?php
                $i = 1;
                $qty = 0;
                $amt = 0;
                $dis_amt = 0;
                $taxable_amt = 0;
                $csgst_total = 0;
                $gst_total = 0;
                $order_total = 0;
                foreach ($order->items as $item) {
                    
                     //$item_detail = get_item_by_item_code($item['ItemID']);
                ?>
               <tr>
                   <td style="text-align:center;"><?php echo $i; ?></td>
                   <td class="description" align="left;" width="<?php echo $item_name_width; ?>"><?php echo $item['description']; ?></td>
                   <td width="<?php echo $hsn_width; ?>" style="text-align:center;"><?php echo $item['hsn_code']; ?></td>
                   <td style="text-align:right;"><?php echo  (int) $item['CaseQty']; ?></td>
                   <td style="text-align:right;"><?php echo  (int) $item['OrderQty'] / $item['CaseQty']; ?></td>
                  <?php  $qty = $qty + $item['OrderQty'] / $item['CaseQty']; ?>
                   <td style="text-align:right;"><?php echo $item['BasicRate']; ?></td>
                   <td style="text-align:right;"><?php echo $item['OrderAmt']; ?></td>
                   <?php $amt = $amt + $item['OrderAmt']; ?>
                   <td style="text-align:right;"><?php echo round($item['DiscAmt'],2); ?></td>
                   <?php $dis_amt = $dis_amt + $item['DiscAmt']; ?>
                    <?php $taxable_orderamt = $item['OrderAmt'] - $item['DiscAmt']; ?>
                   <td style="text-align:right;"><?php echo $taxable_orderamt; ?></td>
                   <?php $taxable_amt = $taxable_amt + $taxable_orderamt;
                   if($order->client->state == "UP"){
                       $cgst_rate = $item['cgst'];
                       $cgst_amt = $item['cgstamt'];
                       $tcs_col_span = "13";
                    ?>
                   <td style="text-align:right;"><?php echo $cgst_rate; ?></td>
                    <td style="text-align:right;"><?php echo $cgst_amt; ?></td>
                    <?php $csgst = $csgst + $cgst_amt; ?>
                    <td style="text-align:right;"><?php echo $cgst_rate; ?></td>
                    <td style="text-align:right;"><?php echo $cgst_amt; ?></td>  
                   <?php 
                       
                   }else {
                       ?>
                    <td style="text-align:right;"><?php echo $item['igst']; ?></td>
                       <td style="text-align:right;"><?php echo $item['igstamt']; ?></td>
                    <?php 
                    $gst_total = $gst_total + $item['igstamt'];
                    $tcs_col_span = "11";
                   }
                   
                   ?>
                   <td style="text-align:right;"><?php echo $item['NetOrderAmt']; ?></td>
                   <?php $order_total = $order_total + $item['NetOrderAmt']; ?>
                   </tr>
                   <?php
                   $i++;
                }
                $amt = (double) $amt;
                ?>
             
            <?php
            if($order->client->istcs == "1"){
                
                $tcsamt = $order->OrderAmt - $order_total;
                
              ?>
              <tr>
                 <td colspan="<?php echo $tcs_col_span; ?>" style="text-align:right;">TcsAmt</td>
                 <td style="text-align:right;"><?php echo round($tcsamt,2); ?></td>
              </tr>
            <?php
            }
            ?>
            <tr>
            
            <td colspan="4" style="text-align:center;">Total</td>
            <td style="text-align:right;"><?php echo $qty; ?></td>
            <td></td>
            <td style="text-align:right;"><?php echo round($amt,2); ?></td>
            <td style="text-align:right;"><?php echo round($dis_amt,2); ?></td>
            <td style="text-align:right;"><?php echo round($taxable_amt,2); ?></td>
            <?php
            if($order->client->state == "UP"){
                ?>
                <td></td>
                <td style="text-align:right;"><?php echo round($csgst,2); ?></td>
                <td style="text-align:right;"></td>
                <td style="text-align:right;"><?php echo round($csgst,2); ?></td>
                <?php
                }else {
                    ?>
                <td></td>
                <td style="text-align:right;"><?php echo round($gst_total,2); ?></td>
                <?php
                }
                ?>
            <td style="text-align:right;"><?php echo round($order_total,2); ?></td>
            </tr>
            
            
                    </table>
            </div>
   
            </div>
        </div>
                
		    
		    
		    
		</div>
	</div>
</div>


<?php init_tail(); ?>
<?php 
			             
		if(!in_array($challan->VehicleID, $vehicle_ids) && isset($challan)){}else{
			 ?>
		<script>
			 $("#custom_vehicle_number").hide();
	    </script>
		<?php
			 }
		if(!in_array($challan->VehicleID, $vehicle_ids) && isset($challan)){
		?>
		<script>
			 $("#capacity_div").hide();
	    </script>
		<?php
			 }
		?>
<script>
    $('#challan_vehicle').on('change', function() {
				var id = $(this).val();
				if(id == "TV"){
				    $("#custom_vehicle_number").css("display","");
				    
                    $(".chldr").css("display","none");
                    $("#capacity_div").hide();
                    
				}else{
				    $("#custom_vehicle_number").css("display","none");
				    
                    $(".chldr").css("display","");
                    $("#capacity_div").show();
				}
				//alert(id);
				var url = "<?php echo base_url(); ?>admin/challan/get_vehicle_detail";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {id: id},
                        dataType:'json',
                        success: function(data) {
                           
                            //$(".show").html(data);
                            if(data){
                                $("#vahicle_capacity").val(data["VehicleCapacity"]);
                                $("#vahicle_capacity1").val(data["VehicleCapacity"]);
                            }else{
                                $("#vahicle_capacity").val(" ");
                                $("#vahicle_capacity1").val(" ");
                            }
                            
                        }
                    });
			});
</script>
<?php //$this->load->view('admin/challan/challan_js'); ?>
<?php $this->load->view('admin/challan/validate_js'); ?>