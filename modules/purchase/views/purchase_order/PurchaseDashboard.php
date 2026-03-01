<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
	$PendingPurchase = 0;
	$ApprovedPurchase = 0;
	$AllPurchase = 0;
	foreach($PurchaseStatus as $Purchase){
		if($Purchase['cur_status'] == "Pending"){
			$PendingPurchase = $Purchase['count'];
		}
		if($Purchase['cur_status'] == "Approved"){
			$ApprovedPurchase = $Purchase['count'];
		}
	}
	$AllPurchase = $PendingPurchase+$ApprovedPurchase;
	
	$PendingEntry = 0;
	$CompletedEntry = 0;
	foreach($PurchaseEntryStatus as $PurchaseEntry){
		if($PurchaseEntry['cur_status'] == "Pending"){
			$PendingEntry = $PurchaseEntry['count'];
		}
		if($PurchaseEntry['cur_status'] == "Completed"){
			$CompletedEntry = $PurchaseEntry['count'];
		}
	}
	$AllEntry = $PendingEntry+$CompletedEntry;
	
	
	
?>
<div id="wrapper">
	<div class="content" >
	    <div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Dashboard</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
					    <div class="widget relative" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('quick_stats'); ?>">
							<div class="_buttons">
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
										// $from_date = "01/".date('m')."/".date('Y');
										$from_date = date('d/m/Y');
										$to_date = date('d/m/Y');
									}
								?>
								<div class="col-md-3">
									<?php
										echo render_date_input('from_date2','From Date',$from_date);
									?>
								</div>
								<div class="col-md-3">
									<?php
										echo render_date_input('to_date2','To Date',$to_date);
									?>
								</div>
								<div class="col-md-3">
									<button class="btn btn-info pull-left mleft5 search_data_counter" style="margin-top: 19px;" id="search_data_counter">Show</button>
								</div>
								
							</div>
							<div class="clearfix"></div>
							<div class="row" >  
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
									<div class="top_stats_wrapper custdesg bg1">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-shopping-cart"></i></p>
										</div>
										<div class="col-md-9">
											<p class="mtop5 labeltxt"> <?php echo _l('Purchase Orders :'); ?><label id="AllPurchase" class="labeltxt"><?= $AllPurchase?></label><br>
												<span class="numstyl">Pending : <label id="PendingPurchase" class="labeltxt"><?php echo $PendingPurchase; ?></label> PO</span>
											<span class="numstyl">Approved : <label id="ApprovedPurchase" class="labeltxt"><?php echo $ApprovedPurchase; ?></label> PO</span></p>
											<div class="clearfix"></div>
											
										</div>
									</div>
								</div>
								
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
									<div class="top_stats_wrapper custdesg bg2">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-users"></i></p>
										</div>
										<?php
											$RawMaterial = 0;
											$PackingMaterial = 0;
											foreach($PurchaseSKU as $GroupSKU){
												if($GroupSKU['MainGrpID'] == "2"){
													// $RawMaterial = $GroupSKU['count'];
													$RawMaterial++;
												}
												if($GroupSKU['MainGrpID'] == "3"){
													$PackingMaterial++;
												}
											}
											$AllSKUs = $RawMaterial+$PackingMaterial;
										?>
										<div class="col-md-9">
											<p class="mtop5 labeltxt"><?php echo _l('Total Purchase SKU'); ?> : <label id="AllSKUs" class="labeltxt"><?= $AllSKUs;?></label> SKU<br>
												<span class="numstyl">Raw Material : <label id="RawMaterial" class="labeltxt"><?php echo $RawMaterial; ?></label> SKU</span>
											<span class="numstyl">Packing Material : <label id="PackingMaterial" class="labeltxt"><?php echo $PackingMaterial; ?></label> SKU</span></p>
											<div class="clearfix"></div>
											
										</div>
									</div>
								</div>
								
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
									<div class="top_stats_wrapper custdesg bg3">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
										</div>
										<div class="col-md-9">
											<p class="mtop5 labeltxt"><?php echo _l('Purchase Amount'); ?><br>
												<span class="numstyl">Monthly : <?php echo $CompletedInvoices->monthly_invoice_total; ?></span>
											<span class="numstyl">Today : <?php echo $CompletedInvoices->today_invoice_total; ?></span></p>
											<div class="clearfix"></div>
											
										</div>
									</div>
								</div>
								
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 <?php echo $initial_column; ?>">
									<div class="top_stats_wrapper custdesg bg4">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
										</div>
										<?php
											$maida = '';
											$atta = '';
											$yeast = '';
											foreach($RMLowestPurchaseSKU as $LowestSKU){
												$company = (strlen($LowestSKU['company']) > 25) ? substr($LowestSKU['company'], 0, 25) . "..." : $LowestSKU['company'];
												if($LowestSKU['SubGrpID2'] == "17"){
													$maida = $LowestSKU['BasicRate']." - <small style='font-size:7px;' title='".$LowestSKU['company']."'>".$company."</small>";
												}
												if($LowestSKU['SubGrpID2'] == "18"){
													$atta = $LowestSKU['BasicRate']." - <small style='font-size:7px;' title='".$LowestSKU['company']."'>".$company."</small>";
												}
												if($LowestSKU['SubGrpID2'] == "69"){
													$yeast = $LowestSKU['BasicRate']." - <small style='font-size:7px;' title='".$LowestSKU['company']."'>".$company."</small>";
												}
											}
										?>
										<div class="col-md-9">
											<p class="mtop5 labeltxt"><?php echo _l('Top 3 RM SKU Lowest Rate'); ?><br>
												<span class="numstyl">Maida : <label id="maida" class="labeltxt"><?= $maida?></label></span>
												<span class="numstyl">Atta : <label id="atta" class="labeltxt"><?= $atta?></label></span>
											<span class="numstyl">Yeast : <label id="yeast" class="labeltxt"><?= $yeast?></label></span></p>
											<div class="clearfix"></div>
											
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								
								
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
									<div class="top_stats_wrapper custdesg bg1">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
										</div>
										<div class="col-md-9">
											<p class="mtop5 labeltxt">Purchase Entry: <label id="AllEntry" class="labeltxt"><?= $AllEntry?></label><br>
												<span class="numstyl">Pending : <label id="PendingEntry" class="labeltxt"><?php echo $PendingEntry; ?></label></span>
											<span class="numstyl">Approved : <label id="CompletedEntry" class="labeltxt"><?php echo $CompletedEntry; ?></label></span></p>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
									<div class="top_stats_wrapper custdesg bg2">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
										</div>
										<?php
											$RawMaterialVendor = 0;
											$PackingMaterialVendor = 0;
											foreach($PurchaseVendors as $PVendors){
												if($PVendors['SubActGroupID'] == "1000186"){
													$RawMaterialVendor++;
												}
												if($PVendors['SubActGroupID'] == "1000188"){
													$PackingMaterialVendor++;
												}
											}
											$AllVendor = $RawMaterialVendor+$PackingMaterialVendor;
										?>
										<div class="col-md-9">
											<p class="mtop5 labeltxt">Total Purchase Vendor: <label id="AllVendor" class="labeltxt"><?= $AllVendor;?></label><br>
												<span class="numstyl">Raw Material : <label id="RawMaterialVendor" class="labeltxt"><?php echo $RawMaterialVendor; ?></label></span>
											<span class="numstyl">Packing Material : <label id="PackingMaterialVendor" class="labeltxt"><?php echo $PackingMaterialVendor; ?></label></span></p>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
									<div class="top_stats_wrapper custdesg bg3">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
										</div>
										<div class="col-md-9">
											<p class="mtop5 labeltxt"><?php echo _l('Purchase Return Amount'); ?><br>
												<span class="numstyl">Monthly : 0</span>
											<span class="numstyl">Today : 0</span></p>
											<div class="clearfix"></div>
										</div>
									</div>
								</div>
								
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
									<div class="top_stats_wrapper custdesg bg4">
										<div class="col-md-3">
											<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
										</div>
										<?php
											$maida = '';
											$atta = '';
											$yeast = '';
											foreach($PMLowestPurchaseSKU as $key => $PMLowestSKU){
												$description = (strlen($PMLowestSKU['description']) > 25) ? substr($PMLowestSKU['description'], 0, 25) . "..." : $PMLowestSKU['description'];
												if($key == 0){
													$maida = $PMLowestSKU['BasicRate']." - <small style='font-size:7px;' title='".$PMLowestSKU['company']."'>".$description."</small>";
												}
												if($key == 1){
													$atta = $PMLowestSKU['BasicRate']." - <small style='font-size:7px;' title='".$PMLowestSKU['company']."'>".$description."</small>";
												}
												if($key == 2){
													$yeast = $PMLowestSKU['BasicRate']." - <small style='font-size:7px;' title='".$PMLowestSKU['company']."'>".$description."</small>";
												}
											}
										?>
										<div class="col-md-9">
											<p class="mtop5 labeltxt"><?php echo _l('Top 3 PM SKU Lowest Rate'); ?><br>
												<span class="numstyl">SKU1 : <label id="Lowestmaida" class="labeltxt"><?= $maida?></label></span>
												<span class="numstyl">SKU2 : <label id="Lowestatta" class="labeltxt"><?= $atta?><label></span>
												<span class="numstyl">SKU3 : <label id="Lowestyeast" class="labeltxt"><?= $yeast?></label></span></p>
												<div class="clearfix"></div>
												
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
										
										<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
											<div class="top_stats_wrapper custdesg bg1">
												<div class="col-md-3">
													<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
												</div>
												<?php
													$PendingQC = 0;
													$CompletedQC = 0;
													$HoldQC = 0;
													foreach($QCStatusList as $key=>$val){
														$QCStatus = $val["QCStatus"];
														$TotalItem = count($QCStatus);
														$QCStatusButton = "";
														if($TotalItem >0){
															$totalY = 0;
															$totalN = 0;
															$totalH = 0;
															$totalC = 0;
															foreach($QCStatus as $value){
																$status = $value["Status"];
																if($status == 'Y'){
																	$totalY++;
																	}elseif($status == 'N'){
																	$totalN++;
																	}elseif($status == 'H'){
																	$totalH++;
																	}else if($status == 'C'){
																	$totalC++;
																}
															}
															
															if($totalN == $TotalItem || $totalN > 0 && $totalY >0 || $totalN > 0 && $totalH >0){
																$PendingQC++;
															}
															if($totalY == $TotalItem ){
																$CompletedQC++;
															}
															if($totalH == $TotalItem || $totalN == 0 && $totalH > 0 && $totalY >0){
																$QHoldQC++;
															}
														}
													}
												?>
												<div class="col-md-9">
													<p class="mtop5 labeltxt">Todays RM/PM QC Status :<br>
														<span class="numstyl">Pending : <label id="PendingQC" class="labeltxt"><?php echo $PendingQC; ?></label></span>
														<span class="numstyl">Completed : <label id="CompletedQC" class="labeltxt"><?php echo $CompletedQC; ?></label></span>
													<span class="numstyl">Hold : <label id="HoldQC" class="labeltxt"><?php echo $HoldQC; ?></label></span></p>
													<div class="clearfix"></div>
												</div>
											</div>
										</div>
										
										<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
											<div class="top_stats_wrapper custdesg bg2">
												<div class="col-md-3">
													<p class="mtop5 imgsize"><i class="hidden-sm fa fa-shopping-cart"></i></p>
												</div>
												<div class="col-md-9">
													<p class="mtop5 labeltxt"><?php echo _l('Total Purchase Invoice'); ?><br>
														<span class="numstyl">Monthly : <?php echo $CompletedInvoices->monthly_invoice_count; ?></span>
													<span class="numstyl">Today : <?php echo $CompletedInvoices->today_invoice_count; ?></span></p>
													<div class="clearfix"></div>
													
												</div>
											</div>
										</div>
										
										<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
											<div class="top_stats_wrapper custdesg bg3">
												<div class="col-md-3">
													<p class="mtop5 imgsize"><i class="hidden-sm fa fa-user"></i></p>
												</div>
												<div class="col-md-9">
													<p class="mtop5 labeltxt"><?php echo _l('GST Paid Amount On Purchase'); ?><br>
														<span class="numstyl">Monthly : <?php echo $CompletedInvoices->monthly_invoice_gst; ?></span>
													<span class="numstyl">Today : <?php echo $CompletedInvoices->today_invoice_gst; ?></span></p>
													<div class="clearfix"></div>
													
												</div>
											</div>
										</div>
										<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-3 ">
											<div class="top_stats_wrapper custdesg bg4">
												<div class="col-md-3">
													<p class="mtop5 imgsize"><i class="hidden-sm fa fa-area-chart"></i></p>
													</div><?php
													$PMHighest = '';
													$RMHighest = '';
													foreach($TopPartyByPurchAmt as $PartyByPurchAmt){
														$company = (strlen($PartyByPurchAmt['company']) > 25) ? substr($PartyByPurchAmt['company'], 0, 25) . "..." : $PartyByPurchAmt['company'];
														if($PartyByPurchAmt['SubActGroupID'] == '1000186'){
															$RMHighest = $PartyByPurchAmt['Invamt']." - <small style='font-size:7px;' title='".$PartyByPurchAmt['company']."'>".$company."</small>";
														}
														if($PartyByPurchAmt['SubActGroupID'] == '1000188'){
															$PMHighest = $PartyByPurchAmt['Invamt']." - <small style='font-size:7px;' title='".$PartyByPurchAmt['company']."'>".$company."</small>";
														}
													}
												?>
												<div class="col-md-9">
													<p class="mtop5 labeltxt"><?php echo _l('Top Party (By Purchase Amount)'); ?><br>
														<span class="numstyl">RM : <label id="RMHighest" class="labeltxt"><?= $RMHighest?></label></span>
													<span class="numstyl">PM : <label id="PMHighest" class="labeltxt"><?= $PMHighest?></label></span></p>
													<div class="clearfix"></div>
													
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> <!-- End Widget Row-->
				
				<!-- Filet row-->
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
						// $from_date = date('d/m/Y');
						$to_date = date('d/m/Y');
					}
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3">
										<?php
											echo render_date_input('from_date','From Date',$from_date);
										?>
									</div>
									<div class="col-md-3">
										<?php
											echo render_date_input('to_date','To Date',$to_date);
										?>
									</div>
									<!--<div class="col-md-2">
										<?php echo render_input('month','month',date('Y-m'), 'month'); ?>
									</div>-->
									
									<div class="col-md-2">
										<label class="control-label">Chart Type</label>
										<select name="ChartType" id="ChartType" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
											<option value="Bar">Bar Chart</option>
											<option value="Pie">Pie Chart</option>
										</select>
									</div>
									
									<div class="col-md-2">
										<div class="form-group" app-field-wrapper="ItemCount">
											<label for="ItemCount" class="control-label">Max Count</label>
											<input type="text" id="ItemCount" onkeypress="return isNumber(event)" name="ItemCount" class="form-control" value="5">
										</div>
									</div>
									
									<div class="col-md-3">
										<div class="form-group" app-field-wrapper="SubGroup">
											<small class="req text-danger"></small>
											<label for="SubGroup" class="form-label">SubGroup</label>
											<select name="SubGroup[]" multiple id="SubGroup" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
												<?php
													foreach ($SubGroup as $key => $value) {
													?>
													<option value="<?php echo $value['id'];?>"><?php echo $value['name'];?></option>
													<?php
													}
												?>
											</select>
										</div>
									</div>
									
									<div class="col-md-3">
										<div class="form-group" app-field-wrapper="Items">
											<small class="req text-danger"></small>
											<label for="Items" class="form-label">Item</label>
											<select name="Items[]" multiple id="Items" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
												
											</select>
										</div>
									</div>
									
									<!--<div class="clearfix"></div>-->
									
									<div class="col-md-2">
										<div class="form-group" app-field-wrapper="state">
											<small class="req text-danger"></small>
											<label for="state" class="form-label">State</label>
											<select name="state" id="state" class="selectpicker form-control" data-width="100%" data-none-selected-text="None selected" data-live-search="true">
												<option value="">None selected</option>
												<?php
													foreach ($state as $key => $value) {
													?>
													<option value="<?php echo $value['short_name'];?>"><?php echo $value['state_name'];?></option>
													<?php
													}
												?>
											</select>
										</div>
									</div>
									<div class="col-md-2">
										<label class="control-label">Report In</label>
										<select name="ReportIn" id="ReportIn" class="selectpicker" data-none-selected-text="Non selected" data-width="100%" data-live-search="true" tabindex="-98">
											<option value="amount">Amount</option>
											<option value="qty">Quantity</option>
										</select>
									</div>
									
									<div class="col-md-2" style="margin-top:20px;">
										<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button> 
									</div>
									
									
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body" style="max-height: 600px;">
								<div class="row"> 
									<div class="col-md-12">
										<h4 style="text-align:center;"><b>Daily Purchase Summary</b></h4>
									</div>
									<div class="col-md-12">
										<div class="relative" style="max-height:400px">
											<canvas class="chart" height="400" id="contracts-value-by-type-chart"></canvas>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- First Column-->
					<div class="col-md-6">
						<div class="panel_s">
							<div class="panel-body" style="max-height: 600px;">
								
								<div class="row">
									<div class="col-md-12">
										<figure class="highcharts-figure">
											<div id="container"></div>
										</figure>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- Second Column-->
					<div class="col-md-6">
						<div class="panel_s">
							<div class="panel-body" style="max-height: 600px;">
								
								<div class="row">
									<div class="col-md-12">
										<figure class="highcharts-figure">
											<div id="container2"></div>
										</figure>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- Third Column-->
					<div class="col-md-6">
						<div class="panel_s">
							<div class="panel-body" style="max-height: 600px;">
								
								<div class="row">
									<div class="col-md-12">
										<figure class="highcharts-figure">
											<div id="container3"></div>
										</figure>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- Fourth Column-->
					<div class="col-md-6">
						<div class="panel_s">
							<div class="panel-body" style="max-height: 600px;">
								
								<div class="row">
									<div class="col-md-12">
										<center><span class="text-danger">Overdue Trade Payable Report</span></center>
										<hr/>
										<figure class="highcharts-figure">
											<div id="container4"></div>
										</figure>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- Fifth Column-->
					<div class="col-md-6">
						<div class="panel_s">
							<div class="panel-body" style="max-height: 600px;">
								
								<div class="row">
									<div class="col-md-12">
										<center><span class="text-danger">Pending Order Report</span></center>
										<hr/>
										<figure class="highcharts-figure">
											<div id="container5"></div>
										</figure>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					
				</div>
				
			</div>
		</div>
		
		<style>
			@import url("https://code.highcharts.com/css/highcharts.css");
			
			/*	.highcharts-pie-series .highcharts-point {
			stroke: #ede;
			stroke-width: 2px;
			}
			#wrapper{
			background: #fff;
			}
			.highcharts-pie-series .highcharts-data-label-connector {
			stroke: silver;
			stroke-dasharray: 2, 2;
			stroke-width: 2px;
			}
			
			.highcharts-figure,
			.highcharts-data-table table {
			min-width: 320px;
			max-width: 600px;
			margin: 1em auto;
			}
			
			.highcharts-data-table table {
			font-family: Verdana, sans-serif;
			border-collapse: collapse;
			border: 1px solid #ebebeb;
			margin: 10px auto;
			text-align: center;
			width: 100%;
			max-width: 500px;
			}
			
			.highcharts-data-table caption {
			padding: 1em 0;
			font-size: 1.2em;
			color: #555;
			}
			
			.highcharts-data-table th {
			font-weight: 600;
			padding: 0.5em;
			}
			
			.highcharts-data-table td,
			.highcharts-data-table th,
			.highcharts-data-table caption {
			padding: 0.5em;
			}
			
			.highcharts-data-table thead tr,
			.highcharts-data-table tr:nth-child(even) {
			background: #f8f8f8;
			}
			
			.highcharts-data-table tr:hover {
			background: #f1f7ff;
			}
			
			.highcharts-description {
			margin: 0.3rem 10px;
			}
			
			*/
			.highcharts-credits {
			display: none;
			}
			.table-table_staff tbody{
			display: block;
			max-height: 450px;
			overflow-y: scroll;
			width: calc(100% - -8.9em);
			}
			.table-table_staff thead, .table-table_staff tbody tr{
			display: table;
			table-layout: fixed;
			width: 100%;
			
			}
			.table-table_staff thead{
			width: calc(100% - -5.9em);
			}
			.table-table_staff thead{
			position: relative;
			}
			.table-table_staff thead th:last-child:after{
			content: ' ';
			position: absolute;
			background-color: #337ab7;
			width: 1.3em;
			height: 38px;
			right: -1.3em;
			top: 0;
			border-bottom: 2px solid #ddd;
			}
			
			/*.staff_name{*/
			/*width:21%;*/
			/*}*/
			.table-table_staff th td{padding: 32px -20px 12px 14px;
			}
			
			.fontsize{
			font-size:13px;
			}
			.fontsize2{
			font-size:15px;
			}
			
			thead tr:nth-child(2) th {
			top: 20px; /* Offset for the second row to appear below the first */
			}
		</style>
		
		<style>
			.table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
			.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
			.table-daily_report tbody th { position: sticky; left: 0; }
			
			
			table  { border-collapse: collapse; width: 100%; }
			th, td { padding: 0px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
			th     { background: #50607b;
			color: #fff !important; }
			
			.custdesg{
			height:90px;
			}
			.imgsize{
			font-size:40px;
			display: block;
			margin: 0;
			color: #fff;
			}
			.panel_s{
			margin-bottom:5px !important;
			}
			.labeltxt{
			font-size:14px;
			font-weight:400;
			color: #fff;
			}
			.numstyl{
			text-align: left;
			display: block;
			font-size: 14px;
			}
			.mtop5 {
			margin-top: 4px;
			margin-bottom: 2px;
			}
			.bg1{
			background-image: linear-gradient(to right,#008385 0,#008385 100%);
			background-repeat: repeat-x;
			}
			.bg2{
			background-image: linear-gradient(to right,#FF425C 0,#FF425C 100%);
			background-repeat: repeat-x;
			}
			.bg3{
			background-image: linear-gradient(to right,#FF864A 0,#FF864A 100%);
			background-repeat: repeat-x;
			}
			.bg4{
			background-image: linear-gradient(to right,#11A578 0,#11A578 100%);
			background-repeat: repeat-x;
			}
			.top_stats_wrapper{
			margin-top: 0px;
			border-radius: 5px;
			padding:0px !important;
			margin-bottom: 10px !important;
			}
			.top_stats_wrapper:hover{
			box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.4);
			}
			
			
		</style>
		
		<?php init_tail(); ?>
		<!--new update -->
		
		<script>
			$('#SubGroup').on('change',function(){
				var SubGroup = $("#SubGroup").val();
				$.ajax({
					url:"<?php echo admin_url(); ?>sale_reports/GetGroupWiseItemList",
					dataType:"JSON",
					method:"POST",
					data:{SubGroup:SubGroup},
					beforeSend: function () {
					},
					complete: function () {
					},
					success:function(data){
						let ItemList = data;
						$("#Items").children().remove();
						for (var i = 0; i < ItemList.length; i++) {
							$("#Items").append('<option value="'+ItemList[i]["item_code"]+'">'+ItemList[i]["description"]+'</option>');
						}
						$('.selectpicker').selectpicker('refresh');
					}
				});
			});
			
			$(document).ready(function(){
				$('#search_data_counter').on('click',function(){
					var from_date = $("#from_date2").val();
					var to_date = $("#to_date2").val();
					
					GetCountersValue(from_date,to_date);
				});
				
				function GetCountersValue(from_date,to_date)
				{
					$.ajax({
						url:"<?php echo admin_url(); ?>purchase/GetGetPurchaseCounters",
						dataType:"JSON",
						method:"POST",
						data:{from_date:from_date,to_date:to_date},
						beforeSend: function () {
						},
						complete: function () {
						},
						success:function(returndata){
							var PurchaseStatus = returndata.PurchaseStatus;
							var PurchaseEntryStatus = returndata.PurchaseEntryStatus;
							var PurchaseSKU = returndata.PurchaseSKU;
							var PurchaseVendors = returndata.PurchaseVendors;
							var CompletedInvoices = returndata.CompletedInvoices;
							var RMLowestPurchaseSKU = returndata.RMLowestPurchaseSKU;
							var PMLowestPurchaseSKU = returndata.PMLowestPurchaseSKU;
							var TopPartyByPurchAmt = returndata.TopPartyByPurchAmt;
							var QCStatusList = returndata.QCStatusList;
							
							var PendingPurchase = 0;
							var ApprovedPurchase = 0;
							var AllPurchase = 0;
							
							// Iterate through get counts
							$.each(PurchaseStatus, function (index, Purchase) {
								if (Purchase.cur_status === "pending") {
									PendingPurchase = Purchase.count;
								}
								if (Purchase.cur_status === "Approved") {
									ApprovedPurchase = Purchase.count;
								}
							});
							AllPurchase = parseFloat(PendingPurchase) + parseFloat(ApprovedPurchase);
							
							var RawMaterial = 0;
							var PackingMaterial = 0;
							var AllSKUs = 0;
							// Iterate through get counts
							$.each(PurchaseSKU, function (index, GroupSKU) {
								if (GroupSKU.MainGrpID === "2") {
									RawMaterial++;
								}
								if (GroupSKU.MainGrpID === "3") {
									PackingMaterial++;
								}
							});
							AllSKUs = parseFloat(RawMaterial) + parseFloat(PackingMaterial);
							
							
							var maida = "";
							var atta = "";
							var yeast = "";
							
							$.each(RMLowestPurchaseSKU, function (index, LowestSKU) {
								var company =
								LowestSKU.company.length > 25
								? LowestSKU.company.substring(0, 25) + "..."
								: LowestSKU.company;
								
								var formattedData =
								LowestSKU.BasicRate +
								" - <small style='font-size:7px;' title='" +
								LowestSKU.company +
								"'>" +
								company +
								"</small>";
								
								if (LowestSKU.SubGrpID2 === "17") {
									maida = formattedData;
									} else if (LowestSKU.SubGrpID2 === "18") {
									atta = formattedData;
									} else if (LowestSKU.SubGrpID2 === "69") {
									yeast = formattedData;
								}
							});
							
							
							
							var PendingEntry = 0;
							var CompletedEntry = 0;
							var AllEntry = 0;
							// Iterate through get counts
							$.each(PurchaseEntryStatus, function (index, PurchaseEntry) {
								if (PurchaseEntry.cur_status === "Completed") {
									CompletedEntry = PurchaseEntry.count;
								}
								if (PurchaseEntry.cur_status === "Pending") {
									PendingEntry = PurchaseEntry.count;
								}
							});
							AllEntry = parseFloat(CompletedEntry) + parseFloat(PendingEntry);
							
							
							var RawMaterialVendor = 0;
							var PackingMaterialVendor = 0;
							var AllVendor = 0;
							// Iterate through get counts
							$.each(PurchaseVendors, function (index, PVendors) {
								if (PVendors.SubActGroupID === "1000186") {
									RawMaterialVendor++;
								}
								if (PVendors.SubActGroupID === "1000188") {
									PackingMaterialVendor++;
								}
							});
							AllVendor = parseFloat(RawMaterialVendor) + parseFloat(PackingMaterialVendor);
							
							
							var Lowestmaida = "";
							var Lowestatta = "";
							var Lowestyeast = "";
							
							$.each(PMLowestPurchaseSKU, function (key, PMLowestSKU) {
								let description =
								PMLowestSKU.description.length > 25
								? PMLowestSKU.description.substring(0, 25) + "..."
								: PMLowestSKU.description;
								
								let formattedData =
								PMLowestSKU.BasicRate +
								" - <small style='font-size:7px;' title='" +
								PMLowestSKU.company +
								"'>" +
								description +
								"</small>";
								
								if (key == 0) {
									Lowestmaida = formattedData;
									} else if (key == 1) {
									Lowestatta = formattedData;
									} else if (key == 2) {
									Lowestyeast = formattedData;
								}
							});
							
							var PendingQC = 0;
							var CompletedQC = 0;
							var HoldQC = 0;
							
							$.each(QCStatusList, function (key, val) {
								var QCStatus = val.QCStatus;
								var TotalItem = QCStatus.length;
								
								if (TotalItem > 0) {
									var totalY = 0;
									var totalN = 0;
									var totalH = 0;
									var totalC = 0;
									
									$.each(QCStatus, function (index, value) {
										let status = value.Status;
										if (status === "Y") {
											totalY++;
											} else if (status === "N") {
											totalN++;
											} else if (status === "H") {
											totalH++;
											} else if (status === "C") {
											totalC++;
										}
									});
									
									if (
									totalN === TotalItem ||
									(totalN > 0 && totalY > 0) ||
									(totalN > 0 && totalH > 0)
									) {
										PendingQC++;
									}
									if (totalY === TotalItem) {
										CompletedQC++;
									}
									if (
									totalH === TotalItem ||
									(totalN === 0 && totalH > 0 && totalY > 0)
									) {
										HoldQC++;
									}
								}
							});
							
							var PMHighest = "";
							var RMHighest = "";
							
							$.each(TopPartyByPurchAmt, function (index, PartyByPurchAmt) {
								var company =
								PartyByPurchAmt.company.length > 25
								? PartyByPurchAmt.company.substring(0, 25) + "..."
								: PartyByPurchAmt.company;
								
								var formattedData =
								PartyByPurchAmt.Invamt +
								" - <small style='font-size:7px;' title='" +
								PartyByPurchAmt.company +
								"'>" +
								company +
								"</small>";
								
								if (PartyByPurchAmt.SubActGroupID === "1000186") {
									RMHighest = formattedData;
									} else if (PartyByPurchAmt.SubActGroupID === "1000188") {
									PMHighest = formattedData;
								}
							});
							
							$("#AllPurchase").html(AllPurchase);
							$("#PendingPurchase").html(PendingPurchase);
							$("#ApprovedPurchase").html(ApprovedPurchase);
							$("#RawMaterial").html(RawMaterial);
							$("#PackingMaterial").html(PackingMaterial);
							$("#AllSKUs").html(AllSKUs);
							$("#maida").html(maida);
							$("#atta").html(atta);
							$("#yeast").html(yeast);
							$("#PendingEntry").html(PendingEntry);
							$("#CompletedEntry").html(CompletedEntry);
							$("#AllEntry").html(AllEntry);
							$("#RawMaterialVendor").html(RawMaterialVendor);
							$("#PackingMaterialVendor").html(PackingMaterialVendor);
							$("#AllVendor").html(AllVendor);
							$("#Lowestmaida").html(Lowestmaida);
							$("#Lowestatta").html(Lowestatta);
							$("#Lowestyeast").html(Lowestyeast);
							$("#PendingQC").html(PendingQC);
							$("#CompletedQC").html(CompletedQC);
							$("#HoldQC").html(HoldQC);
							$("#RMHighest").html(RMHighest);
							$("#PMHighest").html(PMHighest);
							
							
							
						}
					});
				}
				$('#search_data').on('click',function(){
					var from_date = $("#from_date").val();
					var to_date = $("#to_date").val();
					var ChartType = $("#ChartType").val();
					var MaxCount = $("#ItemCount").val();
					var SubGroup = $("#SubGroup").val();
					var ReportIn = $("#ReportIn").val();
					var Items = $("#Items").val();
					var state = $("#state").val();
					
					
					load_datasummary(from_date,to_date,Items,SubGroup,state,ReportIn);
					load_data(from_date,to_date,ChartType,MaxCount,state,SubGroup,Items,'2',ReportIn);
					load_data2(from_date,to_date,ChartType,MaxCount,state,SubGroup,Items,'3',ReportIn);
					load_data3(from_date,to_date,ChartType,MaxCount,state,SubGroup,Items,'1',ReportIn);
					load_data4(from_date,to_date);
					load_data5(from_date,to_date);
				});
				
				function load_datasummary(from_date,to_date,Items,SubGroup,state,ReportIn)
				{
					$.ajax({
						url:"<?php echo admin_url(); ?>purchase/GetDailyPurchaseReports",
						dataType:"JSON",
						method:"POST",
						data:{from_date:from_date,to_date:to_date,Items:Items,SubGroup:SubGroup,state:state,ReportIn:ReportIn},
						beforeSend: function () {
						},
						complete: function () {
						},
						success:function(returndata){
							new Chart($('#contracts-value-by-type-chart'), {
								type: 'line',
								data: returndata,
								options: {
									responsive: true,
									legend: {
										display: false,
									},
									maintainAspectRatio:false,
									scales: {
										yAxes: [{
											display: true,
											ticks: {
												suggestedMin: 0,
											}
										}]
									}
								}
							});
						}
					});
				}
				function load_data(from_date,to_date,ChartType,MaxCount,state,SubGroup,Items,maingroupid,ReportIn)
				{
					if(ReportIn == 'qty'){
						var newtitle = 'Purchase Qty (Unit)';
					}else{
						var newtitle = 'Purchase Amt';
					}
					$.ajax({
						url:"<?php echo admin_url(); ?>purchase/GetTopPurchaseItem",
						dataType:"JSON",
						method:"POST",
						data:{from_date:from_date,to_date:to_date,ChartType:ChartType,MaxCount:MaxCount,state:state,SubGroup:SubGroup,Items:Items,maingroupid:maingroupid,ReportIn:ReportIn},
						beforeSend: function () {
						},
						complete: function () {
						},
						success:function(returndata){
							if(ChartType == "Pie"){
								Highcharts.chart('container', {
									chart: {
										styledMode: true,  
										height: 600, // Increase chart height
										spacing: [10, 100, 10, 10],
									},
									title: {
										text: '',
									},
									subtitle: {
										text: '<b>Top Purchase RM Items  '+from_date+' To '+to_date+'</b>'
									},
									plotOptions: {
										pie: {
											size: '70%', // Force the pie to occupy 90% of the chart area
											dataLabels: {
												enabled: true,
												distance: 10, // Move data labels closer to the pie
												style: {
													fontSize: '14px'
												}
											}
										}
									},
									series: [{
										type: 'pie',
										allowPointSelect: true,
										keys: ['name', 'y', 'selected', 'sliced'],
										data: returndata.ChartData,
										showInLegend: true
									}],
									legend: {
										layout: 'horizontal', // Arrange legend items horizontally
										align: 'center', // Center-align the legend
										verticalAlign: 'bottom', // Place legend at the bottom
										itemWidth: 150, // Control the width of each legend item for better wrapping
										itemStyle: {
											fontSize: '14px'
										}
									},
								});
							}
							
							if(ChartType == "Bar"){
								Highcharts.chart('container', {
									chart: {
										type: 'column'
									},
									title: {
										text: ''
									},
									subtitle: {
										text: '<b>Top Purchase RM Items  '+from_date+' To '+to_date+'</b>'
									},
									xAxis: {
										type: 'category',
										labels: {
											autoRotation: [-45, -90],
										}
									},
									yAxis: {
										min: 0,
										title: {
											text: newtitle,
										}
									},
									legend: {
										enabled: false
									},
									tooltip: {
										pointFormat: '{point.label:.1f} : <b>{point.y:.1f} </b>'
									},
									series: [{
										name: 'Population',
										colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
										colorByPoint: true,
										groupPadding: 0,
										data: returndata.ChartData,
										dataLabels: {
											enabled: true,
											rotation: -90,
											color: '#FFFFFF',
											inside: true,
											verticalAlign: 'top',
											format: '{point.y:.1f}', // one decimal
											y: 10, // 10 pixels down from the top
											
										}
									}]
								});
							}
						}
					});
				}
				function load_data2(from_date,to_date,ChartType,MaxCount,state,SubGroup,Items,maingroupid,ReportIn)
				{
					if(ReportIn == 'qty'){
						var newtitle = 'Purchase Qty (Unit)';
					}else{
						var newtitle = 'Purchase Amt';
					}
					$.ajax({
						url:"<?php echo admin_url(); ?>purchase/GetTopPurchaseItem",
						dataType:"JSON",
						method:"POST",
						data:{from_date:from_date,to_date:to_date,ChartType:ChartType,MaxCount:MaxCount,state:state,SubGroup:SubGroup,Items:Items,maingroupid:maingroupid,ReportIn:ReportIn},
						beforeSend: function () {
						},
						complete: function () {
						},
						success:function(returndata){
							if(ChartType == "Pie"){
								Highcharts.chart('container2', {
									chart: {
										styledMode: true,  
										height: 600, // Increase chart height
										spacing: [10, 100, 10, 10],
									},
									title: {
										text: '',
									},
									subtitle: {
										text: '<b>Top Purchase PM Items '+from_date+' To '+to_date+'</b>'
									},
									plotOptions: {
										pie: {
											size: '70%', // Force the pie to occupy 90% of the chart area
											dataLabels: {
												enabled: true,
												distance: 10, // Move data labels closer to the pie
												style: {
													fontSize: '14px'
												}
											}
										}
									},
									series: [{
										type: 'pie',
										allowPointSelect: true,
										keys: ['name', 'y', 'selected', 'sliced'],
										data: returndata.ChartData,
										showInLegend: true
									}],
									legend: {
										layout: 'horizontal', // Arrange legend items horizontally
										align: 'center', // Center-align the legend
										verticalAlign: 'bottom', // Place legend at the bottom
										itemWidth: 150, // Control the width of each legend item for better wrapping
										itemStyle: {
											fontSize: '14px'
										}
									},
								});
							}
							
							if(ChartType == "Bar"){
								Highcharts.chart('container2', {
									chart: {
										type: 'column'
									},
									title: {
										text: ''
									},
									subtitle: {
										text: '<b>Top Purchase PM Items  '+from_date+' To '+to_date+'</b>'
									},
									xAxis: {
										type: 'category',
										labels: {
											autoRotation: [-45, -90],
										}
									},
									yAxis: {
										min: 0,
										title: {
											text: newtitle,
										}
									},
									legend: {
										enabled: false
									},
									tooltip: {
										pointFormat: '{point.label:.1f} : <b>{point.y:.1f} </b>'
									},
									series: [{
										name: 'Population',
										colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
										colorByPoint: true,
										groupPadding: 0,
										data: returndata.ChartData,
										dataLabels: {
											enabled: true,
											rotation: -90,
											color: '#FFFFFF',
											inside: true,
											verticalAlign: 'top',
											format: '{point.y:.1f}', // one decimal
											y: 10, // 10 pixels down from the top
											
										}
									}]
								});
							}
						}
					});
				}
				function load_data3(from_date,to_date,ChartType,MaxCount,state,SubGroup,Items,maingroupid,ReportIn)
				{
					if(ReportIn == 'qty'){
						var newtitle = 'Purchase Qty (Unit)';
					}else{
						var newtitle = 'Purchase Amt';
					}
					$.ajax({
						url:"<?php echo admin_url(); ?>purchase/GetTopPurchaseItem",
						dataType:"JSON",
						method:"POST",
						data:{from_date:from_date,to_date:to_date,ChartType:ChartType,MaxCount:MaxCount,state:state,SubGroup:SubGroup,Items:Items,maingroupid:maingroupid,ReportIn:ReportIn},
						beforeSend: function () {
						},
						complete: function () {
						},
						success:function(returndata){
							if(ChartType == "Pie"){
								Highcharts.chart('container', {
									chart: {
										styledMode: true,  
										height: 600, // Increase chart height
										spacing: [10, 100, 10, 10],
									},
									title: {
										text: '',
									},
									subtitle: {
										text: '<b>Top Purchase FG Items  '+from_date+' To '+to_date+'</b>'
									},
									plotOptions: {
										pie: {
											size: '70%', // Force the pie to occupy 90% of the chart area
											dataLabels: {
												enabled: true,
												distance: 10, // Move data labels closer to the pie
												style: {
													fontSize: '14px'
												}
											}
										}
									},
									series: [{
										type: 'pie',
										allowPointSelect: true,
										keys: ['name', 'y', 'selected', 'sliced'],
										data: returndata.ChartData,
										showInLegend: true
									}],
									legend: {
										layout: 'horizontal', // Arrange legend items horizontally
										align: 'center', // Center-align the legend
										verticalAlign: 'bottom', // Place legend at the bottom
										itemWidth: 150, // Control the width of each legend item for better wrapping
										itemStyle: {
											fontSize: '14px'
										}
									},
								});
							}
							
							if(ChartType == "Bar"){
								Highcharts.chart('container3', {
									chart: {
										type: 'column'
									},
									title: {
										text: ''
									},
									subtitle: {
										text: '<b>Top Purchase FG Items '+from_date+' To '+to_date+'</b>'
									},
									xAxis: {
										type: 'category',
										labels: {
											autoRotation: [-45, -90],
										}
									},
									yAxis: {
										min: 0,
										title: {
											text: newtitle,
										}
									},
									legend: {
										enabled: false
									},
									tooltip: {
										pointFormat: '{point.label:.1f} : <b>{point.y:.1f} </b>'
									},
									series: [{
										name: 'Population',
										colors: [ '#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
										colorByPoint: true,
										groupPadding: 0,
										data: returndata.ChartData,
										dataLabels: {
											enabled: true,
											rotation: -90,
											color: '#FFFFFF',
											inside: true,
											verticalAlign: 'top',
											format: '{point.y:.1f}', // one decimal
											y: 10, // 10 pixels down from the top
											
										}
									}]
								});
							}
						}
					});
				}
				function load_data4(from_date,to_date)
				{
					$.ajax({
						url:"<?php echo admin_url(); ?>purchase/GetBillPayableDashboardReport",
						dataType:"JSON",
						method:"POST",
						data:{from_date:from_date,to_date:to_date},
						beforeSend: function () {
						},
						complete: function () {
						},
						success:function(returndata){
							$('#container4').html(returndata);
						}
					});
				}
				function load_data5(from_date,to_date)
				{
					$.ajax({
						url:"<?php echo admin_url(); ?>purchase/GetPendingOrderDashboardReport",
						dataType:"JSON",
						method:"POST",
						data:{from_date:from_date,to_date:to_date},
						beforeSend: function () {
						},
						complete: function () {
						},
						success:function(returndata){
							$('#container5').html(returndata);
						}
					});
				}
				
				$('#search_data').click();
				$('#search_data_counter').click();
				
			});
			
		</script>
		<script>
			function isNumber(evt) {
				evt = (evt) ? evt : window.event;
				var charCode = (evt.which) ? evt.which : evt.keyCode;
				if (charCode = 46 && charCode > 31 
				&& (charCode < 48 || charCode > 57)){
					return false;
				}
				return true;
			}
		</script>
		<script type="text/javascript">
			function printPage(){
				
				var from_date = $("#from_date").val();
				var to_date = $("#to_date").val();
				var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
				var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
				var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->FIRMNAME; ?></td></tr><tr><td style="text-align:center;" colspan="9"><?php echo $PlantDetail->ADDRESS1.' '.$PlantDetail->ADDRESS2; ?></td></tr>';
				heading_data += '<tr>';
				heading_data += '<td style="text-align:center;"colspan="9">Sales Report : '+from_date+' To '+to_date+'</td>';
				heading_data += '</tr>';
				heading_data += '</tbody></table>';
				var print_data = stylesheet+heading_data+tableData
				newWin= window.open("");
				newWin.document.write(print_data);
				newWin.print();
				newWin.close();
			};
		</script>
		
		<script>
			$(document).ready(function(){
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
				
				$(document).on("click", ".sortable", function () {
					var table = $("#table-daily_report tbody");
					var rows = table.find("tr").toArray();
					var index = $(this).index();
					var ascending = !$(this).hasClass("asc");
					
					
					// Remove existing sort classes and reset arrows
					$(".sortable").removeClass("asc desc");
					$(".sortable span").remove();
					
					// Add sort classes and arrows
					$(this).addClass(ascending ? "asc" : "desc");
					$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');
					
					rows.sort(function (a, b) {
						var valA = $(a).find("td").eq(index).text().trim();
						var valB = $(b).find("td").eq(index).text().trim();
						
						if ($.isNumeric(valA) && $.isNumeric(valB)) {
							return ascending ? valA - valB : valB - valA;
							} else {
							return ascending
							? valA.localeCompare(valB)
							: valB.localeCompare(valA);
						}
					});
					table.append(rows);
				});
				
				
			});
		</script> 									