<div id="accordion">
  <div class="card">
    <div style="display:none;">
        <h4 class="text-center">Account Ledger</h4>
    <h5><?php echo $account_name->company; ?></h5>
    <p class="text-center"><?php echo _d($from_date) .' - '. _d($to_date); ?></p>
    </div>
    
    <table class="tree table table-striped table-bordered daily_report">
      <thead>
        <tr class="tr_header">
          <th>Passed From</th>
          <th>Voucher ID</th>
          <th>Date</th>
          <th>Narration</th>
          <th>Debit</th>
          <th class="total_amount">Credit</th>
          <th class="total_amount">Balance</th>
        </tr>
      </thead>
      <tbody>
          
        <?php
        //print_r($total_bal);
        $account_bal = $total_bal->BAL1 + $total_bal->BAL2 + $total_bal->BAL3 + $total_bal->BAL4 + $total_bal->BAL5 + $total_bal->BAL6 + $total_bal->BAL7 + $total_bal->BAL8 + $total_bal->BAL9 + $total_bal->BAL10 + $total_bal->BAL11 + $total_bal->BAL12 + $total_bal->BAL13;
        
        ?>
        
        <?php
        $new_acc_bal = $total_bal->BAL1;
        $opening_bal = $total_bal->BAL1;
        $i = 1;
        $from_date = $from_date . ' 00:00:00';
        $from_date = date('Y-m-d',strtotime($from_date));
        //echo $from_date;
        $to_date = $to_date . ' 23:59:59';
        $to_date = date('Y-m-d',strtotime($to_date));
        $total_debit = 0;
        $total_credit = 0;
        
       
        foreach ($data_report as $key => $value) {
            //$led_from_date = strtotime($value["Transdate"]);
            $led_from_date = date('Y-m-d',strtotime($value["Transdate"]));
            $led_to_date = date('Y-m-d',strtotime($value["Transdate"]));
            
            
                
            if($led_from_date >= $from_date && $led_from_date <= $to_date){
                if($i==1){
                    if($opening_bal>0){
                        $ob_dr_cr = "Dr";
                    }else{
                        $ob_dr_cr = "Cr";
                    }
                    
                    ?>
                <tr style="color:red;">
                    <td></td>
                    <td></td>
                    <td><?php echo $from_date; ?></td>
                    <td>Opening Balance</td>
                    <td align="right">
                        <?php
                        if($opening_bal>=0){
                            $new_bal = abs($opening_bal);
                            $total_debit = $total_debit + $new_bal;
                            $new_bal = abs($new_bal);
                            echo $new_bal;
                        }
                        ?>
                    </td>
                    <td align="right">
                        <?php
                        if($opening_bal<0){
                            $total_credit = $total_credit + $opening_bal;
                            $opening_bal = abs($opening_bal);
                            echo $opening_bal;
                        }
                        ?>
                    </td>
                    <td align="right"><?php 
                    $opening_bal_new = abs($opening_bal);
                    echo $opening_bal_new." ".$ob_dr_cr; ?></td>
                </tr>
                <?php
                }
            ?>
            
            <tr>
                <td><?php echo $value["PassedFrom"];?></td>
                <td><?php echo $value["VoucherID"];?></td>
                <td><?php echo substr($value["Transdate"],0,10);?></td>
                <td><?php echo $value["Narration"];?></td>
                <td align="right"><?php 
                if($value["TType"]=="D"){
                    
                    $new_acc_bal = $new_acc_bal + $value["Amount"];
                    $value = abs($value["Amount"]);
                    $total_debit = $total_debit + $value;
                    echo $value; 
                }
                ?></td>
                <td align="right"><?php 
                if($value["TType"]=="C"){
                    
                    $new_acc_bal = $new_acc_bal - $value["Amount"];
                    $value = abs($value["Amount"]);
                    $total_credit = $total_credit + $value;
                    echo $value;
                     
                }
                ?></td>
                <td align="right"><?php 
                $new_acc_bal2 = abs($new_acc_bal);
                if($new_acc_bal>0){
                        $nab_dr_cr = "Dr";
                    }else{
                        $nab_dr_cr = "Cr";
                    }
                echo round($new_acc_bal2,2)." ".$nab_dr_cr; ?></td>
            </tr>
            <?php
            $i++;
            }else {
                if($value["TType"]=="D"){
                    
                    $new_acc_bal = $new_acc_bal + $value["Amount"];
                    //echo $value["Amount"]; 
                }
            if($value["TType"]=="C"){
                    
                    $new_acc_bal = $new_acc_bal - $value["Amount"];
                    //echo round($value["Amount"],2); 
                }
                $opening_bal = $new_acc_bal;
                
            }
        ?>
            
        <?php
        
            }
        ?>
        <?php 
        if($data_report){ 
         if($i>1){
             ?>
         
        <tr style="color:red;">
            <td></td>
            <td></td>
            <td></td>
            <td>Closing Balance</td>
            <td align="right"><?php echo round($total_debit,2); ?></td>
            <td align="right"><?php echo round($total_credit,2); ?></td>
            <td align="right"><?php echo round($new_acc_bal2,2)." ".$nab_dr_cr; ?></td>
        </tr>
        <?php }else {
            if($data_report == "1"){
                ?>
                <tr style="color:red;">
            
            <td colspan="7">No record found...</td>
            
        </tr>
         <?php   }else{
             ?>
             <tr style="color:red;">
            
            <td colspan="7">No record found...</td>
            
        </tr>
        <?php
         }
        ?>
        
        <?php
        } } ?>
      </tbody>
    </table>
  </div>
</div>