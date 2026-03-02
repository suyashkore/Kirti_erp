<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @since  2.3.3
 * Get available staff permissions, modules can use the filter too to hook permissions
 * @param  array  $data additional data passed from view role.php and member.php
 * @return array
 */
 function get_available_staff_permissions($data = [])
{
    $viewGlobalName = _l('permission_view') . '(' . _l('permission_global') . ')';

    $allPermissionsArray = [
        'view'     => $viewGlobalName,
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => _l('permission_delete'),
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];

    
    $oneoptionPermissionsArray = [
        'view'   => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'update' => "update",
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $v_p_ex = [
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    $view_edit_delete = [
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $onlycreate = [
        'view'     => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $createview = [
        'view'     => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    $view_edit = [
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => _l('permission_edit'),
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    
    $view_create = [
        'view'     => $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    
    $v_c_e_p_ex = [
        'view'     =>  $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => false, 'name' => 'Print'],
        'export' => ['not_applicable' => false, 'name' => 'Export'],
    ];
    $v_c = [
        'view'     =>  $viewGlobalName,
        'create' => _l('permission_create'),
        'edit' => ['not_applicable' => true, 'name' => 'Edit'],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')],
        'print' => ['not_applicable' => true, 'name' => 'Print'],
        'export' => ['not_applicable' => true, 'name' => 'Export'],
    ];
    
    
    
    $withNotApplicableViewOwn = array_merge(['view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')]], $allPermissionsArray);

    $corePermissions = [
        /*'bulk_pdf_exporter' => [
            'name'         => _l('bulk_pdf_exporter'),
            'capabilities' => [
                'view' => $viewGlobalName,
            ],
        ],*/
        /*'contracts' => [
            'name'         => _l('contracts'),
            'capabilities' => $allPermissionsArray,
        ],*/
        
        'customers' => [
            'name'         => _l('clients'),
            'main_menu'    => 'Masters',
            'capabilities' => $allPermissionsArray,
            // 'help'         => [
                // 'view_own' => _l('permission_customers_based_on_admins'),
            // ],
        ],
        'ratemaster' => [
            'name'         => "Rate Master",
            'main_menu'    => 'Masters',
            'capabilities' => $allPermissionsArray,
        ],
        'routemaster' => [
            'name'         => "Route Master",
            'main_menu'    => 'Masters',
            'capabilities' => $allPermissionsArray,
        ],
        'PointMaster' => [
            'name'         => "Point Master",
            'main_menu'    => 'Masters',
            'capabilities' => $allPermissionsArray,
        ],
        'StationMaster' => [
            'name'         => "Station Master",
            'main_menu'    => 'Masters',
            'capabilities' => $allPermissionsArray,
        ],
        'tcsmaster' => [
            'name'         => "TCS Master",
            'main_menu'    => 'Masters',
            'capabilities' => $allPermissionsArray,
        ],
        'tdsmaster' => [
            'name'         => "TDS Master",
            'main_menu'    => 'Masters',
            'capabilities' => $allPermissionsArray,
            // 'capabilities' => $v_a_e_d,
        ],
        
        'hierarchy' => [
            'name'         => "Hierarchy",
            'main_menu'    => 'Masters',
            'capabilities' => $oneoptionPermissionsArray,
        ],
        'salesperassign' => [
            'name'         => "Attach SalesTeam to Parties",
            'main_menu'    => 'Masters',
            'capabilities' => $oneoptionPermissionsArray,
        ],
        // 'tour' => [
            // 'name'         => "Tour Plan",
            // 'main_menu'    => 'Masters',
            // 'capabilities' => $allPermissionsArray,
        // ],
        'year_transfer' => [
            'name'         => "Year Transfer",
            'main_menu'    => 'Masters',
            'capabilities' => $oneoptionPermissionsArray,
        ],
        'InventoryDashboard' => [
            'name'         => "Dashboard",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_p_ex,
        ],
        'hsnmaster' => [
            'name'         => "HSN Master",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'items' => [
            'name'         => "Item Master",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'itemsdivision' => [
            'name'         => "Item Division",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'itemsmaingrp' => [
            'name'         => "ItemMain Group",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'itemssubgrp' => [
            'name'         => "Item SubGroup 1",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'itemssubgrp2' => [
            'name'         => "Item SubGroup 2",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'ItemList' => [
            'name'         => "ItemList",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_p_ex,
        ],
        'ArticleMaster' => [
            'name'         => "Article Master",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_c_e_p_ex, 
        ],
        'GodownMaster' => [
            'name'         => "Godown Master",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'stock_position' => [
            'name'         => "Case/Crate Wise Stock Position",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_p_ex,
        ],
        'unit_stock_position' => [
            'name'         => "Unit Wise Stock Position",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_p_ex,
        ],
        'stockCummulative' => [
            'name'         => "stock Cummulative",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_p_ex,
        ],
        // 'PartyWiseRateReport' => [
            // 'name'         => "Party Wise Rate Report",
            // 'main_menu'    => 'Inventory',
            // 'capabilities' => $v_p_ex,
        // ],
        'StockTransfer' => [
            'name'         => "Stock Transfer",
            'main_menu'    => 'Inventory',
            'capabilities' => $allPermissionsArray,
        ],
        'NeededQtyTransfer' => [
            'name'         => "Production Wise Stock Transfer",
            'main_menu'    => 'Inventory',
            'capabilities' => $view_edit,
        ],
        'stock_adjustment' => [
            'name'         => "Stock Adjustment",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_c_e_p_ex,
        ],
        'damage_entry' => [
            'name'         => "Damage Entry",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_c_e_p_ex,
        ],
        'PhysicalStockEntry' => [
            'name'         => "Physical Stock Entry",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_c_e_p_ex,
        ],
        'PhysicalStockEntryReport' => [
            'name'         => "Physical Stock Report",
            'main_menu'    => 'Inventory',
            'capabilities' => $v_p_ex,
        ],
        'TransportDashboard' => [
            'name'         => "Transport Dashboard",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'vehiclemaster' => [
            'name'         => "Vehicle Master",
            'main_menu'    => 'Transport',
            'capabilities' => $allPermissionsArray,
        ],
        'PendingVehicleReturnList' => [
            'name'         => "Vehicle Return Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'VehicleReturnEntry' => [
            'name'         => "Vehicle Return Entry",
            'main_menu'    => 'Transport',
            'capabilities' => $allPermissionsArray,
        ],
        'Vehicle_Crate' => [
            'name'         => "Vehicle Return Crates",
            'main_menu'    => 'Transport',
            'capabilities' => $allPermissionsArray,
        ],
        'Vehicle_Payment' => [
            'name'         => "Vehicle Return Payment",
            'main_menu'    => 'Transport',
            'capabilities' => $allPermissionsArray,
        ],
        'Vehicle_Expense' => [
            'name'         => "Vehicle Return Expense",
            'main_menu'    => 'Transport',
            'capabilities' => $allPermissionsArray,
        ],
        // 'Only_Vehicle_Rtn' => [
            // 'name'         => "Only Vehicle Return",
            // 'main_menu'    => 'Transport',
            // 'capabilities' => $allPermissionsArray,
        // ],
        // 'TransportEntryList' => [
            // 'name'         => "Transport Entry List",
            // 'main_menu'    => 'Transport',
            // 'capabilities' => $v_p_ex,
        // ],
        'TransportEntry' => [
            'name'         => "Transport Entry",
            'main_menu'    => 'Transport',
            'capabilities' => $allPermissionsArray,
        ],
        'MileageReport' => [
            'name'         => "Mileage Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'FinalVehicleReport' => [
            'name'         => "Final Vehicle Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'DamageCurrencyReport' => [
            'name'         => "Damage Currency Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'VehicleLoadedCapacityReport' => [
            'name'         => "Vehicle Loaded Capacity Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'PremisesReport' => [
            'name'         => "Vehicle In Premises Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'DriverRestRecord' => [
            'name'         => "Driver Rest Entry",
            'main_menu'    => 'Transport',
            'capabilities' => $v_c_e_p_ex,
        ],
        'RestRecordReport' => [
            'name'         => "Driver Rest Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'VehicleMaintenanceReport' => [
            'name'         => "Vehicle Maintenance Report",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'DelayDelivery' => [
            'name'         => "Delay Deliveries",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'ShortageEntry' => [
            'name'         => "Shortage Entry",
            'main_menu'    => 'Transport',
            'capabilities' => $v_c_e_p_ex,
        ],
        'ShortageList' => [
            'name'         => "Shortage List",
            'main_menu'    => 'Transport',
            'capabilities' => $v_p_ex,
        ],
        'orders' => [
            'name'         => "Order",
            'main_menu'    => 'Transactions',
            'capabilities' => $v_c_e_p_ex,
        ],
        'sale_list' => [
            'name'         => "Sale List",
            'main_menu'    => 'Transactions',
            'capabilities' => $v_p_ex,
        ],
        'pending_orders' => [
            'name'         => "Pending Orders",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit,
        ],
        'OrderCancel' => [
            'name'         => "Order Cancel",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit,
        ],
        'LimitExceededOrders' => [
            'name'         => "Limit Exceeded Orders",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit,
        ],
        'challan' => [
            'name'         => "Challan",
            'main_menu'    => 'Transactions',
            'capabilities' => $onlycreate,
        ],
        'challan_list' => [
            'name'         => "Challan List",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit_delete,
        ],
        'crate_update' => [
            'name'         => "Crate Update",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit,
        ],
        'change_vehicle' => [
            'name'         => "Change Vehicle",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit,
        ],
        'gatepass' => [
            'name'         => "Gatepass",
            'main_menu'    => 'Transactions',
            'capabilities' => $createview,
        ],
        // 'vehicle_return' => [
            // 'name'         => "Vehicle Return",
            // 'main_menu'    => 'Transactions',
            // 'capabilities' => $allPermissionsArray,
        // ],
        // 'cd_notes' => [
            // 'name'         => "Credit/Debit Notes",
            // 'main_menu'    => 'Transactions',
            // 'capabilities' => $allPermissionsArray,
        // ],
        
        'sale_return' => [
            'name'         => "Sale Return",
            'main_menu'    => 'Transactions',
            'capabilities' => $allPermissionsArray,
        ],
        'staff_target' => [
            'name'         => "Staff Target",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit,
        ],
        'GroupWise_target_sale' => [
            'name'         => "Sale Target",
            'main_menu'    => 'Transactions',
            'capabilities' => $view_edit,
        ],
        'einvoice' => [
            'name'         => "E-invoice",
            'main_menu'    => 'Transactions',
            'capabilities' => $createview,
        ],
        
        'SplDisc' => [
            'name'         => "Special Discount",
            'main_menu'    => 'Transactions',
            'capabilities' => $v_c_e_p_ex,
        ],
        // 'SchemeMaster' => [
            // 'name'         => "Scheme Master",
            // 'main_menu'    => 'Transactions',
            // 'capabilities' => $v_c_e_p_ex,
        // ],
        'ProductionDashboard' => [
            'name'         => "Dashboard",
            'main_menu'    => 'Production',
            'capabilities' => $v_p_ex,
        ],
        'recipe' => [
            'name'         => "Recipe",
            'main_menu'    => 'Production',
            'capabilities' => $v_c_e_p_ex,
        ],
        'production' => [
            'name'         => "Production",
            'main_menu'    => 'Production',
            'capabilities' => $v_c_e_p_ex,
        ],
        'production_list' => [
            'name'         => "Production List",
            'main_menu'    => 'Production',
            'capabilities' => $v_p_ex,
        ],
        'AddEditBakingQty' => [
            'name'         => "Production Baking",
            'main_menu'    => 'Production',
            'capabilities' => $view_edit,
        ],
        'AddEditPackingQty' => [
            'name'         => "Production Packing",
            'main_menu'    => 'Production',
            'capabilities' => $view_edit,
        ],
        'production_reports' => [
            'name'         => "Production Report",
            'main_menu'    => 'Production',
            'capabilities' => $v_p_ex,
        ],
        'production_order_report' => [
            'name'         => "Production Order Report",
            'main_menu'    => 'Production',
            'capabilities' => $v_p_ex,
        ],
        'cost_report' => [
            'name'         => "Cost Report",
            'main_menu'    => 'Production',
            'capabilities' => $v_p_ex,
        ],
        'ItemUsedInRecipeReport' => [
            'name'         => "Item Used In Recipe Report",
            'main_menu'    => 'Production',
            'capabilities' => $v_p_ex,
        ],
		'qc_unit' => [
            'name'         => "QC Unit",
            'main_menu'    => 'QC',
            'capabilities' => $v_c_e_p_ex,
        ],
        'qc_parameter' => [
            'name'         => "QC Parameter",
            'main_menu'    => 'QC',
            'capabilities' => $v_c_e_p_ex,
        ],
        'qc_master' => [
            'name'         => "QC Master",
            'main_menu'    => 'QC',
            'capabilities' => $v_c_e_p_ex,
        ],
        'QcStatusList' => [
            'name'         => "QC Status List",
            'main_menu'    => 'QC',
            'capabilities' => $v_p_ex,
        ],
        'PurchaseQCAddEdit' => [
            'name'         => "Purchase QC Add Edit",
            'main_menu'    => 'QC',
            'capabilities' => $v_c,
        ],
        'PurchaseQCApproveReject' => [
            'name'         => "Purchase QC Approve/Reject",
            'main_menu'    => 'QC',
            'capabilities' => $v_c,
        ],
		'Item_QC' => [
            'name'         => "Item Wise QC",
            'main_menu'    => 'QC',
            'capabilities' => $v_c_e_p_ex,
        ],
        'Fg_Test' => [
            'name'         => "Finish Good Test",
            'main_menu'    => 'QC',
            'capabilities' => $v_c_e_p_ex,
        ],
        // 'In_Process_QC' => [
            // 'name'         => "In Process Plant QC",
            // 'main_menu'    => 'QC',
            // 'capabilities' => $v_c_e_p_ex,
        // ],
        'Metal_Detector' => [
            'name'         => "Metal Detector QC",
            'main_menu'    => 'QC',
            'capabilities' => $v_c_e_p_ex,
        ],
        'SalesDashboard' => [
            'name'         => "Sales Dashboard",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'daily_sale' => [
            'name'         => "Daily Sales Report",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'cummulatives_sale' => [
            'name'         => "Cummulatives Sales Report",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'cummulatives_sale_shipto' => [
            'name'         => "Ship To Party PackWise Cummulatives Sales",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        // 'target_vs_achivements' => [
            // 'name'         => "Target Vs Achievement",
            // 'main_menu'    => 'Sales Reports',
            // 'capabilities' => $v_p_ex,
        // ],
        'SaleRtn' => [
            'name'         => "Sales Return - Report",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'saleVsSaleRtn' => [
            'name'         => "Sales Vs SalesReturn",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'TargetVsAchievement' => [
            'name'         => "Target Vs Achievement",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'PartyItemWiseReport' => [
            'name'         => "Party ItemWise Report",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'OrderVsDispatch' => [
            'name'         => "Order Vs Dispatch",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'OrderVsDispatchItemWise' => [
            'name'         => "OrderVsDispatch ItemWise",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'TradeReceivableReport' => [
            'name'         => "Trade Receivable Report",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'daily_sale_summary' => [
            'name'         => "Daily Sales Summary",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'daily_ItemWise_sale_summary_report' => [
            'name'         => "Daily Item Wise Sales Summary",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
		'SaleItemFlowReport' => [
            'name'         => "Sale Item Flow Report",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
		'CustomerPerformanceReport' => [
            'name'         => "Customer Performance Report",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
		'item_rate_list' => [
            'name'         => "Item Rate List",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'ItemWiseRateList' => [
            'name'         => "Item Wise Rate List",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        'ItemWiseDistWiseRateList' => [
            'name'         => "Item Wise Distributor Wise Rate List",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
		'GroupWiseItemSale' => [
            'name'         => "Group Wise Item Sale",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
		// 'ItemDivisionWiseSale' => [
            // 'name'         => "Division Wise Sale Summary",
            // 'main_menu'    => 'Sales Reports',
            // 'capabilities' => $v_p_ex,
        // ],
		'ItemGroupWiseSale' => [
            'name'         => "Group Wise Sale Summary",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
		'FoodLicenseNumberStatus' => [
            'name'         => "Food License Number Status",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
		
		'rp_general_ledger_shipto' => [
            'name'         => "Ship To Account Ledger",
            'main_menu'    => 'Sales Reports',
            'capabilities' => $v_p_ex,
        ],
        // 'purchase_register' => [
            // 'name'         => "Purchase Register",
            // 'main_menu'    => 'Pur. Reports',
            // 'capabilities' => $v_p_ex,
        // ],
        'account_list' => [
            'name'         => "Account List",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'market_outstanding' => [
            'name'         => "Market Outstanding",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'crate_ledger' => [
            'name'         => "Party Wise Crate Ledger",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'all_crate_ledger' => [
            'name'         => "Crate Ledger",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'Crates_received_via_vehicle_return' => [
            'name'         => "Crates rec. via Vehicle rtn.",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'routes_covered_during_a_period' => [
            'name'         => "Routes Covered during a period",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'vehicles_on_route_duty' => [
            'name'         => "Vehicles on Route Duty",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'vehicle_operation_during_a_month' => [
            'name'         => "Vehicle Operation during a month",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'contractor_production_report' => [
            'name'         => "Contractor Production Rep.",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'contractor_production_MTD' => [
            'name'         => "Contractor Production-MTD",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'ItemWiseStockReport' => [
            'name'         => "ItemWise Stock Report",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        
        'AcccountGroupList' => [
            'name'         => "AcccountGroupList",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'AcccountSubGroup1List' => [
            'name'         => "AcccountSubGroup1List",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'AcccountSubGroup2List' => [
            'name'         => "AcccountSubGroup2List",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'AccountHeadList' => [
            'name'         => "AccountHeadList",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'CustomerList' => [
            'name'         => "CustomerList",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'CustomerFeedback' => [
            'name'         => "Customer Feedback",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_c_e_p_ex,
        ],
        'CustomerFeedbackReport' => [
            'name'         => "Customer Feedback Report",
            'main_menu'    => 'Misc. Reports',
            'capabilities' => $v_p_ex,
        ],
        'GSTR_purchase' => [
            'name'         => "GSTR Purchase",
            'main_menu'    => 'E-Filling',
            'capabilities' => $v_p_ex,
        ],
        'GSTR_sales' => [
            'name'         => "GSTR Sales",
            'main_menu'    => 'E-Filling',
            'capabilities' => $v_p_ex,
        ],
        'EInvoiceReport' => [
            'name'         => "EInvoice Report",
            'main_menu'    => 'E-Filling',
            'capabilities' => $v_p_ex,
        ],
        'EWayBillReport' => [
            'name'         => "E-Way Bill Report",
            'main_menu'    => 'E-Filling',
            'capabilities' => $v_p_ex,
        ],
        'GGSTR_1' => [
            'name'         => "GGSTR-1",
            'main_menu'    => 'E-Filling',
            'capabilities' => $v_p_ex,
        ],
        'GSTR_3B' => [
            'name'         => "GSTR-3B",
            'main_menu'    => 'E-Filling',
            'capabilities' => $v_p_ex,
        ],
        'user_master' => [
            'name'         => "User Master",
            'main_menu'    => 'Admin',
            'capabilities' => $view_edit,
        ],
        'user_rights' => [
            'name'         => "User Rights",
            'main_menu'    => 'Admin',
            'capabilities' => $view_edit,
        ],
        'user_rights' => [
            'name'         => "Invoice Note",
            'main_menu'    => 'Admin',
            'capabilities' => $view_edit,
        ],
        
        'no_show' => [
            'name'         => "No Show Accounts",
            'main_menu'    => 'Admin',
            'capabilities' => $view_edit,
        ],
        'distributor_type' => [
            'name'         => "Distributor Type",
            'main_menu'    => 'Admin',
            'capabilities' => $allPermissionsArray,
        ],
        'AccountIDMerge' => [
            'name'         => "Merge AccountID",
            'main_menu'    => 'Admin',
            'capabilities' => $oneoptionPermissionsArray,
        ],
        'ItemIDMerge' => [
            'name'         => "Merge ItemID",
            'main_menu'    => 'Admin',
            'capabilities' => $oneoptionPermissionsArray,
        ],
        'roles' => [
            'name'         => "Staff Roles",
            'main_menu'    => 'Admin',
            'capabilities' => $allPermissionsArray,
        ],
        'hrm_salary_type' => [
            'name'         => "HR Records Salary Type",
            'main_menu'    => 'Admin',
            'capabilities' => $allPermissionsArray,
        ],
        'hrm_allowance_type' => [
            'name'         => "HR Records Allowance Type",
            'main_menu'    => 'Admin',
            'capabilities' => $allPermissionsArray,
        ],
        'hrm_hedquarter' => [
            'name'         => "HR Records Hedquarter",
            'main_menu'    => 'Admin',
            'capabilities' => $allPermissionsArray,
        ],
        'hrm_hedquarter' => [
            'name'         => "HR Records Hedquarter",
            'main_menu'    => 'Admin',
            'capabilities' => $view_create,
        ],
        'hrm_gen_setting' => [
            'name'         => "HR Records General Setting",
            'main_menu'    => 'Admin',
            'capabilities' => $view_edit,
        ],
        'enquiry' => [
            'name'         => "Enquiry",
            'main_menu'    => 'Admin',
            'capabilities' => $allPermissionsArray,
        ],
        
        /*'email_templates' => [
            'name'         => _l('email_templates'),
            'capabilities' => [
                'view' => $viewGlobalName,
                'edit' => _l('permission_edit'),
            ],
        ],
        'estimates' => [
            'name'         => _l('estimates'),
            'capabilities' => $allPermissionsArray,
        ],*/
        /*'expenses' => [
            'name'         => _l('expenses'),
            'capabilities' => $allPermissionsArray,
        ],*/
        
        /*'knowledge_base' => [
            'name'         => _l('knowledge_base'),
            'capabilities' => $allPermissionsArray,
        ],*/
        /*'payments' => [
            'name'         => _l('payments'),
            'capabilities' => $allPermissionsArray,
            'help'         => [
                'view_own' => _l('permission_payments_based_on_invoices'),
            ],
        ],*/
        /*'projects' => [
            'name'         => _l('projects'),
            'capabilities' => $allPermissionsArray,
            'help'         => [
                'view'     => _l('help_project_permissions'),
                'view_own' => _l('permission_projects_based_on_assignee'),
            ],
        ],
        'proposals' => [
            'name'         => _l('proposals'),
            'capabilities' => $allPermissionsArray,
        ],*/
        /*'reports' => [
            'name'         => _l('reports'),
            'capabilities' => [
                'view' => $viewGlobalName,
            ],
        ],*/
        //admin Menu
        /*'roles' => [
            'name'         => _l('roles'),
            'main_menu'    => 'Transactions',
            'capabilities' => $allPermissionsArray,
        ],*/
        
        /*'view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')],
        'view'     => $viewGlobalName,
        'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
        'edit' => ['not_applicable' => true, 'name' => _l('permission_edit')],
        'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')]*/
        
        // admin menu
        /*'settings' => [
            'name'         => _l('settings'),
            'main_menu'    => 'Transactions',
            'capabilities' => [
                'view_own' => ['not_applicable' => true, 'name' => _l('permission_view_own')],
                'view' => $viewGlobalName,
                'create' => ['not_applicable' => true, 'name' => _l('permission_create')],
                'edit' => _l('permission_edit'),
                'delete' => ['not_applicable' => true, 'name' => _l('permission_delete')]
            ],
        ],*/
        /*'staff' => [
            'name'         => _l('staff'),
            'capabilities' => $allPermissionsArray,
        ],*/
         'tasks' => [
            'name'         => 'Tickets',
            'main_menu'    => 'Admin',
            'capabilities' => $allPermissionsArray,
             'help'        => [
                'view'     => _l('help_tasks_permissions'),
                'view_own' => _l('permission_tasks_based_on_assignee'),
            ],
        ],
        'hr_dashboard' => [
            'name'         => "HR Dashboard",
            'main_menu'    => 'HR',
            'capabilities' => $v_p_ex,
        ],
        /*'subscriptions' => [
            'name'         => _l('subscriptions'),
            'capabilities' => $allPermissionsArray,
        ],
       
        'checklist_templates' => [
            'name'         => _l('checklist_templates'),
            'capabilities' => [
                'create' => _l('permission_create'),
                'delete' => _l('permission_delete'),
            ],
        ],*/
    ];

    /*$addLeadsPermission = true;
    if (isset($data['staff_id']) && $data['staff_id']) {
        $is_staff_member = is_staff_member($data['staff_id']);
        if (!$is_staff_member) {
            $addLeadsPermission = false;
        }
    }

    if ($addLeadsPermission) {
        $corePermissions['leads'] = [
            'name'         => _l('leads'),
            'capabilities' => [
                'view'   => $viewGlobalName,
                'delete' => _l('permission_delete'),
            ],
            'help' => [
                'view' => _l('help_leads_permission_view'),
            ],
        ];
    }*/

    return hooks()->apply_filters('staff_permissions', $corePermissions, $data);
}

function get_dist_name($id)
{
    $CI = &get_instance();
    $CI->db->select('company')->from(db_prefix() . 'clients')->where('userid', $id);

    return $CI->db->get()->row()->company;
}

/**
 * Get staff by ID or current logged in staff
 * @param  mixed $id staff id
 * @return mixed
 */
function get_staff($id = null)
{
    if (empty($id) && isset($GLOBALS['current_user'])) {
        return $GLOBALS['current_user'];
    }

    // Staff not logged in
    if (empty($id)) {
        return null;
    }

    if (!class_exists('staff_model', false)) {
        get_instance()->load->model('staff_model');
    }

    return get_instance()->staff_model->get($id);
}

/**
 * Return staff profile image url
 * @param  mixed $staff_id
 * @param  string $type
 * @return string
 */
function staff_profile_image_url($staff_id, $type = 'small')
{
    $url = base_url('assets/images/user-placeholder.jpg');

    if ((string) $staff_id === (string) get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $staff = $GLOBALS['current_user'];
    } else {
        $CI = & get_instance();
        $CI->db->select('profile_image')
        ->where('staffid', $staff_id);

        $staff = $CI->db->get(db_prefix() . 'staff')->row();
    }

    if ($staff) {
        if (!empty($staff->profile_image)) {
            $profileImagePath = 'uploads/staff_profile_images/' . $staff_id . '/' . $type . '_' . $staff->profile_image;
            if (file_exists($profileImagePath)) {
                $url = base_url($profileImagePath);
            }
        }
    }

    return $url;
}

/**
 * Staff profile image with href
 * @param  boolean $id        staff id
 * @param  array   $classes   image classes
 * @param  string  $type
 * @param  array   $img_attrs additional <img /> attributes
 * @return string
 */
function staff_profile_image($id, $classes = ['staff-profile-image'], $type = 'small', $img_attrs = [])
{
    $url = base_url('assets/images/user-placeholder.jpg');

    $id = trim($id);

    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . e($val) . '" ';
    }

    $blankImageFormatted = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';

    if ((string) $id === (string) get_staff_user_id() && isset($GLOBALS['current_user'])) {
        $result = $GLOBALS['current_user'];
    } else {
        $CI     = & get_instance();
        $result = $CI->app_object_cache->get('staff-profile-image-data-' . $id);

        if (!$result) {
            $CI->db->select('profile_image,firstname,lastname');
            $CI->db->where('staffid', $id);
            $result = $CI->db->get(db_prefix() . 'staff')->row();
            $CI->app_object_cache->add('staff-profile-image-data-' . $id, $result);
        }
    }

    if (!$result) {
        return $blankImageFormatted;
    }

    if ($result && $result->profile_image !== null) {
        $profileImagePath = 'uploads/staff_profile_images/' . $id . '/' . $type . '_' . $result->profile_image;
        if (file_exists($profileImagePath)) {
            $profile_image = '<img ' . $_attributes . ' src="' . base_url($profileImagePath) . '" class="' . implode(' ', $classes) . '" />';
        } else {
            return $blankImageFormatted;
        }
    } else {
        $profile_image = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';
    }

    return $profile_image;
}

/**
 * Get staff full name
 * @param  string $userid Optional
 * @return string Firstname and Lastname
 */
function get_staff_full_name($userid = '')
{
    $tmpStaffUserId = get_staff_user_id();
    if ($userid == '' || $userid == $tmpStaffUserId) {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->firstname . ' ' . $GLOBALS['current_user']->lastname;
        }
        $userid = $tmpStaffUserId;
    }

    $CI = & get_instance();

    $staff = $CI->app_object_cache->get('staff-full-name-data-' . $userid);

    if (!$staff) {
        $CI->db->where('staffid', $userid);
        $staff = $CI->db->select('firstname,lastname')->from(db_prefix() . 'staff')->get()->row();
        $CI->app_object_cache->add('staff-full-name-data-' . $userid, $staff);
    }

    return $staff ? $staff->firstname . ' ' . $staff->lastname : '';
}

/**
 * Get staff default language
 * @param  mixed $staffid
 * @return mixed
 */
function get_staff_default_language($staffid = '')
{
    if (!is_numeric($staffid)) {
        // checking for current user if is admin
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->default_language;
        }

        $staffid = get_staff_user_id();
    }
    $CI = & get_instance();
    $CI->db->select('default_language');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('staffid', $staffid);
    $staff = $CI->db->get()->row();
    if ($staff) {
        return $staff->default_language;
    }

    return '';
}

function get_staff_recent_search_history($staff_id = null)
{
    $recentSearches = get_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches');

    if ($recentSearches == '') {
        $recentSearches = [];
    } else {
        $recentSearches = json_decode($recentSearches);
    }

    return $recentSearches;
}

function update_staff_recent_search_history($history, $staff_id = null)
{
    $totalRecentSearches = hooks()->apply_filters('total_recent_searches', 5);
    $history             = array_reverse($history);
    $history             = array_unique($history);
    $history             = array_splice($history, 0, $totalRecentSearches);

    update_staff_meta($staff_id ? $staff_id : get_staff_user_id(), 'recent_searches', json_encode($history));

    return $history;
}


/**
 * Check if user is staff member
 * In the staff profile there is option to check IS NOT STAFF MEMBER eq like contractor
 * Some features are disabled when user is not staff member
 * @param  string  $staff_id staff id
 * @return boolean
 */
function is_staff_member($staff_id = '')
{
    $CI = & get_instance();
    if ($staff_id == '') {
        if (isset($GLOBALS['current_user'])) {
            return $GLOBALS['current_user']->is_not_staff === '0';
        }
        $staff_id = get_staff_user_id();
    }

    $CI->db->where('staffid', $staff_id)
    ->where('is_not_staff', 0);

    return $CI->db->count_all_results(db_prefix() . 'staff') > 0 ? true : false;
}

/* Custom functions start */
function get_staff_permission($id)
{
    if (empty($id) && isset($GLOBALS['current_user'])) {
        return $GLOBALS['current_user'];
    }
    // Staff not logged in
    if (empty($id)) {
        return null;
    }

    $CI = & get_instance();
    $CI->db->select('*');
    $CI->db->from(db_prefix() . 'staff');
    $CI->db->where('staffid', $id);
    $staff_details =  $CI->db->get()->row();
    
    $CI->db->select(db_prefix() . 'setup.FIRMNAME,'.db_prefix() . 'setup.YEARFROM,'.db_prefix() . 'setup.YEARTO,'.db_prefix() . 'setup.PlantID');
    $CI->db->from(db_prefix() . 'setup');
    $CI->db->where(db_prefix() . 'setup.Status', 'Y');
    $CI->db->order_by(db_prefix() . 'setup.FY,'.db_prefix() . 'setup.PlantID',"desc");
    if($staff_details->admin == "1"){
        
    }else{
        $CI->db->join(db_prefix() . 'staff_permissions', '' . db_prefix() . 'staff_permissions.plant_id = ' . db_prefix() . 'setup.PlantID AND '. db_prefix() . 'staff_permissions.year = ' . db_prefix() . 'setup.FY');
        $CI->db->where(db_prefix() . 'staff_permissions.staff_id', $id);
        $CI->db->group_by(db_prefix() . 'staff_permissions.year,'.db_prefix() . 'staff_permissions.plant_id');
        
    }
    return $CI->db->get()->result_array();
}

/**
 * Get Root Company name
 */
function get_root_company_name($compid = '')
{
    
    $CI = & get_instance();
    $selected_year = $CI->session->userdata("finacial_year");
    $CI->db->select('FIRMNAME,FY');
    $CI->db->from(db_prefix() . 'setup');
    $CI->db->where('PlantID', $compid);
    $CI->db->where('FY', $selected_year);
    $company_data = $CI->db->get()->row();
    if ($company_data) {
        return $company_data->FIRMNAME."(".$company_data->FY.")";
    }
}

function get_days_new($feature_name = '')
{
    $CI = & get_instance();
    $selected_year = $CI->session->userdata("finacial_year");
    $selected_company = $CI->session->userdata("root_company");
    $curr_user = $GLOBALS['current_user'];
    $CI->db->select('days');
    //$CI->db->from(db_prefix() . 'staff_permissions');
    $CI->db->LIKE('feature', $feature_name);
    $CI->db->where('staff_id', $curr_user);
    $CI->db->where('plant_id', $selected_company);
    $CI->db->where('year', $selected_year);
    return $CI->db->get(db_prefix() . 'staff_permissions')->row();
}

function get_route_name($routeid = '',$selected_company)
{
    $CI = & get_instance();
    $selected_company = $CI->session->userdata("root_company");
    $CI->db->where('PlantID', $selected_company);
    $CI->db->where('RouteID', $routeid);
    $route_data = $CI->db->get(db_prefix() . 'route')->row();
    if ($route_data) {
        return $route_data->name;
    }
}

function get_account_details($ChallanID = '',$selected_company)
{
    $CI = & get_instance();
    $CI->db->select('AccountID');
    $CI->db->from(db_prefix() . 'ordermaster');
    $CI->db->where('ChallanID', $ChallanID);
    $CI->db->where('PlantID', $selected_company);
    $acc_data = $CI->db->get()->result_array();
    return $acc_data;
}

function get_travel_detail_by_staff_id($staff_id = '',$date)
{
    $CI = & get_instance();
    $CI->db->select('*');
    $CI->db->from(db_prefix() . 'travel_report');
    $CI->db->where('staff_id', $staff_id);
    $CI->db->where('date', $date);
    $travel_data = $CI->db->get()->result_array();
    return $travel_data;
}

function get_party_detail($AccountID = '',$selected_company)
{
    $CI = & get_instance();
    $CI->db->select('company,state,StationName,city');
    $CI->db->from(db_prefix() . 'clients');
    $CI->db->where('AccountID', $AccountID);
    $CI->db->where('PlantID', $selected_company);
    $acc_details = $CI->db->get()->row();
    return $acc_details;
    /*if ($route_data) {
        return $route_data->name;
    }*/
}

/**
 * Get Root Company name
 
 */
function get_all_root_company($compid = '')
{
    $CI = & get_instance();
    $CI->db->select('company_name,id');
    $CI->db->from(db_prefix() . 'rootcompany');
    //$CI->db->where('id', $compid);
    return $CI->db->get()->result_array();
    /*if ($company_data) {
        return $company_data;
    }*/
}
