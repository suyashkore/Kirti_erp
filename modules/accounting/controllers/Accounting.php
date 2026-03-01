<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Accounting extends AdminController
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model('accounting_model');
			$this->load->model('order_model');
			$this->load->model('misc_reports_model');
			if(get_option('acc_add_default_account') == 0){
				$this->accounting_model->add_default_account();
			}
			if(get_option('acc_add_default_account_new') == 0){
				$this->accounting_model->add_default_account_new();
			}
		}
		//=================== Profit loss T Format =====================================
		public function ProfitLossTFormat($from_date = "",$date = "")
		{
			if (!has_permission_new('profitlossTFormat', '', 'view')) {
				access_denied('access denied');
			}
			
			
			$fy = $this->session->userdata('finacial_year');
			
			if(!empty($from_date)){
				$fromdate = $from_date." 00:00:00";
				}else{
				$fromdate = '20'.$fy.date('-04-01 H:i:s');
			}
			if(!empty($date)){
				$todate = $date." 23:59:59";
				}else{
				$todate = date('Y-m-d H:i:s');
			}
			$this->load->model('accounts_master_model');
			$data['title'] = "Proft Loss Report";
			$data['company_detail'] = $this->misc_reports_model->get_company_detail();
			if(!empty($date)){
				// Other Income
				$OtherIncome = $this->accounting_model->GetOtherIncome($fromdate,$todate);
				$data['OtherIncome'] = $OtherIncome;
				$TotalOpnQtyAmt = 0;
				$OpeningInventoryAmt = $this->accounting_model->GetOpeningInventoryAmt($fromdate,$todate);
				$data['OpeningInventoryAmt'] = $OpeningInventoryAmt;
				$ClosingInventoryAmt = $this->accounting_model->GetClosingInventoryAmt($fromdate,$todate);
				$data['ClosingInventoryAmt'] = $ClosingInventoryAmt;
				$TransactionAmt = $this->accounting_model->GetTransactionAmt($fromdate,$todate);
				$data['TransactionAmt'] = $TransactionAmt;
				// Direct Expenses
				$DirectExp = $this->accounting_model->GetDirectExpenses($fromdate,$todate);
				$data['DirectExp'] = $DirectExp;
				// Employee benefits expense
				$EMPBenData = $this->accounting_model->GetEMPBen($fromdate,$todate);
				$data['EMPBenData'] = $EMPBenData;
				// Finance Costs
				$FinanceCostData = $this->accounting_model->GetFinanceCostData($fromdate,$todate);
				$data['FinanceCostData'] = $FinanceCostData;
				// Depreciation And Amortization Expense
				$DeprecData = $this->accounting_model->GetDeprecAmortData($fromdate,$todate);
				$data['DeprecData'] = $DeprecData;
				// Indirect Expenses
				$OtherExpensesData = $this->accounting_model->GetOtherExpensesData($fromdate,$todate);
				$data['OtherExpensesData'] = $OtherExpensesData;
				
				// TAX Expense
				$TaxExpense = $this->accounting_model->GetTaxExpense($fromdate,$todate);
				$data['TaxExpense'] = $TaxExpense;
			}
			if(!empty($date)){
				$formatted_date = date('d/m/Y', strtotime($date));
				$data['Date'] = $formatted_date;
			}
			if(!empty($from_date)){
				$formatted_date2 = date('d/m/Y', strtotime($from_date));
				$data['FromDate'] = $formatted_date2;
			}
			// echo "<pre>";print_r($data['Date']);die;
			/*echo "<pre>";
				print_r($OpeningInventoryAmt);
			die;*/
			$this->load->view('AccountStatements/ProfitLossTFormat', $data);
		}
		
		public function export_ProfitLossTFormat()
		{
			if (!has_permission_new('profitlossTFormat', '', 'export')) {
				access_denied('access_denied');
			}
			
			$fy = $this->session->userdata('finacial_year');
			
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if ($this->input->post()) {
				$from_date = $this->input->post('from_date');
				$as_on_date = $this->input->post('as_on_date');
				$from_date = to_sql_date($from_date);
				$date = to_sql_date($as_on_date);
				
				$fromdate = !empty($from_date) ? $from_date . " 00:00:00" : '20'.$fy.'-04-01 00:00:00';
				$todate = !empty($date) ? $date . " 23:59:59" : date('Y-m-d H:i:s');
				
				$company_details = $this->misc_reports_model->get_company_detail();
				$this->load->model('accounting_model');
				
				// Fetch data
				$OpeningInventoryAmt = $this->accounting_model->GetOpeningInventoryAmt($fromdate,$todate);
				$ClosingInventoryAmt = $this->accounting_model->GetClosingInventoryAmt($fromdate,$todate);
				$TransactionAmt = $this->accounting_model->GetTransactionAmt($fromdate,$todate);
				$DirectExp = $this->accounting_model->GetDirectExpenses($fromdate,$todate);
				$EMPBenData = $this->accounting_model->GetEMPBen($fromdate,$todate);
				$FinanceCostData = $this->accounting_model->GetFinanceCostData($fromdate,$todate);
				$DeprecData = $this->accounting_model->GetDeprecAmortData($fromdate,$todate);
				$OtherExpensesData = $this->accounting_model->GetOtherExpensesData($fromdate,$todate);
				$OtherIncome = $this->accounting_model->GetOtherIncome($fromdate,$todate);
				
				// Calculations
				$TotalSaleRtn = $TransactionAmt->FrtRtnCurrentYear + $TransactionAmt->DFrtRtnCurrentYear;
				$TotalRevenueIncome = $TransactionAmt->SaleCurrentYear - $TotalSaleRtn;
				
				$GrossProfitC_F = ($TotalRevenueIncome + $ClosingInventoryAmt->CurrentYear) - ($OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear);
				$IndirectExp = $EMPBenData->CurrentYear + $FinanceCostData->CurrentYear + $DeprecData->CurrentYear + $OtherExpensesData->CurrentYear;
				$NetProfit = $GrossProfitC_F + $OtherIncome->CurrentYear - $IndirectExp;
				
				$AllTotalLeft = $NetProfit + $IndirectExp;
				$AllTotalRight = $GrossProfitC_F + $OtherIncome->CurrentYear;
				
				$writer = new XLSXWriter();
				$sheet = 'Sheet1';
				$style_bold = ['font-style' => 'bold'];
				$style_amount = ['halign' => 'right', 'number_format' => '#,##0.00'];
				
				// Unicode non-breaking space for alignment
				$nbsp = "\u{00A0}";
				$indent1 = str_repeat($nbsp, 8);
				$indent2 = str_repeat($nbsp, 16);
				$indent3 = str_repeat($nbsp, 32);
				
				// Header
				$writer->writeSheetRow($sheet, [$company_details->company_name], [$style_bold]);
				$writer->writeSheetRow($sheet, [$company_details->address]);
				$writer->writeSheetRow($sheet, ['Profit & Loss T-Format as on ' . $as_on_date], [$style_bold]);
				$writer->writeSheetRow($sheet, ['Expenses', 'Amount', '', 'Income', 'Amount'], [$style_bold, $style_bold, '', $style_bold, $style_bold]);
				
				$rows = [];
				
				// Opening and Sales
				$rows[] = ['Opening Amount', '', '', 'Revenue from Operation', number_format($TotalRevenueIncome, 2)];
				$rows[] = [$indent1 . 'RM Opening Amt', number_format($OpeningInventoryAmt->TotalRMOpnAmt, 2), '', $indent1 . 'Sale Amount', number_format($TransactionAmt->SaleCurrentYear, 2)];
				$rows[] = [$indent1 . 'FG Opening Amt', number_format($OpeningInventoryAmt->TotalFGOpnAmt, 2), '', $indent1 . 'Sale Return Amount', number_format($TotalSaleRtn, 2)];
				
				$rows[] = ['Purchase Account', number_format($TransactionAmt->PurchCurrentYear, 2), '', '', ''];
				
				// Direct Expenses
				$rows[] = ['Direct Expense - add', number_format($DirectExp->CurrentYear, 2), '', '', ''];
				foreach ($DirectExp->nestedData as $grp) {
					$rows[] = [$indent1 . $grp['Group1Name'], number_format($grp['Group1ClsBal'], 2), '', '', ''];
					foreach ($grp['SubGroups2'] as $subgrp) {
						$rows[] = [$indent2 . $subgrp['SubGroupName'], number_format($subgrp['Group2ClsBal'], 2), '', '', ''];
						foreach ($subgrp['Accounts'] as $acc) {
							$rows[] = [$indent3 . $acc['AccountName'], number_format($acc['AccountClsBal'], 2), '', '', ''];
						}
					}
				}
				
				// Gross Profit and Closing Amt
				$rows[] = ['Gross Profit c/o', number_format($GrossProfitC_F, 2), '', 'Closing Amt', number_format($ClosingInventoryAmt->CurrentYear, 2)];
				$rows[] = ['', number_format($OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear + $GrossProfitC_F, 2), '', '', number_format($TotalRevenueIncome + $ClosingInventoryAmt->CurrentYear, 2)];
				
				// Employee Benefits
				$rows[] = ['Employee Benefits Expense', number_format($EMPBenData->CurrentYear, 2), '', 'Gross Profit b/f', number_format($GrossProfitC_F, 2)];
				foreach ($EMPBenData->nestedData as $grp) {
					$rows[] = [$indent1 . $grp['SubGroupName'], number_format($grp['Group2ClsBal'], 2), '', '', ''];
					foreach ($grp['Accounts'] as $acc) {
						$rows[] = [$indent2 . $acc['AccountName'], number_format($acc['AccountClsBal'], 2), '', '', ''];
					}
				}
				
				// Finance Costs
				$rows[] = ['FINANCE COST', number_format($FinanceCostData->CurrentYear, 2), '', 'Other Income', number_format($OtherIncome->CurrentYear, 2)];
				foreach ($FinanceCostData->nestedData as $grp) {
					$rows[] = [$indent1 . $grp['SubGroupName'], number_format($grp['Group2ClsBal'], 2), '', '', ''];
					foreach ($grp['Accounts'] as $acc) {
						$rows[] = [$indent2 . $acc['AccountName'], number_format($acc['AccountClsBal'], 2), '', '', ''];
					}
				}
				
				// Depreciation
				$rows[] = ['Depreciation And Amortization', number_format($DeprecData->CurrentYear, 2), '', '', ''];
				foreach ($DeprecData->nestedData as $grp) {
					$rows[] = [$indent1 . $grp['SubGroupName'], number_format($grp['Group2ClsBal'], 2), '', '', ''];
					foreach ($grp['Accounts'] as $acc) {
						$rows[] = [$indent2 . $acc['AccountName'], number_format($acc['AccountClsBal'], 2), '', '', ''];
					}
				}
				
				// Indirect Expenses
				$rows[] = ['INDIRECT EXPENSES', number_format($OtherExpensesData->CurrentYear, 2), '', '', ''];
				foreach ($OtherExpensesData->nestedData as $grp) {
					$rows[] = [$indent1 . $grp['Group1Name'], number_format($grp['Group1ClsBal'], 2), '', '', ''];
					foreach ($grp['SubGroups2'] as $subgrp) {
						$rows[] = [$indent2 . $subgrp['SubGroupName'], number_format($subgrp['Group2ClsBal'], 2), '', '', ''];
						foreach ($subgrp['Accounts'] as $acc) {
							$rows[] = [$indent3 . $acc['AccountName'], number_format($acc['AccountClsBal'], 2), '', '', ''];
						}
					}
				}
				
				// Other Income Drill-down
				foreach ($OtherIncome->nestedData as $grp) {
					$rows[] = ['', '', '', $indent1 . $grp['Group1Name'], number_format($grp['Group1ClsBal'], 2)];
					foreach ($grp['SubGroups2'] as $subgrp) {
						$rows[] = ['', '', '', $indent2 . $subgrp['SubGroupName'], number_format($subgrp['Group2ClsBal'], 2)];
						foreach ($subgrp['Accounts'] as $acc) {
							$rows[] = ['', '', '', $indent3 . $acc['AccountName'], number_format($acc['AccountClsBal'], 2)];
						}
					}
				}
				
				$rows[] = ['Net Profit', number_format($NetProfit, 2), '', '', ''];
				$rows[] = ['Total', number_format($AllTotalLeft, 2), '', 'Total', number_format($AllTotalRight, 2)];
				
				// Write all rows
				foreach ($rows as $row) {
					// LEFT column bolding
					if (!empty($row[0])) {
						if (strpos($row[0], $indent1) === 0 && strpos($row[0], $indent2) === false) {
							$left_style = $style_bold; // SubGroup
							} elseif (strpos($row[0], $indent1) === false) {
							$left_style = $style_bold; // MainGroup
							} else {
							$left_style = []; // Account
						}
						} else {
						$left_style = [];
					}
					
					// RIGHT column bolding
					if (!empty($row[3])) {
						if (strpos($row[3], $indent1) === 0 && strpos($row[3], $indent2) === false) {
							$right_style = $style_bold; // SubGroup
							} elseif (strpos($row[3], $indent1) === false) {
							$right_style = $style_bold; // MainGroup
							} else {
							$right_style = []; // Account
						}
						} else {
						$right_style = [];
					}
					
					$writer->writeSheetRow($sheet, $row, [
					$left_style, $style_amount, '', $right_style, $style_amount
					]);
				}
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
				foreach ($files as $file) {
					if (is_file($file)) unlink($file);
				}
				
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
				foreach ($files as $file) {
					if (is_file($file)) unlink($file);
				}
				
				$filename = 'ProfitLossTFormat_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		
		private function buildDrilldownRows($groupData)
		{
			$rows = [];
			$nbsp = "\u{00A0}"; // Unicode non-breaking space for indentation
			
			$rows[] = [strtoupper($groupData['MainGroup']), ''];
			
			foreach ($groupData['SubGroups1'] as $sub1) {
				$indent1 = str_repeat($nbsp, 4);
				$rows[] = [$indent1 . strtoupper($sub1['SubGroup1Name']), number_format($sub1['Group1ClsBal'], 2)];
				
				foreach ($sub1['SubGroups'] as $sub2) {
					$indent2 = str_repeat($nbsp, 8);
					$rows[] = [$indent2 . strtoupper($sub2['SubGroupName']), number_format($sub2['Group2ClsBal'], 2)];
					
					foreach ($sub2['Accounts'] as $account) {
						$indent3 = str_repeat($nbsp, 12);
						$rows[] = [$indent3 . strtoupper($account['AccountName']), number_format($account['AccountClsBal'], 2)];
					}
				}
			}
			
			return $rows;
		}
		
		public function prepareProfitLossDrilldownData($todate)
		{
			$this->load->model('accounting_model');
			
			$fy = $this->session->userdata('finacial_year');
			$last_fy = $fy - 1;
			
			// Fetch all required data
			$OpeningInventoryAmt = $this->accounting_model->GetOpeningInventoryAmt($todate);
			$ClosingInventoryAmt = $this->accounting_model->GetClosingInventoryAmt($todate);
			$TransactionAmt = $this->accounting_model->GetTransactionAmt($todate);
			$DirectExp = $this->accounting_model->GetDirectExpenses($todate);
			$EMPBenData = $this->accounting_model->GetEMPBen($todate);
			$FinanceCostData = $this->accounting_model->GetFinanceCostData($todate);
			$DeprecData = $this->accounting_model->GetDeprecAmortData($todate);
			$OtherExpensesData = $this->accounting_model->GetOtherExpensesData($todate);
			$OtherIncome = $this->accounting_model->GetOtherIncome($todate);
			$TaxExpense = $this->accounting_model->GetTaxExpense($todate);
			
			// Prepare Expenses Side (Left)
			$left = [
			'MainGroup' => 'Expenses',
			'MainGroupClsBal' => 0,
			'SubGroups1' => []
			];
			
			$expenseItems = [
			['Opening Stock', $OpeningInventoryAmt->CurrentYear],
			['Purchases', $TransactionAmt->PurchCurrentYear],
			['Direct Expenses', $DirectExp->CurrentYear],
			['Employee Benefits', $EMPBenData->CurrentYear],
			['Finance Costs', $FinanceCostData->CurrentYear],
			['Depreciation & Amortization', $DeprecData->CurrentYear],
			['Other Expenses', $OtherExpensesData->CurrentYear],
			['Tax Expense', $TaxExpense->CurrentYear],
			];
			
			foreach ($expenseItems as $item) {
				$subGroup1 = [
				'SubGroup1Name' => $item[0],
				'Group1ClsBal' => $item[1],
				'SubGroups' => [
                [
				'SubGroupName' => $item[0],
				'Group2ClsBal' => $item[1],
				'Accounts' => [
				[
				'AccountName' => $item[0],
				'AccountClsBal' => $item[1],
				'AccountClsBalPre' => 0
				]
				]
                ]
				]
				];
				$left['SubGroups1'][] = $subGroup1;
				$left['MainGroupClsBal'] += $item[1];
			}
			
			// Prepare Income Side (Right)
			$right = [
			'MainGroup' => 'Income',
			'MainGroupClsBal' => 0,
			'SubGroups1' => []
			];
			
			$TotalSale = $TransactionAmt->SaleCurrentYear;
			$TotalSaleRtn = $TransactionAmt->FrtRtnCurrentYear + $TransactionAmt->DFrtRtnCurrentYear;
			$NetSaleIncome = $TotalSale - $TotalSaleRtn;
			
			$incomeItems = [
			['Sales', $TotalSale],
			['Sales Return (-)', -$TotalSaleRtn],
			['Net Sales', $NetSaleIncome],
			['Closing Stock', $ClosingInventoryAmt->CurrentYear],
			['Other Income', $OtherIncome->CurrentYear],
			];
			
			foreach ($incomeItems as $item) {
				$subGroup1 = [
				'SubGroup1Name' => $item[0],
				'Group1ClsBal' => $item[1],
				'SubGroups' => [
                [
				'SubGroupName' => $item[0],
				'Group2ClsBal' => $item[1],
				'Accounts' => [
				[
				'AccountName' => $item[0],
				'AccountClsBal' => $item[1],
				'AccountClsBalPre' => 0
				]
				]
                ]
				]
				];
				$right['SubGroups1'][] = $subGroup1;
				$right['MainGroupClsBal'] += $item[1];
			}
			
			return [
			'nestedData' => [$left, $right]
			];
		}
		
		//=================== Profit loss report =======================================
		public function profitlossreport($from_date = "",$date = "")
		{
			if (!has_permission_new('profitlossreport', '', 'view')) {
				access_denied('access denied');
			}
			
			$fy = $this->session->userdata('finacial_year');
			
			if(!empty($from_date)){
				$fromdate = $from_date." 00:00:00";
				}else{
				$fromdate = '20'.$fy.date('-04-01 H:i:s');
			}
			if(!empty($date)){
				$todate = $date." 23:59:59";
				}else{
				$todate = date('Y-m-d H:i:s');
			}
			$this->load->model('accounts_master_model');
			$data['title'] = "Proft Loss Report";
			$data['company_detail'] = $this->misc_reports_model->get_company_detail();
			
			// I. Revenue from Operation
			// Other Income
			$OtherIncome = $this->accounting_model->GetOtherIncome($fromdate,$todate);
			$data['OtherIncome'] = $OtherIncome;
			// II. Other Income
			$OtherIncome;
			$OtherIncome->isPrimary = 'Y';
			
			// III. Total Revenue (I + II)
			
			// IV. Expenses
			
			// Direct Expenses
			$DirectExp = $this->accounting_model->GetDirectExpenses($fromdate,$todate);
			$data['DirectExp'] = $DirectExp;
			
			// Inventory
			$TotalOpnQtyAmt = 0;
			$OpeningInventoryAmt = $this->accounting_model->GetOpeningInventoryAmt($fromdate,$todate);
			$data['OpeningInventoryAmt'] = $OpeningInventoryAmt;
			/**/
			$ClosingInventoryAmt = $this->accounting_model->GetClosingInventoryAmt($fromdate,$todate);
			$data['ClosingInventoryAmt'] = $ClosingInventoryAmt;
			/**/
			$TransactionAmt = $this->accounting_model->GetTransactionAmt($fromdate,$todate);
			$data['TransactionAmt'] = $TransactionAmt;
			
			// Employee benefits expense
			$EMPBenData = $this->accounting_model->GetEMPBen($fromdate,$todate);
			$data['EMPBenData'] = $EMPBenData;
			// Finance Costs
			$FinanceCostData = $this->accounting_model->GetFinanceCostData($fromdate,$todate);
			$data['FinanceCostData'] = $FinanceCostData;
			// Depreciation And Amortization Expense
			$DeprecData = $this->accounting_model->GetDeprecAmortData($fromdate,$todate);
			$data['DeprecData'] = $DeprecData;
			// Indirect Expenses
			$OtherExpensesData = $this->accounting_model->GetOtherExpensesData($fromdate,$todate);
			$data['OtherExpensesData'] = $OtherExpensesData;
			
			// TAX Expense
			$TaxExpense = $this->accounting_model->GetTaxExpense($fromdate,$todate);
			$data['TaxExpense'] = $TaxExpense;
			if(!empty($date)){
				$formatted_date = date('d/m/Y', strtotime($date));
				$data['Date'] = $formatted_date;
			}
			if(!empty($from_date)){
				$formatted_date2 = date('d/m/Y', strtotime($from_date));
				$data['FromDate'] = $formatted_date2;
			}
			$this->load->view('AccountStatements/profitloss_report', $data);
			
		}
		//======================= Balance Sheet ========================================
		//Balance sheet code
		public function balancesheet()
		{
			if (!has_permission_new('balancesheet', '', 'view')) {
				access_denied('access_denied');
			}
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$last_fy = $fy - 1;
			$data['company_detail'] = $this->misc_reports_model->get_company_detail();
			$data['title'] = "Balance Sheet"; 
			$finalArray = [];
			$BalanceSheet_head['MainGroup'] = array("10000","10035");
			$ActMainGroup = $this->accounting_model->fetchAccountsData($BalanceSheet_head);
			$ActSubGroup1 = $this->accounting_model->GetActSubGroup1ByMainGroup($BalanceSheet_head);
			$ActSubGroup2 = $this->accounting_model->GetActSubGroup2ByMainGroup($BalanceSheet_head);
			$AccountList = $this->accounting_model->GetAccountListByMainGroup($BalanceSheet_head);
			$StaffList = $this->accounting_model->GetStaffList($BalanceSheet_head);
			$ledger_data = $this->accounting_model->GetLedgerData($BalanceSheet_head);
			$staffledger_data = $this->accounting_model->GetStaffLedgerData($BalanceSheet_head);
			$opn_data = $this->accounting_model->GetOpnBalData($BalanceSheet_head);
			// Calculate Profit/ Loss Amount
			$TransactionAmt = $this->accounting_model->GetTransactionAmt();
			$TotalRevenueIncome = 0;
			$TotalRevenueIncomePre = 0;
			$TotalSale = $TransactionAmt->SaleCurrentYear;
			$TotalSalePre = $TransactionAmt->SalePriviousYear;
			$TotalSaleRtn = ($TransactionAmt->FrtRtnCurrentYear + $TransactionAmt->DFrtRtnCurrentYear);
			$TotalSaleRtnPre = ($TransactionAmt->FrtRtnPriviousYear + $TransactionAmt->DFrtRtnPriviousYear);
			$NetSaleIncome = $TotalSale - $TotalSaleRtn;
			$NetSaleIncomePre = $TotalSalePre - $TotalSaleRtnPre;
			$TotalRevenueIncome += $NetSaleIncome;
			$TotalRevenueIncomePre += $NetSaleIncomePre;
			$OtherIncome = $this->accounting_model->GetOtherIncome();
			$TotalRevenueIncome += $OtherIncome->CurrentYear;
			$TotalRevenueIncomePre += $OtherIncome->PriviousYear;
			
			$OpeningInventoryAmt = $this->accounting_model->GetOpeningInventoryAmt();
			$DirectExp = $this->accounting_model->GetDirectExpenses();
			$ClosingInventoryAmt = $this->accounting_model->GetClosingInventoryAmt();
			
			$COGS =  $OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear - $ClosingInventoryAmt->CurrentYear;
			$COGSPre = $OpeningInventoryAmt->PriviousYear + $TransactionAmt->PurchPriviousYear + $DirectExp->PriviousYear - $ClosingInventoryAmt->PriviousYear;
			
			// Employee benefits expense
			$EMPBenData = $this->accounting_model->GetEMPBen();
			// Finance Costs
			$FinanceCostData = $this->accounting_model->GetFinanceCostData();
			// Depreciation And Amortization Expense
			$DeprecData = $this->accounting_model->GetDeprecAmortData();
			// Indirect Expenses
			$OtherExpensesData = $this->accounting_model->GetOtherExpensesData();
			
			$TotalExp = $COGS + $EMPBenData->CurrentYear + $FinanceCostData->CurrentYear + $DeprecData->CurrentYear + $OtherExpensesData->CurrentYear;
			$TotalExpPre = $COGSPre + $EMPBenData->PriviousYear + $FinanceCostData->PriviousYear + $DeprecData->PriviousYear + $OtherExpensesData->PriviousYear;
			$ProfitLossBeforeTax = $TotalRevenueIncome - $TotalExp;
			$ProfitLossBeforeTaxPre = $TotalRevenueIncomePre - $TotalExpPre;
			// TAX Expense
			$TaxExpense = $this->accounting_model->GetTaxExpense();
			$TotalTaxExpenses = $TaxExpense->CurrentYear;
			$TotalTaxExpensesPre = $TaxExpense->PriviousYear;
			
			$NetProfitLoss = $ProfitLossBeforeTax - $TotalTaxExpenses;
			$NetProfitLossPre = $ProfitLossBeforeTaxPre - $TotalTaxExpensesPre;
			
			
			$PL_for_the_period->CurrentYear = $NetProfitLoss;
			$PL_for_the_period->PriviousYear = $NetProfitLossPre;
			$PL_for_the_period->isPrimary = "Y";
			
			$nestedData = [];
			$i = 1;
			foreach ($ActMainGroup as $mainGroup) {
				$ClsBalMainGrpWise = 0;
				$ClsBalMainGrpWisePre = 0;
				$mainGroupData = [
				'MainGroup' => $mainGroup['ActGroupName'],
				];
				foreach ($ActSubGroup1 as $ActsubGrp1) {
					if($mainGroup["ActGroupID"] == $ActsubGrp1["ActGroupID"]){
						$ClsBalSubGrp1Wise = 0;
						$ClsBalSubGrp1WisePre = 0;
						
						$subGroupData1 = [
						'SubGroup1Name' => $ActsubGrp1['SubActGroupName'],
						'SubGroup1' => $ActsubGrp1['SubActGroupID1'],
						];
						foreach ($ActSubGroup2 as $ActsubGrp2) {
							if($ActsubGrp1["SubActGroupID1"]==$ActsubGrp2["SubActGroupID1"]){
								$ClsBalSubGrp2Wise = 0;
								$ClsBalSubGrp2WisePre = 0;
								
								$subGroupData = [
								'SubGroupName' => $ActsubGrp2['SubActGroupName'],
								'SubActGroupID' => $ActsubGrp2['SubActGroupID'],
								];
								// From Client Table
								foreach($AccountList as $ActList){
									if($ActList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
										$ClsBalAccountWise = 0;
										$ClsBalAccountWisePre = 0;
										$Act_opn = 0;
										$ActCr = 0;
										$ActDr = 0;
										$Act_opnPre = 0;
										$ActCrPre = 0;
										$ActDrPre = 0;
										// opening balances for current year
										foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
											if ($Val45["AccountID"] == $ActList["AccountID"] && $Val45["FY"] == $fy) {
												if($i == 1){
													$Act_opn = $Val45["SUMAmt"];
													}else{
													$Act_opn = $Val45["SUMAmt"];
												}
											}
										}
										
										// opening balances for privious year
										foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
											if ($Val455["AccountID"] == $ActList["AccountID"] && $Val455["FY"] == $last_fy) {
												if($i == 1){
													$Act_opnPre = $Val455["SUMAmt"];
													}else{
													$Act_opnPre = $Val455["SUMAmt"];
												}
											}
										}
										
										// transaction data for current year
										foreach ($ledger_data->Cur_yr_ledger as $Key44 => $val44) {
											if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
												$ActCr = $val44["SUMAmt"];
											}
											if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
												$ActDr = $val44["SUMAmt"];
											}
										}
										// transaction data for privious year
										foreach ($ledger_data->Last_yr_ledger as $Key444 => $val444) {
											if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
												$ActCrPre = $val444["SUMAmt"];
											}
											if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
												$ActDrPre = $val444["SUMAmt"];
											}
										}
										
										if($Act_opn > 0){
											$ActDr += abs($Act_opn);
											}else{
											$ActCr += abs($Act_opn);
										}
										if($Act_opnPre > 0){
											$ActDrPre += abs($Act_opnPre);
											}else{
											$ActCrPre += abs($Act_opnPre);
										}
										if($i>1){
											$ClsBalAccountWise =   $ActDr - $ActCr;
											$ClsBalAccountWisePre =   $ActDrPre - $ActCrPre;
											}else{
											$ClsBalAccountWise =   $ActCr - $ActDr;
											$ClsBalAccountWisePre =  $ActCrPre - $ActDrPre;
										}
										if($ActList["AccountID"] == "L01452"){
											$ClsBalAccountWise += $PL_for_the_period->CurrentYear + $PL_for_the_period->PriviousYear;
											//$ClsBalAccountWise += $PL_for_the_period->CurrentYear;
											$ClsBalAccountWisePre += $PL_for_the_period->PriviousYear;
											$Act_opn = 0;
											$Act_opnPre = 0;
										}
										if($ActList["AccountID"] == "L001457"){
											$ClsBalAccountWise += $ClosingInventoryAmt->CurrentYear;
											$ClsBalAccountWisePre += $ClosingInventoryAmt->PriviousYear;
											$Act_opn = 0;
											$Act_opnPre = 0;
										}
										$ClsBalSubGrp2Wise += $ClsBalAccountWise;
										$ClsBalSubGrp2WisePre += $ClsBalAccountWisePre;
										
										if($ClsBalAccountWise == "0" && $ClsBalAccountWisePre == "0" ){
											
											}else{
											$AccountData = [
											'AccountName' => $ActList['company'],
											'AccountID' => $ActList['AccountID'],
											'AccountClsBal' =>$ClsBalAccountWise,
											'AccountClsBalPre' =>$ClsBalAccountWisePre,
											];
											$subGroupData['Accounts'][] = $AccountData;
										}
									}
								}// Client table  record end
								
								
								// From Staff table
								foreach($StaffList as $staffList){
									if($staffList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
										$ClsBalAccountWise = 0;
										$ClsBalAccountWisePre = 0;
										$Act_opn = 0;
										$ActCr = 0;
										$ActDr = 0;
										$Act_opnPre = 0;
										$ActCrPre = 0;
										$ActDrPre = 0;
										// opening balances for current year
										foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
											if ($Val45["AccountID"] == $staffList["AccountID"] && $Val45["FY"] == $fy) {
												$Act_opn = $Val45["SUMAmt"];
											}
										}
										
										// opening balances for privious year
										foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
											if ($Val455["AccountID"] == $staffList["AccountID"] && $Val455["FY"] == $last_fy) {
												$Act_opnPre = $Val455["SUMAmt"];
											}
										}
										// transaction data for current year
										foreach ($staffledger_data->Cur_yr_ledger as $Key44 => $val44) {
											if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
												$ActCr = $val44["SUMAmt"];
											}
											if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
												$ActDr = $val44["SUMAmt"];
											}
										}
										// transaction data for privious year
										foreach ($staffledger_data->Last_yr_ledger as $Key444 => $val444) {
											if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
												$ActCrPre = $val444["SUMAmt"];
											}
											if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
												$ActDrPre = $val444["SUMAmt"];
											}
										}
										if($Act_opn > 0){
											$ActDr += abs($Act_opn);
											}else{
											$ActCr += abs($Act_opn);
										}
										if($Act_opnPre > 0){
											$ActDrPre += abs($Act_opnPre);
											}else{
											$ActCrPre += abs($Act_opnPre);
										}
										if($i>1){
											$ClsBalAccountWise = $ActDr - $ActCr;
											$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
											}else{
											$ClsBalAccountWise =  $ActCr - $ActDr;
											$ClsBalAccountWisePre =  $ActCrPre - $ActDrPre;
										}
										
										$ClsBalSubGrp2Wise += $ClsBalAccountWise;
										$ClsBalSubGrp2WisePre += $ClsBalAccountWisePre;
										
										if($ClsBalAccountWise == "0" && $ClsBalAccountWisePre == "0"){
											
											}else{
											$AccountData = [
											'AccountName' => $staffList['firstname'].' '.$staffList['lastname'],
											'AccountID' => $staffList['AccountID'],
											'AccountClsBal' =>$ClsBalAccountWise,
											'AccountClsBalPre' =>$ClsBalAccountWisePre,
											];
											$subGroupData['Accounts'][] = $AccountData;
										}
									}
								}
								
								if($ActsubGrp2['SubActGroupID'] == "1000015"){
									$ClsBalSubGrp2Wise += $CurrentInventoryValue["AllInventoryAmt"];
									$ClsBalSubGrp2WisePre += $CurrentInventoryValue["AllInventoryAmtPre"];
								}
								$subGroupData['Group2ClsBal'] = $ClsBalSubGrp2Wise;
								$subGroupData['Group2ClsBalPre'] = $ClsBalSubGrp2WisePre;
								
								$ClsBalSubGrp1Wise += $ClsBalSubGrp2Wise;
								$ClsBalSubGrp1WisePre += $ClsBalSubGrp2WisePre;
								
								$subGroupData1['SubGroups'][] = $subGroupData;
							}
						}
						$subGroupData1['Group1ClsBal'] = $ClsBalSubGrp1Wise;
						$subGroupData1['Group1ClsBalPre'] = $ClsBalSubGrp1WisePre;
						
						$ClsBalMainGrpWise += $ClsBalSubGrp1Wise;
						$ClsBalMainGrpWisePre += $ClsBalSubGrp1WisePre;
						
						$mainGroupData['SubGroups1'][] = $subGroupData1;
					}
				}
				$mainGroupData['MainGroupClsBal'] = $ClsBalMainGrpWise;
				$mainGroupData['MainGroupClsBalPre'] = $ClsBalMainGrpWisePre;
				$nestedData[] = $mainGroupData;
				$i++;
			}
			$data['nestedData'] = $nestedData;
			$data['FixedAssets'] = $this->accounting_model->GetFixedAssetsLedger();
			
			$this->load->view('AccountStatements/balancesheet', $data);
		}
		
		//======================= Balance Sheet ========================================
		//Balance sheet code
		public function TFormatBalanceSheet($date = "")
		{
			if (!has_permission_new('TFormatBalanceSheet', '', 'view')) {
				access_denied('access_denied');
			}
			
			if(!empty($date)){
				$todate = $date." 23:59:59";
				}else{
				$todate = date('Y-m-d H:i:s');
			}
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$last_fy = $fy - 1;
			$data['company_detail'] = $this->misc_reports_model->get_company_detail();
			$data['title'] = "Balance Sheet";
			if(!empty($date)){
				$finalArray = [];
				$BalanceSheet_head['MainGroup'] = array("10000","10035");
				$ActMainGroup = $this->accounting_model->fetchAccountsData($BalanceSheet_head);
				$ActSubGroup1 = $this->accounting_model->GetActSubGroup1ByMainGroup($BalanceSheet_head);
				$ActSubGroup2 = $this->accounting_model->GetActSubGroup2ByMainGroup($BalanceSheet_head);
				$AccountList = $this->accounting_model->GetAccountListByMainGroup($BalanceSheet_head);
				$StaffList = $this->accounting_model->GetStaffList($BalanceSheet_head);
				$ledger_data = $this->accounting_model->GetLedgerData($BalanceSheet_head,$todate);
				$staffledger_data = $this->accounting_model->GetStaffLedgerData($BalanceSheet_head,$todate);
				$opn_data = $this->accounting_model->GetOpnBalData($BalanceSheet_head);
				// Calculate Profit/ Loss Amount
				$TransactionAmt = $this->accounting_model->GetTransactionAmt("",$todate);
				$TotalRevenueIncome = 0;
				$TotalRevenueIncomePre = 0;
				$TotalSale = $TransactionAmt->SaleCurrentYear;
				$TotalSalePre = $TransactionAmt->SalePriviousYear;
				$TotalSaleRtn = ($TransactionAmt->FrtRtnCurrentYear + $TransactionAmt->DFrtRtnCurrentYear);
				$TotalSaleRtnPre = ($TransactionAmt->FrtRtnPriviousYear + $TransactionAmt->DFrtRtnPriviousYear);
				$NetSaleIncome = $TotalSale - $TotalSaleRtn;
				$NetSaleIncomePre = $TotalSalePre - $TotalSaleRtnPre;
				$TotalRevenueIncome += $NetSaleIncome;
				$TotalRevenueIncomePre += $NetSaleIncomePre;
				$OtherIncome = $this->accounting_model->GetOtherIncome($todate);
				$TotalRevenueIncome += $OtherIncome->CurrentYear;
				$TotalRevenueIncomePre += $OtherIncome->PriviousYear;
				
				$OpeningInventoryAmt = $this->accounting_model->GetOpeningInventoryAmt($todate);
				$DirectExp = $this->accounting_model->GetDirectExpenses($todate);
				$ClosingInventoryAmt = $this->accounting_model->GetClosingInventoryAmt($todate);
				
				$COGS =  $OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear - $ClosingInventoryAmt->CurrentYear;
				$COGSPre = $OpeningInventoryAmt->PriviousYear + $TransactionAmt->PurchPriviousYear + $DirectExp->PriviousYear - $ClosingInventoryAmt->PriviousYear;
				
				// Employee benefits expense
				$EMPBenData = $this->accounting_model->GetEMPBen($todate);
				// Finance Costs
				$FinanceCostData = $this->accounting_model->GetFinanceCostData($todate);
				// Depreciation And Amortization Expense
				$DeprecData = $this->accounting_model->GetDeprecAmortData($todate);
				// Indirect Expenses
				$OtherExpensesData = $this->accounting_model->GetOtherExpensesData($todate);
				// echo "<pre>";print_r($OtherExpensesData);die;
				
				$TotalExp = $COGS + $EMPBenData->CurrentYear + $FinanceCostData->CurrentYear + $DeprecData->CurrentYear + $OtherExpensesData->CurrentYear;
				$TotalExpPre = $COGSPre + $EMPBenData->PriviousYear + $FinanceCostData->PriviousYear + $DeprecData->PriviousYear + $OtherExpensesData->PriviousYear;
				$ProfitLossBeforeTax = $TotalRevenueIncome - $TotalExp;
				$ProfitLossBeforeTaxPre = $TotalRevenueIncomePre - $TotalExpPre;
				// TAX Expense
				$TaxExpense = $this->accounting_model->GetTaxExpense($todate);
				$TotalTaxExpenses = $TaxExpense->CurrentYear;
				$TotalTaxExpensesPre = $TaxExpense->PriviousYear;
				
				$NetProfitLoss = $ProfitLossBeforeTax - $TotalTaxExpenses;
				$NetProfitLossPre = $ProfitLossBeforeTaxPre - $TotalTaxExpensesPre;
				
				
				$PL_for_the_period->CurrentYear = $NetProfitLoss;
				$PL_for_the_period->PriviousYear = $NetProfitLossPre;
				$PL_for_the_period->isPrimary = "Y";
				
				$nestedData = [];
				$i = 1;
				foreach ($ActMainGroup as $mainGroup) {
					$ClsBalMainGrpWise = 0;
					$ClsBalMainGrpWisePre = 0;
					$mainGroupData = [
					'MainGroup' => $mainGroup['ActGroupName'],
					];
					foreach ($ActSubGroup1 as $ActsubGrp1) {
						if($mainGroup["ActGroupID"] == $ActsubGrp1["ActGroupID"]){
							$ClsBalSubGrp1Wise = 0;
							$ClsBalSubGrp1WisePre = 0;
							
							$subGroupData1 = [
							'SubGroup1Name' => $ActsubGrp1['SubActGroupName'],
							'SubGroup1' => $ActsubGrp1['SubActGroupID1'],
							];
							foreach ($ActSubGroup2 as $ActsubGrp2) {
								if($ActsubGrp1["SubActGroupID1"]==$ActsubGrp2["SubActGroupID1"]){
									$ClsBalSubGrp2Wise = 0;
									$ClsBalSubGrp2WisePre = 0;
									
									$subGroupData = [
									'SubGroupName' => $ActsubGrp2['SubActGroupName'],
									'SubActGroupID' => $ActsubGrp2['SubActGroupID'],
									];
									// From Client Table
									foreach($AccountList as $ActList){
										if($ActList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
											$ClsBalAccountWise = 0;
											$ClsBalAccountWisePre = 0;
											$Act_opn = 0;
											$ActCr = 0;
											$ActDr = 0;
											$Act_opnPre = 0;
											$ActCrPre = 0;
											$ActDrPre = 0;
											// opening balances for current year
											foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
												if ($Val45["AccountID"] == $ActList["AccountID"] && $Val45["FY"] == $fy) {
													if($i == 1){
														$Act_opn = $Val45["SUMAmt"];
														}else{
														$Act_opn = $Val45["SUMAmt"];
													}
												}
											}
											
											// opening balances for privious year
											foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
												if ($Val455["AccountID"] == $ActList["AccountID"] && $Val455["FY"] == $last_fy) {
													if($i == 1){
														$Act_opnPre = $Val455["SUMAmt"];
														}else{
														$Act_opnPre = $Val455["SUMAmt"];
													}
												}
											}
											
											// transaction data for current year
											foreach ($ledger_data->Cur_yr_ledger as $Key44 => $val44) {
												if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
													$ActCr = $val44["SUMAmt"];
												}
												if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
													$ActDr = $val44["SUMAmt"];
												}
											}
											// transaction data for privious year
											foreach ($ledger_data->Last_yr_ledger as $Key444 => $val444) {
												if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
													$ActCrPre = $val444["SUMAmt"];
												}
												if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
													$ActDrPre = $val444["SUMAmt"];
												}
											}
											
											if($Act_opn > 0){
												$ActDr += abs($Act_opn);
												}else{
												$ActCr += abs($Act_opn);
											}
											if($Act_opnPre > 0){
												$ActDrPre += abs($Act_opnPre);
												}else{
												$ActCrPre += abs($Act_opnPre);
											}
											if($i>1){
												$ClsBalAccountWise =   $ActDr - $ActCr;
												$ClsBalAccountWisePre =   $ActDrPre - $ActCrPre;
												}else{
												$ClsBalAccountWise =   $ActCr - $ActDr;
												$ClsBalAccountWisePre =  $ActCrPre - $ActDrPre;
											}
											if($ActList["AccountID"] == "L01452"){
												$ClsBalAccountWise += $PL_for_the_period->CurrentYear + $PL_for_the_period->PriviousYear;
												//$ClsBalAccountWise += $PL_for_the_period->CurrentYear;
												$ClsBalAccountWisePre += $PL_for_the_period->PriviousYear;
												$Act_opn = 0;
												$Act_opnPre = 0;
											}
											if($ActList["AccountID"] == "L001457"){
												$ClsBalAccountWise += $ClosingInventoryAmt->CurrentYear;
												$ClsBalAccountWisePre += $ClosingInventoryAmt->PriviousYear;
												$Act_opn = 0;
												$Act_opnPre = 0;
											}
											$ClsBalSubGrp2Wise += $ClsBalAccountWise;
											$ClsBalSubGrp2WisePre += $ClsBalAccountWisePre;
											
											if($ClsBalAccountWise == "0" && $ClsBalAccountWisePre == "0" ){
												
												}else{
												$AccountData = [
												'AccountName' => $ActList['company'],
												'AccountID' => $ActList['AccountID'],
												'AccountClsBal' =>$ClsBalAccountWise,
												'AccountClsBalPre' =>$ClsBalAccountWisePre,
												];
												$subGroupData['Accounts'][] = $AccountData;
											}
										}
									}// Client table  record end
									
									
									// From Staff table
									foreach($StaffList as $staffList){
										if($staffList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
											$ClsBalAccountWise = 0;
											$ClsBalAccountWisePre = 0;
											$Act_opn = 0;
											$ActCr = 0;
											$ActDr = 0;
											$Act_opnPre = 0;
											$ActCrPre = 0;
											$ActDrPre = 0;
											// opening balances for current year
											foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
												if ($Val45["AccountID"] == $staffList["AccountID"] && $Val45["FY"] == $fy) {
													$Act_opn = $Val45["SUMAmt"];
												}
											}
											
											// opening balances for privious year
											foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
												if ($Val455["AccountID"] == $staffList["AccountID"] && $Val455["FY"] == $last_fy) {
													$Act_opnPre = $Val455["SUMAmt"];
												}
											}
											// transaction data for current year
											foreach ($staffledger_data->Cur_yr_ledger as $Key44 => $val44) {
												if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
													$ActCr = $val44["SUMAmt"];
												}
												if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
													$ActDr = $val44["SUMAmt"];
												}
											}
											// transaction data for privious year
											foreach ($staffledger_data->Last_yr_ledger as $Key444 => $val444) {
												if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
													$ActCrPre = $val444["SUMAmt"];
												}
												if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
													$ActDrPre = $val444["SUMAmt"];
												}
											}
											if($Act_opn > 0){
												$ActDr += abs($Act_opn);
												}else{
												$ActCr += abs($Act_opn);
											}
											if($Act_opnPre > 0){
												$ActDrPre += abs($Act_opnPre);
												}else{
												$ActCrPre += abs($Act_opnPre);
											}
											if($i>1){
												$ClsBalAccountWise = $ActDr - $ActCr;
												$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
												}else{
												$ClsBalAccountWise =  $ActCr - $ActDr;
												$ClsBalAccountWisePre =  $ActCrPre - $ActDrPre;
											}
											
											$ClsBalSubGrp2Wise += $ClsBalAccountWise;
											$ClsBalSubGrp2WisePre += $ClsBalAccountWisePre;
											
											if($ClsBalAccountWise == "0" && $ClsBalAccountWisePre == "0"){
												
												}else{
												$AccountData = [
												'AccountName' => $staffList['firstname'].' '.$staffList['lastname'],
												'AccountID' => $staffList['AccountID'],
												'AccountClsBal' =>$ClsBalAccountWise,
												'AccountClsBalPre' =>$ClsBalAccountWisePre,
												];
												$subGroupData['Accounts'][] = $AccountData;
											}
										}
									}
									
									if($ActsubGrp2['SubActGroupID'] == "1000015"){
										$ClsBalSubGrp2Wise += $CurrentInventoryValue["AllInventoryAmt"];
										$ClsBalSubGrp2WisePre += $CurrentInventoryValue["AllInventoryAmtPre"];
									}
									$subGroupData['Group2ClsBal'] = $ClsBalSubGrp2Wise;
									$subGroupData['Group2ClsBalPre'] = $ClsBalSubGrp2WisePre;
									
									$ClsBalSubGrp1Wise += $ClsBalSubGrp2Wise;
									$ClsBalSubGrp1WisePre += $ClsBalSubGrp2WisePre;
									
									$subGroupData1['SubGroups'][] = $subGroupData;
								}
							}
							$subGroupData1['Group1ClsBal'] = $ClsBalSubGrp1Wise;
							$subGroupData1['Group1ClsBalPre'] = $ClsBalSubGrp1WisePre;
							
							$ClsBalMainGrpWise += $ClsBalSubGrp1Wise;
							$ClsBalMainGrpWisePre += $ClsBalSubGrp1WisePre;
							
							$mainGroupData['SubGroups1'][] = $subGroupData1;
						}
					}
					$mainGroupData['MainGroupClsBal'] = $ClsBalMainGrpWise;
					$mainGroupData['MainGroupClsBalPre'] = $ClsBalMainGrpWisePre;
					$nestedData[] = $mainGroupData;
					$i++;
				}
				$data['nestedData'] = $nestedData;
				
				$data['FixedAssets'] = $this->accounting_model->GetFixedAssetsLedger();
			}
			if(!empty($date)){
				$formatted_date = date('d/m/Y', strtotime($date));
				$data['Date'] = $formatted_date;
			}
			
			//echo "<pre>";print_r($nestedData);die;
			$this->load->view('AccountStatements/TFormatBalanceSheet', $data);
		}
		public function export_TFormatBalanceSheet()
		{
			if (!has_permission_new('TFormatBalanceSheet', '', 'export')) {
				access_denied('access_denied');
			}
			
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if ($this->input->post()) {
				$date = $this->input->post('as_on_date');
				$todate = !empty($date) ? to_sql_date($date) . " 23:59:59" : date('Y-m-d H:i:s');
				// echo $todate;die;
				$as_on_date = !empty($date) ? date('d/m/Y', strtotime($date)) : date('d/m/Y');
				
				$this->load->model('sale_reports_model');
				$this->load->model('misc_reports_model');
				$company_details = $this->misc_reports_model->get_company_detail();
				
				$data = $this->prepareBalanceSheetData($todate);
				$nestedData = $data['nestedData'];
				
				$writer = new XLSXWriter();
				$sheet = 'Sheet1';
				
				$writer->writeSheetRow($sheet, [$company_details->company_name], ['font-style' => 'bold', 'font-size' => 14]);
				$writer->writeSheetRow($sheet, [$company_details->address], ['font-size' => 12]);
				$writer->writeSheetRow($sheet, ['TFormat Balance Sheet as on ' . $as_on_date], ['font-style' => 'bold']);
				
				$writer->writeSheetRow($sheet, ['Particulars', 'Amount', '', 'Particulars', 'Amount'], ['font-style' => 'bold']);
				
				$side1 = $nestedData[0];
				$side2 = $nestedData[1];
				
				// echo "<pre>";print_r($nestedData);die;
				$rows1 = $this->buildNestedRows($side1);
				$rows2 = $this->buildNestedRows($side2);
				
				$max_rows = max(count($rows1), count($rows2));
				
				for ($i = 0; $i < $max_rows; $i++) {
					$row1 = isset($rows1[$i]) ? $rows1[$i] : ['', ''];
					$row2 = isset($rows2[$i]) ? $rows2[$i] : ['', ''];
					$writer->writeSheetRow($sheet, [$row1[0], $row1[1], '', $row2[0], $row2[1]]);
				}
				
				$writer->writeSheetRow($sheet, [
				'Total ' . $side1['MainGroup'], number_format($side1['MainGroupClsBal'], 2), '',
				'Total ' . $side2['MainGroup'], number_format($side2['MainGroupClsBal'], 2)
				], ['font-style' => 'bold']);
				
				
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE . '*');
				foreach ($files as $file) {
					if (is_file($file)) unlink($file);
				}
				
				$filename = 'TFormatBalanceSheet_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		
		private function buildNestedRows($groupData)
		{
			$rows = [];
			$nbsp = "\u{00A0}"; // Unicode non-breaking space
			
			$rows[] = [strtoupper($groupData['MainGroup']), ''];
			
			foreach ($groupData['SubGroups1'] as $sub1) {
				$indent1 = str_repeat($nbsp, 8);
				$rows[] = [$indent1 . strtoupper($sub1['SubGroup1Name']), number_format($sub1['Group1ClsBal'], 2)];
				// echo "<pre>";print_r($sub1);die;
				foreach ($sub1['SubGroups'] as $sub2) {
					$indent2 = str_repeat($nbsp, 16);
					$rows[] = [$indent2 . strtoupper($sub2['SubGroupName']), number_format($sub2['Group2ClsBal'], 2)];
					
					foreach ($sub2['Accounts'] as $account) {
						$indent3 = str_repeat($nbsp, 32);
						$rows[] = [$indent3 . strtoupper($account['AccountName']), number_format($account['AccountClsBal'], 2)];
					}
				}
			}
			
			return $rows;
		}
		
		
		public function prepareBalanceSheetData($todate)
		{
			// echo $todate;die;
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$last_fy = $fy - 1;
			
			$finalArray = [];
			$BalanceSheet_head['MainGroup'] = array("10000","10035");
			$ActMainGroup = $this->accounting_model->fetchAccountsData($BalanceSheet_head);
			$ActSubGroup1 = $this->accounting_model->GetActSubGroup1ByMainGroup($BalanceSheet_head);
			$ActSubGroup2 = $this->accounting_model->GetActSubGroup2ByMainGroup($BalanceSheet_head);
			$AccountList = $this->accounting_model->GetAccountListByMainGroup($BalanceSheet_head);
			$StaffList = $this->accounting_model->GetStaffList($BalanceSheet_head);
			$ledger_data = $this->accounting_model->GetLedgerData($BalanceSheet_head,$todate);
			$staffledger_data = $this->accounting_model->GetStaffLedgerData($BalanceSheet_head,$todate);
			$opn_data = $this->accounting_model->GetOpnBalData($BalanceSheet_head);
			// Calculate Profit/ Loss Amount
			$TransactionAmt = $this->accounting_model->GetTransactionAmt($todate);
			$TotalRevenueIncome = 0;
			$TotalRevenueIncomePre = 0;
			$TotalSale = $TransactionAmt->SaleCurrentYear;
			$TotalSalePre = $TransactionAmt->SalePriviousYear;
			$TotalSaleRtn = ($TransactionAmt->FrtRtnCurrentYear + $TransactionAmt->DFrtRtnCurrentYear);
			$TotalSaleRtnPre = ($TransactionAmt->FrtRtnPriviousYear + $TransactionAmt->DFrtRtnPriviousYear);
			$NetSaleIncome = $TotalSale - $TotalSaleRtn;
			$NetSaleIncomePre = $TotalSalePre - $TotalSaleRtnPre;
			$TotalRevenueIncome += $NetSaleIncome;
			$TotalRevenueIncomePre += $NetSaleIncomePre;
			$OtherIncome = $this->accounting_model->GetOtherIncome($todate);
			$TotalRevenueIncome += $OtherIncome->CurrentYear;
			$TotalRevenueIncomePre += $OtherIncome->PriviousYear;
			
			$OpeningInventoryAmt = $this->accounting_model->GetOpeningInventoryAmt($todate);
			$DirectExp = $this->accounting_model->GetDirectExpenses($todate);
			$ClosingInventoryAmt = $this->accounting_model->GetClosingInventoryAmt($todate);
			
			$COGS =  $OpeningInventoryAmt->CurrentYear + $TransactionAmt->PurchCurrentYear + $DirectExp->CurrentYear - $ClosingInventoryAmt->CurrentYear;
			$COGSPre = $OpeningInventoryAmt->PriviousYear + $TransactionAmt->PurchPriviousYear + $DirectExp->PriviousYear - $ClosingInventoryAmt->PriviousYear;
			
			// Employee benefits expense
			$EMPBenData = $this->accounting_model->GetEMPBen($todate);
			// Finance Costs
			$FinanceCostData = $this->accounting_model->GetFinanceCostData($todate);
			// Depreciation And Amortization Expense
			$DeprecData = $this->accounting_model->GetDeprecAmortData($todate);
			// Indirect Expenses
			$OtherExpensesData = $this->accounting_model->GetOtherExpensesData($todate);
			
			$TotalExp = $COGS + $EMPBenData->CurrentYear + $FinanceCostData->CurrentYear + $DeprecData->CurrentYear + $OtherExpensesData->CurrentYear;
			$TotalExpPre = $COGSPre + $EMPBenData->PriviousYear + $FinanceCostData->PriviousYear + $DeprecData->PriviousYear + $OtherExpensesData->PriviousYear;
			$ProfitLossBeforeTax = $TotalRevenueIncome - $TotalExp;
			$ProfitLossBeforeTaxPre = $TotalRevenueIncomePre - $TotalExpPre;
			// TAX Expense
			$TaxExpense = $this->accounting_model->GetTaxExpense($todate);
			$TotalTaxExpenses = $TaxExpense->CurrentYear;
			$TotalTaxExpensesPre = $TaxExpense->PriviousYear;
			
			$NetProfitLoss = $ProfitLossBeforeTax - $TotalTaxExpenses;
			$NetProfitLossPre = $ProfitLossBeforeTaxPre - $TotalTaxExpensesPre;
			
			
			$PL_for_the_period->CurrentYear = $NetProfitLoss;
			$PL_for_the_period->PriviousYear = $NetProfitLossPre;
			$PL_for_the_period->isPrimary = "Y";
			
			$nestedData = [];
			$i = 1;
			foreach ($ActMainGroup as $mainGroup) {
				$ClsBalMainGrpWise = 0;
				$ClsBalMainGrpWisePre = 0;
				$mainGroupData = [
				'MainGroup' => $mainGroup['ActGroupName'],
				];
				foreach ($ActSubGroup1 as $ActsubGrp1) {
					if($mainGroup["ActGroupID"] == $ActsubGrp1["ActGroupID"]){
						$ClsBalSubGrp1Wise = 0;
						$ClsBalSubGrp1WisePre = 0;
						
						$subGroupData1 = [
						'SubGroup1Name' => $ActsubGrp1['SubActGroupName'],
						'SubGroup1' => $ActsubGrp1['SubActGroupID1'],
						];
						foreach ($ActSubGroup2 as $ActsubGrp2) {
							if($ActsubGrp1["SubActGroupID1"]==$ActsubGrp2["SubActGroupID1"]){
								$ClsBalSubGrp2Wise = 0;
								$ClsBalSubGrp2WisePre = 0;
								
								$subGroupData = [
								'SubGroupName' => $ActsubGrp2['SubActGroupName'],
								'SubActGroupID' => $ActsubGrp2['SubActGroupID'],
								];
								// From Client Table
								foreach($AccountList as $ActList){
									if($ActList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
										$ClsBalAccountWise = 0;
										$ClsBalAccountWisePre = 0;
										$Act_opn = 0;
										$ActCr = 0;
										$ActDr = 0;
										$Act_opnPre = 0;
										$ActCrPre = 0;
										$ActDrPre = 0;
										// opening balances for current year
										foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
											if ($Val45["AccountID"] == $ActList["AccountID"] && $Val45["FY"] == $fy) {
												if($i == 1){
													$Act_opn = $Val45["SUMAmt"];
													}else{
													$Act_opn = $Val45["SUMAmt"];
												}
											}
										}
										
										// opening balances for privious year
										foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
											if ($Val455["AccountID"] == $ActList["AccountID"] && $Val455["FY"] == $last_fy) {
												if($i == 1){
													$Act_opnPre = $Val455["SUMAmt"];
													}else{
													$Act_opnPre = $Val455["SUMAmt"];
												}
											}
										}
										
										// transaction data for current year
										foreach ($ledger_data->Cur_yr_ledger as $Key44 => $val44) {
											if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
												$ActCr = $val44["SUMAmt"];
											}
											if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
												$ActDr = $val44["SUMAmt"];
											}
										}
										// transaction data for privious year
										foreach ($ledger_data->Last_yr_ledger as $Key444 => $val444) {
											if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
												$ActCrPre = $val444["SUMAmt"];
											}
											if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
												$ActDrPre = $val444["SUMAmt"];
											}
										}
										
										if($Act_opn > 0){
											$ActDr += abs($Act_opn);
											}else{
											$ActCr += abs($Act_opn);
										}
										if($Act_opnPre > 0){
											$ActDrPre += abs($Act_opnPre);
											}else{
											$ActCrPre += abs($Act_opnPre);
										}
										if($i>1){
											$ClsBalAccountWise =   $ActDr - $ActCr;
											$ClsBalAccountWisePre =   $ActDrPre - $ActCrPre;
											}else{
											$ClsBalAccountWise =   $ActCr - $ActDr;
											$ClsBalAccountWisePre =  $ActCrPre - $ActDrPre;
										}
										if($ActList["AccountID"] == "L01452"){
											$ClsBalAccountWise += $PL_for_the_period->CurrentYear + $PL_for_the_period->PriviousYear;
											//$ClsBalAccountWise += $PL_for_the_period->CurrentYear;
											$ClsBalAccountWisePre += $PL_for_the_period->PriviousYear;
											$Act_opn = 0;
											$Act_opnPre = 0;
										}
										if($ActList["AccountID"] == "L001457"){
											$ClsBalAccountWise += $ClosingInventoryAmt->CurrentYear;
											$ClsBalAccountWisePre += $ClosingInventoryAmt->PriviousYear;
											$Act_opn = 0;
											$Act_opnPre = 0;
										}
										$ClsBalSubGrp2Wise += $ClsBalAccountWise;
										$ClsBalSubGrp2WisePre += $ClsBalAccountWisePre;
										
										if($ClsBalAccountWise == "0" && $ClsBalAccountWisePre == "0" ){
											
											}else{
											$AccountData = [
											'AccountName' => $ActList['company'],
											'AccountID' => $ActList['AccountID'],
											'AccountClsBal' =>$ClsBalAccountWise,
											'AccountClsBalPre' =>$ClsBalAccountWisePre,
											];
											$subGroupData['Accounts'][] = $AccountData;
										}
									}
								}// Client table  record end
								
								
								// From Staff table
								foreach($StaffList as $staffList){
									if($staffList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
										$ClsBalAccountWise = 0;
										$ClsBalAccountWisePre = 0;
										$Act_opn = 0;
										$ActCr = 0;
										$ActDr = 0;
										$Act_opnPre = 0;
										$ActCrPre = 0;
										$ActDrPre = 0;
										// opening balances for current year
										foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
											if ($Val45["AccountID"] == $staffList["AccountID"] && $Val45["FY"] == $fy) {
												$Act_opn = $Val45["SUMAmt"];
											}
										}
										
										// opening balances for privious year
										foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
											if ($Val455["AccountID"] == $staffList["AccountID"] && $Val455["FY"] == $last_fy) {
												$Act_opnPre = $Val455["SUMAmt"];
											}
										}
										// transaction data for current year
										foreach ($staffledger_data->Cur_yr_ledger as $Key44 => $val44) {
											if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
												$ActCr = $val44["SUMAmt"];
											}
											if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
												$ActDr = $val44["SUMAmt"];
											}
										}
										// transaction data for privious year
										foreach ($staffledger_data->Last_yr_ledger as $Key444 => $val444) {
											if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
												$ActCrPre = $val444["SUMAmt"];
											}
											if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
												$ActDrPre = $val444["SUMAmt"];
											}
										}
										if($Act_opn > 0){
											$ActDr += abs($Act_opn);
											}else{
											$ActCr += abs($Act_opn);
										}
										if($Act_opnPre > 0){
											$ActDrPre += abs($Act_opnPre);
											}else{
											$ActCrPre += abs($Act_opnPre);
										}
										if($i>1){
											$ClsBalAccountWise = $ActDr - $ActCr;
											$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
											}else{
											$ClsBalAccountWise =  $ActCr - $ActDr;
											$ClsBalAccountWisePre =  $ActCrPre - $ActDrPre;
										}
										
										$ClsBalSubGrp2Wise += $ClsBalAccountWise;
										$ClsBalSubGrp2WisePre += $ClsBalAccountWisePre;
										
										if($ClsBalAccountWise == "0" && $ClsBalAccountWisePre == "0"){
											
											}else{
											$AccountData = [
											'AccountName' => $staffList['firstname'].' '.$staffList['lastname'],
											'AccountID' => $staffList['AccountID'],
											'AccountClsBal' =>$ClsBalAccountWise,
											'AccountClsBalPre' =>$ClsBalAccountWisePre,
											];
											$subGroupData['Accounts'][] = $AccountData;
										}
									}
								}
								
								if($ActsubGrp2['SubActGroupID'] == "1000015"){
									$ClsBalSubGrp2Wise += $CurrentInventoryValue["AllInventoryAmt"];
									$ClsBalSubGrp2WisePre += $CurrentInventoryValue["AllInventoryAmtPre"];
								}
								$subGroupData['Group2ClsBal'] = $ClsBalSubGrp2Wise;
								$subGroupData['Group2ClsBalPre'] = $ClsBalSubGrp2WisePre;
								
								$ClsBalSubGrp1Wise += $ClsBalSubGrp2Wise;
								$ClsBalSubGrp1WisePre += $ClsBalSubGrp2WisePre;
								
								$subGroupData1['SubGroups'][] = $subGroupData;
							}
						}
						$subGroupData1['Group1ClsBal'] = $ClsBalSubGrp1Wise;
						$subGroupData1['Group1ClsBalPre'] = $ClsBalSubGrp1WisePre;
						
						$ClsBalMainGrpWise += $ClsBalSubGrp1Wise;
						$ClsBalMainGrpWisePre += $ClsBalSubGrp1WisePre;
						
						$mainGroupData['SubGroups1'][] = $subGroupData1;
					}
				}
				$mainGroupData['MainGroupClsBal'] = $ClsBalMainGrpWise;
				$mainGroupData['MainGroupClsBalPre'] = $ClsBalMainGrpWisePre;
				$nestedData[] = $mainGroupData;
				$i++;
			}
			$data['nestedData'] = $nestedData;
			return $data;
		}
		
		
		public function transaction()
		{
			if (!has_permission_new('accounting_transaction', '', 'view')) {
				access_denied('transaction');
			}
			$data          = [];
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			
			$data['_status'] = '';
			if( $this->input->get('status')){
				$data['_status'] = [$this->input->get('status')];
			}
			$data['tab_2'] = $this->input->get('tab');
			
			
			$data['group'] = $this->input->get('group');
			$data['tab'][] = 'banking';
			$data['tab'][] = 'sales';
			$data['tab'][] = 'expenses';
			if(acc_get_status_modules('hr_payroll')){
				$data['tab'][] = 'payslips';
			}
			
			if(acc_get_status_modules('purchase')){
				$data['tab'][] = 'purchase';
			}
			
			if(acc_get_status_modules('warehouse')){
				$data['tab'][] = 'warehouse';
			}
			
			if ($data['group'] == '') {
				$data['group'] = 'banking';
			}
			
			if($data['group'] == 'sales'){
				$this->load->model('payment_modes_model');
				$data['count_invoice'] = $this->accounting_model->count_invoice_not_convert_yet();
				$data['count_payment'] = $this->accounting_model->count_payment_not_convert_yet();
				$data['invoices'] = $this->accounting_model->get_data_invoices_for_select();
				$data['payment_modes'] = $this->payment_modes_model->get();
				
				if ($data['tab_2'] == '') {
					$data['tab_2'] = 'payment';
				}
				}elseif ($data['group'] == 'warehouse') {
				$data['count_stock_import'] = $this->accounting_model->count_stock_import_not_convert_yet();
				$data['count_stock_export'] = $this->accounting_model->count_stock_export_not_convert_yet();
				$data['count_loss_adjustment'] = $this->accounting_model->count_loss_adjustment_not_convert_yet();
				$data['count_opening_stock'] = $this->accounting_model->count_opening_stock_not_convert_yet();
				
				
				if ($data['tab_2'] == '') {
					$data['tab_2'] = 'stock_import';
				}
				}elseif ($data['group'] == 'purchase') {
				
				$data['count_purchase_order'] = $this->accounting_model->count_purchase_order_not_convert_yet();
				$data['count_purchase_payment'] = $this->accounting_model->count_purchase_payment_not_convert_yet();
				
				if ($data['tab_2'] == '') {
					$data['tab_2'] = 'purchase_order';
				}
			}
			
			$data['accounts'] = $this->accounting_model->get_accounts();
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select();
			$data['title']        = _l($data['group']);
			$data['tabs']['view'] = 'transaction/' . $data['group'];
			$this->load->view('transaction/manage', $data);
		}
		
		/**
			* sales table
			* @return json
		*/
		public function sales_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1', // bulk actions
				db_prefix() . 'invoicepaymentrecords.id as id',
				'amount',
				'invoiceid',
				db_prefix() . 'payment_modes.name as name',
				db_prefix() .'invoicepaymentrecords.date as date',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_type = "payment") as count_account_historys'
				];
				$where = [];
				if ($this->input->post('invoice')) {
					$invoice = $this->input->post('invoice');
					array_push($where, 'AND invoiceid IN (' . implode(', ', $invoice) . ')');
				}
				
				if ($this->input->post('payment_mode')) {
					$payment_mode = $this->input->post('payment_mode');
					array_push($where, 'AND paymentmode IN (' . implode(', ', $payment_mode) . ')');
				}
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_type = "payment") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_type = "payment") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_type = "payment") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_type = "payment") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date >= "' . $from_date . '" and ' . db_prefix() . 'invoicepaymentrecords.date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'invoicepaymentrecords.date <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'invoicepaymentrecords';
				$join         = ['LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'invoicepaymentrecords.paymentmode',
				'LEFT JOIN ' . db_prefix() . 'acc_account_history ON ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_id = "payment"',
				'LEFT JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid',
				'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency'
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['paymentmode', db_prefix(). 'currencies.name as currency_name']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$categoryOutput = _d($aRow['date']);
					
					$categoryOutput .= '<div class="row-options">';
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="payment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="payment" data-amount="'.$aRow['amount'].'">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" id="payment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="payment" data-amount="'.$aRow['amount'].'">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$categoryOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'payment\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					
					$row[] = app_format_money($aRow['amount'], $aRow['currency_name']);
					
					$row[] = $aRow['name'];
					$row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					
					$row[] = '<span class="label label-' . $label_class . ' s-status payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-amount' => $aRow['amount'],
						'data-type' => 'payment',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* sales table
			* @return json
		*/
		public function sales_invoice_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1', // bulk actions
				db_prefix() . 'invoices.id as id',
				'total',
				'clientid',
				'number',
				db_prefix() .'invoices.date as date',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_type = "invoice") as count_account_historys',
				db_prefix() . 'invoices.status'
				];
				$where = [];
				if ($this->input->post('invoice')) {
					$invoice = $this->input->post('invoice');
					array_push($where, 'AND id IN (' . implode(', ', $invoice) . ')');
				}
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_type = "invoice") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_type = "invoice") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_type = "invoice") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_type = "invoice") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'invoices.date >= "' . $from_date . '" and ' . db_prefix() . 'invoices.date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'invoices.date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'invoices.date <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'invoices';
				$join         = ['LEFT JOIN ' . db_prefix() . 'acc_account_history ON ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_id = "invoice"',
				'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency',
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix(). 'currencies.name as currency_name']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$categoryOutput = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';
					
					$categoryOutput .= '<div class="row-options">';
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="invoice-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="invoice" data-amount="'.$aRow['total'].'">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" id="invoice-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="invoice" data-amount="'.$aRow['total'].'">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$categoryOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'invoice\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					
					$row[] = _d($aRow['date']);
					$row[] = app_format_money($aRow['total'], $aRow['currency_name']);
					
					$row[] = get_company_name($aRow['clientid']);
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					
					$row[] = '<span class="label label-' . $label_class . ' s-status invoice-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$row[] = format_invoice_status($aRow[db_prefix() . 'invoices.status']);
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-amount' => $aRow['total'],
						'data-type' => 'invoice',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* expenses table
			* @return json
		*/
		public function expenses_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1', // bulk actions
				db_prefix() . 'expenses.id as id',
				'amount',
				'invoiceid',
				db_prefix() . 'expenses_categories.name as category_name',
				'expense_name',
				db_prefix() . 'payment_modes.name as payment_mode_name',
				db_prefix() . 'expenses.date as date',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'expenses.id and ' . db_prefix() . 'acc_account_history.rel_type = "expense") as count_account_historys'
				];
				$where = [];
				
				if ($this->input->post('invoice')) {
					$invoice = $this->input->post('invoice');
					array_push($where, 'AND invoiceid IN (' . implode(', ', $invoice) . ')');
				}
				
				if ($this->input->post('payment_mode')) {
					$payment_mode = $this->input->post('payment_mode');
					array_push($where, 'AND paymentmode IN (' . implode(', ', $payment_mode) . ')');
				}
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'expenses.id and ' . db_prefix() . 'acc_account_history.rel_type = "expense") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'expenses.id and ' . db_prefix() . 'acc_account_history.rel_type = "expense") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'expenses.id and ' . db_prefix() . 'acc_account_history.rel_type = "expense") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'expenses.id and ' . db_prefix() . 'acc_account_history.rel_type = "expense") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '" and ' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'expenses.date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'expenses.date <= "' . $to_date . '")');
				}
				
				$select_purchase = '0 as count_purchases';
				if(acc_get_status_modules('purchase')){
					$select_purchase = '(select count(*) from ' . db_prefix() . 'pur_orders where ' . db_prefix() . 'pur_orders.expense_convert = ' . db_prefix() . 'expenses.id) as count_purchases';
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'expenses';
				$join         = [
				'JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'expenses.category',
				'LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'expenses.paymentmode',
				'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'expenses.currency'
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix(). 'currencies.name as currency_name', $select_purchase]);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					$categoryOutput = $aRow['expense_name'];
					
					$categoryOutput .= '<div class="row-options">';
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date)) && $aRow['count_purchases'] == 0) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="expense-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="expense" data-amount="'.$aRow['amount'].'">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" id="expense-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="expense" data-amount="'.$aRow['amount'].'">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$categoryOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'expense\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					$row[] = _d($aRow['date']);
					
					$row[] = app_format_money($aRow['amount'], $aRow['currency_name']);
					
					$row[] = $aRow['category_name'];
					$row[] = $aRow['payment_mode_name'];
					$row[] = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['invoiceid']) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					}
					if ($aRow['count_purchases'] > 0) {
						$row[] = '';
						}else{
						$row[] = '<span class="label label-' . $label_class . ' s-status expense-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					}
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date)) && $aRow['count_purchases'] == 0){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-amount' => $aRow['amount'],
						'data-type' => 'expense',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* banking table
			* @return json
		*/
		public function banking_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1', // bulk actions
				'id',
				db_prefix() . 'acc_transaction_bankings.date as date',
				'withdrawals',
				'deposits',
				'payee',
				'description',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'acc_transaction_bankings.id and ' . db_prefix() . 'acc_account_history.rel_type = "banking") as count_account_historys'
				
				];
				$where = [];
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'acc_transaction_bankings.date >= "' . $from_date . '" and ' . db_prefix() . 'acc_transaction_bankings.date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'acc_transaction_bankings.date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'acc_transaction_bankings.date <= "' . $to_date . '")');
				}
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'acc_transaction_bankings.id and ' . db_prefix() . 'acc_account_history.rel_type = "banking") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'acc_transaction_bankings.id and ' . db_prefix() . 'acc_account_history.rel_type = "banking") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'acc_transaction_bankings.id and ' . db_prefix() . 'acc_account_history.rel_type = "banking") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'acc_transaction_bankings.id and ' . db_prefix() . 'acc_account_history.rel_type = "banking") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_transaction_bankings';
				$join         = [];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					$categoryOutput = _d($aRow['date']);
					$amount = $aRow['withdrawals'] > 0 ? $aRow['withdrawals'] : $aRow['deposits'];
					$categoryOutput .= '<div class="row-options">';
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="banking-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="banking" data-amount="'.$amount.'">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" id="banking-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="banking" data-amount="'.$amount.'">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$categoryOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'banking\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					
					$row[] = app_format_money($aRow['withdrawals'], $currency->name);
					$row[] = app_format_money($aRow['deposits'], $currency->name);
					
					$row[] = $aRow['payee'];
					$row[] = $aRow['description'];
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					
					$row[] = '<span class="label label-' . $label_class . ' s-status payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-amount' => $amount,
						'data-type' => 'banking',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* manage chart of accounts
		*/
		public function chart_of_accounts(){
			if (!has_permission_new('accounting_chart_of_accounts', '', 'view')) {
				access_denied('chart_of_accounts');
			}
			
			$data['title'] = _l('chart_of_accounts');
			//$data['account_types'] = $this->accounting_model->get_account_types();
			$data['account_types'] = $this->accounting_model->get_accoun_main_group();
			//$data['detail_types'] = $this->accounting_model->get_account_type_details();
			$data['detail_types'] = $this->accounting_model->get_account_subgroup();
			//$data['accounts'] = $this->accounting_model->get_accounts();
			$data['accounts'] = $this->accounting_model->get_accounts_list();
			/*echo "<pre>";
				echo count($data['accounts']);
				print_r($data['accounts']);
			die;*/
			$this->load->view('chart_of_accounts/manage', $data);
		}
		
		/**
			* setting
			* @return view
		*/
		public function setting()
		{
			if (!has_permission_new('accounting_setting', '', 'view')) {
				access_denied('setting');
			}
			
			$data          = [];
			$data['group'] = $this->input->get('group');
			
			$data['tab'][] = 'general';
			$data['tab'][] = 'banking_rules';
			$data['tab'][] = 'mapping_setup';
			$data['tab'][] = 'account_type_details';
			
			$data['tab_2'] = $this->input->get('tab');
			if ($data['group'] == '') {
				$data['group'] = 'general';
			}
			
			if ($data['group'] == 'mapping_setup') {
				if ($data['tab_2'] == '') {
					$data['tab_2'] = 'general_mapping_setup';
				}
				
				$data['items'] = $this->accounting_model->get_items_not_yet_auto();
				$this->load->model('invoice_items_model');
				$data['_items'] = $this->invoice_items_model->get();
				$this->load->model('taxes_model');
				$data['_taxes'] = $this->taxes_model->get();
				$data['taxes'] = $this->accounting_model->get_taxes_not_yet_auto();
				
				$this->load->model('expenses_model');
				$data['_categories'] = $this->expenses_model->get_category();
				$data['categories'] = $this->accounting_model->get_expense_category_not_yet_auto();
				
				$this->load->model('payment_modes_model');
				$data['_payment_modes'] = $this->payment_modes_model->get();
				$data['payment_modes'] = $this->accounting_model->get_payment_mode_not_yet_auto();
				}elseif ($data['group'] == 'account_type_details') {
				$data['account_types'] = $this->accounting_model->get_account_types();
			}
			$data['accounts'] = $this->accounting_model->get_accounts();
			$data['title']        = _l($data['group']);
			$data['tabs']['view'] = 'setting/' . $data['group'];
			$this->load->view('setting/manage', $data);
		}
		
		/**
			* update general setting
		*/
		public function update_general_setting(){
			if (!has_permission_new('accounting_setting', '', 'edit') && !is_admin()) {
				access_denied('accounting_setting');
			}
			$data = $this->input->post();
			$success = $this->accounting_model->update_general_setting($data);
			if($success == true){
				$message = _l('updated_successfully', _l('setting'));
				set_alert('success', $message);
			}
			redirect(admin_url('accounting/setting?group=general'));
		}
		
		/**
			* update automatic conversion
		*/
		public function update_automatic_conversion(){
			if (!has_permission_new('accounting_setting', '', 'edit') && !is_admin()) {
				access_denied('accounting_setting');
			}
			$data = $this->input->post();
			$success = $this->accounting_model->update_automatic_conversion($data);
			if($success == true){
				$message = _l('updated_successfully', _l('setting'));
				set_alert('success', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup'));
		}
		
		/**
			* accounts table
			* @return json
		*/
		public function accounts_table()
		{
			if ($this->input->is_ajax_request()) {
				$acc_enable_account_numbers = get_option('acc_enable_account_numbers');
				$acc_show_account_numbers = get_option('acc_show_account_numbers');
				
				$accounts = $this->accounting_model->get_accounts();
				$account_types = $this->accounting_model->get_account_types();
				$detail_types = $this->accounting_model->get_account_type_details();
				
				$account_name = [];
				$account_type_name = [];
				$detail_type_name = [];
				
				foreach ($accounts as $key => $value) {
					$account_name[$value['id']] = $value['name'];
				}
				
				foreach ($account_types as $key => $value) {
					$account_type_name[$value['id']] = $value['name'];
				}
				
				foreach ($detail_types as $key => $value) {
					$detail_type_name[$value['id']] = $value['name'];
				}
				
				$array_history = [2,3,4,5,7,8,9,10];
				
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				
				if($acc_enable_account_numbers == 1 && $acc_show_account_numbers == 1){
					$select = [
					'1', // bulk actions
					'id',
					'number',
					'name',
					'parent_account',
					'account_type_id',
					'account_detail_type_id',
					'balance',
					'key_name',
					'active',
					];
					}else {
					$select = [
					'1', // bulk actions
					'id',
					'name',
					'parent_account',
					'account_type_id',
					'account_detail_type_id',
					'balance',
					'key_name',
					'active',
					];
				}
				
				$where = [];
				if ($this->input->post('ft_active')) {
					$ft_active = $this->input->post('ft_active');
					if($ft_active == 'yes'){
						array_push($where, 'AND active = 1');
						}elseif($ft_active == 'no'){
						array_push($where, 'AND active = 0');
					}
				}
				if ($this->input->post('ft_account')) {
					$ft_account = $this->input->post('ft_account');
					array_push($where, 'AND id IN (' . implode(', ', $ft_account) . ')');
				}
				if ($this->input->post('ft_parent_account')) {
					$ft_parent_account = $this->input->post('ft_parent_account');
					array_push($where, 'AND parent_account IN (' . implode(', ', $ft_parent_account) . ')');
				}
				if ($this->input->post('ft_type')) {
					$ft_type = $this->input->post('ft_type');
					array_push($where, 'AND account_type_id IN (' . implode(', ', $ft_type) . ')');
				}
				if ($this->input->post('ft_detail_type')) {
					$ft_detail_type = $this->input->post('ft_detail_type');
					array_push($where, 'AND account_detail_type_id IN (' . implode(', ', $ft_detail_type) . ')');
				}
				
				$accounting_method = get_option('acc_accounting_method');
				
				if($accounting_method == 'cash'){
					$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as debit';
					$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as credit';
					}else{
					$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as debit';
					$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as credit';
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_accounts';
				$join         = [];
				$result       = $this->accounting_model->get_account_data_tables($aColumns, $sIndexColumn, $sTable, $join, $where, ['number', 'description', 'balance_as_of', $debit, $credit, 'default_account']);
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$categoryOutput = '';
					if(isset($aRow['level'])){
						for ($i=0; $i < $aRow['level']; $i++) { 
							$categoryOutput .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
					}
					
					if($acc_enable_account_numbers == 1 && $acc_show_account_numbers == 1 && $aRow['number'] != ''){
						$categoryOutput .= $aRow['number'] .' - ';
					}
					
					if($aRow['name'] == ''){
						$categoryOutput .= _l($aRow['key_name']);
						}else{
						$categoryOutput .= $aRow['name'];
					}
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_chart_of_accounts', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_account(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
					}
					
					if (has_permission_new('accounting_chart_of_accounts', '', 'delete') && $aRow['default_account'] == 0) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_account/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					if($aRow['parent_account'] != '' && $aRow['parent_account'] != 0){
						$row[] = (isset($account_name[$aRow['parent_account']]) ? $account_name[$aRow['parent_account']] : '');
						}else{
						$row[] = '';
					}
					$row[] = isset($account_type_name[$aRow['account_type_id']]) ? $account_type_name[$aRow['account_type_id']] : '';
					$row[] = isset($detail_type_name[$aRow['account_detail_type_id']]) ? $detail_type_name[$aRow['account_detail_type_id']] : '';
					if($aRow['account_type_id'] == 11 || $aRow['account_type_id'] == 12 || $aRow['account_type_id'] == 8 || $aRow['account_type_id'] == 9 || $aRow['account_type_id'] == 10 || $aRow['account_type_id'] == 7){
						$row[] = app_format_money($aRow['credit'] - $aRow['debit'], $currency->name);
						}else{
						$row[] = app_format_money($aRow['debit'] - $aRow['credit'], $currency->name);
					}
					$row[] = '';
					
					$checked = '';
					if ($aRow['active'] == 1) {
						$checked = 'checked';
					}
					
					$_data = '<div class="onoffswitch">
					<input type="checkbox" ' . ((!has_permission_new('accounting_chart_of_accounts', '', 'edit') && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'accounting/change_account_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
					<label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
					</div>';
					
					// For exporting
					$_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
					$row[] = $_data;
					
					$options = '';
					if(in_array($aRow['account_type_id'], $array_history)){
						$options = icon_btn(admin_url('accounting/rp_account_history?account='.$aRow['id']), 'history', 'btn-default', [
						'title' => _l('account_history'),
						]);
					}
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* accounts table
			* @return json
		*/
		public function table_for_chart_accounts()
		{
			if ($this->input->is_ajax_request()) {
				$acc_enable_account_numbers = get_option('acc_enable_account_numbers');
				$acc_show_account_numbers = get_option('acc_show_account_numbers');
				$selected_company = $this->session->userdata('root_company');
				/*$accounts = $this->accounting_model->get_accounts();
					$account_types = $this->accounting_model->get_account_types();
					$detail_types = $this->accounting_model->get_account_type_details();
					
					$account_name = [];
					$account_type_name = [];
					$detail_type_name = [];
					
					foreach ($accounts as $key => $value) {
					$account_name[$value['id']] = $value['name'];
					}
					
					foreach ($account_types as $key => $value) {
					$account_type_name[$value['id']] = $value['name'];
					}
					
					foreach ($detail_types as $key => $value) {
					$detail_type_name[$value['id']] = $value['name'];
					}
					
					$array_history = [2,3,4,5,7,8,9,10];
					
					$this->load->model('currencies_model');
					
					$currency = $this->currencies_model->get_base_currency();
					
					if($acc_enable_account_numbers == 1 && $acc_show_account_numbers == 1){
					$select = [
					'1', // bulk actions
					'id',
					'number',
					'name',
					'parent_account',
					'account_type_id',
					'account_detail_type_id',
					'balance',
					'key_name',
					'active',
					];
					}else {
					$select = [
					'1', // bulk actions
					'id',
					'name',
					'parent_account',
					'account_type_id',
					'account_detail_type_id',
					'balance',
					'key_name',
					'active',
					];
					}
				*/
				$select = [
				
				'AccountID',
				'company',
				'ActGroupID',
				'SubActGroupID',
				'active',
				'MaxCrdAmt',
				'ManagerID',
				'DistributorType',
				];
				$where = [];
				array_push($where, 'AND active = 1');
				array_push($where, 'AND '. db_prefix() .'clients.PlantID = '.$selected_company);
				array_push($where, 'AND SubActGroupID NOT IN ("10022003","1002503","1002504","1002506","30000006","30000004","10022005","10022004","30000007","60001004","50003002")');
				
				/*if ($this->input->post('ft_active')) {
					$ft_active = $this->input->post('ft_active');
					if($ft_active == 'yes'){
					array_push($where, 'AND active = 1');
					}elseif($ft_active == 'no'){
					array_push($where, 'AND active = 0');
					}
				}*/
				/*if ($this->input->post('ft_account')) {
					$ft_account = $this->input->post('ft_account');
					array_push($where, 'AND id IN (' . implode(', ', $ft_account) . ')');
				}*/
				/*if ($this->input->post('ft_parent_account')) {
					$ft_parent_account = $this->input->post('ft_parent_account');
					array_push($where, 'AND parent_account IN (' . implode(', ', $ft_parent_account) . ')');
				}*/
				/*if ($this->input->post('ft_type')) {
					$ft_type = $this->input->post('ft_type');
					array_push($where, 'AND account_type_id IN (' . implode(', ', $ft_type) . ')');
				}*/
				/*if ($this->input->post('ft_detail_type')) {
					$ft_detail_type = $this->input->post('ft_detail_type');
					array_push($where, 'AND account_detail_type_id IN (' . implode(', ', $ft_detail_type) . ')');
				}*/
				
				$accounting_method = get_option('acc_accounting_method');
				
				/*if($accounting_method == 'cash'){
					$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as debit';
					$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as credit';
					}else{
					$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as debit';
					$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as credit';
				}*/
				
				$aColumns     = $select;
				$sIndexColumn = 'AccountID';
				$sTable       = db_prefix() . 'clients';
				$join         = [];
				//$result       = $this->accounting_model->get_account_data_tables($aColumns, $sIndexColumn, $sTable, $join, $where, []);
				$result       = $this->accounting_model->get_data_tables_for_chart_of_account($aColumns, $sIndexColumn, $sTable, $join, $where, []);
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					/*$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['AccountID'] . '"><label></label></div>';*/
					$row[] = $aRow['AccountID'];
					$row[] = $aRow['company'];
					
					/*if(empty($aRow['ActGroupID'])){
						$row[] = $aRow['ActGroupID'];
						}else {
						
						$group_name = $this->accounting_model->get_account_group_name($aRow['ActGroupID']);
						$row[] = $group_name->ActGroupName;
					}*/
					$row[] = $aRow['ActGroupID'];
					if(empty($aRow['SubActGroupID'])){
						$row[] = $aRow['SubActGroupID'];
						}else {
						
						$subgroup_name = $this->accounting_model->get_account_subgroup_name($aRow['SubActGroupID']);
						$row[] = $subgroup_name->SubActGroupName;
					}
					
					
					//$row[] = $aRow['SubActGroupID'];
					/*$total_bal = $aRow['BAL1'] + $aRow['BAL2'] +$aRow['BAL3'] +$aRow['BAL4'] +$aRow['BAL5'] +$aRow['BAL6'] +$aRow['BAL7'] +$aRow['BAL8'] + $aRow['BAL9'] + $aRow['BAL10'] + $aRow['BAL11'] + $aRow['BAL12'] + $aRow['BAL13'];
					$row[] = round($total_bal,2);*/
					$row[] = $aRow['active'];
					if($aRow['active']=="1"){
						$status = "Active";
						}else{
						$status = "DeActive";
					}
					$row[] = $status;
					
					
					/*$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
						
						$categoryOutput = '';
						if(isset($aRow['level'])){
						for ($i=0; $i < $aRow['level']; $i++) { 
						$categoryOutput .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						}
						}
						
						if($acc_enable_account_numbers == 1 && $acc_show_account_numbers == 1 && $aRow['number'] != ''){
						$categoryOutput .= $aRow['number'] .' - ';
						}
						
						if($aRow['name'] == ''){
						$categoryOutput .= _l($aRow['key_name']);
						}else{
						$categoryOutput .= $aRow['name'];
						}
						
						$categoryOutput .= '<div class="row-options">';
						
						if (has_permission_new('accounting_chart_of_accounts', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_account(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
						}
						
						if (has_permission_new('accounting_chart_of_accounts', '', 'delete') && $aRow['default_account'] == 0) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_account/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
						}
						
						$categoryOutput .= '</div>';
						$row[] = $categoryOutput;
						if($aRow['parent_account'] != '' && $aRow['parent_account'] != 0){
						$row[] = (isset($account_name[$aRow['parent_account']]) ? $account_name[$aRow['parent_account']] : '');
						}else{
						$row[] = '';
						}
						$row[] = isset($account_type_name[$aRow['account_type_id']]) ? $account_type_name[$aRow['account_type_id']] : '';
						$row[] = isset($detail_type_name[$aRow['account_detail_type_id']]) ? $detail_type_name[$aRow['account_detail_type_id']] : '';
						if($aRow['account_type_id'] == 11 || $aRow['account_type_id'] == 12 || $aRow['account_type_id'] == 8 || $aRow['account_type_id'] == 9 || $aRow['account_type_id'] == 10 || $aRow['account_type_id'] == 7){
						$row[] = app_format_money($aRow['credit'] - $aRow['debit'], $currency->name);
						}else{
						$row[] = app_format_money($aRow['debit'] - $aRow['credit'], $currency->name);
						}
						$row[] = '';
						
						$checked = '';
						if ($aRow['active'] == 1) {
						$checked = 'checked';
						}
						
						$_data = '<div class="onoffswitch">
						<input type="checkbox" ' . ((!has_permission_new('accounting_chart_of_accounts', '', 'edit') && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'accounting/change_account_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
						<label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
						</div>';
						
						// For exporting
						$_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
						$row[] = $_data;
						
						$options = '';
						if(in_array($aRow['account_type_id'], $array_history)){
						$options = icon_btn(admin_url('accounting/rp_account_history?account='.$aRow['id']), 'history', 'btn-default', [
						'title' => _l('account_history'),
						]);
						}
					$row[] =  $options;*/
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			*
			*  add or edit account
			*  @param  integer  $id     The identifier
			*  @return view
		*/
		public function account()
		{
			if (!has_permission_new('accounting_chart_of_accounts', '', 'edit') && !has_permission_new('accounting_chart_of_accounts', '', 'create')) {
				access_denied('accounting');
			}
			
			if ($this->input->post()) {
				$data = $this->input->post();
				$data['description'] = $this->input->post('description', false);
				$message = '';
				if ($data['id'] == '') {
					if (!has_permission_new('accounting_chart_of_accounts', '', 'create')) {
						access_denied('accounting');
					}
					$success = $this->accounting_model->add_account($data);
					if ($success) {
						$message = _l('added_successfully', _l('acc_account'));
						}else {
						$message = _l('add_failure');
					}
					} else {
					if (!has_permission_new('accounting_chart_of_accounts', '', 'edit')) {
						access_denied('accounting');
					}
					$id = $data['id'];
					unset($data['id']);
					$success = $this->accounting_model->update_account($data, $id);
					if ($success) {
						$message = _l('updated_successfully', _l('acc_account'));
						}else {
						$message = _l('updated_fail');
					}
				}
				
				echo json_encode(['success' => $success, 'message' => $message]);
				die();
			}
		}
		
		/**
			* get data convert
			* @param  integer $id   
			* @param  string $type 
			* @return json       
		*/
		public function get_data_convert($id, $type){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$html = '';
			$list_item = [];
			if($type == 'payment'){
				$this->load->model('payments_model');
				$payment = $this->payments_model->get($id);
				
				$this->load->model('invoices_model');
				$invoice = $this->invoices_model->get($payment->invoiceid);
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('invoice').'</td>
				<td>'. '<a href="' . admin_url('invoices/list_invoices/' . $payment->invoiceid) . '" target="_blank">' . format_invoice_number($payment->invoiceid) . '</a>' .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('acc_amount').'</td>
				<td>'. app_format_money($payment->amount, $invoice->currency_name) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('expense_dt_table_heading_date').'</td>
				<td>'. _d($payment->date) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('payment_modes').'</td>
				<td>'. html_entity_decode($payment->name) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('note').'</td>
				<td colspan="2">'. html_entity_decode($payment->note) .'</td>
				</tr>';
				$amount = 1;
				
				$_html = '';
				if($invoice->currency_name != $currency->name){
					$amount = $this->accounting_model->currency_converter($invoice->currency_name, $currency->name, 1);
					
					$edit_template = "";
					$edit_template .= render_input('edit_exchange_rate','exchange_rate', $amount, 'number');
					$edit_template .= "<div class='text-center mtop10'>";
					$edit_template .= "<button type='button' class='btn btn-success edit_conversion_rate_action'>"._l('copy_task_confirm')."</button>";
					$edit_template .= "</div>";
					$_html .= form_hidden('currency_from', $invoice->currency_name);
					$_html .= form_hidden('currency_to', $currency->name);
					$_html .= form_hidden('exchange_rate', $amount);
					$_html .= form_hidden('payment_amount', $payment->amount);
					$_html .= '<div class="row"><div class="col-md-12"><label class="currency_converter_label th font-medium mbot15 pull-left">1 '.$invoice->currency_name.' = '.$amount.' '.$currency->name.'</label><a href="#" onclick="return false;" data-placement="bottom" data-toggle="popover" data-content="'. htmlspecialchars($edit_template) .'" data-html="true" data-original-title class="pull-left mleft5 font-medium-xs"><i class="fa fa-pencil-square-o"></i></a><br></div></div>';
					$html .=   '<tr class="project-overview">
					<td class="bold">'. _l('amount_after_convert').'</td>
					<td class="amount_after_convert">'.app_format_money(round($amount*$payment->amount, 2), $currency->name).'</td>
					<td>'.$_html.'</td>
					</tr>';
				}
				$html .=   '</tbody>
				</table>';
				if($invoice->currency_name != $currency->name){
					$amount = $this->accounting_model->currency_converter($invoice->currency_name, $currency->name, 1);
					
					$edit_template = "";
					$edit_template .= render_input('edit_exchange_rate','exchange_rate', $amount, 'number');
					$edit_template .= "<div class='text-center mtop10'>";
					$edit_template .= "<button type='button' class='btn btn-success edit_conversion_rate_action'>"._l('copy_task_confirm')."</button>";
					$edit_template .= "</div>";
					$html .= form_hidden('currency_from', $invoice->currency_name);
					$html .= form_hidden('currency_to', $currency->name);
					$html .= form_hidden('exchange_rate', $amount);
					$html .= '<h4>'._l('currency_converter').'</h4><div class="row"><div class="col-md-12"><label class="currency_converter_label th font-medium mbot15 pull-left">1 '.$invoice->currency_name.' = '.$amount.' '.$currency->name.'</label><a href="#" onclick="return false;" data-placement="bottom" data-toggle="popover" data-content="'. htmlspecialchars($edit_template) .'" data-html="true" data-original-title class="pull-left mleft5 font-medium-xs"><i class="fa fa-pencil-square-o"></i></a><br></div></div>';
					
				}
				$debit = get_option('acc_payment_deposit_to');
				$credit = get_option('acc_payment_payment_account');
				}elseif ($type == 'expense') {
				$this->load->model('expenses_model');
				$expense = $this->expenses_model->get($id);
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('expense_category').'</td>
				<td>'. $expense->category_name  .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('expense_name').'</td>
				<td>'. $expense->expense_name  .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('invoice').'</td>
				<td>'. '<a href="' . admin_url('invoices/list_invoices/' . $expense->invoiceid) . '" target="_blank">' . format_invoice_number($expense->invoiceid) . '</a>' .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('acc_amount').'</td>
				<td>'. app_format_money($expense->amount, $expense->currency_data->name) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('expense_dt_table_heading_date').'</td>
				<td>'. _d($expense->date) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('payment_modes').'</td>
				<td>'. html_entity_decode($expense->payment_mode_name) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('note').'</td>
				<td colspan="2">'. html_entity_decode($expense->note) .'</td>
				</tr>';
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				$amount = 1;
				if($expense->currency_data->name != $currency->name){
					$amount = $this->accounting_model->currency_converter($expense->currency_data->name, $currency->name, 1);
					$_html = '';
					$edit_template = "";
					$edit_template .= render_input('edit_exchange_rate','exchange_rate', $amount, 'number');
					$edit_template .= "<div class='text-center mtop10'>";
					$edit_template .= "<button type='button' class='btn btn-success edit_conversion_rate_action'>"._l('copy_task_confirm')."</button>";
					$edit_template .= "</div>";
					$_html .= form_hidden('currency_from', $expense->currency_data->name);
					$_html .= form_hidden('currency_to', $currency->name);
					$_html .= form_hidden('exchange_rate', $amount);
					$_html .= form_hidden('expense_amount', $expense->amount);
					
					$_html .= '<div class="row"><div class="col-md-12"><label class="currency_converter_label th font-medium mbot15 pull-left">1 '.$expense->currency_data->name.' = '.$amount.' '.$currency->name.'</label><a href="#" onclick="return false;" data-placement="bottom" data-toggle="popover" data-content="'. htmlspecialchars($edit_template) .'" data-html="true" data-original-title class="pull-left mleft5 font-medium-xs"><i class="fa fa-pencil-square-o"></i></a><br></div></div>';
					
					$html .=   '<tr class="project-overview">
					<td class="bold">'. _l('amount_after_convert').'</td>
					<td class="amount_after_convert">'.app_format_money(round($amount*$expense->amount, 2), $currency->name).'</td>
					<td>'.$_html.'</td>
					</tr>';
					
				}
				
				$html .=    '</tbody>
				</table>';
				
				$debit = get_option('acc_expense_deposit_to');
				$credit = get_option('acc_expense_payment_account');
				}elseif ($type == 'banking') {
				$banking = $this->accounting_model->get_transaction_banking($id);
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('expense_dt_table_heading_date').'</td>
				<td>'. _d($banking->date)  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('withdrawals').'</td>
				<td>'. app_format_money($banking->withdrawals, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('deposits').'</td>
				<td>'. app_format_money($banking->deposits, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('payee').'</td>
				<td>'. $banking->payee .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('description').'</td>
				<td>'. $banking->description .'</td>
				</tr>
				</tbody>
				</table>';
				
				$debit = 0;
				$credit = 0;
				}elseif ($type == 'invoice') {
				$this->load->model('invoices_model');
				$invoice = $this->invoices_model->get($id);
				$accounts = $this->accounting_model->get_accounts();
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('number').'</td>
				<td>'. format_invoice_number($invoice->id)  .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('expense_dt_table_heading_date').'</td>
				<td>'. _d($invoice->date)  .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('invoice_dt_table_heading_duedate').'</td>
				<td>'. _d($invoice->duedate)  .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('customer').'</td>
				<td>'. get_company_name($invoice->clientid) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('total').'</td>
				<td>'. app_format_money($invoice->total, $invoice->currency_name) .'</td>
				<td></td>
				</tr>';
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				$amount = 1;
				if($invoice->currency_name != $currency->name){
					$amount = $this->accounting_model->currency_converter($invoice->currency_name, $currency->name, 1);
					$_html = '';
					$edit_template = "";
					$edit_template .= render_input('edit_exchange_rate','exchange_rate', $amount, 'number');
					$edit_template .= "<div class='text-center mtop10'>";
					$edit_template .= "<button type='button' class='btn btn-success edit_conversion_rate_action'>"._l('copy_task_confirm')."</button>";
					$edit_template .= "</div>";
					$_html .= form_hidden('currency_from', $invoice->currency_name);
					$_html .= form_hidden('currency_to', $currency->name);
					$_html .= form_hidden('exchange_rate', $amount);
					$_html .= form_hidden('payment_amount', $invoice->total);
					
					$_html .= '<div class="row"><div class="col-md-12"><label class="currency_converter_label th font-medium mbot15 pull-left">1 '.$invoice->currency_name.' = '.$amount.' '.$currency->name.'</label><a href="#" onclick="return false;" data-placement="bottom" data-toggle="popover" data-content="'. htmlspecialchars($edit_template) .'" data-html="true" data-original-title class="pull-left mleft5 font-medium-xs"><i class="fa fa-pencil-square-o"></i></a><br></div></div>';
					
					$html .=   '<tr class="project-overview">
					<td class="bold">'. _l('amount_after_convert').'</td>
					<td class="amount_after_convert">'.app_format_money(round($amount*$invoice->total, 2), $currency->name).'</td>
					<td>'.$_html.'</td>
					</tr>';
					
				}
				
				$html .=    '</tbody>
				</table>';
				
				
				
				if($invoice->items){
					$payment_account = get_option('acc_invoice_payment_account');
					$deposit_to = get_option('acc_invoice_deposit_to');
					
					$html .= '<h4>'._l('list_of_items').'</h4>';
					
					foreach ($invoice->items as $value) {
						$item = $this->accounting_model->get_item_by_name($value['description']);
						$item_id = 0;
						if(isset($item->id)){
							$item_id = $item->id;
						}
						$list_item[] = $item_id;
						
						$this->db->where('rel_id', $id);
						$this->db->where('rel_type', $type);
						$this->db->where('item', $item_id);
						$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
						
						foreach ($account_history as $key => $val) {
							if($val['debit'] > 0){
								$debit = $val['account'];
							}
							
							if($val['credit'] > 0){
								$credit =  $val['account'];
							}
						}
						
						if($account_history){
							$html .= '
							<div class="div_content">
							<h5>'.$value['description'].'</h5>
							<div class="row">
							'.form_hidden('item_amount['.$item_id.']', $value['qty'] * $value['rate']).'
							<div class="col-md-6"> '.
							render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
							</div>
							<div class="col-md-6">
							'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
							</div>
							</div>
							</div>';
							}else{
							$item_automatic = $this->accounting_model->get_item_automatic($item_id);
							
							if($item_automatic){
								$html .= '
								<div class="div_content">
								<h5>'.$value['description'].'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $value['qty'] * $value['rate']).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$item_automatic->income_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
								}else{
								
								$html .= '
								<div class="div_content">
								<h5>'.$value['description'].'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $value['qty'] * $value['rate']).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
							}
						}
					}
				}
				
				$debit = get_option('acc_invoice_deposit_to');
				$credit = get_option('acc_invoice_payment_account');
				}elseif ($type == 'payslip') {
				$this->db->where('id', $id);
				$payslip = $this->db->get(db_prefix(). 'hrp_payslips')->row();
				
				$this->db->where('payslip_id', $id);
				$payslip_details = $this->db->get(db_prefix(). 'hrp_payslip_details')->result_array();
				
				$accounts = $this->accounting_model->get_accounts();
				
				
				$payment_account = get_option('acc_pl_total_insurance_payment_account');
				$deposit_to = get_option('acc_pl_total_insurance_deposit_to');
				
				if($payslip->payslip_status == 'payslip_closing'){
					$_data_status = ' <span class="label label-success "> '._l($payslip->payslip_status).' </span>';
					}else{
					$_data_status = ' <span class="label label-primary"> '._l($payslip->payslip_status).' </span>';
				}
				$total_insurance = 0;
				$net_pay = 0;
				$income_tax_paye = 0;
				foreach ($payslip_details as $key => $value) {
					if(is_numeric($value['total_insurance'])){
						$total_insurance += $value['total_insurance'];
					}
					
					if(is_numeric($value['net_pay'])){
						$net_pay += $value['net_pay'];
					}
					
					if(is_numeric($value['income_tax_paye'])){
						$income_tax_paye += $value['income_tax_paye'];
					}
				}
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('payslip_name').'</td>
				<td>'. $payslip->payslip_name  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('payslip_name').'</td>
				<td>'. get_payslip_template_name($payslip->payslip_template_id) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('payslip_month').'</td>
				<td>'. date('m-Y', strtotime($payslip->payslip_month))  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('date_created').'</td>
				<td>'. _dt($payslip->date_created)  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('status').'</td>
				<td>'. $_data_status  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('ps_total_insurance').'</td>
				<td>'. app_format_money($total_insurance, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('ps_income_tax_paye').'</td>
				<td>'. app_format_money($income_tax_paye, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('ps_net_pay').'</td>
				<td>'. app_format_money($net_pay, $currency->name) .'</td>
				</tr>
				</tbody>
				</table>';
				
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', $type);
				$this->db->where('payslip_type', 'total_insurance');
				$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
				
				$payment_account_insurance = get_option('acc_pl_total_insurance_payment_account');
				$deposit_to_insurance = get_option('acc_pl_total_insurance_deposit_to');
				foreach ($account_history as $key => $val) {
					if($val['debit'] > 0){
						$deposit_to_insurance =  $val['account'];
					}
					
					if($val['credit'] > 0){
						$payment_account_insurance = $val['account'];
					}
				}
				
				$html .= '
				<div class="div_content">
				<h5>'._l('ps_total_insurance').'</h5>
				<div class="row">
				'.form_hidden('total_insurance', $total_insurance).'
				<div class="col-md-6"> '.
				render_select('payment_account_insurance',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account_insurance,array(),array(),'','',false) .'
				</div>
				<div class="col-md-6">
				'. render_select('deposit_to_insurance',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to_insurance,array(),array(),'','',false).'
				</div>
				</div>
				</div>';
				
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', $type);
				$this->db->where('payslip_type', 'tax_paye');
				$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
				
				$payment_account_tax_paye = get_option('acc_pl_tax_paye_payment_account');
				$deposit_to_tax_paye = get_option('acc_pl_tax_paye_deposit_to');
				foreach ($account_history as $key => $val) {
					if($val['debit'] > 0){
						$deposit_to_tax_paye =  $val['account'];
					}
					
					if($val['credit'] > 0){
						$payment_account_tax_paye = $val['account'];
					}
				}
				
				$html .= '
				<div class="div_content">
				<h5>'._l('ps_income_tax_paye').'</h5>
				<div class="row">
				'.form_hidden('tax_paye', $income_tax_paye).'
				<div class="col-md-6"> '.
				render_select('payment_account_tax_paye',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account_tax_paye,array(),array(),'','',false) .'
				</div>
				<div class="col-md-6">
				'. render_select('deposit_to_tax_paye',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to_tax_paye,array(),array(),'','',false).'
				</div>
				</div>
				</div>';
				
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', $type);
				$this->db->where('payslip_type', 'net_pay');
				$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
				
				$payment_account_net_pay = get_option('acc_pl_net_pay_payment_account');
				$deposit_to_net_pay = get_option('acc_pl_net_pay_deposit_to');
				foreach ($account_history as $key => $val) {
					if($val['debit'] > 0){
						$deposit_to_net_pay =  $val['account'];
					}
					
					if($val['credit'] > 0){
						$payment_account_net_pay = $val['account'];
					}
				}
				
				$html .= '
				<div class="div_content">
				<h5>'._l('ps_net_pay').'</h5>
				<div class="row">
				'.form_hidden('net_pay', $net_pay).'
				<div class="col-md-6"> '.
				render_select('payment_account_net_pay',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account_net_pay,array(),array(),'','',false) .'
				</div>
				<div class="col-md-6">
				'. render_select('deposit_to_net_pay',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to_net_pay,array(),array(),'','',false).'
				</div>
				</div>
				</div>';
				
				$debit = get_option('acc_expense_deposit_to');
				$credit = get_option('acc_expense_payment_account');
				}elseif ($type == 'purchase_order') {
				$accounts = $this->accounting_model->get_accounts();
				
				$this->load->model('purchase/purchase_model');
				$purchase_order = $this->purchase_model->get_pur_order($id);
				$purchase_order_detail = $this->purchase_model->get_pur_order_detail($id);
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('purchase_order').'</td>
				<td>'. '<a href="' . admin_url('purchase/purchase_order/' . $purchase_order->id) . '">'.$purchase_order->pur_order_number. '</a>'  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('order_date').'</td>
				<td>'. _d($purchase_order->order_date) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('vendor').'</td>
				<td>'. '<a href="' . admin_url('purchase/vendor/' . $purchase_order->vendor) . '" >' .  get_vendor_company_name($purchase_order->vendor) . '</a>' .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('po_value').'</td>
				<td>'. app_format_money($purchase_order->subtotal, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('tax_value').'</td>
				<td>'. app_format_money($purchase_order->total_tax, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('po_value_included_tax').'</td>
				<td>'. app_format_money($purchase_order->total, $currency->name) .'</td>
				</tr>
				</tbody>
				</table>';
				
				if($purchase_order_detail){
					$payment_account = get_option('acc_pur_order_payment_account');
					$deposit_to = get_option('acc_pur_order_deposit_to');
					
					$html .= '<h4>'._l('list_of_items').'</h4>';
					foreach ($purchase_order_detail as $value) {
						
						$this->db->where('id', $value['item_code']);
						$item = $this->db->get(db_prefix().'items')->row();
						
						$item_description = '';
						if(isset($item) && isset($item->commodity_code) && isset($item->description)){
							$item_description = $item->commodity_code.' - '.$item->description;
						}
						
						$item_id = 0;
						if(isset($item->id)){
							$item_id = $item->id;
						}
						
						if($item_id == 0){
							continue;
						}
						$list_item[] = $item_id;
						
						$this->db->where('rel_id', $id);
						$this->db->where('rel_type', $type);
						$this->db->where('item', $item_id);
						$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
						
						foreach ($account_history as $key => $val) {
							if($val['debit'] > 0){
								$debit = $val['account'];
							}
							
							if($val['credit'] > 0){
								$credit =  $val['account'];
							}
						}
						
						if($account_history){
							$html .= '
							<div class="div_content">
							<h5>'.$item_description.'</h5>
							<div class="row">
							'.form_hidden('item_amount['.$item_id.']', $value['into_money']).'
							<div class="col-md-6"> '.
							render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
							</div>
							<div class="col-md-6">
							'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
							</div>
							</div>
							</div>';
							}else{
							$item_automatic = $this->accounting_model->get_item_automatic($item_id);
							
							if($item_automatic){
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $value['into_money']).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$item_automatic->expence_account,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
								}else{
								
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $value['into_money']).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
							}
						}
					}
				}
				
				$debit = 0;
				$credit = 0;
				}elseif ($type == 'stock_export') {
				$this->load->model('warehouse/warehouse_model');
				$goods_delivery = $this->warehouse_model->get_goods_delivery($id);
				$goods_delivery_detail = $this->warehouse_model->get_goods_delivery_detail($id);
				$accounts = $this->accounting_model->get_accounts();
				$status = '';
				
				if($goods_delivery->approval == 1){
					$status = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
					}elseif($goods_delivery->approval == 0){
					$status = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
					}elseif($goods_delivery->approval == -1){
					$status = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
				}
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('expense_dt_table_heading_date').'</td>
				<td><a href="' . admin_url('warehouse/view_delivery/' . $goods_delivery->id ).'">' . $goods_delivery->goods_delivery_code . '</a></td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('accounting_date').'</td>
				<td>'. _d($goods_delivery->date_c)  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('status').'</td>
				<td>'. $status .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('subtotal').'</td>
				<td>'. app_format_money($goods_delivery->total_money, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('total_discount').'</td>
				<td>'. app_format_money($goods_delivery->total_discount, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('total_money').'</td>
				<td>'. app_format_money($goods_delivery->after_discount, $currency->name) .'</td>
				</tr>
				</tbody>
				</table>';
				
				if($goods_delivery_detail){
					$payment_account = get_option('acc_wh_stock_export_payment_account');
					$deposit_to = get_option('acc_wh_stock_export_deposit_to');
					
					$html .= '<h4>'._l('list_of_items').'</h4>';
					
					foreach ($goods_delivery_detail as $value) {
						
						$this->db->where('id', $value['commodity_code']);
						$item = $this->db->get(db_prefix().'items')->row();
						
						$item_description = '';
						if(isset($item) && isset($item->commodity_code) && isset($item->description)){
							$item_description = $item->commodity_code.' - '.$item->description;
						}
						
						$item_id = 0;
						if(isset($item->id)){
							$item_id = $item->id;
						}
						
						if($item_id == 0){
							continue;
						}
						
						$list_item[] = $item_id;
						
						$this->db->where('rel_id', $id);
						$this->db->where('rel_type', $type);
						$this->db->where('item', $item_id);
						$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
						
						foreach ($account_history as $key => $val) {
							if($val['debit'] > 0){
								$debit = $val['account'];
							}
							
							if($val['credit'] > 0){
								$credit =  $val['account'];
							}
						}
						
						if($account_history){
							$html .= '
							<div class="div_content">
							<h5>'.$item_description.'</h5>
							<div class="row">
							'.form_hidden('item_amount['.$item_id.']', ($value['quantities'] * $value['unit_price'])).'
							<div class="col-md-6"> '.
							render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
							</div>
							<div class="col-md-6">
							'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
							</div>
							</div>
							</div>';
							}else{
							$item_automatic = $this->accounting_model->get_item_automatic($item_id);
							
							if($item_automatic){
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', ($value['quantities'] * $value['unit_price'])).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$item_automatic->inventory_asset_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
								}else{
								
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', ($value['quantities'] * $value['unit_price'])).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
							}
						}
					}
				}
				
				$debit = 0;
				$credit = 0;
				}elseif ($type == 'stock_import') {
				$accounts = $this->accounting_model->get_accounts();
				
				$this->load->model('warehouse/warehouse_model');
				$goods_receipt = $this->warehouse_model->get_goods_receipt($id);
				$goods_receipt_detail = $this->warehouse_model->get_goods_receipt_detail($id);
				
				$status = '';
				
				if($goods_receipt->approval == 1){
					$status = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
					}elseif($goods_receipt->approval == 0){
					$status = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
					}elseif($goods_receipt->approval == -1){
					$status = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
				}
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold">'. _l('withdrawals').'</td>
				<td><a href="' . admin_url('warehouse/view_purchase/' . $goods_receipt->id) . '" target="_blank">' . $goods_receipt->goods_receipt_code . '</a></td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('accounting_date').'</td>
				<td>'. _d($goods_receipt->date_c)  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('status').'</td>
				<td>'. $status .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('total_tax_money').'</td>
				<td>'. app_format_money($goods_receipt->total_tax_money, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('total_goods_money').'</td>
				<td>'. app_format_money($goods_receipt->total_goods_money, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('value_of_inventory').'</td>
				<td>'. app_format_money($goods_receipt->value_of_inventory, $currency->name) .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('total_money').'</td>
				<td>'. app_format_money($goods_receipt->total_money, $currency->name) .'</td>
				</tr>
				</tbody>
				</table>';
				
				if($goods_receipt_detail){
					$payment_account = get_option('acc_wh_stock_import_payment_account');
					$deposit_to = get_option('acc_wh_stock_import_deposit_to');
					
					$html .= '<h4>'._l('list_of_items').'</h4>';
					
					foreach ($goods_receipt_detail as $value) {
						
						$this->db->where('id', $value['commodity_code']);
						$item = $this->db->get(db_prefix().'items')->row();
						
						$item_description = '';
						if(isset($item) && isset($item->commodity_code) && isset($item->description)){
							$item_description = $item->commodity_code.' - '.$item->description;
						}
						
						$item_id = 0;
						if(isset($item->id)){
							$item_id = $item->id;
						}
						
						if($item_id == 0){
							continue;
						}
						
						$list_item[] = $item_id;
						
						$this->db->where('rel_id', $id);
						$this->db->where('rel_type', $type);
						$this->db->where('item', $item_id);
						$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
						
						foreach ($account_history as $key => $val) {
							if($val['debit'] > 0){
								$debit = $val['account'];
							}
							
							if($val['credit'] > 0){
								$credit =  $val['account'];
							}
						}
						
						if($account_history){
							$html .= '
							<div class="div_content">
							<h5>'.$item_description.'</h5>
							<div class="row">
							'.form_hidden('item_amount['.$item_id.']', $value['goods_money']).'
							<div class="col-md-6"> '.
							render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
							</div>
							<div class="col-md-6">
							'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
							</div>
							</div>
							</div>';
							}else{
							$item_automatic = $this->accounting_model->get_item_automatic($item_id);
							
							if($item_automatic){
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $value['goods_money']).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$item_automatic->inventory_asset_account,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
								}else{
								
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $value['goods_money']).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
							}
						}
					}
				}
				
				$debit = 0;
				$credit = 0;
				}elseif ($type == 'loss_adjustment') {
				$accounts = $this->accounting_model->get_accounts();
				
				$this->load->model('warehouse/warehouse_model');
				
				$loss_adjustment = $this->warehouse_model->get_loss_adjustment($id);
				$loss_adjustment_detail = $this->warehouse_model->get_loss_adjustment_detailt_by_masterid($id);
				
				$banking = $this->accounting_model->get_transaction_banking($id);
				
				$status = '';
				
				if ((int) $loss_adjustment->status == 0) {
					$status = '<div class="btn btn-warning" >' . _l('draft') . '</div>';
					} elseif ((int) $loss_adjustment->status == 1) {
					$status = '<div class="btn btn-success" >' . _l('Adjusted') . '</div>';
					} elseif((int) $loss_adjustment->status == -1){
					
					$status = '<div class="btn btn-danger" >' . _l('reject') . '</div>';
				}
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold">'. _l('type').'</td>
				<td><a href="' . admin_url('warehouse/view_lost_adjustment/' . $loss_adjustment->id) . '" target="_blank">' . _l($loss_adjustment->type) . '</a></td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('_time').'</td>
				<td>'. _d($loss_adjustment->time)  .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('status').'</td>
				<td>'. $status .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('reason').'</td>
				<td>'. html_entity_decode($loss_adjustment->reason) .'</td>
				</tr>
				</tbody>
				</table>';
				
				if($loss_adjustment_detail){
					$decrease_payment_account = get_option('acc_wh_decrease_payment_account');
					$decrease_deposit_to = get_option('acc_wh_decrease_deposit_to');
					
					$increase_payment_account = get_option('acc_wh_increase_payment_account');
					$increase_deposit_to = get_option('acc_wh_increase_deposit_to');
					
					
					$html .= '<h4>'._l('list_of_items').'</h4>';
					
					foreach ($loss_adjustment_detail as $value) {
						if($value['current_number'] < $value['updates_number']){
							$number = $value['updates_number'] - $value['current_number'];
							$payment_account = $increase_payment_account;
							$deposit_to = $increase_deposit_to;
							}else{
							$number = $value['current_number'] - $value['updates_number'];
							$payment_account = $decrease_payment_account;
							$deposit_to = $decrease_deposit_to;
						}
						
						$this->db->where('id', $value['items']);
						$item = $this->db->get(db_prefix().'items')->row();
						
						$item_description = '';
						if(isset($item) && isset($item->commodity_code) && isset($item->description)){
							$item_description = $item->commodity_code.' - '.$item->description;
						}
						
						$item_id = 0;
						if(isset($item->id)){
							$item_id = $item->id;
						}
						
						if($item_id == 0){
							continue;
						}
						$list_item[] = $item_id;
						
						$this->db->where('rel_id', $id);
						$this->db->where('rel_type', $type);
						$this->db->where('item', $item_id);
						$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
						
						foreach ($account_history as $key => $val) {
							if($val['debit'] > 0){
								$debit = $val['account'];
							}
							
							if($val['credit'] > 0){
								$credit =  $val['account'];
							}
						}
						
						$price = 0;
						if($value['lot_number'] != ''){
							$this->db->where('lot_number', $value['lot_number']);
							$this->db->where('expiry_date', $value['expiry_date']);
							$receipt_detail = $this->db->get(db_prefix().'goods_receipt_detail')->row();
							if($receipt_detail){
								$price = $receipt_detail->unit_price;
								}else{
								$this->db->where('id' ,$item_id);
								$item = $this->db->get(db_prefix().'items')->row();
								if($item){
									$price = $item->purchase_price;
								}
							}
							}else{
							$this->db->where('id' ,$item_id);
							$item = $this->db->get(db_prefix().'items')->row();
							if($item){
								$price = $item->purchase_price;
							}
						}
						
						if($account_history){
							$html .= '
							<div class="div_content">
							<h5>'.$item_description.'</h5>
							<div class="row">
							'.form_hidden('item_amount['.$item_id.']', $number * $price).'
							<div class="col-md-6"> '.
							render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$credit,array(),array(),'','',false) .'
							</div>
							<div class="col-md-6">
							'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$debit,array(),array(),'','',false).'
							</div>
							</div>
							</div>';
							}else{
							$item_automatic = $this->accounting_model->get_item_automatic($item_id);
							
							if($item_automatic){
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $number * $price).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$item_automatic->inventory_asset_account,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
								}else{
								
								$html .= '
								<div class="div_content">
								<h5>'.$item_description.'</h5>
								<div class="row">
								'.form_hidden('item_amount['.$item_id.']', $number * $price).'
								<div class="col-md-6"> '.
								render_select('payment_account['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
								</div>
								<div class="col-md-6">
								'. render_select('deposit_to['.$item_id.']',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
								</div>
								</div>
								</div>';
							}
						}
					}
				}
				
				$debit = 0;
				$credit = 0;
				}elseif ($type == 'opening_stock') {
				
				$accounts = $this->accounting_model->get_accounts();
				$opening_stock = $this->accounting_model->get_opening_stock_data($id);
				$deposit_to = get_option('acc_wh_opening_stock_deposit_to');
				$payment_account = get_option('acc_wh_opening_stock_payment_account');
				$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
				
				$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold">'. _l('commodity_code').'</td>
				<td><a href="' . admin_url('warehouse/view_commodity_detail/' . $opening_stock->id) . '" target="_blank">' . $opening_stock->commodity_code . '</a></td>
				</tr>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('commodity_name').'</td>
				<td>'. $opening_stock->description .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('sku_code').'</td>
				<td>'. $opening_stock->sku_code .'</td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('opening_stock').'</td>
				<td>'. app_format_money($opening_stock->opening_stock, $currency->name) .'</td>
				</tr>
				</tbody>
				</table><br>';
				
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', $type);
				$this->db->where('date >= "'.$date_financial_year.'"');
				$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
				
				foreach ($account_history as $key => $value) {
					if($value['debit'] > 0){
						$deposit_to = $value['account'];
					}
					
					if($value['credit'] > 0){
						$payment_account =  $value['account'];
					}
				}
				
				$html .= '
				<div class="row">
				<div class="col-md-6"> '.
				render_select('payment_account',$accounts,array('id','name', 'account_type_name'),'payment_account',$payment_account,array(),array(),'','',false) .'
				</div>
				<div class="col-md-6">
				'. render_select('deposit_to',$accounts,array('id','name', 'account_type_name'),'deposit_to',$deposit_to,array(),array(),'','',false).'
				</div>
				</div>';
				
				$debit = 0;
				$credit = 0;
				}elseif($type == 'purchase_payment'){
				$this->load->model('purchase/purchase_model');
				$payment = $this->purchase_model->get_payment_pur_invoice($id);
				
				$invoice = $this->purchase_model->get_pur_invoice($payment->pur_invoice);
				
				$html = '<table class="table border table-striped no-margin">
				<tbody>
				<tr class="project-overview">
				<td class="bold" width="30%">'. _l('purchase_order').'</td>
				<td>'.'<a href="'.admin_url('purchase/purchase_order/'.$invoice->pur_order).'">'.get_pur_order_subject($invoice->pur_order).'</a>' .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('acc_amount').'</td>
				<td>'. app_format_money($payment->amount, $currency->name) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('expense_dt_table_heading_date').'</td>
				<td>'. _d($payment->date) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('payment_modes').'</td>
				<td>'. get_payment_mode_name_by_id($payment->paymentmode) .'</td>
				<td></td>
				</tr>
				<tr class="project-overview">
				<td class="bold">'. _l('note').'</td>
				<td colspan="2">'. html_entity_decode($payment->note) .'</td>
				</tr>';
				$amount = 1;
				
				
				$html .=   '</tbody>
				</table>';
				
				$debit = get_option('acc_pur_payment_deposit_to');
				$credit = get_option('acc_pur_payment_payment_account');
			}
			
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', $type);
			$this->db->where('tax', 0);
			$account_history = $this->db->get(db_prefix(). 'acc_account_history')->result_array();
			
			foreach ($account_history as $key => $value) {
				if($value['debit'] > 0){
					$debit = $value['account'];
				}
				
				if($value['credit'] > 0){
					$credit =  $value['account'];
				}
			}
			
			echo json_encode(['html' => $html, 'debit' => $debit, 'credit' => $credit, 'list_item' => $list_item]);
			die();
		}
		
		/**
			* convert
			* @return json 
		*/
		public function convert(){
			if (!has_permission_new('accounting_transaction', '', 'create')) {
				access_denied('accounting');
			}
			$data = $this->input->post();
			$success = $this->accounting_model->add_account_history($data);
			if ($success) {
				$message = _l('successfully_converted');
				}else {
				$message = _l('conversion_failed');
			}
			echo json_encode(['success' => $success, 'message' => $message]);
			die();
		}
		
		/**
			* transfer
			* @return view
		*/
		public function transfer(){
			if (!has_permission_new('accounting_transfer', '', 'view')) {
				access_denied('accounting');
			}
			$data['title']         = _l('transfer');
			$data['accounts'] = $this->accounting_model->get_accounts('', 'find_in_set(account_type_id, "2,3,4,5,7,8,9,10")');
			
			$this->load->view('transfer/manage', $data);
		}
		
		/**
			* accounts table
			* @return json
		*/
		public function transfer_table()
		{
			if ($this->input->is_ajax_request()) {
				$accounts = $this->accounting_model->get_accounts();
				$account_name = [];
				
				foreach ($accounts as $key => $value) {
					$account_name[$value['id']] = $value['name'];
				}
				
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$select = [
				'1', // bulk actions
				'id',
				'transfer_funds_from',
				'transfer_funds_to',
				'transfer_amount',
				];
				
				$where = [];
				
				if ($this->input->post('ft_transfer_funds_from')) {
					$ft_transfer_funds_from = $this->input->post('ft_transfer_funds_from');
					array_push($where, 'AND transfer_funds_from IN (' . implode(', ', $ft_transfer_funds_from) . ')');
				}
				
				if ($this->input->post('ft_transfer_funds_to')) {
					$ft_transfer_funds_to = $this->input->post('ft_transfer_funds_to');
					array_push($where, 'AND transfer_funds_to IN (' . implode(', ', $ft_transfer_funds_to) . ')');
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (date >= "' . $from_date . '" and date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (date <= "' . $to_date . '")');
				}
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_transfers';
				$join         = [];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					$categoryOutput = (isset($account_name[$aRow['transfer_funds_from']]) ? $account_name[$aRow['transfer_funds_from']] : '');
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_transfer', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_transfer(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
					}
					
					if (has_permission_new('accounting_transfer', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_transfer/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					$row[] = (isset($account_name[$aRow['transfer_funds_to']]) ? $account_name[$aRow['transfer_funds_to']] : '');
					$row[] = app_format_money($aRow['transfer_amount'], $currency->name);
					$row[] = _d($aRow['date']);
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* add transfer
			* @return json
		*/
		public function add_transfer(){
			$data = $this->input->post();
			$data['description'] = $this->input->post('description', false);
			if($data['id'] == ''){
				if (!has_permission_new('accounting_transfer', '', 'create')) {
					access_denied('accounting');
				}
				$success = $this->accounting_model->add_transfer($data);
				if ($success === 'close_the_book') {
					$message = _l('has_closed_the_book');
					}elseif($success){
					$message = _l('successfully_transferred');
					}else {
					$message = _l('transfer_failed');
				}
				}else{
				if (!has_permission_new('accounting_transfer', '', 'edit')) {
					access_denied('accounting');
				}
				$id = $data['id'];
				unset($data['id']);
				$success = $this->accounting_model->update_transfer($data, $id);
				if ($success === 'close_the_book') {
					$message = _l('has_closed_the_book');
					}elseif ($success) {
					$message = _l('updated_successfully', _l('transfer'));
				}
			}
			echo json_encode(['success' => $success, 'message' => $message]);
			die();
		}
		
		/**
			* journal entry
			* @return view
		*/
		public function journal_entry(){
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				access_denied('accounting');
			}
			$data['title']         = _l('journal_entry');
			$data['accounts'] = $this->accounting_model->get_accounts();
			$data['accounts_to_select'] = $this->accounting_model->get_data_account_to_select();
			$this->load->view('journal_entry/manage', $data);
		}
		
		/**
			* journal entry
			* @return view
		*/
		public function account_group_master(){
			/*if (!has_permission_new('account_group', '', 'view')) {
				access_denied('accounting');
			}*/
			$data['title']         = "Account Group Master";
			$data['accounts'] = $this->accounting_model->get_accounts();
			$data['accounts_to_select'] = $this->accounting_model->get_data_account_to_select();
			$this->load->view('group_master/manage', $data);
		}
		
		/**
			* journal entry table
			* @return json
		*/
		public function journal_entry_table(){
			if ($this->input->is_ajax_request()) {
				
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$select = [
				'1', // bulk actions
				'id',
				'number',
				'journal_date',
				];
				
				$where = [];
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (journal_date >= "' . $from_date . '" and journal_date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (journal_date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (journal_date <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_journal_entries';
				$join         = [];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['amount', 'description']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					$categoryOutput = _d($aRow['journal_date']);
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_journal_entry', '', 'edit')) {
						$categoryOutput .= '<a href="' . admin_url('accounting/journal_entry_export/' . $aRow['id']) . '" class="text-success">' . _l('acc_export_excel') . '</a>';
					}
					
					if (has_permission_new('accounting_journal_entry', '', 'edit')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/new_journal_entry/' . $aRow['id']) . '">' . _l('edit') . '</a>';
					}
					
					if (has_permission_new('accounting_journal_entry', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_journal_entry/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					if(strlen($aRow['number'].' - '.html_entity_decode($aRow['description'])) > 150){
						$row[] = '<div data-toggle="tooltip" data-title="'. $aRow['number'].' - '.html_entity_decode(strip_tags($aRow['description'])).'">'.substr($aRow['number'].' - '.html_entity_decode($aRow['description']), 0, 150).'...</div>';
						}else{
						$row[] = $aRow['number'].' - '.html_entity_decode($aRow['description']);
					}
					$row[] = app_format_money($aRow['amount'], $currency->name);
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* journal entry table
			* @return json
		*/
		public function group_master_table(){
			if ($this->input->is_ajax_request()) {
				
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$select = [
				'1', // bulk actions
				db_prefix().'accountgroupssub.SubActGroupName as subgroupName',
				];
				
				$where = [];
				
				$join = [
				'INNER JOIN '.db_prefix().'accountgroups ON '.db_prefix().'accountgroupssub.ActGroupID='.db_prefix().'accountgroups.ActGroupID',
				];
				$from_date = '';
				$to_date   = '';
				/*if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
					$from_date = to_sql_date($from_date);
					}
				}*/
				
				/*if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
					$to_date = to_sql_date($to_date);
					}
				}*/
				/* if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (journal_date >= "' . $from_date . '" and journal_date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (journal_date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (journal_date <= "' . $to_date . '")');
				}*/
				
				$aColumns     = $select;
				$sIndexColumn = 'SubActGroupID';
				$sTable       = db_prefix() . 'accountgroupssub';
				// $join         = [];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['SubActGroupID',
				db_prefix().'accountgroups.PrimaryGroupYN AS prmgrpYN',
				db_prefix().'accountgroups.ActGroupTypeID AS actgrptype',
				db_prefix().'accountgroups.ActGroupName AS grpName',]);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				$i = 1;
				foreach ($rResult as $aRow) {
					$row   = [];
					
					$row[] = $i;
					//$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['SubActGroupID'] . '"><label></label></div>';
					/*$categoryOutput = _d($aRow['journal_date']);
						
						$categoryOutput .= '<div class="row-options">';
						
						if (has_permission_new('accounting_journal_entry', '', 'edit')) {
						$categoryOutput .= '<a href="' . admin_url('accounting/journal_entry_export/' . $aRow['id']) . '" class="text-success">' . _l('acc_export_excel') . '</a>';
						}
						
						if (has_permission_new('accounting_journal_entry', '', 'edit')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/new_journal_entry/' . $aRow['id']) . '">' . _l('edit') . '</a>';
						}
						
						if (has_permission_new('accounting_journal_entry', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_journal_entry/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
						}
						
						$categoryOutput .= '</div>';
					$row[] = $categoryOutput;*/
					/*if(strlen($aRow['number'].' - '.html_entity_decode($aRow['description'])) > 150){
						$row[] = '<div data-toggle="tooltip" data-title="'. $aRow['number'].' - '.html_entity_decode(strip_tags($aRow['description'])).'">'.substr($aRow['number'].' - '.html_entity_decode($aRow['description']), 0, 150).'...</div>';
						}else{
						$row[] = $aRow['number'].' - '.html_entity_decode($aRow['description']);
						}
					$row[] = app_format_money($aRow['amount'], $currency->name);*/
					
					$row[] = $aRow['subgroupName'];
					$row[] = $aRow['prmgrpYN'];
					
					if($aRow['actgrptype'] == "L"){
						$row[] = "LIABILITY";
						}else {
						$row[] = "ASSETS";
					}
					$row[] = $aRow['grpName'];
					
					
					$output['aaData'][] = $row;
					$i = $i + 1;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* add journal entry
			* @return view
		*/
		public function new_journal_entry($id = ''){
			
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				access_denied('accounting_journal_entry');
			}
			if ($this->input->post()) {
				
				$data = $this->input->post();
				// die;
				if($id == ''){
					if (!has_permission_new('accounting_journal_entry', '', 'create')) {
						access_denied('accounting_journal_entry');
					}
					
					$success = $this->accounting_model->add_journal_entry($data);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('added_successfully', _l('journal_entry')));
					}
					}else{
					if (!has_permission_new('accounting_journal_entry', '', 'edit')) {
						access_denied('accounting_journal_entry');
					}
					
					$success = $this->accounting_model->update_journal_entry($data, $id);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('updated_successfully', _l('journal_entry')));
					}
				}
				redirect(admin_url('accounting/new_journal_entry'));
			}
			
			if($id != ''){
				$data['journal_entry'] = $this->accounting_model->get_journal_entry($id);
				
			}
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['next_number'] = $this->accounting_model->get_journal_entry_next_number();
			//$data['alljournal_entry'] = $this->accounting_model->getall_journal_entry();
			$data['title'] = _l('journal_entry');
			$dr_cr = [];
			$d_c = [];
			$d_c['id'] = 'C';
			$d_c['label'] = "C";
			$dr_cr[] = $d_c;
			
			$d_c = [];
			$d_c['id'] = 'D';
			$d_c['label'] = "D";
			$dr_cr[] = $d_c;
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select_for_journal();
			$data['dr_cr'] = $dr_cr;
			/*echo "<pre>";
				print_r($data);
			die;*/
			$this->load->view('journal_entry/journal_entry', $data);
		}
		
		public function AccountChange($val){
			$value = $this->accounting_model->AccountChange($val);
			echo json_encode([
			'value' => $value
			]);
		}
		public function AccountChangeForContra($val){
			$value = $this->accounting_model->AccountChangeForContra($val);
			echo json_encode([
			'value' => $value
			]);
		}
		
		public function load_data()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$data = $this->accounting_model->load_data($data);
			echo json_encode($data);
		}
		
		
		public function load_data_for_payment()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'Status' => $this->input->post('Isapprove')
			);
			
			$data = $this->accounting_model->load_data_for_payment($data);
			echo json_encode($data);
		}
		
		public function load_data_for_VoucherEntry()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'Status' => $this->input->post('Isapprove'),
			'PassedFrom' => $this->input->post('PassedFrom')
			);
			
			$data = $this->accounting_model->load_data_for_VoucherEntry($data);
			echo json_encode($data);
		}
		
		
		public function load_data_for_receipts()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'entryStatus'  => $this->input->post('entryStatus')
			);
			$data = $this->accounting_model->load_data_for_receipts($data);
			echo json_encode($data);
		}
		public function load_data_for_contra()
		{
			$data = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date')
			);
			$data = $this->accounting_model->load_data_for_contra($data);
			echo json_encode($data);
		}
		
		/**
			* add Contra entry
			* @return view
		*/
		public function new_contra_entry($id = ''){
			if (!has_permission_new('accounting_contra_entry', '', 'view')) {
				access_denied('accounting_contra_entry');
			}
			if ($this->input->post()) { 
				$data                = $this->input->post();
				
				if($id == ''){
					if (!has_permission_new('accounting_contra_entry', '', 'create')) {
						access_denied('accounting_contra_entry');
					}
					$success = $this->accounting_model->add_contra_entry($data);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('added_successfully', "Contra"));
					}
					}else{
					if (!has_permission_new('accounting_contra_entry', '', 'edit')) {
						access_denied('accounting_contra_entry');
					}
					$success = $this->accounting_model->update_contra_entry($data, $id);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('updated_successfully', _l('journal_entry')));
					}
				}
				redirect(admin_url('accounting/new_contra_entry'));
			}
			
			if($id != ''){
				$data['contra_entry'] = $this->accounting_model->get_contra_entry($id);
				
			}
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['title'] = "Contra Entry";
			$dr_cr = [];
			$d_c = [];
			$d_c['id'] = 'C';
			$d_c['label'] = "C";
			$dr_cr[] = $d_c;
			
			$d_c = [];
			$d_c['id'] = 'D';
			$d_c['label'] = "D";
			$dr_cr[] = $d_c;
			$data['dr_cr'] = $dr_cr;
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select_for_contra();
			
			$this->load->view('contra_entry/contra_entry', $data);
		}
		
		//======================= New Receipt Entry Page Load ==========================
		public function new_receipt_entry($id = '')
		{			
			if (!has_permission_new('accounting_receipt_entry', '', 'view')) {
				access_denied('accounting_receipt_entry');
			}
			if ($this->input->post()) {
				$data = $this->input->post();
				/*echo "<pre>";
					print_r($data);
				die;*/
				// die;
				if($id == ''){
					if (!has_permission_new('accounting_receipt_entry', '', 'create')) {
						access_denied('accounting_receipt_entry');
					}
					$success = $this->accounting_model->add_receipts_entry($data);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('added_successfully', "Receipts Entry "));
					}
					}else{
					if (!has_permission_new('accounting_receipt_entry', '', 'edit')) {
						access_denied('accounting_receipt_entry');
					}
					$success = $this->accounting_model->update_receipts_entry($data, $id);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('updated_successfully', _l('journal_entry')));
					}
				}
				redirect(admin_url('accounting/new_receipt_entry'));
			}
			
			$data['title'] = "Add Receipts Entry";
			if($id != ''){
				$data['receipts_entry'] = $this->accounting_model->get_receipts_entry($id);
				$data['title'] = "Edit Receipts Entry";
			}
			
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select_for_receipts();
			$data['genral_account_to_select'] = $this->accounting_model->get_data_ganeral_account_to_select();
			
			$this->load->view('receipt/new_receiptUpdated', $data);
		}
		//============= Add Edit Payment Entry Page Load ===============================
		public function new_payment_entry($id = '')
		{
			if (!has_permission_new('accounting_payment_entry', '', 'view')) {
				access_denied('accounting_payment_entry');
			}
			if ($this->input->post()) {
				$data = $this->input->post();
				
				if($id == ''){
					if (!has_permission_new('accounting_payment_entry', '', 'create')) {
						access_denied('accounting_payment_entry');
					}
					$success = $this->accounting_model->add_payment_entry($data);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('added_successfully', 'Payment Entry'));
					}
					}else{
					if (!has_permission_new('accounting_payment_entry', '', 'edit')) {
						access_denied('accounting_payment_entry');
					}
					$success = $this->accounting_model->update_payments_entry($data, $id);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('updated_successfully', _l('journal_entry')));
					}
				}
				redirect(admin_url('accounting/new_payment_entry'));
			}
			
			if($id != ''){
				$data['payment_entry'] = $this->accounting_model->get_payments_entry($id);				
			}
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['title'] = "Add Edit Payment Voucher";
			
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select_for_payment();
			$data['genral_account_to_select'] = $this->accounting_model->get_data_ganeral_account_to_select();
			$this->load->view('payment/new_paymentUpdated', $data);			
		}
		
		
		
		/**
			* add Payment entry
			* @return view
		*/
		public function voucher_register_new($id = ''){
			$data['title'] = "Voucher Register";
			$data['type_of_voucher'] = $this->accounting_model->get_type_of_voucher();
			$this->load->view('voucher_register/manage', $data);
		}
		
		public function voucher_register_report(){
			
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				$filterdata = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'  => $this->input->post('to_date'),
				'voucher_type'  => $this->input->post('voucher_type')
				);
				$voucher_type = $this->input->post('voucher_type');
				$from_date = $this->input->post('from_date');
				$to_date = $this->input->post('to_date');
				
				$table_data = $this->accounting_model->get_voucher_data($filterdata);
				$data_for_pay_rec = $this->accounting_model->get_for_pay_rec($filterdata);
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();
				
				$writer = new XLSXWriter();
				
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 9);  //merge cells
					
					}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
					
					}else if($voucher_type == "PURCHASE"){
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 13);  //merge cells
					
					}else if($voucher_type == "SALE"){
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 10);  //merge cells 
				}
				$writer->writeSheetRow('Sheet1', $company_addr);
				
				$msg = "Voucher Register ".$this->input->post('from_date')." To " .$this->input->post('to_date')." Book :".$voucher_type;
				$filter = array($msg);
				if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 9);  //merge cells
					}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
					}else if($voucher_type == "PURCHASE"){
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 13);  //merge cells
					}else if($voucher_type == "SALE"){
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 10);  //merge cells
				}
				
				$writer->writeSheetRow('Sheet1', $filter);
				
				// empty row
				$list_add = [];
				if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					}else if($voucher_type == "PURCHASE"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					}else if($voucher_type == "SALE"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
				}
				
				$writer->writeSheetRow('Sheet1', $list_add);
				
				
				$set_col_tk = [];
				if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
					
					$set_col_tk["Voucher_No"] =  'Voucher No';
					$set_col_tk["Voucher_Date"] = 'Voucher Date';
					$set_col_tk["Account_Code"] = 'Account Code';
					$set_col_tk["Account_Name"] = 'Account Name';
					$set_col_tk["Dr/Cr"] = '    Dr/Cr';
					$set_col_tk["Debit_Amount"] = 'Debit Amount';
					$set_col_tk["Credit_Amount"] = 'Credit Amount';
					$set_col_tk["Narration"] = 'Narration';
					$set_col_tk["Address"] = 'Address';
					
					}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
					
					$set_col_tk["Passed_From"] =  'Passed From';
					$set_col_tk["Voucher_Date"] = 'Voucher Date';
					$set_col_tk["Voucher_No"] = 'Voucher No';
					$set_col_tk["Account_Name"] = 'Account Name';
					$set_col_tk["Amount"] = '   Amount';
					$set_col_tk["Second_Account_Name"] = 'Second Account Name';
					$set_col_tk["Description"] = 'Description';
					$set_col_tk["Address"] = 'Address';
					
					}else if($voucher_type == "PURCHASE"){
					
					$set_col_tk["Voucher_No"] =  'Voucher No';
					$set_col_tk["Voucher_Date"] = 'Voucher Date';
					$set_col_tk["Invoice_No"] = 'Invoice No';
					$set_col_tk["Party_Name"] = 'Party Name';
					$set_col_tk["PurchAmt"] = 'PurchAmt';
					$set_col_tk["Discount"] = 'Discount';
					$set_col_tk["Excise"] = 'Excise';
					$set_col_tk["CST"] = 'CST';
					$set_col_tk["TaxAmt"] = 'TaxAmt';
					$set_col_tk["Claim"] = 'Claim';
					$set_col_tk["Freight"] = 'Freight';
					$set_col_tk["RoundOff"] = 'RoundOff';
					$set_col_tk["InvoiceAmt"] = 'InvoiceAmt';
					
					}else if($voucher_type == "SALE"){
					
					$set_col_tk["Voucher_No"] =  'Voucher No';
					$set_col_tk["Voucher_Date"] = 'Voucher Date';
					$set_col_tk["Party_Name"] = 'Party Name';
					$set_col_tk["Address"] = 'Address';
					$set_col_tk["Sale_Amount"] = '  Sale Amount';
					$set_col_tk["Disc_Amount"] = 'Disc Amount';
					$set_col_tk["Tax_Amount"] = 'Tax Amount';
					$set_col_tk["Claim_Amount"] = 'Claim Amount';
					$set_col_tk["RoundOff"] = 'RoundOff';
					$set_col_tk["Bill Amount"] = 'Bill Amount';
				}
				
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				$Payment_sum = 0;
				$purch_amt_total = 0.00;
				$DiscAmt = 0.00;
				$TaxAmt = 0.00;
				$freight_amt_total = 0.00;
				$round_off1 = 0.00;
				$InvAmt = 0.00;
				foreach ($table_data as $k => $value) {
					$tax_sale = 0.00;
					
					$list_add = [];
					if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
						$list_add[] = $value["VoucherID"];
						$date = substr($value['Transdate'],0,10);
						$list_add[] = _d($date);
						$list_add[] = $value["AccountID"];
						if($value['company'] == null){
							$accountName = $value['firstname']." ".$value['lastname'];
							}else{
							$accountName = $value['company'];
						}
						$list_add[] = $accountName;
						if($value['TType'] == "C"){
							$credit_amt = $value['Amount'];
							$debit_amt = "";
							$dr_cr = "Cr";
							$total_credit = $total_credit + $value['Amount'];
							}else {
							$credit_amt = "";
							$debit_amt = $value['Amount'];
							$dr_cr = "Dr";
							$total_debit = $total_debit + $value['Amount'];
						}
						$list_add[] = $dr_cr;
						$list_add[] = number_format($debit_amt,2);
						$list_add[] = number_format($credit_amt,2);
						$list_add[] = $value["Narration"];
						$list_add[] = $value["address"];
						}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
						$list_add[] = $value["PassedFrom"];
						$date = substr($value['Transdate'],0,10);
						$list_add[] = _d($date);
						$list_add[] = $value["VoucherID"];
						foreach ($data_for_pay_rec as $key1 => $value1) {
							if($value['VoucherID'] == $value1['VoucherID'] && $value['Amount'] == $value1['Amount'] && $value['Narration'] == $value1['Narration']){
								if($value1['company'] == null){
									$accountName1 = $value1['firstname']." ".$value1['lastname'];
									}else{
									$accountName1 = $value1['company'];
								}
							}
						}
						$list_add[] = $accountName1;
						$list_add[] = $value["Amount"];
						$Payment_sum = $Payment_sum + $value['Amount'];
						if($value['company'] == null){
							$accountName2 = $value['firstname']." ".$value['lastname'];
							}else{
							$accountName2 = $value['company'];
						}
						$list_add[] = $accountName2;
						$list_add[] = $value["Narration"];
						$list_add[] = $value["address"];
						}else if($voucher_type == "PURCHASE"){
						$list_add[] = $value["PurchID"];
						$date = substr($value['Transdate'],0,10);
						$list_add[] = _d($date);
						$list_add[] = $value["Invoiceno"];
						$list_add[] = $value["company"];
						$list_add[] = $value["Purchamt"];
						$purch_amt_total = $purch_amt_total + $value['Purchamt'];
						$list_add[] = number_format($value['Discamt'],2);
						$DiscAmt = $DiscAmt + $value['Discamt'];
						$list_add[] = number_format($value['Excamt'],2);
						$list_add[] = number_format($value['Cstamt'],2);
						if($value['sgstamt']!=0 || $value['cgstamt']!=0){
							$tax= $value['sgstamt'] + $value['cgstamt'];
							}else{
							$tax= $value['igstamt'];
						}
						$list_add[] = number_format($tax,2);
						$TaxAmt = $TaxAmt + $tax;
						$list_add[] = "";
						$list_add[] = number_format($value['Frtamt'],2);
						$freight_amt_total = $freight_amt_total + $value['Frtamt'];
						$list_add[] = number_format($value['RoundOffAmt'],2);
						$round_off1 = $round_off1 + $value['RoundOffAmt'];
						$list_add[] = number_format($value['Invamt'],2);
						$InvAmt = $InvAmt + $value['Invamt'];
						}else if($voucher_type == "SALE"){
						$list_add[] = $value["SalesID"];
						$date = substr($value['Transdate'],0,10);
						$list_add[] = _d($date);
						$list_add[] = $value["company"];
						$list_add[] = $value["address"];
						$list_add[] = number_format($value['SaleAmt'],2);
						$list_add[] = number_format($value['DiscAmt'],2);
						if($value['sgstamt']!=0 || $value['cgstamt']!=0){
							$tax_sale= $value['sgstamt'] + $value['cgstamt'];
							}else{
							$tax_sale= $value['igstamt'];
						}
						
						$list_add[] = number_format($tax_sale,2);
						$list_add[] = "";
						$roundff=($value['BillAmt']-$value['RndAmt']);
						$round_off=round($roundff,2);
						$list_add[] = number_format($round_off,2);
						$list_add[] = number_format($value['BillAmt'],2);
						$SaleAmt = $SaleAmt + $value['SaleAmt'];
						$DiscAmt = $DiscAmt + $value['DiscAmt'];
						$TaxAmt += $tax_sale;
						$BillAmt = $BillAmt + $value['BillAmt'];
						$round_off1 += $round_off;
					}
					$writer->writeSheetRow('Sheet1', $list_add);
					
				}
				$list_add = [];
				if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "Total";
					$list_add[] = "";
					$list_add[] = number_format($total_debit,2);
					$list_add[] = number_format($total_credit,2);
					$list_add[] = "";
					$list_add[] = "";
					}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "Total";
					$list_add[] = $Payment_sum;
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					}else if($voucher_type == "PURCHASE"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "Total";
					$list_add[] = number_format($purch_amt_total,2);
					$list_add[] = number_format($DiscAmt,2);
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = number_format($TaxAmt,2);
					$list_add[] = "";
					$list_add[] = number_format($freight_amt_total,2);
					$list_add[] = number_format($round_off1,2);
					$list_add[] = number_format($InvAmt,2);
					}else if($voucher_type == "SALE"){
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "Total";
					$list_add[] = number_format($SaleAmt,2);
					$list_add[] = number_format($DiscAmt,2);
					$list_add[] = number_format($TaxAmt,2);
					$list_add[] = "";
					$list_add[] = number_format($round_off1,2);
					$list_add[] = "";
					$list_add[] = number_format($BillAmt,2);
				}
				
				$writer->writeSheetRow('Sheet1', $list_add);
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'Voucher_register_report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		
		public function get_data()
		{
			$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'  => $this->input->post('to_date'),
			'voucher_type'  => $this->input->post('voucher_type')
			);
			$voucher_type = $this->input->post('voucher_type');
			$from_date = $this->input->post('from_date');
			$to_date = $this->input->post('to_date');
			$selected_company = $this->session->userdata('root_company');
			$table_data = $this->accounting_model->get_voucher_data($filterdata);
			//echo json_encode($table_data);
			/* print_r($table_data);
			die;*/
			$data_for_pay_rec = $this->accounting_model->get_for_pay_rec($filterdata);
			$company_details = $this->accounting_model->get_company_detail1($selected_company);
			$html = '';
			$html .= '<table class="table-striped table-bordered voucher_register" id="voucher_register" width="100%">';
			$html .= '<thead style="font-size:11px;">';
			$html .= '<tr style="display:none;">';
			$html .= '<th style="text-align:center;" colspan="6"><b>'.$company_details->company_name.'</b></th>';
			$html .= '</tr>';
			$html .= '<tr style="display:none;">';
			$html .= '<th style="text-align:center;" colspan="6"><b>'.$company_details->address.'</b></th>';
			$html .= '</tr>';
			$html .= '<tr style="display:none;">';
			$html .= '<th style="text-align:left;" colspan="6"><b>Date:</b> '.$from_date.' To '.$to_date.' , <b>Book:</b> '.$voucher_type.'</th>';
			$html .= '</tr>';
			$html .= '<tr>';
			if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
				$html .= '<th Class="sortablePop">Voucher No</th>';
				$html .= '<th Class="sortablePop">Voucher Date</th>';
				$html .= '<th Class="sortablePop">Account Code</th>';
				$html .= '<th Class="sortablePop">Account Name</th>';
				$html .= '<th Class="sortablePop">Dr/Cr</th>';
				$html .= '<th Class="sortablePop">Debit Amount</th>';
				$html .= '<th Class="sortablePop">Credit Amount</th>';
				$html .= '<th Class="sortablePop">Narration</th>';
				$html .= '<th Class="sortablePop">Address</th>';
				}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
				$html .= '<th Class="sortablePop">Passed From</th>';
				$html .= '<th Class="sortablePop">Voucher Date</th>';
				$html .= '<th Class="sortablePop">Voucher No</th>';
				$html .= '<th Class="sortablePop">Account Name</th>';
				$html .= '<th Class="sortablePop">Amount</th>';
				$html .= '<th Class="sortablePop">Second Account Name</th>';
				$html .= '<th Class="sortablePop">Description</th>';
				$html .= '<th Class="sortablePop">Address</th>';
				}else if($voucher_type == "PURCHASE"){
				$html .= '<th Class="sortablePop">Voucher No</th>';
				$html .= '<th Class="sortablePop">Voucher Date</th>';
				$html .= '<th Class="sortablePop">Invoice No</th>';
				$html .= '<th Class="sortablePop">Party Name</th>';
				$html .= '<th Class="sortablePop">PurchAmt</th>';
				$html .= '<th Class="sortablePop">Discount</th>';
				$html .= '<th Class="sortablePop">Excise</th>';
				$html .= '<th Class="sortablePop">CST</th>';
				$html .= '<th Class="sortablePop">TaxAmt</th>';
				$html .= '<th Class="sortablePop">Claim</th>';
				$html .= '<th Class="sortablePop">Freight</th>';
				$html .= '<th Class="sortablePop">RoundOff</th>';
				$html .= '<th Class="sortablePop">InvoiceAmt</th>';
				}else if($voucher_type == "SALE"){
				$html .= '<th Class="sortablePop">Voucher No</th>';
				$html .= '<th Class="sortablePop">Voucher Date</th>';
				$html .= '<th Class="sortablePop">Party Name</th>';
				$html .= '<th Class="sortablePop">Address</th>';
				$html .= '<th Class="sortablePop">Sale Amount</th>';
				$html .= '<th Class="sortablePop">Disc Amount</th>';
				$html .= '<th Class="sortablePop">Tax Amount</th>';
				$html .= '<th Class="sortablePop">Claim Amount</th>';
				$html .= '<th Class="sortablePop">RoundOff</th>';
				$html .= '<th Class="sortablePop">Bill Amount</th>';
			}
			
			$html .= '</tr>';
			$html .= '</thead>';
			$html .= '<body>';
			$Payment_sum = 0;
			$purch_amt_total = 0.00;
			$DiscAmt = 0.00;
			$TaxAmt = 0.00;
			$freight_amt_total = 0.00;
			$round_off1 = 0.00;
			$InvAmt = 0.00;
			
			foreach ($table_data as $key => $value) {
				$tax_sale = 0.00;
				$html .= '<tr>';
				if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
					$html .= '<td>'.$value['VoucherID'].'</td>';
					$date = substr($value['Transdate'],0,10);
					$html .= '<td>'._d($date).'</td>';
					$html .= '<td>'.$value['AccountID'].'</td>';
					if($value['firstname'] == null || $value['firstname'] == ''){
						$accountName = $value['company'];
						}else{
						$accountName = $value['firstname']." ".$value['lastname'];
					}
					$html .= '<td>'.$accountName.'</td>';
					if($value['TType'] == "C"){
						$credit_amt = $value['Amount'];
						$debit_amt = "";
						$dr_cr = "Cr";
						$total_credit = $total_credit + $value['Amount'];
						}else {
						$credit_amt = "";
						$debit_amt = $value['Amount'];
						$dr_cr = "Dr";
						$total_debit = $total_debit + $value['Amount'];
					}
					$html .= '<td>'.$dr_cr.'</td>';
					$html .= '<td align="right">'.number_format($debit_amt,2).'</td>';
					$html .= '<td align="right">'.number_format($credit_amt,2).'</td>';
					$html .= '<td>'.$value['Narration'].'</td>';
					$html .= '<td>'.$value['address'].'</td>';
					}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
					$html .= '<td>'.$value['PassedFrom'].'</td>';
					$date = substr($value['Transdate'],0,10);
					$html .= '<td>'._d($date).'</td>';
					$html .= '<td>'.$value['VoucherID'].'</td>';
					//$ss = get_for_voucher($value['VoucherID'],$value['Amount'],$voucher_type);
					foreach ($data_for_pay_rec as $key1 => $value1) {
						if($value['VoucherID'] == $value1['VoucherID'] && $value['Amount'] == $value1['Amount'] && $value['Narration'] == $value1['Narration']){
							if($value1['company'] == null){
								$accountName1 = $value1['firstname']." ".$value1['lastname'];
								}else{
								$accountName1 = $value1['company'];
							}
						}
					}
					$html .= '<td>'.$accountName1.'</td>';
					$html .= '<td>'.$value['Amount'].'</td>';
					$Payment_sum = $Payment_sum + $value['Amount'];
					if($value['company'] == null){
						$accountName2 = $value['firstname']." ".$value['lastname'];
						}else{
						$accountName2 = $value['company'];
					}
					$html .= '<td>'.$accountName2.'</td>';
					$html .= '<td>'.$value['Narration'].'</td>';
					$html .= '<td>'.$value['address'].'</td>';
					}else if($voucher_type == "PURCHASE"){
					$html .= '<td>'.$value['PurchID'].'</td>';
					$date = substr($value['Transdate'],0,10);
					$html .= '<td>'._d($date).'</td>';
					$html .= '<td>'.$value['Invoiceno'].'</td>';
					$html .= '<td>'.$value['company'].'</td>';
					$html .= '<td style="text-align:right;">'.$value['Purchamt'].'</td>';
					$purch_amt_total = $purch_amt_total + $value['Purchamt'];
					$html .= '<td style="text-align:right;">'.number_format($value['Discamt'],2).'</td>';
					$DiscAmt = $DiscAmt + $value['Discamt'];
					$html .= '<td style="text-align:right;">'.number_format($value['Excamt'],2).'</td>';
					$html .= '<td style="text-align:right;">'.number_format($value['Cstamt'],2).'</td>';
					if($value['sgstamt']!=0 || $value['cgstamt']!=0){
						$tax= $value['sgstamt'] + $value['cgstamt'];
						}else{
						$tax= $value['igstamt'];
					}
					$html .= '<td style="text-align:right;">'.number_format($tax,2).'</td>';
					$TaxAmt = $TaxAmt + $tax;
					$html .= '<td></td>';
					$html .= '<td style="text-align:right;">'.number_format($value['Frtamt'],2).'</td>';
					$freight_amt_total = $freight_amt_total + $value['Frtamt'];
					$html .= '<td style="text-align:right;">'.number_format($value['RoundOffAmt'],2).'</td>';
					$round_off1 = $round_off1 + $value['RoundOffAmt'];
					$html .= '<td style="text-align:right;">'.number_format($value['Invamt'],2).'</td>';
					$InvAmt = $InvAmt + $value['Invamt'];
					}else if($voucher_type == "SALE"){
					$html .= '<td>'.$value['SalesID'].'</td>';
					$date = substr($value['Transdate'],0,10);
					$html .= '<td>'._d($date).'</td>';
					$html .= '<td>'.$value['company'].'</td>';
					$html .= '<td>'.$value['address'].'</td>';
					$html .= '<td style="text-align:right;">'.number_format($value['SaleAmt'],2).'</td>';
					$html .= '<td style="text-align:right;">'.number_format($value['DiscAmt'],2).'</td>';
					if($value['sgstamt']!=0 || $value['cgstamt']!=0){
						$tax_sale= $value['sgstamt'] + $value['cgstamt'];
						}else{
						$tax_sale= $value['igstamt'];
					}
					$html .= '<td style="text-align:right;">'.number_format($tax_sale,2).'</td>';
					$html .= '<td></td>';
					$roundff=($value['BillAmt']-$value['RndAmt']);
					$round_off=round($roundff,2);
					$html .= '<td style="text-align:right;">'.number_format($round_off,2).'</td>';
					$html .= '<td style="text-align:right;">'.number_format($value['BillAmt'],2).'</td>';
					
					$SaleAmt = $SaleAmt + $value['SaleAmt'];
					$DiscAmt = $DiscAmt + $value['DiscAmt'];
					$TaxAmt += $tax_sale;
					$BillAmt = $BillAmt + $value['BillAmt'];
					$round_off1 += $round_off;
				}
				$html .= '</tr>';
			}
			$html .= '<tr>';
			if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td style="color:red;"><b>Total</b></td>';
				$html .= '<td></td>';
				$html .= '<td align="right" style="color:red;"><b>'.number_format($total_debit,2).'</b></td>';
				$html .= '<td align="right" style="color:red;"><b>'.number_format($total_credit,2).'</b></td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td style="color:red;">Total</td>';
				$html .= '<td align="right" style="color:red;">'.$Payment_sum.'</td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				}else if($voucher_type == "PURCHASE"){
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td style="color:red;"><b>Total</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($purch_amt_total,2).'</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($DiscAmt,2).'</b></td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($TaxAmt,2).'</b></td>';
				$html .= '<td></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($freight_amt_total,2).'</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($round_off1,2).'</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($InvAmt,2).'</b></td>';
				}else if($voucher_type == "SALE"){
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td></td>';
				$html .= '<td style="color:red;"><b>Total</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($SaleAmt,2).'</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($DiscAmt,2).'</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($TaxAmt,2).'</b></td>';
				$html .= '<td></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($round_off1,2).'</b></td>';
				$html .= '<td style="color:red;text-align:right;"><b>'.number_format($BillAmt,2).'</b></td>';
				
			}
			$html .= '<tr>';
			$html .= '</body>';
			$html .= '</table>';
			echo json_encode($html);
			die;
		}
		public function voucher_register($id = ''){
			
			if (!has_permission_new('accounting_voucher_register_entry', '', 'view')) {
				access_denied('accounting_voucher_register_entry');
			} 
			if ($this->input->post()) {
				$data  = $this->input->post();
				
				if($id == ''){
					if (!has_permission_new('accounting_journal_entry', '', 'create')) {
						access_denied('accounting_journal_entry');
					}
					$success = $this->accounting_model->add_payment_entry($data);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('added_successfully', 'Payment Entry'));
					}
					}else{
					if (!has_permission_new('accounting_journal_entry', '', 'edit')) {
						access_denied('accounting_journal_entry');
					}
					$success = $this->accounting_model->update_journal_entry($data, $id);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('updated_successfully', _l('journal_entry')));
					}
				}
				redirect(admin_url('accounting/new_payment_entry'));
			}
			
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$selected_company = $this->session->userdata('root_company');
			$data['title'] = "Voucher Register";
			$data['type_of_voucher'] = $this->accounting_model->get_type_of_voucher();
			$data['company_detail'] = $this->accounting_model->get_company_detail1($selected_company);
			/*echo "<pre>";
				print_r($data['type_of_voucher']);
			die;*/
			
			$this->load->view('voucher_register/manage', $data);
		}
		
		public function company_detail1(){
			$comid  = $this->input->post('comid');
			//$type  = $this->input->post('voucher_type');
			$company_detail=$this->accounting_model->get_company_detail1($comid);
			//$staff_data->state_name = $staff_data->state;
			$company_detail->company_name;
			$company_detail->address;
			// $company_detail->PassedFrom;
			echo json_encode($company_detail); 
			// print($company_detail);
			die();
		}
		
		public function delete_receipt_entry($id,$PassedFrom)
		{ 
			if (!has_permission_new('accounting_receipt_entry', '', 'delete')) {
				access_denied('accounting_receipt_entry');
			}
			$success = $this->accounting_model->delete_receipt_entry($id,$PassedFrom);
			$message = '';
			if ($success) {
				$message = _l('deleted');
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/new_receipt_entry'));
		}
		
		/**
			* delete PAyment entry
			* @param  integer $id
			* @return
		*/
		public function delete_payment_entry($id,$PassedFrom)
		{
			if (!has_permission_new('accounting_payment_entry', '', 'delete')) {
				access_denied('accounting_payment_entry');
			}
			$success = $this->accounting_model->delete_payment_entry($id,$PassedFrom);
			$message = '';
			if ($success) {
				$message = _l('deleted');
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/new_payment_entry'));
		}
		
		
		public function voucher_reg_table()
		{
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('voucher_register');
				}
			}
		}
		
		public function voucher_cdnote_reg_table()
		{
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('voucher_register_cdnote');
				}
			}
		}
		
		
		public function voucher_contra_reg_table()
		{
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('voucher_register_contra');
				}
			}
		}
		
		public function voucher_payments_reg_table()
		{
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('voucher_register_payments');
				}
			}
		}
		
		public function voucher_purchase_reg_table()
		{
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('voucher_register_purchase');
				}
			}
		}
		
		public function voucher_receipts_reg_table()
		{
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('voucher_register_receipts');
				}
			}
		}
		
		public function voucher_sales_reg_table()
		{
			if (!has_permission_new('accounting_journal_entry', '', 'view')) {
				ajax_access_denied();
			}
			if ($this->input->is_ajax_request()) {
				if($this->input->post()){
					$this->app->get_table_data('voucher_register_sales');
				}
			}
		}
		
		/**
			* delete journal entry
			* @param  integer $id
			* @return
		*/
		public function delete_journal_entry($id)
		{
			if (!has_permission_new('accounting_journal_entry', '', 'delete')) {
				access_denied('accounting_journal_entry');
			}
			$success = $this->accounting_model->delete_journal_entry($id);
			$message = '';
			if ($success) {
				$message = _l('deleted');
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/new_journal_entry'));
		}
		
		/**
			* delete contra entry
			* @param  integer $id
			* @return
		*/
		public function delete_contra_entry($id)
		{
			if (!has_permission_new('accounting_contra_entry', '', 'delete')) {
				access_denied('accounting_contra_entry');
			}
			$success = $this->accounting_model->delete_contra_entry($id);
			$message = '';
			if ($success) {
				$message = _l('deleted');
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/new_contra_entry'));
		}
		
		/**
			* report manage
			* @return view
		*/
		public function report(){
			if (!has_permission_new('accounting_report', '', 'view')) {
				access_denied('accounting_report');
			}
			$data['title'] = _l('accounting_report');
			
			$this->load->view('report/manage', $data);
		}
		
		/**
			* report balance sheet
			* @return view
		*/
		public function rp_balance_sheet(){
			$this->load->model('currencies_model');
			$data['title'] = _l('balance_sheet');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/balance_sheet', $data);
		}
		
		/**
			* report balance sheet comparison
			* @return view
		*/
		public function rp_balance_sheet_comparison(){
			$this->load->model('currencies_model');
			$data['title'] = _l('balance_sheet_comparison');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounting_method'] = get_option('acc_accounting_method');
			
			$this->load->view('report/includes/balance_sheet_comparison', $data);
		}
		
		/**
			* report balance sheet detail
			* @return view
		*/
		public function rp_balance_sheet_detail(){
			$this->load->model('currencies_model');
			$data['title'] = _l('balance_sheet_detail');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/balance_sheet_detail', $data);
		}
		
		/**
			* report balance sheet summary
			* @return view 
		*/
		public function rp_balance_sheet_summary(){
			$this->load->model('currencies_model');
			$data['title'] = _l('balance_sheet_summary');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/balance_sheet_summary', $data);
		}
		
		/**
			* report business snapshot
			* @return view
		*/
		public function rp_business_snapshot(){
			$this->load->model('currencies_model');
			$data['title'] = _l('business_snapshot');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['data_report'] = $this->accounting_model->get_data_balance_sheet_summary([]);
			$this->load->view('report/includes/balance_sheet_summary', $data);
		}
		
		/**
			* custom summary report
			* @return view
		*/
		public function rp_custom_summary_report(){
			$this->load->model('currencies_model');
			$data['title'] = _l('custom_summary_report');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/custom_summary_report', $data);
		}
		
		/**
			* report profit and loss as of total income
			* @return view
		*/
		public function rp_profit_and_loss_as_of_total_income(){
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss_as_of_total_income');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/profit_and_loss_as_of_total_income', $data);
		}
		
		/**
			* report profit and loss comparison
			* @return view
		*/
		public function rp_profit_and_loss_comparison(){
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss_comparison');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/profit_and_loss_comparison', $data);
		}
		
		/**
			* report profit and loss detail
			* @return view
		*/
		public function rp_profit_and_loss_detail(){
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss_detail');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/profit_and_loss_detail', $data);
		}
		
		/**
			* report profit and loss year to date comparison
			* @return view
		*/
		public function rp_profit_and_loss_year_to_date_comparison(){
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss_year_to_date_comparison');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$this->load->view('report/includes/profit_and_loss_year_to_date_comparison', $data);
		}
		
		/**
			* report profit and loss
			* @return view
		*/
		public function rp_profit_and_loss(){
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/profit_and_loss', $data);
		}
		
		/**
			* report statement of cash flows
			* @return view
		*/
		public function rp_statement_of_cash_flows(){
			$this->load->model('currencies_model');
			$data['title'] = _l('statement_of_cash_flows');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/statement_of_cash_flows', $data);
		}
		
		/**
			* report statement of changes in equity description
			* @return view
		*/
		public function rp_statement_of_changes_in_equity(){
			$this->load->model('currencies_model');
			$data['title'] = _l('statement_of_changes_in_equity');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/statement_of_changes_in_equity', $data);
		}
		
		/**
			* report deposit detail
			* @return view
		*/
		public function rp_deposit_detail(){
			$this->load->model('currencies_model');
			$data['title'] = _l('deposit_detail');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/deposit_detail', $data);
		}
		
		/**
			* report income by customer summary
			* @return view
		*/
		public function rp_income_by_customer_summary(){
			$this->load->model('currencies_model');
			$data['title'] = _l('income_by_customer_summary');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/income_by_customer_summary', $data);
		}
		
		/**
			* report check detail
			* @return view
		*/
		public function rp_check_detail(){
			$this->load->model('currencies_model');
			$data['title'] = _l('cheque_detail');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/check_detail', $data);
		}
		
		/**
			* report account list
			* @return view
		*/
		public function rp_account_list(){
			$this->load->model('currencies_model');
			$data['title'] = _l('account_list');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/account_list', $data);
		}
		
		/**
			* report account history
			* @return view
		*/
		public function rp_account_history(){
			$this->load->model('currencies_model');
			$data['title'] = _l('account_history');
			$data['account'] = $this->input->get('account');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounts'] = $this->accounting_model->get_accounts('', 'find_in_set(account_type_id, "2,3,4,5,7,8,9,10")');
			$this->load->view('report/includes/account_history', $data);
		}
		
		/**
			* report general ledger
			* @return view
		*/
		public function rp_general_ledger(){
			$this->load->model('currencies_model');
			$data['title'] = _l('general_ledger');
			$fy = $this->session->userdata('finacial_year');
			$fy1 = $fy."-04-01";
			$fy_new  = $fy + 1;
			$lastdate_date = '20'.$fy_new.'-03-31';
			$curr_date = date('Y-m-d');
			$curr_date_new    = new DateTime($curr_date);
			$last_date_yr = new DateTime($lastdate_date);
			if($last_date_yr < $curr_date_new){
				$date = $lastdate_date;
				}else{
				$date = date('Y-m-d');
			}
			$data['from_date'] = $fy1;
			$data['to_date'] = $date;
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounts_list'] = $this->accounting_model->get_accounts_for_ledger();
			$data['accounts_list_staff'] = $this->accounting_model->get_staff_for_ledger();
			$data['selected_company_details']    = $this->order_model->get_selected_company_details();
			$this->load->view('report/includes/general_ledger', $data);
		}
		
		/**
			* report journal
			* @return view
		*/
		public function rp_journal(){
			$this->load->model('currencies_model');
			$data['title'] = _l('journal');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/journal', $data);
		}
		
		/**
			* report recent transactions
			* @return view
		*/
		public function rp_recent_transactions(){
			$this->load->model('currencies_model');
			$data['title'] = _l('recent_transactions');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/recent_transactions', $data);
		}
		
		/**
			* report transaction detail by account
			* @return view
		*/
		public function rp_transaction_detail_by_account(){
			$this->load->model('currencies_model');
			$data['title'] = _l('transaction_detail_by_account');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/transaction_detail_by_account', $data);
		}
		
		/**
			* report transaction list by date
			* @return view
		*/
		public function rp_transaction_list_by_date(){
			$this->load->model('currencies_model');
			$data['title'] = _l('transaction_list_by_date');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/transaction_list_by_date', $data);
		}
		
		/**
			* report trial balance
			* @return view
		*/
		public function rp_trial_balance(){
			$this->load->model('currencies_model');
			$data['title'] = _l('trial_balance');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/trial_balance', $data);
		}
		
		/**
			* dashboard
			* @return view
		*/
		public function dashboard(){
			if (!has_permission_new('accounting_dashboard', '', 'view')) {
				access_denied('accounting_dashboard');
			}
			$data['title'] = _l('dashboard');
			$this->load->model('currencies_model');
			
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['currencys'] = $this->currencies_model->get();
			
			$data_filter = ['date' => 'last_30_days'];
			
			$this->load->view('dashboard/manage', $data);
		}
		
		/**
			* import xlsx banking
			* @return view
		*/
		public function import_xlsx_banking() {
			if (!has_permission_new('accounting_transaction', '', 'create')) {
				access_denied('accounting_transaction');
			}
			
			$this->load->model('staff_model');
			$data_staff = $this->staff_model->get(get_staff_user_id());
			
			/*get language active*/
			if ($data_staff) {
				if ($data_staff->default_language != '') {
					$data['active_language'] = $data_staff->default_language;
					
					} else {
					
					$data['active_language'] = get_option('active_language');
				}
				
				} else {
				$data['active_language'] = get_option('active_language');
			}
			$data['title'] = _l('import_excel');
			
			$this->load->view('transaction/import_banking', $data);
		}
		
		/**
			* import file xlsx banking
			* @return json
		*/
		public function import_file_xlsx_banking(){
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(ACCOUNTING_MODULE_NAME).'assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(ACCOUNTING_MODULE_NAME).'assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			$filename ='';
			if($this->input->post()){
				if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
					$this->delete_error_file_day_before(1, ACCOUTING_IMPORT_ITEM_ERROR);
					
					// Get the temp file path
					$tmpFilePath = $_FILES['file_csv']['tmp_name'];                
					// Make sure we have a filepath
					if (!empty($tmpFilePath) && $tmpFilePath != '') {
						$rows          = [];
						$arr_insert          = [];
						
						$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';
						
						if (!file_exists(TEMP_FOLDER)) {
							mkdir(TEMP_FOLDER, 0755);
						}
						
						if (!file_exists($tmpDir)) {
							mkdir($tmpDir, 0755);
						}
						
						// Setup our new file path
						$newFilePath = $tmpDir . $_FILES['file_csv']['name'];                    
						
						if (move_uploaded_file($tmpFilePath, $newFilePath)) {
							//Writer file
							$writer_header = array(
							_l('invoice_payments_table_date_heading').' (dd/mm/YYYY)'            =>'string',
							_l('withdrawals')     =>'string',
							_l('deposits')    =>'string',
							_l('payee')      =>'string',
							_l('description')     =>'string',
							_l('error')       =>'string',
							);
							
							$rowstyle[] =array('widths'=>[10,20,30,40]);
							
							$writer = new XLSXWriter();
							$writer->writeSheetHeader('Sheet1', $writer_header,  $col_options = ['widths'=>[40,40,40,40,50,50]]);
							
							//Reader file
							$xlsx = new XLSXReader_fin($newFilePath);
							$sheetNames = $xlsx->getSheetNames();
							$data = $xlsx->getSheetData($sheetNames[1]);
							
							$arr_header = [];
							
							$arr_header['date'] = 0;
							$arr_header['withdrawals'] = 1;
							$arr_header['deposits'] = 2;
							$arr_header['payee'] = 3;
							$arr_header['description'] = 4;
							
							$total_rows = 0;
							$total_row_false    = 0; 
							
							for ($row = 1; $row < count($data); $row++) {
								
								$total_rows++;
								
								$rd = array();
								$flag = 0;
								$flag2 = 0;
								
								$string_error ='';
								$flag_position_group;
								$flag_department = null;
								
								$value_date  = isset($data[$row][$arr_header['date']]) ? $data[$row][$arr_header['date']] : '' ;
								$value_withdrawals   = isset($data[$row][$arr_header['withdrawals']]) ? $data[$row][$arr_header['withdrawals']] : '' ;
								$value_deposits     = isset($data[$row][$arr_header['deposits']]) ? $data[$row][$arr_header['deposits']] : '' ;
								$value_payee    = isset($data[$row][$arr_header['payee']]) ? $data[$row][$arr_header['payee']] : '' ;
								$value_description   = isset($data[$row][$arr_header['description']]) ? $data[$row][$arr_header['description']] : '' ;
								
								$reg_day = '/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/'; /*yyyy-mm-dd*/
								
								if(is_null($value_date) != true){
									if(preg_match($reg_day, $value_date, $match) != 1){
										$string_error .=_l('invoice_payments_table_date_heading'). _l('invalid');
										$flag = 1; 
									}
									}else{
									$string_error .= _l('invoice_payments_table_date_heading') . _l('not_yet_entered');
									$flag = 1;
								}
								
								if (is_null($value_withdrawals) == true) {
									$string_error .= _l('withdrawals') . _l('not_yet_entered');
									$flag = 1;
									}else{
									if(!is_numeric($value_withdrawals) && $value_deposits == ''){
										$string_error .= _l('withdrawals') . _l('invalid');
										$flag = 1;
									}
								}
								
								if (is_null($value_deposits) == true) {
									$string_error .= _l('deposits') . _l('not_yet_entered');
									$flag = 1;
									}else{
									if(!is_numeric($value_deposits) && $value_withdrawals == ''){
										$string_error .= _l('deposits') . _l('invalid');
										$flag = 1;
									}
								}
								
								if (is_null($value_payee) == true) {
									$string_error .= _l('payee') . _l('not_yet_entered');
									$flag = 1;
								}
								
								
								if(($flag == 1) || $flag2 == 1 ){
									//write error file
									$writer->writeSheetRow('Sheet1', [
									$value_date,
									$value_withdrawals,
									$value_deposits,
									$value_payee,
									$value_description,
									$string_error,
									]);
									
									// $numRow++;
									$total_row_false++;
								}
								
								if($flag == 0 && $flag2 == 0){
									
									$rd['date']       = $value_date;
									$rd['withdrawals']         = $value_withdrawals;
									$rd['deposits']        = $value_deposits;
									$rd['payee']       = $value_payee;
									$rd['description']               = $value_description;
									$rd['datecreated']               = date('Y-m-d H:i:s');
									$rd['addedfrom']               = get_staff_user_id();
									
									$rows[] = $rd;
									array_push($arr_insert, $rd);
									
								}
								
							}
							
							//insert batch
							if(count($arr_insert) > 0){
								$this->accounting_model->insert_batch_banking($arr_insert);
							}
							
							$total_rows = $total_rows;
							$total_row_success = isset($rows) ? count($rows) : 0;
							$dataerror = '';
							$message ='Not enought rows for importing';
							
							if($total_row_false != 0){
								$filename = 'Import_banking_error_'.get_staff_user_id().'_'.strtotime(date('Y-m-d H:i:s')).'.xlsx';
								$writer->writeToFile(str_replace($filename, ACCOUTING_IMPORT_ITEM_ERROR.$filename, $filename));
							}
							
							
						}
					}
				}
			}
			
			
			if (file_exists($newFilePath)) {
				@unlink($newFilePath);
			}
			
			echo json_encode([
			'message'           => $message,
			'total_row_success' => $total_row_success,
			'total_row_false'   => $total_row_false,
			'total_rows'        => $total_rows,
			'site_url'          => site_url(),
			'staff_id'          => get_staff_user_id(),
			'filename'          => ACCOUTING_IMPORT_ITEM_ERROR.$filename,
			]);
		}
		/**
			* get data transfer
			* @param  integer $id 
			* @return json     
		*/
		public function get_data_transfer($id){
			$transfer = $this->accounting_model->get_transfer($id);
			$transfer->date = _d($transfer->date);
			echo json_encode($transfer);
		}
		
		/**
			* delete transfer
			* @param  integer $id
			* @return
		*/
		public function delete_transfer($id)
		{
			if (!has_permission_new('accounting_transfer', '', 'delete')) {
				access_denied('accounting_transfer');
			}
			
			$success = $this->accounting_model->delete_transfer($id);
			$message = '';
			if ($success) {
				$message = _l('deleted', _l('transfer'));
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/transfer'));
		}
		
		/**
			* get data account
			* @param  integer $id 
			* @return json     
		*/
		public function get_data_account($id){
			$account = $this->accounting_model->get_accounts($id);
			$account->balance_as_of = _d($account->balance_as_of);
			$account->name = $account->name != '' ? $account->name : _l($account->key_name);
			
			if($account->balance == 0){
				if($account->account_type_id > 10 || $account->account_type_id == 1 || $account->account_type_id == 6){
					$account->balance = 1;
					}else{
					$this->db->where('account', $id);
					$count = $this->db->count_all_results(db_prefix().'acc_account_history');
					if($count > 0){
						$account->balance = 1;
					}
				}
			}
			
			echo json_encode($account);
		}
		
		/**
			* delete account
			* @param  integer $id
			* @return
		*/
		public function delete_account($id)
		{
			if (!has_permission_new('accounting_chart_of_accounts', '', 'delete')) {
				access_denied('accounting_chart_of_accounts');
			}
			$success = $this->accounting_model->delete_account($id);
			$message = '';
			
			if ($success === 'have_transaction') {
				$message = _l('cannot_delete_transaction_already_exists');
				set_alert('warning', $message);
				}elseif ($success) {
				$message = _l('deleted', _l('acc_account'));
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/chart_of_accounts'));
		}
		
		/**
			* add rule
			* @return view
		*/
		public function new_rule($id = ''){
			if (!has_permission_new('accounting_rule', '', 'create') && !is_admin() ) {
				access_denied('accounting_rule');
			}
			
			if ($this->input->post()) {
				$data                = $this->input->post();
				if($id == ''){
					$success = $this->accounting_model->add_rule($data);
					if ($success) {
						set_alert('success', _l('added_successfully', _l('banking_rule')));
					}
					}else{
					$success = $this->accounting_model->update_rule($data, $id);
					if ($success) {
						set_alert('success', _l('updated_successfully', _l('banking_rule')));
					}
				}
				redirect(admin_url('accounting/setting?group=banking_rules'));
			}
			
			if($id != ''){
				$data['rule'] = $this->accounting_model->get_rule($id);
			}
			$this->load->model('currencies_model');
			
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounts'] = $this->accounting_model->get_accounts();
			$data['title'] = _l('banking_rule');
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select();
			
			$this->load->view('setting/rule', $data);
		}
		
		/**
			* delete convert
			* @param  integer $id
			* @return json
		*/
		public function delete_convert($id,$type)
		{
			if (!has_permission_new('accounting_transaction', '', 'delete')) {
				access_denied('accounting_transaction');
			}
			$success = $this->accounting_model->delete_convert($id,$type);
			
			$message = _l('problem_deleting', _l('acc_convert'));
			
			if ($success) {
				$message = _l('deleted', _l('acc_convert'));
			}
			
			echo json_encode(['success' => $success, 'message' => $message]);
		}
		
		/**
			* reconcile
			* @return view or redirect
		*/
		public function reconcile(){
			if (!has_permission_new('accounting_reconcile', '', 'view')) {
				access_denied('accounting_reconcile');
			}
			if ($this->input->post()) {
				if (!has_permission_new('accounting_reconcile', '', 'create')) {
					access_denied('accounting_reconcile');
				}
				$data                = $this->input->post();
				if($data['resume'] == 0){
					unset($data['resume']);
					$success = $this->accounting_model->add_reconcile($data);
				}
				redirect(admin_url('accounting/reconcile_account/'.$data['account']));
				
			}
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			
			$data['title']         = _l('reconcile');
			$data['accounts'] = $this->accounting_model->get_accounts('', 'find_in_set(account_type_id, "2,3,4,5,7,8,9,10,20,21,22,23,24,25")');
			$data['beginning_balance'] = 0;
			$data['resume'] = 0;
			
			$closing_date = false;
			$reconcile = $this->accounting_model->get_reconcile_by_account($data['accounts'][0]['id']);
			if($reconcile){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($reconcile->ending_date) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						$closing_date = true;
					}
				}
				$data['beginning_balance'] = $reconcile->ending_balance;
				if($reconcile->finish == 0){
					$data['resume'] = 1;
				}
			}
			$data['accounts_to_select'] = $this->accounting_model->get_data_account_to_select();
			
			$hide_restored=' hide';
			
			$check_reconcile_restored = $this->accounting_model->check_reconcile_restored($data['accounts'][0]['id']);
			if($check_reconcile_restored){
				$hide_restored='';
			}
			
			$data['hide_restored'] = $closing_date == false ? $hide_restored : 'hide';
			
			$this->load->view('reconcile/reconcile', $data);
		}
		
		/**
			* reconcile account
			* @param  integer $account 
			* @return view          
		*/
		public function reconcile_account($account){
			if (!has_permission_new('accounting_reconcile', '', 'create') && !is_admin() ) {
				access_denied('accounting_reconcile');
			}
			$data['accounts'] = $this->accounting_model->get_accounts();
			$data['account'] = $this->accounting_model->get_accounts($account);
			$data['reconcile'] = $this->accounting_model->get_reconcile_by_account($account);
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['title'] = _l('reconcile');
			
			$this->load->view('reconcile/reconcile_account', $data);
		}
		
		/**
			* get info reconcile
			* @param  integer $account
			* @return json
		*/
		public function get_info_reconcile($account) {
			$reconcile = $this->accounting_model->get_reconcile_by_account($account);
			$beginning_balance = 0;
			$resume_reconciling = false;
			$hide_restored = true;
			
			$check_reconcile_restored = $this->accounting_model->check_reconcile_restored($account);
			if($check_reconcile_restored){
				$hide_restored = false;
			}
			$closing_date = false;
			
			if ($reconcile) {
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($reconcile->ending_date) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						$closing_date = true;
					}
				}
				
				$beginning_balance = $reconcile->ending_balance;
				if ($reconcile->finish == 0) {
					$resume_reconciling = true;
				}
			}
			
			echo json_encode(['beginning_balance' => $beginning_balance, 'resume_reconciling' => $resume_reconciling, 'hide_restored' => $hide_restored, 'closing_date' => $closing_date]);
			die();
		}
		
		/**
			* reconcile history table
			* @return json
		*/
		public function reconcile_history_table(){
			if ($this->input->is_ajax_request()) {
				$accounts = $this->accounting_model->get_accounts();
				$account_name = [];
				
				foreach ($accounts as $key => $value) {
					$account_name[$value['id']] = $value['name'];
				}
				
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$select = [
				db_prefix() .'acc_account_history.id as id',
				'account',
				'rel_type',
				'debit',
				'credit',
				db_prefix() .'acc_account_history.description as description',
				db_prefix() . 'acc_account_history.customer as history_customer'
				];
				
				$where = [];
				
				if ($this->input->post('account') && $this->input->post('reconcile')) {
					$account = $this->input->post('account');
					array_push($where, 'AND (account = ' . $account.') and (reconcile = 0 or reconcile = '.$this->input->post('reconcile').') ');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_account_history';
				$join         = ['LEFT JOIN ' . db_prefix() . 'acc_transfers ON ' . db_prefix() . 'acc_transfers.id = ' . db_prefix() . 'acc_account_history.rel_id and ' . db_prefix() . 'acc_account_history.rel_type = "transfer"',
				'LEFT JOIN ' . db_prefix() . 'acc_journal_entries ON ' . db_prefix() . 'acc_journal_entries.id = ' . db_prefix() . 'acc_account_history.rel_id and ' . db_prefix() . 'acc_account_history.rel_type = "journal_entry"',
				'LEFT JOIN ' . db_prefix() . 'invoicepaymentrecords ON ' . db_prefix() . 'invoicepaymentrecords.id = ' . db_prefix() . 'acc_account_history.rel_id and ' . db_prefix() . 'acc_account_history.rel_type = "payment"',
				'LEFT JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid and ' . db_prefix() . 'acc_account_history.rel_type = "payment"',
				'LEFT JOIN ' . db_prefix() . 'expenses ON ' . db_prefix() . 'expenses.id = ' . db_prefix() . 'acc_account_history.rel_id and ' . db_prefix() . 'acc_account_history.rel_type = "expense"'];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [ db_prefix() . 'expenses.clientid as expenses_customer', db_prefix() . 'expenses.date as expenses_date', db_prefix() . 'invoices.clientid as payment_customer', db_prefix() . 'invoicepaymentrecords.date as payment_date', db_prefix() . 'acc_journal_entries.journal_date as journal_date', db_prefix() . 'acc_transfers.date as transfer_date', 'date_format('.db_prefix() . 'acc_account_history.datecreated, \'%Y-%m-%d\') as history_date', 'reconcile','split']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$checked = '';
					if($aRow['reconcile'] != 0){
						$checked = 'checked';
					}
					$row[] = '<div class="checkbox"><input '.$checked.' type="checkbox" id="history_checkbox_' . $aRow['id'] . '" value="' . $aRow['id'] . '" data-payment="'.$aRow['credit'] .'" data-deposit="'.$aRow['debit'] .'"><label class="label_checkbox"></label></div>';
					if($aRow['rel_type'] == 'payment'){
						$row[] = _d($aRow['payment_date']);
						}elseif ($aRow['rel_type'] == 'expense') {
						$row[] = _d($aRow['expenses_date']);
						}elseif ($aRow['rel_type'] == 'journal_entry') {
						$row[] = _d($aRow['journal_date']);
						}elseif ($aRow['rel_type'] == 'transfer') {
						$row[] = _d($aRow['transfer_date']);
						}else{
						$row[] = _d($aRow['history_date']);
					}
					$row[] = _l($aRow['rel_type']);
					if($aRow['split'] > 0 && isset($account_name[$aRow['split']])){
						$row[] = $account_name[$aRow['split']];
						}else{
						$row[] = '-Split-';
					}
					
					if($aRow['rel_type'] == 'payment'){
						$row[] = get_company_name($aRow['payment_customer']);
						}elseif ($aRow['rel_type'] == 'expense') {
						$row[] = get_company_name($aRow['expenses_customer']);
						}else{
						$row[] = get_company_name($aRow['history_customer']);
					}
					
					$row[] = $aRow['description'];
					if($aRow['credit'] > 0){
						$row[] = app_format_money($aRow['credit'], $currency->name);
						}else{
						$row[] = '';
					}
					
					if($aRow['debit'] > 0){
						$row[] = app_format_money($aRow['debit'], $currency->name);
						}else{
						$row[] = '';
					}
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			*
			*  add adjustment
			*  @return view
		*/
		public function adjustment()
		{
			if (!has_permission_new('accounting_reconcile', '', 'create')) {
				access_denied('accounting');
			}
			
			if ($this->input->post()) {
				$data = $this->input->post();
				$message = '';
				$success = $this->accounting_model->add_adjustment($data);
				
				if ($success === 'close_the_book') {
					$message = _l('has_closed_the_book');
					}elseif ($success) {
					$message = _l('added_successfully', _l('adjustment'));
					}else {
					$message = _l('add_failure');
				}
				
				echo json_encode(['success' => $success, 'message' => $message]);
				die();
			}
		}
		
		/**
			* reconcile account
			* @param  integer $account 
			* @return view          
		*/
		public function finish_reconcile_account(){
			if (!has_permission_new('accounting_reconcile', '', 'create') && !is_admin() ) {
				access_denied('accounting_reconcile');
			}
			
			if ($this->input->post()) {
				$data = $this->input->post();
				$message = '';
				$success = $this->accounting_model->finish_reconcile_account($data);
				
				if ($success) {
					$message = _l('added_successfully', _l('reconcile'));
					set_alert('success', $message);
					}else {
					$message = _l('add_failure');
					set_alert('warning', $message);
				}
			}
			
			redirect(admin_url('accounting/reconcile'));
		}
		
		/**
			* edit reconcile
			* @return redirect 
		*/
		public function edit_reconcile(){
			if (!has_permission_new('accounting_reconcile', '', 'edit') && !is_admin() ) {
				access_denied('accounting_reconcile');
			}
			
			if ($this->input->post()) {
				$data = $this->input->post();
				$id = $data['reconcile_id'];
				$account = $data['account'];
				unset($data['reconcile_id']);
				$message = '';
				$success = $this->accounting_model->update_reconcile($data, $id);
				
				if ($success) {
					$message = _l('updated_successfully', _l('reconcile'));
					set_alert('success', $message);
				}
			}
			
			redirect(admin_url('accounting/reconcile_account/'.$account));
		}
		
		/**
			* banking rules table
			* @return json
		*/
		public function banking_rules_table(){
			if ($this->input->is_ajax_request()) {
				
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$select = [
				'id',
				'name',
				];
				
				$where = [];
				$from_date = '';
				$to_date   = '';
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_banking_rules';
				$join         = [];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['transaction']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$categoryOutput = $aRow['name'];
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_setting', '', 'edit')) {
						$categoryOutput .= '<a href="' . admin_url('accounting/new_rule/' . $aRow['id']) . '">' . _l('edit') . '</a>';
					}
					
					if (has_permission_new('accounting_setting', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_rule/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					$row[] = _l($aRow['transaction']);
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* delete rule
			* @param  integer $id
			* @return
		*/
		public function delete_rule($id)
		{
			if (!has_permission_new('accounting_setting', '', 'delete')) {
				access_denied('accounting_setting');
			}
			
			$success = $this->accounting_model->delete_rule($id);
			$message = '';
			if ($success) {
				$message = _l('deleted');
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/setting?group=banking_rules'));
		}
		
		/**
			* view report
			* @return view
		*/
		public function export_general_ledger(){
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				$data_filter = $this->input->post();
				
				$account_name = $this->accounting_model->get_name_account($data_filter);
				if($account_name->company){
					$name = $account_name->company;
					$account_full_name = $name." (".$account_name->AccountID.")". " - ".$account_name->StationName;
					}else{
					$name = $account_name->firstname." ". $account_name->lastname;
					$account_full_name = $name." (".$account_name->AccountID.")";
				}
				$SubActGroupID = $account_name->SubActGroupID;
				if (!has_permission_new('accounting_ledger_entry_SC', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "50003002"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry_SD', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "60001004"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID != "60001004" && $SubActGroupID != "50003002"){
					echo json_encode('denied');
					die;
					}else{
					$data_report = $this->accounting_model->get_data_general_ledger2($data_filter);
					$total_bal = $this->accounting_model->get_data_for_account_bal($data_filter);
					
					$new_acc_bal = $total_bal->BAL1;
					$opening_bal = $total_bal->BAL1;
					$i = 1;
					$from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
					$from_date = date('Y-m-d',strtotime($from_date));
					$to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
					$to_date = date('Y-m-d',strtotime($to_date));
					
					$finacial_year = $this->session->userdata('finacial_year');
					if($from_date > date('20'.$finacial_year.'-04-01')){
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_cr_sum($data_filter);
						$CRSum = $getuptofromdatebal[0]['Amount'];
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_dr_sum($data_filter);
						$DRSum = $getuptofromdatebal[0]['Amount'];
						$opening_bal = $total_bal->BAL1 + $DRSum - $CRSum;
						$new_acc_bal = $total_bal->BAL1 + $DRSum - $CRSum;
					}
					
					$this->load->model('sale_reports_model');
					$selected_company_details    = $this->sale_reports_model->get_company_detail();
					
					$writer = new XLSXWriter();
					
					$company_name = array($selected_company_details->company_name);
					$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_name);
					
					$address = $selected_company_details->address;
					$company_addr = array($address,);
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_addr);
					
					$msg = "Account Ledger Report ".$this->input->post('from_date')." To " .$this->input->post('to_date')." Account: ".$account_full_name;
					$filter = array($msg);
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $filter);
					
					// empty row
					$list_add = [];
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$writer->writeSheetRow('Sheet1', $list_add);
					
					$set_col_tk = [];
					$set_col_tk["Date"] =  'Date';
					$set_col_tk["Particular"] =  'Particular';
					$set_col_tk["Voucher Type"] =  'Voucher Type';
					$set_col_tk["Voucher_ID"] =  'Voucher ID';
					$set_col_tk["Narration"] =  'Narration';
					$set_col_tk["Debit"] =  'Debit';
					$set_col_tk["Credit"] =  'Credit';
					$set_col_tk["Balance"] =  'Balance';
					$set_col_tk["CR/DR"] =  'CR/DR';
					
					$writer_header = $set_col_tk;
					$writer->writeSheetRow('Sheet1', $writer_header);
					
					$total_debit = 0;
					$total_credit = 0;
					foreach ($data_report as $k => $value) {
						$led_from_date = date('Y-m-d',strtotime($value["Transdate"]));
						$led_to_date = date('Y-m-d',strtotime($value["Transdate"]));
						if($led_from_date >= $from_date && $led_from_date <= $to_date){
							if($i==1){
								if($opening_bal>0){
									$ob_dr_cr = "Dr";
									}else{
									$ob_dr_cr = "Cr";
								}
								
								$list_add = [];
								$list_add[] = _d($from_date);
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "Opening Balance";
								$new_bal = '';
								if($opening_bal>0){
									$new_bal = abs($opening_bal);
									$total_debit = $total_debit + $new_bal;
									$new_bal = abs($new_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								$new_bal = '';
								if($opening_bal<=0){
									$total_credit = $total_credit + abs($opening_bal);
									$new_bal = abs($opening_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								
								$list_add[] = $opening_bal_new;
								$list_add[] = $ob_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);
							}
							if($value["Amount"] !== "0.00"){
								$list_add = [];
								$list_add[] = _d(substr($value["Transdate"],0,10));
								$list_add[] = $value["EffectLedger"];
								$list_add[] = $value["PassedFrom"];
								$list_add[] = $value["VoucherID"];
								$list_add[] = $value["Narration"];
								
								$dvalue = "";
								if($value["TType"]=="D"){
									
									$new_acc_bal = $new_acc_bal + $value["Amount"];
									$dvalue = $value["Amount"];
									$total_debit = $total_debit + $dvalue;
									$dvalue = $dvalue;
								}
								$list_add[] = $dvalue;
								$cvalue = "";
								if($value["TType"]=="C"){
									$new_acc_bal = $new_acc_bal - $value["Amount"];
									$cvalue = $value["Amount"];
									$total_credit = $total_credit + $cvalue;
									$cvalue = $cvalue;
								}
								$list_add[] = $cvalue;
								$new_acc_bal2 = abs($new_acc_bal);
								if($new_acc_bal>0){
									$nab_dr_cr = "Dr";
									}else{
									$nab_dr_cr = "Cr";
								}
								$new_acc_bal2 = round($new_acc_bal2,2);
								$list_add[] = $new_acc_bal2;
								$list_add[] = $nab_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);   
								$i++;
							}    
							
							}else{
							if($value["TType"]=="D"){
								$new_acc_bal = $new_acc_bal + $value["Amount"];
							}
							if($value["TType"]=="C"){
								$new_acc_bal = $new_acc_bal - $value["Amount"];
							}
							$opening_bal = $new_acc_bal;
						}
					}
					
					if($data_report){ 
						if($i>1)
						{
							$list_add = [];
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "Closing Balance";
							$list_add[] = $total_debit;
							$list_add[] = $total_credit;
							$list_add[] = $new_acc_bal2;
							$list_add[] = $nab_dr_cr;
							$writer->writeSheetRow('Sheet1', $list_add);
							}else{
							
						}
					}
					
					
					$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
					foreach($files as $file){
						if(is_file($file)) {
							unlink($file); 
						}
					}
					$filename = 'Account_ledger_Report.xlsx';
					$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
					echo json_encode([
					'site_url'          => site_url(),
					'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
					]);
					die;
				}
				
			}
		}
		public function view_report2(){
			$data_filter = $this->input->post();
			
			$account_name = $this->accounting_model->get_name_account($data_filter);
			if($account_name->company){
				$name = $account_name->company;
				$actDetail = $name." (".$account_name->AccountID.")". " - ".$account_name->StationName;
				}else{
				$name = $account_name->firstname." ". $account_name->lastname;
				$actDetail = $name." (".$account_name->AccountID.")";
			}
			$SubActGroupID = $account_name->SubActGroupID;
			if (!has_permission_new('accounting_ledger_entry_SC', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "50003002"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else if (!has_permission_new('accounting_ledger_entry_SD', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "60001004"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else if (!has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID != "60001004" && $SubActGroupID != "50003002"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else{
				$data_report = $this->accounting_model->get_data_general_ledger2($data_filter);
				$SaleIds = $this->accounting_model->GetSaleIds($data_filter);
				
				$total_bal = $this->accounting_model->get_data_for_account_bal($data_filter);
				$data = array();
				$data["account_name"] = $actDetail;
				
				$new_acc_bal = $total_bal->BAL1;
				$opening_bal = $total_bal->BAL1;
				$i = 1;
				$CRSum = 0;
				$DRSum = 0;
				$finacial_year = $this->session->userdata('finacial_year');
				$from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
				$from_date = date('Y-m-d',strtotime($from_date));
				$to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
				$to_date = date('Y-m-d',strtotime($to_date));
				if($from_date > date('20'.$finacial_year.'-04-01')){
					$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_cr_sum($data_filter);
					$CRSum = $getuptofromdatebal[0]['Amount'];
					$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_dr_sum($data_filter);
					$DRSum = $getuptofromdatebal[0]['Amount'];
					$opening_bal = $total_bal->BAL1 + $DRSum - $CRSum;
					$new_acc_bal = $total_bal->BAL1 + $DRSum - $CRSum;
				}
				$total_debit = 0;
				$total_credit = 0;
				$html = '';
				
				if(empty($data_report)){
					$OCR = 0.00;
					$ODR = 0.00;
					if($new_acc_bal <=0){
						$OCR = abs($new_acc_bal);
						$OB = $OCR.'Cr';
						}else{
						$ODR = abs($new_acc_bal);
						$OB = $ODR.'Dr';
					}
					$html .= '<tr style="color:red;">';
					$html .= '<td>'. _d($from_date).'</td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>Opening Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					
					$html .= '<tr style="color:red;">';
					$html .= '<td>'. _d($to_date).'</td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>Closing Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					
					}else{
					$OCR = 0.00;
					$ODR = 0.00;
					if($new_acc_bal <=0){
						$OCR = abs($new_acc_bal);
						$OB = $OCR.'Cr';
						}else{
						$ODR = abs($new_acc_bal);
						$OB = $ODR.'Dr';
					}
					$html .= '<tr style="color:red;">';
					$html .= '<td>'. _d($from_date).'</td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>Opening Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					$total_credit = $total_credit + $OCR;
					$total_debit = $total_debit + $ODR;
					foreach ($data_report as $key => $value) {
						if($value["Amount"] !== "0.00"){
							
							$url = '';
							if($value["PassedFrom"] == "SALE"){
								foreach($SaleIds as $key1 => $value1){
									if($value1["SalesID"] == $value["VoucherID"]){
										$ChallanID = $value1["ChallanID"];
									}
								}
								$url = admin_url().'challan/edit_challan/'.$ChallanID;
								}else if($value["PassedFrom"] == "SALESRTN"){
								$url = admin_url().'sale_return/edit/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PURCHASE"){
								$url = admin_url().'purchase/EditPurchaseEntry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PURCHASERTN"){
								$url = admin_url().'purchase/purchaseRtn_list/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "CDNOTE"){
								$url = admin_url().'cd_notes/edit/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "JOURNAL"){
								$url = admin_url().'accounting/new_journal_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PAYMENTS"){
								$url = admin_url().'accounting/new_payment_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "RECEIPTS"){
								$url = admin_url().'accounting/new_receipt_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "CONTRA"){
								$url = admin_url().'accounting/new_contra_entry/'.$value["VoucherID"];
								}else{
								$url = "#";
							}
							$url2 = "_blank";
							$html .= '<tr onclick="window.open('."'".$url."'".', '."'".$url2."'".')" >';    
							$html .= '<td>'. _d(substr($value["Transdate"],0,10)).'</td>';
							$html .= '<td>'. $value["EffectLedger"].'</td>';
							$html .= '<td>'. $value["PassedFrom"].'</td>';
							$html .= '<td>'. $value["VoucherID"].'</td>';
							$len = strlen($value["Narration"]);
							if($len >67){
								$str = "...";
								}else{
								$str = "";
							}
							$html .= '<td title="'.$value["Narration"].'">'. substr($value["Narration"],0,70).''.$str.'</td>';
							$dvalue = "";
							if($value["TType"]=="D"){
								
								$new_acc_bal = $new_acc_bal + $value["Amount"];
								$dvalue = $value["Amount"];
								$total_debit = $total_debit + $dvalue;
								$dvalue = number_format($dvalue,2);
							}
							$html .= '<td align="right">'. $dvalue .'</td>';
							$cvalue = "";
							if($value["TType"]=="C"){
								$new_acc_bal = $new_acc_bal - $value["Amount"];
								$cvalue = $value["Amount"];
								$total_credit = $total_credit + $cvalue;
								$cvalue = number_format($cvalue,2);
							}
							$html .= '<td align="right">'.$cvalue.'</td>';
							$new_acc_bal2 = $new_acc_bal;
							if($new_acc_bal>0){
								$nab_dr_cr = "Dr";
								}else{
								$nab_dr_cr = "Cr";
							}
							$new_acc_bal2 = round($new_acc_bal2,2)." ".$nab_dr_cr;
							$html .= '<td align="right">'.number_format($new_acc_bal,2)." ".$nab_dr_cr.'</td>';
							$html .= '</tr>';
							$i++;
						}
					}
					if($data_report){
						$html .= '<tr style="color:red;">';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td>Closing Balance</td>';
						$html .= '<td align="right">'. number_format($total_debit,2).'</td>';
						$html .= '<td align="right">'. number_format($total_credit,2).'</td>';
						$html .= '<td align="right">'. number_format($new_acc_bal2,2)." ".$nab_dr_cr.'</td>';
						$html .= '</tr>';
					}
				}
				$data["table"] = $html;
				echo json_encode($data);
			}
			
		}
		
		/**
			* view report
			* @return view
		*/
		public function view_report(){
			$data_filter = $this->input->post();
			
			$this->load->model('currencies_model');
			$data['title'] = _l($data_filter['type']);
			$data['currency'] = $this->currencies_model->get_base_currency();
			
			switch ($data_filter['type']) {
				case 'balance_sheet':
				$data['data_report'] = $this->accounting_model->get_data_balance_sheet($data_filter);
				break;
				case 'balance_sheet_comparison':
				$data['data_report'] = $this->accounting_model->get_data_balance_sheet_comparison($data_filter);
				break;
				case 'balance_sheet_detail':
				$data['data_report'] = $this->accounting_model->get_data_balance_sheet_detail($data_filter);
				break;
				case 'balance_sheet_summary':
				$data['data_report'] = $this->accounting_model->get_data_balance_sheet_summary($data_filter);
				break;
				case 'custom_summary_report':
				$data['data_report'] = $this->accounting_model->get_data_custom_summary_report($data_filter);
				break;
				case 'profit_and_loss_as_of_total_income':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss_as_of_total_income($data_filter);
				break;
				case 'profit_and_loss_comparison':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss_comparison($data_filter);
				break;
				case 'profit_and_loss_detail':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss_detail($data_filter);
				break;
				case 'profit_and_loss_year_to_date_comparison':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss_year_to_date_comparison($data_filter);
				break;
				case 'profit_and_loss':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss($data_filter);
				break;
				case 'statement_of_cash_flows':
				$data['data_report'] = $this->accounting_model->get_data_statement_of_cash_flows($data_filter);
				break;
				case 'statement_of_changes_in_equity':
				$data['data_report'] = $this->accounting_model->get_data_statement_of_changes_in_equity($data_filter);
				break;
				case 'deposit_detail':
				$data['data_report'] = $this->accounting_model->get_data_deposit_detail($data_filter);
				break;
				case 'income_by_customer_summary':
				$data['data_report'] = $this->accounting_model->get_data_income_by_customer_summary($data_filter);
				break;
				case 'check_detail':
				$data['data_report'] = $this->accounting_model->get_data_check_detail($data_filter);
				break;
				case 'general_ledger2':
				$data['data_report'] = $this->accounting_model->get_data_general_ledger2($data_filter);
				//$data['total_bal'] = $this->accounting_model->get_data_for_account_bal($data_filter);
				$data['from_date'] = $data_filter['from_date'];
				$data['to_date'] = $data_filter['to_date'];
				$data['account_name'] = $this->accounting_model->get_name_account($data_filter);
				break;
				case 'journal':
				$data['data_report'] = $this->accounting_model->get_data_journal($data_filter);
				break;
				case 'recent_transactions':
				$data['data_report'] = $this->accounting_model->get_data_recent_transactions($data_filter);
				break;
				case 'transaction_detail_by_account':
				$data['data_report'] = $this->accounting_model->get_data_transaction_detail_by_account($data_filter);
				break;
				case 'transaction_list_by_date':
				$data['data_report'] = $this->accounting_model->get_data_transaction_list_by_date($data_filter);
				break;
				case 'trial_balance':
				$data['data_report'] = $this->accounting_model->get_data_trial_balance($data_filter);
				break;
				case 'account_history':
				$data['data_report'] = $this->accounting_model->get_data_account_history($data_filter);
				break;
				case 'tax_detail_report':
				$data['data_report'] = $this->accounting_model->get_data_tax_detail_report($data_filter);
				break;
				case 'tax_summary_report':
				$data['data_report'] = $this->accounting_model->get_data_tax_summary_report($data_filter);
				break;
				case 'tax_liability_report':
				$data['data_report'] = $this->accounting_model->get_data_tax_liability_report($data_filter);
				break;
				case 'account_list':
				$data['data_report'] = $this->accounting_model->get_data_account_list($data_filter);
				break;
				case 'accounts_receivable_ageing_detail':
				$data['data_report'] = $this->accounting_model->get_data_accounts_receivable_ageing_detail($data_filter);
				break;
				case 'accounts_receivable_ageing_summary':
				$data['data_report'] = $this->accounting_model->get_data_accounts_receivable_ageing_summary($data_filter);
				break;
				case 'accounts_payable_ageing_detail':
				$data['data_report'] = $this->accounting_model->get_data_accounts_payable_ageing_detail($data_filter);
				break;
				case 'accounts_payable_ageing_summary':
				$data['data_report'] = $this->accounting_model->get_data_accounts_payable_ageing_summary($data_filter);
				break;
				case 'profit_and_loss_12_months':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss_12_months($data_filter);
				break;
				case 'budget_overview':
				$data['data_report'] = $this->accounting_model->get_data_budget_overview($data_filter);
				break;
				case 'budget_variance':
				$data['data_report'] = $this->accounting_model->get_data_budget_variance($data_filter);
				break;
				case 'budget_comparison':
				$data['data_report'] = $this->accounting_model->get_data_budget_comparison($data_filter);
				break;
				case 'profit_and_loss_budget_performance':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss_budget_performance($data_filter);
				break;
				case 'profit_and_loss_budget_vs_actual':
				$data['data_report'] = $this->accounting_model->get_data_profit_and_loss_budget_vs_actual($data_filter);
				break;
				default:
				break;
			}
			$data['from_date'] = $data_filter['from_date'];
			$data['to_date'] = $data_filter['to_date'];
			$this->load->view('report/details/'.$data_filter['type'], $data);
		}
		
		/**
			* get data dashboard
			* @return json
		*/
		public function get_data_dashboard(){
			$data_filter = $this->input->get();
			
			$data['profit_and_loss_chart'] = $this->accounting_model->get_data_profit_and_loss_chart($data_filter);
			$data['expenses_chart'] = $this->accounting_model->get_data_expenses_chart($data_filter);
			$data['income_chart'] = $this->accounting_model->get_data_income_chart($data_filter);
			$data['sales_chart'] = $this->accounting_model->get_data_sales_chart($data_filter);
			$data['bank_accounts'] = $this->accounting_model->get_data_bank_accounts_dashboard($data_filter);
			$data['convert_status'] = $this->accounting_model->get_data_convert_status_dashboard($data_filter);
			
			echo json_encode($data);
		}
		
		/**
			* update reset all data accounting module
		*/
		public function reset_data(){
			if (!has_permission_new('accounting_setting', '', 'delete') && !is_admin() ) {
				access_denied('accounting_setting');
			}
			
			$data = $this->input->post();
			$success = $this->accounting_model->reset_data();
			if($success == true){
				$message = _l('reset_data_successfully');
				set_alert('success', $message);
			}
			redirect(admin_url('accounting/setting?group=general'));
		}
		
		/* Change status to account active or inactive / ajax */
		public function change_account_status($id, $status)
		{
			if (has_permission_new('accounting_chart_of_accounts', '', 'edit')) {
				if ($this->input->is_ajax_request()) {
					$this->accounting_model->change_account_status($id, $status);
				}
			}
		}
		
		/**
			* item automatic table
			* @return json
		*/
		public function item_automatic_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				
				$select = [
				db_prefix() . 'acc_item_automatics.id as id',
				'rate',
				'description',
				];
				$where = [];
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_item_automatics';
				$join         = ['LEFT JOIN ' . db_prefix() . 'items ON ' . db_prefix() . 'items.id = ' . db_prefix() . 'acc_item_automatics.item_id',
				'LEFT JOIN ' . db_prefix() . 'items_groups ON ' . db_prefix() . 'items_groups.id = ' . db_prefix() . 'items.group_id',
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'items_groups.name as group_name', 'inventory_asset_account', 'income_account', 'expense_account','item_id']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$categoryOutput = $aRow['description'];
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_setting', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_item_automatic(this); return false;" data-id="'.$aRow['id'].'" data-inventory-asset-account="'.$aRow['inventory_asset_account'].'" data-income-account="'.$aRow['income_account'].'" data-expense-account="'.$aRow['expense_account'].'" data-item-id="'.$aRow['item_id'].'">' . _l('edit') . '</a>';
					}
					if (has_permission_new('accounting_setting', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_item_automatic/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					
					$row[] = app_format_money($aRow['rate'], $currency->name);
					
					$row[] = $aRow['group_name'];
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* add or edit item automatic
			* @return json
		*/
		public function item_automatic(){
			$data = $this->input->post();
			if($data['id'] == ''){
				if (!has_permission_new('accounting_setting', '', 'create')) {
					access_denied('accounting');
				}
				$success = $this->accounting_model->add_item_automatic($data);
				if($success){
					$message = _l('added_successfully', _l('item_automatic'));
					}else {
					$message = _l('add_failure');
				}
				}else{
				if (!has_permission_new('accounting_setting', '', 'edit')) {
					access_denied('accounting');
				}
				$id = $data['id'];
				unset($data['id']);
				$success = $this->accounting_model->update_item_automatic($data, $id);
				$message = _l('fail');
				if ($success) {
					$message = _l('updated_successfully', _l('item_automatic'));
				}
			}
			
			echo json_encode(['success' => $success, 'message' => $message]);
			die();
		}
		
		/**
			* delete item automatic
			* @param  integer $id
			* @return
		*/
		public function delete_item_automatic($id)
		{
			if (!has_permission_new('accounting_setting', '', 'delete')) {
				access_denied('accounting');
			}
			
			$success = $this->accounting_model->delete_item_automatic($id);
			$message = '';
			if ($success) {
				$message = _l('deleted', _l('item_automatic'));
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup'));
		}
		
		/**
			* transaction bulk action
		*/
		public function transaction_bulk_action()
		{
			$total_deleted = 0;
			if ($this->input->post()) {
				$type    = $this->input->post('type');
				$ids       = $this->input->post('ids');
				
				$is_admin  = is_admin();
				if (is_array($ids)) {
					if($type == 'payment'){
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_payment_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'payment')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'invoice') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_invoice_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'invoice')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'expense') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_expense_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'expense')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'banking') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_delete') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->delete_banking($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'banking')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'payslip') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_payslip_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'payslip')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'purchase_order') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_purchase_order_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'purchase_order')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'purchase_payment') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_purchase_payment_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'purchase_payment')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'stock_import') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_stock_import_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'stock_import')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'stock_export') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_stock_export_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'stock_export')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'loss_adjustment') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_loss_adjustment_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'loss_adjustment')) {
										$total_deleted++;
									}
								}
							}
						}
						}elseif ($type == 'opening_stock') {
						foreach ($ids as $id) {
							if ($this->input->post('mass_convert') === 'true') {
								if (has_permission_new('accounting_transaction', '', 'create')) {
									if ($this->accounting_model->automatic_opening_stock_conversion($id)) {
										$total_deleted++;
									}
								}
								}elseif($this->input->post('mass_delete_convert') === 'true'){
								if (has_permission_new('accounting_transaction', '', 'delete')) {
									if ($this->accounting_model->delete_convert($id, 'opening_stock')) {
										$total_deleted++;
									}
								}
							}
						}
					}
				}
				if ($this->input->post('mass_convert') === 'true') {
					set_alert('success', _l('total_converted', $total_deleted));
					}elseif ($this->input->post('mass_delete_convert') === 'true') {
					set_alert('success', _l('total_convert_deleted', $total_deleted));
					}elseif ($this->input->post('mass_delete') === 'true') {
					set_alert('success', _l('total_deleted', $total_deleted));
				}
			}
		}
		
		/**
			* journal entry bulk action
		*/
		public function journal_entry_bulk_action()
		{
			$total_deleted = 0;
			if ($this->input->post()) {
				$ids       = $this->input->post('ids');
				$is_admin  = is_admin();
				if (is_array($ids)) {
					foreach ($ids as $id) {
						if($this->input->post('mass_delete') === 'true'){
							if (has_permission_new('accounting_journal_entry', '', 'delete')) {
								if ($this->accounting_model->delete_journal_entry($id)) {
									$total_deleted++;
								}
							}
						}
					}
					
				}
				if ($this->input->post('mass_delete') === 'true') {
					set_alert('success', _l('total_deleted', $total_deleted));
				}
			}
		}
		
		/**
			* transfer bulk action
		*/
		public function transfer_bulk_action()
		{
			$total_deleted = 0;
			if ($this->input->post()) {
				$ids       = $this->input->post('ids');
				$is_admin  = is_admin();
				if (is_array($ids)) {
					foreach ($ids as $id) {
						if($this->input->post('mass_delete') === 'true'){
							if (has_permission_new('accounting_transfer', '', 'delete')) {
								if ($this->accounting_model->delete_transfer($id)) {
									$total_deleted++;
								}
							}
						}
					}
					
				}
				if ($this->input->post('mass_delete') === 'true') {
					set_alert('success', _l('total_deleted', $total_deleted));
				}
			}
		}
		
		/**
			* tax mapping table
			* @return json
		*/
		public function tax_mapping_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				
				$select = [
				db_prefix() . 'acc_tax_mappings.id as id',
				'name',
				'taxrate',
				];
				$where = [];
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_tax_mappings';
				$join         = ['LEFT JOIN ' . db_prefix() . 'taxes ON ' . db_prefix() . 'taxes.id = ' . db_prefix() . 'acc_tax_mappings.tax_id'];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tax_id', 'payment_account', 'deposit_to', 'expense_deposit_to', 'expense_payment_account']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$categoryOutput = $aRow['tax_id'];
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_setting', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_tax_mapping(this); return false;" data-id="'.$aRow['id'].'" data-deposit-to="'.$aRow['deposit_to'].'" data-payment-account="'.$aRow['payment_account'].'" data-expense-deposit-to="'.$aRow['expense_deposit_to'].'" data-expense-payment-account="'.$aRow['expense_payment_account'].'" data-tax-id="'.$aRow['tax_id'].'">' . _l('edit') . '</a>';
					}
					if (has_permission_new('accounting_setting', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_tax_mapping/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					$row[] = $aRow['name'];
					
					$row[] = $aRow['taxrate'];
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* add or edit tax mapping
			* @return json
		*/
		public function tax_mapping(){
			$data = $this->input->post();
			if($data['id'] == ''){
				if (!has_permission_new('accounting_setting', '', 'create')) {
					access_denied('accounting');
				}
				$success = $this->accounting_model->add_tax_mapping($data);
				if($success){
					$message = _l('added_successfully', _l('tax_mapping'));
					}else {
					$message = _l('add_failure');
				}
				}else{
				if (!has_permission_new('accounting_setting', '', 'edit')) {
					access_denied('accounting');
				}
				$id = $data['id'];
				unset($data['id']);
				$success = $this->accounting_model->update_tax_mapping($data, $id);
				$message = _l('fail');
				if ($success) {
					$message = _l('updated_successfully', _l('tax_mapping'));
				}
			}
			
			echo json_encode(['success' => $success, 'message' => $message]);
			die();
		}
		
		/**
			* delete tax mapping
			* @param  integer $id
			* @return
		*/
		public function delete_tax_mapping($id)
		{
			if (!has_permission_new('accounting_setting', '', 'delete')) {
				access_denied('accounting');
			}
			
			$success = $this->accounting_model->delete_tax_mapping($id);
			$message = '';
			if ($success) {
				$message = _l('deleted', _l('tax_mapping'));
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup'));
		}
		
		/**
			* accounts bulk action
		*/
		public function accounts_bulk_action()
		{
			$total_deleted = 0;
			if ($this->input->post()) {
				$ids       = $this->input->post('ids');
				$is_admin  = is_admin();
				if (is_array($ids)) {
					foreach ($ids as $id) {
						if($this->input->post('mass_delete') === 'true'){
							if (has_permission_new('accounting_chart_of_accounts', '', 'delete')) {
								$success = $this->accounting_model->delete_account($id);
								if ($success === 'have_transaction') {
									$message = _l('cannot_delete_transaction_already_exists');
									set_alert('warning', $message);
									}elseif ($success) {
									$total_deleted++;
								} 
							}
							}elseif($this->input->post('mass_activate') === 'true'){
							if (has_permission_new('accounting_chart_of_accounts', '', 'edit')) {
								if ($this->accounting_model->change_account_status($id, 1)) {
									$total_deleted++;
								}
							}
							}elseif($this->input->post('mass_deactivate') === 'true'){
							if (has_permission_new('accounting_chart_of_accounts', '', 'edit')) {
								if ($this->accounting_model->change_account_status($id, 0)) {
									$total_deleted++;
								}
							}
						}
					}
					
				}
				if ($this->input->post('mass_delete') === 'true') {
					set_alert('success', _l('total_deleted', $total_deleted));
					}elseif ($this->input->post('mass_activate') === 'true') {
					set_alert('success', _l('total_activate', $total_deleted));
					}elseif ($this->input->post('mass_deactivate') === 'true') {
					set_alert('success', _l('total_deactivate', $total_deleted));
				}
			}
		}
		
		/**
			* expense category mapping table
			* @return json
		*/
		public function expense_category_mapping_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				
				$select = [
				db_prefix() . 'acc_expense_category_mappings.id as id',
				'name',
				'description',
				'preferred_payment_method',
				];
				$where = [];
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_expense_category_mappings';
				$join         = ['LEFT JOIN ' . db_prefix() . 'expenses_categories ON ' . db_prefix() . 'expenses_categories.id = ' . db_prefix() . 'acc_expense_category_mappings.category_id'];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['category_id', 'payment_account', 'deposit_to']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$categoryOutput = $aRow['category_id'];
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_setting', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_expense_category_mapping(this); return false;" data-id="'.$aRow['id'].'" data-deposit-to="'.$aRow['deposit_to'].'" data-payment-account="'.$aRow['payment_account'].'" data-category-id="'.$aRow['category_id'].'" data-preferred-payment-method="'.$aRow['preferred_payment_method'].'">' . _l('edit') . '</a>';
					}
					
					if (has_permission_new('accounting_setting', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_expense_category_mapping/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					$row[] = $aRow['name'];
					
					$row[] = $aRow['description'];
					
					$checked = '';
					if ($aRow['preferred_payment_method'] == 1) {
						$checked = 'checked';
					}
					
					$_data = '<div class="onoffswitch">
					<input type="checkbox" ' . ((!is_admin() && has_permission_new('accounting_setting', '', 'edit')) ? 'disabled' : '') . ' data-switch-url="' . admin_url() . 'accounting/change_preferred_payment_method" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
					<label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
					</div>';
					
					// For exporting
					$_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
					$row[] = $_data;
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* add or edit expense category mapping
			* @return json
		*/
		public function expense_category_mapping(){
			$data = $this->input->post();
			if($data['id'] == ''){
				if (!has_permission_new('accounting_setting', '', 'create')) {
					access_denied('accounting');
				}
				$success = $this->accounting_model->add_expense_category_mapping($data);
				if($success){
					$message = _l('added_successfully', _l('expense_category_mapping'));
					}else {
					$message = _l('add_failure');
				}
				}else{
				if (!has_permission_new('accounting_setting', '', 'edit')) {
					access_denied('accounting');
				}
				$id = $data['id'];
				unset($data['id']);
				$success = $this->accounting_model->update_expense_category_mapping($data, $id);
				$message = _l('fail');
				if ($success) {
					$message = _l('updated_successfully', _l('expense_category_mapping'));
				}
			}
			
			echo json_encode(['success' => $success, 'message' => $message]);
			die();
		}
		
		/**
			* delete expense_category mapping
			* @param  integer $id
			* @return
		*/
		public function delete_expense_category_mapping($id)
		{
			if (!has_permission_new('accounting_setting', '', 'delete')) {
				access_denied('accounting');
			}
			
			$success = $this->accounting_model->delete_expense_category_mapping($id);
			$message = '';
			if ($success) {
				$message = _l('deleted', _l('expense_category_mapping'));
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup'));
		}
		
		/**
			* tax detail report
			* @return view
		*/
		public function rp_tax_detail_report(){
			$this->load->model('currencies_model');
			$data['title'] = _l('tax_detail_report');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/tax_detail_report', $data);
		}
		
		/**
			* tax summary report
			* @return view
		*/
		public function rp_tax_summary_report(){
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			
			$this->load->model('taxes_model');
			$data['taxes'] = $this->taxes_model->get();
			
			$data['title'] = _l('tax_summary_report');
			$data['from_date'] = date('Y-m-01');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['to_date'] = date('Y-m-d');
			$this->load->view('report/includes/tax_summary_report', $data);
		}
		
		/**
			* tax liability report
			* @return view
		*/
		public function rp_tax_liability_report(){
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			
			$this->load->model('taxes_model');
			$data['taxes'] = $this->taxes_model->get();
			
			$data['title'] = _l('tax_liability_report');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$this->load->view('report/includes/tax_liability_report', $data);
		}
		
		
		/**
			* get data convert status dashboard
			* @return json
		*/
		public function get_data_convert_status_dashboard(){
			$data_filter = $this->input->get();
			
			$data['convert_status'] = $this->accounting_model->get_data_convert_status_dashboard($data_filter);
			
			echo json_encode($data);
		}
		
		/**
			* get data income chart
			* @return json
		*/
		public function get_data_income_chart(){
			$data_filter = $this->input->get();
			
			$data['income_chart'] = $this->accounting_model->get_data_income_chart($data_filter);
			
			echo json_encode($data);
		}
		
		/**
			* get data sales chart
			* @return json
		*/
		public function get_data_sales_chart(){
			$data_filter = $this->input->get();
			
			$data['sales_chart'] = $this->accounting_model->get_data_sales_chart($data_filter);
			
			echo json_encode($data);
		}
		
		/**
			* payment mode mapping table
			* @return json
		*/
		public function payment_mode_mapping_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				
				$select = [
				db_prefix() . 'acc_payment_mode_mappings.id as id',
				'name',
				];
				$where = [];
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_payment_mode_mappings';
				$join         = ['LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'acc_payment_mode_mappings.payment_mode_id'];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['payment_mode_id', 'payment_account', 'deposit_to',  'expense_payment_account', 'expense_deposit_to','description']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$categoryOutput = $aRow['name'];
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_setting', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_payment_mode_mapping(this); return false;" data-id="'.$aRow['id'].'" data-deposit-to="'.$aRow['deposit_to'].'" data-payment-account="'.$aRow['payment_account'].'" data-expense-deposit-to="'.$aRow['expense_deposit_to'].'" data-expense-payment-account="'.$aRow['expense_payment_account'].'" data-payment-mode-id="'.$aRow['payment_mode_id'].'">' . _l('edit') . '</a>';
					}
					if (has_permission_new('accounting_setting', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_payment_mode_mapping/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					
					$row[] = $aRow['description'];
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* add or edit payment mode mapping
			* @return json
		*/
		public function payment_mode_mapping(){
			$data = $this->input->post();
			if($data['id'] == ''){
				if (!has_permission_new('accounting_setting', '', 'create')) {
					access_denied('accounting');
				}
				$success = $this->accounting_model->add_payment_mode_mapping($data);
				if($success){
					$message = _l('added_successfully', _l('payment_mode_mapping'));
					}else {
					$message = _l('add_failure');
				}
				}else{
				if (!has_permission_new('accounting_setting', '', 'edit')) {
					access_denied('accounting');
				}
				$id = $data['id'];
				unset($data['id']);
				$success = $this->accounting_model->update_payment_mode_mapping($data, $id);
				$message = _l('fail');
				if ($success) {
					$message = _l('updated_successfully', _l('payment_mode_mapping'));
				}
			}
			
			echo json_encode(['success' => $success, 'message' => $message]);
			die();
		}
		
		/**
			* delete payment mode mapping
			* @param  integer $id
			* @return
		*/
		public function delete_payment_mode_mapping($id)
		{
			if (!has_permission_new('accounting_setting', '', 'delete')) {
				access_denied('accounting');
			}
			
			$success = $this->accounting_model->delete_payment_mode_mapping($id);
			$message = '';
			if ($success) {
				$message = _l('deleted', _l('payment_mode_mapping'));
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup'));
		}
		
		/* Change status to payment mode mapping active or inactive / ajax */
		public function change_active_payment_mode_mapping($id, $status)
		{
			if (has_permission_new('accounting_setting', '', 'edit')) {
				if ($this->input->is_ajax_request()) {
					$this->accounting_model->change_active_payment_mode_mapping($status);
				}
			}
		}
		
		/* Change status to expense category mapping active or inactive / ajax */
		public function change_active_expense_category_mapping($id, $status)
		{
			if (has_permission_new('accounting_setting', '', 'edit')) {
				if ($this->input->is_ajax_request()) {
					$this->accounting_model->change_active_expense_category_mapping($status);
				}
			}
		}
		
		/**
			* account type details table
			* @return json
		*/
		public function account_type_details_table(){
			if ($this->input->is_ajax_request()) {
				
				$this->load->model('currencies_model');
				$account_types = $this->accounting_model->get_account_types();
				
				$account_type_name = [];
				foreach ($account_types as $key => $value) {
					$account_type_name[$value['id']] = $value['name'];
				}
				
				$currency = $this->currencies_model->get_base_currency();
				$select = [
				'id',
				'name',
				];
				
				$where = [];
				$from_date = '';
				$to_date   = '';
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'acc_account_type_details';
				$join         = [];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['account_type_id']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					
					$categoryOutput = $aRow['name'];
					
					$categoryOutput .= '<div class="row-options">';
					
					if (has_permission_new('accounting_setting', '', 'edit')) {
						$categoryOutput .= '<a href="#" onclick="edit_account_type_detail(' . $aRow['id'] . '); return false;">' . _l('edit') . '</a>';
					}
					
					if (has_permission_new('accounting_setting', '', 'delete')) {
						$categoryOutput .= ' | <a href="' . admin_url('accounting/delete_account_type_detail/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
					}
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					$row[] = isset($account_type_name[$aRow['account_type_id']]) ? $account_type_name[$aRow['account_type_id']] : '';
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			*
			*  add or edit account type detail
			*  @param  integer  $id     The identifier
			*  @return view
		*/
		public function account_type_detail()
		{
			if (!has_permission_new('accounting_setting', '', 'edit') && !has_permission_new('accounting_setting', '', 'create')) {
				access_denied('accounting');
			}
			
			if ($this->input->post()) {
				$data = $this->input->post();
				$data['note'] = $this->input->post('note', false);
				$message = '';
				if ($data['id'] == '') {
					if (!has_permission_new('accounting_setting', '', 'create')) {
						access_denied('accounting');
					}
					$success = $this->accounting_model->add_account_type_detail($data);
					if ($success) {
						$message = _l('added_successfully', _l('account_type_detail'));
						}else {
						$message = _l('add_failure');
					}
					} else {
					if (!has_permission_new('accounting_setting', '', 'edit')) {
						access_denied('accounting');
					}
					$id = $data['id'];
					unset($data['id']);
					$success = $this->accounting_model->update_account_type_detail($data, $id);
					if ($success) {
						$message = _l('updated_successfully', _l('account_type_detail'));
						}else {
						$message = _l('updated_fail');
					}
				}
				
				echo json_encode(['success' => $success, 'message' => $message]);
				die();
			}
		}
		
		/**
			* delete account type detail
			* @param  integer $id
			* @return
		*/
		public function delete_account_type_detail($id)
		{
			if (!has_permission_new('accounting_setting', '', 'delete')) {
				access_denied('accounting_setting');
			}
			$success = $this->accounting_model->delete_account_type_detail($id);
			$message = '';
			
			if ($success === 'have_account') {
				$message = _l('cannot_delete_account_already_exists');
				set_alert('warning', $message);
				}elseif ($success) {
				$message = _l('deleted', _l('account_type_detail'));
				set_alert('success', $message);
				} else {
				$message = _l('can_not_delete');
				set_alert('warning', $message);
			}
			redirect(admin_url('accounting/setting?group=account_type_details'));
		}
		
		/**
			* get data account type detail
			* @param  integer $id 
			* @return json     
		*/
		public function get_data_account_type_detail($id){
			$account_type_detail = $this->accounting_model->get_data_account_type_details($id);
			
			echo json_encode($account_type_detail);
		}
		
		/**
			* journal entry export
			* @param  integer $id
		*/
		public function journal_entry_export($id){
			$this->delete_error_file_day_before(1,ACCOUTING_EXPORT_XLSX); 
			
			$this->load->model('currencies_model');
			
			$currency = $this->currencies_model->get_base_currency();
			
			$header = [];
			$header = [ _l('asp_order'), _l('asp_date'), _l('asp_creation_date'), _l('asp_invoice_number'), _l('asp_reference'), _l('asp_book'), _l('asp_account'), _l('asp_nif'), _l('asp_desc'), _l('asp_total_invoice'), _l('asp_subtotal_1'), _l('asp_vat_1'), _l('asp_subtotal_2'), _l('asp_vat_2'), _l('asp_subtotal_3'), _l('asp_vat_3'),  _l('asp_subtotal_4'), _l('asp_vat_4'),  _l('asp_subtotal_5'), _l('asp_vat_5'), _l('asp_libro_contrapartida'), _l('asp_cuenta_contrapartida'), _l('asp_lote_a_contabilizar')];
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$journal_entry = $this->accounting_model->get_journal_entry($id);
			
			if(!class_exists('XLSXWriter')){
				require_once(module_dir_path(ACCOUNTING_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');             
			}
			
			$header = [ 
			1 => _l('acc_account'), 
			2 => _l('debit'), 
			3 => _l('credit'), 
			4 => _l('description'), 
			];
			
			$widths_arr = array();
			
			for($i = 1; $i <= count($header); $i++ ){
				if($i == 1){
					$widths_arr[] = 60;
					}else if($i == 8){
					$widths_arr[] = 60;
					}else{
					$widths_arr[] = 40;
				}
			}
			
			$writer = new XLSXWriter();
			$writer->writeSheetRow('Sheet1', []);
			$writer->writeSheetRow('Sheet1', [1 => _l('journal_date').': '. _d($journal_entry->journal_date), ]);
			$writer->writeSheetRow('Sheet1', [1 => _l('number').': '. $journal_entry->number, ]);
			$writer->writeSheetRow('Sheet1', [1 => _l('description').': '. $journal_entry->Description, ]);
			$writer->writeSheetRow('Sheet1', []);
			
			
			$style3 = array('fill' => '#C65911', 'height'=>25, 'font-style'=>'bold', 'color' => '#FFFFFF', 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 15, 'font' => 'Calibri');
			$style1 = array('fill' => '#F8CBAD', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 15, 'font' => 'Calibri', 'color' => '#000000');
			$style2 = array('fill' => '#FCE4D6', 'height'=>25, 'border'=>'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 15, 'font' => 'Calibri', 'color' => '#000000');
			
			$writer->writeSheetRow('Sheet1', $header, $style3);
			
			foreach($journal_entry->details as $k => $row){
				$row['account'] = isset($account_name[$row['account']]) ? $account_name[$row['account']] : $row['account'];
				$row['debit'] =$row['debit'] > 0 ? app_format_money($row['debit'], $currency->name) : '';
				$row['credit'] =$row['credit'] > 0 ? app_format_money($row['credit'], $currency->name) : '';
				if(($k%2) == 0){
					$writer->writeSheetRow('Sheet1', $row , $style1);
					}else{
					$writer->writeSheetRow('Sheet1', $row , $style2);
				}
			}
			
			$writer->writeSheetRow('Sheet1', [1 => _l('total'), 2 => app_format_money($journal_entry->amount, $currency->name), 3 => app_format_money($journal_entry->amount, $currency->name), 4 => ''], $style3);
			
			$filename = 'journal_entry_'.time().'.xlsx';
			$writer->writeToFile(str_replace($filename, ACCOUTING_EXPORT_XLSX.$filename, $filename));
			$this->download_xlsx_file(ACCOUTING_EXPORT_XLSX.$filename);
			die();
		}
		
		/**
			* download xlsx file
			* @param  string $filename
		*/
		public function download_xlsx_file($filename){
			$file = $filename;
			$mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
			ob_end_clean();
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $mime);
			header("Content-Transfer-Encoding: Binary");
			header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			readfile($file);
			unlink($file);
			exit();
		}
		
		/**
			* delete error file day before
			* @param  string $before_day  
			* @param  string $folder_name 
			* @return boolean              
		*/
		public function delete_error_file_day_before($before_day ='', $folder_name='')
		{
			if($before_day != ''){
				$day = $before_day;
				}else{
				$day = '7';
			}
			
			if($folder_name != ''){
				$folder = $folder_name;
				}else{
				$folder = ACCOUTING_IMPORT_ITEM_ERROR;
			}
			
			//Delete old file before 7 day
			$date = date_create(date('Y-m-d H:i:s'));
			date_sub($date,date_interval_create_from_date_string($day." days"));
			$before_7_day = strtotime(date_format($date,"Y-m-d H:i:s"));
			
			foreach(glob($folder . '*') as $file) {
				
				$file_arr = explode("/",$file);
				$filename = array_pop($file_arr);
				
				if(file_exists($file)) {
					//don't delete index.html file
					if($filename != 'index.html'){
						$file_name_arr = explode("_",$filename);
						$date_create_file = array_pop($file_name_arr);
						$date_create_file =  str_replace('.xlsx', '', $date_create_file);
						
						if((float)$date_create_file <= (float)$before_7_day){
							unlink($folder.$filename);
						}
					}
				}
			}
			return true;
		}
		
		/* Change status to preferred payment method on or off / ajax */
		public function change_preferred_payment_method($id, $status)
		{
			if (has_permission_new('staff', '', 'edit')) {
				if ($this->input->is_ajax_request()) {
					$this->accounting_model->change_preferred_payment_method($id, $status);
				}
			}
		}
		
		/**
			* payslips table
			* @return json
		*/
		public function payslips_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1',
				'payslip_name',
				'payslip_template_id',
				'payslip_month',
				'staff_id_created',
				'date_created',
				'payslip_status',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'hrp_payslips.id and ' . db_prefix() . 'acc_account_history.rel_type = "payslip") as count_account_historys',
				'id',
				];
				
				$where = [];
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'hrp_payslips.id and ' . db_prefix() . 'acc_account_history.rel_type = "payslip") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'hrp_payslips.id and ' . db_prefix() . 'acc_account_history.rel_type = "payslip") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'hrp_payslips.id and ' . db_prefix() . 'acc_account_history.rel_type = "payslip") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'hrp_payslips.id and ' . db_prefix() . 'acc_account_history.rel_type = "payslip") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'hrp_payslips.payslip_month >= "' . $from_date . '" and ' . db_prefix() . 'hrp_payslips.payslip_month <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'hrp_payslips.payslip_month >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'hrp_payslips.payslip_month <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'hrp_payslips';
				$join         = [
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					//load by manager
					if(!is_admin() && !has_permission_new('hrp_payslip','','view')){
						//View own
						$code = '<a href="' . admin_url('hr_payroll/view_payslip_detail_v2/' . $aRow['id']) . '" target="_blank">' . $aRow['payslip_name'] . '</a>';
						$code .= '<div class="row-options">';
						}else{
						//admin or view global
						$code = '<a href="' . admin_url('hr_payroll/view_payslip_detail/' . $aRow['id']) . '" target="_blank">' . $aRow['payslip_name'] . '</a>';
						$code .= '<div class="row-options">';
					}
					
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['payslip_month'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$code .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="payslip-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="payslip">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$code .= '<a href="#" onclick="convert(this); return false;" id="payslip-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="payslip">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$code .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'payslip\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					$code .= '</div>';
					
					$row[] = $code;
					
					$row[] = get_payslip_template_name($aRow['payslip_template_id']);
					
					$row[] =  date('m-Y', strtotime($aRow['payslip_month']));
					
					$_data = '<a href="' . admin_url('staff/profile/' . $aRow['staff_id_created']) . '" target="_blank">' . staff_profile_image($aRow['staff_id_created'], [
					'staff-profile-image-small',
					]) . '</a>';
					$_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staff_id_created']) . '" target="_blank">' . get_staff_full_name($aRow['staff_id_created']) . '</a>';
					
					$row[] = $_data;
					$row[] = _dt($aRow['date_created']);
					
					if($aRow['payslip_status'] == 'payslip_closing'){
						$row[] = ' <span class="label label-success "> '._l($aRow['payslip_status']).' </span>';
						}else{
						$row[] = ' <span class="label label-primary"> '._l($aRow['payslip_status']).' </span>';
					}
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					$row[] = '<span class="label label-' . $label_class . ' s-status payslip-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['payslip_month'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-type' => 'payslip',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* purchase order table
			* @return json
		*/
		public function purchase_order_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1',
				'pur_order_number',
				'order_date',
				db_prefix().'pur_orders.vendor as vendor',
				'subtotal',
				'total_tax',
				'total',
				'number',
				'expense_convert',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_orders.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_order") as count_account_historys',
				db_prefix() .'pur_orders.id as id',
				];
				
				$where = [];
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_orders.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_order") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_orders.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_order") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_orders.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_order") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_orders.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_order") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'pur_orders.order_date >= "' . $from_date . '" and ' . db_prefix() . 'pur_orders.order_date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'pur_orders.order_date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'pur_orders.order_date <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'pur_orders';
				$join         = [
				'LEFT JOIN '.db_prefix().'pur_vendor ON '.db_prefix().'pur_vendor.userid = '.db_prefix().'pur_orders.vendor',
				'LEFT JOIN '.db_prefix().'departments ON '.db_prefix().'departments.departmentid = '.db_prefix().'pur_orders.department',
				'LEFT JOIN '.db_prefix().'projects ON '.db_prefix().'projects.id = '.db_prefix().'pur_orders.project',
				'LEFT JOIN '.db_prefix().'expenses ON '.db_prefix().'expenses.id = '.db_prefix().'pur_orders.expense_convert',
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['company','pur_order_number','expense_convert',db_prefix().'projects.name as project_name',db_prefix().'departments.name as department_name', db_prefix().'expenses.id as expense_id', db_prefix().'expenses.expense_name as expense_name']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$numberOutput = '';
					
					$numberOutput = '<a href="' . admin_url('purchase/purchase_order/' . $aRow['id']) . '"  onclick="init_pur_order(' . $aRow['id'] . '); return false;" >'.$aRow['pur_order_number']. '</a>';
					
					$numberOutput .= '<div class="row-options">';
					
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['order_date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$numberOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="purchase-order-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_order">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$numberOutput .= '<a href="#" onclick="convert(this); return false;" id="purchase-order-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_order">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$numberOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'purchase_order\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					$numberOutput .= '</div>';
					
					$row[] = $numberOutput;
					
					$row[] = _d($aRow['order_date']);
					
					$row[] = '<a href="' . admin_url('purchase/vendor/' . $aRow['vendor']) . '" >' .  $aRow['company'] . '</a>';
					
					$row[] = app_format_money($aRow['subtotal'], $currency->name);
					
					$row[] = app_format_money($aRow['total_tax'], $currency->name);
					
					$row[] = app_format_money($aRow['total'], $currency->name);
					
					$paid = $aRow['total'] - purorder_inv_left_to_pay($aRow['id']);
					
					$percent = 0;
					
					if($aRow['total'] > 0){
						
						$percent = ($paid / $aRow['total'] ) * 100;
						
					}
					
					$row[] = '<div class="progress">
					
					<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"
					
					aria-valuemin="0" aria-valuemax="100" style="width:'.round($percent).'%">
					
					' .round($percent).' % 
					
					</div>
					
					</div>';
					
					if($aRow['expense_convert'] == 0){
						$row[] = '';
						}else{
						if($aRow['expense_name'] != ''){
							$row[] = '<a href="'.admin_url('expenses/list_expenses/'.$aRow['expense_convert']).'">#'.$aRow['expense_id'].' - '. $aRow['expense_name'].'</a>';
							}else{
							$row[] = '<a href="'.admin_url('expenses/list_expenses/'.$aRow['expense_convert']).'">#'.$aRow['expense_id'].'</a>';
						}
					}
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					
					$row[] = '<span class="label label-' . $label_class . ' s-status purchase_order-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['order_date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-type' => 'purchase_order',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* stock import table
			* @return json
		*/
		public function stock_import_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1',
				'goods_receipt_code',
				'date_c',
				'total_tax_money', 
				'total_goods_money',
				'value_of_inventory',
				'total_money',
				'approval',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_receipt.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_import") as count_account_historys',
				'id',
				];
				
				$where = [];
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_receipt.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_import") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_receipt.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_import") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_receipt.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_import") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_receipt.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_import") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'goods_receipt.date_c >= "' . $from_date . '" and ' . db_prefix() . 'goods_receipt.date_c <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'goods_receipt.date_c >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'goods_receipt.date_c <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'goods_receipt';
				$join         = [
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_add','goods_receipt_code', 'supplier_code']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$name = '<a href="' . admin_url('warehouse/edit_purchase/' . $aRow['id'] ).'">' . $aRow['goods_receipt_code'] . '</a>';
					
					$name .= '<div class="row-options">';
					
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$name .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="stock-import-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_import">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$name .= '<a href="#" onclick="convert(this); return false;" id="stock-import-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_import">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$name .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'stock_import\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					$name .= '</div>';
					
					$row[] = $name;
					
					$row[] =  _d($aRow['date_c']);
					
					$row[] = app_format_money((float)$aRow['total_tax_money'],'');
					
					$row[] = app_format_money((float)$aRow['total_goods_money'],'');
					
					$row[] = app_format_money((float)$aRow['value_of_inventory'],'');
					
					$row[] = app_format_money((float)$aRow['total_money'],'');
					
					if($aRow['approval'] == 1){
						$row[] = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
						}elseif($aRow['approval'] == 0){
						$row[] = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
						}elseif($aRow['approval'] == -1){
						$row[] = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
					}
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					$row[] = '<span class="label label-' . $label_class . ' s-status stock-import-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-type' => 'stock_import',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* stock export table
			* @return json
		*/
		public function stock_export_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1',
				'goods_delivery_code',
				'customer_code',
				'date_add',
				'invoice_id',
				'approval',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_delivery.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_export") as count_account_historys',
				'id',
				];
				
				$where = [];
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_delivery.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_export") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_delivery.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_export") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_delivery.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_export") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_delivery.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_export") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'goods_delivery.date_c >= "' . $from_date . '" and ' . db_prefix() . 'goods_delivery.date_c <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'goods_delivery.date_c >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'goods_delivery.date_c <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'goods_delivery';
				$join         = [
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['date_add','date_c','goods_delivery_code','total_money']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$name = '<a href="' . admin_url('warehouse/edit_delivery/' . $aRow['id'] ).'">' . $aRow['goods_delivery_code'] . '</a>';
					
					$name .= '<div class="row-options">';
					
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$name .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="stock-export-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_export">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$name .= '<a href="#" onclick="convert(this); return false;" id="stock-export-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="stock_export">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$name .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'stock_export\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					$name .= '</div>';
					
					$row[] = $name;
					
					$_data = '';
					if($aRow['customer_code']){
						$this->db->where(db_prefix() . 'clients.userid', $aRow['customer_code']);
						$client = $this->db->get(db_prefix() . 'clients')->row();
						if($client){
							$_data = $client->company;
						}
						
					}
					
					$row[] = $_data;
					
					$row[] =  _d($aRow['date_c']);
					
					$_data = '';
					
					if($aRow['invoice_id']){
						$_data = format_invoice_number($aRow['invoice_id']).get_invoice_company_projecy($aRow['invoice_id']);
					}
					
					$row[] = $_data;
					
					if($aRow['approval'] == 1){
						$row[] = '<span class="label label-tag tag-id-1 label-tab1"><span class="tag">'._l('approved').'</span><span class="hide">, </span></span>&nbsp';
						}elseif($aRow['approval'] == 0){
						$row[] = '<span class="label label-tag tag-id-1 label-tab2"><span class="tag">'._l('not_yet_approve').'</span><span class="hide">, </span></span>&nbsp';
						}elseif($aRow['approval'] == -1){
						$row[] = '<span class="label label-tag tag-id-1 label-tab3"><span class="tag">'._l('reject').'</span><span class="hide">, </span></span>&nbsp';
					}
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					$row[] = '<span class="label label-' . $label_class . ' s-status stock-export-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_c'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-type' => 'stock_export',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* loss adjustment table
			* @return json
		*/
		public function loss_adjustment_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				
				$time_filter = $this->input->post('time_filter');
				$date_create = $this->input->post('date_create');
				$type_filter = $this->input->post('type_filter');
				$status_filter = $this->input->post('status_filter');
				
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1',
				'time',
				'type',
				'status',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'wh_loss_adjustment.id and ' . db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") as count_account_historys',
				'id',
				];
				
				$where = [];
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'wh_loss_adjustment.id and ' . db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'wh_loss_adjustment.id and ' . db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'wh_loss_adjustment.id and ' . db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'wh_loss_adjustment.id and ' . db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'wh_loss_adjustment.date_create >= "' . $from_date . '" and ' . db_prefix() . 'wh_loss_adjustment.date_create <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'wh_loss_adjustment.date_create >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'wh_loss_adjustment.date_create <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'wh_loss_adjustment';
				$join         = [
				];
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$name = _l($aRow['type']);
					$name .= '<div class="row-options">';
					
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_create'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$name .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="loss-adjustment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="loss_adjustment">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$name .= '<a href="#" onclick="convert(this); return false;" id="loss-adjustment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="loss_adjustment">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$name .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'loss_adjustment\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					$name .= '</div>';
					$row[] = $name;
					
					$row[] = _dt($aRow['time']);
					
					$status = '';
					if ((int) $aRow['status'] == 0) {
						$status = '<div class="btn btn-warning" >' . _l('draft') . '</div>';
						} elseif ((int) $aRow['status'] == 1) {
						$status = '<div class="btn btn-success" >' . _l('Adjusted') . '</div>';
						} elseif((int) $aRow['status'] == -1){
						
						$status = '<div class="btn btn-danger" >' . _l('reject') . '</div>';
					}
					
					$row[] = $status;
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					$row[] = '<span class="label label-' . $label_class . ' s-status stock-export-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date_create'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-type' => 'loss_adjustment',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* update payslip automatic conversion
		*/
		public function update_payslip_automatic_conversion(){
			if (!has_permission_new('accounting_setting', '', 'edit') && !is_admin()) {
				access_denied('accounting_setting');
			}
			$data = $this->input->post();
			$success = $this->accounting_model->update_payslip_automatic_conversion($data);
			if($success == true){
				$message = _l('updated_successfully', _l('setting'));
				set_alert('success', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup&tab=payslip'));
		}
		
		/**
			* opening stock table
			* @return json
		*/
		public function opening_stock_table()
		{
			if ($this->input->is_ajax_request()) {
				$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
				
				$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
				
				$this->load->model('warehouse/warehouse_model');
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1',
				'commodity_code',
				'description',
				'sku_code',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'items.id and ' . db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") as count_account_historys',
				'id',
				];
				
				$where = [];
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'items.id and ' . db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'items.id and ' . db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'items.id and ' . db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'items.id and ' . db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . db_prefix() . 'acc_account_history.date >= "'.$date_financial_year.'") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'items';
				$join         = [
				];
				
				$result = $this->accounting_model->get_opening_stock_data_tables($aColumns, $sIndexColumn, $sTable, $join, $where, []);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$code = '<a href="' . admin_url('warehouse/view_commodity_detail/' . $aRow['id']) . '">' . $aRow['commodity_code'] . '</a>';
					$code .= '<div class="row-options">';
					
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && ($acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$code .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="opening-stock-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="opening_stock" data-amount="'.$aRow['opening_stock'].'">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$code .= '<a href="#" onclick="convert(this); return false;" id="opening-stock-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="opening_stock" data-amount="'.$aRow['opening_stock'].'">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$code .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'opening_stock\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					$code .= '</div>';
					
					$row[] = $code;
					
					$inventory = $this->warehouse_model->check_inventory_min($aRow['id']);
					
					if ($inventory) {
						$row[] = '<a href="#" onclick="show_detail_item(this);return false;" data-name="' . $aRow['description'] . '"  data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
						} else {
						
						$row[] = '<a href="#" class="text-danger"  onclick="show_detail_item(this);return false;" data-name="' . $aRow['description'] . '" data-warehouse_id="' . $aRow['warehouse_id'] . '" data-commodity_id="' . $aRow['id'] . '"  >' . $aRow['description'] . '</a>';
						
					}
					
					$row[] = '<span class="label label-tag tag-id-1"><span class="tag">' . $aRow['sku_code'] . '</span><span class="hide">, </span></span>&nbsp';
					$row[] = app_format_money($aRow['opening_stock'], $currency->name);
					
					$status_name = _l('has_not_been_converted');
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					$row[] = '<span class="label label-' . $label_class . ' s-status stock-export-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && ($acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-type' => 'opening_stock',
						'data-amount' => $aRow['opening_stock'],
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* update warehouse automatic conversion
		*/
		public function update_warehouse_automatic_conversion(){
			if (!has_permission_new('accounting_setting', '', 'edit') && !is_admin()) {
				access_denied('accounting_setting');
			}
			$data = $this->input->post();
			$success = $this->accounting_model->update_warehouse_automatic_conversion($data);
			if($success == true){
				$message = _l('updated_successfully', _l('setting'));
				set_alert('success', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup&tab=warehouse'));
		}
		
		/**
			* purchase payment table
			* @return json
		*/
		public function purchase_payment_table()
		{
			if ($this->input->is_ajax_request()) {
				$this->load->model('currencies_model');
				
				$currency = $this->currencies_model->get_base_currency();
				$acc_closing_date = '';
				if(get_option('acc_close_the_books') == 1){
					$acc_closing_date = get_option('acc_closing_date');
				}
				$select = [
				'1', // bulk actions
				db_prefix() . 'pur_invoice_payment.id as id',
				'amount',
				db_prefix() . 'payment_modes.name as name',
				db_prefix() . 'pur_invoices.pur_order',
				db_prefix() .'pur_invoice_payment.date as date',
				'(select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_invoice_payment.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_payment") as count_account_historys'
				];
				$where = [];
				array_push($where, 'AND (' . db_prefix() . 'pur_invoices.pur_order is not null)');
				
				if ($this->input->post('status')) {
					$status = $this->input->post('status');
					$where_status = '';
					foreach ($status as $key => $value) {
						if($value == 'converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_invoice_payment.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_payment") > 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_invoice_payment.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_payment") > 0)';
							}
						}
						
						if($value == 'has_not_been_converted'){
							if($where_status != ''){
								$where_status .= ' or ((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_invoice_payment.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_payment") = 0)';
								}else{
								$where_status .= '((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_invoice_payment.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_payment") = 0)';
							}
						}
					}
					
					if($where_status != ''){
						array_push($where, 'AND ('. $where_status . ')');
					}
				}
				
				$from_date = '';
				$to_date   = '';
				if ($this->input->post('from_date')) {
					$from_date = $this->input->post('from_date');
					if (!$this->accounting_model->check_format_date($from_date)) {
						$from_date = to_sql_date($from_date);
					}
				}
				
				if ($this->input->post('to_date')) {
					$to_date = $this->input->post('to_date');
					if (!$this->accounting_model->check_format_date($to_date)) {
						$to_date = to_sql_date($to_date);
					}
				}
				if ($from_date != '' && $to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'pur_invoice_payment.date >= "' . $from_date . '" and ' . db_prefix() . 'pur_invoice_payment.date <= "' . $to_date . '")');
					} elseif ($from_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'pur_invoice_payment.date >= "' . $from_date . '")');
					} elseif ($to_date != '') {
					array_push($where, 'AND (' . db_prefix() . 'pur_invoice_payment.date <= "' . $to_date . '")');
				}
				
				$aColumns     = $select;
				$sIndexColumn = 'id';
				$sTable       = db_prefix() . 'pur_invoice_payment';
				$join         = ['LEFT JOIN ' . db_prefix() . 'payment_modes ON ' . db_prefix() . 'payment_modes.id = ' . db_prefix() . 'pur_invoice_payment.paymentmode',
				'LEFT JOIN ' . db_prefix() . 'acc_account_history ON ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_invoice_payment.id and ' . db_prefix() . 'acc_account_history.rel_id = "purchase_payment"',
				'LEFT JOIN ' . db_prefix() . 'pur_invoices ON ' . db_prefix() . 'pur_invoices.id = ' . db_prefix() . 'pur_invoice_payment.pur_invoice',
				];
				
				$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['paymentmode', db_prefix() . 'pur_invoices.pur_order']);
				
				$output  = $result['output'];
				$rResult = $result['rResult'];
				
				foreach ($rResult as $aRow) {
					$row   = [];
					$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
					
					$categoryOutput = _d($aRow['date']);
					
					$categoryOutput .= '<div class="row-options">';
					if ($aRow['count_account_historys'] == 0) {
						if (has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" class="text-success" id="purchase-payment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_payment" data-amount="'.$aRow['amount'].'">' . _l('acc_convert') . '</a>';
						}
						}else{
						if (has_permission_new('accounting_transaction', '', 'edit')) {
							$categoryOutput .= '<a href="#" onclick="convert(this); return false;" id="purchase-payment-id-'.$aRow['id'].'" data-id="'.$aRow['id'].'" data-type="purchase_payment" data-amount="'.$aRow['amount'].'">' . _l('edit') . '</a>';
						}
						if (has_permission_new('accounting_transaction', '', 'delete')) {
							$categoryOutput .= ' | <a href="#" onclick="delete_convert('.$aRow['id'].', \'purchase_payment\'); return false;" class="text-danger">' . _l('delete') . '</a>';
						}
					}
					
					
					
					$categoryOutput .= '</div>';
					$row[] = $categoryOutput;
					
					$row[] = app_format_money($aRow['amount'], $currency->name);
					
					$row[] = $aRow['name'];
					
					$row[] = '<a href="'.admin_url('purchase/purchase_order/'.$aRow[db_prefix().'pur_invoices.pur_order']).'">'.get_pur_order_subject($aRow[ db_prefix().'pur_invoices.pur_order']).'</a>';
					
					$status_name = _l('has_not_been_converted');
					
					$label_class = 'default';
					
					if ($aRow['count_account_historys'] > 0) {
						$label_class = 'success';
						$status_name = _l('acc_converted');
					} 
					
					$row[] = '<span class="label label-' . $label_class . ' s-status payment-status-' . $aRow['id'] . '">' . $status_name . '</span>';
					
					$options = '';
					if($aRow['count_account_historys'] == 0 && has_permission_new('accounting_transaction', '', 'create') && (($acc_closing_date != '' && strtotime($acc_closing_date) <= strtotime($aRow['date'])) || $acc_closing_date == '' || strtotime(date('Y-m-d')) <= strtotime($acc_closing_date))){
						$options = icon_btn('#', 'share', 'btn-success', [
						'title' => _l('acc_convert'),
						'data-id' =>$aRow['id'],
						'data-amount' => $aRow['amount'],
						'data-type' => 'purchase_payment',
						'onclick' => 'convert(this); return false;'
						]);
					}
					
					$row[] =  $options;
					
					$output['aaData'][] = $row;
				}
				
				echo json_encode($output);
				die();
			}
		}
		
		/**
			* update purchase automatic conversion
		*/
		public function update_purchase_automatic_conversion(){
			if (!has_permission_new('accounting_setting', '', 'edit') && !is_admin()) {
				access_denied('accounting_setting');
			}
			$data = $this->input->post();
			$success = $this->accounting_model->update_purchase_automatic_conversion($data);
			if($success == true){
				$message = _l('updated_successfully', _l('setting'));
				set_alert('success', $message);
			}
			redirect(admin_url('accounting/setting?group=mapping_setup&tab=purchase'));
		}
		
		/**
			* Budget
			* @return view
		*/
		public function budget(){
			if ($this->input->post()) {
				$data = $this->input->post();
				$message = '';
				
				if (!has_permission_new('accounting_budget', '', 'edit')) {
					access_denied('accounting_budget');
				}
				
				$success = $this->accounting_model->update_budget_detail($data);
				if ($success) {
					$message = _l('updated_successfully', _l('budget'));
				}
				
				echo json_encode([
				'success' => $success,
				'message' => $message,
				]);
				die();
			}
			if (!has_permission_new('accounting_budget', '', 'view')) {
				access_denied('budget');
			}
			
			$data['budgets'] = $this->accounting_model->get_budgets();
			
			if(count($data['budgets']) > 0){
				$data_fill = [];
				$data_fill['budget'] = $data['budgets'][0]['id'];
				$data_fill['view_type'] = 'monthly';
				
				$data['nestedheaders'] = $this->accounting_model->get_nestedheaders_budget($data['budgets'][0]['id'], 'monthly');
				$data['columns'] = $this->accounting_model->get_columns_budget($data['budgets'][0]['id'], 'monthly');
				$data['data_budget'] = $this->accounting_model->get_data_budget($data_fill);
				}else{
				$data['nestedheaders'] = [];
				$data['columns'] = [];
				$data['data_budget'] =[];
				$data['hide_handson'] = 'true';
			}
			
			$data['title'] = _l('budget');
			$this->load->view('budget/manage', $data);
		}
		
		/**
			* Gets the data budget.
			* @return json data budget
		*/
		public function get_data_budget() {
			$data = $this->input->post();
			
			$data_budget = $this->accounting_model->get_data_budget($data);
			$nestedheaders = $this->accounting_model->get_nestedheaders_budget($data['budget'], $data['view_type']);
			$columns = $this->accounting_model->get_columns_budget($data['budget'], $data['view_type']);
			echo json_encode([
			'columns' => $columns,
			'nestedheaders' => $nestedheaders,
			'data_budget' => $data_budget,
			]);
			die();
		}
		
		/**
			* Add budget.
			* @return json data budget
		*/
		public function add_budget() {
			$data = $this->input->post();
			
			$budget = $this->accounting_model->add_budget($data);
			$budget_id = '';
			$success = false;
			$message = _l('add_failure');
			$name = $data['year'].' - '. _l($data['type']);
			
			if($budget){
				$message = _l('added_successfully', _l('acc_account'));
				$success = true;
				$budget_id = $budget;
			}
			echo json_encode([
			'name' => $name,
			'id' => $budget_id,
			'success' => $success,
			'message' => $message
			]);
			die();
		}
		
		/**
			* check budget.
			* @return json data budget
		*/
		public function check_budget() {
			$data = $this->input->post();
			
			$success = $this->accounting_model->check_budget($data);
			
			echo json_encode([
			'success' => $success,
			]);
			die();
		}
		
		/**
			* update budget.
			* @return json data budget
		*/
		public function update_budget() {
			$data = $this->input->post();
			$success = false;
			if (isset($data['budget'])) {
				$id = $data['budget'];
				unset($data['budget']);
				
				$success = $this->accounting_model->update_budget($data, $id);
			}
			
			echo json_encode([
			'success' => $success,
			]);
			die();
		}
		
		/**
			* reconcile restored
			* @param  [type] $account 
			* @param  [type] $company 
			* @return [type]          
		*/
		public function reconcile_restored($account) {
			if ($this->input->is_ajax_request()) {
				$success = false;
				$message = _l('acc_restored_failure');
				$hide_restored = true;
				
				$reconcile_restored = $this->accounting_model->reconcile_restored($account);
				if($reconcile_restored){
					$success = true;
					$message = _l('acc_restored_successfully');
				}
				
				$check_reconcile_restored = $this->accounting_model->check_reconcile_restored($account);
				if($check_reconcile_restored){
					$hide_restored = false;
				}
				
				$closing_date = false;
				$reconcile = $this->accounting_model->get_reconcile_by_account($account);
				
				if ($reconcile) {
					if(get_option('acc_close_the_books') == 1){
						$closing_date = (strtotime(get_option('acc_closing_date')) > strtotime(date('Y-m-d'))) ? true : false;
					}
				}
				
				echo json_encode([
				'success' => $success,
				'hide_restored' => $hide_restored,
				'closing_date' => $closing_date,
				'message' => $message,
				]);
				die();
			}
		}
		
		/**
			* report Accounts receivable ageing detail
			* @return view
		*/
		public function rp_accounts_receivable_ageing_detail() {
			$this->load->model('currencies_model');
			$data['title'] = _l('accounts_receivable_ageing_detail');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/accounts_receivable_ageing_detail', $data);
		}
		
		/**
			* report Accounts payable ageing detail
			* @return view
		*/
		public function rp_accounts_payable_ageing_detail() {
			$this->load->model('currencies_model');
			$data['title'] = _l('accounts_payable_ageing_detail');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/accounts_payable_ageing_detail', $data);
		}
		
		/**
			* report Accounts receivable ageing summary
			* @return view
		*/
		public function rp_accounts_receivable_ageing_summary() {
			$this->load->model('currencies_model');
			$data['title'] = _l('accounts_receivable_ageing_summary');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/accounts_receivable_ageing_summary', $data);
		}
		
		/**
			* report Accounts payable ageing summary
			* @return view
		*/
		public function rp_accounts_payable_ageing_summary() {
			$this->load->model('currencies_model');
			$data['title'] = _l('accounts_payable_ageing_summary');
			$data['from_date'] = date('Y-m-01');
			$data['to_date'] = date('Y-m-d');
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/accounts_payable_ageing_summary', $data);
		}
		
		/**
			* report profit and loss trailing 12 months
			* @return view
		*/
		public function rp_profit_and_loss_12_months() {
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss_12_months');
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$data['from_date'] = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			$data['to_date'] = date('Y-m-t', strtotime($data['from_date'] . '  - 1 month + 1 year '));
			
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$this->load->view('report/includes/profit_and_loss_12_months', $data);
		}
		
		/**
			* report budget overview
			* @return view
		*/
		public function rp_budget_overview() {
			$this->load->model('currencies_model');
			$data['title'] = _l('budget_overview');
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$data['from_date'] = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			$data['to_date'] = date('Y-m-t', strtotime($data['from_date'] . '  - 1 month + 1 year '));
			
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['budgets'] = $this->accounting_model->get_budgets();
			$this->load->view('report/includes/budget_overview', $data);
		}
		
		/**
			* rp profit and loss budget performance
		*/
		public function rp_profit_and_loss_budget_performance(){
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss_budget_performance');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['budgets'] = $this->accounting_model->get_budgets('', 'type = "profit_and_loss_accounts"');
			
			$this->load->view('report/includes/profit_and_loss_budget_performance', $data);
		}
		
		/**
			* profit and loss budget vs actual
		*/
		public function rp_profit_and_loss_budget_vs_actual(){
			$this->load->model('currencies_model');
			$data['title'] = _l('profit_and_loss_budget_vs_actual');
			$data['from_date'] = date('Y-01-01');
			$data['to_date'] = date('Y-m-d');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['budgets'] = $this->accounting_model->get_budgets('', 'type = "profit_and_loss_accounts"');
			
			$this->load->view('report/includes/profit_and_loss_budget_vs_actual', $data);
		}
		
		//====================== Load TDS Report Page ==================================
		public function TDSReport()
		{
			if (!has_permission_new('accounting_tds_report', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data['title'] = "Tax Deducted at Source (TDS)";
			$selected_company = $this->session->userdata('root_company');
			$data['company_detail'] = $this->accounting_model->get_company_detail1($selected_company);
			$this->load->view('TDS/Manage', $data);
		}
		//============================== Get TDS Report ================================
		public function GetTDSReport()
		{		
			if (!has_permission_new('accounting_tds_report', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data =$this->accounting_model->GetTDSReport($this->input->post());
			$selected_company = $this->session->userdata('root_company');
			$html ='';
			if(count($data) > 0){
				$i =1;
				$TotalTaxableAmt = 0;
				$TotalTdsAmt = 0;
				$TotalBillAmt = 0;
				foreach($data as $value){
					$taxableAmt = 0;
					$html.= '<tr>';
					$html.= '<td align="center">'.$i.'</td>';
					$html.= '<td align="center">'.$value['PO_Number'].'</td>';
					$html.= '<td align="center">'.$value['PurchID'].'</td>';
					$date = substr($value['Transdate'],0,10);
					$html .= '<td align="center">'._d($date).'</td>';
					$html.= '<td align="center">'.$value['Invoiceno'].'</td>';
					$html.= '<td align="center">'._d(substr($value['Invoicedate'],0,10)).'</td>';
					$html.= '<td>'.$value['company'].'</td>';
					
					$taxableAmt = $value['Purchamt'] - $value['Discamt'];
					$TotalTaxableAmt += $taxableAmt;
					$TotalTdsAmt += $value['TdsAmt'];
					$TotalBillAmt += $value['Invamt'];
					$html.= '<td align="left">'.$value['TDSName'].'</td>';
					$html.= '<td align="right">'.$value['TdsRate'].'</td>';
					$html.= '<td align="right">'.$taxableAmt.'</td>';
					$html.= '<td align="right">'.$value['TdsAmt'].'</td>';
					$html.= '<td align="right">'.$value['Invamt'].'</td>';
					$html.= '</tr>';
					$i++; 
				}
				$html.= '<tr>';
				$html.= '<td colspan="9" align="right"><b>Total Rs.</b></td>';
				$html.= '<td align="right"><b>'.number_format($TotalTaxableAmt, 2, '.', '').'</b></td>';
				$html.= '<td align="right"><b>'.number_format($TotalTdsAmt, 2, '.', '').'</b></td>';
				$html.= '<td align="right"><b>'.number_format($TotalBillAmt, 2, '.', '').'</b></td>';
				$html.= '</tr>';
				}else{
				$html.= '<span style="color:red;">No record Found..</span>'; 
			}
			echo $html;
		}
		//==================== Export TDS Report =======================================
		public function ExportTDSReport()
		{
			if (!has_permission_new('accounting_tds_report', '', 'export')) {
				access_denied('accounting_tcs_report');
			}
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				
				$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'  => $this->input->post('to_date')
				);
				$data =$this->accounting_model->GetTDSReport($this->input->post());
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();
				
				$writer = new XLSXWriter();
				
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				
				$msg = "Tax Deducted at Source (TDS) : ".$this->input->post('from_date')." To " .$this->input->post('to_date');
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);
				
				$set_col_tk = [];
				$set_col_tk["Purchase Order"] =  'Purchase Order';
				$set_col_tk["Purchase Invoice"] = 'Purchase Invoice';
				$set_col_tk["Purch Invoice Date"] = 'Purch Invoice Date';
				$set_col_tk["Vendor Invoice No"] = 'Vendor Invoice No';
				$set_col_tk["Vendor Invoice Date"] = 'Vendor Invoice Date';
				$set_col_tk["Vendor Name"] = 'Vendor Name';
				$set_col_tk["TDS Section Name"] = 'TDS Section Name';
				$set_col_tk["TDS Rate %"] = 'TDS Rate %';
				$set_col_tk["Taxable Amt"] = 'Taxable Amt';
				$set_col_tk["TDS Amt"] = 'TDS Amt';
				$set_col_tk["Bill Amt"] = 'Bill Amt';
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				$i =1;
				$taxable_amt = 0;
				$tcs = 0;
				foreach ($data as $k => $value) {
					$list_add = [];
					$list_add[] = $value["PO_Number"];
					$list_add[] = $value["PurchID"];
					$date = substr($value['Transdate'],0,10);
					$list_add[] = $date;
					$list_add[] = $value["Invoiceno"];
					$Invoicedate = _d(substr($value["Invoicedate"],0,10));
					$list_add[] = $Invoicedate;
					$list_add[] = $value["company"];
					$taxableAmt = $value['Purchamt'] - $value['Discamt'];
					$TotalTaxableAmt += $taxableAmt;
					$TotalTdsAmt += $value['TdsAmt'];
					$TotalBillAmt += $value['Invamt'];
					
					$list_add[] = $value["TDSName"];
					$list_add[] = $value["TdsRate"];
					$list_add[] = $taxableAmt;
					$list_add[] = $value["TdsAmt"];
					$list_add[] = $value["Invamt"];
					$writer->writeSheetRow('Sheet1', $list_add);
				}
				
				$list_add = [];
				$list_add[] = "Total Rs.";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = $TotalTaxableAmt;
				$list_add[] = $TotalTdsAmt;
				$list_add[] = $TotalBillAmt;
				$writer->writeSheetRow('Sheet1', $list_add);
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'TDS_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		//====================== Load TCS Report Page ==================================
		public function tcs_data()
		{
			if (!has_permission_new('accounting_tcs_report', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data['title'] = _l('Tax Collection at source');
			$selected_company = $this->session->userdata('root_company');
			$data['company_detail'] = $this->accounting_model->get_company_detail1($selected_company);
			$this->load->view('tcs/manage', $data);
		}
		//============================== Get TCS Report ================================
		public function load_data_for_tcs()
		{		
			if (!has_permission_new('accounting_tcs_report', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data =$this->accounting_model->tcs_table_data($this->input->post());
			$selected_company = $this->session->userdata('root_company');
			$html ='';
			if(count($data) > 0){
				$i =1;
				$taxable_amt = 0;
				$tcs = 0;
				foreach($data as $value){
					$html.= '<tr>';
					$html.= '<td align="center">'.$i.'</td>';
					$html.= '<td>'.$value['company'].'</td>';
					$html.= '<td>'.$value['address'].'</td>';
					$html.= '<td>'.$value['Pan'].'</td>';
					$html.= '<td>'.$value['SalesID'].'</td>';
					$date = substr($value['Transdate'],0,10);
					$html .= '<td>'._d($date).'</td>';
					$taxableAmt = $value['BillAmt'] - $value['tcsAmt'];
					$taxable_amt+=$taxableAmt;
					$tcs+=$value['tcsAmt'];
					$html.= '<td align="right">'.$taxableAmt.'</td>';
					$html.= '<td align="right">'.$value['tcs'].'</td>';
					$html.= '<td align="right">'.$value['tcsAmt'].'</td>';
					
					$html.= '</tr>';
				$i++; }
				$html.= '<tr>';
				
				$html.= '<td><b>Total Rs.</b></td>';
				$html.= '<td></td>';
				$html.= '<td></td>';
				$html.= '<td></td>';
				$html.= '<td></td>';
				$html.= '<td></td>';
				$html.= '<td><b>'.$taxable_amt.'</b></td>';
				$html.= '<td></td>';
				$html.= '<td><b>'.$tcs.'</b></td>';
				
				
				$html.= '</tr>';
				}else{
				$html.= '<span style="color:red;">No record Found..</span>'; 
			}
			echo $html;
		}
		//==================== Export TCS Report =======================================
		public function export_tcs_report()
		{
			if (!has_permission_new('accounting_tcs_report', '', 'export')) {
				access_denied('accounting_tcs_report');
			}
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				
				$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'  => $this->input->post('to_date')
				);
				$data =$this->accounting_model->tcs_table_data($this->input->post());
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();
				
				$writer = new XLSXWriter();
				
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				
				$msg = "TCS Report ".$this->input->post('from_date')." To " .$this->input->post('to_date');
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);
				
				// empty row
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				
				$set_col_tk = [];
				$set_col_tk["AccountName"] =  'AccountName';
				$set_col_tk["Address"] = 'Address';
				$set_col_tk["Pan"] = 'Pan';
				$set_col_tk["SaleID"] = 'SaleID';
				$set_col_tk["BillDate"] = ' BillDate';
				$set_col_tk["TaxableAmt"] = 'TaxableAmt';
				$set_col_tk["TCS%"] = 'TCS%';
				$set_col_tk["TCSAmt"] = 'TCSAmt';
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				$i =1;
				$taxable_amt = 0;
				$tcs = 0;
				foreach ($data as $k => $value) {
					$RndAmt = $value["RndAmt"];
					$grand_total = $grand_total + $RndAmt ;
					$list_add = [];
					$list_add[] = $value["company"];
					$list_add[] = $value["address"];
					$list_add[] = $value["Pan"];
					$list_add[] = $value["SalesID"];
					$date = _d(substr($value["Transdate"],0,10));
					$list_add[] = $date;
					$taxableAmt = $value['BillAmt'] - $value['tcsAmt'];
					$taxable_amt+=$taxableAmt;
					$tcs+=$value['tcsAmt'];
					$list_add[] = $taxableAmt;
					$list_add[] = $value["tcs"];
					$list_add[] = $value["tcsAmt"];
					
					
					$writer->writeSheetRow('Sheet1', $list_add);
					
					
					
				}
				
				$list_add = [];
				$list_add[] = "Total Rs.";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = $taxable_amt;
				$list_add[] = "";
				$list_add[] = $tcs;
				$writer->writeSheetRow('Sheet1', $list_add);
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'TCS_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		//==============================================================================    
		// Account Monitor
		
		public function AccountMonitor(){
			if(!has_permission_new('account_monitor_report', '', 'view')) {
				access_denied('account_monitor_report');
			}
			$data['title'] = "Account Monitor";
			$this->load->model('accounts_master_model');
			$data['company_detail'] = $this->accounts_master_model->get_company_detail();
			$data['account_subgroup_table'] = $this->accounts_master_model->get_actsubgroup_data();
			$data['account_maingroup'] = $this->accounts_master_model->get_act_maingroup();
			$this->load->view('AccountMonitor/AccountMonitor', $data);
		}
		
		public function GetAccountMonitor(){
			if(!has_permission_new('account_monitor_report', '', 'view')) {
				access_denied('account_monitor_report');
			}
			$data_post = $this->input->post();
			$AccountData =$this->accounting_model->GetAccountMonitor($this->input->post());
			$AccountDataStaff =$this->accounting_model->GetAccountMonitorStaff($this->input->post());
			$AccountIDs = array();
			foreach($AccountData as $key=> $value){
				array_push($AccountIDs,$value["AccountID"]);
			}
			foreach($AccountDataStaff as $key1 => $value1){
				array_push($AccountIDs,$value1["AccountID"]);
			}
			
			$account_ids = array_unique($AccountIDs);
			$account_ids_str = "";
			$ii = 1;
			foreach ($account_ids as $id) {
				if($ii == "1"){
					$account_ids_str = '"'.$id.'"';
					}else{
					$account_ids_str = $account_ids_str.',"'.$id.'"';
				}
				$ii++;
			}
			
			$CreditData =$this->accounting_model->GetAccountMonitorCreditNew($this->input->post(),$account_ids_str);
			$DebitData =$this->accounting_model->GetAccountMonitorDebitNew($this->input->post(),$account_ids_str);
			//$CreditData =$this->accounting_model->GetAccountMonitorCredit($this->input->post());
			//$DebitData =$this->accounting_model->GetAccountMonitorDebit($this->input->post());
			/*echo "<pre>";
				print_r($DebitData);
			die;*/
			$mergeArray = array_merge($AccountData,$AccountDataStaff);
			array_multisort(array_column($mergeArray, 'SubActGroupName'), SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $mergeArray);
			
			$date1 = to_sql_date($this->input->post('from_date'));
			$date2 = to_sql_date($this->input->post('to_date'));
			$output = [];
			$time   = strtotime($date1);
			$last   = date('M-Y', strtotime($date2));
			
			do {
				$month = date('M-Y', $time);
				$total = date('t', $time);
				
				$output[] = $month;
				
				$time = strtotime('+1 month', $time);
			} while ($month != $last);
			
			//print_r($AccountData);die;
			$html ='';
			$html .='<table class="tree table table-striped table-bordered table-account_monitor tableFixHead" id="table-account_monitor" width="100%">';
			$html .='<thead>';
			$html .='<tr>';
			$html .='<th class="sortable">SrNo</th>';
			$html .='<th class="sortable">AccountID</th>';
			$html .='<th class="sortable">AccountName</th>';
			$html .='<th class="sortable">AccountGroupName</th>';
			foreach($output as $value){
				$html .='<th class="sortable" style="text-align:right;">'.$value.'</th>';
			}
			$html .='<th class="sortable" style="text-align:right;">Total</th>';
			$html .='<th class="sortable" style="text-align:right;">Average</th>';
			$html .='</tr>';
			$html .='</thead>';
			$html .='<tbody id="table-account_monitor_filter">';
			$srNo = 1;
			foreach($mergeArray as $value){
				$CHKBAL = 0;
				$CBALCHK = 0;
				$DBALCHK = 0;
				foreach($CreditData as $value1){
					if(trim(strtoupper($value["AccountID"])) === trim(strtoupper($value1["AccountID"]))){
						$CBALCHK = $value1["total_amount"];
					}
				}
				
				foreach($DebitData as $value2){
					if(trim(strtoupper($value["AccountID"])) === trim(strtoupper($value2["AccountID"]))){
						$DBALCHK = $value2["total_amount"];
					}
				}
				$CHKBAL = $CBALCHK - $DBALCHK;
				if($CHKBAL == "0" || $CHKBAL == "" || $CHKBAL == "0.00"){
					
					}else{
					if($value["company"] == ""){
						$accountName = $value["firstname"] . " ".$value["lastname"];
						}else{
						$accountName = $value["company"];
					}
					$html .='<tr>';
					$html .='<td style="text-align:center;">'.$srNo.'</td>';
					$html .='<td style="text-align:left;">'.$value["AccountID"].'</td>';
					$html .='<td style="text-align:left;">'.$accountName.'</td>';
					$html .='<td style="text-align:left;">'.$value["SubActGroupName"].'</td>';
					$RowTotal = 0;
					$count = 0;
					foreach($output as $Month){
						$count++;
						$month = substr($Month,0,3);
						$year = substr($Month,4,4);
						$monthNumber = date('m', strtotime(strtoupper($month)));
						$CBAL = 0;
						$DBAL = 0;
						foreach($CreditData as $value1){
							if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value1["AccountID"])) && $value1['month']== $monthNumber && $value1['year']== $year){
								$CBAL = $value1["total_amount"];
							}
						}
						foreach($DebitData as $value2){
							if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value2["AccountID"])) && $value2['month']== $monthNumber && $value2['year']== $year){
								$DBAL = $value2["total_amount"];
							}
						}
						if($DBAL == "0" && $CBAL == "0"){
							$Bal = '';
							}else{
							$Bal = $DBAL - $CBAL;
							$Bal = number_format($Bal, 2, '.', '');
							$RowTotal = $RowTotal + $Bal;
						}
						$html .='<td style="text-align:right;">'.$Bal.'</td>';
						
					}
					$html .='<td style="text-align:right;">'.number_format($RowTotal, 2, '.', '').'</td>';
					$Avg = $RowTotal/$count;
					$html .='<td style="text-align:right;">'.number_format($Avg, 2, '.', '').'</td>';
					$srNo++;
					$html .='</tr>';
				}
			}
			$html .='</tbody>';
			$html .='</table>';
			echo $html;
			
		}
		
		public function ExportAccountMonitor(){
			if(!has_permission_new('account_monitor_report', '', 'view')) {
				access_denied('account_monitor_report');
			}
			
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				$data_post = $this->input->post();
				//$CreditData =$this->accounting_model->GetAccountMonitorCredit($this->input->post());
				//$DebitData =$this->accounting_model->GetAccountMonitorDebit($this->input->post());
				$AccountData =$this->accounting_model->GetAccountMonitor($this->input->post());
				$AccountDataStaff =$this->accounting_model->GetAccountMonitorStaff($this->input->post());
				$AccountIDs = array();
				foreach($AccountData as $key=> $value){
					array_push($AccountIDs,$value["AccountID"]);
				}
				foreach($AccountDataStaff as $key1 => $value1){
					array_push($AccountIDs,$value1["AccountID"]);
				}
				$account_ids = array_unique($AccountIDs);
				$account_ids_str = "";
				$ii = 1;
				foreach ($account_ids as $id) {
					if($ii == "1"){
						$account_ids_str = '"'.$id.'"';
						}else{
						$account_ids_str = $account_ids_str.',"'.$id.'"';
					}
					$ii++;
				}
				
				$CreditData =$this->accounting_model->GetAccountMonitorCreditNew($this->input->post(),$account_ids_str);
				$DebitData =$this->accounting_model->GetAccountMonitorDebitNew($this->input->post(),$account_ids_str);
				$mergeArray = array_merge($AccountData,$AccountDataStaff);
				array_multisort(array_column($mergeArray, 'SubActGroupName'), SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $mergeArray);
				
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();
				
				
				$date1 = to_sql_date($this->input->post('from_date'));
				$date2 = to_sql_date($this->input->post('to_date'));
				$output = [];
				$time   = strtotime($date1);
				$last   = date('M-Y', strtotime($date2));
				
				do {
					$month = date('M-Y', $time);
					$total = date('t', $time);
					
					$output[] = $month;
					
					$time = strtotime('+1 month', $time);
				} while ($month != $last);
				
				$writer = new XLSXWriter();
				
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				
				$msg = "Account Monitor ".$this->input->post('from_date')." To " .$this->input->post('to_date');
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);
				
				// empty row
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				$set_col_tk = [];
				$set_col_tk["AccountID"] =  'AccountID';
				$set_col_tk["AccountName"] = 'AccountName';
				$set_col_tk["AccountGroupName"] = 'AccountGroupName';
				
				foreach($output as $value){
					$set_col_tk[$value] = $value;
				}
				$set_col_tk["Total"] = 'Total';
				$set_col_tk["Average"] = ' Average';
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				foreach($mergeArray as $value){
					$CHKBAL = 0;
					$CBALCHK = 0;
					$DBALCHK = 0;
					foreach($CreditData as $value1){
						if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value1["AccountID"]))){
							$CBALCHK = $value1["total_amount"];
						}
					}
					
					foreach($DebitData as $value2){
						if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value2["AccountID"]))){
							$DBALCHK = $value2["total_amount"];
						}
					}
					$CHKBAL = $CBALCHK - $DBALCHK;
					if($CHKBAL == "0" || $CHKBAL =="" || $CHKBAL == "0.00"){
						
						}else{
						if($value["company"] == ""){
							$accountName = $value["firstname"] . " ".$value["lastname"];
							}else{
							$accountName = $value["company"];
						}
						$list_add = [];
						$list_add[] = $value["AccountID"];
						$list_add[] = $accountName;
						$list_add[] = $value["SubActGroupName"];
						$RowTotal = 0;
						$count = 0;
						foreach($output as $Month){
							$count++;
							$month = substr($Month,0,3);
							$year = substr($Month,4,4);
							$monthNumber = date('m', strtotime(strtoupper($month)));
							$CBAL = 0;
							$DBAL = 0;
							foreach($CreditData as $value1){
								if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value1["AccountID"])) && $value1['month']== $monthNumber && $value1['year']== $year){
									$CBAL = $value1["total_amount"];
								}
							}
							foreach($DebitData as $value2){
								if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value2["AccountID"])) && $value2['month']== $monthNumber && $value2['year']== $year){
									$DBAL = $value2["total_amount"];
								}
							}
							if($DBAL == "0" && $CBAL == "0"){
								$Bal = '';
								}else{
								$Bal = $DBAL - $CBAL;
								$Bal = number_format($Bal, 2, '.', '');
								$RowTotal = $RowTotal + $Bal;
							}
							$list_add[] = number_format($Bal, 2, '.', '');
						}
						$list_add[] = number_format($RowTotal, 2, '.', '');
						$Avg = $RowTotal/$count;
						$list_add[] = number_format($Avg, 2, '.', '');
						$writer->writeSheetRow('Sheet1', $list_add);
					} 
				}
				
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'AccountMonitor.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		//==============================================================================
		
		//credit_debit_report start
		public function credit_debit_report(){
			if(!has_permission_new('accounting_cd_report', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data['title'] = "Credit Debit Report";
			$this->load->model('accounts_master_model');
			$data['company_detail'] = $this->accounts_master_model->get_company_detail();
			$this->load->view('cd_notes/credit_debit_report', $data);
		}
		
		public function export_credit_debit_report(){
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				
				$data = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'  => $this->input->post('to_date')
				);
				$data =$this->accounting_model->table_data($this->input->post());
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();
				
				$writer = new XLSXWriter();
				$j=0;
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				$j++;
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				$j++;
				if($this->input->post('gst_type') == 1){
					$gst_type = 'All Account';
					}else if($this->input->post('gst_type') == 2){
					$gst_type = 'Get Account';
					}else if($this->input->post('gst_type') == 3){
					$gst_type = 'Non GST Account';
				}
				$msg = "Credit Debit Report ".$this->input->post('from_date')." To " .$this->input->post('to_date');
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);
				$j++;
				
				if($this->input->post('credit_debit_type') == "C"){
					$noteType = "Credit";
					}else if($this->input->post('credit_debit_type') == "D"){
					$noteType = "Debit";
					}else {
					$noteType = "All";
				}
				$msg1 = " Note Type: ".$noteType;
				$filter1 = array($msg1);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter1);
				$j++;
				$msg2 = " GST Type: ".$gst_type;
				$filter2 = array($msg2);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 15);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter2);
				
				// empty row
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				
				$set_col_tk = [];
				$set_col_tk["CDNote"] =  'CDNote';
				$set_col_tk["CDDATE"] =  'CDDATE';
				$set_col_tk["Cr/Dr"] =  'Cr/Dr';
				$set_col_tk["Account_Name"] =  'Account Name';
				$set_col_tk["GSTIN"] =  'GSTIN';
				$set_col_tk["HSN/SAC"] =  'HSN/SAC';
				$set_col_tk["Rate"] =  'Rate';
				$set_col_tk["Qty"] =  'Qty';
				$set_col_tk["CGST%"] =  'CGST%';
				$set_col_tk["CGSTAmt"] =  'CGSTAmt';
				$set_col_tk["SGST%"] =  'SGST%';
				$set_col_tk["SGSTAmt"] =  'SGSTAmt';
				$set_col_tk["IGST%"] =  'IGST%';
				$set_col_tk["IGSTAmt"] =  'IGSTAmt';
				$set_col_tk["Cr/Dr Amt"] =  'Cr/Dr Amt';
				
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				$cgst_amt =0;
				$sgst_amt =0;
				$igst_amt =0;
				$total_amt =0;
				$taxable_amt =0;
				foreach ($data as $k => $value) {
					
					$cgst_amt += $value['cgstamts'];
					$igst_amt += $value['igstamts'];
					$sgst_amt += $value['sgstamts'];
					$taxable_amt += $value['SaleAmt'];
					$total_amt += $value['BillAmt'];
					
					$list_add = [];
					$list_add[] = $value["billno"];
					$list_add[] = date("d/m/Y", strtotime(substr($value['transdate'],0,10)));
					$list_add[] = $value["ttype"];
					$list_add[] = $value["company"];
					$list_add[] = $value["vat"];
					$list_add[] = $value["hsncode"];
					$list_add[] = $value["SaleAmt"];
					$list_add[] = $value["qty"];
					$list_add[] = $value["cgst"];
					$list_add[] = $value["cgstamts"];
					$list_add[] = $value["sgst"];
					$list_add[] = $value["sgstamts"];
					$list_add[] = $value["igst"];
					$list_add[] = $value["igstamts"];
					$list_add[] = $value["BillAmt"];
					
					$writer->writeSheetRow('Sheet1', $list_add);
					
				}
				
				$list_add = [];
				$list_add[] = "Total";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = $taxable_amt;
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = $cgst_amt;
				$list_add[] = "";
				$list_add[] = $sgst_amt;
				$list_add[] = "";
				$list_add[] = $igst_amt;
				$list_add[] = $total_amt;
				$writer->writeSheetRow('Sheet1', $list_add);
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'Credit_debit_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		public function load_data_cd_report(){
			if(!has_permission_new('accounting_cd_report', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data_post = $this->input->post();
			$data =$this->accounting_model->table_data($this->input->post());
			//   print_r($data);die;//
			$selected_company = $this->session->userdata('root_company');
			$html ='';
			$i =1;
			$cgst_amt =0;
			$sgst_amt =0;
			$igst_amt =0;
			$total_amt =0;
			$taxable_amt =0;
			foreach($data as $value){
				$html.= '<tr>';
				
				
				$html.= '<td align="center">'.$i.'</td>';
				$html.= '<td align="center">'.$value['billno'].'</td>';
				$html.= '<td align="center">'.date("d/m/Y", strtotime(substr($value['transdate'],0,10))).'</td>';
				$html.= '<td align="center">'.$value['ttype'].'</td>';
				$html.= '<td>'.$value['company'].'</td>';
				$html.= '<td align="center">'.$value['vat'].'</td>';
				$html.= '<td align="center">'.$value['hsncode'].'</td>';
				$html.= '<td align="right">'.number_format($value['SaleAmt'], 2, '.', '').'</td>';
				$html.= '<td align="right">'.$value['qty'].'</td>';
				$html.= '<td align="right">'.$value['cgst'].'</td>';
				$cgst_amt += $value['cgstamts'];
				$igst_amt += $value['igstamts'];
				$sgst_amt += $value['sgstamts'];
				$total_amt += $value['BillAmt'];
				$taxable_amt += $value['SaleAmt'];
				$html.= '<td align="right">'.number_format($value['cgstamts'], 2, '.', '').'</td>';
				$html.= '<td align="right">'.$value['sgst'].'</td>';
				$html.= '<td align="right">'.number_format($value['sgstamts'], 2, '.', '').'</td>';
				$html.= '<td align="right">'.$value['igst'].'</td>';
				$html.= '<td align="right">'.number_format($value['igstamts'], 2, '.', '').'</td>';
				$html.= '<td align="right">'.number_format($value['BillAmt'], 2, '.', '').'</td>';
				
				$html.= '</tr>';
			$i++;}
			$html.= '<tr>';
			$html.= '<td></td>';
			$html.= '<td><b>Total</b></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td align="right"><b>'.number_format($taxable_amt, 2, '.', '').'</b></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td align="right"><b>'.number_format($cgst_amt, 2, '.', '').'</b></td>';
			$html.= '<td></td>';
			$html.= '<td align="right"><b>'.number_format($sgst_amt, 2, '.', '').'</b></td>';
			$html.= '<td></td>';
			$html.= '<td align="right"><b>'.number_format($igst_amt, 2, '.', '').'</b></td>';
			$html.= '<td align="right"><b>'.number_format($total_amt, 2, '.', '').'</b></td>';
			$html.= '</tr>';
			echo $html;
		}
		//credit_debit_report end
		
		public function get_account_list_by_accoutID()
		{
			// POST data
			$postData = $this->input->post();
			
			// Get data
			$data = $this->accounting_model->get_account_list_by_accoutID($postData);
			
			echo json_encode($data);
		}
		public function get_account_details_by_AccountID()
		{
			$AccountID = $this->input->post('AccountID'); 
			$member = $this->accounting_model->get_account_details_by_AccountID($AccountID);
			//echo json_encode($userID);
			if($member == false){
				echo json_encode("false");
				}else{
				// return $member;
				echo json_encode($member);
			}
			
		}
		
		//Trial Balance
		public function trial_balance_report(){
			if(!has_permission_new('accounting_trial_balance', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data['title'] = "Trial Balance Report";
			$this->load->model('accounts_master_model');
			$data['company_detail'] = $this->accounts_master_model->get_company_detail();
			$this->load->view('trial_balance/trial_balance', $data);
		}
		
		public function trial_balance(){
			if(!has_permission_new('accounting_trial_balance', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data['title'] = "Trial Balance Report";
			$this->load->model('accounts_master_model');
			$data['account_subgroup1'] = $this->accounting_model->get_subgroup_for_accounting_head1();
			$data['account_subgroup'] = $this->accounts_master_model->get_subgroup_for_accounting_head();
			$data['company_detail'] = $this->accounts_master_model->get_company_detail();
			$this->load->view('trial_balance/trial_balanceNew', $data);
		}
		
		public function load_data_trial_balance_reportNew(){
			if(!has_permission_new('accounting_trial_balance', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data_post = $this->input->post();
			
			$creditledgerdata =$this->accounting_model->creditledger_data($this->input->post());
			$debitledgerdata =$this->accounting_model->debitledger_data($this->input->post());
			
			$data = $this->accounting_model->clientData($this->input->post());
			$data2 = $this->accounting_model->staffData($this->input->post());
			$mergeArray = array_merge($data,$data2);
			array_multisort(array_column($mergeArray, 'SubActGroupName'), SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $mergeArray);
			
			$selected_company = $this->session->userdata('root_company');
			$html ='';
			$i =1;
			$cgst_amt =0;
			$sgst_amt =0;
			$igst_amt =0;
			$total_amt =0;
			
			$totalOpn = 0; 
			$totalDAmt = 0;
			$totalCAmt = 0;
			$totalBalAmt = 0;
			
			foreach($mergeArray as $value){
				$balance = 0;
				$debitAmt = 0;
				$creditAmt = 0;
				$OBAL = 0;
				$AccountID = $value["AccountID"];
				$groupName1 = $value['SubActGroupName1'];
				$groupName = $value['SubActGroupName'];
				if($value["company"] == ''){
					$Name = $value['firstname'].' '.$value['lastname'];
					$Address = $value['current_address'].' '.$value['home_town'].' '.$value['city_name'].' '.$value['state_name'].' '.$value['pincode'];
					$OBAL = $value['BAL1'];
					}else{
					$OBAL = $value['BAL1'];
					$Name = $value['company'];
					$Address = $value['address'].' '.$value['Address3'].' '.$value['city_name'].' '.$value['state_name'].' '.$value['zip'];
				}
				
				foreach($debitledgerdata as $value1){
					if(trim(strtoupper($AccountID)) === trim(strtoupper($value1["AccountID"]))){
						$debitAmt = $value1["debit_bal"];
					}
				}
				foreach($creditledgerdata as $value2){
					if(trim(strtoupper($AccountID)) === trim(strtoupper($value2["AccountID"]))){
						$creditAmt = $value2["credit_bal"];
					}
				}
				$balance = $OBAL + $debitAmt - $creditAmt;
				if((($OBAL) == 0) && (($debitAmt) == 0) && (($creditAmt) == 0) && (($balance) == 0) && ($AccountID !== '')){
					
					}else{
					if($AccountID){
						$totalOpn += $value['BAL1'];
						$totalDAmt += $debitAmt;
						$totalCAmt += $creditAmt;
						$totalBalAmt += $balance;
						
						$url = admin_url().'accounting/rp_general_ledger';
						$url2 = "_blank";
						
						/*$html.= '<tr onclick="window.open('."'".$url."'".', '."'".$url2."'".')" class="get_AccountID" data-id="'.$AccountID.'">';*/
						$html.= '<tr class="get_AccountID" data-id="'.$AccountID.'">';
						$html.= '<td align="center">'.$i.'</td>';
						$len = strlen($groupName);
						if($len >30){
							$dots = '...';
							}else{
							$dots = '';
						}
						$html.= '<td align="left">'. substr($groupName1,0,30).$dots.'</td>';
						$html.= '<td align="left">'. substr($groupName,0,30).$dots.'</td>';
						$html.= '<td align="left">'.$AccountID.'</td>';
						$len11 = strlen($Name);
						if($len11 >25){
							$dots11 = '...';
							}else{
							$dots11 = '';
						}
						$html.= '<td align="left">'.substr($Name,0,25).$dots11.'</td>';
						
						$len = strlen($Address);
						if($len >20){
							$dots = '...';
							}else{
							$dots = '';
						}
						$html.= '<td align="left">'.substr($Address,0,20).$dots.'</td>';
						$html.= '<td align="right">'.number_format($value['BAL1'],2).'</td>';
						
						$html.= '<td align="right">'.number_format($debitAmt,2).'</td>';
						
						$html.= '<td align="right">'.number_format($creditAmt,2).'</td>';
						
						if($balance <= 0){
							$cr_dr = "CR";
							}else{
							$cr_dr = "DR";
						}
						$html.= '<td align="right">'.number_format($balance,2).' '.$cr_dr.'</td>';
						$html.= '</tr>';
						$i++;
					}
				}
			}
			
			$html.= '<tr>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td><b>Total</b></td>';
			$html.= '<td align="right"><b>'.number_format($totalOpn,2).'</b></td>';
			$html.= '<td align="right"><b>'.number_format($totalDAmt,2).'</b></td>';
			$html.= '<td align="right"><b>'.number_format($totalCAmt,2).'</b></td>';
			if($totalBalAmt <= 0){
				$cr_dr1 = "CR";
				}else{
				$cr_dr1 = "DR";
			}
			$html.= '<td align="right"><b>'.number_format($totalBalAmt,2).' '.$cr_dr1.'</b></td>';
			$html.= '</tr>';
			
			echo json_encode($html);
			die;
		}
		
		public function load_data_trial_balance_reportNewExport(){
			if(!has_permission_new('accounting_trial_balance', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			ini_set('precision', '15');
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			$data_post = $this->input->post();
			
			$creditledgerdata =$this->accounting_model->creditledger_data($this->input->post());
			$debitledgerdata =$this->accounting_model->debitledger_data($this->input->post());
			
			$data = $this->accounting_model->clientData($this->input->post());
			$data2 = $this->accounting_model->staffData($this->input->post());
			$mergeArray = array_merge($data,$data2);
			array_multisort(array_column($mergeArray, 'SubActGroupName'), SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $mergeArray);
			
			$selected_company = $this->session->userdata('root_company');
			$this->load->model('sale_reports_model');
			$selected_company_details    = $this->sale_reports_model->get_company_detail();
			//$html ='';
			$writer = new XLSXWriter();
			$j=0;
			$company_name = array($selected_company_details->company_name);
			$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col =9);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_name);
			$j++;
			$address = $selected_company_details->address;
			$company_addr = array($address,);
			$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells
			$writer->writeSheetRow('Sheet1', $company_addr);
			$j++;
			
			$msg = "Trial Balance Report: ".$this->input->post('as_on');
			$filter = array($msg);
			$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells
			$writer->writeSheetRow('Sheet1', $filter);
			$j++;
			
			// empty row
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);
			
			$set_col_tk = [];
			$set_col_tk["SrNo"] =  'SrNo';
			$set_col_tk["AccountSubGroup1"] =  'AccountSubGroup1';
			$set_col_tk["AccountSubGroup2"] =  'AccountSubGroup2';
			$set_col_tk["AccountID"] =  'AccountID';
			$set_col_tk["AccountName"] =  'AccountName';
			$set_col_tk["Address"] =  'Address';
			$set_col_tk["OpeningBal"] =  'OpeningBal';
			$set_col_tk["DebitAmt"] =  'DebitAmt';
			$set_col_tk["CreditAmt"] =  'CreditAmt';
			$set_col_tk["Balance"] =  'Balance';
			$set_col_tk["Cr/Dr"] =  'Cr/Dr';
			
			$writer_header = $set_col_tk;
			$writer->writeSheetRow('Sheet1', $writer_header);
			
			
			$i =1;
			$cgst_amt =0;
			$sgst_amt =0;
			$igst_amt =0;
			$total_amt =0;
			
			$totalOpn = 0; 
			$totalDAmt = 0;
			$totalCAmt = 0;
			$totalBalAmt = 0;
			
			foreach($mergeArray as $value){
				$balance = 0;
				$debitAmt = 0;
				$creditAmt = 0;
				$OBAL = 0;
				$AccountID = $value["AccountID"];
				$groupName1 = $value['SubActGroupName1'];
				$groupName = $value['SubActGroupName'];
				if($value["company"] == ''){
					$Name = $value['firstname'].' '.$value['lastname'];
					$Address = $value['current_address'].' '.$value['home_town'].' '.$value['city_name'].' '.$value['state_name'].' '.$value['pincode'];
					$OBAL = $value['BAL1'];
					}else{
					$OBAL = $value['BAL1'];
					$Name = $value['company'];
					$Address = $value['address'].' '.$value['Address3'].' '.$value['city_name'].' '.$value['state_name'].' '.$value['zip'];
				}
				
				foreach($debitledgerdata as $value1){
					if(trim(strtoupper($AccountID)) === trim(strtoupper($value1["AccountID"]))){
						$debitAmt = $value1["debit_bal"];
					}
				}
				foreach($creditledgerdata as $value2){
					if(trim(strtoupper($AccountID)) === trim(strtoupper($value2["AccountID"]))){
						$creditAmt = $value2["credit_bal"];
					}
				}
				$balance = $OBAL + $debitAmt - $creditAmt;
				if((($OBAL) == 0) && (($debitAmt) == 0) && (($creditAmt) == 0) && (($balance) == 0) && ($AccountID !== '')){
					
					}else{
					if($AccountID){
						$totalOpn += $value['BAL1'];
						$totalDAmt += $debitAmt;
						$totalCAmt += $creditAmt;
						$totalBalAmt += $balance;
						
						$list_add = [];
						$list_add[] = $i;
						$list_add[] = $groupName1;
						$list_add[] = $groupName;
						$list_add[] = $AccountID;
						$list_add[] = $Name;
						$list_add[] = $Address;
						$list_add[] = $value["BAL1"];
						$list_add[] = $debitAmt;
						$list_add[] = $creditAmt;
						$list_add[] = number_format($balance,2);
						if($balance <= 0){
							$cr_dr = "CR";
							}else{
							$cr_dr = "DR";
						}
						$list_add[] = $cr_dr;
						$writer->writeSheetRow('Sheet1', $list_add);
						
						$i++;
					}
				}
			}
			
			$list_add = [];
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "";
			$list_add[] = "Total";
			$list_add[] = number_format($totalOpn,2);
			$list_add[] = number_format($totalDAmt,2);
			$list_add[] = number_format($totalCAmt,2);
			$list_add[] = number_format($totalBalAmt,2);
			$list_add[] = "";
			$writer->writeSheetRow('Sheet1', $list_add);
			
			$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
			foreach($files as $file){
				if(is_file($file)) {
					unlink($file); 
				}
			}
			$filename = 'trial_balance_Report.xlsx';
			$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
			echo json_encode([
			'site_url'          => site_url(),
			'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
			]);
			die;
		}
		
		public function load_data_trial_balance_report(){
			if(!has_permission_new('accounting_trial_balance', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$data_post = $this->input->post();
			$data = $this->accounting_model->table_data_for_trial_balance($this->input->post());
			$data2 = $this->accounting_model->table_data_for_trial_balance_staff($this->input->post());
			
			$selected_company = $this->session->userdata('root_company');
			$html ='';
			$i =1;
			$cgst_amt =0;
			$sgst_amt =0;
			$igst_amt =0;
			$total_amt =0;
			
			$totalOpn = 0; 
			$totalDAmt = 0;
			$totalCAmt = 0;
			$totalBalAmt = 0;
			
			foreach($data as $value){
				$debitAmt = $value['DRBAL'];
				$balance = 0;
				/*foreach($debitledgerdata as $value1){
					if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value1["AccountID"]))){
					$debitAmt = $value1["debit_bal"];
					}
				}*/
				$creditAmt = $value['CRBAL'];
				/*foreach($creditledgerdata as $value2){
					if(trim(strtoupper($value["AccountID"])) == trim(strtoupper($value2["AccountID"]))){
					$creditAmt = $value2["credit_bal"];
					}
				}*/
				$balance = $value['BAL1'] + $debitAmt - $creditAmt;
				if((($value['BAL1']) == 0) && (($debitAmt) == 0) && (($creditAmt) == 0) && (($balance) == 0)){
					
					}else{
					
					$totalOpn += $value['BAL1'];
					$totalDAmt += $value['DRBAL'];
					$totalCAmt += $value['CRBAL'];
					$totalBalAmt += $balance;
					
					$html.= '<tr>';
					$html.= '<td align="center">'.$i.'</td>';
					$html.= '<td align="left">'.$value['SubActGroupName'].'</td>';
					$html.= '<td align="left">'.$value['AccountID'].'</td>';
					$len11 = strlen($value['company']);
					if($len11 >42){
						$dots11 = '...';
						}else{
						$dots11 = '';
					}
					$html.= '<td align="left">'.substr($value['company'],0,42).$dots11.'</td>';
					$Address = $value['address'].' '.$value['Address3'].' '.$value['city_name'].' '.$value['state_name'].' '.$value['zip'];
					$len = strlen($Address);
					if($len >20){
						$dots = '...';
						}else{
						$dots = '';
					}
					$html.= '<td align="left">'.substr($Address,0,20).$dots.'</td>';
					$html.= '<td align="right">'.number_format($value['BAL1'],2).'</td>';
					
					$html.= '<td align="right">'.number_format($value['DRBAL'],2).'</td>';
					
					$html.= '<td align="right">'.number_format($value['CRBAL'],2).'</td>';
					
					if($balance <= 0){
						$cr_dr = "CR";
						}else{
						$cr_dr = "DR";
					}
					
					$html.= '<td align="right">'.number_format($balance,2).' '.$cr_dr.'</td>';
					//$html.= '<td align="center">'.$cr_dr.'</td>';
					
					$html.= '</tr>';
					$i++;
				}
			}
			
			foreach($data2 as $value3){
				$debitAmt = $value3['DRBAL'];
				$balance = 0;
				/*foreach($debitledgerdata as $value4){
					if(trim(strtoupper($value3["AccountID"])) == trim(strtoupper($value4["AccountID"]))){
					$debitAmt = $value4["debit_bal"];
					}
				}*/
				$creditAmt = $value3['CRBAL'];
				/*foreach($creditledgerdata as $value5){
					if( trim(strtoupper($value3["AccountID"])) == trim(strtoupper($value5["AccountID"]))){
					$creditAmt = $value5["credit_bal"];
					}
				}*/
				
				$balance = $value3['BAL1'] + $debitAmt - $creditAmt;
				if($balance <= 0){
					$cr_dr = "CR";
					}else{
					$cr_dr = "DR";
				}
				
				if((($value3['BAL1']) == 0) && (($debitAmt) == 0) && (($creditAmt) == 0) && (($balance) == 0)){
					
					}else{
					
					$totalOpn += $value3['BAL1'];
					$totalDAmt += $value3['DRBAL'];
					$totalCAmt += $value3['CRBAL'];
					$totalBalAmt += $balance;
					
					$html.= '<tr>';
					$html.= '<td align="center">'.$i.'</td>';
					$html.= '<td align="left">'.$value3['SubActGroupName'].'</td>';
					$html.= '<td align="left">'.$value3['AccountID'].'</td>';
					$fullname = $value3['firstname'].' '.$value3['lastname'];
					$len22 = strlen($fullname);
					if($len22 >42){
						$dots22 = '...';
						}else{
						$dots22 = '';
					}
					$html.= '<td align="left">'.substr($fullname,0,42).$dots22.'</td>';
					$Address2 = $value3['current_address'].' '.$value3['city_name'].' '.$value3['state_name'].' '.$value3['pincode'];
					$len2 = strlen($Address2);
					if($len2 >20){
						$dots2 = '...';
						}else{
						$dots2 = '';
					}
					$html.= '<td align="left">'.substr($Address2,0,20).$dots2.'</td>';
					$html.= '<td align="right">'.$value3['BAL1'].'</td>';
					$html.= '<td align="right">'.number_format($value3['DRBAL'],2).'</td>';
					$html.= '<td align="right">'.number_format($value3['CRBAL'],2).'</td>';
					$html.= '<td align="right">'.number_format($balance,2).' '.$cr_dr.'</td>';
					//$html.= '<td align="center">'.$cr_dr.'</td>';
					
					$html.= '</tr>';
					$i++;
				}
				
			}
			$html.= '<tr>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td></td>';
			$html.= '<td><b>Total</b></td>';
			$html.= '<td align="right"><b>'.number_format($totalOpn,2).'</b></td>';
			//$html.= '<td></td>';
			$html.= '<td align="right"><b>'.number_format($totalDAmt,2).'</b></td>';
			//$html.= '<td></td>';
			$html.= '<td align="right"><b>'.number_format($totalCAmt,2).'</b></td>';
			if($totalBalAmt <= 0){
				$cr_dr1 = "CR";
				}else{
				$cr_dr1 = "DR";
			}
			$html.= '<td align="right"><b>'.number_format($totalBalAmt,2).' '.$cr_dr1.'</b></td>';
			$html.= '</tr>';
			echo $html;
		}
		
		public function export_trial_balance_report2(){
			ini_set('precision', '15');
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				
				$data = array(
				'as_on' => $this->input->post('as_on')
				);
				$dataNew = $this->accounting_model->TrialBalData($this->input->post());
				$creditledgerdata =$this->accounting_model->creditledger_data($this->input->post());
				/*echo "<pre>";
					print_r($creditledgerdata);
				die;*/
				$debitledgerdata =$this->accounting_model->debitledger_data($this->input->post());
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();
				
				$writer = new XLSXWriter();
				$j=0;
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col =9);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				$j++;
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				$j++;
				
				$msg = "Trial Balance Report: ".$this->input->post('as_on');
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);
				$j++;
				
				
				// empty row
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				
				$set_col_tk = [];
				$set_col_tk["SrNo"] =  'SrNo';
				$set_col_tk["AccountSubGroup"] =  'AccountSubGroup';
				$set_col_tk["AccountID"] =  'AccountID';
				$set_col_tk["AccountName"] =  'AccountName';
				$set_col_tk["Address"] =  'Address';
				$set_col_tk["OpeningBal"] =  'OpeningBal';
				$set_col_tk["DebitAmt"] =  'DebitAmt';
				$set_col_tk["CreditAmt"] =  'CreditAmt';
				$set_col_tk["Balance"] =  'Balance';
				$set_col_tk["Cr/Dr"] =  'Cr/Dr';
				
				
				
				
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				$i = 1;
				
				$totalOpn = 0; 
				$totalDAmt = 0;
				$totalCAmt = 0;
				$totalBalAmt = 0;
				
				foreach ($dataNew as $k => $value) {
					$balance = 0;
					$debitAmt = 0;
					$creditAmt = 0;
					if($value["AccountID"] == ''){
						$AccountID = $value["AccountID2"];
						$groupName = $value['Group'];
						$Name = $value['firstname'].' '.$value['lastname'];
						$Address = $value['current_address'].' '.$value['home_town'].' '.$value['city_name2'].' '.$value['state_name2'].' '.$value['pincode'];
						}else{
						$AccountID = $value["AccountID"];
						$groupName = $value['Group'];
						$Name = $value['company'];
						$Address = $value['address'].' '.$value['Address3'].' '.$value['city_name'].' '.$value['state_name'].' '.$value['zip'];
					}
					foreach($debitledgerdata as $value1){
						if(trim(strtoupper($AccountID)) === trim(strtoupper($value1["AccountID"]))){
							$debitAmt = $value1["debit_bal"];
						}
					}
					//$creditAmt = $value['CRBAL'];
					foreach($creditledgerdata as $value2){
						if(trim(strtoupper($AccountID)) === trim(strtoupper($value2["AccountID"]))){
							$creditAmt = $value2["credit_bal"];
						}
					}
					$balance = $value['BAL1'] + $debitAmt - $creditAmt;
					if((($value['BAL1']) == 0) && (($debitAmt) == 0) && (($creditAmt) == 0) && (($balance) == 0) && ($AccountID !== '')){
						
						}else{
						if($AccountID){
							$totalOpn += $value['BAL1'];
							$totalDAmt += $debitAmt;
							$totalCAmt += $creditAmt;
							$totalBalAmt += $balance;
							$list_add = [];
							$list_add[] = $i;
							$list_add[] = $groupName;
							$list_add[] = $AccountID;
							$list_add[] = $Name;
							
							$list_add[] = $Address;
							$list_add[] = $value["BAL1"];
							$list_add[] = $debitAmt;
							$list_add[] = $creditAmt;
							$list_add[] = number_format($balance,2);
							if($balance <= 0){
								$cr_dr = "CR";
								}else{
								$cr_dr = "DR";
							}
							$list_add[] = $cr_dr;
							$writer->writeSheetRow('Sheet1', $list_add);
							$i++;  
						}
						
					}
				}
				
				
				
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "Total";
				$list_add[] = number_format($totalOpn,2);
				$list_add[] = number_format($totalDAmt,2);
				$list_add[] = number_format($totalCAmt,2);
				$list_add[] = number_format($totalBalAmt,2);
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'trial_balance_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		
		public function export_trial_balance_report(){
			ini_set('precision', '15');
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				
				$data = array(
				'as_on' => $this->input->post('as_on')
				);
				$data = $this->accounting_model->table_data_for_trial_balance($this->input->post());
				$data2 = $this->accounting_model->table_data_for_trial_balance_staff($this->input->post());
				//$creditledgerdata =$this->accounting_model->creditledger_data($this->input->post());
				//$debitledgerdata =$this->accounting_model->debitledger_data($this->input->post());
				$this->load->model('sale_reports_model');
				$selected_company_details    = $this->sale_reports_model->get_company_detail();
				
				$writer = new XLSXWriter();
				$j=0;
				$company_name = array($selected_company_details->company_name);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col =9);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_name);
				$j++;
				$address = $selected_company_details->address;
				$company_addr = array($address,);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells
				$writer->writeSheetRow('Sheet1', $company_addr);
				$j++;
				
				$msg = "Trial Balance Report: ".$this->input->post('as_on');
				$filter = array($msg);
				$writer->markMergedCell('Sheet1', $start_row = $j, $start_col = 0, $end_row = $j, $end_col = 9);  //merge cells
				$writer->writeSheetRow('Sheet1', $filter);
				$j++;
				
				
				// empty row
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				
				
				$set_col_tk = [];
				$set_col_tk["SrNo"] =  'SrNo';
				$set_col_tk["AccountSubGroup"] =  'AccountSubGroup';
				$set_col_tk["AccountID"] =  'AccountID';
				$set_col_tk["AccountName"] =  'AccountName';
				$set_col_tk["Address"] =  'Address';
				$set_col_tk["OpeningBal"] =  'OpeningBal';
				$set_col_tk["DebitAmt"] =  'DebitAmt';
				$set_col_tk["CreditAmt"] =  'CreditAmt';
				$set_col_tk["Balance"] =  'Balance';
				$set_col_tk["Cr/Dr"] =  'Cr/Dr';
				
				/*$sheet1header = array(
					'c1-integer'=>'integer',
					'c2-string'=>'string',
					'c3-string'=>'string',
					'c4-string'=>'string',
					'c5-string'=>'string',
					'c6-custom-2decimal'=>'0.00',
					'c7-custom-2decimal'=>'0.00',
					'c8-custom-2decimal'=>'0.00',
					'c9-custom-2decimal'=>'0.00',
					'c10-string'=>'string',
				);*/
				
				
				$writer_header = $set_col_tk;
				$writer->writeSheetRow('Sheet1', $writer_header);
				
				$i = 1;
				
				$totalOpn = 0; 
				$totalDAmt = 0;
				$totalCAmt = 0;
				$totalBalAmt = 0;
				
				foreach ($data as $k => $value) {
					$debitAmt = $value['DRBAL'];
					$balance = 0.00;
					/*foreach($debitledgerdata as $value1){
						if(strtoupper($value["AccountID"])==strtoupper($value1["AccountID"])){
						$debitAmt = $value1["debit_bal"];
						}
					}*/
					
					$creditAmt = $value['CRBAL'];
					/*foreach($creditledgerdata as $value2){
						if(strtoupper($value["AccountID"])==strtoupper($value2["AccountID"])){
						$creditAmt = $value2["credit_bal"];
						}
					}*/
					
					$balance = (double) $value['BAL1'] + (double) $debitAmt - (double) $creditAmt;
					if($balance <= 0){
						$cr_dr = "CR";
						}else{
						$cr_dr = "DR";
					}
					if((($value['BAL1']) == 0) && (($debitAmt) == 0) && (($creditAmt) == 0) && (($balance) == 0)){
						
						}else{
						if($value['BAL1'] == '' || $value['BAL1'] == null){
							
							}else{
							$totalOpn = $totalOpn + $value['BAL1'];
						}
						
						$totalDAmt += $value['DRBAL'];
						$totalCAmt += $value['CRBAL'];
						$totalBalAmt = $totalBalAmt + $balance;
						$list_add = [];
						$list_add[] = $i;
						$list_add[] = $value["SubActGroupName"];
						$list_add[] = $value["AccountID"];
						$list_add[] = $value["company"];
						$Address = $value['address'].' '.$value['Address3'].' '.$value['city_name'].' '.$value['state_name'].' '.$value['zip'];
						
						$list_add[] = $Address;
						$list_add[] = $value["BAL1"];
						
						
						$list_add[] = $value['DRBAL'];
						$list_add[] = $value['CRBAL'];
						$list_add[] = $balance;
						$list_add[] = $cr_dr;
						$writer->writeSheetRow('Sheet1', $list_add);
						$i++;  
					}
				}
				
				foreach ($data2 as $k => $value3) {
					$debitAmt = $value3['DRBAL'];
					$balance = 0.00;
					/*foreach($debitledgerdata as $value4){
						if(strtoupper($value3["AccountID"])==strtoupper($value4["AccountID"])){
						$debitAmt = $value4["debit_bal"];
						}
					}*/
					
					$creditAmt = $value3['CRBAL'];
					/*foreach($creditledgerdata as $value5){
						if(strtoupper($value3["AccountID"])==strtoupper($value5["AccountID"])){
						$creditAmt = $value5["credit_bal"];
						}
					}*/
					
					$balance = $value3['BAL1'] + $debitAmt - $creditAmt;
					if($balance <= 0){
						$cr_dr = "CR";
						}else{
						$cr_dr = "DR";
					}
					if((($value3['BAL1']) == 0) && (($debitAmt) == 0) && (($creditAmt) == 0) && (($balance) == 0)){
						
						}else{
						
						if($value3['BAL1'] == '' || $value3['BAL1'] == null){
							
							}else{
							$totalOpn = $totalOpn + $value3['BAL1'];
						}
						$totalDAmt += $value3['DRBAL'];
						$totalCAmt += $value3['CRBAL'];
						$totalBalAmt = $totalBalAmt + $balance;
						
						$list_add = [];
						$list_add[] = $i;
						$list_add[] = $value3["SubActGroupName"];
						$list_add[] = $value3["AccountID"];
						$list_add[] = $value3["firstname"]." ".$value3["lastname"];
						$Address2 = $value3['current_address'].' '.$value3['city_name'].' '.$value3['state_name'].' '.$value3['pincode'];
						$list_add[] = $Address2;
						$list_add[] = $value3["BAL1"];
						$list_add[] = $value3['DRBAL'];
						$list_add[] = $value3['CRBAL'];
						$list_add[] = $balance;
						$list_add[] = $cr_dr;
						$writer->writeSheetRow('Sheet1', $list_add);
						$i++;  
					}
				}
				
				$list_add = [];
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "";
				$list_add[] = "Total";
				$list_add[] = $totalOpn;
				$list_add[] = $totalDAmt;
				$list_add[] = $totalCAmt;
				$list_add[] = $totalBalAmt;
				$list_add[] = "";
				$writer->writeSheetRow('Sheet1', $list_add);
				$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
				foreach($files as $file){
					if(is_file($file)) {
						unlink($file); 
					}
				}
				$filename = 'trial_balance_Report.xlsx';
				$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
				echo json_encode([
				'site_url'          => site_url(),
				'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
				]);
				die;
			}
		}
		
		public function SetAccountID()
		{
			$AccountID = $this->input->post('AccountID');
			$AccountID_for_ledger = array(
			'AccountID_for_ledger'  => $AccountID,
			);
			$this->session->set_userdata($AccountID_for_ledger);
			echo json_encode($AccountID);
		}
		
		//=================== Trial balance report with drill down =====================
		public function trial_balance_summary()
		{
			if(!has_permission_new('trial_balance_summary', '', 'view')) {
				access_denied('accounting_tcs_report');
			}
			$fy = $this->session->userdata('finacial_year');
			$last_fy = $fy - 1;
			$data['title'] = "Trial Balance Summary";
			$selected_company = $this->session->userdata('root_company');
			$data['company_detail'] = $this->accounting_model->get_company_detail1($selected_company);
			$finalArray = [];
			$BalanceSheet_head['MainGroup'] = array("10000","10035","10025","10028","10010","10011","10018","10019");
			$ActMainGroup = $this->accounting_model->fetchAccountsData();
			$ActSubGroup1 = $this->accounting_model->GetActSubGroup1ByMainGroup($BalanceSheet_head);
			$ActSubGroup2 = $this->accounting_model->GetActSubGroup2ByMainGroup($BalanceSheet_head);
			$AccountList = $this->accounting_model->GetAccountListByMainGroup($BalanceSheet_head);
			$StaffList = $this->accounting_model->GetStaffList($BalanceSheet_head);
			
			$ledger_data = $this->accounting_model->GetLedgerData($BalanceSheet_head);
			$staffledger_data = $this->accounting_model->GetStaffLedgerData($BalanceSheet_head);
			$opn_data = $this->accounting_model->GetOpnBalData($BalanceSheet_head);
			
			$nestedData = [];
			$i = 0;$TotalOpnDR = 0;$TotalOpnCR = 0;$TotalDR = 0;$TotalCR = 0;$TotalClsDR = 0;$TotalClsCR = 0;
			foreach ($ActMainGroup as $mainGroup) {
				$ClsBalMainGrpWise = 0;
				$ClsBalMainGrpWisePre = 0;
				$crMainGrpAmt = 0;
				$drMainGrpAmt = 0;
				$crMainGrpAmtPre = 0;
				$drMainGrpAmtPre = 0;
				$DrOpnMainGrpAmt = 0;
				$CrOpnMainGrpAmt = 0;
				$DrOpnMainGrpAmtPre = 0;
				$CrOpnMainGrpAmtPre = 0;
				$DrClsBalMainGrpWise = 0;
				$CrClsBalMainGrpWise = 0;
				$DrClsBalMainGrpWisePre = 0;
				$CrClsBalMainGrpWisePre = 0;
				$mainGroupData = [
				'MainGroup' => $mainGroup['ActGroupName'],
				];
				foreach ($ActSubGroup1 as $ActsubGrp1) {
					if($mainGroup["ActGroupID"] == $ActsubGrp1["ActGroupID"]){
						$ClsBalSubGrp1Wise = 0;
						$ClsBalSubGrp1WisePre = 0;
						$crSubGrp1Amt = 0;
						$drSubGrp1Amt = 0;
						$crSubGrp1AmtPre = 0;
						$drSubGrp1AmtPre = 0;
						$DrOpnSubGrp1Amt = 0;
						$CrOpnSubGrp1Amt = 0;
						$DrClsBalSubGrp1Wise = 0;
						$CrClsBalSubGrp1Wise = 0;
						$DrClsBalSubGrp1WisePre = 0;
						$CrClsBalSubGrp1WisePre = 0;
						$subGroupData1 = [
						'SubGroup1Name' => $ActsubGrp1['SubActGroupName'],
						'SubGroup1' => $ActsubGrp1['SubActGroupID1'],
						];
						foreach ($ActSubGroup2 as $ActsubGrp2) {
							if($ActsubGrp1["SubActGroupID1"]==$ActsubGrp2["SubActGroupID1"]){
								$ClsBalSubGrp2Wise = 0;
								$crSubGrp2Amt = 0;
								$drSubGrp2Amt = 0;
								$crSubGrp2AmtPre = 0;
								$drSubGrp2AmtPre = 0;
								$DrOpnSubGrp2Amt = 0;
								$DrOpnSubGrp2AmtPre = 0;
								$CrOpnSubGrp2Amt = 0;
								$CrOpnSubGrp2AmtPre = 0;
								$DrClsBalSubGrp2Wise = 0;
								$CrClsBalSubGrp2Wise = 0;
								$DrClsBalSubGrp2WisePre = 0;
								$CrClsBalSubGrp2WisePre = 0;
								$subGroupData = [
								'SubGroupName' => $ActsubGrp2['SubActGroupName'],
								'SubActGroupID' => $ActsubGrp2['SubActGroupID'],
								];
								
								// From Client table
								foreach($AccountList as $ActList){
									if($ActList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
										$ClsBalAccountWise = 0;
										$ClsBalAccountWisePre = 0;
										$crActAmt = 0;
										$drActAmt = 0;
										$crActAmtPre = 0;
										$drActAmtPre = 0;
										$DrOpnActAmt = 0;
										$DrOpnActAmtPre = 0;
										$CrOpnActAmt = 0;
										$CrOpnActAmtPre = 0;
										$DrClsBalAccountWise = 0;
										$CrClsBalAccountWise = 0;
										$DrClsBalAccountWisePre = 0;
										$CrClsBalAccountWisePre = 0;
										// opening balances for current year
										foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
											if ($Val45["AccountID"] == $ActList["AccountID"] && $Val45["FY"] == $fy) {
												if($Val45["SUMAmt"] > 0){
													$DrOpnActAmt = $Val45["SUMAmt"];
													$CrOpnActAmt = 0;
													$DrOpnSubGrp2Amt += $DrOpnActAmt;
													}else{
													$CrOpnActAmt = $Val45["SUMAmt"];
													$DrOpnActAmt = 0;
													$CrOpnSubGrp2Amt += $CrOpnActAmt;
												}
											}
										}
										
										// opening balances for privious year
										foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
											if ($Val455["AccountID"] == $ActList["AccountID"] && $Val455["FY"] == $last_fy) {
												if($Val455["SUMAmt"] > 0){
													$DrOpnActAmtPre = $Val455["SUMAmt"];
													$CrOpnActAmtPre = 0;
													$DrOpnSubGrp2AmtPre += $DrOpnActAmtPre;
													}else{
													$CrOpnActAmtPre = $Val455["SUMAmt"];
													$DrOpnActAmtPre = 0;
													$CrOpnSubGrp2AmtPre += $CrOpnActAmtPre;
												}
											}
										}
										
										// transaction data for current year
										foreach ($ledger_data->Cur_yr_ledger as $Key44 => $val44) {
											if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
												$crActAmt = $val44["SUMAmt"];
											}
											if ($val44["AccountID"] == $ActList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
												$drActAmt = $val44["SUMAmt"];
											}
										}
										$crSubGrp2Amt += $crActAmt;
										$drSubGrp2Amt += $drActAmt;
										
										// transaction data for privious year
										foreach ($ledger_data->Last_yr_ledger as $Key444 => $val444) {
											if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
												$crActAmtPre = $val444["SUMAmt"];
											}
											if ($val444["AccountID"] == $ActList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
												$drActAmtPre = $val444["SUMAmt"];
											}
										}
										$crSubGrp2AmtPre += $crActAmtPre;
										$drSubGrp2AmtPre += $drActAmtPre;
										
										if($i>1){
											$ClsBalAccountWise = $CrOpnActAmt + $DrOpnActAmt + $drActAmt - $crActAmt;
											}else{
											$ClsBalAccountWise = $CrOpnActAmt + $DrOpnActAmt + $crActAmt - $drActAmt;
										}
										
										if($i>1){
											$ClsBalAccountWisePre = $CrOpnActAmtPre + $DrOpnActAmtPre + $drActAmtPre - $crActAmtPre;
											}else{
											$ClsBalAccountWisePre = $CrOpnActAmtPre + $DrOpnActAmtPre + $crActAmtPre - $drActAmtPre;
										}
										
										if($ClsBalAccountWise < 0){
											$DrClsBalAccountWise = $ClsBalAccountWise;
											$CrClsBalAccountWise = 0;
											$DrClsBalSubGrp2Wise += $DrClsBalAccountWise;
											}else{
											$CrClsBalAccountWise = $ClsBalAccountWise;
											$DrClsBalAccountWise = 0;
											$CrClsBalSubGrp2Wise += $CrClsBalAccountWise;
										}
										
										if($ClsBalAccountWisePre < 0){
											$DrClsBalAccountWisePre = $ClsBalAccountWise;
											$CrClsBalAccountWisePre = 0;
											$DrClsBalSubGrp2WisePre += $DrClsBalAccountWisePre;
											}else{
											$CrClsBalAccountWisePre = $ClsBalAccountWise;
											$DrClsBalAccountWisePre = 0;
											$CrClsBalSubGrp2WisePre += $CrClsBalAccountWisePre;
										}
										
										if($CrOpnActAmt == "0" && $DrOpnActAmt == "0" && $drActAmt == "0" && $crActAmt == "0" && $CrOpnActAmtPre == "0" && $DrOpnActAmtPre == "0" && $drActAmtPre == "0" && $crActAmtPre == "0"){
											
											}else{
											
											$AccountData = [
											'AccountName' => $ActList['company'],
											'AccountID' => $ActList['AccountID'],
											'CROpeningAmt' => abs($CrOpnActAmt),
											'DROpeningAmt' => abs($DrOpnActAmt),
											'CRAmt' => $crActAmt,
											'DRAmt' => $drActAmt,
											'DRClsAmt' => abs($DrClsBalAccountWise),
											'CRClsAmt' => abs($CrClsBalAccountWise),
											'CROpeningAmtPre' => abs($CrOpnActAmtPre),
											'DROpeningAmtPre' => abs($DrOpnActAmtPre),
											'CRAmtPre' => $crActAmtPre,
											'DRAmtPre' => $drActAmtPre,
											'DRClsAmtPre' => abs($DrClsBalAccountWisePre),
											'CRClsAmtPre' => abs($CrClsBalAccountWisePre),
											];
											$subGroupData['Accounts'][] = $AccountData;
										}
									}
								}
								// From Staff table
								foreach($StaffList as $staffList){
									if($staffList["SubActGroupID"]==$ActsubGrp2['SubActGroupID']){
										
										$ClsBalAccountWise = 0;
										$ClsBalAccountWisePre = 0;
										$crActAmt = 0;
										$drActAmt = 0;
										$crActAmtPre = 0;
										$drActAmtPre = 0;
										$DrOpnActAmt = 0;
										$CrOpnActAmt = 0;
										$DrOpnActAmtPre = 0;
										$CrOpnActAmtPre = 0;
										$DrClsBalAccountWise = 0;
										$CrClsBalAccountWise = 0;
										$DrClsBalAccountWisePre = 0;
										$CrClsBalAccountWisePre = 0;
										// opening balances for current year
										foreach ($opn_data->Cur_yr_OpnBal as $Key45 => $Val45) {
											if ($Val45["AccountID"] == $staffList["AccountID"] && $Val45["FY"] == $fy) {
												if($Val45["SUMAmt"] > 0){
													$DrOpnActAmt = $Val45["SUMAmt"];
													$CrOpnActAmt = 0;
													$DrOpnSubGrp2Amt += $DrOpnActAmt;
													}else{
													$CrOpnActAmt = $Val45["SUMAmt"];
													$DrOpnActAmt = 0;
													$CrOpnSubGrp2Amt += $CrOpnActAmt;
												}
											}
										}
										
										// opening balances for privious year
										foreach ($opn_data->Last_yr_OpnBal as $Key455 => $Val455) {
											if ($Val455["AccountID"] == $staffList["AccountID"] && $Val455["FY"] == $last_fy) {
												if($Val455["SUMAmt"] > 0){
													$DrOpnActAmtPre = $Val455["SUMAmt"];
													$CrOpnActAmtPre = 0;
													$DrOpnSubGrp2AmtPre += $DrOpnActAmtPre;
													}else{
													$CrOpnActAmtPre = $Val455["SUMAmt"];
													$DrOpnActAmtPre = 0;
													$CrOpnSubGrp2AmtPre += $CrOpnActAmtPre;
												}
											}
										}
										// transaction data for current year
										foreach ($staffledger_data->Cur_yr_ledger as $Key44 => $val44) {
											if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "C" && $val44["FY"] == $fy) {
												$crActAmt = $val44["SUMAmt"];
											}
											if ($val44["AccountID"] == $staffList["AccountID"] && $val44["TType"] == "D" && $val44["FY"] == $fy) {
												$drActAmt = $val44["SUMAmt"];
											}
										}
										$crSubGrp2Amt += $crActAmt;
										$drSubGrp2Amt += $drActAmt;
										
										// transaction data for privious year
										foreach ($staffledger_data->Last_yr_ledger as $Key444 => $val444) {
											if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "C" && $val444["FY"] == $last_fy) {
												$crActAmtPre = $val444["SUMAmt"];
											}
											if ($val444["AccountID"] == $staffList["AccountID"] && $val444["TType"] == "D" && $val444["FY"] == $last_fy) {
												$drActAmtPre = $val444["SUMAmt"];
											}
										}
										$crSubGrp2AmtPre += $crActAmtPre;
										$drSubGrp2AmtPre += $drActAmtPre;
										
										if($i>1){
											$ClsBalAccountWise = $CrOpnActAmt + $DrOpnActAmt + $drActAmt - $crActAmt;
											}else{
											$ClsBalAccountWise = $CrOpnActAmt + $DrOpnActAmt + $crActAmt - $drActAmt;
										}
										
										if($i>1){
											$ClsBalAccountWisePre = $CrOpnActAmtPre + $DrOpnActAmtPre + $drActAmtPre - $crActAmtPre;
											}else{
											$ClsBalAccountWisePre = $CrOpnActAmtPre + $DrOpnActAmtPre + $crActAmtPre - $drActAmtPre;
										}
										
										if($ClsBalAccountWise < 0){
											$DrClsBalAccountWise = $ClsBalAccountWise;
											$CrClsBalAccountWise = 0;
											$DrClsBalSubGrp2Wise += $DrClsBalAccountWise;
											}else{
											$CrClsBalAccountWise = $ClsBalAccountWise;
											$DrClsBalAccountWise = 0;
											$CrClsBalSubGrp2Wise += $CrClsBalAccountWise;
										}
										
										if($ClsBalAccountWisePre < 0){
											$DrClsBalAccountWisePre = $ClsBalAccountWisePre;
											$CrClsBalAccountWisePre = 0;
											$DrClsBalSubGrp2WisePre += $DrClsBalAccountWisePre;
											}else{
											$CrClsBalAccountWisePre = $ClsBalAccountWisePre;
											$DrClsBalAccountWisePre = 0;
											$CrClsBalSubGrp2WisePre += $CrClsBalAccountWisePre;
										}
										
										if($CrOpnActAmt == "0" && $DrOpnActAmt == "0" && $drActAmt == "0" && $crActAmt == "0" && $CrOpnActAmtPre == "0" && $DrOpnActAmtPre == "0" && $drActAmtPre == "0" && $crActAmtPre == "0"){
											
											}else{
											
											$AccountData = [
											'AccountName' => $staffList['firstname'].' '.$staffList['lastname'],
											'AccountID' => $staffList['AccountID'],
											'CROpeningAmt' => $CrOpnActAmt,
											'DROpeningAmt' => $DrOpnActAmt,
											'CRAmt' => $crActAmt,
											'DRAmt' => $drActAmt,
											'DRClsAmt' => abs($DrClsBalAccountWise),
											'CRClsAmt' => abs($CrClsBalAccountWise),
											'CROpeningAmtPre' => $CrOpnActAmtPre,
											'DROpeningAmtPre' => $DrOpnActAmtPre,
											'CRAmtPre' => $crActAmtPre,
											'DRAmtPre' => $drActAmtPre,
											'DRClsAmtPre' => abs($DrClsBalAccountWisePre),
											'CRClsAmtPre' => abs($CrClsBalAccountWisePre),
											];
											$subGroupData['Accounts'][] = $AccountData;
										}
									}
								}
								$subGroupData['CROpeningAmt'] = abs($CrOpnSubGrp2Amt);
								$subGroupData['DROpeningAmt'] = abs($DrOpnSubGrp2Amt);
								$subGroupData['CRAmt'] = $crSubGrp2Amt;
								$subGroupData['DRAmt'] = $drSubGrp2Amt;
								$subGroupData['DRClsAmt'] = abs($DrClsBalSubGrp2Wise);
								$subGroupData['CRClsAmt'] = abs($CrClsBalSubGrp2Wise);
								
								$subGroupData['CROpeningAmtPre'] = abs($CrOpnSubGrp2AmtPre);
								$subGroupData['DROpeningAmtPre'] = abs($DrOpnSubGrp2AmtPre);
								$subGroupData['CRAmtPre'] = $crSubGrp2AmtPre;
								$subGroupData['DRAmtPre'] = $drSubGrp2AmtPre;
								$subGroupData['DRClsAmtPre'] = abs($DrClsBalSubGrp2WisePre);
								$subGroupData['CRClsAmtPre'] = abs($CrClsBalSubGrp2WisePre);
								
								if($CrOpnSubGrp2Amt == "0" && $DrOpnSubGrp2Amt == "0" && $crSubGrp2Amt == "0" && $drSubGrp2Amt == "0" && $CrOpnSubGrp2AmtPre == "0" && $DrOpnSubGrp2AmtPre == "0" && $crSubGrp2AmtPre == "0" && $drSubGrp2AmtPre == "0"){
									
									}else{
									$CrOpnSubGrp1Amt += $CrOpnSubGrp2Amt;
									$DrOpnSubGrp1Amt += $DrOpnSubGrp2Amt;
									$crSubGrp1Amt += $crSubGrp2Amt;
									$drSubGrp1Amt += $drSubGrp2Amt;
									$DrClsBalSubGrp1Wise += $DrClsBalSubGrp2Wise;
									$CrClsBalSubGrp1Wise += $CrClsBalSubGrp2Wise;
									
									$CrOpnSubGrp1AmtPre += $CrOpnSubGrp2AmtPre;
									$DrOpnSubGrp1AmtPre += $DrOpnSubGrp2AmtPre;
									$crSubGrp1AmtPre += $crSubGrp2AmtPre;
									$drSubGrp1AmtPre += $drSubGrp2AmtPre;
									$DrClsBalSubGrp1WisePre += $DrClsBalSubGrp2WisePre;
									$CrClsBalSubGrp1WisePre += $CrClsBalSubGrp2WisePre;
									$subGroupData1['SubGroups'][] = $subGroupData;
								}
							}
						}
						$subGroupData1['CROpeningAmt'] = abs($CrOpnSubGrp1Amt);
						$subGroupData1['DROpeningAmt'] = abs($DrOpnSubGrp1Amt);
						$subGroupData1['CRAmt'] = $crSubGrp1Amt;
						$subGroupData1['DRAmt'] = $drSubGrp1Amt;
						$subGroupData1['DRClsAmt'] = abs($DrClsBalSubGrp1Wise);
						$subGroupData1['CRClsAmt'] = abs($CrClsBalSubGrp1Wise);
						
						$subGroupData1['CROpeningAmtPre'] = abs($CrOpnSubGrp1AmtPre);
						$subGroupData1['DROpeningAmtPre'] = abs($DrOpnSubGrp1AmtPre);
						$subGroupData1['CRAmtPre'] = $crSubGrp1AmtPre;
						$subGroupData1['DRAmtPre'] = $drSubGrp1AmtPre;
						$subGroupData1['DRClsAmtPre'] = abs($DrClsBalSubGrp1WisePre);
						$subGroupData1['CRClsAmtPre'] = abs($CrClsBalSubGrp1WisePre);
						if($CrOpnSubGrp1Amt == "0" && $DrOpnSubGrp1Amt == "0" && $crSubGrp1Amt == "0" && $drSubGrp1Amt == "0" && $CrOpnSubGrp1AmtPre == "0" && $DrOpnSubGrp1AmtPre == "0" && $crSubGrp1AmtPre == "0" && $drSubGrp1AmtPre == "0"){
							
							}else{
							$CrOpnMainGrpAmt += $CrOpnSubGrp1Amt;
							$DrOpnMainGrpAmt += $DrOpnSubGrp1Amt;
							$crMainGrpAmt += $crSubGrp1Amt;
							$drMainGrpAmt += $drSubGrp1Amt;
							$DrClsBalMainGrpWise += $DrClsBalSubGrp1Wise;
							$CrClsBalMainGrpWise += $CrClsBalSubGrp1Wise;
							
							$CrOpnMainGrpAmtPre += $CrOpnSubGrp1AmtPre;
							$DrOpnMainGrpAmtPre += $DrOpnSubGrp1AmtPre;
							$crMainGrpAmtPre += $crSubGrp1AmtPre;
							$drMainGrpAmtPre += $drSubGrp1AmtPre;
							$DrClsBalMainGrpWisePre += $DrClsBalSubGrp1WisePre;
							$CrClsBalMainGrpWisePre += $CrClsBalSubGrp1WisePre;
							
							$mainGroupData['SubGroups1'][] = $subGroupData1;
						}
					}
				}
				if($CrOpnMainGrpAmt == "0" && $DrOpnMainGrpAmt == "0" && $crMainGrpAmt == "0" && $drMainGrpAmt == "0" && $CrOpnMainGrpAmtPre == "0" && $DrOpnMainGrpAmtPre == "0" && $crMainGrpAmtPre == "0" && $drMainGrpAmtPre == "0"){
					
					}else{
					$TotalOpnDR += abs($DrOpnMainGrpAmt);
					$TotalOpnCR += abs($CrOpnMainGrpAmt);
					$TotalDR += abs($drMainGrpAmt);
					$TotalCR += abs($crMainGrpAmt);
					$TotalClsDR += abs($DrClsBalMainGrpWise);
					$TotalClsCR += abs($CrClsBalMainGrpWise);
					
					$mainGroupData['CROpeningAmt'] = abs($CrOpnMainGrpAmt);
					$mainGroupData['DROpeningAmt'] = abs($DrOpnMainGrpAmt);
					$mainGroupData['CRAmt'] = $crMainGrpAmt;
					$mainGroupData['DRAmt'] = $drMainGrpAmt;
					$mainGroupData['DRClsAmt'] = abs($DrClsBalMainGrpWise);
					$mainGroupData['CRClsAmt'] = abs($CrClsBalMainGrpWise);
					
					$mainGroupData['CROpeningAmtPre'] = abs($CrOpnMainGrpAmtPre);
					$mainGroupData['DROpeningAmtPre'] = abs($DrOpnMainGrpAmtPre);
					$mainGroupData['CRAmtPre'] = $crMainGrpAmtPre;
					$mainGroupData['DRAmtPre'] = $drMainGrpAmtPre;
					$mainGroupData['DRClsAmtPre'] = abs($DrClsBalMainGrpWisePre);
					$mainGroupData['CRClsAmtPre'] = abs($CrClsBalMainGrpWisePre);
					
					$nestedData[] = $mainGroupData;
					$i++;
				}
			}
			$Total["MainGroup"] = "Total";
			$Total["SubGroups1"] = [];
			$Total["CROpeningAmt"] = $TotalOpnCR;
			$Total["DROpeningAmt"] = $TotalOpnDR;
			$Total["CRAmt"] = $TotalCR;
			$Total["DRAmt"] = $TotalDR;
			$Total["CRClsAmt"] = $TotalClsCR;
			$Total["DRClsAmt"] = $TotalClsDR;
			$nestedData[] = $Total;
			$data['nestedData'] = $nestedData;
			
			/*echo "<pre>";
				print_r($nestedData);
			die;*/
			$data['ledger_data'] = $ledger_data; 
			$data['staffledger_data'] = $staffledger_data; 
			$data['OpnBal'] = $opn_data; 
			
			$this->load->view('trial_balance/trial_balance_summary', $data);
		}
		
		public function rp_general_ledger_shipto(){
			if (!has_permission_new('rp_general_ledger_shipto', '', 'view')) {
				access_denied('accounting');
			}
			$this->load->model('currencies_model');
			$data['title'] = _l('general_ledger');
			$fy = $this->session->userdata('finacial_year');
			$fy1 = $fy."-04-01";
			$fy_new  = $fy + 1;
			$lastdate_date = '20'.$fy_new.'-03-31';
			$curr_date = date('Y-m-d');
			$curr_date_new    = new DateTime($curr_date);
			$last_date_yr = new DateTime($lastdate_date);
			if($last_date_yr < $curr_date_new){
				$date = $lastdate_date;
				}else{
				$date = date('Y-m-d');
			}
			$data['from_date'] = $fy1;
			$data['to_date'] = $date;
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounts_list'] = $this->accounting_model->get_accounts_for_ledger();
			$data['accounts_list_staff'] = $this->accounting_model->get_staff_for_ledger();
			$data['selected_company_details']    = $this->order_model->get_selected_company_details();
			$this->load->view('report/includes/general_ledger_shipto', $data);
		}
		
		public function view_report2_shipto(){
			$data_filter = $this->input->post();
			
			$account_name = $this->accounting_model->get_name_account($data_filter);
			if($account_name->company){
				$name = $account_name->company;
				$actDetail = $name." (".$account_name->AccountID.")". " - ".$account_name->StationName;
				}else{
				$name = $account_name->firstname." ". $account_name->lastname;
				$actDetail = $name." (".$account_name->AccountID.")";
			}
			$SubActGroupID = $account_name->SubActGroupID;
			if (!has_permission_new('accounting_ledger_entry_SC', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "50003002"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else if (!has_permission_new('accounting_ledger_entry_SD', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "60001004"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else if (!has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID != "60001004" && $SubActGroupID != "50003002"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else{
				$data_report = $this->accounting_model->get_data_general_ledger2_shipto($data_filter);
				// print_r($data_report);die;
				$SaleIds = $this->accounting_model->GetSaleIds_shipto($data_filter);
				
				$total_bal = $this->accounting_model->get_data_for_account_bal($data_filter);
				$data = array();
				$data["account_name"] = $actDetail;
				
				$new_acc_bal = $total_bal->BAL1;
				$opening_bal = $total_bal->BAL1;
				$i = 1;
				$CRSum = 0;
				$DRSum = 0;
				$finacial_year = $this->session->userdata('finacial_year');
				$from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
				$from_date = date('Y-m-d',strtotime($from_date));
				$to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
				$to_date = date('Y-m-d',strtotime($to_date));
				if($from_date > date('20'.$finacial_year.'-04-01')){
					$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_cr_sum($data_filter);
					$CRSum = $getuptofromdatebal[0]['Amount'];
					$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_dr_sum($data_filter);
					$DRSum = $getuptofromdatebal[0]['Amount'];
					$opening_bal = $total_bal->BAL1 + $DRSum - $CRSum;
					$new_acc_bal = $total_bal->BAL1 + $DRSum - $CRSum;
				}
				$total_debit = 0;
				$total_credit = 0;
				$html = '';
				
				if(empty($data_report)){
					$OCR = 0.00;
					$ODR = 0.00;
					if($new_acc_bal <=0){
						$OCR = abs($new_acc_bal);
						$OB = $OCR.'Cr';
						}else{
						$ODR = abs($new_acc_bal);
						$OB = $ODR.'Dr';
					}
					$html .= '<tr style="color:red;">';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>'. _d($from_date).'</td>';
					$html .= '<td>Opening Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					
					$html .= '<tr style="color:red;">';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>'. _d($to_date).'</td>';
					$html .= '<td>Closing Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					
					}else{
					$OCR = 0.00;
					$ODR = 0.00;
					if($new_acc_bal <=0){
						$OCR = abs($new_acc_bal);
						$OB = $OCR.'Cr';
						}else{
						$ODR = abs($new_acc_bal);
						$OB = $ODR.'Dr';
					}
					$html .= '<tr style="color:red;">';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>'. _d($from_date).'</td>';
					$html .= '<td>Opening Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					$total_credit = $total_credit + $OCR;
					$total_debit = $total_debit + $ODR;
					foreach ($data_report as $key => $value) {
						if($value["Amount"] !== "0.00"){
							
							$url = '';
							if($value["PassedFrom"] == "SALE"){
								foreach($SaleIds as $key1 => $value1){
									if($value1["SalesID"] == $value["VoucherID"]){
										$ChallanID = $value1["ChallanID"];
										$OrderID = $value1["OrderID"];
									}
								}
								// $url = admin_url().'challan/edit_challan/'.$ChallanID;
								$url = admin_url().'order/order/'.$OrderID;
								}else if($value["PassedFrom"] == "SALESRTN"){
								$url = admin_url().'sale_return/edit/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PURCHASE"){
								$url = admin_url().'purchase/EditPurchaseEntry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PURCHASERTN"){
								$url = admin_url().'purchase/purchaseRtn_list/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "CDNOTE"){
								$url = admin_url().'cd_notes/edit/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "JOURNAL"){
								$url = admin_url().'accounting/new_journal_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PAYMENTS"){
								$url = admin_url().'accounting/new_payment_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "RECEIPTS"){
								$url = admin_url().'accounting/new_receipt_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "CONTRA"){
								$url = admin_url().'accounting/new_contra_entry/'.$value["VoucherID"];
								}else{
								$url = "#";
							}
							$url2 = "_blank";
							$html .= '<tr onclick="window.open('."'".$url."'".', '."'".$url2."'".')" >';    
							$html .= '<td>'. $value["PassedFrom"].'</td>';
							$html .= '<td>'. $value["VoucherID"].'</td>';
							$html .= '<td>'. _d(substr($value["Transdate"],0,10)).'</td>';
							$len = strlen($value["Narration"]);
							if($len >67){
								$str = "...";
								}else{
								$str = "";
							}
							$html .= '<td title="'.$value["Narration"].'">'. substr($value["Narration"],0,70).''.$str.'</td>';
							$dvalue = "";
							if($value["TType"]=="D"){
								
								$new_acc_bal = $new_acc_bal + $value["Amount"];
								$dvalue = $value["Amount"];
								$total_debit = $total_debit + $dvalue;
								$dvalue = number_format($dvalue,2);
							}
							$html .= '<td align="right">'. $dvalue .'</td>';
							$cvalue = "";
							if($value["TType"]=="C"){
								$new_acc_bal = $new_acc_bal - $value["Amount"];
								$cvalue = $value["Amount"];
								$total_credit = $total_credit + $cvalue;
								$cvalue = number_format($cvalue,2);
							}
							$html .= '<td align="right">'.$cvalue.'</td>';
							$new_acc_bal2 = $new_acc_bal;
							if($new_acc_bal>0){
								$nab_dr_cr = "Dr";
								}else{
								$nab_dr_cr = "Cr";
							}
							$new_acc_bal2 = round($new_acc_bal2,2)." ".$nab_dr_cr;
							$html .= '<td align="right">'.number_format($new_acc_bal,2)." ".$nab_dr_cr.'</td>';
							$html .= '</tr>';
							$i++;
						}
					}
					if($data_report){
						$html .= '<tr style="color:red;">';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td>Closing Balance</td>';
						$html .= '<td align="right">'. number_format($total_debit,2).'</td>';
						$html .= '<td align="right">'. number_format($total_credit,2).'</td>';
						$html .= '<td align="right">'. number_format($new_acc_bal2,2)." ".$nab_dr_cr.'</td>';
						$html .= '</tr>';
					}
				}
				$data["table"] = $html;
				echo json_encode($data);
			}
			
		}
		
		public function export_general_ledger_shipto(){
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				$data_filter = $this->input->post();
				
				$account_name = $this->accounting_model->get_name_account($data_filter);
				if($account_name->company){
					$name = $account_name->company;
					$account_full_name = $name." (".$account_name->AccountID.")". " - ".$account_name->StationName;
					}else{
					$name = $account_name->firstname." ". $account_name->lastname;
					$account_full_name = $name." (".$account_name->AccountID.")";
				}
				$SubActGroupID = $account_name->SubActGroupID;
				if (!has_permission_new('accounting_ledger_entry_SC', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "50003002"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry_SD', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "60001004"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID != "60001004" && $SubActGroupID != "50003002"){
					echo json_encode('denied');
					die;
					}else{
					$data_report = $this->accounting_model->get_data_general_ledger2_shipto($data_filter);
					$total_bal = $this->accounting_model->get_data_for_account_bal($data_filter);
					
					$new_acc_bal = $total_bal->BAL1;
					$opening_bal = $total_bal->BAL1;
					$i = 1;
					$from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
					$from_date = date('Y-m-d',strtotime($from_date));
					$to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
					$to_date = date('Y-m-d',strtotime($to_date));
					
					$finacial_year = $this->session->userdata('finacial_year');
					if($from_date > date('20'.$finacial_year.'-04-01')){
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_cr_sum($data_filter);
						$CRSum = $getuptofromdatebal[0]['Amount'];
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_dr_sum($data_filter);
						$DRSum = $getuptofromdatebal[0]['Amount'];
						$opening_bal = $total_bal->BAL1 + $DRSum - $CRSum;
						$new_acc_bal = $total_bal->BAL1 + $DRSum - $CRSum;
					}
					
					$this->load->model('sale_reports_model');
					$selected_company_details    = $this->sale_reports_model->get_company_detail();
					
					$writer = new XLSXWriter();
					
					$company_name = array($selected_company_details->company_name);
					$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_name);
					
					$address = $selected_company_details->address;
					$company_addr = array($address,);
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_addr);
					
					$msg = "Account Ledger Report ".$this->input->post('from_date')." To " .$this->input->post('to_date')." Account: ".$account_full_name;
					$filter = array($msg);
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $filter);
					
					// empty row
					$list_add = [];
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$writer->writeSheetRow('Sheet1', $list_add);
					
					$set_col_tk = [];
					$set_col_tk["Passed_From"] =  'Passed From';
					$set_col_tk["Voucher_ID"] =  'Voucher ID';
					$set_col_tk["Date"] =  'Date';
					$set_col_tk["Narration"] =  'Narration';
					$set_col_tk["Debit"] =  'Debit';
					$set_col_tk["Credit"] =  'Credit';
					$set_col_tk["Balance"] =  'Balance';
					$set_col_tk["CR/DR"] =  'CR/DR';
					
					$writer_header = $set_col_tk;
					$writer->writeSheetRow('Sheet1', $writer_header);
					
					$total_debit = 0;
					$total_credit = 0;
					foreach ($data_report as $k => $value) {
						$led_from_date = date('Y-m-d',strtotime($value["Transdate"]));
						$led_to_date = date('Y-m-d',strtotime($value["Transdate"]));
						if($led_from_date >= $from_date && $led_from_date <= $to_date){
							if($i==1){
								if($opening_bal>0){
									$ob_dr_cr = "Dr";
									}else{
									$ob_dr_cr = "Cr";
								}
								
								$list_add = [];
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = _d($from_date);
								$list_add[] = "Opening Balance";
								$new_bal = '';
								if($opening_bal>0){
									$new_bal = abs($opening_bal);
									$total_debit = $total_debit + $new_bal;
									$new_bal = abs($new_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								$new_bal = '';
								if($opening_bal<=0){
									$total_credit = $total_credit + abs($opening_bal);
									$new_bal = abs($opening_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								
								$list_add[] = $opening_bal_new;
								$list_add[] = $ob_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);
							}
							if($value["Amount"] !== "0.00"){
								$list_add = [];
								$list_add[] = $value["PassedFrom"];
								$list_add[] = $value["VoucherID"];
								$list_add[] = _d(substr($value["Transdate"],0,10));
								$list_add[] = $value["Narration"];
								
								$dvalue = "";
								if($value["TType"]=="D"){
									
									$new_acc_bal = $new_acc_bal + $value["Amount"];
									$dvalue = $value["Amount"];
									$total_debit = $total_debit + $dvalue;
									$dvalue = $dvalue;
								}
								$list_add[] = $dvalue;
								$cvalue = "";
								if($value["TType"]=="C"){
									$new_acc_bal = $new_acc_bal - $value["Amount"];
									$cvalue = $value["Amount"];
									$total_credit = $total_credit + $cvalue;
									$cvalue = $cvalue;
								}
								$list_add[] = $cvalue;
								$new_acc_bal2 = abs($new_acc_bal);
								if($new_acc_bal>0){
									$nab_dr_cr = "Dr";
									}else{
									$nab_dr_cr = "Cr";
								}
								$new_acc_bal2 = round($new_acc_bal2,2);
								$list_add[] = $new_acc_bal2;
								$list_add[] = $nab_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);   
								$i++;
							}    
							
							}else{
							if($value["TType"]=="D"){
								$new_acc_bal = $new_acc_bal + $value["Amount"];
							}
							if($value["TType"]=="C"){
								$new_acc_bal = $new_acc_bal - $value["Amount"];
							}
							$opening_bal = $new_acc_bal;
						}
					}
					
					if($data_report){ 
						if($i>1)
						{
							$list_add = [];
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "Closing Balance";
							$list_add[] = $total_debit;
							$list_add[] = $total_credit;
							$list_add[] = $new_acc_bal2;
							$list_add[] = $nab_dr_cr;
							$writer->writeSheetRow('Sheet1', $list_add);
							}else{
							
						}
					}
					
					
					$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
					foreach($files as $file){
						if(is_file($file)) {
							unlink($file); 
						}
					}
					$filename = 'Ship_To_Account_ledger_Report.xlsx';
					$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
					echo json_encode([
					'site_url'          => site_url(),
					'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
					]);
					die;
				}
				
			}
		}
		
		public function saleIds()
		{
			$AccountID = $this->input->post('AccountID');
			$AccountIDs = $this->accounting_model->fetchSaleIds($AccountID);
			echo json_encode([
			'AccountID' => $AccountIDs
			]);
		}
		public function purchaseIds()
		{
			$AccountID = $this->input->post('AccountID');
			$AccountIDs = $this->accounting_model->fetchPurchaseIds($AccountID);
			echo json_encode([
			'AccountID' => $AccountIDs
			]);
		}
		
		public function FetchAllIds()
		{
			$AccountID = $this->input->post('AccountID');
			$AccountIDsPurch = $this->accounting_model->fetchPurchaseIds($AccountID);
			$AccountIDsSale = $this->accounting_model->fetchSaleIds($AccountID);
			
			echo json_encode([
            'AccountID' => $AccountIDsPurch,
            'AccountIDSale' => $AccountIDsSale
			]);
		}
		
		public function fetchBillDetails()
		{
			$AccountID = $this->input->post('AccountID');
			$BillID = $this->input->post('BillID');
			$info = $this->accounting_model->fetchBillinfo($AccountID,$BillID);  
			
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->where('tblReconsileMaster.TransID', $BillID);
			$this->db->where('tblReconsileMaster.TType', 'DR');  
			$this->db->where('tblReconsileMaster.AccountID', $AccountID);          
			$DebitEntry = $this->db->get()->row(); 
			
			$this->db->from(db_prefix() . 'accountledger');
			$this->db->where('BillNo', $BillID);
			$this->db->where('TType', 'D');  
			$this->db->where('AccountID', $AccountID);  
			$details = $this->db->get()->result_array();       
			$DebitAmount = array_sum(array_column($details, 'Amount'));
			// echo "<pre>";print_r($details);die;
			$this->db->from(db_prefix() . 'accountledger');
			$this->db->where('BillNo', $BillID);
			$this->db->where('TType', 'C');  
			$this->db->where('AccountID', $AccountID);  
			$details = $this->db->get()->result_array();       
			$CreditAmount = array_sum(array_column($details, 'Amount'));
			
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->where('TransID', $BillID);
			$this->db->where('TType', 'DR');  
			$this->db->where('AccountID', $AccountID);  
			$debit = $this->db->get()->row();       
			$Debitedamt = $debit->Amount;
			
			$diff = $CreditAmount - $DebitAmount;
			$total_pending_amt = $Debitedamt - $diff;
			
			echo json_encode([
            'info' => $info,
            'DebitEntry'=>$DebitEntry,
            'total_pending_amt'=>$total_pending_amt,
			]); 
		}
		public function fetchPurchBillDetails()
		{
			$AccountID = $this->input->post('AccountID');
			$BillID = $this->input->post('BillID');
			$info = $this->accounting_model->fetchPurchBillinfo($AccountID,$BillID);  
			
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->where('tblReconsileMaster.TransID', $BillID);
			$this->db->where('tblReconsileMaster.TType', 'CR');  
			$this->db->where('tblReconsileMaster.AccountID', $AccountID);          
			$CreditEntry = $this->db->get()->row(); 
			
			$this->db->from(db_prefix() . 'accountledger');
			$this->db->where('BillNo', $BillID);
			$this->db->where('TType', 'D');  
			$this->db->where('AccountID', $AccountID);  
			$details = $this->db->get()->result_array();       
			$DebitAmount = array_sum(array_column($details, 'Amount'));
			// echo "<pre>";print_r($details);die;
			$this->db->from(db_prefix() . 'accountledger');
			$this->db->where('BillNo', $BillID);
			$this->db->where('TType', 'C');  
			$this->db->where('AccountID', $AccountID);  
			$details = $this->db->get()->result_array();       
			$CreditAmount = array_sum(array_column($details, 'Amount'));
			
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->where('TransID', $BillID);
			$this->db->where('TType', 'CR');  
			$this->db->where('AccountID', $AccountID);  
			$debit = $this->db->get()->row();       
			$creditedamt = $debit->Amount;
			
			$diff = $DebitAmount - $CreditAmount;
			$total_pending_amt = $creditedamt - $diff;
			
			echo json_encode([
            'info' => $info,
            'CreditEntry'=>$CreditEntry,
            'total_pending_amt'=>$total_pending_amt,
			]); 
		}
		
		public function rp_general_ledger_new()
		{
			$this->load->model('currencies_model');
			$data['title'] = _l('general_ledger');
			$fy = $this->session->userdata('finacial_year');
			$fy1 = $fy."-04-01";
			$fy_new  = $fy + 1;
			$lastdate_date = '20'.$fy_new.'-03-31';
			$curr_date = date('Y-m-d');
			$curr_date_new    = new DateTime($curr_date);
			$last_date_yr = new DateTime($lastdate_date);
			if($last_date_yr < $curr_date_new){
				$date = $lastdate_date;
				}else{
				$date = date('Y-m-d');
			}
			$data['from_date'] = $fy1;
			$data['to_date'] = $date;
			$data['accounting_method'] = get_option('acc_accounting_method');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['accounts_list'] = $this->accounting_model->get_accounts_for_ledger();
			$data['accounts_list_staff'] = $this->accounting_model->get_staff_for_ledger();
			$data['selected_company_details']    = $this->order_model->get_selected_company_details();
			$this->load->view('report/includes/general_ledger_new', $data);
		}
		
		public function view_report2_new(){
			$data_filter = $this->input->post();
			
			$account_name = $this->accounting_model->get_name_account($data_filter);
			if($account_name->company){
				$name = $account_name->company;
				$actDetail = $name." (".$account_name->AccountID.")". " - ".$account_name->StationName;
				}else{
				$name = $account_name->firstname." ". $account_name->lastname;
				$actDetail = $name." (".$account_name->AccountID.")";
			}
			$SubActGroupID = $account_name->SubActGroupID;
			if (!has_permission_new('accounting_ledger_entry_SC', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "50003002"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else if (!has_permission_new('accounting_ledger_entry_SD', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "60001004"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else if (!has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID != "60001004" && $SubActGroupID != "50003002"){
				$html = '';
				$html .= '<tr style="color:red;">';
				$html .= '<td colspan="7">Your Access denied for this account</td>';
				$html .= '</tr>';
				$data["table"] = $html;
				echo json_encode($data);
				}else{
				$data_report = $this->accounting_model->get_data_general_ledger2_new($data_filter);
				/*echo "<pre>";
					print_r($data_report);
				die;*/
				$SaleIds = $this->accounting_model->GetSaleIds($data_filter);
				
				$total_bal = $this->accounting_model->get_data_for_account_bal($data_filter);
				$data = array();
				$data["account_name"] = $actDetail;
				
				$new_acc_bal = $total_bal->BAL1;
				$opening_bal = $total_bal->BAL1;
				$i = 1;
				$CRSum = 0;
				$DRSum = 0;
				$finacial_year = $this->session->userdata('finacial_year');
				$from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
				$from_date = date('Y-m-d',strtotime($from_date));
				$to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
				$to_date = date('Y-m-d',strtotime($to_date));
				if($from_date > date('20'.$finacial_year.'-04-01')){
					$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_cr_sum($data_filter);
					$CRSum = $getuptofromdatebal[0]['Amount'];
					$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_dr_sum($data_filter);
					$DRSum = $getuptofromdatebal[0]['Amount'];
					$opening_bal = $total_bal->BAL1 + $DRSum - $CRSum;
					$new_acc_bal = $total_bal->BAL1 + $DRSum - $CRSum;
				}
				$total_debit = 0;
				$total_credit = 0;
				$html = '';
				
				if(empty($data_report)){
					$OCR = 0.00;
					$ODR = 0.00;
					if($new_acc_bal <=0){
						$OCR = abs($new_acc_bal);
						$OB = $OCR.'Cr';
						}else{
						$ODR = abs($new_acc_bal);
						$OB = $ODR.'Dr';
					}
					$html .= '<tr style="color:red;">';
					$html .= '<td>'. _d($from_date).'</td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>Opening Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					
					$html .= '<tr style="color:red;">';
					$html .= '<td>'. _d($to_date).'</td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>Closing Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					
					}else{
					$OCR = 0.00;
					$ODR = 0.00;
					if($new_acc_bal <=0){
						$OCR = abs($new_acc_bal);
						$OB = $OCR.'Cr';
						}else{
						$ODR = abs($new_acc_bal);
						$OB = $ODR.'Dr';
					}
					$html .= '<tr style="color:red;">';
					$html .= '<td>'. _d($from_date).'</td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td></td>';
					$html .= '<td>Opening Balance</td>';
					$html .= '<td align="right">'.number_format($ODR,2).'</td>';
					$html .= '<td align="right">'.number_format($OCR,2).'</td>';
					$html .= '<td align="right">'.number_format($OB,2).'</td>';
					$html .= '</tr>';
					$total_credit = $total_credit + $OCR;
					$total_debit = $total_debit + $ODR;
					$current_debit = '';
					$current_credit = '';
					foreach ($data_report as $key => $value) {
						if($value["Amount"] !== "0.00"){
							
							$url = '';
							$ShippingParty = '';
							$ShippingAddress = '';
							if($value["PassedFrom"] == "SALE"){
								foreach($SaleIds as $key1 => $value1){
									if($value1["SalesID"] == $value["VoucherID"]){
										$ChallanID = $value1["ChallanID"];
										$ShippingParty = $value1["ShippingParty"];
										$ShippingAddress = $value1["ShippingAddress"];
									}
								}
								$url = admin_url().'challan/edit_challan/'.$ChallanID;
								}else if($value["PassedFrom"] == "SALESRTN"){
								$url = admin_url().'sale_return/edit/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PURCHASE"){
								$url = admin_url().'purchase/EditPurchaseEntry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PURCHASERTN"){
								$url = admin_url().'purchase/purchaseRtn_list/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "CDNOTE"){
								$url = admin_url().'cd_notes/edit/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "JOURNAL"){
								$url = admin_url().'accounting/new_journal_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "PAYMENTS"){
								$url = admin_url().'accounting/new_payment_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "RECEIPTS"){
								$url = admin_url().'accounting/new_receipt_entry/'.$value["VoucherID"];
								}else if($value["PassedFrom"] == "CONTRA"){
								$url = admin_url().'accounting/new_contra_entry/'.$value["VoucherID"];
								}else{
								$url = "#";
							}
							$url2 = "_blank";
							$html .= '<tr onclick="window.open('."'".$url."'".', '."'".$url2."'".')" >';    
							$html .= '<td>'. _d(substr($value["Transdate"],0,10)).'</td>';
							if($value["EffectLedger"] == "" || $value["EffectLedger"] == NULL){
								$AccountName = $value["firstname"] . " ". $value["lastname"];
								}else{
								$AccountName = $value["EffectLedger"];
							}
							$html .= '<td>'. $AccountName.'</td>';
							$html .= '<td>'. $value["PassedFrom"].'</td>';
							$html .= '<td>'. $value["VoucherID"].'</td>';
							$html .= '<td>'. $ShippingParty.'</td>';
							$html .= '<td>'. $ShippingAddress.'</td>';
							$len = strlen($value["Narration"]);
							if($len >67){
								$str = "...";
								}else{
								$str = "";
							}
							$html .= '<td title="'.$value["Narration"].'">'. substr($value["Narration"],0,70).''.$str.'</td>';
							$dvalue = "";
							if($value["TType"]=="D"){
								
								$new_acc_bal = $new_acc_bal + $value["Amount"];
								$dvalue = $value["Amount"];
								$total_debit = $total_debit + $dvalue;
								$current_debit = $current_debit + $dvalue;
								$dvalue = number_format($dvalue,2);
							}
							$html .= '<td align="right">'. $dvalue .'</td>';
							$cvalue = "";
							if($value["TType"]=="C"){
								$new_acc_bal = $new_acc_bal - $value["Amount"];
								$cvalue = $value["Amount"];
								$total_credit = $total_credit + $cvalue;
								$current_credit = $current_credit + $cvalue;
								$cvalue = number_format($cvalue,2);
							}
							$html .= '<td align="right">'.$cvalue.'</td>';
							$new_acc_bal2 = $new_acc_bal;
							if($new_acc_bal>0){
								$nab_dr_cr = "Dr";
								}else{
								$nab_dr_cr = "Cr";
							}
							$new_acc_bal2 = round($new_acc_bal2,2)." ".$nab_dr_cr;
							$html .= '<td align="right">'.number_format($new_acc_bal,2)." ".$nab_dr_cr.'</td>';
							$html .= '</tr>';
							$i++;
						}
					}
					if($data_report){
						$html .= '<tr style="color:red;">';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td>Current Total</td>';
						$html .= '<td align="right">'. number_format($current_debit,2).'</td>';
						$html .= '<td align="right">'. number_format($current_credit,2).'</td>';
						$html .= '<td align="right"></td>';
						$html .= '</tr>';
						
						$html .= '<tr style="color:red;">';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td></td>';
						$html .= '<td>Closing Balance</td>';
						// $html .= '<td align="right">'. number_format($total_debit,2).'</td>';
						// $html .= '<td align="right">'. number_format($total_credit,2).'</td>';
						$html .= '<td align="right"></td>';
						$html .= '<td align="right"></td>';
						$html .= '<td align="right">'. number_format($new_acc_bal2,2)." ".$nab_dr_cr.'</td>';
						$html .= '</tr>';
					}
				}
				$data["table"] = $html;
				echo json_encode($data);
			}
			
		}
		//============================ Share And Save File Account Ledger ==============
		public function AccountLedgerShareOnWhatsApp()
		{
			if (!has_permission_new('accounting_ledger_share', '', 'view')){
				echo json_encode('denied');
				die;
			}
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				$data_filter = $this->input->post();
				
				$account_name = $this->accounting_model->get_name_account($data_filter);
				$AccountID = $account_name->AccountID;
				$MobileNO = '91'.$account_name->phonenumber;
				if($account_name->company){
					$name = $account_name->company;
					$account_full_name = $name." (".$account_name->AccountID.")";
					}else{
					$name = $account_name->firstname." ". $account_name->lastname;
					$account_full_name = $name." (".$account_name->AccountID.")";
				}
				$SubActGroupID = $account_name->SubActGroupID;
				
				$PassedFrom = $this->input->post('PassedFrom');
				if(empty($PassedFrom)){
					$PassedFrom = 'All';
				}
				if (!has_permission_new('accounting_ledger_entry_SC', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "50003002"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry_SD', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "60001004"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID != "60001004" && $SubActGroupID != "50003002"){
					echo json_encode('denied');
					die;
					}else{
					$data_report = $this->accounting_model->get_data_general_ledger2_new($data_filter);
					$SaleIds = $this->accounting_model->GetSaleIds($data_filter);
					
					$total_bal = $this->accounting_model->get_data_for_account_bal($data_filter);
					
					$new_acc_bal = $total_bal->BAL1;
					$opening_bal = $total_bal->BAL1;
					$i = 1;
					$from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
					$from_date = date('Y-m-d',strtotime($from_date));
					$to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
					$to_date = date('Y-m-d',strtotime($to_date));
					
					$finacial_year = $this->session->userdata('finacial_year');
					if($from_date > date('20'.$finacial_year.'-04-01')){
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_cr_sum($data_filter);
						$CRSum = $getuptofromdatebal[0]['Amount'];
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_dr_sum($data_filter);
						$DRSum = $getuptofromdatebal[0]['Amount'];
						$opening_bal = $total_bal->BAL1 + $DRSum - $CRSum;
						$new_acc_bal = $total_bal->BAL1 + $DRSum - $CRSum;
					}
					
					$this->load->model('sale_reports_model');
					$selected_company_details    = $this->sale_reports_model->get_company_detail();
					
					$writer = new XLSXWriter();
					
					$company_name = array($selected_company_details->company_name);
					$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_name);
					
					$address = $selected_company_details->address;
					$company_addr = array($address,);
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_addr);
					
					$msg = "Account Ledger Report ".$this->input->post('from_date')." To " .$this->input->post('to_date')." Account: ".$account_full_name." Voucher Type: ".$PassedFrom;
					$filter = array($msg);
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $filter);
					
					// empty row
					$list_add = [];
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$writer->writeSheetRow('Sheet1', $list_add);
					
					$set_col_tk = [];
					$set_col_tk["Date"] =  'Date';
					$set_col_tk["Particular"] =  'Particular';
					$set_col_tk["Voucher Type"] =  'Voucher Type';
					$set_col_tk["Voucher_ID"] =  'Voucher ID';
					$set_col_tk["Buyer Party"] =  'Buyer Party';
					$set_col_tk["Buyer Address"] =  'Buyer Address';
					$set_col_tk["Narration"] =  'Narration';
					$set_col_tk["Debit"] =  'Debit';
					$set_col_tk["Credit"] =  'Credit';
					$set_col_tk["Balance"] =  'Balance';
					$set_col_tk["CR/DR"] =  'CR/DR';
					
					$writer_header = $set_col_tk;
					$writer->writeSheetRow('Sheet1', $writer_header);
					
					$total_debit = 0;
					$total_credit = 0;
					foreach ($data_report as $k => $value) {
						$led_from_date = date('Y-m-d',strtotime($value["Transdate"]));
						$led_to_date = date('Y-m-d',strtotime($value["Transdate"]));
						if($led_from_date >= $from_date && $led_from_date <= $to_date){
							if($i==1){
								if($opening_bal>0){
									$ob_dr_cr = "Dr";
									}else{
									$ob_dr_cr = "Cr";
								}
								
								$list_add = [];
								$list_add[] = _d($from_date);
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "Opening Balance";
								$new_bal = '';
								if($opening_bal>0){
									$new_bal = abs($opening_bal);
									$total_debit = $total_debit + $new_bal;
									$new_bal = abs($new_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								$new_bal = '';
								if($opening_bal<=0){
									$total_credit = $total_credit + abs($opening_bal);
									$new_bal = abs($opening_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								
								$list_add[] = $opening_bal_new;
								$list_add[] = $ob_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);
							}
							
							if($value["Amount"] !== "0.00"){
								$ShippingParty = '';
								$ShippingAddress = '';
								if($value["PassedFrom"] == "SALE"){
									foreach($SaleIds as $key1 => $value1){
										if($value1["SalesID"] == $value["VoucherID"]){
											$ShippingParty = $value1["ShippingParty"];
											$ShippingAddress = $value1["ShippingAddress"];
										}
									}
								}
								
								$list_add = [];
								$list_add[] = _d(substr($value["Transdate"],0,10));
								if($value["EffectLedger"] == "" || $value["EffectLedger"] == NULL){
									$AccountName = $value["firstname"] . " ". $value["lastname"];
									}else{
									$AccountName = $value["EffectLedger"];
								}
								$list_add[] = $AccountName;
								$list_add[] = $value["PassedFrom"];
								$list_add[] = $value["VoucherID"];
								$list_add[] = $ShippingParty;
								$list_add[] = $ShippingAddress;
								$list_add[] = $value["Narration"];
								
								$dvalue = "";
								if($value["TType"]=="D"){
									
									$new_acc_bal = $new_acc_bal + $value["Amount"];
									$dvalue = $value["Amount"];
									$total_debit = $total_debit + $dvalue;
									$dvalue = $dvalue;
								}
								$list_add[] = $dvalue;
								$cvalue = "";
								if($value["TType"]=="C"){
									$new_acc_bal = $new_acc_bal - $value["Amount"];
									$cvalue = $value["Amount"];
									$total_credit = $total_credit + $cvalue;
									$cvalue = $cvalue;
								}
								$list_add[] = $cvalue;
								$new_acc_bal2 = abs($new_acc_bal);
								if($new_acc_bal>0){
									$nab_dr_cr = "Dr";
									}else{
									$nab_dr_cr = "Cr";
								}
								$new_acc_bal2 = round($new_acc_bal2,2);
								$list_add[] = $new_acc_bal2;
								$list_add[] = $nab_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);   
								$i++;
							}    
							
							}else{
							if($value["TType"]=="D"){
								$new_acc_bal = $new_acc_bal + $value["Amount"];
							}
							if($value["TType"]=="C"){
								$new_acc_bal = $new_acc_bal - $value["Amount"];
							}
							$opening_bal = $new_acc_bal;
						}
					}
					
					if($data_report){ 
						if($i>1)
						{
							$list_add = [];
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "Closing Balance";
							$list_add[] = $total_debit;
							$list_add[] = $total_credit;
							$list_add[] = $new_acc_bal2;
							$list_add[] = $nab_dr_cr;
							$writer->writeSheetRow('Sheet1', $list_add);
							}else{
							
						}
					}
					
					$path = 'uploads/AccountLedger/'.$AccountID."/";
					if (!file_exists($path)) {
						mkdir($path, 0755, true);
					}
					/*$files = glob($path.'*');
						foreach($files as $file){
						if(is_file($file)) {
						unlink($file); 
						}
					}*/
					//$filename = 'Account_ledger'.$data_filter['from_date'].'To'.$data_filter['to_date'].'.xlsx';
					$filename = 'Account_ledger.xlsx';
					$writer->writeToFile(str_replace($filename, $path.$filename, $filename));
					$FullPath = site_url().$path.$filename;
					$pathNew = $path.$filename;
					$response = $this->SendMsgToWhatsApp($MobileNO,$pathNew,$FullPath,$account_full_name,$data_filter['from_date'],$data_filter['to_date']);
					$status = $response["status"];
					$data = $response["data"];
					/*echo "<pre>";
						print_r($response);
						echo $status;
					die;*/
					echo json_encode([
				    'site_url'          => site_url(),
				    'filename'          => $path.$filename,
				    'status'          =>$status,
				    'data'=>$data
					]);
					die;
				}
			}
		}
		//================= Send Message to WhatsAPP ===================================
		public function SendMsgToWhatsApp($MobileNO,$path,$FullPath,$AccountName,$FromDate,$ToDate)
		{
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{
            "integrated_number": "919511116350",
            "content_type": "template",
            "payload": {
			"messaging_product": "whatsapp",
			"type": "template",
			"template": {
			"name": "customer_ledger_share",
			"language": {
			"code": "en",
			"policy": "deterministic"
			},
			"namespace": "9af5a9d2_f151_4144_8ae2_7e5190a3a07e",
			"to_and_components": [
			{
			"to": [
			"'.$MobileNO.'"
			],
			"components": {
			"body_1": {
			"type": "text",
			"value": "'.$AccountName.'"
			},
			"body_2": {
			"type": "text",
			"value": "'.$FromDate.'"
			},
			"body_3": {
			"type": "text",
			"value": "'.$ToDate.'"
			},
			"body_4": {
			"type": "text",
			"value": "'.$FullPath.'"
			},
			"button_1": {
			"subtype": "url",
			"type": "text",
			"value": "'.$path.'"
			}
			}
			}
			]
			}
            }
			}',
			CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'authkey: 466363AedlbXQ869059f72P1',
			),
			));
			$response = curl_exec($curl);
			
			curl_close($curl);
			return json_decode($response,true);
		}
		//=========================== Export Account Ledger ============================	
		public function export_general_ledger_new()
		{
			if(!class_exists('XLSXReader_fin')){
				require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(TIMESHEETS_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');
			
			if($this->input->post()){
				$data_filter = $this->input->post();
				
				$account_name = $this->accounting_model->get_name_account($data_filter);
				if($account_name->company){
					$name = $account_name->company;
					$account_full_name = $name." (".$account_name->AccountID.")". " - ".$account_name->StationName;
					}else{
					$name = $account_name->firstname." ". $account_name->lastname;
					$account_full_name = $name." (".$account_name->AccountID.")";
				}
				$SubActGroupID = $account_name->SubActGroupID;
				
				$PassedFrom = $this->input->post('PassedFrom');
				if(empty($PassedFrom)){
					$PassedFrom = 'All';
				}
				if (!has_permission_new('accounting_ledger_entry_SC', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "50003002"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry_SD', '', 'view') && !has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID == "60001004"){
					echo json_encode('denied');
					die;
					}else if (!has_permission_new('accounting_ledger_entry', '', 'view') && $SubActGroupID != "60001004" && $SubActGroupID != "50003002"){
					echo json_encode('denied');
					die;
					}else{
					$data_report = $this->accounting_model->get_data_general_ledger2_new($data_filter);
					$SaleIds = $this->accounting_model->GetSaleIds($data_filter);
					
					$total_bal = $this->accounting_model->get_data_for_account_bal($data_filter);
					
					$new_acc_bal = $total_bal->BAL1;
					$opening_bal = $total_bal->BAL1;
					$i = 1;
					$from_date = to_sql_date($data_filter['from_date']) . ' 00:00:00';
					$from_date = date('Y-m-d',strtotime($from_date));
					$to_date = to_sql_date($data_filter['to_date']) . ' 23:59:59';
					$to_date = date('Y-m-d',strtotime($to_date));
					
					$finacial_year = $this->session->userdata('finacial_year');
					if($from_date > date('20'.$finacial_year.'-04-01')){
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_cr_sum($data_filter);
						$CRSum = $getuptofromdatebal[0]['Amount'];
						$getuptofromdatebal = $this->accounting_model->get_data_in_between_ledger_dr_sum($data_filter);
						$DRSum = $getuptofromdatebal[0]['Amount'];
						$opening_bal = $total_bal->BAL1 + $DRSum - $CRSum;
						$new_acc_bal = $total_bal->BAL1 + $DRSum - $CRSum;
					}
					
					$this->load->model('sale_reports_model');
					$selected_company_details    = $this->sale_reports_model->get_company_detail();
					
					$writer = new XLSXWriter();
					
					$company_name = array($selected_company_details->company_name);
					$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_name);
					
					$address = $selected_company_details->address;
					$company_addr = array($address,);
					$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 1, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $company_addr);
					
					$msg = "Account Ledger Report ".$this->input->post('from_date')." To " .$this->input->post('to_date')." Account: ".$account_full_name." Voucher Type: ".$PassedFrom;
					$filter = array($msg);
					$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 0, $end_row = 2, $end_col = 8);  //merge cells
					$writer->writeSheetRow('Sheet1', $filter);
					
					// empty row
					$list_add = [];
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$list_add[] = "";
					$writer->writeSheetRow('Sheet1', $list_add);
					
					$set_col_tk = [];
					$set_col_tk["Date"] =  'Date';
					$set_col_tk["Particular"] =  'Particular';
					$set_col_tk["Voucher Type"] =  'Voucher Type';
					$set_col_tk["Voucher_ID"] =  'Voucher ID';
					$set_col_tk["Buyer Party"] =  'Buyer Party';
					$set_col_tk["Buyer Address"] =  'Buyer Address';
					$set_col_tk["Narration"] =  'Narration';
					$set_col_tk["Debit"] =  'Debit';
					$set_col_tk["Credit"] =  'Credit';
					$set_col_tk["Balance"] =  'Balance';
					$set_col_tk["CR/DR"] =  'CR/DR';
					
					$writer_header = $set_col_tk;
					$writer->writeSheetRow('Sheet1', $writer_header);
					
					$total_debit = 0;
					$total_credit = 0;
					foreach ($data_report as $k => $value) {
						$led_from_date = date('Y-m-d',strtotime($value["Transdate"]));
						$led_to_date = date('Y-m-d',strtotime($value["Transdate"]));
						if($led_from_date >= $from_date && $led_from_date <= $to_date){
							if($i==1){
								if($opening_bal>0){
									$ob_dr_cr = "Dr";
									}else{
									$ob_dr_cr = "Cr";
								}
								
								$list_add = [];
								$list_add[] = _d($from_date);
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "";
								$list_add[] = "Opening Balance";
								$new_bal = '';
								if($opening_bal>0){
									$new_bal = abs($opening_bal);
									$total_debit = $total_debit + $new_bal;
									$new_bal = abs($new_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								$new_bal = '';
								if($opening_bal<=0){
									$total_credit = $total_credit + abs($opening_bal);
									$new_bal = abs($opening_bal);
									$opening_bal_new = abs($new_bal);
								}
								$list_add[] = $new_bal;
								
								$list_add[] = $opening_bal_new;
								$list_add[] = $ob_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);
							}
							
							if($value["Amount"] !== "0.00"){
								$ShippingParty = '';
								$ShippingAddress = '';
								if($value["PassedFrom"] == "SALE"){
									foreach($SaleIds as $key1 => $value1){
										if($value1["SalesID"] == $value["VoucherID"]){
											$ShippingParty = $value1["ShippingParty"];
											$ShippingAddress = $value1["ShippingAddress"];
										}
									}
								}
								
								$list_add = [];
								$list_add[] = _d(substr($value["Transdate"],0,10));
								if($value["EffectLedger"] == "" || $value["EffectLedger"] == NULL){
									$AccountName = $value["firstname"] . " ". $value["lastname"];
									}else{
									$AccountName = $value["EffectLedger"];
								}
								$list_add[] = $AccountName;
								$list_add[] = $value["PassedFrom"];
								$list_add[] = $value["VoucherID"];
								$list_add[] = $ShippingParty;
								$list_add[] = $ShippingAddress;
								$list_add[] = $value["Narration"];
								
								$dvalue = "";
								if($value["TType"]=="D"){
									
									$new_acc_bal = $new_acc_bal + $value["Amount"];
									$dvalue = $value["Amount"];
									$total_debit = $total_debit + $dvalue;
									$dvalue = $dvalue;
								}
								$list_add[] = $dvalue;
								$cvalue = "";
								if($value["TType"]=="C"){
									$new_acc_bal = $new_acc_bal - $value["Amount"];
									$cvalue = $value["Amount"];
									$total_credit = $total_credit + $cvalue;
									$cvalue = $cvalue;
								}
								$list_add[] = $cvalue;
								$new_acc_bal2 = abs($new_acc_bal);
								if($new_acc_bal>0){
									$nab_dr_cr = "Dr";
									}else{
									$nab_dr_cr = "Cr";
								}
								$new_acc_bal2 = round($new_acc_bal2,2);
								$list_add[] = $new_acc_bal2;
								$list_add[] = $nab_dr_cr;
								$writer->writeSheetRow('Sheet1', $list_add);   
								$i++;
							}    
							
							}else{
							if($value["TType"]=="D"){
								$new_acc_bal = $new_acc_bal + $value["Amount"];
							}
							if($value["TType"]=="C"){
								$new_acc_bal = $new_acc_bal - $value["Amount"];
							}
							$opening_bal = $new_acc_bal;
						}
					}
					
					if($data_report){ 
						if($i>1)
						{
							$list_add = [];
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "";
							$list_add[] = "Closing Balance";
							$list_add[] = $total_debit;
							$list_add[] = $total_credit;
							$list_add[] = $new_acc_bal2;
							$list_add[] = $nab_dr_cr;
							$writer->writeSheetRow('Sheet1', $list_add);
							}else{
							
						}
					}
					
					
					$files = glob(TIMESHEETS_PATH_EXPORT_FILE.'*');
					foreach($files as $file){
						if(is_file($file)) {
							unlink($file); 
						}
					}
					$filename = 'Account_ledger_Report.xlsx';
					$writer->writeToFile(str_replace($filename, TIMESHEETS_PATH_EXPORT_FILE.$filename, $filename));
					echo json_encode([
					'site_url'          => site_url(),
					'filename'          => TIMESHEETS_PATH_EXPORT_FILE.$filename,
					]);
					die;
				}
				
			}
		}
		//=================== aprrove Single Voucher Entry =============================
		public function ApproveVoucherEntry($UniquID,$PassedFrom,$list="")
		{
			if (!has_permission_new('accounting_voucher_entry_approve', '', 'edit')) {
				access_denied('access_denied');
			}
			$success = $this->accounting_model->ApproveVoucherEntry($UniquID,$PassedFrom);
			
			if ($this->input->is_ajax_request()) {
				if ($success) {
					echo json_encode(['success' => true,'message' => _l('approve')]);
					} else {
					echo json_encode(['success' => false,'message' => _l('can_not_approve')]);
				}
				} else {
				// For regular form submissions
				if ($success) {
					set_alert('success', _l('approve'));
					} else {
					set_alert('warning', _l('can_not_approve'));
				}
				if($PassedFrom == "PAYMENTS" && $list ==""){
					redirect(admin_url('accounting/new_payment_entry'));
					}elseif($PassedFrom == "RECEIPTS" && $list ==""){
					redirect(admin_url('accounting/new_receipt_entry'));
					}else{
					redirect(admin_url('accounting/PendingVoucherEntry'));
				}
				
			}
		}		
		
		//========================== Approve Voucher Entry Page=========================
		
		public function PendingVoucherEntry($id = '')
		{	
			if (!has_permission_new('accounting_voucher_entry_approve', '', 'view')) {
				access_denied('access_denied');
			}
			if($id != ''){
				$data['payment_entry'] = $this->accounting_model->get_payments_entryUpdated($id);
				if($data['payment_entry']->Status == 'N') {
					$data['show_approve_button'] = true;
					} else {
					$data['show_approve_button'] = false;
				}
			}
			
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['title'] = "New Payment";
			
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select_for_payment();
			$data['genral_account_to_select'] = $this->accounting_model->get_data_ganeral_account_to_select();
			
			$this->load->view('payment/PendingVoucherEntry', $data); // show pending entry			 
		}
		//================ Approve Multiple Voucher Entry ==============================
		public function approve_multiple_payments() 
		{		
			if (!has_permission_new('accounting_voucher_entry_approve', '', 'edit')) {
				access_denied('access_denied');
			}
			$UniquIDs = $this->input->post('voucher_ids');  
			$PassedFrom = $this->input->post('PassedFrom');  
			if (!empty($UniquIDs)) {
				$results = [];
				foreach ($UniquIDs as $id) {
					$results[] = $this->accounting_model->ApproveVoucherEntry($id,$PassedFrom);
				}
				if (in_array(false, $results, true)) {
					echo 'error';
					} else {
					echo 'success';
				}
				} else {
				show_error('No vouchers received');
			}
		} 
		
		//=========== closing Balance =================
		public function getclosing_balance() 
		{
			$AccountID = $this->input->post('AccountID');
			
			$closing_balance = $this->accounting_model->getclosing_balance($AccountID);
			echo json_encode(['closing_balance' => $closing_balance ]);
		}
		
		//=========== Opening Balance =================
		public function get_account_opening_balance()
		{
			$AccountID = $this->input->post('account_id');
			
			$Opening_Balance = $this->accounting_model->getclosing_balance($AccountID);
			
			echo json_encode([ 'success' => true,'Opening_Balance' => $Opening_Balance, 
			'Formatted_Balance' => app_format_money($Opening_Balance, $this->currency->name)]);
		}	
		
		public function journal_entryNew($id = '')
		{		
			if (!has_permission_new('accounting_journal_entry_multiple', '', 'view')) {
				access_denied('accounting_journal_entry_multiple');
			}
			if ($this->input->post()) {
				
				$data = $this->input->post();
				// die;
				if($id == ''){
					if (!has_permission_new('accounting_journal_entry_multiple', '', 'create')) {
						access_denied('accounting_journal_entry_multiple');
					}
					// echo "<pre>";print_r($data);die;
					$success = $this->accounting_model->add_journal_entryNew($data);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('added_successfully', _l('journal_entry')));
					}
					}else{
					if (!has_permission_new('accounting_journal_entry_multiple', '', 'edit')) {
						access_denied('accounting_journal_entry_multiple');
					}
					
					$success = $this->accounting_model->update_journal_entryNew($data, $id);
					if ($success === 'close_the_book') {
						$message = _l('has_closed_the_book');
						set_alert('warning', _l('has_closed_the_book'));
						}elseif ($success) {
						set_alert('success', _l('updated_successfully', _l('journal_entry')));
					}
				}
				redirect(admin_url('accounting/journal_entryNew'));
			}
			
			if($id != ''){
				$data['journal_entry'] = $this->accounting_model->get_journal_entryNew($id);
				
			} 
			/*  echo "<pre>Journal Entry Object: ";
				print_r($data['journal_entry']); 
			echo "</pre>"; */
			
			$this->load->model('currencies_model');
			$data['currency'] = $this->currencies_model->get_base_currency();
			$data['next_number'] = $this->accounting_model->get_journal_entry_next_number();
			//$data['alljournal_entry'] = $this->accounting_model->getall_journal_entry();
			$data['title'] = _l('journal_entry');
			
			$data['account_to_select'] = $this->accounting_model->get_data_account_to_select_for_journal();
			
			$this->load->view('journal_entry/journal_entryNew', $data);
		}
		
		
		
		//----------------- Account Dashboard ------------------	
		
		
		
		
		
		public function AccountDashboard()
		{  
			if (!has_permission_new('AccountDashboard', '', 'view')) {
				access_denied('AccountDashboard');
			}
			$data['title'] = "Account Dashboard";
			$this->load->view('report/AccountDashboard', $data);
		}
		
		public function getExpensesChart()
		{
			$selected_company = $this->session->userdata('root_company');
			
			$fromdate = to_sql_date($this->input->post('from_date'));
			$todate = to_sql_date($this->input->post('to_date'));
			
			// Direct & Indirect data
			$DirectExp   = $this->accounting_model->GetDirectExpenses($fromdate, $todate);
			$IndirectExp = $this->accounting_model->GetOtherExpensesData($fromdate, $todate);
			
			$series = [];
			$drilldown = [];
			
			/* ===========================
				LEVEL 1 – ROOT
			============================ */
			
			$series[] = [
			'name' => 'Expenses',
			'colorByPoint' => true,
			'data' => [
            [
			'name' => 'Direct Expenses',
			'y' => $DirectExp->CurrentYear,
			'drilldown' => 'DIRECT'
            ],
            [
			'name' => 'Indirect Expenses',
			'y' => $IndirectExp->CurrentYear,
			'drilldown' => 'INDIRECT'
            ]
			]
			];
			
			/* ===========================
				LEVEL 2 – GROUPS
			============================ */
			
			$directGroups = [];
			foreach ($DirectExp->nestedData as $g1) {
				$directGroups[] = [
				'name' => $g1['Group1Name'],
				'y' => $g1['Group1ClsBal'],
				'drilldown' => 'G1_' . $g1['Group1ID']
				];
			}
			
			$drilldown[] = [
			'id' => 'DIRECT',
			'name' => 'Direct Expense Groups',
			'data' => $directGroups
			];
			
			$indirectGroups = [];
			foreach ($IndirectExp->nestedData as $g1) {
				$indirectGroups[] = [
				'name' => $g1['Group1Name'],
				'y' => $g1['Group1ClsBal'],
				'drilldown' => 'G1_' . $g1['Group1ID']
				];
			}
			
			$drilldown[] = [
			'id' => 'INDIRECT',
			'name' => 'Indirect Expense Groups',
			'data' => $indirectGroups
			];
			
			/* ===========================
				LEVEL 3 – SUB GROUPS
				LEVEL 4 – ACCOUNTS
			============================ */
			
			$allGroups = array_merge($DirectExp->nestedData, $IndirectExp->nestedData);
			
			foreach ($allGroups as $g1) {
				
				$subGroupData = [];
				
				foreach ($g1['SubGroups2'] as $g2) {
					
					$subGroupData[] = [
					'name' => $g2['SubGroupName'],
					'y' => $g2['Group2ClsBal'],
					'drilldown' => 'G2_' . $g2['SubActGroupID']
					];
					
					// LEVEL 4 – Accounts
					$accounts = [];
					foreach ($g2['Accounts'] as $acc) {
						$accounts[] = [
						$acc['AccountName'],
						(float)$acc['AccountClsBal']
						];
					}
					
					$drilldown[] = [
					'id' => 'G2_' . $g2['SubActGroupID'],
					'name' => $g2['SubGroupName'],
					'data' => $accounts
					];
				}
				
				$drilldown[] = [
				'id' => 'G1_' . $g1['Group1ID'],
				'name' => $g1['Group1Name'],
				'data' => $subGroupData
				];
			}
			
			echo json_encode([
			'series' => $series,
			'drilldown' => $drilldown
			]);
			die;
		}
		
		public function GetCalenderMonthlyDueData()
		{	 
			$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'Month' => $this->input->post('Month'),
			);
			
			$body_data = $this->accounting_model->GetBillsReceivableDueCalendarData($filterdata);
			
			
			$month_input = $filterdata['Month'];  
			$first_day = date('Y-m-01', strtotime($month_input));
			$last_day = date('Y-m-t', strtotime($month_input));
			$days_in_month = date('t', strtotime($month_input));
			
			$calendar_data = array();
			for ($day = 1; $day <= $days_in_month; $day++) {
				$current_date = date('Y-m-d', strtotime($month_input . '-' . str_pad($day, 2, '0', STR_PAD_LEFT)));
				$calendar_data[$current_date] = 0;
			}
			
			
			foreach ($body_data as $key => $value) {
				$collectionAmt = $value["TotalAmount"];
				$collectionDate = $value["TransDate"];
				
				if (isset($calendar_data[$collectionDate])) {
					$calendar_data[$collectionDate] += $collectionAmt;
				}
			}
			
			
			$formatted_data = array();
			foreach ($calendar_data as $date => $amount) {
				$formatted_data[] = array(
				'date' => $date,
				'temperature' => floatval($amount)  
				);
			}
			
			header('Content-Type: application/json');
			echo json_encode($formatted_data);
		}
		
		
		public function GetCalenderMonthlyPaymentData()
		{	 
			$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date' => $this->input->post('to_date'),
			'Month' => $this->input->post('Month'),
			);
			
			$body_data = $this->accounting_model->GetBillsPayableDueCalendarData($filterdata);
			
			
			$month_input = $filterdata['Month'];  
			$first_day = date('Y-m-01', strtotime($month_input));
			$last_day = date('Y-m-t', strtotime($month_input));
			$days_in_month = date('t', strtotime($month_input));
			
			
			$calendar_data = array();
			for ($day = 1; $day <= $days_in_month; $day++) {
				$current_date = date('Y-m-d', strtotime($month_input . '-' . str_pad($day, 2, '0', STR_PAD_LEFT)));
				$calendar_data[$current_date] = 0;
			}
			
			
			foreach ($body_data as $key => $value) {
				$paymentAmt = $value["TotalAmount"];
				$paymentDate = $value["TransDate"];
				
				if (isset($calendar_data[$paymentDate])) {
					$calendar_data[$paymentDate] += $paymentAmt;
				}
			}
			
			
			$formatted_data = array();
			foreach ($calendar_data as $date => $amount) {
				$formatted_data[] = array(
				'date' => $date,
				'temperature' => floatval($amount)  
				);
			}
			
			header('Content-Type: application/json');
			echo json_encode($formatted_data);
		}	
		
		public function GetDashboardCounters()
		{ 
			$JournalEntryAmt = $this->accounting_model->JournalEntryAmt($this->input->post());
			$ContraEntryAmt = $this->accounting_model->ContraEntryAmt($this->input->post());
			$ReceiptEntryAmt = $this->accounting_model->ReceiptEntryAmt($this->input->post());
			$PaymentEntryAmt = $this->accounting_model->PaymentEntryAmt($this->input->post());
			
			$return = [
			'JournalEntryAmt' => $JournalEntryAmt,
			'ContraEntryAmt' => $ContraEntryAmt,
			'ReceiptEntryAmt' => $ReceiptEntryAmt,
			'PaymentEntryAmt' => $PaymentEntryAmt,
			];
			
			echo json_encode($return);
		}	
		
		public function get_monthly_payable_amounts()
		{  
			$data = $this->accounting_model->get_monthly_payable_amounts();
			echo json_encode($data);
		}  
		
		public function getMonthly_Due_amounts()
		{		 
			$data = $this->accounting_model->getMonthly_Due_amounts();		
			echo json_encode($data);
		}  
		
		public function getMonthWiseClosingBalance()
		{
			/* $filterdata = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'   => $this->input->post('to_date')
			); */
			
			$data = $this->accounting_model->getMonthly_ClosingBal();
			echo json_encode($data);
		}
		
		
		
		public function getMonthly_Sale_Purch()
		{
			/* $filterdata = array(
				'from_date' => $this->input->post('from_date'),
				'to_date'   => $this->input->post('to_date')
				);
			*/
			$data = $this->accounting_model->getMonthly_Sale_Purchase();
			echo json_encode($data);
		}
		
		
		
		public function getMonthWise_ClosingStock()
		{
			$filterdata = array(
			'from_date' => $this->input->post('from_date'),
			'to_date'   => $this->input->post('to_date'), 
			);
			
			$data = $this->accounting_model->getMonthly_ClosingStock($filterdata);
			
			//print_r($data); die();
			echo json_encode($data);
		}
		
		public function GetReceiptPayment()
		{
			$TotalReceiptPayment = $this->accounting_model->GetReceiptPayment($this->input->post());
			// echo "<pre>";print_r($TotalExpense);die;
			$return = [
			'TotalReceiptPayment' => $TotalReceiptPayment['chartData'],
			'Dates' => $TotalReceiptPayment['Dates'],
			];
			
			echo json_encode($return);
		}
		
		public function GetCashAndEquivalant()
		{
			$RtnData = $this->accounting_model->GetCashAndEquivalant($this->input->post());
			$return = [
			'RtnData' => $RtnData,
			];
			
			echo json_encode($return);
		}
	}	
	
