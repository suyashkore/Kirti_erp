(function($) {
  "use strict";
  $("input[data-type='currency']").on({
    keyup: function() {  
    
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
  });
})(jQuery);

function formatNumber(n) {
  "use strict"; 
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}
function formatCurrency(input, blur) {
  "use strict"; 
  var input_val = input.val();
  if (input_val === "") { return; }
  var original_len = input_val.length;
  var caret_pos = input.prop("selectionStart");
  if (input_val.indexOf(".") >= 0) {
    var decimal_pos = input_val.indexOf(".");
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);
    left_side = formatNumber(left_side);
    right_side = formatNumber(right_side);
    right_side = right_side.substring(0, 2);
    input_val = left_side + "." + right_side;

  } else {
    input_val = formatNumber(input_val);
    input_val = input_val;

  }
  input.val(input_val);
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}


function numberWithCommas(x) {
  "use strict";
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function removeCommas(str) {
  "use strict";
  return(str.replace(/,/g,''));
}

function contract_change(el){
	"use strict";
	if(el.value != ''){
		$.post(admin_url + 'purchase/contract_change/'+el.value).done(function(response) {
			response = JSON.parse(response);
			$('#subtotal').val(numberWithCommas(response.value));
			$('#total').val(numberWithCommas(response.value));
		});
	}
}

function pur_order_change(el){
  "use strict";
  if(el.value != ''){
    $.post(admin_url + 'purchase/pur_order_change/'+el.value).done(function(response) {
      response = JSON.parse(response);
      $('#subtotal').val(numberWithCommas(response.value));
      $('#total').val(numberWithCommas(response.value));
    });
  }
}

function subtotal_change(el){
  "use strict";
  var tax = $('#tax').val();
  if(tax == ''){
    tax = '0';
  }
  var total_value =  parseFloat(removeCommas(el.value)) + parseFloat(removeCommas(tax));
  $('#total').val( numberWithCommas(total_value) );
}

function tax_rate_change(el){
	"use strict";
	var subtotal = $('#subtotal').val();
	var tax = $('#tax').val();
	var total = $('#total').val();
	if(el.value != ''){
		$.post(admin_url + 'purchase/tax_rate_change/'+el.value).done(function(response) {
			response = JSON.parse(response);
			var tax_value = parseFloat(removeCommas(subtotal)*response.rate)/100;
			var total_value =  parseFloat(removeCommas(subtotal)) +  tax_value;
			$('#tax').val(numberWithCommas(tax_value));
			$('#total').val( numberWithCommas(total_value) );
		});
	}
}