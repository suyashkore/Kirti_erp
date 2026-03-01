<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
		    <div class="panel_s invoice accounting-template">
               <div class="additional"></div>
               <div class="panel-body">
            <?php
            /*echo "<pre>";
            print_r($vehicle);
            die;
            */
            if($challan){
                
                echo form_open($this->uri->uri_string(),array('id'=>'challan_form','class'=>'_transaction_form invoice-form'));
            }else {
                echo form_open($this->uri->uri_string()."/challan",array('id'=>'challan_form','class'=>'_transaction_form invoice-form'));
            }
            ?>       
			    <p>Route Challan</p>
			    <hr class="hr-panel-heading2" />
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
               //$next_challan_number = get_option('next_challan_number');
               $format = get_option('invoice_number_format');

               if(isset($invoice)){
                  $format = $invoice->number_format;
               }

               //$prefix = get_option('invoice_prefix');
                $prefix = "CHL".$this->session->userdata('finacial_year');
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

               $_is_challan = (isset($challan)) ? true : false;
               $next_challan_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
               if(isset($challan)){
                   $challan_nu = substr($challan->ChallanID,5); 
               }
               
               $_challan_number = str_pad($challan_nu??'', get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
               $isedit = isset($challan) ? 'true' : 'false';
               $data_original_number = isset($challan) ? $challan->number : 'false';

               ?>
               
               
               <!--<form class="info">-->
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
                              <input type="text" name="number1" id="number1" class="form-control number1" value="<?php echo ($challan) ? $_challan_number : $next_challan_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft??'') ? 'disabled' : '' ?>>
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
                              <!--<div class="input-group-addon">
                                  <i class="fa fa-search calendar-icon"></i>
                              </div>-->
                           </div>
                        </div>
                        <!--</form>-->
			                    <!--<div class="form-group">
                                    <label for="number">Challan ID</label>
                                    <input type="text" class="form-control" name="challan_no" id="challan_no">
                                </div>-->
			                </div>
			
			                <div class="col-md-6">
			                    
                                <input type="hidden" name="number" class="form-control" value="<?php echo ($challan) ? $prefix.$_challan_number : $next_challan_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft??'') ? 'disabled' : '' ?>>    
                                    <?php 
                                    //$date_attrs['disabled'] = true;
                                    $date = substr($challan->Transdate??'',0,10);
                                    //echo $date;
                                    $value = (isset($challan) ? _d($date) : _d(date('Y-m-d')));
                                      $date_attrs = array();
                                      
                                        //$date_attrs['disabled'] = true;
                                     
                                    
                                    echo render_date_input('date','Date',$value,$date_attrs); ?>
                                    
                                
			                </div>
			                <div class="col-md-12">
			                    
                                   
                                    <?php
                                    //print_r($routes);
                                        //echo $client->routes;
                                        //print_r($routes);
                                        //echo $challan->RouteID;
                                        
                                        
                                        //$selected_route = unserialize($client->routes);
                                        $selected = (isset($challan) ? $challan->route : '');
                                        
                                        //echo render_select( 'challan_route',$routes,array( 'id',array( 'name')), 'route',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                                        ?>
                                <div class="form-group">
                                    <label for="challan_route" class="control-label"><small class="req text-danger">* </small> Route</label>
                                    <select class="selectpicker" name="challan_route" id="challan_route" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php
                                    
                                        foreach($routes as $key => $value) {
                                            # code...
                                            
                                            ?>
                                            <option value="<?php echo $value["RouteID"]?>" <?php if(isset($challan) && $challan->RouteID == $value['RouteID']){echo 'selected';} ?>><?php echo $value["name"]?></option>
                                            <?php
                                        }
                                        
                                    ?>
                                    
                                    </select>
                                </div>    
                                <?php
                                //print_r($route_ids);
                                ?>
			                </div>
			                <div class="col-md-6">
			                    
                                    <?php
                                        //echo $client->routes;
                                        $new_element = array(
                                            "id"=>0,
                                            "reg_no"=>"Transport Vehicle",
                                            "type"=>"other",
                                            "status"=>1,
                                            );
                                            //array_push($vehicle, $new_element);
                                                //print_r($vehicle);
                                        //$selected_route = unserialize($client->routes);
                                        $selected = (isset($challan) ? $challan->vehicle : '');
                                        $vehicle_ids = array();
                                        //echo render_select( 'challan_vehicle',$vehicle,array( 'id',array( 'reg_no')), 'Vehicle',$selected);
                                        ?>
                                <div class="form-group">
                                    <label for="challan_vehicle" class="control-label"><small class="req text-danger">* </small> Vehicle</label>
                                    <select class="selectpicker" name="challan_vehicle" id="challan_vehicle" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    
                                    <?php
                                        foreach ($vehicle as $key => $value) {
                                            # code...
                                            array_push($vehicle_ids, $value['VehicleID']);
                                            ?>
                                            <option value="<?php echo $value["VehicleID"]?>" <?php if(isset($challan) && $challan->VehicleID == $value['VehicleID']){echo 'selected';} ?>><?php echo $value["VehicleID"]?></option>
                                            <?php
                                        }
                                        
                                    ?>
                                    <option value="TV" <?=
    (
        isset($challan, $challan->VehicleType)
        && !in_array($challan->VehicleType, $vehicle_ids ?? [], true)
        && $challan->VehicleType === 'TV'
    ) ? 'selected' : ''
?>>Transport Vehicle</option>
                                    <!-- <option value="TV" <?php 
                                    // if(!in_array($challan->VehicleID, $vehicle_ids) && isset($challan)){ echo "selected"; }?>>Transport Vehicle</option> -->
                                    </select>
                                </div>
			                </div>
			                
			                
			                
			                <div class="col-md-6 cvn" id="custom_vehicle_number">
			                    <div class="form-group">
                                    <label for="number"><small class="req text-danger">* </small> Vehicle No.</label>
                                    <?php $value = (isset($challan) ? $challan->VehicleID : ''); ?>
                                    <input type="text" class="form-control" name="vahicle_number" id="vahicle_number" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                    
                                    
                                </div>
			                </div>
			                <?php //if(!in_array($challan->VehicleID, $vehicle_ids) && isset($challan)){ }else { ?>
			                <div class="col-md-6" id="capacity_div">
			                    <div class="form-group">
                                    <label for="number">Capacity</label>
                                    <?php 
                                    $vehicle_capacity = get_vehicle_capacity($challan->VehicleID??'');
                                    //print_r($vehicle_capacity);
                                    //$value = (isset($challan) ? $challan->vahicle_capacity : ''); ?>
                                    <input type="text" class="form-control" name="vahicle_capacity" id="vahicle_capacity" value="<?php echo $vehicle_capacity->VehicleCapacity??''; ?>" disabled>
                                    <!--<input type="hidden" name="vahicle_capacity" id="vahicle_capacity" value="<?php echo $value; ?>">
                                    -->
                                </div>
			                </div>
			                <?php //} ?>
			                
			            </div>
			        </div>
			        <div class="col-md-3">
			            <div class="row">
			                <div class="col-md-12">
			                    <div class="form-group" app-field-wrapper="challan_driver">
                                    <label for="challan_driver" class="control-label"> <small class="chldr text-danger">* </small>Driver</label>
                                    <?php $value = (isset($challan) ? $challan->DriverID : ''); ?>
                                    <input type="text" class="form-control" name="challan_driver" id="challan_driver" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group" app-field-wrapper="challan_loader">
                                    <label for="challan_loader" class="control-label">Loader</label>
                                    <?php $value = (isset($challan) ? $challan->LoaderID : ''); ?>
                                    <input type="text" class="form-control" name="challan_loader" id="challan_loader" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group" app-field-wrapper="challan_sales_man">
                                    <label for="challan_sales_man" class="control-label">Sales Man</label>
                                    <?php $value = (isset($challan) ? $challan->SalesmanID : ''); ?>
                                    <input type="text" class="form-control" name="challan_sales_man" id="challan_sales_man" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-md-3">
			            <div class="row">
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Challan Value</label>
                                    <?php $value = (isset($challan) ? $challan->ChallanAmt : '0'); ?>
                                    <input type="text" class="form-control" name="txtchalanvalue1" id="txtchalanvalue1" value="<?php echo $value; ?>"  disabled>
                                    <input type="hidden" name="txtchalanvalue" id="txtchalanvalue" value="<?php echo $value; ?>">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Total Cases</label>
                                    <?php $value = (isset($challan) ? $challan->Cases : ''); ?>
                                    <input type="text" class="form-control" name="txtCases1" id="txtCases1" value="<?php echo $value; ?>" disabled>
                                    <input type="hidden" name="txtCases" id="txtCases" value="<?php echo $value ?>">
                                </div>
			                </div>
			                <div class="col-md-12">
			                    <div class="form-group">
                                    <label for="number">Total Crates</label>
                                    <?php $value = (isset($challan) ? $challan->Crates : ''); ?>
                                    <input type="text" class="form-control" name="txtCrates1" id="txtCrates1" value="<?php echo $value; ?>" disabled>
                                    <input type="hidden" name="txtCrates" id="txtCrates" value="<?php echo $value ?>">
                                    <input type="hidden" name="order_id" id="order_id" value="<?php echo $order->number??''; ?>">
                                    <input type="hidden" name="rate_changeID" id="rate_changeID" value="0">
                                    <input type="hidden" name="new_record" id="new_record" value="">
                                
                                </div>
			                </div>
			                
			            </div>
			        </div>
			        <div class="col-md-2" style="margin-top: 11%;">
			            <a href="#" class="btn btn-danger updateRate_btn" style="display:none;">update Rate</a>
			         </div>
			        <div class="clearfix"></div>
			        <div class="col-md-12">
			            <span ><b style="color:red;">Note:</b> Item values in red color indicates old rates are applied.</span>
			        </div>
			        <div class="clearfix"></div>
			        
			        <!-- order table-->
			        <div class="col-md-12">
			            <br>
			            <?php
			            if($challan){
			                
			                
			                 $current_chl_order = array();
			                 $order_ids = array();
			                 $allorder_ids = array();
			                 $item_code_list = array();
			                 $order_acc = array();
			                 $order_route = array();
			                 foreach ($challan->order as $key => $value) {
			                     
			                     $item_data = get_item_by_order_id($value["OrderID"]);
			                     
			                     foreach ($item_data as $key => $values) {
                                        array_push($item_code_list, $values["ItemID"]);
                                    }
                                
                                       
                                   
			                 }
			                 
			                  
			                 $selected_company = $this->session->userdata('root_company');
			                 
			                 $get_acc_by_route = get_acc_by_route($challan->RouteID,$selected_company);
			                 
			                 
			                
			                 $acc_list = array();
			                 foreach ($get_acc_by_route as $key => $accname) {
			                     array_push($acc_list, $accname["AccountID"]);
			                 }
			                 
			                 //echo "madhav";
			                 $orderid_list = get_orderids_by_Acc_ids($acc_list);
			                 
			                 if($orderid_list){
			                     
    			                     foreach ($orderid_list as $key1 => $value1) {
                
                                        array_push($order_ids,$value1['OrderID']);
                                    }
                                 
                                    $item_ids = get_itemID_by_order_IDs($order_ids);
                                    
                                    foreach ($item_ids as $key2 => $value2) {
                    
                                        array_push($item_code_list,$value2['ItemID']);
                                    }
			                 }
			                 
			             
			                 /*foreach ($acc_list as $accountID) {
			                      
			                      $order_details = get_order_by_Acc_id($accountID,$selected_company);
			                      if($order_details){
			                          foreach ($order_details as $key => $orders) {
			                              
			                              $item_data = get_item_by_order_id($orders["OrderID"]);
			                     
            			                     foreach ($item_data as $key => $values) {
                                                    array_push($item_code_list, $values["ItemID"]);
                                                }
                                
                                        
			                          }
			                          
			                      }
			                        
			                  }*/
			                 
			               
			                
			                  $item_code_list = array_unique($item_code_list);
			                  
			                 
			              
			                $challan_cases = 0;
			                $challan_crates = 0;
                            $challan_subtotal = 0;
                            $challan_total = 0;
                            foreach ($challan->order as $key2 => $value2) {
                                array_push($current_chl_order, $value2["OrderID"]);
                                //echo $value;
                                
                            }
                            /*echo "<pre>";
                            //echo $challan->RouteID;
                            print_r($current_chl_order);
                            die;*/
                            ?>
                            <table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;"><thead style="background: #438EB9;color: #FFF;">
			             <thead style="background: #438EB9;color: #FFF;">
			                 <th>Tag</th>
			                 <th>OrderNo</th>
			                 <th>AccountName</th>
			                 <th>StateID</th>
			                 <th>Ordertype</th>
			                 <th>SalesID</th>
			                 <th>SalesDate</th>
			                 <?php
			                 foreach ($item_code_list as $code) { ?>
                                    <th width="5%"><?php echo $code; ?></th>
                            <?php    }
			                 ?>
			                 <th>Crates</th>
			                 <th>Cases</th>
			                 <th>OrderAmt</th>
			                 <th>SaleAmt</th>
			                 <th>TCSPer</th>
			                 <th>TCSAmt</th>
			             </thead>
			             <tbody>
                        <?php
                            
			                 foreach ($current_chl_order as $value) {
			                     array_push($allorder_ids,$value);
			                 $order_data = get_order_detail($value);
			                 //$transaction_number = format_transaction_number($order_data->ChallanID);
			                 //print_r($order_data);
			                 //echo $transaction_number
			                ?>
			         
			                 <tr class="bg-an">
			                 <td><input type="checkbox" name="order_id" class="chk" checked value="<?php echo $order_data->OrderID;?>"></td>
			                 <td><?php echo $order_data->OrderID;?></td>
			                 <!--<td><?php echo get_company_name($order_data->OrderID); ?></td>-->
			                 <td><?php $account_name = get_account_name($order_data->AccountID,$selected_company);
			                 echo $account_name->company;  ?></td>
			                 <td><?php 
			                 $short_name = get_state_name_by_acc_id($order_data->AccountID,$selected_company);
			                 echo $short_name; ?></td>
			                 <td><?php echo $order_data->OrderType;?></td>
			                 <td><?php echo $order_data->SalesID;?></td>
			                 <td><?php echo substr($order_data->Transdate,0,10);?></td>
			                 <?php
			                 foreach ($item_code_list as $code) { 
                                    $item_data1 = get_item_detail_by_order($order_data->OrderID,$code);
			                 if($item_data1){
                                
                            ?>
                                <td width="5%"><input type="hidden" value="<?php echo $qty.'-'.$pack_qty.'-'.$rate.'-'.$gst.'-'.$cscr; ?>" /><input style="width: 50px;" type="text" onchange="total()" name="<?php echo 'qty_'.$item_data1->ItemID; ?>" value="<?php echo $item_data1->OrderQty / $item_data1->CaseQty; ?>"></td>
                <?php }else {
                   ?>
                                <td width="5%"><input type="hidden" value="<?php echo $qty.'-'.$pack_qty.'-'.$rate.'-'.$gst.'-'.$cscr; ?>" /><input style="width: 50px;" type="text" onchange="total()" name="<?php echo 'qty_'.$item_data1->ItemID; ?>" value="0"></td>
                <?php  }
			                   }
                             
			                 ?>
			                 <td style="text-align: right;"><?php echo (int) $order_data->Crates;
			                 $challan_crates = $challan_crates + $order_data->Crates;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo (int) $order_data->Cases;
			                 $challan_cases = $challan_cases + $order_data->Cases;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo $order_data->subtotal;
			                 $challan_subtotal = $challan_subtotal + $order_data->subtotal;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo $order_data->OrderAmt;
			                 $challan_total = $challan_total + $order_data->OrderAmt;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo "0";?></td>
			                 <td style="text-align: right;"><?php echo "0";?></td>
			                 
			                 </tr>
			                 <?php } ?>
			                 <?php
			                 if($order_ids){
			                     foreach ($order_ids as $IDs) {
			                     array_push($allorder_ids,$IDs);
			                        $order_data = get_order_detail($IDs);
			                        
			                        ?>
			                        <tr class="">
			                 <td><input type="checkbox" name="order_id" class="chk" value="<?php echo $order_data->OrderID;?>"></td>
			                 <td><?php echo $order_data->OrderID;?></td>
			                 <!--<td><?php echo get_company_name($order_data->OrderID); ?></td>-->
			                 <td><?php 
			                 
			                 $account_name = get_account_name($order_data->AccountID,$selected_company);
			                 echo $account_name->company; ?></td>
			                 <td><?php 
			                 $short_name = get_state_name_by_acc_id($order_data->AccountID,$selected_company);
			                 echo $short_name; ?></td>
			                 <td><?php echo $order_data->OrderType;?></td>
			                 <td><?php echo $order_data->SalesID;?></td>
			                 <td><?php echo substr($order_data->Transdate,0,10); ?></td>
			                 <?php
			                 foreach ($item_code_list as $code) { 
                                    $item_data1 = get_item_detail_by_order($IDs,$code);
			                 if($item_data1){
                                $qty = $item_data1->OrderQty / $item_data1->CaseQty;
                            ?>
                                <td width="5%" align="right"><input style="width: 50px;" type="text" name="qty" value="<?php echo $qty; ?>"></td>
                <?php }else {
                   ?>
                                <td width="5%"><input style="width: 50px;" type="text" name="qty" value=""></td>
                <?php  }
                                ?>
                                
                                <?php
			                   }
                             
			                 ?>
			                 <td style="text-align: right;"><?php echo (int) $order_data->Crates;
			                 $challan_crates = $challan_crates + $order_data->Crates;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo (int) $order_data->Cases;
			                 $challan_cases = $challan_cases + $order_data->Cases;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo $order_data->subtotal;
			                 $challan_subtotal = $challan_subtotal + $order_data->subtotal;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo $order_data->OrderAmt;
			                 $challan_total = $challan_total + $order_data->OrderAmt;
			                 ?></td>
			                 <td style="text-align: right;"><?php echo "0";?></td>
			                 <td style="text-align: right;"><?php echo "0";?></td>
			                 
			                 </tr>
			                 
			                 <?php
			                    }
			                 }
			                 ?>
			                 </tbody>
			             <tfoot>
			                 <tr>
			                    <td style="text-align:center;">Total</td>
			                    <td></td>
			                    <td></td>
			                    <td></td>
			                    <td></td>
			                    <td></td>
			                    <td></td> 
			                    <?php
			                    foreach ($item_code_list as $code) {
                                        
                                    $item_count = get_itemcout_all_order($allorder_ids,$code);
                                
                                    $item_count_new = (int) $item_count;
                                    ?>
                                    <td style="text-align: right;"><?php echo $item_count_new; ?></td>
                            <?php    }
			                    ?>
			                    <td style="text-align: right;"><?php echo $challan_crates; ?></td>
			                    <td style="text-align: right;"><?php echo $challan_cases; ?></td>
			                    <td style="text-align: right;"><?php echo $challan_subtotal; ?></td>
			                    <td style="text-align: right;"><?php echo $challan_total; ?></td>
			                    <td style="text-align: right;">0.00</td>
			                    <td style="text-align: right;">0.00</td>
			                 </tr>
			             </tfoot>
			         </table>
			             
			         <?php
			                 
			            }
			            
			           
			            ?>
			            
			            <span id="searchh2" class="searchh2" style="display:none;">
			                Loading.....
			            </span>
			            <div id="showtable" class="showtable">
			                
			            </div>
			        </div>
			        
			        <div class="col-md-12">
			        <div class="row">
                        <div class="col-md-12 mtop15">
			            
			                <div class="btn-bottom-toolbar text-right" style="margin: 0;">
			                    <?php
			                    if($challan){
			                        if (has_permission('challan', '', 'edit')) {
			                        ?>
			                 <button type="submit" class="btn-tr btn btn-info invoice-form-submit transaction-submit">Update</button>
			                 <?php } ?>
			                 <!--<button type="button" class="btn-tr btn btn-info invoice-form-submit transaction-submit">Dispatch Sheet</button>-->
			                 <!--<a href="<?php echo admin_url('challan/gatepass/'.$challan->ChallanID.'?output_type=I'); ?>" target="_blank" class="mleft10 pull-right btn btn-success">
                     <i class="fa fa-eye"></i> Gate Pass </a>-->
                     <?php if (has_permission('invoices', '', 'view')) { ?>
			                 <a href="<?php echo admin_url('challan/dispatchsheet/'.$challan->ChallanID.'?output_type=I'); ?>" target="_blank" class="mleft10 pull-right btn btn-success">
                     <i class="fa fa-eye"></i> Dispatch Sheet </a>
                     <?php } ?>
			                 <!--<button type="button" class="btn-tr btn btn-info invoice-form-submit transaction-submit">-->
			     <?php if (has_permission('invoices', '', 'view')) { ?>
			                 <a href="<?php echo admin_url('challan/pdf/'.$challan->ChallanID.'?output_type=I'); ?>" target="_blank" class="mleft10 pull-right btn btn-success">
                     <i class="fa fa-eye"></i> Invoice Print </a> 
                     <?php } ?>
			                     <!--</button>-->
			                
			                 <?php
			                    }else {
			                    ?>
			         <?php if (has_permission('challan', '', 'create')) { ?>
			                    <button type="submit" class="btn-tr btn btn-info invoice-form-submit transaction-submit">Save</button>
			           <?php } ?>         
			                    
			                    <?php } ?>
			                    </div>
			              
                        </div>
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

<?php 
			                //$vehicle_detail = get_vehicle_detail($challan->VehicleID);
			                //print_r($vehicle_detail);
			                if(!in_array($challan->VehicleID??'', $vehicle_ids) && isset($challan)){}else{
			                    ?>
			                    <script>
			                        $("#custom_vehicle_number").hide();
			                    </script>
			                    <?php
			                }
			                if(!in_array($challan->VehicleID??'', $vehicle_ids) && isset($challan)){
			                    ?>
			                    <script>
			                        $("#capacity_div").hide();
			                    </script>
			                    <?php
			                }
			                ?>
<?php $this->load->view('admin/challan/challan_js'); ?>
<?php $this->load->view('admin/challan/validate_js'); ?>
</body>
<style>
    #challan_data th,td{
        padding:5px;
        border:1px solid #ccc;
    }
    #challan_data input[type=text] {
        height: 30px;
        text-align: right;
    }
    .bg-an {
    background-color: #65baba;
}
</style>
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
   /* console.log(minStartDate);
    console.log(maxEndDate_new);*/
    
    $('#date').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    
    
    });
</script> 
</html>
