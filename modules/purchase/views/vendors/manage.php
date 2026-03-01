<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<style>

    .table-daily_report          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }

.table-daily_report thead th { position: sticky; top: 0; z-index: 1; }

.table-daily_report tbody th { position: sticky; left: 0; }





table  { border-collapse: collapse; width: 100%; }

th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}

th     { background: #50607b;

    color: #fff !important; }

</style>

<div id="wrapper">

   <div class="content">

      <div class="row">

         <div class="col-md-12">

            

            <div class="panel_s">

               <div class="panel-body">

                   <nav aria-label="breadcrumb">

            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">

            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>

            					<li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>

            					<li class="breadcrumb-item active" aria-current="page"><b>Vendor List</b></li>

							</ol>

						</nav>

                        <hr class="hr_style">

                  <div class="_buttons">

                     <!--<?php if (has_permission_new('vendors','','create')) { ?>

                     <a href="<?php echo admin_url('purchase/vendor'); ?>" class="btn btn-info mright5 test pull-left display-block">

                     <?php echo _l('new_vendor'); ?></a>-->



                     <!--<a href="<?php // echo admin_url('purchase/vendor_import'); ?>" class="btn btn-info mright5 test pull-left display-block">-->

                     <!--<?php echo _l('import_vendors'); ?></a>-->



                     <!--<a href="<?php //echo admin_url('purchase/all_contacts'); ?>" class="btn btn-info pull-left display-block mright5">-->

                     <!--<?php echo _l('vendor_contacts'); ?></a>-->

                     

                  <?php } ?>

                  </div>

                 

                  

                  

                  <div class="clearfix mtop20"></div>



                 

                   <div class="col-md-3">

                    <?php 

                        echo render_select( 'states',$states,array( 'short_name',array( 'state_name')), 'client_state',$selected??'',array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));

                    ?>

                 </div>

                  <div class="col-md-2">

                       <div class="form-group">

                            <label class="control-label">Status</label>

                            <select name="status" id="status" class="form-control">

                                <option value="">Non selected</option>

                                <option  value="1">Active</option>

                                <option value="0">DeActive</option>

                            </select>

                        </div>

                 </div>

                 <button class="btn btn-info pull-left mleft5 " style="margin-top: 19px;" id="search_data">Show</button>

                  <div class="row col-md-12 d"><hr/></div>



                  <a href="#"  onclick="staff_bulk_actions(); return false;" data-table=".table-vendors" class=" hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>

              <div class="custom_button">

            &nbsp;<a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 19px;"  tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>

            <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 19px;"  onclick="printPage();">Print</a>

            <!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->

        </div>    

                 <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">

              <div class="table-daily_report tableFixHead2">

             

              <table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">

                  

                <thead>

                 

                    <tr style="display:none;">

                      <td colspan="9" id="header_info"><h5 style="text-align:center;">
                          <span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span>
                          <br>
                          <span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span>
                          <br>
                          <span class="report_for" style="font-size:10px;"></span></h5>
                    </td>

                  </tr>

                  <tr>

                    <th class="sortable" style="text-align:left;">Vendor type</th>

                    <th class="sortable" style="text-align:left;" >AccountID </th>

                    <th class="sortable" style="text-align:left;">Firm Name</th>
                    
                    <th class="sortable" style="text-align:left;">Favouring Name</th>

                    
                    <th class="sortable" style="text-align:left;">Address</th>
                    
                    <th class="sortable" style="text-align:left;">State</th>
                    
                    <th class="sortable" style="text-align:left;">City</th>
                    
                    <th class="sortable" style="text-align:left;">Pincode</th>
                    
                    <th class="sortable" style="text-align:left;">Station</th>
                    
                    <th class="sortable" style="text-align:left;">Mobile Number</th>
                    
                    <th class="sortable" style="text-align:left;">Alt Number</th>
                    
                    <th class="sortable" style="text-align:left;">Email</th>
                    
                    <th class="sortable" style="text-align:left;">GST Number</th>
                    
                    <th class="sortable" style="text-align:left;">Status</th>

                    
                    <th class="sortable" style="text-align:left;">Approve Status</th>

                  </tr>

                </thead>

                <tbody>

                </tbody>

              </table>   

            </div>

            <span id="searchh2" style="display:none;">Loading.....</span>

                 

               </div>

            </div>

         </div>

      </div>

   </div>

</div>

<?php init_tail(); ?>

<script src="<?= base_url() ?>public/plugins/jquery.table2excel.js"></script>

<script>

$(document).on("click", ".sortable", function () {

		var table = $("#table-daily_report tbody");

		var rows = table.find("tr").toArray();

		var index = $(this).index();

		var ascending = !$(this).hasClass("asc");

		

		

		// Remove existing sort classes and reset arrows

		$(".sortable").removeClass("asc desc");

		$(".sortable span").remove();

		

		// Add sort classes and arrows

		$(this).addClass(ascending ? "asc" : "desc");

		$(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');

		

		rows.sort(function (a, b) {

			var valA = $(a).find("td").eq(index).text().trim();

			var valB = $(b).find("td").eq(index).text().trim();

			

			if ($.isNumeric(valA) && $.isNumeric(valB)) {

				return ascending ? valA - valB : valB - valA;

				} else {

				return ascending

                ? valA.localeCompare(valB)

                : valB.localeCompare(valA);

			}

		});

		table.append(rows);

	});	

</script>

</body>

</html>

