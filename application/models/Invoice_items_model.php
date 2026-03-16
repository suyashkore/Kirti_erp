<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Invoice_items_model extends App_Model
	{
		public function __construct()
		{
			parent::__construct();
		}
		
		/**
			* Get invoice item by ID
			* @param  mixed $id
			* @return mixed - array if not passed id, object if id passed
		*/
		public function get($id = '')
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			$columns             = $this->db->list_fields(db_prefix() . 'items');
			$rateCurrencyColumns = '';
			foreach ($columns as $column) {
				if (strpos($column, 'rate_currency_') !== false) {
					$rateCurrencyColumns .= $column . ',';
				}
			}
			$this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid,rate,' . db_prefix() . 'stockmaster.OQty,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            description,long_description,item_code,MainGrpID,SubGrpID1,SubGrpID2,brand_name,local_supply_in,outst_supply_in,crate_qty,case_qty,bowl_qty,min_qty,max_qty,weight,mrp,product_range,
            case_weight,min_day,monitorstock,hsn_code,rack_id,subrack_id,isactive,' . db_prefix() . 'ItemsDivisionMaster.name as group_name,' . db_prefix() . 'ItemsSubGroup1.name as subgroup_name,unit,image');
			$this->db->from(db_prefix() . 'items');
			$this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
			$this->db->join(db_prefix() . 'ItemsDivisionMaster', '' . db_prefix() . 'ItemsDivisionMaster.id = ' . db_prefix() . 'items.group_id', 'left');
			$this->db->join(db_prefix() . 'ItemsSubGroup1', '' . db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1', 'left');
			$this->db->join(db_prefix() . 'ItemsSubGroup2', '' . db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2', 'left');
			$this->db->join(db_prefix() . 'stockmaster', '' . db_prefix() . 'stockmaster.ItemID = ' . db_prefix() . 'items.item_code AND ' . db_prefix() . 'stockmaster.PlantID = ' . db_prefix() . 'items.PlantID AND ' . db_prefix() . 'stockmaster.FY = "'.$fy.'" AND ' . db_prefix() . 'stockmaster.cnfid = "1" AND ' . db_prefix() . 'stockmaster.GodownID = "'.$GodownID.'"' , 'left');
			$this->db->order_by('description', 'asc');
			if ($id) {
				$this->db->where(db_prefix() . 'items.item_code', $id);
				
				$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
				
				$data = $this->db->get()->row();
				
				if($data){
					$data->SubGroup1List = $this->GetSubgroup1Data($data->MainGrpID);
					$data->SubGroup2List = $this->GetSubgroup2Data($data->SubGrpID1);
					
					$Stocks = $this->getStocks($id);
					$StocksDetails = $this->getStocksDetails($id);
					$data->PQty = 0;
					$data->PRQty = 0;
					$data->IQty = 0;
					$data->PRDQty = 0;
					$data->SQty = 0;
					$data->SRQty = 0;
					$data->ADJQTY = 0;
					$adj = 0;
					foreach ($StocksDetails as $stock) {
						if($stock['TType'] == 'P'){
							$data->PQty = $stock['BilledQty'];
							}elseif($stock['TType'] == 'N'){
							$data->PRQty = $stock['BilledQty'];
							}elseif($stock['TType'] == 'A'){
							$data->IQty = $stock['BilledQty'];
							}elseif($stock['TType'] == 'B'){
							$data->PRDQty = $stock['BilledQty'];
							}elseif($stock['TType'] == 'O' && $stock['TType2'] == 'Order'){
							$data->SQty = $stock['BilledQty'];
							}elseif($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh'){
							$data->SRQty = $stock['BilledQty'];
							}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment'){
							$adj += $stock['BilledQty'];
							}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity'){
							$adj += $stock['BilledQty'];
							}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free Distribution'){
							$adj += $stock['BilledQty'];
							}elseif($stock['TType'] == 'X' && $stock['TType2'] == 'Free distribution'){
							$adj += $stock['BilledQty'];
						}
					}
					$data->ADJQTY = $adj;
					$itemStatus = $this->getItemStatus($id);
					if(empty($itemStatus)){
						}else{
						$data->itemStatus = $itemStatus;
					}
					
					if(empty($Stocks)){
						}else{
						$data->stocks = $Stocks;
					}
				}
				return $data;
			}
			
			return $this->db->get()->result_array();
		}
		
		function getStocks($id){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			
			$this->db->select('*');
			$this->db->where(db_prefix() . 'stockmaster.ItemID', $id);
			$this->db->where(db_prefix() . 'stockmaster.FY', $fy);
			$this->db->where(db_prefix() . 'stockmaster.cnfid', 1);
			$this->db->where('GodownID',$GodownID);
			$this->db->order_by('PlantID', 'ASC');
			$records = $this->db->get(db_prefix() . 'stockmaster')->result();
			return $records;
		}
		
		function getStocksDetails($id){
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');
			$this->db->from(db_prefix() .'history');
			$this->db->where(db_prefix() .'history.PlantID', $selected_company);
			$this->db->where(db_prefix() . 'history.ItemID ', $id);
			$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);
			$this->db->where(db_prefix() .'history.FY', $fy);
			$this->db->group_by('ItemID,TType,TType2');
			return $this->db->get()->result_array();
		}
		
		function getItemStatus($id){
			$fy = $this->session->userdata('finacial_year');
			$this->db->select(db_prefix() . 'items.isactive,'.db_prefix() . 'items.PlantID');
			$this->db->where(db_prefix() . 'items.item_code', $id);
			$this->db->order_by('PlantID', 'ASC');
			$records = $this->db->get(db_prefix() . 'items')->result();
			return $records;
		}
		function getitem($postData){
			
			$response = array();
			$subgroup = array('9','20','36');
			if(isset($postData['search']) ){
				// Select record
				$item_div_id = explode(",",$postData['item_divistion']);
				//if($postData['item_taxes']=="Non-Taxable"){
				//$non_taxable_id = get_zerotaxrate_id();
				//}
				
				$this->db->select('*');
				$this->db->where("description like '%".$postData['search']."%' OR item_code like '%".$postData['search']."%'");
				$this->db->where_not_in('SubGrpID1',$subgroup);
				
				$selected_company = $this->session->userdata('root_company');
				$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
				$records = $this->db->get(db_prefix() . 'items')->result();
				
				
				if(isset($postData['clientid']) ){
					$this->db->select('*');
					$this->db->where(db_prefix() . 'clients.AccountID', $postData['clientid']);
					$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
					$client = $this->db->get(db_prefix() . 'clients')->row();
					// print_r($client);die;
					
				}
				
				foreach($records as $row ){
					
					$clientdis = 0;
					if($row->tax > 1){
						if(!empty($client)){
							
							$clientdis = $client->dis_per_taxable;
							// print_r( $client);die;
						}
						}else{
						if(!empty($client)){
							$clientdis = $client->dis_per;
						}
					}
					$response[] = array("itemdiv"=>$row->group_id,"gst"=>$row->tax,"value"=>$row->item_code,"label"=>$row->description,"location"=>$postData['location'],"item_taxes"=>$postData['item_taxes'],"dist_type_id"=>$postData['dist_type_id'],"dist_state_id"=>$postData['dist_state_id'],"clientid"=>$clientdis,"isactive"=>$row->isactive);
				}
				
			}
			
			return $response;
		}
		
		function getitem_using_itemcode($postData){
			
			$response = array();
			$FGMainGroupID = array('1');
			if(isset($postData['search']) ){
				// Select record
				$item_div_id = explode(",",$postData['item_divistion']);
				//if($postData['item_taxes']=="Non-Taxable"){
				//$non_taxable_id = get_zerotaxrate_id();
				//}
				
				$this->db->select('*');
				$this->db->where("item_code like '%".$postData['search']."%' OR  description like '%".$postData['search']."%'");
				$this->db->where_in('MainGrpID',$FGMainGroupID);
				
				$selected_company = $this->session->userdata('root_company');
				$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
				$records = $this->db->get(db_prefix() . 'items')->result();
				
				if(isset($postData['clientid']) ){
					$this->db->select('*');
					$this->db->where(db_prefix() . 'clients.AccountID', $postData['clientid']);
					$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
					$client = $this->db->get(db_prefix() . 'clients')->row();
				}
				
				foreach($records as $row ){
					$clientdis = 0;
					if($row->tax > 1){
						if(!empty($client)){
							$clientdis = $client->dis_per_taxable;
						}
						}else{
						if(!empty($client)){
							$clientdis = $client->dis_per;
						}
					}
					$response[] = array("itemdiv"=>$row->group_id,"gst"=>$row->tax,"value"=>$row->item_code,"label"=>$row->description,"location"=>$postData['location'],"item_taxes"=>$postData['item_taxes'],"dist_type_id"=>$postData['dist_type_id'],"dist_state_id"=>$postData['dist_state_id'],"clientid"=>$clientdis,"isactive"=>$row->isactive);
				}
				
			}
			
			return $response;
		}
		
		function getItemDetailsByID($postData){
			
			$response = array();
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select('group_id,tax,item_code,description,isactive');
			$this->db->where("item_code" ,$postData['ItemID']);
			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
			$records = $this->db->get(db_prefix() . 'items')->row();
			
			if ($records) {
				if(isset($postData['clientid']) ){
					$this->db->select('*');
					$this->db->where(db_prefix() . 'clients.AccountID', $postData['clientid']);
					$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
					$client = $this->db->get(db_prefix() . 'clients')->row();
					// print_r( $client);die;
				}
				
				$clientdis = 0;
				if($records->tax > 1){
					if(!empty($client)){
						
						$clientdis = $client->dis_per_taxable;
					}
					}else{
					if(!empty($client)){
						$clientdis = $client->dis_per;
					}
				}
				$records->location = $postData['location'];
				$records->item_taxes = $postData['item_taxes'];
				$records->dist_type_id = $postData['dist_type_id'];
				$records->dist_state_id = $postData['dist_state_id'];
				$records->clientid = $clientdis;
			}
			return $records;
		}
		
		/**
			* Get invoice item by ID
			* @param  mixed $id
			* @return mixed - array if not passed id, object if id passed
		*/
		public function get2($id = '')
		{
			$columns             = $this->db->list_fields(db_prefix() . 'items');
			$rateCurrencyColumns = '';
			foreach ($columns as $column) {
				if (strpos($column, 'rate_currency_') !== false) {
					$rateCurrencyColumns .= $column . ',';
				}
			}
			$this->db->select($rateCurrencyColumns . '' . db_prefix() . 'items.id as itemid,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
            description,long_description,item_code,group_id,SubGrpID2,' . db_prefix() . 'ItemsDivisionMaster.name as group_name,' . db_prefix() . 'ItemsSubGroup1.name as subgroup_name,unit');
			$this->db->from(db_prefix() . 'items');
			$this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
			$this->db->join('' . db_prefix() . 'taxes t2', 't2.id = ' . db_prefix() . 'items.tax2', 'left');
			$this->db->join(db_prefix() . 'ItemsDivisionMaster', '' . db_prefix() . 'ItemsDivisionMaster.id = ' . db_prefix() . 'items.group_id', 'left');
			$this->db->join(db_prefix() . 'ItemsSubGroup1', '' . db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID2', 'left');
			$this->db->order_by('description', 'asc');
			return $this->db->get()->result_array();
		}
		
		public function get_rate_master_data_by_id2($item_id, $distributor_id, $state_id)
		{
			$curDate = date('Y-m-d H:i:s');
			$this->db->select('*');
			$this->db->where('state_id', $state_id);
			$this->db->where('distributor_id', $distributor_id);
			$this->db->where('item_id', $item_id);
			$this->db->where('effective_date <=', $curDate);
			$this->db->from(db_prefix() . 'rate_master');
			$this->db->order_by('effective_date DESC, id DESC');
			$data =  $this->db->get()->row();
			
			if(empty($data)){
				$this->db->select(db_prefix() . 'ratehistory2.BasicRate AS assigned_rate');
				$this->db->where('StateID', $state_id);
				$this->db->where('DistributorType', $distributor_id);
				$this->db->where('ItemID', $item_id);
				$this->db->where('EffDate <=', $curDate);
				$this->db->order_by('EffDate', 'DESC');
				$this->db->from(db_prefix() . 'ratehistory2');
				$data2 =  $this->db->get()->row();
				return $data2;
				}else{
				return $data;
			}
			
		}
		
		public function get_grouped()
		{
			$items = [];
			$this->db->order_by('name', 'asc');
			$groups = $this->db->get(db_prefix() . 'ItemsDivisionMaster')->result_array();
			
			array_unshift($groups, [
            'id'   => 0,
            'name' => '',
			]);
			
			foreach ($groups as $group) {
				$this->db->select('*,' . db_prefix() . 'ItemsDivisionMaster.name as group_name,' . db_prefix() . 'items.id as id');
				$this->db->where('group_id', $group['id']);
				$this->db->join(db_prefix() . 'ItemsDivisionMaster', '' . db_prefix() . 'ItemsDivisionMaster.id = ' . db_prefix() . 'items.group_id', 'left');
				$this->db->order_by('description', 'asc');
				$_items = $this->db->get(db_prefix() . 'items')->result_array();
				if (count($_items) > 0) {
					$items[$group['id']] = [];
					foreach ($_items as $i) {
						array_push($items[$group['id']], $i);
					}
				}
			}
			
			return $items;
		}
		
		// Add New ItemID
		public function SaveItemID($data,$StockQty,$ItemStatus_new)
		{
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			
			$stockQty = explode(",",$StockQty);
			$ItemStatus = explode(",",$ItemStatus_new);
			$company_data = $this->GetRootCompany();
			$data['UserId'] = $UserID;
			$data['TransDate'] = date('Y-m-d H:i:s');
			
			$i = 0;
            foreach ($company_data as $key => $value) {
                $data['PlantID'] = $value['id'];
                // $data['isactive'] = $ItemStatus[$i];
                $this->db->insert(db_prefix() . 'items', $data);
                $INSERT = $this->db->affected_rows();
                if($INSERT > 0){
                    if($value['id'] == "1"){
                        $GodownID = 'CSPL';
						}else if($value['id'] == "2"){
                        $GodownID = 'CFF';
						}else if($value['id'] == "3"){
                        $GodownID = 'CBUPL';
					}
                    //$checkRecord = $this->ChkRecord($data['item_code'],$value['id'],$FY);
                    //stock update
					$stock_data = array(
					"OQty"=>$stockQty[$i],
					"cnfid"=>1,
					"ItemID"=>$data['item_code'],
					"PlantID"=>$value['id'],
					"GodownID"=>$GodownID,
					"FY"=>$FY,
					"UserId"=>$UserID,
					"EffDate"=>date('Y-m-d H:i:s')
					);
					$this->db->insert(db_prefix() . 'stockmaster', $stock_data);
					$i++;
				}
			}
			
			if($INSERT > 0){
				return true;
				}else{
				return false;
			}
		}
		
		//==================== Add New MainItemGroup ===================================
		public function SaveMainItemGroup($data)
		{
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$this->db->insert(db_prefix() . 'items_main_groups', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		function CheckPrefixExit($MainItemGroupPrefix)
		{
			$this->db->select('tblitems_main_groups.*');
			$this->db->where("prefix" ,$MainItemGroupPrefix);
			return $this->db->get(db_prefix() . 'items_main_groups')->row();
		}
		
		// Add New Item Division
		public function SaveItemDivision($data)
		{
			$this->db->insert(db_prefix() . 'ItemsDivisionMaster', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		// Add New ItemGroup
		public function SaveItemGroup($data)
		{
			$this->db->insert(db_prefix() . 'ItemsSubGroup1', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		// Add New ItemSubGroup2
		public function SaveItemSubGroup2($data)
		{
			$this->db->insert(db_prefix() . 'ItemsSubGroup2', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		// Update Exiting ItemID
		public function UpdateItemID($data,$StockQty,$item_code,$ItemStatus_new)
		{
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$status = $data['isactive'];
			// unset($data['isactive']);
			$stockQty = explode(",",$StockQty);
			$ItemStatus = explode(",",$ItemStatus_new);
			$company_data = $this->GetRootCompany();
			
			$UPDATE = 0;
			$i = 0;
            foreach ($company_data as $key => $value) {
                $checkItemRecord = $this->ChkItemRecord($item_code,$value['id']);
                if(empty($checkItemRecord)){
                    
                    unset($data['UserID2']);
                    unset($data['Lupdate']);
                    unset($data['isactive']);
                    /*if($value['id'] !== $selected_company){
                        $data['isactive'] = $status;
					}*/
                    $data['isactive'] = $ItemStatus[$i];
                    $data['item_code'] = $item_code;
                    $data['PlantID'] = $value['id'];
                    $data['UserID'] = $UserID;
                    $data['TransDate'] = date('Y-m-d H:i:s');
                    if($value['id'] == $selected_company){
                        $this->db->insert(db_prefix() . 'items', $data);
					}
					}else{
                    unset($data['item_code']);
                    unset($data['PlantID']);
                    unset($data['UserID']);
                    unset($data['TransDate']);
                    // unset($data['isactive']);
                    /*if($value['id'] == $selected_company){
                        $data['isactive'] = $status;
					}*/
                    // $data['isactive'] = $ItemStatus[$i];
                    $data['UserID2'] = $UserID;
                    $data['Lupdate'] = date('Y-m-d H:i:s');
                    if($value['id'] == $selected_company){
                        $this->db->where('item_code', $item_code);
                        $this->db->where('PlantID', $value['id']);
                        $this->db->update(db_prefix() . 'items', $data);
						}else{
                        $data2 = array(
						'isactive'=> $ItemStatus[$i],
						'UserID2'=> $UserID,
						'Lupdate'=> date('Y-m-d H:i:s'),
                        );
                        $this->db->where('item_code', $item_code);
                        $this->db->where('PlantID', $value['id']);
                        $this->db->update(db_prefix() . 'items', $data2);
					}
				}
                
                $updateR = $this->db->affected_rows();
                $UPDATE += $updateR;
                //if($UPDATE > 0){
				$checkStockRecord = $this->ChkStockRecord($item_code,$value['id'],$FY);
				if(empty($checkStockRecord)){
					//stock Record Create
					$stock_data = array(
					"OQty"=>$stockQty[$i],
					"cnfid"=>1,
					"ItemID"=>$item_code,
					"PlantID"=>$value['id'],
					"GodownID"=>$GodownID,
					"FY"=>$FY,
					"UserId"=>$UserID,
					"EffDate"=>date('Y-m-d H:i:s')
					);
					$this->db->insert(db_prefix() . 'stockmaster', $stock_data);
                    }else{
					//stock Record Update
					$stock_data = array(
					"OQty"=>$stockQty[$i],
					"UserID2"=>$UserID,
					"Lupdate"=>date('Y-m-d H:i:s')
					);
					$this->db->where('ItemID', $item_code);
					$this->db->where('PlantID', $value['id']);
					$this->db->where('GodownID', $GodownID);
					$this->db->where('FY', $FY);
					$this->db->update(db_prefix() . 'stockmaster', $stock_data);
				}
				$i++;
                //}
			}
			
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		//====================== Update Exiting MainItemGroup ==========================
		public function UpdateMainItemGroup($data,$itemGroupID)
		{
			$company_data = $this->GetRootCompany();
			$UserID = $this->session->userdata('username');
			// $i = 0;
			$this->db->where('id', $itemGroupID);
			$this->db->update(db_prefix() . 'items_main_groups', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		// Update Exiting Item Division
		public function UpdateItemDivision($data,$ItemDivisionID)
		{
			$this->db->where('id', $ItemDivisionID);
			$this->db->update(db_prefix() . 'ItemsDivisionMaster', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		// Update Exiting ItemGroup
		public function UpdateItemGroup($data,$itemGroupID)
		{
			$this->db->where('id', $itemGroupID);
			$this->db->update(db_prefix() . 'ItemsSubGroup1', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		// Update Exiting ItemSubGroup2
		public function UpdateItemSubGroup2($data,$itemGroupID)
		{
			$this->db->where('id', $itemGroupID);
			$this->db->update(db_prefix() . 'ItemsSubGroup2', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		// Get Root Company
		public function GetRootCompany()
		{
			$this->db->select(db_prefix() . 'rootcompany.*');
			$this->db->order_by('id', 'ASC');
			$this->db->from(db_prefix() . 'rootcompany');
			$data =  $this->db->get()->result_array();
			return $data;
		}
		
		// Check StockMaster Record
		public function ChkStockRecord($ItemID,$PlantID,$fy)
		{
			if($PlantID == "1"){
				$GodownID = 'CSPL';
				}else if($PlantID == "2"){
				$GodownID = 'CFF';
				}else if($PlantID == "3"){
				$GodownID = 'CBUPL';
			}
			$this->db->select(db_prefix() . 'stockmaster.*');
			$this->db->where('ItemID', $ItemID);
			$this->db->where('PlantID', $PlantID);
			$this->db->where('GodownID',$GodownID);
			$this->db->where('FY', $fy);
			$this->db->from(db_prefix() . 'stockmaster');
			$data =  $this->db->get()->row();
			return $data;
		}
		
		// Check ItemMaster Record
		public function ChkItemRecord($ItemID,$PlantID)
		{
			$this->db->select(db_prefix() . 'items.*');
			$this->db->where('item_code', $ItemID);
			$this->db->where('PlantID', $PlantID);
			$this->db->from(db_prefix() . 'items');
			$data =  $this->db->get()->row();
			return $data;
		}
		
		/**
			* Add new invoice item
			* @param array $data Invoice item data
			* @return boolean
		*/
		public function add($data)
		{
			unset($data['itemid']);
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			
			$FY = $this->session->userdata('finacial_year');
			$UserID = $this->session->userdata('username');
			$data['PlantID'] = $selected_company;
			$data['tax2'] = 0;
			
			$data['item_code'] = $data['item_code1'];
			unset($data['item_code1']);
			
			unset($data['rate']);
			
			
			$this->db->insert(db_prefix() . 'items', $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id) {
				$data_stock = array(
                'PlantID'=>$selected_company,
                'FY'=>$FY,
                'cnfid'=>1,
                'GodownID'=>$GodownID,
                'ItemID'=>$data['item_code'],
                'EffDate'=>date('Y-m-d H:i:s'),
                'UserId'=>$UserID,
                );
				$this->db->insert(db_prefix() . 'stockmaster', $data_stock);
				hooks()->do_action('item_created', $insert_id);
				
				log_activity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');
				
				return $insert_id;
			}
			
			return false;
		}
		
		/**
			* Update invoiec item
			* @param  array $data Invoice data to update
			* @return boolean
		*/
		public function edit($data)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			if($selected_company == "1"){
				$GodownID = 'CSPL';
				}else if($selected_company == "2"){
				$GodownID = 'CFF';
				}else if($selected_company == "3"){
				$GodownID = 'CBUPL';
			}
			
			$itemid = $data['itemid'];
			unset($data['itemid']);
			$opening_stock = $data['opening_stock'];
			unset($data['opening_stock']);
			
			if (isset($data['group_id']) && $data['group_id'] == '') {
				$data['group_id'] = 0;
			}
			
			if (isset($data['tax']) && $data['tax'] == '') {
				$data['tax'] = null;
			}
			
			if (isset($data['tax2']) && $data['tax2'] == '') {
				$data['tax2'] = null;
			}
			
			if (isset($data['custom_fields'])) {
				$custom_fields = $data['custom_fields'];
				unset($data['custom_fields']);
			}
			
			$columns = $this->db->list_fields(db_prefix() . 'items');
			$this->load->dbforge();
			
			foreach ($data as $column => $itemData) {
				if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== false) {
					$field = [
					$column => [
					'type' => 'decimal(15,' . get_decimal_places() . ')',
					'null' => true,
					],
					];
					$this->dbforge->add_column('items', $field);
				}
			}
			
			$affectedRows = 0;
			
			$data = hooks()->apply_filters('before_update_item', $data, $itemid);
			
			$this->db->where('item_code', $itemid);
			$this->db->where('PlantID', $selected_company);
			$this->db->update(db_prefix() . 'items', $data);
			
			if($opening_stock !== '' && isset($opening_stock)){
				//stock update
				$stock_data = array(
                "OQty"=>$opening_stock,
                );
				$this->db->where('ItemID', $itemid);
				$this->db->where('PlantID', $selected_company);
				$this->db->where('FY', $fy);
				$this->db->where('GodownID',$GodownID);
				$this->db->update(db_prefix() . 'stockmaster', $stock_data);
			}
            
			if ($this->db->affected_rows() > 0) {
				log_activity('Invoice Item Updated [ID: ' . $itemid . ', ' . $data['description'] . ']');
				$affectedRows++;
			}
			
			if (isset($custom_fields)) {
				if (handle_custom_fields_post($itemid, $custom_fields, true)) {
					$affectedRows++;
				}
			}
			
			if ($affectedRows > 0) {
				hooks()->do_action('item_updated', $itemid);
			}
			
			return $affectedRows > 0 ? true : false;
		}
		
		public function search($q)
		{
			$this->db->select('rate, id, description as name, long_description as subtext');
			$this->db->like('description', $q);
			$this->db->or_like('long_description', $q);
			
			$items = $this->db->get(db_prefix() . 'items')->result_array();
			
			foreach ($items as $key => $item) {
				$items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
				$items[$key]['name']    = '(' . app_format_number($item['rate']) . ') ' . $item['name'];
			}
			
			return $items;
		}
		
		/**
			* Delete invoice item
			* @param  mixed $id
			* @return boolean
		*/
		public function delete($id)
		{
			$this->db->where('id', $id);
			$this->db->delete(db_prefix() . 'items');
			if ($this->db->affected_rows() > 0) {
				$this->db->where('relid', $id);
				$this->db->where('fieldto', 'items_pr');
				$this->db->delete(db_prefix() . 'customfieldsvalues');
				
				log_activity('Invoice Item Deleted [ID: ' . $id . ']');
				
				hooks()->do_action('item_deleted', $id);
				
				return true;
			}
			
			return false;
		}
		
		public function get_groups()
		{
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->order_by('name', 'asc');
			//$this->db->where('PlantID', $selected_company);
			
			return $this->db->get(db_prefix() . 'ItemsDivisionMaster')->result_array();
		}
		public function get_custitem_groups($AccountID)
		{
			
			$selected_company = $this->session->userdata('root_company');
			//$this->db->order_by('name', 'asc');
			$this->db->where('plant_assign', $selected_company);
			$this->db->where('AccountID', $AccountID);
			return $this->db->get(db_prefix() . 'accountitemdiv')->result_array();
		}
		
		public function add_group($data)
		{
			$this->db->insert(db_prefix() . 'ItemsDivisionMaster', $data);
			if ($this->db->affected_rows() > 0) {
				log_activity('Items Group Created [Name: ' . $data['name'] . ']');
				
				return $this->db->insert_id();
				}else{
				return false;
			}
			
		}
		
		public function edit_group($data, $id)
		{
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'ItemsDivisionMaster', $data);
			if ($this->db->affected_rows() > 0) {
				log_activity('Items Group Updated [Name: ' . $data['name'] . ']');
				
				return true;
			}
			
			return false;
		}
		
		public function delete_group($id)
		{
			$this->db->where('id', $id);
			$group = $this->db->get(db_prefix() . 'ItemsDivisionMaster')->row();
			
			if ($group) {
				$this->db->where('group_id', $id);
				$this->db->update(db_prefix() . 'items', [
                'group_id' => 0,
				]);
				
				$this->db->where('id', $id);
				$this->db->delete(db_prefix() . 'ItemsDivisionMaster');
				if ($this->db->affected_rows() > 0) {
					log_activity('Item Group Deleted [Name: ' . $group->name . ']');
					
					return true;
				}
				return false;
			}
			
			return false;
		}
		
		
		
		public function get_main_groups()
		{
			//$selected_company = $this->session->userdata('root_company');
			$this->db->order_by('name', 'asc');
			//$this->db->where('PlantID', $selected_company);
			return $this->db->get(db_prefix() . 'items_main_groups')->result_array();
		}
		
		public function add_main_group($data)
		{
			$this->db->insert(db_prefix() . 'items_main_groups', $data);
			log_activity('Items Main Group Created [Name: ' . $data['name'] . ']');
			
			return $this->db->insert_id();
		}
		
		public function edit_main_group($data, $id)
		{
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'items_main_groups', $data);
			if ($this->db->affected_rows() > 0) {
				log_activity('Items Main Group Updated [Name: ' . $data['name'] . ']');
				
				return true;
			}
			
			return false;
		}
		
		public function edit_sub_group($data, $id)
		{
			$this->db->where('id', $id);
			$this->db->update(db_prefix() . 'ItemsSubGroup1', $data);
			if ($this->db->affected_rows() > 0) {
				log_activity('Items Sub Group Updated [Name: ' . $data['name'] . ']');
				
				return true;
			}
			
			return false;
		}
		
		public function delete_main_group($id)
		{
			$this->db->where('id', $id);
			$group = $this->db->get(db_prefix() . 'items_main_groups')->row();
			
			if ($group) {
				/*$this->db->where('group_id', $id);
					$this->db->update(db_prefix() . 'items', [
					'group_id' => 0,
					]);
				*/
				$this->db->where('id', $id);
				$this->db->delete(db_prefix() . 'items_main_groups');
				if ($this->db->affected_rows() > 0) {
					log_activity('Item Main Group Deleted [Name: ' . $group->name . ']');
					
					return true;
				}
				return false;
			}
			
			return false;
		}
		
		public function delete_sub_group($id)
		{
			$this->db->where('id', $id);
			$group = $this->db->get(db_prefix() . 'ItemsSubGroup1')->row();
			
			if ($group) {
				/*$this->db->where('group_id', $id);
					$this->db->update(db_prefix() . 'items', [
					'group_id' => 0,
					]);
				*/
				$this->db->where('id', $id);
				$this->db->delete(db_prefix() . 'ItemsSubGroup1');
				if ($this->db->affected_rows() > 0) {
					log_activity('Item Sub Group Deleted [Name: ' . $group->name . ']');
					
					return true;
				}
				return false;
				
			}
			
			return false;
		}
		
		public function get_sub_groups()
		{
			//$selected_company = $this->session->userdata('root_company');
			$this->db->order_by('name', 'asc');
			//$this->db->where('PlantID', $selected_company);
			
			return $this->db->get(db_prefix() . 'ItemsSubGroup1')->result_array();
		}
		public function get_measure_unit()
		{
			//$selected_company = $this->session->userdata('root_company');
			$this->db->order_by('UOM', 'asc');
			//$this->db->where('PlantID', $selected_company);
			
			return $this->db->get(db_prefix() . 'unitofmeasures')->result_array();
		}
		
		public function get_item_rack()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->order_by('RackName', 'asc');
			$this->db->where('PlantID', $selected_company);
			
			return $this->db->get(db_prefix() . 'rackmaster')->result_array();
		}
		
		public function add_sub_group($data)
		{
			$this->db->insert(db_prefix() . 'ItemsSubGroup1', $data);
			if ($this->db->affected_rows() > 0) {
				log_activity('Items Sub Group Created [Name: ' . $data['name'] . ']');
				
				return $this->db->insert_id();
			}
			
			return false;
			
			
			
		}
		
		// here code for table
		public function get_table_data(){
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'items.id as itemid,'.db_prefix() . 'items.UserId as useriditem,rate,
            t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
            description,long_description,item_code,group_id,SubGrpID1,local_supply_in,outst_supply_in,crate_qty,case_qty,bowl_qty,min_qty,
            case_weight,min_day,monitorstock,hsn_code,rack_id,subrack_id,isactive,' . db_prefix() . 'ItemsDivisionMaster.name as group_name,' . db_prefix() . 'items_main_groups.name as MainGrpName,' . db_prefix() . 'ItemsSubGroup1.name as subgroup_name,' . db_prefix() . 'ItemsSubGroup2.name as subgroup2_name,unit,image');
			$this->db->from(db_prefix() . 'items');
			$this->db->join('' . db_prefix() . 'taxes t1', 't1.id = ' . db_prefix() . 'items.tax', 'left');
			$this->db->join(db_prefix() . 'ItemsDivisionMaster', '' . db_prefix() . 'ItemsDivisionMaster.id = ' . db_prefix() . 'items.group_id', 'left');
			$this->db->join(db_prefix() . 'items_main_groups', '' . db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items.MainGrpID ', 'left');
			$this->db->join(db_prefix() . 'ItemsSubGroup1', '' . db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1', 'left');
			$this->db->join(db_prefix() . 'ItemsSubGroup2', '' . db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2', 'left');
			$this->db->order_by('item_code', 'ASC');
			
			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
			return $this->db->get()->result_array();
		}
		// end
		
		
		// MainItemGroup Table Data
		public function get_MainItemGroup_data(){
        $this->db->select('
        mig.*,
        itm.ItemTypeName
    ');
        $this->db->from('tblitems_main_groups mig');

		// Item Type Master
        $this->db->join('tblItemTypeMaster itm', 'itm.id = mig.ItemTypeID', 'left');

        return $this->db->get()->result_array();
		}
		
		// ItemDivision Table Data
		public function get_ItemDivision_data(){
			
			$this->db->select(db_prefix() . 'ItemsDivisionMaster.*');
			$this->db->from(db_prefix() . 'ItemsDivisionMaster');
			$this->db->order_by('id', 'ASC');
			return $this->db->get()->result_array();
		}
		
		// ItemGroup Table Data
		public function get_ItemGroup_data(){
			
			$this->db->select(db_prefix() . 'ItemsSubGroup1.*,'.db_prefix() . 'items_main_groups.name AS MainGroupName');
			$this->db->from(db_prefix() . 'ItemsSubGroup1');
			$this->db->join(db_prefix() . 'items_main_groups', '' . db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'ItemsSubGroup1.main_group_id');
			$this->db->order_by(db_prefix() . 'ItemsSubGroup1.id', 'ASC');
			return $this->db->get()->result_array();
		}
		// ItemSubGroup2 Table Data
		public function get_ItemSubGroup2_data(){
			
			$this->db->select(db_prefix() . 'ItemsSubGroup2.*,'.db_prefix() . 'ItemsSubGroup1.name AS SubGroup1Name,'.db_prefix() . 'items_main_groups.name AS MainGroupName');
			$this->db->from(db_prefix() . 'ItemsSubGroup2');
			$this->db->join(db_prefix() . 'ItemsSubGroup1', '' . db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'ItemsSubGroup2.sub_group_id1');
			$this->db->join(db_prefix() . 'items_main_groups', '' . db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'ItemsSubGroup2.main_group_id');
			$this->db->order_by(db_prefix() . 'ItemsSubGroup2.id', 'ASC');
			return $this->db->get()->result_array();
		}
		
		public function get_last_recordItemGroup(){
			$this->db->select('*');
			$this->db->from('ItemsSubGroup1');
			$this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
			$ItemGroupRecord =  $this->db->get()->row();
			return $ItemGroupRecord->id;
		}
		public function get_last_recordItemGroup2(){
			$this->db->select('*');
			$this->db->from('tblItemsSubGroup2');
			$this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
			$ItemGroupRecord =  $this->db->get()->row();
			return $ItemGroupRecord->id;
		}
		
		// MainItemGroup Table Data By ID
		public function getMainItemGroupDetails($ItemGroupID){
			
			$this->db->select(db_prefix() . 'items_main_groups.*');
			$this->db->from(db_prefix() . 'items_main_groups');
			$this->db->where(db_prefix() . 'items_main_groups.id', $ItemGroupID);
			$data = $this->db->get()->row();
			if(empty($data)){
				
				}else{
				$stockMonitor = $this->GetStockMonitor($ItemGroupID);
				$data->Stocks = $stockMonitor;
			}
			
			return $data;
		}
		
		// MinItemGroup Stock Monitor
		public function GetStockMonitor($ItemGroupID){
			
			$this->db->select(db_prefix() . 'items_main_groupsMonitor.*');
			$this->db->from(db_prefix() . 'items_main_groupsMonitor');
			$this->db->where(db_prefix() . 'items_main_groupsMonitor.GroupID', $ItemGroupID);
			$this->db->order_by(db_prefix() . 'items_main_groupsMonitor.PlantID', 'ASC');
			return $this->db->get()->result_array();
		}
		
		// Item Division Table Data By ID
		public function getitemDivisionDetails($ItemDivisionID){
			
			$this->db->select(db_prefix() . 'ItemsDivisionMaster.*');
			$this->db->from(db_prefix() . 'ItemsDivisionMaster');
			$this->db->where(db_prefix() . 'ItemsDivisionMaster.id', $ItemDivisionID);
			return $this->db->get()->row();
		}
		// Last Id For Item Division
		public function get_last_recordItemDevision(){
			$this->db->select('*');
			$this->db->from('ItemsDivisionMaster');
			$this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
			$ItemDivisionRecord =  $this->db->get()->row();
			return $ItemDivisionRecord->id;
		}
		
		// ItemGroup Table Data By ID
		public function getItemGroupDetails($ItemGroupID){
			
			$this->db->select(db_prefix() . 'ItemsSubGroup1.*');
			$this->db->from(db_prefix() . 'ItemsSubGroup1');
			$this->db->where(db_prefix() . 'ItemsSubGroup1.id', $ItemGroupID);
			return $this->db->get()->row();
		}
		// ItemSubGroup2 Table Data By ID
		public function getItemSubGroup2Details($ItemGroup2ID){
			
			$this->db->select(db_prefix() . 'ItemsSubGroup2.*');
			$this->db->from(db_prefix() . 'ItemsSubGroup2');
			$this->db->where(db_prefix() . 'ItemsSubGroup2.id', $ItemGroup2ID);
			
			$data = $this->db->get()->row();
			
			if(!empty($data)){
				$data->SubGroup1List = $this->GetSubgroup1Data($data->main_group_id);
			}
			return $data;
		}
		
		public function GetSubgroup1Data($MainGroupId)
		{
			$this->db->select(db_prefix() . 'ItemsSubGroup1.*');
			$this->db->where(db_prefix() . 'ItemsSubGroup1.main_group_id', $MainGroupId);
			$this->db->order_by(db_prefix() . 'ItemsSubGroup1.name', 'ASC');
			return $this->db->get('tblItemsSubGroup1')->result_array();
		}
		public function GetSubgroup1DataNew($filterdata ="")
		{
			$ItemType = $filterdata["ItemType"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			
			$this->db->select(db_prefix() . 'ItemsSubGroup1.*');
			$this->db->from(db_prefix() . 'items');
			$this->db->join(db_prefix() . 'ItemsSubGroup1', 'tblItemsSubGroup1.id = tblitems.SubGrpID1','INNER');
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			$this->db->where(db_prefix() . 'ItemsSubGroup1.main_group_id', $MainItemGroup);
			$this->db->order_by(db_prefix() . 'ItemsSubGroup1.name', 'ASC');
			$this->db->group_by(db_prefix() . 'ItemsSubGroup1.id');
			return $this->db->get()->result_array();
		}
		public function GetSubgroup2DataByMainGroup($MainGroup)
		{
			$this->db->select(db_prefix() . 'ItemsSubGroup2.*');
			$this->db->where(db_prefix() . 'ItemsSubGroup2.main_group_id', $MainGroup);
			$this->db->order_by(db_prefix() . 'ItemsSubGroup2.name', 'ASC');
			return $this->db->get('tblItemsSubGroup2')->result_array();
		}
		public function GetSubgroup2Data($SubGroup1)
		{
			$this->db->select(db_prefix() . 'ItemsSubGroup2.*');
			$this->db->where(db_prefix() . 'ItemsSubGroup2.sub_group_id1', $SubGroup1);
			$this->db->order_by(db_prefix() . 'ItemsSubGroup2.name', 'ASC');
			return $this->db->get('tblItemsSubGroup2')->result_array();
		}
		public function GetSubgroup2DataNew($filterdata ="")
		{
			$ItemType = $filterdata["ItemType"];
			$SubGroup1 = $filterdata["SubGroup1"];
			
			$this->db->select(db_prefix() . 'ItemsSubGroup2.*');
			$this->db->from(db_prefix() . 'items');
			$this->db->join(db_prefix() . 'ItemsSubGroup2', 'ItemsSubGroup2.id = tblitems.SubGrpID2','INNER');
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			$this->db->where(db_prefix() . 'ItemsSubGroup2.sub_group_id1', $SubGroup1);
			$this->db->group_by(db_prefix() . 'ItemsSubGroup2.id');
			$this->db->order_by(db_prefix() . 'ItemsSubGroup2.name', 'ASC');
			return $this->db->get()->result_array();
		}
		
		public function GetItemBySubgroup2Data($SubGroup2)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblitems.*');
			$this->db->where_in('SubGrpID2', $SubGroup2);
			$this->db->where('isactive', 'Y');
			$this->db->order_by('tblitems.description', 'ASC');
			return $this->db->get('tblitems')->result_array();
			
		}
		public function GetItemBySubgroup2DataNew($filterdata ="")
		{
			$ItemType = $filterdata["ItemType"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblitems.*');
			
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			
			$this->db->where_in('SubGrpID2', $SubGroup2);
			$this->db->where('isactive', 'Y');
			$this->db->order_by('tblitems.description', 'ASC');
			return $this->db->get('tblitems')->result_array();
			
		}
		public function GetSubGroup2ByGroupID($SubGroup1)
		{
			if(!empty($SubGroup1)){
				$this->db->select(db_prefix() . 'ItemsSubGroup2.*');
				$this->db->where_in(db_prefix() . 'ItemsSubGroup2.sub_group_id1', $SubGroup1);
				$this->db->order_by(db_prefix() . 'ItemsSubGroup2.name', 'ASC');
				return $this->db->get('tblItemsSubGroup2')->result_array();
				}else{
				return array();
			}
		}
		public function GetItemCodeByMainGroup($MainGrpID)
		{
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select(db_prefix() . 'items.*');
			$this->db->where(db_prefix() . 'items.MainGrpID', $MainGrpID);
			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);
			$this->db->order_by(db_prefix() . 'items.id', 'DESC');
			return $this->db->get('tblitems')->row();
		}
		
		public function GetGroupsWiseItemList($SubGroup,$SubGroup2)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$this->db->select('tblitems.*');
			$this->db->where_in('SubGrpID1', $SubGroup);
			$this->db->where_in('SubGrpID2', $SubGroup2);
			$this->db->where('isactive', 'Y');
			$this->db->order_by('tblitems.description', 'ASC');
			return $this->db->get('tblitems')->result_array();
			
		}
		public function GetPlantDetails()
		{   
			$selected_company = $this->session->userdata('root_company');
			$FY = $this->session->userdata('finacial_year');
			
			$sql ='SELECT '.db_prefix().'setup.*
			FROM '.db_prefix().'setup WHERE PlantID = '.$selected_company.' AND FY = "'.$FY.'"';
			$result = $this->db->query($sql)->row();
			return $result;
		}
		
		public function GetStateList()
		{
			$this->db->select('tblxx_statelist.*');
			$this->db->where('country_id', 1);
			$this->db->order_by('tblxx_statelist.state_name', 'ASC');
			return $this->db->get('tblxx_statelist')->result_array();
		}
		public function GetItemGroupList()
		{
			$this->db->select('tblItemsSubGroup1.*');
			// $this->db->where_in('main_group_id', ['2','3']);
			$this->db->order_by('tblItemsSubGroup1.name', 'ASC');
			return $this->db->get('tblItemsSubGroup1')->result_array();
		}
		
		
		public function TotalSKUCount()
		{  
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = ''.db_prefix().'items.PlantID = "'.$selected_company.'"';
			
			$sql1 .= '  GROUP BY '.db_prefix().'items.MainGrpID';
			
			$sql ='SELECT '.db_prefix().'items.MainGrpID,COUNT(*) as count
			FROM '.db_prefix().'items 
			WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		
		public function GetTopInventoryItem($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			
			$ItemCount = $filterdata["MaxCount"];
			$state = $filterdata["state"];
			$SubGroup = $filterdata["SubGroup"];
			$Items = $filterdata["Items"];
			$filterdata["from_date"] = $from_date;
			$filterdata["to_date"] = $to_date;
			$AllItemList = $this->GetItemList($filterdata);
			$AllOQtyList = $this->GetOQtyItemList($filterdata);
			$StockOQtyData = $this->get_item_open_qty($filterdata);
			$StockData = $this->GetStockData($filterdata);
			// echo "<pre>";print_r($StockOQtyData);die;
			$chart = [];
			
            $OQTYCasesSum = 0;
            $PurchQtyCasesSum = 0;
            $PurchRtnQtyCasesSum = 0;
            $IssueQtyCasesSum = 0;
            $PRDCasesSum = 0;
            $SalesCasesSum = 0;
            $SalesRtnCasesSum = 0;
            $AdjCasesSum = 0;
            $GOCasesSum = 0;
            $GICasesSum = 0;
            $BQtySum = 0;
            $stockValue_sum = 0;
            $SrNo = 1;
			foreach ($AllItemList as $key => $value) {
				$rate = 0;
				$OQTY = 0;
				$OQTYCases = 0;
				$PurchQty = 0;
				$PurchQtyCases = 0;
				
				$CaseQty = 1;
				
				$PurchRtnQty = 0;
				$PurchRtnQtyCases = 0;
				
				$IssueQty = 0;
				$IssueQtyCases = 0;
				
				$PRDQty = 0;
				$PRDCases = 0;
				
				$SalesQty = 0;
				$SalesCases = 0;
				
				$SalesRtnQty = 0;
				$SalesRtnCases = 0;
				
				$AdjQty = 0;
				$AdjCases = 0;
				
				$GOQty = 0;
				$GOCases = 0;
				
				$GIQty = 0;
				$GICases = 0;
				// if($value["item_code"] == '35'){
				// echo $OQty;die;
				// }
				foreach ($AllOQtyList as $OQtyKey1 => $OQtyVal1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($OQtyVal1["ItemID"]))){
						
						$OQTY += $OQtyVal1['OQty'];
					}
				}
				
				foreach ($StockData as $key1 => $value1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "P" && $value1["TType2"] == "Purchase"){
						$PurchQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "N" && $value1["TType2"] == "PurchaseReturn"){
						$PurchRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "A" && $value1["TType2"] == "Issue"){
						$IssueQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "B" && $value1["TType2"] == "Production"){
						$PRDQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "O" && $value1["TType2"] == "Order"){
						$SalesQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "R" && $value1["TType2"] == "Fresh")){
						$SalesRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "X" && $value1["TType2"] == "Free Distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Free distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Promotional Activity" || $value1["TType"] == "X" && $value1["TType2"] == "Stock Adjustment" || $value1["TType"] == "X" && $value1["TType2"] == "IssueAgainstReturn")){
						$AdjQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "Out")){
						$GOQty += $value1['BilledQty'];
						$GOValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "In")){
						$GIQty += $value1['BilledQty'];
						$GIValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
				}
				if($PurchQty !== '0'){
					$PurchQtyCases = floatval($PurchQty) / floatval($CaseQty);
					$PurchQtyCasesSum += $PurchQtyCases;
				}
				
				if($PurchRtnQty !== '0'){
					$PurchRtnQtyCases = floatval($PurchRtnQty) / floatval($CaseQty);
					$PurchRtnQtyCasesSum += $PurchRtnQtyCases;
				}
				
				if($IssueQty !== '0'){
					$IssueQtyCases = floatval($IssueQty) / floatval($CaseQty);
					$IssueQtyCasesSum += $IssueQtyCases;
				}
				
				if($PRDQty !== '0'){
					$PRDCases = floatval($PRDQty) / floatval($CaseQty);
					$PRDCasesSum += $PRDCases;
				}
				
				if($SalesQty !== '0'){
					$SalesCases = floatval($SalesQty) / floatval($CaseQty);
					$SalesCasesSum += $SalesCases;
				}
				
				if($SalesRtnQty !== '0'){
					$SalesRtnCases = floatval($SalesRtnQty) / floatval($CaseQty);
					$SalesRtnCasesSum += $SalesRtnCases;
				}
				
				if($AdjQty !== '0'){
					$AdjCases = floatval($AdjQty) / floatval($CaseQty);
					$AdjCasesSum += $AdjCases;
				}
				
				
				if($GOQty >0){
					$GOCases = floatval($GOQty) / floatval($CaseQty);
					$GOCasesSum += $GOCases;
				}
				
				if($GIQty >0){
					$GICases = floatval($GIQty) / floatval($CaseQty);
					$GICasesSum += $GICases;
				}
				$from_date_value = '20'.$fy.'-04-01';
				
				if($from_date == $from_date_value){
					$OQTYCases = floatval($OQTY) / floatval($CaseQty);
					
					}else{
					$OQtySum = 0;
					$OQtySum += floatval($OQTY);
					
					foreach ($StockOQtyData as $keyOQty => $valueOQty) {
						
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "P"  && $valueOQty['TType2'] == "Purchase"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "N"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "A" && $valueOQty['TType2'] == "Issue"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "B"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "O" && $valueOQty['TType2'] == "Order"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if((strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"])) && ($valueOQty['TType'] == "R" && $valueOQty["TType2"] == "Fresh")){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "X"){
							$OQtySum -= $valueOQty['billsum'];
						}
						
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "Out")){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "In")){
							$OQtySum += $valueOQty['billsum'];
						}
					}
					$OQTYCases = floatval($OQtySum) / floatval($CaseQty);
				}
				
				$OQTYCasesSum += $OQTYCases;
				$BQty =    $OQTYCases +  $PurchQtyCases - $PurchRtnQtyCases - $IssueQtyCases + $PRDCases - $SalesCases + $SalesRtnCases - $AdjCases  - $GOCases + $GICases;
				// echo $CaseQty;die;
				$BQtySum += $BQty;    
				if(floatval($OQTYCases) == '0.00' && floatval($PurchQtyCases) == "0.00" && floatval($PurchRtnQtyCases) == "0.00" && floatval($IssueQtyCases) == "0.00" && floatval($PRDCases) == "0.00" && floatval($SalesCases) == "0.00" && floatval($SalesRtnCases) == "0.00" && floatval($AdjCases) == "0.00" && floatval($GOCases) == "0.00" && floatval($GICases) == "0.00"){
					
					}else{
					
					
					if($value["assigned_rate"] == null || $value["assigned_rate"] == "" || $value["assigned_rate"] == "0.00"){
						//$rate = 0;
						}else{
						$rate = $value["assigned_rate"];
					}
					
					if($value["case_qty"] == '0' || $value["case_qty"] == ''){
						$stockqty = round($BQty) * 1;
						}else{
						$stockqty = round($BQty) * 1;
					}
					$stockValue = $stockqty * $rate;
					
					$stockValue_sum = $stockValue_sum + $stockValue;
					
				}
				
				
				array_push($chart, [
				'name' 		=> $value['description_name'],
				'y' 		=>	round((float)($BQty), 2),
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
			}
			// echo "<pre>";print_r($chart);die;
			
			
			// Sort the array by 'y' value in descending order
			usort($chart, function ($a, $b) {
				return $b['y'] <=> $a['y'];
			});
			
			// Get only the top $ItemCount elements
			$chart = array_slice($chart, 0, $ItemCount);
			
			$data = [
			'ChartData' => $chart,
			];
			
			return $data;
		}
		
		public function Purchase_VS_Sales($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			if(empty($filterdata["from_date"])){
				$from_date = date('Y-m-01');
				$to_date = date('Y-m-d');
				}else{
				$from_date = to_sql_date($filterdata["from_date"]);
				$to_date = to_sql_date($filterdata["to_date"]);
			}
			$ItemCount = $filterdata["MaxCount"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$Items = $filterdata["Items"];
			
			$chart = [];
			$Purchase = [];
			
			
			if($SubGroup){
			    $this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items.description as description_name,tblitems.SubGrpID1 AS SubGroupID');
				}else{
			    $this->db->select(db_prefix().'ItemsSubGroup1.id as ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'ItemsSubGroup1.name as description_name,tblItemsSubGroup1.id AS SubGroupID');
			}
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			
			if($SubGroup){
				
				}else{
			    $this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.TType ', 'P');
			$this->db->where('tblhistory.TType2 ', 'Purchase');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			$this->db->where('tblitems.MainGrpID','1');
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				$this->db->group_by('tblhistory.ItemID');
				}else{
			    $this->db->group_by('tblitems.SubGrpID1');
			}
			
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->order_by("total_qty", "DESC");
			
			$this->db->limit($ItemCount);
			$TopPurchase = $this->db->get('tblhistory')->result_array();
			$i=0;
			
			$SubGroup_arr = array();
			foreach ($TopPurchase as $key => $value) {
				array_push($SubGroup_arr,$value['ItemID']);
				array_push($Purchase, [
				'name' 		=> $value['description_name'],
				'y' 		=>	(int)$value['total_qty'],
				'z' 		=> 100,
				'label' 		=> "Qty"
				]);
				$i++;
			}
			$SubGroup_arr = array_unique($SubGroup_arr);
			if(count($SubGroup_arr)>0){
				// print_r($SubGroup_arr);die;
				if($SubGroup){
					$this->db->select(db_prefix().'history.ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'items.description as description_name');
					}else{
					$this->db->select(db_prefix().'ItemsSubGroup1.id as ItemID, SUM(BilledQty) as total_qty,'.db_prefix().'ItemsSubGroup1.name as description_name');
				}
				$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
				$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
				
				if($SubGroup){
					
					}else{
					$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
				}
				$this->db->where('tblhistory.PlantID',$selected_company);
				$this->db->where('tblhistory.FY',$fy);
				$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
				$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
				$this->db->where('tblhistory.TType ', 'O');
				$this->db->where('tblhistory.TType2 ', 'Order');
				$this->db->where('tblhistory.TransID IS NOT NULL');
				
				if($SubGroup){
					$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
					$this->db->where_in('tblhistory.ItemID', $SubGroup_arr);
					$this->db->group_by('tblhistory.ItemID');
					}else{
					$this->db->where_in('tblitems.SubGrpID1', $SubGroup_arr);
					$this->db->group_by('tblitems.SubGrpID1');
				}
				
				if($SubGroup2){
					$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
				}
				if($Items){
					$this->db->where_in('tblitems.item_code', $Items);
				}
				$this->db->order_by("total_qty", "DESC");
				
				$this->db->limit($ItemCount);
				$TopItem = $this->db->get(db_prefix().'history')->result_array();
				
				$i=0;
				foreach ($TopItem as $key => $value) {
					array_push($chart, [
					'name' 		=> $value['description_name'],
					'y' 		=>	(int)$value['total_qty'],
					'z' 		=> 100,
					'label' 		=> "Qty"
					]);
					$i++;
				}
			}
			
			
			
			$data = [
			'Sales' => $chart,
			'Purchase' => $Purchase,
			];
			
			return $data;
		}
		
		public function GetPurchaseRegisterDataItemWise($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			// $month_input = $filterdata['month']; // Example: '2024-11'
    		// $date = $month_input.'-01';//your given date
            // $first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");
            // $from_date = date("Y-m-d",$first_date_find);
            
            // $last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");
            // $last_date = date("Y-m-d",$last_date_find);
			
            // $Currentdate = date('Y-m-d');
            // if($last_date > $Currentdate){
			// $to_date = $Currentdate;
			// }else{
			// $to_date = $last_date;
			// }
			
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$ItemCount = $filterdata["MaxCount"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$Items = $filterdata["Items"];
			
			$Purchase = [];
			
			
			$this->db->select('tblhistory.OrderID,tblhistory.ItemID,tblhistory.mfg_date,tblhistory.expiry_date,DATE_FORMAT(tblhistory.Transdate, "%d-%b") as Transdate,tblclients.company,CONCAT(tblclients.company, " (", DATE_FORMAT(tblhistory.Transdate, "%d-%b"), ")") as company_with_date,tblpurchasemaster.Invoicedate,tblpurchasemaster.Invoiceno,tblhistory.PurchRate,SUM(tblhistory.BilledQty) as rcptqty,SUM(tblhistory.ChallanAmt) as amount,SUM(tblhistory.DiscAmt) as discamt,SUM(tblhistory.sgstamt) as sgstamt,SUM(tblhistory.cgstamt) as cgstamt,SUM(tblhistory.igstamt) as igstamt,SUM(tblhistory.ChallanAmt) as netamount,tblhistory.AccountID,tblitems.description ');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND '.db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'purchasemaster', 'tblpurchasemaster.PurchID = tblhistory.OrderID  AND '.db_prefix() . 'purchasemaster.PlantID = ' . db_prefix() . 'history.PlantID','INNER');
			$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->group_by('tblhistory.ItemID,tblhistory.OrderID');
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
			}
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->order_by("tblhistory.TransDate", "DESC");
			
			$this->db->limit($ItemCount);
			$TopPurchase = $this->db->get('tblhistory')->result_array();
			$i=0;
			
			foreach ($TopPurchase as $key => $value) {
				array_push($Purchase, [
				'name' 		=> $value['company_with_date'],
				'y' 		=>	(float)$value['PurchRate'],
				'z' 		=> 100,
				'label' 		=> "Rate"
				]);
				$i++;
			}
			
			
			
			
			$data = [
			'Purchase' => $Purchase,
			];
			
			return $data;
		}
		
		
		public function GetItemList($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = $filterdata["from_date"].' 00:00:00';
			$to_date = $filterdata["to_date"].' 23:59:59';
			
			if($filterdata["SubGroup"]){
			    $this->db->select(db_prefix().'items.item_code AS item_code,'.db_prefix().'items.description as description_name');
				}else{
			    $this->db->select(db_prefix().'ItemsSubGroup1.id as item_code,'.db_prefix().'ItemsSubGroup1.name as description_name');
			}
			if($filterdata["SubGroup"]){
				
				}else{
			    $this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			
			if($filterdata["MainGrpID"]){
				$this->db->where_in('tblitems.MainGrpID', $filterdata["MainGrpID"]);
			}
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["SubGroup2"]){
				$this->db->where_in('tblitems.SubGrpID2', $filterdata["SubGroup2"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			$this->db->where('tblitems.PlantID', $selected_company);
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
				$this->db->group_by('tblitems.item_code');
				}else{
			    $this->db->group_by('tblitems.SubGrpID1');
			}
			$this->db->order_by('description_name', 'ASC');
			return $this->db->get('tblitems')->result_array();
			
		}
		
		public function GetItemListStockValue($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$this->db->select(db_prefix().'items.item_code AS item_code,'.db_prefix().'items.description as description_name,tblitems.MainGrpID,tblrate_master.assigned_rate');
			$this->db->join(db_prefix() . 'rate_master', db_prefix() . 'rate_master.item_id = ' . db_prefix() . 'items.item_code  AND tblrate_master.PlantID = tblitems.PlantID AND tblrate_master.state_id = "UP" ');
			if($filterdata["MainGrpID"]){
				$this->db->where_in('tblitems.MainGrpID', $filterdata["MainGrpID"]);
			}
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["SubGroup2"]){
				$this->db->where_in('tblitems.SubGrpID2', $filterdata["SubGroup2"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			$this->db->where('tblitems.PlantID', $selected_company);
			
			$this->db->group_by('tblitems.item_code');
			
			$this->db->order_by('description_name', 'ASC');
			return $this->db->get('tblitems')->result_array();
			
		}
		public function GetOQtyItemList($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			if($filterdata["SubGroup"]){
			    $this->db->select('tblitems.item_code AS ItemID,'.db_prefix().'items.description as description_name,SUM(tblstockmaster.OQty) AS OQty');
				}else{
			    $this->db->select('tblItemsSubGroup1.id as ItemID,'.db_prefix().'ItemsSubGroup1.name as description_name,SUM(tblstockmaster.OQty) AS OQty');
			}
			
			$this->db->join('tblitems', 'tblitems.item_code = ' . db_prefix() . 'stockmaster.ItemID');
			if($filterdata["SubGroup"]){
				
				}else{
			    $this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			
			if($filterdata["MainGrpID"]){
				$this->db->where_in('tblitems.MainGrpID', $filterdata["MainGrpID"]);
			}
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["SubGroup2"]){
				$this->db->where_in('tblitems.SubGrpID2', $filterdata["SubGroup2"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			$this->db->where('tblitems.PlantID', $selected_company);
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
				$this->db->group_by('tblitems.item_code');
				}else{
			    $this->db->group_by('tblitems.SubGrpID1');
			}
			$this->db->order_by('description_name', 'ASC');
			return $this->db->get('tblstockmaster')->result_array();
			
		}
		public function GetOQtyItemListStockValue($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			
			$this->db->select('tblitems.item_code AS ItemID,'.db_prefix().'items.description as description_name,SUM(tblstockmaster.OQty) AS OQty');
			
			$this->db->join('tblitems', 'tblitems.item_code = ' . db_prefix() . 'stockmaster.ItemID');
			
			if($filterdata["MainGrpID"]){
				$this->db->where_in('tblitems.MainGrpID', $filterdata["MainGrpID"]);
			}
			if($filterdata["SubGroup"]){
				$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);
			}
			if($filterdata["SubGroup2"]){
				$this->db->where_in('tblitems.SubGrpID2', $filterdata["SubGroup2"]);
			}
			if($filterdata["Items"]){
				$this->db->where_in('tblitems.item_code', $filterdata["Items"]);
			}
			$this->db->where('tblitems.PlantID', $selected_company);
			
			$this->db->group_by('tblitems.item_code');
			
			$this->db->order_by('description_name', 'ASC');
			return $this->db->get('tblstockmaster')->result_array();
			
		}
		public function get_item_open_qty($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = $filterdata["from_date"];
			$to_date = $filterdata["to_date"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$MainGrpID = $filterdata["MainGrpID"];
			
			$from_date_value = '20'.$fy.'-04-01';
			
			if($from_date == $from_date_value){
				$day_before = $from_date_value;
				}else{
				$day_before = date( 'Y-m-d', strtotime( $to_date . ' -1 day' ) );
			}
			$first_date = $from_date_value;
			
			if($SubGroup){
			    $this->db->select(db_prefix().'history.ItemID,tblhistory.TType,tblhistory.TType2, SUM(BilledQty) as billsum,'.db_prefix().'items.description as description_name');
				}else{
			    $this->db->select(db_prefix().'ItemsSubGroup1.id as ItemID,tblhistory.TType,tblhistory.TType2, SUM(BilledQty) as billsum,'.db_prefix().'ItemsSubGroup1.name as description_name');
			}
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			
			if($SubGroup){
				
				}else{
			    $this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $first_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $day_before.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				$this->db->group_by('tblhistory.ItemID');
				}else{
			    $this->db->group_by('tblitems.SubGrpID1');
			}
			
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($MainGrpID){
				$this->db->where_in('tblitems.MainGrpID', $MainGrpID);
			}
			
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->order_by("billsum", "DESC");
			return $this->db->get(db_prefix().'history')->result_array();
			
		}
		public function get_item_open_qty_stockvalue($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = $filterdata["from_date"];
			$to_date = $filterdata["to_date"];
			$from_date_value = '20'.$fy.'-04-01';
			
			if($from_date == $from_date_value){
				$day_before = $from_date_value;
				}else{
				$day_before = date( 'Y-m-d', strtotime( $to_date . ' -1 day' ) );
			}
			$first_date = $from_date_value;
			
			$this->db->select(db_prefix().'history.ItemID,tblhistory.TType,tblhistory.TType2, SUM(BilledQty) as billsum,'.db_prefix().'items.description as description_name');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			
			
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $first_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $day_before.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			
			$this->db->group_by('tblhistory.ItemID');
			
			
			$this->db->order_by("billsum", "DESC");
			return $this->db->get(db_prefix().'history')->result_array();
			
		}
		public function GetStockData($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = $filterdata["from_date"];
			$to_date = $filterdata["to_date"];
			$SubGroup = $filterdata["SubGroup"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$MainGrpID = $filterdata["MainGrpID"];
			
			
			if($SubGroup){
			    $this->db->select(db_prefix().'history.ItemID,tblhistory.TType,tblhistory.TType2, tblhistory.BilledQty ,'.db_prefix().'items.description as description_name');
				}else{
			    $this->db->select(db_prefix().'ItemsSubGroup1.id as ItemID,,tblhistory.TType,tblhistory.TType2,tblhistory.BilledQty,'.db_prefix().'ItemsSubGroup1.name as description_name');
			}
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			
			if($SubGroup){
				
				}else{
			    $this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			
			if($SubGroup){
				$this->db->where_in('tblitems.SubGrpID1', $SubGroup);
				// $this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2');
				}else{
			    // $this->db->group_by('tblitems.SubGrpID1,tblhistory.TType,tblhistory.TType2');
			}
			if($SubGroup2){
				$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);
			}
			if($MainGrpID){
				$this->db->where_in('tblitems.MainGrpID', $MainGrpID);
			}
			if($Items){
				$this->db->where_in('tblitems.item_code', $Items);
			}
			$this->db->order_by("BilledQty", "DESC");
			return $this->db->get(db_prefix().'history')->result_array();
			
		}
		public function GetStockDataStockValue($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			$from_date = $filterdata["from_date"];
			$to_date = $filterdata["to_date"];
			
			
			$this->db->select(db_prefix().'history.ItemID,tblhistory.TType,tblhistory.TType2, tblhistory.BilledQty ,'.db_prefix().'items.description as description_name');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			
			$this->db->where('tblhistory.PlantID',$selected_company);
			$this->db->where('tblhistory.FY',$fy);
			$this->db->where('tblhistory.TransDate >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			
			
			$this->db->order_by("BilledQty", "DESC");
			return $this->db->get(db_prefix().'history')->result_array();
			
		}
		
		public function GetTopInventoryStockValue($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$filterdata["from_date"] = $from_date;
			$filterdata["to_date"] = $to_date;
			
			$AllItemList = $this->GetItemListStockValue($filterdata);
			$AllOQtyList = $this->GetOQtyItemListStockValue($filterdata);
			$StockOQtyData = $this->get_item_open_qty_stockvalue($filterdata);
			$StockData = $this->GetStockDataStockValue($filterdata);
			// echo "<pre>";print_r($StockData);die;
			$chart = [];
			
            $OQTYCasesSum = 0;
            $PurchQtyCasesSum = 0;
            $PurchRtnQtyCasesSum = 0;
            $IssueQtyCasesSum = 0;
            $PRDCasesSum = 0;
            $SalesCasesSum = 0;
            $SalesRtnCasesSum = 0;
            $AdjCasesSum = 0;
            $GOCasesSum = 0;
            $GICasesSum = 0;
            $BQtySum = 0;
            $stockValue_sum = 0;
			
			$finishGoodValue = 0;
			$RawMaterialValue = 0;
			$PackingMaterialValue = 0;
            $SrNo = 1;
			foreach ($AllItemList as $key => $value) {
				$rate = 0;
				$OQTY = 0;
				$OQTYCases = 0;
				$PurchQty = 0;
				$PurchQtyCases = 0;
				
				$CaseQty = 1;
				
				$PurchRtnQty = 0;
				$PurchRtnQtyCases = 0;
				
				$IssueQty = 0;
				$IssueQtyCases = 0;
				
				$PRDQty = 0;
				$PRDCases = 0;
				
				$SalesQty = 0;
				$SalesCases = 0;
				
				$SalesRtnQty = 0;
				$SalesRtnCases = 0;
				
				$AdjQty = 0;
				$AdjCases = 0;
				
				$GOQty = 0;
				$GOCases = 0;
				
				$GIQty = 0;
				$GICases = 0;
				// if($value["item_code"] == '35'){
				// echo $OQty;die;
				// }
				foreach ($AllOQtyList as $OQtyKey1 => $OQtyVal1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($OQtyVal1["ItemID"]))){
						
						$OQTY += $OQtyVal1['OQty'];
					}
				}
				
				foreach ($StockData as $key1 => $value1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "P" && $value1["TType2"] == "Purchase"){
						$PurchQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "N" && $value1["TType2"] == "PurchaseReturn"){
						$PurchRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "A" && $value1["TType2"] == "Issue"){
						$IssueQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "B" && $value1["TType2"] == "Production"){
						$PRDQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "O" && $value1["TType2"] == "Order"){
						$SalesQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "R" && $value1["TType2"] == "Fresh")){
						$SalesRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "X" && $value1["TType2"] == "Free Distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Free distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Promotional Activity" || $value1["TType"] == "X" && $value1["TType2"] == "Stock Adjustment" || $value1["TType"] == "X" && $value1["TType2"] == "IssueAgainstReturn")){
						$AdjQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "Out")){
						$GOQty += $value1['BilledQty'];
						$GOValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "In")){
						$GIQty += $value1['BilledQty'];
						$GIValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
				}
				if($PurchQty !== '0'){
					$PurchQtyCases = floatval($PurchQty) / floatval($CaseQty);
					$PurchQtyCasesSum += $PurchQtyCases;
				}
				
				if($PurchRtnQty !== '0'){
					$PurchRtnQtyCases = floatval($PurchRtnQty) / floatval($CaseQty);
					$PurchRtnQtyCasesSum += $PurchRtnQtyCases;
				}
				
				if($IssueQty !== '0'){
					$IssueQtyCases = floatval($IssueQty) / floatval($CaseQty);
					$IssueQtyCasesSum += $IssueQtyCases;
				}
				
				if($PRDQty !== '0'){
					$PRDCases = floatval($PRDQty) / floatval($CaseQty);
					$PRDCasesSum += $PRDCases;
				}
				
				if($SalesQty !== '0'){
					$SalesCases = floatval($SalesQty) / floatval($CaseQty);
					$SalesCasesSum += $SalesCases;
				}
				
				if($SalesRtnQty !== '0'){
					$SalesRtnCases = floatval($SalesRtnQty) / floatval($CaseQty);
					$SalesRtnCasesSum += $SalesRtnCases;
				}
				
				if($AdjQty !== '0'){
					$AdjCases = floatval($AdjQty) / floatval($CaseQty);
					$AdjCasesSum += $AdjCases;
				}
				
				
				if($GOQty >0){
					$GOCases = floatval($GOQty) / floatval($CaseQty);
					$GOCasesSum += $GOCases;
				}
				
				if($GIQty >0){
					$GICases = floatval($GIQty) / floatval($CaseQty);
					$GICasesSum += $GICases;
				}
				$from_date_value = '20'.$fy.'-04-01';
				
				if($from_date == $from_date_value){
					$OQTYCases = floatval($OQTY) / floatval($CaseQty);
					
					}else{
					$OQtySum = 0;
					$OQtySum += floatval($OQTY);
					
					foreach ($StockOQtyData as $keyOQty => $valueOQty) {
						
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "P"  && $valueOQty['TType2'] == "Purchase"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "N"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "A" && $valueOQty['TType2'] == "Issue"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "B"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "O" && $valueOQty['TType2'] == "Order"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if((strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"])) && ($valueOQty['TType'] == "R" && $valueOQty["TType2"] == "Fresh")){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "X"){
							$OQtySum -= $valueOQty['billsum'];
						}
						
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "Out")){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "In")){
							$OQtySum += $valueOQty['billsum'];
						}
					}
					$OQTYCases = floatval($OQtySum) / floatval($CaseQty);
				}
				
				$OQTYCasesSum += $OQTYCases;
				$BQty =    $OQTYCases +  $PurchQtyCases - $PurchRtnQtyCases - $IssueQtyCases + $PRDCases - $SalesCases + $SalesRtnCases - $AdjCases  - $GOCases + $GICases;
				// echo $CaseQty;die;
				$BQtySum += $BQty;    
				if(floatval($OQTYCases) == '0.00' && floatval($PurchQtyCases) == "0.00" && floatval($PurchRtnQtyCases) == "0.00" && floatval($IssueQtyCases) == "0.00" && floatval($PRDCases) == "0.00" && floatval($SalesCases) == "0.00" && floatval($SalesRtnCases) == "0.00" && floatval($AdjCases) == "0.00" && floatval($GOCases) == "0.00" && floatval($GICases) == "0.00"){
					
					}else{
					
					
					if($value["assigned_rate"] == null || $value["assigned_rate"] == "" || $value["assigned_rate"] == "0.00"){
						//$rate = 0;
						}else{
						$rate = $value["assigned_rate"];
					}
					
					if($value["case_qty"] == '0' || $value["case_qty"] == ''){
						$stockqty = round($BQty) * 1;
						}else{
						$stockqty = round($BQty) * $value["case_qty"];
					}
					$stockValue = $stockqty * $rate;
					
					$stockValue_sum = $stockValue_sum + $stockValue;
					// if($value['item_code'] == 'GFFG0148'){
					// echo $BQty;die;
					// }
					if($value['MainGrpID'] == '1'){
						$finishGoodValue = $finishGoodValue + $stockValue;
					}
					if($value['MainGrpID'] == '2'){
						$RawMaterialValue  = $RawMaterialValue + $stockValue;
					}
					if($value['MainGrpID'] == '3'){
						$PackingMaterialValue = $PackingMaterialValue + $stockValue;
					}
					
				}
				
				
				// array_push($chart, [
				// 'name' 		=> $value['description_name'],
				// 'y' 		=>	round((float)($BQty), 2),
				// 'z' 		=> 100,
				// 'label' 		=> "Qty"
				// ]);
			}
			// echo "<pre>";print_r($finishGoodValue);die;
			
			
			// Sort the array by 'y' value in descending order
			// usort($chart, function ($a, $b) {
			// return $b['y'] <=> $a['y'];
			// });
			
			
			$data = [
			'FinishGoodValue' => $finishGoodValue,
			'RawMaterialValue' => $RawMaterialValue,
			'PackingMaterialValue' => $PackingMaterialValue,
			];
			
			return $data;
		}
		
		public function GetOutOfStockInventory($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$filterdata["from_date"] = $from_date;
			$filterdata["to_date"] = $to_date;
			
			$AllItemList = $this->GetItemListStockValue($filterdata);
			$AllOQtyList = $this->GetOQtyItemListStockValue($filterdata);
			$StockOQtyData = $this->get_item_open_qty_stockvalue($filterdata);
			$StockData = $this->GetStockDataStockValue($filterdata);
			// echo "<pre>";print_r($StockData);die;
			$chart = [];
			
            $OQTYCasesSum = 0;
            $PurchQtyCasesSum = 0;
            $PurchRtnQtyCasesSum = 0;
            $IssueQtyCasesSum = 0;
            $PRDCasesSum = 0;
            $SalesCasesSum = 0;
            $SalesRtnCasesSum = 0;
            $AdjCasesSum = 0;
            $GOCasesSum = 0;
            $GICasesSum = 0;
            $BQtySum = 0;
            $stockValue_sum = 0;
			
			$finishGoodValue = 0;
			$RawMaterialValue = 0;
			$PackingMaterialValue = 0;
            $SrNo = 1;
			foreach ($AllItemList as $key => $value) {
				$rate = 0;
				$OQTY = 0;
				$OQTYCases = 0;
				$PurchQty = 0;
				$PurchQtyCases = 0;
				
				$CaseQty = 1;
				
				$PurchRtnQty = 0;
				$PurchRtnQtyCases = 0;
				
				$IssueQty = 0;
				$IssueQtyCases = 0;
				
				$PRDQty = 0;
				$PRDCases = 0;
				
				$SalesQty = 0;
				$SalesCases = 0;
				
				$SalesRtnQty = 0;
				$SalesRtnCases = 0;
				
				$AdjQty = 0;
				$AdjCases = 0;
				
				$GOQty = 0;
				$GOCases = 0;
				
				$GIQty = 0;
				$GICases = 0;
				// if($value["item_code"] == '35'){
				// echo $OQty;die;
				// }
				foreach ($AllOQtyList as $OQtyKey1 => $OQtyVal1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($OQtyVal1["ItemID"]))){
						
						$OQTY += $OQtyVal1['OQty'];
					}
				}
				
				foreach ($StockData as $key1 => $value1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "P" && $value1["TType2"] == "Purchase"){
						$PurchQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "N" && $value1["TType2"] == "PurchaseReturn"){
						$PurchRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "A" && $value1["TType2"] == "Issue"){
						$IssueQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "B" && $value1["TType2"] == "Production"){
						$PRDQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "O" && $value1["TType2"] == "Order"){
						$SalesQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "R" && $value1["TType2"] == "Fresh")){
						$SalesRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "X" && $value1["TType2"] == "Free Distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Free distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Promotional Activity" || $value1["TType"] == "X" && $value1["TType2"] == "Stock Adjustment" || $value1["TType"] == "X" && $value1["TType2"] == "IssueAgainstReturn")){
						$AdjQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "Out")){
						$GOQty += $value1['BilledQty'];
						$GOValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "In")){
						$GIQty += $value1['BilledQty'];
						$GIValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
				}
				if($PurchQty !== '0'){
					$PurchQtyCases = floatval($PurchQty) / floatval($CaseQty);
					$PurchQtyCasesSum += $PurchQtyCases;
				}
				
				if($PurchRtnQty !== '0'){
					$PurchRtnQtyCases = floatval($PurchRtnQty) / floatval($CaseQty);
					$PurchRtnQtyCasesSum += $PurchRtnQtyCases;
				}
				
				if($IssueQty !== '0'){
					$IssueQtyCases = floatval($IssueQty) / floatval($CaseQty);
					$IssueQtyCasesSum += $IssueQtyCases;
				}
				
				if($PRDQty !== '0'){
					$PRDCases = floatval($PRDQty) / floatval($CaseQty);
					$PRDCasesSum += $PRDCases;
				}
				
				if($SalesQty !== '0'){
					$SalesCases = floatval($SalesQty) / floatval($CaseQty);
					$SalesCasesSum += $SalesCases;
				}
				
				if($SalesRtnQty !== '0'){
					$SalesRtnCases = floatval($SalesRtnQty) / floatval($CaseQty);
					$SalesRtnCasesSum += $SalesRtnCases;
				}
				
				if($AdjQty !== '0'){
					$AdjCases = floatval($AdjQty) / floatval($CaseQty);
					$AdjCasesSum += $AdjCases;
				}
				
				
				if($GOQty >0){
					$GOCases = floatval($GOQty) / floatval($CaseQty);
					$GOCasesSum += $GOCases;
				}
				
				if($GIQty >0){
					$GICases = floatval($GIQty) / floatval($CaseQty);
					$GICasesSum += $GICases;
				}
				$from_date_value = '20'.$fy.'-04-01';
				
				if($from_date == $from_date_value){
					$OQTYCases = floatval($OQTY) / floatval($CaseQty);
					
					}else{
					$OQtySum = 0;
					$OQtySum += floatval($OQTY);
					
					foreach ($StockOQtyData as $keyOQty => $valueOQty) {
						
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "P"  && $valueOQty['TType2'] == "Purchase"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "N"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "A" && $valueOQty['TType2'] == "Issue"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "B"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "O" && $valueOQty['TType2'] == "Order"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if((strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"])) && ($valueOQty['TType'] == "R" && $valueOQty["TType2"] == "Fresh")){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "X"){
							$OQtySum -= $valueOQty['billsum'];
						}
						
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "Out")){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "In")){
							$OQtySum += $valueOQty['billsum'];
						}
					}
					$OQTYCases = floatval($OQtySum) / floatval($CaseQty);
				}
				
				$OQTYCasesSum += $OQTYCases;
				$BQty =    $OQTYCases +  $PurchQtyCases - $PurchRtnQtyCases - $IssueQtyCases + $PRDCases - $SalesCases + $SalesRtnCases - $AdjCases  - $GOCases + $GICases;
				// echo $CaseQty;die;
				$BQtySum += $BQty;    
				// if(floatval($OQTYCases) == '0.00' && floatval($PurchQtyCases) == "0.00" && floatval($PurchRtnQtyCases) == "0.00" && floatval($IssueQtyCases) == "0.00" && floatval($PRDCases) == "0.00" && floatval($SalesCases) == "0.00" && floatval($SalesRtnCases) == "0.00" && floatval($AdjCases) == "0.00" && floatval($GOCases) == "0.00" && floatval($GICases) == "0.00"){
				
				// }else{
				
				
				if($value["assigned_rate"] == null || $value["assigned_rate"] == "" || $value["assigned_rate"] == "0.00"){
					//$rate = 0;
					}else{
					$rate = $value["assigned_rate"];
				}
				
				if($value["case_qty"] == '0' || $value["case_qty"] == ''){
					$stockqty = round($BQty) * 1;
					}else{
					$stockqty = round($BQty) * $value["case_qty"];
				}
				$stockValue = $stockqty * $rate;
				
				$stockValue_sum = $stockValue_sum + $stockValue;
				// if($value['item_code'] == 'GFFG0148'){
				// echo $BQty;die;
				// }
				if($BQty <= 0){
					if($value['MainGrpID'] == '1'){
						
						$finishGoodValue++;
					}
					if($value['MainGrpID'] == '2'){
						$RawMaterialValue++;
					}
					if($value['MainGrpID'] == '3'){
						$PackingMaterialValue++;
					}
				}
				// }
				
				
			}
			// echo "<pre>";print_r($finishGoodValue);die;
			
			
			// Sort the array by 'y' value in descending order
			// usort($chart, function ($a, $b) {
			// return $b['y'] <=> $a['y'];
			// });
			
			
			$data = [
			'FinishGoodValue' => $finishGoodValue,
			'RawMaterialValue' => $RawMaterialValue,
			'PackingMaterialValue' => $PackingMaterialValue,
			];
			
			return $data;
		}
		public function GetLowStockInventory($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$filterdata["from_date"] = $from_date;
			$filterdata["to_date"] = $to_date;
			
			$AllItemList = $this->GetItemListStockValue($filterdata);
			$AllOQtyList = $this->GetOQtyItemListStockValue($filterdata);
			$StockOQtyData = $this->get_item_open_qty_stockvalue($filterdata);
			$StockData = $this->GetStockDataStockValue($filterdata);
			// echo "<pre>";print_r($StockData);die;
			$chart = [];
			
            $OQTYCasesSum = 0;
            $PurchQtyCasesSum = 0;
            $PurchRtnQtyCasesSum = 0;
            $IssueQtyCasesSum = 0;
            $PRDCasesSum = 0;
            $SalesCasesSum = 0;
            $SalesRtnCasesSum = 0;
            $AdjCasesSum = 0;
            $GOCasesSum = 0;
            $GICasesSum = 0;
            $BQtySum = 0;
            $stockValue_sum = 0;
			
			$finishGoodValue = 0;
			$RawMaterialValue = 0;
			$PackingMaterialValue = 0;
            $SrNo = 1;
			foreach ($AllItemList as $key => $value) {
				$rate = 0;
				$OQTY = 0;
				$OQTYCases = 0;
				$PurchQty = 0;
				$PurchQtyCases = 0;
				
				$CaseQty = 1;
				
				$PurchRtnQty = 0;
				$PurchRtnQtyCases = 0;
				
				$IssueQty = 0;
				$IssueQtyCases = 0;
				
				$PRDQty = 0;
				$PRDCases = 0;
				
				$SalesQty = 0;
				$SalesCases = 0;
				
				$SalesRtnQty = 0;
				$SalesRtnCases = 0;
				
				$AdjQty = 0;
				$AdjCases = 0;
				
				$GOQty = 0;
				$GOCases = 0;
				
				$GIQty = 0;
				$GICases = 0;
				// if($value["item_code"] == '35'){
				// echo $OQty;die;
				// }
				foreach ($AllOQtyList as $OQtyKey1 => $OQtyVal1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($OQtyVal1["ItemID"]))){
						
						$OQTY += $OQtyVal1['OQty'];
					}
				}
				
				foreach ($StockData as $key1 => $value1) {
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "P" && $value1["TType2"] == "Purchase"){
						$PurchQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "N" && $value1["TType2"] == "PurchaseReturn"){
						$PurchRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "A" && $value1["TType2"] == "Issue"){
						$IssueQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "B" && $value1["TType2"] == "Production"){
						$PRDQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && $value1["TType"] == "O" && $value1["TType2"] == "Order"){
						$SalesQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "R" && $value1["TType2"] == "Fresh")){
						$SalesRtnQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "X" && $value1["TType2"] == "Free Distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Free distribution" || $value1["TType"] == "X" && $value1["TType2"] == "Promotional Activity" || $value1["TType"] == "X" && $value1["TType2"] == "Stock Adjustment" || $value1["TType"] == "X" && $value1["TType2"] == "IssueAgainstReturn")){
						$AdjQty += $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "Out")){
						$GOQty += $value1['BilledQty'];
						$GOValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
					if(trim(strtoupper($value["item_code"])) == trim(strtoupper($value1["ItemID"])) && ($value1["TType"] == "T" && $value1["TType2"] == "In")){
						$GIQty += $value1['BilledQty'];
						$GIValueSum += $value1["SaleRate"] * $value1['BilledQty'];
						if($value1["SaleRate"] !== '' || $value1["SaleRate"] !== null){
							$rate = $value1["SaleRate"];
						}
					}
				}
				if($PurchQty !== '0'){
					$PurchQtyCases = floatval($PurchQty) / floatval($CaseQty);
					$PurchQtyCasesSum += $PurchQtyCases;
				}
				
				if($PurchRtnQty !== '0'){
					$PurchRtnQtyCases = floatval($PurchRtnQty) / floatval($CaseQty);
					$PurchRtnQtyCasesSum += $PurchRtnQtyCases;
				}
				
				if($IssueQty !== '0'){
					$IssueQtyCases = floatval($IssueQty) / floatval($CaseQty);
					$IssueQtyCasesSum += $IssueQtyCases;
				}
				
				if($PRDQty !== '0'){
					$PRDCases = floatval($PRDQty) / floatval($CaseQty);
					$PRDCasesSum += $PRDCases;
				}
				
				if($SalesQty !== '0'){
					$SalesCases = floatval($SalesQty) / floatval($CaseQty);
					$SalesCasesSum += $SalesCases;
				}
				
				if($SalesRtnQty !== '0'){
					$SalesRtnCases = floatval($SalesRtnQty) / floatval($CaseQty);
					$SalesRtnCasesSum += $SalesRtnCases;
				}
				
				if($AdjQty !== '0'){
					$AdjCases = floatval($AdjQty) / floatval($CaseQty);
					$AdjCasesSum += $AdjCases;
				}
				
				
				if($GOQty >0){
					$GOCases = floatval($GOQty) / floatval($CaseQty);
					$GOCasesSum += $GOCases;
				}
				
				if($GIQty >0){
					$GICases = floatval($GIQty) / floatval($CaseQty);
					$GICasesSum += $GICases;
				}
				$from_date_value = '20'.$fy.'-04-01';
				
				if($from_date == $from_date_value){
					$OQTYCases = floatval($OQTY) / floatval($CaseQty);
					
					}else{
					$OQtySum = 0;
					$OQtySum += floatval($OQTY);
					
					foreach ($StockOQtyData as $keyOQty => $valueOQty) {
						
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "P"  && $valueOQty['TType2'] == "Purchase"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "N"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "A" && $valueOQty['TType2'] == "Issue"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "B"){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "O" && $valueOQty['TType2'] == "Order"){
							$OQtySum -= $valueOQty['billsum'];
						}
						if((strtoupper($valueOQty['ItemID']) == strtoupper($value["item_code"])) && ($valueOQty['TType'] == "R" && $valueOQty["TType2"] == "Fresh")){
							$OQtySum += $valueOQty['billsum'];
						}
						if(trim(strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"])) && $valueOQty['TType'] == "X"){
							$OQtySum -= $valueOQty['billsum'];
						}
						
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "Out")){
							$OQtySum -= $valueOQty['billsum'];
						}
						if(trim((strtoupper($valueOQty['ItemID'])) == trim(strtoupper($value["item_code"]))) && ($valueOQty['TType'] == "T" && $valueOQty["TType2"] == "In")){
							$OQtySum += $valueOQty['billsum'];
						}
					}
					$OQTYCases = floatval($OQtySum) / floatval($CaseQty);
				}
				
				$BQty =    $OQTYCases +  $PurchQtyCases - $PurchRtnQtyCases - $IssueQtyCases + $PRDCases - $SalesCases + $SalesRtnCases - $AdjCases  - $GOCases + $GICases;
				// echo $OQTYCases;die;  
				
				// if($value["item_code"] == "GFFG0072"){
				// echo $OQTYCases;die;
				// }
				if($BQty > 0 && $BQty < 15){
					if($value['MainGrpID'] == '1'){
						
						$finishGoodValue++;
					}
					if($value['MainGrpID'] == '2'){
						$RawMaterialValue++;
					}
					if($value['MainGrpID'] == '3'){
						$PackingMaterialValue++;
					}
				}
				
				
			}
			
			$data = [
			'FinishGoodValue' => $finishGoodValue,
			'RawMaterialValue' => $RawMaterialValue,
			'PackingMaterialValue' => $PackingMaterialValue,
			];
			
			return $data;
		}
		
		public function GetAllPartyList()
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->select(db_prefix() . 'clients.*');
			$this->db->order_by(db_prefix() . 'clients.company', 'ASC');
			return $this->db->get('tblclients')->result_array();
		}
		
		public function GetGodownData()
		{
			$PlantID = $this->session->userdata('root_company');
			$this->db->where('PlantID', $PlantID);
			$this->db->order_by(db_prefix() . 'godownmaster.Type,'.db_prefix() . 'godownmaster.AccountID', 'ASC');
			return $this->db->get(db_prefix().'godownmaster')->result_array();
		}
		
		public function GetPartyWiseCrateBalace($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$Route = $filterdata["Route"];
			$AccountID = $filterdata["AccountID"];
			
			
			$CratesData = $this->GetCratesByDate($filterdata);
			$DayBeforeTransaction = $this->DayBeforeTransactionCrate($filterdata);
			$OpenCrates = $this->GetOpeningCrates($filterdata);
			
			$chart = [];
			
			$this->db->select(db_prefix() . 'clients.*');
			$this->db->where('tblclients.SubActGroupID1 ', '100056');
			if(!empty($AccountID)){
				$this->db->where('tblclients.AccountID ', $AccountID);
			}
			$this->db->order_by(db_prefix() . 'clients.company', 'ASC');
			$Clients = $this->db->get('tblclients')->result_array();
			
			foreach($Clients as $Client){
				
				$Party = $Client['company'];
				$InCrate = 0;
				$OutCrate = 0;
				$BeforeOutCrate = 0;
				$BeforeInCrate = 0;
				$OpeningCrate = 0;
				
				foreach($DayBeforeTransaction as $Transaction){
					if($Transaction['AccountID'] == $Client['AccountID']){
						if($Transaction['TType'] == 'D'){
							$BeforeOutCrate += $Transaction['Qty'];
						}
						if($Transaction['TType'] == 'C'){
							$BeforeInCrate += $Transaction['Qty'];
						}
					}
				}
				$BeforeCrate = $BeforeOutCrate - $BeforeInCrate;
				
				
				foreach($OpenCrates as $OpeningCrates){
					if($OpeningCrates['AccountID'] == $Client['AccountID']){
						if($OpeningCrates['TType'] == 'D'){
							$OpeningCrate += $OpeningCrates['Qty'];
						}
						if($OpeningCrates['TType'] == 'C'){
							$OpeningCrate -= $OpeningCrates['Qty'];
						}
					}
				}
				$OpenCrate = $OpeningCrate + $BeforeCrate;
				
				foreach($CratesData as $Crates){
					if($Crates['AccountID'] == $Client['AccountID']){
						if($Crates['TType'] == 'D'){
							$OutCrate += $Crates['Qty'];
						}
						if($Crates['TType'] == 'C'){
							$InCrate += $Crates['Qty'];
						}
					}
				}
				
				$BalanceCrate = $OpenCrate + $OutCrate - $InCrate;
				
				array_push($chart, [
				'name'  => $Party,                 // Party name
				'y'     => (int)$BalanceCrate,     // Balance crates
				'z'     => 100,                    // optional, for bubble chart if needed
				'label' => "Balance"
				]);
				
				$i++;
			}
			
			usort($chart, function ($a, $b) {
				return $b['y'] <=> $a['y'];
			});
			
			// 🔹 Get only top 20
			$chart = array_slice($chart, 0, 20);
			
			return $chart;
		}
		
		public function GetAllDailyTransactionChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$GodownID = $filterdata["GodownID"];
			$ItemType = $filterdata["ItemType"];
			
			$this->db->select('SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2,COALESCE(NULLIF(tblitems.case_qty, 0), 1) AS AvgCaseQty');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID ', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			$this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2');
			$Transaction = $this->db->get('tblhistory')->result_array();
			// echo "<pre>";print_r($Transaction);die;
			$Production = 0;
			$Sale = 0;
			$Purchase = 0;
			$Issue = 0;
			$FreshRtn = 0;
			$DamageRtn = 0;
			
			foreach($Transaction as $each){
				if($each['TType'] == 'B' && $each['TType2'] == 'Production'){
					$Production += $each['BilledQty'] / $each['AvgCaseQty'];
				}
				if($each['TType'] == 'O' && $each['TType2'] == 'Order'){
					$Sale += $each['BilledQty'] / $each['AvgCaseQty'];
				}
				if($each['TType'] == 'P' && $each['TType2'] == 'Purchase'){
					$Purchase += $each['BilledQty'] / $each['AvgCaseQty'];
				}
				if($each['TType'] == 'A' && $each['TType2'] == 'Issue'){
					$Issue += $each['BilledQty'] / $each['AvgCaseQty'];
				}
				if($each['TType'] == 'R' && $each['TType2'] == 'Fresh'){
					$FreshRtn += $each['BilledQty'] / $each['AvgCaseQty'];
				}
				if($each['TType'] == 'R' && $each['TType2'] == 'Damage'){
					$DamageRtn += $each['BilledQty'] / $each['AvgCaseQty'];
				}
			}
			
			$chart = [];
			
			$TransTypes = [
			'Sale'      => $Sale,
			'Purchase'   => $Purchase,
			'Production' => $Production,
			'Issue'     => $Issue,
			'Fresh Return'    => $FreshRtn,
			'Damage Return'      => $DamageRtn,
			];
			// print_r($TransTypes);die;
			foreach ($TransTypes as $name => $value) {
				$chart[] = [
				'name'  => $name,
				'y'     => (float)$value,
				'z'     => 100,
				'label' => "Total"
				];
			}
			return $chart;
		}
		public function GetTopStockLevelChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = (2000 + (int)$fy) . '-04-01';
			$to_date = to_sql_date($filterdata["to_date"]);
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$GodownID = $filterdata["GodownID"];
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblItemsSubGroup1.id as ItemID,tblItemsSubGroup1.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblItemsSubGroup2.id as ItemID,tblItemsSubGroup2.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,tblitems.case_qty AS CaseQty');
			}
			$this->db->from(db_prefix() . 'items');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblitems.item_code', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
			}
			
			$this->db->order_by('ItemName', 'ASC');
			$Group = $this->db->get()->result_array();
			// echo $this->db->last_query();die;
			// echo "<pre>";print_r($Group);die;
			// Opening Stock Query Start
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblItemsSubGroup1.id as ItemID,tblItemsSubGroup1.name as ItemName,SUM(OQty) AS OQty');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblItemsSubGroup2.id as ItemID,tblItemsSubGroup2.name as ItemName,SUM(OQty) AS OQty');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(OQty) AS OQty');
			}
			$this->db->from(db_prefix() . 'stockmaster');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'stockmaster.ItemID');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblitems.item_code', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblstockmaster.GodownID', $GodownID);
			}
			$this->db->where('tblstockmaster.FY', $fy);
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
				}elseif (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
				}else{
				$this->db->group_by(db_prefix() . 'items.item_code');
			}
			
			$this->db->order_by('ItemName', 'ASC');
			$OpeningStock = $this->db->get()->result_array();
			
			
			// echo "<pre>";print_r($OpeningStock);die;
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblItemsSubGroup1.id as ItemID,tblItemsSubGroup1.name as ItemName,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblItemsSubGroup2.id as ItemID,tblItemsSubGroup2.name as ItemName,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
			}
			$this->db->from('tblhistory');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID1,tblhistory.TType,tblhistory.TType2');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID2,tblhistory.TType,tblhistory.TType2');
				}else{
				$this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2');
			}
			$Transaction = $this->db->get()->result_array();
			// echo "<pre>";print_r($Transaction);die;
			
			
			$chart = [];
			
			foreach($Group as $key1 => $value1){
				if($value1["CaseQty"] == "0"){
					$CaseQty = 1;
					}else{
					$CaseQty = $value1["CaseQty"];
				}
				$OQty = 0;
				foreach($OpeningStock as $key2 => $value2){
					if($value1['ItemID'] ==  $value2['ItemID']){
						$OQty = $value2['OQty'];
					}
				}
				
				$PurchQty = 0;
				$PurchRtnQty = 0;
				$IssueQty = 0;
				$PRDQty = 0;
				$SalesQty = 0;
				$SalesRtnQty = 0;
				$AdjQty = 0;
				$GOQty = 0;
				$GIQty = 0;
				foreach ($Transaction as $key3 => $value3) {
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "P" && $value3["TType2"] == "Purchase"){
						$PurchQty += $value3['BilledQty'];
						
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "N" && $value3["TType2"] == "PurchaseReturn"){
						$PurchRtnQty += $value3['BilledQty'];
						
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "A" && $value3["TType2"] == "Issue"){
						$IssueQty += $value3['BilledQty'];
						
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "B" && $value3["TType2"] == "Production"){
						$PRDQty += $value3['BilledQty'];
						
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "O" && $value3["TType2"] == "Order"){
						$SalesQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "R" && $value3["TType2"] == "Fresh")){
						$SalesRtnQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "X" && $value3["TType2"] == "Free Distribution" || $value3["TType"] == "X" && $value3["TType2"] == "Free distribution" || $value3["TType"] == "X" && $value3["TType2"] == "Promotional Activity" || $value3["TType"] == "X" && $value3["TType2"] == "Stock Adjustment" || $value3["TType"] == "X" && $value3["TType2"] == "IssueAgainstReturn")){
						$AdjQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "T" && $value3["TType2"] == "Out")){
						$GOQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "T" && $value3["TType2"] == "In")){
						$GIQty += $value3['BilledQty'];
					}
				}
				$BQty =    $OQty +  $PurchQty - $PurchRtnQty - $IssueQty + $PRDQty - $SalesQty + $SalesRtnQty - $AdjQty  - $GOQty + $GIQty;
				
				$BQtyCases = floatval($BQty)/floatval($CaseQty);
				
				if($BQtyCases > 0){
					$chart[] = [
					'name'  => $value1["ItemName"],
					'y'     => (float)$BQtyCases,
					'z'     => 100,
					'label' => "Total"
					];
				}
			}
			
			usort($chart, function ($a, $b) {
				return $b['y'] <=> $a['y'];
			});
			
			// 🔹 Get only top 20
			$chart = array_slice($chart, 0, 20);
			
			return $chart;
		}
		public function GetTopMonthlyStockLevelChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = (2000 + (int)$fy) . '-04-01';
			$to_date = date('Y-m-t');
			$start_year = 2000 + $fy; 
			$end_year   = $start_year + 1;
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$GodownID = $filterdata["GodownID"];
			
			
			$Months = [];
			$currentYear = date('Y');
			$currentMonth = date('n'); // numeric month without leading zero, e.g. 9 for September
			
			// Months from April to December of the start year
			for ($i = 4; $i <= 12; $i++) {
				if ($start_year > $currentYear || ($start_year == $currentYear && $i > $currentMonth)) {
					break;
				}
				$date = "$start_year-$i-01";
				$Months[] = date("M-Y", strtotime($date));
			}
			
			// Months from January to March of the end year
			for ($i = 1; $i <= 3; $i++) {
				if ($end_year > $currentYear || ($end_year == $currentYear && $i > $currentMonth)) {
					break;
				}
				$date = "$end_year-$i-01";
				$Months[] = date("M-Y", strtotime($date));
			}
			
			
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblItemsSubGroup1.id as ItemID,tblItemsSubGroup1.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblItemsSubGroup2.id as ItemID,tblItemsSubGroup2.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,tblitems.case_qty AS CaseQty');
			}
			$this->db->from(db_prefix() . 'items');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblitems.item_code', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
			}
			
			$this->db->order_by('ItemName', 'ASC');
			$Group = $this->db->get()->result_array();
			
			// Opening Stock Query Start
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('tblItemsSubGroup1.id as ItemID,tblItemsSubGroup1.name as ItemName,SUM(OQty) AS OQty');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('tblItemsSubGroup2.id as ItemID,tblItemsSubGroup2.name as ItemName,SUM(OQty) AS OQty');
				}else{
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(OQty) AS OQty');
			}
			$this->db->from(db_prefix() . 'stockmaster');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'stockmaster.ItemID');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblitems.item_code', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblstockmaster.GodownID', $GodownID);
			}
			$this->db->where('tblstockmaster.FY', $fy);
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
				}elseif (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
				}else{
				$this->db->group_by(db_prefix() . 'items.item_code');
			}
			
			$this->db->order_by('ItemName', 'ASC');
			$OpeningStock = $this->db->get()->result_array();
			
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%b-%Y") as month,tblItemsSubGroup1.id as ItemID,tblItemsSubGroup1.name as ItemName,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%b-%Y") as month,tblItemsSubGroup2.id as ItemID,tblItemsSubGroup2.name as ItemName,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
				}else{
				$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%b-%Y") as month,tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
			}
			$this->db->from('tblhistory');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			
			$this->db->group_by("YEAR(tblhistory.TransDate2), MONTH(tblhistory.TransDate2)");
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID1,tblhistory.TType,tblhistory.TType2');
				}elseif(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->group_by(db_prefix() . 'items.SubGrpID2,tblhistory.TType,tblhistory.TType2');
				}else{
				$this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2');
			}
			$Transaction = $this->db->get()->result_array();
			
			
			$chart = [];
			$groupData = [];
			
			foreach ($Group as $key1 => $value1) {
				
				$groupId = $value1['ItemID'];
				$groupName = $value1['ItemName'];
				$CaseQty = ($value1["CaseQty"] == "0") ? 1 : $value1["CaseQty"];
				
				// Find opening stock for this item
				$OQty = 0;
				foreach ($OpeningStock as $key2 => $value2) {
					if ($value1['ItemID'] == $value2['ItemID']) {
						$OQty = $value2['OQty'];
						break;
					}
				}
				
				// Filter transactions for this item
				$itemTrans = array_filter($Transaction, function($tr) use ($groupId) {
					return trim(strtoupper($tr["ItemID"])) == trim(strtoupper($groupId));
				});
				
				// Re-index transactions month wise
				$monthlyTxn = [];
				foreach ($itemTrans as $tr) {
					$monthKey = $tr['month'];
					
					$qty = 0;
					if ($tr["TType"] == "P" && $tr["TType2"] == "Purchase") {
						$qty += $tr['BilledQty'];
					}
					if ($tr["TType"] == "N" && $tr["TType2"] == "PurchaseReturn") {
						$qty -= $tr['BilledQty'];
					}
					if ($tr["TType"] == "A" && $tr["TType2"] == "Issue") {
						$qty -= $tr['BilledQty'];
					}
					if ($tr["TType"] == "B" && $tr["TType2"] == "Production") {
						$qty += $tr['BilledQty'];
					}
					if ($tr["TType"] == "O" && $tr["TType2"] == "Order") {
						$qty -= $tr['BilledQty'];
					}
					if ($tr["TType"] == "R" && $tr["TType2"] == "Fresh") {
						$qty += $tr['BilledQty'];
					}
					if ($tr["TType"] == "X") { // Adjustments
						$qty -= $tr['BilledQty'];
					}
					if ($tr["TType"] == "T" && $tr["TType2"] == "Out") {
						$qty -= $tr['BilledQty'];
					}
					if ($tr["TType"] == "T" && $tr["TType2"] == "In") {
						$qty += $tr['BilledQty'];
					}
					
					if (!isset($monthlyTxn[$monthKey])) {
						$monthlyTxn[$monthKey] = 0;
					}
					$monthlyTxn[$monthKey] += $qty;
				}
				
				// Now cumulative month wise stock
				$balance = $OQty;  // start with opening
				$dataPoints = [];
				
				foreach ($Months as $m) {
					if (isset($monthlyTxn[$m])) {
						$balance += $monthlyTxn[$m];
					}
					$dataPoints[] = round($balance / $CaseQty, 2);
				}
				if (array_sum($dataPoints) > 0) {
					$groupData[$groupId] = [
					'name' => $groupName,
					'data' => $dataPoints
					];
				}
			}
			
			$series = array_values($groupData);
			
			usort($series, function($a, $b) {
				$lastA = end($a['data']);
				$lastB = end($b['data']);
				return $lastB <=> $lastA; // Desc order
			});
			
			// Take only top 20
			$series = array_slice($series, 0, 20);
			
			$ReturnData = [
			'Stock' => $series,
			'Months' => $Months,
			];
			return $ReturnData;
		}
		public function GetTopDayWiseStockLevelChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date_data = (2000 + (int)$fy) . '-04-01';
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date   = to_sql_date($filterdata["to_date"]);
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1     = $filterdata["SubGroup1"];
			$SubGroup2     = $filterdata["SubGroup2"];
			$ItemID        = $filterdata["ItemID"];
			$ItemType        = $filterdata["ItemType"];
			$GodownID      = $filterdata["GodownID"];
			
			// Generate all dates between range
			$Days = [];
			$period = new DatePeriod(
			new DateTime($from_date),
			new DateInterval('P1D'),
			(new DateTime($to_date))->modify('+1 day')
			);
			foreach ($period as $date) {
				$Days[] = $date->format("d-M-Y");
			}
			
			/** ------------------------
				* Step 1: Get Item Groups
			* ------------------------ */
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->select('tblItemsSubGroup1.id as ItemID,tblItemsSubGroup1.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');
				} elseif (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->select('tblItemsSubGroup2.id as ItemID,tblItemsSubGroup2.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');
				} else {
				$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,tblitems.case_qty AS CaseQty');
			}
			$this->db->from(db_prefix() . 'items');
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			if (!empty($MainItemGroup)) {
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if (!empty($SubGroup1)) {
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if (!empty($SubGroup2)) {
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if (!empty($ItemID)) {
				$this->db->where('tblitems.item_code', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
			}
			if (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
			}
			$this->db->order_by('ItemName', 'ASC');
			$Group = $this->db->get()->result_array();
			/** ------------------------
				* Step 2: Opening Stock (before from_date)
			* ------------------------ */
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->select('tblItemsSubGroup1.id as ItemID,SUM(OQty) AS OQty');
				} elseif (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->select('tblItemsSubGroup2.id as ItemID,SUM(OQty) AS OQty');
				} else {
				$this->db->select('tblitems.item_code as ItemID,SUM(OQty) AS OQty');
			}
			$this->db->from(db_prefix() . 'stockmaster');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'stockmaster.ItemID');
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			if (!empty($MainItemGroup)) {
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if (!empty($SubGroup1)) {
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if (!empty($SubGroup2)) {
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if (!empty($ItemID)) {
				$this->db->where('tblitems.item_code', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if (!empty($GodownID)) {
				$this->db->where('tblstockmaster.GodownID', $GodownID);
			}
			$this->db->where('tblstockmaster.FY', $fy);
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID1');
				}elseif (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->group_by(db_prefix() . 'items.SubGrpID2');
				}else{
				$this->db->group_by(db_prefix() . 'items.item_code');
			}
			
			$OpeningStock = $this->db->get()->result_array();
			
			// echo "<pre>";print_r($OpeningStock);die;
			/** ------------------------
				* Step 3: Transactions (split into before from_date & between range)
			* ------------------------ */
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%d-%b-%Y") as day,tblItemsSubGroup1.id as ItemID,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
				} elseif (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%d-%b-%Y") as day,tblItemsSubGroup2.id as ItemID,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
				} else {
				$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%d-%b-%Y") as day,tblitems.item_code as ItemID,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
			}
			$this->db->from('tblhistory');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			if (!empty($MainItemGroup) && empty($SubGroup1)) {
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if (!empty($SubGroup1) && empty($SubGroup2)) {
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			$this->db->where('tblhistory.TransDate2 >=', $from_date_data . ' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if (!empty($MainItemGroup)) {
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if (!empty($SubGroup1)) {
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if (!empty($SubGroup2)) {
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if (!empty($ItemID)) {
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if (!empty($GodownID)) {
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			$this->db->group_by("DATE(tblhistory.TransDate2), tblhistory.ItemID, tblhistory.TType, tblhistory.TType2");
			$Transaction = $this->db->get()->result_array();
			
			/** ------------------------
				* Step 4: Build chart data
			* ------------------------ */
			$groupData = [];
			
			foreach ($Group as $g) {
				$groupId   = $g['ItemID'];
				$groupName = $g['ItemName'];
				$CaseQty   = ($g["CaseQty"] == "0") ? 1 : $g["CaseQty"];
				
				// Opening stock
				$OQty = 0;
				foreach ($OpeningStock as $os) {
					if ($os['ItemID'] == $groupId) {
						$OQty = $os['OQty'];
						break;
					}
				}
				// if($groupId == 'GFFG0015'){
				// echo $OQty;die;
				// }
				// Transactions of this item
				$itemTrans = array_filter($Transaction, function ($tr) use ($groupId) {
					return trim(strtoupper($tr["ItemID"])) == trim(strtoupper($groupId));
				});
				
				// Split: before from_date → add into OQty, else → day-wise
				$dailyTxn = [];
				foreach ($itemTrans as $tr) {
					$dayKey = $tr['day'];
					$transDate = DateTime::createFromFormat("d-M-Y", $dayKey)->format("Y-m-d");
					
					$qty = 0;
					if ($tr["TType"] == "P" && $tr["TType2"] == "Purchase") $qty += $tr['BilledQty'];
					if ($tr["TType"] == "N" && $tr["TType2"] == "PurchaseReturn") $qty -= $tr['BilledQty'];
					if ($tr["TType"] == "A" && $tr["TType2"] == "Issue") $qty -= $tr['BilledQty'];
					if ($tr["TType"] == "B" && $tr["TType2"] == "Production") $qty += $tr['BilledQty'];
					if ($tr["TType"] == "O" && $tr["TType2"] == "Order") $qty -= $tr['BilledQty'];
					if ($tr["TType"] == "R" && $tr["TType2"] == "Fresh") $qty += $tr['BilledQty'];
					if ($tr["TType"] == "X") $qty -= $tr['BilledQty'];
					if ($tr["TType"] == "T" && $tr["TType2"] == "Out") $qty -= $tr['BilledQty'];
					if ($tr["TType"] == "T" && $tr["TType2"] == "In") $qty += $tr['BilledQty'];
					
					if ($transDate < $from_date) {
						// Add to opening
						$OQty += $qty;
						} else {
						// Day-wise
						if (!isset($dailyTxn[$dayKey])) $dailyTxn[$dayKey] = 0;
						$dailyTxn[$dayKey] += $qty;
					}
				}
				
				
				// Cumulative calculation
				$balance = $OQty;
				
				
				
				$dataPoints = [];
				foreach ($Days as $d) {
					if (isset($dailyTxn[$d])) {
						$balance += $dailyTxn[$d];
					}
					
					$dataPoints[] = round($balance / $CaseQty, 2);
				}
				
				if (array_sum($dataPoints) > 0) {
					$groupData[$groupId] = [
					'name' => $groupName,
					'data' => $dataPoints
					];
				}
			}
			
			// Sort by last day stock
			$series = array_values($groupData);
			usort($series, function ($a, $b) {
				$lastA = end($a['data']);
				$lastB = end($b['data']);
				return $lastB <=> $lastA;
			});
			
			// Limit top 20
			$series = array_slice($series, 0, 20);
			
			return [
			'Stock' => $series,
			'Days'  => $Days
			];
		}
		public function DayWiseTransactionChart($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date_data = (2000 + (int)$fy) . '-04-01';
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date   = to_sql_date($filterdata["to_date"]);
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1     = $filterdata["SubGroup1"];
			$SubGroup2     = $filterdata["SubGroup2"];
			$ItemID        = $filterdata["ItemID"];
			$ItemType      = $filterdata["ItemType"];
			$GodownID      = $filterdata["GodownID"];
			
			// Generate all dates between range
			// $Days = [];
			// $period = new DatePeriod(
			// new DateTime($from_date),
			// new DateInterval('P1D'),
			// (new DateTime($to_date))->modify('+1 day')
			// );
			// foreach ($period as $date) {
			// $Days[] = $date->format("d-M-Y");
			// }
			
			$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%d-%b-%Y") as day,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2,COALESCE(NULLIF(tblitems.case_qty, 0), 1) AS AvgCaseQty');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID ', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			$this->db->group_by('DATE(tblhistory.TransDate2),tblhistory.ItemID,tblhistory.TType,tblhistory.TType2');
			$Transaction = $this->db->get('tblhistory')->result_array();
			
			$Days = [];
			foreach ($Transaction as $each) {
				$Days[] = $each['day'];
			}
			$Days = array_values(array_unique($Days));
			sort($Days);
			
			$series = [
			'Sale'         => array_fill(0, count($Days), 0),
			'Purchase'     => array_fill(0, count($Days), 0),
			'Production'   => array_fill(0, count($Days), 0),
			'Issue'        => array_fill(0, count($Days), 0),
			'Fresh Return' => array_fill(0, count($Days), 0),
			'Damage Return'=> array_fill(0, count($Days), 0),
			];
			
			// Index days for quick lookup
			$dayIndex = array_flip($Days);
			
			foreach ($Transaction as $each) {
				$day = $each['day'];
				if (!isset($dayIndex[$day])) continue; // skip out of range
				
				$idx = $dayIndex[$day];
				$qty = $each['BilledQty'] / $each['AvgCaseQty'];
				
				if ($each['TType'] == 'B' && $each['TType2'] == 'Production') {
					$series['Production'][$idx] += $qty;
				}
				if ($each['TType'] == 'O' && $each['TType2'] == 'Order') {
					$series['Sale'][$idx] += $qty;
				}
				if ($each['TType'] == 'P' && $each['TType2'] == 'Purchase') {
					$series['Purchase'][$idx] += $qty;
				}
				if ($each['TType'] == 'A' && $each['TType2'] == 'Issue') {
					$series['Issue'][$idx] += $qty;
				}
				if ($each['TType'] == 'R' && $each['TType2'] == 'Fresh') {
					$series['Fresh Return'][$idx] += $qty;
				}
				if ($each['TType'] == 'R' && $each['TType2'] == 'Damage') {
					$series['Damage Return'][$idx] += $qty;
				}
			}
			
			/** ------------------------
				* Step 4: Build chart series
			* ------------------------ */
			$finalSeries = [];
			foreach ($series as $name => $data) {
				$finalSeries[] = [
				'name' => $name,
				'data' => array_map(function($v){ return round($v, 2); }, $data)
				];
			}
			
			return [
			'Transaction' => $finalSeries,
			'Days'        => $Days
			];
		}
		
		
		
		
		public function GetCratesByDate($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$sql1 = '('.db_prefix().'accountcrates.Transdate BETWEEN "'.$from_date.' 00:00:00" AND "'.$to_date.' 23:59:59") AND '.db_prefix().'accountcrates.PlantID="'.$selected_company.'"  AND PassedFrom !="OPENCRATES" GROUP BY AccountID,TType';
			
			$sql ='SELECT AccountID,TType,COALESCE(SUM(Qty),0) As Qty  FROM '.db_prefix().'accountcrates WHERE '.$sql1;
			
			$result = $this->db->query($sql)->result_array();
			return $result;
		}
		public function DayBeforeTransactionCrate($data)
		{  
			$from_date = to_sql_date($data["from_date"]);
			$to_date = to_sql_date($data["to_date"]);
			
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$FirstDate = '20'.$fy.'-04-01';
			if($from_date == $FirstDate){
                $FromDate = $FirstDate;
                $ToDate = to_sql_date($data["from_date"]);
				return;
				}else{
                $FromDate = $FirstDate;
                $ToDate = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
				$sql1 = '('.db_prefix().'accountcrates.Transdate BETWEEN "'.$FirstDate.' 00:00:00" AND "'.$ToDate.' 23:59:59") AND '.db_prefix().'accountcrates.PlantID="'.$selected_company.'"  AND PassedFrom !="OPENCRATES"  GROUP BY AccountID,TType';
				
				$sql ='SELECT AccountID,TType,COALESCE(SUM(Qty),0) As Qty  FROM '.db_prefix().'accountcrates WHERE '.$sql1;
				$result = $this->db->query($sql)->result_array();
				return $result;
			}
			
		}
		public function GetOpeningCrates($data)
		{  
			
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$FirstDate = '20'.$fy.'-04-01';
			
			$FromDate = $FirstDate;
			$sql1 = '('.db_prefix().'accountcrates.Transdate BETWEEN "'.$FirstDate.' 00:00:00" AND "'.date('Y-m-d').' 23:59:59") AND '.db_prefix().'accountcrates.PlantID="'.$selected_company.'" AND PassedFrom ="OPENCRATES"  GROUP BY AccountID,TType';
			
			$sql ='SELECT AccountID,TType,COALESCE(SUM(Qty),0) As Qty  FROM '.db_prefix().'accountcrates WHERE '.$sql1;
			$result = $this->db->query($sql)->result_array();
			return $result;
			
			
		}
		
		//==================== All Active SKU Count ====================================
		public function AllTotalSKUCount($filterdata)
		{  
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1     = $filterdata["SubGroup1"];
			$SubGroup2     = $filterdata["SubGroup2"];
			$ItemID        = $filterdata["ItemID"];
			$ItemType        = $filterdata["ItemType"];
			
			$sql1 = ''.db_prefix().'items.PlantID = "'.$selected_company.'"';
			if(!empty($MainItemGroup)){
				$sql1 .= ' AND '.db_prefix().'items.MainGrpID = "'.$MainItemGroup.'"';
			}
			if(!empty($SubGroup1)){
				$sql1 .= ' AND '.db_prefix().'items.SubGrpID1 = "'.$SubGroup1.'"';
			}
			if(!empty($SubGroup2)){
				$sql1 .= ' AND '.db_prefix().'items.SubGrpID2 = "'.$SubGroup2.'"';
			}
			if(!empty($ItemID)){
				$sql1 .= ' AND '.db_prefix().'items.item_code = "'.$ItemID.'"';
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$sql1 .= ' AND '.db_prefix().'items.tax = "1"';
				}
				if($ItemType == 'Taxable'){
					$sql1 .= ' AND '.db_prefix().'items.tax != "1"';
				}
			}
			// $sql1 .= '  GROUP BY '.db_prefix().'items.MainGrpID';
			
			$sql ='SELECT COUNT(*) as TotalEntry
			FROM '.db_prefix().'items 
			WHERE '.$sql1;
			$result = $this->db->query($sql)->row();
			return $result;
		}
		//================= Get Low And Out Stock SKU ==================================
		public function GetLowAndOutStockCount($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = '20'.$fy. '-04-01';
			$to_date = to_sql_date($filterdata["to_date"]);
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$GodownID = $filterdata["GodownID"];
			
			$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,tblitems.case_qty AS CaseQty,tblitems.min_qty');
			$this->db->from(db_prefix() . 'items');
			
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblitems.item_code', $ItemID);
			}
			
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
				    $this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
				    $this->db->where('tblitems.tax !=', '1');
				}
			}
			$this->db->where('tblitems.isactive', 'Y');
			$this->db->order_by('ItemName', 'ASC');
			$ItemList = $this->db->get()->result_array();
			
			// Opening Stock Query Start
			$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(OQty) AS OQty');
			$this->db->from(db_prefix() . 'stockmaster');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'stockmaster.ItemID');
			if(!empty($MainItemGroup) && empty($SubGroup1)){
				$this->db->join(db_prefix() . 'ItemsSubGroup1', db_prefix() . 'ItemsSubGroup1.id = ' . db_prefix() . 'items.SubGrpID1');
			}
			if(!empty($SubGroup1) && empty($SubGroup2)){
				$this->db->join(db_prefix() . 'ItemsSubGroup2', db_prefix() . 'ItemsSubGroup2.id = ' . db_prefix() . 'items.SubGrpID2');
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblitems.item_code', $ItemID);
			}
			if(!empty($GodownID)){
				$this->db->where('tblstockmaster.GodownID', $GodownID);
			}
			
			$this->db->where('tblstockmaster.FY', $fy);
			$this->db->group_by(db_prefix() . 'items.item_code');
			
			$this->db->order_by('ItemName', 'ASC');
			$ItemWiseOpnQty = $this->db->get()->result_array();
			
			
			// echo "<pre>";print_r($OpeningStock);die;
			$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(tblhistory.BilledQty) as BilledQty,tblhistory.TType,tblhistory.TType2');
			$this->db->from('tblhistory');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			$this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2');
			$TransactinalData = $this->db->get()->result_array();
			// echo "<pre>";print_r($Transaction);die;
			
			
			$chart = [];
			$LowStockCount = 0;
			$OutOfStockCount = 0;
			foreach($ItemList as $key1 => $value1){
				if($value1["CaseQty"] == "0"){
					$CaseQty = 1;
					}else{
					$CaseQty = $value1["CaseQty"];
				}
				$OQty = 0;
				foreach($ItemWiseOpnQty as $key2 => $value2){
					if($value1['ItemID'] ==  $value2['ItemID']){
						$OQty = $value2['OQty'];
					}
				}
				$PurchQty = 0;$PurchRtnQty = 0;$IssueQty = 0;$PRDQty = 0;$SalesQty = 0;$SalesRtnQty = 0;$AdjQty = 0;$GOQty = 0;$GIQty = 0;
				foreach ($TransactinalData as $key3 => $value3) {
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "P" && $value3["TType2"] == "Purchase"){
						$PurchQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "N" && $value3["TType2"] == "PurchaseReturn"){
						$PurchRtnQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "A" && $value3["TType2"] == "Issue"){
						$IssueQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "B" && $value3["TType2"] == "Production"){
						$PRDQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "O" && $value3["TType2"] == "Order"){
						$SalesQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "R" && $value3["TType2"] == "Fresh")){
						$SalesRtnQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "X" && $value3["TType2"] == "Free Distribution" || $value3["TType"] == "X" && $value3["TType2"] == "Free distribution" || $value3["TType"] == "X" && $value3["TType2"] == "Promotional Activity" || $value3["TType"] == "X" && $value3["TType2"] == "Stock Adjustment" || $value3["TType"] == "X" && $value3["TType2"] == "IssueAgainstReturn")){
						$AdjQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "T" && $value3["TType2"] == "Out")){
						$GOQty += $value3['BilledQty'];
					}
					if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "T" && $value3["TType2"] == "In")){
						$GIQty += $value3['BilledQty'];
					}
				}
				$BQty =    $OQty +  $PurchQty - $PurchRtnQty - $IssueQty + $PRDQty - $SalesQty + $SalesRtnQty - $AdjQty  - $GOQty + $GIQty;
				
				$BQtyCases = floatval($BQty)/floatval($CaseQty);
				
				if($BQtyCases > 0 && $BQtyCases < $value1["min_qty"]){
					$LowStockCount++;
				}
				if($BQtyCases <= 0){
					$OutOfStockCount++;
				}
			}
			$data = [
			'LowStockCount' => $LowStockCount,
			'OutOfStockCount' => $OutOfStockCount,
			];
			return $data;
		}
		/*public function GetStockValuesByMainGroupWise($filterdata)
			{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$ItemType = $filterdata["ItemType"];
			$GodownID = $filterdata["GodownID"];
			$from_date_value = '20'.$fy.'-04-01';
			// Item List
			$this->db->select('tblitems.MainGrpID,tblitems.item_code as ItemID,tblitems.description as ItemName,tblitems.case_qty AS CaseQty');
			$this->db->from(db_prefix() . 'items');
			$this->db->order_by('ItemName', 'ASC');
			$ItemList = $this->db->get()->result_array();
			// Item Wise Opening Stock
			$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,SUM(OQty) AS OQty');
			$this->db->from(db_prefix() . 'stockmaster');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'stockmaster.ItemID');
			if(!empty($GodownID)){
			$this->db->where('tblstockmaster.GodownID', $GodownID);
			}
			if(!empty($ItemType)){
			if($ItemType == 'NonTaxable'){
			$this->db->where('tblitems.tax', '1');
			}
			if($ItemType == 'Taxable'){
			$this->db->where('tblitems.tax !=', '1');
			}
			}
			$this->db->where('tblstockmaster.FY', $fy);
			$this->db->group_by(db_prefix() . 'items.item_code');
			$this->db->order_by('ItemName', 'ASC');
			$ItemWiseOpnStock = $this->db->get()->result_array();
			// Transactinal Data
			$this->db->select('tblitems.item_code as ItemID,tblitems.description as ItemName,tblhistory.BilledQty,tblhistory.TType,tblhistory.TType2,tblhistory.SaleRate');
			$this->db->from('tblhistory');
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date_value.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($GodownID)){
			$this->db->where('tblhistory.GodownID', $GodownID);
			}
			if(!empty($ItemType)){
			if($ItemType == 'NonTaxable'){
			$this->db->where('tblitems.tax', '1');
			}
			if($ItemType == 'Taxable'){
			$this->db->where('tblitems.tax !=', '1');
			}
			}
			$TransactionData = $this->db->get()->result_array();
			
			$chart = [];
			$FGValue = 0;
			$RMValue = 0;
			$PMValue = 0;
			foreach($ItemList as $key1 => $value1){
			if($value1["CaseQty"] == "0"){
			$CaseQty = 1;
			}else{
			$CaseQty = $value1["CaseQty"];
			}
			$PurchRate = 0;
			$SaleRate = 0;
			$OQty = 0;
			foreach($ItemWiseOpnStock as $key2 => $value2){
			if($value1['ItemID'] ==  $value2['ItemID']){
			$OQty = $value2['OQty'];
			}
			}
			$PurchQty = 0;$PurchRtnQty = 0;$IssueQty = 0;$PRDQty = 0;$SalesQty = 0;$SalesRtnQty = 0;$AdjQty = 0;$GOQty = 0;$GIQty = 0;
			foreach ($TransactionData as $key3 => $value3) {
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "P" && $value3["TType2"] == "Purchase"){
			$PurchQty += $value3['BilledQty'];
			if($value3["SaleRate"] != '' && $value3["SaleRate"] != null && $value3["SaleRate"] >0){
			$PurchRate = $value3["SaleRate"];
			}
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "N" && $value3["TType2"] == "PurchaseReturn"){
			$PurchRtnQty += $value3['BilledQty'];
			if($value3["SaleRate"] != '' && $value3["SaleRate"] != null && $value3["SaleRate"] >0){
			$PurchRate = $value3["SaleRate"];
			}
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "A" && $value3["TType2"] == "Issue"){
			$IssueQty += $value3['BilledQty'];
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "B" && $value3["TType2"] == "Production"){
			$PRDQty += $value3['BilledQty'];
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && $value3["TType"] == "O" && $value3["TType2"] == "Order"){
			$SalesQty += $value3['BilledQty'];
			if($value3["SaleRate"] != '' && $value3["SaleRate"] != null && $value3["SaleRate"] >0){
			$SaleRate = $value3["SaleRate"];
			}
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "R" && $value3["TType2"] == "Fresh")){
			$SalesRtnQty += $value3['BilledQty'];
			if($value3["SaleRate"] != '' && $value3["SaleRate"] != null && $value3["SaleRate"] >0){
			$SaleRate = $value3["SaleRate"];
			}
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "X" && $value3["TType2"] == "Free Distribution" || $value3["TType"] == "X" && $value3["TType2"] == "Free distribution" || $value3["TType"] == "X" && $value3["TType2"] == "Promotional Activity" || $value3["TType"] == "X" && $value3["TType2"] == "Stock Adjustment" || $value3["TType"] == "X" && $value3["TType2"] == "IssueAgainstReturn")){
			$AdjQty += $value3['BilledQty'];
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "T" && $value3["TType2"] == "Out")){
			$GOQty += $value3['BilledQty'];
			}
			if(trim(strtoupper($value1["ItemID"])) == trim(strtoupper($value3["ItemID"])) && ($value3["TType"] == "T" && $value3["TType2"] == "In")){
			$GIQty += $value3['BilledQty'];
			}
			}
			$BQty =    $OQty +  $PurchQty - $PurchRtnQty - $IssueQty + $PRDQty - $SalesQty + $SalesRtnQty - $AdjQty  - $GOQty + $GIQty;
			
			$BQtyCases = floatval($BQty)/floatval($CaseQty);
			
			if($value1["MainGrpID"] == '1'){
			$FGValue += $BQty * $SaleRate;
			}
			if($value1["MainGrpID"] == '2'){
			$RMValue += $BQty * $PurchRate;
			}
			if($value1["MainGrpID"] == '3'){
			$PMValue += $BQty * $PurchRate;
			}
			
			}
			
			
			
			$data = [
			'TotalFGStockAmt' => number_format((float)($FGValue), 2, '.', ','),
			'TotalRMStockAmt' => number_format((float)($RMValue), 2, '.', ','),
			'TotalPMStockAmt' => number_format((float)($PMValue), 2, '.', ','),
			];
			
			return $data;
		}*/
		
		public function GetStockValuesByMainGroupWise($filterdata)
		{
			$fy               = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date   = to_sql_date($filterdata["to_date"]);
			$ItemType  = $filterdata["ItemType"];
			$GodownID  = $filterdata["GodownID"];
			
			$from_date_value = '20' . $fy . '-04-01';
			
			/* =========================================================
				1️⃣ ITEM MASTER
			========================================================= */
			$this->db->select('i.MainGrpID, i.item_code AS ItemID, i.case_qty AS CaseQty');
			$this->db->from(db_prefix() . 'items i');
			
			if (!empty($ItemType)) {
				if ($ItemType == 'NonTaxable') {
					$this->db->where('i.tax', '1');
				}
				if ($ItemType == 'Taxable') {
					$this->db->where('i.tax !=', '1');
				}
			}
			
			$ItemList = $this->db->get()->result_array();
			
			if (empty($ItemList)) {
				return [
				'TotalFGStockAmt' => '0.00',
				'TotalRMStockAmt' => '0.00',
				'TotalPMStockAmt' => '0.00'
				];
			}
			
			/* =========================================================
				2️⃣ OPENING STOCK (GROUPED)
			========================================================= */
			$this->db->select('sm.ItemID, SUM(sm.OQty) AS OQty');
			$this->db->from(db_prefix() . 'stockmaster sm');
			$this->db->where('sm.FY', $fy);
			
			if (!empty($GodownID)) {
				$this->db->where('sm.GodownID', $GodownID);
			}
			
			$this->db->group_by('sm.ItemID');
			$openingStock = $this->db->get()->result_array();
			
			$opnStockMap = array_column($openingStock, 'OQty', 'ItemID');
			
			/* =========================================================
				3️⃣ TRANSACTION DATA (SQL AGGREGATION)
			========================================================= */
			$this->db->select("
			h.ItemID,
			
			SUM(CASE WHEN h.TType='P' AND h.TType2='Purchase' THEN h.BilledQty ELSE 0 END) AS PurchQty,
			SUM(CASE WHEN h.TType='N' AND h.TType2='PurchaseReturn' THEN h.BilledQty ELSE 0 END) AS PurchRtnQty,
			SUM(CASE WHEN h.TType='A' AND h.TType2='Issue' THEN h.BilledQty ELSE 0 END) AS IssueQty,
			SUM(CASE WHEN h.TType='B' AND h.TType2='Production' THEN h.BilledQty ELSE 0 END) AS PRDQty,
			SUM(CASE WHEN h.TType='O' AND h.TType2='Order' THEN h.BilledQty ELSE 0 END) AS SalesQty,
			SUM(CASE WHEN h.TType='R' AND h.TType2='Fresh' THEN h.BilledQty ELSE 0 END) AS SalesRtnQty,
			SUM(CASE WHEN h.TType='X' THEN h.BilledQty ELSE 0 END) AS AdjQty,
			SUM(CASE WHEN h.TType='T' AND h.TType2='Out' THEN h.BilledQty ELSE 0 END) AS GOQty,
			SUM(CASE WHEN h.TType='T' AND h.TType2='In' THEN h.BilledQty ELSE 0 END) AS GIQty,
			
			MAX(CASE WHEN h.TType='O' THEN h.SaleRate ELSE 0 END) AS SaleRate,
			MAX(CASE WHEN h.TType IN ('P','N') THEN h.SaleRate ELSE 0 END) AS PurchRate
			");
			
			$this->db->from('tblhistory h');
			$this->db->where('h.TransDate2 >=', $from_date_value . ' 00:00:00');
			$this->db->where('h.TransDate2 <=', $to_date . ' 23:59:59');
			$this->db->where('h.BillID IS NOT NULL', null, false);
			
			if (!empty($GodownID)) {
				$this->db->where('h.GodownID', $GodownID);
			}
			
			$this->db->group_by('h.ItemID');
			
			$txnData = $this->db->get()->result_array();
			$txnMap  = array_column($txnData, null, 'ItemID');
			
			/* =========================================================
				4️⃣ FINAL CALCULATION
			========================================================= */
			$FGValue = 0;
			$RMValue = 0;
			$PMValue = 0;
			
			foreach ($ItemList as $item) {
				
				$ItemID  = $item['ItemID'];
				$CaseQty = ($item['CaseQty'] == 0) ? 1 : $item['CaseQty'];
				
				$OQty = $opnStockMap[$ItemID] ?? 0;
				$t    = $txnMap[$ItemID] ?? [];
				
				$BQty =
				$OQty +
				($t['PurchQty'] ?? 0) -
				($t['PurchRtnQty'] ?? 0) -
				($t['IssueQty'] ?? 0) +
				($t['PRDQty'] ?? 0) -
				($t['SalesQty'] ?? 0) +
				($t['SalesRtnQty'] ?? 0) -
				($t['AdjQty'] ?? 0) -
				($t['GOQty'] ?? 0) +
				($t['GIQty'] ?? 0);
				
				if ($item['MainGrpID'] == '1') {
					$FGValue += $BQty * ($t['SaleRate'] ?? 0);
				}
				if ($item['MainGrpID'] == '2') {
					$RMValue += $BQty * ($t['PurchRate'] ?? 0);
				}
				if ($item['MainGrpID'] == '3') {
					$PMValue += $BQty * ($t['PurchRate'] ?? 0);
				}
			}
			
			/* =========================================================
				5️⃣ RETURN
			========================================================= */
			return [
			'TotalFGStockAmt' => number_format((float)$FGValue, 2, '.', ','),
			'TotalRMStockAmt' => number_format((float)$RMValue, 2, '.', ','),
			'TotalPMStockAmt' => number_format((float)$PMValue, 2, '.', ',')
			];
		}
		
		public function GetStockReceivedAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$AccountID = $filterdata["AccountID"];
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$GodownID = $filterdata["GodownID"];
			
			$this->db->select('SUM(tblhistory.BilledQty) AS BilledQty,COALESCE(SUM(tblhistory.NetChallanAmt),0) as NetChallanAmt');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($AccountID)){
				$this->db->where('tblhistory.AccountID ', $AccountID);
			}
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			$this->db->where('tblhistory.TType', 'P');
			$this->db->where('tblhistory.TType2', 'Purchase');
			$Transaction = $this->db->get('tblhistory')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			$data = [
			'ReceivedAmt' => $Transaction->NetChallanAmt,
			'ReceivedQty' => $Transaction->BilledQty,
			];
			
			return $data;
		}
		public function GetStockIssueAmt($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$GodownID = $filterdata["GodownID"];
			
			$this->db->select('tblhistory.ItemID,SUM(tblhistory.BilledQty) AS BilledQty,
			(SELECT history2.BasicRate FROM tblhistory as history2 WHERE history2.ItemID=tblhistory.ItemID AND  history2.TType2="Purchase" AND history2.PlantID = '.$selected_company.' AND history2.FY = "'.$fy.'" GROUP BY history2.ItemID Order By TransDate2 DESC LIMIT 1) AS Rate');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			/*if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
				}
				if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
				}
				if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}*/
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			$this->db->where_in('tblhistory.TType', array('A'));
			$this->db->where_in('tblhistory.TType2', array('Issue'));
			$this->db->group_by('tblhistory.ItemID');
			$Transaction = $this->db->get('tblhistory')->result_array();
			// echo "<pre>";print_r($Transaction);die;
			
			$Amount = 0;
			$BilledQty = 0;
			foreach($Transaction as $each){
				$Amount += ($each['BilledQty'] * $each['Rate']);
				$BilledQty += $each['BilledQty'];
			}
			
			$data = [
			'TotalIssueStockAmt' => number_format((float)($Amount), 2, '.', ','),
			'TotalIssueStockQty' => number_format((float)($BilledQty), 2, '.', ','),
			];
			
			return $data;
		}
		public function HighestOrderItem($filterdata)
		{
			$fy = $this->session->userdata('finacial_year');
			$selected_company = $this->session->userdata('root_company');
			
			$from_date = to_sql_date($filterdata["from_date"]);
			$to_date = to_sql_date($filterdata["to_date"]);
			$MainItemGroup = $filterdata["MainItemGroup"];
			$SubGroup1 = $filterdata["SubGroup1"];
			$SubGroup2 = $filterdata["SubGroup2"];
			$ItemID = $filterdata["ItemID"];
			$ItemType = $filterdata["ItemType"];
			$GodownID = $filterdata["GodownID"];
			
			$this->db->select('tblhistory.ItemID,tblitems.description,SUM(tblhistory.BilledQty) AS BilledQty');
			
			$this->db->join(db_prefix() . 'items', db_prefix() . 'items.item_code = ' . db_prefix() . 'history.ItemID AND '.db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');
			$this->db->where('tblhistory.TransDate2 >=', $from_date.' 00:00:00');
			$this->db->where('tblhistory.TransDate2 <=', $to_date.' 23:59:59');
			$this->db->where('tblhistory.BillID IS NOT NULL');
			if(!empty($MainItemGroup)){
				$this->db->where('tblitems.MainGrpID', $MainItemGroup);
			}
			if(!empty($SubGroup1)){
				$this->db->where('tblitems.SubGrpID1', $SubGroup1);
			}
			if(!empty($SubGroup2)){
				$this->db->where('tblitems.SubGrpID2', $SubGroup2);
			}
			if(!empty($ItemID)){
				$this->db->where('tblhistory.ItemID', $ItemID);
			}
			if(!empty($GodownID)){
				$this->db->where('tblhistory.GodownID', $GodownID);
			}
			if(!empty($ItemType)){
				if($ItemType == 'NonTaxable'){
					$this->db->where('tblitems.tax', '1');
				}
				if($ItemType == 'Taxable'){
					$this->db->where('tblitems.tax !=', '1');
				}
			}
			$this->db->where_in('tblhistory.TType', array('O'));
			$this->db->where_in('tblhistory.TType2', array('Order'));
			$this->db->group_by('tblhistory.ItemID');
			$this->db->order_by('tblhistory.BilledQty','DESC');
			$Transaction = $this->db->get('tblhistory')->row();
			// echo "<pre>";print_r($Transaction);die;
			
			//	$response->ItemName = $Transaction->description;
			//	$response->Qty = $Transaction->TotalBilledQty;
		    $response = $Transaction->description."(".$Transaction->BilledQty.")";
			return $response;
		}

		public function get_item_main_groups()
    {
        $this->db->select('id, name'); // adjust column names
        $this->db->from('items_main_groups');       // adjust table name
        $query = $this->db->get();

        return $query->result(); // returns array of objects
    }

	public function GetVendorList()
{
    // Step 1: Get Vendor SubActGroupIDs
    $this->db->select('SubActGroupID');
    $this->db->where_in(db_prefix() . 'AccountSubGroup2.SubActGroupID1', ['100023']);
    $this->db->where(db_prefix() . 'AccountSubGroup2.IsVendor', 'Y');
    $subGroups = $this->db->get(db_prefix() . 'AccountSubGroup2')->result_array();

    if (empty($subGroups)) {
        return [];
    }

    $subGroupIds = array_column($subGroups, 'SubActGroupID');

    // Step 2: Fetch Vendors
    $this->db->select('AccountID, company');
    $this->db->from(db_prefix() . 'clients');
    $this->db->where(db_prefix() . 'clients.PlantID', $this->session->userdata('root_company'));
    $this->db->where_in(db_prefix() . 'clients.SubActGroupID', $subGroupIds);
    $this->db->order_by('company', 'asc');

    return $this->db->get()->result_array();
}
public function get_data_table_unit()

        {

            $selected_company = $this->session->userdata('root_company');

			

            $data = $this->db->get(db_prefix() . 'qc_unit')->result_array();

			return $data;

		}

		 public function get_data_table()
    {
        $selected_company = $this->session->userdata('root_company');
        
        //$this->db->where(db_prefix() . 'hsn.PlantID', $selected_company);
        $this->db->order_by(db_prefix() . 'hsn.name', 'ASC');
        $data = $this->db->get(db_prefix() . 'hsn')->result_array();
         return $data;
    }


	 // Last Id For Priority
    public function get_last_recordMainItemGroup()
    {
        $this->db->select('*');
        $this->db->from('items_main_groups');
        $this->db->order_by('id', 'DESC');
        $PriorityRecord =  $this->db->get()->row();
        return $PriorityRecord ? $PriorityRecord->id : 0;
    }

	// Item Table Data
    public function get_ItemType_data()
    {

        $this->db->select(db_prefix() . 'ItemTypeMaster.*');
        $this->db->from(db_prefix() . 'ItemTypeMaster');
        $this->db->order_by('id', 'ASC');
        return $this->db->get()->result_array();
    }

	}
