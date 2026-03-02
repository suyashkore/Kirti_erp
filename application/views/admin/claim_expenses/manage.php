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
         <div class="col-md-12">
            
            <div class="panel_s">
               <div class="panel-body">
                  
                    <nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>HR</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Claim Expenses</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
      <!--</div>-->
         <div class="row">
                       <!--<div class="col-md-2">-->
                            <?php //$cur_date = date('d/m/Y'); 
                          //  $first_date = "01/".date('m')."/".date('Y');
                            ?>
                          <?php //echo render_date_input('from_date','from_date',$first_date); ?>
                        <!--</div>-->
                        <!--<div class="col-md-2">-->
                          <?php // echo render_date_input('to_date','to_date',$cur_date); ?>
                        <!--</div>-->
                        <div class="col-md-2">
                             <?php $month = date('m');?>
                             <div class="form-group">
                                    <label class="control-label" for="month_data">Month</label>
                                    <select name="month_data" class="selectpicker form-control" id="month_data" data-none-selected-text="<?php echo _l('Select Month'); ?>" data-live-search="true">
                                       <option></option>
                                        <option value="04" <?php if($month == '04'){ echo 'Selected';}   ?>>Apr - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="05" <?php if($month == '05'){ echo 'Selected';}   ?>>May - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="06" <?php if($month == '06'){ echo 'Selected';}   ?>>Jun  - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="07" <?php if($month == '07'){ echo 'Selected';}   ?>>Jul - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="08" <?php if($month == '08'){ echo 'Selected';}   ?> >Aug - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="09" <?php if($month == '09'){ echo 'Selected';}   ?> >Sep - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="10" <?php if($month == '10'){ echo 'Selected';}   ?> >Oct - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="11" <?php if($month == '11'){ echo 'Selected';}   ?> >Nov - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                        <option value="12" <?php if($month == '12'){ echo 'Selected';}   ?> >Dec - <?php  echo $this->session->userdata('finacial_year'); ?></option>
                                         <option value="01" <?php if($month == '01'){ echo 'Selected';}   ?> >Jan - <?php  echo $this->session->userdata('finacial_year')+1; ?></option>
                                        <option value="02" <?php if($month == '02'){ echo 'Selected';}   ?> >Feb - <?php  echo $this->session->userdata('finacial_year')+1; ?></option>
                                        <option value="03" <?php if($month == '03'){ echo 'Selected';}   ?> >Mar - <?php  echo $this->session->userdata('finacial_year')+1; ?></option>
                                    </select>
                                </div> 
                        </div>
                        <div class="col-md-2">
                            <lebal class="control-label">Staff</lebal>
                           <select name="staff_id" id="staff_id" class="selectpicker form-control" data-none-selected-text="Non Selected" data-live-search="true">
                                <option></option>
                            <?php foreach($staff_details as $value){?>
                              <option value="<?= $value['AccountID']; ?>" ><?= $value['firstname'].' '.$value['lastname']; ?></option>  
                           <?php  } ?>
                              
                              
                          </select>
                        </div>
                          <div class="col-md-2">
                            <lebal class="control-label">Deparment</lebal>
								<select name="hr_profile_deparment" class="selectpicker" id="hr_profile_deparment" data-width="100%"  data-live-search="true" data-none-selected-text="<?php echo _l('departments'); ?>"> 
									<option value=""></option>
									<?php 
									foreach ($departments as $value) { ?>
										<option data-id="<?php echo html_entity_decode($value['name']) ?>" value="<?php echo html_entity_decode($value['departmentid']); ?>"><?php echo html_entity_decode($value['name']) ?></option>
									<?php }
									?>              
								</select>
							</div>
							<!--<div class="col-md-2">
							    <lebal class="control-label">Company</lebal>
								<select name="hr_company" class="selectpicker" id="company" data-width="100%"  data-live-search="true" data-none-selected-text="<?php echo _l('company'); ?>"> 
									<option value=""></option>
									<option value="1" data-id="Crazy Snacks Pvt. Ltd.">Crazy Snacks Pvt. Ltd.</option>
									<option value="2" data-id="CrazyFun Foods (P) Ltd.">CrazyFun Foods (P) Ltd.</option>
									<option value="3" data-id="CRAZY BAKERY UDYOG">CRAZY BAKERY UDYOG</option>
								              
								</select>
							 </div>-->
                        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
     
            &nbsp;<a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 19px;"  tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Excel</span></a>
            <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 19px;"  onclick="printPage();">Print</a>
           
                  </div>
                  <div class="clearfix mtop20"></div>
                  <span id="searchh3" style="display:none;"><b>Please wait Exporting data...</b></span>
                   <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search.." title="Type in a name" style="float: right;">
            <form action="<?= base_url();?>admin/claim_expenses/update_claim_fields" method="post">
            <div class="table-daily_report tableFixHead2">
             
              
            </div>
            <div style="padding:40px 10px 0px; float: right;">
                <?php
                if (has_permission_new('cliam_expenses', '', 'edit')) {
            ?>
                <button type="submit" class="btn btn-primary">Submit</button>
                <?php
                    }
                ?>
            </div>
             
            </form>
             <span id="searchh2" style="display:none;">Loading.....</span>
             
                </div>
    </div>
    <?php
    /*$table_data = [];

    $table_data = array_merge($table_data, array(
        'HSN Code',
        "Description",
      "Date",
      
      ));

    render_datatable($table_data,'hsn-table');*/
    ?>
  </div>
</div>
</div>
</div>

<?php init_tail(); ?>
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
   /* console.log(minStartDate);
    console.log(maxEndDate_new);*/
    
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
    
    
    });
    
</script>
<script>
function load_data(from_date,to_date,staff_id,deparment)
  {
       var month_data = $("#month_data").val();
    $.ajax({
      url:"<?php echo admin_url(); ?>Claim_expenses/load_data",
      dataType:"json",
      method:"POST",
      data:{month_data:month_data, staff_id:staff_id, deparment:deparment},
      beforeSend: function () {
           $('.tableFixHead2 table').remove();
               $('#searchh22').css('display','none');
        $('#searchh2').css('display','block');
        $('.table-daily_report tbody').css('display','none');
        
     },
      complete: function () {
         
                            
        $('.table-daily_report tbody').css('display','');
        $('#searchh2').css('display','none');
         
     },
      success:function(data){
        
          $('.tableFixHead2').html(data.html);
          $("#sl").click(function(){
                    // alert('test')
                  if ( $('.up').css('display') == 'none')
                {
                     $(".up_starting").hide()
                  $(".up").show()
                  $(".down").hide()
                }else{
                     $(".up_starting").hide()
                    $(".up").hide()
                  $(".down").show()
                }
                    f_sl *= -1;
                    var n = $(this).prevAll().length;
                    sortTable(f_sl,n);
                });
                
            
        // $('tbody').html(data);
      }
    });
  }
 $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var staff_id = $("#staff_id").val();
	   /* var company = $("#company").val();*/
	   var deparment = $("#hr_profile_deparment").val();
	   
	   
        load_data(from_date,to_date,staff_id,deparment);
     
        
 });


</script>
 <script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
 <script>
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
      td5 = tr[i].getElementsByTagName("td")[5];
      td6 = tr[i].getElementsByTagName("td")[6];
      td7 = tr[i].getElementsByTagName("td")[7];
      td8 = tr[i].getElementsByTagName("td")[8];
      td10= tr[i].getElementsByTagName("td")[10];
      td11 = tr[i].getElementsByTagName("td")[11];
      td12 = tr[i].getElementsByTagName("td")[12];
      td13 = tr[i].getElementsByTagName("td")[13];
      td14 = tr[i].getElementsByTagName("td")[14];
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
        
      }else if(td10){
         txtValue = td10.textContent || td10.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td11){
         txtValue = td11.textContent || td11.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td12){
         txtValue = td12.textContent || td12.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td13){
         txtValue = td13.textContent || td13.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        
      }else if(td14){
         txtValue = td14.textContent || td14.innerText;
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
}
}
}
}
}
}
}
}
}
}
}
 </script>
<script>
$("#caexcel").click(function(){
//   var from_date = $("#from_date").val();
// 	    var to_date = $("#to_date").val();
	    var staff_id = $("#staff_id").val();
	    var deparment = $("#hr_profile_deparment").val();
	    var deparment_name = $('#hr_profile_deparment').find(':selected').attr('data-id');
	    var company_name = $('#company').find(':selected').attr('data-id')
	     var company = $("#company").val();
	      var month_data = $("#month_data").val();
	    $.ajax({
            url:"<?php echo admin_url(); ?>claim_expenses/export_claim_expenses",
            method:"POST",
             data:{month_data:month_data, staff_id:staff_id, deparment:deparment,deparment_name:deparment_name,company:company,company_name:company_name},
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
         var tables = document.getElementsByTagName('table');

    if (tables.length === 0) {
        alert("No table found to print");
        return;
    }
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Claim Expenses </td>';
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
    
      function sortTable(f,n){
	var rows = $('#table-daily_report tbody  tr').get();

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
		$('#table-daily_report').children('tbody').append(row);
	});
    }
    var f_sl = 1;
    var f_nm = 1;
    
    // function dercment_increment(){
    
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
</body>
</html>
