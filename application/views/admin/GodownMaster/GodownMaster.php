<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb custombreadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Inventory</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Godown Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="searchh2" style="display:none;">Please wait fetching data...</div>
                                <div class="searchh3" style="display:none;">Please wait Create new Godown...</div>
                                <div class="searchh4" style="display:none;">Please wait update Godown...</div>
                            </div>
                            <br>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="GodownCode">
                                    <small class="req text-danger">* </small>
                                    <label for="GodownCode" class="control-label">Godown Code</label>
                                    <input type="text" id="GodownCode" name="GodownCode" class="form-control" required oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '')">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="GodownName">Godown Name</label>
                                    <input type="text" name="GodownName" id="GodownName" class="form-control" value="" required>
                                    <input type="hidden" name="form_mode" id="form_mode" value="add">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="Location" class="control-label">Plant Location</label>
                                    <select class="selectpicker display-block" data-width="100%" id="Location" name="Location" data-none-selected-text="<?php echo 'Non Selected'; ?>" data-live-search="true" required>
                                        <option value="" disabled selected>-- Select Plant Location --</option>
                                        <?php foreach ($locations as $st) {
                                        ?>
                                            <option value="<?php echo $st['id']; ?>">
                                                <?php echo $st['LocationName'];?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="PINCode" class="control-label">Pin Code</label>
                                    <input type="text" id="PINCode" name="PINCode" class="form-control" maxlength="6" onkeypress="isNumberOnly(event)">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="state" class="control-label">State</label>
                                    <select class="selectpicker display-block" data-width="100%" id="state" name="state" data-none-selected-text="<?php echo 'Non Selected'; ?>" data-live-search="true">
                                        <option value="" disabled selected>-- Select State--</option>
                                        <?php foreach ($state as $st) { ?>
                                            <option value="<?php echo $st['short_name']; ?>"><?php echo $st['state_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <small class="req text-danger">* </small>
                                    <label for="city" class="control-label">City</label>
                                    <select class="selectpicker display-block" data-width="100%" id="city" name="city" data-none-selected-text="<?php echo 'Non Selected'; ?>" data-live-search="true">
                                            <option value="" disabled selected>-- Select City--</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" app-field-wrapper="GodownCode">
                                    <label for="IsActive" class="control-label">Is Active ?</label>
                                    <select id="IsActive" class="form-control selectpicker" data-live-search="true">
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="Address">Address</label>
                                    <textarea
                                        id="Address"
                                        name="Address"
                                        class="form-control" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <br><br>
                            <div class="col-md-12">
                                <div class="action-buttons text-right">
                                    <?php if (has_permission('GodownMaster', '', 'create')) {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom saveBtn"><i class="fa fa-save"></i> Save</button>
                                    <?php
                                    } else {
                                    ?>
                                        <button type="button" class="btn btn-success btn-group-custom saveBtn2 disabled"><i class="fa fa-save"></i> Save</button>
                                    <?php
                                    } ?>

                                    <?php if (has_permission('GodownMaster', '', 'edit')) {
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
                        <!-- Godown List Model-->

                        <div class="modal fade GodownMaster_List" id="GodownMaster_List" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="padding:5px 10px;">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Godown Master List</h4>
                                    </div>
                                    <div class="modal-body" style="padding:0px 5px !important">

                                        <div class="table-GodownMaster_List tableFixHead2">
                                            <table class="tree table table-striped table-bordered table-GodownMaster_List tableFixHead2" id="table_GodownMaster_List" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="sortablePop" style="text-align:left;">Godown Code </th>
                                                        <th class="sortablePop" style="text-align:left;">Godown Name</th>
                                                        <th class="sortablePop" style="text-align:left;">Location</th>
                                                        <th class="sortablePop" style="text-align:left;">PIN Code</th>
                                                        <th class="sortablePop" style="text-align:left;">State</th>
                                                        <th class="sortablePop" style="text-align:left;">City</th>
                                                        <th class="sortablePop" style="text-align:left;">Address</th>
                                                        <th class="sortablePop" style="text-align:left;">Is Active ?</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="GodownMastertableBody">
                                                    <?php
                                                    foreach ($table_data as $key => $value) {
                                                    ?>
                                                        <tr class="get_GodownMaster" data-id="<?php echo $value["GodownCode"]; ?>">
                                                            <td><?php echo $value["GodownCode"]; ?></td>
                                                            <td><?php echo $value["GodownName"]; ?></td>
                                                            <td><?php echo $value["LocationName"]; ?></td>
                                                            <td><?php echo $value["Pincode"]; ?></td>
                                                            <td><?php echo $value["StateName"]; ?></td>
                                                            <td><?php echo $value["CityName"]; ?></td>
                                                            <td><?php echo $value["Address"]; ?></td>
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
        $("#GodownCode").dblclick(function() {
            $('#GodownMaster_List').modal('show');
            $('#GodownMaster_List').on('shown.bs.modal', function() {
                $('#myInput1').focus();
            })
        });

        // Cancel selected data
        $(".cancelBtn").click(function() {
            var NextGodownCode = $('#NextGodownCode').val();
            $('#GodownCode').val(NextGodownCode);
            $('#GodownName').val('');
            $('#Location').val('').selectpicker('refresh');
            $('#PINCode').val('');
            $('#Address').val('');
            $('#state').val('').selectpicker('refresh');
            $('#city')
                .empty()
                .append('<option value="" disabled selected>-- Select City--</option>')
                .selectpicker('refresh');
            $('#IsActive').val('Y').selectpicker('refresh');


            $('.saveBtn').show();
            $('.saveBtn2').show();
            $('.updateBtn').hide();
            $('.updateBtn2').hide();

        });

        // On Blur ItemID Get All Date
        $('#GodownCode').on('blur', function() {

            let GodownCode = $(this).val().trim();

            // =========================
            // EMPTY → RESET FORM
            // =========================
            if (GodownCode === '') {

                $('#GodownName').val('');
                $('#PINCode').val('');
                $('#Address').val('');
                $('#Location').val('').selectpicker('refresh');

                $('#state').val('').selectpicker('refresh');
                $('#city').empty().append('<option value=""></option>').selectpicker('refresh');
                $('#IsActive').val('Y').selectpicker('refresh');

                $('.saveBtn, .saveBtn2').show();
                $('.updateBtn, .updateBtn2').hide();
                return;
            }

            // =========================
            // FETCH GODOWN DETAILS
            // =========================
            $.ajax({
                url: "<?php echo admin_url(); ?>GodownMaster/GetGodownMasterDetailByID",
                dataType: "JSON",
                method: "POST",
                data: {
                    GodownCode: GodownCode
                },

                beforeSend: function() {
                    $('.searchh2').show().css('color', 'blue');
                },
                complete: function() {
                    $('.searchh2').hide();
                },

                success: function(data) {

                    // =========================
                    // NOT FOUND → SAVE MODE
                    // =========================
                    if (!data || $.isEmptyObject(data)) {

                        $('#GodownName').val('');
                        $('#PINCode').val('');
                        $('#Address').val('');
                        $('#IsActive').val('Y').selectpicker('refresh');
                        $('#Location').val('').selectpicker('refresh');


                        $('#state').val('').selectpicker('refresh');
                        $('#city').empty().append('<option value=""></option>').selectpicker('refresh');

                        $('.saveBtn, .saveBtn2').show();
                        $('.updateBtn, .updateBtn2').hide();
                    }
                    // =========================
                    // FOUND → UPDATE MODE
                    // =========================
                    else {

                        $('#GodownCode').val(data.GodownCode);
                        $('#GodownName').val(data.GodownName);
                        $('#Location').val(data.Location).selectpicker('refresh');
                        $('#PINCode').val(data.Pincode);
                        $('#Address').val(data.Address);
                        $('#IsActive').val(data.IsActive).selectpicker('refresh');

                        $('#state').val(data.State).selectpicker('refresh');
                        $('#state').trigger('change', {
                            cityID: data.City
                        });

                        $('.saveBtn, .saveBtn2').hide();
                        $('.updateBtn, .updateBtn2').show();
                    }
                }
            });
        });


        $(document).on('click', '.get_GodownMaster', function() {
            GodownCode = $(this).attr("data-id");
            $.ajax({
                url: "<?php echo admin_url(); ?>GodownMaster/GetGodownMasterDetailByID",
                dataType: "JSON",
                method: "POST",
                data: {
                    GodownCode: GodownCode
                },
                beforeSend: function() {
                    $('.searchh2').css('display', 'block');
                    $('.searchh2').css('color', 'blue');
                },
                complete: function() {
                    $('.searchh2').css('display', 'none');
                },
                success: function(data) {
                    $('#GodownCode').val(data.GodownCode);
                    $('#GodownName').val(data.GodownName);
                    $('#Location').val(data.Location).selectpicker('refresh');
                    $('#PINCode').val(data.Pincode);
                    $('#state').val(data.State).selectpicker('refresh');
                    $('#state').trigger('change', {
                        cityID: data.City
                    });
                    $('#Address').val(data.Address);
                    $('#IsActive').val(data.IsActive).selectpicker('refresh');


                    $('.saveBtn').hide();
                    $('.updateBtn').show();
                    $('.saveBtn2').hide();
                    $('.updateBtn2').show();
                }
            });
            $('#GodownMaster_List').modal('hide');
        });

        // Save New ItemDivision
        $('.saveBtn').on('click', function() {
            GodownCode = $('#GodownCode').val();
            GodownName = $('#GodownName').val();
            Location = $('#Location').val();

            Pincode = $('#PINCode').val();
            State = $('#state').val();
            City = $('#city').val();
            Address = $('#Address').val();
            IsActive = $('#IsActive').val();


            $.ajax({
                url: "<?php echo admin_url(); ?>GodownMaster/SaveGodownMaster",
                dataType: "JSON",
                method: "POST",
                data: {
                    GodownCode: GodownCode,
                    GodownName: GodownName,
                    Location: Location,
                    Pincode: Pincode,
                    State: State,
                    City: City,
                    Address: Address,
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
                        var NextGodownCode = $('#NextGodownCode').val();
                        $('#GodownCode').val('');
                        $('#GodownName').val('');
                        $('#Location').val('').selectpicker('refresh');
                        $('#PINCode').val('');
                        $('#Address').val('');

                        // Reset dropdowns
                        $('#state').val('').selectpicker('refresh');

                        $('#city')
                            .empty()
                            .append('<option value=""></option>')
                            .selectpicker('refresh');
                        $('#IsActive').val('Y').selectpicker('refresh');

                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        refreshItemDivisionList();
                    } else {
                        alert_float('warning', data.message || 'Something went wrong...');
                    }
                    //    location.reload();
                }
            });
        });
        // Update Exiting Item Division
        $('.updateBtn').on('click', function() {
            GodownCode = $('#GodownCode').val();
            GodownName = $('#GodownName').val();
            Location = $('#Location').val();

            Pincode = $('#PINCode').val();
            State = $('#state').val();
            City = $('#city').val();
            Address = $('#Address').val();
            IsActive = $('#IsActive').val();

            $.ajax({
                url: "<?php echo admin_url(); ?>GodownMaster/UpdateGodownMaster",
                dataType: "JSON",
                method: "POST",
                data: {
                    GodownCode: GodownCode,
                    GodownName: GodownName,
                    Location: Location,
                    Pincode: Pincode,
                    State: State,
                    City: City,
                    Address: Address,
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
                        var NextGodownCode = $('#NextGodownCode').val();
                        $('#GodownCode').val(NextGodownCode);
                        $('#GodownName').val('');
                        $('#Location').val('').selectpicker('refresh');
                        $('#PINCode').val('');
                        $('#Address').val('');

                        // Reset dropdowns
                        $('#state').val('').selectpicker('refresh');

                        $('#city')
                            .empty()
                            .append('<option value=""></option>')
                            .selectpicker('refresh');
                        $('#IsActive').val('Y').selectpicker('refresh');
                        $('.saveBtn').show();
                        $('.updateBtn').hide();
                        $('.saveBtn2').show();
                        $('.updateBtn2').hide();
                        refreshItemDivisionList();
                    } else {
                        alert_float('warning', data.message || 'Something went wrong...');
                    }
                    //    location.reload();
                }
            });
        });
    });
    $('.showListBtn').on('click', function() {
        $('#GodownMaster_List').modal('show');

        $('#GodownMaster_List').on('shown.bs.modal', function() {
            $('#myInput1').focus();
        });
    });
</script>

<script>
    function myFunction2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput1");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_GodownMaster_List");
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
        var table = $("#table_GodownMaster_List tbody");
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

    function refreshItemDivisionList() {
        $('#GodownMastertableBody').load(location.href + ' #GodownMastertableBody > *');
    }
</script>
<script>
    $('#PINCode').on('blur', function() {
        let pincode = $(this).val().trim();
        if (pincode.length !== 6) return;

        fetch('https://api.postalpincode.in/pincode/' + pincode)
            .then(res => res.json())
            .then(data => {
                if (!data || data[0].Status !== "Success") {
                    alert_float('warning', 'PIN code not found');
                    return;
                }

                let postOffice = data[0].PostOffice[0];

                let apiState = postOffice.State.trim();
                let apiCity = postOffice.District.trim();

                // Normalize Delhi
                if (apiState.toLowerCase() === 'new delhi') apiState = 'Delhi';

                // Select state
                let stateMatched = false;
                $('#state option').each(function() {
                    if ($(this).text().trim().toLowerCase() === apiState.toLowerCase()) {
                        $('#state').val($(this).val()).selectpicker('refresh');
                        stateMatched = true;
                    }
                });

                if (!stateMatched) {
                    alert_float('warning', 'State not found');
                    return;
                }

                $('#state').trigger('change', [{
                    cityName: apiCity
                }]);
            });
    });


    $('#state').on('change', function(e, cityData = null) {

        let stateID = $(this).val();
        let url = "<?php echo admin_url(); ?>GodownMaster/GetCityListByStateID";

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                id: stateID
            },
            dataType: 'json',
            success: function(data) {

                $("#city").empty().append('<option value=""></option>');

                $.each(data, function(i, v) {
                    $('#city').append(
                        '<option value="' + v.id + '">' + v.city_name + '</option>'
                    );
                });

                // =========================
                // EDIT MODE (City ID)
                // =========================
                if (cityData && cityData.cityID) {
                    $('#city').val(cityData.cityID);
                }

                // =========================
                // PIN MODE (City NAME)
                // =========================
                if (cityData && typeof cityData.cityName === 'string' && cityData.cityName !== '') {

                    let target = cityData.cityName.toLowerCase();

                    $('#city option').each(function() {
                        let txt = $(this).text();
                        if (txt && txt.toLowerCase().includes(target)) {
                            $('#city').val($(this).val());
                            return false;
                        }
                    });
                }

                $('#city').selectpicker('refresh');
            }
        });
    });
    // Allow numbers only
    function isNumberOnly(event) {
        return event.charCode >= 48 && event.charCode <= 57;
    }
   $('#GodownCode').on('click', function () {
    $('.cancelBtn').trigger('click');
});


</script>

<style>
    #item_code1 {
        text-transform: uppercase;
    }

    #table_GodownMaster_List td:hover {
        cursor: pointer;
    }

    #table_GodownMaster_List tr:hover {
        background-color: #ccc;
    }

    .table-GodownMaster_List {
        overflow: auto;
        max-height: 65vh;
        width: 100%;
        position: relative;
        top: 0px;
    }

    .table-GodownMaster_List thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-GodownMaster_List tbody th {
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

    #GodownCode {
        text-transform: uppercase;
    }
    #GodownName {
        text-transform: uppercase;
    }
</style>