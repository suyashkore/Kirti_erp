<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="panel_s">
            <div class="panel-body">
				
						<nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Admin</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>User Rights</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
               <!--<h4 class="no-margin">
                  <?php echo $title; ?>
               </h4>-->
               
               <?php echo form_open($this->uri->uri_string()); ?>
               <div class="row">
                   <?php $attrs = (isset($member) ? '' : 'autofocus = true'); ?>
                   <div class="col-md-2" style="padding-right: 0px;">
                       <?php $value = (isset($member) ? $member->staffid : ''); ?>
                            <?php $value2 = (isset($member) ? $member->username : ''); ?>
                       <input type="hidden" name="staff_id" id="staff_id" value="<?php echo $value; ?>">
                       <div class="form-group">
                           <label for="user_id">AccountID</label>
                        </div>
                       <!-- <div class="input-group">-->
                            
                            <input type="text" id="user_id" name="user_id" class="form-control " value="<?php echo $value2; ?>" <?php echo $attrs;?>>
                            
                            <!--<div class="input-group-addon add-new-transfer">
                                  <i class="fa fa-search calendar-icon"></i>
                            </div>-->
                        <!--</div>-->
                    </div>
                    
                    <div class="col-md-4" style="margin-top: 23px;padding-left: 0px;padding-right: 0px;">
                       <?php $value = (isset($member) ? $member->firstname." ".$member->lastname : ''); ?>
                        <input type="text" id="username" name="username" class="form-control " value="<?php echo $value; ?>" readonly>
                        
                    </div>
                    
                    <div class="col-md-2" style="margin-top: 23px;padding-left: 0px;">
                       <?php 
                    //    $value = (isset($member) ? $member->active : '');
                    //     if($member->active == "1"){
                    //         $value = "Active";
                    //     }else{
                    //         $value = "DeActive";
                    //     }
                    $value = (isset($member) && ($member->active ?? '0') == '1') ? 'Active' : 'DeActive';
                       ?>
                        <!--<select name="staff_status" id="staff_status" class="selectpicker">
                            <option value="Active" <?php if($value == "1"){ echo "selected"; }?>>Active</option>
                            <option value="InActive" <?php if($value == "0"){ echo "selected"; }?>>InActive</option>
                        </select>-->
                        <input type="text" id="staff_status" name="staff_status" class="form-control " value="<?php echo $value; ?>" readonly>
                        
                    </div>
                       
                </div>
            <br>
               <div class="row">
                   <div class="col-md-4">
                       <div class="tableFixHead">
                           <table class="table table-bordered roles no-margin tableFixHead" style="font-size:11px;">
                          <thead>
                             <tr>
                                <th>Tag</th>
                                <th>FirmName</th>
                                <th>YearFrom</th>
                                <th>YearTo</th>
                             </tr>
                          </thead>
                          <tbody id="company_data">
                            <?php
                            if(isset($member)){
                                foreach ($firm_data as $key => $value) {
                                    # code...
                                ?>
                                <tr>
                                <td><input type="checkbox" name="company_select" class="radio" onclick="checkOnlyOne(this)" value="<?php echo $value["PlantID"]."-".$value["FY"];?>" / ></td>
                                <td><?php echo $value["FIRMNAME"]; ?></td>
                                <td><?php echo substr($value["YEARFROM"],0,10); ?></td>
                                <td><?php echo substr($value["YEARTO"],0,10); ?></td>
                                </tr>
                                <?php
                                }
                            }
                              
                            ?>
                              
                          </tbody>
                        </table>
                       </div>
                       
                        <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
                        
                   </div>
                   
                   <div class="col-md-8" style="padding-left: 0px;">
                       <div class="show_autorazation" >
                           <div class="tableFixHead2">
                           <table class="table table-bordered roles no-margin  tableFixHead2">
                              <thead>
                                 <tr>
                                    <th>MainMenu</th>
                                    <th>SubMenu</th>
                                    <th>View</th>
                                    <th>add</th>
                                    <th>edit</th>
                                    <th>delete</th>
                                    <th>Print</th>
                                    <th>Export</th>
                                    <th>days</th>
                                 </tr>
                              </thead>
                           <?php
                                /*$permissionsData = [ 'funcData' => ['staff_id'=> isset($member) ? $member->staffid : null ] ];
                                if(isset($member)) {
                                        $permissionsData['member'] = $member;
                                     }*/
                                  //$permissionsData = [ 'funcData' => ['role'=> isset($role) ? $role : null ] ];
                                  //$this->load->view('admin/staff/permissions_new', $permissionsData);
                               ?>
                               
                               <tbody id="permission_data">
                                 
                                 
                               </tbody>
                            </table>
                            <span id="searchh" style="display:none;">
                                Loading.....
                            </span>
                            </div>
                       </div>
                       
                   </div>
               </div>
               <?php $value = (isset($role) ? $role->name : ''); ?>
               <?php //echo render_input('name','role_add_edit_name',$value,'text',$attrs); ?>
                
               <hr />
               <br>
               <?php
                    if (has_permission_new('user_rights', '', 'edit')) {
                ?>
                  <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                <?php }else{
                        echo "<span style='color:red'>You are not permitted to update record..</span>";
                } ?>
                  <?php echo form_close(); ?>
            </div>
         </div>
      </div>
      
   </div>
</div>

<?php init_tail(); ?>


<style type="text/css">

#number_day{
    width: 30px!important;
    height: 20px!important;
}
.tableFixHead { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; }
.tableFixHead tbody th { position: sticky; left: 0; }

.tableFixHead2 { overflow: auto;max-height: 60vh;width:100%;position:relative;top: 0px; }
.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
.tableFixHead2 tbody th { position: sticky; left: 0; }


 table  { border-collapse: collapse; width: 100%; }
 th, td { padding: 5px 5px !important; white-space: nowrap; border:1px solid !important; vertical-align: middle !important;}
 th     { background: #50607b;
    color: #fff !important; }
    
 .checkbox, .radio {
     margin-bottom:0px !important;
 }
</style>
<!--<script>
    $(document)
  .ready(function () {
    $('#table_id')
      .DataTable({
        "order": [[ 0, "desc" ],[ 2, "false" ]]
    });
  });
</script>-->
<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>-->
<script>
    /*$('.add-new-transfer').on('click', function(){
    $('#transfer-modal').find('button[type="submit"]').prop('disabled', false);
      $('#transfer-modal').modal('show');
      
    });*/
    
    // Initialize For Account
     $( "#user_id" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/roles/get_userlist_details_by_userid",
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
         
          $('#user_id').val(ui.item.value); // display the selected text
          $('#username').val(ui.item.label); // display the selected text
          $('#staff_id').val(ui.item.staff_id); // display the selected text
          //load_company_data(ui.item.staff_id);
            //return false;      
            
        }
      });
    $('#user_id').on('focus',function(){
            $('#user_id').val('');
            $('#username').val('');
            $('#company_data').html('');
            $("#permission_data").html('');
       
     });
$('#user_id').on('blur', function(){
    
    var user_id = $(this).val();
    if(user_id == ""){
        $('#company_data').html('');
        $('#username').val('');
        $("#permission_data").html('');
    }else{
        jQuery.ajax({
                type: 'POST',
                url:"<?=base_url()?>admin/roles/get_user_details_by_userid",
                dataType:"JSON",
                data: {user_id: user_id},
                        
                success: function(data3) {
                    if (data3 == "false") {
                        alert('AccountId not found in this company...');
                        $('#company_data').html('');
                        $('#username').val('');
                        $("#permission_data").html('');
                    }else{
                        if(data3.active == "0"){
                                alert("User status is deactive");
                            }else if(data3.login_access == "No"){
                                 alert("ERP access is disabed for this user");
                            }else{
                                load_company_data(data3.staffid);
                            }
                        
                    }
                    
                }
        });
    }
    
    /*
    var staff_id = $('#staff_id').val();
    */
                    
    });
</script>
<script>
    function load_company_data(staff_id){
        var url = "<?php echo base_url(); ?>admin/roles/get_company_list";
        var url2 = "<?php echo base_url(); ?>admin/roles/get_user_details";
        if(staff_id == "" || staff_id == null){
            //alert("UserId not registerd...");
            $('#company_data').html('');
            $('#username').val('');
            $("#permission_data").html('');
           
        }else{
            jQuery.ajax({
                            type: 'POST',
                            url:url,
                            data: {staff_id: staff_id},
                            
                            beforeSend: function () {
                   
                               $('#searchh2').css('display','block');
                               $('#company_data').css('display','none');
                               
                            },
                            complete: function () {
                                //$("#item_code").val("");
                                $('#company_data').css('display','');
                                $('#searchh2').css('display','none');
                            },
                            success: function(data) {
                               
                                //$(".permission_data").html(data);
                                if(data == false){
                                    alert("AccountID not registerd...");
                                    $('#company_data').html('');
                                    $("#permission_data").html('');
                                    
                                }else{
                                    $('#company_data').html(data);
                                    $("#permission_data").html('');
                                //alert(data);
                                }
                                
                                
                            }
                        });
            jQuery.ajax({
                            type: 'POST',
                            url:url2,
                            dataType:"JSON",
                            data: {staff_id: staff_id},
                            
                            success: function(data1) {
                            if(data1.active == "0"){
                                alert("User status is deactive");
                            }else if(data1.login_access == "No"){
                                 alert("ERP access is disabed for this user");
                            }else{
                            var fullname = data1.firstname + " "+data1.lastname;
                                $("#username").val(fullname);
                                $("#staff_id").val(data1.staffid);
                                if(data1.active == "1"){
                                    $("#staff_status").val('Active');
                                }else{
                                    $("#staff_status").val('DeActive');
                                }
                            }
                            }
                        });
            }
    }
</script>
<script>
  function checkOnlyOne(value){
      //alert("hello");
    var checkboxes = document.getElementsByName('company_select')
    checkboxes.forEach((item) => {
      if (item !== value) item.checked =  false
    });
    var staff_id = $("#staff_id").val();
    var plant_fy = value.value;
    //alert(value.value);
    
    var url = "<?php echo base_url(); ?>admin/roles/get_permission_by_staff";
                    jQuery.ajax({
                        type: 'POST',
                        url:url,
                        cache: false,
                        data: {staff_id: staff_id,plant_fy: plant_fy},
                        
                        beforeSend: function () {
               
                           $('#searchh').css('display','block');
                           $('#permission_data').css('display','none');
                           //$("#ui-id-2").prepend("<li value='' class='ui-menu-item'>Serching</li>");
                        },
                        complete: function () {
                            //$("#item_code").val("");
                            $('#permission_data').css('display','');
                            $('#searchh').css('display','none');
                        },
                        success: function(data) {
                           
                            //$(".permission_data").html(data);
                            $('#permission_data').html(data);
                            //alert(data);
                            
                        }
                    });
  
  }
</script>
<script>
   $(function(){
     appValidateForm($('form'),{user_id:'required'},{company_select:'required'});
   });
</script>
</body>
</html>
