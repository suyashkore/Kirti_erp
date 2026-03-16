<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .th_total {
	padding-right: 10px;
    }
</style>

<div id="wrapper">
    <div class="panel_s">
        <div class="panel-body">
			<nav aria-label="breadcrumb" >
				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
					<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
					<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
					<li class="breadcrumb-item active" aria-current="page"><b>Profit Loss Report</b></li>
				</ol>
			</nav>
			<hr class="hr_style">
			<div class="row ">
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
							$from_date = '01/04/20'.$fy_new;
							}else{
							$from_date = date('01/04/Y');
							$to_date = date('d/m/Y');
						}
						if(!empty($Date)){
							$to_date = $Date;
						}
						if(!empty($FromDate)){
							$from_date = $FromDate;
						}
					?> 
					<?php //$cur_date = _d(date('Y-m-d')); ?>
					<?php echo render_date_input('from_date','From Date',$from_date); ?>
				</div>
				<div class="col-md-2">
					<?php echo render_date_input('as_on_date','To Date',$to_date); ?>
				</div>
				<div class="col-md-6">
					<br>
					<button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
					<?php if (has_permission_new('profitlossreport', '', 'print')) {
					?>
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" onclick="printPage();">Print</a>
                    <?php } ?>
				</div>
			</div>
            <div class="row ">
                <div class="col-md-8">
                    <?php
						$FY = $this->session->userdata('finacial_year');
						$lastFy = $FY - 1;
						$fy_ = $FY + 1;
						$lastFy_ = $lastFy + 1;
						$CurrYrLastDate = '31/03/20' . $fy_;
						$LastYrLastDate = '31/03/20' . $lastFy_;
						if(!empty($Date)){
						?>
						<div class="page" id="DivIdToPrint">
							<div id="accordion">
								<div class="card">
									<table class="tree">
										<thead>
											<tr class="tr_header">
												<th style=""><b>Particular</b></th>
												<!--<th class="th_total"><b>Note No.</b></th>-->
												<th class="th_total"><b>
                                                    <?php echo $CurrYrLastDate; ?>
												</b>
												</th>
												<th class="th_total"><b>
                                                    <?php echo $LastYrLastDate; ?>
												</b>
												</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$MainCounter = 1000;
												$SubCounter1 = 2000;
												$SubCounter2 = 3000;
												$SubCounter3 = 4000;
												$SubCounter4 = 5000;
												$SubCounter5 = 6000;
												$TotalRevenueIncome = 0;
												$TotalRevenueIncomePre = 0;
											?>
											<?php $MainCounter++;?>
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<?php
                                                    $TotalSaleRtn = ($TransactionAmt->FrtRtnCurrentYear + $TransactionAmt->DFrtRtnCurrentYear);
                                                    $TotalSaleRtnPre = ($TransactionAmt->FrtRtnPriviousYear + $TransactionAmt->DFrtRtnPriviousYear);
                                                    $TotalRevenueIncome += $TransactionAmt->SaleCurrentYear - $TotalSaleRtn;
                                                    $TotalRevenueIncomePre += $TransactionAmt->SalePriviousYear - $TotalSaleRtnPre;
												?>
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;"><b>I. Revenue from Operation</b></td>
												<td class="parent col-id-particular" style="text-align:left;font-weight:700;font-size: 14px;text-align:right;"><b><?php echo  number_format($TotalRevenueIncome, 2, '.', '') ?></b></td>
												<td class="parent col-id-particular" style="text-align:left;font-weight:700;font-size: 14px;text-align:right;"><b><?php echo  number_format($TotalRevenueIncomePre, 2, '.', '') ?></b></td>
											</tr>
                                            <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no" style="font-size:13px;font-weight:500;">Sale Amount</td>
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($TransactionAmt->SaleCurrentYear, 2, '.', '') ?></td>
                                                
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($TransactionAmt->SalePriviousYear, 2, '.', '') ?></td>
											</tr>
                                            <?php $SubCounter1++;?>
                                            <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
                                                <td class="col-id-sr-no" style="font-size:13px;font-weight:500;">Sale Return Amount</td>
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($TotalSaleRtn, 2, '.', '') ?></td>
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($TotalSaleRtnPre, 2, '.', '') ?></td>
											</tr>
                                            <?php $SubCounter1++;?>
                                            
											<?php $MainCounter++;?>
											
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">II. Other Income</td>
												<?php
													$TotalRevenueIncome += $OtherIncome->CurrentYear;
													$TotalRevenueIncomePre += $OtherIncome->PriviousYear;
												?>
												<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($OtherIncome->CurrentYear, 2, '.', '') ?></td>
												<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($OtherIncome->PriviousYear, 2, '.', '') ?></td>
											</tr>
											<?php
												foreach($OtherIncome->nestedData as $OthKey=>$OthVal){
												?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $OthVal["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $OthVal["Group1Name"]; ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($OthVal["Group1ClsBal"], 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($OthVal["Group1ClsBalPre"], 2, '.', '') ?></td>
												</tr>
												<?php
													foreach($OthVal["SubGroups2"] as $Oth2Key=>$Oth2Val){
													?>
                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $Oth2Val["SubActGroupID"] ?>">
                                                        <td class="col-id-particular"><?php echo $Oth2Val["SubGroupName"]; ?></td> 
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($Oth2Val["Group2ClsBal"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($Oth2Val["Group2ClsBalPre"], 2, '.', '') ?></td>
													</tr>
													<?php
														foreach($Oth2Val["Accounts"] as $OthActKey=>$OthActVal){
														?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $OthActVal["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $OthActVal["AccountName"]; ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($OthActVal["AccountClsBal"], 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($OthActVal["AccountClsBalPre"], 2, '.', '') ?></td>
														</tr>            
														<?php
															$SubCounter3++;
														}
														$SubCounter2++;
													}
													$SubCounter1++;
												}
											?>
											
											<?php $MainCounter++;?>
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">III. Total Revenue (I + II)</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncomePre, 2, '.', '') ?></td>
											</tr>
											<?php $MainCounter++;?>
											<?php
												$COGS =  $OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear - $ClosingInventoryAmt->CurrentYear;
												$COGSPre = $OpeningInventoryAmt->PriviousYear + $TransactionAmt->PurchPriviousYear + $DirectExp->PriviousYear - $ClosingInventoryAmt->PriviousYear;
											?>
											<?php
												
												$TotalExp = $COGS + $EMPBenData->CurrentYear + $FinanceCostData->CurrentYear + $DeprecData->CurrentYear + $OtherExpensesData->CurrentYear;
												$TotalExpPre = $COGSPre + $EMPBenData->PriviousYear + $FinanceCostData->PriviousYear + $DeprecData->PriviousYear + $OtherExpensesData->PriviousYear;
											?>
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded" id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">IV. Expenses</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalExp, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalExpPre, 2, '.', '') ?></td>
											</tr>
											
											<tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
												<td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">1. Cost of Goods Sold (COGS)</td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($COGS, 2, '.', '') ?></td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($COGSPre, 2, '.', '') ?></td>
											</tr>   
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node expanded" id="subgroup">
                                                <td class="col-id-sr-no" style="font-size:12px;font-weight:500;">Opening Amt - add</td>
												
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($OpeningInventoryAmt->CurrentYear, 2, '.', '') ?></td>
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($OpeningInventoryAmt->PriviousYear, 2, '.', '') ?></td>
											</tr>
                                            <?php $SubCounter2++;?>
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node expanded " id="subgroup">
                                                <td class="col-id-particular" style="font-size:12px;font-weight:500;">Purchase Amt - add</td>
												
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($TransactionAmt->PurchCurrentYear, 2, '.', '') ?></td>
                                                <td style="font-size:12px;font-weight:500;text-align:right;"><?php echo  number_format($TransactionAmt->PurchPriviousYear, 2, '.', '') ?></td>
											</tr>
                                            <?php $SubCounter2++;?>
                                            
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node " id="subgroup">
                                                <td class="col-id-particular" style="font-size:12px;font-weight:500;">Direct Expense - add</td>
                                                <td style="font-size:14px;font-weight:500;text-align:right;"><?php echo  number_format($DirectExp->CurrentYear, 2, '.', '') ?></td>
                                                <td style="font-size:14px;font-weight:500;text-align:right;"><?php echo  number_format($DirectExp->PriviousYear, 2, '.', '') ?></td>
											</tr>
                                            
                                            <?php
												foreach($DirectExp->nestedData as $DEKey=>$DEVal){
												?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node" id="subgroup" data-id="<?php echo $DEVal["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $DEVal["Group1Name"]; ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DEVal["Group1ClsBal"], 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DEVal["Group1ClsBalPre"], 2, '.', '') ?></td>
												</tr>
												<?php
													foreach($DEVal["SubGroups2"] as $DE2Key=>$DE2Val){
													?>
                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node  "
                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $DE2Val["SubActGroupID"] ?>">
                                                        <td class="col-id-particular"><?php echo $DE2Val["SubGroupName"]; ?></td> 
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($DE2Val["Group2ClsBal"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($DE2Val["Group2ClsBalPre"], 2, '.', '') ?></td>
													</tr>
													<?php
														foreach($DE2Val["Accounts"] as $DEActKey=>$DEActVal){
														?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter5); ?> treegrid-parent-<?php echo $SubCounter4; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $DEActVal["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $DEActVal["AccountName"]; ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($DEActVal["AccountClsBal"], 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($DEActVal["AccountClsBalPre"], 2, '.', '') ?></td>
														</tr>            
														<?php
															$SubCounter5++;
														}
														$SubCounter4++;
													}
													$SubCounter3++;
												}
											?>
											
                                            <?php $SubCounter2++;?>
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
											style="font-size:13px;" id="subgroup">
                                                <td class="col-id-particular">Closing Amt - less</td>
												
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($ClosingInventoryAmt->CurrentYear, 2, '.', '') ?></td>
                                                <td style="text-align:right;"><?php echo  number_format($ClosingInventoryAmt->PriviousYear, 2, '.', '') ?></td>
											</tr>
                                            
                                            <?php $SubCounter3++;?>
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
											style="font-size:13px;" id="subgroup">
                                                <td class="col-id-particular">RM Inventory Amt (as per last purchase amount) - Add</td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($ClosingInventoryAmt->RMCurrentYear, 2, '.', '') ?></td>
                                                <td style="text-align:right;"><?php echo  number_format($ClosingInventoryAmt->RMPriviousYear, 2, '.', '') ?></td>
											</tr>
                                            <?php $SubCounter3++;?>
                                            <tr class="treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
											style="font-size:13px;" id="subgroup">
                                                <td class="col-id-particular">FG Inventory Amt (As per rate for state : UP, Distributor Type : Test) - Add</td>
                                                <td style="text-align:right;background-color: orange;"><?php echo  number_format($ClosingInventoryAmt->FGCurrentYear, 2, '.', '') ?></td>
                                                <td style="text-align:right;"><?php echo  number_format($ClosingInventoryAmt->FGPriviousYear, 2, '.', '') ?></td>
											</tr>
                                            <?php $SubCounter3++;?>
                                            
                                            <?php $SubCounter2++;?>
                                            <?php $SubCounter1++;?>
											
											
											<tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
												<td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">2. Employee benefits expense</td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($EMPBenData->CurrentYear, 2, '.', '') ?></td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($EMPBenData->PriviousYear, 2, '.', '') ?></td>
											</tr>
											
											<?php
												foreach($EMPBenData->nestedData as $EBKey=>$EBVal){
												?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node" id="subgroup" data-id="<?php echo $EBVal["SubActGroupID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $EBVal["SubGroupName"]; ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($EBVal["Group2ClsBal"], 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($EBVal["Group2ClsBalPre"], 2, '.', '') ?></td>
												</tr>
												<?php
													foreach($EBVal["Accounts"] as $EBActKey=>$EBActVal){
													?>
                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $EBActVal["AccountID"] ?>">
                                                        <td class="col-id-particular"><?php echo $EBActVal["AccountName"]; ?></td> 
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($EBActVal["AccountClsBal"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($EBActVal["AccountClsBalPre"], 2, '.', '') ?></td>
													</tr>
													<?php
														$SubCounter3++;
													}
													$SubCounter2++;
												}
											?>
											<?php $SubCounter1++;?>
											
											<tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
												<td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">3. FINANCE COST</td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($FinanceCostData->CurrentYear, 2, '.', '') ?></td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($FinanceCostData->PriviousYear, 2, '.', '') ?></td>
											</tr>
											
											<?php
												foreach($FinanceCostData->nestedData as $FCKey=>$FCVal){
												?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node" id="subgroup" data-id="<?php echo $FCVal["SubActGroupID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $FCVal["SubGroupName"]; ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($FCVal["Group2ClsBal"], 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($FCVal["Group2ClsBalPre"], 2, '.', '') ?></td>
												</tr>
												<?php
													foreach($FCVal["Accounts"] as $FCActKey=>$FCActVal){
													?>
                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $FCActVal["AccountID"] ?>">
                                                        <td class="col-id-particular"><?php echo $FCActVal["AccountName"]; ?></td> 
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($FCActVal["AccountClsBal"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($FCActVal["AccountClsBalPre"], 2, '.', '') ?></td>
													</tr>
													<?php
														$SubCounter3++;
													}
													$SubCounter2++;
												}
											?>
											<?php $SubCounter1++;?>
											
											
											
											<tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
												<td class="parent col-id-sr-no" style="font-size:13px;font-weight:500;">4. Depreciation And Amortization Expense</td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DeprecData->CurrentYear, 2, '.', '') ?></td>
												<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DeprecData->PriviousYear, 2, '.', '') ?></td>
											</tr>
											
											<?php
												foreach($DeprecData->nestedData as $DAKey=>$DAVal){
												?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node" id="subgroup" data-id="<?php echo $DAVal["SubActGroupID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $DAVal["SubGroupName"]; ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DAVal["Group2ClsBal"], 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DAVal["Group2ClsBalPre"], 2, '.', '') ?></td>
												</tr>
												<?php
													foreach($DAVal["Accounts"] as $DAActKey=>$DAActVal){
													?>
                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $DAActVal["AccountID"] ?>">
                                                        <td class="col-id-particular"><?php echo $DAActVal["AccountName"]; ?></td> 
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($DAActVal["AccountClsBal"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($DAActVal["AccountClsBalPre"], 2, '.', '') ?></td>
													</tr>
													<?php
														$SubCounter3++;
													}
													$SubCounter2++;
												}
											?>
											<?php $SubCounter1++;?>
											
											<tr class="treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup">
                                                <td class="col-id-particular" style="font-size:13px;font-weight:500;">5. InDirect Expenses</td>
                                                <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($OtherExpensesData->CurrentYear, 2, '.', '') ?></td>
                                                <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($OtherExpensesData->PriviousYear, 2, '.', '') ?></td>
											</tr>
                                            
                                            <?php
												foreach($OtherExpensesData->nestedData as $IExpKey=>$IExpVal){
												?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node" id="subgroup" data-id="<?php echo $IExpVal["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $IExpVal["Group1Name"]; ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($IExpVal["Group1ClsBal"], 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($IExpVal["Group1ClsBalPre"], 2, '.', '') ?></td>
												</tr>
												<?php
													foreach($IExpVal["SubGroups2"] as $IExp2Key=>$IExp2Val){
													?>
                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $IExp2Val["SubActGroupID"] ?>">
                                                        <td class="col-id-particular"><?php echo $IExp2Val["SubGroupName"]; ?></td> 
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($IExp2Val["Group2ClsBal"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($IExp2Val["Group2ClsBalPre"], 2, '.', '') ?></td>
													</tr>
													<?php
														foreach($IExp2Val["Accounts"] as $IExpActKey=>$IExpActVal){
														?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter4); ?> treegrid-parent-<?php echo $SubCounter3; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $IExpActVal["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $IExpActVal["AccountName"]; ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($IExpActVal["AccountClsBal"], 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($IExpActVal["AccountClsBalPre"], 2, '.', '') ?></td>
														</tr>            
														<?php
															$SubCounter4++;
														}
														$SubCounter3++;
													}
													$SubCounter2++;
												}
											?>
											
											<?php $SubCounter1++;?>
											
											
											<?php $MainCounter++;?>
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">V. Profit before Exceptional and extraordinary items and tax(III- IV)</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncomePre - $TotalExpPre, 2, '.', '') ?></td>
											</tr>
											<?php $MainCounter++;?>
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">VI. Exceptional Items</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
											</tr>
											<?php $MainCounter++;?>
											
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">VII. Profit before extraordinary items and tax (V - VI)</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncomePre - $TotalExpPre, 2, '.', '') ?></td>
											</tr>
											<?php $MainCounter++;?>
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">VIII. Extraordinary Items</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format(0, 2, '.', '') ?></td>
											</tr>
											<?php $MainCounter++;?>
											
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">IX. Profit before tax(VII - VIII)</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncomePre - $TotalExpPre, 2, '.', '') ?></td>
											</tr>
											<?php $MainCounter++;?>
											
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">X. Tax Expense</td>
												<?php
													$TotalRevenueIncome -= $TaxExpense->CurrentYear;
													$TotalRevenueIncomePre -= $TaxExpense->PriviousYear;
												?>
												<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TaxExpense->CurrentYear, 2, '.', '') ?></td>
												<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TaxExpense->PriviousYear, 2, '.', '') ?></td>
											</tr>
											
											<?php
												foreach($TaxExpense->nestedData as $DEKey=>$DEVal){
												?>
                                                <tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $DEVal["AccountID"] ?>">
                                                    <td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $DEVal["Group1Name"]; ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DEVal["Group1ClsBal"], 2, '.', '') ?></td>
                                                    <td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DEVal["Group1ClsBalPre"], 2, '.', '') ?></td>
												</tr>
												<?php
													foreach($DEVal["SubGroups2"] as $DE2Key=>$DE2Val){
													?>
                                                    <tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
                                                    style="font-size:13px;" id="subgroup" data-id="<?php echo $DE2Val["SubActGroupID"] ?>">
                                                        <td class="col-id-particular"><?php echo $DE2Val["SubGroupName"]; ?></td> 
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($DE2Val["Group2ClsBal"], 2, '.', '') ?></td>
                                                        <td style="text-align:right;font-weight:500;"><?php echo  number_format($DE2Val["Group2ClsBalPre"], 2, '.', '') ?></td>
													</tr>
													<?php
														foreach($DE2Val["Accounts"] as $DEActKey=>$DEActVal){
														?>
                                                        <tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
                                                        style="font-size:13px;" id="subgroup" data-id="<?php echo $DEActVal["AccountID"] ?>">
                                                            <td class="col-id-particular"><?php echo $DEActVal["AccountName"]; ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($DEActVal["AccountClsBal"], 2, '.', '') ?></td>
                                                            <td style="text-align:right;font-weight:400;"><?php echo  number_format($DEActVal["AccountClsBalPre"], 2, '.', '') ?></td>
														</tr>            
														<?php
															$SubCounter3++;
														}
														$SubCounter2++;
													}
													$SubCounter1++;
												}
											?>
											
											
											<?php $MainCounter++;?>
											
											<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
												<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">XI. Profit(Loss) for the period (IX-X)</td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncome - $TotalExp - $TotalTax, 2, '.', '') ?></td>
												<td class="parent col-id-particular" style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TotalRevenueIncomePre - $TotalExpPre - $TotalTaxPre, 2, '.', '') ?></td>
											</tr>
											<?php $MainCounter++;?>
											
											
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="revenue_from_operation-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Revenue from Operation</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_tradePayable_data" id="table_tradePayable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <?php
												foreach($TotalSaleGroupWise as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
												?>
												<th>Total</th>
											</tr>
											</thead>
											<tbody>
												<tr>
													<td>Sale Amount</td>
													<?php
														$TotalSaleAmt = 0;
														foreach($TotalSaleGroupWise as $key=>$val){
														?>
														<td style="text-align:right;"><?php echo number_format($val["SaleAmt"], 2, '.', ',');?></td>
														<?php
															$TotalSaleAmt += $val["SaleAmt"];
														}    
													?>
													<td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ',');?></td>
												</tr>
												
												<tr>
													<td>Sale Return Amount</td>
													<?php
														foreach($ItemGroup as $key=>$val){
														?>
														<td style="text-align:right;">0.00</td>
														
														<?php
														}    
													?>
													<td>0.00</td>
												</tr>
											</tbody>
											</table>   
											</div>
											</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
		
		
		<div class="modal fade" id="OtherIncome-modal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header" style="padding: 4px 10px;">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="modal-title">Other Income</h4>
					</div>
					<div class="modal-body" style="padding:5px;">
						<div class="row">
							<div class="col-md-12">
								<div class="table_annexure">
									<table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
										<thead>
											<tr>
												<th>Particular</th>
												<th>Opening</th>
												<th>Credit</th>
												<th>Debit</th>
												<th>Closing</th>
											</tr>
										</thead>
										<tbody>
											<?php
												foreach($OtherIncomeSubgroup2Wise as $key=>$val){
												?>
												<tr>
													<td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>
												<?php
												$TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
												?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
												</tr>
												<?php
												}
                                                
											?>
											<tr>
												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
												<td></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
											</tr>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="COGS-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">COGS Details</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        
                        <div class="col-md-12">
                            <h4>Inventory Opening Value</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
											<?php
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
											?>
                                            <th>Total</th>
										</tr>
									</thead>
                                    <tbody>
                                        <tr>
                                            <td>Opening Amt</td>
                                            <?php
                                                $TotalOpnAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["OpnBal"], 2, '.', ',');?></td>
												<?php
													$TotalOpnAmt += $val["OpnBal"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalOpnAmt, 2, '.', ',');?></td>
										</tr>
									</tbody>
								</table>   
							</div>
						</div>
                        
                        <div class="col-md-12">
                            <h4>Total Purchase Details</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
											<?php
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
											?>
                                            <th>Total</th>
										</tr>
									</thead>
                                    <tbody>
										
                                        <tr>
                                            <td>Purchase Amt</td>
                                            <?php
                                                $TotalPurchAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["PurchAmt"], 2, '.', ',');?></td>
												<?php
													$TotalPurchAmt += $val["PurchAmt"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
										</tr>
                                        
									</tbody>
								</table>   
							</div>
						</div>
                        
                        
                        <div class="col-md-12">
                            <h4>Direct Expense</h4>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($DirectExpSubgroup2Wise as $key=>$val){
											?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
											<?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
												?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
												</tr>
												<?php
												}
                                                
											?>
											<tr>
												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
												<td></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
											</tr>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
                        
                        
                        
                        <div class="col-md-12">
                            <h4>Closing Inventory Details (Inventory Value : <?php echo number_format($CurrentInventoryValue, 2, '.', ',');?>)</h4>
                            <span style="color:red;font-size:10px;">Calculated closing inventory as per FIFO Based.</span>
                            <div class="table_annexure">
                                <table class="tree table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
											<?php
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<th><?php echo $val["name"];?></th>
												<?php
												}    
											?>
                                            <th>Total</th>
										</tr>
									</thead>
                                    <tbody>
                                        <tr>
                                            <td>Opening Amt</td>
                                            <?php
                                                $TotalOpnAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["OpnBal"], 2, '.', ',');?></td>
												<?php
													$TotalOpnAmt += $val["OpnBal"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalOpnAmt, 2, '.', ',');?></td>
										</tr>
                                        <tr>
                                            <td>Purchase Amt</td>
                                            <?php
                                                $TotalPurchAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["PurchAmt"], 2, '.', ',');?></td>
												<?php
													$TotalPurchAmt += $val["PurchAmt"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
										</tr>
                                        <tr>
                                            <td>Sale Amt</td>
                                            <?php
                                                $TotalSaleAmt = 0;
												foreach($CurrentInventoryItemWiseValue as $key=>$val){
												?>
												<td style="text-align:right;"><?php echo number_format($val["SaleAmt"], 2, '.', ',');?></td>
												<?php
													$TotalSaleAmt += $val["SaleAmt"];
												}    
											?>
                                            <td style="text-align:right;"><?php echo number_format($TotalSaleAmt, 2, '.', ',');?></td>
										</tr>
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="EmpBen-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Employee Benefits Expense</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($EMPBenSubgroup2Wise as $key=>$val){
											?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
											<?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
												?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
												</tr>
												<?php
												}
                                                
											?>
											<tr>
												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
												<td></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
											</tr>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="FinCost-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Finance Costs</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($FinCostSubgroup2Wise as $key=>$val){
											?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
											<?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
												?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
												</tr>
												<?php
												}
                                                
											?>
											<tr>
												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
												<td></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
											</tr>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <div class="modal fade" id="OtherExp-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="padding: 4px 10px;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="modal-title">Other Expense</h4>
				</div>
                <div class="modal-body" style="padding:5px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table_annexure">
                                <table class="tree table-bordered table_OtherIncome_data" id="table_OtherIncome_data" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Particular</th>
                                            <th>Opening</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Closing</th>
										</tr>
									</thead>
                                    <tbody>
                                        <?php
                                            foreach($IndirectExpSubgroup2Wise as $key=>$val){
											?>
                                            <tr>
                                                <td style="font-weight:700;"><?php echo $val["SubActGroupName1"];?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
											<?php
                                                $TotalCR = 0;
                                                $TotalDR = 0;
                                                $TotalBalance = 0;
                                                foreach($val["SubGroup2"] as $SG2Key=>$SG2val){
                                                    $TotalCR += $SG2val["CR"];
                                                    $TotalDR += $SG2val["DR"];
                                                    $TotalBalance += $SG2val["Balance"];
												?>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $SG2val["ActSubGroupName2"];?></td>
                                                    <td></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["CR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["DR"], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;"><?php echo number_format($SG2val["Balance"], 2, '.', ''); ?></td>
												</tr>
												<?php
												}
                                                
											?>
											<tr>
												<td style="font-weight:700;">Total for <?php echo $val["SubActGroupName1"];?></td>
												<td></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalCR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalDR, 2, '.', ''); ?></td>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalBalance, 2, '.', ''); ?></td>
											</tr>
											<?php
											}
										?>
										
                                        
									</tbody>
								</table>   
							</div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
    
    <?php init_tail(); ?>
    <style>
        table  { border-collapse: collapse; width: 100%; }
        th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
        th     { background: #50607b;color: #fff !important; }
        .table_annexure {
		overflow: auto;
		max-height: 60vh;
		width: 98%;
		position: relative;
		top: 0px;
        }
	</style>
    <script>
        function GetAnnexure(Name){
            if(Name == "I. Revenue from Operation"){
                $('#revenue_from_operation-modal').modal('show');
				}else if(Name == "II. Other Income"){
                $('#OtherIncome-modal').modal('show');
				}else if(Name == "1. Cost of Goods Sold (COGS)"){
                $('#COGS-modal').modal('show');
				}else if(Name == "2. Employee benefits expense"){
                $('#EmpBen-modal').modal('show');
				}else if(Name == "3. Finance Costs"){
                $('#FinCost-modal').modal('show');
				}else if(Name == "5. Other Expenses"){
                $('#OtherExp-modal').modal('show');
			}
		}
        
	</script>
    <script type="text/javascript">
        function printPage() {
            var html_filter_name = $('.report_for').html();
            var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
            var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">' + document.getElementsByTagName('table')[0].innerHTML + '</table>';
            var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
            heading_data += '<tr>';
            heading_data += '<td style="text-align:center;"colspan="3">Profit Loss Sheet</td>';
            heading_data += '</tr>';
            heading_data += '<tr>';
            heading_data += '</tbody></table>';
            var print_data = stylesheet + heading_data + tableData
            newWin = window.open("");
            newWin.document.write(print_data);
            newWin.print();
            newWin.close();
		};
	</script>
	
	<script>
		$(document).ready(function(){
			$('#search_data').on('click', function () {
				var as_on_date = $("#as_on_date").val();  
				var from_date = $("#from_date").val();  
				
				if (as_on_date && from_date) {
					var parts = as_on_date.split('/');
					var parts2 = from_date.split('/');
					if (parts.length === 3 && parts2.length === 3) {
						var formatted_date = parts[2] + '-' + parts[1] + '-' + parts[0];  
						var formatted_date2 = parts2[2] + '-' + parts2[1] + '-' + parts2[0];  
						
						var redirect_url = '<?php echo admin_url(); ?>accounting/profitlossreport/' + formatted_date2+'/'+formatted_date;
						
						window.location.href = redirect_url;  // Perform redirect
						} else {
						alert("Invalid date format. Please use dd/mm/yyyy.");
					}
					} else {
					alert("Please enter a date.");
				}
			});
			
		});
	</script>	