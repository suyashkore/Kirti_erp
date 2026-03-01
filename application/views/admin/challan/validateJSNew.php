<!--<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>-->

<script>
	// 3) Function to attach rules to .SequenceInput fields
		function attachSequenceRules(ctx) {
			$(ctx).find(".SequenceInput").each(function () {
				$(this).rules("add", {
					required: {
						depends: function (el) {
							return $(el).closest("tr").find('input[type="checkbox"]').is(":checked");
						}
					},
					uniqueSeqIfChecked: true,
					messages: { required: "Please enter sequence number." }
				});
			});
		}
    jQuery(document).ready(function() {
        //$("#custom_vehicle_number").css("display","none");
		
		// 1) Custom rule: unique among checked rows only
		jQuery.validator.addMethod("uniqueSeqIfChecked", function (value, element) {
			var $row = $(element).closest("tr");
			var isChecked = $row.find('input[type="checkbox"]').is(":checked");
			if (!isChecked) return true;
			
			var v = $.trim(value);
			if (v === "") return true;
			
			var dup = 0;
			$(".SequenceInput").each(function () {
				var $r = $(this).closest("tr");
				if ($r.find('input[type="checkbox"]').is(":checked")) {
					if ($.trim($(this).val()) === v) dup++;
				}
			});
			return dup <= 1;
		}, "Sequence must be unique among checked rows.");
		
		var validator = jQuery("#challan_form").validate({
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
		
		
		
		// Run on page load (for UPDATE mode, since inputs are already in DOM)
		attachSequenceRules(document);
		
		// 4) Revalidate on input/change
		$(document).on("keyup change", ".SequenceInput, input[type='checkbox']", function () {
			$(".SequenceInput").each(function () {
				validator.element(this);
			});
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