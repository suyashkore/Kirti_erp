<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vehicle_return_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function challan_model_table($data){
          $selected_company = $this->session->userdata('root_company');
          $year = $this->session->userdata('finacial_year');
          $from_date = to_sql_date($data["from_date"]);
          $to_date = to_sql_date($data["to_date"]);
          
          $challanIDS = array();
          $this->db->select('*');
          $this->db->where('tblvehiclereturn.PlantID', $selected_company);
          $this->db->where('tblvehiclereturn.FY', $year);
          $vehRtnChallanID = $this->db->get('tblvehiclereturn')->result_array();
            foreach ($vehRtnChallanID as $key => $value) {
               array_push($challanIDS, $value["ChallanID"]);
            }
        if(empty($challanIDS)){
            return null;
        }else{
            $this->db->select('tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln');
            $this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
            $this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
            $this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
            $this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
            $this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
            $this->db->where_not_in('tblchallanmaster.ChallanID', $challanIDS);
            $this->db->where('tblchallanmaster.Transdate  BETWEEN "'. $from_date. ' 00:00:00" and "'. $to_date.' 23:59:59"');
            $this->db->where('tblchallanmaster.PlantID', $selected_company);
            $this->db->where('tblchallanmaster.FY', $year);
            $this->db->group_by('tblchallanmaster.ChallanID');
            $this->db->order_by('tblchallanmaster.ChallanID','desc');
            return $this->db->get('tblchallanmaster')->result_array();
        }
    }
    public function challan_unique_data($data){
           $selected_company = $this->session->userdata('root_company');
           $year = $this->session->userdata('finacial_year');
      
            $this->db->select('tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name,tblroute.KM,tblvehicle.VehicleCapacity, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln');
            $this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
            $this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
            $this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
            $this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
            $this->db->join('tblvehicle ', 'tblchallanmaster.VehicleID = tblvehicle.VehicleID', 'left');
            $this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
            $this->db->where('tblchallanmaster.PlantID LIKE', $selected_company);
            $this->db->where('tblchallanmaster.ChallanID', $data['challan_id']);
            $this->db->where('tblchallanmaster.FY', $year);
            $this->db->group_by('tblchallanmaster.ChallanID');
            return $this->db->get('tblchallanmaster')->row_array();
    }
      public function challan_all_data($data){
          
        $result = array();
         $selected_company = $this->session->userdata('root_company');
         $year = $this->session->userdata('finacial_year');
      
            $this->db->select('tblchallanmaster.*,tblordermaster.*,tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,tblclients.state,tblaccountcrates.Qty,tblaccountcrates.TType');
            $this->db->join('tblordermaster ', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
            $this->db->join('tblaccountcrates ', 'tblaccountcrates.PassedFrom = "OPENCRATES" AND tblordermaster.AccountID = tblaccountcrates.AccountID AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.FY = '.$year, 'left');
            $this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
            $this->db->where('tblchallanmaster.PlantID LIKE', $selected_company);
            $this->db->where('tblchallanmaster.ChallanID', $data['challan_id']);
            $this->db->where('tblchallanmaster.FY', $year);
            $data = $this->db->get('tblchallanmaster')->result_array();
            
             $i = 0;
            $response = array();
            
             $item_unq = array();
             foreach($data as $value){
                // credit crated
                  $this->db->select('sum(Qty) as credit_crate,AccountID');
                  $this->db->where('tblaccountcrates.PlantID', $selected_company);
                  $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
                  $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
                  $this->db->where('tblaccountcrates.TType LIKE', 'C');
                  $this->db->group_by('AccountID');
                  $credit_crate = $this->db->get('tblaccountcrates')->result_array();
                
                // debit crated
                  $this->db->select('sum(Qty) as debit_crate,AccountID');
                  $this->db->where('tblaccountcrates.PlantID', $selected_company);
                  $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
                  $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
                  $this->db->where('tblaccountcrates.TType LIKE', 'D');
                  $this->db->group_by('AccountID');
                  $debit_crate = $this->db->get('tblaccountcrates')->result_array();
                // balance crates
                  $balance = $debit_crate[0]['debit_crate'] - $credit_crate[0]['credit_crate'];
                    
                    
                   if($value['TType'] == 'D'){
                       $data[$i]['balance_crates'] = $balance+$value['Qty'];
                       $data[$i]['balance_crates_org'] = $balance+$value['Qty'];
                   }else{
                       $data[$i]['balance_crates'] = $balance-$value['Qty'];
                       $data[$i]['balance_crates_org'] = $balance-$value['Qty'];
                   }
                   
                   // item list using challan id
                   
                    $this->db->select('*');
                    $this->db->where('tblhistory.PlantID', $selected_company);
                    $this->db->where('tblhistory.FY', $year);
                    $this->db->where('tblhistory.AccountID', $value['AccountID']);
                    $this->db->where('tblhistory.BillID ', $value['ChallanID']);
                    $this->db->where('tblhistory.TType ', "O");
                    $this->db->where('tblhistory.TType2 ', "Order");
                    $itemlist_data = $this->db->get('tblhistory')->result_array();
                    $item_list_ary = array();
                    
                    foreach ($itemlist_data as $key => $value) {
                        # code...
                        array_push($item_list_ary, $value["ItemID"]);
                        if(!in_array($value["ItemID"], $item_unq)){
                            array_push($item_unq, $value["ItemID"]);
                        }
                    }
                    
                    //$data[$i]['itemhead'] = $item_unq;
                    $data[$i]['itemdetails'] = $itemlist_data;
                $i++; 
                 
             }
        $response["data"] = $data;
        $response["itemhead"] = $item_unq;
        return $response;
           
    }
    
    public function get_saleRtn_list($ChallanID){
          
        $result = array();
         $selected_company = $this->session->userdata('root_company');
         $year = $this->session->userdata('finacial_year');
      
            $this->db->select('tblchallanmaster.*,tblordermaster.*, tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,tblclients.state,tblaccountcrates.Qty,tblaccountcrates.TType');
            $this->db->join('tblordermaster ', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
            $this->db->join('tblaccountcrates ', 'tblaccountcrates.PassedFrom = "OPENCRATES" AND tblordermaster.AccountID = tblaccountcrates.AccountID AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.FY = '.$year, 'left');
            $this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
            //$this->db->join('tblhistory tblhistory_a', 'tblhistory_a.PlantID = '.$selected_company.' AND tblhistory_a.BillID = "'.$ChallanID.'" AND tblhistory_a.TType= "O" AND tblhistory_a.TType2 = "Order"', 'left');
            $this->db->where('tblchallanmaster.PlantID LIKE', $selected_company);
            $this->db->where('tblchallanmaster.ChallanID', $ChallanID);
            $this->db->where('tblchallanmaster.FY', $year);
            $data = $this->db->get('tblchallanmaster')->result_array();
            
             $i = 0;
            $response = array();
            
             $item_unq = array();
             foreach($data as $value){
                // credit crated
                  $this->db->select('sum(Qty) as credit_crate,AccountID');
                  $this->db->where('tblaccountcrates.PlantID', $selected_company);
                  $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
                  $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
                  $this->db->where('tblaccountcrates.TType LIKE', 'C');
                  $this->db->group_by('AccountID');
                  $credit_crate = $this->db->get('tblaccountcrates')->result_array();
                
                // debit crated
                  $this->db->select('sum(Qty) as debit_crate,AccountID');
                  $this->db->where('tblaccountcrates.PlantID', $selected_company);
                  $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
                  $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
                  $this->db->where('tblaccountcrates.TType LIKE', 'D');
                  $this->db->group_by('AccountID');
                  $debit_crate = $this->db->get('tblaccountcrates')->result_array();
                // balance crates
                  $balance = $debit_crate[0]['debit_crate'] - $credit_crate[0]['credit_crate'];
                    
                    
                   if($value['TType'] == 'D'){
                       $data[$i]['balance_crates'] = $balance+$value['Qty'];
                       $data[$i]['balance_crates_org'] = $balance+$value['Qty'];
                   }else{
                       $data[$i]['balance_crates'] = $balance-$value['Qty'];
                       $data[$i]['balance_crates_org'] = $balance-$value['Qty'];
                   }
                   
                   // item list using challan id
                   
                    $this->db->select('*');
                    $this->db->where('tblhistory.PlantID', $selected_company);
                    $this->db->where('tblhistory.FY', $year);
                    $this->db->where('tblhistory.AccountID', $value['AccountID']);
                    $this->db->where('tblhistory.BillID ', $value['ChallanID']);
                    $this->db->where('tblhistory.TType ', "O");
                    $this->db->where('tblhistory.TType2 ', "Order");
                    $itemlist_data = $this->db->get('tblhistory')->result_array();
                    $j = 0;
                    foreach ($itemlist_data as $key => $value) {
                        
                        $this->db->select('BilledQty,ChallanAmt,igstamt,cgstamt,sgstamt');
                        $this->db->where('tblhistory.PlantID', $selected_company);
                        $this->db->where('tblhistory.FY', $year);
                        $this->db->where('tblhistory.AccountID', $value['AccountID']);
                        $this->db->where('tblhistory.BillID ', $value['BillID']);
                        $this->db->where('tblhistory.ItemID ', $value['ItemID']);
                        $this->db->where('tblhistory.TType ', "R");
                        $this->db->where('tblhistory.TType2 ', "Fresh");
                        $ItemBilledQty = $this->db->get('tblhistory')->row();
                        $itemlist_data[$j]['OrdBilledQty'] = $ItemBilledQty->BilledQty;
                        $itemlist_data[$j]['RtnChallanAmt'] = $ItemBilledQty->ChallanAmt;
                        $itemlist_data[$j]['Rtnigstamt'] = $ItemBilledQty->igstamt;
                        $itemlist_data[$j]['Rtncgstamt'] = $ItemBilledQty->cgstamt;
                        $itemlist_data[$j]['Rtnsgstamt'] = $ItemBilledQty->sgstamt;
                        if(!in_array($value["ItemID"], $item_unq)){
                            array_push($item_unq, $value["ItemID"]);
                        }
                        $j++;
                    }
                    
                    //$data[$i]['itemhead'] = $item_unq;
                    $data[$i]['itemdetails'] = $itemlist_data;
                $i++; 
                 
             }
        $response["data"] = $data;
        $response["itemhead"] = $item_unq;
        return $response;
           
    }
    
    public function get_saleRtn_Itemlist($ChallanID){
        
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
      
            $this->db->select('*');
            $this->db->where('tblhistory.PlantID', $selected_company);
            $this->db->where('tblhistory.FY', $year);
            $this->db->where('tblhistory.BillID ', $ChallanID);
            $this->db->where('tblhistory.TType ', "R");
            $this->db->where('tblhistory.TType2 ', "Fresh");
            $itemlist_data = $this->db->get('tblhistory')->result_array();
                    
        return $itemlist_data;
           
    }
    
    public function get_vendor_data()
    {
      
        $selected_company = $this->session->userdata('root_company');
        
        $this->db->select('AccountID as id,AccountID,address,CONCAT(company," - ",AccountID) as label');
       
        $this->db->where_in(db_prefix() . 'clients.SubActGroupID', ['50003002','60001004']);
        $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        $this->db->order_by('company', 'asc');
        return $this->db->get(db_prefix() . 'clients')->result_array();
    
    }
    
     public function get_staff_data($data='')
    {
      
        $selected_company = $this->session->userdata('root_company');
        
        $this->db->select('tblclients.AccountID as id,tblclients.AccountID,tblclients.company,address,CONCAT(tblclients.company," - ",tblclients.AccountID) as label');
   
        $this->db->join('tblclients ', 'tblstaff.AccountID = tblclients.AccountID ');
         if($data != ''){
           $this->db->where('tblstaff.AccountID', $data);
         }
        $this->db->where('tblstaff.active', 1);
        if($data != ''){
        return $this->db->get('tblstaff')->row_array();
        }else{
            return $this->db->get('tblstaff')->result_array();
        }

    }
    public function get_vendor_details($data){
        $selected_company = $this->session->userdata('root_company');
         $year = $this->session->userdata('finacial_year');
      
            $this->db->select('tblclients.company,tblclients.address,tblaccountcrates.Qty,tblaccountcrates.TType');
            $this->db->join('tblaccountcrates ', 'tblaccountcrates.PassedFrom = "OPENCRATES" AND tblaccountcrates.AccountID = tblclients.AccountID AND tblaccountcrates.PlantID = '.$selected_company.' AND tblaccountcrates.FY = '.$year, 'left');
            $this->db->where('tblclients.PlantID LIKE', $selected_company);
            $this->db->where('tblclients.AccountID', $data);
            $data_return =  $this->db->get('tblclients')->row_array();
            
            $this->db->select('sum(Qty) as credit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $data);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'C');
            $this->db->group_by('AccountID');
            $credit_crate = $this->db->get('tblaccountcrates')->row_array();
        
            $this->db->select('sum(Qty) as debit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $data);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'D');
            $this->db->group_by('AccountID');
            $debit_crate = $this->db->get('tblaccountcrates')->row_array();

           $balance = $debit_crate['debit_crate'] - $credit_crate['credit_crate'];
        
          if($data_return['TType'] == 'D'){
                       $data_return['balance_crates'] = $balance+$data_return['Qty'];
                   }else{
                       $data_return['balance_crates'] = $balance-$data_return['Qty'];
                   }
            return $data_return;
    }
    public function add_vehicle_return($data){
     
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        
            if($selected_company == 1){
                
                $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cspl');
                
            }elseif($selected_company == 2){
                $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cff');
                
            }elseif($selected_company == 3){
                $new_vehicle_returnNumber = get_option('next_vehicle_return_number_for_cbu');
            }
            
         $new_vehicle_return_Numbar = 'VRT'.$FY.$new_vehicle_returnNumber;
         $Transdate =  to_sql_date($data['from_date'])." ".date('H:i:s');
         $crates = $data['refund_crates'];
         $ChallanID = $data['challan_n'];
        $vehicleRtn_data = array(
            'PlantID'=> $selected_company,
            'ReturnID'=>$new_vehicle_return_Numbar,
            'Transdate'=>$Transdate,
            'Crates'=>$crates,
            'ChallanID'=>$ChallanID,
            'UserID'=>$_SESSION['username'],
            'FY'=>$FY
            );
        
        $this->db->insert(db_prefix() . 'vehiclereturn',$vehicleRtn_data);
        $this->increment_next_number();
        
        // Delete Eng Vehicle
            $this->db->where('PlantID', $selected_company);
            $this->db->where('EngageID', $data["vehicle_number"]);
            $this->db->delete(db_prefix() . 'accountsld');
                                            
        $count = $data['row_count'];
        for($i=1; $i<=$count; $i++) {
            $AccountID = "AccountID".$i;
            $rtncrates = "rtncrates".$i;
            $ItemID = "ItemID".$i;
            
            $vehicleCrates_data = array(
                'PlantID'=>$selected_company,
                'VoucherID' =>$new_vehicle_return_Numbar,
                'Transdate' =>$Transdate,
                'TransDate2' =>date('Y-m-d H:i:s'),
                'ChallanID' =>$ChallanID,
                'AccountID' =>$data[$AccountID],
                'TType' =>'C',
                'Qty'=>$data[$rtncrates],
                'PassedFrom'=>'VEHRTNCRATES',
                'Narration'=> 'Against VehicleID '.$new_vehicle_return_Numbar.'/ChallanID /'.$ChallanID,
                'Ordinalno'=>$i,
                'UserID'=>$_SESSION['username'],
                 'FY'=>$FY,
            );
            //print_r($vehicleCrates_data);
            $data_i = $this->db->insert(db_prefix() . 'accountcrates',$vehicleCrates_data);
           
        }
        $ItemCount = $data['ItemCount'];
        for($IC=1; $IC<=$ItemCount; $IC++) {
            
            $TransID_val = "TransID_val".$IC;
            $ItemID_val = "ItemID_val".$IC;
            $AccountID_val = "AccountID_val".$IC;
            $rate_val = "rate_val".$IC;
            $gst_val = "gst_val".$IC;
            $state_val = "state_val".$IC;
            $rtnqty = "rtnqty".$IC;
            $PackQty_val = "PackQty_val".$IC;
            if($data[$rtnqty] == ''){
                
            }else{
                $ChallanAmt = $data[$rate_val] * $data[$rtnqty];
                $gst_amt = ($ChallanAmt/100) * $data[$gst_val];
                $NetChallanAmt = $ChallanAmt + $gst_amt;
                $gstRate = ($data[$rate_val]/100) * $data[$gst_val];
                $saleRate = $gstRate + $data[$rate_val];
                $CaseQty = $data[$PackQty_val];
                if($data[$state_val] == "UP"){
                    $cgstAmt = $gst_amt / 2;
                    $sgstAmt = $gst_amt / 2;
                    $igstAmt = 0.00;
                    
                    $cgstPer = $data[$gst_val] / 2;
                    $sgstPer = $data[$gst_val] / 2;
                    $igstPer = 0.00;
                }else{
                    $cgstAmt = 0.00;
                    $sgstAmt = 0.00;
                    $igstAmt = $gst_amt;
                    
                    $cgstPer = 0.00;
                    $sgstPer = 0.00;
                    $igstPer = $data[$gst_val];
                }
                
                    // stock update
                    $stock_data = $this->get_stock_item($data[$ItemID_val]);
                    $item_stock= $stock_data->SRQty;
                    $new_stock = $item_stock + $data[$rtnqty];
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->where('FY', $FY);  
                    $this->db->where('ItemID', $data[$ItemID_val]);
                    $this->db->update(db_prefix() . 'stockmaster', [
                                                'SRQty' => $new_stock,
                                            ]);
                
                $new_record_details = array(
                        "PlantID"=>$selected_company,
                        "FY"=>$FY,
                        "cnfid"=>"1",
                        "OrderID"=>$new_vehicle_return_Numbar,
                        "TransDate"=>$Transdate,
                        "TransDate2"=>$Transdate,
                        "BillID"=>$ChallanID,
                        "TransID"=>$data[$TransID_val],
                        "TType"=>"R",
                        "TType2"=>"Fresh",
                        "AccountID"=>$data[$AccountID_val],
                        "ItemID"=>$data[$ItemID_val],
                        "CaseQty"=>$CaseQty,
                        "SaleRate"=>$saleRate,
                        "BasicRate"=>$data[$rate_val],
                        "SuppliedIn"=>"CS",
                        "BilledQty"=>$data[$rtnqty],
                        "DiscPerc"=>"0.00",
                        "DiscAmt"=>"0.00",
                        "cgst"=>$cgstPer,
                        "cgstamt"=>$cgstAmt,
                        "sgst"=>$sgstPer,
                        "sgstamt"=>$sgstAmt,
                        "igst"=>$igstPer,
                        "igstamt"=>$igstAmt,
                        "ChallanAmt"=>$ChallanAmt,
                        "NetChallanAmt"=>$NetChallanAmt,
                        "Ordinalno"=>$IC,
                        "UserID"=>$this->session->userdata('username'),
                    );
                //print_r($new_record_details);
                $this->db->insert(db_prefix() . 'history', $new_record_details);
                }
        }
        
        $newmonth = substr($Transdate,5,2);
            $month = $newmonth;
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
            
        // For payments
        $row_count_pay = $data['row_count_pay'];
        $ord_no = 1;
        for($j=1; $j<=$row_count_pay; $j++) {
            $AccountID_pay = "AccountID_pay".$j;
            $receiptamt = "receiptamt".$j;
            
            $payment_reciept_result = array(
                    'PlantID'=>$selected_company,
                    'FY' =>$FY,
                    'Transdate' =>$Transdate,
                    'TransDate2' =>date('Y-m-d H:i:s'),
                    'VoucherID' =>$new_vehicle_return_Numbar,
                    'AccountID' =>$data[$AccountID_pay],
                    'TType' =>'C',
                    'Amount'=>$data[$receiptamt],
                    'PassedFrom'=>'VEHRTNPYMTS',
                    'Narration'=> 'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                    'OrdinalNo'=>$ord_no,
                    'UserID'=>$_SESSION['username'],
                    );
                //print_r($payment_reciept_result);
                $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result);
               
                $get_account_bal = $this->get_acc_bal($data[$AccountID_pay]);
                $get_lastamount = $this->get_last_ledger_amt_pay($new_vehicle_return_Numbar,$data[$AccountID_pay]);
            
                $current_bal = $get_account_bal->$mm;
                $new_amt = $get_lastamount->Amount;
                $new_credit_amt = $current_bal - $new_amt;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', $data[$AccountID_pay]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt,
                                        ]);
               
                
                $payment_reciept_result_debit = array(
                    'PlantID'=>$selected_company,
                    'FY' =>$FY,
                    'Transdate' =>$Transdate,
                    'TransDate2' =>date('Y-m-d H:i:s'),
                    'VoucherID' =>$new_vehicle_return_Numbar,
                    'AccountID' =>'CASH',
                    'TType' =>'D',
                    'Amount'=>$data[$receiptamt],
                    'PassedFrom'=>'VEHRTNPYMTS',
                    'Narration'=> 'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                    'OrdinalNo'=>$ord_no,
                    'UserID'=>$_SESSION['username'],
                    );
                //print_r($payment_reciept_result_debit);
                $data_i = $this->db->insert(db_prefix() . 'accountledger',$payment_reciept_result_debit);
                
                $get_account_bal1 = $this->get_acc_bal("CASH");
                $get_lastamount1 = $this->get_last_ledger_amt_pay_cash($new_vehicle_return_Numbar,"CASH",$ord_no);
            
                $current_bal1 = $get_account_bal1->$mm;
                $new_amt1 = $get_lastamount1->Amount;
                $new_credit_amt1 = $current_bal1 + $new_amt1;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', "CASH");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt1,
                                        ]);
                
            $ord_no++;
        }
        
        // For Expenses
        
        $row_count_exp = $data['row_count_exp'];
        for($k=1; $k<=$row_count_exp; $k++) {
            $AccountID_exp = "AccountID_exp".$k;
            $expamt = "expamt".$k;
            
            $expense_detail_result = array(
                    'PlantID'=>$selected_company,
                    'FY' =>$FY,
                    'Transdate' =>$Transdate,
                    'TransDate2' =>date('Y-m-d H:i:s'),
                    'VoucherID' =>$new_vehicle_return_Numbar,
                    'AccountID' =>$data[$AccountID_exp],
                    'TType' =>'D',
                    'Amount'=>$data[$expamt],
                    'PassedFrom'=>'VEHRTNEXP',
                    'Narration'=> 'By Vehicle Expense '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                    'OrdinalNo'=>$ord_no,
                    'UserID'=>$_SESSION['username'], 
                    );
                //print_r($expense_detail_result);
                $data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result);
                
                $get_account_bal111 = $this->get_acc_bal($data[$AccountID_exp]);
                $get_lastamount111 = $this->get_last_ledger_amt_exp($new_vehicle_return_Numbar,$data[$AccountID_exp]);
            
                $current_bal111 = $get_account_bal111->$mm;
                $new_amt111 = $get_lastamount111->Amount;
                $new_credit_amt111 = $current_bal111 + $new_amt111;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', $data[$AccountID_exp]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt111,
                                        ]);
                                        
                
                                                  
                 $expense_detail_result_debit = array(
                    'PlantID'=>$selected_company,
                    'FY' =>$FY,
                    'Transdate' =>$Transdate,
                    'TransDate2' =>date('Y-m-d H:i:s'),
                    'VoucherID' =>$new_vehicle_return_Numbar,
                    'AccountID' =>'CASH',
                    'TType' =>'C',
                    'Amount'=>$data[$expamt],
                    'PassedFrom'=>'VEHRTNEXP',
                    'Narration'=> 'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                    'OrdinalNo'=>$ord_no,
                    'UserID'=>$_SESSION['username'],
                    );
                //print_r($expense_detail_result_debit);
                $data_i = $this->db->insert(db_prefix() . 'accountledger',$expense_detail_result_debit);
                
                
                $get_account_bal11 = $this->get_acc_bal("CASH");
                $get_lastamount11 = $this->get_last_ledger_amt_exp_cash($new_vehicle_return_Numbar,"CASH",$ord_no);
            
                $current_bal11 = $get_account_bal11->$mm;
                $new_amt11 = $get_lastamount11->Amount;
                $new_credit_amt11 = $current_bal11 - $new_amt11;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', "CASH");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt11,
                                        ]);
                
            $ord_no++;
        }
        
        // For FreshRtn
        
        $row_count_fRtn = $data['row_count_frRtn'];
        $igst_total = 0;
        $cgst_total = 0;
        $sgst_total = 0;
        for($m=1; $m<=$row_count_fRtn; $m++) {
            
            $AccountID_SRtn = "AccountID_SRtn".$m;
            $RtnAmt_val = "RtnAmt_val".$m;
            $cgst_val = "cgst_val".$m;
            $sgst_val = "sgst_val".$m;
            $igst_val = "igst_val".$m;
            
            
            $igst_total = $igst_total + $data[$igst_val];
            $cgst_total = $cgst_total + $data[$cgst_val];
            $sgst_total = $sgst_total + $data[$sgst_val];
            
            $net_total = $data[$igst_val] + $data[$cgst_val] + $data[$sgst_val] + $data[$RtnAmt_val];
            // Respective Account ledger Entry for Credit
            $credit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$new_vehicle_return_Numbar,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"C",
                "AccountID"=>$data[$AccountID_SRtn],
                "Amount"=>$net_total,
                "Narration"=>'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
            
            // Cash Account ledger Entry for Credit
            $debit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$new_vehicle_return_Numbar,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"SALE",
                "Amount"=>$data[$RtnAmt_val],
                "Narration"=>'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            
            //  Credit Respective Account Balance
            $get_account_bal2 = $this->get_acc_bal($data[$AccountID_SRtn]);
            $get_lastamount2 = $this->get_last_ledger_amt_SRtn($new_vehicle_return_Numbar,$data[$AccountID_SRtn]);
            
            $current_bal2 = $get_account_bal2->$mm;
            $new_amt = $get_lastamount2->Amount;
            $new_credit_amt = $current_bal2 - $new_amt;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', $data[$AccountID_SRtn]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt,
                                        ]);
            
            // Debit Cash Account balance
            $get_account_bal22 = $this->get_acc_bal("SALE");
            $get_lastamount22 = $this->get_last_ledger_amt_SRtn_sale($new_vehicle_return_Numbar,"SALE",$ord_no);
            
            $current_bal22 = $get_account_bal22->$mm;
            $new_amt22 = $get_lastamount22->Amount;
            $new_credit_amt22 = $current_bal22 + $new_amt22;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', "SALE");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt22,
                                        ]);
            $ord_no++;
        }
        
        // Ledger fr CGST,SGST and IGST
        
        if($cgst_total !== 0){
            
            $debit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$new_vehicle_return_Numbar,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"SGST",
                "Amount"=>$sgst_total,
                "Narration"=>'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            $ord_no++;
            //  Debit IGST Account Balance
            $get_account_bal8 = $this->get_acc_bal("SGST");
            $get_lastamount8 = $this->get_last_ledger_amt_gst($new_vehicle_return_Numbar,"SGST");
            
            $current_bal8 = $get_account_bal8->$mm;
            $new_amt8 = $get_lastamount8->Amount;
            $new_credit_amt8 = $current_bal8 + $new_amt8;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->LIKE('AccountID', "SGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt8,
                                        ]);
        }
        if($cgst_total !== 0){
            
            $debit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$new_vehicle_return_Numbar,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"CGST",
                "Amount"=>$cgst_total,
                "Narration"=>'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            $ord_no++;
            //  Debit IGST Account Balance
            $get_account_bal7 = $this->get_acc_bal("CGST");
            $get_lastamount7 = $this->get_last_ledger_amt_gst($new_vehicle_return_Numbar,"CGST");
            
            $current_bal7 = $get_account_bal7->$mm;
            $new_amt7 = $get_lastamount7->Amount;
            $new_credit_amt7 = $current_bal7 + $new_amt7;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', "CGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt7,
                                        ]);
        }
        if($igst_total !== 0){
            
            $debit_ledger = array(
                "FY"=>$FY,
                "PlantID"=>$selected_company,
                "VoucherID"=>$new_vehicle_return_Numbar,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"IGST",
                "Amount"=>$igst_total,
                "Narration"=>'Cash Received/VehicleReturn '.$new_vehicle_return_Numbar.'/'.$ChallanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            $ord_no++;
            //  Debit IGST Account Balance
            $get_account_bal6 = $this->get_acc_bal("IGST");
            $get_lastamount6 = $this->get_last_ledger_amt_gst($new_vehicle_return_Numbar,"IGST");
            
            $current_bal6 = $get_account_bal6->$mm;
            $new_amt6 = $get_lastamount6->Amount;
            $new_credit_amt6 = $current_bal6 + $new_amt6;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $FY);
                $this->db->where('AccountID', "IGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt6,
                                        ]);
        }
        return true;
    }
    
    public function update_vehicle_rtn($data){
        /* echo "<pre>";
         print_r($data);
         echo $data["RtnAmt_val1"];
         die;*/
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $vRtnID = $data['ex_vehicle_return_id'];
        $challanID = $data['challan_n'];
        $Transdate =  to_sql_date($data['VrtnDate'])." ".date('H:i:s');
        $month = substr($Transdate,5,2);
        $old_date = $data['old_date'];
        $month_old = substr($old_date,5,2);
        $crates = $data['refund_crates'];
        
        $vehicleRtn_data = array(
            
            'Transdate'=>$Transdate,
            'Crates'=>$crates,
            "UserID2"=>$this->session->userdata('username'),
            "Lupdate"=>date('Y-m-d H:i:s'),
            );
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->where('ReturnID', $vRtnID);
        $this->db->where('ChallanID', $challanID);
        $this->db->update(db_prefix() . 'vehiclereturn', $vehicleRtn_data);
        $old_crate_details = $this->get_all_crate_vehicle_return_details($vRtnID);
        $old_Accounts = array();
        $update_Accounts = array();
        //$new_Accounts = array();
        //$delete_Accounts = array();
        foreach ($old_crate_details as $key => $value) {
            # code...
            array_push($old_Accounts, $value["AccountID"]);
        }
        //print_r();
        $count = $data['row_count'];
        for($i=1; $i<=$count; $i++) {
            
            $AccountID = "AccountID".$i;
            $rtncrates = "rtncrates".$i;
           //array_push($new_Accounts, $data[$AccountID]);
            if(in_array($data[$AccountID], $old_Accounts)){
            // Update record
                $vehicleCrates_update = array(
                    
                    'Transdate' =>$Transdate,
                    'Qty'=>$data[$rtncrates],
                    "UserID2"=>$this->session->userdata('username'),
                    "Lupdate"=>date('Y-m-d H:i:s'),
                );
                //print_r($vehicleCrates_update);
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $fy);
                $this->db->where('VoucherID', $vRtnID);
                $this->db->where('ChallanID', $challanID);
                $this->db->where('AccountID', $data[$AccountID]);
                $this->db->update(db_prefix() . 'accountcrates', $vehicleCrates_update);
            }else{
            // Insert record
                $vehicleCrates_data = array(
                    'PlantID'=>$selected_company,
                    'VoucherID' =>$vRtnID,
                    'Transdate' =>$Transdate,
                    'ChallanID' =>$challanID,
                    'AccountID' =>$data[$AccountID],
                    'TType' =>'C',
                    'Qty'=>$data[$rtncrates],
                    'PassedFrom'=>'VEHRTNCRATES',
                    'Narration'=> 'Against VehicleID '.$vRtnID.'/ChallanID /'.$challanID,
                    'Ordinalno'=>$i,
                    'UserID'=>$_SESSION['username'],
                    'FY'=>$fy,
                );
                //print_r($vehicleCrates_data);
                $data_i = $this->db->insert(db_prefix() . 'accountcrates',$vehicleCrates_data);
            }
        }
       
        /*foreach ($old_Accounts as $value) {
            if(in_array($value, $new_Accounts)){
                array_push($delete_Accounts, $value);
            }
        }*/
        
        $ItemCount = $data['ItemCount'];
        for($IC=1; $IC<=$ItemCount; $IC++) {
            
            $TransID_val = "TransID_val".$IC;
            $ItemID_val = "ItemID_val".$IC;
            $AccountID_val = "AccountID_val".$IC;
            $rate_val = "rate_val".$IC;
            $gst_val = "gst_val".$IC;
            $state_val = "state_val".$IC;
            $rtnqty = "rtnqty".$IC;
            $PackQty_val = "PackQty_val".$IC;
            if($data[$rtnqty] == "0" || $data[$rtnqty] == "0.000" || $data[$rtnqty] == "0.00" || $data[$rtnqty] == "0.0"){
                // Stock value revert from old sale return 
                $salertn_details = $this->get_salertn_details($data[$AccountID_val],$data[$TransID_val],$data[$ItemID_val]);
                
                    $stock_data = $this->get_stock_item($salertn_details->ItemID);
                    $item_stock= $stock_data->SRQty;
                    $new_stock = $item_stock - $salertn_details->BilledQty;
                    
                    $this->db->where('PlantID', $selected_company);
                    $this->db->where('FY', $fy);  
                    $this->db->where('ItemID', $salertn_details->ItemID);
                    $this->db->update(db_prefix() . 'stockmaster', [
                                                'SRQty' => $new_stock,
                                            ]);
                // END Stock value revert from old sale return 
                
                // Delete History record
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->LIKE('TransID', $data[$TransID_val]);
                $this->db->LIKE('AccountID', $data[$AccountID_val]);
                $this->db->LIKE('OrderID', $vRtnID);
                $this->db->LIKE('ItemID', $data[$ItemID_val]);
                $this->db->delete(db_prefix() . 'history');
            }else{
                $ChallanAmt = $data[$rate_val] * $data[$rtnqty];
                $gst_amt = ($ChallanAmt/100) * $data[$gst_val];
                $NetChallanAmt = $ChallanAmt + $gst_amt;
                $gstRate = ($data[$rate_val]/100) * $data[$gst_val];
                $saleRate = $gstRate + $data[$rate_val];
                $CaseQty = $data[$PackQty_val];
                if($data[$state_val] == "UP"){
                    $cgstAmt = $gst_amt / 2;
                    $sgstAmt = $gst_amt / 2;
                    $igstAmt = 0.00;
                    
                    $cgstPer = $data[$gst_val] / 2;
                    $sgstPer = $data[$gst_val] / 2;
                    $igstPer = 0.00;
                }else{
                    $cgstAmt = 0.00;
                    $sgstAmt = 0.00;
                    $igstAmt = $gst_amt;
                    
                    $cgstPer = 0.00;
                    $sgstPer = 0.00;
                    $igstPer = $data[$gst_val];
                }
            
            $check_item_exit = $this->get_salertn_details($data[$AccountID_val],$data[$TransID_val],$data[$ItemID_val]);
            if($check_item_exit){
                // Stock value revert from old sale return 
                $salertn_details = $this->get_salertn_details($data[$AccountID_val],$data[$TransID_val],$data[$ItemID_val]);
                    
                        $stock_data = $this->get_stock_item($salertn_details->ItemID);
                        $item_stock= $stock_data->SRQty;
                        $new_stock = $item_stock - $salertn_details->BilledQty;
                        
                        $this->db->where('PlantID', $selected_company);
                        $this->db->where('FY', $fy);  
                        $this->db->where('ItemID', $salertn_details->ItemID);
                        $this->db->update(db_prefix() . 'stockmaster', [
                                                    'SRQty' => $new_stock,
                                                ]);
                // END Stock value revert from old sale return 
                
                
                // New stock update
                    $stock_data = $this->get_stock_item($data[$ItemID_val]);
                    $item_stock= $stock_data->SRQty;
                    $new_stock = $item_stock + $data[$rtnqty];
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->where('FY', $fy);  
                    $this->db->where('ItemID', $data[$ItemID_val]);
                    $this->db->update(db_prefix() . 'stockmaster', [
                                                'SRQty' => $new_stock,
                                            ]);
                                            
                // Update Item Records
                
                $update_record_details = array(
                        
                        "TransDate"=>$Transdate,
                        "TransDate2"=>$Transdate,
                        "BilledQty"=>$data[$rtnqty],
                        "cgst"=>$cgstPer,
                        "cgstamt"=>$cgstAmt,
                        "sgst"=>$sgstPer,
                        "sgstamt"=>$sgstAmt,
                        "igst"=>$igstPer,
                        "igstamt"=>$igstAmt,
                        "ChallanAmt"=>$ChallanAmt,
                        "NetChallanAmt"=>$NetChallanAmt,
                        "Ordinalno"=>$IC,
                        "UserID2"=>$this->session->userdata('username'),
                        "Lupdate"=>date('Y-m-d H:i:s'),
                    );
                //print_r($update_record_details);
                $this->db->where('PlantID', $selected_company);
                $this->db->where('FY', $fy);
                $this->db->where('OrderID', $vRtnID);
                $this->db->where('BillID', $challanID);
                $this->db->where('AccountID', $data[$AccountID_val]);
                $this->db->where('ItemID', $data[$ItemID_val]);
                $this->db->update(db_prefix() . 'history', $update_record_details);
            }else{
                
                // New stock update
                    $stock_data = $this->get_stock_item($data[$ItemID_val]);
                    $item_stock= $stock_data->SRQty;
                    $new_stock = $item_stock + $data[$rtnqty];
                
                    $this->db->where('PlantID', $selected_company);
                    $this->db->where('FY', $fy);  
                    $this->db->where('ItemID', $data[$ItemID_val]);
                    $this->db->update(db_prefix() . 'stockmaster', [
                                                'SRQty' => $new_stock,
                                            ]);
                                            
                $new_record_details = array(
                        "PlantID"=>$selected_company,
                        "FY"=>$fy,
                        "cnfid"=>"1",
                        "OrderID"=>$vRtnID,
                        "TransDate"=>$Transdate,
                        "TransDate2"=>$Transdate,
                        "BillID"=>$challanID,
                        "TransID"=>$data[$TransID_val],
                        "TType"=>"R",
                        "TType2"=>"Fresh",
                        "AccountID"=>$data[$AccountID_val],
                        "ItemID"=>$data[$ItemID_val],
                        "CaseQty"=>$CaseQty,
                        "SaleRate"=>$saleRate,
                        "BasicRate"=>$data[$rate_val],
                        "SuppliedIn"=>"CS",
                        "BilledQty"=>$data[$rtnqty],
                        "DiscPerc"=>"0.00",
                        "DiscAmt"=>"0.00",
                        "cgst"=>$cgstPer,
                        "cgstamt"=>$cgstAmt,
                        "sgst"=>$sgstPer,
                        "sgstamt"=>$sgstAmt,
                        "igst"=>$igstPer,
                        "igstamt"=>$igstAmt,
                        "ChallanAmt"=>$ChallanAmt,
                        "NetChallanAmt"=>$NetChallanAmt,
                        "Ordinalno"=>$IC,
                        "UserID"=>$this->session->userdata('username'),
                    );
                //print_r($new_record_details);
                $this->db->insert(db_prefix() . 'history', $new_record_details);
            }
                
                
            }
            
        }
       
       if($month_old == "01"){
               $m_old = 11; 
            }
            if($month_old == "02"){
               $m_old = 12; 
            }
            if($month_old == "03"){
               $m_old = 13; 
            }
            if($month_old == "04"){
               $m_old = 2; 
            }
            if($month_old == "05"){
               $m_old = 3; 
            }
            if($month_old == "06"){
               $m_old = 4; 
            }
            if($month_old == "07"){
               $m_old = 5; 
            }
            if($month_old == "08"){
               $m_old = 6; 
            }
            if($month_old == "09"){
               $m_old = 7; 
            }
            if($month_old == "10"){
               $m_old = 8; 
            }
            if($month_old == "11"){
               $m_old = 9; 
            }
            if($month_old == "12"){
               $m_old = 10; 
            }
            $mm_old = "BAL".$m_old;
    
            
            
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
        /*echo $mm;
        echo "<br>";
        echo $mm_old;*/
        
        // For payments
        $row_count_pay = $data['row_count_pay'];
        $ord_no = 1;
        for($j=1; $j<=$row_count_pay; $j++) {
            $AccountID_pay = "AccountID_pay".$j;
            $receiptamt = "receiptamt".$j;
            
            // Delete Previous Balance & ledger records
            // For AccountID
            $get_account_bal = $this->get_acc_bal($data[$AccountID_pay]);
            $get_lastamount = $this->get_last_ledger_amt_pay($vRtnID,$data[$AccountID_pay]);
            
            $current_bal = $get_account_bal->$mm_old;
            $credited_amt = $get_lastamount->Amount;
            $debit_pre_ledger_amt = $current_bal + $credited_amt;
                 
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', $data[$AccountID_pay]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $debit_pre_ledger_amt,
                                        ]);
            // For CASH Account
            $get_account_bal1 = $this->get_acc_bal("CASH");
            $get_lastamount1 = $this->get_last_ledger_amt_pay_cash($vRtnID,"CASH",$get_lastamount->OrdinalNo);
            
            $current_bal1 = $get_account_bal1->$mm_old;
            $credited_amt1 = $get_lastamount1->Amount;
            $debit_pre_ledger_amt1 = $current_bal1 - $credited_amt1;
                 
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "CASH");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $debit_pre_ledger_amt1,
                                        ]);
            
            // ledger audit history
            if($get_lastamount){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount->PlantID,
                    "FY"=>$get_lastamount->FY,
                    "Transdate"=>$get_lastamount->Transdate,
                    "TransDate2"=>$get_lastamount->TransDate2,
                    "VoucherID"=>$get_lastamount->VoucherID,
                    "AccountID"=>$get_lastamount->AccountID,
                    "TType"=>$get_lastamount->TType,
                    "Amount"=>$get_lastamount->Amount,
                    "Narration"=>$get_lastamount->Narration,
                    "PassedFrom"=>$get_lastamount->PassedFrom,
                    "OrdinalNo"=>$get_lastamount->OrdinalNo,
                    "UserID"=>$get_lastamount->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            
            if($get_lastamount1){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount1->PlantID,
                    "FY"=>$get_lastamount1->FY,
                    "Transdate"=>$get_lastamount1->Transdate,
                    "TransDate2"=>$get_lastamount1->TransDate2,
                    "VoucherID"=>$get_lastamount1->VoucherID,
                    "AccountID"=>$get_lastamount1->AccountID,
                    "TType"=>$get_lastamount1->TType,
                    "Amount"=>$get_lastamount1->Amount,
                    "Narration"=>$get_lastamount1->Narration,
                    "PassedFrom"=>$get_lastamount1->PassedFrom,
                    "OrdinalNo"=>$get_lastamount1->OrdinalNo,
                    "UserID"=>$get_lastamount1->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            
            // Delete Respective Account
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "VEHRTNPYMTS");
            $this->db->where('AccountID', $data[$AccountID_pay]);
            $this->db->where('VoucherID', $vRtnID);
            $this->db->delete(db_prefix() . 'accountledger');
            
            // Delete Cash Account
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "VEHRTNPYMTS");
            $this->db->where('AccountID', "CASH");
            $this->db->where('VoucherID', $vRtnID);
            $this->db->where('OrdinalNo', $ord_no);
            $this->db->delete(db_prefix() . 'accountledger');
            
            
                // Respective Account ledger Entry for Credit
            $credit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"C",
                "AccountID"=>$data[$AccountID_pay],
                "Amount"=>$data[$receiptamt],
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNPYMTS",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
            
            // Cash Account ledger Entry for Debit
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"CASH",
                "Amount"=>$data[$receiptamt],
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNPYMTS",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            
            //  Credit Respective Account Balance
            $get_account_bal2 = $this->get_acc_bal($data[$AccountID_pay]);
            $get_lastamount2 = $this->get_last_ledger_amt_pay($vRtnID,$data[$AccountID_pay]);
            
            $current_bal2 = $get_account_bal2->$mm;
            $new_amt = $get_lastamount2->Amount;
            $new_credit_amt = $current_bal2 - $new_amt;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', $data[$AccountID_pay]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt,
                                        ]);
            
            // Debit Cash Account balance
            $get_account_bal22 = $this->get_acc_bal("CASH");
            $get_lastamount22 = $this->get_last_ledger_amt_pay_cash($vRtnID,"CASH",$ord_no);
            
            $current_bal22 = $get_account_bal22->$mm;
            $new_amt22 = $get_lastamount22->Amount;
            $new_credit_amt22 = $current_bal22 + $new_amt22;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "CASH");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt22,
                                        ]);
            $ord_no++;
            
        }
        
        // for Expense
        
        $row_count_exp = $data['row_count_exp'];
        for($k=1; $k<=$row_count_exp; $k++) {
            $AccountID_exp = "AccountID_exp".$k;
            $expamt = "expamt".$k;
            
            $get_lastamount2 = "";
            $get_lastamount22 = "";
            // Delete Previous Balance & ledger records
            // For AccountID
            $get_account_bal = $this->get_acc_bal($data[$AccountID_exp]);
            $get_lastamount2 = $this->get_last_ledger_amt_exp($vRtnID,$data[$AccountID_exp]);
            
            $current_bal = $get_account_bal->$mm_old;
            $credited_amt = $get_lastamount2->Amount;
            $debit_pre_ledger_amt = $current_bal - $credited_amt;
                 
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', $data[$AccountID_exp]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $debit_pre_ledger_amt,
                                        ]);
            // For CASH Account
            $get_account_bal1 = $this->get_acc_bal("CASH");
            $get_lastamount22 = $this->get_last_ledger_amt_exp_cash($vRtnID,"CASH",$get_lastamount2->OrdinalNo);
            
            $current_bal1 = $get_account_bal1->$mm_old;
            $credited_amt1 = $get_lastamount22->Amount;
            $debit_pre_ledger_amt1 = $current_bal1 + $credited_amt1;
                 
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "CASH");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $debit_pre_ledger_amt1,
                                        ]);
            
            // ledger audit history
            if($get_lastamount2){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount2->PlantID,
                    "FY"=>$get_lastamount2->FY,
                    "Transdate"=>$get_lastamount2->Transdate,
                    "TransDate2"=>$get_lastamount2->TransDate2,
                    "VoucherID"=>$get_lastamount2->VoucherID,
                    "AccountID"=>$get_lastamount2->AccountID,
                    "TType"=>$get_lastamount2->TType,
                    "Amount"=>$get_lastamount2->Amount,
                    "Narration"=>$get_lastamount2->Narration,
                    "PassedFrom"=>$get_lastamount2->PassedFrom,
                    "OrdinalNo"=>$get_lastamount2->OrdinalNo,
                    "UserID"=>$get_lastamount2->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            
            if($get_lastamount22){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount22->PlantID,
                    "FY"=>$get_lastamount22->FY,
                    "Transdate"=>$get_lastamount22->Transdate,
                    "TransDate2"=>$get_lastamount22->TransDate2,
                    "VoucherID"=>$get_lastamount22->VoucherID,
                    "AccountID"=>$get_lastamount22->AccountID,
                    "TType"=>$get_lastamount22->TType,
                    "Amount"=>$get_lastamount22->Amount,
                    "Narration"=>$get_lastamount22->Narration,
                    "PassedFrom"=>$get_lastamount22->PassedFrom,
                    "OrdinalNo"=>$get_lastamount22->OrdinalNo,
                    "UserID"=>$get_lastamount22->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            
            // Delete Respective Account
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "VEHRTNEXP");
            $this->db->where('AccountID', $data[$AccountID_exp]);
            $this->db->where('VoucherID', $vRtnID);
            $this->db->delete(db_prefix() . 'accountledger');
            
            // Delete Cash Account
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->where('PassedFrom', "VEHRTNEXP");
            $this->db->where('AccountID', "CASH");
            $this->db->where('VoucherID', $vRtnID);
            $this->db->where('OrdinalNo', $ord_no);
            $this->db->delete(db_prefix() . 'accountledger');
            
            
                // Respective Account ledger Entry for Debit
            $credit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>$data[$AccountID_exp],
                "Amount"=>$data[$expamt],
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNEXP",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
            
            // Cash Account ledger Entry for Credit
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"C",
                "AccountID"=>"CASH",
                "Amount"=>$data[$expamt],
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNEXP",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            
            //  Debit Respective Account Balance
            $get_account_bal2 = $this->get_acc_bal($data[$AccountID_exp]);
            $get_lastamount2 = $this->get_last_ledger_amt_exp($vRtnID,$data[$AccountID_exp]);
            
            $current_bal2 = $get_account_bal2->$mm;
            $new_amt = $get_lastamount2->Amount;
            $new_credit_amt = $current_bal2 + $new_amt;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', $data[$AccountID_exp]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt,
                                        ]);
            
            // Credit Cash Account balance
            $get_account_bal22 = $this->get_acc_bal("CASH");
            $get_lastamount22 = $this->get_last_ledger_amt_exp_cash($vRtnID,"CASH",$ord_no);
            
            $current_bal22 = $get_account_bal22->$mm;
            $new_amt22 = $get_lastamount22->Amount;
            $new_credit_amt22 = $current_bal22 - $new_amt22;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "CASH");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt22,
                                        ]);
            $ord_no++;
            
        }
        
        $row_count_frRtn = $data['row_count_frRtn'];
        $igst_total = 0;
        $cgst_total = 0;
        $sgst_total = 0;
        for($m=1; $m<=$row_count_frRtn; $m++) {
            $AccountID_SRtn = "AccountID_SRtn".$m;
            $RtnAmt_val = "RtnAmt_val".$m;
            $cgst_val = "cgst_val".$m;
            $sgst_val = "sgst_val".$m;
            $igst_val = "igst_val".$m;
            
            $igst_total = $igst_total + $data[$igst_val];
            $cgst_total = $cgst_total + $data[$cgst_val];
            $sgst_total = $sgst_total + $data[$sgst_val];
            
            $net_amt = $data[$RtnAmt_val] + $data[$igst_val] + $data[$sgst_val] + $data[$cgst_val];
            // Delete Previous Balance & ledger records
            // For AccountID
            $get_account_bal = $this->get_acc_bal($data[$AccountID_SRtn]);
            $get_lastamount = $this->get_last_ledger_amt_SRtn($vRtnID,$data[$AccountID_SRtn]);
            
            $current_bal = $get_account_bal->$mm_old;
            $credited_amt = $get_lastamount->Amount;
            $debit_pre_ledger_amt = $current_bal + $credited_amt;
                 
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', $data[$AccountID_SRtn]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $debit_pre_ledger_amt,
                                        ]);
            // For SALE Account
            $get_account_bal1 = $this->get_acc_bal("SALE");
            $get_lastamount1 = $this->get_last_ledger_amt_SRtn_sale($vRtnID,"SALE",$get_lastamount->OrdinalNo);
            
            $current_bal1 = $get_account_bal1->$mm_old;
            $credited_amt1 = $get_lastamount1->Amount;
            $debit_pre_ledger_amt1 = $current_bal1 - $credited_amt1;
                 
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "SALE");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $debit_pre_ledger_amt1,
                                        ]);
            
            // ledger audit history
            if($get_lastamount){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount->PlantID,
                    "FY"=>$get_lastamount->FY,
                    "Transdate"=>$get_lastamount->Transdate,
                    "TransDate2"=>$get_lastamount->TransDate2,
                    "VoucherID"=>$get_lastamount->VoucherID,
                    "AccountID"=>$get_lastamount->AccountID,
                    "TType"=>$get_lastamount->TType,
                    "Amount"=>$get_lastamount->Amount,
                    "Narration"=>$get_lastamount->Narration,
                    "PassedFrom"=>$get_lastamount->PassedFrom,
                    "OrdinalNo"=>$get_lastamount->OrdinalNo,
                    "UserID"=>$get_lastamount->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            
            if($get_lastamount1){
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount1->PlantID,
                    "FY"=>$get_lastamount1->FY,
                    "Transdate"=>$get_lastamount1->Transdate,
                    "TransDate2"=>$get_lastamount1->TransDate2,
                    "VoucherID"=>$get_lastamount1->VoucherID,
                    "AccountID"=>$get_lastamount1->AccountID,
                    "TType"=>$get_lastamount1->TType,
                    "Amount"=>$get_lastamount1->Amount,
                    "Narration"=>$get_lastamount1->Narration,
                    "PassedFrom"=>$get_lastamount1->PassedFrom,
                    "OrdinalNo"=>$get_lastamount1->OrdinalNo,
                    "UserID"=>$get_lastamount1->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            }
            
            // Delete Respective Account
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
            $this->db->where('AccountID', $data[$AccountID_SRtn]);
            $this->db->where('VoucherID', $vRtnID);
            $this->db->delete(db_prefix() . 'accountledger');
            
            // Delete Cash Account
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
            $this->db->where('AccountID', "SALE");
            $this->db->where('VoucherID', $vRtnID);
            $this->db->WHERE('OrdinalNo', $get_lastamount1->OrdinalNo);
            $this->db->delete(db_prefix() . 'accountledger');
            
            // Respective Account ledger Entry for Credit
            $credit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"C",
                "AccountID"=>$data[$AccountID_SRtn],
                "Amount"=>$net_amt,
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
           /* echo "<pre>";
            print_r($credit_ledger);*/
            $this->db->insert(db_prefix() . 'accountledger', $credit_ledger);
            
            // Cash Account ledger Entry for Credit
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"SALE",
                "Amount"=>$data[$RtnAmt_val],
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            //print_r($debit_ledger);
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            
            //  Credit Respective Account Balance
            $get_account_bal2 = $this->get_acc_bal($data[$AccountID_SRtn]);
            $get_lastamount2 = $this->get_last_ledger_amt_SRtn($vRtnID,$data[$AccountID_SRtn]);
            
            $current_bal2 = $get_account_bal2->$mm;
            $new_amt = $get_lastamount2->Amount;
            $new_credit_amt = $current_bal2 - $new_amt;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', $data[$AccountID_SRtn]);
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt,
                                        ]);
            
            // Debit Cash Account balance
            $get_account_bal22 = $this->get_acc_bal("SALE");
            $get_lastamount22 = $this->get_last_ledger_amt_SRtn_sale($vRtnID,"SALE",$ord_no);
            
            $current_bal22 = $get_account_bal22->$mm;
            $new_amt22 = $get_lastamount22->Amount;
            $new_credit_amt22 = $current_bal22 + $new_amt22;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "SALE");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt22,
                                        ]);
            $ord_no++;
        }
        //die;
        // Ledger fr CGST,SGST and IGST
        
        $get_lastamount3 = $this->get_last_ledger_amt_gst($vRtnID,"IGST");
        if($get_lastamount3){
            $get_account_bal3 = $this->get_acc_bal("IGST");
            $current_bal3 = $get_account_bal3->$mm_old;
            $new_amt3 = $get_lastamount3->Amount;
            $new_credit_amt3 = $current_bal3 - $new_amt3;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "IGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $new_credit_amt3,
                                        ]);
                $ledger_audit = array(
                    "PlantID"=>$get_lastamount3->PlantID,
                    "FY"=>$get_lastamount3->FY,
                    "Transdate"=>$get_lastamount3->Transdate,
                    "TransDate2"=>$get_lastamount3->TransDate2,
                    "VoucherID"=>$get_lastamount3->VoucherID,
                    "AccountID"=>$get_lastamount3->AccountID,
                    "TType"=>$get_lastamount3->TType,
                    "Amount"=>$get_lastamount3->Amount,
                    "Narration"=>$get_lastamount3->Narration,
                    "PassedFrom"=>$get_lastamount3->PassedFrom,
                    "OrdinalNo"=>$get_lastamount3->OrdinalNo,
                    "UserID"=>$get_lastamount3->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
           
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
            $this->db->where('AccountID', "IGST");
            $this->db->where('VoucherID', $vRtnID);
            $this->db->delete(db_prefix() . 'accountledger');
        }
        
        $get_lastamount4 = $this->get_last_ledger_amt_gst($vRtnID,"CGST");
        if($get_lastamount4){
            $get_account_bal4 = $this->get_acc_bal("CGST");
            $current_bal4 = $get_account_bal4->$mm_old;
            $new_amt4 = $get_lastamount4->Amount;
            $new_credit_amt4 = $current_bal4 - $new_amt4;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "CGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $new_credit_amt4,
                                        ]);
            $ledger_audit = array(
                    "PlantID"=>$get_lastamount4->PlantID,
                    "FY"=>$get_lastamount4->FY,
                    "Transdate"=>$get_lastamount4->Transdate,
                    "TransDate2"=>$get_lastamount4->TransDate2,
                    "VoucherID"=>$get_lastamount4->VoucherID,
                    "AccountID"=>$get_lastamount4->AccountID,
                    "TType"=>$get_lastamount4->TType,
                    "Amount"=>$get_lastamount4->Amount,
                    "Narration"=>$get_lastamount4->Narration,
                    "PassedFrom"=>$get_lastamount4->PassedFrom,
                    "OrdinalNo"=>$get_lastamount4->OrdinalNo,
                    "UserID"=>$get_lastamount4->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
                
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
            $this->db->where('AccountID', "CGST");
            $this->db->where('VoucherID', $vRtnID);
            $this->db->delete(db_prefix() . 'accountledger');
        }
        $get_lastamount5 = $this->get_last_ledger_amt_gst($vRtnID,"SGST");
        if($get_lastamount5){
            $get_account_bal5 = $this->get_acc_bal("SGST");
            $current_bal5 = $get_account_bal5->$mm_old;
            $new_amt5 = $get_lastamount5->Amount;
            $new_credit_amt5 = $current_bal5 - $new_amt5;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "SGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm_old => $new_credit_amt5,
                                        ]);
            $ledger_audit = array(
                    "PlantID"=>$get_lastamount5->PlantID,
                    "FY"=>$get_lastamount5->FY,
                    "Transdate"=>$get_lastamount5->Transdate,
                    "TransDate2"=>$get_lastamount5->TransDate2,
                    "VoucherID"=>$get_lastamount5->VoucherID,
                    "AccountID"=>$get_lastamount5->AccountID,
                    "TType"=>$get_lastamount5->TType,
                    "Amount"=>$get_lastamount5->Amount,
                    "Narration"=>$get_lastamount5->Narration,
                    "PassedFrom"=>$get_lastamount5->PassedFrom,
                    "OrdinalNo"=>$get_lastamount5->OrdinalNo,
                    "UserID"=>$get_lastamount5->UserID,
                    "Lupdate"=>date('Y-m-d H:i:s'),
                    "UserID2"=>$this->session->userdata('username')
                );
                $this->db->insert(db_prefix().'accountledgeraudit', $ledger_audit);
            $this->db->where('PlantID', $selected_company);
            $this->db->LIKE('FY', $fy);
            $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
            $this->db->where('AccountID', "SGST");
            $this->db->where('VoucherID', $vRtnID);
            $this->db->delete(db_prefix() . 'accountledger');
        }
        
        
        if($cgst_total !== 0){
            
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"SGST",
                "Amount"=>$sgst_total,
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            $ord_no++;
            //  Debit IGST Account Balance
            $get_account_bal8 = $this->get_acc_bal("SGST");
            $get_lastamount8 = $this->get_last_ledger_amt_gst($vRtnID,"SGST");
            
            $current_bal8 = $get_account_bal8->$mm;
            $new_amt8 = $get_lastamount8->Amount;
            $new_credit_amt8 = $current_bal8 + $new_amt8;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "SGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt8,
                                        ]);
        }
        if($cgst_total !== 0){
            
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"CGST",
                "Amount"=>$cgst_total,
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            $ord_no++;
            //  Debit IGST Account Balance
            $get_account_bal7 = $this->get_acc_bal("CGST");
            $get_lastamount7 = $this->get_last_ledger_amt_gst($vRtnID,"CGST");
            
            $current_bal7 = $get_account_bal7->$mm;
            $new_amt7 = $get_lastamount7->Amount;
            $new_credit_amt7 = $current_bal7 + $new_amt7;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "CGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt7,
                                        ]);
        }
        if($igst_total !== 0){
            
            $debit_ledger = array(
                "FY"=>$fy,
                "PlantID"=>$selected_company,
                "VoucherID"=>$vRtnID,
                "Transdate"=>$Transdate,
                "TransDate2"=>date('Y-m-d H:i:s'),
                "TType"=>"D",
                "AccountID"=>"IGST",
                "Amount"=>$igst_total,
                "Narration"=>'Cash Received/VehicleReturn '.$vRtnID.'/'.$challanID,
                "PassedFrom"=>"VEHRTNFRESH",
                "OrdinalNo"=>$ord_no,
                "UserID"=>$this->session->userdata('username'),
            );
            $this->db->insert(db_prefix() . 'accountledger', $debit_ledger);
            $ord_no++;
            //  Debit IGST Account Balance
            $get_account_bal6 = $this->get_acc_bal("IGST");
            $get_lastamount6 = $this->get_last_ledger_amt_gst($vRtnID,"IGST");
            
            $current_bal6 = $get_account_bal6->$mm;
            $new_amt6 = $get_lastamount6->Amount;
            $new_credit_amt6 = $current_bal6 + $new_amt6;
                
                $this->db->where('PlantID', $selected_company);
                $this->db->LIKE('FY', $fy);
                $this->db->where('AccountID', "IGST");
                $this->db->update(db_prefix() . 'accountbalances', [
                                            $mm => $new_credit_amt6,
                                        ]);
        }
        
        return true;
    }
    
    public function get_last_ledger_amt_pay($id,$account_id)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->WHERE('AccountID', $account_id);
        $this->db->WHERE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "VEHRTNPYMTS");
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }
    public function get_last_ledger_amt_pay_cash($id,$account_id,$ord_no)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->WHERE('AccountID', $account_id);
        $this->db->WHERE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "VEHRTNPYMTS");
        $this->db->WHERE('OrdinalNo', $ord_no);
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }
    
    public function get_last_ledger_amt_exp($id,$account_id)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->WHERE('AccountID', $account_id);
        $this->db->WHERE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "VEHRTNEXP");
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }
    public function get_last_ledger_amt_exp_cash($id,$account_id,$ord_no)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->WHERE('AccountID', $account_id);
        $this->db->WHERE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "VEHRTNEXP");
        $this->db->WHERE('OrdinalNo', $ord_no);
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }
    
    public function get_last_ledger_amt_SRtn($id,$account_id)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->WHERE('AccountID', $account_id);
        $this->db->WHERE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }
    public function get_last_ledger_amt_SRtn_sale($id,$account_id,$ord_no)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->WHERE('AccountID', $account_id);
        $this->db->WHERE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
        $this->db->WHERE('OrdinalNo', $ord_no);
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }
    public function get_last_ledger_amt_gst($id,$account_id)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $fy);
        $this->db->WHERE('AccountID', $account_id);
        $this->db->WHERE('VoucherID', $id);
        $this->db->LIKE('PassedFrom', "VEHRTNFRESH");
        return $this->db->get(db_prefix() . 'accountledger')->row();
    }
    public function get_stock_item($id)
    {
        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->where('FY', $FY);
        $this->db->where('ItemID', $id);

        return $this->db->get(db_prefix() . 'stockmaster')->row();
    }
    
    public function get_salertn_details($AccountID,$TransID,$ItemID)
    {
        $selected_company = $this->session->userdata('root_company');
        $fy = $this->session->userdata('finacial_year');
        $this->db->where('PlantID', $selected_company);
        $this->db->LIKE('FY', $fy);
        $this->db->WHERE('AccountID', $AccountID);
        $this->db->WHERE('TransID', $TransID);
        $this->db->WHERE('ItemID', $ItemID);
        $this->db->WHERE('TType', "R");
        $this->db->WHERE('TType2', "Fresh");
        return $this->db->get(db_prefix() . 'history')->row();
    }
    public function increment_next_number()
    {
        // Update next CHALLAN number in settings
       $FY = $this->session->userdata('finacial_year'); 
      $selected_company = $this->session->userdata('root_company');
            if($selected_company == 1){
                $this->db->where('name', 'next_vehicle_return_number_for_cspl');
                
            }elseif($selected_company == 2){
                $this->db->where('name', 'next_vehicle_return_number_for_cff');
               
            }elseif($selected_company == 3){
                $this->db->where('name', 'next_vehicle_return_number_for_cbu');
                
            }
        $this->db->set('value', 'value+1', false);
        $this->db->WHERE('FY', $FY);
        $this->db->update(db_prefix() . 'options');
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
    public function vehicle_return_table($data){
          $selected_company = $this->session->userdata('root_company');
          $year = $this->session->userdata('finacial_year');
          $from_date = to_sql_date($data["from_date"]);
          $to_date = to_sql_date($data["to_date"]);

            $this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.Crates  as return_crates,tblvehiclereturn.Transdate as returnTransdate,tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln');
           $this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
            
            $this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
            $this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
            $this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
            $this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
            $this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
            $this->db->where('tblvehiclereturn.Transdate  BETWEEN "'. $from_date. ' 00:00:00" and "'. $to_date.' 23:59:59"');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.FY', $year);
            $this->db->order_by('tblvehiclereturn.ReturnID','desc');
            return $this->db->get('tblvehiclereturn')->result_array();
            // echo $this->db->last_query();die; 
    }
    public function get_unique_vehicle_return($id){
        $selected_company = $this->session->userdata('root_company');
          $year = $this->session->userdata('finacial_year');
            $this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.Crates as return_crates,tblvehiclereturn.Transdate as returnTransdate,tblchallanmaster.*,tblchallanothervehicles.OtherVehicleDetails,tblroute.name,tblroute.KM,tblvehicle.VehicleCapacity, users_table_a.firstname as driver_fn, users_table_a.lastname AS driver_ln,users_table_b.firstname as loader_fn, users_table_b.lastname AS loader_ln, users_table_c.firstname as Salesman_fn, users_table_c.lastname AS Salesman_ln');
            $this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
          
            $this->db->join('tblstaff users_table_a', 'tblchallanmaster.DriverID = users_table_a.AccountID', 'left');
            $this->db->join('tblstaff users_table_b', 'tblchallanmaster.LoaderID = users_table_b.AccountID', 'left');
            $this->db->join('tblstaff users_table_c', 'tblchallanmaster.SalesmanID = users_table_c.AccountID', 'left');
            $this->db->join('tblroute ', 'tblchallanmaster.RouteID = tblroute.RouteID AND tblroute.PlantID = '.$selected_company, 'left');
            $this->db->join('tblvehicle ', 'tblchallanmaster.VehicleID = tblvehicle.VehicleID', 'left');
            $this->db->join('tblchallanothervehicles ', 'tblchallanmaster.ChallanID = tblchallanothervehicles.ChallanID AND tblchallanothervehicles.PlantID = '.$selected_company.' AND tblchallanothervehicles.FY = '.$year, 'left');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.ReturnID', $id);
            $this->db->where('tblvehiclereturn.FY', $year);
            // $this->db->group_by('tblvehiclereturn.ChallanID');
            return $this->db->get('tblvehiclereturn')->row_array();
    }
    public function get_all_expense_vehicle_return($id){
        $selected_company = $this->session->userdata('root_company');
          $year = $this->session->userdata('finacial_year');
          
            $this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.Crates as return_crates,tblstaff.firstname,tblstaff.lastname,tblstaff.current_address,tblvehiclereturn.Transdate as returnTransdate,tblchallanmaster.*,expense_d.Amount as expense_Amount,expense_d.AccountID as Aid');
             $this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
          
            $this->db->join('tblaccountledger expense_d', 'tblvehiclereturn.ReturnID = expense_d.VoucherID AND expense_d.TType = "D" AND expense_d.PassedFrom = "VEHRTNEXP" AND expense_d.PlantID = '.$selected_company.' AND expense_d.FY = '.$year, 'left');
            $this->db->join('tblstaff ', 'expense_d.AccountID = tblstaff.AccountID ', 'left');
           
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.ReturnID', $id);
            $this->db->where('tblvehiclereturn.FY', $year);
            return $this->db->get('tblvehiclereturn')->result_array();
    }
    public function get_all_payment_vehicle_return($id){
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
          
            $this->db->select('tblvehiclereturn.ReturnID,tblvehiclereturn.Crates as return_crates,tblclients.company,tblclients.address,tblvehiclereturn.Transdate as returnTransdate,tblchallanmaster.*,payment_recipt.Amount as payment_recipt_Amount,payment_recipt.AccountID as Aid');
             $this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
          
            $this->db->join('tblaccountledger payment_recipt', 'tblvehiclereturn.ReturnID = payment_recipt.VoucherID AND payment_recipt.TType = "C"  AND payment_recipt.PassedFrom = "VEHRTNPYMTS" AND payment_recipt.PlantID = '.$selected_company.' AND payment_recipt.FY = '.$year, 'left');
            $this->db->join('tblclients ', 'payment_recipt.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.ReturnID', $id);
            $this->db->where('tblvehiclereturn.FY', $year);
            return $this->db->get('tblvehiclereturn')->result_array();
    }
    
    public function get_sum_payment_vehicle_return($id){
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
          
            $this->db->select('SUM(tblaccountledger.Amount) AS creditsum');
            $this->db->where('tblaccountledger.PlantID', $selected_company);
            $this->db->where('tblaccountledger.VoucherID', $id);
            $this->db->where('tblaccountledger.FY', $year);
            $this->db->where('tblaccountledger.TType', "C");
            $this->db->where('tblaccountledger.PassedFrom', "VEHRTNPYMTS");
            return $this->db->get('tblaccountledger')->row();
    }
    public function get_sum_saleRtn_vehicle_return($id){
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
          
            $this->db->select('SUM(tblhistory.ChallanAmt) AS salertnsum');
            $this->db->where('tblhistory.PlantID', $selected_company);
            $this->db->where('tblhistory.OrderID', $id);
            $this->db->where('tblhistory.FY', $year);
            $this->db->where('tblhistory.TType', "R");
            $this->db->where('tblhistory.TType2', "Fresh");
            return $this->db->get('tblhistory')->row();
    }
    public function get_all_crate_vehicle_return($id){
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
        $selected_company = $this->session->userdata('root_company');
         $year = $this->session->userdata('finacial_year');
      
            // $this->db->select('tblvehiclereturn.ReturnID,tblordermaster.AccountID as act_id,tblchallanmaster.*,tblordermaster.*,tblvehiclereturn.Crates as return_crates,tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,crate_data.Qty as crate_data_qty,opening_crates.Qty,opening_crates.TType');
            $this->db->select('tblvehiclereturn.ReturnID,tblordermaster.AccountID as act_id,tblchallanmaster.*,tblordermaster.*,tblvehiclereturn.Crates as return_crates,tblordermaster.Crates as crates_data,tblclients.company,tblclients.address,opening_crates.Qty,opening_crates.TType');
            $this->db->join('tblchallanmaster ', 'tblchallanmaster.ChallanID = tblvehiclereturn.ChallanID AND tblchallanmaster.PlantID = '.$selected_company.' AND tblchallanmaster.FY = '.$year, 'left');
          
            $this->db->join('tblordermaster ', 'tblchallanmaster.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
            //  $this->db->join('tblaccountcrates crate_data', 'tblvehiclereturn.ReturnID = crate_data.VoucherID AND crate_data.TType = "C"  AND crate_data.PassedFrom = "VehicleReturn" AND crate_data.PlantID = '.$selected_company.' AND crate_data.FY = '.$year, 'left');
            $this->db->join('tblaccountcrates opening_crates', 'opening_crates.PassedFrom = "OPENCRATES" AND tblordermaster.AccountID = opening_crates.AccountID AND opening_crates.PlantID = '.$selected_company.' AND opening_crates.FY = '.$year, 'left');
           
            $this->db->join('tblclients ', 'tblordermaster.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
             $this->db->where('tblvehiclereturn.ReturnID', $id);
            $this->db->where('tblvehiclereturn.FY', $year);
            $data = $this->db->get('tblvehiclereturn')->result_array();
            
            
            
            $this->db->select('crate_data.AccountID as act_id,crate_data.Qty as crate_data_qty,tblordermaster.OrderID AS ORD');
            $this->db->join('tblaccountcrates crate_data', 'tblvehiclereturn.ReturnID = crate_data.VoucherID AND crate_data.TType = "C"  AND crate_data.PassedFrom = "VEHRTNCRATES" AND crate_data.PlantID = '.$selected_company.' AND crate_data.FY = '.$year, 'left');
            $this->db->join('tblordermaster ', 'tblvehiclereturn.ChallanID = tblordermaster.ChallanID AND tblordermaster.PlantID = '.$selected_company.' AND tblordermaster.FY = '.$year, 'left');
            $this->db->where('tblvehiclereturn.PlantID LIKE', $selected_company);
            $this->db->where('tblvehiclereturn.ReturnID', $id);
            $this->db->where('tblvehiclereturn.FY', $year);
            $data_next = $this->db->get('tblvehiclereturn')->result_array();
       
            foreach($data as $key=>$value){
                foreach($data_next as $value_data){
                    if($value['act_id'] ==$value_data['act_id'] && $value['OrderID'] == $value_data['ORD']){
                       
                        $data[$key]['crate_data_qty'] = $value_data['crate_data_qty'];
                    }
                }
            }
             $i = 0;
             foreach($data as $value){
                
                  $this->db->select('sum(Qty) as credit_crate,AccountID');
                  $this->db->where('tblaccountcrates.PlantID', $selected_company);
                  $this->db->where('tblaccountcrates.FY', $year);
                  $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
                  $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
                  $this->db->where('tblaccountcrates.TType LIKE', 'C');
                  $this->db->group_by('AccountID');
                  $credit_crate = $this->db->get('tblaccountcrates')->result_array();
                    
                  $this->db->select('sum(Qty) as debit_crate,AccountID');
                  $this->db->where('tblaccountcrates.PlantID', $selected_company);
                  $this->db->where('tblaccountcrates.FY', $year);
                  $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
                  $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
                  $this->db->where('tblaccountcrates.TType LIKE', 'D');
                  $this->db->group_by('AccountID');
                  $debit_crate = $this->db->get('tblaccountcrates')->result_array();
                    
                  $balance = $debit_crate[0]['debit_crate'] - $credit_crate[0]['credit_crate'];
                    
                    
                   if($value['TType'] == 'D'){
                       $data[$i]['balance_crates'] = $balance+$value['Qty'];
                   }else{
                       $data[$i]['balance_crates'] = $balance-$value['Qty'];
                   }
            $i++; }
            return $data;
    }
    function get_all_crate_vehicle_return_details($vRtnId){
        
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
        $this->db->select('*');
        $this->db->where('tblaccountcrates.PlantID', $selected_company);
        $this->db->where('tblaccountcrates.FY', $year);
        $this->db->where('tblaccountcrates.VoucherID', $vRtnId);
        return $this->db->get('tblaccountcrates')->result_array();
    }
    
    function get_crate_vehicle_return_new_added($vRtnId,$challanID){
        
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
        
        $AccountID = array();
          $this->db->select('*');
          $this->db->where('tblordermaster.PlantID', $selected_company);
          $this->db->where('tblordermaster.FY', $year);
          $this->db->where('tblordermaster.ChallanID', $challanID);
          $AccountList = $this->db->get('tblordermaster')->result_array();
            foreach ($AccountList as $key => $value) {
               array_push($AccountID, $value["AccountID"]);
            }
            
        $this->db->select('*');
        $this->db->join('tblclients ', 'tblaccountcrates.AccountID = tblclients.AccountID AND tblclients.PlantID = '.$selected_company, 'left');
        $this->db->where('tblaccountcrates.PlantID', $selected_company);
        $this->db->where('tblaccountcrates.FY', $year);
        $this->db->where('tblaccountcrates.VoucherID', $vRtnId);
        $this->db->where_not_in('tblaccountcrates.AccountID', $AccountID);
        $crate_list = $this->db->get('tblaccountcrates')->result_array();
        foreach($crate_list as $key=>$value){
            
            // for open Qty
            $this->db->select('*');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.FY', $year);
            $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
            $this->db->where('tblaccountcrates.PassedFrom', 'OPENCRATES');
            $openCrate = $this->db->get('tblaccountcrates')->row();
            $crate_list[$key]['open_qty'] = $openCrate->Qty;
            
            $this->db->select('sum(Qty) as credit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.FY', $year);
            $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'C');
            $this->db->group_by('AccountID');
            $credit_crate = $this->db->get('tblaccountcrates')->result_array();
                    
            $this->db->select('sum(Qty) as debit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.FY', $year);
            $this->db->where('tblaccountcrates.AccountID', $value['AccountID']);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'D');
            $this->db->group_by('AccountID');
            $debit_crate = $this->db->get('tblaccountcrates')->result_array();
                    
            $balance = $debit_crate[0]['debit_crate'] - $credit_crate[0]['credit_crate'];
            $crate_list[$key]['balance_crates'] = $balance - $openCrate->Qty + $value["Qty"];
            $crate_list[$key]['currbalance_crates'] = $balance - $openCrate->Qty;
        }
        return $crate_list;
    }
    
    function getaccounts($postData){

    $response = array();
    $selected_company = $this->session->userdata('root_company');
    $year = $this->session->userdata('finacial_year');
    $where_clients = '';
    
     if(isset($postData['search']) ){
       
       $q = $postData['search'];
       $this->db->select(db_prefix() . 'clients.*,opening_crates.Qty');
       $where_clients .= '(company LIKE "%' . $q . '%" ESCAPE \'!\' OR StationName LIKE "%' . $q . '%" ESCAPE \'!\' OR tblclients.AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR address LIKE "%' . $q. '%" ESCAPE \'!\' OR Address3 LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'clients.active = 1 AND ' . db_prefix() . 'clients.SubActGroupID = 60001004';
       $this->db->join('tblaccountcrates opening_crates', 'opening_crates.PassedFrom = "OPENCRATES" AND tblclients.AccountID = opening_crates.AccountID AND opening_crates.PlantID = tblclients.PlantID AND opening_crates.FY = '.$year, 'left');
       $this->db->where($where_clients);
       $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
       $records = $this->db->get(db_prefix() . 'clients')->result();

        foreach($records as $row ){
           
            $this->db->select('sum(Qty) as credit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $row->AccountID);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'C');
            $this->db->group_by('AccountID');
            $credit_crate = $this->db->get('tblaccountcrates')->row();
                    
            $this->db->select('sum(Qty) as debit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $row->AccountID);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'D');
            $this->db->group_by('AccountID');
            $debit_crate = $this->db->get('tblaccountcrates')->row();
                    
            $balance = $debit_crate->debit_crate - $credit_crate->credit_crate;
                if($row->TType == 'D'){
                    $balance_crates = $balance + $row->Qty;
                }else{
                    $balance_crates = $balance - $row->Qty;
                }
           
            $response[] = array("label"=>$row->company,"value"=>$row->AccountID,"address"=>$row->address,"openCrates"=>$row->Qty,"BalCrates"=>$balance_crates);
        }

     }

     return $response;
  }
    
    function staffgetaccounts($postData){

    $response = array();
    $selected_company = $this->session->userdata('root_company');
    $year = $this->session->userdata('finacial_year');
    $where_clients = '';
    
     if(isset($postData['search']) ){
       
       $q = $postData['search'];
       $this->db->select(db_prefix() . 'staff.*');
       $where_clients .= '(AccountID LIKE "%' . $q . '%" ESCAPE \'!\' OR firstname LIKE "%' . $q . '%" ESCAPE \'!\' OR lastname LIKE "%' . $q . '%" ESCAPE \'!\' ) ';
       $this->db->where($where_clients);
       $records = $this->db->get(db_prefix() . 'staff')->result();
        foreach($records as $row ){
            $fullname = $row->firstname." ".$row->lastname;
            $response[] = array("label"=>$fullname,"value"=>$row->AccountID,"address"=>$row->current_address);
        }
     }

     return $response;
    }
    
    public function get_Account_Details($postData)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
        $AccountID = $postData['AccountID'];
        $this->db->select(db_prefix() . 'clients.*,opening_crates.Qty');
        $this->db->join('tblaccountcrates opening_crates', 'opening_crates.PassedFrom = "OPENCRATES" AND tblclients.AccountID = opening_crates.AccountID AND opening_crates.PlantID = tblclients.PlantID AND opening_crates.FY = '.$year, 'left');
        $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);
        $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);
        $result =  $this->db->get(db_prefix() . 'clients')->row();
        $result->balance_crates = 0;
            $this->db->select('sum(Qty) as credit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $result->AccountID);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'C');
            $this->db->group_by('AccountID');
            $credit_crate = $this->db->get('tblaccountcrates')->row();
                    
            $this->db->select('sum(Qty) as debit_crate,AccountID');
            $this->db->where('tblaccountcrates.PlantID', $selected_company);
            $this->db->where('tblaccountcrates.AccountID', $result->AccountID);
            $this->db->where('tblaccountcrates.PassedFrom !=', 'OPENCRATES');
            $this->db->where('tblaccountcrates.TType LIKE', 'D');
            $this->db->group_by('AccountID');
            $debit_crate = $this->db->get('tblaccountcrates')->row();
                    
            $balance = $debit_crate->debit_crate - $credit_crate->credit_crate;
                if($result->TType == 'D'){
                    $balance_crates = $balance + $result->Qty;
                    $result->balance_crates = $balance_crates;
                }else if($result->TType == 'c'){
                    $balance_crates = $balance - $result->Qty;
                    $result->balance_crates = $balance_crates;
                }
        return $result;
    }
    
    public function get_staffAccount_Details($postData)
    {
        $selected_company = $this->session->userdata('root_company');
        $year = $this->session->userdata('finacial_year');
        $AccountID = $postData['AccountID'];
        $this->db->select(db_prefix() . 'staff.*');
        $this->db->where(db_prefix() . 'staff.AccountID', $AccountID);
        $result =  $this->db->get(db_prefix() . 'staff')->row();
        return $result;
    }
    
}