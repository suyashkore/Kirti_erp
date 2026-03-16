<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Accounting_model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		//===================== Profit & Loss Function =================================
		//===================== Revenue From Operation =================================
		public function Getrevenue_from_opn()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy- 1;
			$year = array($fy,$last_fy);
			$this->db->select('SUM(tblhistory.ChallanAmt - tblhistory.DiscAmt) AS TotalSaleAmt,tblhistory.FY');
			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'history.FY', $year);
			$this->db->where(db_prefix() . 'history.TType', "O");
			$this->db->where(db_prefix() . 'history.TType2', "Order");
			$this->db->where(db_prefix() . 'history.BillID IS NOT NULL');
			$this->db->where(db_prefix() . 'history.TransID IS NOT NULL');
			$this->db->group_by(db_prefix() . 'history.FY');
			$Saledata =  $this->db->get(db_prefix() . 'history')->result_array();
			$TotalSale = 0;
			$TotalSalePre = 0;
			foreach($Saledata as $val){
				if($val["FY"] == $fy){
					$TotalSale = $val["TotalSaleAmt"];
					}elseif($val["FY"] == $last_fy){
					$TotalSalePre = $val["TotalSaleAmt"];
				}
			}
			$revenueData->CurrentYear = $TotalSale;
			$revenueData->PriviousYear = $TotalSalePre;
			return $revenueData;
		}
		
		//===================== Revenue From Operation =================================
		public function Getsalereturn_from_opn()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy- 1;
			$year = array($fy,$last_fy);
			$TType2 = array("Damage","Fresh");
			$this->db->select('SUM(tblhistory.ChallanAmt - tblhistory.DiscAmt) AS TotalSaleAmt,tblhistory.FY');
			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'history.FY', $year);
			$this->db->where(db_prefix() . 'history.TType', "R");
			$this->db->where_in(db_prefix() . 'history.TType2', $TType2);
			$this->db->where(db_prefix() . 'history.BillID IS NOT NULL');
			$this->db->where(db_prefix() . 'history.TransID IS NOT NULL');
			$this->db->group_by(db_prefix() . 'history.FY');
			$SaleRtndata =  $this->db->get(db_prefix() . 'history')->result_array();
			$TotalSaleRtn = 0;
			$TotalSaleRtnPre = 0;
			foreach($SaleRtndata as $val){
				if($val["FY"] == $fy){
					$TotalSaleRtn = $val["TotalSaleAmt"];
					}elseif($val["FY"] == $last_fy){
					$TotalSaleRtnPre = $val["TotalSaleAmt"];
				}
			}
			$SaleRtnData->CurrentYear = $TotalSaleRtn;
			$SaleRtnData->PriviousYear = $TotalSaleRtnPre;
			return $SaleRtnData;
		}
		
		
		//=========================== Other Income =====================================
		public function GetOtherIncome($fromdate="",$todate="")
		{
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			// echo $todate;die
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			$this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,clients.company,
			tblclients.AccountID,tblclients.SubActGroupID1,tblaccountgroupssub1.SubActGroupName As ActGrp1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.ActGroupID', '10019');
			
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
			$OtherIncomeData =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			$ActGroup1List = array();
			$ActGroup2List = array();
			$AccountList = array();
			foreach($OtherIncomeData as $OIncKey=>$OIncVal){
				$new1 = array(
                "AccountID"=>$OIncVal["SubActGroupID1"],
                "AccountName"=>$OIncVal["ActGrp1"]
				);
				array_push($ActGroup1List,$new1);
				$new2 = array(
                "AccountID1"=>$OIncVal["SubActGroupID1"],
                "AccountID"=>$OIncVal["SubActGroupID"],
                "AccountName"=>$OIncVal["SubActGroupName"]
				);
				array_push($ActGroup2List,$new2);
				
				$new = array(
                "AccountID2"=>$OIncVal["SubActGroupID"],
                "AccountID"=>$OIncVal["AccountID"],
                "AccountName"=>$OIncVal["company"]
				);
				array_push($AccountList,$new);
			}
			$ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
			$ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
			$AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
			$i = 0;
			$nestedData = [];
			$TotalIncome = 0;
			$TotalIncomePre = 0;
			foreach($ActGroup1UniqueList as $ActGrp1){
				$Group1Data = [
                'Group1Name' => $ActGrp1['AccountName'],
                'Group1ID' => $ActGrp1['AccountID'],
				];
				$ClsBalGroup1 = 0;
				$ClsBalGroup1Pre = 0;
				foreach($ActGroup2UniqueList as $val2){
					$ClsBalGroup2 = 0;
					$ClsBalGroup2Pre = 0;
					if($ActGrp1["AccountID"] == $val2["AccountID1"]){
						$Group2Data = [
                        'SubGroupName' => $val2['AccountName'],
                        'SubActGroupID' => $val2['AccountID'],
						];
						foreach($AccountUniqueList as $ActList){
							if($ActList["AccountID2"]==$val2['AccountID']){
								$ClsBalAccountWise = 0;
								$ClsBalAccountWisePre = 0;
								$Act_opn = 0;
								$ActCr = 0;
								$ActDr = 0;
								$Act_opnPre = 0;
								$ActCrPre = 0;
								$ActDrPre = 0;
								foreach($OtherIncomeData as $key=>$val){
									if($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCr += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDr += $val["SumAmt"];
									}
									if($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCrPre += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDrPre += $val["SumAmt"];
									}
								}
								$ClsBalAccountWise = $ActCr - $ActDr;
								$ClsBalAccountWisePre = $ActCrPre - $ActDrPre;
								
								$ClsBalGroup2 += $ClsBalAccountWise;
								$ClsBalGroup2Pre += $ClsBalAccountWisePre;
								$AccountData = [
                                'AccountName' => $ActList['AccountName'],
                                'AccountID' => $ActList['AccountID'],
                                'AccountClsBal' =>$ClsBalAccountWise,
                                'AccountClsBalPre' =>$ClsBalAccountWisePre,
								];
								$Group2Data['Accounts'][] = $AccountData;
							}
						}
						$Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
						$Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
						$ClsBalGroup1 += $ClsBalGroup2;
						$ClsBalGroup1Pre += $ClsBalGroup2Pre;
						$Group1Data['SubGroups2'][] = $Group2Data; 
					}
				}
				$TotalIncome += abs($ClsBalGroup1);
				$TotalIncomePre += abs($ClsBalGroup1Pre);
				$Group1Data['Group1ClsBal'] = abs($ClsBalGroup1);
				$Group1Data['Group1ClsBalPre'] = abs($ClsBalGroup1Pre);
				$nestedData[] = $Group1Data;
				$i++;
			}
			$OtherIncome->nestedData = $nestedData;
			$OtherIncome->CurrentYear = $TotalIncome;
			$OtherIncome->PriviousYear = $TotalIncomePre;
			return $OtherIncome;
		}
		//================== Get RM Last Purchase rate By Item List ====================
		public function GetRMLastPurchaseRate($ItemList,$todate="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy.'-04-01 00:00:00';
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			// For RM rate Master
			$this->db->select('tblhistory.ItemID,tblhistory.PurchRate');
			$this->db->where('tblhistory.FY', $fy);
			$this->db->where('tblhistory.PlantID', $selected_company);
			$this->db->where('tblhistory.TType', "P");
			$this->db->where('tblhistory.TType2', "Purchase");
			$this->db->where('tblhistory.BilledQty>0');
			if (!empty($ItemList)) {
				$this->db->where_in('tblhistory.ItemID', $ItemList);
				} else {
				$this->db->where("1=0");
			}
			$this->db->where( db_prefix() . 'history.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblhistory.ItemID');
			$this->db->from(db_prefix() . 'history');
			$RMRateData =  $this->db->get()->result_array();
			return $RMRateData;
		}
		//================== Get FG rate from Master By Item List ======================
		public function GetFGRateItemWise($ItemList,$todate="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$curDate = date('Y-m-d H:i:s');
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			// For FG rate Master
			$this->db->select('tblrate_master.assigned_rate,rate_master.item_id');
			$this->db->where('tblrate_master.state_id', "UP");
			$this->db->where('tblrate_master.distributor_id', "594");
			if (!empty($ItemList)) {
				$this->db->where_in('tblrate_master.item_id', $ItemList);
				} else {
				$this->db->where("1=0");
			}
			// $this->db->where('tblrate_master.effective_date <=', $curDate);
			$this->db->where( db_prefix() . 'rate_master.effective_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->from(db_prefix() . 'rate_master');
			$FGRateData =  $this->db->get()->result_array();
			return $FGRateData;
		}
		//===================== Opening Inventory Amount ===============================
		public function GetOpeningInventoryAmt($fromdate="",$todate="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblstockmaster.OQty) AS OQty,tblstockmaster.ItemID,tblstockmaster.FY,tblitems.MainGrpID');
			$this->db->join('tblitems', 'tblitems.item_code = tblstockmaster.ItemID AND tblitems.PlantID = tblstockmaster.PlantID');
			$this->db->where_in(db_prefix() . 'stockmaster.FY', $year);
			$this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);
			$this->db->group_by('tblstockmaster.ItemID,tblstockmaster.FY');
			$ItemWiseInventory =  $this->db->get(db_prefix() . 'stockmaster')->result_array();
			$ItemList = array();
			$ItemListKeyVal = array();
			/*foreach($ItemWiseInventory as $val){
				if($val["OQty"] > 0){
		        array_push($ItemList,$val["ItemID"]);
				}
			}*/
			foreach($ItemWiseInventory as $value){
				if($value["OQty"] > 0){
					array_push($ItemList,$value["ItemID"]);
					array_push($ItemListKeyVal,["ItemID"=>$value["ItemID"],"MainGrpID"=>$value["MainGrpID"],"OQty"=>$value["OQty"],"FY"=>$value["FY"]]);
				}
			}
			$ItemList = array_unique($ItemList, SORT_REGULAR);
			$ItemListKeyValNew = array_map("unserialize", array_unique(array_map("serialize", $ItemListKeyVal)));
			
			$RMRateData = $this->GetRMLastPurchaseRate($ItemList,$todate);
			$FGRateData = $this->GetFGRateItemWise($ItemList,$todate);
			
			$i = 0;
			$TotalOpnAmt = 0;
			$TotalOpnAmtPre = 0;
			$TotalRMOpnAmt = 0;
			$TotalFGOpnAmt = 0;
			foreach($ItemListKeyValNew as $key=>$val){
				$BasicRate = 1;
				if($val["MainGrpID"] == "1"){
					foreach($FGRateData as $ratekey=>$rateval){
						if($val["ItemID"] == $rateval["item_id"] && $BasicRate == "0"){
							$BasicRate = $rateval["assigned_rate"];
						}
					}
					if($val["OQty"] > 0 && $val["FY"] == $fy){
						$ItemAmt = $val["OQty"] * $BasicRate;
						$TotalOpnAmt += $ItemAmt;
						$TotalFGOpnAmt += $ItemAmt;
					}
					if($val["OQty"] > 0 && $val["FY"] == $last_fy){
						$ItemAmtPre = $val["OQty"] * $BasicRate;
						$TotalOpnAmtPre += $ItemAmtPre;
					}
					}else{
					foreach($RMRateData as $ratekey1=>$rateval1){
						if($val["ItemID"] == $rateval1["ItemID"] && $BasicRate == "0"){
							$BasicRate = $rateval1["PurchRate"];
						}
					}
					if($val["OQty"] > 0 && $val["FY"] == $fy){
						$ItemAmt = $val["OQty"] * $BasicRate;
						$TotalOpnAmt += $ItemAmt;
						$TotalRMOpnAmt += $ItemAmt;
					}
					if($val["OQty"] > 0 && $val["FY"] == $last_fy){
						$ItemAmtPre = $val["OQty"] * $BasicRate;
						$TotalOpnAmtPre += $ItemAmtPre;
					}
				}
				$ItemWiseInventory[$key]["rate"] = $BasicRate;
				$i++;
			}
			$OpeningAmt->CurrentYear = $TotalOpnAmt;
			$OpeningAmt->PriviousYear = $TotalOpnAmtPre;
			$OpeningAmt->TotalRMOpnAmt = $TotalRMOpnAmt;
			$OpeningAmt->TotalFGOpnAmt = $TotalFGOpnAmt;
			$OpeningAmt->ItemWiseInventory = $ItemWiseInventory;
			return $OpeningAmt;	
		}
		
		//========================== Transaction Amount ================================
		public function GetTransactionAmt($fromdate = "",$todate = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblhistory.BilledQty * tblhistory.BasicRate) AS TotalAmt,tblhistory.FY,TType,TType2');
			$this->db->where_in(db_prefix() . 'history.FY', $year);
			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'history.BillID IS NOT NULL');
			$this->db->where(db_prefix() . 'history.TransID IS NOT NULL');
			$this->db->where( db_prefix() . 'history.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblhistory.TType,tblhistory.TType2,tblhistory.FY');
			$PurchaseAmt =  $this->db->get(db_prefix() . 'history')->result_array();
			$i = 0;
			$TotalPurchAmt = 0;
			$TotalPurchAmtPre = 0;
			$TotalIssueAmt = 0;
			$TotalIssueAmtPre = 0;
			$TotalPrdAmt = 0;
			$TotalPrdAmtPre = 0;
			$TotalSaleAmt = 0;
			$TotalSaleAmtPre = 0;
			$TotalFrtRtnSaleAmt = 0;
			$TotalFrtRtnSaleAmtPre = 0;
			$TotalDFrtRtnSaleAmt = 0;
			$TotalDFrtRtnSaleAmtPre = 0;
			$TotalFreeAmt = 0;
			$TotalFreeAmtPre = 0;
			$TotalFreeDistAmt = 0;
			$TotalFreeDistAmtPre = 0;
			foreach($PurchaseAmt as $key=>$val){
				if($val["FY"] == $fy && $val["TType"] == "P" && $val["TType2"] == "Purchase"){
					$TotalPurchAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "P" && $val["TType2"] == "Purchase"){
					$TotalPurchAmtPre += $val["TotalAmt"];
					}elseif($val["FY"] == $fy && $val["TType"] == "A" && $val["TType2"] == "Issue"){
					$TotalIssueAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "A" && $val["TType2"] == "Issue"){
					$TotalIssueAmtPre += $val["TotalAmt"];
					}elseif($val["FY"] == $fy && $val["TType"] == "B" && $val["TType2"] == "Production"){
					$TotalPrdAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "B" && $val["TType2"] == "Production"){
					$TotalPrdAmtPre += $val["TotalAmt"];
					}elseif($val["FY"] == $fy && $val["TType"] == "O" && $val["TType2"] == "Order"){
					$TotalSaleAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "O" && $val["TType2"] == "Order"){
					$TotalSaleAmtPre += $val["TotalAmt"];
					}elseif($val["FY"] == $fy && $val["TType"] == "R" && $val["TType2"] == "Fresh"){
					$TotalFrtRtnSaleAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "R" && $val["TType2"] == "Fresh"){
					$TotalFrtRtnSaleAmtPre += $val["TotalAmt"];
					}elseif($val["FY"] == $fy && $val["TType"] == "R" && $val["TType2"] == "Damage"){
					$TotalDFrtRtnSaleAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "R" && $val["TType2"] == "Damage"){
					$TotalDFrtRtnSaleAmtPre += $val["TotalAmt"];
					}elseif($val["FY"] == $fy && $val["TType"] == "O" && $val["TType2"] == "Free Distribution"){
					$TotalFreeAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "O" && $val["TType2"] == "Free Distribution"){
					$TotalFreeAmtPre += $val["TotalAmt"];
					}elseif($val["FY"] == $fy && $val["TType"] == "X"){
					$TotalFreeDistAmt += $val["TotalAmt"];
					}else if($val["FY"] == $last_fy && $val["TType"] == "X"){
					$TotalFreeDistAmtPre += $val["TotalAmt"];
				}
			}
			$InventoryAmts->PurchCurrentYear = $TotalPurchAmt;
			$InventoryAmts->PurchPriviousYear = $TotalPurchAmtPre;
			$InventoryAmts->IssueCurrentYear = $TotalIssueAmt;
			$InventoryAmts->IssuePriviousYear = $TotalIssueAmtPre;
			$InventoryAmts->PrdCurrentYear = $TotalPrdAmt;
			$InventoryAmts->PrdPriviousYear = $TotalPrdAmtPre;
			$InventoryAmts->SaleCurrentYear = $TotalSaleAmt;
			$InventoryAmts->SalePriviousYear = $TotalSaleAmtPre;
			$InventoryAmts->FrtRtnCurrentYear = $TotalFrtRtnSaleAmt;
			$InventoryAmts->FrtRtnPriviousYear = $TotalFrtRtnSaleAmtPre;
			$InventoryAmts->DFrtRtnCurrentYear = $TotalDFrtRtnSaleAmt;
			$InventoryAmts->DFrtRtnPriviousYear = $TotalDFrtRtnSaleAmtPre;
			$InventoryAmts->FreeCurrentYear = $TotalFreeAmt;
			$InventoryAmts->FreePriviousYear = $TotalFreeAmtPre;
			$InventoryAmts->FreeDistCurrentYear = $TotalFreeDistAmt;
			$InventoryAmts->FreeDistPriviousYear = $TotalFreeDistAmtPre;
			return $InventoryAmts;	
		}
		//==================== Calculate Closing Inventory Amt =========================
		public function GetClosingInventoryAmt($fromdate = "",$todate = "")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			$ItemList = array();
			$ItemListKeyVal = array();
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			// Opening Qty Current year 
			$this->db->select('SUM(tblstockmaster.OQty) AS OQty,tblstockmaster.ItemID,tblstockmaster.FY,tblitems.MainGrpID');
			$this->db->join('tblitems', 'tblitems.item_code = tblstockmaster.ItemID AND tblitems.PlantID = tblstockmaster.PlantID');
			$this->db->where_in(db_prefix() . 'stockmaster.FY', $year);
			$this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);
			$this->db->group_by('tblstockmaster.ItemID,tblstockmaster.FY');
			$ItemWiseOpnInventory =  $this->db->get(db_prefix() . 'stockmaster')->result_array();
			foreach($ItemWiseOpnInventory as $val){
				if($val["OQty"] > 0){
					array_push($ItemList,$val["ItemID"]);
					array_push($ItemListKeyVal,["ItemID"=>$val["ItemID"],"MainGrpID"=>$val["MainGrpID"]]);
				}
			}
			// Current year Transaction
			$this->db->select('SUM(tblhistory.BilledQty) AS TotalQty,tblhistory.ItemID,tblhistory.FY,TType,TType2,tblitems.MainGrpID');
			$this->db->join('tblitems', 'tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID');
			$this->db->where_in(db_prefix() . 'history.FY', $year);
			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'history.BillID IS NOT NULL');
			$this->db->where(db_prefix() . 'history.TransID IS NOT NULL');
			$this->db->where( db_prefix() . 'history.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblhistory.TType,tblhistory.TType2,tblhistory.FY,tblhistory.ItemID');
			$TransactionData =  $this->db->get(db_prefix() . 'history')->result_array();
			
			foreach($TransactionData as $val){
				array_push($ItemList,$val["ItemID"]);
				array_push($ItemListKeyVal,["ItemID"=>$val["ItemID"],"MainGrpID"=>$val["MainGrpID"]]);
			}
			$ItemList = array_unique($ItemList, SORT_REGULAR);
			$ItemListKeyValNew = array_map("unserialize", array_unique(array_map("serialize", $ItemListKeyVal)));
			$i = 0;
			foreach($ItemListKeyValNew as $kitem=>$ItemID){
				$OpnQty = 0;$TotalPurchQty = 0;$TotalIssueQty = 0;$TotalPrdQty = 0;$TotalSaleQty = 0;$TotalFrtRtnSaleQty = 0;$TotalDFrtRtnSaleQty = 0;$TotalFreeQty = 0;
				$TotalFreeDistQty = 0;$BalQty = 0;
				$OpnQtyPre = 0;$TotalPurchQtyPre = 0;$TotalIssueQtyPre = 0;$TotalPrdQtyPre = 0;$TotalSaleQtyPre = 0;$TotalFrtRtnSaleQtyPre = 0;$TotalDFrtRtnSaleQtyPre = 0;$TotalFreeQtyPre = 0;
				$TotalFreeDistQtyPre = 0;$BalQtyPre = 0;
				foreach($ItemWiseOpnInventory as $kOpn =>$vOpn){
					if($ItemID["ItemID"] == $vOpn["ItemID"] && $vOpn["FY"] == $fy){
						$OpnQty = $vOpn["OQty"];
						}elseif($ItemID["ItemID"] == $vOpn["ItemID"] && $vOpn["FY"] == $last_fy){
						$OpnQtyPre = $vOpn["OQty"];
					}
				}
				foreach($TransactionData as $key=>$val){
					if($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "P" && $val["TType2"] == "Purchase"){
						$TotalPurchQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "A" && $val["TType2"] == "Issue"){
						$TotalIssueQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "B" && $val["TType2"] == "Production"){
						$TotalPrdQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "O" && $val["TType2"] == "Order"){
						$TotalSaleQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "R" && $val["TType2"] == "Fresh"){
						$TotalFrtRtnSaleQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "R" && $val["TType2"] == "Damage"){
						$TotalDFrtRtnSaleQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "O" && $val["TType2"] == "Free Distribution"){
						$TotalFreeQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $fy && $val["TType"] == "X"){
						$TotalFreeDistQty += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "P" && $val["TType2"] == "Purchase"){
						$TotalPurchQtyPre += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "A" && $val["TType2"] == "Issue"){
						$TotalIssueQtyPre += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "B" && $val["TType2"] == "Production"){
						$TotalPrdQtyPre += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "O" && $val["TType2"] == "Order"){
						$TotalSaleQtyPre += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "R" && $val["TType2"] == "Fresh"){
						$TotalFrtRtnSaleQtyPre += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "R" && $val["TType2"] == "Damage"){
						$TotalDFrtRtnSaleQtyPre += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "O" && $val["TType2"] == "Free Distribution"){
						$TotalFreeQtyPre += $val["TotalQty"];
						}elseif($ItemID["ItemID"] == $val["ItemID"] && $val["FY"] == $last_fy && $val["TType"] == "X"){
						$TotalFreeDistQtyPre += $val["TotalQty"];
					}
				}
				$BalQty = $OpnQty + $TotalPurchQty - $TotalIssueQty + $TotalPrdQty - $TotalSaleQty + $TotalFrtRtnSaleQty - $TotalFreeQty - $TotalFreeDistQty;
				$BalQtyPre = $OpnQtyPre + $TotalPurchQtyPre - $TotalIssueQtyPre + $TotalPrdQtyPre - $TotalSaleQtyPre + $TotalFrtRtnSaleQtyPre - $TotalFreeQtyPre - $TotalFreeDistQtyPre;
				$ItemListKeyValNew[$kitem]["BalanceQty"] = $BalQty;
				$ItemListKeyValNew[$kitem]["BalanceQtyPre"] = $BalQtyPre;
				$i++;
			}
			$RMRateData = $this->GetRMLastPurchaseRate($ItemList,$todate);
			$FGRateData = $this->GetFGRateItemWise($ItemList,$todate);
			
			$i = 0;
			$TotalClsAmt = 0;
			$TotalClsAmtPre = 0;
			
			$FGTotalClsAmt = 0;
			$FGTotalClsAmtPre = 0;
			$RMTotalClsAmt = 0;
			$RMTotalClsAmtPre = 0;
			foreach($ItemListKeyValNew as $key=>$val){
				$ItemAmt = 0;
				$ItemAmtPre = 0;
				$BasicRate = 1;
				if($val["MainGrpID"] == "1"){
					foreach($FGRateData as $ratekey=>$rateval){
						if($val["ItemID"] == $rateval["item_id"] && $BasicRate == "0"){
							$BasicRate = $rateval["assigned_rate"];
						}
					}
					if($val["BalanceQty"] > 0){
						$ItemAmt = $val["BalanceQty"] * $BasicRate;
						$FGTotalClsAmt += $ItemAmt;
						$TotalClsAmt += $ItemAmt;
					}
					if($val["BalanceQtyPre"] > 0){
						$ItemAmtPre = $val["BalanceQtyPre"] * $BasicRate;
						$FGTotalClsAmtPre += $ItemAmtPre;
						$TotalClsAmtPre += $ItemAmtPre;
					}
					}else{
					foreach($RMRateData as $ratekey1=>$rateval1){
						if($val["ItemID"] == $rateval1["ItemID"] && $BasicRate == "0"){
							$BasicRate = $rateval1["PurchRate"];
						}
					}
					if($val["BalanceQty"] > 0){
						$ItemAmt = $val["BalanceQty"] * $BasicRate;
						$RMTotalClsAmt += $ItemAmt;
						$TotalClsAmt += $ItemAmt;
					}
					if($val["BalanceQtyPre"] > 0){
						$ItemAmtPre = $val["BalanceQtyPre"] * $BasicRate;
						$RMTotalClsAmtPre += $ItemAmtPre;
						$TotalClsAmtPre += $ItemAmtPre;
					}
				}
				
				$ItemListKeyValNew[$key]["rate"] = $BasicRate;
				$ItemListKeyValNew[$key]["BalanceQtyAmt"] = $ItemAmt;
				$ItemListKeyValNew[$key]["BalanceQtyAmtPre"] = $ItemAmtPre;
				$i++;
			}
			$ClsInventoryAmts->CurrentYear = $TotalClsAmt;
			$ClsInventoryAmts->PriviousYear = $TotalClsAmtPre;
			$ClsInventoryAmts->FGCurrentYear = $FGTotalClsAmt;
			$ClsInventoryAmts->FGPriviousYear = $FGTotalClsAmtPre;
			$ClsInventoryAmts->RMCurrentYear = $RMTotalClsAmt;
			$ClsInventoryAmts->RMPriviousYear = $RMTotalClsAmtPre;
			return $ClsInventoryAmts;	
		}
		//======================== Direct Expense ======================================
		public function GetDirectExpenses($fromdate ="",$todate ="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,clients.company,
			tblclients.AccountID,tblclients.SubActGroupID1,tblaccountgroupssub1.SubActGroupName As ActGrp1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.ActGroupID', '10010');
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
			$DirectExpData =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			$ActGroup1List = array();
			$ActGroup2List = array();
			$AccountList = array();
			foreach($DirectExpData as $DEKey=>$DeVal){
				$new1 = array(
				"AccountID"=>$DeVal["SubActGroupID1"],
				"AccountName"=>$DeVal["ActGrp1"]
				);
				array_push($ActGroup1List,$new1);
				$new2 = array(
				"AccountID1"=>$DeVal["SubActGroupID1"],
				"AccountID"=>$DeVal["SubActGroupID"],
				"AccountName"=>$DeVal["SubActGroupName"]
				);
				array_push($ActGroup2List,$new2);
				
				$new = array(
				"AccountID2"=>$DeVal["SubActGroupID"],
				"AccountID"=>$DeVal["AccountID"],
				"AccountName"=>$DeVal["company"]
				);
				array_push($AccountList,$new);
			}
			$ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
			$ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
			$AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
			$i = 0;
			$nestedData = [];
			$TotalExpense = 0;
			$TotalExpensePre = 0;
			foreach($ActGroup1UniqueList as $ActGrp1){
				$Group1Data = [
				'Group1Name' => $ActGrp1['AccountName'],
				'Group1ID' => $ActGrp1['AccountID'],
				];
				$ClsBalGroup1 = 0;
				$ClsBalGroup1Pre = 0;
				foreach($ActGroup2UniqueList as $val2){
					$ClsBalGroup2 = 0;
					$ClsBalGroup2Pre = 0;
					if($ActGrp1["AccountID"] == $val2["AccountID1"]){
						$Group2Data = [
						'SubGroupName' => $val2['AccountName'],
						'SubActGroupID' => $val2['AccountID'],
						];
						foreach($AccountUniqueList as $ActList){
							if($ActList["AccountID2"]==$val2['AccountID']){
								$ClsBalAccountWise = 0;
								$ClsBalAccountWisePre = 0;
								$Act_opn = 0;
								$ActCr = 0;
								$ActDr = 0;
								$Act_opnPre = 0;
								$ActCrPre = 0;
								$ActDrPre = 0;
								foreach($DirectExpData as $key=>$val){
									if($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCr += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDr += $val["SumAmt"];
									}
									if($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCrPre += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDrPre += $val["SumAmt"];
									}
								}
								$ClsBalAccountWise =  $ActDr - $ActCr;
								$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
								
								$ClsBalGroup2 += $ClsBalAccountWise;
								$ClsBalGroup2Pre += $ClsBalAccountWisePre;
								$AccountData = [
								'AccountName' => $ActList['AccountName'],
								'AccountID' => $ActList['AccountID'],
								'AccountClsBal' =>$ClsBalAccountWise,
								'AccountClsBalPre' =>$ClsBalAccountWisePre,
								];
								$Group2Data['Accounts'][] = $AccountData;
							}
						}
						$Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
						$Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
						$ClsBalGroup1 += $ClsBalGroup2;
						$ClsBalGroup1Pre += $ClsBalGroup2Pre;
						$Group1Data['SubGroups2'][] = $Group2Data; 
					}
				}
				$TotalExpense += abs($ClsBalGroup1);
				$TotalExpensePre += abs($ClsBalGroup1Pre);
				$Group1Data['Group1ClsBal'] = abs($ClsBalGroup1);
				$Group1Data['Group1ClsBalPre'] = abs($ClsBalGroup1Pre);
				$nestedData[] = $Group1Data;
				$i++;
			}
			$DirectExpense->nestedData = $nestedData;
			$DirectExpense->CurrentYear = $TotalExpense;
			$DirectExpense->PriviousYear = $TotalExpensePre;
			return $DirectExpense;
		}
		//============== Employee Benefits =============================================
		public function GetEMPBen($fromdate ="",$todate ="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,tblclients.company,
			tblclients.AccountID,tblclients.SubActGroupID1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.SubActGroupID1', '100025');
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
			$EmpBenTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			$ActGroup2List = array();
			$AccountList = array();
			foreach($EmpBenTrans as $DEKey=>$DeVal){
				$new2 = array(
				"AccountID1"=>$DeVal["SubActGroupID1"],
				"AccountID"=>$DeVal["SubActGroupID"],
				"AccountName"=>$DeVal["SubActGroupName"]
				);
				array_push($ActGroup2List,$new2);
				
				$new = array(
				"AccountID2"=>$DeVal["SubActGroupID"],
				"AccountID"=>$DeVal["AccountID"],
				"AccountName"=>$DeVal["company"]
				);
				array_push($AccountList,$new);
			}
			$ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
			$AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
			
			$i = 0;
			$nestedData = [];
			$TotalExpense = 0;
			$TotalExpensePre = 0;
			
			foreach($ActGroup2UniqueList as $val2){
				$ClsBalGroup2 = 0;
				$ClsBalGroup2Pre = 0;
				if($ActGrp1["AccountID"] == $val2["AccountID2"]){
					$Group2Data = [
					'SubGroupName' => $val2['AccountName'],
					'SubActGroupID' => $val2['AccountID'],
					];
					foreach($AccountUniqueList as $ActList){
						if($ActList["AccountID2"]==$val2['AccountID']){
							$ClsBalAccountWise = 0;
							$ClsBalAccountWisePre = 0;
							$Act_opn = 0;
							$ActCr = 0;
							$ActDr = 0;
							$Act_opnPre = 0;
							$ActCrPre = 0;
							$ActDrPre = 0;
							foreach($EmpBenTrans as $key=>$val){
								if($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActCr += $val["SumAmt"];
									}else if($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActDr += $val["SumAmt"];
								}
								if($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActCrPre += $val["SumAmt"];
									}else if($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActDrPre += $val["SumAmt"];
								}
							}
							$ClsBalAccountWise =  $ActDr - $ActCr;
							$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
							
							$ClsBalGroup2 += $ClsBalAccountWise;
							$ClsBalGroup2Pre += $ClsBalAccountWisePre;
							$AccountData = [
							'AccountName' => $ActList['AccountName'],
							'AccountID' => $ActList['AccountID'], 
							'AccountClsBal' =>$ClsBalAccountWise,
							'AccountClsBalPre' =>$ClsBalAccountWisePre,
							];
							$Group2Data['Accounts'][] = $AccountData;
						}
					}
					$Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
					$Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
				}
				$TotalExpense += abs($ClsBalGroup2);
				$TotalExpensePre += abs($ClsBalGroup2Pre);
				$nestedData[] = $Group2Data;
				$i++;
			}
			
			$EmpBenExpense->nestedData = $nestedData;
			$EmpBenExpense->CurrentYear = $TotalExpense;
			$EmpBenExpense->PriviousYear = $TotalExpensePre;
			return $EmpBenExpense;
			
		}
		//========================= Get Finance Cost ===================================
		public function GetFinanceCostData($fromdate ="",$todate ="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,tblclients.company,
			tblclients.AccountID,tblclients.SubActGroupID1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.SubActGroupID1', '100029');
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
			$FinCostTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			$ActGroup2List = array();
			$AccountList = array();
			foreach($EmpBenTrans as $DEKey=>$DeVal){
				$new2 = array(
				"AccountID1"=>$DeVal["SubActGroupID1"],
				"AccountID"=>$DeVal["SubActGroupID"],
				"AccountName"=>$DeVal["SubActGroupName"]
				);
				array_push($ActGroup2List,$new2);
				
				$new = array(
				"AccountID2"=>$DeVal["SubActGroupID"],
				"AccountID"=>$DeVal["AccountID"],
				"AccountName"=>$DeVal["company"]
				);
				array_push($AccountList,$new);
			}
			$ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
			$AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
			
			$i = 0;
			$nestedData = [];
			$TotalfinCost = 0;
			$TotalfinCostPre = 0;
			
			foreach($ActGroup2UniqueList as $val2){
				$ClsBalGroup2 = 0;
				$ClsBalGroup2Pre = 0;
				if($ActGrp1["AccountID"] == $val2["AccountID2"]){
					$Group2Data = [
					'SubGroupName' => $val2['AccountName'],
					'SubActGroupID' => $val2['AccountID'],
					];
					foreach($AccountUniqueList as $ActList){
						if($ActList["AccountID2"]==$val2['AccountID']){
							$ClsBalAccountWise = 0;
							$ClsBalAccountWisePre = 0;
							$Act_opn = 0;
							$ActCr = 0;
							$ActDr = 0;
							$Act_opnPre = 0;
							$ActCrPre = 0;
							$ActDrPre = 0;
							foreach($FinCostTrans as $key=>$val){
								if($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActCr += $val["SumAmt"];
									}else if($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActDr += $val["SumAmt"];
								}
								if($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActCrPre += $val["SumAmt"];
									}else if($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActDrPre += $val["SumAmt"];
								}
							}
							$ClsBalAccountWise =  $ActDr - $ActCr;
							$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
							
							$ClsBalGroup2 += $ClsBalAccountWise;
							$ClsBalGroup2Pre += $ClsBalAccountWisePre;
							$AccountData = [
							'AccountName' => $ActList['AccountName'],
							'AccountID' => $ActList['AccountID'], 
							'AccountClsBal' =>$ClsBalAccountWise,
							'AccountClsBalPre' =>$ClsBalAccountWisePre,
							];
							$Group2Data['Accounts'][] = $AccountData;
						}
					}
					$Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
					$Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
				}
				$TotalfinCost += abs($ClsBalGroup2);
				$TotalfinCostPre += abs($ClsBalGroup2Pre);
				$nestedData[] = $Group2Data;
				$i++;
			}
			
			$FinCostExpense->nestedData = $nestedData;
			$FinCostExpense->CurrentYear = $TotalfinCost;
			$FinCostExpense->PriviousYear = $TotalfinCostPre;
			return $FinCostExpense;
			
		}
		//===================== DEPRECIATION AND AMORTIZATION (PLANT) ==================
		public function GetDeprecAmortData($fromdate ="",$todate ="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,tblclients.company,
			tblclients.AccountID,tblclients.SubActGroupID1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.SubActGroupID1', '100007');
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
			$DepreAmortTrans =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			$ActGroup2List = array();
			$AccountList = array();
			foreach($EmpBenTrans as $DEKey=>$DeVal){
				$new2 = array(
				"AccountID1"=>$DeVal["SubActGroupID1"],
				"AccountID"=>$DeVal["SubActGroupID"],
				"AccountName"=>$DeVal["SubActGroupName"]
				);
				array_push($ActGroup2List,$new2);
				
				$new = array(
				"AccountID2"=>$DeVal["SubActGroupID"],
				"AccountID"=>$DeVal["AccountID"],
				"AccountName"=>$DeVal["company"]
				);
				array_push($AccountList,$new);
			}
			$ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
			$AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
			
			$i = 0;
			$nestedData = [];
			$TotalDeprAmort = 0;
			$TotalDeprAmortPre = 0;
			
			foreach($ActGroup2UniqueList as $val2){
				$ClsBalGroup2 = 0;
				$ClsBalGroup2Pre = 0;
				if($ActGrp1["AccountID"] == $val2["AccountID2"]){
					$Group2Data = [
					'SubGroupName' => $val2['AccountName'],
					'SubActGroupID' => $val2['AccountID'],
					];
					foreach($AccountUniqueList as $ActList){
						if($ActList["AccountID2"]==$val2['AccountID']){
							$ClsBalAccountWise = 0;
							$ClsBalAccountWisePre = 0;
							$Act_opn = 0;
							$ActCr = 0;
							$ActDr = 0;
							$Act_opnPre = 0;
							$ActCrPre = 0;
							$ActDrPre = 0;
							foreach($DepreAmortTrans as $key=>$val){
								if($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActCr += $val["SumAmt"];
									}else if($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActDr += $val["SumAmt"];
								}
								if($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActCrPre += $val["SumAmt"];
									}else if($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
									$ActDrPre += $val["SumAmt"];
								}
							}
							$ClsBalAccountWise =  $ActDr - $ActCr;
							$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
							
							$ClsBalGroup2 += $ClsBalAccountWise;
							$ClsBalGroup2Pre += $ClsBalAccountWisePre;
							$AccountData = [
							'AccountName' => $ActList['AccountName'],
							'AccountID' => $ActList['AccountID'], 
							'AccountClsBal' =>$ClsBalAccountWise,
							'AccountClsBalPre' =>$ClsBalAccountWisePre,
							];
							$Group2Data['Accounts'][] = $AccountData;
						}
					}
					$Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
					$Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
				}
				$TotalDeprAmort += abs($ClsBalGroup2);
				$TotalDeprAmortPre += abs($ClsBalGroup2Pre);
				$nestedData[] = $Group2Data;
				$i++;
			}
			
			$DeprAmortExpense->nestedData = $nestedData;
			$DeprAmortExpense->CurrentYear = $TotalDeprAmort;
			$DeprAmortExpense->PriviousYear = $TotalDeprAmortPre;
			return $DeprAmortExpense;
			
		}
		//=========================== Indirect Expenses ================================
		public function GetOtherExpensesData($fromdate="",$todate="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			// Get All Indirect Expenses Group1
			
			// 100007 = DEPRECIATION AND AMORTIZATION (PLANT)
			// 100029 = FINANCE COST
			// 100025 - SALARIES & STAFF WELFARE (OFFICE)
			$ActSubGroup1 = array("100007","100029","100025");
			
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,clients.company,
			tblclients.AccountID,tblclients.SubActGroupID1,tblaccountgroupssub1.SubActGroupName As ActGrp1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'clients.ActGroupID', '10018');
			$this->db->where_not_in(db_prefix() . 'accountgroupssub1.SubActGroupID1', $ActSubGroup1);
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
			$InDirectExpData =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			$ActGroup1List = array();
			$ActGroup2List = array();
			$AccountList = array();
			foreach($InDirectExpData as $DEKey=>$DeVal){
				$new1 = array(
				"AccountID"=>$DeVal["SubActGroupID1"],
				"AccountName"=>$DeVal["ActGrp1"]
				);
				array_push($ActGroup1List,$new1);
				$new2 = array(
				"AccountID1"=>$DeVal["SubActGroupID1"],
				"AccountID"=>$DeVal["SubActGroupID"],
				"AccountName"=>$DeVal["SubActGroupName"]
				);
				array_push($ActGroup2List,$new2);
				
				$new = array(
				"AccountID2"=>$DeVal["SubActGroupID"],
				"AccountID"=>$DeVal["AccountID"],
				"AccountName"=>$DeVal["company"]
				);
				array_push($AccountList,$new);
			}
			$ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
			$ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
			$AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
			$i = 0;
			$nestedData = [];
			$TotalExpense = 0;
			$TotalExpensePre = 0;
			foreach($ActGroup1UniqueList as $ActGrp1){
				$Group1Data = [
				'Group1Name' => $ActGrp1['AccountName'],
				'Group1ID' => $ActGrp1['AccountID'],
				];
				$ClsBalGroup1 = 0;
				$ClsBalGroup1Pre = 0;
				foreach($ActGroup2UniqueList as $val2){
					$ClsBalGroup2 = 0;
					$ClsBalGroup2Pre = 0;
					if($ActGrp1["AccountID"] == $val2["AccountID1"]){
						$Group2Data = [
						'SubGroupName' => $val2['AccountName'],
						'SubActGroupID' => $val2['AccountID'],
						];
						foreach($AccountUniqueList as $ActList){
							if($ActList["AccountID2"]==$val2['AccountID']){
								$ClsBalAccountWise = 0;
								$ClsBalAccountWisePre = 0;
								$Act_opn = 0;
								$ActCr = 0;
								$ActDr = 0;
								$Act_opnPre = 0;
								$ActCrPre = 0;
								$ActDrPre = 0;
								foreach($InDirectExpData as $key=>$val){
									if($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCr += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDr += $val["SumAmt"];
									}
									if($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCrPre += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDrPre += $val["SumAmt"];
									}
								}
								$ClsBalAccountWise =  $ActDr - $ActCr;
								$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
								
								$ClsBalGroup2 += $ClsBalAccountWise;
								$ClsBalGroup2Pre += $ClsBalAccountWisePre;
								$AccountData = [
								'AccountName' => $ActList['AccountName'],
								'AccountID' => $ActList['AccountID'],
								'AccountClsBal' =>$ClsBalAccountWise,
								'AccountClsBalPre' =>$ClsBalAccountWisePre,
								];
								$Group2Data['Accounts'][] = $AccountData;
							}
						}
						$Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
						$Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
						$ClsBalGroup1 += $ClsBalGroup2;
						$ClsBalGroup1Pre += $ClsBalGroup2Pre;
						$Group1Data['SubGroups2'][] = $Group2Data; 
					}
				}
				$TotalExpense += abs($ClsBalGroup1);
				$TotalExpensePre += abs($ClsBalGroup1Pre);
				$Group1Data['Group1ClsBal'] = abs($ClsBalGroup1);
				$Group1Data['Group1ClsBalPre'] = abs($ClsBalGroup1Pre);
				$nestedData[] = $Group1Data;
				$i++;
			}
			$InDirectExpense->nestedData = $nestedData;
			$InDirectExpense->CurrentYear = $TotalExpense;
			$InDirectExpense->PriviousYear = $TotalExpensePre;
			return $InDirectExpense;
		}
		
		//=========================== Tax Expense ======================================
		public function GetTaxExpense($fromdate="",$todate="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$last_fy = $fy - 1;
			$year = array($fy,$last_fy);
			// 100063 = TAX EXPENSES
			$ActSubGroup1 = array("100063"); // Group 1
			if(empty($fromdate)){
				$from_date = '20'.$fy.'-04-01 00:00:00';
				}else{
				$from_date = $fromdate;	
			}
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SumAmt,tblaccountledger.TType,tblaccountledger.FY,clients.company,
			tblclients.AccountID,tblclients.SubActGroupID1,tblaccountgroupssub1.SubActGroupName As ActGrp1,tblclients.SubActGroupID,tblaccountgroupssub.SubActGroupName');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where_not_in(db_prefix() . 'accountgroupssub1.SubActGroupID1', $ActSubGroup1);
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblaccountledger.FY,tblaccountledger.AccountID');
			$TaxExpenseData =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			$ActGroup1List = array();
			$ActGroup2List = array();
			$AccountList = array();
			foreach($TaxExpenseData as $DEKey=>$DeVal){
				$new1 = array(
				"AccountID"=>$DeVal["SubActGroupID1"],
				"AccountName"=>$DeVal["ActGrp1"]
				);
				array_push($ActGroup1List,$new1);
				$new2 = array(
				"AccountID1"=>$DeVal["SubActGroupID1"],
				"AccountID"=>$DeVal["SubActGroupID"],
				"AccountName"=>$DeVal["SubActGroupName"]
				);
				array_push($ActGroup2List,$new2);
				
				$new = array(
				"AccountID2"=>$DeVal["SubActGroupID"],
				"AccountID"=>$DeVal["AccountID"],
				"AccountName"=>$DeVal["company"]
				);
				array_push($AccountList,$new);
			}
			$ActGroup1UniqueList = array_unique($ActGroup1List,SORT_REGULAR);
			$ActGroup2UniqueList = array_unique($ActGroup2List,SORT_REGULAR);
			$AccountUniqueList = array_unique($AccountList,SORT_REGULAR);
			$i = 0;
			$nestedData = [];
			$TotalExpense = 0;
			$TotalExpensePre = 0;
			foreach($ActGroup1UniqueList as $ActGrp1){
				$Group1Data = [
				'Group1Name' => $ActGrp1['AccountName'],
				'Group1ID' => $ActGrp1['AccountID'],
				];
				$ClsBalGroup1 = 0;
				$ClsBalGroup1Pre = 0;
				foreach($ActGroup2UniqueList as $val2){
					$ClsBalGroup2 = 0;
					$ClsBalGroup2Pre = 0;
					if($ActGrp1["AccountID"] == $val2["AccountID1"]){
						$Group2Data = [
						'SubGroupName' => $val2['AccountName'],
						'SubActGroupID' => $val2['AccountID'],
						];
						foreach($AccountUniqueList as $ActList){
							if($ActList["AccountID2"]==$val2['AccountID']){
								$ClsBalAccountWise = 0;
								$ClsBalAccountWisePre = 0;
								$Act_opn = 0;
								$ActCr = 0;
								$ActDr = 0;
								$Act_opnPre = 0;
								$ActCrPre = 0;
								$ActDrPre = 0;
								foreach($TaxExpenseData as $key=>$val){
									if($val["TType"] == "C" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCr += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDr += $val["SumAmt"];
									}
									if($val["TType"] == "C" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActCrPre += $val["SumAmt"];
										}else if($val["TType"] == "D" && $val["FY"] == $last_fy && $ActList["AccountID"] == $val["AccountID"]){
										$ActDrPre += $val["SumAmt"];
									}
								}
								$ClsBalAccountWise =  $ActDr - $ActCr;
								$ClsBalAccountWisePre = $ActDrPre - $ActCrPre;
								
								$ClsBalGroup2 += $ClsBalAccountWise;
								$ClsBalGroup2Pre += $ClsBalAccountWisePre;
								$AccountData = [
								'AccountName' => $ActList['AccountName'],
								'AccountID' => $ActList['AccountID'],
								'AccountClsBal' =>$ClsBalAccountWise,
								'AccountClsBalPre' =>$ClsBalAccountWisePre,
								];
								$Group2Data['Accounts'][] = $AccountData;
							}
						}
						$Group2Data['Group2ClsBal'] = abs($ClsBalGroup2);
						$Group2Data['Group2ClsBalPre'] = abs($ClsBalGroup2Pre);
						$ClsBalGroup1 += $ClsBalGroup2;
						$ClsBalGroup1Pre += $ClsBalGroup2Pre;
						$Group1Data['SubGroups2'][] = $Group2Data; 
					}
				}
				$TotalExpense += abs($ClsBalGroup1);
				$TotalExpensePre += abs($ClsBalGroup1Pre);
				$Group1Data['Group1ClsBal'] = abs($ClsBalGroup1);
				$Group1Data['Group1ClsBalPre'] = abs($ClsBalGroup1Pre);
				$nestedData[] = $Group1Data;
				$i++;
			}
			$TaxExpenseData->nestedData = $nestedData;
			$TaxExpenseData->CurrentYear = $TotalExpense;
			$TaxExpenseData->PriviousYear = $TotalExpensePre;
			return $TaxExpenseData;
		}
		
		//============================ Balance Sheet Function ==========================
		//=================== Get Balance Sheet Main Group List ========================
		public function fetchAccountsData($filter_data = "")
		{
			$BalanceSheet_head['MainGroup'] = array("10000","10035","10025","10028","10010","10011","10018","10019");
			$this->db->select('tblaccountgroups.ActGroupName,tblaccountgroups.ActGroupID');
			if($filter_data["MainGroup"]){
				$this->db->where_in('tblaccountgroups.ActGroupID', $filter_data["MainGroup"]);
				}else{
				$this->db->where_in('tblaccountgroups.ActGroupID', $BalanceSheet_head["MainGroup"]);
			}
			return $this->db->get('tblaccountgroups')->result_array();
		}
		//================ Get Balance Sheet Account SUbGroup1 List ====================
		public function GetActSubGroup1ByMainGroup($BalanceSheet_head,$All = "")
		{
			$this->db->select('tblaccountgroupssub1.SubActGroupName,tblaccountgroupssub1.SubActGroupID1,tblaccountgroupssub1.ActGroupID');
			if($BalanceSheet_head["ActSubGroup1"] && $All == ""){
				$this->db->where_in('SubActGroupID1', $BalanceSheet_head["ActSubGroup1"]);
				}else{
				$this->db->where_in('ActGroupID', $BalanceSheet_head["MainGroup"]);
			}
			return $this->db->get('tblaccountgroupssub1')->result_array();
		}
		//================== Get Balance Sheet Account SUbGroup2 List ==================
		public function GetActSubGroup2ByMainGroup($BalanceSheet_head,$All = "")
		{
			$this->db->select('tblaccountgroupssub.SubActGroupName,tblaccountgroupssub.SubActGroupID,tblaccountgroupssub.SubActGroupID1');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');
			if($BalanceSheet_head["AccountSubGroupID2"] && $All == ""){
				$this->db->where_in('tblaccountgroupssub.SubActGroupID', $BalanceSheet_head["AccountSubGroupID2"]);
				}else if($BalanceSheet_head["ActSubGroup1"]){
				$this->db->where_in('tblaccountgroupssub.SubActGroupID1', $BalanceSheet_head["ActSubGroup1"]);
				}else{
				$this->db->where_in('tblaccountgroupssub1.ActGroupID', $BalanceSheet_head["MainGroup"]);
			}
			return $this->db->get('tblaccountgroupssub')->result_array();
		}
		//=================== Get Balance Sheet Account List ===========================
		public function GetAccountListByMainGroup($mainGroupID)
		{
			$this->db->select('tblclients.company,tblclients.AccountID,tblclients.SubActGroupID,tblaccountgroupssub1.ActGroupID');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');
			$this->db->where_in('tblaccountgroupssub1.ActGroupID', $mainGroupID["MainGroup"]);
			return $this->db->get('tblclients')->result_array();
		}
		//================ Get Balance Sheet Staff Account List ========================
		public function GetStaffList($mainGroupID)
		{
			$GICAccounts = array("GIC","GIC7","MAN");
			$this->db->select('tblstaff.firstname,tblstaff.lastname,tblstaff.AccountID,tblstaff.SubActGroupID,tblaccountgroupssub1.ActGroupID');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblstaff.SubActGroupID');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1');
			$this->db->where_in('tblaccountgroupssub1.ActGroupID', $mainGroupID["MainGroup"]);
			$this->db->where_not_in('tblstaff.AccountID', $GICAccounts);
			return $this->db->get('tblstaff')->result_array();
		}
		//================== Get Account Ledger Data ===================================
		public function GetLedgerData($BalanceSheet_head,$todate="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = '20'.$fy.'-04-01 00:00:00';
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblclients.AccountID,tblclients.SubActGroupID,tblclients.SubActGroupID1,tblclients.ActGroupID,tblaccountledger.FY');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');
			$this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
			$this->db->where('tblaccountledger.FY', $fy);
			$this->db->where('tblaccountledger.PlantID', $selected_company);
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblclients.AccountID');
			$CurrentYrLedger_data = $this->db->get('tblaccountledger')->result_array();
			$Ledger_data->Cur_yr_ledger = $CurrentYrLedger_data;
			// Privius year ledger
			$last_fy = $fy - 1;
			$this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblclients.AccountID,tblclients.SubActGroupID,tblclients.SubActGroupID1,tblclients.ActGroupID,tblaccountledger.FY');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID');
			$this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
			$this->db->where('tblaccountledger.FY', $last_fy);
			$this->db->where('tblaccountledger.PlantID', $selected_company);
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblclients.AccountID');
			$lastYrLedger_data = $this->db->get('tblaccountledger')->result_array();
			$Ledger_data->Last_yr_ledger = $lastYrLedger_data;
			return $Ledger_data;
		}
		//================== Get Staff Account Ledger Data =============================
		public function GetStaffLedgerData($BalanceSheet_head,$todate="")
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = '20'.$fy.'-04-01 00:00:00';
			if(empty($todate)){
				$to_date = date('Y-m-d H:i:s');	
				}else{
				$to_date = $todate;	
			}
			$this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblstaff.AccountID,tblstaff.SubActGroupID,tblstaff.SubActGroupID1,tblstaff.ActGroupID,tblaccountledger.FY');
			$this->db->join('tblstaff', 'tblstaff.AccountID = tblaccountledger.AccountID');
			$this->db->where('tblaccountledger.FY', $fy);
			$this->db->where('tblaccountledger.PlantID', $selected_company);
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblstaff.AccountID');
			$CurrentYrLedger_data = $this->db->get('tblaccountledger')->result_array();
			$Ledger_data->Cur_yr_ledger = $CurrentYrLedger_data;
			// Privius year ledger
			$last_fy = $fy - 1;
			$this->db->select('SUM(tblaccountledger.Amount) AS SUMAmt,tblaccountledger.TType,tblstaff.AccountID,tblaccountledger.FY');
			$this->db->join('tblstaff', 'tblstaff.AccountID = tblaccountledger.AccountID');
			$this->db->where('tblaccountledger.FY', $last_fy);
			$this->db->where('tblaccountledger.PlantID', $selected_company);
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('tblaccountledger.TType,tblstaff.AccountID');
			$lastYrLedger_data = $this->db->get('tblaccountledger')->result_array();
			$Ledger_data->Last_yr_ledger = $lastYrLedger_data;
			return $Ledger_data;
		}
		//============== Get Opn Balance For All Accounts ==============================
		public function GetOpnBalData($BalanceSheet_head)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			//$Ledger_data = array();
			$this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblaccountbalances.AccountID,tblaccountbalances.FY');
			//$this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');
			//$this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
			$this->db->where('tblaccountbalances.FY', $fy);
			$this->db->where('tblaccountbalances.PlantID', $selected_company);
			$this->db->group_by('tblaccountbalances.AccountID');
			$CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();
			$OpnBal_data->Cur_yr_OpnBal = $CurrentYrOpnBal;
			// Privius year ledger
			$last_fy = $fy - 1;
			$this->db->select('SUM(tblaccountbalances.BAL1) AS SUMAmt,tblaccountbalances.AccountID,tblaccountbalances.FY');
			/*$this->db->join('tblclients', 'tblclients.AccountID = tblaccountbalances.AccountID');
				$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
				$this->db->where_in('tblclients.ActGroupID', $BalanceSheet_head["MainGroup"]);
			*/$this->db->where('tblaccountbalances.FY', $last_fy);
			$this->db->where('tblaccountbalances.PlantID', $selected_company);
			$this->db->group_by('tblaccountbalances.AccountID');
			$CurrentYrOpnBal = $this->db->get('tblaccountbalances')->result_array();
			$OpnBal_data->Last_yr_OpnBal = $CurrentYrOpnBal;
			return $OpnBal_data;
		}
		//====================== Get Fixed Assets ======================================
		public function GetFixedAssetsLedger()
		{
			$this->db->select('SubActGroupID,AccountID,company');
			$this->db->where_in(db_prefix() . 'clients.SubActGroupID1', "100040");
			$FixedAssetsLedger =  $this->db->get(db_prefix() . 'clients')->result_array();
			return $FixedAssetsLedger;
		}
		public function get_account_types()
		{
			$account_types = hooks()->apply_filters('before_get_account_types', [
			[
			'id'             => 1,
			'name'           => _l('acc_accounts_receivable'),
			'order'          => 1,
			],
			[
			'id'             => 2,
			'name'           => _l('acc_current_assets'),
			'order'          => 2,
			],
			[
			'id'             => 3,
			'name'           => _l('acc_cash_and_cash_equivalents'),
			'order'          => 3,
			],
			[
			'id'             => 4,
			'name'           => _l('acc_fixed_assets'),
			'order'          => 4,
			],
			[
			'id'             => 5,
			'name'           => _l('acc_non_current_assets'),
			'order'          => 5,
			],
			[
			'id'             => 6,
			'name'           => _l('acc_accounts_payable'),
			'order'          => 6,
			],
			[
			'id'             => 7,
			'name'           => _l('acc_credit_card'),
			'order'          => 7,
			],
			[
			'id'             => 8,
			'name'           => _l('acc_current_liabilities'),
			'order'          => 8,
			],
			[
			'id'             => 9,
			'name'           => _l('acc_non_current_liabilities'),
			'order'          => 9,
			],
			[
			'id'             => 10,
			'name'           => _l('acc_owner_equity'),
			'order'          => 10,
			],
			[
			'id'             => 11,
			'name'           => _l('acc_income'),
			'order'          => 11,
			],
			[
			'id'             => 12,
			'name'           => _l('acc_other_income'),
			'order'          => 12,
			],
			[
			'id'             => 13,
			'name'           => _l('acc_cost_of_sales'),
			'order'          => 13,
			],
			[
			'id'             => 14,
			'name'           => _l('acc_expenses'),
			'order'          => 14,
			],
			[
			'id'             => 15,
			'name'           => _l('acc_other_expense'),
			'order'          => 15,
			],
			]);
			
			usort($account_types, function ($a, $b) {
				return $a['order'] - $b['order'];
			});
			
			return $account_types;
		}
		
		/**
			* get account type details
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_account_type_details()
		{
			$account_type_details = hooks()->apply_filters('before_get_account_type_details', [
			[
			'id'                => 1,
			'account_type_id'   => 1,
			'name'              => _l('acc_accounts_receivable'),
			'note'              => _l('acc_accounts_receivable_note'),
			'order'             => 1,
			],
			[
			'id'                => 2,
			'account_type_id'   => 2,
			'name'              => _l('acc_allowance_for_bad_debts'),
			'note'              => _l('acc_allowance_for_bad_debts_note'),
			'order'             => 2,
			],
			[
			'id'                => 3,
			'account_type_id'   => 2,
			'name'              => _l('acc_assets_available_for_sale'),
			'note'              => _l('acc_assets_available_for_sale_note'),
			'order'             => 3,
			],
			[
			'id'                => 4,
			'account_type_id'   => 2,
			'name'              => _l('acc_development_costs'),
			'note'              => _l('acc_development_costs_note'),
			'order'             => 4,
			],
			[
			'id'                => 141,
			'account_type_id'   => 2,
			'name'              => _l('acc_employee_cash_advances'),
			'note'              => _l('acc_employee_cash_advances_note'),
			'order'             => 5,
			],
			[
			'id'                => 5,
			'account_type_id'   => 2,
			'name'              => _l('acc_inventory'),
			'note'              => _l('acc_inventory_note'),
			'order'             => 5,
			],
			[
			'id'                => 6,
			'account_type_id'   => 2,
			'name'              => _l('acc_investments_other'),
			'note'              => _l('acc_investments_other_note'),
			'order'             => 6,
			],
			[
			'id'                => 7,
			'account_type_id'   => 2,
			'name'              => _l('acc_loans_to_officers'),
			'note'              => _l('acc_loans_to_officers_note'),
			'order'             => 7,
			],
			[
			'id'                => 8,
			'account_type_id'   => 2,
			'name'              => _l('acc_loans_to_others'),
			'note'              => _l('acc_loans_to_others_note'),
			'order'             => 8,
			],
			[
			'id'                => 9,
			'account_type_id'   => 2,
			'name'              => _l('acc_loans_to_shareholders'),
			'note'              => _l('acc_loans_to_shareholders_note'),
			'order'             => 9,
			],
			[
			'id'                => 10,
			'account_type_id'   => 2,
			'name'              => _l('acc_other_current_assets'),
			'note'              => _l('acc_other_current_assets_note'),
			'order'             => 10,
			],
			[
			'id'                => 11,
			'account_type_id'   => 2,
			'name'              => _l('acc_prepaid_expenses'),
			'note'              => _l('acc_prepaid_expenses_note'),
			'order'             => 11,
			],
			[
			'id'                => 12,
			'account_type_id'   => 2,
			'name'              => _l('acc_retainage'),
			'note'              => _l('acc_retainage_note'),
			'order'             => 12,
			],
			[
			'id'                => 13,
			'account_type_id'   => 2,
			'name'              => _l('acc_undeposited_funds'),
			'note'              => _l('acc_undeposited_funds_note'),
			'order'             => 13,
			],
			[
			'id'                => 14,
			'account_type_id'   => 3,
			'name'              => _l('acc_bank'),
			'note'              => _l('acc_bank_note'),
			'order'             => 14,
			],
			[
			'id'                => 15,
			'account_type_id'   => 3,
			'name'              => _l('acc_cash_and_cash_equivalents'),
			'note'              => _l('acc_cash_and_cash_equivalents_note'),
			'order'             => 15,
			],
			[
			'id'                => 16,
			'account_type_id'   => 3,
			'name'              => _l('acc_cash_on_hand'),
			'note'              => _l('acc_cash_on_hand_note'),
			'order'             => 16,
			],
			[
			'id'                => 17,
			'account_type_id'   => 3,
			'name'              => _l('acc_client_trust_account'),
			'note'              => _l('acc_client_trust_account_note'),
			'order'             => 17,
			],
			[
			'id'                => 18,
			'account_type_id'   => 3,
			'name'              => _l('acc_money_market'),
			'note'              => _l('acc_money_market_note'),
			'order'             => 18,
			],
			[
			'id'                => 19,
			'account_type_id'   => 3,
			'name'              => _l('acc_rents_held_in_trust'),
			'note'              => _l('acc_rents_held_in_trust_note'),
			'order'             => 19,
			],
			[
			'id'                => 20,
			'account_type_id'   => 3,
			'name'              => _l('acc_savings'),
			'note'              => _l('acc_savings_note'),
			'order'             => 20,
			],
			[
			'id'                => 21,
			'account_type_id'   => 4,
			'name'              => _l('acc_accumulated_depletion'),
			'note'              => _l('acc_accumulated_depletion_note'),
			'order'             => 21,
			],
			[
			'id'                => 22,
			'account_type_id'   => 4,
			'name'              => _l('acc_accumulated_depreciation_on_property_plant_and_equipment'),
			'note'              => _l('acc_accumulated_depreciation_on_property_plant_and_equipment_note'),
			'order'             => 22,
			],
			[
			'id'                => 23,
			'account_type_id'   => 4,
			'name'              => _l('acc_buildings'),
			'note'              => _l('acc_buildings_note'),
			'order'             => 23,
			],
			[
			'id'                => 24,
			'account_type_id'   => 4,
			'name'              => _l('acc_depletable_assets'),
			'note'              => _l('acc_depletable_assets_note'),
			'order'             => 24,
			],
			[
			'id'                => 25,
			'account_type_id'   => 4,
			'name'              => _l('acc_furniture_and_fixtures'),
			'note'              => _l('acc_furniture_and_fixtures_note'),
			'order'             => 25,
			],
			[
			'id'                => 26,
			'account_type_id'   => 4,
			'name'              => _l('acc_land'),
			'note'              => _l('acc_land_note'),
			'order'             => 26,
			],
			[
			'id'                => 27,
			'account_type_id'   => 4,
			'name'              => _l('acc_leasehold_improvements'),
			'note'              => _l('acc_leasehold_improvements_note'),
			'order'             => 27,
			],
			[
			'id'                => 28,
			'account_type_id'   => 4,
			'name'              => _l('acc_machinery_and_equipment'),
			'note'              => _l('acc_machinery_and_equipment_note'),
			'order'             => 28,
			],
			[
			'id'                => 29,
			'account_type_id'   => 4,
			'name'              => _l('acc_other_fixed_assets'),
			'note'              => _l('acc_other_fixed_assets_note'),
			'order'             => 29,
			],
			[
			'id'                => 30,
			'account_type_id'   => 4,
			'name'              => _l('acc_vehicles'),
			'note'              => _l('acc_vehicles_note'),
			'order'             => 30,
			],
			[
			'id'                => 31,
			'account_type_id'   => 5,
			'name'              => _l('acc_accumulated_amortisation_of_non_current_assets'),
			'note'              => _l('acc_accumulated_amortisation_of_non_current_assets_note'),
			'order'             => 31,
			],
			[
			'id'                => 32,
			'account_type_id'   => 5,
			'name'              => _l('acc_assets_held_for_sale'),
			'note'              => _l('acc_assets_held_for_sale_note'),
			'order'             => 32,
			],
			[
			'id'                => 33,
			'account_type_id'   => 5,
			'name'              => _l('acc_deferred_tax'),
			'note'              => _l('acc_deferred_tax_note'),
			'order'             => 33,
			],
			[
			'id'                => 34,
			'account_type_id'   => 5,
			'name'              => _l('acc_goodwill'),
			'note'              => _l('acc_goodwill_note'),
			'order'             => 34,
			],
			[
			'id'                => 35,
			'account_type_id'   => 5,
			'name'              => _l('acc_intangible_assets'),
			'note'              => _l('acc_intangible_assets_note'),
			'order'             => 35,
			],
			[
			'id'                => 36,
			'account_type_id'   => 5,
			'name'              => _l('acc_lease_buyout'),
			'note'              => _l('acc_lease_buyout_note'),
			'order'             => 36,
			],
			[
			'id'                => 37,
			'account_type_id'   => 5,
			'name'              => _l('acc_licences'),
			'note'              => _l('acc_licences_note'),
			'order'             => 37,
			],
			[
			'id'                => 38,
			'account_type_id'   => 5,
			'name'              => _l('acc_long_term_investments'),
			'note'              => _l('acc_long_term_investments_note'),
			'order'             => 38,
			],
			[
			'id'                => 39,
			'account_type_id'   => 5,
			'name'              => _l('acc_organisational_costs'),
			'note'              => _l('acc_organisational_costs_note'),
			'order'             => 39,
			],
			[
			'id'                => 40,
			'account_type_id'   => 5,
			'name'              => _l('acc_other_non_current_assets'),
			'note'              => _l('acc_other_non_current_assets_note'),
			'order'             => 40,
			],
			[
			'id'                => 41,
			'account_type_id'   => 5,
			'name'              => _l('acc_security_deposits'),
			'note'              => _l('acc_security_deposits_note'),
			'order'             => 41,
			],
			[
			'id'                => 42,
			'account_type_id'   => 6,
			'name'              => _l('acc_accounts_payable'),
			'note'              => _l('acc_accounts_payable_note'),
			'order'             => 42,
			],
			[
			'id'                => 43,
			'account_type_id'   => 7,
			'name'              => _l('acc_credit_card'),
			'note'              => _l('acc_credit_card_note'),
			'order'             => 43,
			],
			[
			'id'                => 44,
			'account_type_id'   => 8,
			'name'              => _l('acc_accrued_liabilities'),
			'note'              => _l('acc_accrued_liabilities_note'),
			'order'             => 44,
			],
			[
			'id'                => 45,
			'account_type_id'   => 8,
			'name'              => _l('acc_client_trust_accounts_liabilities'),
			'note'              => _l('acc_client_trust_accounts_liabilities_note'),
			'order'             => 45,
			],
			[
			'id'                => 46,
			'account_type_id'   => 8,
			'name'              => _l('acc_current_tax_liability'),
			'note'              => _l('acc_current_tax_liability_note'),
			'order'             => 46,
			],
			[
			'id'                => 47,
			'account_type_id'   => 8,
			'name'              => _l('acc_current_portion_of_obligations_under_finance_leases'),
			'note'              => _l('acc_current_portion_of_obligations_under_finance_leases_note'),
			'order'             => 47,
			],
			[
			'id'                => 48,
			'account_type_id'   => 8,
			'name'              => _l('acc_dividends_payable'),
			'note'              => _l('acc_dividends_payable_note'),
			'order'             => 48,
			],
			[
			'id'                => 50,
			'account_type_id'   => 8,
			'name'              => _l('acc_income_tax_payable'),
			'note'              => _l('acc_income_tax_payable_note'),
			'order'             => 50,
			],
			[
			'id'                => 51,
			'account_type_id'   => 8,
			'name'              => _l('acc_insurance_payable'),
			'note'              => _l('acc_insurance_payable_note'),
			'order'             => 51,
			],
			[
			'id'                => 52,
			'account_type_id'   => 8,
			'name'              => _l('acc_line_of_credit'),
			'note'              => _l('acc_line_of_credit_note'),
			'order'             => 52,
			],
			[
			'id'                => 53,
			'account_type_id'   => 8,
			'name'              => _l('acc_loan_payable'),
			'note'              => _l('acc_loan_payable_note'),
			'order'             => 53,
			],
			[
			'id'                => 54,
			'account_type_id'   => 8,
			'name'              => _l('acc_other_current_liabilities'),
			'note'              => _l('acc_other_current_liabilities_note'),
			'order'             => 54,
			],
			[
			'id'                => 55,
			'account_type_id'   => 8,
			'name'              => _l('acc_payroll_clearing'),
			'note'              => _l('acc_payroll_clearing_note'),
			'order'             => 55,
			],
			[
			'id'                => 56,
			'account_type_id'   => 8,
			'name'              => _l('acc_payroll_liabilities'),
			'note'              => _l('acc_payroll_liabilities_note'),
			'order'             => 56,
			],
			[
			'id'                => 58,
			'account_type_id'   => 8,
			'name'              => _l('acc_prepaid_expenses_payable'),
			'note'              => _l('acc_prepaid_expenses_payable_note'),
			'order'             => 58,
			],
			[
			'id'                => 59,
			'account_type_id'   => 8,
			'name'              => _l('acc_rents_in_trust_liability'),
			'note'              => _l('acc_rents_in_trust_liability_note'),
			'order'             => 59,
			],
			[
			'id'                => 60,
			'account_type_id'   => 8,
			'name'              => _l('acc_sales_and_service_tax_payable'),
			'note'              => _l('acc_sales_and_service_tax_payable_note'),
			'order'             => 60,
			],
			[
			'id'                => 61,
			'account_type_id'   => 9,
			'name'              => _l('acc_accrued_holiday_payable'),
			'note'              => _l('acc_accrued_holiday_payable_note'),
			'order'             => 61,
			],
			[
			'id'                => 62,
			'account_type_id'   => 9,
			'name'              => _l('acc_accrued_non_current_liabilities'),
			'note'              => _l('acc_accrued_non_current_liabilities_note'),
			'order'             => 62,
			],
			[
			'id'                => 63,
			'account_type_id'   => 9,
			'name'              => _l('acc_liabilities_related_to_assets_held_for_sale'),
			'note'              => _l('acc_liabilities_related_to_assets_held_for_sale_note'),
			'order'             => 63,
			],
			[
			'id'                => 64,
			'account_type_id'   => 9,
			'name'              => _l('acc_long_term_debt'),
			'note'              => _l('acc_long_term_debt_note'),
			'order'             => 64,
			],
			[
			'id'                => 65,
			'account_type_id'   => 9,
			'name'              => _l('acc_notes_payable'),
			'note'              => _l('acc_notes_payable_note'),
			'order'             => 65,
			],
			[
			'id'                => 66,
			'account_type_id'   => 9,
			'name'              => _l('acc_other_non_current_liabilities'),
			'note'              => _l('acc_other_non_current_liabilities_note'),
			'order'             => 66,
			],
			[
			'id'                => 67,
			'account_type_id'   => 9,
			'name'              => _l('acc_shareholder_potes_payable'),
			'note'              => _l('acc_shareholder_potes_payable_note'),
			'order'             => 67,
			],
			[
			'id'                => 68,
			'account_type_id'   => 10,
			'name'              => _l('acc_accumulated_adjustment'),
			'note'              => _l('acc_accumulated_adjustment_note'),
			'order'             => 68,
			],
			[
			'id'                => 69,
			'account_type_id'   => 10,
			'name'              => _l('acc_dividend_disbursed'),
			'note'              => _l('acc_dividend_disbursed_note'),
			'order'             => 69,
			],
			[
			'id'                => 70,
			'account_type_id'   => 10,
			'name'              => _l('acc_equity_in_earnings_of_subsidiaries'),
			'note'              => _l('acc_equity_in_earnings_of_subsidiaries_note'),
			'order'             => 70,
			],
			[
			'id'                => 71,
			'account_type_id'   => 10,
			'name'              => _l('acc_opening_balance_equity'),
			'note'              => _l('acc_opening_balance_equity_note'),
			'order'             => 71,
			],
			[
			'id'                => 72,
			'account_type_id'   => 10,
			'name'              => _l('acc_ordinary_shares'),
			'note'              => _l('acc_ordinary_shares_note'),
			'order'             => 72,
			],
			[
			'id'                => 73,
			'account_type_id'   => 10,
			'name'              => _l('acc_other_comprehensive_income'),
			'note'              => _l('acc_other_comprehensive_income_note'),
			'order'             => 73,
			],
			[
			'id'                => 74,
			'account_type_id'   => 10,
			'name'              => _l('acc_owner_equity'),
			'note'              => _l('acc_owner_equity_note'),
			'order'             => 74,
			],
			[
			'id'                => 75,
			'account_type_id'   => 10,
			'name'              => _l('acc_paid_in_capital_or_surplus'),
			'note'              => _l('acc_paid_in_capital_or_surplus_note'),
			'order'             => 75,
			],
			[
			'id'                => 76,
			'account_type_id'   => 10,
			'name'              => _l('acc_partner_contributions'),
			'note'              => _l('acc_partner_contributions_note'),
			'order'             => 76,
			],
			[
			'id'                => 77,
			'account_type_id'   => 10,
			'name'              => _l('acc_partner_distributions'),
			'note'              => _l('acc_partner_distributions_note'),
			'order'             => 77,
			],
			[
			'id'                => 78,
			'account_type_id'   => 10,
			'name'              => _l('acc_partner_equity'),
			'note'              => _l('acc_partner_equity_note'),
			'order'             => 78,
			],
			[
			'id'                => 79,
			'account_type_id'   => 10,
			'name'              => _l('acc_preferred_shares'),
			'note'              => _l('acc_preferred_shares_note'),
			'order'             => 79,
			],
			[
			'id'                => 80,
			'account_type_id'   => 10,
			'name'              => _l('acc_retained_earnings'),
			'note'              => _l('acc_retained_earnings_note'),
			'order'             => 80,
			],
			[
			'id'                => 81,
			'account_type_id'   => 10,
			'name'              => _l('acc_share_capital'),
			'note'              => _l('acc_share_capital_note'),
			'order'             => 81,
			],
			[
			'id'                => 82,
			'account_type_id'   => 10,
			'name'              => _l('acc_treasury_shares'),
			'note'              => _l('acc_treasury_shares_note'),
			'order'             => 82,
			],
			[
			'id'                => 83,
			'account_type_id'   => 11,
			'name'              => _l('acc_discounts_refunds_given'),
			'note'              => _l('acc_discounts_refunds_given_note'),
			'order'             => 83,
			],
			[
			'id'                => 84,
			'account_type_id'   => 11,
			'name'              => _l('acc_non_profit_income'),
			'note'              => _l('acc_non_profit_income_note'),
			'order'             => 84,
			],
			[
			'id'                => 85,
			'account_type_id'   => 11,
			'name'              => _l('acc_other_primary_income'),
			'note'              => _l('acc_other_primary_income_note'),
			'order'             => 85,
			],
			[
			'id'                => 86,
			'account_type_id'   => 11,
			'name'              => _l('acc_revenue_general'),
			'note'              => _l('acc_revenue_general_note'),
			'order'             => 86,
			],
			[
			'id'                => 87,
			'account_type_id'   => 11,
			'name'              => _l('acc_sales_retail'),
			'note'              => _l('acc_sales_retail_note'),
			'order'             => 87,
			],
			[
			'id'                => 88,
			'account_type_id'   => 11,
			'name'              => _l('acc_sales_wholesale'),
			'note'              => _l('acc_sales_wholesale_note'),
			'order'             => 88,
			],
			[
			'id'                => 89,
			'account_type_id'   => 11,
			'name'              => _l('acc_sales_of_product_income'),
			'note'              => _l('acc_sales_of_product_income_note'),
			'order'             => 89,
			],
			[
			'id'                => 90,
			'account_type_id'   => 11,
			'name'              => _l('acc_service_fee_income'),
			'note'              => _l('acc_service_fee_income_note'),
			'order'             => 90,
			],
			[
			'id'                => 91,
			'account_type_id'   => 11,
			'name'              => _l('acc_unapplied_cash_payment_income'),
			'note'              => _l('acc_unapplied_cash_payment_income_note'),
			'order'             => 91,
			],
			[
			'id'                => 92,
			'account_type_id'   => 12,
			'name'              => _l('acc_dividend_income'),
			'note'              => _l('acc_dividend_income_note'),
			'order'             => 92,
			],
			[
			'id'                => 93,
			'account_type_id'   => 12,
			'name'              => _l('acc_interest_earned'),
			'note'              => _l('acc_interest_earned_note'),
			'order'             => 93,
			],
			[
			'id'                => 94,
			'account_type_id'   => 12,
			'name'              => _l('acc_loss_on_disposal_of_assets'),
			'note'              => _l('acc_loss_on_disposal_of_assets_note'),
			'order'             => 94,
			],
			[
			'id'                => 95,
			'account_type_id'   => 12,
			'name'              => _l('acc_other_investment_income'),
			'note'              => _l('acc_other_investment_income_note'),
			'order'             => 95,
			],
			[
			'id'                => 96,
			'account_type_id'   => 12,
			'name'              => _l('acc_other_miscellaneous_income'),
			'note'              => _l('acc_other_miscellaneous_income_note'),
			'order'             => 96,
			],
			[
			'id'                => 97,
			'account_type_id'   => 12,
			'name'              => _l('acc_other_operating_income'),
			'note'              => _l('acc_other_operating_income_note'),
			'order'             => 97,
			],
			[
			'id'                => 98,
			'account_type_id'   => 12,
			'name'              => _l('acc_tax_exempt_interest'),
			'note'              => _l('acc_tax_exempt_interest_note'),
			'order'             => 98,
			],
			[
			'id'                => 99,
			'account_type_id'   => 12,
			'name'              => _l('acc_unrealised_loss_on_securities_net_of_tax'),
			'note'              => _l('acc_unrealised_loss_on_securities_net_of_tax_note'),
			'order'             => 99,
			],
			[
			'id'                => 100,
			'account_type_id'   => 13,
			'name'              => _l('acc_cost_of_labour_cos'),
			'note'              => _l('acc_cost_of_labour_cos_note'),
			'order'             => 100,
			],
			[
			'id'                => 101,
			'account_type_id'   => 13,
			'name'              => _l('acc_equipment_rental_cos'),
			'note'              => _l('acc_equipment_rental_cos_note'),
			'order'             => 101,
			],
			[
			'id'                => 102,
			'account_type_id'   => 13,
			'name'              => _l('acc_freight_and_delivery_cos'),
			'note'              => _l('acc_freight_and_delivery_cos_note'),
			'order'             => 102,
			],
			[
			'id'                => 103,
			'account_type_id'   => 13,
			'name'              => _l('acc_other_costs_of_sales_cos'),
			'note'              => _l('acc_other_costs_of_sales_cos_note'),
			'order'             => 103,
			],
			[
			'id'                => 104,
			'account_type_id'   => 13,
			'name'              => _l('acc_supplies_and_materials_cos'),
			'note'              => _l('acc_supplies_and_materials_cos_note'),
			'order'             => 104,
			],
			[
			'id'                => 105,
			'account_type_id'   => 14,
			'name'              => _l('acc_advertising_promotional'),
			'note'              => _l('acc_advertising_promotional_note'),
			'order'             => 105,
			],
			[
			'id'                => 106,
			'account_type_id'   => 14,
			'name'              => _l('acc_amortisation_expense'),
			'note'              => _l('acc_amortisation_expense_note'),
			'order'             => 106,
			],
			[
			'id'                => 107,
			'account_type_id'   => 14,
			'name'              => _l('acc_auto'),
			'note'              => _l('acc_auto_note'),
			'order'             => 107,
			],
			[
			'id'                => 108,
			'account_type_id'   => 14,
			'name'              => _l('acc_bad_debts'),
			'note'              => _l('acc_bad_debts_note'),
			'order'             => 108,
			],
			[
			'id'                => 109,
			'account_type_id'   => 14,
			'name'              => _l('acc_bank_charges'),
			'note'              => _l('acc_bank_charges_note'),
			'order'             => 109,
			],
			[
			'id'                => 110,
			'account_type_id'   => 14,
			'name'              => _l('acc_charitable_contributions'),
			'note'              => _l('acc_charitable_contributions_note'),
			'order'             => 110,
			],
			[
			'id'                => 111,
			'account_type_id'   => 14,
			'name'              => _l('acc_commissions_and_fees'),
			'note'              => _l('acc_commissions_and_fees_note'),
			'order'             => 111,
			],
			[
			'id'                => 112,
			'account_type_id'   => 14,
			'name'              => _l('acc_cost_of_labour'),
			'note'              => _l('acc_cost_of_labour_note'),
			'order'             => 112,
			],
			[
			'id'                => 113,
			'account_type_id'   => 14,
			'name'              => _l('acc_dues_and_subscriptions'),
			'note'              => _l('acc_dues_and_subscriptions_note'),
			'order'             => 113,
			],
			[
			'id'                => 114,
			'account_type_id'   => 14,
			'name'              => _l('acc_equipment_rental'),
			'note'              => _l('acc_equipment_rental_note'),
			'order'             => 114,
			],
			[
			'id'                => 115,
			'account_type_id'   => 14,
			'name'              => _l('acc_finance_costs'),
			'note'              => _l('acc_finance_costs_note'),
			'order'             => 115,
			],
			[
			'id'                => 116,
			'account_type_id'   => 14,
			'name'              => _l('acc_income_tax_expense'),
			'note'              => _l('acc_income_tax_expense_note'),
			'order'             => 116,
			],
			[
			'id'                => 117,
			'account_type_id'   => 14,
			'name'              => _l('acc_insurance'),
			'note'              => _l('acc_insurance_note'),
			'order'             => 117,
			],
			[
			'id'                => 118,
			'account_type_id'   => 14,
			'name'              => _l('acc_interest_paid'),
			'note'              => _l('acc_interest_paid_note'),
			'order'             => 118,
			],
			[
			'id'                => 119,
			'account_type_id'   => 14,
			'name'              => _l('acc_legal_and_professional_fees'),
			'note'              => _l('acc_legal_and_professional_fees_note'),
			'order'             => 119,
			],
			[
			'id'                => 120,
			'account_type_id'   => 14,
			'name'              => _l('acc_loss_on_discontinued_operations_net_of_tax'),
			'note'              => _l('acc_loss_on_discontinued_operations_net_of_tax_note'),
			'order'             => 120,
			],
			[
			'id'                => 121,
			'account_type_id'   => 14,
			'name'              => _l('acc_management_compensation'),
			'note'              => _l('acc_management_compensation_note'),
			'order'             => 121,
			],
			[
			'id'                => 122,
			'account_type_id'   => 14,
			'name'              => _l('acc_meals_and_entertainment'),
			'note'              => _l('acc_meals_and_entertainment_note'),
			'order'             => 122,
			],
			[
			'id'                => 123,
			'account_type_id'   => 14,
			'name'              => _l('acc_office_general_administrative_expenses'),
			'note'              => _l('acc_office_general_administrative_expenses_note'),
			'order'             => 123,
			],
			[
			'id'                => 124,
			'account_type_id'   => 14,
			'name'              => _l('acc_other_miscellaneous_service_cost'),
			'note'              => _l('acc_other_miscellaneous_service_cost_note'),
			'order'             => 124,
			],
			[
			'id'                => 125,
			'account_type_id'   => 14,
			'name'              => _l('acc_other_selling_expenses'),
			'note'              => _l('acc_other_selling_expenses_note'),
			'order'             => 125,
			],
			[
			'id'                => 126,
			'account_type_id'   => 14,
			'name'              => _l('acc_payroll_expenses'),
			'note'              => _l('acc_payroll_expenses_note'),
			'order'             => 126,
			],
			[
			'id'                => 127,
			'account_type_id'   => 14,
			'name'              => _l('acc_rent_or_lease_of_buildings'),
			'note'              => _l('acc_rent_or_lease_of_buildings_note'),
			'order'             => 127,
			],
			[
			'id'                => 128,
			'account_type_id'   => 14,
			'name'              => _l('acc_repair_and_maintenance'),
			'note'              => _l('acc_repair_and_maintenance_note'),
			'order'             => 128,
			],
			[
			'id'                => 129,
			'account_type_id'   => 14,
			'name'              => _l('acc_shipping_and_delivery_expense'),
			'note'              => _l('acc_shipping_and_delivery_expense_note'),
			'order'             => 129,
			],
			[
			'id'                => 130,
			'account_type_id'   => 14,
			'name'              => _l('acc_supplies_and_materials'),
			'note'              => _l('acc_supplies_and_materials_note'),
			'order'             => 130,
			],
			[
			'id'                => 131,
			'account_type_id'   => 14,
			'name'              => _l('acc_taxes_paid'),
			'note'              => _l('acc_taxes_paid_note'),
			'order'             => 131,
			],
			[
			'id'                => 132,
			'account_type_id'   => 14,
			'name'              => _l('acc_travel_expenses_general_and_admin_expenses'),
			'note'              => _l('acc_travel_expenses_general_and_admin_expenses_note'),
			'order'             => 132,
			],
			[
			'id'                => 133,
			'account_type_id'   => 14,
			'name'              => _l('acc_travel_expenses_selling_expense'),
			'note'              => _l('acc_travel_expenses_selling_expense_note'),
			'order'             => 133,
			],
			[
			'id'                => 134,
			'account_type_id'   => 14,
			'name'              => _l('acc_unapplied_cash_bill_payment_expense'),
			'note'              => _l('acc_unapplied_cash_bill_payment_expense_note'),
			'order'             => 134,
			],
			[
			'id'                => 135,
			'account_type_id'   => 14,
			'name'              => _l('acc_utilities'),
			'note'              => _l('acc_utilities_note'),
			'order'             => 135,
			],
			[
			'id'                => 136,
			'account_type_id'   => 15,
			'name'              => _l('acc_amortisation'),
			'note'              => _l('acc_amortisation_note'),
			'order'             => 136,
			],
			[
			'id'                => 137,
			'account_type_id'   => 15,
			'name'              => _l('acc_depreciation'),
			'note'              => _l('acc_depreciation_note'),
			'order'             => 137,
			],
			[
			'id'                => 138,
			'account_type_id'   => 15,
			'name'              => _l('acc_exchange_gain_or_loss'),
			'note'              => _l('acc_exchange_gain_or_loss_note'),
			'order'             => 138,
			],
			[
			'id'                => 139,
			'account_type_id'   => 15,
			'name'              => _l('acc_other_expense'),
			'note'              => _l('acc_other_expense_note'),
			'order'             => 139,
			],
			[
			'id'                => 140,
			'account_type_id'   => 15,
			'name'              => _l('acc_penalties_and_settlements'),
			'note'              => _l('acc_penalties_and_settlements_note'),
			'order'             => 140,
			],
			]);
			
			usort($account_type_details, function ($a, $b) {
				return $a['order'] - $b['order'];
			});
			
			$account_type_details_2 = $this->db->get(db_prefix().'acc_account_type_details')->result_array();
			
			return array_merge($account_type_details, $account_type_details_2);
		}
		
		/**
			* add default account
		*/
		public function add_default_account(){
			
			if($this->db->count_all_results(db_prefix().'acc_accounts') > 1){
				return false;
			}
			
			$accounts = [
			[
			'name' => '',
			'key_name' => 'acc_accounts_receivable',
			'account_type_id' => 1,
			'account_detail_type_id' => 1,
			],
			[
			'name' => '',
			'key_name' => 'acc_accrued_holiday_payable',
			'account_type_id' => 9,
			'account_detail_type_id' => 61,
			],
			[
			'name' => '',
			'key_name' => 'acc_accrued_liabilities',
			'account_type_id' => 8,
			'account_detail_type_id' => 44,
			],
			[
			'name' => '',
			'key_name' => 'acc_accrued_non_current_liabilities',
			'account_type_id' => 9,
			'account_detail_type_id' => 62,
			],
			[
			'name' => '',
			'key_name' => 'acc_accumulated_depreciation_on_property_plant_and_equipment',
			'account_type_id' => 4,
			'account_detail_type_id' => 22,
			],
			[
			'name' => '',
			'key_name' => 'acc_allowance_for_bad_debts',
			'account_type_id' => 2,
			'account_detail_type_id' => 2,
			],
			[
			'name' => '',
			'key_name' => 'acc_amortisation_expense',
			'account_type_id' => 14,
			'account_detail_type_id' => 106,
			],
			[
			'name' => '',
			'key_name' => 'acc_assets_held_for_sale',
			'account_type_id' => 5,
			'account_detail_type_id' => 32,
			],
			[
			'name' => '',
			'key_name' => 'acc_available_for_sale_assets_short_term',
			'account_type_id' => 2,
			'account_detail_type_id' => 3,
			],
			[
			'name' => '',
			'key_name' => 'acc_bad_debts',
			'account_type_id' => 14,
			'account_detail_type_id' => 108,
			],
			[
			'name' => '',
			'key_name' => 'acc_bank_charges',
			'account_type_id' => 14,
			'account_detail_type_id' => 109,
			],
			[
			'name' => '',
			'key_name' => 'acc_billable_expense_income',
			'account_type_id' => 11,
			'account_detail_type_id' => 89,
			],
			[
			'name' => '',
			'key_name' => 'acc_cash_and_cash_equivalents',
			'account_type_id' => 3,
			'account_detail_type_id' => 15,
			],
			[
			'name' => '',
			'key_name' => 'acc_change_in_inventory_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_commissions_and_fees',
			'account_type_id' => 14,
			'account_detail_type_id' => 111,
			],
			[
			'name' => '',
			'key_name' => 'acc_cost_of_sales',
			'account_type_id' => 13,
			'account_detail_type_id' => 104,
			],
			[
			'name' => '',
			'key_name' => 'acc_deferred_tax_assets',
			'account_type_id' => 5,
			'account_detail_type_id' => 33,
			],
			[
			'name' => '',
			'key_name' => 'acc_direct_labour_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_discounts_given_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_dividend_disbursed',
			'account_type_id' => 10,
			'account_detail_type_id' => 69,
			],
			[
			'name' => '',
			'key_name' => 'acc_dividend_income',
			'account_type_id' => 12,
			'account_detail_type_id' => 92,
			],
			[
			'name' => '',
			'key_name' => 'acc_dividends_payable',
			'account_type_id' => 8,
			'account_detail_type_id' => 48,
			],
			[
			'name' => '',
			'key_name' => 'acc_dues_and_subscriptions',
			'account_type_id' => 14,
			'account_detail_type_id' => 113,
			],
			[
			'name' => '',
			'key_name' => 'acc_equipment_rental',
			'account_type_id' => 14,
			'account_detail_type_id' => 114,
			],
			[
			'name' => '',
			'key_name' => 'acc_equity_in_earnings_of_subsidiaries',
			'account_type_id' => 10,
			'account_detail_type_id' => 70,
			],
			[
			'name' => '',
			'key_name' => 'acc_freight_and_delivery_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_goodwill',
			'account_type_id' => 5,
			'account_detail_type_id' => 34,
			],
			[
			'name' => '',
			'key_name' => 'acc_income_tax_expense',
			'account_type_id' => 14,
			'account_detail_type_id' => 116,
			],
			[
			'name' => '',
			'key_name' => 'acc_income_tax_payable',
			'account_type_id' => 8,
			'account_detail_type_id' => 50,
			],
			[
			'name' => '',
			'key_name' => 'acc_insurance_disability',
			'account_type_id' => 14,
			'account_detail_type_id' => 117,
			],
			[
			'name' => '',
			'key_name' => 'acc_insurance_general',
			'account_type_id' => 14,
			'account_detail_type_id' => 117,
			],
			[
			'name' => '',
			'key_name' => 'acc_insurance_liability',
			'account_type_id' => 14,
			'account_detail_type_id' => 117,
			],
			[
			'name' => '',
			'key_name' => 'acc_intangibles',
			'account_type_id' => 5,
			'account_detail_type_id' => 35,
			],
			[
			'name' => '',
			'key_name' => 'acc_interest_expense',
			'account_type_id' => 14,
			'account_detail_type_id' => 118,
			],
			[
			'name' => '',
			'key_name' => 'acc_interest_income',
			'account_type_id' => 12,
			'account_detail_type_id' => 93,
			],
			[
			'name' => '',
			'key_name' => 'acc_inventory',
			'account_type_id' => 2,
			'account_detail_type_id' => 5,
			],
			[
			'name' => '',
			'key_name' => 'acc_inventory_asset',
			'account_type_id' => 2,
			'account_detail_type_id' => 5,
			],
			[
			'name' => '',
			'key_name' => 'acc_legal_and_professional_fees',
			'account_type_id' => 14,
			'account_detail_type_id' => 119,
			],
			[
			'name' => '',
			'key_name' => 'acc_liabilities_related_to_assets_held_for_sale',
			'account_type_id' => 9,
			'account_detail_type_id' => 63,
			],
			[
			'name' => '',
			'key_name' => 'acc_long_term_debt',
			'account_type_id' => 9,
			'account_detail_type_id' => 64,
			],
			[
			'name' => '',
			'key_name' => 'acc_long_term_investments',
			'account_type_id' => 5,
			'account_detail_type_id' => 38,
			],
			[
			'name' => '',
			'key_name' => 'acc_loss_on_discontinued_operations_net_of_tax',
			'account_type_id' => 14,
			'account_detail_type_id' => 120,
			],
			[
			'name' => '',
			'key_name' => 'acc_loss_on_disposal_of_assets',
			'account_type_id' => 12,
			'account_detail_type_id' => 94,
			],
			[
			'name' => '',
			'key_name' => 'acc_management_compensation',
			'account_type_id' => 14,
			'account_detail_type_id' => 121,
			],
			[
			'name' => '',
			'key_name' => 'acc_materials_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_meals_and_entertainment',
			'account_type_id' => 14,
			'account_detail_type_id' => 122,
			],
			[
			'name' => '',
			'key_name' => 'acc_office_expenses',
			'account_type_id' => 14,
			'account_detail_type_id' => 123,
			],
			[
			'name' => '',
			'key_name' => 'acc_other_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_other_comprehensive_income',
			'account_type_id' => 10,
			'account_detail_type_id' => 73,
			],
			[
			'name' => '',
			'key_name' => 'acc_other_general_and_administrative_expenses',
			'account_type_id' => 14,
			'account_detail_type_id' => 123,
			],
			[
			'name' => '',
			'key_name' => 'acc_other_operating_income_expenses',
			'account_type_id' => 12,
			'account_detail_type_id' => 97,
			],
			[
			'name' => '',
			'key_name' => 'acc_other_selling_expenses',
			'account_type_id' => 14,
			'account_detail_type_id' => 125,
			],
			[
			'name' => '',
			'key_name' => 'acc_other_type_of_expenses_advertising_expenses',
			'account_type_id' => 14,
			'account_detail_type_id' => 105,
			],
			[
			'name' => '',
			'key_name' => 'acc_overhead_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_payroll_clearing',
			'account_type_id' => 8,
			'account_detail_type_id' => 55,
			],
			[
			'name' => '',
			'key_name' => 'acc_payroll_expenses',
			'account_type_id' => 14,
			'account_detail_type_id' => 126,
			],
			[
			'name' => '',
			'key_name' => 'acc_payroll_liabilities',
			'account_type_id' => 8,
			'account_detail_type_id' => 56,
			],
			[
			'name' => '',
			'key_name' => 'acc_prepaid_expenses',
			'account_type_id' => 2,
			'account_detail_type_id' => 11,
			],
			[
			'name' => '',
			'key_name' => 'acc_property_plant_and_equipment',
			'account_type_id' => 4,
			'account_detail_type_id' => 26,
			],
			[
			'name' => '',
			'key_name' => 'acc_purchases',
			'account_type_id' => 14,
			'account_detail_type_id' => 130,
			],
			[
			'name' => '',
			'key_name' => 'acc_reconciliation_discrepancies',
			'account_type_id' => 15,
			'account_detail_type_id' => 139,
			],
			[
			'name' => '',
			'key_name' => 'acc_rent_or_lease_payments',
			'account_type_id' => 14,
			'account_detail_type_id' => 127,
			],
			[
			'name' => '',
			'key_name' => 'acc_repair_and_maintenance',
			'account_type_id' => 14,
			'account_detail_type_id' => 128,
			],
			[
			'name' => '',
			'key_name' => 'acc_retained_earnings',
			'account_type_id' => 10,
			'account_detail_type_id' => 80,
			],
			[
			'name' => '',
			'key_name' => 'acc_revenue_general',
			'account_type_id' => 11,
			'account_detail_type_id' => 86,
			],
			[
			'name' => '',
			'key_name' => 'acc_sales',
			'account_type_id' => 11,
			'account_detail_type_id' => 89,
			],
			[
			'name' => '',
			'key_name' => 'acc_sales_retail',
			'account_type_id' => 11,
			'account_detail_type_id' => 87,
			],
			[
			'name' => '',
			'key_name' => 'acc_sales_wholesale',
			'account_type_id' => 11,
			'account_detail_type_id' => 88,
			],
			[
			'name' => '',
			'key_name' => 'acc_sales_of_product_income',
			'account_type_id' => 11,
			'account_detail_type_id' => 89,
			],
			[
			'name' => '',
			'key_name' => 'acc_share_capital',
			'account_type_id' => 10,
			'account_detail_type_id' => 81,
			],
			[
			'name' => '',
			'key_name' => 'acc_shipping_and_delivery_expense',
			'account_type_id' => 14,
			'account_detail_type_id' => 129,
			],
			[
			'name' => '',
			'key_name' => 'acc_short_term_debit',
			'account_type_id' => 8,
			'account_detail_type_id' => 54,
			],
			[
			'name' => '',
			'key_name' => 'acc_stationery_and_printing',
			'account_type_id' => 14,
			'account_detail_type_id' => 123,
			],
			[
			'name' => '',
			'key_name' => 'acc_subcontractors_cos',
			'account_type_id' => 13,
			'account_detail_type_id' => 100,
			],
			[
			'name' => '',
			'key_name' => 'acc_supplies',
			'account_type_id' => 14,
			'account_detail_type_id' => 130,
			],
			[
			'name' => '',
			'key_name' => 'acc_travel_expenses_general_and_admin_expenses',
			'account_type_id' => 14,
			'account_detail_type_id' => 132,
			],
			[
			'name' => '',
			'key_name' => 'acc_travel_expenses_selling_expense',
			'account_type_id' => 14,
			'account_detail_type_id' => 133,
			],
			[
			'name' => '',
			'key_name' => 'acc_unapplied_cash_payment_income',
			'account_type_id' => 11,
			'account_detail_type_id' => 91,
			],
			[
			'name' => '',
			'key_name' => 'acc_uncategorised_asset',
			'account_type_id' => 2,
			'account_detail_type_id' => 10,
			],
			[
			'name' => '',
			'key_name' => 'acc_uncategorised_expense',
			'account_type_id' => 14,
			'account_detail_type_id' => 124,
			],
			[
			'name' => '',
			'key_name' => 'acc_uncategorised_income',
			'account_type_id' => 11,
			'account_detail_type_id' => 89,
			],
			[
			'name' => '',
			'key_name' => 'acc_undeposited_funds',
			'account_type_id' => 2,
			'account_detail_type_id' => 13,
			],
			[
			'name' => '',
			'key_name' => 'acc_unrealised_loss_on_securities_net_of_tax',
			'account_type_id' => 12,
			'account_detail_type_id' => 99,
			],
			[
			'name' => '',
			'key_name' => 'acc_utilities',
			'account_type_id' => 14,
			'account_detail_type_id' => 135,
			],
			[
			'name' => '',
			'key_name' => 'acc_wage_expenses',
			'account_type_id' => 14,
			'account_detail_type_id' => 126,
			],
			[
			'name' => '',
			'key_name' => 'acc_credit_card',
			'account_type_id' => 7,
			'account_detail_type_id' => 43,
			],
			[
			'name' => '',
			'key_name' => 'acc_accounts_payable',
			'account_type_id' => 6,
			'account_detail_type_id' => 42,
			],
			];
			
			$affectedRows = $this->db->insert_batch(db_prefix().'acc_accounts',  $accounts);
			
			if ($affectedRows > 0) {
				$this->db->where('name', 'acc_add_default_account');
				$this->db->update(db_prefix() . 'options', [
				'value' => 1,
				]);
				
				return true;
			}
			
			return false;
		}
		
		/**
			* add default account new
		*/
		public function add_default_account_new(){
			$this->db->where('key_name != ""');
			$affectedRows = $this->db->update(db_prefix().'acc_accounts',  ['default_account' => 1]);
			
			if ($affectedRows > 0) {
				$this->db->where('name', 'add_default_account_new');
				$this->db->update(db_prefix() . 'options', [
				'value' => 1,
				]);
				
				return true;
			}
			
			return false;
		}
		
		/**
			* update general setting
			*
			* @param      array   $data   The data
			*
			* @return     boolean 
		*/
		public function update_general_setting($data){
			$affectedRows = 0;
			if(!isset($data['acc_close_the_books'])){
				$data['acc_close_the_books'] = 0;
			}
			if(!isset($data['acc_enable_account_numbers'])){
				$data['acc_enable_account_numbers'] = 0;
			}
			if(!isset($data['acc_show_account_numbers'])){
				$data['acc_show_account_numbers'] = 0;
			}
			
			if($data['acc_closing_date'] != ''){
				$data['acc_closing_date'] = to_sql_date($data['acc_closing_date']);
			}
			
			foreach ($data as $key => $value) {
				$this->db->where('name', $key);
				$this->db->update(db_prefix() . 'options', [
				'value' => $value,
				]);
				if ($this->db->affected_rows() > 0) {
					$affectedRows++;
				}
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* update automatic conversion
			*
			* @param      array   $data   The data
			*
			* @return     boolean 
		*/
		public function update_automatic_conversion($data){
			$affectedRows = 0;
			
			if(!isset($data['acc_invoice_automatic_conversion'])){
				$data['acc_invoice_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_payment_automatic_conversion'])){
				$data['acc_payment_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_expense_automatic_conversion'])){
				$data['acc_expense_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_tax_automatic_conversion'])){
				$data['acc_tax_automatic_conversion'] = 0;
			}
			
			foreach ($data as $key => $value) {
				$this->db->where('name', $key);
				$this->db->update(db_prefix() . 'options', [
				'value' => $value,
				]);
				if ($this->db->affected_rows() > 0) {
					$affectedRows++;
				}
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* get accounts
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_accounts($id = '', $where = [])
		{
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'acc_accounts')->row();
			}
			
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$this->db->where($where);
			$this->db->where('active', 1);
			$this->db->order_by('account_type_id,account_detail_type_id', 'desc');
			$accounts = $this->db->get(db_prefix() . 'acc_accounts')->result_array();
			
			$account_types = $this->accounting_model->get_account_types();
			$detail_types = $this->accounting_model->get_account_type_details();
			
			$account_type_name = [];
			$detail_type_name = [];
			
			foreach ($account_types as $key => $value) {
				$account_type_name[$value['id']] = $value['name'];
			}
			
			foreach ($detail_types as $key => $value) {
				$detail_type_name[$value['id']] = $value['name'];
			}
			
			foreach ($accounts as $key => $value) {
				
				if($acc_show_account_numbers == 1 && $value['number'] != ''){
					$accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
					}else{
					$accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
				}
				
				$_account_type_name = isset($account_type_name[$value['account_type_id']]) ? $account_type_name[$value['account_type_id']] : '';
				$_detail_type_name = isset($detail_type_name[$value['account_detail_type_id']]) ? $detail_type_name[$value['account_detail_type_id']] : '';
				$accounts[$key]['account_type_name'] = $_account_type_name;
				$accounts[$key]['detail_type_name'] = $_detail_type_name;
			}
			
			return $accounts;
		}
		
		/**
			* get accounts
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_accounts_for_receipts($id = '', $where = [])
		{
			$subgroup = array('1000001');
			if ($id) {
				
				$selected_company = $this->session->userdata('root_company');
				$this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
				$this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID');
				$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
				$this->db->where(db_prefix() . 'clients.AccountID', $id);
				//$this->db->where(db_prefix() . 'clients.active', 1);
				return $this->db->get(db_prefix() . 'clients')->row();
			}
			
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
			$this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			//$this->db->where(db_prefix() . 'clients.active', 1);
			$this->db->where_not_in('tblclients.SubActGroupID',$subgroup);
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			
			foreach ($accounts as $key => $value) {
				if($acc_show_account_numbers == 1 && $value['number'] != ''){
					$accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
					}else{
					$accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
				}
			}
			
			return $accounts;
		}
		
		/**
			* get accounts
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_accounts_for_payment($id = '', $where = [])
		{
			$subgroup = array('1000001');
			if ($id) {
				
				$selected_company = $this->session->userdata('root_company');
				$this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
				$this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID');
				$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
				$this->db->where('AccountID', $id);
				$this->db->where_not_in('SubActGroupID',$subgroup);
				return $this->db->get(db_prefix() . 'clients')->row();
			}
			
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
			$this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_not_in('tblclients.SubActGroupID',$subgroup);
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			
			foreach ($accounts as $key => $value) {
				
				if($acc_show_account_numbers == 1 && $value['number'] != ''){
					$accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
					}else{
					$accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
				}
			}
			return $accounts;
		}
		
		public function get_staff_for_payment($id = '', $where = [])
		{
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'staff.*');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			$this->db->where('tblstaff.PlantID ',$selected_company);
			//$this->db->where(db_prefix() . 'staff.active', 1);
			$accounts = $this->db->get(db_prefix() . 'staff')->result_array();
			
			foreach ($accounts as $key => $value) {
				
				if($acc_show_account_numbers == 1 && $value['number'] != ''){
					$accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
					}else{
					$accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
				}
			}
			return $accounts;
		}
		
		/**
			* get accounts
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_accounts_for_contra($id = '', $where = [])
		{
			$subgroup = array('1000001');
			
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
			$this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'clients.SubActGroupID',$subgroup);
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			foreach ($accounts as $key => $value) {
				if($acc_show_account_numbers == 1 && $value['number'] != ''){
					$accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
					}else{
					$accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
				}
			}
			return $accounts;
		}
		
		/**
			* get accounts
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_accounts_for_ledger()
		{
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->order_by('company', 'asc');
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			
			return $accounts;
		}
		public function get_staff_for_ledger()
		{
			
			$selected_company = $this->session->userdata('root_company');
			//$this->db->where('PlantID', $selected_company);
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			$this->db->where('tblstaff.PlantID',$selected_company);
			$this->db->order_by('firstname', 'asc');
			$accounts = $this->db->get(db_prefix() . 'staff')->result_array();
			
			return $accounts;
		}
		
		/**
			* get accounts
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_accounts_for_journal($id = '', $where = [])
		{
			
			$subgroup = array('1000001');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'clients.*,'.db_prefix() . 'accountgroupssub.SubActGroupName');
			$this->db->join(db_prefix() . 'accountgroupssub', db_prefix() . 'accountgroupssub.SubActGroupID=' . db_prefix() . 'clients.SubActGroupID');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->where_not_in(db_prefix() . 'clients.SubActGroupID',$subgroup);
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			
			foreach ($accounts as $key => $value) {
				
				if($acc_show_account_numbers == 1 && $value['number'] != ''){
					$accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
					}else{
					$accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
				}
				
				
			}
			
			return $accounts;
		}
		
		public function get_staff_for_journal($id = '', $where = [])
		{
			
			//$subgroup = array('60001007','60001008','50003001');
			
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'staff.*');
			//$this->db->where(db_prefix() . 'staff.active', '1');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			$this->db->where('tblstaff.PlantID',$selected_company);
			$accounts = $this->db->get(db_prefix() . 'staff')->result_array();
			
			
			
			foreach ($accounts as $key => $value) {
				
				if($acc_show_account_numbers == 1 && $value['number'] != ''){
					$accounts[$key]['name'] = $value['name'] != '' ? $value['number'].' - '.$value['name'] : $value['number'].' - '._l($value['key_name']);
					}else{
					$accounts[$key]['name'] = $value['name'] != '' ? $value['name'] : _l($value['key_name']);
				}
				
				
			}
			
			return $accounts;
		}
		
		
		/**
			* add new account
			* @param array $data
			* @return integer
		*/
		public function add_account($data)
		{
			if (isset($data['id'])) {
				unset($data['id']);
			}
			
			if($data['balance_as_of'] != ''){
				$data['balance_as_of'] = to_sql_date($data['balance_as_of']);
			}
			
			if(isset($data['update_balance'])){
				unset($data['update_balance']);
			}
			
			$data['balance'] = str_replace(',', '', $data['balance']);
			$this->db->insert(db_prefix() . 'acc_accounts', $data);
			
			$insert_id = $this->db->insert_id();
			
			if ($insert_id) {
				if($data['balance'] > 0){
					$node = [];
					$node['account'] = $insert_id;
					$node['ending_balance'] = $data['balance'];
					$node['beginning_balance'] = 0;
					$node['finish'] = 1;
					if($data['balance_as_of'] != ''){
						$node['ending_date'] = $data['balance_as_of'];
						}else{
						$node['ending_date'] = date('Y-m-d');
					}
					
					$this->db->insert(db_prefix().'acc_reconciles', $node);
					$reconcile_id = $this->db->insert_id();
					
					$this->db->where('account_type_id', 10);
					$this->db->where('account_detail_type_id', 71);
					$account = $this->db->get(db_prefix().'acc_accounts')->row();
					
					if($account){
						$node = [];
						
						if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
							$node['debit'] = $data['balance'];
							$node['credit'] = 0;
							}else{
							$node['debit'] = 0;
							$node['credit'] = $data['balance'];
						}
						
						$node['split'] = $insert_id;
						$node['account'] = $account->id;
						$node['rel_id'] = 0;
						$node['rel_type'] = 'deposit';
						if($data['balance_as_of'] != ''){
							$node['date'] = $data['balance_as_of'];
							}else{
							$node['date'] = date('Y-m-d');
						}
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						
						$this->db->insert(db_prefix().'acc_account_history', $node);
						
						$node = [];
						if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
							$node['debit'] = 0;
							$node['credit'] = $data['balance'];
							}else{
							$node['debit'] = $data['balance'];
							$node['credit'] = 0;
						}
						
						$node['reconcile'] = $reconcile_id;
						$node['split'] = $account->id;
						$node['account'] = $insert_id;
						$node['rel_id'] = 0;
						$node['rel_type'] = 'deposit';
						if($data['balance_as_of'] != ''){
							$node['date'] = $data['balance_as_of'];
							}else{
							$node['date'] = date('Y-m-d');
						}
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						
						$this->db->insert(db_prefix().'acc_account_history', $node);
						}else{
						$this->db->insert(db_prefix().'acc_accounts', [
						'name' => '',
						'key_name' => 'acc_opening_balance_equity',
						'account_type_id' => 10,
						'account_detail_type_id' => 71,
						]);
						
						$account_id = $this->db->insert_id();
						
						if ($account_id) {
							$node = [];
							if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
								$node['debit'] = $data['balance'];
								$node['credit'] = 0;
								}else{
								$node['debit'] = 0;
								$node['credit'] = $data['balance'];
							}
							
							$node['split'] = $insert_id;
							$node['account'] = $account_id;
							if($data['balance_as_of'] != ''){
								$node['date'] = $data['balance_as_of'];
								}else{
								$node['date'] = date('Y-m-d');
							}
							$node['rel_id'] = 0;
							$node['rel_type'] = 'deposit';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							
							$this->db->insert(db_prefix().'acc_account_history', $node);
							
							$node = [];
							if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
								$node['debit'] = 0;
								$node['credit'] = $data['balance'];
								}else{
								$node['debit'] = $data['balance'];
								$node['credit'] = 0;
							}
							
							$node['reconcile'] = $reconcile_id;
							$node['split'] = $account_id;
							$node['account'] = $insert_id;
							if($data['balance_as_of'] != ''){
								$node['date'] = $data['balance_as_of'];
								}else{
								$node['date'] = date('Y-m-d');
							}
							$node['rel_id'] = 0;
							$node['rel_type'] = 'deposit';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							
							$this->db->insert(db_prefix().'acc_account_history', $node);
						}
					}
				}
				
				
				return $insert_id;
			}
			
			return false;
		}
		
		/**
			* update account
			* @param array $data
			* @param integer $id
			* @return integer
		*/
		public function update_account($data, $id)
		{
			if (isset($data['id'])) {
				unset($data['id']);
			}
			
			if($data['balance_as_of'] != ''){
				$data['balance_as_of'] = to_sql_date($data['balance_as_of']);
			}
			$update_balance = 0;
			if(isset($data['update_balance'])){
				$update_balance = $data['update_balance'];
				unset($data['update_balance']);
			}
			
			$data['balance'] = str_replace(',', '', $data['balance']);
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'acc_accounts', $data);
			
			if ($this->db->affected_rows() > 0) {
				if($data['balance'] > 0 && $update_balance == 1){
					$node = [];
					$node['account'] = $id;
					$node['ending_balance'] = $data['balance'];
					$node['beginning_balance'] = 0;
					$node['finish'] = 1;
					if($data['balance_as_of'] != ''){
						$node['ending_date'] = $data['balance_as_of'];
						}else{
						$node['ending_date'] = date('Y-m-d');
					}
					
					$this->db->insert(db_prefix().'acc_reconciles', $node);
					$reconcile_id = $this->db->insert_id();
					
					$this->db->where('account_type_id', 10);
					$this->db->where('account_detail_type_id', 71);
					$account = $this->db->get(db_prefix().'acc_accounts')->row();
					
					if($account){
						$node = [];
						
						if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
							$node['debit'] = $data['balance'];
							$node['credit'] = 0;
							
							}else{
							$node['debit'] = 0;
							$node['credit'] = $data['balance'];
						}
						
						$node['split'] = $id;
						$node['account'] = $account->id;
						
						if($data['balance_as_of'] != ''){
							$node['date'] = $data['balance_as_of'];
							}else{
							$node['date'] = date('Y-m-d');
						}
						
						$node['rel_id'] = 0;
						$node['rel_type'] = 'deposit';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$this->db->insert(db_prefix().'acc_account_history', $node);
						
						$node = [];
						if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
							$node['debit'] = 0;
							$node['credit'] = $data['balance'];
							}else{
							$node['debit'] = $data['balance'];
							$node['credit'] = 0;
						}
						
						$node['reconcile'] = $reconcile_id;
						$node['split'] = $account->id;
						$node['account'] = $id;
						$node['rel_id'] = 0;
						
						if($data['balance_as_of'] != ''){
							$node['date'] = $data['balance_as_of'];
							}else{
							$node['date'] = date('Y-m-d');
						}
						$node['rel_type'] = 'deposit';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						
						$this->db->insert(db_prefix().'acc_account_history', $node);
						}else{
						$this->db->insert(db_prefix().'acc_accounts', [
						'name' => '',
						'key_name' => 'acc_opening_balance_equity',
						'account_type_id' => 10,
						'account_detail_type_id' => 71,
						]);
						
						$account_id = $this->db->insert_id();
						
						if ($account_id) {
							$node = [];
							if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
								$node['debit'] = $data['balance'];
								$node['credit'] = 0;
								}else{
								$node['debit'] = 0;
								$node['credit'] = $data['balance'];
							}
							
							$node['split'] = $id;
							$node['account'] = $account_id;
							$node['rel_id'] = 0;
							if($data['balance_as_of'] != ''){
								$node['date'] = $data['balance_as_of'];
								}else{
								$node['date'] = date('Y-m-d');
							}
							$node['rel_type'] = 'deposit';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							
							$this->db->insert(db_prefix().'acc_account_history', $node);
							
							$node = [];
							if($data['account_type_id'] == 7 || $data['account_type_id'] == 15 || $data['account_type_id'] == 8 || $data['account_type_id'] == 9){
								$node['debit'] = 0;
								$node['credit'] = $data['balance'];
								}else{
								$node['debit'] = $data['balance'];
								$node['credit'] = 0;
							}
							
							$node['reconcile'] = $reconcile_id;
							$node['split'] = $account_id;
							$node['account'] = $id;
							$node['rel_id'] = 0;
							if($data['balance_as_of'] != ''){
								$node['date'] = $data['balance_as_of'];
								}else{
								$node['date'] = date('Y-m-d');
							}
							$node['rel_type'] = 'deposit';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							
							$this->db->insert(db_prefix().'acc_account_history', $node);
						}
					}
				}
				
				return true;
			}
			
			return false;
		}
		
		/**
			* Get the data account to choose from.
			*
			* @return     array  The product group select.
		*/
		public function get_data_account_to_select() {
			
			$accounts = $this->get_accounts();
			
			$acc_enable_account_numbers = get_option('acc_enable_account_numbers');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$list_accounts = [];
			
			$account_types = $this->accounting_model->get_account_types();
			$account_type_name = [];
			
			foreach ($account_types as $key => $value) {
				$account_type_name[$value['id']] = $value['name'];
			}
			
			foreach ($accounts as $key => $account) {
				$note = [];
				$note['id'] = $account['id'];
				
				$_account_type_name = isset($account_type_name[$account['account_type_id']]) ? $account_type_name[$account['account_type_id']] : '';
				
				$note['label'] = $account['name'].' - '.$_account_type_name;
				
				$list_accounts[] = $note;
			}
			return $list_accounts;
		}
		
		/**
			* Get the data account to choose from.
			*
			* @return     array  The product group select.
			* New Function created by madhav
		*/
		public function get_data_account_to_select_for_receipts() 
		{
			$accounts = $this->get_accounts_for_receipts();
			$staff_list = $this->get_staff_for_payment();
			
			$acc_enable_account_numbers = get_option('acc_enable_account_numbers');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$list_accounts = [];
			
			foreach ($accounts as $key => $account) {
				$note = [];
				$note['id'] = strtoupper($account['AccountID']);
				$note['label'] = $account['company'].'-'.$account['StationName'];
				
				$list_accounts[] = $note;
			}
			foreach ($staff_list as $key1 => $account1) {
				$note = [];
				$note['id'] = strtoupper($account1['AccountID']);
				$note['label'] = $account1['firstname']." ".$account1['lastname'].'-'.$account1["stationName"];
				
				$list_accounts[] = $note;
			}
			return $list_accounts;
			
		}
		
		/**
			* Get the data account to choose from.
			*
			* @return     array  The product group select.
			* New Function created by madhav
		*/
		public function get_data_account_to_select_for_payment() {
			
			
			
			$accounts = $this->get_accounts_for_payment();
			$staff_list = $this->get_staff_for_payment();
			
			$acc_enable_account_numbers = get_option('acc_enable_account_numbers');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$list_accounts = [];
			
			
			foreach ($accounts as $key => $account) {
				$note = [];
				$note['id'] = strtoupper($account['AccountID']);
				$note['label'] = $account['company'].' - '.$account['AccountID'].'-'.$account['StationName'];
				
				$list_accounts[] = $note;
			}
			
			foreach ($staff_list as $key1 => $account1) {
				$note = [];
				$note['id'] = strtoupper($account1['AccountID']);
				$note['label'] = $account1['firstname']." ".$account1['lastname'].' - '.$account1['AccountID'].'-'.$account1["stationName"];
				
				$list_accounts[] = $note;
			}
			return $list_accounts;
			
		}
		
		/**
			* Get the data account to choose from.
			*
			* @return     array  The product group select.
			* New Function created by madhav
		*/
		public function get_data_account_to_select_for_contra() 
		{
			
			$accounts = $this->get_accounts_for_contra();
			$acc_enable_account_numbers = get_option('acc_enable_account_numbers');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$list_accounts = [];
			
			foreach ($accounts as $key => $account) {
				$note = [];
				$note['id'] = strtoupper($account['AccountID']);
				$note['label'] = $account['company'].' - '.$account['AccountID'];
				
				$list_accounts[] = $note;
			}
			return $list_accounts;
			
		}
		
		/**
			* Get the data account to choose from.
			*
			* @return     array  The product group select.
			* New Function created by madhav
		*/
		public function get_data_account_to_select_for_journal() {
			
			
			
			$accounts = $this->get_accounts_for_journal();
			$staff_list = $this->get_staff_for_journal();
			/*echo "<pre>";
				print_r($accounts);
			die;*/
			$acc_enable_account_numbers = get_option('acc_enable_account_numbers');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$list_accounts = [];
			
			
			foreach ($accounts as $key => $account) {
				$note = [];
				$note['id'] = strtoupper($account['AccountID']);
				$note['label'] = $account['company'].' - '.$account['StationName'];
				
				$list_accounts[] = $note;
			}
			foreach ($staff_list as $key1 => $account1) {
				$note = [];
				$note['id'] = strtoupper($account1['AccountID']);
				$note['label'] = $account1['firstname']." ".$account1['lastname'].' - '.$account1['stationName'];
				
				$list_accounts[] = $note;
			}
			return $list_accounts;
			
		}
		
		
		/**
			* Get the data account to choose from.
			*
			* @return     array  The product group select.
		*/
		public function get_data_ganeral_account_to_select() 
		{
			$selected_company = $this->session->userdata('root_company');
			$subgroup = array('1000001');
			$this->db->where('PlantID', $selected_company);
			$this->db->where_in('SubActGroupID',$subgroup);
			$this->db->order_by('company', 'ASC');
			$accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			return $accounts;
		}
		
		/**
			* Get the Type of voucher
			*
			* @return     array  list of voucher.
		*/
		public function get_type_of_voucher() {
			$selected_company = $this->session->userdata('root_company');
			$FY = $fy = $this->session->userdata('finacial_year');
			$this->db->select('PassedFrom');
			$this->db->distinct('PassedFrom'); 
			
			$this->db->where('PlantID', $selected_company);
			$this->db->where('PassedFrom !=', 'CDNOTE');
			$this->db->where('PassedFrom !=', 'PURCHASERTN');
			$this->db->where('PassedFrom !=', 'SALESRECEIPT');
			$this->db->where('PassedFrom !=', 'SALESRTN');
			$this->db->where_in('FY',$FY);
			$this->db->order_by('PassedFrom', 'ASC');
			$voucher_type = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $voucher_type;
		}
		
		public function get_company_detail1($selected_company)
		{  
			
			$sql ='SELECT '.db_prefix().'rootcompany.* FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
			
			$result = $this->db->query($sql)->row();
			
			return $result;
			
			
		}
		
		/**
			* add account history
			* @param array $data
			* @return boolean
		*/
		public function add_account_history($data){
			$this->db->where('rel_id', $data['id']);
			$this->db->where('rel_type', $data['type']);
			$this->db->delete(db_prefix().'acc_account_history');
			
			$data['amount'] = str_replace(',', '', $data['amount']);
			
			$data_insert = [];
			if($data['type'] == 'invoice'){
				$this->load->model('invoices_model');
				$invoice = $this->invoices_model->get($data['id']);
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
				$currency_converter = 0;
				if($invoice->currency_name != $currency->name){
					$currency_converter = 1;
				}
				
				$payment_account = $data['payment_account'];
				$deposit_to = $data['deposit_to'];
				$invoice_payment_account = get_option('acc_invoice_payment_account');
				$invoice_deposit_to = get_option('acc_invoice_deposit_to');
				$item_amount = $data['item_amount'];
				$paid = 0;
				if($invoice->status == 2){
					$paid = 1;
				}
				
				foreach ($invoice->items as $value) {
					$item = $this->get_item_by_name($value['description']);
					$item_id = 0;
					if(isset($item->id)){
						$item_id = $item->id;
					}
					
					$item_total = $value['qty'] * $value['rate'];
					if(isset($data['exchange_rate'])){
						$item_total = round(($value['qty'] * $value['rate']) * $data['exchange_rate'], 2);
						}elseif($currency_converter == 1){
						$item_total = round($this->currency_converter($invoice->currency_name, $currency->name, $value['qty'] * $value['rate']), 2);
					}
					
					if(isset($payment_account[$item_id])) {
						$node = [];
						$node['split'] = $payment_account[$item_id];
						$node['account'] = $deposit_to[$item_id];
						$node['debit'] = $item_total;
						$node['paid'] = $paid;
						$node['date'] = $invoice->date;
						$node['item'] = $item_id;
						$node['customer'] = $invoice->clientid;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to[$item_id];
						$node['paid'] = $paid;
						$node['date'] = $invoice->date;
						$node['item'] = $item_id;
						$node['account'] = $payment_account[$item_id];
						$node['customer'] = $invoice->clientid;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $invoice_payment_account;
						$node['account'] = $invoice_deposit_to;
						$node['date'] = $invoice->date;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['customer'] = $invoice->clientid;
						$node['paid'] = $paid;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $invoice_deposit_to;
						$node['customer'] = $invoice->clientid;
						$node['account'] = $invoice_payment_account;
						$node['date'] = $invoice->date;
						$node['item'] = $item_id;
						$node['paid'] = $paid;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
				}
				
				if(get_option('acc_tax_automatic_conversion') == 1){
					$tax_payment_account = get_option('acc_tax_payment_account');
					$tax_deposit_to = get_option('acc_tax_deposit_to');
					
					$items = get_items_table_data($invoice, 'invoice', 'html', true);
					foreach($items->taxes() as $tax){
						$t = explode('|', $tax['tax_name']);
						$tax_name = '';
						$tax_rate = 0;
						if(isset($t[0])){
							$tax_name = $t[0];
						}
						if(isset($t[1])){
							$tax_rate = $t[1];
						}
						
						$this->db->where('name', $tax_name);
						$this->db->where('taxrate', $tax_rate);
						$_tax = $this->db->get(db_prefix().'taxes')->row();
						
						$total_tax = $tax['total_tax'];
						if(isset($data['exchange_rate'])){
							$total_tax = round($tax['total_tax'] * $data['exchange_rate'], 2);
							}elseif($currency_converter == 1){
							$total_tax = round($this->currency_converter($invoice->currency_name, $currency->name, $tax['total_tax']), 2);
						}
						
						if($_tax){
							$tax_mapping = $this->get_tax_mapping($_tax->id);
							
							if($tax_mapping){
								$node = [];
								$node['split'] = $tax_mapping->payment_account;
								$node['account'] = $tax_mapping->deposit_to;
								$node['tax'] = $_tax->id;
								$node['item'] = 0;
								$node['date'] = $invoice->date;
								$node['paid'] = $paid;
								$node['debit'] = $total_tax;
								$node['customer'] = $invoice->clientid;
								$node['credit'] = 0;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'invoice';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								
								$node = [];
								$node['split'] = $tax_mapping->deposit_to;
								$node['customer'] = $invoice->clientid;
								$node['account'] = $tax_mapping->payment_account;
								$node['tax'] = $_tax->id;
								$node['item'] = 0;
								$node['date'] = $invoice->date;
								$node['paid'] = $paid;
								$node['debit'] = 0;
								$node['credit'] = $total_tax;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'invoice';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								}else{
								$node = [];
								$node['split'] = $tax_payment_account;
								$node['account'] = $tax_deposit_to;
								$node['tax'] = $_tax->id;
								$node['item'] = 0;
								$node['date'] = $invoice->date;
								$node['paid'] = $paid;
								$node['debit'] = $total_tax;
								$node['customer'] = $invoice->clientid;
								$node['credit'] = 0;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'invoice';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								
								$node = [];
								$node['split'] = $tax_deposit_to;
								$node['customer'] = $invoice->clientid;
								$node['date'] = $invoice->date;
								$node['account'] = $tax_payment_account;
								$node['tax'] = $_tax->id;
								$node['item'] = 0;
								$node['paid'] = $paid;
								$node['debit'] = 0;
								$node['credit'] = $total_tax;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'invoice';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
							}
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['item'] = 0;
							$node['tax'] = 0;
							$node['date'] = $invoice->date;
							$node['paid'] = $paid;
							$node['debit'] = $total_tax;
							$node['customer'] = $invoice->clientid;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'invoice';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['customer'] = $invoice->clientid;
							$node['account'] = $tax_payment_account;
							$node['date'] = $invoice->date;
							$node['tax'] = 0;
							$node['item'] = 0;
							$node['paid'] = $paid;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'invoice';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				}elseif($data['type'] == 'loss_adjustment'){
				
				$this->load->model('warehouse/warehouse_model');
				$loss_adjustment = $this->warehouse_model->get_loss_adjustment($data['id']);
				$loss_adjustment_detail = $this->warehouse_model->get_loss_adjustment_detailt_by_masterid($data['id']);
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
				$payment_account = $data['payment_account'];
				$deposit_to = $data['deposit_to'];
				$item_amount = $data['item_amount'];
				
				$decrease_payment_account = get_option('acc_wh_decrease_payment_account');
				$decrease_deposit_to = get_option('acc_wh_decrease_deposit_to');
				$increase_payment_account = get_option('acc_wh_increase_payment_account');
				$increase_deposit_to = get_option('acc_wh_increase_deposit_to');
				
				
				foreach ($loss_adjustment_detail as $value) {
					if($value['current_number'] < $value['loss_adjustment']){
						$loss_adjustment_payment_account = $increase_payment_account;
						$loss_adjustment_deposit_to = $increase_deposit_to;
						}else{
						$loss_adjustment_payment_account = $decrease_payment_account;
						$loss_adjustment_deposit_to = $decrease_deposit_to;
					}
					
					$item_id = $value['items'];
					$item_total = $item_amount[$item_id];
					
					if(isset($payment_account[$item_id])) {
						$node = [];
						$node['split'] = $payment_account[$item_id];
						$node['account'] = $deposit_to[$item_id];
						$node['debit'] = $item_total;
						$node['date'] = date('Y-m-d', strtotime($loss_adjustment->time));
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to[$item_id];
						$node['date'] = date('Y-m-d', strtotime($loss_adjustment->time));
						$node['item'] = $item_id;
						$node['account'] = $payment_account[$item_id];
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $loss_adjustment_payment_account;
						$node['account'] = $loss_adjustment_deposit_to;
						$node['date'] = date('Y-m-d', strtotime($loss_adjustment->time));
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $loss_adjustment_deposit_to;
						$node['account'] = $loss_adjustment_payment_account;
						$node['date'] = date('Y-m-d', strtotime($loss_adjustment->time));
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
				}
				}elseif($data['type'] == 'stock_export'){
				
				$this->load->model('warehouse/warehouse_model');
				$goods_delivery = $this->warehouse_model->get_goods_delivery($data['id']);
				$goods_delivery_detail = $this->warehouse_model->get_goods_delivery_detail($data['id']);
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
				$payment_account = $data['payment_account'];
				$deposit_to = $data['deposit_to'];
				$stock_export_payment_account = get_option('acc_wh_stock_export_payment_account');
				$stock_export_deposit_to = get_option('acc_wh_stock_export_deposit_to');
				$item_amount = $data['item_amount'];
				
				foreach ($goods_delivery_detail as $value) {
					$item_id = $value['commodity_code'];
					$item_total = $item_amount[$item_id];
					
					if(isset($payment_account[$item_id])) {
						$node = [];
						$node['split'] = $payment_account[$item_id];
						$node['account'] = $deposit_to[$item_id];
						$node['debit'] = $item_total;
						$node['date'] = $goods_delivery->date_c;
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to[$item_id];
						$node['date'] = $goods_delivery->date_c;
						$node['item'] = $item_id;
						$node['account'] = $payment_account[$item_id];
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $stock_export_payment_account;
						$node['account'] = $stock_export_deposit_to;
						$node['date'] = $goods_delivery->date_c;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $stock_export_deposit_to;
						$node['account'] = $stock_export_payment_account;
						$node['date'] = $goods_delivery->date_c;
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
					if(get_option('acc_tax_automatic_conversion') == 1 && $value['tax_id'] != 0){
						$tax_payment_account = get_option('acc_expense_tax_payment_account');
						$tax_deposit_to = get_option('acc_expense_tax_deposit_to');
						
						$total_tax = $value['total_money'] - $item_total;
						
						$tax_mapping = $this->get_tax_mapping($value['tax_id']);
						
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->payment_account;
							$node['account'] = $tax_mapping->deposit_to;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['date'] = $goods_delivery->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->deposit_to;
							$node['account'] = $tax_mapping->payment_account;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['date'] = $goods_delivery->date_c;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['date'] = $goods_delivery->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['date'] = $goods_delivery->date_c;
							$node['account'] = $tax_payment_account;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				}elseif($data['type'] == 'stock_import'){
				
				$this->load->model('warehouse/warehouse_model');
				$goods_receipt = $this->warehouse_model->get_goods_receipt($data['id']);
				$goods_receipt_detail = $this->warehouse_model->get_goods_receipt_detail($data['id']);
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
				$payment_account = $data['payment_account'];
				$deposit_to = $data['deposit_to'];
				$stock_import_payment_account = get_option('acc_wh_stock_import_payment_account');
				$stock_import_deposit_to = get_option('acc_wh_stock_import_deposit_to');
				$item_amount = $data['item_amount'];
				
				foreach ($goods_receipt_detail as $value) {
					$item_id = $value['commodity_code'];
					$item_total = $item_amount[$item_id];
					
					if(isset($payment_account[$item_id])) {
						$node = [];
						$node['split'] = $payment_account[$item_id];
						$node['account'] = $deposit_to[$item_id];
						$node['debit'] = $item_total;
						$node['date'] = $goods_receipt->date_c;
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to[$item_id];
						$node['date'] = $goods_receipt->date_c;
						$node['item'] = $item_id;
						$node['account'] = $payment_account[$item_id];
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $stock_import_payment_account;
						$node['account'] = $stock_import_deposit_to;
						$node['date'] = $goods_receipt->date_c;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $stock_import_deposit_to;
						$node['account'] = $stock_import_payment_account;
						$node['date'] = $goods_receipt->date_c;
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
					if(get_option('acc_tax_automatic_conversion') == 1 && $value['tax'] != 0){
						$tax_payment_account = get_option('acc_expense_tax_payment_account');
						$tax_deposit_to = get_option('acc_expense_tax_deposit_to');
						
						$total_tax = $value['tax_money'];
						
						$tax_mapping = $this->get_tax_mapping($value['tax']);
						
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->payment_account;
							$node['account'] = $tax_mapping->deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $goods_receipt->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->deposit_to;
							$node['account'] = $tax_mapping->payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $goods_receipt->date_c;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $goods_receipt->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['date'] = $goods_receipt->date_c;
							$node['account'] = $tax_payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				}elseif($data['type'] == 'purchase_order'){
				$this->load->model('purchase/purchase_model');
				$purchase_order = $this->purchase_model->get_pur_order($data['id']);
				$purchase_order_detail = $this->purchase_model->get_pur_order_detail($data['id']);
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
				$payment_account = $data['payment_account'];
				$deposit_to = $data['deposit_to'];
				
				$expense_payment_account = get_option('acc_pur_order_payment_account');
				$expense_deposit_to = get_option('acc_pur_order_deposit_to');
				
				$item_amount = $data['item_amount'];
				foreach ($purchase_order_detail as $value) {
					$item_id = $value['item_code'];
					$item_total = $item_amount[$item_id];
					
					if(isset($payment_account[$item_id])) {
						
						$node = [];
						$node['split'] = $payment_account[$item_id];
						$node['account'] = $deposit_to[$item_id];
						$node['debit'] = $item_total;
						$node['date'] = $purchase_order->order_date;
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to[$item_id];
						$node['date'] = $purchase_order->order_date;
						$node['item'] = $item_id;
						$node['account'] = $payment_account[$item_id];
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $expense_payment_account;
						$node['account'] = $expense_deposit_to;
						$node['date'] = $purchase_order->order_date;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $expense_deposit_to;
						$node['account'] = $expense_payment_account;
						$node['date'] = $purchase_order->order_date;
						$node['item'] = $item_id;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $data['id'];
						$node['rel_type'] = $data['type'];
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
					if(get_option('acc_tax_automatic_conversion') == 1 && $value['tax'] > 0){
						$tax_payment_account = get_option('acc_expense_tax_payment_account');
						$tax_deposit_to = get_option('acc_expense_tax_deposit_to');
						
						$total_tax = $value['total'] - $value['into_money'];
						
						$tax_mapping = $this->get_tax_mapping($value['tax']);
						
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->payment_account;
							$node['account'] = $tax_mapping->deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $purchase_order->order_date;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->deposit_to;
							$node['account'] = $tax_mapping->payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $purchase_order->order_date;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $purchase_order->order_date;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['date'] = $purchase_order->order_date;
							$node['account'] = $tax_payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $data['id'];
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				
				}elseif ($data['type'] == 'payslip') {
				$date = date('Y-m-d');
				
				$node = [];
				$node['split'] = $data['payment_account_insurance'];
				$node['account'] = $data['deposit_to_insurance'];
				$node['debit'] = $data['total_insurance'];
				$node['date'] = $date;
				$node['credit'] = 0;
				$node['tax'] = 0;
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$node['payslip_type'] = 'total_insurance';
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $data['deposit_to_insurance'];
				$node['account'] = $data['payment_account_insurance'];
				$node['date'] = $date;
				$node['tax'] = 0;
				$node['debit'] = 0;
				$node['credit'] = $data['total_insurance'];
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$node['payslip_type'] = 'total_insurance';
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $data['payment_account_tax_paye'];
				$node['account'] = $data['deposit_to_tax_paye'];
				$node['debit'] = $data['tax_paye'];
				$node['date'] = $date;
				$node['credit'] = 0;
				$node['tax'] = 0;
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$node['payslip_type'] = 'tax_paye';
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $data['deposit_to_tax_paye'];
				$node['account'] = $data['payment_account_tax_paye'];
				$node['date'] = $date;
				$node['tax'] = 0;
				$node['debit'] = 0;
				$node['credit'] = $data['tax_paye'];
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$node['payslip_type'] = 'tax_paye';
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $data['payment_account_net_pay'];
				$node['account'] = $data['deposit_to_net_pay'];
				$node['debit'] = $data['net_pay'];
				$node['date'] = $date;
				$node['credit'] = 0;
				$node['tax'] = 0;
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$node['payslip_type'] = 'net_pay';
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $data['deposit_to_net_pay'];
				$node['account'] = $data['payment_account_net_pay'];
				$node['date'] = $date;
				$node['tax'] = 0;
				$node['debit'] = 0;
				$node['credit'] = $data['net_pay'];
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$node['payslip_type'] = 'net_pay';
				$data_insert[] = $node;
				
				}elseif($data['type'] == 'opening_stock'){
				$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
				
				$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
				
				$date = date('Y-m-d');
				
				$node = [];
				$node['split'] = $data['payment_account'];
				$node['account'] = $data['deposit_to'];
				$node['debit'] = $data['amount'];
				$node['date'] = $date;
				$node['credit'] = 0;
				$node['tax'] = 0;
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $data['deposit_to'];
				$node['account'] = $data['payment_account'];
				$node['date'] = $date;
				$node['tax'] = 0;
				$node['debit'] = 0;
				$node['credit'] = $data['amount'];
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$data_insert[] = $node;
				}else{
				$customer = 0;
				$date = date('Y-m-d');
				if($data['type'] == 'payment'){
					$this->load->model('payments_model');
					$this->load->model('invoices_model');
					$payment = $this->payments_model->get($data['id']);
					$date = $payment->date;
					$invoice = $this->invoices_model->get($payment->invoiceid);
					
					$this->automatic_invoice_conversion($payment->invoiceid);
					
					$customer = $invoice->clientid;
					
					$this->load->model('currencies_model');
					$currency = $this->currencies_model->get_base_currency();
					
					if(isset($data['exchange_rate'])){
						$data['amount'] = round($data['amount'] * $data['exchange_rate'], 2);
						}elseif($invoice->currency_name != $currency->name){
						$data['amount'] = round($this->currency_converter($invoice->currency_name, $currency->name, $data['amount']), 2);
					}
					}elseif ($data['type'] == 'expense') {
					$this->load->model('expenses_model');
					$expense = $this->expenses_model->get($data['id']);
					$date = $expense->date;
					$customer = $expense->clientid;
					
					$this->load->model('currencies_model');
					$currency = $this->currencies_model->get_base_currency();
					
					if(isset($data['exchange_rate'])){
						$data['amount'] = round($data['amount'] * $data['exchange_rate'], 2);
						}elseif($expense->currency_data->name != $currency->name){
						$data['amount'] = round($this->currency_converter($expense->currency_data->name, $currency->name, $data['amount']), 2);
					}
					
					if(get_option('acc_tax_automatic_conversion') == 1){
						$tax_payment_account = get_option('acc_tax_payment_account');
						$tax_deposit_to = get_option('acc_tax_deposit_to');
						
						if($expense->tax > 0){
							$this->db->where('id', $expense->tax);
							$tax = $this->db->get(db_prefix().'taxes')->row();
							$total_tax = 0;
							if($tax){
								$total_tax = ($tax->taxrate/100) * $data['amount'];
							}
							$tax_mapping = $this->get_tax_mapping($expense->tax);
							if($tax_mapping){
								$node = [];
								$node['split'] = $tax_mapping->expense_payment_account;
								$node['account'] = $tax_mapping->expense_deposit_to;
								$node['tax'] = $expense->tax;
								$node['debit'] = $total_tax;
								$node['credit'] = 0;
								$node['customer'] = $expense->clientid;
								$node['date'] = $expense->date;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								
								$node = [];
								$node['split'] = $tax_mapping->expense_deposit_to;
								$node['customer'] = $expense->clientid;
								$node['account'] = $tax_mapping->expense_payment_account;
								$node['tax'] = $expense->tax;
								$node['date'] = $expense->date;
								$node['debit'] = 0;
								$node['credit'] = $total_tax;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								}else{
								$node = [];
								$node['split'] = $tax_payment_account;
								$node['account'] = $tax_deposit_to;
								$node['tax'] = $expense->tax;
								$node['date'] = $expense->date;
								$node['debit'] = $total_tax;
								$node['customer'] = $expense->clientid;
								$node['credit'] = 0;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								
								$node = [];
								$node['split'] = $tax_deposit_to;
								$node['customer'] = $expense->clientid;
								$node['account'] = $tax_payment_account;
								$node['date'] = $expense->date;
								$node['tax'] = $expense->tax;
								$node['debit'] = 0;
								$node['credit'] = $total_tax;
								$node['description'] = '';
								$node['rel_id'] = $data['id'];
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
							}
						}
						
						if($expense->tax2 > 0){
							$this->db->where('id', $expense->tax2);
							$tax = $this->db->get(db_prefix().'taxes')->row();
							$total_tax = 0;
							if($tax){
								$total_tax = ($tax->taxrate/100) * $data['amount'];
							}
							$tax_mapping = $this->get_tax_mapping($expense->tax2);
							if($tax_mapping){
								$node = [];
								$node['split'] = $tax_mapping->expense_payment_account;
								$node['account'] = $tax_mapping->expense_deposit_to;
								$node['tax'] = $expense->tax2;
								$node['debit'] = $total_tax;
								$node['credit'] = 0;
								$node['customer'] = $expense->clientid;
								$node['date'] = $expense->date;
								$node['description'] = '';
								$node['rel_id'] = $expense_id;
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								
								$node = [];
								$node['split'] = $tax_mapping->expense_deposit_to;
								$node['customer'] = $expense->clientid;
								$node['account'] = $tax_mapping->expense_payment_account;
								$node['tax'] = $expense->tax2;
								$node['date'] = $expense->date;
								$node['debit'] = 0;
								$node['credit'] = $total_tax;
								$node['description'] = '';
								$node['rel_id'] = $expense_id;
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								}else{
								$node = [];
								$node['split'] = $tax_payment_account;
								$node['account'] = $tax_deposit_to;
								$node['tax'] = $expense->tax2;
								$node['date'] = $expense->date;
								$node['debit'] = $total_tax;
								$node['customer'] = $expense->clientid;
								$node['credit'] = 0;
								$node['description'] = '';
								$node['rel_id'] = $expense_id;
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
								
								$node = [];
								$node['split'] = $tax_deposit_to;
								$node['customer'] = $expense->clientid;
								$node['account'] = $tax_payment_account;
								$node['date'] = $expense->date;
								$node['tax'] = $expense->tax2;
								$node['debit'] = 0;
								$node['credit'] = $total_tax;
								$node['description'] = '';
								$node['rel_id'] = $expense_id;
								$node['rel_type'] = 'expense';
								$node['datecreated'] = date('Y-m-d H:i:s');
								$node['addedfrom'] = get_staff_user_id();
								$data_insert[] = $node;
							}
						}
					}
					}elseif($data['type'] == 'banking'){
					$banking = $this->get_transaction_banking($data['id']);
					if($banking){
						$date = $banking->date;
					}
					}elseif($data['type'] == 'purchase_payment'){
					$this->load->model('purchase/purchase_model');
					$payment = $this->purchase_model->get_payment_pur_invoice($data['id']);
					$date = $payment->date;
					$data['amount'] = $payment->amount;
				}
				
				$node = [];
				$node['split'] = $data['payment_account'];
				$node['account'] = $data['deposit_to'];
				$node['debit'] = $data['amount'];
				$node['customer'] = $customer;
				$node['date'] = $date;
				$node['credit'] = 0;
				$node['tax'] = 0;
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $data['deposit_to'];
				$node['account'] = $data['payment_account'];
				$node['customer'] = $customer;
				$node['date'] = $date;
				$node['tax'] = 0;
				$node['debit'] = 0;
				$node['credit'] = $data['amount'];
				$node['description'] = '';
				$node['rel_id'] = $data['id'];
				$node['rel_type'] = $data['type'];
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$data_insert[] = $node;
			}
			
			$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
			
			if ($affectedRows > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
			* add transfer
			* @param array $data
			* @return boolean
		*/
		public function add_transfer($data){
			if(isset($data['id'])){
				unset($data['id']);
			}
			$data['date'] = to_sql_date($data['date']);
			if(get_option('acc_close_the_books') == 1){
				if(strtotime($data['date']) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
					return 'close_the_book';
				}
			}
			$data['transfer_amount'] = str_replace(',', '', $data['transfer_amount']);
			$data['datecreated'] = date('Y-m-d H:i:s');
			$data['addedfrom'] = get_staff_user_id();
			
			$this->db->insert(db_prefix().'acc_transfers', $data);
			$insert_id = $this->db->insert_id();
			
			if($insert_id){
				$node = [];
				$node['split'] = $data['transfer_funds_to'];
				$node['account'] = $data['transfer_funds_from'];
				$node['debit'] = 0;
				$node['date'] = $data['date'];
				$node['credit'] = $data['transfer_amount'];
				$node['rel_id'] = $insert_id;
				$node['rel_type'] = 'transfer';
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				
				$this->db->insert(db_prefix().'acc_account_history', $node);
				
				$node = [];
				$node['split'] = $data['transfer_funds_from'];
				$node['account'] = $data['transfer_funds_to'];
				$node['debit'] = $data['transfer_amount'];
				$node['date'] = $data['date'];
				$node['credit'] = 0;
				$node['rel_id'] = $insert_id;
				$node['rel_type'] = 'transfer';
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				
				$this->db->insert(db_prefix().'acc_account_history', $node);
				
				return true;
			}
			
			return false;
		}
		
		
		public function load_data($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'accountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND PassedFrom = "JOURNAL" AND '.db_prefix().'accountledger.FY = "'.$fy.'" AND '.db_prefix().'accountledger.PlantID = "'.$selected_company.'" ORDER BY ABS(tblaccountledger.VoucherID)';
			
			$sql ='SELECT '.db_prefix().'accountledger.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'accountledger.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName, 
			(SELECT GROUP_CONCAT(firstname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledger.AccountID ) as firstname,
			(SELECT GROUP_CONCAT(lastname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledger.AccountID ) as lastname,
			(SELECT GROUP_CONCAT(AccountID SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledger.AccountID ) as staff_AccountID
			FROM '.db_prefix().'accountledger WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		
		public function load_data_for_payment($data)
		{
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$status_type = isset($data["Status"]) ? $data["Status"] : null;
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$regExp ="'.*;s:[0-9]+:'".$selected_company."'.*'";
			$regExp1 ="'.*;s:[0-9]+:";
			$regExp2 =".*'";
			
			$sql1 = '('.db_prefix().'accountledgerPending.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND PassedFrom = "PAYMENTS" AND '.db_prefix().'accountledgerPending.FY = "'.$fy.'" AND '.db_prefix().'accountledgerPending.PlantID = "'.$selected_company.'"';
			if (!empty($status_type)) {
				$sql1 .= ' AND '.db_prefix().'accountledgerPending.Status = "'.$status_type.'"';
			}
			$sql1 .= ' ORDER BY abs('.db_prefix().'accountledgerPending.VoucherID) DESC';
			
			$sql ='SELECT '.db_prefix().'accountledgerPending.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'accountledgerPending.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName, 
			(SELECT GROUP_CONCAT(firstname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as firstname,
			(SELECT GROUP_CONCAT(lastname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as lastname,
			(SELECT GROUP_CONCAT(AccountID SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as staff_AccountID
			FROM '.db_prefix().'accountledgerPending WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function load_data_for_VoucherEntry($data)
		{
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$status_type = isset($data["Status"]) ? $data["Status"] : null;
			$PassedFrom = $data["PassedFrom"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$regExp ="'.*;s:[0-9]+:'".$selected_company."'.*'";
			$regExp1 ="'.*;s:[0-9]+:";
			$regExp2 =".*'";
			
			$sql1 = '('.db_prefix().'accountledgerPending.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND PassedFrom = "'.$PassedFrom.'" AND '.db_prefix().'accountledgerPending.FY = "'.$fy.'" AND '.db_prefix().'accountledgerPending.PlantID = "'.$selected_company.'"';
			if (!empty($status_type)) {
				$sql1 .= ' AND '.db_prefix().'accountledgerPending.Status = "'.$status_type.'"';
			}
			$sql1 .= ' ORDER BY abs('.db_prefix().'accountledgerPending.VoucherID) DESC';
			
			$sql ='SELECT '.db_prefix().'accountledgerPending.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'accountledgerPending.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName, 
			(SELECT GROUP_CONCAT(firstname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as firstname,
			(SELECT GROUP_CONCAT(lastname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as lastname,
			(SELECT GROUP_CONCAT(AccountID SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as staff_AccountID
			FROM '.db_prefix().'accountledgerPending WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function load_data_for_receipts($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$entryStatus = $data["entryStatus"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'accountledgerPending.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND PassedFrom = "RECEIPTS" AND '.db_prefix().'accountledgerPending.FY = "'.$fy.'" AND '.db_prefix().'accountledgerPending.PlantID = "'.$selected_company.'"';
			if (!empty($entryStatus)) {
				$sql1 .= ' AND '.db_prefix().'accountledgerPending.Status = "'.$entryStatus.'"';
			}
			$sql1 .= ' ORDER BY abs('.db_prefix().'accountledgerPending.VoucherID) DESC';
			
			$sql ='SELECT '.db_prefix().'accountledgerPending.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'accountledgerPending.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName, 
			(SELECT GROUP_CONCAT(firstname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as firstname,
			(SELECT GROUP_CONCAT(lastname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as lastname,
			(SELECT GROUP_CONCAT(AccountID SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledgerPending.AccountID AND tblstaff.PlantID = '.$selected_company.') as staff_AccountID
			FROM '.db_prefix().'accountledgerPending WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function load_data_for_contra($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'accountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND PassedFrom = "CONTRA" AND '.db_prefix().'accountledger.FY = "'.$fy.'" AND '.db_prefix().'accountledger.PlantID = "'.$selected_company.'" ORDER BY ABS(tblaccountledger.VoucherID)';
			
			$sql ='SELECT '.db_prefix().'accountledger.*,  
			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM '.db_prefix().'clients WHERE '.db_prefix().'clients.AccountID = '.db_prefix().'accountledger.AccountID AND '.db_prefix().'clients.PlantID = '.$selected_company.') as AccountName, 
			(SELECT GROUP_CONCAT(firstname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledger.AccountID ) as firstname,
			(SELECT GROUP_CONCAT(lastname SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledger.AccountID ) as lastname,
			(SELECT GROUP_CONCAT(AccountID SEPARATOR ",") FROM '.db_prefix().'staff WHERE '.db_prefix().'staff.AccountID = '.db_prefix().'accountledger.AccountID ) as staff_AccountID
			FROM '.db_prefix().'accountledger WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		/**
			* add new journal entry
			* @param array $data 
			* @return boolean
		*/
		public function add_journal_entry($data){
			$journal_entry = json_decode($data['journal_entry']);
			/* echo "<pre>";
				print_r($journal_entry);
			die;*/
			unset($data['journal_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$journal_date = to_sql_date($data['journal_date'])." ".date('H:i:s');
			$date = to_sql_date($data['journal_date']);
			$month = substr($journal_date,5,2);
			$get_result_to_cur_date = $this->get_result_to_cur_date_journal($date);
			
			if(empty($get_result_to_cur_date)){
				$selected_company = $this->session->userdata('root_company');
				if($selected_company == 1){
					
					$new_journalNumber = get_option('next_journal_number_for_cspl');
					}elseif($selected_company == 2){
					$new_journalNumber = get_option('next_journal_number_for_cff');
					}elseif($selected_company == 3){
					$new_journalNumber = get_option('next_journal_number_for_cbu');
				}
				
				$new_voucher_number = $new_journalNumber;
				}else{ 
				
				$count = count($get_result_to_cur_date);
				$last_index = $count - 1;
				$new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
				
				$incNo = (int) $new_voucher_number - 1;
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "JOURNAL" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				if ($this->db->affected_rows() > 0) {
					$this->increment_next_journal_number();
				}
			}
			
			if($data['TransType'] == 'D'){
				$ttype = 'C';
				}else{
				$ttype = 'D';
			}
			
			$i = 1;
			$_data = array(
			"PlantID" =>$selected_company,
			"Transdate" =>$journal_date,
			"TransDate2" =>date('Y-m-d H:i:s'),
			"VoucherID" =>$new_voucher_number,
			"AccountID" =>$data['ganeral_account'],
			"EffectOn" =>null,
			"TType" =>$data['TransType'],
			"Amount" =>$data['amount'],
			"Narration" =>'',
			"PassedFrom" =>"JOURNAL",
			"OrdinalNo" =>$i,
			"UserID" =>$this->session->userdata('username'),
			"FY" =>$fy,
			);
			$this->db->insert(db_prefix().'accountledger', $_data);
			
			$i = 1;
			foreach ($journal_entry as $key => $value) {
				if($value[0] != ''){
					$_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$journal_date,
					"TransDate2" =>date('Y-m-d H:i:s'),
					"VoucherID" =>$new_voucher_number,
					"AccountID" =>$value[0],
					"EffectOn" =>$data['ganeral_account'],
					"TType" =>$ttype,
					"Against"=>$value[3],
    				"BillNo"=>$value[4],
    				"Amount" =>$value[6],
    				"Narration" =>$value[7],
					"PassedFrom" =>"JOURNAL",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					);
					$this->db->insert(db_prefix().'accountledger', $_data);
					$i++;
				}
			}
			
			if(empty($get_result_to_cur_date)){
				$this->increment_next_journal_number();
			}
			return true;
			
		}
		
		/**
			* add Contra entry
			* @param array $data 
			* @return boolean
		*/
		public function add_contra_entry($data){
			$contra_entry = json_decode($data['contra_entry']);
			unset($data['contra_entry']);
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$contra_date = to_sql_date($data['contra_date'])." ".date('H:i:s');
			$month = substr($contra_date,5,2);
			$date = to_sql_date($data['contra_date']);
			$get_result_to_cur_date = $this->get_result_to_cur_date_contra($date);
			
			if(empty($get_result_to_cur_date)){
				$selected_company = $this->session->userdata('root_company');
				if($selected_company == 1){
					
					$new_contraNumber = get_option('next_contra_number_for_cspl');
					}elseif($selected_company == 2){
					$new_contraNumber = get_option('next_contra_number_for_cff');
					}elseif($selected_company == 3){
					$new_contraNumber = get_option('next_contra_number_for_cbu');
				}
				
				$new_voucher_number = $new_contraNumber;
				
				}else{
				
				$count = count($get_result_to_cur_date);
				$last_index = $count - 1;
				$new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
				$incNo = (int) $new_voucher_number - 1;
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "CONTRA" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				if ($this->db->affected_rows() > 0) {
					$this->increment_next_contra_number();
				}
			}
			
			
			if($data['TransType'] == 'D'){
				$ttype = 'C';
				}else{
				$ttype = 'D';
			}
			
			$i = 1;
			$_data = array(
			"PlantID" =>$selected_company,
			"Transdate" =>$contra_date,
			"TransDate2" =>date('Y-m-d H:i:s'),
			"VoucherID" =>$new_voucher_number,
			"AccountID" =>$data['ganeral_account'],
			"EffectOn" =>null,
			"TType" =>$data['TransType'],
			"Amount" =>$data['amount'],
			"Narration" =>'',
			"PassedFrom" =>"CONTRA",
			"OrdinalNo" =>$i,
			"UserID" =>$this->session->userdata('username'),
			"FY" =>$fy,
			);
			$this->db->insert(db_prefix().'accountledger', $_data);
			$i = 1;
			
			foreach ($contra_entry as $key => $value) {
				if($value[0] != ''){
					
					// ledger entry
					$_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$contra_date,
					"TransDate2" =>date('Y-m-d H:i:s'),
					"VoucherID" =>$new_voucher_number,
					"AccountID" =>$value[0],
					"EffectOn" =>$data['ganeral_account'],
					"TType" =>$ttype,
					"Amount" =>$value[2],
					"Narration" =>$value[3],
					"PassedFrom" =>"CONTRA",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					);
					
					$this->db->insert(db_prefix().'accountledger', $_data);
					$i++;
				}
			}
			
			if(empty($get_result_to_cur_date)){
				$this->increment_next_contra_number();
			} 
			return true;
			
		}
		
		public function add_receipts_entry($data)
		{
			$receipts_entry = json_decode($data['receipts_entry']);
			unset($data['receipts_entry']);
			
			$data['receipts_entry'] = to_sql_date($data['receipts_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$receipt_date = to_sql_date($data['receipt_date'])." ".date('H:i:s');
			$month = substr($receipt_date,5,2);
			$date = to_sql_date($data['receipt_date']);
			$get_result_to_cur_date = $this->get_result_to_cur_date_receipts($date);
			$PassedFrom = "RECEIPTS";
			$GetLastUniqueNo = $this->GetLastUniqueNo($PassedFrom);
			$LastUniqueID = $GetLastUniqueNo[0]['UniquID'] + 1;
			if(empty($get_result_to_cur_date)){
				if($selected_company == 1){					
					$new_tax_transactionNumber = get_option('next_receipts_number_for_cspl');
					}elseif($selected_company == 2){
					$new_tax_transactionNumber = get_option('next_receipts_number_for_cff');
					}elseif($selected_company == 3){
					$new_tax_transactionNumber = get_option('next_receipts_number_for_cbu');
				}
				$new_voucher_number = $new_tax_transactionNumber;
				}else{				
				$count = count($get_result_to_cur_date);
				$last_index = $count - 1;
				$new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
				
				$incNo = (int) $new_voucher_number - 1;
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "RECEIPTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				$sql = 'UPDATE tblaccountledgerPending SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "RECEIPTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				if ($this->db->affected_rows() > 0) {
					$this->increment_next_receipts_number();
					$sql2 = 'UPDATE tblReconsileMaster SET TransID = abs(TransID) + 1 where abs(TransID) > "'.$incNo.'" AND PassedFrom = "RECEIPT"';
					$this->db->query($sql2);
				}
			}			
			$i = 1;
			foreach ($receipts_entry as $key => $value) 
			{			 
				if($value[0] != ''){				
					// Ledger Entry
					$credit_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$receipt_date,
					"TransDate2" =>date('Y-m-d H:i:s'),
					"VoucherID" =>$new_voucher_number,
					"AccountID" =>$value[0],
					"EffectOn" =>$data['ganeral_account'],
					"TType" =>"C",
					"Against"=>$value[3],
					"BillNo"=>$value[4],
					"Amount" =>$value[6],
					"Narration" =>$value[7],
					"PassedFrom" =>"RECEIPTS",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					"UniquID" =>$LastUniqueID,
					);
					$this->db->insert(db_prefix().'accountledgerPending', $credit_data);
					
					$debit_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$receipt_date,
					"TransDate2" =>date('Y-m-d H:i:s'),
					"VoucherID" =>$new_voucher_number,
					"AccountID" =>$data['ganeral_account'],
					"EffectOn" =>$value[0],
					"TType" =>"D",
					"Against"=>$value[3],
					"BillNo"=>$value[4],
					"Amount" =>$value[6],
					"Narration" =>$value[7],
					"PassedFrom" =>"RECEIPTS",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					"UniquID" =>$LastUniqueID,
					);
					$this->db->insert(db_prefix().'accountledgerPending', $debit_data);
					$i++;
				}
				
				if(($value[3]=="AGAINST" || $value[3]=="Against") && $value[0] != '' && $value[4] != '')
				{           
					
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('EffectOn', $value[4]);
					$this->db->where('TType', 'CR');      
					$info = $this->db->get()->row();      
					$billid = $info->BillID;    
					
					$this->db->select('*');
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->group_start();  // Start grouping conditions
					$this->db->where('EffectOn', $value[4]);
					$this->db->or_where('TransID', $value[4]);
					$this->db->group_end();    // End grouping conditions
					$invoiceData = $this->db->get()->result_array();                
					
					$this->db->select('*'); 
					$this->db->from(db_prefix() . 'ReconsileMaster'); 
					$this->db->where('TransID', $value[4]);
					$this->db->where('TType', 'DR');
					$dtypeamt = $this->db->get()->row(); 
					$amt = $dtypeamt->Amount;
					
					$typeCAmount = null;
					$typeDAmount = null;      
					foreach ($invoiceData as $row) {
						if ($row['TType'] == 'CR') {
							$typeCAmount += $row['Amount'];
							} elseif ($row['TType'] == 'DR') {
							$typeDAmount += $row['Amount'];
						}
					}                                     
					
					if(!empty($info))
					{
						if ($typeCAmount !== null && $typeDAmount !== null && $invoiceData) {               
							$difference = abs($typeCAmount - $typeDAmount);
							if($difference && $difference > 0)
							{
								if($value[6] == $difference)
								{  $amount = $difference;  }
								else if($value[6] > $difference) 
								{ $amount = $difference;}
								else if($value[6] < $difference) 
								{ $amount = $value[6]; }                           
							}
							else if($difference == 0)
							{
								$amount = $value[6];
							} 
							
							$reconciliation = array(
							"TransID"=>$new_voucher_number,
							"EffectOn"=>$value[4],
							"TransDate"=>date('Y-m-d H:i:s'),
							"AccountID"=>$value[0],
							"Amount"=>$amount,
							"TType"=>"CR",
							"Status"=>"Y",
							"PassedFrom"=>"RECEIPT",
							"UserID"=>$this->session->userdata('username'),
							);
							$insertdata = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);
							if($insertdata && ($value[6] == $difference || $value[6] > $difference))
							{
								// echo "ok";die;	
								$updatestatus = array(
								"Status"=>"Y"
								);                           
								$this->db->where('TType', 'DR');
								$this->db->where('TransID', $value[3]);
								$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
								// echo "ok";die;
							}                                              
						}
					}
					else if(empty($info))
					{                     
						if($dtypeamt && $amt)
						{
							if($value[6] > $amt)
							{
								$diff = $amt;
							}  
							else if ($value[6] < $amt)    
							{
								$diff = $value[6];
							}   
							else if($value[6] == $amt)   
							{
								$diff = $amt;
							}
						}
						else
						{                       
							$diff = $value[6];
						}           
						
						$reconciliation = array(
						"TransID"=>$new_voucher_number,
						"EffectOn"=>$value[4],
						"TransDate"=>date('Y-m-d H:i:s'),
						"AccountID"=>$value[0],
						"Amount"=>$diff,
						"TType"=>"CR",
						"Status"=>"Y",
						"PassedFrom"=>"RECEIPT",
						"UserID"=>$this->session->userdata('username'),
						);
						$insertdetails = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);  
						if($insertdetails && ($value[6] == $amt || $value[6] > $amt))
						{
							$updatestatus = array(
							"Status"=>"Y"
							);                           
							$this->db->where('TType', 'DR');
							$this->db->where('TransID', $value[4]);
							$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
						}  
					}              
				}
			}
			if(empty($get_result_to_cur_date)){
				$this->increment_next_receipts_number();
			}
			return true;
		}
		
		
		/**
			* @since  2.7.0
			*
			* Increment the Receipts next nubmer
			*
			* @return void
		*/
		public function increment_next_receipts_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_receipts_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_receipts_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_receipts_number_for_cbu');
				
			}
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		
		
		/**
			* @since  2.7.0
			*
			* decrement the Receipts next nubmer
			*
			* @return void
		*/
		public function decrement_receipts_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year'); 
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_receipts_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_receipts_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_receipts_number_for_cbu');
				
			}
			$this->db->set('value', 'value-1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		
		/**
			* @since  2.7.0
			*
			* decrement the Receipts next nubmer
			*
			* @return void
		*/
		public function decrement_payments_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_payment_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_payment_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_payment_number_for_cbu');
				
			}
			$this->db->set('value', 'value-1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		
		/**
			* @since  2.7.0
			*
			* decrement the Journal next nubmer
			*
			* @return void
		*/
		public function decrement_journal_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year'); 
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_journal_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_journal_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_journal_number_for_cbu');
				
			}
			$this->db->set('value', 'value-1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		
		/**
			* @since  2.7.0
			*
			* decrement the Contra next nubmer
			*
			* @return void
		*/
		public function decrement_contra_number()
		{
			
			$FY = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_contra_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_contra_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_contra_number_for_cbu');
				
			}
			$this->db->set('value', 'value-1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		/**
			* @since  2.7.0
			*
			* Increment the COntra next nubmer
			*
			* @return void
		*/
		public function increment_next_contra_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_contra_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_contra_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_contra_number_for_cbu');
				
			}
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		
		/**
			* @since  2.7.0
			*
			* Increment the COntra next nubmer
			*
			* @return void
		*/
		public function increment_next_journal_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_journal_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_journal_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_journal_number_for_cbu');
				
			}
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		/**
			* @since  2.7.0
			*
			* Increment the challan next nubmer
			*
			* @return void
		*/
		public function increment_next_payment_number()
		{
			// Update next CHALLAN number in settings
			$FY = $this->session->userdata('finacial_year'); 
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == 1){
				$this->db->where('name', 'next_payment_number_for_cspl');
				
				}elseif($selected_company == 2){
				$this->db->where('name', 'next_payment_number_for_cff');
				
				}elseif($selected_company == 3){
				$this->db->where('name', 'next_payment_number_for_cbu');
				
			}
			$this->db->set('value', 'value+1', false);
			$this->db->WHERE('FY', $FY);
			$this->db->update(db_prefix() . 'options');
		}
		
		public function add_payment_entry($data)
		{
			$payment_entry = json_decode($data['payment_entry']);
			unset($data['payment_entry']);
			$data['payment_entry'] = to_sql_date($data['payment_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$payment_date = to_sql_date($data['payment_date'])." ".date('H:i:s');
			$date= to_sql_date($data['payment_date']);
			$month = substr($payment_date,5,2);
			$get_result_to_cur_date = $this->get_result_to_cur_date_payments($date);
			$PassedFrom = "PAYMENTS";
			$GetLastUniqueNo = $this->GetLastUniqueNo($PassedFrom);
			$LastUniqueID = $GetLastUniqueNo[0]['UniquID'] + 1;
			
			if(empty($get_result_to_cur_date)){
				if($selected_company == 1){
					$new_tax_transactionNumber = get_option('next_payment_number_for_cspl');
					}elseif($selected_company == 2){
					$new_tax_transactionNumber = get_option('next_payment_number_for_cff');
					}elseif($selected_company == 3){
					$new_tax_transactionNumber = get_option('next_payment_number_for_cbu');
				}
				$new_voucher_number = $new_tax_transactionNumber;
				}else{
				$count = count($get_result_to_cur_date);
				$last_index = $count - 1;
				$new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
				
				$incNo = (int) $new_voucher_number - 1;
				$sql = 'UPDATE tblaccountledgerPending SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "PAYMENTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				if ($this->db->affected_rows() > 0) {
					$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "PAYMENTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
					$this->db->query($sql);
					$this->increment_next_payment_number();
					$sql2 = 'UPDATE tblReconsileMaster SET TransID = abs(TransID) + 1 where abs(TransID) > "'.$incNo.'" AND PassedFrom = "PAYMENT"';
					$this->db->query($sql2);
				}
			}            
			$i = 1;
			foreach ($payment_entry as $key => $value) {
				if($value[0] != ''){
					// Insert Ledger Entry
					$credit_data = array(
    				"PlantID" =>$selected_company,
    				"Transdate" =>$payment_date,
    				"TransDate2" =>$payment_date,
    				"VoucherID" =>$new_voucher_number,
    				"AccountID" =>$value[0],
    				"EffectOn" =>$data['ganeral_account'],
    				"TType" =>"D",
    				"Against"=>$value[3],
    				"BillNo"=>$value[4],
    				"Amount" =>$value[6],
    				"Narration" =>$value[7],
    				"PassedFrom" =>"PAYMENTS",
    				"Status"=>"N",
    				"OrdinalNo" =>$i,
    				"UserID" =>$this->session->userdata('username'),
    				"FY" =>$fy,
    				"UniquID" =>$LastUniqueID,
					); 
					$this->db->insert(db_prefix().'accountledgerPending', $credit_data);
					
					$debit_data = array(
    				"PlantID" =>$selected_company,
    				"Transdate" =>$payment_date,
    				"TransDate2" =>$payment_date,
    				"VoucherID" =>$new_voucher_number,
    				"AccountID" =>$data['ganeral_account'],
    				"EffectOn" =>$value[0],
    				"TType" =>"C",
    				"Against"=>$value[3],
    				"BillNo"=>$value[4],
    				"Amount" =>$value[6],
    				"Narration" =>$value[7],
    				"PassedFrom" =>"PAYMENTS",
    				"Status"=>"N",
    				"OrdinalNo" =>$i,
    				"UserID" =>$this->session->userdata('username'),
    				"FY" =>$fy,
    				"UniquID" =>$LastUniqueID,
					);
					$this->db->insert(db_prefix().'accountledgerPending', $debit_data);
					$i++;					 
				}
				
				if(($value[3]=="AGAINST" || $value[3]=="Against") && $value[0] != '' && $value[4] != '')
				{  				
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('EffectOn', $value[4]);
					$this->db->where('TType', 'DR');      
					$info = $this->db->get()->row();      
					$billid = $info->BillID;    
					
					$this->db->select('*');
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->group_start();  // Start grouping conditions
					$this->db->where('EffectOn', $value[4]);
					$this->db->or_where('TransID', $value[4]);
					$this->db->group_end();    // End grouping conditions
					$invoiceData = $this->db->get()->result_array();                
					
					$this->db->select('*'); 
					$this->db->from(db_prefix() . 'ReconsileMaster'); 
					$this->db->where('TransID', $value[4]);
					$this->db->where('TType', 'CR');
					$ctypeamt = $this->db->get()->row(); 
					$amt = $ctypeamt->Amount;
					
					$typeCAmount = null;
					$typeDAmount = null;      
					foreach ($invoiceData as $row) {
						if ($row['TType'] == 'CR') {
							$typeCAmount += $row['Amount'];
							} elseif ($row['TType'] == 'DR') {
							$typeDAmount += $row['Amount'];
						}
					}                                     
					
					if(!empty($info))
					{
						if ($typeCAmount !== null && $typeDAmount !== null && $invoiceData) {               
							$difference = abs($typeCAmount - $typeDAmount);
							if($difference && $difference > 0)
							{
								if($value[6] == $difference)
								{  $amount = $difference;  }
								else if($value[6] > $difference) 
								{ $amount = $difference;}
								else if($value[6] < $difference) 
								{ $amount = $value[6]; }                           
							}
							else if($difference == 0)
							{
								$amount = $value[6];
							} 
							
							$reconciliation = array(
    						"TransID"=>$new_voucher_number,
    						"EffectOn"=>$value[4],
    						"TransDate"=>$payment_date,
    						"AccountID"=>$value[0],
    						"Amount"=>$amount,
    						"TType"=>"DR",
    						"Status"=>"Y",
    						"PassedFrom"=>"PAYMENT",
    						"UserID"=>$this->session->userdata('username'),
							);
							$insertdata = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);
							if($insertdata && ($value[6] == $difference || $value[6] > $difference))
							{
								// echo "ok";die;	
								$updatestatus = array(
							    "Status"=>"Y"
								);                           
								$this->db->where('TType', 'CR');
								$this->db->where('TransID', $value[4]);
								$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
								// echo "ok";die;
							}                                              
						}
					}else if(empty($info))
					{                     
						if($ctypeamt && $amt)
						{
							if($value[6] > $amt)
							{
								$diff = $amt;
							}  
							else if ($value[6] < $amt)    
							{
								$diff = $value[6];
							}   
							else if($value[6] == $amt)   
							{
								$diff = $amt;
							}
						}
						else
						{                       
							$diff = $value[6];
						}					
						$reconciliation = array(
    					"TransID"=>$new_voucher_number,
    					"EffectOn"=>$value[4],
    					"TransDate"=>$payment_date,
    					"AccountID"=>$value[0],
    					"Amount"=>$diff,
    					"TType"=>"DR",
    					"Status"=>"Y",
    					"PassedFrom"=>"PAYMENT",
    					"UserID"=>$this->session->userdata('username'),
						);
						$insertdetails = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);  
						if($insertdetails && ($value[6] == $amt || $value[6] > $amt))
						{
							$updatestatus = array(
							"Status"=>"Y"
							);                           
							$this->db->where('TType', 'CR');
							$this->db->where('TransID', $value[4]);
							$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
						}  
					}              
				}
			}
			if(empty($get_result_to_cur_date)){
				$this->increment_next_payment_number();
			}
			return true;			
		}
		
		
		
		public function get_acc_bal($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('AccountID', $id);
			return $this->db->get(db_prefix() . 'accountbalances')->row();
		}
		
		public function get_last_ledger_amtjournal($id,$account_id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('AccountID', $account_id);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			return $this->db->get(db_prefix() . 'accountledger')->row();
		}
		
		public function get_last_ledger_amtcontra($id,$account_id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('AccountID', $account_id);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "CONTRA");
			return $this->db->get(db_prefix() . 'accountledger')->row();
		}
		
		public function get_last_ledger_amtreceipts($id,$account_id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('AccountID', $account_id);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			return $this->db->get(db_prefix() . 'accountledger')->row();
		}
		
		public function get_last_ledger_amtpayments($id,$account_id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('AccountID', $account_id);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			return $this->db->get(db_prefix() . 'accountledger')->row();
		}
		
		public function get_all_ledger_amtreceipts($id,$account_id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('AccountID', $account_id);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			return $this->db->get(db_prefix() . 'accountledger')->result_array();
		}
		
		public function get_all_ledger_amtpayment($id,$account_id)
		{
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('AccountID', $account_id);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			return $this->db->get(db_prefix() . 'accountledger')->result_array();
		}
		
		/**
			* get data balance sheet
			* @param  array $data_filter
			* @return array           
		*/
		public function get_data_balance_sheet($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_balance_sheet_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 10 || $value['account_type_id'] == 7 || $value['account_type_id'] == 6){
							$data_report[$data_key][] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $child_account];
							$total += $credits - $debits;
							}else{
							$data_report[$data_key][] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $child_account];
							$total += $debits - $credits;
						}
						
					}
				}
				$data_total[$data_key] = $total;
			}
			
			$data_total_2 = [];
			foreach ($data_accounts as $data_key => $data_account) {
				if($data_key != 'income' && $data_key != 'other_income' && $data_key != 'cost_of_sales' && $data_key != 'expenses' && $data_key != 'other_expenses'){
					continue;
				}
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 10 || $value['account_type_id'] == 7){
							$total += $credits - $debits;
							}else{
							$total += $debits - $credits;
						}
						
					}
				}
				$data_total_2[$data_key] = $total;
			}
			
			$income = $data_total_2['income'] + $data_total_2['other_income'];
			$expenses = $data_total_2['expenses'] + $data_total_2['other_expenses'] + $data_total_2['cost_of_sales'];
			$net_income = $income - $expenses;
			
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date, 'net_income' => $net_income];
			
		}
		
		/**
			* get data balance sheet comparison
			* @param  array $data_filter 
			* @return array           
		*/
		public function get_data_balance_sheet_comparison($data_filter){
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$last_from_date = date('Y-m-d', strtotime($from_date.' - 1 year'));
			$last_to_date = date('Y-m-d', strtotime($to_date.' - 1 year'));
			$this_year = date('Y', strtotime($to_date));
			$last_year = date('Y', strtotime($last_to_date));
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				$py_total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date_format(datecreated, \'%Y-%m-%d\') >= "' . $last_from_date . '" and date_format(datecreated, \'%Y-%m-%d\') <= "' . $last_to_date . '")');
						$py_account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$py_credits = $py_account_history->credit != '' ? $py_account_history->credit : 0;
						$py_debits = $py_account_history->debit != '' ? $py_account_history->debit : 0;
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_balance_sheet_comparison_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers);
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 10 || $value['account_type_id'] == 7 || $value['account_type_id'] == 6){
							$data_report[$data_key][] = ['name' => $name, 'amount' => ($credits - $debits), 'py_amount' => ($py_credits - $py_debits), 'child_account' => $child_account];
							$total += $credits - $debits;
							$py_total += $py_credits - $py_debits;
							}else{
							$data_report[$data_key][] = ['name' => $name, 'amount' => ($debits - $credits), 'py_amount' => ($py_debits - $py_credits), 'child_account' => $child_account];
							$total += $debits - $credits;
							$py_total += $py_debits - $py_credits;
						}
					}
				}
				$data_total[$data_key] = ['this_year' => $total, 'last_year' => $py_total];
			}
			
			$data_total_2 = [];
			foreach ($data_accounts as $data_key => $data_account) {
				if($data_key != 'income' && $data_key != 'other_income' && $data_key != 'cost_of_sales' && $data_key != 'expenses' && $data_key != 'other_expenses'){
					continue;
				}
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date_format(datecreated, \'%Y-%m-%d\') >= "' . $last_from_date . '" and date_format(datecreated, \'%Y-%m-%d\') <= "' . $last_to_date . '")');
						$py_account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$py_credits = $py_account_history->credit != '' ? $py_account_history->credit : 0;
						$py_debits = $py_account_history->debit != '' ? $py_account_history->debit : 0;
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 10 || $value['account_type_id'] == 7){
							$total += $credits - $debits;
							$py_total += $py_credits - $py_debits;
							}else{
							$total += $debits - $credits;
							$py_total += $py_debits - $py_credits;
						}
						
					}
				}
				$data_total_2[$data_key] = ['this_year' => $total, 'last_year' => $py_total];
			}
			
			$this_income = $data_total_2['income']['this_year'] + $data_total_2['other_income']['this_year'];
			$this_expenses = $data_total_2['expenses']['this_year'] + $data_total_2['other_expenses']['this_year'] + $data_total_2['cost_of_sales']['this_year'];
			$this_net_income = $this_income - $this_expenses;
			
			$last_income = $data_total_2['income']['last_year'] + $data_total_2['other_income']['last_year'];
			$last_expenses = $data_total_2['expenses']['last_year'] + $data_total_2['other_expenses']['last_year'] + $data_total_2['cost_of_sales']['last_year'];
			$last_net_income = $last_income - $last_expenses;
			
			return ['data' => $data_report, 'total' => $data_total, 'this_year' => $this_year, 'last_year' => $last_year, 'from_date' => $from_date, 'to_date' => $to_date, 'this_net_income' => $this_net_income, 'last_net_income' => $last_net_income];
		}
		
		/**
			* get data balance sheet detail
			* @param  array $data_filter 
			* @return array           
		*/
		public function get_data_balance_sheet_detail($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				if($data_key != 'income' && $data_key != 'other_income' && $data_key != 'cost_of_sales' && $data_key != 'expenses' && $data_key != 'other_expenses'){
					$data_report[$data_key] = [];
					$total = 0;
					$balance_total = 0;
					foreach ($data_account as $key => $value) {
						$this->db->where('active', 1);
						$this->db->where('(parent_account is null or parent_account = 0)');
						$this->db->where('account_detail_type_id', $value['id']);
						$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
						foreach ($accounts as $val) {
							$this->db->where('account', $val['id']);
							if($accounting_method == 'cash'){
								$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
							}
							
							$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
							$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
							$node = [];
							$balance = 0;
							$amount = 0;
							foreach ($account_history as $v) {
								if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 10 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 7){
									$am = $v['credit'] - $v['debit'];
									}else{
									$am = $v['debit'] - $v['credit'];
								}
								
								$node[] =   [
								'date' => date('Y-m-d', strtotime($v['date'])),
								'type' => _l($v['rel_type']),
								'description' => $v['description'],
								'debit' => $v['debit'],
								'credit' => $v['credit'],
								'amount' => $am,
								'balance' => $balance + $am,
								];
								$amount += $am;
								$balance += $am;
							}
							
							if($acc_show_account_numbers == 1 && $val['number'] != ''){
								$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
								}else{
								$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
							}
							
							
							$child_account = $this->get_data_balance_sheet_detail_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
							
							$data_report[$data_key][] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' => $child_account];
							
							
							$total += $amount;
							$balance_total += $balance;
						}
					}
					$data_total[$data_key] = ['amount' => $total, 'balance' => $balance_total];
				}
			}
			$data_total_2 = [];
			foreach ($data_accounts as $data_key => $data_account) {
				if($data_key != 'income' && $data_key != 'other_income' && $data_key != 'cost_of_sales' && $data_key != 'expenses' && $data_key != 'other_expenses'){
					continue;
				}
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 10 || $value['account_type_id'] == 7){
							$total += $credits - $debits;
							}else{
							$total += $debits - $credits;
						}
						
					}
				}
				$data_total_2[$data_key] = $total;
			}
			
			$income = $data_total_2['income'] + $data_total_2['other_income'];
			$expenses = $data_total_2['expenses'] + $data_total_2['other_expenses'] + $data_total_2['cost_of_sales'];
			$net_income = $income - $expenses;
			
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date, 'net_income' => $net_income];
			
		}
		
		/**
			* get data balance sheet summary
			* @param  array $data_filter 
			* @return array           
		*/
		public function get_data_balance_sheet_summary($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_balance_sheet_summary_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 10 || $value['account_type_id'] == 7){
							$data_report[$data_key][] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $child_account];
							$total += $credits - $debits;
							}else{
							$data_report[$data_key][] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $child_account];
							$total += $debits - $credits;
						}
					}
				}
				$data_total[$data_key] = $total;
			}
			
			$income = $data_total['income'] + $data_total['other_income'];
			$expenses = $data_total['expenses'] + $data_total['other_expenses'] + $data_total['cost_of_sales'];
			$net_income = $income - $expenses;
			
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date, 'net_income' => $net_income];
			
		}
		
		/**
			* get data custom summary report
			* @param  array $data_filter 
			* @return array           
		*/
		public function get_data_custom_summary_report($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_custom_summary_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$data_report[$data_key][] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $child_account];
							}else{
							$data_report[$data_key][] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $child_account];
						}
					}
				}
			}
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data profit and loss as of total income
			* @param  array $data_filter
			* @return array             
		*/
		public function get_data_profit_and_loss_as_of_total_income($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_percent = [];
			
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			$total = 0;
			foreach ($data_accounts['income'] as $value) {
				$this->db->where('active', 1);
				$this->db->where('account_detail_type_id', $value['id']);
				$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
				foreach ($accounts as $val) {
					$this->db->select('sum(credit) as credit, sum(debit) as debit');
					$this->db->where('account', $val['id']);
					if($accounting_method == 'cash'){
						$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
					}
					$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
					$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
					$credits = $account_history->credit != '' ? $account_history->credit : 0;
					$debits = $account_history->debit != '' ? $account_history->debit : 0;
					if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
						$total += $credits - $debits;
						}else{
						$total += $debits - $credits;
					}
				}
			}
			$data_total['income'] = $total;
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				$percent = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$r_am = $credits - $debits;
							$total += $credits - $debits;
							}else{
							$r_am = $debits - $credits;
							$total += $debits - $credits;
						}
						
						$child_account = $this->get_data_profit_and_loss_as_of_total_income_recursive([], $data_total['income'], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						if($data_total['income'] != 0){
							$data_report[$data_key][] = ['name' => $name, 'amount' => $r_am, 'percent' => round((($r_am) / $data_total['income']) * 100, 2), 'child_account' => $child_account];
							}else{
							$data_report[$data_key][] = ['name' => $name, 'amount' => $r_am, 'percent' => 0, 'child_account' => $child_account];
						}
					}
				}
				$data_total[$data_key] = $total;
				if($data_total['income'] != 0){
					$data_percent[$data_key] = round(($total / $data_total['income']) * 100, 2);
					}else{
					$data_percent[$data_key] = 0;
				}
			}
			
			return ['data' => $data_report, 'total' => $data_total, 'percent' => $data_percent, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data profit and loss comparison
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss_comparison($data_filter){
			$this_year = date('Y');
			$last_year = $this_year - 1;
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$last_from_date = date('Y-m-d', strtotime($from_date.' - 1 year'));
			$last_to_date = date('Y-m-d', strtotime($to_date.' - 1 year'));
			$this_year = date('Y', strtotime($to_date));
			$last_year = date('Y', strtotime($last_to_date));
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_this_year = [];
			$data_last_year = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				$py_total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date_format(datecreated, \'%Y-%m-%d\') >= "' . $last_from_date . '" and date_format(datecreated, \'%Y-%m-%d\') <= "' . $last_to_date . '")');
						$py_account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$py_credits = $py_account_history->credit != '' ? $py_account_history->credit : 0;
						$py_debits = $py_account_history->debit != '' ? $py_account_history->debit : 0;
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_profit_and_loss_comparison_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers);
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$data_report[$data_key][] = ['name' => $name, 'this_year' => $credits - $debits, 'last_year' => $py_credits - $py_debits, 'child_account' => $child_account];
							}else{
							$data_report[$data_key][] = ['name' => $name, 'this_year' => $debits - $credits, 'last_year' => $py_debits - $py_credits, 'child_account' => $child_account];
						}
					}
				}
			}
			
			return ['data' => $data_report, 'this_year_header' => $this_year, 'last_year_header' => $last_year, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data profit and loss detail
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss_detail($data_filter){
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				$balance_total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						$node = [];
						$balance = 0;
						$amount = 0;
						foreach ($account_history as $v) {
							if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
								$am = $v['credit'] - $v['debit'];
								}else{
								$am = $v['debit'] - $v['credit'];
							}
							$node[] =   [
							'date' => date('Y-m-d', strtotime($v['date'])),
							'type' => _l($v['rel_type']),
							'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
							'description' => $v['description'],
							'customer' => $v['customer'],
							'amount' => $am,
							'balance' => $balance + $am,
							];
							$amount += $am;
							$balance += $am;
						}
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						$child_account = $this->get_data_profit_and_loss_detail_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						$data_report[$data_key][] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' => $child_account];
						
						$total += $amount;
						$balance_total += $balance;
					}
				}
				$data_total[$data_key] = ['amount' => $total, 'balance' => $balance_total];
			}
			
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data profit and loss year to date comparison
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss_year_to_date_comparison($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$last_from_date = date('Y-01-01');
			$last_to_date = date('Y-03-t');
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->where('(date_format(datecreated, \'%Y-%m-%d\') >= "' . $last_from_date . '" and date_format(datecreated, \'%Y-%m-%d\') <= "' . $last_to_date . '")');
						$py_account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$py_credits = $py_account_history->credit != '' ? $py_account_history->credit : 0;
						$py_debits = $py_account_history->debit != '' ? $py_account_history->debit : 0;
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_profit_and_loss_year_to_date_comparison_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers);
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$data_report[$data_key][] = ['name' => $name, 'this_year' => $credits - $debits, 'last_year' => $py_credits - $py_debits, 'child_account' => $child_account];
							}else{
							$data_report[$data_key][] = ['name' => $name, 'this_year' => $debits - $credits, 'last_year' => $py_debits - $py_credits, 'child_account' => $child_account];
						}
					}
				}
			}
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data profit and loss
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_profit_and_loss_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$data_report[$data_key][] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $child_account];
							}else{
							$data_report[$data_key][] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $child_account];
						}
					}
				}
			}
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data statement of cash flows
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_statement_of_cash_flows($data_filter){
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			$data_accounts['cash_flows_from_operating_activities'] = [];
			$data_accounts['cash_flows_from_financing_activities'] = [];
			$data_accounts['cash_flows_from_investing_activities'] = [];
			$data_accounts['cash_and_cash_equivalents_at_beginning_of_year'] = [];
			
			foreach ($account_type_details as $key => $value) {
				if(isset($value['statement_of_cash_flows'])){
					$data_accounts[$value['statement_of_cash_flows']][] = $value;
					continue;
				}
				
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					if($value['id'] == 13){
						$data_accounts['current_assets_3'][] = $value;
						}elseif($value['id'] == 3 || $value['id'] == 6){
						$data_accounts['current_assets_2'][] = $value;
						}else{
						$data_accounts['current_assets_1'][] = $value;
					}
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					if($value['id'] == 21 || $value['id'] == 26){
						$data_accounts['fixed_assets_2'][] = $value;
						}else{
						$data_accounts['fixed_assets_1'][] = $value;
					}
				}
				if($value['account_type_id'] == 5){
					if($value['id'] != 31){
						$data_accounts['non_current_assets_2'][] = $value;
						}else{
						$data_accounts['non_current_assets_1'][] = $value;
					}
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					if($value['id'] != 63 && $value['id'] != 64){
						$data_accounts['non_current_liabilities_2'][] = $value;
						}else{
						$data_accounts['non_current_liabilities_1'][] = $value;
					}
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						if($val['id'] == 13){
							$this->db->where('(rel_type != "invoice" and rel_type != "expense" and rel_type != "payment")');
						}
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('account', $val['id']);
						
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_statement_of_cash_flows_recursive([], $val['id'], $value['account_type_id'], $value['id'], $from_date, $to_date, $acc_show_account_numbers);
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 10 || $value['account_type_id'] == 8 || $value['account_type_id'] == 7 || $value['account_type_id'] == 4 || $value['account_type_id'] == 5 || $value['account_type_id'] == 6 || $value['account_type_id'] == 2 || $value['account_type_id'] == 9 || $value['account_type_id'] == 1){
							$data_report[$data_key][] = ['account_detail_type_id' => $value['id'], 'name' => $name, 'amount' => $credits - $debits, 'child_account' => $child_account];
							$total += $credits - $debits;
							}else{
							$data_report[$data_key][] = ['account_detail_type_id' => $value['id'], 'name' => $name, 'amount' => $debits - $credits, 'child_account' => $child_account];
							$total += $debits - $credits;
						}
					}
				}
				$data_total[$data_key] = $total;
			}
			
			$income = $data_total['income'] + $data_total['other_income'];
			$expenses = $data_total['expenses'] + $data_total['other_expenses'] + $data_total['cost_of_sales'];
			$net_income = $income - $expenses;
			
			return ['data' => $data_report, 'total' => $data_total, 'net_income' => $net_income, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data statement of changes in equity
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_statement_of_changes_in_equity($data_filter){
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_statement_of_changes_in_equity_recursive([], $val['id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						$data_report[$data_key][] = ['account_detail_type_id' => $value['id'], 'name' => $name, 'amount' => $credits - $debits, 'child_account' => $child_account];
						$total += $credits - $debits;
						
					}
				}
				$data_total[$data_key] = $total;
			}
			
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data deposit detail
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_deposit_detail($data_filter){
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				$balance_total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('account', $val['id']);
						$this->db->where('((rel_type = "payment" and debit > 0) or (rel_type = "deposit"  and credit > 0))');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						$node = [];
						$balance = 0;
						$amount = 0;
						foreach ($account_history as $v) {
							if($value['account_type_id'] == 10 || $value['account_type_id'] == 9 || $value['account_type_id'] == 8 || $value['account_type_id'] == 7){
								$amount += $v['credit'] - $v['debit'];
								$am = ($v['credit'] - $v['debit']);
								}else{
								$amount += $v['debit'] - $v['credit'];
								$am = ($v['debit'] - $v['credit']);
							}
							
							$node[] =   [
							'date' => date('Y-m-d', strtotime($v['date'])),
							'type' => _l($v['rel_type']),
							'description' => $v['description'],
							'customer' => $v['customer'],
							'debit' => $v['debit'],
							'credit' => $v['credit'],
							'amount' =>  $am,
							];
						}
						
						$child_account = $this->get_data_deposit_detail_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $acc_show_account_numbers);
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						$data_report[$data_key][] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'details' => $node, 'child_account' => $child_account];
						
						$total += $amount;
						$balance_total += $balance;
					}
				}
				$data_total[$data_key] = ['amount' => $total, 'balance' => $balance_total];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data income by customer summary
			* @return array
		*/
		public function get_data_income_by_customer_summary($data_filter){
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			$list_customer = [];
			foreach ($data_accounts as $data_key => $data_account) {
				$total = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit, customer');
						$this->db->group_by('customer');
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('(customer != 0)');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						
						foreach ($account_history as $v) {
							$credits = $v['credit'] != '' ? $v['credit'] : 0;
							$debits = $v['debit'] != '' ? $v['debit'] : 0;
							
							if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
								$amount = $credits - $debits;
								}else{
								$amount = $debits - $credits;
							}
							
							if(isset($total[$v['customer']])){
								$total[$v['customer']] += $amount;
								}else{
								$total[$v['customer']] = $amount;
							}
							
							if(!in_array($v['customer'], $list_customer)){
								$list_customer[] = $v['customer'];
							}
						}
					}
				}
				$data_total[$data_key] = $total;
			}
			
			return ['list_customer' => $list_customer, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data check detail
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_check_detail($data_filter){
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('rel_type', 'expense');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						$data_report[$data_key][] = ['account_detail_type_id' => $value['id'], 'name' => $name, 'details' => $account_history];
					}
				}
			}
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data account list
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_account_list($data_filter){
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$account_types = $this->get_account_types();
			$detail_types = $this->get_account_type_details();
			
			$account_type_name = [];
			$detail_type_name = [];
			
			foreach ($account_types as $key => $value) {
				$account_type_name[$value['id']] = $value['name'];
			}
			
			foreach ($detail_types as $key => $value) {
				$detail_type_name[$value['id']] = $value['name'];
			}
			
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_account_list_recursive([], $val['id'], $value['account_type_id'], $account_type_name, $detail_type_name, $acc_show_account_numbers);
						
						$_account_type_name = isset($account_type_name[$val['account_type_id']]) ? $account_type_name[$val['account_type_id']] : '';
						$_detail_type_name = isset($detail_type_name[$val['account_detail_type_id']]) ? $detail_type_name[$val['account_detail_type_id']] : '';
						
						$data_report[$data_key][] = ['description' => $val['description'], 'type' => $_account_type_name, 'detail_type' => $_detail_type_name, 'name' => $name, 'amount' => $debits - $credits, 'child_account' => $child_account];
						$total += $debits - $credits;
					}
				}
				$data_total[$data_key] = $total;
			}
			
			return ['data' => $data_report, 'total' => $data_total];
		}
		
		/**
			* get data general ledger 
			* @return array
		*/
		public function GetSaleIds($data_filter)
		{
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			$this->db->select(db_prefix().'salesmaster.SalesID,'.db_prefix().'salesmaster.ChallanID,tblclients.company as ShippingParty,'
			.'CONCAT(tblclientwiseshippingdata.ShippingAdrees, ", ", '
			.'xx_citylist.city_name, ", ", '
			.'tblclientwiseshippingdata.ShippingState, ", ", '
			.'tblclientwiseshippingdata.ShippingPin) as ShippingAddress');
			
			$this->db->join(db_prefix() . 'ordermaster', db_prefix() . 'ordermaster.OrderID=' . db_prefix() . 'salesmaster.OrderID', 'left');
			$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID=' . db_prefix() . 'ordermaster.AccountID2', 'left');
			$this->db->join(db_prefix() .'clientwiseshippingdata', db_prefix() .'clientwiseshippingdata.AccountID = '.db_prefix() .'clients.AccountID',"LEFT");
			$this->db->join(db_prefix() .'xx_citylist', db_prefix() .'xx_citylist.id = '.db_prefix() .'clientwiseshippingdata.ShippingCity',"LEFT");
			$this->db->where(db_prefix().'salesmaster.PlantID', $selected_company);
			$this->db->LIKE(db_prefix().'salesmaster.FY', $finacial_year);
			$this->db->WHERE(db_prefix().'salesmaster.Transdate>=',$from_date.' 00:00:00');
			$this->db->WHERE(db_prefix().'salesmaster.Transdate<=',$to_date.' 23:59:59');
			$this->db->order_by(db_prefix().'salesmaster.Transdate', "asc");
			$query = $this->db->get(db_prefix().'salesmaster')->result_array();
			return $query; 
		}
		public function get_data_general_ledger2($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-04-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			$username = $this->session->userdata('username');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$accounts_details = $this->db->get(db_prefix().'clients')->row();
			
			
			// get permission
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$this->db->where('UserID', $username);
			$permission_details = $this->db->get(db_prefix().'nsaccountmaster')->row();
			
			if($accounts_details->no_show == "1" && !is_admin() && $permission_details->AccountID !== $data_filter['accounting_method']){
				return $accounts_details->no_show;
				}else{
				
				$this->db->select(db_prefix().'accountledger.*,tblclients.company as EffectLedger');
				//$this->db->from(db_prefix().'accountledger');
				$this->db->join(db_prefix().'clients', db_prefix().'accountledger.EffectOn = '.db_prefix().'clients.AccountID AND '.db_prefix().'accountledger.PlantID = '.db_prefix().'clients.PlantID','LEFT');
				$this->db->where(db_prefix().'accountledger.PlantID', $selected_company);
				if(isset($data_filter['accounting_method'])){
					$accounting_method = $data_filter['accounting_method'];
					$this->db->where(db_prefix().'accountledger.AccountID', $accounting_method);
				}
				$this->db->LIKE(db_prefix().'accountledger.FY', $finacial_year);
				$this->db->WHERE(db_prefix().'accountledger.Transdate>=',$from_date.' 00:00:00');
				$this->db->WHERE(db_prefix().'accountledger.Transdate<=',$to_date.' 23:59:59');
				$this->db->order_by(db_prefix().'accountledger.Transdate,OrdinalNo', "asc");
				$query = $this->db->get(db_prefix().'accountledger')->result_array();
				
				return $query;
			}
			
		}
		public function get_data_general_ledger2_new($data_filter)
		{
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-04-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			$username = $this->session->userdata('username');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$accounts_details = $this->db->get(db_prefix().'clients')->row();
			
			
			// get permission
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$this->db->where('UserID', $username);
			$permission_details = $this->db->get(db_prefix().'nsaccountmaster')->row();
			
			if($accounts_details->no_show == "1" && !is_admin() && $permission_details->AccountID !== $data_filter['accounting_method']){
				return $accounts_details->no_show;
				}else{
				
				$this->db->select(db_prefix().'accountledger.*,tblclients.company as EffectLedger,tblstaff.firstname,tblstaff.lastname');
				//$this->db->from(db_prefix().'accountledger');
				$this->db->join(db_prefix().'clients', db_prefix().'accountledger.EffectOn = '.db_prefix().'clients.AccountID AND '.db_prefix().'accountledger.PlantID = '.db_prefix().'clients.PlantID','LEFT');
				$this->db->join(db_prefix().'staff', db_prefix().'staff.AccountID = '.db_prefix().'accountledger.EffectOn','LEFT');
				$this->db->where(db_prefix().'accountledger.PlantID', $selected_company);
				if(isset($data_filter['accounting_method'])){
					$accounting_method = $data_filter['accounting_method'];
					$this->db->where(db_prefix().'accountledger.AccountID', $accounting_method);
				}
				if(isset($data_filter['PassedFrom']) && $data_filter['PassedFrom'] != ''){
					$this->db->where(db_prefix().'accountledger.PassedFrom', $data_filter['PassedFrom']);
				}
				$this->db->LIKE(db_prefix().'accountledger.FY', $finacial_year);
				$this->db->WHERE(db_prefix().'accountledger.Transdate>=',$from_date.' 00:00:00');
				$this->db->WHERE(db_prefix().'accountledger.Transdate<=',$to_date.' 23:59:59');
				$this->db->order_by(db_prefix().'accountledger.Transdate,OrdinalNo', "asc");
				$query = $this->db->get(db_prefix().'accountledger')->result_array();
				
				return $query;
			}
			
		}
		
		
		/*
			* get CR sum for in between date ledger
		*/
		public function get_data_in_between_ledger_cr_sum($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$newfrom_date = date('20'.$finacial_year.'-04-01');
			$to_date = to_sql_date($data_filter['from_date']);
			$to_date = date('Y-m-d', strtotime('-1 day', strtotime($to_date)));
			
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			$username = $this->session->userdata('username');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$accounts_details = $this->db->get(db_prefix().'clients')->row();
			
			// get permission
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$this->db->where('UserID', $username);
			$permission_details = $this->db->get(db_prefix().'nsaccountmaster')->row();
			
			if($accounts_details->no_show == "1" && !is_admin() && $permission_details->AccountID !== $data_filter['accounting_method']){
				return $accounts_details->no_show;
				}else{
				
				$this->db->select_sum('Amount');
				$this->db->where(db_prefix().'accountledger.PlantID', $selected_company);
				if(isset($data_filter['accounting_method'])){
					$accounting_method = $data_filter['accounting_method'];
					$this->db->where(db_prefix().'accountledger.AccountID', $accounting_method);
				}
				if(isset($data_filter['PassedFrom']) && $data_filter['PassedFrom'] != ''){
					$this->db->where(db_prefix().'accountledger.PassedFrom', $data_filter['PassedFrom']);
				}
				$this->db->LIKE(db_prefix().'accountledger.FY', $finacial_year);
				$this->db->WHERE(db_prefix().'accountledger.Transdate>=',$newfrom_date.' 00:00:00');
				$this->db->WHERE(db_prefix().'accountledger.Transdate<=',$to_date.' 23:59:59');
				$this->db->WHERE(db_prefix().'accountledger.TType','C');
				$crAmt = $this->db->get(db_prefix().'accountledger')->result_array();
				return $crAmt;
			}
		}
		
		
		/*
			* get DR sum for in between date ledger
		*/
		public function get_data_in_between_ledger_dr_sum($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$newfrom_date = date('20'.$finacial_year.'-04-01');
			$to_date = to_sql_date($data_filter['from_date']);
			$to_date = date('Y-m-d', strtotime('-1 day', strtotime($to_date)));
			
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			$username = $this->session->userdata('username');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$accounts_details = $this->db->get(db_prefix().'clients')->row();
			
			// get permission
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$this->db->where('UserID', $username);
			$permission_details = $this->db->get(db_prefix().'nsaccountmaster')->row();
			
			if($accounts_details->no_show == "1" && !is_admin() && $permission_details->AccountID !== $data_filter['accounting_method']){
				return $accounts_details->no_show;
				}else{
				$this->db->select_sum('Amount');
				$this->db->where(db_prefix().'accountledger.PlantID', $selected_company);
				if(isset($data_filter['accounting_method'])){
					$accounting_method = $data_filter['accounting_method'];
					$this->db->where(db_prefix().'accountledger.AccountID', $accounting_method);
				}
				if(isset($data_filter['PassedFrom']) && $data_filter['PassedFrom'] != ''){
					$this->db->where(db_prefix().'accountledger.PassedFrom', $data_filter['PassedFrom']);
				}
				$this->db->LIKE(db_prefix().'accountledger.FY', $finacial_year);
				$this->db->WHERE(db_prefix().'accountledger.Transdate>=',$newfrom_date.' 00:00:00');
				$this->db->WHERE(db_prefix().'accountledger.Transdate<=',$to_date.' 23:59:59');
				$this->db->WHERE(db_prefix().'accountledger.TType','D');
				$drAmt = $this->db->get(db_prefix().'accountledger')->result_array();
				return $drAmt;
			}
		}
		
		
		/**
			* get data general ledger 
			* @return array
		*/
		public function get_data_for_account_bal($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $finacial_year);    
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
				$this->db->where('AccountID', $accounting_method);
			}
			//$this->db->where('(Transdate >= "' . $from_date . ' 00:00:00" and Transdate <= "' . $to_date . ' 23:23:59")');
			$accounts = $this->db->get(db_prefix().'accountbalances')->row();
			return $accounts;		
		}
		
		public function get_name_account($data_filter)
		{
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			
			$this->db->where('PlantID', $selected_company);
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
				$this->db->where('AccountID', $accounting_method);
			}
			$accounts = $this->db->get(db_prefix().'clients')->row();
			if(empty($accounts)){
				if(isset($data_filter['accounting_method'])){
					$accounting_method = $data_filter['accounting_method'];
					$this->db->where('AccountID', $accounting_method);
				}
				$accounts = $this->db->get(db_prefix().'staff')->row();
			}
			return $accounts;		
		}
		
		
		/**
			* get data general ledger new created by madhav
			* @return array
		*/
		public function get_data_general_ledger($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				$balance_total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						$node = [];
						$balance = 0;
						$amount = 0;
						foreach ($account_history as $v) {
							if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 10 || $value['account_type_id'] == 9 || $value['account_type_id'] == 8 || $value['account_type_id'] == 7 || $value['account_type_id'] == 6){
								$am = $v['credit'] - $v['debit'];
								}else{
								$am = $v['debit'] - $v['credit'];
							}
							
							$node[] =   [
							'date' => date('Y-m-d', strtotime($v['date'])),
							'type' => _l($v['rel_type']),
							'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
							'description' => $v['description'],
							'customer' => $v['customer'],
							'debit' => $v['debit'],
							'credit' => $v['credit'],
							'amount' => $am,
							'balance' => $balance + $am,
							];
							
							
							$amount += $am;
							$balance += $am;
						}
						$child_account = $this->get_data_general_ledger_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						$data_report[$data_key][] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' => $child_account];
						
						$total += $amount;
						$balance_total += $balance;
					}
				}
				$data_total[$data_key] = ['amount' => $total, 'balance' => $balance_total];
			}
			
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data journal
			* @return array 
		*/
		public function get_data_journal($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$data_report = [];
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
			$this->db->order_by('date', 'asc');
			
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			$balance = 0;
			$amount = 0;
			foreach ($account_history as $v) {
				$data_report[] =   [
				'date' => date('Y-m-d', strtotime($v['date'])),
				'type' => _l($v['rel_type']),
				'name' => (isset($account_name[$v['account']]) ? $account_name[$v['account']] : ''),
				'description' => $v['description'],
				'customer' => $v['customer'],
				'debit' => $v['debit'],
				'credit' => $v['credit'],
				];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data recent transactions
			* @return array
		*/
		public function get_data_recent_transactions($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						
						$this->db->where('((debit > 0 and (rel_type != "expense" and rel_type != "transfer")) or (credit > 0 and (rel_type = "expense" or rel_type = "transfer")))');
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->order_by('rel_type,date', 'asc');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						
						foreach ($account_history as $v) {
							if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 10 || $value['account_type_id'] == 9 || $value['account_type_id'] == 8 || $value['account_type_id'] == 7 || $value['account_type_id'] == 6){
								$am = $v['credit'] - $v['debit'];
								}else{
								$am = $v['debit'] - $v['credit'];
							}
							
							$data_report[$v['rel_type']][] =   [
							'date' => date('Y-m-d', strtotime($v['date'])),
							'type' => _l($v['rel_type']),
							'name' => (isset($account_name[$v['account']]) ? $account_name[$v['account']] : ''),
							'description' => $v['description'],
							'customer' => $v['customer'],
							'amount' => $am,
							];
						}
					}
				}
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data transaction detail by account
			* @return array
		*/
		public function get_data_transaction_detail_by_account($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				$balance_total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						$node = [];
						$balance = 0;
						$amount = 0;
						foreach ($account_history as $v) {
							if($value['account_type_id'] == 11 || $value['account_type_id'] == 12 || $value['account_type_id'] == 10 || $value['account_type_id'] == 9 || $value['account_type_id'] == 8 || $value['account_type_id'] == 7 || $value['account_type_id'] == 6){
								$am = $v['credit'] - $v['debit'];
								}else{
								$am = $v['debit'] - $v['credit'];
							}
							$node[] =   [
							'date' => date('Y-m-d', strtotime($v['date'])),
							'type' => _l($v['rel_type']),
							'description' => $v['description'],
							'customer' => $v['customer'],
							'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
							'debit' => $v['debit'],
							'credit' => $v['credit'],
							'amount' => $am,
							'balance' => $balance + ($am),
							];
							$amount += $am;
							$balance += $am;
						}
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						$child_account = $this->get_data_transaction_detail_by_account_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						$data_report[$data_key][] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' => $child_account];
						
						$total += $amount;
						$balance_total += $balance;
					}
				}
				$data_total[$data_key] = ['amount' => $total, 'balance' => $balance_total];
			}
			
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data transaction list by date
			* @return array
		*/
		public function get_data_transaction_list_by_date($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			$account_type = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
				$account_type[$value['id']] = $value['account_type_id'];
			}
			
			
			$data_report = [];
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
			$this->db->where('((debit > 0 and (rel_type != "expense" and rel_type != "transfer")) or (credit > 0 and (rel_type = "expense" or rel_type = "transfer")))');
			$this->db->order_by('date', 'asc');
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			$balance = 0;
			$amount = 0;
			foreach ($account_history as $v) {
				$account_type_id = (isset($account_type[$v['account']]) ? $account_type[$v['account']] : '');
				if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 8 || $account_type_id == 9 || $account_type_id == 10 || $account_type_id == 7 || $account_type_id == 6){
					$am = $v['credit'] - $v['debit'];
					}else{
					$am = $v['debit'] - $v['credit'];
				}
				$data_report[] =   [
				'date' => date('Y-m-d', strtotime($v['date'])),
				'type' => _l($v['rel_type']),
				'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
				'name' => isset($account_name[$v['account']]) ? $account_name[$v['account']] : '',
				'description' => $v['description'],
				'customer' => $v['customer'],
				'amount' => $am,
				'debit' => $v['debit'],
				'credit' => $v['credit'],
				];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data trial balance
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_trial_balance($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 1){
					$data_accounts['accounts_receivable'][] = $value;
				}
				if($value['account_type_id'] == 2){
					$data_accounts['current_assets'][] = $value;
				}
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 4){
					$data_accounts['fixed_assets'][] = $value;
				}
				if($value['account_type_id'] == 5){
					$data_accounts['non_current_assets'][] = $value;
				}
				if($value['account_type_id'] == 6){
					$data_accounts['accounts_payable'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
				if($value['account_type_id'] == 8){
					$data_accounts['current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 9){
					$data_accounts['non_current_liabilities'][] = $value;
				}
				if($value['account_type_id'] == 10){
					$data_accounts['owner_equity'][] = $value;
				}
				
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($credits > $debits){
							$credits = $credits - $debits;
							$debits = 0;
							}else{
							$debits = $debits - $credits;
							$credits = 0;
						}
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_trial_balance_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						$data_report[$data_key][] = ['name' => $name, 'debit' => $debits, 'credit' => $credits, 'child_account' => $child_account];
					}
				}
				$data_total[$data_key] = $total;
			}
			return ['data' => $data_report, 'total' => $data_total, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* import xlsx banking
			* @param  array $data
			* @return integer or boolean      
		*/
		public function import_xlsx_banking($data){
			$data['datecreated'] = date('Y-m-d H:i:s');
			$data['addedfrom'] = get_staff_user_id();
			$data['date'] = str_replace('/', '-', $data['date']);
			$data['date'] = date("Y-m-d", strtotime($data['date']));
			$this->db->insert(db_prefix() . 'acc_transaction_bankings', $data);
			
			$insert_id = $this->db->insert_id();
			
			if ($insert_id) {
				return $insert_id;
			}
			
			return false;
		}
		
		/**
			* get transaction banking
			* @param  string $id
			* @param  array  $where
			* @return array or object
		*/
		public function get_transaction_banking($id = '', $where = [])
		{
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'acc_transaction_bankings')->row();
			}
			
			$this->db->where($where);
			$this->db->order_by('id', 'desc');
			return $this->db->get(db_prefix() . 'acc_transaction_bankings')->result_array();
		}
		/**
			* get journal entry
			* @param  integer $id 
			* @return object     
		*/
		/*public function get_journal_entry($id){
			$this->db->where('id', $id);
			$journal_entrie = $this->db->get(db_prefix() . 'acc_journal_entries')->row();
			
			if($journal_entrie){
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'journal_entry');
			$details = $this->db->get(db_prefix().'acc_account_history')->result_array();
			
			$data_details =[];
			foreach ($details as $key => $value) {
			$data_details[] = [
			"account" => $value['account'],
			"debit" => floatval($value['debit']),
			"credit" => floatval($value['credit']),
			"description" => $value['description']];
			}
			if(count($data_details) < 10){
			
			}
			$journal_entrie->details = $data_details;
			}
			
			return $journal_entrie;
		}*/
		
		public function get_journal_entry_details($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $journal_data;
		}
		
		public function get_contra_entry_details($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "CONTRA");
			$journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $journal_data;
		}
		
		public function get_receipt_entry_details($id)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('UniquID', $id);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$journal_data = $this->db->get(db_prefix() . 'accountledgerPending')->result_array();
			return $journal_data;
		}
		
		public function get_payment_entry_details($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$journal_data = $this->db->get(db_prefix() . 'accountledgerPending')->result_array();
			return $journal_data;
		}
		
		public function get_payment_entry_detailsNew($id)
		{
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('UniquID', $id);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$journal_data = $this->db->get(db_prefix() . 'accountledgerPending')->result_array();
			return $journal_data;
		}
		
		public function get_journal_entry($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$journal_entrie = $this->db->get(db_prefix() . 'accountledger')->row();
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$this->db->order_by('OrdinalNo', "ASC");
			$journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			
			
			
			$data_details =[];
			$total_amt = 0;
			$TransType = '';
			$MainAccount = '';
			$TotalAmt = '';
			foreach ($journal_data as $key => $value) {
				
				$closingBalance = $this->getclosing_balance($value['AccountID']);
				if($value['EffectOn'] != '' && $value['EffectOn'] != null){
					
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('TransID', $value['BillNo']);
					$this->db->where('TType', 'CR');  
					$this->db->where('PassedFrom', 'PURCHASE');  
					$this->db->where('AccountID', $value['AccountID']);  
					$Purch = $this->db->get()->row();
					
					
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('TransID', $value['BillNo']);
					$this->db->where('TType', 'DR');  
					$this->db->where('PassedFrom', 'SALE');  
					$this->db->where('AccountID', $value['AccountID']);  
					$Sale = $this->db->get()->row();
					
					if(!empty($Purch)){
						$creditedamt = $Purch->Amount;
						
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'D');  
						$this->db->where('AccountID', $value['AccountID']);  
						$details = $this->db->get()->result_array();       
						$DebitAmount = array_sum(array_column($details, 'Amount'));
						// echo "<pre>";print_r($details);die;
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'C');  
						$this->db->where('AccountID', $value['AccountID']);  
						$details = $this->db->get()->result_array();       
						$CreditAmount = array_sum(array_column($details, 'Amount'));
						
						
						
						$diff = $DebitAmount - $CreditAmount;
						$total_pending_amt = $creditedamt - $diff;
						
						if($value['TType'] == 'D'){
							$total_pending_amt = $total_pending_amt + $value['Amount'];
							}else{
							$total_pending_amt = $total_pending_amt - $value['Amount'];
						}
					}
					
					if(!empty($Sale)){
						$DebitAmt = $Sale->Amount;
						
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'D');  
						$this->db->where('AccountID', $value['AccountID']);  
						$details = $this->db->get()->result_array();       
						$DebitAmount = array_sum(array_column($details, 'Amount'));
						
						// echo "<pre>";print_r($details);die;
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'C');  
						$this->db->where('AccountID', $value['AccountID']);  
						$details = $this->db->get()->result_array();       
						$CreditAmount = array_sum(array_column($details, 'Amount'));
						
						
						
						$diff = $CreditAmount - $DebitAmount;
						$total_pending_amt = $DebitAmt - $diff;
						
						if($value['TType'] == 'D'){
							$total_pending_amt = $total_pending_amt - $value['Amount'];
							}else{
							$total_pending_amt = $total_pending_amt + $value['Amount'];
						}
					}
					$data_details[] = [
					"AccountID" => strtoupper($value['AccountID']),
					"company" => strtoupper($value['AccountID']),
					"ClosingBalance" => $closingBalance,
					"against"=>ucfirst($value['Against']),
					"bill"=>strtoupper($value['BillNo']),
					"pendingAmt"=>$total_pending_amt,
					"amount" => $value['Amount'],
					"description" => $value['Narration']];
					}else{
					$TransType = $value['TType'];
					$MainAccount = strtoupper($value['AccountID']);
					$TotalAmt = $value['Amount'];
				}
			}
			
			$journal_entrie->details = $data_details;
			$journal_entrie->TotalAmt = $TotalAmt;
			$journal_entrie->TransType = $TransType;
			$journal_entrie->MainAccount = $MainAccount;
			/*echo "<pre>";
				print_r($journal_entrie);
			die;*/
			
			return $journal_entrie;
		}
		
		public function get_contra_entry($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "CONTRA");
			$contra_entrie = $this->db->get(db_prefix() . 'accountledger')->row();
			
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "CONTRA");
			$this->db->order_by('OrdinalNo', "ASC");
			$contra_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			$data_details =[];
			$TransType = '';
			$MainAccount = '';
			$TotalAmt = '';
			foreach ($contra_data as $key => $value) {
				
				if($value['EffectOn'] != '' && $value['EffectOn'] != null){
					$data_details[] = [
					"AccountID" => strtoupper($value['AccountID']),
					"company" => strtoupper($value['AccountID']),
					"amount" => $value['Amount'],
					"description" => $value['Narration']];
					}else{
					$TransType = $value['TType'];
					$MainAccount = strtoupper($value['AccountID']);
					$TotalAmt = $value['Amount'];
				}
			}
			$contra_entrie->details = $data_details;
			$contra_entrie->TotalAmt = $TotalAmt;
			$contra_entrie->TransType = $TransType;
			$contra_entrie->MainAccount = $MainAccount;
			/*echo "<pre>";
				print_r($journal_entrie);
			die;*/
			
			return $contra_entrie;
		}
		
		
		public function get_receipts_entry($id)
		{			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$receipts_entry = $this->db->get(db_prefix() . 'accountledgerPending')->row();
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$this->db->order_by('OrdinalNo', "ASC");
			$receipts_data = $this->db->get(db_prefix() . 'accountledgerPending')->result_array();
			
			// echo "<pre>";
			// print_r($total_pending_amt);
			// die;
			
			$data_details =[];
			$total_amt = 0;
			$debamt = 0;
			foreach ($receipts_data as $key => $value) {
				
				$amt = '';
				if($value['TType']=="C"){
					$amt = $value['Amount'];
					$amt = floatval($amt);
					$dr_cr = "C";
					$total_amt = $total_amt + $amt;
					
					// Get Pending Balance
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('TransID', $value['BillNo']);
					$this->db->where('TType', 'DR');  
					$this->db->where('PassedFrom', 'SALE');  
					$this->db->where('AccountID', $value['AccountID']);  
					$Sale = $this->db->get()->row();
					
					$DebitAmt = $Sale->Amount;
					
					$this->db->from(db_prefix() . 'accountledger');
					$this->db->where('BillNo', $value['BillNo']);
					$this->db->where('TType', 'D');  
					$this->db->where('AccountID', $value['AccountID']);  
					$details = $this->db->get()->result_array();       
					$DebitAmount = array_sum(array_column($details, 'Amount'));
					
					// echo "<pre>";print_r($details);die;
					$this->db->from(db_prefix() . 'accountledger');
					$this->db->where('BillNo', $value['BillNo']);
					$this->db->where('TType', 'C');  
					$this->db->where('AccountID', $value['AccountID']);  
					$details = $this->db->get()->result_array();       
					$CreditAmount = array_sum(array_column($details, 'Amount'));
					
					
					
					$diff = $CreditAmount - $DebitAmount;
					$total_pending_amt = $DebitAmt - $diff;
					$total_pending_amt = $total_pending_amt + $value['Amount'];
					
					// closing balance for this account
					$closingBalance = $this->getclosing_balance($value['AccountID']);
					$data_details[] = [
					"AccountID" => strtoupper($value['AccountID']),
					"company" => strtoupper($value['AccountID']),
					"ClosingBalance" => $closingBalance,
					"against"=>ucfirst($value['Against']),
					"bill"=>strtoupper($value['BillNo']),
					"pendingAmt"=>$total_pending_amt,
					"debit" => $amt,
					"description" => $value['Narration']];
					}else{
					$amt = $value['Amount'];
					$deb_act = strtoupper($value['AccountID']);
					$debamt = $debamt + $amt;
				}
				
				$debamt = floatval($debamt);
				
			}
			if(count($data_details) < 10){
				
			}
			$receipts_entry->details = $data_details;
			$receipts_entry->damt = $debamt;
			$receipts_entry->d_act = $deb_act;
			
			return $receipts_entry;
		}
		
		public function get_payments_entry($id)
		{
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$payment_entry = $this->db->get(db_prefix() . 'accountledgerPending')->row();
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$this->db->order_by('OrdinalNo', "ASC");
			$payment_data = $this->db->get(db_prefix() . 'accountledgerPending')->result_array();
			
			
			// echo "<pre>";
			// print_r($creditedamt);
			// die;
			
			$data_details =[];
			$total_amt = 0;
			$debamt = 0;
			foreach ($payment_data as $key => $value) {
				
				$amt = '';
				
				if($value['TType']=="D"){
					$amt = $value['Amount'];
					$amt = floatval($amt);
					$dr_cr = "D";
					$total_amt = $total_amt + $amt;
					
					// Get Pending Amount
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('TransID', $value['BillNo']);
					$this->db->where('TType', 'CR');  
					$this->db->where('PassedFrom', 'PURCHASE');  
					$this->db->where('AccountID', $value['AccountID']);  
					$Purch = $this->db->get()->row();
					$creditedamt = $Purch->Amount;
					
					$this->db->from(db_prefix() . 'accountledger');
					$this->db->where('BillNo', $value['BillNo']);
					$this->db->where('TType', 'D');  
					$this->db->where('AccountID', $value['AccountID']);  
					$details = $this->db->get()->result_array();       
					$DebitAmount = array_sum(array_column($details, 'Amount'));
					// echo "<pre>";print_r($details);die;
					$this->db->from(db_prefix() . 'accountledger');
					$this->db->where('BillNo', $value['BillNo']);
					$this->db->where('TType', 'C');  
					$this->db->where('AccountID', $value['AccountID']);  
					$details = $this->db->get()->result_array();       
					$CreditAmount = array_sum(array_column($details, 'Amount'));
					
					
					
					$diff = $DebitAmount - $CreditAmount;
					$total_pending_amt = $creditedamt - $diff;
					
					$total_pending_amt = $total_pending_amt + $value['Amount'];
					
					//  closing balance for this account
					$closingBalance = $this->getclosing_balance($value['AccountID']);
					
					$data_details[] = [
					"AccountID" => strtoupper($value['AccountID']),
					"company" => strtoupper($value['AccountID']),
					"ClosingBalance" => $closingBalance,
					"against"=>ucfirst($value['Against']),
					"bill"=>strtoupper($value['BillNo']),
					"pendingAmt"=>$total_pending_amt,
					"debit" => $amt,
					"Status" => $value['Status'],
					"description" => $value['Narration']];
					}else{
					$amt = $value['Amount'];
					$deb_act = strtoupper($value['AccountID']);
					$debamt = $debamt + $amt;
				}
				
				$debamt = floatval($debamt);
				
			}
			if(count($data_details) < 10){
				
			}
			$payment_entry->details = $data_details;
			$payment_entry->damt = $debamt;
			$payment_entry->d_act = $deb_act;
			/*echo "<pre>";
				print_r($data_details);
			die;*/
			
			return $payment_entry;
		}
		
		
		
		
		/**
			* delete journal entry
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_journal_entry($id)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$journal_entry_details = $this->get_journal_entry_detail($id);
			$_nextjournal_entry_details = $this->get_next_journal_entry($id);
			//echo "<pre>";
			//print_r($_nextjournal_entry_details);
			//die;
			
			foreach ($journal_entry_details as $key => $value) {
				
				$get_account_bal = $this->get_acc_bal($value['AccountID']);
				
				$month = substr($value['Transdate'],5,2);
				
				if($month == "01"){
					$m = 11; 
				}
				if($month == "02"){
					$m = 12; 
				}
				if($month == "03"){
					$m = 13; 
				}
				if($month == "04"){
					$m = 2; 
				}
				if($month == "05"){
					$m = 3; 
				}
				if($month == "06"){
					$m = 4; 
				}
				if($month == "07"){
					$m = 5; 
				}
				if($month == "08"){
					$m = 6; 
				}
				if($month == "09"){
					$m = 7; 
				}
				if($month == "10"){
					$m = 8; 
				}
				if($month == "11"){
					$m = 9; 
				}
				if($month == "12"){
					$m = 10; 
				}
				
				$mm = "BAL".$m;
				$current_bal = $get_account_bal->$mm;
				$amt = $value['Amount'];
				
				if($value['TType'] == "C"){
					
					$update_amt = $current_bal + $amt;
					}else{
					
					$update_amt = $current_bal - $amt;
				}
				
				
				$this->db->where('PlantID', $selected_company);
				$this->db->LIKE('FY', $fy);
				$this->db->where('AccountID', $value['AccountID']);
				$this->db->update(db_prefix() . 'accountbalances', [
				$mm => $update_amt,
				]);
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$this->db->where('VoucherID', $id);
			$this->db->delete(db_prefix() . 'accountledger');
			if ($this->db->affected_rows() > 0) {
				
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) - 1 where abs(VoucherID) > "'.$id.'" AND PassedFrom = "JOURNAL" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				/*if ($this->db->affected_rows() > 0) {*/
				$this->decrement_journal_number();
				//}
				return true;
			}
			return false;
		}
		
		/**
			* delete Contra entry
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_contra_entry($id)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$contra_entry_details = $this->get_contra_entry_detail($id);
			$_nextcontra_entry_details = $this->get_next_contra_entry($id);
			
			
			foreach ($contra_entry_details as $key => $value) {
				
				$get_account_bal = $this->get_acc_bal($value['AccountID']);
				
				$month = substr($value['Transdate'],5,2);
				
				if($month == "01"){
					$m = 11; 
				}
				if($month == "02"){
					$m = 12; 
				}
				if($month == "03"){
					$m = 13; 
				}
				if($month == "04"){
					$m = 2; 
				}
				if($month == "05"){
					$m = 3; 
				}
				if($month == "06"){
					$m = 4; 
				}
				if($month == "07"){
					$m = 5; 
				}
				if($month == "08"){
					$m = 6; 
				}
				if($month == "09"){
					$m = 7; 
				}
				if($month == "10"){
					$m = 8; 
				}
				if($month == "11"){
					$m = 9; 
				}
				if($month == "12"){
					$m = 10; 
				}
				
				$mm = "BAL".$m;
				$current_bal = $get_account_bal->$mm;
				$amt = $value['Amount'];
				
				if($value['TType'] == "C"){
					
					$update_amt = $current_bal + $amt;
					}else{
					
					$update_amt = $current_bal - $amt;
				}
				
				
				$this->db->where('PlantID', $selected_company);
				$this->db->LIKE('FY', $fy);
				$this->db->where('AccountID', $value['AccountID']);
				$this->db->update(db_prefix() . 'accountbalances', [
				$mm => $update_amt,
				]);
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "CONTRA");
			$this->db->where('VoucherID', $id);
			$this->db->delete(db_prefix() . 'accountledger');
			if ($this->db->affected_rows() > 0) {
				
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) - 1 where abs(VoucherID) > "'.$id.'" AND PassedFrom = "CONTRA" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				/*if ($this->db->affected_rows() > 0) {*/
				$this->decrement_contra_number();
				//}
				return true;
			}
			return false;
		}
		
		/**
			* delete receipts entry
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_receipt_entry($id,$PassedFrom)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$receipts_entry_details = $this->get_receipts_entry_detail($id);
			$_nextreceipts_entry_details = $this->get_next_receipts_entry($id); 
			foreach ($receipts_entry_details as $key => $value) {
				$Type = $value["Against"];
				$BillNo = $value["BillNo"];
				$NewVoucherID = $value["VoucherID"];
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			// Delete From Main Table
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$this->db->where('UniquID', $id);
			$this->db->delete(db_prefix() . 'accountledger');
			
			// Delete From Pending Table
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$this->db->where('UniquID', $id);
			$this->db->delete(db_prefix() . 'accountledgerPending');
			
			if ($this->db->affected_rows() > 0) {
				$this->db->LIKE('PassedFrom', "RECEIPT");
				$this->db->LIKE('TType', "CR");
				$this->db->where('TransID', $NewVoucherID);
				$this->db->delete(db_prefix() . 'ReconsileMaster');
				
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) - 1 where abs(VoucherID) > "'.$NewVoucherID.'" AND PassedFrom = "RECEIPTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				$sql = 'UPDATE tblaccountledgerPending SET VoucherID = abs(VoucherID) - 1 where abs(VoucherID) > "'.$NewVoucherID.'" AND PassedFrom = "RECEIPTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				$this->decrement_receipts_number();
				
				$sql2 = 'UPDATE tblReconsileMaster SET TransID = abs(TransID) - 1 where abs(TransID) > "'.$NewVoucherID.'" AND PassedFrom = "RECEIPT"';
				$this->db->query($sql2);
				
				
				if(strtolower($Type) == 'against' && !empty($BillNo)){
					$this->db->where('TransID', $BillNo);
					$this->db->LIKE('TType', 'DR');
					$this->db->where('PassedFrom', 'SALE');
					$this->db->update(db_prefix() . 'ReconsileMaster', [
					'Status' => 'N',
					]);
				}
				return true;
			}
			return false;
		}
		
		public function delete_payment_entry($id,$PassedFrom)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$payments_entry_details = $this->GetEntryDetailsByUniqueID($UniquID,$PassedFrom);
			$_nextpayments_entry_details = $this->get_next_payments_entry($id);
			
			foreach ($payments_entry_details as $key => $value) {
				
				$NewVoucherID = $value["VoucherID"];
				
				$Type = $value["Against"];
				$BillNo = $value["BillNo"];
				
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$NewVoucherID,
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			
			// Delete From main Table
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$this->db->where('UniquID', $id);
			$this->db->delete(db_prefix() . 'accountledger');
			
			// Delete From Pending  Table
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$this->db->where('UniquID', $id);
			$this->db->delete(db_prefix() . 'accountledgerPending');
			
			
			if ($this->db->affected_rows() > 0) {
				$this->db->LIKE('PassedFrom', "PAYMENT");
				$this->db->LIKE('TType', "DR");
				$this->db->where('TransID', $NewVoucherID);
				$this->db->delete(db_prefix() . 'ReconsileMaster');
				
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) - 1 where abs(VoucherID) > "'.$NewVoucherID.'" AND PassedFrom = "PAYMENTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				$sql = 'UPDATE tblaccountledgerPending SET VoucherID = abs(VoucherID) - 1 where abs(VoucherID) > "'.$NewVoucherID.'" AND PassedFrom = "PAYMENTS" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				/*if ($this->db->affected_rows() > 0) {*/
				$this->decrement_payments_number();
				
				$sql2 = 'UPDATE tblReconsileMaster SET TransID = abs(TransID) - 1 where abs(TransID) > "'.$NewVoucherID.'" AND PassedFrom = "PAYMENT"';
				$this->db->query($sql2);
				
				if(strtolower($Type) == 'against' && !empty($BillNo)){
					$this->db->where('TransID', $BillNo);
					$this->db->LIKE('TType', 'CR');
					$this->db->where('PassedFrom', 'PURCHASE');
					$this->db->update(db_prefix() . 'ReconsileMaster', [
					'Status' => 'N',
					]);
				}
				return true;
			}
			return false;
		}
		
		public function get_receipts_entry_detail($id)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('UniquID', $id);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$receipts_data = $this->db->get(db_prefix() . 'accountledgerPending')->result_array();
			return $receipts_data;
		}
		
		public function get_payments_entry_detail($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('UniquID', $id);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$receipts_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $receipts_data;
		}
		
		public function GetEntryDetailsByUniqueID($UniquID,$PassedFrom)
		{			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('UniquID', $UniquID);
			$this->db->LIKE('PassedFrom', $PassedFrom);
			$receipts_data = $this->db->get(db_prefix() . 'accountledgerPending')->result_array();
			return $receipts_data;
		}
		
		public function get_next_receipts_entry($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "RECEIPTS" AND FY LIKE "'.$fy.'" AND VoucherID > '.$id.' GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) ASC';
			$staff_data = $this->db->query($sql)->result_array();
			return $staff_data;
		}
		
		public function get_next_payments_entry($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "'.$fy.'" AND VoucherID > '.$id.' GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) ASC';
			$staff_data = $this->db->query($sql)->result_array();
			return $staff_data;
		}
		
		public function get_next_journal_entry($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "JOURNAL" AND FY LIKE "'.$fy.'" AND VoucherID > '.$id.' GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) ASC';
			$staff_data = $this->db->query($sql)->result_array();
			return $staff_data;
		}
		
		public function get_next_contra_entry($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "CONTRA" AND FY LIKE "'.$fy.'" AND VoucherID > '.$id.' GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) ASC';
			$staff_data = $this->db->query($sql)->result_array();
			return $staff_data;
		}
		
		public function get_journal_entry_detail($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$receipts_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $receipts_data;
		}
		
		public function get_contra_entry_detail($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "CONTRA");
			$receipts_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $receipts_data;
		}
		
		public function get_result_to_cur_date_journal($journal_date){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			/*$this->db->where('PlantID', $selected_company);
				$this->db->LIKE('FY', $fy);
				$this->db->LIKE('PassedFrom', "JOURNAL");
				$this->db->where('Transdate >', $journal_date);
				$this->db->order_by("VoucherID", "desc");
				$journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $journal_data;*/
			
			$fy_ne = $fy + 1;
			$las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
			$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "JOURNAL" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$journal_date.' H:i:m" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) DESC ';
			$journal_data = $this->db->query($sql)->result_array();
			return $journal_data;
			
		}
		
		public function get_result_to_cur_date_contra($contra_date){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			/*$this->db->where('PlantID', $selected_company);
				$this->db->LIKE('FY', $fy);
				$this->db->LIKE('PassedFrom', "CONTRA");
				$this->db->where('Transdate >', $contra_date);
				$this->db->order_by("VoucherID", "desc");
				$journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			return $journal_data;*/
			
			$fy_ne = $fy + 1;
			$las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
			$sql = 'SELECT * FROM tblaccountledger WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "CONTRA" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$contra_date.' H:i:m" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledger.VoucherID) DESC ';
			$contra_data = $this->db->query($sql)->result_array();
			return $contra_data;
			
		}
		
		public function get_result_to_cur_date_receipts($receipts_date)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$fy_ne = $fy + 1;
			$las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
			$sql = 'SELECT * FROM tblaccountledgerPending WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "RECEIPTS" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$receipts_date.' H:i:m" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledgerPending.VoucherID) DESC ';
			$receipts_data = $this->db->query($sql)->result_array();
			return $receipts_data;
			
		} 
		public function GetLastUniqueNo($PassedFrom)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$sql = 'SELECT * FROM tblaccountledgerPending WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "'.$PassedFrom.'" AND FY LIKE "'.$fy.'"  GROUP BY UniquID ORDER BY abs(tblaccountledgerPending.UniquID) DESC ';
			$UniqueID = $this->db->query($sql)->result_array();
			return $UniqueID;
		}
		
		
		
		public function get_result_to_cur_date_payments($payment_date)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$fy_ne = $fy + 1;
			$las_date_fy = '20'.$fy_ne.'-03-31 23:59:59';
			$sql = 'SELECT * FROM tblaccountledgerPending WHERE PlantID = '.$selected_company.' AND PassedFrom LIKE "PAYMENTS" AND FY LIKE "'.$fy.'" AND Transdate BETWEEN "'.$payment_date.' H:i:s" AND "'.$las_date_fy.'" GROUP BY VoucherID ORDER BY abs(tblaccountledgerPending.VoucherID) DESC ';
			$staff_data = $this->db->query($sql)->result_array();
			return $staff_data;
			
		}
		
		
		
		/**
			* Update journal entry
			* @param array $data 
			* @return boolean
		*/
		public function update_journal_entry($data,$id){
			$journal_entry = json_decode($data['journal_entry']);
			unset($data['journal_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$journal_date = to_sql_date($data['journal_date1'])." ".date('H:i:s');
			$journal_details = $this->get_journal_entry_details($id);
			/*echo "<pre>";
				print_r($journal_entry);
			die;*/
			
			
			// Delete previous ledger details
			
			foreach ($journal_details as $key => $value) {
				
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$this->db->where('VoucherID', $id);
			$this->db->delete(db_prefix() . 'accountledger');
			// END Delete previous ledger details
			
			
			/*echo "<pre>";
				print_r($data);
				print_r($get_result_to_cur_date);
				print_r($journal_entry);
			die;*/
			if($data['TransType'] == 'D'){
				$ttype = 'C';
				}else{
				$ttype = 'D';
			}
			
			
			$i = 1;
			$_data = array(
			"PlantID" =>$selected_company,
			"Transdate" =>$journal_date,
			"TransDate2" =>date('Y-m-d H:i:s'),
			"VoucherID" =>$id,
			"AccountID" =>$data['ganeral_account'],
			"EffectOn" =>null,
			"TType" =>$data['TransType'],
			"Amount" =>$data['amount'],
			"Narration" =>'',
			"PassedFrom" =>"JOURNAL",
			"OrdinalNo" =>$i,
			"UserID" =>$this->session->userdata('username'),
			"FY" =>$fy,
			);
			$this->db->insert(db_prefix().'accountledger', $_data);
			
			$i = 1;
			foreach ($journal_entry as $key => $value) {
				if($value[0] != ''){
					$_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$journal_date,
					"TransDate2" =>date('Y-m-d H:i:s'),
					"VoucherID" =>$id,
					"AccountID" =>$value[0],
					"EffectOn" =>$data['ganeral_account'],
					"TType" =>$ttype,
					"Against"=>$value[3],
    				"BillNo"=>$value[4],
    				"Amount" =>$value[6],
    				"Narration" =>$value[7],
					"PassedFrom" =>"JOURNAL",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					);
					
					$this->db->insert(db_prefix().'accountledger', $_data);
					$i++;
				}
			}
			
			return true;
			
			
		}
		
		/**
			* Update Contra entry
			* @param array $data 
			* @return boolean
		*/
		public function update_contra_entry($data,$id){
			$contra_entry = json_decode($data['contra_entry']);
			unset($data['contra_entry']);
			
			/*echo "<pre>";
				print_r($data);
				print_r($contra_entry);
			die;*/
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$contra_date = to_sql_date($data['contra_date1'])." ".date('H:i:s');
			$contra_details = $this->get_contra_entry_details($id);
			$month = substr($contra_date,5,2);
			
			
			// Delete previous ledger details
			
			foreach ($contra_details as $key => $value) {
				
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "CONTRA");
			$this->db->where('VoucherID', $id);
			$this->db->delete(db_prefix() . 'accountledger');
			
			if($data['TransType'] == 'D'){
				$ttype = 'C';
				}else{
				$ttype = 'D';
			}
			
			
			$i = 1;
			$_data = array(
			"PlantID" =>$selected_company,
			"Transdate" =>$contra_date,
			"TransDate2" =>date('Y-m-d H:i:s'),
			"VoucherID" =>$id,
			"AccountID" =>$data['ganeral_account'],
			"EffectOn" =>null,
			"TType" =>$data['TransType'],
			"Amount" =>$data['amount'],
			"Narration" =>'',
			"PassedFrom" =>"CONTRA",
			"OrdinalNo" =>$i,
			"UserID" =>$this->session->userdata('username'),
			"FY" =>$fy,
			);
			$this->db->insert(db_prefix().'accountledger', $_data);
			
			$i = 1;
			foreach ($contra_entry as $key => $value) {
				if($value[0] != ''){
					$_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$contra_date,
					"TransDate2" =>date('Y-m-d H:i:s'),
					"VoucherID" =>$id,
					"AccountID" =>$value[0],
					"EffectOn" =>$data['ganeral_account'],
					"TType" =>$ttype,
					"Amount" =>$value[2],
					"Narration" =>$value[3],
					"PassedFrom" =>"CONTRA",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					);
					$this->db->insert(db_prefix().'accountledger', $_data);
					
					$i++;
				}
			}
			
			return true;
			
		}
		
		public function update_receipts_entry($data,$id)
		{
			$receipts_entry = json_decode($data['receipts_entry']);
			unset($data['receipts_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$receipt_date = to_sql_date($data['receipt_date1'])." ".date('H:i:s');
			$UniqueID = $data['UniqueID'];
			$receipts_details = $this->get_receipt_entry_details($UniqueID);
			
			foreach ($receipts_details as $key => $value) 
			{			
				$NewVoucherID = $value["VoucherID"];
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);				
			}
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$this->db->where('UniquID', $UniqueID);
			$this->db->delete(db_prefix() . 'accountledgerPending');
			// Delete Main Ledger table
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "RECEIPTS");
			$this->db->where('UniquID', $UniqueID);
			$this->db->delete(db_prefix() . 'accountledger');
			// END Delete previous ledger details
			
			$this->db->LIKE('PassedFrom', "RECEIPT");
			$this->db->where('TransID', $id);
			$this->db->where('TType', 'CR');
			$this->db->delete(db_prefix() . 'ReconsileMaster');
			// END Delete previous Reconsile Record details
			
			$i = 1;
			foreach ($receipts_entry as $key => $value) {
				if($value[0] != ''){
					
					$credit_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$receipt_date,
					"TransDate2"=>$value["TransDate2"],
					"VoucherID" =>$NewVoucherID,
					"AccountID" =>$value[0],
					"EffectOn" =>$data['ganeral_account'],
					"TType" =>"C",
					"Against"=>$value[3],
					"BillNo"=>$value[4],
					"Amount" =>$value[6],
					"Narration" =>$value[7],
					"PassedFrom" =>"RECEIPTS",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					"UniquID"=>$UniqueID
					);
					
					$this->db->insert(db_prefix().'accountledgerPending', $credit_data);
					
					$debit_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$receipt_date,
					"TransDate2"=>$value["TransDate2"],
					"VoucherID" =>$NewVoucherID,
					"AccountID" =>$data['ganeral_account'],
					"EffectOn" =>$value[0],
					"TType" =>"D",
					"Against"=>$value[3],
					"BillNo"=>$value[4],
					"Amount" =>$value[6],
					"Narration" =>$value[7],
					"PassedFrom" =>"RECEIPTS",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					"UniquID"=>$UniqueID
					);
					$this->db->insert(db_prefix().'accountledgerPending', $debit_data);
					$i++;
				}
				
				if(($value[3]=="AGAINST" || $value[3]=="Against") && $value[0] != '' && $value[4] != '')
				{           
					
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('EffectOn', $value[4]);
					$this->db->where('TType', 'CR');      
					$info = $this->db->get()->row();      
					$billid = $info->BillID;    
					
					$this->db->select('*');
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->group_start();  // Start grouping conditions
					$this->db->where('EffectOn', $value[4]);
					$this->db->or_where('TransID', $value[4]);
					$this->db->group_end();    // End grouping conditions
					$invoiceData = $this->db->get()->result_array();                
					
					$this->db->select('*'); 
					$this->db->from(db_prefix() . 'ReconsileMaster'); 
					$this->db->where('TransID', $value[4]);
					$this->db->where('TType', 'DR');
					$dtypeamt = $this->db->get()->row(); 
					$amt = $dtypeamt->Amount;
					
					$typeCAmount = null;
					$typeDAmount = null;      
					foreach ($invoiceData as $row) {
						if ($row['TType'] == 'CR') {
							$typeCAmount += $row['Amount'];
							} elseif ($row['TType'] == 'DR') {
							$typeDAmount += $row['Amount'];
						}
					}                                     
					
					if(!empty($info))
					{
						if ($typeCAmount !== null && $typeDAmount !== null && $invoiceData) {               
							$difference = abs($typeCAmount - $typeDAmount);
							if($difference && $difference > 0)
							{
								if($value[6] == $difference)
								{  $amount = $difference;  }
								else if($value[6] > $difference) 
								{ $amount = $difference;}
								else if($value[6] < $difference) 
								{ $amount = $value[6]; }                           
							}
							else if($difference == 0)
							{
								$amount = $value[6];
							} 
							
							$reconciliation = array(
							"TransID"=>$NewVoucherID,
							"EffectOn"=>$value[4],
							"TransDate"=>date('Y-m-d H:i:s'),
							"AccountID"=>$value[0],
							"Amount"=>$amount,
							"TType"=>"CR",
							"Status"=>"Y",
							"PassedFrom"=>"RECEIPT",
							"UserID"=>$this->session->userdata('username'),
							);
							
							$insertdata = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);
							// echo $difference;die;
							if($insertdata)
							{
								$this->db->select('*');
								$this->db->from(db_prefix() . 'ReconsileMaster');
								$this->db->group_start();  // Start grouping conditions
								$this->db->where('EffectOn', $value[3]);
								$this->db->or_where('TransID', $value[3]);
								$this->db->group_end();    // End grouping conditions
								$invoiceData = $this->db->get()->result_array();
								$typeCAmount = null;
								$typeDAmount = null;      
								foreach ($invoiceData as $row) {
									if ($row['TType'] == 'CR') {
										$typeCAmount += $row['Amount'];
										} elseif ($row['TType'] == 'DR') {
										$typeDAmount += $row['Amount'];
									}
								} 
								$difference = abs($typeCAmount - $typeDAmount);
								if($value[6] == $difference || $value[6] > $difference)
								{
									
									// echo "ok";die;	
									$updatestatus = array(
									"Status"=>"Y"
									);                           
									$this->db->where('TType', 'DR');
									$this->db->where('TransID', $value[3]);
									$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
									// echo "ok";die;
								}
							}                                              
						}
					}
					else if(empty($info))
					{                     
						if($dtypeamt && $amt)
						{
							if($value[6] > $amt)
							{
								$diff = $amt;
							}  
							else if ($value[6] < $amt)    
							{
								$diff = $value[6];
							}   
							else if($value[6] == $amt)   
							{
								$diff = $amt;
							}
						}
						else
						{                       
							$diff = $value[6];
						}           
						
						$reconciliation = array(
						"TransID"=>$NewVoucherID,
						"EffectOn"=>$value[4],
						"TransDate"=>date('Y-m-d H:i:s'),
						"AccountID"=>$value[0],
						"Amount"=>$diff,
						"TType"=>"CR",
						"Status"=>"Y",
						"PassedFrom"=>"RECEIPT",
						"UserID"=>$this->session->userdata('username'),
						);
						$insertdetails = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);  
						if($insertdetails && ($value[6] == $amt || $value[6] > $amt))
						{
							$updatestatus = array(
							"Status"=>"Y"
							);                           
							$this->db->where('TType', 'DR');
							$this->db->where('TransID', $value[4]);
							$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
						}  
					}              
				}
			}
			return true;
		}	
		
		public function update_payments_entry($data,$id)
		{
			$payments_entry = json_decode($data['payment_entry']);
			
			unset($data['payment_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$payment_date = to_sql_date($data['payment_date1'])." ".date('H:i:s');
			
			$UniqueID = $data['UniqueID'];
			//$payments_details = $this->get_payment_entry_details($id);
			$payments_details = $this->get_payment_entry_detailsNew($UniqueID);
			
			// Delete previous ledger details
			foreach ($payments_details as $key => $value) {
				$NewVoucherID = $value["VoucherID"];
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$this->db->where('UniquID', $UniqueID);
			$this->db->delete(db_prefix() . 'accountledgerPending');
			// Delete Main Table
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "PAYMENTS");
			$this->db->where('UniquID', $UniqueID);
			$this->db->delete(db_prefix() . 'accountledger');
			// END Delete previous ledger details
			
			$this->db->LIKE('PassedFrom', "PAYMENT");
			$this->db->where('TransID', $id);
			$this->db->where('TType', 'DR');
			$this->db->delete(db_prefix() . 'ReconsileMaster');
			// END Delete previous Reconsile Record details
			
			$i = 1;
			foreach ($payments_entry as $key => $value) {
				if($value[0] != ''){
					
					$credit_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$payment_date,
					"TransDate2" =>$payment_date,
					"VoucherID" =>$NewVoucherID,
					"AccountID" =>$value[0],
					"EffectOn" =>$data['ganeral_account'],
					"TType" =>"D",
					"Against"=>$value[3],
					"BillNo"=>$value[4],
					"Amount" =>$value[6],
					"Narration" =>$value[7],
					"PassedFrom" =>"PAYMENTS",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					"UniquID" =>$UniqueID,
					);
					$this->db->insert(db_prefix().'accountledgerPending', $credit_data);
					
					
					$debit_data = array(
					"PlantID" =>$selected_company,
					"Transdate" =>$payment_date,
					"TransDate2" =>$payment_date,
					"VoucherID" =>$NewVoucherID,
					"AccountID" =>$data['ganeral_account'],
					"EffectOn" =>$value[0],
					"TType" =>"C",
					"Against"=>$value[3],
					"BillNo"=>$value[4],
					"Amount" =>$value[6],
					"Narration" =>$value[7],
					"PassedFrom" =>"PAYMENTS",
					"OrdinalNo" =>$i,
					"UserID" =>$this->session->userdata('username'),
					"FY" =>$fy,
					"UniquID" =>$UniqueID,
					);
					$this->db->insert(db_prefix().'accountledgerPending', $debit_data);
					$i++;
				}
				
				if(($value[3]=="AGAINST" || $value[3]=="Against") && $value[0] != '' && $value[4] != '')
				{           
					
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('EffectOn', $value[3]);
					$this->db->where('TType', 'DR');      
					$info = $this->db->get()->row();      
					$billid = $info->BillID;    
					
					$this->db->select('*');
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->group_start();  // Start grouping conditions
					$this->db->where('EffectOn', $value[4]);
					$this->db->or_where('TransID', $value[4]);
					$this->db->group_end();    // End grouping conditions
					$invoiceData = $this->db->get()->result_array();                
					
					$this->db->select('*'); 
					$this->db->from(db_prefix() . 'ReconsileMaster'); 
					$this->db->where('TransID', $value[4]);
					$this->db->where('TType', 'CR');
					$ctypeamt = $this->db->get()->row(); 
					$amt = $ctypeamt->Amount;
					
					$typeCAmount = null;
					$typeDAmount = null;      
					foreach ($invoiceData as $row) {
						if ($row['TType'] == 'CR') {
							$typeCAmount += $row['Amount'];
							} elseif ($row['TType'] == 'DR') {
							$typeDAmount += $row['Amount'];
						}
					}                                     
					
					if(!empty($info))
					{
						if ($typeCAmount !== null && $typeDAmount !== null && $invoiceData) {               
							$difference = abs($typeCAmount - $typeDAmount);
							if($difference && $difference > 0)
							{
								if($value[6] == $difference)
								{  $amount = $difference;  }
								else if($value[6] > $difference) 
								{ $amount = $difference;}
								else if($value[6] < $difference) 
								{ $amount = $value[6]; }                           
							}
							else if($difference == 0)
							{
								$amount = $value[5];
							} 
							
							$reconciliation = array(
							"TransID"=>$id,
							"EffectOn"=>$value[4],
							"TransDate"=>date('Y-m-d H:i:s'),
							"AccountID"=>$value[0],
							"Amount"=>$amount,
							"TType"=>"DR",
							"Status"=>"Y",
							"PassedFrom"=>"PAYMENT",
							"UserID"=>$this->session->userdata('username'),
							);
							
							$insertdata = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);
							// echo $difference;die;
							if($insertdata)
							{
								$this->db->select('*');
								$this->db->from(db_prefix() . 'ReconsileMaster');
								$this->db->group_start();  // Start grouping conditions
								$this->db->where('EffectOn', $value[4]);
								$this->db->or_where('TransID', $value[4]);
								$this->db->group_end();    // End grouping conditions
								$invoiceData = $this->db->get()->result_array();
								$typeCAmount = null;
								$typeDAmount = null;      
								foreach ($invoiceData as $row) {
									if ($row['TType'] == 'CR') {
										$typeCAmount += $row['Amount'];
										} elseif ($row['TType'] == 'DR') {
										$typeDAmount += $row['Amount'];
									}
								} 
								$difference = abs($typeCAmount - $typeDAmount);
								if($value[6] == $difference || $value[6] > $difference)
								{
									
									// echo "ok";die;	
									$updatestatus = array(
									"Status"=>"Y"
									);                           
									$this->db->where('TType', 'CR');
									$this->db->where('TransID', $value[3]);
									$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
									// echo "ok";die;
								}
							}                                              
						}
					}
					else if(empty($info))
					{                     
						if($ctypeamt && $amt)
						{
							if($value[6] > $amt)
							{
								$diff = $amt;
							}  
							else if ($value[6] < $amt)    
							{
								$diff = $value[6];
							}   
							else if($value[6] == $amt)   
							{
								$diff = $amt;
							}
						}
						else
						{                       
							$diff = $value[6];
						}           
						
						$reconciliation = array(
						"TransID"=>$id,
						"EffectOn"=>$value[4],
						"TransDate"=>date('Y-m-d H:i:s'),
						"AccountID"=>$value[0],
						"Amount"=>$diff,
						"TType"=>"DR",
						"Status"=>"Y",
						"PassedFrom"=>"PAYMENT",
						"UserID"=>$this->session->userdata('username'),
						);
						$insertdetails = $this->db->insert(db_prefix().'ReconsileMaster', $reconciliation);  
						if($insertdetails && ($value[6] == $amt || $value[6] > $amt))
						{
							$updatestatus = array(
							"Status"=>"Y"
							);                           
							$this->db->where('TType', 'CR');
							$this->db->where('TransID', $value[4]);
							$this->db->update(db_prefix().'ReconsileMaster', $updatestatus);
						}  
					}              
				}
			}
			return true;
		}
		
		
		
		/**
			* check format date Y-m-d
			*
			* @param      String   $date   The date
			*
			* @return     boolean
		*/
		public function check_format_date($date)
		{
			if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
				return true;
				} else {
				return false;
			}
		}
		
		/**
			* get transfer
			* @param  integer $id 
			* @return object    
		*/
		public function get_transfer($id){
			$this->db->where('id', $id);
			return $this->db->get(db_prefix() . 'acc_transfers')->row();
		}
		
		/**
			* update transfer
			* @param array $data
			* @param  integer $id 
			* @return boolean
		*/
		public function update_transfer($data, $id){
			if(isset($data['id'])){
				unset($data['id']);
			}
			$data['date'] = to_sql_date($data['date']);
			
			if(get_option('acc_close_the_books') == 1){
				if(strtotime($data['date']) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
					return 'close_the_book';
				}
			}
			
			$data['transfer_amount'] = str_replace(',', '', $data['transfer_amount']);
			
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_transfers', $data);
			
			$this->db->where('rel_id', $id);
			$this->db->where('rel_type', 'transfer');
			$this->db->delete(db_prefix() . 'acc_account_history');
			
			$node = [];
			$node['account'] = $data['transfer_funds_from'];
			$node['debit'] = 0;
			$node['credit'] = $data['transfer_amount'];
			$node['date'] = $data['date'];
			$node['rel_id'] = $id;
			$node['rel_type'] = 'transfer';
			$node['datecreated'] = date('Y-m-d H:i:s');
			$node['addedfrom'] = get_staff_user_id();
			
			$this->db->insert(db_prefix().'acc_account_history', $node);
			
			$node = [];
			$node['account'] = $data['transfer_funds_to'];
			$node['debit'] = $data['transfer_amount'];
			$node['credit'] = 0;
			$node['date'] = $data['date'];
			$node['rel_id'] = $id;
			$node['rel_type'] = 'transfer';
			$node['datecreated'] = date('Y-m-d H:i:s');
			$node['addedfrom'] = get_staff_user_id();
			
			$this->db->insert(db_prefix().'acc_account_history', $node);
			
			return true;
		}
		
		/**
			* delete transfer
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_transfer($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_transfers');
			if ($this->db->affected_rows() > 0) {
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'transfer');
				$this->db->delete(db_prefix() . 'acc_account_history');
				
				return true;
			}
			return false;
		}
		
		/**
			* delete account
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_account($id)
		{
			$this->db->where('(account = '. $id .' or split = '. $id.')');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			
			if($count > 0){
				return 'have_transaction';
			}
			
			$this->db->where('id', $id);
			$this->db->where('default_account', 0);
			$this->db->delete(db_prefix() . 'acc_accounts');
			if ($this->db->affected_rows() > 0) {
				$this->db->where('account', $id);
				$this->db->delete(db_prefix() . 'acc_account_history');
				
				return true;
			}
			return false;
		}
		
		/**
			* delete convert
			* @param integer $id
			* @return boolean
		*/
		public function delete_convert($id, $type)
		{
			if($type == 'opening_stock'){
				$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
				
				$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
				
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', $type);
				$this->db->where('date >= "'.$date_financial_year.'"');
				$this->db->delete(db_prefix() . 'acc_account_history');
				}else{
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', $type);
				$this->db->delete(db_prefix() . 'acc_account_history');
			}
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* Gets the invoice without commission.
			* 
			* @param      bool        $old_invoice 
			* 
			* @return     array  The invoice without commission.
		*/
		public function get_data_invoices_for_select($where = []){
			$this->db->where($where);
			$invoices = $this->db->get(db_prefix() . 'invoices')->result_array();
			
			$invoice_return = [];
			
			foreach ($invoices as $key => $value) {
				$payments_amount = sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $value['id'])));
				
				if($payments_amount > 0){
					$node = [];
					$node['id'] = $value['id'];
					$node['name'] = format_invoice_number($value['id']);
					$invoice_return[] = $node;
				}
			}
			
			return $invoice_return;
		}
		
		/**
			* get reconcile by account
			* @param  integer $account 
			* @return object or boolean          
		*/
		public function get_reconcile_by_account($account){
			$this->db->where('account', $account);
			$this->db->order_by('id', 'desc');
			$reconcile = $this->db->get(db_prefix() . 'acc_reconciles')->row();
			
			if($reconcile){
				return $reconcile;
			}
			
			return false;
		}
		
		/**
			* add reconcile
			* @param array $data 
			* @return  integer or boolean
		*/
		public function add_reconcile($data){
			if($data['ending_date'] != ''){
				$data['ending_date'] = to_sql_date($data['ending_date']);
			}
			
			if($data['income_date'] != ''){
				$data['income_date'] = to_sql_date($data['income_date']);
			}
			
			if($data['expense_date'] != ''){
				$data['expense_date'] = to_sql_date($data['expense_date']);
			}
			
			$data['service_charge'] = str_replace(',', '', $data['service_charge']);
			$data['interest_earned'] = str_replace(',', '', $data['interest_earned']);
			$data['ending_balance'] = str_replace(',', '', $data['ending_balance']);
			$data['beginning_balance'] = str_replace(',', '', $data['beginning_balance']);
			
			$this->db->insert(db_prefix().'acc_reconciles', $data);
			$insert_id = $this->db->insert_id();
			
			if($insert_id){
				if($data['service_charge'] > 0){
					$node = [];
					$node['split'] = $data['account'];
					$node['reconcile'] = $insert_id;
					$node['account'] = $data['expense_account'];
					$node['debit'] = $data['service_charge'];
					$node['credit'] = 0;
					$node['rel_id'] = 0;
					$node['rel_type'] = 'cheque_expense';
					$node['description'] = _l('service_charge');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
					
					$node = [];
					$node['split'] = $data['expense_account'];
					$node['reconcile'] = $insert_id;
					$node['account'] = $data['account'];
					
					$node['debit'] = 0;
					$node['credit'] = $data['service_charge'];
					$node['rel_id'] = 0;
					$node['rel_type'] = 'cheque_expense';
					$node['description'] = _l('service_charge');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
				}
				if($data['interest_earned'] > 0){
					$node = [];
					$node['split'] = $data['account'];
					$node['reconcile'] = $insert_id;
					$node['account'] = $data['income_account'];
					$node['debit'] = 0;
					$node['credit'] = $data['interest_earned'];
					$node['rel_id'] = 0;
					$node['rel_type'] = 'deposit';
					$node['description'] = _l('interest_earned');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
					
					$node = [];
					$node['split'] = $data['income_account'];
					$node['reconcile'] = $insert_id;
					$node['account'] = $data['account'];
					$node['debit'] = $data['interest_earned'];
					$node['credit'] = 0;
					$node['rel_id'] = 0;
					$node['rel_type'] = 'deposit';
					$node['description'] = _l('interest_earned');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
				}
				
				return $insert_id;
			}
			
			return false;
		}
		
		/**
			* update reconcile
			* @param array $data 
			* @param integer $id 
			* @return  boolean
		*/
		public function update_reconcile($data, $id){
			if($data['ending_date'] != ''){
				$data['ending_date'] = to_sql_date($data['ending_date']);
			}
			
			if($data['income_date'] != ''){
				$data['income_date'] = to_sql_date($data['income_date']);
			}
			
			if($data['expense_date'] != ''){
				$data['expense_date'] = to_sql_date($data['expense_date']);
			}
			
			$account = 0;
			if(isset($data['expense_date'])){
				$account = $data['account'];
				unset($data['account']);
			}
			
			$data['service_charge'] = str_replace(',', '', $data['service_charge']);
			$data['interest_earned'] = str_replace(',', '', $data['interest_earned']);
			$data['ending_balance'] = str_replace(',', '', $data['ending_balance']);
			$data['beginning_balance'] = str_replace(',', '', $data['beginning_balance']);
			
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_reconciles', $data);
			
			if ($this->db->affected_rows() > 0) {
				$this->db->where('rel_id', 0);
				$this->db->where('rel_type', 'cheque_expense');
				$this->db->where('reconcile', $id);
				$this->db->delete(db_prefix().'acc_account_history');
				
				$this->db->where('rel_id', 0);
				$this->db->where('rel_type', 'deposit');
				$this->db->where('reconcile', $id);
				$this->db->delete(db_prefix().'acc_account_history');
				
				if($data['service_charge'] > 0){
					$node = [];
					$node['split'] = $account;
					$node['reconcile'] = 0;
					$node['account'] = $data['expense_account'];
					$node['debit'] = $data['service_charge'];
					$node['credit'] = 0;
					$node['rel_id'] = 0;
					$node['rel_type'] = 'cheque_expense';
					$node['description'] = _l('service_charge');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
					
					$node = [];
					$node['split'] = $data['expense_account'];
					$node['reconcile'] = $id;
					$node['account'] = $account;
					$node['debit'] = 0;
					$node['credit'] = $data['service_charge'];
					$node['rel_id'] = 0;
					$node['rel_type'] = 'cheque_expense';
					$node['description'] = _l('service_charge');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
				}
				if($data['interest_earned'] > 0){
					$node = [];
					$node['split'] = $account;
					$node['reconcile'] = 0;
					$node['account'] = $data['income_account'];
					$node['debit'] = 0;
					$node['credit'] = $data['interest_earned'];
					$node['rel_id'] = 0;
					$node['rel_type'] = 'deposit';
					$node['description'] = _l('interest_earned');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
					
					$node = [];
					$node['split'] = $data['income_account'];
					$node['reconcile'] = $id;
					$node['account'] = $account;
					$node['debit'] = $data['interest_earned'];
					$node['credit'] = 0;
					$node['rel_id'] = 0;
					$node['rel_type'] = 'deposit';
					$node['description'] = _l('interest_earned');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
				}
				
				return true;
			}
			
			return false;
		}
		
		/**
			* add adjustment
			* @param array $data 
			* @return  integer or boolean
		*/
		public function add_adjustment($data){
			$this->db->where('account_type_id', 15);
			$this->db->where('account_detail_type_id', 139);
			$account = $this->db->get(db_prefix().'acc_accounts')->row();
			$data['adjustment_date'] = to_sql_date($data['adjustment_date']);
			
			if(get_option('acc_close_the_books') == 1){
				if(strtotime($data['adjustment_date']) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
					return 'close_the_book';
				}
			}
			if($account){
				$data['adjustment_amount'] = str_replace(',', '', $data['adjustment_amount']);
				
				$node = [];
				
				$node['account'] = $account->id;
				if($data['adjustment_amount'] > 0){
					$node['rel_id'] = 0;
					$node['rel_type'] = 'deposit';
					$node['debit'] = $data['adjustment_amount'];
					$node['credit'] = 0;
					}else{
					$node['rel_id'] = 0;
					$node['rel_type'] = 'cheque_expense';
					$node['debit'] = 0;
					$node['credit'] = $data['adjustment_amount'];
				}
				$node['split'] = $data['account'];
				$node['reconcile'] = $data['reconcile'];
				$node['description'] = _l('reconcile_adjustment');
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['date'] = $data['adjustment_date'];
				$node['addedfrom'] = get_staff_user_id();
				
				$this->db->insert(db_prefix().'acc_account_history', $node);
				
				$node = [];
				$node['account'] = $data['account'];
				if($data['adjustment_amount'] > 0){
					$node['rel_id'] = 0;
					$node['rel_type'] = 'deposit';
					$node['debit'] = 0;
					$node['credit'] = $data['adjustment_amount'];
					}else{
					$node['rel_id'] = 0;
					$node['rel_type'] = 'cheque_expense';
					$node['debit'] = $data['adjustment_amount'];
					$node['credit'] = 0;
				}
				
				$node['split'] = $account->id;
				$node['reconcile'] = $data['reconcile'];
				$node['description'] = _l('reconcile_adjustment');
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['date'] = $data['adjustment_date'];
				$node['addedfrom'] = get_staff_user_id();
				
				$this->db->insert(db_prefix().'acc_account_history', $node);
				
				$insert_id = $this->db->insert_id();
				if ($insert_id) {
					return $insert_id;
				}
				}else{
				$this->db->insert(db_prefix().'acc_accounts', [
				'name' => '',
				'key_name' => 'acc_reconciliation_discrepancies',
				'account_type_id' => 15,
				'account_detail_type_id' => 139,
				]);
				
				$account_id = $this->db->insert_id();
				
				if ($account_id) {
					$node = [];
					$node['split'] = $data['account'];
					$node['account'] = $account_id;
					if($data['adjustment_amount'] > 0){
						$node['rel_id'] = $id;
						$node['rel_type'] = 'deposit';
						$node['debit'] = $data['adjustment_amount'];
						$node['credit'] = 0;
						}else{
						$node['rel_id'] = $id;
						$node['rel_type'] = 'cheque_expense';
						$node['debit'] = 0;
						$node['credit'] = $data['adjustment_amount'];
					}
					
					$node['reconcile'] = $data['reconcile'];
					$node['description'] = _l('reconcile_adjustment');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['date'] = $data['adjustment_date'];
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
					
					$node = [];
					$node['account'] = $data['account'];
					if($data['adjustment_amount'] > 0){
						$node['rel_id'] = 0;
						$node['rel_type'] = 'deposit';
						$node['debit'] = 0;
						$node['credit'] = $data['adjustment_amount'];
						}else{
						$node['rel_id'] = 0;
						$node['rel_type'] = 'cheque_expense';
						$node['debit'] = $data['adjustment_amount'];
						$node['credit'] = 0;
					}
					
					$node['split'] = $account_id;
					$node['reconcile'] = $data['reconcile'];
					$node['description'] = _l('reconcile_adjustment');
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['date'] = $data['adjustment_date'];
					$node['addedfrom'] = get_staff_user_id();
					
					$this->db->insert(db_prefix().'acc_account_history', $node);
					
					$insert_id = $this->db->insert_id();
					
					if ($insert_id) {
						return $insert_id;
					}
				}
			}
			
			return false;
		}
		
		/**
			* finish reconcile account
			* @param  array $data 
			* @return boolean       
		*/
		public function finish_reconcile_account($data){
			$affectedRows = 0;
			
			if($data['history_ids'] != ''){
				$history_ids = explode(', ', $data['history_ids']);
				
				foreach ($history_ids as $key => $value) {
					$this->db->where('id', $value);
					$this->db->update(db_prefix().'acc_account_history', ['reconcile' => $data['reconcile']]);
					
					if ($this->db->affected_rows() > 0) {
						$affectedRows++;
					}
				}
			}
			
			if($data['finish'] == 1){
				$this->db->where('id', $data['reconcile']);
				$this->db->update(db_prefix().'acc_reconciles', ['finish' => 1]);
				
				if ($this->db->affected_rows() > 0) {
					$affectedRows++;
				}
				
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			
			return true;
		}
		
		/**
			* reconcile save for later
			* @param  array $data 
			* @return boolean       
		*/
		
		public function reconcile_save_for_later($data){
			$affectedRows = 0;
			if($data['history_ids'] != ''){
				$history_ids = explode(', ', $data['history_ids']);
				
				foreach ($history_ids as $key => $value) {
					$this->db->where('id', $value);
					$this->db->update(db_prefix().'acc_account_history', ['reconcile' => $data['reconcile']]);
					
					if ($this->db->affected_rows() > 0) {
						$affectedRows++;
					}
				}
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			return true;
		}
		
		/**
			* get data bank accounts dashboard
			* @param  array $data_filter 
			* @return array 
		*/
		public function get_data_bank_accounts_dashboard($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$where = $this->get_where_report_period();
			
			$account_type_details = $this->get_account_type_details();
			$data_return = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 3){
					$data_accounts['cash_and_cash_equivalents'][] = $value;
				}
				if($value['account_type_id'] == 7){
					$data_accounts['credit_card'][] = $value;
				}
			}
			$html = '<ul class="list-group">
			<li class="list-group-item bold">'. _l('bank_accounts_uppercase').'<span class="badge">'. _l('balance').'</span></li>';
			foreach ($data_accounts as $data_key => $data_account) {
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('account', $val['id']);
						if($where != ''){
							$this->db->where($where);
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						if($value['account_type_id'] == 10 || $value['account_type_id'] == 8 || $value['account_type_id'] == 9 || $value['account_type_id'] == 7){
							$html .= '<li class="list-group-item">'.$name.'<span class="badge">'.app_format_money($credits - $debits, $currency->name).'</span></li>';
							}else{
							$html .= '<li class="list-group-item">'.$name.'<span class="badge">'.app_format_money($debits - $credits, $currency->name).'</span></li>';
						}
						
						$data_return[] = ['name' => $name, 'balance' => $debits - $credits];
					}
				}
			}
			$html .= '</ul>';
			
			return $html;
		}
		
		/**
			* get data convert status dashboard
			* @param  array $data_filter 
			* @return array 
		*/
		public function get_data_convert_status_dashboard($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$where = $this->get_where_report_period();
			
			$data_currency = $currency->id;
			if($this->input->get('currency')){
				$data_currency = $this->input->get('currency');
				$currency = $this->currencies_model->get($data_currency);
			}
			
			$this->db->select_sum('total');
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_type = "invoice") = 0) and currency = '.$data_currency);
			$invoice = $this->db->get(db_prefix().'invoices')->row();
			
			$this->db->select_sum('amount');
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'expenses.id and ' . db_prefix() . 'acc_account_history.rel_type = "expense") = 0) and currency = '.$data_currency);
			$expense = $this->db->get(db_prefix().'expenses')->row();
			
			$where_payment = $this->get_where_report_period(db_prefix() . 'invoicepaymentrecords.date');
			$this->db->select_sum('amount');
			if($where_payment != ''){
				$this->db->where($where_payment);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_type = "payment") = 0) and currency = '.$data_currency);
			$this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id=' . db_prefix() . 'invoicepaymentrecords.invoiceid', 'left');
			$payment = $this->db->get(db_prefix().'invoicepaymentrecords')->row();
			
			
			$html = '<table class="table border table-striped no-margin">
			<tbody>
			<tr class="project-overview">
			<td colspan="3" class="text-center"><h4>'. _l('transaction_not_yet_converted').'</h4></td>
			</tr>
			<tr class="project-overview">
			<td class="bold" width="30%">'. _l('transaction').'</td>
			<td class="bold" width="30%">'. _l('invoice_table_quantity_heading').'</td>
			<td class="bold">'. _l('acc_amount').'</td>
			</tr>
			<tr class="project-overview '. ($invoice->total > 0 ? 'text-danger' : '').'">
			<td class="bold" width="30%"><a href="'.admin_url('accounting/transaction?group=sales&tab=invoice&status=has_not_been_converted').'">'. _l('invoice').'</a></td>
			<td width="30%">'. $this->count_invoice_not_convert_yet($data_currency, $where) .'</td>
			<td>'. app_format_money($invoice->total, $currency->name)  .'</td>
			</tr>
			<tr class="project-overview '. ($payment->amount > 0 ? 'text-danger' : '').'">
			<td class="bold" width="30%"><a href="'.admin_url('accounting/transaction?group=sales&tab=payment&status=has_not_been_converted').'">'. _l('payment').'</a></td>
			<td width="30%">'. $this->count_payment_not_convert_yet($data_currency, $where_payment)  .'</td>
			<td>'. app_format_money($payment->amount, $currency->name)  .'</td>
			</tr>
			<tr class="project-overview '. ($expense->amount > 0 ? 'text-danger' : '').'">
			<td class="bold" width="30%"><a href="'.admin_url('accounting/transaction?group=expenses&status=has_not_been_converted').'">'. _l('expense').'</a></td>
			<td width="30%">'. $this->count_expense_not_convert_yet($data_currency, $where)  .'</td>
			<td>'. app_format_money($expense->amount, $currency->name)  .'</td>
			</tr>
			</tbody>
			</table>';
			return $html;
		}
		
		/**
			* get data profit and loss chart
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss_chart(){
			$accounting_method = get_option('acc_accounting_method');
			
			$where = $this->get_where_report_period();
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						if($where != ''){
							$this->db->where($where);
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$total += $credits - $debits;
							}else{
							$total += $debits - $credits;
						}
					}
				}
				$data_total[$data_key] = $total;
			}
			
			$income = $data_total['income'] + $data_total['other_income'];
			$expenses = $data_total['expenses'] + $data_total['other_expenses'] + $data_total['cost_of_sales'];
			$net_income = $income - $expenses;
			
			return [$net_income, $income, $expenses];
		}
		
		/**
			* get data expenses chart
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_expenses_chart($data_filter){
			$where = $this->get_where_report_period();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			$total = 0;
			
			if($where != ''){
				$this->db->select('*, (SELECT (sum(debit) - sum(credit)) as balance FROM '.db_prefix().'acc_account_history where account = '.db_prefix().'acc_accounts.id and '.$where.') as amount');
				}else{
				$this->db->select('*, (SELECT (sum(debit) - sum(credit)) as balance FROM '.db_prefix().'acc_account_history where account = '.db_prefix().'acc_accounts.id) as amount');
			}
			
			$this->db->where('(account_type_id = 13 or account_type_id = 14 or account_type_id = 15)');
			$this->db->where('active', 1);
			
			$this->db->order_by('amount', 'desc');
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $k => $val) {
				if($k > 2){
					$total += $val['amount'];
					}else{
					if($acc_show_account_numbers == 1 && $val['number'] != ''){
						$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
						}else{
						$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
					}
					
					if($val['amount'] < 0){
						$data_return[] = ['name' => $name, 'y' => floatval(-$val['amount']), 'amount' => ''.floatval($val['amount'])];
						}else{
						$data_return[] = ['name' => $name, 'y' => floatval($val['amount']), 'amount' => ''.floatval($val['amount'])];
					}
				}
			}
			if($total < 0){
				$data_return[] = ['name' => _l('everything_else'), 'y' => floatval(-$total), 'amount' => ''.$total];
				}else{
				$data_return[] = ['name' => _l('everything_else'), 'y' => floatval($total), 'amount' => ''.$total];
			}
			return $data_return;
		}
		
		/**
			* get data income chart
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_income_chart($data_filter){
			$accounting_method = get_option('acc_accounting_method');
			$where = $this->get_where_report_period('date');
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			if(isset($data_filter['currency'])){
				$data_currency = $data_filter['currency'];
				}else{
				$data_currency = $currency->id;
			}
			
			$last_30_days = date('Y-m-d', strtotime('today - 30 days'));
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->select('*, (SELECT sum(amount) as amount FROM '.db_prefix().'invoicepaymentrecords where invoiceid = '.db_prefix().'invoices.id and date >= "'.$last_30_days.'") as amount');
			$this->db->where('currency', $data_currency);
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			$mapped = 0;
			$open_invoice = 0;
			$overdue_invoices = 0;
			$paid_last_30_days = 0;
			$list_invoice = '0';
			
			foreach ($invoices as $key => $value) {
				$list_invoice .= ','.$value['id'];
				
				$this->db->select('sum(credit) as credit');
				$this->db->where('rel_id', $value['id']);
				$this->db->where('rel_type', 'invoice');
				$this->db->where('tax < 1');
				$this->db->where('paid', 1);
				$count = $this->db->get(db_prefix().'acc_account_history')->row();
				if(isset($count->credit) && $count->credit > 0){
					$mapped += $count->credit;
					}else{
					if($value['status'] == 1){
						$open_invoice += $value['subtotal'];
						}elseif ($value['status'] == 2 && $value['amount'] > 0) {
						$paid_last_30_days += $value['subtotal'];
						}elseif ($value['status'] == 4) {
						$overdue_invoices += $value['subtotal'];
					}
				}
			}
			
			$data_return = [];
			$data_return[] = ['name' => _l('open_invoice'), 'data' => [floatval($open_invoice)]];
			$data_return[] = ['name' => _l('overdue_invoices'), 'data' => [floatval($overdue_invoices)]];
			$data_return[] = ['name' => _l('paid_last_30_days'), 'data' => [floatval($paid_last_30_days)]];
			
			$where = $this->get_where_report_period();
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_total = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				$total = 0;
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						if($where != ''){
							$this->db->where($where);
						}
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						if($currency->id != $data_currency){
							$this->db->where('1=0');
						}
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						if($where != ''){
							$this->db->where($where);
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$total += $credits - $debits;
							}else{
							$total += $debits - $credits;
						}
					}
				}
				$data_total[$data_key] = $total;
			}
			
			$income = $data_total['income'] + $data_total['other_income'];
			$data_return[] = ['name' => _l('has_been_mapping'), 'data' => [floatval($data_total['income'] + $data_total['other_income'])]];
			return $data_return;
		}
		
		/**
			* get data sales chart
			* @param  array $data_filter
			* @return array
		*/
		public function get_data_sales_chart($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			if(isset($data_filter['currency'])){
				$data_currency = $data_filter['currency'];
				}else{
				$data_currency = $currency->id;
			}
			
			
			$where = $this->get_where_report_period('date');
			
			if($where != ''){
				$this->db->where($where);
			}
			
			$this->db->where('currency', $data_currency);
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('currency', $data_currency);
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			$data_return = [];
			$data_date = [];
			
			$list_invoice = '0';
			foreach ($invoices as $key => $value) {
				$list_invoice .= ','.$value['id'];
				$this->db->where('rel_id', $value['id']);
				$this->db->where('rel_type', 'invoice');
				$this->db->where('paid', 1);
				$count = $this->db->count_all_results(db_prefix().'acc_account_history');
				
				if($count == 0){
					if(isset($data_date[$value['date']])){
						$data_date[$value['date']]['payment'] += floatval($value['subtotal']);
						}else{
						$data_date[$value['date']] = [];
						$data_date[$value['date']]['payment'] = floatval($value['subtotal']);
						$data_date[$value['date']]['expense'] = 0;
						$data_date[$value['date']]['invoice_have_been_mapping'] = 0;
						$data_date[$value['date']]['expense_have_been_mapping'] = 0;
					}
				}
			}
			
			$list_expense = '0';
			
			foreach ($expenses as $key => $value) {
				$list_expense .= ','.$value['id'];
				
				$this->db->where('rel_id', $value['id']);
				$this->db->where('rel_type', 'expense');
				$count = $this->db->count_all_results(db_prefix().'acc_account_history');
				if($count == 0){
					if(isset($data_date[$value['date']])){
						$data_date[$value['date']]['expense'] += floatval($value['amount']);
						}else{
						$data_date[$value['date']] = [];
						$data_date[$value['date']]['expense'] = floatval($value['amount']);
						$data_date[$value['date']]['payment'] = 0;
						$data_date[$value['date']]['invoice_have_been_mapping'] = 0;
						$data_date[$value['date']]['expense_have_been_mapping'] = 0;
					}
				}
			}
			
			$account_type_details = $this->get_account_type_details();
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						$this->db->select('credit, debit, date, datecreated');
						if($currency->id != $data_currency){
							$this->db->where('1=0');
						}
						if($where != ''){
							$this->db->where($where);
						}
						$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
						
						foreach ($account_history as $val) {
							$credits = $val['credit'] != '' ? $val['credit'] : 0;
							$debits = $val['debit'] != '' ? $val['debit'] : 0;
							$date = $val['date'] != '' ? $val['date'] : date('Y-m-d', strtotime($val['datecreated']));
							
							if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
								$total = $credits - $debits;
								if(isset($data_date[$date])){
									$data_date[$date]['invoice_have_been_mapping'] += floatval($total);
									
									}else{
									$data_date[$date] = [];
									$data_date[$date]['invoice_have_been_mapping'] = floatval($total);
									$data_date[$date]['expense_have_been_mapping'] = 0;
									$data_date[$date]['payment'] = 0;
									$data_date[$date]['expense'] = 0;
								}
								}else{
								$total = $debits - $credits;
								if(isset($data_date[$date])){
									$data_date[$date]['expense_have_been_mapping'] += floatval($total);
									}else{
									$data_date[$date] = [];
									$data_date[$date]['expense_have_been_mapping'] = floatval($total);
									$data_date[$date]['invoice_have_been_mapping'] = 0;
									$data_date[$date]['payment'] = 0;
									$data_date[$date]['expense'] = 0;
								}
							}
						}
						
					}
				}
			}
			
			$sales = [];
			$invoice_have_been_mapping = [];
			$expense_have_been_mapping = [];
			$expenses = [];
			$categories = [];
			$date_array = [];
			
			foreach ($data_date as $d => $val) {
				$_date = $d;
				foreach ($data_date as $date => $value) {
					if(strtotime($_date) > (strtotime($date)) && !in_array($date,$date_array)){
						$_date = $date;
						}elseif(!in_array($date,$date_array) && in_array($_date,$date_array)){
						$_date = $date;
					}
				}
				
				$date_array[] = $_date;
				
			}
			
			foreach ($date_array as $date) {
				if(isset($data_date[$date])){
					$sales[] = $data_date[$date]['payment'];
					$expenses[] = $data_date[$date]['expense'];
					$invoice_have_been_mapping[] = $data_date[$date]['invoice_have_been_mapping'];
					$expense_have_been_mapping[] = $data_date[$date]['expense_have_been_mapping'];
					$categories[] = _d($date);
				}
			}
			
			$data_return = [
			'data' => [
			['name' => _l('sales'), 'data' => $sales],
			['name' => _l('sales_have_been_mapping'), 'data' => $invoice_have_been_mapping],
			['name' => _l('expenses'), 'data' => $expenses],
			['name' => _l('expenses_have_been_mapping'), 'data' => $expense_have_been_mapping],
			],
			'categories' => $categories
			];
			return $data_return;
		}
		
		/**
			* add rule
			* @param array $data 
		*/
		public function add_rule($data){
			if(isset($data['type'])){
				$type = $data['type'];
				unset($data['type']);
			}
			
			if(isset($data['subtype'])){
				$subtype = $data['subtype'];
				unset($data['subtype']);
			}
			
			if(isset($data['text'])){
				$text = $data['text'];
				unset($data['text']);
			}
			
			if(isset($data['subtype_amount'])){
				$subtype_amount = $data['subtype_amount'];
				unset($data['subtype_amount']);
			}
			
			if(!isset($data['auto_add'])){
				$data['auto_add'] = 0;
			}
			
			$this->db->insert(db_prefix().'acc_banking_rules', $data);
			
			$insert_id = $this->db->insert_id();
			
			if ($insert_id) {
				if(isset($type)){
					foreach ($type as $key => $value) {
						$this->db->insert(db_prefix().'acc_banking_rule_details', [
						'rule_id' => $insert_id,
						'type' => $value,
						'subtype' => $subtype[$key],
						'subtype_amount' => $subtype_amount[$key],
						'text' => $text[$key],
						]);
					}
				}
				
				return $insert_id;
			}
			
			return false;
		}
		
		/**
			* update rule
			* @param array $data 
		*/
		public function update_rule($data, $id){
			$affectedRows = 0;
			
			if(isset($data['type'])){
				$type = $data['type'];
				unset($data['type']);
			}
			
			if(isset($data['subtype'])){
				$subtype = $data['subtype'];
				unset($data['subtype']);
			}
			
			if(isset($data['text'])){
				$text = $data['text'];
				unset($data['text']);
			}
			
			if(isset($data['subtype_amount'])){
				$subtype_amount = $data['subtype_amount'];
				unset($data['subtype_amount']);
			}
			
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_banking_rules', $data);
			
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			
			$this->db->where('rule_id', $id);
			$this->db->delete(db_prefix() . 'acc_banking_rule_details');
			
			if(isset($type)){
				foreach ($type as $key => $value) {
					$this->db->insert(db_prefix().'acc_banking_rule_details', [
					'rule_id' => $id,
					'type' => $value,
					'subtype_amount' => $subtype_amount[$key],
					'subtype' => $subtype[$key],
					'text' => $text[$key],
					]);
				}
			}
			
			if ($affectedRows > 0) {
				return $insert_id;
			}
			
			return false;
		}
		
		/**
			* get rule
			* @param  integer $id 
			* @param  array $where 
			* @return object     
		*/
		public function get_rule($id = '', $where = []){
			if($id != ''){
				$this->db->where('id', $id);
				$rule = $this->db->get(db_prefix() . 'acc_banking_rules')->row();
				
				if($rule){
					$this->db->where('rule_id', $id);
					$rule->details = $this->db->get(db_prefix() . 'acc_banking_rule_details')->result_array();
				}
				return $rule;
			}
			
			$this->db->where($where);
			$rule = $this->db->get(db_prefix() . 'acc_banking_rules')->result_array();
			if($rule){
				foreach ($rule as $key => $value) {
					$this->db->where('rule_id', $value['id']);
					$rule[$key]['details'] = $this->db->get(db_prefix() . 'acc_banking_rule_details')->result_array();
				}
			}
			
			return $rule;
		}
		
		/**
			* delete journal entry
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_rule($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_banking_rules');
			if ($this->db->affected_rows() > 0) {
				$this->db->where('rule_id', $id);
				$this->db->delete(db_prefix() . 'acc_banking_rule_details');
				
				return true;
			}
			return false;
		}
		
		/**
			* insert batch banking
			* @param  array $data_insert 
			* @return boolean              
		*/
		public function insert_batch_banking($data_insert){
			$rule = $this->get_rule();
			
			foreach ($data_insert as $value) {
				$value['date'] = str_replace('/', '-', $value['date']);
				$value['date'] = date('Y-m-d', strtotime($value['date']));
				$this->db->insert(db_prefix().'acc_transaction_bankings', $value);
				
				$insert_id = $this->db->insert_id();
				
				if (!$insert_id) {
					continue;
				}
				
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($value['date']) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						continue;
					}
				}
				
				$amount = $value['deposits'];
				if($value['withdrawals'] > 0){
					$amount = $value['withdrawals'];
				}
				foreach ($rule as $val) {
					if($this->check_rule($val, $value)){
						if($val['then'] == 'exclude'){
							break;
							}elseif($val['auto_add'] == 0){
							continue;
						}
						
						$data = [];
						$node = [];
						$node['split'] = $val['payment_account'];
						$node['account'] = $val['deposit_to'];
						$node['debit'] = $amount;
						$node['date'] = $val['date'];
						$node['credit'] = 0;
						$node['description'] = _l('banking_rule');
						$node['rel_id'] = $insert_id;
						$node['rel_type'] = 'banking';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data[] = $node;
						
						$node = [];
						$node['split'] = $val['deposit_to'];
						$node['account'] = $val['payment_account'];
						$node['date'] = $val['date'];
						$node['debit'] = 0;
						$node['credit'] = $amount;
						$node['description'] = _l('banking_rule');
						$node['rel_id'] = $insert_id;
						$node['rel_type'] = 'banking';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data[] = $node;
						
						$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data);
						
						
						break;
					}
				}
			}
			
			return true;
		}
		
		/**
			* check rule
			* @param  array $rule 
			* @param  array $data 
			* @return boolean       
		*/
		public function check_rule($rule, $data){
			$check = false;
			$amount = $data['deposits'];
			if($data['withdrawals'] > 0){
				$amount = $data['withdrawals'];
			}
			if(($rule['transaction'] == 'money_out' && $data['withdrawals'] > 0) || ($rule['transaction'] == 'money_in' && $data['deposits'] > 0)){
				if($rule['following'] == 'any'){
					foreach ($rule['details'] as $v) {
						if ($v['type'] == 'amount') {
							switch ($v['subtype_amount']) {
								case 'does_not_equal':
								if(floatval($v['text']) != $amount){
									return true;
								}
								break;
								case 'equals':
								if(floatval($v['text']) == $amount){
									return true;
								}
								break;
								case 'is_greater_than':
								if(floatval($v['text']) < $amount){
									return true;
								}
								break;
								case 'is_loss_than':
								if(floatval($v['text']) > $amount){
									return true;
								}
								break;
								default:
								break;
							}
							}elseif($v['type'] == 'description'){
							switch ($v['subtype']) {
								case 'contains':
								if (str_contains($data['description'], $v['text'])) { 
									return true;
								}
								break;
								case 'does_not_contain':
								if (!str_contains($data['description'], $v['text'])) { 
									return true;
								}
								break;
								case 'is_exactly':
								if ($data['description'] == $v['text']) { 
									return true;
								}
								break;
								default:
								break;
							}
						}                      
					}
					}else{
					foreach ($rule['details'] as $v) {
						if ($v['type'] == 'amount') {
							switch ($v['subtype_amount']) {
								case 'does_not_equal':
								if(floatval($v['text']) == $amount){
									return false;
								}
								break;
								case 'equals':
								if(floatval($v['text']) != $amount){
									return false;
								}
								break;
								case 'is_greater_than':
								if(floatval($v['text']) > $amount){
									return false;
								}
								break;
								case 'is_loss_than':
								if(floatval($v['text']) < $amount){
									return false;
								}
								break;
								default:
								break;
							}
							}elseif($v['type'] == 'description'){
							switch ($v['subtype']) {
								case 'contains':
								if (!str_contains($data['description'], $v['text'])) { 
									return false;
								}
								break;
								case 'does_not_contain':
								if (str_contains($data['description'], $v['text'])) { 
									return false;
								}
								break;
								case 'is_exactly':
								if ($data['description'] != $v['text']) { 
									return false;
								}
								break;
								default:
								break;
							}
						} 
						$check = true;                     
					}
					
					return true;
				}
			}
			
			return $check;
		}
		
		/**
			* get data journal
			* @return array 
		*/
		public function get_data_account_history($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$account = 0;
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			if(isset($data_filter['account'])){
				$account = $data_filter['account'];
			}
			
			$info_account = $this->accounting_model->get_accounts($account);
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$data_report = [];
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
			if($account != ''){
				$this->db->where('account', $account);
			}
			$this->db->order_by('date', 'asc');
			
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			$balance = 0;
			$amount = 0;
			foreach ($account_history as $v) {
				$decrease = 0;
				$increase = 0;
				if($info_account->account_type_id == 7 || $info_account->account_type_id == 8){
					$increase = $v['credit'];
					$decrease = $v['debit'];
					$balance += ($v['credit'] - $v['debit']);
					}elseif($info_account->account_type_id == 1){
					$increase = $v['credit'];
					$decrease = $v['debit'];
					$balance += ($v['debit'] - $v['credit']);
					}else{
					$increase = $v['debit'];
					$decrease = $v['credit'];
					$balance += ($v['debit'] - $v['credit']);
				}
				$data_report[] =   [
				'date' => date('Y-m-d', strtotime($v['date'])),
				'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
				'type' => _l($v['rel_type']),
				'name' => (isset($account_name[$v['account']]) ? $account_name[$v['account']] : ''),
				'description' => $v['description'],
				'customer' => $v['customer'],
				'decrease' => $decrease,
				'increase' => $increase,
				'balance' => $balance,
				];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date, 'account_type' => $info_account->account_type_id];
		}
		
		/**
			* Gets the where report period.
			*
			* @param      string  $field  The field
			*
			* @return     string  The where report period.
		*/
		private function get_where_report_period($field = 'date')
		{
			$months_report      = $this->input->get('date_filter');
			
			$custom_date_select = '';
			if ($months_report != '') {
				if (is_numeric($months_report)) {
					// Last month
					if ($months_report == '1') {
						$beginMonth = date('Y-m-01', strtotime('first day of last month'));
						$endMonth   = date('Y-m-t', strtotime('last day of last month'));
						} else {
						$months_report = (int) $months_report;
						$months_report--;
						$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
						$endMonth   = date('Y-m-t');
					}
					
					$custom_date_select = '(' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
					} elseif ($months_report == 'last_30_days') {
					$custom_date_select = '(' . $field . ' BETWEEN "' . date('Y-m-d', strtotime('today - 30 days')) . '" AND "' . date('Y-m-d') . '")';
					} elseif ($months_report == 'this_month') {
					$custom_date_select = '(' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
					} elseif ($months_report == 'last_month') {
					$this_month = date('m') - 1;
					$custom_date_select = '(' . $field . ' BETWEEN "' . date("Y-m-d", strtotime("first day of previous month")) . '" AND "' . date("Y-m-d", strtotime("last day of previous month")) . '")';
					}elseif ($months_report == 'this_quarter') {
					$current_month = date('m');
					$current_year = date('Y');
					if($current_month>=1 && $current_month<=3)
					{
						$start_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // timestamp or 1-Januray 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM means end of 31 March
					}
					else  if($current_month>=4 && $current_month<=6)
					{
						$start_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM means end of 30 June
					}
					else  if($current_month>=7 && $current_month<=9)
					{
						$start_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM means end of 30 September
					}
					else  if($current_month>=10 && $current_month<=12)
					{
						$start_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-January-'.($current_year+1)));  // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
					}
					$custom_date_select = '(' . $field . ' BETWEEN "' .
					$start_date .
					'" AND "' .
					$end_date . '")';
					
					}elseif ($months_report == 'last_quarter') {
					$current_month = date('m');
					$current_year = date('Y');
					
					if($current_month>=1 && $current_month<=3)
					{
						$start_date = date('Y-m-d', strtotime('1-October-'.($current_year-1)));  // timestamp or 1-October Last Year 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // // timestamp or 1-January  12:00:00 AM means end of 31 December Last year
					} 
					else if($current_month>=4 && $current_month<=6)
					{
						$start_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // timestamp or 1-Januray 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM means end of 31 March
					}
					else  if($current_month>=7 && $current_month<=9)
					{
						$start_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM means end of 30 June
					}
					else  if($current_month>=10 && $current_month<=12)
					{
						$start_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM
						$end_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM means end of 30 September
					}
					$custom_date_select = '(' . $field . ' BETWEEN "' .
					$start_date .
					'" AND "' .
					$end_date . '")';
					
					}elseif ($months_report == 'this_year') {
					$custom_date_select = '(' . $field . ' BETWEEN "' .
					date('Y-m-d', strtotime(date('Y-01-01'))) .
					'" AND "' .
					date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
					} elseif ($months_report == 'last_year') {
					$custom_date_select = '(' . $field . ' BETWEEN "' .
					date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
					'" AND "' .
					date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
					} elseif ($months_report == 'custom') {
					$from_date = to_sql_date($this->input->post('report_from'));
					$to_date   = to_sql_date($this->input->post('report_to'));
					if ($from_date == $to_date) {
						$custom_date_select = '' . $field . ' = "' . $from_date . '"';
						} else {
						$custom_date_select = '(' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
					}
					} elseif(!(strpos($months_report, 'financial_year') === false)){
					$year = explode('financial_year_', $months_report);
					
					$first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
					
					$month = date('m', strtotime($first_month_of_financial_year));
					$custom_date_select = '(' . $field . ' BETWEEN "' . date($year[1].'-'.$month.'-01') . '" AND "' . date(($year[1]+1).'-'.$month.'-01') . '")';
				}
			}
			
			return $custom_date_select;
		}
		
		/**
			* delete all data the accounting module
			*
			* @param      int   $id     The identifier
			*
			* @return     boolean
		*/
		public function reset_data()
		{
			$affectedRows = 0;
			if ($this->db->table_exists(db_prefix() . 'acc_accounts')) {
				$this->db->query('DROP TABLE `'.db_prefix() .'acc_accounts`;');
				$this->db->query('CREATE TABLE ' . db_prefix() . "acc_accounts (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255) NOT NULL,
				`key_name` VARCHAR(255) NULL,
				`number` VARCHAR(45) NULL,
				`parent_account` INT(11) NULL,
				`account_type_id` INT(11) NOT NULL,
				`account_detail_type_id` INT(11) NOT NULL,
				`balance` DECIMAL(15,2) NULL,
				`balance_as_of` DATE NULL,
				`description` TEXT NULL,
				`default_account` INT(11) NOT NULL DEFAULT 0,
				`active` INT(11) NOT NULL DEFAULT 1,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=" . $this->db->char_set . ';');
				$this->add_default_account();
				$this->add_default_account_new();
				$affectedRows++;
			}
			
			update_option('acc_first_month_of_financial_year', 'January');
			update_option('acc_first_month_of_tax_year', 'same_as_financial_year');
			update_option('acc_accounting_method', 'accrual');
			update_option('acc_close_the_books', 0);
			update_option('acc_allow_changes_after_viewing', 'allow_changes_after_viewing_a_warning');
			update_option('acc_enable_account_numbers', 0);
			update_option('acc_show_account_numbers', 0);
			
			update_option('acc_add_default_account', 0);
			update_option('acc_add_default_account_new', 0);
			update_option('acc_invoice_automatic_conversion', 1);
			update_option('acc_payment_automatic_conversion', 1);
			update_option('acc_expense_automatic_conversion', 1);
			update_option('acc_tax_automatic_conversion', 1);
			
			update_option('acc_invoice_payment_account', 66);
			update_option('acc_invoice_deposit_to', 1);
			update_option('acc_payment_payment_account', 1);
			update_option('acc_payment_deposit_to', 13);
			update_option('acc_expense_payment_account', 13);
			update_option('acc_expense_deposit_to', 80);
			update_option('acc_tax_payment_account', 29);
			update_option('acc_tax_deposit_to', 1);
			
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_account_history');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_banking_rules');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_banking_rule_details');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_journal_entries');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_reconciles');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_transaction_bankings');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_transfers');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_item_automatics');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			
			$this->db->where('id > 0');
			$this->db->delete(db_prefix() . 'acc_tax_mappings');
			if ($this->db->affected_rows() > 0) {
				$affectedRows++;
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* Change account status / active / inactive
			* @param  mixed $id     staff id
			* @param  mixed $status status(0/1)
		*/
		public function change_account_status($id, $status)
		{
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'acc_accounts', [
			'active' => $status,
			]);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* Automatic invoice conversion
			* @param  integer $invoice_id 
			* @return boolean
		*/
		public function automatic_invoice_conversion($invoice_id){
			$this->db->where('rel_id', $invoice_id);
			$this->db->where('rel_type', 'invoice');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			$affectedRows = 0;
			
			if($count > 0 || get_option('acc_invoice_automatic_conversion') == 0){
				return false;
			}
			
			$this->load->model('invoices_model');
			$invoice = $this->invoices_model->get($invoice_id);
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$currency_converter = 0;
			if($invoice->currency_name != $currency->name){
				$currency_converter = 1;
			}
			
			$payment_account = get_option('acc_invoice_payment_account');
			$deposit_to = get_option('acc_invoice_deposit_to');
			$tax_payment_account = get_option('acc_tax_payment_account');
			$tax_deposit_to = get_option('acc_tax_deposit_to');
			
			if($invoice){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($invoice->date) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				$paid = 0;
				if($invoice->status == 2){
					$paid = 1;
				}
				$data_insert = [];
				
				$items = get_items_table_data($invoice, 'invoice', 'html', true);
				
				foreach($items->taxes() as $tax){
					$t = explode('|', $tax['tax_name']);
					$tax_name = '';
					$tax_rate = 0;
					if(isset($t[0])){
						$tax_name = $t[0];
					}
					if(isset($t[1])){
						$tax_rate = $t[1];
					}
					
					$this->db->where('name', $tax_name);
					$this->db->where('taxrate', $tax_rate);
					$_tax = $this->db->get(db_prefix().'taxes')->row();
					
					$total_tax = $tax['total_tax'];
					if($currency_converter == 1){
						$total_tax = round($this->currency_converter($invoice->currency_name, $currency->name, $tax['total_tax']), 2);
					}
					
					if($_tax){
						$tax_mapping = $this->get_tax_mapping($_tax->id);
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->payment_account;
							$node['account'] = $tax_mapping->deposit_to;
							$node['tax'] = $_tax->id;
							$node['item'] = 0;
							$node['paid'] = $paid;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['customer'] = $invoice->clientid;
							$node['date'] = $invoice->date;
							$node['description'] = '';
							$node['rel_id'] = $invoice_id;
							$node['rel_type'] = 'invoice';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->deposit_to;
							$node['customer'] = $invoice->clientid;
							$node['account'] = $tax_mapping->payment_account;
							$node['tax'] = $_tax->id;
							$node['item'] = 0;
							$node['paid'] = $paid;
							$node['date'] = $invoice->date;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $invoice_id;
							$node['rel_type'] = 'invoice';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $_tax->id;
							$node['item'] = 0;
							$node['date'] = $invoice->date;
							$node['paid'] = $paid;
							$node['debit'] = $total_tax;
							$node['customer'] = $invoice->clientid;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $invoice_id;
							$node['rel_type'] = 'invoice';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['customer'] = $invoice->clientid;
							$node['account'] = $tax_payment_account;
							$node['date'] = $invoice->date;
							$node['tax'] = $_tax->id;
							$node['item'] = 0;
							$node['paid'] = $paid;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $invoice_id;
							$node['rel_type'] = 'invoice';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
						}else{
						$node = [];
						$node['split'] = $tax_payment_account;
						$node['account'] = $tax_deposit_to;
						$node['tax'] = 0;
						$node['item'] = 0;
						$node['date'] = $invoice->date;
						$node['paid'] = $paid;
						$node['debit'] = $total_tax;
						$node['customer'] = $invoice->clientid;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $invoice_id;
						$node['rel_type'] = 'invoice';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $tax_deposit_to;
						$node['customer'] = $invoice->clientid;
						$node['account'] = $tax_payment_account;
						$node['date'] = $invoice->date;
						$node['tax'] = 0;
						$node['item'] = 0;
						$node['paid'] = $paid;
						$node['debit'] = 0;
						$node['credit'] = $total_tax;
						$node['description'] = '';
						$node['rel_id'] = $invoice_id;
						$node['rel_type'] = 'invoice';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
				}
				
				foreach ($invoice->items as $value) {
					$item = $this->get_item_by_name($value['description']);
					$item_id = 0;
					if(isset($item->id)){
						$item_id = $item->id;
					}
					
					$item_total = $value['qty'] * $value['rate'];
					if($currency_converter == 1){
						$item_total = round($this->currency_converter($invoice->currency_name, $currency->name, $value['qty'] * $value['rate']), 2);
					}
					
					$item_automatic = $this->get_item_automatic($item_id);
					
					if($item_automatic){
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['item'] = $item_id;
						$node['date'] = $invoice->date;
						$node['paid'] = $paid;
						$node['debit'] = $item_total;
						$node['customer'] = $invoice->clientid;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $invoice_id;
						$node['rel_type'] = 'invoice';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['customer'] = $invoice->clientid;
						$node['account'] = $item_automatic->income_account;
						$node['item'] = $item_id;
						$node['paid'] = $paid;
						$node['date'] = $invoice->date;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $invoice_id;
						$node['rel_type'] = 'invoice';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['customer'] = $invoice->clientid;
						$node['paid'] = $paid;
						$node['date'] = $invoice->date;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $invoice_id;
						$node['rel_type'] = 'invoice';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['customer'] = $invoice->clientid;
						$node['account'] = $payment_account;
						$node['item'] = $item_id;
						$node['date'] = $invoice->date;
						$node['paid'] = $paid;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $invoice_id;
						$node['rel_type'] = 'invoice';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
				}
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* Automatic payment conversion
			* @param  integer $payment_id 
			* @return boolean
		*/
		public function automatic_payment_conversion($payment_id){
			$this->db->where('rel_id', $payment_id);
			$this->db->where('rel_type', 'payment');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			
			if($count > 0){
				return false;
			}
			
			$this->load->model('payments_model');
			$payment = $this->payments_model->get($payment_id);
			$payment_account = get_option('acc_payment_payment_account');
			$deposit_to = get_option('acc_payment_deposit_to');
			$affectedRows = 0;
			
			$this->automatic_invoice_conversion($payment->invoiceid);
			
			if($payment){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($payment->date) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				$this->load->model('invoices_model');
				$invoice = $this->invoices_model->get($payment->invoiceid);
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
				$payment_total = $payment->amount;
				if($invoice->currency_name != $currency->name){
					$payment_total = round($this->currency_converter($invoice->currency_name, $currency->name, $payment->amount), 2);
				}
				
				if(get_option('acc_active_payment_mode_mapping') == 1){
					$payment_mode_mapping = $this->get_payment_mode_mapping($payment->paymentmode);
					
					
					$data_insert = [];
					if($payment_mode_mapping){
						$node = [];
						$node['split'] = $payment_mode_mapping->payment_account;
						$node['account'] = $payment_mode_mapping->deposit_to;
						$node['date'] = $payment->date;
						$node['debit'] = $payment_total;
						$node['customer'] = $invoice->clientid;
						$node['credit'] = 0;
						$node['tax'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $payment_id;
						$node['rel_type'] = 'payment';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $payment_mode_mapping->deposit_to;
						$node['customer'] = $invoice->clientid;
						$node['account'] = $payment_mode_mapping->payment_account;
						$node['date'] = $payment->date;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $payment_total;
						$node['description'] = '';
						$node['rel_id'] = $payment_id;
						$node['rel_type'] = 'payment';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
					}else{
					if(get_option('acc_payment_automatic_conversion') == 1){
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['customer'] = $invoice->clientid;
						$node['debit'] = $payment_total;
						$node['credit'] = 0;
						$node['date'] = $payment->date;
						$node['description'] = '';
						$node['rel_id'] = $payment_id;
						$node['rel_type'] = 'payment';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['customer'] = $invoice->clientid;
						$node['account'] = $payment_account;
						$node['date'] = $payment->date;
						$node['debit'] = 0;
						$node['credit'] = $payment_total;
						$node['description'] = '';
						$node['rel_id'] = $payment_id;
						$node['rel_type'] = 'payment';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* Automatic expense conversion
			* @param  integer $expense_id 
			* @return boolean
		*/
		public function automatic_expense_conversion($expense_id){
			$this->load->model('expenses_model');
			$expense = $this->expenses_model->get($expense_id);
			$payment_account = get_option('acc_expense_payment_account');
			$deposit_to = get_option('acc_expense_deposit_to');
			$tax_payment_account = get_option('acc_tax_payment_account');
			$tax_deposit_to = get_option('acc_tax_deposit_to');
			$payment_mode_payment_account = get_option('acc_expense_payment_payment_account');
			$payment_mode_deposit_to = get_option('acc_expense_payment_deposit_to');
			$affectedRows = 0;
			
			if($expense){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($expense->date) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
				$expense_total = $expense->amount;
				if($expense->currency_data->name != $currency->name){
					$expense_total = round($this->currency_converter($expense->currency_data->name, $currency->name, $expense->amount), 2);
				}
				
				$data_insert = [];
				
				if(get_option('acc_active_expense_category_mapping') == 1){
					$expense_category_mapping = $this->get_expense_category_mapping($expense->category);
					if($expense_category_mapping){
						if($expense_category_mapping->preferred_payment_method == 1 && $expense->paymentmode > 0){
							$payment_mode_mapping = $this->get_payment_mode_mapping($expense->paymentmode);
							
							if($payment_mode_mapping){
								if(get_option('acc_active_payment_mode_mapping') == 1){
									$node = [];
									$node['split'] = $payment_mode_mapping->expense_payment_account;
									$node['account'] = $payment_mode_mapping->expense_deposit_to;
									$node['tax'] = 0;
									$node['debit'] = $expense_total;
									$node['credit'] = 0;
									$node['customer'] = $expense->clientid;
									$node['date'] = $expense->date;
									$node['description'] = '';
									$node['rel_id'] = $expense_id;
									$node['rel_type'] = 'expense';
									$node['datecreated'] = date('Y-m-d H:i:s');
									$node['addedfrom'] = get_staff_user_id();
									$data_insert[] = $node;
									
									$node = [];
									$node['split'] = $payment_mode_mapping->expense_deposit_to;
									$node['customer'] = $expense->clientid;
									$node['account'] = $payment_mode_mapping->expense_payment_account;
									$node['tax'] = 0;
									$node['date'] = $expense->date;
									$node['debit'] = 0;
									$node['credit'] = $expense_total;
									$node['description'] = '';
									$node['rel_id'] = $expense_id;
									$node['rel_type'] = 'expense';
									$node['datecreated'] = date('Y-m-d H:i:s');
									$node['addedfrom'] = get_staff_user_id();
									$data_insert[] = $node;
								}
							}
						}
						
						if(count($data_insert) == 0){   
							$node = [];
							$node['split'] = $expense_category_mapping->payment_account;
							$node['account'] = $expense_category_mapping->deposit_to;
							$node['date'] = $expense->date;
							$node['debit'] = $expense_total;
							$node['customer'] = $expense->clientid;
							$node['credit'] = 0;
							$node['tax'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $expense_category_mapping->deposit_to;
							$node['customer'] = $expense->clientid;
							$node['account'] = $expense_category_mapping->payment_account;
							$node['date'] = $expense->date;
							$node['tax'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $expense_total;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
						
					}
					}else{
					
					if(get_option('acc_expense_automatic_conversion') == 1){
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['debit'] = $expense_total;
						$node['customer'] = $expense->clientid;
						$node['date'] = $expense->date;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $expense_id;
						$node['rel_type'] = 'expense';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['account'] = $payment_account;
						$node['customer'] = $expense->clientid;
						$node['date'] = $expense->date;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $expense_total;
						$node['description'] = '';
						$node['rel_id'] = $expense_id;
						$node['rel_type'] = 'expense';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
				}
				
				if(count($data_insert) == 0 && $expense->paymentmode > 0){
					$payment_mode_mapping = $this->get_payment_mode_mapping($expense->paymentmode);
					
					if($payment_mode_mapping){
						if(get_option('acc_active_payment_mode_mapping') == 1){
							$node = [];
							$node['split'] = $payment_mode_mapping->expense_payment_account;
							$node['account'] = $payment_mode_mapping->expense_deposit_to;
							$node['tax'] = 0;
							$node['debit'] = $expense_total;
							$node['credit'] = 0;
							$node['customer'] = $expense->clientid;
							$node['date'] = $expense->date;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $payment_mode_mapping->expense_deposit_to;
							$node['customer'] = $expense->clientid;
							$node['account'] = $payment_mode_mapping->expense_payment_account;
							$node['tax'] = 0;
							$node['date'] = $expense->date;
							$node['debit'] = 0;
							$node['credit'] = $expense_total;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
						}else{
						if(get_option('acc_payment_expense_automatic_conversion') == 1){
							$node = [];
							$node['split'] = $payment_mode_payment_account;
							$node['account'] = $payment_mode_deposit_to;
							$node['tax'] = 0;
							$node['date'] = $expense->date;
							$node['debit'] = $expense_total;
							$node['customer'] = $expense->clientid;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $payment_mode_deposit_to;
							$node['customer'] = $expense->clientid;
							$node['account'] = $payment_mode_payment_account;
							$node['date'] = $expense->date;
							$node['tax'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $expense_total;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				
				if(get_option('acc_tax_automatic_conversion') == 1){
					if($expense->tax > 0){
						$this->db->where('id', $expense->tax);
						$tax = $this->db->get(db_prefix().'taxes')->row();
						$total_tax = 0;
						if($tax){
							$total_tax = ($tax->taxrate/100) * $expense_total;
						}
						$tax_mapping = $this->get_tax_mapping($expense->tax);
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->expense_payment_account;
							$node['account'] = $tax_mapping->expense_deposit_to;
							$node['tax'] = $expense->tax;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['customer'] = $expense->clientid;
							$node['date'] = $expense->date;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->expense_deposit_to;
							$node['customer'] = $expense->clientid;
							$node['account'] = $tax_mapping->expense_payment_account;
							$node['tax'] = $expense->tax;
							$node['date'] = $expense->date;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $expense->tax;
							$node['date'] = $expense->date;
							$node['debit'] = $total_tax;
							$node['customer'] = $expense->clientid;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['customer'] = $expense->clientid;
							$node['account'] = $tax_payment_account;
							$node['date'] = $expense->date;
							$node['tax'] = $expense->tax;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
					
					if($expense->tax2 > 0){
						$this->db->where('id', $expense->tax2);
						$tax = $this->db->get(db_prefix().'taxes')->row();
						$total_tax = 0;
						if($tax){
							$total_tax = ($tax->taxrate/100) * $expense_total;
						}
						$tax_mapping = $this->get_tax_mapping($expense->tax2);
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->expense_payment_account;
							$node['account'] = $tax_mapping->expense_deposit_to;
							$node['tax'] = $expense->tax2;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['customer'] = $expense->clientid;
							$node['date'] = $expense->date;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->expense_deposit_to;
							$node['customer'] = $expense->clientid;
							$node['account'] = $tax_mapping->expense_payment_account;
							$node['tax'] = $expense->tax2;
							$node['date'] = $expense->date;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $expense->tax2;
							$node['date'] = $expense->date;
							$node['debit'] = $total_tax;
							$node['customer'] = $expense->clientid;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['customer'] = $expense->clientid;
							$node['account'] = $tax_payment_account;
							$node['date'] = $expense->date;
							$node['tax'] = $expense->tax2;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $expense_id;
							$node['rel_type'] = 'expense';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		
		/**
			* count invoice not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object          
		*/
		public function count_invoice_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoices.id and ' . db_prefix() . 'acc_account_history.rel_type = "invoice") = 0) '.$where_currency);
			return $this->db->count_all_results(db_prefix().'invoices');
		}
		
		/**
			* count payment not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object
		*/
		public function count_payment_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'invoicepaymentrecords.id and ' . db_prefix() . 'acc_account_history.rel_type = "payment") = 0) '.$where_currency);
			$this->db->join(db_prefix() . 'invoices', db_prefix() . 'invoices.id=' . db_prefix() . 'invoicepaymentrecords.invoiceid', 'left');
			return $this->db->count_all_results(db_prefix().'invoicepaymentrecords');
		}
		
		
		public function get_accoun_main_group(){
			
			$acc_main_group = $this->db->get(db_prefix() . 'accountgroups')->result_array();
			return $acc_main_group;
		}
		public function get_account_subgroup(){
			
			$acc_main_group = $this->db->get(db_prefix() . 'accountgroupssub')->result_array();
			return $acc_main_group;
		}
		
		public function get_accounts_list(){
			
			/* $acc_main_group = $this->db->get(db_prefix() . 'accountgroupssub')->result_array();
			return $acc_main_group;*/
			$selected_company = $this->session->userdata('root_company');
			
			$ss = 'SELECT *
			FROM tblclients WHERE PlantID ='.$selected_company.' AND active = 1 AND SubActGroupID NOT IN("10022003","1002503","1002504","1002506","30000006","30000004","10022005","10022004","30000007","60001004","50003002")';
			
			$result_data = $this->db->query($ss)->result_array();
			return $result_data;
		}
		public function get_account_group_name($ActGroupID = ''){
			
			
			$ss1 = 'SELECT * FROM tblaccountgroups WHERE ActGroupID ="'.$ActGroupID.'"';
			
			$result_data = $this->db->query($ss1)->row();
			return $result_data;
		}
		public function get_account_subgroup_name($subActGroupID = ''){
			
			
			$ss = 'SELECT * FROM tblaccountgroupssub WHERE SubActGroupID ="'.$subActGroupID.'"';
			
			$result_data = $this->db->query($ss)->row();
			return $result_data;
		}
		
		/**
			* count expense not convert yet
			* @param  string $where
			* @param  integer $currency
			* @return object
		*/
		public function count_expense_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'expenses.id and ' . db_prefix() . 'acc_account_history.rel_type = "expense") = 0) '.$where_currency);
			return $this->db->count_all_results(db_prefix().'expenses');
		}
		
		/**
			* delete invoice convert
			* @param  integer $invoice_id 
			* @return boolean            
		*/
		public function delete_invoice_convert($invoice_id){
			$affectedRows = 0;
			
			$check = $this->delete_convert($invoice_id,'invoice');
			if($check){
				$affectedRows++;
			}
			
			$this->db->where('invoiceid', $invoice_id);
			$payments = $this->db->get(db_prefix() . 'invoicepaymentrecords')->result_array();
			
			foreach ($payments as $key => $value) {
				$check = $this->delete_convert($value['id'],'payment');
				if($check){
					$affectedRows++;
				}
			}
			
			if($affectedRows > 0){
				return true;
			}
			
			return false;
		}
		
		/**
			* invoice status changed
			* @param  array $data 
			* @return boolean       
		*/
		public function invoice_status_changed($data){
			if(isset($data['invoice_id']) && isset($data['status'])){
				if($data['status'] == 2){
					$this->db->where('rel_id', $data['invoice_id']);
					$this->db->where('rel_type', 'invoice');
					$this->db->update(db_prefix().'acc_account_history', ['paid' => 1]);
					if ($this->db->affected_rows() > 0) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		/**
			* get items are not yet auto
			* @return array
		*/
		public function get_items_not_yet_auto(){
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_item_automatics where ' . db_prefix() . 'acc_item_automatics.item_id = ' . db_prefix() . 'items.id) = 0)');
			return $this->db->get(db_prefix().'items')->result_array();
		}
		
		/**
			* add item automatic
			* @param array $data
			* @return boolean
		*/
		public function add_item_automatic($data){
			if(isset($data['id'])){
				unset($data['id']);
			}
			$items = [];
			if(isset($data['item'])){
				$items = $data['item'];
				unset($data['item']);
			}
			$data_insert = [];
			foreach ($items as $value) {
				$this->db->where('item_id', $value);
				$count = $this->db->count_all_results(db_prefix() . 'acc_item_automatics');
				
				if($count == 0){
					$node = [];
					$node['item_id'] = $value;
					$node['inventory_asset_account'] = $data['inventory_asset_account'];
					$node['income_account'] = $data['income_account'];
					$node['expense_account'] = $data['expense_account'];
					
					$data_insert[] = $node;
				}
				
			}
			
			$affectedRows = $this->db->insert_batch(db_prefix().'acc_item_automatics',  $data_insert);
			
			if ($affectedRows > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
			* update item automatic
			* @param array $data
			* @param  integer $id 
			* @return boolean
		*/
		public function update_item_automatic($data, $id){
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_item_automatics', $data);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* delete item automatic
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_item_automatic($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_item_automatics');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* Gets the item by name.
			*
			* @param      string  $item_name  The itemid
			*
			* @return     object  The item.
		*/
		public function get_item_by_name($item_name) {
			
			$this->db->where('description', $item_name);
			return $this->db->get(db_prefix() . 'items')->row();
		}
		
		/**
			* Gets the item automatic
			*
			* @param      string  $item_id  The itemid
			*
			* @return     object  The item automatic.
		*/
		public function get_item_automatic($item_id) {
			
			$this->db->where('item_id', $item_id);
			return $this->db->get(db_prefix() . 'acc_item_automatics')->row();
		}
		
		/**
			* delete banking
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_banking($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_transaction_bankings');
			if ($this->db->affected_rows() > 0) {
				$this->db->where('rel_id', $id);
				$this->db->where('rel_type', 'banking');
				$this->db->delete(db_prefix() . 'acc_account_history');
				
				return true;
			}
			return false;
		}
		
		/**
			* add tax mapping
			* @param array $data
			* @return boolean
		*/
		public function add_tax_mapping($data){
			if(isset($data['id'])){
				unset($data['id']);
			}
			$taxs = [];
			if(isset($data['tax'])){
				$taxs = $data['tax'];
				unset($data['tax']);
			}
			$data_insert = [];
			foreach ($taxs as $value) {
				$this->db->where('tax_id', $value);
				$count = $this->db->count_all_results(db_prefix() . 'acc_tax_mappings');
				
				if($count == 0){
					$node = [];
					$node['tax_id'] = $value;
					$node['payment_account'] = $data['payment_account'];
					$node['deposit_to'] = $data['deposit_to'];
					$node['expense_payment_account'] = $data['expense_payment_account'];
					$node['expense_deposit_to'] = $data['expense_deposit_to'];
					
					$data_insert[] = $node;
				}
				
			}
			
			$affectedRows = $this->db->insert_batch(db_prefix().'acc_tax_mappings',  $data_insert);
			
			if ($affectedRows > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
			* update tax mapping
			* @param array $data
			* @param  integer $id 
			* @return boolean
		*/
		public function update_tax_mapping($data, $id){
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_tax_mappings', $data);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* delete tax mapping
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_tax_mapping($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_tax_mappings');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* get taxes are not yet auto
			* @return array
		*/
		public function get_taxes_not_yet_auto(){
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_tax_mappings where ' . db_prefix() . 'acc_tax_mappings.tax_id = ' . db_prefix() . 'taxes.id) = 0)');
			return $this->db->get(db_prefix().'taxes')->result_array();
		}
		
		/**
			* Gets the tax mapping
			*
			* @param      string  $tax_id  The tax id
			*
			* @return     object  The tax mapping.
		*/
		public function get_tax_mapping($tax_id) {
			
			$this->db->where('tax_id', $tax_id);
			return $this->db->get(db_prefix() . 'acc_tax_mappings')->row();
		}
		
		/**
			* [currency_converter description]
			* @param  string $from   Currency Code
			* @param  string $to     Currency Code
			* @param  float $amount
			* @return float        
		*/
		public function currency_converter($from,$to,$amount)
		{
			$url = "https://api.frankfurter.app/latest?amount=$amount&from=$from&to=$to"; 
			
			$response = json_decode($this->api_get($url));
			
			if(isset($response->rates->$to)){
				return $response->rates->$to;
			}
			
			return false;
		}
		
		/**
			* api get
			* @param  string $url
			* @return string    
		*/
		public function api_get($url) {
			$curl = curl_init($url);
			
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_AUTOREFERER, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 120);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
			curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
			
			return curl_exec($curl);
		}
		
		/**
			* get expense category are not yet auto
			* @return array
		*/
		public function get_expense_category_not_yet_auto(){
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_expense_category_mappings where ' . db_prefix() . 'acc_expense_category_mappings.category_id = ' . db_prefix() . 'expenses_categories.id) = 0)');
			return $this->db->get(db_prefix().'expenses_categories')->result_array();
		}
		
		/**
			* add expense category mapping
			* @param array $data
			* @return boolean
		*/
		public function add_expense_category_mapping($data){
			if(isset($data['id'])){
				unset($data['id']);
			}
			$categorys = [];
			if(isset($data['category'])){
				$categorys = $data['category'];
				unset($data['category']);
			}
			
			if (!isset($data['preferred_payment_method'])) {
				$data['preferred_payment_method'] = 0;
			}
			
			$data_insert = [];
			foreach ($categorys as $value) {
				$this->db->where('category_id', $value);
				$count = $this->db->count_all_results(db_prefix() . 'acc_expense_category_mappings');
				
				if($count == 0){
					$node = [];
					$node['category_id'] = $value;
					$node['payment_account'] = $data['payment_account'];
					$node['deposit_to'] = $data['deposit_to'];
					
					$data_insert[] = $node;
				}
				
			}
			
			$affectedRows = $this->db->insert_batch(db_prefix().'acc_expense_category_mappings',  $data_insert);
			
			if ($affectedRows > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
			* update expense category mapping
			* @param array $data
			* @param  integer $id 
			* @return boolean
		*/
		public function update_expense_category_mapping($data, $id){
			if (!isset($data['preferred_payment_method'])) {
				$data['preferred_payment_method'] = 0;
			}
			
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_expense_category_mappings', $data);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* delete expense category mapping
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_expense_category_mapping($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_expense_category_mappings');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* Gets the expense category mappings
			*
			* @param      string  $category_id  The expense category id
			*
			* @return     object  The expense category mapping.
		*/
		public function get_expense_category_mapping($category_id) {
			
			$this->db->where('category_id', $category_id);
			return $this->db->get(db_prefix() . 'acc_expense_category_mappings')->row();
		}
		
		/**
			* get data tax detail report
			* @return array 
		*/
		public function get_data_tax_detail_report($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$data_report = [];
			$data_report['tax_collected_on_sales'] = [];
			$data_report['total_taxable_sales_in_period_before_tax'] = [];
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '") and tax > 0 and rel_type = "invoice" and debit > 0');
			if($accounting_method == 'cash'){
				$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
			}
			$this->db->order_by('date', 'asc');
			
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			
			$list_invoice = [];        
			$this->load->model('invoices_model');
			foreach ($account_history as $v) {
				
				if(!in_array($v['rel_id'], $list_invoice)){
					$list_invoice[] = $v['rel_id'];
					$invoice = $this->invoices_model->get($v['rel_id']);
					
					
					$data_report['total_taxable_sales_in_period_before_tax'][] = [
					'date' => date('Y-m-d', strtotime($v['date'])),
					'type' => _l($v['rel_type']),
					'description' => $v['description'],
					'customer' => $v['customer'],
					'amount' => $invoice->subtotal,
					];
				}
				
				$this->db->where('id', $v['tax']);
				$_tax = $this->db->get(db_prefix().'taxes')->row();
				
				$data_report['tax_collected_on_sales'][] = [
				'date' => date('Y-m-d', strtotime($v['date'])),
				'type' => _l($v['rel_type']),
				'tax_name' => $_tax->name,
				'tax_rate' => $_tax->taxrate,
				'description' => $v['description'],
				'customer' => $v['customer'],
				'amount' => $v['debit'],
				];
			}
			
			$data_report['tax_reclaimable_on_purchases'] = [];
			$data_report['total_taxable_purchases_in_period_before_tax'] = [];
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '") and tax > 0 and rel_type = "expense" and credit > 0');
			if($accounting_method == 'cash'){
				$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
			}
			$this->db->order_by('date', 'asc');
			
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			
			$list_expense = [];        
			$this->load->model('expenses_model');
			foreach ($account_history as $v) {
				
				if(!in_array($v['rel_id'], $list_expense)){
					$list_expense[] = $v['rel_id'];
					
					$expense = $this->expenses_model->get($v['rel_id']);
					
					$data_report['total_taxable_purchases_in_period_before_tax'][] = [
					'date' => date('Y-m-d', strtotime($v['date'])),
					'type' => _l($v['rel_type']),
					'description' => $v['description'],
					'customer' => $v['customer'],
					'amount' => $expense->amount,
					];
				}
				
				$this->db->where('id', $v['tax']);
				$_tax = $this->db->get(db_prefix().'taxes')->row();
				
				$data_report['tax_reclaimable_on_purchases'][] = [
				'date' => date('Y-m-d', strtotime($v['date'])),
				'type' => _l($v['rel_type']),
				'tax_name' => $_tax->name,
				'tax_rate' => $_tax->taxrate,
				'description' => $v['description'],
				'customer' => $v['customer'],
				'amount' => $v['credit'],
				];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data tax summary report
			* @return array 
		*/
		public function get_data_tax_summary_report($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$tax = 0;
			if(isset($data_filter['tax'])){
				$tax = $data_filter['tax'];
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$data_report = [];
			$data_report['tax_collected_on_sales'] = 0;
			$data_report['total_taxable_sales_in_period_before_tax'] = 0;
			$data_report['adjustments_to_tax_on_sales'] = 0;
			$data_report['total_taxable_purchases_in_period_before_tax'] = 0;
			$data_report['tax_reclaimable_on_purchases'] = 0;
			$data_report['other_adjustments'] = 0;
			$data_report['tax_due_or_credit_from_previous_periods'] = 0;
			$data_report['tax_payments_made_this_period'] = 0;
			$data_report['adjustments_to_reclaimable_tax_on_purchases'] = 0;
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '") and tax = '.$tax.' and rel_type = "invoice" and debit > 0');
			
			if($accounting_method == 'cash'){
				$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
			}
			$this->db->order_by('date', 'asc');
			
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			
			$list_invoice = [];        
			$this->load->model('invoices_model');
			foreach ($account_history as $v) {
				
				if(!in_array($v['rel_id'], $list_invoice)){
					$list_invoice[] = $v['rel_id'];
					$invoice = $this->invoices_model->get($v['rel_id']);
					
					$data_report['total_taxable_sales_in_period_before_tax'] += $invoice->subtotal;
				}
				
				$data_report['tax_collected_on_sales'] += $v['debit'];
			}
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '") and tax = '.$tax.' and rel_type = "expense" and credit > 0');
			if($accounting_method == 'cash'){
				$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
			}
			$this->db->order_by('date', 'asc');
			
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			
			$this->load->model('expenses_model');
			$list_expense = [];        
			foreach ($account_history as $v) {
				
				if(!in_array($v['rel_id'], $list_expense)){
					$list_expense[] = $v['rel_id'];
					$expense = $this->expenses_model->get($v['rel_id']);
					
					$data_report['total_taxable_purchases_in_period_before_tax'] += $expense->amount;
				}
				
				$data_report['tax_reclaimable_on_purchases'] += $v['credit'];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data tax liability report
			* @return array 
		*/
		public function get_data_tax_liability_report($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'cash';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$accounts = $this->accounting_model->get_accounts();
			
			$account_name = [];
			
			foreach ($accounts as $key => $value) {
				$account_name[$value['id']] = $value['name'];
			}
			
			$data_report = [];
			
			$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '") and tax > 0 and (rel_type = "invoice" or rel_type = "expense") and debit > 0');
			if($accounting_method == 'cash'){
				$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
			}
			$this->db->order_by('tax, rel_type', 'asc');
			$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
			
			$list_invoice = [];        
			foreach ($account_history as $v) {
				if(isset($data_report[$v['tax'].'_'.$v['rel_type']])){
					$data_report[$v['tax'].'_'.$v['rel_type']]['amount'] += $v['debit'];
					}else{
					$this->db->where('id', $v['tax']);
					$_tax = $this->db->get(db_prefix().'taxes')->row();
					
					$data_report[$v['tax'].'_'.$v['rel_type']] = [];
					$data_report[$v['tax'].'_'.$v['rel_type']]['name'] = $_tax->name.' ('._l($v['rel_type']).')('.$_tax->taxrate.'%)';
					$data_report[$v['tax'].'_'.$v['rel_type']]['amount'] = $v['debit'];
				}
				
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get journal entry next number
			* @return integer
		*/
		public function get_journal_entry_next_number()
		{
			$this->db->select('max(number) as max_number');
			$max = $this->db->get(db_prefix().'acc_journal_entries')->row();
			if(is_numeric($max->max_number)){
				return $max->max_number + 1;
			}
			return 1;
		}
		
		/**
			* add payment mode mapping
			* @param array $data
			* @return boolean
		*/
		public function add_payment_mode_mapping($data){
			if(isset($data['id'])){
				unset($data['id']);
			}
			$payment_modes = [];
			if(isset($data['payment_mode'])){
				$payment_modes = $data['payment_mode'];
				unset($data['payment_mode']);
			}
			$data_insert = [];
			foreach ($payment_modes as $value) {
				$this->db->where('payment_mode_id', $value);
				$count = $this->db->count_all_results(db_prefix() . 'acc_payment_mode_mappings');
				
				if($count == 0){
					$node = [];
					$node['payment_mode_id'] = $value;
					$node['payment_account'] = $data['payment_account'];
					$node['deposit_to'] = $data['deposit_to'];
					$node['expense_payment_account'] = $data['expense_payment_account'];
					$node['expense_deposit_to'] = $data['expense_deposit_to'];
					
					$data_insert[] = $node;
				}
				
			}
			
			$affectedRows = $this->db->insert_batch(db_prefix().'acc_payment_mode_mappings',  $data_insert);
			
			if ($affectedRows > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
			* update payment mode mapping
			* @param array $data
			* @param  integer $id 
			* @return boolean
		*/
		public function update_payment_mode_mapping($data, $id){
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_payment_mode_mappings', $data);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* delete payment mode mapping
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_payment_mode_mapping($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_payment_mode_mappings');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* get payment mode are not yet auto
			* @return array
		*/
		public function get_payment_mode_not_yet_auto(){
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_payment_mode_mappings where ' . db_prefix() . 'acc_payment_mode_mappings.payment_mode_id = ' . db_prefix() . 'payment_modes.id) = 0)');
			return $this->db->get(db_prefix().'payment_modes')->result_array();
		}
		
		/**
			* Gets the payment mode mappings
			*
			* @param      string  $payment_mode_id  The payment mode id
			*
			* @return     object  The expense category mapping.
		*/
		public function get_payment_mode_mapping($payment_mode_id) {
			
			$this->db->where('payment_mode_id', $payment_mode_id);
			return $this->db->get(db_prefix() . 'acc_payment_mode_mappings')->row();
		}
		
		/**
			* Change payment mode mapping active
			* @param  mixed $status status(0/1)
		*/
		public function change_active_payment_mode_mapping($status)
		{
			$this->db->where('name', 'acc_active_payment_mode_mapping');
			$this->db->update(db_prefix() . 'options', [
			'value' => $status,
			]);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* Change expense category mapping active
			* @param  mixed $status status(0/1)
		*/
		public function change_active_expense_category_mapping($status)
		{
			$this->db->where('name', 'acc_active_expense_category_mapping');
			$this->db->update(db_prefix() . 'options', [
			'value' => $status,
			]);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* get account data tables
			* @param  array $aColumns           table columns
			* @param  mixed $sIndexColumn       main column in table for bettter performing
			* @param  string $sTable            table name
			* @param  array  $join              join other tables
			* @param  array  $where             perform where in query
			* @param  array  $additionalSelect  select additional fields
			* @param  string $sGroupBy group results
			* @return array
		*/
		function get_account_data_tables($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
		{
			$CI          = & get_instance();
			$__post      = $CI->input->post();
			$where = implode(' ', $where);
			$where = trim($where);
			if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
				if (startsWith($where, 'OR')) {
					$where = substr($where, 2);
					} else {
					$where = substr($where, 3);
				}
				
				$this->db->where($where);
			}
			
			if(!$this->input->post('ft_account')){
				$this->db->where('(parent_account is null or parent_account = 0)');
			}
			
			$accounting_method = get_option('acc_accounting_method');
			
			if($accounting_method == 'cash'){
				$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as debit';
				$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as credit';
				}else{
				$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as debit';
				$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as credit';
			}
			
			
			$this->db->select('id, number, name, parent_account, account_type_id, account_detail_type_id, balance, key_name, active, number, description, balance_as_of, '.$debit.', '.$credit.', default_account');
			$this->db->limit(intval($CI->input->post('length')), intval($CI->input->post('start')));
			$this->db->order_by('id', 'desc');
			
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			
			$rResult = [];
			
			foreach ($accounts as $key => $value) {
				$rResult[] = $value;
				$rResult = $this->get_recursive_account($rResult, $value['id'], $where, 1);
			}
			
			/* Data set length after filtering */
			$sQuery = '
			SELECT FOUND_ROWS()
			';
			$_query         = $CI->db->query($sQuery)->result_array();
			$iFilteredTotal = $_query[0]['FOUND_ROWS()'];
			
			/* Total data set length */
			$sQuery = '
			SELECT COUNT(' . $sTable . '.' . $sIndexColumn . ")
			FROM $sTable " . ($where != '' ? 'WHERE '.$where : $where);
			$_query = $CI->db->query($sQuery)->result_array();
			
			$iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
			/*
				* Output
			*/
			$output = [
			'draw'                 => $__post['draw'] ? intval($__post['draw']) : 0,
			'iTotalRecords'        => $iTotal,
			'iTotalDisplayRecords' => $iTotal,
			'aaData'               => [],
			];
			
			return [
			'rResult' => $rResult,
			'output'  => $output,
			];
		}
		
		/**
			* get account data tables
			* @param  array $aColumns           table columns
			* @param  mixed $sIndexColumn       main column in table for bettter performing
			* @param  string $sTable            table name
			* @param  array  $join              join other tables
			* @param  array  $where             perform where in query
			* @param  array  $additionalSelect  select additional fields
			* @param  string $sGroupBy group results
			* @return array
		*/
		function get_data_tables_for_chart_of_account($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
		{
			$CI          = & get_instance();
			$__post      = $CI->input->post();
			$where = implode(' ', $where);
			$where = trim($where);
			if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
				if (startsWith($where, 'OR')) {
					$where = substr($where, 2);
					} else {
					$where = substr($where, 3);
				}
				
				$this->db->where($where);
			}
			
			/*if(!$this->input->post('ft_account')){
				$this->db->where('(parent_account is null or parent_account = 0)');
			}*/
			
			$accounting_method = get_option('acc_accounting_method');
			
			/*if($accounting_method == 'cash'){
				$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as debit';
				$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id) AND (('.db_prefix().'acc_account_history.rel_type = "invoice" AND '.db_prefix().'acc_account_history.paid = 1) or rel_type != "invoice")) as credit';
				}else{
				$debit = '(SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as debit';
				$credit = '(SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as credit';
			}*/
			
			$fy = $this->session->userdata('finacial_year');
			$this->db->select(db_prefix() . 'clients.AccountID, company, ActGroupID, SubActGroupID, active, MaxCrdAmt, ManagerID, DistributorType,'. db_prefix() . 'accountbalances.BAL1,'. db_prefix() . 'accountbalances.BAL2,'
			. db_prefix() . 'accountbalances.BAL3,'. db_prefix() . 'accountbalances.BAL4,'. db_prefix() . 'accountbalances.BAL5,'. db_prefix() . 'accountbalances.BAL6,'. db_prefix() . 'accountbalances.BAL7,'. db_prefix() . 'accountbalances.BAL8,'
			. db_prefix() . 'accountbalances.BAL9,'. db_prefix() . 'accountbalances.BAL10,'. db_prefix() . 'accountbalances.BAL11,'. db_prefix() . 'accountbalances.BAL12,'. db_prefix() . 'accountbalances.BAL13');
			//$this->db->limit(intval($CI->input->post('length')), intval($CI->input->post('start')));
			$this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND '. db_prefix() .'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND  ' . db_prefix() . 'accountbalances.FY = "'.$fy.'"', 'left');
			$this->db->order_by(db_prefix() . 'clients.AccountID', 'asc');
			
			$accounts = $this->db->get(db_prefix().'clients')->result_array();
			
			$rResult = [];
			
			foreach ($accounts as $key => $value) {
				$rResult[] = $value;
				//$rResult = $this->get_recursive_account($rResult, $value['id'], $where, 1);
			}
			
			/* Data set length after filtering */
			$sQuery = '
			SELECT FOUND_ROWS()
			';
			$_query         = $CI->db->query($sQuery)->result_array();
			$iFilteredTotal = $_query[0]['FOUND_ROWS()'];
			
			/* Total data set length */
			$sQuery = '
			SELECT COUNT(' . $sTable . '.' . $sIndexColumn . ")
			FROM $sTable " . ($where != '' ? 'WHERE '.$where : $where);
			$_query = $CI->db->query($sQuery)->result_array();
			
			$iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
			/*
				* Output
			*/
			$output = [
			'draw'                 => $__post['draw'] ? intval($__post['draw']) : 0,
			'iTotalRecords'        => $iTotal,
			'iTotalDisplayRecords' => $iTotal,
			'aaData'               => [],
			];
			
			return [
			'rResult' => $rResult,
			'output'  => $output,
			];
		}
		
		/**
			* get recursive account
			* @param  array $accounts  
			* @param  integer $account_id
			* @param  string $where     
			* @param  integer $number    
			* @return array            
		*/
		public function get_recursive_account($accounts, $account_id, $where, $number){
			$this->db->select('id, number, name, parent_account, account_type_id, account_detail_type_id, balance, key_name, active, number, description, balance_as_of, (SELECT sum(debit) as debit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as debit, (SELECT sum(credit) as credit FROM '.db_prefix().'acc_account_history where (account = '.db_prefix().'acc_accounts.id or parent_account = '.db_prefix().'acc_accounts.id)) as credit, default_account');
			if($where != ''){
				$this->db->where($where);
			}
			
			$this->db->where('parent_account', $account_id);
			$this->db->order_by('number,name', 'asc');
			$account_list = $this->db->get(db_prefix().'acc_accounts')->result_array();
			
			if($account_list){
				foreach ($account_list as $key => $value) {
					foreach ($accounts as $k => $val) {
						if($value['id'] == $val['id']){
							unset($accounts[$k]);
						}
					}
					
					$value['level'] = $number;
					array_push($accounts, $value);
					$accounts = $this->get_recursive_account($accounts, $value['id'], $where, $number + 1);
				}
			}
			
			return $accounts;
		}
		
		/**
			* get data balance sheet comparison recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $last_from_date  
			* @param  string $last_to_date    
			* @param  string $accounting_method    
			* @return array                 
		*/
		public function get_data_balance_sheet_comparison_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			foreach ($accounts as $val) {
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date_format(datecreated, \'%Y-%m-%d\') >= "' . $last_from_date . '" and date_format(datecreated, \'%Y-%m-%d\') <= "' . $last_to_date . '")');
				$py_account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$py_credits = $py_account_history->credit != '' ? $py_account_history->credit : 0;
				$py_debits = $py_account_history->debit != '' ? $py_account_history->debit : 0;
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 8 || $account_type_id == 9 || $account_type_id == 10 || $account_type_id == 7 || $account_type_id == 6){
					$child_account[] = ['name' => $name, 'amount' => ($credits - $debits), 'py_amount' => ($py_credits - $py_debits), 'child_account' => $this->get_data_balance_sheet_comparison_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers)];
					}else{
					$child_account[] = ['name' => $name, 'amount' => ($debits - $credits), 'py_amount' => ($py_debits - $py_credits), 'child_account' => $this->get_data_balance_sheet_comparison_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers)];
				}
			}
			
			return $child_account; 
		}
		
		/**
			* get html balance sheet comparision
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_balance_sheet_comparision($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$total_py_amount = 0;
			$data_return['total_amount'] = 0;
			$data_return['total_py_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$total_py_amount = $val['py_amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['py_amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$t_py = $data_return['total_py_amount'];
					$data_return = $this->get_html_balance_sheet_comparision($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					$total_py_amount += $data_return['total_py_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_py_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
					$data_return['total_py_amount'] += $t_py;
				}
				
				$data_return['total_amount'] += $val['amount'];
				$data_return['total_py_amount'] += $val['py_amount'];
			}
			return $data_return; 
		}
		
		/**
			* get data balance sheet detail recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $accounting_method         
			* @return array                 
		*/
		public function get_data_balance_sheet_detail_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			foreach ($accounts as $val) {
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
				$node = [];
				$balance = 0;
				$amount = 0;
				foreach ($account_history as $v) {
					if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 10 || $account_type_id == 8 || $account_type_id == 9 || $account_type_id == 7){
						$am = $v['credit'] - $v['debit'];
						}else{
						$am = $v['debit'] - $v['credit'];
					}
					
					$node[] =   [
					'date' => date('Y-m-d', strtotime($v['date'])),
					'type' => _l($v['rel_type']),
					'description' => $v['description'],
					'debit' => $v['debit'],
					'credit' => $v['credit'],
					'amount' => $am,
					'balance' => $balance + $am,
					];
					
					$amount += $am;
					$balance += $am;
				}
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				$child_account[] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' => $this->get_data_balance_sheet_detail_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
			}
			
			return $child_account; 
		}
		
		/**
			* get html balance sheet detail
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_balance_sheet_detail($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $value) {
				$amount = 0;
				$data_return['row_index']++;
				$_parent_index = $data_return['row_index'];
				if(count($value['details']) > 0 || count($value['child_account']) > 0){
					$data_return['html'] .= '<tr class="treegrid-'.$_parent_index.' treegrid-parent-'.$parent_index.' parent-node expanded">
					<td class="parent">'.$value['name'].'</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>';
				}
				
				foreach ($value['details'] as $val) { 
					$data_return['row_index']++;
					$amount += $val['amount'];
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' treegrid-parent-'.$_parent_index.'">
					<td>
					'. _d($val['date']).'
					</td>
					<td>
					'. html_entity_decode($val['type']).' 
					</td>
					<td>
					'. html_entity_decode($val['description']).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['debit'], $currency->name).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['credit'], $currency->name).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['amount'], $currency->name).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['balance'], $currency->name).' 
					</td>
					</tr>';
				}
				$total_amount = $amount;
				$data_return['row_index']++;
				
				if(count($value['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_balance_sheet_detail($value['child_account'], $data_return, $_parent_index, $currency);
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '
					<tr class="treegrid-'.$data_return['row_index'].' treegrid-parent-'.$parent_index.' tr_total">
					<td>
					'._l('total_for', $value['name']).'
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td></td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $amount;
			}
			return $data_return; 
		}
		
		/**
			* get data balance sheet summary recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $accounting_method         
			* @return array                 
		*/
		public function get_data_balance_sheet_summary_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			foreach ($accounts as $val) {
				$this->db->where('account', $val['id']);
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$node = [];
				$balance = 0;
				$amount = 0;
				
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 8 || $account_type_id == 9 || $account_type_id == 10 || $account_type_id == 7){
					$child_account[] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $this->get_data_balance_sheet_summary_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
					
					}else{
					$child_account[] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $this->get_data_balance_sheet_summary_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
				}
				
			}
			
			return $child_account;
		}
		
		/**
			* get html balance sheet summary
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_balance_sheet_summary($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_balance_sheet_summary($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $val['amount'];
			}
			return $data_return; 
		}
		
		/**
			* get data balance sheet summary recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $accounting_method         
			* @return array                 
		*/
		public function get_data_balance_sheet_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			foreach ($accounts as $val) {
				$this->db->where('account', $val['id']);
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$node = [];
				$balance = 0;
				$amount = 0;
				
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 8 || $account_type_id == 9 || $account_type_id == 10 || $account_type_id == 7 || $account_type_id == 6){
					$child_account[] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $this->get_data_balance_sheet_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
					
					}else{
					$child_account[] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $this->get_data_balance_sheet_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
				}
				
			}
			
			return $child_account;
		}
		
		/**
			* get html balance sheet
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_balance_sheet($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_balance_sheet($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $val['amount'];
			}
			return $data_return; 
		}
		
		/**
			* get data custom summary recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $accounting_method 
			* @return array                 
		*/
		public function get_data_custom_summary_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			
			foreach ($accounts as $val) {
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12){
					$child_account[] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $this->get_data_custom_summary_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
					}else{
					$child_account[] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $this->get_data_custom_summary_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
				}
			}
			
			return $child_account;
		}
		
		/**
			* get html custom summary
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_custom_summary($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_custom_summary($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $val['amount'];
			}
			return $data_return; 
		}
		
		/**
			* get data profit and loss as of total income recursive
			* @param  array $child_account         
			* @param  integer $income      
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @return array                 
		*/
		public function get_data_profit_and_loss_as_of_total_income_recursive($child_account, $income, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12){
					$r_am = $credits - $debits;
					}else{
					$r_am = $debits - $credits;
				}
				
				if($income != 0){
					$child_account[] = ['name' => $name, 'amount' => $r_am, 'percent' => round((($r_am) / $income) * 100, 2), 'child_account' => $this->get_data_profit_and_loss_as_of_total_income_recursive([], $income, $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
					}else{
					$child_account[] = ['name' => $name, 'amount' => $r_am, 'percent' => 0, 'child_account' => $this->get_data_profit_and_loss_as_of_total_income_recursive([], $income, $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
				}
			}
			
			return $child_account;
		}
		
		/**
			* get html profit and loss as of total income
			* @param  array $child_account 
			* @param  integer $income 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss_as_of_total_income($child_account, $income, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			$data_return['percent'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'. html_entity_decode($val['percent']).'% 
				</td>
				</tr>';
				
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$p = $data_return['percent'];
					$data_return = $this->get_html_profit_and_loss_as_of_total_income($val['child_account'], $income, $data_return, $data_return['row_index'], $currency);
					$total_amount += $data_return['total_amount'];
					
					if($income != 0){
						$percent = round((($total_amount) / $income) * 100, 2);
						}else{
						$percent = 0;
					}
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'. html_entity_decode($percent).'% 
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
					$data_return['percent'] += $p;
				}
				
				$data_return['total_amount'] += $val['amount'];
				$data_return['percent'] += $val['percent'];
			}
			return $data_return; 
		}
		
		/**
			* get data profit and loss comparison recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date 
			* @param  string $last_from_date       
			* @param  string $last_to_date         
			* @param  string $accounting_method         
			* @return array                 
		*/
		public function get_data_profit_and_loss_comparison_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			
			foreach ($accounts as $val) {
				
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date_format(datecreated, \'%Y-%m-%d\') >= "' . $last_from_date . '" and date_format(datecreated, \'%Y-%m-%d\') <= "' . $last_to_date . '")');
				$py_account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$py_credits = $py_account_history->credit != '' ? $py_account_history->credit : 0;
				$py_debits = $py_account_history->debit != '' ? $py_account_history->debit : 0;
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12){
					$child_account[] = ['name' => $name, 'this_year' => $credits - $debits, 'last_year' => $py_credits - $py_debits, 'child_account' => $this->get_data_profit_and_loss_comparison_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers)];
					}else{
					$child_account[] = ['name' => $name, 'this_year' => $debits - $credits, 'last_year' => $py_debits - $py_credits, 'child_account' => $this->get_data_profit_and_loss_comparison_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers)];
				}
			}
			
			return $child_account;
		}
		
		/**
			* get html profit and loss comparison
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss_comparison($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$total_py_amount = 0;
			$data_return['total_amount'] = 0;
			$data_return['total_py_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['this_year'];
				$total_py_amount = $val['last_year'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['this_year'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['last_year'], $currency->name).'
				</td>
				</tr>';
				
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$p = $data_return['total_py_amount'];
					$data_return = $this->get_html_profit_and_loss_comparison($val['child_account'], $data_return, $data_return['row_index'], $currency);
					$total_amount += $data_return['total_amount'];
					$total_py_amount += $data_return['total_py_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_py_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
					$data_return['total_py_amount'] += $p;
				}
				
				$data_return['total_amount'] += $val['this_year'];
				$data_return['total_py_amount'] += $val['last_year'];
			}
			return $data_return; 
		}
		
		/**
			* get data profit and loss detail recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $accounting_method         
			* @return array                 
		*/
		public function get_data_profit_and_loss_detail_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
				$node = [];
				$balance = 0;
				$amount = 0;
				foreach ($account_history as $v) {
					if($account_type_id == 11 || $account_type_id == 12){
						$am = $v['credit'] - $v['debit'];
						}else{
						$am = $v['debit'] - $v['credit'];
					}
					$node[] =   [
					'date' => date('Y-m-d', strtotime($v['date'])),
					'type' => _l($v['rel_type']),
					'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
					'description' => $v['description'],
					'customer' => $v['customer'],
					'amount' => $am,
					'balance' => $balance + $am,
					];
					$amount += $am;
					$balance += $am;
				}
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				$child_account[] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' =>  $this->get_data_profit_and_loss_detail_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
			}
			
			return $child_account;
		}
		
		/**
			* get html profit and loss detail
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss_detail($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $value) {
				$amount = 0;
				$data_return['row_index']++;
				$_parent_index = $data_return['row_index'];
				if(count($value['details']) > 0 || count($value['child_account']) > 0){
					$data_return['html'] .= '<tr class="treegrid-'.$_parent_index.' treegrid-parent-'.$parent_index.' parent-node expanded">
					<td class="parent">'.$value['name'].'</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>';
				}
				
				foreach ($value['details'] as $val) { 
					$data_return['row_index']++;
					$amount += $val['amount'];
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' treegrid-parent-'.$_parent_index.'">
					<td>
					'. _d($val['date']).'
					</td>
					<td>
					'. html_entity_decode($val['type']).' 
					</td>
					<td>
					'. html_entity_decode($val['description']).' 
					</td>
					<td>
					'. html_entity_decode($val['split']).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['amount'], $currency->name).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['balance'], $currency->name).' 
					</td>
					</tr>';
				}
				$total_amount = $amount;
				$data_return['row_index']++;
				
				if(count($value['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_profit_and_loss_detail($value['child_account'], $data_return, $_parent_index, $currency);
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '
					<tr class="treegrid-'.$data_return['row_index'].' treegrid-parent-'.$parent_index.' tr_total">
					<td>
					'._l('total_for', $value['name']).'
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td></td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $amount;
			}
			return $data_return; 
		}
		
		/**
			* get data profit and loss year to date comparison recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $last_from_date       
			* @param  string $last_to_date         
			* @param  string $accounting_method         
			* @return array                 
		*/
		public function get_data_profit_and_loss_year_to_date_comparison_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date_format(datecreated, \'%Y-%m-%d\') >= "' . $last_from_date . '" and date_format(datecreated, \'%Y-%m-%d\') <= "' . $last_to_date . '")');
				$py_account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$py_credits = $py_account_history->credit != '' ? $py_account_history->credit : 0;
				$py_debits = $py_account_history->debit != '' ? $py_account_history->debit : 0;
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				if($account_type_id == 11 || $account_type_id == 12){
					$child_account[] = ['name' => $name, 'this_year' => $credits - $debits, 'last_year' => $py_credits - $py_debits, 'child_account' => $this->get_data_profit_and_loss_year_to_date_comparison_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers)];
					}else{
					$child_account[] = ['name' => $name, 'this_year' => $debits - $credits, 'last_year' => $py_debits - $py_credits, 'child_account' => $this->get_data_profit_and_loss_year_to_date_comparison_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $last_from_date, $last_to_date, $accounting_method, $acc_show_account_numbers)];
				}
			}
			
			return $child_account;
		}
		
		/**
			* get html profit and loss year to date comparison
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss_year_to_date_comparison($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$total_py_amount = 0;
			$data_return['total_amount'] = 0;
			$data_return['total_py_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['this_year'];
				$total_py_amount = $val['last_year'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['this_year'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['last_year'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$p = $data_return['total_py_amount'];
					$data_return = $this->get_html_profit_and_loss_year_to_date_comparison($val['child_account'], $data_return, $data_return['row_index'], $currency);
					$total_amount += $data_return['total_amount'];
					$total_py_amount += $data_return['total_py_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_py_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
					$data_return['total_py_amount'] += $p;
				}
				
				$data_return['total_amount'] += $val['this_year'];
				$data_return['total_py_amount'] += $val['last_year'];
			}
			return $data_return; 
		}
		
		/**
			* get data profit and loss recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $accounting_method   
			* @return array                 
		*/
		public function get_data_profit_and_loss_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12){
					$child_account[] = ['name' => $name, 'amount' => $credits - $debits, 'child_account' => $this->get_data_profit_and_loss_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
					}else{
					$child_account[] = ['name' => $name, 'amount' => $debits - $credits, 'child_account' => $this->get_data_profit_and_loss_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
				}
			}
			
			return $child_account;
		}
		
		/**
			* get html profit and loss
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_custom_summary($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $val['amount'];
			}
			return $data_return; 
		}
		
		/**
			* get data statement of cash flows recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  integer $account_detail_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @return array                 
		*/
		public function get_data_statement_of_cash_flows_recursive($child_account, $account_id, $account_type_id, $account_detail_type_id, $from_date, $to_date, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				if($val['id'] == 13){
					$this->db->where('(rel_type != "invoice" and rel_type != "expense" and rel_type != "payment")');
				}
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$this->db->where('account', $val['id']);
				
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 10 || $account_type_id == 8 || $account_type_id == 7 || $account_type_id == 4 || $account_type_id == 5 || $account_type_id == 6 || $account_type_id == 2 || $account_type_id == 9 || $account_type_id == 1){
					$child_account[] = ['account_detail_type_id' => $account_detail_type_id, 'name' => $name, 'amount' => $credits - $debits, 'child_account' => $this->get_data_statement_of_cash_flows_recursive([], $val['id'], $account_type_id, $account_detail_type_id, $from_date, $to_date, $acc_show_account_numbers)];
					}else{
					$child_account[] = ['account_detail_type_id' => $account_detail_type_id, 'name' => $name, 'amount' => $debits - $credits, 'child_account' => $this->get_data_statement_of_cash_flows_recursive([], $val['id'], $account_type_id, $account_detail_type_id, $from_date, $to_date, $acc_show_account_numbers)];
				}
			}
			
			return $child_account;
		}
		
		/**
			* get html statement of cash flows
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_statement_of_cash_flows($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_statement_of_cash_flows($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $val['amount'];
			}
			return $data_return; 
		}
		
		/**
			* get data statement of changes in equity recursive recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  integer $account_detail_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $accounting_method   
			* @return array                 
		*/
		public function get_data_statement_of_changes_in_equity_recursive($child_account, $account_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				$child_account[] = ['account_detail_type_id' => $value['id'], 'name' => $name, 'amount' => $credits - $debits, 'child_account' => $this->get_data_statement_of_changes_in_equity_recursive([], $val['id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
			}
			
			return $child_account;
		}
		
		/**
			* get html statement of changes in equity
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_statement_of_changes_in_equity($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_statement_of_changes_in_equity($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $val['amount'];
			}
			return $data_return; 
		}
		
		/**
			* get data account list recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  array $account_type_name 
			* @param  array $detail_type_name 
			* @return array                 
		*/
		public function get_data_account_list_recursive($child_account, $account_id, $account_type_id, $account_type_name, $detail_type_name, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('account', $val['id']);
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				$_account_type_name = isset($account_type_name[$val['account_type_id']]) ? $account_type_name[$val['account_type_id']] : '';
				$_detail_type_name = isset($detail_type_name[$val['account_detail_type_id']]) ? $detail_type_name[$val['account_detail_type_id']] : '';
				
				$child_account[] = ['description' => $val['description'], 'type' => $_account_type_name, 'detail_type' => $_detail_type_name, 'name' => $name, 'amount' => $debits - $credits, 'child_account' => $this->get_data_account_list_recursive([], $val['id'], $account_type_id, $account_type_name, $detail_type_name, $acc_show_account_numbers)];
			}
			
			return $child_account;
		}
		
		/**
			* get html account list
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_account_list($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				
				$name = '';
				
				$name .= $val['name'];
				
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$name.'
				</td>
				<td>
				'.$val['type'].'
				</td>
				<td>
				'.$val['detail_type'].'
				</td>
				<td>
				'.$val['description'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_account_list($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					$data_return['row_index']++;
					
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $val['amount'];
			}
			return $data_return; 
		}
		
		
		/**
			* get data general ledger recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $accounting_method   
			* @return array                 
		*/
		public function get_data_general_ledger_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
				$node = [];
				$balance = 0;
				$amount = 0;
				foreach ($account_history as $v) {
					if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 10 || $account_type_id == 9 || $account_type_id == 8 || $account_type_id == 7 || $account_type_id == 6){
						$am = $v['credit'] - $v['debit'];
						}else{
						$am = $v['debit'] - $v['credit'];
					}
					
					$node[] =   [
					'date' => date('Y-m-d', strtotime($v['date'])),
					'type' => _l($v['rel_type']),
					'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
					'description' => $v['description'],
					'customer' => $v['customer'],
					'debit' => $v['debit'],
					'credit' => $v['credit'],
					'amount' => $am,
					'balance' => $balance + $am,
					];
					
					$amount += $am;
					$balance += $am;
				}
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				$child_account[] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' => $this->get_data_general_ledger_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
			}
			
			return $child_account;
		}
		
		/**
			* get html general ledger
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_general_ledger($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $value) {
				$amount = 0;
				$data_return['row_index']++;
				$_parent_index = $data_return['row_index'];
				if(count($value['details']) > 0 || count($value['child_account']) > 0){
					$data_return['html'] .= '<tr class="treegrid-'.$_parent_index.' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' parent-node expanded">
					<td class="parent">'.$value['name'].'</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>';
				}
				
				foreach ($value['details'] as $val) { 
					$data_return['row_index']++;
					$amount += $val['amount'];
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' treegrid-parent-'.$_parent_index.'">
					<td>
					'. _d($val['date']).'
					</td>
					<td>
					'. html_entity_decode($val['type']).' 
					</td>
					<td>
					'. get_company_name($val['customer']).' 
					</td>
					<td>
					'. html_entity_decode($val['description']).' 
					</td>
					<td>
					'. html_entity_decode($val['split']).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['amount'], $currency->name).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['balance'], $currency->name).' 
					</td>
					</tr>';
				}
				$total_amount = $amount;
				$data_return['row_index']++;
				$t = 0;
				if(count($value['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_general_ledger($value['child_account'], $data_return, $_parent_index, $currency);
					$total_amount += $data_return['total_amount'];
				}
				
				if(count($value['details']) > 0 || count($value['child_account']) > 0){
					$data_return['row_index']++;
					$data_return['html'] .= '
					<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>'._l('total_for', $value['name']).'</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td></td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $amount;
			}
			return $data_return; 
		}
		
		/**
			* get data trial balance recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $accounting_method   
			* @return array                 
		*/
		public function get_data_trial_balance_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($credits > $debits){
					$credits = $credits - $debits;
					$debits = 0;
					}else{
					$debits = $debits - $credits;
					$credits = 0;
				}
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				$child_account[] = ['name' => $name, 'debit' => $debits, 'credit' => $credits, 'child_account' => $this->get_data_trial_balance_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
			}
			
			return $child_account;
		}
		
		/**
			* get html trial balance
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_trial_balance($child_account, $data_return, $parent_index, $currency){
			$total_debit = 0;
			$total_credit = 0;
			$data_return['total_debit'] = 0;
			$data_return['total_credit'] = 0;
			foreach ($child_account as $val) {
				$data_return['row_index']++;
				$total_debit = $val['debit'];
				$total_credit = $val['credit'];
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['debit'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['credit'], $currency->name).'
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$d = $data_return['total_debit'];
					$c = $data_return['total_credit'];
					$data_return = $this->get_html_trial_balance($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_debit += $data_return['total_debit'];
					$total_credit += $data_return['total_credit'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_debit, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_credit, $currency->name).'
					</td>
					</tr>';
					$data_return['total_debit'] += $d;
					$data_return['total_credit'] += $c;
				}
				
				$data_return['total_debit'] += $val['debit'];
				$data_return['total_credit'] += $val['credit'];
			}
			return $data_return; 
		}
		
		/**
			* get data transaction detail by account recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $accounting_method   
			* @return array                 
		*/
		public function get_data_transaction_detail_by_account_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
				$node = [];
				$balance = 0;
				$amount = 0;
				foreach ($account_history as $v) {
					if($account_type_id == 11 || $account_type_id == 12 || $account_type_id == 10 || $account_type_id == 9 || $account_type_id == 8 || $account_type_id == 7 || $account_type_id == 6){
						$am = $v['credit'] - $v['debit'];
						}else{
						$am = $v['debit'] - $v['credit'];
					}
					$node[] =   [
					'date' => date('Y-m-d', strtotime($v['date'])),
					'type' => _l($v['rel_type']),
					'description' => $v['description'],
					'customer' => $v['customer'],
					'split' => $v['split'] != 0 ? (isset($account_name[$v['split']]) ? $account_name[$v['split']] : '') : '-Split-',
					'debit' => $v['debit'],
					'credit' => $v['credit'],
					'amount' => $am,
					'balance' => $balance + ($am),
					];
					$amount += $am;
					$balance += $am;
				}
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				$child_account[] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'balance' => $balance, 'details' => $node, 'child_account' => $this->get_data_transaction_detail_by_account_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
			}
			
			return $child_account;
		}
		
		/**
			* get html transaction detail by account
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_transaction_detail_by_account($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $value) {
				$amount = 0;
				$data_return['row_index']++;
				$_parent_index = $data_return['row_index'];
				if(count($value['details']) > 0 || count($value['child_account']) > 0){
					$data_return['html'] .= '<tr class="treegrid-'.$_parent_index.' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' parent-node expanded">
					<td class="parent">'.$value['name'].'</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>';
				}
				
				foreach ($value['details'] as $val) { 
					$data_return['row_index']++;
					$amount += $val['amount'];
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' treegrid-parent-'.$_parent_index.'">
					<td>
					'. _d($val['date']).'
					</td>
					<td>
					'. html_entity_decode($val['type']).' 
					</td>
					<td>
					'. get_company_name($val['customer']).' 
					</td>
					<td>
					'. html_entity_decode($val['description']).' 
					</td>
					<td>
					'. html_entity_decode($val['split']).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['amount'], $currency->name).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['balance'], $currency->name).' 
					</td>
					</tr>';
				}
				$total_amount = $amount;
				$data_return['row_index']++;
				
				if(count($value['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_transaction_detail_by_account($value['child_account'], $data_return, $_parent_index, $currency);
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '
					<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $value['name']).'
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td></td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $amount;
			}
			return $data_return; 
		}
		
		/**
			* get data deposit detail recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @return array                 
		*/
		public function get_data_deposit_detail_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$this->db->where('account', $val['id']);
				$this->db->where('((rel_type = "payment" and debit > 0) or (rel_type = "deposit"  and credit > 0))');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->result_array();
				$node = [];
				$balance = 0;
				$amount = 0;
				foreach ($account_history as $v) {
					if($account_type_id == 10 || $account_type_id == 9 || $account_type_id == 8 || $account_type_id == 7){
						$amount += $v['credit'] - $v['debit'];
						$am = ($v['credit'] - $v['debit']);
						}else{
						$amount += $v['debit'] - $v['credit'];
						$am = ($v['debit'] - $v['credit']);
					}
					
					$node[] =   [
					'date' => date('Y-m-d', strtotime($v['date'])),
					'type' => _l($v['rel_type']),
					'description' => $v['description'],
					'customer' => $v['customer'],
					'debit' => $v['debit'],
					'credit' => $v['credit'],
					'amount' =>  $am,
					];
				}
				
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				$child_account[] = ['account' => $val['id'], 'name' => $name, 'amount' => $amount, 'details' => $node, 'child_account' => $this->get_data_deposit_detail_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $acc_show_account_numbers)];
				
			}
			
			return $child_account;
		}
		
		/**
			* get html transaction detail by account
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_deposit_detail($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $value) {
				$amount = 0;
				$data_return['row_index']++;
				$_parent_index = $data_return['row_index'];
				if(count($value['details']) > 0 || count($value['child_account']) > 0){
					$data_return['html'] .= '<tr class="treegrid-'.$_parent_index.' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' parent-node expanded">
					<td class="parent">'.$value['name'].'</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>';
				}
				
				foreach ($value['details'] as $val) { 
					$data_return['row_index']++;
					$amount += $val['amount'];
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' treegrid-parent-'.$_parent_index.'">
					<td>
					'. _d($val['date']).'
					</td>
					<td>
					'. html_entity_decode($val['type']).' 
					</td>
					<td>
					'. get_company_name($val['customer']).' 
					</td>
					<td>
					'. html_entity_decode($val['description']).' 
					</td>
					<td class="total_amount">
					'. app_format_money($val['amount'], $currency->name).' 
					</td>
					</tr>';
				}
				$total_amount = $amount;
				$data_return['row_index']++;
				
				if(count($value['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_deposit_detail($value['child_account'], $data_return, $_parent_index, $currency);
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '
					<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $value['name']).'
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $amount;
			}
			return $data_return; 
		}
		
		
		/**
			* add new account type detail
			* @param array $data
			* @return integer
		*/
		public function add_account_type_detail($data)
		{
			if (isset($data['id'])) {
				unset($data['id']);
			}
			
			$this->db->insert(db_prefix() . 'acc_account_type_details', $data);
			
			$insert_id = $this->db->insert_id();
			
			if ($insert_id) {
				return true;
			}
			
			return false;
		}
		
		/**
			* update account type detail
			* @param array $data
			* @param integer $id
			* @return integer
		*/
		public function update_account_type_detail($data, $id)
		{
			if (isset($data['id'])) {
				unset($data['id']);
			}
			
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'acc_account_type_details', $data);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			
			return false;
		}
		
		/**
			* delete account type detail
			* @param integer $id
			* @return boolean
		*/
		
		public function delete_account_type_detail($id)
		{
			$this->db->where('account_detail_type_id',$id);
			$count = $this->db->count_all_results(db_prefix() . 'acc_accounts');
			
			if($count > 0){
				return 'have_account';
			}
			
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'acc_account_type_details');
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* get account type details
			* @param  integer $id    member group id
			* @param  array  $where
			* @return object
		*/
		public function get_data_account_type_details($id = '', $where = [])
		{
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				return $this->db->get(db_prefix() . 'acc_account_type_details')->row();
			}
			
			$this->db->where($where);
			$this->db->order_by('account_type_id', 'desc');
			$account_type_details = $this->db->get(db_prefix() . 'acc_account_type_details')->result_array();
			
			$account_types = $this->accounting_model->get_account_types();
			
			$account_type_name = [];
			
			foreach ($account_types as $key => $value) {
				$account_type_name[$value['id']] = $value['name'];
			}
			
			foreach ($account_type_details as $key => $value) {
				$_account_type_name = isset($account_type_name[$value['account_type_id']]) ? $account_type_name[$value['account_type_id']] : '';
				$account_type_details[$key]['account_type_name'] = $_account_type_name;
			}
			
			return $account_type_details;
		}
		
		/**
			* Change preferred payment method status / on / off
			* @param  mixed $id     staff id
			* @param  mixed $status status(0/1)
		*/
		public function change_preferred_payment_method($id, $status)
		{
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'acc_expense_category_mappings', [
			'preferred_payment_method' => $status,
			]);
		}
		public function get_for_pay_rec($filterdata)
		{ 
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$voucher_type = $filterdata["voucher_type"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$regExp ="'.*;s:[0-9]+:'".$selected_company."'.*'";
			$regExp1 ="'.*;s:[0-9]+:";
			$regExp2 =".*'";
			
			$sql = 'SELECT tblaccountledger.*,tblclients.company,tblclients.address,tblstaff.firstname,tblstaff.lastname  FROM `tblaccountledger` 
			LEFT JOIN tblclients ON tblclients.AccountID=tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
			LEFT JOIN tblstaff ON tblstaff.AccountID=tblaccountledger.AccountID  AND tblstaff.PlantID = '.$selected_company.'
			WHERE tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$fy.'" AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tblaccountledger.PassedFrom = "'.$voucher_type.'" AND tblaccountledger.TType = "D"';
			$sql .= ' ORDER BY tblaccountledger.VoucherID,tblaccountledger.AccountID ASC';
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function get_voucher_data($filterdata)
		{ 
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$voucher_type = $filterdata["voucher_type"];
			$selected_company = $this->session->userdata('root_company');
			$regExp ="'.*;s:[0-9]+:'".$selected_company."'.*'";
			$regExp1 ="'.*;s:[0-9]+:";
			$regExp2 =".*'";
			$fy = $this->session->userdata('finacial_year');
			
			if($voucher_type == "CONTRA" || $voucher_type == "JOURNAL"){
				$sql = 'SELECT tblaccountledger.*,tblclients.company,tblclients.address,tblstaff.firstname,tblstaff.lastname  FROM `tblaccountledger` 
				LEFT JOIN tblclients ON tblclients.AccountID=tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
				LEFT JOIN tblstaff ON tblstaff.AccountID=tblaccountledger.AccountID AND tblstaff.PlantID = '.$selected_company.'
				WHERE tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$fy.'" AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tblaccountledger.PassedFrom = "'.$voucher_type.'"';
				$sql .= ' ORDER BY ABS(tblaccountledger.VoucherID),tblaccountledger.OrdinalNo ASC';
				
				//$sql .= ' GROUP BY tblhistory.ItemID,tblhistory.TType,tblhistory.TType2';
				}else if($voucher_type == "PAYMENTS" || $voucher_type == "RECEIPTS"){
				$sql = 'SELECT tblaccountledger.*,tblclients.company,tblclients.address,tblstaff.firstname,tblstaff.lastname  FROM `tblaccountledger` 
				LEFT JOIN tblclients ON tblclients.AccountID=tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
				LEFT JOIN tblstaff ON tblstaff.AccountID=tblaccountledger.AccountID AND tblstaff.PlantID = '.$selected_company.' 
				WHERE tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$fy.'" AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" AND tblaccountledger.PassedFrom = "'.$voucher_type.'" AND tblaccountledger.TType = "C"';
				$sql .= ' ORDER BY ABS(tblaccountledger.VoucherID) ,tblaccountledger.OrdinalNo ASC';
				}else if($voucher_type == "PURCHASE"){
				$sql = 'SELECT tblpurchasemaster.*,tblclients.company,tblclients.address  FROM `tblpurchasemaster` 
				INNER JOIN tblclients ON tblclients.AccountID=tblpurchasemaster.AccountID AND tblclients.PlantID = tblpurchasemaster.PlantID
				WHERE tblpurchasemaster.PlantID = '.$selected_company.' AND tblpurchasemaster.FY = "'.$fy.'" AND tblpurchasemaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
				$sql .= ' ORDER BY tblpurchasemaster.PurchID ASC';
				}else if($voucher_type == "SALE"){
				$sql = 'SELECT tblsalesmaster.*,tblclients.company,tblclients.address  FROM `tblsalesmaster` 
				INNER JOIN tblclients ON tblclients.AccountID=tblsalesmaster.AccountID AND tblclients.PlantID = tblsalesmaster.PlantID
				WHERE tblsalesmaster.PlantID = '.$selected_company.' AND tblsalesmaster.FY = "'.$fy.'" AND tblsalesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
				$sql .= ' ORDER BY tblsalesmaster.Transdate ASC';
			}
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		/**
			* count stock import not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object          
		*/
		public function count_stock_import_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_receipt.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_import") = 0) '.$where_currency);
			return $this->db->count_all_results(db_prefix().'goods_receipt');
		}
		
		/**
			* count stock export not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object          
		*/
		public function count_stock_export_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'goods_delivery.id and ' . db_prefix() . 'acc_account_history.rel_type = "stock_export") = 0) '.$where_currency);
			return $this->db->count_all_results(db_prefix().'goods_delivery');
		}
		
		/**
			* count loss adjustment not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object          
		*/
		public function count_loss_adjustment_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'wh_loss_adjustment.id and ' . db_prefix() . 'acc_account_history.rel_type = "loss_adjustment") = 0) '.$where_currency);
			return $this->db->count_all_results(db_prefix().'wh_loss_adjustment');
		}
		
		/**
			* count opening stock not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object          
		*/
		public function count_opening_stock_not_convert_yet($currency = '', $where = ''){
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'items.id and ' . db_prefix() . 'acc_account_history.rel_type = "opening_stock" and date = "'.$date_financial_year.'") = 0)');
			return $this->db->count_all_results(db_prefix().'items');
		}
		
		/**
			* update payslip automatic conversion
			*
			* @param      array   $data   The data
			*
			* @return     boolean 
		*/
		public function update_payslip_automatic_conversion($data){
			$affectedRows = 0;
			
			if(!isset($data['acc_pl_total_insurance_automatic_conversion'])){
				$data['acc_pl_total_insurance_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_pl_tax_paye_automatic_conversion'])){
				$data['acc_pl_tax_paye_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_pl_net_pay_automatic_conversion'])){
				$data['acc_pl_net_pay_automatic_conversion'] = 0;
			}
			
			foreach ($data as $key => $value) {
				$this->db->where('name', $key);
				$this->db->update(db_prefix() . 'options', [
				'value' => $value,
				]);
				if ($this->db->affected_rows() > 0) {
					$affectedRows++;
				}
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* get opening stock data tables
			* @param  array $aColumns           table columns
			* @param  mixed $sIndexColumn       main column in table for bettter performing
			* @param  string $sTable            table name
			* @param  array  $join              join other tables
			* @param  array  $where             perform where in query
			* @param  array  $additionalSelect  select additional fields
			* @param  string $sGroupBy group results
			* @return array
		*/
		function get_opening_stock_data_tables($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
		{
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			
			$CI          = & get_instance();
			$__post      = $CI->input->post();
			$where = implode(' ', $where);
			$where = trim($where);
			if (startsWith($where, 'AND') || startsWith($where, 'OR')) {
				if (startsWith($where, 'OR')) {
					$where = substr($where, 2);
					} else {
					$where = substr($where, 3);
				}
				
				$this->db->where($where);
			}
			
			$this->db->select('*, (select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'items.id and ' . db_prefix() . 'acc_account_history.rel_type = "opening_stock" and ' . db_prefix() . 'acc_account_history.date = "'.$date_financial_year.'") as count_account_historys');
			$this->db->limit(intval($CI->input->post('length')), intval($CI->input->post('start')));
			$this->db->order_by('id', 'desc');
			
			$items = $this->db->get(db_prefix().'items')->result_array();
			
			$rResult = [];
			
			foreach ($items as $key => $value) {
				$value['opening_stock'] = $this->calculate_opening_stock($value['id'], $date_financial_year);
				$rResult[] = $value;
			}
			
			/* Data set length after filtering */
			$sQuery = '
			SELECT FOUND_ROWS()
			';
			$_query         = $CI->db->query($sQuery)->result_array();
			$iFilteredTotal = $_query[0]['FOUND_ROWS()'];
			
			/* Total data set length */
			$sQuery = '
			SELECT COUNT(' . $sTable . '.' . $sIndexColumn . ")
			FROM $sTable " . ($where != '' ? 'WHERE '.$where : $where);
			$_query = $CI->db->query($sQuery)->result_array();
			
			$iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
			/*
				* Output
			*/
			$output = [
			'draw'                 => $__post['draw'] ? intval($__post['draw']) : 0,
			'iTotalRecords'        => $iTotal,
			'iTotalDisplayRecords' => $iTotal,
			'aaData'               => [],
			];
			
			return [
			'rResult' => $rResult,
			'output'  => $output,
			];
		}
		
		/**
			* calculate opening stock
			* @param  integer $item_id             
			* @param  date $date_financial_year 
			* @return float                     
		*/
		public function calculate_opening_stock($item_id, $date_financial_year){
			
			
			$this->db->where('(' . db_prefix() . 'goods_receipt.date_c >= "' . $date_financial_year.'" and ' . db_prefix() . 'goods_receipt_detail.commodity_code = ' . $item_id.')');
			$this->db->join(db_prefix() . 'goods_receipt', db_prefix() . 'goods_receipt.id=' . db_prefix() . 'goods_receipt_detail.goods_receipt_id', 'left');
			$goods_receipt_detail = $this->db->get(db_prefix().'goods_receipt_detail')->result_array();
			
			$this->db->where('(' . db_prefix() . 'goods_delivery.date_c >= "' . $date_financial_year.'" and ' . db_prefix() . 'goods_delivery_detail.commodity_code = ' . $item_id.')');
			$this->db->join(db_prefix() . 'goods_delivery', db_prefix() . 'goods_delivery.id=' . db_prefix() . 'goods_delivery_detail.goods_delivery_id', 'left');
			$goods_delivery_detail = $this->db->get(db_prefix().'goods_delivery_detail')->result_array();
			
			$this->db->where('(date_format(' . db_prefix() . 'wh_loss_adjustment.time, \'%Y-%m-%d\') >= "' . $date_financial_year.'" and ' . db_prefix() . 'wh_loss_adjustment_detail.items = ' . $item_id.')');
			$this->db->join(db_prefix() . 'wh_loss_adjustment', db_prefix() . 'wh_loss_adjustment.id=' . db_prefix() . 'wh_loss_adjustment_detail.loss_adjustment', 'left');
			$wh_loss_adjustment_detail = $this->db->get(db_prefix().'wh_loss_adjustment_detail')->result_array();
			
			$this->db->where('commodity_id', $item_id);
			$inventory_manage = $this->db->get(db_prefix().'inventory_manage')->result_array();
			
			$amount = 0;
			
			foreach($goods_receipt_detail as $value){
				$amount -= ($value['quantities'] * $value['unit_price']);
			}
			
			foreach($goods_delivery_detail as $value){
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
				
				$amount += ($value['quantities'] * $price);
			}
			
			foreach($wh_loss_adjustment_detail as $value){
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
				
				if($value['current_number'] > $value['updates_number']){
					$amount -= ($value['current_number'] - $value['updates_number']) * $price;
					}else{
					$amount += ($value['updates_number'] - $value['current_number']) * $price;
				}
			}
			foreach($inventory_manage as $value){
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
				
				$amount += $value['inventory_number'] * $price;
			}
			
			return $amount;
		}
		
		/**
			* get opening stock data
			* @param  integer $item_id 
			* @return object         
		*/
		public function get_opening_stock_data($item_id){
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			
			$this->db->where('id' ,$item_id);
			$item = $this->db->get(db_prefix().'items')->row();
			
			$item->opening_stock = $this->calculate_opening_stock($item_id, $date_financial_year);
			
			return $item;
		}
		
		/**
			* update warehouse automatic conversion
			*
			* @param      array   $data   The data
			*
			* @return     boolean 
		*/
		public function update_warehouse_automatic_conversion($data){
			$affectedRows = 0;
			
			if(!isset($data['acc_wh_stock_import_automatic_conversion'])){
				$data['acc_wh_stock_import_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_wh_stock_export_automatic_conversion'])){
				$data['acc_wh_stock_export_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_wh_loss_adjustment_automatic_conversion'])){
				$data['acc_wh_loss_adjustment_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_wh_opening_stock_automatic_conversion'])){
				$data['acc_wh_opening_stock_automatic_conversion'] = 0;
			}
			
			foreach ($data as $key => $value) {
				$this->db->where('name', $key);
				$this->db->update(db_prefix() . 'options', [
				'value' => $value,
				]);
				if ($this->db->affected_rows() > 0) {
					$affectedRows++;
				}
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* Automatic payslip conversion
			* @param  integer $payslip_id 
			* @return boolean
		*/
		public function automatic_payslip_conversion($payslips_id){
			$this->db->where('rel_id', $payslips_id);
			$this->db->where('rel_type', 'payslip');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			
			if($count > 0){
				return false;
			}
			
			$this->db->where('id', $payslips_id);
			$payslip = $this->db->get(db_prefix(). 'hrp_payslips')->row();
			
			$this->db->where('payslip_id', $payslips_id);
			$payslip_details = $this->db->get(db_prefix(). 'hrp_payslip_details')->result_array();
			
			$insurance_payment_account = get_option('acc_pl_total_insurance_payment_account');
			$insurance_deposit_to = get_option('acc_pl_total_insurance_deposit_to');
			
			$tax_paye_payment_account = get_option('acc_pl_tax_paye_payment_account');
			$tax_paye_deposit_to = get_option('acc_pl_tax_paye_deposit_to');
			
			$net_pay_payment_account = get_option('acc_pl_net_pay_payment_account');
			$net_pay_deposit_to = get_option('acc_pl_net_pay_deposit_to');
			
			$affectedRows = 0;
			
			if($payslip){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($payslip->payslip_month) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$this->load->model('currencies_model');
				$currency = $this->currencies_model->get_base_currency();
				
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
				
				$data_insert = [];
				
				if(get_option('acc_pl_total_insurance_automatic_conversion') == 1){
					$node = [];
					$node['split'] = $insurance_payment_account;
					$node['account'] = $insurance_deposit_to;
					$node['date'] = $payslip->payslip_month;
					$node['debit'] = $total_insurance;
					$node['credit'] = 0;
					$node['description'] = '';
					$node['rel_id'] = $payslips_id;
					$node['rel_type'] = 'payslip';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
					
					$node = [];
					$node['split'] = $insurance_deposit_to;
					$node['account'] = $insurance_payment_account;
					$node['date'] = $payslip->payslip_month;
					$node['debit'] = 0;
					$node['credit'] = $total_insurance;
					$node['description'] = '';
					$node['rel_id'] = $payslips_id;
					$node['rel_type'] = 'payslip';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
				}
				
				if(get_option('acc_pl_tax_paye_automatic_conversion') == 1){
					$node = [];
					$node['split'] = $tax_paye_payment_account;
					$node['account'] = $tax_paye_deposit_to;
					$node['date'] = $payslip->payslip_month;
					$node['debit'] = $income_tax_paye;
					$node['credit'] = 0;
					$node['description'] = '';
					$node['rel_id'] = $payslips_id;
					$node['rel_type'] = 'payslip';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
					
					$node = [];
					$node['split'] = $tax_paye_deposit_to;
					$node['account'] = $tax_paye_payment_account;
					$node['date'] = $payslip->payslip_month;
					$node['debit'] = 0;
					$node['credit'] = $income_tax_paye;
					$node['description'] = '';
					$node['rel_id'] = $payslips_id;
					$node['rel_type'] = 'payslip';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
				}
				
				if(get_option('acc_pl_net_pay_automatic_conversion') == 1){
					$node = [];
					$node['split'] = $net_pay_payment_account;
					$node['account'] = $net_pay_deposit_to;
					$node['date'] = $payslip->payslip_month;
					$node['debit'] = $net_pay;
					$node['credit'] = 0;
					$node['description'] = '';
					$node['rel_id'] = $payslips_id;
					$node['rel_type'] = 'payslip';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
					
					$node = [];
					$node['split'] = $net_pay_deposit_to;
					$node['account'] = $net_pay_payment_account;
					$node['date'] = $payslip->payslip_month;
					$node['debit'] = 0;
					$node['credit'] = $net_pay;
					$node['description'] = '';
					$node['rel_id'] = $payslips_id;
					$node['rel_type'] = 'payslip';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* Automatic purchase order conversion
			* @param  integer $purchase_order_id 
			* @return boolean
		*/
		public function automatic_purchase_order_conversion($purchase_order_id){
			$this->db->where('rel_id', $purchase_order_id);
			$this->db->where('rel_type', 'purchase_orde');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			$affectedRows = 0;
			
			if($count > 0){
				return false;
			}
			
			$this->load->model('purchase/purchase_model');
			$purchase_order = $this->purchase_model->get_pur_order($purchase_order_id);
			$purchase_order_detail = $this->purchase_model->get_pur_order_detail($purchase_order_id);
			
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$payment_account = get_option('acc_pur_order_payment_account');
			$deposit_to = get_option('acc_pur_order_deposit_to');
			$tax_payment_account = get_option('acc_expense_tax_payment_account');
			$tax_deposit_to = get_option('acc_expense_tax_deposit_to');
			
			if($purchase_order){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($purchase_order->order_date) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$data_insert = [];
				
				foreach ($invoice->items as $value) {
					
				}
				
				foreach ($purchase_order_detail as $value) {
					
					$item = get_item_hp($value['item_code']);
					
					$item_id = 0;
					if(isset($item->id)){
						$item_id = $item->id;
					}
					
					$item_total = $value['into_money'];
					
					$item_automatic = $this->get_item_automatic($item_id);
					
					if($item_automatic){
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $item_automatic->expence_account;
						$node['item'] = $item_id;
						$node['date'] = $purchase_order->order_date;
						$node['debit'] = $item_total;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $purchase_order_id;
						$node['rel_type'] = 'purchase_order';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $item_automatic->expence_account;
						$node['account'] = $payment_account;
						$node['item'] = $item_id;
						$node['date'] = $purchase_order->order_date;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $purchase_order_id;
						$node['rel_type'] = 'purchase_order';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['date'] = $purchase_order->order_date;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $purchase_order_id;
						$node['rel_type'] = 'purchase_order';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['account'] = $payment_account;
						$node['item'] = $item_id;
						$node['date'] = $purchase_order->order_date;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $purchase_order_id;
						$node['rel_type'] = 'purchase_order';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
					
					if(get_option('acc_tax_automatic_conversion') == 1 && $value['tax'] > 0){
						$tax_payment_account = get_option('acc_expense_tax_payment_account');
						$tax_deposit_to = get_option('acc_expense_tax_deposit_to');
						
						$total_tax = $value['total'] - $value['into_money'];
						
						$tax_mapping = $this->get_tax_mapping($value['tax']);
						
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->payment_account;
							$node['account'] = $tax_mapping->deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $purchase_order->order_date;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $purchase_order_id;
							$node['rel_type'] = 'purchase_order';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->deposit_to;
							$node['account'] = $tax_mapping->payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $purchase_order->order_date;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $purchase_order_id;
							$node['rel_type'] = 'purchase_order';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $purchase_order->order_date;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $purchase_order_id;
							$node['rel_type'] = 'purchase_order';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['date'] = $purchase_order->order_date;
							$node['account'] = $tax_payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $purchase_order_id;
							$node['rel_type'] = 'purchase_order';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* Automatic stock import conversion
			* @param  integer $stock_import_id 
			* @return boolean
		*/
		public function automatic_stock_import_conversion($stock_import_id){
			$this->db->where('rel_id', $stock_import_id);
			$this->db->where('rel_type', 'stock_import');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			$affectedRows = 0;
			
			if($count > 0 || get_option('acc_wh_stock_import_automatic_conversion') == 0){
				return false;
			}
			
			$this->load->model('warehouse/warehouse_model');
			$goods_receipt = $this->warehouse_model->get_goods_receipt($stock_import_id);
			$goods_receipt_detail = $this->warehouse_model->get_goods_receipt_detail($stock_import_id);
			
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$payment_account = get_option('acc_wh_stock_import_payment_account');
			$deposit_to = get_option('acc_wh_stock_import_deposit_to');
			
			$tax_payment_account = get_option('acc_expense_tax_payment_account');
			$tax_deposit_to = get_option('acc_expense_tax_deposit_to');
			
			if($goods_receipt){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($goods_receipt->date_c) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$data_insert = [];
				
				foreach ($invoice->items as $value) {
					
				}
				
				foreach ($goods_receipt_detail as $value) {
					
					$this->db->where('id', $value['commodity_code']);
					$item = $this->db->get(db_prefix().'items')->row();
					
					$item_id = 0;
					if(isset($item->id)){
						$item_id = $item->id;
					}
					
					$item_total = $value['goods_money'];
					
					$item_automatic = $this->get_item_automatic($item_id);
					
					if($item_automatic){
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $item_automatic->inventory_asset_account;
						$node['item'] = $item_id;
						$node['date'] = $goods_receipt->date_c;
						$node['debit'] = $item_total;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $stock_import_id;
						$node['rel_type'] = 'stock_import';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $item_automatic->inventory_asset_account;
						$node['account'] = $payment_account;
						$node['item'] = $item_id;
						$node['date'] = $goods_receipt->date_c;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $stock_import_id;
						$node['rel_type'] = 'stock_import';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['date'] = $goods_receipt->date_c;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $stock_import_id;
						$node['rel_type'] = 'stock_import';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['account'] = $payment_account;
						$node['item'] = $item_id;
						$node['date'] = $goods_receipt->date_c;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $stock_import_id;
						$node['rel_type'] = 'stock_import';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
					
					if(get_option('acc_tax_automatic_conversion') == 1 && $value['tax'] > 0){
						$tax_payment_account = get_option('acc_expense_tax_payment_account');
						$tax_deposit_to = get_option('acc_expense_tax_deposit_to');
						
						$total_tax = $value['tax_money'];
						
						$tax_mapping = $this->get_tax_mapping($value['tax']);
						
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->payment_account;
							$node['account'] = $tax_mapping->deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $goods_receipt->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $stock_import_id;
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->deposit_to;
							$node['account'] = $tax_mapping->payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $goods_receipt->date_c;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $stock_import_id;
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['date'] = $goods_receipt->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $stock_import_id;
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['date'] = $goods_receipt->date_c;
							$node['account'] = $tax_payment_account;
							$node['tax'] = $value['tax'];
							$node['item'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $stock_import_id;
							$node['rel_type'] = 'stock_import';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* Automatic stock export conversion
			* @param  integer $stock_export_id 
			* @return boolean
		*/
		public function automatic_stock_export_conversion($stock_export_id){
			$this->db->where('rel_id', $stock_export_id);
			$this->db->where('rel_type', 'stock_export');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			$affectedRows = 0;
			
			if($count > 0 || get_option('acc_wh_stock_export_automatic_conversion') == 0){
				return false;
			}
			
			$this->load->model('warehouse/warehouse_model');
			$goods_delivery = $this->warehouse_model->get_goods_delivery($stock_export_id);
			$goods_delivery_detail = $this->warehouse_model->get_goods_delivery_detail($stock_export_id);
			
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			$payment_account = get_option('acc_wh_stock_export_payment_account');
			$deposit_to = get_option('acc_wh_stock_export_deposit_to');
			
			$tax_payment_account = get_option('acc_tax_payment_account');
			$tax_deposit_to = get_option('acc_tax_deposit_to');
			
			if($goods_delivery){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($goods_delivery->date_c) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$data_insert = [];
				
				foreach ($invoice->items as $value) {
					
				}
				
				foreach ($goods_delivery_detail as $value) {
					
					$this->db->where('id', $value['commodity_code']);
					$item = $this->db->get(db_prefix().'items')->row();
					
					$item_id = 0;
					if(isset($item->id)){
						$item_id = $item->id;
					}
					
					$item_total = ($value['quantities'] * $value['unit_price']);
					
					$item_automatic = $this->get_item_automatic($item_id);
					
					if($item_automatic){
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $item_automatic->inventory_asset_account;
						$node['item'] = $item_id;
						$node['date'] = $goods_delivery->date_c;
						$node['debit'] = $item_total;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $stock_export_id;
						$node['rel_type'] = 'stock_export';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $item_automatic->inventory_asset_account;
						$node['account'] = $payment_account;
						$node['item'] = $item_id;
						$node['date'] = $goods_delivery->date_c;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $stock_export_id;
						$node['rel_type'] = 'stock_export';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						}else{
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['item'] = $item_id;
						$node['debit'] = $item_total;
						$node['date'] = $goods_delivery->date_c;
						$node['tax'] = 0;
						$node['credit'] = 0;
						$node['description'] = '';
						$node['rel_id'] = $stock_export_id;
						$node['rel_type'] = 'stock_export';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['account'] = $payment_account;
						$node['item'] = $item_id;
						$node['date'] = $goods_delivery->date_c;
						$node['tax'] = 0;
						$node['debit'] = 0;
						$node['credit'] = $item_total;
						$node['description'] = '';
						$node['rel_id'] = $stock_export_id;
						$node['rel_type'] = 'stock_export';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
					
					if(get_option('acc_tax_automatic_conversion') == 1 && $value['tax'] > 0){
						$tax_payment_account = get_option('acc_tax_payment_account');
						$tax_deposit_to = get_option('acc_tax_deposit_to');
						
						$total_tax = $value['total_money'] - $item_total;
						
						$tax_mapping = $this->get_tax_mapping($value['tax']);
						
						if($tax_mapping){
							$node = [];
							$node['split'] = $tax_mapping->payment_account;
							$node['account'] = $tax_mapping->deposit_to;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['date'] = $goods_delivery->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $stock_export_id;
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_mapping->deposit_to;
							$node['account'] = $tax_mapping->payment_account;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['date'] = $goods_delivery->date_c;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $stock_export_id;
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							}else{
							$node = [];
							$node['split'] = $tax_payment_account;
							$node['account'] = $tax_deposit_to;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['date'] = $goods_delivery->date_c;
							$node['debit'] = $total_tax;
							$node['credit'] = 0;
							$node['description'] = '';
							$node['rel_id'] = $stock_export_id;
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
							
							$node = [];
							$node['split'] = $tax_deposit_to;
							$node['date'] = $goods_delivery->date_c;
							$node['account'] = $tax_payment_account;
							$node['tax'] = $value['tax_id'];
							$node['item'] = 0;
							$node['debit'] = 0;
							$node['credit'] = $total_tax;
							$node['description'] = '';
							$node['rel_id'] = $stock_export_id;
							$node['rel_type'] = 'stock_export';
							$node['datecreated'] = date('Y-m-d H:i:s');
							$node['addedfrom'] = get_staff_user_id();
							$data_insert[] = $node;
						}
					}
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* Automatic loss adjustment conversion
			* @param  integer $loss_adjustment_id 
			* @return boolean
		*/
		public function automatic_loss_adjustment_conversion($loss_adjustment_id){
			$this->db->where('rel_id', $loss_adjustment_id);
			$this->db->where('rel_type', 'loss_adjustment');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			$affectedRows = 0;
			
			if($count > 0 || get_option('acc_wh_loss_adjustment_automatic_conversion') == 0){
				return false;
			}
			
			$this->load->model('warehouse/warehouse_model');
			$loss_adjustment = $this->warehouse_model->get_loss_adjustment($loss_adjustment_id);
			$loss_adjustment_detail = $this->warehouse_model->get_loss_adjustment_detailt_by_masterid($loss_adjustment_id);
			
			$decrease_payment_account = get_option('acc_wh_decrease_payment_account');
			$decrease_deposit_to = get_option('acc_wh_decrease_deposit_to');
			$increase_payment_account = get_option('acc_wh_increase_payment_account');
			$increase_deposit_to = get_option('acc_wh_increase_deposit_to');
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			if($loss_adjustment){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime(date('Y-m-d', strtotime($loss_adjustment->time))) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$data_insert = [];
				
				foreach ($loss_adjustment_detail as $value) {
					
					
					$this->db->where('id', $value['items']);
					$item = $this->db->get(db_prefix().'items')->row();
					
					$item_id = 0;
					if(isset($item->id)){
						$item_id = $item->id;
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
					
					
					$item_automatic = $this->get_item_automatic($item_id);
					
					if($item_automatic){
						if($value['current_number'] < $value['updates_number']){
							$number = $value['updates_number'] - $value['current_number'];
							$loss_adjustment_payment_account = $increase_payment_account;
							$loss_adjustment_deposit_to = $item_automatic->inventory_asset_account;
							}else{
							$number = $value['current_number'] - $value['updates_number'];
							$loss_adjustment_payment_account = $item_automatic->inventory_asset_account;
							$loss_adjustment_deposit_to = $increase_deposit_to;
						}
						}else{
						if($value['current_number'] < $value['updates_number']){
							$number = $value['updates_number'] - $value['current_number'];
							$loss_adjustment_payment_account = $increase_payment_account;
							$loss_adjustment_deposit_to = $increase_deposit_to;
							}else{
							$number = $value['current_number'] - $value['updates_number'];
							$loss_adjustment_payment_account = $decrease_payment_account;
							$loss_adjustment_deposit_to = $decrease_deposit_to;
						}
					}
					
					$item_total = $number * $price;
					
					$node = [];
					$node['split'] = $loss_adjustment_payment_account;
					$node['account'] = $loss_adjustment_deposit_to;
					$node['item'] = $item_id;
					$node['debit'] = $item_total;
					$node['date'] = date('Y-m-d', strtotime($loss_adjustment->time));
					$node['tax'] = 0;
					$node['credit'] = 0;
					$node['description'] = '';
					$node['rel_id'] = $loss_adjustment_id;
					$node['rel_type'] = 'loss_adjustment';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
					
					$node = [];
					$node['split'] = $loss_adjustment_deposit_to;
					$node['account'] = $loss_adjustment_payment_account;
					$node['item'] = $item_id;
					$node['date'] = date('Y-m-d', strtotime($loss_adjustment->time));
					$node['tax'] = 0;
					$node['debit'] = 0;
					$node['credit'] = $item_total;
					$node['description'] = '';
					$node['rel_id'] = $loss_adjustment_id;
					$node['rel_type'] = 'loss_adjustment';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* Automatic opening stock conversion
			* @param  integer $loss_adjustment_id 
			* @return boolean
		*/
		public function automatic_opening_stock_conversion($opening_stock_id){
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			
			$this->db->where('rel_id', $opening_stock_id);
			$this->db->where('rel_type', 'opening_stock');
			$this->db->where('date', $date_financial_year);
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			$affectedRows = 0;
			
			if($count > 0 || get_option('acc_wh_opening_stock_automatic_conversion') == 0){
				return false;
			}
			
			$opening_stock = $this->get_opening_stock_data($opening_stock_id);
			
			$deposit_to = get_option('acc_wh_opening_stock_deposit_to');
			$payment_account = get_option('acc_wh_opening_stock_payment_account');
			
			
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			
			if($opening_stock){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($date_financial_year) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$data_insert = [];
				
				$node = [];
				$node['split'] = $payment_account;
				$node['account'] = $deposit_to;
				$node['debit'] = $opening_stock->opening_stock;
				$node['date'] = $date_financial_year;
				$node['credit'] = 0;
				$node['tax'] = 0;
				$node['description'] = '';
				$node['rel_id'] = $opening_stock_id;
				$node['rel_type'] = 'opening_stock';
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$data_insert[] = $node;
				
				$node = [];
				$node['split'] = $deposit_to;
				$node['account'] = $payment_account;
				$node['date'] = $date_financial_year;
				$node['tax'] = 0;
				$node['debit'] = 0;
				$node['credit'] = $opening_stock->opening_stock;
				$node['description'] = '';
				$node['rel_id'] = $opening_stock_id;
				$node['rel_type'] = 'opening_stock';
				$node['datecreated'] = date('Y-m-d H:i:s');
				$node['addedfrom'] = get_staff_user_id();
				$data_insert[] = $node;
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
			* update purchase automatic conversion
			*
			* @param      array   $data   The data
			*
			* @return     boolean 
		*/
		public function update_purchase_automatic_conversion($data){
			$affectedRows = 0;
			
			if(!isset($data['acc_pur_order_automatic_conversion'])){
				$data['acc_pur_order_automatic_conversion'] = 0;
			}
			
			if(!isset($data['acc_pur_payment_automatic_conversion'])){
				$data['acc_pur_payment_automatic_conversion'] = 0;
			}
			
			foreach ($data as $key => $value) {
				$this->db->where('name', $key);
				$this->db->update(db_prefix() . 'options', [
				'value' => $value,
				]);
				if ($this->db->affected_rows() > 0) {
					$affectedRows++;
				}
			}
			
			if ($affectedRows > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* count purchase order not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object          
		*/
		public function count_purchase_order_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_orders.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_order") = 0) '.$where_currency);
			return $this->db->count_all_results(db_prefix().'pur_orders');
		}
		
		/**
			* count purchase payment not convert yet
			* @param  integer $currency
			* @param  string $where
			* @return object          
		*/
		public function count_purchase_payment_not_convert_yet($currency = '', $where = ''){
			$where_currency = '';
			if($currency != ''){
				$where_currency = 'and currency = '.$currency;
			}
			
			if($where != ''){
				$this->db->where($where);
			}
			$this->db->where('((select count(*) from ' . db_prefix() . 'acc_account_history where ' . db_prefix() . 'acc_account_history.rel_id = ' . db_prefix() . 'pur_invoice_payment.id and ' . db_prefix() . 'acc_account_history.rel_type = "purchase_payment") = 0) AND (' . db_prefix() . 'pur_invoices.pur_order is not null) '.$where_currency);
			$this->db->join(db_prefix().'pur_invoices', db_prefix() . 'pur_invoices.id = ' . db_prefix() . 'pur_invoice_payment.pur_invoice', 'left');
			return $this->db->count_all_results(db_prefix().'pur_invoice_payment');
		}
		
		/**
			* Automatic payment conversion
			* @param  integer $payment_id 
			* @return boolean
		*/
		public function automatic_purchase_payment_conversion($payment_id){
			$this->db->where('rel_id', $payment_id);
			$this->db->where('rel_type', 'purchase_payment');
			$count = $this->db->count_all_results(db_prefix() . 'acc_account_history');
			
			if($count > 0){
				return false;
			}
			
			$this->load->model('purchase/purchase_model');
			$payment = $this->purchase_model->get_payment_pur_invoice($payment_id);
			
			$payment_account = get_option('acc_pur_payment_payment_account');
			$deposit_to = get_option('acc_pur_payment_deposit_to');
			$affectedRows = 0;
			$data_insert = [];
			
			if($payment){
				if(get_option('acc_close_the_books') == 1){
					if(strtotime($payment->date) <= strtotime(get_option('acc_closing_date')) && strtotime(date('Y-m-d')) > strtotime(get_option('acc_closing_date'))){
						return false;
					}
				}
				
				$payment_total = $payment->amount;
				
				$payment_mode_mapping = $this->get_payment_mode_mapping($payment->paymentmode);
				
				if($payment_mode_mapping && get_option('acc_active_payment_mode_mapping') == 1){
					$node = [];
					$node['split'] = $payment_mode_mapping->expense_payment_account;
					$node['account'] = $payment_mode_mapping->expense_deposit_to;
					$node['date'] = $payment->date;
					$node['debit'] = $payment_total;
					$node['credit'] = 0;
					$node['tax'] = 0;
					$node['description'] = '';
					$node['rel_id'] = $payment_id;
					$node['rel_type'] = 'purchase_payment';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
					
					$node = [];
					$node['split'] = $payment_mode_mapping->expense_deposit_to;
					$node['account'] = $payment_mode_mapping->expense_payment_account;
					$node['date'] = $payment->date;
					$node['tax'] = 0;
					$node['debit'] = 0;
					$node['credit'] = $payment_total;
					$node['description'] = '';
					$node['rel_id'] = $payment_id;
					$node['rel_type'] = 'purchase_payment';
					$node['datecreated'] = date('Y-m-d H:i:s');
					$node['addedfrom'] = get_staff_user_id();
					$data_insert[] = $node;
					}else{
					if(get_option('acc_pur_payment_automatic_conversion') == 1){
						$node = [];
						$node['split'] = $payment_account;
						$node['account'] = $deposit_to;
						$node['debit'] = $payment_total;
						$node['credit'] = 0;
						$node['date'] = $payment->date;
						$node['description'] = '';
						$node['rel_id'] = $payment_id;
						$node['rel_type'] = 'purchase_payment';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
						
						$node = [];
						$node['split'] = $deposit_to;
						$node['account'] = $payment_account;
						$node['date'] = $payment->date;
						$node['debit'] = 0;
						$node['credit'] = $payment_total;
						$node['description'] = '';
						$node['rel_id'] = $payment_id;
						$node['rel_type'] = 'purchase_payment';
						$node['datecreated'] = date('Y-m-d H:i:s');
						$node['addedfrom'] = get_staff_user_id();
						$data_insert[] = $node;
					}
				}
				
				if($data_insert != []){
					$affectedRows = $this->db->insert_batch(db_prefix().'acc_account_history', $data_insert);
				}
				
				if ($affectedRows > 0) {
					return true;
				}
			}
			
			return false;
		}
		
		public function get_budgets($id = '', $where = []){
			if (is_numeric($id)) {
				$this->db->where('id', $id);
				$budget = $this->db->get(db_prefix() . 'acc_budgets')->row();
				
				if($budget){
					$this->db->where('budget_id', $id);
					$budget->details = $this->db->get(db_prefix() . 'acc_budget_details')->result_array();
				}
				
				return $budget;
			}
			
			$this->db->where($where);
			$this->db->order_by('id', 'desc');
			return $this->db->get(db_prefix() . 'acc_budgets')->result_array();
		}
		
		/**
			* Adds a budget.
		*/
		public function add_budget($data){
			$data['name'] = $data['year'].' - '. _l($data['type']);
			
			$this->db->insert(db_prefix().'acc_budgets', $data);
			$insert_id = $this->db->insert_id();
			
			if($insert_id){
				return $insert_id;
			}
			return false;
		}
		
		/**
			* add journal entry
			* @param array $data 
			* @return boolean
		*/
		public function update_budget_detail($data){
			
			$this->db->where('budget_id', $data['budget']);
			$this->db->delete(db_prefix().'acc_budget_details');
			
			$budget_data = json_decode($data['budget_data']);
			unset($data['budget_data']);
			
			$columns = $this->get_columns_budget($data['budget'], $data['view_type'], true);
			
			
			$data_insert = [];
			foreach($budget_data as $row){
				$data_details = array_combine($columns, $row);
				$account_id = '';
				$month = '';
				foreach($data_details as $key => $value){
					if($key == 'account_id'){
						$account_id = $value;
					}
					
					if($key != 'account_name' && $key != 'account_id' && $key != 'total' && $value != null){
						if($data['view_type'] == 'monthly'){
							$month = explode('_', $key);
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => $month[0], 'year' => $month[1], 'account' => $account_id, 'amount' => $value];
							}elseif($data['view_type'] == 'quarterly'){
							$month = explode('_', $key);
							
							if($month[0] == 'q1')
							{
								$value_1 = round($value/3);
								$value_2 = round($value/3);
								$value_3 = $value - $value_2 - $value_1;
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 1, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 2, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_2];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 3, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_3];
							}
							else  if($month[0] == 'q2')
							{
								$value_1 = round($value/3);
								$value_2 = round($value/3);
								$value_3 = $value - $value_2 - $value_1;
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 4, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 5, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_2];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 6, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_3];
							}
							else  if($month[0] == 'q3')
							{
								$value_1 = round($value/3);
								$value_2 = round($value/3);
								$value_3 = $value - $value_2 - $value_1;
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 7, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 8, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_2];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 9, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_3];
							}
							else  if($month[0] == 'q4')
							{
								$value_1 = round($value/3);
								$value_2 = round($value/3);
								$value_3 = $value - $value_2 - $value_1;
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 10, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 11, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_2];
								$data_insert[] = ['budget_id' => $data['budget'], 'month' => 12, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_3];
							}
							}else{
							$month = explode('_', $key);
							
							$value_1 = round($value/12);
							$value_2 = $value - ($value_1*11);
							
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 1, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 2, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 3, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 4, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 5, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 6, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 7, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 8, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 9, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 10, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 11, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_1];
							$data_insert[] = ['budget_id' => $data['budget'], 'month' => 12, 'year' => $month[1], 'account' => $account_id, 'amount' => $value_2];
						}
					}
				}
			}
			
			if(count($data_insert) > 0){
				$affectedRows = $this->db->insert_batch(db_prefix().'acc_budget_details',  $data_insert);
			}
			
			return true;
		}
		
		/**
			* check budget.
		*/
		public function check_budget($data){
			
			$this->db->where('year', $data['year']);
			$this->db->where('type', $data['type']);
			$budget = $this->db->get(db_prefix() . 'acc_budgets')->row();
			
			if($budget){
				return $budget->id;
			}
			return true;
		}
		
		/**
			* get data budget
			* @param  array  $data_fill 
			* @param  boolean $only_data 
			* @return object             
		*/
		public function get_data_budget($data_fill, $only_data = false)
		{
			if(isset($data_fill['view_type'])){
				switch ($data_fill['view_type']) {
					case 'quarterly':
					$data = $this->get_data_budget_quarterly($data_fill, $only_data);
					break;
					case 'yearly':
					$data = $this->get_data_budget_yearly($data_fill, $only_data);
					break;
					case 'monthly':
					$data = $this->get_data_budget_monthly($data_fill, $only_data);
					break;
					default:
					$data = $this->get_data_budget_monthly($data_fill, $only_data);
					break;
				}
				}else{
				$data = $this->get_data_budget_monthly($data_fill, $only_data);
			}
			
			return $data;
		}
		
		/**
			* Gets the data budget.
			*
			* @param      object  $data_fill  The data fill
			*
			* @return     array   The data budget.
		*/
		public function get_data_budget_monthly($data_fill, $only_data = false)
		{
			if(isset($data_fill['budget']) && $data_fill['budget'] != 0){
				$budget = $this->get_budgets($data_fill['budget']);
				if($budget){
					$year = $budget->year;
					}else{
					$year = date('Y');
				}
				}elseif(isset($data_fill['year'])){
				$year = $data_fill['year'];
				}else{
				$year = date('Y');
			}
			
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.($year + 1)));
			
			$this->db->where('active', 1);
			if(isset($budget)){
				if($budget->type == 'profit_and_loss_accounts'){
					$this->db->where('find_in_set(account_type_id, "11,12,13,14,15")');
					}elseif($budget->type == 'balance_sheet_accounts'){
					$this->db->where('account_type_id not in (11,12,13,14,15)');
				}
			}
			$this->db->where('(parent_account is null or parent_account = 0)');
			
			$this->db->order_by('id', 'asc');
			
			$accounts = $this->db->get(db_prefix() . 'acc_accounts')->result_array();
			
			$data_return = [];
			$rResult = [];
			
			foreach ($accounts as $key => $value) {
				$rResult[] = $value;
				$rResult = $this->get_recursive_account($rResult, $value['id'], [], 1);
			}
			
			$data = [];
			if (isset($budget) && !isset($data_fill['clear'])) {
				foreach($budget->details as $detail){
					if($detail['month'] < 10){
						$detail['month'] = '0'.$detail['month'];
					}
					if(isset($data[$detail['account']])){
						$data[$detail['account']][$detail['month'].'_'.$detail['year']] = $detail['amount'];
						}else{
						$data[$detail['account']] = [];
						$data[$detail['account']][$detail['month'].'_'.$detail['year']] = $detail['amount'];
					}
				}
			}
			
			foreach($rResult as $account){
				$name = '';
				if($account['number'] != ''){
					$name = $account['number'].' - ';
				}
				
				if (isset($account['level'])) {
					for ($i = 0; $i < $account['level']; $i++) {
						$name .= '          ';
					}
				}
				
				if ($account['name'] == '') {
					$name .= _l($account['key_name']);
					} else {
					$name .= $account['name'];
				}
				
				if(isset($data[$account['id']])){
					$data_return[] = array_merge($data[$account['id']], ['account_name' => $name,'account_id' => $account['id']]);
					}else{
					$data_return[] = ['account_name' => $name,'account_id' => $account['id']];
				}
			}
			
			return $data_return;
		}
		
		/**
			* Gets the data budget.
			*
			* @param      object  $data_fill  The data fill
			*
			* @return     array   The data budget.
		*/
		public function get_data_budget_quarterly($data_fill, $only_data = false)
		{
			
			if(isset($data_fill['budget']) && $data_fill['budget'] != 0){
				$budget = $this->get_budgets($data_fill['budget']);
				if($budget){
					$year = $budget->year;
					}else{
					$year = date('Y');
				}
				}elseif(isset($data_fill['year'])){
				$year = $data_fill['year'];
				}else{
				$year = date('Y');
			}
			
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.($year + 1)));
			
			$this->db->where('active', 1);
			if(isset($budget)){
				if($budget->type == 'profit_and_loss_accounts'){
					$this->db->where('find_in_set(account_type_id, "11,12,13,14,15")');
					}elseif($budget->type == 'balance_sheet_accounts'){
					$this->db->where('account_type_id not in (11,12,13,14,15)');
				}
			}
			$this->db->where('(parent_account is null or parent_account = 0)');
			
			$this->db->order_by('id', 'asc');
			
			$accounts = $this->db->get(db_prefix() . 'acc_accounts')->result_array();
			
			$data_return = [];
			$rResult = [];
			
			foreach ($accounts as $key => $value) {
				$rResult[] = $value;
				$rResult = $this->get_recursive_account($rResult, $value['id'], [], 1);
			}
			
			$data = [];
			if (isset($budget) && !isset($data_fill['clear'])) {
				foreach($budget->details as $detail){
					if($detail['month'] < 10){
						$detail['month'] = '0'.$detail['month'];
					}
					
					if($detail['month']>=1 && $detail['month']<=3)
					{
						$t = 'q1_'.$detail['year'];
					}
					else  if($detail['month']>=4 && $detail['month']<=6)
					{
						$t = 'q2_'.$detail['year'];
					}
					else  if($detail['month']>=7 && $detail['month']<=9)
					{
						$t = 'q3_'.$detail['year'];
					}
					else  if($detail['month']>=10 && $detail['month']<=12)
					{
						$t = 'q4_'.$detail['year'];
					}
					
					if(isset($data[$detail['account']])){
						if(isset($data[$detail['account']][$t])){
							$data[$detail['account']][$t] += $detail['amount'];
							}else{
							$data[$detail['account']][$t] = $detail['amount'];
						}
						}else{
						$data[$detail['account']] = [];
						$data[$detail['account']][$t] = $detail['amount'];
					}
				}
			}
			
			foreach($rResult as $account){
				$name = '';
				if($account['number'] != ''){
					$name = $account['number'].' - ';
				}
				
				if (isset($account['level'])) {
					for ($i = 0; $i < $account['level']; $i++) {
						$name .= '          ';
					}
				}
				
				if ($account['name'] == '') {
					$name .= _l($account['key_name']);
					} else {
					$name .= $account['name'];
				}
				
				if(isset($data[$account['id']])){
					$data_return[] = array_merge($data[$account['id']], ['account_name' => $name,'account_id' => $account['id']]);
					}else{
					$data_return[] = ['account_name' => $name,'account_id' => $account['id']];
				}
			}
			
			return $data_return;
			
		}
		
		/**
			* Gets the data budget.
			*
			* @param      object  $data_fill  The data fill
			*
			* @return     array   The data budget.
		*/
		public function get_data_budget_yearly($data_fill, $only_data = false)
		{
			if(isset($data_fill['budget']) && $data_fill['budget'] != 0){
				$budget = $this->get_budgets($data_fill['budget']);
				if($budget){
					$year = $budget->year;
					}else{
					$year = date('Y');
				}
				}elseif(isset($data_fill['year'])){
				$year = $data_fill['year'];
				}else{
				$year = date('Y');
			}
			
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.($year + 1)));
			
			$this->db->where('active', 1);
			if(isset($budget)){
				if($budget->type == 'profit_and_loss_accounts'){
					$this->db->where('find_in_set(account_type_id, "11,12,13,14,15")');
					}elseif($budget->type == 'balance_sheet_accounts'){
					$this->db->where('account_type_id not in (11,12,13,14,15)');
				}
			}
			$this->db->where('(parent_account is null or parent_account = 0)');
			
			$this->db->order_by('id', 'asc');
			
			$accounts = $this->db->get(db_prefix() . 'acc_accounts')->result_array();
			
			$data_return = [];
			$rResult = [];
			
			foreach ($accounts as $key => $value) {
				$rResult[] = $value;
				$rResult = $this->get_recursive_account($rResult, $value['id'], [], 1);
			}
			
			$data = [];
			if (isset($budget) && !isset($data_fill['clear'])) {
				foreach($budget->details as $detail){
					
					if(isset($data[$detail['account']])){
						$data[$detail['account']]['_'.$detail['year']] += $detail['amount'];
						}else{
						$data[$detail['account']] = [];
						$data[$detail['account']]['_'.$detail['year']] = $detail['amount'];
					}
				}
			}
			
			foreach($rResult as $account){
				$name = '';
				if($account['number'] != ''){
					$name = $account['number'].' - ';
				}
				
				if (isset($account['level'])) {
					for ($i = 0; $i < $account['level']; $i++) {
						$name .= '          ';
					}
				}
				
				if ($account['name'] == '') {
					$name .= _l($account['key_name']);
					} else {
					$name .= $account['name'];
				}
				if(isset($data[$account['id']])){
					$data_return[] = array_merge(['account_name' => $name,'account_id' => $account['id']], $data[$account['id']]);
					}else{
					$data_return[] = ['account_name' => $name,'account_id' => $account['id']];
				}
			}
			
			return $data_return;
		}
		
		/**
			* Gets the nestedheaders budget.
			*
			* @param      integer  $budget_id 
			* @param      string  $budget_type    monthly or quarterly or yearly
			*
			* @return     array   The nestedheaders budget.
		*/
		public function get_nestedheaders_budget($budget_id, $budget_type)
		{
			
			$budget = $this->get_budgets($budget_id);
			
			$year = $budget->year;
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.($year + 1)));
			
			$nestedheaders    = [];
			$nestedheaders[] = _l('acc_account');
			$nestedheaders[] = _l('account_id');
			
			switch ($budget_type) {
				case 'yearly':
				$nestedheaders[] = $year;
				
				break;
				case 'quarterly':
				while (strtotime($from_date) < strtotime($to_date)) {
					$month = date('m', strtotime($from_date));
					$year = date('Y', strtotime($from_date));
					if($month>=1 && $month<=3)
					{
						$t = 'Q1 - '.$year;
					}
					else  if($month>=4 && $month<=6)
					{
						$t = 'Q2 - '.$year;
					}
					else  if($month>=7 && $month<=9)
					{
						$t = 'Q3 - '.$year;
					}
					else  if($month>=10 && $month<=12)
					{
						$t = 'Q4 - '.$year;
					}
					
					$nestedheaders[] = $t;
					
					$from_date = date('Y-m-d', strtotime('+3 month', strtotime($from_date)));
					
					if(strtotime($from_date) > strtotime($to_date)){
						$month_2 = date('m', strtotime($from_date));
						$year_2 = date('Y', strtotime($from_date));
						if($month_2>=1 && $month_2<=3)
						{
							$t_2 = 'Q1 - '.$year_2;
						}
						else  if($month_2>=4 && $month_2<=6)
						{
							$t_2 = 'Q2 - '.$year_2;
						}
						else  if($month_2>=7 && $month_2<=9)
						{
							$t_2 = 'Q3 - '.$year_2;
						}
						else  if($month_2>=10 && $month_2<=12)
						{
							$t_2 = 'Q4 - '.$year_2;
						}
						
						if($month . ' - ' . $year != $month_2 . ' - ' . $year_2){
							$nestedheaders[] = $t_2;
						}
					}
				}
				// $nestedheaders[] = _l('total');
				
				break;
				case 'monthly':
				while (strtotime($from_date) < strtotime($to_date)) {
					
					$month = date('M - Y', strtotime($from_date));
					
					$nestedheaders[] = $month;
					
					$from_date = date('Y-m-d', strtotime('+1 month', strtotime($from_date)));
					
					if(strtotime($from_date) > strtotime($to_date)){
						$month_2 = date('M - Y', strtotime($to_date));
						
						if($month != $month_2){
							$nestedheaders[] = $month_2;
						}
					}
				}
				
				// $nestedheaders[] = _l('total');
				
				break;
				default:
				break;
			}
			
			return $nestedheaders;
		}
		
		/**
			* Gets the columns budget.
			*
			* @param      integer  $budget_id 
			* @param      string  $budget_type    day or week or month
			*
			* @return     array   The columns budget.
		*/
		public function get_columns_budget($budget_id, $budget_type, $only_data = false)
		{
			$budget = $this->get_budgets($budget_id);
			
			$year = $budget->year;
			
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.($year + 1)));
			
			if($only_data){
				$columns = ['account_name', 'account_id'];
				}else{
				$columns = [['data' => 'account_name', 'type' => 'text', 'readOnly' => true],
				['data' => 'account_id', 'type' => 'text', 'readOnly' => true]
				];
			}
			switch ($budget_type) {
				case 'yearly':
				if($only_data){
					array_push($columns, '_'.$year);
					}else{
					array_push($columns, ['data' => '_'.$year, 'type' => 'numeric', 'numericFormat' => [
					'pattern' => '0.00',
					]]);
				}
				break;
				case 'quarterly':
				while (strtotime($from_date) < strtotime($to_date)) {
					$month = date('m', strtotime($from_date));
					$year = date('Y', strtotime($from_date));
					if($month>=1 && $month<=3)
					{
						$t = 'q1_'.$year;
					}
					else  if($month>=4 && $month<=6)
					{
						$t = 'q2_'.$year;
					}
					else  if($month>=7 && $month<=9)
					{
						$t = 'q3_'.$year;
					}
					else  if($month>=10 && $month<=12)
					{
						$t = 'q4_'.$year;
					}
					
					$nestedheaders[] = $t;
					
					if($only_data){
						array_push($columns, $t);
						}else{
						array_push($columns, ['data' => $t, 'type' => 'numeric', 'numericFormat' => [
						'pattern' => '0.00',
						]]);
					}
					
					$from_date = date('Y-m-d', strtotime('+3 month', strtotime($from_date)));
					
					if(strtotime($from_date) > strtotime($to_date)){
						$month_2 = date('m', strtotime($from_date));
						$year_2 = date('Y', strtotime($from_date));
						if($month_2>=1 && $month_2<=3)
						{
							$t_2 = 'q1_'.$year_2;
						}
						else  if($month_2>=4 && $month_2<=6)
						{
							$t_2 = 'q2_'.$year_2;
						}
						else  if($month_2>=7 && $month_2<=9)
						{
							$t_2 = 'q3_'.$year_2;
						}
						else  if($month_2>=10 && $month_2<=12)
						{
							$t_2 = 'q4_'.$year_2;
						}
						
						if($month . ' - ' . $year != $month_2 . ' - ' . $year_2){
							if($only_data){
								array_push($columns, $t_2);
								}else{
								array_push($columns, ['data' => $t_2, 'type' => 'numeric', 'numericFormat' => [
								'pattern' => '0.00',
								]]);
							}
						}
					}
				}
				
				// if($only_data){
				//     array_push($columns, 'total');
				// }else{
				//     array_push($columns, ['data' => 'total', 'type' => 'numeric', 'numericFormat' => [
				//             'pattern' => '0.00',
				//         ]]);
				// }
				break;
				case 'monthly':
				
				while (strtotime($from_date) < strtotime($to_date)) {
					$month = date('m_Y', strtotime($from_date));
					
					if($only_data){
						array_push($columns, $month);
						}else{
						array_push($columns, ['data' => $month, 'type' => 'numeric', 'numericFormat' => [
						'pattern' => '0.00',
						]]);
					}
					$from_date = date('Y-m-d', strtotime('+1 month', strtotime($from_date)));
					
					if(strtotime($from_date) > strtotime($to_date)){
						$month_2 = date('m_Y', strtotime($to_date));
						
						if($month != $month_2){
							if($only_data){
								array_push($columns, $month_2);
								}else{
								array_push($columns, ['data' => $month_2, 'type' => 'numeric', 'numericFormat' => [
								'pattern' => '0.00',
								]]);
							}
						}
					}
				}
				
				// if($only_data){
				//     array_push($columns, 'total');
				// }else{
				//     array_push($columns, ['data' => 'total', 'type' => 'numeric', 'numericFormat' => [
				//             'pattern' => '0.00',
				//         ]]);
				// }
				break;
				default:
				break;
			}
			
			return $columns;
		}
		
		/**
			* Gets the columns budget.
			*
			* @param      string  $from_date  The from date format dd/mm/YYYY
			* @param      string  $to_date    To date format dd/mm/YYYY
			*
			* @return     array   The columns budget.
		*/
		public function get_columns_budget_by_month($from_date, $to_date)
		{
			$visible = [];
			$visible[1] = get_option('staff_workload_monday_visible');
			$visible[2] = get_option('staff_workload_tuesday_visible');
			$visible[3] = get_option('staff_workload_thursday_visible');
			$visible[4] = get_option('staff_workload_wednesday_visible');
			$visible[5] = get_option('staff_workload_friday_visible');
			$visible[6] = get_option('staff_workload_saturday_visible');
			$visible[7] = get_option('staff_workload_sunday_visible');
			
			
			if (!$this->check_format_date($from_date)) {
				$from_date = to_sql_date($from_date);
			}
			if (!$this->check_format_date($to_date)) {
				$to_date = to_sql_date($to_date);
			}
			$columns = [['data' => 'staff_name', 'type' => 'text', 'readOnly' => true],
			['data' => 'staff_id', 'type' => 'text', 'readOnly' => true],
			['data' => 'capacity', 'type' => 'text', 'readOnly' => true],
			['data' => 'remainCapacityEstimated', 'type' => 'numeric', 'readOnly' => true, 'numericFormat' => ['pattern' => '0.00']],
			['data' => 'remainCapacity', 'type' => 'numeric', 'readOnly' => true, 'numericFormat' => ['pattern' => '0.00']],
			['data' => 'staff_department', 'type' => 'text', 'readOnly' => true],
			['data' => 'staff_role', 'type' => 'text', 'readOnly' => true]];
			while (strtotime($from_date) < strtotime($to_date)) {
				if($visible[date('N', strtotime($from_date))] == 1){
					array_push($columns, ['data' => date('d_m_Y', strtotime($from_date)) . '_e', 'type' => 'numeric', 'numericFormat' => [
					'pattern' => '0.00',
					]]);
					array_push($columns, ['data' => date('d_m_Y', strtotime($from_date)) . '_s', 'type' => 'numeric', 'numericFormat' => [
					'pattern' => '0.00',
					]]);
				}
				$from_date = date('Y-m-d', strtotime('+1 day', strtotime($from_date)));
			}
			return $columns;
		}
		
		/**
			* update a budget.
		*/
		public function update_budget($data, $id){
			$this->db->where('id', $id);
			$this->db->update(db_prefix().'acc_budgets', $data);
			
			if ($this->db->affected_rows() > 0) {
				return true;
			}
			return false;
		}
		
		/**
			* check reconcile restored
			* @param  [type] $account 
			* @param  [type] $company 
			* @return [type]          
		*/
		public function check_reconcile_restored($account){
			$restored = false;
			
			$this->db->where('account', $account);
			$this->db->where('finish', 1);
			$this->db->order_by('id', 'desc');
			$reconcile = $this->db->get(db_prefix() . 'acc_reconciles')->result_array();
			
			if(count($reconcile) > 0){
				$reconcile = true;
			}
			
			return $reconcile;
		}
		
		/**
			* reconcile restored
			* @param  [type] $account 
			* @return [type]          
		*/
		public function reconcile_restored($account)
		{
			$affected_rows=0;
			//get reconcile
			$this->db->where('account', $account);
			$this->db->where('finish', 1);
			$this->db->order_by('ending_date', 'desc');
			
			$reconcile = $this->db->get(db_prefix() . 'acc_reconciles')->row();
			
			if($reconcile){
				$this->db->where('reconcile', $reconcile->id);
				$this->db->update(db_prefix() . 'acc_account_history', ['reconcile' => 0]);
				
				if ($this->db->affected_rows() > 0) {
					$affected_rows++;
					
					$this->db->where('id', $reconcile->id);
					$this->db->delete(db_prefix().'acc_reconciles');
					
					if ($this->db->affected_rows() > 0) {
						$affected_rows++;
					}
				}
			}
			
			if($affected_rows > 0){
				return true;
			}
			return false;
		}
		
		/**
			* get data accounts receivable ageing detail
			* @return array 
		*/
		public function get_data_accounts_receivable_ageing_detail($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$data_report = [];
			$data_report['current'] = [];
			$data_report['1_30_days_past_due'] = [];
			$data_report['31_60_days_past_due'] = [];
			$data_report['61_90_days_past_due'] = [];
			$data_report['91_and_over'] = [];
			
			$this->db->select('*, (select sum(amount) from '.db_prefix() . 'invoicepaymentrecords where invoiceid = '.db_prefix().'invoices.id) as total_payments');
			$this->db->where('IF(duedate IS NOT NULL,(date <= "' . $to_date . '" and duedate >= "' . $to_date . '"),(date = "' .  $to_date . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$data_report['current'][] = [
				'date' => $v['date'],
				'duedate' => $v['duedate'],
				'type' => _l('invoice'),
				'number' => format_invoice_number($v['id']),
				'customer' => $v['clientid'],
				'amount' => $v['total'] - $v['total_payments'],
				];
			}
			
			$this->db->select('*, (select sum(amount) from '.db_prefix() . 'invoicepaymentrecords where invoiceid = '.db_prefix().'invoices.id) as total_payments');
			$this->db->where('IF(duedate IS NOT NULL,(duedate >=  "' . date('Y-m-d', strtotime($to_date.' - 30 days')) . '" and duedate <= "' . date('Y-m-d', strtotime($to_date.' - 1 days')) . '"),(date >=  "' . date('Y-m-d', strtotime($to_date.' - 30 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 1 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$data_report['1_30_days_past_due'][] = [
				'date' => $v['date'],
				'duedate' => $v['duedate'],
				'type' => _l('invoice'),
				'number' => format_invoice_number($v['id']),
				'customer' => $v['clientid'],
				'amount' => $v['total'] - $v['total_payments'],
				];
			}
			
			$this->db->select('*, (select sum(amount) from '.db_prefix() . 'invoicepaymentrecords where invoiceid = '.db_prefix().'invoices.id) as total_payments');
			$this->db->where('IF(duedate IS NOT NULL,(duedate >=  "' . date('Y-m-d', strtotime($to_date.' - 60 days')) . '" and duedate <= "' . date('Y-m-d', strtotime($to_date.' - 31 days')) . '"),(date >=  "' . date('Y-m-d', strtotime($to_date.' - 60 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 31 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$data_report['31_60_days_past_due'][] = [
				'date' => $v['date'],
				'duedate' => $v['duedate'],
				'type' => _l('invoice'),
				'number' => format_invoice_number($v['id']),
				'customer' => $v['clientid'],
				'amount' => $v['total'] - $v['total_payments'],
				];
			}
			
			$this->db->select('*, (select sum(amount) from '.db_prefix() . 'invoicepaymentrecords where invoiceid = '.db_prefix().'invoices.id) as total_payments');
			$this->db->where('IF(duedate IS NOT NULL,(duedate >=  "' . date('Y-m-d', strtotime($to_date.' - 90 days')) . '" and duedate <= "' . date('Y-m-d', strtotime($to_date.' - 61 days')) . '"),(date >=  "' . date('Y-m-d', strtotime($to_date.' - 90 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 61 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$data_report['61_90_days_past_due'][] = [
				'date' => $v['date'],
				'duedate' => $v['duedate'],
				'type' => _l('invoice'),
				'number' => format_invoice_number($v['id']),
				'customer' => $v['clientid'],
				'amount' => $v['total'] - $v['total_payments'],
				];
			}
			
			$this->db->select('*, (select sum(amount) from '.db_prefix() . 'invoicepaymentrecords where invoiceid = '.db_prefix().'invoices.id) as total_payments');
			$this->db->where('IF(duedate IS NOT NULL,(duedate <=  "' . date('Y-m-d', strtotime($to_date.' - 91 days')) . '"),(date <=  "' . date('Y-m-d', strtotime($to_date.' - 91 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$data_report['91_and_over'][] = [
				'date' => $v['date'],
				'duedate' => $v['duedate'],
				'type' => _l('invoice'),
				'number' => format_invoice_number($v['id']),
				'customer' => $v['clientid'],
				'amount' => $v['total'] - $v['total_payments'],
				];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data accounts payable ageing detail
			* @return array 
		*/
		public function get_data_accounts_payable_ageing_detail($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			
			$this->db->where('paymentmode', '');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			$list_expenses = [];
			foreach ($expenses as $key => $value) {
				$list_expenses[] = $value['id'];
			}
			$list_expenses = implode(',', $list_expenses);
			
			$this->db->where('account_detail_type_id', 1004);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			
			$list_accounts = [];
			foreach ($accounts as $key => $value) {
				$list_accounts[] = $value['id'];
			}
			$list_accounts = implode(',', $list_accounts);
			
			$data_report = [];
			$data_report['current'] = [];
			$data_report['1_30_days_past_due'] = [];
			$data_report['31_60_days_past_due'] = [];
			$data_report['61_90_days_past_due'] = [];
			$data_report['91_and_over'] = [];
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('date = "' .  $to_date . '" and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				$total = $v['amount'];
				
				if($v['tax'] != 0){
					$total += ($total / 100 * $v['taxrate']);
				}
				if($v['tax2'] != 0){
					$total += ($v['amount'] / 100 * $v['taxrate2']);
				}
				
				$data_report['current'][] = [
				'date' => $v['date'],
				'duedate' => $v['date'],
				'type' => _l('expenses'),
				'number' => '#'.$v['expense_id'],
				'vendor' => $v['vendor'],
				'customer' => $v['clientid'],
				'amount' => $total,
				];
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('(date >= "' .  date('Y-m-d', strtotime($to_date.' - 30 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 1 days')) . '") and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				$total = $v['amount'];
				
				if($v['tax'] != 0){
					$total += ($total / 100 * $v['taxrate']);
				}
				if($v['tax2'] != 0){
					$total += ($v['amount'] / 100 * $v['taxrate2']);
				}
				
				$data_report['1_30_days_past_due'][] = [
				'date' => $v['date'],
				'duedate' => $v['date'],
				'type' => _l('expenses'),
				'number' => '#'.$v['expense_id'],
				'vendor' => $v['vendor'],
				'customer' => $v['clientid'],
				'amount' => $total,
				];
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('(date >= "' .  date('Y-m-d', strtotime($to_date.' - 60 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 31 days')) . '") and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				$total = $v['amount'];
				
				if($v['tax'] != 0){
					$total += ($total / 100 * $v['taxrate']);
				}
				if($v['tax2'] != 0){
					$total += ($v['amount'] / 100 * $v['taxrate2']);
				}
				
				$data_report['31_60_days_past_due'][] = [
				'date' => $v['date'],
				'duedate' => $v['date'],
				'type' => _l('expenses'),
				'number' => '#'.$v['expense_id'],
				'vendor' => $v['vendor'],
				'customer' => $v['clientid'],
				'amount' => $total,
				];
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('(date >= "' .  date('Y-m-d', strtotime($to_date.' - 90 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 61 days')) . '") and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				$total = $v['amount'];
				
				if($v['tax'] != 0){
					$total += ($total / 100 * $v['taxrate']);
				}
				if($v['tax2'] != 0){
					$total += ($v['amount'] / 100 * $v['taxrate2']);
				}
				
				$data_report['61_90_days_past_due'][] = [
				'date' => $v['date'],
				'duedate' => $v['date'],
				'type' => _l('expenses'),
				'number' => '#'.$v['expense_id'],
				'vendor' => $v['vendor'],
				'customer' => $v['clientid'],
				'amount' => $total,
				];
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('date <= "' .  date('Y-m-d', strtotime($to_date.' - 91 days')) . '" and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				$total = $v['amount'];
				
				if($v['tax'] != 0){
					$total += ($total / 100 * $v['taxrate']);
				}
				if($v['tax2'] != 0){
					$total += ($v['amount'] / 100 * $v['taxrate2']);
				}
				
				$data_report['91_and_over'][] = [
				'date' => $v['date'],
				'duedate' => $v['date'],
				'type' => _l('expenses'),
				'number' => '#'.$v['expense_id'],
				'vendor' => $v['vendor'],
				'customer' => $v['clientid'],
				'amount' => $total,
				];
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data accounts receivable ageing summary
			* @return array 
		*/
		public function get_data_accounts_receivable_ageing_summary($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$tax = 0;
			if(isset($data_filter['tax'])){
				$tax = $data_filter['tax'];
			}
			
			
			$data_report = [];
			
			
			$this->db->where('IF(duedate IS NOT NULL,(date <= "' . $to_date . '" and duedate >= "' . $to_date . '"),(date = "' .  $to_date . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$total_payments = sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $v['id'])));
				
				if(!isset($data_report[$v['clientid']])){
					$data_report[$v['clientid']]['current'] = 0;
					$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
					$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
					$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
					$data_report[$v['clientid']]['91_and_over'] = 0;
					$data_report[$v['clientid']]['total'] = 0;
				}
				
				$data_report[$v['clientid']]['current'] += $v['total'] - $total_payments;
				$data_report[$v['clientid']]['total'] += $v['total'] - $total_payments;
			}
			
			$this->db->where('IF(duedate IS NOT NULL,(duedate >=  "' . date('Y-m-d', strtotime($to_date.' - 30 days')) . '" and duedate <= "' . date('Y-m-d', strtotime($to_date.' - 1 days')) . '"),(date >=  "' . date('Y-m-d', strtotime($to_date.' - 30 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 1 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$total_payments = sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $v['id'])));
				
				if(!isset($data_report[$v['clientid']])){
					$data_report[$v['clientid']]['current'] = 0;
					$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
					$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
					$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
					$data_report[$v['clientid']]['91_and_over'] = 0;
					$data_report[$v['clientid']]['total'] = 0;
				}
				
				$data_report[$v['clientid']]['1_30_days_past_due'] += $v['total'] - $total_payments;
				$data_report[$v['clientid']]['total'] += $v['total'] - $total_payments;
				
			}
			
			$this->db->where('IF(duedate IS NOT NULL,(duedate >=  "' . date('Y-m-d', strtotime($to_date.' - 60 days')) . '" and duedate <= "' . date('Y-m-d', strtotime($to_date.' - 31 days')) . '"),(date >=  "' . date('Y-m-d', strtotime($to_date.' - 60 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 31 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$total_payments = sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $v['id'])));
				
				if(!isset($data_report[$v['clientid']])){
					$data_report[$v['clientid']]['current'] = 0;
					$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
					$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
					$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
					$data_report[$v['clientid']]['91_and_over'] = 0;
					$data_report[$v['clientid']]['total'] = 0;
				}
				
				$data_report[$v['clientid']]['31_60_days_past_due'] += $v['total'] - $total_payments;
				$data_report[$v['clientid']]['total'] += $v['total'] - $total_payments;
				
			}
			
			$this->db->where('IF(duedate IS NOT NULL,(duedate >=  "' . date('Y-m-d', strtotime($to_date.' - 90 days')) . '" and duedate <= "' . date('Y-m-d', strtotime($to_date.' - 61 days')) . '"),(date >=  "' . date('Y-m-d', strtotime($to_date.' - 90 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 61 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$total_payments = sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $v['id'])));
				if(!isset($data_report[$v['clientid']])){
					$data_report[$v['clientid']]['current'] = 0;
					$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
					$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
					$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
					$data_report[$v['clientid']]['91_and_over'] = 0;
					$data_report[$v['clientid']]['total'] = 0;
				}
				
				$data_report[$v['clientid']]['61_90_days_past_due'] += $v['total'] - $total_payments;
				$data_report[$v['clientid']]['total'] += $v['total'] - $total_payments;
				
			}
			
			$this->db->where('IF(duedate IS NOT NULL,(duedate <=  "' . date('Y-m-d', strtotime($to_date.' - 91 days')) . '"),(date <=  "' . date('Y-m-d', strtotime($to_date.' - 91 days')) . '")) and (status = 1 or status = 3 or status = 4)');
			
			$this->db->order_by('date', 'asc');
			
			$invoices = $this->db->get(db_prefix().'invoices')->result_array();
			
			foreach ($invoices as $v) {
				$total_payments = sum_from_table(db_prefix() . 'invoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $v['id'])));
				if(!isset($data_report[$v['clientid']])){
					$data_report[$v['clientid']]['current'] = 0;
					$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
					$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
					$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
					$data_report[$v['clientid']]['91_and_over'] = 0;
					$data_report[$v['clientid']]['total'] = 0;
				}
				
				$data_report[$v['clientid']]['91_and_over'] += $v['total'] - $total_payments;
				$data_report[$v['clientid']]['total'] += $v['total'] - $total_payments;
				
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data accounts payable ageing summary
			* @return array 
		*/
		public function get_data_accounts_payable_ageing_summary($data_filter){
			$from_date = date('Y-m-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$tax = 0;
			if(isset($data_filter['tax'])){
				$tax = $data_filter['tax'];
			}
			
			
			$data_report = [];
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('date = "' .  $to_date . '" and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				if($v['clientid'] != ''){
					
					$total = $v['amount'];
					
					if($v['tax'] != 0){
						$total += ($total / 100 * $v['taxrate']);
					}
					if($v['tax2'] != 0){
						$total += ($v['amount'] / 100 * $v['taxrate2']);
					}
					
					if(!isset($data_report[$v['clientid']])){
						$data_report[$v['clientid']]['current'] = 0;
						$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
						$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
						$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
						$data_report[$v['clientid']]['91_and_over'] = 0;
						$data_report[$v['clientid']]['total'] = 0;
					}
					
					$data_report[$v['clientid']]['current'] += $total;
					$data_report[$v['clientid']]['total'] += $total;
				}
				
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('(date >= "' .  date('Y-m-d', strtotime($to_date.' - 30 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 1 days')) . '") and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				if($v['clientid'] != ''){
					$total = $v['amount'];
					
					if($v['tax'] != 0){
						$total += ($total / 100 * $v['taxrate']);
					}
					if($v['tax2'] != 0){
						$total += ($v['amount'] / 100 * $v['taxrate2']);
					}
					
					if(!isset($data_report[$v['clientid']])){
						$data_report[$v['clientid']]['current'] = 0;
						$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
						$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
						$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
						$data_report[$v['clientid']]['91_and_over'] = 0;
						$data_report[$v['clientid']]['total'] = 0;
					}
					
					$data_report[$v['clientid']]['1_30_days_past_due'] += $total;
					$data_report[$v['clientid']]['total'] += $total;
				}
				
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('(date >= "' .  date('Y-m-d', strtotime($to_date.' - 60 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 31 days')) . '") and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				if($v['clientid'] != ''){
					$total = $v['amount'];
					
					if($v['tax'] != 0){
						$total += ($total / 100 * $v['taxrate']);
					}
					if($v['tax2'] != 0){
						$total += ($v['amount'] / 100 * $v['taxrate2']);
					}
					
					if(!isset($data_report[$v['clientid']])){
						$data_report[$v['clientid']]['current'] = 0;
						$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
						$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
						$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
						$data_report[$v['clientid']]['91_and_over'] = 0;
						$data_report[$v['clientid']]['total'] = 0;
					}
					
					$data_report[$v['clientid']]['31_60_days_past_due'] += $total;
					$data_report[$v['clientid']]['total'] += $total;
				}
				
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('(date >= "' .  date('Y-m-d', strtotime($to_date.' - 90 days')) . '" and date <= "' . date('Y-m-d', strtotime($to_date.' - 61 days')) . '") and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				if($v['clientid'] != ''){
					$total = $v['amount'];
					
					if($v['tax'] != 0){
						$total += ($total / 100 * $v['taxrate']);
					}
					if($v['tax2'] != 0){
						$total += ($v['amount'] / 100 * $v['taxrate2']);
					}
					
					if(!isset($data_report[$v['clientid']])){
						$data_report[$v['clientid']]['current'] = 0;
						$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
						$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
						$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
						$data_report[$v['clientid']]['91_and_over'] = 0;
						$data_report[$v['clientid']]['total'] = 0;
					}
					
					$data_report[$v['clientid']]['61_90_days_past_due'] += $total;
					$data_report[$v['clientid']]['total'] += $total;
				}
				
			}
			
			$this->db->select('*, ' . db_prefix() . 'expenses.id as expense_id, ' . db_prefix() . 'taxes.taxrate as taxrate, ' . db_prefix() . 'taxes_2.taxrate as taxrate2');
			$this->db->where('date <= "' .  date('Y-m-d', strtotime($to_date.' - 91 days')) . '" and paymentmode = ""');
			$this->db->join(db_prefix() . 'taxes', '' . db_prefix() . 'taxes.id = ' . db_prefix() . 'expenses.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes as ' . db_prefix() . 'taxes_2', '' . db_prefix() . 'taxes_2.id = ' . db_prefix() . 'expenses.tax2', 'left');
			$this->db->order_by('date', 'asc');
			$expenses = $this->db->get(db_prefix().'expenses')->result_array();
			
			foreach ($expenses as $v) {
				if($v['clientid'] != ''){
					$total = $v['amount'];
					
					if($v['tax'] != 0){
						$total += ($total / 100 * $v['taxrate']);
					}
					if($v['tax2'] != 0){
						$total += ($v['amount'] / 100 * $v['taxrate2']);
					}
					
					if(!isset($data_report[$v['clientid']])){
						$data_report[$v['clientid']]['current'] = 0;
						$data_report[$v['clientid']]['1_30_days_past_due'] = 0;
						$data_report[$v['clientid']]['31_60_days_past_due'] = 0;
						$data_report[$v['clientid']]['61_90_days_past_due'] = 0;
						$data_report[$v['clientid']]['91_and_over'] = 0;
						$data_report[$v['clientid']]['total'] = 0;
					}
					
					$data_report[$v['clientid']]['91_and_over'] += $total;
					$data_report[$v['clientid']]['total'] += $total;
				}
				
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
		}
		
		/**
			* get data profit and loss 12 months
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss_12_months($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$accounting_method = 'accrual';
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$row = [];
						$start = $month = strtotime($from_date);
						$end = strtotime($to_date);
						
						while($month < $end)
						{
							$this->db->where('account', $val['id']);
							if($accounting_method == 'cash'){
								$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
							}
							$this->db->select('sum(credit) as credit, sum(debit) as debit');
							$this->db->where('(month(date) = "' . date('m',$month) . '" and year(date) = "' . date('Y',$month) . '")');
							$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
							
							$credits = $account_history->credit != '' ? $account_history->credit : 0;
							$debits = $account_history->debit != '' ? $account_history->debit : 0;
							if($acc_show_account_numbers == 1 && $val['number'] != ''){
								$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
								}else{
								$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
							}
							
							
							if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
								$row[date('m-Y', $month)] = $credits - $debits;
								}else{
								$row[date('m-Y', $month)] = $debits - $credits;
							}
							
							$month = strtotime("+1 month", $month);
						}
						$child_account = $this->get_data_profit_and_loss_12_months_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $acc_show_account_numbers);
						
						$data_report[$data_key][] = ['name' => $name, 'amount' => $row, 'child_account' => $child_account];
						
					}
				}
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data profit and loss recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $accounting_method   
			* @return array                 
		*/
		public function get_data_profit_and_loss_12_months_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$row = [];
				$start = $month = strtotime($from_date);
				$end = strtotime($to_date);
				while($month < $end)
				{
					$this->db->where('account', $val['id']);
					if($accounting_method == 'cash'){
						$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
					}
					$this->db->select('sum(credit) as credit, sum(debit) as debit');
					$this->db->where('(month(date) = "' . date('m',$month) . '" and year(date) = "' . date('Y',$month) . '")');
					$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
					
					$credits = $account_history->credit != '' ? $account_history->credit : 0;
					$debits = $account_history->debit != '' ? $account_history->debit : 0;
					if($acc_show_account_numbers == 1 && $val['number'] != ''){
						$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
						}else{
						$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
					}
					
					
					if($val['account_type_id'] == 11 || $val['account_type_id'] == 12){
						$row[date('m-Y', $month)] = $credits - $debits;
						}else{
						$row[date('m-Y', $month)] = $debits - $credits;
					}
					
					$month = strtotime("+1 month", $month);
				}
				
				$child_account[] = ['name' => $name, 'amount' => $row, 'child_account' => $this->get_data_profit_and_loss_12_months_recursive([], $val['id'], $account_type_id, $from_date, $to_date, $accounting_method, $acc_show_account_numbers)];
				
			}
			
			return $child_account;
		}
		
		/**
			* get html profit and loss
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss_12_months($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				$data_return['row_index']++;
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>';
				$total = 0;
				foreach($val['amount'] as $amount){
					$data_return['html'] .= '
					<td class="total_amount">
					'.app_format_money($amount, $currency->name).'
					</td>';
					$total += $amount;
					
				}
				$total_amount = $total;
				$data_return['html'] .= '
				<td class="total_amount">
				'.app_format_money($total_amount, $currency->name).'
				</td>';
				$data_return['html'] .= '</tr>';
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_profit_and_loss_12_months($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>';
					foreach($val['amount'] as $amount){
						$data_return['html'] .= '
						<td class="total_amount"></td>';
						
					}
					$data_return['html'] .= '<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $total;
			}
			return $data_return; 
		}
		
		/**
			* get data budget overview
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_budget_overview($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$budget_id = 0;
			
			if(isset($data_filter['budget'])){
				$budget_id = $data_filter['budget'];
			}
			
			if($budget_id == 0){
				return ['type' => '','data' => []];
			}
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_accounts = [];
			$budget = $this->get_budgets($budget_id);
			
			if($budget->type == 'profit_and_loss_accounts'){
				foreach ($account_type_details as $key => $value) {
					if($value['account_type_id'] == 11){
						$data_accounts['income'][] = $value;
					}
					
					if($value['account_type_id'] == 12){
						$data_accounts['other_income'][] = $value;
					}
					
					if($value['account_type_id'] == 13){
						$data_accounts['cost_of_sales'][] = $value;
					}
					
					if($value['account_type_id'] == 14){
						$data_accounts['expenses'][] = $value;
					}
					
					if($value['account_type_id'] == 15){
						$data_accounts['other_expenses'][] = $value;
					}
				}
				}else{
				foreach ($account_type_details as $key => $value) {
					if($value['account_type_id'] == 1){
						$data_accounts['accounts_receivable'][] = $value;
					}
					if($value['account_type_id'] == 2){
						$data_accounts['current_assets'][] = $value;
					}
					if($value['account_type_id'] == 3){
						$data_accounts['cash_and_cash_equivalents'][] = $value;
					}
					if($value['account_type_id'] == 4){
						$data_accounts['fixed_assets'][] = $value;
					}
					if($value['account_type_id'] == 5){
						$data_accounts['non_current_assets'][] = $value;
					}
					if($value['account_type_id'] == 6){
						$data_accounts['accounts_payable'][] = $value;
					}
					if($value['account_type_id'] == 7){
						$data_accounts['credit_card'][] = $value;
					}
					if($value['account_type_id'] == 8){
						$data_accounts['current_liabilities'][] = $value;
					}
					if($value['account_type_id'] == 9){
						$data_accounts['non_current_liabilities'][] = $value;
					}
					if($value['account_type_id'] == 10){
						$data_accounts['owner_equity'][] = $value;
					}
				}
			}
			
			$year = $budget->year;
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.($year + 1)));
			
			$_from_date = $from_date;
			
			$headers = [];
			while (strtotime($_from_date) < strtotime($to_date)) {
				
				$month = date('M - Y', strtotime($_from_date));
				
				$headers[] = $month;
				
				$_from_date = date('Y-m-d', strtotime('+1 month', strtotime($_from_date)));
				
				if(strtotime($_from_date) > strtotime($to_date)){
					$month_2 = date('M - Y', strtotime($to_date));
					
					if($month != $month_2){
						$headers[] = $month_2;
					}
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$row = [];
						$start = $month = strtotime($from_date);
						$end = strtotime($to_date);
						
						while($month < $end)
						{
							$this->db->select('sum(amount) as amount');
							$this->db->where('account', $val['id']);
							$this->db->where('budget_id', $budget_id);
							$this->db->where('month', date('m',$month));
							$this->db->where('year', date('Y',$month));
							
							$budget_data = $this->db->get(db_prefix() . 'acc_budget_details')->row();
							
							if($acc_show_account_numbers == 1 && $val['number'] != ''){
								$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
								}else{
								$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
							}
							
							$row[date('m-Y', $month)] = $budget_data->amount;
							
							$month = strtotime("+1 month", $month);
						}
						$child_account = $this->get_data_budget_overview_recursive([], $val['id'], $from_date, $to_date, $budget_id, $acc_show_account_numbers);
						
						$data_report[$data_key][] = ['name' => $name, 'amount' => $row, 'child_account' => $child_account];
						
					}
				}
			}
			
			return ['type' => $budget->type,'data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date, 'headers' => $headers];
			
		}
		
		/**
			* get data profit and loss recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date   
			* @param  string $accounting_method   
			* @return array                 
		*/
		public function get_data_budget_overview_recursive($child_account, $account_id, $from_date, $to_date, $budget_id, $acc_show_account_numbers){
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			foreach ($accounts as $val) {
				$row = [];
				$start = $month = strtotime($from_date);
				$end = strtotime($to_date);
				while($month < $end)
				{
					$this->db->select('sum(amount) as amount');
					$this->db->where('account', $val['id']);
					$this->db->where('budget_id', $budget_id);
					$this->db->where('month', date('m',$month));
					$this->db->where('year', date('Y',$month));
					
					$budget_data = $this->db->get(db_prefix() . 'acc_budget_details')->row();
					if($acc_show_account_numbers == 1 && $val['number'] != ''){
						$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
						}else{
						$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
					}
					
					$row[date('m-Y', $month)] = $budget_data->amount;
					
					$month = strtotime("+1 month", $month);
				}
				
				$child_account[] = ['name' => $name, 'amount' => $row, 'child_account' => $this->get_data_budget_overview_recursive([], $val['id'], $from_date, $to_date, $budget_id, $acc_show_account_numbers)];
				
			}
			
			return $child_account;
		}
		
		/**
			* get html profit and loss
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_budget_overview($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			foreach ($child_account as $val) {
				$data_return['row_index']++;
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>';
				$total = 0;
				foreach($val['amount'] as $amount){
					$data_return['html'] .= '
					<td class="total_amount">
					'.app_format_money($amount, $currency->name).'
					</td>';
					$total += $amount;
					
				}
				$total_amount = $total;
				$data_return['html'] .= '
				<td class="total_amount">
				'.app_format_money($total_amount, $currency->name).'
				</td>';
				$data_return['html'] .= '</tr>';
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$data_return = $this->get_html_budget_overview($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>';
					foreach($val['amount'] as $amount){
						$data_return['html'] .= '
						<td class="total_amount"></td>';
						
					}
					$data_return['html'] .= '<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
				}
				
				$data_return['total_amount'] += $total;
			}
			return $data_return; 
		}
		
		/**
			* get data profit and loss
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss_budget_vs_actual($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			$date_financial_year_2 = date('Y-m-t', strtotime($date_financial_year . '  - 1 month + 1 year '));
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$year = date('Y');
			$accounting_method = 'accrual';
			$display_columns = 'total_only';
			$budget_id = 0;
			
			if(isset($data_filter['budget'])){
				$budget_id = $data_filter['budget'];
			}
			
			if($budget_id == 0){
				return ['data' => []];
			}
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			$budget = $this->get_budgets($budget_id);
			
			$year = $budget->year;
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-t', strtotime($from_date . '  - 1 month + 1 year '));
			
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_profit_and_loss_budget_vs_actual_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $budget_id, $acc_show_account_numbers);
						
						$budget_amount = $this->get_budget_by_account($budget_id, $val['id'], $from_date, $to_date);
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$amount = $credits - $debits;
							}else{
							$amount = $debits - $credits;
						}
						
						$data_report[$data_key][] = ['name' => $name, 'amount' => $amount, 'budget_amount' => $budget_amount, 'child_account' => $child_account];
					}
				}
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date];
			
		}
		
		/**
			* get data profit and loss
			* @param  array $data_filter 
			* @return array              
		*/
		public function get_data_profit_and_loss_budget_performance($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$date_financial_year = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.date('Y')));
			$date_financial_year_2 = date('Y-m-t', strtotime($date_financial_year . '  - 1 month + 1 year '));
			
			$from_date = date('Y-01-01');
			$to_date = date('Y-m-d');
			$year = date('Y');
			
			
			$accounting_method = 'accrual';
			$budget_id = 0;
			
			if(isset($data_filter['budget'])){
				$budget_id = $data_filter['budget'];
			}
			
			if($budget_id == 0){
				return ['data' => []];
			}
			
			if(isset($data_filter['accounting_method'])){
				$accounting_method = $data_filter['accounting_method'];
			}
			
			$budget = $this->get_budgets($budget_id);
			
			$year = $budget->year;
			$acc_first_month_of_financial_year = get_option('acc_first_month_of_financial_year');
			
			$from_date = date('Y-m-d', strtotime($acc_first_month_of_financial_year . ' 01 '.$year));
			$to_date = date('Y-m-t', strtotime($from_date . '  - 1 month + 1 year '));
			
			$last_from_date = date('Y-m-01');
			$last_to_date = date('Y-m-t');
			$account_type_details = $this->get_account_type_details();
			$data_report = [];
			$data_accounts = [];
			
			foreach ($account_type_details as $key => $value) {
				if($value['account_type_id'] == 11){
					$data_accounts['income'][] = $value;
				}
				
				if($value['account_type_id'] == 12){
					$data_accounts['other_income'][] = $value;
				}
				
				if($value['account_type_id'] == 13){
					$data_accounts['cost_of_sales'][] = $value;
				}
				
				if($value['account_type_id'] == 14){
					$data_accounts['expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 15){
					$data_accounts['other_expenses'][] = $value;
				}
				
				if($value['account_type_id'] == 23){
					$data_accounts['cash_flow_data'][] = $value;
				}                
			}
			
			foreach ($data_accounts as $data_key => $data_account) {
				$data_report[$data_key] = [];
				foreach ($data_account as $key => $value) {
					$this->db->where('active', 1);
					$this->db->where('(parent_account is null or parent_account = 0)');
					$this->db->where('account_detail_type_id', $value['id']);
					$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
					foreach ($accounts as $val) {
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('(date >= "' . $last_from_date . '" and date <= "' . $last_to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$last_amount = $credits - $debits;
							}else{
							$last_amount = $debits - $credits;
						}
						$last_budget_amount = $this->get_budget_by_account($budget_id, $val['id'],  $last_from_date, $last_to_date);
						
						
						$this->db->where('account', $val['id']);
						if($accounting_method == 'cash'){
							$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
						}
						$this->db->select('sum(credit) as credit, sum(debit) as debit');
						$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
						$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
						$credits = $account_history->credit != '' ? $account_history->credit : 0;
						$debits = $account_history->debit != '' ? $account_history->debit : 0;
						if($value['account_type_id'] == 11 || $value['account_type_id'] == 12){
							$amount = $credits - $debits;
							}else{
							$amount = $debits - $credits;
						}
						$budget_amount = $this->get_budget_by_account($budget_id, $val['id'], $from_date, $to_date);
						
						if($acc_show_account_numbers == 1 && $val['number'] != ''){
							$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
							}else{
							$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
						}
						
						
						$child_account = $this->get_data_profit_and_loss_budget_performance_recursive([], $val['id'], $value['account_type_id'], $from_date, $to_date, $accounting_method, $budget_id, $acc_show_account_numbers);
						
						$data_report[$data_key][] = ['name' => $name, 'last_amount' => $last_amount, 'last_budget_amount' => $last_budget_amount, 'amount' => $amount, 'budget_amount' => $budget_amount, 'child_account' => $child_account];
					}
				}
			}
			
			return ['data' => $data_report, 'from_date' => $from_date, 'to_date' => $to_date, 'last_from_date' => $last_from_date, 'last_to_date' => $last_to_date];
			
		}
		
		/**
			* get html budget variance
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss_budget_vs_actual($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_amount'] = 0;
			$data_return['total_budget_amount'] = 0;
			foreach ($child_account as $val) {
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$total_budget_amount = $val['budget_amount'];
				
				$percent = 0;
				if($val['amount'] != 0){
					if($val['budget_amount'] != 0){
						$percent = round(($val['amount'] / $val['budget_amount']) * 100, 2);
						}else{
						$percent = 100;
					}
				}
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['budget_amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money(($val['amount'] - $val['budget_amount']), $currency->name).'
				</td>
				<td class="total_amount">
				'.$percent.'%
				</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_amount'];
					$t_2 = $data_return['total_budget_amount'];
					$data_return = $this->get_html_profit_and_loss_budget_vs_actual($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_amount += $data_return['total_amount'];
					$total_budget_amount += $data_return['total_budget_amount'];
					
					$data_return['row_index']++;
					$percent = 0;
					if($total_amount != 0){
						if($total_budget_amount != 0){
							$percent = round(($total_amount / $total_budget_amount) * 100, 2);
							}else{
							$percent = 100;
						}
					}
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_budget_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money(($total_amount - $total_budget_amount), $currency->name).'
					</td>
					<td class="total_amount">
					'.$percent.'%
					</td>
					</tr>';
					$data_return['total_amount'] += $t;
					$data_return['total_budget_amount'] += $t_2;
				}
				
				$data_return['total_amount'] += $val['amount'];
				$data_return['total_budget_amount'] += $val['budget_amount'];
			}
			return $data_return; 
		}
		
		
		/**
			* get html budget comparison
			* @param  array $child_account 
			* @param  array $data_return   
			* @param  integer $parent_index  
			* @param  object $currency      
			* @return array               
		*/
		public function get_html_profit_and_loss_budget_performance($child_account, $data_return, $parent_index, $currency){
			$total_amount = 0;
			$data_return['total_last_amount'] = 0;
			$data_return['total_last_budget_amount'] = 0;
			$data_return['total_amount'] = 0;
			$data_return['total_budget_amount'] = 0;
			
			foreach ($child_account as $val) {
				$data_return['row_index']++;
				$total_amount = $val['amount'];
				$total_budget_amount = $val['budget_amount'];
				$total_last_amount = $val['last_amount'];
				$total_last_budget_amount = $val['last_budget_amount'];
				
				$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' expanded">
				<td>
				'.$val['name'].'
				</td>
				<td class="total_amount">
				'.app_format_money($val['last_amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['last_budget_amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['budget_amount'], $currency->name).'
				</td>
				<td class="total_amount">
				'.app_format_money($val['amount'] - $val['budget_amount'], $currency->name).'
				</td>
				<td>&nbsp;&nbsp;&nbsp;</td>
				</tr>';
				
				if(count($val['child_account']) > 0){
					$t = $data_return['total_last_amount'];
					$t_2 = $data_return['total_last_budget_amount'];
					$t_3 = $data_return['total_amount'];
					$t_4 = $data_return['total_budget_amount'];
					$data_return = $this->get_html_profit_and_loss_budget_performance($val['child_account'], $data_return, $data_return['row_index'], $currency);
					
					$total_last_amount += $data_return['total_last_amount'];
					$total_last_budget_amount += $data_return['total_last_budget_amount'];
					$total_amount += $data_return['total_amount'];
					$total_budget_amount += $data_return['total_budget_amount'];
					
					$data_return['row_index']++;
					$data_return['html'] .= '<tr class="treegrid-'.$data_return['row_index'].' '.($parent_index != 0 ? 'treegrid-parent-'.$parent_index : '').' tr_total">
					<td>
					'._l('total_for', $val['name']).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_last_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_last_budget_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_budget_amount, $currency->name).'
					</td>
					<td class="total_amount">
					'.app_format_money($total_amount - $total_budget_amount, $currency->name).'
					</td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					</tr>';
					$data_return['total_last_amount'] += $t;
					$data_return['total_last_budget_amount'] += $t_2;
					$data_return['total_amount'] += $t_3;
					$data_return['total_budget_amount'] += $t_4;
				}
				
				$data_return['total_last_amount'] += $val['last_amount'];
				$data_return['total_last_budget_amount'] += $val['last_budget_amount'];
				$data_return['total_amount'] += $val['amount'];
				$data_return['total_budget_amount'] += $val['budget_amount'];
			}
			return $data_return; 
		}
		
		/**
			* get budget by account
			* @param  integer $company    
			* @param  integer $account_id 
			* @param  integer $year       
			* @return integer            
		*/
		public function get_budget_by_account($budget_id, $account_id, $from_date, $to_date){
			$month = date('m', strtotime($from_date));
			$year = date('Y', strtotime($from_date));
			$month_2 = date('m', strtotime($to_date));
			$year_2 = date('Y', strtotime($to_date));
			
			$this->db->select('sum(amount) as amount');
			$this->db->where('account', $account_id);
			$this->db->where('budget_id', $budget_id);
			$this->db->where('(month >= '.$month.' and year >= '.$year.') and (month <= '.$month_2.' and year <= '.$year_2.')');
			
			$data = $this->db->get(db_prefix() . 'acc_budget_details')->row();
			if($data->amount){
				return $data->amount;
				}else{
				return 0;
			}
		}
		
		/**
			* get data balance sheet summary recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $accounting_method         
			* @param  integer $acc_report_show_non_zero         
			* @return array                 
		*/
		public function get_data_profit_and_loss_budget_performance_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $budget_id, $acc_show_account_numbers){
			$year = date('Y', strtotime($to_date));
			$last_from_date = date('Y-m-01');
			$last_to_date = date('Y-m-t');
			
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			foreach ($accounts as $val) {
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('(date >= "' . $last_from_date . '" and date <= "' . $last_to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($account_type_id == 11 || $account_type_id == 12){
					$last_amount = $credits - $debits;
					}else{
					$last_amount = $debits - $credits;
				}
				$last_budget_amount = $this->get_budget_by_account($budget_id, $val['id'], $last_from_date, $last_to_date);
				
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				$budget_amount = $this->get_budget_by_account($budget_id, $val['id'], $from_date, $to_date);
				if($account_type_id == 11 || $account_type_id == 12){
					$amount = $credits - $debits;
					}else{
					$amount = $debits - $credits;
				}
				
				$child_account[] = ['name' => $name, 'last_amount' => $last_amount, 'last_budget_amount' => $last_budget_amount, 'amount' => $amount, 'budget_amount' => $budget_amount, 'child_account' => $this->get_data_profit_and_loss_budget_performance_recursive([], $val['id'],$account_type_id, $from_date, $to_date, $accounting_method, $budget_id, $acc_show_account_numbers)];
				
				
			}
			
			return $child_account;
		}
		
		/**
			* get data balance sheet summary recursive
			* @param  array $child_account         
			* @param  integer $account_id      
			* @param  integer $account_type_id 
			* @param  string $from_date       
			* @param  string $to_date         
			* @param  string $accounting_method         
			* @param  integer $acc_report_show_non_zero         
			* @return array                 
		*/
		public function get_data_profit_and_loss_budget_vs_actual_recursive($child_account, $account_id, $account_type_id, $from_date, $to_date, $accounting_method, $budget_id, $acc_show_account_numbers){
			$year = date('Y', strtotime($to_date));
			$last_from_date = date('Y-m-01');
			$last_to_date = date('Y-m-t');
			
			$this->db->where('active', 1);
			$this->db->where('parent_account', $account_id);
			$accounts = $this->db->get(db_prefix().'acc_accounts')->result_array();
			$data_return = [];
			foreach ($accounts as $val) {
				$this->db->where('account', $val['id']);
				$this->db->where('account', $val['id']);
				if($accounting_method == 'cash'){
					$this->db->where('((rel_type = "invoice" and paid = 1) or rel_type != "invoice")');
				}
				$this->db->select('sum(credit) as credit, sum(debit) as debit');
				$this->db->where('(date >= "' . $from_date . '" and date <= "' . $to_date . '")');
				$account_history = $this->db->get(db_prefix().'acc_account_history')->row();
				
				$credits = $account_history->credit != '' ? $account_history->credit : 0;
				$debits = $account_history->debit != '' ? $account_history->debit : 0;
				if($acc_show_account_numbers == 1 && $val['number'] != ''){
					$name = $val['name'] != '' ? $val['number'].' - '.$val['name'] : $val['number'].' - '._l($val['key_name']);
					}else{
					$name = $val['name'] != '' ? $val['name'] : _l($val['key_name']);
				}
				
				
				$budget_amount = $this->get_budget_by_account($budget_id, $val['id'], $from_date, $to_date);
				if($account_type_id == 11 || $account_type_id == 12){
					$amount = $credits - $debits;
					}else{
					$amount = $debits - $credits;
				}
				
				$child_account[] = ['name' => $name, 'amount' => $amount, 'budget_amount' => $budget_amount, 'child_account' => $this->get_data_profit_and_loss_budget_vs_actual_recursive([], $val['id'],$account_type_id, $from_date, $to_date, $accounting_method, $budget_id, $acc_show_account_numbers)];
				
				
			}
			
			return $child_account;
		}
		
		//=============================== Get TCS Report ===============================
		public function tcs_table_data($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date( $data['from_date']);
			$to_date = to_sql_date($data['to_date']);
			
			$this->db->select(db_prefix() .'clients.company,'.db_prefix() .'clients.address,'.db_prefix() .'contacts.Pan,'.db_prefix() .'salesmaster.SalesID,'.db_prefix() .'salesmaster.Transdate,'.db_prefix() .'salesmaster.BillAmt,'.db_prefix() .'salesmaster.tcs,'.db_prefix() .'salesmaster.tcsAmt');
			
			$this->db->from(db_prefix() . 'salesmaster');
			
			$this->db->join(db_prefix() .'clients', db_prefix() .'salesmaster.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'salesmaster.PlantID='.db_prefix().'clients.PlantID','left');
			$this->db->join(db_prefix() .'contacts', db_prefix() .'clients.AccountID = '.db_prefix() .'contacts.AccountID AND '.db_prefix().'salesmaster.PlantID='.db_prefix().'contacts.PlantID','left');
			$this->db->where(db_prefix() . 'salesmaster.tcsAmt  is NOT NULL', NULL, FALSE);
			$this->db->where(db_prefix() . 'salesmaster.tcsAmt !=',  0);
			$this->db->where(db_prefix() . 'salesmaster.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'salesmaster.FY', $fy);
			$this->db->where( db_prefix() . 'salesmaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			$this->db->group_by( db_prefix() .'salesmaster.SalesID');
			$this->db->order_by( db_prefix() .'salesmaster.Transdate','ASC');
			return $this->db->get()->result_array();
		}
		
		//========================= Get TDS Report =====================================
		public function GetTDSReport($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date( $data['from_date']);
			$to_date = to_sql_date($data['to_date']);
			
			$this->db->select('tblpurchasemaster.*,tblclients.company,tblTDSMaster.TDSName');
			$this->db->from(db_prefix() . 'purchasemaster');
			$this->db->join(db_prefix() .'clients', db_prefix() .'purchasemaster.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'purchasemaster.PlantID='.db_prefix().'clients.PlantID');
			$this->db->join('tblTDSMaster', 'tblTDSMaster.TDSCode = tblpurchasemaster.TdsSection');
			$this->db->where(db_prefix() . 'purchasemaster.TdsAmt  is NOT NULL', NULL, FALSE);
			$this->db->where(db_prefix() . 'purchasemaster.TdsAmt >', 0);
			$this->db->where(db_prefix() . 'purchasemaster.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'purchasemaster.FY', $fy);
			$this->db->where( db_prefix() . 'purchasemaster.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			$this->db->order_by( db_prefix() .'purchasemaster.Transdate','ASC');
			return $this->db->get()->result_array();
		}
		
		public function table_data($data){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date( $data['from_date']);
			$to_date = to_sql_date($data['to_date']);
			
			/*$this->db->select(db_prefix() . 'cdnote.*,'.db_prefix() .'cdnotehistory.*,'.db_prefix() .'clients.company,'.db_prefix() .'clients.vat');
				
				$this->db->from(db_prefix() . 'cdnote');
				$this->db->join(db_prefix() .'cdnotehistory', db_prefix() .'cdnote.Billno = '.db_prefix() .'cdnotehistory.billno AND '.db_prefix().'cdnotehistory.PlantID='.db_prefix().'cdnote.PlantID AND '.db_prefix().'cdnotehistory.FY='.db_prefix().'cdnote.FY');
				if($data['gst_type'] == "1"){
				$this->db->join(db_prefix() .'clients', db_prefix() .'cdnote.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'cdnote.PlantID='.db_prefix().'clients.PlantID');
				} else if($data['gst_type'] == "2"){
				$this->db->join(db_prefix() .'clients', db_prefix() .'cdnote.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'cdnote.PlantID='.db_prefix().'clients.PlantID AND '.db_prefix().'clients.vat IS NOT NULL');
				} else if($data['gst_type'] == "3"){
				$this->db->join(db_prefix() .'clients', db_prefix() .'cdnote.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'cdnote.PlantID='.db_prefix().'clients.PlantID AND '.db_prefix().'clients.vat IS NULL');
				}
				$this->db->where(db_prefix() . 'cdnote.PlantID', $selected_company);
				$this->db->where(db_prefix() . 'cdnote.FY', $fy);
				$this->db->where( db_prefix() . 'cdnote.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				if($data['credit_debit_type'] != ''){
				$this->db->where(db_prefix() . 'cdnote.BT', $data['credit_debit_type']);
				}
				$this->db->order_by( db_prefix() .'cdnote.Billno','ASC');
			return $this->db->get()->result_array();*/
			
			$this->db->select(db_prefix() . 'cdnotehistory.transdate,'.db_prefix() .'cdnotehistory.billno,tblcdnotehistory.ttype,tblcdnotehistory.cgst,SUM(tblcdnotehistory.cgstamt) AS cgstamts,tblcdnotehistory.sgst,SUM(tblcdnotehistory.sgstamt) AS sgstamts,tblcdnotehistory.igst,SUM(tblcdnotehistory.igstamt) AS igstamts,tblcdnotehistory.hsncode,SUM(tblcdnotehistory.rate) AS SaleAmt,SUM(tblcdnotehistory.amount) AS BillAmt,'.db_prefix() .'clients.company,'.db_prefix() .'clients.vat');
			
			$this->db->from(db_prefix() . 'cdnotehistory');
			//$this->db->join(db_prefix() .'cdnotehistory', db_prefix() .'cdnote.Billno = '.db_prefix() .'cdnotehistory.billno AND '.db_prefix().'cdnotehistory.PlantID='.db_prefix().'cdnote.PlantID AND '.db_prefix().'cdnotehistory.FY='.db_prefix().'cdnote.FY');
			if($data['gst_type'] == "1"){
				$this->db->join(db_prefix() .'clients', db_prefix() .'cdnotehistory.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'cdnotehistory.PlantID='.db_prefix().'clients.PlantID');
				} else if($data['gst_type'] == "2"){
				$this->db->join(db_prefix() .'clients', db_prefix() .'cdnotehistory.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'cdnotehistory.PlantID='.db_prefix().'clients.PlantID AND '.db_prefix().'clients.vat IS NOT NULL');
				} else if($data['gst_type'] == "3"){
				$this->db->join(db_prefix() .'clients', db_prefix() .'cdnotehistory.AccountID = '.db_prefix() .'clients.AccountID AND '.db_prefix().'cdnotehistory.PlantID='.db_prefix().'clients.PlantID AND '.db_prefix().'clients.vat IS NULL');
			}
			$this->db->where(db_prefix() . 'cdnotehistory.plantid', $selected_company);
			$this->db->where(db_prefix() . 'cdnotehistory.fy', $fy);
			$this->db->where( db_prefix() . 'cdnotehistory.transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			if($data['credit_debit_type'] != ''){
				$this->db->where(db_prefix() . 'cdnotehistory.ttype', $data['credit_debit_type']);
			}
			$this->db->group_by( db_prefix() .'cdnotehistory.billno');
			$this->db->order_by( db_prefix() .'cdnotehistory.billno','ASC');
			return $this->db->get()->result_array();
			
		}
		
		function get_account_list_by_accoutID($postData)
		{
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			$where_ = '';
			$where1_ = '';
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				
				$this->db->select(db_prefix() . 'staff.*');
				$where_ .= '(AccountID = "' . $q . '" )';
				$this->db->where($where_);
				$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
				//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
				$this->db->where('tblstaff.PlantID',$selected_company);
				$records = $this->db->get(db_prefix() . 'staff')->result();
				if($records){
					foreach($records as $row )
					{
						$full_name = $row->firstname." ".$row->lastname;
						$response[] = array("label"=>$full_name,"value"=>$row->AccountID);
					}
					}else{
					$this->db->select(db_prefix() . 'clients.*');
					$where1_ .= '(AccountID = "' . $q . '" )';
					$this->db->where($where1_);
					$this->db->where('PlantID',$selected_company);
					$records1 = $this->db->get(db_prefix() . 'clients')->result();
					foreach($records1 as $row1 )
					{
						
						$response[] = array("label"=>$row1->company,"value"=>$row1->AccountID);
					}
				}
			}
			
			return $response;
		}
		
		public function get_account_details_by_AccountID($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('*');
			$this->db->where('AccountID', $id);
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			$this->db->where('tblstaff.PlantID',$selected_company);
			$staff_act = $this->db->get(db_prefix() . 'staff')->row();
			if($staff_act){
				return $staff_act;
				}else{
				$this->db->select('*');
				$this->db->where('AccountID', $id);
				$this->db->where('PlantID',$selected_company);
				$act1 = $this->db->get(db_prefix() . 'clients')->row();
				if($act1){
					return $act1;
					}else{
					return false;
				}
			}
			
		}
		
		public function TrialBalData($data){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$regExp ="'.*;s:[0-9]+:'".$selected_company."'.*'";
			$regExp1 ="'.*;s:[0-9]+:";
			$regExp2 =".*'";
			
			
			$this->db->select(db_prefix() . 'clients.company,'.db_prefix() . 'clients.AccountID,'.db_prefix() . 'staff.AccountID AS AccountID2,'.db_prefix() . 'clients.address,'.db_prefix() . 'clients.Address3,'.db_prefix() . 'clients.zip,
			'.db_prefix() .'accountbalances.BAL1,'.db_prefix().'accountgroupssub.SubActGroupName,Group2.SubActGroupName AS SubActGroupName2,'.db_prefix().'xx_citylist.city_name,City2.city_name AS city_name2,'.db_prefix().'xx_statelist.state_name,State2.state_name AS state_name2,
			tblstaff.firstname, tblstaff.lastname, tblstaff.current_address, tblstaff.home_town, tblstaff.pincode');
			
			$this->db->from(db_prefix() . 'accountbalances');
			$this->db->join(db_prefix() .'clients', db_prefix().'clients.AccountID='.db_prefix().'accountbalances.AccountID AND ' .db_prefix().'clients.PlantID='.db_prefix().'accountbalances.PlantID','LEFT');
			$this->db->join(db_prefix() .'staff', db_prefix().'staff.AccountID='.db_prefix().'accountbalances.AccountID AND ' .db_prefix().'staff.PlantID = '.$selected_company.'','LEFT');
			$this->db->join(db_prefix() .'accountgroupssub', db_prefix().'accountgroupssub.SubActGroupID='.db_prefix().'clients.SubActGroupID ','LEFT');
			$this->db->join(db_prefix() .'accountgroupssub AS Group2', 'Group2.SubActGroupID='.db_prefix().'staff.SubActGroupID ','LEFT');
			$this->db->join(db_prefix() .'xx_citylist', db_prefix().'xx_citylist.id='.db_prefix().'clients.city ','LEFT');
			$this->db->join(db_prefix() .'xx_citylist As City2', 'City2.id='.db_prefix().'staff.city ','LEFT');
			$this->db->join(db_prefix() .'xx_statelist', db_prefix().'xx_statelist.short_name='.db_prefix().'clients.state ','LEFT');
			$this->db->join(db_prefix() .'xx_statelist AS State2', 'State2.short_name='.db_prefix().'staff.state ','LEFT');
			$this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'accountbalances.FY', $fy);
			//$this->db->order_by('Group2.SubActGroupName,'.db_prefix() .'accountgroupssub.SubActGroupName','ASC');
			$resultData = $this->db->get()->result_array();
			$i = 0;
			foreach($resultData as $value){
				if($value['SubActGroupName'] == null){
					$resultData[$i]['Group'] = $value['SubActGroupName2'];
					}else{
					$resultData[$i]['Group'] = $value['SubActGroupName'];
				}
				$i++;
			}
			array_multisort(array_column($resultData, 'Group'), SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $resultData);
			return $resultData;
		}
		
		public function clientData($data){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy.'-04-01';
			$to_date = to_sql_date($data['as_on']);
			$subGroupID1 = $data['SubgroupID1'];
			$subGroupID = $data['SubgroupID'];
			$m = substr($to_date,5,2);
			//return $m;
			$from_date_new = '20'.$fy.'-'.$m.'01';
			$this->db->select(db_prefix() . 'clients.company,'.db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.address,'.db_prefix() . 'clients.Address3,'.db_prefix() . 'clients.zip,'.db_prefix() .'accountbalances.BAL1,'.db_prefix().'accountgroupssub1.SubActGroupName as SubActGroupName1,'.db_prefix().'accountgroupssub.SubActGroupName,'.db_prefix().'xx_citylist.city_name,'.db_prefix().'xx_statelist.state_name');
			
			$this->db->from(db_prefix() . 'clients');
			$this->db->join(db_prefix() .'accountbalances', db_prefix().'accountbalances.AccountID='.db_prefix().'clients.AccountID AND ' .db_prefix().'accountbalances.PlantID='.db_prefix().'clients.PlantID AND '.db_prefix().'accountbalances.FY="'.$fy.'"','LEFT');
			$this->db->join(db_prefix() .'accountgroupssub', db_prefix().'accountgroupssub.SubActGroupID='.db_prefix().'clients.SubActGroupID ','LEFT');
			$this->db->join(db_prefix() .'accountgroupssub1', db_prefix().'accountgroupssub1.SubActGroupID1='.db_prefix().'accountgroupssub.SubActGroupID1 ','LEFT');
			$this->db->join(db_prefix() .'xx_citylist', db_prefix().'xx_citylist.id='.db_prefix().'clients.city ','LEFT');
			$this->db->join(db_prefix() .'xx_statelist', db_prefix().'xx_statelist.short_name='.db_prefix().'clients.state ','LEFT');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			if($subGroupID1 == ""){
				
				}else{
				$this->db->where(db_prefix() . 'accountgroupssub1.SubActGroupID1', $subGroupID1);
			}
			if($subGroupID == ""){
				
				}else{
				$this->db->where(db_prefix() . 'clients.SubActGroupID', $subGroupID);
			}
			$this->db->order_by( db_prefix() .'accountgroupssub.SubActGroupName','ASC');
			$resultData = $this->db->get()->result_array();
			return $resultData;
		}
		
		public function staffData($data){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy.'-04-01';
			$to_date = to_sql_date($data['as_on']);
			$subGroupID1 = $data['SubgroupID1'];
			$subGroupID = $data['SubgroupID'];
			$m = substr($to_date,5,2);
			//return $m;
			$from_date_new = '20'.$fy.'-'.$m.'01';
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$this->db->select(db_prefix() . 'staff.firstname,'.db_prefix() . 'staff.AccountID,'.db_prefix() . 'staff.firstname,'.db_prefix() . 'staff.lastname,'.db_prefix() . 'staff.current_address,'.db_prefix() . 'staff.pincode,'.db_prefix() . 'staff.home_town,'.db_prefix() .'accountbalances.BAL1,'.db_prefix().'accountgroupssub1.SubActGroupName as SubActGroupName1,'.db_prefix().'accountgroupssub.SubActGroupName,'.db_prefix().'xx_citylist.city_name,'.db_prefix().'xx_statelist.state_name');
			
			$this->db->from(db_prefix() . 'staff');
			$this->db->join(db_prefix() .'accountbalances', db_prefix().'accountbalances.AccountID='.db_prefix().'staff.AccountID AND ' .db_prefix().'accountbalances.PlantID='.$selected_company.' AND '.db_prefix().'accountbalances.FY="'.$fy.'"','LEFT');
			$this->db->join(db_prefix() .'accountgroupssub', db_prefix().'accountgroupssub.SubActGroupID='.db_prefix().'staff.SubActGroupID ','LEFT');
			$this->db->join(db_prefix() .'accountgroupssub1', db_prefix().'accountgroupssub1.SubActGroupID1='.db_prefix().'accountgroupssub.SubActGroupID1 ','LEFT');
			$this->db->join(db_prefix() .'xx_citylist', db_prefix().'xx_citylist.id='.db_prefix().'staff.city ','LEFT');
			$this->db->join(db_prefix() .'xx_statelist', db_prefix().'xx_statelist.short_name='.db_prefix().'staff.state ','LEFT');
			$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);
			//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			if($subGroupID1 == ""){
				
				}else{
				$this->db->where(db_prefix() . 'accountgroupssub1.SubActGroupID1', $subGroupID1);
			}
			if($subGroupID == ""){
				
				}else{
				$this->db->where(db_prefix() . 'staff.SubActGroupID', $subGroupID);
			}
			$this->db->order_by( db_prefix() .'accountgroupssub.SubActGroupName','ASC');
			$resultData = $this->db->get()->result_array();
			
			return $resultData;
		}
		
		
		public function table_data_for_trial_balance($data){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy.'-04-01';
			$to_date = to_sql_date($data['as_on']);
			$m = substr($to_date,5,2);
			//return $m;
			$from_date_new = '20'.$fy.'-'.$m.'01';
			$this->db->select(db_prefix() . 'clients.*,'.db_prefix() .'accountbalances.*,'.db_prefix().'accountgroupssub.SubActGroupName,'.db_prefix().'xx_citylist.city_name,'.db_prefix().'xx_statelist.state_name');
			
			$this->db->from(db_prefix() . 'clients');
			$this->db->join(db_prefix() .'accountbalances', db_prefix().'accountbalances.AccountID='.db_prefix().'clients.AccountID AND ' .db_prefix().'accountbalances.PlantID='.db_prefix().'clients.PlantID AND '.db_prefix().'accountbalances.FY="'.$fy.'"','LEFT');
			$this->db->join(db_prefix() .'accountgroupssub', db_prefix().'accountgroupssub.SubActGroupID='.db_prefix().'clients.SubActGroupID ','LEFT');
			$this->db->join(db_prefix() .'xx_citylist', db_prefix().'xx_citylist.id='.db_prefix().'clients.city ','LEFT');
			$this->db->join(db_prefix() .'xx_statelist', db_prefix().'xx_statelist.short_name='.db_prefix().'clients.state ','LEFT');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->order_by( db_prefix() .'accountgroupssub.SubActGroupName','ASC');
			$resultData = $this->db->get()->result_array();
			$i = 0;
			foreach($resultData as $row )
			{
				$AccountID = trim($row["AccountID"]);
				$this->db->select('sum(Amount) as credit_bal,AccountID');
				$this->db->where('tblaccountledger.PlantID', $selected_company);
				$this->db->where('tblaccountledger.TType LIKE', 'C');
				$this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				$this->db->group_by('AccountID');
				$this->db->WHERE('tblaccountledger.AccountID', $AccountID);
				$credit_bal = $this->db->get('tblaccountledger')->row();
				$resultData[$i]["CRBAL"] = $credit_bal->credit_bal;
				
				$this->db->select('sum(Amount) as debit_bal,AccountID');
				$this->db->where('tblaccountledger.PlantID', $selected_company);
				$this->db->where('tblaccountledger.TType', 'D');
				$this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				$this->db->group_by('AccountID');
				$this->db->WHERE('tblaccountledger.AccountID', $AccountID);
				$debit_bal = $this->db->get('tblaccountledger')->row();
				$resultData[$i]["DRBAL"] = $debit_bal->debit_bal;
				
				$i++;
			}
			
			return $resultData;
		}
		
		public function table_data_for_trial_balance_staff($data){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy.'-04-01';
			$to_date = to_sql_date($data['as_on']);
			$m = substr($to_date,5,2);
			//return $m;
			$from_date_new = '20'.$fy.'-'.$m.'01';
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$this->db->select(db_prefix() . 'staff.*,'.db_prefix() .'accountbalances.*,'.db_prefix().'accountgroupssub.SubActGroupName,'.db_prefix().'xx_citylist.city_name,'.db_prefix().'xx_statelist.state_name');
			
			$this->db->from(db_prefix() . 'staff');
			$this->db->join(db_prefix() .'accountbalances', db_prefix().'accountbalances.AccountID='.db_prefix().'staff.AccountID AND ' .db_prefix().'accountbalances.PlantID='.$selected_company.' AND '.db_prefix().'accountbalances.FY="'.$fy.'"');
			$this->db->join(db_prefix() .'accountgroupssub', db_prefix().'accountgroupssub.SubActGroupID='.db_prefix().'staff.SubActGroupID ','LEFT');
			$this->db->join(db_prefix() .'xx_citylist', db_prefix().'xx_citylist.id='.db_prefix().'staff.city ','LEFT');
			$this->db->join(db_prefix() .'xx_statelist', db_prefix().'xx_statelist.short_name='.db_prefix().'staff.state ','LEFT');
			$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);
			//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);
			$this->db->order_by( db_prefix() .'accountgroupssub.SubActGroupName','ASC');
			$resultData = $this->db->get()->result_array();
			
			$i = 0;
			foreach($resultData as $row )
			{
				$AccountID = trim($row["AccountID"]);
				$this->db->select('sum(Amount) as credit_bal,AccountID');
				$this->db->where('tblaccountledger.PlantID', $selected_company);
				$this->db->where('tblaccountledger.TType', 'C');
				$this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				$this->db->group_by('AccountID');
				$this->db->WHERE('tblaccountledger.AccountID', $AccountID);
				$credit_bal = $this->db->get('tblaccountledger')->row();
				$resultData[$i]["CRBAL"] = $credit_bal->credit_bal;
				
				$this->db->select('sum(Amount) as debit_bal,AccountID');
				$this->db->where('tblaccountledger.PlantID', $selected_company);
				$this->db->where('tblaccountledger.TType', 'D');
				$this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
				$this->db->group_by('AccountID');
				$this->db->WHERE('tblaccountledger.AccountID', $AccountID);
				$debit_bal = $this->db->get('tblaccountledger')->row();
				$resultData[$i]["DRBAL"] = $debit_bal->debit_bal;
				
				$i++;
			}
			
			return $resultData;
		}
		
		public function creditledger_data($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy.'-04-01';
			$to_date = to_sql_date($data['as_on']);
			
			// credit crated
			$this->db->select('sum(Amount) as credit_bal,AccountID');
			$this->db->where('tblaccountledger.PlantID', $selected_company);
			$this->db->where('tblaccountledger.TType', 'C');
			$this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			$this->db->group_by('AccountID');
			$credit_bal = $this->db->get('tblaccountledger')->result_array();
			
			return $credit_bal;
		}
		
		public function debitledger_data($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy.'-04-01';
			$to_date = to_sql_date($data['as_on']);
			
			// debit crated
			$this->db->select('sum(Amount) as debit_bal,AccountID');
			$this->db->where('tblaccountledger.PlantID', $selected_company);
			$this->db->where('tblaccountledger.TType', 'D');
			$this->db->where('tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"');
			$this->db->group_by('AccountID');
			$debit_bal = $this->db->get('tblaccountledger')->result_array();
			
			return $debit_bal;
		}
		
		//==============================================================================
		// Account Monitor
		public function GetAccountMonitorCreditNew($data,$AccountIDs){
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$from_date = to_sql_date( $data['from_date']).' 00:00:00';
			$to_date = to_sql_date($data['to_date']).' 23:59:59';
			
			$SQL = 'select year(tblaccountledger.Transdate) as year, month(tblaccountledger.Transdate) as month,tblaccountledger.AccountID,tblaccountledger.TType, sum(tblaccountledger.Amount) as total_amount from tblaccountledger 
			WHERE tblaccountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND tblaccountledger.PlantID = "'.$PlantID.'" AND tblaccountledger.FY = "'.$FY.'" AND TType = "C" AND tblaccountledger.AccountID IN('.$AccountIDs.')';
			
			$SQL .= ' group by year(tblaccountledger.Transdate), month(tblaccountledger.Transdate),tblaccountledger.AccountID  ORDER BY tblaccountledger.AccountID ASC';
			$query = $this->db->query($SQL);   
			
			return $query->result_array(); 
			
		}
		public function GetAccountMonitorDebitNew($data,$AccountIDs){
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$from_date = to_sql_date( $data['from_date']).' 00:00:00';
			$to_date = to_sql_date($data['to_date']).' 23:59:59';
			
			$SQL = 'select year(tblaccountledger.Transdate) as year, month(tblaccountledger.Transdate) as month,tblaccountledger.AccountID,tblaccountledger.TType, sum(tblaccountledger.Amount) as total_amount from tblaccountledger 
			WHERE tblaccountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND tblaccountledger.PlantID = "'.$PlantID.'" AND tblaccountledger.FY = "'.$FY.'" AND TType = "D"  AND tblaccountledger.AccountID IN('.$AccountIDs.')';
			
			$SQL .= ' group by year(tblaccountledger.Transdate), month(tblaccountledger.Transdate),tblaccountledger.AccountID  ORDER BY tblaccountledger.AccountID ASC';
			$query = $this->db->query($SQL);   
			
			return $query->result_array(); 
			
		}
		public function GetAccountMonitorCredit($data){
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$from_date = to_sql_date( $data['from_date']).' 00:00:00';
			$to_date = to_sql_date($data['to_date']).' 23:59:59';
			$SubgroupID = $data['SubgroupID'];
			
			$SQL = 'select year(tblaccountledger.Transdate) as year, month(tblaccountledger.Transdate) as month,tblaccountledger.AccountID,tblaccountledger.TType, sum(tblaccountledger.Amount) as total_amount from tblaccountledger 
			LEFT JOIN tblclients ON tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
			LEFT JOIN tblstaff ON tblstaff.AccountID = tblaccountledger.AccountID AND tblstaff.PlantID = tblaccountledger.PlantID
			WHERE tblaccountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND tblaccountledger.PlantID = "'.$PlantID.'" AND tblaccountledger.FY = "'.$FY.'" AND TType = "C" ';
			if($SubgroupID !==""){
				$SQL .= ' AND (tblclients.SubActGroupID = "'.$SubgroupID.'" OR tblstaff.SubActGroupID = "'.$SubgroupID.'")';
			}
			$SQL .= ' group by year(tblaccountledger.Transdate), month(tblaccountledger.Transdate),tblaccountledger.AccountID  ORDER BY tblaccountledger.AccountID ASC';
			$query = $this->db->query($SQL);   
			
			return $query->result_array(); 
		}
		
		public function GetAccountMonitorDebit($data){
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$from_date = to_sql_date( $data['from_date']).' 00:00:00';
			$to_date = to_sql_date($data['to_date']).' 23:59:59';
			$SubgroupID = $data['SubgroupID'];
			$SQL = 'select year(tblaccountledger.Transdate) as year, month(tblaccountledger.Transdate) as month,tblaccountledger.AccountID,tblaccountledger.TType, sum(tblaccountledger.Amount) as total_amount from tblaccountledger 
			LEFT JOIN tblclients ON tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID
			LEFT JOIN tblstaff ON tblstaff.AccountID = tblaccountledger.AccountID AND tblstaff.PlantID = tblaccountledger.PlantID
			WHERE tblaccountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'" AND tblaccountledger.PlantID = "'.$PlantID.'" AND tblaccountledger.FY = "'.$FY.'" AND TType = "D" ';
			if($SubgroupID !==""){
				$SQL .= ' AND (tblclients.SubActGroupID = "'.$SubgroupID.'" OR tblstaff.SubActGroupID = "'.$SubgroupID.'")';
			}
			$SQL .= ' group by year(tblaccountledger.Transdate), month(tblaccountledger.Transdate),tblaccountledger.AccountID  ORDER BY tblaccountledger.AccountID ASC';
			$query = $this->db->query($SQL);   
			
			return $query->result_array(); 
		}
		
		public function GetAccountMonitor($data){
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$SubgroupID = $data['SubgroupID'];
			$SQL = 'select tblclients.AccountID, tblclients.company,tblaccountgroupssub.SubActGroupName
			from tblclients 
			INNER JOIN tblaccountgroupssub ON tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID
			WHERE  tblclients.PlantID = "'.$PlantID.'"';
			if($SubgroupID !==""){
				$SQL .= ' AND tblclients.SubActGroupID = "'.$SubgroupID.'"';
			}
			$SQL .= ' group by tblclients.AccountID';
			$query = $this->db->query($SQL); 
			return $query->result_array(); 
		}
		public function GetAccountMonitorStaff($data){
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$SubgroupID = $data['SubgroupID'];
			$SQL = 'select tblstaff.AccountID, tblstaff.firstname,tblstaff.lastname,tblaccountgroupssub.SubActGroupName
			from tblstaff 
			INNER JOIN tblaccountgroupssub ON tblaccountgroupssub.SubActGroupID = tblstaff.SubActGroupID
			WHERE  tblstaff.PlantID = "'.$PlantID.'"';
			if($SubgroupID !==""){
				$SQL .= ' AND tblstaff.SubActGroupID = "'.$SubgroupID.'"';
			}
			$SQL .= ' group by tblstaff.AccountID';
			$query = $this->db->query($SQL); 
			return $query->result_array(); 
		}
		//==============================================================================
		
		//==============================================================================
		// New function For Journal, contra,payment and receipts
		
		public function AccountChange($AccountID){
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'clients.company AS fullName,AccountID');
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
			$this->db->where(db_prefix() .'clients.PlantID', $PlantID);
			$clientData = $this->db->get()->row();
			if($clientData){
				return $clientData;
				}else{
				$this->db->select('CONCAT('.db_prefix() . 'staff.firstname," ",'.db_prefix() . 'staff.lastname) as fullName,AccountID');
				$this->db->from(db_prefix() . 'staff');
				$this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
				$this->db->where(db_prefix() .'staff.PlantID', $PlantID);
				$StaffData = $this->db->get()->row();
				return $StaffData;
			}
		}
		public function AccountChangeForContra($AccountID){
			$FY = $this->session->userdata('finacial_year');
			$subgroup = array('60001007','60001008','50003001');
			$PlantID = $this->session->userdata('root_company');
			$this->db->select('tblclients.company AS fullName,AccountID');
			$this->db->from(db_prefix() . 'clients');
			$this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
			$this->db->where(db_prefix() .'clients.PlantID', $PlantID);
			$this->db->where_in(db_prefix() . 'clients.SubActGroupID',$subgroup);
			$clientData = $this->db->get()->row();
			return $clientData;
		}
		
		public function get_data_general_ledger2_shipto($data_filter){
			$this->load->model('currencies_model');
			$currency = $this->currencies_model->get_base_currency();
			$acc_show_account_numbers = get_option('acc_show_account_numbers');
			
			$from_date = date('Y-04-01');
			$to_date = date('Y-m-d');
			
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			$username = $this->session->userdata('username');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$accounts_details = $this->db->get(db_prefix().'clients')->row();
			
			
			// get permission
			$this->db->where('PlantID', $selected_company);
			$this->db->where('AccountID', $data_filter['accounting_method']);
			$this->db->where('UserID', $username);
			$permission_details = $this->db->get(db_prefix().'nsaccountmaster')->row();
			
			if($accounts_details->no_show == "1" && !is_admin() && $permission_details->AccountID !== $data_filter['accounting_method']){
				return $accounts_details->no_show;
				}else{
				
				$this->db->select(db_prefix().'accountledger.*');
				// $this->db->from(db_prefix().'accountledger');
				$this->db->join(db_prefix().'salesmaster', db_prefix().'accountledger.VoucherID = '.db_prefix().'salesmaster.SalesID AND '.db_prefix().'accountledger.PlantID = '.db_prefix().'salesmaster.PlantID AND '.db_prefix().'accountledger.FY = '.db_prefix().'salesmaster.FY','LEFT');
				$this->db->join(db_prefix().'ordermaster', db_prefix().'ordermaster.OrderID = '.db_prefix().'salesmaster.OrderID AND '.db_prefix().'ordermaster.PlantID = '.db_prefix().'salesmaster.PlantID AND '.db_prefix().'ordermaster.FY = '.db_prefix().'salesmaster.FY','LEFT');
				$this->db->where(db_prefix().'accountledger.PlantID', $selected_company);
				if(isset($data_filter['accounting_method'])){
					$accounting_method = $data_filter['accounting_method'];
					$this->db->where(db_prefix().'ordermaster.AccountID2', $accounting_method);
					$this->db->where(db_prefix().'accountledger.AccountID = '.db_prefix().'ordermaster.AccountID', NULL, FALSE);
					
				}
				$this->db->LIKE(db_prefix().'accountledger.FY', $finacial_year);
				$this->db->WHERE(db_prefix().'accountledger.Transdate>=',$from_date.' 00:00:00');
				$this->db->WHERE(db_prefix().'accountledger.Transdate<=',$to_date.' 23:59:59');
				$this->db->order_by(db_prefix().'accountledger.Transdate', "asc");
				$query = $this->db->get(db_prefix().'accountledger')->result_array();
				
				return $query;
			}
			
		}
		
		
		public function GetSaleIds_shipto($data_filter)
		{
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			if(isset($data_filter['from_date'])){
				$from_date = to_sql_date($data_filter['from_date']);
			}
			
			if(isset($data_filter['to_date'])){
				$to_date = to_sql_date($data_filter['to_date']);
			}
			$this->db->select(db_prefix().'salesmaster.OrderID,'.db_prefix().'salesmaster.SalesID,'.db_prefix().'salesmaster.ChallanID');
			$this->db->where(db_prefix().'salesmaster.PlantID', $selected_company);
			$this->db->LIKE(db_prefix().'salesmaster.FY', $finacial_year);
			$this->db->WHERE(db_prefix().'salesmaster.Transdate>=',$from_date.' 00:00:00');
			$this->db->WHERE(db_prefix().'salesmaster.Transdate<=',$to_date.' 23:59:59');
			$this->db->order_by(db_prefix().'salesmaster.Transdate', "asc");
			$query = $this->db->get(db_prefix().'salesmaster')->result_array();
			return $query; 
		}
		
		public function fetchSaleIds($AccountID)
		{
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'salesmaster.SalesID,DATE_FORMAT(tblsalesmaster.TransDate, "%d/%m/%Y") AS Date,tblReconsileMaster.AccountID,tblReconsileMaster.Amount');
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->join(db_prefix().'salesmaster', db_prefix().'ReconsileMaster.TransID = '.db_prefix().'salesmaster.SalesID','INNER');
			$this->db->where(db_prefix() . 'salesmaster.AccountID', $AccountID);
			$this->db->where(db_prefix() . 'ReconsileMaster.Status', 'N');
			$this->db->where(db_prefix() . 'salesmaster.FY', $FY);
			$this->db->where(db_prefix() .'salesmaster.PlantID', $PlantID);
			$clientData = $this->db->get()->result_array();
			if($clientData){
				return $clientData;
				}else {
				return [];  
			}
		}
		public function fetchPurchaseIds($AccountID)
		{
			$FY = $this->session->userdata('finacial_year');
			$PlantID = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'purchasemaster.PurchID,DATE_FORMAT(tblpurchasemaster.Transdate, "%d/%m/%Y") AS Date,tblReconsileMaster.AccountID,tblReconsileMaster.Amount');
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->join(db_prefix().'purchasemaster', db_prefix().'ReconsileMaster.TransID = '.db_prefix().'purchasemaster.PurchID','INNER');
			$this->db->where(db_prefix() . 'purchasemaster.AccountID', $AccountID);
			$this->db->where(db_prefix() . 'ReconsileMaster.Status', 'N');
			$this->db->where(db_prefix() . 'purchasemaster.FY', $FY);
			$this->db->where(db_prefix() .'purchasemaster.PlantID', $PlantID);
			$clientData = $this->db->get()->result_array();
			if($clientData){
				return $clientData;
				}else {
				return [];  
			}
		}
		
		
		public function fetchBillinfo($AccountID,$BillID)
		{
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->where('tblReconsileMaster.EffectOn', $BillID);
			$this->db->where('tblReconsileMaster.TType', 'CR');  
			$this->db->where('tblReconsileMaster.AccountID', $AccountID);  
			$details = $this->db->get()->result_array(); 
			if($details){
				return $details;
			}
			else {
				return [];  
			}
		}
		public function fetchPurchBillinfo($AccountID,$BillID)
		{
			$this->db->from(db_prefix() . 'ReconsileMaster');
			$this->db->where('tblReconsileMaster.EffectOn', $BillID);
			$this->db->where('tblReconsileMaster.TType', 'DR');  
			$this->db->where('tblReconsileMaster.AccountID', $AccountID);  
			$details = $this->db->get()->result_array(); 
			if($details){
				return $details;
			}
			else {
				return [];  
			}
		}
		
		public function ApproveVoucherEntry($UniquID,$PassedFrom) 
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$payments_entry_detailsList = $this->GetEntryDetailsByUniqueID($UniquID,$PassedFrom);
			// First approve the voucher (moved from approve_voucher function)
			
			if (empty($payments_entry_detailsList)) {
				return false; 
				}else{
				// Update voucher status to approved
				$this->db->where('UniquID', $UniquID);
				$this->db->where('Status', 'N'); 
				$this->db->where('PassedFrom', $PassedFrom); 
				$voucher_approved = $this->db->update('tblaccountledgerPending', [
				'Status' => 'Y',
				'UserID' => $this->session->userdata('username')
				]);
				if($voucher_approved){
					$this->db->where('UniquID', $UniquID);
					$this->db->where('PassedFrom', $PassedFrom);
					$this->db->delete(db_prefix().'accountledger');
					
					foreach($payments_entry_detailsList as $key=>$val){
						$credit_data = array(
                		"PlantID" =>$val["PlantID"],
                		"Transdate" => $val["Transdate"],
                		"TransDate2" => $val["TransDate2"],
                		"VoucherID" => $val["VoucherID"],
                		'AccountID' => $val["AccountID"],
                		"EffectOn" => $val["EffectOn"],
                		"TType" => $val["TType"],
                		'Amount' => $val["Amount"],
                		'Against' => $val["Against"],
                		'BillNo' => $val["BillNo"],
                		"Narration" => $val["Narration"],
                		"PassedFrom" => $val["PassedFrom"],
                		"OrdinalNo" => $val["OrdinalNo"],
                		"UserID" => $val["UserID"],
                		"FY" => $val["FY"],
                		"UniquID" => $val["UniquID"],
                		"Status" => "Y"             
						);
						$credit_result = $this->db->insert(db_prefix().'accountledger', $credit_data);
					}
					return true;
					}else{
					return false;
				}
			}
		}
		//=========== closing Balance  =================	
		public function getclosing_balance($AccountID)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			
			$Obal = 0;
			
			$sql = '';
			$sql .= 'SELECT SUM(Amount) as dramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';
			$sql .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'" AND tblaccountledger.TType = "D"';
			$result1 = $this->db->query($sql)->row();
			
			$sql2 = '';
			$sql2 .= 'SELECT SUM(Amount) as cramt_sum,tblaccountledger.AccountID,Transdate FROM `tblaccountledger`';
			$sql2 .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountledger.PlantID = '.$selected_company.' AND tblaccountledger.FY = "'.$FY.'" AND tblaccountledger.TType = "C"';
			$result2 = $this->db->query($sql2)->row();
			
			$sql3 = '';
			$sql3 .= 'SELECT BAL1 FROM `tblaccountbalances`';
			$sql3 .= ' WHERE  AccountID = "'.$AccountID.'" AND tblaccountbalances.PlantID = '.$selected_company.' AND tblaccountbalances.FY = "'.$FY.'"';
			$result3 = $this->db->query($sql3)->row();
			if(empty($result3)){			
				}else{
				$Obal = $result3->BAL1;
			}
			$closing_balance = $Obal + $result1->dramt_sum - $result2->cramt_sum;
			return $closing_balance;
		}	
		
		
		public function get_subgroup_for_accounting_head1()
		{
			$ss = 'SELECT * FROM tblaccountgroupssub1 ';
			$result_data = $this->db->query($ss)->result_array();
			return $result_data;
		}
		
		//--------------- Journal Entry New --------------------------
		
		public function add_journal_entryNew($data){
			$journal_entry = json_decode($data['journal_entry']);
			unset($data['journal_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$journal_date = to_sql_date($data['journal_date'])." ".date('H:i:s');
			$date = to_sql_date($data['journal_date']);
			$month = substr($journal_date,5,2);
			$get_result_to_cur_date = $this->get_result_to_cur_date_journal($date);
			
			if(empty($get_result_to_cur_date)){
				$selected_company = $this->session->userdata('root_company');
				if($selected_company == 1){
					$new_journalNumber = get_option('next_journal_number_for_cspl');
					}elseif($selected_company == 2){
					$new_journalNumber = get_option('next_journal_number_for_cff');
					}elseif($selected_company == 3){
					$new_journalNumber = get_option('next_journal_number_for_cbu');
					}elseif($selected_company == 4){
					$new_journalNumber = get_option('next_journal_number_for_cbupl');
				}
				
				$new_voucher_number = $new_journalNumber;
				} else { 
				$count = count($get_result_to_cur_date);
				$last_index = $count - 1;
				$new_voucher_number = $get_result_to_cur_date[$last_index]['VoucherID'];
				
				$incNo = (int) $new_voucher_number - 1;
				$sql = 'UPDATE tblaccountledger SET VoucherID = abs(VoucherID) + 1 where abs(VoucherID) > "'.$incNo.'" AND PassedFrom = "JOURNAL" AND FY = "'.$fy.'" AND PlantID = '.$selected_company;
				$this->db->query($sql);
				if ($this->db->affected_rows() > 0) {
					$this->increment_next_journal_number();
				}
			}
			
			$i = 1;
			foreach($journal_entry as $key => $value){			
				if(isset($value[0]) && $value[0] != ''){				
					$amount = 0;
					$ttype = '';
					
					$dr_cr = strtoupper($value[6]);  
					
					if($dr_cr == 'D' && isset($value[7]) && $value[7] != '' && floatval($value[7]) > 0){
						$amount = floatval($value[7]);  
						$ttype = 'D';
						} elseif($dr_cr == 'C' && isset($value[8]) && $value[8] != '' && floatval($value[8]) > 0){
						$amount = floatval($value[8]);  
						$ttype = 'C';
					}
					
					if($amount > 0 && $ttype != ''){
						$_data_detail = array(
						"PlantID" => $selected_company,
						"Transdate" => $journal_date,
						"TransDate2" => date('Y-m-d H:i:s'),
						"VoucherID" => $new_voucher_number,
						"AccountID" => $value[0],  
						"EffectOn" => NULL, 
						"TType" => $ttype,  
						"Against" => $value[3],  
						"BillNo" => $value[4],  
						"Amount" => $amount,
						"Narration" => $value[9],  
						"PassedFrom" => "JOURNAL",
						"OrdinalNo" => $i,
						"flag" =>2,
						"UserID" => $this->session->userdata('username'),
						"FY" => $fy,
						);
						$this->db->insert(db_prefix().'accountledger', $_data_detail);
						$i++;
					}
				}
			}
			
			if(empty($get_result_to_cur_date)){
				$this->increment_next_journal_number();
			}
			return true;
		}
		
		//------- update ----
		public function update_journal_entryNew($data,$id){
			$journal_entry = json_decode($data['journal_entry']);
			unset($data['journal_entry']);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$journal_date = to_sql_date($data['journal_date1'])." ".date('H:i:s');
			$journal_details = $this->get_journal_entry_details($id);
			
			// Delete previous ledger details
			foreach ($journal_details as $key => $value) {
				$ledger_audit = array(
				"PlantID"=>$value["PlantID"],
				"FY"=>$value["FY"],
				"Transdate"=>$value["Transdate"],
				"TransDate2"=>$value["TransDate2"],
				"VoucherID"=>$value["VoucherID"],
				"AccountID"=>$value["AccountID"],
				"TType"=>$value["TType"],
				"Amount"=>$value["Amount"],
				"Narration"=>$value["Narration"],
				"PassedFrom"=>$value["PassedFrom"],
				"flag" =>2,
				"OrdinalNo"=>$value["OrdinalNo"],
				"UserID"=>$value["UserID"],
				"Lupdate"=>date('Y-m-d H:i:s'),
				"UserID2"=>$this->session->userdata('username')
				);
				$this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
			}
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$this->db->where('VoucherID', $id);
			$this->db->delete(db_prefix() . 'accountledger');
			
			$i = 1;
			foreach ($journal_entry as $key => $value) {
				
				if(isset($value[0]) && $value[0] != ''){
					
					
					$amount = 0;
					$ttype = '';
					
					$dr_cr = strtoupper($value[6]);  
					
					if($dr_cr == 'D' && isset($value[7]) && $value[7] != '' && floatval($value[7]) > 0){
						$amount = floatval($value[7]);  
						$ttype = 'D';
						} elseif($dr_cr == 'C' && isset($value[8]) && $value[8] != '' && floatval($value[8]) > 0){
						$amount = floatval($value[8]);  
						$ttype = 'C';
					}
					
					if($amount > 0 && $ttype != ''){
						$_data_detail = array(
						"PlantID" => $selected_company,
						"Transdate" => $journal_date,
						"TransDate2" => date('Y-m-d H:i:s'),
						"VoucherID" => $id,
						"AccountID" => $value[0],  
						"EffectOn" => NULL,  
						"flag" =>2,	
						"TType" => $ttype,  
						"Against" => $value[3],  
						"BillNo" => $value[4],  
						"Amount" => $amount,
						"Narration" => $value[9],  
						"PassedFrom" => "JOURNAL",
						"OrdinalNo" => $i,
						"UserID" => $this->session->userdata('username'),
						"FY" => $fy,
						);
						
						$this->db->insert(db_prefix().'accountledger', $_data_detail);
						$i++;
					}
				}
			}
			
			return true;
		}
		
		
		public function get_journal_entryNew($id){
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$this->db->order_by('OrdinalNo', "ASC");  
			$journal_entrie = $this->db->get(db_prefix() . 'accountledger')->row();
			
			if(!$journal_entrie){
				return null; 
			}
			
			$this->db->where('PlantID', $selected_company);
			$this->db->LIKE('FY', $fy);
			$this->db->where('VoucherID', $id);
			$this->db->LIKE('PassedFrom', "JOURNAL");
			$this->db->order_by('OrdinalNo', "ASC");
			$journal_data = $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			$data_details =[];
			$total_debit = '';
			$total_credit = '';
			
			foreach ($journal_data as $key => $value) {
				
				$closingBalance = $this->getclosing_balance($value['AccountID']);
				
				$total_pending_amt = 0;  
				
				if($value['BillNo'] != '' && $value['BillNo'] != null) {
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('TransID', $value['BillNo']);
					$this->db->where('TType', 'CR'); 
					$this->db->where('PassedFrom', 'PURCHASE'); 
					$this->db->where('AccountID', $value['AccountID']); 
					$Purch = $this->db->get()->row();
					
					$this->db->from(db_prefix() . 'ReconsileMaster');
					$this->db->where('TransID', $value['BillNo']);
					$this->db->where('TType', 'DR'); 
					$this->db->where('PassedFrom', 'SALE'); 
					$this->db->where('AccountID', $value['AccountID']); 
					$Sale = $this->db->get()->row();
					
					if(!empty($Purch)){
						$creditedamt = $Purch->Amount;
						
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'D'); 
						$this->db->where('flag', '2'); 
						$this->db->where('AccountID', $value['AccountID']); 
						$details_debit = $this->db->get()->result_array(); 
						$DebitAmount = array_sum(array_column($details_debit, 'Amount'));
						
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'C'); 
						$this->db->where('flag', '2');
						$this->db->where('AccountID', $value['AccountID']); 
						$details_credit = $this->db->get()->result_array(); 
						$CreditAmount = array_sum(array_column($details_credit, 'Amount'));
						
						$diff = $DebitAmount - $CreditAmount;
						$total_pending_amt = $creditedamt - $diff;
						
						if($value['TType'] == 'D'){
							$total_pending_amt = $total_pending_amt + $value['Amount'];
							}else{
							$total_pending_amt = $total_pending_amt - $value['Amount'];
						}
					}
					
					if(!empty($Sale)){
						$DebitAmt = $Sale->Amount;
						
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'D'); 
						$this->db->where('flag', '2');
						$this->db->where('AccountID', $value['AccountID']); 
						$details_debit_sale = $this->db->get()->result_array(); 
						$DebitAmount = array_sum(array_column($details_debit_sale, 'Amount'));
						
						$this->db->from(db_prefix() . 'accountledger');
						$this->db->where('BillNo', $value['BillNo']);
						$this->db->where('TType', 'C'); 
						$this->db->where('flag', '2');
						$this->db->where('AccountID', $value['AccountID']); 
						$details_credit_sale = $this->db->get()->result_array(); 
						$CreditAmount = array_sum(array_column($details_credit_sale, 'Amount'));
						
						$diff = $CreditAmount - $DebitAmount;
						$total_pending_amt = $DebitAmt - $diff;
						
						if($value['TType'] == 'D'){
							$total_pending_amt = $total_pending_amt - $value['Amount'];
							}else{
							$total_pending_amt = $total_pending_amt + $value['Amount'];
						}
					}
				}
				
				$debit_amt = '';
				$credit_amt = '';
				if(strtoupper($value['TType']) == 'D'){
					$debit_amt = $value['Amount'];
					$total_debit = $total_debit + $debit_amt;
					} else {
					$credit_amt = $value['Amount'];
					$total_credit = $total_credit + $credit_amt;
				}
				
				
				$data_details[] = [
				"AccountID"     => $value['AccountID'],
				"company"       => $value['AccountID'],
				"ClosingBalance"=> $closingBalance,
				"against"       => $value['Against'],
				"bill"          => $value['BillNo'],
				"pendingAmt"    => $total_pending_amt,
				"dr_cr"         => strtoupper($value['TType']),
				"debit"         => $debit_amt,
				"credit"        => $credit_amt,
				"description"   => $value['Narration']
				];
				
			}
			
			$journal_entrie->details = $data_details;
			
			$journal_entrie->damt = $total_debit;
			$journal_entrie->camt = $total_credit;
			
			unset($journal_entrie->TotalAmt);
			unset($journal_entrie->TransType);
			unset($journal_entrie->MainAccount);
			
			return $journal_entrie;
		}
		
		//____________________ Account dashboard ________________________________	
		
		
		
		
		public function GetBillsReceivableDueCalendarData($filterdata)
		{  
			$month_input = $filterdata['Month']; // Example: '2024-11'
			$date = $month_input.'-01';//your given date
			$first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			$first_date = date("Y-m-d",$first_date_find);
			
			$last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			$last_date = date("Y-m-d",$last_date_find);
			
			$from_date = $first_date . " 00:00:00";
			$to_date = $last_date . " 23:59:59";
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql = "
			SELECT 
			DATE(tblaccountledger.Transdate) AS TransDate,
			SUM(tblaccountledger.Amount) AS TotalAmount
			FROM 
			tblaccountledger
			WHERE 
			tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'
			AND tblaccountledger.PassedFrom = 'RECEIPTS'
			AND tblaccountledger.TType = 'C'
			AND tblaccountledger.FY = '$fy'
			AND tblaccountledger.PlantID = '$selected_company'
			GROUP BY 
			DATE(tblaccountledger.Transdate)
			ORDER BY 
			tblaccountledger.Transdate ASC
			";
			
			return $this->db->query($sql)->result_array();
		}	
		
		
		public function GetBillsPayableDueCalendarData($filterdata)
		{  
			$month_input = $filterdata['Month'];  
			$date = $month_input.'-01'; 
			$first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
			$first_date = date("Y-m-d",$first_date_find);
			
			$last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
			$last_date = date("Y-m-d",$last_date_find);
			
			$from_date = $first_date . " 00:00:00";
			$to_date = $last_date . " 23:59:59";
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql = "
			SELECT 
			DATE(tblaccountledger.Transdate) AS TransDate,
			SUM(tblaccountledger.Amount) AS TotalAmount
			FROM 
			tblaccountledger
			WHERE 
			tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'
			AND tblaccountledger.PassedFrom = 'PAYMENTS'
			AND tblaccountledger.TType = 'D'
			AND tblaccountledger.FY = '$fy'
			AND tblaccountledger.PlantID = '$selected_company'
			GROUP BY 
			DATE(tblaccountledger.Transdate)
			ORDER BY 
			tblaccountledger.Transdate ASC
			";
			
			return $this->db->query($sql)->result_array();
		}
		
		
		public function JournalEntryAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]) . " 00:00:00";
			$to_date   = to_sql_date($filterdata["to_date"]) . " 23:59:59";
			
			$sql = "
			SELECT 
			COUNT(DISTINCT tblaccountledger.VoucherID) AS JournalEntryAmt
			FROM 
			tblaccountledger
			WHERE 
			tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'
			AND tblaccountledger.PassedFrom = 'JOURNAL'
			AND tblaccountledger.TType = 'D'
			AND tblaccountledger.FY = '$fy'
			AND tblaccountledger.PlantID = '$selected_company'
			";
			
			$result = $this->db->query($sql)->row();
			return $result ? $result->JournalEntryAmt : 0;
		}
		
		public function ContraEntryAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]) . " 00:00:00";
			$to_date   = to_sql_date($filterdata["to_date"]) . " 23:59:59";
			
			$sql = "
			SELECT 
			COUNT(DISTINCT tblaccountledger.VoucherID) AS ContraEntryAmt
			FROM 
			tblaccountledger
			WHERE 
			tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'
			AND tblaccountledger.PassedFrom = 'CONTRA'
			AND tblaccountledger.TType = 'D'
			AND tblaccountledger.FY = '$fy'
			AND tblaccountledger.PlantID = '$selected_company'
			";
			
			$result = $this->db->query($sql)->row();
			return $result ? $result->ContraEntryAmt : 0;
		}
		
		public function ReceiptEntryAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]) . " 00:00:00";
			$to_date   = to_sql_date($filterdata["to_date"]) . " 23:59:59";
			
			$sql = "SELECT 
			COUNT(DISTINCT tblaccountledger.VoucherID) AS ReceiptEntryAmt
			FROM 
			tblaccountledger
			WHERE 
			tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'
			AND tblaccountledger.PassedFrom = 'RECEIPTS'
			AND tblaccountledger.TType = 'D'
			AND tblaccountledger.FY = '$fy'
			AND tblaccountledger.PlantID = '$selected_company'
			";
			
			$result = $this->db->query($sql)->row();
			return $result ? $result->ReceiptEntryAmt : 0;
		}
		
		public function PaymentEntryAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]) . " 00:00:00";
			$to_date   = to_sql_date($filterdata["to_date"]) . " 23:59:59";
			
			$sql = "
			SELECT 
			COUNT(DISTINCT tblaccountledger.VoucherID) AS PaymentEntryAmt
			FROM 
			tblaccountledger
			WHERE 
			tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'
			AND tblaccountledger.PassedFrom = 'PAYMENTS'
			AND tblaccountledger.TType = 'D'
			AND tblaccountledger.FY = '$fy'
			AND tblaccountledger.PlantID = '$selected_company'
			";
			
			$result = $this->db->query($sql)->row();
			return $result ? $result->PaymentEntryAmt : 0;
		}
		
		
		
		//-------*********************** Creditors Outstanding  ***************************------------   
		
		public function get_monthly_payable_amounts()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = "20".$fy."-04-01";
			$to_date = "20".($fy + 1)."-03-31";
			
			// Step 1: Get Purchases with clients (same as GetBillsPayableBodyData)
			$sql = 'SELECT pm.*, 
            c.company, 
            COALESCE(c.credit_limit,0) AS MaxDays
            FROM '.db_prefix().'purchasemaster pm
            INNER JOIN tblclients c ON c.AccountID = pm.AccountID
            WHERE pm.cur_status="Completed" 
            AND pm.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"
            AND c.SubActGroupID1 IN("100023")';
			
			$result = $this->db->query($sql)->result_array();
			
			if (empty($result)) {
				return $this->_generate_empty_monthly_data($from_date, $to_date);
			}
			
			// Step 2: Collect PurchIDs for ledger query
			$purchIDs = array_column($result, 'PurchID');
			$inPurchIDs = implode('","', $purchIDs);
			
			// Step 3: Aggregate ledger amounts (same as GetBillsPayableBodyData)
			$ledgerSql = '
			SELECT BillNo,
			SUM(CASE WHEN TType="D" AND PassedFrom="PAYMENTS"   THEN Amount ELSE 0 END) AS PaidAmt,
			SUM(CASE WHEN TType="D" AND PassedFrom="PURCHASERTN" THEN Amount ELSE 0 END) AS PurchRtnAmt,
			SUM(CASE WHEN TType="D" AND PassedFrom="CDNOTE"      THEN Amount ELSE 0 END) AS DebitNoteAmt,
			SUM(CASE WHEN TType="D" AND PassedFrom="JOURNAL"     THEN Amount ELSE 0 END) AS JournalDebitAmt,
			SUM(CASE WHEN TType="C" AND PassedFrom="JOURNAL"     THEN Amount ELSE 0 END) AS JournalCreditAmt
			FROM tblaccountledger
			WHERE BillNo IN ("'.$inPurchIDs.'")
			GROUP BY BillNo
			';
			
			$ledgerData = $this->db->query($ledgerSql)->result_array();
			
			// Map ledger by PurchID
			$ledgerMap = [];
			foreach ($ledgerData as $row) {
				$ledgerMap[$row['BillNo']] = $row;
			}
			
			// Step 4: Process data and calculate monthly due amounts
			$monthlyTotalDue = [];
			$monthlyOverdue = [];
			$monthlyNonOverdue = [];
			
			foreach ($result as $value) {
				$pid = $value['PurchID'];
				
				// Get ledger data
				$led = isset($ledgerMap[$pid]) ? $ledgerMap[$pid] : [
				'PaidAmt' => 0, 
				'PurchRtnAmt' => 0, 
				'DebitNoteAmt' => 0,
				'JournalDebitAmt' => 0, 
				'JournalCreditAmt' => 0
				];
				
				// Calculate JournalAmt and DueAmt (same as your report)
				$JournalAmt = $led['JournalDebitAmt'] - $led['JournalCreditAmt'];
				$dueAmt = $value["Invamt"] - $led["PaidAmt"] - $led["PurchRtnAmt"] - $led["DebitNoteAmt"] - $JournalAmt;
				
				// Skip if no due amount
				if ($dueAmt <= 0) {
					continue;
				}
				
				$transdate = $value["Transdate"];
				$month_label = date('M-Y', strtotime($transdate));
				
				// Calculate overdue days (same as your report)
				$paymentTerm = $value["MaxDays"];
				$nextDateTimestamp = strtotime($transdate . " + $paymentTerm days");
				$currentTimestamp = time();
				$differenceInSeconds = $currentTimestamp - $nextDateTimestamp;
				$overdueDays = ceil($differenceInSeconds / (60 * 60 * 24));
				
				// Add to monthly total due
				if (!isset($monthlyTotalDue[$month_label])) {
					$monthlyTotalDue[$month_label] = 0;
				}
				$monthlyTotalDue[$month_label] += $dueAmt;
				
				// Categorize as overdue or non-overdue
				if ($overdueDays > 0) {
					if (!isset($monthlyOverdue[$month_label])) {
						$monthlyOverdue[$month_label] = 0;
					}
					$monthlyOverdue[$month_label] += $dueAmt;
					} else {
					if (!isset($monthlyNonOverdue[$month_label])) {
						$monthlyNonOverdue[$month_label] = 0;
					}
					$monthlyNonOverdue[$month_label] += $dueAmt;
				}
			}
			
			// Step 5: Generate all months in range
			$start = DateTime::createFromFormat('Y-m-d', $from_date);
			$end = DateTime::createFromFormat('Y-m-d', $to_date);
			
			$allMonths = [];
			$current = clone $start;
			$current->modify('first day of this month');
			
			while ($current <= $end) {
				$monthKey = $current->format('M-Y');
				$allMonths[$monthKey] = [
				'total_due' => isset($monthlyTotalDue[$monthKey]) ? (float)$monthlyTotalDue[$monthKey] : 0,
				'overdue' => isset($monthlyOverdue[$monthKey]) ? (float)$monthlyOverdue[$monthKey] : 0,
				'non_overdue' => isset($monthlyNonOverdue[$monthKey]) ? (float)$monthlyNonOverdue[$monthKey] : 0
				];
				$current->modify('+1 month');
			}
			
			// Step 6: Prepare chart data
			$months = array_keys($allMonths);
			$totalDueData = array_column($allMonths, 'total_due');
			$overdueData = array_column($allMonths, 'overdue');
			$nonOverdueData = array_column($allMonths, 'non_overdue');
			
			return [
			'Creditors_Months' => $months,
			'CreditOut_Amt' => [
            [
			'name' => 'Total Creditors Outstanding',
			'data' => $totalDueData
            ],
            [
			'name' => 'Overdue Amount',
			'data' => $overdueData
            ],
            [
			'name' => 'Non-Overdue Amount',
			'data' => $nonOverdueData
            ]
			]
			];
		}
		
		// Helper function for empty data
		private function _generate_empty_monthly_data($from_date, $to_date)
		{
			$start = DateTime::createFromFormat('Y-m-d', $from_date);
			$end = DateTime::createFromFormat('Y-m-d', $to_date);
			
			$allMonths = [];
			$current = clone $start;
			$current->modify('first day of this month');
			
			while ($current <= $end) {
				$monthKey = $current->format('M-Y');
				$allMonths[$monthKey] = 0;
				$current->modify('+1 month');
			}
			
			return [
			'Creditors_Months' => array_keys($allMonths),
			'CreditOut_Amt' => [[
            'name' => 'Creditors Outstanding',
            'data' => array_values($allMonths)
			]]
			];
		}
		
		
		//-------*********************** Debtors Outstanding ***************************------------           
		
		
		
		public function getMonthly_Due_amounts()
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = "20".$fy."-04-01";
			$to_date = "20".($fy + 1)."-03-31";
			
			// Use the EXACT same query as GetBillsReceivableBodyData
			$sql = 'SELECT 
			s.*,
			c.company,
			COALESCE(c.credit_days, 0) AS MaxDays,
			COALESCE(al.PaidAmt, 0) AS PaidAmt,
			COALESCE(al2.CDNoteAmt, 0) AS CDNoteAmt,
			COALESCE(al3.SaleRtnAmt, 0) AS SaleRtnAmt,
			COALESCE(al5.JournalDebitAmt, 0) AS JournalDebitAmt,
			COALESCE(al4.JournalCreditAmt, 0) AS JournalCreditAmt
			FROM 
			tblsalesmaster s
			INNER JOIN 
			tblclients c ON c.AccountID = s.AccountID
			LEFT JOIN 
			(SELECT AccountID,BillNo, SUM(Amount) AS PaidAmt 
			FROM tblaccountledger 
			WHERE TType = "C" AND PassedFrom = "RECEIPTS" 
			GROUP BY BillNo, AccountID) al 
			ON al.BillNo = s.SalesID AND al.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS CDNoteAmt FROM tblaccountledger WHERE TType = "C" AND PassedFrom ="CDNOTE" GROUP BY BillNo, AccountID) al2 ON al2.BillNo = s.SalesID AND al2.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS SaleRtnAmt FROM tblaccountledger WHERE TType = "C" AND PassedFrom ="SALESRTN" GROUP BY BillNo, AccountID) al3 ON al3.BillNo = s.SalesID AND al3.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS JournalCreditAmt FROM tblaccountledger WHERE TType = "C" AND PassedFrom ="JOURNAL" GROUP BY BillNo, AccountID) al4 ON al4.BillNo = s.SalesID AND al4.AccountID = s.AccountID
			LEFT JOIN (SELECT AccountID,BillNo, SUM(Amount) AS JournalDebitAmt FROM tblaccountledger WHERE TType = "D" AND PassedFrom ="JOURNAL" GROUP BY BillNo, AccountID) al5 ON al5.BillNo = s.SalesID AND al5.AccountID = s.AccountID
			WHERE 
			s.FY = '.$fy.' 
			AND s.PlantID = '.$selected_company.'
			AND s.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59"';
			
			$result = $this->db->query($sql)->result_array();
			
			$monthlyOverdue = [];
			
			foreach ($result as $value) {
				// Calculate due amount (EXACT same formula)
				$dueAmt = $value["RndAmt"] - $value["PaidAmt"] - $value["CDNoteAmt"] - $value["SaleRtnAmt"] - ($value["JournalCreditAmt"] - $value["JournalDebitAmt"]);
				
				// Skip if no due amount
				if ($dueAmt <= 0) {
					continue;
				}
				
				// Calculate overdue days (EXACT same logic)
				$transdate = $value["Transdate"];
				$paymentTerm = $value["MaxDays"];
				$nextDateTimestamp = strtotime($transdate . " + $paymentTerm days");
				$currentTimestamp = time();
				$overdueDays = ceil(($currentTimestamp - $nextDateTimestamp) / (60 * 60 * 24));
				
				// Only include if overdue (like ReportType = "Overdue")
				if ($overdueDays > 0) {
					$month_label = date('M-Y', strtotime($transdate));
					
					if (!isset($monthlyOverdue[$month_label])) {
						$monthlyOverdue[$month_label] = 0;
					}
					$monthlyOverdue[$month_label] += $dueAmt;
				}
			}
			
			// Generate all months in range
			$start = DateTime::createFromFormat('Y-m-d', $from_date);
			$end = DateTime::createFromFormat('Y-m-d', $to_date);
			
			$allMonths = [];
			$current = clone $start;
			$current->modify('first day of this month');
			
			while ($current <= $end) {
				$monthKey = $current->format('M-Y');
				$allMonths[$monthKey] = isset($monthlyOverdue[$monthKey]) ? (float)$monthlyOverdue[$monthKey] : 0;
				$current->modify('+1 month');
			}
			
			return [
			'Debtors_Months' => array_keys($allMonths),
			'DebtOut_Amt' => [[
            'name' => 'Overdue Amount',
            'data' => array_values($allMonths)
			]]
			];
		}
		
		
		
		
		
		//*********************  Get current closing balances ***************
		
		
		public function getMonthly_ClosingBal() {
			$selected_company = $this->session->userdata('root_company');
			$finacial_year = $this->session->userdata('finacial_year');
			$fy_start_date = '20' . $finacial_year . '-04-01';
			
			/*  $from_date = to_sql_date($filterdata['from_date']);
			$to_date = to_sql_date($filterdata['to_date']); */
			$from_date = "20".$finacial_year."-04-01";
			$to_date = "20".($finacial_year + 1)."-03-31";
			
			// --- 1. Get All Cash Accounts ---
			$this->db->select('AccountID, company as AccountName');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('SubActGroupID', '1000001');
			$cash_accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			
			if (empty($cash_accounts)) {
				return array(); 
			}
			
			$account_ids = array_column($cash_accounts, 'AccountID');
			$accounts_map = array_column($cash_accounts, 'AccountName', 'AccountID');
			
			// --- 2. Get FY Opening Balances for ALL accounts  ---
			$this->db->select('AccountID, BAL1');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $finacial_year);
			$this->db->where_in('AccountID', $account_ids);
			$balances_result = $this->db->get(db_prefix() . 'accountbalances')->result();
			
			//   FY opening balance
			$running_balances = array_fill_keys($account_ids, 0);
			foreach ($balances_result as $row) {
				$running_balances[$row->AccountID] = (float) $row->BAL1;
			}
			
			// --- 3. Get Pre-Report Transactions to calculate initial opening balance  ---
			
			$pre_report_end_date = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
			
			if ($from_date > $fy_start_date) {
				$this->db->select('AccountID, TType, SUM(Amount) as Total');
				$this->db->where('PlantID', $selected_company);
				$this->db->like('FY', $finacial_year);
				$this->db->where_in('AccountID', $account_ids);
				$this->db->where('Transdate >=', $fy_start_date . ' 00:00:00');
				$this->db->where('Transdate <=', $pre_report_end_date . ' 23:59:59');
				$this->db->group_by(array('AccountID', 'TType'));
				$pre_report_txs = $this->db->get(db_prefix() . 'accountledger')->result();
				
				// opening balance for the report's start date
				foreach ($pre_report_txs as $tx) {
					if ($tx->TType == 'D') {
						$running_balances[$tx->AccountID] += (float) $tx->Total;
						} else if ($tx->TType == 'C') {
						$running_balances[$tx->AccountID] -= (float) $tx->Total;
					}
				}
			}
			
			// --- 4. Get All Transactions for the ENTIRE Report Period ---
			$this->db->select('AccountID, Transdate, TType, Amount');
			$this->db->where('PlantID', $selected_company);
			$this->db->like('FY', $finacial_year);
			$this->db->where_in('AccountID', $account_ids);
			$this->db->where('Transdate >=', $from_date . ' 00:00:00');
			$this->db->where('Transdate <=', $to_date . ' 23:59:59');
			$report_txs = $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			
			// 5a. Generate month "buckets" for the report
			$start = new DateTime($from_date);
			$end = new DateTime($to_date);
			$end->modify('first day of next month'); // Include the last month
			$interval = new DateInterval('P1M');
			$period = new DatePeriod($start, $interval, $end);
			
			$report_months = array(); // e.g., ['2023-04' => 'Apr 2023', ...]
			$monthly_net_change = array(); // e.g., [$account_id]['2023-04'] = 0
			
			foreach ($period as $dt) {
				$month_key = $dt->format('Y-m');
				$report_months[$month_key] = $dt->format('M Y');
				
				// Initialize net change buckets for all accounts for this month
				foreach ($account_ids as $account_id) {
					$monthly_net_change[$account_id][$month_key] = 0;
				}
			}
			
			// 5b. Bucket all transactions into their respective month's net change
			foreach ($report_txs as $tx) {
				$month_key = date('Y-m', strtotime($tx['Transdate']));
				$account_id = $tx['AccountID'];
				
				// Ensure the transaction's month is in our report range (it should be)
				if (isset($monthly_net_change[$account_id][$month_key])) {
					if ($tx['TType'] == 'D') {
						$monthly_net_change[$account_id][$month_key] += (float) $tx['Amount'];
						} else if ($tx['TType'] == 'C') {
						$monthly_net_change[$account_id][$month_key] -= (float) $tx['Amount'];
					}
				}
			}
			
			// 5c. Build the final data structure by calculating the running total
			$monthly_data = array();
			
			foreach ($account_ids as $account_id) {
				$account_balances = array();
				
				// opening balance for the first month
				$month_opening_balance = $running_balances[$account_id]; 
				
				foreach ($report_months as $month_key => $month_label) {
					
					$net_change_for_month = $monthly_net_change[$account_id][$month_key];
					
					// Closing balance is opening + this month's change
					$month_closing_balance = $month_opening_balance + $net_change_for_month;
					
					$account_balances[] = array(
					'month' => $month_label,
					'balance' => $month_closing_balance
					);
					
					//  closing balance of this month is the opening balance for the next
					$month_opening_balance = $month_closing_balance;
				}
				
				$monthly_data[] = array(
				'name' => $accounts_map[$account_id],
				'data' => $account_balances
				);
			}
			
			return $monthly_data;
		}
		
		
		
		// ************************* Total Sales, Total Purchase, Closing Stock ****************************************
		
		public function getMonthly_Sale_Purchase()
		{
			
			$selected_company = $this->session->userdata('root_company');
			$fy = $this->session->userdata('finacial_year');
			
			/* $from_date = to_sql_date($filterdata['from_date']);
			$to_date = to_sql_date($filterdata['to_date']); */
			$from_date = "20".$fy."-04-01";
			$to_date = "20".($fy + 1)."-03-31";
			
			$wherePurchase = "";
			$whereSale = "";
			if (!empty($from_date) && !empty($to_date)) {
				$wherePurchase = "AND Transdate BETWEEN '$from_date' AND '$to_date'";
				$whereSale = "AND Transdate BETWEEN '$from_date' AND '$to_date'";
			}
			
			// Monthly Purchase
			$purchaseQuery = $this->db->query("
			SELECT 
            DATE_FORMAT(Transdate, '%Y-%m') AS month,
            SUM(Purchamt) AS total_purchase
			FROM tblpurchasemaster
			WHERE Transdate IS NOT NULL $wherePurchase
			GROUP BY DATE_FORMAT(Transdate, '%Y-%m')
			ORDER BY month ASC
			")->result_array();
			
			// Monthly Sales
			$salesQuery = $this->db->query("
			SELECT 
            DATE_FORMAT(Transdate, '%Y-%m') AS month,
            SUM(BillAmt) AS total_sale
			FROM tblsalesmaster
			WHERE Transdate IS NOT NULL $whereSale
			GROUP BY DATE_FORMAT(Transdate, '%Y-%m')
			ORDER BY month ASC
			")->result_array();
			
			// Combine data for Highcharts
			$months = [];
			foreach ($purchaseQuery as $p) {
				$months[$p['month']] = $p['month'];
			}
			foreach ($salesQuery as $s) {
				$months[$s['month']] = $s['month'];
			}
			ksort($months);
			
			$purchaseData = [];
			$salesData = [];
			
			foreach ($months as $month) {
				$purchase = 0;
				$sale = 0;
				foreach ($purchaseQuery as $p) {
					if ($p['month'] == $month) {
						$purchase = $p['total_purchase'];
						break;
					}
				}
				foreach ($salesQuery as $s) {
					if ($s['month'] == $month) {
						$sale = $s['total_sale'];
						break;
					}
				}
				$purchaseData[] = (float)$purchase;
				$salesData[] = (float)$sale;
			}
			
			return [
			'months' => array_values($months),
			'series' => [
            [
			'name' => 'Total Purchase',
			'data' => $purchaseData,
			'type' => 'line'
            ],
            [
			'name' => 'Total Sale',
			'data' => $salesData,
			'type' => 'line'
            ]
			]
			];
		}
		
		
		// ************************* Stock value *****************************************
		
		
		
		/* 
			
			public function getMonthly_ClosingStock($filterdata)
			{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			
			// Initialize result array
			$monthly_data = array(); 
			
			// Create date range by months
			$start = new DateTime($from_date);
			$end = new DateTime($to_date);
			$interval = new DateInterval('P1M');
			$period = new DatePeriod($start, $interval, $end);
			
			foreach ($period as $dt) {
			$month_start = $dt->format('Y-m-01');
			$month_end = $dt->format('Y-m-t');
			
			// Get stock value for this month
			$month_stock_value = $this->calculateMonthlyStockValue($selected_company, $fy, $month_start, $month_end);
			
			$monthly_data[] = array(
            'month' => $dt->format('M Y'),
            'month_start' => $month_start,
            'month_end' => $month_end,
            'closing_stock_value' => $month_stock_value
			);
			}
			
			return $monthly_data;
			}
			
			private function calculateMonthlyStockValue($selected_company, $fy, $month_start, $month_end)
			{
			$stockValue_sum = 0;
			
			// Get all items for the company (you might want to add item_group filter here)
			$this->db->select('item_code, case_qty, description, unit');
			$this->db->where('PlantID', $selected_company);
			$AllItemList = $this->db->get('tblitems')->result_array();
			
			foreach ($AllItemList as $item) {
			// Calculate opening stock for the month
			$opening_qty = $this->getOpeningQtyForMonth($selected_company, $fy, $item['item_code'], $month_start);
			
			// Get transactions for the month
			$month_transactions = $this->getMonthTransactions($selected_company, $fy, $item['item_code'], $month_start, $month_end);
			
			// Calculate closing quantity for the month
			$closing_qty = $this->calculateClosingQty($opening_qty, $month_transactions);
			
			// Get item rate
			$rate = $this->getItemRate($selected_company, $item['item_code']);
			
			// Calculate stock value
			$case_qty = ($item["case_qty"] == "0" || $item["case_qty"] == "") ? 1 : $item["case_qty"];
			$stock_qty = round($closing_qty) * $case_qty;
			$stock_value = $stock_qty * $rate;
			
			$stockValue_sum += $stock_value;
			}
			
			return $stockValue_sum;
			}
			
			private function getOpeningQtyForMonth($selected_company, $fy, $item_code, $month_start)
			{
			$fy_start = '20'.$fy.'-04-01';
			
			if ($month_start == $fy_start) {
			// Get opening stock from stock master
			$this->db->select('OQty');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('ItemID', $item_code);
			$this->db->where('cnfid', '1');
			$result = $this->db->get('tblstockmaster')->row();
			
			return $result ? floatval($result->OQty) : 0;
			} else {
			// Calculate opening stock from history
			$day_before = date('Y-m-d', strtotime($month_start . ' -1 day'));
			
			$this->db->select('TType, TType2, SUM(BilledQty) as billsum');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('ItemID', $item_code);
			$this->db->where('TransDate2 BETWEEN "'.$fy_start.' 00:00:00" AND "'.$day_before.' 23:59:59"');
			$this->db->where('BillID IS NOT NULL');
			$this->db->group_by('TType, TType2');
			$transactions = $this->db->get('tblhistory')->result_array();
			
			// Get initial opening stock
			$this->db->select('OQty');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('ItemID', $item_code);
			$this->db->where('cnfid', '1');
			$opening_stock = $this->db->get('tblstockmaster')->row();
			$opening_qty = $opening_stock ? floatval($opening_stock->OQty) : 0;
			
			// Adjust opening quantity based on transactions
			foreach ($transactions as $transaction) {
            $opening_qty = $this->adjustQuantityByTransactionType($opening_qty, $transaction);
			}
			
			return $opening_qty;
			}
			}
			
			private function getMonthTransactions($selected_company, $fy, $item_code, $month_start, $month_end)
			{
			$this->db->select('TType, TType2, SUM(BilledQty) as quantity');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('ItemID', $item_code);
			$this->db->where('TransDate2 BETWEEN "'.$month_start.' 00:00:00" AND "'.$month_end.' 23:59:59"');
			$this->db->where('BillID IS NOT NULL');
			$this->db->group_by('TType, TType2');
			return $this->db->get('tblhistory')->result_array();
			}
			
			private function calculateClosingQty($opening_qty, $transactions)
			{
			$closing_qty = $opening_qty;
			
			foreach ($transactions as $transaction) {
			$closing_qty = $this->adjustQuantityByTransactionType($closing_qty, $transaction);
			}
			
			return $closing_qty;
			}
			
			private function adjustQuantityByTransactionType($current_qty, $transaction)
			{
			$quantity = floatval($transaction['quantity']);
			$ttype = $transaction['TType'];
			$ttype2 = $transaction['TType2'];
			
			// Add transactions
			if (($ttype == "P" && $ttype2 == "Purchase") ||
			($ttype == "I" && $ttype2 == "Inward") ||
			($ttype == "B" && $ttype2 == "Production") ||
			($ttype == "R" && $ttype2 == "Fresh") ||
			($ttype == "T" && $ttype2 == "In")) {
			$current_qty += $quantity;
			}
			// Subtract transactions
			elseif (($ttype == "N" && $ttype2 == "PurchaseReturn") ||
            ($ttype == "A" && $ttype2 == "Issue") ||
            ($ttype == "O" && $ttype2 == "Order") ||
            ($ttype == "X" && in_array($ttype2, ["Free Distribution", "Promotional Activity", "Stock Adjustment", "IssueAgainstReturn"])) ||
            ($ttype == "T" && $ttype2 == "Out")) {
			$current_qty -= $quantity;
			}
			
			return $current_qty;
			}
			
			private function getItemRate($selected_company, $item_code)
			{
			// Try to get assigned rate first
			if($selected_company == "1"){
			$CustType = '1';
			}else if($selected_company == "2"){
			$CustType = '13';
			}else if($selected_company == "3"){
			$CustType = '21';
			}
			
			$this->db->select('assigned_rate');
			$this->db->where('item_id', $item_code);
			$this->db->where('PlantID', $selected_company);
			$this->db->where('state_id', 'UP');
			$this->db->where('distributor_id', $CustType);
			$rate_result = $this->db->get('tblrate_master')->row();
			
			if ($rate_result && $rate_result->assigned_rate > 0) {
			return floatval($rate_result->assigned_rate);
			}
			
			// Fallback to stock rate
			$this->db->select('BasicRate as StockRate');
			$this->db->where('ItemID', $item_code);
			$this->db->where('PlantID', $selected_company);
			$stock_rate = $this->db->get('tblstockmaster')->row();
			
			return $stock_rate ? floatval($stock_rate->StockRate) : 0;
			}
			
			
		*/// ************************* Stock value *****************************************
		
		
		
		public function getMonthly_ClosingStock($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			
			// Initialize result array
			$result = array();
			
			// Get all months between from_date and to_date
			$months = $this->getMonthsBetweenDates($from_date, $to_date);
			
			// Process for each main group (1=FG, 2=RM, 3=PM)
			$main_groups = [1, 2, 3];
			
			foreach ($months as $month) {
				$month_data = array(
                'month' => $month['label'],
                'month_start' => $month['start'],
                'month_end' => $month['end']
				);
				
				foreach ($main_groups as $main_group_id) {
					// Get all subgroups belonging to this main group
					$subgroups = $this->get_subgroups_by_main_group($main_group_id);
					
					if (!empty($subgroups)) {
						$subgroup_ids = implode(',', $subgroups);
						
						// Calculate stock value sum for this main group in this month
						$stock_value_sum = $this->calculateMonthlyStockValue(
                        $month['start'], 
                        $month['end'], 
                        $subgroup_ids, 
                        $selected_company, 
                        $fy
						);
						
						// Assign to appropriate main group
						if ($main_group_id == 1) {
							$month_data['FG'] = $stock_value_sum;
							} elseif ($main_group_id == 2) {
							$month_data['RM'] = $stock_value_sum;
							} elseif ($main_group_id == 3) {
							$month_data['PM'] = $stock_value_sum;
						}
						} else {
						// If no subgroups found 
						if ($main_group_id == 1) {
							$month_data['FG'] = 0;
							} elseif ($main_group_id == 2) {
							$month_data['RM'] = 0;
							} elseif ($main_group_id == 3) {
							$month_data['PM'] = 0;
						}
					}
				}
				
				$result[] = $month_data;
			}
			
			return $result;
		}
		
		private function getMonthsBetweenDates($start_date, $end_date)
		{
			$months = array();
			$current = date('Y-m-01', strtotime($start_date));
			$end = date('Y-m-t', strtotime($end_date));
			
			while ($current <= $end) {
				$month_start = date('Y-m-01', strtotime($current));
				$month_end = date('Y-m-t', strtotime($current));
				
				$months[] = array(
                'label' => date('M Y', strtotime($current)),
                'start' => $month_start,
                'end' => $month_end
				);
				
				$current = date('Y-m-01', strtotime($current . ' +1 month'));
			}
			
			return $months;
		}
		
		private function get_subgroups_by_main_group($main_group_id)
		{
			$this->db->select('id');
			$this->db->where('main_group_id', $main_group_id);
			$query = $this->db->get(db_prefix() . 'items_sub_groups');
			
			$subgroups = array();
			foreach ($query->result() as $row) {
				$subgroups[] = $row->id;
			}
			
			return $subgroups;
		}
		
		private function calculateMonthlyStockValue($month_start, $month_end, $subgroup_ids, $selected_company, $fy)
		{
			$stock_value_sum = 0;
			
			// Get items for these subgroups
			$items = $this->getItemsBySubgroups($subgroup_ids, $selected_company);
			
			foreach ($items as $item) {
				// Calculate opening quantity for the month
				$opening_qty = $this->getOpeningQtyForMonth($item['item_code'], $month_start, $selected_company, $fy, $subgroup_ids);
				
				// Get transactions for the month
				$transactions = $this->getMonthlyTransactions($item['item_code'], $month_start, $month_end, $selected_company, $fy);
				
				// Calculate closing quantity
				$closing_qty = $this->calculateClosingQty($opening_qty, $transactions);
				
				// Get rate for the item
				$rate = $this->getItemRate($item['item_code'], $selected_company, $transactions);
				
				// Calculate stock value
				$case_qty = ($item['case_qty'] == 0 || $item['case_qty'] == '') ? 1 : $item['case_qty'];
				$stock_qty = round($closing_qty);
				$stock_value = $stock_qty * $rate;
				
				$stock_value_sum += $stock_value;
			}
			
			return $stock_value_sum;
		}
		
		private function getItemsBySubgroups($subgroup_ids, $selected_company)
		{
			$this->db->select('item_code, case_qty, description');
			$this->db->where('PlantID', $selected_company);
			$this->db->where_in('SubGrpID1', explode(',', $subgroup_ids));
			$query = $this->db->get('tblitems');
			
			return $query->result_array();
		}
		
		private function getOpeningQtyForMonth($item_code, $month_start, $selected_company, $fy, $subgroup_ids)
		{
			$from_date_value = '20' . $fy . '-04-01';
			
			if ($month_start == $from_date_value) {
				// Get opening quantity from stock master
				$this->db->select('OQty');
				$this->db->where('ItemID', $item_code);
				$this->db->where('PlantID', $selected_company);
				$this->db->where('FY', $fy);
				$this->db->where('cnfid', '1');
				$query = $this->db->get('tblstockmaster');
				
				if ($query->num_rows() > 0) {
					return floatval($query->row()->OQty);
				}
				return 0;
				} else {
				// Calculate opening quantity from history
				$day_before = date('Y-m-d', strtotime($month_start . ' -1 day'));
				
				$sql = "SELECT SUM(tblhistory.BilledQty) as billsum, tblhistory.TType, tblhistory.TType2 
				FROM tblhistory 
				INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
				WHERE tblhistory.PlantID = ? 
				AND tblhistory.FY = ? 
				AND tblhistory.TransDate2 BETWEEN ? AND ? 
				AND tblitems.SubGrpID1 IN ({$subgroup_ids})
				AND tblhistory.ItemID = ?
				AND tblhistory.BillID IS NOT NULL 
				GROUP BY tblhistory.TType, tblhistory.TType2";
				
				$query = $this->db->query($sql, [$selected_company, $fy, $from_date_value . ' 00:00:00', $day_before . ' 23:59:59', $item_code]);
				$transactions = $query->result_array();
				
				// Get base opening quantity
				$this->db->select('SUM(OQty) as OQty');
				$this->db->where('ItemID', $item_code);
				$this->db->where('PlantID', $selected_company);
				$this->db->where('FY', $fy);
				$this->db->where('cnfid', '1');
				$base_query = $this->db->get('tblstockmaster');
				$opening_qty = ($base_query->num_rows() > 0) ? floatval($base_query->row()->OQty) : 0;
				
				// Adjust opening quantity based on transactions
				foreach ($transactions as $transaction) {
					$billsum = floatval($transaction['billsum']);
					
					if (in_array($transaction['TType'], ['P', 'I', 'B']) || 
                    ($transaction['TType'] == 'R' && $transaction['TType2'] == 'Fresh') ||
                    ($transaction['TType'] == 'T' && $transaction['TType2'] == 'In')) {
						$opening_qty += $billsum;
					} elseif (in_array($transaction['TType'], ['N', 'A', 'O', 'X']) || 
					($transaction['TType'] == 'T' && $transaction['TType2'] == 'Out')) {
						$opening_qty -= $billsum;
					}
				}
				
				return $opening_qty;
			}
		}
		
		private function getMonthlyTransactions($item_code, $month_start, $month_end, $selected_company, $fy)
		{
			$sql = "SELECT tblhistory.TType, tblhistory.TType2, tblhistory.BilledQty, tblhistory.SaleRate 
			FROM tblhistory 
			INNER JOIN tblitems ON tblitems.item_code = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID 
			WHERE tblhistory.PlantID = ? 
			AND tblhistory.FY = ? 
			AND tblhistory.TransDate2 BETWEEN ? AND ? 
			AND tblhistory.ItemID = ? 
			AND tblhistory.BillID IS NOT NULL";
			
			$query = $this->db->query($sql, [
            $selected_company, 
            $fy, 
            $month_start . ' 00:00:00', 
            $month_end . ' 23:59:59', 
            $item_code
			]);
			
			return $query->result_array();
		}
		
		private function calculateClosingQty($opening_qty, $transactions)
		{
			$closing_qty = $opening_qty;
			
			foreach ($transactions as $transaction) {
				$billed_qty = floatval($transaction['BilledQty']);
				
				if (in_array($transaction['TType'], ['P', 'I', 'B']) || 
                ($transaction['TType'] == 'R' && $transaction['TType2'] == 'Fresh') ||
                ($transaction['TType'] == 'T' && $transaction['TType2'] == 'In')) {
					$closing_qty += $billed_qty;
				} elseif (in_array($transaction['TType'], ['N', 'A', 'O', 'X']) || 
				($transaction['TType'] == 'T' && $transaction['TType2'] == 'Out')) {
					$closing_qty -= $billed_qty;
				}
			}
			
			return $closing_qty;
		}
		
		private function getItemRate($item_code, $selected_company, $transactions)
		{
			$rate = 0;
			
			// First try to get rate from transactions
			foreach ($transactions as $transaction) {
				if (!empty($transaction['SaleRate']) && floatval($transaction['SaleRate']) > 0) {
					$rate = floatval($transaction['SaleRate']);
					break;
				}
			}
			
			// If no rate from transactions, get from stock master
			if ($rate <= 0) {
				$this->db->select('ROUND(AVG(BQty),2) as StockRate');
				$this->db->where('ItemID', $item_code);
				$this->db->where('PlantID', $selected_company);
				$this->db->where('cnfid', '1');
				$query = $this->db->get('tblstockmaster');
				
				if ($query->num_rows() > 0 && !empty($query->row()->StockRate)) {
					$rate = floatval($query->row()->StockRate);
				}
			}
			
			// If still no rate, get assigned rate
			if ($rate <= 0) {
				$CustType = $this->getCustType($selected_company);
				
				$this->db->select('assigned_rate');
				$this->db->where('item_id', $item_code);
				$this->db->where('PlantID', $selected_company);
				$this->db->where('state_id', 'UP');
				$this->db->where('distributor_id', $CustType);
				$query = $this->db->get('tblrate_master');
				
				if ($query->num_rows() > 0 && !empty($query->row()->assigned_rate)) {
					$rate = floatval($query->row()->assigned_rate);
				}
			}
			
			return $rate;
		}
		
		private function getCustType($selected_company)
		{
			if ($selected_company == "1") {
				return '1';
				} elseif ($selected_company == "2") {
				return '13';
				} elseif ($selected_company == "3") {
				return '21';
			}
			return '1';
		}
		
		public function GetReceiptPayment($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]); // format: Y-m-d
			$to_date = to_sql_date($filterdata["to_date"]);     // format: Y-m-d
			
			
			$sql = "
			SELECT 
			DATE(tblaccountledger.Transdate) AS TransDate,
			SUM(tblaccountledger.Amount) AS TotalAmount,
			tblaccountledger.PassedFrom
			FROM 
			tblaccountledger
			WHERE 
			tblaccountledger.Transdate BETWEEN '$from_date' AND '$to_date'
			AND tblaccountledger.PassedFrom IN ('PAYMENTS','RECEIPTS')
			AND tblaccountledger.TType = 'D'
			AND tblaccountledger.FY = '$fy'
			AND tblaccountledger.PlantID = '$selected_company'
			GROUP BY 
			tblaccountledger.PassedFrom ,DATE(tblaccountledger.Transdate)
			ORDER BY 
			tblaccountledger.Transdate ASC
			";
			
			$rows = $this->db->query($sql)->result_array();
			
			$this->db->select('DATE(tblaccountledger.Transdate) AS TransDate,SUM(tblaccountledger.Amount) AS TotalAmount');
			$this->db->join('tblclients', 'tblclients.AccountID = tblaccountledger.AccountID AND tblclients.PlantID = tblaccountledger.PlantID ');
			$this->db->join('tblaccountgroupssub1', 'tblaccountgroupssub1.SubActGroupID1 = tblclients.SubActGroupID1');
			$this->db->join('tblaccountgroupssub', 'tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID');
			$this->db->where_in(db_prefix() . 'accountledger.FY', $year);
			$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
			$this->db->where_in(db_prefix() . 'clients.ActGroupID', ['10010','10018']);
			$this->db->where( db_prefix() . 'accountledger.Transdate BETWEEN "'.$from_date.'" AND "'.$to_date.'"');
			$this->db->group_by('DATE(tblaccountledger.Transdate)');
			$ExpensesData =  $this->db->get(db_prefix() . 'accountledger')->result_array();
			
			
			// echo "<pre>";print_r($ExpData);die;
			// Generate list of dates between from_date and to_date with format d-m-Y
			$Dates = [];
			$begin = new DateTime($from_date);
			$end = new DateTime($to_date);
			$end->modify('+1 day'); // include end date
			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($begin, $interval, $end);
			
			foreach ($daterange as $date) {
				$Dates[] = $date->format('d-M');
			}
			
			// Prepare utilization data with all dates initialized to 0
			$ReceiptsData = array_fill(0, count($Dates), 0);
			$PaymentsData = array_fill(0, count($Dates), 0);
			$ExpData = array_fill(0, count($Dates), 0);
			
			foreach ($rows as $row) {
				$rowDate = date('d-M', strtotime($row['TransDate']));
				
				$index = array_search($rowDate, $Dates);
				if ($index !== false) {
					
					if($row['PassedFrom'] == 'RECEIPTS'){
						$Receipts = $row['TotalAmount'];
						$ReceiptsData[$index] = floatval($Receipts);
					}
					
					if($row['PassedFrom'] == 'PAYMENTS'){
						$Payments = $row['TotalAmount'];
						$PaymentsData[$index] = floatval($Payments);
					}
				}
			}
			foreach ($ExpensesData as $Exp) {
				$rowDate = date('d-M', strtotime($Exp['TransDate']));
				
				$index = array_search($rowDate, $Dates);
				if ($index !== false) {
					$Expenses = $Exp['TotalAmount'];
					$ExpData[$index] = floatval($Expenses);
				}
			}
			
			// Prepare the final series data
			$series = [
			[
            'name' => 'Receipts',
            'data' => $ReceiptsData
			],
			[
            'name' => 'Payments',
            'data' => $PaymentsData
			],
			[
            'name' => 'Expenses',
            'data' => $ExpData
			],
			];
			
			$ReturnData = [
			'chartData' => $series,
			'Dates' => $Dates
			];
			
			return $ReturnData;
		}
		
		public function GetCashAndEquivalant($filterdata)
		{
			
			$selected_company = $this->session->userdata('root_company');
			$finacial_year    = $this->session->userdata('finacial_year');
			
			$fy_start_date = "20{$finacial_year}-04-01";
			$today         = date('Y-m-d');
			$yesterday     = date('Y-m-d', strtotime('-1 day'));
			
			// --- 1. Get Cash Accounts ---
			$this->db->select('AccountID, company as AccountName');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('SubActGroupID', '1000001');
			$cash_accounts = $this->db->get(db_prefix() . 'clients')->result_array();
			
			if (empty($cash_accounts)) {
				return [];
			}
			
			$account_ids  = array_column($cash_accounts, 'AccountID');
			$accounts_map = array_column($cash_accounts, 'AccountName', 'AccountID');
			
			// --- 2. FY Opening Balances ---
			$this->db->select('AccountID, BAL1');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $finacial_year);
			$this->db->where_in('AccountID', $account_ids);
			$balances_result = $this->db->get(db_prefix() . 'accountbalances')->result();
			
			$running = array_fill_keys($account_ids, 0);
			foreach ($balances_result as $row) {
				$running[$row->AccountID] = (float)$row->BAL1;
			}
			
			// --- 3. Add previous transactions (FY Start → Yesterday) ---
			$this->db->select('AccountID, TType, SUM(Amount) as Total');
			$this->db->where('PlantID', $selected_company);
			$this->db->like('FY', $finacial_year);
			$this->db->where_in('AccountID', $account_ids);
			$this->db->where('Transdate >=', $fy_start_date . ' 00:00:00');
			$this->db->where('Transdate <=', $yesterday . ' 23:59:59');
			$this->db->group_by(['AccountID', 'TType']);
			$past_tx = $this->db->get(db_prefix() . 'accountledger')->result();
			
			foreach ($past_tx as $tx) {
				if ($tx->TType == 'D') {
					$running[$tx->AccountID] += (float)$tx->Total;
					} else {
					$running[$tx->AccountID] -= (float)$tx->Total;
				}
			}
			
			// --- 4. Today's Transactions ---
			$this->db->select('AccountID, TType, SUM(Amount) as Total');
			$this->db->where('PlantID', $selected_company);
			$this->db->like('FY', $finacial_year);
			$this->db->where_in('AccountID', $account_ids);
			$this->db->where('Transdate >=', $today . ' 00:00:00');
			$this->db->where('Transdate <=', $today . ' 23:59:59');
			$this->db->group_by(['AccountID', 'TType']);
			$today_tx = $this->db->get(db_prefix() . 'accountledger')->result();
			
			// Compute final closing balance of today
			$final_output = [];
			
			foreach ($account_ids as $acc_id) {
				
				$today_total = 0;
				
				foreach ($today_tx as $tx) {
					if ($tx->AccountID == $acc_id) {
						$today_total += ($tx->TType == 'D') ? $tx->Total : -$tx->Total;
					}
				}
				
				$closing_today = $running[$acc_id] + $today_total;
				
				$final_output[] = [
				"name" => $accounts_map[$acc_id],
				"y"    => round($closing_today, 2)
				];
			}
			
			return $final_output; // Ready for bar chart
		}
	}
