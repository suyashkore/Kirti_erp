<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

<!--<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />-->
<!--<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>-->
<!--<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />-->


<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .dt-buttons {
    width: 150px;
    display: contents;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:active {
    outline: none;
    background-color: #0c0c0c;
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #2b2b2b), color-stop(100%, #0c0c0c));
    background: -webkit-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);
    background: -moz-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);
    background: -ms-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);
    background: -o-linear-gradient(top, #2b2b2b 0%, #0c0c0c 100%);
    background: linear-gradient(to bottom, #2b2b2b 0%, #0c0c0c 100%);
     box-shadow: inset 0 0 0px #fff; 
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    color: white !important;
    border: 0px solid #ffffff;
    background-color: #ffffff;
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ffffff), color-stop(100%, #ffffff));
    background: -webkit-linear-gradient(top, #ffffff 0%, #ffffff 100%);
    background: -moz-linear-gradient(top, #585858 0%, #111 100%);
    background: -ms-linear-gradient(top, #585858 0%, #111 100%);
    background: -o-linear-gradient(top, #585858 0%, #111 100%);
    background: linear-gradient(to bottom, #ffffff 0%, #ffffff 100%);
}
a:focus {
     outline: 0px auto -webkit-focus-ring-color; 
    outline-offset: -2px;
}
div#wrapper {
    min-height: 100% !important;
}
    </style>

<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-7">
            
            <div class="panel_s">
               <div class="panel-body">
                  
                  <div class="clearfix"></div>
                  <hr class="hr-panel-heading" />
                  
                  <div class="row ">
                    
                    <!--<div class="col-md-3">
                      <?php echo render_select('status',$staff,array('staffid',array('firstname','lastname')),'Staff','',array(),array(),'','',false); ?>
                    </div>-->
                    <div class="col-md-4 leads-filter-column" >
                    <?php
                    $selected_staff =  $this->uri->segment('5');
                    ?>
                    <?php echo render_select('staff',$staff,array('staffid',array('firstname','lastname')),'staff',$selected_staff,array(),array(),'','',false); ?>
                    </div>
                    <div class="col-md-4 leads-filter-column">
                    <?php
                       $selected_date =  $this->uri->segment('4');
                       $newselecteddate = strtotime($selected_date);
                       $formatted_date = date('d/m/Y', $newselecteddate);
                    ?>
                     <?php echo render_date_input('report_date','Date',$formatted_date); ?>
                    </div>
                    <div class="col-md-3">
                        <br>
                        <span class="pull-bot">
                            <button name="add" class="btn show_result1 btn-success" onclick="travel_data_filter(); return false;" type="button">Show</button>
                        </span>
                    </div>
                    <!--<div class="col-md-3 leads-filter-column">
                      <?php echo render_select('responsible_admin',$staff_list,array('staffid',array('firstname','lastname')),'responsible_admin'); ?>
                    </div>
                    
                    <div class="col-md-2 leads-filter-column">
                      
                        <div class="form-group">
                            <label class="control-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Non selected</option>
                                <option value="1">Active</option>
                                <option value="0">DeActive</option>
                            </select>
                        </div>
                        
                    </div>-->
                               
                </div>
                  
                  <hr class="hr-panel-heading" />
                 
                  
                  <div class="clearfix mtop20"></div>
                  
                  
                  <!--?php
                    
                    
                     $table_data = array();
                     $_table_data = array(
                      
                        array(
                         'name'=>"Staff Name",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),
                         /*array(
                         'name'=>"Firm Name",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-company')
                        ),*/
                         
                         /*array(
                         'name'=>'KM',
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-active')
                        ),*/
                        array(
                         'name'=>"location",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-station')
                        ),
                        array(
                         'name'=>"Battery",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-station')
                        ),
                        array(
                         'name'=>"Mobile",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-groups')
                        ),
                        array(
                         'name'=>"GPS Status",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-state')
                        ),
                        array(
                         'name'=>"API Time",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-town')
                        ),
                        array(
                         'name'=>"Date",
                         'th_attrs'=>array('class'=>'toggleable', 'id'=>'th-sales_person')
                        ),
                      );
                     foreach($_table_data as $_t){
                      array_push($table_data,$_t);
                     }

                     $custom_fields = get_custom_fields('customers',array('show_on_table'=>1));
                     foreach($custom_fields as $field){
                      array_push($table_data,$field['name']);
                     }
                     
                     
                     $table_data = hooks()->apply_filters('customers_table_columns', $table_data);

                     render_datatable($table_data,'clients',[],[
                           'data-last-order-identifier' => 'customers',
                           'data-default-order'         => get_table_last_order('customers'),
                     ]);
                     ?-->
                     
                    <div class="">
                        <table id="example5" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Staff Name</th>
                                    <th>Location</th>
                                    <th>Battery</th>
                                    <th>Mobile</th>
                                    <th>GPS Status</th>
                                    <th>API Time</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($travel_report as $row){
                                    $staffquery = $this->db->get_where('tblstaff', array('staffid' => $row['staff_id']));
                                    $result = $staffquery->row_array();
                                    
                                    $staffname = $result['firstname'] . ' ' . $result['lastname'];
                                ?>
                                <tr>
                                    <td><?php echo $staffname ?></td>
                                    <td><?php echo $row['location_name_list'] ?></td>
                                    <td><?php echo $row['battery_level'] ?></td>
                                    <td><?php echo $row['device_information'] ?></td>
                                    <td><?php echo $row['GPS_Status'] ?></td>
                                    <td><?php echo date('H:i:s', strtotime($row['created_date'])) ?></td>
                                    <td><?php echo $row['date'] ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>    
                    </div>
                    
               </div>
            </div>
         </div>
         <div class="col-md-5 " id="map_container_parent" style="padding: 0px 5px 5px 5px;">
             <!--<div class="panel_s">-->
             <!--  <div class="panel-body">-->
                  
                  <!--<div class="clearfix"></div>-->
             <div id="map0" style="box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset;position: fixed;width:40.4%;height:88%;margin-top: 7px;"></div>
            <!-- </div>-->
            <!--</div>-->
         </div>
         
      </div>
   </div>
</div>


<?php init_tail(); ?>

<script>

/*$(function(){
       var CustomersServerParams = {};
       $.each($('._hidden_inputs._filters input'),function(){
          CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
      });
       CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
       CustomersServerParams['staff'] = '[name="staff"]';
       CustomersServerParams['report_date'] = '[name="report_date"]';
    //   CustomersServerParams['division'] = '[name="division"]';
    //   CustomersServerParams['responsible_admin'] = '[name="responsible_admin"]';
    //   CustomersServerParams['status'] = '[name="status"]';
       
       var tAPI = initDataTable('.table-clients', admin_url+'timesheets/travel_report_table', [0], [0], CustomersServerParams,<!?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0,'asc'))); ?>);
       $('input[name="exclude_inactive"]').on('change',function(){
           tAPI.ajax.reload();
       });
       $('select[name="staff"]').on('change',function(){
           tAPI.ajax.reload();
       });
       $('input[name="report_date"]').on('change',function(){
           tAPI.ajax.reload();
       });
    //   $('select[name="division"]').on('change',function(){
    //       tAPI.ajax.reload();
    //   });
    //   $('select[name="responsible_admin"]').on('change',function(){
    //       tAPI.ajax.reload();
    //   });
    //   $('select[name="status"]').on('change',function(){
    //       tAPI.ajax.reload();
    //   });
        
       
   });*/
   
   
   /*$(function() {
        var CustomersServerParams = {};
        $.each($('._hidden_inputs._filters input'), function() {
            CustomersServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
        });
        CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
        CustomersServerParams['staff'] = '[name="staff"]';
        CustomersServerParams['report_date'] = '[name="report_date"]';
       
        var tAPI = initDataTable('.table-clients', admin_url + 'timesheets/travel_report_table', [0], [0], CustomersServerParams, <!?php echo hooks()->apply_filters('customers_table_default_order', json_encode(array(0, 'asc'))); ?>);
    
        $('input[name="exclude_inactive"]').on('change', function() {
            tAPI.ajax.reload();
        });
        $('select[name="staff"]').on('change', function() {
            tAPI.ajax.reload();
        });
        $('input[name="report_date"]').on('change', function() {
            tAPI.ajax.reload();
        });
        
        
        var locationData = <!?php echo json_encode($aaData); ?>;
        console.log('Location Data from PHP:', locationData);
    
        function calculateDistance(lat1, lon1, lat2, lon2) {
            var R = 6371; // Radius of the earth in km
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a =
                0.5 - Math.cos(dLat) / 2 +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                (1 - Math.cos(dLon)) / 2;
    
            return R * 2 * Math.asin(Math.sqrt(a));
        }
    
        // Function to load the map
        function loadMap(locationData) {
            if (!Array.isArray(locationData) || locationData.length === 0) {
                console.error('No location data available:', locationData);
                return;
            }
    
            // Process location data to map
            var travel_report = locationData.map(function(item) {
                var locationList = item[1]; // Assuming location is in the 2nd index
                if (locationList) {
                    var latLon = locationList.split(',');
                    var latitude = parseFloat(latLon[0]);
                    var longitude = parseFloat(latLon[1]);
    
                    return L.latLng(latitude, longitude);
                } else {
                    console.error('No location found in item:', item);
                    return null;
                }
            }).filter(function(latLng) {
                return latLng !== null;
            });
    
            if (travel_report.length === 0) {
                console.error('No valid locations to display on the map.');
                return;
            }
    
            var totalDistance = 0;
            for (var i = 0; i < travel_report.length - 1; i++) {
                totalDistance += calculateDistance(
                    travel_report[i].lat, travel_report[i].lng,
                    travel_report[i + 1].lat, travel_report[i + 1].lng
                );
            }
            console.log("Calculated Distance: " + totalDistance.toFixed(2) + " km");
    
            var map = L.map('map0').setView(travel_report[0], 14);
    
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);
    
            locationData.forEach(function(item) {
                var locationList = item[1]; // Assuming location is in the 2nd index
                if (locationList) {
                    var latLon = locationList.split(',');
                    var latitude = parseFloat(latLon[0]);
                    var longitude = parseFloat(latLon[1]);
    
                    var marker = L.marker([latitude, longitude]).addTo(map);
                    marker.bindPopup("<b>" + (item[0] || 'Location') + "</b><br>Latitude: " + latitude + "<br>Longitude: " + longitude);
                }
            });
    
            L.polyline(travel_report, { weight: 3, opacity: 1, color: 'black' }).addTo(map);
        }
    
        loadMap(locationData);
    });*/

	
    
   
   function customers_bulk_action(event) {
       var r = confirm(app.lang.confirm_action_prompt);
       if (r == false) {
           return false;
       } else {
           var mass_delete = $('#mass_delete').prop('checked');
           var ids = [];
           var data = {};
           if(mass_delete == false || typeof(mass_delete) == 'undefined'){
               data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
               if (data.groups.length == 0) {
                   data.groups = 'remove_all';
               }
           } else {
               data.mass_delete = true;
           }
           var rows = $('.table-clients').find('tbody tr');
           $.each(rows, function() {
               var checkbox = $($(this).find('td').eq(0)).find('input');
               if (checkbox.prop('checked') == true) {
                   ids.push(checkbox.val());
               }
           });
           data.ids = ids;
           $(event).addClass('disabled');
           setTimeout(function(){
             $.post(admin_url + 'clients/bulk_action', data).done(function() {
              window.location.reload();
          });
         },50);
       }
   }
   
   
    /*function travel_data_filter() {
        
    var staff = $('select[name="staff"]').val();
    var reportDate = $('input[name="report_date"]').val();
    
    var formattedDate = moment(reportDate, 'DD/MM/YYYY').format('YYYY-MM-DD');

    $.ajax({
        url: '<?php echo admin_url("timesheets/travel_report_table_filter"); ?>',
        type: 'POST',
        dataType: 'json',
        data: {
            staff: staff,
            report_date: reportDate
        },
        success: function(response) {
            if (response.status === 'success') {
                // Construct the redirect URL
                var redirectUrl = '<?php echo admin_url("timesheets/reports_new1/"); ?>' + formattedDate + '/' + staff;
                window.location.href = redirectUrl;
            } else {
                alert('No data found for the selected filters.');
                // Optionally redirect to a different URL or show a message
                // var redirectUrl = '<?php echo admin_url("timesheets/reports_new1/"); ?>' + formattedDate + '/' + staff;
                // window.location.href = redirectUrl;
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response Text:', xhr.responseText);
            alert('An error occurred while fetching data.');
        }
    });
}*/

function travel_data_filter() {
    var staff = $('select[name="staff"]').val();
    var reportDate = $('input[name="report_date"]').val();

    // Format the date to YYYY-MM-DD
    var formattedDate = moment(reportDate, 'DD/MM/YYYY').format('YYYY-MM-DD');

    $.ajax({
        url: '<?php echo admin_url("timesheets/travel_report_table_filter"); ?>',  
        type: 'POST',
        dataType: 'json',
        data: {
            staff: staff,
            report_date: reportDate
        },
        success: function(response) {
            if (response.status === 'success') {
                // Construct the redirect URL
                var redirectUrl = '<?php echo admin_url("timesheets/reports_new1/"); ?>' + formattedDate + '/' + staff;

                // Debugging: Print the redirect URL
                console.log('Redirect URL:', redirectUrl);

                // Redirect to the new URL
                window.location.href = redirectUrl;
            } else {
                alert('No data found for the selected filters.');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.error('Response Text:', xhr.responseText);
            alert('An error occurred while fetching data.');
        }
    });
}





    
    

    /*var locationData = <!?php echo json_encode($travel_report); ?>;
    // var locationData = <!?php echo json_encode($travel_report_table_filter); ?>;
    console.log('Location Data from PHP:', locationData);
    
    if (!Array.isArray(locationData)) {
        locationData = [];
    }
    
    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the earth in km
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a =
            0.5 - Math.cos(dLat) / 2 +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            (1 - Math.cos(dLon)) / 2;
    
        return R * 2 * Math.asin(Math.sqrt(a));
    }
    
    function loadMap(locationData) {
        if (!Array.isArray(locationData) || locationData.length === 0) {
            console.error('No location data available:', locationData);
            return;
        }
    
        var travel_report = locationData.map(function(item) {
            var locationList = item.location_list; 
            if (locationList) {
                var latLon = locationList.split(',');
                var latitude = parseFloat(latLon[0]);
                var longitude = parseFloat(latLon[1]);
    
                return L.latLng(latitude, longitude);
            } else {
                console.error('No location_list found in item:', item);
                return null;
            }
        }).filter(function(latLng) {
            return latLng !== null;
        });
    
        if (travel_report.length === 0) {
            console.error('No valid locations to display on the map.');
            return;
        }
    
        var totalDistance = 0;
        for (var i = 0; i < travel_report.length - 1; i++) {
            totalDistance += calculateDistance(
                travel_report[i].lat, travel_report[i].lng,
                travel_report[i + 1].lat, travel_report[i + 1].lng
            );
        }
        console.log("Calculated Distance: " + totalDistance.toFixed(2) + " km");
    
        var map = L.map('map0').setView(travel_report[0], 14);
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
    
        locationData.forEach(function(item) {
            var locationList = item.location_list; 
            if (locationList) {
                var latLon = locationList.split(',');
                var latitude = parseFloat(latLon[0]);
                var longitude = parseFloat(latLon[1]);
    
                var marker = L.marker([latitude, longitude]).addTo(map);
                marker.bindPopup("<b>" + (item.location_name_list || 'Location') + "</b><br>Latitude: " + latitude + "<br>Longitude: " + longitude);
            }
        });
    
        L.polyline(travel_report, { weight: 3, opacity: 1, color: 'black' }).addTo(map);
    }
    
    loadMap(locationData);*/
    
    var locationData = <?php echo json_encode($travel_report); ?>;
    console.log('Location Data from PHP:', locationData);
    
    if (!Array.isArray(locationData)) {
        locationData = [];
    }
    
    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the earth in km
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a =
            0.5 - Math.cos(dLat) / 2 +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            (1 - Math.cos(dLon)) / 2;
    
        return R * 2 * Math.asin(Math.sqrt(a));
    }
    
    function loadMap(locationData) {
        if (!Array.isArray(locationData) || locationData.length === 0) {
            // console.error('No location data available:', locationData);
            return;
        }
    
        var travel_report = locationData.map(function(item) {
            var locationList = item.location_list; 
            if (locationList) {
                var latLon = locationList.split(',');
                var latitude = parseFloat(latLon[0]);
                var longitude = parseFloat(latLon[1]);
    
                return L.latLng(latitude, longitude);
            } else {
                // console.error('No location_list found in item:', item);
                return null;
            }
        }).filter(function(latLng) {
            return latLng !== null;
        });
    
        if (travel_report.length === 0) {
            // console.error('No valid locations to display on the map.');
            return;
        }
    
        var totalDistance = 0;
        for (var i = 0; i < travel_report.length - 1; i++) {
            totalDistance += calculateDistance(
                travel_report[i].lat, travel_report[i].lng,
                travel_report[i + 1].lat, travel_report[i + 1].lng
            );
        }
        console.log("Calculated Distance: " + totalDistance.toFixed(2) + " km");
    
        var map = L.map('map0').setView(travel_report[0], 14);
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
    
        locationData.forEach(function(item) {
            var locationList = item.location_list; 
            if (locationList) {
                var latLon = locationList.split(',');
                var latitude = parseFloat(latLon[0]);
                var longitude = parseFloat(latLon[1]);
    
                var marker = L.marker([latitude, longitude]).addTo(map);
                marker.bindPopup("<b>" + (item.location_name_list || 'Location') + "</b><br>Latitude: " + latitude + "<br>Longitude: " + longitude);
            }
        });
    
        L.polyline(travel_report, { weight: 3, opacity: 1, color: 'black' }).addTo(map);
    }
    
    loadMap(locationData);


</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>


<!--<script src="https://code.jquery.com/jquery-3.7.1.js"></script>-->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.2/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js"></script>



<script>
    $(document).ready(function() {
        $('#example5').DataTable({
            searching: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: 'Timesheet Report Excel'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    title: 'Timesheet Report'
                }
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                ['10 rows', '25 rows', '50 rows', '100 rows']
            ],
            pageLength: 10,
            language: {
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries to show",
                infoFiltered: "(filtered from _MAX_ total entries)"
            }
        });
    });
    </script>


</body>
</html>
