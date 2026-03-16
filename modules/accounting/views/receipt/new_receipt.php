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
                    					<li class="breadcrumb-item active" aria-current="page"><b>Receipt Entry</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
          <?php $arrAtt = array();
                $arrAtt['data-type']='currency';
                ?>
          <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'receipt-entry-form','autocomplete'=>'off')); ?>
          <!--<h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <hr />-->
          <div class="row">
              <?php
            $data_attr = array();
            if(isset($receipts_entry)){
                $data_attr = array(
                    "disabled" =>true
                    );
            }
            $data_attr2 = array(
                    "disabled" =>true
                    );
            ?>
            <div class="col-md-3">
                <?php
                    $fy = $this->session->userdata('finacial_year');
                    $fy_new  = $fy + 1;
                    $lastdate_date = '20'.$fy_new.'-03-31';
                    $curr_date = date('Y-m-d');
                    $curr_date_new    = new DateTime($curr_date);
                    $last_date_yr = new DateTime($lastdate_date);
                    if($last_date_yr < $curr_date_new){
                        $date = $lastdate_date;
                    }else{
                        $date = date('Y-m-d');
                    }
                ?>
                
              <?php $value = (isset($receipts_entry) ? _d(substr($receipts_entry->Transdate,0,10)) : _d($date)); ?>
              <?php echo render_date_input('receipt_date','Voucher Date',$value,$data_attr); ?>
              <?php
                if(isset($receipts_entry)){
                    ?>
                    <input type="hidden" name="receipt_date1" value="<?php echo _d(substr($receipts_entry->Transdate,0,10)); ?>">
                    <input type="hidden" name="VoucheriD" value="<?php echo $receipts_entry->VoucherID; ?>">
                <?php
                }
              ?>
            </div>
            <div class="col-md-2">
                <?php
                $selected_company = $this->session->userdata('root_company');
                if($selected_company == 1){
                
                $new_tax_transactionNumber = get_option('next_receipts_number_for_cspl');
            }elseif($selected_company == 2){
                $new_tax_transactionNumber = get_option('next_receipts_number_for_cff');
            }elseif($selected_company == 3){
                $new_tax_transactionNumber = get_option('next_receipts_number_for_cbu');
            }elseif($selected_company == 4){
                $new_tax_transactionNumber = get_option('next_receipts_number_for_cbupl');
            }
            ?>
              <?php $value = (isset($receipts_entry) ? $receipts_entry->VoucherID : $new_tax_transactionNumber); ?>
              <?php echo render_input('receipt_number','Voucher No.',$value,'text',$data_attr2); ?>
            </div>
            <div class="col-md-3">
                 <?php $value = (isset($receipts_entry) ? $receipts_entry->d_act : 'CASH'); ?>
                 
            <?php echo render_select('ganeral_account',$genral_account_to_select,array('AccountID','company'),'Rcpt Mode',$value); ?>
            </div>
            <div class="col-md-1" style="margin-top: 20px;">
                <?php
                    if(isset($receipts_entry)){
                ?>
                <?php
                if (has_permission_new('accounting_receipt_entry', '', 'edit')) {
                    $selected_company = $this->session->userdata('root_company');
                    $fy = $this->session->userdata('finacial_year');
                    $fy_new  = $fy + 1;
                    $first_date = '20'.$fy.'-04-01';
                    $lastdate_date = '20'.$fy_new.'-03-31';
                    $lgstaff = $this->session->userdata('staff_user_id');
                    $receipts_date = substr($receipts_entry->Transdate,0,10);
                    
                    $curr_date = date('Y-m-d');
                    $first_date_yr = new DateTime($first_date);
                    $last_date_yr = new DateTime($lastdate_date);
                    $curr_date_new = new DateTime($curr_date);
                    
                    if($curr_date_new > $last_date_yr){
                    $lastdate = $lastdate_date;
                    }else{
                        $lastdate = date('Y-m-d');
                    }
                    /*$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "RECEIPTS" AND FY LIKE "'.$fy.'" ORDER BY abs(tblaccountledger.VoucherID) DESC ';
                    $result_data = $this->db->query($sql)->row();
                    $lastdate = substr($result_data->Transdate,0,10);*/
                
                    $this->db->select('*');
                    $this->db->where('plant_id', $selected_company);
                    $this->db->where('year', $fy);
                    $this->db->where('staff_id', $lgstaff);
                    $this->db->LIKE('feature', "accounting_receipt_entry");
                    $this->db->LIKE('capability', "view");
                    $this->db->from(db_prefix() . 'staff_permissions');
                    $result2 = $this->db->get()->row();
                    $day = $result2->days;
                
                if($day == 0){
                            $return = '';
                        }else{
                            
                            $days = '- '.$day.' days';
                            $tillDate = date('Y-m-d', strtotime($lastdate. $days));
                            $tillDate_new = new DateTime($tillDate);
                            $receipts_date_new    = new DateTime($receipts_date);
                            if ($receipts_date_new < $tillDate_new) {
                                $return = 'disabled';
                            }else{
                                $return = '';
                            }
                        }
            
                ?>
                <?php if($return == "disabled"){
            ?>
            <a href="#" class="btn btn-info <?php echo $return;?>">Update</a>
            <?php
            }else{
            ?>
            <button type="button" class="btn btn-info receipt-entry-form-submiter">Update</button>
            <?php
            }?>
                <?php
                }
                ?>
                <?php 
                
                    }else{
                ?>
                <?php
                if (has_permission_new('accounting_receipt_entry', '', 'create')) {
                ?>
                <button type="button" class="btn btn-info receipt-entry-form-submiter" ><?php echo _l('submit'); ?></button>
                <?php
                }
                ?>
                <?php
                    }
                ?>
            </div>
            <?php
                    if(isset($receipts_entry)){
                ?>
            <div class="col-md-1" style="margin-top: 20px;">
                
              <?php
                if (has_permission_new('accounting_receipt_entry', '', 'delete')) {
                ?>
              <a href="<?php echo admin_url('accounting/delete_receipt_entry/' . $receipts_entry->VoucherID) ?>" class="btn btn-danger <?php echo $return;?>">Delete</a>
            <?php
                }
                ?>    
          </div>
          <?php
                    }
                ?>
            <div class="col-md-1" style="margin-top: 20px;">
            <?php
                if (has_permission_new('accounting_receipt_entry', '', 'view')) {
                ?>  
              <a href="#" class="btn btn-warning add-new-transfer mbot15">show list</a>
            <?php
                }
            ?>
          </div>
          </div>
          <div id="receipt_entry_container"></div>
          <div class="col-md-12">
         <table class="table text-right">
            <tbody>
                
               <tr>
                   <td><span class="bold" style="width: 15%;">Account Opening Balance :</span>
                  </td>
                  <td class="acc_opt_bal" style="width: 8%;">
                    <?php $value = (isset($receipts_entry) ? $receipts_entry->amount : 0); ?>
                    <?php echo app_format_money($value, $currency->name); ?>
                  </td>
                  
                  <td><span class="bold" style="width: 10%;">Total Payment Amount :</span>
                  </td>
                  <td class="total_debit" style="width: 10%;">
                    <?php $value = (isset($receipts_entry) ? $receipts_entry->damt : 0); ?>
                    <?php echo app_format_money($value, $currency->name); ?>
                  </td>
                  <td style="width: 51%;"></td>
                  
               </tr>
            </tbody>
         </table>
        </div>
          <?php echo form_hidden('receipts_entry'); ?>
          <?php echo form_hidden('amount'); ?>
         
          <?php echo form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="transfer-modal" data-keyboard="false" data-backdrop="static">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Receipts Voucher List</h4>
         </div>
         
         
         <div class="modal-body">
             
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
                <?php //$current_date = date('d/m/Y'); 
                //$from_date = '01/'.date('m').'/'.date('Y');
                ?>
                <div class="col-md-3">
                    <?php
                   echo render_date_input('from_date','From',$from_date);
                   ?>
                </div>
                <div class="col-md-3">
                    <?php
                   echo render_date_input('to_date','To',$to_date);
                   ?>
                </div>
                <div class="col-md-3">
                    <br>
                    <button class="btn btn-info pull-left mleft5 search_data" id="search_data"><?php echo _l('rate_filter'); ?></button>
                </div>
                <div class="col-md-3">
                    <br>
                    <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
                </div>
                <div class="col-md-12">
                 
            <div class="table_receipts_report">
             
              <table class="tree table table-striped table-bordered table_receipts_report" id="table_receipts_report" width="100%">
                  
                <thead>
                  <tr>
                    <th class="sortablePop" style="text-align:left;">VoucherNo</th>
                    <th class="sortablePop" style="text-align:left;">VoucherDate</th>
                    <th class="sortablePop" style="text-align:left;">AccountID</th>
                    <th class="sortablePop" style="text-align:left;">Account Name</th>
                    <th class="sortablePop" style="text-align:left;">Dr/Cr</th>
                    <th class="sortablePop" style="text-align:left;">Amount</th>
                    <th class="sortablePop" style="text-align:left;">Description</th>
                    
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
                    
                </div>
              </div>
         </div>
        
      </div>
   </div>
</div>
<style>
    .table_receipts_report { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.table_receipts_report thead th { position: sticky; top: 0; z-index: 1; }
.table_receipts_report tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
.table_receipts_report table  { border-collapse: collapse; width: 100%; }
.table_receipts_report th, td { padding: 3px 3px !important; white-space: nowrap; font-size:11px; line-height:1.42857143;vertical-align: middle;}
.table_receipts_report th     { background: #50607b;color: #fff !important; }


#table_receipts_report tr:hover {
    background-color: #ccc;
}

#table_receipts_report td:hover {
    cursor: pointer;
}
</style>
<?php init_tail(); ?>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
 
  function load_data(from_date,to_date)
  {
    $.ajax({
      url:"<?php echo admin_url(); ?>accounting/load_data_for_receipts",
      dataType:"JSON",
      method:"POST",
      data:{from_date:from_date, to_date:to_date},
      beforeSend: function () {
               
        $('#searchh2').css('display','block');
        $('.table_receipts_report tbody').css('display','none');
        
     },
      complete: function () {
                            
        $('.table_receipts_report tbody').css('display','');
        $('#searchh2').css('display','none');
     },
      success:function(data){
        var html = '';
      
        for(var count = 0; count < data.length; count++)
        {
           if(data[count].AccountID == null){
              var new_AccountID = data[count].staff_AccountID;
          }else{
              var new_AccountID = data[count].AccountID;
          }
          var url = "'<?php echo admin_url() ?>accounting/new_receipt_entry/"+data[count].VoucherID+"'";
        html += '<tr onclick="location.href='+url+'">';
        html += '<td style="text-align:center;">'+data[count].VoucherID+'</td>';
          
        var date = data[count].Transdate.substring(0, 10)
        var date_new = date.split("-").reverse().join("/");
          
          html += '<td  style="text-align:center;">'+date_new+'</td>';
          
          html += '<td >'+new_AccountID+'</td>';
          if(data[count].AccountName == null){
              var AccoutName = data[count].firstname + data[count].lastname;
          }else{
              var AccoutName = data[count].AccountName;
          }
          html += '<td  style="text-align:left;">'+ AccoutName +'</td>';
          html += '<td  style="text-align:center;">'+data[count].TType+'</td>';
          html += '<td style="text-align:right;">'+data[count].Amount+'</td>';
          html += '<td >'+data[count].Narration+'</td>';
         
          html += '</tr>';
        }
         $('.table_receipts_report tbody').html(html);
      
      }
    });
  }
  
 $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "Sales Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
        load_data(from_date,to_date);
        
 });

});
</script>

<script>
    function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_receipts_report");
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
   
    $('#receipt_date').datetimepicker({
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
		var table = $("#table_receipts_report tbody");
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
<?php require 'modules/accounting/assets/js/receipt/receipt_entry_js.php';?>