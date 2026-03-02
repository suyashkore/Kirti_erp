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

function get_sales_notes_inv(id, controller) {
  "use strict";
    requestGet(controller + '/get_notes_pur_invoice/' + id).done(function(response) {
        $('#sales_notes_area').html(response);
        var totalNotesNow = $('#sales-notes-wrapper').attr('data-total');
        if (totalNotesNow > 0) {
            $('.notes-total').html('<span class="badge">' + totalNotesNow + '</span>').removeClass('hide');
        }
    });
}

function add_payment(){
  "use strict"; 
   appValidateForm($('#purinvoice-add_payment-form'),{amount:'required', date:'required'});
   $('#payment_record_pur').modal('show');
   $('.edit-title').addClass('hide');
   $('#additional').html('');
}

function preview_purinv_btn(invoker){
  "use strict"; 
    var id = $(invoker).attr('id');
    var rel_id = $(invoker).attr('rel_id');
    view_purinv_file(id, rel_id);
}

function view_purinv_file(id, rel_id) {
  "use strict"; 
      $('#purinv_file_data').empty();
      $("#purinv_file_data").load(admin_url + 'purchase/file_purinv/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}
function close_modal_preview(){
  "use strict"; 
 $('._project_file').modal('hide');
}

function delete_purinv_attachment(id) {
  "use strict"; 
    if (confirm_delete()) {
        requestGet('purchase/delete_purinv_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("#purinv_pv_file").find('[data-attachment-id="' + id + '"]').remove();
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
  }
