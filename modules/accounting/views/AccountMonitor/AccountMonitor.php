<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>
<style>
    .table-account_monitor { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
    .table-account_monitor thead th { position: sticky; top: 0; z-index: 1; }
    .table-account_monitor tbody th { position: sticky; left: 0; }

    #table_AccountSubgroup td:hover {
        cursor: pointer;
    }
    #table_AccountSubgroup tr:hover {
        background-color: #ccc;
    }

    .table-AccountSubgroup          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-AccountSubgroup thead th { position: sticky; top: 0; z-index: 1; }
    .table-AccountSubgroup tbody th { position: sticky; left: 0; }

    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
        color: #fff !important; }
</style>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-10">
            <div class="panel_s">
               <div class="panel-body">
			   
			   <nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Account Monitor</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="subgroup_code">Sub Act Group ID</label>
                                <input type="text" name="subgroup_code" id="subgroup_code" class="form-control" value="" onkeypress="return isNumber(event)">
                                            
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                            <label for="subgroup_name">Sub Act Group Name</label>
                            <input type="text" name="subgroup_name" id="subgroup_name" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                $from_date = '01/03/20'.$fy_new;
                            }else{
                                $from_date = "01/".date('m')."/".date('Y');
                                $to_date = date('d/m/Y');
                            }
                        ?>     
                          <?php echo render_date_input('from_date','from_date',$from_date); ?>
                        </div>
                        <div class="col-md-2">
                          <?php echo render_date_input('to_date','to_date',$to_date); ?>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-default buttons-excel buttons-html5" style="margin-top: 19px;"  tabindex="0" aria-controls="table-account_monitor" href="#" id="caexcel"><span>Export to excel</span></a>
                        </div>
                    </div>
    
                    <div class="clearfix mtop20"></div>
                    <div class="row">
                        <div class="col-md-6">
                         <span id="searchh2" style="display:none;">Please wait fetching data...</span>
                         <span id="searchh3" style="display:none;">Please wait exporting data...</span>
                        </div>
                        <div class="col-md-6">
                            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search .." title="Type in a name" style="float: right;">
                        </div>
                    </div>
                    <div class="clearfix mtop20"></div>
        <!-- ===============  Account Group Model ============================= -->
        
                    <div class="modal fade AccountSubgroup" id="AccountSubgroup" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="padding:5px 10px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Account SubGroup</h4>
                                </div>
                                <div class="modal-body" style="padding:0px 5px !important">
                            
                                <div class="table-AccountSubgroup tableFixHead2">
                                    <table class="tree table table-striped table-bordered table-AccountSubgroup tableFixHead2" id="table_AccountSubgroup" width="100%">
                                        <thead>
                                            <tr style="display:none;">
                                                <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
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
                                            <tr class="get_AccountID" data-id="<?php echo $value["SubActGroupID"]; ?>">
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
                                        <?php } ?>
                                        </tbody>
                                    </table>   
                                </div>
                                </div>
                                <div class="modal-footer" style="padding:0px;">
                                    <input type="text" id="myInput1"  autofocus="1" name='myInput1' onkeyup="myFunction2()" placeholder="Search for names.."  style="float: left;width: 100%;">
                                </div>
                            </div>
                        <!-- /.modal-content -->
                        </div>
                    <!-- /.modal-dialog -->
                    </div>
                <!-- /.modal -->
            <!-- ===============  Account Group Model End ============================= -->
            
                   <div class="table-account_monitor tableFixHead">
                    </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
    function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
</script>
<script>

    $("#subgroup_code").dblclick(function(){
        $('#AccountSubgroup').modal('show');
        $('#AccountSubgroup').on('shown.bs.modal', function () {
            $('#myInput1').val('');
            $('#myInput1').focus();
        })
    });
    
    // Focus in Subgroup ID 
    $('#subgroup_code').on('focus',function(){
        $('#subgroup_code').val('');
        $('#subgroup_name').val('');
    });
    // Blur Subgroup ID
    $('#subgroup_code').on('blur',function(){
        var account_subgroupID = $(this).val();
        if(account_subgroupID == ""){
            
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details",
                dataType:"JSON",
                method:"POST",
                cache: false,
                data:{account_subgroupID:account_subgroupID},
                success:function(data){
                    if(empty(data)){
                        $('#subgroup_name').val('');
                    }else{
                        $('#subgroup_code').val(data.SubActGroupID);
                        $('#subgroup_name').val(data.SubActGroupName);
                    }
                }
            });
        }
    });
    
    $('.get_AccountID').on('click',function(){ 
        account_subgroupID = $(this).attr("data-id");
        $.ajax({
            url:"<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details",
            dataType:"JSON",
            method:"POST",
            cache: false,
            data:{account_subgroupID:account_subgroupID},
            success:function(data){
                if(empty(data)){
                    $('#subgroup_name').val('');
                }else{
                    $('#subgroup_code').val(data.SubActGroupID);
                    $('#subgroup_name').val(data.SubActGroupName);
                }
            }
        });
        $('#AccountSubgroup').modal('hide');
    });
    
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var SubgroupID = $("#subgroup_code").val();
	    /*if(SubgroupID == ""){
	        alert('please select Account Subgroup');
	    }else{*/
	        load_data(from_date,to_date,SubgroupID);
	    //}
    });
    
    function load_data(from_date,to_date,SubgroupID)
    {
        $.ajax({
            url:"<?php echo admin_url(); ?>accounting/GetAccountMonitor",
            dataType:"html",
            method:"POST",
            data:{from_date:from_date, to_date:to_date,SubgroupID},
            beforeSend: function () {
                $('#searchh2').css('display','block');
                $('.table-account_monitor').css('display','none');
            },
            complete: function () {
                $('.table-account_monitor').css('display','');
                $('#searchh2').css('display','none');
            },
            success:function(data){
                $('.table-account_monitor').html(data);
            }
        });
    }
  
</script>

 <script>
    function myFunction2() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInput1");
          filter = input.value.toUpperCase();
          table = document.getElementById("table_AccountSubgroup");
          tr = table.getElementsByTagName("tr");
           for (i = 1; i < tr.length; i++) {
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
              }else{
                   tr[i].style.display = "none";
              } 
            }
            }   
          }
        }
    }
    
    function myFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInput");
          filter = input.value.toUpperCase();
          table = document.getElementById("table-account_monitor");
          tr = table.getElementsByTagName("tr");
          for (i = 1; i < tr.length; i++) {
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
                 txtValue = td2.textContent || td1.innerText;
              if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
              }else{
                   tr[i].style.display = "none";
              } 
            }}
            }   
          }
    }
 </script>
 <script>
 
    $("#caexcel").click(function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var SubgroupID = $("#subgroup_code").val();
	    /*if(SubgroupID == ""){
	        alert('please select Account Subgroup');
	    }else{*/
	        $.ajax({
                url:"<?php echo admin_url(); ?>accounting/ExportAccountMonitor",
                method:"POST",
                data:{from_date:from_date, to_date:to_date,SubgroupID},
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
	    //}
	    
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
    
	 $(document).on("click", ".sortable", function () {
		var table = $("#table-account_monitor_filter");
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
</body>
</html>
