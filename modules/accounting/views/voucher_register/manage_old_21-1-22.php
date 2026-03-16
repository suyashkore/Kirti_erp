<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content"> 
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold">Voucher Register</h4>
          <hr />
          <div class="row">
            <div class="col-md-3">
               
            <?php echo render_select('voucher_type',$type_of_voucher,array('PassedFrom','PassedFrom'),'Select Book'); 
           // echo render_select( 'states',$states,array( 'short_name','state_name'), 'client_state','',array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                    $selected_company = $this->session->userdata('root_company');
                         ?>
                   <input type="hidden" name="comid" id"comid" value="<?php echo $selected_company;?>">      
            </div>  
            <div class="col-md-2">
                <?php $cur_date = _d(date('Y-m-d')); ?>
              <?php echo render_date_input('from_date','from_date',$cur_date); ?>
            </div>
            <div class="col-md-2">
              <?php echo render_date_input('to_date','to_date',$cur_date); ?>
            </div>
            <div class="col-md-2">
                <br>
                <button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
            </div>
            
          </div>
          
          <div class="row cdnote">
            <div class="col-md-12">
                
              <?php
               
        
                $server_side = [ 'Voucher No.','Voucher Date','Account Code','Account Name','Dr/Cr','Debit Amount','Credit Amount','Narration','Address1',];
             
                $table_data = array_merge($server_side);
                render_datatable($table_data,'voucher_register_cdnote');
              ?>
            </div>
           </div>
           
           
           <div class="row contra">
            <div class="col-md-12">
              <?php
                $table_data = [];
                $table_data = array_merge($table_data, array(
                  'Voucher No.',
                  //'Passed From',
                  'Voucher Date',
                  'Account Code',
                  'Account Name',
                  'Dr/Cr',
                  'Debit Amount',
                  'Credit Amount',
                  'Narration',
                  'Address1',
                  ));

                 render_datatable($table_data,'voucher_register_contra');
              ?>
            </div>
           </div>
          
           <div class="row payments">
            <div class="col-md-12">
              <?php
                $table_data = [];
                $table_data = array_merge($table_data, array(
                  'Passed From',
                  'Voucher Date',
                  'Voucher No.',
                //   'Account Code',
                  'Account Name',
                 // 'Dr/Cr',
                  'Amount',
                  'Second Account Name',
                //   'Debit Amount',
                //   'Credit Amount',
                  
                   'Description',
                  'Address1',
                  ));

                 render_datatable($table_data,'voucher_register_payments');
              ?>
            </div>
           </div>
           
            <div class="row receipts">
            <div class="col-md-12">
              <?php
                $table_data = [];
                $table_data = array_merge($table_data, array(
                  'Passed From',
                  'Voucher Date',
                  'Voucher No.',
                //   'Account Code',
                  'Account Name',
                 // 'Dr/Cr',
                  'Amount',
                  'Second Account Name',
                //   'Debit Amount',
                //   'Credit Amount',
                   'Description',
                  'Address1',
                  ));

                 render_datatable($table_data,'voucher_register_receipts');
              ?>
            </div>
           </div>
           
           
           <div class="row sales">
            <div class="col-md-12">
              <?php
                $table_data = [];
                $table_data = array_merge($table_data, array(
                 // 'Passed From',
                  'Voucher No.',
                  'Voucher Date',
                  'Party Name', 
                   'Address',
                   'Sale Amount',
                   'Disc Amount',
                   'Tax Amount',
                   'Claim Amount',
                   'RoundOff',
                   'Bill Amount',
                  ));

                 render_datatable($table_data,'voucher_register_sales');
              ?>
            </div>
           </div>
           
           <div class="row purchase">
            <div class="col-md-12">
              <?php
                $table_data = [];
                $table_data = array_merge($table_data, array(
                 // 'Passed From',
                  'Voucher No.',
                  'Voucher Date',
                  'Invoice No.',
                  'Party Name', 
                   'PurchAmt',
                   'Discount',
                   'Excise',
                   'CST',
                   'TaxAmt',
                   'Claim',
                   'Freight',
                   'RoundOff',
                   'InvoiceAmt'
                  ));

                 render_datatable($table_data,'voucher_register_purchase');
              ?>
            </div>
           </div>
           
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<script>

  $(function(){

    $('#search_data').on('click',function(){
    
    var notSortableAndSearchableItemColumns = [];
    <?php if(has_permission('items','','delete')){ ?>
      notSortableAndSearchableItemColumns.push(0);
    <?php } ?>
    
	var CustomersServerParams = {};

	CustomersServerParams['voucher_type'] = '[name="voucher_type"]';
	CustomersServerParams['from_date'] = '[name="from_date"]';
	CustomersServerParams['to_date'] = '[name="to_date"]';
	CustomersServerParams['comid'] = '[name="comid"]';
	var voucher_type = $("#voucher_type").val();
	
	//var comid = $('#comid').val();
	var comid = 3;
	//alert(voucher_type);

  //  $("#voucher_type").change(function(){
    // var value2 = this.value;
	if(voucher_type=="CDNOTE"){
	     $(".cdnote").show();
	     $(".contra").hide();
	     $(".payments").hide(); 
	     $(".receipts").hide();
	     $(".sales").hide();
	     $(".purchase").hide();
	    var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "CDNOTE Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
	    //alert(msg);
	 
	 	$.ajax({
            url: "<?=admin_url()?>accounting/company_detail1",
            type: 'post',
            dataType: "json",
            data: {
              comid: comid,
            },
            success: function( data ) {
             // response( data );
            
            
             $('.table-voucher_register_cdnote')
             .append('<caption style="caption-side: top"> <table class="table table-striped table-bordered daily_report" id="daily_report" width="100%"><thead><tr style="display:none;"><th colspan="9" >'+data.company_name+'</th></tr><tr style="display:none;"><th> - '+msg+'</th></tr></thead></table></caption>');
            
            /*$('.table-voucher_register_cdnote')
             .append('<caption style="caption-side: top"> <table class="table table-striped table-bordered daily_report" id="daily_report" width="100%"><thead><tr style="display:none;"><th colspan="9" >'+data.company_name+'</th></tr><tr style="display:none;"><th>'+data.address+'</th></tr><tr style="display:none;"><th>'+msg+'</th></tr></thead></table></caption>');
            */
            }
          });
	 	
	 	
	if (voucher_type.trim()!=''){
	    if ($.fn.DataTable.isDataTable('.table-voucher_register_cdnote')) {
	 		$('.table-voucher_register_cdnote').DataTable().destroy();
	 	} 
	 initDataTable('.table-voucher_register_cdnote', admin_url+'accounting/voucher_cdnote_reg_table', [0], [0], CustomersServerParams,'');

    	}else{
              alert("Please select Voucher Type");
            }
	}
	
	if(voucher_type=="CONTRA" || voucher_type=="JOURNAL"){
	     $(".cdnote").hide();
	     $(".contra").show();
	     $(".payments").hide();
	     $(".receipts").hide();
	     $(".sales").hide();
	     $(".purchase").hide();
	     
	     var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    if(voucher_type=="CONTRA"){
	        var msg = "CONTRA Report "+from_date +" To " + to_date;
	    }else{
	        var msg = "JOURNAL Report "+from_date +" To " + to_date;
	    }
	    
	    $(".report_for").text(msg);
	    //alert(msg);
	 
	 	$.ajax({
            url: "<?=admin_url()?>accounting/company_detail1",
            type: 'post',
            dataType: "json",
            data: {
              comid: comid,
            },
            success: function( data ) {
             // response( data );
            
            
             $('.table-voucher_register_contra')
             .append('<caption style="caption-side: top"> <table class="table table-striped table-bordered daily_report" id="daily_report" width="100%"><thead><tr style="display:none;"><th colspan="9" >'+data.company_name+'</th></tr><tr style="display:none;"><th> - '+msg+'</th></tr></thead></table></caption>');
            
           
            }
          });
	 	
	 	
	if (voucher_type.trim()!=''){
	    if ($.fn.DataTable.isDataTable('.table-voucher_register_contra')) {
	 		$('.table-voucher_register_contra').DataTable().destroy();
	 	} 
	  initDataTable('.table-voucher_register_contra', admin_url+'accounting/voucher_contra_reg_table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1,'ASC'))); ?>);
       
    	}
            else{
              alert("Please select Voucher Type");
            }
	}
	
	if(voucher_type=="PAYMENTS"){
	     $(".cdnote").hide();
	     $(".contra").hide();
	     $(".payments").show();
	     $(".receipts").hide();
	     $(".sales").hide();
	     $(".purchase").hide();
	     
	     var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "PAYMENT Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
	    //alert(msg);
	 
	 	$.ajax({
            url: "<?=admin_url()?>accounting/company_detail1",
            type: 'post',
            dataType: "json",
            data: {
              comid: comid,
            },
            success: function( data ) {
             // response( data );
            
            
             $('.table-voucher_register_payments')
             .append('<caption style="caption-side: top"> <table class="table table-striped table-bordered daily_report" id="daily_report" width="100%"><thead><tr style="display:none;"><th colspan="9" >'+data.company_name+'</th></tr><tr style="display:none;"><th> - '+msg+'</th></tr></thead></table></caption>');
            
            
            }
          });
          
	if (voucher_type.trim()!=''){
	    if ($.fn.DataTable.isDataTable('.table-voucher_register_payments')) {
	 		$('.table-voucher_register_payments').DataTable().destroy();
	 	}
	 	
	 	
	  initDataTable('.table-voucher_register_payments', admin_url+'accounting/voucher_payments_reg_table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1,'ASC'))); ?>);
       
    	}
            else{
              alert("Please select Voucher Type");
            }
	}
	
	if(voucher_type=="PURCHASE"){
	     $(".cdnote").hide();
	     $(".contra").hide();
	     $(".payments").hide();
	     $(".receipts").hide();
	     $(".sales").hide();
	     $(".purchase").show();
	     var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "PURCHASE Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
	    //alert(msg);
	 
	 	$.ajax({
            url: "<?=admin_url()?>accounting/company_detail1",
            type: 'post',
            dataType: "json",
            data: {
              comid: comid,
            },
            success: function( data ) {
             // response( data );
            
            
             $('.table-voucher_register_purchase')
             .append('<caption style="caption-side: top"> <table class="table table-striped table-bordered daily_report" id="daily_report" width="100%"><thead><tr style="display:none;"><th colspan="9" >'+data.company_name+'</th></tr><tr style="display:none;"><th> - '+msg+'</th></tr></thead></table></caption>');
            
            
            }
          });
	if (voucher_type.trim()!=''){
	    if ($.fn.DataTable.isDataTable('.table-voucher_register_purchase')) {
	 		$('.table-voucher_register_purchase').DataTable().destroy();
	 	} 
	  initDataTable('.table-voucher_register_purchase', admin_url+'accounting/voucher_purchase_reg_table', [], [], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'ASC'))); ?>);
       
    	}
            else{
              alert("Please select Voucher Type");
            }
	}
	
	if(voucher_type=="RECEIPTS"){
	     $(".cdnote").hide();
	     $(".contra").hide();
	     $(".payments").hide();
	     $(".receipts").show();
	     $(".sales").hide();
	     $(".purchase").hide();
	     var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "RECEIPTS Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
	    //alert(msg);
	 
	 	$.ajax({
            url: "<?=admin_url()?>accounting/company_detail1",
            type: 'post',
            dataType: "json",
            data: {
              comid: comid,
            },
            success: function( data ) {
             // response( data );
            
            
             $('.table-voucher_register_receipts')
             .append('<caption style="caption-side: top"> <table class="table table-striped table-bordered daily_report" id="daily_report" width="100%"><thead><tr style="display:none;"><th colspan="9" >'+data.company_name+'</th></tr><tr style="display:none;"><th> - '+msg+'</th></tr></thead></table></caption>');
            
            
            }
          });
	if (voucher_type.trim()!=''){
	    if ($.fn.DataTable.isDataTable('.table-voucher_register_receipts')) {
	 		$('.table-voucher_register_receipts').DataTable().destroy();
	 	} 
	  initDataTable('.table-voucher_register_receipts', admin_url+'accounting/voucher_receipts_reg_table', [0], [0], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(1,'ASC'))); ?>);
       
    	}
            else{
              alert("Please select Voucher Type");
            }
	}
	
	if(voucher_type=="SALE"){
	     $(".cdnote").hide();
	     $(".contra").hide();
	     $(".payments").hide();
	     $(".receipts").hide();
	     $(".sales").show();
	     $(".purchase").hide();
	     var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var msg = "SALE Report "+from_date +" To " + to_date;
	    $(".report_for").text(msg);
	    //alert(msg);
	 
	 	$.ajax({
            url: "<?=admin_url()?>accounting/company_detail1",
            type: 'post',
            dataType: "json",
            data: {
              comid: comid,
            },
            success: function( data ) {
             // response( data );
            
            
             $('.table-voucher_register_sales')
             .append('<caption style="caption-side: top"> <table class="table table-striped table-bordered daily_report" id="daily_report" width="100%"><thead><tr style="display:none;"><th colspan="9" >'+data.company_name+'</th></tr><tr style="display:none;"><th> - '+msg+'</th></tr></thead></table></caption>');
            
            
            }
          });
	if (voucher_type.trim()!=''){
	    if ($.fn.DataTable.isDataTable('.table-voucher_register_sales')) {
	 		$('.table-voucher_register_sales').DataTable().destroy();
	 	} 
	  initDataTable('.table-voucher_register_sales', admin_url+'accounting/voucher_sales_reg_table', [], [], CustomersServerParams,<?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'ASC'))); ?>);
       
    	}
            else{
              alert("Please select Voucher Type");
            }
	}
  
 });
 
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
</body>
</html>