<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .tableFixHead2          { overflow: auto;max-height: 55vh;width:100%;position:relative;top: 0px; }
.tableFixHead2 thead th { position: sticky; top: 0; z-index: 1; }
.tableFixHead2 tbody th { position: sticky; left: 0; }


table  { border-collapse: collapse; width: 100%; }
th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
th     { background: #50607b;
    color: #fff !important; }
</style>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-6">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
            				<ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
            					<li class="breadcrumb-item"><a href="<?= admin_url();?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
            					<li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
            					<li class="breadcrumb-item active" aria-current="page"><b>Country Master</b></li>
							</ol>
						</nav>
                        <hr class="hr_style">
                        <br>
    <div class="row">
        <div class="col-md-12 text-right">
    <input type="text"
           id="myInput1"
           onkeyup="myFunction2()"
           placeholder="Search country..."
           title="Type to search"
           class="form-control"
           style="max-width: 250px; display: inline-block;">
</div>

    <div class="col-md-12">
            <div class="tableFixHead2">
                <table class="table table-striped table-bordered tableFixHead2" width="100%" id="user_list">
                <thead>
               
                <tr>
                    <th class="sortablePop">Country Code</th>
                    <th class="sortablePop">Country Name</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($countries as $value) {
                ?>
                    <tr>
                    <td><?php echo $value['country_id'];?></td>
                    <td><?php echo $value['short_name'];?></td>
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
</div>
</div>
</div>

<?php init_tail(); ?> 
<script>
function myFunction2() {
    var input, filter, table, tr, td, td1, i, txtValue;

    input = document.getElementById("myInput1");
    filter = input.value.toUpperCase();

    table = document.getElementById("user_list");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        td  = tr[i].getElementsByTagName("td")[0];
        td1 = tr[i].getElementsByTagName("td")[1];

        if (td || td1) {
            var text0 = td  ? td.textContent.toUpperCase()  : '';
            var text1 = td1 ? td1.textContent.toUpperCase() : '';

            if (text0.indexOf(filter) > -1 || text1.indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>
 <style type="text/css">
   body{
    overflow: hidden;
   }
 </style>
</body>
</html>
