<script type='text/javascript'>
    $(document).ready(function(){
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
        $('.printBtn').hide();
        $("#AccountID").autocomplete({
        
        source: function( request, response ) {
          
          $.ajax({
            url: "<?=base_url()?>admin/VehRtn/GetAccountlistForCrates",
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
             
       //alert(Conform);
       var ChallanID = $('#challan_n').val();
       if(empty(ChallanID)){
          alert('please select challanID');
            $("#AccountID").focus();
            return false;
       }else{
                $('#AccountID').val(ui.item.value);
                $('#party_name').html(ui.item.label);
                $('#party_name_val').val(ui.item.label);
                $('#address').html(ui.item.address);
                $('#address_val').val(ui.item.address);
                
                $("#rtncrates").focus();
                return false;
            }
        }
      });
        $('#AccountID').on('blur', function () {
            var AccountID = $(this).val();
            var ChallanID = $('#challan_n').val();
            if(empty(AccountID)){
                
            }else{
                $.ajax({
                    url: "<?=base_url()?>admin/VehRtn/getAccountDetails",
                    type: 'post',
                    dataType: "json",
                    data: {
                      AccountID: AccountID,ChallanID:ChallanID
                    },
                    success: function( data ) {
                        if(empty(data)){
                            alert('AccountID not found.');
                            $("#AccountID").val('');
                            $("#AccountID").focus();
                        }else{
                            $('#AccountID').val(data.AccountID); // display the selected text
                            $('#address_val').val(data.Address); // display the selected text
                            $('#address').html(data.Address); // display the selected text
                            $('#party_name_val').val(data.company); // display the selected text
                            $('#party_name').html(data.company); // display the selected text
                            $('#chlCrates').html(data.CHLCrates); // display the selected text
                            $('#chlCrates_val').val(data.CHLCrates); // display the selected text
                            $('#opnCrates_val').val(data.OQty); // display the selected text
                            $('#opnCrates').html(data.OQty); // display the selected text
                            $('#balCrates_new_val').val(data.BQty); // display the selected text
                            $('#balcrates').val(data.BQty); // display the selected text
                            $('#balCrates_new').html(data.BQty); // display the selected text
                            
                            $("#rtncrates").focus();
                        }
                    }
                });
            }
        });
        
        // Focus On AccountID For Crates
            $('#AccountID').on('focus',function(){
                $('#AccountID').val(''); // display the selected text
                $('#address_val').val(''); // display the selected text
                $('#address').html(''); // display the selected text
                $('#party_name_val').val(''); // display the selected text
                $('#party_name').html(''); // display the selected text
                $('#opnCrates_val').val(''); // display the selected text
                $('#opnCrates').html(''); // display the selected text
                $('#balCrates_new_val').val(''); // display the selected text
                $('#balcrates').val(''); // display the selected text
                $('#balCrates_new').html(''); // display the selected text
            })
        
        $('#crate_details').show();
        $('#fresh_stock_return').hide(); 
        $('#payment_reciept').hide();
        $('#expense_detail').hide();
        
        $(".crate_details").click(function(){
            $('#crate_details').show();
            $('#fresh_stock_return').hide(); 
            $('#payment_reciept').hide();
            $('#expense_detail').hide();
        })
        $(".fresh_stock_return").click(function(){
            $('#crate_details').hide();
            $('#fresh_stock_return').show();
             $('#payment_reciept').hide();
            $('#expense_detail').hide();
        })
        $(".payment_reciept").click(function(){
            $('#crate_details').hide();
            $('#fresh_stock_return').hide();
            $('#payment_reciept').show();
            $('#expense_detail').hide();
        }) 
        
        $(".expense_details").click(function(){
            $('#crate_details').hide();
            $('#fresh_stock_return').hide();
            $('#payment_reciept').hide();
            $('#expense_detail').show();
        })
    
    
        $("#vehicle_return_id").dblclick(function(){
            $('#transfer-modal_return_list').find('button[type="submit"]').prop('disabled', false);
                $('#transfer-modal_return_list').modal('show');
                $('#transfer-modal_return_list').on('shown.bs.modal', function () {
                    $('#myInput2').focus();
                })
        });
        
        // Focus On VehicleRtnID
            $('#vehicle_return_id').on('focus',function(){
                //alert('hello');
                var NextVRtnID = $("#NextVRtnID").val();
                $("#vehicle_return_id").val(NextVRtnID);
                $("#challan_n").val('');
                $("#route_code").val('');
                $("#route_name").val('');
                $("#routekm").val('0.00');
                $("#vehicle_number").val('');
                $("#vehicle_capc").val('0.00');
                $("#driver_id").val('');
                $("#driver_name").val('');
                $("#loder_id").val('');
                $("#loder_name").val('');
                $("#salesman_id").val('');
                $("#salesman_name").val('');
                $("#challan_crates").val('0.00');
                $("#refund_crates").val('0.00');
                $("#fresh_ret_amt1").val('0.00');
                $("#case_depo1").val('0.00');
                $("#check_depo").val('0.00');
                $("#NERT_trans").val('0.00');
                $("#total_expense").val('0.00');
                $("#total_expense1").val('0.00');
                $('#challan_n').attr('readonly', false);
                
                $('#crate_details').show();
                $('#fresh_stock_return').hide(); 
                $('#payment_reciept').hide();
                $('#expense_detail').hide();
                $('#print_tbl1 tbody').html('');
                $('#print_tbl2 tbody').html('');
                var html = '';
                html += '<tr class="accounts" id="row">';
                html += '<td id="AccountIDTD" style="width: 125px;"><input type="text" name="AccountID" style="width: 125px;" id="AccountID" class="ui-autocomplete-input" autocomplete="off" ></td>';
                html += '<td style="padding:1px 5px !important;"><span id="party_name"></span><input type="hidden" name="party_name_val" id="party_name_val"></td>';
                html += '<td style="padding:1px 5px !important;"><span id="address"></span><input type="hidden" name="address_val" id="address_val" value="" ></td>';
                html += '<td style="padding:1px 5px !important;text-align: right;"><span id="opnCrates"></span><input type="hidden" name="opnCrates_val" id="opnCrates_val"></td>';
                html += '<td style="padding:1px 5px !important;text-align: right;"><span id="chlCrates"><input type="hidden" name="chlCrates_val" id="chlCrates_val"></td>';
                html += '<td class="rtnqty" style="width: 80px;">';
                html += '<input type="text" name="rtncrates" id="rtncrates" class="rtncrates" onblur="calculate_balcrates();" style="width: 80px;text-align: right;"  >';
                html += '<input type="hidden" name="balcrates" id="balcrates" value="0">';
                html += '<input type="hidden" name="colno" id="colno" value="">';
                html += '</td>';
                html += '<td tyle="padding:1px 5px !important;text-align: right;"><span id="balCrates_new" style="text-align: right;"></span><input type="hidden" name="balCrates_new_val" id="balCrates_new_val" class="form-control"></td>';
                html += '</tr>';
               // $('#crate_details_tbl tbody').html(html);
               var TotalRow = $("#row_count").val();
               var crRow = parseInt(TotalRow);
               
               for (var A = 1; A <= crRow; A++) {
                   var id = 'row'+A;
                    document.getElementById(id).remove();
               }
                var html2 = '';
                $('#stock_details_tbl').html(html2);
                
                var html3 = '';
                html3 += '<tr class="accounts" id="row">';
                html3 += '<td id="AccountIDTD_pay" style="width: 125px;"><input type="text" name="AccountID_pay" style="width: 125px;" id="AccountID_pay" style="width: 125px;"></td>';
                html3 += '<td style="padding:1px 5px !important;"><span id="party_name_pay"></span><input type="hidden" name="party_name_pay_val" id="party_name_pay_val"></td>';
                html3 += '<td style="padding:1px 5px !important;"><span id="address_pay"></span><input type="hidden" name="address_pay_val" id="address_pay_val" value="" ></td>';
                html3 += '<td style="width: 80px;" class="rcptAmts"><input type="text" name="receiptamt" id="receiptamt" onblur="calculate_payment();"  style="width: 80px;text-align: right" onkeypress="return isNumber(event)" value="" ></td>';
                html3 += '</tr>';
                //$('#payment_details_tbl tbody').html(html3);
                var TotalRow = $("#row_count_pay").val();
                //var crRow = parseInt(TotalRow) - 1;
               
               for (var A = 1; A <= parseInt(TotalRow); A++) {
                   var id = 'row_pay'+A;
                    document.getElementById(id).remove();
               }
                var html4 = '';
                html4 += '<tr class="accounts" id="row">';
                html4 += '<td id="AccountIDTD_exp" style="width: 125px;"><input type="text" name="AccountID_exp" id="AccountID_exp" style="width: 125px;"></td>';
                html4 += '<td style="padding:1px 5px !important;"><span id="party_name_exp"></span><input type="hidden" name="party_name_exp_val" id="party_name_exp_val"></td>';
                html4 += '<td style="padding:1px 5px !important;"><span id="address_exp"></span><input type="hidden" name="address_exp_val" id="address_exp_val" value="" ></td>';
                html4 += '<td style="width: 80px;" class="expamts"><input type="text" name="expamt" id="expamt" value="" onblur="calculate_expense();"  style="width: 80px;text-align:right;" onkeypress="return isNumber(event);"></td>';
                html4 += '</tr>';
                //$('#expense_details_tbl tbody').html(html4);
                var TotalRow = $("#row_count_exp").val();
                //var crRow = parseInt(TotalRow) - 1;
               
               for (var A = 1; A <= parseInt(TotalRow); A++) {
                   var id = 'row_exp'+A;
                    document.getElementById(id).remove();
               }
                
                $("#ItemCount").val('0');
                $("#row_count").val('0');
                $("#row_count_pay").val('0');
                $("#row_count_exp").val('0');
                
                $('.saveBtn').show();
                $('.saveBtn2').show();
                $('.updateBtn').hide();
                $('.updateBtn2').hide();
                $('.printBtn').hide();
            });
    });
    
    // Get Vehicle Rtn Details click on pop up
        
        $('.get_VehicleRtnID').on('click',function(){ 
            VRtnID = $(this).attr("data-id");
            $('#transfer-modal_return_list').modal('hide');
            $.ajax({
              url:"<?php echo admin_url(); ?>VehRtn/GetDetail",
              dataType:"JSON",
              method:"POST",
              data:{VRtnID:VRtnID},
            beforeSend: function () {
                $('#searchh11').css('display','block');
                $('#searchh11').css('color','blue');
            },
            complete: function () {
                $('#searchh11').css('display','none');
            },
            success:function(response){
                console.log(response);
                let VRtnDetails = response.ChallanDetails;
                let CratesDetails = response.CratesDetails;
                //alert(response.ReturnID);
                $('#vehicle_return_id').val(VRtnDetails.ReturnID);
                $('#challan_n').val(VRtnDetails.ChallanID);
                $('#route_code').val(VRtnDetails.RouteID);
                $('#route_name').val(VRtnDetails.name);
                $('#routekm').val(VRtnDetails.KM);
                $('#vehicle_number').val(VRtnDetails.VehicleID);
                $('#driver_id').val(VRtnDetails.DriverID);
                if(VRtnDetails.driver_fn !== null){
                    if(VRtnDetails.driver_ln !== null){
                        var DName = VRtnDetails.driver_fn +' '+VRtnDetails.driver_ln;
                    }else{
                        var DName = VRtnDetails.driver_fn;
                    }
                    
                }else{
                    var DName = '';
                }
                $('#driver_name').val(DName);
                if(VRtnDetails.loader_fn !== null){
                    if(VRtnDetails.loader_ln !== null){
                        var LName = VRtnDetails.loader_fn +' '+VRtnDetails.loader_ln;
                    }else{
                        var LName = VRtnDetails.loader_fn;
                    }
                }else{
                    var LName = '';
                }
                $('#loder_id').val(VRtnDetails.LoaderID);
                $('#loder_name').val(LName);
                $('#salesman_id').val(VRtnDetails.SalesmanID);
                if(VRtnDetails.Salesman_fn !== null){
                    if(VRtnDetails.Salesman_ln !== null){
                        var SName = VRtnDetails.Salesman_fn +' '+VRtnDetails.Salesman_ln;
                    }else{
                        var SName = VRtnDetails.Salesman_fn;
                    }
                }else{
                    var SName = '';
                }
                $('#salesman_name').val(SName);
                
                if(VRtnDetails.UserID_fn !== null){
                    if(VRtnDetails.UserID_ln !== null){
                        var UName = VRtnDetails.UserID_fn +' '+VRtnDetails.UserID_ln;
                    }else{
                        var UName = VRtnDetails.UserID_fn;
                    }
                }else{
                    var UName = '';
                }
                $('#UserIDName').val(UName);
                    
                $('#vehicle_capc').val(VRtnDetails.VehicleCapacity);
                $('#challan_crates').val(VRtnDetails.Crates);
                $('#refund_crates').val(VRtnDetails.return_crates);
                var ChlDate = VRtnDetails.Transdate.substring(0, 10);
                var ChldateNew = ChlDate.split("-").reverse().join("/");
                $('#to_date').val(ChldateNew);
                
                var VRtnDate = VRtnDetails.returnTransdate.substring(0, 10);
                var VRtndateNew = VRtnDate.split("-").reverse().join("/");
                $('#from_date').val(VRtndateNew);
                $('#challan_n').attr('readonly', true);
                var html = '';
                const printAccount = [];
                for (var index = 0; index < CratesDetails.length; index++) {
                    printAccount.push(CratesDetails[index].act_id);
                    var col = index + 1;
                    html += '<tr class="accounts" id="row'+col+'">';
                    html += '<td style="width: 125px;"><input type="text" readonly name="AccountID'+col+'" style="width: 125px;" id="AccountID'+col+'" value="'+ CratesDetails[index].act_id+'"></td>';
            	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].company+'</td>';
            	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].address+'</td>';
            	    if(CratesDetails[index].OQty == null || CratesDetails[index].OQty == 'undefined'){
                	        var OpeningQty = 0;
                	    }else{
                	        var OpeningQty = CratesDetails[index].OQty;
                	    }
                	    if(CratesDetails[index].VRtnCrates){
                	        var VRtnCrates = CratesDetails[index].VRtnCrates;
                	    }else{
                	         var VRtnCrates = 0;
                	    }
            	    html += '<td style="padding:1px 5px !important;">'+OpeningQty+'</td>';
            	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].CHLCrates+'</td>';
            	    var befor_crate_bal = parseInt(OpeningQty);
            	    html += '<td class="rtnqty" style="width: 80px;"><input type="text" name="rtncrates'+col+'" id="rtncrates'+col+'" onblur="calculate_balcrates();" value="'+VRtnCrates+'" style="width: 80px;text-align:right;" class="rtncrates"><input type="hidden" name="balcrates'+col+'" id="balcrates'+col+'" value="'+befor_crate_bal+'"><input type="hidden" name="colno" id="colno" value="'+col+'"></td>';
            	    html += '<td id="balCrates" style="padding:1px 5px !important;text-align:right;"><span>'+CratesDetails[index].balance_crates+'</span></td>';
                    html += '</tr>';
                }
                
                $("#row_count").val(col);
                $('#crate_details_tbl tbody').append(html);
                
                let Itemlist = response.SaleRtnDetails.itemhead;
                let Orderdata = response.SaleRtnDetails.Orderdata;
                let ItemOrderData = response.SaleRtnDetails.ItemOrderData;
                let ItemRtnData = response.SaleRtnDetails.ItemRtnData;
                
                var html2 = '';
                html2 += '<table class="table table-striped table-bordered stock_details_tbl fixed_header1" id="stock_details_tbl" width="100%">';
                html2 += '<thead id="thead">';
                html2 += '<tr>';
                html2 += '<th align="center" >AccountID</th>';
                html2 += '<th align="center" >AccoutName</th>';
                html2 += '<th align="center" >SaleID</th>';
                html2 += '<th align="center">RtnAMT</th>';
                html2 += '<th align="center" >CGST</th>';
                html2 += '<th align="center" >SGST</th>';
                html2 += '<th align="center" >IGST</th>';
                for (var index2 = 0; index2 < Itemlist.length; index2++) {
                    //html2 += '<th align="center" width="10%">'+Itemlist[index2]+'</th>';
                    html2 += '<th style="width: 150px;text-align: center">'+Itemlist[index2]+'</th>';
                }
                
                html2 += '</tr>';
                html2 += '</thead>';
                html2 += '<tbody id="tbody">';
                var j= 1;
                var itemCount = 0;
                var TotalRtnAmt = 0;
                $.each(Orderdata, function (column1, value1) {
                    printAccount.push(value1["AccountID"]);
                    html2 += '<tr class="accounts" id="row'+j+'">';
                    html2 += '<td style="padding:1px 5px !important;">'+value1["AccountID"]+'<input type="hidden" name="AccountID_SRtn'+j+'" id="AccountID_SRtn'+j+'" value="'+value1["AccountID"]+'"></td>';
                    html2 += '<td style="padding:1px 5px !important;">'+value1["company"]+'</td>';
                    html2 += '<td style="padding:1px 5px !important;">'+value1["SalesID"]+'</td>';
                var totaRtmAmt = 0;
                var total_igst = 0;
                var total_cgst = 0;
                var total_sgst = 0;
                
                        $.each(ItemRtnData, function (column3, value3) {
                            if(value1["SalesID"] == value3["TransID"]){
                                totaRtmAmt = parseFloat(totaRtmAmt) + parseFloat(value3['NetChallanAmt']);
                                total_igst = parseFloat(total_igst) + parseFloat(value3['igstamt']);
                                total_cgst = parseFloat(total_cgst) + parseFloat(value3['cgstamt']);
                                total_sgst = parseFloat(total_sgst) + parseFloat(value3['sgstamt']);
                            }
                        })
                    TotalRtnAmt += parseFloat(totaRtmAmt);
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="RtnAmt_val'+j+'" id="RtnAmt_val'+j+'" value="'+parseFloat(totaRtmAmt).toFixed(2)+'"><span id="RtnAmt">'+parseFloat(totaRtmAmt).toFixed(2)+'</span></td>';
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="cgst_val'+j+'" id="cgst_val'+j+'" value="'+parseFloat(total_cgst).toFixed(2)+'"><span id="cgst">'+parseFloat(total_cgst).toFixed(2)+'</span></td>';
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="sgst_val'+j+'" id="sgst_val'+j+'" value="'+parseFloat(total_sgst).toFixed(2)+'"><span id="sgst">'+parseFloat(total_sgst).toFixed(2)+'</span></td>';
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="igst_val'+j+'" id="igst_val'+j+'" value="'+parseFloat(total_igst).toFixed(2)+'"><span id="igst">'+parseFloat(total_igst).toFixed(2)+'</span></td>';
                
                    $.each(Itemlist, function (column4, value4) {
                        var match = 0;
                        var rate = 0;
                        var gst = 0;
                        var BilledQty = 0;
                        var ORDRtnBilledQty = '';
                        var ItemID = 0;
                        var TransID = 0;
                        var AccountID = 0;
                        var PackQty = 0;
                        var state = '';
                        $.each(ItemOrderData, function (column5, value5) {
                            if(value4 == value5["ItemID"] && value1["SalesID"] == value5["TransID"]){
                                itemCount = itemCount + 1;
                                match = 1;
                                rate = value5["BasicRate"];
                                BilledQty = value5["BilledQty"];
                                ItemID = value5["ItemID"];
                                TransID = value5["TransID"];
                                AccountID = value5["AccountID"];
                                PackQty = value5["CaseQty"];
                                if(value5["igst"] == "0.00" || value5["igst"] == null){
                                    gst = value5["cgst"] * 2;
                                    state = "UP"
                                }else{
                                    gst = value5["igst"];
                                    state = "UP1"
                                }
                            }
                        })
                        $.each(ItemRtnData, function (column6, value6) {
                            if(value4 == value6['ItemID'] && value1["SalesID"] == value6["TransID"]){
                                ORDRtnBilledQty = value6['BilledQty'];
                            }
                        })
                        if(match == "1"){
                        html2 += '<td style="width: 100px;"><input type="hidden" name="PackQty_val'+itemCount+'" id="PackQty_val'+itemCount+'" value="'+PackQty+'"><input type="hidden" name="AccountID_val'+itemCount+'" id="AccountID_val'+itemCount+'" value="'+AccountID+'"><input type="hidden" name="ItemID_val'+itemCount+'" id="ItemID_val'+itemCount+'" value="'+ItemID+'"><input type="hidden" name="TransID_val'+itemCount+'" id="TransID_val'+itemCount+'" value="'+TransID+'"><input type="hidden" name="rate_val'+itemCount+'" id="rate_val'+itemCount+'" value="'+rate+'"><input type="hidden" name="gst_val'+itemCount+'" id="gst_val'+itemCount+'" value="'+gst+'"><input type="hidden" name="state_val'+itemCount+'" id="state_val'+itemCount+'" value="'+value1["state"]+'"><input type="hidden" name="BilledQty" id="BilledQty" value="'+BilledQty+'"><input type="hidden" name="state" id="state" value="'+value1["state"]+'"><input type="hidden" name="rate" id="rate" value="'+rate+'"><input type="hidden" name="gst" id="gst" value="'+gst+'"><input type="text" name="rtnqty'+itemCount+'" id="rtnqty'+itemCount+'" onblur="calculate_rtnqty();" onkeypress="return isNumber(event)" onfocus="myFunction(this)" style="width: 60px;text-align: right;" value="'+ORDRtnBilledQty+'" ></td>';
                        }else{
                            html2 += '<td style="width: 150px;padding:1px 5px !important;text-align: right;background-color: #aea5a599;"></td>';
                        }
                    })
                    html2 += '</tr>';
                    j++;
                });
                html2 += '</tbody>';
                html2 += '</table>';
                $('#fixed_header1').html(html2);
                $("#row_count_frRtn").val(j-1);
                $("#ItemCount").val(index2);
                $('#fresh_ret_amt1').val(parseFloat(TotalRtnAmt).toFixed(2));
                $('#fresh_ret_amt').val(parseFloat(TotalRtnAmt).toFixed(2));
                
                // Payment Table
                
                let PaymentsDetails = response.PaymentsDetails;
                var ii = 1;
                var html3 = '';
                var TotalPayAmt = 0;
                $.each(PaymentsDetails, function (column7, value7) {
                    if(value7['payment_recipt_Amount'] == null ){
                            
                        }else{
                            printAccount.push(value7['Aid']);
                            html3 += '<tr class="accounts" id="row_pay'+ii+'">';
                            html3 += '<td style="width: 125px;" id="AccountIDTD_pay"><input type="text" name="AccountID_pay'+ii+'" style="width: 125px;" id="AccountID_pay'+ii+'" value="'+value7['Aid']+'"></td>';
                            html3 += '<td style="padding:1px 5px !important;">'+value7['company']+'</td>';
                            html3 += '<td style="padding:1px 5px !important;">'+value7['address']+'</td>';
                            html3 += '<td class="rcptAmts" style="width: 80px;"><input type="text" name="receiptamt'+ii+'" id="receiptamt'+ii+'"  onblur="calculate_payment();" value="'+value7['payment_recipt_Amount']+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
                            html3 += '</tr>';
                            ii++; 
                            TotalPayAmt += parseFloat(value7['payment_recipt_Amount']);
                        }
                })
                $('#case_depo1').val(parseFloat(TotalPayAmt).toFixed(2));
                $('#case_depo').val(parseFloat(TotalPayAmt).toFixed(2));
                $('#payment_details_tbl tbody').append(html3);
                $("#row_count_pay").val(ii-1);
                
                // Print Table for collection
                    printhtml = '';
                    var srno = 1;
                    var FrtnSum = 0;
                    var AmtSum = 0;
                    var CratesSum = 0;
                    let uniqueChars = [...new Set(printAccount)];
                    $.each(uniqueChars, function (column88, value88) {
                        printhtml += '<tr>';
                        printhtml += '<td align="center">'+srno+'</td>';
                        var partyName = '';
                        var Frtn = 0;
                        var Amt = 0;
                        var Crates = 0;
                        for (var index = 0; index < CratesDetails.length; index++) {
                            if(value88 == CratesDetails[index].act_id){
                                partyName = CratesDetails[index].company;
                                if(CratesDetails[index].VRtnCrates){
                        	        var VRtnCrates = CratesDetails[index].VRtnCrates;
                        	    }else{
                        	         var VRtnCrates = 0;
                        	    }
                        	    Crates += parseFloat(VRtnCrates);
                            }
                        }
                        $.each(Orderdata, function (column1, value1) {
                            if(value88 == value1["AccountID"]){
                                partyName = value1["company"];
                                var totaRtmAmt = 0;
                                $.each(ItemRtnData, function (column3, value3) {
                                    if(value1["SalesID"] == value3["TransID"]){
                                        totaRtmAmt = parseFloat(totaRtmAmt) + parseFloat(value3['NetChallanAmt']);
                                    }
                                })
                                Frtn = parseFloat(Frtn) + parseFloat(totaRtmAmt);
                            }
                        })
                        $.each(PaymentsDetails, function (column7, value7) {
                            if(value88 == value7['Aid']){
                                partyName = value7['company'];
                                Amt = parseFloat(Amt) + parseFloat(value7['payment_recipt_Amount']);
                            }
                        })
                        
                        
                        printhtml += '<td align="left">'+partyName+'</td>';
                        printhtml += '<td align="right">'+Frtn.toFixed(2)+'</td>';
                        FrtnSum = parseFloat(FrtnSum) + parseFloat(Frtn);
                        printhtml += '<td align="right">'+Amt.toFixed(2)+'</td>';
                        AmtSum = parseFloat(AmtSum) + parseFloat(Amt);
                        printhtml += '<td align="right">'+Crates+'</td>';
                        CratesSum = parseFloat(CratesSum) + parseFloat(Crates);
                        printhtml += '</tr>';
                        srno++;
                    })
                    printhtml += '<tr>';
                    printhtml += '<td align="left"></td>';
                    printhtml += '<td align="left">Total</td>';
                    printhtml += '<td align="right">'+FrtnSum.toFixed(2)+'</td>';
                    printhtml += '<td align="right">'+AmtSum.toFixed(2)+'</td>';
                    printhtml += '<td align="right">'+CratesSum.toFixed(2)+'</td>';
                    printhtml += '</tr>';
                    $('#print_tbl1 tbody').append(printhtml);
                    // Expenses table
                    let ExpenseDetails = response.ExpenseDetails;
                    var iii = 1;
                    var html4 = '';
                    var TotalExpAmt = 0;
                    var printhtml2 = '';
                    $.each(ExpenseDetails, function (column8, value8) {
                        if(value8['expense_Amount'] == null ){
                            
                        }else{
                            html4 += '<tr class="accounts" id="row_exp'+iii+'">';
                            html4 += '<td style="width: 125px;" id="AccountIDTD_exp'+iii+'"><input type="text" name="AccountID_exp'+iii+'" style="width: 125px;" id="AccountID_exp'+iii+'" value="'+value8['Aid']+'"></td>';
                            if(value8['firstname'] == null){
                                var Name = value8['company'];
                                if(value8['address'] == null){
                                    var Address = '';
                                }else{
                                    var Address = value8['address'];
                                }
                            }else{
                                var Name = value8['firstname']+' '+value8['lastname'];
                                if(value8['address'] == null){
                                    var Address = '';
                                }else{
                                    var Address = value8['current_address'];
                                }
                            }
                            html4 += '<td style="padding:1px 5px !important;">'+Name+'</td>';
                            html4 += '<td style="padding:1px 5px !important;">'+Address+'</td>';
                            html4 += '<td class="expamts" style="width: 80px;"><input type="text" name="expamt'+iii+'" id="expamt'+iii+'" onblur="calculate_expense();"  value="'+value8['expense_Amount']+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
                            html4 += '</tr>';
                            
                            TotalExpAmt += parseFloat(value8['expense_Amount']);
                            
                            printhtml2 += '<tr>';
                            printhtml2 += '<td align="center">'+iii+'</td>';
                            printhtml2 += '<td>'+Name+'</td>';
                            printhtml2 += '<td align="right">'+value8['expense_Amount']+'</td>';
                            printhtml2 += '</tr>';
                            iii++;
                        }
                    })
                    printhtml2 += '<tr>';
                    printhtml2 += '<td align="center"></td>';
                    printhtml2 += '<td align="right">Total</td>';
                    printhtml2 += '<td align="right">'+TotalExpAmt.toFixed(2)+'</td>';
                    printhtml2 += '</tr>';
                    $('#print_tbl2 tbody').append(printhtml2);
                $('#total_expense1').val(parseFloat(TotalExpAmt).toFixed(2));
                $('#total_expense').val(parseFloat(TotalExpAmt).toFixed(2));
                $('#expense_details_tbl tbody').append(html4);
                $("#row_count_exp").val(iii-1);
                
                    $('.saveBtn').hide();
                    $('.updateBtn').show();
                    $('.saveBtn2').hide();
                    $('.updateBtn2').show();
                    $('.printBtn').show();
            }
            });
        });
    
    // Get VehRtn Detail by VehRtnID
    $('#vehicle_return_id').on('blur',function(){
        var VRtnID = $(this).val();
        $('#route_name').focus();
        $.ajax({
            url:"<?php echo admin_url(); ?>VehRtn/GetDetail",
            dataType:"JSON",
            method:"POST",
            data:{VRtnID:VRtnID},
            beforeSend: function () {
                $('#searchh11').css('display','block');
                $('#searchh11').css('color','blue');
            },
            complete: function () {
                $('#searchh11').css('display','none');
            },
            success:function(response){
                if(empty(response)){
                    var NextVRtnID = $("#NextVRtnID").val();
                    $("#vehicle_return_id").val(NextVRtnID);
                    $("#challan_n").val('');
                    $("#route_code").val('');
                    $("#route_name").val('');
                    $("#routekm").val('0.00');
                    $("#vehicle_number").val('');
                    $("#vehicle_capc").val('0.00');
                    $("#driver_id").val('');
                    $("#driver_name").val('');
                    $("#loder_id").val('');
                    $("#loder_name").val('');
                    $("#salesman_id").val('');
                    $("#salesman_name").val('');
                    $("#challan_crates").val('0.00');
                    $("#refund_crates").val('0.00');
                    $("#fresh_ret_amt1").val('0.00');
                    $("#case_depo1").val('0.00');
                    $("#check_depo").val('0.00');
                    $("#NERT_trans").val('0.00');
                    $("#total_expense").val('0.00');
                    $("#total_expense1").val('0.00');
                    $('#crate_details').show();
                    $('#fresh_stock_return').hide(); 
                    $('#payment_reciept').hide();
                    $('#expense_detail').hide();
                    $('#challan_n').attr('readonly', false);
                   // Create 
                   var TotalRow = $("#row_count").val();
                   var crRow = parseInt(TotalRow);
                   for (var A = 1; A <= crRow; A++) {
                       var id = 'row'+A;
                        document.getElementById(id).remove();
                   }
                   // Fresh RTN
                    var html2 = '';
                    $('#stock_details_tbl').html(html2);
                    
                   // Payments 
                    var TotalRow = $("#row_count_pay").val();
                    //var crRow = parseInt(TotalRow) - 1;
                   for (var A = 1; A <= crRow; A++) {
                       var id = 'row_pay'+A;
                        document.getElementById(id).remove();
                   }
                // Expenses
                    var TotalRow = $("#row_count_exp").val();
                    //var crRow = parseInt(TotalRow) - 1;
                   for (var A = 1; A <= crRow; A++) {
                       var id = 'row_exp'+A;
                        document.getElementById(id).remove();
                   }
                    $("#ItemCount").val('0');
                    $("#row_count").val('0');
                    $("#row_count_pay").val('0');
                    $("#row_count_exp").val('0');
                    
                    $('.saveBtn').show();
                    $('.saveBtn2').show();
                    $('.updateBtn').hide();
                    $('.updateBtn2').hide();
                    $('.printBtn').hide();
                }else{
                    let VRtnDetails = response.ChallanDetails;
                    let CratesDetails = response.CratesDetails;
                    
                    //alert(response.ReturnID);
                    $('#vehicle_return_id').val(VRtnDetails.ReturnID);
                    $('#challan_n').val(VRtnDetails.ChallanID);
                    $('#route_code').val(VRtnDetails.RouteID);
                    $('#route_name').val(VRtnDetails.name);
                    $('#routekm').val(VRtnDetails.KM);
                    $('#vehicle_number').val(VRtnDetails.VehicleID);
                    $('#driver_id').val(VRtnDetails.DriverID);
                    $('#challan_n').attr('readonly', true);
                    if(VRtnDetails.driver_fn !== null){
                        if(VRtnDetails.driver_ln !== null){
                            var DName = VRtnDetails.driver_fn +' '+VRtnDetails.driver_ln;
                        }else{
                            var DName = VRtnDetails.driver_fn;
                        }
                    }else{
                        var DName = '';
                    }
                    $('#driver_name').val(DName);
                    if(VRtnDetails.loader_fn !== null){
                        if(VRtnDetails.loader_ln !== null){
                            var LName = VRtnDetails.loader_fn +' '+VRtnDetails.loader_ln;
                        }else{
                            var LName = VRtnDetails.loader_fn;
                        }
                    }else{
                        var LName = '';
                    }
                    $('#loder_id').val(VRtnDetails.LoaderID);
                    $('#loder_name').val(LName);
                    $('#salesman_id').val(VRtnDetails.SalesmanID);
                    if(VRtnDetails.Salesman_fn !== null){
                        if(VRtnDetails.Salesman_ln !== null){
                            var SName = VRtnDetails.Salesman_fn +' '+VRtnDetails.Salesman_ln;
                        }else{
                            var SName = VRtnDetails.Salesman_fn;
                        }
                        
                    }else{
                        var SName = '';
                    }
                    $('#salesman_name').val(SName);
                    
                    if(VRtnDetails.UserID_fn !== null){
                        if(VRtnDetails.UserID_ln !== null){
                            var UName = VRtnDetails.UserID_fn +' '+VRtnDetails.UserID_ln;
                        }else{
                            var UName = VRtnDetails.UserID_fn;
                        }
                    }else{
                        var UName = '';
                    }
                    $('#UserIDName').val(UName);
                    
                    $('#vehicle_capc').val(VRtnDetails.VehicleCapacity);
                    $('#challan_crates').val(VRtnDetails.Crates);
                    $('#refund_crates').val(VRtnDetails.return_crates);
                    var ChlDate = VRtnDetails.Transdate.substring(0, 10);
                    var ChldateNew = ChlDate.split("-").reverse().join("/");
                    $('#to_date').val(ChldateNew);
                    
                    var VRtnDate = VRtnDetails.returnTransdate.substring(0, 10);
                    var VRtndateNew = VRtnDate.split("-").reverse().join("/");
                    $('#from_date').val(VRtndateNew);
                    var html = '';
                    const printAccount = [];
                    var col = 0;
                    for (var index = 0; index < CratesDetails.length; index++) {
                        var col = index + 1;
                        printAccount.push(CratesDetails[index].act_id);
                        html += '<tr class="accounts" id="row'+col+'">';
                        html += '<td style="width: 125px;"><input type="text" readonly name="AccountID'+col+'" style="width: 125px;" id="AccountID'+col+'" value="'+ CratesDetails[index].act_id+'"></td>';
                	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].company+'</td>';
                	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].address+'</td>';
                	    if(CratesDetails[index].OQty == null || CratesDetails[index].OQty == 'undefined'){
                	        var OpeningQty = 0;
                	    }else{
                	        var OpeningQty = CratesDetails[index].OQty;
                	    }
                	    if(CratesDetails[index].VRtnCrates){
                	        var VRtnCrates = CratesDetails[index].VRtnCrates;
                	    }else{
                	         var VRtnCrates = 0;
                	    }
                	    html += '<td style="padding:1px 5px !important;">'+OpeningQty+'</td>';
                	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].CHLCrates+'</td>';
                	    var befor_crate_bal = parseInt(OpeningQty);
                	    html += '<td class="rtnqty" style="width: 80px;"><input type="text" name="rtncrates'+col+'" id="rtncrates'+col+'" onblur="calculate_balcrates();" value="'+VRtnCrates+'" style="width: 80px;text-align:right;" class="rtncrates"><input type="hidden" name="balcrates'+col+'" id="balcrates'+col+'" value="'+befor_crate_bal+'"><input type="hidden" name="colno" id="colno" value="'+col+'"></td>';
                	    html += '<td id="balCrates" style="padding:1px 5px !important;text-align:right;"><span>'+CratesDetails[index].balance_crates+'</span></td>';
                        html += '</tr>';
                        
                    }
                    
                    $("#row_count").val(col);
                    $('#crate_details_tbl tbody').append(html);
                    
                    let Itemlist = response.SaleRtnDetails.itemhead;
                    let Orderdata = response.SaleRtnDetails.Orderdata;
                    let ItemOrderData = response.SaleRtnDetails.ItemOrderData;
                    let ItemRtnData = response.SaleRtnDetails.ItemRtnData;
                    
                    var html2 = '';
                    html2 += '<table class="table table-striped table-bordered stock_details_tbl fixed_header1" id="stock_details_tbl" width="100%">';
                    html2 += '<thead id="thead">';
                    html2 += '<tr>';
                    html2 += '<th align="center" >AccountID</th>';
                    html2 += '<th align="center" >AccoutName</th>';
                    html2 += '<th align="center" >SaleID</th>';
                    html2 += '<th align="center">RtnAMT</th>';
                    html2 += '<th align="center" >CGST</th>';
                    html2 += '<th align="center" >SGST</th>';
                    html2 += '<th align="center" >IGST</th>';
                    for (var index2 = 0; index2 < Itemlist.length; index2++) {
                        //html2 += '<th align="center" width="10%">'+Itemlist[index2]+'</th>';
                        html2 += '<th style="width: 150px;text-align: center">'+Itemlist[index2]+'</th>';
                    }
                    
                    html2 += '</tr>';
                    html2 += '</thead>';
                    html2 += '<tbody id="tbody">';
                    var j= 1;
                    var itemCount = 0;
                    var TotalRtnAmt = 0;
                    $.each(Orderdata, function (column1, value1) {
                        printAccount.push(value1["AccountID"]);
                        html2 += '<tr class="accounts" id="row'+j+'">';
                        html2 += '<td style="padding:1px 5px !important;">'+value1["AccountID"]+'<input type="hidden" name="AccountID_SRtn'+j+'" id="AccountID_SRtn'+j+'" value="'+value1["AccountID"]+'"></td>';
                        html2 += '<td style="padding:1px 5px !important;">'+value1["company"]+'</td>';
                        html2 += '<td style="padding:1px 5px !important;">'+value1["SalesID"]+'</td>';
                    var totaRtmAmt = 0;
                    var total_igst = 0;
                    var total_cgst = 0;
                    var total_sgst = 0;
                    
                            $.each(ItemRtnData, function (column3, value3) {
                                if(value1["SalesID"] == value3["TransID"]){
                                    totaRtmAmt = parseFloat(totaRtmAmt) + parseFloat(value3['NetChallanAmt']);
                                    total_igst = parseFloat(total_igst) + parseFloat(value3['igstamt']);
                                    total_cgst = parseFloat(total_cgst) + parseFloat(value3['cgstamt']);
                                    total_sgst = parseFloat(total_sgst) + parseFloat(value3['sgstamt']);
                                }
                            })
                        TotalRtnAmt += parseFloat(totaRtmAmt);
                        html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="RtnAmt_val'+j+'" id="RtnAmt_val'+j+'" value="'+parseFloat(totaRtmAmt).toFixed(2)+'"><span id="RtnAmt">'+parseFloat(totaRtmAmt).toFixed(2)+'</span></td>';
                        html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="cgst_val'+j+'" id="cgst_val'+j+'" value="'+parseFloat(total_cgst).toFixed(2)+'"><span id="cgst">'+parseFloat(total_cgst).toFixed(2)+'</span></td>';
                        html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="sgst_val'+j+'" id="sgst_val'+j+'" value="'+parseFloat(total_sgst).toFixed(2)+'"><span id="sgst">'+parseFloat(total_sgst).toFixed(2)+'</span></td>';
                        html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="igst_val'+j+'" id="igst_val'+j+'" value="'+parseFloat(total_igst).toFixed(2)+'"><span id="igst">'+parseFloat(total_igst).toFixed(2)+'</span></td>';
                    
                        $.each(Itemlist, function (column4, value4) {
                            var match = 0;
                            var rate = 0;
                            var gst = 0;
                            var BilledQty = 0;
                            var ORDRtnBilledQty = '';
                            var ItemID = 0;
                            var TransID = 0;
                            var AccountID = 0;
                            var PackQty = 0;
                            var state = '';
                            $.each(ItemOrderData, function (column5, value5) {
                                if(value4 == value5["ItemID"] && value1["SalesID"] == value5["TransID"]){
                                    itemCount = itemCount + 1;
                                    match = 1;
                                    rate = value5["BasicRate"];
                                    BilledQty = value5["BilledQty"];
                                    ItemID = value5["ItemID"];
                                    TransID = value5["TransID"];
                                    AccountID = value5["AccountID"];
                                    PackQty = value5["CaseQty"];
                                    if(value5["igst"] == "0.00" || value5["igst"] == null){
                                        gst = value5["cgst"] * 2;
                                        state = "UP"
                                    }else{
                                        gst = value5["igst"];
                                        state = "UP1"
                                    }
                                }
                            })
                            $.each(ItemRtnData, function (column6, value6) {
                                if(value4 == value6['ItemID'] && value1["SalesID"] == value6["TransID"]){
                                    ORDRtnBilledQty = value6['BilledQty'];
                                }
                            })
                            if(match == "1"){
                            html2 += '<td style="width: 100px;"><input type="hidden" name="PackQty_val'+itemCount+'" id="PackQty_val'+itemCount+'" value="'+PackQty+'"><input type="hidden" name="AccountID_val'+itemCount+'" id="AccountID_val'+itemCount+'" value="'+AccountID+'"><input type="hidden" name="ItemID_val'+itemCount+'" id="ItemID_val'+itemCount+'" value="'+ItemID+'"><input type="hidden" name="TransID_val'+itemCount+'" id="TransID_val'+itemCount+'" value="'+TransID+'"><input type="hidden" name="rate_val'+itemCount+'" id="rate_val'+itemCount+'" value="'+rate+'"><input type="hidden" name="gst_val'+itemCount+'" id="gst_val'+itemCount+'" value="'+gst+'"><input type="hidden" name="state_val'+itemCount+'" id="state_val'+itemCount+'" value="'+value1["state"]+'"><input type="hidden" name="BilledQty" id="BilledQty" value="'+BilledQty+'"><input type="hidden" name="state" id="state" value="'+value1["state"]+'"><input type="hidden" name="rate" id="rate" value="'+rate+'"><input type="hidden" name="gst" id="gst" value="'+gst+'"><input type="text" name="rtnqty'+itemCount+'" id="rtnqty'+itemCount+'" onblur="calculate_rtnqty();" onkeypress="return isNumber(event)" onfocus="myFunction(this)" style="width: 60px;text-align: right;" value="'+ORDRtnBilledQty+'" ></td>';
                            }else{
                                html2 += '<td style="width: 150px;padding:1px 5px !important;text-align: right;background-color: #aea5a599;"></td>';
                            }
                        })
                        html2 += '</tr>';
                        j++;
                    });
                    html2 += '</tbody>';
                    html2 += '</table>';
                    $('#fixed_header1').html(html2);
                    $("#row_count_frRtn").val(j-1);
                    $("#ItemCount").val(index2);
                    $('#fresh_ret_amt1').val(parseFloat(TotalRtnAmt).toFixed(2));
                    $('#fresh_ret_amt').val(parseFloat(TotalRtnAmt).toFixed(2));
                    // Payment Table
                    
                    let PaymentsDetails = response.PaymentsDetails;
                    var ii = 1;
                    var html3 = '';
                    var TotalPayAmt = 0;
                    $.each(PaymentsDetails, function (column7, value7) {
                        if(value7['payment_recipt_Amount'] !== null ){
                            printAccount.push(value7['Aid']);
                            html3 += '<tr class="accounts" id="row_pay'+ii+'">';
                            html3 += '<td style="width: 125px;" id="AccountIDTD_pay"><input type="text" name="AccountID_pay'+ii+'" style="width: 125px;" id="AccountID_pay'+ii+'" value="'+value7['Aid']+'"></td>';
                            html3 += '<td style="padding:1px 5px !important;">'+value7['company']+'</td>';
                            html3 += '<td style="padding:1px 5px !important;">'+value7['address']+'</td>';
                            html3 += '<td class="rcptAmts" style="width: 80px;"><input type="text" name="receiptamt'+ii+'" id="receiptamt'+ii+'"  onblur="calculate_payment();" value="'+value7['payment_recipt_Amount']+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
                            html3 += '</tr>';
                            ii++; 
                            TotalPayAmt += parseFloat(value7['payment_recipt_Amount']);
                        }
                    })
                    $('#case_depo1').val(parseFloat(TotalPayAmt).toFixed(2));
                    $('#case_depo').val(parseFloat(TotalPayAmt).toFixed(2));
                    $('#payment_details_tbl tbody').append(html3);
                    $("#row_count_pay").val(ii-1);
                    
                    // Print Table for collection
                    printhtml = '';
                    var srno = 1;
                    var FrtnSum = 0;
                    var AmtSum = 0;
                    var CratesSum = 0;
                    let uniqueChars = [...new Set(printAccount)];
                    $.each(uniqueChars, function (column88, value88) {
                        printhtml += '<tr>';
                        printhtml += '<td align="center">'+srno+'</td>';
                        var partyName = '';
                        var Frtn = 0;
                        var Amt = 0;
                        var Crates = 0;
                        for (var index = 0; index < CratesDetails.length; index++) {
                            if(value88 == CratesDetails[index].act_id){
                                partyName = CratesDetails[index].company;
                                if(CratesDetails[index].VRtnCrates){
                        	        var VRtnCrates = CratesDetails[index].VRtnCrates;
                        	    }else{
                        	         var VRtnCrates = 0;
                        	    }
                        	    Crates += parseFloat(VRtnCrates);
                            }
                        }
                        $.each(Orderdata, function (column1, value1) {
                            if(value88 == value1["AccountID"]){
                                partyName = value1["company"];
                                var totaRtmAmt = 0;
                                $.each(ItemRtnData, function (column3, value3) {
                                    if(value1["SalesID"] == value3["TransID"]){
                                        totaRtmAmt = parseFloat(totaRtmAmt) + parseFloat(value3['NetChallanAmt']);
                                    }
                                })
                                Frtn = parseFloat(Frtn) + parseFloat(totaRtmAmt);
                            }
                        })
                        $.each(PaymentsDetails, function (column7, value7) {
                            if(value88 == value7['Aid']){
                                partyName = value7['company'];
                                Amt = parseFloat(Amt) + parseFloat(value7['payment_recipt_Amount']);
                            }
                        })
                        
                        
                        printhtml += '<td align="left">'+partyName+'</td>';
                        printhtml += '<td align="right">'+Frtn.toFixed(2)+'</td>';
                        FrtnSum = parseFloat(FrtnSum) + parseFloat(Frtn);
                        printhtml += '<td align="right">'+Amt.toFixed(2)+'</td>';
                        AmtSum = parseFloat(AmtSum) + parseFloat(Amt);
                        printhtml += '<td align="right">'+Crates+'</td>';
                        CratesSum = parseFloat(CratesSum) + parseFloat(Crates);
                        printhtml += '</tr>';
                        srno++;
                    })
                    printhtml += '<tr>';
                    printhtml += '<td align="left"></td>';
                    printhtml += '<td align="left">Total</td>';
                    printhtml += '<td align="right">'+FrtnSum.toFixed(2)+'</td>';
                    printhtml += '<td align="right">'+AmtSum.toFixed(2)+'</td>';
                    printhtml += '<td align="right">'+CratesSum.toFixed(2)+'</td>';
                    printhtml += '</tr>';
                    $('#print_tbl1 tbody').append(printhtml);
                    // Expenses table
                    let ExpenseDetails = response.ExpenseDetails;
                    var iii = 1;
                    var html4 = '';
                    var TotalExpAmt = 0;
                    var printhtml2 = '';
                    $.each(ExpenseDetails, function (column8, value8) {
                        if(value8['expense_Amount'] == null ){
                            
                        }else{
                            html4 += '<tr class="accounts" id="row_exp'+iii+'">';
                            html4 += '<td style="width: 125px;" id="AccountIDTD_exp'+iii+'"><input type="text" name="AccountID_exp'+iii+'" style="width: 125px;" id="AccountID_exp'+iii+'" value="'+value8['Aid']+'"></td>';
                            if(value8['firstname'] == null){
                                var Name = value8['company'];
                                if(value8['address'] == null){
                                    var Address = '';
                                }else{
                                    var Address = value8['address'];
                                }
                            }else{
                                var Name = value8['firstname']+' '+value8['lastname'];
                                if(value8['address'] == null){
                                    var Address = '';
                                }else{
                                    var Address = value8['current_address'];
                                }
                            }
                            html4 += '<td style="padding:1px 5px !important;">'+Name+'</td>';
                            html4 += '<td style="padding:1px 5px !important;">'+Address+'</td>';
                            html4 += '<td class="expamts" style="width: 80px;"><input type="text" name="expamt'+iii+'" id="expamt'+iii+'" onblur="calculate_expense();"  value="'+value8['expense_Amount']+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
                            html4 += '</tr>';
                            
                            TotalExpAmt += parseFloat(value8['expense_Amount']);
                            
                            printhtml2 += '<tr>';
                            printhtml2 += '<td align="center">'+iii+'</td>';
                            printhtml2 += '<td>'+Name+'</td>';
                            printhtml2 += '<td align="right">'+value8['expense_Amount']+'</td>';
                            printhtml2 += '</tr>';
                            iii++;
                        }
                    })
                    printhtml2 += '<tr>';
                    printhtml2 += '<td align="center"></td>';
                    printhtml2 += '<td align="right">Total</td>';
                    printhtml2 += '<td align="right">'+TotalExpAmt.toFixed(2)+'</td>';
                    printhtml2 += '</tr>';
                    $('#print_tbl2 tbody').append(printhtml2);
                    $('#total_expense1').val(parseFloat(TotalExpAmt).toFixed(2));
                    $('#total_expense').val(parseFloat(TotalExpAmt).toFixed(2));
                    $('#expense_details_tbl tbody').append(html4);
                    $("#row_count_exp").val(iii-1);
                    $('#transfer-modal_return_list').modal('hide');
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                        $('.printBtn').show();
                }
                
            }
        });
    });
    
    $("#search_data_vehicle_return").click(function(){ 
        var from_date = $('#from_date2').val();
        var to_date = $('#to_date2').val();
     
        $.ajax({
          url:"<?php echo admin_url(); ?>VehRtn/vehicle_return_model",
          dataType:"html",
          method:"POST",
          data:{from_date:from_date,to_date:to_date},
          beforeSend: function () {
            
            $('#searchh3').css('display','block');
            $('.table_vehicle_return tbody').css('display','none');
            
         },
          complete: function () {
                                
            $('.table_vehicle_return tbody').css('display','');
            $('#searchh3').css('display','none');
         },
          success:function(data){
             $('#table_vehicle_return tbody').html(data);
                $('.get_VehicleRtnID').on('click',function(){ 
                    VRtnID = $(this).attr("data-id");
                    $('#transfer-modal_return_list').modal('hide');
                    $.ajax({
                      url:"<?php echo admin_url(); ?>VehRtn/GetDetail",
                      dataType:"JSON",
                      method:"POST",
                      data:{VRtnID:VRtnID},
                    beforeSend: function () {
                        $('#searchh11').css('display','block');
                        $('#searchh11').css('color','blue');
                    },
                    complete: function () {
                        $('#searchh11').css('display','none');
                    },
                    success:function(response){
                        
                        let VRtnDetails = response.ChallanDetails;
                        let CratesDetails = response.CratesDetails;
                        //alert(response.ReturnID);
                        $('#vehicle_return_id').val(VRtnDetails.ReturnID);
                        $('#challan_n').val(VRtnDetails.ChallanID);
                        $('#route_code').val(VRtnDetails.RouteID);
                        $('#route_name').val(VRtnDetails.name);
                        $('#routekm').val(VRtnDetails.KM);
                        $('#vehicle_number').val(VRtnDetails.VehicleID);
                        $('#driver_id').val(VRtnDetails.DriverID);
                        if(VRtnDetails.driver_fn !== null){
                            var DName = VRtnDetails.driver_fn +' '+VRtnDetails.driver_ln;
                        }else{
                            var DName = '';
                        }
                        $('#driver_name').val(DName);
                        if(VRtnDetails.loader_fn !== null){
                            var LName = VRtnDetails.loader_fn +' '+VRtnDetails.loader_ln;
                        }else{
                            var LName = '';
                        }
                        $('#loder_id').val(VRtnDetails.LoaderID);
                        $('#loder_name').val(LName);
                        $('#salesman_id').val(VRtnDetails.SalesmanID);
                        if(VRtnDetails.Salesman_fn !== null){
                            var SName = VRtnDetails.Salesman_fn +' '+VRtnDetails.Salesman_ln;
                        }else{
                            var SName = '';
                        }
                        $('#salesman_name').val(SName);
                        $('#vehicle_capc').val(VRtnDetails.VehicleCapacity);
                        $('#challan_crates').val(VRtnDetails.Crates);
                        $('#refund_crates').val(VRtnDetails.return_crates);
                        var ChlDate = VRtnDetails.Transdate.substring(0, 10);
                        var ChldateNew = ChlDate.split("-").reverse().join("/");
                        $('#to_date').val(ChldateNew);
                        
                        var VRtnDate = VRtnDetails.returnTransdate.substring(0, 10);
                        var VRtndateNew = VRtnDate.split("-").reverse().join("/");
                        $('#from_date').val(VRtndateNew);
                        $('#challan_n').attr('readonly', true);
                        var html = '';
                        const printAccount = [];
                        for (var index = 0; index < CratesDetails.length; index++) {
                            var col = index + 1;
                            printAccount.push(CratesDetails[index].act_id);
                            html += '<tr class="accounts" id="row'+col+'">';
                            html += '<td style="width: 125px;"><input type="text" readonly name="AccountID'+col+'" style="width: 125px;" id="AccountID'+col+'" value="'+ CratesDetails[index].act_id+'"></td>';
                    	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].company+'</td>';
                    	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].address+'</td>';
                    	    if(CratesDetails[index].Qty == null || CratesDetails[index].Qty == 'undefined'){
                    	        var OQty = 0;
                    	    }else{
                    	        var OQty = CratesDetails[index].Qty;
                    	    }
                    	    if(CratesDetails[index].VRtnCrates){
                	        var VRtnCrates = CratesDetails[index].VRtnCrates;
                	    }else{
                	         var VRtnCrates = 0;
                	    }
                    	    html += '<td style="padding:1px 5px !important;">'+OQty+'</td>';
                    	    html += '<td style="padding:1px 5px !important;">'+CratesDetails[index].CHLCrates+'</td>';
                    	    var befor_crate_bal = parseInt(CratesDetails[index].balance_crates) + parseInt(VRtnCrates);
                    	    html += '<td class="rtnqty" style="width: 80px;"><input type="text" name="rtncrates'+col+'" id="rtncrates'+col+'" onblur="calculate_balcrates();" value="'+OQty+'" style="width: 80px;text-align:right;" class="rtncrates"><input type="hidden" name="balcrates'+col+'" id="balcrates'+col+'" value="'+befor_crate_bal+'"><input type="hidden" name="colno" id="colno" value="'+col+'"></td>';
                    	    html += '<td id="balCrates" style="padding:1px 5px !important;text-align:right;"><span>'+CratesDetails[index].balance_crates+'</span></td>';
                            html += '</tr>';
                        }
                        
                        $("#row_count").val(col);
                        $('#crate_details_tbl tbody').append(html);
                        
                        let Itemlist = response.SaleRtnDetails.itemhead;
                        let Orderdata = response.SaleRtnDetails.Orderdata;
                        let ItemOrderData = response.SaleRtnDetails.ItemOrderData;
                        let ItemRtnData = response.SaleRtnDetails.ItemRtnData;
                        
                        var html2 = '';
                        html2 += '<table class="table table-striped table-bordered stock_details_tbl fixed_header1" id="stock_details_tbl" width="100%">';
                        html2 += '<thead id="thead">';
                        html2 += '<tr>';
                        html2 += '<th align="center" >AccountID</th>';
                        html2 += '<th align="center" >AccoutName</th>';
                        html2 += '<th align="center" >SaleID</th>';
                        html2 += '<th align="center">RtnAMT</th>';
                        html2 += '<th align="center" >CGST</th>';
                        html2 += '<th align="center" >SGST</th>';
                        html2 += '<th align="center" >IGST</th>';
                        for (var index2 = 0; index2 < Itemlist.length; index2++) {
                            //html2 += '<th align="center" width="10%">'+Itemlist[index2]+'</th>';
                            html2 += '<th style="width: 150px;text-align: center">'+Itemlist[index2]+'</th>';
                        }
                        
                        html2 += '</tr>';
                        html2 += '</thead>';
                        html2 += '<tbody id="tbody">';
                        var j= 1;
                        var itemCount = 0;
                        var TotalRtnAmt = 0;
                        $.each(Orderdata, function (column1, value1) {
                            printAccount.push(value1["AccountID"]);
                            html2 += '<tr class="accounts" id="row'+j+'">';
                            html2 += '<td style="padding:1px 5px !important;">'+value1["AccountID"]+'<input type="hidden" name="AccountID_SRtn'+j+'" id="AccountID_SRtn'+j+'" value="'+value1["AccountID"]+'"></td>';
                            html2 += '<td style="padding:1px 5px !important;">'+value1["company"]+'</td>';
                            html2 += '<td style="padding:1px 5px !important;">'+value1["SalesID"]+'</td>';
                        var totaRtmAmt = 0;
                        var total_igst = 0;
                        var total_cgst = 0;
                        var total_sgst = 0;
                        
                                $.each(ItemRtnData, function (column3, value3) {
                                    if(value1["SalesID"] == value3["TransID"]){
                                        totaRtmAmt = parseFloat(totaRtmAmt) + parseFloat(value3['ChallanAmt']);
                                        total_igst = parseFloat(total_igst) + parseFloat(value3['igstamt']);
                                        total_cgst = parseFloat(total_cgst) + parseFloat(value3['cgstamt']);
                                        total_sgst = parseFloat(total_sgst) + parseFloat(value3['sgstamt']);
                                    }
                                })
                            TotalRtnAmt += parseFloat(totaRtmAmt);
                            html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="RtnAmt_val'+j+'" id="RtnAmt_val'+j+'" value="'+parseFloat(totaRtmAmt).toFixed(2)+'"><span id="RtnAmt">'+parseFloat(totaRtmAmt).toFixed(2)+'</span></td>';
                            html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="cgst_val'+j+'" id="cgst_val'+j+'" value="'+parseFloat(total_cgst).toFixed(2)+'"><span id="cgst">'+parseFloat(total_cgst).toFixed(2)+'</span></td>';
                            html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="sgst_val'+j+'" id="sgst_val'+j+'" value="'+parseFloat(total_sgst).toFixed(2)+'"><span id="sgst">'+parseFloat(total_sgst).toFixed(2)+'</span></td>';
                            html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="igst_val'+j+'" id="igst_val'+j+'" value="'+parseFloat(total_igst).toFixed(2)+'"><span id="igst">'+parseFloat(total_igst).toFixed(2)+'</span></td>';
                        
                            $.each(Itemlist, function (column4, value4) {
                                var match = 0;
                                var rate = 0;
                                var gst = 0;
                                var BilledQty = 0;
                                var ORDRtnBilledQty = '';
                                var ItemID = 0;
                                var TransID = 0;
                                var AccountID = 0;
                                var PackQty = 0;
                                var state = '';
                                $.each(ItemOrderData, function (column5, value5) {
                                    if(value4 == value5["ItemID"] && value1["SalesID"] == value5["TransID"]){
                                        itemCount = itemCount + 1;
                                        match = 1;
                                        rate = value5["BasicRate"];
                                        BilledQty = value5["BilledQty"];
                                        ItemID = value5["ItemID"];
                                        TransID = value5["TransID"];
                                        AccountID = value5["AccountID"];
                                        PackQty = value5["CaseQty"];
                                        if(value5["igst"] == "0.00" || value5["igst"] == null){
                                            gst = value5["cgst"] * 2;
                                            state = "UP"
                                        }else{
                                            gst = value5["igst"];
                                            state = "UP1"
                                        }
                                    }
                                })
                                $.each(ItemRtnData, function (column6, value6) {
                                    if(value4 == value6['ItemID'] && value1["SalesID"] == value6["TransID"]){
                                        ORDRtnBilledQty = value6['BilledQty'];
                                    }
                                })
                                if(match == "1"){
                                html2 += '<td style="width: 100px;"><input type="hidden" name="PackQty_val'+itemCount+'" id="PackQty_val'+itemCount+'" value="'+PackQty+'"><input type="hidden" name="AccountID_val'+itemCount+'" id="AccountID_val'+itemCount+'" value="'+AccountID+'"><input type="hidden" name="ItemID_val'+itemCount+'" id="ItemID_val'+itemCount+'" value="'+ItemID+'"><input type="hidden" name="TransID_val'+itemCount+'" id="TransID_val'+itemCount+'" value="'+TransID+'"><input type="hidden" name="rate_val'+itemCount+'" id="rate_val'+itemCount+'" value="'+rate+'"><input type="hidden" name="gst_val'+itemCount+'" id="gst_val'+itemCount+'" value="'+gst+'"><input type="hidden" name="state_val'+itemCount+'" id="state_val'+itemCount+'" value="'+value1["state"]+'"><input type="hidden" name="BilledQty" id="BilledQty" value="'+BilledQty+'"><input type="hidden" name="state" id="state" value="'+value1["state"]+'"><input type="hidden" name="rate" id="rate" value="'+rate+'"><input type="hidden" name="gst" id="gst" value="'+gst+'"><input type="text" name="rtnqty'+itemCount+'" id="rtnqty'+itemCount+'" onblur="calculate_rtnqty();" onkeypress="return isNumber(event)" onfocus="myFunction(this)" style="width: 60px;text-align: right;" value="'+ORDRtnBilledQty+'" ></td>';
                                }else{
                                    html2 += '<td style="width: 150px;padding:1px 5px !important;text-align: right;background-color: #aea5a599;"></td>';
                                }
                            })
                            html2 += '</tr>';
                            j++;
                        });
                        html2 += '</tbody>';
                        html2 += '</table>';
                        $('#fixed_header1').html(html2);
                        $("#row_count_frRtn").val(j-1);
                        $("#ItemCount").val(index2);
                        $('#fresh_ret_amt1').val(parseFloat(TotalRtnAmt).toFixed(2));
                        $('#fresh_ret_amt').val(parseFloat(TotalRtnAmt).toFixed(2));
                        // Payment Table
                        
                        let PaymentsDetails = response.PaymentsDetails;
                        var ii = 1;
                        var html3 = '';
                        var TotalPayAmt = 0;
                        $.each(PaymentsDetails, function (column7, value7) {
                            if(value7['payment_recipt_Amount'] !== null ){
                                printAccount.push(value7['Aid']);
                                html3 += '<tr class="accounts" id="row_pay'+ii+'">';
                                html3 += '<td style="width: 125px;" id="AccountIDTD_pay"><input type="text" name="AccountID_pay'+ii+'" style="width: 125px;" id="AccountID_pay'+ii+'" value="'+value7['Aid']+'"></td>';
                                html3 += '<td style="padding:1px 5px !important;">'+value7['company']+'</td>';
                                html3 += '<td style="padding:1px 5px !important;">'+value7['address']+'</td>';
                                html3 += '<td class="rcptAmts" style="width: 80px;"><input type="text" name="receiptamt'+ii+'" id="receiptamt'+ii+'"  onblur="calculate_payment();" value="'+value7['payment_recipt_Amount']+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
                                html3 += '</tr>';
                                ii++; 
                                TotalPayAmt += parseFloat(value7['payment_recipt_Amount']);
                            }
                        })
                        $('#case_depo1').val(parseFloat(TotalPayAmt).toFixed(2));
                        $('#case_depo').val(parseFloat(TotalPayAmt).toFixed(2));
                        $('#payment_details_tbl tbody').append(html3);
                        $("#row_count_pay").val(ii-1);
                        
                        // Print Table for collection
                    printhtml = '';
                    var srno = 1;
                    var FrtnSum = 0;
                    var AmtSum = 0;
                    var CratesSum = 0;
                    let uniqueChars = [...new Set(printAccount)];
                    $.each(uniqueChars, function (column88, value88) {
                        printhtml += '<tr>';
                        printhtml += '<td align="center">'+srno+'</td>';
                        var partyName = '';
                        var Frtn = 0;
                        var Amt = 0;
                        var Crates = 0;
                        for (var index = 0; index < CratesDetails.length; index++) {
                            if(value88 == CratesDetails[index].act_id){
                                partyName = CratesDetails[index].company;
                                if(CratesDetails[index].VRtnCrates){
                        	        var VRtnCrates = CratesDetails[index].VRtnCrates;
                        	    }else{
                        	         var VRtnCrates = 0;
                        	    }
                        	    Crates += parseFloat(VRtnCrates);
                            }
                        }
                        $.each(Orderdata, function (column1, value1) {
                            if(value88 == value1["AccountID"]){
                                partyName = value1["company"];
                                var totaRtmAmt = 0;
                                $.each(ItemRtnData, function (column3, value3) {
                                    if(value1["SalesID"] == value3["TransID"]){
                                        totaRtmAmt = parseFloat(totaRtmAmt) + parseFloat(value3['NetChallanAmt']);
                                    }
                                })
                                Frtn = parseFloat(Frtn) + parseFloat(totaRtmAmt);
                            }
                        })
                        $.each(PaymentsDetails, function (column7, value7) {
                            if(value88 == value7['Aid']){
                                partyName = value7['company'];
                                Amt = parseFloat(Amt) + parseFloat(value7['payment_recipt_Amount']);
                            }
                        })
                        
                        
                        printhtml += '<td align="left">'+partyName+'</td>';
                        printhtml += '<td align="right">'+Frtn.toFixed(2)+'</td>';
                        FrtnSum = parseFloat(FrtnSum) + parseFloat(Frtn);
                        printhtml += '<td align="right">'+Amt.toFixed(2)+'</td>';
                        AmtSum = parseFloat(AmtSum) + parseFloat(Amt);
                        printhtml += '<td align="right">'+Crates+'</td>';
                        CratesSum = parseFloat(CratesSum) + parseFloat(Crates);
                        printhtml += '</tr>';
                        srno++;
                    })
                    printhtml += '<tr>';
                    printhtml += '<td align="left"></td>';
                    printhtml += '<td align="left">Total</td>';
                    printhtml += '<td align="right">'+FrtnSum.toFixed(2)+'</td>';
                    printhtml += '<td align="right">'+AmtSum.toFixed(2)+'</td>';
                    printhtml += '<td align="right">'+CratesSum.toFixed(2)+'</td>';
                    printhtml += '</tr>';
                    $('#print_tbl1 tbody').append(printhtml);
                    // Expenses table
                    let ExpenseDetails = response.ExpenseDetails;
                    var iii = 1;
                    var html4 = '';
                    var TotalExpAmt = 0;
                    var printhtml2 = '';
                    $.each(ExpenseDetails, function (column8, value8) {
                        if(value8['expense_Amount'] == null ){
                            
                        }else{
                            html4 += '<tr class="accounts" id="row_exp'+iii+'">';
                            html4 += '<td style="width: 125px;" id="AccountIDTD_exp'+iii+'"><input type="text" name="AccountID_exp'+iii+'" style="width: 125px;" id="AccountID_exp'+iii+'" value="'+value8['Aid']+'"></td>';
                            if(value8['firstname'] == null){
                                var Name = value8['company'];
                                if(value8['address'] == null){
                                    var Address = '';
                                }else{
                                    var Address = value8['address'];
                                }
                            }else{
                                var Name = value8['firstname']+' '+value8['lastname'];
                                if(value8['address'] == null){
                                    var Address = '';
                                }else{
                                    var Address = value8['current_address'];
                                }
                            }
                            html4 += '<td style="padding:1px 5px !important;">'+Name+'</td>';
                            html4 += '<td style="padding:1px 5px !important;">'+Address+'</td>';
                            html4 += '<td class="expamts" style="width: 80px;"><input type="text" name="expamt'+iii+'" id="expamt'+iii+'" onblur="calculate_expense();"  value="'+value8['expense_Amount']+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
                            html4 += '</tr>';
                            
                            TotalExpAmt += parseFloat(value8['expense_Amount']);
                            
                            printhtml2 += '<tr>';
                            printhtml2 += '<td align="center">'+iii+'</td>';
                            printhtml2 += '<td>'+Name+'</td>';
                            printhtml2 += '<td align="right">'+value8['expense_Amount']+'</td>';
                            printhtml2 += '</tr>';
                            iii++;
                        }
                    })
                    printhtml2 += '<tr>';
                    printhtml2 += '<td align="center"></td>';
                    printhtml2 += '<td align="right">Total</td>';
                    printhtml2 += '<td align="right">'+TotalExpAmt.toFixed(2)+'</td>';
                    printhtml2 += '</tr>';
                    $('#total_expense1').val(parseFloat(TotalExpAmt).toFixed(2));
                        $('#total_expense').val(parseFloat(TotalExpAmt).toFixed(2));
                        $('#expense_details_tbl tbody').append(html4);
                        $("#row_count_exp").val(iii-1);
                    $('#print_tbl2 tbody').append(printhtml2);
                        
                            $('.saveBtn').hide();
                            $('.updateBtn').show();
                            $('.saveBtn2').hide();
                            $('.updateBtn2').show();
                            $('.printBtn').show();
                    }
                    });
                });
            }
        });
    });
    
    // Challan PopUp Open 
    $("#challan_n").dblclick(function(){
      $('#transfer-modal').modal('show');
      $('#transfer-modal').on('shown.bs.modal', function () {
              $('#myInput1').focus();
        })
    })
    
    // Challan List search
    $("#search_data").click(function(){ 
        var from_date = $('#from_date1').val();
        var to_date = $('#to_date1').val();
        $.ajax({
          url:"<?php echo admin_url(); ?>VehRtn/challan_details_model",
          dataType:"html",
          method:"POST",
          data:{from_date:from_date,to_date:to_date},
        beforeSend: function () {
            $('#searchh2').css('display','block');
            $('.table_adj_report tbody').css('display','none');
        },
        complete: function () {
            $('.table_adj_report tbody').css('display','');
            $('#searchh2').css('display','none');
         },
        success:function(data){
             $('#table_adj_report tbody').html(data);
                $('.get_challan_id').on('click',function(){ 
                    $('#transfer-modal').modal('hide');
                    challan_id = $(this).attr("data-id");
                    myFunction_table_details(challan_id);
                    myFunction(challan_id);
                });
        }
        });
    });
    
    $('.get_challan_id').on('click',function(){ 
        $('#transfer-modal').modal('hide');
        challan_id = $(this).attr("data-id");
        myFunction_table_details(challan_id);
        myFunction(challan_id);
    });
    
    function myFunction_table_details(challan_id) {
        $.ajax({
          url:"<?php echo admin_url(); ?>VehRtn/all_challan_details",
          dataType:"JSON",
          method:"POST",
          data:{challan_id:challan_id},
        beforeSend: function () {
            $('#searchh11').css('display','block');
            $('#searchh11').css('color','blue');
        },
        complete: function () {
            $('#searchh11').css('display','none');
        },
        success:function(response){
            var total_ItemID =(response.itemhead.length);
            $("#ItemCount").val(total_ItemID);
            var $itemModal = $('#crate_details_tbl');
            var count_row = $("#row_count").val();
            var count_row_pay = $("#row_count_pay").val();
            var count_row_exp = $("#row_count_exp").val();
            var i = 1;
            for(var count = 1; count <= count_row; count++)
            {
                $("#row"+count).remove();
            }
            for(var count1 = 1; count1 <= count_row_pay; count1++)
            {
                $("#row_pay"+count1).remove();
            }
            for(var count2 = 1; count2 <= count_row_exp; count2++)
            {
                $("#row_exp"+count2).remove();
            }
            
            // Crate table body and  payment table
            
                $.each(response.cratesandpayments, function (column1, value1) {
                        var html = '';
                        var html3 = '';
                        var col = column1 + 1;
                        $("#row_count").val(col);
                        $("#row_count_pay").val(col);
                        html += '<tr class="accounts" id="row'+col+'">';
                        html += '<td style="width: 125px;"><input type="text" name="AccountID'+col+'" style="width: 125px;" id="AccountID'+col+'" value="'+value1.AccountID+'"></td>';
                        html += '<td style="padding:1px 5px !important;">'+value1.company+'</td>';
                        html += '<td style="padding:1px 5px !important;">'+value1.address+'</td>';
                        if(value1.Qty == null){
                            var qty = 0;
                        }else{
                            var qty =value1.Qty;
                        }
                        html += '<td style="padding:1px 5px !important;text-align: right;">'+qty+'</td>';
                        html += '<td style="padding:1px 5px !important;text-align: right;">'+value1.crates_data+'</td>';
                        html += '<td class="rtnqty" style="width: 80px;"><input type="text" name="rtncrates'+col+'" id="rtncrates'+col+'" onblur="calculate_balcrates();" value="0" style="width: 80px;text-align: right;" class="rtncrates"><input type="hidden" name="balcrates'+col+'" id="balcrates'+col+'" value="'+value1.balance_crates_org+'"><input type="hidden" name="colno" id="colno" value="'+col+'"></td>';
                        html += '<td id="balCrates" style="padding:1px 5px !important;text-align: right;"><span>'+value1.balance_crates+'</span></td>';
                        html += '</tr>';
                        $('#crate_details_tbl tbody').append(html);
                        
                        html3 += '<tr class="accounts" id="row_pay'+col+'">';
                        html3 += '<td style="width: 125px;" id="AccountIDTD_pay"><input type="text" name="AccountID_pay'+col+'" style="width: 125px;" id="AccountID_pay'+col+'" value="'+value1.AccountID+'"></td>';
                        html3 += '<td style="padding:1px 5px !important;">'+value1.company+'</td>';
                        html3 += '<td style="padding:1px 5px !important;">'+value1.address+'</td>';
                        html3 += '<td class="rcptAmts" style="width: 80px;"><input type="text" name="receiptamt'+col+'" id="receiptamt'+col+'" onblur="calculate_payment();" value="0" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
                        html3 += '</tr>';
                        $('#payment_details_tbl tbody').append(html3);
                })
                
                var html2 = '';
                html2 += '<table class="table table-striped table-bordered stock_details_tbl fixed_header1" id="stock_details_tbl" width="100%">';
                html2 += '<thead id="thead">';
                html2 += '<tr>';
                html2 += '<th>AccountID</th>';
                html2 += '<th>AccoutName</th>';
                html2 += '<th>SaleID</th>';
                html2 += '<th>RtnAMT</th>';
                html2 += '<th>CGST</th>';
                html2 += '<th>SGST</th>';
                html2 += '<th>IGST</th>';
                $.each(response.itemhead, function (column1, value1) {
                    html2 += '<th style="width: 60px;">'+value1+'</th>';
                })
                html2 += '</tr>';
                html2 += '</thead>';
                html2 += '<tbody id="tbody">';
                
                
                var itemCount = 0;
                $.each(response.data, function (column, value) {
                    
                    var col = column + 1;
                    $("#row_count_frRtn").val(col);
                    html2 += '<tr id="row'+col+'">';
                    html2 += '<td style="padding:1px 5px !important;">'+value.AccountID+'<input type="hidden" name="AccountID_SRtn'+col+'" id="AccountID_SRtn'+col+'" value="'+value.AccountID+'"></td>';
                    html2 += '<td style="padding:1px 5px !important;">'+value.company+'</td>';
                    html2 += '<td style="padding:1px 5px !important;">'+value.SalesID+'</td>';
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="RtnAmt_val'+col+'" id="RtnAmt_val'+col+'" value="0"><span id="RtnAmt">0.00</span></td>';
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="cgst_val'+col+'" id="cgst_val'+col+'" value="0"><span id="cgst">0.00</span></td>';
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="sgst_val'+col+'" id="sgst_val'+col+'" value="0"><span id="sgst">0.00</span></td>';
                    html2 += '<td style="padding:1px 5px !important;text-align: right;"><input type="hidden" name="igst_val'+col+'" id="igst_val'+col+'" value="0"><span id="igst">0.00</span></td>';
                    
                    $.each(response.itemhead, function (column4, value4) {
                        var column4 = column4 + 1;
                        var match = 0;
                        var rate = 0;
                        var gst = 0;
                        var BilledQty = 0;
                        var ItemID = 0;
                        var TransID = 0;
                        var AccountID = 0;
                        var PackQty = 0;
                        var state = '';
                        $.each(value.itemdetails, function (column3, value3) {
                            if(value4==value3["ItemID"]){
                                
                                match = 1;
                                rate = value3["BasicRate"];
                                BilledQty = value3["BilledQty"];
                                ItemID = value3["ItemID"];
                                TransID = value3["TransID"];
                                AccountID = value3["AccountID"];
                                PackQty = value3["CaseQty"];
                                if(value3["igst"] == null || value3["igst"] == "0.00"){
                                    gst = value3["cgst"] * 2;
                                    state = "UP"
                                }else{
                                    gst = value3["igst"];
                                    state = "UP1"
                                }
                            }
                        })
                        if(match == "1"){
                            itemCount = itemCount + 1;
                        html2 += '<td style="width: 60px;"><input type="hidden" name="PackQty_val'+itemCount+'" id="PackQty_val'+itemCount+'" value="'+PackQty+'"><input type="hidden" name="AccountID_val'+itemCount+'" id="AccountID_val'+itemCount+'" value="'+AccountID+'"><input type="hidden" name="ItemID_val'+itemCount+'" id="ItemID_val'+itemCount+'" value="'+ItemID+'"><input type="hidden" name="TransID_val'+itemCount+'" id="TransID_val'+itemCount+'" value="'+TransID+'"><input type="hidden" name="rate_val'+itemCount+'" id="rate_val'+itemCount+'" value="'+rate+'"><input type="hidden" name="gst_val'+itemCount+'" id="gst_val'+itemCount+'" value="'+gst+'"><input type="hidden" name="state_val'+itemCount+'" id="state_val'+itemCount+'" value="'+state+'"><input type="hidden" name="BilledQty" id="BilledQty" value="'+BilledQty+'"><input type="hidden" name="state" id="state" value="'+value.state+'"><input type="hidden" name="rate" id="rate" value="'+rate+'"><input type="hidden" name="gst" id="gst" value="'+gst+'"><input type="text" name="rtnqty'+itemCount+'" id="rtnqty'+itemCount+'" onblur="calculate_rtnqty();" onkeypress="return isNumber(event)" onfocus="myFunction(this)" style="width: 60px;text-align: right;" value="" ></td>';
                        }else{
                            html2 += '<td style="width: 60px;padding:1px 5px !important;text-align: right;background-color: #aea5a599;"></td>';
                        }   
                    })
                    html2 += '</tr>';
                });
                html2 += '</tbody>';
                html2 += '</table>';
                
                $('#ItemCount').val(itemCount);
                $('#fixed_header1').html(html2);
                //$('#fixed_header3').html(html3);
            }
        });
    }
    function myFunction(challan_id) { 
        $.ajax({
          url:"<?php echo admin_url(); ?>VehRtn/unique_challan_details",
          dataType:"JSON",
          method:"POST",
          data:{challan_id:challan_id},
         
          success:function(data){
               $('#challan_n').val(data.ChallanID);
               $('#route_code').val(data.RouteID);
               $('#route_name').val(data.name);
               $('#routekm').val(data.KM);
               $('#vehicle_number').val(data.VehicleID);
               $('#driver_id').val(data.DriverID);
               $('#driver_name').val(data.driver_fn);
               $('#loder_id').val(data.LoaderID);
               $('#loder_name').val(data.loader_fn);
               $('#salesman_id').val(data.SalesmanID);
               $('#salesman_name').val(data.Salesman_fn);
               $('#challan_crates').val(data.Crates);
               $('#vehicle_capc').val(data.VehicleCapacity);
               var ChallanDate = data.Transdate.substring(0, 10);
                var ChallanDateNew = ChallanDate.split("-").reverse().join("/");
                $('#to_date').val(ChallanDateNew);
               $('#case_depo').val(0.00);
               $('#case_depo1').val(0.00);
               $('#check_depo').val(0.00);
               $('#total_expense').val(0.00);
               $('#total_expense1').val(0.00);
               $('#fresh_ret_amt').val(0.00);
               $('#fresh_ret_amt1').val(0.00);
               $('#NERT_trans').val(0.00);
          
            }
        });
    }
    
    // For Crates Details
    $('#rtncrates').on('blur', function () {
        
        var rtncrates =document.getElementById("rtncrates").value;
        if(rtncrates == "" || rtncrates == null){
            alert('please enter return crates...');
        }else{
            //alert('hello');
        var AccountID =document.getElementById("AccountID").value;
        var party_name_val =document.getElementById("party_name_val").value;
        var address_val =document.getElementById("address_val").value;
        var opnCrates_val =document.getElementById("opnCrates_val").value;
        var chlCrates_val =document.getElementById("chlCrates_val").value;
        var balcrates =document.getElementById("balcrates").value;
        
        var balCrates_new = balcrates - rtncrates;
        
        var table=document.getElementById("crate_details_tbl");
        var table_len=(table.rows.length) - 1;
        var html = '';
        
        if(AccountID == "" || AccountID == null){
            alert('please add AccountID');
            $('#AccountID').val('');
            $('#AccountID').focus();
        }else{
            var count = $("#row_count").val();
            var new_count = parseInt(count) + 1;
            $("#row_count").val(new_count);
            
            
            html += '<tr class="accounts" id="row'+table_len+'">';
            html += '<td><input type="text" name="AccountID'+table_len+'" style="width: 125px;" id="AccountID'+table_len+'" value="'+AccountID+'"></td>';
            html += '<td style="padding:1px 5px !important;">'+party_name_val+'</td>';
            html += '<td style="padding:1px 5px !important;">'+address_val+'</td>';
            if(opnCrates_val == null){
                var qty = 0;
            }else{
                var qty =opnCrates_val;
            }
            html += '<td style="padding:1px 5px !important;text-align: right;">'+qty+'</td>';
            html += '<td style="padding:1px 5px !important;text-align: right;"><span id="chlCrates'+table_len+'">'+chlCrates_val+'</span><input type="hidden" name="chlCrates_val'+table_len+'" id="chlCrates_val'+table_len+'" value="'+chlCrates_val+'"></td>';
            html += '<td class="rtnqty"><input type="text" name="rtncrates'+table_len+'" id="rtncrates'+table_len+'"  onblur="calculate_balcrates();" value="'+rtncrates+'" style="width: 80px;text-align: right;" class="rtncrates"><input type="hidden" name="balcrates'+table_len+'" id="balcrates'+table_len+'" value="'+balcrates+'"><input type="hidden" name="colno" id="colno" value="'+table_len+'"></td>';
            html += '<td id="balCrates" style="padding:1px 5px !important;text-align: right;"><span>'+balCrates_new+'</span></td>';
            html += '</tr>';
            var row = table.insertRow(table_len).outerHTML=html;
            
            document.getElementById("AccountID").value="";
            document.getElementById("party_name_val").value="";
            document.getElementById("address_val").value="";
            document.getElementById("opnCrates_val").value="";
            document.getElementById("balCrates_new_val").value="";
            document.getElementById("balcrates").value ="";
            document.getElementById("rtncrates").value="";
            document.getElementById("chlCrates_val").value="";
            
            
            document.getElementById("party_name").innerHTML="";
            document.getElementById("address").innerHTML ="";
            document.getElementById("opnCrates").innerHTML="";
            document.getElementById("balCrates_new").innerHTML="";
            document.getElementById("chlCrates").innerHTML="";
        }
        }
        
    });
    
    // For Payments Details
    $('#receiptamt').on('blur', function () {
        //alert('hello');
        var AccountID_pay =document.getElementById("AccountID_pay").value;
        var party_name_pay_val =document.getElementById("party_name_pay_val").value;
        var address_pay_val =document.getElementById("address_pay_val").value;
        var receiptamt =document.getElementById("receiptamt").value;
        
        var table=document.getElementById("payment_details_tbl");
        var table_len=(table.rows.length) - 1;
        var html = '';
        
        if(AccountID_pay == "" || AccountID_pay == null){
            alert('please add AccountID');
            $('#AccountID_pay').val('');
            $('#AccountID_pay').focus();
        }if(receiptamt == "" || receiptamt == '0'){
            alert('please add amount');
            $('#receiptamt').val('');
        }else{
            var count = $("#row_count_pay").val();
            var new_count = parseInt(count) + 1;
            $("#row_count_pay").val(new_count);
            html += '<tr class="accounts" id="row_pay'+table_len+'">';
            html += '<td><input type="text" name="AccountID_pay'+table_len+'" style="width: 125px;" id="AccountID_pay'+table_len+'" value="'+AccountID_pay+'"></td>';
            html += '<td style="padding:1px 5px !important;">'+party_name_pay_val+'</td>';
            html += '<td style="padding:1px 5px !important;">'+address_pay_val+'</td>';
            
            html += '<td class="rcptAmts"><input type="text" name="receiptamt'+table_len+'" id="receiptamt'+table_len+'"  onblur="calculate_payment();" value="'+receiptamt+'" style="width: 80px;text-align: right" onkeypress="return isNumber(event)"></td>';
            html += '</tr>';
            var row = table.insertRow(table_len).outerHTML=html;
            
            document.getElementById("AccountID_pay").value="";
            document.getElementById("party_name_pay_val").value="";
            document.getElementById("address_pay_val").value="";
            document.getElementById("receiptamt").value="";
            
            document.getElementById("party_name_pay").innerHTML="";
            document.getElementById("address_pay").innerHTML ="";
        }
    });
    
        // For Expense Details
    $('#expamt').on('blur', function () {
        
        var AccountID_exp =document.getElementById("AccountID_exp").value;
        var party_name_exp_val =document.getElementById("party_name_exp_val").value;
        var address_exp_val =document.getElementById("address_exp_val").value;
        var expamt =document.getElementById("expamt").value;
        
        var table=document.getElementById("expense_details_tbl");
        
        var table_len=(table.rows.length) - 1;
        var html = '';
        
        if(AccountID_exp == "" || AccountID_exp == null){
            alert('please add AccountID');
            $('#AccountID_exp').val('');
            $('#AccountID_exp').focus();
        }if(expamt == "" || expamt == '0'){
            alert('please add amount');
            $('#expamt').val('');
        }else{
            var count = $("#row_count_exp").val();
            var new_count = parseInt(count) + 1;
            $("#row_count_exp").val(new_count);
            html += '<tr class="accounts" id="row_exp'+table_len+'">';
            html += '<td><input type="text" name="AccountID_exp'+table_len+'" style="width: 125px;" id="AccountID_exp'+table_len+'" value="'+AccountID_exp+'"></td>';
            html += '<td style="padding:1px 5px !important;">'+party_name_exp_val+'</td>';
            html += '<td style="padding:1px 5px !important;">'+address_exp_val+'</td>';
            
            html += '<td class="expamts"><input type="text" name="expamt'+table_len+'" id="expamt'+table_len+'"  onblur="calculate_expense();" value="'+expamt+'" style="width: 80px;text-align:right;" onkeypress="return isNumber(event)"></td>';
            html += '</tr>';
            var row = table.insertRow(table_len).outerHTML=html;
            
            document.getElementById("AccountID_exp").value="";
            document.getElementById("party_name_exp_val").value="";
            document.getElementById("address_exp_val").value="";
            document.getElementById("expamt").value="";
            
            document.getElementById("party_name_exp").innerHTML="";
            document.getElementById("address_exp").innerHTML ="";
            
        }
    });
</script>
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
    //  Return Quantity calculation
    function calculate_rtnqty(){
        //var len = $('#challan_data tr')[0].cells.length;
        var frsrtnAmt = 0;
        var item_count = 0;
        $('#stock_details_tbl tbody').find('tr').each(function (k, v) {
            var tr_total = 0;
            var tr_gst = 0;
            var tr_half_gst = 0;
            var state = '';
            $(this).find('td').each(function (k, v) {
                
                var rtnqty = $(this).find('input[type="text"]').val();
                var rate = $(this).find('input[name="rate"]').val();
                var gst = $(this).find('input[name="gst"]').val();
                var billedqty = $(this).find('input[name="BilledQty"]').val();
                state = $(this).find('input[name="state"]').val();
                if(rtnqty !== undefined){
                    item_count++;
                    if(rtnqty !==""){
                         
                        var comp = 0;
                        //alert(state);
                        if(parseFloat(billedqty) >= parseFloat(rtnqty)){
                            
                            var total = parseFloat(rtnqty) * parseFloat(rate);
                            var gstAmt = (total / 100 ) * parseFloat(gst);
                            tr_gst = parseFloat(tr_gst) + parseFloat(gstAmt);
                            tr_total = parseFloat(tr_total) + parseFloat(total) + parseFloat(tr_gst);
                        }else{
                            $(this).find('input[type="text"]').val('0')
                            var msg = "Please enter less than or equal to '"+billedqty+"' ";
                            alert(msg);
                            
                        }
                    }
                }
            })
                frsrtnAmt = parseFloat(frsrtnAmt) + parseFloat(tr_total);
                
                $('td', this).eq(3).find('input[type="hidden"]').val(tr_total.toFixed(2));
                $('td', this).eq(3).find('span').html(tr_total.toFixed(2));
                //alert(state);
                if(state == "UP"){
                     tr_half_gst = tr_gst / 2;
                    $('td', this).eq(4).find('input[type="hidden"]').val(tr_half_gst.toFixed(2));
                    $('td', this).eq(5).find('input[type="hidden"]').val(tr_half_gst.toFixed(2));
                    $('td', this).eq(4).find('span').html(tr_half_gst.toFixed(2));
                    $('td', this).eq(5).find('span').html(tr_half_gst.toFixed(2));
                }else{
                    $('td', this).eq(6).find('input[type="hidden"]').val(tr_gst.toFixed(2));
                    $('td', this).eq(6).find('span').html(tr_gst.toFixed(2));
                }
        })
        $("#fresh_ret_amt").val(frsrtnAmt.toFixed(2));
        $("#fresh_ret_amt1").val(frsrtnAmt.toFixed(2));
        $("#ItemCount").val(item_count);
    }
</script>
<script>
    // balance crate calculation
    function calculate_balcrates(){
        var p = $(".table.crate_details_tbl tbody tr.accounts");
        var totalRfCrates = 0;
        $.each(p, function() {
            var row_no = $(this).find("td.rtnqty input[name='colno']").val();
            var AccountID = $(this).find("td#AccountIDTD input[type='text']").val();
            var actualbal = $(this).find("td.rtnqty input[type='hidden']").val();
            var returnCrates = $(this).find("td.rtnqty input[type='text']").val();
            if(returnCrates !== "" && AccountID !== ""){
                totalRfCrates = parseInt(totalRfCrates) + parseInt(returnCrates);
            }
            var newBal = parseInt(actualbal) - parseInt(returnCrates);
            if(row_no == ""){
              
                $(this).find("td#balCrates_new span").html(newBal);
            
            }else{
                $(this).find("td#balCrates span").html(newBal);
            }
        })
        $("#refund_crates").val(totalRfCrates);
    }
</script>
<script type='text/javascript'>
    //  Receipt Payments calculation
    function calculate_payment(){
        var p = $(".table.payment_details_tbl tbody tr.accounts");
        var totalpymt = 0;
        $.each(p, function() {
            
            var receiptamt = $(this).find("td.rcptAmts input[type='text']").val();
            var AccountID = $(this).find("td#AccountIDTD_pay input[type='text']").val();
            
            if(receiptamt !== "" && AccountID !== ""){
                totalpymt = parseInt(totalpymt) + parseInt(receiptamt);
            }
        })
        $("#case_depo").val(totalpymt.toFixed(2));
        $("#case_depo1").val(totalpymt.toFixed(2));
    }
</script>

<script type='text/javascript'>
    // Expense Amt calculation
    function calculate_expense(){
        var p = $(".table.expense_details_tbl tbody tr.accounts");
        var totalExpense = 0;
        $.each(p, function() {
            
            var expAmt = $(this).find("td.expamts input[type='text']").val();
            var AccountID = $(this).find("td#AccountIDTD_exp input[type='text']").val();
            if(expAmt !== "" && AccountID !== ""){
                totalExpense = parseInt(totalExpense) + parseInt(expAmt);
            }
        })
        $("#total_expense").val(totalExpense.toFixed(2));
        $("#total_expense1").val(totalExpense.toFixed(2));
    }
</script>

<script type='text/javascript'>
    $(document).ready(function(){
     
    // For payment
    // Initialize For Account
     $("#AccountID_pay").autocomplete({
        
        source: function( request, response ) {
          
          $.ajax({
            url: "<?=base_url()?>admin/VehRtn/GetAccountlistForCrates",
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
             
       //alert(Conform);
       var ChallanID = $('#challan_n').val();
       if(empty(ChallanID)){
          alert('please select challanID');
            $("#AccountID_pay").focus();
            return false;
       }else{
                $('#AccountID_pay').val(ui.item.value);
                $('#party_name_pay').html(ui.item.label);
                $('#party_name_pay_val').val(ui.item.label);
                $('#address_pay').html(ui.item.address);
                $('#address_pay_val').val(ui.item.address);
                $("#receiptamt").focus();
                return false;
            }
        }
      });
        $('#AccountID_pay').on('blur', function () {
            var AccountID = $(this).val();
            if(empty(AccountID)){
                
            }else{
                $.ajax({
                    url: "<?=base_url()?>admin/VehRtn/getAccountDetails",
                    type: 'post',
                    dataType: "json",
                    data: {
                      AccountID: AccountID,
                    },
                    success: function( data ) {
                        if(empty(data)){
                            alert('AccountID not found.');
                            $("#AccountID_pay").val('');
                            $("#AccountID_pay").focus();
                        }else{
                            $('#AccountID_pay').val(data.AccountID); // display the selected text
                            $('#party_name_pay_val').val(data.company); // display the selected text
                            $('#party_name_pay').html(data.company); // display the selected text
                            $('#address_pay_val').val(data.Address); // display the selected text
                            $('#address_pay').html(data.Address); // display the selected text
                            $("#receiptamt").focus();
                        }
                    }
                });
            }
        });
        
        $('#AccountID_pay').on('focus', function () {
            $('#AccountID_pay').val('');
            $('#party_name_pay_val').val('');
            $('#party_name_pay').html(''); 
            $('#address_pay_val').val(''); 
            $('#address_pay').html(''); 
        })
        
        // For Expenses
    // Initialize For Account
     $("#AccountID_exp").autocomplete({
        
        source: function( request, response ) {
          
          $.ajax({
            url: "<?=base_url()?>admin/VehRtn/staffaccountlist",
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
             
       //alert(Conform);
       var ChallanID = $('#challan_n').val();
       if(empty(ChallanID)){
          alert('please select challanID');
            $("#AccountID_exp").focus();
            return false;
       }else{
                $('#AccountID_exp').val(ui.item.value);
                $('#party_name_exp').html(ui.item.label);
                $('#party_name_exp_val').val(ui.item.label);
                $('#address_exp').html(ui.item.address);
                $('#address_exp_val').val(ui.item.address);
                $("#expamt").focus();
                return false;
            }
        }
      });
       $('#AccountID_exp').on('blur', function () {
            var AccountID = $(this).val();
            if(empty(AccountID)){
                
            }else{
                $.ajax({
                    url: "<?=base_url()?>admin/VehRtn/get_staffAccount_Details",
                    type: 'post',
                    dataType: "json",
                    data: {
                      AccountID: AccountID,
                    },
                    success: function( data ) {
                        if(empty(data)){
                            alert('AccountID not found.');
                            $("#AccountID_exp").val('');
                            $("#AccountID_exp").focus();
                        }else{
                            if(data.company == null){
                                var fullname = data.firstname+' '+data.lastname;
                                var address = data.current_address;
                            }else{
                                var fullname = data.company;
                                var address = data.address;
                            }
                            $('#AccountID_exp').val(data.AccountID); // display the selected text
                            $('#party_name_exp_val').val(fullname); // display the selected text
                            $('#party_name_exp').html(fullname); // display the selected text
                            $('#address_exp_val').val(address); // display the selected text
                            $('#address_exp').html(address); // display the selected text
                            $("#expamt").focus();
                        }
                    }
                });
            }
        });
        $('#AccountID_exp').on('focus', function () {
            $('#AccountID_exp').val(''); 
            $('#party_name_exp_val').val('');
            $('#party_name_exp').html(''); 
            $('#address_exp_val').val(''); 
            $('#address_exp').html(''); 
        })
    });
</script>
<script>
    // Save New Item
        $('.saveBtn').on('click',function(){ 
            
            // Ganeral Data
        var refund_crates = $("#refund_crates").val();
        var from_date = $("#from_date").val();
        var challan_n = $("#challan_n").val();
        var vehicle_number = $("#vehicle_number").val();
        if(challan_n !== ''){
            // Crate Details
            var CrateCount = $("#row_count").val();
            var CrateArray = new Array();
            for (i=1;i<=CrateCount;i++) {
                var id= 'AccountID'+i;
                var AccountID = document.getElementById(id).value;
                var id2= 'rtncrates'+i;
                var RtnCrates = document.getElementById(id2).value;
                var ii = i - 1;
             CrateArray[ii]=new Array();
             CrateArray[ii][0]=AccountID;
             CrateArray[ii][1]=RtnCrates;
            }
            var CratesSerializedArr = JSON.stringify(CrateArray);
            
            // Payments Details
            var PayCount = $("#row_count_pay").val();
            var PaymentArray = new Array();
            for (i=1;i<=PayCount;i++) {
                var id= 'AccountID_pay'+i;
                var PayAccountID = document.getElementById(id).value;
                var id2= 'receiptamt'+i;
                var PaymentAmt = document.getElementById(id2).value;
                var ii = i - 1;
             PaymentArray[ii]=new Array();
             PaymentArray[ii][0]=PayAccountID;
             PaymentArray[ii][1]=PaymentAmt;
            }
            var PaymentSerializedArr = JSON.stringify(PaymentArray);
            
            // Expenses Details
            var ExpCount = $("#row_count_exp").val();
            var ExpArray = new Array();
            for (i=1;i<=ExpCount;i++) {
                var id= 'AccountID_exp'+i;
                var ExpAccountID = document.getElementById(id).value;
                var id2= 'expamt'+i;
                var ExpAmt = document.getElementById(id2).value;
                var ii = i - 1;
             ExpArray[ii]=new Array();
             ExpArray[ii][0]=ExpAccountID;
             ExpArray[ii][1]=ExpAmt;
            }
        var ExpSerializedArr = JSON.stringify(ExpArray);
        // Fresh Rtn Values 
        var frRtnVal = $("#row_count_frRtn").val();
        var frRtnValArray = new Array();
            for (i=1;i<=frRtnVal;i++) {
                var id= 'AccountID_SRtn'+i;
                var AccountID_SRtn = document.getElementById(id).value;
                
                var id2= 'RtnAmt_val'+i;
                var RtnAmt_val = document.getElementById(id2).value;
                
                var id3= 'cgst_val'+i;
                var cgst_val = document.getElementById(id3).value;
                
                var id4= 'sgst_val'+i;
                var sgst_val = document.getElementById(id4).value;
                
                var id5= 'igst_val'+i;
                var igst_val = document.getElementById(id5).value;
                
                var ii = i - 1;
                frRtnValArray[ii]=new Array();
                frRtnValArray[ii][0]=AccountID_SRtn;
                frRtnValArray[ii][1]=RtnAmt_val;
                frRtnValArray[ii][2]=cgst_val;
                frRtnValArray[ii][3]=sgst_val;
                frRtnValArray[ii][4]=igst_val;
            }
        var frRtnSerializedArr = JSON.stringify(frRtnValArray);
        
        // Fresh Return Data
        var ItemCount = $("#ItemCount").val();
        var FreshRtnArray = new Array();
            for (i=1;i<=ItemCount;i++) {
            
                var id= 'rtnqty'+i;
                var rtnqty = document.getElementById(id).value;
                
                var id2= 'TransID_val'+i;
                var TransID_val = document.getElementById(id2).value;
                
                var id3= 'ItemID_val'+i;
                var ItemID_val = document.getElementById(id3).value;
                
                var id4= 'AccountID_val'+i;
                var AccountID_val = document.getElementById(id4).value;
                
                var id5= 'rate_val'+i;
                var rate_val = document.getElementById(id5).value;
                
                var id6= 'gst_val'+i;
                var gst_val = document.getElementById(id6).value;
                
                var id7= 'state_val'+i;
                var state_val = document.getElementById(id7).value;
                
                var id8= 'PackQty_val'+i;
                var PackQty_val = document.getElementById(id8).value;
                
                var ii = i - 1;
                FreshRtnArray[ii]=new Array();
                FreshRtnArray[ii][0]=rtnqty;
                FreshRtnArray[ii][1]=TransID_val;
                FreshRtnArray[ii][2]=ItemID_val;
                FreshRtnArray[ii][3]=AccountID_val;
                FreshRtnArray[ii][4]=rate_val;
                FreshRtnArray[ii][5]=gst_val;
                FreshRtnArray[ii][6]=state_val;
                FreshRtnArray[ii][7]=PackQty_val;
            }
        var FreshRtnSerializedArr = JSON.stringify(FreshRtnArray);
            $.ajax({
                url:"<?php echo admin_url(); ?>VehRtn/SaveVehRtn",
                dataType:"JSON",
                method:"POST",
                data:{refund_crates:refund_crates,from_date:from_date,challan_n:challan_n,FreshRtnSerializedArr:FreshRtnSerializedArr,CratesSerializedArr:CratesSerializedArr,frRtnVal:frRtnVal,vehicle_number:vehicle_number,
                ExpSerializedArr:ExpSerializedArr,PaymentSerializedArr:PaymentSerializedArr,ItemCount:ItemCount,ExpCount:ExpCount,CrateCount:CrateCount,PayCount:PayCount,frRtnSerializedArr:frRtnSerializedArr
                },
                beforeSend: function () {
                $('.searchh3').css('display','block');
                $('.searchh3').css('color','blue');
                },
                complete: function () {
                $('.searchh3').css('display','none');
                },
                success:function(data){
                    if(data == false){
                        
                    }else if(data == 'Created'){
                        alert_float('warning', 'VehRtn Already created for this challan...');
                        var NextVRtnID = $("#NextVRtnID").val();
                        $("#vehicle_return_id").val(NextVRtnID);
                        $("#challan_n").val('');
                        $("#route_code").val('');
                        $("#route_name").val('');
                        $("#routekm").val('0.00');
                        $("#vehicle_number").val('');
                        $("#vehicle_capc").val('0.00');
                        $("#driver_id").val('');
                        $("#driver_name").val('');
                        $("#loder_id").val('');
                        $("#loder_name").val('');
                        $("#salesman_id").val('');
                        $("#salesman_name").val('');
                        $("#challan_crates").val('0.00');
                        $("#refund_crates").val('0.00');
                        $("#fresh_ret_amt1").val('0.00');
                        $("#case_depo1").val('0.00');
                        $("#check_depo").val('0.00');
                        $("#NERT_trans").val('0.00');
                        $("#total_expense").val('0.00');
                        $("#total_expense1").val('0.00');
                        $('#crate_details').show();
                        $('#fresh_stock_return').hide(); 
                        $('#payment_reciept').hide();
                        $('#expense_detail').hide();
                          // Create 
                          var TotalRow = $("#row_count").val();
                          var crRow = parseInt(TotalRow);
                          for (var A = 1; A <= crRow; A++) {
                              var id = 'row'+A;
                            document.getElementById(id).remove();
                          }
                          // Fresh RTN
                        var html2 = '';
                        $('#stock_details_tbl').html(html2);
                            
                          // Payments 
                        var TotalRow = $("#row_count_pay").val();
                        //var crRow = parseInt(TotalRow) - 1;
                          for (var A = 1; A <= TotalRow; A++) {
                              var id = 'row_pay'+A;
                            document.getElementById(id).remove();
                          }
                    // Expenses
                           var TotalRow = $("#row_count_exp").val();
                        //var crRow = parseInt(TotalRow) - 1;
                          for (var A = 1; A <= TotalRow; A++) {
                              var id = 'row_exp'+A;
                            document.getElementById(id).remove();
                          }
                        $("#ItemCount").val('0');
                        $("#row_count").val('0');
                        $("#row_count_pay").val('0');
                        $("#row_count_exp").val('0');
                            
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('.printBtn').hide();
                    }else{
                            alert_float('success', 'Record created successfully...');
                            $('#vehicle_return_id').val(data);
                            $("#NextVRtnID").val(data);
                            $("#challan_n").val('');
                            $("#route_code").val('');
                            $("#route_name").val('');
                            $("#routekm").val('0.00');
                            $("#vehicle_number").val('');
                            $("#vehicle_capc").val('0.00');
                            $("#driver_id").val('');
                            $("#driver_name").val('');
                            $("#loder_id").val('');
                            $("#loder_name").val('');
                            $("#salesman_id").val('');
                            $("#salesman_name").val('');
                            $("#challan_crates").val('0.00');
                            $("#refund_crates").val('0.00');
                            $("#fresh_ret_amt1").val('0.00');
                            $("#case_depo1").val('0.00');
                            $("#check_depo").val('0.00');
                            $("#NERT_trans").val('0.00');
                            $("#total_expense").val('0.00');
                            $("#total_expense1").val('0.00');
                            $('#crate_details').show();
                            $('#fresh_stock_return').hide(); 
                            $('#payment_reciept').hide();
                            $('#expense_detail').hide();
                           // Create 
                           var TotalRow = $("#row_count").val();
                           var crRow = parseInt(TotalRow);
                           for (var A = 1; A <= crRow; A++) {
                               var id = 'row'+A;
                                document.getElementById(id).remove();
                           }
                           // Fresh RTN
                            var html2 = '';
                            $('#stock_details_tbl').html(html2);
                            
                           // Payments 
                            var TotalRow = $("#row_count_pay").val();
                            //var crRow = parseInt(TotalRow) - 1;
                           for (var A = 1; A <= TotalRow; A++) {
                               var id = 'row_pay'+A;
                                document.getElementById(id).remove();
                           }
                        // Expenses
                            var TotalRow = $("#row_count_exp").val();
                            //var crRow = parseInt(TotalRow) - 1;
                           for (var A = 1; A <= TotalRow; A++) {
                               var id = 'row_exp'+A;
                                document.getElementById(id).remove();
                           }
                            $("#ItemCount").val('0');
                            $("#row_count").val('0');
                            $("#row_count_pay").val('0');
                            $("#row_count_exp").val('0');
                            
                            $('.saveBtn').show();
                            $('.saveBtn2').show();
                            $('.updateBtn').hide();
                            $('.updateBtn2').hide();
                            $('.printBtn').hide();
                    }
                   
                }
            });
        }else{
            alert('please select Challan...');
            $('#challan_n').focus();
        }
            
        });
    // Update Exiting VRtnID
        $('.updateBtn').on('click',function(){ 
            
            // Ganeral Data
        var refund_crates = $("#refund_crates").val();
        var VRtnID = $("#vehicle_return_id").val();
        var from_date = $("#from_date").val();
        var challan_n = $("#challan_n").val();
        var vehicle_number = $("#vehicle_number").val();
        if(challan_n !== ''){
        // Crate Details
            var CrateCount = $("#row_count").val();
            var CrateArray = new Array();
            for (i=1;i<=CrateCount;i++) {
                var id= 'AccountID'+i;
                var AccountID = document.getElementById(id).value;
                var id2= 'rtncrates'+i;
                var RtnCrates = document.getElementById(id2).value;
                var ii = i - 1;
             CrateArray[ii]=new Array();
             CrateArray[ii][0]=AccountID;
             CrateArray[ii][1]=RtnCrates;
            }
            var CratesSerializedArr = JSON.stringify(CrateArray);
            
        // Payments Details
            var PayCount = $("#row_count_pay").val();
            var PaymentArray = new Array();
            for (i=1;i<=PayCount;i++) {
                var id= 'AccountID_pay'+i;
                var PayAccountID = document.getElementById(id).value;
                var id2= 'receiptamt'+i;
                var PaymentAmt = document.getElementById(id2).value;
                var ii = i - 1;
             PaymentArray[ii]=new Array();
             PaymentArray[ii][0]=PayAccountID;
             PaymentArray[ii][1]=PaymentAmt;
            }
            var PaymentSerializedArr = JSON.stringify(PaymentArray);
            
        // Expenses Details
            var ExpCount = $("#row_count_exp").val();
            var ExpArray = new Array();
            for (i=1;i<=ExpCount;i++) {
                var id = 'AccountID_exp'+i;
                var ExpAccountID = document.getElementById(id).value;
                var id2 = 'expamt'+i;
                var ExpAmt = document.getElementById(id2).value;
                var ii = i - 1;
             ExpArray[ii]=new Array();
             ExpArray[ii][0]=ExpAccountID;
             ExpArray[ii][1]=ExpAmt;
            }
        var ExpSerializedArr = JSON.stringify(ExpArray);
        // Fresh Rtn Values 
        var frRtnVal = $("#row_count_frRtn").val();
        var frRtnValArray = new Array();
            for (i=1;i<=frRtnVal;i++) {
                var id= 'AccountID_SRtn'+i;
                var AccountID_SRtn = document.getElementById(id).value;
                
                var id2= 'RtnAmt_val'+i;
                var RtnAmt_val = document.getElementById(id2).value;
                
                var id3= 'cgst_val'+i;
                var cgst_val = document.getElementById(id3).value;
                
                var id4= 'sgst_val'+i;
                var sgst_val = document.getElementById(id4).value;
                
                var id5= 'igst_val'+i;
                var igst_val = document.getElementById(id5).value;
                
                var ii = i - 1;
                frRtnValArray[ii]=new Array();
                frRtnValArray[ii][0]=AccountID_SRtn;
                frRtnValArray[ii][1]=RtnAmt_val;
                frRtnValArray[ii][2]=cgst_val;
                frRtnValArray[ii][3]=sgst_val;
                frRtnValArray[ii][4]=igst_val;
            }
        var frRtnSerializedArr = JSON.stringify(frRtnValArray);
        
        // Fresh Return Data
        var ItemCount = $("#ItemCount").val();
        var FreshRtnArray = new Array();
            for (i=1;i<=ItemCount;i++) {
            
                var id= 'rtnqty'+i;
                var rtnqty = document.getElementById(id).value;
                
                var id2= 'TransID_val'+i;
                var TransID_val = document.getElementById(id2).value;
                
                var id3= 'ItemID_val'+i;
                var ItemID_val = document.getElementById(id3).value;
                
                var id4= 'AccountID_val'+i;
                var AccountID_val = document.getElementById(id4).value;
                
                var id5= 'rate_val'+i;
                var rate_val = document.getElementById(id5).value;
                
                var id6= 'gst_val'+i;
                var gst_val = document.getElementById(id6).value;
                
                var id7= 'state_val'+i;
                var state_val = document.getElementById(id7).value;
                
                var id8= 'PackQty_val'+i;
                var PackQty_val = document.getElementById(id8).value;
                
                var ii = i - 1;
                FreshRtnArray[ii]=new Array();
                FreshRtnArray[ii][0]=rtnqty;
                FreshRtnArray[ii][1]=TransID_val;
                FreshRtnArray[ii][2]=ItemID_val;
                FreshRtnArray[ii][3]=AccountID_val;
                FreshRtnArray[ii][4]=rate_val;
                FreshRtnArray[ii][5]=gst_val;
                FreshRtnArray[ii][6]=state_val;
                FreshRtnArray[ii][7]=PackQty_val;
            }
        var FreshRtnSerializedArr = JSON.stringify(FreshRtnArray);
            $.ajax({
                url:"<?php echo admin_url(); ?>VehRtn/UpdateVehRtn",
                dataType:"JSON",
                method:"POST",
                data:{refund_crates:refund_crates,from_date:from_date,challan_n:challan_n,FreshRtnSerializedArr:FreshRtnSerializedArr,CratesSerializedArr:CratesSerializedArr,frRtnVal:frRtnVal,vehicle_number:vehicle_number,
                ExpSerializedArr:ExpSerializedArr,PaymentSerializedArr:PaymentSerializedArr,ItemCount:ItemCount,ExpCount:ExpCount,CrateCount:CrateCount,PayCount:PayCount,frRtnSerializedArr:frRtnSerializedArr,VRtnID:VRtnID
                },
                beforeSend: function () {
                $('.searchh4').css('display','block');
                $('.searchh4').css('color','blue');
                },
                complete: function () {
                $('.searchh4').css('display','none');
                },
                success:function(data){
                    if(data == false){
                        
                    }else{
                            alert_float('success', 'Record updated successfully...');
                            $('#vehicle_return_id').val(data);
                            $("#NextVRtnID").val(data);
                            $("#challan_n").val('');
                            $("#route_code").val('');
                            $("#route_name").val('');
                            $("#routekm").val('0.00');
                            $("#vehicle_number").val('');
                            $("#vehicle_capc").val('0.00');
                            $("#driver_id").val('');
                            $("#driver_name").val('');
                            $("#loder_id").val('');
                            $("#loder_name").val('');
                            $("#salesman_id").val('');
                            $("#salesman_name").val('');
                            $("#challan_crates").val('0.00');
                            $("#refund_crates").val('0.00');
                            $("#fresh_ret_amt1").val('0.00');
                            $("#case_depo1").val('0.00');
                            $("#check_depo").val('0.00');
                            $("#NERT_trans").val('0.00');
                            $("#total_expense").val('0.00');
                            $("#total_expense1").val('0.00');
                            $('#crate_details').show();
                            $('#fresh_stock_return').hide(); 
                            $('#payment_reciept').hide();
                            $('#expense_detail').hide();
                           // Create 
                           var TotalRow = $("#row_count").val();
                           var crRow = parseInt(TotalRow);
                           for (var A = 1; A <= crRow; A++) {
                               var id = 'row'+A;
                                document.getElementById(id).remove();
                           }
                           // Fresh RTN
                            var html2 = '';
                            $('#stock_details_tbl').html(html2);
                            
                           // Payments 
                            var TotalRow = $("#row_count_pay").val();
                            //var crRow = parseInt(TotalRow) - 1;
                           for (var A = 1; A <= TotalRow; A++) {
                               var id = 'row_pay'+A;
                                document.getElementById(id).remove();
                           }
                        // Expenses
                            var TotalRow = $("#row_count_exp").val();
                            //var crRow = parseInt(TotalRow) - 1;
                           for (var A = 1; A <= TotalRow; A++) {
                               var id = 'row_exp'+A;
                                document.getElementById(id).remove();
                           }
                            $("#ItemCount").val('0');
                            $("#row_count").val('0');
                            $("#row_count_pay").val('0');
                            $("#row_count_exp").val('0');
                            
                            $('.saveBtn').show();
                            $('.saveBtn2').show();
                            $('.updateBtn').hide();
                            $('.updateBtn2').hide();
                            $('.printBtn').hide();
                    }
                   
                }
            });
        }
        
        });
</script>
<script type="text/javascript">
   $('.rtncrates').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 3 )) {
        event.preventDefault();
    }
});
</script>
<script type="text/javascript">
 function printPage(){
    
    var VrtnID = $("#vehicle_return_id").val();
	var VehRtnDate = $("#from_date").val();
	var route_name = $("#route_name").val();
	var challanID = $("#challan_n").val();
	var VehicleNo = $("#vehicle_number").val();
	var driver_name = $("#driver_name").val();
	var salesman_name = $("#salesman_name").val();
	var loder_name = $("#loder_name").val();
	var refund_crates = $("#refund_crates").val();
	var case_depo = $("#case_depo").val();
	var total_expense = $("#total_expense").val();
	var Uname = $("#UserIDName").val();
	
	var NetCashRecvd = parseFloat(case_depo) - parseFloat(total_expense);
	

	var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="80%" class="tree table table-striped table-bordered" style="font-size:12px;">';
    heading_data += '<tbody><tr><td style="text-align:center;" ><b>Vehicle Return Voucher</b></td></tr><tr><td style="text-align:center;" ><?php echo $company_detail->company_name; ?></td></tr>';
    heading_data += '<tr><td style="text-align:center;" ><?php echo $company_detail->address; ?></td></tr>';    
    
    heading_data += '</tbody></table>';
    
	var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} .show_in_print{ display:block; }table, th, td { border: 1px solid black; border-collapse: collapse;}</style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="80%" class="tree table table-striped table-bordered" style="font-size:12px;">';
    tableData += '<tbody>';
    tableData += '<tr>';
    tableData += '<td colspan="2">ReurnID : '+VrtnID+'</td>';
    tableData += '<td colspan="3">Vehicle Regno : '+VehicleNo+'</td>';
    tableData += '<tr>';
    tableData += '<tr>';
    tableData += '<td colspan="2">Date : '+VehRtnDate+'</td>';
    tableData += '<td colspan="3">Driver Name : '+driver_name+'</td>';
    tableData += '<tr>';
    tableData += '<tr>';
    tableData += '<td colspan="2">Route : '+route_name+'</td>';
    tableData += '<td colspan="3">SalesMan Name : '+salesman_name+'</td>';
    tableData += '<tr>';
    tableData += '<tr>';
    tableData += '<td colspan="2">Challan ID : '+challanID+'</td>';
    tableData += '<td colspan="3">Loader Name : '+loder_name+'</td>';
    tableData += '<tr>';
    tableData += '<tbody>';
    tableData += '</table>';
    
    tableData += '<br>';
    tableData += '<table  border="1" cellpadding="0" cellspacing="0" width="80%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
    tableData += '<br>';
    tableData += '<table  border="1" cellpadding="0" cellspacing="0" width="80%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[1].innerHTML+'</table>';
    tableData += '<br>';
    tableData += '<table style="width:100%">';
    tableData += '<tr>';
    
    tableData += '<td width = "50%">Total Crates Received &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;&nbsp;&nbsp;&nbsp;'+parseFloat(refund_crates).toFixed(2)+'</td>';
    tableData += ' <td width = "25%">2000 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    tableData += '<tr>';
    tableData += '<td rowspan="9" style = "vertical-align: top;">Total Cash Received &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;&nbsp;'+parseFloat(case_depo).toFixed(2)+' <br> Less Expenses &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :  &nbsp;&nbsp;&nbsp;&nbsp;'+parseFloat(total_expense).toFixed(2)+'  <br> Net Cash Received Rs. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;&nbsp;'+parseFloat(NetCashRecvd).toFixed(2)+'</td>';
    tableData += '<td width = "25%">500 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    tableData += '<tr>';
    tableData += '<td width = "25%">200 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    
    tableData += '<tr>';
    tableData += '<td width = "25%">100 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    
    tableData += '<tr>';
    tableData += '<td width = "25%">50 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    
    tableData += '<tr>';
    tableData += '<td width = "25%">20 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    
    tableData += '<tr>';
    tableData += '<td width = "25%">10 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    
    tableData += '<tr>';
    tableData += '<td width = "25%">5 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    
    tableData += '<tr>';
    tableData += '<td width = "25%">2 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    
    tableData += '<tr>';
    tableData += '<td width = "25%">1 X</td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += ' <td width = "12.5%"></td>';
    tableData += '</tr>';
    tableData += '</table>';
    tableData += '<br>';
    tableData += '<footer>';
    tableData += '<span>Prepared By '+Uname+'</span>';
    tableData += '</footer>';
       
    var print_data = stylesheet+heading_data+tableData
   newWin= window.open("");
   newWin.document.write(print_data);
   newWin.print();
   newWin.close();
 };
</script>