<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Accounts</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Accounts Sub Group 1</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                                <div class="searchh3" style="display:none;">Please wait Create new Sub Group 1...</div>
                                <div class="searchh4" style="display:none;">Please wait update Sub Group 1...</div>
                            </div>
                            <br>
                            <div class="col-md-6">
                                <?php
                                $nextSubGroup1ID = $lastId;
                                ?>
                                <?php //echo render_input('SubGroup1ID','SubGroup1ID',$nextSubGroup1ID,'text'); 
                                ?>
                                <div class="form-group" app-field-wrapper="SubGroup1ID">
                                    <small class="req text-danger">* </small>
                                    <label for="SubGroup1ID" class="control-label">Account Sub Group 1 Code</label>
                                    <input type="text" id="SubGroup1ID" name="SubGroup1ID" required class="form-control" value="<?= $nextSubGroup1ID; ?>">
                                </div>
                                <span id="lblError" style="color: red"></span>
                                <input type="hidden" id="NextSubGroup1ID" name="NextSubGroup1ID" class="form-control" value="<?php echo $nextSubGroup1ID; ?>">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="SubGroupName1">Account Sub Group 1 Name</label>
                                    <input type="text" name="SubGroupName1" id="SubGroupName1" class="form-control" value="" required>
                                </div>
                            </div>
                            <!-- <div class="clearfix"></div> -->

                            <div class="col-md-6">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="MainGroup">Account Main Group Name</label>
                                    <select name="MainGroup" id="MainGroup" class="form-control selectpicker">
                                        <option value=""></option>
                                        <?php
                                        foreach ($AccountMainGroup as $key => $value) {
                                        ?>
                                            <option value="<?php echo $value["ActGroupID"]; ?>"><?php echo $value["ActGroupName"]; ?></option>
                                        <?php
                                        }
                                        ?>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="IsActive" class="control-label">Is Active ?</label>
                                    <select id="IsActive" class="form-control selectpicker" data-live-search="true">
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <br>
                            <div class="col-md-12">
                                <span id="updateWarning"
                                    class="text-warning"
                                    style="display:none; font-size:13px; margin-bottom:6px;">
                                    ⚠ This record is locked and cannot be updated.
                                </span>
                            </div>
                            <br><br>
                            <div class="col-md-12">
                                <div class="action-buttons text-left">
                                    <?php if (has_permission('account_subgroups1', '', 'create')) {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
                                    <?php
                                    } else {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
                                    <?php
                                    } ?>

                                    <?php if (has_permission('account_subgroups1', '', 'edit')) {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom updateBtn"><i class="fa fa-save"></i> Update</button>
                                    <?php
                                    } else {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom updateBtn2 disabled"><i class="fa fa-save"></i> Update</button>
                                    <?php
                                    } ?>



                                    <button type="reset" class="btn btn-warning cancelBtn">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>

                                    <button type="button" class="btn btn-info showListBtn" id="btnShowItemDivisionList">
                                        <i class="fa fa-list"></i> Show List
                                    </button>
                                </div>
                            </div>

                        </div>

                        <div class="clearfix"></div>
                        <!-- Iteme List Model-->

                        <div class="modal fade SubGroup1_List" id="SubGroup1_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="padding:5px 10px;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Account Sub Group 1 List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">

                                        <div class="table-SubGroup1_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered table-SubGroup1_List tableFixHead2" id="table_SubGroup1_List" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="sortablePop" style="text-align:left;">Account Sub Group 1 Code </th>
                                                        <th class="sortablePop" style="text-align:left;">Account Sub Group 1 Name</th>
                                                        <th class="sortablePop" style="text-align:left;">Account Main Group Name</th>
                                                        <th class="sortablePop" style="text-align:left;">IsActive</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="SubGroup1TableBody">
                                                    <?php
                                                    foreach ($AccountSubGroupID1 as $key => $value) {
                                                    ?>
                                                        <tr class="get_SubGroup1" data-id="<?php echo $value["SubActGroupID1"]; ?>">
                                                            <td><?php echo $value['SubActGroupID1']; ?></td>
                                                            <td><?php echo $value["SubActGroupName"]; ?></td>
                                                            <?php
                                                            $mainGroupName = '';
                                                            foreach ($AccountMainGroup as $key1 => $value1) {
                                                                if ($value["ActGroupID"] == $value1["ActGroupID"]) {
                                                                    $mainGroupName = $value1["ActGroupName"];
                                                                }
                                                            }
                                                            ?>
                                                            <td><?php echo $mainGroupName; ?></td>
                                                            <td><?= $value['IsActive'] == 'Y' ? 'Yes' : 'No'; ?></td>

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

<script>
    $(document).ready(function() {
                $('.updateBtn').hide();
                $('.updateBtn2').hide();
                $("#SubGroup1ID").dblclick(function() {
                    $('#SubGroup1_List').modal('show');
                    $('#SubGroup1_List').on('shown.bs.modal', function() {
                        $('#myInput1').focus();
                    })
                });

                // Empty and open create mode
                $("#SubGroup1ID").focus(function() {
                    var nextSubGroup1ID = $('#nextSubGroup1ID').val();
                    $('#SubGroup1ID').val(nextSubGroup1ID);
                    $('#SubGroupName1').val('');
                    $('#MainGroup').val('').selectpicker('refresh');
                    $('#IsActive').val('Y').selectpicker('refresh');

                    $('.saveBtn').show();
                    $('.saveBtn2').show();
                    $('.updateBtn').hide();
                    $('.updateBtn2').hide();

                });

                // Cancel selected data
                $(".cancelBtn").click(function() {
                    var NextSubGroup1ID = $('#NextSubGroup1ID').val();
                    $('#SubGroup1ID').val(NextSubGroup1ID).prop('readonly', false).prop('disabled', false);
                    $('#SubGroupName1').val('').prop('disabled', false).prop('readonly', false);
                    $('#MainGroup').val('').prop('disabled', false).selectpicker('refresh');
                    $('#IsActive').val('Y').prop('disabled', false).selectpicker('refresh');

                    $('#updateWarning').hide();
                    $('.saveBtn').show();
                    $('.saveBtn2').show();
                    $('.updateBtn').hide();
                    $('.updateBtn2').hide();

                });

                // On Blur SubGroup1ID Get All Date
                $('#SubGroup1ID').blur(function() {
                        SubGroup1ID = $(this).val();
                        if (SubGroup1ID == '') {
                            var NextSubGroup1ID = $('#NextSubGroup1ID').val();
                            $('#SubGroup1ID').val(NextSubGroup1ID);
                        } else {
                            $.ajax({
                                    url: "<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details1",
                                    dataType: "JSON",
                                    method: "POST",
                                    data: {
                                        SubGroup1ID: SubGroup1ID
                                    },
                                    beforeSend: function() {
                                        $('.searchh2').css('display', 'block');
                                        $('.searchh2').css('color', 'blue');
                                    },
                                    complete: function() {
                                        $('.searchh2').css('display', 'none');
                                    },
                                    success: function(data) {

                                        if (data == null) {
                                            var NextSubGroup1ID = $('#NextSubGroup1ID').val();
                                            $('#SubGroup1ID').val(NextSubGroup1ID);
                                            $('#SubGroupName1').val('');
                                            $('#MainGroup').val('').selectpicker('refresh');
                                            $('#IsActive').val('').selectpicker('refresh');


                                            $('.saveBtn').show();
                                            $('.updateBtn').hide();
                                            $('.saveBtn2').show();
                                            $('.updateBtn2').hide();
                                        } else {

                                            $('#SubGroup1ID').val(data.SubGroup1ID);
                                            $('#SubGroupName1').val(data.SubGroupName1);
                                            $('#MainGroup').val(data.MainGroup).selectpicker('refresh');
                                            $('#IsActive').val(data.IsActive).selectpicker('refresh');

                                            // =============================
                                            // EDIT CONTROL BASED ON IsEditYN
                                            // =============================
                                            if (data.IsEditYN === 'N') {

                                                // Show warning span
                                                $('#updateWarning').show();

                                                // Disable update button (do NOT hide)
                                                $('.updateBtn').prop('disabled', true).show();
                                                $('.updateBtn2').prop('disabled', true).show();

                                                // Make fields read-only / disabled
                                                $('#SubGroup1ID').prop('disabled', true);
                                                $('#SubGroupName1').prop('readonly', true);
                                                $('#MainGroup').prop('disabled', true).selectpicker('refresh');
                                                $('#IsActive').prop('disabled', true).selectpicker('refresh');

                                            } else {

                                                // Hide warning
                                                $('#updateWarning').hide();

                                                // Enable update button
                                                $('.updateBtn').prop('disabled', false).show();
                                                $('.updateBtn2').prop('disabled', false).show();

                                                // Editable fields
                                                $('#SubGroupName1').val(data.SubGroupName1);
                                                $('#SubGroupName1').prop('disabled', false).selectpicker('refresh');
                                                $('#MainGroup').prop('disabled', false).selectpicker('refresh');
                                                $('#IsActive').prop('disabled', false).selectpicker('refresh');
                                            }

                                            // Common for both cases
                                            $('.saveBtn').hide();
                                            $('.saveBtn2').hide();

                                        }
}
                                    });
                            }

                        });

                    $(document).on('click', '.get_SubGroup1', function() {
                        SubGroup1ID = $(this).attr("data-id");
                        $.ajax({
                            url: "<?php echo admin_url(); ?>accounts_master/get_account_subgroup_details1",
                            dataType: "JSON",
                            method: "POST",
                            data: {
                                SubGroup1ID: SubGroup1ID
                            },
                            beforeSend: function() {
                                $('.searchh2').css('display', 'block');
                                $('.searchh2').css('color', 'blue');
                            },
                            complete: function() {
                                $('.searchh2').css('display', 'none');
                            },
                            // success: function(data) {
                            //     $('#SubGroup1ID').val(data.SubGroup1ID);
                            //     $('#SubGroupName1').val(data.SubGroupName1);
                            //     $('#MainGroup').val(data.MainGroup).selectpicker('refresh');
                            //     $('#IsActive').val(data.IsActive).selectpicker('refresh');


                            //     $('.saveBtn').hide();
                            //     $('.updateBtn').show();
                            //     $('.saveBtn2').hide();
                            //     $('.updateBtn2').show();

                            success: function(data) {

                                $('#SubGroup1ID').val(data.SubGroup1ID);
                                $('#SubGroupName1').val(data.SubGroupName1);
                                $('#MainGroup').val(data.MainGroup).selectpicker('refresh');
                                $('#IsActive').val(data.IsActive).selectpicker('refresh');

                                // =============================
                                // EDIT CONTROL BASED ON IsEditYN
                                // =============================
                                if (data.IsEditYN === 'N') {

                                    // Show warning span
                                    $('#updateWarning').show();

                                    // Disable update button (do NOT hide)
                                    $('.updateBtn').prop('disabled', true).show();
                                    $('.updateBtn2').prop('disabled', true).show();

                                    // Make fields read-only / disabled
                                    $('#SubGroup1ID').prop('disabled', true);
                                    $('#SubGroupName1').prop('readonly', true);
                                    $('#MainGroup').prop('disabled', true).selectpicker('refresh');
                                    $('#IsActive').prop('disabled', true).selectpicker('refresh');

                                } else {

                                    // Hide warning
                                    $('#updateWarning').hide();

                                    // Enable update button
                                    $('.updateBtn').prop('disabled', false).show();
                                    $('.updateBtn2').prop('disabled', false).show();

                                    // Editable fields
                                    $('#SubGroup1ID').val(data.SubGroup1ID).prop('disabled', false).prop('readonly', false);
                                    $('#SubGroupName1').val(data.SubGroupName1).prop('disabled', false).prop('readonly', false);
                                    $('#MainGroup').prop('disabled', false).selectpicker('refresh');
                                    $('#IsActive').prop('disabled', false).selectpicker('refresh');
                                }

                                // Common for both cases
                                $('.saveBtn').hide();
                                $('.saveBtn2').hide();


                            }
                        });
                        $('#SubGroup1_List').modal('hide');
                    });

                    // Save New MainGroup
                    $('.saveBtn').on('click', function() {
                        SubGroup1ID = $('#SubGroup1ID').val();
                        SubGroupName1 = $('#SubGroupName1').val();
                        MainGroup = $('#MainGroup').val();
                        IsActive = $('#IsActive').val();


                        $.ajax({
                            url: "<?php echo admin_url(); ?>accounts_master/SaveSubGroup1",
                            dataType: "JSON",
                            method: "POST",
                            data: {
                                SubGroup1ID: SubGroup1ID,
                                SubGroupName1: SubGroupName1,
                                MainGroup: MainGroup,
                                IsActive: IsActive
                            },
                            beforeSend: function() {
                                $('.searchh3').css('display', 'block');
                                $('.searchh3').css('color', 'blue');
                            },
                            complete: function() {
                                $('.searchh3').css('display', 'none');
                            },
                            success: function(data) {
                                if (data == true) {

                                    alert_float('success', 'Record created successfully...');
                                    var NextSubGroup1ID = $('#NextSubGroup1ID').val();
                                    var newGroupID = parseInt(NextSubGroup1ID) + 1;
                                    $('#SubGroup1ID').val(newGroupID);
                                    $('#NextSubGroup1ID').val(newGroupID);
                                    $('#SubGroupName1').val('');
                                    $('#MainGroup').val('').selectpicker('refresh');
                                    $('#IsActive').val('Y').selectpicker('refresh');


                                    $('.saveBtn').show();
                                    $('.updateBtn').hide();
                                    $('.saveBtn2').show();
                                    $('.updateBtn2').hide();
                                    refreshSubGroup1List();
                                } else {
                                    alert_float('warning', data.message);
                                    $('#SubGroupName1').focus();
                                    return;
                                }
                            }
                        });
                    });
                    // Update Exiting Item Division
                    $('.updateBtn').on('click', function() {
                        SubGroup1ID = $('#SubGroup1ID').val();
                        SubGroupName1 = $('#SubGroupName1').val();
                        MainGroup = $('#MainGroup').val();
                        IsActive = $('#IsActive').val();

                        $.ajax({
                            url: "<?php echo admin_url(); ?>accounts_master/UpdateSubGroup1",
                            dataType: "JSON",
                            method: "POST",
                            data: {
                                SubGroup1ID: SubGroup1ID,
                                SubGroupName1: SubGroupName1,
                                MainGroup: MainGroup,
                                IsActive: IsActive
                            },
                            beforeSend: function() {
                                $('.searchh4').css('display', 'block');
                                $('.searchh4').css('color', 'blue');
                            },
                            complete: function() {
                                $('.searchh4').css('display', 'none');
                            },
                            success: function(data) {
                                if (data == true) {
                                    alert_float('success', 'Record updated successfully...');
                                    var NextSubGroup1ID = $('#NextSubGroup1ID').val();
                                    $('#SubGroup1ID').val(NextSubGroup1ID);
                                    $('#SubGroupName1').val('');
                                    // $('#GroupType').val('').selectpicker('refresh');
                                    $('#MainGroup').val('').selectpicker('refresh');
                                    $('#IsActive').val('Y').selectpicker('refresh');

                                    $('.saveBtn').show();
                                    $('.updateBtn').hide();
                                    $('.saveBtn2').show();
                                    $('.updateBtn2').hide();
                                    refreshSubGroup1List();
                                } else {
                                    alert_float('warning', data.message || 'Something went wrong...');
                                }
                            }
                        });
                    });
                }); 
                $('.showListBtn').on('click', function() {
                $('#SubGroup1_List').modal('show');

                $('#SubGroup1_List').on('shown.bs.modal', function() {
                    $('#myInput1').focus();
                });
            });
</script>

<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_SubGroup1_List");
        tr = table.getElementsByTagName("tr");
        for (i = 1; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            td1 = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else if (td1) {
                    txtValue = td1.textContent || td1.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    }

    $(document).on("click", ".sortablePop", function() {
        var table = $("#table_SubGroup1_List tbody");
        var rows = table.find("tr").toArray();
        var index = $(this).index();
        var ascending = !$(this).hasClass("asc");


        // Remove existing sort classes and reset arrows
        $(".sortablePop").removeClass("asc desc");
        $(".sortablePop span").remove();

        // Add sort classes and arrows
        $(this).addClass(ascending ? "asc" : "desc");
        $(this).append(ascending ? '<span> &#8593;</span>' : '<span> &#8595;</span>');

        rows.sort(function(a, b) {
            var valA = $(a).find("td").eq(index).text().trim();
            var valB = $(b).find("td").eq(index).text().trim();

            if ($.isNumeric(valA) && $.isNumeric(valB)) {
                return ascending ? valA - valB : valB - valA;
            } else {
                return ascending ?
                    valA.localeCompare(valB) :
                    valB.localeCompare(valA);
            }
        });
        table.append(rows);
    });
</script>
<script>
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode = 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function refreshSubGroup1List() {
        $('#SubGroup1TableBody').load(location.href + ' #SubGroup1TableBody > *');
    }
</script>

<style>
    .modal-content {
        width: 110%;
    }

    #item_code1 {
        text-transform: uppercase;
    }

    #table_SubGroup1_List td:hover {
        cursor: pointer;
    }

    #table_SubGroup1_List tr:hover {
        background-color: #ccc;
    }

    .table-SubGroup1_List {
        overflow: auto;
        max-height: 65vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-SubGroup1_List thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-SubGroup1_List tbody th {
        position: sticky;
        left: 0;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        padding: 1px 5px !important;
        white-space: nowrap;
        border: 1px solid !important;
        font-size: 11px;
        line-height: 1.42857143 !important;
        vertical-align: middle !important;
    }

    th {
        background: #50607b;
        color: #fff !important;
    }

    #SubGroupName1 {
        text-transform: uppercase;
    }
</style>