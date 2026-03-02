<!--<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>-->

<script>
    jQuery(document).ready(function() {
        //$("#custom_vehicle_number").css("display","none");
   jQuery("#challan_form").validate({
      rules: {
         challan_route: 'required',
         challan_vehicle: 'required',
         
         challan_driver: {
				required: {
					depends: function(element) {
						return (jQuery('select[name="challan_vehicle"]').val() == "TV" || jQuery('select[name="challan_vehicle"]').val() == "SELF") ? false : true
					}
				}
			},
		vahicle_number: {
				required: {
					depends: function(element) {
						return (jQuery('select[name="challan_vehicle"]').val() == "TV") ? true : false
					}
				}
			},
		date: {
				remote: {
					url: site_url + "admin/misc/checkchallan_val",
					type: 'post',
					data: {
						date: function() {
							return $('input[name="date"]').val();
						},
						ChallanID: function() {
							return $('input[name="ChallanID"]').val();
						}
					}
				}
			},	
         /*u_email: {
            required: true,
            email: true,//add an email rule that will ensure the value entered is valid email id.
            maxlength: 255,
         },*/
      }
   });
});
</script>
<script>
    
$(document).ready(function() {
  $("#number1").inputFilter(function(value) {
    return /^\d*$/.test(value);    // Allow digits only, using a RegExp
  });
});
</script>