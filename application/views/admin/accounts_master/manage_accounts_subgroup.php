<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
              <?php echo form_open('admin/accounts_master/manage_account_subgroup',array('id'=>'account_subgroup_form')); ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subgroup_code">Sub Act Group ID</label>
                            <input type="text" name="subgroup_code" id="subgroup_code" class="form-control" value="<?php echo $next_act_groupId->SubActGroupID+1;?>" onkeypress="return isNumber(event)">
                                        
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="subgroup_name">Sub Act Group NAme</label>
                        <input type="text" name="subgroup_name" id="subgroup_name" class="form-control" value="">
                        </div>
                    </div>
                    
                </div>
                <div class="row"> 
                    <input type="hidden" name="form_mode" id="form_mode" value="add">
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="main_group">Main Act Group</label>
                           <select name="main_group" id="main_group" class="form-control">
                               <?php
                                foreach ($account_maingroup as $key => $value) {
                                    # code...
                                    ?>
                                <option value="<?php echo $value["ActGroupID"];?>"><?php echo $value["ActGroupName"];?></option>
                                <?php
                                }
                               ?>
                            
                           </select>
                           </div>
                    </div>
                </div>
                
                
                <div class="row"> 
                
                 
                <div class="col-md-12">
                    
                    <div class="add_button" id="add_button">
                        <?php
                        if( has_permission_new('account_subgroups', '', 'create')) { ?>
                        <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Add</button>
                        <?php }else{
                        echo "<h5 style='color:red'>Not permitted to add record..</h5>";
                        } ?>
                    </div>
                    <div class="edit_button" id="edit_button">
                        <?php
                        if( has_permission_new('account_subgroups', '', 'edit')) { ?>
                        <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Update</button>
                        <?php }else{
                        echo "<h5 style='color:red'>Not permitted to edit record..</h5>";
                        } ?>
                    </div>
                </div>
                  
             </div>
        
            <?php echo form_close(); ?>
          
          <div class="clearfix"></div>
                <!-- Account Head List Model-->
            
                <div class="modal fade AccountSubgroup" id="AccountSubgroup" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Account SubGroup</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                            
                            <div class="table-AccountSubgroup tableFixHead2">
                                <table class="tree table table-striped table-bordered table-AccountSubgroup tableFixHead2" id="table_AccountSubgroup" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th id="sl">SubAccountGroupID <span class="up_starting">  &#8595;</span><span class="up" style="display:none;"> &#8593;</span><span class="down" style="display:none;"> &#8595;</span></th>
                                            <th>SubAccountGroupName</th>
                                            <th>MainGroup</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($account_subgroup_table as $key => $value) {
                                    ?>
                                        <tr class="get_AccountID" data-id="<?php echo $value["SubActGroupID"]; ?>">
                                            <td><?php echo $value["SubActGroupID"];?></td>
                                            <td><?php echo $value["SubActGroupName"];?></td>
                                            <?php
                                                $mainGroupName = '';
                                                foreach ($account_maingroup as $key1 => $value1) {
                                                    if($value["ActGroupID"] == $value1["ActGroupID"]){
                                                        $mainGroupName = $value1["ActGroupName"];
                                                    }
                                                }
                                            ?>
                                            <td><?php echo $mainGroupName;?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>   
                            </div>
                        </div>
                        <div class="modal-footer" style="padding:0px;">
                            <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: left;width: 100%;">
                        </div>
                        </div>
                    <!-- /.modal-content -->
                    </div>
                <!-- /.modal-dialog -->
                </div>
            <!-- /.modal -->
            
          </div>
</div>
</div>
</div>
</div>
</div>


<?php init_tail(); ?>
<!--new update -->

<script>
    function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
</script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){

    // Set validation for Accout Group Name form
    appValidateForm($('#account_subgroup_form'), {
            subgroup_code: 'required',
            subgroup_name: 'required',
            
        });
    $("#subgroup_code").dblclick(function(){
            $('#myInput1').focus();
            $('#AccountSubgroup').modal('show');
            
        });    
    $('#edit_button').hide();

// Initialize For SubgroupID
     $( "#subgroup_code" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/accounts_master/get_accounts_subgroup",
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
          $('#subgroup_code').val(ui.item.value); // display the selected text
          $('#subgroup_name').val(ui.item.label); // display the selected text
          $('#add_button').hide();
            $('#edit_button').show();
            $('#subgroup_name').focus();
            //return true;      
            
        }
      });
    
    // Initialize For SubgroupName
     $( "#subgroup_name" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/accounts_master/get_accounts_subgroup",
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
          $('#subgroup_code').val(ui.item.value); // display the selected text
          $('#subgroup_name').val(ui.item.label); // display the selected text
          $('#add_button').hide();
            $('#edit_button').show();
            $('#main_group').focus();
            //return true;      
            
        }
      });
    
    $('#subgroup_code').on('blur',function(){
         
        var account_subgroupID = $(this).val();
        if(account_subgroupID == ""){
            $('#add_button').show();
            $('#edit_button').hide();
            $('#subgroup_name').val('');
            $('.selectpicker').selectpicker('refresh')
        }else{
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{account_subgroupID:account_subgroupID,},
                  
                  success:function(data){
                    if(empty(data)){
                        $('#add_button').show();
                        $('#edit_button').hide();
                        $('#subgroup_name').val('');
                        $('.selectpicker').selectpicker('refresh')
                    }else{
                        $('#subgroup_code').val(data.SubActGroupID);
                        $('#subgroup_name').val(data.SubActGroupName);
                        $('select[name=main_group]').val(data.ActGroupID);
                        $('.selectpicker').selectpicker('refresh');
                        $('#form_mode').val('edit');
                        $('#add_button').hide();
                        $('#edit_button').show();
                        $('#edit_button').focus();
                    }
                    
                  }
            });
        }
        
     });
     
    /*$('#subgroup_name').on('blur',function(){
         
        var account_subgroupID = $("#subgroup_code").val();
        if(account_subgroupID == ""){
            $('#add_button').show();
            $('#edit_button').hide();
            $('#subgroup_name').val('');
            $('.selectpicker').selectpicker('refresh')
        }else{
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{account_subgroupID:account_subgroupID,},
                  
                  success:function(data){
                    if(empty(data)){
                        $('#add_button').show();
                        $('#edit_button').hide();
                        $('#subgroup_name').val('');
                        $('.selectpicker').selectpicker('refresh')
                    }else{
                        $('#subgroup_code').val(data.SubActGroupID);
                        $('#subgroup_name').val(data.SubActGroupName);
                        $('select[name=main_group]').val(data.ActGroupID);
                        $('.selectpicker').selectpicker('refresh');
                        $('#form_mode').val('edit');
                        $('#add_button').hide();
                        $('#edit_button').show();
                        $('#edit_button').focus();
                    }
                    
                  }
            });
        }
     });
     */
     $('.get_AccountID').on('click',function(){ 
            account_subgroupID = $(this).attr("data-id");
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{account_subgroupID:account_subgroupID,},
                  
                  success:function(data){
                    if(empty(data)){
                        $('#add_button').show();
                        $('#edit_button').hide();
                        $('#subgroup_name').val('');
                        $('.selectpicker').selectpicker('refresh')
                    }else{
                        $('#subgroup_code').val(data.SubActGroupID);
                        $('#subgroup_name').val(data.SubActGroupName);
                        $('select[name=main_group]').val(data.ActGroupID);
                        $('.selectpicker').selectpicker('refresh');
                        $('#form_mode').val('edit');
                        $('#add_button').hide();
                        $('#edit_button').show();
                        $('#edit_button').focus();
                    }
                    
                  }
            });
            $('#AccountSubgroup').modal('hide');
        });
    
    $('#subgroup_code').on('focus',function(){
         
        var account_subgroupID = $(this).val();
        var form_mode = $('#form_mode').val();
        var maingroup_id = "50000";
        if(form_mode == "edit"){
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_max_subgroupId",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{maingroup_id:maingroup_id,},
                  
                  success:function(data){
                        
                        $('#subgroup_code').val(data);
                        $('#subgroup_name').val('');
                        $('#form_mode').val('add');
                        $('#add_button').show();
                        $('#edit_button').hide();
                  }
            });
        }else{
            
        }
     });
     
     
    $('#main_group').on('change',function(){
         
        var maingroup_id = $(this).val();
        var form_mode = $('#form_mode').val();
        if(form_mode == "edit"){
            
        }else{
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_max_subgroupId",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{maingroup_id:maingroup_id,},
                  
                  success:function(data){
                        
                        $('#subgroup_code').val(data);
                        $('#subgroup_name').val('');
                        $('#add_button').show();
                        $('#edit_button').hide();
                  }
            });
        }
     })
     
   
});

</script>

<script>
     function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_AccountSubgroup");
  tr = table.getElementsByTagName("tr");
   for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td1){
         txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td2){
         txtValue = td2.textContent || td2.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else{
           tr[i].style.display = "none";
      } 
    }
    }   
  }
}
}
 </script>
<style>

#account_id {
    text-transform: uppercase;
}
#table_AccountSubgroup td:hover {
    cursor: pointer;
}
#table_AccountSubgroup tr:hover {
    background-color: #ccc;
}

    .table-AccountSubgroup          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-AccountSubgroup thead th { position: sticky; top: 0; z-index: 1; }
    .table-AccountSubgroup tbody th { position: sticky; left: 0; }
    table  { border-collapse: collapse; width: 100%; }
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b;
    color: #fff !important; }
</style>
 <style type="text/css">
   body{
    overflow: hidden;
   }
 </style>

