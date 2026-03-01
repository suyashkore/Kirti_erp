<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>HR</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Attendance Sheet</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
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
									$from_date = "01/".date('m')."/".date('Y');
									$to_date = date('d/m/Y');
								}
							?>
							
							<div class="row">
								<div class="col-md-2 leads-filter-column">
									<?php echo render_input('month','month',date('Y-m'), 'month'); ?>
								</div>
								
								<div class="col-md-5">
									<br/>
									<div class="custom_button">
										<!--<button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>-->
										<a class="btn btn-info search_data" href="#" id="search_data">Show</a>
										<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export</span></a>
										<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
									</div>
								</div>
								
							</div>
							<div class="row">
								
								<div class="col-md-8">
									<button style="color:#00c !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="HO: is a public holiday." class="btn">HO</button>
									<button style="color:#00c !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="EB: is a Event break" class="btn">EB</button>
									<button style="color:#00c !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="UB: is a Unexpected break" class="btn">UB</button>
									<button style="color:#00c !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="SL: is sick leave" class="btn">SL</button>
									
									<button style="color:#603 !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="WO: is Weekly Off." class="btn">WO</button>
									
									<button style="color:#c00 !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="A: is Absent Staff" class="btn">A</button>
									<button style="color:#f60 !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="NR: is Not register staff for this date" class="btn">NR</button>
									<button style="color:green !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="P: is Present Staff" class="btn">P</button>
									<button style="color:#fc0 !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="HD: is Half Day" class="btn">HD</button>
									<button style="color:gray !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="HD: is No Shift" class="btn">NS</button>
									<button style="color:#0c0 !important" type="button" data-toggle="tooltip" data-placement="top" data-original-title="AL: is Annual Leave" class="btn">AL</button>
									<div class="clearfix"></div>
								</div>
								<div class="col-md-4">
									<input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." title="Type in a name" style="float: right;">
								</div>
							</div>
							
							
							
						</div>
						
						
						<div class="clearfix"></div>
						<span id="searchh" style="display:none;">please wait fetching data...</span>
						<span id="searchh2" style="display:none;">please wait Exporting data...</span>
						<div class="fixTableHead SaleVsSaleRtn_report">
							
						</div>
						
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<style>
    .SaleVsSaleRtn_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
    .SaleVsSaleRtn_report thead th { position: sticky; top: 0; z-index: 1; }
    .SaleVsSaleRtn_report tbody th { position: sticky; left: 0; }
    
    /* Just common table stuff. Really. */
    .SaleVsSaleRtn_report table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    .SaleVsSaleRtn_report th     { background: #50607b;color: #fff !important; }
	
    .table_accountlist { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
    .table_accountlist thead th { position: sticky; top: 0; z-index: 1; }
    .table_accountlist tbody th { position: sticky; left: 0; }
    
    /* Just common table stuff. Really. */
    .table_accountlist table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    .table_accountlist th     { background: #50607b;color: #fff !important; }
    
    #table_accountlist tr:hover {
    background-color: #ccc;
    }
    
    #table_accountlist td:hover {
	cursor: pointer;
    }
</style>

<script type="text/javascript" language="javascript" >
	function myFunction() 
    {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("SaleVsSaleRtn_report");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) 
        {
            tr[i].style.display = "none"; 
            td = tr[i].getElementsByTagName("td"); 
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;                
                    if (txtValue.toUpperCase().indexOf(filter.toUpperCase()) > -1) {
                        tr[i].style.display = "";  
                        break; 
                    }
                }
            }
        }
    }
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("table_accountlist");
		tr = table.getElementsByTagName("tr");
		for (i = 1; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			td3 = tr[i].getElementsByTagName("td")[3];
			td4 = tr[i].getElementsByTagName("td")[4];
			td5 = tr[i].getElementsByTagName("td")[5];
			td6 = tr[i].getElementsByTagName("td")[6];
			if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
					} else if(td1){
					txtValue = td1.textContent || td1.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						tr[i].style.display = "";
						} else if(td2){
						txtValue = td2.textContent || td2.innerText;
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							tr[i].style.display = "";
							}else if(td3){
							txtValue = td3.textContent || td3.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
								}else if(td4){
								txtValue = td4.textContent || td4.innerText;
								if (txtValue.toUpperCase().indexOf(filter) > -1) {
									tr[i].style.display = "";
									
									}else if(td5){
									txtValue = td5.textContent || td5.innerText;
									if (txtValue.toUpperCase().indexOf(filter) > -1) {
										tr[i].style.display = "";
										
										}else if(td6){
										txtValue = td6.textContent || td6.innerText;
										if (txtValue.toUpperCase().indexOf(filter) > -1) {
											tr[i].style.display = "";
											
											}else{
											tr[i].style.display = "none";
										} 
									}
								}}
						}
					}     
				}
			}
		}
	}
</script>
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		
		$('#search_data').on('click',function(){
			var month = $("#month").val();
			
			$.ajax({
				url:"<?php echo admin_url(); ?>hr_profile/GetAttendanceSheet",
				dataType:"JSON",
				method:"POST",
				cache: false,
				data:{month:month},
				beforeSend: function () {
					$('#searchh').css('display','block');
					$('.SaleVsSaleRtn_report').css('display','none');
				},
				complete: function () {
					$('.SaleVsSaleRtn_report').css('display','');
					$('#searchh').css('display','none');
				},
				success:function(data){
					$('.SaleVsSaleRtn_report').html(data);
				}
			});
			
		});
		
	});
	$("#caexcel").click(function(){
        var month = $("#month").val();
	    
	    $.ajax({
			url:"<?php echo admin_url(); ?>hr_profile/ExportAttendanceSheet",
			method:"POST",
			data:{month:month},
			beforeSend: function () {
				$('#searchh2').css('display','block');
			},
			complete: function () {
				$('#searchh2').css('display','none');
			},
			success:function(data){
				response = JSON.parse(data);
				window.location.href = response.site_url+response.filename;
			}
		});
	});
</script>
<script type="text/javascript">
	function printPage(){
		
		var month = $("#month").val();
		
        
		var colspan = '11';
		var AccountDetails = 'Attendance Sheet';
		
        var colspan = '10';
		var filterdate = 'Report : '+month;
		var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">';
		heading_data += '<tbody><tr><td style="text-align:center;" colspan="'+colspan+'"><?php echo $company_detail->company_name; ?></td></tr>';
		heading_data += '<tr><td style="text-align:center;" colspan="'+colspan+'"><?php echo $company_detail->address; ?></td></tr>';    
		heading_data += '<tr><td style="text-align:center;"colspan="'+colspan+'">'+filterdate+'</td></tr>';
		
		heading_data += '<tr><td style="text-align:center;"colspan="'+colspan+'">'+AccountDetails+'</td></tr>';
		
		heading_data += '</tbody></table>';
		
		var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .show_in_print{ display:block; }</style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
		
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
			var maxEndDate_new = maxEndDate;
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
			timepicker: false
		});
		$(document).on("click", ".sortable", function () {
		var table = $("#SaleVsSaleRtn_report tbody");
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