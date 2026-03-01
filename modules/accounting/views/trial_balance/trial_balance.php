<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .table-trial_bal_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-trial_bal_report thead th { position: sticky; top: 0; z-index: 1; }
.table-trial_bal_report tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-11">
            
            <div class="panel_s">
               <div class="panel-body">
                  
                    <div class="row">
                       <div class="col-md-3">
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
                          <?php echo render_date_input('as_on','As On',$to_date); ?>
                        </div>
                        
                        
                            <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
                        </div>
      <!--</div>-->
                   
                  
                  <div class="clearfix mtop20"></div>
        <div class="row">
            <div class="col-md-6">
                <a class="btn btn-default buttons-excel buttons-html5"   tabindex="0" aria-controls="table-trial_bal_report" href="#" id="caexcel"><span>Export to excel</span></a>
            <a class="btn btn-default" href="javascript:void(0);"    onclick="printPage();">Print</a>
            
            </div>
             <span id="searchh3" style="display:none;">Please wait exporting data...</span>
            <div class="col-md-6">
                <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search .." title="Type in a name" style="float: right;">
            
            </div>
            <div class="col-md-12">
            <div class="table-trial_bal_report tableFixHead2">
                <table class="tree table table-striped table-bordered table-trial_bal_report tableFixHead2" id="table-trial_bal_report" width="100%">
                    <thead>
                        <tr style="display:none;">
                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span style="font-size:10px;font-weight:600;">CD Report</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                        </tr>
                        <tr>
                            <th id="sl" style="text-align:left;">Sr. <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
                            <th class="sortable" style="text-align:left;">AccountSubGroup</th>
                            <th class="sortable" style="text-align:left;">AccountID</th>
                            <th class="sortable" style="text-align:left;">AccountName</th>
                            <th class="sortable" style="text-align:left;">Address</th>
                            <th class="sortable" style="text-align:left;">OpeningAmt</th>
                            <th class="sortable" style="text-align:left;">DebitAmt</th>
                            <th class="sortable" style="text-align:left;">CreditAmt</th>
                            <th class="sortable" style="text-align:left;">BalanceAmt</th><!--
                            <th style="text-align:center;">Cr/Dr</th>-->
                            
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
</div>
<?php init_tail(); ?>
<script>

    $(document).ready(function(){
 
  function load_data(as_on)
  {
    $.ajax({
      url:"<?php echo admin_url(); ?>accounting/load_data_trial_balance_report",
      dataType:"html",
      method:"POST",
      data:{as_on:as_on},
      beforeSend: function () {
               
        $('#searchh2').css('display','block');
        $('.table-trial_bal_report tbody').css('display','none');
        
     },
      complete: function () {
                            
        $('.table-trial_bal_report tbody').css('display','');
        $('#searchh2').css('display','none');
     },
      success:function(data){
        //  data1 = JSON.parse(data);
           
        //   console.log(data1.html);return false; 
           $('#table-trial_bal_report tbody').html(data);
        // $('tbody').html(data);
      }
    });
  }
 $('#search_data').on('click',function(){
        var as_on = $("#as_on").val();
	    
	    var msg = "Filter - Date : "+as_on;
	    
	    $(".report_for").text(msg);
        load_data(as_on);
    
 });

  
  
});
</script>

 <script>
    function myFunction2() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("myInput1");
      filter = input.value.toUpperCase();
      table = document.getElementById("table-trial_bal_report");
      tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
      td3 = tr[i].getElementsByTagName("td")[3];
      td4 = tr[i].getElementsByTagName("td")[4];
      td5 = tr[i].getElementsByTagName("td")[5];
      td6 = tr[i].getElementsByTagName("td")[6];
      td7 = tr[i].getElementsByTagName("td")[7];
      td8 = tr[i].getElementsByTagName("td")[8];
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
        
      }else if(td7){
         txtValue = td7.textContent || td7.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td8){
         txtValue = td8.textContent || td8.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else{
           tr[i].style.display = "none";
    }
    }
    }
    }     
  }}}
}
}
}
}
    }
</script>
 <script>
 
$("#caexcel").click(function(){
    var as_on = $("#as_on").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>accounting/export_trial_balance_report",
            method:"POST",
            data:{as_on:as_on},
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
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Trial Balance Report</td>';
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
    };
 </script>
 <script>
    $(document).on("click", ".sortable", function () {
		var table = $("#table-trial_bal_report tbody");
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
   
    $('#as_on').datetimepicker({
        format: 'd/m/Y',
        minDate: minStartDate,
        maxDate: maxEndDate_new,
        timepicker: false,
        showOtherMonths: false,
        pickTime: false,
            orientation: "left",
    });
    
    });
</script>
</body>
</html>
