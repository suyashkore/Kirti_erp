 <?php init_head();?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="panel_s col-md-12">
				<div class="panel-body">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
							<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
							<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
							<li class="breadcrumb-item active" aria-current="page"><b>Payment Entry</b></li>
						</ol>
					</nav>
					<hr class="hr_style">
					<div class="row">
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
								$to_date = date('d/m/Y');
							}
						?> 
						
						<?php  if(isset($payment_entry)){  ?>
							<input type="hidden" name="VoucheriD" value="<?php echo $payment_entry->VoucherID; ?>">
							<input type="hidden" name="UniqueID" id= "UniqueID" value="<?php echo $payment_entry->UniquID; ?>">
						<?php  }  ?>
						<div class="col-md-2">
							<?php  echo render_date_input('from_date','From',$from_date);  ?>
						</div>
						<div class="col-md-2">
							<?php  echo render_date_input('to_date','To',$to_date);  ?>
						</div>
						<div class="col-md-2">
							<?php $is_approve_value = (isset($payment_entry) ? $payment_entry->Status : 'N'); ?>
							<?php 
								$approve_options = array(
    								array('value' => 'Y', 'name' => 'Yes'),
    								array('value' => 'N', 'name' => 'No')
								);
							echo render_select('Isapprove', $approve_options, array('value', 'name'), 'Is Approve', $is_approve_value); ?>
						</div>
						<div class="col-md-2">
							<?php $PassedFrom = "PAYMENTS"; ?>
							<?php 
								$PassedFromOption = array(
    								array('value' => 'PAYMENTS', 'name' => 'PAYMENTS'),
    								array('value' => 'RECEIPTS', 'name' => 'RECEIPTS')
								);
							echo render_select('PassedFrom', $PassedFromOption, array('value', 'name'), 'Voucher Type', $PassedFrom); ?>
						</div>
						 
						<div class="col-md-2"><br>							 
							<button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
						</div>
						
				    </div>	
				    <div class="row">
				        <div class="col-md-8">
				            <?php
				                if (has_permission_new('accounting_voucher_entry_approve', '', 'edit')) {
				            ?>
				                <button id="approve_selected" class="btn btn-primary mt-3">Approve Selected</button>
				            <?php
                        		}else{
                        		    ?>
                        		    <button id="approve_selected" disabled class="btn btn-primary mt-3">Approve Selected</button>
                        		    <?php
                        		}
				            ?>
														
						</div> 
						<div class="col-md-4"> 
							<input type="text" class="form-control" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" >
						</div>
						<div class="col-md-12">
							<div class="table_payment_report">
								<table class="tree table table-striped table-bordered table_payment_report" id="table_payment_report" width="100%">
									<thead>
										<tr>
											<th width="20px"><input type="checkbox" id="select_all"></th>
											<th class="sortablePop"style="text-align:left;">VoucherNo</th>
											<th class="sortablePop" style="text-align:left;">VoucherDate</th>
											<th class="sortablePop" style="text-align:left;">AccountID</th>
											<th class="sortablePop" style="text-align:left;">Account Name</th>
											<th class="sortablePop" style="text-align:left;">Dr/Cr</th>
											<th class="sortablePop" style="text-align:left;">Amount</th>
											<th class="sortablePop" style="text-align:left;">Narration</th>
											<th class="sortablePop" style="text-align:left;">Status</th>  
											<th class="sortablePop" style="text-align:left;">Action</th> 
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>   
							</div>
							<span id="searchh2" style="display:none;">Loading.....</span>							
						</div>
				    </div>
						
						
					 					
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	.table_payment_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
	.table_payment_report thead th { position: sticky; top: 0; z-index: 1; }
	.table_payment_report tbody th { position: sticky; left: 0; }
	
	/* Just common table stuff. Really. */
	.table_payment_report table  { border-collapse: collapse; width: 100%; }
	.table_payment_report th, td { padding: 3px 3px !important; white-space: nowrap; font-size:11px; line-height:1.42857143;vertical-align: middle;}
	.table_payment_report th     { background: #50607b;color: #fff !important; }
	
	
	#table_payment_report tr:hover {
	background-color: #ccc;
	}
	
	#table_payment_report td:hover {
	cursor: pointer;
	}
	
	.table_payment_report td[rowspan] {
    vertical-align: middle;
    border-right: 1px solid #ddd;
	 text-align: center;
}
 

/* Style for select all checkbox */
#selectAll {
    margin-left: 8px;
}
</style>
<?php init_tail(); ?>
 <script type="text/javascript" language="javascript" >
 
$(document).ready(function(){
    // Date filter handler
    $('#search_data').on('click',function(){
        load_data();
    });
    load_data();
    function load_data()
    {
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
		var Isapprove = $("#Isapprove").val();
		var PassedFrom = $("#PassedFrom").val();
        var msg = "Sales Report "+from_date +" To " + to_date +" To "+ Isapprove;
        $(".report_for").text(msg);
        $.ajax({
            url:"<?php echo admin_url(); ?>accounting/load_data_for_VoucherEntry",
            dataType:"JSON",
            method:"POST",
            data:{from_date:from_date, to_date:to_date, Isapprove:Isapprove,PassedFrom:PassedFrom},
            beforeSend: function () {
                $('#searchh2').css('display','block');
                $('.table_payment_report tbody').css('display','none');
            },
            complete: function () {
                $('.table_payment_report tbody').css('display','');
                $('#searchh2').css('display','none');
            },
            success:function(data){
                var html = '';
                
                // First pass to group by voucher
                var groupedData = {};
                for(var count = 0; count < data.length; count++) {
                    if(!groupedData[data[count].UniquID]) {
                        groupedData[data[count].UniquID] = [];
                    }
                    groupedData[data[count].UniquID].push(data[count]);
                }
                
                // Second pass to build HTML
                for(var UniquID in groupedData) {

                    var voucherData = groupedData[UniquID];
                    var firstRow = voucherData[0];
                    var rowCount = voucherData.length;
                    var isApproved = firstRow.Status === 'Y';
                    
                    // Process account names and IDs
                    for(var i = 0; i < voucherData.length; i++) {
                        var row = voucherData[i];
                        if(row.AccountID == null) {
                            row.new_AccountID = row.staff_AccountID;
                        } else {
                            row.new_AccountID = row.AccountID;
                        }
                        
                        if(row.AccountName == null) {
                            row.AccountName = (row.firstname || '') + ' ' + (row.lastname || '');
                        }
                    }
                    
                    // Format date
                    var date = firstRow.Transdate.substring(0, 10);
                    var date_new = date.split("-").reverse().join("/");
                    
                    // Start row
                    html += '<tr>';
                    if(isApproved) {
                        html += '<td rowspan="'+rowCount+'"></td>'; 
                    }else{
                        // Checkbox (merged)
                        html += '<td rowspan="'+rowCount+'"><input type="checkbox" class="row_checkbox" value="'+UniquID+'"></td>'; 
                    }
                    
                    
                    // VoucherNo (merged)
                    html += '<td rowspan="'+rowCount+'" style="text-align:center;">'+voucherData[0].VoucherID+'</td>';
                    
                    // VoucherDate (merged)
                    html += '<td rowspan="'+rowCount+'" style="text-align:center;">'+date_new+'</td>';
                    
                    // First account row
                    html += '<td>'+voucherData[0].new_AccountID+'</td>';
                    html += '<td style="text-align:left;">'+voucherData[0].AccountName.trim()+'</td>';
                    html += '<td style="text-align:center;">'+voucherData[0].TType+'</td>';
                    html += '<td style="text-align:right;">'+voucherData[0].Amount+'</td>';
                    html += '<td>'+voucherData[0].Narration+'</td>';
                    
                    // Status (merged)
                    html += '<td rowspan="'+rowCount+'">'+(isApproved ? 'Y' : 'N')+'</td>';
                    
                    // Action (merged)
                    html += '<td rowspan="'+rowCount+'" style="text-align:center;">';
                    if(isApproved) {
                        html += '-';
                    } else {
                        var url = '<?php echo admin_url("accounting/ApproveVoucherEntry/") ?>' +UniquID+"/"+voucherData[0].PassedFrom+"/List";
                        html += '<a href="'+url+'" class="btn btn-warning btn-sm approve-voucher-btn" title="Approve">Approve</a>';
                    }
                    html += '</td>';
                    
                    html += '</tr>';
                    
                    // Additional account rows for this voucher
                    for(var i = 1; i < voucherData.length; i++) {
                        html += '<tr>';
                        html += '<td>'+voucherData[i].new_AccountID+'</td>';
                        html += '<td style="text-align:left;">'+voucherData[i].AccountName.trim()+'</td>';
                        html += '<td style="text-align:center;">'+voucherData[i].TType+'</td>';
                        html += '<td style="text-align:right;">'+voucherData[i].Amount+'</td>';
                        html += '<td>'+voucherData[i].Narration+'</td>';
                        html += '</tr>';
                    }
                }
                
                $('.table_payment_report tbody').html(html);
            }
        });
    }

    // Select all checkbox functionality
    $(document).on('change', '#select_all', function() {
        $('.row_checkbox').prop('checked', $(this).is(':checked'));
    });

    // Individual checkbox functionality
    $(document).on('change', '.row_checkbox', function() {
        const total = $('.row_checkbox').length;
        const checked = $('.row_checkbox:checked').length;
        $('#select_all').prop('checked', total === checked);
    });

	// Approve Selected button
	$('#approve_selected').on('click', function() {
	    var PassedFrom = $("#PassedFrom").val();
		var selected = [];
		$('.row_checkbox:checked').each(function() {
			selected.push($(this).val());
		});
		if (selected.length === 0) {
			alert('Please select at least one row to approve.');
			return;
		}

		// AJAX call to approve selected vouchers
		$.ajax({
			url: "<?php echo admin_url(); ?>accounting/approve_multiple_payments",
			method: "POST",
			data: { voucher_ids: selected,PassedFrom:PassedFrom},
			success: function(response) {
				alert('Selected vouchers approved!');
				load_data();  
			},
			error: function() {
				alert('An error occurred while approving vouchers.');
			}
		});
	}); 
});


    
</script>

<script>
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_payment_report");
		tr = table.getElementsByTagName("tr");
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[3];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else {
					tr[i].style.display = "none";
				}
			}       
		}
	}
</script>
<script>
	$('.add-new-transfer').on('click', function(){
		$('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
		$('#transfer-modal').modal('show');
		init_receipt_entry_table();
	}); 	
</script>

<script>
	$(document).ready(function(){
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
		
		$('#payment_date').datetimepicker({
			format: 'd/m/Y',
			minDate: minStartDate,
			maxDate: maxEndDate_new,
			timepicker: false
		});
	});
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
		
	});
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#table_payment_report tbody");
		var rows = table.find("tr").toArray();
		var index = $(this).index();
		var ascending = !$(this).hasClass("asc");
		
		
		// Remove existing sort classes and reset arrows
		$(".sortablePop").removeClass("asc desc");
		$(".sortablePop span").remove();
		
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
</script>
</body>
</html>
<?php require 'modules/accounting/assets/js/payment/payment_entry_js.php';?>