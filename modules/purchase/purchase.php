<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*
Module Name: Purchase
Description: Purchase Management Module is a tool for managing your day-to-day purchases. It is packed with all necessary features that are needed by any business, which has to buy raw material for manufacturing or finished good purchases for trading
Version: 1.0.9
Requires at least: 2.3.*
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/
define('PURCHASE_MODULE_NAME', 'purchase');
define('PURCHASE_MODULE_UPLOAD_FOLDER', module_dir_path(PURCHASE_MODULE_NAME, 'uploads'));
hooks()->add_action('admin_init', 'purchase_permissions');
hooks()->add_action('app_admin_footer', 'purchase_head_components');
hooks()->add_action('app_admin_footer', 'purchase_add_footer_components');
hooks()->add_action('app_admin_head', 'purchase_add_head_components');
hooks()->add_action('admin_init', 'purchase_module_init_menu_items');
hooks()->add_action('before_expense_form_name', 'init_vendor_option');
hooks()->add_action('after_custom_fields_select_options', 'init_vendor_customfield');
hooks()->add_action('after_custom_fields_select_options', 'init_po_customfield');
hooks()->add_action('after_customer_admins_tab', 'init_tab_pur_order');
hooks()->add_action('after_custom_profile_tab_content', 'init_content_pur_order');
//PO task
hooks()->add_action('task_related_to_select', 'po_related_to_select');
hooks()->add_filter('before_return_relation_values', 'po_relation_values', 10, 2);
hooks()->add_filter('before_return_relation_data', 'po_relation_data', 10, 4);
hooks()->add_filter('tasks_table_row_data', 'po_add_table_row', 10, 3);
//Purchase quotation task
hooks()->add_action('task_related_to_select', 'pq_related_to_select');
hooks()->add_filter('before_return_relation_values', 'pq_relation_values', 10, 2);
hooks()->add_filter('before_return_relation_data', 'pq_relation_data', 10, 4);
hooks()->add_filter('tasks_table_row_data', 'pq_add_table_row', 10, 3);
//Purchase contract task
hooks()->add_action('task_related_to_select', 'pc_related_to_select');
hooks()->add_filter('before_return_relation_values', 'pc_relation_values', 10, 2);
hooks()->add_filter('before_return_relation_data', 'pc_relation_data', 10, 4);
hooks()->add_filter('tasks_table_row_data', 'pc_add_table_row', 10, 3);
//Purchase invoice task
hooks()->add_action('task_related_to_select', 'pi_related_to_select');
hooks()->add_filter('before_return_relation_values', 'pi_relation_values', 10, 2);
hooks()->add_filter('before_return_relation_data', 'pi_relation_data', 10, 4);
hooks()->add_filter('tasks_table_row_data', 'pi_add_table_row', 10, 3);
//cronjob auto reset purchase order/request number
hooks()->add_action('after_cron_run', 'reset_pur_order_number');
hooks()->add_action('after_cron_run', 'reset_pur_request_number');
//Purchase mail template
register_merge_fields('purchase/merge_fields/purchase_order_merge_fields');
hooks()->add_filter('other_merge_fields_available_for', 'purchase_register_other_merge_fields');
define('PURCHASE_PATH', 'modules/purchase/uploads/');
define('PURCHASE_MODULE_ITEM_UPLOAD_FOLDER', 'modules/purchase/uploads/item_img/');
define('PURCHASE_REVISION', 109);
define('COMMODITY_ERROR_PUR', FCPATH);
define('COMMODITY_EXPORT_PUR', FCPATH);
define('PURCHASE_IMPORT_ITEM_ERROR', 'modules/purchase/uploads/import_item_error/');
/**
* Register activation module hook
*/
register_activation_hook(PURCHASE_MODULE_NAME, 'purchase_module_activation_hook');
/**
* Load the module helper
*/
$CI = &get_instance();
$CI->load->helper(PURCHASE_MODULE_NAME . '/purchase');
function purchase_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(PURCHASE_MODULE_NAME, [PURCHASE_MODULE_NAME]);
/**
 * Init goals module menu items in setup in admin_init hook
 * @return null
 */
function purchase_module_init_menu_items()
{
    $CI = &get_instance();
    if (has_permission_new('vendors', '', 'view') || has_permission_new('purchase-order-po', '', 'view') || has_permission_new('purchase-order-list', '', 'view') || has_permission_new('purchase-pending-order-po-list', '', 'view') || has_permission_new('purchase-order-po-list', '', 'view') || has_permission_new('purchase-order', '', 'view') || has_permission_new('purchase-return', '', 'view') || has_permission_new('TradePayableReport', '', 'view') || has_permission_new('Stock_alert', '', 'view') || has_permission_new('purchase_register', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('purchase', [
            'name' => _l('purchase'),
            'icon' => 'fa fa-shopping-cart',
            'position' => 2,
        ]);
        $CI->db->where('module_name', 'warehouse');
        $module = $CI->db->get(db_prefix() . 'modules')->row();
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
         'slug'     => 'purchase-items',
         'name'     => _l('items'),
         'icon'     => 'fa fa-clone menu-icon',
         'href'     => admin_url('purchase/items'),
         'position' => 1,
         ]);*/
        // if (has_permission_new('PurchaseDashboard', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'PurchaseDashboard',
        //         'name' => 'Dashboard',
        //         'icon' => 'fa fa-user-o',
        //         'href' => admin_url('purchase/NewPurchaseDashboard'),
        //         'position' => 1,
        //     ]);
        // }
        if (has_permission_new('vendors', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'vendors',
                'name' => _l('vendor'),
                'icon' => 'fa fa-user-o',
                'href' => admin_url('purchase/AddEditVendor'),
                'position' => 1,
            ]);
        }
        if (has_permission_new('VendorList', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'VendorList',
                'name' => 'Vendor List',
                'icon' => 'fa fa-user-o',
                'href' => admin_url('purchase/vendors'),
                'position' => 2,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => 'Purchase Quotation',
                'href' => admin_url('purchase/Quotation'),
                'position' => 3,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => 'Quotation List',
                'href' => admin_url('purchase/Quotation/List'),
                'position' => 4,
            ]);
        }
        if (has_permission_new('purchase-order-po', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-order-po',
                'name' => "Purchase Order",
                'icon' => 'fa fa-cart-plus',
                'href' => admin_url('purchase/AddPurchaseOrder'),
                'position' => 5,
            ]);
        }
        if (has_permission_new('purchase-order-po-list', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-order-po-list',
                'name' => "Order List",
                'icon' => 'fa fa-cart-plus',
                'href' => admin_url('purchase/Orders/List'),
                'position' => 6,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => 'Generate ASN',
                'href' => admin_url('purchase/Inwards/ASN'),
                'position' => 7,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => 'Ganerate Gate In',
                'href' => admin_url('purchase/Vehiclein'),
                'position' => 8,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => 'Purchase Inward',
                'href' => admin_url('purchase/Inwards'),
                'position' => 9,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => 'Gate Control',
                'href' => admin_url('purchase/Vehiclein/GateControl'),
                'position' => 10,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => 'Inward List',
                'href' => admin_url('purchase/Inwards/Reports'),
                'position' => 11,
            ]);
        }
        if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => "Purchase Invoice",
                'icon' => 'fa fa-file-invoice',
                'href' => admin_url('purchase/AddPurchaseInvoice'),
                'position' => 12,
            ]);
        }
            if (has_permission_new('purchase-invoice', '', 'view')) {
            $CI->app_menu->add_sidebar_children_item('purchase', [
                'slug' => 'purchase-invoice',
                'name' => "Goods Receive Note (GRN)",
                'icon' => 'fa fa-file-invoice',
                'href' => admin_url('purchase/Grn'),
                'position' => 13,
            ]);
        }
        // if (has_permission_new('purchase-pending-order-po-list', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'purchase-pending-order-po-list',
        //         'name' => "Pending Order List",
        //         'icon' => 'fa fa-cart-plus',
        //         'href' => admin_url('purchase/PendingPurchaseOrderList'),
        //         'position' => 7,
        //     ]);
        // }
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
            'slug'     => 'vendors-items',
            'name'     => _l('vendor_item'),
            'icon'     => 'fa fa-newspaper-o',
            // 'href'     => admin_url('purchase/vendor_items'),
            'href'     => admin_url('purchase/new_vendor_items'),
            'position' => 3,
        ]);*/
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
            'slug'     => 'purchase-request',
            'name'     => _l('purchase_request'),
            'icon'     => 'fa fa-shopping-basket',
            'href'     => admin_url('purchase/purchase_request'),
            'position' => 4,
        ]);*/
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
            'slug'     => 'purchase-quotation',
            'name'     => _l('quotations'),
            'icon'     => 'fa fa-file-powerpoint-o',
            'href'     => admin_url('purchase/quotations'),
            'position' => 5,
        ]);*/
        // if (has_permission_new('purchase-order-po-list', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug'     => 'purchase-order-po-list',
        //         'name'     => "Purchase Order List",
        //         'icon'     => 'fa fa-cart-plus',
        //         // 'href'     => admin_url('purchase/purchase_order'),
        //         'href'     => admin_url('purchase/PurchaseOrderList'),
        //         'position' => 4,
        //     ]);
        // }
        // if (has_permission_new('purchase-order', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'purchase-order',
        //         'name' => 'Purchase Entry',
        //         'icon' => 'fa fa-cart-plus',
        //         'href' => admin_url('purchase/PurchaseEntry'),
        //         'position' => 5,
        //     ]);
        // }
        // if (has_permission_new('purchase-order-list', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'purchase-order-list',
        //         'name' => 'Purchase Entry List',
        //         'icon' => 'fa fa-cart-plus',
        //         'href' => admin_url('purchase/Purchase_entry_Report'),
        //         'position' => 5,
        //     ]);
        // }
        // if (has_permission_new('purchase-return', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'purchase-order',
        //         'name' => "Purchase Return",
        //         'icon' => 'fa fa-cart-plus',
        //         'href' => admin_url('purchase/pur_return'),
        //         'position' => 5,
        //     ]);
        // }
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
            'slug'     => 'purchase-contract',
            'name'     => _l('contracts'),
            'icon'     => 'fa fa-file-text-o',
            'href'     => admin_url('purchase/contracts'),
            'position' => 7,
        ]);*/
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
            'slug'     => 'purchase-invoices',
            'name'     => _l('invoices'),
            'icon'     => 'fa fa-clipboard',
            'href'     => admin_url('purchase/invoices'),
            'position' => 8,
        ]);*/
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
            'slug'     => 'purchase_reports',
            'name'     => _l('reports'),
            'icon'     => 'fa fa-bar-chart',
            'href'     => admin_url('purchase/reports'),
            'position' => 9,
        ]);*/
        /*$CI->app_menu->add_sidebar_children_item('purchase', [
            'slug'     => 'purchase-settings',
            'name'     => _l('setting'),
            'icon'     => 'fa fa-gears',
            'href'     => admin_url('purchase/setting'),
            'position' => 10,
        ]);*/
        // if (has_permission_new('TradePayableReport', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'TradePayableReport',
        //         'name' => 'Trade Payable Report',
        //         'href' => admin_url('purchase/BillsPayableReport'),
        //         'position' => 10,
        //     ]);
        // }
        // if (has_permission_new('Stock_alert', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'Stock_alert',
        //         'name' => 'Stock Alert',
        //         'href' => admin_url('Sale_reports/Stock_alert'),
        //         'position' => 10,
        //     ]);
        // }
        // if (has_permission_new('purchase_register', '', 'view')) {
        //     $CI->app_menu->add_sidebar_children_item('purchase', [
        //         'slug' => 'purchase-register',
        //         'name' => 'Purchase Register',
        //         'href' => admin_url('purchase/pur_register'),
        //         'position' => 11,
        //     ]);
        // }
    }
}
/**
 * { purchase permissions }
 */
function purchase_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    $capabilities2 = [];
    $capabilities2['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    $capabilities3 = [];
    $capabilities3['capabilities'] = [
        'view' => ['not_applicable' => true, 'name' => _l('permission_global')],
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => _l('permission_edit'),
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    $capabilities_view = [];
    $capabilities_view['capabilities'] = [
        'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    //register_staff_capabilities('purchase-items', $capabilities, _l('items'),'Purchase');
    register_staff_capabilities('PurchaseDashboard', $capabilities_view, _l('Dashboard'), 'Purchase');
    register_staff_capabilities('vendors', $capabilities, _l('vendor'), 'Purchase');
    register_staff_capabilities('VendorList', $capabilities2, 'VendorList', 'Purchase');
    // register_staff_capabilities('vendors-items', $capabilities, _l('vendor_item'),'Purchase');
    register_staff_capabilities('purchase-order-po', $capabilities, 'Purchase Order', 'Purchase');
    register_staff_capabilities('purchase-order-po-list', $capabilities_view, 'Purchase Order List', 'Purchase');
    register_staff_capabilities('purchase-pending-order-po-list', $capabilities_view, 'Pending Purchase Order List', 'Purchase');
    register_staff_capabilities('purchase-order-po-approve', $capabilities3, 'Purchase Order Approval', 'Purchase');
    register_staff_capabilities('purchase-order', $capabilities, 'Purchase Entry', 'Purchase');
    register_staff_capabilities('purchase-order-list', $capabilities_view, 'Purchase Entry List', 'Purchase');
    register_staff_capabilities('purchase-order-approve', $capabilities3, 'Purchase Entry Approval', 'Purchase');
    register_staff_capabilities('purchase-return', $capabilities, _l('purchase_return'), 'Purchase');
    register_staff_capabilities('TradePayableReport', $capabilities_view, 'Trade Payable Report', 'Purchase');
    register_staff_capabilities('Stock_alert', $capabilities_view, 'Stock Alert', 'Purchase');
    register_staff_capabilities('purchase_register', $capabilities_view, 'Purchase Register', 'Purchase');
    //register_staff_capabilities('purchase-invoices', $capabilities, _l('invoices'),'Purchase');
}
/**
 * purchase add footer components
 * @return
 */
function purchase_add_footer_components()
{
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri, '/admin/purchase/vendors') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/vendor_manage.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchase_request') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/pur_request_manage.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/quotations') === false)) {
        echo '<script src="' . base_url('assets/plugins/signature-pad/signature_pad.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/quotation_manage.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_request') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/view_pur_request') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('assets/plugins/signature-pad/signature_pad.min.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/VehicleInspection') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/Item_QC') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/Metal_Detector_Report') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/In_Process_QC') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/FG_Test_Report') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchase_order') === false)) {
        echo '<script src="' . base_url('assets/plugins/signature-pad/signature_pad.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/purchase_order_manage.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/contracts') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/contract_manage.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/contract') === false)) {
        echo '<script src="' . base_url('assets/plugins/signature-pad/signature_pad.min.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/setting') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/manage_setting.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/PurchaseEntry') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/AddPurchaseOrder') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/Discount_Master/Manage') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_return') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/Damage_entry/AddEdit') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/Stock_adjustment') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/stock_adjustment/stock_list') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/EditPurchaseEntry') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/EditPurchaseOrder') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchaseRtn_list') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchaseRtn_list') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/estimate') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/add_update_quotation') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/PurchaseEntry') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/AddPurchaseOrder') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/pur_return') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, 'admin/Stock_adjustment') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/pur_request') === false)) {
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/reports') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/items') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.jquery.min.js') . '"></script>';
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.js') . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/new_vendor_items') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/vendor_items.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/setting?group=commodity_group') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<script src="https://momentjs.com/downloads/moment-timezone.min.js"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/setting?group=sub_group') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link rel="stylesheet prefetch" href="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.css') . '">';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
        echo '<script src="' . base_url('modules/purchase/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
        echo '<script src="https://momentjs.com/downloads/moment-timezone.min.js"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/invoices') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/manage_invoices.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_invoice') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/pur_invoice.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchase_invoice') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/js/pur_invoice_preview.js') . '?v=' . PURCHASE_REVISION . '"></script>';
    }
}
/**
 * add purchase add head components
 * @return
 */
function purchase_add_head_components()
{
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri, '/admin/purchase/pur_request') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/VehicleInspection') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/Item_QC') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/Metal_Detector_Report') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/In_Process_QC') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/FG_Test_Report') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/view_pur_request') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/estimate') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/purchase/vendors_portal/add_update_quotation') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/purchase/vendors_portal/PurchaseEntry') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/purchase/vendors_portal/AddPurchaseOrder') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/purchase/vendors_portal/pur_return') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/Damage_entry/AddEdit') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/purchase/vendors_portal/pur_request') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/PurchaseEntry') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/AddPurchaseOrder') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/Discount_Master/Manage') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_return') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/Stock_adjustment') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/stock_adjustment/stock_list') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/EditPurchaseEntry') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/EditPurchaseOrder') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchaseRtn_list') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/setting?group=units') === false)) {
        echo '<script src="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/setting.css') . '"  rel="stylesheet" type="text/css" />';
    }
}
/**
 * purchase head components
 * @return
 */
function purchase_head_components()
{
    $CI = &get_instance();
    $viewuri = $_SERVER['REQUEST_URI'];
    if (!(strpos($viewuri, '/admin/purchase') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/style.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/contract') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/contract.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/setting') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/setting.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/commodity_list.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchase_order') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_manage.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/PurchaseEntry') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/AddPurchaseOrder') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/Discount_Master/Manage') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/EditPurchaseEntry') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/EditPurchaseOrder') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchaseRtn_list') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_return') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/Damage_entry/AddEdit') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/stock_adjustment/stock_list') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_request') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_request.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchase_request') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_request_manage.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/view_pur_request') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/view_pur_request.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/estimate') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/add_update_quotation') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/PurchaseEntry') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, 'purchase/vendors_portal/AddPurchaseOrder') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_template.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/quotations') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/estimate_preview_template.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/vendor') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_manage.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/items') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/commodity_list.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/simple-lightbox.min.css') . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/plugins/simplelightbox/masonry-layout-vanilla.min.css') . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/pur_invoice') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_invoice.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/purchase_invoice') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_invoice.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
    if (!(strpos($viewuri, '/admin/purchase/payment_invoice') === false)) {
        echo '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/payment_invoice.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
}
/**
 * Initializes the vendor option.
 *
 * @param      string  $expense  The expense
 */
function init_vendor_option($expense = '')
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    $list_vendor = $CI->purchase_model->get_vendor();
    $option = '';
    $option .= '<div class="row">';
    $option .= '<div class="col-md-12">';
    $option .= '<lable for="vendor">' . _l('vendor') . '</label>';
    $option .= '<select name="vendor" id="vendor" data-width="100%" class="selectpicker" data-live-search="true" data-none-selected-text="' . _l('ticket_settings_none_assigned') . '">';
    $select = '';
    $option .= '<option value=""></option>';
    foreach ($list_vendor as $ven) {
        if ($expense != '' && $expense->vendor == $ven['userid']) {
            $select = 'selected';
        } else {
            $select = '';
        }
        $option .= '<option value="' . $ven['userid'] . '" ' . $select . '>' . $ven['company'] . '</option>';
    }
    $option .= '</select>';
    $option .= '</div>';
    $option .= '</div>';
    $option .= '<br>';
    echo html_entity_decode($option);
}
/**
 * Initializes the vendor customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function init_vendor_customfield($custom_field = '')
{
    $select = '';
    if ($custom_field != '') {
        if ($custom_field->fieldto == 'vendors') {
            $select = 'selected';
        }
    }
    $html = '<option value="vendors" ' . $select . ' >' . _l('vendors') . '</option>';
    echo html_entity_decode($html);
}
/**
 * Initializes the purchase order customfield.
 *
 * @param      string  $custom_field  The custom field
 */
function init_po_customfield($custom_field = '')
{
    $select = '';
    if ($custom_field != '') {
        if ($custom_field->fieldto == 'pur_order') {
            $select = 'selected';
        }
    }
    $html = '<option value="pur_order" ' . $select . ' >' . _l('pur_order') . '</option>';
    echo html_entity_decode($html);
}
/**
 * Initializes the tab purchase order in client.
 *
 *
 */
function init_tab_pur_order()
{
    echo '<li role="presentation">
                  <a href="#pur_order" aria-controls="pur_order" role="tab" data-toggle="tab">
                  ' . _l('pur_order') . '
                  </a>
               </li>';
}
/**
 * Initializes the tab content purchase order.
 *
 *
 */
function init_content_pur_order($client)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($client) {
        echo '<div role="tabpanel" class="tab-pane" id="pur_order">';
        require "modules/purchase/views/client_pur_order.php";
        echo '</div>';
    }
}
/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function po_related_to_select($value)
{
    $selected = '';
    if ($value == 'pur_order') {
        $selected = 'selected';
    }
    echo "<option value='pur_order' " . $selected . ">" .
        _l('pur_order') . "
                           </option>";
}
/**
 * PO relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function po_relation_values($values, $relation)
{
    if ($values['type'] == 'pur_order') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = $relation['pur_order_number'];
        } else {
            $values['id'] = $relation->id;
            $values['name'] = $relation->pur_order_number;
        }
        $values['link'] = admin_url('purchase/purchase_order/' . $values['id']);
    }
    return $values;
}
/**
 * PO relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function po_relation_data($data, $type, $rel_id, $q)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($type == 'pur_order') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_pur_order($rel_id);
        } else {
            $data = [];
        }
    }
    return $data;
}
/**
 * PO add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function po_add_table_row($row, $aRow)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($aRow['rel_type'] == 'pur_order') {
        $po = $CI->purchase_model->get_pur_order($aRow['rel_id']);
        if ($po) {
            $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/purchase_order/' . $po->id) . '">' . $po->pur_order_number . '</a><br />';
            $row[2] = $row[2] . $str;
        }
    }
    return $row;
}
/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function pq_related_to_select($value)
{
    $selected = '';
    if ($value == 'pur_quotation') {
        $selected = 'selected';
    }
    echo "<option value='pur_quotation' " . $selected . ">" .
        _l('purchase_quotation') . "
                           </option>";
}
/**
 * pq relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function pq_relation_values($values, $relation)
{
    if ($values['type'] == 'pur_quotation') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = format_pur_estimate_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = format_pur_estimate_number($relation->id);
        }
        $values['link'] = admin_url('purchase/quotations/' . $values['id']);
    }
    return $values;
}
/**
 * pq relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function pq_relation_data($data, $type, $rel_id, $q)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($type == 'pur_quotation') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_estimate($rel_id);
        } else {
            $data = [];
        }
    }
    return $data;
}
/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function pq_add_table_row($row, $aRow)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($aRow['rel_type'] == 'pur_quotation') {
        $pq = $CI->purchase_model->get_estimate($aRow['rel_id']);
        if ($pq) {
            $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/quotations/' . $pq->id) . '">' . format_pur_estimate_number($pq->id) . '</a><br />';
            $row[2] = $row[2] . $str;
        }
    }
    return $row;
}
/**
 * reset purchase order number
 *  
 */
function reset_pur_order_number($manually)
{
    $CI = &get_instance();
    if (date('d') == 1) {
        if (date('Y-m-d') != get_purchase_option('date_reset_number')) {
            $CI->db->where('option_name', 'next_po_number');
            $CI->db->update(db_prefix() . 'purchase_option', ['option_val' => '1']);
            if ($CI->db->affected_rows() > 0) {
                $CI->db->where('option_name', 'date_reset_number');
                $CI->db->update(db_prefix() . 'purchase_option', ['option_val' => date('Y-m-d')]);
            }
        }
    }
}
/**
 * reset purchase order number
 *  
 */
function reset_pur_request_number($manually)
{
    $CI = &get_instance();
    if (date('m-d') == '01-01') {
        if (date('Y-m-d') != get_purchase_option('date_reset_pr_number')) {
            $CI->db->where('option_name', 'next_pr_number');
            $CI->db->update(db_prefix() . 'purchase_option', ['option_val' => '1']);
            if ($CI->db->affected_rows() > 0) {
                $CI->db->where('option_name', 'date_reset_pr_number');
                $CI->db->update(db_prefix() . 'purchase_option', ['option_val' => date('Y-m-d')]);
            }
        }
    }
}
/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function pc_related_to_select($value)
{
    $selected = '';
    if ($value == 'pur_contract') {
        $selected = 'selected';
    }
    echo "<option value='pur_contract' " . $selected . ">" .
        _l('purchase_contract') . "
                           </option>";
}
/**
 * purchase contract relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function pc_relation_values($values, $relation)
{
    if ($values['type'] == 'pur_contract') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = get_pur_contract_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = get_pur_contract_number($relation->id);
        }
        $values['link'] = admin_url('purchase/contract/' . $values['id']);
    }
    return $values;
}
/**
 * purchase contract relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function pc_relation_data($data, $type, $rel_id, $q)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($type == 'pur_contract') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_contract($rel_id);
        } else {
            $data = [];
        }
    }
    return $data;
}
/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function pc_add_table_row($row, $aRow)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($aRow['rel_type'] == 'pur_contract') {
        $pc = $CI->purchase_model->get_contract($aRow['rel_id']);
        if ($pc) {
            $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/contract/' . $pc->id) . '">' . get_pur_contract_number($pc->id) . '</a><br />';
            $row[2] = $row[2] . $str;
        }
    }
    return $row;
}
/**
 * task related to select
 * @param  string $value 
 * @return string        
 */
function pi_related_to_select($value)
{
    $selected = '';
    if ($value == 'pur_invoice') {
        $selected = 'selected';
    }
    echo "<option value='pur_invoice' " . $selected . ">" .
        _l('pur_invoice') . "
                           </option>";
}
/**
 * purchase contract relation values
 * @param  [type] $values   
 * @param  [type] $relation 
 * @return [type]           
 */
function pi_relation_values($values, $relation)
{
    if ($values['type'] == 'pur_invoice') {
        if (is_array($relation)) {
            $values['id'] = $relation['id'];
            $values['name'] = get_pur_invoice_number($relation['id']);
        } else {
            $values['id'] = $relation->id;
            $values['name'] = get_pur_invoice_number($relation->id);
        }
        $values['link'] = admin_url('purchase/purchase_invoice/' . $values['id']);
    }
    return $values;
}
/**
 * purchase contract relation data
 * @param  array $data   
 * @param  string $type   
 * @param  id $rel_id 
 * @param  array $q      
 * @return array         
 */
function pi_relation_data($data, $type, $rel_id, $q)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($type == 'pur_invoice') {
        if ($rel_id != '') {
            $data = $CI->purchase_model->get_pur_invoice($rel_id);
        } else {
            $data = [];
        }
    }
    return $data;
}
/**
 * pq add table row
 * @param  string $row  
 * @param  string $aRow 
 * @return [type]       
 */
function pi_add_table_row($row, $aRow)
{
    $CI = &get_instance();
    $CI->load->model('purchase/purchase_model');
    if ($aRow['rel_type'] == 'pur_invoice') {
        $pc = $CI->purchase_model->get_pur_invoice($aRow['rel_id']);
        if ($pc) {
            $str = '<span class="hide"> - </span><a class="text-muted task-table-related" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . admin_url('purchase/purchase_invoice/' . $pc->id) . '">' . get_pur_invoice_number($pc->id) . '</a><br />';
            $row[2] = $row[2] . $str;
        }
    }
    return $row;
}
/**
 * Register other merge fields for purchase
 *
 * @param [array] $for
 * @return void
 */
function purchase_register_other_merge_fields($for)
{
    $for[] = 'purchase_order';
    return $for;
}
