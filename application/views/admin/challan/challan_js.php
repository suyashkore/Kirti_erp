<?php

// write code for Challan view 
?>
<script>
    $('#challan_route').on('change', function() {
				var id = $(this).val();
				//alert(roleid);
				var url = "<?php echo base_url(); ?>admin/challan/get_order_by_route2";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {id: id},
                        dataType:'json',
                        beforeSend: function () {
               
                            $('#searchh2').css('display','block');
                            $('.showtable').css('display','none');
                            
                         },
                        complete: function () {
                                                
                            $('.showtable').css('display','');
                            $('#searchh2').css('display','none');
                         },
                        success: function(data) {
                           $("#rate_changeID").val("0");
                           $("#new_record").val(" ");
                            $(".updateRate_btn").css("display","none");
                            $(".save_challan").css("display","");
                            $(".showtable").html(data);
                            //alert(data);
                            
                        }
                    });
			});
	$('.updateRate_btn').on('click', function() {
				var RCHID = $("#new_record").val();
				//alert(roleid);
				var url = "<?php echo base_url(); ?>admin/challan/update_rate";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {RCHID: RCHID},
                        dataType:'json',
                        
                        success: function(data) {
                           //alert("rate updated");
                           if(data == true){
                               location.reload(true);
                           }
                           
                        }
                    });
			});
			
	$('#challan_vehicle').on('change', function() {
				var id = $(this).val();
				if(id == "TV"){
				    $("#custom_vehicle_number").css("display","");
				    //remove it
                    //$("#vahicle_capacity1").removeAttr("disabled");
                    $(".chldr").css("display","none");
                    $("#capacity_div").hide();
                    
				}else{
				    $("#custom_vehicle_number").css("display","none");
				    //add disabled
                    //$("#vahicle_capacity1").attr('disabled', 'disabled');
                    $(".chldr").css("display","");
                    $("#capacity_div").show();
				}
				//alert(id);
				var url = "<?php echo base_url(); ?>admin/challan/get_vehicle_detail";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {id: id},
                        dataType:'json',
                        success: function(data) {
                           
                            //$(".show").html(data);
                            if(data){
                                $("#vahicle_capacity").val(data["VehicleCapacity"]);
                                $("#vahicle_capacity1").val(data["VehicleCapacity"]);
                            }else{
                                $("#vahicle_capacity").val(" ");
                                $("#vahicle_capacity1").val(" ");
                            }
                            
                            
                            //alert(data);
                            
                        }
                    });
			});
	
	
        
    $('.number1').on('blur', function() {
        var NestId = $(".number1").val();
        var url = admin_url + 'challan/list_challan/' + NestId;
        window.location.href = url;
     });
        
    $('#challan_form').on('submit', function() {
        var chlval = $("#txtchalanvalue").val();
        if(chlval == '0.00' || chlval == '0'){
            
            alert("please seletc atleast one order");
            return false
        }else{
            return true
        }
        
     });
        
	$(document).on('click', '.chk', function () {
            currentRows = $(this).closest("tr");
            //alert(aa)
            ChallanValues();
           
            var aa = currentRows.find("input[type=checkbox]:checked").val();
            var ab = currentRows.find("input[name=rate_change]").val();
            var orderID = currentRows.find("input[name=OrderID]").val();
            
            //alert(orderID);
            if (aa){
                currentRows.addClass("bg-an");
                $(".transaction-submit").removeAttr("disabled");
                //$('#submit').prop("disabled", false);
                if(ab == "Y"){
                    
                    var new_rec = $('#new_record').val();
                    new_rec = new_rec +","+ orderID
                    $('#new_record').val(new_rec);
                    
                    var rate_changeID = $("#rate_changeID").val();
                    var new_cont = parseInt(rate_changeID) + 1;
                    $("#rate_changeID").val(new_cont);
                }
                updateRate();
            }else {
                currentRows.removeClass("bg-an");
                if(ab == "Y"){
                    var new_rec = $('#new_record').val();
                    $new_item_code = ','+orderID;
                    new_rec = new_rec.replace($new_item_code, " ");
                    $('#new_record').val(new_rec);
                    
                    var rate_changeID = $("#rate_changeID").val();
                    var new_cont = parseInt(rate_changeID) - 1;
                    $("#rate_changeID").val(new_cont);
                }
                updateRate();
            }
        });
        
        function ChallanValues() {
            var x = document.getElementById("challan_data").rows[0].cells.length;
            var challanTotal = 0, cratetotal = 0, casetotal = 0, tcsamt = 0;
            var a = x - 5;
            var b = x - 4;
            var c = x - 6;
            var d = x;

            $('#challan_data tbody input[type=checkbox]:checked').each(function (i, row) {
                var row = $(this).closest("tr")[0];
                
                //row.addClass("bg-primary")
                $(row).find('td').each(function (index, r) {
                    if (index == a) {
                        //Case values
                        var h = r.innerText;
                        casetotal += isNaN(parseFloat(h)) ? 0 : parseFloat(h);
                        //console.log(row);
                    }
                    if (index == b) {
                        //challan values
                        var h = r.innerText;
                        challanTotal += isNaN(parseFloat(h)) ? 0 : parseFloat(h);
                        //console.log(h);
                    }
                    if (index == c) {
                        //crate values
                        var h = r.innerText;
                        cratetotal += isNaN(parseFloat(h)) ? 0 : parseFloat(h);
                        
                    }
                    if (index == d) {
                        //crate values
                        var h = r.innerText;
                        tcsamt += isNaN(parseFloat(h)) ? 0 : parseFloat(h);
                        //console.log(h);
                    }
                })
            });
            $('#txtCrates').val(cratetotal);
            $('#txtCases').val(casetotal);
            $('#txtCrates1').val(cratetotal);
            $('#txtCases1').val(casetotal);
            $('#txtchalanvalue').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
            $('#txtchalanvalue1').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
        }
        
        function total() {
             /*GetRightTotal();
            GetBottomTotal();
            ChallanValues();*/
        }
        function updateRate() {
            var rate_changeID_val = $("#rate_changeID").val();
            if(rate_changeID_val == "0"){
                $(".updateRate_btn").css("display","none");
                $(".save_challan").css("display","");
            }else{
                $(".updateRate_btn").css("display","");
                $(".save_challan").css("display","none");
            }
        }
        
        function GetRightTotal() {
            var tcper = 0;
            var len = $('#challan_data tr')[0].cells.length;
            $('#challan_data tbody').find('tr').each(function (k, v) {
                var totcase = 0; var totcrate = 0; tq = 0; var totorder = 0; var tcase = 0; var tcrate = 0; var totordsale = 0; var tcsper = 0;
                $(this).find('td').each(function (k, v) {
                    var hdval = $(this).find('input[type="hidden"]').val();
                    if (hdval != undefined) {
                        var cscr = CSCR(hdval);
                        //alert(cscr);
                        // var q = qty(hdval);
                        var r = rate(hdval);
                        var pq = pkg(hdval);
                        var g = gst(hdval);
                        
                        /*var dper = discper(hdval);
                        var damt = disamt(hdval);
                        var igp = igstper(hdval);
                        var sgp = sgstper(hdval);
                        var cgp = cgstper(hdval);*/

                        if (cscr == 'Case') {
                            var cqty = $(this).find('input[type="text"]').val();
                            //var cqty = qty(hdval);
                            tcase = isNaN(parseFloat(cqty)) ? 0 : parseFloat(cqty);
                            totcase += tcase;
                            var amt = totcase * r * pq;
                            
                        }
                        else if (cscr == 'Crate') {
                            var crqty = $(this).find('input[type="text"]').val();
                            //var crqty = qty(hdval);
                            tcrate = isNaN(parseFloat(crqty)) ? 0 : parseFloat(crqty);
                            totcrate += tcrate;
                            var amt = totcrate * r * pq;
                        }
                        //var tq = totcase + totcrate;
                        
                        /*var cqty = $(this).find('input[type="text"]').val();
                            //var cqty = qty(hdval);
                            tcase = isNaN(parseFloat(cqty)) ? 0 : parseFloat(cqty);
                            totcase += tcase;*/
                        
                        //var amt = totcase * r * pq;
                        amt = isNaN(parseFloat(amt)) ? 0 : parseFloat(amt);
                        
                        var GSTA = amt * g / 100;
                        var saleamt = amt + GSTA;
                        
                        /*var IGSTA = amt * igp / 100;
                        var SGSTA = amt * sgp / 100;
                        var CGSTA = amt * cgp / 100;
                        IGSTA = isNaN(parseFloat(IGSTA)) ? 0 : parseFloat(IGSTA);
                        SGSTA = isNaN(parseFloat(SGSTA)) ? 0 : parseFloat(SGSTA);
                        CGSTA = isNaN(parseFloat(CGSTA)) ? 0 : parseFloat(CGSTA);
                        var tamt = 0;
                        if (IGSTA > 0)
                            tamt = amt + IGSTA;
                        else
                            tamt = amt + SGSTA + CGSTA;

                        tamt = isNaN(parseFloat(tamt)) ? 0 : parseFloat(tamt);
                        var dpamt = tamt * dper / 100;
                        dpamt = isNaN(parseFloat(dpamt)) ? 0 : parseFloat(dpamt);
                        var tdisamt = dpamt + damt;
                        tdisamt = isNaN(parseFloat(tdisamt)) ? 0 : parseFloat(tdisamt);
                        var saleamt = tamt - tdisamt;*/
                        saleamt = isNaN(parseFloat(saleamt)) ? 0 : parseFloat(saleamt);
                        //dsamt += damt;
                        totorder += amt;
                        totordsale += saleamt;
                    }
                });
                //tcsper = parseFloat($(this).find('td').eq(5).find('input[type="hidden"]').val());
                //$(this).find('td').eq(len - 2).html(tcsper);
                $(this).find('td').eq(len - 6).html(totcrate);
                $(this).find('td').eq(len - 5).html(totcase);
                $(this).find('td').eq(len - 4).html(parseFloat(totorder).toFixed(2));
                $(this).find('td').eq(len - 3).html(parseFloat(totordsale).toFixed(2));

                $(this).find('td:last-child').html(Number(parseFloat(totordsale * tcsper / 100).toFixed(2)));
               $(this).find('td:last-child').addClass('textright');
            });
        }
        
        function GetBottomTotal() {
            var x = document.getElementById("challan_data").rows[0].cells.length;
            var totalRow = '';
            for (var index = 7; index < x; index++) {
                var total = 0;
                $('#challan_data tbody tr').each(function () {
                    //if (index == x - 1 || index == x - 2 || index == x - 3 || index == x - 4) {
                    if (index <= x - 1 && index >= x - 6) {
                        total += isNaN(parseFloat($('td', this).eq(index).html())) ? 0 : parseFloat($('td', this).eq(index).html());
                    }
                    else {
                        total += isNaN(parseFloat($('td', this).eq(index).find('input[type="text"]').val())) ? 0 : parseFloat($('td', this).eq(index).find('input[type="text"]').val());
                    }
                });
                totalRow += '<td style="text-align:right">' + parseFloat(total).toFixed(2) + '</td>';
                $('#challan_data tbody input[type="text"]').on('change', function () {
                    $(this).css({ 'font- weight': 'bold', 'color': 'red' });//'border': '1px solid red',
                })
            }
            $('#challan_data tfoot tr').remove();
            $('#challan_data tfoot').append('<tr><td>Total</td><td></td><td></td><td></td><td></td><td></td><td></td>' + totalRow + '</tr>');
        }
        
        function rate(str) {
            var s = str.split('-');
            var q = parseFloat(s[2]);
            q = isNaN(q) ? 0 : q.toFixed(2);
            return q;
        }
        function CSCR(str) {
            var s = str.split('-');
            var q = s[4];
            //q = isNaN(q) ? 0 : q;
            return q;
        }
        function pkg(str) {
            var s = str.split('-');
            var q = parseFloat(s[1]);
            q = isNaN(q) ? 0 : q.toFixed(2);
            return q;
        }
        function gst(str) {
            var s = str.split('-');
            var q = parseFloat(s[3]);
            q = isNaN(q) ? 0 : q.toFixed(2);
            return q;
        }

        
</script>