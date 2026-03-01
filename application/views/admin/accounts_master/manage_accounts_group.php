<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
              <?php echo form_open('admin/accounts_master/manage_account_group',array('id'=>'account_group_form')); ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_code">GroupCode</label>
                            <input type="text" name="group_code" id="group_code" class="form-control" value="" onkeypress="return isNumber(event)">
                                        
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="group_name">GroupName</label>
                        <input type="text" name="group_name" id="group_name" class="form-control" value="">
                        <input type="hidden" name="form_mode" id="form_mode" value="add">
                        </div>
                    </div>
                    
                </div>
                <div class="row"> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_type">Group Type</label>
                           <select name="group_type" id="group_type" class="form-control">
                            <option value="A">Assets</option>
                            <option value="L">Liability</option>
                           </select>
                           </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="movement_type">Movement</label>
                           <select name="movement_type" id="movement_type" class="form-control">
                               <?php
                                foreach ($account_group_mov as $key => $value) {
                                    # code...
                                    ?>
                                <option value="<?php echo $value["ActGroupMovementID"];?>"><?php echo $value["ActGroupMovementName"];?></option>
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
                        if( has_permission_new('account_groups', '', 'create')) { ?>
                        <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Add</button>
                        <?php }else{
                        echo "<h5 style='color:red'>Not permitted to add record..</h5>";
                        } ?>
                    </div>
                    <div class="edit_button" id="edit_button">
                         <?php
                        if( has_permission_new('account_groups', '', 'edit')) { ?>
                        <button class="btn btn-info pull-left mleft5 search_data" id="search_data" style="font-size:12px;">Update</button>
                        <?php }else{
                        echo "<h5 style='color:red'>Not permitted to edit record..</h5>";
                        } ?>
                    </div>
                </div>
            </div>
        
            <?php echo form_close(); ?>
            <div class="clearfix"></div>
            
            <div class="modal fade AccountGroup" id="AccountGroup" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header" style="padding:5px 10px;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">AccountGroup List</h4>
                        </div>
                        <div class="modal-body" style="padding:0px 5px !important">
                            
                            <div class="table-AccountGroup tableFixHead2">
                                <table class="tree table table-striped table-bordered table-AccountGroup tableFixHead2" id="table_AccountGroup" width="100%">
                                    <thead>
                                        <tr style="display:none;">
                                            <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span class="" style="font-size:10px;">Item Master</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                                        </tr>
                                        <tr>
                                            <th id="sl" style="text-align:left;">AccountGroup <span class="up_starting">  &#8593;</span><span class="down" style="display:none;"> &#8593;</span><span class="up" style="display:none;"> &#8595;</span></th>
                                            <th style="text-align:left;">AccountDescription</th>
                                            <th style="text-align:left;">GroupType</th>
                                            <th style="text-align:left;">Movement</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($account_group_table as $key => $value) {
                                    ?>
                                        <tr class="get_AccountID" data-id="<?php echo $value["ActGroupID"]; ?>">
                        <td><?php echo $value["ActGroupID"];?></td>
                        <td><?php echo $value["ActGroupName"];?></td>
                        <?php
                            if($value["ActGroupTypeID"]=="A"){
                                $groupType = "Assets";
                            }else{
                                $groupType = "Liability";
                            }
                        ?>
                        <td><?php echo $groupType;?></td>
                        <?php 
                            if($value["ActGroupMovementID"]=="B"){
                                $movement = "BALANCE SHEET";
                            }elseif($value["ActGroupMovementID"]=="P"){
                                $movement = "PROFIT & LOSS A/C";
                            }elseif($value["ActGroupMovementID"]=="T"){
                                $movement = "TRADING A/C";
                            }
                        ?>
                        <td><?php echo $movement;?></td>
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
    
  $("#group_code").dblclick(function(){
            $('#myInput1').focus();
            $('#AccountGroup').modal('show');
            
        });
    
    // Set validation for Accout Group Name form
    appValidateForm($('#account_group_form'), {
            group_code: 'required',
            group_name: 'required',
            
            
        });
        
    $('#edit_button').hide();
    
    $('#group_code').on('focus',function(){
         
        var account_groupID = $(this).val();
        var form_mode = $('#form_mode').val();
       
        if(form_mode == "edit"){
            $('#group_code').val('');
            $('#group_name').val('');
            $('#form_mode').val("add");
            $('#add_button').show();
            $('#edit_button').hide();
        }else{
            
        }
        
        
     });
// Initialize For GroupID
     $( "#group_code" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/accounts_master/get_accounts_group",
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
          
          
          $('#group_code').val(ui.item.value); // display the selected text
          $('#group_name').val(ui.item.label); // display the selected text
          $('#form_mode').val("edit");
          $('#group_name').focus();
            return false;      
            
        }
      });
    
    // Initialize For GroupName
     $( "#group_name" ).autocomplete({
        
        source: function( request, response ) {
          // Fetch data
          
          $.ajax({
            url: "<?=base_url()?>admin/accounts_master/get_accounts_group",
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
          
          
          $('#group_code').val(ui.item.value); // display the selected text
          $('#group_name').val(ui.item.label); // display the selected text
          $('#form_mode').val("edit");
          $('#group_type').focus();
            return false;      
            
        }
      });
    // Get Group Detail by Group ID
    $('#group_code').on('blur',function(){
         
        var act_id = $(this).val();
        if(act_id == ""){
            $('#add_button').show();
            $('#edit_button').hide();
            $('#group_name').val('');
            $('.selectpicker').selectpicker('refresh')
        }else{
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_group_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{act_id:act_id,},
                  
                  success:function(data){
                    if(empty(data)){
                        $('#add_button').show();
                        $('#edit_button').hide();
                        $('#group_name').val('');
                        $('.selectpicker').selectpicker('refresh')
                        $('#form_mode').val("add");
                    }else{
                        $('#group_name').val(data.ActGroupName);
                        $('select[name=group_type]').val(data.ActGroupTypeID);
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val(data.ActGroupMovementID);
                        $('.selectpicker').selectpicker('refresh')
                        $('#add_button').hide();
                        $('#edit_button').show();
                        $('#form_mode').val("edit");
                    }
                    
                  }
            });
        }
     })
     
    /*$('#group_name').on('blur',function(){
         
        var act_id = $("#group_code").val();
        if(act_id == ""){
            $('#add_button').show();
            $('#edit_button').hide();
            $('#group_name').val('');
            $('.selectpicker').selectpicker('refresh')
        }else{
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_group_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{act_id:act_id,},
                  
                  success:function(data){
                    if(empty(data)){
                        $('#add_button').show();
                        $('#edit_button').hide();
                        $('#group_name').val('');
                        $('.selectpicker').selectpicker('refresh')
                        $('#form_mode').val("add");
                    }else{
                        $('#group_name').val(data.ActGroupName);
                        $('select[name=group_type]').val(data.ActGroupTypeID);
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val(data.ActGroupMovementID);
                        $('.selectpicker').selectpicker('refresh')
                        $('#add_button').hide();
                        $('#edit_button').show();
                        $('#form_mode').val("edit");
                    }
                    
                  }
            });
        }
     })*/
     
    $('.get_AccountID').on('click',function(){ 
            act_id = $(this).attr("data-id");
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_group_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{act_id:act_id,},
                  
                  success:function(data){
                    if(empty(data)){
                        $('#add_button').show();
                        $('#edit_button').hide();
                        $('#group_name').val('');
                        $('.selectpicker').selectpicker('refresh')
                        $('#form_mode').val("add");
                    }else{
                        $('#group_code').val(data.ActGroupID);
                        $('#group_name').val(data.ActGroupName);
                        $('select[name=group_type]').val(data.ActGroupTypeID);
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val(data.ActGroupMovementID);
                        $('.selectpicker').selectpicker('refresh')
                        $('#add_button').hide();
                        $('#edit_button').show();
                        $('#form_mode').val("edit");
                    }
                  }
            });
            $('#AccountGroup').modal('hide');
        });
     
   
});

</script>

<script>
     function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table_AccountGroup");
  tr = table.getElementsByTagName("tr");
   for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      td2 = tr[i].getElementsByTagName("td")[2];
      td3 = tr[i].getElementsByTagName("td")[3];
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
      }else if(td3){
         txtValue = td3.textContent || td3.innerText;
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
}
 </script>

<style>
    #table_AccountGroup td:hover {
    cursor: pointer;
}
#table_AccountGroup tr:hover {
    background-color: #ccc;
}

    .table-AccountGroup          { overflow: auto;max-height: 65vh;width:100%;position:relative;top: 0px; }
    .table-AccountGroup thead th { position: sticky; top: 0; z-index: 1; }
    .table-AccountGroup tbody th { position: sticky; left: 0; }
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

