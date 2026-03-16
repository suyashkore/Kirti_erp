<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
				<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
				<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
				<li class="breadcrumb-item active" aria-current="page"><b>Accounts Ledger</b></li>
			</ol>
        </nav>
        <hr class="hr_style">
          <!--<h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
         <a href="<?php echo admin_url('accounting/report'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <hr />-->
          <div class="row">
                <div class="col-md-2">
                  <?php echo render_date_input('from_date','from_date', _d($from_date)); ?>
                </div>
                <div class="col-md-2">
                  <?php echo render_date_input('to_date','to_date', _d($to_date)); ?>
                </div>
                
                <div class="col-md-3">
                  <div class="form-group" app-field-wrapper="accounting_method">
                      <label for="accounting_method" class="control-label">Select Account</label>
                      <select name="accounting_method" id="accounting_method" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true">
                        <option></option>
                        <?php 
                        foreach ($accounts_list as $key => $value) {
                             # code...
                             ?>
                                <option value="<?php echo $value["AccountID"]; ?>" ><?php echo $value["company"]."(". $value["AccountID"].") - ".$value["StationName"]; ?></option>
                             <?php
                           }
                        foreach ($accounts_list_staff as $key1 => $value1) {
                             # code...
                             ?>
                                <option value="<?php echo $value1["AccountID"]; ?>" ><?php echo $value1["firstname"]."  ".$value1["lastname"]; ?></option>
                             <?php
                           }
                        ?>
                    </select>
                  </div>
                    
                </div>
                
                <div class="col-md-2">
                    <?php echo render_input('AccountID','AccountID',$AccountID_for_ledger); ?>
                </div>
                
                <div class="col-md-2">
                  <div class="form-group" app-field-wrapper="PassedFrom">
                      <label for="PassedFrom" class="control-label">Voucher Type</label>
                      <select name="PassedFrom" id="PassedFrom" class="selectpicker" data-width="100%" data-none-selected-text="Non selected" data-live-search="true">
                        <option value="">All</option>
                        <option value="RECEIPTS">RECEIPTS</option>
                        <option value="CONTRA">CONTRA</option>
                        <option value="PAYMENTS">PAYMENTS</option>
                        <option value="JOURNAL">JOURNAL</option>
                        <option value="SALE">SALE</option>
                        <option value="SALESRTN">SALESRTN</option>
                        <option value="PURCHASE">PURCHASE</option>
                        <option value="PURCHASERTN">PURCHASERTN</option>
                    </select>
                  </div>
                    
                </div>
                
                <div class="clearfix"></div>
                <div class="col-md-2" style="margin-bottom:5px;">
                  <?php echo form_hidden('type', 'general_ledger2'); ?>
                  <?php if(has_permission_new('accounting_ledger_entry', '', 'view')){?>
                    <button type="submit" class="btn btn-info btn-submit " id="show_ledger">Show</button>
                  <?php }?>
                </div>
                
                
                <div class="col-md-6 ResultBtn" style="margin-bottom:5px;">
                    <div class="btn-group">
                        <?php if(has_permission_new('accounting_ledger_entry', '', 'export')){?>
                            <a class="btn btn-default buttons-excel buttons-html5 " tabindex="0" aria-controls="ledger_report" href="#" id="caexcel"><span>Export to excel</span></a>
                        <?php }?>
                        <?php if(has_permission_new('accounting_ledger_entry', '', 'print')){?>
                            <a class="btn btn-default mleft10" href="javascript:void(0);" onclick="printPage();">Print</a>
                        <?php }?>
                        <?php if(has_permission_new('accounting_ledger_share', '', 'view')){?>
                            <a class="btn btn-success mleft10 ShareLedger" id="ShareLedger" href="javascript:void(0);">Share On WhatsApp</a>
                        <?php }?>
                    </div>
                </div>
                </br>
            
            
          </div>
          
          <div class="page" id="DivIdToPrint" style="display: inherit;">
            <div class="row"> 
            
            <div class="col-md-12"> 
            <span id="searchh2" style="display:none;">Please wait while loading data..</span>
            <span id="searchh3" style="display:none;">Please wait while exporting data..</span>
            <span id="searchh4" style="display:none;">Please wait while sharing data..</span>
            <input type="hidden" name="acct_name" id="acct_name" value="">
            <div class="tableFixHead">
              <table class="tree table table-striped table-bordered ledger_report tableFixHead" id="ledger_report">
                  <thead>
                    <tr class="only_print" style="display:none;" align="right">
                        <th colspan="7" style="text-align:center;" align="right"><b><?php echo $selected_company_details->FIRMNAME; ?></b></th>
                    </tr>
                    <tr class="only_print" style="display:none;" align="right">
                        <th colspan="7" style="text-align:center;" align="right"><b><?php echo $selected_company_details->ADDRESS1.", ".$selected_company_details->ADDRESS2; ?></b></th>
                    </tr>
                    <tr style="display:none;">
                        <th colspan="7"><b>From Date : <span id="from_date_div"></span>, To Date : <span id="to_date_div"></span>, Account Name : <span id="acct_name_div"></span></b></th>
                    </tr>
                    <tr class="tr_header">
                      <th>Date</th>
                      <th>Particular</th>
                      <th>Voucher Type</th>
                      <th>Voucher ID</th>
                      <th>Buyer Party</th>
                      <th>Buyer Address</th>
                      <th>Narration</th>
                      <th>Debit</th>
                      <th class="total_amount">Credit</th>
                      <th class="total_amount">Balance</th>
                    </tr>
                  </thead>
                  <tbody>
                     
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
<!-- box loading -->
<!--<div id="box-loading"></div>-->
<?php init_tail(); ?>
</body>
<style>
    .tableFixHead          { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; }
.tableFixHead tbody th { position: sticky; left: 0; }

/* Just common table stuff. Really. */
table  { border-collapse: collapse; width: 100%; }
th, td { padding: 5px 5px !important; white-space: nowrap; border:1px solid #474343 !important;}
th     { background: #50607b;color: #fff !important; }

#ledger_report tr:hover {
    background-color: #ccc;
}

#ledger_report td:hover {
    cursor: pointer;
}
</style>
<style>
@media screen {
        .only_print{
            display:none !important;
        }
    }
@media print {
  .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
      border:1px solid #474343 !important;
  }
}
</style>
<script>
    function Show_ladger(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var PassedFrom = $("#PassedFrom").val();
	    $("#from_date_div").html(from_date);
	    $("#to_date_div").html(to_date);
	    var AccountID = $("#AccountID").val();
	    var AccountIDDropDown = $("#accounting_method").val();
	    if(AccountID == "" && AccountIDDropDown == ""){
	        alert("Please select Account");
	    }else{
	        if($("#AccountID").val() == ''){
    	        var accounting_method = $("#accounting_method").val();
    	    }else{
    	        var accounting_method = $("#AccountID").val();
    	    }
        	$.ajax({
              url:"<?php echo admin_url(); ?>accounting/view_report2_new",
              dataType:"JSON",
              method:"POST",
              data:{from_date:from_date, to_date:to_date, accounting_method:accounting_method,PassedFrom:PassedFrom},
              beforeSend: function () {
                $('#searchh2').css('display','block');
                $('#ledger_report tbody').css('display','none');
             },
              complete: function () {
                $('#ledger_report tbody').css('display','');
                $('#searchh2').css('display','none');
             },
              success:function(data){
                $('#ledger_report tbody').html(data.table);
                $("#acct_name_div").html(data.account_name);
                $("#acct_name").val(data.account_name);
                $(".ResultBtn").css("display","block");
              }
            });
	    }
    }
    
    $('#show_ledger').on('click',function(){
        Show_ladger();
    })
        
</script>
<script>
//======================= Export Ledger ========================================
$("#caexcel").click(function(){
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    var PassedFrom = $("#PassedFrom").val();
    $("#from_date_div").html(from_date);
    $("#to_date_div").html(to_date);
    var AccountID = $("#AccountID").val();
    var AccountIDDropDown = $("#accounting_method").val();
    if(AccountID == "" && AccountIDDropDown == ""){
        alert("Please select Account");
    }else{
        if($("#AccountID").val() == ''){
            var accounting_method = $("#accounting_method").val();
        }else{
            var accounting_method = $("#AccountID").val();
        }
        $.ajax({
            url:"<?php echo admin_url(); ?>accounting/export_general_ledger_new",
            method:"POST",
           data:{from_date:from_date, to_date:to_date, accounting_method:accounting_method,PassedFrom:PassedFrom},
            beforeSend: function () {
                $('#searchh3').css('display','block');
            },
            complete: function () {
                $('#searchh3').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                if(response == "denied"){
                    alert('Your Access denied for this account');
                }else{
                    window.location.href = response.site_url+response.filename;
                }
            }
        });
    }
    
});

//======================= Export Ledger ========================================
$("#ShareLedger").click(function(){
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    var PassedFrom = $("#PassedFrom").val();
    $("#from_date_div").html(from_date);
    $("#to_date_div").html(to_date);
    var AccountID = $("#AccountID").val();
    var AccountIDDropDown = $("#accounting_method").val();
    if(AccountID == "" && AccountIDDropDown == ""){
        alert("Please select Account");
    }else{
        if($("#AccountID").val() == ''){
            var accounting_method = $("#accounting_method").val();
        }else{
            var accounting_method = $("#AccountID").val();
        }
        $.ajax({
            url:"<?php echo admin_url(); ?>accounting/AccountLedgerShareOnWhatsApp",
            method:"POST",
           data:{from_date:from_date, to_date:to_date, accounting_method:accounting_method,PassedFrom:PassedFrom},
            beforeSend: function () {
                $('#searchh4').css('display','block');
            },
            complete: function () {
                $('#searchh4').css('display','none');
            },
            success:function(data){
                //let res = data.status;
                response = JSON.parse(data);
                //alert(response.status);
                alert(response.data);
                /*if(response.status == "success"){
                    alert(response.data);
                    alert("Account Statement Sent Successfully..");
                }else{
                    alert(response.data);
                    //alert("Account Statement Not Sent, Please try again..");
                }*/
                /*response = JSON.parse(data);
                if(response == "denied"){
                    alert('Your Access denied for this account');
                }else{
                    window.location.href = response.site_url+response.filename;
                }*/
            }
        });
    }
});

</script>
<script type="text/javascript">
 
    function printPage()
{
    var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var account_name = $("#acct_name").val();
	    var PassedFrom = $("#PassedFrom").val();
		
		if(PassedFrom == ''){
			PassedFrom = 'All';
		}
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="7"><?php echo $selected_company_details->FIRMNAME; ?></td></tr><tr><td style="text-align:center;" colspan="7"><?php echo $selected_company_details->ADDRESS1.", ".$selected_company_details->ADDRESS2; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:left;"colspan="3">Date : '+from_date+' To '+to_date+'</td>';
         heading_data += '<td style="text-align:left;" colspan="3"> Account Name : '+account_name+'</td>';
         heading_data += '<td style="text-align:left;" colspan="3"> Voucher Type : '+PassedFrom+'</td>';
         heading_data += '</tr>';
         heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
   newWin= window.open("");
   newWin.document.write(print_data);
   newWin.print();
   newWin.close();
}

 </script>
  <script>
    $(document).ready(function(){
        $(".ResultBtn").css("display","none");
        var AccountIDSession = "<?php echo $this->session->userdata('AccountID_for_ledger') ?>";
        if(AccountIDSession !== ""){
            $('#accounting_method').selectpicker('val', AccountIDSession);
            $('.selectpicker').selectpicker('refresh');
            Show_ladger();
            
        }
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
$('#accounting_method').on('change', function(){
    var AccountID = $(this).val();
    $('#AccountID').val(AccountID);
    $('#ledger_report tbody').css('display','none');
	$(".ResultBtn").css("display","none");
    $('#PassedFrom').val('');
	$("#PassedFrom").selectpicker("refresh");
});
$('#from_date').on('change', function(){
    $('#AccountID').val('');
	$('#accounting_method').val('default').selectpicker('deselectAll');
	$("#accounting_method").selectpicker("refresh");
	$('#ledger_report tbody').css('display','none');
   $(".ResultBtn").css("display","none");
	$('#PassedFrom').val('');
	$("#PassedFrom").selectpicker("refresh");
});
$('#to_date').on('change', function(){
    $(".ResultBtn").css("display","none");
	$('#AccountID').val('');
	$('#accounting_method').val('default').selectpicker('deselectAll');
	$("#accounting_method").selectpicker("refresh");
	$('#ledger_report tbody').css('display','none');
   
	$('#PassedFrom').val('');
	$("#PassedFrom").selectpicker("refresh");
});

// Initialize For Account
     $( "#AccountID" ).autocomplete({
        source: function( request, response ) {
          // Fetch data
          $.ajax({
            url: "<?=base_url()?>admin/accounting/get_account_list_by_accoutID",
            type: 'post',
            dataType: "json",
            data: {
              search: request.term
            },
            success: function( data ) {
              response( data );
            }
          });
        },
        select: function (event, ui) {
          $('#AccountID').val(ui.item.value); // display the selected text
        }
      });
    $('#AccountID').on('focus',function(){
        $('#AccountID').val('');
        $('#accounting_method').val('default').selectpicker('deselectAll');
        $("#accounting_method").selectpicker("refresh");
        $('#ledger_report tbody').css('display','none');
	    $(".ResultBtn").css("display","none");
		$('#PassedFrom').val('');
		$("#PassedFrom").selectpicker("refresh");
    });
    $('#AccountID').on('blur', function(){
        var AccountID = $(this).val();
        if(AccountID == ""){
           
        }else{
            jQuery.ajax({
                    type: 'POST',
                    url:"<?=base_url()?>admin/accounting/get_account_details_by_AccountID",
                    dataType:"JSON",
                    data: {AccountID: AccountID},
                    success: function(data3) {
                        if (data3 == "false") {
                            alert('UserId Not found');
                            $('#AccountID').val('');
                            $('#accounting_method').val('default').selectpicker('deselectAll');
                            $("#accounting_method").selectpicker("refresh");
                            $('#ledger_report tbody').css('display','none');
                        }else{
                            //$("#accounting_method").children().remove();
                            $('#accounting_method').val(data3.AccountID).selectpicker();
                            $("#accounting_method").selectpicker("refresh");
                            $('#ledger_report tbody').css('display','none');
                        }
                        
                    }
            });
        }        
    });
</script>
<style>
    #AccountID {
    text-transform: uppercase;
}
</style>
</html>
