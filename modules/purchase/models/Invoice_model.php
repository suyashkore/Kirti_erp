<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_model extends App_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function GetPendingInvoiceVendorList() 
	{
			$this->db->select('tblPurchInwardsMaster.AccountID, tblclients.company');
			$this->db->from(db_prefix().'PurchInwardsMaster');
			$this->db->join( db_prefix().'clients', 'tblclients.AccountID = tblPurchInwardsMaster.AccountID');
			$this->db->join( db_prefix().'GateMaster', 'tblGateMaster.InwardID = tblPurchInwardsMaster.InwardsID');
			$this->db->where('tblGateMaster.status >= 6');
			$this->db->where('tblPurchInwardsMaster.InvoiceID IS NULL');
			$this->db->group_by('tblPurchInwardsMaster.AccountID');
			return $this->db->get()->result_array();
	}
	public function GetPendingInwardList($VendorID) 
	{
			$this->db->select('tblPurchInwardsMaster.InwardsID,tblGateMaster.VehicleNo');
			$this->db->from(db_prefix().'PurchInwardsMaster');
			$this->db->join( db_prefix().'GateMaster', 'tblGateMaster.InwardID = tblPurchInwardsMaster.InwardsID');
			$this->db->where('tblGateMaster.status >= 6');
			$this->db->where('tblPurchInwardsMaster.AccountID',$VendorID);
			$this->db->where('tblPurchInwardsMaster.InvoiceID IS NULL');
			return $this->db->get()->result_array();
	}
	public function GetVendorDetails($VendorID) 
	{
			$this->db->select('tblclients.AccountID,company,GSTIN,billing_state,TDSSection,TDSPer,FreightTerms');
			$this->db->from(db_prefix().'clients');
			$this->db->where('tblclients.AccountID',$VendorID);
			return $this->db->get()->row();
	}
	

	public function AddNewPurchInvoice($form_data) 
	{
			$PlantID = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$vendor_state = $form_data["vendor_state"];
			$Company_state = $form_data["Company_state"];
			$InwardID = $form_data["InwardID"];
			$InwardIDDetails = $this->GetInwardDetailsByInwardID($InwardID);
			$InvoiceID = 'IN'.$FY.$PlantID.$InwardIDDetails->NextInvoiceID;
			$InvoiceDate = to_sql_date($form_data["InvoiceDate"])." ".date("H:i:s");
			
			$InvoiceArray = array(
					"PlantID"=>$PlantID,    
					"FY"=>$FY,    
					"PurchaseLocation"=>$InwardIDDetails->PurchaseLocation,    
					"InvoiceID"=>$InvoiceID,    
					"TransDate"=>$InvoiceDate,    
					"TransDate2"=>date('Y-m-d H:i:s'),    
					"ItemType"=>$InwardIDDetails->ItemType,    
					"ItemCategory"=>$InwardIDDetails->ItemCategory,    
					"PurchID"=>$InwardIDDetails->OrderID,    
					"InwardID"=>$InwardIDDetails->InwardsID,    
					"GateINID"=>$InwardIDDetails->GateINID,
					"AccountID"=>$InwardIDDetails->AccountID,
					"BrokerID"=>$InwardIDDetails->BrokerID,
					"DeliveryLocation"=>$InwardIDDetails->VendorLocation,
					"PaymentTerms"=>$InwardIDDetails->PaymentTerms,
					"FreightTerms"=>$InwardIDDetails->FreightTerms,
					"GSTIN"=>$InwardIDDetails->GSTIN,
					"Internal_Remarks"=>$form_data["internal_remarks"],
					"Document_Remark"=>$form_data["document_remark"],
					"Attachment"=>$form_data["Attachment"],
					"VendorDocAmt"=>$form_data["vendor_doc_amount"],
					"VendorDocWeight"=>$form_data["vendor_dispatch_weight"],
					"HoldPayment"=>$form_data["hold_payment"],
					"UserID"=>$this->session->userdata('username')
			);
			if($form_data["vendor_doc_no"]){
					$InvoiceArray["VendorDocNo"] = $form_data["vendor_doc_no"];
					$InvoiceArray["VendorDocDate"] = to_sql_date($form_data["VendorDocDate"]);
			}
			$InwardItemList = $InwardIDDetails->ItemList;
			if($this->db->insert('tblPurchaseInvoiceMaster', $InvoiceArray)){
					$TotalWeight = 0;$TotalQty = 0;$TotalItemAmt = 0;$TotalDiscAmt = 0;
					$TotalTaxableAmt = 0;$TotalCGSTAmt = 0;$TotalSGSTAmt = 0;$TotalIGSTAmt = 0;$TotalNetAmt = 0;
					foreach($InwardItemList as $key=>$val){
							$ItemID = $val["ItemID"];
							$OrderQty = $val["OrderQty"];
							$BasicRate = $val["BasicRate"];
							$UnitWeight = $val["UnitWeight"];$DiscAmt = $val["DiscAmt"];$GSTPer = $val["TotalGST"];
							$ItemAmt = 0;$ItemDiscAmt = 0;$ItemTaxableAmt = 0;$ItemGSTAmt = 0;
							$CGSTAmt = 0;$SGSTAmt = 0;$IGSTAmt = 0;$ItemNetAmt = 0;
							$TotalQty += $OrderQty;
							$TotalWeight += ($UnitWeight * $OrderQty); // Weight in kg
							$ItemAmt = $OrderQty * $BasicRate;
							$TotalItemAmt += $ItemAmt;
							$ItemDiscAmt = $DiscAmt * $OrderQty;
							$TotalDiscAmt += $ItemDiscAmt;
							$ItemTaxableAmt = $ItemAmt - $ItemDiscAmt;
							$TotalTaxableAmt += $ItemTaxableAmt;
							$ItemGSTAmt = $ItemTaxableAmt * ($GSTPer/100);
							if($vendor_state == $Company_state){
									$SGSTAmt = $ItemGSTAmt/2;
									$CGSTAmt = $ItemGSTAmt/2;
									$TotalSGSTAmt += $SGSTAmt;
									$TotalCGSTAmt += $CGSTAmt;
							}else{
									$IGSTAmt = $ItemGSTAmt;
									$TotalIGSTAmt += $IGSTAmt;
							}
							$ItemNetAmt = $ItemTaxableAmt + $ItemGSTAmt;
							$TotalNetAmt += $ItemNetAmt;
							$ItemUpdateArray = array(
									"BillID"=>$InwardIDDetails->OrderID,
									"TransID"=>$InvoiceID,
									"BilledQty"=>$OrderQty,
									"cgstamt"=>$CGSTAmt,
									"sgstamt"=>$SGSTAmt,
									"igstamt"=>$IGSTAmt,
									"ChallanAmt"=>$ItemAmt,
									"NetChallanAmt"=>$ItemNetAmt,
							);
							$this->db->where('ItemID', $ItemID);
							$this->db->where('OrderID', $InwardID);
							$this->db->update('tblhistory', $ItemUpdateArray);
					}
					$TDSAmt = 0;
					if($InwardIDDetails->TDSPercentage !=null && $InwardIDDetails->TDSPercentage > 0){
							$TDSAmt = $TotalTaxableAmt * ($InwardIDDetails->TDSPercentage / 100);
					}
					$TotalNetAmt = $TotalNetAmt - $TDSAmt;
					// Update PurchInvoice Master
					$UpdateArray = array(
							"TotalWeight"=>$TotalWeight,
							"TotalQuantity"=>$TotalQty,
							"ItemAmt"=>$TotalItemAmt,
							"DiscAmt"=>$TotalDiscAmt,
							"TaxableAmt"=>$TotalTaxableAmt,
							"CGSTAmt"=>$TotalCGSTAmt,
							"SGSTAmt"=>$TotalSGSTAmt,
							"IGSTAmt"=>$TotalIGSTAmt,
							"TDSSection"=>$InwardIDDetails->TDSSection,
							"TDSPercentage"=>$InwardIDDetails->TDSPercentage,
							"TDSAmt"=>$TDSAmt,
							"NetAmt"=>$TotalNetAmt
					);
					$this->db->where('InvoiceID', $InvoiceID);
					$this->db->update('tblPurchaseInvoiceMaster', $UpdateArray);
					// Update Purch Inward Master with Invoice ID
					$UpdateInward = array(
							"InvoiceID"=>$InvoiceID,
					);
					$this->db->where('InwardsID', $InwardID);
					$this->db->update('tblPurchInwardsMaster', $UpdateInward);
					$RndAmt = round($TotalNetAmt);
					$RoundOffAmt = $RndAmt - $TotalNetAmt;
					// Account Ledger add
					$Narration = "Purchase Invoice against ".$InvoiceID.' InwardID : '.$InwardID." And PO ".$InwardIDDetails->OrderID;
					$ordNo = 1;
					$VendorLedger = array(
							"PlantID" =>$PlantID, 
							"FY" =>$FY, 
							"Transdate" =>$InvoiceDate, 
							"VoucherID" =>$InvoiceID, 
							"TransDate2" =>date('Y-m-d H:i:s'), 
							"AccountID" =>$InwardIDDetails->AccountID, 
							"EffectOn" =>"PURCH", 
							"TType" =>"C", 
							"Amount" =>$RndAmt, 
							"Narration" =>$Narration, 
							"PassedFrom" =>"PURCHASE", 
							"OrdinalNo" =>$ordNo, 
							"UserID" =>$this->session->userdata('username'), 
					);
					$this->db->insert('tblaccountledger', $VendorLedger);
					$ordNo++;
					
					$PurchLedger = array(
							"PlantID" =>$PlantID, 
							"FY" =>$FY, 
							"Transdate" =>$InvoiceDate, 
							"VoucherID" =>$InvoiceID, 
							"TransDate2" =>date('Y-m-d H:i:s'), 
							"AccountID" =>"PURCH", 
							"EffectOn" =>$InwardIDDetails->AccountID, 
							"TType" =>"D", 
							"Amount" =>$TotalItemAmt, 
							"Narration" =>$Narration, 
							"PassedFrom" =>"PURCHASE", 
							"OrdinalNo" =>$ordNo, 
							"UserID" =>$this->session->userdata('username'), 
					);
					$this->db->insert('tblaccountledger', $PurchLedger);
					$ordNo++;
					if($TotalIGSTAmt > 0){
							$IGSTLedger = array(
									"PlantID" =>$PlantID, 
									"FY" =>$FY, 
									"Transdate" =>$InvoiceDate, 
									"VoucherID" =>$InvoiceID, 
									"TransDate2" =>date('Y-m-d H:i:s'), 
									"AccountID" =>"IGST", 
									"EffectOn" =>$InwardIDDetails->AccountID, 
									"TType" =>"D", 
									"Amount" =>$TotalIGSTAmt, 
									"Narration" =>$Narration, 
									"PassedFrom" =>"PURCHASE", 
									"OrdinalNo" =>$ordNo, 
									"UserID" =>$this->session->userdata('username'), 
							);
							$this->db->insert('tblaccountledger', $IGSTLedger);
							$ordNo++;
					}else{
							$CGSTLedger = array(
									"PlantID" =>$PlantID, 
									"FY" =>$FY, 
									"Transdate" =>$InvoiceDate, 
									"VoucherID" =>$InvoiceID, 
									"TransDate2" =>date('Y-m-d H:i:s'), 
									"AccountID" =>"CGST", 
									"EffectOn" =>$InwardIDDetails->AccountID, 
									"TType" =>"D", 
									"Amount" =>$TotalCGSTAmt, 
									"Narration" =>$Narration, 
									"PassedFrom" =>"PURCHASE", 
									"OrdinalNo" =>$ordNo, 
									"UserID" =>$this->session->userdata('username'), 
							);
							$this->db->insert('tblaccountledger', $CGSTLedger);
							$ordNo++;
							$SGSTLedger = array(
									"PlantID" =>$PlantID, 
									"FY" =>$FY, 
									"Transdate" =>$InvoiceDate, 
									"VoucherID" =>$InvoiceID, 
									"TransDate2" =>date('Y-m-d H:i:s'), 
									"AccountID" =>"SGST", 
									"EffectOn" =>$InwardIDDetails->AccountID, 
									"TType" =>"D", 
									"Amount" =>$TotalSGSTAmt, 
									"Narration" =>$Narration, 
									"PassedFrom" =>"PURCHASE", 
									"OrdinalNo" =>$ordNo, 
									"UserID" =>$this->session->userdata('username'), 
							);
							$this->db->insert('tblaccountledger', $SGSTLedger);
							$ordNo++;
					}
					// Discount ledger effect
					if($TotalDiscAmt > 0){
							$DiscLedger = array(
									"PlantID" =>$PlantID, 
									"FY" =>$FY, 
									"Transdate" =>$InvoiceDate, 
									"VoucherID" =>$InvoiceID, 
									"TransDate2" =>date('Y-m-d H:i:s'), 
									"AccountID" =>"PDISC", 
									"EffectOn" =>"PURCH", 
									"TType" =>"C", 
									"Amount" =>$TotalDiscAmt, 
									"Narration" =>$Narration, 
									"PassedFrom" =>"PURCHASE", 
									"OrdinalNo" =>$ordNo, 
									"UserID" =>$this->session->userdata('username'), 
							);
							$this->db->insert('tblaccountledger', $DiscLedger);
							$ordNo++;
					}
					// TDS Ledger Effect
					if($TDSAmt >0){
							$TDSLedger = array(
									"PlantID" =>$PlantID, 
									"FY" =>$FY, 
									"Transdate" =>$InvoiceDate, 
									"VoucherID" =>$InvoiceID, 
									"TransDate2" =>date('Y-m-d H:i:s'), 
									"AccountID" =>$InwardIDDetails->TDSSection, 
									"EffectOn" =>$InwardIDDetails->AccountID, 
									"TType" =>"C", 
									"Amount" =>$TDSAmt, 
									"Narration" =>$Narration, 
									"PassedFrom" =>"PURCHASE", 
									"OrdinalNo" =>$ordNo, 
									"UserID" =>$this->session->userdata('username'), 
							);
							$this->db->insert('tblaccountledger', $TDSLedger);
							$ordNo++;
					}
					
					if($RoundOffAmt > 0 || $RoundOffAmt < 0){
							$roundLedger = array(
									"PlantID" =>$PlantID, 
									"FY" =>$FY, 
									"Transdate" =>$InvoiceDate, 
									"VoucherID" =>$InvoiceID, 
									"TransDate2" =>date('Y-m-d H:i:s'), 
									"AccountID" =>"ROUNDOFF", 
									"EffectOn" =>$InwardIDDetails->AccountID, 
									"TType" =>"D", 
									"Amount" =>$RoundOffAmt, 
									"Narration" =>$Narration, 
									"PassedFrom" =>"PURCHASE", 
									"OrdinalNo" =>$ordNo, 
									"UserID" =>$this->session->userdata('username'), 
							);
							$this->db->insert('tblaccountledger', $roundLedger);
							$ordNo++;
					}
					
					$response = array("status"=>true,'message'=>"Invoice Generated Successfully",'InvoiceID'=>$InvoiceID);
			}else{
					$response = array("status"=>false,'message'=>"something went wrong",'InvoiceID'=>"");
			}
			return $response;/*
			echo "<pre>";
				print_r($InwardItemList);
				die;    */
	}
	public function GetRootCompanyDetails() 
	{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblrootcompany.*');
			$this->db->from(db_prefix().'rootcompany');
			$this->db->where('tblrootcompany.id',$selected_company);
			return $this->db->get()->row();
	}
	
	public function GetInvoiceList($data) 
	{
			$from_date = to_sql_date($data["from_date"]).' 00:00:00';
			$to_date = to_sql_date($data["to_date"]).' 23:59:59';
			$selected_company = $this->session->userdata('root_company');
				
			$this->db->select('PI.InvoiceID,PI.TransDate,tblGateMaster.VehicleNo,tblGateMaster.GateINID,tblGateMaster.TransDate AS GateInDate,
			tblclients.company AS VendorName,PI.GSTIN,PI.TotalWeight,PI.TotalQuantity,PI.NetAmt');
			$this->db->from(db_prefix().'PurchaseInvoiceMaster AS PI');
			$this->db->join( db_prefix().'GateMaster', 'tblGateMaster.GateINID = PI.GateINID');
			$this->db->join( db_prefix().'clients', 'tblclients.AccountID = PI.AccountID');
			$this->db->where('PI.PlantID',$selected_company);
			if($data["category"]){
					$this->db->where('PI.ItemCategory',$data["category"]);
			} 
			$this->db->where('PI.TransDate >=', $from_date);
			$this->db->where('PI.TransDate <=', $to_date);
			return $this->db->get()->result_array();
	}
	
	public function GetPurchaseInvoiceDetails($data) 
	{
			$InvoiceID = $data["InvoiceID"];
			$selected_company = $this->session->userdata('root_company');
				
			$this->db->select('tblclients.company AS VendorName,tblGateMaster.VehicleNo,
			tblItemTypeMaster.ItemTypeName,tblItemCategoryMaster.CategoryName,tblPlantLocationDetails.LocationName,
			tblxx_citylist.city_name,tblclients.billing_state,tblGateMaster.TransDate AS GateInDate,PI.*');
			$this->db->from(db_prefix().'PurchaseInvoiceMaster AS PI');
			$this->db->join( db_prefix().'GateMaster', 'tblGateMaster.GateINID = PI.GateINID');
			$this->db->join( db_prefix().'clients', 'tblclients.AccountID = PI.AccountID');
			$this->db->join( db_prefix().'ItemTypeMaster', 'tblItemTypeMaster.id = PI.ItemType');
			$this->db->join( db_prefix().'ItemCategoryMaster', 'tblItemCategoryMaster.id = PI.ItemCategory');
			$this->db->join( db_prefix().'PlantLocationDetails', 'tblPlantLocationDetails.id = PI.PurchaseLocation');
			$this->db->join( db_prefix().'clientwiseshippingdata', 'tblclientwiseshippingdata.id = PI.DeliveryLocation');
			$this->db->join( db_prefix().'xx_citylist', 'tblxx_citylist.id = tblclientwiseshippingdata.ShippingCity',"LEFT");
			$this->db->where('PI.PlantID',$selected_company);
			$this->db->where('PI.InvoiceID',$InvoiceID);
			$InvoiceDetails = $this->db->get()->row();
			if($InvoiceDetails){
					$ShortInvoiceID = substr($InvoiceDetails->InvoiceID,5,12);
					$InvoiceDetails->ShortInvoiceID = $ShortInvoiceID;
					$this->db->select('tblhistory.ItemID,tblhistory.SuppliedIn,tblhistory.OrderQty,tblhistory.BasicRate,tblitems.hsn_code,tblhistory.UnitWeight,
					tblhistory.DiscAmt,(tblhistory.cgst + tblhistory.sgst + tblhistory.igst) TotalGST,tblhistory.NetOrderAmt,
					tblitems.ItemName');
					$this->db->from(db_prefix().'history');
					$this->db->join( db_prefix().'items', 'tblitems.ItemID = tblhistory.ItemID');
					$this->db->where('tblhistory.TransID',$InvoiceID);
					$this->db->order_by('tblhistory.id',"ASC");
					$ItemDetails = $this->db->get()->result_array();
					$InvoiceDetails->ItemList = $ItemDetails;
			}
			return $InvoiceDetails;
	}
	public function GetInwardDetailsByInwardID($InwardID) 
	{
			$this->db->select('tblPurchInwardsMaster.InwardsID,tblPurchInwardsMaster.OrderID,tblPurchInwardsMaster.AccountID,tblPurchInwardsMaster.BrokerID,
			tblPurchInwardsMaster.VendorLocation,tblPurchInwardsMaster.PaymentTerms,tblPurchInwardsMaster.FreightTerms,tblPurchInwardsMaster.GSTIN,
			tblPurchInwardsMaster.TDSPercentage,tblPurchInwardsMaster.TDSSection,
			tblGateMaster.VehicleNo,tblGateMaster.GateINID,tblGateMaster.TransDate AS GateINDate,
			tblPlantLocationDetails.LocationName,tblPurchInwardsMaster.PurchaseLocation,
			tblPurchInwardsMaster.ItemType,tblItemTypeMaster.ItemTypeName,tblclients.company AS BrokerName,
			tblPurchInwardsMaster.ItemCategory,tblItemCategoryMaster.CategoryName,tblxx_citylist.city_name');
			$this->db->from(db_prefix().'PurchInwardsMaster');
			$this->db->join( db_prefix().'GateMaster', 'tblGateMaster.InwardID = tblPurchInwardsMaster.InwardsID');
			$this->db->join( db_prefix().'PlantLocationDetails', 'tblPlantLocationDetails.id = tblPurchInwardsMaster.PurchaseLocation');
			$this->db->join( db_prefix().'ItemTypeMaster', 'tblItemTypeMaster.id = tblPurchInwardsMaster.ItemType');
			$this->db->join( db_prefix().'ItemCategoryMaster', 'tblItemCategoryMaster.id = tblPurchInwardsMaster.ItemCategory');
			$this->db->join( db_prefix().'clients', 'tblclients.AccountID = tblPurchInwardsMaster.BrokerID',"LEFT");
			$this->db->join( db_prefix().'clientwiseshippingdata', 'tblclientwiseshippingdata.id = tblPurchInwardsMaster.VendorLocation');
			$this->db->join( db_prefix().'xx_citylist', 'tblxx_citylist.id = tblclientwiseshippingdata.ShippingCity',"LEFT");
			$this->db->where('tblPurchInwardsMaster.InwardsID',$InwardID);
			$InwardDetails =  $this->db->get()->row();
			if($InwardDetails){
					$InwardID = $InwardDetails->InwardsID;
					$ItemType = $InwardDetails->ItemType;
					$ItemCategory = $InwardDetails->ItemCategory;
					// Get Next InvoiceNo Against Item Type and Category
					$NextInvoiceNumber = $this->GetNextInvoiceNumber($ItemType,$ItemCategory);
					
					$InwardDetails->NextInvoiceID = $NextInvoiceNumber;
					// Items Details Aginst Inward
					$this->db->select('tblhistory.ItemID,tblhistory.SuppliedIn,tblhistory.OrderQty,tblhistory.BasicRate,tblitems.hsn_code,tblhistory.UnitWeight,
					tblhistory.DiscAmt,(tblhistory.cgst + tblhistory.sgst + tblhistory.igst) TotalGST,tblhistory.NetOrderAmt,
					tblitems.ItemName');
					$this->db->from(db_prefix().'history');
					$this->db->join( db_prefix().'items', 'tblitems.ItemID = tblhistory.ItemID');
					$this->db->where('tblhistory.OrderID',$InwardID);
					$this->db->order_by('tblhistory.id',"ASC");
					$ItemDetails = $this->db->get()->result_array();
					$InwardDetails->ItemList = $ItemDetails;
			}
			return $InwardDetails;
	}
	
	public function GetNextInvoiceNumber($ItemType,$ItemCategory) 
	{
			$this->db->select('Count(tblPurchaseInvoiceMaster.id) TotalInvoice');
			$this->db->from(db_prefix().'PurchaseInvoiceMaster');
			$this->db->where('tblPurchaseInvoiceMaster.ItemType',$ItemType);
			$this->db->where('tblPurchaseInvoiceMaster.ItemCategory',$ItemCategory);
			$this->db->order_by('tblPurchaseInvoiceMaster.id',"DESC");
			$TotalInvoice = $this->db->get()->row();
			$NextInvoiceID = $TotalInvoice->TotalInvoice + 1;
			$number = str_pad($NextInvoiceID, 5, '0', STR_PAD_LEFT);
			$NextInvoiceNumber = str_pad($ItemType, 2, '0', STR_PAD_LEFT).str_pad($ItemCategory, 2, '0', STR_PAD_LEFT).$number;   
			return $NextInvoiceNumber;
	}
    
  public function getInvoiceDetailsPrint($id){
    $this->db->select(' pim.*,  c.company, c.billing_address, c.billing_city, c.billing_state, c.GSTIN, gm.id as gatein_id, gm.GateINID as gatein_no, gm.VehicleNo, pld.LocationName,  sl.state_name,  itm.ItemTypeName,  icm.CategoryName,  cwsd.ShippingCity,  cl.city_name,  ft.FreightTerms, gdm.GodownName', FALSE);

    $this->db->from(db_prefix().'PurchaseInvoiceMaster pim');
    // String joins (disable escaping)
    $this->db->join( db_prefix().'clients c', 'c.AccountID = pim.AccountID COLLATE utf8mb4_general_ci', 'left', FALSE );
    $this->db->join(db_prefix().'GateMaster gm', 'gm.GateINID = pim.GateINID', 'left');
    $this->db->join( db_prefix().'xx_statelist sl', 'CONVERT(sl.short_name USING utf8mb4) COLLATE utf8mb4_general_ci = c.billing_state', 'left', FALSE );
    // Normal joins (no need for collate)
    $this->db->join(db_prefix().'PlantLocationDetails pld', 'pld.id = pim.PurchaseLocation', 'left');
    $this->db->join(db_prefix().'ItemTypeMaster itm', 'itm.Id = pim.ItemType', 'left');
    $this->db->join(db_prefix().'ItemCategoryMaster icm', 'icm.Id = pim.ItemCategory', 'left');
    $this->db->join(db_prefix().'clientwiseshippingdata cwsd', 'cwsd.id = pim.DeliveryLocation', 'left');
    $this->db->join(db_prefix().'xx_citylist cl', 'cl.id = cwsd.ShippingCity', 'left');
    $this->db->join(db_prefix().'FreightTerms ft', 'ft.Id = pim.FreightTerms', 'left');
    $this->db->join(db_prefix().'PurchInwardsMaster piwm', 'piwm.InwardsID = pim.InwardID', 'left');
    $this->db->join(db_prefix().'godownmaster gdm', 'gdm.Id = piwm.GodownID', 'left');

    if (is_numeric($id)) {
      $this->db->where('pim.id', $id);
    } else {
      $this->db->where('pim.InvoiceID', $id);
    }

    $master = $this->db->get()->row();

    if (!$master) {
      return null;
    }

    $this->db->select('h.*, i.ItemName as item_name');
    $this->db->from(db_prefix().'history h');
    $this->db->join(db_prefix().'items i', 'i.ItemID = h.ItemID', 'left');
    $this->db->where('h.TransID', $master->InvoiceID);
    $history = $this->db->get()->result();

    $master->history = $history;

    return $master;
  }

	// ===== UPDATE DATA =====
  public function updateData($table, $data, $where = null){
    $data['Lupdate'] = date('Y-m-d H:i:s');
    $data['UserID2'] = $this->session->userdata('username');

    $this->db->where($where);
    return $this->db->update(db_prefix().$table, $data);
  }
}