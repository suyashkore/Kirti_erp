<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .form-group{
        margin-bottom: 1px;
    }
    input[type=text]{
        height: 29px !important;
    }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <?php
      //echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
      
      ?>
      <div class="col-md-12">
        <div class="panel_s accounting-template estimate">
        <div class="panel-body">
      
            <div class="tab-content">
                
                <div role="tabpanel" class="tab-pane active" id="general_infor">
                <div class="row">
                    
                    <?php
                        $data_attr = array();
                        if(isset($sale_return)){
                            $data_attr = array(
                                "disabled" =>true
                            );
                        }
                    ?>   
                    <?php
                                $selected_company = $this->session->userdata('root_company');
            			        $fy = $this->session->userdata('finacial_year');
                                if($selected_company == 1){
                                        $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');
                                        
                                    }elseif($selected_company == 2){
                                        $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');
                                        
                                    }elseif($selected_company == 3){
                                        $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');
                                        
                                    }
                                   $format = get_option('invoice_number_format');
                
                               
                                $prefix = "VRT".$fy;
                                //$prefix = $prefix.'<span id="prefix_year">'.$fy.'</span>';
                               
                              
                               //$_vehicle_return_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                               $_vehicle_return_number = str_pad($new_vehicle_returnNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                    ?>
                            <div class="col-md-12">
                                <div class="searchh11" id="searchh11" style="display:none;">Please wait fetching data...</div>
                                <div class="searchh3" style="display:none;">Please wait Create new VehRtn...</div>
                                <div class="searchh4" style="display:none;">Please wait update VehRtn...</div>
                            </div>
                            <div class="col-md-1 No-right">
                                <label for="number">Returnno</label>
                            </div>
                            <div class="col-md-2 No-left">
                                <div class="form-group">
                                    <input type="text" name="vehicle_return_id" id="vehicle_return_id" class="form-control vehicle_return_id" value="<?php echo $prefix.$_vehicle_return_number; ?>">
                                   
                                </div>
                            </div>
                    <input type="hidden" name="NextVRtnID" value="<?php echo $prefix.$_vehicle_return_number; ?>" id="NextVRtnID">  
                    <input type="hidden" name="row_count" value="0" id="row_count">
                    <input type="hidden" name="row_count_frRtn" value="0" id="row_count_frRtn">
                    <input type="hidden" name="row_count_pay" value="0" id="row_count_pay">
                    <input type="hidden" name="row_count_exp" value="0" id="row_count_exp">
                    <input type="hidden" name="ItemCount" value="0" id="ItemCount">
                    <?php if(isset($sale_return)){
                        ?>
                    <input type="hidden" name="updated_record" value=" " id="updated_record">
                    <input type="hidden" name="new_record" value=" " id="new_record">
                    <input type="hidden" name="vehRtnID" value="" id="vehRtnID">
                    <?php } ?>
                      
                        <div class="col-md-2">
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
                            <?php $stock_adj_date = (isset($stock_adj) ? _d($stock_adj->order_date) : $to_date);
                            echo render_date_input('from_date','',$stock_adj_date); ?>
                         
                        </div>
                         <!--<div class="col-md-1">
                          <span></span><a href="#" class="btn btn-warning edit-vehicle_return">View List</a>
                        </div>-->
                          
                    </div>
                    
                    <!-- 2 nd Row-->
                    <div class="row">
                        <div class="col-md-1 No-right">
                           <label for="number">ChallanNo</label>
                        </div>
                        <div class="col-md-2 No-left">
                           <div class="form-group ">
                                <input type="text" name="challan_n" placeholder="Challan No" id="challan_n" class="form-control "  value="">
                            </div>
                        </div>
                         <div class="col-md-2">
                             
                            <?php  $data_attr = array(
                                        "disabled" =>true
                                    );
                            echo render_date_input('to_date','','',$data_attr); ?>
                         
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1 No-right">
                            <label for="number">RouteCode</label>
                        </div>
                        <div class="col-md-2 No-left">
                            <div class="form-group">
                                <input type="text" readonly="" class="form-control" placeholder="Route code" name="route_code" id="route_code"  aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-3 ">
                            <div class="form-group">
                                <input type="text" readonly="" class="form-control" name="route_name" id="route_name"  aria-invalid="false">
                            </div>
                        </div>
                        <div class="col-md-1 ">
                            <div class="form-group">
                                <input type="text" readonly="" class="form-control" name="routekm" id="routekm"  aria-invalid="false">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1 No-right">
                            <label for="number">Vehicle</label>
                        </div>
                        <div class="col-md-2 No-left">
                         <div class="form-group">
                                <input type="text" readonly="" class="form-control" value="" name="vehicle_number" id="vehicle_number"  aria-invalid="false">
                            </div>
                    </div>
                    <div class="col-md-3 ">
                       
                    </div>
                    <div class="col-md-1 No-right">
                            <label for="number">Vehicle Cap.</label>
                    </div>
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                            <input type="text" readonly="" class="form-control" name="vehicle_capc" id="vehicle_capc"  aria-invalid="false">
                         </div>
                    </div>
                    <div class="col-md-1 No-right">
                            <label for="number">CashDeposit</label>
                    </div>
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                           <input type="text" readonly=""  class="form-control" name="case_depo1" id="case_depo1"  aria-invalid="false">
                            <input type="hidden" name="case_depo" id="case_depo" value="">
                        </div>
                    </div>
                </div> 
                   
              
                <div class="row">
                    <div class="col-md-1 No-right">
                            <label for="number">Driver</label>
                    </div>
                    <div class="col-md-2 No-left">
                        <input type="text" readonly="" class="form-control" name="driver_id" id="driver_id"  aria-invalid="false">
                    </div>
                    <div class="col-md-3 ">
                        <div class="form-group">
                            <!--<label for="estimate"></label>-->
                            <input type="text" readonly="" class="form-control" name="driver_name" id="driver_name"  aria-invalid="false">
                        </div>
                    </div>
                    <div class="col-md-1 No-right">
                            <label for="number">Challan Crates</label>
                    </div>
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                           <input type="text" readonly="" class="form-control"  name="challan_crates" id="challan_crates"  aria-invalid="false">
                           
                        </div>
                    </div>
                    
                    <div class="col-md-1 No-right">
                            <label for="number">Cheque Deposit</label>
                    </div>
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                           <input type="text" readonly="" class="form-control" name="check_depo" id="check_depo"  aria-invalid="false">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1 No-right">
                            <label for="number">Loader</label>
                    </div>
                    <div class="col-md-2 No-left">
                        <input type="text" readonly="" class="form-control" name="loder_id" id="loder_id"  aria-invalid="false">
                    </div>
                    <div class="col-md-3 ">
                        <div class="form-group">
                            <!--<label for="estimate"></label>-->
                            <input type="text" readonly="" class="form-control" name="loder_name" id="loder_name"  aria-invalid="false">
                        </div>
                    </div>
                    <div class="col-md-1 No-right">
                            <label for="number">ReturnedCrates</label>
                    </div>
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                           <input type="text" readonly="" class="form-control" name="refund_crates" id="refund_crates"  aria-invalid="false">
                           
                        </div>
                    </div>
                    <div class="col-md-1 No-right">
                        <label for="number">NEFT/Transfer</label>
                    </div>
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                           <input type="text" readonly="" class="form-control" name="NERT_trans" id="NERT_trans" aria-invalid="false">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1 No-right">
                        <label for="number">SalesMan</label>
                    </div>
                    <div class="col-md-2 No-left">
                        <input type="text" readonly="" class="form-control" name="salesman_id" id="salesman_id"  aria-invalid="false">
                    </div>
                    <div class="col-md-3 ">
                        <div class="form-group">
                            <input type="text" readonly="" class="form-control" name="salesman_name" id="salesman_name"  aria-invalid="false">
                            <input type="hidden"  class="form-control" name="UserIDName" id="UserIDName"  aria-invalid="false">
                        </div>
                    </div>
                    <div class="col-md-1 No-right">
                        <label for="number">Fresh RtnAmt</label>
                    </div>
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                           <input type="text"  readonly="" class="form-control" name="fresh_ret_amt1" id="fresh_ret_amt1"  aria-invalid="false">
                           <input type="hidden" name="fresh_ret_amt" id="fresh_ret_amt"  aria-invalid="false">
                        </div>
                    </div>
                    
                    <div class="col-md-1 No-right">
                        <label for="number">Total Expense</label>
                    </div>
                    
                    <div class="col-md-1 No-left">
                        <div class="form-group">
                           <input type="text" readonly="" class="form-control" name="total_expense1" id="total_expense1" aria-invalid="false">
                            <input type="hidden" name="total_expense" id="total_expense"  aria-invalid="false">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered print_tbl1" id="print_tbl1" width="80%" style="display:none">
                            <thead>
                                <tr>
                                    <th colspan="5">Collection</th>
                                </tr>
                                <tr>
                                    <th>SrNo</th>
                                    <th>Party Name</th>
                                    <th>Fresh Return</th>
                                    <th>Amount</th>
                                    <th>Crates</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                        
                        <table class="table table-striped table-bordered print_tbl2" id="print_tbl2" width="80%" style="display:none">
                            <thead>
                                <tr>
                                    <th colspan="3">Expense Details</th>
                                </tr>
                                <tr>
                                    <th>SrNo</th>
                                    <th>Account Name</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh3" style="display:none;">Please wait fetching data...</div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        
        <div class="panel-body mtop10">
            <div class="row col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#home" class="crate_details">Crate Details</a></li>
                    <li><a data-toggle="tab" href="#menu1" class="fresh_stock_return">Fresh Stock Return</a></li>
                    <li><a data-toggle="tab" href="#menu2" class="payment_reciept" >Payment Reciept</a></li>
                    <li><a data-toggle="tab" href="#menu3" class="expense_details" >Expense Details</a></li>
                </ul>
                <!-- crates Details -->
                <div class="" id="crate_details" >
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered crate_details_tbl" id="crate_details_tbl" width="100%">
                                <thead>
                                    <tr>
                                        <th>AccountID</th>
                                        <th>AccoutName</th>
                                        <th>Address</th>
                                        <th>OpeningCrates</th>
                                        <th>ChallanCrates</th>
                                        <th>RtnCrates</th>
                                        <th>BalanceCrates</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    <tr class="accounts" id="row">
                                        <td id="AccountIDTD" style="width: 125px;"><input type="text" name="AccountID" style="width: 125px;" id="AccountID"></td>
                                        <td style="padding:1px 5px !important;"><span id="party_name"></span><input type="hidden" name="party_name_val" id="party_name_val"></td>
                                        <td style="padding:1px 5px !important;"><span id="address"></span><input type="hidden" name="address_val" id="address_val" value="" ></td>
                                        <td style="padding:1px 5px !important;text-align: right;"><span id="opnCrates"></span><input type="hidden" name="opnCrates_val" id="opnCrates_val"></td>
                                        <td style="padding:1px 5px !important;text-align: right;"><span id="chlCrates"></span><input type="hidden" name="chlCrates_val" id="chlCrates_val"></td>
                                        <td class="rtnqty" style="width: 80px;">
                                            <input type="text" name="rtncrates" id="rtncrates" class="rtncrates" onblur="calculate_balcrates();" style="width: 80px;text-align: right;" >
                                            <input type="hidden" name="balcrates" id="balcrates" value="0">
                                            <input type="hidden" name="colno" id="colno" value="">
                                        </td>
                                        <td tyle="padding:1px 5px !important;text-align: right;"><span id="balCrates_new" style="text-align: right;"></span><input type="hidden" name="balCrates_new_val" id="balCrates_new_val" class="form-control"></td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> 
                <?php echo form_hidden('crate_details'); ?>
                
                <!-- Fresh Stock returns Details -->
                <div class="" id="fresh_stock_return" >
                    <div class="row">
                        <div class="col-md-12">
                            <div class="fixed_header1" id="fixed_header1">
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo form_hidden('fresh_stock_return'); ?>
                
                <!-- Payments Details -->
                <div class="" id="payment_reciept" >
                    <div class="row">
                        <div class="col-md-12">
                           
                                <table class="table table-striped table-bordered payment_details_tbl " id="payment_details_tbl" width="70%">
                                    <thead id="thead">
                                        <tr>
                                            <th style="width: 125px;">AccountID</th>
                                            <th>AccoutName</th>
                                            <th>Address</th>
                                            <th style="width: 80px;">ReceiptAmt</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        <tr class="accounts" id="row">
                                            <td id="AccountIDTD_pay" style="width: 125px;"><input type="text" name="AccountID_pay" style="width: 125px;" id="AccountID_pay" style="width: 125px;"></td>
                                            <td style="padding:1px 5px !important;"><span id="party_name_pay"></span><input type="hidden" name="party_name_pay_val" id="party_name_pay_val"></td>
                                            <td style="padding:1px 5px !important;"><span id="address_pay"></span><input type="hidden" name="address_pay_val" id="address_pay_val" value="" ></td>
                                            <td style="width: 80px;" class="rcptAmts"><input type="text" name="receiptamt" id="receiptamt" onblur="calculate_payment();"  style="width: 80px;text-align: right" onkeypress="return isNumber(event)" value="" ></td>
                                        </tr>
                                    </tbody>
                                </table>
                            
                        </div>
                    </div>
                </div>
                <?php echo form_hidden('payment_reciept'); ?>
                
                <!-- Expenses Details -->
                <div class="" id="expense_detail" >
                    
                    <table class="table table-striped table-bordered expense_details_tbl " id="expense_details_tbl" width="70%">
                        <thead id="thead">
                            <tr>
                                <th style="width: 125px;">AccountID</th>
                                <th>AccoutName</th>
                                <th>Address</th>
                                <th style="width: 80px;">ExpenseAmt</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <tr class="accounts" id="row">
                                <td id="AccountIDTD_exp" style="width: 125px;"><input type="text" name="AccountID_exp" id="AccountID_exp" style="width: 125px;"></td>
                                <td style="padding:1px 5px !important;"><span id="party_name_exp"></span><input type="hidden" name="party_name_exp_val" id="party_name_exp_val"></td>
                                <td style="padding:1px 5px !important;"><span id="address_exp"></span><input type="hidden" name="address_exp_val" id="address_exp_val" value="" ></td>
                                <td style="width: 80px;" class="expamts"><input type="text" name="expamt" id="expamt" value="" onblur="calculate_expense();"  style="width: 80px;text-align:right;" onkeypress="return isNumber(event);"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php echo form_hidden('expense_detail'); ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12 mtop15">
                <!--<div class="btn-bottom-toolbar text-right" style="width: 100%;">
                <?php
                if (has_permission_new('vehicle_return', '', 'create')) {  
                ?>
                  <button type="button"  class="btn-tr save_detail btn btn-info mleft10 estimate-form-submit transaction-submit">
                  <?php echo _l('submit'); ?>
                  </button>
                <?php } ?>
                </div>-->
                    <div class="btn-bottom-toolbar text-right" style="width: 100%;">
                        <?php if (has_permission_new('vehicle_return', '', 'create')) {
                        ?>
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                        <?php
                        }?>
                        
                        <?php if (has_permission_new('vehicle_return', '', 'edit')) {
                        ?>
                        <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                        <a class="btn btn-default printBtn" href="javascript:void(0);" onclick="printPage();">Print</a>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                        <a class="btn btn-default printBtn" href="javascript:void(0);" onclick="printPage();">Print</a>
                        <?php
                        }?>
                        <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
                    </div>
            </div>
               <div class="btn-bottom-pusher"></div>
          </div>
        </div>
        </div>

      </div>
      <?php //echo form_close(); ?>
      
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="transfer-modal">
   <div class="modal-dialog modal-xl" style=" max-width: 1230px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"> Challan List</h4>
         </div>
         <div class="modal-body" style="padding:5px;">
             
            <div class="row">
               <div class="col-md-2">
                    <div class="form-group" app-field-wrapper="from_date">
                        <label for="from_date" class="control-label">From</label>
                        
                        <div class="input-group date">
                            <input type="text" id="from_date1" name="from_date1" class="form-control datepicker" value="<?php echo $from_date;?>" >
                            <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group" app-field-wrapper="to_date">
                        <label for="to_date" class="control-label">To</label>
                         
                        <div class="input-group date">
                            <input type="text" id="to_date1" name="to_date1" class="form-control datepicker" value="<?php echo $to_date;?>">
                            <div class="input-group-addon"><i class="fa fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <br>
                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data">Search</button>
                </div>
                
                <div class="col-md-3">
                    <!--<br>
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">-->
                </div>
                
                <div class="col-md-12">
                 
            <div class="table_adj_report">
             
              <table class="tree table table-striped table-bordered table_adj_report" id="table_adj_report" width="100%">
                  
                <thead>
                    
                  <tr>
                             <th style="padding:0px 3px !important;">Challan No.</th>
                             <th style="padding:0px 3px !important;">Challan Date</th>
                             <th style="padding:0px 3px !important;">VehRtnId</th>
                             <th style=" text-align:center;">Route</th>
                             <th style=" text-align:center;">VehicleNo</th>
                             <th style=" text-align:center;">DriverName</th>
                             <th style=" text-align:center;">LoaderName</th>
                             <th style=" text-align:center;">SalemsmanName</th>
                             <th style=" text-align:center;">Crates</th>
                             <th style=" text-align:center;">Cases</th>
                             <th style=" text-align:center;">ChallanAmt</th>
                             <th style=" text-align:center;">OtherVehicleDetails</th>
                            
                          </tr>
                </thead>
                <tbody>
                <?php
                    if(count($chllist) >0 ){
                        foreach($chllist as $value){
                ?>
                        <tr class="get_challan_id" data-id="<?php echo $value["ChallanID"]; ?>">
                            <td style="padding:0px 3px !important;" ><?php echo $value["ChallanID"]; ?></td> 
                            <td style="padding:0px 3px !important;"><?php echo  _d(substr($value["Transdate"],0,10)); ?></td> 
                            <td></td> 
                            <td style="padding:0px 3px !important;"><?php echo $value["name"]; ?></td>
                            <td style="padding:0px 3px !important;"><?php echo $value["VehicleID"]; ?></td>
                            <td style="padding:0px 3px !important;"><?php echo $value["driver_fn"].' '.$value["driver_ln"]; ?></td> 
                            <td style="padding:0px 3px !important;"><?php echo $value["loader_fn"].' '.$value["loader_ln"]; ?></td> 
                            <td style="padding:0px 3px !important;"><?php echo $value["Salesman_fn"].' '.$value["Salesman_ln"]; ?></td> 
                            <td style="padding:0px 3px !important;text-align:right;"><?php echo $value["Crates"]; ?></td> 
                            <td style="padding:0px 3px !important;text-align:right;"><?php echo $value["Cases"]; ?></td>
                            <td style="padding:0px 3px !important;text-align:right;"><?php echo $value["ChallanAmt"]; ?></td> 
                            <td style="padding:0px 3px !important;"><?php echo $value["OtherVehicleDetails"]; ?></td>
                            </tr>
                    <?php
                       } 
                        }else{
                    ?>
                           <tr>
                            <td colspan="12"><span style="color:red;">No data found..</span></td>
                            </tr>
                    <?php } ?>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
                    
                </div>
              </div>
         </div>
         
         <div class="modal-footer" style="padding:0px;">
            <input type="text" id="myInput1"  autofocus="1" name='myInput1' onkeyup="myFunction2()" placeholder="Search for names.."  style="float: left;width: 100%;">
        </div>
        
         
      </div>
   </div>
</div>
<div class="modal fade" id="transfer-modal_return_list">
   <div class="modal-dialog modal-xl" style=" max-width: 1230px;">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Vehicle Return List</h4>
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
                <div class="col-md-2">
                    <?php
                   echo render_date_input('from_date2','From',$from_date);
                   ?>
                </div>
                <div class="col-md-2">
                    <?php
                   echo render_date_input('to_date2','To',$to_date);
                   ?>
                </div>
                <div class="col-md-3">
                    <br>
                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data_vehicle_return"><?php echo _l('rate_filter'); ?></button>
                </div>
                <div class="col-md-3">
                    <!--<br>
                    <input type="text" id="myInput2" onkeyup="myFunction3()" placeholder="Search for names.." title="Type in a name" style="float: right;">-->
                </div>
                <div class="col-md-12">
                <span id="searchh11" style="display:none;">Please wait fetching data...</span>
            <div class="table_vehicle_return">
             
              <table class="tree table table-striped table-bordered table_vehicle_return" id="table_vehicle_return" width="100%">
                  
                <thead>
                    <tr style="display:none;">
                      <td colspan="8" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                    </tr>
                    <tr >
                        <th style="padding:0px 3px !important;">ReturnNo.</th>
                        <th style="padding:0px 3px !important;">ReturnDate</th>
                        <th style="padding:0px 3px !important;">ChallanID.</th>
                        <th style="padding:0px 3px !important;">ChallanDate</th>
                        <th style="padding:0px 3px !important;">VehRtnId</th>
                        <th style=" text-align:center;">Route</th>
                        <th style=" text-align:center;">DriverName</th>
                        <th style=" text-align:center;">LoaderName</th>
                        <th style=" text-align:center;">SalemsmanName</th>
                        <th style=" text-align:center;">Crates</th>
                        <th style=" text-align:center;">Cases</th>
                        <th style=" text-align:center;">ChallanAmt</th>
                        <th style=" text-align:center;">OtherVehicleDetails</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            if(count($vRtnlist) > 0 ){
            
        
         foreach($vRtnlist as $value){
          
            $url = admin_url().'Vehicle_return/vehicle_return_list/'.$value["ReturnID"];
        ?>
        <tr class="get_VehicleRtnID" data-id="<?php echo $value["ReturnID"]; ?>">
            <td style="padding:0px 3px !important;"><?php echo $value["ReturnID"]; ?></td>
            <td style="padding:0px 3px !important;"><?php echo  _d(substr($value["returnTransdate"],0,10)); ?></td>
            <td style="padding:0px 3px !important;"><?php echo $value["ChallanID"]; ?></td>
            <td style="padding:0px 3px !important;"><?php echo  _d(substr($value["Transdate"],0,10)); ?></td>
            <td></td>
            <td style="padding:0px 3px !important;"><?php echo $value["name"]; ?></td>
            <td style="padding:0px 3px !important;"><?php echo $value["driver_fn"].' '.$value["driver_ln"]; ?></td>
            <td style="padding:0px 3px !important;"><?php echo $value["loader_fn"].' '.$value["loader_ln"]; ?></td> 
            <td style="padding:0px 3px !important;"><?php echo $value["Salesman_fn"].' '.$value["Salesman_ln"]; ?></td>
            <td style="padding:0px 3px !important;text-align:right;"><?php echo $value["Crates"]; ?></td>
            <td style="padding:0px 3px !important;text-align:right;"><?php echo $value["Cases"]; ?></td>
            <td style="padding:0px 3px !important;text-align:right;"><?php echo $value["ChallanAmt"]; ?></td> 
            <td style="padding:0px 3px !important;"><?php echo $value["OtherVehicleDetails"]; ?></td>
        </tr>
    <?php
       } 
        }else{
    ?>
        <tr>
            <td colspan="13"><span style="color:red;">No data found..</span></td>
        </tr>
    <?php
    }
    ?>
                </tbody>
              </table>   
            </div>
            <span id="searchh3" style="display:none;">Loading.....</span>
                    
                </div>
              </div>
         </div>
        <div class="modal-footer" style="padding:0px;">
            <input type="text" id="myInput2"  autofocus="1" name='myInput2' onkeyup="myFunction3()" placeholder="Search for names.."  style="float: left;width: 100%;">
        </div>
         
      </div>
   </div>
</div>
<style>
.table_adj_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table_adj_report thead th { position: sticky; top: 0; z-index: 1; }
.table_adj_report tbody th { position: sticky; left: 0; }

.No-left {
    padding-left:0px;
}
.No-right {
    padding-right:0px;
}
#table_adj_report tr:hover {
    background-color: #ccc;
}

#table_adj_report td:hover {
    cursor: pointer;
}

.table_vehicle_return { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table_vehicle_return thead th { position: sticky; top: 0; z-index: 1; }
.table_vehicle_return tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
table  { border-collapse: collapse; width: 100%; }
th, td { padding: 3px 3px !important; white-space: nowrap;font-size:11px; line-height:1.42857143;vertical-align: middle;}
th     { background: #50607b;color: #fff !important; }

.fixed_header1 { overflow: auto;max-height: 50vh;width:100%;position:relative;top: 0px; }
.fixed_header1 thead th { position: sticky; top: 0; z-index: 1; }
.fixed_header1 tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.fixed_header1 table  { border-collapse: collapse; width: 100%; }
th, td { padding: 0px 0px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
.fixed_header1 th     { background: #50607b;color: #fff !important; }


#table_vehicle_return tr:hover {
    background-color: #ccc;
}

#table_vehicle_return td:hover {
    cursor: pointer;
}
</style>

<?php init_tail(); ?>

</body>
<style>
    table.dataTable tbody td {
    padding: 4px 4px !important;
    font-size: 11px;
}
</style>
</html>

<?php $this->load->view('admin/VehRtn/ManageJS'); ?>
<script type="text/javascript" language="javascript" >


function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_adj_report");
  tr = table.getElementsByTagName("tr");
for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    td1 = tr[i].getElementsByTagName("td")[1];
    td2 = tr[i].getElementsByTagName("td")[2];
    td3 = tr[i].getElementsByTagName("td")[3];
    td4 = tr[i].getElementsByTagName("td")[4];
    td5 = tr[i].getElementsByTagName("td")[5];
    td6 = tr[i].getElementsByTagName("td")[6];
    td7 = tr[i].getElementsByTagName("td")[7];
    td8 = tr[i].getElementsByTagName("td")[8];
    td9 = tr[i].getElementsByTagName("td")[9];
    td10 = tr[i].getElementsByTagName("td")[10];
    td11 = tr[i].getElementsByTagName("td")[11];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td1){
         txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td2){
         txtValue = td2.textContent || td2.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td3){
         txtValue = td3.textContent || td3.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td4){
         txtValue = td4.textContent || td4.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td5){
         txtValue = td5.textContent || td5.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td6){
         txtValue = td6.textContent || td6.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td7){
         txtValue = td7.textContent || td7.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td8){
         txtValue = td8.textContent || td8.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td9){
         txtValue = td9.textContent || td9.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td10){
         txtValue = td10.textContent || td10.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td11){
         txtValue = td10.textContent || td11.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else{
           tr[i].style.display = "none";
      } 
    }
    }}
    }
    }     
  }
}
}
}
}
}}
}
}
</script>

<script type="text/javascript" language="javascript" >

function myFunction3() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput2");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_vehicle_return");
  tr = table.getElementsByTagName("tr");
for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    td1 = tr[i].getElementsByTagName("td")[1];
    td2 = tr[i].getElementsByTagName("td")[2];
    td3 = tr[i].getElementsByTagName("td")[3];
    td4 = tr[i].getElementsByTagName("td")[4];
    td5 = tr[i].getElementsByTagName("td")[5];
    td6 = tr[i].getElementsByTagName("td")[6];
    td7 = tr[i].getElementsByTagName("td")[7];
    td8 = tr[i].getElementsByTagName("td")[8];
    td9 = tr[i].getElementsByTagName("td")[9];
    td10 = tr[i].getElementsByTagName("td")[10];
    td11 = tr[i].getElementsByTagName("td")[11];
    td12 = tr[i].getElementsByTagName("td")[12];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td1){
         txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td2){
         txtValue = td2.textContent || td2.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td3){
         txtValue = td3.textContent || td3.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else if(td4){
         txtValue = td4.textContent || td4.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td5){
         txtValue = td5.textContent || td5.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td6){
         txtValue = td6.textContent || td6.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td7){
         txtValue = td7.textContent || td7.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td8){
         txtValue = td8.textContent || td8.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td9){
         txtValue = td9.textContent || td9.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td10){
         txtValue = td10.textContent || td10.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td11){
         txtValue = td11.textContent || td11.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td12){
         txtValue = td12.textContent || td12.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else{
           tr[i].style.display = "none";
      } 
    }
    }}}
    }
    }     
  }
}
}
}
}
}}
}
}
</script>
 <script>
    $(document).ready(function(){
        $("#group_code").dblclick(function(){
            $('#AccountGroup').modal('show');
            $('#AccountGroup').on('shown.bs.modal', function () {
                  $('#myInput1').focus();
            })
        });
        
        
    
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
    $('#from_date1').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    $('#to_date1').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false,
        showOtherMonths: false,
        pickTime: false,
            orientation: "left",
    });
    
    $('#from_date2').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false
    });
    
    $('#to_date2').datetimepicker({
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

