<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Challan_model extends App_Model
{
	const STATUS_UNPAID = 1;
	const STATUS_PAID = 2;
	
	const STATUS_PARTIALLY = 3;	
	const STATUS_OVERDUE = 4;	
	const STATUS_CANCELLED = 5;	
	const STATUS_DRAFT = 6;
	
	private $statuses = [
	self::STATUS_UNPAID,
	self::STATUS_PAID,
	self::STATUS_PARTIALLY,
	self::STATUS_OVERDUE,
	self::STATUS_CANCELLED,
	self::STATUS_DRAFT,
	];
	
	private $shipping_fields = [
	'shipping_street',
	'shipping_city',
	'shipping_city',
	'shipping_state',
	'shipping_zip',
	'shipping_country',
	];
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_statuses()
	{
		return $this->statuses;
	}
	
	
	public function get_sale_agents()
	{
		return $this->db->query('SELECT DISTINCT(sale_agent) as sale_agent, CONCAT(firstname, \' \', lastname) as full_name FROM ' . db_prefix() . 'invoices JOIN ' . db_prefix() . 'staff ON ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'invoices.sale_agent WHERE sale_agent != 0')->result_array();
	}
	
	/* Create JSON */
	public function get_json($data = '')
	{
		
		ini_set('serialize_precision','-1');
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$TrandId = $data["TransID"];
		$this->db->select(db_prefix() . 'salesmaster.*,'.db_prefix() . 'clients.vat,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.address,'.db_prefix() . 'clients.address3,'.db_prefix() . 'clients.zip,'.db_prefix() . 'contacts.kms,'.db_prefix() . 'contacts.email,'.db_prefix() . 'clients.city,'.db_prefix() .'xx_citylist.city_name,'.db_prefix() .'xx_statelist.id As StateId');
		$this->db->from(db_prefix() . 'salesmaster');
		$this->db->join(db_prefix() . 'clients', db_prefix() . 'salesmaster.AccountID = '.db_prefix() . 'clients.AccountID AND '.db_prefix() . 'salesmaster.PlantID = '.db_prefix() . 'clients.PlantID');
		
		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'clients.AccountID = '.db_prefix() . 'contacts.AccountID AND '.db_prefix() . 'clients.PlantID = '.db_prefix() . 'contacts.PlantID');
		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'clients.city = '.db_prefix() . 'xx_citylist.id','LEFT');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'clients.state = '.db_prefix() . 'xx_statelist.short_name');
		
		$this->db->where(db_prefix() . 'salesmaster.SalesID', $TrandId);
		$this->db->where(db_prefix() . 'salesmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'salesmaster.FY', $fy);
		$Sales_details = $this->db->get()->row();
		/*echo "<pre>";
			print_r($Sales_details);
		die;*/
		$this->db->select(db_prefix() . 'history.*,'.db_prefix() . 'items.hsn_code,'.db_prefix() . 'items.unit');
		$this->db->from(db_prefix() . 'history');
		$this->db->join(db_prefix() . 'items', db_prefix() . 'history.ItemID = '.db_prefix() . 'items.item_code AND '.db_prefix() . 'history.PlantID = '.db_prefix() . 'items.PlantID');
		$this->db->where(db_prefix() . 'history.TransID', $TrandId);
		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'history.FY', $fy);
		$this->db->where(db_prefix() . 'history.TType', "O");
		$this->db->where(db_prefix() . 'history.NetChallanAmt !=', "0.00");
		$this->db->where(db_prefix() . 'history.TType2', "Order");
		$ItemList = $this->db->get()->result_array();
		$AttribDtls = array(
		"Nm"=>null,
		"Val"=>null
		);
		$AttribDtls_new = array();
		array_push($AttribDtls_new, $AttribDtls);
		$i = 0;
		$SlNo = 1;
		$newItemList = array();
		foreach ($ItemList as $value) {
			# code...
			$newItemList[$i]['SlNo'] = (string)$SlNo;
			$newItemList[$i]['PrdDesc'] = $value["hsn_code"];;
			$newItemList[$i]['IsServc'] = 'N';
			$newItemList[$i]['HsnCd'] = $value["hsn_code"];
			$newItemList[$i]['Barcde'] = null;
			$newItemList[$i]['Qty'] = floatval($value["BilledQty"]);
			$newItemList[$i]['FreeQty'] = 0;
			$newItemList[$i]['Unit'] = $value["unit"];
			$newItemList[$i]['UnitPrice'] = floatval($value["BasicRate"]);
			$newItemList[$i]['TotAmt'] = floatval($value["ChallanAmt"]);
			$newItemList[$i]['Discount'] = floatval($value["DiscAmt"]);
			$newItemList[$i]['PreTaxVal'] = 0.00;
			$newItemList[$i]['AssAmt'] = floatval($value["ChallanAmt"]);
			if($value["igst"] == NULL || $value["igst"] == '0.00'){
				$gst = $value["sgst"] + $value["cgst"];
				$igstAmt = 0.00;
				$cgstAmt = floatval($value["cgstamt"]);
				$sgstAmt = floatval($value["sgstamt"]);
				$IgstOnIntra = "N";
				}else{
				$gst = $value["igst"];
				$igstAmt = floatval($value["igstamt"]);
				$cgstAmt = 0.00;
				$sgstAmt = 0.00;
				$IgstOnIntra = "N";
			}
			$newItemList[$i]['GstRt'] = floatval($gst);
			$newItemList[$i]['IgstAmt'] = $igstAmt;
			$newItemList[$i]['CgstAmt'] = $cgstAmt;
			$newItemList[$i]['SgstAmt'] = $sgstAmt;
			$newItemList[$i]['CesRt'] = 0.00;
			$newItemList[$i]['CesAmt'] = 0.00;
			$newItemList[$i]['CesNonAdvlAmt'] = 0;
			$newItemList[$i]['StateCesRt'] = 0;
			$newItemList[$i]['StateCesAmt'] = 0;
			$newItemList[$i]['StateCesNonAdvlAmt'] = 0;
			$newItemList[$i]['OthChrg'] = 0;
			$newItemList[$i]['TotItemVal'] = floatval($value["NetChallanAmt"]);
			$newItemList[$i]['BchDtls'] = null;
			//$newItemList[$i]['AttribDtls'] = $AttribDtls_new;
			$i++;
			$SlNo++;
		}
		
		$company_details = $this->get_company_detail($selected_company);
		
		$InvNo = $TrandId;
		$InvDate = _d(substr($Sales_details->Transdate,0,10));
		$LglNm = $Sales_details->company;
		$Addr1 = $Sales_details->address;
		$Addr2 = $Sales_details->Address3;
		if($Sales_details->city_name == ""){
			$location = $Sales_details->city;
			}else{
			$location = $Sales_details->city_name;
		}
		$Loc = $location;
		$Pin = $Sales_details->zip;
		$Stcd = $Sales_details->StateId;
		$Ph = $Sales_details->phonenumber;
		$Em = null;
		$pgst = $Sales_details->gstno;
		
		$Gstin_c = $company_details->gst;
		$LglNm_c = $company_details->company_name;
		$Pos_c = $Stcd;
		$Addr1_c = $company_details->address;
		$Addr2_c = null;
		$Loc_c = "Gorakhpur";
		$Pin_c = 273209;
		$Stcd_c = "09";
		$Ph_c = $company_details->mobile1;
		$Em_c = null;
		
		$TranDtls = array(
		"TaxSch"=>"GST",
		"SupTyp"=>"B2B",
		"IgstOnIntra"=>$IgstOnIntra,
		"RegRev"=>"N",
		"EcmGstin"=>null
		);
		$DocDtls = array(
		"Typ"=>"INV",
		"No"=>$InvNo,
		"Dt"=>$InvDate
		);
		$BuyerDtls = array(
		"Gstin"=>$pgst,
		"LglNm"=>$LglNm,
		"TrdNm"=>$LglNm,
		"Pos"=>$Pos_c,
		"Addr1"=>$Addr1,
		"Addr2"=>$Addr2,
		"Loc"=>$Loc,
		"Pin"=>(int)$Pin,
		"Stcd"=>$Stcd,
		"Ph"=>$Ph,
		);
		$SellerDtls = array(
		"Gstin"=>$Gstin_c,
		"LglNm"=>$LglNm_c,
		"TrdNm"=>$LglNm_c,
		"Addr1"=>$Addr1_c,
		"Addr2"=>$Addr2_c,
		"Loc"=>$Loc_c,
		"Pin"=>$Pin_c,
		"Stcd"=>$Stcd_c,
		"Ph"=>$Ph_c,
		);
		$AssVal = number_format((float)$Sales_details->SaleAmt, 2, '.', '');
		$IgstVal = $Sales_details->igstamt;
		$CgstVal = $Sales_details->cgstamt;
		$SgstVal = $Sales_details->sgstamt;
		$CesVal = 0;
		$StCesVal = 0;
		$Discount = $Sales_details->DiscAmt;
		$OthChrg = $Sales_details->tcsAmt;
		$rnd = $Sales_details->RndAmt - $Sales_details->BillAmt;
		$RndOffAmt = number_format($rnd,2);
		$TotInvVal = $Sales_details->RndAmt;
		$TotInvValFc = 0;
		
		$ValDtls = array(
		"AssVal"=>floatval($Sales_details->SaleAmt),
		"IgstVal"=>floatval($IgstVal),
		"CgstVal"=>floatval($CgstVal),
		"SgstVal"=>floatval($SgstVal),
		"CesVal"=>$CesVal,
		"StCesVal"=>$StCesVal,
		"Discount"=>floatval($Discount),
		"OthChrg"=>floatval($OthChrg),
		"RndOffAmt"=>floatval($RndOffAmt),
		"TotInvVal"=>floatval($TotInvVal),
		);
		//$ValDtls = str_replace('"AssVal":"'.$ValDtls['AssVal'].'"', '"AssVal":'.$ValDtls['AssVal'].'', $ValDtls);
		$ExpDtls = array(
		"ShipBNo"=>null,
		"ShipBDt"=>null,
		"Port"=>null,
		"RefClm"=>null,
		"ForCur"=>null,
		"CntCode"=>null,
		"ExpDuty"=>0
		);
		/*$EwbDtls = array(
			"TransId"=>null,
			"TransName"=>null,
			"TransMode"=>null,
			"Distance"=>$Sales_details->kms,
			"TransDocNo"=>null,
			"TransDocDt"=>null,
			"VehNo"=>null,
			"VehType"=>null
		);*/
		$EwbDtls = null;
		$DispDtls = array(
		"Nm"=>$LglNm,
		"Addr1"=>$Addr1,
		"Addr2"=>$Addr2,
		"Loc"=>$Loc,
		"Pin"=>(int)$Pin,
		"Stcd"=>$Stcd,
		);
		$ShipDtls = array(
		"Gstin"=>$pgst,
		"LglNm"=>$LglNm,
		"TrdNm"=>$LglNm,
		"Addr1"=>$Addr1,
		"Addr2"=>$Addr2,
		"Loc"=>$Loc,
		"Pin"=>(int)$Pin,
		"Stcd"=>$Stcd,
		);
		$json_data = array(
		"Version"=>'1.1',
		"TranDtls"=>$TranDtls,
		"DocDtls"=>$DocDtls,
		"SellerDtls"=>$SellerDtls,
		"BuyerDtls"=>$BuyerDtls,
		"DispDtls"=>$DispDtls,
		"ShipDtls"=>$ShipDtls,
		"ValDtls"=>$ValDtls,
		"ExpDtls"=>$ExpDtls,
		"EwbDtls"=>$EwbDtls,/*
			"PayDtls"=>$PayDtls,
			"RefDtls"=>$RefDtls,
		"AddlDocDtls"=>$AddlDocDtls_new,*/
		"ItemList"=>$newItemList
		);
		$json = array();
		array_push($json, $json_data);
		
		$party_name = trim($Sales_details->company);
		$ChallanID = trim($Sales_details->ChallanID);
		
		$party_name_new = str_ireplace( array( '\'', '"',
		',' , ';', '<', '>','/' ), ' ', $party_name);
		
		//echo json_encode($json);
		
		$file_name = 'E-INV_V1_JSON_'.$party_name_new.'_'.$InvNo.'_'.$ChallanID;
		$m = date('m');
		$filePath = 'uploads/E-invoice/'.$m.'/'.$file_name.'.json';
		$result = json_encode($json);
		
		if(write_file('uploads/E-invoice/'.$m.'/'.$file_name.'.json', $result)){
			
			//download file from directory
			force_download($filePath, NULL);
			}else{
			echo 'Error exporting mysql data...';
			
		}
		
	}
	
	public function get_company_detail()
	{  
		
		$selected_company = $this->session->userdata('root_company');
		$sql ='SELECT '.db_prefix().'rootcompany.*
		FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
		$result = $this->db->query($sql)->row();
		return $result;
	}
	/*
		get Challan list
		
	*/
	
	public function get($id = '', $where = [])
	{
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->select(db_prefix() . 'challanmaster.*,users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln');
		$this->db->from(db_prefix() . 'challanmaster');
		$this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
		$this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
		$this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
		
		$this->db->where(db_prefix() . 'challanmaster.ChallanID', $id);
		$this->db->where(db_prefix() . 'challanmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'challanmaster.FY', $fy);
		$challan = $this->db->get()->row();
		
		/*$query = $this->db->get_where('tblchallanmaster', array('ChallanID' => $id,"PlantID" =>$selected_company ,"FY" =>$fy));
		$challan = $query->row();*/
		
		if($challan){
			
			$challan->order       = $this->get_order_by_challan($id);
		}
		return $challan;
	}
	//==================== Get Vehicle List By DriverID ============================	
	public function GetVehicleListByDriverID($postData)
	{
		$this->db->select(db_prefix() . 'vehicle.*');
		$this->db->where(db_prefix() . 'vehicle.DriverID', $postData["AccountID"]);
		return $this->db->get(db_prefix() . 'vehicle')->row();
	}
	
	function accountlist_driver($postData){
		
		$response = array();
		
		$where_clients = '';
		
		if(isset($postData['search']) ){
			$selected_company = $this->session->userdata('root_company');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$q = $postData['search'];
			$this->db->select(db_prefix() . 'staff.*');
			$where_clients .= '('.db_prefix() . 'staff.AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR lastname LIKE "%' . $q . '%" ESCAPE \'!\'  )';
			$this->db->join(db_prefix() . 'accountsld', '' . db_prefix() . 'accountsld.AccountID = ' . db_prefix() . 'staff.AccountID');
			$this->db->where($where_clients);
			$this->db->where(db_prefix() . 'staff.staff_comp REGEXP',$regExp);
			$this->db->where(db_prefix() . 'accountsld.PlantID',$selected_company);
			$this->db->where(db_prefix() . 'accountsld.SLDTypeID','3');
			//$this->db->where(db_prefix() . 'accountsld.EngageID IS NULL', NULL, FALSE);
			$records = $this->db->get(db_prefix() . 'staff')->result();
			
			foreach($records as $row ){
				$fullname = $row->firstname." ".$row->lastname;
				$response[] = array("label"=>$fullname,"value"=>$row->AccountID);
			}
		}
		
		return $response;
	}
	
	public function get_Loader_Details($postData)
	{
		
		$AccountID = $postData['AccountID'];
		$selected_company = $this->session->userdata('root_company');
		$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
		$this->db->select(db_prefix() . 'staff.*');
		$this->db->join(db_prefix() . 'accountsld', '' . db_prefix() . 'accountsld.AccountID = ' . db_prefix() . 'staff.AccountID');
		$this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
		//$this->db->where(db_prefix() . 'staff.SubActGroupID', "1002503");
		$this->db->where(db_prefix() . 'staff.staff_comp REGEXP',$regExp);
		$this->db->where(db_prefix() . 'accountsld.PlantID',$selected_company);
		$this->db->where(db_prefix() . 'accountsld.SLDTypeID','2');
		//$this->db->where(db_prefix() . 'accountsld.EngageID IS NULL', NULL, FALSE);
		return $this->db->get(db_prefix() . 'staff')->row();
		
	}
	
	function accountlist_Loader($postData){
		
		$response = array();
		
		$where_clients = '';
		
		if(isset($postData['search']) ){
			$selected_company = $this->session->userdata('root_company');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$q = $postData['search'];
			$this->db->select(db_prefix() . 'staff.*');
			$where_clients .= '('.db_prefix() . 'staff.AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR lastname LIKE "%' . $q . '%" ESCAPE \'!\'  )';
			$this->db->join(db_prefix() . 'accountsld', '' . db_prefix() . 'accountsld.AccountID = ' . db_prefix() . 'staff.AccountID');
			$this->db->where($where_clients);
			$this->db->where(db_prefix() . 'staff.staff_comp REGEXP',$regExp);
			$this->db->where(db_prefix() . 'accountsld.PlantID',$selected_company);
			$this->db->where(db_prefix() . 'accountsld.SLDTypeID','2');
			//$this->db->where(db_prefix() . 'accountsld.EngageID IS NULL', NULL, FALSE);
			$records = $this->db->get(db_prefix() . 'staff')->result();
			
			foreach($records as $row ){
				$fullname = $row->firstname." ".$row->lastname;
				$response[] = array("label"=>$fullname,"value"=>$row->AccountID);
			}
		}
		
		return $response;
	}
	
	function accountlist_salesMan($postData){
		
		$response = array();
		
		$where_clients = '';
		
		if(isset($postData['search']) ){
			$selected_company = $this->session->userdata('root_company');
			$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
			$q = $postData['search'];
			$this->db->select(db_prefix() . 'staff.*');
			$where_clients .= '('.db_prefix() . 'staff.AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR lastname LIKE "%' . $q . '%" ESCAPE \'!\'  )';
			$this->db->join(db_prefix() . 'accountsld', '' . db_prefix() . 'accountsld.AccountID = ' . db_prefix() . 'staff.AccountID');
			$this->db->where($where_clients);
			$this->db->where(db_prefix() . 'staff.staff_comp REGEXP',$regExp);
			$this->db->where(db_prefix() . 'accountsld.PlantID',$selected_company);
			$this->db->where(db_prefix() . 'accountsld.SLDTypeID','1');
			$this->db->where(db_prefix() . 'accountsld.EngageID IS NULL', NULL, FALSE);
			$records = $this->db->get(db_prefix() . 'staff')->result();
			
			foreach($records as $row ){
				$fullname = $row->firstname." ".$row->lastname;
				$response[] = array("label"=>$fullname,"value"=>$row->AccountID);
			}
		}
		
		return $response;
	}
	
	public function GetTaxableTransaction($postData)
	{
		
		$ChallanID = $postData['ChallanID'];
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select(db_prefix() . 'salesmaster.*,'.db_prefix() . 'clients.vat,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.address,'.db_prefix() . 'clients.address3,'.db_prefix() . 'clients.zip,'.db_prefix() . 'contacts.kms,'.db_prefix() . 'contacts.email,'.db_prefix() . 'clients.city,'.db_prefix() .'xx_citylist.city_name,'.db_prefix() .'xx_statelist.id As StateId,tblchallanmaster.VehicleID');
		$this->db->from(db_prefix() . 'salesmaster');
		$this->db->join(db_prefix() . 'challanmaster', db_prefix() . 'salesmaster.ChallanID = '.db_prefix() . 'challanmaster.ChallanID');
		$this->db->join(db_prefix() . 'clients', db_prefix() . 'salesmaster.AccountID = '.db_prefix() . 'clients.AccountID AND '.db_prefix() . 'salesmaster.PlantID = '.db_prefix() . 'clients.PlantID');
		
		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'clients.AccountID = '.db_prefix() . 'contacts.AccountID AND '.db_prefix() . 'clients.PlantID = '.db_prefix() . 'contacts.PlantID');
		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'clients.city = '.db_prefix() . 'xx_citylist.id','LEFT');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'clients.state = '.db_prefix() . 'xx_statelist.short_name');
		$this->db->where(db_prefix() . 'salesmaster.PlantID',$selected_company);
		$this->db->where(db_prefix() . 'salesmaster.FY',$fy);
		$this->db->where(db_prefix() . 'salesmaster.BT',"T");
		$this->db->where(db_prefix() . 'salesmaster.ChallanID',$ChallanID);
		return $this->db->get()->result_array();
		
	}
	public function GetTaxableNonTaxableTransaction($postData)
	{
		
		$ChallanID = $postData['ChallanID'];
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select(db_prefix() . 'salesmaster.*,'.db_prefix() . 'clients.vat,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.address,'.db_prefix() . 'clients.address3,'.db_prefix() . 'clients.zip,'.db_prefix() . 'contacts.kms,'.db_prefix() . 'contacts.email,'.db_prefix() . 'clients.city,'.db_prefix() .'xx_citylist.city_name,'.db_prefix() .'xx_statelist.id As StateId,tblchallanmaster.VehicleID');
		$this->db->from(db_prefix() . 'salesmaster');
		$this->db->join(db_prefix() . 'challanmaster', db_prefix() . 'salesmaster.ChallanID = '.db_prefix() . 'challanmaster.ChallanID');
		$this->db->join(db_prefix() . 'clients', db_prefix() . 'salesmaster.AccountID = '.db_prefix() . 'clients.AccountID AND '.db_prefix() . 'salesmaster.PlantID = '.db_prefix() . 'clients.PlantID');
		
		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'clients.AccountID = '.db_prefix() . 'contacts.AccountID AND '.db_prefix() . 'clients.PlantID = '.db_prefix() . 'contacts.PlantID');
		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'clients.city = '.db_prefix() . 'xx_citylist.id','LEFT');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'clients.state = '.db_prefix() . 'xx_statelist.short_name');
		$this->db->where(db_prefix() . 'salesmaster.PlantID',$selected_company);
		$this->db->where(db_prefix() . 'salesmaster.FY',$fy);
		$this->db->where(db_prefix() . 'salesmaster.ChallanID',$ChallanID);
		return $this->db->get()->result_array();
		
	}
	//-----------------------------------------------------
	public function GetSalesByIRN($irn){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('*');
		$this->db->from(db_prefix() . 'salesmaster');
		$this->db->where(db_prefix() . 'salesmaster.irn', $irn);
		$this->db->where(db_prefix() . 'salesmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'salesmaster.FY', $fy);
		return $this->db->get()->row();
		
	}
	//-----------------------------------------------------
	public function GetSalesByEwaybill($ewayno){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('*');
		$this->db->from(db_prefix() . 'salesmaster');
		$this->db->where(db_prefix() . 'salesmaster.ewaybill_no', $ewayno);
		$this->db->where(db_prefix() . 'salesmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'salesmaster.FY', $fy);
		return $this->db->get()->row();
		
	}
	public function get_Account_Details_salesman($postData)
	{
		
		$AccountID = $postData['AccountID'];
		$selected_company = $this->session->userdata('root_company');
		$regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
		$this->db->select(db_prefix() . 'staff.*');
		$this->db->join(db_prefix() . 'accountsld', '' . db_prefix() . 'accountsld.AccountID = ' . db_prefix() . 'staff.AccountID');
		$this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
		//$this->db->where(db_prefix() . 'staff.SubActGroupID', "1002503");
		$this->db->where(db_prefix() . 'staff.staff_comp REGEXP',$regExp);
		$this->db->where(db_prefix() . 'accountsld.PlantID',$selected_company);
		$this->db->where(db_prefix() . 'accountsld.SLDTypeID','1');
		$this->db->where(db_prefix() . 'accountsld.EngageID IS NULL', NULL, FALSE);
		return $this->db->get(db_prefix() . 'staff')->row();
		
	}
	public function getchallandetail($id = '', $where = [])
	{
		$this->db->select('tblchallanmaster.*,CONCAT_WS(" ", tblstaff.firstname, tblstaff.lastname) as DriverName');
		$this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.AccountID = ' . db_prefix() . 'challanmaster.DriverID','LEFT');
		$this->db->where('ChallanID', $id);
		$result = $this->db->get(db_prefix() . 'challanmaster')->row();
		return $result;
		
	}
	
	public function get_ledger_data($id)
	{
		$query = $this->db->get_where('tblaccountledger', array('VoucherID' => $id));
		$ledger_data = $query->result_array();
		
		return $ledger_data;
	}
	
	/**
		* Get invoice by id
		* @param  mixed $id
		* @return array|object
	*/
	public function getorder_by_route($id = '', $where = [])
	{
		$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'order.id as id, ' . db_prefix() . 'currencies.name as currency_name');
		$this->db->from(db_prefix() . 'order');
		$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'order.currency', 'left');
		$this->db->where($where);
		if (is_numeric($id)) {
			$this->db->where(db_prefix() . 'order' . '.id', $id);
			$invoice = $this->db->get()->row();
			if ($invoice) {
				$invoice->total_left_to_pay = get_invoice_total_left_to_pay($invoice->id, $invoice->total);
				
				$invoice->items       = get_items_by_type2('invoice', $id);
				$invoice->attachments = $this->get_attachments($id);
				
				if ($invoice->project_id != 0) {
					$this->load->model('projects_model');
					$invoice->project_data = $this->projects_model->get($invoice->project_id);
				}
				
				$invoice->visible_attachments_to_customer_found = false;
				foreach ($invoice->attachments as $attachment) {
					if ($attachment['visible_to_customer'] == 1) {
						$invoice->visible_attachments_to_customer_found = true;
						
						break;
					}
				}
				
				$client          = $this->clients_model->get($invoice->clientid);
				$invoice->client = $client;
				if (!$invoice->client) {
					$invoice->client          = new stdClass();
					$invoice->client->company = $invoice->deleted_customer_name;
				}
				
				$this->load->model('payments_model');
				$invoice->payments = $this->payments_model->get_invoice_payments($id);
				
				$this->load->model('email_schedule_model');
				$invoice->scheduled_email = $this->email_schedule_model->get($id, 'invoice');
			}
			
			return hooks()->apply_filters('get_invoice', $invoice);
		}
		
		$this->db->order_by('number,YEAR(date)', 'desc');
		
		return $this->db->get()->result_array();
	}
	
	
	
	//-----------------------------------------------------
	public function getorderdetail_by_accId($id){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('*');
		$this->db->from(db_prefix() . 'ordermaster');
		$this->db->where(db_prefix() . 'ordermaster.AccountID', $id);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', null);
		//$this->db->where(db_prefix() . 'ordermaster.ischallan', 0);
		return $this->db->get()->result_array();
	}
	
	//------------------- Get order id list using account Id --------------------------------
	
	public function getorderlist_by_accId($account_ids){
		
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('OrderID');
		$this->db->from(db_prefix() . 'ordermaster');
		$this->db->where_in('AccountID', $account_ids);
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $fy);
		$this->db->where('ChallanID', null);
		$this->db->where('OrderStatus', "O");
		return $this->db->get()->result_array();
	}
	
	
	
	//-----------------------------------------------------
	public function get_order_by_challan($id){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('*');
		$this->db->from(db_prefix() . 'ordermaster');
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', $id);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', "O");
		return $this->db->get()->result_array();
		
	}
	
	public function getorderdetail_by_orderId($id = '', $where = [])
	{
		$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'ordermaster.cd_applicable, ' . db_prefix() . 'currencies.name as currency_name');
		$this->db->from(db_prefix() . 'ordermaster');
		$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
		$this->db->where($where);
		if ($id) {
			$this->db->where(db_prefix() . 'ordermaster' . '.OrderID', $id);
			$order = $this->db->get()->row();
			if ($order) {
				$order->items       = $this->get_order_item_data($order->OrderID);
				$client          = $this->clients_model->get($order->AccountID);
				$order->client = $client;
			}
			return hooks()->apply_filters('get_invoice', $order);
		}
		
		//$this->db->order_by('YEAR(date)', 'desc');
		
		return $this->db->get()->result_array();
	}
	
	
	public function GetBilledOrderDetails($id = '', $where = [])
	{
		$this->db->select('tblordermaster.*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name,' . db_prefix() . 'clients.state,' . db_prefix() . 'clients.DistributorType,' . db_prefix() . 'clients.bill_till_bal,' . db_prefix() . 'clients.ActSalestype,' . db_prefix() . 'contacts.istcs,' . db_prefix() . 'accountbalances.BAL1');
		$this->db->from(db_prefix() . 'ordermaster');
		$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'accountbalances.FY = ' . db_prefix() . 'ordermaster.FY',"LEFT");
		$this->db->where($where);
		if ($id) {
			$this->db->where(db_prefix() . 'ordermaster' . '.OrderID', $id);
			$order = $this->db->get()->row();
			if ($order) {
				$order->items       = $this->GetOrderItems($order->OrderID);
				$order->Freeitems       = $this->GetOrderItemsFree($order->OrderID);
			}
			return hooks()->apply_filters('get_invoice', $order);
		}
		return $this->db->get()->result_array();
	}
	public function GetUnbilledOrderDetails($id = '', $where = [])
	{
		$this->db->select('tblordermaster.*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name,' . db_prefix() . 'clients.state,' . db_prefix() . 'clients.DistributorType,' . db_prefix() . 'clients.bill_till_bal,' . db_prefix() . 'clients.MaxCrdAmt,' . db_prefix() . 'clients.ActSalestype,' . db_prefix() . 'contacts.istcs,' . db_prefix() . 'accountbalances.BAL1');
		$this->db->from(db_prefix() . 'ordermaster');
		$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'ordermaster.PlantID AND ' . db_prefix() . 'accountbalances.FY = ' . db_prefix() . 'ordermaster.FY',"LEFT");
		$this->db->where($where);
		if ($id) {
			$this->db->where(db_prefix() . 'ordermaster.ChallanID IS NULL', NULL, FALSE);
			$this->db->where(db_prefix() . 'ordermaster.SalesID IS NULL', NULL, FALSE);
			$this->db->where(db_prefix() . 'ordermaster' . '.OrderID', $id);
			$order = $this->db->get()->row();
			if ($order) {
				$order->items       = $this->GetOrderItems($order->OrderID);
				$order->Freeitems       = $this->GetOrderItemsFree($order->OrderID);
				$order->client_actbal       = $this->get_accbal($order->AccountID,$order->PlantID,$order->FY);
			}
			return hooks()->apply_filters('get_invoice', $order);
		}
		return $this->db->get()->result_array();
	}
	public function get_accbal($AccountID,$PlantID,$FY){
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
		$bal = $Obal + $result1->dramt_sum - $result2->cramt_sum;
		return $bal;
	}
	
	public function getorderSum_by_orderId($orderID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('sum('.db_prefix() .'history.NetOrderAmt) AS OrderSum, sum('.db_prefix() .'history.OrderAmt) AS SaleAmtSum, sum('.db_prefix() .'history.cgstamt) AS cgstAmtSum, sum('.db_prefix() .'history.sgstamt) AS sgstAmtSum, sum('.db_prefix() .'history.igstamt) AS igstAmtSum,' . db_prefix() . 'contacts.istcs');
		$this->db->from(db_prefix() .'history');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'history.AccountID AND '.db_prefix() . 'contacts.PlantID = '.db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() .'history.OrderID', $orderID);
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		$orderSum = $this->db->get()->row();
		if($orderSum){
			$this->db->select(db_prefix() .'history.CaseQty,'.db_prefix() .'history.SuppliedIn,IFNULL('.db_prefix() .'history.eOrderQty,'.db_prefix() .'history.OrderQty) AS Qty');
			$this->db->from(db_prefix() .'history');
			$this->db->where(db_prefix() .'history.OrderID', $orderID);
			//$this->db->where(db_prefix() . 'history.BillID IS NULL', NULL, FALSE);
			//$this->db->where(db_prefix() . 'history.TransID IS NULL', NULL, FALSE);
			$this->db->where(db_prefix() .'history.PlantID', $selected_company);
			$this->db->where(db_prefix() .'history.FY', $fy);
			$OrderDetails = $this->db->get()->result_array();
			$casesSum = 0;
			$cratesSum = 0;
			foreach ($OrderDetails as $key => $value) {
				if($value['SuppliedIn']=='CR'){
					$crates =  $value['Qty'] ;// / $value['CaseQty'] Add If needed
					$cratesSum = $cratesSum + $crates;
					}else{
					$cases =  $value['Qty'] ; // / $value['CaseQty'] Add If Needed
					$casesSum = $casesSum + $cases;
				}
			}
			$orderSum->crateSum = $cratesSum;
			$orderSum->casesSum = $casesSum;
		}
		return $orderSum;
	}
	
	public function get_challan_item_data($challanID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'history.*');
		$this->db->from(db_prefix() .'history');
		$this->db->where(db_prefix() .'history.BillID', $challanID);
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		return $this->db->get()->result_array();
	}
	public function get_OrderItem_data($OrderID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'history.*');
		$this->db->from(db_prefix() .'history');
		$this->db->where(db_prefix() .'history.OrderID', $OrderID);
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		return $this->db->get()->result_array();
	}
	public function get_ledgerDetails($SalesID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'accountledger.*');
		$this->db->from(db_prefix() .'accountledger');
		$this->db->where(db_prefix() .'accountledger.VoucherID', $SalesID);
		$this->db->where(db_prefix() .'accountledger.PlantID', $selected_company);
		$this->db->where(db_prefix() .'accountledger.FY', $fy);
		return $this->db->get()->result_array();
	}
	public function getCratesDetails($SalesID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'accountcrates.*');
		$this->db->from(db_prefix() .'accountcrates');
		$this->db->where(db_prefix() .'accountcrates.VoucherID', $SalesID);
		$this->db->where(db_prefix() .'accountcrates.PlantID', $selected_company);
		$this->db->where(db_prefix() .'accountcrates.FY', $fy);
		return $this->db->get()->row();
	}
	
	public function get_order_item_data($orderID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		
		$this->db->select(db_prefix() .'history.*, '.db_prefix() . 'stockmaster.*');
		$this->db->from(db_prefix() .'history');
		$this->db->join(db_prefix() . 'stockmaster', '' . db_prefix() . 'stockmaster.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'stockmaster.PlantID = ' . db_prefix() . 'history.PlantID AND ' . db_prefix() . 'stockmaster.FY = ' . db_prefix() . 'history.FY');
		$this->db->where(db_prefix() .'history.OrderID', $orderID);
		$this->db->where(db_prefix() . 'stockmaster.GodownID',$GodownID);
		//$this->db->where(db_prefix() . 'history.GodownID',$GodownID);
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		$this->db->where(db_prefix() .'history.TType', 'O');
		$this->db->where(db_prefix() .'history.TType2', 'Order');
		return $this->db->get()->result_array();
	}
	
	public function GetOrderItems($orderID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'history.*,'.db_prefix() .'items.monitorstock');
		$this->db->from(db_prefix() .'history');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() .'history.OrderID', $orderID);
		$this->db->where(db_prefix() .'history.TType', 'O');
		$this->db->where(db_prefix() .'history.TType2', 'Order');
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		return $this->db->get()->result_array();
	}
	public function GetOrderItemsFree($orderID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'history.*,'.db_prefix() .'items.monitorstock');
		$this->db->from(db_prefix() .'history');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() .'history.OrderID', $orderID);
		$this->db->where(db_prefix() .'history.TType', 'O');
		$this->db->where(db_prefix() .'history.TType2', 'Free Distribution');
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		return $this->db->get()->result_array();
	}
	
	//-----------------------------------------------------
	public function get_acc_by_route($id){
		$selected_company = $this->session->userdata('root_company');
		$query = $this->db->get_where(db_prefix() . 'accountroutes', array('RouteID' => $id,'PlantID' => $selected_company));
		return $result = $query->result_array();
	}
	public function GetChallanList()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$curDate = date('Y-m-d H:i:s');
		$preDate = date('Y-m-d', strtotime(' -10 day'))." 00:00:00";
		$this->db->select('*');
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $fy);
		$this->db->where('Transdate <=', $curDate);
		$this->db->where('Transdate >=', $preDate);
		return $this->db->get(db_prefix() . 'challanmaster')->result_array();
	}
	
	public function GetVehicleByChallan($ChallanID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('tblchallanmaster.*, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as Driver');
		
		$this->db->join(db_prefix() . 'staff', '' . db_prefix() . 'staff.AccountID = ' . db_prefix() . 'challanmaster.DriverID AND ' . db_prefix() . 'staff.PlantID = ' . db_prefix() . 'challanmaster.PlantID','left');
		$this->db->where('tblchallanmaster.PlantID', $selected_company);
		$this->db->where('FY', $fy);
		$this->db->where('ChallanID', $ChallanID);
		return $this->db->get(db_prefix() . 'challanmaster')->row();
	}
	
	// Update VehicleID By ChallanID
	public function UpdateVehicle($VehData,$VehicleNo,$ChallanID)
	{
		$selected_company = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$UPDATE = 0; 
		$this->db->where('ChallanID', $ChallanID);
		$this->db->where('VehicleID', $VehicleNo);
		$this->db->where('FY', $FY);
		$this->db->where('PlantID', $selected_company);
		$this->db->update(db_prefix() . 'challanmaster', $VehData);
		if($this->db->affected_rows() > 0){
			return true;
			}else{
			return false;
		}
	}
	public function update_rate($RCHID){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$new_record = str_replace(" ,",'',$RCHID);
		$new_record_array = explode(',', $new_record);
		$this->db->select(db_prefix() . 'history.ItemID,'.db_prefix() . 'clients.state,'.db_prefix() . 'clients.DistributorType,'.db_prefix() . 'history.OrderID, IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as OrderQty,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.cgst');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'history.FY', $fy);
		$this->db->where_in(db_prefix() . 'history.OrderID', $new_record_array);
		$item_list = $this->db->get(db_prefix() . 'history')->result_array();
		// print_r($item_list);die;
		// return $item_list;
		$Order_item = array();
		$affectedRows = 0;
		
		foreach ($item_list as $key1 => $code1) {
			$match = 0;
			$curDate = date('Y-m-d H:i:s');
			$this->db->select('*');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('effective_date <=', $curDate);
			$this->db->where('item_id', $code1["ItemID"]);
			$this->db->order_by('effective_date DESC, id DESC');
			$rate_item =  $this->db->get(db_prefix() . 'rate_master')->result_array();
			//return $rate_item;
			/*if(empty($rate_item)){
				$this->db->select('*');
				$this->db->where('PlantID', $selected_company);
				$this->db->where('EffDate <=', $curDate);
				$this->db->where('ItemID', $code1["ItemID"]);
				$this->db->where('EffDate', 'DESC');
				$rate_item_his =  $this->db->get(db_prefix() . 'ratehistory2')->result_array();
				
				if($rate_item_his["ItemID"] == $code1["ItemID"] && $code1["state_id"] == $rate_item_his["StateID"] && $rate_item_his["DistributorType"] == $code1["DistributorType"]){
				
				if($code1["igst"] == NULL){
				$gst =  $code1["cgst"] *2;
				}else{
				$gst =  $code1["igst"];
				}
				$orderAmt = $code1["OrderQty"] * $rate_item_his["BasicRate"];
				$gstAmt = ($orderAmt /100)* $gst;
				$netAmt = $orderAmt + $gstAmt;
				if($code1["igst"] == NULL){
				$gst_d = $gstAmt / 2;
				$update_array = array(
				"BasicRate"=>$rate_item_his["BasicRate"],
				"OrderAmt"=>$orderAmt,
				"NetOrderAmt"=>$netAmt,
				"cgstamt"=>$gst_d,
				"sgstamt"=>$gst_d,
				);
				}else{
				$update_array = array(
				"BasicRate"=>$rate_item_his["BasicRate"],
				"OrderAmt"=>$orderAmt,
				"NetOrderAmt"=>$netAmt,
				"igstamt"=>$gstAmt,
				);
				}
				$this->db->where('PlantID', $selected_company);
				$this->db->where('FY', $fy);
				$this->db->where('OrderID', $code1['OrderID']);
				$this->db->where('ItemID', $code1['ItemID']);
				$this->db->update(db_prefix() . 'history', $update_array);
				$affectedRows++;
				}
			}else{*/
			foreach ($rate_item as $key2 => $code2) {
				if($code2["item_id"] == $code1["ItemID"] && $code1["state"] == $code2["state_id"] && $code2["distributor_id"] == $code1["DistributorType"]){
					$match++;
					if($code1["igst"] == NULL){
						$gst =  $code1["cgst"] *2;
						}else{
						$gst =  $code1["igst"];
					}
					$orderAmt = $code1["OrderQty"] * $code2["assigned_rate"];
					$gstAmt = ($orderAmt /100)* $gst;
					$netAmt = $orderAmt + $gstAmt;
					if($code1["igst"] == NULL){
						$gst_d = $gstAmt / 2;
						$update_array = array(
						"BasicRate"=>$code2["assigned_rate"],
						"OrderAmt"=>$orderAmt,
						"NetOrderAmt"=>$netAmt,
						"cgstamt"=>$gst_d,
						"sgstamt"=>$gst_d,
						);
						}else{
						$update_array = array(
						"BasicRate"=>$code2["assigned_rate"],
						"OrderAmt"=>$orderAmt,
						"NetOrderAmt"=>$netAmt,
						"igstamt"=>$gstAmt,
						);
					}
					$this->db->where('PlantID', $selected_company);
					$this->db->where('FY', $fy);
					$this->db->where('OrderID', $code1['OrderID']);
					$this->db->where('ItemID', $code1['ItemID']);
					$this->db->update(db_prefix() . 'history', $update_array);
					$affectedRows++;
					//}
				}
			}
			if($match == "0"){
				$this->db->select('*');
				$this->db->where('PlantID', $selected_company);
				$this->db->where('EffDate <=', $curDate);
				$this->db->where('ItemID', $code1["ItemID"]);
				$this->db->order_by('EffDate', 'ASC');
				$rate_item_his =  $this->db->get(db_prefix() . 'ratehistory2')->result_array();
				//return $rate_item_his;
				foreach ($rate_item_his as $key3 => $code3) {
					if($code3["ItemID"] == $code1["ItemID"] && $code3["StateID"] == $code1["state"] && $code3["DistributorType"] == $code1["DistributorType"]){
						//$affectedRows++;  
						if($code1["igst"] == NULL){
							$gst =  $code1["cgst"] *2;
							}else{
							$gst =  $code1["igst"];
						}
						$orderAmt = $code1["OrderQty"] * $code3["BasicRate"];
						$gstAmt = ($orderAmt /100)* $gst;
						$netAmt = $orderAmt + $gstAmt;
						if($code1["igst"] == NULL){
							$gst_d = $gstAmt / 2;
							$update_array = array(
							"BasicRate"=>$code3["BasicRate"],
							"OrderAmt"=>$orderAmt,
							"NetOrderAmt"=>$netAmt,
							"cgstamt"=>$gst_d,
							"sgstamt"=>$gst_d,
							);
							}else{
							$update_array = array(
							"BasicRate"=>$code3["BasicRate"],
							"OrderAmt"=>$orderAmt,
							"NetOrderAmt"=>$netAmt,
							"igstamt"=>$gstAmt,
							);
						}
						//return $update_array;
						$this->db->where('PlantID', $selected_company);
						$this->db->where('FY', $fy);
						$this->db->where('OrderID', $code1['OrderID']);
						$this->db->where('ItemID', $code1['ItemID']);
						$this->db->update(db_prefix() . 'history', $update_array);
						//return $this->db->last_query();
						$affectedRows++;
					}
				}
			}
		}
		
		
		/*foreach ($item_list as $key1 => $code1) {
			array_push($Order_item, $code1["ItemID"]);
		}*/
		/*$curDate = date('Y-m-d H:i:s');
			$this->db->select('*');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('effective_date <=', $curDate);
			$this->db->where_in('item_id', $Order_item);
			$rate_item =  $this->db->get(db_prefix() . 'rate_master')->result_array();
			
			foreach ($rate_item as $key2 => $code2) {
			foreach ($item_list as $key3 => $code3) {
			if($code2["item_id"] == $code3["ItemID"] && $code2["state_id"] == $code3["state"] && $code2["distributor_id"] == $code3["DistributorType"]){
			if($code3["igst"] == NULL){
			$gst =  $code3["cgst"] *2;
			}else{
			$gst =  $code3["igst"];
			}
			$orderAmt = $code3["OrderQty"] * $code2["assigned_rate"];
			$gstAmt = ($orderAmt /100)* $gst;
			$netAmt = $orderAmt + $gstAmt;
			if($code3["igst"] == NULL){
			$gst_d = $gstAmt / 2;
			$update_array = array(
			"BasicRate"=>$code2["assigned_rate"],
			"OrderAmt"=>$orderAmt,
			"NetOrderAmt"=>$netAmt,
			"cgstamt"=>$gst_d,
			"sgstamt"=>$gst_d,
			);
			}else{
			$update_array = array(
			"BasicRate"=>$code2["assigned_rate"],
			"OrderAmt"=>$orderAmt,
			"NetOrderAmt"=>$netAmt,
			"igstamt"=>$gstAmt,
			);
			}
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('OrderID', $code3['OrderID']);
			$this->db->where('ItemID', $code3['ItemID']);
			$this->db->update(db_prefix() . 'history', $update_array);
			$affectedRows++;
			}
			}
		}*/
		foreach ($new_record_array as $value) {
			
			$this->db->select('SUM(NetOrderAmt) AS Sum');
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('OrderID', $value);
			$orderSum =  $this->db->get(db_prefix() . 'history')->row();
			$update_order = array(
			"OrderAmt"=>$orderSum->Sum
			);
			$this->db->where('PlantID', $selected_company);
			$this->db->where('FY', $fy);
			$this->db->where('OrderID', $value);
			$this->db->update(db_prefix() . 'ordermaster', $update_order);
			// $affectedRows++;
		}
		if($affectedRows >0){
			return true;
			}else{
			return false;
		}
	}
	
	// New Code start 17-06-22
	
	public function CheckStockQty($ItemID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		
		$this->db->select('*');
		$this->db->from(db_prefix() . 'stockmaster');
		$this->db->where(db_prefix() . 'stockmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'stockmaster.FY', $fy);
		$this->db->where(db_prefix() . 'stockmaster.cnfid', '1');
		$this->db->where('GodownID',$GodownID);
		$this->db->where(db_prefix() . 'stockmaster.ItemID ', $ItemID);
		return $this->db->get()->row();
	}
	function getStocksDetails($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		
		$this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');
		$this->db->from(db_prefix() .'history');
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'history.ItemID ', $id);
		$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);
		$this->db->where(db_prefix() .'history.FY', $fy);
		$this->db->where('GodownID',$GodownID);
		$this->db->group_by('ItemID,TType,TType2');
		return $this->db->get()->result_array();
	}
	function GetItemStockDetails($ItemID){
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		
		$this->db->select(db_prefix() .'history.ItemID,TType,TType2,SUM(BilledQty) AS BilledQty,'.db_prefix() .'history.CaseQty,'.db_prefix() . 'stockmaster.OQty');
		$this->db->from(db_prefix() .'history');
		$this->db->join(db_prefix() . 'stockmaster', '' . db_prefix() . 'stockmaster.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'stockmaster.PlantID = ' . db_prefix() . 'history.PlantID AND ' . db_prefix() . 'stockmaster.FY = ' . db_prefix() . 'history.FY');
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'history.ItemID ', $ItemID);
		$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);
		$this->db->where(db_prefix() .'history.FY', $fy);
		$this->db->where(db_prefix() . 'stockmaster.GodownID',$GodownID);
		//$this->db->where(db_prefix() . 'history.GodownID',$GodownID);
		$this->db->group_by(db_prefix() .'history.ItemID,'.db_prefix() .'history.TType,'.db_prefix() .'history.TType2');
		$checkStockDetails = $this->db->get()->result_array();
		// return $checkStockDetails;
		$PQty = 0;
		$PRQty = 0;
		$IQty = 0;
		$PRDQty = 0;
		$SQty = 0;
		$SRQty = 0;
		$ADJQTY = 0;
		$staockData = array();
		foreach ($checkStockDetails as $stock) {
			if($stock['TType'] == 'P'){
				$PQty = $stock['BilledQty'];
				}elseif($stock['TType'] == 'N'){
				$PRQty = $stock['BilledQty'];
				}elseif($stock['TType'] == 'A'){
				$IQty = $stock['BilledQty'];
				}elseif($stock['TType'] == 'B'){
				$PRDQty = $stock['BilledQty'];
				}elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){
				$SQty = $stock['BilledQty'];
				}elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){
				$SRQty = $stock['BilledQty'];
				}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment'){
				$ADJQTY += $stock['BilledQty'];
				}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity'){
				$ADJQTY += $stock['BilledQty'];
				}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free Distribution'){
				$ADJQTY += $stock['BilledQty'];
			}
			$balance = (float) $stock['OQty'] + (float) $PQty - (float) $PRQty - (float) $IQty +  (float) $PRDQty - (float) $SQty + (float) $SRQty - (float) $ADJQTY;
			$balCase = $balance / $stock['CaseQty'];
		}
		
		//$staockData[$stock['ItemID']] = $balCase;
		return $balCase;
	}
	function GetStockDetails($Order_item){
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		$this->db->select(db_prefix() .'history.ItemID,TType,TType2,SUM(BilledQty) AS BilledQty,CaseQty,'.db_prefix() . 'stockmaster.OQty');
		$this->db->from(db_prefix() .'history');
		$this->db->join(db_prefix() . 'stockmaster', '' . db_prefix() . 'stockmaster.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'stockmaster.PlantID = ' . db_prefix() . 'history.PlantID AND ' . db_prefix() . 'stockmaster.FY = ' . db_prefix() . 'history.FY');
		//$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID ');
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where_in(db_prefix() . 'history.ItemID ', $Order_item);
		$this->db->where(db_prefix() . 'history.BillID IS NOT NULL', NULL, FALSE);
		$this->db->where(db_prefix() .'history.FY', $fy);
		$this->db->where(db_prefix() . 'stockmaster.GodownID',$GodownID);
		//$this->db->where(db_prefix() . 'history.GodownID',$GodownID);
		$this->db->group_by(db_prefix() .'history.ItemID,'.db_prefix() .'history.TType,,'.db_prefix() .'history.TType2');
		$checkStockDetails = $this->db->get()->result_array();
		return $checkStockDetails;
	}
	
	public function ChallanDetails($ChallanID){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		
		$this->db->select(db_prefix() . 'ordermaster.*,'.db_prefix() . 'challanmaster.Transdate AS TransDate,'.db_prefix() . 'challanmaster.ChallanID');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.ChallanID = ' . db_prefix() . 'challanmaster.ChallanID');
		$this->db->where(db_prefix() . 'challanmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		//$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'challanmaster.FY', $fy);
		$this->db->where(db_prefix() . 'challanmaster.ChallanID', $ChallanID);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->order_by(db_prefix() . 'ordermaster.OrderID', 'DESC');
		$order_ids = $this->db->get(db_prefix() . 'challanmaster')->result_array();
		
		return $order_ids;
	}
	
	public function getNew($ChallanID){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() . 'challanmaster.ChallanID,'.db_prefix() . 'history.AccountID,'.db_prefix() . 'history.ItemID,IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as orderqty,'.db_prefix() . 'history.BilledQty,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.BasicRate,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.sgst,'.db_prefix() . 'history.cgst,'.db_prefix() . 'items.local_supply_in,'.db_prefix() . 'items.outst_supply_in,
		'.db_prefix() . 'history.DiscAmt,'.db_prefix() . 'history.DiscPerc,'.db_prefix() . 'history.OrderAmt,,'.db_prefix() . 'history.NetOrderAmt,'.db_prefix() . 'history.ChallanAmt,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.sgstamt,'.db_prefix() . 'history.igstamt');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'ordermaster', ''. db_prefix() . 'ordermaster.ChallanID = ' . db_prefix() . 'challanmaster.ChallanID');
		
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.BillID = ' . db_prefix() . 'challanmaster.ChallanID');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		
		$this->db->where(db_prefix() . 'challanmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'challanmaster.FY', $fy);
		$this->db->where(db_prefix() . 'challanmaster.ChallanID', $ChallanID);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.TType', 'O');
		$this->db->where(db_prefix() . 'history.TType2', 'Order');
		$item_list = $this->db->get(db_prefix() . 'challanmaster')->result_array();
		
		
		$this->db->select(db_prefix() . 'challanmaster.ChallanID,'.db_prefix() . 'history.AccountID,'.db_prefix() . 'history.ItemID,IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as orderqty,'.db_prefix() . 'history.BilledQty,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.BasicRate,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.sgst,'.db_prefix() . 'history.cgst,'.db_prefix() . 'items.local_supply_in,'.db_prefix() . 'items.outst_supply_in,
		'.db_prefix() . 'history.DiscAmt,'.db_prefix() . 'history.DiscPerc,'.db_prefix() . 'history.OrderAmt,,'.db_prefix() . 'history.NetOrderAmt,'.db_prefix() . 'history.ChallanAmt,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.sgstamt,'.db_prefix() . 'history.igstamt');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'ordermaster', ''. db_prefix() . 'ordermaster.ChallanID = ' . db_prefix() . 'challanmaster.ChallanID');
		
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.BillID = ' . db_prefix() . 'challanmaster.ChallanID');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		
		$this->db->where(db_prefix() . 'challanmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'challanmaster.FY', $fy);
		$this->db->where(db_prefix() . 'challanmaster.ChallanID', $ChallanID);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.TType', 'O');
		$this->db->where(db_prefix() . 'history.TType2', 'Free Distribution');
		$free_list = $this->db->get(db_prefix() . 'challanmaster')->result_array();
		
		$this->db->select(db_prefix() . 'ordermaster.*,'.db_prefix() . 'salesmaster.Transdate AS TransDate,'.db_prefix() . 'ordermaster.SalesID,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.state,'.db_prefix() . 'clients.DistributorType,'.db_prefix() . 'clients.MaxCrdAmt,'.db_prefix() . 'contacts.istcs,'.db_prefix() . 'salesmaster.irn,'.db_prefix() . 'salesmaster.ewaybill_no,'.db_prefix() . 'salesmaster.DeliveryPoint,tblaccountroutes.RouteID,tblroute.name As RouteName');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.ChallanID = ' . db_prefix() . 'challanmaster.ChallanID');
		$this->db->join(db_prefix() . 'accountroutes', '' . db_prefix() . 'accountroutes.AccountID = ' . db_prefix() . 'ordermaster.AccountID');
		$this->db->join(db_prefix() . 'route', '' . db_prefix() . 'route.RouteID = ' . db_prefix() . 'accountroutes.RouteID');
		$this->db->join(db_prefix() . 'salesmaster', '' . db_prefix() . 'salesmaster.SalesID = ' . db_prefix() . 'ordermaster.SalesID');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID');
		$this->db->where(db_prefix() . 'challanmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		//$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'challanmaster.FY', $fy);
		$this->db->where(db_prefix() . 'challanmaster.ChallanID', $ChallanID);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->order_by(db_prefix() . 'ordermaster.OrderID', 'DESC');
		$order_ids = $this->db->get(db_prefix() . 'challanmaster')->result_array();
		
		$result = array(
		"order_ids" =>$order_ids,
		"item_list" =>$item_list,
		"free_item_list" =>$free_list,
		);
		//$order_ids->item_code_list = $item_list;
		return $result;
	}
	
	public function get_order_by_routeNew($routeid)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() . 'history.ItemID,IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as orderqty,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.BasicRate,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.sgst,'.db_prefix() . 'history.cgst,'.db_prefix() . 'items.local_supply_in,'.db_prefix() . 'items.outst_supply_in,
		'.db_prefix() . 'history.DiscAmt,'.db_prefix() . 'history.DiscPerc,'.db_prefix() . 'history.OrderAmt,,'.db_prefix() . 'history.NetOrderAmt,'.db_prefix() . 'history.ChallanAmt,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.sgstamt,'.db_prefix() . 'history.igstamt,'.db_prefix() . 'ordermaster.AccountID,tblaccountroutes.RouteID');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'ordermaster.OrderID');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.TType', 'O');
		$this->db->where(db_prefix() . 'history.TType2', 'Order');
		$item_list = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$this->db->select(db_prefix() . 'history.ItemID,IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as orderqty,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.BasicRate,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.sgst,'.db_prefix() . 'history.cgst,'.db_prefix() . 'items.local_supply_in,'.db_prefix() . 'items.outst_supply_in,
		'.db_prefix() . 'history.DiscAmt,'.db_prefix() . 'history.DiscPerc,'.db_prefix() . 'history.OrderAmt,,'.db_prefix() . 'history.NetOrderAmt,'.db_prefix() . 'history.ChallanAmt,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.sgstamt,'.db_prefix() . 'history.igstamt,'.db_prefix() . 'ordermaster.AccountID,tblaccountroutes.RouteID');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'ordermaster.OrderID');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.TType', 'O');
		$this->db->where(db_prefix() . 'history.TType2', 'Free Distribution');
		$free_list = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$this->db->select(db_prefix() . 'ordermaster.*,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.MaxCrdAmt,'.db_prefix() . 'clients.state,'.db_prefix() . 'clients.DistributorType,'.db_prefix() . 'contacts.istcs,tblaccountroutes.RouteID,tblroute.name As RouteName');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		$this->db->join(db_prefix() . 'route', '' . db_prefix() . 'route.RouteID = ' . db_prefix() . 'accountroutes.RouteID');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->order_by(db_prefix() . 'ordermaster.OrderID', 'DESC');
		$this->db->group_by(db_prefix() . 'ordermaster.OrderID');
		$order_ids = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$result = array(
		"order_ids" =>$order_ids,
		"item_list" =>$item_list,
		"free_item_list" =>$free_list,
		);
		//$order_ids->item_code_list = $item_list;
		return $result;
	}
	public function get_order_by_routeNewAll()
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() . 'history.ItemID,IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as orderqty,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.BasicRate,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.sgst,'.db_prefix() . 'history.cgst,'.db_prefix() . 'items.local_supply_in,'.db_prefix() . 'items.outst_supply_in,
		'.db_prefix() . 'history.DiscAmt,'.db_prefix() . 'history.DiscPerc,'.db_prefix() . 'history.OrderAmt,,'.db_prefix() . 'history.NetOrderAmt,'.db_prefix() . 'history.ChallanAmt,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.sgstamt,'.db_prefix() . 'history.igstamt,'.db_prefix() . 'ordermaster.AccountID,tblaccountroutes.RouteID,tblaccountroutes.RouteID');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'ordermaster.OrderID');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		// $this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.TType', 'O');
		$this->db->where(db_prefix() . 'history.TType2', 'Order');
		$item_list = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$this->db->select(db_prefix() . 'history.ItemID,IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as orderqty,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.BasicRate,'.db_prefix() . 'history.igst,'.db_prefix() . 'history.sgst,'.db_prefix() . 'history.cgst,'.db_prefix() . 'items.local_supply_in,'.db_prefix() . 'items.outst_supply_in,
		'.db_prefix() . 'history.DiscAmt,'.db_prefix() . 'history.DiscPerc,'.db_prefix() . 'history.OrderAmt,,'.db_prefix() . 'history.NetOrderAmt,'.db_prefix() . 'history.ChallanAmt,'.db_prefix() . 'history.cgstamt,'.db_prefix() . 'history.sgstamt,'.db_prefix() . 'history.igstamt,'.db_prefix() . 'ordermaster.AccountID,tblaccountroutes.RouteID');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'ordermaster.OrderID');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		// $this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.TType', 'O');
		$this->db->where(db_prefix() . 'history.TType2', 'Free Distribution');
		$free_list = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$this->db->select(db_prefix() . 'ordermaster.*,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.MaxCrdAmt,'.db_prefix() . 'clients.state,'.db_prefix() . 'clients.DistributorType,'.db_prefix() . 'contacts.istcs,tblaccountroutes.RouteID,tblroute.name As RouteName');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		$this->db->join(db_prefix() . 'route', '' . db_prefix() . 'route.RouteID = ' . db_prefix() . 'accountroutes.RouteID');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		// $this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->order_by(db_prefix() . 'ordermaster.OrderID', 'DESC');
		$this->db->group_by(db_prefix() . 'ordermaster.OrderID');
		$order_ids = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$result = array(
		"order_ids" =>$order_ids,
		"item_list" =>$item_list,
		"free_item_list" =>$free_list,
		);
		//$order_ids->item_code_list = $item_list;
		return $result;
	}
	
	public function get_order_Item_rateNew($Order_item)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$curDate = date('Y-m-d H:i:s');
		$this->db->select('*');
		$this->db->where('effective_date <=', $curDate);
		$this->db->where('PlantID', $selected_company);
		$this->db->where_in('item_id', $Order_item);
		$this->db->order_by('effective_date DESC, id DESC');
		
		return $this->db->get(db_prefix() . 'rate_master')->result_array();
	}
	
	public function get_tcsperNew()
	{
		$c_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->where('EffDate <=',date('Y-m-d'));
		$this->db->from(db_prefix() . 'tcsmaster');
		$this->db->order_by('id',"desc");
		//$this->db->limit(1);
		return $this->db->get()->result_array();
	}
	
	public function get_order_Item_rate_historyNew($Order_item)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$curDate = date('Y-m-d H:i:s');
		$this->db->select('*');
		$this->db->where('EffDate <=', $curDate);
		$this->db->where('PlantID', $selected_company);
		$this->db->where('ItemID', $Order_item);
		$this->db->order_by('EffDate', 'DESC');
		return $this->db->get(db_prefix() . 'ratehistory2')->row();
	}
	
	public function get_itemcout_all_orderNew($routeid,$code)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('SUM(IFNULL('.db_prefix().'history.eOrderQty / '.db_prefix().'history.CaseQty, '.db_prefix().'history.OrderQty / '.db_prefix().'history.CaseQty)) AS OrderQty');
		$this->db->distinct();
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'ordermaster.OrderID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.ItemID', $code);
		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
		$item_list = $this->db->get(db_prefix() . 'accountroutes')->row();
		
		return $item_list;
		
	}
	function GetItemSum($OrderIds)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('SUM(IFNULL(eOrderQty,OrderQty)) AS OrderQty,CaseQty,ItemID');
		$this->db->where_in('OrderID', $OrderIds);
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $fy);
		$this->db->where('TType', 'O');
		$this->db->where('TType2', 'Order');
		$this->db->group_by('ItemID');
		$result = $this->db->get(db_prefix() . 'history')->result_array();
		return $result;
	}
	function GetItemSumFree($OrderIds)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('SUM(IFNULL(eOrderQty,OrderQty)) AS OrderQty,CaseQty,ItemID');
		$this->db->where_in('OrderID', $OrderIds);
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $fy);
		$this->db->where('TType', 'O');
		$this->db->where('TType2', 'Free Distribution');
		$this->db->group_by('ItemID');
		$result = $this->db->get(db_prefix() . 'history')->result_array();
		return $result;
	}
	
	function GetAccountBalancec($AccountIds)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select(db_prefix() . 'accountbalances.BAL1,'.db_prefix() . 'accountbalances.AccountID');
		$this->db->where_in(db_prefix() . 'accountbalances.AccountID', $AccountIds);
		$this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountbalances.FY', $fy);
		$this->db->group_by(db_prefix() . 'accountbalances.AccountID');
		$OBalance = $this->db->get(db_prefix() . 'accountbalances')->result_array();
		
		$this->db->select('SUM('.db_prefix() . 'accountledger.Amount) AS CRAmt,'.db_prefix() . 'accountledger.AccountID');
		$this->db->where_in(db_prefix() . 'accountledger.AccountID', $AccountIds);
		$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountledger.FY', $fy);
		$this->db->where(db_prefix() . 'accountledger.TType', "C");
		$this->db->group_by(db_prefix() . 'accountledger.AccountID');
		$CRresult = $this->db->get(db_prefix() . 'accountledger')->result_array();
		
		$this->db->select('SUM('.db_prefix() . 'accountledger.Amount) AS DRAmt,'.db_prefix() . 'accountledger.AccountID');
		$this->db->where_in(db_prefix() . 'accountledger.AccountID', $AccountIds);
		$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountledger.FY', $fy);
		$this->db->where(db_prefix() . 'accountledger.TType', "D");
		$this->db->group_by(db_prefix() . 'accountledger.AccountID');
		$DRresult = $this->db->get(db_prefix() . 'accountledger')->result_array();
		$result = array();
		foreach ($AccountIds as $AccountID) {
			$CAmt = 0;
			$DAmt = 0;
			$OBAL = 0;
			foreach ($CRresult as $key => $value) {
				if($value["AccountID"]===$AccountID){
					$CAmt = $value["CRAmt"];
				}
			}
			
			foreach ($DRresult as $key1 => $value1) {
				if($value1["AccountID"]===$AccountID){
					$DAmt = $value1["DRAmt"];
				}
			}
			foreach ($OBalance as $key1 => $value2) {
				if($value2["AccountID"]===$AccountID){
					$OBAL = $value2["BAL1"];
				}
			}
			
			$Balance = $OBAL - $CAmt + $DAmt;
			# code...
			$Array = array(
			"AccountID"=>$AccountID,
			"Balance"=>$Balance
			);
			array_push($result, $Array);
		}
		return $result;
	}
	
	// End New code 17-06-22
	
	//-----------------------------------------------------
	public function get_order_by_route($routeid){
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() . 'history.ItemID,IFNULL('.db_prefix() . 'history.eOrderQty,'.db_prefix() . 'history.OrderQty) as orderqty,'.db_prefix() . 'history.OrderID,'.db_prefix() . 'history.CaseQty,'.db_prefix() . 'history.BasicRate');
		$this->db->distinct(db_prefix() . 'history.ItemID');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		//$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'ordermaster.OrderID');
		//$this->db->join(db_prefix() . 'customers_groups', '' . db_prefix() . 'customers_groups.id = ' . db_prefix() . 'clients.DistributorType AND ' . db_prefix() . 'customers_groups.PlantID = ' . db_prefix() . 'clients.PlantID');
		//$this->db->join(db_prefix() . 'rate_master', '' . db_prefix() . 'rate_master.item_id = ' . db_prefix() . 'history.ItemID  AND ' . db_prefix() . 'rate_master.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$item_list = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$this->db->select(db_prefix() . 'ordermaster.*,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.state,'.db_prefix() . 'clients.DistributorType');
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'ordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'ordermaster.PlantID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$order_ids = $this->db->get(db_prefix() . 'accountroutes')->result_array();
		
		$result = array(
		"order_ids" =>$order_ids,
		"item_list" =>$item_list,
		);
		//$order_ids->item_code_list = $item_list;
		
		return $result;
	}
	
	public function get_order_Item_rate($Order_item)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$curDate = date('Y-m-d H:i:s');
		$this->db->select('*');
		$this->db->where('effective_date <=', $curDate);
		$this->db->where('PlantID', $selected_company);
		$this->db->where_in('item_id', $Order_item);
		
		return $this->db->get(db_prefix() . 'rate_master')->result_array();
	}
	public function get_order_Item_rate_history($Order_item)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$curDate = date('Y-m-d H:i:s');
		$this->db->select('*');
		$this->db->where('EffDate <=', $curDate);
		$this->db->where('PlantID', $selected_company);
		$this->db->where('ItemID', $Order_item);
		$this->db->order_by('EffDate', 'DESC');
		return $this->db->get(db_prefix() . 'ratehistory2')->row();
	}
	
	//-----------------------------------------------------
	public function get_account_detailId($accountid){
		$selected_company = $this->session->userdata('root_company');
		
		
		$this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'clients')) . ',' . get_sql_select_client_company().',
		' . db_prefix() . 'contacts.kms,' . db_prefix() . 'contacts.FLNO1,' . db_prefix() . 'contacts.Pan,' . db_prefix() . 'contacts.Aadhaarno,
		' . db_prefix() . 'contacts.istcs,' . db_prefix() . 'contacts.TcsStartDate,' . db_prefix() . 'contacts.phonenumber as altnumber,
		'. db_prefix() . 'contacts.BalancesYN,'. db_prefix() . 'contacts.BalancelYN,'. db_prefix() . 'contacts.pincode as pincodes');
		
		$this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.country', 'left');
		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND '. db_prefix() .'contacts.PlantID = ' . db_prefix() . 'clients.PlantID AND  ' . db_prefix() . 'clients.PlantID = '.$selected_company, 'left');
		
		
		if ($accountid) {
			$this->db->where(db_prefix() . 'clients.AccountID', $accountid);
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$client = $this->db->get(db_prefix() . 'clients')->row();
			
			
			return $client;
		}
		
		
	}
	
	//-----------------------------------------------------
	public function get_account_balance($accountid){
		$selected_company = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() . 'accountbalances.*');
		$this->db->where(db_prefix() . 'accountbalances.AccountID', $accountid);
		$this->db->where(db_prefix() . 'accountbalances.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountbalances.FY', $FY);
		$client_balance = $this->db->get(db_prefix() . 'accountbalances')->row();
		
		return $client_balance;
		
	}
	
	//-----------------------------------------------------
	public function get_vehicle_detail($id){
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->where('PlantID', $selected_company);
		$query = $this->db->get_where('tblvehicle', array('VehicleID' => $id));
		return $result = $query->row();
	}
	
	public function get_order_item($id)
	{
		$selected_company = $this->session->userdata('root_company');
		
		$this->db->where('PlantID', $selected_company);
		$this->db->where('OrderID', $id);
		
		return $this->db->get(db_prefix() . 'history')->result_array();
	}
	public function get_stock_item($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $FY);
		$this->db->where('GodownID',$GodownID);
		$this->db->where('ItemID', $id);
		
		return $this->db->get(db_prefix() . 'stockmaster')->row();
	}
	
	public function get_item_code_list_by_order_ids($order_ids)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('ItemID');
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $fy);
		$this->db->where_in('OrderID', $order_ids);
		
		return $this->db->get(db_prefix() . 'history')->result_array();
	}
	
	public function get_tcsper()
	{
		$c_date = date('Y-m-d');
		$this->db->select('*');
		$this->db->where('EffDate <=',date('Y-m-d'));
		$this->db->from(db_prefix() . 'tcsmaster');
		$this->db->order_by('id',"desc");
		//$this->db->limit(1);
		return $this->db->get()->result_array();
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
	public function get_order_singleitem($orderid,$code)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('OrderQty,CaseQty,eOrderQty');
		$this->db->where('OrderID', $orderid);
		$this->db->where('ItemID', $code);
		$this->db->where('PlantID', $selected_company);
		$this->db->where('FY', $fy);
		return $this->db->get(db_prefix() . 'history')->row();
	}
	public function get_itemcout_all_order($routeid,$code)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->select('SUM(IFNULL('.db_prefix().'history.eOrderQty / '.db_prefix().'history.CaseQty, '.db_prefix().'history.OrderQty / '.db_prefix().'history.CaseQty)) AS OrderQty');
		$this->db->distinct();
		$this->db->join(db_prefix() . 'ordermaster', '' . db_prefix() . 'ordermaster.AccountID = ' . db_prefix() . 'accountroutes.AccountID');
		$this->db->join(db_prefix() . 'history', '' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'ordermaster.OrderID');
		$this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'ordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'accountroutes.RouteID', $routeid);
		$this->db->where(db_prefix() . 'ordermaster.FY', $fy);
		$this->db->where(db_prefix() . 'ordermaster.ChallanID', NULL);
		$this->db->where(db_prefix() . 'ordermaster.OrderStatus', 'O');
		$this->db->where(db_prefix() . 'history.ItemID', $code);
		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
		$item_list = $this->db->get(db_prefix() . 'accountroutes')->row();
		
		return $item_list;
		
	}
	public function check_invoice_generate($id)
	{
		$this->db->where('order_id', $id);
		
		return $this->db->get(db_prefix() . 'invoices')->row();
	}
	
	public function mark_as_cancelled($id)
	{
		$isDraft = $this->is_draft($id);
		
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'invoices', [
		'status' => self::STATUS_CANCELLED,
		'sent'   => 1,
		]);
		
		if ($this->db->affected_rows() > 0) {
			if ($isDraft) {
				$this->change_invoice_number_when_status_draft($id);
			}
			
			$this->log_invoice_activity($id, 'invoice_activity_marked_as_cancelled');
			
			hooks()->do_action('invoice_marked_as_cancelled', $id);
			
			return true;
		}
		
		return false;
	}
	
	public function unmark_as_cancelled($id)
	{
		$this->db->where('id', $id);
		$this->db->update(db_prefix() . 'invoices', [
		'status' => self::STATUS_UNPAID,
		]);
		
		if ($this->db->affected_rows() > 0) {
			$this->log_invoice_activity($id, 'invoice_activity_unmarked_as_cancelled');
			
			return true;
		}
		
		return false;
	}
	
	/**
		* Get this invoice generated recurring invoices
		* @since  Version 1.0.1
		* @param  mixed $id main invoice id
		* @return array
	*/
	public function get_invoice_recurring_invoices($id)
	{
		$this->db->select('id');
		$this->db->where('is_recurring_from', $id);
		$invoices           = $this->db->get(db_prefix() . 'invoices')->result_array();
		$recurring_invoices = [];
		
		foreach ($invoices as $invoice) {
			$recurring_invoices[] = $this->get($invoice['id']);
		}
		
		return $recurring_invoices;
	}
	
	/**
		* Get invoice total from all statuses
		* @since  Version 1.0.2
		* @param  mixed $data $_POST data
		* @return array
	*/
	public function get_invoices_total($data)
	{
		$this->load->model('currencies_model');
		
		if (isset($data['currency'])) {
			$currencyid = $data['currency'];
			} elseif (isset($data['customer_id']) && $data['customer_id'] != '') {
			$currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
			if ($currencyid == 0) {
				$currencyid = $this->currencies_model->get_base_currency()->id;
			}
			} elseif (isset($data['project_id']) && $data['project_id'] != '') {
			$this->load->model('projects_model');
			$currencyid = $this->projects_model->get_currency($data['project_id'])->id;
			} else {
			$currencyid = $this->currencies_model->get_base_currency()->id;
		}
		
		$result            = [];
		$result['due']     = [];
		$result['paid']    = [];
		$result['overdue'] = [];
		
		$has_permission_view                = has_permission('invoices', '', 'view');
		$has_permission_view_own            = has_permission('invoices', '', 'view_own');
		$allow_staff_view_invoices_assigned = get_option('allow_staff_view_invoices_assigned');
		$noPermissionsQuery                 = get_invoices_where_sql_for_staff(get_staff_user_id());
		
		for ($i = 1; $i <= 3; $i++) {
			$select = 'id,total';
			if ($i == 1) {
				$select .= ', (SELECT total - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid = ' . db_prefix() . 'invoices.id) - (SELECT COALESCE(SUM(amount),0) FROM ' . db_prefix() . 'credits WHERE ' . db_prefix() . 'credits.invoice_id=' . db_prefix() . 'invoices.id)) as outstanding';
				} elseif ($i == 2) {
				$select .= ',(SELECT SUM(amount) FROM ' . db_prefix() . 'invoicepaymentrecords WHERE invoiceid=' . db_prefix() . 'invoices.id) as total_paid';
			}
			$this->db->select($select);
			$this->db->from(db_prefix() . 'invoices');
			$this->db->where('currency', $currencyid);
			// Exclude cancelled invoices
			$this->db->where('status !=', self::STATUS_CANCELLED);
			// Exclude draft
			$this->db->where('status !=', self::STATUS_DRAFT);
			
			if (isset($data['project_id']) && $data['project_id'] != '') {
				$this->db->where('project_id', $data['project_id']);
				} elseif (isset($data['customer_id']) && $data['customer_id'] != '') {
				$this->db->where('clientid', $data['customer_id']);
			}
			
			if ($i == 3) {
				$this->db->where('status', self::STATUS_OVERDUE);
				} elseif ($i == 1) {
				$this->db->where('status !=', self::STATUS_PAID);
			}
			
			if (isset($data['years']) && count($data['years']) > 0) {
				$this->db->where_in('YEAR(date)', $data['years']);
				} else {
				$this->db->where('YEAR(date)', date('Y'));
			}
			
			if (!$has_permission_view) {
				$whereUser = $noPermissionsQuery;
				$this->db->where('(' . $whereUser . ')');
			}
			
			$invoices = $this->db->get()->result_array();
			
			foreach ($invoices as $invoice) {
				if ($i == 1) {
					$result['due'][] = $invoice['outstanding'];
					} elseif ($i == 2) {
					$result['paid'][] = $invoice['total_paid'];
					} elseif ($i == 3) {
					$result['overdue'][] = $invoice['total'];
				}
			}
		}
		$currency             = get_currency($currencyid);
		$result['due']        = array_sum($result['due']);
		$result['paid']       = array_sum($result['paid']);
		$result['overdue']    = array_sum($result['overdue']);
		$result['currency']   = $currency;
		$result['currencyid'] = $currencyid;
		
		return $result;
	}
	
	
	public function checkorder($order_ids)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		
		
		$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'ordermaster.OrderID as id, ' . db_prefix() . 'currencies.name as currency_name');
		$this->db->from(db_prefix() . 'ordermaster');
		$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'ordermaster.currency', 'left');
		$this->db->where_in(db_prefix() . 'ordermaster' . '.OrderID', $order_ids);
		$order_details = $this->db->get()->result_array();
		$challan_id = array();
		foreach ($order_details as $key => $value) {
			
			if(is_null($value["ChallanID"])){
				
				}else{
				array_push($challan_id, $value["ChallanID"]);
				
			}
		}
		return $challan_id;
	}
	
	/*
		Insert New Challan to database
	*/
	
	public function AllItemListByOrders($order_ids)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'history.ItemID,tblhistory.CaseQty,tblhistory.AccountID,'.db_prefix() .'items.monitorstock');
		$this->db->from(db_prefix() .'history');
		$this->db->join(db_prefix() . 'items', '' . db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
		$this->db->where_in(db_prefix() .'history.OrderID', $order_ids);
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		$this->db->group_by(db_prefix() .'history.ItemID');
		return $this->db->get()->result_array();
	}
	
	public function AllAccountWiseOrderAmt($order_ids)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'history.SaleRate,tblhistory.CaseQty,tblhistory.AccountID,tblhistory.ItemID,tblhistory.OrderID');
		$this->db->from(db_prefix() .'history');
		$this->db->where_in(db_prefix() .'history.OrderID', $order_ids);
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		return $this->db->get()->result_array();
	}
	
	public function AllItemstockDetails($AllItems)
	{
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'history.ItemID,TType,TType2,SUM(BilledQty) AS BilledQtysum');
		$this->db->from(db_prefix() .'history');
		$this->db->where_in(db_prefix() .'history.ItemID', $AllItems);
		$this->db->where(db_prefix() .'history.PlantID', $selected_company);
		$this->db->where(db_prefix() .'history.FY', $fy);
		$this->db->where(db_prefix() .'history.GodownID', $GodownID);
		$this->db->where(db_prefix() .'history.BillID IS NOT NULL');
		$this->db->group_by(db_prefix() .'history.ItemID,tblhistory.TType,tblhistory.TType2');
		return $this->db->get()->result_array();
	}
	public function AllItemOpnstockDetails($AllItems)
	{
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		$fy = $this->session->userdata('finacial_year');
		$this->db->select('tblstockmaster.OQty,tblstockmaster.ItemID');
		$this->db->from(db_prefix() .'stockmaster');
		$this->db->where_in(db_prefix() .'stockmaster.ItemID', $AllItems);
		$this->db->where(db_prefix() .'stockmaster.PlantID', $selected_company);
		$this->db->where(db_prefix() .'stockmaster.FY', $fy);
		$this->db->where(db_prefix() .'stockmaster.GodownID', $GodownID);
		$this->db->group_by(db_prefix() .'stockmaster.ItemID');
		return $this->db->get()->result_array();
	}
	public function AllAccountBalDetails($AllAccounts)
	{
		$selected_company = $this->session->userdata('root_company');
		
		$fy = $this->session->userdata('finacial_year');
		$this->db->select(db_prefix() .'accountledger.AccountID,tblaccountledger.TType,SUM(tblaccountledger.Amount) AS SumAmount,tblaccountbalances.BAL1,tblclients.MaxCrdAmt');
		$this->db->from(db_prefix() .'accountledger');
		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'accountledger.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'accountledger.PlantID');
		$this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'accountledger.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'accountledger.PlantID AND ' . db_prefix() . 'accountbalances.FY = ' . db_prefix() . 'accountledger.FY');
		$this->db->where_in(db_prefix() .'accountledger.AccountID', $AllAccounts);
		$this->db->where(db_prefix() .'accountledger.PlantID', $selected_company);
		$this->db->where(db_prefix() .'accountledger.FY', $fy);
		$this->db->group_by(db_prefix() .'accountledger.AccountID,tblaccountledger.TType');
		return $this->db->get()->result_array();
	}
	public function UpdateExistingChallan($data)
	{
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		$fy = $this->session->userdata('finacial_year');
		$time = date('H:i:s');
		$date = to_sql_date($data['date'])." ".$time;
		$order_ids = $data["order_id"];
		
		$declineOrder = array();
		$ClientWiseOrdersAmount = array();
		// Check Account Balance limit
		foreach ($order_ids as $orderid1) {
			$order_data1 = $this->GetUnbilledOrderDetails($orderid1);
			
			
			$ClientWiseOrdersAmount[] = [
			'AccountID'=>$order_data1->AccountID,
			'CurrentBal'=>$order_data1->client_actbal,
			'CreditLimit'=>$order_data1->MaxCrdAmt,
			]; 
			
			
		}
		
		// Group by AccountID and sum OrderAmount
		$FinalAccountWiseOrders = [];
		
		foreach ($ClientWiseOrdersAmount as $row) {
			$accountId = $row['AccountID'];
			$CurrentBal =  -1 * floatval($row['CurrentBal']);
			if (!isset($FinalAccountWiseOrders[$accountId])) {
				$FinalAccountWiseOrders[$accountId] = [
				'AccountID' => $row['AccountID'],
				'CurrentBal' =>$CurrentBal,
				'CreditLimit' => $row['CreditLimit'],
				'OrderAbleAmt' => $CurrentBal + $row['CreditLimit'],
				];
			} 
		}
		
		foreach ($order_ids as $orderid1) {
			$order_data1 = $this->GetUnbilledOrderDetails($orderid1);
			$ChkNetOrderAmt = 0;
			foreach ($order_data1->items as $key=>$value) {
				$crate_qty_name = 'crates_'.$orderid1;
				$crateqty = $data[$crate_qty_name];
				$case_qty_name = 'cases_'.$orderid1;
				$caseqty = $data[$case_qty_name];
				$qty_name = 'qty_'.$orderid1.'_'.$value['ItemID'];
				$qty = $data[$qty_name];
				$packQty = $value['CaseQty'];
				$orderQty =  $qty; 
				
				$BasicRate = $value['BasicRate'];
				
				
				$DiscPer = $value['DiscPerc'];
				$OrderAmt = $BasicRate * $orderQty;
				$TotalSaleAmt += $OrderAmt;
				$DiscAmt = $OrderAmt * ($DiscPer/100);
				$TotalDiscAmt += $DiscAmt;
				$TaxableAmt = $OrderAmt - $DiscAmt;
				$CGSTAmt = 0;
				$SGSTAmt = 0;
				$IGSTAmt = 0;
				// echo $orderQty;
				if($value['igst'] == "" || $value['igst'] == NULL || $value['igst'] == "0.00"){
					$CGSTAmt = ($TaxableAmt / 100 ) * $value['cgst'];
					$SGSTAmt = ($TaxableAmt / 100 ) * $value['sgst'];
					$TotalSGSTAmt += $SGSTAmt;
					$ChkNetOrderAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt;
					}else{
					$IGSTAmt = ($TaxableAmt / 100 ) * $value['igst'];
					$ChkNetOrderAmt = $TaxableAmt + $IGSTAmt;
				}
			}
			
			if($order_data1->credit_apply == 'Y'){
				if($FinalAccountWiseOrders[$order_data1->AccountID]['OrderAbleAmt'] >= $ChkNetOrderAmt){
					$FinalAccountWiseOrders[$order_data1->AccountID]['OrderAbleAmt'] = $FinalAccountWiseOrders[$order_data1->AccountID]['OrderAbleAmt'] - $ChkNetOrderAmt;
					}else{
					array_push($declineOrder,$orderid1);
					
					$UpdateChallan = array(
					'credit_exceed'=>'Y',
					); 
					$this->db->where('OrderID', $orderid1);
					$this->db->update(db_prefix() . 'ordermaster', $UpdateChallan);
					
					$this->db->select(db_prefix() . 'staff.*');
					$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);
					$this->db->where(db_prefix() . 'staff.admin', '1');
					$AdminStaff = $this->db->get(db_prefix() . 'staff')->result_array();
					foreach($AdminStaff as $admin){
						
						$Notification_msg = "Credit Limit Reached! Challan can not be generated for order ".$orderid1;
						$notification_data = [
						'description' => $Notification_msg,
						'touserid'    => $admin['staffid'],
						'link'        => 'order/LimitExceededOrders',
						]; 
						$notification_data['additional_data'] = serialize([
						'Order ID. '.$orderid1,
						]);
						
						if (add_notification($notification_data)) {
							pusher_trigger_notification($admin['staffid']);
						}
					}
					
				}
			}
		}
		// print_r($declineOrder);
		// die;
		// die;
		// print_r($order_ids);die;
		// Check order Amount is greter than accountBalance Amount
		/*foreach ($order_ids as $orderid1) {
			$order_data1 = $this->challan_model->getorderdetail_by_orderId($orderid1);
			$get_account_details = $this->challan_model->get_account_detailId($order_data1->AccountID);
			if($get_account_details->bill_till_bal == "Y"){
			$get_account_bal = $this->challan_model->get_account_balance($order_data1->AccountID);
			$sum_bal = $get_account_bal->BAL1 + $get_account_bal->BAL2 + $get_account_bal->BAL3 + $get_account_bal->BAL4 + $get_account_bal->BAL5 + $get_account_bal->BAL6 + $get_account_bal->BAL7 + $get_account_bal->BAL8 + $get_account_bal->BAL9 + $get_account_bal->BAL10 + $get_account_bal->BAL11 + $get_account_bal->BAL12 + $get_account_bal->BAL13;
			if($sum_bal >= 0){
			set_alert('warning', "Challan Amt is greater than Balance Amt... ");
			$redUrl = admin_url('challan/');
			redirect($redUrl);
			}else{
			if(abs($sum_bal) >= $data['txtchalanvalue']){
			$available = true;
			}else{
			set_alert('warning', "Challan Amt is greater than Balance Amt... ");
			$redUrl = admin_url('challan/');
			redirect($redUrl);
			//$available = false;
			}
			}
			}
		}*/
		$cancelOrder = array();
		$ChallanDetail = $this->ChallanDetails($data["number"]);
		$TotalChlAmount = 0;
		$TotalChlCases = 0;
		$TotalChlCrates = 0;
		foreach ($ChallanDetail as $key=>$value) {
			if (in_array($value['OrderID'], $order_ids)){
				$TotalChlAmount += $value['OrderAmt'];
				$TotalChlCases += $value['Cases'];
				$TotalChlCrates += $value['Crates'];
				}else{
				array_push($cancelOrder,$value['OrderID']);
				// Order Master Update
				$this->db->where('OrderID', $value['OrderID']);
				$this->db->update(db_prefix() . 'ordermaster', [
				'OrderAmt' =>0.00,
				'Crates' =>0,
				'Cases' =>0,
				'UserID2' =>$this->session->userdata('username'),
				'Lupdate' =>date('Y-m-d H:i:s'),
				]);
				// History Table Update
				$this->db->where('OrderID', $value['OrderID']);
				$this->db->update(db_prefix() . 'history', [
				'DiscAmt' =>0.00,
				'BilledQty' =>0.00,
				'OrderAmt' =>0.00,
				'cgstamt' =>0.00,
				'sgstamt' =>0.00,
				'igstamt' =>0.00,
				'ChallanAmt' =>0.00,
				'NetOrderAmt' =>0.00,
				'NetChallanAmt' =>0.00,
				'UserID2' =>$this->session->userdata('username'),
				'Lupdate' =>date('Y-m-d H:i:s'),
				]);
				// Update SaleMaster
				$this->db->where('SalesID', $value['SalesID']);
				$this->db->update(db_prefix() . 'salesmaster', [
				'SaleAmt' =>0.00,
				'sgstamt' =>0.00,
				'cgstamt' =>0.00,
				'igstamt' =>0.00,
				'BillAmt' =>0.00,
				'RndAmt' =>0.00,
				'tcsAmt' =>0.00,
				'DiscAmt' =>0.00,
				'UserID2' =>$this->session->userdata('username'),
				'Lupdate' =>date('Y-m-d H:i:s'),
				]);
				// Update Ledger
				$this->db->where('VoucherID', $value['SalesID']);
				$this->db->update(db_prefix() . 'accountledger', [
				'Amount' =>0.00,
				]);
			}
		}
		if($data["number"]){
			$tcs_detail = $this->challan_model->get_tcsper();
			$tcsper = $tcs_detail[0]['tcs'];
			
			if($data["challan_vehicle"]=="TV" || $data["challan_vehicle"]=="SELF"){
				$challan_vehicle = strtoupper($data["vahicle_number"]);
				}else{
				$challan_vehicle = $data["challan_vehicle"];
			}
			
			//die;
			$ChlUpdate = array(
			'ChallanAmt' =>$TotalChlAmount,
			'Crates' =>$TotalChlCrates,
			'Cases' =>$TotalChlCases,
			"VehicleID"=>$challan_vehicle,
			"VehicleType"=>$data["challan_vehicle"],
			"DriverID"=>$data['challan_driver'],
			"remark"=>$data['remark'],
			// "buyer_ord_no"=>$data['buyer_ord_no'],
			'Transdate'=>to_sql_date($data["date"]).' '.date('H:i:s'),
			'UserID2' =>$this->session->userdata('username'),
			'Lupdate' =>date('Y-m-d H:i:s'),
			);
			$this->db->where('ChallanID', $data["number"]);
			if($this->db->update(db_prefix() . 'challanmaster', $ChlUpdate)){
				// Dreiver Free From Challan
				$updateDrv = array(
				'EngageID'=>NULL
				);
				$this->db->where('PlantID', $selected_company);
				$this->db->where('AccountID', $getCHLDetails->DriverID);  
				$this->db->where('SLDTypeID', '3');
				$this->db->update(db_prefix() . 'accountsld', $updateDrv);
				
				// Loader Free From Challan
				if($getCHLDetails->LoaderID !== ''){
					$updateLdr = array(
					'EngageID'=>NULL
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('AccountID', $getCHLDetails->LoaderID);  
					$this->db->where('SLDTypeID', '2');
					$this->db->update(db_prefix() . 'accountsld', $updateLdr);
				}
				// SalesMan Free From Challan
				if($getCHLDetails->SalesmanID !== ''){
					$updateSls = array(
					'EngageID'=>NULL
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('AccountID', $getCHLDetails->SalesmanID);  
					$this->db->where('SLDTypeID', '1');
					$this->db->update(db_prefix() . 'accountsld', $updateSls);
				}
				// Enggaged Driver , loader And SalesMan
				
				if($data['challan_vehicle'] !== "TV" && $data['challan_vehicle'] !== "SELF"){
					
					$updateDrv = array(
					'EngageID'=>$challan_vehicle
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('AccountID', $data['challan_driver']);  
					$this->db->where('SLDTypeID', '3');
					$this->db->update(db_prefix() . 'accountsld', $updateDrv);
				}
				if($data["challan_loader"] !== ''){
					
					$updateLdr = array(
					'EngageID'=>$challan_vehicle
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('AccountID', $data['challan_loader']);  
					$this->db->where('SLDTypeID', '2');
					$this->db->update(db_prefix() . 'accountsld', $updateLdr);
				}
				if($data["challan_sales_man"] !== ''){
					
					$updateSls = array(
					'EngageID'=>$challan_vehicle
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('AccountID', $data['challan_sales_man']);  
					$this->db->where('SLDTypeID', '1');
					$this->db->update(db_prefix() . 'accountsld', $updateSls);
				}
				
				$TotalChlAmount = 0;
				$TotalChlCases = 0;
				$TotalChlCrates = 0;
				// Update Exiting Order Details
				foreach ($order_ids as $orderid) 
				{
					$TotalSaleAmt = 0;
					$TotalDiscAmt = 0;
					$TotalDiscOnSaleAmt = 0;
					$TotalCGSTAmt = 0;
					$TotalSGSTAmt = 0;
					$TotalIGSTAmt = 0;
					$TotalCases = 0;
					$TotalCrates = 0;
					$order_data = $this->GetBilledOrderDetails($orderid);
					$disttype = $order_data->DistributorType;
					$state = $order_data->state;
					$orderdate = date('d/m/Y',strtotime($order_data->Transdate));
					if($order_data){
						$ChallanID = $order_data->ChallanID;
						$SalesID = $order_data->SalesID;
						
						$this->db->where('OrderID', $orderid);  
						$this->db->where('TType', 'O');  
						$this->db->where('TType2', 'Free Distribution'); 
						$this->db->delete(db_prefix() . 'history');
						
						$ExistingItemsIDs = array();
						foreach($order_data->items as $exitemkey => $exitemvalue)
						{
							array_push($ExistingItemsIDs,$exitemvalue['ItemID']);
						}
						$GetMultipleScheme = $this->GetMultipleSchemeData($disttype,$state,$orderdate,$ExistingItemsIDs);
						
						$i = 0;
						foreach ($GetMultipleScheme as $Scheme) {
							${'TotalBilledQty' . $i} = 0; 
							$i++;
						}
						
						$SingleFreeItem = array();
						$SingleDiscItem = array();
						foreach ($order_data->items as $exkey => $exvalue) 
						{
							$Item = $exvalue['ItemID'];
							
							$qty_name = 'qty_'.$orderid.'_'.$exvalue['ItemID'];
							$qty = $data[$qty_name];
							$i = 0;
							$IsApplyMultipleScheme = 0;
							foreach($GetMultipleScheme as $Scheme){
								$SchemeItems = $Scheme["ItemID"];
								$SchemeItemsArr = explode(',',$SchemeItems);
								if(in_array($Item,$SchemeItemsArr)){
									${'TotalBilledQty' . $i} += $qty;
									$IsApplyMultipleScheme++;
									break;
								}
								$i++;
							}
							
							$IsFreeItem = 0;
							if($IsApplyMultipleScheme == 0){
								$ChkSingleScheme = $this->GetSchemeData($disttype,$state,$orderdate,$exvalue['ItemID']);
								if(!empty($ChkSingleScheme)){
									foreach($ChkSingleScheme as $each){
										if($qty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
											$Disc_pkt = floor($qty / $each['SlabQty']) * $each['Disc_pkt'];
											$SingleFreeItem[$exvalue["ItemID"]] = $Disc_pkt;
											$IsFreeItem++;
											break;
										}
									}
								}
							}
							if($IsApplyMultipleScheme == 0 && $IsFreeItem == 0){
								$getDiscscheme = $this->GetDiscSchemeData($disttype,$state,$orderdate,$exvalue['ItemID']);
								if(!empty($getDiscscheme)){
									foreach($getDiscscheme as $each){
										if($qty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
											$SingleDiscItem[$exvalue["ItemID"]] = $each["DiscPerc"];
											break;
										}
									}
								}
							}
							
							
						}
						
						$i = 0;
						foreach($GetMultipleScheme as $Scheme){
							$GetMultipleScheme[$i]["TotalBilledQty"] = ${'TotalBilledQty' . $i};
							$i++;
						}
						// echo "<pre>";print_r($GetMultipleScheme);die;
						foreach ($order_data->items as $key=>$value) 
						{
							$crate_qty_name = 'crates_'.$orderid;
							$crateqty = $data[$crate_qty_name];
							$case_qty_name = 'cases_'.$orderid;
							$caseqty = $data[$case_qty_name];
							$qty_name = 'qty_'.$orderid.'_'.$value['ItemID'];
							$qty = $data[$qty_name];
							$packQty = $value['CaseQty'];
							$orderQty =  $qty; // $packQty * Add If Needed
							if($value["SuppliedIn"] == "CR"){
								// $TotalCrates += ($orderQty/$packQty);
								$TotalCrates = $crateqty;
								}else{
								// $TotalCases += ($orderQty/$packQty);
								$TotalCases = $caseqty;
							}
							$BasicRate = $value['BasicRate'];
							// $getDiscscheme = $this->GetDiscSchemeData($disttype,$state,$orderdate,$value['ItemID']);
							// if(!empty($getDiscscheme)){
							// foreach($getDiscscheme as $each){
							// if($orderQty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
							// $value['DiscPerc'] = $each['DiscPerc'];
							// }
							// }
							// }
							
							if (array_key_exists($value['ItemID'], $SingleDiscItem)) {
								$value['DiscPerc'] = $SingleDiscItem[$value['ItemID']];
							}
							
							$DiscPer = $value['DiscPerc'];
							$OrderAmt = $BasicRate * $orderQty;
							$TotalSaleAmt += $OrderAmt;
							$DiscAmt = $OrderAmt * ($DiscPer/100);
							$TotalDiscAmt += $DiscAmt;
							$TaxableAmt = $OrderAmt - $DiscAmt;
							$CGSTAmt = 0;
							$SGSTAmt = 0;
							$IGSTAmt = 0;
							if($value['igst'] == "" || $value['igst'] == NULL || $value['igst'] == "0.00"){
								$CGSTAmt = ($TaxableAmt / 100 ) * $value['cgst'];
								$TotalCGSTAmt += $CGSTAmt;
								$SGSTAmt = ($TaxableAmt / 100 ) * $value['sgst'];
								$TotalSGSTAmt += $SGSTAmt;
								$NetOrderAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt;
								}else{
								$IGSTAmt = ($TaxableAmt / 100 ) * $value['igst'];
								$TotalIGSTAmt += $IGSTAmt;
								$NetOrderAmt = $TaxableAmt + $IGSTAmt;
							}
							$update_array = array(
							'eOrderQty'=>$orderQty,
							'BilledQty' => $orderQty,
							'DiscPerc' => $DiscPer,
							'DiscAmt' => $DiscAmt,
							'OrderAmt'=>$OrderAmt,
							'ChallanAmt' => $OrderAmt,
							'cgstamt'=>$CGSTAmt,
							'sgstamt'=>$SGSTAmt,
							'igstamt'=>$IGSTAmt,
							'NetOrderAmt'=>$NetOrderAmt,
							'NetChallanAmt' => $NetOrderAmt,
							'UserID2'=>$this->session->userdata('username'),
							'Lupdate'=>date('Y-m-d H:i:s'),
							);
							$this->db->where('PlantID', $selected_company);
							$this->db->where('FY', $fy);  
							$this->db->where('OrderID', $orderid);
							$this->db->where('ItemID', $value['ItemID']);
							$this->db->where('TType', 'O');
							$this->db->where('TType2', 'Order');
							$this->db->update(db_prefix() . 'history', $update_array);
							
						}
						$Ordinalno = 1;
						foreach($GetMultipleScheme as $each){
							if($each["TotalBilledQty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
								$Disc_pkt = floor($each["TotalBilledQty"] / $each['SlabQty']) * $each['Disc_pkt'];
								// echo"ok";die;
								$new_item_data = array(
								"PlantID" =>$selected_company,
								"FY" =>$this->session->userdata('finacial_year'),
								"OrderID" =>$orderid,
								'TransID'   => $SalesID,
								'BillID' => $ChallanID,
								"TType" =>"O",
								"TType2" =>'Free Distribution',
								"AccountID" =>$order_data->AccountID,
								"ItemID" =>$each['FreeItemID'],
								"BasicRate" =>0,
								"SuppliedIn" =>'',
								"OrderQty" => $Disc_pkt,
								"eOrderQty" => $Disc_pkt,
								"BilledQty" => $Disc_pkt,
								"SaleRate" =>0,
								"DiscPerc" =>100,
								"DiscAmt" =>0,
								"cgst" =>0,
								"cgstamt" =>0,
								"sgst" =>0,
								"sgstamt" =>0,
								"igst" =>0,
								"igstamt" =>0,
								"CaseQty" =>1,
								"OrderAmt" =>0,
								"NetOrderAmt" =>0,
								"Ordinalno" =>$Ordinalno,
								"UserID" =>$this->session->userdata('username'),
								"TransDate2"=>$date,
								"TransDate" =>to_sql_date($orderdate)." ".date("H:i:s"),
								);
								$this->db->insert(db_prefix() . 'history', $new_item_data);
								// echo $this->db->last_query();die;
								$Ordinalno++;
							}
						}
						
						foreach($SingleFreeItem as $ItemID => $FreeQty){
							$new_item_data = array(
							"PlantID" =>$selected_company,
							"FY" =>$this->session->userdata('finacial_year'),
							"OrderID" =>$orderid,
							'TransID'   => $SalesID,
							'BillID' => $ChallanID,
							"TType" =>"O",
							"TType2" =>'Free Distribution',
							"AccountID" =>$order_data->AccountID,
							"ItemID" =>$each['FreeItemID'],
							"BasicRate" =>0,
							"SuppliedIn" =>'',
							"OrderQty" => $Disc_pkt,
							"eOrderQty" => $Disc_pkt,
							"BilledQty" => $Disc_pkt,
							"SaleRate" =>0,
							"DiscPerc" =>100,
							"DiscAmt" =>0,
							"cgst" =>0,
							"cgstamt" =>0,
							"sgst" =>0,
							"sgstamt" =>0,
							"igst" =>0,
							"igstamt" =>0,
							"CaseQty" =>1,
							"OrderAmt" =>0,
							"NetOrderAmt" =>0,
							"UserID" =>$this->session->userdata('username'),
							"Ordinalno"=>$Ordinalno,
							"TransDate2"=>$date,
							"TransDate" =>to_sql_date($orderdate)." ".date("H:i:s"),
							);
							$this->db->insert(db_prefix() . 'history', $new_item_data);
							$Ordinalno++;
						}
						
						$TotalNetGSTAmt = ($TotalSGSTAmt + $TotalCGSTAmt + $TotalIGSTAmt);
						$TotalNetOrderAmt = $TotalSaleAmt + $TotalNetGSTAmt - $TotalDiscAmt - $TotalDiscOnSaleAmt;
						if($order_data->istcs=="1"){
							$p = $tcsper/100;
							$tcsable_amt = round($TotalNetOrderAmt);
							$Y = $p * $tcsable_amt;
							}else {
							$tcsper = 0.00;
							$Y = 0.00;
						}
						$TotalNetOrderAmt = $TotalNetOrderAmt + $Y;
						$RndAmt = round($TotalNetOrderAmt);
						$roundup2 = $TotalNetOrderAmt - $RndAmt;
						$round_variation = $roundup2;
						
						$DeliverySequence = 'Sequence_'.$orderid;
						$DeliveryPoint = $data[$DeliverySequence];
						// Update Sale Master
						$UpdateSale = array(
						"DeliveryPoint"=>$DeliveryPoint,
						"tcsAmt"=>$Y,
						"SaleAmt"=>($TotalSaleAmt-$TotalDiscAmt),
						"DiscAmt"=>$TotalDiscAmt,
						"DiscOnSaleAmt"=>$TotalDiscOnSaleAmt,
						"sgstamt"=>$TotalSGSTAmt,
						"cgstamt"=>$TotalCGSTAmt,
						"igstamt"=>$TotalIGSTAmt,
						"BillAmt"=>$TotalNetOrderAmt,
						"RndAmt"=>round($TotalNetOrderAmt),
						);
						$this->db->where('PlantID', $selected_company);
						$this->db->where('FY', $fy);  
						$this->db->where('SalesID', $SalesID);
						$this->db->update(db_prefix() . 'salesmaster', $UpdateSale);
						
						// Update Order Master
						$UpdateOrder = array(
						'OrderAmt'=>$TotalNetOrderAmt,
						"DiscOnSaleAmt"=>$TotalDiscOnSaleAmt,
						"DiscAmt"=>$TotalDiscAmt,
						'tcsAmt'=>$Y,
						'Crates'=>ceil($TotalCrates),
						'Cases'=>ceil($TotalCases),
						'total_tax'=>$TotalNetGSTAmt,
						'UserID2'=>$this->session->userdata('username'),
						'Lupdate'=>date('Y-m-d H:i:s'),
						);
						$this->db->where('PlantID', $selected_company);
						$this->db->where('FY', $fy);  
						$this->db->where('OrderID', $orderid);
						$this->db->update(db_prefix() . 'ordermaster', $UpdateOrder);
						
						// Update Challan Master table
						
						$TotalChlAmount += $TotalNetOrderAmt;
						$TotalChlCases += ceil($TotalCases);
						$TotalChlCrates += ceil($TotalCrates);
						$UpdateChallan = array(
						'ChallanAmt'=>$TotalChlAmount,
						'Crates'=>$TotalChlCrates,
						'Cases'=>$TotalChlCases,
						);
						$this->db->where('PlantID', $selected_company);
						$this->db->where('FY', $fy);  
						$this->db->where('ChallanID', $ChallanID);
						$this->db->update(db_prefix() . 'challanmaster', $UpdateChallan);
						
						
						$Reconsile_Arr =array(
						"Amount"=>round($TotalNetOrderAmt),
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->where('TransID', $SalesID);
						$this->db->update(db_prefix() . 'ReconsileMaster', $Reconsile_Arr);
						
						// Delete Create Ledger
						$this->db->where('VoucherID', $SalesID);  
						$this->db->where('ChallanID', $ChallanID);
						$this->db->delete(db_prefix() . 'accountcrates');
						// Add Create Ledger
						if($TotalCrates >0){
							// create ledger 
							$narration_create = "Against SalesID ".$SalesID."/ ChallanID ".$ChallanID;
							$create_ledgerdata = array(
							"PlantID"=>$selected_company,
							"VoucherID"=>$SalesID,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"ChallanID"=>$ChallanID,
							"AccountID"=>$order_data->AccountID2,
							"TType"=>"D",
							"Qty"=>$TotalCrates,
							"PassedFrom"=>"CHALLAN",
							"Narration"=>$narration_create,
							"OrdinalNo"=>1,
							"UserID"=>$this->session->userdata('username'),
							"FY"=>$fy
							);
							$this->db->insert(db_prefix() . 'accountcrates', $create_ledgerdata);
						}
						
						$narration = "By SalesID ".$SalesID."/".$ChallanID; 
						$narration_tcs = "TCS@0.1000% on SalesID ".$SalesID."/".$ChallanID;
						$Ord = 1;
						// Delete All Ledger
						$this->db->where('VoucherID', $SalesID);  
						$this->db->delete(db_prefix() . 'accountledger');
						
						// SALE Ledger
						$ledgerdata_credit=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$SalesID,
						"AccountID"=>"SALE",
						"EffectOn" => $order_data->AccountID,
						"TType"=>"C",
						"Amount"=>$TotalSaleAmt,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);
						$Ord++;
						if($TotalIGSTAmt >0){
							$acct_name3 = "IGST";
							$ledgerdata_credit_igst=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$SalesID,
							"AccountID"=>$acct_name3,
							"EffectOn" => $order_data->AccountID,
							"TType"=>"C",
							"Amount"=>$TotalIGSTAmt,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);
							$Ord++;
							}else{
							$acct_name1 = "SGST";
							$acct_name2 = "CGST";
							$ledgerdata_credit_sgst=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$SalesID,
							"AccountID"=>$acct_name1,
							"EffectOn" => $order_data->AccountID,
							"TType"=>"C",
							"Amount"=>$TotalSGSTAmt,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);
							$Ord++;
							$ledgerdata_credit_cgst=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$SalesID,
							"AccountID"=>$acct_name2,
							"EffectOn" => $order_data->AccountID,
							"TType"=>"C",
							"Amount"=>$TotalCGSTAmt,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);
							$Ord++;
						}
						
						// Party Account Ledger
						$ledgerdata_debit=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$SalesID,
						"AccountID"=>$order_data->AccountID,
						"EffectOn" => "SALE",
						"TType"=>"D",
						"Amount"=>$RndAmt,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
						$Ord++;
						// Discount Account Ledger
						if($TotalDiscAmt >0){
							$ledgerdata_debit=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$SalesID,
							"AccountID"=>"DISC",
							"EffectOn" => $order_data->AccountID,
							"TType"=>"D",
							"Amount"=>$TotalDiscAmt,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
							$Ord++;
						}
						if($TotalDiscOnSaleAmt>0){
							$ledgerdata_debit=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$SalesID,
							"AccountID"=>"DISC",
							"EffectOn" => $order_data->AccountID,
							"TType"=>"D",
							"Amount"=>$TotalDiscOnSaleAmt,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
							$Ord++;
						}
						
						
						if($Y > 0){
							$ledgerdata_tcs=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$SalesID,
							"AccountID"=>"TCS",
							"EffectOn" => $order_data->AccountID,
							"TType"=>"C",
							"Amount"=>$Y,
							"Narration"=>$narration_tcs,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username'),
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
							$Ord++;
							$ledgerdata_tcs=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$SalesID,
							"AccountID"=>$order_data->AccountID,
							"EffectOn" => 'TCS',
							"TType"=>"D",
							"Amount"=>$Y,
							"Narration"=>$narration_tcs,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username'),
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
							$Ord++;
						}
						
						if($round_variation >=0){
							$rTType = "D";
							$round_variation_new = abs($round_variation);
							}else{
							$rTType = "C";
							$round_variation_new = abs($round_variation);
						}
						$ledgerdata_roundoff =array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$SalesID,
						"AccountID"=>"ROUNDOFF",
						"EffectOn" => $order_data->AccountID,
						"TType"=>$rTType,
						"Amount"=>$round_variation_new,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);
					}
				}// End Exiting order update data
				
				
				// Add Order details
				foreach ($order_ids as $orderid) 
				{
					
					if(in_array($orderid,$declineOrder)){
						continue;
					}
					$order_data = $this->GetUnbilledOrderDetails($orderid);
					$disttype = $order_data->DistributorType;
					$state = $order_data->state;
					$orderdate = date('d/m/Y',strtotime($order_data->Transdate));
					if($order_data){
						$ChallanID = $data["number"];
						$item_count = count($order_data->items);
						if($order_data->ActSalestype =="Sales"){
							if($order_data->OrderType=="TaxItems"){
								$bt = "T";
								if($selected_company == 1){
									$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
									}elseif($selected_company == 2){
									$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
									}elseif($selected_company == 3){
									$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
								}
								$full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
								$saleid = "TAX".$full_tax_number;
								}else {
								$bt = "B";
								if($selected_company == 1){
									$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
									}elseif($selected_company == 2){
									$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
									}elseif($selected_company == 3){
									$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
								}
								$full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
								$saleid = "BOS".$full_nontax_number;
							}
							}elseif($order_data->ActSalestype =="StockTransfer"){
							$bt = "M";
							if($selected_company == 1){
								$new_trnNumber = get_option('next_trn_number_for_cspl');
								}elseif($selected_company == 2){
								$new_trnNumber = get_option('next_trn_number_for_cff');
								}elseif($selected_company == 3){
								$new_trnNumber = get_option('next_trn_number_for_cbu');
							}
							$full_trnnumber = str_pad($new_trnNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
							$saleid = "TRN".$full_trnnumber;
							}elseif($order_data->ActSalestype =="CNF"){
							$bt = "C";
							if($selected_company == 1){
								$new_cnfNumber = get_option('next_cnf_number_for_cspl');
								}elseif($selected_company == 2){
								$new_cnfNumber = get_option('next_cnf_number_for_cff');
								}elseif($selected_company == 3){
								$new_cnfNumber = get_option('next_cnf_number_for_cbu');
							}
							$full_cnfnumber = str_pad($new_cnfNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
							$saleid = "CNF".$full_cnfnumber;
							}else{
							if($order_data->OrderType=="TaxItems"){
								$bt = "T";
								if($selected_company == 1){
									$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
									}elseif($selected_company == 2){
									$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
									}elseif($selected_company == 3){
									$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
								}
								$full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
								$saleid = "TAX".$full_tax_number;
								}else {
								$bt = "B";
								if($selected_company == 1){
									$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
									}elseif($selected_company == 2){
									$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
									}elseif($selected_company == 3){
									$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
								}
								$full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
								$saleid = "BOS".$full_nontax_number;
							}
						}
						
						$this->db->where('OrderID', $orderid);  
						$this->db->where('TType', 'O');  
						$this->db->where('TType2', 'Free Distribution'); 
						$this->db->delete(db_prefix() . 'history');
						
						$salesdata_new = array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"BT"=>$bt,
						"SalesID"=>$saleid,
						"Transdate"=>to_sql_date($data["date"]).' '.date('H:i:s'),
						"OrderID"=>$orderid,
						"ChallanID"=>$ChallanID,
						"AccountID"=>$order_data->AccountID,
						"AccountID2"=>$order_data->AccountID2,
						"cd_applicable"=>$order_data->cd_applicable,
						"ItCount"=>$item_count,
						"cnfid"=>1,
						"tcs"=>$tcsper,
						"tcsAmt"=>0,
						"PayType"=>"C",
						"SaleAmt"=>0,
						"DiscAmt"=>0,
						"sgstamt"=>0,
						"cgstamt"=>0,
						"igstamt"=>0,
						"BillAmt"=>0,
						"RndAmt"=>0,
						"UserID"=>$this->session->userdata('username'),
						);
						if($order_data->GSTNO !== NULL && $order_data->GSTNO !== ''){
							$salesdata_new['gstno'] = $order_data->GSTNO;
						}
						
						if($this->db->insert(db_prefix() . 'salesmaster', $salesdata_new)){
							if($order_data->ActSalestype =="Sales"){
								if($bt =="T"){
									$this->increment_next_tax_transaction_number();
									}else{
									$this->increment_next_nontax_transaction_number();
								}
								}elseif($order_data->ActSalestype =="StockTransfer"){
								$this->increment_trn_transaction_number();
								}elseif($order_data->ActSalestype =="CNF"){
								$this->increment_cnf_transaction_number();
								}else{
								if($bt =="T"){
									$this->increment_next_tax_transaction_number();
									}else{
									$this->increment_next_nontax_transaction_number();
								}
							}
							
							$ExistingItemsIDs = array();
							foreach($order_data->items as $exitemkey => $exitemvalue)
							{
								array_push($ExistingItemsIDs,$exitemvalue['ItemID']);
							}
							$GetMultipleScheme = $this->GetMultipleSchemeData($disttype,$state,$orderdate,$ExistingItemsIDs);
							
							$i = 0;
							foreach ($GetMultipleScheme as $Scheme) {
								${'TotalBilledQty' . $i} = 0; 
								$i++;
							}
							
							$SingleFreeItem = array();
							$SingleDiscItem = array();
							foreach ($order_data->items as $exkey => $exvalue) 
							{
								$Item = $exvalue['ItemID'];
								
								$qty_name = 'qty_'.$orderid.'_'.$exvalue['ItemID'];
								$qty = $data[$qty_name];
								$i = 0;
								$IsApplyMultipleScheme = 0;
								foreach($GetMultipleScheme as $Scheme){
									$SchemeItems = $Scheme["ItemID"];
									$SchemeItemsArr = explode(',',$SchemeItems);
									if(in_array($Item,$SchemeItemsArr)){
										${'TotalBilledQty' . $i} += $qty;
										$IsApplyMultipleScheme++;
										break;
									}
									$i++;
								}
								
								$IsFreeItem = 0;
								if($IsApplyMultipleScheme == 0){
									$ChkSingleScheme = $this->GetSchemeData($disttype,$state,$orderdate,$exvalue['ItemID']);
									if(!empty($ChkSingleScheme)){
										foreach($ChkSingleScheme as $each){
											if($qty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
												$Disc_pkt = floor($qty / $each['SlabQty']) * $each['Disc_pkt'];
												$SingleFreeItem[$exvalue["ItemID"]] = $Disc_pkt;
												$IsFreeItem++;
												break;
											}
										}
									}
								}
								if($IsApplyMultipleScheme == 0 && $IsFreeItem == 0){
									$getDiscscheme = $this->GetDiscSchemeData($disttype,$state,$orderdate,$exvalue['ItemID']);
									if(!empty($getDiscscheme)){
										foreach($getDiscscheme as $each){
											if($qty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
												$SingleDiscItem[$exvalue["ItemID"]] = $each["DiscPerc"];
												break;
											}
										}
									}
								}
								
								
							}
							
							$i = 0;
							foreach($GetMultipleScheme as $Scheme){
								$GetMultipleScheme[$i]["TotalBilledQty"] = ${'TotalBilledQty' . $i};
								$i++;
							}
							
							
							
							$TotalSaleAmt = 0;
							$TotalDiscAmt = 0;
							$TotalDiscOnSaleAmt = 0;
							$TotalCGSTAmt = 0;
							$TotalSGSTAmt = 0;
							$TotalIGSTAmt = 0;
							$TotalCases = 0;
							$TotalCrates = 0;
							
							foreach ($order_data->items as $key=>$value) 
							{
								$crate_qty_name = 'crates_'.$orderid;
								$crateqty = $data[$crate_qty_name];
								$case_qty_name = 'cases_'.$orderid;
								$caseqty = $data[$case_qty_name];
								$qty_name = 'qty_'.$orderid.'_'.$value['ItemID'];
								$qty = $data[$qty_name];
								$packQty = $value['CaseQty'];
								$orderQty =  $qty; // $packQty * Add If Needed
								if($value["SuppliedIn"] == "CR"){
									// $TotalCrates += ($orderQty/$packQty);
									$TotalCrates += $crateqty;
									}else{
									// $TotalCases += ($orderQty/$packQty);
									$TotalCases += $caseqty;
								}
								$BasicRate = $value['BasicRate'];
								// $getDiscscheme = $this->GetDiscSchemeData($disttype,$state,$orderdate,$value['ItemID']);
								// if(!empty($getDiscscheme)){
								// foreach($getDiscscheme as $each){
								// if($orderQty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){ 
								// $value['DiscPerc'] = $each['DiscPerc'];
								// }
								// }
								// }
								
								if (array_key_exists($value['ItemID'], $SingleDiscItem)) {
									$value['DiscPerc'] = $SingleDiscItem[$value['ItemID']];
								}
								
								$DiscPer = $value['DiscPerc'];
								$OrderAmt = $BasicRate * $orderQty;
								$TotalSaleAmt += $OrderAmt;
								$DiscAmt = $OrderAmt * ($DiscPer/100);
								$TotalDiscAmt += $DiscAmt;
								$TaxableAmt = $OrderAmt - $DiscAmt;
								$CGSTAmt = 0;
								$SGSTAmt = 0;
								$IGSTAmt = 0;
								if($value['igst'] == "" || $value['igst'] == NULL || $value['igst'] == "0.00"){
									$CGSTAmt = ($TaxableAmt / 100 ) * $value['cgst'];
									$TotalCGSTAmt += $CGSTAmt;
									$SGSTAmt = ($TaxableAmt / 100 ) * $value['sgst'];
									$TotalCGSTAmt += $SGSTAmt;
									$NetOrderAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt;
									}else{
									$IGSTAmt = ($TaxableAmt / 100 ) * $value['igst'];
									$TotalIGSTAmt += $IGSTAmt;
									$NetOrderAmt = $TaxableAmt + $IGSTAmt;
								}
								//echo $data[$qty];
								$update_array = array(
								'Lupdate' =>date('Y-m-d H:i:s'),
								'eOrderQty'=>$orderQty,
								'BilledQty' => $orderQty,
								'DiscPerc' => $DiscPer,
								'DiscAmt' => $DiscAmt,
								'TransID'   => $saleid,
								'BillID' => $ChallanID,
								'OrderAmt'=>$OrderAmt,
								'ChallanAmt' => $OrderAmt,
								'cgstamt'=>$CGSTAmt,
								'sgstamt'=>$SGSTAmt,
								'igstamt'=>$IGSTAmt,
								'NetOrderAmt'=>$NetOrderAmt,
								'NetChallanAmt' => $NetOrderAmt,
								'GodownID'=>$GodownID,
								);
								$this->db->where('PlantID', $selected_company);
								$this->db->where('FY', $fy);  
								$this->db->where('OrderID', $orderid);
								$this->db->where('ItemID', $value['ItemID']);
								$this->db->where('TType', 'O');
								$this->db->where('TType2', 'Order');
								$this->db->update(db_prefix() . 'history', $update_array);
								
							}
							
							foreach($GetMultipleScheme as $each){
								if($each["TotalBilledQty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
									$Disc_pkt = floor($each["TotalBilledQty"] / $each['SlabQty']) * $each['Disc_pkt'];
									
									$new_item_data = array(
									"PlantID" =>$selected_company,
									"FY" =>$this->session->userdata('finacial_year'),
									"OrderID" =>$orderid,
									'TransID'   => $saleid,
									'BillID' => $ChallanID,
									"TType" =>"O",
									"TType2" =>'Free Distribution',
									"AccountID" =>$order_data->AccountID,
									"ItemID" =>$each['FreeItemID'],
									"BasicRate" =>0,
									"SuppliedIn" =>'',
									"OrderQty" => $Disc_pkt,
									"eOrderQty" => $Disc_pkt,
									"BilledQty" => $Disc_pkt,
									"SaleRate" =>0,
									"DiscPerc" =>100,
									"DiscAmt" =>0,
									"cgst" =>0,
									"cgstamt" =>0,
									"sgst" =>0,
									"sgstamt" =>0,
									"igst" =>0,
									"igstamt" =>0,
									"CaseQty" =>1,
									"OrderAmt" =>0,
									"NetOrderAmt" =>0,
									"UserID" =>$this->session->userdata('username'),
									"TransDate2"=>$date,
									"TransDate" =>to_sql_date($orderdate)." ".date("H:i:s"),
									);
									$this->db->insert(db_prefix() . 'history', $new_item_data);
								}
							}
							
							foreach($SingleFreeItem as $ItemID => $FreeQty){
								$new_item_data = array(
								"PlantID" =>$selected_company,
								"FY" =>$this->session->userdata('finacial_year'),
								"OrderID" =>$orderid,
								'TransID'   => $saleid,
								'BillID' => $ChallanID,
								"TType" =>"O",
								"TType2" =>'Free Distribution',
								"AccountID" =>$order_data->AccountID,
								"ItemID" =>$each['FreeItemID'],
								"BasicRate" =>0,
								"SuppliedIn" =>'',
								"OrderQty" => $Disc_pkt,
								"eOrderQty" => $Disc_pkt,
								"BilledQty" => $Disc_pkt,
								"SaleRate" =>0,
								"DiscPerc" =>100,
								"DiscAmt" =>0,
								"cgst" =>0,
								"cgstamt" =>0,
								"sgst" =>0,
								"sgstamt" =>0,
								"igst" =>0,
								"igstamt" =>0,
								"CaseQty" =>1,
								"OrderAmt" =>0,
								"NetOrderAmt" =>0,
								"UserID" =>$this->session->userdata('username'),
								"TransDate2"=>$date,
								"TransDate" =>to_sql_date($orderdate)." ".date("H:i:s"),
								);
								$this->db->insert(db_prefix() . 'history', $new_item_data);
							}
							
							$TotalNetGSTAmt = ($TotalSGSTAmt + $TotalCGSTAmt + $TotalIGSTAmt);
							$TotalNetOrderAmt = $TotalSaleAmt + $TotalNetGSTAmt - $TotalDiscAmt - $TotalDiscOnSaleAmt;
							if($order_data->istcs=="1"){
								$p = $tcsper/100;
								$tcsable_amt = round($TotalNetOrderAmt);
								$Y = $p * $tcsable_amt;
								}else {
								$tcsper = 0.00;
								$Y = 0.00;
							}
							$TotalNetOrderAmt = $TotalNetOrderAmt + $Y;
							$RndAmt = round($TotalNetOrderAmt);
							$roundup2 = $TotalNetOrderAmt - $RndAmt;
							$round_variation = $roundup2;
							
							// Update Sale Master
							$UpdateSale = array(
							"tcsAmt"=>$Y,
							"SaleAmt"=>($TotalSaleAmt-$TotalDiscAmt),
							"DiscOnSaleAmt"=>$TotalDiscOnSaleAmt,
							"DiscAmt"=>$TotalDiscAmt,
							"sgstamt"=>$TotalSGSTAmt,
							"cgstamt"=>$TotalCGSTAmt,
							"igstamt"=>$TotalIGSTAmt,
							"BillAmt"=>$TotalNetOrderAmt,
							"RndAmt"=>round($TotalNetOrderAmt),
							);
							$this->db->where('PlantID', $selected_company);
							$this->db->where('FY', $fy);  
							$this->db->where('OrderID', $orderid);
							$this->db->update(db_prefix() . 'salesmaster', $UpdateSale);
							
							// Update Order Master
							$UpdateOrder = array(
							'ChallanID'=>$ChallanID,
							'SalesID'=>$saleid,
							'DiscAmt'=>$TotalDiscAmt,
							'DiscOnSaleAmt'=>$TotalDiscOnSaleAmt,
							'OrderAmt'=>$TotalNetOrderAmt,
							'tcsAmt'=>$Y,
							'Crates'=>ceil($TotalCrates),
							'Cases'=>ceil($TotalCases),
							'total_tax'=>$TotalNetGSTAmt,
							'UserID2'=>$this->session->userdata('username'),
							'Lupdate'=>date('Y-m-d H:i:s'),
							);
							$this->db->where('PlantID', $selected_company);
							$this->db->where('FY', $fy);  
							$this->db->where('OrderID', $orderid);
							$this->db->update(db_prefix() . 'ordermaster', $UpdateOrder);
							
							// Update Challan Master table
							
							$TotalChlAmount += $TotalNetOrderAmt;
							$TotalChlCases += ceil($TotalCases);
							$TotalChlCrates += ceil($TotalCrates);
							$UpdateChallan = array(
							'ChallanAmt'=>$TotalChlAmount,
							'Crates'=>$TotalChlCrates,
							'Cases'=>$TotalChlCases,
							);
							$this->db->where('PlantID', $selected_company);
							$this->db->where('FY', $fy);  
							$this->db->where('ChallanID', $ChallanID);
							$this->db->update(db_prefix() . 'challanmaster', $UpdateChallan);
							
							$Reconsile_Arr =array(
							"TransDate"=>$date,
							"AccountID"=>$order_data->AccountID,
							"TransID"=>$saleid,
							"Amount"=>round($TotalNetOrderAmt),
							"TType"=>"DR",
							"PassedFrom"=>"SALE",
							"Status"=>"N",
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'ReconsileMaster', $Reconsile_Arr);
							
							// Add Create Ledger
							if($TotalCrates >0){
								// create ledger 
								$narration_create = "Against SalesID ".$saleid."/ ChallanID ".$ChallanID;
								$create_ledgerdata = array(
								"PlantID"=>$selected_company,
								"VoucherID"=>$saleid,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"ChallanID"=>$ChallanID,
								"AccountID"=>$order_data->AccountID,
								"TType"=>"D",
								"Qty"=>$TotalCrates,
								"PassedFrom"=>"CHALLAN",
								"Narration"=>$narration_create,
								"OrdinalNo"=>1,
								"UserID"=>$this->session->userdata('username'),
								"FY"=>$fy
								);
								$this->db->insert(db_prefix() . 'accountcrates', $create_ledgerdata);
							}
							
							// Add Ledger Entry
							$narration = "By SalesID ".$saleid."/".$ChallanID; 
							$narration_tcs = "TCS@0.1000% on SalesID ".$saleid."/".$ChallanID;
							$Ord = 1;
							// SALE Ledger
							$ledgerdata_credit=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$saleid,
							"AccountID"=>"SALE",
							"EffectOn" => $order_data->AccountID,
							"TType"=>"C",
							"Amount"=>$TotalSaleAmt,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);
							$Ord++;
							if($TotalIGSTAmt >0){
								$acct_name3 = "IGST";
								$ledgerdata_credit_igst=array(
								"PlantID"=>$selected_company,
								"FY"=>$fy,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"VoucherID"=>$saleid,
								"AccountID"=>$acct_name3,
								"EffectOn" => $order_data->AccountID,
								"TType"=>"C",
								"Amount"=>$TotalIGSTAmt,
								"Narration"=>$narration,
								"PassedFrom"=>"SALE",
								"OrdinalNo"=>$Ord,
								"UserID"=>$this->session->userdata('username')
								);
								$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);
								$Ord++;
								}else{
								$acct_name1 = "SGST";
								$acct_name2 = "CGST";
								$ledgerdata_credit_sgst=array(
								"PlantID"=>$selected_company,
								"FY"=>$fy,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"VoucherID"=>$saleid,
								"AccountID"=>$acct_name1,
								"EffectOn" => $order_data->AccountID,
								"TType"=>"C",
								"Amount"=>$TotalSGSTAmt,
								"Narration"=>$narration,
								"PassedFrom"=>"SALE",
								"OrdinalNo"=>$Ord,
								"UserID"=>$this->session->userdata('username')
								);
								$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);
								$Ord++;
								$ledgerdata_credit_cgst=array(
								"PlantID"=>$selected_company,
								"FY"=>$fy,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"VoucherID"=>$saleid,
								"AccountID"=>$acct_name2,
								"EffectOn" => $order_data->AccountID,
								"TType"=>"C",
								"Amount"=>$TotalCGSTAmt,
								"Narration"=>$narration,
								"PassedFrom"=>"SALE",
								"OrdinalNo"=>$Ord,
								"UserID"=>$this->session->userdata('username')
								);
								$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);
								$Ord++;
							}
							// Party Account Ledger
							$ledgerdata_debit=array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$saleid,
							"AccountID"=>$order_data->AccountID,
							"EffectOn" => "SALE",
							"TType"=>"D",
							"Amount"=>$TotalNetOrderAmt,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
							$Ord++;
							if($TotalDiscAmt>0){
								// Discount Account Ledger
								$ledgerdata_debit=array(
								"PlantID"=>$selected_company,
								"FY"=>$fy,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"VoucherID"=>$saleid,
								"AccountID"=>"DISC",
								"EffectOn" => $order_data->AccountID,
								"TType"=>"D",
								"Amount"=>$TotalDiscAmt,
								"Narration"=>$narration,
								"PassedFrom"=>"SALE",
								"OrdinalNo"=>$Ord,
								"UserID"=>$this->session->userdata('username')
								);
								$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
								$Ord++;
							}
							if($TotalDiscOnSaleAmt>0){
								// Discount ON SALE Account Ledger
								$ledgerdata_debit=array(
								"PlantID"=>$selected_company,
								"FY"=>$fy,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"VoucherID"=>$saleid,
								"AccountID"=>"DISC",
								"EffectOn" => $order_data->AccountID,
								"TType"=>"D",
								"Amount"=>$TotalDiscOnSaleAmt,
								"Narration"=>$narration,
								"PassedFrom"=>"SALE",
								"OrdinalNo"=>$Ord,
								"UserID"=>$this->session->userdata('username')
								);
								$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
								$Ord++;
							}
							
							if($Y > 0){
								$ledgerdata_tcs=array(
								"PlantID"=>$selected_company,
								"FY"=>$fy,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"VoucherID"=>$saleid,
								"AccountID"=>"TCS",
								"EffectOn" => $order_data->AccountID,
								"TType"=>"C",
								"Amount"=>$Y,
								"Narration"=>$narration_tcs,
								"PassedFrom"=>"SALE",
								"OrdinalNo"=>$Ord,
								"UserID"=>$this->session->userdata('username'),
								);
								$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
								$Ord++;
								$ledgerdata_tcs=array(
								"PlantID"=>$selected_company,
								"FY"=>$fy,
								"Transdate"=>$date,
								"TransDate2"=>date('Y-m-d H:i:s'),
								"VoucherID"=>$saleid,
								"AccountID"=>$order_data->AccountID,
								"EffectOn" => "TCS",
								"TType"=>"D",
								"Amount"=>$Y,
								"Narration"=>$narration_tcs,
								"PassedFrom"=>"SALE",
								"OrdinalNo"=>$Ord,
								"UserID"=>$this->session->userdata('username'),
								);
								$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
								$Ord++;
							}
							
							if($round_variation >=0){
								$rTType = "D";
								$round_variation_new = abs($round_variation);
								}else{
								$rTType = "C";
								$round_variation_new = abs($round_variation);
							}
							$ledgerdata_roundoff =array(
							"PlantID"=>$selected_company,
							"FY"=>$fy,
							"Transdate"=>$date,
							"TransDate2"=>date('Y-m-d H:i:s'),
							"VoucherID"=>$saleid,
							"AccountID"=>"ROUNDOFF",
							"EffectOn" => $order_data->AccountID,
							"TType"=>$rTType,
							"Amount"=>$round_variation_new,
							"Narration"=>$narration,
							"PassedFrom"=>"SALE",
							"OrdinalNo"=>$Ord,
							"UserID"=>$this->session->userdata('username')
							);
							$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);
						}// Sale Master insert
					}// Order detail exist 
				}// Order IDs List Loop'
				return true;
				}else{
				return false;
			}   
		}// Check Challan number exist or not
	}
	public function AddNewChallan($data, $expense = false)
	{
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == "1"){
			$GodownID = 'CSPL';
			}else if($selected_company == "2"){
			$GodownID = 'CFF';
			}else if($selected_company == "3"){
			$GodownID = 'CBUPL';
		}
		$fy = $this->session->userdata('finacial_year');
		$time = date('H:i:s');
		
		$date = to_sql_date($data['date'])." ".$time;
		$newmonth = substr($date,5,2);
		$tcs_detail = $this->get_tcsper();
		$tcsper = $tcs_detail[0]['tcs'];
		
		if($data['vehicle'] == "TV" || $data['vehicle'] == "SELF"){
			$vehicle_capacity = $data['vahicle_capacity1'];
			$vahicle_number = $data['vahicle_number'];
			}else{
			$vehicle_capacity = $data['vahicle_capacity'];
			$vahicle_number = $data['vehicle'];
		}
		
		$order_ids = $data["order_id"];
		
		$declineOrder = array();
		$ClientWiseOrdersAmount = array();
		// Check Account Balance limit
		foreach ($order_ids as $orderid1) {
			$order_data1 = $this->GetUnbilledOrderDetails($orderid1);
			if($order_data1->bill_till_bal == "Y"){
				$sum_bal = $order_data1->BAL1 + $order_data1->BAL2 + $order_data1->BAL3 + $order_data1->BAL4 + $order_data1->BAL5 + $order_data1->BAL6 + $order_data1->BAL7 + $order_data1->BAL8 + $order_data1->BAL9 + $order_data1->BAL10 + $order_data1->BAL11 + $order_data1->BAL12 + $order_data1->BAL13;
				if($sum_bal >= 0){
					set_alert('warning', "Challan Amt is greater than Balance Amt... ");
					$redUrl = admin_url('challan/challanAddEdit');
					redirect($redUrl);
					}else{
					if(abs($sum_bal) >= $data['txtchalanvalue']){
						$available = true;
						}else{
						set_alert('warning', "Challan amt is greater than balance Amt... ");
						$redUrl = admin_url('challan/challanAddEdit');
						redirect($redUrl);
					}
				}
			}
			
			$ClientWiseOrdersAmount[] = [
			'AccountID'=>$order_data1->AccountID,
			'CurrentBal'=>$order_data1->client_actbal,
			'CreditLimit'=>$order_data1->MaxCrdAmt,
			]; 
			
			
		}
		
		// Group by AccountID and sum OrderAmount
		$FinalAccountWiseOrders = [];
		
		foreach ($ClientWiseOrdersAmount as $row) {
			$accountId = $row['AccountID'];
			$CurrentBal =  -1 * floatval($row['CurrentBal']);
			if (!isset($FinalAccountWiseOrders[$accountId])) {
				$FinalAccountWiseOrders[$accountId] = [
				'AccountID' => $row['AccountID'],
				'CurrentBal' =>$CurrentBal,
				'CreditLimit' => $row['CreditLimit'],
				'OrderAbleAmt' => $CurrentBal + $row['CreditLimit'],
				];
			} 
		}
		
		foreach ($order_ids as $orderid1) {
			$order_data1 = $this->GetUnbilledOrderDetails($orderid1);
			$ChkNetOrderAmt = 0;
			foreach ($order_data1->items as $key=>$value) {
				$crate_qty_name = 'crates_'.$orderid1;
				$crateqty = $data[$crate_qty_name];
				$case_qty_name = 'cases_'.$orderid1;
				$caseqty = $data[$case_qty_name];
				$qty_name = 'qty_'.$orderid1.'_'.$value['ItemID'];
				$qty = $data[$qty_name];
				$packQty = $value['CaseQty'];
				$orderQty =  $qty; 
				
				$BasicRate = $value['BasicRate'];
				
				
				$DiscPer = $value['DiscPerc'];
				$OrderAmt = $BasicRate * $orderQty;
				$TotalSaleAmt += $OrderAmt;
				$DiscAmt = $OrderAmt * ($DiscPer/100);
				$TotalDiscAmt += $DiscAmt;
				$TaxableAmt = $OrderAmt - $DiscAmt;
				$CGSTAmt = 0;
				$SGSTAmt = 0;
				$IGSTAmt = 0;
				// echo $orderQty;
				if($value['igst'] == "" || $value['igst'] == NULL || $value['igst'] == "0.00"){
					$CGSTAmt = ($TaxableAmt / 100 ) * $value['cgst'];
					$SGSTAmt = ($TaxableAmt / 100 ) * $value['sgst'];
					$TotalSGSTAmt += $SGSTAmt;
					$ChkNetOrderAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt;
					}else{
					$IGSTAmt = ($TaxableAmt / 100 ) * $value['igst'];
					$ChkNetOrderAmt = $TaxableAmt + $IGSTAmt;
				}
			}
			
			if($order_data1->credit_apply == 'Y'){
				// echo $FinalAccountWiseOrders[$order_data1->AccountID]['OrderAbleAmt'];die;
				if($FinalAccountWiseOrders[$order_data1->AccountID]['OrderAbleAmt'] >= $ChkNetOrderAmt){
					$FinalAccountWiseOrders[$order_data1->AccountID]['OrderAbleAmt'] = $FinalAccountWiseOrders[$order_data1->AccountID]['OrderAbleAmt'] - $ChkNetOrderAmt;
					}else{
					array_push($declineOrder,$orderid1);
					
					$UpdateChallan = array(
					'credit_exceed'=>'Y',
					); 
					$this->db->where('OrderID', $orderid1);
					$this->db->update(db_prefix() . 'ordermaster', $UpdateChallan);
					
					$this->db->select(db_prefix() . 'staff.*');
					$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);
					$this->db->where(db_prefix() . 'staff.admin', '1');
					$AdminStaff = $this->db->get(db_prefix() . 'staff')->result_array();
					foreach($AdminStaff as $admin){
						
						$Notification_msg = "Credit Limit Reached! Challan can not be generated for order ".$orderid1;
						$notification_data = [
						'description' => $Notification_msg,
						'touserid'    => $admin['staffid'],
						'link'        => 'order/LimitExceededOrders',
						]; 
						$notification_data['additional_data'] = serialize([
						'Order ID. '.$orderid1,
						]);
						
						if (add_notification($notification_data)) {
							pusher_trigger_notification($admin['staffid']);
						}
					}
					
				}
			}
		}
		
		if(count($declineOrder) == count($order_ids)){
			set_alert('warning', "0 Createable Orders. Credit Limit Reached...");
			$redUrl = admin_url('challan/challanAddEdit');
			redirect($redUrl);
		}
		
		if($selected_company == 1){
			$next_challan_number = get_option('next_challan_number_for_gf');
			}/*elseif($selected_company == 2){
			$next_challan_number = get_option('next_challan_number_for_cff');
			}elseif($selected_company == 3){
			$next_challan_number = get_option('next_challan_number_for_cbu');
		}*/
		
		$ChallanID = "CHL".$fy.str_pad($next_challan_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
		
		$challandata_new = array(
		"PlantID"=>$selected_company,
		"FY"=>$fy,
		"ChallanID"=>$ChallanID,
		"RouteID"=>$data['route'],
		"VehicleID"=>$vahicle_number,
		"VehicleType"=>$data["vehicle"],
		"DriverID"=>$data['challan_driver'],
		"LoaderID"=>$data['challan_loader'],
		"SalesmanID"=>$data['challan_sales_man'],
		"remark"=>$data['remark'],
		// "buyer_ord_no"=>$data['buyer_ord_no'],
		"Crates"=>0,
		"Cases"=>0,
		"ChallanAmt"=>0,
		'cnfid'   => "1",
		"UserID"=>$this->session->userdata('username'),
		"Transdate"=>$date
		);
		$TotalChlAmount = 0;
		$TotalChlCaases = 0;
		$TotalChlCreats = 0;
		if($this->db->insert(db_prefix() . 'challanmaster', $challandata_new)){
			$this->increment_next_number();
			// Add Challan Driver Details
			if($data['vehicle'] !== "TV" && $data['vehicle'] !== "SELF"){
				$driver_Eng =array(
				"PlantID"=>$selected_company,
				"AccountID"=>$data['challan_driver'],
				"SLDTypeID"=>3,
				"EngageID"=>$vahicle_number,
				"UserID"=>$this->session->userdata('username'),
				);
				$this->db->insert(db_prefix() . 'accountsld', $driver_Eng);
			}
			
			// Add Challan Loader Details
			$loader_Eng =array(
			"PlantID"=>$selected_company,
			"AccountID"=>$data['challan_loader'],
			"SLDTypeID"=>2,
			"EngageID"=>$vahicle_number,
			"UserID"=>$this->session->userdata('username'),
			);
			$this->db->insert(db_prefix() . 'accountsld', $loader_Eng);
			
			// Add Challan Sales man detail
			$salesman_Eng =array(
			"PlantID"=>$selected_company,
			"AccountID"=>$data['challan_sales_man'],
			"SLDTypeID"=>1,
			"EngageID"=>$vahicle_number,
			"UserID"=>$this->session->userdata('username'),
			);
			$this->db->insert(db_prefix() . 'accountsld', $salesman_Eng);
			
			// Add Order details
			foreach ($order_ids as $orderid) 
			{
				if(in_array($orderid,$declineOrder)){
					continue;
				}
				$order_data = $this->GetUnbilledOrderDetails($orderid);
				/*echo "<pre>";
					print_r($order_data);
				die;*/
				$disttype = $order_data->DistributorType;
				$state = $order_data->state;
				$orderdate = date('d/m/Y',strtotime($order_data->Transdate));
				$item_count = count($order_data->items);
				if($order_data->ActSalestype =="Sales"){
					if($order_data->OrderType=="TaxItems"){
						$bt = "T";
						if($selected_company == 1){
							$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
							}elseif($selected_company == 2){
							$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
							}elseif($selected_company == 3){
							$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
						}
						$full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
						if($fy == "25"){
							$saleid = "TAX".$full_tax_number;
							}else{
							$saleid = "TAX".$fy.$full_tax_number;
						}
						
						}else {
						$bt = "B";
						if($selected_company == 1){
							$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
							}elseif($selected_company == 2){
							$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
							}elseif($selected_company == 3){
							$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
						}
						$full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
						if($fy == "25"){
							$saleid = "BOS".$full_nontax_number;
							}else{
							$saleid = "BOS".$fy.$full_nontax_number;
						}
						
					}
					}elseif($order_data->ActSalestype =="StockTransfer"){
					$bt = "M";
					if($selected_company == 1){
						$new_trnNumber = get_option('next_trn_number_for_cspl');
						}elseif($selected_company == 2){
						$new_trnNumber = get_option('next_trn_number_for_cff');
						}elseif($selected_company == 3){
						$new_trnNumber = get_option('next_trn_number_for_cbu');
					}
					$full_trnnumber = str_pad($new_trnNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
					
					if($fy == "25"){
						$saleid = "TRN".$full_trnnumber;
						}else{
						$saleid = "TRN".$fy.$full_trnnumber;
					}
					}elseif($order_data->ActSalestype =="CNF"){
					$bt = "C";
					if($selected_company == 1){
						$new_cnfNumber = get_option('next_cnf_number_for_cspl');
						}elseif($selected_company == 2){
						$new_cnfNumber = get_option('next_cnf_number_for_cff');
						}elseif($selected_company == 3){
						$new_cnfNumber = get_option('next_cnf_number_for_cbu');
					}
					$full_cnfnumber = str_pad($new_cnfNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
					if($fy == "25"){
						$saleid = "CNF".$full_cnfnumber;
						}else{
						$saleid = "CNF".$fy.$full_cnfnumber;
					}
					}else{
					if($order_data->OrderType=="TaxItems"){
						$bt = "T";
						if($selected_company == 1){
							$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cspl');
							}elseif($selected_company == 2){
							$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cff');
							}elseif($selected_company == 3){
							$new_tax_transactionNumber = get_option('next_tax_transaction_number_for_cbu');
						}
						$full_tax_number = str_pad($new_tax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
						if($fy == "25"){
							$saleid = "TAX".$full_tax_number;
							}else{
							$saleid = "TAX".$fy.$full_tax_number;
						}
						}else {
						$bt = "B";
						if($selected_company == 1){
							$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cspl');
							}elseif($selected_company == 2){
							$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cff');
							}elseif($selected_company == 3){
							$new_nontax_transactionNumber = get_option('next_nontax_transaction_number_for_cbu');
						}
						$full_nontax_number = str_pad($new_nontax_transactionNumber, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
						if($fy == "25"){
							$saleid = "BOS".$full_nontax_number;
							}else{
							$saleid = "BOS".$fy.$full_nontax_number;
						}
					}
				}
				
				$DeliverySequence = 'Sequence_'.$orderid;
				$DeliveryPoint = $data[$DeliverySequence];
				
				$salesdata_new = array(
				"PlantID"=>$selected_company,
				"FY"=>$fy,
				"BT"=>$bt,
				"SalesID"=>$saleid,
				"Transdate"=>$date,
				"OrderID"=>$orderid,
				"ChallanID"=>$ChallanID,
				"AccountID"=>$order_data->AccountID,
				"AccountID2"=>$order_data->AccountID2,
				"cd_applicable"=>$order_data->cd_applicable,
				"DeliveryPoint"=>$DeliveryPoint,
				"ItCount"=>$item_count,
				"cnfid"=>1,
				"tcs"=>$tcsper,
				"tcsAmt"=>0,
				"PayType"=>"C",
				"SaleAmt"=>0,
				"DiscAmt"=>0,
				"sgstamt"=>0,
				"cgstamt"=>0,
				"igstamt"=>0,
				"BillAmt"=>0,
				"RndAmt"=>0,
				"UserID"=>$this->session->userdata('username'),
				);
				if($order_data->GSTNO !== NULL && $order_data->GSTNO !== ''){
					$salesdata_new['gstno'] = $order_data->GSTNO;
				}
				
				if($this->db->insert(db_prefix() . 'salesmaster', $salesdata_new)){
					if($order_data->ActSalestype =="Sales"){
						if($bt =="T"){
							$this->increment_next_tax_transaction_number();
							}else{
							$this->increment_next_nontax_transaction_number();
						}
						}elseif($order_data->ActSalestype =="StockTransfer"){
						$this->increment_trn_transaction_number();
						}elseif($order_data->ActSalestype =="CNF"){
						$this->increment_cnf_transaction_number();
						}else{
						if($bt =="T"){
							$this->increment_next_tax_transaction_number();
							}else{
							$this->increment_next_nontax_transaction_number();
						}
					}
					
					$TotalSaleAmt = 0;
					$TotalDiscAmt = 0;
					$TotalDiscOnSaleAmt = 0;
					$TotalCGSTAmt = 0;
					$TotalSGSTAmt = 0;
					$TotalGSTAmt = 0;
					$TotalIGSTAmt = 0;
					$TotalCases = 0;
					$TotalCrates = 0;
					
					
					$this->db->where('OrderID', $orderid);  
					$this->db->where('TType', 'O');  
					$this->db->where('TType2', 'Free Distribution'); 
					$this->db->delete(db_prefix() . 'history');
					
					$ExistingItemsIDs = array();
					foreach($order_data->items as $exitemkey => $exitemvalue)
					{
						array_push($ExistingItemsIDs,$exitemvalue['ItemID']);
					}
					$GetMultipleScheme = $this->GetMultipleSchemeData($disttype,$state,$orderdate,$ExistingItemsIDs);
					
					$i = 0;
					foreach ($GetMultipleScheme as $Scheme) {
						${'TotalBilledQty' . $i} = 0; 
						$i++;
					}
					
					$SingleFreeItem = array();
					$SingleDiscItem = array();
					foreach ($order_data->items as $exkey => $exvalue) 
					{
						$Item = $exvalue['ItemID'];
						
						$qty_name = 'qty_'.$orderid.'_'.$exvalue['ItemID'];
						$qty = $data[$qty_name];
						$i = 0;
						$IsApplyMultipleScheme = 0;
						foreach($GetMultipleScheme as $Scheme){
							$SchemeItems = $Scheme["ItemID"];
							$SchemeItemsArr = explode(',',$SchemeItems);
							if(in_array($Item,$SchemeItemsArr)){
								${'TotalBilledQty' . $i} += $qty;
								$IsApplyMultipleScheme++;
								break;
							}
							$i++;
						}
						
						$IsFreeItem = 0;
						if($IsApplyMultipleScheme == 0){
							$ChkSingleScheme = $this->GetSchemeData($disttype,$state,$orderdate,$exvalue['ItemID']);
							if(!empty($ChkSingleScheme)){
								foreach($ChkSingleScheme as $each){
									if($qty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
										$Disc_pkt = floor($qty / $each['SlabQty']) * $each['Disc_pkt'];
										$SingleFreeItem[$exvalue["ItemID"]] = $Disc_pkt;
										$IsFreeItem++;
										break;
									}
								}
							}
						}
						if($IsApplyMultipleScheme == 0 && $IsFreeItem == 0){
							$getDiscscheme = $this->GetDiscSchemeData($disttype,$state,$orderdate,$exvalue['ItemID']);
							if(!empty($getDiscscheme)){
								foreach($getDiscscheme as $each){
									if($qty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
										$SingleDiscItem[$exvalue["ItemID"]] = $each["DiscPerc"];
										break;
									}
								}
							}
						}
						
						
					}
					
					$i = 0;
					foreach($GetMultipleScheme as $Scheme){
						$GetMultipleScheme[$i]["TotalBilledQty"] = ${'TotalBilledQty' . $i};
						$i++;
					}
					
					
					foreach ($order_data->items as $key=>$value) {
						$crate_qty_name = 'crates_'.$orderid;
						$crateqty = $data[$crate_qty_name];
						$case_qty_name = 'cases_'.$orderid;
						$caseqty = $data[$case_qty_name];
						$qty_name = 'qty_'.$orderid.'_'.$value['ItemID'];
						$qty = $data[$qty_name];
						$packQty = $value['CaseQty'];
						$orderQty =  $qty; // $packQty * Add If Needed
						if($value["SuppliedIn"] == "CR"){
							// $TotalCrates += ($orderQty/$packQty);
							$TotalCrates = $crateqty;
							}else{
							// $TotalCases += ($orderQty/$packQty);
							$TotalCases = $caseqty;
						}
						$BasicRate = $value['BasicRate'];
						
						
						// $getDiscscheme = $this->GetDiscSchemeData($disttype,$state,$orderdate,$value['ItemID']);
						// if(!empty($getDiscscheme)){
						// foreach($getDiscscheme as $each){
						// if($orderQty >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
						// $value['DiscPerc'] = $each['DiscPerc'];
						// }
						// }
						// }
						
						if (array_key_exists($value['ItemID'], $SingleDiscItem)) {
							$value['DiscPerc'] = $SingleDiscItem[$value['ItemID']];
						}
						
						$DiscPer = $value['DiscPerc'];
						$OrderAmt = $BasicRate * $orderQty;
						$TotalSaleAmt += $OrderAmt;
						$DiscAmt = $OrderAmt * ($DiscPer/100);
						$TotalDiscAmt += $DiscAmt;
						$TaxableAmt = $OrderAmt - $DiscAmt;
						$CGSTAmt = 0;
						$SGSTAmt = 0;
						$IGSTAmt = 0;
						if($value['igst'] == "" || $value['igst'] == NULL || $value['igst'] == "0.00"){
							$CGSTAmt = ($TaxableAmt / 100 ) * $value['cgst'];
							$TotalCGSTAmt += $CGSTAmt;
							$SGSTAmt = ($TaxableAmt / 100 ) * $value['sgst'];
							$TotalSGSTAmt += $SGSTAmt;
							$NetOrderAmt = $TaxableAmt + $CGSTAmt + $SGSTAmt;
							}else{
							$IGSTAmt = ($TaxableAmt / 100 ) * $value['igst'];
							$TotalIGSTAmt += $IGSTAmt;
							$NetOrderAmt = $TaxableAmt + $IGSTAmt;
						}
						$TotalNetOrderAmt += $NetOrderAmt;
						//echo $data[$qty];
						$update_array = array(
						'TransDate2' =>$date,
						// 'eOrderQty'=>$orderQty,
						'BilledQty' => $orderQty,
						'DiscPerc' => $DiscPer,
						'DiscAmt' => $DiscAmt,
						'TransID'   => $saleid,
						'BillID' => $ChallanID,
						'OrderAmt'=>$OrderAmt,
						'ChallanAmt' => $OrderAmt,
						'cgstamt'=>$CGSTAmt,
						'sgstamt'=>$SGSTAmt,
						'igstamt'=>$IGSTAmt,
						'NetOrderAmt'=>$NetOrderAmt,
						'NetChallanAmt' => $NetOrderAmt,
						'GodownID'=>$GodownID,
						'UserID2'=>$this->session->userdata('username'),
						'Lupdate'=>date('Y-m-d H:i:s'),
						);
						$this->db->where('PlantID', $selected_company);
						$this->db->where('FY', $fy);  
						$this->db->where('OrderID', $orderid);
						$this->db->where('ItemID', $value['ItemID']);
						$this->db->where('TType', 'O');
						$this->db->where('TType2', 'Order');
						$this->db->update(db_prefix() . 'history', $update_array);
						
					}
					
					foreach($GetMultipleScheme as $each){
						if($each["TotalBilledQty"] >= $each['SlabQty'] &&  $each['SlabQty'] > 0){
							$Disc_pkt = floor($each["TotalBilledQty"] / $each['SlabQty']) * $each['Disc_pkt'];
							
							$new_item_data = array(
							"PlantID" =>$selected_company,
							"FY" =>$this->session->userdata('finacial_year'),
							"OrderID" =>$orderid,
							'TransID'   => $saleid,
							'BillID' => $ChallanID,
							"TType" =>"O",
							"TType2" =>'Free Distribution',
							"AccountID" =>$order_data->AccountID,
							"ItemID" =>$each['FreeItemID'],
							"BasicRate" =>0,
							"SuppliedIn" =>'',
							"OrderQty" => $Disc_pkt,
							"eOrderQty" => $Disc_pkt,
							"BilledQty" => $Disc_pkt,
							"SaleRate" =>0,
							"DiscPerc" =>100,
							"DiscAmt" =>0,
							"cgst" =>0,
							"cgstamt" =>0,
							"sgst" =>0,
							"sgstamt" =>0,
							"igst" =>0,
							"igstamt" =>0,
							"CaseQty" =>1,
							"OrderAmt" =>0,
							"NetOrderAmt" =>0,
							"UserID" =>$this->session->userdata('username'),
							"TransDate2"=>$date,
							"TransDate" =>to_sql_date($orderdate)." ".date("H:i:s"),
							);
							$this->db->insert(db_prefix() . 'history', $new_item_data);
						}
					}
					
					foreach($SingleFreeItem as $ItemID => $FreeQty){
						$new_item_data = array(
						"PlantID" =>$selected_company,
						"FY" =>$this->session->userdata('finacial_year'),
						"OrderID" =>$orderid,
						'TransID'   => $saleid,
						'BillID' => $ChallanID,
						"TType" =>"O",
						"TType2" =>'Free Distribution',
						"AccountID" =>$order_data->AccountID,
						"ItemID" =>$each['FreeItemID'],
						"BasicRate" =>0,
						"SuppliedIn" =>'',
						"OrderQty" => $Disc_pkt,
						"eOrderQty" => $Disc_pkt,
						"BilledQty" => $Disc_pkt,
						"SaleRate" =>0,
						"DiscPerc" =>100,
						"DiscAmt" =>0,
						"cgst" =>0,
						"cgstamt" =>0,
						"sgst" =>0,
						"sgstamt" =>0,
						"igst" =>0,
						"igstamt" =>0,
						"CaseQty" =>1,
						"OrderAmt" =>0,
						"NetOrderAmt" =>0,
						"UserID" =>$this->session->userdata('username'),
						"TransDate2"=>$date,
						"TransDate" =>to_sql_date($orderdate)." ".date("H:i:s"),
						);
						$this->db->insert(db_prefix() . 'history', $new_item_data);
					}
					
					$TotalNetGSTAMt = $TotalSGSTAmt + $TotalCGSTAmt + $TotalIGSTAmt;
					$NetOrderAmt = $TotalSaleAmt + $TotalNetGSTAMt - $TotalDiscAmt - $TotalDiscOnSaleAmt;
					if($order_data->istcs=="1"){
						$p = $tcsper/100;
						$tcsable_amt = round($NetOrderAmt);
						$Y = $p * $tcsable_amt;
						}else {
						$tcsper = 0.00;
						$Y = 0.00;
					}
					$NetOrderAmt = $NetOrderAmt + $Y;
					$RndAmt = round($NetOrderAmt);
					$roundup2 = $NetOrderAmt - $RndAmt;
					$round_variation = $roundup2;
					// Update Sale Master
					$UpdateSale = array(
					"tcsAmt"=>$Y,
					"SaleAmt"=>($TotalSaleAmt-$TotalDiscAmt),
					"DiscOnSaleAmt"=>$TotalDiscOnSaleAmt,
					"DiscAmt"=>$TotalDiscAmt,
					"sgstamt"=>$TotalSGSTAmt,
					"cgstamt"=>$TotalCGSTAmt,
					"igstamt"=>$TotalIGSTAmt,
					"BillAmt"=>$NetOrderAmt,
					"RndAmt"=>round($NetOrderAmt),
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('FY', $fy);  
					$this->db->where('OrderID', $orderid);
					$this->db->update(db_prefix() . 'salesmaster', $UpdateSale);
					
					// Update Order Master
					$UpdateOrder = array(
					'ChallanID'=>$ChallanID,
					'SalesID'=>$saleid,
					'DiscAmt'=>$TotalDiscAmt,
					'OrderAmt'=>$NetOrderAmt,
					'DiscOnSaleAmt'=>$TotalDiscOnSaleAmt,
					'tcsAmt'=>$Y,
					'Crates'=>ceil($TotalCrates),
					'Cases'=>ceil($TotalCases),
					'total_tax'=>$TotalNetGSTAMt,
					'UserID2'=>$this->session->userdata('username'),
					'Lupdate'=>date('Y-m-d H:i:s'),
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('FY', $fy);  
					$this->db->where('OrderID', $orderid);
					$this->db->update(db_prefix() . 'ordermaster', $UpdateOrder);
					
					// Update Challan Master table
					
					$TotalChlAmount += $NetOrderAmt;
					$TotalChlCaases += ceil($TotalCases);
					$TotalChlCreats += ceil($TotalCrates);
					$UpdateChallan = array(
					'ChallanAmt'=>$TotalChlAmount,
					'Crates'=>$TotalChlCreats,
					'Cases'=>$TotalChlCaases,
					);
					$this->db->where('PlantID', $selected_company);
					$this->db->where('FY', $fy);  
					$this->db->where('ChallanID', $ChallanID);
					$this->db->update(db_prefix() . 'challanmaster', $UpdateChallan);
					
					// Add Create Ledger
					if($TotalCrates >0){
						// create ledger 
						$narration_create = "Against SalesID ".$saleid."/ ChallanID ".$ChallanID;
						$create_ledgerdata = array(
						"PlantID"=>$selected_company,
						"VoucherID"=>$saleid,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"ChallanID"=>$ChallanID,
						"AccountID"=>$order_data->AccountID2,
						"TType"=>"D",
						"Qty"=>$TotalCrates,
						"PassedFrom"=>"CHALLAN",
						"Narration"=>$narration_create,
						"OrdinalNo"=>1,
						"UserID"=>$this->session->userdata('username'),
						"FY"=>$fy
						);
						$this->db->insert(db_prefix() . 'accountcrates', $create_ledgerdata);
					}
					
					// Add Ledger Entry
					$narration = "By SalesID ".$saleid."/".$ChallanID; 
					$narration_tcs = "TCS@0.1000% on SalesID ".$saleid."/".$ChallanID;
					$Ord = 1;
					// SALE Ledger
					$ledgerdata_credit=array(
					"PlantID"=>$selected_company,
					"FY"=>$fy,
					"Transdate"=>$date,
					"TransDate2"=>date('Y-m-d H:i:s'),
					"VoucherID"=>$saleid,
					"AccountID"=>"SALE",
					"EffectOn" => $order_data->AccountID,
					"TType"=>"C",
					"Amount"=>$TotalSaleAmt,
					"Narration"=>$narration,
					"PassedFrom"=>"SALE",
					"OrdinalNo"=>$Ord,
					"UserID"=>$this->session->userdata('username')
					);
					$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit);
					$Ord++;
					if($TotalIGSTAmt >0){
						$acct_name3 = "IGST";
						$ledgerdata_credit_igst=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$saleid,
						"AccountID"=>$acct_name3,
						"EffectOn" => $order_data->AccountID,
						"TType"=>"C",
						"Amount"=>$TotalIGSTAmt,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_igst);
						$Ord++;
						}else{
						$acct_name1 = "SGST";
						$acct_name2 = "CGST";
						$ledgerdata_credit_sgst=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$saleid,
						"AccountID"=>$acct_name1,
						"EffectOn" => $order_data->AccountID,
						"TType"=>"C",
						"Amount"=>$TotalSGSTAmt,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_sgst);
						$Ord++;
						$ledgerdata_credit_cgst=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$saleid,
						"AccountID"=>$acct_name2,
						"EffectOn" => $order_data->AccountID,
						"TType"=>"C",
						"Amount"=>$TotalCGSTAmt,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_credit_cgst);
						$Ord++;
					}
					// Party Account Ledger
					$ledgerdata_debit=array(
					"PlantID"=>$selected_company,
					"FY"=>$fy,
					"Transdate"=>$date,
					"TransDate2"=>date('Y-m-d H:i:s'),
					"VoucherID"=>$saleid,
					"AccountID"=>$order_data->AccountID,
					"EffectOn" => 'SALE',
					"TType"=>"D",
					"Amount"=>$RndAmt,
					"Narration"=>$narration,
					"PassedFrom"=>"SALE",
					"OrdinalNo"=>$Ord,
					"UserID"=>$this->session->userdata('username')
					);
					$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
					$Ord++;
					if($TotalDiscAmt > 0){
						// Discount Account Ledger
						$ledgerdata_debit=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$saleid,
						"AccountID"=>"DISC",
						"EffectOn" => $order_data->AccountID,
						"TType"=>"D",
						"Amount"=>$TotalDiscAmt,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
						$Ord++;
					}
					
					if($TotalDiscOnSaleAmt > 0){
						// Discount ON Sale Account Ledger
						$ledgerdata_debit=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$saleid,
						"AccountID"=>"DISC",
						"EffectOn" => $order_data->AccountID,
						"TType"=>"D",
						"Amount"=>$TotalDiscOnSaleAmt,
						"Narration"=>$narration,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username')
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_debit);
						$Ord++;
					}
					// TCS Ledger Entry
					if($Y > 0){
						$ledgerdata_tcs=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$saleid,
						"AccountID"=>"TCS",
						"EffectOn" => $order_data->AccountID,
						"TType"=>"C",
						"Amount"=>$Y,
						"Narration"=>$narration_tcs,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
						$Ord++;
						$ledgerdata_tcs=array(
						"PlantID"=>$selected_company,
						"FY"=>$fy,
						"Transdate"=>$date,
						"TransDate2"=>date('Y-m-d H:i:s'),
						"VoucherID"=>$saleid,
						"AccountID"=>$acct_name2,
						"EffectOn" => 'TCS',
						"TType"=>"D",
						"Amount"=>$Y,
						"Narration"=>$narration_tcs,
						"PassedFrom"=>"SALE",
						"OrdinalNo"=>$Ord,
						"UserID"=>$this->session->userdata('username'),
						);
						$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_tcs);
						$Ord++;
					}
					
					if($round_variation >=0){
						$rTType = "D";
						$round_variation_new = abs($round_variation);
						}else{
						$rTType = "C";
						$round_variation_new = abs($round_variation);
					}
					$ledgerdata_roundoff =array(
					"PlantID"=>$selected_company,
					"FY"=>$fy,
					"Transdate"=>$date,
					"TransDate2"=>date('Y-m-d H:i:s'),
					"VoucherID"=>$saleid,
					"AccountID"=>"ROUNDOFF",
					"EffectOn" => $order_data->AccountID,
					"TType"=>$rTType,
					"Amount"=>$round_variation_new,
					"Narration"=>$narration,
					"PassedFrom"=>"SALE",
					"OrdinalNo"=>$Ord,
					"UserID"=>$this->session->userdata('username')
					);
					$this->db->insert(db_prefix() . 'accountledger', $ledgerdata_roundoff);
					
					$Reconsile_Arr =array(
					"TransDate"=>$date,
					"TransID"=>$saleid,
					"AccountID"=>$order_data->AccountID,
					"Amount"=>round($NetOrderAmt),
					"TType"=>"DR",
					"PassedFrom"=>"SALE",
					"Status"=>"N",
					"UserID"=>$this->session->userdata('username')
					);
					$this->db->insert(db_prefix() . 'ReconsileMaster', $Reconsile_Arr);
				}// Sale Master insert
			}// Order IDs List Loop
		}// Challan Master Insert 
		return $ChallanID; 
	}
	
	/*
		Last transaction account balance
	*/
	public function get_last_account_bal($dis_id = '')
	{
		$query = $this->db->query("SELECT * FROM tblpayment WHERE id = (SELECT MAX(id) FROM tblpayment WHERE distibutor_id = '".$dis_id."')");
		if ($query->num_rows()== 1)
		{
			$row = $query->row();
			return $row;
		}
		/*$this->db->select('*');
			$this->db->from(db_prefix() . 'payment');
			$this->db->where('distibutor_id',$dis_id);
			$data = $this->db->get()->result_array();
			return $data;
			$ss = "madhav";
		return $ss;*/
	}
	
	/*
		
		Get Order Details 
	*/
	
	public function get_order_detail($id = '', $where = [])
	{
		$this->db->select('*, ' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'order.id as id, ' . db_prefix() . 'currencies.name as currency_name');
		$this->db->from(db_prefix() . 'order');
		$this->db->join(db_prefix() . 'currencies', '' . db_prefix() . 'currencies.id = ' . db_prefix() . 'order.currency', 'left');
		$this->db->where($where);
		if (is_numeric($id)) {
			$this->db->where(db_prefix() . 'order' . '.id', $id);
			$order_detail = $this->db->get()->row();
			if ($order_detail) {
				$order_detail->total_left_to_pay = get_invoice_total_left_to_pay($order_detail->id, $order_detail->total);
				
				$order_detail->items       = get_items_by_type2('order', $id);
				
			}
			
			return hooks()->apply_filters('get_invoice', $order_detail);
		}
		
		$this->db->order_by('number,YEAR(date)', 'desc');
		
		return $this->db->get()->result_array();
	}
	//================= Increment Next Challan Number ==============================
	public function increment_next_number()
	{
		// Update next CHALLAN number in settings
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == 1){
			$this->db->where('name', 'next_challan_number_for_gf');
			}/*elseif($selected_company == 2){
			$this->db->where('name', 'next_challan_number_for_cff');
			}elseif($selected_company == 3){
			$this->db->where('name', 'next_challan_number_for_cbu');
			}elseif($selected_company == 4){
			$this->db->where('name', 'next_challan_number_for_cbupl');
		}*/
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	
	
	/**
		* @since  2.7.0
		*
		* Increment the TAX Transaction next nubmer
		*
		* @return void
	*/
	public function increment_next_tax_transaction_number()
	{
		// Update next TAX Transaction number in settings
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == 1){
			$this->db->where('name', 'next_tax_transaction_number_for_cspl');
			}elseif($selected_company == 2){
			$this->db->where('name', 'next_tax_transaction_number_for_cff');
			}elseif($selected_company == 3){
			$this->db->where('name', 'next_tax_transaction_number_for_cbu');
			}elseif($selected_company == 4){
			$this->db->where('name', 'next_tax_transaction_number_for_cbupl');
		}
		
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	
	public function increment_next_nontax_transaction_number()
	{
		// Update next NONTAX Transaction number in settings
		//$this->db->where('name', 'next_nontax_transaction_number');
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == 1){
			$this->db->where('name', 'next_nontax_transaction_number_for_cspl');
			}elseif($selected_company == 2){
			$this->db->where('name', 'next_nontax_transaction_number_for_cff');
			}elseif($selected_company == 3){
			$this->db->where('name', 'next_nontax_transaction_number_for_cbu');
			}elseif($selected_company == 4){
			$this->db->where('name', 'next_nontax_transaction_number_for_cbupl');
		}
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	
	public function increment_trn_transaction_number()
	{
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == 1){
			$this->db->where('name', 'next_trn_number_for_cspl');
			}elseif($selected_company == 2){
			$this->db->where('name', 'next_trn_number_for_cff');
			}elseif($selected_company == 3){
			$this->db->where('name', 'next_trn_number_for_cbu');
			}elseif($selected_company == 4){
			$this->db->where('name', 'next_trn_number_for_cbupl');
		}
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	
	public function increment_cnf_transaction_number()
	{
		$FY = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		if($selected_company == 1){
			$this->db->where('name', 'next_cnf_number_for_cspl');
			}elseif($selected_company == 2){
			$this->db->where('name', 'next_cnf_number_for_cff');
			}elseif($selected_company == 3){
			$this->db->where('name', 'next_cnf_number_for_cbu');
			}elseif($selected_company == 4){
			$this->db->where('name', 'next_cnf_number_for_cbupl');
		}
		$this->db->set('value', 'value+1', false);
		$this->db->WHERE('FY', $FY);
		$this->db->update(db_prefix() . 'options');
	}
	
	/**
		* @since  2.7.0
		*
		* Decrement the invoies next number
		*
		* @return void
	*/
	public function decrement_next_number()
	{
		$this->db->where('name', 'next_invoice_number');
		$this->db->set('value', 'value-1', false);
		$this->db->update(db_prefix() . 'options');
	}
	
	//========================== Get Shipping Address ==============================
	public function GetShippingAddress($Customer_id = "",$ShippingID = "")
	{
		$this->db->select('tblclients.AccountID,tblclients.vat,tblclients.company,tblclients.state,tblclients.phonenumber,tblcontacts.Pan,tblcontacts.email,tblclientwiseshippingdata.id,tblclientwiseshippingdata.ShippingState,tblclientwiseshippingdata.ShippingCity,
		tblclientwiseshippingdata.ShippingAdrees,tblclientwiseshippingdata.ShippingPin,tblxx_statelist.state_name,tblxx_citylist.city_name');
		$this->db->join(db_prefix() .'clientwiseshippingdata', db_prefix() .'clientwiseshippingdata.AccountID = '.db_prefix() .'clients.AccountID',"LEFT");
		$this->db->join(db_prefix() .'xx_statelist', db_prefix() .'xx_statelist.short_name = '.db_prefix() .'clientwiseshippingdata.ShippingState',"LEFT");
		$this->db->join(db_prefix() .'xx_citylist', db_prefix() .'xx_citylist.id = '.db_prefix() .'clientwiseshippingdata.ShippingCity',"LEFT");
		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID', 'LEFT');
		if($ShippingID){
			$this->db->where('tblclientwiseshippingdata.id', $ShippingID);
			return $this->db->get(db_prefix() . 'clients')->row();
			}else{
			$this->db->where('tblclients.AccountID', $Customer_id);
			$this->db->order_by('tblclientwiseshippingdata.IsBilling', 'DESC');
			return $this->db->get(db_prefix() . 'clients')->result_array(); 
		}
	}

		
	public function get_order_entry_details_PO($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$year = $this->session->userdata('finacial_year');
		$this->db->select('tblpurchaseordermaster.*,tblpurchaseordermaster.AccountID As Vendor,tblclients.*,tblxx_statelist.*,tblaccountbalances.*,tblxx_citylist.city_name','tblcontacts.*', 'tblcontacts.Pan');
		$this->db->from(db_prefix() . 'purchaseordermaster');
		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID', 'left');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');
		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'clients.city', 'left');
		$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND '.db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND '.db_prefix() . 'accountbalances.FY ="'.$year.'"', 'left');
		
		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID', 'LEFT');
		 
		$this->db->where(db_prefix() . 'purchaseordermaster.PurchID', $id);
		$this->db->where(db_prefix() . 'purchaseordermaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'purchaseordermaster.FY', $year);
		$result = $this->db->get()->row_array();
		
		if (!empty($result) && !empty($result['ShipToParty'])) {
			$ShipToAddress = $this->GetShippingAddress($result['ShipToParty']);

			//print_r($ShipToAddress); die;
			if (!empty($ShipToAddress) && isset($ShipToAddress[0])) {
				$address = $ShipToAddress[0]; 
				$phone_number = isset($address['phonenumber']) ? trim($address['phonenumber']) : 'Not Available';
				$result['ShipToPhoneNumber'] = $phone_number;
				$Pan_number = isset($address['Pan']) ? trim($address['Pan']) : 'Not Available';
				$result['ShipToPanNumber'] = $Pan_number;
				$email = isset($address['email']) ? trim($address['email']) : 'Not Available';
				$result['ShipToEmail'] = $email;
				$GSTNumber = isset($address['vat']) ? trim($address['vat']) : 'Not Available';
				$result['ShipToGST'] = $GSTNumber;
				$state = isset($address['state']) ? trim($address['state']) : 'Not Available';
				$result['ShipToState'] = $state;
				$AccoountName = isset($address['company']) ? trim($address['company']) : 'Not Available';
				$result['ShipToAccoountName'] = $AccoountName;
				$stateName = isset($address['state_name']) ? trim($address['state_name']) : 'Not Available';
				$result['ShipToStateName'] = $stateName;
				
		
				$formatted_address = trim($address['ShippingAdrees']) . ' ' . 
									 trim($address['city_name']) . ' (' . 
									 trim($address['ShippingState']) . ') - ' . 
									 trim($address['ShippingPin']);
									 
				$result['ShipToAddressFormatted'] = $formatted_address;
			}
		}

		//print_r($result); die;
		if(!empty($result))
		{
			$result["details"] = $this->get_order_entry_detail_full_PO($id);
		}
		return $result;
	}
	
	
	public function get_order_entry_detail_full_PO($poid){
		$selected_company = $this->session->userdata('root_company');
		$year = $_SESSION['finacial_year'];
		$this->db->select( db_prefix() . 'items_sub_groups.main_group_id,'.db_prefix() . 'history.*,tblhistory.DiscPerc as Disc,'.db_prefix() . 'items.*,'.db_prefix() . 'items_main_groups.name');
		// $this->db->select( db_prefix() . 'clients.company,'.db_prefix() . 'clients.userid,'.db_prefix() . 'clients.AccountID,');
		$this->db->from(db_prefix() . 'history');
		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID', 'left');
		$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'INNER');
		$this->db->join(db_prefix() . 'items_main_groups', db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_group_id', 'left');
		$this->db->where(db_prefix() . 'history.OrderID', $poid);
		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'history.FY', $year);
		$data = $this->db->get()->result_array();
		foreach($data  as $key => $value){
			$data[$key]['sub_total'] = $value['OrderAmt']+$value['cgstamt']+$value['sgstamt']+$value['igstamt'];
			$data[$key]['total'] = $value['OrderAmt']+$value['cgstamt']+$value['sgstamt']+$value['igstamt']+$value['DiscAmt'];
			
		}
		return $data;
		
	}
	
	public function get_order_entry_details($id)
	{
		$selected_company = $this->session->userdata('root_company');
		$year = $this->session->userdata('finacial_year');
		$this->db->select('tblpurchasemaster.*,tblpurchasemaster.AccountID As Vendor,tblclients.*,tblxx_statelist.*,tblaccountbalances.*');
		$this->db->from(db_prefix() . 'purchasemaster');
		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID', 'left');
		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');
		$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND '.db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND '.db_prefix() . 'accountbalances.FY ="'.$year.'"', 'left');
		$this->db->where(db_prefix() . 'purchasemaster.PurchID', $id);
		$this->db->where(db_prefix() . 'purchasemaster.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'purchasemaster.FY', $year);
		$result = $this->db->get()->row_array();
		
		if(!empty($result))
		{
			$result["details"] = $this->get_order_entry_detail_full($id);
		}
		return $result;
	}
	
	public function get_order_entry_detail_full($poid){
		$selected_company = $this->session->userdata('root_company');
		$year = $_SESSION['finacial_year'];
		$this->db->select( db_prefix() . 'items_sub_groups.main_group_id,'.db_prefix() . 'history.*,tblhistory.DiscPerc as Disc,'.db_prefix() . 'items.*,'.db_prefix() . 'items_main_groups.name');
		// $this->db->select( db_prefix() . 'clients.company,'.db_prefix() . 'clients.userid,'.db_prefix() . 'clients.AccountID,');
		$this->db->from(db_prefix() . 'history');
		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID', 'left');
		$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'left');
		$this->db->join(db_prefix() . 'items_main_groups', db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_group_id', 'left');
		$this->db->where(db_prefix() . 'history.OrderID', $poid);
		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'history.FY', $year);
		$data = $this->db->get()->result_array();
		foreach($data  as $key => $value){
			$data[$key]['sub_total'] = $value['OrderAmt']+$value['cgstamt']+$value['sgstamt']+$value['igstamt'];
			$data[$key]['total'] = $value['OrderAmt']+$value['cgstamt']+$value['sgstamt']+$value['igstamt']+$value['DiscAmt'];
		}
		return $data;
		
	}
	
	
	
	public function get_order_item_free($orderid,$itemid)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$this->db->where('PlantID', $selected_company);
		$this->db->where('OrderID', $orderid);
		$this->db->where('ItemID', $itemid);
		$this->db->where('FY', $fy);
		$this->db->where('TType', 'O');
		$this->db->where('TType2', 'Free Distribution');
		
		return $this->db->get(db_prefix() . 'history')->result_array();
	}
	
	public function fetchItemDetails($TransId){
		$selected_company = $this->session->userdata('root_company');
		$year = $_SESSION['finacial_year'];
		$this->db->select(db_prefix() . 'history.*,'.db_prefix() . 'items.hsn_code,'.db_prefix() . 'items.unit,'.db_prefix() . 'items.description');
		$this->db->from(db_prefix() . 'history');
		$this->db->join(db_prefix() . 'items', db_prefix() . 'history.ItemID = '.db_prefix() . 'items.item_code AND '.db_prefix() . 'history.PlantID = '.db_prefix() . 'items.PlantID');
		$this->db->where(db_prefix() . 'history.TransID', $TransId);
		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);
		$this->db->where(db_prefix() . 'history.FY', $year);
		$this->db->where(db_prefix() . 'history.TType', "O");
		$this->db->where(db_prefix() . 'history.NetChallanAmt !=', "0.00");
		$this->db->where(db_prefix() . 'history.TType2', "Order");
		return $this->db->get()->result_array();
	}
	
	public function GetSchemeData($dist_type,$state,$date,$itemid)
	{
		// $distTypeStr = implode(',', $dist_type);
		// echo $distTypeStr;die;
		$date = to_sql_date($date);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$sql = 'SELECT * 
		FROM `tblschemedetails`
		INNER JOIN tblschememaster ON tblschememaster.SchemeID = tblschemedetails.SchemeID
		WHERE ("'.$date.' 00:00:00" BETWEEN tblschememaster.StartDate AND tblschememaster.EndDate) AND tblschemedetails.ItemID = "'.$itemid.'" AND  FIND_IN_SET("'.$dist_type.'", tblschemedetails.DistributorType) AND tblschemedetails.StateID = "'.$state.'" AND tblschemedetails.Disc_type="free_distribution" AND tblschememaster.Approve ="Y" AND tblschemedetails.ActYN = "Y" AND tblschememaster.SchemeType = "Single" Order by tblschemedetails.SlabQty DESC';
		return $this->db->query($sql)->result_array();
		
	}
	public function GetDiscSchemeData($dist_type,$state,$date,$itemid)
	{
		// $distTypeStr = implode(',', $dist_type);
		// echo $distTypeStr;die;
		$date = to_sql_date($date);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		$sql = 'SELECT * 
		FROM `tblschemedetails`
		INNER JOIN tblschememaster ON tblschememaster.SchemeID = tblschemedetails.SchemeID
		WHERE ("'.$date.' 00:00:00" BETWEEN tblschememaster.StartDate AND tblschememaster.EndDate) AND tblschemedetails.ItemID = "'.$itemid.'" AND  FIND_IN_SET("'.$dist_type.'", tblschemedetails.DistributorType) AND tblschemedetails.StateID = "'.$state.'" AND tblschemedetails.Disc_type="disc" AND tblschememaster.Approve ="Y" AND tblschemedetails.ActYN = "Y" AND tblschememaster.SchemeType = "Single" Order by tblschemedetails.SlabQty DESC';
		return $this->db->query($sql)->result_array();
		
	}
	
	
	public function GetMultipleSchemeData($dist_type, $state, $date, $itemids_array)
	{
		$date = to_sql_date($date);
		$fy = $this->session->userdata('finacial_year');
		$selected_company = $this->session->userdata('root_company');
		
		// Build FIND_IN_SET conditions for each item
		$findSet = array_map(function($item) {
			return 'FIND_IN_SET("' . $item . '", tblschemedetails.ItemID)';
		}, $itemids_array);
		
		$findSetStr = implode(' OR ', $findSet);
		
		// Build final SQL query
		$sql = 'SELECT * 
		FROM `tblschemedetails`
		INNER JOIN tblschememaster 
		ON tblschememaster.SchemeID = tblschemedetails.SchemeID
		WHERE ("' . $date . ' 00:00:00" BETWEEN tblschememaster.StartDate AND tblschememaster.EndDate)
		AND (' . $findSetStr . ')
		AND FIND_IN_SET("' . $dist_type . '", tblschemedetails.DistributorType)
		AND tblschemedetails.StateID = "' . $state . '"
		AND tblschemedetails.Disc_type = "free_distribution"
		AND tblschememaster.Approve = "Y"
		AND tblschemedetails.ActYN = "Y"
		AND tblschememaster.SchemeType = "Multiple"
		ORDER BY tblschemedetails.SlabQty DESC';
		
		return $this->db->query($sql)->result_array();
	}
	
	//*********************  Shortage Master Qty  ***********************	 
	
	public function get_challan_details_by_id($challan_number)
	{     
		$this->db->select(        
		'tcm.VehicleID, ' . 
		'tcm.DriverID, ' .
		'tcm.RouteID, ' .        
		'tv.VehicleID AS VehicleNo, ' .
		'CONCAT(ts.firstname, " ", ts.lastname) AS DriverName, ' .
		'tr.name AS RouteName',
		false 
		);
		
		$this->db->from(db_prefix() . 'challanmaster tcm'); 
		
		// Join Vehicle Table using VehicleID
		$this->db->join(db_prefix() . 'vehicle tv', 'tv.VehicleID = tcm.VehicleID', 'left');
		
		// Join Staff Table for Driver Name
		$this->db->join(db_prefix() . 'staff ts', 'ts.AccountID = tcm.DriverID', 'left');
		
		// Join Route Table
		$this->db->join(db_prefix() . 'route tr', 'tr.RouteID = tcm.RouteID', 'left');
		
		$this->db->where('tcm.ChallanID', $challan_number); 
		
		$challan = $this->db->get()->row();
		
		if ($challan) {
			return [
			// IDs for setting the main form's selectpickers
			'vehicle_id'   => $challan->VehicleID,
			'driver_id'    => $challan->DriverID,
			'route_id'     => $challan->RouteID,
			
			// Names  
			'vehicle_no'   => $challan->VehicleNo,
			'driver_name'  => $challan->DriverName,
			'route_name'   => $challan->RouteName
			];
		}
		
		return false;
	}
	
	
	//*******************************************************************************
	public function getChallanOrderTableDetails($ChallanID)
	{
		$selected_company = $this->session->userdata('root_company');
		$fy = $this->session->userdata('finacial_year');
		
		$prefix = db_prefix();  
		
		$this->db->select(
		$prefix . 'ordermaster.OrderID, ' .
		$prefix . 'clients.company AS AccountName, ' .
		$prefix . 'clients.state AS StateID, ' .
		$prefix . 'salesmaster.BT AS OrderType, ' .
		$prefix . 'salesmaster.SalesID, ' .
		$prefix . 'salesmaster.DeliveryPoint, ' .
		$prefix . 'salesmaster.Transdate AS SalesDate, ' .
		$prefix . 'route.name AS RouteName'
		);
		
		$this->db->from($prefix . 'ordermaster');
		
		$this->db->join($prefix . 'salesmaster', $prefix . 'salesmaster.SalesID = ' . $prefix . 'ordermaster.SalesID', 'left');
		$this->db->join($prefix . 'clients', $prefix . 'clients.AccountID = ' . $prefix . 'ordermaster.AccountID AND ' . $prefix . 'clients.PlantID = ' . $prefix . 'ordermaster.PlantID', 'left');
		$this->db->join($prefix . 'accountroutes', $prefix . 'accountroutes.AccountID = ' . $prefix . 'ordermaster.AccountID', 'left');
		$this->db->join($prefix . 'route', $prefix . 'route.RouteID = ' . $prefix . 'accountroutes.RouteID', 'left');
		
		$this->db->where($prefix . 'ordermaster.ChallanID', $ChallanID);
		$this->db->where($prefix . 'ordermaster.PlantID', $selected_company);
		$this->db->where($prefix . 'ordermaster.FY', $fy);
		$this->db->where_in($prefix . 'salesmaster.BT', ['B', 'T']);
		$this->db->order_by($prefix . 'ordermaster.OrderID', 'DESC');
		
		// $query = $this->db->get();
		// echo $this->db->last_query();  
		// return $query->result_array();
		
		return $this->db->get()->result_array();
		
	}
	
	//*********************** 
	
	public function getChallanItemQuantities($ChallanID)
	{
		$prefix = db_prefix();
		
		$this->db->select('OrderID, ItemID, BilledQty');
		$this->db->from($prefix . 'history');
		$this->db->where('BillID', $ChallanID);
		
		$result = $this->db->get()->result_array();
		
		// Organize data by OrderID and ItemID for easy access
		$organized_data = [];
		foreach ($result as $row) {
			$organized_data[$row['OrderID']][$row['ItemID']] = $row['BilledQty'];
		}
		
		return $organized_data;
	}	  
	
	// Get all shortage master records with details
	public function get_shortage_master_list($from_date = null, $to_date = null)
	{
		$prefix = db_prefix();
		
		$this->db->select('
		sm.ShortageID,
		sm.TrasDate,
		sm.ChallanID,
		sm.UserID,
		sm.Lupdate,
		sd.OrderID,
		sd.SaleID,
		sd.ItemID,
		sd.BilledQty,
		sd.ShortageQty,
		c.company AS PartyName,
		i.description AS ItemName,
		cm.DriverID,
		cm.VehicleID,
		CONCAT(st.firstname, " ", st.lastname) AS DriverName,
		v.VehicleID AS VehicleNo,
		h.AccountID
		');
		
		$this->db->from($prefix . 'ShortageMaster sm');
		$this->db->join($prefix . 'ShortageDetails sd', 'sm.ShortageID = sd.ShortageID', 'left');
		$this->db->join($prefix . 'history h', 'h.BillID = sm.ChallanID AND h.OrderID = sd.OrderID AND h.ItemID = sd.ItemID', 'left');
		$this->db->join($prefix . 'clients c', 'c.AccountID = h.AccountID', 'left');
		$this->db->join($prefix . 'challanmaster cm', 'cm.ChallanID = sm.ChallanID', 'left');
		$this->db->join($prefix . 'staff st', 'st.AccountID = cm.DriverID', 'left');
		$this->db->join($prefix . 'vehicle v', 'v.VehicleID = cm.VehicleID', 'left');
		$this->db->join($prefix . 'items i', 'i.item_code = sd.ItemID', 'left');
		
		// Apply date filters
		if ($from_date) {
			$from_date_mysql = date('Y-m-d', strtotime(str_replace('/', '-', $from_date)));
			$this->db->where('DATE(sm.TrasDate) >=', $from_date_mysql);
		}
		
		if ($to_date) {
			$to_date_mysql = date('Y-m-d', strtotime(str_replace('/', '-', $to_date)));
			$this->db->where('DATE(sm.TrasDate) <=', $to_date_mysql);
		}
		
		$this->db->order_by('sm.Lupdate', 'DESC');
		$this->db->order_by('sm.ShortageID', 'DESC');
		$this->db->order_by('sd.OrderID', 'ASC');
		
		return $this->db->get()->result();
	}
	
	
	
	
	// Get shortage master by ID
	public function get_shortage_master_by_id($shortage_id)
	{
		$prefix = db_prefix();
		
		$this->db->select('*');
		$this->db->from($prefix . 'ShortageMaster');
		$this->db->where('ShortageID', $shortage_id);
		
		return $this->db->get()->row();
	}
	
	// Get shortage details by ShortageID
	public function get_shortage_details_by_id($shortage_id)
	{
		$prefix = db_prefix();
		
		$this->db->select('*');
		$this->db->from($prefix . 'ShortageDetails');
		$this->db->where('ShortageID', $shortage_id);
		
		$result = $this->db->get()->result_array();
		
		// Organize by OrderID and ItemID for easy access
		$organized_data = [];
		foreach ($result as $row) {
			$organized_data[$row['OrderID']][$row['ItemID']] = $row;
		}
		
		return $organized_data;
	} 
	
	public function get_detailed_shortage_list($from_date = null, $to_date = null)
	{
		$prefix = db_prefix();
		
		$this->db->select("
		sm.ShortageID,
		sm.TrasDate AS 'ShortDate',
		sm.ChallanID,
		sd.OrderID,
		sd.SaleID,
		sd.ItemID,
		i.description AS 'ItemName',
		sd.BilledQty AS 'BillQty',
		sd.ShortageQty,
		c.company AS 'PartyName',
		v.VehicleID AS 'VehicleNo',
		CONCAT(st_driver.firstname, ' ', st_driver.lastname) AS 'DriverName',
		CONCAT(st_user.firstname, ' ', st_user.lastname) AS 'CreatedBy'
		", false);
		
		$this->db->from($prefix . 'ShortageDetails sd');
		$this->db->join($prefix . 'ShortageMaster sm', 'sm.ShortageID = sd.ShortageID', 'left');
		$this->db->join($prefix . 'history h', 'h.BillID = sm.ChallanID AND h.OrderID = sd.OrderID AND h.ItemID = sd.ItemID', 'left');
		$this->db->join($prefix . 'clients c', 'c.AccountID = h.AccountID', 'left');
		$this->db->join($prefix . 'challanmaster cm', 'cm.ChallanID = sm.ChallanID', 'left');
		$this->db->join($prefix . 'staff st_driver', 'st_driver.AccountID = cm.DriverID', 'left'); // Alias for driver
		$this->db->join($prefix . 'vehicle v', 'v.VehicleID = cm.VehicleID', 'left');
		$this->db->join($prefix . 'items i', 'i.item_code = sd.ItemID', 'left');
		$this->db->join($prefix . 'staff st_user', 'st_user.AccountID = sm.UserID', 'left'); // Alias for user/created by
		
		// Apply date filters
		if ($from_date) {
			$from_date_mysql = date('Y-m-d', strtotime(str_replace('/', '-', $from_date)));
			$this->db->where('DATE(sm.TrasDate) >=', $from_date_mysql);
		}
		
		if ($to_date) {
			$to_date_mysql = date('Y-m-d', strtotime(str_replace('/', '-', $to_date)));
			$this->db->where('DATE(sm.TrasDate) <=', $to_date_mysql);
		}
		
		// **CRUCIAL: Order by the grouping column first**
		$this->db->order_by('sm.ShortageID', 'DESC');
		$this->db->order_by('sd.OrderID', 'ASC');
		
		return $this->db->get()->result();
	}
	
	
	
	
	
	
	public function check_shortage_exists()
	{
		$challan_number = $this->input->post('ChallanNo');
		
		if (empty($challan_number)) {
			echo json_encode(['exists' => false]);
			return;
		}
		
		$this->db->where('ChallanID', $challan_number);
		$query = $this->db->get('tblShortageMaster');
		
		$exists = $query->num_rows() > 0;
		
		echo json_encode(['exists' => $exists]);
	} 
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
} 

