<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }
.table-daily_report tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-9">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Misc. Reports</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Accounts List</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
                
            <div class="row">
              <!--<div class="col-md-12">
                
              <h4 class="no-margin font-bold">Accounts List</h4>
             <hr>
              </div>-->
               <div class="clearfix"></div> 
            <div class="col-md-3">
              <?php echo render_select('MainGroup',$MainGroup,array('ActGroupID','ActGroupName'),'Main Group'); ?>
            </div>
           
            <div class="col-md-3">
              <?php echo render_select('SubGroup1','','','SubActGroup 1'); ?>
            </div>
            <div class="col-md-3">
              <?php echo render_select('SubGroup2','','','SubActGroup 2'); ?>
            </div>
            <div class="col-md-3">
								<div class="form-group" app-field-wrapper="status">
									<label for="status" class="control-label">Is Active</label>
									<select name="status" id="status" class="form-control" >
										<option value="">All</option>
										<option value="Y">Yes</option>
										<option value="N">No</option>
									</select>
								</div>
							</div>
            
             
            <div class="clearfix"></div> 
            <div class="col-md-10">
                <div class="custom_button">	
					<button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
                    &nbsp;<a class="btn btn-default buttons-excel buttons-html5"    tabindex="0"  href="#" id="caexcel"><span>Export to excel</span></a>
                    <a class="btn btn-default" href="javascript:void(0);"    onclick="printPage();">Print</a>
                </div>
            </div>
            <div class="col-md-2">
                 <input type="text" id="myInput1" class="form-control" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
            
            </div>
             
          </div>
         <div class="table-daily_report tableFixHead2">
             
              <table class="tree table table-striped table-bordered table-daily_report  tableFixHead2" id="table-daily_report" width="100%">
                  
                <thead>
                 
                    <tr style="display:none;">
                      <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>
                  <tr>
                    <th class="sortable" style="text-align:left;">AccountID </th>
                    <th class="sortable" style="text-align:left;">Account Name</th>
                    <th class="sortable" style="text-align:left;">Sub Group 2 </th>
                    <th class="sortable" style="text-align:left;">Sub Group 1 </th>
                    <th class="sortable" style="text-align:left;">Main Group</th>
                    <th class="sortable" style="text-align:left;">Is Active</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
            <span id="searchh3" style="display:none;">Please wait data exporting.....</span>
          </div>
        </div>
        
      </div>
      
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>

    $('#MainGroup').on('change',function(){
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
					$('select[name=SubGroup1]').html(optionsHTML);
					$('.selectpicker').selectpicker('refresh');
					var optionsHTML2 = '<option value="">Non selected</option>';
					$('select[name=SubGroup2]').html(optionsHTML2);
					$('.selectpicker').selectpicker('refresh');
            }
        });
    });
    
    $('#SubGroup1').on('change',function(){
	    var SubGroup1 = $(this).val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>accounts_master/GetSubGroupList2BySubGroup1",
            dataType:"json",
            method:"POST",
            data:{ SubGroup1:SubGroup1},
            success:function(data){
                var optionsHTML = '<option value="">Non selected</option>';
					$.each(data, function(index, option) {
						optionsHTML += '<option value="' + option.SubActGroupID2 + '">' + option.SubActGroupName + '</option>';
					});
					$('select[name=SubGroup2]').html(optionsHTML);
					$('.selectpicker').selectpicker('refresh');
            }
        });
    });
   function myFunction2() {
      
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table-daily_report");
  tr = table.getElementsByTagName("tr");
for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
      td3 = tr[i].getElementsByTagName("td")[3];
      td4 = tr[i].getElementsByTagName("td")[4];
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
        
      }else {
        tr[i].style.display = "none";
      }
    }     
  }
}
}
}
}
}

 function load_data(MainGroup,SubGroup1,SubGroup2,status)
  {
    var TType = "2";
    $.ajax({
      url:"<?php echo admin_url(); ?>Accounts_master/loadAccountHead_data",
      dataType:"json",
      method:"POST",
      data:{MainGroup:MainGroup, SubGroup1:SubGroup1,SubGroup2:SubGroup2,status:status,TType:TType},
      beforeSend: function () {
        $('#searchh2').css('display','block');
        $('.table-daily_report tbody').css('display','none');
        
     },
      complete: function () {
        $('.table-daily_report tbody').css('display','');
        $('#searchh2').css('display','none');
     },
      success:function(data){
        var msg = "Accounts List Filter MainGroupID: "+data.MainGroup +", SubGroup 1 Name: " +data.SubGroup1+", SubGroup 2 Name: "+data.SubGroup2;
	    $(".report_for").text(msg);
        $('#table-daily_report tbody').html(data.html);
      }
    });
  }
    $('#search_data').on('click',function(){
        var MainGroup = $("#MainGroup").val();
	    var SubGroup1 = $("#SubGroup1").val();
	    var SubGroup2 = $("#SubGroup2").val();
	    var status = $("#status").val();
        load_data(MainGroup,SubGroup1,SubGroup2,status);
    });

</script>
 
<script>
 
$("#caexcel").click(function(){
    var MainGroup = $("#MainGroup").val();
	var SubGroup1 = $("#SubGroup1").val();
	var SubGroup2 = $("#SubGroup2").val();
	var status = $("#ft_active").val();
    $.ajax({
        url:"<?php echo admin_url(); ?>Accounts_master/export_Account_Head",
        method:"POST",
        data:{MainGroup:MainGroup, SubGroup1:SubGroup1,SubGroup2:SubGroup2,status:status},
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
<script type="text/javascript">
 function printPage(){
    var html_filter_name =    $('.report_for').html();
         var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Create Ladger</td>';
         heading_data += '</tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">'+html_filter_name+'</td>';
         heading_data += '</tr>';
         
         heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
   newWin= window.open("");
   newWin.document.write(print_data);
   newWin.print();
   newWin.close();
    }
	
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
      
 </script>
</body>
</html>