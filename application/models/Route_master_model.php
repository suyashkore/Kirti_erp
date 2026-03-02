<?php
	
	defined('BASEPATH') or exit('No direct script access allowed');
	
	class Route_master_model extends App_Model
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
			
			$this->db->select('*');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where(db_prefix() . 'route.PlantID', $selected_company);
			$this->db->from(db_prefix() . 'route');
			if (is_numeric($id)) {
				$this->db->where(db_prefix() . 'route.RouteID', $id);
				
				$data = $this->db->get()->row();
				if($data)
				{
					$data->RoutePoints = $this->getRoutePoints($id);
				}
				return $data;
			}
			return $this->db->get()->result_array();
		}
		public function getPointMaster($id = '')
		{
			
			$this->db->select('*');
			$this->db->from(db_prefix() . 'PointsMaster');
			if (is_numeric($id)) {
				$this->db->where(db_prefix() . 'PointsMaster.id', $id);
				return $this->db->get()->row();
			}
			return $this->db->get()->result_array();
		}
		public function getRoutePoints($id)
		{
			
			$this->db->select('*');
			$this->db->from(db_prefix() . 'RoutePoints');
				$this->db->where(db_prefix() . 'RoutePoints.RouteID', $id);
			return $this->db->get()->result_array();
		}
		
		public function get_last_recordPointMaster(){
			$this->db->select('*');
			$this->db->from('tblPointsMaster');
			$this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
			$LastRecord =  $this->db->get()->row();
			return $LastRecord->id;
		}
		
		
		/**
			* Add new invoice item
			* @param array $data Invoice item data
			* @return boolean
		*/
		public function add($data)
		{
			$routepoints = $data['routepoints'];
			unset($data["itemid"]);
			unset($data["routepoints"]);
			$data["PlantID"] = $this->session->userdata('root_company');
			$next_route_id = $this->last_route_id();
			$next_route_id = $next_route_id->RouteID + 1;
			$data["RouteID"] = $next_route_id;

			$this->db->insert(db_prefix() . 'route', $data);
			//$insert_id = $this->db->insert_id();
			if ($this->db->affected_rows() > 0) {
				foreach($routepoints as $each){
					$insArr = [
					'RouteID' => $next_route_id,
					'PointID' => $each,
					'TransDate'=>date('Y-m-d H:i:s'),
					'UserID'=>$this->session->userdata('username')??'',
					];
					$this->db->insert(db_prefix() . 'RoutePoints', $insArr);
				}
				
				log_activity('New Route Added [ID:' . $data["RouteID"] . ', ' . $data['name'] . ']');
				
				return $data["RouteID"];
			}
			
			return false;
		}
		
		public function last_route_id($id = '')
		{
			
			$this->db->select('*');
			$selected_company = $this->session->userdata('root_company');
			$this->db->where(db_prefix() . 'route.PlantID', $selected_company);
			$this->db->order_by(db_prefix() . 'route.RouteID', "DESC");
			$this->db->from(db_prefix() . 'route');
			return $this->db->get()->row();
		}
		
		
		/**
			* Update invoiec item
			* @param  array $data Invoice data to update
			* @return boolean
		*/
		public function edit($data)
		{
			$affectedRows = 0;
			$itemid = $data['itemid'];
			$routepoints = $data['routepoints'];
			unset($data['itemid']);
			unset($data['routepoints']);
			
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('RouteID', $itemid);
			$this->db->where('PlantID', $selected_company);
			
			if ($this->db->update(db_prefix() . 'route', $data)) {
				$affectedRows++;
				$this->db->where('RouteID', $itemid);
				$this->db->delete(db_prefix() . 'RoutePoints');
				
				foreach($routepoints as $each){
					$insArr = [
					'RouteID' => $itemid,
					'PointID' => $each,
					'TransDate'=>date('Y-m-d H:i:s'),
					'UserID'=>$this->session->userdata('username')??'',
					];
					$this->db->insert(db_prefix() . 'RoutePoints', $insArr);
				}
				
				log_activity(' Route Updated [ID: ' . $itemid . ', ' . $data['name'] . ']');
			}
			
			
			
			return $affectedRows > 0 ? true : false;
		}
		
		
		
		/**
			* Delete invoice item
			* @param  mixed $id
			* @return boolean
		*/
		public function delete($id)
		{
			$selected_company = $this->session->userdata('root_company');
			$this->db->where('RouteID', $id);
			$this->db->where('PlantID', $selected_company);
			$this->db->delete(db_prefix() . 'route');
			if ($this->db->affected_rows() > 0) {
				
				
				log_activity('Route Deleted [ID: ' . $id . ']');
				
				
				
				return true;
			}
			
			return false;
		}
		
		public function get_company_detail()
		{   
			$selected_company = $this->session->userdata('root_company');
			$sql ='SELECT '.db_prefix().'rootcompany.*
			FROM '.db_prefix().'rootcompany WHERE id = '.$selected_company;
			$result = $this->db->query($sql)->row();
			return $result;
			
		}
		public function get_data_table()
		{
			$selected_company = $this->session->userdata('root_company');
			
			$data = $this->db->get_where(db_prefix() . 'route',array('PlantID'=>$selected_company))->result_array();
			return $data;
		}
		
		public function getallstate()
		{
			
			$this->db->where('country_id', '1');
			$this->db->order_by('state_name', 'ASE');
			return $this->db->get(db_prefix() . 'xx_statelist')->result_array();
		}
		public function SavePointMaster($data)
		{
			$this->db->insert(db_prefix() . 'PointsMaster', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		public function get_data_table_PointMaster(){
			
			$this->db->select(db_prefix() . 'PointsMaster.*,'.db_prefix() . 'xx_statelist.state_name AS StateName,'.db_prefix() . 'xx_citylist.city_name AS CityName');
			$this->db->from(db_prefix() . 'PointsMaster');
			$this->db->join(db_prefix() . 'xx_statelist', '' . db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'PointsMaster.state');
			$this->db->join(db_prefix() . 'xx_citylist', '' . db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'PointsMaster.city');
			$this->db->order_by(db_prefix() . 'PointsMaster.id', 'DESC');
			return $this->db->get()->result_array();
		}
		
		public function GetPointMasterDetailByID($PointID){
			
			$this->db->select(db_prefix() . 'PointsMaster.*');
			$this->db->from(db_prefix() . 'PointsMaster');
			$this->db->where(db_prefix() . 'PointsMaster.id', $PointID);
			
			$data = $this->db->get()->row();
			
			if(!empty($data)){
				$data->CityList = $this->GetCityListData($data->state);
			}
			return $data;
		}
		
		public function GetCityListData($state)
		{
			$this->db->select(db_prefix() . 'xx_citylist.*');
			$this->db->where(db_prefix() . 'xx_citylist.state_id', $state);
			$this->db->order_by(db_prefix() . 'xx_citylist.city_name', 'ASC');
			return $this->db->get('tblxx_citylist')->result_array();
		}
		
		public function UpdatePointMaster($data,$PointID)
		{
			$this->db->where('id', $PointID);
			$this->db->update(db_prefix() . 'PointsMaster', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
		
		public function get_data_table_StationMaster(){
			
			$this->db->select(db_prefix() . 'StationMaster.*');
			$this->db->from(db_prefix() . 'StationMaster');
			$this->db->order_by(db_prefix() . 'StationMaster.id', 'DESC');
			return $this->db->get()->result_array();
		}
		
		public function get_last_recordStationMaster(){
			$this->db->select('*');
			$this->db->from('tblStationMaster');
			$this->db->order_by('id', 'DESC'); // 'created_at' is the column name of the date on which the record has stored in the database.
			$LastRecord =  $this->db->get()->row();
			return $LastRecord->id;
		}
		
		public function GetStationMasterDetailByID($StationID){
			
			$this->db->select(db_prefix() . 'StationMaster.*');
			$this->db->from(db_prefix() . 'StationMaster');
			$this->db->where(db_prefix() . 'StationMaster.id', $StationID);
			
			$data = $this->db->get()->row();
			return $data;
		}
		public function SaveStationMaster($data)
		{
			log_message('error', 'UserID session: ' . print_r($this->session->userdata(), true));

			$this->db->insert(db_prefix() . 'StationMaster', $data);
			$INSERT = $this->db->affected_rows();
			if($INSERT > 0){
				return true;    
				}else{
				return false;
			}
		}
		
		public function UpdateStationMaster($data,$StationID)
		{
			$this->db->where('id', $StationID);
			$this->db->update(db_prefix() . 'StationMaster', $data);
			$UPDATE = $this->db->affected_rows();        
			if($UPDATE > 0){
				return true;
				}else{
				return false;
			}
		}
	}
