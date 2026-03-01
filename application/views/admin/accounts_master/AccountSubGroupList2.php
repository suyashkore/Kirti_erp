<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .tableFixHead2          { overflow: auto;max-height: 45vh;width:100%;position:relative;top: 0px; }
	.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
	.tableFixHead2 tbody th { position: sticky; left: 0; }
	
	
	table  { border-collapse: collapse; width: 100%; }
	th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
	th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-8">
				<div class="panel_s">
					<div class="panel-body">
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Misc. Reports</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Account SubGroup 2 List </b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
						<div class="row">
							<!--<div class="col-md-12">
								<h4 class="no-margin font-bold">Account SubGroup 2 List</h4>
								<hr>
							</div>-->
							<div class="col-md-3 leads-filter-column">
								
								<?php echo render_select('main_group',$AccountMainGroup,array('ActGroupID','ActGroupName'),'Select Main Group'); ?>
							</div>
							<div class="col-md-3 leads-filter-column">
								
								<?php echo render_select('sub_group1','','','Select  Sub Group 1'); ?>
							</div>
							<button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
							<div class="clearfix"></div>
							<div class="col-md-6">
								<div class="custom_button">&nbsp;&nbsp;
									<a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
									<a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
									<!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
								</div>
							</div>
							<div class="col-md-6">
								<input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
							</div>
							<div class="col-md-12">
								<div class="tableFixHead2">
									<table class="table table-striped table-bordered tableFixHead2" width="100%" id="user_list">
										<thead>
											<tr style="display:none;">
												<th style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></th>
											</tr>
											<tr style="display:none;">
												<th style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></th>
											</tr>
											<tr style="display:none;">
												<th colspan="3" style="text-align:center;">Sub Account Group</th>
											</tr>
											<tr>
												<th class="sortable">Account SubGroupID 2</th>
												<th class="sortable">Account SubGroupName 2</th>
												<th class="sortable">Account SubGroupName 1</th>
												<th class="sortable">MainGroup Name</th>
											</tr>
										</thead>
										<tbody id="table_body">
											<?php
												foreach ($AccountSubGroupID2 as $key => $value) {
												?>
												<tr>
													<td><?php echo $value["SubActGroupID"];?></td>
													<td><?php echo $value["SubActGroupName"];?></td>
													<td><?php echo $value["SubGroup1"];?></td>
													<td><?php echo $value["MainGroup"];?></td>
													
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
	</div>
</div>


<?php init_tail(); ?>
<!--new update -->

<script>
	
    $('#search_data').on('click',function(){
	    var main_group = $("#main_group").val();
	    var sub_group1 = $("#sub_group1").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>accounts_master/load_subgroup2_data",
            dataType:"json",
            method:"POST",
            data:{ main_group:main_group,sub_group1:sub_group1},
            beforeSend: function () {
                //$("tbody").children().remove()
                $('.tableFixHead2 table tbody').children().remove();
                $('.tableFixHead2 tbody').css('display','none');
			},
            complete: function () {
                $('.tableFixHead2 tbody').css('display','');
			},
            success:function(data){
                $('.tableFixHead2 tbody').html(data);
			}
		});
	});
    
    $('#main_group').on('change',function(){
	    var main_group = $(this).val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>accounts_master/GetSubGroupList1ByMainGroup",
            dataType:"json",
            method:"POST",
            data:{ main_group:main_group},
            success:function(data){
                var optionsHTML = '<option value="">Non selected</option>';
				$.each(data, function(index, option) {
					optionsHTML += '<option value="' + option.SubActGroupID1 + '">' + option.SubActGroupName + '</option>';
				});
				$('select[name=sub_group1]').html(optionsHTML);
				$('.selectpicker').selectpicker('refresh');
			}
		});
	});
    
    $("#caexcel").click(function(){
        var main_group = $("#main_group").val();
	    var sub_group1 = $("#sub_group1").val();
        $.ajax({
            url:"<?php echo admin_url(); ?>accounts_master/ExportAccount_sub_groupList2",
            method:"POST",
            data:{main_group:main_group,sub_group1:sub_group1},
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
			}
		});
	});
	
	
</script>
<script type="text/javascript">
	function myFunction2() {
		var input, filter, table, tr, td, i, txtValue;
		input = document.getElementById("myInput1");
		filter = input.value.toUpperCase();
		table = document.getElementById("user_list");
		tr = table.getElementsByTagName("tr");
		for (i = 0; i < tr.length; i++) {
			td = tr[i].getElementsByTagName("td")[0];
			td1 = tr[i].getElementsByTagName("td")[1];
			td2 = tr[i].getElementsByTagName("td")[2];
			td3 = tr[i].getElementsByTagName("td")[3];
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
							} else if(td3){
							txtValue = td3.textContent || td3.innerText;
							if (txtValue.toUpperCase().indexOf(filter) > -1) {
								tr[i].style.display = "";
								}else {
								tr[i].style.display = "none";
							}
							}else  {
							tr[i].style.display = "none";
						}
					} }    
			}}
	}
	function printPage(){
        
		var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
		var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
		heading_data += '<tr>';
		heading_data += '<td style="text-align:center;"colspan="3">Account SubGroup List 2</td>';
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
    
	$(document).on("click", ".sortable", function () {
		var table = $("#user_list tbody");
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
</script>
<style type="text/css">
	body{
    overflow: hidden;
	}
</style>
