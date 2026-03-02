<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    /* Table header styling - image  */
    .invoice-items-table thead tr th {
        background-color: #47566d !important;
        color: #ffffff !important;
        padding: 12px 8px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        border: 1px solid #3d4d5f !important;
        vertical-align: middle !important;
        text-align: center !important;
    }
    
    /* Table body styling */
    .invoice-items-table tbody tr td {
        padding: 8px !important;
        vertical-align: middle !important;
        border: 1px solid #ddd !important;
    }
    
    /* Table full width */
    .invoice-items-table {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 15px;
    }
    
    /* Input fields in table */
    .invoice-items-table input.form-control,
    .invoice-items-table select.form-control {
        height: 32px !important;
        padding: 5px 8px !important;
        font-size: 13px !important;
    }
    
    /* Specific column widths - image  */
    .invoice-items-table thead th:nth-child(1) { width: 3%; } /* Dragger */
    .invoice-items-table thead th:nth-child(2) { width: 8%; } /* ItemID */
    .invoice-items-table thead th:nth-child(3) { width: 18%; } /* Item Name */
    .invoice-items-table thead th:nth-child(4) { width: 5%; } /* CS/CR */
    .invoice-items-table thead th:nth-child(5) { width: 5%; } /* Pkg */
    .invoice-items-table thead th:nth-child(6) { width: 7%; } /* Order Qty */
    .invoice-items-table thead th:nth-child(7) { width: 7%; } /* Qty(CS/CR) */
    .invoice-items-table thead th:nth-child(8) { width: 7%; } /* Rate */
    .invoice-items-table thead th:nth-child(9) { width: 6%; } /* Disc */
    .invoice-items-table thead th:nth-child(10) { width: 7%; } /* Disc Amt */
    .invoice-items-table thead th:nth-child(11) { width: 8%; } /* GST */
    .invoice-items-table thead th:nth-child(12) { width: 10%; } /* Amount */
    .invoice-items-table thead th:nth-child(13) { width: 4%; } /* Settings */
    
    ._transaction_form .table.items thead>tr>th {
        min-width: 70px;
    }
</style>


<div class="panel_s invoice accounting-template">

	<div class="additional"></div>

	<div class="panel-body">

	<nav aria-label="breadcrumb">

                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">

                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>

                    					<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>

                    					<li class="breadcrumb-item active" aria-current="page"><b>Order</b></li>

                    				</ol>

                                </nav>

                                <hr class="hr_style">

		<?php

			/*echo $order->OrderType;

				echo "<br>";

				print_r($order);

				

			die;*/

			$OrderType = '';
			$drcr = '';
			$TransDate = '';
			$BillAmt = '';
			$depTransDate = '';
			$depositAmt = '';
			$ShippingState = '';
			$ShippingCity = '';
			$isedit = isset($order);
			$data_original_number = '';
			$_is_draft = false;

			if(isset($order)){

				$OrderType = $order->OrderType;

				if($order->accbal > 0){

					$drcr = "Dr";  

					}else{

					$drcr = "Cr";

				}

				$amtvalue = 0;

				

				$TransDate = '';

				$BillAmt = '';

				foreach ($order->last_billed_on as $key1 => $last_billed_on) {

					if($key1 == "Transdate"){

						$TransDate = _d(substr($last_billed_on,0,10));

					}

					if($key1 == "BillAmt"){

						$BillAmt = $last_billed_on;

					}

				}

				$depTransDate = '';

				$depositAmt = '';

				foreach ($order->last_deposit_on as $key2 => $last_deposit_on) {

					if($key2 == "Transdate"){

						$depTransDate = _d(substr($last_deposit_on,0,10));

					}

					if($key2 == "Amount"){

						$depositAmt = $last_deposit_on;

					}

				}

				

			}

			

			//echo $amtvalue;

		?>

		<div class="row">

			<div class="col-md-6">

				<input type="hidden" value="<?php echo date('y'); ?>" name="years" class="years" id="years">

				<?php

					$selected_company = $this->session->userdata('root_company');

					$fy = $this->session->userdata('finacial_year');

					if($selected_company == 1){

						$next_order_number = get_option('next_order_number_for_gf');

						}/*elseif($selected_company == 2){

						$next_order_number = get_option('next_order_number_for_cff');

						}elseif($selected_company == 3){

						$next_order_number = get_option('next_order_number_for_cbu');

						}elseif($selected_company == 4){

						$next_order_number = get_option('next_order_number_for_cbupl');

					}*/

					

					$prefix = "ORD".$fy;

					

					if(isset($order)){

						$next_order_number = substr($order->OrderID,5);

					}

					if(isset($order)){

						$view = "disabled";

						}else{

						$view = "";

					}

					$_invoice_number = str_pad($next_order_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
					if($isedit){
						$data_original_number = $_invoice_number;
					}

				?>

				<div class="col-md-3">

					<div class="form-group">

                        <label for="number">OrderID</label>

                        <div class="input-group"><span class="input-group-addon"><?php echo $prefix;?></span>

                            <input type="text" id="ordnumber" name="number1" class="form-control number1" value="<?php echo $_invoice_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo $view; ?>>

						</div>

					</div>

				</div>

				

				<div class="col-md-3">

					<?php

						if(isset($order)){

						?>

                        <input type="hidden" name="OrderID" id="OrderID" value="<?php echo $order->OrderID; ?>">

                        <input type="hidden" name="PlantID" id="PlantID" value="<?php echo $this->session->userdata('root_company'); ?>">

                        <input type="hidden" name="SalesID" id="SalesID" value="<?php echo $order->SaleDetails->SalesID; ?>">

                        <input type="hidden" name="SalesDate" id="SalesDate" value="<?php echo $order->SaleDetails->Transdate; ?>">

                        <input type="hidden" name="ChallanID" id="ChallanID" value="<?php echo $order->ChallanDetails->ChallanID; ?>">

						<?php

							}else{

						?>

						<input type="hidden" name="OrderID" id="OrderID" value="">

						<?php

						}

					?>

					<input type="hidden" name="number" class="form-control" value="<?php echo ($_is_draft) ? 'DRAFT' : $_invoice_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" <?php echo ($_is_draft) ? 'disabled' : '' ?>>

					<?php

						$fy = $this->session->userdata('finacial_year');

						$fy_new  = $fy + 1;

						$lastdate_date = '20'.$fy_new.'-03-31';

						$curr_date = date('Y-m-d');

						$curr_date_new    = new DateTime($curr_date);

						$last_date_yr = new DateTime($lastdate_date);

						if($last_date_yr < $curr_date_new){

							$date = $lastdate_date;

							}else{

							$date = date('Y-m-d');

						}

					?>

					<?php $value = (isset($order) ? _d(substr($order->Transdate,0,10)) : _d($date));

						$date_attrs = array();

						if(isset($order)){

							$date_attrs['disabled'] = true;

						}

					?>

					<?php echo render_date_input('date1','invoice_add_edit_date',$value,$date_attrs); ?>

					<input type="hidden" name="transdate" value="<?= $value?>">

					

				</div>

                

                <div class="col-md-3">

                    <label class="control-label">Buyer's Order No</label>

                    <?php $buyer_ord_no = (isset($order) ? $order->buyer_ord_no : ''); ?>

                    <input type="text" class="form-control" name="buyer_ord_no" id="buyer_ord_no" value="<?php echo $buyer_ord_no; ?>" style="text-transform:uppercase">

				</div>

                <div class="col-md-3">

                    <label class="control-label">Buyer's Order Date</label>

                    <?php $buyer_ord_date = (isset($order) ? _d(substr($order->buyer_ord_date,0,10)) : ''); ?>

				    <?php echo render_date_input('buyer_ord_date','',$buyer_ord_date); ?>

				</div>

				

                <div class="col-md-12">

                    <div class="f_client_id1">

                        <div class="form-group select-placeholder">

							<label for="clientid" class="control-label"><?php echo _l('Customer'); ?></label>

							<select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($order) && empty($order->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php echo $view; ?>>

								<?php $selected = (isset($order) ? $order->AccountID : '');

									if($selected == ''){

										$selected = (isset($customer_id) ? $customer_id: '');

									}

									if($selected != ''){

										$rel_data = get_relation_data('customer',$selected);

										$rel_val = get_relation_values($rel_data,'customer');

										echo '<option value="'.$rel_val['AccountID'].'" selected>'.$rel_val['name'].'</option>';

									} ?>

							</select>

						</div>

					</div>

				</div>

                

                <div class="col-md-12">

                    <div class="row">

                        

                        <?php 

                            $istcs = (isset($client_detail) && isset($client_detail->istcs)) ? $client_detail->istcs : '';
                            if($istcs == "1"){
                                $tcs_per = (isset($tcs) && isset($tcs[0]['tcs'])) ? $tcs[0]['tcs'] : '';
			
								}else{

                                $tcs_per = " ";

							}

						?>

						<input type="hidden" name="cust_id" value="<?php echo isset($order) ? $order->AccountID : ''; ?>">

						<input type="hidden" class="form-control" name="istcsper" id="istcsper" value="<?php echo $tcs_per; ?>">

						<input type="hidden" class="form-control" name="istcs" id="istcs" value="<?php echo isset($client_detail) ? $client_detail->istcs : ''; ?>">

						<input type="hidden" class="form-control" name="location_typevalue" id="location_typevalue" value="<?php echo isset($location_type) ? $location_type : ''; ?>">

						<input type="hidden" class="form-control" name="item_divistion" id="item_divistion" value='<?php echo isset($custitemdiv_ids) ? $custitemdiv_ids : ''; ?>'>

						<input type="hidden" class="form-control" name="dist_comp" id="dist_comp" value='<?php echo isset($client_detail) ? $client_detail->company_assigned : ''; ?>'>

						<input type="hidden" class="form-control" name="dist_sale_agent" id="dist_sale_agent" value='<?php echo isset($client_detail) ? $client_detail->company_assigned_staff : ''; ?>'>

						<input type="hidden" class="form-control" name="dist_route" id="dist_route" value='<?php echo isset($client_detail) ? $client_detail->routes : ''; ?>' >

						<input type="hidden" class="form-control" name="dist_tcs" id="dist_tcs" value="" >

						<input type="hidden" class="form-control" name="act_gst" id="act_gst" value="<?php echo isset($client_detail) ? $client_detail->vat : ''; ?>" >

						<input type="hidden" class="form-control" name="ship_to_act_gst" id="ship_to_act_gst" value="<?php echo isset($order) ? $order->Gstin2 : ''; ?>" >

						<input type="hidden" name="customer_group_id" id="customer_group_id" value="<?php echo isset($client_detail) ? $client_detail->DistributorType : ''; ?>" >

						<input type="hidden" name="customer_state_id" id="customer_state_id" value="<?php echo isset($client_detail) ? $client_detail->state : '';?>" >

                        <div class="col-md-4">

                            <?php

                                $LocationTypeName = '';

                                if(isset($order)){

                                    if($client_detail->LocationTypeID == "1"){

                                        $LocationTypeName = "Local";

										}elseif($client_detail->LocationTypeID == "2"){

                                        $LocationTypeName = "OutStation";

										}else{

                                        $LocationTypeName = "Not Defined";

									}

								}

							?>

                            <div class="form-group">

                                <label class="control-lable" >Location Type</label>

                                <input type="text" class="form-control" name="location_type" id="location_type" value="<?php echo $LocationTypeName; ?>" disabled>

							</div>

						</div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="billing_state" class="control-lable">Billing State</label>

                               <input type="text" class="form-control" name="billing_state" id="billing_state" value="<?php echo isset($client_detail) ? $client_detail->state_name : ''; ?>" disabled>
							</div>

						</div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="billing_city" class="control-lable">Billing City</label>

                                <input type="text" class="form-control" name="billing_city" id="billing_city" value="<?php echo isset($order) ? $order->client->city_name : ''; ?>" disabled>

							</div>

						</div>

                        
                        <div class="col-md-5">

                            <div class="form-group">

                                <label for="ShipToParty" class="control-lable">Ship To Party</label>

                                <select name="ShipToParty" id="ShipToParty" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true">

    								<option value="">None selected</option>

    								<?php

    								    foreach($PartyList as $key=>$val){

										?>

    								  			    <option value="<?php echo $val["AccountID"];?>" <?php if(isset($order) && isset($order->AccountID2) && $order->AccountID2 == $val["AccountID"]) { echo "selected";}?>><?php echo $val["company"];?></option>

										<?php

										}

									?>

								</select>

							</div>

						</div>

                        <div class="col-md-7">

                            <div class="form-group">

                                <label for="Ship_to" class="control-lable">Ship To Address</label>

                                <select name="Ship_to" id="Ship_to" class="selectpicker form-control" data-none-selected-text="None selected" data-live-search="true">

    								<!--<option value="">None selected</option>-->

    								<?php

    								    if(isset($order)){

    								        $value = $order->ShipTo;

    								        foreach($ShippingDetails as $key=>$val){

    								            if($value == $val["id"]){

    								                $ShippingState = $val["state_name"];

    								                $ShippingCity = $val["city_name"];

												}

												$leble = $val["ShippingAdrees"]." " .$val["city_name"]." (".$val["ShippingState"].") - ".$val["ShippingPin"];

											?>

											<option value="<?php echo $val["id"];?>" <?php if($value == $val["id"]) { echo "selected";}?>><?php echo $leble;?></option>

											<?php

											}

										}

									?>

								</select>

							</div>

						</div>

                        <div class="col-md-4">

							

                            <?php $value = (isset($order) ? _d(substr($order->Dispatchdate,0,16)) : date('d/m/Y H:m'));

							echo render_datetime_input('DispatchDate','Expected Dispatch Date',$value); ?>

						</div>

                        

                        

                        <div class="col-md-8">

                            <br>

                            <p style="color:#3826d3 !important;font-size: 16px;font-weight: 600;" class="item_added_msg" id="item_added_msg">

                                <?php

									if(isset($order)){

										if($order->OrderType == "TaxItems"){

											echo  "We can add only Taxable items..";

											}else{

											echo "We can add only Non Taxable items..";

										}

									}

								?>

							</p>

                            <?php

								

                                if(isset($order)){

                                    if($order->ChallanDetails->Gatepassuserid !== NULL){

									?>

									<p style="color:red !important;font-size: 16px;font-weight: 600;">GatePass has been Generated for this order..</p>

                                    <?php

									}

                                    if($order->SaleDetails->irn !== NULL){

									?>

									<p style="color:red !important;font-size: 16px;font-weight: 600;">E-invoice has been Generated for this order..</p>

                                    <?php

									}

								}

							?>

						</div>

                        

					</div>

				</div>

				

				

			</div>

			

			<div class="col-md-6">

				<div class="row">

					<div class="col-md-3">

						<div class="form-group">

							<label for="MaxCrdAmt" class="control-label">Credit Limit</label>  

                               <input type="text" class="form-control" name="MaxCrdAmt" id="MaxCrdAmt" value="<?php echo isset($client_detail) ? number_format($client_detail->MaxCrdAmt,2) : '0.00'; ?>" disabled>

						</div>

					</div>

					<div class="col-md-3">

						<div class="form-group">

							<label for="act_bal" class="control-label">A/C Balance</label>

							<input type="text" class="form-control" name="acct_bal" id="acct_bal" value="<?php echo isset($order) ? number_format($order->accbal,2)." ".$drcr : '0.00'; ?>" disabled>

						</div>

					</div>

					<div class="col-md-3">

						<div class="form-group" >

							<label class="control-label">Last Billed On</label>

							<?php $last_bill = (isset($order) ? $order->last_bill : ' '); ?>

							<?php $last_bill = ($last_bill == '' ? '--' :$last_bill); ?>
							
							<input type="text" class="form-control " name="last_bill_date" id="last_bill" value="<?php echo $TransDate; ?>" disabled> 

						</div>

					</div>

					<div class="col-md-3">

						<div class="form-group">

							<label class="control-label">Last Billed Amt</label>

							<input type="text" class="form-control" name="last_bill_amt" id="last_bill_amt" value="<?php echo $BillAmt; ?>" disabled>

						</div>

					</div>

					<div class="clearfix"></div>

					<div class="col-md-3">

						<div class="form-group">

							<label class="control-label">Last Deposit</label>

							<?php $last_dep = (isset($order) ? $order->last_dep : ' '); ?>

							<?php $last_dep = ($last_dep == '' ? '--' :$last_dep); ?>

							<input type="text" class="form-control " name="last_dep_date" id="last_dep" value="<?php echo $depTransDate; ?>" disabled>

						</div>

					</div>

					

					<div class="col-md-3">

						<div class="form-group">

							<label class="control-label">Last Deposit Amt</label>

							<input type="text" class="form-control" name="last_dep_amt" id="last_dep" value="<?php echo $depositAmt; ?>" disabled>

						</div>

					</div>

					<div class="col-md-4">

						<div class="form-group">

							<label class="control-label">Order Type</label>

							<?php $taxable = (isset($order) ? $order->OrderType : ''); ?>

							<input type="hidden" class="form-control" name="taxes1" id="taxes1" value="<?php echo $taxable; ?>">

							<input type="text" class="form-control" name="tax1" id="tax1" value="<?php echo $taxable; ?>"  disabled>

						</div>

					</div>

					<div class="col-md-4">

						<div class="form-group">

							<label class="control-lable">Party GSTNO</label>

							<input type="text" class="form-control" name="PartyGST" id="PartyGST" value="<?php echo isset($client_detail) ? $client_detail->vat : ''; ?>" disabled>

						</div>

					</div>

					<div class="col-md-4">

						<div class="form-group">

							<label class="control-label"><?php echo _l('customer_type'); ?></label>

							<input type="text" class="form-control" name="customer_group" id="customer_group" value="<?php echo isset($customer_groups_name) ? $customer_groups_name->name : ''; ?>" placeholder="Distributor Type" disabled>

						</div>

					</div>

					

					<?php

						if (isset($order)) {

							$add_items       = $order->items;

							$add_items_free_dist       = $order->items_free_dist;

							$item_code_list = array();

							foreach($add_items as $item) {

								array_push($item_code_list, $item['ItemID']);

						}

							$item_code_string = implode(",",$item_code_list);

							}else{

							$item_code_string = "";

						}

					?>

					<div class="col-md-4" style="margin-top:2%;">

						<div class="form-group">

							<?php $order_type = (isset($order) ? $order->order_type : 'Web'); ?>

							<label class="control-label">Order From : <span style="color:#3826d3 !important;font-size: 15px;font-weight: 600;"><?php echo $order_type; ?></span></label>

							<input type="hidden" class="form-control" name="order_type" id="order_type" value="<?php echo $order_type; ?>">

							<input type="hidden" class="form-control" name="item_code_list" id="item_code_list" value="<?php echo $item_code_string; ?>">

						</div>

					</div>

					<div class="clearfix"></div>

					<div class="col-md-4">

						<div class="form-group">

							<label class="control-lable">Shipping State</label>

							<input type="text" class="form-control" name="shipping_state" id="shipping_state" value="<?php echo $ShippingState; ?>" disabled>

						</div>

					</div>

					<div class="col-md-4">

						<div class="form-group">

							<label class="control-lable">Shipping City</label>

							<input type="text" class="form-control" name="shipping_city" id="shipping_city" value="<?php echo $ShippingCity; ?>" disabled>

						</div>

					</div>

					

				</div>

			</div>

			

		</div>

        <div class="row">

            

            <div class="col-md-6">

				

				

				<div class="row">

					

					<div class="col-md-6">

						

					</div>

					<!--<div class="col-md-6">

						<?php

							$value = '';

							if(isset($order)){

								$value = _d($order->duedate);

								} else {

								if(get_option('invoice_due_after') != 0){

									$value = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));

								}

							}

						?>

						<?php echo render_date_input('duedate','invoice_add_edit_duedate',$value); ?>

					</div>-->

				</div>

				

				<div class="row">

					<div class="col-md-12">

						<!--<hr class="hr-10" />-->

						<!--<a href="#" class="edit_shipping_billing_info" data-toggle="modal" data-target="#billing_and_shipping_details"><i class="fa fa-pencil-square-o"></i></a>-->

						<?php include_once(APPPATH .'views/admin/order/billing_and_shipping_template.php'); ?>

					</div>

				</div>

				

			</div>

			

		</div>

		

		<div class="row" style="display:none;">

			

			<?php

				$currency_attr = array('disabled'=>true,'data-show-subtext'=>true,);

				$currency_attr = apply_filters_deprecated('invoice_currency_disabled', [$currency_attr], '2.3.0', 'invoice_currency_attributes');

				

				foreach($currencies as $currency){

					if($currency['isdefault'] == 1){

						$currency_attr['data-base'] = $currency['id'];

					}

					if(isset($order)){

						if($currency['id'] == $order->currency){

							$selected = $currency['id'];

						}

                        } else {

						if($currency['isdefault'] == 1){

							$selected = $currency['id'];

						}

					}

				}

				$currency_attr = hooks()->apply_filters('invoice_currency_attributes',$currency_attr);

				

			?>

			<?php echo render_select('currency', $currencies, array('id','name','symbol'), 'invoice_add_edit_currency', $selected, $currency_attr); ?>

			<div class="col-md-3">

				<input type="hidden" name="currency" value="<?php echo $selected; ?>">

				<?php

					$i = 0;

					$selected = '';

					foreach($staff as $member){

						if(isset($order)){

							if($order->sale_agent == $member['staffid']) {

								$selected = $member['staffid'];

							}

						}

						$i++;

					}

					echo render_select('sale_agent',$staff,array('staffid',array('firstname','lastname')),'sale_agent_string',$selected);

				?>

			</div>

			

		</div>

		

		

		

		

		<div class="row">

			

			<div class="col-md-6">

				

			</div>

			<div class="recurring_custom <?php if((isset($order) && $order->custom_recurring != 1) || (!isset($order))){echo 'hide';} ?>">

				<div class="col-md-6">

					<?php $value = (isset($order) && $order->custom_recurring == 1 ? $order->recurring : 1); ?>

					<?php echo render_input('repeat_every_custom','',$value,'number',array('min'=>1)); ?>

				</div>

				<div class="col-md-6">

					<select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

						<option value="day" <?php if(isset($order) && $order->custom_recurring == 1 && $order->recurring_type == 'day'){echo 'selected';} ?>><?php echo _l('invoice_recurring_days'); ?></option>

						<option value="week" <?php if(isset($order) && $order->custom_recurring == 1 && $order->recurring_type == 'week'){echo 'selected';} ?>><?php echo _l('invoice_recurring_weeks'); ?></option>

						<option value="month" <?php if(isset($order) && $order->custom_recurring == 1 && $order->recurring_type == 'month'){echo 'selected';} ?>><?php echo _l('invoice_recurring_months'); ?></option>

						<option value="year" <?php if(isset($order) && $order->custom_recurring == 1 && $order->recurring_type == 'year'){echo 'selected';} ?>><?php echo _l('invoice_recurring_years'); ?></option>

					</select>

				</div>

			</div>

			<div id="cycles_wrapper" class="<?php if(!isset($order) || (isset($order) && $order->recurring == 0)){echo ' hide';}?>">

				<div class="col-md-12">

					<?php $value = (isset($order) ? $order->cycles : 0); ?>

					<div class="form-group recurring-cycles">

						<label for="cycles"><?php echo _l('recurring_total_cycles'); ?>

                            <?php if(isset($order) && $order->total_cycles > 0){

								echo '<small>' . _l('cycles_passed', $order->total_cycles) . '</small>';

							}

                            ?>

						</label>

						<div class="input-group">

                            <input type="number" class="form-control"<?php if($value == 0){echo ' disabled'; } ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if(isset($order) && $order->total_cycles > 0){echo 'min="'.($order->total_cycles).'"';} ?>>

                            <div class="input-group-addon">

								<div class="checkbox">

									<input type="checkbox"<?php if($value == 0){echo ' checked';} ?> id="unlimited_cycles">

									<label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>

								</div>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>

		<!--<?php $value = (isset($order) ? $order->adminnote : ''); ?>

		<?php echo render_textarea('adminnote','invoice_add_edit_admin_note',$value); ?>-->

		

		<?php if(isset($invoice_from_project)){ echo '<hr class="no-mtop" />'; } ?>

		<div class="table-responsive s_table">

			

			<table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">

				<thead>

					<tr>

						<th></th>

						<th  width="8%" align="left">ItemID</th>

						<th width="30%" align="left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_description_new_lines_notice'); ?>"></i> Item Name</th>

						<th width="5%" align="left">CS/CR</th>

						<th width="5%" align="left">Pkg</th>

						<?php

							

							$qty_heading = "Order Qty.(Pkt)";

							if(isset($order) && $order->show_quantity_as == 2 || isset($hours_quantity)){

								$qty_heading = _l('invoice_table_hours_heading');

								} else if(isset($order) && $order->show_quantity_as == 3){

								$qty_heading = _l('invoice_table_quantity_heading') .'/'._l('invoice_table_hours_heading');

							}

						?>

						<th width="5%" align="left" class="qty1"><?php echo $qty_heading; ?></th>

						<?php if(isset($order)){

						?>

						<th width="2%" align="left" class="qty">new Qty</th>

						<th width="8%" align="left" class="qty">Stock</th>

						<th width="29%" align="left">Reason</th>

						<?php

						}?>

						<th width="5%" align="left">Qty(CS/CR)</th>

						<th width="5%" align="left">Rate</th>

						<th width="5%" align="left">Disc</th>

						<th width="5%" align="left">Disc Amt</th>

						<!-- <th width="5%" align="left">GST</th>-->

						<th width="10%" align="left">GST</th>

						

						<th width="10%" align="left">Amount</th>

						<?php if(isset($order)){}else{ ?>

							<th align="center"><i class="fa fa-cog"></i></th>

						<?php } ?>

					</tr>

				</thead>

				

				<tbody>

					<?php

						//if(isset($order)){
							$order=isset($order) ? $order : null;
							if($order?->SaleDetails?->irn == NULL && $order?->ChallanDetails?->Gatepassuserid == NULL){
								$new_item = 'undefined';
								if(isset($order)){
									$new_item = true;
								}

						?>

						<tr class="main">

							<td></td>

							<td><input type="text" name="item_code" id="item_code" class="form-control">

								<div class="" id="serchh" style="display:none;">Serching</div>

							<input type="hidden" name="hsn_code" class="form-control"></td>

							<td>

								<input type="text" id="autouser" name="autouser" class="form-control" placeholder="item name">

							</td>

							<td>

								<input type="text" name="items_case_qty" id="items_case_qty" class="form-control" placeholder="" disabled>

								

							</td>

							<td>

								<input type="number" name="pack_qty" id="pack_qty" class="form-control" placeholder="" disabled>

							</td>

							<?php if(isset($order)){

								$ee = "11";

								$min = 0;

								$q_dis_not = "disabled";

								}else{
									$ee = "";

								$min = 0;

								$q_dis_not = "";

							}?>

							<td>

								<input type="text" name="quantity<?php echo $ee;?>" id="quantity<?php echo $ee;?>" min="<?php echo $min;?>"  value="" class="form-control"  <?php echo $q_dis_not; ?>>

							</td>

							<?php if(isset($order)){

							?>

							<td>

								<input type="text" name="quantity" id="quantity"  value="" class="form-control" >

							</td>

							<td>

								<input type="text" name="stockqty" id="stockqty" class="form-control" placeholder="" disabled>

							</td>

							<td>

								<input type="text" name="ereason" class="form-control" placeholder="" disabled>

							</td>

							

							<?php } ?>

							<td>

								<input type="text" name="qty_cs_cr" id="qty_cs_cr" class="form-control" placeholder="" disabled>

							</td>

							<td>

								<input type="text" name="rate" id="rate" class="form-control" placeholder="" disabled>

							</td>

							<?php if(isset($order)){

							?>

							<td>

								<input type="text" name="dis" id="dis" class="form-control"  onblur="add_item_to_table2('undefined','undefined',<?php echo $new_item; ?>); return false;"  placeholder="" >

							</td>

							<?php }else{ ?>

							<td>

								<input type="text" name="dis" id="dis" class="form-control"  onblur="add_item_to_table1('undefined','undefined',<?php echo $new_item; ?>);return false;" placeholder="" >

							</td>

							<?php } ?>

							<td>

								<input type="text" name="dis_amt" id="dis_amt" class="form-control" placeholder="" disabled>

								<input type="hidden" name="taxrate1" id="taxrate1" class="form-control" placeholder="" disabled>

							</td>

							<td>

								<?php

									$default_tax = unserialize(get_option('default_tax'));

									$select = '<select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" id="taxname" multiple data-none-selected-text="" disabled>';

									foreach($taxes as $tax){

										$selected = '';

										if(is_array($default_tax)){

											if(in_array($tax['name'] . '|' . $tax['taxrate'],$default_tax)){

												$selected = ' selected ';

											}

										}

										$select .= '<option value="'.$tax['name'].'|'.$tax['taxrate'].'"'.$selected.'data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';

									}

									$select .= '</select>';

									echo $select;

								?>

							</td>

							<td></td>

							<td>

								<?php

									$new_item = 'undefined';

									if(isset($order)){

										$new_item = true;

									}

								?>

							</td>

						</tr>

						<?php

						}

						//}    

					?>

					

					<!--<?php

						if((isset($order) && $order->ChallanID == null) || !isset($order)){

						?> 

						

					<?php } ?>-->

					<?php if (isset($order) || isset($add_items)) {

						$i               = 1;

						//print_r($order->items);

						$items_indicator = 'newitems';

						if (isset($order)) {

							$add_items       = $order->items;

							$itemStocks       = $order->itemStocks;

							$items_indicator = 'items';

						}

						foreach ($add_items as $item) {

							

							$manual    = false;

							$table_row = '<tr class="sortable item ">';

							$table_row .= '<td class="dragger">';

							if (!is_numeric($item['qty'])) {

								$item['qty'] = 1;

							}

							$invoice_item_taxes = get_invoice_item_taxes($item['id']);

							// passed like string

							if ($item['id'] == 0) {

								$invoice_item_taxes = $item['taxname'];

								$manual             = true;

							}

							$table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);

							$amount = $item['rate'] * $item['qty'];

							$amount = app_format_number($amount);

							$code_placeholder = "Item Code";

							

							$table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';

							$table_row .= '</td>';

							$table_row .= '<td><input type="text" placeholder="'.$code_placeholder.'" name="'.$items_indicator.'['.$i.'][item_code]" class="form-control" value="'.$item['ItemID'].'" disabled><input type="hidden" name="'.$items_indicator.'['.$i.'][item_code1]" value="'.$item['ItemID'].'"><input type="hidden" name="'.$items_indicator.'['.$i.'][hsn_code]" value="'.$item['hsn_code'].'"></td>';

							$table_row .= '<td class="bold description"><input name="' . $items_indicator . '[' . $i . '][description]" class="form-control" value="'.$item['description'].'" '. $tt .' disabled></td>';

							$table_row .= '<td class="cs_cr"><input name="' . $items_indicator . '[' . $i . '][items_case_qty]" class="form-control"  value="'.$item['SuppliedIn'].'" disabled><input type="hidden" name="' . $items_indicator . '[' . $i . '][cs_cr]" value="'.$item['SuppliedIn'].'"></td>';

							$CaseQty = (int) $item['CaseQty'];

							$table_row .= '<td class="pack_qty"><input name="' . $items_indicator . '[' . $i . '][pack_qty1]" class="form-control" value="'.$CaseQty.'" disabled><input type="hidden" name="' . $items_indicator . '[' . $i . '][pack_qty]" value="'.$CaseQty.'"></td>';

							//$table_row .= render_custom_fields_items_table_in($item,$items_indicator.'['.$i.']');

							$old_qty = $item['OrderQty']; //  / $item['CaseQty'] Add If Needed

							if(!empty($item['TransID'])){

								$old_qty = $item['eOrderQty'];

							}

							if(is_null($item['eOrderQty'])){

								$qty = $item['OrderQty'] ;//  / $item['CaseQty'] Add If Needed

								}else {

								$qty = $item['eOrderQty'] ;//  / $item['CaseQty'] Add If Needed

								if(!empty($item['TransID'])){

									$qty = $item['BilledQty'];

								}

							}

							if(isset($order)){

								$dq = "";

								}else{

								$dq = "data-quantity";

							}

							$table_row .= '<td class="order_qty1"><input type="text" min="1" onblur="calculate_total();" onchange="calculate_total();" '.$dq.' name="' . $items_indicator . '[' . $i . '][qty1]" value="' . $old_qty . '" class="form-control" disabled>';

							

							$table_row .= '</td>';

							$table_row .= '<td class="order_qty"><input type="text" min="0" onblur="calculate_total2();" onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $qty . '" class="form-control">';

							$table_row .= '</td>';

							$PQty = 0;

							$PRQty = 0;

							$IQty = 0;

							$PRDQty = 0;

							$SQty = 0;

							$SRTQty = 0;

							$AQty = 0;

							$AQty2 = 0;

							$AQty3 = 0;

							$AQty4 = 0;

							$GIQty = 0;

							$GOQty = 0;

							

							foreach ($itemStocks as $stock) {

								if($stock['ItemID']==$item['ItemID']){

									if($stock['TType'] == 'P' && $stock['TType2'] == 'Purchase'){

										$PQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'N'){

										$PRQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'A'){

										$IQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'B'){

										$PRDQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){

										$SQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){

										$SRTQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X'  && $stock['TType2'] == 'Free distribution'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X'  && $stock['TType2'] == 'Free Distribution'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Damaged'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){

										$GIQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){

										$GOQty = $stock['BilledQty'];

									}

								}

							}

							$stockQty = $item['OQty'] + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty - $GOQty + $GIQty;

							/*$stock = $item['OQty'] + $item['PQty'] - $item['PRQty'] - $item['IQty'] + $item['PRDQty'] + $item['gtiqty'] - $item['gtoqty'] - $item['SQty'] + $item['SRQty'] - $item['DQTY'] - $item['ADJQTY']; 

							$stockInCase = $stock / $item['CaseQty'];*/

							$stockQtyInCase = $stockQty / $item['CaseQty'];

							if($order->ChallanID !== null){

								$stockQtyInCase += $qty;

							}

							$table_row .= '<td class="stock_qty"><input type="text" min="0"  onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][stockqty]" value="' . number_format((float)$stockQtyInCase, 2, '.', '') . '" class="form-control" disabled> <input type="hidden" name="' . $items_indicator . '[' . $i . '][stockqty1]" value="' . number_format((float)$stockInCase, 2, '.', '') . '">';

							$table_row .= '</td>';

							

							$table_row .= '<td class="reason_td"><input type="text" name="' . $items_indicator . '[' . $i . '][ereason]" value="' . $item['ereason'] . '" class="form-control">';

							$table_row .= '</td>';

							

							$table_row .= '<td class="qty_cs_cr"><input type="number" value="' . number_format($qty/$CaseQty, 1, '.', '') . '" class="form-control" disabled> <input type="hidden" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['BasicRate'] . '"></td>';

							$table_row .= '<td class="rate"><input type="number" name="' . $items_indicator . '[' . $i . '][rate1]" value="' . $item['BasicRate'] . '" class="form-control" disabled> <input type="hidden" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['BasicRate'] . '"></td>';

							$table_row .= '<td class="dis"><input name="' . $items_indicator . '[' . $i . '][dis]" class="form-control"  value="'.$item['DiscPerc'].'"  onblur="calculate_total();" onchange="calculate_total();"></td>';

							$table_row .= '<td class="dis_amt"><input name="' . $items_indicator . '[' . $i . '][dis_amt]" class="form-control"  value="'.round($item['DiscAmt'],2).'"  readonly><input type="hidden" name="' . $items_indicator . '[' . $i . '][dis_amt1]" value="'.$item['DiscAmt'].'"></td>';

							//$table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item['id'], true, $manual) . '</td>';

							//$gst_value = 0.00;

							if($item['igst']=="0.00" || is_null($item['igst'])){

								

								(double) $gst_value = $item['cgst'] + $item['sgst'];

								

								}else{

								$gst_value = $item['igst'];

							}

							//$final_value = (float) $gst_value;

							$final_value = number_format($gst_value, 2);

							$table_row .= '<td class="taxrate"><input type="hidden" value="'.$final_value.'" name="' . $items_indicator . '[' . $i . '][taxrate1]" >';

							

							$table_row .= '<select class="selectpicker display-block tax" data-width="100%" name="taxname[]" id="taxname" multiple data-none-selected-text="" disabled>';

							//  $select .= '<option value=""'.(count($default_tax) == 0 ? ' selected' : '').'>'._l('no_tax').'</option>';

							foreach($taxes as $tax){

								$selected = "";

								if($tax['taxrate'] == $final_value){

									$selected = ' selected ';

								}

								

								

								$table_row .= '<option value="'.$tax['taxrate'].'"'.$selected.'data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';

							}

							$table_row .= '</select></td>';

							

							$table_row .= '<td class="amount" align="right">' . $item['grand_total'] . '</td>';

							

							//$table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';

							

							if (isset($item['task_id'])) {

								if (!is_array($item['task_id'])) {

									$table_row .= form_hidden('billed_tasks['.$i.'][]', $item['task_id']);

									} else {

									foreach ($item['task_id'] as $task_id) {

										$table_row .= form_hidden('billed_tasks['.$i.'][]', $task_id);

									}

								}

								} else if (isset($item['expense_id'])) {

								$table_row .= form_hidden('billed_expenses['.$i.'][]', $item['expense_id']);

							}

							$table_row .= '</tr>';

							echo $table_row;

							$i++;

						}

						

						$items_indicator = 'free_items';

						foreach ($add_items_free_dist as $item) {

							

							$manual    = false;

							$table_row = '<tr class="sortable item ">';

							$table_row .= '<td class="dragger">';

							if (!is_numeric($item['qty'])) {

								$item['qty'] = 1;

							}

							$invoice_item_taxes = get_invoice_item_taxes($item['id']);

							// passed like string

							if ($item['id'] == 0) {

								$invoice_item_taxes = $item['taxname'];

								$manual             = true;

							}

							$table_row .= form_hidden('' . $items_indicator . '[' . $i . '][itemid]', $item['id']);

							$amount = $item['rate'] * $item['qty'];

							$amount = app_format_number($amount);

							$code_placeholder = "Item Code";

							

							$table_row .= '<input type="hidden" class="order" name="' . $items_indicator . '[' . $i . '][order]">';

							$table_row .= '</td>';

							$table_row .= '<td><input type="text" placeholder="'.$code_placeholder.'" name="'.$items_indicator.'['.$i.'][item_code]" class="form-control" value="'.$item['ItemID'].'" disabled><input type="hidden" name="'.$items_indicator.'['.$i.'][item_code1]" value="'.$item['ItemID'].'"><input type="hidden" name="'.$items_indicator.'['.$i.'][hsn_code]" value="'.$item['hsn_code'].'"></td>';

							$table_row .= '<td class="bold description"><input name="' . $items_indicator . '[' . $i . '][description]" class="form-control" value="'.$item['description'].' - Free" '. $tt .' disabled></td>';

							$table_row .= '<td class="cs_cr"><input name="' . $items_indicator . '[' . $i . '][items_case_qty]" class="form-control"  value="'.$item['SuppliedIn'].'" disabled><input type="hidden" name="' . $items_indicator . '[' . $i . '][cs_cr]" value="'.$item['SuppliedIn'].'"></td>';

							$CaseQty = (int) $item['CaseQty'];

							$table_row .= '<td class="pack_qty"><input name="' . $items_indicator . '[' . $i . '][pack_qty1]" class="form-control" value="'.$CaseQty.'" disabled><input type="hidden" name="' . $items_indicator . '[' . $i . '][pack_qty]" value="'.$CaseQty.'"></td>';

							//$table_row .= render_custom_fields_items_table_in($item,$items_indicator.'['.$i.']');

							$old_qty = $item['OrderQty']; //  / $item['CaseQty'] Add If Needed

							if(is_null($item['eOrderQty'])){

								$qty = $item['OrderQty'] ;//  / $item['CaseQty'] Add If Needed

								}else {

								$qty = $item['eOrderQty'] ;//  / $item['CaseQty'] Add If Needed

							}

							if(isset($order)){

								$dq = "";

								}else{

								$dq = "data-quantity";

							}

							$table_row .= '<td class="order_qty1"><input type="text" min="1" onblur="calculate_total();" onchange="calculate_total();" '.$dq.' name="' . $items_indicator . '[' . $i . '][qty1]" value="' . $old_qty . '" class="form-control" disabled>';

							

							$table_row .= '</td>';

							$table_row .= '<td class="order_qty"><input type="text" min="0" onblur="calculate_total2();" onchange="calculate_total();" readonly data-quantity name="' . $items_indicator . '[' . $i . '][qty]" value="' . $qty . '" class="form-control">';

							$table_row .= '</td>';

							$PQty = 0;

							$PRQty = 0;

							$IQty = 0;

							$PRDQty = 0;

							$SQty = 0;

							$SRTQty = 0;

							$AQty = 0;

							$AQty2 = 0;

							$AQty3 = 0;

							$AQty4 = 0;

							$GIQty = 0;

							$GOQty = 0;

							

							foreach ($itemStocks as $stock) {

								if($stock['ItemID']==$item['ItemID']){

									if($stock['TType'] == 'P' && $stock['TType2'] == 'Purchase'){

										$PQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'N'){

										$PRQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'A'){

										$IQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'B'){

										$PRDQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){

										$SQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){

										$SRTQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X'  && $stock['TType2'] == 'Free distribution'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X'  && $stock['TType2'] == 'Free Distribution'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Damaged'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity'){

										$AQty += $stock['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){

										$GIQty = $stock['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){

										$GOQty = $stock['BilledQty'];

									}

								}

							}

							$stockQty = $item['OQty'] + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty - $GOQty + $GIQty;

							/*$stock = $item['OQty'] + $item['PQty'] - $item['PRQty'] - $item['IQty'] + $item['PRDQty'] + $item['gtiqty'] - $item['gtoqty'] - $item['SQty'] + $item['SRQty'] - $item['DQTY'] - $item['ADJQTY']; 

							$stockInCase = $stock / $item['CaseQty'];*/

							$stockQtyInCase = $stockQty / $item['CaseQty'];

							if($order->ChallanID !== null){

								$stockQtyInCase += $qty;

							}

							$table_row .= '<td class="stock_qty"><input type="text" min="0"  onchange="calculate_total();" data-quantity name="' . $items_indicator . '[' . $i . '][stockqty]" value="' . number_format((float)$stockQtyInCase, 2, '.', '') . '" class="form-control" disabled> <input type="hidden" name="' . $items_indicator . '[' . $i . '][stockqty1]" value="' . number_format((float)$stockInCase, 2, '.', '') . '">';

							$table_row .= '</td>';

							

							$table_row .= '<td class="reason_td1"><input type="text" name="' . $items_indicator . '[' . $i . '][ereason]" value="' . $item['ereason'] . '" class="form-control" readonly>';

							$table_row .= '</td>';

							

							$table_row .= '<td class="qty_cs_cr"><input type="number" value="' . number_format($qty/$CaseQty, 1, '.', '') . '" class="form-control" disabled> <input type="hidden" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['BasicRate'] . '"></td>';

							$table_row .= '<td class="rate"><input type="number"  onblur="calculate_total();" onchange="calculate_total();" name="' . $items_indicator . '[' . $i . '][rate1]" value="' . $item['BasicRate'] . '" class="form-control" disabled> <input type="hidden" name="' . $items_indicator . '[' . $i . '][rate]" value="' . $item['BasicRate'] . '"></td>';

							$table_row .= '<td class="dis"><input name="' . $items_indicator . '[' . $i . '][dis]" class="form-control"  value="'.$item['DiscPerc'].'" readonly></td>';

							$table_row .= '<td class="dis_amt"><input name="' . $items_indicator . '[' . $i . '][dis_amt]" class="form-control"  value="'.round($item['DiscAmt'],2).'" readonly><input type="hidden" name="' . $items_indicator . '[' . $i . '][dis_amt1]" value="'.$item['DiscAmt'].'"></td>';

							//$table_row .= '<td class="taxrate">' . $this->misc_model->get_taxes_dropdown_template('' . $items_indicator . '[' . $i . '][taxname][]', $invoice_item_taxes, 'invoice', $item['id'], true, $manual) . '</td>';

							//$gst_value = 0.00;

							if($item['igst']=="0.00" || is_null($item['igst'])){

								

								(double) $gst_value = $item['cgst'] + $item['sgst'];

								

								}else{

								$gst_value = $item['igst'];

							}

							//$final_value = (float) $gst_value;

							$final_value = number_format($gst_value, 2);

							$table_row .= '<td class="taxrate"><input type="hidden" value="'.$final_value.'" name="' . $items_indicator . '[' . $i . '][taxrate1]" >';

							

							$table_row .= '<select class="selectpicker display-block tax" data-width="100%" name="taxname[]" id="taxname" multiple data-none-selected-text="" disabled>';

							//  $select .= '<option value=""'.(count($default_tax) == 0 ? ' selected' : '').'>'._l('no_tax').'</option>';

							foreach($taxes as $tax){

								$selected = "";

								if($tax['taxrate'] == $final_value){

									$selected = ' selected ';

								}

								

								

								$table_row .= '<option value="'.$tax['taxrate'].'"'.$selected.'data-taxrate="'.$tax['taxrate'].'" data-taxname="'.$tax['name'].'" data-subtext="'.$tax['name'].'">'.$tax['taxrate'].'%</option>';

							}

							$table_row .= '</select></td>';

							

							$table_row .= '<td class="amount" align="right">' . $item['grand_total'] . '</td>';

							

							//$table_row .= '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' . $item['id'] . '); return false;"><i class="fa fa-times"></i></a></td>';

							

							if (isset($item['task_id'])) {

								if (!is_array($item['task_id'])) {

									$table_row .= form_hidden('billed_tasks['.$i.'][]', $item['task_id']);

									} else {

									foreach ($item['task_id'] as $task_id) {

										$table_row .= form_hidden('billed_tasks['.$i.'][]', $task_id);

									}

								}

								} else if (isset($item['expense_id'])) {

								$table_row .= form_hidden('billed_expenses['.$i.'][]', $item['expense_id']);

							}

							$table_row .= '</tr>';

							echo $table_row;

							$i++;

						}

					}

					?>

				</tbody>

			</table>

		</div>

		<div class="col-md-6">

			<table class="table text-right">

				<tbody>

					<tr id="subtotal">

						<td class="total_crates"><span class="bold">Total Crates :</span>

							<input type="hidden" name="total_crates" value="">

						</td>

						<td class="crates"></td>

						<td class="total_cases"><span class="bold">Total Cases :</span>

							<input type="hidden" name="total_cases" value="">

							<input type="hidden" name="total_tax" value="">

						</td>

						<td class="cases"></td>

						<td class="total_pieces"><span class="bold">Total Pieces :</span>

							<input type="hidden" name="total_pieces" value="">

						</td>

						<td class="pieces"></td>

					</tr>

				</tbody>

			</table>

		</div>

		<div class="col-md-6">

			<table class="table text-right">

				<tbody>

					

					

					

					<tr id="subtotal">

						<td><span class="bold"><?php echo _l('invoice_subtotal'); ?> :</span>

						</td>

						<td class="subtotal" colspan="2">

						</td>

					</tr>

					<tr id="discount_area">

						<td>

							<div class="row">

								<div class="col-md-12">

									<span class="bold">

										<?php echo _l('order_discount'); ?>

									</span>

								</div>

								

							</div>

						</td>

						<td class="discount-total" colspan="2"></td>

					</tr>

					

					<tr class="cgsttotaltr">

						<td><span class="bold"><?php echo _l('CGST_Amt'); ?> :</span>

						</td>

						<td class="cgsttotal" colspan="2">

						</td>

					</tr>

					<tr class="sgsttotaltr">

						<td><span class="bold"><?php echo _l('SGST_Amt'); ?> :</span>

						</td>

						<td class="sgsttotal" colspan="2">

						</td>

					</tr>

					<tr class="igsttotaltr">

						<td><span class="bold"><?php echo _l('IGST_Amt'); ?> :</span>

						</td>

						<td class="igsttotal" colspan="2">

						</td>

					</tr>

					<tr class="tcstotaltr">

						<td><span class="bold">TCS :</span>

						</td>

						<td style="width:5%"><span class="istcsper"></span></td>

						<td class="tcstotal">

						</td>

					</tr>

					<tr>

						<td><span class="bold" ><?php echo _l('invoice_total'); ?> :</span>

						</td>

						<td class="total" colspan="2" style="width: 15%">

						</td>

					</tr>

				</tbody>

			</table>

		</div>

		<div id="removed-items"></div>

		<div id="billed-tasks"></div>

		<div id="billed-expenses"></div>

		<?php echo form_hidden('task_id'); ?>

		<?php echo form_hidden('expense_id'); ?>  

		

		

		

	</div>

	

</div>



<div class="row">

	<div class="col-md-12 mtop15">

		<div class="panel-body bottom-transaction">

            

           <div class="btn-bottom-toolbar text-right" style="left: -16%;">
				


					<div class="btn-group dropup">

					<?php

						$selected_company = $this->session->userdata('root_company');

						$fy = $this->session->userdata('finacial_year');

						$fy_new  = $fy + 1;

						$first_date = '20'.$fy.'-04-01';

						$lastdate_date = '20'.$fy_new.'-03-31';

						$curr_date = date('Y-m-d');

						$lgstaff = $this->session->userdata('staff_user_id');

	                     $order_date = "";
							
							if(isset($order)){
								$order_date = substr($order->Transdate,0,10);
							}
						

						$order_date_new    = new DateTime($order_date);

						$first_date_yr = new DateTime($first_date);

						$last_date_yr = new DateTime($lastdate_date);

						$curr_date_new = new DateTime($curr_date);

						

						$sql = 'SELECT * FROM tblordermaster WHERE PlantID = '.$selected_company.' AND FY LIKE "'.$fy.'" AND  OrderStatus = "O" ORDER BY tblordermaster.OrderID DESC ';

						$result_data = $this->db->query($sql)->row();

						if($result_data){
							$lastdate_order = substr($result_data->Transdate,0,10);
						} else {
							$lastdate_order = date('Y-m-d');
						}

						
						

						if($curr_date_new > $last_date_yr){

							$lastdate = $lastdate_date;

							}else{

							$lastdate = date('Y-m-d');

						}

						

						$this->db->select('*');

						$this->db->where('plant_id', $selected_company);

						$this->db->where('year', $fy);

						$this->db->where('staff_id', $lgstaff);

						$this->db->LIKE('feature', "orders");

						$this->db->LIKE('capability', "view");

						$this->db->from(db_prefix() . 'staff_permissions');

						$result2 = $this->db->get()->row();

						// $day = $result2->days;
						if($result2){
							$day = $result2->days;
						} else {
							$day = 0;
						}

						

						if($day == 0){

                            $return = '';

							}else{

                            

                            $days = '- '.$day.' days';

                            $tillDate = date('Y-m-d', strtotime($lastdate. $days));

                            $tillDate_new = new DateTime($tillDate);

                            if ($order_date_new < $tillDate_new) {

                                $return = 'disabled';

								}else{

                                $return = '';

							}

						} 

					?>

					

					<?php

						if(isset($order)){

							if($order->ChallanDetails->Gatepassuserid == NULL && $order->SaleDetails->irn == NULL){

								if (has_permission_new('orders', '', 'edit')) {

									if($return == "disabled"){

									?>

									<a href="#" class="btn btn-info <?php echo $return;?>">Update</a>

									<?php

										}else{

									?>

									<button type="button" class="btn-tr btn btn-info invoice-form-submit transaction-submit">Update</button>

									<?php

									}

								}

							}

							

							/*if(isset($order) && ($order->GSTNO == NULL)){

								if($order->ChallanDetails->Gatepassuserid !== NULL){

								?>

								

								<?php

								}else{

								// Enter Code hare

								

								if (has_permission_new('orders', '', 'edit')) {

								?>

								<?php if($return == "disabled"){

								?>

								<a href="#" class="btn btn-info <?php echo $return;?>">Update</a>

								<?php

								}else{

								?>

								<button type="button" class="btn-tr btn btn-info invoice-form-submit transaction-submit">Update</button>

								<?php

								}?>

								<?php }  ?>

								<?php

								}

								}else{

								if($order->SaleDetails->irn !== NULL){

								?>

								

								<?php    

								}else{

								// Enter Code hare

								if (has_permission_new('orders', '', 'edit')) {

								?>

								<?php if($return == "disabled"){

								?>

								<a href="#" class="btn btn-info <?php echo $return;?>">Update</a>

								<?php

								}else{

								?>

								<button type="button" class="btn-tr btn btn-info invoice-form-submit transaction-submit">Update</button>

								<?php

								}?>

								<?php } 

								}

							}*/

						}

					?>

					<?php

						if(!isset($order)){

						?>  

						<?php

							if (has_permission_new('orders', '', 'create')) {

							?>

							<button type="button" class="btn-tr btn btn-info invoice-form-submit transaction-submit"><?php echo _l('submit'); ?></button>

							

						<?php } } ?>

				</div>
				</div>


		</div>

        <!--<div class="btn-bottom-pusher"></div>-->

	</div>

</div>

</div>

<style>

    ._transaction_form .table.items thead>tr>th {

    min-width: 70px;

	}

</style>

<!-- Script -->

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->

<!-- jQuery UI -->

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script> -->

<script type='text/javascript'>

    // $(document).ready(function(){"

	window.addEventListener('load', function() {

		

		

		$('.search_order').on('click', function() {

			

			var NestId = 'ORD' + $(".years").val() + $(".number1").val();

			var url = admin_url + 'order/list_orders/' + NestId;

			window.location.href = url;

			//init_order(NestId);

			//alert(NestId);

		});

		

		$('#ShipToParty').on('change', function() {

			var ShipToParty = $(this).val();

			var BillToParty = $("#clientid").val();

			var url = "<?php echo base_url(); ?>admin/order/GetShippingAddressList";

			if(BillToParty == ""){

				alert("Please Select Billing Party First");

				$('.selectpicker').selectpicker('refresh');

				}else{

				jQuery.ajax({

					type: 'POST',

					url:url,

					data: {ShipToParty: ShipToParty},

					dataType:'json',

					success: function(data) {

						$('#shipping_state').val("");

						$('#shipping_city').val("");

						$('#ship_to_act_gst').val(data[0]["vat"]);

						let ShippingAddress = data;

						$("#Ship_to").children().remove();

						//$("#Ship_to").append('<option value=" ">None selected</option>');

						for (var i = 0; i < ShippingAddress.length; i++) {

							var leble = ShippingAddress[i]["ShippingAdrees"]+" " +ShippingAddress[i]["city_name"]+ " ("+ShippingAddress[i]["ShippingState"]+") - "+ ShippingAddress[i]["ShippingPin"];

							$("#Ship_to").append('<option value="'+ShippingAddress[i]["id"]+'">'+leble+'</option>');

						}

						$('.selectpicker').selectpicker('refresh');

					}

				});

			}

		});

		

		$('#Ship_to').on('change', function() {

			var Ship_to = $(this).val();

			var url = "<?php echo base_url(); ?>admin/order/GetShippingDetails";

			jQuery.ajax({

				type: 'POST',

				url:url,

				data: {Ship_to: Ship_to},

				dataType:'json',

				success: function(data) {

					$('#shipping_state').val(data.state_name);

					$('#shipping_city').val(data.city_name);

				}

			});

		});

		$('#item_code').on('focus', function() {

			var ItemID = $('#item_code').val();

			if(ItemID == ''){

				}else{

				var item_code_list = $("#item_code_list").val();

				let result = item_code_list.replace(ItemID, " ");

				$("#item_code_list").val(result);

			}

			$('#item_code').val(''); 

			$('#autouser').val(''); 

			$('#items_case_qty').val(''); 

			$('#pack_qty').val(''); 

			$('#dis').val(''); 

			$('#dis_amt').val(''); 

			$('#quantity').val(''); 

			$('#rate').val(''); 

			$('#taxrate1').val('');

			$('#stockqty').val('');

			$('select[name=taxname]').val('');

			$('.selectpicker').selectpicker('refresh')

		}); 

		

		$('#autouser').on('focus', function() {

			var ItemID = $('#item_code').val();

			if(ItemID == ''){

			}else{

				var item_code_list = $("#item_code_list").val();

				let result = item_code_list.replace(ItemID, " ");

				$("#item_code_list").val(result);

			}

			$('#item_code').val(''); 

			$('#autouser').val(''); 

			$('#items_case_qty').val(''); 

			$('#pack_qty').val(''); 

			$('#dis').val(''); 

			$('#dis_amt').val(''); 

			$('#quantity').val(''); 

			$('#rate').val(''); 

			$('#taxrate1').val('');

			$('#stockqty').val('');

			$('select[name=taxname]').val('');

			$('.selectpicker').selectpicker('refresh')

		}); 

		// Initialize 

		$( "#autouser" ).autocomplete({

			source: function( request, response ) {

				// Fetch data

				<?php if(isset($order) && !empty($order->AccountID)){

				?>

				var clientid = '<?= $order->AccountID?>';

				<?php

					}else{

				?>

				var clientid = $("#clientid").val();

				<?php

				} ?>

				var location = $("#location_typevalue").val();

				var dist_type_id = $("#customer_group_id").val();

				var dist_state_id = $("#customer_state_id").val();

				var item_divistion = $("#item_divistion").val();

				var item_taxes = $("#taxes1").val();

				$.ajax({

					url: "<?=base_url()?>admin/order/itemlist",

					type: 'post',

					dataType: "json",

					data: {

						search: request.term,

						location: location,

						dist_type_id: dist_type_id,

						dist_state_id: dist_state_id,

						item_divistion: item_divistion,

						item_taxes: item_taxes,

						clientid: clientid,

					},

					success: function( data ) {

						response( data );

					}

				});

			},

			select: function (event, ui) {

				var item_divistion = $("#item_divistion").val();

				let div_array = item_divistion.split(",");

				

				var item_code_list = $("#item_code_list").val();

				let item_code_list_array = item_code_list.split(",");

				

				var item_taxes1 = $("#taxes1").val();

				if(ui.item.isactive == "N"){

					alert("Item Deactive....");

					$('#item_code').val('');

					}else{

					if(item_code_list_array.includes(ui.item.value)){

						alert("item already added");

						$('#item_code').val('');

						return false;

						}else{

						if(item_taxes1 == "TaxItems") {

							

							if(ui.item.gst != 1){

								/*if(div_array.includes(ui.item.itemdiv)){*/

								

								// Set selection

								$('#autouser').val(ui.item.label); // display the selected text

								$("#item_code_list").val(item_code_list+","+ui.item.value);

								add_item_to_preview1(ui.item.value,ui.item.location,ui.item.dist_type_id,ui.item.dist_state_id,ui.item.clientid);

								$('#userid').val(ui.item.value); // save selected id to input

								$('#quantity').focus();

								return false;

								

								/*}else{

									alert("Selected Item Division not assign to customer...");

									$('#autouser').val(""); // display the selected text

									$('#userid').val(""); // save selected id to input

									$('#item_code').val('');

									return false;

								}*/

								

								}else {

								alert("please add only taxable item");

								// Set selection

								$('#item_code').val('');

								$('#autouser').val(""); // display the selected text

								return false;

							}

						}

						

						if(item_taxes1 == "NonTaxItems"){

							if(ui.item.gst == 1){

								//if(div_array.includes(ui.item.itemdiv)){

								// Set selection

								$('#autouser').val(ui.item.label); // display the selected text

								$("#item_code_list").val(item_code_list+","+ui.item.value);

								add_item_to_preview1(ui.item.value,ui.item.location,ui.item.dist_type_id,ui.item.dist_state_id,ui.item.clientid);

								$('#userid').val(ui.item.value); // save selected id to input

								$('#quantity').focus();

								return false;

								/*}else{

									

									alert("Selected Item Division not assign to customer...");

									$('#autouser').val(""); // display the selected text

									$('#userid').val(""); // save selected id to input

									$('#item_code').val('');

									return false;

								}*/

								

								}else {

								alert("please add only non taxable item");

								// Set selection

								$('#item_code').val('');

								$('#autouser').val(""); // display the selected text

								return false;

							}

							

						}

						

						if(item_taxes1 == ""){

							//if(div_array.includes(ui.item.itemdiv)){

							// Set selection

							$('#autouser').val(ui.item.label); // display the selected text

							$("#item_code_list").val(item_code_list+","+ui.item.value);

							add_item_to_preview1(ui.item.value,ui.item.location,ui.item.dist_type_id,ui.item.dist_state_id,ui.item.clientid);

							$('#userid').val(ui.item.value); // save selected id to input

							$('#quantity').focus();

							return false;

							/*}else{

								

								alert("Selected Item Division not assign to customer...");

								$('#autouser').val(""); // display the selected text

								$('#userid').val(""); // save selected id to input

								$('#item_code').val('');

								return false;

							}*/

						}

					}  

				}

				

				

			}

		});

		

		// On Blur ItemID Get All Date

        $('#item_code').blur(function(){

            $('#quantity').focus();

            ItemID = $(this).val();

            if(ItemID == ''){

                

			}else{

				<?php if(isset($order) && !empty($order->AccountID)){

				?>

			    var clientid = '<?= $order->AccountID?>';

				<?php

					}else{

				?>

				var clientid = $("#clientid").val();

				<?php

				} ?>

				var location = $("#location_typevalue").val();

				var dist_type_id = $("#customer_group_id").val();

				var dist_state_id = $("#customer_state_id").val();

				var item_divistion = $("#item_divistion").val();

				var item_taxes = $("#taxes1").val();

                $.ajax({

                    url:"<?php echo admin_url(); ?>order/GetItemDetailByID",

                    dataType:"JSON",

                    method:"POST",

                    data:{ItemID:ItemID,location:location,dist_type_id:dist_type_id,dist_state_id:dist_state_id,item_divistion:item_divistion,item_taxes:item_taxes,clientid:clientid},

                    beforeSend: function () {

                        $('.searchh2').css('display','block');

                        $('.searchh2').css('color','blue');

					},

					complete: function () {

                        $('.searchh2').css('display','none');

					},

                    success:function(data){

                        var item_divistion = $("#item_divistion").val();

                        let div_array = item_divistion.split(",");

                        var item_code_list = $("#item_code_list").val();

                        let item_code_list_array = item_code_list.split(",");

                        var item_taxes1 = $("#taxes1").val();

                        if(data.isactive == "N"){

                            alert("Item Deactive....");

                            $('#item_code').val('');

						}else{

                            if(item_code_list_array.includes(data.item_code)){

                                alert("item already added");

                                $('#item_code').val('');

                                return false;

							}else{

                                if(item_taxes1 == "TaxItems") {

                                    if(data.tax != 1){

										//if(div_array.includes(data.group_id)){

										// Set selection

										$('#item_code').val(data.item_code); // display the selected text

										$("#item_code_list").val(item_code_list+","+data.item_code);

										add_item_to_preview1(data.item_code,data.location,data.dist_type_id,data.dist_state_id,data.clientid);

										$('#userid').val(data.item_code); // save selected id to input

										$('#quantity').focus();

										return false;

                                        /*}else{

                                            alert("Selected Item Division not assign to customer...");

                                            $('#item_code').val(""); // display the selected text

                                            $('#userid').val(""); // save selected id to input

                                            return false;

										}*/

										}else {

										alert("please add only taxable item");

										// Set selection

                                        $('#item_code').val(""); // display the selected text

                                        return false;

									}

								}if(item_taxes1 == "NonTaxItems"){

                                    if(data.tax == 1){

                                        //if(div_array.includes(data.group_id)){ 

                                        // Set selection

										$('#autouser').val(data.item_code); // display the selected text

										$("#item_code_list").val(item_code_list+","+data.item_code);

										add_item_to_preview1(data.item_code,data.location,data.dist_type_id,data.dist_state_id,data.clientid);

										$('#userid').val(data.item_code); // save selected id to input

										$('#quantity').focus();

										return false;

                                        /*}else{

                                            alert("Selected Item Division not assign to customer...");

                                            $('#item_code').val(""); // display the selected text

                                            $('#userid').val(""); // save selected id to input

                                            return false;

										}*/

									}else {

                                        alert("please add only non taxable item");

                                        // Set selection

                                        $('#item_code').val(""); // display the selected text

                                        return false;

									}

								}

                                

                                if(item_taxes1 == ""){

                                    //if(div_array.includes(data.group_id)){

                                    // Set selection

									$('#item_code').val(data.item_code); // display the selected text

									$("#item_code_list").val(item_code_list+","+data.item_code);

									add_item_to_preview1(data.item_code,data.location,data.dist_type_id,data.dist_state_id,data.clientid);

									$('#userid').val(data.item_code); // save selected id to input

									$('#quantity').focus();

									return false;

                                    /*}else{

                                        alert("Selected Item Division not assign to customer...");

                                        $('#item_code').val(""); // display the selected text

                                        $('#userid').val(""); // save selected id to input

                                        return false;

									}*/

								}

                                

							}

						}

					}

				});

			}

		});  

		// Initialize 

		$( "#item_code" ).autocomplete({

			source: function( request, response ) {

				// Fetch data

				var location = $("#location_typevalue").val();

				var dist_type_id = $("#customer_group_id").val();

				var dist_state_id = $("#customer_state_id").val();

				var item_divistion = $("#item_divistion").val();

				

				var item_divistion = $("#item_divistion").val();

				let div_array = item_divistion.split(",");

				var item_taxes = $("#taxes1").val();

				<?php if(isset($order) && !empty($order->AccountID)){

				?>

				var clientid = '<?= $order->AccountID?>';

				<?php

					}else{

				?>

				var clientid = $("#clientid").val();

				<?php

				} ?>

				$.ajax({

					url: "<?=base_url()?>admin/order/itemlist_using_itemcode",

					type: 'post',

					dataType: "json",

					data: {

						search: request.term,

						location: location,

						dist_type_id: dist_type_id,

						dist_state_id: dist_state_id,

						item_divistion: item_divistion,

						item_taxes: item_taxes,

						clientid:clientid

					},

					beforeSend: function () {

						$('#serchh').css('display','block');

					},

					complete: function () {

						$('#serchh').css('display','none');

					},

					success: function( data ) {

						response( data );

					}

				});

			},

			select: function (event, ui) {

				var item_divistion = $("#item_divistion").val();

				let div_array = item_divistion.split(",");

				var item_code_list = $("#item_code_list").val();

				let item_code_list_array = item_code_list.split(",");

				

				var item_taxes1 = $("#taxes1").val();

				if(ui.item.isactive == "N"){

					alert("Item Deactive....");

					$('#item_code').val('');

				}else{

					if(item_code_list_array.includes(ui.item.value)){

						alert("item already added");

						$('#item_code').val('');

						return false;

					}else{

						

						if(item_taxes1 == "TaxItems") {

							

							if(ui.item.gst != 1){

								//if(div_array.includes(ui.item.itemdiv)){

								// Set selection

								$('#item_code').val(ui.item.value); // display the selected text

								//$("#item_code_list").val(item_code_list+","+ui.item.value);

								

								// add_item_to_preview1(ui.item.value,ui.item.location,ui.item.dist_type_id,ui.item.dist_state_id,ui.item.clientid);

								$('#userid').val(ui.item.value); // save selected id to input

								$('#quantity').focus();

								return false;

								/*}else{

									alert("Selected Item Division not assign to customer...");

									$('#item_code').val(""); // display the selected text

									$('#userid').val(""); // save selected id to input

									return false;

								}*/

							}else {

								alert("please add only taxable item");

								// Set selection

								$('#item_code').val(""); // display the selected text

								return false;

							}

						}

						

						if(item_taxes1 == "NonTaxItems"){

							if(ui.item.gst == 1){

								//if(div_array.includes(ui.item.itemdiv)){ 

								// Set selection

								$('#autouser').val(ui.item.value); // display the selected text

								$('#userid').val(ui.item.value); // save selected id to input

								

								// add_item_to_preview1(ui.item.value,ui.item.location,ui.item.dist_type_id,ui.item.dist_state_id,ui.item.clientid);

								$('#quantity').focus();

								return false;

								/*}else{

									alert("Selected Item Division not assign to customer...");

									$('#item_code').val(""); // display the selected text

									$('#userid').val(""); // save selected id to input

									return false;

								}*/

								}else {

								alert("please add only non taxable item");

								// Set selection

								$('#item_code').val(""); // display the selected text

								return false;

							}

							

						}

						

						if(item_taxes1 == ""){

							//if(div_array.includes(ui.item.itemdiv)){

							// Set selection

							$('#item_code').val(ui.item.value); // display the selected text

							$('#userid').val(ui.item.value); // save selected id to input

							$('#quantity').focus();

							return false;

							/*}else{

								alert("Selected Item Division not assign to customer...");

								$('#item_code').val(""); // display the selected text

								$('#userid').val(""); // save selected id to input

								return false;

							}*/

						}

					}  

				}

			}

		});

	});

    

    

</script>

<script>

    // $(document).ready(function(){
	window.addEventListener('load', function() {

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

		

		$('#date1').datetimepicker({

			format: 'd/m/Y',

			minDate: minStartDate,

			maxDate: maxEndDate_new,

			timepicker: false

		});

		

		

		

	});

</script>     
