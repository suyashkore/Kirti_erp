<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb" style="background-color:#fff !important; margin-Bottom:0px !important;">
                                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                                <li class="breadcrumb-item active text-capitalize"><b>Master</b></li>
                                <li class="breadcrumb-item active" aria-current="page"><b>Currency Master</b></li>
                            </ol>
                        </nav>
                        <hr class="hr_style">

                        <?php echo form_open(admin_url('CurrencyMaster/create'), ['id' => 'currencyForm']); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_input('code', 'Currency Code*', '', 'text', ['required' => true, 'placeholder' => 'Currency Code is required!']); ?>
                            </div>

                            <div class="col-md-4">
                                <?php echo render_input('description', 'Currency Description*', '', 'text', ['required' => true, 'placeholder' => 'Currency Description is required!']); ?>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country" class="control-label">Country*</label>
                                    <select class="selectpicker display-block" data-width="100%" id="country" name="country" required data-live-search="true">
                                        <option value=""></option>
                                        <?php if(!empty($countries)) { foreach ($countries as $st) { ?>
                                        <?php $countryName = (is_array($st) && isset($st['short_name'])) ? $st['short_name'] : $st; ?>
                                        <option value="<?php echo $countryName; ?>" <?php if(!isset($currency) && $countryName == 'India'){ echo 'selected'; } else if(isset($currency) && $currency->country == $countryName){echo 'selected';} ?>><?php echo $countryName; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <?php
                                    $blocked = [
                                        ['id' => 'No', 'name' => 'No'],
                                        ['id' => 'Yes', 'name' => 'Yes']
                                    ];
                                    echo render_select('blocked', $blocked, ['id','name'], 'Blocked', 'No');
                                ?>
                            </div>
                        </div>

                        <div class="text-right">
                            <hr>
                            <button type="submit" class="btn btn-success">Create</button>
                            <button type="button" class="btn btn-danger cancelBtn">Cancel</button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>

                <div class="panel_s" style="margin-top:15px;">
                    <div class="panel-body">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <!-- <button class="btn btn-default btn-sm exportBtn">Export</button> -->
                            </div>
                            <div class="col-md-6 text-right">
                                <!-- Pagination handled by dt-table class -->
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dt-table" id="currency_table">
                                <thead>
                                    <tr>
                                        <th style="background-color : #50607b; ">Sr No.</th>
                                        <th style="background-color : #50607b;">Currency Code</th>
                                        <th style="background-color : #50607b;">Currency Description</th>
                                        <th style="background-color : #50607b;">Country</th>
                                        <th style="background-color : #50607b;">Blocked</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($currencies)){ 
                                        $i = 1;
                                        foreach($currencies as $f){ ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $f['code']; ?></td>
                                            <td><?php echo $f['description']; ?></td>
                                            <td><?php echo $f['country']; ?></td>
                                            <td><?php echo $f['blocked']; ?></td>
                                        </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        // Validation
        appValidateForm($('#currencyForm'), {
            code: 'required',
            description: 'required',
            country: 'required'
        }, currencyFormHandler);

        function currencyFormHandler(form) {
            var $form = $(form);
            var data = $form.serialize();
            $.post('<?php echo admin_url('CurrencyMaster/create'); ?>', data, function(resp){
                try{
                    var r = typeof resp === 'object' ? resp : JSON.parse(resp);
                    if(r.success){
                        alert(r.message || 'Currency added');
                        location.reload();
                    } else {
                        alert(r.message || 'Error');
                    }
                } catch(e){ alert('Unexpected response'); }
            });
            return false; // prevent default form submit
        }

        // Cancel button action
        $('.cancelBtn').on('click', function() {
            $('#currencyForm')[0].reset();
            $('select[name="country"]').val('India').selectpicker('refresh');
            $('select[name="blocked"]').val('No').selectpicker && $('select[name="blocked"]').selectpicker('refresh');
        });

        // Export function (optional)
        $('.exportBtn').on('click', function(){
            function tableToCSV($table) {
                var $rows = $table.find('tr:has(td), tr:has(th)'),
                    tmpColDelim = String.fromCharCode(11), // vertical tab
                    tmpRowDelim = String.fromCharCode(0), // null char
                    colDelim = '"' + ',' + '"',
                    rowDelim = '\r\n',
                    csv = '"' + $rows.map(function(i, row) {
                        var $row = $(row),
                            $cols = $row.find('td, th');

                        return $cols.map(function(j, col) {
                            var text = $(col).text().trim();
                            text = text.replace(/"/g, '""');
                            return text;
                        }).get().join('\",\"');

                    }).get().join(rowDelim) + '"';

                return csv;
            }

            var csv = tableToCSV($('#currency_table'));
            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'currency_list.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

    });
</script>
<style>
    th, td { padding: 1px 5px !important; white-space: nowrap; border:1px solid !important;font-size:11px; line-height:1.42857143!important;vertical-align: middle !important;}
    th     { background: #50607b; color: #fff !important; }
</style>
