<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .th_total {
	padding-right: 10px;
    }
    .tree tr th {
	padding-bottom: 0px !important;
	padding-top: 10px !important;
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
					<?php if (has_permission_new('profitlossTFormat', '', 'export')) {
					?>
                    <a class="btn btn-default" id="caexcel" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" ><i class="fa fa-spinner fa-spin Loader" style="display:none;"></i> Export</a>
                    <?php } ?>
					<?php if (has_permission_new('profitlossTFormat', '', 'print')) {
					?>
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" onclick="printPage();">Print</a>
                    <?php } ?>
					
                    <label class="" style="margin-left: 30px; display: inline-block;">
						<input type="checkbox" id="expandAll" > Expand All
					</label>
				</div>
			</div>
			<?php
				if(!empty($Date)){
				?>
				<div class="row ">
					<div class="col-md-12">
						<div class="page">
							<div id="accordion">
								<div class="card">
									<div class="row" id="DivIdToPrint">
										<div class="col-md-6">
											<table class="tree">
												<thead>
													<tr class="tr_header">
														<th style="text-align:left;font-weight:700;font-size: 14px;"><b>Particular</b></th>
														<th style=""><b></b></th>
														<th style=""><b></b></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$MainCounter = 1000;
														$SubCounter1 = 2000;
														$SubCounter2 = 3000;
														$SubCounter3 = 4000;
													?>
													<?php
														$TotalSaleRtn = ($TransactionAmt->FrtRtnCurrentYear + $TransactionAmt->DFrtRtnCurrentYear);
														$TotalSaleRtnPre = ($TransactionAmt->FrtRtnPriviousYear + $TransactionAmt->DFrtRtnPriviousYear);
														$TotalRevenueIncome += $TransactionAmt->SaleCurrentYear - $TotalSaleRtn;
														$TotalRevenueIncomePre += $TransactionAmt->SalePriviousYear - $TotalSaleRtnPre;
													?>
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node " id="maingroup">
														<td style="text-align:left;font-weight:700;font-size: 14px;">Opening Amount</td>
														<td style="text-align:left;font-weight:700;font-size: 14px;text-align:right;"></td>
														<td style="text-align:left;font-weight:700;font-size: 14px;text-align:right;"><?php echo  number_format($OpeningInventoryAmt->CurrentYear, 2, '.', '') ?></td>
													</tr>
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">RM Opening Amt</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($OpeningInventoryAmt->TotalRMOpnAmt, 2, '.', '') ?></td>
														
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"></td>
													</tr>
													<?php $SubCounter1++;?>
													
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">FG Opening Amt</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($OpeningInventoryAmt->TotalFGOpnAmt, 2, '.', '') ?></td>
														
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"></td>
													</tr>
													<?php $SubCounter1++;?>
													<?php $MainCounter++;?>
													
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node" id="maingroup">
														<td style="text-align:left;font-weight:700;font-size: 14px;">Purchase Account</td>
														<td style="text-align:left;font-weight:700;font-size: 14px;"></td>
														<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($TransactionAmt->PurchCurrentYear, 2, '.', '') ?></td>
													</tr>
													
													<?php $MainCounter++;?>
													
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?> parent-node " id="subgroup">
														<td class="col-id-particular" style="text-align:left;font-weight:700;font-size: 14px;">Direct Expense - add</td>
														<td style="text-align:left;font-weight:700;font-size: 14px;"></td>
														<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($DirectExp->CurrentYear, 2, '.', '') ?></td>
													</tr>
													
													<?php
														foreach($DirectExp->nestedData as $DEKey=>$DEVal){
														?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $DEVal["AccountID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $DEVal["Group1Name"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DEVal["Group1ClsBal"], 2, '.', '') ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
															foreach($DEVal["SubGroups2"] as $DE2Key=>$DE2Val){
															?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
															style="font-size:13px;" id="subgroup" data-id="<?php echo $DE2Val["SubActGroupID"] ?>">
																<td class="col-id-particular"><?php echo $DE2Val["SubGroupName"]; ?></td> 
																<td style="text-align:right;font-weight:500;font-size:13px;"><?php echo  number_format($DE2Val["Group2ClsBal"], 2, '.', '') ?></td>
																<td style="text-align:right;font-weight:500;font-size:13px;"></td>
															</tr>
															<?php
																foreach($DE2Val["Accounts"] as $DEActKey=>$DEActVal){
																?>
																<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
																style="font-size:12px;" id="subgroup" data-id="<?php echo $DEActVal["AccountID"] ?>">
																	<td class="col-id-particular"  style="cursor:pointer;"  onclick="RedirectLedger('<?php echo strtoupper($DEActVal["AccountID"]); ?>')"><?php echo $DEActVal["AccountName"]; ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"><?php echo  number_format($DEActVal["AccountClsBal"], 2, '.', '') ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"></td>
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
													<?php
														$GrossProfitC_F = ($TotalRevenueIncome + $ClosingInventoryAmt->CurrentYear) - ($OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear); 
													?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">Gross Profit c/o</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($GrossProfitC_F, 2, '.', '') ?></td>
													</tr>
													
													<?php $MainCounter++;?>
													<?php $Total = $GrossProfitC_F + ($OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear);?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:14px;font-weight:700;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($Total, 2, '.', '') ?></th>
													</tr>
													
													<?php $MainCounter++;?>
													
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?> parent-node expanded" id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">2. Employee benefits expense</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($EMPBenData->CurrentYear, 2, '.', '') ?></td>
													</tr>
													
													<?php
														foreach($EMPBenData->nestedData as $EBKey=>$EBVal){
														?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $EBVal["SubActGroupID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $EBVal["SubGroupName"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($EBVal["Group2ClsBal"], 2, '.', '') ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
															foreach($EBVal["Accounts"] as $EBActKey=>$EBActVal){
															?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
															style="font-size:12px;" id="subgroup" data-id="<?php echo $EBActVal["AccountID"] ?>">
																<td class="col-id-particular" style="text-align:left;font-weight:500;font-size:12px;cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($EBActVal["AccountID"]); ?>')"><?php echo $EBActVal["AccountName"]; ?></td> 
																<td style="text-align:right;font-weight:500;font-size:12px;"><?php echo  number_format($EBActVal["AccountClsBal"], 2, '.', '') ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"></td>
															</tr>
															<?php
																$SubCounter2++;
															}
															$SubCounter1++;
														}
													?>
													<?php $MainCounter++;?>
													
													
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">FINANCE COST</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($FinanceCostData->CurrentYear, 2, '.', '') ?></td>
													</tr>
													
													<?php
														foreach($FinanceCostData->nestedData as $FCKey=>$FCVal){
														?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $FCVal["SubActGroupID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $FCVal["SubGroupName"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($FCVal["Group2ClsBal"], 2, '.', '') ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
															foreach($FCVal["Accounts"] as $FCActKey=>$FCActVal){
															?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
															style="font-size:12px;" id="subgroup" data-id="<?php echo $FCActVal["AccountID"] ?>">
																<td class="col-id-particular" style="text-align:left;font-weight:500;font-size:12px;cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($FCActVal["AccountID"]); ?>')"><?php echo $FCActVal["AccountName"]; ?></td> 
																<td style="text-align:right;font-weight:500;font-size:12px;"><?php echo  number_format($FCActVal["AccountClsBal"], 2, '.', '') ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"></td>
															</tr>
															<?php
																$SubCounter2++;
															}
															$SubCounter1++;
														}
													?>
													<?php $MainCounter++;?>
													
													
													
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">Depreciation And Amortization Expense</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($DeprecData->CurrentYear, 2, '.', '') ?></td>
													</tr>
													
													<?php  
														foreach($DeprecData->nestedData as $DAKey=>$DAVal){
														?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $DAVal["SubActGroupID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $DAVal["SubGroupName"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($DAVal["Group2ClsBal"], 2, '.', '') ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
															foreach($DAVal["Accounts"] as $DAActKey=>$DAActVal){ 
															?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
															style="font-size:12px;" id="subgroup" data-id="<?php echo $DAActVal["AccountID"] ?>">
																<td class="col-id-particular" style="cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($DAActVal["AccountID"]); ?>')"><?php echo $DAActVal["AccountName"]; ?></td> 
																<td style="text-align:right;font-weight:500;"><?php echo  number_format($DAActVal["AccountClsBal"], 2, '.', '') ?></td>
																<td style="text-align:right;font-weight:500;"></td>
															</tr>
															<?php
																$SubCounter2++;
															}
															$SubCounter1++;
														}
													?>
													<?php $MainCounter++;?>
													
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?> expanded parent-node " id="subgroup">
														<td class="col-id-particular" style="font-size:14px;font-weight:700;">INDIRECT EXPENSES</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($OtherExpensesData->CurrentYear, 2, '.', '') ?></td>
													</tr>
													
													<?php
														foreach($OtherExpensesData->nestedData as $IExpKey=>$IExpVal){
														?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $IExpVal["AccountID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $IExpVal["Group1Name"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($IExpVal["Group1ClsBal"], 2, '.', '') ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
															foreach($IExpVal["SubGroups2"] as $IExp2Key=>$IExp2Val){
															?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
															style="font-size:12px;" id="subgroup" data-id="<?php echo $IExp2Val["SubActGroupID"] ?>">
																<td class="col-id-particular"><?php echo $IExp2Val["SubGroupName"]; ?></td> 
																<td style="text-align:right;font-weight:500;font-size:12px;"><?php echo  number_format($IExp2Val["Group2ClsBal"], 2, '.', '') ?></td>
																<td style="text-align:right;font-weight:500;font-size:12px;"></td>
															</tr>
															<?php
																foreach($IExp2Val["Accounts"] as $IExpActKey=>$IExpActVal){
																?>
																<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
																style="font-size:13px;" id="subgroup" data-id="<?php echo $IExpActVal["AccountID"] ?>">
																	<td class="col-id-particular" style="text-align:left;font-weight:400;font-size:12px;cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($IExpActVal["AccountID"]); ?>')"><?php echo $IExpActVal["AccountName"]; ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"><?php echo  number_format($IExpActVal["AccountClsBal"], 2, '.', '') ?></td>
																	<td style="text-align:right;font-weight:400;font-size:12px;"></td>
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
													<?php
														$IndirectExp = ($OtherExpensesData->CurrentYear + $EMPBenData->CurrentYear + $FinanceCostData->CurrentYear + $DeprecData->CurrentYear);
														$NetProfit = $GrossProfitC_F + $OtherIncome->CurrentYear - $IndirectExp; 
													?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">Net Profit</th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($NetProfit, 2, '.', '') ?></th>
													</tr>
													
													
													<?php $MainCounter++;?>
													
													<?php $AllTotal = $NetProfit + $IndirectExp;?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">Total</th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($AllTotal, 2, '.', '') ?></th>
													</tr>
													
													<?php $MainCounter++;?>
													
													
												</tbody>
											</table>
										</div>
										<div class="col-md-6">
											<table class="tree">
												<thead>
													<tr class="tr_header">
														<th style=""><b>Particular</b></th>
														<th style=""><b></b></th>
														<th style=""><b></b></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$MainCounter = 1000;
														$SubCounter1 = 2000;
													?>
													<?php $MainCounter++;?>
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded " id="maingroup">
														
														<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">Revenue from Operation</td>
														<td class="parent col-id-particular" style="text-align:left;font-weight:700;font-size: 14px;text-align:right;"><b></b></td>
														<td class="parent col-id-particular" style="text-align:left;font-weight:700;font-size: 14px;text-align:right;"><?php echo  number_format($TotalRevenueIncome, 2, '.', '') ?></td>
													</tr>
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node " id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">Sale Amount</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($TransactionAmt->SaleCurrentYear, 2, '.', '') ?></td>
														
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"></td>
													</tr>
													<?php $SubCounter1++;?>
													<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $ActGrp1["AccountID"] ?>">
														<td class="col-id-sr-no" style="font-size:13px;font-weight:500;">Sale Return Amount</td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($TotalSaleRtn, 2, '.', '') ?></td>
														<td class="col-id-particular" style="font-size:13px;font-weight:500;text-align:right;"></td>
													</tr>
													<?php $SubCounter1++;?>
													
													<?php $MainCounter++;?>
													
													
													
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded" id="maingroup">
														<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">Closing Amt</td>
														
														<td style="text-align:right;font-weight:700;font-size: 14px;"></td>
														<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($ClosingInventoryAmt->CurrentYear, 2, '.', '') ?></td>
													</tr>
													<?php $MainCounter++;?>
													
													<?php $Total2 = $TotalRevenueIncome + $ClosingInventoryAmt->CurrentYear;
													?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:14px;font-weight:700;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($Total2, 2, '.', '') ?></th>
													</tr>
													
													<?php $MainCounter++;?>
													
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<td class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">Gross Profit b/f</td>
														<td style="font-size:14px;font-weight:700;text-align:right;"></td>
														<td style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($GrossProfitC_F, 2, '.', '') ?></td>
													</tr>
													
													<?php $MainCounter++;?>
													
													<tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded" id="maingroup">
														<td class="parent col-id-sr-no" style="text-align:left;font-weight:700;font-size: 14px;">Other Income</td>
														<?php
															$TotalRevenueIncome += $OtherIncome->CurrentYear;
															$TotalRevenueIncomePre += $OtherIncome->PriviousYear;
														?>
														<td style="text-align:right;font-weight:700;font-size: 14px;"></td>
														<td style="text-align:right;font-weight:700;font-size: 14px;"><?php echo  number_format($OtherIncome->CurrentYear, 2, '.', '') ?></td>
													</tr>
													<?php
														foreach($OtherIncome->nestedData as $OthKey=>$OthVal){
														?>
														<tr class=" treegrid-<?php echo html_entity_decode($SubCounter1); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node" id="subgroup" data-id="<?php echo $OthVal["AccountID"] ?>">
															<td class="col-id-particular" style="font-size:13px;font-weight:500;"><?php echo $OthVal["Group1Name"]; ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"><?php echo  number_format($OthVal["Group1ClsBal"], 2, '.', '') ?></td>
															<td style="font-size:13px;font-weight:500;text-align:right;"></td>
														</tr>
														<?php
															foreach($OthVal["SubGroups2"] as $Oth2Key=>$Oth2Val){
															?>
															<tr class=" treegrid-<?php echo html_entity_decode($SubCounter2); ?> treegrid-parent-<?php echo $SubCounter1; ?> parent-node  "
															style="font-size:13px;" id="subgroup" data-id="<?php echo $Oth2Val["SubActGroupID"] ?>">
																<td class="col-id-particular"><?php echo $Oth2Val["SubGroupName"]; ?></td> 
																<td style="text-align:right;font-weight:500;"><?php echo  number_format($Oth2Val["Group2ClsBal"], 2, '.', '') ?></td>
																<td style="text-align:right;font-weight:500;"></td>
															</tr>
															<?php
																foreach($Oth2Val["Accounts"] as $OthActKey=>$OthActVal){
																?>
																<tr class=" treegrid-<?php echo html_entity_decode($SubCounter3); ?> treegrid-parent-<?php echo $SubCounter2; ?> parent-node  "
																style="font-size:13px;" id="subgroup" data-id="<?php echo $OthActVal["AccountID"] ?>">
																	<td class="col-id-particular" style="cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($OthActVal["AccountID"]); ?>')"><?php echo $OthActVal["AccountName"]; ?></td>
																	<td style="text-align:right;font-weight:400;"><?php echo  number_format($OthActVal["AccountClsBal"], 2, '.', '') ?></td>
																	<td style="text-align:right;font-weight:400;"></td>
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
													<?php $AllTotal2 = $GrossProfitC_F + $OtherIncome->CurrentYear;?>
													<tr class="treegrid-<?php echo html_entity_decode($MainCounter); ?>  parent-node " id="subgroup">
														<th class="parent col-id-sr-no" style="font-size:14px;font-weight:700;">Total</th>
														<th style="font-size:14px;font-weight:700;text-align:right;"></th>
														<th style="font-size:14px;font-weight:700;text-align:right;"><?php echo  number_format($AllTotal2, 2, '.', '') ?></th>
													</tr>
													
													<?php $MainCounter++;?>
													
													
												</tbody>
											</table>
										</div>
									</div>
									
									
									
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				}
			?>
		</div>
	</div>
</div>
<?php init_tail(); ?>

<script>
	$(document).ready(function () {
		
		$('#expandAll').on('change', function () {
			if (this.checked) {
				// Expand all — show all child rows
				$('.tree tr').show().addClass('expanded');
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-collapsed";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-collapsed").addClass("treegrid-expander-expanded");
					}
				});
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-collapsed";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-collapsed").addClass("treegrid-expander-expanded");
					}
				});
				} else {
				// Collapse all — hide child rows except top-level
				$('.tree tr').each(function () {
					var parent = $(this).attr('class');
					if (parent && parent.match(/treegrid-parent/)) {
						$(this).hide().removeClass('expanded');
					}
				});
				
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-expanded";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-expanded").addClass("treegrid-expander-collapsed");
					}
				});
				$("table.tree tr td div span").each(function() {
					var ClassName = $(this).attr('class');
					$needle = "treegrid-expander-expanded";
					if (ClassName.includes($needle)) {
						$(this).removeClass("treegrid-expander-expanded").addClass("treegrid-expander-collapsed");
					}
				});
			}
		});
		
	});
	// $(document).ready(function() {
    // $('.tree').treegrid({
	// initialState: 'collapsed'
    // });
	
    // $('#expandAll').change(function() {
	// if ($(this).is(':checked')) {
	// $('.tree tr').each(function() {
	// $(this).treegrid('expand');
	// });
	// } else {
	// $('.tree tr').each(function() {
	// $(this).treegrid('collapse');
	// });
	// }
    // });
	// });
	
	function printPage() {
		var from_date = $("#from_date").val();
		var as_on_date = $("#as_on_date").val();
		var stylesheet = '<style type="text/css">body { font-family: Arial, sans-serif; font-size:12px; }th, td { padding: 5px; border: 1px solid #000; border-collapse: collapse; font-size: 12px; }table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }.print-header { text-align:center; font-weight:bold; border: 1px solid #000; border-collapse: collapse; font-size: 12px; }.row { display: flex; justify-content: space-between; }.col-md-6 { width: 48%; }</style>';
		
		var heading_data = '<div class="print-header"><div style="border-bottom: 1px solid #000; padding:5px;"><?php echo $company_detail->company_name; ?></div><div style="border-bottom: 1px solid #000; padding:5px;"><?php echo $company_detail->address; ?></div><div style="padding:5px;"> Profit & Loss From '+from_date+' To '+as_on_date+'</div></div>';
		
		var content = document.getElementById('DivIdToPrint').innerHTML;
		
		var printWindow = window.open('', '', 'height=600,width=1000');
		printWindow.document.write('<html><head><title>T Profit Loss Report</title>');
		printWindow.document.write(stylesheet);
		printWindow.document.write('</head><body>');
		printWindow.document.write(heading_data);
		printWindow.document.write('<div class="row">' + content + '</div>');
		printWindow.document.write('</body></html>');
		printWindow.document.close();
		printWindow.focus();
		printWindow.print();
		printWindow.close();
	}
	
</script>
<script>
	$("#caexcel").click(function(){
		var from_date = $("#from_date").val();
		var as_on_date = $("#as_on_date").val();
		$.ajax({
			url:"<?php echo admin_url(); ?>accounting/export_ProfitLossTFormat",
			method:"POST",
			data: {from_date:from_date,as_on_date:as_on_date},
			beforeSend: function () {
				$('.Loader').show();
			},
			complete: function () {
				$('.Loader').hide();
			},
			success:function(data){
				response = JSON.parse(data);
				window.location.href = response.site_url+response.filename;
			}
		});
	});
	
	function RedirectLedger(AccountID){
		$.ajax({
			url:"<?php echo admin_url(); ?>accounting/SetAccountID",
			dataType:"JSON",
			method:"POST",
			data:{AccountID:AccountID},
			beforeSend: function () {
				$('.searchh2').css('display','block');
				$('.searchh2').css('color','blue');
			},
			complete: function () {
				$('.searchh2').css('display','none');
			},
			success:function(data){
				var url = "<?php echo admin_url();?>accounting/rp_general_ledger";
				window.open(url, '_blank');
			}
		});
	}
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
					
					var redirect_url = '<?php echo admin_url(); ?>accounting/ProfitLossTFormat/' + formatted_date2+'/'+formatted_date;
					
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