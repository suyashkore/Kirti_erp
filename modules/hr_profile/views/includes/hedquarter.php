<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table-headquarter          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.table-headquarter thead th { position: sticky; top: 0; z-index: 1; }
.table-headquarter tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>
<div>
	<!--<div class="_buttons">
		<?php if(is_admin() || has_permission('hrm_hedquarter','','create')) {?>
			<a href="#" onclick="new_headquarter(); return false;" class="btn btn-info pull-left display-block">
				<?php echo _l('hr_hr_add'); ?>
			</a>
		<?php } ?>
	</div>-->
	<?php if(is_admin() || has_permission('hrm_hedquarter','','create')) {?>
	<div class="row">
	    <div class="col-md-12">
	        <h4>Add New Head Quarter</h4>
	    </div>
	</div>
	<div class="clearfix"></div>
	<br>
	
	<?php echo form_open(admin_url('hr_profile/headquarter'), array('id' => 'add_workplace' )); ?>
	<div class="row">
	    <div class="col-md-4">
	        <div class="form-group">
				<label for="state" class="control-label"><?php echo _l('hr_state'); ?></label>
				    <select name="state_id" class="selectpicker" id="state" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
						<option value=""></option>                  
						<?php foreach($state as $s){ ?>

						<option value="<?php echo html_entity_decode($s['short_name']); ?>" <?php if(isset($member) && $member->state_id == $s['id']){echo 'selected';} ?>><?php echo html_entity_decode($s['state_name']); ?></option>

						<?php } ?>
					</select>
			</div>
		</div>
		
		<div class="col-md-4">
	        <div class="form-group">
				<label for="state" class="control-label">Head Quarter Name</label>
				<input type="text" name="name" class="form-control">
			</div>
		</div>
		
		<div class="col-md-2">
		    
		    <div class="form-group">
		    <label for="state" class="control-label" style="margin-bottom: 14px;"></label>
		    <button type="submit" class="btn btn-info form-control"><?php echo _l('submit'); ?></button>
		    
		    </div>
		</div>
			    
	</div>
	<?php echo form_close(); ?>
	<?php } ?>
	<hr>
	<div class="row">
        <div class="col-md-4">
	        <div class="form-group">
				<label for="state" class="control-label"><?php echo _l('hr_state'); ?></label>
				    <select name="state_id" class="selectpicker" id="state_id" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
						<option value=""></option>                  
						<?php foreach($state as $s){ ?>

						<option value="<?php echo html_entity_decode($s['short_name']); ?>" <?php if(isset($member) && $member->state_id == $s['id']){echo 'selected';} ?>><?php echo html_entity_decode($s['state_name']); ?></option>

						<?php } ?>
					</select>
			</div>
		</div>
        <div class="col-md-3">
          <div class="form-group">
				<label for="status" class="control-label">Status</label>
				    <select name="status" class="selectpicker" id="status_id" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
						<option value="1">Active</option>                  
						<option value="0">Deactive</option>                 
					
					</select>
			</div>
        </div>
        <button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
                
            &nbsp;</div>
	    <div class="row">
            <div class="col-md-6">
                <div class="custom_button">&nbsp;&nbsp;
                    <a class="btn btn-default buttons-excel buttons-html5" tabindex="0" aria-controls="table-headquarter" href="#" id="caexcel"><span>Export to excel</span></a>
                    <a class="btn btn-default" href="javascript:void(0);" onclick="printPage();">Print</a>
                </div>
            </div>
            <div class="col-md-4">
                <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
	    <div class="table-headquarter tableFixHead2">
            <table class="tree table table-striped table-bordered table-headquarter tableFixHead2 " id="table-headquarter" width="100%">
        		<thead>
        		      
        		    <tr style="display:none;">
                      <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span style="font-size:10px;font-weight:600;">Head Quarter List</span><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>
                  <tr>
        		    <th>State Name</th>
        			<th>Head Quarter</th>
        			<!--<th><?php echo _l('hr_workplace_address'); ?></th>
        			<th><?php echo _l('hr_latitude_lable'); ?></th>
        			<th><?php echo _l('hr_longitude_lable'); ?></th>-->
        			<th><?php echo _l('Status'); ?></th>
        			<th><?php echo _l('options'); ?></th>
        		</tr>
        		</thead>
        		<tbody>
        			
        		</tbody>
	        </table> 
              
        </div>
        </div>
        </div>
        <span id="searchh2" style="display:none;">
                                Loading.....
                            </span>
        <span id="searchh3" style="display:none;">
            Loading Export.....
        </span>
	       
	<div class="modal" id="new_headquarter" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<?php echo form_open(admin_url('hr_profile/headquarter'), array('id' => 'add_workplace' )); ?>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">
						<span class="edit-title">Edit Head Quarter</span>
						<span class="add-title"><?php echo _l('hr_new_workplace'); ?></span>
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="additional_workplace"></div>   
							<div class="form">     
								<?php 
								echo render_input('name','Head Quarter'); ?>
							</div>
						</div>
						<div class="col-md-12">
                	        <div class="form-group">
                				<label for="state" class="control-label"><?php echo _l('hr_state'); ?></label>
                				    <select name="state_id" class="selectpicker" id="state" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                						<option value=""></option>                  
                						<?php foreach($state as $s){ ?>
                
                						<option value="<?php echo html_entity_decode($s['short_name']); ?>" <?php if(isset($member) && $member->state_id == $s['id']){echo 'selected';} ?>><?php echo html_entity_decode($s['state_name']); ?></option>
                
                						<?php } ?>
                					</select>
                			</div>
                		</div>
		                <div class="col-md-12">
		                    <div class="form-group">
                				<label for="status" class="control-label">Status</label>
                				    <select name="status" class="selectpicker" id="status" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"> 
                						<option value="1">Active</option>                  
                						<option value="0">Deactive</option>
                					</select>
                			</div>
		                </div>
						<!--<div class="col-md-12">
							<?php echo render_textarea('workplace_address', 'hr_workplace_address') ?>
						</div>
						<div class="col-md-6">

							<?php echo render_input('latitude', 'hr_latitude_lable', '', 'number') ?>
						</div>
						<div class="col-md-6">
							<?php echo render_input('longitude', 'hr_longitude_lable', '', 'number') ?>
						</div>-->

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('hr_close'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    function edit_workplace(invoker,id){
        'use strict';

        $('#additional_workplace').html('');
        $('#additional_workplace').append(hidden_input('id',id));

        $('#new_headquarter input[name="name"]').val($(invoker).data('name'));
        //$('#new_headquarter select[name="state_id"]').val($(invoker).data('state'));
        $('#new_headquarter select[name=state_id]').val($(invoker).data('state'));
        $('.selectpicker').selectpicker('refresh');
        $('#new_headquarter select[name=status]').val($(invoker).data('status'));
        $('.selectpicker').selectpicker('refresh');

        $('#new_headquarter').modal('show');
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
    }
     $('#search_data').on('click',function(){
         
        var state_id = $("#state_id").val();
	    var status_id = $("#status_id").val();
	    if(status_id == 1){
	         var msg = " State:"+state_id +", Status: Active";
	    }else{
	         var msg = " State:"+state_id +", Status: Deactive";
	    }
	   
	    $(".report_for").text(msg);
        $.ajax({
      url:"<?php echo admin_url(); ?>hr_profile/load_table_for_headquarter",
      dataType:"html",
      method:"POST",
      data:{state_id:state_id, status_id:status_id},
      beforeSend: function () {
               
        $('#searchh2').css('display','block');
        $('.table-headquarter tbody').css('display','none');
        
     },
      complete: function () {
                            
        $('.table-headquarter tbody').css('display','');
        $('#searchh2').css('display','none');
     },
      success:function(data){
        //  data1 = JSON.parse(data);
           
        //   console.log(data1.html);return false; 
           $('#table-headquarter tbody').html(data);
        // $('tbody').html(data);
      }
    });
        
 });
</script>
<script type="text/javascript">
 function printPage(){
    var html_filter_name =    $('.report_for').html();
        
	    var stylesheet = '<style type = "text/css"> th, td { padding: 5px 5px;} </style>';
    var tableData = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var heading_data = '<table  border="1" cellpadding="0" cellspacing="0" width="100%" class="tree table table-striped table-bordered" style="font-size:12px;"><tbody><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->company_name; ?></td></tr><tr><td style="text-align:center;" colspan="3"><?php echo $company_detail->address; ?></td></tr>';
         heading_data += '<tr>';
         heading_data += '<td style="text-align:center;"colspan="3">Head Quarter List '+html_filter_name+'</td>';
         heading_data += '</tr>';
         
         heading_data += '</tbody></table>';
        var print_data = stylesheet+heading_data+tableData
   newWin= window.open("");
   newWin.document.write(print_data);
   newWin.print();
   newWin.close();
    };
 
 
     function myFunction2() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("table-headquarter");
  tr = table.getElementsByTagName("tr");
for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
      td1 = tr[i].getElementsByTagName("td")[1];
      
   
     if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else if(td1){
         txtValue = td1.textContent || td1.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      }else{
        tr[i].style.display = "none"; 
        }
       }
     }
   }
  }
  
  $("#caexcel").click(function(){
       var state_id = $("#state_id").val();
	    var status_id = $("#status_id").val();
	    
	    $.ajax({
            url:"<?php echo admin_url(); ?>hr_profile/export_headquarter_list",
            method:"POST",
            data:{state_id:state_id, status_id:status_id},
            beforeSend: function () {
                $('#searchh3').css('display','block');
                
            },
            complete: function () {
                
                $('#searchh3').css('display','none');
            },
            success:function(data){
                response = JSON.parse(data);
                window.location.href = response.site_url+response.filename;
            }
        });
});
 </script>
</body>
</html>
