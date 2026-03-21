<?php

defined('BASEPATH') or exit('No direct script access allowed');

function app_init_admin_sidebar_menu_items()
{
	$CI = &get_instance();

	/* $CI->app_menu->add_sidebar_menu_item('dashboard', [
		'name'     => _l('als_dashboard'),
		'href'     => admin_url(),
		'position' => 1,
		'icon'     => 'fa fa-home',
	]);*/


	$CI->app_menu->add_sidebar_menu_item('master', [
		'collapse' => true,
		'name' => "Masters",
		'position' => 1,
		'icon' => 'fa fa-balance-scale',
	]);


	if (has_permission_new('company_master', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'company_master',
			'name' => 'Company Master',
			'href' => admin_url('company_master'),
			'position' => 1,
		]);
	}

	if (has_permission_new('customers', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'customers',
			'name' => 'Customer',
			'href' => admin_url('clients/AddEditAccount'),
			'position' => 2,
		]);
	}

	/*if (has_permission_new('customers', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
		'slug'     => 'account-master',
		'name'     => 'Customer',
		'href'     => admin_url('clients'),
		'position' => 1,
		]);
	}*/

	// if (has_permission_new('ratemaster', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('master', [
	// 	'slug'     => 'rate_master',
	// 	'name'     => 'Rate Master',
	// 	'href'     => admin_url('rate_master/import'),
	// 	'position' => 3,
	// 	]);
	// }

	// Broker Master menu item
	if (has_permission_new('broker_master', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'broker_master',
			'name' => 'Broker Master',
			'href' => admin_url('broker/broker_form'),
			'position' => 4,
		]);
	}

	// Freight Terms Master menu item
	if (has_permission_new('FreightTerm', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'FreightTerm',
			'name' => 'Freight Terms Master',
			'href' => admin_url('FreightTerm'),
			'position' => 5,
		]);
	}

	// Location Master menu item
	if (has_permission_new('location_form', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'location_form',
			'name' => 'Location Master',
			'href' => admin_url('location_master'),
			'position' => 6,
		]);
	}

	if (has_permission_new('payment_terms', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'payment_terms',
			'name' => 'Payment Terms',
			'href' => admin_url('payment_terms'),
			'position' => 7,
		]);
	}

	if (has_permission_new('Territory', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'Territory',
			'name' => 'Territory Master',
			'href' => admin_url('Territory'),
			'position' => 8,
		]);
	}
	// if (has_permission_new('CurrencyMaster', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('master', [
	// 	'slug'     => 'CurrencyMaster',
	// 	'name'     => 'Currency Master',
	// 	'href'     => admin_url('CurrencyMaster'),
	// 	'position' => 9,
	// 	]);
	// }
	if (has_permission_new('currencies', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'currencies',
			'name' => 'Currencies Master',
			'href' => admin_url('currencies'),
			'position' => 9,
		]);
	}
	/*if (has_permission_new('other_staff_master', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
		'slug'     => 'staff_master',
		'name'     => 'Other Staff Master',
		'href'     => admin_url('accounts_master/manage_staff'),
		'position' => 4,
		]);
	}*/

	if (has_permission_new('route_master', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'route_master',
			'name' => 'Route Master',
			'href' => admin_url('route_master'),
			'position' => 10,
		]);
	}

	if (has_permission_new('PointMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'PointMaster',
			'name' => 'Point Master',
			'href' => admin_url('route_master/PointMaster'),
			'position' => 11,
		]);
	}
	if (has_permission_new('StationMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'StationMaster',
			'name' => 'Station Master',
			'href' => admin_url('route_master/StationMaster'),
			'position' => 12,
		]);
	}



	// if (has_permission('tcsmaster', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('master', [
	// 	'slug'     => 'tcs_master',
	// 	'name'     => 'TCS Master',
	// 	'href'     => admin_url('tcs_master'),
	// 	'position' => 13,
	// 	]);
	// }
	if (has_permission('tdsMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'tdsMaster',
			'name' => 'TDS Master',
			'href' => admin_url('tdsMaster'),
			'position' => 14,
		]);
	}







	// if (has_permission_new('hierarchy', '', 'update')) {
	// $CI->app_menu->add_sidebar_children_item('master', [
	// 'slug'     => 'hierarchy',
	// 'name'     => 'Hierarchy',
	// 'href'     => admin_url('hierarchy'),
	// 'position' => 15,
	// ]);
	// }

	// if (has_permission_new('salesperassign', '', 'update')) {
	// $CI->app_menu->add_sidebar_children_item('master', [
	// 'slug'     => 'company_assign',
	// 'name'     => 'Attach SalesTeam to Parties',
	// 'href'     => admin_url('company_assign'),
	// 'position' => 16,
	// ]);
	// }


	// if (has_permission_new('enquiry', '', 'view') || has_permission_new('enquiry', '', 'view_own')) {
	// $CI->app_menu->add_sidebar_children_item('master', [
	// 'name'     => _l('enquiry'),
	// 'href'     => admin_url('enquiry'),
	// 'position' => 17,
	// 'icon'     => 'fa fa-ticket',
	// ]);
	// }

	// if (has_permission('tour', '', 'view') || has_permission('tour', '', 'view_own')) {

	// $CI->app_menu->add_sidebar_children_item('master', [
	// 'name'     => _l('tour_plan'),
	// 'href'     => admin_url('tour'),
	// 'position' => 18,
	// 'icon'     => 'fa fa-ticket',
	// ]);
	// }

	// if (has_permission_new('year_transfer', '', 'edit')) {
	// 	$CI->app_menu->add_sidebar_children_item('master', [
	// 	'slug'     => 'year_transfer',
	// 	'name'     => 'Year Transfer',
	// 	'href'     => admin_url('year_transfer'),
	// 	'position' => 15,
	// 	]);
	// }

	// / Transport Master menu item
	if (has_permission_new('TransportMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'TransportMaster',
			'name' => 'Transport Master',
			'href' => admin_url('TransportMaster'),
			'position' => 16,
		]);
	}

	// if (has_permission_new('manage', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('master', [
	// 		'slug' => 'CustomerCategory',
	// 		'name' => 'Customer Category Master',
	// 		'href' => admin_url('CustomerCategory'),
	// 		'position' => 20,
	// 	]);
	// }

	if (has_permission_new('Chamber', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'Chamber',
			'name' => 'Chamber Master',
			'href' => admin_url('Chamber'),
			'position' => 21,
		]);
	}
	if (has_permission_new('Stack', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'Stack',
			'name' => 'Stack Master',
			'href' => admin_url('Stack'),
			'position' => 23,
		]);
	}
	if (has_permission_new('Lot', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'Lot',
			'name' => 'Lot Master',
			'href' => admin_url('Lot'),
			'position' => 22,
		]);
	}

	if (has_permission_new('Country', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'Country',
			'name' => 'Country Master',
			'href' => admin_url('Country'),
			'position' => 24,
		]);
	}

	if (has_permission_new('Form', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'Form',
			'name' => 'Form Master',
			'href' => admin_url('Form'),
			'position' => 25,
		]);
	}
	if (has_permission_new('Conveyor', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('master', [
			'slug' => 'Conveyor',
			'name' => 'Conveyor Master',
			'href' => admin_url('Conveyor'),
			'position' => 26,
		]);
	}


	/*if (has_permission('salesperassign', '', 'update')) {
		$CI->app_menu->add_sidebar_children_item('master', [
		'slug'     => 'roles',
		'name'     => 'Role Authorization',
		'href'     => admin_url('roles'),
		'position' => 31,
		]);
	}*/

	/*if (has_permission('salesperassign', '', 'update')) {
		$CI->app_menu->add_sidebar_children_item('master', [
		'slug'     => 'account_group_master',
		'name'     => 'Account Group',
		'href'     => admin_url('accounting/account_group_master'),
		'position' => 31,
		]);
	}*/

	$CI->app_menu->add_sidebar_menu_item('Inventory', [
		'collapse' => true,
		'name' => "Inventory",
		'position' => 2,
		'icon' => 'fa fa-balance-scale',
	]);

	// if (has_permission_new('InventoryDashboard', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'InventoryDashboard',
	// 	'name'     => 'Dashboard',
	// 	'href'     => admin_url('invoice_items/NewInventoryDashboard'),
	// 	'position' => 1,
	// 	]);
	// }
	if (has_permission_new('ItemType', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'ItemType',
			'name' => 'Item Type Master',
			'href' => admin_url('ItemType'),
			'position' => 2,
		]);
	}


	if (has_permission_new('items', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'items',
			'name' => "Item Master",
			'href' => admin_url('ItemMaster'),
			'position' => 3,
		]);
	}



	if (has_permission_new('itemsmaingrp', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'itemsmaingrp',
			'name' => "Item Main Group",
			'href' => admin_url('invoice_items/MainGroups'),
			'position' => 4,
		]);
	}

	if (has_permission_new('itemssubgrp', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'itemssubgrp',
			'name' => "Item SubGroup 1",
			'href' => admin_url('invoice_items/ItemGroups'),
			'position' => 5,
		]);
	}
	if (has_permission_new('itemssubgrp2', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'itemssubgrp2',
			'name' => "Item SubGroup 2",
			'href' => admin_url('invoice_items/ItemSubGroups2'),
			'position' => 6,
		]);
	}

	if (has_permission_new('ItemDivision', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'ItemDivision',
			'name' => "Item Division",
			'href' => admin_url('ItemDivision'),
			'position' => 7,
		]);
	}

	// Item Category Master menu item
	if (has_permission_new('ItemCategory', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'ItemCategory',
			'name' => 'Item Category Master',
			'href' => admin_url('ItemCategory'),
			'position' => 8,
		]);
	}

	if (has_permission_new('hsn_master', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'hsn_master',
			'name' => 'HSN Master',
			'href' => admin_url('hsn_master'),
			'position' => 9,
		]);
	}

	if (has_permission_new('GSTMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'GSTMaster',
			'name' => 'GST Master',
			'href' => admin_url('GSTMaster'),
			'position' => 10,
		]);
	}

	if (has_permission_new('UnitMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'UnitMaster',
			'name' => 'Unit Master',
			'href' => admin_url('UnitMaster'),
			'position' => 11,
		]);
	}
	if (has_permission_new('WeightUnitMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'WeightUnitMaster',
			'name' => 'Weight Unit Master',
			'href' => admin_url('WeightUnitMaster'),
			'position' => 12,
		]);
	}

	// Brand Master menu item
	if (has_permission_new('brandMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'brandMaster',
			'name' => 'Brand Master',
			'href' => admin_url('Brand'),
			'position' => 13,
		]);
	}

	// Priority Master menu item
	if (has_permission_new('priorityMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'priorityMaster',
			'name' => 'Priority Master',
			'href' => admin_url('Priority'),
			'position' => 14,
		]);
	}

	if (has_permission_new('ItemList', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'ItemList',
			'name' => 'Item List',
			'href' => admin_url('ItemMaster/List'),
			'position' => 15,
		]);
	}


	// if (has_permission_new('ArticleMaster', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'ArticleMaster',
	// 	'name'     => "Article Master",
	// 	'href'     => admin_url('Masters/ArticleMaster'),
	// 	'position' => 16,
	// 	]);
	// }
	if (has_permission_new('GodownMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('Inventory', [
			'slug' => 'GodownMaster',
			'name' => 'Godown Master',
			'href' => admin_url('GodownMaster'),
			'position' => 16,
		]);
	}
	// if (has_permission_new('stock_position', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'stock-position',
	// 	'name'     => 'Case/Crate Wise Stock Position',
	// 	'href'     => admin_url('misc_reports/stock_position'),
	// 	'position' => 15,
	// 	]);
	// }

	// if (has_permission_new('unit_stock_position', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'unit-stock-position',
	// 	'name'     => 'Unit Wise Stock Position',
	// 	'href'     => admin_url('misc_reports/Unit_wise_stock_position'),
	// 	'position' => 14,
	// 	]);
	// }
	// if (has_permission_new('stockCummulative', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'StockCummulative',
	// 	'name'     => 'Stock Cummulative',
	// 	'href'     => admin_url('misc_reports/StockCummulative'),
	// 	'position' => 15,
	// 	]);
	// }
	// if (has_permission_new('PartyWiseRateReport', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Inventory', [
	// 'slug'     => 'PartyWiseRateReport',
	// 'name'     => 'Party Wise Rate Report',
	// 'href'     => admin_url('misc_reports/PartyWiseRateReport'),
	// 'position' => 16,
	// ]);
	// }
	// if (has_permission_new('StockTransfer', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'StockTransfer',
	// 	'name'     => 'StockTransfer',
	// 	'href'     => admin_url('GodownMaster/StockTransfer'),
	// 	'position' => 17,
	// 	]);
	// }
	// if (has_permission_new('NeededQtyTransfer', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'NeededQtyTransfer',
	// 	'name'     => 'Production Wise Stock Transfer',
	// 	'href'     => admin_url('production/NeededQtyTransfer'),
	// 	'position' => 18,
	// 	]);
	// }
	// if (has_permission_new('stock_adjustment', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	'slug'     => 'stock_adjustment',
	// 	'name'     => 'Stock Adjustment',
	// 	'href'     => admin_url('Stock_adjustment'),
	// 	'position' => 19,
	// 	]);
	// }
	// if (has_permission_new('damage_entry', '', 'view')) {
	// 	 $CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	 'slug'     => 'damage_entry',
	// 	 'name'     => 'Damage Entry',
	// 	 'href'     => admin_url('Damage_entry/AddEdit'),
	// 	 'position' => 20,
	// 	 ]);
	//  }
	// if (has_permission_new('PhysicalStockEntry', '', 'view')) {
	// 	 $CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	 'slug'     => 'PhysicalStockEntry',
	// 	 'name'     => 'Physical Stock Entry',
	// 	 'href'     => admin_url('production/PhysicalStockEntry'),
	// 	 'position' => 21,
	// 	 ]);
	//  }
	// if (has_permission_new('PhysicalStockEntryReport', '', 'view')) {
	// 	 $CI->app_menu->add_sidebar_children_item('Inventory', [
	// 	 'slug'     => 'PhysicalStockEntryReport',
	// 	 'name'     => 'Physical Stock Report',
	// 	 'href'     => admin_url('production/PhysicalStockEntryReport'),
	// 	 'position' => 22,
	// 	 ]);
	//  }

	$CI->app_menu->add_sidebar_menu_item('transport', [
		'collapse' => true,
		'name' => "Transport",
		'position' => 6,
		'icon' => 'fa fa-balance-scale',
	]);


	if (has_permission_new('vehicleMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
		'slug'     => 'vehicleMaster',
		'name'     => 'Vehicle Master',
		'href'     => admin_url('VehicleMaster'),
		'position' => 1,
		]);
	}

	if (has_permission_new('Vehicle_transaction', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'Vehicle_transaction',
			'name' => 'Vehicle Transaction',
			'href' => admin_url('Vehicle_transaction'),
			'position' => 2,
		]);
	}
	if (has_permission_new('PendingVehicleReturnList', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'PendingVehicleReturnList',
			'name' => 'Vehicle Return Report',
			'href' => admin_url('VehRtn/PendingVehicleReturnList'),
			'position' => 3,
		]);
	}
	// if (has_permission_new('VehicleReturnEntry', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'VehicleReturnEntry',
	// 	'name'     => 'Vehicle Return Entry',
	// 	'href'     => admin_url('VehRtn/AddEditVehicleReturnEntry'),
	// 	'position' => 2,
	// 	]);
	// }
	// if (has_permission_new('Vehicle_Crate', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 		'slug' => 'Vehicle_Crate',
	// 		'name' => 'Vehicle Return Crates',
	// 		'href' => admin_url('VehRtn/AddEditCrate'),
	// 		'position' => 4,
	// 	]);
	// }
	// if (has_permission_new('Vehicle_Payment', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 		'slug' => 'Vehicle_Payment',
	// 		'name' => 'Vehicle Return Payment',
	// 		'href' => admin_url('VehRtn/AddEditPayment'),
	// 		'position' => 5,
	// 	]);
	// }
	if (has_permission_new('Vehicle_Expense', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'Vehicle_Expense',
			'name' => 'Vehicle Return Expense',
			'href' => admin_url('VehRtn/AddEditExpense'),
			'position' => 6,
		]);
	}

	// if (has_permission_new('Only_Vehicle_Rtn', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('transport', [
	// 'slug'     => 'Only_Vehicle_Rtn',
	// 'name'     => 'Only Vehicle Return',
	// 'href'     => admin_url('VehRtn/AddEditOnlyVehicleRtn'),
	// 'position' => 5,
	// ]);
	// }
	// if (has_permission_new('TransportEntryList', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('transport', [
	// 'slug'     => 'TransportEntryList',
	// 'name'     => 'Transport Entry List',
	// 'href'     => admin_url('VehRtn/TransportEntryList'),
	// 'position' => 6,
	// ]);
	// }
	// if (has_permission_new('MileageReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'MileageReport',
	// 	'name'     => 'Mileage Report',
	// 	'href'     => admin_url('VehRtn/MileageReport'),
	// 	'position' => 5,
	// 	]);
	// }
	if (has_permission_new('FinalVehicleReport', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'FinalVehicleReport',
			'name' => 'Final Vehicle Report',
			'href' => admin_url('VehRtn/FinalVehicleReport'),
			'position' => 7,
		]);
	}
	// if (has_permission_new('DamageCurrencyReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'DamageCurrencyReport',
	// 	'name'     => 'Damage Currency Report',
	// 	'href'     => admin_url('VehRtn/DamageCurrencyReport'),
	// 	'position' => 7,
	// 	]);
	// }
	// if (has_permission_new('VehicleLoadedCapacityReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'VehicleLoadedCapacityReport',
	// 	'name'     => 'Vehicle Loaded Capacity Report',
	// 	'href'     => admin_url('VehRtn/VehicleLoadedCapacityReport'),
	// 	'position' => 8,
	// 	]);
	// }

	// if (has_permission_new('PremisesReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'PremisesReport',
	// 	'name'     => 'Vehicle In Premises Report',
	// 	'href'     => admin_url('VehRtn/PremisesReport'),
	// 	'position' => 9,
	// 	]);
	// }
	// if (has_permission_new('DriverRestRecord', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'DriverRestRecord',
	// 	'name'     => 'Driver Rest Entry',
	// 	'href'     => admin_url('VehRtn/DriverRestRecord'),
	// 	'position' => 10,
	// 	]);
	// }
	if (has_permission_new('RestRecordReport', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'RestRecordReport',
			'name' => 'Driver Rest Report',
			'href' => admin_url('VehRtn/RestRecordReport'),
			'position' => 8,
		]);
	}
	if (has_permission_new('VehicleMaintenanceReport', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'VehicleMaintenanceReport',
			'name' => 'Vehicle Maintenance Report',
			'href' => admin_url('VehRtn/VehicleMaintenanceReport'),
			'position' => 9,
		]);
	}
	if (has_permission_new('DelayDelivery', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'DelayDelivery',
			'name' => 'Delay Deliveries',
			'href' => admin_url('order/DelayDelivery'),
			'position' => 10,
		]);
	}
	if (has_permission_new('VehicleInPremises', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('transport', [
			'slug' => 'VehicleInPremises',
			'name' => 'Vehicle In Premises',
			'href' => admin_url('VehicleInPremises'),
			'position' => 11,
		]);
	}
	// if (has_permission_new('ShortageEntry', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'ShortageEntry',
	// 	'name'     => 'Shortage Entry',
	// 	'href'     => admin_url('ShortQtyMaster'),
	// 	'position' => 14,
	// 	]);
	// }
	// if (has_permission_new('ShortageList', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('transport', [
	// 	'slug'     => 'ShortageList',
	// 	'name'     => 'Shortage List',
	// 	'href'     => admin_url('ShortQtyMaster/shortage_list'),
	// 	'position' => 15,
	// 	]);
	// }

	$CI->app_menu->add_sidebar_menu_item('tansaction', [
		'collapse' => true,
		'name' => "Transactions",
		'position' => 5,
		'icon' => 'fa fa-balance-scale',
	]);
	
	if (has_permission_new('salesQuotation', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesQuotation',
			'name' => 'Sales Quotation',
			'href' => admin_url('SalesQuotation'),
			'position' => 1,
		]);
	}
	
	if (has_permission_new('salesQuotationList', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesQuotationList',
			'name' => 'Quotation List',
			'href' => admin_url('SalesQuotation/List'),
			'position' => 2,
		]);
	}
	
	if (has_permission_new('salesOrder', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesOrder',
			'name' => 'Sales Orders',
			'href' => admin_url('SalesOrder'),
			'position' => 3,
		]);
	}
	
	if (has_permission_new('salesOrderList', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesOrderList',
			'name' => 'Orders List',
			'href' => admin_url('SalesOrder/List'),
			'position' => 4,
		]);
	}
	if (has_permission_new('deliveryOrder', '', 'create')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'deliveryOrder',
			'name' => 'Delivery Order',
			'href' => admin_url('DeliveryOrder'),
			'position' => 5,
		]);
	}
	if (has_permission_new('pendingDeliveryOrder', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'pendingDeliveryOrder',
			'name' => 'Delivery Order List',
			'href' => admin_url('DeliveryOrder/List'),
			'position' => 6,
		]);
	}
	// if (has_permission_new('orders', '', 'create')) {
	// 	$CI->app_menu->add_sidebar_children_item('tansaction', [
	// 	'slug'     => 'orders',
	// 	'name'     => 'Order',
	// 	'href'     => admin_url('order'),
	// 	'position' => 1,
	// 	]);
	// }
	if (has_permission_new('pendingDeliveryOrder', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'pendingDeliveryOrder',
			'name' => 'Pending Orders',
			'href' => admin_url('order/pending_orders'),
			'position' => 7,
		]);
	}

	if (has_permission_new('limitExceedDeliveryOrder', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'limitExceedDeliveryOrder',
			'name' => 'Limit Exceeded Orders',
			'href' => admin_url('order/LimitExceededOrders'),
			'position' => 8,
		]);
	}
	if (has_permission_new('salesInvoice', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesInvoice',
			'name' => 'Sales Invoice',
			'href' => admin_url('SalesInvoice'),
			'position' => 9,
		]);
	}
	if (has_permission_new('salesInvoiceList', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesInvoiceList',
			'name' => 'Sales Invoice List',
			'href' => admin_url('SalesInvoice/List'),
			'position' => 10,
		]);
	}
	if (has_permission_new('salesInvoiceList', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesInvoiceList',
			'name' => 'Sale List',
			'href' => admin_url('order/SaleList'),
			'position' => 11,
		]);
	}

	if (has_permission_new('changeVehicle', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'changeVehicle',
			'name' => 'Change Vehicle',
			'href' => admin_url('challan/VehicleUpdate'),
			'position' => 12,
		]);
	}
	if (has_permission_new('salesTradeSettlement', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'salesTradeSettlement',
			'name' => 'Sales Trade Settlement',
			'href' => admin_url('TradeSettlement/Sales'),
			'position' => 13,
		]);
	}
	if (has_permission_new('stockTransfer', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('tansaction', [
			'slug' => 'stockTransfer',
			'name' => 'Stock Transfer',
			'href' => admin_url('StockTransfer'),
			'position' => 14,
		]);
	}

	// if (has_permission_new('challan', '', 'create')) {
	// 	$CI->app_menu->add_sidebar_children_item('tansaction', [
	// 	'slug'     => 'challan',
	// 	'name'     => 'Challan',
	// 	'href'     => admin_url('challan/challanAddEdit'),
	// 	'position' => 3,
	// 	]);
	// }

	// if (has_permission_new('challan_list', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('tansaction', [
	// 	'slug'     => 'challan-list',
	// 	'name'     => 'Challan List',
	// 	'href'     => admin_url('challan/challan_list'),
	// 	'position' => 4,
	// 	]);
	// }

	// if (has_permission_new('gatepass', '', 'view') || has_permission_new('gatepass', '', 'view_own')) {
	// 	$CI->app_menu->add_sidebar_children_item('tansaction', [
	// 	'slug'     => 'gatepass',
	// 	'name'     => 'Gatepass',
	// 	'href'     => admin_url('challan/view_gatepass'),
	// 	'position' => 6,
	// 	]);
	// }

	// if (has_permission_new('vehicle_return', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('tansaction', [
	// 'slug'     => 'vehicle_return',
	// 'name'     => 'Vehicle Return',
	// 'href'     => admin_url('VehRtn'),
	// 'position' => 7,
	// ]);
	// }



	// if (has_permission_new('sale_return', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('tansaction', [
	// 	'slug'     => 'sale_return',
	// 	'name'     => 'Sales Return',
	// 	'href'     => admin_url('sale_return'),
	// 	'position' => 8,
	// 	]);
	// }

	// if (has_permission_new('cd_notes', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('tansaction', [
	// 'slug'     => 'cd_notes',
	// 'name'     => 'Credit Note',
	// 'href'     => admin_url('cd_notes'),
	// 'position' => 8,
	// ]);
	// }
	// if (has_permission_new('cd_notes', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('tansaction', [
	// 'slug'     => 'cd_notes',
	// 'name'     => 'Debit Note',
	// 'href'     => admin_url('cd_notes/DebitNote'),
	// 'position' => 8,
	// ]);
	// }

	// if (has_permission_new('staff_target', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('tansaction', [
	// 'slug'     => 'target_sale',
	// 'name'     => 'Staff Target',
	// 'href'     => admin_url('misc_reports/target_sale'),
	// 'position' => 10,
	// ]);
	// }

	// if (has_permission_new('GroupWise_target_sale', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('tansaction', [
	// 	'slug'     => 'GroupWise_target_sale',
	// 	'name'     => 'Sale Target',
	// 	'href'     => admin_url('misc_reports/GroupWise_target_sale'),
	// 	'position' => 10,
	// 	]);
	// }

	// if (has_permission_new('einvoice', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('tansaction', [
	// 'slug'     => 'einvoice',
	// 'name'     => 'E-invoice',
	// 'href'     => admin_url('einvoice'),
	// 'position' => 11,
	// ]);
	// }



	// if (has_permission_new('SplDisc', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('tansaction', [
	// 'slug'     => 'SplDisc',
	// 'name'     => 'Special Discount',
	// 'href'     => admin_url('SplDisc'),
	// 'position' => 13,
	// ]);
	// }
	// if (has_permission_new('SchemeMaster', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('tansaction', [
	// 'slug'     => 'SchemeMaster',
	// 'name'     => 'Scheme Master',
	// 'href'     => admin_url('SchemeMaster'),
	// 'position' => 20,
	// ]);
	// }



	// if (has_permission_new('purchase_register', '', 'view')) {
	// $CI->app_menu->add_sidebar_menu_item('pur-reports', [
	// 'collapse' => true,
	// 'name'     => "Pur. Reports",
	// 'position' => 3,
	// 'icon'     => 'fa fa-user-circle menu-icon',
	// ]);
	// }
	// if (has_permission_new('purchase_register', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('pur-reports', [
	// 'slug'     => 'purchase-register',
	// 'name'     => 'Purchase Register',
	// 'href'     => admin_url('purchase/pur_register'),
	// 'position' => 1,
	// ]);
	// }
	// if (has_permission_new('GSTR_purchase', '', 'view') || has_permission_new('GSTR_sales', '', 'view') 
	// || has_permission_new('GGSTR_1', '', 'view') || has_permission_new('GSTR_3B', '', 'view')
	// || has_permission_new('EInvoiceReport', '', 'view')|| has_permission_new('EWayBillReport', '', 'view')){
	// 	$CI->app_menu->add_sidebar_menu_item('e-filling', [
	//     'collapse' => true,
	//     'name'     => "E-Filling",
	//     'position' => 8,
	//     'icon'     => 'fa fa-user-circle menu-icon',
	// 	]);
	// }
	// if (has_permission_new('GSTR_purchase', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('e-filling', [
	// 	'slug'     => 'GSTR-purchase',
	// 	'name'     => 'GSTR Purchase',
	// 	'href'     => admin_url('e_filling/purchase_gst_report'),
	// 	'position' => 1,
	// 	]);
	// }
	// if (has_permission_new('GSTR_sales', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('e-filling', [
	// 	'slug'     => 'GSTR-sales',
	// 	'name'     => 'GSTR Sales',
	// 	'href'     => admin_url('e_filling'),
	// 	'position' => 2,
	// 	]);
	// }
	// if (has_permission_new('EInvoiceReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('e-filling', [
	// 	'slug'     => 'EInvoiceList',
	// 	'name'     => 'EInvoice Report',
	// 	'href'     => admin_url('e_filling/EInvoiceList'),
	// 	'position' => 3,
	// 	]);
	// }
	// if (has_permission_new('EWayBillReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('e-filling', [
	// 	'slug'     => 'EWayBillList',
	// 	'name'     => 'E-Way Bill Report',
	// 	'href'     => admin_url('e_filling/EWayBillList'),
	// 	'position' => 4,
	// 	]);
	// }
	// if (has_permission_new('GGSTR_1', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('e-filling', [
	// 	'slug'     => 'GGSTR-1',
	// 	'name'     => 'GSTR-1',
	// 	'href'     => admin_url('e_filling/GSTR1'),
	// 	'position' => 5,
	// 	]);
	// }
	// if (has_permission_new('GSTR_3B', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('e-filling', [
	// 	'slug'     => 'GSTR-3B',
	// 	'name'     => 'GSTR-3B',
	// 	'href'     => admin_url('e_filling/GSTR3B'),
	// 	'position' => 6,
	// 	]);
	// }   

	/*if (has_permission('target', '', 'view') || has_permission('target', '', 'view_own')) {
		$CI->app_menu->add_sidebar_menu_item('target', [
					'name'     => _l('Target VS Achievement'),
					'href'     => admin_url('target'),
					'position' => 3,
					'icon'     => 'fa fa-ticket',
		]);
	} */

	// $CI->app_menu->add_sidebar_menu_item('production', [
	// 'collapse' => true,
	// 'name'     => "Production",
	// 'position' => 3,
	// 'icon'     => 'fa fa-balance-scale',
	// ]);



	/*if (has_permission_new('production', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('production', [
		'slug'     => 'view-production',
		'name'     => 'View Production',
		'href'     => admin_url('production/view_Order'),
		'position' => 4,
		]);
	} */

	// if (has_permission_new('ProductionDashboard', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'ProductionDashboard',
	// 	'name'     => 'Dashboard',
	// 	'href'     => admin_url('production/ProductionDashboard'),
	// 	'position' => 1,
	// 	]);
	// } 
	// if (has_permission_new('recipe', '', 'create') || has_permission_new('recipe', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'add-recipe',
	// 	'name'     => ' Recipe',
	// 	'href'     => admin_url('production'),
	// 	'position' => 2,
	// 	]);
	// } 

	// if (has_permission_new('production', '', 'create') || has_permission_new('production', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'create-production',
	// 	'name'     => 'Production',
	// 	'href'     => admin_url('production/create_order'),
	// 	'position' => 2,
	// 	]);
	// } 

	// if (has_permission_new('production_list', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'view_production_list',
	// 	'name'     => 'Production List',
	// 	'href'     => admin_url('production/view_production_list'),
	// 	'position' => 3,
	// 	]);
	// } 
	// if (has_permission_new('AddEditBakingQty', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'AddEditBakingQty',
	// 	'name'     => 'Production Baking',
	// 	'href'     => admin_url('production/AddEditBakingQty'),
	// 	'position' => 4,
	// 	]);
	// } 
	// if (has_permission_new('AddEditPackingQty', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'AddEditPackingQty',
	// 	'name'     => 'Production Packing',
	// 	'href'     => admin_url('production/AddEditPackingQty'),
	// 	'position' => 5,
	// 	]);
	// } 
	// if (has_permission_new('production_reports', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'production-reports',
	// 	'name'     => 'Production Report',
	// 	'href'     => admin_url('misc_reports/production_reports'),
	// 	'position' => 6,
	// 	]);
	// }

	// if (has_permission_new('production_order_report', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'production_order_report',
	// 	'name'     => 'Production Order Report',
	// 	'href'     => admin_url('production/production_order_report'),
	// 	'position' => 7,
	// 	]);
	// }

	// if (has_permission_new('cost_report', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'cost_report',
	// 	'name'     => 'Cost Report',
	// 	'href'     => admin_url('production/ItemWisePrdCostReports'),
	// 	'position' => 8,
	// 	]);
	// }
	// if (has_permission_new('ItemUsedInRecipeReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('production', [
	// 	'slug'     => 'ItemUsedInRecipeReport',
	// 	'name'     => 'Item Used In Recipe Report',
	// 	'href'     => admin_url('production/ItemUsedInRecipeReport'),
	// 	'position' => 9,
	// 	]);
	// }

	/*if (has_permission_new('recipe', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('production', [
		'slug'     => 'view-recipe',
		'name'     => 'View Recipe',
		'href'     => admin_url('production/all_recipe'),
		'position' => 2,
		]);
	} */

	$CI->app_menu->add_sidebar_menu_item('QC', [
		'collapse' => true,
		'name' => "QC",
		'position' => 4,
		'icon' => 'fa fa-balance-scale',
	]);

	if (has_permission_new('qcUnit', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('QC', [
			'slug' => 'qcUnit',
			'name' => 'QC Unit',
			'href' => admin_url('purchase/QC_Unit'),
			'position' => 1,
		]);
	}

	if (has_permission_new('qcMaster', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('QC', [
			'slug' => 'qcMaster',
			'name' => 'QC Parameter',
			'href' => admin_url('QC_Parameter'),
			'position' => 2,
		]);
	}
	// if (has_permission_new('qc_master', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('QC', [
	// 	'slug'     => 'qc-master',
	// 	'name'     => 'QC Master',
	// 	'href'     => admin_url('purchase/QC_Master'),
	// 	'position' => 3,
	// 	]);
	// }

	// if (has_permission_new('QcStatusList', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('QC', [
	// 	'slug'     => 'QcStatusList',
	// 	'name'     => 'QC Status List',
	// 	'href'     => admin_url('QcMaster'),
	// 	'position' => 4,
	// 	]);
	// }

	if (has_permission_new('itemWiseQc', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('QC', [
			'slug' => 'itemWiseQc',
			'name' => 'Item Wise QC',
			'href' => admin_url('QC_Parameter/Item'),
			'position' => 5,
		]);
	}
	if (has_permission_new('deductionMatrix', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('QC', [
			'slug' => 'deductionMatrix',
			'name' => 'Deduction Matrix',
			'href' => admin_url('ItemMaster/DeductionMatrix'),
			'position' => 6,
		]);
	}
	if (has_permission_new('finishGoodTest', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('QC', [
			'slug' => 'finishGoodTest',
			'name' => 'Finish Good Test',
			'href' => admin_url('purchase/FG_Test_Report'),
			'position' => 7,
		]);
	}
	// if (has_permission_new('In_Process_QC', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('QC', [
	// 'slug'     => 'In_Process_QC',
	// 'name'     => 'In Process Plant QC',
	// 'href'     => admin_url('purchase/In_Process_QC'),
	// 'position' => 6,
	// ]);
	// }
	if (has_permission_new('metalDetectorQC', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('QC', [
			'slug' => 'metalDetectorQC',
			'name' => 'Metal Detector QC',
			'href' => admin_url('purchase/Metal_Detector_Report'),
			'position' => 8,
		]);
	}

	// $CI->app_menu->add_sidebar_menu_item('sales_report', [
	// 'collapse' => true,
	// 'name'     => "Sales Reports",
	// 'position' => 10,
	// 'icon'     => 'fa fa-balance-scale',
	// ]);

	/* if (has_permission_new('SalesDashboard', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('sales_report', [
		'slug'     => 'SalesDashboard',
		'name'     => 'Sales Dashboard',
		'href'     => admin_url('Sale_reports/SalesDashboard'),
		'position' => 1,
		]);
	} */
	// if (has_permission_new('SalesDashboard', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'SalesDashboard',
	// 	'name'     => 'New Sales Dashboard',
	// 	'href'     => admin_url('Sale_reports/NewSalesDashboard'),
	// 	'position' => 1,
	// 	]);
	// }
	// if (has_permission_new('daily_sale', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'daily-sale',
	// 	'name'     => 'Daily Sales Report',
	// 	'href'     => admin_url('Sale_reports/daily_sale'),
	// 	'position' => 1,
	// 	]);
	// }

	// if (has_permission_new('cummulatives_sale', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'PartyPackWiseCummulativesSales',
	// 	'name'     => 'Party PackWise Cummulatives Sales',
	// 	'href'     => admin_url('Sale_reports/PartyPackWiseCummulativesSales'),
	// 	'position' => 2,
	// 	]);
	// }
	// if (has_permission_new('cummulatives_sale_shipto', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'PartyPackWiseCummulativesSalesShipto',
	// 	'name'     => 'Ship To Party PackWise Cummulatives Sales',
	// 	'href'     => admin_url('Sale_reports/ShipToPartyPackWiseCummulativesSales'),
	// 	'position' => 2,
	// 	]);
	// }

	// if (has_permission_new('target_vs_achivements', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('sales_report', [
	// 'slug'     => 'target_vs_achievement',
	// 'name'     => 'Target Vs Achievement',
	// 'href'     => admin_url('misc_reports/target_vs_achievement'),
	// 'position' => 3,
	// ]);
	// }
	// if (has_permission_new('SaleRtn', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'SaleRtn',
	// 	'name'     => 'Sales Return - Report',
	// 	'href'     => admin_url('Sale_reports/SaleRtn'),
	// 	'position' => 4,
	// 	]);
	// }
	// if (has_permission_new('saleVsSaleRtn', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'saleVsSaleRtn',
	// 	'name'     => 'Sales Vs SalesReturn',
	// 	'href'     => admin_url('Sale_reports/SaleVsSaleRtn'),
	// 	'position' => 4,
	// 	]);
	// }
	// if (has_permission_new('TargetVsAchievement', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'TargetVsAchievement',
	// 	'name'     => 'Target Vs Achievement',
	// 	'href'     => admin_url('misc_reports/TargetVsAchievement'),
	// 	'position' => 4,
	// 	]);
	// }
	// if (has_permission_new('PartyItemWiseReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'PartyItemWiseReport',
	// 	'name'     => 'Party ItemWise Report',
	// 	'href'     => admin_url('Sale_reports/PartyItemWiseReport'),
	// 	'position' => 5,
	// 	]);
	// }
	// if (has_permission_new('OrderVsDispatch', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'OrderVsDispatch',
	// 	'name'     => 'Order Vs Dispatch',
	// 	'href'     => admin_url('Sale_reports/OrderVsDispatch'),
	// 	'position' => 6,
	// 	]);
	// }
	// if (has_permission_new('OrderVsDispatchItemWise', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'OrderVsDispatchItemWise',
	// 	'name'     => 'OrderVsDispatch ItemWise',
	// 	'href'     => admin_url('Sale_reports/OrderVsDispatchItemWise'),
	// 	'position' => 6,
	// 	]);
	// }
	// if (has_permission_new('TradeReceivableReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'TradeReceivableReport',
	// 	'name'     => 'Trade Receivable Report',
	// 	'href'     => admin_url('Sale_reports/BillsReceivableReport'),
	// 	'position' => 7,
	// 	]);
	// }
	// if (has_permission_new('daily_sale_summary', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'daily_sale_summary',
	// 	'name'     => 'Daily Sales Summary',
	// 	'href'     => admin_url('Sale_reports/daily_sale_summary'),
	// 	'position' => 8,
	// 	]);
	// }

	// if (has_permission_new('daily_ItemWise_sale_summary_report', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'daily_sale_summary_report',
	// 	'name'     => 'Daily Item Wise Sales Report',
	// 	'href'     => admin_url('Sale_reports/daily_ItemWise_sale_summary_report'),
	// 	'position' => 9,
	// 	]);
	// }
	// if (has_permission_new('GroupWiseItemSale', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'GroupWiseItemSale',
	// 	'name'     => 'Group Wise Item Sale',
	// 	'href'     => admin_url('Sale_reports/GroupWiseItemSale'),
	// 	'position' => 10,
	// 	]);
	// }
	// if (has_permission_new('ItemDivisionWiseSale', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('sales_report', [
	// 'slug'     => 'ItemDivisionWiseSale',
	// 'name'     => 'Division Wise Sale Summary',
	// 'href'     => admin_url('Sale_reports/ItemDivisionWiseSale'),
	// 'position' => 11,
	// ]);
	// }
	// if (has_permission_new('ItemGroupWiseSale', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'ItemGroupWiseSale',
	// 	'name'     => 'Group Wise Sale Summary',
	// 	'href'     => admin_url('Sale_reports/ItemGroupWiseSale'),
	// 	'position' => 12,
	// 	]);
	// }
	// if (has_permission_new('Stock_alert', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('sales_report', [
	// 'slug'     => 'Stock_alert',
	// 'name'     => 'Stock Alert',
	// 'href'     => admin_url('Sale_reports/Stock_alert'),
	// 'position' => 13,
	// ]);
	// }
	// if (has_permission_new('FoodLicenseNumberStatus', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'FoodLicenseNumberStatus',
	// 	'name'     => 'Food License Number Status',
	// 	'href'     => admin_url('Sale_reports/FoodLicenseNumberStatus'),
	// 	'position' => 14,
	// 	]);
	// }

	// if (has_permission_new('SaleItemFlowReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'SaleItemFlowReport',
	// 	'name'     => 'Sale Item Flow Report',
	// 	'href'     => admin_url('Sale_reports/SaleItemFlowReport'),
	// 	'position' => 16,
	// 	]);
	// }
	// if (has_permission_new('rp_general_ledger_shipto', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'rp_general_ledger_shipto',
	// 	'name'     => 'Ship To Account Ledger',
	// 	'href'     => admin_url('accounting/rp_general_ledger_shipto'),
	// 	'position' => 16,
	// 	]);
	// }
	// if (has_permission_new('CustomerPerformanceReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'CustomerPerformanceReport',
	// 	'name'     => 'Customer Performance Report',
	// 	'href'     => admin_url('Sale_reports/CustomerPerformanceReport'),
	// 	'position' => 16,
	// 	]);
	// }

	// if (has_permission_new('item_rate_list', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'rate_list_report',
	// 	'name'     => 'Item Rate List',
	// 	'href'     => admin_url('misc_reports/rate_list_report'),
	// 	'position' => 17,
	// 	]);
	// }

	// if (has_permission_new('ItemWiseRateList', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'ItemWiseRateList',
	// 	'name'     => 'Item Wise Rate List',
	// 	'href'     => admin_url('rate_master/ItemWiseRateList'),
	// 	'position' => 18,
	// 	]);
	// }
	// if (has_permission_new('ItemWiseDistWiseRateList', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('sales_report', [
	// 	'slug'     => 'ItemWiseDistWiseRateList',
	// 	'name'     => 'Item Wise Distributor Wise Rate List',
	// 	'href'     => admin_url('misc_reports/ItemWiseDistWiseRateList'),
	// 	'position' => 19,
	// 	]);
	// }


	// $CI->app_menu->add_sidebar_menu_item('Misc_Reports', [
	// 'collapse' => true,
	// 'name'     => "Misc. Reports",
	// 'position' => 11,
	// 'icon'     => 'fa fa-user-circle menu-icon',
	// ]);
	// if (has_permission_new('account_list', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 'slug'     => 'account-list',
	// 'name'     => 'Account List',
	// 'href'     => '#',
	// 'position' => 1,
	// ]);
	// }


	// if (has_permission_new('market_outstanding', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 'slug'     => 'market-outstanding',
	// 'name'     => 'Market Outstanding',
	// 'href'     => admin_url('misc_reports/market_outstanding'),
	// 'position' => 3,
	// ]);
	// }

	// if (has_permission_new('all_crate_ledger', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'crate-ledger',
	// 	'name'     => 'Crate Ledger',
	// 	'href'     => admin_url('misc_reports/All_Crate_Legder_Report'),
	// 	'position' => 4,
	// 	]);
	// }
	// if (has_permission_new('crate_ledger', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'crate-ledger',
	// 	'name'     => 'Party Wise Crate Ledger',
	// 	'href'     => admin_url('misc_reports/Crate_Legder_Report'),
	// 	'position' => 4,
	// 	]);
	// }
	// if (has_permission_new('Crates_received_via_vehicle_return', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'Crates-received-via-vehicle-return',
	// 	'name'     => 'Crates received via Vehicle return',
	// 	'href'     => admin_url('misc_reports/crateRcvdVehicle'),
	// 	'position' => 5,
	// 	]);
	// }
	// if (has_permission_new('routes_covered_during_a_period', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 'slug'     => 'routes-covered-during-a-period',
	// 'name'     => 'Routes Covered during a period',
	// 'href'     => '#',
	// 'position' => 6,
	// ]);
	// }
	// if (has_permission_new('vehicles_on_route_duty', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 'slug'     => 'vehicles-on-route-duty',
	// 'name'     => 'Vehicles on Route Duty',
	// 'href'     => '#',
	// 'position' => 7,
	// ]);
	// }
	// if (has_permission_new('vehicle_operation_during_a_month', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 'slug'     => 'vehicle-operation-during-a-month',
	// 'name'     => 'Vehicle Operation during a month',
	// 'href'     => '#',
	// 'position' => 8,
	// ]);
	// }
	// if (has_permission_new('contractor_production_report', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 'slug'     => 'contractor-production-report',
	// 'name'     => 'Contractor Production Report',
	// 'href'     => '#',
	// 'position' => 9,
	// ]);
	// }
	// if (has_permission_new('contractor_production_MTD', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 'slug'     => 'contractor-production-MTD',
	// 'name'     => 'Contractor Production - MTD',
	// 'href'     => '#',
	// 'position' => 10,
	// ]);
	// }



	// if (has_permission_new('ItemWiseStockReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'ItemWiseStockReport',
	// 	'name'     => 'ItemWise Stock Report',
	// 	'href'     => admin_url('Sale_reports/ItemWiseStockReport'),
	// 	'position' => 12,
	// 	]);
	// }




	// if (has_permission_new('AcccountGroupList', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'AcccountGroupList',
	// 	'name'     => 'AcccountGroupList',
	// 	'href'     => admin_url('accounts_master/AccountMainGroupList'),
	// 	'position' => 14,
	// 	]);
	// }
	// if (has_permission_new('AcccountSubGroup1List', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'AcccountSubGroup1List',
	// 	'name'     => 'AcccountSubGroup1List',
	// 	'href'     => admin_url('accounts_master/AccountSubGroupList1'),
	// 	'position' => 15,
	// 	]);
	// }
	// if (has_permission_new('AcccountSubGroup2List', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'AcccountSubGroup2List',
	// 	'name'     => 'AcccountSubGroup2List',
	// 	'href'     => admin_url('accounts_master/AccountSubGroupList2'),
	// 	'position' => 16,
	// 	]);
	// }
	// if (has_permission_new('AccountHeadList', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'AccountHeadList',
	// 	'name'     => 'AccountHeadList',
	// 	'href'     => admin_url('accounts_master/AccountHeadList'),
	// 	'position' => 16,
	// 	]);
	// }

	// if (has_permission_new('CustomerList', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'CustomerList',
	// 	'name'     => 'CustomerList',
	// 	'href'     => admin_url('clients'),
	// 	'position' => 17,
	// 	]);
	// }

	// if (has_permission_new('CustomerFeedback', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'CustomerFeedback',
	// 	'name'     => 'Customer Feedback',
	// 	'href'     => admin_url('VehRtn/CustomerFeedback'),
	// 	'position' => 18,
	// 	]);
	// }
	// if (has_permission_new('CustomerFeedbackReport', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('Misc_Reports', [
	// 	'slug'     => 'CustomerFeedbackReport',
	// 	'name'     => 'Customer Feedback Report',
	// 	'href'     => admin_url('VehRtn/CustomerFeedbackReport'),
	// 	'position' => 19,
	// 	]);
	// }


	/*if (has_permission('subscriptions', '', 'view') || has_permission('subscriptions', '', 'view_own')) {
		$CI->app_menu->add_sidebar_menu_item('subscriptions', [
		'name'     => _l('subscriptions'),
		'href'     => admin_url('subscriptions'),
		'icon'     => 'fa fa-repeat',
		'position' => 15,
		]);
	}*/

	/*if (has_permission('expenses', '', 'view') || has_permission('expenses', '', 'view_own')) {
		$CI->app_menu->add_sidebar_menu_item('expenses', [
		'name'     => _l('expenses'),
		'href'     => admin_url('expenses'),
		'icon'     => 'fa fa-file-text-o',
		'position' => 20,
		]);
	}*/

	/*if (has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own')) {
		$CI->app_menu->add_sidebar_menu_item('contracts', [
		'name'     => _l('contracts'),
		'href'     => admin_url('contracts'),
		'icon'     => 'fa fa-file',
		'position' => 25,
		]);
	}*/

	/*$CI->app_menu->add_sidebar_menu_item('projects', [
		'name'     => _l('projects'),
		'href'     => admin_url('projects'),
		'icon'     => 'fa fa-bars',
		'position' => 30,
	]);*/



	/*if ((!is_staff_member() && get_option('access_tickets_to_none_staff_members') == 1) || is_staff_member()) {
		$CI->app_menu->add_sidebar_menu_item('support', [
		'name'     => _l('support'),
		'href'     => admin_url('tickets'),
		'icon'     => 'fa fa-ticket',
		'position' => 40,
		]);
	}*/

	/*if (is_staff_member()) {
		$CI->app_menu->add_sidebar_menu_item('leads', [
		'name'     => _l('als_leads'),
		'href'     => admin_url('leads'),
		'icon'     => 'fa fa-tty',
		'position' => 45,
		]);
	}*/

	/*if (has_permission('knowledge_base', '', 'view')) {
		$CI->app_menu->add_sidebar_menu_item('knowledge-base', [
		'name'     => _l('als_kb'),
		'href'     => admin_url('knowledge_base'),
		'icon'     => 'fa fa-folder-open-o',
		'position' => 50,
		]);
	}*/

	// Utilities
	/*$CI->app_menu->add_sidebar_menu_item('utilities', [
					'collapse' => true,
					'name'     => _l('als_utilities'),
					'position' => 70,
					'icon'     => 'fa fa-cogs',
	]);*/

	/*$CI->app_menu->add_sidebar_children_item('utilities', [
		'slug'     => 'media',
		'name'     => _l('als_media'),
		'href'     => admin_url('utilities/media'),
		'position' => 5,
	]);*/

	/*if (has_permission('bulk_pdf_exporter', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('utilities', [
		'slug'     => 'bulk-pdf-exporter',
		'name'     => _l('bulk_pdf_exporter'),
		'href'     => admin_url('utilities/bulk_pdf_exporter'),
		'position' => 10,
		]);
	}*/

	/*$CI->app_menu->add_sidebar_children_item('utilities', [
		'slug'     => 'calendar',
		'name'     => _l('als_calendar_submenu'),
		'href'     => admin_url('utilities/calendar'),
		'position' => 15,
	]);*/


	/*if (is_admin()) {
		$CI->app_menu->add_sidebar_children_item('utilities', [
		'slug'     => 'announcements',
		'name'     => _l('als_announcements_submenu'),
		'href'     => admin_url('announcements'),
		'position' => 20,
	]);*/

	/*$CI->app_menu->add_sidebar_children_item('utilities', [
'slug'     => 'activity-log',
'name'     => _l('als_activity_log_submenu'),
'href'     => admin_url('utilities/activity_log'),
'position' => 25,
]);

$CI->app_menu->add_sidebar_children_item('utilities', [
'slug'     => 'ticket-pipe-log',
'name'     => _l('ticket_pipe_log'),
'href'     => admin_url('utilities/pipe_log'),
'position' => 30,
]);*/
	//}

	/*if (has_permission('reports', '', 'view')) {
		$CI->app_menu->add_sidebar_menu_item('reports', [
		'collapse' => true,
		'name'     => _l('als_reports'),
		'href'     => admin_url('reports'),
		'icon'     => 'fa fa-area-chart',
		'position' => 60,
		]);
		$CI->app_menu->add_sidebar_children_item('reports', [
		'slug'     => 'sales-reports',
		'name'     => _l('als_reports_sales_submenu'),
		'href'     => admin_url('reports/sales'),
		'position' => 5,
		]);
		$CI->app_menu->add_sidebar_children_item('reports', [
		'slug'     => 'expenses-reports',
		'name'     => _l('als_reports_expenses'),
		'href'     => admin_url('reports/expenses'),
		'position' => 10,
		]);
		$CI->app_menu->add_sidebar_children_item('reports', [
		'slug'     => 'expenses-vs-income-reports',
		'name'     => _l('als_expenses_vs_income'),
		'href'     => admin_url('reports/expenses_vs_income'),
		'position' => 15,
		]);
		$CI->app_menu->add_sidebar_children_item('reports', [
		'slug'     => 'leads-reports',
		'name'     => _l('als_reports_leads_submenu'),
		'href'     => admin_url('reports/leads'),
		'position' => 20,
		]);

		if (is_admin()) {
					$CI->app_menu->add_sidebar_children_item('reports', [
		'slug'     => 'timesheets-reports',
		'name'     => _l('timesheets_overview'),
		'href'     => admin_url('staff/timesheets?view=all'),
		'position' => 25,
					]);
		}

		$CI->app_menu->add_sidebar_children_item('reports', [
		'slug'     => 'knowledge-base-reports',
		'name'     => _l('als_kb_articles_submenu'),
		'href'     => admin_url('reports/knowledge_base_articles'),
		'position' => 30,
					]);
	}*/

	// Setup menu
	/*if (has_permission('staff', '', 'view')) {
		$CI->app_menu->add_setup_menu_item('staff', [
		'name'     => _l('als_staff'),
		'href'     => admin_url('staff'),
		'position' => 5,
					]);
	}*/

	if (is_admin()) {

	}
	/*$CI->app_menu->add_setup_menu_item('customers', [
'collapse' => true,
'name'     => _l('clients'),
'position' => 10,
]);*/


	/*$CI->app_menu->add_setup_menu_item('support', [
'collapse' => true,
'name'     => _l('support'),
'position' => 15,
			]);

$CI->app_menu->add_setup_children_item('support', [
'slug'     => 'departments',
'name'     => _l('acs_departments'),
'href'     => admin_url('departments'),
'position' => 5,
			]);
$CI->app_menu->add_setup_children_item('support', [
'slug'     => 'tickets-predefined-replies',
'name'     => _l('acs_ticket_predefined_replies_submenu'),
'href'     => admin_url('tickets/predefined_replies'),
'position' => 10,
			]);
$CI->app_menu->add_setup_children_item('support', [
'slug'     => 'tickets-priorities',
'name'     => _l('acs_ticket_priority_submenu'),
'href'     => admin_url('tickets/priorities'),
'position' => 15,
			]);
$CI->app_menu->add_setup_children_item('support', [
'slug'     => 'tickets-statuses',
'name'     => _l('acs_ticket_statuses_submenu'),
'href'     => admin_url('tickets/statuses'),
'position' => 20,
			]);

$CI->app_menu->add_setup_children_item('support', [
'slug'     => 'tickets-services',
'name'     => _l('acs_ticket_services_submenu'),
'href'     => admin_url('tickets/services'),
'position' => 25,
			]);
$CI->app_menu->add_setup_children_item('support', [
'slug'     => 'tickets-spam-filters',
'name'     => _l('spam_filters'),
'href'     => admin_url('spam_filters/view/tickets'),
'position' => 30,
]);*/

	/*$CI->app_menu->add_setup_menu_item('leads', [
'collapse' => true,
'name'     => _l('acs_leads'),
'position' => 20,
			]);
$CI->app_menu->add_setup_children_item('leads', [
'slug'     => 'leads-sources',
'name'     => _l('acs_leads_sources_submenu'),
'href'     => admin_url('leads/sources'),
'position' => 5,
			]);
$CI->app_menu->add_setup_children_item('leads', [
'slug'     => 'leads-statuses',
'name'     => _l('acs_leads_statuses_submenu'),
'href'     => admin_url('leads/statuses'),
'position' => 10,
			]);
$CI->app_menu->add_setup_children_item('leads', [
'slug'     => 'leads-email-integration',
'name'     => _l('leads_email_integration'),
'href'     => admin_url('leads/email_integration'),
'position' => 15,
			]);
$CI->app_menu->add_setup_children_item('leads', [
'slug'     => 'web-to-lead',
'name'     => _l('web_to_lead'),
'href'     => admin_url('leads/forms'),
'position' => 20,
]);*/

	/*$CI->app_menu->add_setup_menu_item('finance', [
'collapse' => true,
'name'     => _l('acs_finance'),
'position' => 25,
			]);
$CI->app_menu->add_setup_children_item('finance', [
'slug'     => 'taxes',
'name'     => _l('acs_sales_taxes_submenu'),
'href'     => admin_url('taxes'),
'position' => 5,
			]);
$CI->app_menu->add_setup_children_item('finance', [
'slug'     => 'currencies',
'name'     => _l('acs_sales_currencies_submenu'),
'href'     => admin_url('currencies'),
'position' => 10,
			]);
$CI->app_menu->add_setup_children_item('finance', [
'slug'     => 'payment-modes',
'name'     => _l('acs_sales_payment_modes_submenu'),
'href'     => admin_url('paymentmodes'),
'position' => 15,
			]);
$CI->app_menu->add_setup_children_item('finance', [
'slug'     => 'expenses-categories',
'name'     => _l('acs_expense_categories'),
'href'     => admin_url('expenses/categories'),
'position' => 20,
]);*/

	/*$CI->app_menu->add_setup_menu_item('contracts', [
'collapse' => true,
'name'     => _l('acs_contracts'),
'position' => 30,
			]);
$CI->app_menu->add_setup_children_item('contracts', [
'slug'     => 'contracts-types',
'name'     => _l('acs_contract_types'),
'href'     => admin_url('contracts/types'),
'position' => 5,
]);*/

	$CI->app_menu->add_sidebar_menu_item('hr', [
		'collapse' => true,
		'name' => "HR",
		'position' => 9,
		'icon' => 'fa fa-user-circle menu-icon',
	]);

	if (has_permission_new('hrmDashboard', '', 'create')) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmDashboard',
			'name' => _l('HR Dashboard'),
			'icon' => 'fa fa-user',
			'href' => admin_url('hr_profile/dashboard'),
			'position' => 1,
		]);
	}

	if (has_permission_new('hrmStaffMembers', '', 'create')) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmStaffMembers',
			'name' => _l('Staff members'),
			'icon' => 'fa fa-user',
			'href' => admin_url('hr_profile/AddEditStaff'),
			'position' => 2,
		]);
	}
	if (has_permission_new('hrmStaffList', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmStaffList',
			'name' => 'Staff List',
			'icon' => 'fa fa-user',
			'href' => admin_url('hr_profile/staff_infor'),
			'position' => 3,
		]);
	}
	// if (has_permission_new('salarymaster', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('hr', [
	// 	'slug'     => 'SalaryMaster',
	// 	'name'     => 'Salary Master',
	// 	'href'     => admin_url('payroll/NewSalaryMaster'),
	// 	'position' => 4,
	// 	]);
	// }

	// if(has_permission_new('DailyAttendanceRegister','','view')){
	// 	$CI->app_menu->add_sidebar_children_item('hr', [
	// 	'slug'     => 'DailyAttendanceRegister',
	// 	'name'     => 'Daily Attendance Register',
	// 	'icon'     => 'fa fa-user',
	// 	'href'     => admin_url('hr_profile/DailyAttendanceRegister'),
	// 	'position' => 5,
	// 	]);
	// }
	if (has_permission_new('hrmAttendanceSheet', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmAttendanceSheet',
			'name' => 'Attendance Sheet',
			'icon' => 'fa fa-user',
			'href' => admin_url('hr_profile/AttendanceSheet'),
			'position' => 6,
		]);
	}
	if (has_permission_new('hrmClaimExpenses', '', 'view') || has_permission_new('cliam_expenses', '', 'edit')) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmClaimExpenses',
			'name' => 'Claim Expenses',
			'icon' => 'fa fa-user',
			'href' => admin_url('claim_expenses'),
			'position' => 7,
		]);
	}

	if (has_permission_new('hrmJobDepartments', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmJobDepartments',
			'name' => "Job Departments",
			'icon' => 'fa fa-map-pin',
			'href' => admin_url('departments'),
			'position' => 8,
		]);
	}
	if (has_permission_new('hrmJobDesignation', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmJobDesignation',
			'name' => "Job Designation",
			'icon' => 'fa fa-map-pin',
			'href' => admin_url('hr_profile/job_positions'),
			'position' => 8,
		]);
	}

	if (has_permission_new('hrmShiftCategories', '', 'view_own') || has_permission_new('timesheets_shift_type', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmShiftCategories',
			'name' => _l('shift_type'),
			'href' => admin_url('timesheets/manage_shift_type'),
			'icon' => 'fa fa-magic',
			'position' => 9,
		]);
	}

	if (has_permission_new('hrmShift', '', 'view_own') || has_permission_new('table_shiftwork_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmShift',
			'name' => _l('shift_management'),
			'href' => admin_url('timesheets/shift_management'),
			'icon' => 'fa fa-calendar',
			'position' => 10,
		]);
	}

	if (has_permission_new('hrmWorkShift', '', 'view_own') || has_permission_new('timesheets_table_shiftwork', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmWorkShift',
			'name' => _l('shiftwork'),
			'href' => admin_url('timesheets/table_shiftwork'),
			'icon' => 'fa fa-ticket',
			'position' => 11,
		]);
	}

	if (is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'timesheets_setting',
			'name' => 'Annual Leave & Holiday',
			'href' => admin_url('timesheets/setting?group=manage_leave'),
			'icon' => 'fa fa-gears',
			'position' => 12,
		]);
	}

	if (has_permission_new('hrmLeave', '', 'view_own') || has_permission_new('leave_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
			'slug' => 'hrmLeave',
			'name' => _l('leave'),
			'icon' => 'fa fa-clipboard',
			'href' => admin_url('timesheets/requisition_manage'),
			'position' => 13,

		]);
	}
	/*if(has_permission_new('hrm_dependent_person','','view')){
		$CI->app_menu->add_sidebar_children_item('hr', [
		'slug'     => 'hr_profile_dependent_person',
		'name'     => _l('hr_dependent_persons'),
		'icon'     => 'fa fa-address-card-o',
		'href'     => admin_url('hr_profile/dependent_persons'),
		'position' => 4,
		]);
	}*/


	/*if (has_permission_new('attendance_management', '', 'view_own') || has_permission_new('attendance_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
		'slug'     => 'timesheets_timekeeping',
		'name'     => _l('attendance_sheet'),
		'href'     => admin_url('timesheets/timekeeping'),
		'icon'     => 'fa fa-pencil-square-o',
		'position' =>5,
		]); 
	}*/

	/**/

	/*if (has_permission_new('route_management', '', 'view_own') || has_permission_new('route_management', '', 'view') || is_admin()) {  
		$allow_attendance_by_route = 0;
		$data_by_route = get_timesheets_option('allow_attendance_by_route');
		if($data_by_route){
		$allow_attendance_by_route = $data_by_route;
		}  
		if($allow_attendance_by_route == 1){
		$CI->app_menu->add_sidebar_children_item('hr', [
		'slug'     => 'timesheets_route_management',
		'name'     => _l('route_management'),
		'icon'     => 'fa fa-map-signs',
		'href'     => admin_url('timesheets/route_management?tab=route') ,
		'position' => 6,

		]);
		}      
	}*/

	/**/
	/*
	 */
	/*$data_attendance_by_coordinates = get_timesheets_option('allow_attendance_by_coordinates');
		if($data_attendance_by_coordinates){
		if($data_attendance_by_coordinates == 1){
		if (has_permission_new('table_workplace_management', '', 'view_own') || has_permission_new('table_workplace_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
		'slug'     => 'timesheets_workplace_mgt',
		'name'     => _l('workplace_mgt'),
		'href'     => admin_url('timesheets/workplace_mgt?group=workplace_assign'),
		'icon'     => 'fa fa-street-view',
		'position' => 10,
		]);
		}
		}
	}*/



	// if (has_permission_new('salaryComponents', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('hr', [
	// 	'slug'     => 'salaryComponents',
	// 	'name'     => 'Salary Components',
	// 	'href'     => admin_url('payroll/salaryComponents'),
	// 	'position' => 11,
	// 	]);
	// }

	/*if (has_permission('report_management', '', 'view_own') || has_permission('report_management', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('hr', [
		'slug'     => 'timesheets-report',
		'name'     => "Timesheets Reports",
		'href'     => admin_url('timesheets/reports'),
		'icon'     => 'fa fa-line-chart',
		'position' =>11,
		]);
	}*/

	/*if(has_permission('hrp_employee','','view') || has_permission('hrp_employee','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hr_manage_employees',
					'name'     => _l('hr_manage_employees'),
					'icon'     => 'fa fa-vcard-o',
					'href'     => admin_url('hr_payroll/manage_employees'),
					'position' => 12,
		]);
	}*/

	/*if(has_permission('hrp_attendance','','view') || has_permission('hrp_attendance','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hr_manage_attendance',
					'name'     => _l('hr_manage_attendance'),
					'icon'     => 'fa fa-pencil-square-o menu-icon',
					'href'     => admin_url('hr_payroll/manage_attendance'),
					'position' => 13,
		]);
	}*/

	/*if(has_permission_new('hrp_commission','','view') || has_permission_new('hrp_commission','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hr_manage_commissions',
					'name'     => _l('hrp_commission_manage'),
					'icon'     => 'fa fa-american-sign-language-interpreting',
					'href'     => admin_url('hr_payroll/manage_commissions'),
					'position' => 14,
		]);
	}*/


	/*if(has_permission_new('hrp_deduction','','view') || has_permission_new('hrp_deduction','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hr_manage_deductions',
					'name'     => _l('hrp_deduction_manage'),
					'icon'     => 'fa fa-cut',
					'href'     => admin_url('hr_payroll/manage_deductions'),
					'position' => 15,
		]);
	}*/


	/*if(has_permission_new('hrp_bonus_kpi','','view') || has_permission_new('hrp_bonus_kpi','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hr_bonus_kpi',
					'name'     => _l('hr_bonus_kpi'),
					'icon'     => 'fa fa-gift',
					'href'     => admin_url('hr_payroll/manage_bonus'),
					'position' => 16,
		]);
	}*/

	/*if(has_permission_new('hrp_insurrance','','view') || has_permission_new('hrp_insurrance','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hrp_insurrance',
					'name'     => _l('hrp_insurrance'),
					'icon'     => 'fa fa-medkit',
					'href'     => admin_url('hr_payroll/manage_insurances'),
					'position' => 17,
		]);
	}*/

	/*if(has_permission('hrp_payslip','','view') || has_permission('hrp_payslip','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hr_pay_slips',
					'name'     => _l('hr_pay_slips'),
					'icon'     => 'fa fa-money',
					'href'     => admin_url('hr_payroll/payslip_manage'),
					'position' => 18,
		]);
	}*/

	/*if(has_permission('hrp_payslip_template','','view') || has_permission('hrp_payslip_template','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hrp_payslip_template',
					'name'     => _l('hr_pay_slip_templates'),
					'icon'     => 'fa fa-outdent',
					'href'     => admin_url('hr_payroll/payslip_templates_manage'),
					'position' => 19,
		]);
	}*/

	/*if(has_permission_new('hrp_income_tax','','view') || has_permission_new('hrp_income_tax','','view_own')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hrp_income_tax',
					'name'     => _l('hrp_income_tax'),
					'icon'     => 'fa fa-calendar-minus-o',
					'href'     => admin_url('hr_payroll/income_taxs_manage'),
					'position' => 20,
		]);
	}*/

	/*if (has_permission('tour', '', 'view') || has_permission('tour', '', 'view_own')) {

		$CI->app_menu->add_sidebar_children_item('hr', [
					'name'     => _l('tour_plan'),
					'href'     => admin_url('tour'),
					'position' => 21,
					'icon'     => 'fa fa-ticket',
		]);
	}*/

	/*if(has_permission('hrp_report','','view')){
		$CI->app_menu->add_sidebar_children_item('hr', [
					'slug'     => 'hr_payroll_reports',
					'name'     => _l('hrp_reports'),
					'icon'     => 'fa fa-list-alt',
					'href'     => admin_url('hr_payroll/reports'),
					'position' => 21,
		]);
	}*/



	$modules_name = _l('modules');

	if ($modulesNeedsUpgrade = $CI->app_modules->number_of_modules_that_require_database_upgrade()) {
		$modules_name .= '<span class="badge menu-badge bg-warning">' . $modulesNeedsUpgrade . '</span>';
	}

	$CI->app_menu->add_setup_menu_item('modules', [
		'href' => admin_url('modules'),
		'name' => $modules_name,
		'position' => 35,
	]);



	/*$CI->app_menu->add_setup_menu_item('custom-fields', [
'href'     => admin_url('custom_fields'),
'name'     => _l('asc_custom_fields'),
'position' => 45,
]);*/

	/*$CI->app_menu->add_setup_menu_item('gdpr', [
'href'     => admin_url('gdpr'),
'name'     => _l('gdpr_short'),
'position' => 50,
]);*/

	/*$CI->app_menu->add_setup_menu_item('roles', [
'href'     => admin_url('roles'),
'name'     => _l('acs_roles'),
'position' => 55,
]);*/

	/*             $CI->app_menu->add_setup_menu_item('api', [
		'href'     => admin_url('api'),
		'name'     => 'API',
		'position' => 65,
	]);*/

	if (has_permission('settings', '', 'view')) {
		$CI->app_menu->add_setup_menu_item('settings', [
			'href' => admin_url('settings'),
			'name' => _l('acs_settings'),
			'position' => 200,
		]);
	}

	if (has_permission('email_templates', '', 'view')) {
		$CI->app_menu->add_setup_menu_item('email-templates', [
			'href' => admin_url('emails'),
			'name' => _l('acs_email_templates'),
			'position' => 40,
		]);
	}
	if (
		is_admin() || has_permission_new('user_master', '', 'view') || has_permission_new('user_master', '', 'edit') || has_permission_new('no_show', '', 'view') || has_permission_new('no_show', '', 'edit') || has_permission_new('user_rights', '', 'view') || has_permission_new('user_rights', '', 'edit') || has_permission_new('distributor_type', '', 'view') || has_permission_new('distributor_type', '', 'edit') || has_permission_new('distributor_type', '', 'delete') || has_permission_new('roles', '', 'view') || has_permission_new('roles', '', 'edit') || has_permission_new('roles', '', 'delete')
		|| has_permission_new('hrm_salary_type', '', 'create') || has_permission_new('hrm_salary_type', '', 'view') || has_permission_new('hrm_salary_type', '', 'edit') || has_permission_new('hrm_salary_type', '', 'delete') || has_permission_new('hrm_allowance_type', '', 'create') || has_permission_new('hrm_allowance_type', '', 'view') || has_permission_new('hrm_allowance_type', '', 'edit') || has_permission_new('hrm_allowance_type', '', 'delete') || has_permission_new('hrm_hedquarter', '', 'view') || has_permission_new('hrm_hedquarter', '', 'create') || has_permission_new('hrm_gen_setting', '', 'view') || has_permission_new('hrm_gen_setting', '', 'edit')
	) {
		$CI->app_menu->add_sidebar_menu_item('admin', [
			'collapse' => true,
			'name' => "Admin",
			'position' => 80,
			'icon' => 'fa fa-user-circle menu-icon',
		]);
	}

	if (is_admin()) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'slug' => 'media',
			'name' => 'Documents Management',
			'href' => admin_url('utilities/media'),
			'position' => 1,
		]);
	}

	if (is_admin() || has_permission_new('user_master', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'slug' => 'user_master',
			'name' => 'User Master',
			'href' => admin_url('accounts_master/User_master'),
			'position' => 1,
		]);
	}
	if (is_admin() || has_permission_new('user_rights', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'slug' => 'user_rights',
			'name' => 'User Rights',
			'href' => admin_url('roles/user_rights'),
			'position' => 2,
		]);
	}

	// if (is_admin() || has_permission_new('user_rights', '', 'view')) {
	// $CI->app_menu->add_sidebar_children_item('admin', [
	// 'slug'     => 'InvoiceNote',
	// 'name'     => 'Invoice Note',
	// 'href'     => admin_url('accounts_master/InvoiceNote'),
	// 'position' => 2,
	// ]);
	// }

	// if (is_admin() || has_permission_new('no_show', '', 'view')) {
	// 	$CI->app_menu->add_sidebar_children_item('admin', [
	// 	'slug'     => 'no_show',
	// 	'name'     => 'No Show Accounts',
	// 	'href'     => admin_url('clients/no_show'),
	// 	'position' => 3,
	// 	]);
	// }

	if (is_admin() || has_permission_new('distributor_type', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'slug' => 'distributor-type',
			'name' => 'Distributor Type',
			'href' => admin_url('clients/groups'),
			'position' => 4,
		]);
	}

	/*if (has_permission_new('AccountIDMerge', '', 'update')) {
		$CI->app_menu->add_sidebar_children_item('admin', [
		'slug'     => 'AccountIDMerge',
		'name'     => 'Merge AccountID',
		'href'     => admin_url('AccountIDMerge'),
		'position' => 5,
		]);
		}

		if (has_permission_new('ItemIDMerge', '', 'update')) {
		$CI->app_menu->add_sidebar_children_item('admin', [
		'slug'     => 'ItemIDMerge',
		'name'     => 'Merge ItemID',
		'href'     => admin_url('ItemIDMerge'),
		'position' => 6,
		]);
	}*/

	/*if (has_permission('accounting_dashboard', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('admin', [
		'slug' => 'account_group_master',
		'name' => "Account Group",
		'icon' => 'fa fa-book',
		'href' => admin_url('accounting/account_group_master'),
		'position' => 5,
		]);
	}*/
	if (is_admin() || has_permission_new('roles', '', 'view')) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'href' => admin_url('roles'),
			'name' => 'Staff Role',
			'position' => 6,
		]);
	}
	/*if (has_permission_new('enquiry', '', 'view') || has_permission_new('enquiry', '', 'view_own')) {
		$CI->app_menu->add_sidebar_children_item('admin', [
					'name'     => _l('enquiry'),
					'href'     => admin_url('enquiry'),
					'position' => 10,
					'icon'     => 'fa fa-ticket',
		]);
	}*/
	/*if(has_permission_new('hrm_salary_type','','create') || has_permission_new('hrm_salary_type','','view') || has_permission_new('hrm_salary_type','','edit') || has_permission_new('hrm_salary_type','','delete') || has_permission_new('hrm_allowance_type','','create') || has_permission_new('hrm_allowance_type','','view') || has_permission_new('hrm_allowance_type','','edit') || has_permission_new('hrm_allowance_type','','delete') || has_permission_new('hrm_hedquarter','','view') || has_permission_new('hrm_hedquarter','','create') || has_permission_new('hrm_gen_setting','','view') || has_permission_new('hrm_gen_setting','','edit')){
		if(has_permission_new('hrm_salary_type','','view') || has_permission_new('hrm_salary_type','','create') || has_permission_new('hrm_salary_type','','edit') || has_permission_new('hrm_salary_type','','delete')){
		$url = admin_url('hr_profile/setting?group=salary_type');
		}else if(has_permission_new('hrm_allowance_type','','view') || has_permission_new('hrm_allowance_type','','create') || has_permission_new('hrm_allowance_type','','edit') || has_permission_new('hrm_allowance_type','','delete')){
		$url = admin_url('hr_profile/setting?group=allowance_type');
		}else if(has_permission_new('hrm_hedquarter','','view') || has_permission_new('hrm_hedquarter','','create')){
		$url = admin_url('hr_profile/setting?group=hedquarter');
		}else if(has_permission_new('hrm_gen_setting','','view') || has_permission_new('hrm_gen_setting','','edit')){
		$url = admin_url('hr_profile/setting?group=prefix_number');
		}
		$CI->app_menu->add_sidebar_children_item('admin', [
		'slug'     => 'hr_profile_setting',
		'name'     => 'HR Records Setting',
		'icon'     => 'fa fa-cogs',
		'href'     => $url,
		'position' => 7,
		]);
	}*/

	/*if(has_permission_new('hrp_setting','','view') || is_admin()){
		$CI->app_menu->add_sidebar_children_item('admin', [
					'slug'     => 'hrp_settings',
					'name'     => 'HR Payroll Setting',
					'icon'     => 'fa fa-cog menu-icon',
					'href'     => admin_url('hr_payroll/setting?group=income_tax_rates'),
					'position' => 8,
		]);

	}*/



	/*if (is_admin()) {
		$CI->app_menu->add_sidebar_children_item('admin', [
					'slug'     => 'purchase-settings',
					'name'     => 'Purchase Setting',
					'icon'     => 'fa fa-gears',
					'href'     => admin_url('purchase/setting'),
					'position' => 10,
		]);
	}*/
	if (has_permission_new('settings', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'href' => admin_url('settings'),
			'name' => 'Genaral Setting',
			'position' => 11,
		]);
	}
	if (has_permission_new('tasks', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('admin', [
			'name' => _l('als_tasks'),
			'href' => admin_url('tasks'),
			'icon' => 'fa fa-tasks',
			'position' => 12,
		]);

	}

	// if (has_permission_new('support', '', 'view') || is_admin()) {
	// $CI->app_menu->add_sidebar_children_item('admin', [
	// 'name'     => 'Tickets',
	// 'href'     => admin_url('tickets'),
	// 'icon'     => 'fa fa-ticket',
	// 'position' => 13,
	// ]);
	// }
	/*if (has_permission('email_templates', '', 'view') || is_admin()) {
		$CI->app_menu->add_sidebar_children_item('admin', [
		'href'     => admin_url('emails'),
		'name'     => _l('acs_email_templates'),
		'position' => 12,
					]);
	}*/
}