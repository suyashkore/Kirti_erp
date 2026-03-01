<?php defined('BASEPATH') or exit('No direct script access allowed');

 ?>
<div id="invoice-preview">
   <div class="row">
     

   
   <?php
   
    $state_detail = get_state_detail($order->client->state);
    $billing_state_detail = get_state_detail($order->billing_state);
    $shipping_state_detail = get_state_detail($order->shipping_state);
    $city_name = get_city_name_id($order->client->city);
    $user_detail = get_user_detail($order->addedfrom);
    $user_comp = unserialize($user_detail->staff_comp);
    $company_detail = get_company_name_by_id($user_comp[0]);
    /*echo "<pre>";
    print_r($order->client);
    die;*/
   ?>
   <div class="col-md-12">
       <h4 style="text-align: center;">Order Detail</h4>
   </div>
   <div class="col-md-12">
       <table style="width: 100%;" id="order_table" border="1" cellspacing="0" cellpadding="2">
           <tr>
               <td width="20%">Order No. </td>
               <td width="30%"> : <?php echo $order->OrderID; ?></td>
               <td width="20%">Order Date :</td>
               <td width="30%"> : <?php 
               $date = substr($order->Transdate,0,10);
               echo $date; ?></td>
           </tr>
           <tr>
              <td>State </td>
               <td> : <?php echo $state_detail->state_name; ?></td>
               <td>State Code </td>
               <td> : <?php //echo $state_detail->short_name;
               echo $order->client->state; ?></td>
           </tr>
           <tr>
              <td>Date of Supply </td>
               <td>: <?php echo $date; ?></td>
               <td>Place of Supply </td>
               <td>: <?php echo $order->client->shipping_street." ".$order->client->city; ?></td>
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
           <td >: <?php echo $order->client->address." ".$order->client->address3." ".$order->client->pincode; ?> </td>
           <td >Address </td>
           <td >: <?php echo $order->client->address." ".$order->client->address3." ".$order->client->pincode; ?></td>
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
       /* echo "<pre>";
    print_r($order->client);
    die;*/
        ?>
        <?php
        $inv_item = get_item_by_order_id($order->OrderID);
       
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
         /*echo $order->OrderID;
        print_r($inv_item);
        die;*/
        foreach ($inv_item as $item) {
            
            $item_detail = get_item_by_item_code($item['ItemID']);
            /*echo "<pre>";
            print_r($item_detail);
            die;*/
        ?>
       <tr>
           <td style="text-align:center;"><?php echo $i; ?></td>
           <td class="description" align="left;" width="<?php echo $item_name_width; ?>"><?php echo $item_detail->description; ?></td>
           <td width="<?php echo $hsn_width; ?>" style="text-align:center;"><?php echo $item_detail->hsn_code; ?></td>
           <td style="text-align:right;"><?php echo  (int) $item['CaseQty']; ?></td>
           <td style="text-align:right;"><?php 
           if(is_null($item['eOrderQty'])){
               echo  (int) $item['OrderQty'] / $item['CaseQty'];
           }else{
               echo  (int) $item['eOrderQty'] / $item['CaseQty'];
           }
            ?></td>
          <?php  
          if(is_null($item['eOrderQty'])){
              $qty = $qty + $item['OrderQty'] / $item['CaseQty'];
          }else{
              $qty = $qty + $item['eOrderQty'] / $item['CaseQty'];
          }
           ?>
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
    <td style="text-align:right;"><?php echo round($order->OrderAmt,2); ?></td>
    </tr>
    
    <!--<tr>
    <td colspan="2" style="border-right:none;">Pending Crates 0.00</td>
    <td colspan="3" style="border-right:none; border-left:none;">Crates on bill 0</td>
    <td colspan="3" style="border-right:none;">Cases on bill      <?php echo $order->total_cases; ?></td>
    <td colspan="3">Taxable Value/ Amt</td>
    <td colspan="2" style="text-align:right;"><?php echo round($taxable_amt,2); ?></td>
    </tr> -->
            </table>
   </div>
  
   <!--<div class="col-md-6 col-sm-6">
      <h4 class="bold">
         <?php
         //$tags = get_tags_in($order->id,'invoice');
         /*if(count($tags) > 0){
           echo '<i class="fa fa-tag" aria-hidden="true" data-toggle="tooltip" data-title="'.html_escape(implode(', ',$tags)).'"></i>';
        }*/
        ?>
        <a href="<?php echo admin_url('invoices/invoice/'.$order->id); ?>">
         <span id="invoice-number">
            <?php echo format_invoice_number($order->id); ?>
         </span>
      </a>
   </h4>
   <address>
      <?php //echo format_organization_info(); ?>
   </address>
</div>-->
<!--<div class="col-sm-6 text-right">
   <span class="bold"><?php echo _l('invoice_bill_to'); ?>:</span>
   <address>
      <?php echo format_customer_info($order, 'invoice', 'billing', true); ?>
   </address>
   <?php if($order->include_shipping == 1 && $order->show_shipping_on_invoice == 1){ ?>
      <span class="bold"><?php echo _l('ship_to'); ?>:</span>
      <address>
         <?php echo format_customer_info($order, 'invoice', 'shipping'); ?>
      </address>
   <?php } ?>
   <p class="no-mbot">
      <span class="bold">
         <?php echo _l('invoice_data_date'); ?>
      </span>
      <?php echo _d($order->date); ?>
   </p>
   <?php if(!empty($order->duedate)){ ?>
      <p class="no-mbot">
         <span class="bold">
            <?php echo _l('invoice_data_duedate'); ?>
         </span>
         <?php echo _d($order->duedate); ?>
      </p>
   <?php } ?>
   <?php if($order->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1){ ?>
      <p class="no-mbot">
         <span class="bold"><?php echo _l('sale_agent_string'); ?>: </span>
         <?php echo get_staff_full_name($order->sale_agent); ?>
      </p>
   <?php } ?>
   <?php if($order->project_id != 0 && get_option('show_project_on_invoice') == 1){ ?>
      <p class="no-mbot">
         <span class="bold"><?php echo _l('project'); ?>:</span>
         <?php echo get_project_name_by_id($order->project_id); ?>
      </p>
   <?php } ?>
   <?php $pdf_custom_fields = get_custom_fields('invoice',array('show_on_pdf'=>1));
   foreach($pdf_custom_fields as $field){
    $value = get_custom_field_value($order->id,$field['id'],'invoice');
    if($value == ''){continue;} ?>
    <p class="no-mbot">
      <span class="bold"><?php echo $field['name']; ?>: </span>
      <?php echo $value; ?>
   </p>
<?php } ?>
</div>-->
</div>
<!--<div class="row">
   <div class="col-md-12">
      <div class="table-responsive">
         <?php
         //$items = get_items_table_data($order, 'invoice', 'html', true);
         //echo $items->table();
         ?>
      </div>
   </div>
   <div class="col-md-5 col-md-offset-7">
      <table class="table text-right">
         <tbody>
            <tr id="subtotal">
               <td><span class="bold"><?php echo _l('invoice_subtotal'); ?></span>
               </td>
               <td class="subtotal">
                  <?php echo app_format_money($order->subtotal, $order->currency_name); ?>
               </td>
            </tr>
            <?php if(is_sale_discount_applied($order)){ ?>
               <tr>
                  <td>
                     <span class="bold"><?php echo _l('invoice_discount'); ?>
                     <?php if(is_sale_discount($order,'percent')){ ?>
                        (<?php echo app_format_number($order->discount_percent,true); ?>%)
                        <?php } ?></span>
                     </td>
                     <td class="discount">
                        <?php echo '-' . app_format_money($order->discount_total, $order->currency_name); ?>
                     </td>
                  </tr>
               <?php } ?>
               <?php
               /*foreach($items->taxes() as $tax){
                 echo '<tr class="tax-area"><td class="bold">'.$tax['taxname'].' ('.app_format_number($tax['taxrate']).'%)</td><td>'.app_format_money($tax['total_tax'], $order->currency_name).'</td></tr>';
              }*/
              ?>
              <?php if((int)$order->adjustment != 0){ ?>
               <tr>
                  <td>
                     <span class="bold"><?php echo _l('invoice_adjustment'); ?></span>
                  </td>
                  <td class="adjustment">
                     <?php echo app_format_money($order->adjustment, $order->currency_name); ?>
                  </td>
               </tr>
            <?php } ?>
            <tr>
               <td><span class="bold"><?php echo _l('invoice_total'); ?></span>
               </td>
               <td class="total">
                  <?php echo app_format_money($order->total, $order->currency_name); ?>
               </td>
            </tr>
            <?php if(count($order->payments) > 0 && get_option('show_total_paid_on_invoice') == 1) { ?>
               <tr>
                  <td><span class="bold"><?php echo _l('invoice_total_paid'); ?></span></td>
                  <td>
                     <?php echo '-' . app_format_money(sum_from_table(db_prefix().'invoicepaymentrecords',array('field'=>'amount','where'=>array('invoiceid'=>$order->id))), $order->currency_name); ?>
                  </td>
               </tr>
            <?php } ?>
            <?php if(get_option('show_credits_applied_on_invoice') == 1 && $credits_applied = total_credits_applied_to_invoice($order->id)){ ?>
               <tr>
                  <td><span class="bold"><?php echo _l('applied_credits'); ?></span></td>
                  <td>
                     <?php echo '-' . app_format_money($credits_applied, $order->currency_name); ?>
                  </td>
               </tr>
            <?php } ?>
            <?php if(get_option('show_amount_due_on_invoice') == 1 && $order->status != Invoices_model::STATUS_CANCELLED) { ?>
               <tr>
                  <td><span class="<?php if($order->total_left_to_pay > 0){echo 'text-danger ';} ?>bold"><?php echo _l('invoice_amount_due'); ?></span></td>
                  <td>
                     <span class="<?php if($order->total_left_to_pay > 0){echo 'text-danger';} ?>">
                        <?php echo app_format_money($order->total_left_to_pay, $order->currency_name); ?>
                     </span>
                  </td>
               </tr>
            <?php } ?>
         </tbody>
      </table>
   </div>
</div>-->
<!--<?php if(count($order->attachments) > 0){ ?>
   <div class="clearfix"></div>
   <hr />
   <p class="bold text-muted"><?php echo _l('invoice_files'); ?></p>
   <?php foreach($order->attachments as $attachment){
      $attachment_url = site_url('download/file/sales_attachment/'.$attachment['attachment_key']);
      if(!empty($attachment['external'])){
         $attachment_url = $attachment['external_link'];
      }
      ?>
      <div class="mbot15 row inline-block full-width" data-attachment-id="<?php echo $attachment['id']; ?>">
         <div class="col-md-8">
            <div class="pull-left"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
            <a href="<?php echo $attachment_url; ?>" target="_blank"><?php echo $attachment['file_name']; ?></a>
            <br />
            <small class="text-muted"> <?php echo $attachment['filetype']; ?></small>
         </div>
         <div class="col-md-4 text-right">
            <?php if($attachment['visible_to_customer'] == 0){
               $icon = 'fa-toggle-off';
               $tooltip = _l('show_to_customer');
            } else {
               $icon = 'fa-toggle-on';
               $tooltip = _l('hide_from_customer');
            }
            ?>
            <a href="#" data-toggle="tooltip" onclick="toggle_file_visibility(<?php echo $attachment['id']; ?>,<?php echo $order->id; ?>,this); return false;" data-title="<?php echo $tooltip; ?>"><i class="fa <?php echo $icon; ?>" aria-hidden="true"></i></a>
            <?php if($attachment['staffid'] == get_staff_user_id() || is_admin()){ ?>
               <a href="#" class="text-danger" onclick="delete_invoice_attachment(<?php echo $attachment['id']; ?>); return false;"><i class="fa fa-times"></i></a>
            <?php } ?>
         </div>
      </div>
   <?php } ?>
<?php } ?>-->
<!--<hr />
<?php if($order->clientnote != ''){ ?>
   <div class="col-md-12 row mtop15">
      <p class="bold text-muted"><?php echo _l('invoice_note'); ?></p>
      <p><?php echo $order->clientnote; ?></p>
   </div>
<?php } ?>
<?php if($order->terms != ''){ ?>
   <div class="col-md-12 row mtop15">
      <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
      <p><?php echo $order->terms; ?></p>
   </div>
<?php } ?>-->
</div>
