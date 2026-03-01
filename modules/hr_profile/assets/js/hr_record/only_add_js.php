<script>
	$(function(){
		'use strict';
		appValidateForm($('#add_edit_member'), {
			firstname: 'required',
			/*lastname: 'required',*/
			status_work: 'required',
			OfficeID: 'required',
			company_id1: 'required',
			SubActGroupID: 'required',
			state: 'required',
			password: {
				required: {
					depends: function(element) {
						return ($('select[name="app_access"]').val() == "Yes") ? true : false
					}
				}
			},
			rsm_list_reported_by_asm: {
				required: {
					depends: function(element) {
						return ($('select[name="job_position"]').val() == 5) ? true : false
					}
				}
			},
			asm_list_reported_by_ase: {
				required: {
					depends: function(element) {
						return ($('select[name="job_position"]').val() == 6) ? true : false
					}
				}
			},
			ase_list_reported_by_so: {
				required: {
					depends: function(element) {
						return ($('select[name="job_position"]').val() == 8) ? true : false
					}
				}
			},
			so_list_reported_by_tsi: {
				required: {
					depends: function(element) {
						return ($('select[name="job_position"]').val() == 7) ? true : false
					}
				}
			},
			phonenumber: {
				required: {
					depends: function(element) {
						return ($('select[name="app_access"]').val() == "Yes") ? true : false
					}
				},
				remote: {
					url: site_url + "admin/misc/staff_mobile_exists",
					type: 'post',
					data: {
						phonenumber: function() {
							return $('input[name="phonenumber"]').val();
						},
						memberid: function() {
							return $('input[name="memberid"]').val();
						}
					}
				}
			},
			/*username: {
				required: true,
				remote: {
					url: site_url + "admin/misc/staff_username_exists",
					type: 'post',
					data: {
						username: function() {
							return $('input[name="username"]').val();
						},
						memberid: function() {
							return $('input[name="memberid"]').val();
						}
					}
				}
			},*/
			email: {
				remote: {
					url: site_url + "admin/misc/staff_email_exists",
					type: 'post',
					data: {
						email: function() {
							return $('input[name="email"]').val();
						},
						memberid: function() {
							return $('input[name="memberid"]').val();
						}
					}
				}
			},
			AccountID: {
				required: true,
				remote: {
					url: site_url + "admin/misc/accountID_exists",
					type: 'post',
					data: {
						AccountID: function() {
							return $('input[name="AccountID"]').val();
						},
						memberid: function() {
							return $('input[name="memberid"]').val();
						}
					}
				}
			},
			/*staff_identifi: {
				required: false,
				remote: {
					url: site_url + "admin/hr_profile/hr_code_exists",
					type: 'post',
					data: {
						staff_identifi: function() {
							return $('input[name="staff_identifi"]').val();
						},
						memberid: function() {
							return $('input[name="memberid"]').val();
						}
					}
				}
			}*/
		});

		init_datepicker();
		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');

		$('select[name="role"]').on('change', function() {
			var roleid = $(this).val();
			init_roles_permissions(roleid, true);
		});


		$("input[name='profile_image']").on('change', function() {
			readURL(this);
		});
		
        $('#phonenumber').keyup(function(e) {
            e.preventDefault();
            if(!$('#phonenumber').val().match('[0-9]{10}'))  {
                
                $(".mob_denger").text("Enter valid 10 digit mobile number");
            }else{
                $(".mob_denger").text(" ");
            }
        });
        
       
        $('#pan_number').keyup(function(e) {
        var val = $('#pan_number').val();
        if(val == ""){
            $(".pan_denger").text(" ");
        }else{
            e.preventDefault();
            if(!$('#pan_number').val().match('[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}'))  {
                $(".pan_denger").text("Enter valid PAN number");
            }else{
                $(".pan_denger").text(" ");
            }
        }
    });
        
        $('#aadhar_number').keyup(function(e) {
            e.preventDefault();
            if(!$('#aadhar_number').val().match('[0-9]{12}'))  {
                
                $(".aadhar_denger").text("Enter valid 12 digit Aadhar number");
            }else{
                $(".aadhar_denger").text(" ");
            }
            

        });
        
        $('#account_number').keyup(function(e) {
            e.preventDefault();
            if(!$('#account_number').val().match('[0-9]{9}'))  {
                
                $(".actnumber_denger").text("Enter valid Account number");
            }else{
                $(".actnumber_denger").text(" ");
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
			if($('#state').val()==""){
			    $(".add_quarter").css("display", "none");
			}
			
			$('#state').on('change', function() {
				var id = $(this).val();
				//alert(roleid);
				var url = "<?php echo base_url(); ?>admin/hr_profile/select_quarter";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {id: id},
                        dataType:'json',
                        success: function(data) {
                           $('#head_quarter_modal input[name="state_id"]').val(id);
                            //$(".headqurter").html(data);
                            //alert(data);
                            //data = JSON.parse(data);
                               $("#headqurter").children().remove();
                                $.each(data, function (index, value) {
                                // APPEND OR INSERT DATA TO SELECT ELEMENT.
                                $('#headqurter').append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                                //$("#asm_list").val(4);
                                $("#headqurter").selectpicker("refresh");
                             $(".add_quarter").css("display", "");
                            
                        }
                    });
			});
			var job_position = $('select[name="job_position"]').val();
			if(job_position== "4"){
			    
			        $(".for_rsm").css("display", "");
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_so").css("display", "none");
			        $(".for_tsi").css("display", "none");
			}else if(job_position== "5"){
			        $(".for_ase").css("display", "none");
			        $(".for_so").css("display", "none");
			        $(".for_rsm").css("display", "none");
			        $(".for_tsi").css("display", "none");
			        $(".for_asm").css("display", "");
			}else if(job_position== "6"){
			    $(".for_so").css("display", "none");
			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_tsi").css("display", "none");
			        $(".for_ase").css("display", "");
			}else if(job_position== "8"){
			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_tsi").css("display", "none");
			        $(".for_so").css("display", "");
			}else if(job_position== "7"){
			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_tsi").css("display", "");
			        $(".for_so").css("display", "none");
			}else {
			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_so").css("display", "none");
			        $(".for_tsi").css("display", "none");
			    }
			        
			$('#job_position').on('change', function() {
			    
			    var value = $(this).val();
			    if(value == "4"){
			        
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_so").css("display", "none");
			        $(".for_tsi").css("display", "none");
			        $(".for_rsm").css("display", "");
			        /*appValidateForm($('#add_edit_member'), {
			            asm_list_reported_to_rsm: {
            				required: true,
            			    }
			            });*/
			            if(job_position){
			                
			            }else{
			                Get_asm_list_reported_to_rsm();
			            }
			            
			            
			    }else if(value == "5"){
			        
			        $(".for_ase").css("display", "none");
			        $(".for_so").css("display", "none");
			        $(".for_rsm").css("display", "none");
			        $(".for_tsi").css("display", "none");
			        $(".for_asm").css("display", "");
			        appValidateForm($('#add_edit_member'), {
			            rsm_list_reported_by_asm: {
            				required: true,
            			    }/*,
            			ase_list_reported_to_asm: {
            				required: true,
            			    }*/
            			    
			            });
			            if(job_position){
			                
			            }else{
			                Get_rsm_list_reported_by_asm();
			                Get_ase_list_reported_to_asm();
			            }
			            
			    }else if(value == "6"){
			        
			        $(".for_so").css("display", "none");
			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_tsi").css("display", "none");
			        $(".for_ase").css("display", "");
			        appValidateForm($('#add_edit_member'), {
			            asm_list_reported_by_ase: {
            				required: true,
            			    }/*,
            			so_list_reported_to_ase: {
            				required: true,
            			    }*/
			            });
			            
			            if(job_position){
			                
			            }else{
			                Get_asm_list_reported_by_ase();
			                Get_so_list_reported_to_ase();
			            }
			            
			            
			    }else if(value == "8"){

			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_tsi").css("display", "none");
			        $(".for_so").css("display", "");
			        
			        appValidateForm($('#add_edit_member'), {
			            ase_list_reported_by_so: {
            				required: true,
            			    }
			            });
			            if(job_position){
			                
			            }else{
			                Get_ase_list_reported_by_so();
			                Get_distributor_list_reported_to_so();
			            }
			            
			            
			    }else if(value == "7"){

			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_so").css("display", "none");
			        $(".for_tsi").css("display", "");
			        
			        appValidateForm($('#add_edit_member'), {
			            so_list_reported_by_tsi: {
            				required: true,
            			    }
			            });
			            if(job_position){
			                
			            }else{
			                Get_so_list_reported_by_tsi();
			                
			            }
			            
			            
			    }else {
			        $(".for_rsm").css("display", "none");
			        $(".for_asm").css("display", "none");
			        $(".for_ase").css("display", "none");
			        $(".for_so").css("display", "none");
			        $(".for_tsi").css("display", "none");
			    }
			    
			});
		
		function Get_rsm_list_reported_by_asm(){
		    var value = 1;
		    var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_rsm";
                        jQuery.ajax({
                            type: 'POST',
                            url:url,
                            data: {value: value},
                            
                            success: function(data) {
                               
                                
                               data = JSON.parse(data);
                               $("#rsm_list_reported_by_asm").children().remove();
                                $.each(data, function (index, value) {
                                // APPEND OR INSERT DATA TO SELECT ELEMENT.
                                $('#rsm_list_reported_by_asm').append('<option value="' + value.staffid + '">' + value.firstname +' '+ value.lastname + '</option>');
                                });
                                //$("#asm_list").val(4);
                                $("#rsm_list_reported_by_asm").selectpicker("refresh");
                             
                                
                            }
                        });
		}	
		function Get_asm_list_reported_to_rsm(){
		    var value = 1;
		    var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_asm2";
                        jQuery.ajax({
                            type: 'POST',
                            url:url,
                            data: {value: value},
                            
                            success: function(data) {
                               
                                
                               data = JSON.parse(data);
                               if(job_position == "4"){
                                   
                               }else{
                                   $("#asm_list_reported_to_rsm").children().remove();
                               }
                               
                                $.each(data, function (index, value) {
                                // APPEND OR INSERT DATA TO SELECT ELEMENT.
                                $('#asm_list_reported_to_rsm').append('<option value="' + value.staffid + '">' + value.firstname +' '+ value.lastname + '</option>');
                                });
                                //$("#asm_list").val(4);
                                $("#asm_list_reported_to_rsm").selectpicker("refresh");
                             
                                
                            }
                        });
		}
		
		function Get_asm_list_reported_by_ase(){
		    var value = 1;
		    var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_asm";
                        jQuery.ajax({
                            type: 'POST',
                            url:url,
                            data: {value: value},
                            
                            success: function(data) {
                               
                                
                               data = JSON.parse(data);
                               $("#asm_list_reported_by_ase").children().remove();
                                $.each(data, function (index, value) {
                                // APPEND OR INSERT DATA TO SELECT ELEMENT.
                                $('#asm_list_reported_by_ase').append('<option value="' + value.staffid + '">' + value.firstname +' '+ value.lastname + '</option>');
                                });
                                //$("#asm_list").val(4);
                                $("#asm_list_reported_by_ase").selectpicker("refresh");
                             
                                
                            }
                        });
		}
		
		function Get_ase_list_reported_to_asm(){
		    
		    // Get All ASE List
		    var value = 1;
			var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_ase2";
            jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {value: value},
                            
                        success: function(data) {
                              
                            data = JSON.parse(data);
                            $("#ase_list_reported_to_asm").children().remove();
                            $.each(data, function (index, value) {
                            // APPEND OR INSERT DATA TO SELECT ELEMENT.
                            $('#ase_list_reported_to_asm').append('<option value="' + value.staffid + '">' + value.firstname +' '+ value.lastname + '</option>');
                            });
                            //$("#asm_list").val(4);
                            $("#ase_list_reported_to_asm").selectpicker("refresh");
                             
                                
                            }
                        });
		}
		
		function Get_ase_list_reported_by_so(){
		    
		    // Get All ASE List
		    var value = 1;
			var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_ase";
            jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {value: value},
                            
                        success: function(data) {
                              
                            data = JSON.parse(data);
                            $("#ase_list_reported_by_so").children().remove();
                            $.each(data, function (index, value) {
                            // APPEND OR INSERT DATA TO SELECT ELEMENT.
                            $('#ase_list_reported_by_so').append('<option value="' + value.staffid + '">' + value.firstname +' '+ value.lastname + '</option>');
                            });
                            //$("#asm_list").val(4);
                            $("#ase_list_reported_by_so").selectpicker("refresh");
                             
                                
                            }
                        });
		}
		
		function Get_so_list_reported_by_tsi(){
		    
		    // Get All ASE List
		    var value = 1;
			var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_so2";
            jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {value: value},
                            
                        success: function(data) {
                              
                            data = JSON.parse(data);
                            $("#so_list_reported_by_tsi").children().remove();
                            $.each(data, function (index, value) {
                            // APPEND OR INSERT DATA TO SELECT ELEMENT.
                            $('#so_list_reported_by_tsi').append('<option value="' + value.staffid + '">' + value.firstname +' '+ value.lastname + '</option>');
                            });
                            //$("#asm_list").val(4);
                            $("#so_list_reported_by_tsi").selectpicker("refresh");
                             
                                
                            }
                        });
		}
		
		function Get_distributor_list_reported_to_so(){
		    
		    
		    var value = 1;
			var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_distributor";
            jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {value: value},
                            
                        success: function(data) {
                              
                            data = JSON.parse(data);
                            $("#distributor_list_reported_to_so").children().remove();
                            $.each(data, function (index, value) {
                            // APPEND OR INSERT DATA TO SELECT ELEMENT.
                            $('#distributor_list_reported_to_so').append('<option value="' + value.userid + '">' + value.company + '</option>');
                            });
                            //$("#asm_list").val(4);
                            $("#distributor_list_reported_to_so").selectpicker("refresh");
                             
                                
                            }
                        });
		}
		
		function Get_so_list_reported_to_ase(){
		    var value = 1;
		    var url = "<?php echo base_url(); ?>admin/hr_profile/get_all_so";
            jQuery.ajax({
                        type: 'POST',
                        url:url,
                        data: {value: value},
                            
                        success: function(data) {
                            data = JSON.parse(data);
                            $('#so_list_reported_to_ase').children().remove();
                            $.each(data, function (index, value) {
                            // APPEND OR INSERT DATA TO SELECT ELEMENT.
                            $('#so_list_reported_to_ase').append('<option value="' + value.staffid + '">' + value.firstname +' '+ value.lastname + '</option>');
                            });
                            //$("#asm_list").val(4);
                            $("#so_list_reported_to_ase").selectpicker("refresh");
                             
                                
                            }
                        });
		}
			/*$(".asm_div").css("display", "none");
			$(".ase_div").css("display", "none");
			
			$('#job_position').on('change', function() {
				var value = $(this).val();
				if(value == "4"){
				    $(".asm_div").css("display", "");
			        $(".ase_div").css("display", "none");
				    
				}
				
				if(value == "5"){
				    $(".asm_div").css("display", "none");
			        $(".ase_div").css("display", "");
				    
				}
				
				if(value == "6"){
				    $(".asm_div").css("display", "none");
			        $(".ase_div").css("display", "none");
			        $(".so_div").css("display", "");
				    
				}
				 
			});*/
			
			var app_access = $("#app_access").val();
			var erp_access = $("#login_access").val();
			if(app_access == "No"){
			    $("#password_field").css("display", "none");
			}
			
			
			
			$('#app_access').on('change', function() {
				var app_access = $(this).val();
				
				if(app_access == "Yes"){
				    $("#password_field").css("display", "");
				}else {
				    $//("").css("display", "inline-table");
				    $("#password_field").css("display", "none");
				}
				
				
			});

	});

	function readURL(input) {
		"use strict";
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$("img[id='wizardPicturePreview']").attr('src', e.target.result).fadeIn('slow');
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	function hr_profile_update_staff(staff_id) {
		"use strict";

		$("#modal_wrapper").load("<?php echo admin_url('hr_profile/hr_profile/member_modal'); ?>", {
			slug: 'update',
			staff_id: staff_id
		}, function() {
			if ($('.modal-backdrop.fade').hasClass('in')) {
				$('.modal-backdrop.fade').remove();
			}
			if ($('#appointmentModal').is(':hidden')) {
				$('#appointmentModal').modal({
					show: true
				});
			}
		});

		init_selectpicker();
		$(".selectpicker").selectpicker('refresh');
	}

</script>