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
			</div>
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
							$from_date = '31/03/20'.$fy_new;
							}else{
							$from_date = date('d/m/Y');
						}
						if(!empty($Date)){
							$from_date = $Date;
						}
					?> 
					<?php //$cur_date = _d(date('Y-m-d')); ?>
					<?php echo render_date_input('as_on_date','As On Date',$from_date); ?>
				</div>
				<div class="col-md-10">
					<br>
					<button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
					
                    <?php if (has_permission_new('TFormatBalanceSheet', '', 'print')) {
					?>
                    <a class="btn btn-default" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" onclick="printPage();">Print</a>
                    <?php } ?>
                    <?php if (has_permission_new('TFormatBalanceSheet', '', 'export')) {
					?>
                    <a class="btn btn-default" id="caexcel" href="javascript:void(0);" style="margin-bottom: 20px;margin-left: 10px;" ><i class="fa fa-spinner fa-spin Loader" style="display:none;"></i> Export</a>
                    <?php } ?>
                    <label class="" style="margin-left: 30px; display: inline-block;">
						<input type="checkbox" id="extendAll" onchange="toggleExtendAll()"> Expand All
					</label>
				</div>
				
			</div>
			<?php
				if(!empty($Date)){
				?>
				<div class="row">
					<div class="col-md-12">
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
							$CurrentYrFirstDate = '01/04/20' . $fy;
						?>
						<div class="page">
							<div id="accordion">
								<div class="card">
									<div class="row" id="DivIdToPrint">
										<div class="col-md-6">
											<table class="tree">
												<thead>
													<tr class="tr_header" id="tr_header">
														<th style="text-align:center;" colspan="3" ><?php echo $CurrentYrFirstDate.' - '.$from_date; ?></th>
													</tr>
													<tr class="tr_header" id="tr_header">
														<th>Particular</th>
														<th class="th_total" colspan="2" ></th>
													</tr>
												</thead>
												
												<tbody>
													<?php
														$MainCounter = 1000;
														$SubCounter = 2000;
														$Counter2 = 3000;
														$i = 1;
														foreach ($nestedData as $key => $val) {
															if($i > 1){
																continue;
															}
														?>
														<tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded"
                                                        style="font-size:14px;"id="maingroup">
															<td class="parent" style="font-size:13px;font-weight:600;"><?php echo $val['MainGroup']; ?></td>
															<td></td>
															<td></td>
														</tr>
														<?php
															
															foreach ($val['SubGroups1'] as $key1 => $val1) {
																
															?>
															<tr class="treegrid-<?php echo html_entity_decode($SubCounter); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
															id="subgroup">
																<td class="parent" style="font-size:12px;font-weight:400;"><?php echo strtoupper($val1["SubGroup1Name"]); ?></td>
																
																<td style="text-align:right;font-size:12px;font-weight:600;"></td>
																<td style="text-align:right;font-size:12px;font-weight:600;"><?php echo number_format($val1['Group1ClsBal'], 2, '.', ''); ?></td>
															</tr>
															
															<?php
																foreach ($val1['SubGroups'] as $key2 => $val2) {
																	
																?>
																<tr
                                                                class="treegrid-<?php echo html_entity_decode($Counter2); ?> treegrid-parent-<?php echo $SubCounter; ?> parent-node " id="subgroup1">
																	<td class="parent"><?php echo strtoupper($val2["SubGroupName"]); ?></td>
																	
																	<td style="text-align:right;font-weight:600;font-size:12px;"><?php echo number_format($val2["Group2ClsBal"], 2, '.', '');?></td>
																	<td style="text-align:right;font-weight:600;font-size:12px;"></td>
																</tr>
																<?php
																	foreach ($val2['Accounts'] as $key3 => $val3) {
																		$AccountBal = $val3["AccountClsBal"];
																		$AcountBalPre = $val3["AccountClsBalPre"];
																	?>
																	<tr class="treegrid-<?php echo html_entity_decode($Counter3); ?> treegrid-parent-<?php echo $Counter2; ?> parent-node " id="Accounts">
																		<td class="parent" style="cursor:pointer;" onclick="RedirectLedger('<?php echo strtoupper($val3["AccountID"]); ?>')"><?php echo strtoupper($val3["AccountName"]); ?></td>
																		<td style="text-align:right;"><?php echo number_format($AccountBal, 2, '.', '');?></td>
																		<td style="text-align:right;"></td>
																		
																		<?php
																			$Counter3 ++;
																		}
																	?>
																	<?php
																		$Counter2 ++;
																	}
																?>
																<?php
																	$SubCounter ++;
																}
															?>
															<tr id="maingroup_bottom">
																<th  style="font-size:13px;font-weight:700;">Total for <?php echo $val['MainGroup']; ?></th>
																<th colspan="2" style="text-align:right;font-size:13px;font-weight:700;">
																	<?php echo number_format($val['MainGroupClsBal'], 2, '.', ''); ?>
																</th>
															</tr>
															<?php
																$MainCounter++;
																$i++;
															}
														?>
													</tbody>
												</table>
											</div>
											
											<div class="col-md-6">
												<table class="tree">
													<thead>
														<tr class="tr_header" id="tr_header">
															<th style="text-align:center;" colspan="3" ><?php echo $CurrentYrFirstDate.' - '.$from_date; ?></th>
														</tr>
														<tr class="tr_header" id="tr_header">
															<th>Particular</th>
															<th class="th_total" colspan="2" ></th>
														</tr>
													</thead>
													
													<tbody>
														<?php
															$MainCounter = 1000;
															$SubCounter = 2000;
															$Counter2 = 3000;
															$Counter3 = 4000;
															$i = 1;
															foreach ($nestedData as $key => $val) {
																if($i > 1){
																?>
																<tr class="treegrid-<?php echo $MainCounter; ?> parent-node expanded"
																style="font-size:14px;"id="maingroup">
																	<td class="parent" style="font-size:13px;font-weight:600;"><?php echo $val['MainGroup']; ?></td>
																	<td></td>
																	<td></td>
																</tr>
																<?php
																	
																	foreach ($val['SubGroups1'] as $key1 => $val1) {
																		
																	?>
																	<tr class="treegrid-<?php echo html_entity_decode($SubCounter); ?> treegrid-parent-<?php echo $MainCounter; ?> parent-node  "
																	id="subgroup">
																		<td class="parent" style="font-size:12px;font-weight:400;"><?php echo strtoupper($val1["SubGroup1Name"]); ?></td>
																		
																		<td style="text-align:right;font-size:12px;font-weight:600;"></td>
																		<td style="text-align:right;font-size:12px;font-weight:600;"><?php echo number_format($val1['Group1ClsBal'], 2, '.', ''); ?></td>
																	</tr>
																	
																	<?php
																		foreach ($val1['SubGroups'] as $key2 => $val2) {
																			
																		?>
																		<tr
																		class="treegrid-<?php echo html_entity_decode($Counter2); ?> treegrid-parent-<?php echo $SubCounter; ?> parent-node " id="subgroup1">
																			<td class="parent"><?php echo strtoupper($val2["SubGroupName"]); ?></td>
																			
																			<td style="text-align:right;font-weight:600;font-size:12px;"><?php echo number_format($val2["Group2ClsBal"], 2, '.', '');?></td>
																			<td style="text-align:right;font-weight:600;font-size:12px;"></td>
																		</tr>
																		<?php
																			foreach ($val2['Accounts'] as $key3 => $val3) {
																				$AccountBal = $val3["AccountClsBal"];
																				$AcountBalPre = $val3["AccountClsBalPre"];
																			?>
																			<tr class="treegrid-<?php echo html_entity_decode($Counter3); ?> treegrid-parent-<?php echo $Counter2; ?> parent-node " id="Accounts">
																				<td class="parent"><?php echo strtoupper($val3["AccountName"]); ?></td>
																				<td style="text-align:right;"><?php echo number_format($AccountBal, 2, '.', '');?></td>
																				<td style="text-align:right;"></td>
																				
																				<?php
																					$Counter3 ++;
																				}
																			?>
																			<?php
																				$Counter2 ++;
																			}
																		?>
																		<?php
																			$SubCounter ++;
																		}
																	?>
																	<tr id="maingroup_bottom">
																		<th style="font-size:13px;font-weight:700;">Total for <?php echo $val['MainGroup']; ?></th>
																		
																		<th colspan="2" style="text-align:right;font-size:13px;font-weight:700;">
																			<?php echo number_format($val['MainGroupClsBal'], 2, '.', ''); ?>
																		</th>
																	</tr>
																	<?php
																		$MainCounter++;
																	}
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
						<?php
						}
					?>
				</div>
			</div>
			
	
			
<?php init_tail(); ?>
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
            if(IdName == "maingroup" || IdName == "maingroup_bottom" || IdName == "subgroup" || IdName == "tr_header"){
                
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
            if(IdName == "maingroup" || IdName == "maingroup_bottom" || IdName == "subgroup" || IdName == "tr_header"){
                
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
		
			
	<script>
		function printPage() {
			
			var as_on_date = $("#as_on_date").val();
			var stylesheet = '<style type="text/css">body { font-family: Arial, sans-serif; font-size:12px; }th, td { padding: 5px; border: 1px solid #000; border-collapse: collapse; font-size: 12px; }table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }.print-header { text-align:center; font-weight:bold; border: 1px solid #000; border-collapse: collapse; font-size: 12px; }.row { display: flex; justify-content: space-between; }.col-md-6 { width: 48%; }</style>';
			
			var heading_data = '<div class="print-header"><div style="border-bottom: 1px solid #000; padding:5px;"><?php echo $company_detail->company_name; ?></div><div style="border-bottom: 1px solid #000; padding:5px;"><?php echo $company_detail->address; ?></div><div style="padding:5px;"> Balance Sheet Statement As on '+as_on_date+'</div></div>';
			
			var content = document.getElementById('DivIdToPrint').innerHTML;
			
			var printWindow = window.open('', '', 'height=600,width=1000');
			printWindow.document.write('<html><head><title>Balance Sheet</title>');
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
			var as_on_date = $("#as_on_date").val();
			$.ajax({
				url:"<?php echo admin_url(); ?>accounting/export_TFormatBalanceSheet",
				method:"POST",
				data: {as_on_date:as_on_date},
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
		
	</script>
			
	<script>
		$(document).ready(function(){
			$('#search_data').on('click', function () {
				var as_on_date = $("#as_on_date").val();  // e.g., "02/08/2025"
				
				if (as_on_date) {
					var parts = as_on_date.split('/');
					if (parts.length === 3) {
						var formatted_date = parts[2] + '-' + parts[1] + '-' + parts[0];  
						
						var redirect_url = '<?php echo admin_url(); ?>accounting/TFormatBalanceSheet/' + formatted_date;
						
						window.location.href = redirect_url;  // Perform redirect
						} else {
						alert("Invalid date format. Please use dd/mm/yyyy.");
					}
				} else {
					alert("Please enter a date.");
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