<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
		    
		    <?php
                    $order_details = $challan->order;
                    
            ?>
		    
        <div class="panel_s invoice accounting-template">
            <div class="panel-body">
                
            <?php
                echo form_open($this->uri->uri_string(),array('id'=>'challan_form','class'=>'_transaction_form invoice-form'));
            ?>
            <div class="col-md-10">
                    </div>
                    <div class="col-md-2">
                    <?php
                        if(isset($challan) && $challan->ChallanAmt == 0.00){ }else{
                        $selected_company = $this->session->userdata('root_company');
                        $fy = $this->session->userdata('finacial_year');
                        $fy_new  = $fy + 1;
                        $first_date = '20'.$fy.'-04-01';
                        $lastdate_date = '20'.$fy_new.'-03-31';
                        $curr_date = date('Y-m-d');
                        $lgstaff = $this->session->userdata('staff_user_id');
                        $challan_date = substr($challan->Transdate,0,10);
                        
                        $challan_date_new    = new DateTime($challan_date);
                        $first_date_yr = new DateTime($first_date);
                        $last_date_yr = new DateTime($lastdate_date);
                        $curr_date_new = new DateTime($curr_date);
                
                $sql = 'SELECT * FROM tblchallanmaster WHERE PlantID = '.$selected_company.' AND FY LIKE "'.$fy.'" ORDER BY tblchallanmaster.ChallanID DESC ';
                $result_data = $this->db->query($sql)->row();
                $lastdate_challan = substr($result_data->Transdate,0,10);
                if($curr_date_new > $last_date_yr){
                    $lastdate = $lastdate_date;
                }else{
                    $lastdate = date('Y-m-d');
                }
                
                $this->db->select('*');
                $this->db->where('plant_id', $selected_company);
                $this->db->where('year', $fy);
                $this->db->where('staff_id', $lgstaff);
                $this->db->LIKE('feature', "challan_list");
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
                            
                            if ($challan_date_new < $tillDate_new) {
                                $return = 'disabled';
                                
                            }else{
                                //$tillDate_new2 = new DateTime($lastdate_challan);
                                /*if ($challan_date_new < $tillDate_new2) {
                                    $return = 'disabled';
                                }else{*/
                                    $return = '';
                                //}
                            }
                        }
		if (has_permission_new('challan_list', '', 'delete') && is_null($challan->Gatepassuserid)) {
            ?>
            <?php if($return == "disabled"){
            ?>
            <a href="#" class="btn btn-danger <?php echo $return;?>">Cancel</a>
            <?php
            }else{
            ?>
            <button type="submit" class="btn-tr btn btn-danger" value="cancel" name="submitForm">Cancel</button>
            <?php
            }?>
			         
			            <?php }else{
			                     if(is_admin()){
			                     ?>
			                                
			                 <button type="submit" class="btn-tr btn btn-danger" value="cancel" name="submitForm">Cancel</button>
			             <?php
			                          }
			                      } 
			                }
			             ?>
                    </div>
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
               
               $_challan_number = str_pad($challan_nu, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
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
                              <input type="text" name="number1" id="number1" class="form-control number1" value="<?php echo ($challan) ? $_challan_number : $next_challan_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>
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
			                    
                                <input type="hidden" name="number" class="form-control" value="<?php echo ($challan) ? $prefix.$_challan_number : $next_challan_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>    
                                    <?php 
                                    //$date_attrs['disabled'] = true;
                                    $date = substr($challan->Transdate,0,10);
                                    //echo $date;
                                    $value = (isset($challan) ? _d($date) : _d(date('Y-m-d')));
                                      $date_attrs = array();
                                      
                                        $date_attrs['disabled'] = true;
                                     
                                    
                                    echo render_date_input('date','Date',$value,$date_attrs); ?>
                                    
                                <input type="hidden" name="org_date" value="<?php echo $date; ?>">
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
                                    <option value="TV" <?php if(!in_array($challan->VehicleID, $vehicle_ids) && isset($challan)){ echo "selected"; }?>>Transport Vehicle</option>
                                    </select>
                                </div>
			                </div>
			                <div class="col-md-6 cvn" id="custom_vehicle_number">
			                    <div class="form-group">
                                    <label for="number"><small class="req text-danger">* </small> Vehicle No.</label>
                                    <?php $value = (isset($challan) ? $challan->VehicleID : ''); ?>
                                    <input type="text" class="form-control" name="vahicle_number" id="vahicle_number" value="<?php echo $value; ?>"  style="text-transform:uppercase">
                                    
                                    
                                </div>
			                </div>
			                <div class="col-md-6" id="capacity_div">
			                    <div class="form-group">
                                    <label for="number">Capacity</label>
                                    <?php 
                                    $vehicle_capacity = get_vehicle_capacity($challan->VehicleID);
                                    //print_r($vehicle_capacity);
                                    //$value = (isset($challan) ? $challan->vahicle_capacity : ''); ?>
                                    <input type="text" class="form-control" name="vahicle_capacity" id="vahicle_capacity" value="<?php echo $vehicle_capacity->VehicleCapacity; ?>" disabled>
                                    <!--<input type="hidden" name="vahicle_capacity" id="vahicle_capacity" value="<?php echo $value; ?>">
                                    -->
                                </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-md-4">
			            <div class="row">
			                <div class="col-md-4">
			                    <div class="form-group" app-field-wrapper="challan_driver">
                                    <label for="challan_driver" class="control-label"> <small class="chldr text-danger">* </small>Driver</label>
                                    <?php $value = (isset($challan) ? $challan->DriverID : ''); ?>
                                    <input type="text" class="form-control" name="challan_driver" id="challan_driver" value="<?php echo $value; ?>"  style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-8" style="margin-top: 20px;">
			                    <div class="form-group" app-field-wrapper="challan_driver_name">
                                    
                                    <?php $value = (isset($challan) ? $challan->driver_fn." ".$challan->driver_ln : ''); ?>
                                    <input readonly type="text" class="form-control" name="challan_driver_name" id="challan_driver_name" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                </div>
			                </div>
			                
			                <div class="col-md-4">
			                    <div class="form-group" app-field-wrapper="challan_loader">
                                    <label for="challan_loader" class="control-label">Loader</label>
                                    <?php $value = (isset($challan) ? $challan->LoaderID : ''); ?>
                                    <input type="text" class="form-control" name="challan_loader" id="challan_loader" value="<?php echo $value; ?>"  style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-8" style="margin-top: 20px;">
			                    <div class="form-group" app-field-wrapper="challan_loader_name">
                                    
                                    <?php $value = (isset($challan) ? $challan->loader_fn." ".$challan->loader_ln : ''); ?>
                                    <input readonly type="text" class="form-control" name="challan_loader_name" id="challan_loader_name" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-4">
			                    <div class="form-group" app-field-wrapper="challan_sales_man">
                                    <label for="challan_sales_man" class="control-label">Sales Man</label>
                                    <?php $value = (isset($challan) ? $challan->SalesmanID : ''); ?>
                                    <input type="text" class="form-control" name="challan_sales_man" id="challan_sales_man" value="<?php echo $value; ?>"  style="text-transform:uppercase">
                                </div>
			                </div>
			                <div class="col-md-8" style="margin-top: 20px;">
			                    <div class="form-group" app-field-wrapper="challan_sales_man_name">
                                    
                                    <?php $value = (isset($challan) ? $challan->Salesman_fn." ".$challan->Salesman_ln : ''); ?>
                                    <input readonly type="text" class="form-control" name="challan_sales_man_name" id="challan_sales_man_name" value="<?php echo $value; ?>" style="text-transform:uppercase">
                                </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-md-2">
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
                                    <!--<input type="hidden" name="order_id" id="order_id" value="<?php echo $order->number; ?>">
                                    -->
                                    
                                </div>
			                </div>
			            </div>
			        </div>
			        <div class="col-md-12">
			        <div class="row">
                        <div class="col-md-12 mtop15">
			            
			                <div class="btn-bottom-toolbar text-right">
			                   
			                <?php
			                    if($challan){
			                        if(isset($challan) && $challan->ChallanAmt == 0.00){ }else{
			                            if (has_permission_new('challan_list', '', 'edit')) {
			                         
			                        ?>
			                        
			                        <?php if($return == "disabled"){
                                    ?>
                                    <a href="#" class="btn btn-info <?php echo $return;?>">Update</a>
                                    <?php
                                    }else{
                                    ?>
                                    <button type="submit" class="btn-tr btn btn-info" value="update" name="submitForm">Update</button>
                                    <?php
                                    }?>
			                            
			                        <?php } 
			                        /*if (has_permission('challan', '', 'delete') && is_null($challan->Gatepassuserid)) {
			                        ?>
			                            <button type="submit" class="btn-tr btn btn-danger" value="cancel" name="submitForm">Cancel</button>
			                        <?php }else{
			                            if(is_admin()){
			                                ?>
			                                
			                                <button type="submit" class="btn-tr btn btn-danger" value="cancel" name="submitForm">Cancel</button>
			                          <?php
			                          }
			                        } */
			                        }
			                        ?>
                                    <?php if (has_permission_new('challan_list', '', 'view')) { ?>
        			                 <a href="<?php echo admin_url('challan/dispatchsheet/'.$challan->ChallanID.'?output_type=I'); ?>" target="_blank" class="mleft10 pull-right btn btn-success">
                                        <i class="fa fa-eye"></i> Dispatch Sheet </a>
                                    <?php } ?>
			                 
			                        <?php if (has_permission_new('challan_list', '', 'view')) { ?>
            			                 <a href="<?php echo admin_url('challan/pdf/'.$challan->ChallanID.'?output_type=I'); ?>" target="_blank" class="mleft10 pull-right btn btn-success">
                                        <i class="fa fa-eye"></i> Invoice Print </a> 
                                    <?php } ?>
			                     <!--</button>-->
			                
			                 <?php
			                    }
			                   ?>
			                </div>
			              
                        </div>
                    </div>
                </div>
                <?php
                $order_ids = "";
                $state_ids = "";
                $i = 1;
                foreach ($challan->order as $orderkey => $ordervalue) {
                    
                    if($i == 1){
                        $order_ids = $ordervalue["SalesID"];
                    }else{
                        $order_ids = $order_ids.",".$ordervalue["SalesID"];
                    }
                    
            ?>  
            
            
            
            <?php } ?>
            <input type="hidden" name="trans_id" id="trans_id" value="<?php echo $order_ids;?>">
            
            <?php echo form_close(); ?>
                <hr class="hr-panel-heading2" />
                <div class="col-md-12">
            <h4 style="text-align: center;">Order Detail</h4>
            </div>
            <?php
                foreach ($challan->order as $orderkey => $ordervalue) {
                    
                    $client_details = get_client_detail($ordervalue["AccountID"]);
                    $client_details2 = get_client_detail($ordervalue["AccountID2"]);
                    
            ?>  
            
                <div class="col-md-12">
               <table style="width: 100%;" id="order_table" border="1" cellspacing="0" cellpadding="2">
                   <tr>
                       <td width="15%">Order No. </td>
                       <td width="35%">: <?php echo $ordervalue["OrderID"]; ?></td>
                       <td width="15%">Order Date :</td>
                       <td width="35%"> : <?php echo _d(substr($ordervalue["Transdate"],0,10)); ?></td>
                   </tr>
                   <tr>
                      <td>SalesID </td>
                       <td> : <?php echo $ordervalue["SalesID"]; ?></td>
                       <td>State Code </td>
                       <td> : <?php echo $client_details->state; ?></td>
                   </tr>
                   <tr>
                      <td>Date of Supply </td>
                       <td>: <?php echo _d(substr($challan->Transdate,0,10)); ?></td>
                       <td>Place of Supply </td>
                       <td>: <?php echo $client_details->city.", ".$client_details->state; ?></td>
                   </tr>
                   <tr style="background: #415164;">
                       <td  colspan="2" style="text-align:center;color:#fff;border-color:#333;">Detail of Receiver / Billed to</td>
                       <td  colspan="2" style="text-align:center;color:#fff;border-color:#333;">Details of Consignee/Shipped to</td>
                   </tr>
                   <tr>
                   <td >Name </td>
                   <td >: <?php echo $client_details->company; ?></td>
                   <td >Name </td>
                   <td >: <?php echo $client_details2->company; ?></td>
                   </tr>
                   <tr>
                   <td >Mob </td>
                   <td>: <?php echo $client_details->cmobile; ?></td>
                   <td >Mob </td>
                   <td >: <?php echo $client_details2->cmobile; ?></td>
                   </tr>
                   <tr>
                   <td >Address </td>
                   <td >: <?php echo $client_details->address." ".$client_details->Address3." ".$client_details->city.", ".$client_details->zip; ?> </td>
                   <td >Address </td>
                   <td >: <?php echo $client_details2->address." ".$client_details2->Address3." ".$client_details2->city.", ".$client_details2->zip; ?></td>
                   </tr>
                   
                   <tr>
                   <td >GSTIN </td>
                   <td>: <?php echo $client_details->vat; ?></td>
                   
                   <td>GSTIN </td>
                   <td >: <?php echo $client_details2->vat; ?></td>
                   
                   </tr>
                   
                </table>
                <br>
                <?php
                
                
                    $rowspan = 'rowspan="2"';
                    $item_name_width = "25%";
                    $hsn_width = "12%";
                    if($client_details->state == "UP"){
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
                <?php if($client_details->state == "UP"){
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
                if($client_details->state == "UP"){
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
                $order_detail = get_item_by_order_id($ordervalue['OrderID']);
                foreach ($order_detail as $item) {
                    $hsn_code = get_hsn_byitem_id($item['ItemID']);
                if($item['NetOrderAmt'] == "0.00"){
                    
                }else{
                ?>
                
               <tr>
                   <td style="text-align:center;"><?php echo $i; ?></td>
                   <td class="description" align="left;" width="<?php echo $item_name_width; ?>"><?php echo $item['description']; ?></td>
                   <td width="<?php echo $hsn_width; ?>" style="text-align:center;"><?php echo $hsn_code->hsn_code; ?></td>
                   <td style="text-align:right;"><?php echo  (int) $item['CaseQty']; ?></td>
                   <td style="text-align:right;"><?php echo  (int) $item['caseqty']; ?></td>
                  <?php  $qty = $qty + $item['caseqty']; ?>
                   <td style="text-align:right;"><?php echo $item['BasicRate']; ?></td>
                   <td style="text-align:right;"><?php echo $item['OrderAmt']; ?></td>
                   <?php $amt = $amt + $item['OrderAmt']; ?>
                   <td style="text-align:right;"><?php echo round($item['DiscAmt'],2); ?></td>
                   <?php $dis_amt = $dis_amt + $item['DiscAmt']; ?>
                    <?php $taxable_orderamt = $item['OrderAmt'] - $item['DiscAmt']; ?>
                   <td style="text-align:right;"><?php echo $taxable_orderamt; ?></td>
                   <?php $taxable_amt = $taxable_amt + $taxable_orderamt;
                   if($client_details->state == "UP"){
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
                }
                $amt = (double) $amt;
                ?>
             
            <?php
            if($client_details->istcs == "1"){
                
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
            if($client_details->state == "UP"){
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
            
            <?php } ?>
   
            </div>
        </div>
        <br>
        
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
<script>
$(document).ready(function(){
    
    $('#challan_driver').on('focus',function(){
            $('#challan_driver').val('');
            $('#challan_driver_name').val('');
       
     });
    // Initialize For Account
     $( "#challan_driver" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/challan/accountlist_driver",
            type: 'post',
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          $('#challan_driver').val(ui.item.value);
          $('#challan_driver_name').val(ui.item.label);
            return false;
        }
      });
      
    $('#challan_driver').on('blur', function () {
      
        var AccountID = $(this).val();
            if(empty(AccountID)){
                
            }else{
                $.ajax({
                    url: "<?=base_url()?>admin/challan/get_Account_Details",
                    type: 'post',
                    dataType: "json",
                    data: {
                      AccountID: AccountID,
                    },
                    success: function( data ) {
                        if(empty(data)){
                            alert('AccountID not found.');
                            $("#challan_driver").val('');
                             $("#challan_driver_name").val('');
                            $("#challan_driver").focus();
                        }else{
                            var fullName = data.firstname +" " + data.lastname;
                            $('#challan_driver_name').val(fullName); // display the selected text
                          
                        }
                    }
                });
            }
    });
    
    $('#challan_loader').on('focus',function(){
            $('#challan_loader').val('');
            $('#challan_loader_name').val('');
       
     });
    // Initialize For Account
     $( "#challan_loader" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/challan/accountlist_driver",
            type: 'post',
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          $('#challan_loader').val(ui.item.value);
          $('#challan_loader_name').val(ui.item.label);
            return false;
        }
      });
      
    $('#challan_loader').on('blur', function () {
      
        var AccountID = $(this).val();
            if(empty(AccountID)){
                
            }else{
                $.ajax({
                    url: "<?=base_url()?>admin/challan/get_Account_Details",
                    type: 'post',
                    dataType: "json",
                    data: {
                      AccountID: AccountID,
                    },
                    success: function( data ) {
                        if(empty(data)){
                            alert('AccountID not found.');
                            $("#challan_loader").val('');
                            $("#challan_loader_name").val('');
                            $("#challan_loader").focus();
                        }else{
                            var fullName = data.firstname +" " + data.lastname;
                            $('#challan_loader_name').val(fullName); // display the selected text
                          
                        }
                    }
                });
            }
    });
    
    $('#challan_sales_man').on('focus',function(){
            $('#challan_sales_man').val('');
            $('#challan_sales_man_name').val('');
       
     });
    // Initialize For Account
     $( "#challan_sales_man" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/challan/accountlist_salesMan",
            type: 'post',
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          $('#challan_sales_man').val(ui.item.value);
          $('#challan_sales_man_name').val(ui.item.label);
            return false;
        }
      });
      
    $('#challan_sales_man').on('blur', function () {
      
        var AccountID = $(this).val();
            if(empty(AccountID)){
                
            }else{
                $.ajax({
                    url: "<?=base_url()?>admin/challan/get_Account_Details_salesman",
                    type: 'post',
                    dataType: "json",
                    data: {
                      AccountID: AccountID,
                    },
                    success: function( data ) {
                        if(empty(data)){
                            alert('AccountID not found.');
                            $("#challan_sales_man").val('');
                            $("#challan_sales_man_name").val('');
                            $("#challan_sales_man").focus();
                        }else{
                            var fullName = data.firstname +" " + data.lastname;
                            $('#challan_sales_man_name').val(fullName); // display the selected text
                          
                        }
                    }
                });
            }
    });
});
</script>
<?php //$this->load->view('admin/challan/challan_js'); ?>
<?php $this->load->view('admin/challan/validate_js'); ?>