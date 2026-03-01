<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="panel_s">
          <div class="panel-body">
                <nav aria-label="breadcrumb" >
					<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
						<li class="breadcrumb-item" ><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
						<li class="breadcrumb-item active text-capitalize"><b>Masters</b></li>
						<li class="breadcrumb-item active" aria-current="page"><b>TDS Master</b></li>
					</ol>
				</nav>
				<hr class="hr_style">
    					
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new ItemID...</div>
                    </div>
                    <br>
                   <div class="col-md-2">
						<label class="control-label" for="TDSCode">TDS Code <span style="color: red;">*</span></label>
						<input type="text" id="TDSCode" name="TDSCode" class="form-control" value="">
						<span id="lblError" style="color: red"></span>
                    <!-- <button type="button" id="viewListBtn" class="btn btn-info btn-sm" style="margin-top: 26px; width: 100%;" title="View TDS List">View List</button> -->
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="TDSName">TDS Name <span style="color: red;">*</span></label>
                        <input type="text" id="TDSName" name="TDSName" pattern="[A-Za-z]+" class="form-control" value="" style="text-transform: uppercase;">
                        <span id="lblError" style="color: red"></span>
                    </div>
                    
                     <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Blocked <span style="color: red;">*</span></label>
                            <div style="margin-top: 8px;">
                                <label class="switch">
                                    <input type="checkbox" name="isactive" id="isactive_chk">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
					<input type="hidden" id="PerCount" name="PerCount"  value="0">
                    <div class="col-md-8" style="margin-top:1%;">
                        <table class="table items table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="45%">TDS Sub Section Name <span style="color: red;">*</span></th>
                                    <th width="30%">Rate (%) <span style="color: red;">*</span><span id="lblErrorRate" style="color: red"></span></th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                                <tbody id="parameter_body">
                                    <tr>
                                        <td width="45%"><input id="Description" class="form-control" name="TDSName[]" type="text" value=""></td>
                                        <td width="30%"><input id="rate" class="form-control numbers-only" name="TDSRate[]" value=""></td>
                                        <input type="hidden" name="xyz" id="xyz" value="count_TDSRate">
                                        <td width="5%"><button type="button" onclick="add_row()" class="btn btn-success" title="Add TDS Rate"><i class="fa fa-plus"></i></button></td>
                                    </tr>
                                </tbody>
                        </table>
                    </div>
                    <br><br>
                    <div class="col-md-12" style="margin-top:2%;">
                        <?php if (has_permission_new('tdsmaster', '', 'create')) {
                        ?>
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                        <?php
                        }?>
                        
                        <?php if (has_permission_new('tdsmaster', '', 'edit')) {
                        ?>
                        <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                        <?php
                        }?>
                        
                        <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
                    </div>
                </div>
                
                <div class="clearfix"></div>
            <!-- Iteme List Model-->
            
                <div class="modal fade AccountHead_List" id="AccountHead_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">TDS Section List</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                                <div class="col-md-5">
                                    <?php if (has_permission_new('tdsmaster', '', 'export')) {
                                    ?>
                                        <a class="btn btn-default buttons-excel buttons-html2" tabindex="0"
                                            aria-controls="table-trial_bal_report" href="#" id="caexcel"
                                            style="float: left ! important;"><span>Export to Excel</span></a>
                                    <?php } ?>
                                    
                                    <?php if (has_permission_new('tdsmaster', '', 'print')) {
                                    ?>
                                        <button class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</button>
                                    <?php } ?>
                               </div>
                            <div class="table-AccountHead_List tableFixHead2">
                                <table class="tree table table-striped table-bordered table-AccountHead_List tableFixHead2"
								id="table_AccountHead_List" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                        <td colspan="3" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?>
                                        </span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br>
                                        <span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th id="sl" style="text-align:left;">TDS Code</th>
                                            <th style="text-align:left;">TDS Name</th>
                                            <th style="text-align:left;">Threshold Limit</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ListTableBody">

                                    </tbody>
                                    
                                </table>   
                            </div>
                        </div>
                        <div class="modal-footer" style="padding:0px;">
                            <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." 
                            title="Type in a name" style="float: left;width: 100%;">
                        </div>
                        </div>
                    <!-- /.modal-content -->
                    </div>
                <!-- /.modal-dialog -->
                </div>
            <!-- /.modal -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script>
$(document).ready(function () {
    $('.updateBtn').hide();
    $('.updateBtn2').hide();

    // Initialize TDS Code on page load
    $.ajax({
        url: "<?php echo admin_url(); ?>TdsMaster/GetNextTDSCode",
        method: "POST",
        cache: false,
        dataType: "JSON",
        success: function (data) {
            $('#TDSCode').val(data.code);
            $('#TDSCode').prop('readonly', true);
            $('#TDSCode').css('background-color', '#e9ecef');
            // default Blocked unchecked for new records
            $('#isactive_chk').prop('checked', false);
        }
    });

    // View List button click
    $('#viewListBtn').on('click', function() {
        $('#AccountHead_List').modal('show');
    });

    $("#TDSCode").dblclick(function () {
        $('#AccountHead_List').modal('show');
        $('#AccountHead_List').on('shown.bs.modal', function () {
            $('#myInput1').val('');
            $('#myInput1').focus();

            var AccountID = "";
            $.ajax({
                url: "<?php echo admin_url(); ?>TdsMaster/AccountListPopUp",
                method: "POST",
                cache: false,
                data: { id: AccountID },
                success: function (data) {
                    if (data !== '') {
                        $("#ListTableBody").html(data);
                        $('.get_AccountID').on('click', function () {
							var TDSCode = $(this).data("id");  
                            $.ajax({
								url:"<?php echo admin_url(); ?>TdsMaster/GetAccountDetailByID",
								dataType:"JSON",
								method:"POST",
								data:{ TDSCode:TDSCode },
								beforeSend: function () {
								$('.searchh2').css('display','block');
								$('.searchh2').css('color','blue');
								},
								complete: function () {
								$('.searchh2').css('display','none');
								},
                                success:function(res){
                                    init_selectpicker();
                                    $('#TDSCode').val(res.TDSCode);
                                    $('#TDSName').val(res.TDSName);
                                    // Set toggle checked if Blocked == 'Y'
                                    $('#isactive_chk').prop('checked', (res.Blocked == 'Y'));

                                    // Clear existing parameter rows and append fetched details
                                    $('#parameter_body').empty();
                                    let TDSRateList = res.Details || [];
                                    for(var count = 0; count < TDSRateList.length; count++) {
                                        var TDSName = TDSRateList[count].description;
                                        var TDSRate = TDSRateList[count].rate;
                                        var ids = TDSRateList[count].id;
                                        var newcount = parseInt(count) + 1;

                                        var markup = "<tr class='addedtr'>";
                                        markup += "<td><input type='hidden' name='ids[]' id='ids"+newcount+"' value='"+ids+"'><input name='TDSName[]' id='TDSName"+newcount+"' value='"+TDSName+"' class='form-control'></td>";
                                        markup += "<td><input type='text' name='TDSRate[]' id='TDSRate"+newcount+"' value='"+TDSRate+"' class='form-control numbers-only'></td>";
                                        markup += "<td><a href='#' class='btn btn-danger removebtn' style='padding:2px 6px;'><i class='fa fa-times'></i></a></td></tr>";
                                        $('#parameter_body').append(markup);
                                    }

                                    // Update PerCount so newly added rows get unique ids
                                    $('#PerCount').val(TDSRateList.length);

                                    // Append a blank row with Add (+) button so user can add more rows while editing
                                    var addRow = "<tr>";
                                    addRow += "<td width='45%'><input id='Description' class='form-control' name='TDSName[]' type='text' value=''></td>";
                                    addRow += "<td width='30%'><input id='rate' class='form-control numbers-only' name='TDSRate[]' value=''></td>";
                                    addRow += "<input type='hidden' name='xyz' id='xyz' value='count_TDSRate'>";
                                    addRow += "<td width='5%'><button type='button' onclick='add_row()' class='btn btn-success' title='Add TDS Rate'><i class='fa fa-plus'></i></button></td>";
                                    addRow += "</tr>";
                                    $('#parameter_body').append(addRow);

                                    $('.saveBtn').hide();
                                    $('.updateBtn').show();
                                    $('.saveBtn2').hide();
                                    $('.updateBtn2').show();
                                }
							});
                            $('#AccountHead_List').modal('hide');
                        });
                    }
                }
            });
        })
    });
});
</script>
<script>
   $("#parameter_body").on('click','.removebtn',function(){
        $(this).parent().parent().remove();
	});
	
	function add_row(){
		var TDSSubName = $("#Description").val();
		var TDSRate = $("#rate").val();
		var PerCount = $("#PerCount").val();
		
		if(TDSSubName != '' && TDSRate !=''){
            var lastInputId = $('#parameter_body tr:last td:last input').attr('id');
            var newcount = parseInt(PerCount)+1;

            markup = "<tr class='addedtr'>";
            markup += "<td><input name='TDSName[]' id='TDSSubName"+newcount+"' value='"+TDSSubName+"' class='form-control'></td>";
            markup += "<td><input name='TDSRate[]' id='TDSRate"+newcount+"' value='"+TDSRate+"' class='form-control numbers-only'></td>";
            markup += "<td><a href='#' style='float:right;padding: 2px;width: 30px;' id='removebtn' class='btn btn-danger removebtn'><i class='fa fa-times'></i></a></td></tr>";
			tableBody = $("#parameter_body");
			tableBody.append(markup);
				
			$("#Description").val('');
			$("#rate").val('');
			$("#PerCount").val(newcount);
		}else{
			alert('All Fields Are Required');
		}
	
	}
	// Empty and open create mode
        $("#TDSCode").focus(function(){
            ResetForm();
        });
		function ResetForm(){
		    $('#TDSCode').val('');
            $('#TDSName').val('');
            $('#Description').val('');
            $('#rate').val('');
            $('#isactive_chk').prop('checked', false);
            $(".addedtr").remove();           
            $('.saveBtn').show();
            $('.saveBtn2').show();
        
                    // Fetch next TDS Code
                    $.ajax({
                        url: "<?php echo admin_url(); ?>TdsMaster/GetNextTDSCode",
                        method: "POST",
                        cache: false,
                        dataType: "JSON",
                        success: function (data) {
                            $('#TDSCode').val(data.code);
                        }
                    });
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
		}
		// Cancel selected data
        $(".cancelBtn").click(function(){
            ResetForm();
        });
		
		 // Save New Item
        $('.saveBtn').on('click',function(){ 
            TDSCode = $('#TDSCode').val();
            TDSName = $('#TDSName').val();
            isactive = $('#isactive_chk').is(':checked') ? 'Y' : 'N';
	        let ParadataArr = [];
		    var i = 1;
			var fill_subName = $("input[name='TDSName[]']")
			.map(function(){return $(this).val();}).get();
    		fill_subName.forEach(function callback(value, index) {
    			if(value != "")
    			{
    				var rate = $("input[name='TDSRate[]']")
    				.map(function(){return $(this).val();}).get()[index];
    				
    				var ii = i - 1;
    				ParadataArr[ii]=new Array();
    				ParadataArr[ii][0]=value;
    				ParadataArr[ii][1]=rate;
    				i++;
    			}
    		});
		
		    let paradataArraylength = ParadataArr.length;
		    var paradataSerializedArr = JSON.stringify(ParadataArr);
        if(TDSCode == ''){
            alert('please enterTDSCode');
            $('#TDSCode1').focus();
        }else{
            $.ajax({
                url:"<?php echo admin_url(); ?>TdsMaster/SaveItemID",
                dataType:"JSON",
                method:"POST",
                data:{TDSCode:TDSCode,isactive:isactive,
                    TDSName:TDSName,paradataArraylength:paradataArraylength,paradataSerializedArr:paradataSerializedArr
                },
                beforeSend: function () {
                $('.searchh3').css('display','block');
                $('.searchh3').css('color','blue');
                },
                complete: function () {
                $('.searchh3').css('display','none');
                },
                success:function(data){
                   if(data.success == true){
                       alert_float('success', 'Record created successfully...');
                       ResetForm();
                   }else{
                       alert_float('warning', data.message || 'Something went wrong...');
                   }
                }
            });    
        }
            
        });
		
		// Update Exiting Item
        $('.updateBtn').on('click',function(){ 
            TDSCode = $('#TDSCode').val();
			TDSName = $('#TDSName').val();
            isactive = $('#isactive_chk').is(':checked') ? 'Y' : 'N';
			let ParadataArr = [];
			var fill_subName = $("input[name='TDSName[]']")
				.map(function () {
					return $(this).val();
				}).get();
			fill_subName.forEach(function callback(value, index) {
				if (value != "") {

					var rate = $("input[name='TDSRate[]']")
						.map(function () {
							return $(this).val();
						}).get()[index];
					var ids = $("input[name='ids[]']")
						.map(function () {
							return $(this).val();
						}).get()[index];

					ParadataArr[index] = [value, rate, ids];
				}
			});

			let paradataArraylength = ParadataArr.length;
			var paradataSerializedArr = JSON.stringify(ParadataArr);
			 $.ajax({
                url:"<?php echo admin_url(); ?>TdsMaster/UpdateItemID",
                dataType:"JSON",
                method:"POST",
				data:{TDSCode:TDSCode,isactive:isactive,
                    TDSName:TDSName,paradataArraylength:paradataArraylength,paradataSerializedArr:paradataSerializedArr
                },
                beforeSend: function () {
                $('.searchh3').css('display','block');
                $('.searchh3').css('color','blue');
                },
                complete: function () {
                $('.searchh3').css('display','none');
                },
                success:function(data){
                   if(data.success == true){
                       alert_float('success', 'Record updated successfully...');
                       ResetForm();
                   }else{
                       alert_float('warning', data.message || 'Something went wrong...');
                   }
                }
            });
        });
</script>
<script>
$(".numbers-only").keypress(function (e) {
    if(e.which == 46){
        if($(this).val().indexOf('.') != -1) {
            return false;
        }
    }

    if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});

    $("#ThresholdLimit").on("input", function(evt) {
		var self = $(this);
		self.val(self.val().replace(/[^0-9\.]/g, ''));
		if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
		{
			evt.preventDefault();
		}
	});
</script>

<style>
.switch {position:relative;display:inline-block;width:50px;height:24px}
.switch input {display:none}
.switch .slider {position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#ccc;transition:.4s;border-radius:24px}
.switch .slider:before {position:absolute;content:"";height:18px;width:18px;left:3px;bottom:3px;background-color:white;transition:.4s;border-radius:50%;}
.switch input:checked + .slider {background-color:#28a745}
.switch input:checked + .slider:before {transform:translateX(26px)}

#tds_code {
    text-transform: uppercase;
}
#table_Item_List td:hover {
    cursor: pointer;
}
#table_Item_List tr:hover {
    background-color: #ccc;
}
    .table-Item_List          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-Item_List thead th { position: sticky; top: 0; z-index: 1; }
    .table-Item_List tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>
<style>
    #table_AccountHead_List td:hover {
        cursor: pointer;
    }

    #table_AccountHead_List tr:hover {
        background-color: #ccc;
    }
    .table-AccountHead_List {
        overflow: auto;
        max-height: 65vh;
        width: 100%;
        position: relative;
        top: 0px;
    }
    .table-AccountHead_List thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .table-AccountHead_List tbody th {
        position: sticky;
        left: 0;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th,
    td {
        padding: 1px 5px !important;
        white-space: nowrap;
        border: 1px solid !important;
        font-size: 11px;
        line-height: 1.42857143 !important;
        vertical-align: middle !important;
    }
    th {
        background: #50607b;
        color: #fff !important;
    }
</style>