<?php

	

	defined('BASEPATH') or exit('No direct script access allowed');

	

	class Challan extends AdminController

	{

		public function __construct()

		{

			parent::__construct();

			$this->load->model('invoices_model');

			$this->load->model('order_model');

			$this->load->model('challan_model');

			$this->load->model('clients_model');

			$this->load->model('invoice_items_model');

			$this->load->helper('sales_helper');

		}

		

		/* Get all invoices in case user go on index page */

		public function index($id = '')

		{

			//$this->list_challan($id);

			//$this->challanAddEdit();

			$redUrl = admin_url('challan/challanAddEdit');

			redirect($redUrl);

		}

		

		public function itemlist(){

			$this->load->model('invoice_items_model');

			// POST data

			$postData = $this->input->post();

			

			// Get data

			$data = $this->invoice_items_model->getitem($postData);

			

			echo json_encode($data);

		}

		

		public function challan_list()

		{

			if (!has_permission_new('challan_list', '', 'view')) {

				access_denied('invoices');

			}

			

			$data['title']                = " Challan List";

			$data['bodyclass']            = 'challan-total-manual';

			$this->load->view('admin/challan/challan_list', $data);

		}

		

		public function VehicleUpdate()

		{

			if (!has_permission_new('change_vehicle', '', 'view')) {

				access_denied('invoices');

			}

			

			$data['title']                = "Vehicle Update";

			$data['bodyclass']            = 'challan-total-manual';

			$DriverType = "1000159";

			$data['DriverList']    = $this->clients_model->GetStaffListTypeWise($DriverType);

			$data['ChallanList']          = $this->challan_model->GetChallanList();

			/*echo "<pre>";

				print_r($data['ChallanList']);

			die;*/

			$this->load->view('admin/challan/UpdateVehicle', $data);

		}

		

		/* Get Challan Vehicle by ChallanID / ajax */

		public function GetVehicleByChallan()

		{

			$ChallanID = $this->input->post('ChallanID');

			$ChallanDetails = $this->challan_model->GetVehicleByChallan($ChallanID);

			echo json_encode($ChallanDetails);

		}

		

		/* Update Exiting ItemID / ajax */

		public function UpdateVehicle()

		{

			$UserID = $this->session->userdata('username');

			$VehData = array(

            "UserID2"=>$UserID,

            "Lupdate"=>date('Y-m-d H:i:s')

			);

			if(!empty($this->input->post('NewVehicleNo'))){

				$VehData['VehicleID'] = $this->input->post('NewVehicleNo');

			}

			if(!empty($this->input->post('challan_driver'))){

				$VehData['DriverID'] = $this->input->post('challan_driver');

			}

			$VehicleNo = $this->input->post('VehicleNo');

			$ChallanID = $this->input->post('ChallanID');

			$Result         = $this->challan_model->UpdateVehicle($VehData,$VehicleNo,$ChallanID);

			echo json_encode($Result);

		}

		//==================== Get Vehicle List By DriverID ============================	

		public function GetVehicleListByDriverID()

		{

			$postData = $this->input->post();

			$VehicleData = $this->challan_model->GetVehicleListByDriverID($postData);

			echo json_encode($VehicleData);

		}

		public function accountlist_driver(){

			

			// POST data

			$postData = $this->input->post();

			// Get data

			$data = $this->challan_model->accountlist_driver($postData);

			

			echo json_encode($data);

		}

		

		public function get_Loader_Details(){

			

			// POST data

			$postData = $this->input->post();

			// Get data

			$Account_data = $this->challan_model->get_Loader_Details($postData);

			

			echo json_encode($Account_data);

		}

		public function accountlist_Loader(){

			

			// POST data

			$postData = $this->input->post();

			// Get data

			$data = $this->challan_model->accountlist_Loader($postData);

			

			echo json_encode($data);

		}

		

		public function accountlist_salesMan(){

			

			// POST data

			$postData = $this->input->post();

			// Get data

			$data = $this->challan_model->accountlist_salesMan($postData);

			

			echo json_encode($data);

		}

		

		public function get_Account_Details_salesman(){

			

			// POST data

			$postData = $this->input->post();

			

			// Get data

			$Account_data = $this->challan_model->get_Account_Details_salesman($postData);

			

			echo json_encode($Account_data);

		}

		

		public function GetTaxableTransaction(){

			// POST data

			$postData = $this->input->post();

			// Get data

			$Salesdata = $this->challan_model->GetTaxableTransaction($postData);

			echo json_encode($Salesdata);

		}

		

		

		public function edit_challan($id = '')

		{

			if (!has_permission_new('challan_list', '', 'view')) {

				access_denied('challan');

			}

			$redUrl = admin_url('challan/UpdateChallan/'.$id);

			redirect($redUrl);

			close_setup_menu();

			if ($id == '') {

				$data['title']                = "Create Challan";

				}else {

				$data['title']                = "Update Challan";

				$data['challan']    = $this->challan_model->get($id);

			}

			

			$this->load->model('payment_modes_model');

			$data['invoiceid']            = $id;

			

			$data['routes']    = $this->clients_model->getroute();

			$data['vehicle']    = $this->clients_model->getvehicle();

			$data['bodyclass']            = 'invoices-total-manual';

			$this->load->view('admin/challan/edit_challan', $data);

		}

		

		/* List all invoices datatables */

		public function list_challan($id = '')

		{

			

			if ($this->input->post()) {

				$challan_data = $this->input->post();

			}

			close_setup_menu();

			

			if ($id == '') {

				$data['title']                = "Create Challan";

				}else {

				$data['title']                = "Update Challan";

				$data['challan']    = $this->challan_model->get($id);

			}

			

			$this->load->model('payment_modes_model');

			

			$data['invoiceid']            = $id;

			

			$data['routes']    = $this->clients_model->getroute();

			$data['vehicle']    = $this->clients_model->getvehicle();

			

			$data['bodyclass']            = 'invoices-total-manual';

			$this->load->view('admin/challan/manage', $data);

		}

		

		/* List all invoices datatables */

		public function challanAddEdit($id = '')

		{

			if ($this->input->post()) {

				$challan_data = $this->input->post();

				$challan_data["route"] = $challan_data["challan_route"];

				$challan_data["vehicle"] = $challan_data["challan_vehicle"];

				unset($challan_data["challan_route"]);

				unset($challan_data["challan_vehicle"]);

				

				if (!has_permission('challan', '', 'create')) {

                    access_denied('challan');

				}

                // Check food license expiration for selected orders
                $selected_company = $this->session->userdata('root_company');
                $orderIds = is_array($challan_data["order_id"]) ? $challan_data["order_id"] : array($challan_data["order_id"]);
                
                // Get AccountIDs for the selected orders
                $this->db->select('AccountID');
                $this->db->where_in('OrderID', $orderIds);
                $this->db->where('PlantID', $selected_company);
                $orderAccounts = $this->db->get(db_prefix() . 'ordermaster')->result_array();
                
                $accountIds = array();
                foreach ($orderAccounts as $orderAccount) {
                    $accountIds[] = $orderAccount['AccountID'];
                }
                $accountIds = array_unique($accountIds);
                
                // Check food license expiration
                $expiredAccounts = array();
                if (!empty($accountIds)) {
                    $this->db->select('AccountID, FLNO1, expiry_licence');
                    $this->db->where_in('AccountID', $accountIds);
                    $this->db->where('PlantID', $selected_company);
                    $foodLicenseData = $this->db->get(db_prefix() . 'contacts')->result_array();
                    
                    $today = date('Y-m-d');
                    foreach ($foodLicenseData as $license) {
                        if (!empty($license['expiry_licence'])) {
                            $expiryDate = date('Y-m-d', strtotime($license['expiry_licence']));
                            if ($expiryDate < $today) {
                                // Get customer name
                                $this->db->select('company');
                                $this->db->where('AccountID', $license['AccountID']);
                                $this->db->where('PlantID', $selected_company);
                                $customer = $this->db->get(db_prefix() . 'clients')->row();
                                $expiredAccounts[] = array(
                                    'account_id' => $license['AccountID'],
                                    'customer_name' => $customer ? $customer->company : 'Unknown',
                                    'license_number' => !empty($license['FLNO1']) ? $license['FLNO1'] : 'N/A',
                                    'expiry_date' => $license['expiry_licence']
                                );
                            }
                        }
                    }
                }
                
                // If any account has expired food license, prevent challan creation
                if (!empty($expiredAccounts)) {
                    $errorMsg = "Cannot create challan. Food license expired for the following customers: ";
                    $errorDetails = array();
                    foreach ($expiredAccounts as $expired) {
                        $errorDetails[] = $expired['customer_name'] . " (License: " . $expired['license_number'] . ", Expired: " . date('d/m/Y', strtotime($expired['expiry_date'])) . ")";
                    }
                    $errorMsg .= implode(", ", $errorDetails);
                    set_alert('warning', $errorMsg);
                    $redUrl = admin_url('challan/challanAddEdit');
                    redirect($redUrl);
                    return;
                }

                $challan = $this->challan_model->checkorder($challan_data["order_id"]);

                

                if(empty($challan)){

                    $challan_data["challan_driver"] = strtoupper($challan_data["challan_driver"]);

                    $challan_data["challan_loader"] = strtoupper($challan_data["challan_loader"]);

                    $challan_data["challan_sales_man"] = strtoupper($challan_data["challan_sales_man"]);

                    $challan_data["vahicle_number"] = strtoupper($challan_data["vahicle_number"]);

                    $id = $this->challan_model->AddNewChallan($challan_data);

					if ($id == false) {

						set_alert('warning', 'Stock Not Available...');

						$redUrl = admin_url('challan/challanAddEdit');

						redirect($redUrl);

                        }else{

						set_alert('success', _l('added_successfully', 'Challan'));

						$redUrl = admin_url('challan/challan_list/');

						redirect($redUrl);

					}

					}else{

					set_alert('warning', "Challan already created for this order");

					redirect(admin_url('challan/challan_list'));

				}

			}

			close_setup_menu();

			

			if ($id == '') {

				$data['title']                = "Create Challan";

				}else {

				$data['title']                = "Update Challan";

				$data['challan']    = $this->challan_model->get($id);

			}

			$this->load->model('payment_modes_model');

			$data['invoiceid']            = $id;

			$data['routes']    = $this->clients_model->getroute();

			$data['vehicle']    = $this->clients_model->getvehicle();

			$DriverType = "1000159";

			$data['DriverList']    = $this->clients_model->GetStaffListTypeWise($DriverType);

			$LoaderType = "1000161";

			$data['LoaderList']    = $this->clients_model->GetStaffListTypeWise($LoaderType);

			$SalesManType = "1000163";

			$data['SalesManList']    = $this->clients_model->GetStaffListTypeWise($SalesManType);

			$data['bodyclass']            = 'invoices-total-manual';

			// Add JavaScript directly to data array to ensure it's available
			$data['food_license_check_script'] = true;
			
			

			$data['challan']     = $challan     ?? null;
$data['challan_nu']  = $challan_nu  ?? '';
$data['_is_draft']   = $_is_draft   ?? false;

			$this->load->view('admin/challan/manageNew', $data);

		}

		

//--------------------------- UpdateChallan -------------------------------------------------		

		

		public function UpdateChallan($id = '')

		{

			if (!has_permission_new('challan_list', '', 'view')) {

				access_denied('challan');

			}

			if ($this->input->post()) {

				if (!has_permission_new('challan_list', '', 'edit')) {

					access_denied('challan');

				}

				$data = $this->input->post();

				$id = $this->challan_model->UpdateExistingChallan($data);

				if($id == true){

					set_alert('success', 'Challan Updated Successfully');  

					}else{

					set_alert('warning', 'Something went wrong, please try again later.');

				}

				$redUrl = admin_url('challan/UpdateChallan/'.$data["number"]);

				redirect($redUrl);

			}// If Post data from Front end

			

			close_setup_menu();

			

			if ($id == '') {

				$data['title']                = "Create Challan";

				}else {

				$data['title']                = "Update Challan";

				$Order_item_free = array();

				$Order_item = array();

				$OrderIds = array();

				$AccountIds = array();

				// Existing order

				$data['challan']    = $this->challan_model->get($id);

				

				$challan    = $this->challan_model->getNew($id);

				

				// echo"<pre>";print_r($challan);die;

				foreach ($challan["item_list"] as $key => $code) {

					array_push($Order_item, $code["ItemID"]);

					array_push($OrderIds, $code["OrderID"]);

					array_push($AccountIds, $code["AccountID"]);

				}

				foreach ($challan["free_item_list"] as $key => $code) {

					array_push($Order_item_free, $code["ItemID"]);

				}

				$get_order_list = $this->challan_model->get_order_by_routeNew($data['challan']->RouteID);

				foreach ($get_order_list["item_list"] as $key1 => $code1) {

					array_push($Order_item, $code1["ItemID"]);

					array_push($OrderIds, $code1["OrderID"]);

					array_push($AccountIds, $code1["AccountID"]);

				}

				foreach ($get_order_list["free_item_list"] as $key1 => $code1) {

					array_push($Order_item_free, $code1["ItemID"]);

					

				}

				$Order_item =  array_unique($Order_item);

				$Order_item_free =  array_unique($Order_item_free);

				$OrderIds =  array_unique($OrderIds);

				$AccountIds =  array_unique($AccountIds);

				

				if(empty($Order_item)){

					

					}else{

					$get_item_rate = $this->challan_model->get_order_Item_rateNew($Order_item);

					$ItemSum = $this->challan_model->GetItemSum($OrderIds);

					$ItemStockDetails = $this->challan_model->GetStockDetails($Order_item);

				}

				$AccountBalances = $this->challan_model->GetAccountBalancec($AccountIds);

				$GetTcsPer = $this->challan_model->get_tcsperNew();

				$tcsPerValue = $GetTcsPer[0]['tcs'];

				$data['Curchallan']    = $challan;

				// echo "<pre>";

				// print_r($challan);

				// die;

				$data['ORDItem']    = $Order_item;

				$data['ORDItemFree']    = $Order_item_free;

				$data['ItemRate']    = $get_item_rate;

				$data['TCSValue']    = $tcsPerValue;

				$data['AccountBalances']    = $AccountBalances;

				$data['get_order_list']    = $get_order_list;

				$data['AllItemSum']    = $ItemSum;

				$data['ItemStockDetails']    = $ItemStockDetails;

				

			}

			// echo "<pre>";

			

			$DriverType = "1000159";

			$data['DriverList']    = $this->clients_model->GetStaffListTypeWise($DriverType);

			$LoaderType = "1000161";

			$data['LoaderList']    = $this->clients_model->GetStaffListTypeWise($LoaderType);

			$SalesManType = "1000163";

			$data['SalesManList']    = $this->clients_model->GetStaffListTypeWise($SalesManType);

			

			$data['invoiceid']            = $id;

			$data['routes']    = $this->clients_model->getroute();

			$data['vehicle']    = $this->clients_model->getvehicle();

			$data['bodyclass']            = 'invoices-total-manual';
			
			// Add JavaScript to disable save button when food license is expired (backup hook - inline script is primary)
			hooks()->add_action('app_admin_footer', function() {
				echo '<script type="text/javascript">
			// Function to initialize when jQuery is ready
			function initFoodLicenseCheck() {
				if (typeof jQuery === "undefined" || typeof $ === "undefined") {
					setTimeout(initFoodLicenseCheck, 100);
					return;
				}
				
				var $ = jQuery;
				
				$(document).ready(function() {
					
					// Function to check food license expiration and disable save button
					function checkFoodLicenseExpiration() {
						var hasExpiredLicense = false;
						var expiredCustomers = [];
						
						// Check all checked orders for expired food license
						// Try multiple selectors to find checkboxes - prioritize more specific ones
						var $checkedBoxes = $("#challan_data tbody input[type=\'checkbox\']:checked, input.chk:checked, input[name=\'order_id[]\']:checked");
						
						// Debug logging
						console.log("Checking food license expiration. Found " + $checkedBoxes.length + " checked boxes");
						
						if ($checkedBoxes.length === 0) {
							// If no checkboxes found, enable save button and return
							var $saveButton = $("button[type=\'submit\'], input[type=\'submit\'], button.btn-primary, .btn-save, #save-challan, button.btn-success, input.btn-success");
							$saveButton.prop("disabled", false).removeClass("disabled").css("opacity", "1").css("cursor", "pointer").removeAttr("title").css("pointer-events", "auto").removeAttr("onclick");
							$("#food-license-warning").remove();
							return;
						}
						
						$checkedBoxes.each(function() {
							var $checkbox = $(this);
							var orderRow = $checkbox.closest("tr");
							
							if (orderRow.length === 0) {
								return; // Skip if row not found
							}
							
							var customerCell = orderRow.find("td.col-id-custname");
							
							if (customerCell.length === 0) {
								return; // Skip if customer cell not found
							}
							
							var isExpired = customerCell.attr("data-food-license-expired");
							var customerName = customerCell.text().trim();
							var expiryDate = customerCell.attr("data-food-license-expiry");
							var licenseNumber = customerCell.attr("data-food-license-number");
							
							// Check if expired (handle both string "true" and boolean true)
							if (isExpired === "true" || isExpired === true || isExpired === "1" || isExpired === 1) {
								hasExpiredLicense = true;
								var expiryDateFormatted = "N/A";
								if (expiryDate) {
									try {
										expiryDateFormatted = new Date(expiryDate).toLocaleDateString();
									} catch (e) {
										expiryDateFormatted = expiryDate;
									}
								}
								expiredCustomers.push(customerName + " (License: " + (licenseNumber || "N/A") + ", Expired: " + expiryDateFormatted + ")");
								
								// Highlight the row
								orderRow.css("background-color", "#ffebee");
								customerCell.css("color", "#f44336");
								customerCell.css("font-weight", "bold");
							}
						});
						
						// Find save button with multiple selectors - be very comprehensive
						var $saveButton = $("button[type=\'submit\'], input[type=\'submit\'], button.btn-primary, .btn-save, #save-challan, button.btn-success, input.btn-success");
						
						// Also try to find by text content - search all buttons and inputs
						if ($saveButton.length === 0) {
							$("button, input[type=\'button\'], input[type=\'submit\']").each(function() {
								var $btn = $(this);
								var btnText = ($btn.text() || $btn.val() || "").toLowerCase();
								if (btnText.indexOf("save") !== -1 || btnText.indexOf("submit") !== -1) {
									$saveButton = $saveButton.add($btn);
								}
							});
						}
						
						// If still not found, try to find any button near the form
						if ($saveButton.length === 0) {
							$saveButton = $("form button, form input[type=\'submit\'], form input[type=\'button\']").first();
						}
						
						// Last resort - find any submit button on the page
						if ($saveButton.length === 0) {
							$saveButton = $("button[type=\'submit\'], input[type=\'submit\']").first();
						}
						
						if (hasExpiredLicense) {
							// Disable save button - use multiple methods to ensure it works
							if ($saveButton.length > 0) {
								$saveButton.each(function() {
									var $btn = $(this);
									$btn.prop("disabled", true);
									$btn.attr("disabled", "disabled");
									$btn.addClass("disabled");
									$btn.css("opacity", "0.5");
									$btn.css("cursor", "not-allowed");
									$btn.css("pointer-events", "none");
									$btn.attr("title", "Cannot save: Selected order(s) have expired food licenses");
									// Prevent form submission
									$btn.off("click").on("click", function(e) {
										e.preventDefault();
										e.stopPropagation();
										return false;
									});
								});
							} else {
								// Try to find button by looking for text containing "SAVE"
								$("button, input[type=\'button\'], input[type=\'submit\']").each(function() {
									var $btn = $(this);
									var btnText = ($btn.text() || $btn.val() || "").toUpperCase();
									if (btnText.indexOf("SAVE") !== -1) {
										$btn.prop("disabled", true).attr("disabled", "disabled").addClass("disabled")
											.css({"opacity": "0.5", "cursor": "not-allowed", "pointer-events": "none"})
											.attr("title", "Cannot save: Selected order(s) have expired food licenses")
											.off("click").on("click", function(e) {
												e.preventDefault();
												e.stopPropagation();
												return false;
											});
									}
								});
							}
							
							// Show warning message
							var warningMsg = "Cannot create challan. Food license expired for: " + expiredCustomers.join(", ");
							if ($("#food-license-warning").length === 0) {
								// Try to find form or a container to prepend warning
								var $container = $("form").first();
								if ($container.length === 0) {
									$container = $("#challan_data").closest("div").first();
								}
								if ($container.length === 0) {
									$container = $("body");
								}
								$container.prepend(\'<div id="food-license-warning" class="alert alert-warning" style="background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin-bottom: 20px; border-radius: 5px; z-index: 9999; position: relative;"><strong>⚠️ Warning:</strong> \' + warningMsg + \'</div>\');
							} else {
								$("#food-license-warning").html(\'<strong>⚠️ Warning:</strong> \' + warningMsg);
							}
						} else {
							// Enable save button
							if ($saveButton.length > 0) {
								$saveButton.each(function() {
									var $btn = $(this);
									$btn.prop("disabled", false);
									$btn.removeAttr("disabled");
									$btn.removeClass("disabled");
									$btn.css("opacity", "1");
									$btn.css("cursor", "pointer");
									$btn.css("pointer-events", "auto");
									$btn.removeAttr("title");
									$btn.removeAttr("onclick");
									// Re-enable click events
									$btn.off("click.prevent-submit");
								});
							}
							
							// Remove warning message
							$("#food-license-warning").remove();
							
							// Remove highlighting
							$("td.col-id-custname").css("background-color", "").css("color", "").css("font-weight", "");
							$("#challan_data tr").css("background-color", "");
						}
						
						// Also prevent form submission at form level if expired
						if (hasExpiredLicense) {
							$("form").off("submit.prevent-food-license").on("submit.prevent-food-license", function(e) {
								e.preventDefault();
								e.stopPropagation();
								alert("Cannot create challan. Food license expired for selected order(s).");
								return false;
							});
						} else {
							$("form").off("submit.prevent-food-license");
						}
					}
					
					// Check on page load
					setTimeout(function() {
						checkFoodLicenseExpiration();
					}, 1000);
					
					// Primary event handler for checkbox changes - use immediate execution
					$(document).on("change", "#challan_data tbody input[type=\'checkbox\'], input.chk, input[name=\'order_id[]\']", function(e) {
						e.stopPropagation();
						var $checkbox = $(this);
						// Use requestAnimationFrame for immediate execution after DOM update
						requestAnimationFrame(function() {
							setTimeout(function() {
								checkFoodLicenseExpiration();
							}, 0);
						});
					});
					
					// Also handle click events immediately - this fires before change event
					$(document).on("click", "#challan_data tbody input[type=\'checkbox\'], input.chk, input[name=\'order_id[]\']", function(e) {
						var $checkbox = $(this);
						// Check immediately after click, then again after a short delay
						setTimeout(function() {
							checkFoodLicenseExpiration();
						}, 0);
						// Also check after checkbox state is fully updated
						setTimeout(function() {
							checkFoodLicenseExpiration();
						}, 50);
					});
					
					// Additional handler for any checkbox in the table
					$("#challan_data").on("change click", "input[type=\'checkbox\']", function(e) {
						setTimeout(function() {
							checkFoodLicenseExpiration();
						}, 0);
					});
					
					// Extract food license status from embedded script tag after AJAX success
					$(document).ajaxSuccess(function(event, xhr, settings) {
						if (settings.url && (settings.url.indexOf("get_order_by_routeNew") !== -1)) {
							// Wait for DOM to be updated, then extract food license status
							setTimeout(function() {
								// Try to extract food license status from embedded script tag
								var $foodLicenseScript = $("#food-license-status-data");
								if ($foodLicenseScript.length > 0) {
									try {
										var foodLicenseJson = $foodLicenseScript.html();
										window.foodLicenseStatus = JSON.parse(foodLicenseJson) || {};
										// Remove the script tag after extracting data
										$foodLicenseScript.remove();
									} catch (e) {
										window.foodLicenseStatus = {};
									}
								}
								
								// Check food license expiration after DOM is updated
								checkFoodLicenseExpiration();
							}, 500);
						}
					});
					});
				}
				
				// Start initialization
				if (document.readyState === "loading") {
					document.addEventListener("DOMContentLoaded", initFoodLicenseCheck);
				} else {
					// DOM is already loaded
					initFoodLicenseCheck();
				}
				</script>';
			});

			$this->load->view('admin/challan/manageNew', $data);

		}

		





//------------------- Vehicle Detail -------------------------------

		public function get_vehicle_detail()

		{

			$id=$this->input->post('id'); 

			$vehicle_data = $this->challan_model->get_vehicle_detail($id);

			echo json_encode($vehicle_data);

		}

		

		public function update_rate()

		{

			

			$RCHID = $this->input->post('RCHID'); 

			$update_rate = $this->challan_model->update_rate($RCHID);

			echo json_encode($update_rate);

		}

		

		// New Code start

		

		

		public function get_order_by_routeNew()

		{

			$selected_company = $this->session->userdata('root_company');

			$id = $this->input->post('id'); 

			$Order_item = array();

			$Order_item_free = array();

			$OrderIds = array();

			$AccountIds = array();

			$get_order_list = $this->challan_model->get_order_by_routeNew($id);

			

			foreach ($get_order_list["item_list"] as $key1 => $code1) {

				array_push($Order_item, $code1["ItemID"]);

				array_push($OrderIds, $code1["OrderID"]);

			}

			foreach ($get_order_list["free_item_list"] as $key1 => $code1) {

				array_push($Order_item_free, $code1["ItemID"]);

			}

			

			foreach ($get_order_list["order_ids"] as $key2 => $code2) {

				array_push($AccountIds, $code2["AccountID"]);

			}

			

			$Order_item =  array_unique($Order_item);

			$Order_item_free =  array_unique($Order_item_free);

			// echo json_encode($Order_item_free);

			// die;

			if(empty($Order_item)){

				

				}else{

				$get_item_rate = $this->challan_model->get_order_Item_rateNew($Order_item);

				if($selected_company !== "1"){

					$ItemStockDetails = $this->challan_model->GetStockDetails($Order_item);

				}

				$AllItemSum = $this->challan_model->GetItemSum($OrderIds);

				$AllItemSumFree = $this->challan_model->GetItemSumFree($OrderIds);

				$AccountBalances = $this->challan_model->GetAccountBalancec($AccountIds);

			}

			// echo json_encode($get_item_rate);

			// die;

			$GetTcsPer = $this->challan_model->get_tcsperNew();

			$tcsPerValue = $GetTcsPer[0]['tcs'];
			
			// Check food license expiration for all accounts
			$foodLicenseStatus = array();
			if (!empty($AccountIds)) {
				$this->db->select('AccountID, FLNO1, expiry_licence');
				$this->db->where_in('AccountID', $AccountIds);
				$this->db->where('PlantID', $selected_company);
				$foodLicenseData = $this->db->get(db_prefix() . 'contacts')->result_array();
				
				$today = date('Y-m-d');
				foreach ($foodLicenseData as $license) {
					$isExpired = false;
					if (!empty($license['expiry_licence'])) {
						$expiryDate = date('Y-m-d', strtotime($license['expiry_licence']));
						if ($expiryDate < $today) {
							$isExpired = true;
						}
					}
					$foodLicenseStatus[$license['AccountID']] = array(
						'expired' => $isExpired,
						'expiry_date' => !empty($license['expiry_licence']) ? $license['expiry_licence'] : null,
						'license_number' => !empty($license['FLNO1']) ? $license['FLNO1'] : null
					);
				}
			}

			/*echo json_encode($get_order_list["item_list"]);

			die;*/

			if($get_order_list["order_ids"]){

				

				$html = '';

				$html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;height: 400px;"><thead style="background: #438EB9;color: #FFF;">';

				// $html .='<tr>';

				// $html .='<th colspan="7" class="col-id-no fixed-header"><center>Order Detais</center></th>';

				// if(count($Order_item)>0){

				// $html .='<th colspan="'.count($Order_item).'" class="col-id-no fixed-header"><center>Order Items</center></th>';

				// }

				// if(count($Order_item_free)>0){

				// $html .='<th colspan="'.count($Order_item_free).'" class="col-id-no fixed-header"><center>Free Items</center></th>';

				// }

				// $html .='<th colspan="11" class="col-id-no fixed-header"><center>Amount Details</center></th>';

				// $html .='</tr>';

				$html .='<tr>';

				$html .='<th class="col-id-no fixed-header">Tag</th>';

				$html .='<th class="col-id-ordid fixed-header">OrderNo</th>';

				$html .='<th class="col-id-custname fixed-header">AccountName</th>';

				$html .='<th class="col-id-custstate fixed-header">Sequence</th>';

				$html .='<th class="col-id-custstate fixed-header">StateID</th>';

				$html .='<th class="col-id-custstate fixed-header">Route Name</th>';

				$html .='<th class="col-id-ordtype fixed-header">Ordertype</th>';

				$html .='<th>SalesID</th>';

				$html .='<th>SalesDate</th>';

				foreach ($Order_item as $code) {

					$item =	$this->db->get_where('tblitems',array('item_code'=>$code))->row(); 

					$html .='<th width="5%" title="'.$item->description.'">'.$code.'</th>';

				}

				$html .='<th>Crates</th>';

				$html .='<th>Cases</th>';

				$html .='<th>OrderAmt</th>';

				$html .='<th>SaleAmt</th>';

				$html .='<th>DiscAmt</th>';

				$html .='<th>CGSTAMT</th>';

				$html .='<th>SGSTAMT</th>';

				$html .='<th>IGSTAMT</th>';

				$html .='<th>TCSPer</th>';

				$html .='<th>TCSAmt</th>';

				$html .='<th>BillAmt</th>';

				$html .='</tr>';

				$html .='</thead>';

				$challan_cases = 0;

				$challan_crate = 0;

				$challan_subtotal = 0;

				$challan_total = 0;

				$DiscAmtSum = 0;

				$CGSTAMTSum = 0;

				$SGSTAMTSum = 0;

				$IGSTAMTSum = 0;

				$html .='<tbody>';

				

				foreach ($get_order_list["order_ids"] as $key1 => $ids) {

					$css = '';

					if($ids['credit_exceed'] == 'Y' && $ids['credit_apply'] == 'Y'){

						$css = 'color:red';

					}

					

					if($ids['credit_exceed'] == 'Y' && $ids['credit_apply'] == 'N'){

						$css = 'color:green';

					}

					$html .='<tr>';

					//$order_data = $this->challan_model->getorderdetail_by_orderId($ids["OrderID"]);

					$html .='<td scope="row" class="col-id-no"><input type="checkbox" name="order_id[]" class="chk" value="'.$ids["OrderID"].'"><input type="hidden" name="OrderID" value="'.$ids["OrderID"].'"><input type="hidden" name="credit_apply" value="'.$ids["credit_apply"].'"><input type="hidden" name="PrevOrderAmt" value="'.$ids["OrderAmt"].'"></td>';

					$BAL = 0;

					foreach ($AccountBalances as $BalKey => $BalVal) {

						if($ids["AccountID"] === $BalVal["AccountID"]){

							$BAL = (-1 * floatval($BalVal["Balance"])) + $ids["MaxCrdAmt"];

						}

					}

					$html .='<td scope="row" class="col-id-ordid"><input type="hidden" name="Balance" value="'.$BAL.'"><input type="hidden" name="MaxCrdAmt" value="'.$ids["MaxCrdAmt"].'"><span style="'.$css.'">'.$ids["OrderID"].'</span></td>';

					

					// Add food license expiration status as data attribute
					$foodLicenseExpired = isset($foodLicenseStatus[$ids["AccountID"]]) && $foodLicenseStatus[$ids["AccountID"]]['expired'] ? 'true' : 'false';
					$foodLicenseExpiryDate = isset($foodLicenseStatus[$ids["AccountID"]]) ? htmlspecialchars($foodLicenseStatus[$ids["AccountID"]]['expiry_date'], ENT_QUOTES) : '';
					$foodLicenseNumber = isset($foodLicenseStatus[$ids["AccountID"]]) ? htmlspecialchars($foodLicenseStatus[$ids["AccountID"]]['license_number'], ENT_QUOTES) : '';
					$html .='<td scope="row" class="col-id-custname" data-account-id="'.$ids["AccountID"].'" data-food-license-expired="'.$foodLicenseExpired.'" data-food-license-expiry="'.$foodLicenseExpiryDate.'" data-food-license-number="'.$foodLicenseNumber.'">'.$ids["company"].'</td>';

					$html .='<td scope="row" class="col-id-custstate"><input class= "SequenceInput" style="width: 45px;" type="text" name="Sequence_'.$ids["OrderID"].'" value=""></td>';

					$html .='<td scope="row" class="col-id-custstate">'.$ids["state"].'</td>';

					$html .='<td scope="row" class="col-id-custstate">'.$ids["RouteName"].'</td>';

					

					$html .='<td scope="row" class="col-id-ordtype">'.$ids["OrderType"].'</td>';

					if($ids["istcs"] == "1"){

						$tcs = $tcsPerValue;

						}else{

						$tcs = 0.00;

					}

					$html .='<td><input type="hidden" name="istcs" value="'.$tcs.'"></td>';

					

					$html .='<td></td>';

					$mm = 0;

					$OrderSaleAmt = 0;

					$OrderBillAmt = 0;

					$DiscAmt = 0; 

					$OSGST = 0; 

					$OCGST = 0; 

					$OIGST = 0; 

					foreach ($Order_item as $ItemIDc) {

						$isItem = '';

						foreach ($get_order_list["item_list"] as $key => $code) {

							if($code["ItemID"] == $ItemIDc){

								$matched = '';

								

								if($ids["OrderID"] == $code["OrderID"]){

									$isItem = 1;

									foreach ($get_item_rate as $key2 => $code2) {

										if($code2["item_id"]==$code["ItemID"] && $ids["state"] == $code2["state_id"] && $ids["DistributorType"]==$code2["distributor_id"]){

											if($code["BasicRate"] == $code2["assigned_rate"]){

												break;

											}else{

												$matched= 'color:red;';

												$mm++;

											}

										}

									}

									/*if($mm == 0){

										$his_rate = $this->challan_model->get_order_Item_rate_history($code["ItemID"]);

										if($his_rate->ItemID ==$code["ItemID"] &&  $ids["state"] == $his_rate->StateID && $ids["DistributorType"]==$his_rate->DistributorType && $code["BasicRate"] !== $his_rate->BasicRate){

										$matched= 'style="color:red"';

										$mm++;

										}

									}*/

									

									$pack_qty = $code["CaseQty"];

									$rate = $code["BasicRate"];

									$DiscPer = $code["DiscPerc"];

									$gst = $code["cgst"] + $code["sgst"] + $code["igst"];

									if($ids["state"] == "UP"){

										$cscr = $code["local_supply_in"];

										}else{

										$cscr = $code["outst_supply_in"];

									}

									

									$qty = (int) $code["orderqty"] ;// / $code["CaseQty"] Add If Needed

									$OrderSaleAmt = $OrderSaleAmt + $code["OrderAmt"];

									$OrderBillAmt += $code["NetOrderAmt"];

									$DiscAmt += $code["DiscAmt"];

									$OSGST += $code["sgstamt"];

									$OCGST += $code["cgstamt"];

									$OIGST += $code["igstamt"];

									

									//$html .='<td width="5%" align="right" '.$matched.'>'.$qty1.'</td>';

								}

							}

						}

						$balCase = 0;

						if($selected_company !== "1"){

							$PQty = 0;

							$PRQty = 0;

							$IQty = 0;

							$PRDQty = 0;

							$SQty = 0;

							$SRQty = 0;

							$ADJQTY = 0;

							$GIQTY = 0;

							$GOQTY = 0;

							foreach ($ItemStockDetails as $key => $value) {

								if($value['ItemID'] == $ItemIDc){

									$oQty = $value['OQty'];

									$caseQty = $value['CaseQty'];

									if($value['TType'] == 'P'){

										$PQty = $value['BilledQty'];

										}elseif($value['TType'] == 'N'){

										$PRQty = $value['BilledQty'];

										}elseif($value['TType'] == 'A'){

										$IQty = $value['BilledQty'];

										}elseif($value['TType'] == 'B'){

										$PRDQty = $value['BilledQty'];

										}elseif($value['TType'] == 'O' && $value['TType2'] == 'Order'){

										$SQty = $value['BilledQty'];

										}elseif($value['TType'] == 'R' && $value['TType2'] == 'Fresh'){

										$SRQty = $value['BilledQty'];

										}elseif($value['TType'] == 'X' && $value['TType2'] == 'Stock Adjustment'){

										$ADJQTY += $value['BilledQty'];

										}elseif($value['TType'] == 'X' && $value['TType2'] == 'Promotional Activity'){

										$ADJQTY += $value['BilledQty'];

										}elseif($value['TType'] == 'X' && $value['TType2'] == 'Free Distribution'){

										$ADJQTY += $value['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){

										$GIQTY += $stock['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){

										$GOQTY += $stock['BilledQty'];

									}

								}

							}

							$balance = (float) $oQty + (float) $PQty - (float) $PRQty - (float) $IQty +  (float) $PRDQty - (float) $SQty + (float) $SRQty - (float) $ADJQTY  - (float) $GOQTY +  - (float) $GIQTY;

							$balCase = $balance ;// / $caseQty Add If Needed

						}

						if($isItem == ""){

							$html .='<td width="5%" align="right" ></td>';

							}else{

							$html .='<td width="5%"><input type="hidden" value="'.$qty.'_'.$pack_qty.'_'.$rate.'_'.$gst.'_'.$cscr.'_'.$ids["state"].'_'.$balCase.'_'.$DiscPer.'_'.$ids["DistributorType"].'_'.$ids["Transdate"].'" id="qtyhidden"/><input type="hidden" id="orgqty_'.$ids["OrderID"].'_'.$ItemIDc.'" name="orgqty_'.$ids["OrderID"].'_'.$ItemIDc.'" value="'.$qty.'"/><input class= "QtyInput" style="width: 45px;'.$matched.'" type="text" onchange="total(this,'.$qty.')" name="qty_'.$ids["OrderID"].'_'.$ItemIDc.'" value="'.$qty.'"></td>';

						}

					}

					

					

					$html .='<td style="text-align: right;"><input class= "CratesInput" style="width: 45px;" type="text" onchange="ChallanValues()" name="crates_'.$ids["OrderID"].'" value="'.$ids["Crates"].'">';

					if($mm > 0){

						$html .='<input type="hidden" name="rate_change" id="rate_change" value="Y">';

					}

					$html .='</td>';

					$challan_crate = $challan_crate + $ids["Crates"];

					$html .='<td style="text-align: right;"><input class= "CasesInput" style="width: 45px;" type="text" onchange="ChallanValues()" name="cases_'.$ids["OrderID"].'" value="'.$ids["Cases"].'"></td>';

					$challan_cases = $challan_cases + $ids["Cases"];

					// bill Amt

					$html .='<td style="text-align: right;">'.$OrderBillAmt.' </td>';

					$challan_total = $challan_total + $OrderBillAmt;

					//sale Amt

					$html .='<td style="text-align: right;">'.$OrderSaleAmt.'</td>';

					$challan_subtotal = $challan_subtotal + $OrderSaleAmt;

					// Disc Amt

					$html .='<td style="text-align: right;">'.$DiscAmt.'</td>';

					$DiscAmtSum = $DiscAmtSum + $DiscAmt;

					// CGST Amt

					$html .='<td style="text-align: right;">'.$OCGST.'</td>';

					$CGSTAMTSum = $CGSTAMTSum + $OCGST;

					// SGST Amt

					$html .='<td style="text-align: right;">'.$OSGST.'</td>';

					$SGSTAMTSum = $SGSTAMTSum + $OSGST;

					// IGST Amt

					$html .='<td style="text-align: right;">'.$OIGST.'</td>';

					$IGSTAMTSum = $IGSTAMTSum + $OIGST;

					// TCS Amt

					$html .='<td style="text-align: right;"><input type="hidden" name="tcsper" value="'.$tcs.'">'.$tcs.'</td>';

					if($tcs !=="0.00"){

						$tcsAmt = ($OrderBillAmt / 100) * $tcs;

						}else{

						$tcsAmt = 0.00;

					}

					

					$html .='<td style="text-align: right;">'.round($tcsAmt,2).'</td>';

					// Bill Amt Include TCSAMT

					$finalBillAmt = $OrderBillAmt + $tcsAmt;

					$html .='<td style="text-align: right;">'.round($finalBillAmt,2).'<input type="hidden" name="FBilAmt" id="FBilAmt" value="'.$finalBillAmt.'"></td>';

					$html .='</tr>';

				}

				

				$html .='<tfoot><tr>';

				

				$html .='<td style="text-align:center; scope="row" class="col-id-no"">Total</td>

				<td scope="row" class="col-id-ordid"></td>

				<td scope="row" class="col-id-custname"></td>

				<td scope="row" class="col-id-custstate"></td>

				<td scope="row" class="col-id-custstate"></td>

				<td scope="row" class="col-id-custRoute"></td>

				<td scope="row" class="col-id-ordtype"></td>

				<td></td><td></td>';

				

				foreach ($Order_item as $ItemIDc) {

					foreach ($AllItemSum as $keys => $values) {

						if($ItemIDc == $values['ItemID']){

							$ItemSum = $values['OrderQty'] ;// / $values['CaseQty'] Add If Needed

							$html .='<td style="text-align: right;">'.(int) $ItemSum.'</td>';

						}

					}                  

				}

				

				$html .='<td style="text-align: right;">'.$challan_crate.'</td>';

				$html .='<td style="text-align: right;">'.$challan_cases.'</td>';

				$html .='<td style="text-align: right;">'.$challan_total.'</td>';

				$html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';

				$html .='<td style="text-align: right;">'.$DiscAmtSum.'</td>';

				$html .='<td style="text-align: right;">'.$CGSTAMTSum.'</td>';

				$html .='<td style="text-align: right;">'.$SGSTAMTSum.'</td>';

				$html .='<td style="text-align: right;">'.$IGSTAMTSum.'</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='<td style="text-align: right;">'.$challan_total.'</td>';

				$html .='</tr></tfoot>';

				

				$html .='</tbody>';

				$html .='</table>';

				}else{

				$html = '<p style="color:red;">No data found...</p>';

			}

			// Include food license status in HTML as JSON script tag for backward compatibility
			// This allows frontend to get food license data while maintaining HTML response format
			if (!empty($foodLicenseStatus)) {
				$html .= '<script type="application/json" id="food-license-status-data">' . json_encode($foodLicenseStatus) . '</script>';
			}
			
			// Return HTML directly for backward compatibility
			// Food license status is embedded in the HTML as a script tag
			echo $html;

		}

		public function get_order_by_routeNewAll()

		{

			$selected_company = $this->session->userdata('root_company');

			$Order_item = array();

			$Order_item_free = array();

			$OrderIds = array();

			$AccountIds = array();

			$get_order_list = $this->challan_model->get_order_by_routeNewAll();

			

			foreach ($get_order_list["item_list"] as $key1 => $code1) {

				array_push($Order_item, $code1["ItemID"]);

				array_push($OrderIds, $code1["OrderID"]);

			}

			foreach ($get_order_list["free_item_list"] as $key1 => $code1) {

				array_push($Order_item_free, $code1["ItemID"]);

			}

			

			foreach ($get_order_list["order_ids"] as $key2 => $code2) {

				array_push($AccountIds, $code2["AccountID"]);

			}

			

			$Order_item =  array_unique($Order_item);

			$Order_item_free =  array_unique($Order_item_free);

			// echo json_encode($get_order_list["order_ids"]);

			// die;

			if(empty($Order_item)){

				

				}else{

				$get_item_rate = $this->challan_model->get_order_Item_rateNew($Order_item);

				if($selected_company !== "1"){

					$ItemStockDetails = $this->challan_model->GetStockDetails($Order_item);

				}

				$AllItemSum = $this->challan_model->GetItemSum($OrderIds);

				$AllItemSumFree = $this->challan_model->GetItemSumFree($OrderIds);

				$AccountBalances = $this->challan_model->GetAccountBalancec($AccountIds);

			}

			// echo json_encode($AccountBalances);

			// die;

			$GetTcsPer = $this->challan_model->get_tcsperNew();

			$tcsPerValue = $GetTcsPer[0]['tcs'];
			
			// Check food license expiration for all accounts
			$foodLicenseStatus = array();
			if (!empty($AccountIds)) {
				$this->db->select('AccountID, FLNO1, expiry_licence');
				$this->db->where_in('AccountID', $AccountIds);
				$this->db->where('PlantID', $selected_company);
				$foodLicenseData = $this->db->get(db_prefix() . 'contacts')->result_array();
				
				$today = date('Y-m-d');
				foreach ($foodLicenseData as $license) {
					$isExpired = false;
					if (!empty($license['expiry_licence'])) {
						$expiryDate = date('Y-m-d', strtotime($license['expiry_licence']));
						if ($expiryDate < $today) {
							$isExpired = true;
						}
					}
					$foodLicenseStatus[$license['AccountID']] = array(
						'expired' => $isExpired,
						'expiry_date' => !empty($license['expiry_licence']) ? $license['expiry_licence'] : null,
						'license_number' => !empty($license['FLNO1']) ? $license['FLNO1'] : null
					);
				}
			}

			/*echo json_encode($get_order_list["item_list"]);

			die;*/

			if($get_order_list["order_ids"]){

				

				$html = '';

				$html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;height: 400px;"><thead style="background: #438EB9;color: #FFF;">';

				$html .='<tr>';

				$html .='<th class="col-id-no fixed-header">Tag</th>';

				$html .='<th class="col-id-ordid fixed-header">OrderNo</th>';

				$html .='<th class="col-id-custname fixed-header">AccountName</th>';

				$html .='<th class="col-id-custstate fixed-header" style="width:50px;">Sequence</th>';

				$html .='<th class="col-id-custstate fixed-header">StateID</th>';

				$html .='<th class="col-id-custstate fixed-header">Route Name</th>';

				$html .='<th class="col-id-ordtype fixed-header">Ordertype</th>';

				$html .='<th>SalesID</th>';

				$html .='<th>SalesDate</th>';

				foreach ($Order_item as $code) {

					$item =	$this->db->get_where('tblitems',array('item_code'=>$code))->row(); 

					$html .='<th width="5%" title="'.$item->description.'">'.$code.'</th>';

				}

				$html .='<th>Crates</th>';

				$html .='<th>Cases</th>';

				$html .='<th>OrderAmt</th>';

				$html .='<th>SaleAmt</th>';

				$html .='<th>DiscAmt</th>';

				$html .='<th>CGSTAMT</th>';

				$html .='<th>SGSTAMT</th>';

				$html .='<th>IGSTAMT</th>';

				$html .='<th>TCSPer</th>';

				$html .='<th>TCSAmt</th>';

				$html .='<th>BillAmt</th>';

				$html .='</tr>';

				$html .='</thead>';

				$challan_cases = 0;

				$challan_crate = 0;

				$challan_subtotal = 0;

				$challan_total = 0;

				$DiscAmtSum = 0;

				$CGSTAMTSum = 0;

				$SGSTAMTSum = 0;

				$IGSTAMTSum = 0;

				$html .='<tbody>';

				

				foreach ($get_order_list["order_ids"] as $key1 => $ids) {

					$css = '';

					if($ids['credit_exceed'] == 'Y' && $ids['credit_apply'] == 'Y'){

						$css = 'color:red';

					}

					

					if($ids['credit_exceed'] == 'Y' && $ids['credit_apply'] == 'N'){

						$css = 'color:green';

					}

					$html .='<tr>';

					//$order_data = $this->challan_model->getorderdetail_by_orderId($ids["OrderID"]);

					$html .='<td scope="row" class="col-id-no"><input type="checkbox" name="route_id[]" onclick="GetRouteOrder('.$ids["RouteID"].')" class="getroute" value="'.$ids["RouteID"].'"><input type="hidden" name="credit_apply" value="'.$ids["credit_apply"].'"><input type="hidden" name="PrevOrderAmt" value="'.$ids["OrderAmt"].'"></td>';

					$BAL = 0;

					foreach ($AccountBalances as $BalKey => $BalVal) {

						if($ids["AccountID"] === $BalVal["AccountID"]){

							$BAL = (-1 * floatval($BalVal["Balance"])) + $ids["MaxCrdAmt"];

						}

					}

					$html .='<td scope="row" class="col-id-ordid"><input type="hidden" name="Balance" value="'.$BAL.'"><input type="hidden" name="MaxCrdAmt" value="'.$ids["MaxCrdAmt"].'"><span style="'.$css.'">'.$ids["OrderID"].'</span></td>';

					// Add food license expiration status as data attribute
					$foodLicenseExpired = isset($foodLicenseStatus[$ids["AccountID"]]) && $foodLicenseStatus[$ids["AccountID"]]['expired'] ? 'true' : 'false';
					$foodLicenseExpiryDate = isset($foodLicenseStatus[$ids["AccountID"]]) ? htmlspecialchars($foodLicenseStatus[$ids["AccountID"]]['expiry_date'], ENT_QUOTES) : '';
					$foodLicenseNumber = isset($foodLicenseStatus[$ids["AccountID"]]) ? htmlspecialchars($foodLicenseStatus[$ids["AccountID"]]['license_number'], ENT_QUOTES) : '';
					$html .='<td scope="row" class="col-id-custname" data-account-id="'.$ids["AccountID"].'" data-food-license-expired="'.$foodLicenseExpired.'" data-food-license-expiry="'.$foodLicenseExpiryDate.'" data-food-license-number="'.$foodLicenseNumber.'">'.$ids["company"].'</td>';

					$html .='<td scope="row" class="col-id-custstate"><input class= "SequenceInput" style="width: 45px;" type="text" name="Sequence_'.$ids["OrderID"].'" value=""></td>';

					$html .='<td scope="row" class="col-id-custstate">'.$ids["state"].'</td>';

					$html .='<td scope="row" class="col-id-custstate">'.$ids["RouteName"].'</td>';

					

					$html .='<td scope="row" class="col-id-ordtype">'.$ids["OrderType"].'</td>';

					if($ids["istcs"] == "1"){

						$tcs = $tcsPerValue;

						}else{

						$tcs = 0.00;

					}

					$html .='<td><input type="hidden" name="istcs" value="'.$tcs.'"></td>';

					

					$html .='<td></td>';

					$mm = 0;

					$OrderSaleAmt = 0;

					$OrderBillAmt = 0;

					$DiscAmt = 0; 

					$OSGST = 0; 

					$OCGST = 0; 

					$OIGST = 0; 

					foreach ($Order_item as $ItemIDc) {

						$isItem = '';

						foreach ($get_order_list["item_list"] as $key => $code) {

							if($code["ItemID"] == $ItemIDc){

								$matched = '';

								

								if($ids["OrderID"] == $code["OrderID"]){

									$isItem = 1;

									foreach ($get_item_rate as $key2 => $code2) {

										if($code2["item_id"]==$code["ItemID"] && $ids["state"] == $code2["state_id"] && $ids["DistributorType"]==$code2["distributor_id"] && $code["BasicRate"] !== $code2["assigned_rate"]){

											$matched= 'color:red;';

											$mm++;

											

										}

									}

									/*if($mm == 0){

										$his_rate = $this->challan_model->get_order_Item_rate_history($code["ItemID"]);

										if($his_rate->ItemID ==$code["ItemID"] &&  $ids["state"] == $his_rate->StateID && $ids["DistributorType"]==$his_rate->DistributorType && $code["BasicRate"] !== $his_rate->BasicRate){

										$matched= 'style="color:red"';

										$mm++;

										}

									}*/

									

									$pack_qty = $code["CaseQty"];

									$rate = $code["BasicRate"];

									$DiscPer = $code["DiscPerc"];

									$gst = $code["cgst"] + $code["sgst"] + $code["igst"];

									if($ids["state"] == "UP"){

										$cscr = $code["local_supply_in"];

										}else{

										$cscr = $code["outst_supply_in"];

									}

									

									$qty = (int) $code["orderqty"] ;// / $code["CaseQty"] Add If Needed

									$OrderSaleAmt = $OrderSaleAmt + $code["OrderAmt"];

									$OrderBillAmt += $code["NetOrderAmt"];

									$DiscAmt += $code["DiscAmt"];

									$OSGST += $code["sgstamt"];

									$OCGST += $code["cgstamt"];

									$OIGST += $code["igstamt"];

									

									//$html .='<td width="5%" align="right" '.$matched.'>'.$qty1.'</td>';

								}

							}

						}

						$balCase = 0;

						if($selected_company !== "1"){

							$PQty = 0;

							$PRQty = 0;

							$IQty = 0;

							$PRDQty = 0;

							$SQty = 0;

							$SRQty = 0;

							$ADJQTY = 0;

							$GIQTY = 0;

							$GOQTY = 0;

							foreach ($ItemStockDetails as $key => $value) {

								if($value['ItemID'] == $ItemIDc){

									$oQty = $value['OQty'];

									$caseQty = $value['CaseQty'];

									if($value['TType'] == 'P'){

										$PQty = $value['BilledQty'];

										}elseif($value['TType'] == 'N'){

										$PRQty = $value['BilledQty'];

										}elseif($value['TType'] == 'A'){

										$IQty = $value['BilledQty'];

										}elseif($value['TType'] == 'B'){

										$PRDQty = $value['BilledQty'];

										}elseif($value['TType'] == 'O' && $value['TType2'] == 'Order'){

										$SQty = $value['BilledQty'];

										}elseif($value['TType'] == 'R' && $value['TType2'] == 'Fresh'){

										$SRQty = $value['BilledQty'];

										}elseif($value['TType'] == 'X' && $value['TType2'] == 'Stock Adjustment'){

										$ADJQTY += $value['BilledQty'];

										}elseif($value['TType'] == 'X' && $value['TType2'] == 'Promotional Activity'){

										$ADJQTY += $value['BilledQty'];

										}elseif($value['TType'] == 'X' && $value['TType2'] == 'Free Distribution'){

										$ADJQTY += $value['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'In'){

										$GIQTY += $stock['BilledQty'];

										}elseif($stock['TType'] == 'T' && $stock['TType2'] == 'Out'){

										$GOQTY += $stock['BilledQty'];

									}

								}

							}

							$balance = (float) $oQty + (float) $PQty - (float) $PRQty - (float) $IQty +  (float) $PRDQty - (float) $SQty + (float) $SRQty - (float) $ADJQTY  - (float) $GOQTY +  - (float) $GIQTY;

							$balCase = $balance ;// / $caseQty Add If Needed

						}

						if($isItem == ""){

							$html .='<td width="5%" align="right" ></td>';

							}else{

							$html .='<td width="5%"><input type="hidden" value="'.$qty.'_'.$pack_qty.'_'.$rate.'_'.$gst.'_'.$cscr.'_'.$ids["state"].'_'.$balCase.'_'.$DiscPer.'_'.$ids["DistributorType"].'_'.$ids["Transdate"].'" id="qtyhidden"/><input type="hidden" id="orgqty_'.$ids["OrderID"].'_'.$ItemIDc.'" name="orgqty_'.$ids["OrderID"].'_'.$ItemIDc.'" value="'.$qty.'"/><input class= "QtyInput" style="width: 45px;'.$matched.'" type="text" onchange="total(this,'.$qty.')" name="qty_'.$ids["OrderID"].'_'.$ItemIDc.'" value="'.$qty.'"></td>';

						}

					}

					

					

					$html .='<td style="text-align: right;"><input class= "CratesInput" style="width: 45px;" type="text" onchange="ChallanValues()" name="crates_'.$ids["OrderID"].'" value="'.$ids["Crates"].'">';

					if($mm > 0){

						$html .='<input type="hidden" name="rate_change" id="rate_change" value="Y">';

					}

					$html .='</td>';

					$challan_crate = $challan_crate + $ids["Crates"];

					$html .='<td style="text-align: right;"><input class= "CasesInput" style="width: 45px;" type="text" onchange="ChallanValues()" name="cases_'.$ids["OrderID"].'" value="'.$ids["Cases"].'"></td>';

					$challan_cases = $challan_cases + $ids["Cases"];

					// bill Amt

					$html .='<td style="text-align: right;">'.$OrderBillAmt.' </td>';

					$challan_total = $challan_total + $OrderBillAmt;

					//sale Amt

					$html .='<td style="text-align: right;">'.$OrderSaleAmt.'</td>';

					$challan_subtotal = $challan_subtotal + $OrderSaleAmt;

					// Disc Amt

					$html .='<td style="text-align: right;">'.$DiscAmt.'</td>';

					$DiscAmtSum = $DiscAmtSum + $DiscAmt;

					// CGST Amt

					$html .='<td style="text-align: right;">'.$OCGST.'</td>';

					$CGSTAMTSum = $CGSTAMTSum + $OCGST;

					// SGST Amt

					$html .='<td style="text-align: right;">'.$OSGST.'</td>';

					$SGSTAMTSum = $SGSTAMTSum + $OSGST;

					// IGST Amt

					$html .='<td style="text-align: right;">'.$OIGST.'</td>';

					$IGSTAMTSum = $IGSTAMTSum + $OIGST;

					// TCS Amt

					$html .='<td style="text-align: right;"><input type="hidden" name="tcsper" value="'.$tcs.'">'.$tcs.'</td>';

					if($tcs !=="0.00"){

						$tcsAmt = ($OrderBillAmt / 100) * $tcs;

						}else{

						$tcsAmt = 0.00;

					}

					

					$html .='<td style="text-align: right;">'.round($tcsAmt,2).'</td>';

					// Bill Amt Include TCSAMT

					$finalBillAmt = $OrderBillAmt + $tcsAmt;

					$html .='<td style="text-align: right;">'.round($finalBillAmt,2).'<input type="hidden" name="FBilAmt" id="FBilAmt" value="'.$finalBillAmt.'"></td>';

					$html .='</tr>';

				}

				

				$html .='<tfoot><tr>';

				

				$html .='<td style="text-align:center; scope="row" class="col-id-no"">Total</td>

				<td scope="row" class="col-id-ordid"></td>

				<td scope="row" class="col-id-custname"></td>

				<td scope="row" class="col-id-custname"></td>

				<td scope="row" class="col-id-custstate"></td>

				<td scope="row" class="col-id-custRoute"></td>

				<td scope="row" class="col-id-ordtype"></td>

				<td></td><td></td>';

				

				foreach ($Order_item as $ItemIDc) {

					foreach ($AllItemSum as $keys => $values) {

						if($ItemIDc == $values['ItemID']){

							$ItemSum = $values['OrderQty'] ;// / $values['CaseQty'] Add If Needed

							$html .='<td style="text-align: right;">'.(int) $ItemSum.'</td>';

						}

					}                  

				}

				

				$html .='<td style="text-align: right;">'.$challan_crate.'</td>';

				$html .='<td style="text-align: right;">'.$challan_cases.'</td>';

				$html .='<td style="text-align: right;">'.$challan_total.'</td>';

				$html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';

				$html .='<td style="text-align: right;">'.$DiscAmtSum.'</td>';

				$html .='<td style="text-align: right;">'.$CGSTAMTSum.'</td>';

				$html .='<td style="text-align: right;">'.$SGSTAMTSum.'</td>';

				$html .='<td style="text-align: right;">'.$IGSTAMTSum.'</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='<td style="text-align: right;">'.$challan_total.'</td>';

				$html .='</tr></tfoot>';

				

				$html .='</tbody>';

				$html .='</table>';

				}else{

				$html = '<p style="color:red;">No data found...</p>';

			}

			

			echo json_encode($html);

		}

		// New Code End 

		public function get_order_by_route2()

		{

			$selected_company = $this->session->userdata('root_company');

			$id = $this->input->post('id'); 

			$Order_item = array();

			

			$get_order_list = $this->challan_model->get_order_by_route($id);

			

			foreach ($get_order_list["item_list"] as $key1 => $code1) {

				array_push($Order_item, $code1["ItemID"]);

			}

			if(empty($Order_item)){

				

				}else{

				$get_item_rate = $this->challan_model->get_order_Item_rate($Order_item);

			}

			//echo json_encode($get_item_rate);

			if($get_order_list["order_ids"]){

				

				$html = '';

				$html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;"><thead style="background: #438EB9;color: #FFF;">';

				$html .='<th>Tag</th>';

				$html .='<th>OrderNo</th>';

				$html .='<th>AccountName</th>';

				$html .='<th>StateID</th>';

				$html .='<th>Ordertype</th>';

				$html .='<th>SalesID</th>';

				$html .='<th>SalesDate</th>';

				foreach ($get_order_list["item_list"] as $key => $code) {

					$html .='<th width="5%">'.$code["ItemID"].'</th>';

				}

				$html .='<th>Crates</th>';

				$html .='<th>Cases</th>';

				$html .='<th>OrderAmt</th>';

				$html .='<th>SaleAmt</th>';

				$html .='<th>TCSPer</th>';

				$html .='<th>TCSAmt</th>';

				$html .='</thead>';

				$challan_cases = 0;

				$challan_crate = 0;

				$challan_subtotal = 0;

				$challan_total = 0;

				$html .='<tbody>';

				

				foreach ($get_order_list["order_ids"] as $key1 => $ids) {

					$html .='<tr>';

					//$order_data = $this->challan_model->getorderdetail_by_orderId($ids["OrderID"]);

					$html .='<td><input type="checkbox" name="order_id[]" class="chk" value="'.$ids["OrderID"].'"><input type="hidden" name="OrderID" value="'.$ids["OrderID"].'"></td>';

					$html .='<td>'.$ids["OrderID"].'</td>';

					//$account_name = get_account_name($order_data->AccountID,$selected_company);

					$html .='<td>'.$ids["company"].'</td>';

					$html .='<td>'.$ids["state"].'</td>';

					

					$html .='<td>'.$ids["OrderType"].'</td>';

					$html .='<td></td>';

					

					$html .='<td></td>';

					$mm = 0;

					

					foreach ($get_order_list["item_list"] as $key => $code) {

						$matched = '';

						if($ids["OrderID"] == $code["OrderID"]){

							foreach ($get_item_rate as $key2 => $code2) {

								if($code2["item_id"]==$code["ItemID"] && $ids["state"] == $code2["state_id"] && $ids["DistributorType"]==$code2["distributor_id"] && $code["BasicRate"] !== $code2["assigned_rate"]){

									$matched= 'style="color:red"';

									$mm++;

									

								}

							}

							if($mm == 0){

								$his_rate = $this->challan_model->get_order_Item_rate_history($code["ItemID"]);

								if($his_rate->ItemID ==$code["ItemID"] &&  $ids["state"] == $his_rate->StateID && $ids["DistributorType"]==$his_rate->DistributorType && $code["BasicRate"] !== $his_rate->BasicRate){

									$matched= 'style="color:red"';

									$mm++;

								}

							}

							$qty1 = $code["orderqty"] / $code["CaseQty"];

							$html .='<td width="5%" align="right" '.$matched.'>'.$qty1.'</td>';

							}else{

							$html .='<td></td>';

						}

						//$item_data1 = $this->challan_model->get_order_singleitem($ids["OrderID"],$code["ItemID"]);

						/*if($item_data1){

							if(is_null($item_data1->eOrderQty)){

							$qty1 = $item_data1->OrderQty / $item_data1->CaseQty;

							}else{

							$qty1 = $item_data1->eOrderQty / $item_data1->CaseQty;

							}

							

							$html .='<td width="5%">'.$qty1.'</td>';

							}else{

							

							$html .='<td></td>';

						}*/

						

						

					}

					

					$html .='<td style="text-align: right;">'.$ids["Crates"];

					if($mm > 0){

						$html .='<input type="hidden" name="rate_change" id="rate_change" value="Y">';

					}

					$html .='</td>';

					$challan_crate = $challan_crate + $ids["Crates"];

					$html .='<td style="text-align: right;">'.$ids["Cases"].'</td>';

					$challan_cases = $challan_cases + $ids["Cases"];

					$html .='<td style="text-align: right;">'.$ids["OrderAmt"].'</td>';

					$challan_subtotal = $challan_subtotal + $ids["OrderAmt"];

					$html .='<td style="text-align: right;"></td>';

					$challan_total = $challan_total + $ids["OrderAmt"];

					$html .='<td style="text-align: right;">0</td>';

					$html .='<td style="text-align: right;">0</td>';

					$html .='</tr>';

				}

				

				$html .='<tfoot><tr>';

				

				$html .='<td style="text-align:center;">Total</td><td></td><td></td><td></td><td></td><td></td><td></td>';

				foreach ($get_order_list["item_list"] as $key => $code1) {

					

					$item_count = $this->challan_model->get_itemcout_all_order($id,$code1["ItemID"]);

					

					$item_count_new = (int) $item_count->OrderQty;

					$html .='<td style="text-align: right;">'.$item_count_new.'</td>';

				}

				$html .='<td style="text-align: right;">'.$challan_crate.'</td>';

				$html .='<td style="text-align: right;">'.$challan_cases.'</td>';

				$html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';

				$html .='<td style="text-align: right;">'.$challan_total.'</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='</tr></tfoot>';

				

				$html .='</tbody>';

				$html .='</table>';
				
				// Include food license status in HTML as JSON script tag for backward compatibility
				if (!empty($foodLicenseStatus)) {
					$html .= '<script type="application/json" id="food-license-status-data">' . json_encode($foodLicenseStatus) . '</script>';
				}

			}

			

			echo json_encode($html);

		}

		

		//------------------- List of Order By route-------------------------------

		public function get_order_by_route()

		{

			$selected_company = $this->session->userdata('root_company');

			$id = $this->input->post('id'); 

			

			$get_acc_by_route = $this->challan_model->get_acc_by_route($id);

			

			$order_ids = array();

			$account_ids = array();

			

			

			foreach ($get_acc_by_route as $key => $value) {

				

				array_push($account_ids,$value['AccountID']);

			}

			$order_ids_details = $this->challan_model->getorderlist_by_accId($account_ids);

			

			foreach ($order_ids_details as $key1 => $value1) {

				

				array_push($order_ids,$value1['OrderID']);

			}

			

			

			if($order_ids){

				$item_code_list_new = array();

				

				

				

				$item_code_list = $this->challan_model->get_item_code_list_by_order_ids($order_ids);

				

				foreach ($item_code_list as $key2 => $value2) {

					

					array_push($item_code_list_new,$value2['ItemID']);

				}

				

				$item_code_list_new_unique = array_unique($item_code_list_new);

				

				$html = '';

				$html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;"><thead style="background: #438EB9;color: #FFF;">';

				$html .='<th>Tag</th>';

				$html .='<th>OrderNo</th>';

				$html .='<th>AccountName</th>';

				$html .='<th>StateID</th>';

				$html .='<th>Ordertype</th>';

				$html .='<th>SalesID</th>';

				$html .='<th>SalesDate</th>';

				foreach ($item_code_list_new_unique as $code) {

					$html .='<th width="5%">'.$code.'</th>';

				}

				$html .='<th>Crates</th>';

				$html .='<th>Cases</th>';

				$html .='<th>OrderAmt</th>';

				$html .='<th>SaleAmt</th>';

				$html .='<th>TCSPer</th>';

				$html .='<th>TCSAmt</th>';

				$html .='</thead>';

				$challan_cases = 0;

				$challan_crate = 0;

				$challan_subtotal = 0;

				$challan_total = 0;

				$html .='<tbody>';

				foreach ($order_ids as $ids) {

					$html .='<tr>';

					$order_data = $this->challan_model->getorderdetail_by_orderId($ids);

					$html .='<td><input type="checkbox" name="order_id[]" class="chk" value="'.$ids.'"></td>';

					$html .='<td>'.$ids.'</td>';

					$account_name = get_account_name($order_data->AccountID,$selected_company);

					$html .='<td>'.$account_name->company.'</td>';

					$html .='<td>'.$order_data->client->state.'</td>';

					$html .='<td>'.$order_data->OrderType.'</td>';

					$html .='<td></td>';

					

					$html .='<td></td>';

					

					foreach ($item_code_list_new_unique as $code) {

						$item_data1 = $this->challan_model->get_order_singleitem($ids,$code);

						if($item_data1){

							if(is_null($item_data1->eOrderQty)){

								$qty1 = $item_data1->OrderQty / $item_data1->CaseQty;

								}else{

								$qty1 = $item_data1->eOrderQty / $item_data1->CaseQty;

							}

							

							$html .='<td width="5%"><input style="width: 50px;" type="text" name="qty" value="'.$qty1.'"></td>';

							}else{

							

							$html .='<td></td>';

						}

						

					}

					

					$html .='<td style="text-align: right;">'.$order_data->Crates.'</td>';

					$challan_crate = $challan_crate + $order_data->Crates;

					$html .='<td style="text-align: right;">'.$order_data->Cases.'</td>';

					$challan_cases = $challan_cases + $order_data->Cases;

					$html .='<td style="text-align: right;">'.$order_data->OrderAmt.'</td>';

					$challan_subtotal = $challan_subtotal + $order_data->OrderAmt;

					$html .='<td style="text-align: right;"></td>';

					$challan_total = $challan_total + $order_data->OrderAmt;

					$html .='<td style="text-align: right;">0</td>';

					$html .='<td style="text-align: right;">0</td>';

					$html .='</tr>';

				}

				

				$html .='<tfoot><tr>';

				

				$html .='<td style="text-align:center;">Total</td><td></td><td></td><td></td><td></td><td></td><td></td>';

				foreach ($item_code_list_new_unique as $code) {

					

					$item_count = $this->challan_model->get_itemcout_all_order($order_ids,$code);

					

					$item_count_new = (int) $item_count;

					$html .='<td style="text-align: right;">'.$item_count_new.'</td>';

				}

				$html .='<td style="text-align: right;">'.$challan_crate.'</td>';

				$html .='<td style="text-align: right;">'.$challan_cases.'</td>';

				$html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';

				$html .='<td style="text-align: right;">'.$challan_total.'</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='<td style="text-align: right;">0</td>';

				$html .='</tr></tfoot>';

				

				

				$html .='</tbody>';

				$html .='</table>';

				

				/*echo json_encode($html);

				die;*/

				/*//

					$html = '';

					$html .='<table width="100%" id="challan_data" border="1" style="display: block;overflow: scroll;white-space: nowrap;"><thead style="background: #438EB9;color: #FFF;">';

					$html .='<th>Tag</th>';

					$html .='<th>OrderNo</th>';

					$html .='<th>AccountName</th>';

					$html .='<th>StateID</th>';

					$html .='<th>Ordertype</th>';

					$html .='<th>SalesID</th>';

					$html .='<th>SalesDate</th>';

					foreach ($item_code_list_new_unique as $code) {

					$html .='<th width="5%">'.$code.'</th>';

					}

					$html .='<th>Crates</th>';

					$html .='<th>Cases</th>';

					$html .='<th>OrderAmt</th>';

					$html .='<th>SaleAmt</th>';

					$html .='<th>TCSPer</th>';

					$html .='<th>TCSAmt</th>';

					$html .='</thead>';

					$challan_cases = 0;

					$challan_crate = 0;

					$challan_subtotal = 0;

					$challan_total = 0;

					

					$html .='<tbody>';

					

					

					foreach ($order_ids as $ids) {

					$html .='<tr>';

					$order_data = $this->challan_model->getorderdetail_by_orderId($ids);

					$html .='<td><input type="checkbox" name="order_id[]" class="chk" value="'.$ids.'"></td>';

					$html .='<td>'.$ids.'</td>';

					$account_name = get_account_name($order_data->AccountID,$selected_company);

					$html .='<td>'.$account_name->company.'</td>';

					$html .='<td>'.get_state_code($order_data->client->billing_state).'</td>';

					$html .='<td>'.$order_data->OrderType.'</td>';

					$html .='<td></td>';

					

					$html .='<td></td>';

					

					foreach ($item_code_list_new_unique as $code) {

					$item_data1 = $this->challan_model->get_order_singleitem($ids,$code);

					

					if($item_data1){

					

					$html .='<td width="5%"><input style="width: 50px;" type="text" onchange="total()" name="qty_'.$ids.'_'.$item_data1->ItemID.'" value="'.$item_data1->OrderQty / $item_data1->CaseQty.'"></td>';

					

					}else {

					

					$html .='<td width="5%"><input style="width: 50px;" type="text" onchange="total()" name="qty_'.$ids.'_'.$item_data1->ItemID.'" value="0"></td>';

					}

					}

					

					$html .='<td style="text-align: right;">'.$order_data->Crates.'</td>';

					$challan_crate = $challan_crate + $order_data->Crates;

					$html .='<td style="text-align: right;">'.$order_data->Cases.'</td>';

					$challan_cases = $challan_cases + $order_data->Cases;

					$html .='<td style="text-align: right;">'.$order_data->subtotal.'</td>';

					$challan_subtotal = $challan_subtotal + $order_data->subtotal;

					$html .='<td style="text-align: right;">'.$order_data->OrderAmt.'</td>';

					$challan_total = $challan_total + $order_data->OrderAmt;

					$html .='<td style="text-align: right;">0</td>';

					$html .='<td style="text-align: right;">0</td>';

					$html .='</tr>';

					}

					$html .='</tbody>';

					

					

					$html .='<tfoot><tr>';

					

					$html .='<td style="text-align:center;">Total</td><td></td><td></td><td></td><td></td><td></td><td></td>';

					foreach ($item_code_list as $code) {

					

					$item_count = $this->challan_model->get_itemcout_all_order($order_ids,$code);

					

					$item_count_new = (int) $item_count;

					$html .='<td style="text-align: right;">'.$item_count_new.'</td>';

					}

					$html .='<td style="text-align: right;">'.$challan_crate.'</td>';

					$html .='<td style="text-align: right;">'.$challan_cases.'</td>';

					$html .='<td style="text-align: right;">'.$challan_subtotal.'</td>';

					$html .='<td style="text-align: right;">'.$challan_total.'</td>';

					$html .='<td style="text-align: right;">0</td>';

					$html .='<td style="text-align: right;">0</td>';

					$html .='</tr></tfoot>';

				$html .='</table>';*/

				}else {

				$html = '<h3 style="color:#fc0a0b;">No Record Found...</h3>';

			}

			

			

			echo json_encode($html);

			die;

		}

		

		/* List all Gatepass datatables */

		public function view_gatepass($id = '')

		{

			

			close_setup_menu();

			

			

			$data['title']                = "View Gatepass";

			

			$data['bodyclass']            = 'invoices-total-manual';

			$this->load->view('admin/gatepass/manage', $data);

		}

		

		public function gatepass_list()

		{

			if (!has_permission_new('gatepass', '', 'view')) {

				ajax_access_denied();

			}

			if ($this->input->is_ajax_request()) {

				if($this->input->post()){

					$this->app->get_table_data('gatepass');

				}

			}

		}

		

		/* List all recurring invoices */

		public function recurring($id = '')

		{

			

			

			close_setup_menu();

			

			$data['invoiceid']            = $id;

			$data['title']                = _l('invoices_list_recurring');

			$data['invoices_years']       = $this->invoices_model->get_invoices_years();

			$data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();

			$this->load->view('admin/invoices/recurring/list', $data);

		}

		

		public function table($clientid = '')

		{

			

			

			$this->app->get_table_data(($this->input->get('recurring') ? 'recurring_invoices' : 'challan'), [

			'clientid' => $clientid,

			'data'     => $data,

			]);

		}

		

		public function client_change_data($customer_id, $current_invoice = '')

		{

			if ($this->input->is_ajax_request()) {

				$this->load->model('projects_model');

				$this->load->model('invoice_items_model');

				$data                     = [];

				$data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);

				$data['client_currency']  = $this->clients_model->get_customer_default_currency($customer_id);

				$data['client_details']  = $this->clients_model->get($customer_id);

				$client_item_div = unserialize($data['client_details']->itemdivision);

				$client_item_div2 = implode(" ",$client_item_div);

				$data['client_details']->itemdivision = $client_item_div2;

				//$data['division'] = $client_item_div;

				$data['item_data'] = $this->invoice_items_model->get2($client_item_div);

				$data['customer_groups'] = $this->clients_model->get_customer_groups($customer_id);

				$data['customer_groups_name'] = $this->clients_model->get_customer_groups_name($data['customer_groups']['0']['groupid']);

				$data['customer_has_projects'] = customer_has_projects($customer_id);

				$data['billable_tasks']        = $this->tasks_model->get_billable_tasks($customer_id);

				

				if ($current_invoice != '') {

					$this->db->select('status');

					$this->db->where('id', $current_invoice);

					$current_invoice_status = $this->db->get(db_prefix() . 'invoices')->row()->status;

				}

				

				$_data['invoices_to_merge'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->check_for_merge_invoice($customer_id, $current_invoice) : [];

				

				$data['merge_info'] = $this->load->view('admin/invoices/merge_invoice', $_data, true);

				

				$this->load->model('currencies_model');

				

				$__data['expenses_to_bill'] = !isset($current_invoice_status) || (isset($current_invoice_status) && $current_invoice_status != Invoices_model::STATUS_CANCELLED) ? $this->invoices_model->get_expenses_to_bill($customer_id) : [];

				

				$data['expenses_bill_info'] = $this->load->view('admin/invoices/bill_expenses', $__data, true);

				echo json_encode($data);

			}

		}

		

		

		

		public function add_note($rel_id)

		{

			if ($this->input->post() && user_can_view_invoice($rel_id)) {

				$this->misc_model->add_note($this->input->post(), 'invoice', $rel_id);

				echo $rel_id;

			}

		}

		

		public function get_notes($id)

		{

			if (user_can_view_invoice($id)) {

				$data['notes'] = $this->misc_model->get_notes($id, 'invoice');

				$this->load->view('admin/includes/sales_notes_template', $data);

			}

		}

		

		public function pause_overdue_reminders($id)

		{

			if (has_permission('challan', '', 'edit')) {

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 1]);

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function resume_overdue_reminders($id)

		{

			if (has_permission('challan', '', 'edit')) {

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'invoices', ['cancel_overdue_reminders' => 0]);

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function mark_as_cancelled($id)

		{

			if (!has_permission('challan', '', 'edit') && !has_permission('challan', '', 'create')) {

				access_denied('invoices');

			}

			

			$success = $this->invoices_model->mark_as_cancelled($id);

			

			if ($success) {

				set_alert('success', _l('invoice_marked_as_cancelled_successfully'));

			}

			

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function unmark_as_cancelled($id)

		{

			if (!has_permission('invoices', '', 'edit') && !has_permission('invoices', '', 'create')) {

				access_denied('invoices');

			}

			$success = $this->invoices_model->unmark_as_cancelled($id);

			if ($success) {

				set_alert('success', _l('invoice_unmarked_as_cancelled'));

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function copy($id)

		{

			if (!$id) {

				redirect(admin_url('invoices'));

			}

			if (!has_permission('invoices', '', 'create')) {

				access_denied('invoices');

			}

			$new_id = $this->invoices_model->copy($id);

			if ($new_id) {

				set_alert('success', _l('invoice_copy_success'));

				redirect(admin_url('invoices/invoice/' . $new_id));

				} else {

				set_alert('success', _l('invoice_copy_fail'));

			}

			redirect(admin_url('invoices/invoice/' . $id));

		}

		

		public function get_merge_data($id)

		{

			$invoice = $this->invoices_model->get($id);

			$cf      = get_custom_fields('items');

			

			$i = 0;

			

			foreach ($invoice->items as $item) {

				$invoice->items[$i]['taxname']          = get_invoice_item_taxes($item['id']);

				$invoice->items[$i]['long_description'] = clear_textarea_breaks($item['long_description']);

				$this->db->where('item_id', $item['id']);

				$rel              = $this->db->get(db_prefix() . 'related_items')->result_array();

				$item_related_val = '';

				$rel_type         = '';

				foreach ($rel as $item_related) {

					$rel_type = $item_related['rel_type'];

					$item_related_val .= $item_related['rel_id'] . ',';

				}

				if ($item_related_val != '') {

					$item_related_val = substr($item_related_val, 0, -1);

				}

				$invoice->items[$i]['item_related_formatted_for_input'] = $item_related_val;

				$invoice->items[$i]['rel_type']                         = $rel_type;

				

				$invoice->items[$i]['custom_fields'] = [];

				

				foreach ($cf as $custom_field) {

					$custom_field['value']                 = get_custom_field_value($item['id'], $custom_field['id'], 'items');

					$invoice->items[$i]['custom_fields'][] = $custom_field;

				}

				$i++;

			}

			echo json_encode($invoice);

		}

		

		public function get_bill_expense_data($id)

		{

			$this->load->model('expenses_model');

			$expense = $this->expenses_model->get($id);

			

			$expense->qty              = 1;

			$expense->long_description = clear_textarea_breaks($expense->description);

			$expense->description      = $expense->name;

			$expense->rate             = $expense->amount;

			if ($expense->tax != 0) {

				$expense->taxname = [];

				array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);

			}

			if ($expense->tax2 != 0) {

				array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);

			}

			echo json_encode($expense);

		}

		

		public function challan($id = '')

		{

			$redUrl = admin_url('challan/challanAddEdit');

			redirect($redUrl);

			

		}

		

		/* Add new Challan or update existing */

		public function challan2($id = '')

		{

			if ($this->input->post()) {

				$invoice_data = $this->input->post();

				if ($id == '') {

					if (!has_permission('challan', '', 'create')) {

						access_denied('challan');

					}

					

					$id = $this->order_model->add($invoice_data);

					if ($id == false) {

						set_alert('warning', "Challan already created for this order");

						redirect(admin_url('challan/challan_list'));

						

						}else{

						set_alert('success', _l('added_successfully', _l('invoice')));

						$redUrl = admin_url('order/list_orders/' . $id);

						redirect($redUrl);

					}

					} else {

					if (!has_permission('challan', '', 'edit')) {

						access_denied('challan');

					}

					$success = $this->invoices_model->update($invoice_data, $id);

					if ($success) {

						set_alert('success', _l('updated_successfully', _l('invoice')));

					}

					redirect(admin_url('order/list_orders/' . $id));

				}

			}

			if ($id == '') {

				$title                  = _l('create_new_order');

				$data['billable_tasks'] = [];

				} else {

				$invoice = $this->invoices_model->get($id);

				

				if (!$invoice || !user_can_view_invoice($id)) {

					blank_page(_l('invoice_not_found'));

				}

				

				$data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);

				$data['expenses_to_bill']  = $this->invoices_model->get_expenses_to_bill($invoice->clientid);

				

				$data['invoice']        = $invoice;

				$data['edit']           = true;

				$data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid, !empty($invoice->project_id) ? $invoice->project_id : '');

				

				$title = _l('edit', _l('invoice_lowercase')) . ' - ' . format_invoice_number($invoice->id);

			}

			

			if ($this->input->get('customer_id')) {

				$data['customer_id'] = $this->input->get('customer_id');

			}

			

			$this->load->model('payment_modes_model');

			$data['payment_modes'] = $this->payment_modes_model->get('', [

			'expenses_only !=' => 1,

			]);

			

			$this->load->model('taxes_model');

			$data['taxes'] = $this->taxes_model->get();

			$this->load->model('invoice_items_model');

			

			$data['ajaxItems'] = false;

			if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {

				$data['items'] = $this->invoice_items_model->get_grouped();

				} else {

				$data['items']     = [];

				$data['ajaxItems'] = true;

			}

			$data['items_groups'] = $this->invoice_items_model->get_groups();

			

			$this->load->model('currencies_model');

			$this->load->model('clients_model');

			$data['currencies'] = $this->currencies_model->get();

			

			$data['base_currency'] = $this->currencies_model->get_base_currency();

			

			$data['staff']     = $this->staff_model->get('', ['active' => 1]);

			$data['rootcompany'] = $this->clients_model->get_rootcompany();

			// Customer groups

			$data['groups'] = $this->clients_model->get_groups();

			$data['title']     = $title;

			$data['bodyclass'] = 'invoice';

			$this->load->view('admin/order/order', $data);

		}

		

		/* Get all invoice data used when user click on invoiec number in a datatable left side*/

		public function get_order_data_ajax($id)

		{

			if (!has_permission('challan', '', 'view')

			&& !has_permission('challan', '', 'view_own')

			&& get_option('allow_staff_view_invoices_assigned') == '0') {

				echo _l('access_denied');

				die;

			}

			

			if (!$id) {

				die(_l('invoice_not_found'));

			}

			

			$invoice = $this->order_model->get($id);

			

			if (!$invoice || !user_can_view_invoice($id)) {

				echo _l('invoice_not_found');

				die;

			}

			

			$template_name = 'invoice_send_to_customer';

			

			if ($invoice->sent == 1) {

				$template_name = 'invoice_send_to_customer_already_sent';

			}

			

			$data = prepare_mail_preview_data($template_name, $invoice->clientid);

			

			// Check for recorded payments

			$this->load->model('payments_model');

			$data['invoices_to_merge']          = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $id);

			$data['members']                    = $this->staff_model->get('', ['active' => 1]);

			$data['payments']                   = $this->payments_model->get_invoice_payments($id);

			$data['activity']                   = $this->invoices_model->get_invoice_activity($id);

			$data['totalNotes']                 = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'invoice']);

			$data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recurring_invoices($id);

			

			$data['applied_credits'] = $this->credit_notes_model->get_applied_invoice_credits($id);

			// This data is used only when credit can be applied to invoice

			if (credits_can_be_applied_to_invoice($invoice->status)) {

				$data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($invoice->clientid);

				

				if ($data['credits_available'] > 0) {

					$data['open_credits'] = $this->credit_notes_model->get_open_credits($invoice->clientid);

				}

				

				$customer_currency = $this->clients_model->get_customer_default_currency($invoice->clientid);

				$this->load->model('currencies_model');

				

				if ($customer_currency != 0) {

					$data['customer_currency'] = $this->currencies_model->get($customer_currency);

					} else {

					$data['customer_currency'] = $this->currencies_model->get_base_currency();

				}

			}

			

			$data['invoice'] = $invoice;

			$data['invoice_generate'] = $this->order_model->check_invoice_generate($id);

			$data['record_payment'] = false;

			$data['send_later']     = false;

			

			if ($this->session->has_userdata('record_payment')) {

				$data['record_payment'] = true;

				$this->session->unset_userdata('record_payment');

				} elseif ($this->session->has_userdata('send_later')) {

				$data['send_later'] = true;

				$this->session->unset_userdata('send_later');

			}

			

			$this->load->view('admin/order/invoice_preview_template', $data);

		}

		

		public function apply_credits($invoice_id)

		{

			$total_credits_applied = 0;

			foreach ($this->input->post('amount') as $credit_id => $amount) {

				$success = $this->credit_notes_model->apply_credits($credit_id, [

				'invoice_id' => $invoice_id,

				'amount'     => $amount,

				]);

				if ($success) {

					$total_credits_applied++;

				}

			}

			

			if ($total_credits_applied > 0) {

				update_invoice_status($invoice_id, true);

				set_alert('success', _l('invoice_credits_applied'));

			}

			redirect(admin_url('order/list_orders/' . $invoice_id));

		}

		

		public function get_invoices_total()

		{

			if ($this->input->post()) {

				load_invoices_total_template();

			}

		}

		

		/* Record new inoice payment view */

		public function record_invoice_payment_ajax($id)

		{

			$this->load->model('payment_modes_model');

			$this->load->model('payments_model');

			$data['payment_modes'] = $this->payment_modes_model->get('', [

			'expenses_only !=' => 1,

			]);

			$data['invoice']  = $this->invoices_model->get($id);

			$data['payments'] = $this->payments_model->get_invoice_payments($id);

			$this->load->view('admin/invoices/record_payment_template', $data);

		}

		

		/* Record new inoice  */

		public function crate_invoice_by_ajax($id)

		{

			$this->load->model('payment_modes_model');

			$this->load->model('payments_model');

			$data['payment_modes'] = $this->payment_modes_model->get('', [

			'expenses_only !=' => 1,

			]);

			$order  = $this->order_model->get($id);

			//echo "<pre>";

			$addedfrom = !DEFINED('CRON') ? get_staff_user_id() : 0;

			$invoicedata=array(

			"sent"=>$order->sent,

			"datesend"=>$order->datesend,

			"clientid"=>$order->clientid,

			"deleted_customer_name"=>$order->deleted_customer_name,

			"order_id"=>$order->number,

			"order_type"=>$order->order_type,

			"dist_comp"=>$order->dist_comp,

			"dist_sale_agent"=>$order->dist_sale_agent,

			"prefix"=>'INV-',

			"number_format"=>$order->number_format,

			"datecreated"=>date('Y-m-d H:i:s'),

			"date"=>date('Y-m-d'),

			"currency"=>$order->currency,

			"subtotal"=>$order->subtotal,

			"total_tax"=>$order->total_tax,

			"total"=>$order->total,

			"total_cases"=>$order->total_cases,

			"adjustment"=>$order->adjustment,

			"addedfrom"=>$addedfrom,

			"hash"=>$order->hash,

			"status"=>$order->status,

			"allowed_payment_modes"=>$order->allowed_payment_modes,

			"token"=>$order->token,

			"discount_percent"=>$order->discount_percent,

			"discount_total"=>$order->discount_total,

			"discount_type"=>$order->discount_type,

			"sale_agent"=>$order->sale_agent,

			"billing_street"=>$order->billing_street,

			"billing_city"=>$order->billing_city,

			"billing_state"=>$order->billing_state,

			"billing_zip"=>$order->billing_zip,

			"billing_country"=>$order->billing_country,

			"shipping_street"=>$order->shipping_street,

			"shipping_state"=>$order->shipping_state,

			"shipping_city"=>$order->shipping_city,

			"shipping_zip"=>$order->shipping_zip,

			"shipping_country"=>$order->shipping_country,

			"include_shipping"=>$order->include_shipping,

			"show_shipping_on_invoice"=>$order->show_shipping_on_invoice,

			"show_quantity_as"=>$order->show_quantity_as,

			"subscription_id"=>$order->subscription_id,

			"short_link"=>$order->short_link,

			"project_id"=>$order->project_id

			);

			

			$items = $order->items;

			/*foreach ($items as $key => $value) {

				# code...

				echo $value["description"];

				echo "<br>";

				

			}*/

			//print_r($items);

			//echo $items[0]['rel_type'];

			//die;

			$this->db->insert(db_prefix() . 'invoices', $invoicedata);

			$invoice_id = $this->db->insert_id();

			if($invoice_id){

				

				$this->db->where('id', $invoice_id);

				$this->db->update(db_prefix() . 'invoices', [

				'number' => $invoice_id,

				]);

				

				foreach ($items as $key => $item) {

					# code...

					

					$itemdata=array(

					"rel_id"=>$invoice_id,

					"rel_type"=>$item['rel_type'],

					"description"=>$item['description'],

					"long_description"=>$item['long_description'],

					"hsn_code"=>$item['hsn_code'],

					"qty"=>$item['qty'],

					"pack_qty"=>$item['pack_qty'],

					"rate"=>$item['rate'],

					"total_amt"=>$item['total_amt'],

					"discount_amt"=>$item['discount_amt'],

					"taxable_amt"=>$item['taxable_amt'],

					"gst"=>$item['gst'],

					"gst_amt"=>$item['gst_amt'],

					"unit"=>$item['unit'],

					"grand_total"=>$item['grand_total'],

					"item_order"=>$item['item_order']

					);

					$this->db->insert(db_prefix() . 'itemable', $itemdata);

				}        

				

			}

			

			redirect(admin_url('order/get_order_data_ajax/' . $id));

			

		}

		

		/* This is where invoice payment record $_POST data is send */

		public function record_payment()

		{

			if (!has_permission('payments', '', 'create')) {

				access_denied('Record Payment');

			}

			if ($this->input->post()) {

				$this->load->model('payments_model');

				$id = $this->payments_model->process_payment($this->input->post(), '');

				if ($id) {

					set_alert('success', _l('invoice_payment_recorded'));

					redirect(admin_url('payments/payment/' . $id));

					} else {

					set_alert('danger', _l('invoice_payment_record_failed'));

				}

				redirect(admin_url('order/list_orders/' . $this->input->post('invoiceid')));

			}

		}

		

		/* Send invoice to email */

		public function send_to_email($id)

		{

			$canView = user_can_view_invoice($id);

			if (!$canView) {

				access_denied('Invoices');

				} else {

				if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {

					access_denied('Invoices');

				}

			}

			

			try {

				$statementData = [];

				if ($this->input->post('attach_statement')) {

					$statementData['attach'] = true;

					$statementData['from']   = to_sql_date($this->input->post('statement_from'));

					$statementData['to']     = to_sql_date($this->input->post('statement_to'));

				}

				

				$success = $this->invoices_model->send_invoice_to_client(

				$id,

				'',

				$this->input->post('attach_pdf'),

				$this->input->post('cc'),

				false,

				$statementData

				);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			// In case client use another language

			load_admin_language();

			if ($success) {

				set_alert('success', _l('invoice_sent_to_client_success'));

				} else {

				set_alert('danger', _l('invoice_sent_to_client_fail'));

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		/* Delete invoice payment*/

		public function delete_payment($id, $invoiceid)

		{

			if (!has_permission('payments', '', 'delete')) {

				access_denied('payments');

			}

			$this->load->model('payments_model');

			if (!$id) {

				redirect(admin_url('payments'));

			}

			$response = $this->payments_model->delete($id);

			if ($response == true) {

				set_alert('success', _l('deleted', _l('payment')));

				} else {

				set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));

			}

			redirect(admin_url('order/list_orders/' . $invoiceid));

		}

		

		/* Delete invoice */

		public function delete($id)

		{

			if (!has_permission('invoices', '', 'delete')) {

				access_denied('invoices');

			}

			if (!$id) {

				redirect(admin_url('order/list_orders'));

			}

			$success = $this->invoices_model->delete($id);

			

			if ($success) {

				set_alert('success', _l('deleted', _l('invoice')));

				} else {

				set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));

			}

			if (strpos($_SERVER['HTTP_REFERER'], 'list_orders') !== false) {

				redirect(admin_url('order/list_orders'));

				} else {

				redirect($_SERVER['HTTP_REFERER']);

			}

		}

		

		public function delete_attachment($id)

		{

			$file = $this->misc_model->get_file($id);

			if ($file->staffid == get_staff_user_id() || is_admin()) {

				echo $this->invoices_model->delete_attachment($id);

				} else {

				header('HTTP/1.0 400 Bad error');

				echo _l('access_denied');

				die;

			}

		}

		

		/* Will send overdue notice to client */

		public function send_overdue_notice($id)

		{

			$canView = user_can_view_invoice($id);

			if (!$canView) {

				access_denied('Invoices');

				} else {

				if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own') && $canView == false) {

					access_denied('Invoices');

				}

			}

			

			$send = $this->invoices_model->send_invoice_overdue_notice($id);

			if ($send) {

				set_alert('success', _l('invoice_overdue_reminder_sent'));

				} else {

				set_alert('warning', _l('invoice_reminder_send_problem'));

			}

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		/* Generates invoice PDF and senting to email of $send_to_email = true is passed */

		public function pdf($id)

		{

			if (!$id) {

				redirect(admin_url('challan/challan_list'));

			}

			

			if (!has_permission_new('challan_list', '', 'view')) {

				access_denied('Invoices');

			}

			

			$invoice        = $this->challan_model->getchallandetail($id);

			//print_r($invoice);

			

			$invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);

			//$invoice_number = format_invoice_number($invoice->id);

			

			try {

				$pdf = invoice_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(mb_strtoupper(slug_it($id)) . '-Invoice.pdf', $type);

		}

		public function DeliveryNotePdf($id)

		{

			if (!$id) {

				redirect(admin_url('challan/challan_list'));

			}

			

			if (!has_permission_new('challan_list', '', 'view')) {

				access_denied('Invoices');

			}

			

			$invoice        = $this->challan_model->getchallandetail($id);

			//print_r($invoice);

			

			$invoice        = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);

			//$invoice_number = format_invoice_number($invoice->id);

			

			try {

				$pdf = deliverynote_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(mb_strtoupper(slug_it($id)) . '-Invoice.pdf', $type);

		}

		

		public function RouteMemo($id)

		{

			if (!$id) {

				redirect(admin_url('challan/challan_list'));

			}

			

			if (!has_permission_new('challan_list', '', 'view')) {

				access_denied('Invoices');

			}

			

			$invoice = $this->challan_model->get($id);

			//print_r($invoice);

			

			$invoice = hooks()->apply_filters('before_admin_view_invoice_pdf', $invoice);

			//$invoice_number = format_invoice_number($invoice->id);

			

			try {

				$pdf = RouteMemo_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			} 

			

			$pdf->Output(mb_strtoupper(slug_it($id)) . '-RouteMemo.pdf', $type);

		}

		

		public function dispatchsheet($challan_id)

		{

			if (!$challan_id) {

				redirect(admin_url('challan/challan_list'));

			}

			

			if (!has_permission_new('challan_list', '', 'view')) {

				access_denied('Invoices');

			}

			

			$invoice        = $this->challan_model->getchallandetail($challan_id);

			/*print_r($invoice);

			die;*/

			try {

				$pdf = dispatch_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(mb_strtoupper(slug_it($challan_id)) . '-Dispatch.pdf', $type);

		}

		

		/* Generates Gate Pass PDF and senting to email of $send_to_email = true is passed */

		public function gatepass($challan_id)

		{

			if (!$challan_id) {

				redirect(admin_url('challan/challan_list'));

			}

			

			if (!has_permission_new('gatepass', '', 'view')) {

				access_denied('Invoices');

			}

			

			$invoice        = $this->challan_model->getchallandetail($challan_id);

			if(is_null($invoice->Gatepassuserid)){

				$selected_company = $this->session->userdata('root_company');

				$fy = $this->session->userdata('finacial_year');

				$this->db->where('PlantID', $selected_company);

				$this->db->where('FY', $fy);  

				$this->db->where('ChallanID', $challan_id);

				$this->db->update(db_prefix() . 'challanmaster', [

				'gatepasstime' => date('Y-m-d H:i:s'),

				'Gatepassuserid' => $this->session->userdata('username'),

				'GetPassTime' => date('Y-m-d H:i:s'),

				]);

			}

			

			

			try {

				$pdf = gatepass_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(mb_strtoupper(slug_it($challan_id)) . '-Gatepass.pdf', $type);

		}

		

		

		public function mark_as_sent($id)

		{

			if (!$id) {

				redirect(admin_url('order/list_orders'));

			}

			if (!user_can_view_invoice($id)) {

				access_denied('Invoice Mark As Sent');

			}

			

			$success = $this->invoices_model->set_invoice_sent($id, true);

			

			if ($success) {

				set_alert('success', _l('invoice_marked_as_sent'));

				} else {

				set_alert('warning', _l('invoice_marked_as_sent_failed'));

			}

			

			redirect(admin_url('order/list_orders/' . $id));

		}

		

		public function get_due_date()

		{

			if ($this->input->post()) {

				$date    = $this->input->post('date');

				$duedate = '';

				if (get_option('invoice_due_after') != 0) {

					$date    = to_sql_date($date);

					$d       = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));

					$duedate = _d($d);

					echo $duedate;

				}

			}

		}

		

		public function OtherPOpdf($id)

		{

			if (!$id) {

				redirect(admin_url('purchase/purchase_Po'));

			}

			

			if (!has_permission_new('purchase-order-po', '', 'view')) {

				access_denied('Invoices');

			}

			$invoice  = $this->challan_model->get_order_entry_details_PO($id);

			

			//print_r($invoice); die;

			try {

				$pdf = PO_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			// print_r($pdf);

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(mb_strtoupper(slug_it($id)) . '-PurchaseOrder.pdf', $type);

		}

		public function PurchEntrypdf($id)

		{

			if (!$id) {

				redirect(admin_url('purchase/pur_order'));

			}

			

			if (!has_permission_new('purchase-order', '', 'view')) {

				access_denied('Invoices');

			}

			$invoice        = $this->challan_model->get_order_entry_details($id);

			

			

			try {

				$pdf = PO_Entry_pdf($invoice);

				} catch (Exception $e) {

				$message = $e->getMessage();

				echo $message;

				if (strpos($message, 'Unable to get the size of the image') !== false) {

					show_pdf_unable_to_get_image_size_error();

				}

				die;

			}

			// print_r($pdf);

			$type = 'D';

			

			if ($this->input->get('output_type')) {

				$type = $this->input->get('output_type');

			}

			

			if ($this->input->get('print')) {

				$type = 'I';

			}

			

			$pdf->Output(mb_strtoupper(slug_it($id)) . '-PurchaseEntry.pdf', $type);

		}

		

		public function GetSchemeData()

		{

			$qty=$this->input->post('qty'); 

			$DistType=$this->input->post('DistType'); 

			$State=$this->input->post('State'); 

			$date=$this->input->post('date'); 

			$ItemID=$this->input->post('ItemID'); 

			$Scheme = $this->challan_model->GetSchemeData($DistType,$State,$date,$ItemID);

			$return = 0;

			foreach($Scheme as $each){

				if($qty >= $each['SlabQty']  &&  $each['SlabQty'] > 0){

					$Disc_pkt = floor($qty / $each['SlabQty']) * $each['Disc_pkt'];

					$return = $Disc_pkt;

					break;

				}

			}

			echo json_encode($return);

		}

		public function generateEInvoice()

		{

			

            $fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			$postData = $this->input->post();

			// Get data

			$Salesdata = $this->challan_model->GetTaxableTransaction($postData);

			$company_details = $this->challan_model->get_company_detail($selected_company);

			

			

			//E Invoice API

            $date = date("d/m/Y");

            $headersAuth = array(

			'email' => $company_details->einvoice_email,

			'username' => $company_details->einvoice_username,

			'password' => $company_details->einvoice_password,

			'ip_address' => $_SERVER['REMOTE_ADDR'],

			'client_id' => $company_details->einvoice_client_id,

			'client_secret' => $company_details->einvoice_client_secret,

			'gstin' => $company_details->einvoice_gstin,

            );   

            

            $base_url = 'https://api.mastergst.com/einvoice/authenticate';

            $query_params = http_build_query(array(

			'email' => $headersAuth['email'],

            ));

            // echo "<pre>";print_r($headersAuth);die;

            $curl = curl_init();

			

            curl_setopt_array($curl, array(

			CURLOPT_URL => $base_url . '?' . $query_params,

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_HTTPHEADER => array(

			'username: ' . $headersAuth['username'],

			'password: ' . $headersAuth['password'],

			'ip_address: ' . $headersAuth['ip_address'],

			'client_id: ' . $headersAuth['client_id'],

			'client_secret: ' . $headersAuth['client_secret'],

			'gstin: ' . $headersAuth['gstin'],

			),

            ));

			

            $api_response = curl_exec($curl);

			

            if ($api_response === false) {

                return $api_response;

			}

			

            curl_close($curl);

			

            // // Decode the JSON response

            $response_data = json_decode($api_response, true);

			

            // // Return the AuthToken

            $authKey = $response_data['data']['AuthToken'];

            

            $headersGenerateInvoice = array(

			'email' => $company_details->einvoice_email,

			'username' => $company_details->einvoice_username,

			'ip_address' => $_SERVER['REMOTE_ADDR'],

			'client_id' => $company_details->einvoice_client_id,

			'client_secret' => $company_details->einvoice_client_secret,

			'authToken' => $authKey,

			'gstin' => $company_details->einvoice_gstin

            );

			

			$authHeaders = [

			'email'         => $company_details->eway_email,

			'username'      => $company_details->eway_username,

			'password'      => $company_details->eway_password,

			'ip_address'    => $_SERVER['REMOTE_ADDR'],

			'client_id'     => $company_details->eway_client_id,

			'client_secret' => $company_details->eway_client_secret,

			'gstin'         => $company_details->eway_gstin,

			];

			

			

            

			$return = false;

			$ErrorMsg = '';

			$SuccessMsg = '';

            //Fetch Item Details

			foreach($Salesdata as $Skey => $Sval){

				if(!empty($Sval['gstno']) && ($Sval['irn'] == '' || $Sval['irn'] == null)){

					$itemDetails = $this->challan_model->fetchItemDetails($Sval['SalesID']);

					$SalesID = $Sval['SalesID'];

					

					if($Sval['city_name'] == ""){

						$location = $Sval['city'];

						}else{

						$location = $Sval['city_name'];

					}

					$Loc = $location;

					$Pin = $Sval['zip'];

					

					// $ewayData = [

					// "supplyType" => "O",

					// "subSupplyType" => "1",

					// "subSupplyDesc" => " ",

					// "docType" => "INV",

					// "docNo" => $Sval['SalesID'],

					// "docDate" => date("d/m/Y"),

					// "fromGstin" => $company_details->eway_gstin,

					// "fromTrdName" => $company_details->company_name,

					// "fromAddr1" => $company_details->address,

					// "fromAddr2" => " ",

					// "fromPlace" => $company_details->city,

					// "actFromStateCode" => (int) sprintf('%02d', $company_details->eway_statecode),

					// "fromPincode" => (int)$company_details->pincode,

					// "fromStateCode" => (int) sprintf('%02d', $company_details->eway_statecode),

					// "toGstin" => $Sval['gstno'],

					// "toTrdName" => $Sval['company'],

					// "toAddr1" => $Sval['address'],

					// "toAddr2" => " ",

					// "toPlace" => $Loc,

					// "toPincode" => (int) $Pin,

					// "actToStateCode" => (int) sprintf('%02d', $Sval['StateId']),

					// "toStateCode" => (int) sprintf('%02d', $Sval['StateId']),

					// "transactionType" => 4,

					// "dispatchFromGSTIN" => $company_details->eway_gstin,

					// "dispatchFromTradeName" => $company_details->company_name,

					// "shipToGSTIN" => $Sval['gstno'],

					// "shipToTradeName" => $Sval['company'],

					// "totalValue" => floatval($Sval['SaleAmt']),

					// "cgstValue" => floatval($Sval['cgstamt']),

					// "sgstValue" => floatval($Sval['sgstamt']),

					// "igstValue" => floatval($Sval['igstamt']),

					// "cessValue" => 0,

					// "cessNonAdvolValue" => 0,

					// "totInvValue" => floatval($Sval['RndAmt']),

					// "transMode" => "1",

					// "transDistance" => "67",

					// "transporterName" => "",

					// "transporterId" => "05AAACG0904A1ZL",

					// "transDocNo" => "12",

					// "transDocDate" => date("d/m/Y"),

					// "vehicleNo" => $Sval['VehicleID'],

					// "vehicleType" => "R",

					// "itemList" => []

					// ];

					

					

					$i = 0;

					$SlNo = 1;

					$newItemList = array();

					foreach ($itemDetails as $value) {

						

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

						

						// $ewayData['itemList'][] = [

						// "productName"   => $value['description'],

						// "productDesc"   => $value['description'],

						// "hsnCode"       => $value['hsn_code'],

						// "quantity"      => floatval($value['BilledQty']),

						// "qtyUnit"       => 'PCS',// $value['unit']

						// "cgstRate"      => floatval($value['cgst']),

						// "sgstRate"      => floatval($value['sgst']),

						// "igstRate"      => floatval($value['igst']),

						// "cessRate"      => 0,

						// "taxableAmount"=> floatval($value['ChallanAmt'])

						// ];

						

						$i++;

						$SlNo++;

					}

					

					

					

					$TrandId = $Sval['SalesID'];

					$IgstVal = $Sval['igstamt'];

					$CgstVal = $Sval['cgstamt'];

					$SgstVal = $Sval['sgstamt'];

					$CesVal = 0;

					$StCesVal = 0;

					$Discount = $Sval['DiscAmt'];

					$OthChrg = $Sval['tcsAmt'];

					$rnd = $Sval['RndAmt'] - $Sval['BillAmt'];

					$RndOffAmt = number_format($rnd,2);

					$TotInvVal = $Sval['RndAmt'];

					

					$ValDtls = array(

					"AssVal"=>floatval($Sval['SaleAmt']+$Discount),

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

					

					

					//Buyer Details

					$pgst = $Sval['gstno'];

					$LglNm = $Sval['company'];

					$Addr1 = $Sval['address'];

					$Addr2 = $Sval['Address3'];

					$Stcd = $Sval['StateId'];

					$Pos_c = $Stcd;

					if($Sval['city_name'] == ""){

						$location = $Sval['city'];

						}else{

						$location = $Sval['city_name'];

					}

					$Loc = $location;

					$Pin = $Sval['zip'];

					$Ph = $Sval['phonenumber'];

					

					//Seller Details

					$Gstin_c = $company_details->gst;

					$LglNm_c = $company_details->company_name;

					$Addr1_c = $company_details->address;

					$Addr2_c = null;

					$Loc_c = $company_details->city;

					$Pin_c = $company_details->pincode;

					$Stcd_c = "09";

					$Ph_c = $company_details->mobile1;

					

					

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

					"Pin"=>(int)$Pin_c,

					"Stcd"=>$Stcd_c,

					"Ph"=>$Ph_c,

					);

					

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

					$InvoiceNo = $Sval['SalesID'];

					$body = [];

					array_push($body, array('sellerDetails' => $SellerDtls, 'buyerDetails' => $BuyerDtls, 'dispDtls' => $DispDtls, 'shipDtls' => $ShipDtls ,'itemList' => $newItemList, 'valDtls' => $ValDtls));

					$curl = curl_init();

					curl_setopt_array($curl, array(

					CURLOPT_URL => 'https://api.mastergst.com/einvoice/type/GENERATE/version/V1_03?email=ajinkya.bhalerao@globalinfocloud.com',

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_ENCODING => '',

					CURLOPT_MAXREDIRS => 10,

					CURLOPT_TIMEOUT => 0,

					CURLOPT_FOLLOWLOCATION => true,

					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

					CURLOPT_CUSTOMREQUEST => 'POST',

					CURLOPT_POSTFIELDS => '{

					"Version": "1.1",

					"TranDtls": {

					"TaxSch": "GST",

					"SupTyp": "B2B",

					"IgstOnIntra": "N",

					"RegRev": "N",

					"EcmGstin": null

					},

					"DocDtls": {

					"Typ": "INV",

					"No": "'. $InvoiceNo .'",

					"Dt": "'. $date .'"

					},

					"SellerDtls": {

					"Gstin": "29AABCT1332L000",

					"LglNm": "INDMARK PAPER FORM PRIVATE LIMITED",

					"TrdNm": "INDMARK PAPER FORM PRIVATE LIMITED",

					"Addr1": "Pune",

					"Addr2": null,

					"Loc": "Gorakhpur",

					"Pin": 560001,

					"Stcd": "29",

					"Ph": "7355356548"

					},

					"BuyerDtls": {

					"Gstin": "09AABCB2066P3ZB",

					"LglNm": "BRITANNIA INDUSTRIES LIMITED",

					"TrdNm": "BRITANNIA INDUSTRIES LIMITED",

					"Pos": "9",

					"Addr1": "GATA NO. 1801,1802K,1803 VILLAGE PARA KHADAULI TEHSIL NAWABGANJ BARABANKI ,UTTAR PRADESH",

					"Addr2": null,

					"Loc": "BARABANKI",

					"Pin": 273005,

					"Stcd": "9",

					"Ph": null

					},

					"DispDtls": ' . json_encode($body[0]['dispDtls']) . ',

					"ShipDtls": {

					"Gstin": "09AABCB2066P3ZB",

					"LglNm": "BRITANNIA INDUSTRIES LIMITED",

					"TrdNm": "BRITANNIA INDUSTRIES LIMITED",

					"Addr1": "GATA NO. 1801,1802K,1803 VILLAGE PARA KHADAULI TEHSIL NAWABGANJ BARABANKI ,UTTAR PRADESH",

					"Addr2": null,

					"Loc": "BARABANKI",

					"Pin": 273005,

					"Stcd": "9"

					},

					"ItemList": [

					{

					"SlNo": "1",

					"PrdDesc": "48191010",

					"IsServc": "N",

					"HsnCd": "48191010",

					"Barcde": null,

					"Qty": 500,

					"FreeQty": 0,

					"Unit": "Pcs",

					"UnitPrice": 8,

					"TotAmt": 4000,

					"Discount": 0,

					"PreTaxVal": 0,

					"AssAmt": 4000,

					"GstRt": 18,

					"IgstAmt": 720,

					"CgstAmt": 0,

					"SgstAmt": 0,

					"CesRt": 0,

					"CesAmt": 0,

					"CesNonAdvlAmt": 0,

					"StateCesRt": 0,

					"StateCesAmt": 0,

					"StateCesNonAdvlAmt": 0,

					"OthChrg": 0,

					"TotItemVal": 4720,

					"BchDtls": null

					}

					],

					"ValDtls": {

					"AssVal": 4000,

					"IgstVal": 720,

					"CgstVal": 0,

					"SgstVal": 0,

					"CesVal": 0,

					"StCesVal": 0,

					"Discount": 0,

					"OthChrg": 0,

					"RndOffAmt": 0,

					"TotInvVal": 4720

					}

					}',

					CURLOPT_HTTPHEADER => array(

					'ip_address: ' . $headersGenerateInvoice['ip_address'] . '',

					'client_id: ' . $headersGenerateInvoice['client_id'] . '',

					'client_secret: ' . $headersGenerateInvoice['client_secret'] . '',

					'username: ' . $headersGenerateInvoice['username'] . '',

					'auth-token:' . $headersGenerateInvoice['authToken'] . '',

					'gstin: ' . $headersGenerateInvoice['gstin'] . '',

					'Content-Type: application/json',

					),

					));

					$apiResponse = curl_exec($curl);

					curl_close($curl);

					$data = json_decode($apiResponse, true);

					$irn = $data['data']['Irn'];

					$signedQRCode = $data['data']['SignedQRCode'];

					$AckNo = $data['data']['AckNo'];

					$AckDt = $data['data']['AckDt'];

					$Status = $data['data']['Status'];

					$status_cd = $data['data']['status_cd'];

					$signedInvoice = $data['data']['SignedInvoice'];

					$status_desc = $data['status_desc'];

					

					$response = array(

					'IRN' => $irn,

					'SignedQRCode' => $signedQRCode,

					'AckNo' => $AckNo,

					'AckDate' => $AckDt,

					'Status' => $Status,

					'status_cd' => $status_cd,

					'status_desc' => $status_desc,

					'SignedInvoice' => $signedInvoice

					);

					$statusRes = json_decode($response['status_desc'], true); // decode it

					$errorMessage = $statusRes[0]['ErrorMessage'];

					if($response["Status"] == 'ACT'){

						//Update Table entry

						$updateArray = array(

						'irn' => $response['IRN'],

						'Qrcode' => $response['SignedQRCode'],

						'ackno' => $response['AckNo'],

						'ackdate' => $response['AckDate'],

						'SignedInvoice' => $response['SignedInvoice'],

						'irn_cancelled' => null,

						'cancel_remark' => null,

						'Lupdate' => date('Y-m-d H:i:s')

						);

						$this->db->where('SalesID', $Sval['SalesID']);

						$this->db->update(db_prefix() . 'salesmaster', $updateArray);

						$return = true;

						$SuccessMsg .= "E-Invoice Is Generated Successfully OrderID ".$Sval['OrderID']." . ";

						

						// $queryParams = http_build_query([

						// 'email'    => $authHeaders['email'],

						// 'username' => $authHeaders['username'],

						// 'password' => $authHeaders['password']

						// ]);

						

						// $authURL = "https://api.mastergst.com/ewaybillapi/v1.03/authenticate?" . $queryParams;

						

						// $ch = curl_init();

						// curl_setopt_array($ch, [

						// CURLOPT_URL            => $authURL,

						// CURLOPT_RETURNTRANSFER => true,

						// CURLOPT_HTTPHEADER     => [

						// "email: {$authHeaders['email']}",

						// "username: {$authHeaders['username']}",

						// "password: {$authHeaders['password']}",

						// "ip_address: {$authHeaders['ip_address']}",

						// "client_id: {$authHeaders['client_id']}",

						// "client_secret: {$authHeaders['client_secret']}",

						// "gstin: {$authHeaders['gstin']}"

						// ],

						// ]);

						

						// $response = curl_exec($ch);

						// curl_close($ch);

						// $authRes = json_decode($response, true);

						

						

						// $AuthToken = $authRes['data']['AuthToken'];

						

						// $ch = curl_init();

						// curl_setopt_array($ch, [

						// CURLOPT_URL            => "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']),

						// CURLOPT_RETURNTRANSFER => true,

						// CURLOPT_POST           => true,

						// CURLOPT_POSTFIELDS     => json_encode($ewayData),

						// CURLOPT_HTTPHEADER     => [

						// "Content-Type: application/json",

						// "email: {$authHeaders['email']}",

						// "ip_address: {$authHeaders['ip_address']}",

						// "client_id: {$authHeaders['client_id']}",

						// "client_secret: {$authHeaders['client_secret']}",

						// "username: {$authHeaders['username']}",

						// "gstin: {$authHeaders['gstin']}"

						// ]

						// ]);

						

						// $ewayRes = curl_exec($ch);

						// curl_close($ch);

						

						// $ewayResData = json_decode($ewayRes, true);

						// if (isset($ewayResData['data']['ewayBillNo'])) {

						// $this->db->where('SalesID', $SalesID);

						// $this->db->update(db_prefix().'salesmaster', [

						// 'ewaybill_no' => $ewayResData['data']['ewayBillNo'],

						// 'ewaybill_date' => date('Y-m-d H:i:s'),

						// 'ewaybill_valid_upto' => $ewayResData['data']['validUpto']

						// ]);

						// $SuccessMsg .= "E-Way Bill Is Generated Successfully OrderID ".$Sval['OrderID']." . ";

						

						// } else {

						

						// $ErrorMsg .= "E-Way Bill Is Not Generate OrderID ".$Sval['OrderID'].". ";

						

						// }

						}else{

						

						$ErrorMsg .= "E-Invoice Is Not Generate OrderID ".$Sval['OrderID']." - ".$errorMessage." . ";

						// set_alert('warning', 'Error Occurred');

						// echo json_encode($apiResponse);

					}

					

				}

			}

			

			$Result['Status'] = $return;

			$Result['ErrorMsg'] = $ErrorMsg;

			$Result['SuccessMsg'] = $SuccessMsg;

            echo json_encode($Result);

		}

		

		public function generateEwayBill()

		{

			$postData = $this->input->post();

			// Get data

			

			$fy = $this->session->userdata('finacial_year');

			$selected_company = $this->session->userdata('root_company');

			

			// Get Company Details

			$company_details = $this->challan_model->get_company_detail($selected_company);

			

			// Step 1: Authentication - Get AuthToken

			$authHeaders = [

			'email'         => $company_details->eway_email,

			'username'      => $company_details->eway_username,

			'password'      => $company_details->eway_password,

			'ip_address'    => $_SERVER['REMOTE_ADDR'],

			'client_id'     => $company_details->eway_client_id,

			'client_secret' => $company_details->eway_client_secret,

			'gstin'         => $company_details->eway_gstin,

			];

			

			$queryParams = http_build_query([

			'email'    => $authHeaders['email'],

			'username' => $authHeaders['username'],

			'password' => $authHeaders['password']

			]);

			

			$authURL = "https://api.mastergst.com/ewaybillapi/v1.03/authenticate?" . $queryParams;

			

			$ch = curl_init();

			curl_setopt_array($ch, [

			CURLOPT_URL            => $authURL,

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_HTTPHEADER     => [

            "email: {$authHeaders['email']}",

            "username: {$authHeaders['username']}",

            "password: {$authHeaders['password']}",

            "ip_address: {$authHeaders['ip_address']}",

            "client_id: {$authHeaders['client_id']}",

            "client_secret: {$authHeaders['client_secret']}",

            "gstin: {$authHeaders['gstin']}"

			],

			]);

			

			$response = curl_exec($ch);

			curl_close($ch);

			$authRes = json_decode($response, true);

			if ($authRes['status_cd'] == 0) {

				echo json_encode(['Status' => 'error', 'ErrorMsg' => 'Auth failedd', 'response' => $authRes]);

				return;

			}

			

			

			$AuthToken = $authRes['data']['AuthToken'];

			

			

			$return = false;

			$ErrorMsg = '';

			$SuccessMsg = '';

			

			

			$Salesdata = $this->challan_model->GetTaxableNonTaxableTransaction($postData);

			// Step 2: Prepare E-Way Bill Payload

			foreach($Salesdata as $Skey => $Sval){

				if($Sval['ewaybill_no'] == null){

					$items = $this->challan_model->fetchItemDetails($Sval['SalesID']);

					$SalesID = $Sval['SalesID'];

					

					

					if($Sval['city_name'] == ""){

						$location = $Sval['city'];

						}else{

						$location = $Sval['city_name'];

					}

					$Loc = $location;

					$Pin = $Sval['zip'];

					$Ph = $Sval['phonenumber'];

					

					

					

					// If party is unregistered, GSTIN should be 'URP', shipToGSTIN must be blank

					$toGstin = (empty($Sval['gstno']) || strtoupper($Sval['gstno']) == 'URP') ? 'URP' : $Sval['gstno'];

					$isUnregistered = ($toGstin == '' || $toGstin == 'URP');

					

					$ewayData = [

					"supplyType"        => "O",

					"subSupplyType"     => "1",

					"subSupplyDesc"     => " ",

					"docType"           => "INV",

					"docNo"             => $Sval['SalesID'],

					"docDate"           => date("d/m/Y"),

					"fromGstin"         => $company_details->eway_gstin,

					"fromTrdName"       => $company_details->company_name,

					"fromAddr1"         => $company_details->address,

					"fromAddr2"         => " ",

					"fromPlace"         => $company_details->city,

					"actFromStateCode"  => (int) sprintf('%02d', $company_details->eway_statecode),

					"fromPincode"       => (int) $company_details->pincode,

					"fromStateCode"     => (int) sprintf('%02d', $company_details->eway_statecode),

					"toGstin"           => $toGstin,

					"toTrdName"         => $Sval['company'],

					"toAddr1"           => $Sval['address'],

					"toAddr2"           => " ",

					"toPlace"           => $Loc,

					"toPincode"         => (int) $Pin,

					"actToStateCode"    => (int) sprintf('%02d', $Sval['StateId']),

					"toStateCode"       => (int) sprintf('%02d', $Sval['StateId']),

					"transactionType"   => 4,

					"dispatchFromGSTIN" => $company_details->eway_gstin,

					"dispatchFromTradeName" => $company_details->company_name,

					"shipToTradeName"   => $Sval['company'],

					"totalValue"        => floatval($Sval['SaleAmt']),

					"cgstValue"         => floatval($Sval['cgstamt']),

					"sgstValue"         => floatval($Sval['sgstamt']),

					"igstValue"         => floatval($Sval['igstamt']),

					"cessValue"         => 0,

					"cessNonAdvolValue" => 0,

					"totInvValue"       => floatval($Sval['RndAmt']),

					"transMode"         => "1",

					"transDistance"     => $Sval['kms'],

					"transporterName"   => "",

					"transporterId"     => "05AAACG0904A1ZL",

					"transDocNo"        => "12",

					"transDocDate"      => date("d/m/Y"),

					"vehicleNo"         => $Sval['VehicleID'],

					"vehicleType"       => "R",

					"itemList"          => []

					];

					if (!$isUnregistered) {

						$ewayData["shipToGSTIN"] = $toGstin;

					}

					

					$sl = 1;

					foreach ($items as $item) {

						$ewayData['itemList'][] = [

						"productName"   => $item['description'],

						"productDesc"   => $item['description'],

						"hsnCode"       => $item['hsn_code'],

						"quantity"      => floatval($item['BilledQty']),

						"qtyUnit"       => 'PCS',// $item['unit']

						"cgstRate"      => floatval($item['cgst']),

						"sgstRate"      => floatval($item['sgst']),

						"igstRate"      => floatval($item['igst']),

						"cessRate"      => 0,

						"taxableAmount"=> floatval($item['ChallanAmt'])

						];

						$sl++;

					}

					

					// $ewayData = [

					// "supplyType" => "O",

					// "subSupplyType" => "1",

					// "subSupplyDesc" => " ",

					// "docType" => "INV",

					// "docNo" => "bX/06/202ddj0",

					// "docDate" => "09/07/2025",

					// "fromGstin" => "05AAACH6188F1ZM",

					// "fromTrdName" => "welton",

					// "fromAddr1" => "2ND CROSS NO 59  19  A",

					// "fromAddr2" => "GROUND FLOOR OSBORNE ROAD",

					// "fromPlace" => "FRAZER TOWN",

					// "actFromStateCode" => 5,

					// "fromPincode" => 263652,

					// "fromStateCode" => 5,

					// "toGstin" => "05AAACH6886N1Z0",

					// "toTrdName" => "sthuthya",

					// "toAddr1" => "Shree Nilaya",

					// "toAddr2" => "Dasarahosahalli",

					// "toPlace" => "Beml Nagar",

					// "toPincode" => 263680,

					// "actToStateCode" => 5,

					// "toStateCode" => 5,

					// "transactionType" => 4,

					// "dispatchFromGSTIN" => "05AAACH6188F1ZM",

					// "dispatchFromTradeName" => "ABC Traders",

					// "shipToGSTIN" => "05AAACH6886N1Z0",

					// "shipToTradeName" => "XYZ Traders",

					// "totalValue" => 56099,

					// "cgstValue" => 150.34,

					// "sgstValue" => 150.34,

					// "igstValue" => 0,

					// "cessValue" => 400.56,

					// "cessNonAdvolValue" => 400,

					// "totInvValue" => 57200.24,

					// "transMode" => "1",

					// "transDistance" => "67",

					// "transporterName" => "",

					// "transporterId" => "05AAACG0904A1ZL",

					// "transDocNo" => "12",

					// "transDocDate" => date("d/m/Y"),

					// "vehicleNo" => "APR3214",

					// "vehicleType" => "R",

					// "itemList" => [

					// [

					// "productName" => "Wheat",

					// "productDesc" => "Wheat",

					// "hsnCode" => 1001,

					// "quantity" => 4,

					// "qtyUnit" => "BOX",

					// "taxableAmount" => 56099,

					// "sgstRate" => 1.5,

					// "cgstRate" => 1.5,

					// "igstRate" => 0,

					// "cessRate" => 0

					// ]

					// ]

					// ];

					

					// Step 3: Send E-Way Bill request

					$ch = curl_init();

					curl_setopt_array($ch, [

					CURLOPT_URL            => "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/genewaybill?email=" . urlencode($authHeaders['email']),

					CURLOPT_RETURNTRANSFER => true,

					CURLOPT_POST           => true,

					CURLOPT_POSTFIELDS     => json_encode($ewayData),

					CURLOPT_HTTPHEADER     => [

					"Content-Type: application/json",

					"email: {$authHeaders['email']}",

					"ip_address: {$authHeaders['ip_address']}",

					"client_id: {$authHeaders['client_id']}",

					"client_secret: {$authHeaders['client_secret']}",

					"username: {$authHeaders['username']}",

					"gstin: {$authHeaders['gstin']}"

					]

					]);

					

					$ewayRes = curl_exec($ch);

					curl_close($ch);

					

					$ewayResData = json_decode($ewayRes, true);

					// echo "<pre>";print_r($ewayResData);

					// die;

					if (isset($ewayResData['data']['ewayBillNo'])) {

						// Save to DB

						$this->db->where('SalesID', $SalesID);

						$this->db->update(db_prefix().'salesmaster', [

						'ewaybill_cancelled' => null,

						'EwayCancelRemark' => null,

						'ewaybill_no' => $ewayResData['data']['ewayBillNo'],

						'ewaybill_date' => date('Y-m-d H:i:s'),

						'ewaybill_valid_upto' => $ewayResData['data']['validUpto']

						]);

						$return = true;

						$SuccessMsg .= "E-Way Bill Is Generated Successfully OrderID ".$Sval['OrderID']." . ";

						

						} else {

						

						$ErrorMsg .= "E-Way Bill Is Not Generate OrderID ".$Sval['OrderID'].". ";

						

					}

				}

			}

			$Result['Status'] = $return;

			$Result['ErrorMsg'] = $ErrorMsg;

			$Result['SuccessMsg'] = $SuccessMsg;

			echo json_encode($Result);

		}

		

		

		public function GenerateConsolidatedEwayBill()

		{

			$postData = $this->input->post(); 

			$ChallanID = $this->input->post('ChallanID'); 

			

			$selected_company = $this->session->userdata('root_company');

			$company_details = $this->challan_model->get_company_detail($selected_company);

			

			// Step 1: Authenticate

			$authHeaders = [

			'email'         => $company_details->eway_email,

			'username'      => $company_details->eway_username,

			'password'      => $company_details->eway_password,

			'ip_address'    => $_SERVER['REMOTE_ADDR'],

			'client_id'     => $company_details->eway_client_id,

			'client_secret' => $company_details->eway_client_secret,

			'gstin'         => $company_details->eway_gstin,

			];

			

			$queryParams = http_build_query([

			'email'    => $authHeaders['email'],

			'username' => $authHeaders['username'],

			'password' => $authHeaders['password']

			]);

			

			$authURL = "https://api.mastergst.com/ewaybillapi/v1.03/authenticate?" . $queryParams;

			

			$ch = curl_init();

			curl_setopt_array($ch, [

			CURLOPT_URL            => $authURL,

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_HTTPHEADER     => [

            "email: {$authHeaders['email']}",

            "username: {$authHeaders['username']}",

            "password: {$authHeaders['password']}",

            "ip_address: {$authHeaders['ip_address']}",

            "client_id: {$authHeaders['client_id']}",

            "client_secret: {$authHeaders['client_secret']}",

            "gstin: {$authHeaders['gstin']}"

			]

			]);

			$response = curl_exec($ch);

			curl_close($ch);

			

			$authRes = json_decode($response, true);

			if ($authRes['status_cd'] == 0) {

				echo json_encode(['Status' => false, 'ErrorMsg' => 'Authentication Failed', 'response' => $authRes]);

				return;

			}

			

			$AuthToken = $authRes['data']['AuthToken'];

			

			$Salesdata = $this->challan_model->GetTaxableNonTaxableTransaction($postData);

			

			// Step 2: Build Consolidated Request

			$ewayData = [

			"fromPlace"     => $company_details->city,

			"fromState"     => (int) sprintf('%02d', $company_details->eway_statecode),

			"vehicleNo"     => $Salesdata[0]['VehicleID'],

			"transMode"     => "1", // 1=Road

			"transDocNo"     => "12",

			"transDocDate"     => date("d/m/Y"),

			"tripSheetEwbBills"     => [],

			];

			$sl = 1;

			foreach ($Salesdata as $Sales) {

				if(!empty($Sales['ewaybill_no'])){

					$ewayData['tripSheetEwbBills'][] = [

					"ewbNo"   => (int) $Sales['ewaybill_no'],

					];

					$sl++;

				}

			}

			

			// echo "<pre>";print_r($ewayData);die;

			

			$ch = curl_init();

			curl_setopt_array($ch, [

			CURLOPT_URL            => "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/gencewb?email=" . urlencode($authHeaders['email']),

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_POST           => true,

			CURLOPT_POSTFIELDS     => json_encode($ewayData),

			CURLOPT_HTTPHEADER     => [

			"Content-Type: application/json",

			"email: {$authHeaders['email']}",

			"ip_address: {$authHeaders['ip_address']}",

			"client_id: {$authHeaders['client_id']}",

			"client_secret: {$authHeaders['client_secret']}",

			"username: {$authHeaders['username']}",

			"gstin: {$authHeaders['gstin']}"

			]

			]);

			$consolidatedRes = curl_exec($ch);

			curl_close($ch);

			

			$resData = json_decode($consolidatedRes, true);

			// echo "<pre>";print_r($resData);die;

			if ($resData['status_cd'] == 1 && isset($resData['data']['cEwbNo'])) {

				$this->db->where('ChallanID', $ChallanID);

				$this->db->update(db_prefix().'challanmaster', [

				'ConsolidatedEWayBillNo' => $resData['data']['cEwbNo'],

				'ConsolidatedEWayBillDate' => $resData['data']['cEwbDate'],

				'ConsolidateValidUpto' => $resData['data']['validUpto'],

				]);

				

				echo json_encode([

				'Status' => true,

				'ConsolidatedNo' => $resData['data']['consolidatedEWayBillNo'],

				'validUpto' => $resData['data']['validUpto'],

				'SuccessMsg' => 'Consolidated E-Way Bill generated successfully.'

				]);

				} else {

				echo json_encode([

				'Status' => false,

				'ErrorMsg' => 'Failed to generate Consolidated E-Way Bill.',

				'Response' => $resData

				]);

			}

		}

		

		public function CancelEInvoice()

		{

			$postData = $this->input->post();

			$selected_company = $this->session->userdata('root_company');

			

			$irn = $postData['irn'];

			$remark = $postData['remark'];

			

			if (empty($irn) || empty($remark)) {

				echo json_encode(['Status' => false, 'ErrorMsg' => 'IRN or Remark is missing']);

				return;

			}

			

			$company_details = $this->challan_model->get_company_detail($selected_company);

			

			

			$Salesdata = $this->challan_model->GetSalesByIRN($irn);

			// Step 1: Authenticate

			$headersAuth = [

			'email' => $company_details->einvoice_email,

			'username' => $company_details->einvoice_username,

			'password' => $company_details->einvoice_password,

			'ip_address' => $_SERVER['REMOTE_ADDR'],

			'client_id' => $company_details->einvoice_client_id,

			'client_secret' => $company_details->einvoice_client_secret,

			'gstin' => $company_details->einvoice_gstin,

			];

			

			$authUrl = 'https://api.mastergst.com/einvoice/authenticate?' . http_build_query(['email' => $headersAuth['email']]);

			

			$ch = curl_init();

			curl_setopt_array($ch, [

			CURLOPT_URL => $authUrl,

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_HTTPHEADER => [

            'username: ' . $headersAuth['username'],

            'password: ' . $headersAuth['password'],

            'ip_address: ' . $headersAuth['ip_address'],

            'client_id: ' . $headersAuth['client_id'],

            'client_secret: ' . $headersAuth['client_secret'],

            'gstin: ' . $headersAuth['gstin'],

			]

			]);

			$response = curl_exec($ch);

			curl_close($ch);

			

			$authRes = json_decode($response, true);

			if (empty($authRes['data']['AuthToken'])) {

				echo json_encode(['Status' => false, 'ErrorMsg' => 'Authentication failed']);

				return;

			}

			

			$authToken = $authRes['data']['AuthToken'];

			

			// Step 2: Cancel IRN

			$cancelData = [

			'Irn' => $irn,

			'CnlRsn' => '1', // 1 = Duplicate, 2 = Data Entry Mistake, 3 = Order Cancelled, 4 = Others

			'CnlRem' => $remark

			];

			

			$cancelUrl = "https://api.mastergst.com/einvoice/type/CANCEL/version/V1_03?email=" . urlencode($headersAuth['email']);

			

			$ch = curl_init();

			curl_setopt_array($ch, [

			CURLOPT_URL => $cancelUrl,

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_CUSTOMREQUEST => 'POST',

			CURLOPT_POSTFIELDS => json_encode($cancelData),

			CURLOPT_HTTPHEADER => [

            "Content-Type: application/json",

            "email: " . $headersAuth['email'],

            "client_id: " . $headersAuth['client_id'],

            "client_secret: " . $headersAuth['client_secret'],

            "username: " . $headersAuth['username'],

            "ip_address: " . $headersAuth['ip_address'],

            "auth-token: " . $authToken,

            "gstin: " . $headersAuth['gstin']

			],

			]);

			$cancelRes = curl_exec($ch);

			curl_close($ch);

			

			$resData = json_decode($cancelRes, true);

			// echo "<pre>";print_r($resData);die;

			if (!empty($resData['data']) && $resData['data']['CancelDate']) {

				// Update DB

				$this->db->where('irn', $irn);

				$this->db->update(db_prefix() . 'salesmaster', [

				'irn_cancelled' => 'Y',

				'cancel_remark' => $remark,

				'irn' => null,

				'Qrcode' => null,

				'ackno' => null,

				'ackdate' => null,

				'SignedInvoice' => null,

				'cancel_date' => date('Y-m-d H:i:s')

				]);

				

				$insert_data =[

				'SalesID' => $Salesdata->SalesID,

				'irn' => $Salesdata->irn,

				'Qrcode' => $Salesdata->Qrcode,

				'ackno' => $Salesdata->ackno,

				'ackdate' => $Salesdata->ackdate,

				'SignedInvoice' => $Salesdata->SignedInvoice,

				'EInvoiceCancelRemark' => $remark,

				'TransDate' => date('Y-m-d H:i:s'),

				];

				$this->db->insert(db_prefix() . 'Einvoice_history', $insert_data);

				echo json_encode([

				'Status' => true,

				'SuccessMsg' => "IRN Cancelled Successfully"

				]);

				} else {

				$error = isset($resData['error']) ? $resData['error'] : 'Unknown Error';

				echo json_encode([

				'Status' => false,

				'ErrorMsg' => "IRN Cancellation Failed - " . $error

				]);

			}

		}

		

		public function CancelEWayBill()

		{

			$ewayno = $this->input->post('ewayno');

			$remark = $this->input->post('remark');

			

			if (empty($ewayno) || strlen($ewayno) != 12 || !is_numeric($ewayno)) {

				echo json_encode(['Status' => false, 'ErrorMsg' => 'Invalid E-Way Bill Number']);

				return;

			}

			

			$selected_company = $this->session->userdata('root_company');

			$company_details = $this->challan_model->get_company_detail($selected_company);

			

			$Salesdata = $this->challan_model->GetSalesByEwaybill($ewayno);

			// 1. Auth headers for MasterGST

			$authHeaders = [

			'email'         => $company_details->eway_email,

			'username'      => $company_details->eway_username,

			'password'      => $company_details->eway_password,

			'ip_address'    => $_SERVER['REMOTE_ADDR'],

			'client_id'     => $company_details->eway_client_id,

			'client_secret' => $company_details->eway_client_secret,

			'gstin'         => $company_details->eway_gstin,

			];

			

			

			// 3. Prepare cancel payload

			$cancelPayload = [

			'ewbNo'         => (int) $ewayno,

			'cancelRsnCode' => 3, // Order cancelled

			'cancelRmrk'    => $remark

			];

			

			// 4. Call cancel EWB API

			$cancelUrl = "https://api.mastergst.com/ewaybillapi/v1.03/ewayapi/canewb?email=" . urlencode($authHeaders['email']);

			$curl = curl_init();

			curl_setopt_array($curl, [

			CURLOPT_URL => $cancelUrl,

			CURLOPT_RETURNTRANSFER => true,

			CURLOPT_POST => true,

			CURLOPT_POSTFIELDS => json_encode($cancelPayload),

			CURLOPT_HTTPHEADER => [

            "Content-Type: application/json",

            "email: {$authHeaders['email']}",

            "ip_address: {$authHeaders['ip_address']}",

            "client_id: {$authHeaders['client_id']}",

            "client_secret: {$authHeaders['client_secret']}",

            "username: {$authHeaders['username']}",

            "gstin: {$authHeaders['gstin']}",

			]

			]);

			

			$response = curl_exec($curl);

			curl_close($curl);

			$responseData = json_decode($response, true);

			// echo "<pre>";print_r($responseData);die;

			// Handle response

			if (isset($responseData['data'])) {

				$this->db->where('ewaybill_no', $ewayno);

				$this->db->update(db_prefix() . 'salesmaster', [

				'ewaybill_cancelled' => 'Y',

				'EwayCancelRemark' => $remark,

				'ewaybill_no' => null,

				'ewaybill_date' => null,

				'ewaybill_valid_upto' => null,

				]);

				

				

				$insert_data =[

				'SalesID' => $Salesdata->SalesID,

				'ewaybill_no' => $Salesdata->ewaybill_no,

				'ewaybill_date' => $Salesdata->ewaybill_date,

				'ewaybill_valid_upto' => $Salesdata->ewaybill_valid_upto,

				'EwayCancelRemark' => $remark,

				'TransDate' => date('Y-m-d H:i:s'),

				];

				$this->db->insert(db_prefix() . 'Ewaybill_history', $insert_data);

				echo json_encode(['Status' => true, 'SuccessMsg' => 'E-Way Bill Cancelled Successfully']);

				} else {

				$errMsg = $responseData['message'] ?? 'Unknown error';

				echo json_encode(['Status' => false, 'ErrorMsg' => $errMsg]);

			}

		}

		

		

		

	}

