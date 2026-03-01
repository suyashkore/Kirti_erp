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
           
            <div class="row">
                <div class="col-md-12">
                    <h4 class="no-margin font-bold">Account SubGroup List</h4>
                    <hr>
                </div>
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
                    <th id="sl">SubAccountGroupID <span class="up_starting">  &#8595;</span><span class="up" style="display:none;"> &#8593;</span><span class="down" style="display:none;"> &#8595;</span></th>
                    <th>SubAccountGroupName</th>
                    <th>MainGroup</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($account_subgroup_table as $key => $value) {
                ?>
                    <tr>
                        <td><?php echo $value["SubActGroupID"];?></td>
                        <td><?php echo $value["SubActGroupName"];?></td>
                        <?php
                            $mainGroupName = '';
                            foreach ($account_maingroup as $key1 => $value1) {
                                if($value["ActGroupID"] == $value1["ActGroupID"]){
                                    $mainGroupName = $value1["ActGroupName"];
                                }
                            }
                        ?>
                        <td><?php echo $mainGroupName;?></td>
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
$("#caexcel").click(function(){
  var from_date = '2022-13-11';
    $.ajax({
        url:"<?php echo admin_url(); ?>accounts_master/Account_sub_group",
        method:"POST",
        data:{from_date:from_date},
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
      }else {
        tr[i].style.display = "none";
      }
    }else  {
        tr[i].style.display = "none";
      }
    }     
  }}
}
 function printPage(){
        
         var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Sub Account Group</td>';
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
    
      function sortTable(f,n){
	var rows = $('#user_list tbody  tr').get();

	rows.sort(function(a, b) {

		var A = getVal(a);
		var B = getVal(b);

		if(A < B) {
			return -1*f;
		}
		if(A > B) {
			return 1*f;
		}
		return 0;
	});

	function getVal(elm){
		var v = $(elm).children('td').eq(n).text().toUpperCase();
		if($.isNumeric(v)){
			v = parseInt(v,10);
		}
		return v;
	}

	$.each(rows, function(index, row) {
		$('#user_list').children('tbody').append(row);
	});
    }
    var f_sl = 1;
    var f_nm = 1;
    $("#sl").click(function(){
             if ( $('.up').css('display') == 'none')
    {
      $(".up").show()
      $(".down").hide()
       $(".up_starting").hide()
    }else{
        $(".up").hide()
      $(".down").show()
       $(".up_starting").hide()
    }
        f_sl *= -1;
        var n = $(this).prevAll().length;
        sortTable(f_sl,n);
    });
    $("#nm").click(function(){
        f_nm *= -1;
        var n = $(this).prevAll().length;
        sortTable(f_nm,n);
    });
 </script>
 <style type="text/css">
   body{
    overflow: hidden;
   }
 </style>
