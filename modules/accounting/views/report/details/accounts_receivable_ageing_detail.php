<div id="accordion">
  <div class="card">
    <h3 class="text-center"><?php echo get_option('companyname'); ?></h3>
    <h4 class="text-center"><?php echo _l('accounts_receivable_ageing_detail'); ?></h4>
    <p class="text-center"><?php echo _d($data_report['to_date']); ?></p>
    <table class="tree">
      <thead>
        <tr class="tr_header">
          <th><?php echo _l('invoice_payments_table_date_heading'); ?></th>
          <th><?php echo _l('transaction_type'); ?></th>
          <th><?php echo _l('acc_no'); ?></th>
          <th><?php echo _l('customer'); ?></th>
          <th><?php echo _l('invoice_add_edit_duedate'); ?></th>
          <th class="total_amount"><?php echo _l('amount'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
         $row_index = 1; 
         $parent_index = 1; 
              $i = 0;
         $total = 0; 
         ?>

         <tr class="treegrid-10001 parent-node expanded">
            <td class="parent"><?php echo _l('current'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
         <?php foreach ($data_report['data']['current'] as $val) {
              $row_index += 1;
              $total += $val['amount'];
            if($i == 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td class="total_amount">
                  <strong>...</strong>
                  </td>
                </tr>
              <? 
              }elseif($i < 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <?php echo _d($val['date']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['type']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['number']); ?> 
                  </td>
                  <td>
                  <?php echo get_company_name($val['customer']); ?> 
                  </td>
                  <td>
                  <?php echo _d($val['duedate']); ?> 
                  </td>
                  <td class="total_amount">
                  <?php echo app_format_money($val['amount'], $currency->name); ?> 
                  </td>
                </tr>
              <?php }
              $i++;}
            $row_index += 1;
           ?>
          
           <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10001 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l('current')); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total, $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; 
              $i = 0;
           $total = 0; ?>

         <tr class="treegrid-10000 parent-node expanded">
            <td class="parent"><?php echo _l('1_30_days_past_due'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
         <?php foreach ($data_report['data']['1_30_days_past_due'] as $val) {
              $row_index += 1;
              $total += $val['amount'];
            if($i == 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td class="total_amount">
                  <strong>...</strong>
                  </td>
                </tr>
              <? 
              }elseif($i < 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <?php echo _d($val['date']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['type']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['number']); ?> 
                  </td>
                  <td>
                  <?php echo get_company_name($val['customer']); ?> 
                  </td>
                  <td>
                  <?php echo _d($val['duedate']); ?> 
                  </td>
                  <td class="total_amount">
                  <?php echo app_format_money($val['amount'], $currency->name); ?> 
                  </td>
                </tr>
              <?php }
              $i++; }
            $row_index += 1;
           ?>
          
           <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l('1_30_days_past_due')); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total, $currency->name); ?></td>
          </tr>

          <?php
         $row_index++; 
              $i = 0;
         $total = 0; 
         ?>
         <tr class="treegrid-10001 parent-node expanded">
            <td class="parent"><?php echo _l('31_60_days_past_due'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
         <?php foreach ($data_report['data']['31_60_days_past_due'] as $val) {
              $row_index += 1;
              $total += $val['amount'];
            if($i == 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td class="total_amount">
                  <strong>...</strong>
                  </td>
                </tr>
              <? 
              }elseif($i < 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <?php echo _d($val['date']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['type']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['number']); ?> 
                  </td>
                  <td>
                  <?php echo get_company_name($val['customer']); ?> 
                  </td>
                  <td>
                  <?php echo _d($val['duedate']); ?> 
                  </td>
                  <td class="total_amount">
                  <?php echo app_format_money($val['amount'], $currency->name); ?> 
                  </td>
                </tr>
              <?php }
              $i++;}
            $row_index += 1;
           ?>
          
           <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10001 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l('31_60_days_past_due')); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total, $currency->name); ?></td>
          </tr>
          <?php $row_index += 1; 
              $i = 0;
           $total = 0; ?>
          <tr class="treegrid-10000 parent-node expanded">
            <td class="parent"><?php echo _l('61_90_days_past_due'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
         <?php foreach ($data_report['data']['61_90_days_past_due'] as $val) {
              $row_index += 1;
              $total += $val['amount'];
            if($i == 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td class="total_amount">
                  <strong>...</strong>
                  </td>
                </tr>
              <? 
              }elseif($i < 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <?php echo _d($val['date']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['type']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['number']); ?> 
                  </td>
                  <td>
                  <?php echo get_company_name($val['customer']); ?> 
                  </td>
                  <td>
                  <?php echo _d($val['duedate']); ?> 
                  </td>
                  <td class="total_amount">
                  <?php echo app_format_money($val['amount'], $currency->name); ?> 
                  </td>
                </tr>
              <?php }
              $i++;
           }
            $row_index += 1;
           ?>
          
           <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l('61_90_days_past_due')); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total, $currency->name); ?></td>
          </tr>

           <?php $row_index += 1; 
           $total = 0; ?>
          <tr class="treegrid-10000 parent-node expanded">
            <td class="parent"><?php echo _l('91_and_over'); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>

         <?php 
              $i = 0;
              foreach ($data_report['data']['91_and_over'] as $val) {
              $row_index += 1;
              $total += $val['amount'];
              if($i == 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td>
                  <strong>...</strong>
                  </td>
                  <td class="total_amount">
                  <strong>...</strong>
                  </td>
                </tr>
              <? 
              }elseif($i < 200){ ?>
                <tr class="treegrid-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 ">
                  <td>
                  <?php echo _d($val['date']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['type']); ?> 
                  </td>
                  <td>
                  <?php echo html_entity_decode($val['number']); ?> 
                  </td>
                  <td>
                  <?php echo get_company_name($val['customer']); ?> 
                  </td>
                  <td>
                  <?php echo _d($val['duedate']); ?> 
                  </td>
                  <td class="total_amount">
                  <?php echo app_format_money($val['amount'], $currency->name); ?> 
                  </td>
                </tr>
              <?php }
              $i++;
            ?>

          <?php }
            $row_index += 1;
           ?>
          
           <tr class="treegrid-total-<?php echo html_entity_decode($row_index); ?> treegrid-parent-10000 parent-node expanded tr_total">
            <td class="parent"><?php echo _l('total_for', _l('91_and_over')); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="total_amount"><?php echo app_format_money($total, $currency->name); ?></td>
          </tr>
      </tbody>
    </table>
  </div>
</div>