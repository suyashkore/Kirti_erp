 <div class="_buttons">
    <?php
            $fy = $this->session->userdata('finacial_year');
            $fy_new  = $fy + 1;
            $lastdate_date = '20'.$fy_new.'-03-31';
            $firstdate_date = '20'.$fy_new.'-04-01';
            $curr_date = date('Y-m-d');
            $curr_date_new    = new DateTime($curr_date);
            $last_date_yr = new DateTime($lastdate_date);
            if($last_date_yr < $curr_date_new){
                $to_date = '31/03/20'.$fy_new;
                $from_date = '01/03/20'.$fy_new;
            }else{
                $from_date = "01/".date('m')."/".date('Y');
                $to_date = date('d/m/Y');
            }
            ?> 
        
                <div class="col-md-2">
                    <?php
                   echo render_date_input('from_date','From',$from_date);
                   ?>
                </div>
                <div class="col-md-2">
                    <?php
                   echo render_date_input('to_date','To',$to_date);
                   ?>
                </div>
           
         <div class="col-md-3">
          <!--<a href="<?php echo admin_url('invoice_items/import'); ?>" class="btn btn-info pull-left mleft5"><?php echo _l('rate_filter'); ?></a>
          --><button class="btn btn-info pull-left mleft5 search_data" style="margin-top: 19px;" id="search_data">Show</button>
          <div class="custom_button">
                &nbsp;<a class="btn btn-default buttons-excel buttons-html5"  style="margin-top: 19px;"  tabindex="0" aria-controls="table-daily_report" href="#" id="caexcel"><span>Export to excel</span></a>
                <a class="btn btn-default" href="javascript:void(0);"  style="margin-top: 19px;"  onclick="printPage();">Print</a>
                <!--<a class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="ca_datatable" href="#"><span>Export to PDF</span></a>-->
            </div>
        </div>
        <div class="col-md-5" style="margin-top: 20px;">
            <input type="text" id="myInput1" onkeyup="myFunction2()" placeholder="Search for names.." title="Type in a name" style="float: right;">
        </div>
     
      </div>

        <div class="clearfix mtop20"></div>
                   
            <div class="table-daily_report tableFixHead2">
             
              <table class="tree table table-striped table-bordered table-daily_report tableFixHead2" id="table-daily_report" width="100%">
                  
                <thead>
                 
                    <tr style="display:none;">
                      <td colspan="9" ><h5 style="text-align:center;"><span style="font-size:15px;font-weight:700;"><?php echo $company_detail->company_name; ?></span><br><span style="font-size:10px;font-weight:600;"><?php echo $company_detail->address; ?></span><br><span style="font-size:10px;font-weight:600;">Order List</span><br><span class="report_for" style="font-size:10px;"></span></h5></td>
                  </tr>
                  <tr>
                    <th class="sortable" style="text-align:center;">Sr.No</th>
                    <th class="sortable" style="text-align:left;">Order No.</th> 
                    <th class="sortable" style="text-align:center;">Order Date</th>
                    <th class="sortable" style="text-align:center;">Invoice</th>
                    <th class="sortable" style="text-align:center;">Invoice Date</th>
                    <th class="sortable" style="text-align:center;">Challan</th>
                    <th class="sortable" style="text-align:center;">Party Name</th>
                    <th class="sortable" style="text-align:right;">OrderAmt</th>
                    <th class="sortable" style="text-align:right;">BillAmt</th>
                    <th class="sortable" style="text-align:center;">Order Type</th>
                   
                  </tr>
                </thead>
                <tbody>
                   <?php $selected_company = $this->session->userdata('root_company'); ?>
           
                    
                </tbody>
              </table>   
            </div>
            <span id="searchh2" style="display:none;">Loading.....</span>
<?php defined('BASEPATH') or exit('No direct script access allowed');
?>
