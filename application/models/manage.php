<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('customer_master/create'); ?>" class="btn btn-info pull-left display-block">
                                <?php echo _l('new_customer_master'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable(array(
                            _l('id'),
                            _l('customer_code'),
                            _l('customer_name'),
                            _l('mobile_no'),
                            _l('email'),
                            _l('city'),
                            _l('options')
                        ),'customer_master'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-customer_master', window.location.href, [6], [6]);
    });
</script>
</body>
</html>