<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .table-order tbody{
  display: block;
  max-height: 350px;
  overflow-y: scroll;
}
.table-order thead, .table-order tbody tr{
  display: table;
  table-layout: fixed;
  width: 100%;
}
.table-order thead{
  width: calc(100% - 1.1em);
}
.table-order thead{
  position: relative;
}
.table-order thead th:last-child:after{
  content: ' ';
  position: absolute;
  background-color: #337ab7;
  width: 1.3em;
  height: 38px;
  right: -1.3em;
  top: 0;
  border-bottom: 2px solid #ddd;
}
</style>
<div class="col-md-12">
   <div class="row">
      <div class="col-md-12" id="small-table">
         <div class="panel_s">
            <div class="panel-body">
				<nav aria-label="breadcrumb">
                    				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                    					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                    					<li class="breadcrumb-item active text-capitalize"><b>Transaction</b></li>
                    					<li class="breadcrumb-item active" aria-current="page"><b>Sale List</b></li>
                    				</ol>
                                </nav>
                                <hr class="hr_style">
               <!-- if invoiceid found in url -->
               <?php echo form_hidden('invoiceid',$invoiceid); ?>
               <?php $this->load->view('admin/order/table_html'); ?>
            </div>
         </div>
      </div>
      <div class="col-md-7 small-table-right-col">
         <div id="invoice" class="hide">
         </div>
      </div>
   </div>
</div>
