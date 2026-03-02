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
            <div class="row ">
				<div class="col-md-12 text-centerr"  >
					<nav aria-label="breadcrumb" >
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Balance Sheet</b></li>
							
						</ol>
					</nav>
					<hr class="hr_style" style="margin-Bottom:12px !important;">
				</div>
                <div class="col-md-2">
                    <?php if (has_permission_new('balancesheet', '', 'print')) {
					?>
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-left: 10px;" onclick="printPage();">Print</a>
                    <?php } ?>
                    <label class="" style="margin-left: 30px; display: inline-block;">
						<input type="checkbox" id="extendAll" onchange="toggleExtendAll()"> Expand All
					</label>
				</div>
                <div class="col-md-4"><h4 style="text-align:center">Balance Sheet</h4></div>
                <div class="clearfix"></div>
                <div class="col-md-8" style="margin-top: 10px;">
                    <?php
                        $fy = $this->session->userdata('finacial_year');
                        $lastFy = $fy - 1;
                        $fy_ = $fy + 1;
                        $CurrYrFirstDate = '01/04/20' . $fy;
                        $CurrDate = date('d-m-Y');
                        $CurrYrLastDate = '31-03-20' . $fy_;
                        
                        $date1 = new DateTime($CurrDate); 
                        $date2 = new DateTime($CurrYrLastDate); 
                        
                        if($date1 < $date2){
                            $last_date = $CurrDate;
							}else{
                            $last_date = $CurrYrLastDate;
						}
                        
                        $LastYrFirstDate = '01/04/20' . $lastFy;
                        $LastYrLastDate = '31/03/20' . $fy;
					?>
                    <div class="page" id="DivIdToPrint">
                        <div id="accordion">
                            <div class="card">
                                <table class="tree">
                                    <thead>
                                        <tr class="tr_header" id="tr_header">
                                            <th></th>
                                            <th colspan="3" class="text-center th_total">
                                                <?php echo _l('total'); ?>
											</th>
										</tr>
                                        <tr class="tr_header" id="tr_header">
                                            <th class="th_total"></th>
                                            <th class="th_total" >Note</th>
                                            <th class="th_total" ><?php echo $CurrYrFirstDate.' - '._d($last_date); ?></th>
                                            <th class="th_total"><?php echo $LastYrFirstDate.' - '.$LastYrLastDate; ?></th>
										</tr>
									</thead>
									
                                    <tbody>
                                        <?php
											$MainCounter = 1000;
											$SubCounter = 2000;
											$Counter2 = 3000;
											$i = 1;
											foreach ($nestedData as $key => $val) {
												
											?>
                                            <tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded"
											style="font-size:14px;" id="maingroup">
                                                <td class="parent" style="font-size:13px;font-weight:600;"><?php echo $val['MainGroup']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
											</tr>
                                            <?php
												
												foreach ($val['SubGroups1'] as $key1 => $val1) {
                                                    
												?>
                                                <tr class="treegrid-<?php echo html_entity_decode($SubCounter); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
												id="subgroup">
                                                    <td class="parent" style="font-size:12px;font-weight:400;"><?php echo strtoupper($val1["SubGroup1Name"]); ?></td>
                                                    <?php
                                                        if($val1["SubGroup1"] == "1000015"){
														?>  
														<td style="text-align:right;"><a href="#" onclick="GetAnnexure('<?php echo $val1["SubGroup1"];?>','<?php echo $val1["SubGroup1Name"];?>')" class=" Open_Annexure mbot15">Fixed assets</a></td>
														<?php
															}else{
														?>
														<td></td>
														<?php
														}
													?>
                                                    
                                                    <td style="text-align:right;font-size:12px;font-weight:600;"><?php echo number_format($val1['Group1ClsBal'], 2, '.', ''); ?></td>
                                                    <td style="text-align:right;font-size:12px;font-weight:600;"><?php echo number_format($val1['Group1ClsBalPre'], 2, '.', ''); ?></td>
												</tr>
                                                
                                                <?php
													foreach ($val1['SubGroups'] as $key2 => $val2) {
														
													?>
                                                    <tr
													class="treegrid-<?php echo html_entity_decode($Counter2); ?> treegrid-parent-<?php echo $SubCounter; ?> parent-node " id="subgroup1">
                                                        <td class="parent"><?php echo strtoupper($val2["SubGroupName"]); ?></td>
                                                        <?php 
															//Inventories
                                                            if($val2["SubActGroupID"] == "1000005"){
															?>
															<td style="text-align:right;"><a href="#" onclick="GetAnnexure('<?php echo $val2["SubActGroupID"];?>','<?php echo $val2["SubGroupName"];?>')" class=" Open_Annexure mbot15">Inventories</a></td>
															<?php        
																}else{
															?>
															<td></td>
															<?php
															}
														?>
                                                        <td style="text-align:right;font-weight:600;font-size:12px;"><?php echo number_format($val2["Group2ClsBal"], 2, '.', '');?></td>
                                                        <td style="text-align:right;font-weight:600;font-size:12px;"><?php echo number_format($val2["Group2ClsBalPre"], 2, '.', '');?></td>
													</tr>
                                                    <?php
                                                        foreach ($val2['Accounts'] as $key3 => $val3) {
                                                            $AccountBal = $val3["AccountClsBal"];
                                                            $AcountBalPre = $val3["AccountClsBalPre"];
														?>
                                                        <tr class="treegrid-<?php echo html_entity_decode($Counter3); ?> treegrid-parent-<?php echo $Counter2; ?> parent-node " id="Accounts">
                                                            <td class="parent"  style="cursor:pointer;"  onclick="RedirectLedger('<?php echo strtoupper($val3["AccountID"]); ?>')"><?php echo strtoupper($val3["AccountName"]); ?></td>
                                                            <td></td>
                                                            <td style="text-align:right;"><?php echo number_format($AccountBal, 2, '.', '');?></td>
                                                            <td style="text-align:right;"><?php echo number_format($AcountBalPre, 2, '.', '');?></td>
															<?php
																$Counter3 ++;
															}
														?>
														<?php
															$Counter2 ++;
														}
													?>
													<!--<tr style="border: 1px solid #000;">
														<td colspan="2" style="font-size:13px;font-weight:600;">Total for <?php echo $val1['SubGroup1Name']; ?></td>
														
														<td style="text-align:right;font-size:13px;font-weight:600;">
                                                        <?php echo number_format($val1['Group1ClsBal'], 2, '.', ''); ?>
														</td>
														<td style="text-align:right;font-size:13px;font-weight:600;">
                                                        <?php echo number_format($val1['Group1ClsBalPre'], 2, '.', ''); ?>
														</td>
													</tr>-->
													<?php
														$SubCounter ++;
													}
												?>
												
												<tr style="border: 1px solid #000;" id="maingroup">
													<td colspan="2" style="font-size:13px;font-weight:700;">Total for <?php echo $val['MainGroup']; ?></td>
													
													<td style="text-align:right;font-size:13px;font-weight:700;">
														<?php echo number_format($val['MainGroupClsBal'], 2, '.', ''); ?>
													</td>
													<td style="text-align:right;font-size:13px;font-weight:700;">
														<?php echo number_format($val['MainGroupClsBalPre'], 2, '.', ''); ?>
													</td>
												</tr>
												<?php
													$MainCounter++;
													$i++;
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
		
		
		
		
		<div class="modal fade" id="Inventory-modal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header" style="padding: 4px 10px;">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="modal-title">Item Wise Inventory Details</h4>
					</div>
					<div class="modal-body" style="padding:5px;">
						<div class="row">
							<div class="col-md-12">
								<h4>Closing Inventory Details (Inventory Value : <?php echo number_format($CurrentInventoryValue, 2, '.', ',');?>)</h4><!--
								<span style="color:red;font-size:10px;">Calculated closing inventory as per FIFO Based.</span>-->
								<div class="table_annexure">
									<table class="tree table table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
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
											
											<tr>
												<td>Closing Inv Amt</td>
												<?php
													$TotalClsAmt = 0;
													foreach($CurrentInventoryItemWiseValue as $key=>$val){
													?>
													<td style="text-align:right;font-weight:700;"><?php echo number_format($val["CurrentValue"], 2, '.', ',');?></td>
													<?php
														$TotalClsAmt += $val["CurrentValue"];
													}    
												?>
												<td style="text-align:right;font-weight:700;"><?php echo number_format($TotalClsAmt, 2, '.', ',');?></td>
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
		
		<div class="modal fade" id="FixedAssets-modal">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header" style="padding: 4px 10px;">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="modal-title">Fixed Assets Details</h4>
					</div>
					<?php 
						$fy = $this->session->userdata('finacial_year');
						$Nfy = $fy + 1;
						$NNfy = $fy - 1;
						$count = count($FixedAssets) + 1;
					?>
					<div class="modal-body" style="padding:5px;">
						<div class="row">
							<div class="col-md-12">
								<div class="table_annexure">
									<table class="tree table table-bordered table_TradeReceivable_data" id="table_TradeReceivable_data" width="100%">
										<thead>
											<tr>
												<th>Particular</th>
												<?php
													foreach($FixedAssets as $key=>$val){
													?>
													<th><?php echo $val["company"];?></th>
													<?php
													}    
												?>
												<th>Total</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="color: #226faa;font-weight: 700;font-size: 13px;">A. YEAR ENDED MARCH 31 <?php echo '20'.$Nfy; ?></td>
												
												<td style="text-align:right;" colspan="<?php echo $count;?>"></td>
											</tr>
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Gross Carrying Amoount</td>
												
												<td style="text-align:right;" colspan="<?php echo $count;?>"></td>
											</tr>
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Opening Gross Carrying amount as at April 1 <?php echo '20'.$fy; ?></td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : Additions</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : Slump Purchase as per BTA</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Less : Deduction</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Closing Gross Carrying Amount</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Accumulated Depreciation and Impairment</td>
												
												<td style="text-align:right;" colspan="<?php echo $count;?>"></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Opening Accumulated Depreciation and Impairment as at April 1 <?php echo '20'.$fy; ?></td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : depreciation charges during this year</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : Impairment</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Less : Deduction</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Closing Depreciation and Impairment</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Net Carrying Amount</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											
											<!--Section B -->
											<tr>
												<td style="color: #226faa;font-weight: 700;font-size: 13px;">B. YEAR ENDED MARCH 31 <?php echo '20'.$fy; ?></td>
												
												<td style="text-align:right;" colspan="<?php echo $count;?>"></td>
											</tr>
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Gross Carrying Amoount</td>
												
												<td style="text-align:right;" colspan="<?php echo $count;?>"></td>
											</tr>
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Opening Gross Carrying amount as at April 1 <?php echo '20'.$NNfy; ?></td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : Additions</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : Slump Purchase as per BTA</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Less : Deduction</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Closing Gross Carrying Amount</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Accumulated Depreciation and Impairment</td>
												
												<td style="text-align:right;" colspan="<?php echo $count;?>"></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Opening Accumulated Depreciation and Impairment as at April 1 <?php echo '20'.$NNfy; ?></td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : depreciation charges during this year</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Add : Impairment</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td>Less : Deduction</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Closing Depreciation and Impairment</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
											</tr>
											
											<tr>
												<td style="font-weight: 700;font-size: 12px;">Net Carrying Amount</td>
												<?php
													$TotalGrossAmt = 0;
													foreach($FixedAssets as $key=>$val){
													?>
													<td style="text-align:right;"><?php //echo number_format($val["company"], 2, '.', ',');?></td>
													<?php
														//$TotalPurchAmt += $val["PurchAmt"];
													}    
												?>
												<td style="text-align:right;"><?php //echo number_format($TotalPurchAmt, 2, '.', ',');?></td>
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
<script>
    function toggleExtendAll() {
        var extendAllCheckbox = document.getElementById('extendAll');
        if (extendAllCheckbox.checked) {
            Expand();
        } else {
            Collaps();
        }
    }

    function Expand() { 
        $("table.tree tr").each(function() {
            var IdName = $(this).attr('id');
            if(IdName == "maingroup" || IdName == "subgroup" || IdName == "tr_header"){
                
            }else{
                $(this).removeClass("collapsed").addClass("expanded");
                $(this).show();
                
            } 
        })
        $("table.tree tr#subgroup td div span").each(function() {
            var ClassName = $(this).attr('class');
            $needle = "treegrid-expander-collapsed";
            if (ClassName.includes($needle)) {
                $(this).removeClass("treegrid-expander-collapsed").addClass("treegrid-expander-expanded");
            }
        });
        $("table.tree tr#subgroup1 td div span").each(function() {
            var ClassName = $(this).attr('class');
            $needle = "treegrid-expander-collapsed";
            if (ClassName.includes($needle)) {
                $(this).removeClass("treegrid-expander-collapsed").addClass("treegrid-expander-expanded");
            }
        });
        document.getElementById('extendAll').checked = true;
    }

    function Collaps() {  
        $("table.tree tr").each(function() {
            var IdName = $(this).attr('id');
            if(IdName == "maingroup" || IdName == "subgroup" || IdName == "tr_header"){
                
            }else{
                $(this).removeClass("expanded").addClass("collapsed");
                $(this).hide();
            }
        });
        
        $("table.tree tr#subgroup td div span").each(function() {
            var ClassName = $(this).attr('class');
            $needle = "treegrid-expander-expanded";
            if (ClassName.includes($needle)) {
                $(this).removeClass("treegrid-expander-expanded").addClass("treegrid-expander-collapsed");
            }
        });
        $("table.tree tr#subgroup1 td div span").each(function() {
            var ClassName = $(this).attr('class');
            $needle = "treegrid-expander-expanded";
            if (ClassName.includes($needle)) {
                $(this).removeClass("treegrid-expander-expanded").addClass("treegrid-expander-collapsed");
            }
        });
        document.getElementById('extendAll').checked = false;
    }
</script>
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
			
			function GetAnnexure(SubgroupID,SubgroupName){
				if(SubgroupID == "1000005"){
					$('#Inventory-modal').modal('show');
					}else if(SubgroupID == "1000015"){
					$('#FixedAssets-modal').modal('show');
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
				heading_data += '<td style="text-align:center;"colspan="3">Balance Sheet</td>';
				heading_data += '</tr>';
				heading_data += '<tr>';
				heading_data += '</tbody></table>';
				var print_data = stylesheet + heading_data + tableData
				newWin = window.open("");
				newWin.document.write(print_data);
				newWin.print();
				newWin.close();
			};
			
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
			$("#caexcel").click(function(){
				var maingroup = $("#maingroup").val();
				var subgroup = $("#subgroup").val();
				var subgroup1 = $("#subgroup1").val();
				$.ajax({
					url:"<?php echo admin_url(); ?>Misc_reports/export_balsheetreport",
					method:"POST",
					data: {maingroup:maingroup, subgroup:subgroup,subgroup1:subgroup1},
					beforeSend: function () {
						$('#searchh3').css('display','block');
					},
					complete: function () {
						$('#searchh3').css('display','none');
					},
					success:function(data){
						response = JSON.parse(data);
						window.location.href = response.site_url+response.filename;
					}
				});
			});
			
		</script>		