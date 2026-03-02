
<script>
	function GetRouteOrder(routeid) {
		$('#challan_route').val(routeid);
		$('#challan_route').change();
        // var id = routeid;
        
	}
    $('#challan_route').on('change', function() {
        var id = $(this).val();
        //alert(roleid);
        var url = "<?php echo base_url(); ?>admin/challan/get_order_by_routeNew";
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
				$("#txtchalanvalue1").val("0.00");
				$("#txtchalanvalue").val("0.00");
				$("#txtCases1").val("0");
				$("#txtCases").val("0");
				$("#txtCrates1").val("0");
				$("#txtCrates").val("0");
				$("#rate_changeID").val("0");
				$("#new_record").val(" ");
				$(".updateRate_btn").css("display","none");
				$(".save_challan").css("display","");
				$(".showtable").html(data);
				
				attachSequenceRules($(".showtable"));
				//alert(data);
				
			}
		});
	});
    $('#CrateUpdateButton').on('click', function() {
		var ChallanID = $('#ChallanID').val();
		
        let CrateData = {};
        let CaseData = {};
		// Loop through each checked order_id[] checkbox
		$('input[name="order_id[]"]:checked').each(function () {
			let orderId = $(this).val(); // Get the OrderID
			let crateValue = $('input[name="crates_' + orderId + '"]').val(); // Get corresponding crate input
			let caseValue = $('input[name="cases_' + orderId + '"]').val(); // Get corresponding crate input
			CrateData[orderId] = crateValue;
			CaseData[orderId] = caseValue;
		});
		
		 var url = "<?php echo base_url(); ?>admin/challan/UpdateCratesCasesAfterGatepass";
		jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ChallanID: ChallanID,CrateData: CrateData,CaseData: CaseData},
            dataType:'json',
            success: function(data) {
				if(data == true){
					alert_float('success','Updated Successfully');
					location.reload();
				}
			}
		})
	});
    $('.InvoicePrint').on('click', function() {
        var ChallanID = $('#ChallanID').val();
        var url = "<?php echo base_url(); ?>admin/challan/GetTaxableTransaction";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ChallanID: ChallanID},
            dataType:'json',
            success: function(data) {
				
                var Link = '<?php echo admin_url(); ?>challan/pdf/'+ChallanID+'?output_type=I';
                var NotMAtch = 0;
                for(var count = 0; count < data.length; count++)
                {
                    if(data[count].PlantID !== "3"){
                        if(data[count].gstno == null || data[count].gstno == ''){
							
							}else{
                            if(data[count].irn == null && data[count].BillAmt > 0){
                                NotMAtch++;
							}
						}
					}
				}
                // if(NotMAtch == 0){
				//alert(NotMAtch)
				window.open(Link,'_blank');
				// }else{
				//alert(NotMAtch)
				// alert('Please create E-invoice for all GST registered parties...');
				// }
			}
		})
	})
	$('.RouteMemo').on('click', function() {
        var ChallanID = $('#ChallanID').val();
        var url = "<?php echo base_url(); ?>admin/challan/GetTaxableTransaction";
        jQuery.ajax({
            type: 'POST',
            url:url,
            data: {ChallanID: ChallanID},
            dataType:'json',
            success: function(data) {
				
                var Link = '<?php echo admin_url(); ?>challan/RouteMemo/'+ChallanID+'?output_type=I';
                var NotMAtch = 0;
                for(var count = 0; count < data.length; count++)
                {
                    if(data[count].PlantID !== "3"){
                        if(data[count].gstno == null || data[count].gstno == ''){
							
							}else{
                            if(data[count].irn == null && data[count].BillAmt > 0){
                                NotMAtch++;
							}
						}
					}
				}
				window.open(Link,'_blank');
                // if(NotMAtch == 0){
				// window.open(Link,'_blank');
				// }else{
				// alert('Please create E-invoice for all GST registered parties...');
				// }
			}
		})
	})
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
        
        /*var len = $('#challan_data tr')[0].cells.length;
            $('#challan_data tbody').find('tr').each(function (k, v) {
			$(this).find('td').each(function (k, v) {
			$(this).find("input[type=checkbox]").prop('checked', false); // Unchecks it
			
			})
			$(this).removeClass("bg-an");
		})*/
        if(id == "TV" || id == "SELF"){
            $("#custom_vehicle_number").css("display","");
			$(".chldr").css("display","none");
			if(id == "SELF"){
				$(".chlvhl").css("display","none");
			}
			$("#capacity_div").hide();
			
			}else{
            $("#custom_vehicle_number").css("display","none");
			$(".chldr").css("display","");
			$(".chlvhl").css("display","");
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
				if(data){
					if(data["ActiveYN"] == '1'){
						$("#vahicle_capacity").val(data["VehicleCapacity"]);
						$("#vahicle_capacity1").val(data["VehicleCapacity"]);
						$("#challan_driver").val(data.DriverID);
						$('.selectpicker').selectpicker('refresh');
						}else{
						alert('This Vehicle Is Not Available');
						$("#challan_vehicle").val('');
						$('.selectpicker').selectpicker('refresh');
						$("#vahicle_capacity").val(" ");
						$("#vahicle_capacity1").val(" ");
					}
					}else{
					$("#vahicle_capacity").val(" ");
					$("#vahicle_capacity1").val(" ");
				}
			}
		});
	});
	
	
	
    $('.number1').on('blur', function() {
        var NestId = $(".number1").val();
        var url = admin_url + 'challan/list_challan/' + NestId;
        window.location.href = url;
	});
	
    
	
    /*$('#challan_form').on('submit', function() {
        var chlval = $("#txtchalanvalue").val();
        if(chlval == '0.00' || chlval == '0'){
		alert("please select atleast one order");
		return false
        }else{
		return true
        }
        
	});*/
	
    $('#challan_form').on('submit', function() {
		var chlval = $("#txtchalanvalue").val();
		var ORDChecked = $("input[type=checkbox]:checked").val();
		if(ORDChecked == "undefined"){
			alert("please select atleast one order");
			return false;
			}else{
			return true;
		}
		/*alert(aa);
		return false*/
		if(chlval == '0.00' || chlval == '0'){
			alert("Challan value must be grater than 0");
			return false
			}else{
			return true
		}
	});
    
	
	$(document).on('click', '.chk', function () {
		currentRows = $(this).closest("tr");
		//alert(aa)
		var Vehicle = $('#challan_vehicle').val();
		var MaxCreditLimit = $('#MaxCreditLimit').val();
		var OBal = currentRows.find("input[name=Balance]").val();
		var MaxCrdAmt = currentRows.find("input[name=MaxCrdAmt]").val();
		var FBillAmt = currentRows.find("input[name=FBilAmt]").val();
		//alert(FBillAmt);
		// if(OBal < 0 || MaxCreditLimit == "N" || MaxCrdAmt == "0.00"){
		// if(Math.abs(OBal) >= FBillAmt || MaxCreditLimit == "N" || MaxCrdAmt == "0.00"){
		//alert('Bill Amt Valid');   
		
		/*if(Vehicle == ""){
			alert('please select vehicle first..');
			currentRows.find("input[type=checkbox]").prop('checked', false); // Unchecks it
			}else{
			var status = ChallanValues();
			if(status == false){
			currentRows.find("input[type=checkbox]").prop('checked', false); // Unchecks it
			alert('Vehicle Capacity Overlloaded please select anather vehicle..');
		}else{*/
		
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
		/*}
		}*/
		// }else{
		// alert('Max credit limit exceeds');
		// currentRows.find("input[type=checkbox]").prop('checked', false); // Unchecks it
		// }
		// }else{
		// alert('Max credit limit exceeds');
		// currentRows.find("input[type=checkbox]").prop('checked', false); // Unchecks it
		// }
	});
	
	function ChallanValues() {
		var x = document.getElementById("challan_data").rows[0].cells.length;
		var challanTotal = 0, cratetotal = 0, casetotal = 0, tcsamt = 0;
		var a = x - 10;
		var b = x - 1;
		var c = x - 11;
		var d = x;
		
		$('#challan_data tbody input[type=checkbox]:checked').each(function (i, row) {
			var row = $(this).closest("tr")[0];
			
			//row.addClass("bg-primary")
			$(row).find('td').each(function (index, r) {
				if (index == a) {
					//Case values
					var h = $(r).find('input').val();
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
					var h = $(r).find('input').val();
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
		var vahicle_capacity = $('#vahicle_capacity').val();
		if(vahicle_capacity !== ''){
			if(parseFloat(cratetotal) > parseFloat(vahicle_capacity)){
				alert('Crates Quantity Is Greater Than Vehicle Capacity');
			}
		}
		$('#txtCrates').val(cratetotal);
		$('#txtCases').val(casetotal);
		$('#txtCrates1').val(cratetotal);
		$('#txtCases1').val(casetotal);
		$('#txtchalanvalue').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
		$('#txtchalanvalue1').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
		GetBottomTotal();
		return true;
		/*var VCapacity = $("#vahicle_capacity").val();
            var Vehicle = $('#challan_vehicle').val();
            if(Vehicle == "TV"){
			$('#txtCrates').val(cratetotal);
			$('#txtCases').val(casetotal);
			$('#txtCrates1').val(cratetotal);
			$('#txtCases1').val(casetotal);
			$('#txtchalanvalue').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
			$('#txtchalanvalue1').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
			return true;
            }else{
			if(VCapacity == ""){
			$('#txtCrates').val(cratetotal);
			$('#txtCases').val(casetotal);
			$('#txtCrates1').val(cratetotal);
			$('#txtCases1').val(casetotal);
			$('#txtchalanvalue').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
			$('#txtchalanvalue1').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
			return true;
            }else if(VCapacity >= cratetotal){
			$('#txtCrates').val(cratetotal);
			$('#txtCases').val(casetotal);
			$('#txtCrates1').val(cratetotal);
			$('#txtCases1').val(casetotal);
			$('#txtchalanvalue').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
			$('#txtchalanvalue1').val(Number.parseFloat(challanTotal + tcsamt).toFixed(2));
			return true;
            }else if(VCapacity < cratetotal){
			return false;
            }
		}*/
	}
	
	function total(val,oldvl) {
		var name = val.name;
		var NewName = 'org'+name; 
		// var date = $('#date').val();
		var oldVal = val.value;
		$('#challan_data tbody').find('tr').each(function (k, v) {
			$(this).find('td').each(function (k, v) {
				var hdval = $(this).find('input[id="qtyhidden"]').val();
				if (hdval != undefined) {
					var qty = $(this).find('input[type="text"]').val();
					var name = $(this).find('input[type="text"]').attr("name");
					var ItemID = GETItemID(name); 
					var Stock = GETStock(hdval); 
					var State = GETState(hdval); 
					var DistType = GETDistType(hdval); 
					var date = GETDate(hdval); 
					var pq = pkg(hdval);
					
					if('org'+name == NewName){
						GetSchemeData(qty, DistType, State, date, ItemID, function(freeqty) {
							if (freeqty !== null) {
								$("input[name='free_" + name + "']").val(freeqty);// Handle the returned data here
							}
						});
					}
					//alert(Stock);
					if(qty == '0' || qty == '0.00'){
						
                        }else{
						if(parseFloat(Stock) < qty){
							$(this).find('input[type="text"]').css({ 'font- weight': 'bold', 'color': 'darkorange' });//'border': '1px solid red',
						}
					}
					
					/* var url = "<?php echo base_url(); ?>admin/challan/GetStockDetails";
                        jQuery.ajax({
						type: 'POST',
						url:url,
						data: {qty: qty,ItemID:ItemID,pq:pq},
						dataType:'json',
						success: function(data) {
						if(data == false){
						alert('Stock qty not available..');
						$('qty_ORD22300010_ZRT2').css({ 'font- weight': 'bold', 'color': 'red' });//'border': '1px solid red',
						}else{
						alert('Stock qty available..');
						}
						}
					});*/
				}
			});
		});
		GetRightTotal(name,oldVal,NewName);
		GetBottomTotal();
		ChallanValues();
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
	
	function GetRightTotal(name,oldVal,NewName) {
		var tcper = 0;
		var len = $('#challan_data tr')[0].cells.length;
		var CHLCrate = 0.00;
		var CHLCases = 0.00;
		var CHLAmt = 0.00;
		$('#challan_data tbody').find('tr').each(function (k, v) {
            
			var totcase = 0; var totcrate = 0; tq = 0; var totorder = 0; var tcase = 0; var tcrate = 0; var totordsale = 0; var tcsper = 0;
			var IGSTAmtSum = 0; var CGSTAmtSum = 0; var SGSTAmtSum = 0;var TotalDiscAmt = 0;
			$(this).find('td').each(function (k, v) {
				var hdval = $(this).find('input[id="qtyhidden"]').val();
				//alert(hdval);
				if (hdval != undefined) {
					var cscr = CSCR(hdval);
					var name = $(this).find('input[type="text"]').attr("name");
					var ItemID = GETItemID(name); 
					var r = rate(hdval);
					var pq = pkg(hdval);
					var g = gst(hdval);
					var state = stateval(hdval);
					var DiscPer = DiscPerval(hdval);
					var DistType = GETDistType(hdval); 
					var date = GETDate(hdval); 
					
					if (cscr == 'CS') {
						var cqty = $(this).find('input[type="text"]').val();
						tcase = isNaN(parseFloat(cqty)) ? 0 : (parseFloat(cqty)/parseFloat(pq));
						totcase += tcase;
						CHLCases += tcase;
                        }else if (cscr == 'CR') {
						var crqty = $(this).find('input[type="text"]').val();
						tcrate = isNaN(parseFloat(crqty)) ? 0 : (parseFloat(crqty)/parseFloat(pq));
						totcrate += tcrate;
						CHLCrate += tcrate;
						// alert(CHLCrate);
					}
					
					var qty = $(this).find('input[type="text"]').val();
					
					
					var saleAmt = qty  * r; // * pq Add If Needed
					var DiscAmt = saleAmt * (DiscPer /100);
					TotalDiscAmt = parseFloat(TotalDiscAmt) + parseFloat(DiscAmt);
					var TaxableAmt = saleAmt - DiscAmt;
					totordsale = parseFloat(totordsale) + parseFloat(saleAmt);
					
					if(state == "UK"){
						var CGSTPer = g /2;
						var CGSTAmt = (TaxableAmt / 100) * parseFloat(CGSTPer);
						var SGSTAmt = (TaxableAmt / 100) * parseFloat(CGSTPer);
						SGSTAmtSum = parseFloat(SGSTAmtSum) + parseFloat(SGSTAmt);
						CGSTAmtSum = parseFloat(CGSTAmtSum) + parseFloat(CGSTAmt);
						var BillAmt = parseFloat(TaxableAmt) + parseFloat(CGSTAmt) + parseFloat(SGSTAmt);
						}else{
						var IGSTAmt = (TaxableAmt / 100) * parseFloat(g);
						IGSTAmtSum = parseFloat(IGSTAmtSum) + parseFloat(IGSTAmt);
						var BillAmt = parseFloat(TaxableAmt) + parseFloat(IGSTAmt);
					}
					totorder = parseFloat(totorder) + parseFloat(BillAmt);
					
					// if('org'+name == NewName){
					// var url = "<?php echo base_url(); ?>admin/challan/GetSchemeData";
					// jQuery.ajax({
					// type: 'POST',
					// url: url,
					// data: {qty: qty, DistType: DistType, State: state, date: date, ItemID: ItemID},
					// dataType: 'json',
					// success: function(freeqty) {
					
					// if (freeqty !== null && freeqty >0) {
					
					// if (cscr == 'CS') {
					// tcase = isNaN(parseFloat(freeqty)) ? 0 : (parseFloat(freeqty)/parseFloat(pq));
					// totcase += tcase;
					// CHLCases += tcase;
					// }else if (cscr == 'CR') {
					// tcrate = isNaN(parseFloat(freeqty)) ? 0 : (parseFloat(freeqty)/parseFloat(pq));
					// totcrate += tcrate;
					// CHLCrate += tcrate;
					// }
					
					// TotalDiscAmt = TotalDiscAmt + (freeqty * r);
					// alert(TotalDiscAmt);
					// }
					// },
					// });
					
					
					// }
				}
			});
			
			//alert(totordsale);
			
			credit_apply = $(this).find('td').eq(0).find('input[name="credit_apply"]').val();
			PrevOrderAmt = parseFloat($(this).find('td').eq(0).find('input[name="PrevOrderAmt"]').val());
			
			tcsper = parseFloat($(this).find('td').eq(7).find('input[type="hidden"]').val());
			if(tcsper=="0.00"){
				tcsAmt = 0.00;
                }else{
				var tcsAmt = (parseFloat(totorder) / 100) * tcsper;
			}
			
			var finalBillAmt = parseFloat(totorder) +  parseFloat(tcsAmt);
			CHLAmt += finalBillAmt;
			var aa = $(this).find("input[type=checkbox]:checked").val();
			var FBillAmt = $(this).find('td:last-child').find('input[name="FBilAmt"]').val();
			var OBal = $(this).find('td').eq(1).find('input[name="Balance"]').val();
			//alert(OBal);
			//alert(finalBillAmt);
			var MaxCreditLimit = $('#MaxCreditLimit').val();
			var MaxCrdAmt = $(this).find("input[name=MaxCrdAmt]").val();
			// if(OBal < 0 || MaxCreditLimit == "N" || MaxCrdAmt == "0.00"){
				if(OBal >= finalBillAmt || MaxCreditLimit == "N" || MaxCrdAmt == "0.00" || credit_apply== "N" && finalBillAmt <= PrevOrderAmt){
					
					$('input[name="LastValue"]').val(oldVal);
					$('input[name="'+NewName+'"]').val(oldVal);
					//alert('Bill Amt Valid11');   
					// $(this).find('td input').eq(len - 11).val(Math.ceil(totcrate).toFixed(2));
					// $(this).find('td input').eq(len - 10).val(Math.ceil(totcase).toFixed(2));
					$(this).find('td').eq(len - 11).find('input[type="text"]').val(Math.ceil(totcrate).toFixed(2));
					$(this).find('td').eq(len - 10).find('input[type="text"]').val(Math.ceil(totcase).toFixed(2));
					$(this).find('td').eq(len - 9).html(parseFloat(totorder).toFixed(2));
					$(this).find('td').eq(len - 8).html(parseFloat(totordsale).toFixed(2));
					$(this).find('td').eq(len - 7).html(parseFloat(TotalDiscAmt).toFixed(2));
					$(this).find('td').eq(len - 6).html(parseFloat(CGSTAmtSum).toFixed(2));
					$(this).find('td').eq(len - 5).html(parseFloat(SGSTAmtSum).toFixed(2));
					$(this).find('td').eq(len - 4).html(parseFloat(IGSTAmtSum).toFixed(2));
					$(this).find('td').eq(len - 2).html(parseFloat(tcsAmt).toFixed(2));
					
					//$(this).find('td:last-child').html(Number(parseFloat(totordsale * tcsper / 100).toFixed(2)));
					var htmls = Number(parseFloat(finalBillAmt).toFixed(2))+'<input type="hidden" name="FBilAmt" id="FBilAmt" value="'+finalBillAmt+'">';
					$(this).find('td:last-child').html(htmls);
					$(this).find('td:last-child').addClass('textright');
                    }else{
					if(aa){
						alert('Max credit limit exceeds');
						var preVal = $('input[name="'+NewName+'"]').val();
						$('input[name="'+name+'"]').val(0);
					}
				}
                // }else{
				// if(aa){
					// alert('Max credit limit exceeds');
					// var preVal = $('input[name="'+NewName+'"]').val();
					// $('input[name="'+name+'"]').val(0);
				// }
			// }
		});
		
		$('#txtchalanvalue1').val(parseFloat(CHLAmt).toFixed(2));
		$('#txtchalanvalue').val(parseFloat(CHLAmt).toFixed(2));
		
		$('#txtCases1').val(parseFloat(CHLCases).toFixed(2));
		$('#txtCases').val(parseFloat(CHLCases).toFixed(2));
		
		$('#txtCrates1').val(parseFloat(CHLCrate).toFixed(2));
		$('#txtCrates').val(parseFloat(CHLCrate).toFixed(2));
	}
	
	function GetBottomTotal() {
		var x = document.getElementById("challan_data").rows[0].cells.length;
		var totalRow = '';
		for (var index = 8; index < x; index++) {
			var total = 0;
			$('#challan_data tbody tr').each(function () {
				//if (index == x - 1 || index == x - 2 || index == x - 3 || index == x - 4) {
				if (index <= x - 1 && index >= x - 9) {
					total += isNaN(parseFloat($('td', this).eq(index).html())) ? 0 : parseFloat($('td', this).eq(index).html());
				}
				else {
					total += isNaN(parseFloat($('td', this).eq(index).find('input[type="text"]').val())) ? 0 : parseFloat($('td', this).eq(index).find('input[type="text"]').val());
				}
			});
			totalRow += '<td style="text-align:right">' + parseFloat(total).toFixed(2) + '</td>';
			$('#challan_data tbody input[type="text"]').on('change', function () {
				$(this).css({ 'font- weight': 'bold', 'color': 'blue' });//'border': '1px solid red',
			})
		}
		$('#challan_data tfoot tr').remove();
		$('#challan_data tfoot').append('<tr><td>Total</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>' + totalRow + '</tr>');
	}
	
	function rate(str) {
		var s = str.split('_');
		var q = parseFloat(s[2]);
		q = isNaN(q) ? 0 : q.toFixed(2);
		return q;
	}
	function CSCR(str) {
		var s = str.split('_');
		var q = s[4];
		//q = isNaN(q) ? 0 : q;
		return q;
	}
	function pkg(str) {
		var s = str.split('_');
		var q = parseFloat(s[1]);
		q = isNaN(q) ? 0 : q.toFixed(2);
		return q;
	}
	function GETItemID(str) {
		var s = str.split('_');
		var q = s[2];
		//q = isNaN(q) ? 0 : q.toFixed(2);
		return q;
	}
	function gst(str) {
		var s = str.split('_');
		var q = parseFloat(s[3]);
		q = isNaN(q) ? 0 : q.toFixed(2);
		return q;
	}
	function stateval(str) {
		var s = str.split('_');
		var q = s[5];
		return q;
	}
	function GETStock(str) {
		var s = str.split('_');
		var q = s[6];
		return q;
	}
	function GETState(str) {
		var s = str.split('_');
		var q = s[5];
		return q;
	}
	function GETDistType(str) {
		var s = str.split('_');
		var q = s[8];
		return q;
	}
	function GETDate(str) {
		var s = str.split('_');
		
		
		// Parse the date string into a Date object
		var date = new Date(s[9]);
		
		// Extract the day, month, and year
		var day = date.getDate();
		var month = date.getMonth() + 1; // Months are zero-based
		var year = date.getFullYear();
		
		// Format the day and month to ensure two digits
		if (day < 10) {
			day = '0' + day;
		}
		if (month < 10) {
			month = '0' + month;
		}
		
		// Construct the desired date format
		var q = day + '/' + month + '/' + year;
		return q;
	}
	function DiscPerval(str) {
		var s = str.split('_');
		var q = s[7];
		return q;
	}
	
	function GetSchemeData(qty, DistType, State, date, ItemID, callback) {
		var url = "<?php echo base_url(); ?>admin/challan/GetSchemeData";
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: {qty: qty, DistType: DistType, State: State, date: date, ItemID: ItemID},
			dataType: 'json',
			success: function(data) {
				callback(data);
			},
			error: function(error) {
				console.error('Error:', error);
				callback(null); // or handle error as needed
			}
		});
	}
	
	
</script>