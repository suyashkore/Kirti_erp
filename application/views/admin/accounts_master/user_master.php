<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
    <!-- Panel body -->
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
			
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Admin</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>User Master</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
            <!--<div class="row">
                <div class="col-md-12">
                  <h4 class="no-margin font-bold">User Master</h4>
                  <hr>
                  
                </div>
            </div>-->
            
                <?php echo form_open('admin/accounts_master/User_master',array('id'=>'user_master_form')); ?>
                    <div class="row">
                        <div class="col-md-3">
                            
                            <div class="form-group">
                            <label for="userid">AccountID</label>
                            <input type="hidden" name="isedit" id="isedit" value="0">
                            <input type="text" name="userid" id="userid" class="form-control" value="" >
                                        
                            </div>
                        </div>
                        <div class="col-md-3">
                            <?php echo render_input('username','Name',''); ?>
                        </div>
                        <div class="clearfix"></div>
                        <!--<div class="col-md-2">
                            <?php echo render_input('orignal_password','Original Password',''); ?>
                        </div>
                        -->
                        <div class="col-md-3">
                            <?php echo render_input('new_password','New Password',''); ?>
                        </div>
                        
                        <div class="col-md-3">
                            <?php echo render_input('re_password','ReEnter Password',''); ?>
                        </div>
                        <div class="clearfix"></div>
                        <!--<div class="col-md-3">
    						<div class="form-group">
    							<label for="status" class="control-label">Active ?</label>
    							<select name="status" class="selectpicker" id="status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
    								
    								<option value="1">Yes</option>	
    								<option value="0">No</option>
    								
    							</select>
    						</div>
					    </div>-->
					    
					    <div class="col-md-3">
							<div class="form-group">
								<label for="login_access" class="control-label">ERP Access</label>
								<select name="login_access" class="selectpicker" id="login_access" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
									<option value="No">Disable</option>
									<option value="Yes">Enable</option>
									
								</select>
							</div>
						</div>
					    <div class="clearfix"></div>
					    <div class="col-md-12 table_data">
					    </div>
					    
					    <div class="clearfix"></div>
					    <div class="col-md-12">
					   <?php
                        if (has_permission_new('user_master', '', 'edit')) {
                        ?>
                            <div class="add_button" id="add_button">
                                <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Save</button>
                            </div>
                        <?php }else{
                        echo "<span style='color:red'>Your not permitted to update record..</span>";
                        } ?>
                        </div>
                   </div> 
                <?php echo form_close(); ?>
                
          </div>
        </div>
      </div>
      <!-- Panel body-->
      
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                  <h4 class="no-margin font-bold">User List</h4>
                  <hr>
                </div>
                <div class="col-md-12">
                <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
            <div class="tableFixHead2">
                <table class="table table-striped table-bordered tableFixHead2" width="100%" id="user_list">
                <thead>
                <tr>
                    <th>AccountID</th>
                    <!--<th>UserID</th>-->
                    <th>Name</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($user_list as $key => $value) {
                ?>
                    <tr>
                    <td><?php echo $value["AccountID"];?></td>
                    <!--<td><?php echo $value["username"];?></td>-->
                    <td><?php echo $value["firstname"]." ".$value["lastname"];?></td>
                    </tr>
                <?php
                    }
                ?>
                </tbody>
                </table>
            </div>
                </div>
            </div>
            
                
          </div>
        </div>
      </div>
      <!-- pane end-->
    </div>
  </div>
</div>

<?php init_tail(); ?>

<style>
.tableFixHead          { overflow: auto;max-height: 40vh;width:100%;position:relative;top: 0px; }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; }
.tableFixHead tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>

<style>
.tableFixHead2          { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
.tableFixHead2 tbody th { position: sticky; left: 0; }
</style>
<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>
<script>
    
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("no_show_act_table");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}

function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("user_list");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){ 
    
    // Set validation for Accout Group Name form
    appValidateForm($('#user_master_form'), {
            userid: 'required',
            username: 'required',
            new_password: {
				required: {
					depends: function(element) {
						return ($('input[name="isedit"]').val() == "0") ? true : false
					}
				}
			},
            re_password: { equalTo: "#new_password"}
            
        });
        
    $('#userid').on('focus',function(){
        
        $('#userid').val('');
        $('#username').val('');
        $('#orignal_password').val("");
        $('#new_password').val("");
        $('#re_password').val("");
        $('select[name=login_access]').val('No');
        $('.selectpicker').selectpicker('refresh');
        $('.table_data').html("");
        
     });
    // Initialize For Account
     $( "#userid" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/accounts_master/get_user_list",
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
          
          
          $('#userid').val(ui.item.value); // display the selected text
          $('#username').val(ui.item.label); // display the selected text
          /*$('#form_mode').val("edit");
          $('#group_name').focus();*/
          //$('#username').focus();
          //get_noshow_account_list(ui.item.value);
            return false;      
            
        }
      });
      
      // blur from userID
      
      $('#userid').on('blur',function(){
         
        var userID = $(this).val();
        if(userID == "" || userID == null){
            
        }else{
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_staff_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{userID:userID,},
                  
                  success:function(data){
                    if(empty(data)){
                        alert('AccountID not available');
                        $('#username').val('');
                        $('#userid').val('');
                        $('#isedit').val("0");
                        $('#orignal_password').val("");
                        $('#new_password').val("");
                        $('#re_password').val("");
                        $('.table_data').html("");
                        $('#userid').focus();
                    }else{
                        if(data.active == "0"){
                                alert("User status is deactive");
                                $('#userid').val("");
                                $('#username').val("");
                                $('#new_password').val("");
                                $('#re_password').val("");
                                $('.table_data').html("");
                                $('#userid').focus();
                            }else{
                                $('#userid').val(data.AccountID);
                                if(data.password_erp == null){
                                    $('#isedit').val("0");
                                }else{
                                    $('#isedit').val(data.password_erp);
                                }
                                
                                var full_name = data.firstname + ' ' + data.lastname;
                                $('#username').val(full_name);
                                $('select[name=login_access]').val(data.login_access);
                                $('.selectpicker').selectpicker('refresh');
                                $('select[name=status]').val(data.active);
                                $('.selectpicker').selectpicker('refresh');
                                get_noshow_account_list(data.AccountID);
                            }
                        
                    }
                    
                  }
            });
        }
        
     })
});
</script>
<script>
    function get_noshow_account_list(userid){
        $.ajax({
            url: "<?=base_url()?>admin/accounts_master/get_no_act_list",
            type: 'post',
            dataType: "json",
            data: {
              userid: userid
            },
            success: function( data ) {
              //response( data );
              $(".table_data").html(data);
            }
          });
    }
</script>