<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();   ?>

 <style>   
.voucher_register          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.voucher_register thead th { position: sticky; top: 0; z-index: 1; }
.voucher_register tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
    
</style>
<div id="wrapper">
  <div class="content"> 
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          
		<nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Voucher Register</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
          <div class="row">
            <div class="col-md-3">
               
            <?php echo render_select('voucher_type',$type_of_voucher,array('PassedFrom','PassedFrom'),'Select Book'); 
           // echo render_select( 'states',$states,array( 'short_name','state_name'), 'client_state','',array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                    $selected_company = $this->session->userdata('root_company');
                         ?>
                   <input type="hidden" name="comid" id"comid" value="<?php echo $selected_company;?>">      
            </div>  
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
                <?php //$cur_date = _d(date('Y-m-d')); ?>
              <?php echo render_date_input('from_date','from_date',$from_date); ?>
            </div>
            <div class="col-md-2">
              <?php echo render_date_input('to_date','to_date',$to_date); ?>
            </div>
            <div class="col-md-1">
                <br>
                <button class="btn btn-info pull-left mleft5 search_data" id="search_data">Show</button>
            </div>
            
            <div class="4">
                <br>
                <div class="custom_button">
                    <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="voucher_register" href="#" id="caexcel"><span>Export to excel</span></a>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                    <!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
                </div>
            </div>
            
          </div>
          
          <div class="row">
                <div class="col-md-12">
                    <div class="voucher_register load_data">
                    </div>
                </div>  
            
        </div>
            
        <span id="searchh" style="display:none;">
            Loading.....
        </span>
           
           
           
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
    $('#search_data').on('click',function(){
        var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var voucher_type = $("#voucher_type").val();
	    
	   $.ajax({
          url:"<?php echo admin_url(); ?>accounting/get_data",
          dataType:"JSON",
          method:"POST",
          cache: false,
          data:{from_date:from_date, to_date:to_date, voucher_type:voucher_type},
          beforeSend: function () {
            $('#searchh').css('display','block');
            $('.load_data').css('display','none');
         },
          complete: function () {
            $('.load_data').css('display','');
            $('#searchh').css('display','none');
         },
          success:function(data){
            $('.load_data').html(data);
          }
        });
	    
    });
});
</script>

<script>
    $("#caexcel").click(function(){
  var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var voucher_type = $("#voucher_type").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>accounting/voucher_register_report",
            method:"POST",
           data:{from_date:from_date, to_date:to_date, voucher_type:voucher_type},
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

function newexportaction(e, dt, button, config) {
         var self = this;
         var oldStart = dt.settings()[0]._iDisplayStart;
         dt.one('preXhr', function (e, s, data) {
             // Just this once, load all data from the server...
             data.start = 0;
             data.length = 2147483647;
             dt.one('preDraw', function (e, settings) {
                 // Call the original action function
                 if (button[0].className.indexOf('buttons-copy') >= 0) {
                     $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                 } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                     $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                         $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                         $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                 } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                     $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                         $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                         $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                 } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                     $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                         $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                         $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                 } else if (button[0].className.indexOf('buttons-print') >= 0) {
                     $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                 }
                 dt.one('preXhr', function (e, s, data) {
                     // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                     // Set the property to what it was before exporting.
                     settings._iDisplayStart = oldStart;
                     data.start = oldStart;
                 });
                 // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                 setTimeout(dt.ajax.reload, 0);
                 // Prevent rendering of the full data to the DOM
                 return false;
             });
         });
         // Requery the server with the new one-time export settings
         dt.ajax.reload();
     }
</script>

<script type="text/javascript">
 function printPage(){
        
         var from_date = $("#from_date").val();
	    var to_date = $("#to_date").val();
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="9"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="9"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:left;"colspan="9">Voucher Register : '+from_date+' To '+to_date+'</td>';
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
	
	
	$(document).on("click", ".sortablePop", function () {
		var table = $("#voucher_register tbody");
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