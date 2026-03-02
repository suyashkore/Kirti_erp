<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            
            <div class="panel_s">
               <div class="panel-body">
                   <?php
                   //echo "<pre>";
                   echo count($travel_reports);
                   //print_r($travel_reports);
                   
                   ?>
                   <table class="table table-striped table-bordered daily_report">
                       <thead>
                           <th>id</th>
                           <th>location_list</th>
                           <th>location_trav</th>
                           <th>location_name_list</th>
                           <th>battery_level</th>
                           <th>device_information</th>
                           <th>GPS_Status</th>
                       </thead>
                       <tbody>
                           <?php
                           $j = 1;
                            $dist_cal_new = 0.00;
                            $pi80 = M_PI / 180; 
                            $r = 6372.797; // mean radius of Earth in km 
                           foreach ($travel_reports as $key => $value) {
                            ?>

                           <tr>
                               <td><?php echo $value["id"]; ?></td>
                               <td><?php 
                                
                                
                                if($j == 1){
                                    $value_array = explode(",", $value["location_list"]);
                                    $lat1 = $value_array["0"];
                                    $lon1 = $value_array["1"];
                                    $lat1 *= $pi80; 
                                    $lon1 *= $pi80;
                                    /*$lat1 = deg2rad($lat1);
                                    $lon1 = deg2rad($lon1);*/
                                    
                                       
                                }
                                
                                if($j>1){
                       
                                    $value_array = explode(",", $value["location_list"]);
                                    $lat2 = $value_array["0"];
                                    $lon2 = $value_array["1"];
                                    /*$lat2 = deg2rad($lat2);
                                    $lon2 = deg2rad($lon2);*/
                                    $lat2 *= $pi80; 
                                    $lon2 *= $pi80;
                                    
                                    if($lat1 == $lat2 && $lon1 == $lon2){
                                        echo "empty";
                                    }else{
                                        /*$theta = $lon1 - $lon2;
                                        if($theta == 0){
                                            
                                        }else{
                                            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                                            $dist = acos($dist);
                                            $dist = rad2deg($dist);
                                            $miles = $dist * 60 * 1.1515;
                                            $new_dist = $miles * 1.609344;
                                            if($new_dist){
                                                $dist_cal_new =  $dist_cal_new  + $new_dist;
                                            }
                                            
                                            $lat1 = $lat2;
                                            $lon1 = $lon2;
                                            echo $dist_cal_new;
                                        }*/
                                        
                                       /* $dlong = $lon2 - $lon1;
                                       $dlati = $lat2 - $lat1;
                                         
                                       $val = pow(sin($dlati/2),2)+cos($lat1)*cos($lat2)*pow(sin($dlong/2),2);
                                         
                                       $res = 2 * asin(sqrt($val));
                                         
                                       $radius = 3958.756;
                                         
                                       $final_dist = $res*$radius;
                                        $lat1 = $lat2;
                                            $lon1 = $lon2;
                                            $dist_cal_new =  $dist_cal_new  + $final_dist;
                                            echo $dist_cal_new;*/
                                        $dlat = $lat2 - $lat1; 
                                        $dlon = $lon2 - $lon1; 
                                        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2); 
                                        $c = 2 * atan2(sqrt($a), sqrt(1 - $a)); 
                                        $km = $r * $c; 
                                        
                                        $lat1 = $lat2;
                                            $lon1 = $lon2;
                                            $dist_cal_new =  $dist_cal_new  + $km;
                                            echo $dist_cal_new;
                                     
                                    }
                                    
                               }
                               
                               $j++;
                               
                                ?></td>
                               <td><?php echo $value["location_trav"]; ?></td>
                               <td><?php echo $value["location_list"]; ?></td>
                               <td><?php echo $value["battery_level"]; ?></td>
                               <td><?php echo $value["created_date"]; ?></td>
                               <td><?php echo $value["GPS_Status"]; ?></td>
                           </tr>
                           <?php } ?>
                       </tbody>
                   </table>
                </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>