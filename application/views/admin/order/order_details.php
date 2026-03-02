<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
	<div class="content">
		<div class="row">
		    <div class="col-md-8">
		        <div class="panel_s">
                  <div class="panel-body">
                      <div id="invoice-preview">
                          <div class="row mtop10">
            
            <div class="col-md-12 _buttons">
               <div class="visible-xs">
                  <div class="mtop10"></div>
               </div>
               <div class="pull-left">
                  <?php
                     $_tooltip = _l('invoice_sent_to_email_tooltip');
                     $_tooltip_already_send = '';
                     if($invoice->sent == 1 && is_date($invoice->datesend)){
                      $_tooltip_already_send = _l('invoice_already_send_to_client_tooltip',time_ago($invoice->datesend));
                     }
                     ?>
                  <?php if(has_permission('orders','','edit')){ 
                  if(is_null($order->ChallanID)){ ?>
                  <a href="<?php echo admin_url('order/order/'.$order->OrderID); ?>" data-toggle="tooltip" title="edit order" class="btn btn-default btn-with-tooltip" data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>
                  <?php }} ?>
                  
                   
                   <?php
                    
                       if(is_null($order->ChallanID)){
                           if(has_permission('challan','','create')){
                   ?>
                      <!-- <a href="<?php echo admin_url('challan/challan/'.$order->OrderID); ?>"   class="mleft10 pull-right btn btn-success<?php if($invoice->status == Invoices_model::STATUS_PAID || $invoice->status == Invoices_model::STATUS_CANCELLED){echo ' disabled';} ?>">
                         <i class="fa fa-plus-square"></i> Create Challan </a>-->
                   <?php 
                       }}else { 
                           
                       if(has_permission('challan','','view') || has_permission('challan','','view_own')){
                       ?>
                       <a href="<?php echo admin_url('challan/edit_challan/'.$order->ChallanID); ?>"  target="_blank" class="mleft10 pull-right btn btn-success<?php if($invoice->status == Invoices_model::STATUS_PAID || $invoice->status == Invoices_model::STATUS_CANCELLED){echo ' disabled';} ?>">
                     <i class="fa fa-eye"></i> View Challan </a>
                       <?php
                   }}  ?>
                  
                  <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print Order</a>
                 
               </div>
            </div>
         </div>
   <div class="row">
     
   <?php
   
    $state_detail = get_state_detail($order->client->state);
    $state_detail2 = get_state_detail($order->client2->state);
    
   ?>
  
   <div class="col-md-12">
       <h4 style="text-align: center;">Order Detail</h4>
       <table style="width: 100%;" id="party_detail" border="1" cellspacing="0" cellpadding="2">
           <tr>
               <td colspan="4" align="center"><?php echo $selected_company_details->FIRMNAME;?></td>
           </tr>
           <tr>
               <td colspan="4" align="center"><?php echo $selected_company_details->ADDRESS1.' '.$selected_company_details->ADDRESS2;?></td>
           </tr>
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
           <td >: <?php echo $order->client2->company; ?></td>
           </tr>
           <tr>
           <td >Mob </td>
           <td>: <?php echo $order->client->phonenumber; ?></td>
           <td >Mob </td>
           <td >: <?php echo $order->client2->phonenumber; ?></td>
           </tr>
           <tr>
           <td >Address </td>
           <td >: <?php echo $order->client->address." ".$order->client->address3." ".$order->client->pincode; ?> </td>
           <td >Address </td>
           <td >: <?php echo $order->client2->address." ".$order->client2->address3." ".$order->client2->pincode; ?></td>
           </tr>
           
           <tr>
           <td >GSTIN </td>
           <td>: <?php echo $order->client->vat; ?></td>
           
           <td>GSTIN </td>
           <td >: <?php echo $order->client2->vat; ?></td>
           
           </tr>
           <tr>
           <td >State </td>
           <td >: <?php echo $state_detail->state_name; ?></td>
           
           <td >State </td>
           <td >: <?php echo $state_detail2->state_name; ?></td>
           
           </tr>
        </table>
        <br>
    
        <?php
        //$inv_item = get_item_by_order_id($order->OrderID);
       
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
            <thead>
                <tr style="background: #415164;">
        <th width="1%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Sr. No.</th>
        <th width="<?php echo $item_name_width;  ?>" <?php echo $rowspan; ?> style="color:#fff;border-color:#333;">Product Description</th>
        <th width="<?php echo $hsn_width;  ?>" <?php echo $rowspan; ?> style="color:#fff;border-color:#333;">HSN Code</th>
        <th width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Pkg</th>
        <th width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Qty.</th>
        <th width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Qty (CS/CR).</th>
        <th width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Rate</th>
        <th width="8%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Amt</th>
        <th width="5%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Disc. Amt</th>
        <th width="8%" <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Taxable Amt</th>
        <?php if($order->client->state == "UP"){
            ?>
        <th colspan="2" style="text-align:center;color:#fff;border-color:#333;">CGST</th>
        <th colspan="2" style="text-align:center;color:#fff;border-color:#333;">SGST</th>
        <?php 
            
        } else{
            ?>
        <th colspan="2" style="text-align:center;color:#fff;border-color:#333;">IGST</th>
    <?php } ?>
        <th <?php echo $rowspan; ?> style="text-align:center;color:#fff;border-color:#333;">Total Amt</th>
        </tr>
        <?php
        if($order->client->state == "UP"){
            ?>
            <tr style="background: #415164;color: #fff;">
        
            <th style="text-align:center;color:#fff;border-color:#333;">Rate</th>
            <th style="text-align:center;color:#fff;border-color:#333;">Amt</th>
            <th style="text-align:center;color:#fff;border-color:#333;">Rate</th>
            <th style="text-align:center;color:#fff;border-color:#333;">Amt</th>
            </tr>   
       <?php 
            
        }else {
        ?>
            <tr style="background: #415164;">
            
            <th style="text-align:center;color:#fff;border-color:#333;">Rate</th>
            <th style="text-align:center;color:#fff;border-color:#333;">Amt</th>
            
            </tr> 
        <?php 
            
        }
        ?>
            </thead>
        
        <?php
        $i = 1;
        $qty = 0;
        $totcscsr = 0;
        $amt = 0;
        $dis_amt = 0;
        $taxable_amt = 0;
        $csgst_total = 0;
        $gst_total = 0;
        $order_total = 0;
        
        foreach ($order->items as $item) {
            
            //$item_detail = get_item_by_item_code($item['ItemID']);
        if($item['NetOrderAmt'] == "0.00"){
            
        }else{
        ?>
       <tr>
           <td style="text-align:center;"><?php echo $i; ?></td>
           <td class="description" align="left;" width="<?php echo $item_name_width; ?>"><?php echo $item['description']; ?></td>
           <td width="<?php echo $hsn_width; ?>" style="text-align:center;"><?php echo $item['hsn_code']; ?></td>
           <td style="text-align:right;"><?php echo  (int) $item['CaseQty']; ?></td>
           <td style="text-align:right;"><?php 
           if(is_null($item['eOrderQty'])){
               echo  (int) $item['OrderQty'] ;// / $item['CaseQty'] Add If Needed
           }else{
               echo  (int) $item['eOrderQty'] ; // / $item['CaseQty'] Add If Needed
           }
            ?></td>
          <?php  
          if(is_null($item['eOrderQty'])){
              $ordqty = $item['OrderQty'];// / $item['CaseQty'] Add If Needed
              $qty = $qty + $item['OrderQty'];// / $item['CaseQty'] Add If Needed
          }else{
              $ordqty = $item['eOrderQty'] ;// / $item['CaseQty'] Add If Needed
              $qty = $qty + $item['eOrderQty'] ;// / $item['CaseQty'] Add If Needed
          }
              $totcscsr = $totcscsr + number_format($ordqty/$item['CaseQty'], 2, '.', '') ;
           ?>
           <td style="text-align:right;"><?php echo number_format($ordqty/$item['CaseQty'], 2, '.', ''); ?></td>
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
        
    <tr>
        <?php
        }
        ?>
    
    <td colspan="4" style="text-align:center;">Total</td>
    <td style="text-align:right;"><?php echo $qty; ?></td>
    <td style="text-align:right;"><?php echo round($totcscsr); ?></td>
    <td colspan=""></td>
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
		 </div>
	</div>
</div>



<?php init_tail(); ?>

</body>
<style>
     td, th {
    padding: 2px;
    border: 1px solid;
}
</style>
<!--<script type="text/javascript">
 function printPage(){
        var tableData = '<table border="1" cellpadding="0" cellspacing="0">'+document.getElementById('party_detail').innerHTML+'</table>';
         tableData += '<table border="1" cellpadding="0" cellspacing="0">'+document.getElementById('order_table').innerHTML+'</table>';
         //tableData += '<style>'+document.getElementsByTagName('style')[1].innerHTML+'</style>';
         
        var data = '<button onclick="window.print()" id="print_button">Print this page</button>'+tableData;  
        
        myWindow=window.open('','','width=1400,height=1000');
        myWindow.innerWidth = screen.width;
        myWindow.innerHeight = screen.height;
        myWindow.screenX = 0;
        myWindow.screenY = 0;
        myWindow.document.write(data);
        myWindow.focus();
    };
 </script>-->
 <script type="text/javascript">
 function printPage(){
        
        
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
        var tableData = '<table border="1" cellpadding="0" cellspacing="0">'+document.getElementById('party_detail').innerHTML+'</table>';
         tableData += '<table border="1" cellpadding="0" cellspacing="0">'+document.getElementById('order_table').innerHTML+'</table>';
         
        var print_data = stylesheet+tableData
   newWin= window.open("");
   newWin.document.write(print_data);
   newWin.print();
   newWin.close();
    };
 </script>
</html>