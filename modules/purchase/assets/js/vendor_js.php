<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
/**
 * Included in application/views/admin/clients/client.php
 */
?>
<script>
<?php if(isset($client)){ ?>

   $(function(){
      init_rel_tasks_table(<?php echo html_entity_decode($client->userid); ?>,'customer');
      initDataTable('.table-table_contracts', admin_url+'purchase/table_vendor_contracts/'+<?php echo html_entity_decode($client->userid); ?>);

      initDataTable('.table-table_pur_order', admin_url+'purchase/table_vendor_pur_order/'+<?php echo html_entity_decode($client->userid); ?>);
   });


<?php } ?>

Dropzone.options.clientAttachmentsUpload = false;
var customer_id = $('input[name="userid"]').val();
$(function() {

    "use strict"; 
    var optionsHeading = [];
      var allContactsServerParams = {
       "custom_view": "[name='custom_view']",
     }
     <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
      optionsHeading.push($('#th-consent').index());
      <?php } ?>
      var _table_api = initDataTable('.table-all-contacts', window.location.href, optionsHeading, optionsHeading, allContactsServerParams, [0,'asc']);
      if(_table_api) {
       <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
        _table_api.on('draw', function () {
          var tableData = $('.table-all-contacts').find('tbody tr');
          $.each(tableData, function() {
            $(this).find('td:eq(2)').addClass('bg-light-gray');
          });
        });
        $('select[name="custom_view"]').on('change', function(){
          _table_api.ajax.reload()
          .columns.adjust()
          .responsive.recalc();
        });
        <?php } ?>
      }

    if ($('#client-attachments-upload').length > 0) {
        new Dropzone('#client-attachments-upload', appCreateDropzoneOptions({
            paramName: "file",
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    window.location.reload();
                }
            }
        }));
    }

    // Save button not hidden if passed from url ?tab= we need to re-click again
    if (tab_active) {
        $('body').find('.nav-tabs [href="#' + tab_active + '"]').click();
    }

    $('a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').addClass('hide');
    });

    $('.profile-tabs a').not('a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').removeClass('hide');
    });

    $("input[name='tasks_related_to[]']").on('change', function() {
        var tasks_related_values = []
        $('#tasks_related_filter :checkbox:checked').each(function(i) {
            tasks_related_values[i] = $(this).val();
        });
        $('input[name="tasks_related_to"]').val(tasks_related_values.join());
        $('.table-rel-tasks').DataTable().ajax.reload();
    });

    var contact_id = get_url_param('contactid');
    if (contact_id) {
        get_url_param(customer_id, contact_id);
    }

    // consents=CONTACT_ID
    var consents = get_url_param('consents');
    if(consents){
        view_contact_consent(consents);
    }

    // If user clicked save and add new contact
    if (get_url_param('new_contact')) {
        vendor_contact(customer_id);
    }

    $('body').on('change', '.onoffswitch input.customer_file', function(event, state) {
        var invoker = $(this);
        var checked_visibility = invoker.prop('checked');
        var share_file_modal = $('#customer_file_share_file_with');
        setTimeout(function() {
            $('input[name="file_id"]').val(invoker.attr('data-id'));
            if (checked_visibility && share_file_modal.attr('data-total-contacts') > 1) {
                share_file_modal.modal('show');
            } else {
                do_share_file_contacts();
            }
        }, 200);
    });

    $('.customer-form-submiter').on('click', function() {
        // alert('test');return false;
        var form = $('.vendor-form');
        if (form.valid()) {
            if ($(this).hasClass('save-and-add-contact')) {
                form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
    });

    if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
        document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
            success: function(files) {
                saveCustomerProfileExternalFile(files, 'dropbox');
            },
            linkType: "preview",
            extensions: app.options.allowed_files.split(','),
        }));
    }

    

    /* Custome profile contacts table */
    var contactsNotSortable = [];
    <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
        contactsNotSortable.push($('#th-consent').index());
    <?php } ?>
    _table_api = initDataTable('.table-vendor_contacts', admin_url + 'purchase/vendor_contacts/' + customer_id, contactsNotSortable, contactsNotSortable);
    if(_table_api) {
          <?php if(is_gdpr() && get_option('gdpr_enable_consent_for_contacts') == '1'){ ?>
        _table_api.on('draw', function () {
            var tableData = $('.table-vendor_contacts').find('tbody tr');
            $.each(tableData, function() {
                $(this).find('td:eq(1)').addClass('bg-light-gray');
            });
        });
        <?php } ?>
    }


    var vRules = {};
    if (app.options.company_is_required == 1) {
        vRules = {
            company: 'required',
            state: 'required',
            city: 'required',
            account_group: 'required',
            Mobile_number: 'required',
            vendor_code: {
				required: true,
				remote: {
					url: site_url + "admin/misc/accountID_exists",
					type: 'post',
					data: {
						AccountID: function() {
							return $('input[name="vendor_code"]').val();
						},
						memberid: function() {
							return $('input[name="userid"]').val();
						}
					}
				}
			},
           
        }
    }

    appValidateForm($('.vendor-form'), vRules);

    if(typeof(customer_id) == 'undefined'){
        $('#company').on('blur', function() {
            var company = $(this).val();
            var $companyExistsDiv = $('#company_exists_info');

            if(company == '') {
                $companyExistsDiv.addClass('hide');
                return;
            }

            $.post(admin_url+'clients/check_duplicate_customer_name', {company:company})
            .done(function(response) {
                if(response) {
                    response = JSON.parse(response);
                    if(response.exists == true) {
                        $companyExistsDiv.removeClass('hide');
                        $companyExistsDiv.html('<div class="info-block mbot15">'+response.message+'</div>');
                    } else {
                        $companyExistsDiv.addClass('hide');
                    }
                }
            });
        });
    }
  var now = new Date();

var day = ("0" + now.getDate()).slice(-2);
var month = ("0" + (now.getMonth() + 1)).slice(-2);

var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

    $('select[name="istcs"]').addClass("tcs_type");
    
    $('input[name="TcsStartDate1"]').addClass("tcs_date");
    $('.tcs_date').attr('disabled', 'disabled');
    
    var value1 = $('select[name="istcs"]').val();
    if(value1 == '0'){
        $('.tcs_date').attr('disabled', 'disabled');
            //alert(value);
            $('input[name="TcsStartDate1"]').val('');
            $('input[name="TcsStartDate"]').val('');
    }
    /*if(value1 == '1'){
        $('input[name="TcsStartDate"]').val(today);
    $('input[name="TcsStartDate1"]').val(today);
    }*/
    
    $('body').on('change', '.tcs_type', function(event, state) {
        
        var value = $('select[name="istcs"]').val();
        
       //alert(value);
       if(value == '1'){
           
            //$('.tcs_date').removeAttr('disabled');
            $('input[name="TcsStartDate1"]').val(today);
            $('input[name="TcsStartDate"]').val(today);
           
            //alert(value);
        }
        if(value == '0'){
           
            $('.tcs_date').attr('disabled', 'disabled');
            //alert(value);
            $('input[name="TcsStartDate"]').val('');
            $('input[name="TcsStartDate1"]').val('');
            
        }
        
    });
  $('#state').on('change', function() {
				var id = $(this).val();
				//alert(roleid);
				var url = "<?php echo base_url(); ?>admin/hr_profile/select_city";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {id: id},
                        dataType:'json',
                        success: function(data) {
                           
                            $(".city").html(data);
                            //alert(data);
                            
                        }
                    });
			});
		
   
    
    $('body').on('hidden.bs.modal', '#contact', function() {
        $('#contact_data').empty();
    });

    $('.vendor-form').on('submit', function() {
        $('select[name="default_currency"]').prop('disabled', false);
    });

});

</script>
<script>
   function validateZipCode(elementValue){
  var zipCodePattern = /^\d{5}$|^\d{5}-\d{4}$/;
  return zipCodePattern.test(elementValue);
}
</script>
<script>

    $('#pan').keyup(function(e) {
        var val = $('#pan').val();
        if(val == ""){
            $(".pan_denger").text(" ");
        }else{
            e.preventDefault();
            if(!$('#pan').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}'))  {
                $(".pan_denger").text("Enter valid PAN number");
            }else{
                $(".pan_denger").text(" ");
            }
        }
    });
    
    $('#vat').keyup(function(e) {
        var val = $('#vat').val();
        if(val == ""){
             $(".gst_denger").text(" ");
        }else{
            e.preventDefault();
            if(!$('#vat').val().match('([0-9]){2}([A-Za-z]){5}([0-9]){4}([A-Za-z]){1}([0-9]{1})([0-9A-Za-z]){2}')) {
                $(".gst_denger").text("Enter valid GST number");
            }else{
                $(".gst_denger").text(" ");
            }
        }
        
    });
    
    
    $('#adhaar').keyup(function(e) {
        var val = $('#adhaar').val();
        if(val == ""){
             $(".aadhar_denger").text(" ");
             return true;
        }else{
            e.preventDefault();
            if(!$('#adhaar').val().match('[0-9]{12}'))  {
                return false;
                $(".aadhar_denger").text("Enter valid 12 digit Aadhar number");
            }else{
                $(".aadhar_denger").text(" ");
                return true;
            }
        }
        
    });
</script>
<script>
    function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode = 46 && charCode > 31 
            && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
    }
</script>
<script type="text/javascript">
   $('#opening_b').on('keypress',function (event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 45 || event.which > 57)) {
        event.preventDefault();
    }
    var input = $(this).val();
    if ((input.indexOf('.') != -1) && (input.substring(input.indexOf('.')).length > 2)) {
        event.preventDefault();
    }
});
</script>
