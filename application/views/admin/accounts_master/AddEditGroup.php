<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
              <?php //echo form_open('admin/accounts_master/manage_account_group',array('id'=>'account_group_form')); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                        <div class="searchh3" style="display:none;">Please wait Create new Group...</div>
                        <div class="searchh4" style="display:none;">Please wait update Group...</div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="group_code">Group Code</label>
                            <input type="text" name="group_code" id="group_code" class="form-control" value="" onkeypress="return isNumber(event)">
                                        
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                        <label for="group_name">Group Name</label>
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
                <!--<div class="col-md-12">
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
                </div>-->
                
                    <div class="col-md-12">
                        <?php if (has_permission('account_groups', '', 'create')) {
                        ?>
                        <button type="button" class="btn btn-info saveBtn" style="margin-right: 25px;">Save</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info saveBtn2 disabled" style="margin-right: 25px;">Save</button>
                        <?php
                        }?>
                        
                        <?php if (has_permission('account_groups', '', 'edit')) {
                        ?>
                        <button type="button" class="btn btn-info updateBtn" style="margin-right: 25px;">Update</button>
                        <?php
                        }else{
                        ?>
                        <button type="button" class="btn btn-info updateBtn2 disabled" style="margin-right: 25px;">Update</button>
                        <?php
                        }?>
                        
                        <button type="button" class="btn btn-default cancelBtn" >Cancel</button>
                    </div>
            </div>
        
            <?php //echo form_close(); ?>
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
        $('#AccountGroup').modal('show');
        $('#AccountGroup').on('shown.bs.modal', function () {
            $('#myInput1').val('');
            $('#myInput1').focus();
        })
    });
   
    $('.updateBtn').hide();
    $('.updateBtn2').hide();
    
// Focus on GroupID
    $('#group_code').on('focus',function(){
        $('#group_code').val('');
        $('#group_name').val('');
        $('select[name=group_type]').val('A');
        $('.selectpicker').selectpicker('refresh');
        $('select[name=movement_type]').val('B');
        $('.selectpicker').selectpicker('refresh');
        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
    });

// Cancel selected data
    $(".cancelBtn").click(function(){
        $('#group_code').val('');
        $('#group_name').val('');
        $('select[name=group_type]').val('A');
        $('.selectpicker').selectpicker('refresh');
        $('select[name=movement_type]').val('B');
        $('.selectpicker').selectpicker('refresh');
        $('.saveBtn').show();
        $('.saveBtn2').show();
        $('.updateBtn').hide();
        $('.updateBtn2').hide();
    });
    
// Get Group Detail by Group ID
    $('#group_code').on('blur',function(){
        var act_id = $(this).val();
        if(act_id == ""){
            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();
            $('#group_name').val('');
            $('select[name=group_type]').val('A');
            $('.selectpicker').selectpicker('refresh');
            $('select[name=movement_type]').val('B');
            $('.selectpicker').selectpicker('refresh');
        }else{
            $.ajax({
                  url:"<?php echo admin_url(); ?>accounts_master/get_account_group_details",
                  dataType:"JSON",
                  method:"POST",
                  cache: false,
                  data:{act_id:act_id,},
                  success:function(data){
                    if(empty(data)){
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('#group_name').val('');
                        $('select[name=group_type]').val('A');
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val('B');
                        $('.selectpicker').selectpicker('refresh');
                    }else{
                        $('#group_name').val(data.ActGroupName);
                        $('select[name=group_type]').val(data.ActGroupTypeID);
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val(data.ActGroupMovementID);
                        $('.selectpicker').selectpicker('refresh')
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                    }
                  }
            });
        }
     })
     
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
          $('.saveBtn').hide();
          $('.updateBtn').show();
          $('.saveBtn2').hide();
          $('.updateBtn2').show();
          $('#group_name').focus();
            return false;  
        }
    });
    
     
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
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('#group_code').val('');
                        $('#group_name').val('');
                        $('select[name=group_type]').val('A');
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val('B');
                        $('.selectpicker').selectpicker('refresh');
                    }else{
                        $('#group_code').val(data.ActGroupID);
                        $('#group_name').val(data.ActGroupName);
                        $('select[name=group_type]').val(data.ActGroupTypeID);
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val(data.ActGroupMovementID);
                        $('.selectpicker').selectpicker('refresh')
                        $('.saveBtn').hide();
                        $('.updateBtn').show();
                        $('.saveBtn2').hide();
                        $('.updateBtn2').show();
                    }
                  }
            });
            $('#AccountGroup').modal('hide');
        });
    
    // Save New Group
        $('.saveBtn').on('click',function(){ 
            ActGroupID = $('#group_code').val();
            ActGroupName = $('#group_name').val();
            ActGroupTypeID = $('#group_type').val();
            ActGroupMovementID = $('#movement_type').val();
            $.ajax({
                url:"<?php echo admin_url(); ?>accounts_master/SaveGroup",
                dataType:"JSON",
                method:"POST",
                data:{ActGroupID:ActGroupID,ActGroupName:ActGroupName,ActGroupTypeID:ActGroupTypeID,ActGroupMovementID:ActGroupMovementID
                },
                beforeSend: function () {
                $('.searchh3').css('display','block');
                $('.searchh3').css('color','blue');
                },
                complete: function () {
                $('.searchh3').css('display','none');
                },
                success:function(data){
                   if(data == true){
                       alert_float('success', 'Record created successfully...');
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('#group_code').val('');
                        $('#group_name').val('');
                        $('select[name=group_type]').val('A');
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val('B');
                        $('.selectpicker').selectpicker('refresh');
                              
                   }else{
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });
        }); 
        
    // Update Exiting Item
        $('.updateBtn').on('click',function(){ 
            ActGroupID = $('#group_code').val();
            ActGroupName = $('#group_name').val();
            ActGroupTypeID = $('#group_type').val();
            ActGroupMovementID = $('#movement_type').val();
            
            $.ajax({
                url:"<?php echo admin_url(); ?>accounts_master/UpdateGroup",
                dataType:"JSON",
                method:"POST",
                data:{ActGroupID:ActGroupID,ActGroupName:ActGroupName,ActGroupTypeID:ActGroupTypeID,ActGroupMovementID:ActGroupMovementID
                },
                beforeSend: function () {
                $('.searchh4').css('display','block');
                $('.searchh4').css('color','blue');
                },
                complete: function () {
                $('.searchh4').css('display','none');
                },
                success:function(data){
                    if(data == true){
                       alert_float('success', 'Record updated successfully...');
                        $('.saveBtn').show();
                        $('.saveBtn2').show();
                        $('.updateBtn').hide();
                        $('.updateBtn2').hide();
                        $('#group_code').val('');
                        $('#group_name').val('');
                        $('select[name=group_type]').val('A');
                        $('.selectpicker').selectpicker('refresh');
                        $('select[name=movement_type]').val('B');
                        $('.selectpicker').selectpicker('refresh');
                              
                    }else{
                       alert_float('warning', 'Something went wrong...');
                   }
                }
            });
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

