<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Accounts_master_model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		//====================== VERYFIED CODE =========================================
		
		public function get_company_detail()
		{   
			$selected_company = $this->session->userdata('root_company');
			$sql ='SELECT '.db_prefix().'rootcompany.*
			FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		// Get Account Movement
		public function GetActMovement()
		{
			$ActMovement = $this->db->get(db_prefix() . 'actgroupmovement')->result_array();
			return $ActMovement;
		}
		
		public function GetActMainGroupData()
		{
			$this->db->order_by(db_prefix() . 'AccountMainGroup.ActGroupID', 'ASC');
			return $this->db->get(db_prefix().'AccountMainGroup')->result_array();
		}
		
		// GET MAIN ACCOUNT GROUP
		public function GetMainGroup()
		{
			$this->db->order_by(db_prefix() . 'AccountMainGroup.ActGroupID','ASC');
			$account_maingroup = $this->db->get(db_prefix() . 'AccountMainGroup')->result_array();
			return $account_maingroup;
		}
		
		public function get_account_Maingroup_details($AccountID)
		{  
			$sql ='SELECT '.db_prefix().'AccountMainGroup.*
			FROM '.db_prefix().'AccountMainGroup WHERE ActGroupID = '.$AccountID;
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		function GetMainAccountList($postData)
		{
			$response = array();
			$where_ = '';
			if(isset($postData['search']) ){
				$q = $postData['search'];
				$this->db->select(db_prefix() . 'accountgroups.*');
				$where_ .= '(ActGroupName LIKE "%' . $q . '%" ESCAPE \'!\' OR CtrlActGroupID LIKE "%' . $q . '%" ESCAPE \'!\' OR ActGroupID LIKE "%' . $q. '%" ESCAPE \'!\')';
				$this->db->where($where_);
				
				$records = $this->db->get(db_prefix() . 'accountgroups')->result();
				foreach($records as $row ){
					$response[] = array("label"=>$row->ActGroupName,"value"=>$row->ActGroupID);
				}
			}
			return $response;
		}
		
		// Add New Account Main Group
		public function SaveMainGroup($data)
		{
			$this->db->insert(db_prefix() . 'AccountMainGroup', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		public function get_login_user_list()
		{   
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'staff.*');
			$this->db->where(db_prefix() . 'staff.login_access', "Yes");
			$this->db->where(db_prefix() . 'staff.active', 1);
			$this->db->where(db_prefix() . 'staff.admin', 0);
			$this->db->order_by(db_prefix() . 'staff.firstname', 'ASC');
			$result = $this->db->get(db_prefix() . 'staff')->result_array();
			return $result;
		}
		function get_user_list($postData){
			
			$response = array();
			$where_ = '';
			$selected_company = $this->session->userdata('root_company');
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				$this->db->select(db_prefix() . 'staff.*');
				$where_ .= '(AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR 	lastname LIKE "%' . $q. '%" ESCAPE \'!\')';
				$this->db->where($where_);
				$records = $this->db->get(db_prefix() . 'staff')->result();
				
				foreach($records as $row ){
					$full_name = $row->firstname." ".$row->lastname;
					$response[] = array("label"=>$full_name,"value"=>$row->AccountID);
				}
			}
			return $response;
		}
		
		// Update Exiting Account Main Group
		public function UpdateMainGroup($data,$ActGroupID)
		{
			$this->db->where('ActGroupID', $ActGroupID);
			$this->db->update(db_prefix() . 'AccountMainGroup', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		// GET ACCOUNT SUBGROUP List 1
		public function GetAccountSubGroupID1_List($main_group = '')
		{
			$this->db->select(db_prefix() . 'AccountSubGroup1.*,tblAccountMainGroup.ActGroupName');
			$this->db->join(db_prefix() . 'AccountMainGroup', '' . db_prefix() . 'AccountMainGroup.ActGroupID = ' . db_prefix() . 'AccountSubGroup1.ActGroupID');
			if($main_group){
			    $this->db->where(db_prefix() . 'AccountSubGroup1.ActGroupID',$main_group);
			}
			$this->db->order_by(db_prefix() . 'AccountSubGroup1.SubActGroupID1','ASC');
			$AccountSubGroupID1 = $this->db->get(db_prefix() . 'AccountSubGroup1')->result_array();
			return $AccountSubGroupID1;
		}
		
		// GET ACCOUNT SUBGROUP List 2
		public function GetAccountSubGroupID2_List($main_group = '',$subgroup1 = '')
		{
			$this->db->select(db_prefix() . 'accountgroupssub2.*,tblaccountgroups.ActGroupName,tblAccountSubGroup1.SubActGroupName AS SubActGroupName1');
			$this->db->join(db_prefix() . 'AccountSubGroup1', '' . db_prefix() . 'AccountSubGroup1.SubActGroupID1 = ' . db_prefix() . 'accountgroupssub2.SubActGroupID1');
			$this->db->join(db_prefix() . 'accountgroups', '' . db_prefix() . 'accountgroups.ActGroupID = ' . db_prefix() . 'AccountSubGroup1.ActGroupID');
			if($main_group){
			    $this->db->where(db_prefix() . 'AccountSubGroup1.ActGroupID',$main_group);
			}
			if($subgroup1){
			    $this->db->where(db_prefix() . 'AccountSubGroup1.SubActGroupID1',$subgroup1);
			}
			$this->db->order_by(db_prefix() . 'accountgroupssub2.SubActGroupID2','ASC');
			$AccountSubGroupID1 = $this->db->get(db_prefix() . 'accountgroupssub2')->result_array();
			return $AccountSubGroupID1;
		}
		
		// GET ACCOUNT SUBGROUP1
		public function GetAccountSubGroupID1($InitialMainGroup)
		{
			$this->db->select(db_prefix() . 'AccountSubGroup1.*');
			
			$this->db->where(db_prefix() . 'AccountSubGroup1.ActGroupID',$InitialMainGroup);
			$this->db->order_by(db_prefix() . 'AccountSubGroup1.SubActGroupID1','ASC');
			$AccountSubGroupID1 = $this->db->get(db_prefix() . 'AccountSubGroup1')->result_array();
			return $AccountSubGroupID1;
		}
		public function get_actsubgroup_data()
        {
            $this->db->order_by(db_prefix() . 'accountgroupssub.SubActGroupName', 'ASC');
			return $this->db->get(db_prefix().'accountgroupssub')->result_array();
		}
		
		public function get_act_maingroup(){
			
			$this->db->order_by(db_prefix() . 'accountgroups.ActGroupName','ASC');
			$account_maingroup = $this->db->get(db_prefix() . 'accountgroups')->result_array();
			return $account_maingroup;
		}
		public function get_accounts_list($data = '')
		{
			$selected_company = $this->session->userdata('root_company');
			$MainGroup = $data['MainGroup'];
			$SubGroup1 = $data['SubGroup1'];
			$SubGroup2 = $data['SubGroup2'];
			$TType = $data['TType'];
			$status = $data['status'];
			if($status == "Y"){
				$status = "1"; 
				}else if($status == "N"){
			    $status = "0"; 
			}
			$sql = 'SELECT tblclients.*,tblaccountgroupssub.SubActGroupName AS SubActGroupName2,tblaccountgroups.ActGroupName As MainGroupName,tblAccountSubGroup1.SubActGroupName AS SubActGroupName1 FROM tblclients 
			LEFT JOIN tblaccountgroups ON tblaccountgroups.ActGroupID = tblclients.ActGroupID
			LEFT JOIN tblAccountSubGroup1 ON tblAccountSubGroup1.SubActGroupID1 = tblclients.SubActGroupID1
			LEFT JOIN tblaccountgroupssub ON tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID
			WHERE PlantID ='.$selected_company;
			if($MainGroup){
			    $sql .= ' AND tblclients.ActGroupID = "'.$MainGroup.'"';  
				}else{
			    $sql .= ' AND tblclients.SubActGroupID NOT IN("1000016","1000006")';
			}
			if($SubGroup1){
			    $sql .= ' AND tblclients.SubActGroupID1 = "'.$SubGroup1.'"';  
			}
			if($SubGroup2){
			    $sql .= ' AND tblclients.SubActGroupID = "'.$SubGroup2.'"';  
			}
			if($status != ""){
			    $sql .= ' AND tblclients.active = "'.$status.'"';  
			}
			if($TType){
				// $sql .= ' AND tblclients.TType = "'.$TType.'"';  
			}
			$sql .= ' ORDER BY tblclients.company ASC';
			
			$result_data = $this->db->query($sql)->result_array();
			return $result_data;
		}
		// All Ledger Except Trade Payable and Trade Receivable 
		public function GetAccountLedger($data = '')
		{
			$selected_company = $this->session->userdata('root_company');
			$Payable_receivable = array("1000016","1000006");
			$sql = 'SELECT tblclients.*,tblaccountgroupssub.SubActGroupName AS SubActGroupName2,tblaccountgroups.ActGroupName As MainGroupName,tblAccountSubGroup1.SubActGroupName AS SubActGroupName1 FROM tblclients 
			LEFT JOIN tblaccountgroups ON tblaccountgroups.ActGroupID = tblclients.ActGroupID
			LEFT JOIN tblAccountSubGroup1 ON tblAccountSubGroup1.SubActGroupID1 = tblclients.SubActGroupID1
			LEFT JOIN tblaccountgroupssub ON tblaccountgroupssub.SubActGroupID = tblclients.SubActGroupID
			WHERE PlantID ='.$selected_company;
			$sql .= ' AND tblclients.SubActGroupID NOT IN("1000016")';
			
			$sql .= ' ORDER BY tblclients.company ASC';
			
			$result_data = $this->db->query($sql)->result_array();
			return $result_data;
		}
		
		// Add New Ledger Accounts
		public function SaveAccountID($data,$BAL1,$AccountID)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			
			$data['PlantID'] = $selected_company;
			$data['addedfrom'] = $UserID;
			$data['datecreated'] = date('Y-m-d H:i:s');
			
			$this->db->insert(db_prefix() . 'clients', $data);
			$INSERT = $this->db->affected_rows();
            if($INSERT > 0){
				$next_number = (int) get_option('next_account_ledger_number');
				// Update next number in settings
				$next_number = $next_number+1;
				$this->db->where('name', 'next_account_ledger_number');
				$this->db->update(db_prefix() . 'options',['value' =>  $next_number,]);
				
                $dataContacts = array();
                $dataContacts['PlantID'] = $selected_company;
                $dataContacts['AccountID'] = $AccountID;
                $this->db->insert(db_prefix() . 'contacts', $dataContacts);
				$Bal_data = array(
				"BAL1"=>$BAL1,
				"AccountID"=>$AccountID,
				"PlantID"=>$selected_company,
				"FY"=>$FY
				);
				$this->db->insert(db_prefix() . 'accountbalances', $Bal_data);
			}   
			if($INSERT > 0){
				return true;
				}else{
				return false;
			}
		}
		
		// Update Exiting ItemID
		public function UpdateAccountID($dataClient,$AccountID,$BAL1)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$UPDATE = 0; 
			$this->db->where('AccountID', $AccountID);
			$this->db->where('PlantID', $selected_company);
			$this->db->update(db_prefix() . 'clients', $dataClient);
			//$UPDATE = $UPDATE + $this->db->affected_rows();
			if($this->db->affected_rows() > 0){
				$UPDATE++;
			}
			$staff_user_id = $this->session->userdata('staff_user_id');
            $checkBalRecord = $this->ChkBalRecord($AccountID,$selected_company,$FY);
			if(empty($checkBalRecord)){
				//Balance Record Create
				$Bal_data = array(
				"BAL1"=>$BAL1,
				"PlantID"=>$selected_company,
				"FY"=>$FY,
				"AccountID"=>$AccountID
				);
				$this->db->insert(db_prefix() . 'accountbalances', $Bal_data);
				$UPDATE++;
			}else{
				//Balance Record Update
				$Bal_data = array(
				"BAL1"=>$BAL1,
				"UserID2"=>$UserID,
				"Lupdate"=>date('Y-m-d H:i:s')
				);
				$this->db->where('AccountID', $AccountID);
				$this->db->where('PlantID', $selected_company);
				$this->db->where('FY', $FY);
				$this->db->update(db_prefix() . 'accountbalances', $Bal_data);
				if($this->db->affected_rows() > 0){
					$UPDATE++;
				}
			}
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		public function GetAccountDetails($id = '')
		{                          
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			
			$this->db->select('tblclients.AccountID,tblclients.company,tblclients.PlantID,tblclients.StartDate,tblclients.Blockyn,tblclients.ActGroupID,tblclients.SubActGroupID1,
			tblclients.SubActGroupID,tblclients.ifsc_code,tblclients.bank_name,tblclients.bank_add,tblclients.acc_name,tblclients.TType,tblclients.acc_no,
			tblclients.acc_type,tblclients.ad_code,'.db_prefix() . 'clients.closing_bal,'.db_prefix() . 'clients.payment_term,'.db_prefix() . 'clients.hsn_code,'.db_prefix() . 'clients.tax,'.db_prefix() . 'accountbalances.BAL1,'.db_prefix() . 'contacts.BalancesYN');
			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID','LEFT');
			$this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY = "'.$FY.'"','LEFT');
			$this->db->from(db_prefix() . 'clients');
			if ($id) {
				$this->db->where(db_prefix() . 'clients.AccountID', $id);
				$Data = $this->db->get()->row();
				if($Data){
					$Data->SubGroupData1 = $this->db->query("select * from tblAccountSubGroup1 where ActGroupID = '".$Data->ActGroupID."'")->result();
					$Data->SubGroupData2 = $this->db->query("select * from tblaccountgroupssub where SubActGroupID1 = '".$Data->SubActGroupID1."'")->result();
				}
				return $Data;
			}
		}
		
		public function ChkBalRecord($AccountID,$PlantID,$fy)
		{
			$this->db->select(db_prefix() . 'accountbalances.*');
			$this->db->where('AccountID', $AccountID);
			$this->db->where('PlantID', $PlantID);
			$this->db->where('FY', $fy);
			$this->db->from(db_prefix() . 'accountbalances');
			$data =  $this->db->get()->row();
			return $data;
		}
		/*============================ Below this mixed code =============================*/
		
		
		// GET MAIN ACCOUNT GROUP
		public function GetSubGroupList()
		{
			$this->db->order_by(db_prefix() . 'AccountSubGroup1.ActGroupID','ASC');
			$account_maingroup = $this->db->get(db_prefix() . 'AccountSubGroup1')->result_array();
			return $account_maingroup;
		}
		// Get Next Main Group ID
		public function GetNextAccountMainGroupID()
		{  
			$sql ='SELECT '.db_prefix().'AccountMainGroup.ActGroupID
			FROM '.db_prefix().'AccountMainGroup ORDER BY ActGroupID DESC LIMIT 1';
			$result = $this->db->query($sql)->row();
			return $result->ActGroupID+1;
			
		}
		
		
		
		
		// GET ACCOUNT SUBGROUP2
		public function GetAccountSubGroupID2($InitialAccountSubGroupID1)
		{
			$this->db->select(db_prefix() . 'accountgroupssub2.*');
			$this->db->where(db_prefix() . 'accountgroupssub2.SubActGroupID1',$InitialAccountSubGroupID1);
			$this->db->order_by(db_prefix() . 'accountgroupssub2.SubActGroupID2','ASC');
			$AccountSubGroupID2 = $this->db->get(db_prefix() . 'accountgroupssub2')->result_array();
			return $AccountSubGroupID2;
		}
		// GET ACCOUNT SUBGROUP3
		public function GetAccountSubGroupID3($InitialAccountSubGroupID1)
		{
			
			$sql = "SELECT `tblaccountgroupssub2`.*, `tblAccountSubGroup1`.`SubActGroupID1` as `SubActID1`, `tblAccountSubGroup1`.`SubActGroupName` as `SubActGroupName1`, `tblaccountgroups`.`ActGroupID` as `ActGroupIDMain`, `tblaccountgroups`.`ActGroupName` as `ActGroupNameMain`
			FROM `tblaccountgroupssub2`
			LEFT JOIN `tblAccountSubGroup1` ON `tblAccountSubGroup1`.`SubActGroupID1` = `tblaccountgroupssub2`.`SubActGroupID1`
			LEFT JOIN `tblaccountgroups` ON `tblaccountgroups`.`ActGroupID` = `tblAccountSubGroup1`.`ActGroupID`
			WHERE `tblaccountgroupssub2`.`SubActGroupID2` = '".$InitialAccountSubGroupID1."'
			ORDER BY `tblaccountgroupssub2`.`SubActGroupID2` ASC";
			$data = $this->db->query($sql);
			return $data->result_array();
			
		}
		
		// GET NEXT ACCOUNTSUBGROUPID3
		
		public function GetNextAccountSunGroupID3()
		{
			$this->db->select(db_prefix() . 'accountgroupssub.SubActGroupID');
			$this->db->order_by(db_prefix() . 'accountgroupssub.SubActGroupID', 'DESC');
			$row = $this->db->get(db_prefix() . 'accountgroupssub')->row();
			return $row;
		}
		
		// GET ALL ACCOUNTSUBGROUPID2 DATA
		public function GetAllAccountSubGroupID2()
		{
			// $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupID', 'ASC');
			// return $this->db->get(db_prefix().'AccountSubGroup2')->result_array();

			$this->db->select(
        db_prefix().'AccountSubGroup2.*,'.
        db_prefix().'AccountSubGroup1.SubActGroupID1,'.
        db_prefix().'AccountSubGroup1.SubActGroupName AS SubActGroupName1,'.
        db_prefix().'AccountMainGroup.ActGroupID,'.
        db_prefix().'AccountMainGroup.ActGroupName AS ActGroupNameMain'
    );

    $this->db->from(db_prefix().'AccountSubGroup2');

    $this->db->join(
        db_prefix().'AccountSubGroup1',
        db_prefix().'AccountSubGroup1.SubActGroupID1 = '.db_prefix().'AccountSubGroup2.SubActGroupID1',
        'inner'
    );

    $this->db->join(
        db_prefix().'AccountMainGroup',
        db_prefix().'AccountMainGroup.ActGroupID = '.db_prefix().'AccountSubGroup1.ActGroupID',
        'inner'
    );

			return $this->db->get()->result_array();


		}
		// GET ALL ACCOUNTSUBGROUPID2 DATA
		public function GetAllAccountSubGroupID2_List($main_group = '',$subgroup1 = '')
		{
			if (!empty($main_group)) {
				$sqlcon .= " WHERE tblaccountgroups.ActGroupID = '".$main_group."'";
			}
			
			// Check if $subgroup1 is set and not empty
			if (!empty($subgroup1)) {
				// If $sqlcon is already populated with a condition, add AND
				if (!empty($sqlcon)) {
					$sqlcon .= " AND tblaccountgroupssub.SubActGroupID1 = '".$subgroup1."'";
					} else {
					$sqlcon .= " WHERE tblaccountgroupssub.SubActGroupID1 = '".$subgroup1."'";
				}
			}
			$sql ='SELECT '.db_prefix().'accountgroupssub.*,tblAccountSubGroup1.SubActGroupName AS SubGroup1,tblaccountgroups.ActGroupName AS MainGroup
			FROM '.db_prefix().'accountgroupssub
			LEFT JOIN tblAccountSubGroup1 ON tblAccountSubGroup1.SubActGroupID1 = tblaccountgroupssub.SubActGroupID1
			LEFT JOIN tblaccountgroups ON tblaccountgroups.ActGroupID = tblAccountSubGroup1.ActGroupID
			'.$sqlcon.' ORDER BY tblaccountgroupssub.SubActGroupID ASC';
			return $result = $this->db->query($sql)->result_array();

		}
		// GET ALL ACCOUNTSUBGROUPID3 DATA
		
		public function GetAllAccountSubGroupID()
		{
			$this->db->order_by(db_prefix() . 'accountgroupssub.SubActGroupName', 'ASC');
			return $this->db->get(db_prefix().'accountgroupssub')->result_array();
		}
		
		// Get Next AccountSubGroupID2
		
		public function GetNextAccountSunGroupID2()
		{  
			$sql ='SELECT '.db_prefix().'AccountSubGroup2.SubActGroupID
			FROM '.db_prefix().'AccountSubGroup2 ORDER BY SubActGroupID DESC LIMIT 1';
			$result = $this->db->query($sql)->row();
			return $result->SubActGroupID+1;
			
		}
		// Get Next AccountSubGroupID3 
		
		public function GetNextAccountSunGroupID33()
		{  
			$sql ='SELECT '.db_prefix().'accountgroupssub.SubActGroupID
			FROM '.db_prefix().'accountgroupssub ORDER BY SubActGroupID DESC LIMIT 1';
			$result = $this->db->query($sql)->row();
			return $result->SubActGroupID+1;
			
		}
		public function GetNextAccountSunGroupID1()
		{  
			$sql ='SELECT '.db_prefix().'AccountSubGroup1.SubActGroupID1
			FROM '.db_prefix().'AccountSubGroup1 ORDER BY SubActGroupID1 DESC LIMIT 1';
			$result = $this->db->query($sql)->row();
			return $result->SubActGroupID1+1;
			
		}
		
		//======================= END VERYFIED CODE ====================================
		
		public function get($id = '')
		{
			$this->db->select(db_prefix() . 'clients.AccountID,'.db_prefix() . 'clients.company,'.db_prefix() . 'clients.StartDate,'.db_prefix() . 'clients.Blockyn,'.db_prefix() . 'clients.SubActGroupID,'.db_prefix() . 'accountbalances.BAL1,'.db_prefix() . 'contacts.BalancesYN');
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID');
			$this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY = "'.$FY.'"','LEFT');
			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
			$this->db->from(db_prefix() . 'clients');
			if ($id) {
				$this->db->where(db_prefix() . 'clients.AccountID', $id);
				
				return $this->db->get()->row();
			}
			return $this->db->get()->result_array();
		}
		
		public function get_accoun_main_group(){
			
			$acc_main_group = $this->db->get(db_prefix() . 'accountgroups')->result_array();
			return $acc_main_group;
		}
		
		function get_accounts_subgroup2($postData)
		{
			$response = array();
			$where_ = '';
			if(isset($postData['search']) ){
				$q = $postData['search'];
				$this->db->select(db_prefix() . 'accountgroupssub.*');
				$where_ .= '(SubActGroupID LIKE "%' . $q . '%" ESCAPE \'!\' OR SubActGroupName LIKE "%' . $q . '%" ESCAPE \'!\')';
				$this->db->where($where_);
				$records = $this->db->get(db_prefix() . 'accountgroupssub')->result();
				foreach($records as $row ){
					$response[] = array("label"=>$row->SubActGroupName,"value"=>$row->SubActGroupID);
				}
			}
			return $response;
		}
		function get_accounts_subgroup($postData){
			
			$response = array();
			
			$where_ = '';
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				
				$this->db->select(db_prefix() . 'accountgroupssub.*');
				$where_ .= '(SubActGroupID LIKE "%' . $q . '%" ESCAPE \'!\' OR SubActGroupName LIKE "%' . $q . '%" ESCAPE \'!\')';
				$this->db->where($where_);
				
				$records = $this->db->get(db_prefix() . 'accountgroupssub')->result();
				//   echo $this->db->last_query();die;
				
				foreach($records as $row ){
					$response[] = array("label"=>$row->SubActGroupName,"value"=>$row->SubActGroupID);
				}
				
			}
			
			return $response;
		}
		
		function get_accounts_subgroup1($postData){
			
			$response = array();
			
			$where_ = '';
			if(isset($postData['search']) ){
				
				$q = $postData['search'];
				
				$this->db->select(db_prefix() . 'AccountSubGroup1.*');
				$where_ .= '(SubActGroupID1 LIKE "%' . $q . '%" ESCAPE \'!\' OR SubActGroupName LIKE "%' . $q . '%" ESCAPE \'!\')';
				$this->db->where($where_);
				
				$records = $this->db->get(db_prefix() . 'AccountSubGroup1')->result();
				//   echo $this->db->last_query();die;
				
				foreach($records as $row ){
					$response[] = array("label"=>$row->SubActGroupName,"value"=>$row->SubActGroupID1);
				}
				
			}
			
			return $response;
		}
		
		
		
		// Add New AccountSubGroup
		public function SaveSubGroup($data)
		{
			$this->db->insert(db_prefix() . 'accountgroupssub', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		// Update Exiting Account SubGroup
		public function UpdateSubGroup($data,$SubGroupID)
		{
			$this->db->where('SubActGroupID', $SubGroupID);
			$this->db->update(db_prefix() . 'accountgroupssub', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		// Add New AccountSubGroup
		public function SaveSubGroup2($data)
		{
			$this->db->insert(db_prefix() . 'AccountSubGroup2', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		// Update Exiting Account SubGroup
		public function UpdateSubGroup2($data,$SubGroupID)
		{
			$this->db->where('SubActGroupID', $SubGroupID);
			$this->db->update(db_prefix() . 'AccountSubGroup2', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		// Add New AccountSubGroup1
		public function SaveSubGroup1($data)
		{
			$this->db->insert(db_prefix() . 'AccountSubGroup1', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		// Update Exiting Account SubGroup
		public function UpdateSubGroup1($data,$SubGroupID)
		{
			$this->db->where('SubActGroupID1', $SubGroupID);
			$this->db->update(db_prefix() . 'AccountSubGroup1', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
		}
	}
	public function get_no_act_list($user_id)
	{
		$selected_company = $this->session->userdata('root_company');

		// Fetch clients that are inactive (IsActive = 'N') for the selected company
		$sql = '
        SELECT * 
        FROM ' . db_prefix() . 'clients 
        WHERE IsActive = "N"
          AND PlantID = ' . $selected_company;

		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	public function get_no_act_list_for_staff($user_id)
	{
		$selected_company = $this->session->userdata('root_company');
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'staff.*');
			$this->db->where('no_show',"1");
			$this->db->order_by(db_prefix() . 'staff.firstname', 'ASC');
			$result = $this->db->get(db_prefix() . 'staff')->result_array();
			return $result;
			
		}	
		public function get_selected_record($postData)
		{  
			$selected_company = $this->session->userdata('root_company');
			
			$sql ='SELECT '.db_prefix().'nsaccountmaster.* FROM '.db_prefix().'nsaccountmaster WHERE UserID = "'.$postData['userid'].'" AND PlantID = '.$selected_company;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
			
		}
		public function get_staff_details($userID)
		{  
			$selected_company = $this->session->userdata('root_company');
            $regExp ='.*;s:[0-9]+:"'.$selected_company.'".*';
            
			
			$this->db->select(db_prefix() . 'staff.*');
			$this->db->where(db_prefix() . 'staff.AccountID',$userID);
			$result = $this->db->get(db_prefix() . 'staff')->row(); 
			return $result;
			
		}
		
		public function get_account_subgroup_details1($subgroup_code)
		{  
			
			$sql ='SELECT '.db_prefix().'AccountSubGroup1.*
			FROM '.db_prefix().'AccountSubGroup1 WHERE SubActGroupID1 = '.$subgroup_code;
			
			$result = $this->db->query($sql)->row();
			return $result;
			
		}
		public function get_account_subgroup_details2($subgroup_code)
		{  
			$sql ='SELECT '.db_prefix().'AccountSubGroup2.*,'.db_prefix().'AccountSubGroup1.SubActGroupID1,'.db_prefix().'AccountSubGroup1.SubActGroupName AS SubActGroupName1,'.db_prefix().'AccountMainGroup.ActGroupID,'.db_prefix().'AccountMainGroup.ActGroupName AS ActGroupNameMain
			FROM '.db_prefix().'AccountSubGroup2
			INNER JOIN '.db_prefix().'AccountSubGroup1 ON '.db_prefix().'AccountSubGroup1.SubActGroupID1 = '.db_prefix().'AccountSubGroup2.SubActGroupID1
			INNER JOIN '.db_prefix().'AccountMainGroup ON '.db_prefix().'AccountMainGroup.ActGroupID = '.db_prefix().'AccountSubGroup1.ActGroupID
			WHERE SubActGroupID = '.$subgroup_code;
			$result = $this->db->query($sql)->row();
			return $result;
		}

		


		public function get_account_subgroup_details($subgroup_code)
		{  
			$sql ='SELECT '.db_prefix().'accountgroupssub.*
			FROM '.db_prefix().'accountgroupssub WHERE SubActGroupID = '.$subgroup_code;
			
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		
		// Update account Group data
		public function update_account_subgroup($accout_subgroup_id,$data)
		{
			
			$this->db->where('SubActGroupID', $accout_subgroup_id);
			$this->db->update(db_prefix() . 'accountgroupssub', $data);
			if ($this->db->affected_rows() > 0) {
				
				return true;
			}
			
			return false;
		}
		
		
		public function get_state(){
			
			$this->db->order_by('state_name');
			$state__list = $this->db->get(db_prefix() . 'xx_statelist')->result_array();
			return $state__list;
		}
		
		public function get_acount_detail($account_id)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$this->db->select(db_prefix() . 'clients.*,' . db_prefix() . 'contacts.firstname,' . db_prefix() . 'contacts.phonenumber AS mobile1,'. db_prefix() . 'contacts.email,
			'. db_prefix() . 'contacts.pincode,'. db_prefix() . 'contacts.kms,'. db_prefix() . 'contacts.Pan,'. db_prefix() . 'contacts.Aadhaarno,'. db_prefix() . 'contacts.Officeno,'. db_prefix() . 'accountbalances.BAL1');
            $this->db->where(db_prefix() . 'contacts.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
            $this->db->where(db_prefix() . 'clients.AccountID', $account_id);
            $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID');
            $this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY = "'.$FY.'"','LEFT');
            //$this->db->order_by('description', 'asc');
            $account_detail = $this->db->get(db_prefix() . 'clients')->row();
            return $account_detail;
		}
		
		
		public function get_subgroup_for_accounting_head()
		{
			$ss = 'SELECT * FROM tblaccountgroupssub ';
			$result_data = $this->db->query($ss)->result_array();
			return $result_data;
		}
		
		public function get_group_for_accounting_head(){
			
			
			$ss = 'SELECT * FROM tblaccountgroups';
			$result_data = $this->db->query($ss)->result_array();
			return $result_data;
		}
		
		public function GetSubGroupOneByMainGroupId($Account_Group){
			
			$ss = "SELECT * FROM tblAccountSubGroup1 WHERE ActGroupID=".$Account_Group."";
			$result_data = $this->db->query($ss)->result_array();
			return $result_data;
		}
		
		public function GetSubGroupTwoBySubAccount_Group1($SubAccount_Group1){
			
			$ss = "SELECT * FROM tblaccountgroupssub WHERE SubActGroupID1=".$SubAccount_Group1."";
			$result_data = $this->db->query($ss)->result_array();
			return $result_data;
		}
		public function GetSubGroupBySubAccount_Group2($SubAccount_Group2){
			
			$ss = "SELECT * FROM tblaccountgroupssub WHERE SubActGroupID2=".$SubAccount_Group2."";
			$result_data = $this->db->query($ss)->result_array();
			return $result_data;
		}
		
		
		
		public function update_bal($update_bal,$account_id)
		{
			$selected_company = $this->session->userdata("root_company");
			$FY = $this->session->userdata("finacial_year");
            $this->db->where('PlantID', $selected_company);
            $this->db->where('AccountID', $account_id);
            $this->db->where('FY', $FY);
            $this->db->update(db_prefix() . 'accountbalances', $update_bal);
            if ($this->db->affected_rows() > 0) {
                return true;
				}else{
				return false;
			}
		}
		
		
		
		// add contact details
		
		public function add_contact($data)
		{
			
			$this->db->insert(db_prefix() . 'contacts', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				
				return true;
			}
			
			return false;
		}
		
		// add Act Balance record
		
		public function add_act_bal($data)
		{
			$this->db->insert(db_prefix() . 'accountbalances', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				return true;
			}
			return false;
		}
		
		
		
		public function GetDayReportBodyData($filterdata)
		{ 
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql = 'SELECT '.db_prefix() . 'accountledger.*,tblclients.company,tblclients.payment_term FROM '.db_prefix() . 'accountledger 
			INNER JOIN tblclients ON tblclients.AccountID = tblaccountledger.AccountID
			WHERE '.db_prefix() . 'accountledger.FY = '.$fy.'  AND '.db_prefix() . 'accountledger.PlantID = '.$selected_company.'
			AND tblaccountledger.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59" 
			ORDER BY tblaccountledger.Transdate DESC';
			
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		
		//end here
		
		
		
		public function get_hsn($id = '')
		{
			
			$this->db->select('*');
			$this->db->from(db_prefix() . 'hsn');
			if (is_numeric($id)) {
				$this->db->where(db_prefix() . 'hsn.id', $id);
				
				return $this->db->get()->row();
			}
			return $this->db->get()->result_array();
		}

		function CheckShortCodeExit($ShortCode)
		{
			$this->db->select('tblAccountSubGroup2.*');
			$this->db->where("ShortCode" ,$ShortCode);
			return $this->db->get(db_prefix() . 'AccountSubGroup2')->row();
		}
		
		
	}
