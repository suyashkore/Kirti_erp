<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .tableFixHead { overflow: auto; max-height: 65vh; width: 100%; position: relative; }
    .tableFixHead thead th { position: sticky; top: 0; z-index: 1; background: #50607b; color: #fff; }
    table { border-collapse: collapse; width: 100%; }
    th, td { padding: 8px 10px; border: 1px solid #ddd; font-size: 12px; }
    th { background: #50607b; color: #fff; font-weight: 600; }
    .btn-actions { white-space: nowrap; }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s" style="margin-top: 1.5rem;">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Master Data</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Customer Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">

                        <?php if (has_permission('customer_master', '', 'create')) { ?>
                            <div class="_buttons">
                                <a href="<?php echo admin_url('customer_master/customer'); ?>" class="btn btn-info">
                                    <i class="fa fa-plus"></i> Add New Customer
                                </a>
                            </div>
                            <div class="clearfix"></div>
                            <hr class="hr-panel-heading" />
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="custom_button">
                                    <a class="btn btn-default" href="javascript:void(0);" onclick="printTable();"><i class="fa fa-print"></i> Print</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search Customer..." style="float: right; padding: 5px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <div class="tableFixHead">
                                <table class="table table-striped table-bordered" id="customerTable">
                                    <thead>
                                        <tr>
                                            <th>Customer Code</th>
                                            <th>Customer Name</th>
                                            <th>Favouring Name</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Category</th>
                                            <th>Territory</th>
                                            <th>Created By</th>
                                            <th>Created Date</th>
                                            <th style="width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($customers)) { ?>
                                            <?php foreach ($customers as $customer) { ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($customer->customer_code); ?></td>
                                                    <td><?php echo htmlspecialchars($customer->customer_name); ?></td>
                                                    <td><?php echo htmlspecialchars($customer->favouring_name); ?></td>
                                                    <td><?php echo htmlspecialchars($customer->mobile_no ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($customer->email_id ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($customer->customer_category ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($customer->territory ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($customer->created_by ?? '-'); ?></td>
                                                    <td><?php echo !empty($customer->created_at) ? date('d-m-Y', strtotime($customer->created_at)) : '-'; ?></td>
                                                    <td class="btn-actions">
                                                        <?php if (has_permission('customer_master', '', 'edit')) { ?>
                                                            <a href="<?php echo admin_url('customer_master/customer/' . $customer->id); ?>" class="btn btn-xs btn-primary" title="Edit">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if (has_permission('customer_master', '', 'delete')) { ?>
                                                            <a href="<?php echo admin_url('customer_master/delete/' . $customer->id); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Are you sure you want to delete this customer?');" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="10" class="text-center">No customers found.</td>
                                            </tr>
                                        <?php } ?>
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

<?php init_tail(); ?>

<script>
function filterTable() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toUpperCase();
    var table = document.getElementById("customerTable");
    var tr = table.getElementsByTagName("tr");
    
    for (var i = 1; i < tr.length; i++) {
        var row = tr[i];
        var cells = row.getElementsByTagName("td");
        var match = false;
        
        for (var j = 0; j < cells.length - 1; j++) {
            if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                match = true;
                break;
            }
        }
        row.style.display = match ? "" : "none";
    }
}

function printTable() {
    var printWindow = window.open('', '', 'height=600,width=800');
    var table = document.getElementById('customerTable').outerHTML;
    var stylesheet = '<style>table{border-collapse: collapse; width: 100%;} th, td{border: 1px solid #000; padding: 8px; text-align: left;} th{background-color: #f2f2f2;}</style>';
    
    printWindow.document.write('<html><head><title>Customer Master Report</title>' + stylesheet + '</head><body>');
    printWindow.document.write('<h2 style="text-align: center;">Customer Master Report</h2>');
    printWindow.document.write(table);
    printWindow.document.write('</body></html>');
    printWindow.print();
}
</script>
