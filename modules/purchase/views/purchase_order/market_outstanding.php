<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'pur_order-form','class'=>'_transaction_form'));
			
			?>
			<div class="col-md-12">
        <div class="panel_s accounting-template estimate">
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
                <!--<div class="row">-->
                
                       
                       
                     
                      <!--</div>-->
                       <div class="row">
                       <div class="col-md-6">
                             <div class="row">
                           <div class="col-md-6 ">
                              <div class="form-group">
                        <label for="vendor"><?php echo _l('Account'); ?></label>
                        <select name="vendor" id="vendor" class="selectpicker" onchange="estimate_by_vendor(this); return false;" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value=""></option>
                        <?php foreach($vendors as $s) { ?>
                        <option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if(isset($pur_order) && $pur_order->vendor == $s['userid']){ echo 'selected'; }else{ if(isset($ven) && $ven == $s['userid']){ echo 'selected';} } ?>><?php echo html_entity_decode($s['AccountID']); ?></option>
                        <?php } ?>
                        </select>              
                        </div>
                          
                              </div>

                   <div class="col-md-6 ">
                         <div class="form-group">
                            <label for="estimate"></label>
                           <input type="text" readonly="" class="form-control" name="c_name" id="c_name"  aria-invalid="false">
                           
                          </div>
                         
                      </div>
                      </div>
                      <div class="row">
                           <div class="col-md-6 ">
                         <div class="form-group">
                            <label for="estimate">Route Id</label>
                            <select name="route_id" id="route_id" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                         <<option></option>
                            <?php foreach($route as $s) { ?>
                        <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && $pur_order->vendor == $s['userid']){ echo 'selected'; }else{ if(isset($ven) && $ven == $s['userid']){ echo 'selected';} } ?>><?php echo html_entity_decode($s['name']); ?></option>
                        <?php } ?>
                        </select>
                          </div>
                         
                      </div>
                       <div class="col-md-6 ">
                         <div class="form-group">
                            <label for="estimate"></label>
                           <input type="text"  class="form-control" name=" " id=" " value="All Route" aria-invalid="false">
                           
                          </div>
                         
                      </div> 
                          </div>
                               <div class="row">
                           <div class="col-md-6 ">
                         <div class="form-group">
                            
                            <?php $order_date = (isset($pur_order) ? _d($pur_order->order_date) : _d(date('Y-m-d')));
                        echo render_date_input('as_on','As on',$order_date); ?>
                          </div>
                         
                      </div>
                       <div class="col-md-6 ">
                         <div class="form-group station_hide_show"> 
                             <label for="estimate"></label>
                            <select name="station" id="station" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value="">Select Station </option>
                        <?php foreach($station as $s) { ?>
                        <option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if(isset($pur_order) && $pur_order->vendor == $s['userid']){ echo 'selected'; }else{ if(isset($ven) && $ven == $s['userid']){ echo 'selected';} } ?>><?php echo html_entity_decode($s['StationName']); ?></option>
                        <?php } ?>
                        </select>    
                          </div>
                         
                      </div>
                          </div>
                                   <div class="row">
                           <div class="col-md-6 ">
                          <?php echo render_select( 'states',$states,array( 'short_name',array( 'state_name')), 'client_state',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex'))); ?>
                         
                      </div>
                       <div class="col-md-6 ">
                         <div class="form-group">
                             <label for="estimate">VP</label>
                            <select name="VP" id="VP" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value=""></option>
                                <option value="vp">VP</option>
                                <option value="dgm">DGM</option>
                                <option value="4">RSM</option>
                                <option value="5">ASM</option>
                                <option value="6">ASE</option>
                                <option value="7">TSI</option>
                                <option value="8">SO</option>

                        </select>    
                          </div>
                         
                      </div>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="row">
                        <div class="col-md-4 ">
                            <label for="estimate">Report Type</label>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault1" id="flexRadioDefault1">
                              <label class="form-check-label" for="flexRadioDefault1">
                                Parent Controlled - Consolidated
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault1" id="flexRadioDefault2" >
                              <label class="form-check-label" for="flexRadioDefault2">
                                Individual Identity - Consolidated
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault1" id="flexRadioDefault2" checked>
                              <label class="form-check-label" for="flexRadioDefault2">
                                Detailed - Bill Wise
                              </label>
                            </div>
                         <!--<div class="form-group">-->
                         <!--   <label for="estimate"></label>-->
                         <!--  <input type="text" readonly="" class="form-control" name="gst_num" id="gst_num"  aria-invalid="false">-->
                           
                         <!-- </div>-->
                         
                      </div>
                       <div class="col-md-4 ">
                         <label for="estimate">Sales Type</label>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" checked>
                              <label class="form-check-label" for="flexRadioDefault1">
                                All
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" >
                              <label class="form-check-label" for="flexRadioDefault2">
                                Tax
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" >
                              <label class="form-check-label" for="flexRadioDefault2">
                               BOS
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" >
                              <label class="form-check-label" for="flexRadioDefault2">
                              Depot Transfer
                              </label>
                            </div>
                         
                      </div>
                       <div class="col-md-4">
                              <div class="form-group">
                            <label for="estimate">Item Division</label>
                             <select name="itemdivision" id="itemdivision" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value="">All Item Division</option>
                        <?php //foreach($vendors as $s) { ?>
                        <!--<option value="<?php echo html_entity_decode($s['userid']); ?>" <?php if(isset($pur_order) && $pur_order->vendor == $s['userid']){ echo 'selected'; }else{ if(isset($ven) && $ven == $s['userid']){ echo 'selected';} } ?>><?php echo html_entity_decode($s['company']); ?></option>-->
                        <?php //} ?>
                        </select> 
                        <input type="text"  class="form-control" name="invoce_n" id="invoce_n" value="Find MaxCredit"  aria-invalid="false">
                           
                          </div>
                          
                </div>
                </div>
                 <div class="row">
                           <div class="col-md-4 ">
                         <div class="form-group">
                             <label for="estimate">Loc Type</label>
                               <select name="loc_type" id="loc_type" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true">
                                <option value="1">Local</option>
                                <option value="2">OutStation</option>
                                <option value="3" selected>NotDefined</option>
                            </select>
   
                          </div>
                         
                      </div>
                       <div class="col-md-4 ">
                         <div class="form-group">
                             <label for="estimate"></label>
                            <select name="station_name" id="station_name" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value="station_name">Station Name</option>
                        <option value="account_name">Account Name</option>
                        
                        </select>     
                          </div>
                         
                      </div>
                      <div class="col-md-4 ">
                         <div class="form-group">
                             <label for="estimate"></label>
                            <select name="show_pending" id="show_pending" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value="pending">Show Pending Bill</option>
                        <option value="all">Show All Bill</option>

                        </select>    
                          </div>
                         
                      </div>
                          </div>
                          
                          <div class="row">
                           <div class="col-md-3 ">
                         <div class="form-group">
                             <label for="estimate"></label>
                             <select name="loc_type" id="loc_type" class="selectpicker"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>" >
                        <option value="select_all">Selected All</option>
                        <?php foreach($groups as $s) { ?>
                        <option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(isset($pur_order) && $pur_order->vendor == $s['userid']){ echo 'selected'; }else{ if(isset($ven) && $ven == $s['userid']){ echo 'selected';} } ?>><?php echo html_entity_decode($s['name']); ?></option>
                        <?php } ?>
                        

                        </select>    
                          </div>
                         
                      </div>
                       <div class="col-md-3 ">
                         <div class="form-group">
                             <label for="estimate"></label>
                            <a href="#" class="btn btn-primary show" id="show">Show</a>   
                          </div>
                         
                      </div>
                      <div class="col-md-3 ">
                         <div class="form-group">
                             <label for="estimate"></label>
                            <a href="#" class="btn btn-primary show" id="print">Print</a>   
                          </div>
                         
                      </div>
                      <div class="col-md-3 ">
                         <div class="form-group">
                             <label for="estimate"></label>
                            <a href="#" class="btn btn-primary show" id="excel">Excel</a>   
                          </div>
                         
                      </div>
                          </div>
                </div>

                    </div>
                 
              
                   
                

              </div>
            </div>
        </div>
        <div class="panel-body mtop10">
        <!--<div class="row col-md-12">-->
            <div class="row">
                <div class="col-md-12">
                    <?php 
                    //print_r($sale_returns);
                    ?>
                    <table class="table table-striped table-bordered" id="table_id" style="font-size:12px;" width="100%">
                       <thead>
                          <tr>
                             <th></th>
                             <th>SalesId</th>
                             <th>TransDate</th>
                             <th >AccountID</th>
                             <th>AccountName</th>
                             <th>Station</th>
                             <th>StateID</th>
                             <th>BillAMT</th>
                             <th>ReceiptAmt</th>
                             <th>BalanceAMT</th>
                             <th>EId</th>
                          </tr>
                       </thead>
                       <tbody>
                           <?php
                           foreach ($Order_list as $key => $value) {
                           ?>
                           <tr>
                                 <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                           
                           </tr>
                           <?php
                           }
                           ?>
                       </tbody>
                    </table>
                </div>
              </div>
       
        <hr class="hr_style"/>
         <div class="" id="example_id">
         </div>
         
</div>
</div>
</div>
</div>
</div>
</div>
<style>
/*    @media (min-width: 768px)*/ 
/*        .modal-xl {*/
/*    width: 90%;*/
/*    max-width: 1230px;*/
/*}*/
</style>

<?php init_tail(); ?>
</body>
</html>
<?php require 'modules/purchase/assets/js/pur_order_js.php';?>
<script>
 $('#station_name').on('change', function(){
   if(this.value == 'account_name'){
      $('.station_hide_show').hide();  
   }else{
        $('.station_hide_show').show();
   }
     
      //init_journal_entry_table();
    });
$( "#vendor" ).change(function() {
   if(this.value != 0){
    $.post(admin_url + 'purchase/get_vendor_data/'+this.value).done(function(response){
       
       
      response = JSON.parse(response);
     
      $("#c_name").val(response.vendor.company);
      
    });
   }
});

    $(document)
  .ready(function () {
    $('#table_id')
      .DataTable({
        "order": [[ 0, "desc" ],[ 2, "false" ]]
    });
  });
</script>
<!-- Script -->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
    
    <!-- jQuery UI -->
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    
 <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
